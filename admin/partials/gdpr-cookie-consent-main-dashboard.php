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
		
// check if pro is activated or installed.
$pro_is_activated  = get_option( 'wpl_pro_active', false );
$the_options       = Gdpr_Cookie_Consent::gdpr_get_settings();
$is_data_req_on    = isset( $the_options['data_reqs_on'] ) ? $the_options['data_reqs_on'] : null;
$is_consent_log_on = isset( $the_options['logging_on'] ) ? $the_options['logging_on'] : null;
$installed_plugins = get_plugins();
$pro_installed     = isset( $installed_plugins['wpl-cookie-consent/wpl-cookie-consent.php'] ) ? true : false;

$plugin_name                   = 'wplegalpages/wplegalpages.php';
$is_legalpages_active = is_plugin_active( $plugin_name );
$page_view_notice_message = get_option( 'page_view_notice_message', '' );
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

$gdpr_pages_scanned 			 = get_option('gdpr_no_of_page_scan', 0);
$gdpr_no_of_page_scan            = $total_no_of_free_scans - get_option( 'gdpr_no_of_page_scan' );
$remaining_percentage_scan_limit = round( ( get_option( 'gdpr_no_of_page_scan' ) / $total_no_of_free_scans ) * 100 );

//Monthly scan
$gdpr_monthly_scan_percent = 0;
if ( 'free' === $api_user_plan ) { 
	$scan_limit     = get_transient( 'gdpr_monthly_scan_limit_exhausted' );
	$scan_limit_int = (int) $scan_limit; 
	$gdpr_monthly_scan_percent = ( ( $scan_limit_int ) / 5 ) * 100;
}

$gdpr_monthly_page_views = get_option('wpl_monthly_page_views', 0);
$gdpr_monthly_page_views_limit = 0;
$gdpr_monthly_page_views_percent = 0;
if ( 'free' === $api_user_plan ) { 
	$gdpr_monthly_page_views_limit = 20000;
	$gdpr_monthly_page_views_percent = ( ( $gdpr_monthly_page_views ) / 20000 ) * 100;
} else if ( '3sites' === $api_user_plan ) {
	$gdpr_monthly_page_views_limit = 100000;
	$gdpr_monthly_page_views_percent = ( ( $gdpr_monthly_page_views ) / 100000 ) * 100;
}

$gdpr_remaining_page_views = $gdpr_monthly_page_views_limit - $gdpr_monthly_page_views;

$gdpr_plan_warning = false;

if( $gdpr_monthly_page_views_percent === 100 || $remaining_percentage_scan_limit === 100 || $gdpr_monthly_scan_percent === 100 ) {
	$gdpr_plan_warning = true;
}

?>

<div id="gdpr-cookie-consent-main-admin-structure" class="gdpr-cookie-consent-main-admin-structure">
	<div id="gdpr-cookie-consent-main-admin-header" class="gdpr-cookie-consent-main-admin-header">
		<div class="wplp-compliance-main-wrapper">
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
						<div class="gdpr-cookie-consent-admin-new-dashboard-btn"><a style = "text-decoration: none;" target="_blank" href="<?php echo esc_url( GDPR_APP_URL . '/app' ); ?>"><?php esc_html_e( 'Try New Dashboard', 'gdpr-cookie-consent' ); ?><span class="gdpr-cookie-consent-admin-new-dashboard-btn-beta-span"><?php esc_html_e( 'BETA', 'gdpr-cookie-consent')?></span></a></div>
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
			<div class="wplp-compliance-main">
				<div class="gdpr-cookie-consent-admin-tabs-section">
					<div class="gdpr-cookie-consent-admin-tabs dashboard-tabs">
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
											<path d="M10.8333 7.5V2.5H17.5V7.5H10.8333ZM2.5 10.8333V2.5H9.16667V10.8333H2.5ZM10.8333 17.5V9.16667H17.5V17.5H10.8333ZM2.5 17.5V12.5H9.16667V17.5H2.5Z" fill="currentColor"/>
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
								<svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" clip-rule="evenodd" d="M4.4073 6.46477L0.282304 2.33977C-0.0430841 2.01438 -0.0430841 1.48682 0.282304 1.16143C0.607691 0.836044 1.13525 0.836045 1.46064 1.16143L4.28936 3.99016C4.67989 4.38068 5.31305 4.38068 5.70358 3.99016L8.5323 1.16143C8.85769 0.836044 9.38525 0.836044 9.71064 1.16143C10.036 1.48682 10.036 2.01438 9.71064 2.33977L5.58564 6.46477C5.42936 6.62099 5.21744 6.70875 4.99647 6.70875C4.7755 6.70875 4.56358 6.62099 4.4073 6.46477Z" fill="currentColor"/>
								</svg>
							</div>
						</a>
						<!-- Cookie Consent Plugin tab  -->
						<a href="?page=gdpr-cookie-consent" class="gdpr-admin-tab-link gdpr-cookie-consent-tab">
							<div class="wplp-admin-tab-link-content wplp-compliance-cookie-consent-tab">
								<div class="gdpr-admin-main-tab wplp-admin-tab-link-left">
									<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M8.99984 17.334C7.84706 17.334 6.76373 17.1151 5.74984 16.6773C4.73595 16.2395 3.854 15.6459 3.104 14.8965C2.354 14.147 1.76039 13.2651 1.32317 12.2507C0.885948 11.2362 0.667059 10.1529 0.666504 9.00065C0.666504 7.95898 0.867893 6.93815 1.27067 5.93815C1.67345 4.93815 2.23595 4.04565 2.95817 3.26065C3.68039 2.47565 4.54845 1.84371 5.56234 1.36482C6.57623 0.885929 7.68734 0.646484 8.89567 0.646484C9.18734 0.646484 9.48595 0.660373 9.7915 0.688151C10.0971 0.715929 10.4096 0.76454 10.729 0.833984C10.604 1.45898 10.6457 2.04926 10.854 2.60482C11.0623 3.16037 11.3748 3.62204 11.7915 3.98982C12.2082 4.3576 12.7048 4.61121 13.2815 4.75065C13.8582 4.8901 14.4518 4.85537 15.0623 4.64648C14.7012 5.46593 14.7534 6.25065 15.219 7.00065C15.6846 7.75065 16.3754 8.13954 17.2915 8.16732C17.3054 8.3201 17.3159 8.46232 17.3232 8.59398C17.3304 8.72565 17.3337 8.86815 17.3332 9.02148C17.3332 10.1604 17.1143 11.2332 16.6765 12.2398C16.2387 13.2465 15.6451 14.1284 14.8957 14.8857C14.1462 15.6429 13.2643 16.2401 12.2498 16.6773C11.2354 17.1145 10.1521 17.3334 8.99984 17.334ZM7.74984 7.33398C8.09706 7.33398 8.39234 7.2126 8.63567 6.96982C8.879 6.72704 9.00039 6.43176 8.99984 6.08398C8.99928 5.73621 8.87789 5.44121 8.63567 5.19898C8.39345 4.95676 8.09817 4.8351 7.74984 4.83398C7.4015 4.83287 7.1065 4.95454 6.86484 5.19898C6.62317 5.44343 6.5015 5.73843 6.49984 6.08398C6.49817 6.42954 6.61984 6.72482 6.86484 6.96982C7.10984 7.21482 7.40484 7.33621 7.74984 7.33398ZM6.08317 11.5007C6.43039 11.5007 6.72567 11.3793 6.969 11.1365C7.21234 10.8937 7.33373 10.5984 7.33317 10.2507C7.33261 9.90287 7.21123 9.60787 6.969 9.36565C6.72678 9.12343 6.4315 9.00176 6.08317 9.00065C5.73484 8.99954 5.43984 9.12121 5.19817 9.36565C4.9565 9.6101 4.83484 9.9051 4.83317 10.2507C4.8315 10.5962 4.95317 10.8915 5.19817 11.1365C5.44317 11.3815 5.73817 11.5029 6.08317 11.5007ZM11.4998 12.334C11.7359 12.334 11.934 12.254 12.094 12.094C12.254 11.934 12.3337 11.7362 12.3332 11.5007C12.3326 11.2651 12.2526 11.0673 12.0932 10.9073C11.9337 10.7473 11.7359 10.6673 11.4998 10.6673C11.2637 10.6673 11.0659 10.7473 10.9065 10.9073C10.7471 11.0673 10.6671 11.2651 10.6665 11.5007C10.6659 11.7362 10.7459 11.9343 10.9065 12.0948C11.0671 12.2554 11.2648 12.3351 11.4998 12.334Z" fill="currentColor"/>
									</svg>

									<?php echo esc_html('Cookie Consent','gdpr-cookie-consent'); ?>
								</div>
								<svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" clip-rule="evenodd" d="M4.4073 6.46477L0.282304 2.33977C-0.0430841 2.01438 -0.0430841 1.48682 0.282304 1.16143C0.607691 0.836044 1.13525 0.836045 1.46064 1.16143L4.28936 3.99016C4.67989 4.38068 5.31305 4.38068 5.70358 3.99016L8.5323 1.16143C8.85769 0.836044 9.38525 0.836044 9.71064 1.16143C10.036 1.48682 10.036 2.01438 9.71064 2.33977L5.58564 6.46477C5.42936 6.62099 5.21744 6.70875 4.99647 6.70875C4.7755 6.70875 4.56358 6.62099 4.4073 6.46477Z" fill="currentColor"/>
								</svg>
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

								<!-- connect your website to WP Cookie Consent  -->
								<div class="gdpr-cookie-consent-connect-api-container">
									<div class="gdpr-api-info-content">
										<div class="wplp-compliance-banner-content">
											<h1 class="wplp-compliance-banner-header"><?php echo esc_html( 'Welcome to WPLP Compliance Platform!', 'gdpr-cookie-consent' ); ?></h1>
											<p><?php echo esc_html('Complete Legal & Cookie Protection', 'gdpr-cookie-consent'); ?></p>
											<p><?php echo esc_html('Your complete compliance package for your website, from legal documents to cookie consent.', 'gdpr-cookie-consent'); ?></p>
											<div class="wplp-compliance-banner-tags">
												<span class="wplp-compliance-banner-tag"><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/WPLP_banner-tag.png'; ?>" alt="Banner Tag"><?php echo esc_html( 'GDPR Compliant', 'gdpr-cookie-consent' ); ?></span>
												<span class="wplp-compliance-banner-tag"><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/WPLP_banner-tag.png'; ?>" alt="Banner Tag"><?php echo esc_html( 'CCPA Ready', 'gdpr-cookie-consent' ); ?></span>
												<span class="wplp-compliance-banner-tag"><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/WPLP_banner-tag.png'; ?>" alt="Banner Tag"><?php echo esc_html( 'Auto-Generated Policies', 'gdpr-cookie-consent' ); ?></span>
												<span class="wplp-compliance-banner-tag"><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/WPLP_banner-tag.png'; ?>" alt="Banner Tag"><?php echo esc_html( 'Real-Time Monitoring', 'gdpr-cookie-consent' ); ?></span>
											</div>
										</div>
									</div>
								
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
								</div>
								
								<!-- scans -->
								<?php
								// if user is connected to the app.wplegalpages then show remaining scans
								if ( $is_user_connected == true && !$pro_installed ) { ?>
									<div class="gdpr-remaining-scans-content gdpr-remaining-scans-content-dashboard" >
										<div class="wplp-remaining-scan-header-left">
											<div class="wplp-scan-label" style="<?php if( $gdpr_plan_warning === true ) {
												echo 'flex-direction: row;';
											} ?>">
												<p><?php echo esc_html( 'Remaining Scans', 'gdpr-cookie-consent' ); ?></p>
												<p><?php echo esc_html( '& Usage:', 'gdpr-cookie-consent' ); ?></p>
											</div>
										
											<?php if( $gdpr_plan_warning === true ) { ?>
												<div class="wplp-plan-limit-warning">
													<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/plan_limit_exceeded.svg'; ?>" alt="Plan Limit Exceeded"></span>
													<p><?php echo esc_html( 'You have exhausted your current plan.', 'gdpr-cookie-consent' ); ?><br><?php echo esc_html( 'Upgrade to Continue', 'gdpr-cookie-consent'); ?></p>
												</div>
											<?php } ?>
										</div>	
											
										<div class="gdpr-progress-wrapper">
											<div class="gdpr-monthly-scans-progress" style="
												background: 
													radial-gradient(closest-side, white 90%, transparent 80% 100%), 
													conic-gradient( <?php echo ( $gdpr_monthly_scan_percent < 35 ) ? esc_html('#469955', 'gdpr-cookie-consent') : ( ( $gdpr_monthly_scan_percent < 75 ) ? esc_html('#ca8b25', 'gdpr-cookie-consent') : esc_html('#c93a38', 'gdpr-cookie-consent') ); ?> <?php echo esc_html( $gdpr_monthly_scan_percent ); ?>%, <?php echo ( $gdpr_monthly_scan_percent < 35 ) ? esc_html('#e6f5ee', 'gdpr-cookie-consent') : ( ( $gdpr_monthly_scan_percent < 75 ) ? esc_html('#fef7c3', 'gdpr-cookie-consent') : esc_html('#f8e6e6', 'gdpr-cookie-consent') ); ?> 0);"
											>
												<?php if ( 'free' === $api_user_plan ) { ?>
													<span style="color: <?php echo ( $gdpr_monthly_scan_percent < 35 ) ? esc_html('#469955', 'gdpr-cookie-consent') : ( ( $gdpr_monthly_scan_percent < 75 ) ? esc_html('#ca8b25', 'gdpr-cookie-consent') : esc_html('#c93a38', 'gdpr-cookie-consent') ); ?>;"><?php echo ceil( $gdpr_monthly_scan_percent ); ?>%</span>
  													<progress value="<?php echo ceil( $gdpr_monthly_scan_percent ); ?>" min="0" max="100" style="visibility:hidden;height:0;width:0;"></progress>
												<?php } else { ?>
													<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/Unlimited_scan.svg'; ?>" alt="Unlimited Monthly Scans"></span>
  													<progress value="<?php echo ceil( $gdpr_monthly_scan_percent ); ?>" min="0" max="100" style="visibility:hidden;height:0;width:0;"></progress>
												<?php } ?>
												
											</div>
												
											<div class="gdpr-progress-content">
												<h3><?php echo esc_html( 'Scans / Month', 'gdpr-cookie-consent' ); ?></h3>
												<?php if ( 'free' === $api_user_plan ) { ?>
														<p><?php echo esc_html( $scan_limit_int . ' / 5', 'gdpr-cookie-consent' ) ?>
															<span>
																<?php if ( $gdpr_monthly_scan_percent >= 75 ) {
        															echo '<img src="' . esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/limit_warning.svg' ) . '" alt="">'; 
																} ?>
															</span>
														<p><?echo esc_html( ( 5 - $scan_limit_int ) . ' Remaining', 'gdpr-cookie-consent' ); ?></p>
												<?php } else { ?>
														<p><?php echo esc_html( 'Unlimited', 'gdpr-cookie-consent' ) ?></p>
												<?php } ?>
											</div>
										</div>
												
										<div class="gdpr-progress-wrapper">
											<?php if ( '10Sites' === $api_user_plan || '10sites' === $api_user_plan ) { ?>
												<div class="gdpr-remaining-scans-progress" style="
													background: 
														radial-gradient(closest-side, white 90%, transparent 80% 100%),
    													conic-gradient(#469955 0%, #e6f5ee 0);"
												>
													<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/Unlimited_scan.svg'; ?>" alt="Unlimited Monthly Scans"></span>
  													<progress value="<?php echo ceil( $remaining_percentage_scan_limit ); ?>" min="0" max="100" style="visibility:hidden;height:0;width:0;"></progress>
												</div>
											
												<div class="gdpr-progress-content">
													<h3><?php echo esc_html( 'Pages / Scan', 'gdpr-cookie-consent' ); ?></h3>
													<p><?php echo esc_html( 'Unlimited', 'gdpr-cookie-consent' ) ?></p>
												</div>
											<?php } else { ?>
												<div class="gdpr-remaining-scans-progress" style="
													background: 
														radial-gradient(closest-side, white 90%, transparent 80% 100%),
    													conic-gradient( <?php echo ( $remaining_percentage_scan_limit < 35 ) ? esc_html('#469955', 'gdpr-cookie-consent') : ( ( $remaining_percentage_scan_limit < 75 ) ? esc_html('#ca8b25', 'gdpr-cookie-consent') : esc_html('#c93a38', 'gdpr-cookie-consent') ); ?> <?php echo esc_html( $remaining_percentage_scan_limit ); ?>%, <?php echo ( $remaining_percentage_scan_limit < 35 ) ? esc_html('#e6f5ee', 'gdpr-cookie-consent') : ( ( $remaining_percentage_scan_limit < 75 ) ? esc_html('#fef7c3', 'gdpr-cookie-consent') : esc_html('#f8e6e6', 'gdpr-cookie-consent') ); ?> 0);"
												>
													<span style="color: <?php echo ( $remaining_percentage_scan_limit < 35 ) ? esc_html('#469955', 'gdpr-cookie-consent') : ( ( $remaining_percentage_scan_limit < 75 ) ? esc_html('#ca8b25', 'gdpr-cookie-consent') : esc_html('#c93a38', 'gdpr-cookie-consent') ); ?>;"><?php echo ceil( $remaining_percentage_scan_limit ); ?>%</span>
  													<progress value="<?php echo ceil( $remaining_percentage_scan_limit ); ?>" min="0" max="100" style="visibility:hidden;height:0;width:0;"></progress>
												</div>
											
												<div class="gdpr-progress-content">
													<h3><?php echo esc_html( 'Pages / Scan', 'gdpr-cookie-consent' ); ?></h3>
													<p><?php echo esc_html( $gdpr_pages_scanned . ' / ' . $total_no_of_free_scans, 'gdpr-cookie-consent' ) ?>
														<span>
															<?php if ( $remaining_percentage_scan_limit >= 75 ) {
        														echo '<img src="' . esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/limit_warning.svg' ) . '" alt="">'; 
															} ?>
														</span>
													</p>
													<p><?echo esc_html( $gdpr_no_of_page_scan . ' Remaining', 'gdpr-cookie-consent' ); ?></p>
												</div>
											<?php } ?>
										</div>
														
										<div class="gdpr-progress-wrapper">
											<?php if ( '10Sites' === $api_user_plan || '10sites' === $api_user_plan ) { ?>
												<div class="gdpr-pageviews-progress" style="
													background: 
														radial-gradient(closest-side, white 90%, transparent 80% 100%),
    													conic-gradient(#469955 0%, #e6f5ee 0);"
												>
													<span><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/Unlimited_scan.svg'; ?>" alt="Unlimited Pageviews"></span>
  													<progress value="<?php echo ceil( $gdpr_monthly_page_views_percent ); ?>" min="0" max="100" style="visibility:hidden;height:0;width:0;"></progress>
												</div>
											
												<div class="gdpr-progress-content">
													<h3><?php echo esc_html( 'Page Views / Month', 'gdpr-cookie-consent' ); ?></h3>
													<p><?php echo esc_html( 'Unlimited', 'gdpr-cookie-consent' ) ?></p>
												</div>
											<?php } else { ?>
												<div class="gdpr-remaining-scans-progress" style="
													background: 
														radial-gradient(closest-side, white 90%, transparent 80% 100%),
    													conic-gradient( <?php echo ( $gdpr_monthly_page_views_percent < 35 ) ? esc_html('#469955', 'gdpr-cookie-consent') : ( ( $gdpr_monthly_page_views_percent < 75 ) ? esc_html('#ca8b25', 'gdpr-cookie-consent') : esc_html('#c93a38', 'gdpr-cookie-consent') ); ?> <?php echo esc_html( $gdpr_monthly_page_views_percent ); ?>%, <?php echo ( $gdpr_monthly_page_views_percent < 35 ) ? esc_html('#e6f5ee', 'gdpr-cookie-consent') : ( ( $gdpr_monthly_page_views_percent < 75 ) ? esc_html('#fef7c3', 'gdpr-cookie-consent') : esc_html('#f8e6e6', 'gdpr-cookie-consent') ); ?> 0);"
												>
													<span style="color: <?php echo ( $gdpr_monthly_page_views_percent < 35 ) ? esc_html('#469955', 'gdpr-cookie-consent') : ( ( $gdpr_monthly_page_views_percent < 75 ) ? esc_html('#ca8b25', 'gdpr-cookie-consent') : esc_html('#c93a38', 'gdpr-cookie-consent') ); ?>;"><?php echo ceil( $gdpr_monthly_page_views_percent ); ?>%</span>
  													<progress value="<?php echo ceil( $gdpr_monthly_page_views_percent ); ?>" min="0" max="100" style="visibility:hidden;height:0;width:0;"></progress>
												</div>
											
												<div class="gdpr-progress-content">
													<h3><?php echo esc_html( 'Page Views / Month', 'gdpr-cookie-consent' ); ?></h3>
													<p><?php echo esc_html( $gdpr_monthly_page_views . ' / ' . $gdpr_monthly_page_views_limit, 'gdpr-cookie-consent' ) ?>
														<span>
															<?php if ( $gdpr_monthly_page_views_percent >= 75 ) {
        														echo '<img src="' . esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/limit_warning.svg' ) . '" alt="">'; 
															} ?>
														</span>
													</p>
													<p><?echo esc_html( $gdpr_remaining_page_views . ' Remaining', 'gdpr-cookie-consent' ); ?></p>
												</div>
											<?php } ?>
										</div>
														
										<div class="wplp-plan-details">
											<p><?php echo esc_html('Current Plan: ', 'gdpr-cookie-consent'); ?>
											<?php if( $api_user_plan !== 'free' ) { ?>
												<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/gdpr_pro_account.svg'; ?>" alt="Pro Account">
											<?php } ?>
											<span><?php echo esc_html( $api_user_plan ); ?></span></p>
											<?php if( $api_user_plan === 'free' || $api_user_plan === 'Free' ) { ?>
												<a class="wplp--scan-header-upgrade-plan gdpr-cookie-consent-admin-upgrade-button"><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/gdpr_header_upgrade_icon.svg'; ?>" alt=""><?php echo esc_html('Upgrade', 'gdpr-cookie-consent'); ?></a>
											<?php } else { ?>
												<a class="wplp-scan-header-add-sites gdpr-cookie-consent-admin-upgrade-button"><?php echo esc_html('Add More Sites', 'gdpr-cookie-consent'); ?><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/gdpr_add_site.svg'; ?>" alt=""></a>
											<?php } ?>
										</div>
									</div>
									<?php if ( get_transient( 'app_wplp_subscription_payment_status_failed' ) ) { ?>
									<div class="gdpr-subsription-payment-failed-notice" >
										<svg class="gdpr-payment-fail-icon" viewBox="0 0 24 24">
  										  <circle cx="12" cy="12" r="10" stroke="currentColor" fill="none" stroke-width="2"/>
  										  <line x1="12" y1="8" x2="12" y2="8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
  										  <line x1="12" y1="12" x2="12" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
  										</svg>
										<p>
											<span class="gdpr-payment-fail-message">
												<strong>
													<?php esc_html_e( 'Your last payment attempt failed.', 'gdpr-cookie-consent' ); ?>
												</strong> 
												<?php esc_html_e( 'Please update your payment details within 7 days to avoid service disruption.', 'gdpr-cookie-consent' ); ?>
											</span>
										</p>
									</div>
									<?php
									}
									if ( get_option( 'app_wplp_subscription_status_pending_cancel' ) ) { ?>
									<div class="gdpr-subsription-payment-failed-notice" >
										<svg class="gdpr-payment-fail-icon" viewBox="0 0 24 24">
  										  <circle cx="12" cy="12" r="10" stroke="currentColor" fill="none" stroke-width="2"/>
  										  <line x1="12" y1="8" x2="12" y2="8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
  										  <line x1="12" y1="12" x2="12" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
  										</svg>
										<p>
											<span class="gdpr-payment-fail-message">
											<?php esc_html_e( 'Your plan has been cancelled to the Free Plan due to a failed payment or manual cancellation.', 'gdpr-cookie-consent' ); ?>
												<strong>
													<?php esc_html_e( 'Upgrade now to restore premium features.', 'gdpr-cookie-consent' ); ?>
												</strong>
											</span>
										</p>
									</div>
									<?php
									}
								
								}
								?>

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
