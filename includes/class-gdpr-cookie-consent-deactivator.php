<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://club.wpeka.com
 * @since      1.0
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/includes
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0
	 */
	public static function deactivate() {
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		if ( isset( $the_options['delete_on_deactivation'] ) && true === $the_options['delete_on_deactivation'] ) {
			global $wpdb;
			$tables_arr = array(
				'gdpr_cookie_post_cookies',
				'gdpr_cookie_scan_categories',
			);
			foreach ( $tables_arr as $table ) {
				$tablename = $wpdb->prefix . $table;
				$wpdb->query( 'DROP TABLE IF EXISTS ' . $tablename ); // phpcs:ignore
			}
			delete_option( 'gdpr_admin_modules' );
			delete_option( 'gdpr_public_modules' );
			delete_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
			delete_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
		}
	}
}
