<?php

/**
 * Provide a cookie manager tab area view for the WP Cookie Consent plugin
 *
 * This file is used to markup the admin-facing aspects of the WP Cookie Consent plugin.
 *
 * @link       https://club.wpeka.com/
 * @since      4.0.0
 *
 * @package gdpr-cookie-consent
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$baseurl = '';
if ( isset( $_SERVER['PHP_SELF'] ) ) {
	$baseurl = esc_url_raw( wp_unslash( $_SERVER['PHP_SELF'] ) );
}

if ( class_exists( 'Gdpr_Cookie_Consent_Admin' ) ) {
	Gdpr_Cookie_Consent_Admin::gdpr_cookie_consent_cookie_manager_settings();
}
