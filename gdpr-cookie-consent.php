<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://wplegalpages.com
 * @since             1.0
 * @package           Gdpr_Cookie_Consent
 *
 * @wordpress-plugin
 * Plugin Name:       Cookie Banner for GDPR / CCPA - WPLP Cookie Consent
 * Plugin URI:        https://wplegalpages.com/
 * Description:       Cookie Consent will help you put up a subtle banner in the footer of your website to showcase compliance status regarding the EU Cookie law.
 * Version:           4.4.3
 * Author:            WPLP Compliance Platform
 * Author URI:        https://wplegalpages.com
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
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
define( 'GDPR_COOKIE_CONSENT_VERSION', '4.4.3' );
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

define( 'GDPR_COOKIE_CONSENT_SETTINGS_REVOKE_ICON', GDPR_COOKIE_CONSENT_DB_KEY_PREFIX . 'RevokeIcon' . GDPR_COOKIE_CONSENT_LATEST_VERSION_NUMBER );
define( 'GDPR_COOKIE_CONSENT_SETTINGS_REVOKE_ICON1', GDPR_COOKIE_CONSENT_DB_KEY_PREFIX . 'RevokeIcon1' . GDPR_COOKIE_CONSENT_LATEST_VERSION_NUMBER );
define( 'GDPR_COOKIE_CONSENT_SETTINGS_REVOKE_ICON2', GDPR_COOKIE_CONSENT_DB_KEY_PREFIX . 'RevokeIcon2' . GDPR_COOKIE_CONSENT_LATEST_VERSION_NUMBER );

define( 'GDPR_COOKIE_CONSENT_SETTINGS_VENDOR', 'vendordata' );
define( 'GDPR_COOKIE_CONSENT_SETTINGS_GACM_VENDOR', 'gacmvendordata' );
define( 'GDPR_COOKIE_CONSENT_SETTINGS_VENDOR_CONSENT', 'iabtcfConsent' );
define( 'GDPR_COOKIE_CONSENT_PLUGIN_FILENAME', __FILE__ );
define( 'GDPR_POLICY_DATA_POST_TYPE', 'gdprpolicies' );
define( 'GDPR_CSV_DELIMITER', ',' );
define( 'GDPR_URL', plugins_url( '/', __FILE__ ) );
if ( ! defined( 'GDPR_CC_SUFFIX' ) ) {
	define( 'GDPR_CC_SUFFIX', ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min' );
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

 
if ( ! defined( 'APPWPLP_SECRET_KEY_FEATURE_VERSION' ) ) {
	define( 'APPWPLP_SECRET_KEY_FEATURE_VERSION', '4.4.3' );
}

/**
 * How long a successful bearer-token verdict stays cached, in seconds.
 */
if ( ! defined( 'APPWPLP_JWT_CACHE_TTL' ) ) {
	define( 'APPWPLP_JWT_CACHE_TTL', 15 * MINUTE_IN_SECONDS );
}

if ( ! defined( 'APPWPLP_SECRET_KEY_OPTION' ) ) {
	define( 'APPWPLP_SECRET_KEY_OPTION', 'appwplp_shared_secret_key' );
}
 
if ( ! defined( 'APPWPLP_SECRET_KEY_STATUS_OPTION' ) ) {
	define( 'APPWPLP_SECRET_KEY_STATUS_OPTION', 'appwplp_shared_secret_key_status' );
}

if ( ! defined( 'APPWPLP_SECRET_KEY_VERSION_OPTION' ) ) {
	define( 'APPWPLP_SECRET_KEY_VERSION_OPTION', 'appwplp_secret_key_feature_version' );
}

if ( ! defined( 'APPWPLP_SECRET_KEY_ATTEMPTS_OPTION' ) ) {
	define( 'APPWPLP_SECRET_KEY_ATTEMPTS_OPTION', 'appwplp_secret_key_retry_attempts' );
}

/**
 * Total number of registration posts a site will make before giving up.
 *
 * Counted as posts, not as retries on top of a first try, so eight means eight
 * requests and then silence. On a 15 minute loop that is a two hour window.
 *
 * Verification runs inside the registration request on the server, so a post
 * from a site the server cannot reach holds a php-fpm worker there for the full
 * timeout. Without a cap an unreachable site pays that cost every 15 minutes
 * forever; with one, each site's total cost is bounded and the traffic stops on
 * its own.
 */
if ( ! defined( 'APPWPLP_SECRET_KEY_MAX_ATTEMPTS' ) ) {
	define( 'APPWPLP_SECRET_KEY_MAX_ATTEMPTS', 8 );
}


/**
 * Temporay fix for a critical error
 */
add_action( 'plugins_loaded', function () {
    $opt = get_option( 'gdpr_no_of_scans' );

    if ( is_array( $opt ) ) {
        // if array has exactly one element → get the value
        $value = reset( $opt );  

        // overwrite option with the value only
        update_option( 'gdpr_no_of_scans', $value );
    }
}, 0 );

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
 * Generates a cryptographically strong 32-character secret key.
 *
 * @return string
 */
if ( ! function_exists( 'appwplp_generate_secret_key' ) ) {
	function appwplp_generate_secret_key() {
		// random_bytes(16) -> 32 hex characters. Cryptographically secure.
		return bin2hex( random_bytes( 16 ) );
	}
}
/**
 * Single-flight guard shared by WP Cookie Consent and WPLegalPages.
 *
 * Both plugins listen on `appwplp_secret_key_generated` and post the very same
 * key to the very same endpoint, and each one triggers the routine on its own
 * activation and version upgrade. Without a shared claim a single site ends up
 * registering the key several times over.
 *
 * Only the first caller wins - inside one request through the static flag, and
 * across requests through a lock that expires shortly before the 15 minute
 * retry cron is due, so a genuine retry is never blocked.
 *
 * The function is defined once, by whichever of the two plugins loads first, so
 * both share the same static flag and the same lock.
 *
 * @return bool True when the caller owns this registration attempt.
 */
if ( ! function_exists( 'appwplp_claim_secret_key_registration' ) ) {
	function appwplp_claim_secret_key_registration() {
		static $claimed = false;

		// A second listener in the same request - the key is already going out.
		if ( $claimed ) {
			return false;
		}

		// An attempt from another request is still inside the current retry window.
		if ( get_transient( 'appwplp_secret_key_registration_lock' ) ) {
			return false;
		}

		$claimed = true;

		// One minute short of the retry interval so the cron tick always gets through.
		set_transient( 'appwplp_secret_key_registration_lock', time(), 14 * MINUTE_IN_SECONDS );

		return true;
	}
}

/**
 * Generates and stores a local secret key for this site, if one doesn't
 * already exist. Does NOT register it with the server - that happens in
 * step 3, triggered separately after this runs.
 */
if ( ! function_exists( 'appwplp_maybe_generate_secret_key' ) ) {
	function appwplp_maybe_generate_secret_key() {
		$existing_key    = get_option( APPWPLP_SECRET_KEY_OPTION );
		$existing_status = get_option( APPWPLP_SECRET_KEY_STATUS_OPTION );

		if ( ! empty( $existing_key ) && 'confirmed' === $existing_status ) {
			$timestamp = wp_next_scheduled( 'appwplp_secret_key_retry_event' );
			if ( $timestamp ) {
				wp_clear_scheduled_hook( 'appwplp_secret_key_retry_event' );
			}
			delete_option( APPWPLP_SECRET_KEY_ATTEMPTS_OPTION );
			return;
		}

		/*
		 * Out of attempts - stop the loop for good.
		 *
		 * The counter is raised by the listener that actually posts, so an
		 * attempt is never spent by the second plugin standing down on the
		 * shared claim. The counter is cleared by the one-time feature version
		 * check, so the next plugin update allows a fresh set - until then a site
		 * that was unreachable while these ran out stays stopped.
		 */
		if ( (int) get_option( APPWPLP_SECRET_KEY_ATTEMPTS_OPTION, 0 ) >= APPWPLP_SECRET_KEY_MAX_ATTEMPTS ) {
			$timestamp = wp_next_scheduled( 'appwplp_secret_key_retry_event' );
			if ( $timestamp ) {
				wp_clear_scheduled_hook( 'appwplp_secret_key_retry_event' );
			}
			return;
		}

		if ( ! empty( $existing_key ) ) {
			update_option( APPWPLP_SECRET_KEY_STATUS_OPTION, 'pending', false );
			do_action( 'appwplp_secret_key_generated', $existing_key );
		} else {
			/*
			* First installation - generate the key.
			*/
			$new_key = appwplp_generate_secret_key();
			update_option( APPWPLP_SECRET_KEY_OPTION, $new_key, false );
			update_option( APPWPLP_SECRET_KEY_STATUS_OPTION, 'pending', false );

			do_action( 'appwplp_secret_key_generated', $new_key );
			
		}
		if ( ! wp_next_scheduled( 'appwplp_secret_key_retry_event' ) ) {
			wp_schedule_event( time() + ( 15 * MINUTE_IN_SECONDS ), 'appwplp_fifteen_minutes', 'appwplp_secret_key_retry_event' );
		}
	}
}

add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['appwplp_fifteen_minutes'] = array(
		'interval' => 15 * MINUTE_IN_SECONDS,
		'display'  => 'Every 15 Minutes',
	);
	return $schedules;
} );

add_action( 'appwplp_secret_key_retry_event', 'appwplp_maybe_generate_secret_key' );

/**
 * Runs the secret key routine once per site, on the first admin load.
 *
 * Deliberately not hooked to register_activation_hook(). Core appends the
 * plugin to `active_plugins` only *after* the activation hook has fired, so a
 * key posted from there is verified by the server calling straight back into a
 * WordPress that has not loaded this plugin - /verify_connection is not
 * registered yet and the handshake 404s. By the first admin_init the plugin is
 * active and that route answers.
 *
 * The activation hook would also miss in-place plugin updates, so a stored
 * feature version is compared against the current one either way; this runs
 * exactly once per site per feature version, covering both a fresh activation
 * and an upgrade from a version without the feature.
 *
 * @return void
 */
function appwplp_secret_key_version_check() {
	if ( APPWPLP_SECRET_KEY_FEATURE_VERSION === get_option( APPWPLP_SECRET_KEY_VERSION_OPTION ) ) {
		return;
	}

	/*
	 * A fresh activation or an upgrade is someone actively trying to get this
	 * connected, so allow a fresh set of attempts rather than staying capped out.
	 */
	delete_option( APPWPLP_SECRET_KEY_ATTEMPTS_OPTION );

	appwplp_maybe_generate_secret_key();

	update_option( APPWPLP_SECRET_KEY_VERSION_OPTION, APPWPLP_SECRET_KEY_FEATURE_VERSION, false );
}
add_action( 'admin_init', 'appwplp_secret_key_version_check' );

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
		exit( esc_html( wp_redirect( admin_url( 'admin.php?page=wplp-dashboard' ) ) ) );
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

	delete_option( GDPR_COOKIE_CONSENT_SETTINGS_REVOKE_ICON );
	delete_option( GDPR_COOKIE_CONSENT_SETTINGS_REVOKE_ICON1 );
	delete_option( GDPR_COOKIE_CONSENT_SETTINGS_REVOKE_ICON2 );

	delete_option('gdpr_default_template_object');
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
		echo '<p>' . esc_html__('Action Required: Switch to the New WPLP Compliance Platform! The new platform no longer requires Pro plugins.', 'gdpr-cookie-consent') . 
		' <a href="https://wplegalpages.com/docs/migration-from-wpeka/migration/seamless-migration-to-the-new-wp-legal-pages-compliance-platform/" target="_blank" rel="noopener noreferrer" previewlistener="true">' . 
		esc_html__('Follow this guide to migrate now.', 'gdpr-cookie-consent') . '</a></p>';
		echo '</div>';
	}
}
// Added for plugin tour
function gdpr_complete_tour() {
    check_ajax_referer( 'gdpr-cookie-consent', '_ajax_nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( esc_html__( 'You do not have permission to perform this action.', 'gdpr-cookie-consent' ), 403 );
    }
    update_option('gdpr_first_time_installed', false);
    wp_send_json_success();
}
add_action('wp_ajax_gdpr_complete_tour', 'gdpr_complete_tour');
