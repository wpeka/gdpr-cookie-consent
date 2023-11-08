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

$cookie_scan_settings                = array();
$cookie_scan_settings                = apply_filters( 'gdpr_settings_cookie_scan_values', '' );

/**
 * Total No of scanned cookies.
 */
if ( ! empty( $cookie_scan_settings ) ){

	$total_no_of_found_cookies = $cookie_scan_settings['scan_cookie_list']['total'];
}else{
	$total_no_of_found_cookies = 0;
}

/**
 * Total No of cookie categories.
 */
if ( ! empty( $cookie_scan_settings ) ){

	$scan_cookie_list = $cookie_scan_settings['scan_cookie_list'];

	// Create an array to store unique category names
	$unique_categories = array();

	// Loop through the 'data' sub-array
	foreach ($scan_cookie_list['data'] as $cookie) {
		$category = $cookie['category'];

		// Check if the category is not already in the $uniqueCategories array
		if (!in_array($category, $unique_categories)) {
			// If it's not in the array, add it
			$unique_categories[] = $category;
		}
}

// Count the number of unique categories
$number_of_categories = count($unique_categories);
}else{
	$number_of_categories = 0;
}

/**
 * Total no of scanned pages
 */
global $wpdb;

// The table name you want to check for existence
$table_name = $wpdb->prefix . 'wpl_cookie_scan';

// Check if the table exists in the database
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;

if ($table_exists) {
	// The table exists, so you can fetch the total_url
	$result = $wpdb->get_results("SELECT total_url FROM $table_name");

	if (!empty($result)) {
		// Access the value of total_url
		$total_scanned_pages = $result[0]->total_url;
	} else {
		$total_scanned_pages = "0 Pages";
	}
} else {
	// The table doesn't exist, so set $total_scanned_pages to "0 Pages"
	$total_scanned_pages = "0 Pages";
}

?>
<div id="gdpr-dashboard-loader"></div>
<div id="gdpr-cookie-consent-dashboard-page">
	<c-container class="gdpr-cookie-consent-dashboard-container">
		<c-card class="gdpr-progress-bar-card">
			<c-card-body>
				<c-row>
					<c-col class="col-sm-6">
						<span class="gdpr-progress-heading">Your Progress</span>
					</c-col class="col-sm-6">
					<c-col class="col-sm-6 gdpr-progress-bar-buttons">
						<a class="gdpr-progress-list-link" :href="documentation_url" target="_blank">
							<c-button color="info" variant="outline" class="gdpr-progress-bar-button">
								<?php esc_html_e( 'Documentation', 'gdpr-cookie-consent' ); ?>
							</c-button>
						</a>
						<a class="gdpr-progress-list-link" :href="videos_url" target="_blank">
							<c-button color="info" variant="outline" class="gdpr-progress-bar-button">
								<?php esc_html_e( 'Video Guides', 'gdpr-cookie-consent' ); ?>
							</c-button>
						</a>
						<a class="gdpr-progress-list-link" :href="pro_support_url" target="_blank">
							<c-button v-show="pro_installed" color="info" variant="outline" class="gdpr-progress-bar-button">
								<?php esc_html_e( 'Support', 'gdpr-cookie-consent' ); ?>
							</c-button>
						</a>
						<a class="gdpr-progress-list-link" :href="free_support_url" target="_blank">
							<c-button v-show="!pro_installed" color="info" variant="outline" class="gdpr-progress-bar-button">
								<?php esc_html_e( 'Support', 'gdpr-cookie-consent' ); ?>
							</c-button>
						</a>
					</c-col class="col-sm-6">
				</c-row>
				<c-row>
					<c-col class="col-sm-5 gdpr-progress-circle-column">
						<vue-ellipse-progress
						class="gdpr-progress-bar-class"
						:progress="progress"
						font-size="4.5rem"
						font-color="#39f"
						color="#39f"
						:size="250"
						:thickness="20"
						:dot="0"
						>
						<span class="gdpr-progress-circle-legend" slot="legend-value">%</span>
						<p class="gdpr-progress-circle-caption" slot="legend-caption">Complete</p>
						</vue-ellipse-progress>
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
						<c-row :class="['gdpr-progress-list-item', pro_installed && pro_activated && api_key_activated && cookie_scanned ? 'gdpr-green-progress' : 'gdpr-gray-progress']">
							<span v-show="api_key_activated && cookie_scanned">
								<?php esc_html_e( 'Cookies were last scanned on ', 'gdpr-cookie-consent' ); ?>
								{{last_scanned + '.'}}
								<a class="gdpr-progress-list-link" :href="cookie_scan_url"><?php esc_html_e( 'Scan again.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<span v-show="!pro_installed">
								<?php esc_html_e( 'Scan Cookies.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" target="_blank" :href="gdpr_pro_url"><?php esc_html_e( 'Pro Feature.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<span v-show="pro_installed && !pro_activated">
								<?php esc_html_e( 'Activate Pro plugin to scan cookies.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="plugin_page_url"><?php esc_html_e( 'Click here to activate.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<span v-show="pro_installed && pro_activated && !api_key_activated">
								<?php esc_html_e( 'Activate API license key to scan cookies.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="key_activate_url"><?php esc_html_e( 'Click here to activate.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<span v-show="pro_installed && pro_activated && api_key_activated && !cookie_scanned">
								{{last_scanned}}
								<a class="gdpr-progress-list-link" :href="cookie_scan_url"><?php esc_html_e( 'Scan now.', 'gdpr-cookie-consent' ); ?></a>
							</span>
						</c-row>
						<c-row :class="['gdpr-progress-list-item', showing_cookie_notice ? 'gdpr-green-progress' : 'gdpr-gray-progress']">
							<span  v-show="showing_cookie_notice">
								<?php esc_html_e( 'Showing Cookie Notice on Website.', 'gdpr-cookie-consent' ); ?>
							</span>
							<span v-show="!showing_cookie_notice">
								<?php esc_html_e( 'Cookie Notice disabled.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="show_cookie_url"><?php esc_html_e( 'Click here to configure.', 'gdpr-cookie-consent' ); ?></a>
							</span>
						</c-row>
						<c-row :class="['gdpr-progress-list-item', pro_installed && pro_activated && api_key_activated ? 'gdpr-green-progress' : 'gdpr-gray-progress']">
							<span v-show="pro_installed && pro_activated && api_key_activated">
								<?php esc_html_e( 'GDPR Pro activated.', 'gdpr-cookie-consent' ); ?>
							</span>
							<span v-show="!pro_installed">
								<?php esc_html_e( 'Install GDPR Pro.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" target="_blank" :href="gdpr_pro_url"><?php esc_html_e( 'Click here.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<span v-show="pro_installed && !pro_activated">
								<?php esc_html_e( 'Activate GDPR Pro plugin.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="plugin_page_url"><?php esc_html_e( 'Click here to activate.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<span v-show="pro_installed && pro_activated && !api_key_activated">
								<?php esc_html_e( 'Activate your API license key.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="key_activate_url"><?php esc_html_e( 'Click here to activate.', 'gdpr-cookie-consent' ); ?></a>
							</span>
						</c-row>
						<c-row :class="['gdpr-progress-list-item', pro_installed && pro_activated && api_key_activated  && maxmind_integrated ? 'gdpr-green-progress' : 'gdpr-gray-progress']">
							<span v-show="pro_installed && pro_activated && api_key_activated && maxmind_integrated">
								<?php esc_html_e( 'Integrated with Maxmind.', 'gdpr-cookie-consent' ); ?>
							</span>
							<span v-show="!pro_installed">
								<?php esc_html_e( 'Enable Geotargeting with Maxmind Integration.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" target="_blank" :href="gdpr_pro_url"><?php esc_html_e( 'Pro Feature.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<span v-show="pro_installed && !pro_activated">
								<?php esc_html_e( 'Activate Pro plugin to enable Geotargeting.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="plugin_page_url"><?php esc_html_e( 'Click here to activate.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<span v-show="pro_installed && pro_activated && !api_key_activated">
								<?php esc_html_e( 'Activate API license key to enable Geotargeting.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="key_activate_url"><?php esc_html_e( 'Click here to activate.', 'gdpr-cookie-consent' ); ?></a>
							</span>
							<span v-show="pro_installed && pro_activated && api_key_activated && !maxmind_integrated">
								<?php esc_html_e( 'Integrate with Maxmind for free.', 'gdpr-cookie-consent' ); ?>
								<a class="gdpr-progress-list-link" :href="maxmind_url"><?php esc_html_e( 'Click here to configure.', 'gdpr-cookie-consent' ); ?></a>
							</span>
						</c-row>
					</c-col>
				</c-row>
			</c-card-body>
		</c-card>
		<!-- cookie insights and cookie summary card  -->
		<?php if ( $is_pro_active ) : ?>
		<div class="gdpr-dashboard-promotional-cards-insights">
			<c-card class="gdpr-dashboard-promotional-card">
				<c-card-header class="gdpr-dashboard-promotional-card-header">
					<span class="gdpr-dashboard-promotional-heading">
						<?php esc_html_e( 'Consent Insights', 'gdpr-cookie-consent' ); ?>
					</span>
				</c-card-header>
				<c-card-body>

				<!-- cookie insights apex pie chart  -->
				<?php if (
					( get_option( 'wpl_cl_decline' ) != 0 && get_option( 'wpl_cl_accept' ) != 0 && get_option( 'wpl_cl_partially_accept' ) != 0 )
					||
					( get_option( 'wpl_cl_decline' ) != 0 || get_option( 'wpl_cl_accept' ) != 0 || get_option( 'wpl_cl_partially_accept' ) != 0 )
				) : ?>
				<div id="chart">
					<apexchart type="pie" width="480" :options="chartOptions" :series="series"></apexchart>
				</div>

				<?php else:  ?>
				<!-- empty pie chart when logs recorded  -->
				<div class="gdpr-dashboard-no-insights">
					<div class="gdpr-dashboard-no-insights-circle">
						No Consent Recorded
					</div>
					<div class="gdpr-dashboard-no-insights-series-labels">
							<div class="gdpr-dashboard-no-insights-series-acc">
								<div  class="gdpr-dashboard-small-circle"></div>
								<div class="gdpr-dashboard-label-text">Accepted</div>
							</div>
							<div class="gdpr-dashboard-no-insights-series-acc">
								<div  class="gdpr-dashboard-small-circle" style="background-color: #F1C7C7;"></div>
								<div class="gdpr-dashboard-label-text">Rejected</div>
							</div>
							<div class="gdpr-dashboard-no-insights-series-acc">
								<div  class="gdpr-dashboard-small-circle" style="background-color: #BDF;"></div>
								<div class="gdpr-dashboard-label-text">Partially Accepted</div>
							</div>
					</div>
				</div>

				<?php endif ?>

				</c-card-body>
			</c-card>
			<c-card class="gdpr-dashboard-promotional-card">
				<c-card-header class="gdpr-dashboard-promotional-card-header">
					<span class="gdpr-dashboard-promotional-heading">
						<?php esc_html_e( 'Cookie Summary', 'gdpr-cookie-consent' ); ?>
					</span>
				</c-card-header>
				<!-- cookie summary body  -->
				<c-card-body class="gdpr-cookie-summary-body">
					<div class="gdpr-cookie-summary">
							<div class="gdpr-cookie-summary-total-cookie-details">
								<img :src="cookie_summary.default" class="gdpr-cookie-summary-icon">
								<div class="gpdr-cookie-summary-total-cookies">
									<div class="gpdr-cookie-no-total-cookies"><?php echo $total_no_of_found_cookies ?></div>
									<!-- <br> -->
									<div class="gpdr-cookie-no-total-text">Total Cookies</div>
								</div>
							</div>

								<div class="gdpr-cookie-summary-total-cookie-details">
									<img :src="cookie_cat.default" class="gdpr-cookie-summary-icon">
									<div class="gpdr-cookie-summary-scan-cat">
										<div class="gpdr-cookie-no-total-cookies"><?php echo $number_of_categories ?></div>
										<div class="gpdr-cookie-no-total-text">Categories</div>
									</div>
								</div>

					</div>

					<div class="gdpr-cookie-summary-details">
							<!-- last scan  -->
							<div class="gdpr-cookie-summary-last-scan">
								<img :src="search_icon.default" class="gdpr-cookie-summary-last-scan-icon">
								<div class="gdpr-cookie-summary-last-title">
									<span class="gdpr-cookie-summary-heading">Last Scan</span>
									<!-- <br> -->
									<div>
										<span class="gdpr-cookie-summary-dynaminc-values">
										<?php
												if (isset($cookie_scan_settings['last_scan']['created_at'])) {
												// esc_attr_e( 'Last successful scan : ', 'gdpr-cookie-consent' );
												echo esc_attr( gmdate( 'F j, Y g:i a T', $cookie_scan_settings['last_scan']['created_at'] ) );
											} else {
												esc_attr_e( 'You haven\'t performed a site scan yet.', 'gdpr-cookie-consent' );
											}
										?>
										</span>
									</div>
								</div>

							</div>
							<!-- Pages scanned  -->
							<div class="gdpr-cookie-summary-last-scan">
									<img :src="page_icon.default" class="gdpr-cookie-summary-last-scan-icon">
									<div class="gdpr-cookie-summary-last-title">
										<span class="gdpr-cookie-summary-heading">Pages Scanned</span>
										<!-- <br> -->
										<div>
										<span class="gdpr-cookie-summary-dynaminc-values"><?php echo $total_scanned_pages ?></span>
										</div>
									</div>

							</div>
							<!-- Next Scan  -->
							<div class="gdpr-cookie-summary-last-scan">
								<img :src="next_scan_icon.default" class="gdpr-cookie-summary-last-scan-icon">
								<div class="gdpr-cookie-summary-last-title">
									<span class="gdpr-cookie-summary-heading">Next Scan</span>
									<!-- <br>
									<br> -->
									<div>
										<span class="gdpr-cookie-summary-dynaminc-values"><?php
										if (isset($the_options['schedule_scan_when'])) {

											echo $the_options['schedule_scan_when'];
										}else{
											echo 'Not Scheduled';
										}

										?></span>
										<a class="gdpr-cookie-summary-schedule" href="<?php echo admin_url( 'admin.php?page=gdpr-cookie-consent-settings#cookie_list' ) ?>">Schedule</a>
									</div>

								</div>

							</div>
							<!-- Manage Cookies  -->
							<div class="gdpr-cookie-summary-manage-cookies">
								<div class="gdpr-cookie-summary-last-title">
									<a class="gdpr-cookie-summary-manage-link" href="<?php echo admin_url( 'admin.php?page=gdpr-cookie-consent-settings#cookie_list' ) ?>">Manage Cookies</a>
								</div>
								<img :src="cookie_icon.default" class="gdpr-cookie-summary-last-scan-icon">


							</div>
					</div>
				</c-card-body>
			</c-card>
		</div>
		<?php endif ?>
		<!-- show consent log and promotional section when pro is activated  -->
		<?php if ( $is_pro_active ) : ?>
		<div class="gdpr-dashboard-promotional-cards">

		<!-- consent log card  -->
			<c-card class="gdpr-dashboard-promotional-card consent-log-card">
				<c-card-header class="gdpr-dashboard-promotional-card-header">
					<span class="gdpr-dashboard-promotional-heading">
						<?php esc_html_e( 'Recent Consent Logs', 'gdpr-cookie-consent' ); ?>
					</span>
				</c-card-header>
				<c-card-body>
					<!-- add consent log table -->
					<?php do_action('gdpr_consent_log_table_dashboard'); ?>
					<div class="gdpr-dashboard-cl-view-all-logs">
						<span><a class="gdpr-dashboard-cl-view-all-logs-text" href="<?php echo admin_url( 'edit.php?post_type=wplconsentlogs' ) ?>">View All Logs</a></span>
						<img :src="view_all_logs.default" class="gdpr-cookie-summary-view-all-logs">
					</div>
				</c-card-body>
			</c-card>
			<!-- tips n tricks card  -->
			<c-card class="gdpr-dashboard-promotional-card tips-n-trick-card">
				<c-card-header class="gdpr-dashboard-promotional-card-header">
					<span class="gdpr-dashboard-promotional-heading">
						<?php esc_html_e( 'Tips and Tricks', 'gdpr-cookie-consent' ); ?>
					</span>
				</c-card-header>
				<c-card-body>
					<c-row class="gdpr-dashboard-faq-row">
						<img :src="arrow_icon.default" class="gdpr-dashboard-faq-icon">
						<a target="blank" :href="faq1_url" class="gdpr-dashboard-faq-link">
							<?php esc_html_e( 'How to activate your License Key?', 'gdpr-cookie-consent' ); ?>
						</a>
					</c-row>
					<c-row class="gdpr-dashboard-faq-row">
						<img :src="arrow_icon.default" class="gdpr-dashboard-faq-icon">
						<a target="blank" :href="faq2_url" class="gdpr-dashboard-faq-link">
							<?php esc_html_e( 'What you need to know about the EU Cookie law?', 'gdpr-cookie-consent' ); ?>
						</a>
					</c-row>
					<c-row class="gdpr-dashboard-faq-row">
						<img :src="arrow_icon.default" class="gdpr-dashboard-faq-icon">
						<a target="blank" :href="faq3_url" class="gdpr-dashboard-faq-link">
							<?php esc_html_e( 'Frequently asked questions', 'gdpr-cookie-consent' ); ?>
						</a>
					</c-row>
					<c-row class="gdpr-dashboard-faq-row">
						<img :src="arrow_icon.default" class="gdpr-dashboard-faq-icon">
						<a target="blank" :href="faq4_url" class="gdpr-dashboard-faq-link">
							<?php esc_html_e( 'What are the CCPA regulations and how we can comply?', 'gdpr-cookie-consent' ); ?>
						</a>
					</c-row>
					<c-row class="gdpr-dashboard-faq-row">
						<img :src="arrow_icon.default" class="gdpr-dashboard-faq-icon">
						<a target="blank" :href="faq5_url" class="gdpr-dashboard-faq-link">
							<?php esc_html_e( 'All you need to know about IAB', 'gdpr-cookie-consent' ); ?>
						</a>
					</c-row>
				</c-card-body>
			</c-card>
		<!-- other plugin card  -->
			<c-card class="gdpr-dashboard-promotional-card other-plugins-card">
				<c-card-header class="gdpr-dashboard-promotional-card-header">
					<span class="gdpr-dashboard-promotional-heading">
						<?php esc_html_e( 'Other Plugins', 'gdpr-cookie-consent' ); ?>
					</span>
				</c-card-header>
				<c-card-body class="gdpr-dashboard-promotional-card-body">
					<div>
						<c-row class="gdpr-dashboard-plugins-row">
							<span>
								<img :src="legalpages_icon.default" class="gdpr-dashboard-plugins-icon">
								<?php esc_html_e( 'WP LegalPages', 'gdpr-cookie-consent' ); ?>
							</span>
							<a target="blank" :href="legalpages_url" class="gdpr-dashboard-plugins-link">
								<?php esc_html_e( 'Install', 'gdpr-cookie-consent' ); ?>
							</a>
						</c-row>
						<c-row class="gdpr-dashboard-plugins-row">
							<span>
								<img :src="adcenter_icon.default" class="gdpr-dashboard-plugins-icon">
								<?php esc_html_e( 'WP Adcenter', 'gdpr-cookie-consent' ); ?>
							</span>
							<a target="blank" :href="adcenter_url" class="gdpr-dashboard-plugins-link">
								<?php esc_html_e( 'Install', 'gdpr-cookie-consent' ); ?>
							</a>
						</c-row>
						<c-row class="gdpr-dashboard-plugins-row">
							<span>
								<img :src="survey_funnel_icon.default" class="gdpr-dashboard-plugins-icon">
								<?php esc_html_e( 'Survey Funnel', 'gdpr-cookie-consent' ); ?>
							</span>
							<a target="blank" :href="survey_funnel_url" class="gdpr-dashboard-plugins-link">
								<?php esc_html_e( 'Install', 'gdpr-cookie-consent' ); ?>
							</a>
						</c-row>
					</div>
					<c-row class="gdpr-dashboard-all-plugins-row">
						<a target="blank" :href="all_plugins_url" class="gdpr-dashboard-plugins-link">
							<c-button class="gdpr-progress-view-plugins-link" color="info" variant="outline">
							<?php esc_html_e( 'View all Plugins', 'gdpr-cookie-consent' ); ?>
							</c-button>
						</a>
					</c-row>
				</c-card-body>
			</c-card>
		</div>
		<?php endif ?>
		<c-card class="gdpr-dashboard-quick-links-card">
			<c-card-header class="gdpr-dashboard-quick-links-card-header">
				<span class="gdpr-dashboard-quick-links-heading">
					<?php esc_html_e( 'Quick Links', 'gdpr-cookie-consent' ); ?>
				</span>
				<span>
					<a v-show="!pro_installed" class="gdpr-progress-list-link" :href="gdpr_pro_url" target="_blank">
						<c-button class="gdpr-upgrade-pro-button" color="info" :variant="highlight_variant">
							<?php esc_html_e( 'Upgrade to Pro', 'gdpr-cookie-consent' ); ?>
						</c-button>
					</a>
					<a v-show="pro_installed && !pro_activated" class="gdpr-progress-list-link" :href="plugin_page_url" target="_blank">
						<c-button class="gdpr-upgrade-pro-button" color="info" :variant="highlight_variant">
							<?php esc_html_e( 'Activate Pro', 'gdpr-cookie-consent' ); ?>
						</c-button>
					</a>
					<a v-show="pro_installed && pro_activated && !api_key_activated " class="gdpr-progress-list-link" :href="key_activate_url" target="_blank">
						<c-button class="gdpr-upgrade-pro-button" color="info" :variant="highlight_variant">
							<?php esc_html_e( 'Activate License Key', 'gdpr-cookie-consent' ); ?>
						</c-button>
					</a>
				</span>
			</c-card-header>
			<c-card-body class="gdpr-dashboard-quick-links-body">
				<c-row v-show="pro_installed && pro_activated && api_key_activated" class="gdpr-quick-links-images-row">
					<span class="gdpr-quick-link-item">
						<a class="gdpr-quick-link" :href="show_cookie_url">
							<img class="gdpr-quick-link-image" :src="settings_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
						<?php esc_html_e( 'Settings', 'gdpr-cookie-consent' ); ?>
						</span>
					</span>
					<span class="gdpr-quick-link-item">
						<a class="gdpr-quick-link" :href="consent_log_url">
							<img class="gdpr-quick-link-image" :src="consent_log_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
						<?php esc_html_e( 'Consent Log', 'gdpr-cookie-consent' ); ?>
						</span>
					</span>
					<span class="gdpr-quick-link-item">
						<a class="gdpr-quick-link" :href="cookie_scan_url">
							<img class="gdpr-quick-link-image" :src="cookie_scan_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
						<?php esc_html_e( 'Scan Cookies', 'gdpr-cookie-consent' ); ?>
						</span>
					</span>
					<span class="gdpr-quick-link-item">
						<a class="gdpr-quick-link" :href="maxmind_url">
							<img class="gdpr-quick-link-image" :src="geolocation_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
						<?php esc_html_e( 'Geotargeting', 'gdpr-cookie-consent' ); ?>
						</span>
					</span>
					<span class="gdpr-quick-link-item">
						<a class="gdpr-quick-link" :href="cookie_design_url">
							<img class="gdpr-quick-link-image" :src="cookie_design_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
						<?php esc_html_e( 'Design Cookie Banner', 'gdpr-cookie-consent' ); ?>
						</span>
					</span>
					<span class="gdpr-quick-link-item">
						<a class="gdpr-quick-link" :href="cookie_template_url">
							<img class="gdpr-quick-link-image" :src="cookie_template_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
						<?php esc_html_e( 'Banner Templates', 'gdpr-cookie-consent' ); ?>
						</span>
					</span>
					<span class="gdpr-quick-link-item">
						<a class="gdpr-quick-link" :href="script_blocker_url">
							<img class="gdpr-quick-link-image" :src="script_blocker_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
						<?php esc_html_e( 'Script Blocker', 'gdpr-cookie-consent' ); ?>
						</span>
					</span>
					<span class="gdpr-quick-link-item">
						<a class="gdpr-quick-link" :href="third_party_url">
							<img class="gdpr-quick-link-image" :src="cookie_table_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
						<?php esc_html_e( 'Third Party Details', 'gdpr-cookie-consent' ); ?>
						</span>
					</span>
				</c-row>
				<c-row v-show="!pro_installed || !pro_activated || !api_key_activated" class="gdpr-quick-links-images-row">
					<span class="gdpr-quick-link-item">
						<a class="gdpr-quick-link" :href="show_cookie_url">
							<img class="gdpr-quick-link-image" :src="settings_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
						<?php esc_html_e( 'Settings', 'gdpr-cookie-consent' ); ?>
						</span>
					</span>
					<span class="gdpr-quick-link-item">
						<a class="gdpr-quick-link" :href="cookie_design_url">
							<img class="gdpr-quick-link-image" :src="cookie_design_image.default">
						</a>
						<span class="gdpr-quick-link-caption">
						<?php esc_html_e( 'Design Cookie Banner', 'gdpr-cookie-consent' ); ?>
						</span>
					</span>
					<span class="gdpr-quick-link-item">
						<c-button class="gdpr-dashboard-disabled-icon" @mouseover="highlight_variant=''" @mouseleave="highlight_variant='outline'">
							<img class="gdpr-quick-link-image" :src="script_blocker_image_disabled.default">
						</c-button>
						<span class="gdpr-quick-link-caption">
						<?php esc_html_e( 'Script Blocker', 'gdpr-cookie-consent' ); ?>
						</span>
					</span>
					<span class="gdpr-quick-link-item">
						<c-button class="gdpr-dashboard-disabled-icon" @mouseover="highlight_variant=''" @mouseleave="highlight_variant='outline'">
							<img class="gdpr-quick-link-image" :src="consent_log_image_disabled.default">
						</c-button>
						<span class="gdpr-quick-link-caption">
						<?php esc_html_e( 'Consent Log', 'gdpr-cookie-consent' ); ?>
						</span>
					</span>
					<span class="gdpr-quick-link-item">
						<c-button class="gdpr-dashboard-disabled-icon" @mouseover="highlight_variant=''" @mouseleave="highlight_variant='outline'">
							<img class="gdpr-quick-link-image" :src="cookie_scan_image_disabled.default">
						</c-button>
						<span class="gdpr-quick-link-caption">
						<?php esc_html_e( 'Scan Cookies', 'gdpr-cookie-consent' ); ?>
						</span>
					</span>
					<span class="gdpr-quick-link-item">
						<c-button class="gdpr-dashboard-disabled-icon" @mouseover="highlight_variant=''" @mouseleave="highlight_variant='outline'">
							<img class="gdpr-quick-link-image" :src="geolocation_image_disabled.default">
						</c-button>
						<span class="gdpr-quick-link-caption">
						<?php esc_html_e( 'Geotargeting', 'gdpr-cookie-consent' ); ?>
						</span>
					</span>
					<span class="gdpr-quick-link-item">
						<c-button class="gdpr-dashboard-disabled-icon" @mouseover="highlight_variant=''" @mouseleave="highlight_variant='outline'">
							<img class="gdpr-quick-link-image" :src="cookie_template_image_disabled.default">
						</c-button>
						<span class="gdpr-quick-link-caption">
						<?php esc_html_e( 'Banner Templates', 'gdpr-cookie-consent' ); ?>
						</span>
					</span>
					<span class="gdpr-quick-link-item">
						<c-button class="gdpr-dashboard-disabled-icon" @mouseover="highlight_variant=''" @mouseleave="highlight_variant='outline'">
							<img class="gdpr-quick-link-image" :src="cookie_table_image_disabled.default">
						</c-button>
						<span class="gdpr-quick-link-caption">
						<?php esc_html_e( 'Third Party Details', 'gdpr-cookie-consent' ); ?>
						</span>
					</span>
				</c-row>
			</c-card-body>
		</c-card>
		<?php if ( !$is_pro_active ) : ?>
		<div class="gdpr-dashboard-promotional-cards">
			<c-card class="gdpr-dashboard-promotional-card">
				<c-card-header class="gdpr-dashboard-promotional-card-header">
					<span class="gdpr-dashboard-promotional-heading">
						<?php esc_html_e( 'Tips and Tricks', 'gdpr-cookie-consent' ); ?>
					</span>
				</c-card-header>
				<c-card-body>
					<c-row class="gdpr-dashboard-faq-row">
						<img :src="arrow_icon.default" class="gdpr-dashboard-faq-icon">
						<a target="blank" :href="faq1_url" class="gdpr-dashboard-faq-link">
							<?php esc_html_e( 'How to activate your License Key?', 'gdpr-cookie-consent' ); ?>
						</a>
					</c-row>
					<c-row class="gdpr-dashboard-faq-row">
						<img :src="arrow_icon.default" class="gdpr-dashboard-faq-icon">
						<a target="blank" :href="faq2_url" class="gdpr-dashboard-faq-link">
							<?php esc_html_e( 'What you need to know about the EU Cookie law?', 'gdpr-cookie-consent' ); ?>
						</a>
					</c-row>
					<c-row class="gdpr-dashboard-faq-row">
						<img :src="arrow_icon.default" class="gdpr-dashboard-faq-icon">
						<a target="blank" :href="faq3_url" class="gdpr-dashboard-faq-link">
							<?php esc_html_e( 'Frequently asked questions', 'gdpr-cookie-consent' ); ?>
						</a>
					</c-row>
					<c-row class="gdpr-dashboard-faq-row">
						<img :src="arrow_icon.default" class="gdpr-dashboard-faq-icon">
						<a target="blank" :href="faq4_url" class="gdpr-dashboard-faq-link">
							<?php esc_html_e( 'What are the CCPA regulations and how we can comply?', 'gdpr-cookie-consent' ); ?>
						</a>
					</c-row>
					<c-row class="gdpr-dashboard-faq-row">
						<img :src="arrow_icon.default" class="gdpr-dashboard-faq-icon">
						<a target="blank" :href="faq5_url" class="gdpr-dashboard-faq-link">
							<?php esc_html_e( 'All you need to know about IAB', 'gdpr-cookie-consent' ); ?>
						</a>
					</c-row>
				</c-card-body>
			</c-card>
			<c-card class="gdpr-dashboard-promotional-card">
				<c-card-header class="gdpr-dashboard-promotional-card-header">
					<span class="gdpr-dashboard-promotional-heading">
						<?php esc_html_e( 'Other Plugins', 'gdpr-cookie-consent' ); ?>
					</span>
				</c-card-header>
				<c-card-body class="gdpr-dashboard-promotional-card-body">
					<div>
						<c-row class="gdpr-dashboard-plugins-row">
							<span>
								<img :src="legalpages_icon.default" class="gdpr-dashboard-plugins-icon">
								<?php esc_html_e( 'WP LegalPages', 'gdpr-cookie-consent' ); ?>
							</span>
							<a target="blank" :href="legalpages_url" class="gdpr-dashboard-plugins-link">
								<?php esc_html_e( 'Install', 'gdpr-cookie-consent' ); ?>
							</a>
						</c-row>
						<c-row class="gdpr-dashboard-plugins-row">
							<span>
								<img :src="adcenter_icon.default" class="gdpr-dashboard-plugins-icon">
								<?php esc_html_e( 'WP Adcenter', 'gdpr-cookie-consent' ); ?>
							</span>
							<a target="blank" :href="adcenter_url" class="gdpr-dashboard-plugins-link">
								<?php esc_html_e( 'Install', 'gdpr-cookie-consent' ); ?>
							</a>
						</c-row>
						<c-row class="gdpr-dashboard-plugins-row">
							<span>
								<img :src="survey_funnel_icon.default" class="gdpr-dashboard-plugins-icon">
								<?php esc_html_e( 'Survey Funnel', 'gdpr-cookie-consent' ); ?>
							</span>
							<a target="blank" :href="survey_funnel_url" class="gdpr-dashboard-plugins-link">
								<?php esc_html_e( 'Install', 'gdpr-cookie-consent' ); ?>
							</a>
						</c-row>
					</div>
					<c-row class="gdpr-dashboard-all-plugins-row">
						<a target="blank" :href="all_plugins_url" class="gdpr-dashboard-plugins-link">
							<c-button class="gdpr-progress-view-plugins-link" color="info" variant="outline">
							<?php esc_html_e( 'View all Plugins', 'gdpr-cookie-consent' ); ?>
							</c-button>
						</a>
					</c-row>
				</c-card-body>
			</c-card>
		</div>
		<?php endif ?>
	</c-container>
</div>
