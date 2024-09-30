<?php
/**
 * Policy data Reports Table Class
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class GDPR_Policy_Data_Table extends WP_List_Table {

	/**
	 * Number of items per page
	 *
	 * @var int
	 * @since 2.16.0
	 */
	public $per_page = 10;

	/**
	 * Number of results found
	 *
	 * @var int
	 * @since 2.16.0
	 */
	public $count = 0;

	/**
	 * Total results
	 *
	 * @var int
	 * @since 2.16.0
	 */
	public $total = 0;

	/**
	 * The arguments for the data set
	 *
	 * @var array
	 * @since  2.16.0
	 */
	public $args = array();

	/**
	 * Get things started
	 *
	 * @since 2.16.0
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
	 * @since 2.16.0
	 */
	public function search_box( $text, $input_id ) {
		$input_id = $input_id . '-search-input-policy-data';

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
				<input placeholder="Search Policy Data"type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php echo esc_html( $search ); ?>"/>
				<img id="search-logo-policy-data" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/vector.png'; ?>" alt="Search Logo">
				<?php
				submit_button(
					$text,
					'button',
					false,
					false,
					array( 'ID' => 'search-submit-policy-data' )
				);
				?>
			</div>
		</div>
		<script type="text/javascript">
			document.getElementById('search-logo-policy-data').addEventListener('click', function() {
				document.getElementById('search-submit-policy-data').click();
			});
		</script>
		<?php
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @return string Name of the primary column.
	 * @since  2.16.0
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
	 * @since 2.16.0
	 */
	public function column_default( $item, $column_name ) {

		$this->column_name( $item, $column_name );

		switch ( $column_name ) {
			case 'resolve':
				$value = $item['gdprdomain'] ? __( 'Resolved', 'gdpr-cookie-consent' ) : __( 'Open', 'gdpr-cookie-consent' );
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
	public function column_name( $item, $column_name ) {

		$item_ID = $item['ID'];
		$nonce   = wp_create_nonce( 'gdpr_policy_delete_nonce_' . $item_ID ); // Create nonce using the item's ID
		switch ( $column_name ) {
			case 'title':
				?>

				<?php
				break;
			default:
				$value = isset( $item[ $column_name ] ) ? $item[ $column_name ] : null;
				break;
		}
	}
	/**
	 * Column name actions
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_actions_policy_data( $item ) {
		$item_ID = $item['ID'];
		$nonce   = wp_create_nonce( 'gdpr_policy_delete_nonce_' . $item_ID ); // Create nonce using the item's ID
		$actions = array(
			'edit'          => '<a href="' . admin_url( 'post.php?post=' . $item['ID'] . '&action=edit' ) . '">' . __( 'Edit', 'gdpr-cookie-consent' ) . '</a>',
			'policy_delete' => '<a href="' . admin_url( 'admin.php?page=gdpr-cookie-consent&action=policy_delete&id=' . $item['ID'] . '&_wpnonce=' . $nonce ) . '">' . __( 'Trash', 'gdpr-cookie-consent' ) . '</a>', // Add nonce to the delete action URL
		);

		return $this->row_actions( $actions );
	}
	/**
	 * Retrieve the table columns
	 *
	 * @return array $columns Array of all the list table columns
	 * @since 2.16.0
	 */
	public function get_columns() {
		$columns = array(
			'cb'                  => '<input type="checkbox"/>',
			'title'               => __( 'Company Name', 'gdpr-cookie-consent' ),
			'gdprpurpose'         => __( 'Policy Purpose', 'gdpr-cookie-consent' ),
			'gdprlinks'           => __( 'Links', 'gdpr-cookie-consent' ),
			'gdprdomain'          => __( 'Domain', 'gdpr-cookie-consent' ),
			'actions_policy_data' => __( 'Actions', 'gdpr-cookie-consent' ),
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
	 * @since 2.16.0
	 */
	public function get_sortable_columns() {
		return array(
			'request_date'        => array( 'request_date', true ),
			'title'               => array( 'title', true ),
			'region'              => array( 'region', true ),
			'gdprpurpose'         => array( 'gdprpurpose', true ),
			'gdprlinks'           => array( 'gdprlinks', true ),
			'gdprdomain'          => array( 'gdprdomain', true ),
			'actions_policy_data' => array( 'actions_policy_data', true ),
		);
	}

	/**
	 * Outputs the reporting views
	 *
	 * @return void
	 * @since 2.16.0
	 */
	public function get_bulk_actions( $which = '' ) {

		$actions     = array(
			'delete' => __( 'Move to Trash', 'gdpr-cookie-consent' ),
		);
		echo $this->resolved_select();
		return $actions;
	}

	/**
	 * Process bulk actions
	 *
	 * @access      private
	 * @since       2.16.0
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

		foreach ( $ids as $id ) {
			if ( 'delete' === $this->current_action() ) {

				wp_delete_post( $id, true );

				$paged = isset( $_GET['paged'] ) ? 'paged=' . intval( $_GET['paged'] ) : '';
			}
		}
	}

	/**
	 * Retrieve the current page number
	 *
	 * @return int Current page number
	 * @since 2.16.0
	 */
	public function get_paged() {
		return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	}

	/**
	 * Retrieves the search query string
	 *
	 * @return mixed string If search is present, false otherwise
	 * @since 2.16.0
	 */
	public function get_search() {
		return ! empty( $_GET['s'] ) ? urldecode( trim( $_GET['s'] ) ) : false;
	}


	public function resolved_select() {

		$options = array();

		// Query to get all posts
		$all_posts = get_posts(
			array(
				'post_type'      => 'gdprpolicies',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'orderby'        => 'ID',
				'order'          => 'DESC',
			)
		);

		// Loop through each post to extract creation dates and populate the options array
		foreach ( $all_posts as $post ) {
			$post_date  = strtotime( $post->post_date ); // Get the UNIX timestamp of the post creation date
			$month_year = gmdate( 'F Y', $post_date ); // Format the timestamp to month and year

			// Check if the month_year is not already added to the options array
			if ( ! in_array( $month_year, $options ) ) {
				// Add the month_year as an option
				$options[] = $month_year;
			}
		}

		// Add an 'ALL Dates' option at the beginning of the array
		array_unshift( $options, __( 'ALL Dates', 'gdpr-cookie-consent' ) );

		$selected = 0;
		if ( isset( $_GET['wpl_resolved_select1'] ) ) {
			$selected = intval( $_GET['wpl_resolved_select1'] );
		}

		?>
		<!-- submit the form on change  -->
		<script>
				document.addEventListener('DOMContentLoaded', function() {
					var resolvedSelect = document.getElementById('wpl_resolved_select');
					if (resolvedSelect) {
						resolvedSelect.addEventListener('change', function() {
							document.getElementById('wpl-dnsmpd-filter').submit();
						});
					}
				});
		</script>

		<?php

		echo '<select name="wpl_resolved_select1" id="wpl_resolved_select" class="wpl_resolved_select">';
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
	 * @since 2.16.0
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

		if ( isset( $_GET['wpl_resolved_select1'] ) ) {
			$args['month'] = intval( $_GET['wpl_resolved_select1'] );
		}

		$this->args = $args;

		$requests = $this->get_requests( $args );

		if ( $requests ) {
			foreach ( $requests as $request ) {
				$data[] = array(
					'ID'          => $request['ID'],
					'title'       => $request['title'],
					'gdprpurpose' => $request['gdprpurpose'],
					'gdprlinks'   => $request['gdprlinks'],
					'gdprdomain'  => $request['gdprdomain'],
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
	 * Count number of users
	 *
	 * @param $args
	 *
	 * @return int
	 */
	public function count_requests() {

		global $post;
		$custom_posts = get_posts(
			array(
				'post_type'      => 'gdprpolicies',
				'posts_per_page' => -1,
				'post_status'    => 'publish', // Retrieve all posts
			)
		);

		$data  = array(); // Initialize the $data array
		$count = 0;
		foreach ( $custom_posts as $post ) {
			++$count;
		}

		return $count;
	}

	/**
	 * Get users
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function get_requests( $args ) {
		global $post;
		$number = isset( $args['number'] ) ? intval( $args['number'] ) : 10;
		$offset = isset( $args['offset'] ) ? intval( $args['offset'] ) : 0;
		$search = isset( $args['search'] ) ? sanitize_text_field( $args['search'] ) : '';
		$month  = isset( $args['month'] ) ? intval( $args['month'] ) : 0;
		
		$post_args = array(
			'post_type'      => 'gdprpolicies',
			'posts_per_page' => $number,
			'offset'         => $offset,
			'post_status'    => 'publish', // Retrieve all posts
			'orderby'        => 'ID',
			'order'          => 'DESC',
			'meta_query'     => array(),
		);
		// search on the basis company name
		// Search by post title
		if ( ! empty( $search ) ) {
			$post_args['s']     = $search;
			$post_args['exact'] = true;
		}

		if ( $month >= 1 && $month <= 12 ) {
			$post_args['date_query'] = array(
				array(
					'month' => $month,
				),
			);
		}

		$custom_posts     = get_posts( $post_args );
		$all_consent_data = array(); // Initialize the $data array

		foreach ( $custom_posts as $post ) {

			setup_postdata( $post ); // Setup post data for each post

			$post_id = $post->ID;

			// title

			if ( isset( $post->post_title ) ) {
				$policy_data_title = $post->post_title;
			}

			// purpose

			if ( isset( $post->post_content ) ) {
				$policy_data_purpose = esc_attr( $post->post_content );
			}

			// links

			$custom = get_post_custom();
			if ( isset( $custom['_gdpr_policies_links_editor'][0] ) ) {
				$policy_data_links = wp_kses_post( $custom['_gdpr_policies_links_editor'][0] );
			}
			// Domain
			if ( isset( $custom['_gdpr_policies_domain'][0] ) ) {
				$policy_data_domain = esc_attr( $custom['_gdpr_policies_domain'][0] );
			}

			// all data for table
			$all_consent_data[] = array(
				'ID'          => $post_id,
				'title'       => $policy_data_title,
				'gdprpurpose' => $policy_data_purpose,
				'gdprlinks'   => $policy_data_links,
				'gdprdomain'  => $policy_data_domain,
			);

		}

		return $all_consent_data;
	}
}
