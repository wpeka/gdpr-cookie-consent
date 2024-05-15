<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://club.wpeka.com
 * @since      1.0
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The name of the database table storing GDPR cookie scan categories.
	 *
	 * @var string
	 */
	public $category_table = 'gdpr_cookie_scan_categories';

	/**
	 * Supported languages.
	 *
	 * @var array
	 */
	private $supported_languages = array( 'fr', 'en', 'nl', 'bg', 'cs', 'da', 'de', 'es', 'hr', 'is', 'sl', 'gr', 'hu', 'po' );

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Admin module list, Module folder and main file must be same as that of module name.
	 * Please check the `admin_modules` method for more details.
	 *
	 * @since 1.0
	 * @access private
	 * @var array $modules Admin module list.
	 */
	private $modules = array(
		'cookie-custom',
		'policy-data',
	);

	/**
	 * Existing modules array.
	 *
	 * @since 1.0
	 * @access public
	 * @var array $existing_modules Existing modules array.
	 */
	public static $existing_modules = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		if ( ! get_option( 'wpl_pro_active' ) ) {
			add_action( 'add_consent_log_content', array( $this, 'wpl_consent_log_overview' ) );
		}
		$pro_is_activated = get_option( 'wpl_pro_active', false );

		if ( ! $pro_is_activated ) {
			if ( ! shortcode_exists( 'wpl_data_request' ) ) {
				add_shortcode( 'wpl_data_request', array( $this, 'wpl_data_reqs_shortcode' ) );         // a shortcode [wpl_data_request].
			}

			if ( class_exists( 'Gdpr_Cookie_Consent' ) ) {
				$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
			}

			add_action( 'admin_init', array( $this, 'wpl_data_req_process_resolve' ) );
			add_action( 'admin_init', array( $this, 'wpl_data_req_process_delete' ) );
			add_action( 'add_data_request_content', array( $this, 'wpl_data_requests_overview' ) );
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0
	 */
	public function enqueue_styles() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Gdpr_Cookie_Consent_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gdpr_Cookie_Consent_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( 'wp-color-picker' );
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gdpr-cookie-consent-admin' . GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name . '-dashboard', plugin_dir_url( __FILE__ ) . 'css/gdpr-cookie-consent-dashboard' . GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );
		// wizard style.
		wp_register_style( $this->plugin_name . '-wizard', plugin_dir_url( __FILE__ ) . 'css/gdpr-cookie-consent-wizard' . GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'css/select2.css', array(), $this->version, 'all' );
		wp_register_style( 'gdpr_policy_data_tab_style', plugin_dir_url( __FILE__ ) . 'css/gdpr-policy-data-tab' . GDPR_CC_SUFFIX . '.css', array( 'dashicons' ), $this->version, 'all' );
		wp_register_style( $this->plugin_name . '-integrations', plugin_dir_url( __FILE__ ) . 'css/wpl-cookie-consent-integrations.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0
	 */
	public function enqueue_scripts() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Gdpr_Cookie_Consent_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Gdpr_Cookie_Consent_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_media();
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gdpr-cookie-consent-admin' . GDPR_CC_SUFFIX . '.js', array( 'jquery', 'wp-color-picker', 'gdprcookieconsent_cookie_custom' ), $this->version, false );
		wp_register_script( $this->plugin_name . '-vue', plugin_dir_url( __FILE__ ) . 'js/vue/vue.min.js', array(), $this->version, false );
		wp_register_script( $this->plugin_name . '-mascot', plugin_dir_url( __FILE__ ) . 'js/vue/gdpr-cookie-consent-mascot.js', array( 'jquery' ), $this->version, false );
		wp_register_script( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'js/select2.js', array( 'jquery' ), $this->version, false );
		wp_register_script( $this->plugin_name . '-main', plugin_dir_url( __FILE__ ) . 'js/vue/gdpr-cookie-consent-admin-main.js', array( 'jquery' ), $this->version, false );
		wp_register_script( $this->plugin_name . '-dashboard', plugin_dir_url( __FILE__ ) . 'js/vue/gdpr-cookie-consent-admin-dashboard.js', array( 'jquery' ), $this->version, false );
		wp_register_script( $this->plugin_name . '-integrations', plugin_dir_url( __FILE__ ) . 'js/vue/wpl-cookie-consent-admin-integrations.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Filter callback to return if maxmind is integrated
	 *
	 * @param String $maxmind_integrated Filter variable.
	 *
	 * @since 3.0.2
	 */
	public function wpl_get_maxmind_integrated( $maxmind_integrated ) {
		return get_option( 'wpl_pro_maxmind_integrated' );
	}

	/**
	 * Ajax callback function for Integrations Page.
	 */
	public function wpl_cookie_consent_integrations_settings() {
		if ( isset( $_POST['_wpnonce'] ) ) {
			$geoip       = new Gdpr_Cookie_Consent_Geo_Ip();
			$license_key = isset( $_POST['wpl-maxmind-license-key'] ) ? sanitize_text_field( wp_unslash( $_POST['wpl-maxmind-license-key'] ) ) : '';
			$license_key = is_null( $license_key ) ? '' : $license_key;
			$license_key = trim( stripslashes( $license_key ) );
			if ( ! empty( $license_key ) ) {
				$license_key = $geoip->validate_maxmind_license_key( $license_key );
			}
			$enable_geotargeting = isset( $_POST['wpl-enable-geo-targeting'] ) ?
    ( true === sanitize_text_field( wp_unslash( $_POST['wpl-enable-geo-targeting'] ) ) || 'true' === sanitize_text_field( wp_unslash( $_POST['wpl-enable-geo-targeting'] ) ) ) ? 'true' : 'false' : 'false';
			$geo_options                        = get_option( 'wpl_geo_options' );
			$geo_options['maxmind_license_key'] = $license_key;
			$geo_options['enable_geotargeting'] = $enable_geotargeting;
			update_option( 'wpl_geo_options', $geo_options );
			if ( '2' === get_option( 'wpl_pro_maxmind_integrated' ) ) {
				wp_send_json_success();
			}
		}
	}
	/**
	 * Ajax callback function for Deactivation Popup.
	 *
	 * @since 3.1.0
	 *
	 * 2 Ajax function callback method.
	 */
	public static function gdpr_cookie_consent_deactivate_popup() {
		// Verify AJAX nonce.
		check_ajax_referer( 'gdpr-cookie-consent', '_ajax_nonce' );

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( 'Permission denied' );
		}
		if ( ! empty( $_POST ) && isset( $_POST['reason'] ) ) {
			$reason = $_POST['reason'];

			if ( $reason === 'gdpr-plugin-deactivate-with-data' ) {
				delete_option( 'gdpr_admin_modules' );
				delete_option( 'gdpr_public_modules' );
				delete_option( 'wpl_pro_maxmind_integrated' );
				delete_option( 'gdpr_version_number' );
				delete_option( '	analytics_activation_redirect_gdpr-cookie-consent' );
				delete_option( 'wpl_logs_admin' );
				delete_option( 'wpl_datarequests_db_version' );
				delete_option( 'wpl_cl_decline' );
				delete_option( 'wpl_cl_accept' );
				delete_option( 'wpl_cl_partially_accept' );
				delete_option( 'wpl_geo_options' );
				delete_option( 'wpl_bypass_script_blocker' );
				delete_option( 'wpl_consent_timestamp' );
				delete_option( 'GDPR_COOKIE_CONSENT_SETTINGS_FIELD' );
				global $wpdb;
				$tables_arr = array(
					'wpl_cookie_scan',
					'wpl_cookie_scan_url',
					'wpl_cookie_scan_cookies',
					'wpl_cookie_scripts',
					'wpl_data_req',
					'gdpr_cookie_post_cookies',
					'gdpr_cookie_scan_categories',
				);

				foreach ( $tables_arr as $table ) {
					$tablename = $wpdb->prefix . $table;
					$wpdb->query( "DROP TABLE IF EXISTS $tablename" ); // SQL query included to drop tables
				}

				$option_name = 'GDPRCookieConsent-9.0';
				$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name = %s", $option_name ) ); // SQL query included to delete option

				// Deactivating the gdpr-cookie-consent plugin.
				deactivate_plugins( 'gdpr-cookie-consent/gdpr-cookie-consent.php' );
			} elseif ( $reason === 'gdpr-plugin-deactivate-without-data' ) {
				// Code to execute if 'reason' is 'gdpr-plugin-deactivate-without-data'.
				deactivate_plugins( 'gdpr-cookie-consent/gdpr-cookie-consent.php' );
				wp_send_json_success();

			}
			wp_send_json_success();
		}
	}
	/**
	 * Displays admin notices related to GDPR Cookie Consent plugin.
	 *
	 * This function is responsible for displaying admin notices based on the
	 * connection status of the user to the GDPR Cookie Consent plugin.
	 *
	 * @since 3.0.0
	 */
	public function gdpr_admin_notices() {

		$installed_plugins = get_plugins();
		$pro_installed     = isset( $installed_plugins['wpl-cookie-consent/wpl-cookie-consent.php'] ) ? true : false;

		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';

		// Instantiate a new object of the GDPR_Cookie_Consent_Settings class.
		$this->settings = new GDPR_Cookie_Consent_Settings();

		// Call the is_connected() method from the instantiated object to check if the user is connected.
		$is_user_connected = $this->settings->is_connected();

		if ( ! $pro_installed ) {
			if ( isset( $_GET['page'] ) && $_GET['page'] === 'gdpr-cookie-consent' && $is_user_connected ) {
				// Display successfull connection notice.
				echo '<div id="gdpr-wpcc-notice" class="notice notice-success is-dismissible wpcc-notice gdpr-hidden"><p>Successful Connection - You get full control of your website\'s cookie compliance with comprehensive settings and features, including a built-in Cookie scanner, advanced dashboard, and Geo-targeting capabilities.
				</p></div>';
			} elseif ( isset( $_GET['page'] ) && $_GET['page'] === 'gdpr-cookie-consent' && ! $is_user_connected ) {
				// Display  disconnection notice.
				echo '<div id="gdpr-disconnect-wpcc-notice" class="notice notice-warning is-dismissible wpcc-notice gdpr-hidden"><p>Your website has been disconnected from WP Cookie Consent. Please <span class="api-connect-to-account-btn">click here</span> to connect again and unlock advanced features.
				</p></div>';
			}
		}
	}


	/**
	 * Print admin notices for Maxmind integration.
	 */
	public function wpl_admin_notices() {
		if ( class_exists( 'Gdpr_Cookie_Consent' ) ) {
			$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
			$style       = '';
			if ( ! $the_options['is_eu_on'] && ! $the_options['is_ccpa_on'] ) {
				$style = 'display:none';
			}
			$geo_options = get_option( 'wpl_geo_options' );
			if ( '2' !== get_option( 'wpl_pro_maxmind_integrated' ) && ( ! isset( $geo_options['enable_geotargeting'] ) || 'true' !== $geo_options['enable_geotargeting'] ) ) {
				?>
				<div class="gdpr-maxmind-notice notice notice-error dismissible" style="<?php echo esc_attr( $style ); ?>">
					<p>
						<strong><?php esc_html_e( 'WP Cookie Consent Pro: Geotargeting not enabled and MaxMind integration has not been configured.', 'gdpr-cookie-consent' ); ?></strong>
					</p>
					<p>
						<?php
						echo wp_kses_post(
							sprintf(
								/* translators: %1%s: integration page */
								__( 'You must enable geotargeting and enter a valid license key on the <a href="%1$s">MaxMind integration page</a> in order to use the geolocation services.', 'gdpr-cookie-consent' ),
								admin_url( 'admin.php?page=gdpr-cookie-consent#cookie_settings#integrations' )
							)
						);
						?>
					</p>
				</div>
				<?php
			} elseif ( '2' !== get_option( 'wpl_pro_maxmind_integrated' ) ) {
				?>
				<div class="gdpr-maxmind-notice notice notice-error dismissible" style="<?php echo esc_attr( $style ); ?>">
					<p>
						<strong><?php esc_html_e( 'WP Cookie Consent Pro: MaxMind integration has not been configured.', 'gdpr-cookie-consent' ); ?></strong>
					</p>
					<p>
						<?php
						echo wp_kses_post(
							sprintf(
								/* translators: %1%s: integration page */
								__( 'You must enter a valid license key on the <a href="%1$s">MaxMind integration page</a> in order to use the geolocation services.', 'gdpr-cookie-consent' ),
								admin_url( 'admin.php?page=gdpr-cookie-consent#cookie_settings#integrations' )
							)
						);
						?>
					</p>
				</div>
				<?php
			} elseif ( ! isset( $geo_options['enable_geotargeting'] ) || 'true' !== $geo_options['enable_geotargeting'] ) {
				?>
				<div class="gdpr-maxmind-notice notice notice-error dismissible" style="<?php echo esc_attr( $style ); ?>">
					<p>
						<strong><?php esc_html_e( 'WP Cookie Consent Pro: Geotargeting is not enabled.', 'gdpr-cookie-consent' ); ?></strong>
					</p>
					<p>
						<?php
						echo wp_kses_post(
							sprintf(
								/* translators: %1%s: integration page */
								__( 'You must enable geotargeting on the <a href="%1$s">MaxMind integration page</a> in order to use the geolocation services.', 'gdpr-cookie-consent' ),
								admin_url( 'admin.php?page=gdpr-cookie-consent#cookie_settings#integrations' )
							)
						);
						?>
					</p>
				</div>
				<?php
			}
		}
	}
	/**
	 * Consent Log overview
	 *
	 * @return void
	 */
	public function wpl_consent_log_overview() {
		ob_start();
		include GDPR_COOKIE_CONSENT_PLUGIN_PATH . '/public/modules/consent-logs/class-wpl-consent-logs.php';
		// Style for consent log report.
		wp_register_style( 'wplcookieconsent_data_reqs_style', plugin_dir_url( __FILE__ ) . 'data-req/data-request-style' . GDPR_CC_SUFFIX . '.css', array( 'dashicons' ), $this->version, 'all' );
		wp_enqueue_style( 'wplcookieconsent_data_reqs_style' );

		$consent_logs = new WPL_Consent_Logs();
		$consent_logs->prepare_items();
		?>
		<div class="wpl-consentlogs">
			<h1 class="wp-heading-inline"><?php _e( 'Consent Logs', 'gdpr-cookie-consent' ); ?>

			</h1>
			<form id="wpl-dnsmpd-filter-consent-log" method="get" action="<?php echo admin_url( 'admin.php?page=gdpr-cookie-consent#consent_logs' ); ?>">
			<?php
				$consent_logs->search_box( __( 'Search Logs', 'gdpr-cookie-consent' ), 'gdpr-cookie-consent' );
				$consent_logs->display();
			?>
				<input type="hidden" name="page" value="gdpr-cookie-consent"/>

			</form>
			<script>
					document.addEventListener('DOMContentLoaded', function() {

				jQuery('#wpl-dnsmpd-filter-consent-log input[id="doaction"]').attr('id', 'consentLogApplyButton');
				jQuery('#wpl-dnsmpd-filter-consent-log input[id="doaction2"]').attr('id', 'consentLogApplyButton2');
				jQuery('#wpl-dnsmpd-filter-consent-log select[id="bulk-action-selector-bottom"]').attr('id', 'bulk-action-selector-consent-log-bottom');
				jQuery('#wpl-dnsmpd-filter-consent-log select[id="bulk-action-selector-top"]').attr('id', 'bulk-action-selector-consent-log-top');

	});
</script>
		</div>
			<?php

			$content = ob_get_clean();
			$args    = array(
				'page'    => 'do-not-sell-my-personal-information',
				'content' => $content,
			);
			echo $this->wpl_get_consent_template( 'gdpr-consent-logs-tab-template.php', $args );
	}

	/**
	 * Get a template based on filename, overridable in the theme directory.
	 *
	 * @param string $filename The name of the template file.
	 * @param array  $args     An array of arguments to pass to the template.
	 * @param string $path     The path to the template file (optional).
	 * @return string The content of the template.
	 */
	public function wpl_get_consent_template( $filename, $args = array(), $path = false ) {

		$file = GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'admin/partials/gdpr-consent-logs-tab-template.php';

		if ( ! file_exists( $file ) ) {
			return false;
		}

		if ( strpos( $file, '.php' ) !== false ) {
			ob_start();
			require $file;
			$contents = ob_get_clean();
		} else {
			$contents = file_get_contents( $file );
		}

		if ( ! empty( $args ) && is_array( $args ) ) {
			foreach ( $args as $fieldname => $value ) {
				$contents = str_replace( '{' . $fieldname . '}', $value, $contents );
			}
		}

		return $contents;
	}
	/**
	 * Register admin modules
	 *
	 * @since 1.0
	 */
	public function admin_modules() {
		$initialize_flag    = false;
		$active_flag        = false;
		$non_active_flag    = false;
		$gdpr_admin_modules = get_option( 'gdpr_admin_modules' );
		if ( false === $gdpr_admin_modules ) {
			$initialize_flag    = true;
			$gdpr_admin_modules = array();
		}
		foreach ( $this->modules as $module ) {
			$is_active = 1;
			if ( isset( $gdpr_admin_modules[ $module ] ) ) {
				$is_active = $gdpr_admin_modules[ $module ]; // checking module status.
				if ( 1 === $is_active ) {
					$active_flag = true;
				}
			} else {
				$active_flag                   = true;
				$gdpr_admin_modules[ $module ] = 1; // default status is active.
			}
			$module_file = plugin_dir_path( __FILE__ ) . "modules/$module/class-gdpr-cookie-consent-$module.php";
			if ( file_exists( $module_file ) && 1 === $is_active ) {
				self::$existing_modules[] = $module; // this is for module_exits checking.
				require_once $module_file;
			} else {
				$non_active_flag               = true;
				$gdpr_admin_modules[ $module ] = 0;
			}
		}
		if ( $initialize_flag || ( $active_flag && $non_active_flag ) ) {
			$out = array();
			foreach ( $gdpr_admin_modules as $k => $m ) {
				if ( in_array( $k, $this->modules, true ) ) {
					$out[ $k ] = $m;
				}
			}
			update_option( 'gdpr_admin_modules', $out );
		}
	}

	/**
	 * Adds help tabs in admin screens.
	 *
	 * @since 1.0
	 */
	public function add_tabs() {
		$screen = get_current_screen();
		if ( ! $screen || 'toplevel_page_gdpr-cookie-consent' !== $screen->id ) {
			return;
		}
		$gdpr_shortcode_content = '<h2>' . __( 'Cookie Bar Shortcodes', 'gdpr-cookie-consent' ) . '</h2>' .
									'<p>' . __( 'Use the below shortcode to display third-party cookie details on your privacy or cookie policy pages.', 'gdpr-cookie-consent' ) . '</p>' .
									'<div style="font-weight: bold;">[wpl_cookie_details]</div>';
		$screen->add_help_tab(
			array(
				'id'      => 'gdprcookieconsent_shortcodes',
				'title'   => __( 'Cookie Bar Shortcodes', 'gdpr-cookie-consent' ),
				'content' => $gdpr_shortcode_content,
			)
		);
		$screen->add_help_tab(
			array(
				'id'      => 'gdprcookieconsent_support_tab',
				'title'   => __( 'Help &amp; Support', 'gdpr-cookie-consent' ),
				'content' => '<h2>' . __( 'Help &amp; Support', 'gdpr-cookie-consent' ) . '</h2>' .
								'<p>' . __( 'If you need help understanding, using, or extending WP Cookie Consent Plugin,', 'gdpr-cookie-consent' ) . ' <a href="https://club.wpeka.com/docs/wp-cookie-consent/" target="_blank">' . __( 'please read our documentation.', 'gdpr-cookie-consent' ) . '</a> ' . __( 'You will find all kinds of resources including snippets, tutorials and more.', 'gdpr-cookie-consent' ) . '</p>' .
								'<p>' . __( 'For further assistance with WP Cookie Consent plugin you can use the', 'gdpr-cookie-consent' ) . ' <a href="https://wordpress.org/support/plugin/gdpr-cookie-consent" target="_blank">' . __( 'community forum.', 'gdpr-cookie-consent' ) . '</a> ' . __( 'If you need help with premium extensions sold by WPEka', 'gdpr-cookie-consent' ) . ' <a href="https://wpeka.freshdesk.com/" target="_blank">' . __( 'use our helpdesk.', 'gdpr-cookie-consent' ) . '</a></p>',
			)
		);
		$screen->add_help_tab(
			array(
				'id'      => 'gdprcookieconsent_bugs_tab',
				'title'   => __( 'Found a bug?', 'gdpr-cookie-consent' ),
				'content' => '<h2>' . __( 'Found a bug?', 'gdpr-cookie-consent' ) . '</h2>' .
								'<p>' . __( 'If you find a bug within WP Cookie Consent plugin you can create a ticket via', 'gdpr-cookie-consent' ) . ' <a href="https://wpeka.freshdesk.com/" target="_blank">' . __( 'our helpdesk.', 'gdpr-cookie-consent' ) . '</a></p>',
			)
		);
		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'gdpr-cookie-consent' ) . '</strong></p>' .
					'<p><a href="https://club.wpeka.com/product/wp-gdpr-cookie-consent/?utm_source=plugin&utm_medium=gdpr&utm_campaign=top-help-bar&utm_content=about-gdpr" target="_blank">' . __( 'About WP Cookie Consent', 'gdpr-cookie-consent' ) . '</a></p>' .
					'<p><a href="https://wordpress.org/support/plugin/gdpr-cookie-consent/" target="_blank">' . __( 'WordPress.org project', 'gdpr-cookie-consent' ) . '</a></p>' .
					'<p><a href="https://club.wpeka.com/category/plugins/?orderby=popularity/?utm_source=plugin&utm_medium=gdpr&utm_campaign=top-help-bar&utm_content=wpeka-plugins" target="_blank">' . __( 'WPEka Plugins', 'gdpr-cookie-consent' ) . '</a></p>'
		);
	}

	/**
	 * DATA Reqs Shortcode callback function
	 *
	 * @return string|void
	 */
	public function wpl_data_reqs_shortcode() {
		wp_register_script( 'wplcookieconsent_data_reqs', plugin_dir_url( __FILE__ ) . 'data-req/data-request' . GDPR_CC_SUFFIX . '.js#async', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'wplcookieconsent_data_reqs' );
		wp_localize_script(
			'wplcookieconsent_data_reqs',
			'data_req_obj',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
		);
		ob_start();
		?>
			<div class="wpl-datarequest wpl-alert">
				<span class="wpl-close">&times;</span>
				<span id="wpl-message"></span>
			</div>
			<form id="wpl-datarequest-form">
				<input type="hidden" name="wpl_data_req_form_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpl-data-req-form-nonce' ) ); ?>"/>
				<label for="wpl_datarequest_firstname" class="wpl-first-name"><?php echo __( 'Name', 'gdpr-cookie-consent' ); ?>
					<input type="search" class="datarequest-firstname" value="" placeholder="your first name" id="wpl_datarequest_firstname" name="wpl_datarequest_firstname" >
				</label>
				<div>
					<label for="wpl_datarequest_name"><?php echo __( 'Name', 'gdpr-cookie-consent' ); ?></label>
					<input type="text" required value="" placeholder="<?php echo __( 'Your name', 'gdpr-cookie-consent' ); ?>" id="wpl_datarequest_name" name="wpl_datarequest_name">
				</div>
				<div>
					<label for="wpl_datarequest_email"><?php echo __( 'Email', 'gdpr-cookie-consent' ); ?></label>
					<input type="email" required value="" placeholder="<?php echo __( 'email@email.com', 'gdpr-cookie-consent' ); ?>" id="wpl_datarequest_email" name="wpl_datarequest_email">
				</div>
				<?php
					$options = $this->wpl_data_reqs_options();
				foreach ( $options as $id => $label ) {
					?>
						<div class="wpl_datarequest wpl_datarequest_<?php echo esc_attr( $id ); ?>">
							<label for="wpl_datarequest_<?php echo esc_attr( $id ); ?>">
								<input type="checkbox" value="1" name="wpl_datarequest_<?php echo esc_attr( $id ); ?>" id="wpl_datarequest_<?php echo esc_attr( $id ); ?>"/>
								<?php echo esc_html( $label['long'] ); ?>
							</label>
						</div>
				<?php } ?>
				<input type="button" id="wpl-datarequest-submit"  value="<?php echo __( 'Send', 'gdpr-cookie-consent' ); ?>">
			</form>
			<style>
				/* first-name is honeypot */
				.wpl-first-name {
					position: absolute !important;
					left: -5000px !important;
				}
				.wpl-alert {
					display: none;
					padding: 7px;
					color: white;
					margin: 10px 0;
				}
				.wpl-alert.wpl-error {
					background-color: #f44336;
				}
				.wpl-alert.wpl-success {
					background-color: green;
				}
			</style>
		<?php
		return ob_get_clean();
	}


	/**
	 * Get DATA Reqs data requirements options.
	 *
	 * @param array $options An array of options for configuring DATA Reqs data requirements.
	 *
	 * @return array The configured data requirements options.
	 */
	public static function wpl_data_reqs_options( $options = array() ) {
		$options = $options + array(
			'request_for_access'        => array(
				'short' => __( 'Request for access', 'gdpr-cookie-consent' ),
				'long'  => __( 'Submit a request for access to the data we process about you.', 'gdpr-cookie-consent' ),
				'slug'  => 'docs/wp-cookie-consent/how-to-guides/what-is-the-right-to-access/',
			),
			'right_to_be_forgotten'     => array(
				'short' => __( 'Right to be forgotten', 'gdpr-cookie-consent' ),
				'long'  => __( 'Submit a request for deletion of the data if it is no longer relevant.', 'gdpr-cookie-consent' ),
				'slug'  => 'docs/wp-cookie-consent/how-to-guides/right-to-be-forgotten/',
			),
			'right_to_data_portability' => array(
				'short' => __( 'Right to data portability', 'gdpr-cookie-consent' ),
				'long'  => __( 'Submit a request to receive an export file of the data we process about you.', 'gdpr-cookie-consent' ),
				'slug'  => 'docs/wp-cookie-consent/how-to-guides/right-to-data-portability/',
			),
		);
		return $options;
	}
	/**
	 * Process the form submit
	 *
	 * @return void
	 */
	public function wpl_data_reqs_handle_form_submit() {
			// Get the form data from the post.
		if ( isset( $_POST['form_data'] ) && ! empty( $_POST['form_data'] ) ) {
			$form_data = $_POST['form_data'];
		}
			$new_request = false;
			$error       = false;
			$message     = '';
			// Initialize an empty array.
			$decoded_data = array();
			// Parse the serialized form data into an associative array.
			parse_str( $form_data, $decoded_data );
			// Now, $decoded_data contains the form data as an array.
			// Submit form nonce verification.
		if ( isset( $_POST['form_data'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $decoded_data['wpl_data_req_form_nonce'] ) ), 'wpl-data-req-form-nonce' ) ) {
				return;
			}
		}
			$params = $decoded_data;
			// check honeypot.
		if ( isset( $params['wpl_datarequest_firstname'] ) && ! empty( $params['wpl_datarequest_firstname'] ) ) {
			$error   = true;
			$message = __( "Sorry, it looks like you're a bot", 'gdpr-cookie-consent' );
		}
		if ( ! isset( $params['wpl_datarequest_email'] ) || ! is_email( $params['wpl_datarequest_email'] ) ) {
			$error   = true;
			$message = __( 'Please enter a valid email address.', 'gdpr-cookie-consent' );
		}
		if ( ! isset( $params['wpl_datarequest_name'] ) || empty( $params['wpl_datarequest_name'] ) ) {
			$error   = true;
			$message = __( 'Please enter your name', 'gdpr-cookie-consent' );
		}
		if ( strlen( $params['wpl_datarequest_name'] ) > 100 ) {
			$error   = true;
			$message = __( "That's a long name you got there. Please try to shorten the name.", 'gdpr-cookie-consent' );
		}
		if ( ! $error ) {
			$email = sanitize_email( $params['wpl_datarequest_email'] );
			$name  = sanitize_text_field( $params['wpl_datarequest_name'] );
			// check if this email address is already registered:.
			global $wpdb;
			$options = $this->wpl_data_reqs_options();
			foreach ( $options as $fieldname => $label ) {
				$value = isset( $params[ 'wpl_datarequest_' . $fieldname ] ) ? intval( $params[ 'wpl_datarequest_' . $fieldname ] ) : false;
				if ( $value === 1 ) {
					$count = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) from {$wpdb->prefix}wpl_data_req WHERE email = %s and $fieldname=1", $email ) );
					if ( $count == 0 ) {
						$new_request = true;
						$wpdb->insert(
							$wpdb->prefix . 'wpl_data_req',
							array(
								'name'         => $name,
								'email'        => $email,
								$fieldname     => $value,
								'request_date' => time(),
							)
						);
					}
				}
			}
			// sending mail.
			if ( $new_request ) {
				$this->wpl_send_confirmation_mail( $email, $name );
				$this->wpl_send_notification_mail();
				$message = __( 'Your request has been processed successfully!', 'gdpr-cookie-consent' );
			} else {
				$message = __( 'Your request could not be processed. A request is already in progress for this email address or the form is not complete.', 'gdpr-cookie-consent' );
			}
		}
			// response for ajax.
			$response = array(
				'message' => $message,
				'success' => ! $error,
			);
			wp_send_json( $response );
	}
	/**
	 * Send confirmation mail to the specified email address.
	 *
	 * @param string $email The email address to send the confirmation mail to.
	 * @param string $name  The name associated with the email address.
	 *
	 * @return void
	 */
	private function wpl_send_confirmation_mail( $email, $name ) {
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		$message     = $the_options['data_req_editor_message'];
		$message     = html_entity_decode( $message );
		$message     = stripslashes( $message );
		$subject     = $the_options['data_req_subject'];
		$message     = str_replace( '{name}', $name, $message );
		$message     = str_replace( '{blogname}', get_bloginfo( 'name' ), $message );
		$this->wpl_send_mail( $email, $subject, $message );
	}
	/**
	 * Send confirmation mail
	 *
	 * @return void
	 */
	private function wpl_send_notification_mail() {

		$email   = sanitize_email( get_option( 'admin_email' ) );
		$subject = 'You have received a new data request on ' . get_bloginfo( 'name' );
		$message = $subject . '<br />' . 'Please check the data request on ' . '<a href="' . site_url() . '" target="_blank">' . site_url() . '</a>';
		$this->wpl_send_mail( $email, $subject, $message );
	}
	/**
	 * Send an email.
	 *
	 * @param string $email    The recipient's email address.
	 * @param string $subject  The subject of the email.
	 * @param string $message  The content of the email.
	 *
	 * @return bool True if the email was sent successfully, false otherwise.
	 */
	private function wpl_send_mail( $email, $subject, $message ) {
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		$headers     = array();
		$from_name   = get_bloginfo( 'name' );
		$from_email  = $the_options['data_req_email_address'];
		add_filter(
			'wp_mail_content_type',
			function ( $content_type ) {
				return 'text/html';
			}
		);
		if ( ! empty( $from_email ) ) {
			$headers[] = 'From: ' . $from_name . ' <' . $from_email . '>'
							. "\r\n";
		}
		$success = true;
		if ( wp_mail( $email, $subject, $message, $headers ) === false ) {
			$success = false;
		}
		// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578.
		remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
		return $success;
	}
	/**
	 * Extend the table to include pro data request options
	 *
	 * @return void
	 */
	public function update_db_check() {
		if ( get_option( 'wpl_datarequests_db_version' ) != GDPR_COOKIE_CONSENT_VERSION ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();
			$table_name      = $wpdb->prefix . 'wpl_data_req';
			$sql             = "CREATE TABLE $table_name (
			  `ID` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) NOT NULL,
			  `email` varchar(255) NOT NULL,
			  `request_date` int(11) NOT NULL,
			  `resolved` int(11) NOT NULL,
			  `request_for_access` int(11) NOT NULL,
			  `right_to_be_forgotten` int(11) NOT NULL,
			  `right_to_data_portability` int(11) NOT NULL,
			  PRIMARY KEY  (ID)
			) $charset_collate;";
			dbDelta( $sql );
			update_option( 'wpl_datarequests_db_version', GDPR_COOKIE_CONSENT_VERSION );
		}
	}

	/**
	 * Removed users overview
	 *
	 * @return void
	 */
	public function wpl_data_requests_overview() {
		ob_start();
		include __DIR__ . '/data-req/class-wpl-data-req-table.php';
		// Style for data request report.
		wp_register_style( 'wplcookieconsent_data_reqs_style', plugin_dir_url( __FILE__ ) . 'data-req/data-request-style' . GDPR_CC_SUFFIX . '.css', array( 'dashicons' ), $this->version, 'all' );
		wp_enqueue_style( 'wplcookieconsent_data_reqs_style' );

		$datarequests = new WPL_Data_Req_Table();
		$datarequests->prepare_items();
		?>
		<div class="wpl-datarequests">
			<h1 class="wp-heading-inline"><?php _e( 'Data Requests', 'gdpr-cookie-consent' ); ?>

			</h1>
			<form id="wpl-dnsmpd-filter-datarequest" method="get" action="<?php echo admin_url( 'admin.php?page=gdpr-cookie-consent#data_request' ); ?>">
			<?php
				$datarequests->search_box( __( 'Search Requests', 'gdpr-cookie-consent' ), 'gdpr-cookie-consent' );
				$datarequests->display();
			?>
				<input type="hidden" name="page" value="gdpr-cookie-consent"/>
			</form>
		</div>
			<?php

			$content = ob_get_clean();
			$args    = array(
				'page'    => 'do-not-sell-my-personal-information',
				'content' => $content,
			);
			echo $this->wpl_get_template_data_request( 'gdpr-data-request-tab-template.php', $args );
	}

	/**
	 * Handle  resolve request
	 */
	public function wpl_data_req_process_resolve() {

		if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'gdpr-cookie-consent' )
		&& isset( $_GET['action'] )
		&& $_GET['action'] == 'resolve'
		&& isset( $_GET['id'] )
		) {
			global $wpdb;
			$wpdb->update(
				$wpdb->prefix . 'wpl_data_req',
				array(
					'resolved' => 1,
				),
				array( 'ID' => intval( $_GET['id'] ) )
			);
			$paged = isset( $_GET['paged'] ) ? 'paged=' . intval( $_GET['paged'] ) : '';
			wp_redirect( admin_url( 'admin.php?page=gdpr-cookie-consent#data_request' . $paged ) );
			exit;
		}
	}
	/**
	 * Handle delete request.
	 */
	public function wpl_data_req_process_delete() {
		if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'gdpr-cookie-consent' )
			&& isset( $_GET['action'] )
			&& $_GET['action'] == 'delete'
			&& isset( $_GET['id'] )
		) {
			global $wpdb;
			$wpdb->delete( $wpdb->prefix . 'wpl_data_req', array( 'ID' => intval( $_GET['id'] ) ) );
			$paged = isset( $_GET['paged'] ) ? 'paged=' . intval( $_GET['paged'] ) : '';
			wp_redirect( admin_url( 'admin.php?page=gdpr-cookie-consent#data_request' . $paged ) );
		}
	}


	/**
	 * Modify admin footer text.
	 *
	 * @param string $footer Footer text.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function admin_footer_text( $footer ) {
		$screen = get_current_screen();
		if ( ! $screen || 'toplevel_page_gdpr-cookie-consent' !== $screen->id ) {
			return $footer;
		}
		$footer = sprintf(
			/* translators: 1: GDPR Cookie Consent 2:: five stars */
			__( 'If you like %1$s please leave us a %2$s rating. A huge thanks in advance!', 'gdpr-cookie-consent' ),
			sprintf( '<strong>%s</strong>', esc_html__( 'WP Cookie Consent', 'gdpr-cookie-consent' ) ),
			'<a href="https://wordpress.org/support/plugin/gdpr-cookie-consent/reviews?rate=5#new-post" target="_blank" aria-label="' . esc_attr__( 'five star', 'gdpr-cookie-consent' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
		);
		return $footer;
	}

	/**
	 * Registers menu options, hooked into admin_menu.
	 *
	 * @since 1.0
	 */
	public function admin_menu() {
		add_menu_page( 'WP Cookie Consent', __( 'WP Cookie Consent', 'gdpr-cookie-consent' ), 'manage_options', 'gdpr-cookie-consent', array( $this, 'gdpr_cookie_consent_new_admin_screen' ), GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/wp_cookies_icon_menu.png', 67 );
		// adding a blank submenu so that main menu does not create a copy of own.
		add_submenu_page(
			'gdpr-cookie-consent',
			'',
			'',
			'manage_options',
			'gdpr-cookie-consent',
			array( $this, 'gdpr_cookie_consent_new_admin_screen' )
		);
		add_submenu_page( '', __( 'Import Policies', 'gdpr-cookie-consent' ), __( 'Import Policies', 'gdpr-cookie-consent' ), 'manage_options', 'gdpr-policies-import', array( $this, 'gdpr_policies_import_page' ) );


		// Check if $_GET['scan_url'] is set
		$scan_url_value = isset($_GET['scan_url']) ? $_GET['scan_url'] : '';

		// Check if the key exists in the options table
		if (get_option('gdpr_single_page_scan_url') !== false) {
			// Update the existing option
			update_option('gdpr_single_page_scan_url', $scan_url_value);
		} else {
			// Add a new option
			add_option('gdpr_single_page_scan_url', $scan_url_value);
		}


	}

	/**
	 * Registers menu options, hooked into admin_menu.
	 *
	 * @since 3.2.0
	 */
	public function gdpr_quick_toolbar_menu ( $wp_admin_bar ) {

		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		//cookie banner enable
		$is_banner_active = $the_options['is_on'];
		//script blocker enable
		$is_script_blocker_active = $the_options['is_script_blocker_on'];

		$enabled_label = '<span style="color:#05E900; font-size:13px;">&#11044;</span>';
		$disabled_label = '<span style="color:#E10101; font-size:13px;;">&#11044;</span>';

		// Add parent menu item
		$args = array(
			'id'    => 'gdpr-quick-menu',
			'title' => 'WP Cookie Consent <span class="custom-icon" style="float:right;width:22px !important;height:22px !important;margin: 5px 5px 0 !important;"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.36305 18.2675C7.43268 18.739 8.57557 18.9748 9.79172 18.9748C11.0079 18.9742 12.1508 18.7384 13.2204 18.2675C14.29 17.7965 15.2205 17.1532 16.0117 16.3376C16.8023 15.522 17.4286 14.572 17.8904 13.4877C18.3523 12.4033 18.5832 11.2478 18.5832 10.0211C18.5838 9.85589 18.5803 9.7024 18.5727 9.56058C18.565 9.41875 18.5539 9.26556 18.5392 9.101C17.5728 9.07108 16.844 8.65219 16.3528 7.84433C15.8617 7.03648 15.8066 6.19122 16.1875 5.30856C15.5434 5.53297 14.9172 5.57037 14.3088 5.42077C13.7004 5.27116 13.1764 4.99799 12.7369 4.60124C12.2973 4.20509 11.9676 3.70781 11.7478 3.1094C11.528 2.51099 11.4841 1.87518 11.616 1.20196C11.2789 1.12716 10.9493 1.0748 10.6269 1.04488C10.3046 1.01496 9.98953 1 9.68183 1C8.40707 0.999403 7.23487 1.25732 6.16524 1.77375C5.09561 2.29018 4.17983 2.97087 3.4179 3.81583C2.65597 4.66138 2.06255 5.62273 1.63763 6.69987C1.2127 7.77701 1.00024 8.87659 1.00024 9.99862C1.00083 11.2403 1.23175 12.4072 1.69301 13.4993C2.15427 14.5914 2.78052 15.5414 3.57175 16.3493C4.36299 17.1565 5.29342 17.7959 6.36305 18.2675Z" fill="white"/><ellipse cx="5.10827" cy="6.64684" rx="1.75451" ry="1.79137" fill="#171C1F"/><ellipse cx="7.11088" cy="14.1328" rx="1.40361" ry="1.43309" fill="#171C1F"/><ellipse cx="4.05556" cy="10.8357" rx="0.701803" ry="0.716547" fill="#171C1F"/><circle cx="9.72125" cy="8.8703" r="0.877254" fill="#171C1F"/><ellipse cx="14.9546" cy="10.2109" rx="1.40361" ry="1.43309" fill="#171C1F"/><circle cx="12.5134" cy="14.7998" r="1.31588" fill="#171C1F"/><ellipse cx="9.5458" cy="4.00341" rx="0.701803" ry="0.716547" fill="#171C1F"/></svg></span>',
			'href'  => admin_url( 'admin.php?page=gdpr-cookie-consent' ), // Add your custom URL here
			'meta'  => array(
				'class'  => 'gdpr-quick-menu-item',
				'target' => '' // Add target attribute if needed
			)
		);

		$wp_admin_bar->add_node( $args );

		$args = array(
			'id'     => 'gdpr-quick-menu-item-1',
			'title'  => 'Scan this Page',
			'parent' => 'gdpr-quick-menu',
			'href'   => admin_url( 'admin.php?page=gdpr-cookie-consent&scan_url=' ).get_permalink().'#cookie_settings#cookie_list',
		);

		$wp_admin_bar->add_node( $args );

		$args = array(
			'id'     => 'gdpr-quick-menu-item-2',
			'title'  => 'Settings',
			'parent' => 'gdpr-quick-menu',
			'href'   => admin_url( 'admin.php?page=gdpr-cookie-consent#cookie_settings' ),
		);
		$wp_admin_bar->add_node( $args );

		$wp_admin_bar->add_node( $args );


		$banner_title = 'Cookie Banner : ' . ($is_banner_active ? 'Enabled ' . $enabled_label : 'Disabled ' . $disabled_label);

		$args = array(
			'id'     => 'gdpr-quick-menu-item-3',
			'title'  => $banner_title,
			'parent' => 'gdpr-quick-menu',
			'href'   => '',
		);
		$wp_admin_bar->add_node( $args );


		$script_blocker_title = 'Script Blocker : ' . ($is_script_blocker_active ? 'Enabled ' . $enabled_label : 'Disabled ' . $disabled_label);
		$args = array(
			'id'     => 'gdpr-quick-menu-item-4',
			'title'  => $script_blocker_title,
			'parent' => 'gdpr-quick-menu',
			'href'   => '',
		);
		$wp_admin_bar->add_node( $args );

	}

	/**
	 * Policy data tab overview
	 *
	 * @return void
	 */
	public function gdpr_policy_data_overview() {
			ob_start();

			include GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'admin/modules/policy-data/class-gdpr-policy-data.php';
			// Style for consent log report.
			wp_enqueue_style( 'gdpr_policy_data_tab_style' );

			$policy_data = new GDPR_Policy_Data_Table();
			$policy_data->prepare_items();
		?>
			<div class="wpl-consentlogs">
				<h1 class="wp-heading-inline"><?php _e( 'Policy Data', 'gdpr-cookie-consent' ); ?>

				</h1>
				<form id="wpl-dnsmpd-filter" method="get" action="<?php echo admin_url( 'admin.php?page=gdpr-cookie-consent#policy_data' ); ?>">
				<?php
					$policy_data->search_box( __( 'Search Policy Data', 'gdpr-cookie-consent' ), 'gdpr-cookie-consent' );
					$policy_data->display();
				?>
					<input type="hidden" name="page" value="gdpr-cookie-consent"/>
				</form>
			</div>
				<?php

				$content = ob_get_clean();
				$args    = array(
					'page'    => 'policy-data-tab',
					'content' => $content,
				);
				echo $this->wpl_get_template_policy_data( 'gdpr-policy-data-tab-template.php', $args );
	}

    /**
	 * Get a template for data request based on filename, overridable in the theme directory.
	 *
	 * @param string $filename The name of the template file.
	 * @param array  $args     An array of arguments to pass to the template.
	 * @param string $path     The path to the template file (optional).
	 * @return string The content of the template.
	 */
	public function wpl_get_template_data_request($filename, $args = array(), $path = false){
		$file = GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'admin/partials/gdpr-data-request-tab-template.php';

		if ( ! file_exists( $file ) ) {
			return false;
		}

		if ( strpos( $file, '.php' ) !== false ) {
			ob_start();
			require $file;
			$contents = ob_get_clean();
		} else {
			$contents = file_get_contents( $file );
		}

		if ( ! empty( $args ) && is_array( $args ) ) {
			foreach ( $args as $fieldname => $value ) {
				$contents = str_replace( '{' . $fieldname . '}', $value, $contents );
			}
		}

		return $contents;
	}
	/**
	 * Get a template for policy data based on filename, overridable in the theme directory.
	 *
	 * @param string $filename The name of the template file.
	 * @param array  $args     An array of arguments to pass to the template.
	 * @param string $path     The path to the template file (optional).
	 * @return string The content of the template.
	 */
	public function wpl_get_template_policy_data( $filename, $args = array(), $path = false ) {

		$file = GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'admin/partials/gdpr-policy-data-tab-template.php';

		if ( ! file_exists( $file ) ) {
			return false;
		}

		if ( strpos( $file, '.php' ) !== false ) {
			ob_start();
			require $file;
			$contents = ob_get_clean();
		} else {
			$contents = file_get_contents( $file );
		}

		if ( ! empty( $args ) && is_array( $args ) ) {
			foreach ( $args as $fieldname => $value ) {
				$contents = str_replace( '{' . $fieldname . '}', $value, $contents );
			}
		}

		return $contents;
	}

	/**
	 * Handle delete request.
	 */
	public function gdpr_policy_process_delete() {

		// Check if the user is logged in and has the necessary capability
		if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
			// Verify nonce
			if ( isset( $_GET['page'], $_GET['action'], $_GET['id'], $_GET['_wpnonce'] )
			&& $_GET['page'] == 'gdpr-cookie-consent'
			&& $_GET['action'] == 'policy_delete'
			&& wp_verify_nonce( $_GET['_wpnonce'], 'gdpr_policy_delete_nonce_' . $_GET['id'] ) ) { // Verify nonce using the ID appended to the nonce name

			// Delete the post
			wp_delete_post( $_GET['id'], true );

			// Redirect back to the admin page
			$paged = isset( $_GET['paged'] ) ? 'paged=' . intval( $_GET['paged'] ) : '';
			wp_redirect( admin_url( 'admin.php?page=gdpr-cookie-consent#policy_data' . $paged ) );
			exit; // Always exit after a wp_redirect()
		}
		}
	}

	/**
	 * Returns plugin actions links.
	 *
	 * @param array $links Plugin action links.
	 * @return array
	 */
	public function admin_plugin_action_links( $links ) {
		$current_url = get_site_url();
		$current_url = $current_url . '/wp-admin/admin.php?page=gdpr-cookie-consent#create_cookie_banner';

		if ( ! get_option( 'wpl_pro_active' ) ) {
			$links = array_merge(
				array(
					'<a href="' . esc_url( 'https://club.wpeka.com/product/wp-gdpr-cookie-consent/?utm_source=gdpr&utm_medium=plugins&utm_campaign=link&utm_content=upgrade-to-pro' ) . '" target="_blank" rel="noopener noreferrer"><strong style="color: #11967A; display: inline;">' . __( 'Upgrade to Pro', 'gdpr-cookie-consent' ) . '</strong></a>',
				),
				$links
			);
		}
		$links = array_merge(
			array(
				'<a href="' . esc_url( $current_url ) . '" target="_self" rel="noopener noreferrer"><strong style="color: #11967A; display: inline;">' . __( 'Create Cookie Banner', 'gdpr-cookie-consent' ) . '</strong></a>',
			),
			$links
		);
		return $links;
	}

	/**
	 * Migrate previous settings.
	 *
	 * @since 1.7.6
	 */
	public function admin_init() {
		global $wpdb;
		if ( ! get_option( 'wpl_pro_maxmind_integrated' ) ) {
			add_option( 'wpl_pro_maxmind_integrated', '1' );
		}
		if ( ! get_option( 'gdpr_version_number' ) ) {
			update_option( 'gdpr_version_number', GDPR_COOKIE_CONSENT_VERSION );
		} elseif ( get_option( 'gdpr_version_number' ) !== GDPR_COOKIE_CONSENT_VERSION ) {
				update_option( 'gdpr_version_number', GDPR_COOKIE_CONSENT_VERSION );
		}
		// Update settings from Version 1.7.6.
		$prev_gdpr_option = get_option( 'GDPRCookieConsent-2.0' );
		if ( isset( $prev_gdpr_option['is_on'] ) ) {
			unset( $prev_gdpr_option['button_1_selected_text'] );
			$prev_gdpr_option['button_1_text']              = 'Accept';
			$prev_gdpr_option['notify_message']             = addslashes( 'This website uses cookies to improve your experience. We\'ll assume you\'re ok with this, but you can opt-out if you wish.' );
			$prev_gdpr_option['opacity']                    = '0.80';
			$prev_gdpr_option['template']                   = 'banner-default';
			$prev_gdpr_option['banner_template']            = 'banner-default';
			$prev_gdpr_option['popup_template']             = 'popup-default';
			$prev_gdpr_option['widget_template']            = 'widget-default';
			$prev_gdpr_option['button_1_is_on']             = true;
			$prev_gdpr_option['button_2_is_on']             = true;
			$prev_gdpr_option['button_3_is_on']             = true;
			$prev_gdpr_option['notify_position_horizontal'] = false;
			$prev_gdpr_option['bar_heading_text']           = 'This website uses cookies';

			$prev_gdpr_option['button_4_text']         = 'Cookie Settings';
			$prev_gdpr_option['button_4_url']          = '#';
			$prev_gdpr_option['button_4_action']       = '#cookie_action_settings';
			$prev_gdpr_option['button_4_link_color']   = '#ffffff';
			$prev_gdpr_option['button_4_button_color'] = '#333333';
			$prev_gdpr_option['button_4_new_win']      = false;
			$prev_gdpr_option['button_4_as_button']    = true;
			$prev_gdpr_option['button_4_button_size']  = 'medium';
			$prev_gdpr_option['button_4_is_on']        = true;
			$prev_gdpr_option['button_4_as_popup']     = false;
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $prev_gdpr_option );
			delete_option( 'GDPRCookieConsent-2.0' );
		}
		// Update settings from Version 1.7.9.
		$prev_gdpr_option = get_option( 'GDPRCookieConsent-3.0' );
		if ( isset( $prev_gdpr_option['is_on'] ) ) {
			$prev_gdpr_option['bar_heading_text']     = '';
			$prev_gdpr_option['show_again']           = true;
			$prev_gdpr_option['is_script_blocker_on'] = false;
			$prev_gdpr_option['auto_hide']            = false;
			$prev_gdpr_option['auto_banner_initialize']  = false;
			$prev_gdpr_option['auto_scroll']          = false;
			$prev_gdpr_option['show_again_position']  = 'right';
			$prev_gdpr_option['show_again_text']      = 'Cookie Settings';
			$prev_gdpr_option['show_again_margin']    = '5%';
			$prev_gdpr_option['auto_hide_delay']      = '10000';
			$prev_gdpr_option['auto_banner_initialize_delay']  = '10000';
			$prev_gdpr_option['show_again_div_id']    = '#gdpr-cookie-consent-show-again';

			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $prev_gdpr_option );
			delete_option( 'GDPRCookieConsent-3.0' );
		}
		// update settings from Version 1.8.1.
		$prev_gdpr_option = get_option( 'GDPRCookieConsent-4.0' );
		if ( isset( $prev_gdpr_option['is_on'] ) && GDPR_COOKIE_CONSENT_VERSION >= '1.8.1' ) {
			// button_1 => button_accept.
			$prev_gdpr_option['button_accept_text']         = $prev_gdpr_option['button_1_text'];
			$prev_gdpr_option['button_accept_url']          = $prev_gdpr_option['button_1_url'];
			$prev_gdpr_option['button_accept_action']       = $prev_gdpr_option['button_1_action'];
			$prev_gdpr_option['button_accept_link_color']   = $prev_gdpr_option['button_1_link_color'];
			$prev_gdpr_option['button_accept_button_color'] = $prev_gdpr_option['button_1_button_color'];
			$prev_gdpr_option['button_accept_new_win']      = $prev_gdpr_option['button_1_new_win'];
			$prev_gdpr_option['button_accept_as_button']    = $prev_gdpr_option['button_1_as_button'];
			$prev_gdpr_option['button_accept_button_size']  = $prev_gdpr_option['button_1_button_size'];
			$prev_gdpr_option['button_accept_is_on']        = $prev_gdpr_option['button_1_is_on'];
			// button_2 => button_readmore.
			$prev_gdpr_option['button_readmore_text']         = $prev_gdpr_option['button_2_text'];
			$prev_gdpr_option['button_readmore_url']          = $prev_gdpr_option['button_2_url'];
			$prev_gdpr_option['button_readmore_action']       = $prev_gdpr_option['button_2_action'];
			$prev_gdpr_option['button_readmore_link_color']   = $prev_gdpr_option['button_2_link_color'];
			$prev_gdpr_option['button_readmore_button_color'] = $prev_gdpr_option['button_2_button_color'];
			$prev_gdpr_option['button_readmore_new_win']      = $prev_gdpr_option['button_2_new_win'];
			$prev_gdpr_option['button_readmore_as_button']    = $prev_gdpr_option['button_2_as_button'];
			$prev_gdpr_option['button_readmore_button_size']  = $prev_gdpr_option['button_2_button_size'];
			$prev_gdpr_option['button_readmore_is_on']        = $prev_gdpr_option['button_2_is_on'];
			// button_3 => button_decline.
			$prev_gdpr_option['button_decline_text']         = $prev_gdpr_option['button_3_text'];
			$prev_gdpr_option['button_decline_url']          = $prev_gdpr_option['button_3_url'];
			$prev_gdpr_option['button_decline_action']       = $prev_gdpr_option['button_3_action'];
			$prev_gdpr_option['button_decline_link_color']   = $prev_gdpr_option['button_3_link_color'];
			$prev_gdpr_option['button_decline_button_color'] = $prev_gdpr_option['button_3_button_color'];
			$prev_gdpr_option['button_decline_new_win']      = $prev_gdpr_option['button_3_new_win'];
			$prev_gdpr_option['button_decline_as_button']    = $prev_gdpr_option['button_3_as_button'];
			$prev_gdpr_option['button_decline_button_size']  = $prev_gdpr_option['button_decline_button_size'];
			$prev_gdpr_option['button_decline_is_on']        = $prev_gdpr_option['button_3_is_on'];
			// button_4 => button_settings.
			$prev_gdpr_option['button_settings_text']         = $prev_gdpr_option['button_4_text'];
			$prev_gdpr_option['button_settings_url']          = $prev_gdpr_option['button_4_url'];
			$prev_gdpr_option['button_settings_action']       = $prev_gdpr_option['button_4_action'];
			$prev_gdpr_option['button_settings_link_color']   = $prev_gdpr_option['button_4_link_color'];
			$prev_gdpr_option['button_settings_button_color'] = $prev_gdpr_option['button_4_button_color'];
			$prev_gdpr_option['button_settings_new_win']      = $prev_gdpr_option['button_4_new_win'];
			$prev_gdpr_option['button_settings_as_button']    = $prev_gdpr_option['button_4_as_button'];
			$prev_gdpr_option['button_settings_button_size']  = $prev_gdpr_option['button_4_button_size'];
			$prev_gdpr_option['button_settings_is_on']        = $prev_gdpr_option['button_4_is_on'];
			$prev_gdpr_option['button_settings_as_popup']     = $prev_gdpr_option['button_4_as_popup'];

			// CCPA buttons.
			$prev_gdpr_option['button_donotsell_text']       = 'Do Not Sell My Personal Information';
			$prev_gdpr_option['button_donotsell_link_color'] = '#359bf5';
			$prev_gdpr_option['button_donotsell_as_button']  = false;
			$prev_gdpr_option['button_donotsell_is_on']      = true;

			$prev_gdpr_option['button_confirm_text']         = 'Confirm';
			$prev_gdpr_option['button_confirm_button_color'] = '#18a300';
			$prev_gdpr_option['button_confirm_link_color']   = '#ffffff';
			$prev_gdpr_option['button_confirm_as_button']    = 'true';
			$prev_gdpr_option['button_confirm_button_size']  = 'medium';
			$prev_gdpr_option['button_confirm_is_on']        = true;

			$prev_gdpr_option['button_cancel_text']         = 'Cancel';
			$prev_gdpr_option['button_cancel_button_color'] = '#333333';
			$prev_gdpr_option['button_cancel_link_color']   = '#ffffff';
			$prev_gdpr_option['button_cancel_as_button']    = 'true';
			$prev_gdpr_option['button_cancel_button_size']  = 'medium';
			$prev_gdpr_option['button_cancel_is_on']        = true;

			// reload options.
			$prev_gdpr_option['auto_scroll_reload'] = false;
			$prev_gdpr_option['accept_reload']      = false;
			$prev_gdpr_option['decline_reload']     = false;
			$prev_gdpr_option['cookie_expiry']      = '365';
			// offset for auto scroll.
			$prev_gdpr_option['auto_scroll_offset'] = '10';
			// cookie usage for.
			$prev_gdpr_option['cookie_usage_for'] = 'gdpr';
			// ccpa message.
			$prev_gdpr_option['notify_message_ccpa'] = addslashes( 'In case of sale of your personal information, you may opt out by using the link' );
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $prev_gdpr_option );
			delete_option( 'GDPRCookieConsent-4.0' );
		}
		// update settings from Version 1.8.5.
		$prev_gdpr_option = get_option( 'GDPRCookieConsent-5.0' );
		if ( isset( $prev_gdpr_option['is_on'] ) && GDPR_COOKIE_CONSENT_VERSION >= '1.8.5' ) {
			$prev_gdpr_option['is_ccpa_on'] = false;
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $prev_gdpr_option );
			delete_option( 'GDPRCookieConsent-5.0' );
		}
		// update settings from Version 1.8.8.
		$prev_gdpr_option = get_option( 'GDPRCookieConsent-6.0' );
		if ( isset( $prev_gdpr_option['is_on'] ) && GDPR_COOKIE_CONSENT_VERSION >= '1.8.8' ) {
			$prev_gdpr_option['is_ccpa_iab_on'] = false;
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $prev_gdpr_option );
			delete_option( 'GDPRCookieConsent-6.0' );
		}
		// update settings from Version 1.9.0.
		$prev_gdpr_option = get_option( 'GDPRCookieConsent-7.0' );
		if ( isset( $prev_gdpr_option['is_on'] ) && GDPR_COOKIE_CONSENT_VERSION >= '1.9.0' ) {
			$prev_gdpr_option['button_settings_display_cookies'] = true;
			$prev_gdpr_option['header_scripts']                  = '';
			$prev_gdpr_option['body_scripts']                    = '';
			$prev_gdpr_option['footer_scripts']                  = '';
			$prev_gdpr_option['delete_on_deactivation']          = false;
			$prev_gdpr_option['button_readmore_url_type']        = true;
			$prev_gdpr_option['button_readmore_wp_page']         = false;
			$prev_gdpr_option['button_readmore_page']            = '0';
			$prev_gdpr_option['notify_message_eprivacy']         = addslashes( 'This website uses cookies to improve your experience. We\'ll assume you\'re ok with this, but you can opt-out if you wish.' );
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $prev_gdpr_option );
			delete_option( 'GDPRCookieConsent-7.0' );
		}

		// update setting from Version 1.9.4.
		$prev_gdpr_option = get_option( 'GDPRCookieConsent-8.0' );
		if ( isset( $prev_gdpr_option['is_on'] ) && GDPR_COOKIE_CONSENT_VERSION >= '1.9.4' ) {
			$prev_gdpr_option['background_border_width']              = '0';
			$prev_gdpr_option['background_border_style']              = 'none';
			$prev_gdpr_option['background_border_color']              = '#ffffff';
			$prev_gdpr_option['background_border_radius']             = '0';
			$prev_gdpr_option['button_accept_button_opacity']         = '1';
			$prev_gdpr_option['button_accept_button_border_width']    = '0';
			$prev_gdpr_option['button_accept_button_border_style']    = 'none';
			$prev_gdpr_option['button_accept_button_border_color']    = '#18a300';
			$prev_gdpr_option['button_accept_button_border_radius']   = '0';
			$prev_gdpr_option['button_readmore_button_opacity']       = '1';
			$prev_gdpr_option['button_readmore_button_border_width']  = '0';
			$prev_gdpr_option['button_readmore_button_border_style']  = 'none';
			$prev_gdpr_option['button_readmore_button_border_color']  = '#333333';
			$prev_gdpr_option['button_readmore_button_border_radius'] = '0';
			$prev_gdpr_option['button_decline_button_opacity']        = '1';
			$prev_gdpr_option['button_decline_button_border_width']   = '0';
			$prev_gdpr_option['button_decline_button_border_style']   = 'none';
			$prev_gdpr_option['button_decline_button_border_color']   = '#333333';
			$prev_gdpr_option['button_decline_button_border_radius']  = '0';
			$prev_gdpr_option['button_settings_layout_skin']          = 'layout-default';
			$prev_gdpr_option['button_settings_button_opacity']       = '1';
			$prev_gdpr_option['button_settings_button_border_width']  = '0';
			$prev_gdpr_option['button_settings_button_border_style']  = 'none';
			$prev_gdpr_option['button_settings_button_border_color']  = '#333333';
			$prev_gdpr_option['button_settings_button_border_radius'] = '0';
			$prev_gdpr_option['button_confirm_button_opacity']        = '1';
			$prev_gdpr_option['button_confirm_button_border_width']   = '0';
			$prev_gdpr_option['button_confirm_button_border_style']   = 'none';
			$prev_gdpr_option['button_confirm_button_border_color']   = '#18a300';
			$prev_gdpr_option['button_confirm_button_border_radius']  = '0';
			$prev_gdpr_option['button_cancel_button_opacity']         = '1';
			$prev_gdpr_option['button_cancel_button_border_width']    = '0';
			$prev_gdpr_option['button_cancel_button_border_style']    = 'none';
			$prev_gdpr_option['button_cancel_button_border_color']    = '#333333';
			$prev_gdpr_option['button_cancel_button_border_radius']   = '0';
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $prev_gdpr_option );
			delete_option( 'GDPRCookieConsent-8.0' );
		}
	}

	/**
	 * Function returns list of templates.
	 *
	 * @since 2.5
	 * @param String $template_type Template type.
	 * @return array
	 */
	public function get_templates( $template_type ) {
		$templates = apply_filters(
			'gdprcookieconsent_templates',
			array(
				'banner' => array(
					'default'          => array(
						'name'             => 'banner-default',
						'css'              => 'max-width:500px;color:#000000;background-color:#ffffff;text-align:justify;',
						'color'            => '#000000',
						'background_color' => '#ffffff',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#ffffff',
						'border_radius'    => '0',
						'layout'           => 'classic',
						'accept'           => array(
							'text'                 => __( 'Accept', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#66cc66;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#66cc66',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#66cc66',
							'button_border_radius' => '0',
						),
						'decline'          => array(
							'text'                 => __( 'Decline', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#ef5454',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#ef5454',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#007cba;',
							'link_color' => '#007cba',
						),
						'settings'         => array(
							'text'                 => __( 'Cookie Settings', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#007cba;color:#ffffff;float:right;',
							'link_color'           => '#ffffff',
							'button_color'         => '#007cba',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#007cba',
							'button_border_radius' => '0',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#66cc66;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#66cc66',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#66cc66',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#ef5454',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#ef5454',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#007cba;',
							'link_color' => '#007cba',
						),
					),
					'almond_column'    => array(
						'name'             => 'banner-almond_column',
						'css'              => 'max-width:500px;color:#1e3d59;background-color:#e8ddbb;text-align:justify;',
						'color'            => '#1e3d59',
						'background_color' => '#e8ddbb',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#e8ddbb',
						'border_radius'    => '0',
						'layout'           => 'default',
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#de7834',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#de7834',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:#252525;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#252525',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#252525',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#de7834;',
							'link_color' => '#de7834',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#de7834',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#66cc66',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#252525',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#252525',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#de7834;',
							'link_color' => '#de7834',
						),
					),
					'navy_blue_center' => array(
						'name'             => 'banner-navy_blue_center',
						'css'              => 'max-width:500px;color:#e5e5e5;background-color:#2a3e71;text-align:center;',
						'color'            => '#e5e5e5',
						'background_color' => '#2a3e71',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#2a3e71',
						'border_radius'    => '0',
						'layout'           => 'default',
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#369ee3;',
							'link_color' => '#369ee3',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#e5e5e5',
							'button_border_radius' => '0',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#369ee3;',
							'link_color' => '#369ee3',
						),
					),
					'grey_column'      => array(
						'name'             => 'banner-grey_column',
						'css'              => 'max-width:500px;color:#000000;background-color:#f4f4f4;text-align:justify;',
						'color'            => '#000000',
						'background_color' => '#f4f4f4',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#f4f4f4',
						'border_radius'    => '0',
						'layout'           => 'classic',
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#e14469;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#e14469',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#e14469',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:#111111;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#111111',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#111111',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#e14469;',
							'link_color' => '#e14469',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#e14469;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#e14469',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#e14469',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#111111;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#111111',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#111111',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#e14469;',
							'link_color' => '#e14469',
						),
					),
					'dark_row'         => array(
						'name'             => 'banner-dark_row',
						'css'              => 'max-width:500px;color:#ffffff;background-color:#323742;text-align:center;',
						'color'            => '#ffffff',
						'background_color' => '#323742',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#323742',
						'border_radius'    => '0',
						'layout'           => 'default',
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#3eaf9a;color:#ffffff;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid 6a8ee7',
							'link_color'           => '#ffffff',
							'button_color'         => '#3eaf9a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#3eaf9a',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(50, 55, 66, 0);color:#3eaf9a;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid #3eaf9a;',
							'link_color'           => '#3eaf9a',
							'button_color'         => '#323742',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#3eaf9a',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#3eaf9a;',
							'link_color' => '#3eaf9a',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#3eaf9a;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#3eaf9a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#3eaf9a',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#323742;color:#3eaf9a;margin:0 0.5rem 0 0',
							'link_color'           => '#3eaf9a',
							'button_color'         => '#323742',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#323742',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#3eaf9a;',
							'link_color' => '#3eaf9a',
						),
					),
					'grey_center'      => array(
						'name'             => 'banner-grey_center',
						'css'              => 'max-width:500px;color:#000000;background-color:#f4f4f4;text-align:center;',
						'color'            => '#000000',
						'background_color' => '#f4f4f4',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#f4f4f4',
						'border_radius'    => '0',
						'layout'           => 'classic',
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#de7834;',
							'link_color' => '#de7834',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#ffffff;min-width:5rem;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#de7834',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#de7834',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:#252525;color:#ffffff;',
							'link_color'           => '#ffffff',
							'button_color'         => '#252525',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#252525',
							'button_border_radius' => '0',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#de7834',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#66cc66',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#252525',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#252525',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#de7834;',
							'link_color' => '#de7834',
						),
					),
					'dark'             => array(
						'name'             => 'banner-dark',
						'css'              => 'max-width:500px;color:#ffffff;background-color:#262626;text-align:justify;',
						'color'            => '#ffffff',
						'background_color' => '#262626',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#262626',
						'border_radius'    => '0',
						'layout'           => 'default',
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#6a8ee7;color:#ffffff;margin:0 0.5rem 0 0;border:1px solid #6a8ee7;',
							'link_color'           => '#ffffff',
							'button_color'         => '#6a8ee7',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#6a8ee7',
							'button_border_radius' => '0',
						),
						'decline'          => array(
							'text'                 => 'Decline',
							'as_button'            => true,
							'css'                  => 'background-color:#808080;color:#ffffff;float:right;border:1px solid #808080;',
							'link_color'           => '#ffffff',
							'button_color'         => '#808080',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#808080',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#6a8ee7;',
							'link_color' => '#6a8ee7',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(38, 38, 38, 0);color:#808080;float:right;margin:0 0.5rem 0 0;border:1px solid #808080',
							'link_color'           => '#808080',
							'button_color'         => '#262626',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#808080',
							'button_border_radius' => '0',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#6a8ee7;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#6a8ee7',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#6a8ee7',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#808080;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#808080',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#808080',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#6a8ee7;',
							'link_color' => '#6a8ee7',
						),
					),
				),
				'popup'  => array(
					'default'          => array(
						'name'             => 'popup-default',
						'css'              => 'max-width:350px;color:#000000;background-color:#ffffff;text-align:justify;',
						'color'            => '#000000',
						'background_color' => '#ffffff',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#ffffff',
						'border_radius'    => '0',
						'layout'           => 'classic',
						'accept'           => array(
							'text'                 => __( 'Accept', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#66cc66;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#66cc66',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#66cc66',
							'button_border_radius' => '0',
						),
						'decline'          => array(
							'text'                 => __( 'Decline', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#ef5454',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#ef5454',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#007cba;',
							'link_color' => '#007cba',
						),
						'settings'         => array(
							'text'                 => __( 'Cookie Settings', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#007cba;color:#ffffff;float:right;',
							'link_color'           => '#ffffff',
							'button_color'         => '#007cba',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#007cba',
							'button_border_radius' => '0',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#66cc66;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#66cc66',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#66cc66',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#ef5454',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#ef5454',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#007cba;',
							'link_color' => '#007cba',
						),
					),
					'dark'             => array(
						'name'             => 'popup-dark',
						'css'              => 'max-width:350px;color:#ffffff;background-color:#262626;text-align:justify;',
						'color'            => '#ffffff',
						'background_color' => '#262626',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#262626',
						'border_radius'    => '0',
						'layout'           => 'default',
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#6a8ee7;color:#ffffff;margin:0 0.5rem 0 0;border:1px solid #6a8ee7;',
							'link_color'           => '#ffffff',
							'button_color'         => '#6a8ee7',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#6a8ee7',
							'button_border_radius' => '0',
						),
						'decline'          => array(
							'text'                 => 'Decline',
							'as_button'            => true,
							'css'                  => 'background-color:#808080;color:#ffffff;float:none;border:1px solid #808080;',
							'link_color'           => '#ffffff',
							'button_color'         => '#808080',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#808080',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#6a8ee7;',
							'link_color' => '#6a8ee7',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(38, 38, 38, 0);color:#808080;float:right;margin:0 0.5rem 0 0;border:1px solid #808080',
							'link_color'           => '#808080',
							'button_color'         => '#262626',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#808080',
							'button_border_radius' => '0',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#6a8ee7',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#6a8ee7',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#808080;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#808080',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#808080',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#6a8ee7;',
							'link_color' => '#6a8ee7',
						),
					),
					'almond_column'    => array(
						'name'             => 'popup-almond_column',
						'css'              => 'max-width:350px;color:#1e3d59;background-color:#e8ddbb;text-align:justify;',
						'color'            => '#1e3d59',
						'background_color' => '#e8ddbb',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#e8ddbb',
						'border_radius'    => '0',
						'layout'           => 'default',
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#de7834',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#de7834',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:#252525;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#252525',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#252525',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#de7834;',
							'link_color' => '#de7834',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#de7834',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#252525',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#252525',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#252525',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#de7834;',
							'link_color' => '#de7834',
						),
					),
					'navy_blue_center' => array(
						'name'             => 'popup-navy_blue_center',
						'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:center;',
						'color'            => '#e5e5e5',
						'background_color' => '#2a3e71',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#2a3e71',
						'border_radius'    => '0',
						'layout'           => 'default',
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#369ee3;',
							'link_color' => '#369ee3',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#e5e5e5',
							'button_border_radius' => '0',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#66cc66',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#369ee3;',
							'link_color' => '#369ee3',
						),
					),
					'dark_row'         => array(
						'name'             => 'popup-dark_row',
						'css'              => 'max-width:350px;color:#ffffff;background-color:#323742;text-align:center;',
						'color'            => '#ffffff',
						'background_color' => '#323742',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#323742',
						'border_radius'    => '0',
						'layout'           => 'default',
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#3eaf9a;color:#ffffff;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid 6a8ee7',
							'link_color'           => '#ffffff',
							'button_color'         => '#3eaf9a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#3eaf9a',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(50, 55, 66, 0);color:#3eaf9a;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid #3eaf9a;',
							'link_color'           => '#3eaf9a',
							'button_color'         => '#323742',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#3eaf9a',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#3eaf9a;',
							'link_color' => '#3eaf9a',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#3eaf9a;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#3eaf9a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#3eaf9a',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#323742;color:#3eaf9a;margin:0 0.5rem 0 0',
							'link_color'           => '#3eaf9a',
							'button_color'         => '#323742',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#323742',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#3eaf9a;',
							'link_color' => '#3eaf9a',
						),
					),
					'grey_center'      => array(
						'name'             => 'popup-grey_center',
						'css'              => 'max-width:350px;color:#000000;background-color:#f4f4f4;text-align:center;',
						'color'            => '#000000',
						'background_color' => '#f4f4f4',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#f4f4f4',
						'border_radius'    => '0',
						'layout'           => 'classic',
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#de7834;',
							'link_color' => '#de7834',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#ffffff;min-width:5rem;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#de7834',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#de7834',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:#252525;color:#ffffff;',
							'link_color'           => '#ffffff',
							'button_color'         => '#252525',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#252525',
							'button_border_radius' => '0',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#de7834',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#de7834',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#252525',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#252525',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#de7834;',
							'link_color' => '#de7834',
						),
					),
					'navy_blue_box'    => array(
						'name'             => 'popup-navy_blue_box',
						'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:justify;border-radius:15px;',
						'color'            => '#e5e5e5',
						'background_color' => '#2a3e71',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#2a3e71',
						'border_radius'    => '15',
						'layout'           => 'default',
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#369ee3;',
							'link_color' => '#369ee3',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;width:100%;',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;width:100%;',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#e5e5e5',
							'button_border_radius' => '0',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#e5e5e5;margin:0 0.5rem 0 0',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#369ee3;',
							'link_color' => '#369ee3',
						),
					),
					'grey_column'      => array(
						'name'             => 'popup-grey_column',
						'css'              => 'max-width:350px;color:#000000;background-color:#f4f4f4;text-align:justify;border:1px solid #111111',
						'color'            => '#000000',
						'background_color' => '#f4f4f4',
						'opacity'          => '1',
						'border_style'     => 'solid',
						'border_width'     => '1',
						'border_color'     => '#111111',
						'border_radius'    => '0',
						'layout'           => 'classic',
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#e14469;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#e14469',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#e14469',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:#111111;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#111111',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#111111',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#e14469;',
							'link_color' => '#e14469',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#e14469;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#e14469',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#e14469',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#111111;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#111111',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#111111',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#e14469;',
							'link_color' => '#e14469',
						),
					),
					'navy_blue_square' => array(
						'name'             => 'popup-navy_blue_square',
						'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:justify;',
						'color'            => '#e5e5e5',
						'background_color' => '#2a3e71',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#2a3e71',
						'border_radius'    => '0',
						'layout'           => 'default',
						'decline'          => array(
							'text'                 => 'Decline',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;width:41%;margin:0 0.5rem 0 0;min-width:5rem;border:1px solid #369ee3;',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;width:41%;min-width:5rem;float:right;border:1px solid #369ee3;',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;width:100%;margin:1rem auto 0 auto;min-width:5rem;',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#e5e5e5;margin:0 0.5rem 0 0',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#007cba;',
							'link_color' => '#007cba',
						),
					),
				),
				'widget' => array(
					'default'          => array(
						'name'             => 'widget-default',
						'css'              => 'max-width:350px;color:#000000;background-color:#ffffff;text-align:justify;',
						'color'            => '#000000',
						'background_color' => '#ffffff',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#ffffff',
						'border_radius'    => '0',
						'layout'           => 'classic',
						'accept'           => array(
							'text'                 => __( 'Accept', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#66cc66;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#66cc66',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#66cc66',
							'button_border_radius' => '0',
						),
						'decline'          => array(
							'text'                 => __( 'Decline', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#ef5454',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#ef5454',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#007cba;',
							'link_color' => '#007cba',
						),
						'settings'         => array(
							'text'                 => __( 'Cookie Settings', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#007cba;color:#ffffff;float:right;',
							'link_color'           => '#ffffff',
							'button_color'         => '#007cba',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#007cba',
							'button_border_radius' => '0',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#66cc66;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#66cc66',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#66cc66',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#ef5454',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#ef5454',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#007cba;',
							'link_color' => '#007cba',
						),
					),
					'dark'             => array(
						'name'             => 'widget-dark',
						'css'              => 'max-width:350px;color:#ffffff;background-color:#262626;text-align:justify;',
						'color'            => '#ffffff',
						'background_color' => '#262626',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#262626',
						'border_radius'    => '0',
						'layout'           => 'default',
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#6a8ee7;color:#ffffff;margin:0 0.5rem 0 0;border:1px solid #6a8ee7;',
							'link_color'           => '#ffffff',
							'button_color'         => '#6a8ee7',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#6a8ee7',
							'button_border_radius' => '0',
						),
						'decline'          => array(
							'text'                 => 'Decline',
							'as_button'            => true,
							'css'                  => 'background-color:#808080;color:#ffffff;float:none;border:1px solid #808080;',
							'link_color'           => '#ffffff',
							'button_color'         => '#808080',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#808080',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#6a8ee7;',
							'link_color' => '#6a8ee7',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(38, 38, 38, 0);color:#808080;float:right;margin:0 0.5rem 0 0;border:1px solid #808080',
							'link_color'           => '#808080',
							'button_color'         => '#262626',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#808080',
							'button_border_radius' => '0',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#6a8ee7',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#6a8ee7',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#808080;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#808080',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#808080',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#6a8ee7;',
							'link_color' => '#6a8ee7',
						),
					),
					'almond_column'    => array(
						'name'             => 'widget-almond_column',
						'css'              => 'max-width:350px;color:#1e3d59;background-color:#e8ddbb;text-align:justify;',
						'color'            => '#1e3d59',
						'background_color' => '#e8ddbb',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#e8ddbb',
						'border_radius'    => '0',
						'layout'           => 'default',
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#de7834',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#de7834',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:#252525;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#252525',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#252525',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#de7834;',
							'link_color' => '#de7834',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#de7834',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#252525',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#252525',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#252525',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#de7834;',
							'link_color' => '#de7834',
						),
					),
					'navy_blue_box'    => array(
						'name'             => 'widget-navy_blue_box',
						'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:justify;border-radius:15px;',
						'color'            => '#e5e5e5',
						'background_color' => '#2a3e71',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#2a3e71',
						'border_radius'    => '15',
						'layout'           => 'default',
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#369ee3;',
							'link_color' => '#369ee3',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;width:100%;',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;width:100%;',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#e5e5e5',
							'button_border_radius' => '0',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#e5e5e5;margin:0 0.5rem 0 0',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#369ee3;',
							'link_color' => '#369ee3',
						),
					),
					'dark_row'         => array(
						'name'             => 'widget-dark_row',
						'css'              => 'max-width:350px;color:#ffffff;background-color:#323742;text-align:center;',
						'color'            => '#ffffff',
						'background_color' => '#323742',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#323742',
						'border_radius'    => '0',
						'layout'           => 'default',
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#3eaf9a;color:#ffffff;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid 6a8ee7',
							'link_color'           => '#ffffff',
							'button_color'         => '#3eaf9a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#3eaf9a',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(50, 55, 66, 0);color:#3eaf9a;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid #3eaf9a;',
							'link_color'           => '#3eaf9a',
							'button_color'         => '#323742',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#3eaf9a',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#3eaf9a;',
							'link_color' => '#3eaf9a',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#3eaf9a;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#3eaf9a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#3eaf9a',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#323742;color:#3eaf9a;margin:0 0.5rem 0 0',
							'link_color'           => '#3eaf9a',
							'button_color'         => '#323742',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#323742',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#3eaf9a;',
							'link_color' => '#3eaf9a',
						),
					),
					'navy_blue_center' => array(
						'name'             => 'widget-navy_blue_center',
						'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:center;',
						'color'            => '#e5e5e5',
						'background_color' => '#2a3e71',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#2a3e71',
						'border_radius'    => '0',
						'layout'           => 'default',
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#369ee3;',
							'link_color' => '#369ee3',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#e5e5e5',
							'button_border_radius' => '0',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#66cc66',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#369ee3;',
							'link_color' => '#369ee3',
						),
					),
					'grey_column'      => array(
						'name'             => 'widget-grey_column',
						'css'              => 'max-width:350px;color:#000000;background-color:#f4f4f4;text-align:justify;border: 1px solid #111111;',
						'color'            => '#000000',
						'background_color' => '#f4f4f4',
						'opacity'          => '1',
						'border_style'     => 'solid',
						'border_width'     => '1',
						'border_color'     => '#111111',
						'border_radius'    => '0',
						'layout'           => 'classic',
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#e14469;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#e14469',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#e14469',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:#111111;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#111111',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#111111',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#e14469;',
							'link_color' => '#e14469',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#e14469;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#e14469',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#e14469',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#111111;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#111111',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#111111',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#e14469;',
							'link_color' => '#e14469',
						),
					),
					'grey_center'      => array(
						'name'             => 'widget-grey_center',
						'css'              => 'max-width:350px;color:#000000;background-color:#f4f4f4;text-align:center;',
						'color'            => '#000000',
						'background_color' => '#f4f4f4',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#f4f4f4',
						'border_radius'    => '0',
						'layout'           => 'classic',
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#de7834;',
							'link_color' => '#de7834',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#ffffff;min-width:5rem;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#de7834',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#de7834',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:#252525;color:#ffffff;',
							'link_color'           => '#ffffff',
							'button_color'         => '#252525',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#252525',
							'button_border_radius' => '0',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#de7834',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#de7834',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#252525',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#252525',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#de7834;',
							'link_color' => '#de7834',
						),
					),
					'navy_blue_square' => array(
						'name'             => 'widget-navy_blue_square',
						'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:justify;',
						'color'            => '#e5e5e5',
						'background_color' => '#2a3e71',
						'opacity'          => '1',
						'border_style'     => 'none',
						'border_width'     => '0',
						'border_color'     => '#2a3e71',
						'border_radius'    => '0',
						'layout'           => 'default',
						'decline'          => array(
							'text'                 => 'Decline',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;width:41%;margin:0 0.5rem 0 0;min-width:5rem;border:1px solid #369ee3;',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;width:41%;min-width:5rem;float:right;border:1px solid #369ee3;',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;width:100%;margin:1rem auto 0 auto;min-width:5rem;',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#de7834;color:#e5e5e5;margin:0 0.5rem 0 0',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#369ee3',
							'button_border_radius' => '0',
						),
						'donotsell'        => array(
							'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
							'as_button'  => false,
							'css'        => 'color:#007cba;',
							'link_color' => '#007cba',
						),
					),
				),
			)
		);
		return $templates[ $template_type ];
	}

	/**
	 * Admin settings page.
	 *
	 * @since 1.0
	 */
	public function admin_settings_page() {
		$installed_plugins = get_plugins();
		$pro_installed     = isset( $installed_plugins['wpl-cookie-consent/wpl-cookie-consent.php'] ) ? true : false;
		$is_pro            = get_option( 'wpl_pro_active', false );
		if ( $is_pro ) {
			$support_url = 'https://club.wpeka.com/my-account/orders/?utm_source=plugin&utm_medium=gdpr&utm_campaign=help-mascot&utm_content=support';
		} else {
			$support_url = 'https://wordpress.org/support/plugin/gdpr-cookie-consent/';
		}
		wp_enqueue_style( $this->plugin_name );
		wp_enqueue_script( $this->plugin_name );
		wp_enqueue_script( $this->plugin_name . '-vue' );
		wp_enqueue_script( $this->plugin_name . '-mascot' );
		wp_enqueue_style( $this->plugin_name . '-select2' );
		wp_enqueue_script( $this->plugin_name . '-select2' );

		wp_localize_script(
			$this->plugin_name . '-mascot',
			'mascot_obj',
			array(
				'is_pro'            => $is_pro,
				'documentation_url' => 'https://club.wpeka.com/docs/wp-cookie-consent/',
				'faq_url'           => 'https://club.wpeka.com/docs/wp-cookie-consent/faqs/faq-2/',
				'support_url'       => $support_url,
				'upgrade_url'       => 'https://club.wpeka.com/product/wp-gdpr-cookie-consent/?utm_source=plugin&utm_medium=gdpr&utm_campaign=help-mascot_&utm_content=upgrade-to-pro',
				'pro_installed'     => $pro_installed,
			)
		);
		// Lock out non-admins.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_attr__( 'You do not have sufficient permission to perform this operation', 'gdpr-cookie-consent' ) );
		}
		// Get options.
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		// Check if form has been set.
		if ( isset( $_POST['update_admin_settings_form'] ) || ( isset( $_POST['gdpr_settings_ajax_update'] ) ) ) {
			// Check nonce.
			check_admin_referer( 'gdprcookieconsent-update-' . GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
			if ( 'update_admin_settings_form' === $_POST['gdpr_settings_ajax_update'] ) {
				// module settings saving hook.
				do_action( 'gdpr_module_save_settings' );
				// setting manually default value for restrict posts field.
				if ( ! isset( $_POST['restrict_posts_field'] ) ) {
					$_POST['restrict_posts_field'] = array();
				}
				foreach ( $the_options as $key => $value ) {
					if ( isset( $_POST[ $key . '_field' ] ) ) {
						// Store sanitised values only.
						$the_options[ $key ] = Gdpr_Cookie_Consent::gdpr_sanitise_settings( $key, wp_unslash( $_POST[ $key . '_field' ] ) ); // phpcs:ignore
					}
				}
				switch ( $the_options['cookie_bar_as'] ) {
					case 'banner':
						$the_options['template'] = $the_options['banner_template'];
						break;
					case 'popup':
						$the_options['template'] = $the_options['popup_template'];
						break;
					case 'widget':
						$the_options['template'] = $the_options['widget_template'];
						break;
				}
				$the_options = apply_filters( 'gdpr_module_after_save_settings', $the_options );
				update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
				echo '<div class="updated"><p><strong>' . esc_attr__( 'Settings Updated.', 'gdpr-cookie-consent' ) . '</strong></p></div>';
			}
		}
		if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) ) === 'xmlhttprequest' ) {
			exit();
		}
		if ( get_option( 'wpl_pro_active' ) && '1' === get_option( 'wpl_pro_active' ) && ( ! get_option( 'wpl_pro_version_number' ) || version_compare( get_option( 'wpl_pro_version_number' ), '2.9.0', '<' ) ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'partials/gdpr-cookie-consent-admin-display.php';
			return;
		}
		$settings        = Gdpr_Cookie_Consent::gdpr_get_settings();
		$gdpr_policies   = self::get_cookie_usage_for_options();
		$policies_length = count( $gdpr_policies );
		$policy_keys     = array_keys( $gdpr_policies );
		$policies        = array();
		$is_pro_active   = get_option( 'wpl_pro_active' );
		for ( $i = 0; $i < $policies_length; $i++ ) {
			$policies[ $i ] = array(
				'label' => $policy_keys[ $i ],
				'code'  => $gdpr_policies[ $policy_keys[ $i ] ],
			);
		}
		$cookie_durations        = self::get_cookie_expiry_options();
		$cookie_durations_length = count( $cookie_durations );
		$cookie_expiry_keys      = array_keys( $cookie_durations );
		$cookie_expiry_options   = array();
		for ( $i = 0; $i < $cookie_durations_length; $i++ ) {
			$cookie_expiry_options[ $i ] = array(
				'label' => $cookie_expiry_keys[ $i ],
				'code'  => $cookie_durations[ $cookie_expiry_keys[ $i ] ],
			);
		}
		$position_options           = array();
		$position_options[0]        = array(
			'label' => 'Top',
			'code'  => 'top',
		);
		$position_options[1]        = array(
			'label' => 'Bottom',
			'code'  => 'bottom',
		);
		$widget_position_options    = array();
		$widget_position_options[0] = array(
			'label' => 'Bottom Left',
			'code'  => 'left',
		);
		$widget_position_options[1] = array(
			'label' => 'Bottom Right',
			'code'  => 'right',
		);
		$widget_position_options[2] = array(
			'label' => 'Top Left',
			'code'  => 'top_left',
		);
		$widget_position_options[3] = array(
			'label' => 'Top Right',
			'code'  => 'top_right',
		);

		$show_cookie_as_options    = array();
		$show_cookie_as_options[0] = array(
			'label' => 'Banner',
			'code'  => 'banner',
		);
		$show_cookie_as_options[1] = array(
			'label' => 'Popup',
			'code'  => 'popup',
		);
		$show_cookie_as_options[2] = array(
			'label' => 'Widget',
			'code'  => 'widget',
		);

		$show_language_as_options = array();
		$show_language_as_options = array(
			array(
				'label' => 'Bulgarian',
				'code'  => 'bg',
			),
			array(
				'label' => 'Croatian',
				'code'  => 'hr',
			),
			array(
				'label' => 'Czech',
				'code'  => 'cs',
			),
			array(
				'label' => 'Danish',
				'code'  => 'da',
			),
			array(
				'label' => 'Dutch',
				'code'  => 'nl',
			),
			array(
				'label' => 'English',
				'code'  => 'en',
			),
			array(
				'label' => 'French',
				'code'  => 'fr',
			),
			array(
				'label' => 'German',
				'code'  => 'de',
			),
			array(
				'label' => 'Greek',
				'code'  => 'gr',
			),
			array(
				'label' => 'Hungarian',
				'code'  => 'hu',
			),
			array(
				'label' => 'Icelandic',
				'code'  => 'is',
			),
			array(
				'label' => 'Polish',
				'code'  => 'po',
			),
			array(
				'label' => 'Slovenian',
				'code'  => 'sl',
			),
			array(
				'label' => 'Spanish',
				'code'  => 'es',
			),
		);

		// dropdown option for schedule scan.
		$schedule_scan_options    = array();
		$schedule_scan_options[0] = array(
			'label' => 'Never',
			'code'  => 'never',
		);
		$schedule_scan_options[1] = array(
			'label' => 'Only Once',
			'code'  => 'once',
		);
		$schedule_scan_options[2] = array(
			'label' => 'Monthly',
			'code'  => 'monthly',
		);
		// dropdown option for schedule scan day.
		$schedule_scan_day_options = array();

		for ( $day = 0; $day < 31; $day++ ) {
			$label = 'Day ' . ( $day + 1 );
			$code  = 'Day ' . ( $day + 1 );

			$schedule_scan_day_options[] = array(
				'label' => $label,
				'code'  => $code,
			);
		}

		$on_hide_options         = array();
		$on_hide_options[0]      = array(
			'label' => 'Animate',
			'code'  => true,
		);
		$on_hide_options[1]      = array(
			'label' => 'Disappear',
			'code'  => false,
		);
		$on_load_options         = array();
		$on_load_options[0]      = array(
			'label' => 'Animate',
			'code'  => true,
		);
		$on_load_options[1]      = array(
			'label' => 'Sticky',
			'code'  => false,
		);
		$tab_position_options    = array();
		$tab_position_options[0] = array(
			'label' => 'Left',
			'code'  => 'left',
		);
		$tab_position_options[1] = array(
			'label' => 'Right',
			'code'  => 'right',
		);
		$posts_list              = get_posts();
		$pages_list              = get_pages();
		$list_of_contents        = array();
		$index                   = 0;
		foreach ( $posts_list as $post ) {
			$list_of_contents[ $index ] = array(
				'label' => $post->post_title,
				'code'  => $post->ID,
			);
			++$index;
		}
		foreach ( $pages_list as $page ) {
			$list_of_contents[ $index ] = array(
				'label' => $page->post_title,
				'code'  => $page->ID,
			);
			++$index;
		}
		// pages for hide banner.
		$list_of_pages = array();
		$indx          = 0;
		foreach ( $pages_list as $page ) {
			$list_of_pages[ $indx ] = array(
				'label' => $page->post_title,
				'code'  => $page->ID,
			);
			++$indx;
		}
		// sites for consent forward.
		if ( is_multisite() ) {

			$list_of_sites   = array();
			$sites_list      = get_sites();
			$idx             = 0;
			$current_site_id = get_current_blog_id();
			foreach ( $sites_list as $site ) {
				if ( $site->blog_id != $current_site_id ) {
					$site_details          = get_blog_details( $site->blog_id );
					$list_of_sites[ $idx ] = array(
						'label' => $site_details->blogname,
						'code'  => (int) $site_details->blog_id,
					);
					++$idx;
				}
			}
		}
		$show_as_options      = array();
		$show_as_options[0]   = array(
			'label' => 'Button',
			'code'  => true,
		);
		$show_as_options[1]   = array(
			'label' => 'Link',
			'code'  => false,
		);
		$url_type_options     = array();
		$url_type_options[0]  = array(
			'label' => 'Page',
			'code'  => true,
		);
		$url_type_options[1]  = array(
			'label' => 'Custom URL',
			'code'  => false,
		);
		$border_styles        = self::get_background_border_styles();
		$styles_length        = count( $border_styles );
		$styles_keys          = array_keys( $border_styles );
		$border_style_options = array();
		for ( $i = 0; $i < $styles_length; $i++ ) {
			$border_style_options[ $i ] = array(
				'label' => $styles_keys[ $i ],
				'code'  => $border_styles[ $styles_keys[ $i ] ],
			);
		}
		$cookie_font    = array();
		$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ? GDPR_COOKIE_CONSENT_VERSION : '';
		if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
			$cookie_font = apply_filters( 'gcc_font_options', $cookie_font );
		} else {
			$cookie_font = self::get_fonts();
		}
		$font_length  = count( $cookie_font );
		$font_keys    = array_keys( $cookie_font );
		$font_options = array();
		for ( $i = 0; $i < $font_length; $i++ ) {
			$font_options[ $i ] = array(
				'label' => $font_keys[ $i ],
				'code'  => $cookie_font[ $font_keys[ $i ] ],
			);
		}
		$layout_skin         = array();
		$layout_skin         = apply_filters( 'gcc_layout_skin_options', $layout_skin );
		$layout_length       = count( $layout_skin );
		$layout_keys         = array_keys( $layout_skin );
		$layout_skin_options = array();

		for ( $i = 0; $i < $layout_length; $i++ ) {
			$layout_skin_options[ $i ] = array(
				'label' => $layout_keys[ $i ],
				'code'  => $layout_skin[ $layout_keys[ $i ] ],
			);
		}
		$privacy_policy_page_options = array();
		$index                       = 0;
		foreach ( $pages_list as $page ) {
			$privacy_policy_page_options[ $index ] = array(
				'label' => $page->post_title,
				'code'  => $page->ID,
			);
			++$index;
		}
		$button_sizes        = self::get_button_sizes();
		$button_sizes_length = count( $button_sizes );
		$button_sizes_keys   = array_keys( $button_sizes );
		$button_size_options = array();
		for ( $i = 0; $i < $button_sizes_length; $i++ ) {
			$button_size_options[ $i ] = array(
				'label' => $button_sizes_keys[ $i ],
				'code'  => $button_sizes[ $button_sizes_keys[ $i ] ],
			);
		}
		$button_sizes        = self::get_button_sizes();
		$sizes_length        = count( $button_sizes );
		$sizes_keys          = array_keys( $button_sizes );
		$accept_size_options = array();

		for ( $i = 0; $i < $sizes_length; $i++ ) {
			$accept_size_options[ $i ] = array(
				'label' => $sizes_keys[ $i ],
				'code'  => $button_sizes[ $sizes_keys[ $i ] ],
			);
		}

		$button_actions        = self::get_js_actions();
		$action_length         = count( $button_actions );
		$action_keys           = array_keys( $button_actions );
		$accept_action_options = array();

		for ( $i = 0; $i < $action_length; $i++ ) {
			$accept_action_options[ $i ] = array(
				'label' => $action_keys[ $i ],
				'code'  => $button_actions[ $action_keys[ $i ] ],
			);
		}
		$accept_button_as_options    = array();
		$accept_button_as_options[0] = array(
			'label' => 'Button',
			'code'  => true,
		);
		$accept_button_as_options[1] = array(
			'label' => 'Link',
			'code'  => false,
		);
		$open_url_options            = array();
		$open_url_options[0]         = array(
			'label' => 'Yes',
			'code'  => true,
		);
		$open_url_options[1]         = array(
			'label' => 'No',
			'code'  => false,
		);
		$decline_action_options      = array();
		$decline_action_options[0]   = array(
			'label' => 'Close Header',
			'code'  => '#cookie_action_close_header_reject',
		);
		$decline_action_options[1]   = array(
			'label' => 'Open URL',
			'code'  => 'CONSTANT_OPEN_URL',
		);

		$settings_layout_options             = array();
		$settings_layout_options[0]          = array(
			'label' => 'Extented Banner',
			'code'  => false,
		);
		$settings_layout_options[1]          = array(
			'label' => 'Popup',
			'code'  => true,
		);
		$settings_layout_options_extended    = array();
		$settings_layout_options_extended[0] = end( $settings_layout_options );
		$script_blocker_settings             = array();
		$cookie_list_settings                = array();
		$cookie_scan_settings                = array();
		$script_blocker_settings             = apply_filters( 'gdpr_settings_script_blocker_values', '' );
		$cookie_list_settings                = apply_filters( 'gdpr_settings_cookie_list_values', '' );
		$cookie_scan_settings                = apply_filters( 'gdpr_settings_cookie_scan_values', '' );
		$geo_options                         = get_option( 'wpl_geo_options' );
		if ( ! is_array( $geo_options ) ) {
			$geo_options = array();
		}
		if ( ! isset( $geo_options['database_prefix'] ) ) {
			$geo_options['maxmind_license_key'] = '';
			$geo_options['database_prefix']     = wp_generate_password( 32, false, false );
			update_option( 'wpl_geo_options', $geo_options );
		}
		if ( ! isset( $geo_options['enable_geotargeting'] ) ) {
			$geo_options['enable_geotargeting'] = false;
			update_option( 'wpl_geo_options', $geo_options );
		}
		$uploads_dir                       = wp_upload_dir();
		$geo_options['database_file_path'] = trailingslashit( $uploads_dir['basedir'] ) . 'gdpr_uploads/' . $geo_options['database_prefix'] . '-GeoLite2-City.mmdb';
		update_option( 'wpl_geo_options', $geo_options );
		wp_enqueue_style( 'gdpr-cookie-consent-integrations' );
		wp_localize_script(
			$this->plugin_name . '-main',
			'settings_obj',
			array(
				'the_options'                      => $settings,
				'ajaxurl'                          => admin_url( 'admin-ajax.php' ),
				'policies'                         => $policies,
				'position_options'                 => $position_options,
				'show_cookie_as_options'           => $show_cookie_as_options,
				'show_language_as_options'         => $show_language_as_options,
				'schedule_scan_options'            => $schedule_scan_options,
				'schedule_scan_day_options'        => $schedule_scan_day_options,
				'on_hide_options'                  => $on_hide_options,
				'on_load_options'                  => $on_load_options,
				'is_pro_active'                    => $is_pro_active,
				'tab_position_options'             => $tab_position_options,
				'cookie_expiry_options'            => $cookie_expiry_options,
				'list_of_contents'                 => $list_of_contents,
				'border_style_options'             => $border_style_options,
				'show_as_options'                  => $show_as_options,
				'url_type_options'                 => $url_type_options,
				'privacy_policy_options'           => $privacy_policy_page_options,
				'button_size_options'              => $button_size_options,
				'accept_size_options'              => $accept_size_options,
				'accept_action_options'            => $accept_action_options,
				'accept_button_as_options'         => $accept_button_as_options,
				'open_url_options'                 => $open_url_options,
				'widget_position_options'          => $widget_position_options,
				'decline_action_options'           => $decline_action_options,
				'settings_layout_options'          => $settings_layout_options,
				'settings_layout_options_extended' => $settings_layout_options_extended,
				'script_blocker_settings'          => $script_blocker_settings,
				'font_options'                     => $font_options,
				'layout_skin_options'              => $layout_skin_options,
				'cookie_list_settings'             => $cookie_list_settings,
				'cookie_scan_settings'             => $cookie_scan_settings,
				'restore_settings_nonce'           => wp_create_nonce( 'restore_default_settings' ),
				// added nonce for.
				'import_settings_nonce'            => wp_create_nonce( 'import_settings' ),
				// for pages.
				'list_of_pages'                    => $list_of_pages,
				// for sites.
				'list_of_sites'                    => is_multisite() ? $list_of_sites : null,
				'geo_options'                      => $geo_options,
			)
		);
		wp_enqueue_script( $this->plugin_name . '-main' );
		require_once plugin_dir_path( __FILE__ ) . 'gdpr-cookie-consent-admin-settings.php';
	}

	/**
	 * Register block.
	 *
	 * @since 1.8.4
	 */
	public function gdpr_register_block_type() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		wp_register_script(
			$this->plugin_name . '-block',
			plugin_dir_url( __FILE__ ) . 'js/blocks/gdpr-admin-block.js',
			array(
				'jquery',
				'wp-blocks',
				'wp-i18n',
				'wp-editor',
				'wp-element',
				'wp-components',
			),
			$this->version,
			true
		);
		register_block_type(
			'gdpr/block',
			array(
				'editor_script'   => $this->plugin_name . '-block',
				'render_callback' => array( $this, 'gdpr_block_render_callback' ),
			)
		);
	}

	/**
	 * Render callback for block.
	 *
	 * @since 1.8.4
	 * @return string
	 */
	public function gdpr_block_render_callback() {
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( method_exists( $screen, 'is_block_editor' ) ) {
				wp_enqueue_script( $this->plugin_name . '-block' );
			}
		}
		if ( has_block( 'gdpr/block', get_the_ID() ) ) {
			wp_enqueue_script( $this->plugin_name . '-block' );
		}
		$styles              = 'border: 1px solid #767676';
		$args                = array(
			'numberposts' => -1,
			'post_type'   => 'gdprpolicies',
		);
		$wp_legalpolicy_data = get_posts( $args );
		$content             = '';
		if ( is_array( $wp_legalpolicy_data ) && ! empty( $wp_legalpolicy_data ) ) {
			$content .= '<p>For further information on how we use cookies, please refer to the table below.</p>';
			$content .= "<div class='wp_legalpolicy' style='overflow-x:scroll;overflow:auto;'>";
			$content .= '<table style="width:100%;margin:0 auto;border-collapse:collapse;">';
			$content .= '<thead>';
			$content .= "<th style='" . $styles . "'>Third Party Companies</th><th style='" . $styles . "'>Purpose</th><th style='" . $styles . "'>Applicable Privacy/Cookie Policy Link</th>";
			$content .= '</thead>';
			$content .= '<tbody>';
			foreach ( $wp_legalpolicy_data as $policypost ) {
				$content .= '<tr>';
				$content .= "<td style='" . $styles . "'>" . $policypost->post_title . '</td>';
				$content .= "<td style='" . $styles . "'>" . $policypost->post_content . '</td>';
				$links    = get_post_meta( $policypost->ID, '_gdpr_policies_links_editor' );
				$content .= "<td style='" . $styles . "'>" . $links[0] . '</td>';
				$content .= '</tr>';
			}
			$content .= '</tbody></table></div>';
		}
		return $content;
	}

	/**
	 * Prints a combobox based on options and selected=match value.
	 *
	 * @since 1.0
	 * @param array  $options Array of options.
	 * @param string $selected Which of those options should be selected (allows just one; is case sensitive).
	 */
	public function print_combobox_options( $options, $selected ) {
		foreach ( $options as $key => $value ) {
			echo '<option value="' . esc_html( $value ) . '"';
			if ( $value === $selected ) {
				echo ' selected="selected"';
			}
			echo '>' . esc_html( $key ) . '</option>';
		}
	}

	/**
	 * Return cookie expiry options.
	 *
	 * @since 1.8.1
	 */
	public function get_cookie_expiry_options() {
		$options = array(
			__( 'An hour', 'gdpr-cookie-consent' )  => '' . number_format( 1 / 24, 2 ) . '',
			__( '1 Day', 'gdpr-cookie-consent' )    => '1',
			__( '1 Week', 'gdpr-cookie-consent' )   => '7',
			__( '1 Month', 'gdpr-cookie-consent' )  => '30',
			__( '3 Months', 'gdpr-cookie-consent' ) => '90',
			__( '6 Months', 'gdpr-cookie-consent' ) => '180',
			__( '1 Year', 'gdpr-cookie-consent' )   => '365',
		);
		$options = apply_filters( 'gdprcookieconsent_cookie_expiry_options', $options );
		return $options;
	}

	/**
	 * Return cookie usage options.
	 *
	 * @since 1.8.1
	 */
	public function get_cookie_usage_for_options() {
		$options = array(
			__( 'ePrivacy', 'gdpr-cookie-consent' )    => 'eprivacy',
			__( 'GDPR', 'gdpr-cookie-consent' )        => 'gdpr',
			__( 'CCPA', 'gdpr-cookie-consent' )        => 'ccpa',
			__( 'LGPD', 'gdpr-cookie-consent' )        => 'lgpd',
			__( 'GDPR & CCPA', 'gdpr-cookie-consent' ) => 'both',
		);
		$options = apply_filters( 'gdprcookieconsent_cookie_usage_for_options', $options );
		return $options;
	}

	/**
	 * Return cookie design options.
	 *
	 * @since 1.8.1
	 */
	public function get_cookie_design_options() {
		$options = array(
			__( 'Banner', 'gdpr-cookie-consent' ) => 'banner',
			__( 'Popup', 'gdpr-cookie-consent' )  => 'popup',
			__( 'Widget', 'gdpr-cookie-consent' ) => 'widget',
		);
		$options = apply_filters( 'gdprcookieconsent_cookie_design_options', $options );
		return $options;
	}

	/**
	 * Return border styles for cookie bar and buttons background.
	 *
	 * @return array|mixed|void
	 */
	public function get_background_border_styles() {
		$options = array(
			__( 'None', 'gdpr-cookie-consent' )   => 'none',
			__( 'Solid', 'gdpr-cookie-consent' )  => 'solid',
			__( 'Dashed', 'gdpr-cookie-consent' ) => 'dashed',
			__( 'Dotted', 'gdpr-cookie-consent' ) => 'dotted',
			__( 'Double', 'gdpr-cookie-consent' ) => 'double',
			__( 'Groove', 'gdpr-cookie-consent' ) => 'groove',
			__( 'Hidden', 'gdpr-cookie-consent' ) => 'hidden',
			__( 'Ridge', 'gdpr-cookie-consent' )  => 'ridge',
			__( 'Inset', 'gdpr-cookie-consent' )  => 'inset',
			__( 'Outset', 'gdpr-cookie-consent' ) => 'outset',
		);
		$options = apply_filters( 'gdprcookieconsent_background_border_styles', $options );
		return $options;
	}

	/**
	 * Function returns list of supported fonts, used when printing admin form.
	 *
	 * @since 1.0.0
	 * @return array
	 *
	 * @phpcs:enable
	 */
	public function get_fonts() {
		$fonts = array(
			__( 'Default theme font', 'gdpr-cookie-consent' ) => 'inherit',
			'Sans Serif'      => 'Helvetica, Arial, sans-serif',
			'Serif'           => 'Georgia, Times New Roman, Times, serif',
			'Arial'           => 'Arial, Helvetica, sans-serif',
			'Arial Black'     => 'Arial Black,Gadget,sans-serif',
			'Georgia'         => 'Georgia, serif',
			'Helvetica'       => 'Helvetica, sans-serif',
			'Lucida'          => 'Lucida Sans Unicode, Lucida Grande, sans-serif',
			'Tahoma'          => 'Tahoma, Geneva, sans-serif',
			'Times New Roman' => 'Times New Roman, Times, serif',
			'Trebuchet'       => 'Trebuchet MS, sans-serif',
			'Verdana'         => 'Verdana, Geneva',
		);
		$fonts = apply_filters( 'gdprcookieconsent_fonts', $fonts );
		return $fonts;
	}
	/**
	 * Returns button sizes, used when printing admin form.
	 *
	 * @since 1.0
	 * @return array
	 */
	public function get_button_sizes() {
		$sizes = array(
			__( 'Large', 'gdpr-cookie-consent' )  => 'large',
			__( 'Medium', 'gdpr-cookie-consent' ) => 'medium',
			__( 'Small', 'gdpr-cookie-consent' )  => 'small',
		);
		$sizes = apply_filters( 'gdprcookieconsent_sizes', $sizes );
		return $sizes;
	}

	/**
	 * Return WordPress policy pages for Readmore button.
	 *
	 * @since 1.9.0
	 * @return mixed|void
	 */
	public function get_readmore_pages() {
		$args           = array(
			'sort_order'   => 'ASC',
			'sort_column'  => 'post_title',
			'hierarchical' => 0,
			'child_of'     => 0,
			'parent'       => -1,
			'offset'       => 0,
			'post_type'    => 'page',
			'post_status'  => 'publish',
		);
		$readmore_pages = get_pages( $args );
		return apply_filters( 'gdprcookieconsent_readmore_pages', $readmore_pages );
	}

	/**
	 * Returns list of available jQuery actions, used by buttons/links in header.
	 *
	 * @since 1.0
	 * @return array
	 */
	public function get_js_actions() {
		$js_actions = array(
			__( 'Close Header', 'gdpr-cookie-consent' ) => '#cookie_action_close_header',
			__( 'Open URL', 'gdpr-cookie-consent' )     => 'CONSTANT_OPEN_URL',   // Don't change this value, is used by jQuery.
		);
		return $js_actions;
	}

	/**
	 * Gdpr Policies Import Page
	 *
	 * @since 1.9
	 */
	public function gdpr_policies_import_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_attr__( 'You do not have sufficient permissions to access this page.', 'gdpr-cookie-consent' ) );
		}
		include plugin_dir_path( __FILE__ ) . 'views/gdpr-policies-import-page.php';
	}

	/**
	 *  Function is encoding CSS.
	 *
	 * @param string $css_string it is encoding the CSS.
	 *
	 * @since 2.11.0
	 */
	public function encode_css( $css_string ) {
		$lines        = explode( "\n", $css_string );
		$encoded_line = array();

		foreach ( $lines as $line ) {
			$encoded_line[] = $line . "\\r\\n";
		}

		return implode( "\n", $encoded_line );
	}

	/**
	 * Ajax callback for wizard settings page
	 */
	public function gdpr_cookie_consent_ajax_save_wizard_settings() {
		$is_pro = get_option( 'wpl_pro_active', false );
		if ( isset( $_POST['gcc_settings_form_nonce_wizard'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gcc_settings_form_nonce_wizard'] ) ), 'gcc-settings-form-nonce-wizard' ) ) {
				return;
			}

			$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();

			$the_options['is_on']                 = isset( $_POST['gcc-cookie-enable'] ) && ( true === $_POST['gcc-cookie-enable'] || 'true' === $_POST['gcc-cookie-enable'] ) ? 'true' : 'true';
			$the_options['cookie_usage_for']      = isset( $_POST['gcc-gdpr-policy'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-gdpr-policy'] ) ) : 'gdpr';
			$the_options['cookie_bar_as']         = isset( $_POST['show-cookie-as'] ) ? sanitize_text_field( wp_unslash( $_POST['show-cookie-as'] ) ) : 'banner';
			$the_options['button_accept_is_on']   = isset( $_POST['gcc-cookie-accept-enable'] ) && ( true === $_POST['gcc-cookie-accept-enable'] || 'true' === $_POST['gcc-cookie-accept-enable'] ) ? 'true' : 'true';
			$the_options['show_again']            = isset( $_POST['gcc-revoke-consent-enable'] ) && ( true === $_POST['gcc-revoke-consent-enable'] || 'true' === $_POST['gcc-revoke-consent-enable'] ) ? 'true' : 'true';
			$the_options['button_decline_is_on']  = isset( $_POST['gcc-cookie-decline-enable'] ) && ( true === $_POST['gcc-cookie-decline-enable'] || 'true' === $_POST['gcc-cookie-decline-enable'] ) ? 'true' : 'true';
			$the_options['button_settings_is_on'] = isset( $_POST['gcc-cookie-settings-enable'] ) && ( true === $_POST['gcc-cookie-settings-enable'] || 'true' === $_POST['gcc-cookie-settings-enable'] ) ? 'true' : 'true';
			// adding the extra elseif condn to set the correct value for the geolocation selections for the wizard.
			// for IAB.
			if ( isset( $_POST['gcc-iab-enable'] ) ) {
				if ( 'no' === $_POST['gcc-iab-enable'] ) {
					$the_options['is_ccpa_iab_on'] = 'false';
				} elseif ( 'false' == $_POST['gcc-iab-enable'] ) {
					$the_options['is_ccpa_iab_on'] = 'false';
				} else {
					$the_options['is_ccpa_iab_on'] = 'true';
				}
			}
			if ( ! get_option( 'wpl_pro_active' ) ) {
				$the_options['is_script_blocker_on'] = isset( $_POST['gcc-script-blocker-on'] ) && ( true === $_POST['gcc-script-blocker-on'] || 'true' === $_POST['gcc-script-blocker-on'] ) ? 'true' : 'false';
				$the_options['enable_safe']          = isset( $_POST['gcc-enable-safe'] ) && ( true === $_POST['gcc-enable-safe'] || 'true' === $_POST['gcc-enable-safe'] ) ? 'true' : 'false';
				$the_options['logging_on']           = isset( $_POST['gcc-logging-on'] ) && ( true === $_POST['gcc-logging-on'] || 'true' === $_POST['gcc-logging-on'] ) ? 'true' : 'false';
				// For EU.
				if ( isset( $_POST['gcc-eu-enable'] ) ) {
					if ( 'no' === $_POST['gcc-eu-enable'] ) {
						$the_options['is_eu_on'] = 'false';
					} elseif ( 'false' == $_POST['gcc-eu-enable'] ) {
						$the_options['is_eu_on'] = 'false';
					} else {
						$the_options['is_eu_on'] = 'true';
					}
				}
				// For CCPA.
				if ( isset( $_POST['gcc-ccpa-enable'] ) ) {
					if ( 'no' === $_POST['gcc-ccpa-enable'] ) {
						$the_options['is_ccpa_on'] = 'false';
					} elseif ( 'false' == $_POST['gcc-ccpa-enable'] ) {
						$the_options['is_ccpa_on'] = 'false';
					} else {
						$the_options['is_ccpa_on'] = 'true';
					}
				}
				if ( isset( $the_options['cookie_usage_for'] ) ) {
					switch ( $the_options['cookie_usage_for'] ) {
						case 'both':
						case 'gdpr':
						case 'lgpd':
						case 'eprivacy':
							update_option( 'wpl_bypass_script_blocker', 0 );
							break;
						case 'ccpa':
							update_option( 'wpl_bypass_script_blocker', 1 );
							break;
					}
				}
			}
			if ( ! get_option( 'wpl_pro_active' ) ) {

				$saved_options                  = get_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
				$the_options['banner_template'] = isset( $_POST['gdpr-banner-template'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-banner-template'] ) ) : 'banner-default';

				$the_options['popup_template'] = isset( $_POST['gdpr-popup-template'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-popup-template'] ) ) : 'popup-default';

				$the_options['widget_template'] = isset( $_POST['gdpr-widget-template'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-widget-template'] ) ) : 'widget-default';

				$template      = isset( $_POST['gdpr-template'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-template'] ) ) : 'none';
				$cookie_bar_as = $the_options['cookie_bar_as'];
				if ( 'none' !== $template && $saved_options['template'] !== $template ) {
					$the_options[ $cookie_bar_as . '_template' ] = $template;
					$the_options['template']                     = $template;
					$template_parts                              = explode( '-', $template );
					$template                                    = array_pop( $template_parts );
					$templates                                   = apply_filters( 'gdpr_get_templates', $cookie_bar_as );
					$template                                    = $templates[ $template ];
					$the_options['text']                         = $template['color'];
					$the_options['background']                   = $template['background_color'];
					$the_options['opacity']                      = $template['opacity'];
					$the_options['background_border_style']      = $template['border_style'];
					$the_options['background_border_width']      = $template['border_width'];
					$the_options['background_border_color']      = $template['border_color'];
					$the_options['background_border_radius']     = $template['border_radius'];
					if ( isset( $template['accept'] ) ) {
						$the_options['button_accept_is_on']     = true;
						$the_options['button_accept_all_is_on'] = false;
						if ( $template['accept']['as_button'] ) {
							$the_options['button_accept_as_button']             = $template['accept']['as_button'];
							$the_options['button_accept_button_color']          = $template['accept']['button_color'];
							$the_options['button_accept_button_opacity']        = $template['accept']['button_opacity'];
							$the_options['button_accept_button_border_style']   = $template['accept']['button_border_style'];
							$the_options['button_accept_button_border_width']   = $template['accept']['button_border_width'];
							$the_options['button_accept_button_border_color']   = $template['accept']['button_border_color'];
							$the_options['button_accept_button_border_radius']  = $template['accept']['button_border_radius'];
							$the_options['button_accept_button_size']           = $template['accept']['button_size'];
							$the_options['button_accept_all_as_button']         = $template['accept']['as_button'];
							$the_options['button_accept_all_button_color']      = $template['accept']['button_color'];
							$the_options['button_accept_all_btn_opacity']       = $template['accept']['button_opacity'];
							$the_options['button_accept_all_btn_border_style']  = $template['accept']['button_border_style'];
							$the_options['button_accept_all_btn_border_width']  = $template['accept']['button_border_width'];
							$the_options['button_accept_all_btn_border_color']  = $template['accept']['button_border_color'];
							$the_options['button_accept_all_btn_border_radius'] = $template['accept']['button_border_radius'];
							$the_options['button_accept_all_button_size']       = $template['accept']['button_size'];
						} else {
							$the_options['button_accept_as_button']     = false;
							$the_options['button_accept_all_as_button'] = false;
						}
						$the_options['button_accept_link_color']     = $template['accept']['link_color'];
						$the_options['button_accept_all_link_color'] = $template['accept']['link_color'];
					} else {
						$the_options['button_accept_is_on']     = false;
						$the_options['button_accept_all_is_on'] = false;
					}
					if ( isset( $template['decline'] ) ) {
						$the_options['button_decline_is_on'] = true;
						if ( $template['decline']['as_button'] ) {
							$the_options['button_decline_as_button']            = $template['decline']['as_button'];
							$the_options['button_decline_button_color']         = $template['decline']['button_color'];
							$the_options['button_decline_button_opacity']       = $template['decline']['button_opacity'];
							$the_options['button_decline_button_border_style']  = $template['decline']['button_border_style'];
							$the_options['button_decline_button_border_width']  = $template['decline']['button_border_width'];
							$the_options['button_decline_button_border_color']  = $template['decline']['button_border_color'];
							$the_options['button_decline_button_border_radius'] = $template['decline']['button_border_radius'];
							$the_options['button_decline_button_size']          = $template['decline']['button_size'];
						} else {
							$the_options['button_decline_as_button'] = false;
						}
						$the_options['button_decline_link_color'] = $template['decline']['link_color'];
					} else {
						$the_options['button_decline_is_on'] = false;
					}
					if ( isset( $template['settings'] ) ) {
						$the_options['button_settings_is_on'] = true;
						if ( $template['settings']['as_button'] ) {
							$the_options['button_settings_as_button']            = $template['settings']['as_button'];
							$the_options['button_settings_button_color']         = $template['settings']['button_color'];
							$the_options['button_settings_button_opacity']       = $template['settings']['button_opacity'];
							$the_options['button_settings_button_border_style']  = $template['settings']['button_border_style'];
							$the_options['button_settings_button_border_width']  = $template['settings']['button_border_width'];
							$the_options['button_settings_button_border_color']  = $template['settings']['button_border_color'];
							$the_options['button_settings_button_border_radius'] = $template['settings']['button_border_radius'];
							$the_options['button_settings_button_size']          = $template['settings']['button_size'];
						} else {
							$the_options['button_settings_as_button'] = false;
						}
						$the_options['button_settings_link_color'] = $template['settings']['link_color'];
					} else {
						$the_options['button_settings_is_on'] = false;
					}
					if ( isset( $template['readmore'] ) ) {
						$the_options['button_readmore_is_on'] = true;
						if ( $template['readmore']['as_button'] ) {
							$the_options['button_readmore_as_button']            = $template['readmore']['as_button'];
							$the_options['button_readmore_button_color']         = $template['readmore']['button_color'];
							$the_options['button_readmore_button_opacity']       = $template['readmore']['button_opacity'];
							$the_options['button_readmore_button_border_style']  = $template['readmore']['button_border_style'];
							$the_options['button_readmore_button_border_width']  = $template['readmore']['button_border_width'];
							$the_options['button_readmore_button_border_color']  = $template['readmore']['button_border_color'];
							$the_options['button_readmore_button_border_radius'] = $template['readmore']['button_border_radius'];
							$the_options['button_readmore_button_size']          = $template['readmore']['button_size'];
						} else {
							$the_options['button_readmore_as_button'] = false;
						}
						$the_options['button_readmore_link_color'] = $template['readmore']['link_color'];
					} else {
						$the_options['button_readmore_is_on'] = false;
					}
					if ( isset( $template['confirm'] ) ) {
						$the_options['button_confirm_button_color']         = $template['confirm']['button_color'];
						$the_options['button_confirm_button_opacity']       = $template['confirm']['button_opacity'];
						$the_options['button_confirm_button_border_style']  = $template['confirm']['button_border_style'];
						$the_options['button_confirm_button_border_width']  = $template['confirm']['button_border_width'];
						$the_options['button_confirm_button_border_color']  = $template['confirm']['button_border_color'];
						$the_options['button_confirm_button_border_radius'] = $template['confirm']['button_border_radius'];
						$the_options['button_confirm_button_size']          = $template['confirm']['button_size'];
						$the_options['button_confirm_link_color']           = $template['confirm']['link_color'];
					}
					if ( isset( $template['cancel'] ) ) {
						$the_options['button_cancel_button_color']         = $template['cancel']['button_color'];
						$the_options['button_cancel_button_opacity']       = $template['cancel']['button_opacity'];
						$the_options['button_cancel_button_border_style']  = $template['cancel']['button_border_style'];
						$the_options['button_cancel_button_border_width']  = $template['cancel']['button_border_width'];
						$the_options['button_cancel_button_border_color']  = $template['cancel']['button_border_color'];
						$the_options['button_cancel_button_border_radius'] = $template['cancel']['button_border_radius'];
						$the_options['button_cancel_button_size']          = $template['cancel']['button_size'];
						$the_options['button_cancel_link_color']           = $template['cancel']['link_color'];
					}
					if ( isset( $template['donotsell'] ) ) {
						$the_options['button_donotsell_link_color'] = $template['donotsell']['link_color'];
					}
					if ( isset( $template['layout'] ) ) {
						$the_options['button_settings_as_popup']    = true;
						$the_options['button_settings_layout_skin'] = 'layout-' . $template['layout'];
					}
				}
			}

			if ( get_option( 'wpl_pro_active' ) && get_option( 'wc_am_client_wpl_cookie_consent_activated' ) && 'Activated' === get_option( 'wc_am_client_wpl_cookie_consent_activated' ) ) {
				// For EU.
				if ( isset( $_POST['gcc-eu-enable'] ) ) {
					if ( 'no' === $_POST['gcc-eu-enable'] ) {
						$the_options['is_eu_on'] = 'false';
					} elseif ( 'false' == $_POST['gcc-eu-enable'] ) {
						$the_options['is_eu_on'] = 'false';
					} else {
						$the_options['is_eu_on'] = 'true';
					}
				}
				// For CCPA.
				if ( isset( $_POST['gcc-ccpa-enable'] ) ) {
					if ( 'no' === $_POST['gcc-ccpa-enable'] ) {
						$the_options['is_ccpa_on'] = 'false';
					} elseif ( 'false' == $_POST['gcc-ccpa-enable'] ) {
						$the_options['is_ccpa_on'] = 'false';
					} else {
						$the_options['is_ccpa_on'] = 'true';
					}
				}
				$the_options['logging_on'] = isset( $_POST['gcc-logging-on'] ) && ( true === $_POST['gcc-logging-on'] || 'true' === $_POST['gcc-logging-on'] ) ? 'true' : 'false';

				$the_options['banner_template'] = isset( $_POST['gdpr-banner-template'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-banner-template'] ) ) : 'banner-default';

				$the_options['popup_template'] = isset( $_POST['gdpr-popup-template'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-popup-template'] ) ) : 'popup-default';

				$the_options['widget_template'] = isset( $_POST['gdpr-widget-template'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-widget-template'] ) ) : 'widget-default';

				$the_options['is_script_blocker_on'] = isset( $_POST['gcc-script-blocker-on'] ) && ( true === $_POST['gcc-script-blocker-on'] || 'true' === $_POST['gcc-script-blocker-on'] ) ? 'true' : 'false';

				if ( isset( $the_options['cookie_usage_for'] ) ) {
					switch ( $the_options['cookie_usage_for'] ) {
						case 'both':
						case 'gdpr':
						case 'lgpd':
						case 'eprivacy':
							update_option( 'wpl_bypass_script_blocker', 0 );
							break;
						case 'ccpa':
							update_option( 'wpl_bypass_script_blocker', 1 );
							break;
					}
				}

				$template      = isset( $_POST['gdpr-template'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-template'] ) ) : 'none';
				$cookie_bar_as = $the_options['cookie_bar_as'];
				if ( 'none' !== $template && $saved_options['template'] !== $template ) {
					$the_options[ $cookie_bar_as . '_template' ] = $template;
					$the_options['template']                     = $template;
					$template_parts                              = explode( '-', $template );
					$template                                    = array_pop( $template_parts );
					$templates                                   = apply_filters( 'gdpr_get_templates', $cookie_bar_as );
					$template                                    = $templates[ $template ];
					$the_options['text']                         = $template['color'];
					$the_options['background']                   = $template['background_color'];
					$the_options['opacity']                      = $template['opacity'];
					$the_options['background_border_style']      = $template['border_style'];
					$the_options['background_border_width']      = $template['border_width'];
					$the_options['background_border_color']      = $template['border_color'];
					$the_options['background_border_radius']     = $template['border_radius'];
					if ( isset( $template['accept'] ) ) {
						$the_options['button_accept_all_is_on'] = false;
						if ( $template['accept']['as_button'] ) {
							$the_options['button_accept_as_button']             = $template['accept']['as_button'];
							$the_options['button_accept_button_color']          = $template['accept']['button_color'];
							$the_options['button_accept_button_opacity']        = $template['accept']['button_opacity'];
							$the_options['button_accept_button_border_style']   = $template['accept']['button_border_style'];
							$the_options['button_accept_button_border_width']   = $template['accept']['button_border_width'];
							$the_options['button_accept_button_border_color']   = $template['accept']['button_border_color'];
							$the_options['button_accept_button_border_radius']  = $template['accept']['button_border_radius'];
							$the_options['button_accept_button_size']           = $template['accept']['button_size'];
							$the_options['button_accept_all_as_button']         = $template['accept']['as_button'];
							$the_options['button_accept_all_button_color']      = $template['accept']['button_color'];
							$the_options['button_accept_all_btn_opacity']       = $template['accept']['button_opacity'];
							$the_options['button_accept_all_btn_border_style']  = $template['accept']['button_border_style'];
							$the_options['button_accept_all_btn_border_width']  = $template['accept']['button_border_width'];
							$the_options['button_accept_all_btn_border_color']  = $template['accept']['button_border_color'];
							$the_options['button_accept_all_btn_border_radius'] = $template['accept']['button_border_radius'];
							$the_options['button_accept_all_button_size']       = $template['accept']['button_size'];
						} else {
							$the_options['button_accept_as_button']     = false;
							$the_options['button_accept_all_as_button'] = false;
						}
						$the_options['button_accept_link_color']     = $template['accept']['link_color'];
						$the_options['button_accept_all_link_color'] = $template['accept']['link_color'];
					} else {
						$the_options['button_accept_all_is_on'] = false;
					}
					if ( isset( $template['decline'] ) ) {
						if ( $template['decline']['as_button'] ) {
							$the_options['button_decline_as_button']            = $template['decline']['as_button'];
							$the_options['button_decline_button_color']         = $template['decline']['button_color'];
							$the_options['button_decline_button_opacity']       = $template['decline']['button_opacity'];
							$the_options['button_decline_button_border_style']  = $template['decline']['button_border_style'];
							$the_options['button_decline_button_border_width']  = $template['decline']['button_border_width'];
							$the_options['button_decline_button_border_color']  = $template['decline']['button_border_color'];
							$the_options['button_decline_button_border_radius'] = $template['decline']['button_border_radius'];
							$the_options['button_decline_button_size']          = $template['decline']['button_size'];
						} else {
							$the_options['button_decline_as_button'] = false;
						}
						$the_options['button_decline_link_color'] = $template['decline']['link_color'];
					} else {//phpcs:ignore
					}
					if ( isset( $template['settings'] ) ) {
						$the_options['button_settings_is_on'] = true;
						if ( $template['settings']['as_button'] ) {
							$the_options['button_settings_as_button']            = $template['settings']['as_button'];
							$the_options['button_settings_button_color']         = $template['settings']['button_color'];
							$the_options['button_settings_button_opacity']       = $template['settings']['button_opacity'];
							$the_options['button_settings_button_border_style']  = $template['settings']['button_border_style'];
							$the_options['button_settings_button_border_width']  = $template['settings']['button_border_width'];
							$the_options['button_settings_button_border_color']  = $template['settings']['button_border_color'];
							$the_options['button_settings_button_border_radius'] = $template['settings']['button_border_radius'];
							$the_options['button_settings_button_size']          = $template['settings']['button_size'];
						} else {
							$the_options['button_settings_as_button'] = false;
						}
						$the_options['button_settings_link_color'] = $template['settings']['link_color'];
					} else {
						$the_options['button_settings_is_on'] = false;
					}
					if ( isset( $template['readmore'] ) ) {
						$the_options['button_readmore_is_on'] = true;
						if ( $template['readmore']['as_button'] ) {
							$the_options['button_readmore_as_button']            = $template['readmore']['as_button'];
							$the_options['button_readmore_button_color']         = $template['readmore']['button_color'];
							$the_options['button_readmore_button_opacity']       = $template['readmore']['button_opacity'];
							$the_options['button_readmore_button_border_style']  = $template['readmore']['button_border_style'];
							$the_options['button_readmore_button_border_width']  = $template['readmore']['button_border_width'];
							$the_options['button_readmore_button_border_color']  = $template['readmore']['button_border_color'];
							$the_options['button_readmore_button_border_radius'] = $template['readmore']['button_border_radius'];
							$the_options['button_readmore_button_size']          = $template['readmore']['button_size'];
						} else {
							$the_options['button_readmore_as_button'] = false;
						}
						$the_options['button_readmore_link_color'] = $template['readmore']['link_color'];
					} else {
						$the_options['button_readmore_is_on'] = false;
					}
					if ( isset( $template['confirm'] ) ) {
						$the_options['button_confirm_button_color']         = $template['confirm']['button_color'];
						$the_options['button_confirm_button_opacity']       = $template['confirm']['button_opacity'];
						$the_options['button_confirm_button_border_style']  = $template['confirm']['button_border_style'];
						$the_options['button_confirm_button_border_width']  = $template['confirm']['button_border_width'];
						$the_options['button_confirm_button_border_color']  = $template['confirm']['button_border_color'];
						$the_options['button_confirm_button_border_radius'] = $template['confirm']['button_border_radius'];
						$the_options['button_confirm_button_size']          = $template['confirm']['button_size'];
						$the_options['button_confirm_link_color']           = $template['confirm']['link_color'];
					}
					if ( isset( $template['cancel'] ) ) {
						$the_options['button_cancel_button_color']         = $template['cancel']['button_color'];
						$the_options['button_cancel_button_opacity']       = $template['cancel']['button_opacity'];
						$the_options['button_cancel_button_border_style']  = $template['cancel']['button_border_style'];
						$the_options['button_cancel_button_border_width']  = $template['cancel']['button_border_width'];
						$the_options['button_cancel_button_border_color']  = $template['cancel']['button_border_color'];
						$the_options['button_cancel_button_border_radius'] = $template['cancel']['button_border_radius'];
						$the_options['button_cancel_button_size']          = $template['cancel']['button_size'];
						$the_options['button_cancel_link_color']           = $template['cancel']['link_color'];
					}
					if ( isset( $template['donotsell'] ) ) {
						$the_options['button_donotsell_link_color'] = $template['donotsell']['link_color'];
					}
					if ( isset( $template['layout'] ) ) {
						$the_options['button_settings_as_popup']    = true;
						$the_options['button_settings_layout_skin'] = 'layout-' . $template['layout'];
					}
				}
			}
			if ( isset( $_POST['gdpr-cookie-bar-logo-url-holder'] ) ) {
				update_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD, esc_url_raw( wp_unslash( $_POST['gdpr-cookie-bar-logo-url-holder'] ) ) );
			}
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
			wp_send_json_success( array( 'form_options_saved' => true ) );
		}
	}

	/**
	 * Translator function to convert the public facing side texts
	 *
	 * @param string $text          Text to be translated.
	 * @param array  $translations  Array of translations.
	 * @param string $target_language Target language to translate the text into.
	 */
	public function translated_text( $text, $translations, $target_language ) {
		// Assuming $text is the key for the translation in the JSON file.
		if ( isset( $translations[ $text ][ $target_language ] ) ) {
			return $translations[ $text ][ $target_language ];
		} else {
			// Return the original text if no translation is found.
			return $text;
		}
	}

	/**
	 * Return categories.
	 *
	 * @since 1.0
	 * @return array|mixed|object
	 */
	public function gdpr_get_categories() {
		include plugin_dir_path( __FILE__ ) . '/modules/cookie-custom/classes/class-gdpr-cookie-consent-cookie-serve-api.php';
		$cookie_serve_api = new Gdpr_Cookie_Consent_Cookie_Serve_Api();
		$categories       = $cookie_serve_api->get_categories();
		return $categories;
	}

	/**
	 *  Cookie Template card for Pro version.
	 *
	 * @param string $name name of the template.
	 *
	 * @param array  $templates list of template settings.
	 *
	 * @param string $checked name of the selected template.
	 *
	 * @since 1.0.0
	 */
	public function print_template_boxes( $name, $templates, $checked ) {
		$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
		$the_options    = Gdpr_Cookie_Consent::gdpr_get_settings();
		?>
			<div class="gdpr-templates-field-container">
			<?php
			foreach ( $templates as $key => $template ) :
				if ( false !== strpos( $template['name'], 'column' ) ) {
					$column = true;
				} else {
					$column = false;
				}
				if ( false !== strpos( $template['name'], 'square' ) ) {
					$square = true;
				} else {
					$square = false;
				}
				?>
				<div class="gdpr-template-field gdpr-<?php echo esc_attr( $template['name'] ); ?>">
					<div class="gdpr-left-field">
					<c-input type="radio"  name="<?php echo esc_attr( $name ) . '_template_field'; ?>" value="<?php echo esc_attr( $template['name'] ); ?>" @change="onTemplateChange"
					<?php
					if ( $template['name'] === $checked ) {
						echo ':checked="true"';
					}
					?>
					>
					</div>
					<div class="gdpr-right-field" style="<?php echo esc_attr( $template['css'] ); ?>">
						<div class="gdpr-right-field-content">
							<div class="gdpr-group-description">
					<?php
					$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
					if ( '' !== $get_banner_img ) {
						?>
							<img class="gdpr_logo_image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
							<?php
					}
					?>
							<?php
							if ( $the_options['cookie_usage_for'] === 'gdpr' || $the_options['cookie_usage_for'] === 'both' ) :
								?>
								<h3 v-if="gdpr_message_heading.length>0">{{gdpr_message_heading}}</h3>
								<?php elseif ( $the_options['cookie_usage_for'] === 'lgpd' ) : ?>
								<h3 v-if="gdpr_message_heading.length>0">{{lgpd_message_heading}}</h3>
								<?php endif; ?>
							<?php if ( $column ) : ?>
								<?php if ( $the_options['cookie_usage_for'] === 'gdpr' || $the_options['cookie_usage_for'] === 'both' ) : ?>
									<p>{{gdpr_message}}</p>
									<?php elseif ( $the_options['cookie_usage_for'] === 'lgpd' ) : ?>
									<p>{{lgpd_message}}</p>
									<?php endif; ?>
									<?php
									if ( isset( $template['readmore'] ) ) :
										$class = '';
										if ( $template['readmore']['as_button'] ) :
											$class = 'btn btn-sm';
										endif;
										?>
										<p><a style="<?php echo esc_attr( $template['readmore']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['readmore']['text'] ); ?></a></p>
									<?php endif; ?>
								<?php else : ?>
									<p><?php if ( $the_options['cookie_usage_for'] === 'gdpr' || $the_options['cookie_usage_for'] === 'both' ) : ?>
									<p>{{gdpr_message}}</p>
									<?php elseif ( $the_options['cookie_usage_for'] === 'lgpd' ) : ?>
									<p>{{lgpd_message}}</p>
									<?php endif; ?>
										<?php
										if ( isset( $template['readmore'] ) ) :
											$class = '';
											if ( $template['readmore']['as_button'] ) :
												$class = 'btn btn-sm';
											endif;
											?>
											<a style="<?php echo esc_attr( $template['readmore']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['readmore']['text'] ); ?></a>
										<?php endif; ?>
									</p>
								<?php endif; ?>
							</div>
							<div class="gdpr-group-buttons">
									<?php if ( $square ) : ?>
										<?php
										if ( isset( $template['decline'] ) ) :
											$class = '';
											if ( $template['decline']['as_button'] ) :
												$class = 'btn btn-sm';
											endif;
											?>
										<a style="<?php echo esc_attr( $template['decline']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['decline']['text'] ); ?></a>
										<?php endif; ?>
										<?php
										if ( isset( $template['settings'] ) ) :
											$class = '';
											if ( $template['settings']['as_button'] ) :
												$class = 'btn btn-sm';
											endif;
											?>
										<a style="<?php echo esc_attr( $template['settings']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['settings']['text'] ); ?></a>
										<?php endif; ?>
										<?php
										if ( isset( $template['accept'] ) ) :
											$class = '';
											if ( $template['accept']['as_button'] ) :
												$class = 'btn btn-sm';
											endif;
											?>
										<a style="<?php echo esc_attr( $template['accept']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['accept']['text'] ); ?></a>
										<?php endif; ?>
								<?php else : ?>
									<?php
									if ( isset( $template['accept'] ) ) :
										$class = '';
										if ( $template['accept']['as_button'] ) :
											$class = 'btn btn-sm';
										endif;
										?>
										<a style="<?php echo esc_attr( $template['accept']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['accept']['text'] ); ?></a>
									<?php endif; ?>
									<?php
									if ( isset( $template['decline'] ) ) :
										$class = '';
										if ( $template['decline']['as_button'] ) :
											$class = 'btn btn-sm';
										endif;
										?>
										<a style="<?php echo esc_attr( $template['decline']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['decline']['text'] ); ?></a>
									<?php endif; ?>
									<?php
									if ( isset( $template['settings'] ) ) :
										$class = '';
										if ( $template['settings']['as_button'] ) :
											$class = 'btn btn-sm';
										endif;
										?>
										<a style="<?php echo esc_attr( $template['settings']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['settings']['text'] ); ?></a>
									<?php endif; ?>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
			<?php
	}

	/**
	 *  Cookie Template card for Pro version.
	 *
	 * @since 1.0.0
	 */
	public function wpl_cookie_template() {
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		?>
			<c-card v-show="is_gdpr || is_lgpd">
				<c-card-header><?php esc_html_e( 'Cookie Bar Template', 'gdpr-cookie-consent' ); ?></c-card-header>
				<c-card-body>
					<c-row v-show="!show_banner_template">
						<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cookie Templates', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Use a pre-built template to style your Cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
						<c-col class="col-sm-8">
							<div role="group" class="form-group">
								<span class="gdpr-cookie-consent-description"><?php esc_attr_e( 'To preview the pre-built templates below, simply choose a template and then click the "Save Changes" button. Please note that this action will replace your current banner settings.', 'gdpr-cookie-consent' ); ?></span>
							</div>
						</c-col>
					</c-row>
					<c-row v-show="show_banner_template">
						<c-col class="col-sm-3"><label><?php esc_attr_e( 'Cookie Templates', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Use a pre-built template to style your Cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
						<c-col class="col-sm-9">
							<div role="group" class="form-group">
								<span class="gdpr-cookie-consent-description"><?php esc_attr_e( 'To preview the pre-built templates below, simply choose a template and then click the "Save Changes" button. Please note that this action will replace your current banner settings.', 'gdpr-cookie-consent' ); ?></span>
							</div>
						</c-col>
					</c-row>
					<c-row v-show="show_banner_template">
						<c-col class="col-sm-3">
							<input type="hidden" name="gdpr-banner-template" v-model="banner_template">
						</c-col>
						<c-col class="col-sm-9">
				<?php $this->print_template_boxes( 'banner', $this->get_templates( 'banner' ), $the_options['banner_template'] ); ?>
						</c-col>
					</c-row>
					<c-row v-show="show_popup_template">
						<c-col class="col-sm-4">
							<input type="hidden" name="gdpr-popup-template" v-model="popup_template">
						</c-col>
						<c-col class="col-sm-8">
				<?php $this->print_template_boxes( 'popup', $this->get_templates( 'popup' ), $the_options['popup_template'] ); ?>
						</c-col>
					</c-row>
					<c-row v-show="show_widget_template">
						<c-col class="col-sm-4">
							<input type="hidden" name="gdpr-widget-template" v-model="widget_template">
						</c-col>
						<c-col class="col-sm-8">
				<?php $this->print_template_boxes( 'widget', $this->get_templates( 'widget' ), $the_options['widget_template'] ); ?>
						</c-col>
					</c-row>
					<input type="hidden" name="gdpr-template" v-model="template">
				</c-card-body>
			</c-card>
			<?php
	}

	/**
	 * Ajax callback for setting page.
	 */
	public function gdpr_cookie_consent_ajax_save_settings() {
		if ( isset( $_POST['gcc_settings_form_nonce'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gcc_settings_form_nonce'] ) ), 'gcc-settings-form-nonce' ) ) {
				return;
			}
			$the_options                  = Gdpr_Cookie_Consent::gdpr_get_settings();
			$plugin_version               = defined( 'GDPR_COOKIE_CONSENT_VERSION' );
			$the_options['lang_selected'] = isset( $_POST['select-banner-lan'] ) ? sanitize_text_field( wp_unslash( $_POST['select-banner-lan'] ) ) : 'en';
			// consent renewed.
			$the_options['consent_renew_enable'] = isset( $_POST['gcc-consent-renew-enable'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-consent-renew-enable'] ) ) : 'false';
			// scan when.
			$the_options['schedule_scan_when'] = isset( $_POST['gdpr-schedule-scan-when'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-schedule-scan-when'] ) ) : 'Not Scheduled';
			// scan type.
			$the_options['schedule_scan_type'] = isset( $_POST['gdpr-schedule-scan-freq-type'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-schedule-scan-freq-type'] ) ) : 'never';
			// scan date.
			$the_options['scan_date'] = isset( $_POST['gdpr-schedule-scan-date'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-schedule-scan-date'] ) ) : 'Oct 10 2023';
			// scan day.
			$the_options['scan_day'] = isset( $_POST['gdpr-schedule-scan-day'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-schedule-scan-day'] ) ) : 'Day 1';
			// scan time.
			$the_options['scan_time']             = isset( $_POST['gdpr-schedule-scan-time'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-schedule-scan-time'] ) ) : '8:00 PM';
			$the_options['banner_preview_enable'] = isset( $_POST['gcc-banner-preview-enable'] ) && ( true === $_POST['gcc-banner-preview-enable'] || 'true' === $_POST['gcc-banner-preview-enable'] ) ? 'true' : 'false';
			// DO NOT TRACK.
			$the_options['do_not_track_on'] = isset( $_POST['gcc-do-not-track'] ) && ( true === $_POST['gcc-do-not-track'] || 'true' === $_POST['gcc-do-not-track'] ) ? 'true' : 'false';
			// Data Reqs.
			$the_options['data_reqs_on'] = isset( $_POST['gcc-data_reqs'] ) && ( true === $_POST['gcc-data_reqs'] || 'true' === $_POST['gcc-data_reqs'] ) ? 'true' : 'false';
			// Consent log
			$the_options['logging_on'] = isset( $_POST['gcc-logging-on'] ) && ( true === $_POST['gcc-logging-on'] || 'true' === $_POST['gcc-logging-on'] ) ? 'true' : 'false';

			$the_options['is_on']                              = isset( $_POST['gcc-cookie-enable'] ) && ( true === $_POST['gcc-cookie-enable'] || 'true' === $_POST['gcc-cookie-enable'] ) ? 'true' : 'false';
			$the_options['cookie_usage_for']                   = isset( $_POST['gcc-gdpr-policy'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-gdpr-policy'] ) ) : 'gdpr';
			$the_options['cookie_bar_as']                      = isset( $_POST['show-cookie-as'] ) ? sanitize_text_field( wp_unslash( $_POST['show-cookie-as'] ) ) : 'banner';
			$the_options['notify_position_vertical']           = isset( $_POST['gcc-gdpr-cookie-position'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-gdpr-cookie-position'] ) ) : 'bottom';
			$the_options['notify_position_horizontal']         = isset( $_POST['gcc-gdpr-cookie-widget-position'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-gdpr-cookie-widget-position'] ) ) : 'left';
			$the_options['popup_overlay']                      = isset( $_POST['gdpr-cookie-add-overlay'] ) && ( true === $_POST['gdpr-cookie-add-overlay'] || 'true' === $_POST['gdpr-cookie-add-overlay'] ) ? 'true' : 'false';
			$the_options['notify_animate_hide']                = isset( $_POST['gcc-gdpr-cookie-on-hide'] ) && ( true === $_POST['gcc-gdpr-cookie-on-hide'] || 'true' === $_POST['gcc-gdpr-cookie-on-hide'] ) ? 'true' : 'false';
			$the_options['notify_animate_show']                = isset( $_POST['gcc-gdpr-cookie-on-load'] ) && ( true === $_POST['gcc-gdpr-cookie-on-load'] || 'true' === $_POST['gcc-gdpr-cookie-on-load'] ) ? 'true' : 'false';
			$the_options['background']                         = isset( $_POST['gdpr-cookie-bar-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-color'] ) ) : '#ffffff';
			$the_options['text']                               = isset( $_POST['gdpr-cookie-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-text-color'] ) ) : '#000000';
			$the_options['opacity']                            = isset( $_POST['gdpr-cookie-bar-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-opacity'] ) ) : '0.80';
			$the_options['background_border_width']            = isset( $_POST['gdpr-cookie-bar-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-border-width'] ) ) : '0';
			$the_options['background_border_style']            = isset( $_POST['gdpr-cookie-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-border-style'] ) ) : 'none';
			$the_options['background_border_color']            = isset( $_POST['gdpr-cookie-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-border-color'] ) ) : '#ffffff';
			$the_options['background_border_radius']           = isset( $_POST['gdpr-cookie-bar-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-border-radius'] ) ) : '0';
			$the_options['font_family']                        = isset( $_POST['gdpr-cookie-font'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-font'] ) ) : 'inherit';
			$the_options['button_accept_is_on']                = isset( $_POST['gcc-cookie-accept-enable'] ) && ( true === $_POST['gcc-cookie-accept-enable'] || 'true' === $_POST['gcc-cookie-accept-enable'] ) ? 'true' : 'false';
			$the_options['button_accept_text']                 = isset( $_POST['button_accept_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_accept_text_field'] ) ) : 'Accept';
			$the_options['button_accept_button_size']          = isset( $_POST['gdpr-cookie-accept-size'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-size'] ) ) : 'medium';
			$the_options['button_accept_action']               = isset( $_POST['gdpr-cookie-accept-action'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-action'] ) ) : '#cookie_action_close_header';
			$the_options['button_accept_url']                  = isset( $_POST['gdpr-cookie-accept-url'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-url'] ) ) : '#';
			$the_options['button_accept_as_button']            = isset( $_POST['gdpr-cookie-accept-as'] ) && ( true === $_POST['gdpr-cookie-accept-as'] || 'true' === $_POST['gdpr-cookie-accept-as'] ) ? 'true' : 'false';
			$the_options['button_accept_new_win']              = isset( $_POST['gdpr-cookie-url-new-window'] ) && ( true === $_POST['gdpr-cookie-url-new-window'] || 'true' === $_POST['gdpr-cookie-url-new-window'] ) ? 'true' : 'false';
			$the_options['button_accept_button_color']         = isset( $_POST['gdpr-cookie-accept-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-background-color'] ) ) : '#18a300';
			$the_options['button_accept_button_opacity']       = isset( $_POST['gdpr-cookie-accept-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-opacity'] ) ) : '1';
			$the_options['button_accept_button_border_style']  = isset( $_POST['gdpr-cookie-accept-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-style'] ) ) : 'none';
			$the_options['button_accept_button_border_color']  = isset( $_POST['gdpr-cookie-accept-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-color'] ) ) : '#18a300';
			$the_options['button_accept_button_border_width']  = isset( $_POST['gdpr-cookie-accept-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-width'] ) ) : '0';
			$the_options['button_accept_button_border_radius'] = isset( $_POST['gdpr-cookie-accept-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-radius'] ) ) : '0';
			$the_options['button_accept_link_color']           = isset( $_POST['gdpr-cookie-accept-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-text-color'] ) ) : '#ffffff';
			$the_options['notify_message_eprivacy']            = isset( $_POST['notify_message_eprivacy_field'] ) ? wp_kses(
				wp_unslash( $_POST['notify_message_eprivacy_field'] ),
				array(
					'a'      => array(
						'href'   => array(),
						'title'  => array(),
						'target' => array(),
						'rel'    => array(),
						'class'  => array(),
						'id'     => array(),
						'style'  => array(),
					),
					'br'     => array(),
					'em'     => array(),
					'strong' => array(),
					'span'   => array(),
					'p'      => array(),
					'i'      => array(),
					'img'    => array(),
					'b'      => array(),
					'div'    => array(),
					'label'  => array(),
				)
			) : "This website uses cookies to improve your experience. We'll assume you're ok with this, but you can opt-out if you wish.";
			$the_options['bar_heading_text']                   = isset( $_POST['bar_heading_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['bar_heading_text_field'] ) ) : '';
			$the_options['bar_heading_lgpd_text']              = isset( $_POST['bar_heading_text_lgpd_field'] ) ? sanitize_text_field( wp_unslash( $_POST['bar_heading_text_lgpd_field'] ) ) : '';
			// custom css.
			$the_options['gdpr_css_text'] = isset( $_POST['gdpr_css_text_field'] ) ? wp_kses( wp_unslash( $_POST['gdpr_css_text_field'] ), array(), array( 'style' => array() ) ) : '';
			$css_file_path                = ABSPATH . 'wp-content/plugins/gdpr-cookie-consent/public/css/gdpr-cookie-consent-public-custom.css';
			// custom css min file.
			$css_min_file_path = ABSPATH . 'wp-content/plugins/gdpr-cookie-consent/public/css/gdpr-cookie-consent-public-custom.min.css';

			$css_code_to_add = $the_options['gdpr_css_text'];

			// Open the CSS file for writing.
			$css_file = fopen( $css_file_path, 'w' );

			// Check if the file was opened successfully.
			if ( $css_file ) {
				// Write the CSS code to the file.
				fwrite( $css_file, $css_code_to_add );

				// Close the file.
				fclose( $css_file );
			}
			// Open the CSS min file for writing.
			$css_min_file = fopen( $css_min_file_path, 'w' );

			// Check if the file was opened successfully.
			if ( $css_min_file ) {
				// Write the CSS code to the file.
				fwrite( $css_min_file, $css_code_to_add );

				// Close the file.
				fclose( $css_min_file );
			}

			$encode_css                   = $this->encode_css( $the_options['gdpr_css_text'] );
			$the_options['gdpr_css_text'] = $encode_css;

			$the_options['notify_message']                       = isset( $_POST['notify_message_field'] ) ? wp_kses(
				wp_unslash( $_POST['notify_message_field'] ),
				array(
					'a'      => array(
						'href'   => array(),
						'title'  => array(),
						'target' => array(),
						'rel'    => array(),
						'class'  => array(),
						'id'     => array(),
						'style'  => array(),
					),
					'br'     => array(),
					'em'     => array(),
					'strong' => array(),
					'span'   => array(),
					'p'      => array(),
					'i'      => array(),
					'img'    => array(),
					'b'      => array(),
					'div'    => array(),
					'label'  => array(),
				)
			) : "This website uses cookies to improve your experience. We'll assume you're ok with this, but you can opt-out if you wish.";
			$the_options['notify_message_lgpd']                  = isset( $_POST['notify_message_lgpd_field'] ) ? wp_kses(
				wp_unslash( $_POST['notify_message_lgpd_field'] ),
				array(
					'a'      => array(
						'href'   => array(),
						'title'  => array(),
						'target' => array(),
						'rel'    => array(),
						'class'  => array(),
						'id'     => array(),
						'style'  => array(),
					),
					'br'     => array(),
					'em'     => array(),
					'strong' => array(),
					'span'   => array(),
					'p'      => array(),
					'i'      => array(),
					'img'    => array(),
					'b'      => array(),
					'div'    => array(),
					'label'  => array(),
				)
			) : "This website uses cookies for technical and other purposes as specified in the cookie policy. We'll assume you're ok with this, but you can opt-out if you wish.";
			$the_options['about_message']                        = isset( $_POST['about_message_field'] ) ? sanitize_text_field( wp_unslash( $_POST['about_message_field'] ) ) : "Cookies are small text files that can be used by websites to make a user's experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.";
			$the_options['about_message_lgpd']                   = isset( $_POST['about_message_lgpd_field'] ) ? sanitize_text_field( wp_unslash( $_POST['about_message_lgpd_field'] ) ) : "Cookies are small text files that can be used by websites to make a user's experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.";
			$the_options['notify_message_ccpa']                  = isset( $_POST['notify_message_ccpa_field'] ) ? wp_kses(
				wp_unslash( $_POST['notify_message_ccpa_field'] ),
				array(
					'a'      => array(
						'href'   => array(),
						'title'  => array(),
						'target' => array(),
						'rel'    => array(),
						'class'  => array(),
						'id'     => array(),
						'style'  => array(),
					),
					'br'     => array(),
					'em'     => array(),
					'strong' => array(),
					'span'   => array(),
					'p'      => array(),
					'i'      => array(),
					'img'    => array(),
					'b'      => array(),
					'div'    => array(),
					'label'  => array(),
				)
			) : 'In case of sale of your personal information, you may opt out by using the link';
			$the_options['optout_text']                          = isset( $_POST['notify_message_ccpa_optout_field'] ) ? sanitize_text_field( wp_unslash( $_POST['notify_message_ccpa_optout_field'] ) ) : 'Do you really wish to opt-out?';
			$the_options['is_ccpa_iab_on']                       = isset( $_POST['gcc-iab-enable'] ) && ( true === $_POST['gcc-iab-enable'] || 'true' === $_POST['gcc-iab-enable'] ) ? 'true' : 'false';
			$the_options['show_again']                           = isset( $_POST['gcc-revoke-consent-enable'] ) && ( true === $_POST['gcc-revoke-consent-enable'] || 'true' === $_POST['gcc-revoke-consent-enable'] ) ? 'true' : 'false';
			$the_options['show_again_position']                  = isset( $_POST['gcc-tab-position'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-tab-position'] ) ) : 'right';
			$the_options['show_again_margin']                    = isset( $_POST['gcc-tab-margin'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-tab-margin'] ) ) : '5';
			$the_options['show_again_text']                      = isset( $_POST['show_again_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['show_again_text_field'] ) ) : 'Cookie Settings';
			$the_options['is_ticked']                            = isset( $_POST['gcc-autotick'] ) && ( true === $_POST['gcc-autotick'] || 'true' === $_POST['gcc-autotick'] ) ? 'true' : 'false';
			$the_options['auto_hide']                            = isset( $_POST['gcc-auto-hide'] ) && ( true === $_POST['gcc-auto-hide'] || 'true' === $_POST['gcc-auto-hide'] ) ? 'true' : 'false';
			$the_options['auto_hide_delay']                      = isset( $_POST['gcc-auto-hide-delay'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-auto-hide-delay'] ) ) : '10000';
			$the_options['auto_banner_initialize']               = isset( $_POST['gcc-auto-banner-initialize'] ) && ( true === $_POST['gcc-auto-banner-initialize'] || 'true' === $_POST['gcc-auto-banner-initialize'] ) ? 'true' : 'false';
			$the_options['auto_banner_initialize_delay']         = isset( $_POST['gcc-auto-banner-initialize-delay'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-auto-banner-initialize-delay'] ) ) : '10000';
			$the_options['auto_scroll']                          = isset( $_POST['gcc-auto-scroll'] ) && ( true === $_POST['gcc-auto-scroll'] || 'true' === $_POST['gcc-auto-scroll'] ) ? 'true' : 'false';
			$the_options['auto_click']                           = isset( $_POST['gcc-auto-click'] ) && ( true === $_POST['gcc-auto-click'] || 'true' === $_POST['gcc-auto-click'] ) ? 'true' : 'false';
			$the_options['auto_scroll_offset']                   = isset( $_POST['gcc-auto-scroll-offset'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-auto-scroll-offset'] ) ) : '10';
			$the_options['auto_scroll_reload']                   = isset( $_POST['gcc-auto-scroll-reload'] ) && ( true === $_POST['gcc-auto-scroll-reload'] || 'true' === $_POST['gcc-auto-scroll-reload'] ) ? 'true' : 'false';
			$the_options['accept_reload']                        = isset( $_POST['gcc-accept-reload'] ) && ( true === $_POST['gcc-accept-reload'] || 'true' === $_POST['gcc-accept-reload'] ) ? 'true' : 'false';
			$the_options['decline_reload']                       = isset( $_POST['gcc-decline-reload'] ) && ( true === $_POST['gcc-decline-reload'] || 'true' === $_POST['gcc-decline-reload'] ) ? 'true' : 'false';
			$the_options['delete_on_deactivation']               = isset( $_POST['gcc-delete-on-deactivation'] ) && ( true === $_POST['gcc-delete-on-deactivation'] || 'true' === $_POST['gcc-delete-on-deactivation'] ) ? 'true' : 'false';
			$the_options['show_credits']                         = isset( $_POST['gcc-show-credits'] ) && ( true === $_POST['gcc-show-credits'] || 'true' === $_POST['gcc-show-credits'] ) ? 'true' : 'false';
			$the_options['cookie_expiry']                        = isset( $_POST['gcc-cookie-expiry'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-cookie-expiry'] ) ) : '365';
			$the_options['button_readmore_is_on']                = isset( $_POST['gcc-readmore-is-on'] ) && ( true === $_POST['gcc-readmore-is-on'] || 'true' === $_POST['gcc-readmore-is-on'] ) ? 'true' : 'false';
			$the_options['button_readmore_text']                 = isset( $_POST['button_readmore_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_readmore_text_field'] ) ) : 'Read More';
			$the_options['button_readmore_link_color']           = isset( $_POST['gcc-readmore-link-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-link-color'] ) ) : '#359bf5';
			$the_options['button_readmore_as_button']            = isset( $_POST['gcc-readmore-as-button'] ) && ( true === $_POST['gcc-readmore-as-button'] || 'true' === $_POST['gcc-readmore-as-button'] ) ? 'true' : 'false';
			$the_options['button_readmore_url_type']             = isset( $_POST['gcc-readmore-url-type'] ) && ( false === $_POST['gcc-readmore-url-type'] || 'false' === $_POST['gcc-readmore-url-type'] ) ? 'false' : 'true';
			$the_options['button_readmore_page']                 = isset( $_POST['gcc-readmore-page'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-page'] ) ) : '0';
			$the_options['button_readmore_wp_page']              = isset( $_POST['gcc-readmore-wp-page'] ) && ( true === $_POST['gcc-readmore-wp-page'] || 'true' === $_POST['gcc-readmore-wp-page'] ) ? 'true' : 'false';
			$the_options['button_readmore_new_win']              = isset( $_POST['gcc-readmore-new-win'] ) && ( true === $_POST['gcc-readmore-new-win'] || 'true' === $_POST['gcc-readmore-new-win'] ) ? 'true' : 'false';
			$the_options['button_readmore_url']                  = isset( $_POST['gcc-readmore-url'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-url'] ) ) : '#';
			$the_options['button_readmore_button_color']         = isset( $_POST['gcc-readmore-button-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-color'] ) ) : '#000000';
			$the_options['button_readmore_button_opacity']       = isset( $_POST['gcc-readmore-button-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-opacity'] ) ) : '1';
			$the_options['button_readmore_button_border_style']  = isset( $_POST['gcc-readmore-button-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-style'] ) ) : '1';
			$the_options['button_readmore_button_border_width']  = isset( $_POST['gcc-readmore-button-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-width'] ) ) : '0';
			$the_options['button_readmore_button_border_color']  = isset( $_POST['gcc-readmore-button-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-color'] ) ) : '#000000';
			$the_options['button_readmore_button_border_radius'] = isset( $_POST['gcc-readmore-button-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-radius'] ) ) : '0';
			$the_options['button_readmore_button_size']          = isset( $_POST['gcc-readmore-button-size'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-size'] ) ) : 'medium';
			// The below phpcs ignore comments have been added after referring competitor wordpress.org plugins.
			$the_options['header_scripts']                       = isset( $_POST['gcc-header-scripts'] ) ? wp_unslash( $_POST['gcc-header-scripts'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$the_options['body_scripts']                         = isset( $_POST['gcc-body-scripts'] ) ? wp_unslash( $_POST['gcc-body-scripts'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$the_options['footer_scripts']                       = isset( $_POST['gcc-footer-scripts'] ) ? wp_unslash( $_POST['gcc-footer-scripts'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$the_options['button_decline_is_on']                 = isset( $_POST['gcc-cookie-decline-enable'] ) && ( true === $_POST['gcc-cookie-decline-enable'] || 'true' === $_POST['gcc-cookie-decline-enable'] ) ? 'true' : 'false';
			$the_options['button_decline_text']                  = isset( $_POST['button_decline_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_decline_text_field'] ) ) : 'Decline';
			$the_options['button_decline_link_color']            = isset( $_POST['gdpr-cookie-decline-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-text-color'] ) ) : '#ffffff';
			$the_options['button_decline_as_button']             = isset( $_POST['gdpr-cookie-decline-as'] ) && ( true === $_POST['gdpr-cookie-decline-as'] || 'true' === $_POST['gdpr-cookie-decline-as'] ) ? 'true' : 'false';
			$the_options['button_decline_button_color']          = isset( $_POST['gdpr-cookie-decline-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-background-color'] ) ) : '#333333';
			$the_options['button_decline_button_opacity']        = isset( $_POST['gdpr-cookie-decline-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-opacity'] ) ) : '1';
			$the_options['button_decline_button_border_style']   = isset( $_POST['gdpr-cookie-decline-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-style'] ) ) : 'none';
			$the_options['button_decline_button_border_color']   = isset( $_POST['gdpr-cookie-decline-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-color'] ) ) : '#333333';
			$the_options['button_decline_button_border_width']   = isset( $_POST['gdpr-cookie-decline-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-width'] ) ) : '0';
			$the_options['button_decline_button_border_radius']  = isset( $_POST['gdpr-cookie-decline-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-radius'] ) ) : '0';
			$the_options['button_decline_button_size']           = isset( $_POST['gdpr-cookie-decline-size'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-size'] ) ) : 'medium';
			$the_options['button_decline_action']                = isset( $_POST['gdpr-cookie-decline-action'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-action'] ) ) : '#cookie_action_close_header_reject';
			$the_options['button_decline_url']                   = isset( $_POST['gdpr-cookie-decline-url'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-url'] ) ) : '#';
			$the_options['button_decline_new_win']               = isset( $_POST['gdpr-cookie-decline-url-new-window'] ) && ( true === $_POST['gdpr-cookie-decline-url-new-window'] || 'true' === $_POST['gdpr-cookie-decline-url-new-window'] ) ? 'true' : 'false';
			$the_options['button_settings_is_on']                = isset( $_POST['gcc-cookie-settings-enable'] ) && ( true === $_POST['gcc-cookie-settings-enable'] || 'true' === $_POST['gcc-cookie-settings-enable'] ) ? 'true' : 'false';
			$the_options['button_settings_as_popup']             = isset( $_POST['gdpr-cookie-settings-layout'] ) && ( true === $_POST['gdpr-cookie-settings-layout'] || 'true' === $_POST['gdpr-cookie-settings-layout'] ) ? 'true' : 'false';
			$the_options['button_settings_layout_skin']          = isset( $_POST['gdpr-cookie-layout-skin'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-layout-skin'] ) ) : 'layout-default';
			$the_options['button_settings_text']                 = isset( $_POST['button_settings_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_settings_text_field'] ) ) : 'Cookie Settings';
			$the_options['button_settings_link_color']           = isset( $_POST['gdpr-cookie-settings-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-text-color'] ) ) : '#ffffff';
			$the_options['button_settings_as_button']            = isset( $_POST['gdpr-cookie-settings-as'] ) && ( true === $_POST['gdpr-cookie-settings-as'] || 'true' === $_POST['gdpr-cookie-settings-as'] ) ? 'true' : 'false';
			$the_options['button_settings_button_color']         = isset( $_POST['gdpr-cookie-settings-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-background-color'] ) ) : '#333333';
			$the_options['button_settings_button_opacity']       = isset( $_POST['gdpr-cookie-settings-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-opacity'] ) ) : '1';
			$the_options['button_settings_button_border_style']  = isset( $_POST['gdpr-cookie-settings-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-style'] ) ) : 'none';
			$the_options['button_settings_button_border_color']  = isset( $_POST['gdpr-cookie-settings-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-color'] ) ) : '#333333';
			$the_options['button_settings_button_border_width']  = isset( $_POST['gdpr-cookie-settings-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-width'] ) ) : '0';
			$the_options['button_settings_button_border_radius'] = isset( $_POST['gdpr-cookie-settings-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-radius'] ) ) : '0';
			$the_options['button_settings_button_size']          = isset( $_POST['gdpr-cookie-settings-size'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-size'] ) ) : 'medium';
			$the_options['button_settings_display_cookies']      = isset( $_POST['gcc-cookie-on-frontend'] ) && ( true === $_POST['gcc-cookie-on-frontend'] || 'true' === $_POST['gcc-cookie-on-frontend'] ) ? 'true' : 'false';
			$the_options['button_confirm_text']                  = isset( $_POST['button_confirm_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_confirm_text_field'] ) ) : 'Confirm';
			$the_options['button_confirm_link_color']            = isset( $_POST['gdpr-cookie-confirm-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-text-color'] ) ) : '#ffffff';
			$the_options['button_confirm_button_color']          = isset( $_POST['gdpr-cookie-confirm-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-background-color'] ) ) : '#18a300';
			$the_options['button_confirm_button_opacity']        = isset( $_POST['gdpr-cookie-confirm-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-opacity'] ) ) : '1';
			$the_options['button_confirm_button_border_style']   = isset( $_POST['gdpr-cookie-confirm-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-style'] ) ) : 'none';
			$the_options['button_confirm_button_border_color']   = isset( $_POST['gdpr-cookie-confirm-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-color'] ) ) : '#18a300';
			$the_options['button_confirm_button_border_width']   = isset( $_POST['gdpr-cookie-confirm-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-width'] ) ) : '0';
			$the_options['button_confirm_button_border_radius']  = isset( $_POST['gdpr-cookie-confirm-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-radius'] ) ) : '0';
			$the_options['button_confirm_button_size']           = isset( $_POST['gdpr-cookie-confirm-size'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-size'] ) ) : 'medium';
			$the_options['button_cancel_text']                   = isset( $_POST['button_cancel_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_cancel_text_field'] ) ) : 'Cancel';
			$the_options['button_cancel_link_color']             = isset( $_POST['gdpr-cookie-cancel-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-text-color'] ) ) : '#ffffff';
			$the_options['button_cancel_button_color']           = isset( $_POST['gdpr-cookie-cancel-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-background-color'] ) ) : '#333333';
			$the_options['button_cancel_button_opacity']         = isset( $_POST['gdpr-cookie-cancel-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-opacity'] ) ) : '1';
			$the_options['button_cancel_button_border_style']    = isset( $_POST['gdpr-cookie-cancel-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-style'] ) ) : 'none';
			$the_options['button_cancel_button_border_color']    = isset( $_POST['gdpr-cookie-cancel-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-color'] ) ) : '#333333';
			$the_options['button_cancel_button_border_width']    = isset( $_POST['gdpr-cookie-cancel-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-width'] ) ) : '0';
			$the_options['button_cancel_button_border_radius']   = isset( $_POST['gdpr-cookie-cancel-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-radius'] ) ) : '0';
			$the_options['button_cancel_button_size']            = isset( $_POST['gdpr-cookie-cancel-size'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-size'] ) ) : 'medium';
			$the_options['button_donotsell_text']                = isset( $_POST['button_donotsell_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_donotsell_text_field'] ) ) : 'Do Not Sell My Personal Information';
			$the_options['button_donotsell_link_color']          = isset( $_POST['gdpr-cookie-opt-out-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-opt-out-text-color'] ) ) : '#359bf5';
			$the_options['button_accept_all_is_on']              = isset( $_POST['gcc-cookie-accept-all-enable'] ) && ( true === $_POST['gcc-cookie-accept-enable'] || 'true' === $_POST['gcc-cookie-accept-all-enable'] ) ? 'true' : 'false';
			$the_options['button_accept_all_text']               = isset( $_POST['button_accept_all_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_accept_all_text_field'] ) ) : 'Accept All';
			$the_options['button_accept_all_link_color']         = isset( $_POST['gdpr-cookie-accept-all-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-text-color'] ) ) : '#ffffff';
			$the_options['button_accept_all_as_button']          = isset( $_POST['gdpr-cookie-accept-all-as'] ) && ( true === $_POST['gdpr-cookie-accept-all-as'] || 'true' === $_POST['gdpr-cookie-accept-all-as'] ) ? 'true' : 'false';
			$the_options['button_accept_all_action']             = isset( $_POST['gdpr-cookie-accept-all-action'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-action'] ) ) : '#cookie_action_close_header';
			$the_options['button_accept_all_url']                = isset( $_POST['gdpr-cookie-accept-all-url'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-url'] ) ) : '#';
			$the_options['button_accept_all_new_win']            = isset( $_POST['gdpr-cookie-accept-all-new-window'] ) && ( true === $_POST['gdpr-cookie-accept-all-new-window'] || 'true' === $_POST['gdpr-cookie-accept-all-new-window'] ) ? 'true' : 'false';
			$the_options['button_accept_all_button_color']       = isset( $_POST['gdpr-cookie-accept-all-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-background-color'] ) ) : '#18a300';
			$the_options['button_accept_all_button_size']        = isset( $_POST['gdpr-cookie-accept-all-size'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-size'] ) ) : 'medium';
			$the_options['button_accept_all_btn_border_style']   = isset( $_POST['gdpr-cookie-accept-all-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-style'] ) ) : 'none';
			$the_options['button_accept_all_btn_border_color']   = isset( $_POST['gdpr-cookie-accept-all-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-color'] ) ) : '#18a300';
			$the_options['button_accept_all_btn_opacity']        = isset( $_POST['gdpr-cookie-accept-all-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-opacity'] ) ) : '1';
			$the_options['button_accept_all_btn_border_width']   = isset( $_POST['gdpr-cookie-accept-all-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-width'] ) ) : '0';
			$the_options['button_accept_all_btn_border_radius']  = isset( $_POST['gdpr-cookie-accept-all-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-radius'] ) ) : '0';
			// data reqs fields.
			$the_options['data_req_email_address'] = isset( $_POST['data_req_email_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['data_req_email_text_field'] ) ) : '';
			$the_options['data_req_subject']       = isset( $_POST['data_req_subject_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['data_req_subject_text_field'] ) ) : 'We have received your request';

			if ( ! $the_options['data_req_subject'] ) {
				$the_options['data_req_subject'] = 'We have received your request';
			}

			$the_options['data_req_editor_message'] = isset( $_POST['data_req_mail_content_text_field'] ) ? htmlentities( $_POST['data_req_mail_content_text_field'] ) : '';

			if ( $the_options['data_req_editor_message'] == '' ) {
				$the_options['data_req_editor_message'] = '&lt;p&gt;Hi {name}&lt;/p&gt;&lt;p&gt;We have received your request on {blogname}. Depending on the specific request and legal obligations we might follow-up.&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;Kind regards,&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;{blogname}&lt;/p&gt;';
			}
			// pro features to free.
			if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
				// hide banner.
				$selected_pages = array();
				$selected_pages = isset( $_POST['gcc-selected-pages'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-selected-pages'] ) ) ) : '';
				// storing id of pages in database.
				$the_options['select_pages'] = $selected_pages;
			}
			if ( ! get_option( 'wpl_pro_active' ) ) {
				// script blocker.
				$the_options['is_script_blocker_on'] = isset( $_POST['gcc-script-blocker-on'] ) && ( true === $_POST['gcc-script-blocker-on'] || 'true' === $_POST['gcc-script-blocker-on'] ) ? 'true' : 'false';
				// enable safe mode.
				$the_options['enable_safe'] = isset( $_POST['gcc-enable-safe'] ) && ( true === $_POST['gcc-enable-safe'] || 'true' === $_POST['gcc-enable-safe'] ) ? 'true' : 'false';
				// consent log.
				$the_options['logging_on'] = isset( $_POST['gcc-logging-on'] ) && ( true === $_POST['gcc-logging-on'] || 'true' === $_POST['gcc-logging-on'] ) ? 'true' : 'false';
				// consent forwarding.
				$selected_sites                 = array();
				$selected_sites                 = isset( $_POST['gcc-selected-sites'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-selected-sites'] ) ) ) : '';
				$the_options['consent_forward'] = isset( $_POST['gcc-consent-forward'] ) && ( true === $_POST['gcc-consent-forward'] || 'true' === $_POST['gcc-consent-forward'] ) ? 'true' : 'false';
				$the_options['select_sites']    = $selected_sites;
				// For EU.
				if ( isset( $_POST['gcc-eu-enable'] ) ) {
					if ( 'no' === $_POST['gcc-eu-enable'] ) {
						$the_options['is_eu_on'] = 'false';
					} elseif ( 'false' == $_POST['gcc-eu-enable'] ) {
						$the_options['is_eu_on'] = 'false';
					} else {
						$the_options['is_eu_on'] = 'true';
					}
				}
				// For CCPA.
				if ( isset( $_POST['gcc-ccpa-enable'] ) ) {
					if ( 'no' === $_POST['gcc-ccpa-enable'] ) {
						$the_options['is_ccpa_on'] = 'false';
					} elseif ( 'false' == $_POST['gcc-ccpa-enable'] ) {
						$the_options['is_ccpa_on'] = 'false';
					} else {
						$the_options['is_ccpa_on'] = 'true';
					}
				}
				if ( isset( $the_options['cookie_usage_for'] ) ) {
					switch ( $the_options['cookie_usage_for'] ) {
						case 'both':
						case 'gdpr':
						case 'lgpd':
						case 'eprivacy':
							update_option( 'wpl_bypass_script_blocker', 0 );
							break;
						case 'ccpa':
							update_option( 'wpl_bypass_script_blocker', 1 );
							break;
					}
				}
			}
			if ( ! get_option( 'wpl_pro_active' ) ) {

				$saved_options                  = get_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
				$the_options['banner_template'] = isset( $_POST['gdpr-banner-template'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-banner-template'] ) ) : 'banner-default';

				$the_options['popup_template'] = isset( $_POST['gdpr-popup-template'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-popup-template'] ) ) : 'popup-default';

				$the_options['widget_template'] = isset( $_POST['gdpr-widget-template'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-widget-template'] ) ) : 'widget-default';

				$template      = isset( $_POST['gdpr-template'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-template'] ) ) : 'none';
				$cookie_bar_as = $the_options['cookie_bar_as'];
				if ( 'none' !== $template && $saved_options['template'] !== $template ) {
					$the_options[ $cookie_bar_as . '_template' ] = $template;
					$the_options['template']                     = $template;
					$template_parts                              = explode( '-', $template );
					$template                                    = array_pop( $template_parts );
					$templates                                   = apply_filters( 'gdpr_get_templates', $cookie_bar_as );
					$template                                    = $templates[ $template ];
					$the_options['text']                         = $template['color'];
					$the_options['background']                   = $template['background_color'];
					$the_options['opacity']                      = $template['opacity'];
					$the_options['background_border_style']      = $template['border_style'];
					$the_options['background_border_width']      = $template['border_width'];
					$the_options['background_border_color']      = $template['border_color'];
					$the_options['background_border_radius']     = $template['border_radius'];
					if ( isset( $template['accept'] ) ) {
						$the_options['button_accept_is_on']     = true;
						$the_options['button_accept_all_is_on'] = false;
						if ( $template['accept']['as_button'] ) {
							$the_options['button_accept_as_button']             = $template['accept']['as_button'];
							$the_options['button_accept_button_color']          = $template['accept']['button_color'];
							$the_options['button_accept_button_opacity']        = $template['accept']['button_opacity'];
							$the_options['button_accept_button_border_style']   = $template['accept']['button_border_style'];
							$the_options['button_accept_button_border_width']   = $template['accept']['button_border_width'];
							$the_options['button_accept_button_border_color']   = $template['accept']['button_border_color'];
							$the_options['button_accept_button_border_radius']  = $template['accept']['button_border_radius'];
							$the_options['button_accept_button_size']           = $template['accept']['button_size'];
							$the_options['button_accept_all_as_button']         = $template['accept']['as_button'];
							$the_options['button_accept_all_button_color']      = $template['accept']['button_color'];
							$the_options['button_accept_all_btn_opacity']       = $template['accept']['button_opacity'];
							$the_options['button_accept_all_btn_border_style']  = $template['accept']['button_border_style'];
							$the_options['button_accept_all_btn_border_width']  = $template['accept']['button_border_width'];
							$the_options['button_accept_all_btn_border_color']  = $template['accept']['button_border_color'];
							$the_options['button_accept_all_btn_border_radius'] = $template['accept']['button_border_radius'];
							$the_options['button_accept_all_button_size']       = $template['accept']['button_size'];
						} else {
							$the_options['button_accept_as_button']     = false;
							$the_options['button_accept_all_as_button'] = false;
						}
						$the_options['button_accept_link_color']     = $template['accept']['link_color'];
						$the_options['button_accept_all_link_color'] = $template['accept']['link_color'];
					} else {
						$the_options['button_accept_is_on']     = false;
						$the_options['button_accept_all_is_on'] = false;
					}
					if ( isset( $template['decline'] ) ) {
						$the_options['button_decline_is_on'] = true;
						if ( $template['decline']['as_button'] ) {
							$the_options['button_decline_as_button']            = $template['decline']['as_button'];
							$the_options['button_decline_button_color']         = $template['decline']['button_color'];
							$the_options['button_decline_button_opacity']       = $template['decline']['button_opacity'];
							$the_options['button_decline_button_border_style']  = $template['decline']['button_border_style'];
							$the_options['button_decline_button_border_width']  = $template['decline']['button_border_width'];
							$the_options['button_decline_button_border_color']  = $template['decline']['button_border_color'];
							$the_options['button_decline_button_border_radius'] = $template['decline']['button_border_radius'];
							$the_options['button_decline_button_size']          = $template['decline']['button_size'];
						} else {
							$the_options['button_decline_as_button'] = false;
						}
						$the_options['button_decline_link_color'] = $template['decline']['link_color'];
					} else {
						$the_options['button_decline_is_on'] = false;
					}
					if ( isset( $template['settings'] ) ) {
						$the_options['button_settings_is_on'] = true;
						if ( $template['settings']['as_button'] ) {
							$the_options['button_settings_as_button']            = $template['settings']['as_button'];
							$the_options['button_settings_button_color']         = $template['settings']['button_color'];
							$the_options['button_settings_button_opacity']       = $template['settings']['button_opacity'];
							$the_options['button_settings_button_border_style']  = $template['settings']['button_border_style'];
							$the_options['button_settings_button_border_width']  = $template['settings']['button_border_width'];
							$the_options['button_settings_button_border_color']  = $template['settings']['button_border_color'];
							$the_options['button_settings_button_border_radius'] = $template['settings']['button_border_radius'];
							$the_options['button_settings_button_size']          = $template['settings']['button_size'];
						} else {
							$the_options['button_settings_as_button'] = false;
						}
						$the_options['button_settings_link_color'] = $template['settings']['link_color'];
					} else {
						$the_options['button_settings_is_on'] = false;
					}
					if ( isset( $template['readmore'] ) ) {
						$the_options['button_readmore_is_on'] = true;
						if ( $template['readmore']['as_button'] ) {
							$the_options['button_readmore_as_button']            = $template['readmore']['as_button'];
							$the_options['button_readmore_button_color']         = $template['readmore']['button_color'];
							$the_options['button_readmore_button_opacity']       = $template['readmore']['button_opacity'];
							$the_options['button_readmore_button_border_style']  = $template['readmore']['button_border_style'];
							$the_options['button_readmore_button_border_width']  = $template['readmore']['button_border_width'];
							$the_options['button_readmore_button_border_color']  = $template['readmore']['button_border_color'];
							$the_options['button_readmore_button_border_radius'] = $template['readmore']['button_border_radius'];
							$the_options['button_readmore_button_size']          = $template['readmore']['button_size'];
						} else {
							$the_options['button_readmore_as_button'] = false;
						}
						$the_options['button_readmore_link_color'] = $template['readmore']['link_color'];
					} else {
						$the_options['button_readmore_is_on'] = false;
					}
					if ( isset( $template['confirm'] ) ) {
						$the_options['button_confirm_button_color']         = $template['confirm']['button_color'];
						$the_options['button_confirm_button_opacity']       = $template['confirm']['button_opacity'];
						$the_options['button_confirm_button_border_style']  = $template['confirm']['button_border_style'];
						$the_options['button_confirm_button_border_width']  = $template['confirm']['button_border_width'];
						$the_options['button_confirm_button_border_color']  = $template['confirm']['button_border_color'];
						$the_options['button_confirm_button_border_radius'] = $template['confirm']['button_border_radius'];
						$the_options['button_confirm_button_size']          = $template['confirm']['button_size'];
						$the_options['button_confirm_link_color']           = $template['confirm']['link_color'];
					}
					if ( isset( $template['cancel'] ) ) {
						$the_options['button_cancel_button_color']         = $template['cancel']['button_color'];
						$the_options['button_cancel_button_opacity']       = $template['cancel']['button_opacity'];
						$the_options['button_cancel_button_border_style']  = $template['cancel']['button_border_style'];
						$the_options['button_cancel_button_border_width']  = $template['cancel']['button_border_width'];
						$the_options['button_cancel_button_border_color']  = $template['cancel']['button_border_color'];
						$the_options['button_cancel_button_border_radius'] = $template['cancel']['button_border_radius'];
						$the_options['button_cancel_button_size']          = $template['cancel']['button_size'];
						$the_options['button_cancel_link_color']           = $template['cancel']['link_color'];
					}
					if ( isset( $template['donotsell'] ) ) {
						$the_options['button_donotsell_link_color'] = $template['donotsell']['link_color'];
					}
					if ( isset( $template['layout'] ) ) {
						$the_options['button_settings_as_popup']    = true;
						$the_options['button_settings_layout_skin'] = 'layout-' . $template['layout'];
					}
				}
			}
			// restrict posts when gpdr free is activated.
			$restricted_posts              = array();
			$restricted_posts              = isset( $_POST['gcc-restrict-posts'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-restrict-posts'] ) ) ) : '';
			$the_options['restrict_posts'] = $restricted_posts;

			if ( get_option( 'wpl_pro_active' ) && get_option( 'wc_am_client_wpl_cookie_consent_activated' ) && 'Activated' === get_option( 'wc_am_client_wpl_cookie_consent_activated' ) ) {
				$saved_options    = get_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
				$restricted_posts = array();
				$restricted_posts = isset( $_POST['gcc-restrict-posts'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-restrict-posts'] ) ) ) : '';
				if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
					// hide banner.
					$selected_pages = array();
					$selected_pages = isset( $_POST['gcc-selected-pages'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-selected-pages'] ) ) ) : '';
					// storing id of pages in database.
					$the_options['select_pages'] = $selected_pages;
				}
				// consent forward .
				$selected_sites                      = array();
				$selected_sites                      = isset( $_POST['gcc-selected-sites'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-selected-sites'] ) ) ) : '';
				$the_options['is_eu_on']             = isset( $_POST['gcc-eu-enable'] ) && ( true === $_POST['gcc-eu-enable'] || 'true' === $_POST['gcc-eu-enable'] ) ? 'true' : 'false';
				$the_options['is_ccpa_on']           = isset( $_POST['gcc-ccpa-enable'] ) && ( true === $_POST['gcc-ccpa-enable'] || 'true' === $_POST['gcc-ccpa-enable'] ) ? 'true' : 'false';
				$the_options['logging_on']           = isset( $_POST['gcc-logging-on'] ) && ( true === $_POST['gcc-logging-on'] || 'true' === $_POST['gcc-logging-on'] ) ? 'true' : 'false';
				$the_options['enable_safe']          = isset( $_POST['gcc-enable-safe'] ) && ( true === $_POST['gcc-enable-safe'] || 'true' === $_POST['gcc-enable-safe'] ) ? 'true' : 'false';
				$the_options['banner_template']      = isset( $_POST['gdpr-banner-template'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-banner-template'] ) ) : 'banner-default';
				$the_options['popup_template']       = isset( $_POST['gdpr-popup-template'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-popup-template'] ) ) : 'popup-default';
				$the_options['widget_template']      = isset( $_POST['gdpr-widget-template'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-widget-template'] ) ) : 'widget-default';
				$the_options['is_script_blocker_on'] = isset( $_POST['gcc-script-blocker-on'] ) && ( true === $_POST['gcc-script-blocker-on'] || 'true' === $_POST['gcc-script-blocker-on'] ) ? 'true' : 'false';
				$the_options['restrict_posts']       = $restricted_posts;
				// consent forward .
				$the_options['consent_forward'] = isset( $_POST['gcc-consent-forward'] ) && ( true === $_POST['gcc-consent-forward'] || 'true' === $_POST['gcc-consent-forward'] ) ? 'true' : 'false';
				$the_options['select_sites']    = $selected_sites;
				if ( isset( $the_options['cookie_usage_for'] ) ) {
					switch ( $the_options['cookie_usage_for'] ) {
						case 'both':
						case 'gdpr':
						case 'lgpd':
						case 'eprivacy':
							update_option( 'wpl_bypass_script_blocker', 0 );
							break;
						case 'ccpa':
							update_option( 'wpl_bypass_script_blocker', 1 );
							break;
					}
				}
				$template      = isset( $_POST['gdpr-template'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-template'] ) ) : 'none';
				$cookie_bar_as = $the_options['cookie_bar_as'];
				if ( 'none' !== $template && $saved_options['template'] !== $template ) {
					$the_options[ $cookie_bar_as . '_template' ] = $template;
					$the_options['template']                     = $template;
					$template_parts                              = explode( '-', $template );
					$template                                    = array_pop( $template_parts );
					$templates                                   = apply_filters( 'gdpr_get_templates', $cookie_bar_as );
					$template                                    = $templates[ $template ];
					$the_options['text']                         = $template['color'];
					$the_options['background']                   = $template['background_color'];
					$the_options['opacity']                      = $template['opacity'];
					$the_options['background_border_style']      = $template['border_style'];
					$the_options['background_border_width']      = $template['border_width'];
					$the_options['background_border_color']      = $template['border_color'];
					$the_options['background_border_radius']     = $template['border_radius'];
					if ( isset( $template['accept'] ) ) {
						$the_options['button_accept_is_on']     = true;
						$the_options['button_accept_all_is_on'] = false;
						if ( $template['accept']['as_button'] ) {
							$the_options['button_accept_as_button']             = $template['accept']['as_button'];
							$the_options['button_accept_button_color']          = $template['accept']['button_color'];
							$the_options['button_accept_button_opacity']        = $template['accept']['button_opacity'];
							$the_options['button_accept_button_border_style']   = $template['accept']['button_border_style'];
							$the_options['button_accept_button_border_width']   = $template['accept']['button_border_width'];
							$the_options['button_accept_button_border_color']   = $template['accept']['button_border_color'];
							$the_options['button_accept_button_border_radius']  = $template['accept']['button_border_radius'];
							$the_options['button_accept_button_size']           = $template['accept']['button_size'];
							$the_options['button_accept_all_as_button']         = $template['accept']['as_button'];
							$the_options['button_accept_all_button_color']      = $template['accept']['button_color'];
							$the_options['button_accept_all_btn_opacity']       = $template['accept']['button_opacity'];
							$the_options['button_accept_all_btn_border_style']  = $template['accept']['button_border_style'];
							$the_options['button_accept_all_btn_border_width']  = $template['accept']['button_border_width'];
							$the_options['button_accept_all_btn_border_color']  = $template['accept']['button_border_color'];
							$the_options['button_accept_all_btn_border_radius'] = $template['accept']['button_border_radius'];
							$the_options['button_accept_all_button_size']       = $template['accept']['button_size'];
						} else {
							$the_options['button_accept_as_button']     = false;
							$the_options['button_accept_all_as_button'] = false;
						}
						$the_options['button_accept_link_color']     = $template['accept']['link_color'];
						$the_options['button_accept_all_link_color'] = $template['accept']['link_color'];
					} else {
						$the_options['button_accept_is_on']     = false;
						$the_options['button_accept_all_is_on'] = false;
					}
					if ( isset( $template['decline'] ) ) {
						$the_options['button_decline_is_on'] = true;
						if ( $template['decline']['as_button'] ) {
							$the_options['button_decline_as_button']            = $template['decline']['as_button'];
							$the_options['button_decline_button_color']         = $template['decline']['button_color'];
							$the_options['button_decline_button_opacity']       = $template['decline']['button_opacity'];
							$the_options['button_decline_button_border_style']  = $template['decline']['button_border_style'];
							$the_options['button_decline_button_border_width']  = $template['decline']['button_border_width'];
							$the_options['button_decline_button_border_color']  = $template['decline']['button_border_color'];
							$the_options['button_decline_button_border_radius'] = $template['decline']['button_border_radius'];
							$the_options['button_decline_button_size']          = $template['decline']['button_size'];
						} else {
							$the_options['button_decline_as_button'] = false;
						}
						$the_options['button_decline_link_color'] = $template['decline']['link_color'];
					} else {
						$the_options['button_decline_is_on'] = false;
					}
					if ( isset( $template['settings'] ) ) {
						$the_options['button_settings_is_on'] = true;
						if ( $template['settings']['as_button'] ) {
							$the_options['button_settings_as_button']            = $template['settings']['as_button'];
							$the_options['button_settings_button_color']         = $template['settings']['button_color'];
							$the_options['button_settings_button_opacity']       = $template['settings']['button_opacity'];
							$the_options['button_settings_button_border_style']  = $template['settings']['button_border_style'];
							$the_options['button_settings_button_border_width']  = $template['settings']['button_border_width'];
							$the_options['button_settings_button_border_color']  = $template['settings']['button_border_color'];
							$the_options['button_settings_button_border_radius'] = $template['settings']['button_border_radius'];
							$the_options['button_settings_button_size']          = $template['settings']['button_size'];
						} else {
							$the_options['button_settings_as_button'] = false;
						}
						$the_options['button_settings_link_color'] = $template['settings']['link_color'];
					} else {
						$the_options['button_settings_is_on'] = false;
					}
					if ( isset( $template['readmore'] ) ) {
						$the_options['button_readmore_is_on'] = true;
						if ( $template['readmore']['as_button'] ) {
							$the_options['button_readmore_as_button']            = $template['readmore']['as_button'];
							$the_options['button_readmore_button_color']         = $template['readmore']['button_color'];
							$the_options['button_readmore_button_opacity']       = $template['readmore']['button_opacity'];
							$the_options['button_readmore_button_border_style']  = $template['readmore']['button_border_style'];
							$the_options['button_readmore_button_border_width']  = $template['readmore']['button_border_width'];
							$the_options['button_readmore_button_border_color']  = $template['readmore']['button_border_color'];
							$the_options['button_readmore_button_border_radius'] = $template['readmore']['button_border_radius'];
							$the_options['button_readmore_button_size']          = $template['readmore']['button_size'];
						} else {
							$the_options['button_readmore_as_button'] = false;
						}
						$the_options['button_readmore_link_color'] = $template['readmore']['link_color'];
					} else {
						$the_options['button_readmore_is_on'] = false;
					}
					if ( isset( $template['confirm'] ) ) {
						$the_options['button_confirm_button_color']         = $template['confirm']['button_color'];
						$the_options['button_confirm_button_opacity']       = $template['confirm']['button_opacity'];
						$the_options['button_confirm_button_border_style']  = $template['confirm']['button_border_style'];
						$the_options['button_confirm_button_border_width']  = $template['confirm']['button_border_width'];
						$the_options['button_confirm_button_border_color']  = $template['confirm']['button_border_color'];
						$the_options['button_confirm_button_border_radius'] = $template['confirm']['button_border_radius'];
						$the_options['button_confirm_button_size']          = $template['confirm']['button_size'];
						$the_options['button_confirm_link_color']           = $template['confirm']['link_color'];
					}
					if ( isset( $template['cancel'] ) ) {
						$the_options['button_cancel_button_color']         = $template['cancel']['button_color'];
						$the_options['button_cancel_button_opacity']       = $template['cancel']['button_opacity'];
						$the_options['button_cancel_button_border_style']  = $template['cancel']['button_border_style'];
						$the_options['button_cancel_button_border_width']  = $template['cancel']['button_border_width'];
						$the_options['button_cancel_button_border_color']  = $template['cancel']['button_border_color'];
						$the_options['button_cancel_button_border_radius'] = $template['cancel']['button_border_radius'];
						$the_options['button_cancel_button_size']          = $template['cancel']['button_size'];
						$the_options['button_cancel_link_color']           = $template['cancel']['link_color'];
					}
					if ( isset( $template['donotsell'] ) ) {
						$the_options['button_donotsell_link_color'] = $template['donotsell']['link_color'];
					}
					if ( isset( $template['layout'] ) ) {
						$the_options['button_settings_as_popup']    = true;
						$the_options['button_settings_layout_skin'] = 'layout-' . $template['layout'];
					}
				}
			}
			// language translation based on the selected language.
			if ( $_POST['lang_changed'] == 'true' && isset( $_POST['select-banner-lan'] ) && in_array( $_POST['select-banner-lan'], $this->supported_languages ) ) {  //phpcs:ignore

				// Load and decode translations from JSON file.
				$translations_file = plugin_dir_path( __FILE__ ) . 'translations/translations.json';
				$translations      = json_decode( file_get_contents( $translations_file ), true );

				// Define an array of text keys to translate.
				$text_keys_to_translate = array(
					'dash_notify_message_eprivacy',
					'dash_notify_message_lgpd',
					'dash_button_readmore_text',
					'dash_button_accept_text',
					'dash_button_accept_all_text',
					'dash_button_decline_text',
					'dash_about_message',
					'dash_about_message_lgpd',
					'dash_notify_message',
					'dash_button_settings_text',
					'dash_notify_message_ccpa',
					'dash_button_donotsell_text',
					'dash_button_confirm_text',
					'dash_button_cancel_text',
					'dash_show_again_text',
					'dash_optout_text',
					'gdpr_cookie_category_description_necessary',
					'gdpr_cookie_category_name_necessary',
					'gdpr_cookie_category_description_analytics',
					'gdpr_cookie_category_name_analytics',
					'gdpr_cookie_category_description_marketing',
					'gdpr_cookie_category_description_preference',
					'gdpr_cookie_category_description_unclassified',
					'gdpr_cookie_category_name_marketing',
					'gdpr_cookie_category_name_preference',
					'gdpr_cookie_category_name_unclassified',
				);

				// Determine the target language based on the POST value.
				$target_language = $_POST['select-banner-lan'];   //phpcs:ignore
				// Initialize arrays to store translated category descriptions and names.
				$translated_category_descriptions = array();
				$translated_category_names        = array();
				// Loop through the text keys and translate them.
				foreach ( $text_keys_to_translate as $text_key ) {
					$translated_text                 = $this->translated_text( $text_key, $translations, $target_language );
					$stripped_string                 = str_replace( 'dash_', '', $text_key );
					$the_options[ $stripped_string ] = $translated_text;

					// Check if the current text key is for category description or category name.
					if ( 'gdpr_cookie_category_description_necessary' === $text_key ) {
						$translated_category_description_necessary = $translated_text;
					} elseif ( 'gdpr_cookie_category_description_analytics' === $text_key ) {
						$translated_category_description_analytics = $translated_text;
					} elseif ( 'gdpr_cookie_category_description_marketing' === $text_key ) {
						$translated_category_description_marketing = $translated_text;
					} elseif ( 'gdpr_cookie_category_description_preference' === $text_key ) {
						$translated_category_description_preferences = $translated_text;
					} elseif ( 'gdpr_cookie_category_description_unclassified' === $text_key ) {
						$translated_category_description_unclassified = $translated_text;
					} elseif ( 'gdpr_cookie_category_name_analytics' === $text_key ) {
						$translated_category_name_analytics = $translated_text;
					} elseif ( 'gdpr_cookie_category_name_marketing' === $text_key ) {
						$translated_category_name_marketing = $translated_text;
					} elseif ( 'gdpr_cookie_category_name_necessary' === $text_key ) {
						$translated_category_name_necessary = $translated_text;
					} elseif ( 'gdpr_cookie_category_name_preference' === $text_key ) {
						$translated_category_name_preferences = $translated_text;
					} elseif ( 'gdpr_cookie_category_name_unclassified' === $text_key ) {
						$translated_category_name_unclassified = $translated_text;
					}
				}

				// non dynaminc text for the cookie settings.
				global $wpdb;
				$cat_table  = $wpdb->prefix . $this->category_table;
				$categories = $this->gdpr_get_categories();
				$cat_arr    = array();

				$translated_category_descriptions = array(
					1 => $translated_category_description_analytics,
					2 => $translated_category_description_marketing,
					3 => $translated_category_description_necessary,
					4 => $translated_category_description_preferences,
					5 => $translated_category_description_unclassified,
				);
				$translated_category_names        = array(
					1 => $translated_category_name_analytics,
					2 => $translated_category_name_marketing,
					3 => $translated_category_name_necessary,
					4 => $translated_category_name_preferences,
					5 => $translated_category_name_unclassified,
				);

				if ( ! empty( $categories ) ) {
					foreach ( $categories as $category ) {
						$cat_description = isset( $category['description'] ) ? addslashes( $category['description'] ) : '';
						$cat_category    = isset( $category['name'] ) ? $category['name'] : '';
						$cat_slug        = isset( $category['slug'] ) ? $category['slug'] : '';

						// Check if the category has a translation available.
						$category_i_d = -1;
						switch ( $cat_category ) {
							case 'Analytics':
								$category_i_d = 1;
								break;
							case 'Marketing':
								$category_i_d = 2;
								break;
							case 'Necessary':
								$category_i_d = 3;
								break;
							case 'Preferences':
								$category_i_d = 4;
								break;
							case 'Unclassified':
								$category_i_d = 5;
								break;
						}

						if ( -1 != $category_i_d ) {
							// Update the table with the translated values.
							$wpdb->query(
								$wpdb->prepare(
									'UPDATE `' . $wpdb->prefix . 'gdpr_cookie_scan_categories`
									SET `gdpr_cookie_category_description` = %s,
										`gdpr_cookie_category_name` = %s
									WHERE `id_gdpr_cookie_category` = %d',
									$translated_category_descriptions[ $category_i_d ],
									$translated_category_names[ $category_i_d ],
									$category_i_d
								)
							);
						}
					}
				}
			}

			// Set consent renew to all the users when consent renew is enabled.

			if ( $the_options['consent_renew_enable'] ) {
				global $wpdb;
				$meta_key_cl_ip            = '_wplconsentlogs_ip';
				$meta_key_cl_renew_consent = '_wpl_renew_consent';

				// Find posts with _wplconsentlogs_ip meta key.
				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT pm1.post_id, pm1.meta_value AS ip_value, pm2.meta_value AS consent_value
						FROM {$wpdb->prefix}postmeta AS pm1
						LEFT JOIN {$wpdb->prefix}postmeta AS pm2 ON pm1.post_id = pm2.post_id
						WHERE pm1.meta_key = %s",
						$meta_key_cl_ip
					)
				);

				if ( $results ) {
					foreach ( $results as $result ) {
						$post_id       = $result->post_id;
						$consent_value = $the_options['consent_renew_enable'];

						// Check if _wpl_renew_consent meta key exists, and add if it doesn't.
						if ( ! get_post_meta( $post_id, $meta_key_cl_renew_consent, true ) ) {
							add_post_meta( $post_id, $meta_key_cl_renew_consent, $consent_value, true );
						} else {
							// Update _wpl_renew_consent meta value if it exists.
							update_post_meta( $post_id, $meta_key_cl_renew_consent, $consent_value );
						}
					}
				}

				$option_name     = 'wpl_consent_timestamp';
				$timestamp_value = time();

				// Check if the option already exists.
				if ( false === get_option( $option_name ) ) {
					// If it doesn't exist, add the option.
					add_option( $option_name, $timestamp_value );
				} else {
					// If it exists, update the option.
					update_option( $option_name, $timestamp_value );
				}

				// make renew consent false once done.

				$the_options['consent_renew_enable'] = 'false';
			}

			if ( isset( $_POST['logo_removed'] ) && 'true' == $_POST['logo_removed'] ) {
				update_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD, '' );
			} elseif ( isset( $_POST['gdpr-cookie-bar-logo-url-holder'] ) && ! empty( $_POST['gdpr-cookie-bar-logo-url-holder'] ) ) {
				// Update the option if a new logo is provided.
				update_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD, esc_url_raw( wp_unslash( $_POST['gdpr-cookie-bar-logo-url-holder'] ) ) );
			}
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
			wp_send_json_success( array( 'form_options_saved' => true ) );
		}
	}

	/**
	 * Callback function for Wizard.
	 */
	public function gdpr_cookie_consent_wizard() {

		$is_pro = get_option( 'wpl_pro_active', false );

		wp_enqueue_script( $this->plugin_name );
		wp_enqueue_script( $this->plugin_name . '-vue' );
		wp_enqueue_script( $this->plugin_name . '-mascot' );
		wp_enqueue_style( $this->plugin_name . '-select2' );
		wp_enqueue_script( $this->plugin_name . '-select2' );

		wp_localize_script(
			$this->plugin_name . '-mascot',
			'mascot_obj',
			array(
				'is_pro'            => $is_pro,
				'documentation_url' => 'https://club.wpeka.com/docs/wp-cookie-consent/',
				'faq_url'           => 'https://club.wpeka.com/docs/wp-cookie-consent/',
				// 'support_url'       => $support_url,
				'upgrade_url'       => 'https://club.wpeka.com/product/wp-gdpr-cookie-consent/?utm_source=plugin&utm_medium=gdpr&utm_campaign=help-mascot_&utm_content=upgrade-to-pro',
			)
		);

		// Lock out non-admins.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_attr__( 'You do not have sufficient permission to perform this operation', 'gdpr-cookie-consent' ) );
		}
		// Get options.
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		// Check if form has been set.
		if ( isset( $_POST['update_admin_settings_form'] ) || ( isset( $_POST['gdpr_settings_ajax_update'] ) ) ) {
			// Check nonce.
			check_admin_referer( 'gdprcookieconsent-update-' . GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
			if ( 'update_admin_settings_form' === $_POST['gdpr_settings_ajax_update'] ) {
				// module settings saving hook.
				do_action( 'gdpr_module_save_settings' );
				// setting manually default value for restrict posts field.
				if ( ! isset( $_POST['restrict_posts_field'] ) ) {
					$_POST['restrict_posts_field'] = array();
				}
				foreach ( $the_options as $key => $value ) {
					if ( isset( $_POST[ $key . '_field' ] ) ) {
						// Store sanitised values only.
						$the_options[ $key ] = Gdpr_Cookie_Consent::gdpr_sanitise_settings( $key, wp_unslash( $_POST[ $key . '_field' ] ) ); // phpcs:ignore
					}
				}
				switch ( $the_options['cookie_bar_as'] ) {
					case 'banner':
						$the_options['template'] = $the_options['banner_template'];
						break;
					case 'popup':
						$the_options['template'] = $the_options['popup_template'];
						break;
					case 'widget':
						$the_options['template'] = $the_options['widget_template'];
						break;
				}
				$the_options = apply_filters( 'gdpr_module_after_save_settings', $the_options );
				update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
				echo '<div class="updated"><p><strong>' . esc_attr__( 'Settings Updated.', 'gdpr-cookie-consent' ) . '</strong></p></div>';
			}
		}
		if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) ) === 'xmlhttprequest' ) {
			exit();
		}
		if ( get_option( 'wpl_pro_active' ) && '1' === get_option( 'wpl_pro_active' ) && ( ! get_option( 'wpl_pro_version_number' ) || version_compare( get_option( 'wpl_pro_version_number' ), '2.9.0', '<' ) ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'partials/gdpr-cookie-consent-admin-display.php';
			return;
		}
		$settings        = Gdpr_Cookie_Consent::gdpr_get_settings();
		$gdpr_policies   = self::get_cookie_usage_for_options();
		$policies_length = count( $gdpr_policies );
		$policy_keys     = array_keys( $gdpr_policies );
		$policies        = array();
		$is_pro_active   = get_option( 'wpl_pro_active' );
		for ( $i = 0; $i < $policies_length; $i++ ) {
			$policies[ $i ] = array(
				'label' => $policy_keys[ $i ],
				'code'  => $gdpr_policies[ $policy_keys[ $i ] ],
			);
		}
		$cookie_durations        = self::get_cookie_expiry_options();
		$cookie_durations_length = count( $cookie_durations );
		$cookie_expiry_keys      = array_keys( $cookie_durations );
		$cookie_expiry_options   = array();
		for ( $i = 0; $i < $cookie_durations_length; $i++ ) {
			$cookie_expiry_options[ $i ] = array(
				'label' => $cookie_expiry_keys[ $i ],
				'code'  => $cookie_durations[ $cookie_expiry_keys[ $i ] ],
			);
		}
		$position_options           = array();
		$position_options[0]        = array(
			'label' => 'Top',
			'code'  => 'top',
		);
		$position_options[1]        = array(
			'label' => 'Bottom',
			'code'  => 'bottom',
		);
		$widget_position_options    = array();
		$widget_position_options[0] = array(
			'label' => 'Botton Left',
			'code'  => 'left',
		);
		$widget_position_options[1] = array(
			'label' => 'Bottom Right',
			'code'  => 'right',
		);
		$widget_position_options[2] = array(
			'label' => 'Top Left',
			'code'  => 'top_left',
		);
		$widget_position_options[3] = array(
			'label' => 'Top Right',
			'code'  => 'top_right',
		);

		$show_cookie_as_options    = array();
		$show_cookie_as_options[0] = array(
			'label' => 'Banner',
			'code'  => 'banner',
		);
		$show_cookie_as_options[1] = array(
			'label' => 'Popup',
			'code'  => 'popup',
		);
		$show_cookie_as_options[2] = array(
			'label' => 'Widget',
			'code'  => 'widget',
		);
		$show_language_as_options  = array();
		$show_language_as_options  = array(
			array(
				'label' => 'Bulgarian',
				'code'  => 'bg',
			),
			array(
				'label' => 'Croatian',
				'code'  => 'hr',
			),
			array(
				'label' => 'Czech',
				'code'  => 'cs',
			),
			array(
				'label' => 'Danish',
				'code'  => 'da',
			),
			array(
				'label' => 'Dutch',
				'code'  => 'nl',
			),
			array(
				'label' => 'English',
				'code'  => 'en',
			),
			array(
				'label' => 'French',
				'code'  => 'fr',
			),
			array(
				'label' => 'German',
				'code'  => 'de',
			),
			array(
				'label' => 'Greek',
				'code'  => 'gr',
			),
			array(
				'label' => 'Hungarian',
				'code'  => 'hu',
			),
			array(
				'label' => 'Icelandic',
				'code'  => 'is',
			),
			array(
				'label' => 'Polish',
				'code'  => 'po',
			),
			array(
				'label' => 'Slovenian',
				'code'  => 'sl',
			),
			array(
				'label' => 'Spanish',
				'code'  => 'es',
			),
		);

		// dropdown option for schedule scan.
		$schedule_scan_options    = array();
		$schedule_scan_options[0] = array(
			'label' => 'Never',
			'code'  => 'never',
		);
		$schedule_scan_options[1] = array(
			'label' => 'Only Once',
			'code'  => 'once',
		);
		$schedule_scan_options[2] = array(
			'label' => 'Monthly',
			'code'  => 'monthly',
		);
		// dropdown option for schedule scan day.
		$schedule_scan_day_options = array();

		for ( $day = 0; $day < 31; $day++ ) {
			$label = 'Day ' . ( $day + 1 );
			$code  = 'Day ' . ( $day + 1 );

			$schedule_scan_day_options[] = array(
				'label' => $label,
				'code'  => $code,
			);
		}
		$on_hide_options         = array();
		$on_hide_options[0]      = array(
			'label' => 'Animate',
			'code'  => true,
		);
		$on_hide_options[1]      = array(
			'label' => 'Disappear',
			'code'  => false,
		);
		$on_load_options         = array();
		$on_load_options[0]      = array(
			'label' => 'Animate',
			'code'  => true,
		);
		$on_load_options[1]      = array(
			'label' => 'Sticky',
			'code'  => false,
		);
		$tab_position_options    = array();
		$tab_position_options[0] = array(
			'label' => 'Left',
			'code'  => 'left',
		);
		$tab_position_options[1] = array(
			'label' => 'Right',
			'code'  => 'right',
		);
		$posts_list              = get_posts();
		$pages_list              = get_pages();
		$list_of_contents        = array();
		$index                   = 0;
		foreach ( $posts_list as $post ) {
			$list_of_contents[ $index ] = array(
				'label' => $post->post_title,
				'code'  => $post->ID,
			);
			++$index;
		}
		foreach ( $pages_list as $page ) {
			$list_of_contents[ $index ] = array(
				'label' => $page->post_title,
				'code'  => $page->ID,
			);
			++$index;
		}
		// pages for hide banner.
		$list_of_pages = array();
		$indx          = 0;
		foreach ( $pages_list as $page ) {
			$list_of_pages[ $indx ] = array(
				'label' => $page->post_title,
				'code'  => $page->ID,
			);
			++$indx;
		}
		$show_as_options      = array();
		$show_as_options[0]   = array(
			'label' => 'Button',
			'code'  => true,
		);
		$show_as_options[1]   = array(
			'label' => 'Link',
			'code'  => false,
		);
		$url_type_options     = array();
		$url_type_options[0]  = array(
			'label' => 'Page',
			'code'  => true,
		);
		$url_type_options[1]  = array(
			'label' => 'Custom URL',
			'code'  => false,
		);
		$border_styles        = self::get_background_border_styles();
		$styles_length        = count( $border_styles );
		$styles_keys          = array_keys( $border_styles );
		$border_style_options = array();
		for ( $i = 0; $i < $styles_length; $i++ ) {
			$border_style_options[ $i ] = array(
				'label' => $styles_keys[ $i ],
				'code'  => $border_styles[ $styles_keys[ $i ] ],
			);
		}
		$cookie_font    = array();
		$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ? GDPR_COOKIE_CONSENT_VERSION : '';
		if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
			$cookie_font = apply_filters( 'gcc_font_options', $cookie_font );
		} else {
			$cookie_font = self::get_fonts();
		}
		$font_length  = count( $cookie_font );
		$font_keys    = array_keys( $cookie_font );
		$font_options = array();
		for ( $i = 0; $i < $font_length; $i++ ) {
			$font_options[ $i ] = array(
				'label' => $font_keys[ $i ],
				'code'  => $cookie_font[ $font_keys[ $i ] ],
			);
		}
		$layout_skin         = array();
		$layout_skin         = apply_filters( 'gcc_layout_skin_options', $layout_skin );
		$layout_length       = count( $layout_skin );
		$layout_keys         = array_keys( $layout_skin );
		$layout_skin_options = array();

		for ( $i = 0; $i < $layout_length; $i++ ) {
			$layout_skin_options[ $i ] = array(
				'label' => $layout_keys[ $i ],
				'code'  => $layout_skin[ $layout_keys[ $i ] ],
			);
		}
		$privacy_policy_page_options = array();
		$index                       = 0;
		foreach ( $pages_list as $page ) {
			$privacy_policy_page_options[ $index ] = array(
				'label' => $page->post_title,
				'code'  => $page->ID,
			);
			++$index;
		}
		$button_sizes        = self::get_button_sizes();
		$button_sizes_length = count( $button_sizes );
		$button_sizes_keys   = array_keys( $button_sizes );
		$button_size_options = array();
		for ( $i = 0; $i < $button_sizes_length; $i++ ) {
			$button_size_options[ $i ] = array(
				'label' => $button_sizes_keys[ $i ],
				'code'  => $button_sizes[ $button_sizes_keys[ $i ] ],
			);
		}
		$button_sizes        = self::get_button_sizes();
		$sizes_length        = count( $button_sizes );
		$sizes_keys          = array_keys( $button_sizes );
		$accept_size_options = array();

		for ( $i = 0; $i < $sizes_length; $i++ ) {
			$accept_size_options[ $i ] = array(
				'label' => $sizes_keys[ $i ],
				'code'  => $button_sizes[ $sizes_keys[ $i ] ],
			);
		}

		$button_actions        = self::get_js_actions();
		$action_length         = count( $button_actions );
		$action_keys           = array_keys( $button_actions );
		$accept_action_options = array();

		for ( $i = 0; $i < $action_length; $i++ ) {
			$accept_action_options[ $i ] = array(
				'label' => $action_keys[ $i ],
				'code'  => $button_actions[ $action_keys[ $i ] ],
			);
		}
		$accept_button_as_options    = array();
		$accept_button_as_options[0] = array(
			'label' => 'Button',
			'code'  => true,
		);
		$accept_button_as_options[1] = array(
			'label' => 'Link',
			'code'  => false,
		);
		$open_url_options            = array();
		$open_url_options[0]         = array(
			'label' => 'Yes',
			'code'  => true,
		);
		$open_url_options[1]         = array(
			'label' => 'No',
			'code'  => false,
		);
		$decline_action_options      = array();
		$decline_action_options[0]   = array(
			'label' => 'Close Header',
			'code'  => '#cookie_action_close_header_reject',
		);
		$decline_action_options[1]   = array(
			'label' => 'Open URL',
			'code'  => 'CONSTANT_OPEN_URL',
		);

		$settings_layout_options             = array();
		$settings_layout_options[0]          = array(
			'label' => 'Extented Banner',
			'code'  => false,
		);
		$settings_layout_options[1]          = array(
			'label' => 'Popup',
			'code'  => true,
		);
		$settings_layout_options_extended    = array();
		$settings_layout_options_extended[0] = end( $settings_layout_options );
		$script_blocker_settings             = array();
		$cookie_list_settings                = array();
		$cookie_scan_settings                = array();
		$script_blocker_settings             = apply_filters( 'gdpr_settings_script_blocker_values', '' );
		$cookie_list_settings                = apply_filters( 'gdpr_settings_cookie_list_values', '' );
		$cookie_scan_settings                = apply_filters( 'gdpr_settings_cookie_scan_values', '' );
		wp_localize_script(
			$this->plugin_name . '-main',
			'settings_obj',
			array(
				'the_options'                      => $settings,
				'ajaxurl'                          => admin_url( 'admin-ajax.php' ),
				'policies'                         => $policies,
				'position_options'                 => $position_options,
				'show_cookie_as_options'           => $show_cookie_as_options,
				'show_language_as_options'         => $show_language_as_options,
				'schedule_scan_options'            => $schedule_scan_options,
				'schedule_scan_day_options'        => $schedule_scan_day_options,
				'on_hide_options'                  => $on_hide_options,
				'on_load_options'                  => $on_load_options,
				'is_pro_active'                    => $is_pro_active,
				'tab_position_options'             => $tab_position_options,
				'cookie_expiry_options'            => $cookie_expiry_options,
				'list_of_contents'                 => $list_of_contents,
				'border_style_options'             => $border_style_options,
				'show_as_options'                  => $show_as_options,
				'url_type_options'                 => $url_type_options,
				'privacy_policy_options'           => $privacy_policy_page_options,
				'button_size_options'              => $button_size_options,
				'accept_size_options'              => $accept_size_options,
				'accept_action_options'            => $accept_action_options,
				'accept_button_as_options'         => $accept_button_as_options,
				'open_url_options'                 => $open_url_options,
				'widget_position_options'          => $widget_position_options,
				'decline_action_options'           => $decline_action_options,
				'settings_layout_options'          => $settings_layout_options,
				'settings_layout_options_extended' => $settings_layout_options_extended,
				'script_blocker_settings'          => $script_blocker_settings,
				'font_options'                     => $font_options,
				'layout_skin_options'              => $layout_skin_options,
				'cookie_list_settings'             => $cookie_list_settings,
				'cookie_scan_settings'             => $cookie_scan_settings,
				'restore_settings_nonce'           => wp_create_nonce( 'restore_default_settings' ),
				// hide banner.
				'list_of_pages'                    => $list_of_pages,
			)
		);
		wp_enqueue_script( $this->plugin_name . '-main' );

		// enqueue wizard admin style.
		wp_enqueue_style( $this->plugin_name . '-wizard' );

		// require wizard template.

		require_once plugin_dir_path( __FILE__ ) . 'views/wizard.php';
	}
	/**
	 * Callback function for import settings.
	 *
	 * @since 2.5
	 */
	public function gdpr_cookie_consent_import_settings() {
		if ( isset( $_POST['security'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'import_settings' ) ) {
				return;
			}
			if ( isset( $_POST['settings'] ) ) {
				$the_options = $_POST['settings'];//phpcs:ignore
				update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
				wp_send_json_success( array( 'imported_settings' => true ) );
			}
		}
	}
	/**
	 * Callback function for Dashboard page.
	 *
	 * @since 2.1.0
	 */
	public function gdpr_cookie_consent_new_admin_screen() {
		// Require the class file for gdpr cookie consent api framework settings.
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';

		// Instantiate a new object of the GDPR_Cookie_Consent_Settings class.
		$this->settings = new GDPR_Cookie_Consent_Settings();

		// Call the is_connected() method from the instantiated object to check if the user is connected.
		$is_user_connected = $this->settings->is_connected();

		$pro_is_activated = get_option( 'wpl_pro_active', false );

		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();

		// find out if data reqs is on.
		$data_reqs_on   = isset( $the_options['data_reqs_on'] ) ? $the_options['data_reqs_on'] : null;
		$consent_log_on = isset( $the_options['logging_on'] ) ? $the_options['logging_on'] : null;

		wp_enqueue_style( $this->plugin_name );
		wp_enqueue_script(
			'gdpr-cookie-consent-admin-revamp',
			GDPR_URL . 'admin/js/gdpr-cookie-consent-admin-revamp.js',
			array( 'jquery' ),
			GDPR_COOKIE_CONSENT_VERSION,
			true
		);
		wp_localize_script(
			'gdpr-cookie-consent-admin-revamp',
			'gdpr_localize_data',
			array(
				'ajaxurl'           => admin_url( 'admin-ajax.php' ),
				'gdprurl'           => GDPR_URL,
				'siteurl'           => site_url(),
				'admin_url'         => admin_url(),
				'is_pro_activated'  => $pro_is_activated,
				'is_data_req_on'    => $data_reqs_on,
				'is_consent_log_on' => $consent_log_on,
				'gdpr_app_url'      => GDPR_APP_URL,
				'_ajax_nonce'       => wp_create_nonce( 'gdpr-cookie-consent' ),
				'is_user_connected' => $is_user_connected,
			)
		);
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'admin/partials/gdpr-cookie-consent-main-admin.php';
	}
	/**
	 * Callback function for Dashboard page.
	 *
	 * @since 2.1.0
	 */
	public function gdpr_cookie_consent_dashboard() {
		// Lock out non-admins.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_attr__( 'You do not have sufficient permission to perform this operation', 'gdpr-cookie-consent' ) );
		}
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';

		// Instantiate a new object of the GDPR_Cookie_Consent_Settings class.
		$this->settings = new GDPR_Cookie_Consent_Settings();

		// Call the is_connected() method from the instantiated object to check if the user is connected.
		$is_user_connected = $this->settings->is_connected();

		$installed_plugins = get_plugins();
		$active_plugins    = $this->gdpr_cookie_consent_active_plugins();
		$cookie_options    = get_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
		$pro_installed     = isset( $installed_plugins['wpl-cookie-consent/wpl-cookie-consent.php'] ) ? '1' : '0';
		$is_cookie_on      = isset( $cookie_options['is_on'] ) ? $cookie_options['is_on'] : '1';
		if ( $is_cookie_on == 'true' ) {
			$is_cookie_on = true;
		}
		$is_pro_active     = get_option( 'wpl_pro_active' );
		$api_key_activated = '';
		$api_key_activated = get_option( 'wc_am_client_wpl_cookie_consent_activated' );

		// if pro is active then fetch $max_mind_integrated from pro otherwise from free.
		if ( $is_pro_active ) {

			$max_mind_integrated = '0';
			$max_mind_integrated = apply_filters( 'gdpr_get_maxmind_integrated', $max_mind_integrated );
		} else {
			$max_mind_integrated = get_option( 'wpl_pro_maxmind_integrated' );
		}

		// if pro is active then fetch last scanned details from pro otherwise from free.
		if ( $is_pro_active ) {

			$last_scanned_details = '';
			$last_scanned_details = apply_filters( 'gdpr_get_last_scanned_details', $last_scanned_details );

		} else {
			global $wpdb;
			$scan_table           = $wpdb->prefix . 'wpl_cookie_scan';
			$sql                  = "SELECT * FROM `$scan_table` ORDER BY id_wpl_cookie_scan DESC LIMIT 1";
			$last_scanned_details = $wpdb->get_row( $sql, ARRAY_A ); //phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			if ( $last_scanned_details ) {
				$last_scanned_details = gmdate( 'F j, Y g:i a T', $last_scanned_details['created_at'] );
			} else {
				$last_scanned_details = 'Perform your first Cookie Scan.';
			}
		}
		$admin_url           = admin_url();
		$admin_url_length    = strlen( $admin_url );
		$show_cookie_url     = $admin_url . 'admin.php?page=gdpr-cookie-consent#cookie_settings#compliances';
		$language_url        = $admin_url . 'admin.php?page=gdpr-cookie-consent#cookie_settings#language';
		$maxmind_url         = $admin_url . 'admin.php?page=gdpr-cookie-consent#cookie_settings#integrations';
		$cookie_scan_url     = $admin_url . 'admin.php?page=gdpr-cookie-consent#cookie_settings#cookie_list';
		$plugin_page_url     = $admin_url . 'plugins.php';
		$key_activate_url    = $admin_url . 'admin.php?page=gdpr-cookie-consent#activation_key';
		$consent_log_url     = $admin_url . 'admin.php?page=gdpr-cookie-consent#consent_logs';
		$cookie_design_url   = $admin_url . 'admin.php?page=gdpr-cookie-consent#cookie_settings#gdpr_design';
		$cookie_template_url = $admin_url . 'admin.php?page=gdpr-cookie-consent#cookie_settings#configuration';
		$script_blocker_url  = $admin_url . 'admin.php?page=gdpr-cookie-consent#cookie_settings#script_blocker';
		$third_party_url     = $admin_url . 'admin.php?page=gdpr-cookie-consent#policy_data';
		$documentation_url   = 'https://club.wpeka.com/docs/wp-cookie-consent/';
		$gdpr_pro_url        = 'https://club.wpeka.com/product/wp-gdpr-cookie-consent/?utm_source=plugin&utm_medium=gdpr&utm_campaign=quick-links&utm_content=upgrade-to-pro';
		$free_support_url    = 'https://wordpress.org/support/plugin/gdpr-cookie-consent/';
		$pro_support_url     = 'https://club.wpeka.com/my-account/?utm_source=plugin&utm_medium=gdpr&utm_campaign=dashboard&utm_content=support';
		$videos_url          = 'https://youtube.com/playlist?list=PLb2uZyVYHgAXpXCWL6jPde03uGCzqKELQ';
		$legalpages_url      = 'https://wordpress.org/plugins/wplegalpages/';
		$adcenter_url        = 'https://wordpress.org/plugins/wpadcenter/';
		$survey_funnel_url   = 'https://wordpress.org/plugins/surveyfunnel-lite/';
		$decline_log         = get_option( 'wpl_cl_decline' );
		$accept_log          = get_option( 'wpl_cl_accept' );
		$partially_acc_log   = get_option( 'wpl_cl_partially_accept' );
		wp_enqueue_style( $this->plugin_name . '-dashboard' );
		wp_enqueue_script( $this->plugin_name . '-dashboard' );
		wp_localize_script(
			$this->plugin_name . '-dashboard',
			'dashboard_options',
			array(
				'active_plugins'        => $active_plugins,
				'showing_cookie_notice' => $is_cookie_on,
				'pro_installed'         => $pro_installed,
				'pro_activated'         => $is_pro_active,
				'maxmind_integrated'    => $max_mind_integrated,
				'last_scanned'          => $last_scanned_details,
				'show_cookie_url'       => $show_cookie_url,
				'language_url'          => $language_url,
				'maxmind_url'           => $maxmind_url,
				'cookie_scan_url'       => $cookie_scan_url,
				'plugin_page_url'       => $plugin_page_url,
				'gdpr_pro_url'          => $gdpr_pro_url,
				'documentation_url'     => $documentation_url,
				'free_support_url'      => $free_support_url,
				'pro_support_url'       => $pro_support_url,
				'videos_url'            => $videos_url,
				'key_activate_url'      => $key_activate_url,
				'api_key_activated'     => $api_key_activated,
				'consent_log_url'       => $consent_log_url,
				'cookie_design_url'     => $cookie_design_url,
				'cookie_template_url'   => $cookie_template_url,
				'script_blocker_url'    => $script_blocker_url,
				'third_party_url'       => $third_party_url,
				'legalpages_url'        => $legalpages_url,
				'adcenter_url'          => $adcenter_url,
				'survey_funnel_url'     => $survey_funnel_url,
				'decline_log'           => $decline_log,
				'accept_log'            => $accept_log,
				'partially_acc_log'     => $partially_acc_log,
				'is_user_connected'     => $is_user_connected,
			)
		);
		require_once plugin_dir_path( __FILE__ ) . 'views/gdpr-dashboard-page.php';
	}

	/**
	 * Function to get list of active plugins.
	 *
	 * @since 2.1.0
	 */
	public function gdpr_cookie_consent_active_plugins() {
		return get_option( 'active_plugins' );
	}

	/**
	 * Ajax callback to restore settings to default.
	 *
	 * @since 2.1.0
	 */
	public function gdpr_cookie_consent_ajax_restore_default_settings() {
		if ( isset( $_POST['security'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'restore_default_settings' ) ) {
				return;
			}
			// restore translation of public facing side text.
			// Load and decode translations from JSON file.
			$translations_file = plugin_dir_path( __FILE__ ) . 'translations/translations.json';
			$translations      = json_decode( file_get_contents( $translations_file ), true );

			// Define an array of text keys to translate.
			$text_keys_to_translate = array(
				'gdpr_cookie_category_description_necessary',
				'gdpr_cookie_category_name_necessary',
				'gdpr_cookie_category_description_analytics',
				'gdpr_cookie_category_name_analytics',
				'gdpr_cookie_category_description_marketing',
				'gdpr_cookie_category_description_preference',
				'gdpr_cookie_category_description_unclassified',
				'gdpr_cookie_category_name_marketing',
				'gdpr_cookie_category_name_preference',
				'gdpr_cookie_category_name_unclassified',
			);

			// reset to "english".
			$target_language = 'en';
			// Initialize arrays to store translated category descriptions and names.
			$translated_category_descriptions = array();
			$translated_category_names        = array();

			// Loop through the text keys and translate them.
			foreach ( $text_keys_to_translate as $text_key ) {
				$translated_text = $this->translated_text( $text_key, $translations, $target_language );
				// Check if the current text key is for category description or category name.
				if ( 'gdpr_cookie_category_description_necessary' === $text_key ) {
					$translated_category_description_necessary = $translated_text;
				} elseif ( 'gdpr_cookie_category_description_analytics' === $text_key ) {
					$translated_category_description_analytics = $translated_text;
				} elseif ( 'gdpr_cookie_category_description_marketing' === $text_key ) {
					$translated_category_description_marketing = $translated_text;
				} elseif ( 'gdpr_cookie_category_description_preference' === $text_key ) {
					$translated_category_description_preferences = $translated_text;
				} elseif ( 'gdpr_cookie_category_description_unclassified' === $text_key ) {
					$translated_category_description_unclassified = $translated_text;
				} elseif ( 'gdpr_cookie_category_name_analytics' === $text_key ) {
					$translated_category_name_analytics = $translated_text;
				} elseif ( 'gdpr_cookie_category_name_marketing' === $text_key ) {
					$translated_category_name_marketing = $translated_text;
				} elseif ( 'gdpr_cookie_category_name_necessary' === $text_key ) {
					$translated_category_name_necessary = $translated_text;
				} elseif ( 'gdpr_cookie_category_name_preference' === $text_key ) {
					$translated_category_name_preferences = $translated_text;
				} elseif ( 'gdpr_cookie_category_name_unclassified' === $text_key ) {
					$translated_category_name_unclassified = $translated_text;
				}
			}
			// non dynaminc text for the cookie settings.
			global $wpdb;
			$cat_table  = $wpdb->prefix . $this->category_table;
			$categories = $this->gdpr_get_categories();
			$cat_arr    = array();

			$translated_category_descriptions = array(
				1 => $translated_category_description_analytics,
				2 => $translated_category_description_marketing,
				3 => $translated_category_description_necessary,
				4 => $translated_category_description_preferences,
				5 => $translated_category_description_unclassified,
			);
			$translated_category_names        = array(
				1 => $translated_category_name_analytics,
				2 => $translated_category_name_marketing,
				3 => $translated_category_name_necessary,
				4 => $translated_category_name_preferences,
				5 => $translated_category_name_unclassified,
			);

			if ( ! empty( $categories ) ) {
				foreach ( $categories as $category ) {
					$cat_description = isset( $category['description'] ) ? addslashes( $category['description'] ) : '';
					$cat_category    = isset( $category['name'] ) ? $category['name'] : '';
					$cat_slug        = isset( $category['slug'] ) ? $category['slug'] : '';

					// Check if the category has a translation available.
					$category_i_d = -1;
					switch ( $cat_category ) {
						case 'Analytics':
							$category_i_d = 1;
							break;
						case 'Marketing':
							$category_i_d = 2;
							break;
						case 'Necessary':
							$category_i_d = 3;
							break;
						case 'Preferences':
							$category_i_d = 4;
							break;
						case 'Unclassified':
							$category_i_d = 5;
							break;
					}

					if ( -1 != $category_i_d ) {
						// Update the table with the translated values.
						$wpdb->query(
							$wpdb->prepare(
								'UPDATE `' . $wpdb->prefix . 'gdpr_cookie_scan_categories`
								SET `gdpr_cookie_category_description` = %s,
									`gdpr_cookie_category_name` = %s
								WHERE `id_gdpr_cookie_category` = %d',
								$translated_category_descriptions[ $category_i_d ],
								$translated_category_names[ $category_i_d ],
								$category_i_d
							)
						);
					}
				}
			}
			// resetting the custom css when restore setting is clicked.
			$all_settings  = Gdpr_Cookie_Consent::gdpr_get_settings();
			$css_file_path = ABSPATH . 'wp-content/plugins/gdpr-cookie-consent/public/css/gdpr-cookie-consent-public-custom.css';
			// custom css min file.
			$css_min_file_path = ABSPATH . 'wp-content/plugins/gdpr-cookie-consent/public/css/gdpr-cookie-consent-public-custom.min.css';

			$all_settings['gdpr_css_text'] = '';
			$css_code_to_add               = $all_settings['gdpr_css_text'];

			// Open the CSS file for writing.
			$css_file = fopen( $css_file_path, 'w' );

			// Check if the file was opened successfully.
			if ( $css_file ) {
				// Write the CSS code to the file.
				fwrite( $css_file, $css_code_to_add );

				// Close the file.
				fclose( $css_file );
			}
			// Open the CSS min file for writing.
			$css_min_file = fopen( $css_min_file_path, 'w' );

			// Check if the file was opened successfully.
			if ( $css_min_file ) {
				// Write the CSS code to the file.
				fwrite( $css_min_file, $css_code_to_add );

				// Close the file.
				fclose( $css_min_file );
			}

			$the_options                            = Gdpr_Cookie_Consent::gdpr_get_default_settings();
			$the_options['data_req_editor_message'] = '&lt;p&gt;Hi {name}&lt;/p&gt;&lt;p&gt;We have received your request on {blogname}. Depending on the specific request and legal obligations we might follow-up.&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;Kind regards,&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;{blogname}&lt;/p&gt;';
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
			wp_send_json_success( array( 'restore_default_saved' => true ) );
		}
	}
}
