<?php
/**
 * The policy data functionality of the plugin.
 *
 * @since      1.9
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/includes
 * @author     wpeka <https://club.wpeka.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The functionality for policy data custom post type.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/includes
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent_Policy_Data {

	/**
	 * Gdpr_Cookie_Consent_Policy_Data constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'gdpr_register_custom_post_type' ) );
		if ( Gdpr_Cookie_Consent::is_request( 'admin' ) ) {
			add_action( 'add_meta_boxes', array( $this, 'gdpr_add_meta_box' ) );
			add_action( 'save_post', array( $this, 'gdpr_save_custom_metabox' ) );
			add_action( 'manage_edit-gdprpolicies_columns', array( $this, 'gdpr_manage_edit_columns' ) );
			add_action( 'manage_posts_custom_column', array( $this, 'gdpr_manage_custom_columns' ) );
			add_action( 'admin_head-edit.php', array( $this, 'gdpr_add_policies_import_button' ) );
			add_action( 'admin_head-edit.php', array( $this, 'gdpr_add_policies_export_button' ) );
			add_action( 'admin_post_gdpr_policies_export.csv', array( $this, 'gdpr_process_csv_export_policies' ) );
			add_action( 'admin_init', array( $this, 'gdpr_process_csv_policies' ) );
		}
	}

	/**
	 * Registers policy data post type.
	 *
	 * @since 1.9
	 */
	public function gdpr_register_custom_post_type() {
		$labels = array(
			'name'               => __( 'Policy Data', 'gdpr-cookie-consent' ),
			'all_items'          => __( 'Policy Data List', 'gdpr-cookie-consent' ),
			'singular_name'      => __( 'Policy Data', 'gdpr-cookie-consent' ),
			'add_new'            => __( 'Add New', 'gdpr-cookie-consent' ),
			'add_new_item'       => __( 'Add New Policy Data', 'gdpr-cookie-consent' ),
			'edit_item'          => __( 'Edit Policy Data', 'gdpr-cookie-consent' ),
			'new_item'           => __( 'New Policy Data', 'gdpr-cookie-consent' ),
			'view_item'          => __( 'View Policy Data', 'gdpr-cookie-consent' ),
			'search_items'       => __( 'Search Policy Data', 'gdpr-cookie-consent' ),
			'not_found'          => __( 'Nothing found', 'gdpr-cookie-consent' ),
			'not_found_in_trash' => __( 'Nothing found in Trash', 'gdpr-cookie-consent' ),
			'parent_item_colon'  => '',
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
			'capabilities'        => array(
				'publish_posts'       => 'manage_options',
				'edit_posts'          => 'manage_options',
				'edit_others_posts'   => 'manage_options',
				'delete_posts'        => 'manage_options',
				'delete_others_posts' => 'manage_options',
				'read_private_posts'  => 'manage_options',
				'edit_post'           => 'manage_options',
				'delete_post'         => 'manage_options',
				'read_post'           => 'manage_options',
			),
			'supports'            => array( 'title', 'editor' ),
		);
		register_post_type( GDPR_POLICY_DATA_POST_TYPE, $args );
	}

	/**
	 * Add metaboxes to policy data post type.
	 *
	 * @since 1.9
	 */
	public function gdpr_add_meta_box() {
		add_meta_box( '_gdpr_policies_domain', 'Domain', array( $this, 'gdpr_metabox_policies_domain' ), 'gdprpolicies', 'normal', 'default' );
		add_meta_box( '_gdpr_policies_links', 'Links', array( $this, 'gdpr_metabox_policies_links' ), 'gdprpolicies', 'normal', 'default' );
	}

	/**
	 * Add meta to policy data post type.
	 *
	 * @since 1.9
	 */
	public function gdpr_metabox_policies_domain() {
		global $post;
		$custom          = get_post_custom( $post->ID );
		$policies_domain = ( isset( $custom['_gdpr_policies_domain'][0] ) ) ? $custom['_gdpr_policies_domain'][0] : '';
		wp_nonce_field( 'gdpr_save_custom_metabox', '_gdpr_policies_domain_nonce' );
		?>
		<input id="_gdpr_policies_domain" name="_gdpr_policies_domain" value="<?php echo esc_attr( sanitize_text_field( $policies_domain ) ); ?>" />
		<?php
	}

	/**
	 * Add meta to policy data post type.
	 *
	 * @since 1.9
	 */
	public function gdpr_metabox_policies_links() {
		global $post;
		$custom    = get_post_custom( $post->ID );
		$content   = ( isset( $custom['_gdpr_policies_links_editor'][0] ) ) ? $custom['_gdpr_policies_links_editor'][0] : '';
		$editor_id = '_gdpr_policies_links_editor';
		wp_nonce_field( 'gdpr_save_custom_metabox', '_gdpr_policies_links_editor_nonce' );
		wp_editor( $content, $editor_id );
	}

	/**
	 * Save meta to policy data post type.
	 *
	 * @since 1.9
	 */
	public function gdpr_save_custom_metabox() {
		global $post;
		if ( isset( $_POST['_gdpr_policies_domain'] ) && check_admin_referer( 'gdpr_save_custom_metabox', '_gdpr_policies_domain_nonce' ) ) {
			update_post_meta( $post->ID, '_gdpr_policies_domain', sanitize_text_field( wp_unslash( $_POST['_gdpr_policies_domain'] ) ) );
		}
		if ( isset( $_POST['_gdpr_policies_links_editor'] ) && check_admin_referer( 'gdpr_save_custom_metabox', '_gdpr_policies_links_editor_nonce' ) ) {
			$data = wp_kses_post( $_POST['_gdpr_policies_links_editor'] ); // phpcs:ignore input var ok, CSRF ok, sanitization ok.
			update_post_meta( $post->ID, '_gdpr_policies_links_editor', $data );
		}
	}

	/**
	 * Manage columns in policy data post type.
	 *
	 * @since 1.9
	 * @param array $columns comment columns array.
	 * @return array
	 */
	public function gdpr_manage_edit_columns( $columns ) {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'title'       => __( 'Company Name', 'gdpr-cookie-consent' ),
			'gdprpurpose' => __( 'Policy Purpose', 'gdpr-cookie-consent' ),
			'gdprlinks'   => __( 'Links', 'gdpr-cookie-consent' ),
			'gdprdomain'  => __( 'Domain', 'gdpr-cookie-consent' ),
		);
		return $columns;
	}

	/**
	 * Manage columns in policy data post type.
	 *
	 * @param string $column comment column name.
	 * @param int    $post_id comment post id.
	 * @since 1.9
	 */
	public function gdpr_manage_custom_columns( $column, $post_id = 0 ) {
		global $post;

		switch ( $column ) {
			case 'title':
				if ( isset( $post->post_title ) ) {
					echo esc_attr( $post->post_title, 'gdpr-cookie-consent' );
				}
				break;
			case 'gdprpurpose':
				if ( isset( $post->post_content ) ) {
					echo esc_attr( $post->post_content, 'gdpr-cookie-consent' );
				}
				break;
			case 'gdprlinks':
				$custom = get_post_custom();
				if ( isset( $custom['_gdpr_policies_links_editor'][0] ) ) {
					echo wp_kses_post( $custom['_gdpr_policies_links_editor'][0], 'gdpr-cookie-consent' );
				}
				break;
			case 'gdprdomain':
				$custom = get_post_custom();
				if ( isset( $custom['_gdpr_policies_domain'][0] ) ) {
					echo esc_attr( $custom['_gdpr_policies_domain'][0], 'gdpr-cookie-consent' );
				}
				break;

		}
	}

	/**
	 * Adds csv import button.
	 *
	 * @since 1.9
	 */
	public function gdpr_add_policies_import_button() {
		global $current_screen;
		if ( GDPR_POLICY_DATA_POST_TYPE !== $current_screen->post_type ) {
			return;
		}
		$scan_import_menu = __( 'Import from CSV', 'gdpr-cookie-consent' );

		$import_page = admin_url( 'edit.php?page=gdpr-policies-import' );

		?>
		<script type="text/javascript">
			jQuery(document).ready( function($)
			{
				jQuery('<a class="add-new-h2" href="<?php echo esc_attr( $import_page ); ?>"><?php echo esc_attr( $scan_import_menu ); ?></a>').insertAfter(".wrap h1");
			});
		</script>
		<?php
	}

	/**
	 * Adds csv export button.
	 *
	 * @since 1.9
	 */
	public function gdpr_add_policies_export_button() {
		global $current_screen;
		if ( GDPR_POLICY_DATA_POST_TYPE !== $current_screen->post_type ) {
			return;
		}
		$scan_export_menu = __( 'Export as CSV', 'gdpr-cookie-consent' );
		?>
		<script type="text/javascript">
			jQuery(document).ready( function($)
			{
				jQuery("<a  href='<?php echo esc_attr( admin_url( 'admin-post.php?action=gdpr_policies_export.csv' ) ); ?>' id='export_gdpr_policies' class='add-new-h2'><?php echo esc_attr( $scan_export_menu ); ?></a>").insertAfter(".wrap h1");
			});
		</script>
		<?php
	}

	/**
	 * Export policy data functionality.
	 *
	 * @since 1.9
	 */
	public function gdpr_process_csv_export_policies() {
		global $wpdb;

		$wpdb->hide_errors();
		if ( function_exists( 'apache_setenv' ) ) {
			apache_setenv( 'no-gzip', 1 ); // @codingStandardsIgnoreLine
		}
		ini_set( 'zlib.output_compression', 0 ); // @codingStandardsIgnoreLine
		ob_clean();

		header( 'Content-Type: text/csv; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename=gdpr-policies.csv' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		$fp            = fopen( 'php://output', 'w' );
		$row           = array();
		$policy_fields = array(
			'post_title',
			'post_content',
			'post_status',
			'_gdpr_policies_links_editor',
			'_gdpr_policies_domain',
		);

		foreach ( $policy_fields as $column ) {
			$row[] = self::format_data( $column );
		}

		fwrite( $fp, implode( ',', $row ) . "\n" ); // @codingStandardsIgnoreLine
		unset( $row );

		$gdpr_args = apply_filters(
			'gdpr_csv_export_policies_args',
			array(
				'post_status' => array( 'publish', 'draft' ),
				'post_type'   => array( GDPR_POLICY_DATA_POST_TYPE ),
				'orderby'     => 'ID',
				'numberposts' => -1,
				'order'       => 'ASC',
			)
		);

		$policies = get_posts( $gdpr_args );

		if ( ! $policies || is_wp_error( $policies ) ) {
			fclose( $fp ); // @codingStandardsIgnoreLine
			exit;
		}

		foreach ( $policies as $policy ) {
			$row       = array();
			$meta_data = get_post_custom( $policy->ID );
			foreach ( $policy_fields as $column ) {
				switch ( $column ) {
					case 'post_title':
						$row[] = self::format_data( $policy->post_title );
						break;
					case 'post_content':
						$row[] = self::format_data( $policy->post_content );
						break;
					case 'post_status':
						$row[] = self::format_data( $policy->post_status );
						break;
					case '_gdpr_policies_links_editor':
						$row[] = self::format_data( $meta_data['_gdpr_policies_links_editor'][0] );
						break;
					case '_gdpr_policies_domain':
						$row[] = self::format_data( $meta_data['_gdpr_policies_domain'][0] );
						break;
					default:
						break;
				}
			}
			fputcsv( $fp, $row, ',', '"' );
			unset( $row );
		}
		unset( $policies );
		fclose( $fp ); // @codingStandardsIgnoreLine
		exit;
	}

	/**
	 * Format data for csv.
	 *
	 * @since 1.9
	 * @param string $data comment data.
	 * @return string
	 */
	public static function format_data( $data ) {
		$enc  = mb_detect_encoding( $data, 'UTF-8, ISO-8859-1', true );
		$data = ( 'UTF-8' === $enc ) ? $data : utf8_encode( $data );
		return $data;
	}

	/**
	 * Process policy data csv.
	 *
	 * @since 1.9
	 */
	public function gdpr_process_csv_policies() {
		if ( isset( $_POST['_wpnonce-gdpr-policies-import-page'] ) ) {
			check_admin_referer( 'gdpr-policies-import-page', '_wpnonce-gdpr-policies-import-page' );

			if ( ! empty( $_FILES['policies_csv']['tmp_name'] ) ) {
				// Setup settings variables.
				$filename = sanitize_text_field( wp_unslash( $_FILES['policies_csv']['tmp_name'] ) );

				$result = $this->gdpr_import_csv_policies( $filename );

				if ( ! $result['post_ids'] ) { // Some posts imported.
					wp_safe_redirect( add_query_arg( 'import', 'fail', wp_get_referer() ) );
				} elseif ( $result['errors'] ) { // Some posts imported.
					wp_safe_redirect( add_query_arg( 'import', 'errors', wp_get_referer() ) );
				} else { // All posts imported.
					wp_safe_redirect( add_query_arg( 'import', 'success', wp_get_referer() ) );
				}

				exit;
			}
			wp_safe_redirect( add_query_arg( 'import', 'file', wp_get_referer() ) );
			exit();
		}
	}

	/**
	 * Import policy data functionality.
	 *
	 * @since 1.9
	 * @param string $filename comment filename.
	 * @return array
	 */
	public function gdpr_import_csv_policies( $filename ) {
		$errors   = array();
		$post_ids = array();

		$gdprpolicies_fields = array(
			'post_title',
			'post_content',
			'post_status',
			'_gdpr_policies_links_editor',
			'_gdpr_policies_domain',
		);

		$gdprpolicies_meta_fields = array(
			'_gdpr_policies_links_editor',
			'_gdpr_policies_domain',
		);

		$file_handle = @fopen( $filename, 'r' ); // @codingStandardsIgnoreLine
		if ( $file_handle ) {
			$csv_reader = new Gdpr_Cookies_Read_Csv( $file_handle, GDPR_CSV_DELIMITER, "\xEF\xBB\xBF" ); // Skip any UTF-8 byte order mark.
			$first      = true;
			$rkey       = 0;
			while ( ( $csv_reader->get_row() ) !== null ) {
				$line = $csv_reader->get_row();
				if ( empty( $line ) ) {
					if ( $first ) { // If the first line is empty, abort.
						break;
					} else { // If another line is empty, just skip it.
						continue;
					}
				}
				// If we are on the first line, the columns are the headers.
				if ( $first ) {
					$headers = $line;
					$first   = false;
					continue;
				}
				// Separate post data from meta.
				$gdpr_policies_data = array();
				foreach ( $line as $ckey => $column ) {
					$column_name = $headers[ $ckey ];
					$column      = trim( $column );
					if ( in_array( $column_name, $gdprpolicies_fields, true ) ) {
						$gdpr_policies_data[ $column_name ] = $column;
					}
				}
				// A plugin may need to filter the data and meta.
				$gdpr_policies_data = apply_filters( 'modify_import_gdprpoliciesdata', $gdpr_policies_data );
				// If no data, bailout!
				if ( empty( $gdpr_policies_data ) ) {
					continue;
				}
				// Some plugins may need to do things before importing one policy.
				do_action( 'gdprpolicies_pre_import', $gdpr_policies_data );
				$post_data = array(
					'post_author'   => get_current_user_id(),
					'post_date'     => date( 'Y-m-d H:i:s', strtotime( 'now' ) ),
					'post_date_gmt' => date( 'Y-m-d H:i:s', strtotime( 'now' ) ),
					'post_content'  => isset( $gdpr_policies_data['post_content'] ) ? $gdpr_policies_data['post_content'] : '',
					'post_title'    => $gdpr_policies_data['post_title'],
					'post_name'     => ( sanitize_title( $gdpr_policies_data['post_title'] ) ),
					'post_status'   => ( $gdpr_policies_data['post_status'] ) ? $gdpr_policies_data['post_status'] : 'publish',
					'post_parent'   => 0,
					'post_type'     => GDPR_POLICY_DATA_POST_TYPE,
				);
				$post_id   = post_exists( $gdpr_policies_data['post_title'] );
				if ( $post_id ) {
					$post_id = wp_update_post( $post_data, true );
				} else {
					$post_id = wp_insert_post( $post_data, true );
				}
				if ( is_wp_error( $post_id ) ) {
					$errors[ $rkey ] = $post_id;
				} else {
					// If no error, let's update the post meta and taxonomy!
					if ( $post_id ) {
						foreach ( $gdprpolicies_meta_fields as $metakey ) {
							if ( isset( $gdpr_policies_data[ $metakey ] ) ) {
								$metavalue = maybe_unserialize( $gdpr_policies_data[ $metakey ] );
								update_post_meta( $post_id, $metakey, $metavalue );
							}
						}
					}
					// Some plugins may need to do things after one post has been imported.
					do_action( 'gdprpolicies_post_import', $post_id );
					$post_ids[] = $post_id;
				}
				$rkey++;
			}
			fclose( $file_handle ); // @codingStandardsIgnoreLine
		} else {
			$errors[] = new WP_Error( 'file_read', 'Unable to open CSV file.' );
		}
		return array(
			'post_ids' => $post_ids,
			'errors'   => $errors,
		);
	}

}
new Gdpr_Cookie_Consent_Policy_Data();
