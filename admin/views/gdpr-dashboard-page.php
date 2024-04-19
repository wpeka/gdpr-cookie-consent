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
// Require the class file for gdpr cookie consent api framework settings.
require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';

// Instantiate a new object of the GDPR_Cookie_Consent_Settings class.
$this->settings = new GDPR_Cookie_Consent_Settings();

// Call the is_connected() method from the instantiated object to check if the user is connected.
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

// The table name you want to check for existence.
$table_name = $wpdb->prefix . 'wpl_cookie_scan';

// Check if the table exists in the database.
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;   //phpcs:ignore

if ( $table_exists ) {
	// The table exists, so you can fetch the total_url.
	$result = $wpdb->get_results("SELECT total_url FROM $table_name");  //phpcs:ignore

	if ( ! empty( $result ) ) {
		// Access the value of total_url.
		$total_scanned_pages = $result[0]->total_url;
	} else {
		$total_scanned_pages = '0 Pages';
	}
} else {
	// The table doesn't exist, so set $total_scanned_pages to "0 Pages".
	$total_scanned_pages = '0 Pages';
}

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
			'schedule_scan_when'               => isset( $the_options['schedule_scan_when'] ) ? $the_options['schedule_scan_when'] : null,
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
			'wpl_cl_accept'                    => get_option( 'wpl_cl_accept' ),
			'wpl_cl_partially_accept'          => get_option( 'wpl_cl_partially_accept' ),
			'consent_log_table'                => $consent_log_table,
			'admin_url'                        => admin_url(),
			'cookie_usage_for'                 => $gdpr_policy
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
<div id="gdpr-dashboard-loader"></div>
<div id="gdpr-cookie-consent-dashboard-page">
	<c-container class="gdpr-cookie-consent-dashboard-container">
		<c-card class="gdpr-progress-bar-card">
			<c-card-body>
				<c-row class="gdpr-progress-bar-heading">
					<c-col class="col-sm-6">
						<span class="gdpr-progress-heading"><?php esc_html_e( 'Your Progress', 'gdpr-cookie-consent' ); ?></span>
					</c-col class="col-sm-6">
				</c-row>
				<c-row>
					<c-col class="col-sm-5 ">
					<div class="gdpr-progress-circle-column">
					<vue-ellipse-progress class="gdpr-progress-bar-class" :progress="progress" line="square" font-size="60px" font-color="#0059B3" color="#0059B3" :size="250" :thickness="20" :dot="0">
							<span class="gdpr-progress-circle-legend" slot="legend-value"><?php esc_html_e( '%', 'gdpr-cookie-consent' ); ?></span>
							<p class="gdpr-progress-circle-caption" slot="legend-caption"><?php esc_html_e( 'Completed', 'gdpr-cookie-consent' ); ?></p>
						</vue-ellipse-progress>
						<div class="progress-bar-caption">
							   <div>
                                <span class="progress-bar-caption-text"><?php esc_html_e( 'Cookie Notice Status : ', 'gdpr-cookie-consent' ); ?></span><span class="progress-bar-caption-button"><?php if($the_options['is_on'] == '1'){echo esc_html_e( 'Live', 'gdpr-cookie-consent' );}else{echo esc_html_e( 'Inactive', 'gdpr-cookie-consent' );} ?></span>
							   </div>
						</div>
					</div>
					</c-col>
					<c-col class="col-sm-7 gdpr-progress-list-column">
						<c-row :class="['gdpr-progress-list-item', !other_plugins_active ? 'gdpr-green-progress' : 'gdpr-gray-progress']">
							<span v-show="!other_plugins_active">
								<?php esc_html_e( 'No other cookies plugin detected.', 'gdpr-cookie-consent' ); ?>
							</span>
							<span v-show="other_plugins_active">
								<?php esc_html_e( 'Other Cookie Consent Plugins detected. ', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="plugin_page_url"><?php esc_html_e( ' Turn them off to avoid conflict.', 'gdpr-cookie-consent' ); ?></a>
							</span>
						</c-row>
						<c-row :class="['gdpr-progress-list-item', (pro_installed && pro_activated && api_key_activated && cookie_scanned)||(!pro_installed && is_user_connected && cookie_scanned) ? 'gdpr-green-progress' : 'gdpr-gray-progress']">
							<span class="gdpr_scan_again_link" v-show="api_key_activated && cookie_scanned">
								<?php esc_html_e( 'Cookies were last scanned on ', 'gdpr-cookie-consent' ); ?>
								{{last_scanned + '.'}}
								<a class="gdpr-progress-list-link" :href="cookie_scan_url"><?php esc_html_e( 'Scan again.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<!-- when pro is not installed and user is conneted to the api and cookie scan performed -->
							<span class="gdpr_scan_again_link" v-show="is_user_connected && cookie_scanned && !pro_installed">
								<?php esc_html_e( 'Cookies were last scanned on ', 'gdpr-cookie-consent' ); ?>
								{{last_scanned + '.'}}
								<a class="gdpr-progress-list-link" :href="cookie_scan_url"><?php esc_html_e( 'Scan again.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<!-- when pro is not installed and user is not conneted to the api -->
							<span v-show="!pro_installed && !is_user_connected ">
								<?php esc_html_e( 'Scan Cookies.', 'gdpr-cookie-consent' ); ?>
								<span class="gdpr-progress-list-link gdpr-dashboard-start-auth"><?php esc_html_e( 'Connect Your Free Account', 'gdpr-cookie-consent' ); ?></span>
							</span>
							<!-- when pro is not installed and user is conneted to the api and cookie scan not performed-->
							<span class="gdpr-dashboard-scan-now" v-show="!pro_installed && is_user_connected && !cookie_scanned">
								{{last_scanned}}
								<a class="gdpr-progress-list-link" :href="cookie_scan_url"><?php esc_html_e( 'Scan now.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<span v-show="pro_installed && !pro_activated">
								<?php esc_html_e( 'Activate Pro plugin to scan cookies.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="plugin_page_url"><?php esc_html_e( 'Click here to activate.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<span class="gdpr-dashboard-activation-tab" v-show="pro_installed && pro_activated && !api_key_activated">
								<?php esc_html_e( 'Activate API license key to scan cookies.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="key_activate_url"><?php esc_html_e( 'Click here to activate.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<span class="gdpr-dashboard-scan-now" v-show="pro_installed && pro_activated && api_key_activated && !cookie_scanned">
								{{last_scanned}}
								<a class="gdpr-progress-list-link" :href="cookie_scan_url"><?php esc_html_e( 'Scan now.', 'gdpr-cookie-consent' ); ?></a>
							</span>
						</c-row>
						<c-row :class="['gdpr-progress-list-item', showing_cookie_notice ? 'gdpr-green-progress' : 'gdpr-gray-progress']">
							<span v-show="showing_cookie_notice">
								<?php esc_html_e( 'Showing Cookie Notice on Website.', 'gdpr-cookie-consent' ); ?>
							</span>
							<span class="gdpr_notice_configure_link" v-show="!showing_cookie_notice">
								<?php esc_html_e( 'Cookie Notice disabled.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="show_cookie_url"><?php esc_html_e( 'Click here to configure.', 'gdpr-cookie-consent' ); ?></a>
							</span>
						</c-row>
						<c-row :class="['gdpr-progress-list-item', (pro_installed && pro_activated && api_key_activated)||(!pro_installed && is_user_connected) ? 'gdpr-green-progress' : 'gdpr-gray-progress']">
							<span v-show="pro_installed && pro_activated && api_key_activated">
								<?php esc_html_e( 'GDPR Pro activated.', 'gdpr-cookie-consent' ); ?>
							</span>
							<!-- when pro is not installed and user is not conneted to the api -->
							<span v-show="!pro_installed && !is_user_connected">
								<?php esc_html_e( 'Connect Your Free Account.', 'gdpr-cookie-consent' ); ?>
								<span class="gdpr-progress-list-link gdpr-dashboard-start-auth"><?php esc_html_e( 'Click here.', 'gdpr-cookie-consent' ); ?></span>
							</span>
							<!-- when pro is not installed and user is conneted to the api -->
							<span v-show="!pro_installed && is_user_connected">
								<?php esc_html_e( 'Website is connected to WP Cookie Consent.', 'gdpr-cookie-consent' ); ?>
							</span>
							<span v-show="pro_installed && !pro_activated">
								<?php esc_html_e( 'Activate GDPR Pro plugin.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="plugin_page_url"><?php esc_html_e( 'Click here to activate.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<span class="gdpr-dashboard-activation-tab" v-show="pro_installed && pro_activated && !api_key_activated">
								<?php esc_html_e( 'Activate your API license key.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="key_activate_url"><?php esc_html_e( 'Click here to activate.', 'gdpr-cookie-consent' ); ?></a>
							</span>
						</c-row>
						<c-row :class="['gdpr-progress-list-item', (pro_installed && pro_activated && api_key_activated  && maxmind_integrated)||(!pro_installed && is_user_connected && maxmind_integrated ) ? 'gdpr-green-progress' : 'gdpr-gray-progress']">
							<span v-show="pro_installed && pro_activated && api_key_activated && maxmind_integrated">
								<?php esc_html_e( 'Integrated with Maxmind.', 'gdpr-cookie-consent' ); ?>
							</span>
							<!-- when pro is not installed and user is conneted to the api and maxmind is connected-->
							<span v-show="!pro_installed && is_user_connected && maxmind_integrated">
								<?php esc_html_e( 'Integrated with Maxmind.', 'gdpr-cookie-consent' ); ?>
							</span>
							<!-- when pro is not installed and user is not conneted to the api -->
							<span v-show="!pro_installed && !is_user_connected">
								<?php esc_html_e( 'Enable Geotargeting With MaxMind Integration.', 'gdpr-cookie-consent' ); ?>
								<span class="gdpr-progress-list-link gdpr-dashboard-start-auth"><?php esc_html_e( 'Connect Your Free Account.', 'gdpr-cookie-consent' ); ?></span>
							</span>
							<!-- when pro is not installed and user is conneted to the api and maxmind is not connected -->
							<span class="gdpr-dashboard-maxmind-integrate" v-show="!pro_installed && is_user_connected && !maxmind_integrated">
								<?php esc_html_e( 'Integrate with Maxmind for free.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="maxmind_url"><?php esc_html_e( 'Click here to configure.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<span v-show="pro_installed && !pro_activated">
								<?php esc_html_e( 'Activate Pro plugin to enable Geotargeting.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="plugin_page_url"><?php esc_html_e( 'Click here to activate.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<span class="gdpr-dashboard-activation-tab" v-show="pro_installed && pro_activated && !api_key_activated">
								<?php esc_html_e( 'Activate API license key to enable Geotargeting.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="key_activate_url"><?php esc_html_e( 'Click here to activate.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<span class="gdpr-dashboard-maxmind-integrate" v-show="pro_installed && pro_activated && api_key_activated && !maxmind_integrated">
								<?php esc_html_e( 'Integrate with Maxmind for free.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="maxmind_url"><?php esc_html_e( 'Click here to configure.', 'gdpr-cookie-consent' ); ?></a>
							</span>
						</c-row>
					</c-col>
				</c-row>
			</c-card-body>
		</c-card>
		<!-- cookie insights and cookie summary card  -->

		<?php echo $api_gdpr_dashboard; ?>

		<c-card class="gdpr-dashboard-quick-links-card">
				<h1 class="gdpr-dashboard-quick-links-heading">
					<?php esc_html_e( 'Quick Links', 'gdpr-cookie-consent' ); ?>
				</h1>
			
			<c-card-body class="gdpr-dashboard-quick-links-body">
					<div class="gdpr-quick-link-item settings">
						<a class="gdpr-quick-link" :href="show_cookie_url">
							<img class="gdpr-quick-link-image" :src="settings_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
							<?php esc_html_e( 'Settings', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-quick-link-description">
							<?php esc_html_e( 'Configure your cookie banner settings easily.', 'gdpr-cookie-consent' ); ?>
						</span>
					</div>
					<div class="gdpr-quick-link-item cookie_banner">
						<a class="gdpr-quick-link" :href="cookie_design_url">
							<img class="gdpr-quick-link-image" :src="cookie_design_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
							<?php esc_html_e( 'Design Cookie Banner', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-quick-link-description">
							<?php esc_html_e( 'Effortlessly design your cookie banner.', 'gdpr-cookie-consent' ); ?>
						</span>
					</div>
					<div class="gdpr-quick-link-item script_blocker">
						<a class="gdpr-quick-link" :href="script_blocker_url">
							<img class="gdpr-quick-link-image" :src="script_blocker_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
							<?php esc_html_e( 'Script Blocker', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-quick-link-description">
							<?php esc_html_e( 'Auto-block known third-party cookies.', 'gdpr-cookie-consent' ); ?>
						</span>
					</div>
					<div class="gdpr-quick-link-item consent_logs">
						<a class="gdpr-quick-link" :href="consent_log_url">
							<img class="gdpr-quick-link-image" :src="consent_log_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
							<?php esc_html_e( 'Consent Log', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-quick-link-description">
							<?php esc_html_e( 'Stores a consent log of consents given by website visitors.', 'gdpr-cookie-consent' ); ?>
						</span>
					</div>
				<div class="gdpr-quick-link-item scan_cookies">
						<a class="gdpr-quick-link" :href="cookie_scan_url">
							<img class="gdpr-quick-link-image" :src="cookie_scan_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
							<?php esc_html_e( 'Scan Cookies', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-quick-link-description">
							<?php esc_html_e( 'Quickly detects all your website cookies in one-click.', 'gdpr-cookie-consent' ); ?>
						</span>
					</div>
					<div class="gdpr-quick-link-item geo_targeting">
						<a class="gdpr-quick-link" :href="maxmind_url">
							<img class="gdpr-quick-link-image" :src="geolocation_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
							<?php esc_html_e( 'Geotargeting', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-quick-link-description">
							<?php esc_html_e( "Display or hide the cookie consent based on visitor's location.", 'gdpr-cookie-consent' ); ?>
						</span>
					</div>
					
					<div class="gdpr-quick-link-item banner_template">
						<a class="gdpr-quick-link" :href="cookie_template_url">
							<img class="gdpr-quick-link-image" :src="cookie_template_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
							<?php esc_html_e( 'Banner Templates', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-quick-link-description">
							<?php esc_html_e( 'Choose a banner design from a set of pre-designed templates.', 'gdpr-cookie-consent' ); ?>
						</span>
					</div>
					
					<div class="gdpr-quick-link-item policy_data">
						<a class="gdpr-quick-link" :href="third_party_url">
							<img class="gdpr-quick-link-image" :src="cookie_table_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
							<?php esc_html_e( 'Third Party Details', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-quick-link-description">
							<?php esc_html_e( 'Automatically fetches the 3rd party cookie details.', 'gdpr-cookie-consent' ); ?>
						</span>
					</div>
				</c-card-body>
		</c-card>

		<c-card class="gdpr-dashboard-help-card">
				<h1 class="gdpr-dashboard-help-heading">
					<?php esc_html_e( 'Help', 'gdpr-cookie-consent' ); ?>
				</h1>

				<c-card-body class="gdpr-dashboard-help-body">
				<div class="gdpr-help-item">
							<img class="gdpr-other-plugin-image" :src="documentation.default">
						<div class="gdpr-help-content">
						<span class="gdpr-help-caption">
							<?php esc_html_e( 'Documentation', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-help-description">
							<?php esc_html_e( 'If you need help understanding, using, or extending WP Cookie Consent Plugin.', 'gdpr-cookie-consent' ); ?>
						</span>
						<a href="https://club.wpeka.com/docs/wp-cookie-consent/" target="_blank" class="gdpr-help-button"><?php esc_html_e( 'Read Documents', 'gdpr-cookie-consent' ); ?> <img class="gdpr-other-plugin-arrow" :src="right_arrow.default"></a>
						</div>
					</div>
					<div class="gdpr-help-item">
							<img class="gdpr-other-plugin-image" :src="video_guide.default">
						<div class="gdpr-help-content">
						<span class="gdpr-help-caption">
							<?php esc_html_e( 'Video Guides', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-help-description">
							<?php esc_html_e( 'Explore video tutorials for insights on WP Cookie Consent functionality.', 'gdpr-cookie-consent' ); ?>
						</span>
						<a href="https://club.wpeka.com/docs/wp-cookie-consent/video-guides/video-resources/" target="_blank" class="gdpr-help-button"><?php esc_html_e( 'Watch Now', 'gdpr-cookie-consent' ); ?> <img class="gdpr-other-plugin-arrow" :src="right_arrow.default"></a>
						</div>
					</div>
					<div class="gdpr-help-item">
							<img class="gdpr-other-plugin-image" :src="faq_question.default">
						<div class="gdpr-help-content">
						<span class="gdpr-help-caption">
							<?php esc_html_e( 'FAQ with Answers', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-help-description">
							<?php esc_html_e( 'Find answers to some of the most commonly asked questions.', 'gdpr-cookie-consent' ); ?>
						</span>
						<a href="https://club.wpeka.com/docs/wp-cookie-consent/faqs/faq-2/" target="_blank" class="gdpr-help-button"><?php esc_html_e( 'Find Out', 'gdpr-cookie-consent' ); ?> <img class="gdpr-other-plugin-arrow" :src="right_arrow.default"></a>
						</div>
					</div>
					<div class="gdpr-help-item">
							<img class="gdpr-other-plugin-image" :src="feedback.default">
						<div class="gdpr-help-content">
						<span class="gdpr-help-caption">
							<?php esc_html_e( 'Feedback', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-help-description">
							<?php esc_html_e( 'Enjoy our WordPress plugin? Share your feedback!', 'gdpr-cookie-consent' ); ?>
						</span>
						<a href="https://wordpress.org/support/plugin/gdpr-cookie-consent/reviews/" target="__blank" class="gdpr-help-button"><?php esc_html_e( 'Share Reviews', 'gdpr-cookie-consent' ); ?> <img class="gdpr-other-plugin-arrow" :src="right_arrow.default"></a>
						</div>
					</div>
					<div class="gdpr-help-item">
							<img class="gdpr-other-plugin-image" :src="found_bug.default">
						<div class="gdpr-help-content">
						<span class="gdpr-help-caption">
							<?php esc_html_e( 'Found Bug ?', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-help-description">
							<?php esc_html_e( 'Report bugs in the WP Cookie Consent plugin by creating a helpdesk ticket.', 'gdpr-cookie-consent' ); ?>
						</span>
						<a href="https://club.wpeka.com/contact/" target="__blank" class="gdpr-help-button"><?php esc_html_e( 'Go To Help Desk', 'gdpr-cookie-consent' ); ?> <img class="gdpr-other-plugin-arrow" :src="right_arrow.default"></a>
						</div>
					</div>
				</c-card-body>
		</c-card>

		<c-card class="gdpr-dashboard-other-plugin-card">
				<h1 class="gdpr-dashboard-other-plugin-heading">
					<?php esc_html_e( 'Other Plugins by WPeka Club', 'gdpr-cookie-consent' ); ?>
				</h1>

				<c-card-body class="gdpr-dashboard-other-plugin-body">
					<div class="gdpr-other-plugin-item">
								<img class="gdpr-other-plugin-image" :src="legalpages_icon.default">
						<div class="gdpr-other-plugin-content">
						<span class="gdpr-other-plugin-caption">
							<?php esc_html_e( 'WP Legal Pages', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-other-plugin-description">
							<?php esc_html_e( 'Privacy Policy Generator, Terms & Conditions Generator WordPress Plugin.', 'gdpr-cookie-consent' ); ?>
						</span>
						<a href="https://wordpress.org/plugins/wplegalpages/" target="_blank" class="gdpr-other-plugin-button"><?php esc_html_e( 'Get Plugin', 'gdpr-cookie-consent' ); ?> <img class="gdpr-other-plugin-arrow" :src="right_arrow.default"></a>
						</div>
					</div>
					<div class="gdpr-other-plugin-item">
							<img class="gdpr-other-plugin-image" :src="adcenter_icon.default">
						<div class="gdpr-other-plugin-content">
						<span class="gdpr-other-plugin-caption">
							<?php esc_html_e( 'WP Adcenter', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-other-plugin-description">
							<?php esc_html_e( 'Ad Manager & Adsense Ads.', 'gdpr-cookie-consent' ); ?>
						</span>
						<a href="https://wordpress.org/plugins/wpadcenter/" target="_blank" class="gdpr-other-plugin-button"><?php esc_html_e( 'Get Plugin', 'gdpr-cookie-consent' ); ?> <img class="gdpr-other-plugin-arrow" :src="right_arrow.default"></a>
						</div>
					</div>
					<div class="gdpr-other-plugin-item">
							<img class="gdpr-other-plugin-image" :src="survey_funnel_icon.default">
						<div class="gdpr-other-plugin-content">
						<span class="gdpr-other-plugin-caption">
							<?php esc_html_e( 'Survey Funnel', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-other-plugin-description">
							<?php esc_html_e( 'Survey Plugin for WordPress.', 'gdpr-cookie-consent' ); ?>
						</span>
						<a href="https://wordpress.org/plugins/surveyfunnel-lite/" target="_blank" class="gdpr-other-plugin-button"><?php esc_html_e( 'Get Plugin', 'gdpr-cookie-consent' ); ?> <img class="gdpr-other-plugin-arrow" :src="right_arrow.default"></a>
						</div>
					</div>
					<div class="gdpr-other-plugin-item">
							<img class="gdpr-other-plugin-image" :src="localplus_icon.default">
						<div class="gdpr-other-plugin-content">
						<span class="gdpr-other-plugin-caption">
							<?php esc_html_e( 'WP Local Plus', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-other-plugin-description">
							<?php esc_html_e( 'WordPress Directory Plugin For Business Listings.', 'gdpr-cookie-consent' ); ?>
						</span>
						<a href="https://wordpress.org/plugins/wplocalplus-lite/" target="_blank" class="gdpr-other-plugin-button"><?php esc_html_e( 'Get Plugin', 'gdpr-cookie-consent' ); ?> <img class="gdpr-other-plugin-arrow" :src="right_arrow.default"></a>
						</div>
					</div>
					<div class="gdpr-other-plugin-item">
							<img class="gdpr-other-plugin-image" :src="auction_icon.default">
						<div class="gdpr-other-plugin-content">
						<span class="gdpr-other-plugin-caption">
							<?php esc_html_e( 'WP Auction Software', 'gdpr-cookie-consent' ); ?>
						</span>
						<span class="gdpr-other-plugin-description">
							<?php esc_html_e( 'Live auctions on your website.', 'gdpr-cookie-consent' ); ?>
						</span>
						<a href="https://wordpress.org/plugins/auction-software/" target="__blank" class="gdpr-other-plugin-button"><?php esc_html_e( 'Get Plugin', 'gdpr-cookie-consent' ); ?> <img class="gdpr-other-plugin-arrow" :src="right_arrow.default"></a>
						</div>
					</div>
				</c-card-body>
		</c-card>

		<c-card class="gdpr-dashboard-tips-tricks-card">
			<header class="gdpr-dashboard-tips-tricks-heading">
				<h1 class="gdpr-dashboard-tips-tricks-title">
					<?php esc_html_e( 'Tips and Tricks', 'gdpr-cookie-consent' ); ?>
				</h1>
				<a href="https://www.wpeka.com/" target="_blank">
				<button class="gdpr-dashboard-tips-tricks-button">
                <?php esc_html_e('Visit Our Blog ','gdpr-cookie-consent') ?> <img class="gdpr-other-plugin-arrow" :src="right_arrow.default"> 
				</button>
				</a>
			</header>

			<c-card-body class="gdpr-dashboard-tips-tricks-body">
             <div class="gdpr-dashboard-tips-tricks-body-parts">
              <span class="gdpr-dashboard-tips-tricks-text"><?php esc_html_e( 'How to activate your License Key?', 'gdpr-cookie-consent' ); ?></span>
			  <a href="https://www.youtube.com/watch?v=ZESzSKnUkOg" target="_blank"><img class="gdpr-tips-tricks-arrow" :src="angle_arrow.default"></a>
			 </div>
			 <div class="gdpr-dashboard-tips-tricks-body-parts">
              <span class="gdpr-dashboard-tips-tricks-text"><?php esc_html_e( 'What you need to know about the EU Cookie law?', 'gdpr-cookie-consent' ); ?></span>
			  <a href="https://wplegalpages.com/blog/what-you-need-to-know-about-the-eu-cookie-law/?utm_source=plugin&utm_medium=gdpr&utm_campaign=tips-tricks&utm_content=eu-cookie-law" target="_blank"><img class="gdpr-tips-tricks-arrow" :src="angle_arrow.default"></a>
			 </div>
			 <div class="gdpr-dashboard-tips-tricks-body-parts">
              <span class="gdpr-dashboard-tips-tricks-text"><?php esc_html_e( 'Frequently asked questions', 'gdpr-cookie-consent' ); ?></span>
			  <a href="https://club.wpeka.com/docs/wp-cookie-consent/faqs/faq-2/" target="_blank"><img class="gdpr-tips-tricks-arrow" :src="angle_arrow.default"></a>
			 </div>
			 <div class="gdpr-dashboard-tips-tricks-body-parts">
              <span class="gdpr-dashboard-tips-tricks-text"><?php esc_html_e( 'What are the CCPA regulations and how we can comply?', 'gdpr-cookie-consent' ); ?></span>
			  <a href="https://wplegalpages.com/blog/california-consumer-privacy-act-become-ccpa-compliant-today/?utm_source=plugin&utm_medium=gdpr&utm_campaign=tips-tricks&utm_content=ccpa-regulations" target="_blank"><img class="gdpr-tips-tricks-arrow" :src="angle_arrow.default"></a>
			 </div>
			 <div class="gdpr-dashboard-tips-tricks-body-parts">
              <span class="gdpr-dashboard-tips-tricks-text"><?php esc_html_e( 'All you need to know about IAB', 'gdpr-cookie-consent' ); ?></span>
			  <a href="https://wplegalpages.com/blog/interactive-advertising-bureau-all-you-need-to-know/?utm_source=plugin&utm_medium=gdpr&utm_campaign=tips-tricks&utm_content=iab" target="_blank"><img class="gdpr-tips-tricks-arrow" :src="angle_arrow.default"></a>
			 </div>
			</c-card-body>
		</c-card>
	</c-container>
</div>
