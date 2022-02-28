<?php
/**
 * Class Test_Gdpr_Cookie_Consent
 *
 * @package Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/Tests
 */

/**
 * Test for Gdpr_Cookie_Consent class
 */
class Test_Gdpr_Cookie_Consent extends WP_UnitTestCase {

	/**
	 * Class Instance.
	 *
	 * @var object $gdpr_cookie_consent Class Instance
	 * @access public
	 */
	public static $gdpr_cookie_consent;

	/**
	 * Setup function for all tests.
	 *
	 * @param WP_UnitTest_Factory $factory helper for unit test functionality.
	 */
	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$gdpr_cookie_consent = new Gdpr_Cookie_Consent();
	}

    /**
     * Test for __cnstruct function.
     */
    public function test__construct(){

        $obj = new Gdpr_Cookie_Consent();
		$this->assertTrue( $obj instanceof Gdpr_Cookie_Consent );
    }

    /**
     * Test is_request function
     * 
     * @param string $type Request Type.
     */
    public function test_is_request(){
        $type='admin';
        $request=self::$gdpr_cookie_consent->is_request($type);
        $this->assertTrue(!$request);
        $type='ajax';
        $request=self::$gdpr_cookie_consent->is_request($type);
        $this->assertTrue(!$request);
        $type='cron';
        $request=self::$gdpr_cookie_consent->is_request($type);
        $this->assertTrue(!$request);
        $type='frontend';
        $request=self::$gdpr_cookie_consent->is_request($type);
        $this->assertTrue($request);
    }
    
	/**
	 * Test for get_loader function
	 */
	public function test_get_loader() {
		$loader = self::$gdpr_cookie_consent->get_loader();
		$loader = (array) $loader;
		$it     = new RecursiveIteratorIterator( new RecursiveArrayIterator( $loader ) );
		$array  = array();
		foreach ( $it as $v ) {
			array_push( $array, $v );
		}
        fwrite(STDOUT,print_r($array));
		// Test for some hooks.
		$this->assertTrue( in_array( 'plugins_loaded', $array, true ) );
		$this->assertTrue( in_array( 'init', $array, true ) );
	}

    /**
     * Test run function.
     */
    public function test_run(){
        self::$gdpr_cookie_consent->run();
        $this->assertTrue(true);
    }

    /**
     * Test gdpr_envelope_settings_tabcontent function
     */
    public function test_gdpr_envelope_settings_tabcontent(){
        $variables=array(
            'post_cookie_list'=>'abc',
            'scripts_list'=>'abc',
            'category_list'=>'abc',
        );
        self::$gdpr_cookie_consent->gdpr_envelope_settings_tabcontent($class='abc', $target_id='abc', $view_file = '', $html = '', $variables, $need_submit_btn = 1, $error_message = '');
        $this->assertTrue(true);
        $view_file=plugin_dir_path( GDPR_COOKIE_CONSENT_PLUGIN_FILENAME ) . 'admin/views/admin-display-design.php';
        self::$gdpr_cookie_consent->gdpr_envelope_settings_tabcontent($class='abc', $target_id='abc', $view_file='admin/views/admin-display-save-button.php', $html = '', $variables, $need_submit_btn = 2, $error_message = '');
        $this->assertTrue(true);
    }

    /**
     * Test for gdpr_su_hex_shift function
     */
    public function test_gdpr_su_hex_shift(){
        $supplied_hex="#121212";
        $shift_method="down";
        $ans=self::$gdpr_cookie_consent->gdpr_su_hex_shift( $supplied_hex, $shift_method, $percentage = 101 );
        $this->assertEquals($supplied_hex,$ans);
        $supplied_hex="#121212";
        self::$gdpr_cookie_consent->gdpr_su_hex_shift( $supplied_hex, $shift_method, $percentage = 50 );
        $supplied_hex1="#12g";
        $ans1=self::$gdpr_cookie_consent->gdpr_su_hex_shift( $supplied_hex1, $shift_method, $percentage = 50 );
        $this->assertEquals('12g',$ans1);
        $supplied_hex2="#12121g";
        $ans2=self::$gdpr_cookie_consent->gdpr_su_hex_shift( $supplied_hex2, $shift_method, $percentage = 50 );
        $this->assertEquals('12121g',$ans2);
    }

    /**
     * Test for gdpr_get_json_settings functions
     */
    public function test_gdpr_get_json_settings(){
        $ans=self::$gdpr_cookie_consent->gdpr_get_json_settings();
        $this->assertTrue(!empty($ans));
    }

    /**
     * Test for get ccpa countries.
     */
    public function test_get_ccpa_countries(){
        $expected=self::$gdpr_cookie_consent->get_ccpa_countries();
        $this->assertTrue(!empty($expected));
    }

    /**
     * Test for get eu countries.
     */
    public function test_get_eu_countries(){
        $expected=self::$gdpr_cookie_consent->get_eu_countries();
        $this->assertTrue(!empty($expected));
    }
}


