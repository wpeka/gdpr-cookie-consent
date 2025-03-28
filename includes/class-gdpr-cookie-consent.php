<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://club.wpeka.com
 * @since      1.0
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/includes
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      Gdpr_Cookie_Consent_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The currently stored option settings of the plugin.
	 *
	 * @since 1.0
	 * @access private
	 * @var array $stored_options The stored option settings of the plugin.
	 */
	private static $stored_options = array();

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0
	 */
	public $settings;
	public $library_auth;
	public $respadons_api;

	public function __construct() {
		if ( defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ) {
			$this->version = GDPR_COOKIE_CONSENT_VERSION;
		} else {
			$this->version = '3.7.6';
		}
		add_action(
			'current_screen',
			function () {
				if ( ! $this->is_plugins_screen() ) {
					return;
				}
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_deactivate_popup_dialog_scripts' ) );
			}
		);
		$this->plugin_name = 'gdpr-cookie-consent';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Gdpr_Cookie_Consent_Loader. Orchestrates the hooks of the plugin.
	 * - Gdpr_Cookie_Consent_I18n. Defines internationalization functionality.
	 * - Gdpr_Cookie_Consent_Admin. Defines all hooks for the admin area.
	 * - Gdpr_Cookie_Consent_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the.
		 * core plugin.
		 */
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/class-gdpr-cookie-consent-loader.php';

		/**
		 * The class responsible for defining internationalization functionality.
		 * of the plugin.
		 */
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/class-gdpr-cookie-consent-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'admin/class-gdpr-cookie-consent-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing.
		 * side of the site.
		 */
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'public/class-gdpr-cookie-consent-public.php';
		/**
		 * The class responsible for orchestrating the actions and filters of the.
		 * Script blocker, Cookie Scan.
		 */
		$wpl_pro_active = get_option( 'wpl_pro_active', false );

		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'public/modules/script-blocker/class-wpl-cookie-consent-script-blocker.php';
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . '/public/modules/consent-logs/class-wpl-cookie-consent-consent-logs.php';
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'admin/modules/cookie-scanner/class-wpl-cookie-consent-cookie-scanner.php';
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . '/public/modules/geo-ip/class-wpl-cookie-consent-geo-ip.php';
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . '/admin/modules/ab-testing/class-wpl-cookie-consent-ab-testing.php';
		/**
		 * The class responsible for defining App Authentication functionality
		 * of the plugin.
		 */
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/class-gdpr-cookie-consent-app-auth.php';

		$this->library_auth = new GDPR_Cookie_Consent_App_Auth();

		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-api.php';

		$this->respadons_api = new GDPR_Cookie_Consent_Api();

		$this->loader = new Gdpr_Cookie_Consent_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Gdpr_Cookie_Consent_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Gdpr_Cookie_Consent_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	public static function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! defined( 'REST_REQUEST' );
		}
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Gdpr_Cookie_Consent_Admin( $this->get_plugin_name(), $this->get_version() );
		/**
		 * Load admin modules.
		 */
		$plugin_admin->admin_modules();
		$this->loader->add_action( 'init', $plugin_admin, 'gdpr_register_block_type' );
		if ( ! self::is_request( 'admin' ) ) {
			$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'gdpr_quick_toolbar_menu', 999 );
		}
		if ( self::is_request( 'admin' ) ) {
			$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu');
			// Adding admin menu.
			// $this->loader->add_action( 'current_screen', $plugin_admin, 'add_tabs', 15 );
			$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'admin_footer_text', 10, 1 );
			$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init', 5 );
			$this->loader->add_action( 'admin_init', $plugin_admin, 'gdpr_admin_init' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
			$this->loader->add_filter( 'plugin_action_links_' . GDPR_COOKIE_CONSENT_PLUGIN_BASENAME, $plugin_admin, 'admin_plugin_action_links' );
			$this->loader->add_action( 'wp_ajax_gcc_save_admin_settings', $plugin_admin, 'gdpr_cookie_consent_ajax_save_settings', 10, 1 );
			$this->loader->add_action( 'wp_ajax_gcc_enable_iab', $plugin_admin, 'gdpr_cookie_consent_ajax_enable_iab', 10, 1 );
			$this->loader->add_action( 'wp_ajax_ab_testing_enable', $plugin_admin, 'gdpr_cookie_consent_ab_testing_enable', 10, 1 );
			$this->loader->add_action( 'wp_ajax_gcc_restore_default_settings', $plugin_admin, 'gdpr_cookie_consent_ajax_restore_default_settings', 10, 1 );
			$this->loader->add_action( 'wp_ajax_gcc_auto_generated_banner', $plugin_admin, 'gdpr_cookie_consent_ajax_auto_generated_banner', 10, 1 );
			// added ajax callback for wizard.
			$this->loader->add_action( 'wp_ajax_gcc_save_wizard_settings', $plugin_admin, 'gdpr_cookie_consent_ajax_save_wizard_settings', 10, 1 );
			// added ajax for import settings.
			$this->loader->add_action( 'wp_ajax_gcc_update_imported_settings', $plugin_admin, 'gdpr_cookie_consent_import_settings', 10, 1 );

			$this->loader->add_action( 'add_policy_data_content', $plugin_admin, 'gdpr_policy_data_overview' );
			$this->loader->add_action( 'admin_init', $plugin_admin, 'gdpr_policy_process_delete' );
			$this->loader->add_filter( 'gdpr_get_maxmind_integrated', $plugin_admin, 'wpl_get_maxmind_integrated' );
			$this->loader->add_action( 'wp_ajax_wpl_cookie_consent_integrations_settings', $plugin_admin, 'wpl_cookie_consent_integrations_settings' );
			$this->loader->add_action( 'admin_init', $plugin_admin, 'wpl_get_ab_testing_settings' );
			$wpl_pro_active = get_option( 'wpl_pro_active' );
			if ( ! $wpl_pro_active ) {
				$this->loader->add_filter( 'gdpr_get_templates', $plugin_admin, 'get_templates', 10, 1 );
				$this->loader->add_action( 'gdpr_cookie_template', $plugin_admin, 'wpl_cookie_template' );
				$this->loader->add_filter( 'gdpr_datarequest_options', $plugin_admin, 'wpl_data_reqs_options' );
				// action hooks for data reqs.
				$this->loader->add_action( 'wp_ajax_nopriv_data_reqs_form_submit', $plugin_admin, 'wpl_data_reqs_handle_form_submit' );
				$this->loader->add_action( 'wp_ajax_data_reqs_form_submit', $plugin_admin, 'wpl_data_reqs_handle_form_submit' );
				// create table in db.
				$this->loader->add_action( 'activated_plugin', $plugin_admin, 'update_db_check' );
				$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'update_db_check' );
				// $this->loader->add_action( 'admin_notices', $plugin_admin, 'wpl_admin_notices' );
				// action to add admin notice for api connections
				$this->loader->add_action( 'admin_notices', $plugin_admin, 'gdpr_admin_notices' );
			}
			// Deactivate Popup action hooks.
			$this->loader->add_action( 'wp_ajax_gdpr_cookie_consent_deactivate_popup', $plugin_admin, 'gdpr_cookie_consent_deactivate_popup' );
			$this->loader->add_action( 'wp_ajax_nopriv_gdpr_cookie_consent_deactivate_popup', $plugin_admin, 'gdpr_cookie_consent_deactivate_popup' );
			// action to add review notice
			$this->loader->add_action( 'admin_notices', $plugin_admin, 'gdpr_admin_review_notice' );
			$this->loader->add_action( 'admin_init', $plugin_admin, 'gdpr_review_already_done', 5 );
			// action to update banner according to ab testing result
			$this->loader->add_action( 'admin_init', $plugin_admin, 'gdpr_ab_testing_complete' );
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Gdpr_Cookie_Consent_Public( $this->get_plugin_name(), $this->get_version() );
		/**
		 * Load public modules.
		 */
		$plugin_public->public_modules();
		if ( self::is_request( 'frontend' ) ) {
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
			$this->loader->add_action( 'template_redirect', $plugin_public, 'gdprcookieconsent_template_redirect', 99 );
			$this->loader->add_action( 'wp_footer', $plugin_public, 'gdprcookieconsent_inject_gdpr_script' );
			// added rest endpoint for fetching current options for banner.
			$this->loader->add_action( 'rest_api_init', $plugin_public, 'gdpr_cookie_data_endpoint' );
			if ( ! get_option( 'wpl_pro_active' ) ) {
				// action hooks for geo integration.
				$this->loader->add_action( 'wp_ajax_nopriv_show_cookie_consent_bar', $plugin_public, 'show_cookie_consent_bar' );
				$this->loader->add_action( 'wp_ajax_show_cookie_consent_bar', $plugin_public, 'show_cookie_consent_bar' );
				$this->loader->add_filter( 'gdprcookieconsent_json_settings', $plugin_public, 'wplcookieconsent_json_settings', 10, 1 );
			}
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0
	 * @return    Gdpr_Cookie_Consent_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
	/**
	 * Checking the deactivate page called by constructor.
	 *
	 * @since 3.1.0
	 * @access private
	 */
	private function is_plugins_screen() {
		return in_array( get_current_screen()->id, array( 'plugins', 'plugins-network' ) );
	}
	/**
	 * Enqueue deactivate Popup dialog scripts.
	 *
	 * Registers the deactivate Popup dialog scripts and enqueues them.
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function enqueue_deactivate_popup_dialog_scripts() {
		wp_enqueue_style(
			'gdpr-cookie-consent-admin-deactivate-popup', // Handle for the stylesheet
			GDPR_URL . 'admin/css/wpl-cookie-consent-deactivate-popup.css', // URL to the CSS file
			GDPR_COOKIE_CONSENT_VERSION
		);
		wp_enqueue_script(
			'gdpr-cookie-consent-admin-deactivate-popup',
			GDPR_URL . 'admin/js/DeactivatePopup/gdpr-cookie-consent-admin-deactivate-popup.js',
			array( 'jquery' ),
			GDPR_COOKIE_CONSENT_VERSION,
			true
		);
		wp_localize_script(
			'gdpr-cookie-consent-admin-deactivate-popup',
			'gdpr_localize_deactivate_popup_data',
			array(
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'_ajax_nonce' => wp_create_nonce( 'gdpr-cookie-consent' ),
			)
		);
		add_action( 'admin_footer', array( $this, 'print_deactivate_popup_dialog' ) );
	}
	/**
	 * Print deactivate Popup dialog.
	 *
	 * Display a dialog box to ask the user to keep the data or not while deactivating the plugin.
	 *
	 * Fired by `admin_footer` filter.
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function print_deactivate_popup_dialog() {
		?>
		<div class="gdpr-deactivate-popup-form-wrapper-outer">
			<div class="gdpr-deactivate-popup-form-wrapper">
				<form class="gdpr-deactivate-popup-form">
					<div>
					<p class="gdpr-deactivate-popup-form-title">Deactivate WP Cookie Consent :</p>
					<div class="gdpr-deactivate-popup-form-description">
					<p class="gdpr-deactivate-popup-form-description-content">You are about to deactivate WP Cookie Consent. Would you like to delete its data or keep it in place?</p>
					</div>
					<div class="gdpr-deactivate-popup-inputs">
						<div class="gdpr-deactivate-input-choices">
						<input type="radio" id="gdpr-plugin-deactivate-without-data" name="reason" value="gdpr-plugin-deactivate-without-data">
					<label for="gdpr-plugin-deactivate-without-data">Keep all WP Cookie Consent tables and data</label><br>
						</div>
						<div class="gdpr-deactivate-input-choices">
						<input type="radio" id="gdpr-plugin-deactivate-with-data" name="reason" value="gdpr-plugin-deactivate-with-data">
					<label for="gdpr-plugin-deactivate-with-data">Delete all WP Cookie Consent tables and data</label><br>
						</div>
					</div>
					</div>
					<div class="gdpr-gdpr-deactivate-popup-form-buttons-wrap">
					<div class="gdpr-deactivate-popup-form-buttons">
					<button class="gdpr-deactivate-delete-button" id="gdpr-deactivate-delete">DEACTIVATE AND DELETE DATA</button>
					<button class="gdpr-deactivate-button" id="gdpr-deactivate">DEACTIVATE</button>
						<button class="gdpr-cancel-button" id="gdpr-cancel">CANCEL</button>
					</div>
					</div>

				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Generate tab head for settings page,
	 * method will translate the string to current language.
	 *
	 * @param array $title_arr Tab labels.
	 */
	public static function gdpr_generate_settings_tabhead( $title_arr ) {
		$out_arr = array();
		foreach ( $title_arr as $k => $v ) {
			if ( 'gdpr-cookie-consent-buttons' === $k ) {
				$out_arr[ $k ] = $v;
				// tab head for modules.
				$out_arr = apply_filters( 'gdpr_module_settings_tabhead', $out_arr );
			} else {
				$out_arr[ $k ] = $v;
			}
		}
		foreach ( $out_arr as $k => $v ) {
			if ( is_array( $v ) ) {
				$v = ( isset( $v[2] ) ? $v[2] : '' ) . esc_attr( $v[0] ) . ' ' . ( isset( $v[1] ) ? $v[1] : '' );
			} else {
				$v = esc_attr( $v );
			}
			?>
			<a class="nav-tab" href="#<?php echo esc_html( $k ); ?>"><?php echo esc_html( $v ); ?></a>
			<?php
		}
	}

	/**
	 * Envelope settings tab content with tab div.
	 * relative path is not acceptable in view file
	 *
	 * @since 1.0
	 *
	 * @param string $class Classname.
	 * @param int    $target_id Target ID.
	 * @param string $view_file View template file.
	 * @param string $html Html content.
	 * @param array  $variables Variables.
	 * @param int    $need_submit_btn Need submit button flag.
	 * @param string $error_message Error message.
	 */
	public static function gdpr_envelope_settings_tabcontent( $class, $target_id, $view_file = '', $html = '', $variables = array(), $need_submit_btn = 0, $error_message = '' ) {
		if ( 1 === $need_submit_btn ) {
			$post_cookie_list = array();
			if ( isset( $variables['post_cookie_list'] ) ) {
				$post_cookie_list = $variables['post_cookie_list'];
			}
		} elseif ( 2 === $need_submit_btn ) {
			$scripts_list  = array();
			$category_list = array();
			if ( isset( $variables['scripts_list'] ) ) {
				$scripts_list = $variables['scripts_list'];
			}
			if ( isset( $variables['category_list'] ) ) {
				$category_list = $variables['category_list'];
			}
		}
		$the_options = self::gdpr_get_settings();
		if ( 'script-blocker-advanced' === $target_id ) {
			?>
			<div class="<?php echo esc_attr( $class ); ?>" data-id="<?php echo esc_attr( $target_id ); ?>" gdpr_tab_frm_tgl-id="gdpr_usage_option" gdpr_tab_frm_tgl-val="gdpr">
		<?php } else { ?>
			<div class="<?php echo esc_attr( $class ); ?>" data-id="<?php echo esc_attr( $target_id ); ?>">
		<?php } ?>
			<?php
			if ( '' !== $view_file && file_exists( $view_file ) ) {
				include_once $view_file;
			}
			?>
			<?php
			if ( 1 === $need_submit_btn ) {
				include plugin_dir_path( GDPR_COOKIE_CONSENT_PLUGIN_FILENAME ) . 'admin/views/admin-display-save-button.php';
			}
			?>
		</div>
		<?php
	}

	/**
	 * Deliberately allows class and ID declarations to assist with custom CSS styling.
	 * Returns list of HTML tags allowed in HTML fields for use in declaration of wp_kset field validation.
	 *
	 * @since 1.0
	 */
	public static function gdpr_allowed_html() {
		$allowed_html = array(
			// Allowed:     <a href="" id="" class="" title="" target="">...</a>.
			// Not allowed: <a href="javascript(...);">...</a>.
			'a'      => array(
				'href'   => array(),
				'id'     => array(),
				'class'  => array(),
				'title'  => array(),
				'target' => array(),
				'rel'    => array(),
				'style'  => array(),
				'data'   => array(),
				'data-toggle' => array(),
				'data-target' => array(), 
			),
			'b'      => array(),
			'br'     => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'div'    => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'em'     => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'i'      => array(),
			'img'    => array(
				'src'   => array(),
				'id'    => array(),
				'class' => array(),
				'alt'   => array(),
				'style' => array(),
			),
			'p'      => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'span'   => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'strong' => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
			'label'  => array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			),
		);
		$html5_tags   = array( 'article', 'section', 'aside', 'details', 'figcaption', 'figure', 'footer', 'header', 'main', 'mark', 'nav', 'summary', 'time' );
		foreach ( $html5_tags as $html5_tag ) {
			$allowed_html[ $html5_tag ] = array(
				'id'    => array(),
				'class' => array(),
				'style' => array(),
			);
		}
		return $allowed_html;
	}

	/**
	 * Returns list of allowed protocols, for use in declaration of wp_kset field validation.
	 * N.B. JavaScript is specifically disallowed for security reasons.
	 * Don't even trust your own database, as you don't know if another plugin has written to your settings.
	 *
	 * @since 1.0
	 * @return array
	 */
	public static function gdpr_allowed_protocols() {
		// Additional options : ftp, ftps, mailto, news, irc, gopher, nntp, feed, telnet.
		return array( 'http', 'https' );
	}

	/**
	 * Returns default settings.
	 * If you override the settings here, be ultra careful to use escape characters.
	 *
	 * @since 1.0
	 * @param string $key Return default settings for particular key.
	 * @return array|mixed
	 */
	public static function gdpr_get_default_settings( $key = '' ) {
		$settings = array(
			'gdpr_cookie_bar_logo_url_holder'        => '',
			'animate_speed_hide'                     => '500',
			'animate_speed_show'                     => '500',

			'background'                             => '#ffffff',
			'opacity'                                => '0.80',
			'background_border_width'                => '0',
			'background_border_style'                => 'none',
			'background_border_color'                => '#ffffff',
			'background_border_radius'               => '0',
			'template'                               => 'banner-default',
			'banner_template'                        => 'banner-default',
			'popup_template'                         => 'popup-default',
			'widget_template'                        => 'widget-default',

			'button_accept_text'                     => 'Accept',
			'button_accept_url'                      => '#',
			'button_accept_action'                   => '#cookie_action_close_header',
			'button_accept_link_color'               => '#ffffff',
			'button_accept_button_color'             => '#18a300',
			'button_accept_new_win'                  => false,
			'button_accept_as_button'                => true,
			'button_accept_button_size'              => 'medium',
			'button_accept_is_on'                    => true,
			'button_accept_button_opacity'           => '1', // 0 to 1.
			'button_accept_button_border_width'      => '0', // in pixel.
			'button_accept_button_border_style'      => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_accept_button_border_color'      => '#18a300',
			'button_accept_button_border_radius'     => '0', // in pixel.

			'button_accept_all_is_on'                => false,
			'button_accept_all_text'                 => 'Accept All',
			'button_accept_all_link_color'           => '#ffffff',
			'button_accept_all_as_button'            => true,
			'button_accept_all_action'               => '#cookie_action_close_header',
			'button_accept_all_url'                  => '#',
			'button_accept_all_new_win'              => false,
			'button_accept_all_button_color'         => '#18a300',
			'button_accept_all_button_size'          => 'medium',
			'button_accept_all_btn_border_style'     => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_accept_all_btn_border_color'     => '#18a300',
			'button_accept_all_btn_opacity'          => '1', // 0 to 1.
			'button_accept_all_btn_border_width'     => '0', // in pixel.
			'button_accept_all_btn_border_radius'    => '0', // in pixel.

			'button_readmore_text'                   => 'Read More',
			'button_readmore_url'                    => '#',
			'button_readmore_action'                 => 'CONSTANT_OPEN_URL',
			'button_readmore_link_color'             => '#359bf5',
			'button_readmore_button_color'           => '#333333',
			'button_readmore_new_win'                => false,
			'button_readmore_as_button'              => false,
			'button_readmore_button_size'            => 'medium',
			'button_readmore_is_on'                  => true,
			'button_readmore_url_type'               => true,
			'button_readmore_wp_page'                => false,
			'button_readmore_page'                   => '0',
			'button_readmore_button_opacity'         => '1', // 0 to 1.
			'button_readmore_button_border_width'    => '0', // in pixel.
			'button_readmore_button_border_style'    => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_readmore_button_border_color'    => '#333333',
			'button_readmore_button_border_radius'   => '0', // in pixel.

			'button_decline_text'                    => 'Decline',
			'button_decline_url'                     => '#',
			'button_decline_action'                  => '#cookie_action_close_header_reject',
			'button_decline_link_color'              => '#ffffff',
			'button_decline_button_color'            => '#333333',
			'button_decline_new_win'                 => false,
			'button_decline_as_button'               => true,
			'button_decline_button_size'             => 'medium',
			'button_decline_is_on'                   => true,
			'button_decline_button_opacity'          => '1', // 0 to 1.
			'button_decline_button_border_width'     => '0', // in pixel.
			'button_decline_button_border_style'     => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_decline_button_border_color'     => '#333333',
			'button_decline_button_border_radius'    => '0', // in pixel.

			'button_settings_text'                   => 'Cookie Settings',
			'button_settings_url'                    => '#',
			'button_settings_action'                 => '#cookie_action_settings',
			'button_settings_link_color'             => '#ffffff',
			'button_settings_button_color'           => '#333333',
			'button_settings_new_win'                => false,
			'button_settings_as_button'              => true,
			'button_settings_button_size'            => 'medium',
			'button_settings_is_on'                  => true,
			'button_settings_display_cookies'        => true,
			'button_settings_as_popup'               => true,
			'button_settings_layout_skin'            => 'layout-default',
			'button_settings_button_opacity'         => '1', // 0 to 1.
			'button_settings_button_border_width'    => '0', // in pixel.
			'button_settings_button_border_style'    => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_settings_button_border_color'    => '#333333',
			'button_settings_button_border_radius'   => '0', // in pixel.

			'button_donotsell_text'                  => 'Do Not Sell My Personal Information',
			'button_donotsell_link_color'            => '#359bf5',
			'button_donotsell_as_button'             => false,
			'button_donotsell_is_on'                 => true,

			'button_confirm_text'                    => 'Confirm',
			'button_confirm_button_color'            => '#18a300',
			'button_confirm_link_color'              => '#ffffff',
			'button_confirm_as_button'               => true,
			'button_confirm_button_size'             => 'medium',
			'button_confirm_is_on'                   => true,
			'button_confirm_button_opacity'          => '1', // 0 to 1.
			'button_confirm_button_border_width'     => '0', // in pixel.
			'button_confirm_button_border_style'     => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_confirm_button_border_color'     => '#18a300',
			'button_confirm_button_border_radius'    => '0', // in pixel.

			'button_cancel_text'                     => 'Cancel',
			'button_cancel_button_color'             => '#333333',
			'button_cancel_link_color'               => '#ffffff',
			'button_cancel_as_button'                => true,
			'button_cancel_button_size'              => 'medium',
			'button_cancel_is_on'                    => true,
			'button_cancel_button_opacity'           => '1', // 0 to 1.
			'button_cancel_button_border_width'      => '0', // in pixel.
			'button_cancel_button_border_style'      => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_cancel_button_border_color'      => '#333333',
			'button_cancel_button_border_radius'     => '0', // in pixel.

			'cookie_bar_color1' 					 => "#ffffff",
      		'cookie_bar_opacity1' 					 => "0.80",
      		'cookie_bar_border_width1'				 => "0",
      		'cookie_font1'							 => "inherit",
      		'cookie_text_color1'					 => "#000000",
      		'border_style1'							 => "none",
      		'cookie_border_color1'					 => "#ffffff",
      		'cookie_bar_border_radius1'				 => "0",

			'cookie_bar_color2' 					 => "#ffffff",
      		'cookie_bar_opacity2' 					 => "0.80",
      		'cookie_bar_border_width2'				 => "0",
      		'cookie_font2'							 => "inherit",
      		'cookie_text_color2'					 => "#000000",
      		'border_style2'							 => "none",
      		'cookie_border_color2'					 => "#ffffff",
      		'cookie_bar_border_radius2'				 => "0",

			'button_accept_text1'                     => 'Accept',
			'button_accept_url1'                      => '#',
			'button_accept_action1'                   => '#cookie_action_close_header',
			'button_accept_link_color1'               => '#ffffff',
			'button_accept_button_color1'             => '#18a300',
			'button_accept_new_win1'                  => false,
			'button_accept_as_button1'                => true,
			'button_accept_button_size1'              => 'medium',
			'button_accept_is_on1'                    => true,
			'button_accept_button_opacity1'           => '1', // 0 to 1.
			'button_accept_button_border_width1'      => '0', // in pixel.
			'button_accept_button_border_style1'      => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_accept_button_border_color1'      => '#18a300',
			'button_accept_button_border_radius1'     => '0', // in pixel.

			'button_accept_all_is_on1'               => false,
			'button_accept_all_text1'                => 'Accept All',
			'button_accept_all_link_color1'          => '#ffffff',
			'button_accept_all_as_button1'           => true,
			'button_accept_all_action1'              => '#cookie_action_close_header',
			'button_accept_all_url1'                 => '#',
			'button_accept_all_new_win1'             => false,
			'button_accept_all_button_color1'        => '#18a300',
			'button_accept_all_button_size1'         => 'medium',
			'button_accept_all_btn_border_style1'    => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_accept_all_btn_border_color1'    => '#18a300',
			'button_accept_all_btn_opacity1'         => '1', // 0 to 1.
			'button_accept_all_btn_border_width1'    => '0', // in pixel.
			'button_accept_all_btn_border_radius1'   => '0', // in pixel.

			'button_decline_text1'                   => 'Decline',
			'button_decline_url1'                    => '#',
			'button_decline_action1'                 => '#cookie_action_close_header_reject',
			'button_decline_link_color1'             => '#ffffff',
			'button_decline_button_color1'           => '#333333',
			'button_decline_new_win1'                => false,
			'button_decline_as_button1'              => true,
			'button_decline_button_size1'            => 'medium',
			'button_decline_is_on1'                  => true,
			'button_decline_button_opacity1'         => '1', // 0 to 1.
			'button_decline_button_border_width1'    => '0', // in pixel.
			'button_decline_button_border_style1'    => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_decline_button_border_color1'    => '#333333',
			'button_decline_button_border_radius1'   => '0', // in pixel.
			'multiple_legislation_accept_all_border_radius1'   => '0', // in pixel.

			'button_settings_text1'                  => 'Cookie Settings',
			'button_settings_url1'                   => '#',
			'button_settings_action1'                => '#cookie_action_settings',
			'button_settings_link_color1'            => '#ffffff',
			'button_settings_button_color1'          => '#333333',
			'button_settings_new_win1'               => false,
			'button_settings_as_button1'             => true,
			'button_settings_button_size1'           => 'medium',
			'button_settings_is_on1'                 => true,
			'button_settings_display_cookies1'       => true,
			'button_settings_as_popup1'              => false,
			'button_settings_layout_skin1'           => 'layout-default',
			'button_settings_button_opacity1'        => '1', // 0 to 1.
			'button_settings_button_border_width1'   => '0', // in pixel.
			'button_settings_button_border_style1'   => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_settings_button_border_color1'   => '#333333',
			'button_settings_button_border_radius1'  => '0', // in pixel.

			'button_donotsell_text1'                 => 'Do Not Sell My Personal Information',
			'button_donotsell_link_color1'           => '#359bf5',
			'button_donotsell_as_button1'            => false,
			'button_donotsell_is_on1'                => true,

			'button_confirm_text1'                   => 'Confirm',
			'button_confirm_button_color1'           => '#18a300',
			'button_confirm_link_color1'             => '#ffffff',
			'button_confirm_as_button1'              => true,
			'button_confirm_button_size1'            => 'medium',
			'button_confirm_is_on1'                  => true,
			'button_confirm_button_opacity1'         => '1', // 0 to 1.
			'button_confirm_button_border_width1'    => '0', // in pixel.
			'button_confirm_button_border_style1'    => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_confirm_button_border_color1'    => '#18a300',
			'button_confirm_button_border_radius1'   => '0', // in pixel.

			'button_cancel_text1'                    => 'Cancel',
			'button_cancel_button_color1'            => '#333333',
			'button_cancel_link_color1'              => '#ffffff',
			'button_cancel_as_button1'               => true,
			'button_cancel_button_size1'             => 'medium',
			'button_cancel_is_on1'                   => true,
			'button_cancel_button_opacity1'          => '1', // 0 to 1.
			'button_cancel_button_border_width1'     => '0', // in pixel.
			'button_cancel_button_border_style1'     => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_cancel_button_border_color1'     => '#333333',
			'button_cancel_button_border_radius1'    => '0', // in pixel.

			'button_accept_text2'                    => 'Accept',
			'button_accept_url2'                     => '#',
			'button_accept_action2'                  => '#cookie_action_close_header',
			'button_accept_link_color2'              => '#ffffff',
			'button_accept_button_color2'            => '#18a300',
			'button_accept_new_win2'                 => false,
			'button_accept_as_button2'               => true,
			'button_accept_button_size2'             => 'medium',
			'button_accept_is_on2'                   => true,
			'button_accept_button_opacity2'          => '1', // 0 to 1.
			'button_accept_button_border_width2'     => '0', // in pixel.
			'button_accept_button_border_style2'     => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_accept_button_border_color2'     => '#18a300',
			'button_accept_button_border_radius2'    => '0', // in pixel.

			'button_accept_all_is_on2'               => false,
			'button_accept_all_text2'                => 'Accept All',
			'button_accept_all_link_color2'          => '#ffffff',
			'button_accept_all_as_button2'           => true,
			'button_accept_all_action2'              => '#cookie_action_close_header',
			'button_accept_all_url2'                 => '#',
			'button_accept_all_new_win2'             => false,
			'button_accept_all_button_color2'        => '#18a300',
			'button_accept_all_button_size2'         => 'medium',
			'button_accept_all_btn_border_style2'    => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_accept_all_btn_border_color2'    => '#18a300',
			'button_accept_all_btn_opacity2'         => '1', // 0 to 1.
			'button_accept_all_btn_border_width2'    => '0', // in pixel.
			'button_accept_all_btn_border_radius2'   => '0', // in pixel.

			'button_decline_text2'                   => 'Decline',
			'button_decline_url2'                    => '#',
			'button_decline_action2'                 => '#cookie_action_close_header_reject',
			'button_decline_link_color2'             => '#ffffff',
			'button_decline_button_color2'           => '#333333',
			'button_decline_new_win2'                => false,
			'button_decline_as_button2'              => true,
			'button_decline_button_size2'            => 'medium',
			'button_decline_is_on2'                  => true,
			'button_decline_button_opacity2'         => '1', // 0 to 1.
			'button_decline_button_border_width2'    => '0', // in pixel.
			'button_decline_button_border_style2'    => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_decline_button_border_color2'    => '#333333',
			'button_decline_button_border_radius2'   => '0', // in pixel.

			'button_settings_text2'                  => 'Cookie Settings',
			'button_settings_url2'                   => '#',
			'button_settings_action2'                => '#cookie_action_settings',
			'button_settings_link_color2'            => '#ffffff',
			'button_settings_button_color2'          => '#333333',
			'button_settings_new_win2'               => false,
			'button_settings_as_button2'             => true,
			'button_settings_button_size2'           => 'medium',
			'button_settings_is_on2'                 => true,
			'button_settings_display_cookies2'       => true,
			'button_settings_as_popup2'              => false,
			'button_settings_layout_skin2'           => 'layout-default',
			'button_settings_button_opacity2'        => '1', // 0 to 1.
			'button_settings_button_border_width2'   => '0', // in pixel.
			'button_settings_button_border_style2'   => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_settings_button_border_color2'   => '#333333',
			'button_settings_button_border_radius2'  => '0', // in pixel.

			'button_donotsell_text2'                 => 'Do Not Sell My Personal Information',
			'button_donotsell_link_color2'           => '#359bf5',
			'button_donotsell_as_button2'            => false,
			'button_donotsell_is_on2'                => true,

			'button_confirm_text2'                   => 'Confirm',
			'button_confirm_button_color2'           => '#18a300',
			'button_confirm_link_color2'             => '#ffffff',
			'button_confirm_as_button2'              => true,
			'button_confirm_button_size2'            => 'medium',
			'button_confirm_is_on2'                  => true,
			'button_confirm_button_opacity2'         => '1', // 0 to 1.
			'button_confirm_button_border_width2'    => '0', // in pixel.
			'button_confirm_button_border_style2'    => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_confirm_button_border_color2'    => '#18a300',
			'button_confirm_button_border_radius2'   => '0', // in pixel.

			'button_cancel_text2'                    => 'Cancel',
			'button_cancel_button_color2'            => '#333333',
			'button_cancel_link_color2'              => '#ffffff',
			'button_cancel_as_button2'               => true,
			'button_cancel_button_size2'             => 'medium',
			'button_cancel_is_on2'                   => true,
			'button_cancel_button_opacity2'          => '1', // 0 to 1.
			'button_cancel_button_border_width2'     => '0', // in pixel.
			'button_cancel_button_border_style2'     => 'none', // none, solid, hidden, dashed, dotted, double, groove, ridge, inset, outset.
			'button_cancel_button_border_color2'     => '#333333',
			'button_cancel_button_border_radius2'    => '0', // in pixel.

			'font_family'                            => 'inherit', // Pick the family, not the easy name (see helper function below).

			'is_on'                                => true,
			'is_iabtcf_on'                         => false,
			'is_gacm_on'						   => false,
			'is_eu_on'                             => false,
			'is_ccpa_on'                           => false,
			'is_ccpa_iab_on'                       => false,
			'is_worldwide_on'                      => true,
			'is_selectedCountry_on'                => false,
			'logging_on'                           => true,
			'show_credits'                         => true,
			'is_ticked'                            => false,
			'show_again'                           => true,
			'is_script_blocker_on'                 => false,
			'auto_hide'                            => false,
			'auto_banner_initialize'               => false,
			'auto_generated_banner'               => false,
			'auto_scroll'                          => false,
			'auto_click'                           => false,
			'auto_scroll_reload'                   => false,
			'accept_reload'                        => false,
			'decline_reload'                       => false,
			'delete_on_deactivation'               => false,

			'show_again_position'                    => 'right', // 'left' = left | 'right' = right.
			'show_again_text'                        => 'Cookie Settings',
			'show_again_margin'                      => '5',
			'button_revoke_consent_text_color'       => '#000000',
			'button_revoke_consent_background_color' => '#ffffff',
			'auto_hide_delay'                        => '10000',
			'auto_banner_initialize_delay'           => '10000',
			'auto_scroll_offset'                     => '10',
			'cookie_expiry'                          => '365',
			'show_again_div_id'                      => '#gdpr-cookie-consent-show-again',
			'notify_animate_hide'                    => true,
			'notify_animate_show'                    => false,
			'notify_message'                         => addslashes( 'This website uses cookies to improve your experience. We\'ll assume you\'re ok with this, but you can opt-out if you wish.' ),
			'notify_message_lgpd'                    => addslashes( 'This website uses cookies for technical and other purposes as specified in the cookie policy. We\'ll assume you\'re ok with this, but you can opt-out if you wish.' ),
			'notify_message_eprivacy'                => addslashes( 'This website uses cookies to improve your experience. We\'ll assume you\'re ok with this, but you can opt-out if you wish.' ),
			'notify_message_ccpa'                    => addslashes( 'In case of sale of your personal information, you may opt out by using the link' ),
			'optout_text'                            => addslashes( 'Do you really wish to opt-out?' ),
			'notify_div_id'                          => '#gdpr-cookie-consent-bar',
			'notify_position_vertical'               => 'bottom', // 'top' = header | 'bottom' = footer.
			'notify_position_horizontal'             => 'left', // 'left' = left | 'right' = right.
			'text'                                   => '#000000',
			'use_color_picker'                       => true,
			'bar_heading_text'                       => '',
			'bar_heading_lgpd_text'                  => '',
			'cookie_bar_as'                          => 'banner', // banner | popup | widget.
			'cookie_usage_for'                       => 'gdpr',
			'popup_overlay'                          => true,
			'about_message'                          => addslashes( ( 'Cookies are small text files that can be used by websites to make a user\'s experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.' ) ),
			'about_message_lgpd'                     => addslashes( ( 'Cookies are small text files that can be used by websites to make a user\'s experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.' ) ),
			'header_scripts'                         => '',
			'body_scripts'                           => '',
			'footer_scripts'                         => '',
			'restrict_posts'                         => array(),
			'select_pages'                           => array(),
			'gdpr_css_text'                          => '',
			'do_not_track_on'                        => false,
			'data_req_editor_message'                => '&lt;p&gt;Hi {name}&lt;/p&gt;&lt;p&gt;We have received your request on {blogname}. Depending on the specific request and legal obligations we might follow-up.&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;Kind regards,&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;{blogname}&lt;/p&gt;',
			'data_req_subject'                       => 'We have received your request',
			'enable_safe'                            => false,
			'consent_forward'                        => false,
			'select_sites'                           => array(),
			'data_reqs_on'                           => true,
			'select_countries'                       => array(),
		);
		$settings = apply_filters( 'gdprcookieconsent_default_settings', $settings );
		return '' !== $key ? $settings[ $key ] : $settings;
	}

	/**
	 * Returns sanitised content based on field-specific rules defined here
	 * used for both read AND write operations.
	 *
	 * @param string $key Key for the setting.
	 * @param string $value Value for the setting.
	 *
	 * @return bool|null|string
	 */
	public static function gdpr_sanitise_settings( $key, $value ) {
		$ret = null;
		switch ( $key ) {
			// Convert all boolean values from text to bool.
			case 'is_on':
			case 'is_iabtcf_on':
			case 'is_eu_on':
			case 'is_ccpa_on':
			case 'is_ccpa_iab_on':
			case 'is_script_blocker_on':
			case 'show_again':
			case 'auto_hide':
			case 'is_worldwide_on':
			case 'is_selectedCountry_on':
			case 'auto_banner_initialize':
			case 'auto_generated_banner':
			case 'auto_scroll':
			case 'auto_click':
			case 'auto_scroll_reload':
			case 'accept_reload':
			case 'decline_reload':
			case 'delete_on_deactivation':
			case 'logging_on':
			case 'show_credits':
			case 'is_ticked':
			case 'notify_animate_hide':
			case 'notify_animate_show':
			case 'use_color_picker':
			case 'popup_overlay':
			case 'button_accept_new_win':
			case 'button_accept_as_button':
			case 'button_accept_is_on':
			case 'button_accept_all_is_on':
			case 'button_accept_all_as_button':
			case 'button_accept_all_new_win':
			case 'button_readmore_new_win':
			case 'button_readmore_as_button':
			case 'button_readmore_is_on':
			case 'button_readmore_url_type':
			case 'button_readmore_wp_page':
			case 'button_decline_new_win':
			case 'button_decline_as_button':
			case 'button_decline_is_on':
			case 'button_settings_new_win':
			case 'button_settings_as_button':
			case 'button_settings_is_on':
			case 'button_settings_display_cookies':
			case 'button_settings_as_popup':
			case 'button_donotsell_as_button':
			case 'button_donotsell_is_on':
			case 'button_cancel_as_button':
			case 'button_cancel_is_on':
			case 'button_confirm_as_button':
			case 'button_confirm_is_on':
			case 'data_reqs_on':
				// consent forward .
			case 'consent_forward':
				if ( 'true' === $value || true === $value ) {
					$ret = true;
				} elseif ( 'false' === $value || false === $value ) {
					$ret = false;
				} else {
					// Unexpected value returned from radio button, go fix the HTML.
					// Failover = assign null.
					$ret = 'fffffff';
				}
				break;
			// Any hex color e.g. '#f00', '#FE01ab' '#ff0000' but not 'f00' or 'ff0000'.
			case 'background':
			case 'background_border_color':
			case 'text':
			case 'button_accept_link_color':
			case 'button_accept_button_color':
			case 'button_accept_button_border_color':
			case 'button_accept_all_link_color':
			case 'button_accept_all_button_color':
			case 'button_accept_all_btn_border_color':
			case 'button_readmore_link_color':
			case 'button_revoke_consent_background_color':
			case 'button_revoke_consent_text_color':
			case 'button_readmore_button_color':
			case 'button_readmore_button_border_color':
			case 'button_decline_link_color':
			case 'button_decline_button_color':
			case 'button_decline_button_border_color':
			case 'button_settings_link_color':
			case 'button_settings_button_color':
			case 'button_settings_button_border_color':
			case 'button_donotsell_link_color':
			case 'button_confirm_button_color':
			case 'button_confirm_link_color':
			case 'button_confirm_button_border_color':
			case 'button_cancel_button_color':
			case 'button_cancel_link_color':
			case 'button_cancel_button_border_color':
				if ( preg_match( '/^#[a-f0-9]{6}|#[a-f0-9]{3}$/i', $value ) ) {
					// Was: '/^#([0-9a-fA-F]{1,2}){3}$/i' which allowed e.g. '#00dd' (error).
					$ret = $value;
				} else {
					// Failover = assign '#000000' (black).
					$ret = '#000000';
				}
				break;
			// Allow some HTML, but no JavaScript. Note that deliberately NOT stripping out line breaks here, that's done when sending JavaScript parameter elsewhere.
			case 'about_message':
			case 'about_message_lgpd':
			case 'notify_message':
			case 'notify_message_lgpd':
			case 'notify_message_eprivacy':
			case 'notify_message_ccpa':
			case 'optout_text':
			case 'bar_heading_text':
			case 'bar_heading_lgpd_text':
				$ret = wp_kses( $value, self::gdpr_allowed_html(), self::gdpr_allowed_protocols() );
				break;
			// URLs only.
			case 'button_accept_url':
			case 'button_accept_all_url':
			case 'button_readmore_url':
			case 'button_decline_url':
			case 'button_settings_url':
				$ret = esc_url( $value );
				break;
			case 'header_scripts':
			case 'body_scripts':
			case 'footer_scripts':
				$ret = trim( stripslashes( $value ) );
				break;
			case 'restrict_posts':
				$ret = $value;
				break;
			// hide banner.
			case 'select_pages':
				$ret = $value;
				break;
				// consent forward.
			case 'select_sites':
				$ret = $value;
				break;
			case 'select_countries':
				$ret = $value;
				break;
			// Basic sanitisation for all the rest.
			default:
				$ret = sanitize_text_field( $value );
				break;
		}
		if ( 'fffffff' === $ret ) {
			$ret = false;
		}
		return $ret;
	}

	/**
	 * Get current settings.
	 *
	 * @return array|mixed
	 */
	public static function gdpr_get_settings() {
		$settings             = self::gdpr_get_default_settings();
		self::$stored_options = get_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
		if ( ! empty( self::$stored_options ) ) {
			foreach ( self::$stored_options as $key => $option ) {
				$settings[ $key ] = self::gdpr_sanitise_settings( $key, $option );
			}
		}
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $settings );
		return $settings;
	}

	/**
	 * Save Vendor Data.
	 *
	 * @return array|mixed
	 */
	public static function gdpr_save_vendors($data) {
		self::$stored_options = get_option( GDPR_COOKIE_CONSENT_SETTINGS_VENDOR );

		$iabtcf_consent_data = [];
		$iabtcf_consent_data["consent"]=[];
		$iabtcf_consent_data["legint"]=[];
		$iabtcf_consent_data["purpose_consent"]=[];
		$iabtcf_consent_data["purpose_legint"]=[];
		$iabtcf_consent_data["feature_consent"]=[];
		$iabtcf_consent_data["allVendorsSelected"]=false;
		$iabtcf_consent_data["allVendorsRejected"]=false;
		$iabtcf_consent_data["tcString"]="none";
		
		if($data) {
			$iabtcf_consent_data["allvendorIds"]= $data->allvendors;
			$iabtcf_consent_data["allVendorsWithLegint"]= $data->allLegintVendors;
			$iabtcf_consent_data["allPurposesWithLegint"]= $data->allLegintPurposes;
			$iabtcf_consent_data["allPurposeIds"]= $data->allPurposes;
			$iabtcf_consent_data["allSpecialFeatureIds"]= $data->allSpecialFeatures;
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_VENDOR, $data );
			if(! get_option( GDPR_COOKIE_CONSENT_SETTINGS_VENDOR_CONSENT )) {
				update_option( GDPR_COOKIE_CONSENT_SETTINGS_VENDOR_CONSENT, $iabtcf_consent_data );
			}
		}
		else{
		}
	}

	/**
	 * Get Vendor Data.
	 *
	 * @return array|mixed
	 */
	public static function gdpr_get_vendors() {
		$newvendors = '{
						"gvlSpecificationVersion": 3,
						"vendorListVersion": 80,
						"tcfPolicyVersion": 5,
						"lastUpdated": "2024-11-14T16:07:29Z",
						"purposes": [
							{
							"id": 1,
							"name": "Store and/or access information on a device",
							"description": "Cookies, device or similar online identifiers (e.g. login-based identifiers, randomly assigned identifiers, network based identifiers) together with other information (e.g. browser type and information, language, screen size, supported technologies etc.) can be stored or read on your device to recognise it each time it connects to an app or to a website, for one or several of the purposes presented here.",
							"illustrations": []
							},
							{
							"id": 2,
							"name": "Use limited data to select advertising",
							"description": "Advertising presented to you on this service can be based on limited data, such as the website or app you are using, your non-precise location, your device type or which content you are (or have been) interacting with (for example, to limit the number of times an ad is presented to you).",
							"illustrations": []
							},
							{
							"id": 3,
							"name": "Create profiles for personalised advertising",
							"description": "Information about your activity on this service (such as forms you submit, content you look at) can be stored and combined with other information about you (for example, information from your previous activity on this service and other websites or apps) or similar users. This is then used to build or improve a profile about you (that might include possible interests and personal aspects). Your profile can be used (also later) to present advertising that appears more relevant based on your possible interests by this and other entities.",
							"illustrations": []
							},
							{
							"id": 4,
							"name": "Use profiles to select personalised advertising",
							"description": "Advertising presented to you on this service can be based on your advertising profiles, which can reflect your activity on this service or other websites or apps (like the forms you submit, content you look at), possible interests and personal aspects.",
							"illustrations": []
							},
							{
							"id": 5,
							"name": "Create profiles to personalise content",
							"description": "Information about your activity on this service (for instance, forms you submit, non-advertising content you look at) can be stored and combined with other information about you (such as your previous activity on this service or other websites or apps) or similar users. This is then used to build or improve a profile about you (which might for example include possible interests and personal aspects). Your profile can be used (also later) to present content that appears more relevant based on your possible interests, such as by adapting the order in which content is shown to you, so that it is even easier for you to find content that matches your interests.",
							"illustrations": []
							},
							{
							"id": 6,
							"name": "Use profiles to select personalised content",
							"description": "Content presented to you on this service can be based on your content personalisation profiles, which can reflect your activity on this or other services (for instance, the forms you submit, content you look at), possible interests and personal aspects. This can for example be used to adapt the order in which content is shown to you, so that it is even easier for you to find (non-advertising) content that matches your interests.",
							"illustrations": []
							},
							{
							"id": 7,
							"name": "Measure advertising performance",
							"description": "Information regarding which advertising is presented to you and how you interact with it can be used to determine how well an advert has worked for you or other users and whether the goals of the advertising were reached. For instance, whether you saw an ad, whether you clicked on it, whether it led you to buy a product or visit a website, etc. This is very helpful to understand the relevance of advertising campaigns.",
							"illustrations": []
							},
							{
							"id": 8,
							"name": "Measure content performance",
							"description": "Information regarding which content is presented to you and how you interact with it can be used to determine whether the (non-advertising) content e.g. reached its intended audience and matched your interests. For instance, whether you read an article, watch a video, listen to a podcast or look at a product description, how long you spent on this service and the web pages you visit etc. This is very helpful to understand the relevance of (non-advertising) content that is shown to you. ",
							"illustrations": []
							},
							{
							"id": 9,
							"name": "Understand audiences through statistics or combinations of data from different sources",
							"description": "Reports can be generated based on the combination of data sets (like user profiles, statistics, market research, analytics data) regarding your interactions and those of other users with advertising or (non-advertising) content to identify common characteristics (for instance, to determine which target audiences are more receptive to an ad campaign or to certain contents).",
							"illustrations": []
							},
							{
							"id": 10,
							"name": "Develop and improve services",
							"description": "Information about your activity on this service, such as your interaction with ads or content, can be very helpful to improve products and services and to build new products and services based on user interactions, the type of audience, etc. This specific purpose does not include the development or improvement of user profiles and identifiers.",
							"illustrations": []
							},
							{
							"id": 11,
							"name": "Use limited data to select content",
							"description": "Content presented to you on this service can be based on limited data, such as the website or app you are using, your non-precise location, your device type, or which content you are (or have been) interacting with (for example, to limit the number of times a video or an article is presented to you).",
							"illustrations": []
							}
						],
						"specialPurposes": [
							{
							"id": 1,
							"name": "Ensure security, prevent and detect fraud, and fix errors\n",
							"description": "Your data can be used to monitor for and prevent unusual and possibly fraudulent activity (for example, regarding advertising, ad clicks by bots), and ensure systems and processes work properly and securely. It can also be used to correct any problems you, the publisher or the advertiser may encounter in the delivery of content and ads and in your interaction with them.",
							"illustrations": []
							},
							{
							"id": 2,
							"name": "Deliver and present advertising and content",
							"description": "Certain information (like an IP address or device capabilities) is used to ensure the technical compatibility of the content or advertising, and to facilitate the transmission of the content or ad to your device.",
							"illustrations": []
							},
							{
							"id": 3,
							"name": "Save and communicate privacy choices",
							"description": "The choices you make regarding the purposes and entities listed in this notice are saved and made available to those entities in the form of digital signals (such as a string of characters). This is necessary in order to enable both this service and those entities to respect such choices.",
							"illustrations": []
							}
						],
						"features": [
							{
							"id": 1,
							"name": "Match and combine data from other data sources",
							"description": "Information about your activity on this service may be matched and combined with other information relating to you and originating from various sources (for instance your activity on a separate online service, your use of a loyalty card in-store, or your answers to a survey), in support of the purposes explained in this notice.",
							"illustrations": []
							},
							{
							"id": 2,
							"name": "Link different devices",
							"description": "In support of the purposes explained in this notice, your device might be considered as likely linked to other devices that belong to you or your household (for instance because you are logged in to the same service on both your phone and your computer, or because you may use the same Internet connection on both devices).",
							"illustrations": []
							},
							{
							"id": 3,
							"name": "Identify devices based on information transmitted automatically",
							"description": "Your device might be distinguished from other devices based on information it automatically sends when accessing the Internet (for instance, the IP address of your Internet connection or the type of browser you are using) in support of the purposes exposed in this notice.",
							"illustrations": []
							}
						],
						"specialFeatures": [
							{
							"id": 1,
							"name": "Use precise geolocation data",
							"description": "With your acceptance, your precise location (within a radius of less than 500 metres) may be used in support of the purposes explained in this notice.",
							"illustrations": []
							},
							{
							"id": 2,
							"name": "Actively scan device characteristics for identification",
							"description": "With your acceptance, certain characteristics specific to your device might be requested and used to distinguish it from other devices (such as the installed fonts or plugins, the resolution of your screen) in support of the purposes explained in this notice.",
							"illustrations": []
							}
						],
						"dataCategories": [
							{
							"id": 1,
							"name": "IP addresses",
							"description": "Your IP address is a number assigned by your Internet Service Provider to any Internet connection. It is not always specific to your device and is not always a stable identifier.\nIt is used to route information on the Internet and display online content (including ads) on your connected device."
							},
							{
							"id": 2,
							"name": "Device characteristics",
							"description": "Technical characteristics about the device you are using that are not unique to you, such as the language, the time zone or the operating system."
							},
							{
							"id": 3,
							"name": "Device identifiers",
							"description": "A device identifier is a unique string of characters assigned to your device or browser by means of a cookie or other storage technologies. \nIt may be created or accessed to recognise your device e.g. across web pages from the same site or across multiple sites or apps."
							},
							{
							"id": 4,
							"name": "Probabilistic identifiers",
							"description": "A probabilistic identifier can be created by combining characteristics associated with your device (the type of browser or operating system used) and the IP address of the Internet connection. If you give your agreement, additional characteristics (e.g. the installed font or screen resolution) can also be combined to improve precision of the probabilistic identifier.\nSuch an identifier is considered \"probabilistic\" because several devices can share the same characteristics and Internet connection. It may be used to recognise your device across e.g. web pages from the same site or across multiple sites or apps."
							},
							{
							"id": 5,
							"name": "Authentication-derived identifiers",
							"description": "Where an identifier is created on the basis of authentication data, such as contact details associated with online accounts you have created on websites or apps (e.g. e-mail address, phone number) or customer identifiers (e.g. identifier provided by your telecom operator), that identifier may be used to recognise you across websites, apps and devices when you are logged-in with the same contact details."
							},
							{
							"id": 6,
							"name": "Browsing and interaction data",
							"description": "Your online activity such as the websites you visit, apps you are using, the content you search for on this service,  or your interactions with content or ads, such as the number of times you have seen a specific content or ad or whether you clicked on it.\n"
							},
							{
							"id": 7,
							"name": "User-provided data",
							"description": "The information you may have provided by way of declaration via a form (e.g. feedback, a comment) or when creating an account (e.g. your age, your occupation)."
							},
							{
							"id": 8,
							"name": "Non-precise location data",
							"description": "An approximation of your location, expressed as an area with a radius of at least 500 meters. Your approximate location can be deduced from e.g. the IP address of your connection."
							},
							{
							"id": 9,
							"name": "Precise location data",
							"description": "Your precise location within a radius of less than 500 meters based on your GPS coordinates. It may be used only with your acceptance."
							},
							{
							"id": 10,
							"name": "Users’ profiles",
							"description": "Certain characteristics (e.g. your possible interests, your purchase intentions, your consumer profile) may be inferred or modeled from your previous online activity (e.g. the content you viewed or the service you used, your time spent on various online content and services) or the information you have provided (e.g. your age, your occupation)."
							},
							{
							"id": 11,
							"name": "Privacy choices",
							"description": "Your preferences regarding the processing of your data, based on the information you have received."
							}
						],
						"vendors": [
							{
							"id": 1,
							"name": "Exponential Interactive, Inc d/b/a VDX.tv",
							"purposes": [1, 2, 3, 4, 7, 8, 9, 10],
							"legIntPurposes": [],
							"flexiblePurposes": [2, 7, 8, 9, 10],
							"specialPurposes": [1, 2],
							"features": [1, 2, 3],
							"specialFeatures": [],
							"cookieMaxAgeSeconds": 7776000,
							"usesCookies": true,
							"cookieRefresh": true,
							"urls": [
								{
								"langId": "en",
								"privacy": "https://vdx.tv/privacy/",
								"legIntClaim": "https://cdnx.exponential.com/wp-content/uploads/2018/04/Balancing-Assessment-for-Legitimate-Interest-Publishers-v2.pdf"
								}
							],
							"usesNonCookieAccess": false,
							"dataRetention": {
								"stdRetention": 397,
								"purposes": {

								},
								"specialPurposes": {

								}
							},
							"dataDeclaration": [1, 3, 4, 6, 8, 10, 11],
							"deviceStorageDisclosureUrl": "https://vdxtv.expo.workers.dev"
							},
							{
							"id": 2,
							"name": "Captify Technologies Limited",
							"purposes": [1, 2, 3, 4, 7, 9, 10],
							"legIntPurposes": [],
							"flexiblePurposes": [],
							"specialPurposes": [1, 2, 3],
							"features": [2],
							"specialFeatures": [2],
							"cookieMaxAgeSeconds": 31536000,
							"usesCookies": true,
							"cookieRefresh": true,
							"urls": [
								{
								"langId": "en",
								"privacy": "https://www.captifytechnologies.com/privacy-notice/",
								"legIntClaim": "https://www.captifytechnologies.com/privacy-notice/"
								}
							],
							"usesNonCookieAccess": true,
							"dataRetention": {
								"stdRetention": 365,
								"purposes": {

								},
								"specialPurposes": {

								}
							},
							"dataDeclaration": [1, 2, 4, 6, 11],
							"deviceStorageDisclosureUrl": "https://static.dp.cpx.to/gvl/deviceStorageDisclosure.json"
							},
							{
							"id": 4,
							"name": "Roq.ad GmbH",
							"purposes": [1, 2, 3, 4, 7, 9, 10],
							"legIntPurposes": [],
							"flexiblePurposes": [],
							"specialPurposes": [3],
							"features": [1, 2, 3],
							"specialFeatures": [1],
							"cookieMaxAgeSeconds": 31536000,
							"usesCookies": true,
							"cookieRefresh": true,
							"urls": [
								{
								"langId": "en",
								"privacy": "https://www.roq.ad/privacy-policy-roqad/",
								"legIntClaim": "https://www.roq.ad/privacy-policy-roqad/"
								}
							],
							"usesNonCookieAccess": false,
							"dataRetention": {
								"stdRetention": 365,
								"purposes": {

								},
								"specialPurposes": {
								"3": 90
								}
							},
							"dataDeclaration": [1, 2, 3, 4, 5, 6, 8, 9, 11],
							"deviceStorageDisclosureUrl": "https://roqad-public.s3.eu-central-1.amazonaws.com/tcf-disclosure.json"
							},
							{
							"id": 6,
							"name": "AdSpirit GmbH",
							"purposes": [1, 2, 3, 4, 7, 9],
							"legIntPurposes": [],
							"flexiblePurposes": [],
							"specialPurposes": [1, 2],
							"features": [3],
							"specialFeatures": [],
							"overflow": {
								"httpGetLimit": 32
							},
							"cookieMaxAgeSeconds": 2592000,
							"usesCookies": true,
							"cookieRefresh": true,
							"urls": [
								{
								"langId": "en",
								"privacy": "https://help.adspirit.de/privacy.php",
								"legIntClaim": "https://help.adspirit.de/privacy.php"
								},
								{
								"langId": "de",
								"privacy": "https://help.adspirit.de/privacy.php",
								"legIntClaim": "https://help.adspirit.de/privacy.php"
								}
							],
							"usesNonCookieAccess": false,
							"dataRetention": {
								"stdRetention": 60,
								"purposes": {
								"2": 14,
								"7": 14
								},
								"specialPurposes": {

								}
							},
							"dataDeclaration": [1, 2, 3, 4, 6, 8, 10, 11],
							"deviceStorageDisclosureUrl": "https://help.adspirit.de/deviceStorage.json"
							}
						],
						"allvendors":[1,2,4,6],
						"allLegintVendors":[],
						"featureVendorCount":[3, 3, 3],
						"allPurposes":[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
						"purposeVendorCount": [4, 4, 4, 4, 0, 0, 4, 1, 4, 3, 0],
						"allLegintPurposes": [2, 7, 8, 9, 10, 11],
						"legintPurposeVendorCount": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
						"allSpecialFeatures": [1, 2],
						"specialFeatureVendorCount": [1, 1],
						"specialPurposeVendorCount": [3, 3, 2],
						"purposeVendorMap": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]	
					}';
		return json_decode($newvendors);
		
	}

	public static function gdpr_get_all_vendors(){
		$vendors = new stdClass();
		$vendors = get_option( GDPR_COOKIE_CONSENT_SETTINGS_VENDOR );
		if( gettype($vendors) === "boolean"){
			$vendors = new stdClass();
			$vendors->vendors = new stdClass();
			$vendors->purposes =  new stdClass();
			$vendors->purposeVendorMap = new stdClass();
			$vendors->purposeVendorCount = 0;
			$vendors->legintPurposeVendorCount = 0;
			$vendors->specialPurposes = new stdClass();
			$vendors->specialPurposeVendorCount = 0;
			$vendors->features = new stdClass();
			$vendors->featureVendorCount = 0;
			$vendors->specialFeatures = new stdClass();
			$vendors->specialFeatureVendorCount = 0;
			$vendors->allvendors = "";

		} 
		return $vendors;
	}
	/**
	 * Get Vendor Data.
	 *
	 * @return array|mixed
	 */
	public static function gdpr_get_gacm_vendors() {
		// $settings             = self::gdpr_get_default_settings();
		$vendors = [];
		$vendors = get_option( GDPR_COOKIE_CONSENT_SETTINGS_GACM_VENDOR );
		if( gettype($vendors) === "boolean"){
			$vendors = [];
		} 
		return $vendors;
	}

	/**
	 * Get Vendor Consennt  Data.
	 *
	 * @return array|mixed
	 */
	public static function gdpr_get_iabtcf_vendor_consent_data() {
		$iabtcf_consent_data = get_option( GDPR_COOKIE_CONSENT_SETTINGS_VENDOR_CONSENT );

		if (!is_array($iabtcf_consent_data)) {
			$iabtcf_consent_data = [];
		}
		
		$iabtcf_consent_data["consent"] = [];
		$iabtcf_consent_data["legint"] = [];
		$iabtcf_consent_data["purpose_consent"] = [];
		$iabtcf_consent_data["purpose_legint"] = [];
		$iabtcf_consent_data["feature_consent"] = [];
		 $iabtcf_consent_data["gacm_consent"] = [];
		return $iabtcf_consent_data;
	}

	/**
	 * Color shift a hex value by a specific percentage factor.
	 *
	 * @since 1.0
	 * @param string  $supplied_hex Any valid hex value. Short forms e.g. #333333 accepted.
	 * @param string  $shift_method How to shift the value e.g( +,up,lighter,>).
	 * @param integer $percentage Percentage in range of [0-100] to shift provided hex value by.
	 * @return string shifted hex value
	 */
	public static function gdpr_su_hex_shift( $supplied_hex, $shift_method, $percentage = 50 ) {
		$shifted_hex_value     = null;
		$valid_shift_option    = false;
		$current_set           = 1;
		$rgb_values            = array();
		$valid_shift_up_args   = array( 'up', '+', 'lighter', '>' );
		$valid_shift_down_args = array( 'down', '-', 'darker', '<' );
		$shift_method          = strtolower( trim( $shift_method ) );

		// Check Factor.
		$percentage = (int) $percentage;
		if ( ! is_numeric( $percentage ) || ( $percentage ) < 0 || $percentage > 100 ) {
			return $supplied_hex;
		}

		// Check shift method.
		foreach ( array( $valid_shift_down_args, $valid_shift_up_args ) as $options ) {
			foreach ( $options as $method ) {
				if ( $method === $shift_method ) {
					$valid_shift_option = ! $valid_shift_option;
					$shift_method       = ( 1 === $current_set ) ? '+' : '-';
					break 2;
				}
			}
			++$current_set;
		}

		if ( ! $valid_shift_option ) {
			return $supplied_hex;
		}

		// Check Hex string.
		$supplied_hex = ( str_replace( '#', '', trim( $supplied_hex ) ) );
		switch ( strlen( $supplied_hex ) ) {
			case 3:
				if ( preg_match( '/^([0-9a-f])([0-9a-f])([0-9a-f])/i', $supplied_hex ) ) {
					$supplied_hex = preg_replace( '/^([0-9a-f])([0-9a-f])([0-9a-f])/i', '\\1\\1\\2\\2\\3\\3', $supplied_hex );
				} else {
					return $supplied_hex;
				}
				break;
			case 6:
				if ( ! preg_match( '/^[0-9a-f]{2}[0-9a-f]{2}[0-9a-f]{2}$/i', $supplied_hex ) ) {
					return $supplied_hex;
				}
				break;
			default:
				return $supplied_hex;
		}

		// Start shifting.
		$rgb_values['R'] = hexdec( $supplied_hex[0] . $supplied_hex[1] );
		$rgb_values['G'] = hexdec( $supplied_hex[2] . $supplied_hex[3] );
		$rgb_values['B'] = hexdec( $supplied_hex[4] . $supplied_hex[5] );

		foreach ( $rgb_values as $c => $v ) {
			switch ( $shift_method ) {
				case '-':
					$amount = round( ( ( 255 - $v ) / 100 ) * $percentage ) + $v;
					break;
				case '+':
					$amount = $v - round( ( $v / 100 ) * $percentage );
					break;
				default:
					return $supplied_hex;
			}
			$decimal_to_hex     = dechex( $amount );
			$current_value      = ( strlen( $decimal_to_hex ) < 2 ) ? '0' . $decimal_to_hex : $decimal_to_hex;
			$shifted_hex_value .= $current_value;
		}

		return '#' . $shifted_hex_value;
	}

	/**
	 * Returns JSON object containing the settings for the main script.
	 *
	 * @since 1.0
	 * @return mixed|string|void
	 */
	public static function gdpr_get_json_settings() {
		$settings = self::gdpr_get_settings();
		// Slim down JSON objects to the bare bones.
		$slim_settings  = array(
			'animate_speed_hide'                     => $settings['animate_speed_hide'],
			'animate_speed_show'                     => $settings['animate_speed_show'],
			'background'                             => $settings['background'],
			'opacity'                                => $settings['opacity'],
			'background_border_width'                => $settings['background_border_width'],
			'background_border_style'                => $settings['background_border_style'],
			'background_border_color'                => $settings['background_border_color'],
			'background_border_radius'               => $settings['background_border_radius'],
			'background1'                            => $settings['cookie_bar_color1'],
			'text1'                                  => $settings['cookie_text_color1'],
			'opacity1'                               => $settings['cookie_bar_opacity1'],
			'background_border_width1'               => $settings['cookie_bar_border_width1'],
			'background_border_style1'               => $settings['border_style1'],
			'background_border_color1'               => $settings['cookie_border_color1'],
			'background_border_radius1'              => $settings['cookie_bar_border_radius1'],
			'button_cancel_link_color1'              => $settings['button_cancel_link_color1'],
			'button_confirm_link_color1'             => $settings['button_confirm_link_color1'],
			'button_cancel_button_color1'            => $settings['button_cancel_button_color1'],
			'button_cancel_button_hover1'            => ( self::gdpr_su_hex_shift( $settings['button_cancel_button_color1'], 'down', 20 ) ),
			'button_confirm_button_color1'           => $settings['button_confirm_button_color1'],
			'button_confirm_button_hover1'           => ( self::gdpr_su_hex_shift( $settings['button_confirm_button_color1'], 'down', 20 ) ),
			'button_accept_link_color1'              => $settings['button_accept_link_color1'],
			'button_accept_button_color1'            => $settings['button_accept_button_color1'],
			'button_accept_button_hover1'            => ( self::gdpr_su_hex_shift( $settings['button_accept_button_color1'], 'down', 20 ) ),
			'button_accept_as_button1'               => $settings['button_accept_as_button1'],
			'button_accept_new_win1'                 => $settings['button_accept_new_win1'],
			'button_accept_is_on1'                   => isset($settings['button_accept_is_on1']) ? $settings['button_accept_is_on1'] : '',
			'button_accept_all_is_on1'               => $settings['button_accept_all_is_on1'],
			'button_accept_all_link_color1'          => $settings['button_accept_all_link_color1'],
			'button_accept_all_as_button1'           => $settings['button_accept_all_as_button1'],
			'button_accept_all_new_win1'             => $settings['button_accept_all_new_win1'],
			'button_accept_all_button_color1'        => $settings['button_accept_all_button_color1'],
			'button_accept_all_button_hover1'        => ( self::gdpr_su_hex_shift( $settings['button_accept_all_button_color1'], 'down', 20 ) ),
			'button_donotsell_link_color1'           => $settings['button_donotsell_link_color1'],
			'button_donotsell_as_button1'            => isset( $settings['button_donotsell_as_button1'] ) ? $settings['button_donotsell_as_button1'] : '',
			'button_cancel_as_button1'               => isset( $settings['button_cancel_as_button1'] ) ? $settings['button_cancel_as_button1'] : '',
			'button_confirm_as_button1'              => isset( $settings['button_confirm_as_button1'] ) ? $settings['button_confirm_as_button1'] : '',
			'button_donotsell_is_on1'                => isset( $settings['button_donotsell_is_on1'] ) ? $settings['button_donotsell_is_on1'] : '',
			'button_cancel_is_on1'                   => isset( $settings['button_cancel_is_on1'] ) ? $settings['button_cancel_is_on1'] : '',
			'button_confirm_is_on1'                  => isset( $settings['button_confirm_is_on1'] ) ? $settings['button_confirm_is_on1'] : '',
			'button_decline_link_color1'             => $settings['button_decline_link_color1'],
			'button_decline_button_color1'           => $settings['button_decline_button_color1'],
			'button_decline_button_hover1'           => ( self::gdpr_su_hex_shift( $settings['button_decline_button_color1'], 'down', 20 ) ),
			'button_decline_as_button1'              => $settings['button_decline_as_button1'],
			'button_decline_new_win1'                => $settings['button_decline_new_win1'],
			'button_decline_is_on1'                  => $settings['button_decline_is_on1'],
			'button_settings_link_color1'            => $settings['button_settings_link_color1'],
			'button_settings_button_color1'          => $settings['button_settings_button_color1'],
			'button_settings_button_hover1'          => ( self::gdpr_su_hex_shift( $settings['button_settings_button_color1'], 'down', 20 ) ),
			'button_settings_as_button1'             => $settings['button_settings_as_button1'],
			'button_settings_new_win1'               => isset( $settings['button_settings_new_win1'] ) ? $settings['button_settings_new_win1'] : '',
			'button_settings_is_on1'                 => $settings['button_settings_is_on1'],
			'button_settings_display_cookies1'       => $settings['button_settings_display_cookies1'],
			'button_settings_as_popup1'              => $settings['button_settings_as_popup1'],
			'button_settings_layout_skin1'           => $settings['button_settings_layout_skin1'],
			'font_family1'                           => $settings['cookie_font1'],
			'button_accept_button_opacity1'          => $settings['button_accept_button_opacity1'],
			'button_accept_all_btn_opacity1'         => $settings['button_accept_all_btn_opacity1'],
			'button_decline_button_opacity1'         => $settings['button_decline_button_opacity1'],
			'button_settings_button_opacity1'        => $settings['button_settings_button_opacity1'],
			'button_confirm_button_opacity1'         => $settings['button_confirm_button_opacity1'],
			'button_cancel_button_opacity1'          => $settings['button_cancel_button_opacity1'],
			'button_accept_button_border_width1'     => $settings['button_accept_button_border_width1'],
			'button_accept_all_btn_border_width1'    => $settings['button_accept_all_btn_border_width1'],
			'button_decline_button_border_width1'    => $settings['button_decline_button_border_width1'],
			'button_settings_button_border_width1'   => $settings['button_settings_button_border_width1'],
			'button_confirm_button_border_width1'    => $settings['button_confirm_button_border_width1'],
			'button_cancel_button_border_width1'     => $settings['button_cancel_button_border_width1'],
			'button_accept_button_border_style1'     => $settings['button_accept_button_border_style1'],
			'button_accept_all_btn_border_style1'    => $settings['button_accept_all_btn_border_style1'],
			'button_decline_button_border_style1'    => $settings['button_decline_button_border_style1'],
			'button_settings_button_border_style1'   => $settings['button_settings_button_border_style1'],
			'button_confirm_button_border_style1'    => $settings['button_confirm_button_border_style1'],
			'button_cancel_button_border_style1'     => $settings['button_cancel_button_border_style1'],
			'button_accept_button_border_color1'     => $settings['button_accept_button_border_color1'],
			'button_accept_all_btn_border_color1'    => $settings['button_accept_all_btn_border_color1'],
			'button_decline_button_border_color1'    => $settings['button_decline_button_border_color1'],
			'button_settings_button_border_color1'   => $settings['button_settings_button_border_color1'],
			'button_confirm_button_border_color1'    => $settings['button_confirm_button_border_color1'],
			'button_cancel_button_border_color1'     => $settings['button_cancel_button_border_color1'],
			'button_accept_button_border_radius1'    => $settings['button_accept_button_border_radius1'],
			'button_accept_all_btn_border_radius1'   => $settings['button_accept_all_btn_border_radius1'],
			'button_decline_button_border_radius1'   => $settings['button_decline_button_border_radius1'],
			'multiple_legislation_accept_all_border_radius1'   => $settings['multiple_legislation_accept_all_border_radius1'],
			'button_settings_button_border_radius1'  => $settings['button_settings_button_border_radius1'],
			'button_confirm_button_border_radius1'   => $settings['button_confirm_button_border_radius1'],
			'button_cancel_button_border_radius1'    => $settings['button_cancel_button_border_radius1'],
			'background2'                            => $settings['cookie_bar_color2'],
			'text2'                                  => $settings['cookie_text_color2'],
			'opacity2'                               => $settings['cookie_bar_opacity2'],
			'background_border_width2'               => $settings['cookie_bar_border_width2'],
			'background_border_style2'               => $settings['border_style2'],
			'background_border_color2'               => $settings['cookie_border_color2'],
			'background_border_radius2'              => $settings['cookie_bar_border_radius2'],
			'button_cancel_link_color2'              => $settings['button_cancel_link_color2'],
			'button_confirm_link_color2'             => $settings['button_confirm_link_color2'],
			'button_cancel_button_color2'            => $settings['button_cancel_button_color2'],
			'button_cancel_button_hover2'            => ( self::gdpr_su_hex_shift( $settings['button_cancel_button_color2'], 'down', 20 ) ),
			'button_confirm_button_color2'           => $settings['button_confirm_button_color2'],
			'button_confirm_button_hover2'           => ( self::gdpr_su_hex_shift( $settings['button_confirm_button_color2'], 'down', 20 ) ),
			'button_accept_link_color2'              => $settings['button_accept_link_color2'],
			'button_accept_button_color2'            => $settings['button_accept_button_color2'],
			'button_accept_button_hover2'            => ( self::gdpr_su_hex_shift( $settings['button_accept_button_color2'], 'down', 20 ) ),
			'button_accept_as_button2'               => $settings['button_accept_as_button2'],
			'button_accept_new_win2'                 => $settings['button_accept_new_win2'],
			'button_accept_is_on2'                   => $settings['button_accept_is_on2'],
			'button_accept_all_is_on2'               => $settings['button_accept_all_is_on2'],
			'button_accept_all_link_color2'          => $settings['button_accept_all_link_color2'],
			'button_accept_all_as_button2'           => $settings['button_accept_all_as_button2'],
			'button_accept_all_new_win2'             => $settings['button_accept_all_new_win2'],
			'button_accept_all_button_color2'        => $settings['button_accept_all_button_color2'],
			'button_accept_all_button_hover2'        => ( self::gdpr_su_hex_shift( $settings['button_accept_all_button_color2'], 'down', 20 ) ),
			'button_donotsell_link_color2'           => $settings['button_donotsell_link_color2'],
			'button_donotsell_as_button2'            => isset( $settings['button_donotsell_as_button2'] ) ? $settings['button_donotsell_as_button2'] : '',
			'button_cancel_as_button2'               => isset( $settings['button_cancel_as_button2'] ) ? $settings['button_cancel_as_button2'] : '',
			'button_confirm_as_button2'              => isset( $settings['button_confirm_as_button2'] ) ? $settings['button_confirm_as_button2'] : '',
			'button_donotsell_is_on2'                => isset( $settings['button_donotsell_is_on2'] ) ? $settings['button_donotsell_is_on2'] : '',
			'button_cancel_is_on2'                   => isset( $settings['button_cancel_is_on2'] ) ? $settings['button_cancel_is_on2'] : '',
			'button_confirm_is_on2'                  => isset( $settings['button_confirm_is_on2'] ) ? $settings['button_confirm_is_on2'] : '',
			'button_decline_link_color2'             => $settings['button_decline_link_color2'],
			'button_decline_button_color2'           => $settings['button_decline_button_color2'],
			'button_decline_button_hover2'           => ( self::gdpr_su_hex_shift( $settings['button_decline_button_color2'], 'down', 20 ) ),
			'button_decline_as_button2'              => $settings['button_decline_as_button2'],
			'button_decline_new_win2'                => $settings['button_decline_new_win2'],
			'button_decline_is_on2'                  => $settings['button_decline_is_on2'],
			'button_settings_link_color2'            => $settings['button_settings_link_color2'],
			'button_settings_button_color2'          => $settings['button_settings_button_color2'],
			'button_settings_button_hover2'          => ( self::gdpr_su_hex_shift( $settings['button_settings_button_color2'], 'down', 20 ) ),
			'button_settings_as_button2'             => $settings['button_settings_as_button2'],
			'button_settings_new_win2'               => isset( $settings['button_settings_new_win2'] ) ? $settings['button_settings_new_win2'] : '',
			'button_settings_is_on2'                 => $settings['button_settings_is_on2'],
			'button_settings_display_cookies2'       => $settings['button_settings_display_cookies2'],
			'button_settings_as_popup2'              => $settings['button_settings_as_popup2'],
			'button_settings_layout_skin2'           => $settings['button_settings_layout_skin2'],
			'font_family2'                           => $settings['cookie_font2'],
			'button_accept_button_opacity2'          => $settings['button_accept_button_opacity2'],
			'button_accept_all_btn_opacity2'         => $settings['button_accept_all_btn_opacity2'],
			'button_decline_button_opacity2'         => $settings['button_decline_button_opacity2'],
			'button_settings_button_opacity2'        => $settings['button_settings_button_opacity2'],
			'button_confirm_button_opacity2'         => $settings['button_confirm_button_opacity2'],
			'button_cancel_button_opacity2'          => $settings['button_cancel_button_opacity2'],
			'button_accept_button_border_width2'     => $settings['button_accept_button_border_width2'],
			'button_accept_all_btn_border_width2'    => $settings['button_accept_all_btn_border_width2'],
			'button_decline_button_border_width2'    => $settings['button_decline_button_border_width2'],
			'button_settings_button_border_width2'   => $settings['button_settings_button_border_width2'],
			'button_confirm_button_border_width2'    => $settings['button_confirm_button_border_width2'],
			'button_cancel_button_border_width2'     => $settings['button_cancel_button_border_width2'],
			'button_accept_button_border_style2'     => $settings['button_accept_button_border_style2'],
			'button_accept_all_btn_border_style2'    => $settings['button_accept_all_btn_border_style2'],
			'button_decline_button_border_style2'    => $settings['button_decline_button_border_style2'],
			'button_settings_button_border_style2'   => $settings['button_settings_button_border_style2'],
			'button_confirm_button_border_style2'    => $settings['button_confirm_button_border_style2'],
			'button_cancel_button_border_style2'     => $settings['button_cancel_button_border_style2'],
			'button_accept_button_border_color2'     => $settings['button_accept_button_border_color2'],
			'button_accept_all_btn_border_color2'    => $settings['button_accept_all_btn_border_color2'],
			'button_decline_button_border_color2'    => $settings['button_decline_button_border_color2'],
			'button_settings_button_border_color2'   => $settings['button_settings_button_border_color2'],
			'button_confirm_button_border_color2'    => $settings['button_confirm_button_border_color2'],
			'button_cancel_button_border_color2'     => $settings['button_cancel_button_border_color2'],
			'button_accept_button_border_radius2'    => $settings['button_accept_button_border_radius2'],
			'button_accept_all_btn_border_radius2'   => $settings['button_accept_all_btn_border_radius2'],
			'button_decline_button_border_radius2'   => $settings['button_decline_button_border_radius2'],
			'button_settings_button_border_radius2'  => $settings['button_settings_button_border_radius2'],
			'button_confirm_button_border_radius2'   => $settings['button_confirm_button_border_radius2'],
			'button_cancel_button_border_radius2'    => $settings['button_cancel_button_border_radius2'],
			'border_color1'                          => ( self::gdpr_su_hex_shift( $settings['cookie_text_color1'], 'up', 40 ) ),
			'background_color1'                      => ( self::gdpr_su_hex_shift( $settings['cookie_bar_color1'], 'down', 10 ) ),
			'background_active_color1'               => $settings['cookie_bar_color1'],
			'border_active_color1'                   => $settings['cookie_bar_color1'],
			'border_color2'                          => ( self::gdpr_su_hex_shift( $settings['cookie_text_color2'], 'up', 40 ) ),
			'background_color2'                      => ( self::gdpr_su_hex_shift( $settings['cookie_bar_color2'], 'down', 10 ) ),
			'background_active_color2'               => $settings['cookie_bar_color2'],
			'border_active_color2'                   => $settings['cookie_bar_color2'],
			'template'                               => $settings['template'],
			'button_cancel_link_color'               => $settings['button_cancel_link_color'],
			'button_confirm_link_color'              => $settings['button_confirm_link_color'],
			'button_cancel_button_color'             => $settings['button_cancel_button_color'],
			'button_cancel_button_hover'             => ( self::gdpr_su_hex_shift( $settings['button_cancel_button_color'], 'down', 20 ) ),
			'button_confirm_button_color'            => $settings['button_confirm_button_color'],
			'button_confirm_button_hover'            => ( self::gdpr_su_hex_shift( $settings['button_confirm_button_color'], 'down', 20 ) ),
			'button_accept_link_color'               => $settings['button_accept_link_color'],
			'button_accept_button_color'             => $settings['button_accept_button_color'],
			'button_accept_button_hover'             => ( self::gdpr_su_hex_shift( $settings['button_accept_button_color'], 'down', 20 ) ),
			'button_accept_as_button'                => $settings['button_accept_as_button'],
			'button_accept_new_win'                  => $settings['button_accept_new_win'],
			'button_accept_is_on'                    => $settings['button_accept_is_on'],
			'button_accept_all_is_on'                => $settings['button_accept_all_is_on'],
			'button_accept_all_link_color'           => $settings['button_accept_all_link_color'],
			'button_accept_all_as_button'            => $settings['button_accept_all_as_button'],
			'button_accept_all_new_win'              => $settings['button_accept_all_new_win'],
			'button_accept_all_button_color'         => $settings['button_accept_all_button_color'],
			'button_accept_all_button_hover'         => ( self::gdpr_su_hex_shift( $settings['button_accept_all_button_color'], 'down', 20 ) ),
			'button_donotsell_link_color'            => $settings['button_donotsell_link_color'],
			'button_donotsell_as_button'             => $settings['button_donotsell_as_button'],
			'button_cancel_as_button'                => $settings['button_cancel_as_button'],
			'button_confirm_as_button'               => $settings['button_confirm_as_button'],
			'button_donotsell_is_on'                 => $settings['button_donotsell_is_on'],
			'button_cancel_is_on'                    => $settings['button_cancel_is_on'],
			'button_confirm_is_on'                   => $settings['button_confirm_is_on'],
			'button_readmore_link_color'             => $settings['button_readmore_link_color'],
			'button_revoke_consent_text_color'       => $settings['button_revoke_consent_text_color'],
			'button_revoke_consent_background_color' => $settings['button_revoke_consent_background_color'],
			'button_readmore_button_color'           => $settings['button_readmore_button_color'],
			'button_readmore_button_hover'           => ( self::gdpr_su_hex_shift( $settings['button_readmore_button_color'], 'down', 20 ) ),
			'button_readmore_as_button'              => $settings['button_readmore_as_button'],
			'button_readmore_new_win'                => $settings['button_readmore_new_win'],
			'button_readmore_is_on'                  => $settings['button_readmore_is_on'],
			'button_readmore_url_type'               => $settings['button_readmore_url_type'],
			'button_readmore_wp_page'                => $settings['button_readmore_wp_page'],
			'button_readmore_page'                   => $settings['button_readmore_page'],
			'button_decline_link_color'              => $settings['button_decline_link_color'],
			'button_decline_button_color'            => $settings['button_decline_button_color'],
			'button_decline_button_hover'            => ( self::gdpr_su_hex_shift( $settings['button_decline_button_color'], 'down', 20 ) ),
			'button_decline_as_button'               => $settings['button_decline_as_button'],
			'button_decline_new_win'                 => $settings['button_decline_new_win'],
			'button_decline_is_on'                   => $settings['button_decline_is_on'],
			'button_settings_link_color'             => $settings['button_settings_link_color'],
			'button_settings_button_color'           => $settings['button_settings_button_color'],
			'button_settings_button_hover'           => ( self::gdpr_su_hex_shift( $settings['button_settings_button_color'], 'down', 20 ) ),
			'button_settings_as_button'              => $settings['button_settings_as_button'],
			'button_settings_new_win'                => $settings['button_settings_new_win'],
			'button_settings_is_on'                  => $settings['button_settings_is_on'],
			'button_settings_display_cookies'        => $settings['button_settings_display_cookies'],
			'button_settings_as_popup'               => $settings['button_settings_as_popup'],
			'button_settings_layout_skin'            => $settings['button_settings_layout_skin'],
			'font_family'                            => $settings['font_family'],
			'notify_animate_hide'                    => $settings['notify_animate_hide'],
			'notify_animate_show'                    => $settings['notify_animate_show'],
			'notify_div_id'                          => $settings['notify_div_id'],
			'notify_position_vertical'               => $settings['notify_position_vertical'],
			'notify_position_horizontal'             => $settings['notify_position_horizontal'],
			'text'                                   => $settings['text'],
			'cookie_bar_as'                          => $settings['cookie_bar_as'],
			'cookie_usage_for'                       => $settings['cookie_usage_for'],
			'popup_overlay'                          => $settings['popup_overlay'],
			'border_color'                           => ( self::gdpr_su_hex_shift( $settings['text'], 'up', 40 ) ),
			'background_color'                       => ( self::gdpr_su_hex_shift( $settings['background'], 'down', 10 ) ),
			'background_active_color'                => $settings['background'],
			'border_active_color'                    => $settings['background'],
			'logging_on'                             => $settings['logging_on'],
			'is_eu_on'                               => $settings['is_eu_on'],
			'is_ccpa_on'                             => $settings['is_ccpa_on'],
			'is_ccpa_iab_on'                         => $settings['is_ccpa_iab_on'],
			'is_worldwide_on'                        => $settings['is_worldwide_on'],
			'is_selectedCountry_on'                  => $settings['is_selectedCountry_on'],
			'is_ticked'                              => $settings['is_ticked'],
			'is_script_blocker_on'                   => $settings['is_script_blocker_on'],
			'auto_scroll'                            => $settings['auto_scroll'],
			'auto_click'                             => $settings['auto_click'],
			'auto_scroll_reload'                     => $settings['auto_scroll_reload'],
			'accept_reload'                          => $settings['accept_reload'],
			'decline_reload'                         => $settings['decline_reload'],
			'delete_on_deactivation'                 => $settings['delete_on_deactivation'],
			'auto_hide'                              => $settings['auto_hide'],
			'auto_hide_delay'                        => $settings['auto_hide_delay'],
			'auto_banner_initialize'                 => $settings['auto_banner_initialize'],
			'auto_generated_banner'                	 => $settings['auto_generated_banner'],
			'auto_banner_initialize_delay'           => $settings['auto_banner_initialize_delay'],
			'auto_scroll_offset'                     => $settings['auto_scroll_offset'],
			'cookie_expiry'                          => $settings['cookie_expiry'],
			'show_again'                             => $settings['show_again'],
			'show_again_position'                    => $settings['show_again_position'],
			'show_again_text'                        => $settings['show_again_text'],
			'show_again_margin'                      => $settings['show_again_margin'],
			'show_again_div_id'                      => $settings['show_again_div_id'],
			'button_accept_button_opacity'           => $settings['button_accept_button_opacity'],
			'button_accept_all_btn_opacity'          => $settings['button_accept_all_btn_opacity'],
			'button_decline_button_opacity'          => $settings['button_decline_button_opacity'],
			'button_readmore_button_opacity'         => $settings['button_readmore_button_opacity'],
			'button_settings_button_opacity'         => $settings['button_settings_button_opacity'],
			'button_confirm_button_opacity'          => $settings['button_confirm_button_opacity'],
			'button_cancel_button_opacity'           => $settings['button_cancel_button_opacity'],
			'button_accept_button_border_width'      => $settings['button_accept_button_border_width'],
			'button_accept_all_btn_border_width'     => $settings['button_accept_all_btn_border_width'],
			'button_decline_button_border_width'     => $settings['button_decline_button_border_width'],
			'button_readmore_button_border_width'    => $settings['button_readmore_button_border_width'],
			'button_settings_button_border_width'    => $settings['button_settings_button_border_width'],
			'button_confirm_button_border_width'     => $settings['button_confirm_button_border_width'],
			'button_cancel_button_border_width'      => $settings['button_cancel_button_border_width'],
			'button_accept_button_border_style'      => $settings['button_accept_button_border_style'],
			'button_accept_all_btn_border_style'     => $settings['button_accept_all_btn_border_style'],
			'button_decline_button_border_style'     => $settings['button_decline_button_border_style'],
			'button_readmore_button_border_style'    => $settings['button_readmore_button_border_style'],
			'button_settings_button_border_style'    => $settings['button_settings_button_border_style'],
			'button_confirm_button_border_style'     => $settings['button_confirm_button_border_style'],
			'button_cancel_button_border_style'      => $settings['button_cancel_button_border_style'],
			'button_accept_button_border_color'      => $settings['button_accept_button_border_color'],
			'button_accept_all_btn_border_color'     => $settings['button_accept_all_btn_border_color'],
			'button_decline_button_border_color'     => $settings['button_decline_button_border_color'],
			'button_readmore_button_border_color'    => $settings['button_readmore_button_border_color'],
			'button_settings_button_border_color'    => $settings['button_settings_button_border_color'],
			'button_confirm_button_border_color'     => $settings['button_confirm_button_border_color'],
			'button_cancel_button_border_color'      => $settings['button_cancel_button_border_color'],
			'button_accept_button_border_radius'     => $settings['button_accept_button_border_radius'],
			'button_accept_all_btn_border_radius'    => $settings['button_accept_all_btn_border_radius'],
			'button_decline_button_border_radius'    => $settings['button_decline_button_border_radius'],
			'button_readmore_button_border_radius'   => $settings['button_readmore_button_border_radius'],
			'button_settings_button_border_radius'   => $settings['button_settings_button_border_radius'],
			'button_confirm_button_border_radius'    => $settings['button_confirm_button_border_radius'],
			'button_cancel_button_border_radius'     => $settings['button_cancel_button_border_radius'],
			'button_confirm_text' => $settings['button_confirm_text'],
			'button_confirm_button_size1' => $settings['button_confirm_button_size1'],
			// consent forward .
			'consent_forward'                        => $settings['consent_forward'],
			'data_reqs_on'                           => $settings['data_reqs_on'],
			//consent version for renew consent
			'consent_version'						 => isset($settings['consent_version']) ? $settings['consent_version'] : 1,
			
			// Multiple Legislation JSON Data
			'multiple_legislation_cookie_bar_color1' => isset($settings['multiple_legislation_cookie_bar_color1']) ? $settings['multiple_legislation_cookie_bar_color1'] : '',
			'multiple_legislation_cookie_bar_color2' => isset($settings['multiple_legislation_cookie_bar_color2']) ? $settings['multiple_legislation_cookie_bar_color2'] : '',
			'multiple_legislation_cookie_bar_opacity1' => isset($settings['multiple_legislation_cookie_bar_opacity1']) ?  $settings['multiple_legislation_cookie_bar_opacity1'] : '',
			'multiple_legislation_cookie_bar_opacity2' => isset($settings['multiple_legislation_cookie_bar_opacity2']) ? $settings['multiple_legislation_cookie_bar_opacity2'] : '',
			'multiple_legislation_cookie_text_color1' => isset($settings['multiple_legislation_cookie_text_color1']) ? $settings['multiple_legislation_cookie_text_color1'] : '',
			'multiple_legislation_cookie_text_color2' => isset($settings['multiple_legislation_cookie_text_color2']) ? $settings['multiple_legislation_cookie_text_color2'] : '',
			'multiple_legislation_border_style1' => isset($settings['multiple_legislation_border_style1']) ? $settings['multiple_legislation_border_style1'] : '',
			'multiple_legislation_border_style2' => isset($settings['multiple_legislation_border_style2']) ? $settings['multiple_legislation_border_style2'] : '',
			'multiple_legislation_cookie_bar_border_width1' => isset($settings['multiple_legislation_cookie_bar_border_width1']) ? $settings['multiple_legislation_cookie_bar_border_width1'] : '',
			'multiple_legislation_cookie_bar_border_width2' => isset($settings['multiple_legislation_cookie_bar_border_width2']) ? $settings['multiple_legislation_cookie_bar_border_width2'] : '',
			'multiple_legislation_cookie_border_color1' => isset($settings['multiple_legislation_cookie_border_color1']) ? $settings['multiple_legislation_cookie_border_color1'] : '',
			'multiple_legislation_cookie_border_color2' => isset($settings['multiple_legislation_cookie_border_color2']) ? $settings['multiple_legislation_cookie_border_color2'] : '',
			'multiple_legislation_cookie_bar_border_radius1' => isset($settings['multiple_legislation_cookie_bar_border_radius1']) ? $settings['multiple_legislation_cookie_bar_border_radius1'] : '',
			'multiple_legislation_cookie_bar_border_radius2' => isset($settings['multiple_legislation_cookie_bar_border_radius2']) ? $settings['multiple_legislation_cookie_bar_border_radius2'] : '',
			'multiple_legislation_cookie_font1' => isset($settings['multiple_legislation_cookie_font1']) ? $settings['multiple_legislation_cookie_font1'] : '',
			'multiple_legislation_cookie_font2' => isset($settings['multiple_legislation_cookie_font2']) ? $settings['multiple_legislation_cookie_font2'] : '',
		);
		$wpl_pro_active = get_option( 'wpl_pro_active' );
		if ( $wpl_pro_active ) {
			$slim_settings['pro_active'] = true;
		} else {
			$slim_settings['pro_active'] = false;
		}
		$slim_settings = apply_filters( 'gdprcookieconsent_json_settings', $slim_settings );
		return $slim_settings;
	}

	/**
	 * Returns array containing CCPA countries.
	 *
	 * @since 1.8.5
	 * @return array
	 */
	public static function get_ccpa_countries() {
		return apply_filters(
			'gdprcookieconsent_ccpa_countrylist',
			array(
				'US',
			)
		);
	}
	/**
	 * Returns array containing EU countries.
	 *
	 * @since 1.0
	 * @return array
	 */
	public static function get_eu_countries() {
		return apply_filters(
			'gdprcookieconsent_eu_countrylist',
			array(
				'AT', // Austria.
				'BE', // Belgium.
				'BG', // Bulgaria.
				'HR', // Croatia.
				'CY', // Cyprus.
				'CZ', // Czech Republic.
				'DK', // Denmark.
				'EE', // Estonia.
				'FI', // Finland.
				'FR', // France.
				'DE', // Germany.
				'GR', // Greece.
				'HU', // Hungary.
				'IE', // Ireland.
				'IT', // Italy.
				'LV', // Latvia.
				'LT', // Lithuania.
				'LU', // Luxembourg.
				'MT', // Malta.
				'NL', // Netherlands.
				'PL', // Poland.
				'PT', // Portugal.
				'RO', // Romania.
				'SK', // Slovakia.
				'SI', // Slovenia.
				'ES', // Spain.
				'SE', // Sweden.
				'GB', // United Kingdom.
			)
		);
	}
}
