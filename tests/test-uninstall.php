<?php
/**
 * Class Gdpr_Cookie_Consent_Uninstall
 *
 * @package Auction_Software
 */

/**
 * Uninstall test for gdpr_cookie_consent
 */
class Gdpr_Cookie_Consent_Uninstall extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	public function test_uninstall() {
		define( 'WP_UNINSTALL_PLUGIN', true );
        include plugin_dir_path( GDPR_COOKIE_CONSENT_PLUGIN_FILENAME ) . 'uninstall.php';
		$this->assertTrue( true );
	}
}
