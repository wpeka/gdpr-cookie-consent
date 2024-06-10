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
$pro_is_activated = get_option( 'wpl_pro_active', false );
$the_options      = Gdpr_Cookie_Consent::gdpr_get_settings();
$is_data_req_on   = isset( $the_options['data_reqs_on'] ) ? $the_options['data_reqs_on'] : null;
$is_consent_log_on =isset(  $the_options['logging_on'] ) ? $the_options['logging_on'] : null;
$installed_plugins = get_plugins();
$pro_installed     = isset( $installed_plugins['wpl-cookie-consent/wpl-cookie-consent.php'] ) ? true : false;
// Require the class file for gdpr cookie consent api framework settings.
require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';

// Instantiate a new object of the GDPR_Cookie_Consent_Settings class.
$this->settings = new GDPR_Cookie_Consent_Settings();

// Call the is_connected() method from the instantiated object to check if the user is connected.
$is_user_connected = $this->settings->is_connected();
$api_user_plan     = $this->settings->get_plan();
// $api_user_plan = 'pro';

/*
* Number of scans on the basis of user's plan
*/
if ( $api_user_plan == 'free' ) {
	$total_no_of_free_scans = 15;
}else{
	$total_no_of_free_scans = 25;
}

$gdpr_no_of_page_scan = $total_no_of_free_scans - get_option('gdpr_no_of_page_scan');
$remaining_percentage_scan_limit = ( get_option('gdpr_no_of_page_scan') / $total_no_of_free_scans )*100;

?>

<div id="gdpr-cookie-consent-main-admin-structure" class="gdpr-cookie-consent-main-admin-structure">
	<div id="gdpr-cookie-consent-main-admin-header" class="gdpr-cookie-consent-main-admin-header">
		<!-- Main top banner  -->
		<div class="gdpr-cookie-consent-admin-fixed-banner">
				<div class="gdpr-cookie-consent-admin-logo-and-label">
					<div class="gdpr-cookie-consent-admin-logo">
						<!-- //image  -->
						<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/wp-cookie-consent-logo.png'; ?>" alt="WP Cookie Consent Logo">
					</div>
					<div class="gdpr-cookie-consent-admin-label">
						<!-- //label  -->
						<div class="gdpr-cookie-consent-admin-label_wp_label"><span>WP COOKIE CONSENT </span></div>
						<div class="gdpr-cookie-consent-admin-label_gdpr_label"><span>GDPR/CCPA</span></div>
					</div>
				</div>
				<div class="gdpr-cookie-consent-admin-help-and-support">
				<div class="gdpr-cookie-consent-admin-help">
						<div class="gdpr-cookie-consent-admin-help-icon">
							<!-- //image  -->
							<a href="https://club.wpeka.com/docs/wp-cookie-consent/" target="_blank">
								<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/wp_cookie_help.svg'; ?>" alt="WP Cookie Consent Help">
							</a>
						</div>
						<div class="gdpr-cookie-consent-admin-help-text"><a href="https://club.wpeka.com/docs/wp-cookie-consent/" target="_blank">
							Help Guide</a>
						</div>
					</div>
					<div class="gdpr-cookie-consent-admin-support">
						<!-- //support  -->
						<div class="gdpr-cookie-consent-admin-support-icon">
							<!-- //image  -->
							<a href="https://club.wpeka.com/contact" target="_blank">
							<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/wp_cookie_support.svg'; ?>" alt="WP Cookie Consent Support">
							</a>
						</div>
						<div class="gdpr-cookie-consent-admin-support-text"><a href="https://club.wpeka.com/contact" target="_blank">
							Support</a>
						</div>
					</div>
				</div>
		</div>

		<!-- scans -->
		<?php
		// if user is connected to the app.wplegalpages then show remaining scans
		if ( $is_user_connected == true ) {
		?>
			<div class="gdpr-remaining-scans-content" >
				<div class="gdpr-remaining-scans-container">
					<span class="gdpr-remaining-scans-title">Remaining Scans: </span><span><?php echo $gdpr_no_of_page_scan;  ?> / <?php echo $total_no_of_free_scans;  ?><span><span> (<?php echo $remaining_percentage_scan_limit ?>%)</span>
				</div>
				<div class="gdpr-current-plan-container">
					<p><span>Current Plan: </span><?php echo $api_user_plan ?></p>
					<?php
					if ( $api_user_plan == 'free' ) {
					?>
					<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/gdpr_upgrade_btn.png'; ?>" class="gdpr-cookie-consent-admin-upgrade-button" alt="<?php echo esc_attr( 'Upgrade Button', 'gdpr-cookie-consent' ); ?>">
					<?php
					}
					?>
				</div>



			</div>
		<?php

		}
		?>

		<!-- connect your website to WP Cookie Consent  -->

		<?php
		if ( $is_user_connected != true && ! $pro_installed ) {
		?>
		<div class="gdpr-cookie-consent-connect-api-container">
			<div class="gdpr-api-info-content">
			<div class="gdpr-api-detailed-info">
			<h2>
				<?php echo esc_html( 'Connect your website to WP Cookie Consent', 'gdpr-cookie-consent' ); ?>
			</h2>
			<p><?php echo esc_html( 'Sign up for a free account to integrate seamlessly with the WP Cookie Consent server. Once connected, gain full control over your settings and unlock advanced features:' , 'gdpr-cookie-consent'); ?></p>
			<p>
				<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.png'; ?>" alt="<?php echo esc_attr( 'API Connection Success Mark', 'gdpr-cookie-consent' ); ?>"></span> <strong><?php echo esc_html( 'Cookie Scanner:' , 'gdpr-cookie-consent'); ?></strong> <?php echo esc_html( 'Identify cookies on your website and automatically block them before user consent (essential for legal compliance).', 'gdpr-cookie-consent' ); ?>
			</p>
			<p>
				<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.png'; ?>" alt="<?php echo esc_attr( 'API Connection Success Mark', 'gdpr-cookie-consent' ); ?>"></span> <strong><?php echo esc_html( 'Advanced Dashboard:', 'gdpr-cookie-consent' ); ?></strong> <?php echo esc_html( 'Unlock useful insights on user\'s consent data, cookie summary, and consent logs.', 'gdpr-cookie-consent' ); ?>
			</p>
			<p>
				<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.png'; ?>" alt="<?php echo esc_attr( 'API Connection Success Mark', 'gdpr-cookie-consent' ); ?>"></span> <strong><?php echo esc_html( 'Geo-targeting:', 'gdpr-cookie-consent' ); ?></strong> <?php echo esc_html( 'Display or hide the GDPR cookie consent notice depending on the visitor’s location.' , 'gdpr-cookie-consent'); ?>
			</p>
		</div>
		<div class="gdpr-api-connection-btns">
			<button class="gdpr-start-auth"><?php echo esc_html( 'New? Create a free account', 'gdpr-cookie-consent' ); ?></button>
			<button class="api-connect-to-account-btn"><?php echo esc_html( 'Connect your existing account', 'gdpr-cookie-consent' ); ?></button>
		</div>


			</div>

		</div>

		<?php

		}
		?>

		<!-- tabs -->
		<div class="gdpr-cookie-consent-admin-tabs-section">
			<div class="gdpr-cookie-consent-admin-tabs">
				<!-- Dashboard tab  -->
				<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-dashboard-tab" data-tab="gdpr_dashboard">
					<p class="gdpr-cookie-consent-admin-tab-name">Dashboard</p>
				</div>
				<!-- Create Banner tab  -->
				<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-cookie-banner-tab" data-tab="create_cookie_banner">
					<p class="gdpr-cookie-consent-admin-tab-name">Create&nbsp;Cookie&nbsp;Banner</p>
				</div>
				<!-- Cookie Settings tab  -->
				<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-cookie-settings-tab" data-tab="cookie_settings">
					<p class="gdpr-cookie-consent-admin-tab-name">Cookie&nbsp;Settings</p>
				</div>
				<?php
					if ( $is_consent_log_on && !$pro_is_activated ) {
				?>
									<!-- consent log tab  -->
										<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-consent-logs-tab" data-tab="consent_logs">
											<p class="gdpr-cookie-consent-admin-tab-name">Consent&nbsp;Logs</p>
										</div>
					<?php
					}
						if ( $pro_is_activated ) {
							if ( $is_consent_log_on ) {
							?>
									<!-- consent log tab  -->
									<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-consent-logs-tab" data-tab="consent_logs">
										<p class="gdpr-cookie-consent-admin-tab-name">Consent&nbsp;Logs</p>
									</div>
								<?php
									}
						}
			if ( $is_data_req_on && !$pro_is_activated ) {

								?>
								<!-- data req tab  -->
								<div class="gdpr-cookie-consent-admin-tab		gdpr-cookie-consent-admin-data-request-tab" data-tab="data_request">
								<p class="gdpr-cookie-consent-admin-tab-name">Data&nbsp;Request</p>
								</div>

								<?php

							}

				if ( $pro_is_activated ) {

					?>

							<?php

                        if ( $is_data_req_on ) {

	                          ?>
							<!-- data req tab  -->
							<div class="gdpr-cookie-consent-admin-tab		gdpr-cookie-consent-admin-data-request-tab" data-tab="data_request">
								<p class="gdpr-cookie-consent-admin-tab-name">Data&nbsp;Request</p>
								</div>
								<?php

                                  }
					}

				?>

				<!-- Policy data tab  -->
				<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-policy-data-tab" data-tab="policy_data">
				<p class="gdpr-cookie-consent-admin-tab-name">Policy&nbsp;Data</p>
				</div>
				<?php
				if($pro_is_activated){
					?>
					<!-- Pro activation key -->
				        <div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-pro-activation-tab" data-tab="activation_key">
							<p class="gdpr-cookie-consent-admin-tab-name">Pro Activation</p>
						</div>
				<?php }

				?>
			</div>
		</div>

		<!-- tab content  -->

		<div class="gdpr-cookie-consent-admin-tabs-content">
			<div class="gdpr-cookie-consent-admin-tabs-inner-content">
				<!-- dashboard content  -->
				<div class="gdpr-cookie-consent-admin-dashboard-content gdpr-cookie-consent-admin-tab-content" id="gdpr_dashboard">

					<?php require_once plugin_dir_path( __FILE__ ) . 'gdpr-dashboard-tab-template.php'; ?>

				</div>
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
			</div>
		</div>


	</div>
</div>
