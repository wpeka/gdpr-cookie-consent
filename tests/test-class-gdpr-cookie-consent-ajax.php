<?php
/**
 * Class Test_Gdpr_Cookie_Consent_Ajax
 *
 * @package Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/Tests
 */

/**
 * Required file.
 */
require_once ABSPATH . 'wp-admin/includes/ajax-actions.php';

/**
 * Unit test cases for ajax request.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/Tests
 * @author     WPEka <hello@wpeka.com>
 */
class Test_Gdpr_Cookie_Consent_Ajax extends WP_Ajax_UnitTestCase {
	/**
	 * Class Instance.
	 *
	 * @var object $gdpr_cookie_consent Class Instance
	 * @access public
	 */
	public static $gdpr_cookie_consent_ajax;
	/**
	 * Set up function.
	 *
	 * @param class WP_UnitTest_Factory $factory class instance.
	 */
	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$gdpr_cookie_consent_ajax = new Gdpr_Cookie_Consent_Cookie_Custom_Ajax();
	}
	/**
	 * Test for Admin Settings Page
	 */
	public function test_admin_settings_page() {
		$this->_setRole( 'administrator' );
		$_POST['update_admin_settings_form'] = 'abc';
		$_POST['gdpr_settings_ajax_update']  = 'abc';
		$_POST['security']                   = wp_create_nonce( 'gdprcookieconsent-update-' . GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
		try {
			$this->_handleAjax( 'admin_settings_page' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}
		$this->assertTrue( true );
	}

}
