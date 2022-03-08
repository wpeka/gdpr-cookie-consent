<?php
/**
 * Class Gdpr_Cookie_Consent_File_Test
 *
 * @package Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/Tests
 */

/**
 * Test for Gdpr_Cookie_Consent_File_Test class
 */
class Gdpr_Cookie_Consent_File_Test extends WP_UnitTestCase {

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
		self::$plugin_name = 'gdpr_cookie_consent';
	}

	/**
	 * Test for activate_gdpr_cookie_consent.
	 */
	public function test_activate_gdpr_cookie_consent() {
		activate_gdpr_cookie_consent();
		$this->assertTrue( true );
	}
	/**
	 * Test for deactivate_gdpr_cookie_consent.
	 */
	public function test_deactivate_gdpr_cookie_consent() {
		deactivate_gdpr_cookie_consent();
		$this->assertTrue( true );
	}
	/**
	 * Test for gcc_fs.
	 */
	public function test_gcc_fs() {
		gcc_fs();
		$this->assertTrue( true );
	}
	/**
	 * Test for gdprcc_clean
	 */
	public function test_gdprcc_clean() {
		$var = array(
			'a' => 'abc',
		);
		gdprcc_clean( $var );
		$this->assertTrue( true );
		$var1 = 'abc';
		gdprcc_clean( $var1 );
		$this->assertTrue( true );
	}
}


