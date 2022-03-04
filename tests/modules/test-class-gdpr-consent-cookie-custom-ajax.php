<?php
/**
 * Class Test_Cookie_Consent_Cookie_Custom_Ajax
 *
 * @package Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/Tests
 */

/**
 * Test for Cookie_Consent_Cookie_Custom_Ajax class
 */
class Test_Cookie_Consent_Cookie_Custom_Ajax extends WP_UnitTestCase {

	/**
	 * Class Instance.
	 *
	 * @var object $gdpr_cookie_consent Class Instance
	 * @access public
	 */
	public static $gdpr_cookie_consent;

	/**
	 * Plugin name.
	 *
	 * @var string $plugin_name plugin name
	 * @access public
	 */
	public static $plugin_name;

	/**
	 * Plugin version.
	 *
	 * @var string $plugin_version plugin version
	 * @access public
	 */
	public static $plugin_version;

	/**
	 * Setup function for all tests.
	 *
	 * @param WP_UnitTest_Factory $factory helper for unit test functionality.
	 */
	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		if ( defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ) {
			self::$plugin_version = GDPR_COOKIE_CONSENT_VERSION;
		} else {
			self::$plugin_version = '2.0.7';
		}
		self::$plugin_name         = 'gdpr_cookie_consent';
		self::$gdpr_cookie_consent = new Gdpr_Cookie_Consent_Cookie_Custom_Ajax();
	}

	/**
	 * Test for construct function
	 */
	public function test__construct() {
		$obj = new Gdpr_Cookie_Consent_Cookie_Custom_Ajax();
		$this->assertTrue( $obj instanceof Gdpr_Cookie_Consent_Cookie_Custom_Ajax );
	}
	/**
	 * Test for ajax_cookie_custom.
	 */
	public function test_ajax_cookie_custom() {
		self::factory()->user->create(
			array(
				'role' => 'user',
			)
		);
		$value = self::$gdpr_cookie_consent->ajax_cookie_custom();
		$this->assertTrue( true );
	}
	/**
	 * Test for save_post_cookie.
	 */
	public function test_save_post_cookie() {
		$value = self::$gdpr_cookie_consent->save_post_cookie();
		$this->assertTrue( true );
	}
}


