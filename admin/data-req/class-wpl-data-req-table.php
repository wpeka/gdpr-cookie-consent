<?php
/**
 * Data Request Reports Table Class
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class WPL_Data_Req_Table extends WP_List_Table {

	/**
	 * Number of items per page
	 *
	 * @var int
	 * @since  3.0.0
	 */
	public $per_page = 10;

	/**
	 * Number of results found
	 *
	 * @var int
	 * @since  3.0.0
	 */
	public $count = 0;

	/**
	 * Total results
	 *
	 * @var int
	 * @since  3.0.0
	 */
	public $total = 0;

	/**
	 * The arguments for the data set
	 *
	 * @var array
	 * @since   3.0.0
	 */
	public $args = array();

	/**
	 * Get things started
	 *
	 * @since  3.0.0
	 * @see   WP_List_Table::__construct()
	 */
	public function __construct() {
		global $status, $page;

		// Set parent defaults
		parent::__construct(
			array(
				'singular' => __( 'User', 'gdpr-cookie-consent' ),
				'plural'   => __( 'Users', 'gdpr-cookie-consent' ),
				'ajax'     => false,
			)
		);
	}

	/**
	 * Show the search field
	 *
	 * @param string $text     Label for the search box
	 * @param string $input_id ID of the search box
	 *
	 * @return void
	 * @since  3.0.0
	 */
	public function search_box( $text, $input_id ) {
		$input_id = $input_id . '-search-input-data-request';

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="'
				. esc_attr( $_REQUEST['orderby'] ) . '" />';
		}
		if ( ! empty( $_REQUEST['order'] ) ) {
			echo '<input type="hidden" name="order" value="'
				. esc_attr( $_REQUEST['order'] ) . '" />';
		}
		$search = $this->get_search();
		?>
		<div class="search-and-export-container">
			<div class="search-box">
				<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>">
					<?php echo esc_html( $text ); ?>:
				</label>
				<input placeholder="Search Requests" type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php echo esc_html( $search ); ?>"/>
				<img id="search-logo-data-request" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/vector.png'; ?>" alt="Search Logo">
				<?php
				submit_button(
					$text,
					'button',
					false,
					false,
					array( 'ID' => 'search-submit-data-request' )
				);
				?>
			</div>
		</div>
		<script type="text/javascript">
			document.getElementById('search-logo-data-request').addEventListener('click', function() {
				document.getElementById('search-submit-data-request').click();
			});
		</script>
					<?php

			echo $this->resolved_select();

			?>
		<?php
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @return string Name of the primary column.
	 * @since   3.0.0
	 * @access protected
	 */
	protected function get_primary_column_name() {
		return 'name';
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param array  $item        Contains all the data of the customers
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 * @since  3.0.0
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
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_name( $item ) {
		$name  = '#' . $item['ID'] . ' ';
		$name .= ! empty( $item['name'] ) ? $item['name'] : '<em>' . __( 'Unnamed user', 'gdpr-cookie-consent' ) . '</em>';
		return $name;
	}

	/**
	 * Column name actions
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_actions_resolve_delete( $item ) {
		$actions = array(
			'resolve' => '<a href="' . admin_url( 'admin.php?page=gdpr-cookie-consent&action=resolve&id=' . $item['ID'] ) . '">' . __( 'Resolve', 'gdpr-cookie-consent' ) . '</a>',
			'delete'  => '<a href="' . admin_url( 'admin.php?page=gdpr-cookie-consent&action=delete&id=' . $item['ID'] ) . '">' . __( 'Delete', 'gdpr-cookie-consent' ) . '</a>',
		);

		return $this->row_actions( $actions );
	}
	/**
	 * Retrieve the table columns
	 *
	 * @return array $columns Array of all the list table columns
	 * @since  3.0.0
	 */
	public function get_columns() {
		$columns = array(
			'cb'                     => '<input type="checkbox"/>',
			'name'                   => __( 'Name', 'gdpr-cookie-consent' ),
			'email'                  => __( 'Email', 'gdpr-cookie-consent' ),
			'resolved'               => __( 'Status', 'gdpr-cookie-consent' ),
			'datarequest'            => __( 'Data request', 'gdpr-cookie-consent' ),
			'date'                   => __( 'Date', 'gdpr-cookie-consent' ),
			'actions_resolve_delete' => __( 'Actions', 'gdpr-cookie-consent' ),
		);

		return apply_filters( 'wpl_report_customer_columns', $columns );
	}

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
	 * @since  3.0.0
	 */
	public function get_sortable_columns() {
		return array(
			'request_date'           => array( 'request_date', true ),
			'name'                   => array( 'name', true ),
			'region'                 => array( 'region', true ),
			'email'                  => array( 'email', true ),
			'resolved'               => array( 'resolved', true ),
			'date'                   => array( 'date', true ),
			'datarequest'            => array( 'datarequest', true ),
			'actions_resolve_delete' => array( 'actions_resolve_delete', true ),
		);
	}

	/**
	 * Outputs the reporting views
	 *
	 * @return void
	 * @since  3.0.0
	 */
	public function get_bulk_actions( $which = '' ) {

		$actions      = array(
			'delete'  => __( 'Delete', 'gdpr-cookie-consent' ),
			'reslove' => __( 'Resolve', 'gdpr-cookie-consent' ),
		);
		// echo $this->resolved_select();
		return $actions;
	}

	/**
	 * Process bulk actions
	 *
	 * @access      private
	 * @since        3.0.0
	 * @return      void
	 */
	public function process_bulk_action() {

		$ids = isset( $_GET['user_id'] ) ? $_GET['user_id'] : false;
		$action = $this->current_action();
		if ( ! $action ) {
			// If no action was found, check for action2 (bottom dropdown)
			$action = isset($_GET['action2']) && $_GET['action2'] != '-1' ? $_GET['action2'] : false;
		}
		if ( ! $ids ) {
			return;
		}

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		foreach ( $ids as $id ) {
			if ( 'delete' === $action ) {
				global $wpdb;
				$wpdb->delete( $wpdb->prefix . 'wpl_data_req', array( 'ID' => intval( $id ) ) );
				$paged = isset( $_GET['paged'] ) ? 'paged=' . intval( $_GET['paged'] ) : '';
			} elseif ( 'reslove' === $action ) {
				global $wpdb;
				$wpdb->update(
					$wpdb->prefix . 'wpl_data_req',
					array(
						'resolved' => 1,
					),
					array( 'ID' => intval( $id ) )
				);
				$paged = isset( $_GET['paged'] ) ? 'paged=' . intval( $_GET['paged'] ) : '';
				wp_redirect( admin_url( 'admin.php?page=wpl-datarequests' . $paged ) );

			}
		}
	}

	/**
	 * Retrieve the current page number
	 *
	 * @return int Current page number
	 * @since  3.0.0
	 */
	public function get_paged() {
		return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	}

	/**
	 * Retrieves the search query string
	 *
	 * @return mixed string If search is present, false otherwise
	 * @since  3.0.0
	 */
	public function get_search() {
		return ! empty( $_GET['s'] ) ? urldecode( trim( $_GET['s'] ) ) : false;
	}


	public function resolved_select() {
		// Option Select
		$options  = array(
			0 => __( 'ALL', 'gdpr-cookie-consent' ),
			1 => __( 'Resolved', 'gdpr-cookie-consent' ),
			2 => __( 'Unresolved', 'gdpr-cookie-consent' ),
		);
		$selected = 0;

		if ( isset( $_GET['wpl_resolved_select2'] ) ) {
			$selected = intval( $_GET['wpl_resolved_select2'] );
		}
		// Generate a unique identifier for the select element
		$unique_id = uniqid( 'wpl_resolved_select_' );

		?>
		<!-- submit the form on change  -->
		<script>
			
			document.addEventListener('DOMContentLoaded', function () {
				var resolvedSelect = document.getElementById('<?php echo esc_js( $unique_id ); ?>');
				if (resolvedSelect) {
					resolvedSelect.addEventListener('change', function () {
						document.getElementById('wpl-dnsmpd-filter-datarequest').submit();
					});
				}
			});
		</script>
	
		<?php
		// Use the unique identifier in the select element's id attribute
		echo '<select name="wpl_resolved_select2" id="' . esc_js( $unique_id ) . '" class="wpl_resolved_select_filter">';
		foreach ( $options as $value => $label ) {
			echo '<option value="' . esc_attr( $value ) . '" ' . ( $selected == $value ? 'selected' : '' ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select>';
	}

	/**
	 * Build all the reports data
	 *
	 * @return array $reports_data All the data for customer reports
	 * @global object $wpdb Used to query the database using the WordPress
	 *                      Database API
	 * @since  3.0.0
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

		if ( isset( $_GET['wpl_resolved_select2'] ) ) {
			$args['resolved'] = intval( $_GET['wpl_resolved_select2'] );
		}

		$this->args = $args;
		$requests   = $this->get_requests( $args );
		if ( $requests ) {
			foreach ( $requests as $request ) {
				$datarequest = '';

				$options = Gdpr_Cookie_Consent_Admin::wpl_data_reqs_options();
				foreach ( $options as $fieldname => $label ) {
					if ( $request->{$fieldname} == 1 ) {
						$datarequest = '<a href="https://club.wpeka.com/' . $label['slug'] . '" target="_blank">' . $label['short'] . '</a>';
					}
				}
				$time = gmdate( get_option( 'time_format' ), $request->request_date );
				$date = $this->wpl_localize_date( $request->request_date );

				// Translators: Placeholder %1$s represents the date, %2$s represents the time.
				$date_time_format = __( '%1$s at %2$s', 'gdpr-cookie-consent' );

				$date = sprintf( $date_time_format, $date, $time );

				$data[] = array(
					'ID'          => $request->ID,
					'name'        => $request->name,
					'email'       => $request->email,
					'resolved'    => $request->resolved,
					'datarequest' => $datarequest,
					'date'        => $date,
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
		$hidden   = array(); // No hidden columns
		$sortable = $this->get_sortable_columns();
		$this->process_bulk_action();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $this->reports_data();
		$this->total           = $this->count_requests( $this->args );
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
	 * Count number of users
	 *
	 * @param $args
	 *
	 * @return int
	 */
	public function count_requests( $args ) {
		unset( $args['number'] );
		$users = $this->get_requests( $args );
		return count( $users );
	}

	/**
	 * Get users
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function get_requests( $args ) {

		global $wpdb;
		$table_name = $wpdb->prefix . 'wpl_data_req';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) !== $table_name ) {
			// Table doesn't exist yet, return empty result set or handle as needed
			return array();
		}
		$sql = "SELECT * from {$wpdb->prefix}wpl_data_req WHERE 1=1 ";
		if ( isset( $args['email'] ) && ! empty( $args['email'] ) && is_email( $args['email'] ) ) {
			$sql .= " AND email like '" . '%' . sanitize_email( $args['email'] ) . '%' . "'";
		}

		if ( isset( $args['name'] ) && ! empty( $args['name'] ) ) {
			$sql .= " AND name like '%" . sanitize_text_field( $args['name'] ) . "%'";
		}

		if ( isset( $args['resolved'] ) ) {

			if ( intval( $args['resolved'] ) == 1 ) {

				$sql .= ' AND resolved = ' . intval( $args['resolved'] );

			} elseif ( intval( $args['resolved'] ) == 2 ) {

				$args['resolved'] = 0;
				$sql             .= ' AND resolved = ' . intval( $args['resolved'] );
			}
		}

		$sql .= ' ORDER BY ' . sanitize_title( $args['orderby'] ) . ' ' . sanitize_title( $args['order'] );
		if ( isset( $args['number'] ) ) {
			$sql .= ' LIMIT ' . intval( $args['number'] ) . ' OFFSET ' . intval( $args['offset'] );
		}
		// as no data is inserting into the table so no $wpdb->prepare() is needed;
		return $wpdb->get_results( $sql ); // phpcs:ignore
	}

	public function wpl_localize_date( $unix_time ) {

		$formatted_date    = gmdate( get_option( 'date_format' ), $unix_time );
		$month             = gmdate( 'F', $unix_time );
		$month_localized   = date_i18n( 'F', $unix_time );
		$date              = str_replace( $month, $month_localized, $formatted_date );
		$weekday           = gmdate( 'l', $unix_time );
		$weekday_localized = date_i18n( 'l', $unix_time );
		$date              = str_replace( $weekday, $weekday_localized, $date );
		return $date;
	}

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
