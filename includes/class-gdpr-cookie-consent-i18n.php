<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://club.wpeka.com
 * @since      1.0
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/includes
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent_I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0
	 */
	public function load_plugin_textdomain() {
		__( 'HTTP Cookie', 'gdpr-cookie-consent' );
		__( 'HTML Local Storage', 'gdpr-cookie-consent' );
		__( 'Flash Local Shared Object', 'gdpr-cookie-consent' );
		__( 'Pixel Tracker', 'gdpr-cookie-consent' );
		__( 'IndexedDB', 'gdpr-cookie-consent' );
		__( 'Read More', 'gdpr-cookie-consent' );
		__( 'Decline', 'gdpr-cookie-consent' );
		__( 'Accept', 'gdpr-cookie-consent' );
		__( 'Confirm', 'gdpr-cookie-consent' );
		__( 'Cancel', 'gdpr-cookie-consent' );
		__( 'Necessary', 'gdpr-cookie-consent' );
		__( 'Marketing', 'gdpr-cookie-consent' );
		__( 'Analytics', 'gdpr-cookie-consent' );
		__( 'Preferences', 'gdpr-cookie-consent' );
		__( 'Unclassified', 'gdpr-cookie-consent' );
		__( 'Cookie Settings', 'gdpr-cookie-consent' );
		__( 'Necessary cookies help make a website usable by enabling basic functions like page navigation and access to secure areas of the website. The website cannot function properly without these cookies.', 'gdpr-cookie-consent' );
		__( 'Marketing cookies are used to track visitors across websites. The intention is to display ads that are relevant and engaging for the individual user and thereby more valuable for publishers and third party advertisers.', 'gdpr-cookie-consent' );
		__( 'Analytics cookies help website owners to understand how visitors interact with websites by collecting and reporting information anonymously.', 'gdpr-cookie-consent' );
		__( 'Preference cookies enable a website to remember information that changes the way the website behaves or looks, like your preferred language or the region that you are in.', 'gdpr-cookie-consent' );
		__( 'Unclassified cookies are cookies that we are in the process of classifying, together with the providers of individual cookies.', 'gdpr-cookie-consent' );
		__( 'Cookies are small text files that can be used by websites to make a user\'s experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.', 'gdpr-cookie-consent' );
		__( 'This website uses cookies', 'gdpr-cookie-consent' );
		__( 'This website uses cookies to improve your experience. We\'ll assume you\'re ok with this, but you can opt-out if you wish.', 'gdpr-cookie-consent' );
		__( 'In case of sale of your personal information, you may opt out by using the link', 'gdpr-cookie-consent' );
		__( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' );
		__( 'Do you really wish to opt-out?', 'gdpr-cookie-consent' );
		load_plugin_textdomain(
			'gdpr-cookie-consent',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}



}
