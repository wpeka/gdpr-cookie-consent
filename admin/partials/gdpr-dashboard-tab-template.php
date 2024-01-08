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

$baseurl = '';
if ( isset( $_SERVER['PHP_SELF'] ) ) {
	$baseurl = esc_url_raw( wp_unslash( $_SERVER['PHP_SELF'] ) );
}

if ( class_exists( 'Gdpr_Cookie_Consent_Admin' ) ) {
	Gdpr_Cookie_Consent_Admin::gdpr_cookie_consent_dashboard();
}
