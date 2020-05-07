<?php
/**
 * The custom cookie functionality of the plugin.
 *
 * @link       https://club.wpeka.com/
 * @since      1.0
 *
 * @package    Gdpr_Cookie_Consent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require plugin_dir_path( __FILE__ ) . 'classes/class-gdpr-cookie-consent-cookie-custom-ajax.php';
/**
 * The admin-specific functionality for custom cookies.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin/modules
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent_Cookie_Custom {

	/**
	 * Categories table.
	 *
	 * @since 1.0
	 * @access public
	 * @var string $category_table Scan categories table.
	 */
	public $category_table = 'gdpr_cookie_scan_categories';
	/**
	 * Post cookies table.
	 *
	 * @since 1.0
	 * @access public
	 * @var string $post_cookies_table Post cookies table.
	 */
	public $post_cookies_table = 'gdpr_cookie_post_cookies';
	/**
	 * Not to keep records flag.
	 *
	 * @since 1.0
	 * @access public
	 * @var bool $not_keep_records Not to keep records flag.
	 */
	public $not_keep_records = true;

	/**
	 * Gdpr_Cookie_Consent_Cookie_Custom constructor.
	 */
	public function __construct() {
		// Creating necessary tables for cookie custom.
		register_activation_hook( GDPR_COOKIE_CONSENT_PLUGIN_FILENAME, array( $this, 'gdpr_activator' ) );
		$this->status_labels = array(
			0 => '',
			1 => __( 'Incomplete', 'gdpr-cookie-consent' ),
			2 => __( 'Completed', 'gdpr-cookie-consent' ),
			3 => __( 'Stopped', 'gdpr-cookie-consent' ),
		);
		if ( Gdpr_Cookie_Consent::is_request( 'admin' ) ) {
			add_filter( 'gdpr_module_settings_tabhead', array( __CLASS__, 'settings_tabhead' ) );
			add_action( 'gdpr_module_settings_form', array( $this, 'settings_form' ) );
			add_action( 'gdpr_module_settings_general', array( $this, 'settings_general' ), 5 );
		}
	}

	/**
	 * Settings for Cookies About message under General Tab.
	 *
	 * @since 1.0
	 */
	public function settings_general() {
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		?>
			<tr valign="top" gdpr_frm_tgl-id="gdpr_usage_option" gdpr_frm_tgl-val="gdpr">
				<th scope="row"><label for="about_message_field"><?php esc_attr_e( 'About Cookies Message', 'gdpr-cookie-consent' ); ?></label></th>
				<td>
					<?php
					echo '<textarea name="about_message_field" class="vvv_textbox">';
					echo esc_html( apply_filters( 'format_to_edit', stripslashes( $the_options['about_message'] ) ) ) . '</textarea>';
					?>
				</td>
			</tr>
		<?php
	}
	/**
	 * Tab head for settings page.
	 *
	 * @since 1.0
	 * @param array $arr Settings array.
	 *
	 * @return mixed
	 */
	public static function settings_tabhead( $arr ) {
		$arr['gdpr-cookie-consent-cookie-list'] = __( 'Cookie List', 'gdpr-cookie-consent' );
		return $arr;
	}

	/**
	 * Return categories.
	 *
	 * @since 1.0
	 * @return array|mixed|object
	 */
	public function gdpr_get_categories() {
		include plugin_dir_path( __FILE__ ) . '/classes/class-gdpr-cookie-consent-cookie-serve-api.php';
		$cookie_serve_api = new Gdpr_Cookie_Consent_Cookie_Serve_Api();
		$categories       = $cookie_serve_api->get_categories();
		return $categories;
	}

	/**
	 * Returns category array depending on $mode.
	 *
	 * @since 1.0
	 * @param bool $mode Used to return required data.
	 *
	 * @return array|null|object
	 */
	public function gdpr_get_categories_arr( $mode = false ) {
		global $wpdb;
		$out      = array();
		$data_arr = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'gdpr_cookie_scan_categories ORDER BY id_gdpr_cookie_category ASC', ARRAY_A ); // db call ok; no-cache ok.
		if ( $data_arr ) {
			if ( $mode ) {
				foreach ( $data_arr as $key => $value ) {
					$tmp = $data_arr[ $key ];
					if ( 'necessary' === $value['gdpr_cookie_category_slug'] ) {
						$data_arr[ $key ] = $data_arr[0];
						$data_arr[0]      = $tmp;
					}
				}
				$out = $data_arr;
			} else {
				foreach ( $data_arr as $arr ) {
					$out[ $arr['id_gdpr_cookie_category'] ] = $arr['gdpr_cookie_category_name'];
				}
			}
		}
		return $out;
	}

	/**
	 * Returns category array depending on $mode.
	 *
	 * @since 1.0
	 * @param bool $mode Used to return required data.
	 *
	 * @return array|null|object
	 */
	public static function get_categories( $mode = false ) {
		if ( $mode ) {
			$categories = ( new self() )->gdpr_get_categories_arr( $mode );
		} else {
			$categories = ( new self() )->gdpr_get_categories_arr();
		}
		return $categories;
	}

	/**
	 * Returns cookie types.
	 *
	 * @since 1.0
	 * @return array
	 */
	public static function get_types() {
		$types = array(
			'HTTP'        => __( 'HTTP Cookie', 'gdpr-cookie-consent' ),
			'HTML'        => __( 'HTML Local Storage', 'gdpr-cookie-consent' ),
			'Flash Local' => __( 'Flash Local Shared Object', 'gdpr-cookie-consent' ),
			'Pixel'       => __( 'Pixel Tracker', 'gdpr-cookie-consent' ),
			'IndexedDB'   => __( 'IndexedDB', 'gdpr-cookie-consent' ),
		);
		return $types;
	}

	/**
	 * Returns specific cookie type.
	 *
	 * @since 1.0
	 * @param string $type Cookie type.
	 *
	 * @return mixed|void
	 */
	public static function get_cookie_type( $type ) {
		$types = self::get_types();
		if ( isset( $types[ $type ] ) ) {
			return $types[ $type ];
		} else {
			return;
		}
	}

	/**
	 * Displays select box options in admin form for cookie categories and types.
	 *
	 * @since 1.0
	 * @param array  $options Options.
	 * @param string $selected Selection.
	 */
	public static function print_combobox_options( $options, $selected ) {
		foreach ( $options as $key => $value ) {
			echo '<option value="' . esc_html( $key ) . '"';
			if ( strval( $key ) === $selected ) {
				echo ' selected="selected"';
			}
			echo '>' . esc_html( $value ) . '</option>';
		}
	}

	/**
	 * Admin settings form for cookie list tab.
	 *
	 * @since 1.0
	 */
	public function settings_form() {
		$post_cookie_list = $this->get_post_cookie_list();
		wp_enqueue_script( 'gdprcookieconsent_cookie_custom', plugin_dir_url( __FILE__ ) . 'assets/js/cookie-custom' . GDPR_CC_SUFFIX . '.js', array(), GDPR_COOKIE_CONSENT_VERSION, true );
		$params = array(
			'nonces'           => array(
				'gdpr_cookie_custom' => wp_create_nonce( 'gdpr_cookie_custom' ),
			),
			'ajax_url'         => admin_url( 'admin-ajax.php' ),
			'loading_gif'      => plugin_dir_url( __FILE__ ) . 'assets/images/loading.gif',
			'post_cookie_list' => $post_cookie_list,
		);
		wp_localize_script( 'gdprcookieconsent_cookie_custom', 'gdprcookieconsent_cookie_custom', $params );

		$view_file     = 'cookies.php';
		$error_message = '';

		$view_file = plugin_dir_path( __FILE__ ) . 'views/' . $view_file;

		Gdpr_Cookie_Consent::gdpr_envelope_settings_tabcontent( 'gdpr-cookie-consent-cookie-list', $view_file, '', $params, 1, $error_message );
	}

	/**
	 * Run during the plugin's activation to install required tables in database.
	 *
	 * @since 1.0
	 */
	public function gdpr_activator() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		if ( is_multisite() ) {
			// Get all blogs in the network and activate plugin on each one.
			$blog_ids = $wpdb->get_col( 'SELECT blog_id FROM ' . $wpdb->blogs ); // db call ok; no-cache ok.
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				$this->gdpr_install_tables();
				restore_current_blog();
			}
		} else {
			$this->gdpr_install_tables();
		}
	}

	/**
	 * Installs necessary tables.
	 *
	 * @since 1.0
	 */
	public function gdpr_install_tables() {
		global $wpdb;

		$wild = '%';
		// Creating post cookies table.
		$table_name = $wpdb->prefix . $this->post_cookies_table;
		$find       = $table_name;
		$like       = $wild . $wpdb->esc_like( $find ) . $wild;
		$result     = $wpdb->get_results( $wpdb->prepare( 'SHOW TABLES LIKE %s', array( $like ) ), ARRAY_N ); // db call ok; no-cache ok.
		if ( ! $result ) {
			$create_table_sql = "CREATE TABLE `$table_name`(
			    `id_gdpr_cookie_post_cookies` INT NOT NULL AUTO_INCREMENT,
			    `name` VARCHAR(255) NOT NULL,
			    `domain` VARCHAR(255) NOT NULL,
			    `duration` VARCHAR(255) NOT NULL,
			    `type` VARCHAR(255) NOT NULL,
			    `category` VARCHAR(255) NOT NULL,
			    `category_id` INT NOT NULL,
			    `description` TEXT NULL DEFAULT '',
			    PRIMARY KEY(`id_gdpr_cookie_post_cookies`)
			);";
			dbDelta( $create_table_sql );
		}
		// Creating categories table.
		$table_name = $wpdb->prefix . $this->category_table;
		$find       = $table_name;
		$like       = $wild . $wpdb->esc_like( $find ) . $wild;
		$result     = $wpdb->get_results( $wpdb->prepare( 'SHOW TABLES LIKE %s', array( $like ) ), ARRAY_N ); // db call ok; no-cache ok.
		if ( ! $result ) {
			$create_table_sql = "CREATE TABLE `$table_name`(
				 `id_gdpr_cookie_category` INT NOT NULL AUTO_INCREMENT,
				 `gdpr_cookie_category_name` VARCHAR(255) NOT NULL,
				 `gdpr_cookie_category_slug` VARCHAR(255) NOT NULL,
				 `gdpr_cookie_category_description` TEXT  NULL,
				 PRIMARY KEY(`id_gdpr_cookie_category`),
				 UNIQUE `cookie` (`gdpr_cookie_category_name`)
			 );";
			dbDelta( $create_table_sql );
		}
		$this->gdpr_update_category_table();
	}

	/**
	 * Updates category table.
	 *
	 * @since 1.0
	 */
	private function gdpr_update_category_table() {
		global $wpdb;
		$cat_table  = $wpdb->prefix . $this->category_table;
		$categories = $this->gdpr_get_categories();
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


	/**
	 * Returns manually created cookie list from db.
	 *
	 * @since 1.0
	 * @param int $offset Offset.
	 * @param int $limit Limit.
	 *
	 * @return array
	 */
	public function get_post_cookie_list( $offset = 0, $limit = 100 ) {
		global $wpdb;
		$out = array(
			'total' => 0,
			'data'  => array(),
		);

		$count_arr = $wpdb->get_row( 'SELECT COUNT(id_gdpr_cookie_post_cookies) AS ttnum FROM ' . $wpdb->prefix . 'gdpr_cookie_post_cookies', ARRAY_A ); // db call ok; no-cache ok.
		if ( $count_arr ) {
			$out['total'] = $count_arr['ttnum'];
		}

		$data_arr = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'gdpr_cookie_post_cookies ORDER BY id_gdpr_cookie_post_cookies DESC LIMIT %d, %d', array( $offset, $limit ) ), ARRAY_A ); // db call ok; no-cache ok.
		if ( $data_arr ) {
			$out['data'] = $data_arr;
		}
		return $out;
	}

	/**
	 * Returns scanned and custom cookies removing duplicate entries.
	 *
	 * @since 1.0
	 * @return array
	 */
	public function get_cookies() {
		$cookies_array = array();
		$post_cookies  = $this->get_post_cookie_list();
		$post_cookies  = array_reverse( $post_cookies['data'] );
		foreach ( $post_cookies as $key => $post_arr ) {
			$num_days = $post_arr['duration'];
			if ( is_numeric( $num_days ) ) {
				if ( '1' === $num_days ) {
					$num_days .= ' day';
				} elseif ( $num_days < 365 ) {
					if ( $num_days >= 30 ) {
						$num_days = round( $num_days / 30 );
						if ( $num_days > 1 ) {
							$num_days .= ' months';
						} else {
							$num_days .= ' month';
						}
					} else {
						$num_days .= ' days';
					}
				} elseif ( $num_days >= 365 ) {
					$num_days = round( $num_days / 365 );
					if ( $num_days > 1 ) {
						$num_days .= ' years';
					} else {
						$num_days .= ' year';
					}
				}
				$post_arr['duration'] = $num_days;
				$post_cookies[ $key ] = $post_arr;
			}
		}
		$cookies_array = array_merge( $post_cookies );
		$temp_arr      = array_unique( array_column( $cookies_array, 'name' ) );
		$cookies_array = array_intersect_key( $cookies_array, $temp_arr );
		return $cookies_array;
	}

}
new Gdpr_Cookie_Consent_Cookie_Custom();
