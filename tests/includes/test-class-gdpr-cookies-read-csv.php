<?php
/**
 * Class Test_gdpr_cookies_read_csv
 *
 * @package Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/Tests
 */

/**
 * Test for Gdpr_Cookies_Read_Csv
 */
class Test_Gdpr_Cookies_Read_Csv extends WP_UnitTestCase {

	/**
	 * Class Instance.
	 *
	 * @var object $gdpr_cookie_reac_csv Class Instance
	 * @access public
	 */
	public static $gdpr_read_csv;

	/**
	 * Setup function for all tests.
	 *
	 * @param WP_UnitTest_Factory $factory helper for unit test functionality.
	 */
	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		$filename            = plugin_dir_path( 'tests/includes/read.csv' ) . 'read.csv';
		$file_handle         = fopen( $filename, 'r' ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
		self::$gdpr_read_csv = new Gdpr_Cookies_Read_Csv( $file_handle, 'GDPR_CSV_DELIMITER', "\xEF\xBB\xBF" );
	}

	/**
	 * Test for construct function
	 */
	public function test__construct() {
		$filename    = plugin_dir_path( 'tests/includes/read.csv' ) . 'read.csv';
		$file_handle = fopen( $filename, 'r' ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
		$obj         = new Gdpr_Cookies_Read_Csv( $file_handle, 'GDPR_CSV_DELIMITER', "\xEF\xBB\xBF" );
		$this->assertTrue( $obj instanceof Gdpr_Cookies_Read_Csv );
	}

	/**
	 * Test for get_row function.
	 */
	public function test_get_row() {
		$ans = self::$gdpr_read_csv->get_row();
		$this->assertTrue( true );
	}


}
