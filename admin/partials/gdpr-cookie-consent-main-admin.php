<?php

/**
 * Provide a admin area view for the WP Cookie Consent plugin
 *
 * This file is used to markup the admin-facing aspects of the WP Cookie Consent plugin.
 *
 * @link       https://club.wpeka.com/
 * @since      2.4.1
 *
 * @package gdpr-cookie-consent
 */
// check if pro is activated or installed.
$pro_is_activated  = get_option( 'wpl_pro_active', false );
$the_options       = Gdpr_Cookie_Consent::gdpr_get_settings();
$is_data_req_on    = isset( $the_options['data_reqs_on'] ) ? $the_options['data_reqs_on'] : null;
$is_consent_log_on = isset( $the_options['logging_on'] ) ? $the_options['logging_on'] : null;
$installed_plugins = get_plugins();
$pro_installed     = isset( $installed_plugins['wpl-cookie-consent/wpl-cookie-consent.php'] ) ? true : false;
$plugin_name                   = 'wplegalpages/wplegalpages.php';
$is_legalpages_active = is_plugin_active( $plugin_name );
// Require the class file for gdpr cookie consent api framework settings.
require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';

// Instantiate a new object of the GDPR_Cookie_Consent_Settings class.
$this->settings = new GDPR_Cookie_Consent_Settings();

// Call the is_connected() method from the instantiated object to check if the user is connected.
$is_user_connected = $this->settings->is_connected();
$api_user_plan     = $this->settings->get_plan();
/*
* Number of scans on the basis of user's plan
*/
if ( $api_user_plan == 'free' ) {
	$total_no_of_free_scans = 100;
} else {
	$total_no_of_free_scans = 20000; // actual 50000.
}

$gdpr_no_of_page_scan            = $total_no_of_free_scans - get_option( 'gdpr_no_of_page_scan' );
$remaining_percentage_scan_limit = round( ( get_option( 'gdpr_no_of_page_scan' ) / $total_no_of_free_scans ) * 100 );

?>

<div id="gdpr-cookie-consent-main-admin-structure" class="gdpr-cookie-consent-main-admin-structure">
	<div id="gdpr-cookie-consent-main-admin-header" class="gdpr-cookie-consent-main-admin-header">
		<!-- Main top banner  -->
		<div class="gdpr-cookie-consent-admin-fixed-banner">
				<div class="gdpr-cookie-consent-admin-logo-and-label">
					<div class="gdpr-cookie-consent-admin-logo">
						<!-- //image  -->
						<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/WPLPCompliancePlatformWhite.png'; ?>" alt="WP Cookie Consent Logo">
					</div>
				</div>
				<div class="gdpr-cookie-consent-admin-help-and-support">
				<div class="gdpr-cookie-consent-admin-product-tour">
						<div class="gdpr-cookie-consent-admin-product-tour-icon">
							<!-- //image  -->
							<a href="https://club.wpeka.com/docs/wp-cookie-consent/" target="_blank">
								<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/product_tour.svg'; ?>" alt="WP Cookie Consent Help">
							</a>
						</div>
						<div class="gdpr-cookie-consent-admin-product-tour-text"><a href="#" id="start-plugin-tour" target="_blank">
						Start Plugin Tour</a>
						</div>
					</div>
				<div class="gdpr-cookie-consent-admin-help">
						<div class="gdpr-cookie-consent-admin-help-icon">
							<!-- //image  -->
							<a href="https://club.wpeka.com/docs/wp-cookie-consent/" target="_blank">
								<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/wp_cookie_help.svg'; ?>" alt="WP Cookie Consent Help">
							</a>
						</div>
						<div class="gdpr-cookie-consent-admin-help-text"><a href="https://wplegalpages.com/docs/wp-cookie-consent/" target="_blank">
							Help Guide</a>
						</div>
					</div>
					<div class="gdpr-cookie-consent-admin-support">
						<!-- //support  -->
						<div class="gdpr-cookie-consent-admin-support-icon">
							<!-- //image  -->
							<a href="https://wplegalpages.com/contact-us/" target="_blank">
							<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/wp_cookie_support.svg'; ?>" alt="WP Cookie Consent Support">
							</a>
						</div>
						<div class="gdpr-cookie-consent-admin-support-text"><a href="https://wplegalpages.com/contact-us/" target="_blank">
							Support</a>
						</div>
					</div>
				</div>
		</div>

		<!-- scans -->
		<?php
		// if user is connected to the app.wplegalpages then show remaining scans
		if ( $is_user_connected == true && !$pro_installed ) {
			?>
			<div class="gdpr-remaining-scans-content" >
				<div class="gdpr-remaining-scans-container">
					<span class="gdpr-remaining-scans-title">Remaining Scans: </span><span><?php echo $gdpr_no_of_page_scan; ?> / <?php echo $total_no_of_free_scans; ?><span><span> (<?php echo ceil( $remaining_percentage_scan_limit ); ?>%)</span>
				</div>
				<div class="gdpr-current-plan-container">
					<p><span>Current Plan: </span><?php echo $api_user_plan; ?></p>
					<?php
					if ( $api_user_plan == 'free' ) {
						?>
					<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/gdpr_upgrade_btn.png'; ?>" class="gdpr-cookie-consent-admin-upgrade-button" alt="<?php echo esc_attr( 'Upgrade Button', 'gdpr-cookie-consent' ); ?>">
						<?php
					}
					?>
				</div>
				</div>
			<?php if ( get_transient( 'app_wplp_subscription_payment_status_failed' ) ) { ?>
			<div class="gdpr-subsription-payment-failed-notice" >
				<p><span class="dashicons dashicons-warning"></span> <?php esc_html_e( 'Your last payment attempt failed. Please update your payment details within 7 days to avoid service disruption.', 'gdpr-cookie-consent' ); ?></p>
			</div>
			<?php
			}
		}
		?>
		<!-- Upgrade to pro banner  -->
		<?php
		if ( $is_user_connected == true && ! $pro_installed && $api_user_plan == 'free' ) {
			?>
			<div class="cookie-consent-upgrade-to-pro-banner-container">
				<img class="cookie-consent-upgrade-to-pro-banner" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/upgrade-to-pro-banner-cookieconsent.jpg'; ?>" alt="WP Cookie Consent Help">
			</div>
		<?php }?>

		<!-- connect your website to WP Cookie Consent  -->

		<?php
		if ( $is_user_connected != true && ! $pro_installed ) {
			?>
		<div class="gdpr-cookie-consent-connect-api-container">
			<div class="gdpr-api-info-content">
				<div class="gdpr-api-detailed-info-wrapper">
					<div class="gdpr-api-detailed-info">
						<h2>
							<?php echo esc_html( 'Sign Up for Free to Access Core Features', 'gdpr-cookie-consent' ); ?>
						</h2>
						<p><?php echo esc_html( 'Get started with essential tools to manage cookies and legal policies:', 'gdpr-cookie-consent' ); ?></p>
						<p>
							<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.svg'; ?>" alt="<?php echo esc_attr( 'API Connection Success Mark', 'gdpr-cookie-consent' ); ?>"></span> <strong><?php echo esc_html( 'Cookie Insights:', 'gdpr-cookie-consent' ); ?></strong> <?php echo esc_html( 'Detailed reports on cookies detected on your site.', 'gdpr-cookie-consent' ); ?>
						</p>
						<p>
							<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.svg'; ?>" alt="<?php echo esc_attr( 'API Connection Success Mark', 'gdpr-cookie-consent' ); ?>"></span> <strong><?php echo esc_html( 'Cookie Scanner:', 'gdpr-cookie-consent' ); ?></strong> <?php echo esc_html( 'Automatically scan your website for cookies.', 'gdpr-cookie-consent' ); ?>
						</p>
						<p>
							<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.svg'; ?>" alt="<?php echo esc_attr( 'API Connection Success Mark', 'gdpr-cookie-consent' ); ?>"></span> <strong><?php echo esc_html( 'A/B Testing:', 'gdpr-cookie-consent' ); ?></strong> <?php echo esc_html( 'Compare two cookie banners to find the best performer.', 'gdpr-cookie-consent' ); ?>
						</p>
						<p>
							<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.svg'; ?>" alt="<?php echo esc_attr( 'API Connection Success Mark', 'gdpr-cookie-consent' ); ?>"></span> <strong><?php echo esc_html( 'Consent Log:', 'gdpr-cookie-consent' ); ?></strong> <?php echo esc_html( 'Track and store user consent records.', 'gdpr-cookie-consent' ); ?>
						</p>
						<p>
							<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.svg'; ?>" alt="<?php echo esc_attr( 'API Connection Success Mark', 'gdpr-cookie-consent' ); ?>"></span> <strong><?php echo esc_html( 'Data Subject Access Request:', 'gdpr-cookie-consent' ); ?></strong> <?php echo esc_html( 'Simplify user data requests.', 'gdpr-cookie-consent' ); ?>
						</p>
						<p>
							<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.svg'; ?>" alt="<?php echo esc_attr( 'API Connection Success Mark', 'gdpr-cookie-consent' ); ?>"></span> <strong><?php echo esc_html( 'Essential Legal Policies:', 'gdpr-cookie-consent' ); ?></strong> <?php echo esc_html( 'Generate key policies like Privacy Policy, Terms of Use, and more.', 'gdpr-cookie-consent' ); ?>
						</p>
					</div>
				<div class="gdpr-api-connection-btns">
					<button class="gdpr-start-auth"><?php echo esc_html( 'Sign Up for Free', 'gdpr-cookie-consent' ); ?></button>
					<p><?php echo esc_html( 'Already have an account?', 'gdpr-cookie-consent' ); ?><a class="gdpr-start-auth" href=""><?php esc_html_e( 'Connect your existing account', 'gdpr-cookie-consent' ); ?></a></p>
				</div>
			</div>
			
			<div class="gdpr-api-detailed-info-wrapper">
					<div class="gdpr-api-detailed-info">
						<h2>
							<?php echo esc_html( 'Upgrade to Pro for Advanced Features', 'gdpr-cookie-consent' ); ?>
						</h2>
						<p><?php echo esc_html( 'Take your website compliance to the next level with Pro:', 'gdpr-cookie-consent' ); ?></p>
						<p>
							<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.svg'; ?>" alt="<?php echo esc_attr( 'API Connection Success Mark', 'gdpr-cookie-consent' ); ?>"></span> <strong><?php echo esc_html( 'Advanced Dashboard:', 'gdpr-cookie-consent' ); ?></strong> <?php echo esc_html( 'Gain detailed insights into cookie consent performance.', 'gdpr-cookie-consent' ); ?>
						</p>
						<p>
							<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.svg'; ?>" alt="<?php echo esc_attr( 'API Connection Success Mark', 'gdpr-cookie-consent' ); ?>"></span> <strong><?php echo esc_html( 'Geo-targeting:', 'gdpr-cookie-consent' ); ?></strong> <?php echo esc_html( 'Show banners tailored to visitor locations.', 'gdpr-cookie-consent' ); ?>
						</p>
						<p>
							<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.svg'; ?>" alt="<?php echo esc_attr( 'API Connection Success Mark', 'gdpr-cookie-consent' ); ?>"></span> <strong><?php echo esc_html( 'IAB TCF 2.2 Support:', 'gdpr-cookie-consent' ); ?></strong> <?php echo esc_html( 'Comply with the latest transparency framework.', 'gdpr-cookie-consent' ); ?>
						</p>
						<p>
							<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.svg'; ?>" alt="<?php echo esc_attr( 'API Connection Success Mark', 'gdpr-cookie-consent' ); ?>"></span> <strong><?php echo esc_html( 'Google Consent Mode:', 'gdpr-cookie-consent' ); ?></strong> <?php echo esc_html( 'Manage Google tags based on user consent.', 'gdpr-cookie-consent' ); ?>
						</p>
						<p>
							<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.svg'; ?>" alt="<?php echo esc_attr( 'API Connection Success Mark', 'gdpr-cookie-consent' ); ?>"></span> <strong><?php echo esc_html( '25+ Legal Templates:', 'gdpr-cookie-consent' ); ?></strong> <?php echo esc_html( 'Access a library of customizable templates.', 'gdpr-cookie-consent' ); ?>
						</p>
						<p>
							<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.svg'; ?>" alt="<?php echo esc_attr( 'API Connection Success Mark', 'gdpr-cookie-consent' ); ?>"></span> <strong><?php echo esc_html( '20,000 Pages per Scan:', 'gdpr-cookie-consent' ); ?></strong> <?php echo esc_html( 'Ensure comprehensive website cookie scanning.', 'gdpr-cookie-consent' ); ?>
						</p>
					</div>
					<div class="gdpr-api-connection-btns">
						<button class="gdpr-cookie-consent-admin-upgrade-button upgrade-button">Upgrade to Pro</button>
					</div>
				</div>
			</div>
			<div id="popup-site-excausted" class="popup-overlay">
				<div class="popup-content">
					<div class="popup-header">
						<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Right Corner Image" class="popup-image">
					</div>
						<div class="excausted-popup-body">
							<h2>Attention! Usage Limit Reached</h2>
							<p>You've reached your license limit. Please upgrade to continue using the plugin on this site.</p>
							<button class="gdpr-cookie-consent-admin-upgrade-button upgrade-button">Upgrade Plan</button>
							<p>Need to activate on a new site? Manage your licenses in <a href="https://app.wplegalpages.com/signup/api-keys/" target="_blank">My Account.</a></p>
						</div>
				</div>
			</div>
		</div>

			<?php

		}
		?>

		<!-- tabs -->
		<div class="gdpr-cookie-consent-admin-tabs-section">
		<div class="gdpr-cookie-consent-admin-tabs dashboard-tabs">
				<!-- Dashboard tab  -->
				<?php  
					 if ($is_legalpages_active) {
						$plugin_slug = 'wplegalpages/wplegalpages.php';
						// Fetch the plugin data
						$plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_slug);
			
						// Get the version
						$legalpages_version = $plugin_data['Version'];
						if($legalpages_version >= '3.3.0') {
					?>
				 <a href="?page=wplp-dashboard" class="gdpr-admin-tab-link wplp-main-tab gdpr-cookie-consent-admin-dashboard-tab">
					<div class="wp-legalpages-admin-gdpr-main-tab">
						<?php echo esc_html('Dashboard','gdpr-cookie-consent'); ?>
					</div>
				</a>
				<?php } 
				
					}
					else{
						?>
					<a href="?page=wplp-dashboard" class="gdpr-admin-tab-link wplp-main-tab gdpr-cookie-consent-admin-dashboard-tab">
							<div class="wp-legalpages-admin-gdpr-main-tab">
								<?php echo esc_html('Dashboard','gdpr-cookie-consent'); ?>
							</div>
						</a>
					<?php
					} 
				?>
				<!-- Legal Pages Plugin tab  -->
				<a href="?page=legal-pages" class="gdpr-admin-tab-link wplp-main-tab">
					<div class="wp-legalpages-admin-gdpr-main-tab">
						<?php echo esc_html('Legal Pages','gdpr-cookie-consent'); ?>
					</div>
				</a>
				<!-- Cookie Consent Plugin tab  -->
				<a href="?page=gdpr-cookie-consent" class="gdpr-admin-tab-link gdpr-cookie-consent-tab">
						<?php echo esc_html('Cookie Consent','gdpr-cookie-consent'); ?>
				</a>
				<!-- Help tab  -->
				<?php  if ($is_legalpages_active) {
						$plugin_slug = 'wplegalpages/wplegalpages.php';
						// Fetch the plugin data
						$plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_slug);
			
						// Get the version
						$legalpages_version = $plugin_data['Version'];
						if($legalpages_version >= '3.3.0') { ?>
				<a href="?page=wplp-dashboard#help-page" class="gdpr-admin-tab-link gdpr-cookie-consent-admin-help-tab">
					<?php echo esc_html('Help','gdpr-cookie-consent'); ?>
				</a>	
				<?php } }
				else{
					?> 
				<a href="?page=wplp-dashboard#help-page" class="gdpr-admin-tab-link gdpr-cookie-consent-admin-help-tab">
					<?php echo esc_html('Help','gdpr-cookie-consent'); ?>
				</a>	
					<?php } ?>			
			</div>
			<div class="gdpr-cookie-consent-admin-tabs gdpr-sub-tabs">
				
				<!-- Create Banner tab  -->
				<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-cookie-banner-tab" data-tab="create_cookie_banner">
				<?php echo esc_html('Create&nbsp;Cookie&nbsp;Banner','gdpr-cookie-consent'); ?>
				</div>
				<!-- Cookie Settings tab  -->
				<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-cookie-settings-tab" data-tab="cookie_settings">
					<?php echo esc_html('Cookie&nbsp;Settings','gdpr-cookie-consent'); ?>
				</div>
				<?php
				if ( $is_consent_log_on && ! $pro_is_activated ) {
					?>
						<!-- consent log tab  -->
							<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-consent-logs-tab" data-tab="consent_logs">
								<?php echo esc_html('Consent&nbsp;Logs','gdpr-cookie-consent'); ?>
							</div>
					<?php
				}
				if ( $pro_is_activated ) {
					if ( $is_consent_log_on ) {
						?>
									<!-- consent log tab  -->
									<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-consent-logs-tab" data-tab="consent_logs">
										<?php echo esc_html('Consent&nbsp;Logs','gdpr-cookie-consent'); ?>
									</div>
								<?php
					}
				}
				if ( $is_data_req_on && ! $pro_is_activated ) {

					?>
								<!-- data req tab  -->
								<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-data-request-tab" data-tab="data_request">
								<?php echo esc_html('Data&nbsp;Request','gdpr-cookie-consent'); ?>
								</div>

								<?php

				}

				if ( $pro_is_activated ) {

					?>

							<?php

							if ( $is_data_req_on ) {

								?>
							<!-- data req tab  -->
							<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-data-request-tab" data-tab="data_request">
								<?php echo esc_html('Data&nbsp;Request','gdpr-cookie-consent'); ?>
								</div>
								<?php

							}
				}

				?>

				<!-- Policy data tab  -->
				<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-policy-data-tab" data-tab="policy_data">
				<?php echo esc_html('Policy&nbsp;Data','gdpr-cookie-consent'); ?>
				</div>
				<?php
				if ( $pro_is_activated ) {
					?>
					<!-- Pro activation key -->
						<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-pro-activation-tab" data-tab="activation_key">
							<p class="gdpr-cookie-consent-admin-tab-name"><?php echo esc_html('Pro Activation','gdpr-cookie-consent'); ?></p>
						</div>
					<?php
				}

				?>
			</div>
		</div>

		<!-- tab content  -->

		<div class="gdpr-cookie-consent-admin-tabs-content">
			<div class="gdpr-cookie-consent-admin-tabs-inner-content">
				
				<!-- create cookie content  -->
				<div class="gdpr-cookie-consent-admin-create-cookie-content gdpr-cookie-consent-admin-tab-content" id="create_cookie_banner">
					<?php require_once plugin_dir_path( __FILE__ ) . 'gdpr-create-cookie-banner-tab-template.php'; ?>
				</div>
				<!-- cookie settings content -->
				<div class="gdpr-cookie-consent-admin-cookie-settings-content gdpr-cookie-consent-admin-tab-content" id="cookie_settings">
					<?php require_once plugin_dir_path( __FILE__ ) . 'gdpr-cookie-settings-tab-template.php'; ?>
				</div>
				<!-- policy data content  -->
				<div class="gdpr-cookie-consent-admin-policy-data-content gdpr-cookie-consent-admin-tab-content" id="policy_data">
					<?php do_action( 'add_policy_data_content' ); ?>
					<?php require_once plugin_dir_path( __FILE__ ) . 'gdpr-policy-data-tab-template.php'; ?>
				</div>
				<!-- consent log data content  -->
				<div class="gdpr-cookie-consent-admin-consent-logs-data-content gdpr-cookie-consent-admin-tab-content" id="consent_logs">
				<?php do_action( 'add_consent_log_content' ); ?>
				<?php require_once plugin_dir_path( __FILE__ ) . 'gdpr-consent-logs-tab-template.php'; ?>
				</div>
				<!-- data req data content  -->
				<div class="gdpr-cookie-consent-admin-data-request-data-content gdpr-cookie-consent-admin-tab-content" id="data_request">
					<?php do_action( 'add_data_request_content' ); ?>
					<?php require_once plugin_dir_path( __FILE__ ) . 'gdpr-data-request-tab-template.php'; ?>
				</div>
				<!-- activation key content  -->
				<div class="gdpr-cookie-consent-admin-data-request-activation-key gdpr-cookie-consent-admin-tab-content" id="activation_key">
					<?php do_action( 'add_activation_key_content' ); ?>
					<?php require_once plugin_dir_path( __FILE__ ) . 'gdpr-cookies-activation-key.php'; ?>
				</div>
				<!-- Help page content -->
				<div class="gdpr-cookie-consent-admin-data-request-activation-key gdpr-cookie-consent-admin-tab-content" id="help-page">
					<?php require_once plugin_dir_path( __FILE__ ) . 'gdpr-cookie-consent-help-page-template.php'; ?>
				</div>
			</div>
		</div>


	</div>
</div>
