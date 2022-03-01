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
			'cookie-custom' => 1,
			'policy-data'   => 1,
		);
		update_option( 'gdpr_public_modules', false );
		self::$gdpr_cookie_consent->public_modules();
		update_option( 'gdpr_public_modules', $modules );
		self::$gdpr_cookie_consent->public_modules();
		$get_admin_modules = get_option( 'gdpr_public_modules' );
		$this->assertEquals( $modules, $get_admin_modules );
		$this->assertTrue( true );
	}
	/**
	 * Test for gdprcookieconsent_remove_hash function.
	 */
	public function test_gsprcookieconsent_remove_hash() {
		$expected = self::$gdpr_cookie_consent->gdprcookieconsent_remove_hash( '#abc' );
		$this->assertEquals( $expected, 'abc' );
	}
	/**
	 * Test for gdprcookieconsent_clean_async_url function.
	 */
	public function test_gdprcookieconsent_clean_async_url() {
		$expected = self::$gdpr_cookie_consent->gdprcookieconsent_clean_async_url( 'www.test.com' );
		$this->assertEquals( $expected, 'www.test.com' );
		$expected = self::$gdpr_cookie_consent->gdprcookieconsent_clean_async_url( 'www.test.com#async' );
		$this->assertEquals( $expected, "www.test.com' async='async" );

	}
	/**
	 * Test for gdprcookieconsent_inject_gdpr_script function.
	 */
	public function test_gdprcookieconsent_inject_gdpr_script() {
		self::$gdpr_cookie_consent->gdprcookieconsent_inject_gdpr_script();
		$this->assertTrue( true );
	}
	/**
	 * Test for gdprcookieconsent_shortcode_cookie_details function.
	 */
	public function test_gdprcookieconsent_shortcode_cookie_details() {
		$ans=self::$gdpr_cookie_consent->gdprcookieconsent_shortcode_cookie_details();
		$this->assertEquals($ans,'');
	}
	/**
	 * Test for gdprcookieconsent_template_redirect function.
	 */
	public function test_gdprcookieconsent_template_redirect() {
		self::$gdpr_cookie_consent->gdprcookieconsent_template_redirect();
		$this->assertTrue(true);
	}
	/**
	 * Test for gdprcookieconsent_output_header function.
	 */
	public function test_gdprcookieconsent_output_header() {
		self::$gdpr_cookie_consent->gdprcookieconsent_output_header();
		$this->assertTrue(true);
	}
	/**
	 * Test for gdprcookieconsent_output_body function.
	 */
	public function test_gdprcookieconsent_output_body() {
		self::$gdpr_cookie_consent->gdprcookieconsent_output_body();
		$this->assertTrue(true);
	}
	/**
	 * Test for gdprcookieconsent_output_footer function.
	 */
	public function test_gdprcookieconsent_output_footer() {
		self::$gdpr_cookie_consent->gdprcookieconsent_output_footer();
		$this->assertTrue(true);
	}
}


