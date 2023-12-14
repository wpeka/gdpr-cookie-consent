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


?>

<div id="gdpr-cookie-consent-main-admin-structure" class="gdpr-cookie-consent-main-admin-structure">
	<div id="gdpr-cookie-consent-main-admin-header" class="gdpr-cookie-consent-main-admin-header">
		<!-- Main top banner  -->
		<div class="gdpr-cookie-consent-admin-fixed-banner">
				<div class="gdpr-cookie-consent-admin-logo-and-label">
					<div class="gdpr-cookie-consent-admin-logo">
						<!-- //image  -->
						<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/wp_cookie_consent_logo.png'; ?>" alt="">
					</div>
					<div class="gdpr-cookie-consent-admin-label">
						<!-- //label  -->
						<div class="gdpr-cookie-consent-admin-label_wp_label"><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/wp_cookie_consent_label.png'; ?>" alt=""></div>
						<div class="gdpr-cookie-consent-admin-label_gdpr_label"><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/gdpr_ccpa_label.png'; ?>" alt=""></div>
					</div>
				</div>
				<div class="gdpr-cookie-consent-admin-help-and-support">
				<div class="gdpr-cookie-consent-admin-help">
						<div class="gdpr-cookie-consent-admin-help-icon">
							<!-- //image  -->
							<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/wp_cookie_help.png'; ?>" alt="">
						</div>
						<div class="gdpr-cookie-consent-admin-help-text">Help Guide</div>
					</div>
					<div class="gdpr-cookie-consent-admin-support">
						<!-- //support  -->
						<div class="gdpr-cookie-consent-admin-support-icon">
							<!-- //image  -->
							<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/wp_cookie_support.png'; ?>" alt="">
						</div>
						<div class="gdpr-cookie-consent-admin-support-text">Support</div>
					</div>
				</div>
		</div>
		<!-- promotional banner  -->
		<div class="gdpr-cookie-consent-admin-promotional-banner">
			<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/wp_upgrade_to_pro.png'; ?>" alt="">
		</div>
		<!-- tabs -->
		<div class="gdpr-cookie-consent-admin-tabs-section">
			<div class="gdpr-cookie-consent-admin-tabs">
				<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-dashboard-tab" data-tab="gdpr_dashboard">
					<p class="gdpr-cookie-consent-admin-tab-name">Dashboard</p>
				</div>
				<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-cookie-banner-tab" data-tab="create_cookie_banner">
					<p class="gdpr-cookie-consent-admin-tab-name">Create&nbsp;Cookie&nbsp;Banner</p>
				</div>
				<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-cookie-settings-tab" data-tab="cookie_settings">
					<p class="gdpr-cookie-consent-admin-tab-name">Cookie&nbsp;Settings</p>
				</div>
				<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-policy-data-tab" data-tab="policy_data">
					<p class="gdpr-cookie-consent-admin-tab-name">Policy&nbsp;Data</p>
				</div>
				<?php
					$pro_is_activated = false;
					if ( $pro_is_activated ) {

						?>
							<!-- integration tab  -->
							<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-integrations-data-tab" data-tab="integrations">
								<p class="gdpr-cookie-consent-admin-tab-name">Integrations</p>
							</div>
							<!-- consent log tab  -->
							<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-consent-logs-tab" data-tab="consent_logs">
								<p class="gdpr-cookie-consent-admin-tab-name">Consent&nbsp;Logs</p>
							</div>
						<?php

					};


				?>
				<div class="gdpr-cookie-consent-admin-tab gdpr-cookie-consent-admin-legalpage-data-tab" data-tab="legal_page">
					<p class="gdpr-cookie-consent-admin-tab-name">Legal&nbsp;Page</p>
				</div>
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
					<?php require_once plugin_dir_path( __FILE__ ) . 'gdpr-policy-data-tab-template.php'; ?>
				</div>
				<!-- legal pages  -->
				<div class="gdpr-cookie-consent-admin-legal-pages-content gdpr-cookie-consent-admin-tab-content" id="legal_page">
					<?php require_once plugin_dir_path( __FILE__ ) . 'gdpr-legal-pages-tab-template.php'; ?>
				</div>
			</div>
		</div>


	</div>
</div>
