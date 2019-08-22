<?php
/**
 * The cookie api functionality of the plugin.
 *
 * @link       https://club.wpeka.com/
 * @since      1.0
 *
 * @package    Gdpr_Cookie_Consent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Gdpr_Cookie_Consent_Cookie_Serve_Api' ) ) {
	/**
	 * The admin-specific functionality for cookies api.
	 *
	 * @package    Gdpr_Cookie_Consent
	 * @subpackage Gdpr_Cookie_Consent/admin/modules
	 * @author     wpeka <https://club.wpeka.com>
	 */
	class Gdpr_Cookie_Consent_Cookie_Serve_Api {
		/**
		 * API Url.
		 *
		 * @since 1.0
		 * @access public
		 * @var string $gdpr_api_url API Url.
		 */
		public $gdpr_api_url = 'https://api.wpeka.com/wp-json/wplcookies/v2/';

		/**
		 * API Path to get category details.
		 *
		 * @since 1.0
		 * @access public
		 * @var string $gdpr_category_api_path API Path for category.
		 */
		public $gdpr_category_api_path = 'get_category_details';

		/**
		 * Gdpr_Cookie_Consent_Cookie_Serve_Api constructor.
		 */
		public function __construct() {
		}

		/**
		 * Fetch categories.
		 *
		 * @since 1.0
		 * @return array|mixed|object
		 */
		public function get_categories() {
			$out           = array();
			$response      = wp_remote_get( $this->gdpr_api_url . $this->gdpr_category_api_path );
			$response_code = wp_remote_retrieve_response_code( $response );
			if ( 200 === $response_code ) {
				$body = wp_remote_retrieve_body( $response );
				$out  = json_decode( $body, true );
			}
			return $out;
		}

		/**
		 * Check curl availability.
		 *
		 * @since 1.0
		 * @return bool
		 */
		public static function curl_enabled() {
			return function_exists( 'curl_version' );
		}
	}
}
