<?php
/**
 * Class Test_Gdpr_Cookie_Consent_Loader
 *
 * @package Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/Tests
 */

/**
 * Test for Gdpr_Cookie_Consent_Loader class
 */
class Test_Gdpr_Cookie_Consent_Loader extends WP_UnitTestCase {

	/**
	 * Class Instance.
	 *
	 * @var object $gdpr_cookie_consent Class Instance
	 * @access public
	 */
	public static $gdpr_cookie_consent;

    /**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0
	 * @access   public
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
    public static $filters;

    /**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0
	 * @access   public
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
    public static $actions;

	/**
	 * Setup function for all tests.
	 *
	 * @param WP_UnitTest_Factory $factory helper for unit test functionality.
	 */
	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$gdpr_cookie_consent = new Gdpr_Cookie_Consent_Loader();
	}

    /**
     * Test __construct function.
     */
    public function test__construct(){
        $obj=new Gdpr_Cookie_Consent_Loader();
        $this->assertTrue($obj instanceof Gdpr_Cookie_Consent_Loader);
    }

    /**
     * Test add_action function.
     */
    public function test_add_action(){
        self::$gdpr_cookie_consent->add_action('hook', 'component', 'callback', $priority = 10, $accepted_args = 1 );
        $this->assertTrue(true);
    }
    /**
     * Test add_filter function.
     */
    public function test_add_filter(){
        self::$gdpr_cookie_consent->add_filter('hook', 'component', 'callback', $priority = 10, $accepted_args = 1 );
        $this->assertTrue(true); 
    }
    /**
     * Test run function.
     */
    public function test_run(){
        self::$gdpr_cookie_consent->run();
        $this->assertTrue(true);
    }
}


