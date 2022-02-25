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
			self::$plugin_version = '2.0.7';
		}
		self::$plugin_name         = 'gdpr_cookie_consent';
		self::$gdpr_cookie_consent = new Gdpr_Cookie_Consent_Admin( self::$plugin_name, self::$plugin_version );
	}

	/**
	 * Test for construct function
	 */
	public function test__construct() {
		$obj = new Gdpr_Cookie_Consent_Admin( self::$plugin_name, self::$plugin_version );
		$this->assertTrue( $obj instanceof Gdpr_Cookie_Consent_Admin );

	}

	/**
	 * Test for enqueue styles function
	 */
	public function test_enqueue_styles() {
		self::$gdpr_cookie_consent->enqueue_styles();
		global $wp_styles;
		$enqueue_styles = $wp_styles->registered;
		$this->assertArrayHasKey( 'gdpr_cookie_consent', $enqueue_styles );
		$this->assertArrayHasKey( 'gdpr_cookie_consent-select2', $enqueue_styles );
	}

	/**
	 * Test for enqueue scripts function
	 */
	public function test_enqueue_scripts() {
		self::$gdpr_cookie_consent->enqueue_scripts();
		global $wp_scripts;
		$enqueue_scripts = $wp_scripts->registered;
		$this->assertArrayHasKey( 'gdpr_cookie_consent', $enqueue_scripts );
		$this->assertArrayHasKey( 'gdpr_cookie_consent-vue', $enqueue_scripts );
		$this->assertArrayHasKey( 'gdpr_cookie_consent-mascot', $enqueue_scripts );
		$this->assertArrayHasKey( 'gdpr_cookie_consent-select2', $enqueue_scripts );
	}
	/**
	 * Test for admin_modules function
	 */
	public function test_admin_modules() {
		$modules = array(
			'cookie-custom' => 'abc',
			'policy-data'   => 'abc',
		);
		delete_option( 'gdpr_admin_modules' );
		self::$gdpr_cookie_consent->admin_modules();
		update_option( 'gdpr_admin_modules', $modules );
		self::$gdpr_cookie_consent->admin_modules();
		$get_admin_modules = get_option( 'gdpr_admin_modules' );
		$this->assertEquals( $modules, $get_admin_modules );

	}
	/**
	 * Test for add_tabs function.
	 */
	public function test_add_tabs() {
		self::$gdpr_cookie_consent->add_tabs();
		set_current_screen( 'toplevel_page_gdpr-cookie-consent' );
		self::$gdpr_cookie_consent->add_tabs();
		$this->assertTrue( true );
	}

	/**
	 * Test for Admin_footer_text function.
	 */
	public function test_admin_footer_text() {
		$footer       = 'Test';
		$return_value = self::$gdpr_cookie_consent->admin_footer_text( $footer );
		$this->assertEquals( 'Test', $return_value );
		set_current_screen( 'toplevel_page_gdpr-cookie-consent' );
		$return_value = self::$gdpr_cookie_consent->admin_footer_text( $footer );
		$ans          = 'If you like <strong>GDPR Cookie Consent</strong> please leave us a <a href="https://wordpress.org/support/plugin/gdpr-cookie-consent/reviews?rate=5#new-post" target="_blank" aria-label="five star">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. A huge thanks in advance!';
		$this->assertEquals( $ans, $return_value );
	}
	/**
	 * Test for admin_menu function.
	 */
	public function test_admin_menu() {
		self::$gdpr_cookie_consent->admin_menu();
		$this->assertNotEmpty( menu_page_url( 'gdpr-cookie-consent' ) );
	}
	/**
	 * Test for admin_Plugin_actions_links function
	 */
	public function test_admin_plugin_action_links() {
		$links  = array();
		$links2 = self::$gdpr_cookie_consent->admin_plugin_action_links( $links );
		$links3 = array_merge(
			array(
				'<a href="' . esc_url( 'https://club.wpeka.com/product/wp-gdpr-cookie-consent/?utm_source=gdpr&utm_medium=plugins&utm_campaign=link&utm_content=upgrade-to-pro' ) . '" target="_blank" rel="noopener noreferrer"><strong style="color: #11967A; display: inline;">' . __( 'Upgrade to Pro', 'gdpr-cookie-consent' ) . '</strong></a>',
			),
			$links
		);
		$this->assertEquals( $links3, $links2 );
	}
	/**
	 * Test for admin_init function
	 */
	public function test_admin_init() {
		$arr  = array(
			'is_on'                  => 'abc',
			'button_1_selected_text' => 'abc',
		);
		$arr3 = array(
			'is_on'                      => true,
			'button_1_text'              => 'abc',
			'button_1_url'               => 'abc',
			'button_1_action'            => 'abc',
			'button_1_link_color'        => 'abc',
			'button_1_button_color'      => 'abc',
			'button_1_new_win'           => 'abc',
			'button_1_as_button'         => 'abc',
			'button_1_button_size'       => 'abc',
			'button_1_is_on'             => 'abc',
			'button_2_text'              => 'abc',
			'button_2_url'               => 'abc',
			'button_2_action'            => 'abc',
			'button_2_link_color'        => 'abc',
			'button_2_button_color'      => 'abc',
			'button_2_new_win'           => 'abc',
			'button_2_as_button'         => 'abc',
			'button_2_button_size'       => 'abc',
			'button_2_is_on'             => 'abc',
			'button_3_text'              => 'abc',
			'button_3_url'               => 'abc',
			'button_3_action'            => 'abc',
			'button_3_link_color'        => 'abc',
			'button_3_button_color'      => 'abc',
			'button_3_new_win'           => 'abc',
			'button_3_as_button'         => 'abc',
			'button_decline_button_size' => 'abc',
			'button_3_is_on'             => 'abc',
			'button_4_text'              => 'abc',
			'button_4_url'               => 'abc',
			'button_4_action'            => 'abc',
			'button_4_link_color'        => 'abc',
			'button_4_button_color'      => 'abc',
			'button_4_new_win'           => 'abc',
			'button_4_as_button'         => 'abc',
			'button_4_button_size'       => 'abc',
			'button_4_is_on'             => 'abc',
			'button_4_as_popup'          => 'abc',
		);
		update_option( 'GDPRCookieConsent-2.0', $arr );
		update_option( 'GDPRCookieConsent-3.0', $arr );
		update_option( 'GDPRCookieConsent-4.0', $arr3 );
		update_option( 'GDPRCookieConsent-5.0', $arr );
		update_option( 'GDPRCookieConsent-6.0', $arr );
		update_option( 'GDPRCookieConsent-7.0', $arr );
		update_option( 'GDPRCookieConsent-8.0', $arr );
		self::$gdpr_cookie_consent->admin_init();
		$this->assertTrue( ! get_option( 'GDPRCookieConsent-2.0' ) );
		$this->assertTrue( ! get_option( 'GDPRCookieConsent-3.0' ) );
		$this->assertTrue( ! get_option( 'GDPRCookieConsent-4.0' ) );
		$this->assertTrue( ! get_option( 'GDPRCookieConsent-5.0' ) );
		$this->assertTrue( ! get_option( 'GDPRCookieConsent-6.0' ) );
		$this->assertTrue( ! get_option( 'GDPRCookieConsent-7.0' ) );
		$this->assertTrue( ! get_option( 'GDPRCookieConsent-8.0' ) );
	}
	/**
	 * Test for Admin Settings Page function
	 */
	public function test_admin_settings_page() {
		wp_set_current_user(
			self::factory()->user->create(
				array(
					'role' => 'administrator',
				)
			)
		);
		self::$gdpr_cookie_consent->admin_settings_page();
		$this->assertTrue( true );
	}
	/**
	 * Test for gdpr_gettings_started function.
	 */
	public function test_gdpr_getting_started() {
		try {
			self::$gdpr_cookie_consent->gdpr_getting_started();
		} catch ( Exception $e ) {
			unset( $e );
			$this->assertTrue( true );
		}
		wp_set_current_user(
			self::factory()->user->create(
				array(
					'role' => 'administrator',
				)
			)
		);
		self::$gdpr_cookie_consent->gdpr_getting_started();
		global $wp_styles;
		$en_styles = $wp_styles->registered;
		$this->assertArrayHasKey( 'gdpr_cookie_consent', $en_styles );
	}

	/**
	 * Test for gdpr_import_page function
	 */
	public function test_gdpr_policies_import_page() {
		try {
			self::$gdpr_cookie_consent->gdpr_policies_import_page();
		} catch ( Exception $e ) {
			unset( $e );
			$this->assertTrue( true );
		}
		wp_set_current_user(
			self::factory()->user->create(
				array(
					'role' => 'administrator',
				)
			)
		);
		self::$gdpr_cookie_consent->gdpr_policies_import_page();
		$this->assertTrue( file_exists( plugin_dir_path( 'admin/views/gdpr-policies-import-page.php' ) ) );
	}
}


