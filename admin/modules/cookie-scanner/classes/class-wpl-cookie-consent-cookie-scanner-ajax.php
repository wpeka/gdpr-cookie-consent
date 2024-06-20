<?php
/**
 * The cookie scanning functionality of the plugin.
 *
 * @link       https://club.wpeka.com/
 * @since      3.0.0
 *
 * @package    Gdpr_Cookie_Consent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * The admin-specific ajax functionality for cookie scanner.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin/modules
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent_Cookie_Scanner_Ajax extends Gdpr_Cookie_Consent_Cookie_Scanner {

	/**
	 * Gdpr_Cookie_Consent_Cookie_Scanner_Ajax constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_wpl_cookie_scanner', array( $this, 'ajax_cookie_scanner' ) );
	}

	/**
	 * Main ajax hook for processing request.
	 */
	public function ajax_cookie_scanner() {
		$out = array(
			'response' => false,
			'message'  => __( 'Unable to handle your request.', 'gdpr-cookie-consent' ),
		);
		check_ajax_referer( 'wpl_cookie_scanner', 'security' );
		if ( ! current_user_can( 'manage_options' )){
			wp_die( esc_attr__( 'You do not have sufficient permission to perform this operation', 'gdpr-cookie-consent' ) );
		}
		if ( isset( $_POST['wpl_scanner_action'] ) ) {
			$wpl_scan_action = sanitize_text_field( wp_unslash( $_POST['wpl_scanner_action'] ) );
			$allowed_actions = array( 'get_pages', 'scan_pages', 'stop_scan', 'check_api', 'scan_cookie_list', 'update_scan_cookie', 'get_post_scan_cookies', 'get_scanned_cookies_list' );
			if ( in_array( $wpl_scan_action, $allowed_actions, true ) && method_exists( $this, $wpl_scan_action ) ) {
				$out = $this->{$wpl_scan_action}();
			}
		}
		echo wp_json_encode( $out );
		exit();
	}

	/**
	 * Returns scanned cookies list.
	 *
	 * @return array
	 */
	public function get_post_scan_cookies() {
		global $wpdb;
		check_ajax_referer( 'wpl_cookie_scanner', 'security' );
		$out_log    = array();
		$mxdata     = $this->scan_page_maxdata;
		$offset     = (int) isset( $_POST['offset'] ) ? sanitize_text_field( wp_unslash( $_POST['offset'] ) ) : 0;
		$scan_id    = (int) isset( $_POST['scan_id'] ) ? sanitize_text_field( wp_unslash( $_POST['scan_id'] ) ) : 0;
		$total      = (int) isset( $_POST['total'] ) ? sanitize_text_field( wp_unslash( $_POST['total'] ) ) : 0;
		$hash       = isset( $_POST['hash'] ) ? sanitize_text_field( wp_unslash( $_POST['hash'] ) ) : '';
		$new_offset = $offset + $mxdata;
		$out        = array(
			'log'           => array(),
			'offset'        => $offset,
			'scan_id'       => $scan_id,
			'total'         => $total,
			'total_scanned' => 0,
			'total_cookies' => 0,
			'response'      => true,
			'continue'      => true,
			'hash'          => $hash,
		);
		$data_arr   = array(
			'current_action' => 'get_post_scan_cookies',
		);
		$data = $wpdb->get_results( $wpdb->prepare( 'SELECT id_wpl_cookie_scan_url,url FROM ' . $wpdb->prefix . 'wpl_cookie_scan_url WHERE id_wpl_cookie_scan=%d ORDER BY id_wpl_cookie_scan_url ASC LIMIT %d,%d', array( $scan_id, $offset, $mxdata ) ), ARRAY_A ); // db call ok; no-cache ok.
		if ( ! empty( $data ) ) {
			$data_for_api = array(); // data for API request.
			$data_for_db  = array(); // data for insert into db.
			$url_id_arr   = array();
			foreach ( $data as $v ) {
				$data_for_api[]           = $v['url'];
				$data_for_db[ $v['url'] ] = $v['id_wpl_cookie_scan_url'];
				$url_id_arr[]             = $v['id_wpl_cookie_scan_url'];
			}
		}
		// Cookie serve API.
		include plugin_dir_path( __FILE__ ) . 'class-wpl-cookie-consent-cookie-serve-api.php';
		$cookie_serve_api = new Gdpr_Cookie_Consent_Cookie_Serve_Api();

		// Loop through the chunks becuase cookieserve only accept maximum 5 per request.
		$cookies_arr  = $cookie_serve_api->get_post_cookies( $hash );
		$cookies_arr  = json_decode( $cookies_arr );
		$policies_arr = isset( $cookies_arr->policies ) ? $cookies_arr->policies : '';
		// add new policy data.
		if ( isset( $policies_arr ) && ! empty( $policies_arr ) ) {
			foreach ( $policies_arr as $domain => $policy ) {
				$p_data = array();
				if ( ! empty( $policy ) ) {
					$p_data['company'] = isset( $policy->company ) ? $policy->company : '';
					$p_data['purpose'] = isset( $policy->purpose ) ? $policy->purpose : '';
					$p_data['links']   = isset( $policy->links ) ? $policy->links : '';
					$post_exists_id    = post_exists( $p_data['company'], '', '', GDPR_POLICY_DATA_POST_TYPE );
					// update existing posts.
					if ( $post_exists_id ) {
						$post_data = get_post( $post_exists_id );
						if ( isset( $post_data ) && ! empty( $post_data ) ) {
							$post_data->post_title   = $p_data['company'];
							$post_data->post_content = $p_data['purpose'];
						}
						$post_id = wp_update_post( $post_data );
					} else {
						$post_data = array(
							'post_title'   => $p_data['company'],
							'post_content' => $p_data['purpose'],
							'post_status'  => 'publish',
							'post_parent'  => 0,
							'post_type'    => GDPR_POLICY_DATA_POST_TYPE,
						);
						$post_id   = wp_insert_post( $post_data, true );
					}

					if ( $post_id ) {
						update_post_meta( $post_id, '_gdpr_policies_links_editor', $p_data['links'] );
						update_post_meta( $post_id, '_gdpr_policies_domain', $domain );
					}
				}
			}
		}
		$cookies_arr = isset( $cookies_arr->cookies ) ? $cookies_arr->cookies : '';
		if ( is_array( $cookies_arr ) ) {
			$out['response'] = true;
			$out['offset']   = $new_offset;
			if ( $new_offset >= $total ) {
				$out['continue'] = false;
			}
			$out['total_scanned'] = count( $data );
			foreach ( $cookies_arr as $cookies ) {
				$out_log = $this->insert_cookies( $scan_id, $url_id_arr[0], $data_for_api[0], $cookies, $out_log );
			}
			update_option( 'wpl_bypass_script_blocker', 0 );
		} else {
			$out['response'] = false;
		}
		// just give list of cookies.
		$cookies_list         = $this->get_scan_cookies( $scan_id, 0, 1 );
		$out['total_cookies'] = $cookies_list['total'];
		return $out;
	}

	/**
	 * Ajax processing for update scanned cookies.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function update_scan_cookie() {
		$out = array(
			'response' => false,
			'message'  => __( 'Unable to update cookies', 'gdpr-cookie-consent' ),
		);
		check_ajax_referer( 'wpl_cookie_scanner', 'security' );
		if ( isset( $_POST['cookie_arr'] ) ) {
			$cookie_arr = array();
			// The contents of $_POST['cookie_arr'] are being sanitised in the foreach loop.
			foreach ( wp_unslash( $_POST['cookie_arr'] ) as $arr ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				array_push( $cookie_arr, array_map( 'sanitize_text_field', wp_unslash( $arr ) ) );
			}
			$flag = 0;
			foreach ( $cookie_arr as $cookie ) {
				$cid       = isset( $cookie['cid'] ) ? sanitize_text_field( wp_unslash( $cookie['cid'] ) ) : '';
				$ccategory = isset( $cookie['ccategory'] ) ? sanitize_text_field( wp_unslash( $cookie['ccategory'] ) ) : '';
				$cdesc     = isset( $cookie['cdesc'] ) ? sanitize_text_field( wp_unslash( $cookie['cdesc'] ) ) : '';
				global $wpdb;
				$cat_data_arr = $wpdb->get_row( $wpdb->prepare( 'SELECT gdpr_cookie_category_name FROM ' . $wpdb->prefix . 'gdpr_cookie_scan_categories WHERE id_gdpr_cookie_category=%d', array( $ccategory ) ), ARRAY_A ); // db call ok; no-cache ok.
				if ( $cat_data_arr ) {
					$ccategoryname = $cat_data_arr['gdpr_cookie_category_name'];
				}
				$cookies_table = $wpdb->prefix . $this->cookies_table;
				$data_arr      = array();
				if ( ! empty( $ccategoryname ) ) {
					$data_arr['category'] = $ccategoryname;
				}
				if ( ! empty( $ccategory ) ) {
					$data_arr['category_id'] = $ccategory;
				}
				if ( ! empty( $cdesc ) ) {
					$data_arr['description'] = $cdesc;
				}
				$update_status = $wpdb->update( $cookies_table, $data_arr, array( 'id_wpl_cookie_scan_cookies' => $cid ) ); // db call ok; no-cache ok.
				if ( $update_status >= 1 ) {
					$flag            = 1;
					$out['response'] = true;
					$out['message']  = __( 'Cookies updated successfully', 'gdpr-cookie-consent' );
				} elseif ( 0 === $update_status ) {
					$out['message'] = __( 'No data was modified on the form.', 'gdpr-cookie-consent' );
				}
			}
			if ( 1 === $flag ) {
				$out['response'] = true;
				$out['message']  = __( 'Cookies updated successfully', 'gdpr-cookie-consent' );
			}
		}
		return $out;
	}

	/**
	 * Ajax processing for scanned cookie list.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function scan_cookie_list() {
		$out              = array(
			'response' => false,
		);
		$scan_cookie_list = $this->get_scan_cookie_list();
		$view             = plugin_dir_path( GDPR_COOKIE_CONSENT_PLUGIN_FILENAME ) . 'admin/modules/cookie-scanner/views/scan-cookie-list.php';
		ob_start();
		include $view;
		$contents = ob_get_clean();
		ob_get_flush();
		$out['response'] = true;
		$out['message']  = __( 'Success', 'gdpr-cookie-consent' );
		$out['content']  = $contents;
		return $out;
	}

	/**
	 * Function to return list of scanned cookies
	 */
	public function get_scanned_cookies_list() {
		$out = array(
			'response' => false,
		);
		check_admin_referer( 'wpl_cookie_scanner', 'security' );
		$scan_cookie_list = $this->get_scan_cookie_list();
		$out['response']  = true;
		$out['message']   = __( 'Success', 'gdpr-cookie-consent' );
		$out['total']     = $scan_cookie_list['total'];
		$out['data']      = $scan_cookie_list['data'];
		return $out;
	}

	/**
	 * Checks whether cookie serve api is available or not.
	 *
	 * @return array
	 */
	public function check_api() {
		$error_head = '<h3 style="color:#333333;">' . __( 'Oops! ', 'gdpr-cookie-consent' ) . '</h3>';

		$out = array(
			'message'  => $error_head . __( 'Your plugin version is outdated. Please update the plugin to scan.', 'gdpr-cookie-consent' ),
			'response' => false,
		);
		// Cookie serve API.
		include plugin_dir_path( __FILE__ ) . 'class-wpl-cookie-consent-cookie-serve-api.php';
		$cookie_serve_api = new Gdpr_Cookie_Consent_Cookie_Serve_Api();
		$response         = $cookie_serve_api->check_server();
		if ( $response ) {
			if ( 500 === $response ) {
				$out['response'] = false;
				$out['message']  = $error_head . __( 'Cookie Scanner API is not available now. Please try again later.', 'gdpr-cookie-consent' );
			}
			if ( 200 === $response ) {
				$out['response'] = true;
				$out['message']  = __( 'Success', 'gdpr-cookie-consent' );
			}
		}
		return $out;
	}

	/**
	 * Ajax processing for stop scanning.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function stop_scan() {
		check_ajax_referer( 'wpl_cookie_scanner', 'security' );
		$scan_id  = (int) isset( $_POST['scan_id'] ) ? sanitize_text_field( wp_unslash( $_POST['scan_id'] ) ) : 0;
		$data_arr = array( 'status' => 3 ); // updating scan status to stopped.
		$this->update_scan_entry( $data_arr, $scan_id );
		$cookies = $this->get_scan_cookies( $scan_id, 0, 1 ); // we just need total so `limit` argument is set as one.
		$out     = array(
			'log'     => array(),
			'scan_id' => $scan_id,
			'total'   => $cookies['total'],
		);
		update_option( 'wpl_bypass_script_blocker', 0 );
		return $out;
	}

	/**
	 * Ajax processing for scanning pages.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function scan_pages() {
		global $wpdb;
		check_ajax_referer( 'wpl_cookie_scanner', 'security' );
		$mxdata     = $this->scan_page_maxdata;
		$offset     = (int) isset( $_POST['offset'] ) ? sanitize_text_field( wp_unslash( $_POST['offset'] ) ) : 0;
		$scan_id    = (int) isset( $_POST['scan_id'] ) ? sanitize_text_field( wp_unslash( $_POST['scan_id'] ) ) : 0;
		$total      = (int) isset( $_POST['total'] ) ? sanitize_text_field( wp_unslash( $_POST['total'] ) ) : 0;
		$hash       = isset( $_POST['hash'] ) ? sanitize_text_field( wp_unslash( $_POST['hash'] ) ) : '';
		$new_offset = $offset + $mxdata;
		$out        = array(
			'log'           => array(),
			'offset'        => $new_offset,
			'scan_id'       => $scan_id,
			'total'         => $total,
			'total_scanned' => 0,
			'total_cookies' => 0,
			'response'      => true,
			'continue'      => true,
			'message'       => '',
		);
		$data_arr   = array(
			'current_action' => 'scan_pages',
			'current_offset' => $offset,
		);
		if ( $new_offset >= $total ) {
			$out['continue']    = false;
			$data_arr['status'] = 2; // setting finished status.
		} else {
			$data_arr['status'] = 1; // status uncompleted.
		}
		$this->update_scan_entry( $data_arr, $scan_id );
		$out = $this->scan_urls( $scan_id, $offset, $mxdata, $out, $hash );    
		// just give list of cookies.
		$cookies_list         = $this->get_scan_cookies( $scan_id, 0, 1 );
		$out['total_cookies'] = $cookies_list['total'];	
		return $out;
	}

	/**
	 * Ajax processing for get pages.
	 *
	 * @since 3.0.0
	 * @return array
     * @phpcs:disable
	 */
	public function get_pages() {
		global $wpdb;
		check_ajax_referer( 'wpl_cookie_scanner', 'security' );
		$post_table = $wpdb->prefix . 'posts';
		$mxdata     = $this->fetch_page_maxdata;
		// Taking query params.
		$offset  = (int) isset( $_POST['offset'] ) ? sanitize_text_field( wp_unslash( $_POST['offset'] ) ) : 0;
		$scan_id = (int) isset( $_POST['scan_id'] ) ? sanitize_text_field( wp_unslash( $_POST['scan_id'] ) ) : 0;
		$total   = (int) isset( $_POST['total'] ) ? sanitize_text_field( wp_unslash( $_POST['total'] ) ) : 0;
		$out     = array(
			'log'      => array(),
			'total'    => $total,
			'offset'   => $offset,
			'limit'    => $mxdata,
			'scan_id'  => $scan_id,
			'response' => true,
		);
		// Taking post types.
		$post_types = get_post_types(
			array(
				'public'   => true,
				'_builtin' => true,
			)
		);
		unset( $post_types['attachment'] );
		$the_options    = get_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
		$restrict_posts = $the_options['restrict_posts'];
			

		if (empty($restrict_posts) || implode( ',', $restrict_posts ) == "") {
			$sql = "SELECT post_name, post_title, post_type, ID FROM $post_table WHERE post_type IN ('" . implode("','", $post_types) . "') AND post_status = 'publish'";
		} else {
			$sql = "SELECT post_name, post_title, post_type, ID FROM $post_table WHERE post_type IN ('" . implode("','", $post_types) . "') AND post_status = 'publish' AND ID NOT IN (" . implode(',', $restrict_posts) . ')';
		}

		if ( 0 === $total ) {
			$restrict_posts_in_clause = implode( ',', $restrict_posts );
			if(empty($restrict_posts_in_clause)){
				$sql1 = "SELECT COUNT(*) as ttnum FROM ( SELECT 1 FROM $post_table WHERE post_type IN('" . implode( "','", $post_types ) . "') AND post_status='publish' LIMIT $offset, $mxdata) AS T";
			}else{
			$sql1 = "SELECT COUNT(*) as ttnum FROM ( SELECT 1 FROM $post_table WHERE post_type IN('" . implode( "','", $post_types ) . "') AND post_status='publish' AND ID NOT IN ($restrict_posts_in_clause) LIMIT $offset, $mxdata) AS T";
	
		}
			$total_rows   = $wpdb->get_row( $sql1, ARRAY_A );
			$total        = $total_rows ? $total_rows['ttnum'] + 1 : 1; // always add 1 because home url is there.
			$out['total'] = $total;
		}
		if ( 0 === $scan_id ) {
			$scan_id        = $this->create_scan_entry( $total );
			$out['scan_id'] = $scan_id;
			$out['log'][]   = get_home_url();
			$this->insert_url( $scan_id, get_home_url() );
		}

		// Creating sql for fetching data.
		// Initialize an empty string to store the additional SQL query
		$additionalSql = '';

		// Check if the $sql variable is not empty
		if (!empty($sql)) {
			// If $sql is not empty, concatenate it with the additional SQL query
			$additionalSql = ' ' . $sql;
		}

		// Construct the complete SQL query
		$sqlQuery = 'SELECT post_name, post_title, post_type, ID' . $additionalSql . ' ORDER BY post_type=\'page\' ASC LIMIT ' . $offset . ', ' . $mxdata;

		$data = $wpdb->get_results( $sql, ARRAY_A );
		if ( ! empty( $data ) ) {
			foreach ( $data as $value ) {
				$permalink = get_permalink( $value['ID'] );
				if ( $this->filter_url( $permalink ) ) {
					$out['log'][] = $permalink;
					$this->insert_url( $scan_id, $permalink );
				} else {
					$out['total'] = $out['total'] - 1;
				}
			}
		}
		// Saving current action status.
		// $data_arr = array(
		// 	'current_action' => 'get_pages',
		// 	'current_offset' => $offset,
		// 	'status'         => 1,
		// 	'total_url'      => $out['total'],
		// );
		// $this->update_scan_entry( $data_arr, $scan_id );
		return $out;
	}

	/**
	 * Filters non-html URLs.
	 *
	 * @since 3.0.0
	 * @param string $permalink URL.
	 *
	 * @return bool
	 * @phpcs:enable
	 */
	private function filter_url( $permalink ) {
		$url_arr = explode( '/', $permalink );
		$end     = trim( end( $url_arr ) );
		if ( '' !== $end ) {
			$url_end_arr = explode( '.', $end );
			if ( count( $url_end_arr ) > 1 ) {
				$end_end = trim( end( $url_end_arr ) );
				if ( '' !== $end_end ) {
					$allowed = array( 'html', 'htm', 'shtml', 'php' );
					if ( ! in_array( $end_end, $allowed, true ) ) {
						return false;
					}
				}
			}
		}
		return true;
	}

	/**
	 * Ajax processing for scanning URLs.
	 *
	 * @since 3.0.0
	 * @param int    $scan_id Scan ID.
	 * @param int    $offset Offset.
	 * @param int    $limit Limit.
	 * @param array  $out Response array.
	 * @param string $hash Hash.
	 *
	 * @return mixed
	 */
	private function scan_urls( $scan_id, $offset, $limit, $out, $hash ) {
		update_option( 'wpl_bypass_script_blocker', 1 );
		global $wpdb;
		$out_log = array();
		$data    = $wpdb->get_results( $wpdb->prepare( 'SELECT id_wpl_cookie_scan_url,url FROM ' . $wpdb->prefix . 'wpl_cookie_scan_url WHERE id_wpl_cookie_scan=%d ORDER BY id_wpl_cookie_scan_url ASC LIMIT %d,%d', array( $scan_id, $offset, $limit ) ), ARRAY_A ); // db call ok; no-cache ok.
		if ( ! empty( $data ) ) {
			$data_for_api = array(); // data for API request.
			$data_for_db  = array(); // data for insert into db.
			$url_id_arr   = array();
			foreach ( $data as $v ) {
				$data_for_api[]           = $v['url'];
				$data_for_db[ $v['url'] ] = $v['id_wpl_cookie_scan_url'];
				$url_id_arr[]             = $v['id_wpl_cookie_scan_url'];
			}
			$api_data_chunks = array_chunk( $data_for_api, $this->scan_page_maxdata ); // !important do not give value more than 5
			// Cookie serve API.
			include plugin_dir_path( __FILE__ ) . 'class-wpl-cookie-consent-cookie-serve-api.php';
			$cookie_serve_api = new Gdpr_Cookie_Consent_Cookie_Serve_Api();

			// Loop through the chunks becuase cookieserve only accept maximum 5 per request.
			foreach ( $api_data_chunks as $value ) {
				$response = $cookie_serve_api->get_cookies( $value, $hash );
				if ( 'false' !== $response ) {
					$out['response'] = true;
				} else {
					$out['response'] = false;
					$out['message']  = __( 'Cookie Scanner API is not available now. Please try again later.', 'gdpr-cookie-consent' );
				}
			}
			$out['total_scanned'] = count( $data );
		}
		$out['log'] = $out_log;
		return $out;
	}
}
new Gdpr_Cookie_Consent_Cookie_Scanner_Ajax();
