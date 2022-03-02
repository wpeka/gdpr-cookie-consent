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
	 * Set up function.
	 *
	 * @param class WP_UnitTest_Factory $factory class instance.
	 */
	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
	}
	/**
	 * Test for Admin Settings Page
	 */
	public function test_admin_settings_page() {
		$this->assertTrue( true );
	}
}
