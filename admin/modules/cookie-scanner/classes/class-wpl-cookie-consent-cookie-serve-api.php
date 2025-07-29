<?php
/**
 * The cookie api functionality of the plugin.
 *
 * @link       https://club.wpeka.com/
 * @since      3.0.0
 *
 * @package    Gdpr_Cookie_Consent
 */

// phpcs:disable
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
		 * @since 3.0.0
		 * @access public
		 * @var string $wpl_api_url API Url.
		 */
		public $wpl_api_url = 'https://api.wpeka.com/wp-json/wplcookies/v2/';
		/**
		 * API Path to get category details.
		 *
		 * @since 3.0.0
		 * @access public
		 * @var string $wpl_category_api_path API Path for category.
		 */
		public $wpl_category_api_path = 'get_category_details';
		/**
		 * API Path to get cookie details.
		 *
		 * @since 3.0.0
		 * @access public
		 * @var string $wpl_cookie_api_path API Path for cookie.
		 */
		public $wpl_cookie_api_path = 'get_cookie_details';
		/**
		 * API Path to get scanned cookie details.
		 *
		 * @since 3.0.0
		 * @access public
		 * @var string $wpl_post_cookie_api_path API Path for cookie.
		 */
		public $wpl_post_cookie_api_path = 'get_post_cookie_details';

		/**
		 * API path to check api version.
		 *
		 * @since 3.0.0
		 * @access public
		 * @var string $wpl_check_api_path API path for version.
		 */
		public $wpl_check_api_path = 'check_api_version';

		/**
		 * Gdpr_Cookie_Consent_Cookie_Serve_Api constructor.
		 */
		public function __construct() {
		}

		/**
		 * Fetch cookies.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $url_arr URL array to fetch cookies.
		 * @param string $hash Hash.
		 *
		 * @return array
		 */
		public function get_cookies( $url_arr, $hash ) {

			if ( get_option('gdpr_single_page_scan_url') ) {
				$url_arr = array( get_option('gdpr_single_page_scan_url') );
			}

			$no_of_scan_pages = count($url_arr);

			$final_no_of_scanned_pages = get_option('gdpr_no_of_page_scan') + $no_of_scan_pages;
			

			$out           = array();
			$url_arr       = json_encode( $url_arr );
			$site_url      = site_url();
			$response      = wp_remote_get( $this->wpl_api_url . $this->wpl_cookie_api_path . '?urls=' . $url_arr . '&hash=' . $hash . '&site_url=' . $site_url );
			$response_code = wp_remote_retrieve_response_code( $response );
			if ( 200 === $response_code ) {
				$body = wp_remote_retrieve_body( $response );
				return $body;
			} else {
				return false;
			}
		}

		/**
		 * API call to get scanned cookies.
		 *
		 * @since 3.0.0
		 * @param String $hash Hash.
		 *
		 * @return array|bool|mixed|object
		 */
		public function get_post_cookies( $hash ) {
			$response      = wp_remote_get( $this->wpl_api_url . $this->wpl_post_cookie_api_path . '?hash=' . $hash );
			$response_code = wp_remote_retrieve_response_code( $response );
			if ( 200 === $response_code ) {
				$body    = wp_remote_retrieve_body( $response );
				$cookies = json_decode( $body, true );
				return $cookies;
			} else {
				return false;
			}
		}

		/**
		 * Check api version.
		 *
		 * @since 3.0.0
		 * @return bool
		 */
		public function check_server() {
            $site_url      = site_url();
            $response      = wp_remote_get( $this->wpl_api_url . $this->wpl_check_api_path . '?ver=' . GDPR_COOKIE_CONSENT_VERSION . '&site_url=' . $site_url );
			if ( isset( $response->errors ) ) {
				return 500;
			} else {
				$response_code = wp_remote_retrieve_response_code( $response );
				return $response_code;
			}
		}

		/**
		 * Fetch categories.
		 *
		 * @since 3.0.0
		 * @return array|mixed|object
		 */
		public function get_categories() {
			$out           = array();
			$response      = wp_remote_get( $this->wpl_api_url . $this->wpl_category_api_path );
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
		 * @since 3.0.0
		 * @return bool
		 */
		public static function curl_enabled() {
			return function_exists( 'curl_version' );
		}
	}
}
