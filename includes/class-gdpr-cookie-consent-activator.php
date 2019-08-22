<?php
/**
 * Fired during plugin activation
 *
 * @link       https://club.wpeka.com
 * @since      1.0
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/includes
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0
	 */
	public static function activate() {
		// previous version settings.
		$gdpr_option = get_option( 'GDPRCookieConsent-1.0' );
		$wpl_option  = get_option( 'WPLCookieConsent-1.0' );
		if ( isset( $gdpr_option['is_on'] ) ) {
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $gdpr_option );
			delete_option( 'GDPRCookieConsent-1.0' );
		} elseif ( isset( $wpl_option['is_on'] ) ) {
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $wpl_option );
			delete_option( 'WPLCookieConsent-1.0' );
		}
		$default_options = get_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
		if ( isset( $default_options['notify_div_id'] ) ) {
			$default_options['notify_div_id'] = '#gdpr-cookie-consent-bar';
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $default_options );
		}
	}

}
