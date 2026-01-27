<?php
/**
 * The cookie scanning functionality of the plugin.
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
require plugin_dir_path( __FILE__ ) . 'classes/class-wpl-cookie-consent-cookie-scanner-ajax.php';
/**
 * The admin-specific functionality for scanning cookies.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin/modules
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent_Cookie_Scanner {
	/**
	 * @var
	 */
	public $status_labels;
	/**
	 * Main cookie table.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string $main_table Main cookie table.
	 */
	public $main_table = 'wpl_cookie_scan';
	/**
	 * Scan url table.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string $url_table Url table.
	 */
	public $url_table = 'wpl_cookie_scan_url';
	/**
	 * Scan cookies table.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string $cookies_table Scan cookies table.
	 */
	public $cookies_table = 'wpl_cookie_scan_cookies';
	/**
	 * Scan categories table.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string $category_table Scan categories table.
	 */
	public $category_table = 'gdpr_cookie_scan_categories';
	/**
	 * Not to keep records flag.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var bool $not_keep_records Not to keep records flag.
	 */
	public $not_keep_records = true;
	/**
	 * Maximum url per request for scanning.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var int $scan_page_maxdata Url per request for scanning.
	 */
	public $scan_page_maxdata = 5;
	/**
	 * Maximum pages to fetch.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var int $fetch_page_maxdata Pages to fetch.
	 */
	public $fetch_page_maxdata = 100;
	/**
	 * Check if user is connected to the app.wplegalpages.com.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var bool $is_user_connected Keep records of user's connection.
	 */
	public $is_user_connected = false;
	/**
	 * The user's purchased plan.
	 *
	 * @since 3.8.1
	 * @access public
	 * @var string $plan Purchased plan of user.
	 */
	public $plan = '';
	/**
	 * Class for bluring the content.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string $class_for_blur_content Blur class for styling.
	 */
	public $class_for_blur_content = '';
	/**
	 * Class for making the body content blur.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string $class_for_card_body_blur_content Blur class for styling.
	 */
	public $class_for_card_body_blur_content = '';
	public $settings;

	/**
	 * Gdpr_Cookie_Consent_Cookie_Scanner constructor.
	 */
	public function __construct() {
		// Creating necessary tables for cookie scanner.
		register_activation_hook( GDPR_COOKIE_CONSENT_PLUGIN_FILENAME, array( $this, 'wpl_activator' ) );
		add_action('admin_init',  array($this, 'set_status_labels'));
		if ( Gdpr_Cookie_Consent::is_request( 'admin' ) ) {
			add_filter( 'gdprcookieconsent_cookie_sub_tabs', array( $this, 'wpl_cookie_sub_tabs' ), 10, 1 );
			add_action( 'gdpr_module_settings_cookielist', array( $this, 'wpl_cookie_scanned_cookies' ), 10 );
			add_action('admin_enqueue_scripts', array($this, 'register_cookie_scanner_script'));
			add_action( 'wp_ajax_wpl_cookie_scanner_card', array($this, 'wpl_cookie_scanner_card'));
			add_action( 'gdpr_cookie_scanned_history', array( $this, 'wpl_cookie_scanned_history_card' ), 10 );
			add_filter( 'gdpr_settings_cookie_scan_values', array( $this, 'wpl_settings_cookie_scan_values' ), 10, 1 );
			add_action( 'gdpr_scan_history_table', array( $this, 'wpl_scan_history_table' ), 5 );
		}
		add_filter( 'gdprcookieconsent_cookies', array( $this, 'wpl_get_scan_cookies' ), 10, 1 );


		// Require the class file for gdpr cookie consent api framework settings.
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';

		// Instantiate a new object of the GDPR_Cookie_Consent_Settings class.
		$this->settings = new GDPR_Cookie_Consent_Settings();
		// Call the is_connected() method from the instantiated object to check if the user is connected.
		$this->is_user_connected = $this->settings->is_connected();
		$this->plan                             = $this->settings->get_plan(); //Get user's plan.
		$this->class_for_blur_content = $this->is_user_connected ? '' : 'gdpr-blur-background'; // Add a class for styling purposes
		$this->class_for_card_body_blur_content = $this->is_user_connected ? '' : 'gdpr-body-blur-background'; // Add a class for styling purposes

	}
	public function set_status_labels(){
		$this->status_labels = array(
			0 => '',
			1 => __( 'Incomplete', 'gdpr-cookie-consent' ),
			2 => __( 'Completed', 'gdpr-cookie-consent' ),
			3 => __( 'Stopped', 'gdpr-cookie-consent' ),
		);
	}
	public function register_cookie_scanner_script(){
		//getting scan data 
		wp_enqueue_script('cookie_scanner_ajax', plugin_dir_url(__FILE__) . 'assets/js/cookie-scanner-data.js', array('jquery', 'gdpr-cookie-consent-admin-revamp'), '1.0', true);

		wp_localize_script('cookie_scanner_ajax', 'cookie_scanner_ajax', array(
			'ajax_url'         => admin_url( 'admin-ajax.php' )
		));
	}

	/**
	 * Filters cookie array with scanned cookies.
	 *
	 * @param Array $cookies_array Cookies array.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function wpl_get_scan_cookies( $cookies_array ) {
		$scan_cookies = $this->get_scan_cookie_list();
		$scan_cookies = $scan_cookies['data'];
		$cookie_array = array_merge( $scan_cookies, $cookies_array );
		$temp_arr     = array_unique( array_column( $cookie_array, 'name' ) );
		$cookie_array = array_intersect_key( $cookie_array, $temp_arr );
		return $cookie_array;
	}

	/**
	 * Callback to return cookie scan values
	 */
	public function wpl_settings_cookie_scan_values() {
		$scan_cookie_list = $this->get_scan_cookie_list();
		$last_scan = $this->get_last_scan();
		$error_message = '';
		$localhost_arr = array(
			'127.0.0.1',
			'::1',
		);
		$ip_address = $this->wplscan_get_user_ip();

		if ( ! $this->wpl_check_tables() || in_array( $ip_address, $localhost_arr, true ) ) {

			$error_message .= __( 'Unable to load cookie scanner.', 'gdpr-cookie-consent' );

			if ( in_array( $ip_address, $localhost_arr, true ) ) {
				$error_message .= ' ' . __( 'Scanning will not work on local server.', 'gdpr-cookie-consent' );
			}
		}
		$params    = array(
			'nonces'           => array(
				'wpl_cookie_scanner' => wp_create_nonce( 'wpl_cookie_scanner' ),
			),
			'ajax_url'         => admin_url( 'admin-ajax.php' ),
			'loading_gif'      => plugin_dir_url( __FILE__ ) . 'assets/images/loading.gif',
			'labels'           => array(
				'scanned'             => __( 'Scanned', 'gdpr-cookie-consent' ),
				'finished'            => __( 'Scanning completed.', 'gdpr-cookie-consent' ),
				'retrying'            => __( 'Unable to connect. Retrying...', 'gdpr-cookie-consent' ),
				'finding'             => __( 'Finding pages...', 'gdpr-cookie-consent' ),
				'scanning'            => __( 'Scanning pages...', 'gdpr-cookie-consent' ),
				'error'               => __( 'Error', 'gdpr-cookie-consent' ),
				'stop'                => __( 'Stop Scanning', 'gdpr-cookie-consent' ),
				'scan_again'          => __( 'Scan again', 'gdpr-cookie-consent' ),
				'cancel'              => __( 'Cancel', 'gdpr-cookie-consent' ),
				'reload_page'         => __( 'Error !!! Please reload the page to see cookie list.', 'gdpr-cookie-consent' ),
				'stoping'             => __( 'Stopping...', 'gdpr-cookie-consent' ),
				'scanning_stopped'    => __( 'Scanning stopped.', 'gdpr-cookie-consent' ),
				'ru_sure'             => __( 'Are you sure?', 'gdpr-cookie-consent' ),
				'success'             => __( 'Success', 'gdpr-cookie-consent' ),
				'thankyou'            => __( 'Thank you', 'gdpr-cookie-consent' ),
				'checking_api'        => __( 'Checking API', 'gdpr-cookie-consent' ),
				'sending'             => __( 'Sending...', 'gdpr-cookie-consent' ),
				'total_urls_scanned'  => __( 'Total URLs scanned', 'gdpr-cookie-consent' ),
				'total_cookies_found' => __( 'Total Cookies found', 'gdpr-cookie-consent' ),
			),
			'last_scan'        => $last_scan,
			'scan_cookie_list' => $scan_cookie_list,
			'error_message'    => $error_message,
		);
		return $params;
	}

	/**
	 * Returns IP address of the user.
	 *
	 * @since 3.0.0
	 * @return string
	 * @phpcs:disable
	 */
	public function wplscan_get_user_ip() {
		if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) && false === strpos( $_SERVER['HTTP_CLIENT_IP'], '127.0.0.1' ) ) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif( isset($_SERVER['HTTP_CF_CONNECTING_IP']) && false === strpos( $_SERVER['HTTP_CF_CONNECTING_IP'], '127.0.0.1' ) ) {
            $ipaddress = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && count( array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) )) > 0 && false === strpos( $_SERVER['HTTP_X_FORWARDED_FOR'], '127.0.0.1' ) ) {
            $xForwardedFor = $_SERVER['HTTP_X_FORWARDED_FOR'];
			$ipList = array_map('trim', explode(',', $xForwardedFor));

			$ipaddress = filter_var($ipList[0], FILTER_VALIDATE_IP);
        } elseif ( isset( $_SERVER['HTTP_X_FORWARDED'] ) && count( array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED'] ) )) > 0 && false === strpos( $_SERVER['HTTP_X_FORWARDED'], '127.0.0.1' ) ) {
            $xForwarded = $_SERVER['HTTP_X_FORWARDED'];
			$ipList = array_map('trim', explode(',', $xForwarded));

			$ipaddress = filter_var($ipList[0], FILTER_VALIDATE_IP);
        } elseif ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) && count( array_map('trim', explode(',', $_SERVER['HTTP_FORWARDED_FOR'] ) )) > 0 && false === strpos( $_SERVER['HTTP_FORWARDED_FOR'], '127.0.0.1' ) ) {
			$forwardedFor = $_SERVER['HTTP_FORWARDED_FOR'];
			$ipList = array_map('trim', explode(',', $forwardedFor));

			$ipaddress = filter_var($ipList[0], FILTER_VALIDATE_IP);
        } elseif ( isset( $_SERVER['HTTP_FORWARDED'] ) && count( array_map('trim', explode(',', $_SERVER['HTTP_FORWARDED'] ) )) > 0 && false === strpos( $_SERVER['HTTP_FORWARDED'], '127.0.0.1' ) ) {
			$forwarded = $_SERVER['HTTP_FORWARDED'];
			$ipList = array_map('trim', explode(',', $forwarded));

			$ipaddress = filter_var($ipList[0], FILTER_VALIDATE_IP);
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] )  && false === strpos( $_SERVER['REMOTE_ADDR'], '127.0.0.1' ) ) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = '127.0.0.1';
        }
        return $ipaddress;
	}

	/**
	 * Add a card for scanning cookies.
	 */
		public function wpl_cookie_scanner_card() {
			// check if pro is activated or installed.
			$installed_plugins = get_plugins();
			$pro_installed     = isset( $installed_plugins['wpl-cookie-consent/wpl-cookie-consent.php'] ) ? true : false;
			$pro_is_activated = get_option( 'wpl_pro_active', false );
			$api_key_activated = '';
			$api_key_activated = get_option( 'wc_am_client_wpl_cookie_consent_activated' );
			$last_scan = $this->get_last_scan();
			$error_message = '';
			$cookie_scan_settings = array();
			$cookie_scan_settings = apply_filters( 'gdpr_settings_cookie_scan_values', '' );
			global $wpdb;
			
			$localhost_arr = array(
				'127.0.0.1',
				'::1',
			);
			$ip_address = $this->wplscan_get_user_ip();

			if ( ! $this->wpl_check_tables() || in_array( $ip_address, $localhost_arr, true ) ) {

				$error_message .= __( 'Unable to load cookie scanner.', 'gdpr-cookie-consent' );

				if ( in_array( $ip_address, $localhost_arr, true ) ) {
					$error_message .= ' ' . __( 'Scanning will not work on local server.', 'gdpr-cookie-consent' );
				}
			}
			// Query to count the number of rows in the wp_wpl_cookie_scan table
			$cookie_scan_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wpl_cookie_scan" );

			// Check if the table is empty or not
			if ( $cookie_scan_count == 0 ) {
				// Table is empty, return 0
				$cookie_scan_count =  0;
			} else {
				// Table has rows, return 1
				$cookie_scan_count =  1;
			}

			if ( ! empty( $cookie_scan_settings ) ) {
				$total_no_of_found_cookies = $cookie_scan_settings['scan_cookie_list']['total'];
			} else {
				$total_no_of_found_cookies = 0;
			}
			$api_gdpr_cookie_scan = '';

			if ( 'free' !== $this->plan ) {
				delete_transient( 'gdpr_monthly_scan_limit_exhausted' );
			}

			/**
			 * Send a POST request to the GDPR API endpoint 'get_data'
			*/
			$response = wp_remote_post(
				GDPR_API_URL . 'get_cookie_scan_data',
				array(
					'body' => array(
						'error_message'       				=> $error_message,
						'gdpr_plugin_url'    				=> GDPR_COOKIE_CONSENT_PLUGIN_URL,
						'pro_installed' 			 		=> $pro_installed,
						'pro_is_activated'                  => $pro_is_activated,
						'api_key_activated'                 => $api_key_activated,
						'is_user_connected'         		=> $this->is_user_connected,
						'class_for_blur_content'    		=> $this->class_for_blur_content ,
						'class_for_card_body_blur_content'  => $this->class_for_card_body_blur_content ,
						'last_scan'         				=> $last_scan ,
						'cookie_scan_count'         	    => $cookie_scan_count ,
						'total_no_of_found_cookies'         => $total_no_of_found_cookies,
						'plan'                              => $this->plan,
						'is_monthly_limit_exhausted'        => (int) get_transient( 'gdpr_monthly_scan_limit_exhausted' ),
					),
					'timeout' => 60,
				)
			);

			// Check if there's an error with the request.
			if ( is_wp_error( $response ) ) {
				// Set $api_gdpr_cookie_scan to an empty string if there's an error.
				$api_gdpr_cookie_scan = '';
			}
			// Retrieve the response status code.
			$response_status = wp_remote_retrieve_response_code( $response );

			if (200 === $response_status) {
				$api_gdpr_cookie_scan = json_decode(wp_remote_retrieve_body($response));
				wp_send_json_success(['html' => $api_gdpr_cookie_scan]);
			} else {
				wp_send_json_error(['message' => __('Failed to retrieve data from server.', 'gdpr-cookie-consent')]);
			} ?>

			<?php
		}

	public function wpl_scan_history_table(){
		global $wpdb;

		// Query the data from wp_wpl_cookie_scan table
		$results = $wpdb->get_results( "SELECT created_at, status, total_url, total_cookies,total_category FROM {$wpdb->prefix}wpl_cookie_scan ORDER BY created_at DESC LIMIT 25" );
	
		// if ( empty( $results ) ) {
		// 	return new WP_Error( 'no_cookies', 'No cookie scan history found', array( 'status' => 404 ) );
		// }
	
		// Start building the HTML table
		$html = '<table class="cookie-scan-history-table">';
		$html .= '<thead class="cookie-scan-history-table-head"><tr>
					<th class="cookie-scan-history-table-head-data">Scan Date ( UTC + 00:00 )</th>
					<th class="cookie-scan-history-table-head-data">Scan Status</th>
					<th class="cookie-scan-history-table-head-data">URLs Scanned</th>
					<th class="cookie-scan-history-table-head-data">Categories</th>
					<th class="cookie-scan-history-table-head-data">Cookies</th>
				  </tr></thead>';
		$html .= '<tbody class="cookie-scan-history-table-body">';
	
		// Loop through each result and build table rows
		foreach ( $results as $row ) {
			// Convert timestamp to a readable date
			$scan_date = date( 'M d, Y g:i a T', $row->created_at );
	
			// Determine the scan status based on the `status` value
			$scan_status = '';
			switch ( $row->status ) {
				case 1:
					$scan_status = 'Not Completed';
					$div_class = 'cookie-scan-history-table-body-data-scan_status-incomplete';
					break;
				case 2:
					$scan_status = 'Completed';
					$div_class = 'cookie-scan-history-table-body-data-scan_status-completed'; 
					break;
				case 3:
					$scan_status = 'Stopped';
					$div_class = 'cookie-scan-history-table-body-data-scan_status-stopped'; 
					break;
			}
	
			// Create table rows with the retrieved data
			$html .= '<tr>';
			$html .= '<td class="cookie-scan-history-table-body-data">' . esc_html( $scan_date ) . '</td>';
			$html .= '<td class="cookie-scan-history-table-body-data">'. '<div class="' . $div_class  . '">' . $scan_status  . '</div></td>';
			$html .= '<td class="cookie-scan-history-table-body-data">' . esc_html( $row->total_url ) . '</td>';
			$html .= '<td class="cookie-scan-history-table-body-data">' . esc_html( $row->total_category ) . '</td>';
			$html .= '<td class="cookie-scan-history-table-body-data">' . esc_html( $row->total_cookies ) . '</td>';
			$html .= '</tr>';
		}
	
		$html .= '</tbody></table>';
		echo $html;
	}
	/**
	 * Add a card for scanned cookies history.
	 */
	public function wpl_cookie_scanned_history_card(){
		// check if pro is activated or installed.
		$installed_plugins = get_plugins();
		$pro_installed     = isset( $installed_plugins['wpl-cookie-consent/wpl-cookie-consent.php'] ) ? true : false;
		$pro_is_activated = get_option( 'wpl_pro_active', false );
		$api_key_activated = '';
		$api_key_activated = get_option( 'wc_am_client_wpl_cookie_consent_activated' );
		$last_scan = $this->get_last_scan();
		$error_message = '';
		global $wpdb;

		// Query the data from wp_wpl_cookie_scan table
		$results = $wpdb->get_results( "SELECT created_at, status, total_url, total_cookies FROM {$wpdb->prefix}wpl_cookie_scan LIMIT 25" );

		/**
		 * Send a POST request to the GDPR API endpoint 'get_data'
		*/

		$response = wp_remote_post(
			GDPR_API_URL . 'get_cookie_scan_history_data',
			array(
				'body' => array(
					'error_message'       				=> $error_message,
					'gdpr_plugin_url'    				=> GDPR_COOKIE_CONSENT_PLUGIN_URL,
					'pro_installed' 			 		=> $pro_installed,
					'pro_is_activated'                  => $pro_is_activated,
					'api_key_activated'                 => $api_key_activated,
					'is_user_connected'         		=> $this->is_user_connected,
					'class_for_blur_content'    		=> $this->class_for_blur_content ,
					'class_for_card_body_blur_content'  => $this->class_for_card_body_blur_content ,
					'last_scan'         				=> $last_scan ,
				),
				'timeout' => 60,
			)
		);

		// Check if there's an error with the request.
		if ( is_wp_error( $response ) ) {
			// Set $api_gdpr_cookie_scan_history to an empty string if there's an error.
			$api_gdpr_cookie_scan_history = '';
		}
		// Retrieve the response status code.
		$response_status = wp_remote_retrieve_response_code( $response );

		// Check if the response status is 200 (success).
		if ( 200 === $response_status ) {
			// Decode the JSON response body and assign it to $api_gdpr_cookie_scan.
			$api_gdpr_cookie_scan_history = json_decode( wp_remote_retrieve_body( $response ) );
		}

		?>

		<?php if(empty($results)){?>
			<!-- Cookie Scanning -->
			<?php echo $api_gdpr_cookie_scan_history; 

		}
		else{
			do_action( 'gdpr_scan_history_table' );
		}


	}
	/**
	 * Add tab menu for Scanning cookies.
	 *
	 * @since 3.0.0
	 */
	public function wpl_cookie_scanned_cookies() {
		$scan_cookie_list = $this->get_scan_cookie_list();
		wp_enqueue_script( 'wplcookieconsent_cookie_scanner', plugin_dir_url( __FILE__ ) . 'assets/js/cookie-scanner' . GDPR_CC_SUFFIX . '.js', array(), GDPR_COOKIE_CONSENT_VERSION, true );

		$last_scan = $this->get_last_scan();
		$params    = array(
			'nonces'           => array(
				'wpl_cookie_scanner' => wp_create_nonce( 'wpl_cookie_scanner' ),
			),
			'ajax_url'         => admin_url( 'admin-ajax.php' ),
			'loading_gif'      => plugin_dir_url( __FILE__ ) . 'assets/images/loading.gif',
			'labels'           => array(
				'scanned'             => __( 'Scanned', 'gdpr-cookie-consent' ),
				'finished'            => __( 'Scanning completed.', 'gdpr-cookie-consent' ),
				'retrying'            => __( 'Unable to connect. Retrying...', 'gdpr-cookie-consent' ),
				'finding'             => __( 'Finding pages...', 'gdpr-cookie-consent' ),
				'scanning'            => __( 'Scanning pages...', 'gdpr-cookie-consent' ),
				'error'               => __( 'Error', 'gdpr-cookie-consent' ),
				'stop'                => __( 'Stop Scanning', 'gdpr-cookie-consent' ),
				'scan_again'          => __( 'Scan again', 'gdpr-cookie-consent' ),
				'cancel'              => __( 'Cancel', 'gdpr-cookie-consent' ),
				'reload_page'         => __( 'Error !!! Please reload the page to see cookie list.', 'gdpr-cookie-consent' ),
				'stoping'             => __( 'Stopping...', 'gdpr-cookie-consent' ),
				'scanning_stopped'    => __( 'Scanning stopped.', 'gdpr-cookie-consent' ),
				'ru_sure'             => __( 'Are you sure?', 'gdpr-cookie-consent' ),
				'success'             => __( 'Success', 'gdpr-cookie-consent' ),
				'thankyou'            => __( 'Thank you', 'gdpr-cookie-consent' ),
				'checking_api'        => __( 'Checking API', 'gdpr-cookie-consent' ),
				'sending'             => __( 'Sending...', 'gdpr-cookie-consent' ),
				'total_urls_scanned'  => __( 'Total URLs scanned', 'gdpr-cookie-consent' ),
				'total_cookies_found' => __( 'Total Cookies found', 'gdpr-cookie-consent' ),
			),
			'last_scan'        => $last_scan,
			'scan_cookie_list' => $scan_cookie_list,
		);
		wp_localize_script( 'wplcookieconsent_cookie_scanner', 'wplcookieconsent_cookie_scanner', $params );

		$error_message = '';

		$localhost_arr = array(
			'127.0.0.1',
			'::1',
		);
		$ip_address = $this->wplscan_get_user_ip();

		if ( ! $this->wpl_check_tables() || in_array( $ip_address, $localhost_arr, true ) ) {

			$error_message .= __( 'Unable to load cookie scanner.', 'gdpr-cookie-consent' );

			if ( in_array( $ip_address, $localhost_arr, true ) ) {
				$error_message .= ' ' . __( 'Scanning will not work on local server.', 'gdpr-cookie-consent' );
			}
		}
		?>
		<div class="gdpr_cookie_sub_tab_content" data-id="discovered-cookies">
			<div class="gdpr_scanbar_staypage"><?php esc_attr_e( 'Please do not leave this page until the progress bar reaches 100%', 'gdpr-cookie-consent' ); ?></div>
			<div class="gdpr_scanbar">
				<div class="gdpr_infobox">
					<?php if ( '' === $error_message ) : ?>
						<?php
						if ( $last_scan ) {
							esc_attr_e( 'Last successful scan : ', 'gdpr-cookie-consent' );
							echo esc_attr( date( 'F j, Y g:i a T', $last_scan['created_at'] ) );
						} else {
							esc_attr_e( 'You haven\'t performed a site scan yet.', 'gdpr-cookie-consent' );
						}
						?>
					<a style="text-decoration:underline;cursor:pointer;" class="primary pull-right gdpr_scan_now"><?php esc_attr_e( 'Scan Now', 'gdpr-cookie-consent' ); ?></a>
						<?php
					else :
						echo esc_attr( $error_message );
					endif;
					?>
				</div>
			</div>
			<div id="scan_cookie_list">
				<?php
				if ( isset( $scan_cookie_list ) && $scan_cookie_list['total'] > 0 ) :
					if ( isset( $scan_cookie_list['data'] ) && ! empty( $scan_cookie_list['data'] ) ) :
						foreach ( $scan_cookie_list['data'] as $cookies_arr ) {
							?>
							<div class="form-table scan-cookie-list">
								<div class="left">
									<span class="cookie-text"><?php echo esc_attr( strtoupper( substr( stripslashes( $cookies_arr['name'] ), 0, 1 ) ) ); ?></span>
								</div>
								<div class="right">
									<div class="right-grid-1">
										<input type="hidden" name="id_<?php echo esc_attr( $cookies_arr['id_wpl_cookie_scan_cookies'] ); ?>" value="<?php echo esc_attr( $cookies_arr['id_wpl_cookie_scan_cookies'] ); ?>">
										<div class="input-box"><input disabled type="text" name="cookie_name_field_<?php echo esc_attr( $cookies_arr['id_wpl_cookie_scan_cookies'] ); ?>" value="<?php echo esc_attr( stripslashes( $cookies_arr['name'] ) ); ?>" placeholder="<?php esc_attr_e( 'Cookie Name', 'gdpr-cookie-consent' ); ?>" /></div>
										<div class="input-box"><input disabled type="text" name="cookie_domain_field_<?php echo esc_attr( $cookies_arr['id_wpl_cookie_scan_cookies'] ); ?>" value="<?php echo esc_attr( $cookies_arr['domain'] ); ?>" placeholder="<?php esc_attr_e( 'Cookie Domain', 'gdpr-cookie-consent' ); ?>" /></div>
									</div>
									<div class="right-grid-2">
										<div class="input-box"><select name="cookie_category_field_<?php echo esc_attr( $cookies_arr['id_wpl_cookie_scan_cookies'] ); ?>" class="vvv_combobox">
												<?php Gdpr_Cookie_Consent_Cookie_Custom::print_combobox_options( Gdpr_Cookie_Consent_Cookie_Custom::get_categories(), $cookies_arr['category_id'] ); ?>
											</select></div>
										<div class="input-box"><select disabled name="cookie_type_field_<?php echo esc_attr( $cookies_arr['id_wpl_cookie_scan_cookies'] ); ?>" class="vvv_combobox cookie-type-field">
												<?php Gdpr_Cookie_Consent_Cookie_Custom::print_combobox_options( Gdpr_Cookie_Consent_Cookie_Custom::get_types(), $cookies_arr['type'] ); ?>
											</select></div>
										<div class="input-box"><input disabled type="text" name="cookie_duration_field_<?php echo esc_attr( $cookies_arr['id_wpl_cookie_scan_cookies'] ); ?>" value="<?php echo esc_attr( stripslashes( $cookies_arr['duration'] ) ); ?>" class="cookie-duration-field" placeholder="<?php esc_attr_e( 'Cookie Duration (in days)', 'gdpr-cookie-consent' ); ?>" /></div>
									</div>
									<div class="right-grid-3">
										<div class="input-box"><textarea name="cookie_description_field_<?php echo esc_attr( $cookies_arr['id_wpl_cookie_scan_cookies'] ); ?>" class="vvv_textbox" placeholder="<?php esc_attr_e( 'Cookie Purpose', 'gdpr-cookie-consent' ); ?>" ><?php echo esc_attr( stripslashes( $cookies_arr['description'] ) ); ?></textarea></div>
									</div>
								</div>
							</div>
							<?php
						}
					endif;
				endif;
				?>
			</div>
		</div>
		<?php
	}

	public function wpl_cookie_sub_tabs( $tabs ) {
		$tabs['discovered-cookies'] = __( 'Discovered Cookies', 'gdpr-cookie-consent' );
		return $tabs;
	}

	/**
	 * Run during the plugin's activation to install required tables in database.
	 *
	 * @since 3.0.0
	 */
	public function wpl_activator() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		if ( is_multisite() ) {
			// Get all blogs in the network and activate plugin on each one.
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				$this->wpl_install_tables();
				restore_current_blog();
			}
		} else {
			$this->wpl_install_tables();
		}
	}

	/**
	 * Installs necessary tables.
	 *
	 * @since 3.0.0
	 */
	public function wpl_install_tables() {
		global $wpdb;

		// Creating main table.
		$table_name   = $wpdb->prefix . $this->main_table;
		$search_query = "SHOW TABLES LIKE '%" . $table_name . "%'";
		if ( ! $wpdb->get_results( $search_query, ARRAY_N ) ) {
			$create_table_sql = "CREATE TABLE `$table_name`(
			    `id_wpl_cookie_scan` INT NOT NULL AUTO_INCREMENT,
			    `status` INT NOT NULL DEFAULT '0',
			    `created_at` INT NOT NULL DEFAULT '0',
			    `total_url` INT NOT NULL DEFAULT '0',
			    `total_cookies` INT NOT NULL DEFAULT '0',
				`total_category` INT NOT NULL DEFAULT '0',
			    `current_action` VARCHAR(50) NOT NULL,
			    `current_offset` INT NOT NULL DEFAULT '0',
			    PRIMARY KEY(`id_wpl_cookie_scan`)
			);";
			dbDelta( $create_table_sql );
		}

		// Creating url table.
		$table_name   = $wpdb->prefix . $this->url_table;
		$search_query = "SHOW TABLES LIKE '%" . $table_name . "%'";
		if ( ! $wpdb->get_results( $search_query, ARRAY_N ) ) {
			$create_table_sql = "CREATE TABLE `$table_name`(
			    `id_wpl_cookie_scan_url` INT NOT NULL AUTO_INCREMENT,
			    `id_wpl_cookie_scan` INT NOT NULL DEFAULT '0',
			    `url` TEXT NOT NULL,
			    `scanned` INT NOT NULL DEFAULT '0',
			    `total_cookies` INT NOT NULL DEFAULT '0',
			    PRIMARY KEY(`id_wpl_cookie_scan_url`)
			);";
			dbDelta( $create_table_sql );
		}

		// Creating cookies table.
		$table_name   = $wpdb->prefix . $this->cookies_table;
		$search_query = "SHOW TABLES LIKE '%" . $table_name . "%'";
		if ( ! $wpdb->get_results( $search_query, ARRAY_N ) ) {
			$create_table_sql = "CREATE TABLE `$table_name`(
			    `id_wpl_cookie_scan_cookies` INT NOT NULL AUTO_INCREMENT,
			    `id_wpl_cookie_scan` INT NOT NULL DEFAULT '0',
			    `id_wpl_cookie_scan_url` INT NOT NULL DEFAULT '0',
			    `name` VARCHAR(50) NOT NULL,
			    `domain` VARCHAR(255) NOT NULL DEFAULT '',
			    `duration` VARCHAR(255),
			    `type` VARCHAR(255),
			    `category` VARCHAR(50) NOT NULL,
			    PRIMARY KEY(`id_wpl_cookie_scan_cookies`),
			    UNIQUE `cookie` (`id_wpl_cookie_scan`, `name`)
			);";
			dbDelta( $create_table_sql );
		}

		// Creating categories table.
		$table_name   = $wpdb->prefix . $this->category_table;
		$search_query = "SHOW TABLES LIKE '%" . $table_name . "%'";
		if ( ! $wpdb->get_results( $search_query, ARRAY_N ) ) {
			$create_table_sql = "CREATE TABLE `$table_name`(
				 `id_gdpr_cookie_category` INT NOT NULL AUTO_INCREMENT,
				 `gdpr_cookie_category_name` VARCHAR(50) NOT NULL,
				 `gdpr_cookie_category_slug` VARCHAR(50) NOT NULL,
				 `gdpr_cookie_category_description` TEXT  NULL,
				 PRIMARY KEY(`id_gdpr_cookie_category`),
				 UNIQUE `cookie` (`gdpr_cookie_category_name`)
			 );";
			dbDelta( $create_table_sql );
			$this->wpl_update_category_table();
		}

		$this->wpl_update_tables();
	}

	/**
	 * Updates category table.
	 *
	 * @since 3.0.0
	 */
	private function wpl_update_category_table() {
		global $wpdb;
		$cat_table  = $wpdb->prefix . $this->category_table;
		$categories = $this->wpl_get_categories();
		$cat_arr    = array();
		if ( ! empty( $categories ) ) {
			foreach ( $categories as $category ) {
				$cat_description = isset( $category['description'] ) ? addslashes( $category['description'] ) : '';
				$cat_category    = isset( $category['name'] ) ? $category['name'] : '';
				$cat_slug        = isset( $category['slug'] ) ? $category['slug'] : '';
				$wpdb->query( $wpdb->prepare( 'INSERT IGNORE INTO `' . $wpdb->prefix . 'gdpr_cookie_scan_categories` (`gdpr_cookie_category_name`,`gdpr_cookie_category_slug`,`gdpr_cookie_category_description`) VALUES (%s,%s,%s)', array( $cat_category, $cat_slug, $cat_description ) ) ); // db call ok; no-cache ok.
			}
		}
	}


	private function wpl_get_categories() {
		include plugin_dir_path( __FILE__ ) . '/classes/class-wpl-cookie-consent-cookie-serve-api.php';
		$cookie_serve_api = new Gdpr_Cookie_Consent_Cookie_Serve_Api();
		$categories       = $cookie_serve_api->get_categories();
		return $categories;
	}

	/**
	 * Updates required tables.
	 *
	 * @since 3.0.0
	 */
	private function wpl_update_tables() {
		global $wpdb;
		$table_name   = $wpdb->prefix . $this->cookies_table;
		$cat_table    = $wpdb->prefix . $this->category_table;
		$search_query = "SHOW COLUMNS FROM `$table_name` LIKE 'description'";
		if ( ! $wpdb->get_results( $search_query, ARRAY_N ) ) {
			$wpdb->query( "ALTER TABLE `$table_name` ADD `description` TEXT NULL DEFAULT '' AFTER `category`" );
		}
		$search_query = "SHOW COLUMNS FROM `$table_name` LIKE 'category_id'";
		if ( ! $wpdb->get_results( $search_query, ARRAY_N ) ) {
			$wpdb->query( "ALTER TABLE `$table_name` ADD `category_id` INT NOT NULL  AFTER `category`" );
			$wpdb->query( "ALTER TABLE `$table_name` ADD CONSTRAINT FOREIGN KEY (`category_id`) REFERENCES `$cat_table` (`id_gdpr_cookie_category`)" );

		}
	}

	/**
	 * Checking if necessary tables are installed.
	 *
	 * @return bool
	 */
	protected function wpl_check_tables() {
		global $wpdb;
		$out = true;
		// Checking main table.
		$table_name   = $wpdb->prefix . $this->main_table;
		$search_query = "SHOW TABLES LIKE '%" . $table_name . "%'";
		if ( ! $wpdb->get_results( $search_query, ARRAY_N ) ) {
			$out = false;
		}

		// Checking url table.
		$table_name   = $wpdb->prefix . $this->url_table;
		$search_query = "SHOW TABLES LIKE '%" . $table_name . "%'";
		if ( ! $wpdb->get_results( $search_query, ARRAY_N ) ) {
			$out = false;
		}

		// Checking cookies table.
		$table_name   = $wpdb->prefix . $this->cookies_table;
		$search_query = "SHOW TABLES LIKE '%" . $table_name . "%'";
		if ( ! $wpdb->get_results( $search_query, ARRAY_N ) ) {
			$out = false;
		}
		return $out;
	}

	/**
	 * Creates db entry for scanning.
	 *
	 * @since 3.0.0
	 * @param int $total_url Total urls scanned.
	 *
	 * @return int|string
	 */
	protected function create_scan_entry( $total_url = 0 ) {
		global $wpdb;
		$scan_table = $wpdb->prefix . $this->main_table;
		$data_arr   = array(
			'created_at'    => time(),
			'total_url'     => $total_url,
			'total_cookies' => 0,
			'status'        => 1,
		);
		if ( $wpdb->insert( $scan_table, $data_arr ) ) {
			return $wpdb->insert_id;
		} else {
			return '0';
		}
	}

	/**
	 * Updates scanning entry into db.
	 *
	 * @since 3.0.0
	 * @param array $data_arr Data.
	 * @param int   $scan_id Scan ID.
	 *
	 * @return bool
	 */
	public function update_scan_entry( $data_arr, $scan_id ) {
		global $wpdb;
		$scan_table = $wpdb->prefix . $this->main_table;
		if ( $wpdb->update( $scan_table, $data_arr, array( 'id_wpl_cookie_scan' => $scan_id ) ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Inserts URL into db.
	 *
	 * @since 3.0.0
	 * @param int    $scan_id Scan ID.
	 * @param string $permalink URL.
	 */
	protected function insert_url( $scan_id, $permalink ) {
		global $wpdb;
		$url_table = $wpdb->prefix . $this->url_table;
		$data_arr  = array(
			'id_wpl_cookie_scan' => $scan_id,
			'url'                => $permalink,
			'scanned'            => 0,
			'total_cookies'      => 0,
		);
		$wpdb->insert( $url_table, $data_arr );
	}

	/**
	 * Inserts cookies in db.
	 *
	 * @since 3.0.0
	 * @param int    $scan_id Scan ID.
	 * @param int    $url_id URL ID.
	 * @param string $url URL.
	 * @param array  $cookie_data Cookies data.
	 * @param array  $out Response.
	 *
	 * @return array
	 */
	protected function insert_cookies( $scan_id, $url_id, $url, $cookie_data, $out ) {
		global $wpdb;
		$out       = array();
		$url_table = $wpdb->prefix . $this->cookies_table;
		$cat_table = $wpdb->prefix . $this->category_table;
		if ( ! empty( $cookie_data ) ) {
			$sql         = "INSERT IGNORE INTO `$url_table` (`id_wpl_cookie_scan`,`id_wpl_cookie_scan_url`,`name`,`duration`,`domain`,`type`,`category`,`category_id`,`description`) VALUES ";
			$sql_arr     = array();
			$out[]       = $url;
			$name        = trim( $cookie_data->name );
			$duration    = trim( $cookie_data->duration );
			$type        = $cookie_data->type;
			$domain      = $cookie_data->domain;
			$category    = isset( $cookie_data->category ) ? $cookie_data->category : 'Unclassified';
			$description = addslashes( $cookie_data->description );
			$category_id = -1;
					switch ( $category ) {
						case 'Analytics':
							$category_id = 1;
							break;
						case 'Marketing':
							$category_id = 2;
							break;
						case 'Necessary':
							$category_id = 3;
							break;
						case 'Preferences':
							$category_id = 4;
							break;
						case 'Unclassified':
							$category_id = 5;
							break;
					}
			$out[]       = '&nbsp;&nbsp;&nbsp;' . $name;
			$sql_arr[]   = "('$scan_id','$url_id','$name','$duration','$domain','$type','$category','$category_id','$description')";
			$sql         = $sql . implode( ',', $sql_arr );
			$wpdb->query( $sql );
		}
		return $out;
	}

	/**
	 * Updates scanned url data into db.
	 *
	 * @since 3.0.0
	 * @param array $url_id_arr Scan url data.
	 */
	protected function update_url( $url_id_arr ) {
		global $wpdb;
		$url_table = $wpdb->prefix . $this->url_table;
		$sql       = "UPDATE `$url_table` SET `scanned`=1 WHERE id_wpl_cookie_scan_url IN(" . implode( ',', $url_id_arr ) . ')';
		$wpdb->query( $sql );
	}

	/**
	 * Returns last scan data from db.
	 *
	 * @since 3.0.0
	 * @return array|null|object|void
	 */
	protected function get_last_scan() {
		global $wpdb;
		$scan_table = $wpdb->prefix . $this->main_table;
		$sql        = "SELECT * FROM `$scan_table` ORDER BY id_wpl_cookie_scan DESC LIMIT 1";
		return $wpdb->get_row( $sql, ARRAY_A );
	}

	/**
	 * Returns urls to be scanned from db.
	 *
	 * @since 3.0.0
	 * @param int $scan_id Scan ID.
	 * @param int $offset Offset.
	 * @param int $limit Limit.
	 *
	 * @return array
	 */
	public function get_scan_urls( $scan_id, $offset = 0, $limit = 100 ) {
		global $wpdb;
		$out       = array(
			'total' => 0,
			'data'  => array(),
		);
		$url_table = $wpdb->prefix . $this->url_table;
		$count_sql = "SELECT COUNT(id_wpl_cookie_scan_url) AS ttnum FROM $url_table WHERE id_wpl_cookie_scan='$scan_id'";
		$count_arr = $wpdb->get_row( $count_sql, ARRAY_A );
		if ( $count_arr ) {
			$out['total'] = $count_arr['ttnum'];
		}

		$sql = "SELECT * FROM $url_table WHERE id_wpl_cookie_scan='$scan_id' ORDER BY id_wpl_cookie_scan_url ASC LIMIT $offset,$limit";

		$data_arr = $wpdb->get_results( $sql, ARRAY_A );
		if ( $data_arr ) {
			$out['data'] = $data_arr;
		}
		return $out;
	}

	/**
	 * Returns scanned cookies from db.
	 *
	 * @since 3.0.0
	 * @param int $scan_id Scan ID.
	 * @param int $offset Offset.
	 * @param int $limit Limit.
	 *
	 * @return array
	 */
	public function get_scan_cookies( $scan_id, $offset = 0, $limit = 100 ) {
		global $wpdb;
		$out           = array(
			'total' => 0,
			'data'  => array(),
		);
		$cookies_table = $wpdb->prefix . $this->cookies_table;
		$url_table     = $wpdb->prefix . $this->url_table;
		$cat_table     = $wpdb->prefix . $this->category_table;
		$count_sql     = "SELECT COUNT(id_wpl_cookie_scan_cookies) AS ttnum FROM $cookies_table WHERE id_wpl_cookie_scan='$scan_id'";
		$count_arr     = $wpdb->get_row( $count_sql, ARRAY_A );
		if ( $count_arr ) {
			$out['total'] = $count_arr['ttnum'];
		}

		$update_result = $wpdb->update(
			$wpdb->prefix . 'wpl_cookie_scan', // Adjust if your table name is different
			array('total_cookies' => $out['total']), // Set the new value
			array('id_wpl_cookie_scan' => $scan_id) // Replace 'id' with the actual primary key name
		);

		$cookie_scan_settings = array();
		$cookie_scan_settings = apply_filters( 'gdpr_settings_cookie_scan_values', '' );

		if ( ! empty( $cookie_scan_settings ) ) {
			$scan_cookie_list = $cookie_scan_settings['scan_cookie_list'];
			// Create an array to store unique category names.
			$unique_categories = array();
		
			// Loop through the 'data' sub-array.
			foreach ( $scan_cookie_list['data'] as $cookie ) {
				$category = $cookie['category'];
		
				// Check if the category is not already in the $uniqueCategories array.
				if ( ! in_array( $category, $unique_categories ) ) {
					// If it's not in the array, add it.
					$unique_categories[] = $category;
				}
			}
		
			// Count the number of unique categories.
			$categories = count( $unique_categories );
		} else {
			$categories = 0;
		}
		$update_result = $wpdb->update(
			$wpdb->prefix . 'wpl_cookie_scan', // Adjust if your table name is different
			array('total_category' => $categories), // Set the new value
			array('id_wpl_cookie_scan' => $scan_id) // Replace 'id' with the actual primary key name
		);

		$sql      = "SELECT * FROM $cookies_table INNER JOIN $cat_table ON $cookies_table.category_id = $cat_table.id_gdpr_cookie_category INNER JOIN $url_table ON $cookies_table.id_wpl_cookie_scan_url = $url_table.id_wpl_cookie_scan_url WHERE $cookies_table.id_wpl_cookie_scan='$scan_id' ORDER BY id_wpl_cookie_scan_cookies ASC" . ( $limit > 0 ? " LIMIT $offset,$limit" : '' );
		$data_arr = $wpdb->get_results( $sql, ARRAY_A );
		if ( $data_arr ) {
			$out['data'] = $data_arr;
		}
		return $out;
	}
	/**
	 * Returns scanned cookie list from db.
	 *
	 * @since 3.0.0
	 * @param int $offset Offset.
	 * @param int $limit Limit.
	 *
	 * @return array
	 */
		public function get_scan_cookie_list( $offset = 0, $limit = 100 ) {
			global $wpdb;
			$out           = array(
				'total' => 0,
				'data'  => array(),
			);
			$cookies_table = $wpdb->prefix . $this->cookies_table;
			$cat_table     = $wpdb->prefix . $this->category_table;
			$scan_table    = $wpdb->prefix . 'wpl_cookie_scan'; // Replace with your actual scan table name
		
			// Get the latest scan ID with current_action = 'scan_pages'
			$latest_scan_id = $wpdb->get_var("SELECT id_wpl_cookie_scan FROM $scan_table WHERE status = '2' ORDER BY created_at DESC LIMIT 1");
		
			if ($latest_scan_id) {
				$count_sql     = "SELECT COUNT(id_wpl_cookie_scan_cookies) AS ttnum FROM $cookies_table";
				$count_arr     = $wpdb->get_row($count_sql, ARRAY_A);
				if ( $count_arr ) {
					$out['total'] = $count_arr['ttnum'];
				}
		
				$sql      = $wpdb->prepare(
					"SELECT * FROM $cookies_table 
					 INNER JOIN $cat_table ON $cookies_table.category_id = $cat_table.id_gdpr_cookie_category 
					 ORDER BY id_wpl_cookie_scan_cookies ASC" . ( $limit > 0 ? " LIMIT %d,%d" : '' ),
					$offset, $limit
				);
		
				$data_arr = $wpdb->get_results($sql, ARRAY_A);
				if ( $data_arr ) {
					$out['data'] = $data_arr;
				}
			}
			
		
			return $out;
		}
		
		

	/**
	 * Deletes all previous scan records.
	 */
	public function flush_scan_records() {
		global $wpdb;
		$table_name = $wpdb->prefix . $this->main_table;
		$wpdb->query( "TRUNCATE TABLE $table_name" );
		$table_name = $wpdb->prefix . $this->url_table;
		$wpdb->query( "TRUNCATE TABLE $table_name" );
		$table_name = $wpdb->prefix . $this->cookies_table;
		$wpdb->query( "TRUNCATE TABLE $table_name" );
	}

}
new Gdpr_Cookie_Consent_Cookie_Scanner();