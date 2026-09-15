<?php
/**
 * Provide a dashboard view for the admin.
 *
 * This file is used to markup the admin-facing aspects of the plugin (Dashboard Page).
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();

$cookie_scan_settings = array();
$cookie_scan_settings = apply_filters( 'gdpr_settings_cookie_scan_values', '' );

// check if pro is activated or installed.

$pro_is_activated  = get_option( 'wpl_pro_active', false );
$installed_plugins = get_plugins();
$pro_installed     = isset( $installed_plugins['wpl-cookie-consent/wpl-cookie-consent.php'] ) ? true : false;
$pro_is_activated = get_option( 'wpl_pro_active', false );
$api_key_activated = '';
$api_key_activated = get_option( 'wc_am_client_wpl_cookie_consent_activated' );

$plugin_name                   = 'wplegalpages/wplegalpages.php';
$legal_pages_installed     = isset( $installed_plugins['wplegalpages/wplegalpages.php'] ) ? true : false;
$gdpr_installed     = isset( $installed_plugins['gdpr-cookie-consent/gdpr-cookie-consent.php'] ) ? true : false;
$is_legalpages_active = is_plugin_active( $plugin_name );
$plugin_name_gdpr                   = 'gdpr-cookie-consent/gdpr-cookie-consent.php';
$is_gdpr_active = is_plugin_active( $plugin_name_gdpr );
$image_path = GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/';
$legalpages_install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=wplegalpages' ), 'install-plugin_wplegalpages' );
$legalpages_activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin_name . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin_name );
$help_page_tab_url = admin_url() . 'admin.php?page=wplp-dashboard#help-page';
$all_legal_pages_url = admin_url() . 'admin.php?page=legal-pages#all_legal_pages';
$create_legalpages_url = admin_url() . 'admin.php?page=wplegal-wizard#/';
$script_blocker_url = admin_url() . 'admin.php?page=gdpr-cookie-consent#cookie_manager#script_blocker';
// Require the class file for gdpr cookie consent api framework settings.
require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';

// Instantiate a new object of the GDPR_Cookie_Consent_Settings class.
$this->settings = new GDPR_Cookie_Consent_Settings();
$api_user_plan     = $this->settings->get_plan();
$gdpr_monthly_page_views = get_option('wpl_monthly_page_views', 0);
$gdpr_monthly_page_views_limit = 0;
if ( 'free' === $api_user_plan ) { 
	$gdpr_monthly_page_views_limit = 20000;
} else if ( '3sites' === $api_user_plan ) {
	$gdpr_monthly_page_views_limit = 100000;
}// Call the is_connected() method from the instantiated object to check if the user is connected.
$is_user_connected = $this->settings->is_connected();

$class_for_blur_content = $is_user_connected ? '' : 'gdpr-blur-background'; // Add a class for styling purposes.

$class_for_card_body_blur_content = $is_user_connected ? '' : 'gdpr-body-blur-background'; // Add a class for styling purposes.


/**
 * Total No of scanned cookies.
 */
if ( ! empty( $cookie_scan_settings ) ) {
	$total_no_of_found_cookies = $cookie_scan_settings['scan_cookie_list']['total'];
} else {
	$total_no_of_found_cookies = 0;
}

/**
 * Total No of cookie categories.
 */
if ( ! empty( $cookie_scan_settings ) ) {
	$scan_cookie_list = $cookie_scan_settings['scan_cookie_list'];

	// Create an array to store unique category names.
	$unique_categories = array();

	// Loop through the 'data' sub-array.
	foreach ( $scan_cookie_list['data'] as $cookie ) {
		$category = $cookie['category'];

		// Check if the category is not already in the $uniqueCategories array.
		if ( ! in_array( $category, $unique_categories ) ) {
			// If it's not in the array, add it.
			$unique_categories[] = $category;
		}
	}

	// Count the number of unique categories.
	$number_of_categories = count( $unique_categories );
} else {
	$number_of_categories = 0;
}

/**
 * Total no of scanned pages.
 */
global $wpdb;

// // The table name you want to check for existence.
// $table_name = $wpdb->prefix . 'wpl_cookie_scan';

// // Check if the table exists in the database.
// $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;   //phpcs:ignore

// if ( $table_exists ) {
// 	// The table exists, so you can fetch the total_url.
// 	$result = $wpdb->get_results("SELECT total_url FROM $table_name");  //phpcs:ignore
// 	// echo '<pre>';
//     // print_r(get_option('gdpr_no_of_page_scan'));
//     // echo '</pre>';

// 	if ( ! empty( $result ) ) {
// 		// Access the value of total_url.

// 		$total_scanned_pages = $result[0]->total_url;
// 	} else {
// 		$total_scanned_pages = '0 Pages';
// 	}
// } else {
// 	// The table doesn't exist, so set $total_scanned_pages to "0 Pages".
// 	$total_scanned_pages = '0 Pages';
// }
$total_scanned_pages = get_option('gdpr_last_scan') . " Pages";

// $total_scanned_pages = count($url_arr);


// // Print or use the $total_cookies value
// echo "Total Cookies: " . $total_cookies;


ob_start(); // Start output buffering

// Trigger the gdpr_consent_log_table_dashboard action
do_action( 'gdpr_consent_log_table_dashboard' );

// Get the buffered content and clean the buffer
$consent_log_table = ob_get_clean();

// Get the current selected policy name
$cookie_usage_for = $the_options['cookie_usage_for'];
$gdpr_policy = '';

if($cookie_usage_for == 'eprivacy'){
	$gdpr_policy = 'ePrivacy';
}elseif($cookie_usage_for == 'both'){
	$gdpr_policy = 'GDPR & CCPA';
}else{
	$gdpr_policy = strtoupper($cookie_usage_for);
}
/**
 * Send a POST request to the GDPR API endpoint 'get_data'
*/

$response = wp_remote_post(
	GDPR_API_URL . 'get_dashboard_data',
	array(
		'body' => array(
			'cookie_scan_settings'             => $cookie_scan_settings,
			$saved_schedule_data = get_option('gdpr_scan_schedule_data', array()),
			$schedule_scan_when = isset($saved_schedule_data['schedule_scan_when']) ? $saved_schedule_data['schedule_scan_when'] : null,
			'schedule_scan_when' => $schedule_scan_when,
			'pro_installed'                    => $pro_installed,
			'pro_is_activated'                 => $pro_is_activated,
			'api_key_activated'                => $api_key_activated,
			'is_user_connected'                => $is_user_connected,
			'class_for_blur_content'           => $class_for_blur_content,
			'class_for_card_body_blur_content' => $class_for_card_body_blur_content,
			'total_no_of_found_cookies'        => $total_no_of_found_cookies,
			'total_scanned_pages'              => $total_scanned_pages,
			'number_of_categories'             => $number_of_categories,
			'wpl_cl_decline'                   => get_option( 'wpl_cl_decline' ),
			'wpl_page_views'				   => get_option( 'wpl_page_views' ),
			'total_page_views'				   => get_option('wpl_total_page_views'),
			'wpl_cl_accept'                    => get_option( 'wpl_cl_accept' ),
			'wpl_cl_partially_accept'          => get_option( 'wpl_cl_partially_accept' ),
			'wpl_cl_bypass'                    => get_option( 'wpl_cl_bypass' ),
			'consent_log_table'                => $consent_log_table,
			'admin_url'                        => admin_url(),
			'cookie_usage_for'                 => $gdpr_policy,
			'script_blocker_url'			   => $script_blocker_url,
			'plan'						       => $api_user_plan,
			'gdpr_monthly_page_views'		   => $gdpr_monthly_page_views,
			'gdpr_monthly_page_views_limit'   => $gdpr_monthly_page_views_limit,
		),
	)
);

// Check if there's an error with the request.
if ( is_wp_error( $response ) ) {
	// Set $api_gdpr_dashboard to an empty string if there's an error.
	$api_gdpr_dashboard = '';
}
// Retrieve the response status code.
$response_status = wp_remote_retrieve_response_code( $response );

// Check if the response status is 200 (success).
if ( 200 === $response_status ) {
	// Decode the JSON response body and assign it to $api_gdpr_dashboard.
	$api_gdpr_dashboard = json_decode( wp_remote_retrieve_body( $response ) );
}



?>
<div id="gdpr-dashboard-loader">
	<div style="text-align:center;">
  	  	<div class="gdpr-dashboard-loader-content"></div>
  	  	<p class="gdpr-dashboard-loader-text">
  	  	  	Loading...
  	  	</p>
  	</div>
</div>
<?php 
?>
<!-- jQuery for steps progressbar -->
<script>

jQuery(document).ready(function () {
	var plugin_url = "<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ); ?>";

	// Step 1
	if (jQuery(".vstep1").hasClass("gdpr-green-progress")) {
		jQuery("#vertical-progressbar #step1 img").attr("src", plugin_url + "admin/images/greentick.svg");
		jQuery("#vertical-progressbar .vertical-line-step-1").css("background", "var(--green-700)");
		jQuery("#vertical-progressbar .vertical-line-step-init").css("background", "var(--green-700)");
	} else {
		jQuery("#vertical-progressbar #step1 img").attr("src", plugin_url + "admin/images/not-selected-step-progress.png");
		jQuery("#vertical-progressbar .vertical-line-step-1").css("background", "");
		jQuery("#vertical-progressbar .vertical-line-step-init").css("background", "");
	}

	// Step 2
	if (jQuery(".vstep2").hasClass("gdpr-green-progress")) {
		jQuery("#vertical-progressbar #step2 img").attr("src", plugin_url + "admin/images/greentick.svg");
		jQuery("#vertical-progressbar .vertical-line-step-2").css("background", "var(--green-700)");
	} else {
		jQuery("#vertical-progressbar #step2 img").attr("src", plugin_url + "admin/images/not-selected-step-progress.png");
		jQuery("#vertical-progressbar .vertical-line-step-2").css("background", "");
	}

	// Step 3
	if (jQuery(".vstep3").hasClass("gdpr-green-progress")) {
		jQuery("#vertical-progressbar #step3 img").attr("src", plugin_url + "admin/images/greentick.svg");
		jQuery("#vertical-progressbar .vertical-line-step-3").css("background", "var(--green-700)");
	} else {
		jQuery("#vertical-progressbar #step3 img").attr("src", plugin_url + "admin/images/not-selected-step-progress.png");
		jQuery("#vertical-progressbar .vertical-line-step-3").css("background", "");
	}

	// Step 4
	if (jQuery(".vstep4").hasClass("gdpr-green-progress")) {
		jQuery("#vertical-progressbar #step4 img").attr("src", plugin_url + "admin/images/greentick.svg");
		jQuery("#vertical-progressbar .vertical-line-step-4").css("background", "var(--green-700)");
	} else {
		jQuery("#vertical-progressbar #step4 img").attr("src", plugin_url + "admin/images/not-selected-step-progress.png");
		jQuery("#vertical-progressbar .vertical-line-step-4").css("background", "");
	}
	// Step 5
	if (jQuery(".vstep5").hasClass("gdpr-green-progress")) {
		jQuery("#vertical-progressbar #step5 img").attr("src", plugin_url + "admin/images/greentick.svg");
		jQuery("#vertical-progressbar .vertical-line-step-5").css("background", "var(--green-700)");
	} else {
		jQuery("#vertical-progressbar #step5 img").attr("src", plugin_url + "admin/images/not-selected-step-progress.png");
		jQuery("#vertical-progressbar .vertical-line-step-5").css("background", "");
	}

	// Count the number of divs with the class gdpr-gray-progress
    var progcount = jQuery('#gdpr-cookie-consent-dashboard-page .gdpr-gray-progress').length;
    
    // Update the sentence with the count
	if(progcount > 0){
    	jQuery('.tasks-heading').text('You still have ' + progcount + ' tasks open.');
	}
	else{
    	jQuery('.tasks-heading').text('You have 0 tasks open');
	}

	var dashboardOptions = <?php echo json_encode($api_gdpr_dashboard); ?>;

});
</script>
