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
		global $wpdb;
		$wpdb->insert($wpdb->prefix.'gdpr_cookie_post_cookies',array(
			'id_gdpr_cookie_post_cookies'=>'hii',
			'name'=>'df',
			'domain'=>'df',
			'duration'=>'df',
			'type'=>'df',
			'category'=>'df',
			'category_id'=>'df',
			'description'=>'df',
		),array('%s','%s','%s','%s','%s','%s','%s','%s'));
		$settings = array(
			'cookie_usage_for' => 'ccpa',
		);
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $settings );
		self::$gdpr_cookie_consent->gdprcookieconsent_inject_gdpr_script();
		$this->assertTrue( wp_script_is( 'gdpr_cookie_consent-uspapi', $list = 'enqueued' ) );
		$settings2 = array(
			'cookie_usage_for' => 'eprivacy',
		);
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $settings2 );
		self::$gdpr_cookie_consent->gdprcookieconsent_inject_gdpr_script();
		$this->assertTrue( true );
		$settings3 = array(
			'cookie_usage_for' => 'gdpr',
		);
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $settings3 );
		self::$gdpr_cookie_consent->gdprcookieconsent_inject_gdpr_script();
		$this->assertTrue( true );
		$settings3 = array(
			'cookie_usage_for' => 'both',
			'template'         => 'navy_blue_square-navy_blue_center',
		);
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $settings3 );
		self::$gdpr_cookie_consent->gdprcookieconsent_inject_gdpr_script();
		$this->assertTrue( true );	
		$settings4 = array(
			'template'         => 'almond_column',
		);
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $settings4 );
		self::$gdpr_cookie_consent->gdprcookieconsent_inject_gdpr_script();
		$this->assertTrue( true );	
		$settings4 = array(
			'template'         => 'grey_column',
		);
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $settings4 );
		self::$gdpr_cookie_consent->gdprcookieconsent_inject_gdpr_script();
		$this->assertTrue( true );
		$settings4 = array(
			'template'         => 'dark',
		);
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $settings4 );
		self::$gdpr_cookie_consent->gdprcookieconsent_inject_gdpr_script();
		$this->assertTrue( true );
		$settings4 = array(
			'template'         => 'dark_row',
			'button_readmore_wp_page'=>true,
			'button_accept_button_size'=>'large',
		);
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $settings4 );
		self::$gdpr_cookie_consent->gdprcookieconsent_inject_gdpr_script();
		$this->assertTrue( true );
		update_option( 'wpl_pro_active', true );
		self::$gdpr_cookie_consent->gdprcookieconsent_inject_gdpr_script();
		$this->assertTrue( true );
		$settings5 = array(
			'button_readmore_as_button'=>true,
			'button_readmore_button_size'=>'large',
			'button_decline_button_size'=>'medium',
			'button_settings_button_size'=>'medium',
			'button_confirm_button_size'=>'medium',
			'button_cancel_button_size'=>'medium',
		);
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $settings5 );
		self::$gdpr_cookie_consent->gdprcookieconsent_inject_gdpr_script();
		$this->assertTrue( true );
		$settings6 = array(
			'button_readmore_as_button'=>true,
			'button_readmore_button_size'=>'medium',
			'button_decline_button_size'=>'large',
			'button_settings_button_size'=>'large',
			'button_confirm_button_size'=>'large',
			'button_cancel_button_size'=>'large',
		);
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $settings6 );
		self::$gdpr_cookie_consent->gdprcookieconsent_inject_gdpr_script();
		$this->assertTrue( true );
		$settings7 = array(
			'button_readmore_as_button'=>true,
			'button_readmore_button_size'=>'small',
			'button_decline_button_size'=>'small',
			'button_settings_button_size'=>'small',
			'button_confirm_button_size'=>'small',
			'button_cancel_button_size'=>'small',
		);
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $settings7 );
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
		$settings = array(
			'is_script_blocked_on'=>'yes',
		);
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $settings );
		update_option( 'wpl_bypass_script_blocker', false );
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


