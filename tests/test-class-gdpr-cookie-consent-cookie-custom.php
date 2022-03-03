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
class Test_Gdpr_Cookie_Consent_Cookie_Custom extends WP_UnitTestCase {

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
		$user_id = self::factory()->user->create(
			array(
				'role' => 'administrator',
			)
		);
		if ( defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ) {
			self::$plugin_version = GDPR_COOKIE_CONSENT_VERSION;
		} else {
			self::$plugin_version = '2.0.7';
		}
		self::$plugin_name         = 'gdpr_cookie_consent';
		self::$gdpr_cookie_consent = new Gdpr_Cookie_Consent_Cookie_Custom();
	}
	/**
	 * Test for __construct function.
	 */
	public function test__construct() {
		$user_id = self::factory()->user->create(
			array(
				'role' => 'administrator',
			)
		);
		$this->assertTrue( user_can( $user_id, 'edit_others_posts' ) );
		$obj = new Gdpr_Cookie_Consent_Cookie_Custom();
		$this->assertTrue( $obj instanceof Gdpr_Cookie_Consent_Cookie_Custom );
	}
	/**
	 * Test for settings_general() function
	 */
	public function test_settings_general() {
		self::$gdpr_cookie_consent->settings_general();
		$this->assertTrue( true );
	}
	/**
	 * Test for settigns_tabhead function.
	 */
	public function test_settings_tabhead() {
		$arr = array(
			'abc' => 'abc',
		);
		$abc = self::$gdpr_cookie_consent->settings_tabhead( $arr );
		$this->assertArrayHasKey( 'gdpr-cookie-consent-cookie-list', $abc );
	}
	/**
	 * Test for gdpr_get_categories
	 */
	public function test_gdpr_get_categories() {
		$actual = self::$gdpr_cookie_consent->gdpr_get_categories();
		$arr    = array( 'analytics', 'marketing', 'necessary', 'preferences', 'unclassified' );
		for ( $i = 0;$i < 5;$i++ ) {
			$expected = $arr[ $i ];
			$this->assertEquals( $expected, $actual[ $i ]['slug'] );
		}
	}
	/**
	 * Test for get_categories() function
	 */
	public function test_get_categories() {
		$value = self::$gdpr_cookie_consent->get_categories( false );
		$this->assertTrue( ! empty( $value ) );
	}
	/**
	 * Test for get_types function
	 */
	public function test_get_types() {
		$types = array(
			'HTTP'        => __( 'HTTP Cookie', 'gdpr-cookie-consent' ),
			'HTML'        => __( 'HTML Local Storage', 'gdpr-cookie-consent' ),
			'Flash Local' => __( 'Flash Local Shared Object', 'gdpr-cookie-consent' ),
			'Pixel'       => __( 'Pixel Tracker', 'gdpr-cookie-consent' ),
			'IndexedDB'   => __( 'IndexedDB', 'gdpr-cookie-consent' ),
		);
		$value = self::$gdpr_cookie_consent->get_types();
		$this->assertEquals( $types, $value );
	}
	/**
	 * Test for get_cookie_type
	 */
	public function test_get_cookie_type() {
		$value = self::$gdpr_cookie_consent->get_cookie_type( 'HTTP' );
		$this->assertEquals( $value, 'HTTP Cookie' );
		$value = self::$gdpr_cookie_consent->get_cookie_type( 'HTTPs' );
		$this->assertEquals( $value, '' );
	}
	/**
	 * Test for print_combobox_options
	 */
	public function test_print_combobox_options() {
		$arr = array(
			'abc' => 'abc',
		);
		self::$gdpr_cookie_consent->print_combobox_options( $arr, 'abc' );
		$this->assertTrue( true );
	}
	/**
	 * Test for settings_form
	 */
	public function test_settings_form() {
		self::$gdpr_cookie_consent->settings_form();
		$this->assertTrue( true );
	}
	/**
	 * Test for gdpr_activator
	 */
	public function test_gdpr_activator() {
		self::$gdpr_cookie_consent->gdpr_activator();
		$this->assertTrue( true );
	}
	/**
	 * Test for gdpr_install_tables
	 */
	public function test_gdpr_install_tables() {
		self::$gdpr_cookie_consent->gdpr_install_tables();
		$this->assertTrue( true );
	}
	/**
	 * Test for get_cookies function
	 */
	public function test_get_cookies() {
		self::$gdpr_cookie_consent->get_cookies();
		$this->assertTrue( true );
	}
}


