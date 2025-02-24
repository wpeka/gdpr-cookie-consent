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
 * Version:           3.7.4
 * Author:            WP Legal Pages
 * Author URI:        https://wplegalpages.com
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
define( 'GDPR_COOKIE_CONSENT_VERSION', '3.7.4' );
define( 'GDPR_COOKIE_CONSENT_PLUGIN_DEVELOPMENT_MODE', false );
define( 'GDPR_COOKIE_CONSENT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'GDPR_COOKIE_CONSENT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'GDPR_COOKIE_CONSENT_DB_KEY_PREFIX', 'GDPRCookieConsent-' );
define( 'GDPR_COOKIE_CONSENT_LATEST_VERSION_NUMBER', '9.0' );
define( 'GDPR_COOKIE_CONSENT_SETTINGS_FIELD', GDPR_COOKIE_CONSENT_DB_KEY_PREFIX . GDPR_COOKIE_CONSENT_LATEST_VERSION_NUMBER );
define( 'GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD', GDPR_COOKIE_CONSENT_DB_KEY_PREFIX . 'LogoImage' . GDPR_COOKIE_CONSENT_LATEST_VERSION_NUMBER );
define( 'GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1', GDPR_COOKIE_CONSENT_DB_KEY_PREFIX . 'LogoImage1' . GDPR_COOKIE_CONSENT_LATEST_VERSION_NUMBER );
define( 'GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD2', GDPR_COOKIE_CONSENT_DB_KEY_PREFIX . 'LogoImage2' . GDPR_COOKIE_CONSENT_LATEST_VERSION_NUMBER );
define( 'GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELDML1', GDPR_COOKIE_CONSENT_DB_KEY_PREFIX . 'LogoImageML1' . GDPR_COOKIE_CONSENT_LATEST_VERSION_NUMBER );
define( 'GDPR_COOKIE_CONSENT_SETTINGS_VENDOR', 'vendordata' );
define( 'GDPR_COOKIE_CONSENT_SETTINGS_GACM_VENDOR', 'gacmvendordata' );
define( 'GDPR_COOKIE_CONSENT_SETTINGS_VENDOR_CONSENT', 'iabtcfConsent' );
define( 'GDPR_COOKIE_CONSENT_PLUGIN_FILENAME', __FILE__ );
define( 'GDPR_POLICY_DATA_POST_TYPE', 'gdprpolicies' );
define( 'GDPR_CSV_DELIMITER', ',' );
define( 'GDPR_URL', plugins_url( '/', __FILE__ ) );
if ( ! defined( 'GDPR_CC_SUFFIX' ) ) {
	define( 'GDPR_CC_SUFFIX', ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '' );
}
if ( ! defined( 'FS_CHMOD_FILE' ) ) {
	define( 'FS_CHMOD_FILE', ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );
}
/**
 * Check if the constant GDPR_APP_URL is not already defined.
*/
if ( ! defined( 'GDPR_APP_URL' ) ) {
	define( 'GDPR_APP_URL', 'https://app.wplegalpages.com' );
}
if ( ! defined( 'GDPR_API_URL' ) ) {
	define( 'GDPR_API_URL', 'https://app.wplegalpages.com/wp-json/gdpr/v2/' );
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
 * Load WC_AM_Client class if it exists.
 */
if ( ! class_exists( 'WC_AM_Client_2_7_WPGDPR' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'wc-am-client-gdpr.php';
}

/*
 * Instantiate WC_AM_Client class object if the WC_AM_Client class is loaded.
 */
if ( class_exists( 'WC_AM_Client_2_7_WPGDPR' ) ) {

	$wcam_lib_gdpr = new WC_AM_Client_2_7_WPGDPR( __FILE__, '', '3.2.0', 'plugin', GDPR_APP_URL, 'WP Cookie Consent', 'gdpr-cookie-consent' );
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
		exit( esc_html( wp_redirect( admin_url( 'admin.php?page=gdpr-cookie-consent#create_cookie_banner' ) ) ) );
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
	delete_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1 );
	delete_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD2 );
	delete_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELDML1 );
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

/* Added for displaying message on activation/deactivation of other third party cookie plugin */
// Hook into plugin activation and deactivation events
add_action('deactivate_plugin', 'gdpr_display_message_other_plugin_on_change', 10, 2);
add_action('activate_plugin', 'gdpr_display_message_other_plugin_on_change', 10, 2);

function gdpr_display_message_other_plugin_on_change($plugin, $network_deactivating) {
    // Get all installed plugins with their details
    $all_plugins = get_plugins();

    foreach ($all_plugins as $plugin_path => $plugin_data) {
        // Check if the plugin name or description contains cookie-related keywords
        if (
            stripos($plugin_data['Name'], 'cookie') !== false ||
            stripos($plugin_data['Description'], 'cookie') !== false ||
            stripos($plugin_data['Name'], 'consent') !== false ||
            stripos($plugin_data['Description'], 'consent') !== false ||
            stripos($plugin_data['Name'], 'GDPR') !== false ||
            stripos($plugin_data['Description'], 'GDPR') !== false
        ) {
            // If the activated/deactivated plugin matches any cookie consent plugin
            if ($plugin === $plugin_path) {
                // Store a transient to show a message
                set_transient('gdpr_display_message_other_plugin_on_change', true, 60);
                break;
            }
        }
    }
}

// Display the admin notice if a cookie consent plugin was activated or deactivated
add_action('admin_notices', 'gdpr_show_admin_notice_activation_deactivation_third_party_plugins');

function gdpr_show_admin_notice_activation_deactivation_third_party_plugins() {
    // Check if the transient is set
    if (get_transient('gdpr_display_message_other_plugin_on_change')) {
        // Output the admin notice with a link to rescan the website
        echo '<div class="notice notice-warning is-dismissible">';
		echo '<p>' . esc_html__('You have enabled or disabled a cookie consent plugin, which may require your cookie banner to be adjusted. Please scan your website again as soon as you have finished the changes.', 'gdpr-cookie-consent') . ' <a href="' . esc_url( admin_url( 'admin.php?page=gdpr-cookie-consent#cookie_settings#cookie_list#discovered_cookies' ) ) . '">' . esc_html__('Scan website again', 'gdpr-cookie-consent') . '</a></p>';
		echo '</div>';
        
        // Delete the transient after displaying the message
        delete_transient('gdpr_display_message_other_plugin_on_change');
    }
}

// Display the admin notice if a wp cookie consent pro plugin was activated or installed.
add_action('admin_notices', 'gdpr_display_user_mirgation_notice');

function gdpr_display_user_mirgation_notice() {
	$installed_plugins = get_plugins();
	$pro_installed     = isset( $installed_plugins['wpl-cookie-consent/wpl-cookie-consent.php'] ) ? true : false;
	if($pro_installed){
		echo '<div class="notice notice-error notice-alt">';
		echo '<p>' . esc_html__('Action Required: Switch to the New WP Legal Pages Compliance Platform! The new platform no longer requires Pro plugins.', 'gdpr-cookie-consent') . 
		' <a href="https://wplegalpages.com/docs/migration-from-wpeka/migration/seamless-migration-to-the-new-wp-legal-pages-compliance-platform/" target="_blank" rel="noopener noreferrer" previewlistener="true">' . 
		esc_html__('Follow this guide to migrate now.', 'gdpr-cookie-consent') . '</a></p>';
		echo '</div>';
	}
}
// Added for plugin tour
function gdpr_complete_tour() {
    update_option('gdpr_first_time_installed', false);
    wp_send_json_success();
}
add_action('wp_ajax_gdpr_complete_tour', 'gdpr_complete_tour');
