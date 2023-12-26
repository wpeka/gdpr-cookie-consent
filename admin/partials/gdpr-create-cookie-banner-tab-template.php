<?php
/**
 * Provide a Wizard view for the admin.
 *
 * This file is used to markup the admin-facing aspects of the plugin (Wizard Page).
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin
 * @author Omendra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$baseurl = '';
if ( isset( $_SERVER['PHP_SELF'] ) ) {
	$baseurl = esc_url_raw( wp_unslash( $_SERVER['PHP_SELF'] ) );
}

if ( class_exists( 'Gdpr_Cookie_Consent_Admin' ) ) {
	Gdpr_Cookie_Consent_Admin::gdpr_cookie_consent_wizard();
}
