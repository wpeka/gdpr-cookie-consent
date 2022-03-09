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
		// global $user_ID;
		// $new_post = array(
		// 'post_title' => 'My New Post',
		// 'post_content' => 'Lorem ipsum dolor sit amet...',
		// 'post_status' => 'publish',
		// 'post_date' => date('2022-08-08 22:22:22'),
		// 'post_author' => $user_ID,
		// 'post_type' => 'gdpr-cookie-consent',
		// 'post_category' => array(0)
		// );
		// $post_id = wp_insert_post($new_post);
	}

	/**
	 * Test for construct function
	 */
	public function test__construct() {
		set_current_screen( 'post-new.php' );
		$obj = new Gdpr_Cookie_Consent_Policy_Data();
		$this->assertTrue( $obj instanceof Gdpr_Cookie_Consent_Policy_Data );
		// $this->assertTrue(is_admin());
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
	/**
	 * Test for gdpr_metaboc_policies_links.
	 */
	// public function test_gdpr_metabox_policies_links() {
	// 	self::$gdpr_cookie_consent->gdpr_metabox_policies_links();
	// 	$this->assertTrue( true );
	// }
	/**
	 * Test for gdpr_process_csv_export_policies function
	 */
	// public function test_gdpr_process_csv_export_policies() {
	// 	self::$gdpr_cookie_consent->gdpr_process_csv_export_policies();
	// 	$this->assertTrue( true );
	// }
}



