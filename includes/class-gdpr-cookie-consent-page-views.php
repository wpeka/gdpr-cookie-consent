<?php
/**
 * Banner page view counter.
 *
 * Page views used to be kept in the wpl_page_views, wpl_total_page_views and
 * wpl_monthly_page_views options, rewritten on every banner impression. That meant
 * three autoloaded option writes per visitor page view, lost increments under
 * concurrency and an alloptions cache flush each time. Views are now stored in a
 * small table with one row per day, incremented atomically.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores and reads banner page views.
 */
class Gdpr_Cookie_Consent_Page_Views {

	/**
	 * Table name without the site prefix.
	 */
	const TABLE = 'wpl_page_views';

	/**
	 * Bump when the table schema or the migration changes.
	 */
	const DB_VERSION = '1.0';

	/**
	 * Option holding the installed DB_VERSION.
	 */
	const DB_VERSION_OPTION = 'wpl_page_views_db_version';

	/**
	 * Views carried over from the legacy total that have no daily row.
	 * Written once during migration, never on page views.
	 */
	const TOTAL_OFFSET_OPTION = 'wpl_page_views_total_offset';

	/**
	 * Object cache group and lifetime for the monthly count read on every frontend render.
	 */
	const CACHE_GROUP = 'gdpr_page_views';
	const CACHE_TTL   = 60;

	/**
	 * Daily date format used by the legacy option keys and by consumers of get_daily_views().
	 */
	const LEGACY_DATE_FORMAT = 'M d, Y';

	/**
	 * Monthly banner views allowed on free/lite and not connected sites.
	 */
	const FREE_MONTHLY_LIMIT = 20000;

	/**
	 * Full table name for the current site.
	 *
	 * @return string
	 */
	public static function table_name() {
		global $wpdb;
		return $wpdb->prefix . self::TABLE;
	}

	/**
	 * Create the table and migrate the legacy options once per site.
	 *
	 * Called lazily by every public method, so it works per site on multisite and
	 * right after an update without depending on a particular hook having fired.
	 *
	 * @return bool Whether the table is ready.
	 */
	public static function maybe_install() {
		if ( get_option( self::DB_VERSION_OPTION ) === self::DB_VERSION ) {
			return true;
		}

		// add_option() fails if the row exists, so only one request runs the migration.
		if ( ! add_option( 'wpl_page_views_migrating', time(), '', false ) ) {
			// Allow a stale lock (a request that died mid-migration) to be taken over.
			if ( (int) get_option( 'wpl_page_views_migrating' ) > time() - 10 * MINUTE_IN_SECONDS ) {
				return false;
			}
			update_option( 'wpl_page_views_migrating', time(), false );
		}

		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$table = self::table_name();
		dbDelta(
			"CREATE TABLE {$table} (
			view_date date NOT NULL,
			views bigint(20) unsigned NOT NULL DEFAULT 0,
			PRIMARY KEY  (view_date)
			) {$wpdb->get_charset_collate()};"
		);

		if ( ! self::migrate_legacy_options() ) {
			delete_option( 'wpl_page_views_migrating' );
			return false;
		}

		update_option( self::DB_VERSION_OPTION, self::DB_VERSION );
		delete_option( 'wpl_page_views_migrating' );
		return true;
	}

	/**
	 * Move the legacy page view options into the table and delete them.
	 *
	 * @return bool False if the rows could not be written; the legacy options are then kept.
	 */
	private static function migrate_legacy_options() {
		global $wpdb;
		$table  = self::table_name();
		$daily  = get_option( 'wpl_page_views' );
		$total  = (int) get_option( 'wpl_total_page_views', 0 );
		$values = array();
		$sum    = 0;

		if ( is_array( $daily ) ) {
			foreach ( $daily as $date => $views ) {
				$date_obj = DateTime::createFromFormat( '!' . self::LEGACY_DATE_FORMAT, $date, new DateTimeZone( 'UTC' ) );
				if ( ! $date_obj || (int) $views <= 0 ) {
					continue;
				}
				$values[] = $wpdb->prepare( '(%s, %d)', $date_obj->format( 'Y-m-d' ), (int) $views );
				$sum     += (int) $views;
			}
		}

		if ( $values ) {
			// Additive, so rows already in the table (a migration taken over from a stale lock) are kept.
			$inserted = $wpdb->query( "INSERT INTO {$table} (view_date, views) VALUES " . implode( ',', $values ) . ' ON DUPLICATE KEY UPDATE views = views + VALUES(views)' ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- values prepared above.
			if ( false === $inserted ) {
				return false;
			}
		}

		// The legacy total also includes views whose daily entries were never stored or were removed.
		if ( $total > $sum ) {
			update_option( self::TOTAL_OFFSET_OPTION, $total - $sum, false );
		}

		delete_option( 'wpl_page_views' );
		delete_option( 'wpl_total_page_views' );
		delete_option( 'wpl_monthly_page_views' );
		return true;
	}

	/**
	 * Count one banner impression for today (UTC).
	 *
	 * @return void
	 */
	public static function increment() {
		if ( ! self::maybe_install() ) {
			return;
		}
		global $wpdb;
		$table = self::table_name();
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$table} (view_date, views) VALUES (%s, 1) ON DUPLICATE KEY UPDATE views = views + 1", gmdate( 'Y-m-d' ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Views in the current calendar month (UTC).
	 *
	 * @param bool $fresh Skip the object cache.
	 * @return int
	 */
	public static function get_monthly_views( $fresh = false ) {
		$cache_key = 'monthly_' . gmdate( 'Y-m' );
		if ( ! $fresh ) {
			$cached = wp_cache_get( $cache_key, self::CACHE_GROUP );
			if ( false !== $cached ) {
				return (int) $cached;
			}
		}
		if ( ! self::maybe_install() ) {
			return 0;
		}
		global $wpdb;
		$table   = self::table_name();
		$monthly = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(views), 0) FROM {$table} WHERE view_date >= %s", gmdate( 'Y-m-01' ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		wp_cache_set( $cache_key, $monthly, self::CACHE_GROUP, self::CACHE_TTL );
		return $monthly;
	}

	/**
	 * All-time views.
	 *
	 * @return int
	 */
	public static function get_total_views() {
		if ( ! self::maybe_install() ) {
			return 0;
		}
		global $wpdb;
		$table = self::table_name();
		return (int) get_option( self::TOTAL_OFFSET_OPTION, 0 ) + (int) $wpdb->get_var( "SELECT COALESCE(SUM(views), 0) FROM {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Daily views for the last 12 months, in the legacy wpl_page_views shape
	 * ( 'M d, Y' => views, oldest first ) expected by the dashboards.
	 *
	 * @return array
	 */
	public static function get_daily_views() {
		if ( ! self::maybe_install() ) {
			return array();
		}
		global $wpdb;
		$table  = self::table_name();
		$cutoff = gmdate( 'Y-m-d', strtotime( '-12 months', strtotime( gmdate( 'Y-m-d' ) ) ) );
		$rows   = $wpdb->get_results( $wpdb->prepare( "SELECT view_date, views FROM {$table} WHERE view_date >= %s ORDER BY view_date", $cutoff ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$daily  = array();
		foreach ( (array) $rows as $row ) {
			$daily[ gmdate( self::LEGACY_DATE_FORMAT, strtotime( $row['view_date'] . ' UTC' ) ) ] = (int) $row['views'];
		}
		return $daily;
	}

	/**
	 * Monthly banner view limit for a plan.
	 *
	 * Sites that are not connected (plan defaults to 'free') and free/lite accounts are
	 * limited; every paid plan is unlimited.
	 *
	 * @param string $plan Plan from GDPR_Cookie_Consent_Settings::get_plan().
	 * @return int The limit, or 0 for unlimited.
	 */
	public static function get_monthly_limit( $plan ) {
		$plan = strtolower( trim( (string) $plan ) );
		return in_array( $plan, array( '', 'free', 'lite' ), true ) ? self::FREE_MONTHLY_LIMIT : 0;
	}

	/**
	 * Percentage of the monthly limit used.
	 *
	 * @param string   $plan    Plan from GDPR_Cookie_Consent_Settings::get_plan().
	 * @param int|null $monthly Monthly views, if already known.
	 * @return float|int 0 for unlimited plans.
	 */
	public static function get_monthly_percent( $plan, $monthly = null ) {
		$limit = self::get_monthly_limit( $plan );
		if ( ! $limit ) {
			return 0;
		}
		if ( null === $monthly ) {
			$monthly = self::get_monthly_views();
		}
		return ( $monthly / $limit ) * 100;
	}

	/**
	 * Drop the table and its options (plugin data removal).
	 *
	 * @return void
	 */
	public static function uninstall() {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', self::table_name() ) );
		delete_option( self::DB_VERSION_OPTION );
		delete_option( self::TOTAL_OFFSET_OPTION );
		delete_option( 'wpl_page_views_migrating' );
		delete_option( 'wpl_page_views' );
		delete_option( 'wpl_total_page_views' );
		delete_option( 'wpl_monthly_page_views' );
	}
}
