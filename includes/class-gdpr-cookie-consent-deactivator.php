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
		delete_option( 'wpl_ab_options');
		delete_option( GDPR_COOKIE_CONSENT_SETTINGS_VENDOR);
		delete_option( 'gdpr_review_pending');
		
		$the_options['is_worldwide_on'] = 'true';
		$the_options['is_selectedCountry_on'] = 'false';
		$the_options['is_eu_on'] = 'false';
		$the_options['is_ccpa_on'] = 'false';
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );

		update_option( 'gdpr_no_of_page_scan', 0 );
	}
}
