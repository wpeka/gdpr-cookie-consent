<?php
/**
 * Class Test_Gdpr_Cookie_Consent_Public
 *
 * @package Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/Tests
 */

/**
 * Test for Gdpr_Cookie_Consent_Public class
 */
class Test_Gdpr_Cookie_Consent_Public extends WP_UnitTestCase {

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
		self::$gdpr_cookie_consent = new Gdpr_Cookie_Consent_Public( self::$plugin_name, self::$plugin_version );
	}

	/**
	 * Test for construct function
	 */
	public function test__construct() {
		$obj = new Gdpr_Cookie_Consent_Public( self::$plugin_name, self::$plugin_version );
		$this->assertTrue( $obj instanceof Gdpr_Cookie_Consent_Public );

	}

	/**
	 * Test for enqueue styles function
	 */
	public function test_enqueue_styles() {
		self::$gdpr_cookie_consent->enqueue_styles();
		global $wp_styles;
		$enqueue_styles = $wp_styles->registered;
		$this->assertArrayHasKey( 'gdpr_cookie_consent', $enqueue_styles );
	}

	/**
	 * Test for enqueue scripts function
	 */
	public function test_enqueue_scripts() {
		self::$gdpr_cookie_consent->enqueue_scripts();
		global $wp_scripts;
		$enqueue_scripts = $wp_scripts->registered;
		$this->assertArrayHasKey( 'gdpr_cookie_consent', $enqueue_scripts );
		$this->assertArrayHasKey( 'gdpr_cookie_consent-bootstrap-js', $enqueue_scripts );
	}
	/**
	 * Test for public_modules function
	 */
	public function test_public_modules() {
		$modules = array(
			'cookie-custom' => 'abc',
			'policy-data'   => 'abc',
		);
		delete_option( 'gdpr_admin_modules' );
		self::$gdpr_cookie_consent->public_modules();
		update_option( 'gdpr_admin_modules', $modules );
		self::$gdpr_cookie_consent->public_modules();
		$get_admin_modules = get_option( 'gdpr_admin_modules' );
		$this->assertEquals( $modules, $get_admin_modules );

	}
}


