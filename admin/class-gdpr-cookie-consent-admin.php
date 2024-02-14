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
	private $supported_languages = array( 'fr', 'en', 'nl', 'bg', 'cs', 'da', 'de', 'es', 'hr', 'is', 'sl', 'gr' );

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
				echo $this->wpl_get_template( 'gdpr-policy-data-tab-template.php', $args );
	}

	/**
	 * Get a template based on filename, overridable in theme dir
	 *
	 * @param string $filename
	 * @param array  $args
	 * @param string $path
	 * @return string
	 */
	public function wpl_get_template( $filename, $args = array(), $path = false ) {

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

		if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'gdpr-cookie-consent' )
			&& isset( $_GET['action'] )
			&& $_GET['action'] == 'policy_delete'
			&& isset( $_GET['id'] )
		) {

			wp_delete_post( $_GET['id'], true );
			$paged = isset( $_GET['paged'] ) ? 'paged=' . intval( $_GET['paged'] ) : '';
			wp_redirect( admin_url( 'admin.php?page=gdpr-cookie-consent#policy_data' . $paged ) );
		}
	}

	/**
	 * Returns plugin actions links.
	 *
	 * @param array $links Plugin action links.
	 * @return array
	 */
	public function admin_plugin_action_links( $links ) {
		if ( ! get_option( 'wpl_pro_active' ) ) {
			$links = array_merge(
				array(
					'<a href="' . esc_url( 'https://club.wpeka.com/product/wp-gdpr-cookie-consent/?utm_source=gdpr&utm_medium=plugins&utm_campaign=link&utm_content=upgrade-to-pro' ) . '" target="_blank" rel="noopener noreferrer"><strong style="color: #11967A; display: inline;">' . __( 'Upgrade to Pro', 'gdpr-cookie-consent' ) . '</strong></a>',
				),
				$links
			);
		}
		return $links;
	}

	/**
	 * Migrate previous settings.
	 *
	 * @since 1.7.6
	 */
	public function admin_init() {
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
			$prev_gdpr_option['auto_scroll']          = false;
			$prev_gdpr_option['show_again_position']  = 'right';
			$prev_gdpr_option['show_again_text']      = 'Cookie Settings';
			$prev_gdpr_option['show_again_margin']    = '5%';
			$prev_gdpr_option['auto_hide_delay']      = '10000';
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
	 * Admin settings page.
	 *
	 * @since 1.0
	 */
	public function admin_settings_page() {
		$is_pro = get_option( 'wpl_pro_active', false );
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

		$show_language_as_options     = array();
		$show_language_as_options[0]  = array(
			'label' => 'English',
			'code'  => 'en',
		);
		$show_language_as_options[1]  = array(
			'label' => 'French',
			'code'  => 'fr',
		);
		$show_language_as_options[2]  = array(
			'label' => 'Dutch',
			'code'  => 'nl',
		);
		$show_language_as_options[3]  = array(
			'label' => 'Bulgarian',
			'code'  => 'bg',
		);
		$show_language_as_options[4]  = array(
			'label' => 'Czech',
			'code'  => 'cs',
		);
		$show_language_as_options[5]  = array(
			'label' => 'Danish',
			'code'  => 'da',
		);
		$show_language_as_options[6]  = array(
			'label' => 'German',
			'code'  => 'de',
		);
		$show_language_as_options[7]  = array(
			'label' => 'Spanish',
			'code'  => 'es',
		);
		$show_language_as_options[8]  = array(
			'label' => 'Croatian',
			'code'  => 'hr',
		);
		$show_language_as_options[9]  = array(
			'label' => 'Icelandic',
			'code'  => 'is',
		);
		$show_language_as_options[10] = array(
			'label' => 'Slovenian',
			'code'  => 'sl',
		);
		$show_language_as_options[11] = array(
			'label' => 'Greek',
			'code'  => 'gr',
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
			// data reqs fields
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
				// hide banner
				$selected_pages = array();
				$selected_pages = isset( $_POST['gcc-selected-pages'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-selected-pages'] ) ) ) : '';
				// storing id of pages in database.
				$the_options['select_pages'] = $selected_pages;
			}
			if ( get_option( 'wpl_pro_active' ) && get_option( 'wc_am_client_wpl_cookie_consent_activated' ) && 'Activated' === get_option( 'wc_am_client_wpl_cookie_consent_activated' ) ) {
				$saved_options    = get_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
				$restricted_posts = array();
				$restricted_posts = isset( $_POST['gcc-restrict-posts'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-restrict-posts'] ) ) ) : '';
				if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
					// hide banner
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

		$show_cookie_as_options       = array();
		$show_cookie_as_options[0]    = array(
			'label' => 'Banner',
			'code'  => 'banner',
		);
		$show_cookie_as_options[1]    = array(
			'label' => 'Popup',
			'code'  => 'popup',
		);
		$show_cookie_as_options[2]    = array(
			'label' => 'Widget',
			'code'  => 'widget',
		);
		$show_language_as_options     = array();
		$show_language_as_options[0]  = array(
			'label' => 'English',
			'code'  => 'en',
		);
		$show_language_as_options[1]  = array(
			'label' => 'French',
			'code'  => 'fr',
		);
		$show_language_as_options[2]  = array(
			'label' => 'Dutch',
			'code'  => 'nl',
		);
		$show_language_as_options[3]  = array(
			'label' => 'Bulgarian',
			'code'  => 'bg',
		);
		$show_language_as_options[4]  = array(
			'label' => 'Czech',
			'code'  => 'cs',
		);
		$show_language_as_options[5]  = array(
			'label' => 'Danish',
			'code'  => 'da',
		);
		$show_language_as_options[6]  = array(
			'label' => 'German',
			'code'  => 'de',
		);
		$show_language_as_options[7]  = array(
			'label' => 'Spanish',
			'code'  => 'es',
		);
		$show_language_as_options[8]  = array(
			'label' => 'Croatian',
			'code'  => 'hr',
		);
		$show_language_as_options[9]  = array(
			'label' => 'Icelandic',
			'code'  => 'is',
		);
		$show_language_as_options[10] = array(
			'label' => 'Slovenian',
			'code'  => 'sl',
		);
		$show_language_as_options[11] = array(
			'label' => 'Greek',
			'code'  => 'gr',
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
		$pro_is_activated = get_option( 'wpl_pro_active', false );

		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();

		// find out if data reqs is on.
		$data_reqs_on = isset( $the_options['data_reqs_on'] ) ? $the_options['data_reqs_on'] : null;

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
				'ajaxurl'          => admin_url( 'admin-ajax.php' ),
				'gdprurl'          => GDPR_URL,
				'siteurl'          => site_url(),
				'admin_url'        => admin_url(),
				'is_pro_activated' => $pro_is_activated,
				'is_data_req_on'   => $data_reqs_on,
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
		$installed_plugins = get_plugins();
		$active_plugins    = $this->gdpr_cookie_consent_active_plugins();
		$cookie_options    = get_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
		$pro_installed     = isset( $installed_plugins['wpl-cookie-consent/wpl-cookie-consent.php'] ) ? '1' : '0';
		$is_cookie_on      = isset( $cookie_options['is_on'] ) ? $cookie_options['is_on'] : '1';
		if ( $is_cookie_on == 'true' ) {
			$is_cookie_on = true;
		}
		$is_pro_active        = get_option( 'wpl_pro_active' );
		$api_key_activated    = '';
		$api_key_activated    = get_option( 'wc_am_client_wpl_cookie_consent_activated' );
		$max_mind_integrated  = '0';
		$max_mind_integrated  = apply_filters( 'gdpr_get_maxmind_integrated', $max_mind_integrated );
		$last_scanned_details = '';
		$last_scanned_details = apply_filters( 'gdpr_get_last_scanned_details', $last_scanned_details );
		$admin_url            = admin_url();
		$admin_url_length     = strlen( $admin_url );
		$show_cookie_url      = $admin_url . 'admin.php?page=gdpr-cookie-consent#cookie_settings#compliances';
		$language_url         = $admin_url . 'admin.php?page=gdpr-cookie-consent#cookie_settings#language';
		$maxmind_url          = $admin_url . 'admin.php?page=gdpr-cookie-consent#integrations';
		$cookie_scan_url      = $admin_url . 'admin.php?page=gdpr-cookie-consent#cookie_settings#cookie_list';
		$plugin_page_url      = $admin_url . 'plugins.php';
		$key_activate_url     = $admin_url . 'admin.php?page=wc_am_client_wpl_cookie_consent_dashboard';
		$consent_log_url      = $admin_url . 'admin.php?page=gdpr-cookie-consent#consent_logs';
		$cookie_design_url    = $admin_url . 'admin.php?page=gdpr-cookie-consent#cookie_settings#gdpr_design';
		$cookie_template_url  = $admin_url . 'admin.php?page=gdpr-cookie-consent#cookie_settings#configuration';
		$script_blocker_url   = $admin_url . 'admin.php?page=gdpr-cookie-consent#cookie_settings#script_blocker';
		$third_party_url      = $admin_url . 'admin.php?page=gdpr-cookie-consent#policy_data';
		$documentation_url    = 'https://club.wpeka.com/docs/wp-cookie-consent/';
		$gdpr_pro_url         = 'https://club.wpeka.com/product/wp-gdpr-cookie-consent/?utm_source=plugin&utm_medium=gdpr&utm_campaign=quick-links&utm_content=upgrade-to-pro';
		$free_support_url     = 'https://wordpress.org/support/plugin/gdpr-cookie-consent/';
		$pro_support_url      = 'https://club.wpeka.com/my-account/?utm_source=plugin&utm_medium=gdpr&utm_campaign=dashboard&utm_content=support';
		$videos_url           = 'https://youtube.com/playlist?list=PLb2uZyVYHgAXpXCWL6jPde03uGCzqKELQ';
		$legalpages_url       = 'https://wordpress.org/plugins/wplegalpages/';
		$adcenter_url         = 'https://wordpress.org/plugins/wpadcenter/';
		$survey_funnel_url    = 'https://wordpress.org/plugins/surveyfunnel-lite/';
		$decline_log          = get_option( 'wpl_cl_decline' );
		$accept_log           = get_option( 'wpl_cl_accept' );
		$partially_acc_log    = get_option( 'wpl_cl_partially_accept' );
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
