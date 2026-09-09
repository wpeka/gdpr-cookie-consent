<?php

/**
 * Provide an admin dashboard area view for the WP Cookie Consent plugin
 *
 * This file is used to markup the admin-facing aspects of the WP Cookie Consent plugin.
 *
 * @link       https://club.wpeka.com/
 * @since      2.4.1
 *
 * @package gdpr-cookie-consent
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}	
// check if pro is activated or installed.
$pro_is_activated  = get_option( 'wpl_pro_active', false );
$the_options       = Gdpr_Cookie_Consent::gdpr_get_settings();
$is_data_req_on    = isset( $the_options['data_reqs_on'] ) ? $the_options['data_reqs_on'] : null;
$is_consent_log_on = isset( $the_options['logging_on'] ) ? $the_options['logging_on'] : null;
$installed_plugins = get_plugins();
$pro_installed     = isset( $installed_plugins['wpl-cookie-consent/wpl-cookie-consent.php'] ) ? true : false;

$plugin_name                    = 'wplegalpages/wplegalpages.php';
$gdpr_plugin 					= 'gdpr-cookie-consent/gdpr-cookie-consent.php';
$is_legalpages_active			= is_plugin_active( $plugin_name );
$is_gdpr_active 				= is_plugin_active( $gdpr_plugin );
$is_banner_active = $is_gdpr_active && ! empty( $the_options['is_on'] );
$page_view_notice_message = get_option( 'page_view_notice_message', '' );
// Require the class file for gdpr cookie consent api framework settings.
require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';

// Instantiate a new object of the GDPR_Cookie_Consent_Settings class.
$this->settings = new GDPR_Cookie_Consent_Settings();

// Call the is_connected() method from the instantiated object to check if the user is connected.
$is_user_connected = $this->settings->is_connected();
$api_user_plan     = $this->settings->get_plan();

$free_trial_data = get_option( 'wplp_free_trial_data', [] );

$local_expiry = (int) ( $free_trial_data['localExpiry'] ?? 0 );

$is_free_trial_active = ! empty( $free_trial_data['isTrialActive'] ) && time() < $local_expiry;
$trialEndsIn          = $is_free_trial_active ? ceil( ( $local_expiry - time() ) / DAY_IN_SECONDS ) : 0;
$trialStartDate       = $free_trial_data['trialStartDate'] ?? '';
$trialEndDate         = $free_trial_data['trialEndDate'] ?? '';
/*
* Number of scans on the basis of user's plan
*/
if ( $api_user_plan == 'free' ) {
	$total_no_of_free_scans = 100;
} else {
	$total_no_of_free_scans = 20000; // actual 50000.
}

$gdpr_pages_scanned 			 = get_option('gdpr_no_of_page_scan', 0);
$gdpr_no_of_page_scan            = $total_no_of_free_scans - get_option( 'gdpr_no_of_page_scan' );
$remaining_percentage_scan_limit = round( ( get_option( 'gdpr_no_of_page_scan' ) / $total_no_of_free_scans ) * 100 );

//Monthly scan
$gdpr_monthly_scan_percent = 0;
if ( 'free' === $api_user_plan ) { 
	$scan_limit     = get_transient( 'gdpr_monthly_scan_limit_exhausted' );
	$scan_limit_int = (int) $scan_limit; 
	$gdpr_monthly_scan_percent = ( ( $scan_limit_int ) / 5 ) * 100;
} else if ( '3sites' === strtolower( $api_user_plan ) ) {
	$scan_limit     = get_transient( 'gdpr_monthly_scan_limit_exhausted' );
	$scan_limit_int = (int) $scan_limit; 
	$gdpr_monthly_scan_percent = ( ( $scan_limit_int ) / 50 ) * 100;
}

$gdpr_monthly_page_views = get_option('wpl_monthly_page_views', 0);
$gdpr_monthly_page_views_limit = 0;
$gdpr_monthly_page_views_percent = 0;
if ( 'free' === $api_user_plan ) { 
	$gdpr_monthly_page_views_limit = 20000;
	$gdpr_monthly_page_views_percent = ( ( $gdpr_monthly_page_views ) / 20000 ) * 100;
} else if ( '3sites' === strtolower( $api_user_plan ) ) {
	$gdpr_monthly_page_views_limit = 100000;
	$gdpr_monthly_page_views_percent = ( ( $gdpr_monthly_page_views ) / 100000 ) * 100;
}

$gdpr_remaining_page_views = $gdpr_monthly_page_views_limit - $gdpr_monthly_page_views;

$gdpr_plan_warning = false;

if( $gdpr_monthly_page_views_percent === 100 || $remaining_percentage_scan_limit === 100 || $gdpr_monthly_scan_percent === 100 ) {
	$gdpr_plan_warning = true;
}
$site_url = get_site_url();
$site_domain = wp_parse_url($site_url, PHP_URL_HOST);
$gdpr_installed     = isset( $installed_plugins['gdpr-cookie-consent/gdpr-cookie-consent.php'] ) ? true : false;
$step1_completed = (bool) $is_banner_active;
$step2_completed = ( $is_user_connected === 'true' );
if(!$is_banner_active && $is_user_connected){
	$temp_step1      = $step1_completed;
	$step1_completed = $step2_completed;
	$step2_completed = $temp_step1;
}
$completed_steps = (int) $step1_completed + (int) $step2_completed;
$total_steps = 2;

?>

<div id="gdpr-cookie-consent-main-admin-structure" class="gdpr-cookie-consent-main-admin-structure">
	<div id="gdpr-cookie-consent-main-admin-header" class="gdpr-cookie-consent-main-admin-header">
		<div class="wplp-compliance-main-wrapper">

			<div id="gdpr-before-mount">
  				<div style="text-align:center;">
  				  	<div class="gdpr-before-mount-loader-content"></div>
  				  	<p class="gdpr-before-mount-loader-text">
  				  	  	Loading...
  				  	</p>
  				</div>
			</div>

			<!-- Main top banner  -->
			<div class="gdpr-cookie-consent-admin-fixed-banner">
					<div class="gdpr-cookie-consent-admin-logo-and-label">
						<div class="gdpr-cookie-consent-admin-logo">
							<!-- //image  -->
							<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/WPLPCompliancePlatformWhite.png'; ?>" alt="WP Cookie Consent Logo">
						</div>
					</div>
					<div class="gdpr-cookie-consent-admin-help-and-support">
						<?php if ( $is_user_connected ) : ?>
						<div class="gdpr-cookie-consent-admin-new-dashboard-btn"><a style="text-decoration: none;" target="_blank" href="<?php echo esc_url( GDPR_APP_URL . '/app?site=' . $site_domain ); ?>"><?php esc_html_e( 'Try New Dashboard', 'gdpr-cookie-consent' ); ?><span class="gdpr-cookie-consent-admin-new-dashboard-btn-beta-span"><?php esc_html_e( 'BETA', 'gdpr-cookie-consent')?></span></a></div>
						<?php endif; ?>
						<div class="gdpr-cookie-consent-admin-help">
							<div class="gdpr-cookie-consent-admin-help-icon">
								<!-- //image  -->
								<a href="https://club.wpeka.com/docs/wp-cookie-consent/" target="_blank">
									<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/wp_cookie_help.svg'; ?>" alt="WP Cookie Consent Help">
								</a>
							</div>
							<div class="gdpr-cookie-consent-admin-help-text"><a href="https://wplegalpages.com/docs/wplp-docs/" target="_blank">
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

						<div class="gdpr-cookie-consent-admin-login">
							<div class="gdpr-cookie-consent-admin-login-icon">
								<a <?php if ( $is_user_connected ) {
									echo 'href="https://app.wplegalpages.com/my-account" target="_blank"';
								} else {
									echo 'class="api-connect-to-account-btn"'; 
								} ?> >
									<img src="<?php echo $is_user_connected ? esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/admin_my_account.svg' : esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/admin_login.svg'; ?>" alt="Login/Logout">
								</a>
							</div>
							<div class="gdpr-cookie-consent-admin-login-text">
								<a <?php if ( $is_user_connected ) {
									echo 'href="https://app.wplegalpages.com/my-account" target="_blank"';
								} else {
									echo 'class="api-connect-to-account-btn"'; 
								} ?> ><?php echo $is_user_connected ? esc_html('My Account', 'gdpr-cookie-consent') :esc_html('Login', 'gdpr-cookie-consent'); ?></a>
							</div>
						</div>
					</div>
			</div>

			<!-- tabs -->
			<?php 
				$wplp_current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
				$wplp_is_dashboard  = ( $wplp_current_page === 'wplp-dashboard' );
			?>
				<div class="wplp-compliance-main <?php echo $wplp_is_dashboard ? ' wplp-dashboard-horizontal' : ''; ?>">
				<div class="gdpr-cookie-consent-admin-tabs-section">
				<div class="gdpr-cookie-consent-admin-tabs dashboard-tabs<?php echo $wplp_is_dashboard ? ' wplp-tabs-horizontal-top' : ''; ?>">
						<!-- Dashboard tab  -->
						 <?php  if ($is_legalpages_active) {
								$plugin_slug = 'wplegalpages/wplegalpages.php';
								// Fetch the plugin data
								$plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_slug);
						
								// Get the version
								$legalpages_version = $plugin_data['Version'];
								if($legalpages_version >= '3.3.0') { ?>

							<a href="?page=wplp-dashboard" class="gdpr-admin-tab-link wplp-main-tab gdpr-cookie-consent-admin-dashboard-tab">
								<div class="wp-legalpages-admin-gdpr-main-tab wplp-admin-tab-link-content">
									<div class="wplp-admin-tab-link-left">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M10.8333 7.5V2.5H17.5V7.5H10.8333ZM2.5 10.8333V2.5H9.16667V10.8333H2.5ZM10.8333 17.5V9.16667H17.5V17.5H10.8333ZM2.5 17.5V12.5H9.16667V17.5H2.5Z" fill="#074EA8"/>
										</svg>

										<?php echo esc_html('Dashboard','gdpr-cookie-consent'); ?>
									</div>
								</div>
							</a>
						<?php } }
						else{
							?>
							<a href="?page=wplp-dashboard" class="gdpr-admin-tab-link wplp-main-tab gdpr-cookie-consent-admin-dashboard-tab">
								<div class="wp-legalpages-admin-gdpr-main-tab wplp-admin-tab-link-content">
									<div class="wplp-admin-tab-link-left">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M10.8333 7.5V2.5H17.5V7.5H10.8333ZM2.5 10.8333V2.5H9.16667V10.8333H2.5ZM10.8333 17.5V9.16667H17.5V17.5H10.8333ZM2.5 17.5V12.5H9.16667V17.5H2.5Z" fill="currentColor"/>
										</svg>
										
										<?php echo esc_html('Dashboard','gdpr-cookie-consent'); ?>
									</div>
								</div>
							</a> 
						<?php
						} ?>
						<!-- Legal Pages Plugin tab  -->
						<?php $lp_terms_accepted = get_option('lp_accept_terms');?>
                        <a href="<?php echo $lp_terms_accepted === '1' ? '?page=legal-pages#settings' : '?page=legal-pages' ?>" class="gdpr-admin-tab-link wplp-main-tab">
							<div class="wplp-admin-tab-link-content">
								<div class="wp-legalpages-admin-gdpr-main-tab wplp-admin-tab-link-left">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M19.7623 17.0918C19.7623 17.4933 19.6221 17.8342 19.3418 18.1145L18.1259 19.3418C17.8304 19.6221 17.4857 19.7623 17.0918 19.7623C16.6903 19.7623 16.3494 19.6221 16.0691 19.3418L11.9441 15.2054C11.6562 14.9327 11.5123 14.5918 11.5123 14.1827C11.5123 13.7812 11.6751 13.4176 12.0009 13.0918L9.0918 10.1827L7.65998 11.6145C7.55392 11.7206 7.42513 11.7736 7.27361 11.7736C7.1221 11.7736 6.99331 11.7206 6.88725 11.6145C6.9024 11.6297 6.94975 11.6751 7.0293 11.7509C7.10884 11.8266 7.15619 11.8759 7.17134 11.8986C7.18649 11.9213 7.22437 11.9649 7.28498 12.0293C7.34558 12.0937 7.38346 12.1448 7.39861 12.1827C7.41377 12.2206 7.43649 12.2717 7.4668 12.3361C7.4971 12.4005 7.51793 12.463 7.5293 12.5236C7.54066 12.5842 7.54634 12.6524 7.54634 12.7282C7.54634 13.016 7.44028 13.2736 7.22816 13.5009C7.20543 13.5236 7.14293 13.5918 7.04066 13.7054C6.93839 13.8191 6.86642 13.8967 6.82475 13.9384C6.78308 13.9801 6.71301 14.0426 6.61452 14.1259C6.51604 14.2092 6.43271 14.2679 6.36452 14.302C6.29634 14.3361 6.21301 14.3702 6.11452 14.4043C6.01604 14.4384 5.91755 14.4554 5.81907 14.4554C5.51604 14.4554 5.25846 14.3494 5.04634 14.1373L0.409979 9.50089C0.197857 9.28877 0.0917969 9.03119 0.0917969 8.72816C0.0917969 8.62967 0.108842 8.53119 0.142933 8.43271C0.177024 8.33422 0.211115 8.25089 0.245206 8.18271C0.279297 8.11452 0.338009 8.03119 0.421342 7.93271C0.504676 7.83422 0.567176 7.76414 0.608842 7.72248C0.650509 7.68081 0.72816 7.60884 0.841797 7.50657C0.955433 7.4043 1.02362 7.3418 1.04634 7.31907C1.27362 7.10695 1.53119 7.00089 1.81907 7.00089C1.89483 7.00089 1.96301 7.00657 2.02362 7.01793C2.08422 7.0293 2.14672 7.05013 2.21111 7.08043C2.27551 7.11074 2.32665 7.13346 2.36452 7.14861C2.4024 7.16377 2.45354 7.20164 2.51793 7.26225C2.58233 7.32286 2.62589 7.36074 2.64861 7.37589C2.67134 7.39104 2.72058 7.43839 2.79634 7.51793C2.8721 7.59748 2.91755 7.64483 2.93271 7.65998C2.82665 7.55392 2.77361 7.42513 2.77361 7.27361C2.77361 7.1221 2.82665 6.99331 2.93271 6.88725L6.88725 2.93271C6.99331 2.82665 7.1221 2.77361 7.27361 2.77361C7.42513 2.77361 7.55392 2.82665 7.65998 2.93271C7.64483 2.91755 7.59748 2.8721 7.51793 2.79634C7.43839 2.72058 7.39104 2.67134 7.37589 2.64861C7.36074 2.62589 7.32286 2.58233 7.26225 2.51793C7.20164 2.45354 7.16377 2.4024 7.14861 2.36452C7.13346 2.32665 7.11074 2.27551 7.08043 2.21111C7.05013 2.14672 7.0293 2.08422 7.01793 2.02362C7.00657 1.96301 7.00089 1.89483 7.00089 1.81907C7.00089 1.53119 7.10695 1.27362 7.31907 1.04634C7.3418 1.02362 7.4043 0.955433 7.50657 0.841797C7.60884 0.72816 7.68081 0.650509 7.72248 0.608842C7.76414 0.567176 7.83422 0.504676 7.93271 0.421342C8.03119 0.338009 8.11452 0.279297 8.18271 0.245206C8.25089 0.211115 8.33422 0.177024 8.43271 0.142933C8.53119 0.108842 8.62967 0.0917969 8.72816 0.0917969C9.03119 0.0917969 9.28877 0.197857 9.50089 0.409979L14.1373 5.04634C14.3494 5.25846 14.4554 5.51604 14.4554 5.81907C14.4554 5.91755 14.4384 6.01604 14.4043 6.11452C14.3702 6.21301 14.3361 6.29634 14.302 6.36452C14.2679 6.43271 14.2092 6.51604 14.1259 6.61452C14.0426 6.71301 13.9801 6.78308 13.9384 6.82475C13.8967 6.86642 13.8191 6.93839 13.7054 7.04066C13.5918 7.14293 13.5236 7.20543 13.5009 7.22816C13.2736 7.44028 13.016 7.54634 12.7282 7.54634C12.6524 7.54634 12.5842 7.54066 12.5236 7.5293C12.463 7.51793 12.4005 7.4971 12.3361 7.4668C12.2717 7.43649 12.2206 7.41377 12.1827 7.39861C12.1448 7.38346 12.0937 7.34558 12.0293 7.28498C11.9649 7.22437 11.9213 7.18649 11.8986 7.17134C11.8759 7.15619 11.8266 7.10884 11.7509 7.0293C11.6751 6.94975 11.6297 6.9024 11.6145 6.88725C11.7206 6.99331 11.7736 7.1221 11.7736 7.27361C11.7736 7.42513 11.7206 7.55392 11.6145 7.65998L10.1827 9.0918L13.0918 12.0009C13.4176 11.6751 13.7812 11.5123 14.1827 11.5123C14.5766 11.5123 14.9213 11.6524 15.2168 11.9327L19.3418 16.0577C19.6221 16.3532 19.7623 16.6979 19.7623 17.0918Z" fill="currentColor"/>
									</svg>
									<?php echo esc_html('Legal Pages','gdpr-cookie-consent'); ?>
								</div>
								
							</div>
						</a>
						<!-- Cookie Consent Plugin tab  -->
						<a href="?page=gdpr-cookie-consent#cookie_settings" class="gdpr-admin-tab-link gdpr-cookie-consent-tab">
							<div class="wplp-admin-tab-link-content wplp-compliance-cookie-consent-tab">
								<div class="gdpr-admin-main-tab wplp-admin-tab-link-left">
									<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M8.99984 17.334C7.84706 17.334 6.76373 17.1151 5.74984 16.6773C4.73595 16.2395 3.854 15.6459 3.104 14.8965C2.354 14.147 1.76039 13.2651 1.32317 12.2507C0.885948 11.2362 0.667059 10.1529 0.666504 9.00065C0.666504 7.95898 0.867893 6.93815 1.27067 5.93815C1.67345 4.93815 2.23595 4.04565 2.95817 3.26065C3.68039 2.47565 4.54845 1.84371 5.56234 1.36482C6.57623 0.885929 7.68734 0.646484 8.89567 0.646484C9.18734 0.646484 9.48595 0.660373 9.7915 0.688151C10.0971 0.715929 10.4096 0.76454 10.729 0.833984C10.604 1.45898 10.6457 2.04926 10.854 2.60482C11.0623 3.16037 11.3748 3.62204 11.7915 3.98982C12.2082 4.3576 12.7048 4.61121 13.2815 4.75065C13.8582 4.8901 14.4518 4.85537 15.0623 4.64648C14.7012 5.46593 14.7534 6.25065 15.219 7.00065C15.6846 7.75065 16.3754 8.13954 17.2915 8.16732C17.3054 8.3201 17.3159 8.46232 17.3232 8.59398C17.3304 8.72565 17.3337 8.86815 17.3332 9.02148C17.3332 10.1604 17.1143 11.2332 16.6765 12.2398C16.2387 13.2465 15.6451 14.1284 14.8957 14.8857C14.1462 15.6429 13.2643 16.2401 12.2498 16.6773C11.2354 17.1145 10.1521 17.3334 8.99984 17.334ZM7.74984 7.33398C8.09706 7.33398 8.39234 7.2126 8.63567 6.96982C8.879 6.72704 9.00039 6.43176 8.99984 6.08398C8.99928 5.73621 8.87789 5.44121 8.63567 5.19898C8.39345 4.95676 8.09817 4.8351 7.74984 4.83398C7.4015 4.83287 7.1065 4.95454 6.86484 5.19898C6.62317 5.44343 6.5015 5.73843 6.49984 6.08398C6.49817 6.42954 6.61984 6.72482 6.86484 6.96982C7.10984 7.21482 7.40484 7.33621 7.74984 7.33398ZM6.08317 11.5007C6.43039 11.5007 6.72567 11.3793 6.969 11.1365C7.21234 10.8937 7.33373 10.5984 7.33317 10.2507C7.33261 9.90287 7.21123 9.60787 6.969 9.36565C6.72678 9.12343 6.4315 9.00176 6.08317 9.00065C5.73484 8.99954 5.43984 9.12121 5.19817 9.36565C4.9565 9.6101 4.83484 9.9051 4.83317 10.2507C4.8315 10.5962 4.95317 10.8915 5.19817 11.1365C5.44317 11.3815 5.73817 11.5029 6.08317 11.5007ZM11.4998 12.334C11.7359 12.334 11.934 12.254 12.094 12.094C12.254 11.934 12.3337 11.7362 12.3332 11.5007C12.3326 11.2651 12.2526 11.0673 12.0932 10.9073C11.9337 10.7473 11.7359 10.6673 11.4998 10.6673C11.2637 10.6673 11.0659 10.7473 10.9065 10.9073C10.7471 11.0673 10.6671 11.2651 10.6665 11.5007C10.6659 11.7362 10.7459 11.9343 10.9065 12.0948C11.0671 12.2554 11.2648 12.3351 11.4998 12.334Z" fill="currentColor"/>
									</svg>

									<?php echo esc_html('Cookie Consent','gdpr-cookie-consent'); ?>
								</div>
							</div>
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
								<div class="gdpr-admin-main-tab wplp-admin-tab-link-content">
									<div class="wplp-admin-tab-link-left">
										<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M8.95817 13.9994C9.24984 13.9994 9.4965 13.8985 9.69817 13.6969C9.89984 13.4952 10.0004 13.2488 9.99984 12.9577C9.99928 12.6666 9.89873 12.4199 9.69817 12.2177C9.49762 12.0155 9.25095 11.9149 8.95817 11.916C8.66539 11.9171 8.419 12.018 8.219 12.2185C8.019 12.4191 7.91817 12.6655 7.9165 12.9577C7.91484 13.2499 8.01567 13.4966 8.219 13.6977C8.42234 13.8988 8.66873 13.9994 8.95817 13.9994ZM8.20817 10.791H9.74984C9.74984 10.3327 9.80206 9.97157 9.9065 9.70769C10.0109 9.4438 10.3059 9.08269 10.7915 8.62435C11.1526 8.26324 11.4373 7.91935 11.6457 7.59269C11.854 7.26602 11.9582 6.8738 11.9582 6.41602C11.9582 5.63824 11.6734 5.04102 11.104 4.62435C10.5346 4.20769 9.86095 3.99935 9.08317 3.99935C8.2915 3.99935 7.64928 4.20769 7.15651 4.62435C6.66373 5.04102 6.31984 5.54102 6.12484 6.12435L7.49984 6.66602C7.56928 6.41602 7.72567 6.14519 7.969 5.85352C8.21234 5.56185 8.58373 5.41602 9.08317 5.41602C9.52762 5.41602 9.86095 5.53769 10.0832 5.78102C10.3054 6.02435 10.4165 6.29158 10.4165 6.58269C10.4165 6.86046 10.3332 7.12102 10.1665 7.36435C9.99984 7.60769 9.7915 7.83324 9.5415 8.04102C8.93039 8.58269 8.55539 8.99241 8.4165 9.27019C8.27762 9.54796 8.20817 10.0549 8.20817 10.791ZM8.99984 17.3327C7.84706 17.3327 6.76373 17.1141 5.74984 16.6769C4.73595 16.2396 3.854 15.6457 3.10401 14.8952C2.354 14.1446 1.76039 13.2627 1.32317 12.2494C0.885949 11.236 0.667061 10.1527 0.666505 8.99935C0.665949 7.84602 0.884838 6.76269 1.32317 5.74935C1.76151 4.73602 2.35512 3.85408 3.10401 3.10352C3.85289 2.35296 4.73484 1.75935 5.74984 1.32269C6.76484 0.88602 7.84817 0.667131 8.99984 0.66602C10.1515 0.664909 11.2348 0.883798 12.2498 1.32269C13.2648 1.76158 14.1468 2.35519 14.8957 3.10352C15.6446 3.85185 16.2384 4.7338 16.6773 5.74935C17.1162 6.76491 17.3348 7.84824 17.3332 8.99935C17.3315 10.1505 17.1126 11.2338 16.6765 12.2494C16.2404 13.2649 15.6468 14.1469 14.8957 14.8952C14.1446 15.6435 13.2626 16.2374 12.2498 16.6769C11.2371 17.1163 10.1537 17.3349 8.99984 17.3327Z" fill="currentColor"/>
										</svg>

										<?php echo esc_html('Help','gdpr-cookie-consent'); ?>
									</div>
								</div>
							</a>
						<?php } }
						else{
							?>

								<a href="?page=wplp-dashboard#help-page" class="gdpr-admin-tab-link gdpr-cookie-consent-admin-help-tab">
									<div class="gdpr-admin-main-tab wplp-admin-tab-link-content">
										<div class="wplp-admin-tab-link-left">
											<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M8.95817 13.9994C9.24984 13.9994 9.4965 13.8985 9.69817 13.6969C9.89984 13.4952 10.0004 13.2488 9.99984 12.9577C9.99928 12.6666 9.89873 12.4199 9.69817 12.2177C9.49762 12.0155 9.25095 11.9149 8.95817 11.916C8.66539 11.9171 8.419 12.018 8.219 12.2185C8.019 12.4191 7.91817 12.6655 7.9165 12.9577C7.91484 13.2499 8.01567 13.4966 8.219 13.6977C8.42234 13.8988 8.66873 13.9994 8.95817 13.9994ZM8.20817 10.791H9.74984C9.74984 10.3327 9.80206 9.97157 9.9065 9.70769C10.0109 9.4438 10.3059 9.08269 10.7915 8.62435C11.1526 8.26324 11.4373 7.91935 11.6457 7.59269C11.854 7.26602 11.9582 6.8738 11.9582 6.41602C11.9582 5.63824 11.6734 5.04102 11.104 4.62435C10.5346 4.20769 9.86095 3.99935 9.08317 3.99935C8.2915 3.99935 7.64928 4.20769 7.15651 4.62435C6.66373 5.04102 6.31984 5.54102 6.12484 6.12435L7.49984 6.66602C7.56928 6.41602 7.72567 6.14519 7.969 5.85352C8.21234 5.56185 8.58373 5.41602 9.08317 5.41602C9.52762 5.41602 9.86095 5.53769 10.0832 5.78102C10.3054 6.02435 10.4165 6.29158 10.4165 6.58269C10.4165 6.86046 10.3332 7.12102 10.1665 7.36435C9.99984 7.60769 9.7915 7.83324 9.5415 8.04102C8.93039 8.58269 8.55539 8.99241 8.4165 9.27019C8.27762 9.54796 8.20817 10.0549 8.20817 10.791ZM8.99984 17.3327C7.84706 17.3327 6.76373 17.1141 5.74984 16.6769C4.73595 16.2396 3.854 15.6457 3.10401 14.8952C2.354 14.1446 1.76039 13.2627 1.32317 12.2494C0.885949 11.236 0.667061 10.1527 0.666505 8.99935C0.665949 7.84602 0.884838 6.76269 1.32317 5.74935C1.76151 4.73602 2.35512 3.85408 3.10401 3.10352C3.85289 2.35296 4.73484 1.75935 5.74984 1.32269C6.76484 0.88602 7.84817 0.667131 8.99984 0.66602C10.1515 0.664909 11.2348 0.883798 12.2498 1.32269C13.2648 1.76158 14.1468 2.35519 14.8957 3.10352C15.6446 3.85185 16.2384 4.7338 16.6773 5.74935C17.1162 6.76491 17.3348 7.84824 17.3332 8.99935C17.3315 10.1505 17.1126 11.2338 16.6765 12.2494C16.2404 13.2649 15.6468 14.1469 14.8957 14.8952C14.1446 15.6435 13.2626 16.2374 12.2498 16.6769C11.2371 17.1163 10.1537 17.3349 8.99984 17.3327Z" fill="currentColor"/>
											</svg>

											<?php echo esc_html('Help','gdpr-cookie-consent'); ?>
										</div>
									</div>
								</a>
							<?php
						} ?>

					</div>
				</div>

				<div class="wplp-compliance-content-wrapper">
					
					<!-- tab content  -->

					<div class="gdpr-cookie-consent-admin-tabs-content">
						<div class="gdpr-cookie-consent-admin-tabs-inner-content">
							<!-- dashboard content  -->
							<div class="gdpr-cookie-consent-admin-dashboard-content gdpr-cookie-consent-admin-tab-content" id="dashboard-tab">
								<!--exhaused popup -->
									<div id="popup-site-excausted" class="popup-overlay">
										<div class="popup-content">
											<div class="popup-header">
												<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Right Corner Image" class="popup-image">
											</div>
												<div class="excausted-popup-body">
													<h2><?php echo esc_html( 'Attention! Usage Limit Reached', 'gdpr-cookie-consent' ); ?></h2>
													<p><?php echo esc_html( 'You\'ve reached your license limit. Please upgrade to continue using the plugin on this site.', 'gdpr-cookie-consent' ); ?></p>
													<button class="gdpr-cookie-consent-admin-upgrade-button upgrade-button"><?php echo esc_html( 'Upgrade Plan', 'gdpr-cookie-consent' ); ?></button>
													<p><?php echo esc_html( 'Need to activate on a new site? Manage your licenses in', 'gdpr-cookie-consent' ); ?> <a href="https://app.wplegalpages.com/signup/api-keys/" target="_blank"><?php echo esc_html( 'My Account.', 'gdpr-cookie-consent' ); ?></a></p>
												</div>
										</div>
									</div>
									<?php
								if ( ($is_user_connected == true && !$pro_installed) || $is_user_connected == false ) { ?>
									<?php if($gdpr_monthly_page_views_percent >=80) { ?>
											<div class="upgrade-to-pro-banner-cookie-views">
												<svg width="30" height="30" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
													<path fill="#F97316" d="M320 64C334.7 64 348.2 72.1 355.2 85L571.2 485C577.9 497.4 577.6 512.4 570.4 524.5C563.2 536.6 550.1 544 536 544L104 544C89.9 544 76.8 536.6 69.6 524.5C62.4 512.4 62.1 497.4 68.8 485L284.8 85C291.8 72.1 305.3 64 320 64zM320 416C302.3 416 288 430.3 288 448C288 465.7 302.3 480 320 480C337.7 480 352 465.7 352 448C352 430.3 337.7 416 320 416zM320 224C301.8 224 287.3 239.5 288.6 257.7L296 361.7C296.9 374.2 307.4 384 319.9 384C332.5 384 342.9 374.3 343.8 361.7L351.2 257.7C352.5 239.5 338.1 224 319.8 224z"/>
												</svg>
												<div class="upgrade-to-pro-content">
													<div style="display:flex;flex-direction:column">
														<p style="font-size:16px;font-weight:500"><?php esc_html_e("You've used 80% of your monthly cookie banner views.", "gdpr-cookie-consent")?></p>
														<p><?php esc_html_e("Upgrade now for  unlimited banner views and uniterrupted compliance.", "gdpr-cookie-consent") ?></p>
													</div>
													<button
														type="button"
														class="go-to-dashboard-btn"
														<?php if(($is_user_connected==true && $api_user_plan=='free') || !$is_user_connected):?>
															onclick="window.open('<?php echo esc_url( 'https://app.wplegalpages.com/pricing' ); ?>', '_blank');"
														<?php elseif ($is_user_connected==true && ($api_user_plan=='3sites' || $api_user_plan=='3Sites')): ?>
															onclick="window.open('<?php echo esc_url( 'https://app.wplegalpages.com/app/active-plans' ); ?>', '_blank');"
														<?php endif?>
													>
															<?php esc_html_e("Upgrade Plan", "gdpr-cookie-consent")?>
														<span>
															<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path d="M16.6667 8.33325V3.33325H11.6667M16.6667 3.33325L10 9.99992" stroke="#FFF" stroke-width="1.66667"/>
																<path d="M9.16666 4.16675H5.83332C4.91285 4.16675 4.16666 4.91294 4.16666 5.83341V14.1667C4.16666 15.0872 4.91285 15.8334 5.83332 15.8334H14.1667C15.0871 15.8334 15.8333 15.0872 15.8333 14.1667V10.8334" stroke="#FFF" stroke-width="1.66667" stroke-linecap="round"/>
															</svg>
														</span>
													</button>
												</div>
											</div>
									<?php } ?>

									<?php if ( get_transient( 'app_wplp_subscription_payment_status_failed' ) ) { ?>
									<div class="gdpr-subsription-payment-failed-notice" >
										<div class="gdpr-payment-fail-icon-wrapper">
											<svg viewBox="0 0 24 24">
  											  <circle cx="12" cy="12" r="10" stroke="currentColor" fill="none" stroke-width="2"/>
  											  <line x1="12" y1="8" x2="12" y2="8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
  											  <line x1="12" y1="12" x2="12" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
  											</svg>
										</div>
										<div class="gdpr-payment-fail-right-wrapper">
											<div class="gdpr-payment-fail-content-wrapper">
												<h1><?php esc_html_e( 'Your last payment attempt failed.', 'gdpr-cookie-consent' ); ?></h1>
												<p><?php esc_html_e( 'Please update your payment details within 7 days to avoid service disruption.', 'gdpr-cookie-consent' ); ?></p>
											</div>
													
											<a href="<?php echo esc_url( 'https://app.wplegalpages.com/pricing/' ) ?>" target="_blank" class="gdpr-payment-fail-upgrade-button"><?php echo esc_html('Restore Plan', 'gdpr-cookie-consent'); ?></a>
										</div>
									</div>
									<?php
									}

									if ( get_option( 'app_wplp_subscription_status_pending_cancel' ) ) { ?>
										<div class="gdpr-subsription-payment-failed-notice" >
											<div class="gdpr-payment-fail-icon-wrapper">
												<svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M11.9998 8.99999V13M11.9998 17H12.0098M10.6151 3.89171L2.39019 18.0983C1.93398 18.8863 1.70588 19.2803 1.73959 19.6037C1.769 19.8857 1.91677 20.142 2.14613 20.3088C2.40908 20.5 2.86435 20.5 3.77487 20.5H20.2246C21.1352 20.5 21.5904 20.5 21.8534 20.3088C22.0827 20.142 22.2305 19.8857 22.2599 19.6037C22.2936 19.2803 22.0655 18.8863 21.6093 18.0983L13.3844 3.89171C12.9299 3.10654 12.7026 2.71396 12.4061 2.58211C12.1474 2.4671 11.8521 2.4671 11.5935 2.58211C11.2969 2.71396 11.0696 3.10655 10.6151 3.89171Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
											</div>

											<div class="gdpr-payment-fail-right-wrapper">
												<div class="gdpr-payment-fail-content-wrapper">
													<h1><?php esc_html_e( 'Your plan has been cancelled.', 'gdpr-cookie-consent' ); ?></h1>
													<p><?php esc_html_e( 'You\'ll lose access to premium features soon. Upgrade now to avoid interruption.', 'gdpr-cookie-consent' ); ?></p>
												</div>

												<a href="<?php echo esc_url( 'https://app.wplegalpages.com/pricing/' ) ?>" target="_blank" class="gdpr-payment-fail-upgrade-button"><?php echo esc_html('Restore Plan', 'gdpr-cookie-consent'); ?></a>
											</div>
										</div>
									<?php
									}
								}
								?>
								<!-- upgrade to pro banner -->
								<?php if($is_user_connected && $api_user_plan==='free'): ?>
									<div class="upgrade-to-pro-banner">
										<svg width="27" height="26" viewBox="0 0 27 26" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M10.4137 5.0119C11.4303 2.47747 11.9386 1.21026 12.7645 1.03464C12.9816 0.988455 13.206 0.988455 13.4232 1.03464C14.249 1.21026 14.7573 2.47747 15.7739 5.01189C16.3521 6.45317 16.6411 7.17381 17.182 7.66396C17.3337 7.80144 17.4984 7.92388 17.6738 8.02957C18.2989 8.40636 19.0793 8.47626 20.6402 8.61604C23.2824 8.85267 24.6035 8.97099 25.0069 9.72425C25.0905 9.88025 25.1473 10.0492 25.175 10.224C25.3087 11.0679 24.3375 11.9515 22.3951 13.7187L21.8557 14.2094C20.9476 15.0356 20.4935 15.4487 20.2309 15.9643C20.0734 16.2735 19.9677 16.6066 19.9182 16.9501C19.8357 17.5228 19.9687 18.122 20.2346 19.3206L20.3296 19.7488C20.8065 21.8983 21.045 22.973 20.7473 23.5012C20.4799 23.9757 19.9874 24.2795 19.4434 24.3055C18.8378 24.3344 17.9844 23.639 16.2776 22.2482C15.1531 21.3319 14.5908 20.8738 13.9666 20.6948C13.3963 20.5313 12.7914 20.5313 12.221 20.6948C11.5968 20.8738 11.0346 21.3319 9.91008 22.2482C8.20328 23.639 7.34988 24.3344 6.74423 24.3055C6.20019 24.2795 5.7077 23.9757 5.44033 23.5012C5.14268 22.973 5.38112 21.8983 5.85802 19.7488L5.95303 19.3206C6.21896 18.122 6.35192 17.5228 6.26941 16.9501C6.21991 16.6066 6.11428 16.2735 5.95673 15.9643C5.6941 15.4487 5.24005 15.0356 4.33193 14.2094L3.79253 13.7187C1.85012 11.9515 0.878911 11.0679 1.01266 10.224C1.04036 10.0492 1.09717 9.88025 1.18072 9.72425C1.58416 8.97099 2.90526 8.85267 5.54747 8.61604C7.10829 8.47626 7.88871 8.40636 8.51387 8.02957C8.68922 7.92388 8.85391 7.80144 9.00562 7.66396C9.5465 7.17381 9.83557 6.45317 10.4137 5.0119Z" fill="#CA8A04" stroke="#CA8A04" stroke-width="2"/>
										</svg>
										<div class="upgrade-to-pro-content">
											<div style="display:flex;flex-direction:column">
												<p style="font-weight:500"><?php esc_html_e("Unlock Premium Compliance Features", "gdpr-cookie-consent")?></p>
												<p><?php esc_html_e("Get automated scans, compliance reports, and everything you need to stay compliant.", "gdpr-cookie-consent")?></p>
											</div>
											<button
												type="button"
												class="go-to-dashboard-btn"
												onclick="window.open('<?php echo esc_url( 'https://app.wplegalpages.com/pricing' ); ?>','_blank');"
											>
												<?php esc_html_e("Upgrade to Pro", "gdpr-cookie-consent")?>
												<span>
													<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M16.6667 8.33325V3.33325H11.6667M16.6667 3.33325L10 9.99992" stroke="#FFF" stroke-width="1.66667"/>
														<path d="M9.16666 4.16675H5.83332C4.91285 4.16675 4.16666 4.91294 4.16666 5.83341V14.1667C4.16666 15.0872 4.91285 15.8334 5.83332 15.8334H14.1667C15.0871 15.8334 15.8333 15.0872 15.8333 14.1667V10.8334" stroke="#FFF" stroke-width="1.66667" stroke-linecap="round"/>
													</svg>
												</span>
											</button>
										</div>
									</div>
								<?php endif?>
								<!-- compliance setup container  -->
								<div class="compliance-setup-container">
									<div class="compliance-setup-header" id="compliance-setup-toggle">
										<div style="display:flex;gap:18px">
											<p><?php esc_html_e('Compliance Setup', 'gdpr-cookie-consent')?></p>
											<span>
												<?php
														printf(
															/* translators: %1$d: completed steps, %2$d: total steps. */
															esc_html__( '%1$d of %2$d steps completed', 'gdpr-cookie-consent' ),
															absint( $completed_steps ),
															absint( $total_steps )
														);
												?>
											</span>
										</div>
										<button type="button" class="compliance-setup-chevron" id="compliance-setup-chevron" aria-expanded="true" aria-controls="compliance-setup-content">
											<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M5 7.5L10 12.5L15 7.5" stroke="#2D2D32" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</button>
									</div>
									<div class="compliance-setup-content" id="compliance-setup-content">
										<div class="step-indicator">
											<div class="step-row">
												<div class="step <?php echo $step1_completed ? 'completed' : ''; ?>">
													<span class="step-circle">
														<?php if ( $step1_completed ) : ?>
															<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" width="16" height="16" aria-hidden="true">
																<path fill="#fff" d="M530.8 134.1C545.1 144.5 548.3 164.5 537.9 178.8L281.9 530.8C276.4 538.4 267.9 543.1 258.5 543.9C249.1 544.7 240 541.2 233.4 534.6L105.4 406.6C92.9 394.1 92.9 373.8 105.4 361.3C117.9 348.8 138.2 348.8 150.7 361.3L252.2 462.8L486.2 141.1C496.6 126.8 516.6 123.6 530.9 134z"/>
															</svg>
														<?php else : ?>
															1
														<?php endif; ?>
													</span>
												</div>
											</div>
											<?php if ( ! $is_banner_active && $is_user_connected ) : ?>
												<div class="step-line" style="height: 475px"></div>
											<?php elseif ( ($is_banner_active && !$is_user_connected) || ($is_banner_active && $is_user_connected)) : ?>
												<div class="step-line" style="height: 260px"></div>
											<?php elseif (!$is_banner_active && !$is_user_connected): ?>
												<div class="step-line" style="height: 305px"></div>
											<?php else : ?>
												<div class="step-line"></div>
											<?php endif; ?>
											<div class="step-row">
												<div class="step <?php echo $step2_completed ? 'completed' : ''; ?>">
													<span class="step-circle">
														<?php if ( $step2_completed ) : ?>
															<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" width="16" height="16" aria-hidden="true">
																<path fill="#fff" d="M530.8 134.1C545.1 144.5 548.3 164.5 537.9 178.8L281.9 530.8C276.4 538.4 267.9 543.1 258.5 543.9C249.1 544.7 240 541.2 233.4 534.6L105.4 406.6C92.9 394.1 92.9 373.8 105.4 361.3C117.9 348.8 138.2 348.8 150.7 361.3L252.2 462.8L486.2 141.1C496.6 126.8 516.6 123.6 530.9 134z"/>
															</svg>
														<?php else : ?>
															2
														<?php endif; ?>
													</span>
												</div>
											</div>
										</div>
										<div class="compliance-cards">
											<?php if(!$is_banner_active && $is_user_connected) :?>
												<div class="compliance-banner-active-card">
														<!-- card 2 -- connected -->
															<div style="display:flex;gap:14px">
															<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
																	<rect width="40" height="40" rx="20" fill="#D1FAE5"/>
																	<path d="M22.3529 17.6471L17.647 22.353" stroke="#026C3C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
																	<path d="M24.7059 21.1765L27.0588 18.8235C28.6832 17.1992 28.6832 14.5655 27.0588 12.9412C25.4345 11.3168 22.8008 11.3168 21.1765 12.9412L18.8235 15.2941M15.2941 18.8235L12.9412 21.1765C11.3168 22.8008 11.3168 25.4345 12.9412 27.0588C14.5655 28.6832 17.1992 28.6832 18.8235 27.0588L21.1765 24.7059" stroke="#026C3C" stroke-width="1.5" stroke-linecap="round"/>
															</svg>
															<div style="display:flex;flex-direction:column">
																<div class="compliance-card-header">
																	<h3><?php esc_html_e( 'Connect and scan your website for cookies', 'gdpr-cookie-consent' ); ?></h3>
																	
																</div>
																<p>
																	<?php esc_html_e(
																		'Your account is now connected. You’re all set to unlock advanced compliance features for your website.',
																		'gdpr-cookie-consent'
																	); ?>
																</p>
															</div>
																</div>
															<div class="horizontal">
																<div class="current-status-container first-step">
																	<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
																		<rect width="40" height="40" rx="20" fill="#E6F5EE"/>
																		<path d="M13 22L17.3077 25L27 14" stroke="#026C3C" stroke-width="1.5"/>
																	</svg>

																	<div style="display:flex;flex:1;width:100%;flex-direction:column">
																		<div style="display:flex;justify-content:space-between;">
																			<h3><?php esc_html_e("Current Status", "gdpr-cookie-consent")?></h3>
																			<strong>
																				<a
																					href="<?php echo esc_url( get_site_url() ); ?>"
																					target="_blank"
																					rel="noopener noreferrer"
																					class="connected-site-url"
																					style="display:flex; gap:4px; align-items:center;"
																				>
																					<?php echo esc_html( preg_replace( '#^https?://#', '', get_site_url() ) ); ?>

																					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																						<path d="M20 10V4H14M20 4L12 12" stroke="#000"/>
																						<path d="M11 5H7C5.89543 5 5 5.89543 5 7V17C5 18.1046 5.89543 19 7 19H17C18.1046 19 19 18.1046 19 17V13" stroke="#000" stroke-linecap="round"/>
																					</svg>
																				</a>
																			</strong>
																			
																		</div>
																		<p style="color:#026C3C;font-weight:500;font-size:16px;"><?php esc_html_e("Account connected", "gdpr-cookie-consent")?></p>
																		<p style="padding-top:8px"><?php esc_html_e("Your WPLP account is now connected.", "gdpr-cookie-consent")?></p>
																	</div>
																</div>
																<div class="gdpr-feature-list">
																	<p><strong><?php esc_html_e("Why do you need it?", "gdpr-cookie-consent")?></strong></p>
																	<p>
																		<span>
																			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																				<circle cx="12" cy="12" r="9" stroke="#074EA8"/>
																				<path d="M8 12L11 15L16 9" stroke="#074EA8"/>
																			</svg>
																			<?php esc_html_e("Display a GDPR/CCPA compliant cookie banner", "gdpr-cookie-consent")?>
																		</span>
																	</p>
																	<p>
																		<span>
																			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																				<circle cx="12" cy="12" r="9" stroke="#074EA8"/>
																				<path d="M8 12L11 15L16 9" stroke="#074EA8"/>
																			</svg>
																			<?php esc_html_e("Collected visitor consent", "gdpr-cookie-consent")?>
																		</span>
																	</p>
																	<p>
																		<span>
																			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																				<circle cx="12" cy="12" r="9" stroke="#074EA8"/>
																				<path d="M8 12L11 15L16 9" stroke="#074EA8"/>
																			</svg>
																			<?php esc_html_e("Connect your website to unlock cookie scanning, consent logs and compliance reports", "gdpr-cookie-consent")?>
																		</span>
																	</p>
															</div>
															</div>
															<div class="cookie-scan-container">
																<div style="display:flex;flex-direction:column">
																	<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
																		<rect width="48" height="48" rx="24" fill="#E6EDF6"/>
																		<path d="M27.7322 29.4184L28.1728 28.9779M21.5081 30.6628L21.9487 30.2223M19.0184 19.4629L19.4578 19.0224M17.7736 25.6851L18.213 25.2446M23.9977 24.4407L24.4384 24.0001M24.0014 35.2C26.8993 35.1999 29.6845 34.0777 31.7724 32.0688C33.8604 30.0599 35.0888 27.3206 35.1999 24.4257C35.2073 24.2353 35.0007 24.1146 34.8289 24.2005C31.7467 25.7523 29.8421 24.1047 30.1521 21.8933C30.1574 21.8506 30.153 21.8072 30.1392 21.7664C30.1254 21.7256 30.1025 21.6885 30.0724 21.6578C30.0422 21.627 30.0055 21.6035 29.965 21.5889C29.9245 21.5743 29.8812 21.569 29.8384 21.5735C27.1359 21.9505 25.9969 20.3116 26.4226 18.0841C26.4289 18.0461 26.4274 18.0073 26.418 17.97C26.4087 17.9327 26.3917 17.8977 26.3683 17.8672C26.3448 17.8367 26.3153 17.8114 26.2816 17.7929C26.2479 17.7743 26.2107 17.7629 26.1724 17.7593C23.6292 17.5328 23.4189 14.5984 23.8558 13.1673C23.9093 12.9906 23.7824 12.794 23.5981 12.8002C20.6637 12.9073 17.8885 14.1615 15.8695 16.293C13.8506 18.4245 12.749 21.263 12.8019 24.198C12.8547 27.133 14.0577 29.9301 16.1521 31.9875C18.2465 34.045 21.0651 35.1985 24.0014 35.2Z" stroke="#074EA8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
																	</svg>
																</div>
																<div class="cookie-scan-text">
																	<div style="gap:0;display:flex;flex-direction:column">
																		<p style="font-weight:500;font-size:16px"><?php esc_html_e("Run a cookie scan", "gdpr-cookie-consent")?></p>
																		<p style="color:#52525B"><?php esc_html_e("Scan your website to detect cookies and get a complete overview of your website’s cookie usage", "gdpr-cookie-consent")?></p>
																	</div>
																	<div class="compliance-buttons">
																		<button
																			type="button"
																			class="compliance-new-connect-btn"
																			onclick="window.location.href='<?php echo esc_url( admin_url( 'admin.php?page=gdpr-cookie-consent#cookie_manager' ) ); ?>';"
																		>
																			<?php esc_html_e( 'Run a cookie scan', 'gdpr-cookie-consent' ); ?>
																		</button>
																	</div>
																</div>
															</div>										

															<div class="connect-info-container"  id="connect-info-container">
																
																<div style="display: flex;gap: 8px">
																	<span>
																			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																				<rect width="24" height="24" rx="12" fill="#F8FAFC"/>
																				<circle cx="12" cy="12.0001" r="6.31579" stroke="#074EA8"/>
																				<path d="M12.6316 8.84203C12.6316 9.19084 12.3488 9.47361 12 9.47361C11.6512 9.47361 11.3684 9.19084 11.3684 8.84203C11.3684 8.49322 11.6512 8.21045 12 8.21045C12.3488 8.21045 12.6316 8.49322 12.6316 8.84203Z" fill="#074EA8" stroke="#074EA8" stroke-width="0.5"/>
																				<path d="M12 15.7894V10.7368" stroke="#074EA8"/>
																			</svg>
																	</span>
																	<div style="flex:1;width:100%">
																		<div style="display:flex;justify-content:space-between;align-self:start">
																			<h4>
																				<?php esc_html_e( 'What to expect after the scan ?', 'gdpr-cookie-consent' ); ?>
																			</h4>
																			<svg class="connect-info-close" data-target="connect-info-container" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																				<path d="M18 6L6 18" stroke="#3A3A41" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
																				<path d="M6 6L18 18" stroke="#3A3A41" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
																			</svg>
																		</div>
																	<p>
																		<?php esc_html_e(" You’ll get a detailed cookie report, category breakdown and recommendations to stay fully complaint.", 'gdpr-cookie-consent' ); 
																		?>
																	</p>
																</div>
															</div>
														<!-- </div> -->
														</div>
													</div>
													<?php else: ?>
											<div class="<?php echo esc_attr($is_banner_active ? 'compliance-banner-active-card' : 'compliance-banner-active-card-inactive'); ?>">
												<div class="compliance-card-header">
													<?php if ( $is_banner_active ) : ?>
														<div style="display:flex;gap:14px">
															<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
																<rect width="40" height="40" rx="20" fill="#D1FAE5"/>
																<path d="M11.231 26.747C11.3201 27.842 12.158 28.6799 13.253 28.769C14.6364 28.8813 16.844 29 20 29C23.156 29 25.3636 28.8813 26.747 28.769C27.842 28.6799 28.6799 27.842 28.769 26.747C28.8813 25.3636 29 23.156 29 20C29 16.844 28.8813 14.6364 28.769 13.253C28.6799 12.158 27.842 11.3201 26.747 11.231C25.3636 11.1187 23.156 11 20 11C16.844 11 14.6364 11.1187 13.253 11.231C12.158 11.3201 11.3201 12.158 11.231 13.253C11.1187 14.6364 11 16.844 11 20C11 23.156 11.1187 25.3636 11.231 26.747Z" stroke="#026C3C" stroke-width="1.5" stroke-linejoin="round"/>
																<path d="M11.2144 15.5H28.7858" stroke="#026C3C" stroke-width="1.5" stroke-linecap="round"/>
																<path d="M14 13.3572H14.8571M17.4286 13.3572H18.2857" stroke="#026C3C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
															</svg>
															<div style="display:flex;flex-direction:column">
																<div style="display:flex">
																	<h3><?php esc_html_e( 'Your Cookie Banner is Active', 'gdpr-cookie-consent' ); ?></h3>
																	<span class="status-badge">
																		<?php esc_html_e( 'Active', 'gdpr-cookie-consent' ); ?>
																	</span>
																</div>
																<p>
																	<?php esc_html_e(
																		'Great! Your website is now displaying a cookie consent banner to visitors.',
																		'gdpr-cookie-consent'
																	); ?>
																</p>
															</div>
														</div>
													<?php else : ?>
														<div style="display:flex;gap:14px">
															<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
																<rect width="40" height="40" rx="20" fill="#E6EDF6"/>
																<path d="M11.231 26.747C11.3201 27.842 12.158 28.6799 13.253 28.769C14.6364 28.8813 16.844 29 20 29C23.156 29 25.3636 28.8813 26.747 28.769C27.842 28.6799 28.6799 27.842 28.769 26.747C28.8813 25.3636 29 23.156 29 20C29 16.844 28.8813 14.6364 28.769 13.253C28.6799 12.158 27.842 11.3201 26.747 11.231C25.3636 11.1187 23.156 11 20 11C16.844 11 14.6364 11.1187 13.253 11.231C12.158 11.3201 11.3201 12.158 11.231 13.253C11.1187 14.6364 11 16.844 11 20C11 23.156 11.1187 25.3636 11.231 26.747Z" stroke="#074EA8" stroke-width="1.5" stroke-linejoin="round"/>
																<path d="M11.2144 15.5H28.7858" stroke="#074EA8" stroke-width="1.5" stroke-linecap="round"/>
																<path d="M14 13.3572H14.8571M17.4286 13.3572H18.2857" stroke="#074EA8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
															</svg>
															<div style="display:flex;flex-direction:column">
																<div style="display:flex">
																<h3><?php esc_html_e( 'Activate your Cookie Banner', 'gdpr-cookie-consent' ); ?></h3>
																
															</div>
															<p>
																<?php esc_html_e(
																	'Set up your cookie consent banner to get started.',
																	'gdpr-cookie-consent'
																); ?>
															</p>
														</div>
														</div>
													<?php endif?>
												</div>
												<?php if ( $is_banner_active ) : ?>
												<div class="horizontal">
													<div class="current-status-container first-step">
														<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
															<rect width="40" height="40" rx="20" fill="#E6F5EE"/>
															<path d="M13 22L17.3077 25L27 14" stroke="#026C3C" stroke-width="1.5"/>
														</svg>

														<div>
															<h3><?php esc_html_e("Current Status", "gdpr-cookie-consent")?></h3>
															<p style="color:#026C3C;font-weight:500;font-size:16px;"><?php esc_html_e("Cookie Banner is Active", "gdpr-cookie-consent")?></p>
															<p style="padding-top:8px"><?php esc_html_e("Your website is protected with a GDPR compliant cookie banner", "gdpr-cookie-consent")?></p>
														</div>
													</div>
													
												</div>
												<?php else: ?>
													<div class="horizontal">
														<div class="current-status-container first-step">
															<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
																<rect width="40" height="40" rx="20" fill="#E6EDF6"/>
																<path d="M29.381 18.4762H27.8095V14.2857C27.8095 13.73 27.5888 13.1971 27.1958 12.8042C26.8029 12.4112 26.27 12.1905 25.7143 12.1905H21.5238V10.619C21.5238 9.92443 21.2479 9.25827 20.7567 8.7671C20.2655 8.27593 19.5994 8 18.9048 8C18.2101 8 17.544 8.27593 17.0528 8.7671C16.5616 9.25827 16.2857 9.92443 16.2857 10.619V12.1905H12.0952C11.5395 12.1905 11.0066 12.4112 10.6137 12.8042C10.2207 13.1971 10 13.73 10 14.2857V18.2667H11.5714C13.1429 18.2667 14.4 19.5238 14.4 21.0952C14.4 22.6667 13.1429 23.9238 11.5714 23.9238H10V27.9048C10 28.4605 10.2207 28.9934 10.6137 29.3863C11.0066 29.7793 11.5395 30 12.0952 30H16.0762V28.4286C16.0762 26.8571 17.3333 25.6 18.9048 25.6C20.4762 25.6 21.7333 26.8571 21.7333 28.4286V30H25.7143C26.27 30 26.8029 29.7793 27.1958 29.3863C27.5888 28.9934 27.8095 28.4605 27.8095 27.9048V23.7143H29.381C30.0756 23.7143 30.7417 23.4384 31.2329 22.9472C31.7241 22.456 32 21.7899 32 21.0952C32 20.4006 31.7241 19.7345 31.2329 19.2433C30.7417 18.7521 30.0756 18.4762 29.381 18.4762Z" fill="#074EA8"/>
															</svg>

															<div>
																<p style="color:#3A3A41;font-weight:500;font-size:16px;padding-top:0"><?php esc_html_e("Cookie Banner is currently inactive", "gdpr-cookie-consent")?></p>
																<p style="padding-top:8px"><?php esc_html_e("Your website doesn't have an active cookie banner. Activate it to start collecting user consent and stay compliant.", "gdpr-cookie-consent")?></p>
															</div>
														</div>
														<div class="gdpr-feature-list" style="margin-top:0">
																<p><strong><?php esc_html_e("Why activate it?", "gdpr-cookie-consent")?></strong></p>
																<p>
																	<span>
																		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<circle cx="12" cy="12" r="9" stroke="#074EA8"/>
																			<path d="M8 12L11 15L16 9" stroke="#074EA8"/>
																		</svg>
																		<?php esc_html_e("Collect valid consent from visitors", "gdpr-cookie-consent")?>
																	</span>
																</p>
																<p>
																	<span>
																		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<circle cx="12" cy="12" r="9" stroke="#074EA8"/>
																			<path d="M8 12L11 15L16 9" stroke="#074EA8"/>
																		</svg>
																		<?php esc_html_e("Stay compliant with GDPR, CCPA and ePrivacy", "gdpr-cookie-consent")?>
																	</span>
																</p>
																<p>
																	<span>
																		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<circle cx="12" cy="12" r="9" stroke="#074EA8"/>
																			<path d="M8 12L11 15L16 9" stroke="#074EA8"/>
																		</svg>
																		<?php esc_html_e("Avoid compliance risks and potential penalties", "gdpr-cookie-consent")?>
																	</span>
																</p>
														</div>
													</div>
												<button type="button"
													class="customize-banner-btn enable-banner-btn"
													style="margin-top: 12px"
												>
													<?php esc_html_e('Enable Cookie Banner', 'gdpr-cookie-consent' ); ?>
												</button>
												<?php endif ?>
											</div>
											<?php endif ?>
											<?php if ( !$is_banner_active && !$is_user_connected ) : ?>
												<div class="compliance-connect-card">
													<div class="compliance-card-header disabled">
														<h3><?php esc_html_e( 'Connect and scan your website for cookies', 'gdpr-cookie-consent' ); ?></h3>
														
													</div>
													<p>
														<?php esc_html_e(
															'Connect your website to the WPLP web app to unlock advanced compliance features',
															'gdpr-cookie-consent'
														); ?>
													</p>
													<div class="compliance-buttons">
													<button
														type="button"
														class="compliance-new-connect-btn disabled disabled gdpr-start-auth" disabled>
														<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
															<circle cx="10" cy="5.83333" r="3.33333" stroke="white" stroke-linecap="round"/>
															<path d="M4.3832 15.558C4.91397 13.0367 7.42344 11.6667 10 11.6667C12.5766 11.6667 15.086 13.0367 15.6168 15.558C15.6816 15.866 15.7352 16.1821 15.7728 16.5023C15.8372 17.0508 15.3856 17.5001 14.8333 17.5001H5.16667C4.61439 17.5001 4.16283 17.0508 4.22725 16.5023C4.26486 16.1821 4.31837 15.866 4.3832 15.558Z" stroke="white" stroke-linecap="round"/>
														</svg>
														<?php esc_html_e( 'Connect with a new account', 'gdpr-cookie-consent' ); ?>
													</button>
													<button
														type="button"
														class="compliance-connect-btn disabled gdpr-dashboard-start-auth" disabled>
														<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path d="M16.6667 8.33325V3.33325H11.6667M16.6667 3.33325L10 9.99992" stroke="#A1A1AA" stroke-width="1.66667"/>
															<path d="M9.16666 4.16675H5.83332C4.91285 4.16675 4.16666 4.91294 4.16666 5.83341V14.1667C4.16666 15.0872 4.91285 15.8334 5.83332 15.8334H14.1667C15.0871 15.8334 15.8333 15.0872 15.8333 14.1667V10.8334" stroke="#A1A1AA" stroke-width="1.66667" stroke-linecap="round"/>
														</svg>
														<?php esc_html_e( 'Connect with an existing account', 'gdpr-cookie-consent' ); ?>
													</button>
													</div>
												</div>
											<?php elseif ( !$is_user_connected ) : ?>
												<div class="compliance-connect-card">
													<div class="compliance-card-header">
														<h3><?php esc_html_e( 'Connect and scan your website for cookies', 'gdpr-cookie-consent' ); ?></h3>
														
													</div>
													<p>
														<?php esc_html_e(
															'Connect your website to the WPLP web app to unlock advanced compliance features',
															'gdpr-cookie-consent'
														); ?>
													</p>
													<div class="compliance-buttons">
													<button
														type="button"
														class="compliance-new-connect-btn gdpr-start-auth">
														<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
															<circle cx="10" cy="5.83333" r="3.33333" stroke="white" stroke-linecap="round"/>
															<path d="M4.3832 15.558C4.91397 13.0367 7.42344 11.6667 10 11.6667C12.5766 11.6667 15.086 13.0367 15.6168 15.558C15.6816 15.866 15.7352 16.1821 15.7728 16.5023C15.8372 17.0508 15.3856 17.5001 14.8333 17.5001H5.16667C4.61439 17.5001 4.16283 17.0508 4.22725 16.5023C4.26486 16.1821 4.31837 15.866 4.3832 15.558Z" stroke="white" stroke-linecap="round"/>
														</svg>
														<?php esc_html_e( 'Connect with a new account', 'gdpr-cookie-consent' ); ?>
													</button>
													<button
														type="button"
														class="compliance-connect-btn gdpr-dashboard-start-auth">
														<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path d="M16.6667 8.33325V3.33325H11.6667M16.6667 3.33325L10 9.99992" stroke="#074EA8" stroke-width="1.66667"/>
															<path d="M9.16666 4.16675H5.83332C4.91285 4.16675 4.16666 4.91294 4.16666 5.83341V14.1667C4.16666 15.0872 4.91285 15.8334 5.83332 15.8334H14.1667C15.0871 15.8334 15.8333 15.0872 15.8333 14.1667V10.8334" stroke="#074EA8" stroke-width="1.66667" stroke-linecap="round"/>
														</svg>
														<?php esc_html_e( 'Connect with an existing account', 'gdpr-cookie-consent' ); ?>
													</button>
													</div>
													<div class="connect-info-container" id="connect-info-container">
														<div style="display: flex;gap: 8px">
															<span>
																	<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																		<rect width="24" height="24" rx="12" fill="#F8FAFC"/>
																		<circle cx="12" cy="12.0001" r="6.31579" stroke="#074EA8"/>
																		<path d="M12.6316 8.84203C12.6316 9.19084 12.3488 9.47361 12 9.47361C11.6512 9.47361 11.3684 9.19084 11.3684 8.84203C11.3684 8.49322 11.6512 8.21045 12 8.21045C12.3488 8.21045 12.6316 8.49322 12.6316 8.84203Z" fill="#074EA8" stroke="#074EA8" stroke-width="0.5"/>
																		<path d="M12 15.7894V10.7368" stroke="#074EA8"/>
																	</svg>
															</span>
															<div style="flex:1;width:100%">
																<div style="display:flex;justify-content:space-between;align-self:start">
																	<h4>
																		<?php esc_html_e( 'Connecting your website: What to expect', 'gdpr-cookie-consent' ); ?>
																	</h4>
																	<svg class="connect-info-close" data-target="connect-info-container" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																		<path d="M18 6L6 18" stroke="#3A3A41" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
																		<path d="M6 6L18 18" stroke="#3A3A41" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
																	</svg>
																</div>
																<p>
																	<?php esc_html_e(" When you connect your website to the WPLP Compliance Platform, it securely communicates with our cloud services to provide automatic cookie scanning, compliance reports, consent log management, and other advanced compliance features.", 'gdpr-cookie-consent' ); 
																	?>
																</p>
																<p>
																	<?php esc_html_e("Your website will continue to use the plugin locally, and you can disconnect at any time. If you disconnect, cloud-powered features will be disabled while the plugin's free functionality remains available.", "gdpr-cookie-consent")?>
																</p>
															</div>
													</div>
												</div>
												</div>
												<?php elseif ( !$is_banner_active && $is_user_connected ) : ?>
													<div class="compliance-banner-active-card-inactive">
														<div class="compliance-card-header">
															<h3><?php esc_html_e( 'Your Cookie Banner is Not Active', 'gdpr-cookie-consent' ); ?></h3>
															<span class="status-badge-inactive">
																<?php esc_html_e( 'Not Active', 'gdpr-cookie-consent' ); ?>
															</span>
														</div>	
														<p>
															<?php esc_html_e(
																'Set up your cookie consent banner to get started.',
																'gdpr-cookie-consent'
															); ?>
														</p>
														<div class="horizontal">
														<div class="current-status-container first-step">
															<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
																<rect width="40" height="40" rx="20" fill="#E6EDF6"/>
																<path d="M29.381 18.4762H27.8095V14.2857C27.8095 13.73 27.5888 13.1971 27.1958 12.8042C26.8029 12.4112 26.27 12.1905 25.7143 12.1905H21.5238V10.619C21.5238 9.92443 21.2479 9.25827 20.7567 8.7671C20.2655 8.27593 19.5994 8 18.9048 8C18.2101 8 17.544 8.27593 17.0528 8.7671C16.5616 9.25827 16.2857 9.92443 16.2857 10.619V12.1905H12.0952C11.5395 12.1905 11.0066 12.4112 10.6137 12.8042C10.2207 13.1971 10 13.73 10 14.2857V18.2667H11.5714C13.1429 18.2667 14.4 19.5238 14.4 21.0952C14.4 22.6667 13.1429 23.9238 11.5714 23.9238H10V27.9048C10 28.4605 10.2207 28.9934 10.6137 29.3863C11.0066 29.7793 11.5395 30 12.0952 30H16.0762V28.4286C16.0762 26.8571 17.3333 25.6 18.9048 25.6C20.4762 25.6 21.7333 26.8571 21.7333 28.4286V30H25.7143C26.27 30 26.8029 29.7793 27.1958 29.3863C27.5888 28.9934 27.8095 28.4605 27.8095 27.9048V23.7143H29.381C30.0756 23.7143 30.7417 23.4384 31.2329 22.9472C31.7241 22.456 32 21.7899 32 21.0952C32 20.4006 31.7241 19.7345 31.2329 19.2433C30.7417 18.7521 30.0756 18.4762 29.381 18.4762Z" fill="#074EA8"/>
															</svg>

															<div>
																<p style="color:#3A3A41;font-weight:500;font-size:16px;padding-top:0"><?php esc_html_e("Cookie Banner is currently inactive", "gdpr-cookie-consent")?></p>
																<p style="padding-top:8px"><?php esc_html_e("Your website doesn't have an active cookie banner. Activate it to start collecting user consent and stay compliant.", "gdpr-cookie-consent")?></p>
															</div>
														</div>
														<div class="gdpr-feature-list" style="margin-top:0">
																<p><strong><?php esc_html_e("Why activate it?", "gdpr-cookie-consent")?></strong></p>
																<p>
																	<span>
																		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<circle cx="12" cy="12" r="9" stroke="#074EA8"/>
																			<path d="M8 12L11 15L16 9" stroke="#074EA8"/>
																		</svg>
																		<?php esc_html_e("Collect valid consent from visitors", "gdpr-cookie-consent")?>
																	</span>
																</p>
																<p>
																	<span>
																		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<circle cx="12" cy="12" r="9" stroke="#074EA8"/>
																			<path d="M8 12L11 15L16 9" stroke="#074EA8"/>
																		</svg>
																		<?php esc_html_e("Stay compliant with GDPR, CCPA and ePrivacy", "gdpr-cookie-consent")?>
																	</span>
																</p>
																<p>
																	<span>
																		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<circle cx="12" cy="12" r="9" stroke="#074EA8"/>
																			<path d="M8 12L11 15L16 9" stroke="#074EA8"/>
																		</svg>
																		<?php esc_html_e("Avoid compliance risks and potential penalties", "gdpr-cookie-consent")?>
																	</span>
																</p>
														</div>
													</div>
														<button type="button"
															class="customize-banner-btn enable-banner-btn"
															style="margin-top:12px"
														>
															<?php esc_html_e('Enable Cookie Banner', 'gdpr-cookie-consent' ); ?>
														</button>
													</div>
												<?php else : ?>
													<div class="compliance-banner-active-card">
														<!-- card 2 -- connected -->
														 <div style="display:flex;gap:14px">
															<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
																	<rect width="40" height="40" rx="20" fill="#D1FAE5"/>
																	<path d="M22.3529 17.6471L17.647 22.353" stroke="#026C3C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
																	<path d="M24.7059 21.1765L27.0588 18.8235C28.6832 17.1992 28.6832 14.5655 27.0588 12.9412C25.4345 11.3168 22.8008 11.3168 21.1765 12.9412L18.8235 15.2941M15.2941 18.8235L12.9412 21.1765C11.3168 22.8008 11.3168 25.4345 12.9412 27.0588C14.5655 28.6832 17.1992 28.6832 18.8235 27.0588L21.1765 24.7059" stroke="#026C3C" stroke-width="1.5" stroke-linecap="round"/>
															</svg>
															<div style="display:flex;flex-direction:column">
																<div class="compliance-card-header">
																	<h3><?php esc_html_e( 'Connect and scan your website for cookies', 'gdpr-cookie-consent' ); ?></h3>
																	
																</div>
																<p>
																	<?php esc_html_e(
																		'Your account is now connected. You’re all set to unlock advanced compliance features for your website.',
																		'gdpr-cookie-consent'
																	); ?>
																</p>
															</div>
																</div>
															<div class="horizontal">
																<div class="current-status-container first-step">
																	<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
																		<rect width="40" height="40" rx="20" fill="#E6F5EE"/>
																		<path d="M13 22L17.3077 25L27 14" stroke="#026C3C" stroke-width="1.5"/>
																	</svg>

																	<div style="display:flex;flex:1;width:100%;flex-direction:column">
																		<div style="display:flex;justify-content:space-between;">
																			<h3><?php esc_html_e("Current Status", "gdpr-cookie-consent")?></h3>
																			<strong>
																				<a
																					href="<?php echo esc_url( get_site_url() ); ?>"
																					target="_blank"
																					rel="noopener noreferrer"
																					class="connected-site-url"
																					style="display:flex; gap:4px; align-items:center;"
																				>
																					<?php echo esc_html( preg_replace( '#^https?://#', '', get_site_url() ) ); ?>

																					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																						<path d="M20 10V4H14M20 4L12 12" stroke="#000"/>
																						<path d="M11 5H7C5.89543 5 5 5.89543 5 7V17C5 18.1046 5.89543 19 7 19H17C18.1046 19 19 18.1046 19 17V13" stroke="#000" stroke-linecap="round"/>
																					</svg>
																				</a>
																			</strong>
																			
																		</div>
																		<p style="color:#026C3C;font-weight:500;font-size:16px;"><?php esc_html_e("Account connected", "gdpr-cookie-consent")?></p>
																		<p style="padding-top:8px"><?php esc_html_e("Your WPLP account is now connected.", "gdpr-cookie-consent")?></p>
																	</div>
																</div>
																<div class="gdpr-feature-list">
																	<p><strong><?php esc_html_e("Why do you need it?", "gdpr-cookie-consent")?></strong></p>
																	<p>
																		<span>
																			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																				<circle cx="12" cy="12" r="9" stroke="#074EA8"/>
																				<path d="M8 12L11 15L16 9" stroke="#074EA8"/>
																			</svg>
																			<?php esc_html_e("Display a GDPR/CCPA compliant cookie banner", "gdpr-cookie-consent")?>
																		</span>
																	</p>
																	<p>
																		<span>
																			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																				<circle cx="12" cy="12" r="9" stroke="#074EA8"/>
																				<path d="M8 12L11 15L16 9" stroke="#074EA8"/>
																			</svg>
																			<?php esc_html_e("Collected visitor consent", "gdpr-cookie-consent")?>
																		</span>
																	</p>
																	<p>
																		<span>
																			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																				<circle cx="12" cy="12" r="9" stroke="#074EA8"/>
																				<path d="M8 12L11 15L16 9" stroke="#074EA8"/>
																			</svg>
																			<?php esc_html_e("Connect your website to unlock cookie scanning, consent logs and compliance reports", "gdpr-cookie-consent")?>
																		</span>
																	</p>
															</div>
															</div>
															<div class="cookie-scan-container">
																<div style="display:flex;flex-direction:column">
																	<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
																		<rect width="48" height="48" rx="24" fill="#E6EDF6"/>
																		<path d="M27.7322 29.4184L28.1728 28.9779M21.5081 30.6628L21.9487 30.2223M19.0184 19.4629L19.4578 19.0224M17.7736 25.6851L18.213 25.2446M23.9977 24.4407L24.4384 24.0001M24.0014 35.2C26.8993 35.1999 29.6845 34.0777 31.7724 32.0688C33.8604 30.0599 35.0888 27.3206 35.1999 24.4257C35.2073 24.2353 35.0007 24.1146 34.8289 24.2005C31.7467 25.7523 29.8421 24.1047 30.1521 21.8933C30.1574 21.8506 30.153 21.8072 30.1392 21.7664C30.1254 21.7256 30.1025 21.6885 30.0724 21.6578C30.0422 21.627 30.0055 21.6035 29.965 21.5889C29.9245 21.5743 29.8812 21.569 29.8384 21.5735C27.1359 21.9505 25.9969 20.3116 26.4226 18.0841C26.4289 18.0461 26.4274 18.0073 26.418 17.97C26.4087 17.9327 26.3917 17.8977 26.3683 17.8672C26.3448 17.8367 26.3153 17.8114 26.2816 17.7929C26.2479 17.7743 26.2107 17.7629 26.1724 17.7593C23.6292 17.5328 23.4189 14.5984 23.8558 13.1673C23.9093 12.9906 23.7824 12.794 23.5981 12.8002C20.6637 12.9073 17.8885 14.1615 15.8695 16.293C13.8506 18.4245 12.749 21.263 12.8019 24.198C12.8547 27.133 14.0577 29.9301 16.1521 31.9875C18.2465 34.045 21.0651 35.1985 24.0014 35.2Z" stroke="#074EA8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
																	</svg>
																</div>
																<div class="cookie-scan-text">
																	<div style="gap:0;display:flex;flex-direction:column">
																		<p style="font-weight:500;font-size:16px"><?php esc_html_e("Run a cookie scan", "gdpr-cookie-consent")?></p>
																		<p style="color:#52525B"><?php esc_html_e("Scan your website to detect cookies and get a complete overview of your website’s cookie usage", "gdpr-cookie-consent")?></p>
																	</div>
																	<div class="compliance-buttons">
																		<button
																			type="button"
																			class="compliance-new-connect-btn"
																			onclick="window.location.href='<?php echo esc_url( admin_url( 'admin.php?page=gdpr-cookie-consent#cookie_manager' ) ); ?>';"
																		>
																			<?php esc_html_e( 'Run a cookie scan', 'gdpr-cookie-consent' ); ?>
																		</button>
																	</div>
																</div>
															</div>										

															<div class="connect-info-container"  id="connect-info-container">
																
																<div style="display: flex;gap: 8px">
																	<span>
																			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																				<rect width="24" height="24" rx="12" fill="#F8FAFC"/>
																				<circle cx="12" cy="12.0001" r="6.31579" stroke="#074EA8"/>
																				<path d="M12.6316 8.84203C12.6316 9.19084 12.3488 9.47361 12 9.47361C11.6512 9.47361 11.3684 9.19084 11.3684 8.84203C11.3684 8.49322 11.6512 8.21045 12 8.21045C12.3488 8.21045 12.6316 8.49322 12.6316 8.84203Z" fill="#074EA8" stroke="#074EA8" stroke-width="0.5"/>
																				<path d="M12 15.7894V10.7368" stroke="#074EA8"/>
																			</svg>
																	</span>
																	<div style="flex:1;width:100%">
																		<div style="display:flex;justify-content:space-between;align-self:start">
																			<h4>
																				<?php esc_html_e( 'What to expect after the scan ?', 'gdpr-cookie-consent' ); ?>
																			</h4>
																			<svg class="connect-info-close" data-target="connect-info-container" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																				<path d="M18 6L6 18" stroke="#3A3A41" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
																				<path d="M6 6L18 18" stroke="#3A3A41" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
																			</svg>
																		</div>
																	<p>
																		<?php esc_html_e(" You’ll get a detailed cookie report, category breakdown and recommendations to stay fully complaint.", 'gdpr-cookie-consent' ); 
																		?>
																	</p>
																</div>
															</div>
														</div>
													</div>
												<?php endif; ?>
										</div>
									</div>
								</div>
								
								<!-- get more insights card -->
								<?php if( $is_user_connected): ?>
									<div class="get-more-insights-card">
										<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/get-more-insights.png'; ?>" alt="WP Get more insights image">
										<div class="get-more-insights-content">
											<h3><?php esc_html_e("Get more insights in the WPLP Compliance Platform", "gdpr-cookie-consent")?></h3>
											<p><?php esc_html_e("Your website is connected.
												Go to the WPLP web app to access detailed cookie data, consent reports, AI-powered compliance setup and more.", "gdpr-cookie-consent")?>
											</p>
											<div class="feature-container">
												<div class="features">
													<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
														<rect width="30" height="30" rx="15" fill="#E6EDF6"/>
														<path d="M17.3326 18.3865L17.608 18.1111M13.4425 19.1642L13.7179 18.8889M11.8865 12.1643L12.1611 11.889M11.1085 16.0531L11.3831 15.7778M14.9985 15.2754L15.274 15M15.0009 22C16.812 21.9999 18.5528 21.2985 19.8577 20.0429C21.1627 18.7874 21.9305 17.0753 21.9999 15.266C22.0045 15.147 21.8754 15.0716 21.768 15.1253C19.8417 16.0951 18.6513 15.0654 18.845 13.6833C18.8483 13.6566 18.8456 13.6295 18.8369 13.604C18.8283 13.5785 18.8141 13.5553 18.7952 13.5361C18.7764 13.5169 18.7534 13.5022 18.7281 13.493C18.7028 13.4839 18.6757 13.4806 18.649 13.4834C16.9599 13.7191 16.248 12.6947 16.5141 11.3025C16.5181 11.2788 16.5171 11.2545 16.5112 11.2312C16.5054 11.2079 16.4948 11.186 16.4801 11.167C16.4655 11.1479 16.447 11.1321 16.426 11.1205C16.4049 11.1089 16.3817 11.1018 16.3577 11.0995C14.7682 10.958 14.6368 9.12397 14.9098 8.22953C14.9433 8.11909 14.8639 7.9962 14.7488 8.00009C12.9148 8.06705 11.1803 8.85092 9.91843 10.1831C8.65656 11.5153 7.96811 13.2894 8.00114 15.1237C8.03416 16.9581 8.78604 18.7063 10.0951 19.9922C11.4041 21.2781 13.1656 21.9991 15.0009 22Z" stroke="#074EA8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
													</svg>
													<div>
														<h6><?php esc_html_e("Cookie Details", "gdpr-cookie-consent")?></h6>
														<p><?php esc_html_e("View all cookies detected on your website.", "gdpr-cookie-consent")?></p>
													</div>
												</div>
												<div class="features">
													<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
														<rect width="30" height="30" rx="15" fill="#E6EDF6"/>
														<path d="M15.8787 8.25H12.75C11.3358 8.25 10.6287 8.25 10.1893 8.68934C9.75 9.12868 9.75 9.83579 9.75 11.25V18.75C9.75 20.1642 9.75 20.8713 10.1893 21.3107C10.6287 21.75 11.3358 21.75 12.75 21.75H17.25C18.6642 21.75 19.3713 21.75 19.8107 21.3107C20.25 20.8713 20.25 20.1642 20.25 18.75V12.6213C20.25 12.3148 20.25 12.1615 20.1929 12.0236C20.1358 11.8858 20.0274 11.7774 19.8107 11.5607L16.9393 8.68934C16.7226 8.47257 16.6142 8.36418 16.4764 8.30709C16.3385 8.25 16.1852 8.25 15.8787 8.25Z" stroke="#074EA8" stroke-width="1.5"/>
														<path d="M12.75 15.75L17.25 15.75" stroke="#074EA8" stroke-width="1.5" stroke-linecap="round"/>
														<path d="M12.75 18.75L15.75 18.75" stroke="#074EA8" stroke-width="1.5" stroke-linecap="round"/>
														<path d="M15.75 8.25V11.25C15.75 11.9571 15.75 12.3107 15.9697 12.5303C16.1893 12.75 16.5429 12.75 17.25 12.75H20.25" stroke="#074EA8" stroke-width="1.5"/>
													</svg>
													<div>
														<h6><?php esc_html_e("Consent Reports", "gdpr-cookie-consent")?></h6>
														<p><?php esc_html_e("Access consent logs & reports", "gdpr-cookie-consent")?></p>
													</div>
												</div>
												<div class="features">
													<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
														<rect width="30" height="30" rx="15" fill="#E6EDF6"/>
														<path d="M18 22C19.2 18.322 20.526 16.995 24 16C20.526 15.005 19.2 13.678 18 10C16.8 13.678 15.474 15.005 12 16C15.474 16.995 16.8 18.322 18 22ZM10 13C10.6 11.16 11.263 10.497 13 10C11.263 9.503 10.6 8.84 10 7C9.4 8.84 8.737 9.503 7 10C8.737 10.497 9.4 11.16 10 13ZM11.5 23C11.8 22.08 12.131 21.749 13 21.5C12.131 21.251 11.8 20.92 11.5 20C11.2 20.92 10.869 21.251 10 21.5C10.869 21.749 11.2 22.08 11.5 23Z" stroke="#074EA8" stroke-width="1.5" stroke-linejoin="round"/>
													</svg>
													<div>
														<h6><?php esc_html_e("AI Powered Setup", "gdpr-cookie-consent")?></h6>
														<p><?php esc_html_e("Get AI compliance recommendations", "gdpr-cookie-consent")?></p>
													</div>
												</div>
												<div class="features">
													<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
														<rect width="30" height="30" rx="15" fill="#E6EDF6"/>
														<rect x="8" y="8" width="5" height="5" rx="1.25" stroke="#074EA8" stroke-width="1.5" stroke-linecap="round"/>
														<rect x="8" y="17" width="5" height="5" rx="1.25" stroke="#074EA8" stroke-width="1.5" stroke-linecap="round"/>
														<rect x="17" y="8" width="5" height="5" rx="1.25" stroke="#074EA8" stroke-width="1.5" stroke-linecap="round"/>
														<path d="M19 17V22" stroke="#074EA8" stroke-width="1.5" stroke-linecap="round"/>
														<path d="M22 19L17 19" stroke="#074EA8" stroke-width="1.5" stroke-linecap="round"/>
													</svg>
													<div>
														<h6><?php esc_html_e("And more", "gdpr-cookie-consent")?></h6>
														<p><?php esc_html_e("Explore advanced compliance features", "gdpr-cookie-consent")?></p>
													</div>
												</div>
											</div>
											<button
												type="button"
												class="go-to-dashboard-btn"
												onclick="window.open('<?php echo esc_url( 'https://app.wplegalpages.com/app/' ); ?>','_blank');"
											>
												<?php esc_html_e("Go to WPLP Compliance Platform", "gdpr-cookie-consent")?>
												<span>
													<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M16.6667 8.33325V3.33325H11.6667M16.6667 3.33325L10 9.99992" stroke="#FFF" stroke-width="1.66667"/>
														<path d="M9.16666 4.16675H5.83332C4.91285 4.16675 4.16666 4.91294 4.16666 5.83341V14.1667C4.16666 15.0872 4.91285 15.8334 5.83332 15.8334H14.1667C15.0871 15.8334 15.8333 15.0872 15.8333 14.1667V10.8334" stroke="#FFF" stroke-width="1.66667" stroke-linecap="round"/>
													</svg>
												</span>
											</button>
										</div>
									</div>
								<?php endif?>
								<!-- features heading -->
								<div class="compliance-features-header">
									<h3 class="compliance-features-heading">
										<?php esc_html_e('Set up and manage compliance features', 'gdpr-cookie-consent');?>
									</h3>
									<p class="compliance-features-subheading">
										<?php esc_html_e('You can configure basic compliance settings now. Advanced features unlock after connecting your account.', 'gdpr-cookie-consent')?>
									</p>				
									<!-- GDPR & LP cards -->
									<div class="gdpr-lp-cards">
										<div class="gdpr-card">
											<div style="display:flex; gap:14px; flex: 1">
												<?php if ( $is_banner_active ) : ?>

												<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
														<rect width="40" height="40" rx="10" fill="#E6F5EE"/>
														<path d="M19.2119 11.25C19.715 11.0344 20.285 11.0344 20.7881 11.25L26.7021 13.7842C27.5214 14.1353 28.01 14.9867 27.8994 15.8711L27.2861 20.7744C27.066 22.5356 26.1848 24.1479 24.8213 25.2842L21.2803 28.2344C20.5386 28.8525 19.4614 28.8525 18.7197 28.2344L15.1787 25.2842C13.8152 24.1479 12.934 22.5356 12.7139 20.7744L12.1006 15.8711C11.99 14.9867 12.4786 14.1353 13.2979 13.7842L19.2119 11.25Z" stroke="#15803D" stroke-width="2" stroke-linecap="round"/>
														<path d="M17 20L19.5687 22.5687C19.7918 22.7918 20.1633 22.7551 20.3383 22.4925L24 17" stroke="#15803D" stroke-width="2" stroke-linecap="round"/>
												</svg>
												<?php else: ?>
												<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect width="40" height="40" rx="10" fill="#e5e5e5"/>
													<path d="M19.2119 11.25C19.715 11.0344 20.285 11.0344 20.7881 11.25L26.7021 13.7842C27.5214 14.1353 28.01 14.9867 27.8994 15.8711L27.2861 20.7744C27.066 22.5356 26.1848 24.1479 24.8213 25.2842L21.2803 28.2344C20.5386 28.8525 19.4614 28.8525 18.7197 28.2344L15.1787 25.2842C13.8152 24.1479 12.934 22.5356 12.7139 20.7744L12.1006 15.8711C11.99 14.9867 12.4786 14.1353 13.2979 13.7842L19.2119 11.25Z" stroke="#3A3A41" stroke-width="2" stroke-linecap="round"/>
													<path d="M17 20L19.5687 22.5687C19.7918 22.7918 20.1633 22.7551 20.3383 22.4925L24 17" stroke="#3A3A41" stroke-width="2" stroke-linecap="round"/>
												</svg>
													<?php endif ?>
												<div style="display:flex; justify-content:space-between;" class="compliance-card-header">
													<div style="display:flex; flex-direction:column;gap:8px">
														<h3 class=""><?php esc_html_e("Cookie Banner", 'gdpr-cookie-consent')?>
															<?php if ( $is_banner_active ) : ?>
																<span class="status-badge">
																		<?php esc_html_e( 'Active', 'gdpr-cookie-consent' ); ?>
																</span>
																<?php else : ?>
																	<span class="status-badge-inactive">
																	<?php esc_html_e( 'Not active', 'gdpr-cookie-consent' ); ?>
																</span>
																<?php endif ?>
														</h3>
														<?php if ( $is_banner_active ) : ?>
															<p style="font-size:14px"><?php esc_html_e("Your Cookie banner is live on your website", 'gdpr-cookie-consent')?></p>
															<?php else: ?>
															<p style="font-size:14px"><?php esc_html_e("Your Cookie banner is not active on your website", 'gdpr-cookie-consent')?></p>
															<?php endif  ?>
														<div class="card-features">
															<p>
																<svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
																	<path d="M8.5 3.02018L8.85355 2.66663L9.20711 3.02018L8.85355 3.37374L8.5 3.02018ZM0.5 3.52018C0.223858 3.52018 0 3.29632 0 3.02018C0 2.74404 0.223858 2.52018 0.5 2.52018V3.02018V3.52018ZM5.83333 0.353516L6.18689 -3.77595e-05L8.85355 2.66663L8.5 3.02018L8.14645 3.37374L5.47978 0.707069L5.83333 0.353516ZM8.5 3.02018L8.85355 3.37374L6.18689 6.0404L5.83333 5.68685L5.47978 5.3333L8.14645 2.66663L8.5 3.02018ZM8.5 3.02018V3.52018H0.5V3.02018V2.52018H8.5V3.02018Z" fill="#074EA8"/>
																</svg>
																<?php esc_html_e("Customize banner design", 'gdpr-cookie-consent')?>
															</p>
															<p>
																<svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
																	<path d="M8.5 3.02018L8.85355 2.66663L9.20711 3.02018L8.85355 3.37374L8.5 3.02018ZM0.5 3.52018C0.223858 3.52018 0 3.29632 0 3.02018C0 2.74404 0.223858 2.52018 0.5 2.52018V3.02018V3.52018ZM5.83333 0.353516L6.18689 -3.77595e-05L8.85355 2.66663L8.5 3.02018L8.14645 3.37374L5.47978 0.707069L5.83333 0.353516ZM8.5 3.02018L8.85355 3.37374L6.18689 6.0404L5.83333 5.68685L5.47978 5.3333L8.14645 2.66663L8.5 3.02018ZM8.5 3.02018V3.52018H0.5V3.02018V2.52018H8.5V3.02018Z" fill="#074EA8"/>
																</svg>
															<?php esc_html_e("Edit text and colors", 'gdpr-cookie-consent')?>
															</p>
															<p>
																<svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
																	<path d="M8.5 3.02018L8.85355 2.66663L9.20711 3.02018L8.85355 3.37374L8.5 3.02018ZM0.5 3.52018C0.223858 3.52018 0 3.29632 0 3.02018C0 2.74404 0.223858 2.52018 0.5 2.52018V3.02018V3.52018ZM5.83333 0.353516L6.18689 -3.77595e-05L8.85355 2.66663L8.5 3.02018L8.14645 3.37374L5.47978 0.707069L5.83333 0.353516ZM8.5 3.02018L8.85355 3.37374L6.18689 6.0404L5.83333 5.68685L5.47978 5.3333L8.14645 2.66663L8.5 3.02018ZM8.5 3.02018V3.52018H0.5V3.02018V2.52018H8.5V3.02018Z" fill="#074EA8"/>
																</svg>
															<?php esc_html_e("Manage cookie categories", 'gdpr-cookie-consent')?>
															</p>
															<p>
																<svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
																	<path d="M8.5 3.02018L8.85355 2.66663L9.20711 3.02018L8.85355 3.37374L8.5 3.02018ZM0.5 3.52018C0.223858 3.52018 0 3.29632 0 3.02018C0 2.74404 0.223858 2.52018 0.5 2.52018V3.02018V3.52018ZM5.83333 0.353516L6.18689 -3.77595e-05L8.85355 2.66663L8.5 3.02018L8.14645 3.37374L5.47978 0.707069L5.83333 0.353516ZM8.5 3.02018L8.85355 3.37374L6.18689 6.0404L5.83333 5.68685L5.47978 5.3333L8.14645 2.66663L8.5 3.02018ZM8.5 3.02018V3.52018H0.5V3.02018V2.52018H8.5V3.02018Z" fill="#074EA8"/>
																</svg>
															<?php esc_html_e("Configure preferences", 'gdpr-cookie-consent')?>
															</p>
														</div>
															<button
																type="button"
																class="customize-banner-btn gdpr-lp-card"
																onclick="window.location.href='<?php echo esc_url(
																$is_banner_active
																	? admin_url( 'admin.php?page=gdpr-cookie-consent#cookie_settings' )
																	: admin_url( 'admin.php?page=gdpr-cookie-consent#cookie_settings#general' )
															); ?>';">
																<?php if ( $is_banner_active ) : ?>

																		<?php esc_html_e( 'Customize Banner', 'gdpr-cookie-consent' ); ?>
																<?php else: ?>

																		<?php esc_html_e( 'Enable Cookie Banner', 'gdpr-cookie-consent' ); ?>
																<?php endif?>
															</button>
													</div>
													<?php if ( $is_banner_active ) : ?>
														<img src = "<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/gdpr-card.png'; ?>" class="gdpr-card-img" />
													<?php else: ?>
														<img src = "<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/gdpr-card-inactive.png'; ?>" class="gdpr-card-img" />
													<?php endif?>
												</div>
											</div>
										</div>
										<div class="lp-card">
											<div style="display:flex; gap:14px; flex: 1">
												<?php if ( $is_legalpages_active ) : ?>
												<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
													<rect width="40" height="40" rx="10" fill="#F3E8FF"/>
													<path d="M21.3389 10H16.5714C14.4164 10 13.3389 10 12.6695 10.6509C12 11.3017 12 12.3493 12 14.4444V25.5556C12 27.6507 12 28.6983 12.6695 29.3491C13.3389 30 14.4164 30 16.5714 30H23.4286C25.5836 30 26.6611 30 27.3305 29.3491C28 28.6983 28 27.6507 28 25.5556V16.476C28 16.0219 28 15.7948 27.913 15.5906C27.826 15.3864 27.6609 15.2258 27.3305 14.9047L22.9552 10.6509C22.6249 10.3297 22.4597 10.1692 22.2497 10.0846C22.0397 10 21.8061 10 21.3389 10Z" stroke="#7E22CE" stroke-width="2"/>
													<path d="M17 21L23 21" stroke="#7E22CE" stroke-width="2" stroke-linecap="round"/>
													<path d="M17 26L21 26" stroke="#7E22CE" stroke-width="2" stroke-linecap="round"/>
													<path d="M21 10V14.6667C21 15.7666 21 16.3166 21.3417 16.6583C21.6834 17 22.2334 17 23.3333 17H28" stroke="#7E22CE" stroke-width="2"/>
												</svg>
												<?php else: ?>
													<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
														<rect width="40" height="40" rx="10" fill="#e5e5e5"/>
														<path d="M21.3389 10H16.5714C14.4164 10 13.3389 10 12.6695 10.6509C12 11.3017 12 12.3493 12 14.4444V25.5556C12 27.6507 12 28.6983 12.6695 29.3491C13.3389 30 14.4164 30 16.5714 30H23.4286C25.5836 30 26.6611 30 27.3305 29.3491C28 28.6983 28 27.6507 28 25.5556V16.476C28 16.0219 28 15.7948 27.913 15.5906C27.826 15.3864 27.6609 15.2258 27.3305 14.9047L22.9552 10.6509C22.6249 10.3297 22.4597 10.1692 22.2497 10.0846C22.0397 10 21.8061 10 21.3389 10Z" stroke="#3A3A41" stroke-width="2"/>
														<path d="M17 21L23 21" stroke="#3A3A41" stroke-width="2" stroke-linecap="round"/>
														<path d="M17 26L21 26" stroke="#3A3A41" stroke-width="2" stroke-linecap="round"/>
														<path d="M21 10V14.6667C21 15.7666 21 16.3166 21.3417 16.6583C21.6834 17 22.2334 17 23.3333 17H28" stroke="#3A3A41" stroke-width="2"/>
													</svg>
												<?php endif ?>
												<div style="display:flex; justify-content:space-between;" class="compliance-card-header">
													<div style="display:flex; flex-direction:column;gap:8px">
														<h3 class=""><?php esc_html_e("Legal Pages", 'gdpr-cookie-consent')?>
															<?php if ( $is_legalpages_active ) : ?>
																<?php if( !$is_user_connected || $api_user_plan=='free') : ?>
																	<span class="status-badge purple">
																			<?php esc_html_e( '4 Basic available', 'gdpr-cookie-consent' ); ?>
																	</span>
																	<?php elseif ($is_user_connected || $api_user_plan!=='free') :?>
																		<span class="status-badge purple">
																				<?php esc_html_e( '30+ Templates Available', 'gdpr-cookie-consent' ); ?>
																		</span>
																<?php endif ?>
															<?php else : ?>
																<span class="status-badge-inactive">
																		<?php esc_html_e( 'Not installed', 'gdpr-cookie-consent' ); ?>
																	<?php endif ?>
																</span>
														</h3>
														<p style="font-size:14px"><?php esc_html_e("Your legal pages are ready to use. Generate any policy you need.", 'gdpr-cookie-consent')?></p>
														<div class="card-features">
															<?php if( !$is_user_connected || $api_user_plan=='free') : ?>
																<p>
																	<svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
																		<path d="M8.5 3.02018L8.85355 2.66663L9.20711 3.02018L8.85355 3.37374L8.5 3.02018ZM0.5 3.52018C0.223858 3.52018 0 3.29632 0 3.02018C0 2.74404 0.223858 2.52018 0.5 2.52018V3.02018V3.52018ZM5.83333 0.353516L6.18689 -3.77595e-05L8.85355 2.66663L8.5 3.02018L8.14645 3.37374L5.47978 0.707069L5.83333 0.353516ZM8.5 3.02018L8.85355 3.37374L6.18689 6.0404L5.83333 5.68685L5.47978 5.3333L8.14645 2.66663L8.5 3.02018ZM8.5 3.02018V3.52018H0.5V3.02018V2.52018H8.5V3.02018Z" fill="#074EA8"/>
																	</svg>
																	<?php esc_html_e("Standard Privacy Policy", 'gdpr-cookie-consent')?>
																</p>
																<p>
																	<svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
																		<path d="M8.5 3.02018L8.85355 2.66663L9.20711 3.02018L8.85355 3.37374L8.5 3.02018ZM0.5 3.52018C0.223858 3.52018 0 3.29632 0 3.02018C0 2.74404 0.223858 2.52018 0.5 2.52018V3.02018V3.52018ZM5.83333 0.353516L6.18689 -3.77595e-05L8.85355 2.66663L8.5 3.02018L8.14645 3.37374L5.47978 0.707069L5.83333 0.353516ZM8.5 3.02018L8.85355 3.37374L6.18689 6.0404L5.83333 5.68685L5.47978 5.3333L8.14645 2.66663L8.5 3.02018ZM8.5 3.02018V3.52018H0.5V3.02018V2.52018H8.5V3.02018Z" fill="#074EA8"/>
																	</svg>
																<?php esc_html_e("Terms of use", 'gdpr-cookie-consent')?>
																</p>
																<p>
																	<svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
																		<path d="M8.5 3.02018L8.85355 2.66663L9.20711 3.02018L8.85355 3.37374L8.5 3.02018ZM0.5 3.52018C0.223858 3.52018 0 3.29632 0 3.02018C0 2.74404 0.223858 2.52018 0.5 2.52018V3.02018V3.52018ZM5.83333 0.353516L6.18689 -3.77595e-05L8.85355 2.66663L8.5 3.02018L8.14645 3.37374L5.47978 0.707069L5.83333 0.353516ZM8.5 3.02018L8.85355 3.37374L6.18689 6.0404L5.83333 5.68685L5.47978 5.3333L8.14645 2.66663L8.5 3.02018ZM8.5 3.02018V3.52018H0.5V3.02018V2.52018H8.5V3.02018Z" fill="#074EA8"/>
																	</svg>
																<?php esc_html_e("DMCA", 'gdpr-cookie-consent')?>
																</p>
																<p>
																	<svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
																		<path d="M8.5 3.02018L8.85355 2.66663L9.20711 3.02018L8.85355 3.37374L8.5 3.02018ZM0.5 3.52018C0.223858 3.52018 0 3.29632 0 3.02018C0 2.74404 0.223858 2.52018 0.5 2.52018V3.02018V3.52018ZM5.83333 0.353516L6.18689 -3.77595e-05L8.85355 2.66663L8.5 3.02018L8.14645 3.37374L5.47978 0.707069L5.83333 0.353516ZM8.5 3.02018L8.85355 3.37374L6.18689 6.0404L5.83333 5.68685L5.47978 5.3333L8.14645 2.66663L8.5 3.02018ZM8.5 3.02018V3.52018H0.5V3.02018V2.52018H8.5V3.02018Z" fill="#074EA8"/>
																	</svg>
																<?php esc_html_e("Standard CCPA & more", 'gdpr-cookie-consent')?>
																</p>
																<?php elseif ($is_user_connected || $api_user_plan!=='free') :?>
																	<p>
																		<svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path d="M8.5 3.02018L8.85355 2.66663L9.20711 3.02018L8.85355 3.37374L8.5 3.02018ZM0.5 3.52018C0.223858 3.52018 0 3.29632 0 3.02018C0 2.74404 0.223858 2.52018 0.5 2.52018V3.02018V3.52018ZM5.83333 0.353516L6.18689 -3.77595e-05L8.85355 2.66663L8.5 3.02018L8.14645 3.37374L5.47978 0.707069L5.83333 0.353516ZM8.5 3.02018L8.85355 3.37374L6.18689 6.0404L5.83333 5.68685L5.47978 5.3333L8.14645 2.66663L8.5 3.02018ZM8.5 3.02018V3.52018H0.5V3.02018V2.52018H8.5V3.02018Z" fill="#074EA8"/>
																		</svg>
																		<?php esc_html_e("Professional Privacy Policy", 'gdpr-cookie-consent')?>
																	</p>
																	<p>
																		<svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path d="M8.5 3.02018L8.85355 2.66663L9.20711 3.02018L8.85355 3.37374L8.5 3.02018ZM0.5 3.52018C0.223858 3.52018 0 3.29632 0 3.02018C0 2.74404 0.223858 2.52018 0.5 2.52018V3.02018V3.52018ZM5.83333 0.353516L6.18689 -3.77595e-05L8.85355 2.66663L8.5 3.02018L8.14645 3.37374L5.47978 0.707069L5.83333 0.353516ZM8.5 3.02018L8.85355 3.37374L6.18689 6.0404L5.83333 5.68685L5.47978 5.3333L8.14645 2.66663L8.5 3.02018ZM8.5 3.02018V3.52018H0.5V3.02018V2.52018H8.5V3.02018Z" fill="#074EA8"/>
																		</svg>
																	<?php esc_html_e("Terms and Conditions", 'gdpr-cookie-consent')?>
																	</p>
																	<p>
																		<svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path d="M8.5 3.02018L8.85355 2.66663L9.20711 3.02018L8.85355 3.37374L8.5 3.02018ZM0.5 3.52018C0.223858 3.52018 0 3.29632 0 3.02018C0 2.74404 0.223858 2.52018 0.5 2.52018V3.02018V3.52018ZM5.83333 0.353516L6.18689 -3.77595e-05L8.85355 2.66663L8.5 3.02018L8.14645 3.37374L5.47978 0.707069L5.83333 0.353516ZM8.5 3.02018L8.85355 3.37374L6.18689 6.0404L5.83333 5.68685L5.47978 5.3333L8.14645 2.66663L8.5 3.02018ZM8.5 3.02018V3.52018H0.5V3.02018V2.52018H8.5V3.02018Z" fill="#074EA8"/>
																		</svg>
																	<?php esc_html_e("Cookie Policy", 'gdpr-cookie-consent')?>
																	</p>
																	<p>
																		<svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path d="M8.5 3.02018L8.85355 2.66663L9.20711 3.02018L8.85355 3.37374L8.5 3.02018ZM0.5 3.52018C0.223858 3.52018 0 3.29632 0 3.02018C0 2.74404 0.223858 2.52018 0.5 2.52018V3.02018V3.52018ZM5.83333 0.353516L6.18689 -3.77595e-05L8.85355 2.66663L8.5 3.02018L8.14645 3.37374L5.47978 0.707069L5.83333 0.353516ZM8.5 3.02018L8.85355 3.37374L6.18689 6.0404L5.83333 5.68685L5.47978 5.3333L8.14645 2.66663L8.5 3.02018ZM8.5 3.02018V3.52018H0.5V3.02018V2.52018H8.5V3.02018Z" fill="#074EA8"/>
																		</svg>
																	<?php esc_html_e("Disclaimer & more", 'gdpr-cookie-consent')?>
																	</p>
																<?php endif?>
														</div>
														<?php if ( $is_legalpages_active ) : ?>
															<button
																type="button"
																class="customize-banner-btn gdpr-lp-card"
																onclick="window.location.href='<?php echo esc_url( admin_url( 'index.php?page=wplegal-wizard#/' ) ); ?>';">
																<?php esc_html_e( 'Manage Legal pages', 'gdpr-cookie-consent' ); ?>
															</button>
														<?php else : ?>
															<button
																type="button"
																class="customize-banner-btn gdpr-lp-card"
																onclick="window.location.href='<?php echo esc_url( admin_url( 'admin.php?page=legal-pages' ) ); ?>';">
																<?php esc_html_e( 'Install WP Legal Pages', 'gdpr-cookie-consent' ); ?>
															</button>
														<?php endif; ?>
													</div>
													<!-- <div> -->
														<?php if ( $is_legalpages_active ) : ?>
															<img src = "<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/lp-card.png'; ?>" class="lp-card-img" />
														<?php else: ?>
															<img src = "<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/lp-card-not-installed.png'; ?>" class="lp-card-img lp-card-not-installed" />
														<?php endif?>
													</div>
												</div>
											</div>
									</div>
								</div>	
								<div class="help-container">
									<svg width="38" height="38" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
										<rect width="38" height="38" rx="19" fill="#064799"/>
										<rect x="23" y="18.0835" width="5" height="8" rx="2.5" fill="#E6EDF6" stroke="#E6EDF6" stroke-width="2" stroke-linejoin="round"/>
										<rect x="10" y="18.0835" width="4" height="8" rx="2" fill="#E6EDF6" stroke="#E6EDF6" stroke-width="2" stroke-linejoin="round"/>
										<path d="M10 20.0835V23.0835" stroke="#E6EDF6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M28 20.0835V23.0835" stroke="#E6EDF6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M28 19.5835C28 16.7987 27.0518 14.128 25.364 12.1589C23.6761 10.1897 21.3869 9.0835 19 9.0835C16.6131 9.0835 14.3239 10.1897 12.636 12.1589C10.9482 14.128 10 16.7987 10 19.5835" stroke="#E6EDF6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
									<div style="display:flex; justify-content:space-between;flex:1">
										<div style="display:flex; flex-direction: column">
											<p style="font-weight: 500"><?php esc_html_e("Need help?", "gdpr-cookie-consent")?></p>
											<p><?php esc_html_e("Visit our help center or contact support anytime.", "gdpr-cookie-consent")?></p>
										</div>
										<a class="need-more-help-link" href="https://wplegalpages.com/docs/wplp-docs" target="_blank"><?php esc_html_e("View Help Center", "gdpr-cookie-consent")?>
											<span>
												<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M16.6667 8.33325V3.33325H11.6667M16.6667 3.33325L10 9.99992" stroke="#074EA8" stroke-width="1.66667"/>
													<path d="M9.16666 4.16675H5.83332C4.91285 4.16675 4.16666 4.91294 4.16666 5.83341V14.1667C4.16666 15.0872 4.91285 15.8334 5.83332 15.8334H14.1667C15.0871 15.8334 15.8333 15.0872 15.8333 14.1667V10.8334" stroke="#074EA8" stroke-width="1.66667" stroke-linecap="round"/>
												</svg>
											</span>
										</a>
									</div>
								</div>
								<?php require_once plugin_dir_path( __FILE__ ) . 'gdpr-dashboard-tab-template.php'; ?>
							</div>
							<!-- create cookie content  -->
							<!-- <div class="gdpr-cookie-consent-admin-create-cookie-content gdpr-cookie-consent-admin-tab-content" id="create_cookie_banner">
								<?php //require_once plugin_dir_path( __FILE__ ) . 'gdpr-create-cookie-banner-tab-template.php'; ?>
							</div> -->
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
		</div>
	</div>
</div>
