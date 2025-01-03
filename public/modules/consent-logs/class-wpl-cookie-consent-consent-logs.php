<?php
/**
 * The consent logs functionality of the plugin.
 *
 * @link       https://club.wpeka.com/
 * @since      3.0.0
 *
 * @package    Gdpr_Cookie_Consent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GDPR_Cookie_Consent', 'wplconsentlogs' );

/**
 * The frontend-specific functionality for consent log.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public/modules
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent_Consent_Logs {

	/**
	 * Gdpr_Cookie_Consent_Consent_Logs constructor.
	 */
	public function __construct() {
		register_activation_hook( GDPR_COOKIE_CONSENT_PLUGIN_FILENAME, array( $this, 'wplcl_activator' ) );
		add_action( 'init', array( $this, 'wplcl_register_custom_post_type' ) );

		if ( Gdpr_Cookie_Consent::is_request( 'admin' ) ) {
			add_action( 'admin_menu', array( $this, 'wplcl_admin_menu' ), 12 );
			add_action( 'manage_edit-wplconsentlogs_columns', array( $this, 'wplcl_manage_edit_columns' ) );
			add_action( 'manage_posts_custom_column', array( $this, 'wplcl_manage_custom_columns' ) );
			add_filter( 'bulk_actions-edit-wplconsentlogs', array( $this, 'wplcl_remove_bulkactions' ) );
			add_action( 'admin_post_export.csv', array( $this, 'wplcl_process_csv_export' ) );
			add_action( 'admin_head-edit.php', array( $this, 'wplcl_add_export_button' ) );
			add_filter( 'manage_edit-wplconsentlogs_sortable_columns', array( $this, 'wplcl_manage_sortable_columns' ) );
			add_action( 'pre_get_posts', array( $this, 'wplcl_extend_admin_search' ) );
			add_action( 'gdpr_module_before_other_general', array( $this, 'wplcl_other_general' ), 5 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_custom_admin_styles' ) );
			add_action( 'admin_init', array( $this, 'wpl_cl_cookie_details_pie_chart' ) );
			add_action( 'gdpr_consent_log_table_dashboard', array( $this, 'consent_log_dashboard_table' ), 5 );
		}

		add_action( 'wp_ajax_nopriv_gdpr_log_consent_action', array( $this, 'wplcl_log_consent_action' ) );
		add_action( 'wp_ajax_gdpr_log_consent_action', array( $this, 'wplcl_log_consent_action' ) );
		add_action( 'wp_ajax_nopriv_gdpr_increase_page_view', array( $this, 'wplcl_increase_page_view' ) );
		add_action( 'wp_ajax_gdpr_gdpr_increase_page_view', array( $this, 'wplcl_increase_page_view' ) );
		add_action( 'wp_ajax_nopriv_gdpr_increase_ignore_rate', array( $this, 'wplcl_increase_ignore_rate' ) );
		add_action( 'wp_ajax_gdpr_gdpr_increase_ignore_rate', array( $this, 'wplcl_increase_ignore_rate' ) );
		add_action( 'wp_ajax_gdpr_collect_abtesting_data_action', array( $this, 'wplcl_collect_abtesting_data_action' ) );
		add_action( 'wp_ajax_nopriv_gdpr_collect_abtesting_data_action', array( $this, 'wplcl_collect_abtesting_data_action' ) );

		add_action( 'add_consent_log_data', array( $this, 'wplcl_consent_data_overview' ) );
	}
	/**
	 * This function is used for enqueuing the files required for consent log pdf library, javascript and CSS.
	 *
	 * @since 3.0.0
	 */
	public function enqueue_custom_admin_styles() {
		// Css for consent log table on admin section.
		wp_enqueue_style( 'custom-post-table-styles', plugin_dir_url( __FILE__ ) . '/wpl-consentlog-css.css' );
		// Js file for creating the dynamic pdf for consent log.
		wp_enqueue_script( 'custom-admin-script', plugin_dir_url( __FILE__ ) . '/wpl-consentlog-script.js', array(), '1.0.0', true );
		// jspdf library used for generating pdf.
		wp_enqueue_script( 'jspdf', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', array(), '2.5.1', true );
		wp_enqueue_script( 'html2canvas', 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js', array(), '1.4.1', true );
		// jspdf autotable library for creating tables in pdf.
		wp_enqueue_script( 'jspdf-autotable', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.6.0/jspdf.plugin.autotable.min.js', array(), '3.6.0', true );
		wp_script_add_data( 'jspdf-autotable', 'integrity', 'sha512-DgV2mIRy66quVbkj4yS6FN7cccMH/iPXhDOi/ckWIAANbOL78RuoaA6MAu9BAdYEyAdIuIm63LzsaFmHGd7L8w==' );
		wp_script_add_data( 'jspdf-autotable', 'crossorigin', 'anonymous' );
	}
	/**
	 * Settings for Cookies About message under General Tab.
	 *
	 * @since 3.0.0
	 */
	public function wplcl_other_general() {
		if ( class_exists( 'Gdpr_Cookie_Consent' ) ) {
			$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		}
		?>
		<tr valign="top">
			<th scope="row"><label for="logging_on_field"><?php esc_attr_e( 'Enable Consent Logging', 'gdpr-cookie-consent' ); ?></label></th>
			<td>
				<input type="radio" id="logging_on_field_yes" name="logging_on_field" class="styled wpl_bar_on" value="true" <?php echo ( true === $the_options['logging_on'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
				<input type="radio" id="logging_on_field_no" name="logging_on_field" class="styled" value="false" <?php echo ( false === $the_options['logging_on'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Extends the functionality to search custom post by IP Address field.
	 *
	 * @since 3.0.0
	 * @param object $query Query.
	 */
	public function wplcl_extend_admin_search( $query ) {
		$post_type     = GDPR_Cookie_Consent;
		$custom_fields = array(
			'_wplconsentlogs_ip',
			'_wplconsentlogs_country',
		);
		if ( ! is_admin() ) {
			return;
		}
		if ( isset( $query->query['post_type'] ) ) {
			if ( $post_type !== $query->query['post_type'] ) {
				return;
			}
		} else {
			return;
		}
		$search_term            = $query->query_vars['s'];
		$query->query_vars['s'] = '';
		if ( '' !== $search_term ) {
			$meta_query = array( 'relation' => 'OR' );
			foreach ( $custom_fields as $custom_field ) {
				array_push(
					$meta_query,
					array(
						'key'     => $custom_field,
						'value'   => $search_term,
						'compare' => 'LIKE',
					)
				);
			}
			$query->set( 'meta_query', $meta_query );
		}
	}

	/**
	 * Add custom column to sortables.
	 *
	 * @since 3.0.0
	 * @param array $columns Sortable columns.
	 *
	 * @return mixed
	 */
	public function wplcl_manage_sortable_columns( $columns ) {
		$columns['wplconsentlogsdate'] = 'wplconsentlogsdate';
		return $columns;
	}
	/**
	 * This function is used for fetching the cookies from the database to display in the pdf.
	 */
	public function fetch_cookie_scan_data() {
		global $wpdb;

		// Define the table name.
		$table_name = $wpdb->prefix . 'wpl_cookie_scan_cookies';

		// Define the SQL query to select the desired columns.
		$sql = "SELECT name, duration, category, description FROM $table_name";

		// Execute the SQL query.
		// $wpdb->prepare() is not needed as no values is injecting in the query.
		$results = $wpdb->get_results( $sql ); // phpcs:ignore 

		// Check if there are results.
		if ( $results ) {
			// Convert the results to an object.
			$data = (object) $results;
			return $data;
		}

		return null; // Return null if no data is found.
	}

	/**
	 * Run during the plugin's activation to install required tables in database.
	 *
	 * @since 3.0.0
	 *
	 * @phpcs:disable
	 */
	public function wplcl_activator()
	{
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		if (is_multisite()) {
			// Get all blogs in the network and activate plugin on each one.
			$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blog_ids as $blog_id) {
				switch_to_blog($blog_id);
				update_option('wpl_logs_admin', get_current_user_id());
				restore_current_blog();
			}
		} else {
			update_option('wpl_logs_admin', get_current_user_id());
		}
	}

	/**
	 * Add submenu.
	 *
	 * @since 3.0.0
	 * @phpcs:enable
	 */
	public function wplcl_admin_menu() {
		// add submenus here.
	}

	/**
	 * Adds Export to CSV button.
	 *
	 * @since 3.0.0
	 *
	 * @phpcs:disable
	 */
	public function wplcl_add_export_button()
	{
		wp_enqueue_style('gdpr-cookie-consent');
		global $current_screen;
		if (GDPR_Cookie_Consent !== $current_screen->post_type) {
			return;
		}
		$scan_export_menu = __('Export as CSV', 'gdpr-cookie-consent');
	?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				var s = jQuery('#post-search-input').val();
				jQuery("<a  href='<?php echo admin_url('admin-post.php?action=export.csv'); ?>&s=" + s + "' id='export_consent_logs' class='add-new-h2'><?php echo $scan_export_menu; ?></a>").insertAfter(".wrap h1");
			});
		</script>
		<?php
	}

	/**
	 * Process export consent logs to CSV.
	 *
	 * @since 3.0.0
	 * @phpcs:disable
	 */
	public function wplcl_process_csv_export()
	{
		global $wpdb;

		$wpdb->hide_errors();
		if (function_exists('apache_setenv')) {
			@apache_setenv('no-gzip', 1);
		}
		@ini_set('zlib.output_compression', 0);
		@ob_clean();

		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename=wpl-consent-logs.csv');
		header('Pragma: no-cache');
		header('Expires: 0');

		$fp         = fopen('php://output', 'w');
		$row        = array();
		$log_fields = array(
			'_wplconsentlogs_ip',
			'post_date_gmt',
			'_wplconsentlogs_userid',
			'_wplconsentlogs_details',
		);

		foreach ($log_fields as $column) {
			$row[] = self::format_data($column);
		}

		$row = array_map('self::wrap_column', $row);
		fwrite($fp, implode(',', $row) . "\n");
		unset($row);

		$wplcl_args = apply_filters(
			'wplcl_csv_export_args',
			array(
				'post_status' => array('publish'),
				'post_type'   => array(GDPR_Cookie_Consent),
				'orderby'     => 'ID',
				'numberposts' => -1,
				'order'       => 'ASC',
			)
		);
		if (isset($_GET['s']) && !empty($_GET['s'])) {
			$search                   = $_GET['s'];
			$search                   = trim($search);
			$wplcl_args['meta_query'] = array(
				'relation' => 'OR',
				array(
					'key'     => '_wplconsentlogs_ip',
					'value'   => $search,
					'compare' => 'LIKE',
				),
			);
		}

		$logs = get_posts($wplcl_args);

		if (!$logs || is_wp_error($logs)) {
			goto fpclosingarea;
		}

		if (!is_multisite()) {
			foreach ($logs as $log) {
				$row       = array();
				$meta_data = get_post_custom($log->ID);
				foreach ($log_fields as $column) {
					switch ($column) {
						case '_wplconsentlogs_ip':
							$row[] = self::format_data($meta_data['_wplconsentlogs_ip'][0]);
							break;
						case 'post_date_gmt':
							$time_utc = $log->post_date_gmt;
							$utc_timestamp = get_date_from_gmt($time_utc, 'U');
							$tz_string = wp_timezone_string();
							$timezone = new DateTimeZone($tz_string);
							$local_time = wp_date("Y-m-d H:i:s", $utc_timestamp, $timezone);
							$row[] = self::format_data($local_time);
							break;
						case '_wplconsentlogs_userid':
							$row[] = self::format_data($meta_data['_wplconsentlogs_userid'][0]);
							break;
						case '_wplconsentlogs_details':
							$consent_details     = !empty($meta_data['_wplconsentlogs_details'][0]) ? $meta_data['_wplconsentlogs_details'][0] : '';
							$cookies             = unserialize($consent_details);
							$wpl_viewed_cookie   = $cookies['wpl_viewed_cookie'];
							$wpl_user_preference = json_decode($cookies['wpl_user_preference']);
							$cookie_str          = 'wpl_viewed_cookie:' . $wpl_viewed_cookie . '|';
							foreach ($wpl_user_preference as $key => $value) :
								$cookie_str .= $key . ':' . $value . '|';
							endforeach;
							$cookie_str = substr($cookie_str, 0, -1);
							$row[]      = self::format_data($cookie_str);
							break;
						default:
							break;
					}
				}
				$row = array_map('self::wrap_column', $row);
				fwrite($fp, implode(',', $row) . "\n");
				unset($row);
			}
		} else {
			foreach ($logs as $log) {
				$row       = array();
				$meta_data = get_post_custom($log->ID);
				foreach ($log_fields as $column) {
					switch ($column) {
						case '_wplconsentlogs_ip':
							$row[] = self::format_data($meta_data['_wplconsentlogs_ip_cf'][0]);
							break;
						case 'post_date_gmt':
							$time_utc = $log->post_date_gmt;
							$utc_timestamp = get_date_from_gmt($time_utc, 'U');
							$tz_string = wp_timezone_string();
							$timezone = new DateTimeZone($tz_string);
							$local_time = wp_date("Y-m-d H:i:s", $utc_timestamp, $timezone);
							$row[] = self::format_data($local_time);
							break;
						case '_wplconsentlogs_userid':
							$row[] = self::format_data($meta_data['_wplconsentlogs_userid_cf'][0]);
							break;
						case '_wplconsentlogs_details':
							$consent_details     = !empty($meta_data['_wplconsentlogs_details_cf'][0]) ? $meta_data['_wplconsentlogs_details_cf'][0] : '';
							$cookies             = unserialize($consent_details);
							$wpl_viewed_cookie   = $cookies['wpl_viewed_cookie'];
							$wpl_user_preference = json_decode($cookies['wpl_user_preference']);
							$cookie_str          = 'wpl_viewed_cookie:' . $wpl_viewed_cookie . '|';
							foreach ($wpl_user_preference as $key => $value) :
								$cookie_str .= $key . ':' . $value . '|';
							endforeach;
							$cookie_str = substr($cookie_str, 0, -1);
							$row[]      = self::format_data($cookie_str);
							break;
						default:
							break;
					}
				}
				$row = array_map('self::wrap_column', $row);
				fwrite($fp, implode(',', $row) . "\n");
				unset($row);
			}
		}
		unset($logs);
		fpclosingarea:
		fclose($fp);
		exit;
	}

	/**
	 * Format data for CSV.
	 *
	 * @since 3.0.0
	 * @param string $data Data for formatting.
	 *
	 * @return bool|string
	 */
	public static function format_data($data)
	{
		$enc  = mb_detect_encoding($data, 'UTF-8, ISO-8859-1', true);
		$data = ($enc == 'UTF-8') ? $data : utf8_encode($data);
		return $data;
	}

	/**
	 * Wrap a column in quotes for CSV.
	 *
	 * @since 3.0.0
	 * @param string $data Data for column wrapping.
	 *
	 * @return string
	 * @phpcs:enable
	 */
	public static function wrap_column( $data ) {
		return '"' . str_replace( '"', '""', $data ) . '"';
	}

	public function wplcl_collect_abtesting_data_action() {
		check_ajax_referer( 'wpl_consent_logging_nonce', 'security' );
		$ab_option = get_option('wpl_ab_options');
		if($ab_option['ab_testing_enabled'] == false || $ab_option['ab_testing_enabled'] == "false") return;  //do not collect data if a/b testing is disabled.
		$chosenBanner = $_POST['chosenBanner'];
		$user_preference = $_POST['user_preference'];
		if($chosenBanner == 1){
			if($user_preference == "no choice"){
				$ab_option["noChoice1"]++;
			}
			else if($user_preference == "reject"){
				$ab_option["noChoice1"]--;
			}
			else{
				foreach($user_preference as $category => $value){
					if($value == "yes" && ($category == "necessary" || $category == "marketing" || $category == "analytics"|| $category == "DNT")) $ab_option[$category."1"]++;
				}
				if($category != "DNT") $ab_option["noChoice1"]--;
			}
		}
		else{
			if($user_preference == "no choice"){
				$ab_option["noChoice2"]++;
			}
			else if($user_preference == "reject"){
				$ab_option["noChoice2"]--;
			}
			else{
				foreach($user_preference as $category => $value){
					if($value == "yes" && ($category == "necessary" || $category == "marketing" || $category == "analytics"|| $category == "DNT")) $ab_option[$category."2"]++;
				}
				if($category != "DNT") $ab_option["noChoice2"]--;
			}
		}
		update_option('wpl_ab_options',$ab_option);
	}

	/**
	 * Increase ignore count
	 * 
	 * @since 6.3.5
	 */
	public function wplcl_increase_ignore_rate(){
			
		$wpl_total_ignore_count = get_option('wpl_total_ignore_count');
		if($wpl_total_ignore_count === false){
			add_option("wpl_total_ignore_count", 0);
			$wpl_total_ignore_count = 0;
		}
		$wpl_total_ignore_count++;
		update_option('wpl_total_ignore_count', $wpl_total_ignore_count);
	}
	/**
	 * Increase pageview count
	 * 
	 * @since 6.3.5
	 */
	public function wplcl_increase_page_view(){
		$key = date('M d, Y');
		$wpl_page_views = get_option('wpl_page_views');
		if($wpl_page_views === false){
			add_option("wpl_page_views", []);
			$wpl_page_views = [];
		}	
		$wpl_total_page_views = get_option('wpl_total_page_views');
		if($wpl_total_page_views === false){
			add_option("wpl_total_page_views", 0);
			$wpl_page_views = 0;
		}
    	// Check if the key exists in the $wpl_page_views array
		if (isset($wpl_page_views[$key])) {
			// If the key exists, increment its value
			$wpl_page_views[$key] += 1;
		} else {
			// If the key doesn't exist, create it and set its value to 1
			$wpl_page_views[$key] = 1;
		}
		$wpl_total_page_views++;
		update_option('wpl_page_views', $wpl_page_views);
		update_option('wpl_total_page_views', $wpl_total_page_views);
	}
	/**
	 * Save consent logs.
	 *
	 * @since 3.0.0
	 */
	public function wplcl_log_consent_action() {
		check_ajax_referer( 'wpl_consent_logging_nonce', 'security' );
		$settings      = Gdpr_Cookie_Consent::gdpr_get_settings();
		$selectedsites = $settings['select_sites'];

		if ( ! empty( $_POST ) && $settings['logging_on'] ) {
			$js_cookie_list     = array();
			$wpl_cookie_details = array();
			if ( isset( $_POST['subSiteId'] ) ) {
				$subSiteId = sanitize_text_field( wp_unslash( $_POST['subSiteId'] ) );
			}
			if ( isset( $_POST['currentSite'] ) ) {
				$SiteURL = esc_url( $_POST['currentSite'] );
			}
			if ( isset( $_POST['consent_forward'] ) ) {
				$consent_forward = $_POST['consent_forward'];
			}
			if ( isset( $_POST['gdpr_user_action'] ) ) {
				$gdpr_user_action = sanitize_text_field( wp_unslash( $_POST['gdpr_user_action'] ) );
				if ( isset( $_POST['cookie_list'] ) && is_array( $_POST['cookie_list'] ) ) {
					foreach ( wp_unslash( $_POST['cookie_list'] ) as $key => $val ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						$js_cookie_list[ $key ] = sanitize_text_field( wp_unslash( $val ) );
					}

				}
				switch ( $gdpr_user_action ) {
					case 'accept':
					case 'accept_all':
					case 'reject':
					case 'bypassed':
						if ( isset( $js_cookie_list ) ) {
							foreach ( $js_cookie_list as $key => $val ) {
								if ( strpos( $key, 'wpl_user_preference' ) !== false ) {
									$wpl_cookie_details[ $key ] = $val;
								}
								if ( strpos( $key, 'wpl_tc_string' ) !== false) {
									$wpl_cookie_details['wpl_tc_string'] = $val;
								}
							}
							$wpl_cookie_details['wpl_viewed_cookie'] = $js_cookie_list['wpl_viewed_cookie'];
						}
						break;
					case 'confirm':
					case 'cancel':
						if ( isset( $js_cookie_list ) ) {
							$wpl_cookie_details['wpl_optout_cookie'] = $js_cookie_list['wpl_optout_cookie'];
						}
						break;
				}
			}

			$args['consent_details'] = $wpl_cookie_details;
			$subSiteId               = $subSiteId ?? null;

			if ( ( is_multisite() && $consent_forward && $this->wpl_insert_consent_log( $args, $subSiteId, $SiteURL, $consent_forward ) ) || ( is_multisite() && isset( $subSiteId ) && $this->wpl_insert_consent_log( $args, $subSiteId, $SiteURL, $consent_forward ) ) || ( ! is_multisite() && $this->wpl_insert_consent_log( $args, null, $SiteURL, $consent_forward ) ) ) {
				$data = array( 'message' => __( 'Consent Logged Successfully.', 'gdpr-cookie-consent' ) );
			} else {
				$data = array( 'message' => __( 'Error.', 'gdpr-cookie-consent' ) );
			}
			wp_send_json_success( $data );
		} else {
			$data = array( 'message' => __( 'Consent Logging is not enabled.', 'gdpr-cookie-consent' ) );
			wp_send_json_success( $settings );
		}
	}
	/**
	 * Save consent log into custom post type.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $args Consent details.
	 * @param int    $subSiteId Subsite ID.
	 * @param string $SiteURL Site URL.
	 * @param bool   $consent_forward Consent forward flag.
	 *
	 * @return bool|int|WP_Error
	 *
	 * @phpcs:enable
	 */
	public function wpl_insert_consent_log( $args, $subSiteId, $SiteURL, $consent_forward ) {
		if ( class_exists( 'Gdpr_Cookie_Consent' ) ) {
			$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		}
		$user_id = get_option( 'wpl_logs_admin', true );
		if ( $subSiteId ) {
			switch_to_blog( $subSiteId );
		}
		$post_data = array(
			'post_author'   => isset( $user_id ) ? $user_id : 0,
			'post_date'     => gmdate( 'Y-m-d H:i:s', strtotime( 'now' ) ),
			'post_date_gmt' => gmdate( 'Y-m-d H:i:s', strtotime( 'now' ) ),
			'post_title'    => 'wplconsentlog',
			'post_name'     => ( sanitize_title( 'wplconsentlog' ) ),
			'post_status'   => 'publish',
			'post_parent'   => 0,
			'post_type'     => GDPR_Cookie_Consent,
		);
		$post_id   = wp_insert_post( $post_data, true );
		if ( $subSiteId ) {

			restore_current_blog();
		}

		// }

		if ( $post_id ) {
			$details = $args['consent_details'];
			$user_id = get_current_user_id();
			$user_ip = $this->wpl_get_user_ip();
			// Fetch country information using ip-api.com.
			$api_url  = 'http://ip-api.com/json/' . $user_ip;
			$response = wp_safe_remote_get( $api_url );
			if ( is_wp_error( $response ) ) {
				$data = 'Unknown'; // Handle the error gracefully.
			} else {
				$body = wp_remote_retrieve_body( $response );
				$data = json_decode( $body );
			}
			if ( isset($data) && property_exists( $data, 'country' ) ) {
				$user_country = $data->country;
			} else {
				$user_country = 'unknown';
			}
			if ( is_multisite() && $consent_forward == true ) {
				global $wpdb;
				switch_to_blog( $subSiteId );

				update_post_meta( $post_id, '_wplconsentlogs_ip_cf', $user_ip );
				update_post_meta( $post_id, '_wplconsentlogs_userid_cf', $user_id );
				update_post_meta( $post_id, '_wplconsentlogs_details_cf', $details );
				update_post_meta( $post_id, '_wplconsentlogs_country_cf', $user_country );
				update_post_meta( $post_id, '_wplconsentlogs_siteurl_cf', $SiteURL );
				update_post_meta( $post_id, '_wplconsentlogs_consent_forward_cf', $consent_forward );
				restore_current_blog();
			} else {
				update_post_meta( $post_id, '_wplconsentlogs_ip', $user_ip );
				update_post_meta( $post_id, '_wplconsentlogs_userid', $user_id );
				update_post_meta( $post_id, '_wplconsentlogs_details', $details );
				update_post_meta( $post_id, '_wplconsentlogs_country', $user_country );
				update_post_meta( $post_id, '_wplconsentlogs_siteurl', $SiteURL );
				update_post_meta( $post_id, '_wplconsentlogs_consent_forward', $consent_forward );
			}

			return $post_id;
		} else {
			return false;
		}
	}

	/**
	 * Returns IP address of the user for consent log.
	 *
	 * @since 3.0.0
	 * @return string
	 *
	 * @phpcs:disable
	 */
	public function wpl_get_user_ip()
	{
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ipaddress = filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP);
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ipaddress = filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP);
		} elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
			$ipaddress = filter_var($_SERVER['HTTP_X_FORWARDED'], FILTER_VALIDATE_IP);
		} elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			$ipaddress = filter_var($_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP);
		} elseif (isset($_SERVER['HTTP_FORWARDED'])) {
			$ipaddress = filter_var($_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP);
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ipaddress = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
		} else {
			$ipaddress = 'UNKNOWN';
		}
		return esc_html($ipaddress);
	}

	/**
	 * Register custom post type for consent logs.
	 *_wplconsentlogs_siteurl
	 * @since 3.0.0
	 * @phpcs:enable
	 */
	public function wplcl_register_custom_post_type() {
		$labels = array(
			'name'          => __( 'Consent Logs', 'gdpr-cookie-consent' ),
			'singular_name' => __( 'Consent Logs', 'gdpr-cookie-consent' ),
			'search_items'  => __( 'Search Logs', 'gdpr-cookie-consent' ),
			'not_found'     => __( 'No logs found', 'gdpr-cookie-consent' ),
		);
		$args   = array(
			'labels'              => $labels,
			'public'              => false,
			'hierarchical'        => false,
			'rewrite'             => false,
			'query_var'           => false,
			'delete_with_user'    => false,
			'can_export'          => true,
			'publicly_queryable'  => false,
			'show_in_menu'        => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'supports'            => array( 'title' ),
			'capabilities'        => array(
				'publish_posts'       => 'manage_options',
				'edit_posts'          => 'manage_options',
				'edit_others_posts'   => false,
				'delete_posts'        => 'manage_options',
				'delete_others_posts' => 'manage_options',
				'edit_post'           => 'manage_options',
				'create_posts'        => 'do_not_allow',
				'delete_post'         => 'manage_options',
				'read_post'           => 'manage_options',
			),
		);
		register_post_type( GDPR_Cookie_Consent, $args );
	}

	/**
	 * Modify custom post type column headers.
	 *
	 * @since 3.0.0
	 * @param array $columns custom post type columns.
	 *
	 * @return array
	 */
	public function wplcl_manage_edit_columns( $columns ) {
		$columns = array(
			'cb'                    => '<input type="checkbox" />',
			'wplconsentlogsip'      => '<div style="text-align: center;">' . __( 'IP Address', 'gdpr-cookie-consent' ) . '</div>',
			'wplconsentlogsdates'   => '<div style="text-align: center;">' . __( 'Visited Date', 'gdpr-cookie-consent' ) . '</div>',
			'wplconsentlogscountry' => '<div style="text-align: center;">' . __( 'Country', 'gdpr-cookie-consent' ) . '</div>',
			'wplconsentlogstatus'   => '<div style="text-align: center;">' . __( 'Consent Status', 'gdpr-cookie-consent' ) . '</div>',
		);
		if ( is_multisite() ) {
			$columns['wplconsentlogsforwarded'] = '<div style="text-align: center;">' . __( 'Forwarded From ', 'gdpr-cookie-consent' ) . '</div>';
		}
		$columns['wplconsentlogspdf'] = '<div style="text-align: center;">' . __( 'Proof Of Consent', 'gdpr-cookie-consent' ) . '</div>';
		return $columns;
	}

	/**
	 * Consent Log details for the insights pie chart.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function wpl_cl_cookie_details_pie_chart() {

		global $wpdb;
		if ( class_exists( 'Gdpr_Cookie_Consent' ) ) {
			$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		}
		$custom              = get_post_custom();
		$forwarded_site_url  = isset( $custom['_wplconsentlogs_siteurl_cf'][0] ) ? $custom['_wplconsentlogs_siteurl_cf'][0] : null;
		$is_consent_status   = isset( $custom['_wplconsentlogs_consent_forward_cf'][0] ) ? $custom['_wplconsentlogs_consent_forward_cf'][0] : null;
		$forwarded_site_url1 = isset( $custom['_wplconsentlogs_siteurl'][0] ) ? $custom['_wplconsentlogs_siteurl'][0] : null;
		$is_consent_status1  = isset( $custom['_wplconsentlogs_consent_forward'][0] ) ? $custom['_wplconsentlogs_consent_forward'][0] : null;
		$siteurl             = site_url();
		$siteurl             = trailingslashit( $siteurl );
		if ( ! is_multisite() ) {
			$meta_key         = '_wplconsentlogs_details';
			$trash_meta_key   = '_wp_trash_meta_status';
			$trash_meta_value = 'publish';

			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$wpdb->prefix}postmeta
				 WHERE meta_key = %s
				 AND post_id NOT IN (
					 SELECT post_id
					 FROM {$wpdb->prefix}postmeta
					 WHERE meta_key = %s AND meta_value = %s
				 )",
					$meta_key,
					$trash_meta_key,
					$trash_meta_value
				)
			);
		} elseif ( $siteurl == $forwarded_site_url1 && $is_consent_status1 != true ) {
			$meta_key         = '_wplconsentlogs_details';
			$trash_meta_key   = '_wp_trash_meta_status';
			$trash_meta_value = 'publish';

			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$wpdb->prefix}postmeta
				 WHERE meta_key = %s
				 AND post_id NOT IN (
					 SELECT post_id
					 FROM {$wpdb->prefix}postmeta
					 WHERE meta_key = %s AND meta_value = %s
				 )",
					$meta_key,
					$trash_meta_key,
					$trash_meta_value
				)
			);
		} elseif ( $siteurl !== $forwarded_site_url && $is_consent_status == true ) {
			$meta_key         = '_wplconsentlogs_details_cf';
			$trash_meta_key   = '_wp_trash_meta_status';
			$trash_meta_value = 'publish';

			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$wpdb->prefix}postmeta
					 WHERE meta_key = %s
					 AND post_id NOT IN (
						 SELECT post_id
						 FROM {$wpdb->prefix}postmeta
						 WHERE meta_key = %s AND meta_value = %s
					 )",
					$meta_key,
					$trash_meta_key,
					$trash_meta_value
				)
			);
		} elseif ( $siteurl == $forwarded_site_url && $is_consent_status == true ) {
			$meta_key         = '_wplconsentlogs_details_cf';
			$trash_meta_key   = '_wp_trash_meta_status';
			$trash_meta_value = 'publish';

			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$wpdb->prefix}postmeta
					 WHERE meta_key = %s
					 AND post_id NOT IN (
						 SELECT post_id
						 FROM {$wpdb->prefix}postmeta
						 WHERE meta_key = %s AND meta_value = %s
					 )",
					$meta_key,
					$trash_meta_key,
					$trash_meta_value
				)
			);
		} else {
			$meta_key         = '_wplconsentlogs_details_cf';
			$trash_meta_key   = '_wp_trash_meta_status';
			$trash_meta_value = 'publish';

			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$wpdb->prefix}postmeta
					 WHERE meta_key = %s
					 AND post_id NOT IN (
						 SELECT post_id
						 FROM {$wpdb->prefix}postmeta
						 WHERE meta_key = %s AND meta_value = %s
					 )",
					$meta_key,
					$trash_meta_key,
					$trash_meta_value
				)
			);
		}
		$processedData = array();

		foreach ( $results as $item ) {
			$metaValue = unserialize( $item->meta_value ); // Deserialize the serialized data.
			if ( isset( $metaValue['wpl_user_preference'] ) ) {
				$wplUserPreference = json_decode( $metaValue['wpl_user_preference'], true ); // Decode the JSON data.
			} else {
				$wplUserPreference = array(); // Default value if the key doesn't exist.
			}

			$processedData[] = array(
				'post_id'             => $item->post_id,
				'wpl_user_preference' => $wplUserPreference,
				'wpl_viewed_cookie'   => isset( $metaValue['wpl_viewed_cookie'] ) ? $metaValue['wpl_viewed_cookie'] : null, // Check if 'wpl_viewed_cookie' key exists.
			);
		}

		$decline       = 0;
		$approved      = 0;
		$partially_acc = 0;
		$bypass        = 0;

		foreach ( $processedData as $item ) {
			if ( $item['wpl_viewed_cookie'] == 'no' ) {
				++$decline;
			}
			if ( $item['wpl_viewed_cookie'] == 'unset' ) {
				++$bypass;
			}
			if ( $item['wpl_viewed_cookie'] == 'yes' ) {
				if ( is_array( $item['wpl_user_preference'] ) ) {
					$allYes = array_reduce(
						$item['wpl_user_preference'],
						function ( $carry, $value ) {
							return $carry && $value === 'yes';
						},
						true
					);
				} else {
					// Treat as though all values are 'no' if 'wpl_user_preference' is not an array.
					$allYes = false;
				}

				if ( $allYes ) {
					++$approved;
				} else {
					++$partially_acc;
				}
			}
		}

		// Define the option name.
		$wpl_cl_decline_option_name          = 'wpl_cl_decline';
		$wpl_cl_accept_option_name           = 'wpl_cl_accept';
		$wpl_cl_partially_accept_option_name = 'wpl_cl_partially_accept';
		$wpl_cl_bypass_option_name           = 'wpl_cl_bypass';

		// Check if the option already exists.
		$current_value_decline          = get_option( $wpl_cl_decline_option_name );
		$current_value_accept           = get_option( $wpl_cl_accept_option_name );
		$current_value_partially_accept = get_option( $wpl_cl_partially_accept_option_name );
		$current_value_bypass           = get_option( $wpl_cl_bypass_option_name );
		if ( $current_value_decline !== false && $current_value_accept !== false && $current_value_partially_accept !== false ) {
			update_option( $wpl_cl_decline_option_name, $decline );
			update_option( $wpl_cl_accept_option_name, $approved );
			update_option( $wpl_cl_partially_accept_option_name, $partially_acc );
			update_option( $wpl_cl_bypass_option_name, $bypass );
		} else {
			add_option( $wpl_cl_decline_option_name, $decline );
			add_option( $wpl_cl_accept_option_name, $approved );
			add_option( $wpl_cl_partially_accept_option_name, $partially_acc );
			add_option( $wpl_cl_bypass_option_name, $bypass );
		}
	}

	/**
	 * Consent Log table for dashboard.
	 *
	 * @since 3.0.0
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function consent_log_dashboard_table( $post_id ) {

		$args                = array(
			'post_type'      => 'wplconsentlogs',
			'posts_per_page' => 10,
		);
		$loop                = new WP_Query( $args );
		$custom              = get_post_custom();
		$forwarded_site_url  = isset( $custom['_wplconsentlogs_siteurl_cf'][0] ) ? $custom['_wplconsentlogs_siteurl_cf'][0] : null;
		$is_consent_status   = isset( $custom['_wplconsentlogs_consent_forward_cf'][0] ) ? $custom['_wplconsentlogs_consent_forward_cf'][0] : null;
		$forwarded_site_url1 = isset( $custom['_wplconsentlogs_siteurl'][0] ) ? $custom['_wplconsentlogs_siteurl'][0] : null;
		$is_consent_status1  = isset( $custom['_wplconsentlogs_consent_forward'][0] ) ? $custom['_wplconsentlogs_consent_forward'][0] : null;
		echo '<table class="wp-list-table widefat fixed striped gdpr-consent-log-dashboard" style="margin-left: 6px;width: 99%;">';
		echo '<thead><tr>';
		echo '<th style=" padding-left: 20px;border-right: 1px solid rgba(228, 228, 231, 1);font-size:14px;color:rgba(63, 63, 70, 1);">' . esc_html__( 'IP Address', 'gdpr-cookie-consent' ) . '</th>';
		echo '<th style=" padding-left: 20px;border-right: 1px solid rgba(228, 228, 231, 1);font-size:14px;color:rgba(63, 63, 70, 1);">' . esc_html__( 'Country', 'gdpr-cookie-consent' ) . '</th>';
		echo '<th style=" padding-left: 20px;border-right: 1px solid rgba(228, 228, 231, 1);font-size:14px;color:rgba(63, 63, 70, 1);">' . esc_html__( 'Consent Status', 'gdpr-cookie-consent' ) . '</th>';
		echo '<th style=" padding-left: 20px;border-right: 1px solid rgba(228, 228, 231, 1);font-size:14px;color:rgba(63, 63, 70, 1);">' . esc_html__( 'Visited Date', 'gdpr-cookie-consent' ) . '</th>';
		// Add more table headers for other custom fields if needed.
		echo '</tr></thead>';
		echo '<tbody>';
		if ( ! is_multisite() ) {
			if ( $loop->have_posts() ) :
				while ( $loop->have_posts() ) :
					$loop->the_post();
					echo '<tr>';

					// Output custom fields.
					$custom = get_post_custom();

					echo '<td class="wplconsentlogsip">' . ( isset( $custom['_wplconsentlogs_ip'][0] ) ? esc_html( $custom['_wplconsentlogs_ip'][0] ) : '' ) . '</td>';
					// country.
					if ( isset( $custom['_wplconsentlogs_ip'][0] ) ) {
						$ip_address = $custom['_wplconsentlogs_ip'][0];

						// Fetch country information using ip-api.com.
						$api_url  = 'http://ip-api.com/json/' . $ip_address;
						$response = wp_safe_remote_get( $api_url );

						if ( is_wp_error( $response ) ) {
							echo '<td>' . ( esc_html( 'Unknown', 'gdpr-cookie-consent' ) ) . '</td>'; // Handle the error gracefully.
						} else {
							$body = wp_remote_retrieve_body( $response );
							$data = json_decode( $body );

							if ( isset( $data->country ) ) {
								echo '<td>' . ( isset( $data->country ) ? esc_html( $data->country, 'gdpr-cookie-consent' ) : '' ) . '</td>';
							} else {
								echo '<td>' . ( esc_html( 'Unknown', 'gdpr-cookie-consent' ) ) . '</td>';
							}
						}
					}
					// consent status.
					if ( isset( $custom['_wplconsentlogs_details'][0] ) ) {
						$cookies             = unserialize( $custom['_wplconsentlogs_details'][0] );
						$wpl_viewed_cookie   = isset( $cookies['wpl_viewed_cookie'] ) ? $cookies['wpl_viewed_cookie'] : '';
						$wpl_user_preference = isset( $cookies['wpl_user_preference'] ) ? json_decode( $cookies['wpl_user_preference'] ) : '';
						$wpl_optout_cookie   = isset( $cookies['wpl_optout_cookie'] ) ? $cookies['wpl_optout_cookie'] : '';

						// convert the std obj in a php array.
						$allYes = true; // Initialize a flag variable.

						if ( isset( $cookies['wpl_user_preference'] ) ) {
							$decodedText               = html_entity_decode( $cookies['wpl_user_preference'] );
							$wpl_user_preference_array = json_decode( $decodedText, true );

							foreach ( $wpl_user_preference_array as $value ) {
								if ( $value === 'no' ) {
									$allYes = false; // If any element is 'no', set the flag to false and break the loop.
									break;
								}
							}
						}
						$new_consent_status = $allYes ? true : false;

						if($wpl_viewed_cookie == 'unset'){
							echo '<td style="color: #B8B491;">' . esc_html('Bypassed', 'gdpr-cookie-consent') . '</td>';
						}
						else if ( $wpl_optout_cookie == 'yes' || $wpl_viewed_cookie == 'no' ) {
							echo '<td style="color: red;">' . ( esc_html( 'Rejected', 'gdpr-cookie-consent' ) ) . '</td>';
						} elseif ( $new_consent_status ) {

							echo '<td style="color: green;">' . ( esc_html( 'Approved', 'gdpr-cookie-consent' ) ) . '</td>';
						} else {
							echo '<td style="color: blue;">' . ( esc_html( 'Partially Accepted', 'gdpr-cookie-consent' ) ) . '</td>';
						}
					}
					// consent date.
					$content_post  = get_post( $post_id );
					$time_utc      = $content_post->post_date_gmt;
					$utc_timestamp = get_date_from_gmt( $time_utc, 'U' );
					$tz_string     = wp_timezone_string();
					$timezone      = new DateTimeZone( $tz_string );
					$local_time    = gmdate( 'd', $utc_timestamp ) . '/' . gmdate( 'm', $utc_timestamp ) . '/' . gmdate( 'Y', $utc_timestamp );
					if ( $content_post ) {
						echo '<td>' . ( isset( $content_post ) ? esc_html( $local_time, 'gdpr-cookie-consent' ) : '' ) . '</td>';
					}
					echo '</tr>';
				endwhile;
			else :
				// No posts found, display a message.
				echo '<tr><td colspan="4">' . esc_html__( 'No logs found', 'gdpr-cookie-consent' ) . '</td></tr>';
			endif;

			echo '</tbody>';
			echo '</table>';
			// Restore the global post data.
			wp_reset_postdata();
		} else {
			if ( class_exists( 'Gdpr_Cookie_Consent' ) ) {
				$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
			}
			$siteurl = site_url();
			$siteurl = trailingslashit( $siteurl );
			if ( $loop->have_posts() ) :
				while ( $loop->have_posts() ) :
					$loop->the_post();
					echo '<tr>';

					// Output custom fields.
					$custom              = get_post_custom();
					$forwarded_site_url  = isset( $custom['_wplconsentlogs_siteurl_cf'][0] ) ? $custom['_wplconsentlogs_siteurl_cf'][0] : '';
					$is_consent_status   = isset( $custom['_wplconsentlogs_consent_forward_cf'][0] ) ? $custom['_wplconsentlogs_consent_forward_cf'][0] : '';
					$forwarded_site_url1 = isset( $custom['_wplconsentlogs_siteurl'][0] ) ? $custom['_wplconsentlogs_siteurl'][0] : '';
					$is_consent_status1  = isset( $custom['_wplconsentlogs_consent_forward'][0] ) ? $custom['_wplconsentlogs_consent_forward'][0] : '';

					if ( $siteurl == $forwarded_site_url1 && $is_consent_status1 != true ) {
						echo '<td class="wplconsentlogsip">' . ( isset( $custom['_wplconsentlogs_ip'][0] ) ? esc_html( $custom['_wplconsentlogs_ip'][0] ) : '' ) . '</td>';
						// country.
						if ( isset( $custom['_wplconsentlogs_ip'][0] ) ) {
							$ip_address = $custom['_wplconsentlogs_ip'][0];

							// Fetch country information using ip-api.com.
							$api_url  = 'http://ip-api.com/json/' . $ip_address;
							$response = wp_safe_remote_get( $api_url );

							if ( is_wp_error( $response ) ) {
								echo '<td>' . ( esc_html( 'Unknown', 'gdpr-cookie-consent' ) ) . '</td>'; // Handle the error gracefully.
							} else {
								$body = wp_remote_retrieve_body( $response );
								$data = json_decode( $body );

								if ( isset( $data->country ) ) {
									echo '<td>' . ( isset( $data->country ) ? esc_html( $data->country, 'gdpr-cookie-consent' ) : '' ) . '</td>';
								} else {
									echo '<td>' . ( esc_html( 'Unknown', 'gdpr-cookie-consent' ) ) . '</td>';
								}
							}
						}
						// consent status.
						if ( isset( $custom['_wplconsentlogs_details'][0] ) ) {
							$cookies             = unserialize( $custom['_wplconsentlogs_details'][0] );
							$wpl_viewed_cookie   = isset( $cookies['wpl_viewed_cookie'] ) ? $cookies['wpl_viewed_cookie'] : '';
							$wpl_user_preference = isset( $cookies['wpl_user_preference'] ) ? json_decode( $cookies['wpl_user_preference'] ) : '';
							$wpl_optout_cookie   = isset( $cookies['wpl_optout_cookie'] ) ? $cookies['wpl_optout_cookie'] : '';

							// convert the std obj in a php array.
							$allYes = true; // Initialize a flag variable.

							if ( isset( $cookies['wpl_user_preference'] ) ) {
								$decodedText               = html_entity_decode( $cookies['wpl_user_preference'] );
								$wpl_user_preference_array = json_decode( $decodedText, true );

								foreach ( $wpl_user_preference_array as $value ) {
									if ( $value === 'no' ) {
										$allYes = false; // If any element is 'no', set the flag to false and break the loop.
										break;
									}
								}
							}
							$new_consent_status = $allYes ? true : false;

							if($wpl_viewed_cookie == 'unset'){
								echo '<td style="color: #B8B491;">' . esc_html('Bypassed', 'gdpr-cookie-consent') . '</td>';
							}
							else if ( $wpl_optout_cookie == 'yes' || $wpl_viewed_cookie == 'no' ) {
								echo '<td style="color: red;">' . ( esc_html( 'Rejected', 'gdpr-cookie-consent' ) ) . '</td>';
							} elseif ( $new_consent_status ) {

								echo '<td style="color: green;">' . ( esc_html( 'Approved', 'gdpr-cookie-consent' ) ) . '</td>';
							} else {
								echo '<td style="color: blue;">' . ( esc_html( 'Partially Accepted', 'gdpr-cookie-consent' ) ) . '</td>';
							}
						}
						// consent date.
						$content_post  = get_post( $post_id );
						$time_utc      = $content_post->post_date_gmt;
						$utc_timestamp = get_date_from_gmt( $time_utc, 'U' );
						$tz_string     = wp_timezone_string();
						$timezone      = new DateTimeZone( $tz_string );
						$local_time    = gmdate( 'd', $utc_timestamp ) . '/' . gmdate( 'm', $utc_timestamp ) . '/' . gmdate( 'Y', $utc_timestamp );

						if ( $content_post ) {
							echo '<td>' . ( isset( $content_post ) ? esc_html( $local_time, 'gdpr-cookie-consent' ) : '' ) . '</td>';
						}
						echo '</tr>';
					} elseif ( $siteurl !== $forwarded_site_url && $is_consent_status == true ) {
						echo '<td class="wplconsentlogsip">' . ( isset( $custom['_wplconsentlogs_ip_cf'][0] ) ? esc_html( $custom['_wplconsentlogs_ip_cf'][0] ) : '' ) . '</td>';
						// country.
						if ( isset( $custom['_wplconsentlogs_ip_cf'][0] ) ) {
							$ip_address = $custom['_wplconsentlogs_ip_cf'][0];

							// Fetch country information using ip-api.com.
							$api_url  = 'http://ip-api.com/json/' . $ip_address;
							$response = wp_safe_remote_get( $api_url );

							if ( is_wp_error( $response ) ) {
								echo '<td>' . ( esc_html( 'Unknown', 'gdpr-cookie-consent' ) ) . '</td>'; // Handle the error gracefully.
							} else {
								$body = wp_remote_retrieve_body( $response );
								$data = json_decode( $body );

								if ( isset( $data->country ) ) {
									echo '<td>' . ( isset( $data->country ) ? esc_html( $data->country, 'gdpr-cookie-consent' ) : '' ) . '</td>';
								} else {
									echo '<td>' . ( esc_html( 'Unknown', 'gdpr-cookie-consent' ) ) . '</td>';
								}
							}
						}
						// consent status.
						if ( isset( $custom['_wplconsentlogs_details_cf'][0] ) ) {
							$cookies             = unserialize( $custom['_wplconsentlogs_details_cf'][0] );
							$wpl_viewed_cookie   = isset( $cookies['wpl_viewed_cookie'] ) ? $cookies['wpl_viewed_cookie'] : '';
							$wpl_user_preference = isset( $cookies['wpl_user_preference'] ) ? json_decode( $cookies['wpl_user_preference'] ) : '';
							$wpl_optout_cookie   = isset( $cookies['wpl_optout_cookie'] ) ? $cookies['wpl_optout_cookie'] : '';

							// convert the std obj in a php array.
							$allYes = true; // Initialize a flag variable.

							if ( isset( $cookies['wpl_user_preference'] ) ) {
								$decodedText               = html_entity_decode( $cookies['wpl_user_preference'] );
								$wpl_user_preference_array = json_decode( $decodedText, true );

								foreach ( $wpl_user_preference_array as $value ) {
									if ( $value === 'no' ) {
										$allYes = false; // If any element is 'no', set the flag to false and break the loop.
										break;
									}
								}
							}
							$new_consent_status = $allYes ? true : false;

							if($wpl_viewed_cookie == 'unset'){
								echo '<td style="color: #B8B491;">' . esc_html('Bypassed', 'gdpr-cookie-consent') . '<span style="color: orange;"> ( Forwarded )</span></td>';
							}
							else if ( $wpl_optout_cookie == 'yes' || $wpl_viewed_cookie == 'no' ) {
								echo '<td style="color: red;">' . esc_html( 'Rejected ', 'gdpr-cookie-consent' ) . '<span style="color: orange;"> ( Forwarded )</span></td>';
							} elseif ( $new_consent_status ) {

								echo '<td style="color: green;">' . ( esc_html( 'Approved ', 'gdpr-cookie-consent' ) ) . '<span style="color: orange;"> ( Forwarded )</span></td>';
							} else {
								echo '<td style="color: blue;">' . ( esc_html( 'Partially Accepted ', 'gdpr-cookie-consent' ) ) . '<span style="color: orange;">( Forwarded )</span></td>';
							}
						}
						// consent date.
						$content_post  = get_post( $post_id );
						$time_utc      = $content_post->post_date_gmt;
						$utc_timestamp = get_date_from_gmt( $time_utc, 'U' );
						$tz_string     = wp_timezone_string();
						$timezone      = new DateTimeZone( $tz_string );
						$local_time    = gmdate( 'd', $utc_timestamp ) . '/' . gmdate( 'm', $utc_timestamp ) . '/' . gmdate( 'Y', $utc_timestamp );

						if ( $content_post ) {
							echo '<td>' . ( isset( $content_post ) ? esc_html( $local_time, 'gdpr-cookie-consent' ) : '' ) . '</td>';
						}
						echo '</tr>';
					} elseif ( $siteurl == $forwarded_site_url && $is_consent_status == true ) {
						echo '<td class="wplconsentlogsip">' . ( isset( $custom['_wplconsentlogs_ip_cf'][0] ) ? esc_html( $custom['_wplconsentlogs_ip_cf'][0] ) : '' ) . '</td>';
						// country.
						if ( isset( $custom['_wplconsentlogs_ip_cf'][0] ) ) {
							$ip_address = $custom['_wplconsentlogs_ip_cf'][0];

							// Fetch country information using ip-api.com.
							$api_url  = 'http://ip-api.com/json/' . $ip_address;
							$response = wp_safe_remote_get( $api_url );

							if ( is_wp_error( $response ) ) {
								echo '<td>' . ( esc_html( 'Unknown', 'gdpr-cookie-consent' ) ) . '</td>'; // Handle the error gracefully.
							} else {
								$body = wp_remote_retrieve_body( $response );
								$data = json_decode( $body );

								if ( isset( $data->country ) ) {
									echo '<td>' . ( isset( $data->country ) ? esc_html( $data->country, 'gdpr-cookie-consent' ) : '' ) . '</td>';
								} else {
									echo '<td>' . ( esc_html( 'Unknown', 'gdpr-cookie-consent' ) ) . '</td>';
								}
							}
						}
						// consent status.
						if ( isset( $custom['_wplconsentlogs_details_cf'][0] ) ) {
							$cookies             = unserialize( $custom['_wplconsentlogs_details_cf'][0] );
							$wpl_viewed_cookie   = isset( $cookies['wpl_viewed_cookie'] ) ? $cookies['wpl_viewed_cookie'] : '';
							$wpl_user_preference = isset( $cookies['wpl_user_preference'] ) ? json_decode( $cookies['wpl_user_preference'] ) : '';
							$wpl_optout_cookie   = isset( $cookies['wpl_optout_cookie'] ) ? $cookies['wpl_optout_cookie'] : '';

							// convert the std obj in a php array.
							$allYes = true; // Initialize a flag variable.

							if ( isset( $cookies['wpl_user_preference'] ) ) {
								$decodedText               = html_entity_decode( $cookies['wpl_user_preference'] );
								$wpl_user_preference_array = json_decode( $decodedText, true );

								foreach ( $wpl_user_preference_array as $value ) {
									if ( $value === 'no' ) {
										$allYes = false; // If any element is 'no', set the flag to false and break the loop.
										break;
									}
								}
							}
							$new_consent_status = $allYes ? true : false;

							if($wpl_viewed_cookie == 'unset'){
								echo '<td style="color: #B8B491;">' . esc_html('Bypassed', 'gdpr-cookie-consent') . '</td>';
							}
							else if ( $wpl_optout_cookie == 'yes' || $wpl_viewed_cookie == 'no' ) {
								echo '<td style="color: red;">' . ( esc_html( 'Rejected', 'gdpr-cookie-consent' ) ) . '</td>';
							} elseif ( $new_consent_status ) {

								echo '<td style="color: green;">' . ( esc_html( 'Approved', 'gdpr-cookie-consent' ) ) . '</td>';
							} else {
								echo '<td style="color: blue;">' . ( esc_html( 'Partially Accepted', 'gdpr-cookie-consent' ) ) . '</td>';
							}
						}
						// consent date.
						$content_post  = get_post( $post_id );
						$time_utc      = $content_post->post_date_gmt;
						$utc_timestamp = get_date_from_gmt( $time_utc, 'U' );
						$tz_string     = wp_timezone_string();
						$timezone      = new DateTimeZone( $tz_string );
						$local_time    = gmdate( 'd', $utc_timestamp ) . '/' . gmdate( 'm', $utc_timestamp ) . '/' . gmdate( 'Y', $utc_timestamp );

						if ( $content_post ) {
							echo '<td>' . ( isset( $content_post ) ? esc_html( $local_time, 'gdpr-cookie-consent' ) : '' ) . '</td>';
						}
						echo '</tr>';
					} else {
						echo '<td class="wplconsentlogsip">' . ( isset( $custom['_wplconsentlogs_ip_cf'][0] ) ? esc_html( $custom['_wplconsentlogs_ip_cf'][0] ) : '' ) . '</td>';
						// country.
						if ( isset( $custom['_wplconsentlogs_ip_cf'][0] ) ) {
							$ip_address = $custom['_wplconsentlogs_ip_cf'][0];

							// Fetch country information using ip-api.com.
							$api_url  = 'http://ip-api.com/json/' . $ip_address;
							$response = wp_safe_remote_get( $api_url );

							if ( is_wp_error( $response ) ) {
								echo '<td>' . ( esc_html( 'Unknown', 'gdpr-cookie-consent' ) ) . '</td>'; // Handle the error gracefully.
							} else {
								$body = wp_remote_retrieve_body( $response );
								$data = json_decode( $body );

								if ( isset( $data->country ) ) {
									echo '<td>' . ( isset( $data->country ) ? esc_html( $data->country, 'gdpr-cookie-consent' ) : '' ) . '</td>';
								} else {
									echo '<td>' . ( esc_html( 'Unknown', 'gdpr-cookie-consent' ) ) . '</td>';
								}
							}
						}
						// consent status.
						if ( isset( $custom['_wplconsentlogs_details_cf'][0] ) ) {
							$cookies             = unserialize( $custom['_wplconsentlogs_details_cf'][0] );
							$wpl_viewed_cookie   = isset( $cookies['wpl_viewed_cookie'] ) ? $cookies['wpl_viewed_cookie'] : '';
							$wpl_user_preference = isset( $cookies['wpl_user_preference'] ) ? json_decode( $cookies['wpl_user_preference'] ) : '';
							$wpl_optout_cookie   = isset( $cookies['wpl_optout_cookie'] ) ? $cookies['wpl_optout_cookie'] : '';

							// convert the std obj in a php array.
							$allYes = true; // Initialize a flag variable.

							if ( isset( $cookies['wpl_user_preference'] ) ) {
								$decodedText               = html_entity_decode( $cookies['wpl_user_preference'] );
								$wpl_user_preference_array = json_decode( $decodedText, true );

								foreach ( $wpl_user_preference_array as $value ) {
									if ( $value === 'no' ) {
										$allYes = false; // If any element is 'no', set the flag to false and break the loop.
										break;
									}
								}
							}
							$new_consent_status = $allYes ? true : false;

							if($wpl_viewed_cookie == 'unset'){
								echo '<td style="color: #B8B491;>' . esc_html('Bypassed', 'gdpr-cookie-consent') . '</td>';
							}
							else if ( $wpl_optout_cookie == 'yes' || $wpl_viewed_cookie == 'no' ) {
								echo '<td style="color: red;">' . ( esc_html( 'Rejected', 'gdpr-cookie-consent' ) ) . '</td>';
							} elseif ( $new_consent_status ) {

								echo '<td style="color: green;">' . ( esc_html( 'Approved', 'gdpr-cookie-consent' ) ) . '</td>';
							} else {
								echo '<td style="color: blue;">' . ( esc_html( 'Partially Accepted', 'gdpr-cookie-consent' ) ) . '</td>';
							}
						}
						// consent date.
						$content_post  = get_post( $post_id );
						$time_utc      = $content_post->post_date_gmt;
						$utc_timestamp = get_date_from_gmt( $time_utc, 'U' );
						$tz_string     = wp_timezone_string();
						$timezone      = new DateTimeZone( $tz_string );
						$local_time    = gmdate( 'd', $utc_timestamp ) . '/' . gmdate( 'm', $utc_timestamp ) . '/' . gmdate( 'Y', $utc_timestamp );

						if ( $content_post ) {
							echo '<td>' . ( isset( $content_post ) ? esc_html( $local_time, 'gdpr-cookie-consent' ) : '' ) . '</td>';
						}
						echo '</tr>';
					}
				endwhile;
			else :
				// No posts found, display a message.
				echo '<tr><td colspan="4">' . esc_html__( 'No logs found', 'gdpr-cookie-consent' ) . '</td></tr>';
			endif;
			echo '</tbody>';
			echo '</table>';
			// Restore the global post data.
			wp_reset_postdata();
		}
	}

	/**
	 * Modify the column content for the custom post type.
	 *
	 * @since 3.0.0
	 * @param array $column Custom post type column.
	 * @param int   $post_id Post ID.
	 *
	 * @phpcs:disable
	 */
	public function wplcl_manage_custom_columns($column, $post_id = 0)
	{
		global $post;
		if (class_exists('Gdpr_Cookie_Consent')) {
			$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		}
		$curentid = get_current_blog_id();
		$custom = get_post_custom();
		if (!is_multisite()) {
			switch ($column) {
				case 'wplconsentlogsip':
					$custom = get_post_custom();
					if (isset($custom['_wplconsentlogs_ip'][0])) {
						echo '<div style="text-align: center;">' . esc_html($custom['_wplconsentlogs_ip'][0]) . '</div>';
					}
					break;
				case 'wplconsentlogsdates':
					$content_post = get_post($post_id);

					$time_utc = $content_post->post_date_gmt;
					$utc_timestamp = get_date_from_gmt($time_utc, 'U');
					$tz_string = wp_timezone_string();
					$timezone = new DateTimeZone($tz_string);
					$local_time = date('d', $utc_timestamp) . '/' . date('m', $utc_timestamp) . '/' . date('Y', $utc_timestamp);

					if ($content_post) {
						echo '<div style="text-align: center;">' . esc_html($local_time, 'gdpr-cookie-consent') . '</div>';
					}
					break;

					$custom = get_post_custom();
					if (isset($custom['_wplconsentlogs_userid'][0])) {
					if ('0' === $custom['_wplconsentlogs_userid'][0]) {
						echo '---';
						} else {
						echo '<a target="blank" href="' . get_edit_user_link($custom['_wplconsentlogs_userid'][0]) . '">' . get_the_author_meta('display_name', $custom['_wplconsentlogs_userid'][0]) . '</a>';
						}
					}
					break;

				case 'wplconsentlogscountry':
					$custom = get_post_custom();
					if (isset($custom['_wplconsentlogs_ip'][0])) {
						$ip_address = $custom['_wplconsentlogs_ip'][0];

						// Fetch country information using ip-api.com.
						$api_url = 'http://ip-api.com/json/' . $ip_address;
						$response = wp_safe_remote_get($api_url);

						if (is_wp_error($response)) {
							echo esc_attr_e('Unknown', 'gdpr-cookie-consent'); // Handle the error gracefully.
						} else {
							$body = wp_remote_retrieve_body($response);
							$data = json_decode($body);

							if (isset($data->country)) {
								echo '<div style="text-align: center;">' . esc_html($data->country) . '</div>';
							} else {
								echo '<div style="text-align: center;">' . esc_html('Unknown') . '</div>';
							}
						}
					}
					break;
				case 'wplconsentlogstatus':
					$custom = get_post_custom();
					if (isset($custom['_wplconsentlogs_details'][0])) {
						$cookies             = unserialize($custom['_wplconsentlogs_details'][0]);
						$wpl_viewed_cookie   = isset($cookies['wpl_viewed_cookie']) ? $cookies['wpl_viewed_cookie'] : '';
						$wpl_user_preference = isset($cookies['wpl_user_preference']) ? json_decode($cookies['wpl_user_preference']) : '';
						$wpl_optout_cookie   = isset($cookies['wpl_optout_cookie']) ? $cookies['wpl_optout_cookie'] : '';

						//convert the std obj in a php array.
						if (isset($cookies['wpl_user_preference'])) {
							$decodedText = html_entity_decode($cookies['wpl_user_preference']);
							$wpl_user_preference_array = json_decode($decodedText, true);


							$allYes = true; // Initialize a flag variable.

							if (!is_null($wpl_user_preference_array) && is_array($wpl_user_preference_array) && count($wpl_user_preference_array) > 0) {

								foreach ($wpl_user_preference_array as $value) {
									if ($value === 'no') {
										$allYes = false; // If any element is 'no', set the flag to false.
										break;
									}
								}
							}
							$new_consent_status = $allYes ? true : false;
						}
						if($wpl_viewed_cookie == 'unset'){
							echo '<div style="color: #B8B491;text-align:center">' . esc_html('Bypassed', 'gdpr-cookie-consent') . '</div>';
						}
						else if ($wpl_optout_cookie == 'yes' || $wpl_viewed_cookie == 'no') {
							echo '<div style="color: red;text-align:center">' . esc_html('Rejected', 'gdpr-cookie-consent') . '</div>';
						} else {

							if ($new_consent_status) {
								echo '<div style="color: green;text-align:center">' . esc_html('Approved', 'gdpr-cookie-consent') . '</div>';
							} else {
								echo '<div style="color: blue;text-align:center">' . esc_html('Partially Accepted', 'gdpr-cookie-consent') . '</div>';
							}
						}
					}
					break;
				case 'wplconsentlogspdf':
					$custom = get_post_custom($post_id);
					$content_post = get_post($post_id);
					if ($content_post) {
						$time_utc = $content_post->post_date_gmt;
						$utc_timestamp = get_date_from_gmt($time_utc, 'U');
						$tz_string = wp_timezone_string();
						$timezone = new DateTimeZone($tz_string);
						$local_time = date('d', $utc_timestamp) . '/' . date('m', $utc_timestamp) . '/' . date('Y', $utc_timestamp);
						$data = $this->fetch_cookie_scan_data();
					}
					if (isset($custom['_wplconsentlogs_ip'][0])) {
						$cookies             = unserialize($custom['_wplconsentlogs_details'][0]);
						$ip_address = $custom['_wplconsentlogs_ip'][0];
						$viewed_cookie   = isset($cookies['wpl_viewed_cookie']) ? $cookies['wpl_viewed_cookie'] : '';
						$wpl_user_preference = isset($cookies['wpl_user_preference']) ? json_decode($cookies['wpl_user_preference']) : '';
						$optout_cookie   = isset($cookies['wpl_optout_cookie']) ? $cookies['wpl_optout_cookie'] : '';
						$consent_status = 'Unknown';
						$preferencesDecoded = ''; // Initialize with an empty string or an appropriate default value.
						$wpl_user_preference_array = [];
						if (isset($wpl_user_preference) && isset($cookies['wpl_user_preference'])) {
							$preferencesDecoded = json_encode($wpl_user_preference);
							// convert the std obj to a PHP array.
							$decodedText = html_entity_decode($cookies['wpl_user_preference']);
							$wpl_user_preference_array = json_decode($decodedText, true);
						}


						$allYes = true; // Initialize a flag variable.

						if (!is_null($wpl_user_preference_array) && is_array($wpl_user_preference_array) && count($wpl_user_preference_array) > 0) {

							foreach ($wpl_user_preference_array as $value) {
								if ($value === 'no') {
									$allYes = false; // If any element is 'no', set the flag to false.
									break;
								}
							}
						}
						$new_consent_status = $allYes ? true : false;

						if($viewed_cookie == 'unset'){
							$consent_status = 'Bypassed';
						}
						else if ($optout_cookie == 'yes' || $viewed_cookie == 'no') {
							$consent_status = 'Rejected';
						} else {
							$consent_status = $allYes ? 'Approved' : 'Partially Accepted';
						}
						// Fetch country information using ip-api.com.
						$api_url = 'http://ip-api.com/json/' . $ip_address;
						$response = wp_safe_remote_get($api_url);

						if (is_wp_error($response)) {
							echo esc_attr_e('Unknown', 'gdpr-cookie-consent'); // Handle the error gracefully.
						} else {
							$body = wp_remote_retrieve_body($response);
							$data = json_decode($body);
						}
					}
		?>
					<div style="text-align: center;">
						<a href="#" class="download-pdf-button" onclick="generatePDF(
						'<?php echo esc_js(addslashes($local_time)) ?>',
						'<?php echo esc_js(isset($custom['_wplconsentlogs_ip'][0]) ? esc_attr($custom['_wplconsentlogs_ip'][0]) : 'Unknown'); ?>',
						'<?php echo esc_js(isset($data->country) ? esc_attr($data->country) : 'Unknown'); ?>',
						'<?php echo esc_attr($consent_status); ?>',
						'<?php echo esc_attr( $tcString ); ?>',
					<?php echo htmlspecialchars($preferencesDecoded, ENT_QUOTES, 'UTF-8'); ?>,
						)"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g clip-path="url(#clip0_103_5501)">
									<path d="M14.9997 7H11.9997V1H7.99974V7H4.99974L9.99974 12L14.9997 7ZM19.3377 13.532C19.1277 13.308 17.7267 11.809 17.3267 11.418C17.0464 11.1493 16.673 10.9995 16.2847 11H14.5277L17.5917 13.994H14.0477C13.9996 13.9931 13.952 14.0049 13.9099 14.0283C13.8678 14.0516 13.8325 14.0857 13.8077 14.127L12.9917 16H7.00774L6.19174 14.127C6.1668 14.0858 6.13154 14.0519 6.08944 14.0286C6.04734 14.0052 5.99987 13.9933 5.95174 13.994H2.40774L5.47074 11H3.71474C3.31774 11 2.93874 11.159 2.67274 11.418C2.27274 11.81 0.871737 13.309 0.661737 13.532C0.172737 14.053 -0.0962632 14.468 0.0317368 14.981L0.592737 18.055C0.720737 18.569 1.28374 18.991 1.84474 18.991H18.1567C18.7177 18.991 19.2807 18.569 19.4087 18.055L19.9697 14.981C20.0957 14.468 19.8277 14.053 19.3377 13.532Z" fill="#3399FF" />
								</g>
								<defs>
									<clipPath id="clip0_103_5501">
										<rect width="20" height="20" fill="white" />
									</clipPath>
								</defs>
							</svg></a>
					</div>
					<?php
			}
		} else {
			$siteurl = site_url();
			$siteurl = trailingslashit($siteurl);
			$forwarded_site_url  = isset($custom['_wplconsentlogs_siteurl_cf'][0]) ? $custom['_wplconsentlogs_siteurl_cf'][0] : null;
			$is_consent_status   = isset($custom['_wplconsentlogs_consent_forward_cf'][0]) ? $custom['_wplconsentlogs_consent_forward_cf'][0] : null;
			$forwarded_site_url1 = isset($custom['_wplconsentlogs_siteurl'][0]) ? $custom['_wplconsentlogs_siteurl'][0] : null;
			$is_consent_status1  = isset($custom['_wplconsentlogs_consent_forward'][0]) ? $custom['_wplconsentlogs_consent_forward'][0] : null;
			if ($siteurl == $forwarded_site_url1 &&  $is_consent_status1 != true) {
				switch ($column) {
					case 'wplconsentlogsip':
						$custom = get_post_custom();
						if (isset($custom['_wplconsentlogs_ip'][0])) {
							echo '<div style="text-align: center;">' . esc_html($custom['_wplconsentlogs_ip'][0]) . '</div>';
						}
						break;
					case 'wplconsentlogsdates':
						$content_post = get_post($post_id);

						$time_utc = $content_post->post_date_gmt;
						$utc_timestamp = get_date_from_gmt($time_utc, 'U');
						$tz_string = wp_timezone_string();
						$timezone = new DateTimeZone($tz_string);
						$local_time = date('d', $utc_timestamp) . '/' . date('m', $utc_timestamp) . '/' . date('Y', $utc_timestamp);

						if ($content_post) {
							echo '<div style="text-align: center;">' . esc_html($local_time, 'gdpr-cookie-consent') . '</div>';
						}
						break;

						$custom = get_post_custom();
						if (isset($custom['_wplconsentlogs_userid'][0])) {
						if ('0' === $custom['_wplconsentlogs_userid'][0]) {
							echo '---';
							} else {
							echo '<a target="blank" href="' . get_edit_user_link($custom['_wplconsentlogs_userid'][0]) . '">' . get_the_author_meta('display_name', $custom['_wplconsentlogs_userid'][0]) . '</a>';
							}
						}
						break;

					case 'wplconsentlogscountry':
						$custom = get_post_custom();
						if (isset($custom['_wplconsentlogs_ip'][0])) {
							$ip_address = $custom['_wplconsentlogs_ip'][0];

							// Fetch country information using ip-api.com.
							$api_url = 'http://ip-api.com/json/' . $ip_address;
							$response = wp_safe_remote_get($api_url);

							if (is_wp_error($response)) {
								echo esc_attr_e('Unknown', 'gdpr-cookie-consent'); // Handle the error gracefully.
							} else {
								$body = wp_remote_retrieve_body($response);
								$data = json_decode($body);

								if (isset($data->country)) {
									echo '<div style="text-align: center;">' . esc_html($data->country) . '</div>';
								} else {
									echo '<div style="text-align: center;">' . esc_html('Unknown') . '</div>';
								}
							}
						}
						break;
					case 'wplconsentlogstatus':
						$custom = get_post_custom();
						if (isset($custom['_wplconsentlogs_details'][0])) {
							$cookies             = unserialize($custom['_wplconsentlogs_details'][0]);
							$wpl_viewed_cookie   = isset($cookies['wpl_viewed_cookie']) ? $cookies['wpl_viewed_cookie'] : '';
							$wpl_user_preference = isset($cookies['wpl_user_preference']) ? json_decode($cookies['wpl_user_preference']) : '';
							$wpl_optout_cookie   = isset($cookies['wpl_optout_cookie']) ? $cookies['wpl_optout_cookie'] : '';

							//convert the std obj in a php array.
							if (isset($cookies['wpl_user_preference'])) {
								$decodedText = html_entity_decode($cookies['wpl_user_preference']);
								$wpl_user_preference_array = json_decode($decodedText, true);


								$allYes = true; // Initialize a flag variable.

								if (!is_null($wpl_user_preference_array) && is_array($wpl_user_preference_array) && count($wpl_user_preference_array) > 0) {

									foreach ($wpl_user_preference_array as $value) {
										if ($value === 'no') {
											$allYes = false; // If any element is 'no', set the flag to false.
											break;
										}
									}
								}
								$new_consent_status = $allYes ? true : false;
							}

							if($wpl_viewed_cookie == 'unset'){
								echo '<div style="color: #B8B491;text-align:center">' . esc_html('Bypassed', 'gdpr-cookie-consent') . '</div>';
							}
							else if ($wpl_optout_cookie == 'yes' || $wpl_viewed_cookie == 'no') {
								echo '<div style="color: red;text-align:center">' . esc_html('Rejected', 'gdpr-cookie-consent') . '</div>';
							} else {

								if ($new_consent_status) {
									echo '<div style="color: green;text-align:center">' . esc_html('Approved', 'gdpr-cookie-consent') . '</div>';
								} else {
									echo '<div style="color: blue;text-align:center">' . esc_html('Partially Accepted', 'gdpr-cookie-consent') . '</div>';
								}
							}
						}
						break;
					case 'wplconsentlogsforwarded':
						$custom = get_post_custom();
						if ($siteurl == $forwarded_site_url1) {
							echo '<div style="text-align:center"> Self-Consent ' . '</div>';
						} else {
							echo '<div style="color:blue;text-align:center">' . $forwarded_site_url1 . '</div>';
						}
						break;
					case 'wplconsentlogspdf':
						$custom = get_post_custom($post_id);
						$content_post = get_post($post_id);
						if ($content_post) {
							$time_utc = $content_post->post_date_gmt;
							$utc_timestamp = get_date_from_gmt($time_utc, 'U');
							$tz_string = wp_timezone_string();
							$timezone = new DateTimeZone($tz_string);
							$local_time = date('d', $utc_timestamp) . '/' . date('m', $utc_timestamp) . '/' . date('Y', $utc_timestamp);
							$data = $this->fetch_cookie_scan_data();
						}
						if (isset($custom['_wplconsentlogs_ip'][0])) {
							$cookies             = unserialize($custom['_wplconsentlogs_details'][0]);
							$ip_address = $custom['_wplconsentlogs_ip'][0];
							$viewed_cookie   = isset($cookies['wpl_viewed_cookie']) ? $cookies['wpl_viewed_cookie'] : '';
							$wpl_user_preference = isset($cookies['wpl_user_preference']) ? json_decode($cookies['wpl_user_preference']) : '';
							$optout_cookie   = isset($cookies['wpl_optout_cookie']) ? $cookies['wpl_optout_cookie'] : '';
							$consent_status = 'Unknown';
							$preferencesDecoded = ''; // Initialize with an empty string or an appropriate default value.
							$wpl_user_preference_array = [];
							if (isset($wpl_user_preference) && isset($cookies['wpl_user_preference'])) {
								$preferencesDecoded = json_encode($wpl_user_preference);
								// convert the std obj to a PHP array.
								$decodedText = html_entity_decode($cookies['wpl_user_preference']);
								$wpl_user_preference_array = json_decode($decodedText, true);
							}


							$allYes = true; // Initialize a flag variable.

							if (!is_null($wpl_user_preference_array) && is_array($wpl_user_preference_array) && count($wpl_user_preference_array) > 0) {

								foreach ($wpl_user_preference_array as $value) {
									if ($value === 'no') {
										$allYes = false; // If any element is 'no', set the flag to false.
										break;
									}
								}
							}
							$new_consent_status = $allYes ? true : false;

							if($viewed_cookie == 'unset'){
								$consent_status = 'Bypassed';
							}
							else if ($optout_cookie == 'yes' || $viewed_cookie == 'no') {
								$consent_status = 'Rejected';
							} else {
								$consent_status = $allYes ? 'Approved' : 'Partially Accepted';
							}
							// Fetch country information using ip-api.com.
							$api_url = 'http://ip-api.com/json/' . $ip_address;
							$response = wp_safe_remote_get($api_url);

							if (is_wp_error($response)) {
								echo esc_attr_e('Unknown', 'gdpr-cookie-consent'); // Handle the error gracefully.
							} else {
								$body = wp_remote_retrieve_body($response);
								$data = json_decode($body);
							}
							if ($siteurl !== $forwarded_site_url1) {
								$siteaddress = $forwarded_site_url1;
							} else {
								$siteaddress = "Self-Consent";
							}
						}
					?>
						<div style="text-align: center;">
							<a href="#" class="download-pdf-button" onclick="generatePDF(
								'<?php echo esc_js(addslashes($local_time)) ?>',
								'<?php echo esc_js(isset($custom['_wplconsentlogs_ip'][0]) ? esc_attr($custom['_wplconsentlogs_ip'][0]) : 'Unknown'); ?>',
								'<?php echo esc_js(isset($data->country) ? esc_attr($data->country) : 'Unknown'); ?>',
								'<?php echo esc_attr($consent_status); ?>',
								'<?php echo esc_attr( $tcString ); ?>',
								'<?php echo esc_attr($siteaddress); ?>',
						'<?php echo htmlspecialchars($preferencesDecoded, ENT_QUOTES, 'UTF-8'); ?>',
								)"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_103_5501)">
										<path d="M14.9997 7H11.9997V1H7.99974V7H4.99974L9.99974 12L14.9997 7ZM19.3377 13.532C19.1277 13.308 17.7267 11.809 17.3267 11.418C17.0464 11.1493 16.673 10.9995 16.2847 11H14.5277L17.5917 13.994H14.0477C13.9996 13.9931 13.952 14.0049 13.9099 14.0283C13.8678 14.0516 13.8325 14.0857 13.8077 14.127L12.9917 16H7.00774L6.19174 14.127C6.1668 14.0858 6.13154 14.0519 6.08944 14.0286C6.04734 14.0052 5.99987 13.9933 5.95174 13.994H2.40774L5.47074 11H3.71474C3.31774 11 2.93874 11.159 2.67274 11.418C2.27274 11.81 0.871737 13.309 0.661737 13.532C0.172737 14.053 -0.0962632 14.468 0.0317368 14.981L0.592737 18.055C0.720737 18.569 1.28374 18.991 1.84474 18.991H18.1567C18.7177 18.991 19.2807 18.569 19.4087 18.055L19.9697 14.981C20.0957 14.468 19.8277 14.053 19.3377 13.532Z" fill="#3399FF" />
									</g>
									<defs>
										<clipPath id="clip0_103_5501">
											<rect width="20" height="20" fill="white" />
										</clipPath>
									</defs>
								</svg></a>
						</div>
					<?php
				}
			} elseif ($siteurl !== $forwarded_site_url &&  $is_consent_status == true) {
				switch ($column) {
					case 'wplconsentlogsip':
						$custom = get_post_custom();
						if (isset($custom['_wplconsentlogs_ip_cf'][0])) {
							echo '<div style="text-align: center;">' . esc_html($custom['_wplconsentlogs_ip_cf'][0]) . '</div>';
						}
						break;
					case 'wplconsentlogsdates':
						$content_post = get_post($post_id);

						$time_utc = $content_post->post_date_gmt;
						$utc_timestamp = get_date_from_gmt($time_utc, 'U');
						$tz_string = wp_timezone_string();
						$timezone = new DateTimeZone($tz_string);
						$local_time = date('d', $utc_timestamp) . '/' . date('m', $utc_timestamp) . '/' . date('Y', $utc_timestamp);

						if ($content_post) {
							echo '<div style="text-align: center;">' . esc_html($local_time, 'gdpr-cookie-consent') . '</div>';
						}
						break;

						$custom = get_post_custom();
						if (isset($custom['_wplconsentlogs_userid_cf'][0])) {
						if ('0' === $custom['_wplconsentlogs_userid_cf'][0]) {
							echo '---';
							} else {
							echo '<a target="blank" href="' . get_edit_user_link($custom['_wplconsentlogs_userid_cf'][0]) . '">' . get_the_author_meta('display_name', $custom['_wplconsentlogs_userid_cf'][0]) . '</a>';
							}
						}
						break;

					case 'wplconsentlogscountry':
						$custom = get_post_custom();
						if (isset($custom['_wplconsentlogs_ip_cf'][0])) {
							$ip_address = $custom['_wplconsentlogs_ip_cf'][0];

							// Fetch country information using ip-api.com.
							$api_url = 'http://ip-api.com/json/' . $ip_address;
							$response = wp_safe_remote_get($api_url);

							if (is_wp_error($response)) {
								echo esc_attr_e('Unknown', 'gdpr-cookie-consent'); // Handle the error gracefully.
							} else {
								$body = wp_remote_retrieve_body($response);
								$data = json_decode($body);

								if (isset($data->country)) {
									echo '<div style="text-align: center;">' . esc_html($data->country) . '</div>';
								} else {
									echo '<div style="text-align: center;">' . esc_html('Unknown') . '</div>';
								}
							}
						}
						break;
					case 'wplconsentlogstatus':
						$custom = get_post_custom();
						if (isset($custom['_wplconsentlogs_details_cf'][0])) {
							$cookies             = unserialize($custom['_wplconsentlogs_details_cf'][0]);
							$wpl_viewed_cookie   = isset($cookies['wpl_viewed_cookie']) ? $cookies['wpl_viewed_cookie'] : '';
							$wpl_user_preference = isset($cookies['wpl_user_preference']) ? json_decode($cookies['wpl_user_preference']) : '';
							$wpl_optout_cookie   = isset($cookies['wpl_optout_cookie']) ? $cookies['wpl_optout_cookie'] : '';

							//convert the std obj in a php array.
							if (isset($cookies['wpl_user_preference'])) {
								$decodedText = html_entity_decode($cookies['wpl_user_preference']);
								$wpl_user_preference_array = json_decode($decodedText, true);


								$allYes = true; // Initialize a flag variable.

								if (!is_null($wpl_user_preference_array) && is_array($wpl_user_preference_array) && count($wpl_user_preference_array) > 0) {

									foreach ($wpl_user_preference_array as $value) {
										if ($value === 'no') {
											$allYes = false; // If any element is 'no', set the flag to false.
											break;
										}
									}
								}
								$new_consent_status = $allYes ? true : false;
							}

							if($wpl_viewed_cookie == 'unset'){
								echo '<div style="color: #B8B491;text-align:center">' . esc_html('Bypassed', 'gdpr-cookie-consent'). '<div style="color: orange;text-align:center">' . esc_html('( Forwarded )', 'gdpr-cookie-consent') . '</div>'  . '</div>';
							}
							else if ($wpl_optout_cookie == 'yes' || $wpl_viewed_cookie == 'no') {
								echo '<div style="color: red;text-align:center">' . esc_html('Rejected', 'gdpr-cookie-consent') . '<div style="color: orange;text-align:center">' . esc_html('( Forwarded )', 'gdpr-cookie-consent') . '</div>' . '</div>';
							} else {

								if ($new_consent_status) {
									echo '<div style="color: green;text-align:center">' . esc_html('Approved', 'gdpr-cookie-consent') . '<div style="color: orange;text-align:center">' . esc_html('( Forwarded )', 'gdpr-cookie-consent') . '</div>' . '</div>';
								} else {
									echo '<div style="color: blue;text-align:center">' . esc_html('Partially Accepted', 'gdpr-cookie-consent') . '<div style="color: orange;text-align:center">' . esc_html('( Forwarded )', 'gdpr-cookie-consent') . '</div>' . '</div>';
								}
							}
						}
						break;
					case 'wplconsentlogsforwarded':
						$custom = get_post_custom();
						if ($siteurl == $forwarded_site_url) {
							echo '<div style="text-align:center"> Self-Consent ' . '</div>';
						} else {
							echo '<div style="color:blue;text-align:center">' . $forwarded_site_url . '</div>';
						}

						break;
					case 'wplconsentlogspdf':
						$custom = get_post_custom($post_id);
						$content_post = get_post($post_id);
						if ($content_post) {
							$time_utc = $content_post->post_date_gmt;
							$utc_timestamp = get_date_from_gmt($time_utc, 'U');
							$tz_string = wp_timezone_string();
							$timezone = new DateTimeZone($tz_string);
							$local_time = date('d', $utc_timestamp) . '/' . date('m', $utc_timestamp) . '/' . date('Y', $utc_timestamp);
							$data = $this->fetch_cookie_scan_data();
						}
						if (isset($custom['_wplconsentlogs_ip_cf'][0])) {
							$cookies             = unserialize($custom['_wplconsentlogs_details_cf'][0]);
							$ip_address = $custom['_wplconsentlogs_ip_cf'][0];
							$viewed_cookie   = isset($cookies['wpl_viewed_cookie']) ? $cookies['wpl_viewed_cookie'] : '';
							$wpl_user_preference = isset($cookies['wpl_user_preference']) ? json_decode($cookies['wpl_user_preference']) : '';
							$optout_cookie   = isset($cookies['wpl_optout_cookie']) ? $cookies['wpl_optout_cookie'] : '';
							$consent_status = 'Unknown';
							$preferencesDecoded = ''; // Initialize with an empty string or an appropriate default value.
							$wpl_user_preference_array = [];
							if (isset($wpl_user_preference) && isset($cookies['wpl_user_preference'])) {
								$preferencesDecoded = json_encode($wpl_user_preference);
								// convert the std obj to a PHP array.
								$decodedText = html_entity_decode($cookies['wpl_user_preference']);
								$wpl_user_preference_array = json_decode($decodedText, true);
							}


							$allYes = true; // Initialize a flag variable.

							if (!is_null($wpl_user_preference_array) && is_array($wpl_user_preference_array) && count($wpl_user_preference_array) > 0) {

								foreach ($wpl_user_preference_array as $value) {
									if ($value === 'no') {
										$allYes = false; // If any element is 'no', set the flag to false.
										break;
									}
								}
							}
							$new_consent_status = $allYes ? true : false;

							if($viewed_cookie == 'unset'){
								$consent_status = 'Bypassed ( Forwarded )';
							}
							else if ($optout_cookie == 'yes' || $viewed_cookie == 'no') {
								$consent_status = 'Rejected ( Forwarded )';
							} else {
								$consent_status = $allYes ? 'Approved ( Forwarded )' : 'Partially Accepted ( Forwarded )';
							}
							// Fetch country information using ip-api.com.
							$api_url = 'http://ip-api.com/json/' . $ip_address;
							$response = wp_safe_remote_get($api_url);

							if (is_wp_error($response)) {
								echo esc_attr_e('Unknown', 'gdpr-cookie-consent'); // Handle the error gracefully.
							} else {
								$body = wp_remote_retrieve_body($response);
								$data = json_decode($body);
							}
							if ($siteurl !== $forwarded_site_url) {
								$siteaddress = $forwarded_site_url;
							} else {
								$siteaddress = "Self-Consent";
							}
						}
					?>
						<div style="text-align: center;">
							<a href="#" class="download-pdf-button" onclick="generatePDF(
									'<?php echo esc_js(addslashes($local_time)) ?>',
									'<?php echo esc_js(isset($custom['_wplconsentlogs_ip_cf'][0]) ? esc_attr($custom['_wplconsentlogs_ip_cf'][0]) : 'Unknown'); ?>',
									'<?php echo esc_js(isset($data->country) ? esc_attr($data->country) : 'Unknown'); ?>',
									'<?php echo esc_attr($consent_status); ?>',
									'<?php echo esc_attr( $tcString ); ?>',
									'<?php echo esc_attr($siteaddress); ?>',
						'<?php echo htmlspecialchars($preferencesDecoded, ENT_QUOTES, 'UTF-8'); ?>',
									)"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_103_5501)">
										<path d="M14.9997 7H11.9997V1H7.99974V7H4.99974L9.99974 12L14.9997 7ZM19.3377 13.532C19.1277 13.308 17.7267 11.809 17.3267 11.418C17.0464 11.1493 16.673 10.9995 16.2847 11H14.5277L17.5917 13.994H14.0477C13.9996 13.9931 13.952 14.0049 13.9099 14.0283C13.8678 14.0516 13.8325 14.0857 13.8077 14.127L12.9917 16H7.00774L6.19174 14.127C6.1668 14.0858 6.13154 14.0519 6.08944 14.0286C6.04734 14.0052 5.99987 13.9933 5.95174 13.994H2.40774L5.47074 11H3.71474C3.31774 11 2.93874 11.159 2.67274 11.418C2.27274 11.81 0.871737 13.309 0.661737 13.532C0.172737 14.053 -0.0962632 14.468 0.0317368 14.981L0.592737 18.055C0.720737 18.569 1.28374 18.991 1.84474 18.991H18.1567C18.7177 18.991 19.2807 18.569 19.4087 18.055L19.9697 14.981C20.0957 14.468 19.8277 14.053 19.3377 13.532Z" fill="#3399FF" />
									</g>
									<defs>
										<clipPath id="clip0_103_5501">
											<rect width="20" height="20" fill="white" />
										</clipPath>
									</defs>
								</svg></a>
						</div>
					<?php
				}
			} elseif ($siteurl == $forwarded_site_url &&  $is_consent_status == true) {
				switch ($column) {
					case 'wplconsentlogsip':
						$custom = get_post_custom();
						if (isset($custom['_wplconsentlogs_ip_cf'][0])) {
							echo '<div style="text-align: center;">' . esc_html($custom['_wplconsentlogs_ip_cf'][0]) . '</div>';
						}
						break;
					case 'wplconsentlogsdates':
						$content_post = get_post($post_id);

						$time_utc = $content_post->post_date_gmt;
						$utc_timestamp = get_date_from_gmt($time_utc, 'U');
						$tz_string = wp_timezone_string();
						$timezone = new DateTimeZone($tz_string);
						$local_time = date('d', $utc_timestamp) . '/' . date('m', $utc_timestamp) . '/' . date('Y', $utc_timestamp);

						if ($content_post) {
							echo '<div style="text-align: center;">' . esc_html($local_time, 'gdpr-cookie-consent') . '</div>';
						}
						break;

						$custom = get_post_custom();
						if (isset($custom['_wplconsentlogs_userid_cf'][0])) {
						if ('0' === $custom['_wplconsentlogs_userid_cf'][0]) {
							echo '---';
							} else {
							echo '<a target="blank" href="' . get_edit_user_link($custom['_wplconsentlogs_userid_cf'][0]) . '">' . get_the_author_meta('display_name', $custom['_wplconsentlogs_userid_cf'][0]) . '</a>';
							}
						}
						break;

					case 'wplconsentlogscountry':
						$custom = get_post_custom();
						if (isset($custom['_wplconsentlogs_ip_cf'][0])) {
							$ip_address = $custom['_wplconsentlogs_ip_cf'][0];

							// Fetch country information using ip-api.com.
							$api_url = 'http://ip-api.com/json/' . $ip_address;
							$response = wp_safe_remote_get($api_url);

							if (is_wp_error($response)) {
								echo esc_attr_e('Unknown', 'gdpr-cookie-consent'); // Handle the error gracefully.
							} else {
								$body = wp_remote_retrieve_body($response);
								$data = json_decode($body);

								if (isset($data->country)) {
									echo '<div style="text-align: center;">' . esc_html($data->country) . '</div>';
								} else {
									echo '<div style="text-align: center;">' . esc_html('Unknown') . '</div>';
								}
							}
						}
						break;
					case 'wplconsentlogstatus':
						$custom = get_post_custom();
						if (isset($custom['_wplconsentlogs_details_cf'][0])) {
							$cookies             = unserialize($custom['_wplconsentlogs_details_cf'][0]);
							$wpl_viewed_cookie   = isset($cookies['wpl_viewed_cookie']) ? $cookies['wpl_viewed_cookie'] : '';
							$wpl_user_preference = isset($cookies['wpl_user_preference']) ? json_decode($cookies['wpl_user_preference']) : '';
							$wpl_optout_cookie   = isset($cookies['wpl_optout_cookie']) ? $cookies['wpl_optout_cookie'] : '';

							//convert the std obj in a php array.
							if (isset($cookies['wpl_user_preference'])) {
								$decodedText = html_entity_decode($cookies['wpl_user_preference']);
								$wpl_user_preference_array = json_decode($decodedText, true);


								$allYes = true; // Initialize a flag variable.

								if (!is_null($wpl_user_preference_array) && is_array($wpl_user_preference_array) && count($wpl_user_preference_array) > 0) {

									foreach ($wpl_user_preference_array as $value) {
										if ($value === 'no') {
											$allYes = false; // If any element is 'no', set the flag to false.
											break;
										}
									}
								}
								$new_consent_status = $allYes ? true : false;
							}

							if($wpl_viewed_cookie == 'unset'){
								echo '<div style="color: #B8B491;text-align:center">' . esc_html('Bypassed', 'gdpr-cookie-consent') . '</div>';
							}
							else if ($wpl_optout_cookie == 'yes' || $wpl_viewed_cookie == 'no') {
								echo '<div style="color: red;text-align:center">' . esc_html('Rejected', 'gdpr-cookie-consent') . '</div>';
							} else {

								if ($new_consent_status) {
									echo '<div style="color: green;text-align:center">' . esc_html('Approved', 'gdpr-cookie-consent') . '</div>';
								} else {
									echo '<div style="color: blue;text-align:center">' . esc_html('Partially Accepted', 'gdpr-cookie-consent') . '</div>';
								}
							}
						}
						break;
					case 'wplconsentlogsforwarded':
						$custom = get_post_custom();
						if ($siteurl == $forwarded_site_url) {
							echo '<div style="text-align:center"> Self-Consent ' . '</div>';
						} else {
							echo '<div style="color:blue;text-align:center">' . $forwarded_site_url . '</div>';
						}

						break;
					case 'wplconsentlogspdf':
						$custom = get_post_custom($post_id);
						$content_post = get_post($post_id);
						if ($content_post) {
							$time_utc = $content_post->post_date_gmt;
							$utc_timestamp = get_date_from_gmt($time_utc, 'U');
							$tz_string = wp_timezone_string();
							$timezone = new DateTimeZone($tz_string);
							$local_time = date('d', $utc_timestamp) . '/' . date('m', $utc_timestamp) . '/' . date('Y', $utc_timestamp);
							$data = $this->fetch_cookie_scan_data();
						}
						if (isset($custom['_wplconsentlogs_ip_cf'][0])) {
							$cookies             = unserialize($custom['_wplconsentlogs_details_cf'][0]);
							$ip_address = $custom['_wplconsentlogs_ip_cf'][0];
							$viewed_cookie   = isset($cookies['wpl_viewed_cookie']) ? $cookies['wpl_viewed_cookie'] : '';
							$wpl_user_preference = isset($cookies['wpl_user_preference']) ? json_decode($cookies['wpl_user_preference']) : '';
							$optout_cookie   = isset($cookies['wpl_optout_cookie']) ? $cookies['wpl_optout_cookie'] : '';
							$consent_status = 'Unknown';
							$preferencesDecoded = ''; // Initialize with an empty string or an appropriate default value.
							$wpl_user_preference_array = [];
							if (isset($wpl_user_preference) && isset($cookies['wpl_user_preference'])) {
								$preferencesDecoded = json_encode($wpl_user_preference);
								// convert the std obj to a PHP array.
								$decodedText = html_entity_decode($cookies['wpl_user_preference']);
								$wpl_user_preference_array = json_decode($decodedText, true);
							}


							$allYes = true; // Initialize a flag variable.

							if (!is_null($wpl_user_preference_array) && is_array($wpl_user_preference_array) && count($wpl_user_preference_array) > 0) {

								foreach ($wpl_user_preference_array as $value) {
									if ($value === 'no') {
										$allYes = false; // If any element is 'no', set the flag to false.
										break;
									}
								}
							}
							$new_consent_status = $allYes ? true : false;

							if($viewed_cookie == 'unset'){
								$consent_status = 'Bypassed';
							}
							else if ($optout_cookie == 'yes' || $viewed_cookie == 'no') {
								$consent_status = 'Rejected';
							} else {
								$consent_status = $allYes ? 'Approved' : 'Partially Accepted';
							}
							// Fetch country information using ip-api.com.
							$api_url = 'http://ip-api.com/json/' . $ip_address;
							$response = wp_safe_remote_get($api_url);

							if (is_wp_error($response)) {
								echo esc_attr_e('Unknown', 'gdpr-cookie-consent'); // Handle the error gracefully.
							} else {
								$body = wp_remote_retrieve_body($response);
								$data = json_decode($body);
							}
							if ($siteurl !== $forwarded_site_url) {
								$siteaddress = $forwarded_site_url;
							} else {
								$siteaddress = "Self-Consent";
							}
						}
					?>
						<div style="text-align: center;">
							<a href="#" class="download-pdf-button" onclick="generatePDF(
										'<?php echo esc_js(addslashes($local_time)) ?>',
										'<?php echo esc_js(isset($custom['_wplconsentlogs_ip_cf'][0]) ? esc_attr($custom['_wplconsentlogs_ip_cf'][0]) : 'Unknown'); ?>',
										'<?php echo esc_js(isset($data->country) ? esc_attr($data->country) : 'Unknown'); ?>',
										'<?php echo esc_attr($consent_status); ?>',
										'<?php echo esc_attr( $tcString ); ?>',
										'<?php echo esc_attr($siteaddress); ?>',
						'<?php echo htmlspecialchars($preferencesDecoded, ENT_QUOTES, 'UTF-8'); ?>',
										)"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_103_5501)">
										<path d="M14.9997 7H11.9997V1H7.99974V7H4.99974L9.99974 12L14.9997 7ZM19.3377 13.532C19.1277 13.308 17.7267 11.809 17.3267 11.418C17.0464 11.1493 16.673 10.9995 16.2847 11H14.5277L17.5917 13.994H14.0477C13.9996 13.9931 13.952 14.0049 13.9099 14.0283C13.8678 14.0516 13.8325 14.0857 13.8077 14.127L12.9917 16H7.00774L6.19174 14.127C6.1668 14.0858 6.13154 14.0519 6.08944 14.0286C6.04734 14.0052 5.99987 13.9933 5.95174 13.994H2.40774L5.47074 11H3.71474C3.31774 11 2.93874 11.159 2.67274 11.418C2.27274 11.81 0.871737 13.309 0.661737 13.532C0.172737 14.053 -0.0962632 14.468 0.0317368 14.981L0.592737 18.055C0.720737 18.569 1.28374 18.991 1.84474 18.991H18.1567C18.7177 18.991 19.2807 18.569 19.4087 18.055L19.9697 14.981C20.0957 14.468 19.8277 14.053 19.3377 13.532Z" fill="#3399FF" />
									</g>
									<defs>
										<clipPath id="clip0_103_5501">
											<rect width="20" height="20" fill="white" />
										</clipPath>
									</defs>
								</svg></a>
						</div>
					<?php
				}
			} else {
				switch ($column) {
					case 'wplconsentlogsip':
						$custom = get_post_custom();
						if (isset($custom['_wplconsentlogs_ip_cf'][0])) {
							echo '<div style="text-align: center;">' . esc_html($custom['_wplconsentlogs_ip_cf'][0]) . '</div>';
						}
						break;
					case 'wplconsentlogsdates':
						$content_post = get_post($post_id);

						$time_utc = $content_post->post_date_gmt;
						$utc_timestamp = get_date_from_gmt($time_utc, 'U');
						$tz_string = wp_timezone_string();
						$timezone = new DateTimeZone($tz_string);
						$local_time = date('d', $utc_timestamp) . '/' . date('m', $utc_timestamp) . '/' . date('Y', $utc_timestamp);

						if ($content_post) {
							echo '<div style="text-align: center;">' . esc_html($local_time, 'gdpr-cookie-consent') . '</div>';
						}
						break;

						$custom = get_post_custom();
						if (isset($custom['_wplconsentlogs_userid_cf'][0])) {
						if ('0' === $custom['_wplconsentlogs_userid_cf'][0]) {
							echo '---';
							} else {
							echo '<a target="blank" href="' . get_edit_user_link($custom['_wplconsentlogs_userid_cf'][0]) . '">' . get_the_author_meta('display_name', $custom['_wplconsentlogs_userid_cf'][0]) . '</a>';
							}
						}
						break;

					case 'wplconsentlogscountry':
						$custom = get_post_custom();
						if (isset($custom['_wplconsentlogs_ip_cf'][0])) {
							$ip_address = $custom['_wplconsentlogs_ip_cf'][0];

							// Fetch country information using ip-api.com.
							$api_url = 'http://ip-api.com/json/' . $ip_address;
							$response = wp_safe_remote_get($api_url);

							if (is_wp_error($response)) {
								echo esc_attr_e('Unknown', 'gdpr-cookie-consent'); // Handle the error gracefully.
							} else {
								$body = wp_remote_retrieve_body($response);
								$data = json_decode($body);

								if (isset($data->country)) {
									echo '<div style="text-align: center;">' . esc_html($data->country) . '</div>';
								} else {
									echo '<div style="text-align: center;">' . esc_html('Unknown') . '</div>';
								}
							}
						}
						break;
					case 'wplconsentlogstatus':
						$custom = get_post_custom();

						if (isset($custom['_wplconsentlogs_details_cf'][0])) {
							$cookies             = unserialize($custom['_wplconsentlogs_details_cf'][0]);
							$wpl_viewed_cookie   = isset($cookies['wpl_viewed_cookie']) ? $cookies['wpl_viewed_cookie'] : '';
							$wpl_user_preference = isset($cookies['wpl_user_preference']) ? json_decode($cookies['wpl_user_preference']) : '';
							$wpl_optout_cookie   = isset($cookies['wpl_optout_cookie']) ? $cookies['wpl_optout_cookie'] : '';

							//convert the std obj in a php array.
							if (isset($cookies['wpl_user_preference'])) {
								$decodedText = html_entity_decode($cookies['wpl_user_preference']);
								$wpl_user_preference_array = json_decode($decodedText, true);


								$allYes = true; // Initialize a flag variable.

								if (!is_null($wpl_user_preference_array) && is_array($wpl_user_preference_array) && count($wpl_user_preference_array) > 0) {

									foreach ($wpl_user_preference_array as $value) {
										if ($value === 'no') {
											$allYes = false; // If any element is 'no', set the flag to false.
											break;
										}
									}
								}
								$new_consent_status = $allYes ? true : false;
							}

							if($wpl_viewed_cookie == 'unset'){
								echo '<div style="color: #B8B491;text-align:center">' . esc_html('Bypassed', 'gdpr-cookie-consent') . '<div style="color: orange;text-align:center">' . esc_html('( Forwarded )', 'gdpr-cookie-consent') . '</div>' . '</div>';
							}
							else if ($wpl_optout_cookie == 'yes' || $wpl_viewed_cookie == 'no') {
								echo '<div style="color: red;text-align:center">' . esc_html('Rejected', 'gdpr-cookie-consent') . esc_html('( Forwarded )', 'gdpr-cookie-consent') . '<div style="color: orange;text-align:center">' . esc_html('( Forwarded )', 'gdpr-cookie-consent') . '</div>' . '</div>';
							} else {

								if ($new_consent_status) {
									echo '<div style="color: green;text-align:center">' . esc_html('Approved', 'gdpr-cookie-consent') . esc_html('( Forwarded )', 'gdpr-cookie-consent') . '<div style="color: orange;text-align:center">' . esc_html('( Forwarded )', 'gdpr-cookie-consent') . '</div>' . '</div>';
								} else {
									echo '<div style="color: blue;text-align:center">' . esc_html('Partially Accepted', 'gdpr-cookie-consent') . esc_html('( Forwarded )', 'gdpr-cookie-consent') . '<div style="color: orange;text-align:center">' . esc_html('( Forwarded )', 'gdpr-cookie-consent') . '</div>' . '</div>';
								}
							}
						}
						break;
					case 'wplconsentlogsforwarded':
						$custom = get_post_custom();
						if ($siteurl == $forwarded_site_url) {
							echo '<div style="text-align:center"> Self-Consent ' . '</div>';
						} else {
							echo '<div style="color:blue;text-align:center">' . $forwarded_site_url . '</div>';
						}

						break;
					case 'wplconsentlogspdf':
						$custom = get_post_custom($post_id);
						$content_post = get_post($post_id);
						if ($content_post) {
							$time_utc = $content_post->post_date_gmt;
							$utc_timestamp = get_date_from_gmt($time_utc, 'U');
							$tz_string = wp_timezone_string();
							$timezone = new DateTimeZone($tz_string);
							$local_time = date('d', $utc_timestamp) . '/' . date('m', $utc_timestamp) . '/' . date('Y', $utc_timestamp);
							$data = $this->fetch_cookie_scan_data();
						}
						if (isset($custom['_wplconsentlogs_ip_cf'][0])) {
							$cookies             = unserialize($custom['_wplconsentlogs_details_cf'][0]);
							$ip_address = $custom['_wplconsentlogs_ip_cf'][0];
							$viewed_cookie   = isset($cookies['wpl_viewed_cookie']) ? $cookies['wpl_viewed_cookie'] : '';
							$wpl_user_preference = isset($cookies['wpl_user_preference']) ? json_decode($cookies['wpl_user_preference']) : '';
							$optout_cookie   = isset($cookies['wpl_optout_cookie']) ? $cookies['wpl_optout_cookie'] : '';
							$consent_status = 'Unknown';
							$preferencesDecoded = ''; // Initialize with an empty string or an appropriate default value.
							$wpl_user_preference_array = [];
							if (isset($wpl_user_preference) && isset($cookies['wpl_user_preference'])) {
								$preferencesDecoded = json_encode($wpl_user_preference);
								// convert the std obj to a PHP array.
								$decodedText = html_entity_decode($cookies['wpl_user_preference']);
								$wpl_user_preference_array = json_decode($decodedText, true);
							}


							$allYes = true; // Initialize a flag variable.

							if (!is_null($wpl_user_preference_array) && is_array($wpl_user_preference_array) && count($wpl_user_preference_array) > 0) {

								foreach ($wpl_user_preference_array as $value) {
									if ($value === 'no') {
										$allYes = false; // If any element is 'no', set the flag to false.
										break;
									}
								}
							}
							$new_consent_status = $allYes ? true : false;

							if($viewed_cookie == 'unset'){
								$consent_status = 'Bypassed ( Forwarded )';
							}
							else if ($optout_cookie == 'yes' || $viewed_cookie == 'no') {
								$consent_status = 'Rejected ( Forwarded )';
							} else {
								$consent_status = $allYes ? 'Approved ( Forwarded )' : 'Partially Accepted (
								Forwarded )';
							}
							// Fetch country information using ip-api.com.
							$api_url = 'http://ip-api.com/json/' . $ip_address;
							$response = wp_safe_remote_get($api_url);

							if (is_wp_error($response)) {
								echo esc_attr_e('Unknown', 'gdpr-cookie-consent'); // Handle the error gracefully.
							} else {
								$body = wp_remote_retrieve_body($response);
								$data = json_decode($body);
							}
							if ($siteurl !== $forwarded_site_url) {
								$siteaddress = $forwarded_site_url;
							} else {
								$siteaddress = "Self-Consent";
							}
						}
					?>
						<div style="text-align: center;">
							<a href="#" class="download-pdf-button" onclick="generatePDF(
									'<?php echo esc_js(addslashes($local_time)) ?>',
									'<?php echo esc_js(isset($custom['_wplconsentlogs_ip_cf'][0]) ? esc_attr($custom['_wplconsentlogs_ip_cf'][0]) : 'Unknown'); ?>',
									'<?php echo esc_js(isset($data->country) ? esc_attr($data->country) : 'Unknown'); ?>',
									'<?php echo esc_attr($consent_status); ?>',
									'<?php echo esc_attr( $tcString ); ?>',
									'<?php echo esc_attr($siteaddress); ?>',
					                '<?php echo htmlspecialchars($preferencesDecoded, ENT_QUOTES, 'UTF-8'); ?>',
									)"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_103_5501)">
										<path d="M14.9997 7H11.9997V1H7.99974V7H4.99974L9.99974 12L14.9997 7ZM19.3377 13.532C19.1277 13.308 17.7267 11.809 17.3267 11.418C17.0464 11.1493 16.673 10.9995 16.2847 11H14.5277L17.5917 13.994H14.0477C13.9996 13.9931 13.952 14.0049 13.9099 14.0283C13.8678 14.0516 13.8325 14.0857 13.8077 14.127L12.9917 16H7.00774L6.19174 14.127C6.1668 14.0858 6.13154 14.0519 6.08944 14.0286C6.04734 14.0052 5.99987 13.9933 5.95174 13.994H2.40774L5.47074 11H3.71474C3.31774 11 2.93874 11.159 2.67274 11.418C2.27274 11.81 0.871737 13.309 0.661737 13.532C0.172737 14.053 -0.0962632 14.468 0.0317368 14.981L0.592737 18.055C0.720737 18.569 1.28374 18.991 1.84474 18.991H18.1567C18.7177 18.991 19.2807 18.569 19.4087 18.055L19.9697 14.981C20.0957 14.468 19.8277 14.053 19.3377 13.532Z" fill="#3399FF" />
									</g>
									<defs>
										<clipPath id="clip0_103_5501">
											<rect width="20" height="20" fill="white" />
										</clipPath>
									</defs>
								</svg></a>
						</div>
<?php
				}
			}
		}
	}

	/**
	 * Unset edit bulk action for the custom post type.
	 *
	 * @since 3.0.0
	 * @param array $actions Array of bulk actions.
	 *
	 * @return mixed
	 * @phpcs:enable
	 */
	public function wplcl_remove_bulkactions( $actions ) {
		unset( $actions['edit'] );
		$actions['trash'] = 'Delete';
		return $actions;
	}

	/**
	 * Consent logs data tab overview
	 *
	 * @return void
	 */
	public function wplcl_consent_data_overview() {
		ob_start();
		$content = ob_get_clean();
		$args    = array(
			'page'    => 'consent-logs-data-tab',
			'content' => $content,
		);
		echo esc_html( $this->wpl_get_template( 'gdpr-consent-logs-tab-template.php', $args ) );
	}

	/**
	 * Get a template based on filename, overridable in the theme directory.
	 *
	 * @param string $filename The name of the template file.
	 * @param array  $args     Optional. Arguments to pass to the template file.
	 * @param string $path     Optional. The path to the template file.
	 *
	 * @return string The content of the template file.
	 */
	public function wpl_get_template( $filename, $args = array(), $path = false ) {

		$file = GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'admin/partials/gdpr-consent-logs-tab-template.php';

		if ( ! file_exists( $file ) ) {
			return false;
		}

		if ( strpos( $file, '.php' ) !== false ) {
			ob_start();
			require $file;
			$contents = ob_get_clean();
		} else {
			$contents = wp_remote_get( $file );
		}

		if ( ! empty( $args ) && is_array( $args ) ) {
			foreach ( $args as $fieldname => $value ) {
				$contents = str_replace( '{' . $fieldname . '}', $value, $contents );
			}
		}

		return $contents;
	}
}
new Gdpr_Cookie_Consent_Consent_Logs();
