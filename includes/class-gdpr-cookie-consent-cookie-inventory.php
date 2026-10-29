<?php
/**
 * Cookie inventory: name patterns, schema migration and the list the cleaner uses.
 *
 * @link       https://club.wpeka.com
 * @since      4.5.0
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cookie inventory.
 *
 * The scanner reports the exact names it saw (_ga_K3J8Q2, _pk_id.1.a2f3), but
 * many trackers put an ID in the name, so a visitor's cookie rarely matches the
 * scanned name. Each scanned name is mapped to its family's pattern from a
 * maintained library (_ga_*, _pk_id.*); names with no match stay exact. An
 * admin can also enter a pattern directly, with * as the wildcard.
 *
 * Each inventory row has a pattern, a domain, a path and a storage type
 * (cookie, local, session).
 *
 * @since 4.5.0
 */
class Gdpr_Cookie_Consent_Cookie_Inventory {

	/**
	 * Schema version of the inventory tables.
	 *
	 * @var string
	 */
	const DB_VERSION = '1';

	/**
	 * The plugin's own cookies, never cleaned.
	 *
	 * @var string[]
	 */
	const PROTECTED_COOKIES = array( 'wpl_consent', 'wpl_viewed_cookie', 'wpl_user_preference', 'wpl_optout_cookie', 'consent_version', 'usprivacy', 'wpl_tc_string', 'IABTCF_AddtlConsent' );

	/**
	 * Name prefixes never cleaned: WordPress login and settings cookies.
	 *
	 * @var string[]
	 */
	const PROTECTED_PREFIXES = array( 'wordpress_', 'wp-settings', 'wp_lang', 'comment_author_' );

	/**
	 * Known cookie families whose names carry an ID.
	 *
	 * @return array List of array( 'pattern' => glob, 'regex' => regex ).
	 */
	public static function families() {
		$families = array(
			// Google Analytics 4 and Universal Analytics.
			array( '_ga_*', '/^_ga_[A-Za-z0-9]+$/' ),
			array( '_gat_*', '/^_gat_.+$/' ),
			array( '_gac_*', '/^_gac_.+$/' ),
			array( '_dc_gtm_*', '/^_dc_gtm_.+$/' ),
			array( '__utm*', '/^__utm[a-z]$/' ),
			// Hotjar.
			array( '_hjSession_*', '/^_hjSession_\d+$/' ),
			array( '_hjSessionUser_*', '/^_hjSessionUser_\d+$/' ),
			array( '_hjIncludedInSessionSample_*', '/^_hjIncludedInSessionSample_\d+$/' ),
			array( '_hjIncludedInPageviewSample_*', '/^_hjIncludedInPageviewSample_\d+$/' ),
			array( '_hjFirstSeen_*', '/^_hjFirstSeen_\d+$/' ),
			// Matomo / Piwik.
			array( '_pk_id.*', '/^_pk_id\..+$/' ),
			array( '_pk_ses.*', '/^_pk_ses\..+$/' ),
			array( '_pk_ref.*', '/^_pk_ref\..+$/' ),
			array( '_pk_cvar.*', '/^_pk_cvar\..+$/' ),
			array( '_pk_hsr.*', '/^_pk_hsr\..+$/' ),
			array( '_pk_testcookie.*', '/^_pk_testcookie\..+$/' ),
			// Intercom.
			array( 'intercom-id-*', '/^intercom-id-.+$/' ),
			array( 'intercom-session-*', '/^intercom-session-.+$/' ),
			array( 'intercom-device-id-*', '/^intercom-device-id-.+$/' ),
			// Mixpanel, Amplitude, Adobe.
			array( 'mp_*_mixpanel', '/^mp_[A-Za-z0-9]+_mixpanel$/' ),
			array( 'amp_*', '/^amp_[A-Za-z0-9]{6,}$/' ),
			array( 'AMCV_*', '/^AMCV_.+$/' ),
			array( 'AMCVS_*', '/^AMCVS_.+$/' ),
			// Optimizely, Segment-style per-project cookies.
			array( 'optimizelyBuckets*', '/^optimizelyBuckets.*$/' ),
		);
		$out = array();
		foreach ( $families as $family ) {
			$out[] = array(
				'pattern' => $family[0],
				'regex'   => $family[1],
			);
		}
		/**
		 * Filters the library of cookie name families.
		 *
		 * @since 4.5.0
		 * @param array $out List of array( 'pattern' => '_ga_*', 'regex' => '/^_ga_.+$/' ).
		 */
		return (array) apply_filters( 'gdprcookieconsent_cookie_families', $out );
	}

	/**
	 * The pattern for a scanned cookie name: its family's, or the name itself.
	 *
	 * @param string $name Cookie name as scanned.
	 * @return string
	 */
	public static function pattern_for( $name ) {
		$name = trim( (string) $name );
		if ( '' === $name || false !== strpos( $name, '*' ) ) {
			return $name;
		}
		foreach ( self::families() as $family ) {
			if ( isset( $family['regex'], $family['pattern'] ) && 1 === @preg_match( $family['regex'], $name ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- filterable regex.
				return $family['pattern'];
			}
		}
		return $name;
	}

	/**
	 * A glob (* wildcard) as an anchored regex.
	 *
	 * @param string $glob Pattern.
	 * @return string
	 */
	public static function glob_to_regex( $glob ) {
		return '/^' . str_replace( '\*', '.*', preg_quote( (string) $glob, '/' ) ) . '$/';
	}

	/**
	 * Whether a cookie may never be cleaned.
	 *
	 * @param string $name Cookie name.
	 * @return bool
	 */
	public static function is_protected( $name ) {
		if ( in_array( $name, self::PROTECTED_COOKIES, true ) ) {
			return true;
		}
		foreach ( self::PROTECTED_PREFIXES as $prefix ) {
			if ( 0 === strpos( $name, $prefix ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Add the pattern, path and storage columns, widen the scanned name, and
	 * run existing rows through the pattern library. Runs once per schema
	 * version.
	 *
	 * Also attaches every scanned row to the latest completed scan: earlier
	 * versions skipped cookies an older scan had already stored, so the latest
	 * scan alone did not hold the whole inventory.
	 */
	public static function maybe_migrate() {
		if ( get_option( 'wpl_cookie_inventory_db_version' ) === self::DB_VERSION ) {
			return;
		}
		global $wpdb;
		$scan_cookies = $wpdb->prefix . 'wpl_cookie_scan_cookies';
		$post_cookies = $wpdb->prefix . 'gdpr_cookie_post_cookies';

		foreach ( array( $scan_cookies, $post_cookies ) as $table ) {
			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) ) !== $table ) { // db call ok; no-cache ok.
				continue;
			}
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.SchemaChange -- internal table names.
			if ( ! $wpdb->get_results( "SHOW COLUMNS FROM `$table` LIKE 'pattern'" ) ) {
				$wpdb->query( "ALTER TABLE `$table` ADD `pattern` VARCHAR(255) NOT NULL DEFAULT ''" );
			}
			if ( ! $wpdb->get_results( "SHOW COLUMNS FROM `$table` LIKE 'path'" ) ) {
				$wpdb->query( "ALTER TABLE `$table` ADD `path` VARCHAR(255) NOT NULL DEFAULT '/'" );
			}
			if ( ! $wpdb->get_results( "SHOW COLUMNS FROM `$table` LIKE 'storage'" ) ) {
				$wpdb->query( "ALTER TABLE `$table` ADD `storage` VARCHAR(20) NOT NULL DEFAULT 'cookie'" );
			}
			if ( $table === $scan_cookies ) {
				// Names were cut at 50 characters.
				$wpdb->query( "ALTER TABLE `$table` MODIFY `name` VARCHAR(255) NOT NULL" );
			}
			$rows = $wpdb->get_results( "SELECT * FROM `$table` WHERE `pattern` = ''", ARRAY_A );
			// phpcs:enable
			$id_column = ( $table === $scan_cookies ) ? 'id_wpl_cookie_scan_cookies' : 'id_gdpr_cookie_post_cookies';
			foreach ( (array) $rows as $row ) {
				$wpdb->update( $table, array( 'pattern' => self::pattern_for( $row['name'] ) ), array( $id_column => $row[ $id_column ] ) ); // db call ok; no-cache ok.
			}
		}

		$scan_table = $wpdb->prefix . 'wpl_cookie_scan';
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $scan_table ) ) ) === $scan_table ) { // db call ok; no-cache ok.
			$latest = (int) $wpdb->get_var( "SELECT id_wpl_cookie_scan FROM `$scan_table` WHERE status = '2' ORDER BY created_at DESC, id_wpl_cookie_scan DESC LIMIT 1" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery
			if ( $latest ) {
				$wpdb->query( $wpdb->prepare( "UPDATE IGNORE `$scan_cookies` SET id_wpl_cookie_scan = %d", $latest ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery
			}
		}

		update_option( 'wpl_cookie_inventory_db_version', self::DB_VERSION );
	}

	/**
	 * The inventory the cleaner works from: every scanned or manually added
	 * cookie that is not necessary, with its pattern, category, domain, path
	 * and storage. No row limit.
	 *
	 * @return array List of array( 'p' => pattern, 'c' => slug, 'd' => domain, 'path' => path, 's' => storage ).
	 */
	public static function inventory() {
		static $inventory = null;
		if ( null !== $inventory ) {
			return $inventory;
		}
		$inventory = array();
		if ( ! class_exists( 'Gdpr_Cookie_Consent_Cookie_Custom' ) ) {
			return $inventory;
		}

		$slugs = array();
		foreach ( Gdpr_Cookie_Consent_Cookie_Custom::get_categories( true ) as $category ) {
			$slugs[ (int) $category['id_gdpr_cookie_category'] ] = $category['gdpr_cookie_category_slug'];
		}

		$custom  = new Gdpr_Cookie_Consent_Cookie_Custom();
		$cookies = apply_filters( 'gdprcookieconsent_cookies', $custom->get_cookies( 0 ) );
		$seen    = array();
		foreach ( (array) $cookies as $cookie ) {
			$name = isset( $cookie['name'] ) ? trim( (string) $cookie['name'] ) : '';
			if ( '' === $name || self::is_protected( $name ) ) {
				continue;
			}
			$category_id = isset( $cookie['category_id'] ) ? (int) $cookie['category_id'] : 0;
			$slug        = isset( $slugs[ $category_id ] ) ? $slugs[ $category_id ] : 'unclassified';
			if ( 'necessary' === $slug ) {
				continue;
			}
			$pattern = ( isset( $cookie['pattern'] ) && '' !== $cookie['pattern'] ) ? $cookie['pattern'] : self::pattern_for( $name );
			$storage = isset( $cookie['storage'] ) && in_array( $cookie['storage'], array( 'cookie', 'local', 'session' ), true ) ? $cookie['storage'] : 'cookie';
			$key     = $pattern . '|' . $storage;
			if ( isset( $seen[ $key ] ) ) {
				continue;
			}
			$seen[ $key ] = true;
			$inventory[]  = array(
				'p'    => $pattern,
				'c'    => $slug,
				'd'    => isset( $cookie['domain'] ) ? strtolower( ltrim( trim( (string) $cookie['domain'] ), '.' ) ) : '',
				'path' => ( isset( $cookie['path'] ) && '' !== $cookie['path'] ) ? $cookie['path'] : '/',
				's'    => $storage,
			);
		}
		/**
		 * Filters the cookie inventory the cleaner uses.
		 *
		 * @since 4.5.0
		 * @param array $inventory Inventory rows.
		 */
		$inventory = (array) apply_filters( 'gdprcookieconsent_cookie_inventory', $inventory );
		return $inventory;
	}
}
