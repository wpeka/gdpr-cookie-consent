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


?>

<div id="gdpr-cookie-consent-main-admin-structure" class="gdpr-cookie-consent-main-admin-structure">
	<div id="gdpr-cookie-consent-main-admin-header" class="gdpr-cookie-consent-main-admin-header">
		<!-- Main top banner  -->
		<div class="gdpr-cookie-consent-admin-fixed-banner">
				<div class="gdpr-cookie-consent-admin-logo-and-label">
					<div class="gdpr-cookie-consent-admin-logo">
						<!-- //image  -->
						<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/wp_cookie_consent_logo.png'; ?>" alt="WP Cookie Consent Logo">
					</div>
					<div class="gdpr-cookie-consent-admin-label">
						<!-- //label  -->
						<div class="gdpr-cookie-consent-admin-label_wp_label"><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/wp_cookie_consent_label.png'; ?>" alt="WP Cookie Consent Label"></div>
						<div class="gdpr-cookie-consent-admin-label_gdpr_label"><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/gdpr_ccpa_label.png'; ?>" alt="WP Cookie Consent CCPA Label"></div>
					</div>
				</div>
				<div class="gdpr-cookie-consent-admin-help-and-support">
				<div class="gdpr-cookie-consent-admin-help">
						<div class="gdpr-cookie-consent-admin-help-icon">
							<!-- //image  -->
							<a href="https://club.wpeka.com/docs/wp-cookie-consent/" target="_blank">
								<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/wp_cookie_help.png'; ?>" alt="WP Cookie Consent Help">
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
							<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/wp_cookie_support.png'; ?>" alt="WP Cookie Consent Support">
							</a>
						</div>
						<div class="gdpr-cookie-consent-admin-support-text"><a href="https://club.wpeka.com/contact" target="_blank">
							Support</a>
						</div>
					</div>
				</div>
		</div>
		<!-- connect your website to WP Cookie Consent  -->

		<?php
		if ( $is_user_connected != true && ! $pro_installed ) {
		?>
		<div class="gdpr-cookie-consent-connect-api-container">
			<div class="gdpr-api-info-content">
				<div class="gdpr-api-detailed-info">
					<h2>
					Connect your website to WP Cookie Consent
					</h2>
					<p>Sign up for a free account to integrate seamlessly with the WP Cookie Consent server. Once connected, gain full control over your settings and unlock advanced features:</p>
					<p>
					<p><span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.png'; ?>" alt="API Connection Success Mark"></span> <strong>Cookie Scanner :</strong> Identify cookies on your website and automatically block them before user consent (essential for legal compliance).</p>
					<p>
					<p><span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.png'; ?>" alt="API Connection Success Mark"></span> <strong>Advanced Dashboard :</strong> Unlock useful insights on user's consent data, cookie summary, and consent logs.</p>
					<p><span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/bullet_point.png'; ?>" alt="API Connection Success Mark"></span> <strong>Geo-targeting :</strong> Display or hide the GDPR cookie consent notice depending on the visitor’s location.</p>
				</div>
				<div class="gdpr-api-connection-btns">
					<button class="gdpr-start-auth">New? Create a free account</button>
					<button class="api-connect-to-account-btn">Connect your existing account</button>
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
				<!-- tab for legal page promotion  -->
				<!-- <div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-legalpage-data-tab" data-tab="legal_page">
					<p class="gdpr-cookie-consent-admin-tab-name">Legal&nbsp;Page</p>
				</div> -->
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
				<!-- legal pages  -->
				<!-- <div class="gdpr-cookie-consent-admin-legal-pages-content gdpr-cookie-consent-admin-tab-content" id="legal_page">
					<?php require_once plugin_dir_path( __FILE__ ) . 'gdpr-legal-pages-tab-template.php'; ?>
				</div> -->
			</div>
		</div>


	</div>
</div>
