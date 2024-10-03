<?php
/**
 * Consent Logs Reports Table Class
 *
 * @package Gdpr_Cookie_Consent
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load WP_List_Table if not loaded.
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * The frontend-specific functionality for consent log.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public/modules
 * @author     wpeka <https://club.wpeka.com>
 */
class WPL_Consent_Logs extends WP_List_Table {


	/**
	 * Number of items per page.
	 *
	 * @var int
	 * @since 3.0.0
	 */
	public $per_page = 10;

	/**
	 * Number of results found.
	 *
	 * @var int
	 * @since 3.0.0
	 */
	public $count = 0;

	/**
	 * options for month  filter
	 *
	 * @var int
	 * @since 2.16.0
	 */
	public $options = [];

	/**
	 * Total results.
	 *
	 * @var int
	 * @since 3.0.0
	 */
	public $total = 0;

	/**
	 * The arguments for the data set.
	 *
	 * @var array
	 * @since  3.0.0
	 */
	public $args = array();

	/**
	 * Get things started.
	 *
	 * @since 3.0.0
	 * @see   WP_List_Table::__construct()
	 */
	public function __construct() {
		global $status, $page;
		// Set parent defaults.
		parent::__construct(
			array(
				'singular' => __( 'User', 'gdpr-cookie-consent' ),
				'plural'   => __( 'Users', 'gdpr-cookie-consent' ),
				'ajax'     => false,
			)
		);
	}

	/**
	 * Show the search field.
	 *
	 * @param string $text     Label for the search box.
	 * @param string $input_id ID of the search box.
	 *
	 * @return void
	 * @since 3.0.0
	 */
	public function search_box( $text, $input_id ) {
		$input_id = $input_id . '-search-input';
	
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="'
				. esc_attr( wp_unslash( $_REQUEST['orderby'] ) ) . '" />';
		}
		if ( ! empty( $_REQUEST['order'] ) ) {
			echo '<input type="hidden" name="order" value="'
				. esc_attr( wp_unslash( $_REQUEST['order'] ) ) . '" />';
		}
		$search = $this->get_search();
		?>
	
		<div class="search-and-export-container">
			<div class="search-box">
				<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>">
					<?php echo esc_html( $text ); ?>:
				</label>
				<input placeholder="Search Consent Logs" type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php echo esc_html( $search ); ?>"/>
				<img id="search-logo-consent-log" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/vector.png'; ?>" alt="Search Logo">
				<?php
				submit_button(
					$text,
					'button',
					false,
					false,
					array( 'ID' => 'search-submit-consent-log' )
				);
				?>
			</div>
		</div>
		<script type="text/javascript">
			document.getElementById('search-logo-consent-log').addEventListener('click', function() {
				document.getElementById('search-submit-consent-log').click();
			});
		</script>
		<?php
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @return string Name of the primary column.
	 * @since  3.0.0
	 * @access protected
	 */
	protected function get_primary_column_name() {
		return 'name';
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param array  $item Contains all the data of the customers.
	 * @param string $column_name The name of the column.
	 *
	 * @return string Column Name
	 * @since 3.0.0
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'resolved':
				$value = $item['resolved'] ? __( 'Resolved', 'gdpr-cookie-consent' ) : __( 'Open', 'gdpr-cookie-consent' );
				break;
			default:
				$value = isset( $item[ $column_name ] ) ? $item[ $column_name ] : null;
				break;
		}
		return apply_filters( 'wpl_dnsmpd_column_' . $column_name, $value, $item['ID'] );
	}

	/**
	 * Column name
	 *
	 * @param array $item Contains all the data of the customers.
	 *
	 * @return string
	 */
	public function column_name( $item ) {
		$name    = '#' . $item['ID'] . ' ';
		$name   .= ! empty( $item['name'] ) ? $item['name'] : '<em>' . __( 'Unnamed user', 'gdpr-cookie-consent' ) . '</em>';
		$actions = array(
			'resolve' => '<a href="' . admin_url( 'admin.php?page=gdpr-cookie-consent&action=resolve&id=' . $item['ID'] ) . '">' . __( 'Resolve', 'gdpr-cookie-consent' ) . '</a>',
			'delete'  => '<a href="' . admin_url( 'admin.php?page=gdpr-cookie-consent&action=delete&id=' . $item['ID'] ) . '">' . __( 'Delete', 'gdpr-cookie-consent' ) . '</a>',
		);

		return $name . $this->row_actions( $actions );
	}

	/**
	 * Retrieve the table columns
	 *
	 * @return array $columns Array of all the list table columns
	 * @since 3.0.0
	 */
	public function get_columns() {
		$columns = array(
			'cb'                    => '<input class="consent-log-cb" type="checkbox"/>',
			'wplconsentlogsip'      => __( 'IP Address', 'gdpr-cookie-consent' ),
			'wplconsentlogsdates'   => __( 'Visited Date', 'gdpr-cookie-consent' ),
			'wplconsentlogscountry' => __( 'Country', 'gdpr-cookie-consent' ),
			'wplconsentlogstatus'   => __( 'Consent Status', 'gdpr-cookie-consent' ),
		);
		if ( is_multisite() ) {
			$columns['wplconsentlogsforwarded'] = __( 'Forwarded From ', 'gdpr-cookie-consent' );
		}
		$columns['wplconsentlogspdf'] = __( 'Proof Of Consent', 'gdpr-cookie-consent' );
		return apply_filters( 'wpl_report_customer_columns', $columns );
	}
	/**
	 * Column name
	 *
	 * @param array $item Contains all the data of the customers.
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s_id[]" value="%2$s" />',
			esc_attr( $this->_args['singular'] ),
			esc_attr( $item['ID'] )
		);
	}

	/**
	 * Get the sortable columns
	 *
	 * @return array Array of all the sortable columns
	 * @since 3.0.0
	 */
	public function get_sortable_columns() {
		return array(
			'request_date'            => array( 'request_date', true ),
			'wplconsentlogsip'        => array( 'wplconsentlogsip', true ),
			'region'                  => array( 'region', true ),
			'wplconsentlogsdates'     => array( 'wplconsentlogsdates', true ),
			'wplconsentlogscountry'   => array( 'wplconsentlogscountry', true ),
			'wplconsentlogstatus'     => array( 'wplconsentlogstatus', true ),
			'wplconsentlogsforwarded' => array( 'wplconsentlogsforwarded', true ),
			'wplconsentlogspdf'       => array( 'wplconsentlogspdf', true ),
		);
	}

	/**
	 * Outputs the reporting views
	 *
	 * @param string $which The context of the bulk actions. Default empty.
	 * @return void
	 * @since 3.0.0
	 */
	public function get_bulk_actions( $which = '' ) {

		$actions = array(
			'delete' => __( 'Delete', 'gdpr-cookie-consent' ),
		);
		echo wp_kses_post( $this->resolved_select() );
		return $actions;
	}

	/**
	 * Process bulk actions
	 *
	 * @access      private
	 * @since       3.0.0
	 * @return      void
	 */
	public function process_bulk_action() {

		$ids = isset( $_GET['user_id'] ) ? $_GET['user_id'] : false;

		if ( ! $ids ) {
			return;
		}

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		$current_action = $this->current_action();

		if ( empty( $current_action ) ) {
			$current_action = 'delete';
		}

		foreach ( $ids as $id ) {
			if ( 'delete' === $current_action ) {

				wp_delete_post( $id, true );

				$paged = isset( $_GET['paged'] ) ? 'paged=' . intval( $_GET['paged'] ) : '';
			}
		}
	}

	/**
	 * Retrieve the current page number
	 *
	 * @return int Current page number
	 * @since 3.0.0
	 */
	public function get_paged() {
		return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	}

	/**
	 * Retrieves the search query string
	 *
	 * @return mixed string If search is present, false otherwise
	 * @since 3.0.0
	 */
	public function get_search() {
		return ! empty( $_GET['s'] ) ? urldecode( trim( $_GET['s'] ) ) : false;
	}

	/**
	 * Generates a dropdown select for filtering by resolved dates.
	 *
	 * @return void
	 * @since 3.0.0
	 */
	public function resolved_select() {

		$options = $this->options;
		$months = [
			1 => 'January',
			2 => 'February',
			3 => 'March',
			4 => 'April',
			5 => 'May',
			6 => 'June',
			7 => 'July',
			8 => 'August',
			9 => 'September',
			10 => 'October',
			11 => 'November',
			12 => 'December'
		];
		// Loop through each post to extract creation dates and populate the options array
		foreach ( $options as $index => $option ) {
			if($option != "ALL Dates"){
				list($month, $year) = explode(' ', $option);
				$month = $months[(int)$month];
				$options[$index] = $month." ".$year;
			}
		}

		$selected = 0;
		if ( isset( $_GET['wpl_resolved_select'] ) ) {
			$selected = intval( $_GET['wpl_resolved_select'] );
		}

		?>
		<!-- submit the form on change  -->
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				var resolvedSelect = document.getElementById('wpl_resolved_select_consent_log');
				if (resolvedSelect) {
					resolvedSelect.addEventListener('change', function() {
						document.getElementById('wpl-dnsmpd-filter-consent-log').submit();
					});
				}
			});
		</script>

		<?php

		echo '<select name="wpl_resolved_select" id="wpl_resolved_select_consent_log" class="wpl_resolved_select_filter">';
		foreach ( $options as $value => $label ) {
			echo '<option value="' . esc_attr( $value ) . '" ' . ( $selected == $value ? 'selected' : '' ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select>';
	}

	/**
	 * Build all the reports data
	 *
	 * @return array $reports_data All the data for customer reports.
	 * @global object $wpdb Used to query the database using the WordPress.
	 *                      Database API
	 * @since 3.0.0
	 */
	public function reports_data() {

		$data    = array();
		$paged   = $this->get_paged();
		$offset  = $this->per_page * ( $paged - 1 );
		$search  = $this->get_search();
		$order   = isset( $_GET['order'] )
			? sanitize_text_field( $_GET['order'] ) : 'DESC';
		$orderby = isset( $_GET['orderby'] )
			? sanitize_text_field( $_GET['orderby'] ) : 'id';

		$args = array(
			'number'  => $this->per_page,
			'offset'  => $offset,
			'order'   => $order,
			'orderby' => $orderby,
			'search'  => $search,
		);

		if ( is_email( $search ) ) {
			$args['email'] = $search;
		} else {
			$args['name'] = $search;
		}

		$options = array();

		// Query to get all posts
		$all_posts = get_posts(
			array(
				'post_type'      => 'wplconsentlogs',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'orderby'        => 'ID',
				'order'          => 'DESC',
			)
		);

		// Loop through each post to extract creation dates and populate the options array
		foreach ( $all_posts as $post ) {
			$post_date  = strtotime( $post->post_date ); // Get the UNIX timestamp of the post creation date
			$month_year = gmdate( 'm Y', $post_date ); // Format the timestamp to month & year
			// Check if the month_year is not already added to the options array
			if ( ! in_array( $month_year, $options ) ) {
				// Add the month_year as an option
				$options[] = $month_year;
			}
		}

		// Add an 'ALL Dates' option at the beginning of the array
		array_unshift( $options, __( 'ALL Dates', 'gdpr-cookie-consent' ) );

		if ( isset( $_GET['wpl_resolved_select'] ) ) {
			$args['month'] = $options[$_GET['wpl_resolved_select']];
		}
		$this->options = $options;

		$this->args = $args;

		$requests = $this->get_requests( $args );
		
		if ( $requests ) {
			foreach ( $requests as $request ) {
				$data[] = array(
					'ID'                      => $request['ID'],
					'wplconsentlogsip'        => $request['wplconsentlogsip'],
					'wplconsentlogsdates'     => $request['wplconsentlogsdates'],
					'wplconsentlogscountry'   => $request['wplconsentlogscountry'],
					'wplconsentlogstatus'     => $request['wplconsentlogstatus'],
					'wplconsentlogsforwarded' => $request['wplconsentlogsforwarded'] ?? null,
					'wplconsentlogspdf'       => $request['wplconsentlogspdf'],
				);
			}
		}

		return apply_filters( 'wpl_datarequest_data', $data );
	}

	/**
	 * Prepare items for the table
	 *
	 * @return void
	 */
	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns .
		$sortable = $this->get_sortable_columns();
		$this->process_bulk_action();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $this->reports_data();
		$this->total           = $this->count_requests();
		$total_pages           = $this->per_page ? ceil( (int) $this->total / (int) $this->per_page ) : 1;
		$this->set_pagination_args(
			array(
				'total_items' => $this->total,
				'per_page'    => $this->per_page,
				'total_pages' => $total_pages,
			)
		);
	}

	/**
	 * Count the number of users.
	 *
	 * @return int
	 */
	public function count_requests() {

		global $post;
		$custom_posts = get_posts(
			array(
				'post_type'      => 'wplconsentlogs',
				'posts_per_page' => -1,
				'post_status'    => 'publish', // Retrieve all posts.
			)
		);

		$data  = array(); // Initialize the $data array.
		$count = 0;
		foreach ( $custom_posts as $post ) {
			++$count;
		}

		return $count;
	}

	/**
	 * Get users.
	 *
	 * @param array $args {
	 *     Optional. Array of parameters for retrieving requests.
	 *
	 *     @type int    $number Number of requests to retrieve. Default is 10.
	 *     @type int    $offset Offset for pagination. Default is 0.
	 *     @type string $search Search term for filtering requests. Default is an empty string.
	 *     @type int    $month  Month for filtering requests. Default is 0 (no specific month).
	 * }
	 * @return array Array of requests.
	 */
	public function get_requests( $args ) {
		global $post;
		$number = isset( $args['number'] ) ? intval( $args['number'] ) : 10;
		$offset = isset( $args['offset'] ) ? intval( $args['offset'] ) : 0;
		$search = isset( $args['search'] ) ? sanitize_text_field( $args['search'] ) : '';
		$month_year  = isset( $args['month'] ) ? $args['month'] : '0 0000';
		list($month, $year) = explode(' ', $month_year);
		$month = (int)$month;
		$year = (int)$year;
		$tcString = '';
		
		
		$post_args = array(
			'post_type'      => 'wplconsentlogs',
			'posts_per_page' => $number,
			'offset'         => $offset,
			'post_status'    => 'publish', // Retrieve all posts.
			'orderby'        => 'ID',
			'order'          => 'DESC',
			'meta_query'     => array(),
		);
		// search on the basis of IP.
		if ( ! empty( $search ) ) {
			$post_args['meta_query'][] = array(
				'key'     => '_wplconsentlogs_ip',
				'value'   => $search,
				'compare' => 'LIKE',
			);
		}

		if ( $month >= 1 && $month <= 12 ) {
			$post_args['date_query'] = array(
				array(
					'month' => $month,
					'year'  => $year
				),
			);
		}

		$custom_posts     = get_posts( $post_args );
		$all_consent_data = array(); // Initialize the $data array.

		// consent forwarding.

		if ( ! is_multisite() ) {
			foreach ( $custom_posts as $post ) {

				setup_postdata( $post ); // Setup post data for each post.

				$post_id = $post->ID;

				// Fetch specific values from post meta using keys.
				$wplconsentlogs_ip = get_post_meta( $post_id, '_wplconsentlogs_ip', true );

				$wplconsentlogs_country = get_post_meta( $post_id, '_wplconsentlogs_country', true );

				if ( empty( $wplconsentlogs_country ) ) {
					$wplconsentlogs_country = 'Unknown';
				}

				// date.
				$content_post = get_post( $post_id );

				$time_utc      = $content_post->post_date_gmt;
				$utc_timestamp = get_date_from_gmt( $time_utc, 'U' );
				$tz_string     = wp_timezone_string();
				$timezone      = new DateTimeZone( $tz_string );
				$local_time    = gmdate( 'd', $utc_timestamp ) . '/' . gmdate( 'm', $utc_timestamp ) . '/' . gmdate( 'Y', $utc_timestamp );
				$wplconsentlogstatus = '';
				$consent_status = '';
				$preferencesDecoded = '';
				if ( $content_post ) {
					$wplconsentlogs_dates = $local_time;
				}

				// consent status.

				$wplconsentlogs_details = get_post_meta( $post_id, '_wplconsentlogs_details', true );

				if ( $wplconsentlogs_details ) {
					$cookies = $wplconsentlogs_details;
					$new_consent_status =false;
					$wpl_viewed_cookie   = isset( $cookies['wpl_viewed_cookie'] ) ? $cookies['wpl_viewed_cookie'] : '';
					$wpl_user_preference = isset( $cookies['wpl_user_preference'] ) ? $cookies['wpl_user_preference'] : '';
					$wpl_optout_cookie   = isset( $cookies['wpl_optout_cookie'] ) ? $cookies['wpl_optout_cookie'] : '';

					if ( $wpl_user_preference ) {

						$decodedText               = html_entity_decode( $wpl_user_preference );
						$wpl_user_preference_array = json_decode( $decodedText, true );

						$allYes = true; // Initialize a flag variable.

						if ( ! is_null( $wpl_user_preference_array ) && is_array( $wpl_user_preference_array ) && count( $wpl_user_preference_array ) > 0 ) {

							foreach ( $wpl_user_preference_array as $value ) {
								if ( $value === 'no' ) {
									$allYes = false; // If any element is 'no', set the flag to false.
									break;
								}
							}
						}
						$new_consent_status = $allYes ? true : false;
					}

					if ( $wpl_optout_cookie == 'yes' || $wpl_viewed_cookie == 'no' ) {
						$wplconsentlogstatus = '<div style="color: #B42318;font-weight:500;">' . esc_html( 'Rejected', 'gdpr-cookie-consent' ) . '</div>';
					} elseif ( $new_consent_status ) {
						$wplconsentlogstatus = '<div style="color: #15803D;font-weight:500;">' . esc_html( 'Approved', 'gdpr-cookie-consent' ) . '</div>';
					} else {
						$wplconsentlogstatus = '<div style="color: #DB6200;font-weight:500;">' . esc_html( 'Partially Accepted', 'gdpr-cookie-consent' ) . '</div>';
					}
				}
				$siteaddress = null;
				// pdfs.

				$custom       = get_post_custom( $post_id );
				$content_post = get_post( $post_id );
				if ( $content_post ) {
					$time_utc      = $content_post->post_date_gmt;
					$utc_timestamp = get_date_from_gmt( $time_utc, 'U' );
					$tz_string     = wp_timezone_string();
					$timezone      = new DateTimeZone( $tz_string );
					$local_time    = gmdate( 'd', $utc_timestamp ) . '/' . gmdate( 'm', $utc_timestamp ) . '/' . gmdate( 'Y', $utc_timestamp );
				}

				if ( isset( $custom['_wplconsentlogs_ip'][0] ) ) {
					$cookies    = unserialize( $custom['_wplconsentlogs_details'][0] );
					$ip_address = $custom['_wplconsentlogs_ip'][0];

					$viewed_cookie = isset( $cookies['wpl_viewed_cookie'] ) ? $cookies['wpl_viewed_cookie'] : '';

					$wpl_user_preference = isset( $cookies['wpl_user_preference'] ) ? json_decode( $cookies['wpl_user_preference'] ) : '';

					$optout_cookie = isset( $cookies['wpl_optout_cookie'] ) ? $cookies['wpl_optout_cookie'] : '';
					$tcString      = isset($cookies['wpl_tc_string']) ?  $cookies['wpl_tc_string'] : '';
					$consent_status            = 'Unknown';
					$preferencesDecoded        = ''; // Initialize with an empty string or an appropriate default value.
					$wpl_user_preference_array = array();
					if ( isset( $wpl_user_preference ) && isset( $cookies['wpl_user_preference'] ) ) {
						$preferencesDecoded = wp_json_encode( $wpl_user_preference );
						// convert the std obj to a PHP array.
						$decodedText               = html_entity_decode( $cookies['wpl_user_preference'] );
						$wpl_user_preference_array = json_decode( $decodedText, true );
					}
					

					$allYes = true; // Initialize a flag variable.

					if ( ! is_null( $wpl_user_preference_array ) && is_array( $wpl_user_preference_array ) && count( $wpl_user_preference_array ) > 0 ) {

						foreach ( $wpl_user_preference_array as $value ) {
							if ( $value === 'no' ) {
								$allYes = false; // If any element is 'no', set the flag to false.
								break;
							}
						}
					}
					$new_consent_status = $allYes ? true : false;

					if ( $optout_cookie == 'yes' || $viewed_cookie == 'no' ) {
						$consent_status = 'Rejected';
					} else {
						$consent_status = $allYes ? 'Approved' : 'Partially Accepted';
					}
				}

				ob_start();
				?>
				<div class="download-pdf-button">
					<a href="#consent_logs" onclick="generatePDF(
					'<?php echo esc_js( addslashes( $local_time ) ); ?>',
					'<?php echo esc_js( isset( $custom['_wplconsentlogs_ip'][0] ) ? esc_attr( $custom['_wplconsentlogs_ip'][0] ) : 'Unknown' ); ?>',
					'<?php echo esc_js( isset( $wplconsentlogs_country ) ? esc_attr( $wplconsentlogs_country ) : 'Unknown' ); ?>',
					'<?php echo esc_attr( $consent_status ); ?>',
					'<?php echo esc_attr( $tcString ); ?>',
					'<?php echo esc_attr( $siteaddress ); ?>',
					<?php echo esc_html( $preferencesDecoded, ENT_QUOTES, 'UTF-8' ); ?>,
					)">Download</a>
				</div>
				<?php

				$wplconsentlogspdf = ob_get_clean();

				// all data for table.
				$all_consent_data[] = array(
					'ID'                    => $post_id,
					'wplconsentlogsip'      => $wplconsentlogs_ip,
					'wplconsentlogsdates'   => $wplconsentlogs_dates,
					'wplconsentlogscountry' => $wplconsentlogs_country,
					'wplconsentlogstatus'   => $wplconsentlogstatus,
					'wplconsentlogspdf'     => $wplconsentlogspdf,
				);
			}
		} else {
			foreach ( $custom_posts as $post ) {
				$the_options         = Gdpr_Cookie_Consent::gdpr_get_settings();
				$curentid            = get_current_blog_id();
				$custom              = get_post_custom();
				$siteurl             = site_url();
				$siteurl             = trailingslashit( $siteurl );
				$forwarded_site_url  = isset( $custom['_wplconsentlogs_siteurl_cf'][0] ) ? $custom['_wplconsentlogs_siteurl_cf'][0] : '';
				$is_consent_status   = isset( $custom['_wplconsentlogs_consent_forward_cf'][0] ) ? $custom['_wplconsentlogs_consent_forward_cf'][0] : '';
				$forwarded_site_url1 = isset( $custom['_wplconsentlogs_siteurl'][0] ) ? $custom['_wplconsentlogs_siteurl'][0] : '';
				$is_consent_status1  = isset( $custom['_wplconsentlogs_consent_forward'][0] ) ? $custom['_wplconsentlogs_consent_forward'][0] : '';

				setup_postdata( $post ); // Setup post data for each post.
				$post_id = $post->ID;

				if ( $siteurl == $forwarded_site_url1 && $is_consent_status1 != true ) {
					// Fetch specific values from post meta using keys.
					$wplconsentlogs_ip = get_post_meta( $post_id, '_wplconsentlogs_ip', true );

					$wplconsentlogs_country = get_post_meta( $post_id, '_wplconsentlogs_country', true );

					if ( empty( $wplconsentlogs_country ) ) {
						$wplconsentlogs_country = 'Unknown';
					}

					// date.
					$content_post = get_post( $post_id );

					$time_utc      = $content_post->post_date_gmt;
					$utc_timestamp = get_date_from_gmt( $time_utc, 'U' );
					$tz_string     = wp_timezone_string();
					$timezone      = new DateTimeZone( $tz_string );
					$local_time    = gmdate( 'd', $utc_timestamp ) . '/' . gmdate( 'm', $utc_timestamp ) . '/' . gmdate( 'Y', $utc_timestamp );

					if ( $content_post ) {
						$wplconsentlogs_dates = $local_time;
					}

					// consent status.

					$wplconsentlogs_details = get_post_meta( $post_id, '_wplconsentlogs_details', true );

					if ( $wplconsentlogs_details ) {
						$cookies = $wplconsentlogs_details;

						$wpl_viewed_cookie   = isset( $cookies['wpl_viewed_cookie'] ) ? $cookies['wpl_viewed_cookie'] : '';
						$wpl_user_preference = isset( $cookies['wpl_user_preference'] ) ? $cookies['wpl_user_preference'] : '';
						$wpl_optout_cookie   = isset( $cookies['wpl_optout_cookie'] ) ? $cookies['wpl_optout_cookie'] : '';

						if ( $wpl_user_preference ) {

							$decodedText               = html_entity_decode( $wpl_user_preference );
							$wpl_user_preference_array = json_decode( $decodedText, true );

							$allYes = true; // Initialize a flag variable.

							if ( ! is_null( $wpl_user_preference_array ) && is_array( $wpl_user_preference_array ) && count( $wpl_user_preference_array ) > 0 ) {

								foreach ( $wpl_user_preference_array as $value ) {
									if ( $value === 'no' ) {
										$allYes = false; // If any element is 'no', set the flag to false.
										break;
									}
								}
							}
							$new_consent_status = $allYes ? true : false;
						}

						if ( $wpl_optout_cookie == 'yes' || $wpl_viewed_cookie == 'no' ) {
							$wplconsentlogstatus = '<div style="color: #B42318;font-weight:500;">' . esc_html( 'Rejected', 'gdpr-cookie-consent' ) . '</div>';
						} elseif ( $new_consent_status ) {

							$wplconsentlogstatus = '<div style="color: #15803D;font-weight:500;">' . esc_html( 'Approved', 'gdpr-cookie-consent' ) . '</div>';
						} else {
							$wplconsentlogstatus = '<div style="color: #DB6200;font-weight:500;">' . esc_html( 'Partially Accepted', 'gdpr-cookie-consent' ) . '</div>';
						}
					}
					if ( $siteurl == $forwarded_site_url1 ) {
						$wplconsentlogsforwarded = '<div style="color:#0073AA;">' . $siteurl . '</div>';
					} else {
						$wplconsentlogsforwarded = '<div style="color:#0073AA;">' . $forwarded_site_url1 . '</div>';
					}
					if ( $siteurl !== $forwarded_site_url1 ) {
						$siteaddress = $forwarded_site_url1;
					} else {
						$siteaddress = $siteurl;
					}
					// pdfs.

					$custom       = get_post_custom( $post_id );
					$content_post = get_post( $post_id );
					if ( $content_post ) {
						$time_utc      = $content_post->post_date_gmt;
						$utc_timestamp = get_date_from_gmt( $time_utc, 'U' );
						$tz_string     = wp_timezone_string();
						$timezone      = new DateTimeZone( $tz_string );
						$local_time    = gmdate( 'd', $utc_timestamp ) . '/' . gmdate( 'm', $utc_timestamp ) . '/' . gmdate( 'Y', $utc_timestamp );
					}

					if ( isset( $custom['_wplconsentlogs_ip'][0] ) ) {
						$cookies    = unserialize( $custom['_wplconsentlogs_details'][0] );
						$ip_address = $custom['_wplconsentlogs_ip'][0];

						$viewed_cookie = isset( $cookies['wpl_viewed_cookie'] ) ? $cookies['wpl_viewed_cookie'] : '';

						$wpl_user_preference = isset( $cookies['wpl_user_preference'] ) ? json_decode( $cookies['wpl_user_preference'] ) : '';

						$optout_cookie = isset( $cookies['wpl_optout_cookie'] ) ? $cookies['wpl_optout_cookie'] : '';

						$consent_status            = 'Unknown';
						$preferencesDecoded        = ''; // Initialize with an empty string or an appropriate default value.
						$wpl_user_preference_array = array();
						if ( isset( $wpl_user_preference ) && isset( $cookies['wpl_user_preference'] ) ) {
							$preferencesDecoded = wp_json_encode( $wpl_user_preference );
							// convert the std obj to a PHP array.
							$decodedText               = html_entity_decode( $cookies['wpl_user_preference'] );
							$wpl_user_preference_array = json_decode( $decodedText, true );
						}

						$allYes = true; // Initialize a flag variable.

						if ( ! is_null( $wpl_user_preference_array ) && is_array( $wpl_user_preference_array ) && count( $wpl_user_preference_array ) > 0 ) {

							foreach ( $wpl_user_preference_array as $value ) {
								if ( $value === 'no' ) {
									$allYes = false; // If any element is 'no', set the flag to false.
									break;
								}
							}
						}
						$new_consent_status = $allYes ? true : false;

						if ( $optout_cookie == 'yes' || $viewed_cookie == 'no' ) {
							$consent_status = 'Rejected';
						} else {
							$consent_status = $allYes ? 'Approved' : 'Partially Accepted';
						}
					}

					ob_start();
					?>
					<div class="download-pdf-button">
						<a href="#consent_logs" onclick="generatePDF(
							'<?php echo esc_js( addslashes( $local_time ) ); ?>',
							'<?php echo esc_js( isset( $custom['_wplconsentlogs_ip'][0] ) ? esc_attr( $custom['_wplconsentlogs_ip'][0] ) : 'Unknown' ); ?>',
							'<?php echo esc_js( isset( $wplconsentlogs_country ) ? esc_attr( $wplconsentlogs_country ) : 'Unknown' ); ?>',
							'<?php echo esc_attr( $consent_status ); ?>',
							'<?php echo esc_attr( $tcString ); ?>',
							'<?php echo esc_attr( $siteaddress ); ?>',
								<?php echo esc_html( $preferencesDecoded, ENT_QUOTES, 'UTF-8' ); ?>,
							)">Download</a>
					</div>
					<?php

					$wplconsentlogspdf = ob_get_clean();

					// all data for table.
					$all_consent_data[] = array(
						'ID'                      => $post_id,
						'wplconsentlogsip'        => $wplconsentlogs_ip,
						'wplconsentlogsdates'     => $wplconsentlogs_dates,
						'wplconsentlogscountry'   => $wplconsentlogs_country,
						'wplconsentlogstatus'     => $wplconsentlogstatus,
						'wplconsentlogsforwarded' => $wplconsentlogsforwarded,
						'wplconsentlogspdf'       => $wplconsentlogspdf,
					);
				} elseif ( $siteurl !== $forwarded_site_url && $is_consent_status == true ) {
					// Fetch specific values from post meta using keys.
					$wplconsentlogs_ip = get_post_meta( $post_id, '_wplconsentlogs_ip_cf', true );

					$wplconsentlogs_country = get_post_meta( $post_id, '_wplconsentlogs_country_cf', true );

					if ( empty( $wplconsentlogs_country ) ) {
						$wplconsentlogs_country = 'Unknown';
					}

					// date.
					$content_post = get_post( $post_id );

					$time_utc      = $content_post->post_date_gmt;
					$utc_timestamp = get_date_from_gmt( $time_utc, 'U' );
					$tz_string     = wp_timezone_string();
					$timezone      = new DateTimeZone( $tz_string );
					$local_time    = gmdate( 'd', $utc_timestamp ) . '/' . gmdate( 'm', $utc_timestamp ) . '/' . gmdate( 'Y', $utc_timestamp );

					if ( $content_post ) {
						$wplconsentlogs_dates = $local_time;
					}

					// consent status.

					$wplconsentlogs_details = get_post_meta( $post_id, '_wplconsentlogs_details_cf', true );

					if ( $wplconsentlogs_details ) {
						$cookies = $wplconsentlogs_details;

						$wpl_viewed_cookie   = isset( $cookies['wpl_viewed_cookie'] ) ? $cookies['wpl_viewed_cookie'] : '';
						$wpl_user_preference = isset( $cookies['wpl_user_preference'] ) ? $cookies['wpl_user_preference'] : '';
						$wpl_optout_cookie   = isset( $cookies['wpl_optout_cookie'] ) ? $cookies['wpl_optout_cookie'] : '';

						if ( $wpl_user_preference ) {

							$decodedText               = html_entity_decode( $wpl_user_preference );
							$wpl_user_preference_array = json_decode( $decodedText, true );

							$allYes = true; // Initialize a flag variable.

							if ( ! is_null( $wpl_user_preference_array ) && is_array( $wpl_user_preference_array ) && count( $wpl_user_preference_array ) > 0 ) {

								foreach ( $wpl_user_preference_array as $value ) {
									if ( $value === 'no' ) {
										$allYes = false; // If any element is 'no', set the flag to false.
										break;
									}
								}
							}
							$new_consent_status = $allYes ? true : false;
						}

						if ( $wpl_optout_cookie == 'yes' || $wpl_viewed_cookie == 'no' ) {
							$wplconsentlogstatus = '<div style="color: #B42318;font-weight:500;">' . esc_html( 'Rejected', 'gdpr-cookie-consent' ) . '<div style="color: orange;">' . esc_html( '( Forwarded )', 'gdpr-cookie-consent' ) . '</div>' . '</div>';
						} elseif ( $new_consent_status ) {

							$wplconsentlogstatus = '<div style="color: #15803D;font-weight:500;">' . esc_html( 'Approved', 'gdpr-cookie-consent' ) . '<div style="color: orange;">' . esc_html( '( Forwarded )', 'gdpr-cookie-consent' ) . '</div>' . '</div>';
						} else {
							$wplconsentlogstatus = '<div style="color: #DB6200;font-weight:500; ">' . esc_html( 'Partially Accepted', 'gdpr-cookie-consent' ) . '<div style="color: orange;">' . esc_html( '( Forwarded )', 'gdpr-cookie-consent' ) . '</div>' . '</div>';
						}
					}
					if ( $siteurl == $forwarded_site_url ) {
						$wplconsentlogsforwarded = '<div style="style="color:#0073AA;">' . $siteurl . '</div>';
					} else {
						$wplconsentlogsforwarded = '<div style="style="color:#0073AA;">' . $forwarded_site_url . '</div>';
					}
					if ( $siteurl !== $forwarded_site_url ) {
						$siteaddress = $forwarded_site_url;
					} else {
						$siteaddress = $siteurl;
					}
					// pdfs.

					$custom       = get_post_custom( $post_id );
					$content_post = get_post( $post_id );
					if ( $content_post ) {
						$time_utc      = $content_post->post_date_gmt;
						$utc_timestamp = get_date_from_gmt( $time_utc, 'U' );
						$tz_string     = wp_timezone_string();
						$timezone      = new DateTimeZone( $tz_string );
						$local_time    = gmdate( 'd', $utc_timestamp ) . '/' . gmdate( 'm', $utc_timestamp ) . '/' . gmdate( 'Y', $utc_timestamp );
					}

					if ( isset( $custom['_wplconsentlogs_ip_cf'][0] ) ) {
						$cookies    = unserialize( $custom['_wplconsentlogs_details_cf'][0] );
						$ip_address = $custom['_wplconsentlogs_ip_cf'][0];

						$viewed_cookie = isset( $cookies['wpl_viewed_cookie'] ) ? $cookies['wpl_viewed_cookie'] : '';

						$wpl_user_preference = isset( $cookies['wpl_user_preference'] ) ? json_decode( $cookies['wpl_user_preference'] ) : '';

						$optout_cookie = isset( $cookies['wpl_optout_cookie'] ) ? $cookies['wpl_optout_cookie'] : '';

						$consent_status            = 'Unknown';
						$preferencesDecoded        = ''; // Initialize with an empty string or an appropriate default value.
						$wpl_user_preference_array = array();
						if ( isset( $wpl_user_preference ) && isset( $cookies['wpl_user_preference'] ) ) {
							$preferencesDecoded = wp_json_encode( $wpl_user_preference );
							// convert the std obj to a PHP array.
							$decodedText               = html_entity_decode( $cookies['wpl_user_preference'] );
							$wpl_user_preference_array = json_decode( $decodedText, true );
						}

						$allYes = true; // Initialize a flag variable.

						if ( ! is_null( $wpl_user_preference_array ) && is_array( $wpl_user_preference_array ) && count( $wpl_user_preference_array ) > 0 ) {

							foreach ( $wpl_user_preference_array as $value ) {
								if ( $value === 'no' ) {
									$allYes = false; // If any element is 'no', set the flag to false.
									break;
								}
							}
						}
						$new_consent_status = $allYes ? true : false;

						if ( $optout_cookie == 'yes' || $viewed_cookie == 'no' ) {
							$consent_status = 'Rejected ( Forwarded ) ';
						} else {
							$consent_status = $allYes ? 'Approved ( Forwarded ) ' : 'Partially Accepted ( Forwarded ) ';
						}
					}

					ob_start();
					?>
					<div class="download-pdf-button">
						<a href="#consent_logs" onclick="generatePDF(
						'<?php echo esc_js( addslashes( $local_time ) ); ?>',
						'<?php echo esc_js( isset( $custom['_wplconsentlogs_ip_cf'][0] ) ? esc_attr( $custom['_wplconsentlogs_ip_cf'][0] ) : 'Unknown' ); ?>',
						'<?php echo esc_js( isset( $wplconsentlogs_country ) ? esc_attr( $wplconsentlogs_country ) : 'Unknown' ); ?>',
						'<?php echo esc_attr( $consent_status ); ?>',
						'<?php echo esc_attr( $tcString ); ?>',
						'<?php echo esc_attr( $siteaddress ); ?>',
							<?php echo esc_html( $preferencesDecoded, ENT_QUOTES, 'UTF-8' ); ?>,
						)">Download</a>
					</div>
					<?php

					$wplconsentlogspdf = ob_get_clean();

					// all data for table.
					$all_consent_data[] = array(
						'ID'                      => $post_id,
						'wplconsentlogsip'        => $wplconsentlogs_ip,
						'wplconsentlogsdates'     => $wplconsentlogs_dates,
						'wplconsentlogscountry'   => $wplconsentlogs_country,
						'wplconsentlogstatus'     => $wplconsentlogstatus,
						'wplconsentlogsforwarded' => $wplconsentlogsforwarded,
						'wplconsentlogspdf'       => $wplconsentlogspdf,
					);
				} elseif ( $siteurl == $forwarded_site_url && $is_consent_status == true ) {
					// Fetch specific values from post meta using keys.
					$wplconsentlogs_ip = get_post_meta( $post_id, '_wplconsentlogs_ip_cf', true );

					$wplconsentlogs_country = get_post_meta( $post_id, '_wplconsentlogs_country_cf', true );

					if ( empty( $wplconsentlogs_country ) ) {
						$wplconsentlogs_country = 'Unknown';
					}

					// date.
					$content_post = get_post( $post_id );

					$time_utc      = $content_post->post_date_gmt;
					$utc_timestamp = get_date_from_gmt( $time_utc, 'U' );
					$tz_string     = wp_timezone_string();
					$timezone      = new DateTimeZone( $tz_string );
					$local_time    = gmdate( 'd', $utc_timestamp ) . '/' . gmdate( 'm', $utc_timestamp ) . '/' . gmdate( 'Y', $utc_timestamp );

					if ( $content_post ) {
						$wplconsentlogs_dates = $local_time;
					}

					// consent status.

					$wplconsentlogs_details = get_post_meta( $post_id, '_wplconsentlogs_details_cf', true );

					if ( $wplconsentlogs_details ) {
						$cookies = $wplconsentlogs_details;

						$wpl_viewed_cookie   = isset( $cookies['wpl_viewed_cookie'] ) ? $cookies['wpl_viewed_cookie'] : '';
						$wpl_user_preference = isset( $cookies['wpl_user_preference'] ) ? $cookies['wpl_user_preference'] : '';
						$wpl_optout_cookie   = isset( $cookies['wpl_optout_cookie'] ) ? $cookies['wpl_optout_cookie'] : '';

						if ( $wpl_user_preference ) {

							$decodedText               = html_entity_decode( $wpl_user_preference );
							$wpl_user_preference_array = json_decode( $decodedText, true );

							$allYes = true; // Initialize a flag variable.

							if ( ! is_null( $wpl_user_preference_array ) && is_array( $wpl_user_preference_array ) && count( $wpl_user_preference_array ) > 0 ) {

								foreach ( $wpl_user_preference_array as $value ) {
									if ( $value === 'no' ) {
										$allYes = false; // If any element is 'no', set the flag to false.
										break;
									}
								}
							}
							$new_consent_status = $allYes ? true : false;
						}

						if ( $wpl_optout_cookie == 'yes' || $wpl_viewed_cookie == 'no' ) {
							$wplconsentlogstatus = '<div style="color:#B42318;font-weight:500;">' . esc_html( 'Rejected', 'gdpr-cookie-consent' ) . '</div>';
						} elseif ( $new_consent_status ) {

							$wplconsentlogstatus = '<div style="color: #15803D;font-weight:500;">' . esc_html( 'Approved', 'gdpr-cookie-consent' ) . '</div>';
						} else {
							$wplconsentlogstatus = '<div style="color: #DB6200;font-weight:500;">' . esc_html( 'Partially Accepted', 'gdpr-cookie-consent' ) . '</div>';
						}
					}
					if ( $siteurl == $forwarded_site_url ) {
						$wplconsentlogsforwarded = '<div style="style="color:#0073AA;"> ' . $siteurl . '</div>';
					} else {
						$wplconsentlogsforwarded = '<div style="style="color:#0073AA;">' . $forwarded_site_url . '</div>';
					}
					if ( $siteurl !== $forwarded_site_url ) {
						$siteaddress = $forwarded_site_url;
					} else {
						$siteaddress = $siteurl;
					}

					// pdfs.
					$custom       = get_post_custom( $post_id );
					$content_post = get_post( $post_id );
					if ( $content_post ) {
						$time_utc      = $content_post->post_date_gmt;
						$utc_timestamp = get_date_from_gmt( $time_utc, 'U' );
						$tz_string     = wp_timezone_string();
						$timezone      = new DateTimeZone( $tz_string );
						$local_time    = gmdate( 'd', $utc_timestamp ) . '/' . gmdate( 'm', $utc_timestamp ) . '/' . gmdate( 'Y', $utc_timestamp );
					}

					if ( isset( $custom['_wplconsentlogs_ip_cf'][0] ) ) {
						$cookies    = unserialize( $custom['_wplconsentlogs_details_cf'][0] );
						$ip_address = $custom['_wplconsentlogs_ip_cf'][0];

						$viewed_cookie = isset( $cookies['wpl_viewed_cookie'] ) ? $cookies['wpl_viewed_cookie'] : '';

						$wpl_user_preference = isset( $cookies['wpl_user_preference'] ) ? json_decode( $cookies['wpl_user_preference'] ) : '';

						$optout_cookie = isset( $cookies['wpl_optout_cookie'] ) ? $cookies['wpl_optout_cookie'] : '';

						$consent_status            = 'Unknown';
						$preferencesDecoded        = ''; // Initialize with an empty string or an appropriate default value.
						$wpl_user_preference_array = array();
						if ( isset( $wpl_user_preference ) && isset( $cookies['wpl_user_preference'] ) ) {
							$preferencesDecoded = wp_json_encode( $wpl_user_preference );
							// convert the std obj to a PHP array.
							$decodedText               = html_entity_decode( $cookies['wpl_user_preference'] );
							$wpl_user_preference_array = json_decode( $decodedText, true );
						}

						$allYes = true; // Initialize a flag variable.

						if ( ! is_null( $wpl_user_preference_array ) && is_array( $wpl_user_preference_array ) && count( $wpl_user_preference_array ) > 0 ) {

							foreach ( $wpl_user_preference_array as $value ) {
								if ( $value === 'no' ) {
									$allYes = false; // If any element is 'no', set the flag to false.
									break;
								}
							}
						}
						$new_consent_status = $allYes ? true : false;

						if ( $optout_cookie == 'yes' || $viewed_cookie == 'no' ) {
							$consent_status = 'Rejected';
						} else {
							$consent_status = $allYes ? 'Approved' : 'Partially Accepted';
						}
					}

					ob_start();
					?>
					<div class="download-pdf-button">
						<a href="#consent_logs" onclick="generatePDF(
							'<?php echo esc_js( addslashes( $local_time ) ); ?>',
							'<?php echo esc_js( isset( $custom['_wplconsentlogs_ip_cf'][0] ) ? esc_attr( $custom['_wplconsentlogs_ip_cf'][0] ) : 'Unknown' ); ?>',
							'<?php echo esc_js( isset( $wplconsentlogs_country ) ? esc_attr( $wplconsentlogs_country ) : 'Unknown' ); ?>',
							'<?php echo esc_attr( $consent_status ); ?>',
							'<?php echo esc_attr( $tcString ); ?>',
							'<?php echo esc_attr( $siteaddress ); ?>',
							<?php echo esc_html( $preferencesDecoded, ENT_QUOTES, 'UTF-8' ); ?>,
							)">Download</a>
					</div>
					<?php

					$wplconsentlogspdf = ob_get_clean();

					// all data for table.
					$all_consent_data[] = array(
						'ID'                      => $post_id,
						'wplconsentlogsip'        => $wplconsentlogs_ip,
						'wplconsentlogsdates'     => $wplconsentlogs_dates,
						'wplconsentlogscountry'   => $wplconsentlogs_country,
						'wplconsentlogstatus'     => $wplconsentlogstatus,
						'wplconsentlogsforwarded' => $wplconsentlogsforwarded,
						'wplconsentlogspdf'       => $wplconsentlogspdf,
					);
				} else {
					// Fetch specific values from post meta using keys.
					$wplconsentlogs_ip = get_post_meta( $post_id, '_wplconsentlogs_ip_cf', true );

					$wplconsentlogs_country = get_post_meta( $post_id, '_wplconsentlogs_country_cf', true );

					if ( empty( $wplconsentlogs_country ) ) {
						$wplconsentlogs_country = 'Unknown';
					}

					// date.
					$content_post = get_post( $post_id );

					$time_utc      = $content_post->post_date_gmt;
					$utc_timestamp = get_date_from_gmt( $time_utc, 'U' );
					$tz_string     = wp_timezone_string();
					$timezone      = new DateTimeZone( $tz_string );
					$local_time    = gmdate( 'd', $utc_timestamp ) . '/' . gmdate( 'm', $utc_timestamp ) . '/' . gmdate( 'Y', $utc_timestamp );

					if ( $content_post ) {
						$wplconsentlogs_dates = $local_time;
					}

					// consent status.

					$wplconsentlogs_details = get_post_meta( $post_id, '_wplconsentlogs_details_cf', true );

					if ( $wplconsentlogs_details ) {
						$cookies = $wplconsentlogs_details;

						$wpl_viewed_cookie   = isset( $cookies['wpl_viewed_cookie'] ) ? $cookies['wpl_viewed_cookie'] : '';
						$wpl_user_preference = isset( $cookies['wpl_user_preference'] ) ? $cookies['wpl_user_preference'] : '';
						$wpl_optout_cookie   = isset( $cookies['wpl_optout_cookie'] ) ? $cookies['wpl_optout_cookie'] : '';

						if ( $wpl_user_preference ) {

							$decodedText               = html_entity_decode( $wpl_user_preference );
							$wpl_user_preference_array = json_decode( $decodedText, true );

							$allYes = true; // Initialize a flag variable.

							if ( ! is_null( $wpl_user_preference_array ) && is_array( $wpl_user_preference_array ) && count( $wpl_user_preference_array ) > 0 ) {

								foreach ( $wpl_user_preference_array as $value ) {
									if ( $value === 'no' ) {
										$allYes = false; // If any element is 'no', set the flag to false.
										break;
									}
								}
							}
							$new_consent_status = $allYes ? true : false;
						}

						if ( $wpl_optout_cookie == 'yes' || $wpl_viewed_cookie == 'no' ) {
							$wplconsentlogstatus = '<div style="color: #B42318;font-weight:500;">' . esc_html( 'Rejected', 'gdpr-cookie-consent' ) . '<div style="color: orange;">' . esc_html( '( Forwarded )', 'gdpr-cookie-consent' ) . '</div>' . '</div>';
						} elseif ( $new_consent_status ) {

							$wplconsentlogstatus = '<div style="color: #15803D;font-weight:500;">' . esc_html( 'Approved', 'gdpr-cookie-consent' ) . '<div style="color: orange;">' . esc_html( '( Forwarded )', 'gdpr-cookie-consent' ) . '</div>' . '</div>';
						} else {
							$wplconsentlogstatus = '<div style="color: #DB6200;font-weight:500;">' . esc_html( 'Partially Accepted', 'gdpr-cookie-consent' ) . '<div style="color: orange;">' . esc_html( '( Forwarded )', 'gdpr-cookie-consent' ) . '</div>' . '</div>';
						}
					}
					if ( $siteurl == $forwarded_site_url ) {
						$wplconsentlogsforwarded = '<div style="style="color:#0073AA;"> ' . $siteurl . '</div>';
					} else {
						$wplconsentlogsforwarded = '<div style="style="color:#0073AA;">' . $forwarded_site_url . '</div>';
					}
					if ( $siteurl !== $forwarded_site_url ) {
						$siteaddress = $forwarded_site_url;
					} else {
						$siteaddress = $siteurl;
					}
					// pdfs.

					$custom       = get_post_custom( $post_id );
					$content_post = get_post( $post_id );
					if ( $content_post ) {
						$time_utc      = $content_post->post_date_gmt;
						$utc_timestamp = get_date_from_gmt( $time_utc, 'U' );
						$tz_string     = wp_timezone_string();
						$timezone      = new DateTimeZone( $tz_string );
						$local_time    = gmdate( 'd', $utc_timestamp ) . '/' . gmdate( 'm', $utc_timestamp ) . '/' . gmdate( 'Y', $utc_timestamp );
					}

					if ( isset( $custom['_wplconsentlogs_ip_cf'][0] ) ) {
						$cookies    = unserialize( $custom['_wplconsentlogs_details_cf'][0] );
						$ip_address = $custom['_wplconsentlogs_ip_cf'][0];

						$viewed_cookie = isset( $cookies['wpl_viewed_cookie'] ) ? $cookies['wpl_viewed_cookie'] : '';

						$wpl_user_preference = isset( $cookies['wpl_user_preference'] ) ? json_decode( $cookies['wpl_user_preference'] ) : '';

						$optout_cookie = isset( $cookies['wpl_optout_cookie'] ) ? $cookies['wpl_optout_cookie'] : '';

						$consent_status            = 'Unknown';
						$preferencesDecoded        = ''; // Initialize with an empty string or an appropriate default value.
						$wpl_user_preference_array = array();
						if ( isset( $wpl_user_preference ) && isset( $cookies['wpl_user_preference'] ) ) {
							$preferencesDecoded = wp_json_encode( $wpl_user_preference );
							// convert the std obj to a PHP array.
							$decodedText               = html_entity_decode( $cookies['wpl_user_preference'] );
							$wpl_user_preference_array = json_decode( $decodedText, true );
						}

						$allYes = true; // Initialize a flag variable.

						if ( ! is_null( $wpl_user_preference_array ) && is_array( $wpl_user_preference_array ) && count( $wpl_user_preference_array ) > 0 ) {

							foreach ( $wpl_user_preference_array as $value ) {
								if ( $value === 'no' ) {
									$allYes = false; // If any element is 'no', set the flag to false.
									break;
								}
							}
						}
						$new_consent_status = $allYes ? true : false;

						if ( $optout_cookie == 'yes' || $viewed_cookie == 'no' ) {
							$consent_status = 'Rejected ( Forwarded ) ';
						} else {
							$consent_status = $allYes ? 'Approved ( Forwarded ) ' : 'Partially Accepted ( Forwarded ) ';
						}
					}

					ob_start();
					?>
					<div class="download-pdf-button" >
						<a href="#consent_logs" onclick="generatePDF(
						'<?php echo esc_js( addslashes( $local_time ) ); ?>',
						'<?php echo esc_js( isset( $custom['_wplconsentlogs_ip_cf'][0] ) ? esc_attr( $custom['_wplconsentlogs_ip_cf'][0] ) : 'Unknown' ); ?>',
						'<?php echo esc_js( isset( $wplconsentlogs_country ) ? esc_attr( $wplconsentlogs_country ) : 'Unknown' ); ?>',
						'<?php echo esc_attr( $consent_status ); ?>',
						'<?php echo esc_attr( $tcString ); ?>',
						'<?php echo esc_attr( $siteaddress ); ?>',
						<?php echo esc_html( $preferencesDecoded, ENT_QUOTES, 'UTF-8' ); ?>,
						)">Download</a>
					</div>
					<?php

					$wplconsentlogspdf = ob_get_clean();

					// all data for table.
					$all_consent_data[] = array(
						'ID'                      => $post_id,
						'wplconsentlogsip'        => $wplconsentlogs_ip,
						'wplconsentlogsdates'     => $wplconsentlogs_dates,
						'wplconsentlogscountry'   => $wplconsentlogs_country,
						'wplconsentlogstatus'     => $wplconsentlogstatus,
						'wplconsentlogsforwarded' => $wplconsentlogsforwarded,
						'wplconsentlogspdf'       => $wplconsentlogspdf,
					);
				}
			}
		}

		return $all_consent_data;
	}
	/**
	 * Custom sprintf function with additional checks for translation errors.
	 *
	 * @return string Formatted string or a string with a translation error message.
	 */
	public function wpl_sprintf() {

		$args       = func_get_args();
		$count      = substr_count( $args[0], '%s' );
		$args_count = count( $args ) - 1;
		if ( $args_count === $count ) {
			return call_user_func_array( 'sprintf', $args );
		} else {
			$output = $args[0];
			if ( is_admin() ) {
				$output .= 'Translation error';
			}
			return $output;
		}
	}
}