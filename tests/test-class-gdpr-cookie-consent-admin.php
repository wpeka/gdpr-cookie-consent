<?php
/**
 * Class Test_Gdpr_Cookie_Consent_Admin
 *
 * @package Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/Tests
 */

/**
 * Test for Gdpr_Cookie_Consent_Admin class
 */
class Test_Gdpr_Cookie_Consent_Admin extends WP_UnitTestCase {

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
			self::$plugin_version = '2.2.0';
		}
		self::$plugin_name         = 'gdpr_cookie_consent';
		self::$gdpr_cookie_consent = new Gdpr_Cookie_Consent_Admin( self::$plugin_name, self::$plugin_version );
	}

	/**
	 * Test for gdpr_cookie_consent_active_plugins function
	 */
	public function test_gdpr_cookie_consent_active_plugins() {
		$active_plugins = self::$gdpr_cookie_consent->gdpr_cookie_consent_active_plugins();
		$this->assertIsArray( $active_plugins );
	}

	/**
	 * Test for dashboard page
	 */
	public function test_gdpr_cookie_consent_dashboard() {
		wp_set_current_user(
			self::factory()->user->create(
				array(
					'role' => 'administrator',
				)
			)
		);
		ob_start();
		self::$gdpr_cookie_consent->gdpr_cookie_consent_dashboard();
		$expected_html = ob_get_clean();
		$this->assertTrue( is_string( $expected_html ) && wp_strip_all_tags( $expected_html ) );
	}
}
