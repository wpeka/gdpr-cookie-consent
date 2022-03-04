<?php
/**
 * Class Test_Gdpr_Cookie_Consent_Policy_Data
 *
 * @package Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/Tests
 */

/**
 * Test for Gdpr_Cookie_Consent_Policy_Data class
 */
class Test_Gdpr_Cookie_Consent_Policy_Data extends WP_UnitTestCase {

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
		self::$gdpr_cookie_consent = new Gdpr_Cookie_Consent_Policy_Data();
	}

	/**
	 * Test for construct function
	 */
	public function test__construct() {
		wp_set_current_user(
			self::factory()->user->create(
				array(
					'role' => 'administrator',
				)
			)
		);
		$obj = new Gdpr_Cookie_Consent_Policy_Data();
		$this->assertTrue( $obj instanceof Gdpr_Cookie_Consent_Policy_Data );
	}
	/**
	 * Test for gdpr_register_custom_post_type function.
	 */
	public function test_gdpr_register_custom_post_type() {
		self::$gdpr_cookie_consent->gdpr_register_custom_post_type();
		$this->assertTrue( post_type_exists( GDPR_POLICY_DATA_POST_TYPE ) );
	}
	/**
	 * Test for gdpr_add_meta_box function.
	 */
	public function test_gdpr_add_meta_box() {
		self::$gdpr_cookie_consent->gdpr_add_meta_box();
		$this->assertTrue( true );
	}
}


