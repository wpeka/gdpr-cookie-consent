<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://club.wpeka.com
 * @since             1.0
 * @package           Gdpr_Cookie_Consent
 *
 * @wordpress-plugin
 * Plugin Name:       WP Cookie Consent
 * Plugin URI:        https://club.wpeka.com/
 * Description:       Cookie Consent will help you put up a subtle banner in the footer of your website to showcase compliance status regarding the EU Cookie law.
 * Version:           2.3.5
 * Author:            WPEkaClub
 * Author URI:        https://club.wpeka.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gdpr-cookie-consent
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once __DIR__ . '/vendor/autoload.php';

define( 'GDPR_COOKIE_CONSENT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Currently plugin version.
 */
define( 'GDPR_COOKIE_CONSENT_VERSION', '2.3.5' );
define( 'GDPR_COOKIE_CONSENT_PLUGIN_DEVELOPMENT_MODE', false );
define( 'GDPR_COOKIE_CONSENT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'GDPR_COOKIE_CONSENT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'GDPR_COOKIE_CONSENT_DB_KEY_PREFIX', 'GDPRCookieConsent-' );
define( 'GDPR_COOKIE_CONSENT_LATEST_VERSION_NUMBER', '9.0' );
define( 'GDPR_COOKIE_CONSENT_SETTINGS_FIELD', GDPR_COOKIE_CONSENT_DB_KEY_PREFIX . GDPR_COOKIE_CONSENT_LATEST_VERSION_NUMBER );
define( 'GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD', GDPR_COOKIE_CONSENT_DB_KEY_PREFIX . 'LogoImage' . GDPR_COOKIE_CONSENT_LATEST_VERSION_NUMBER );
define( 'GDPR_COOKIE_CONSENT_PLUGIN_FILENAME', __FILE__ );
define( 'GDPR_POLICY_DATA_POST_TYPE', 'gdprpolicies' );
define( 'GDPR_CSV_DELIMITER', ',' );
if ( ! defined( 'GDPR_CC_SUFFIX' ) ) {
	define( 'GDPR_CC_SUFFIX', ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min' );
}


/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var Data to sanitize.
 *
 * @return string|array
 */
function gdprcc_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'gdprcc_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gdpr-cookie-consent-activator.php
 */
function activate_gdpr_cookie_consent() {
	require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/class-gdpr-cookie-consent-activator.php';
	Gdpr_Cookie_Consent_Activator::activate();
	register_uninstall_hook( __FILE__, 'uninstall_gdpr_cookie_consent' );
	add_option( 'analytics_activation_redirect_gdpr-cookie-consent', true );
	// Get redirect URL.
	add_option( 'redirect_after_activation_option', true );
}

/**
 * Redirecting to the wizard page on plguin activation.
 *
 * Handles the redirection of the page after plugin activation.
 */
add_action( 'admin_init', 'activation_redirect' );

/**
 * It will redirect to the wizard page after plugin activation.
 *
 * @return void
 */
function activation_redirect() {
	if ( get_option( 'redirect_after_activation_option', false ) ) {
		delete_option( 'redirect_after_activation_option' );
		exit( esc_html( wp_redirect( admin_url( 'admin.php?page=gdpr-cookie-consent-wizard' ) ) ) );
	}
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gdpr-cookie-consent-deactivator.php
 */
function deactivate_gdpr_cookie_consent() {
	require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/class-gdpr-cookie-consent-deactivator.php';
	Gdpr_Cookie_Consent_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_gdpr_cookie_consent' );
register_deactivation_hook( __FILE__, 'deactivate_gdpr_cookie_consent' );

require plugin_dir_path( __FILE__ ) . 'includes/class-gdpr-cookies-read-csv.php';

/**
 * Delete all settings related to plugin.
 */
function uninstall_gdpr_cookie_consent() {
	delete_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
	delete_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
}
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/class-gdpr-cookie-consent.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0
 */
function run_gdpr_cookie_consent() {

	$plugin = new Gdpr_Cookie_Consent();
	$plugin->run();
}
run_gdpr_cookie_consent();
