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
	private $supported_languages = array( 'fr', 'en', 'nl', 'bg', 'cs', 'da', 'de', 'es', 'hr', 'is', 'sl', 'gr', 'hu', 'po', 'pt', 'ab', 'aa', 'af', 'sq', 'am', 'ar', 'hy', 'az', 'eu', 'be', 'bn', 'bs', 'ca', 'co', 'eo', 'fi', 'fy', 'gl', 'ka', 'gu', 'ha', 'he', 'hi', 'ig', 'id', 'ga', 'it', 'ja', 'kn', 'kk', 'ky', 'ko', 'ku', 'lo', 'lv', 'lb', 'mk', 'mg', 'ms', 'ml', 'mt', 'mi', 'mr', 'mn', 'ne', 'no', 'or', 'ps', 'fa', 'pa', 'ro', 'ru', 'sm', 'gd', 'st', 'sn', 'sd', 'si', 'sk', 'so', 'su', 'sw', 'sv', 'tl', 'tg', 'ta', 'te', 'th', 'tr', 'ug', 'uk', 'ur', 'uz', 'vi', 'cy', 'xh', 'yi', 'yo', 'zu','ceb', 'zh-cn', 'zh-tw', 'et', 'el', 'ht', 'haw', 'iw', 'hmn', 'jw', 'km', 'la', 'lt', 'my', 'pl', 'sr', 'ug' );

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

	public $the_options = array();

	public $tcf_json_data;

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
			add_action('gdpr_cookie_consent_admin_screen', array($this, 'gdpr_cookie_consent_new_admin_screen'));
			add_action('gdpr_cookie_consent_new_admin_dashboard_screen', array($this, 'gdpr_cookie_consent_new_admin_dashboard_screen'));
			add_action('gdpr_help_page_content', array($this, 'gdpr_help_page_content'));
			add_action('rest_api_init', array($this, 'register_gdpr_dashboard_route'));
			//For Import CSV option on Policy data page
			add_action( 'admin_menu', array($this,'register_gdpr_policies_import_page') );
			add_action('admin_menu', array($this,'gdpr_reorder_admin_menu'), 999);
			add_action('admin_notices', array($this,'gdpr_remove_admin_notices'),1);
			add_action('all_admin_notices', array($this,'gdpr_remove_admin_notices'),1);
			//option to store page views
			if(get_option("wpl_page_views") === false) add_option("wpl_page_views", []);
			if(get_option("wpl_total_page_views") === false) add_option("wpl_total_page_views", 0);
			add_action('wp_ajax_install_plugin', array($this, 'gdpr_wplp_install_plugin_ajax_handler'));
			add_action('wp_ajax_gdpr_support_request', array($this, 'gdpr_support_request_handler'));
			add_action('wp_ajax_nopriv_gdpr_support_request', array($this, 'gdpr_support_request_handler'));
			

		}
		
		$json_input = file_get_contents('php://input');
		
		if (!empty($json_input)) {
			// Decode the JSON input
			$tcf_json_data = json_decode($json_input);

			// Check if JSON decoding was successful	
			if (json_last_error() === JSON_ERROR_NONE ) {
				if(!empty($tcf_json_data) && isset($tcf_json_data->secret_key) && $tcf_json_data->secret_key === "sending_vendor_data"){
					Gdpr_Cookie_Consent::gdpr_save_vendors($tcf_json_data);
				}
				
			} else {
			}
		} else {
		}
		
		add_action( 'update_maxmind_db_event', array($this,'download_maxminddb' ));
		add_action('admin_footer', array($this,'add_svg_to_menu_item'));
		if (!isset($the_options['gdpr_current_language'])) {
			$the_options['gdpr_current_language'] = 'en';
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
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
		wp_register_style( $this->plugin_name . '-admin-variables', plugin_dir_url( __FILE__ ) . 'css/gdpr-cookie-consent-admin-variables' . GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-admin-variables' );
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gdpr-cookie-consent-admin' . GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name . '-dashboard', plugin_dir_url( __FILE__ ) . 'css/gdpr-cookie-consent-dashboard' . GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );
		// wizard style.
		wp_register_style( $this->plugin_name . '-wizard', plugin_dir_url( __FILE__ ) . 'css/gdpr-cookie-consent-wizard' . GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'css/select2.css', array(), $this->version, 'all' );
		wp_register_style( 'gdpr_policy_data_tab_style', plugin_dir_url( __FILE__ ) . 'css/gdpr-policy-data-tab' . GDPR_CC_SUFFIX . '.css', array( 'dashicons' ), $this->version, 'all' );
		wp_register_style( $this->plugin_name . '-integrations', plugin_dir_url( __FILE__ ) . 'css/wpl-cookie-consent-integrations.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name . '-review-notice', plugin_dir_url( __FILE__ ) . 'css/wpl-cookie-consent-review-notice' . GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name );
		wp_enqueue_style( $this->plugin_name . '-review-notice' );
		wp_register_style( $this->plugin_name . '-backend', plugin_dir_url( __FILE__ ) . 'css/gdpr-cookie-consent-backend' . GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name );
		wp_enqueue_style( $this->plugin_name . '-backend' );
		wp_enqueue_style( $this->plugin_name .'-introjs-css',plugin_dir_url( __FILE__ ) .  'css/intro.min'. GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-introjs-css' );

		
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
		wp_register_script( $this->plugin_name . '-tcf', plugin_dir_url( __FILE__ ) . 'js/vue/gdpr-cookie-consent-admin-tcf.js');
		wp_enqueue_script($this->plugin_name . 'introjs-js', plugin_dir_url( __FILE__ ) . 'js/intro.min.js', array('jquery'), $this->version, false);

		 
			$iabtcf_consent_data = Gdpr_Cookie_Consent::gdpr_get_iabtcf_vendor_consent_data();
			wp_localize_script(
				$this->plugin_name . '-tcf',
				'iabtcf',
				array(
					'ajax_url'              => plugin_dir_url( __FILE__ )
				)
			);
			wp_enqueue_script( $this->plugin_name . '-tcf', plugin_dir_url( __FILE__ ) . 'js/vue/gdpr-cookie-consent-admin-tcf.js', array( 'jquery' ), $this->version, false );
	}


	/**
	 * Register the script for blocker of services.
	 *
	 * @since    1.0
	 */
	public function gdpr_admin_init() {
		global $wpdb;
		// Check if tawk script is added as third party cookie and add to database if not added.
		if ( ! get_option( 'wpl_pro_tawk_script_added' ) ) {

			$table_name = $wpdb->prefix . 'wpl_cookie_scripts';
			$like       = '%' . $wpdb->esc_like( $table_name ) . '%';
			$result     = $wpdb->get_results( $wpdb->prepare( 'SHOW TABLES LIKE %s', array( $like ) ), ARRAY_N ); // db call ok; no-cache ok.

			if ( $result ) {
				$tawk_table_data = array(
					'script_key'         => 'tawk',
					'script_title'       => 'Tawk Widget',
					'script_category'    => 5,
					'script_status'      => 1,
					'script_description' => 'Chat widget',
				);
				$data_exists     = $wpdb->get_row( $wpdb->prepare( 'SELECT id FROM ' . $wpdb->prefix . 'wpl_cookie_scripts WHERE `script_key`=%s', array( $tawk_table_data['script_key'] ) ), ARRAY_A ); // db call ok; no-cache ok.
				if ( ! $data_exists ) {
					$wpdb->insert( $table_name, $tawk_table_data ); // db call ok; no-cache ok.
				}
				add_option( 'wpl_pro_tawk_script_added', '1' );
			}
		}

		// Check if hubspot script is added as third party cookie and add to database if not added.
		if ( ! get_option( 'wpl_pro_hubspot_script_added' ) ) {

			$table_name = $wpdb->prefix . 'wpl_cookie_scripts';
			$like       = '%' . $wpdb->esc_like( $table_name ) . '%';
			$result     = $wpdb->get_results( $wpdb->prepare( 'SHOW TABLES LIKE %s', array( $like ) ), ARRAY_N ); // db call ok; no-cache ok.

			if ( $result ) {
				$hubspot_table_data = array(
					'script_key'         => 'hubspot',
					'script_title'       => 'Hubspot Analytics',
					'script_category'    => 5,
					'script_status'      => 1,
					'script_description' => 'Hubspot Analytics',
				);
				$data_exists        = $wpdb->get_row( $wpdb->prepare( 'SELECT id FROM ' . $wpdb->prefix . 'wpl_cookie_scripts WHERE `script_key`=%s', array( $hubspot_table_data['script_key'] ) ), ARRAY_A ); // db call ok; no-cache ok.
				if ( ! $data_exists ) {
					$wpdb->insert( $table_name, $hubspot_table_data ); // db call ok; no-cache ok.
				}
				add_option( 'wpl_pro_hubspot_script_added', '1' );
			}
		}

		// Check if recaptcha script is added as third party cookie and add to database if not added.
		if ( ! get_option( 'wpl_pro_recaptcha_script_added' ) ) {
			$table_name = $wpdb->prefix . 'wpl_cookie_scripts';
			$like       = '%' . $wpdb->esc_like( $table_name ) . '%';
			$result     = $wpdb->get_results( $wpdb->prepare( 'SHOW TABLES LIKE %s', array( $like ) ), ARRAY_N ); // db call ok; no-cache ok.

			if ( $result ) {
				$recaptcha_table_data = array(
					'script_key'         => 'recaptcha',
					'script_title'       => 'Google Recaptcha',
					'script_category'    => 5,
					'script_status'      => 1,
					'script_description' => 'Google Recaptcha',
				);
				$data_exists          = $wpdb->get_row( $wpdb->prepare( 'SELECT id FROM ' . $wpdb->prefix . 'wpl_cookie_scripts WHERE `script_key`=%s', array( $recaptcha_table_data['script_key'] ) ), ARRAY_A ); // db call ok; no-cache ok.
				if ( ! $data_exists ) {
					$wpdb->insert( $table_name, $recaptcha_table_data ); // db call ok; no-cache ok.
				}
				add_option( 'wpl_pro_recaptcha_script_added', '1' );
			}
		}
		// Check if adsense script is added as third party cookie and add to database if not added.
		if ( ! get_option( 'wpl_pro_adsense_script_added' ) ) {

			$table_name = $wpdb->prefix . 'wpl_cookie_scripts';
			$like       = '%' . $wpdb->esc_like( $table_name ) . '%';
			$result     = $wpdb->get_results( $wpdb->prepare( 'SHOW TABLES LIKE %s', array( $like ) ), ARRAY_N ); // db call ok; no-cache ok.

			if ( $result ) {
				$adsense_table_data = array(
					'script_key'         => 'adsense',
					'script_title'       => 'Google Adsense',
					'script_category'    => 5,
					'script_status'      => 1,
					'script_description' => 'Google Adsense',
				);
				$data_exists        = $wpdb->get_row( $wpdb->prepare( 'SELECT id FROM ' . $wpdb->prefix . 'wpl_cookie_scripts WHERE `script_key`=%s', array( $adsense_table_data['script_key'] ) ), ARRAY_A ); // db call ok; no-cache ok.
				if ( ! $data_exists ) {
					$wpdb->insert( $table_name, $adsense_table_data ); // db call ok; no-cache ok.
				}
				add_option( 'wpl_pro_adsense_script_added', '1' );
			}
		}

		// Check if matomo script is added as third party cookie and add to database if not added.
		if ( ! get_option( 'wpl_pro_matomo_script_added' ) ) {
			$table_name = $wpdb->prefix . 'wpl_cookie_scripts';
			$like       = '%' . $wpdb->esc_like( $table_name ) . '%';
			$result     = $wpdb->get_results( $wpdb->prepare( 'SHOW TABLES LIKE %s', array( $like ) ), ARRAY_N ); // db call ok; no-cache ok.

			if ( $result ) {
				$matomo_table_data = array(
					'script_key'         => 'matomo',
					'script_title'       => 'Matomo Analytics',
					'script_category'    => 5,
					'script_status'      => 1,
					'script_description' => 'Matomo Analytics',
				);
				$data_exists       = $wpdb->get_row( $wpdb->prepare( 'SELECT id FROM ' . $wpdb->prefix . 'wpl_cookie_scripts WHERE `script_key`=%s', array( $matomo_table_data['script_key'] ) ), ARRAY_A ); // db call ok; no-cache ok.
				if ( ! $data_exists ) {
					$wpdb->insert( $table_name, $matomo_table_data ); // db call ok; no-cache ok.
				}

				add_option( 'wpl_pro_matomo_script_added', '1' );
			}
		}
	}


	/**
	 * Function to register A/B Testing Page
	 */
	public function wpl_get_ab_testing_settings() {
		$abtest = new Gdpr_Cookie_Consent_AB_Testing( $this->plugin_name );
	}

	
	public function get_country_codes() {
		$options = json_decode(
			wp_remote_retrieve_body(
				wp_remote_get( plugin_dir_url( __FILE__ ) . 'data/countries.json', array( 'sslverify' => false ) )
			),
			true
		);
		if ( isset( $options ) && is_array( $options ) ) {
			foreach ( $options as $option ) {
				$country_codes[ $option['code'] ] = $option['name'];
			}
		}
		return apply_filters( 'gdpr_country_codes', $country_codes );
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
				delete_option( 'gdpr_version_number' );
				delete_option( '	analytics_activation_redirect_gdpr-cookie-consent' );
				delete_option( 'wpl_logs_admin' );
				delete_option( 'wpl_datarequests_db_version' );
				delete_option( 'wpl_cl_decline' );
				delete_option( 'wpl_page_views' );
				delete_option( 'wpl_cl_accept' );
				delete_option( 'wpl_cl_partially_accept' );
				delete_option( 'wpl_cl_bypass' );
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
	 * Check if ab-testing-period is completed and set default banner
	 */
	public function gdpr_ab_testing_complete() {
		$ab_options = get_option( 'wpl_ab_options' );
		if ( $ab_options && isset( $ab_options['ab_testing_enabled'] ) && ($ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true') && false === get_transient( 'gdpr_ab_testing_transient' ) ) {
			$banner1_necessary = array_key_exists( 'necessary1', $ab_options ) ? $ab_options['necessary1'] : 0;
			$banner1_marketing = array_key_exists( 'marketing1', $ab_options ) ? $ab_options['marketing1'] : 0;
			$banner1_analytics = array_key_exists( 'analytics1', $ab_options ) ? $ab_options['analytics1'] : 0;
			$banner1_DNT       = array_key_exists( 'DNT1', $ab_options ) ? $ab_options['DNT1'] : 0;
			$banner1_noChoice  = array_key_exists( 'noChoice1', $ab_options ) ? $ab_options['noChoice1'] : 0;
			$banner1_noWarning = array_key_exists( 'noWarning1', $ab_options ) ? $ab_options['noWarning1'] : 0;
			$banner2_necessary = array_key_exists( 'necessary2', $ab_options ) ? $ab_options['necessary2'] : 0;
			$banner2_marketing = array_key_exists( 'marketing2', $ab_options ) ? $ab_options['marketing2'] : 0;
			$banner2_analytics = array_key_exists( 'analytics2', $ab_options ) ? $ab_options['analytics2'] : 0;
			$banner2_DNT       = array_key_exists( 'DNT2', $ab_options ) ? $ab_options['DNT2'] : 0;
			$banner2_noChoice  = array_key_exists( 'noChoice2', $ab_options ) ? $ab_options['noChoice2'] : 0;
			$banner2_noWarning = array_key_exists( 'noWarning2', $ab_options ) ? $ab_options['noWarning2'] : 0;
			// cuurently weights are set equally to 0.25 , noWarning & Do Not Track have a weight of 0.
			$banner1_performance = ( ( $banner1_necessary * 0.25 ) + ( $banner1_marketing * 0.25 ) + ( $banner1_analytics * 0.25 ) - ( $banner1_noChoice * 0.25 ) - ( $banner1_noWarning * 0 ) - ( $banner1_DNT * 0 ) ) / 6;
			$banner2_performance = ( ( $banner2_necessary * 0.25 ) + ( $banner2_marketing * 0.25 ) + ( $banner2_analytics * 0.25 ) - ( $banner2_noChoice * 0.25 ) - ( $banner2_noWarning * 0 ) - ( $banner2_DNT * 0 ) ) / 6;
			$the_options         = Gdpr_Cookie_Consent::gdpr_get_settings();
			$ab_options['ab_testing_enabled'] = 'false';
			update_option( 'wpl_ab_options', $ab_options );
			if ( $banner1_performance > $banner2_performance ) {
				$the_options =  $this->wpl_set_default_ab_testing_banner( $the_options, '1' );
			} else {
				$the_options =  $this->wpl_set_default_ab_testing_banner( $the_options, '2' );
			}
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
		}
	}
	/**
	 * To set the better performing banner as permanent or set defualt as permanent if ab testing turned off manually before period ends.
	 *
	 * @param mixed $banner_choice
	 * @return mixed banner settings
	 */
	public function wpl_set_default_ab_testing_banner( $the_options, $banner_choice ) {
		$the_options['background']                           = $the_options[ 'cookie_bar_color' . $banner_choice ];
		$the_options['text']                                 = $the_options[ 'cookie_text_color' . $banner_choice ];
		$the_options['opacity']                              = $the_options[ 'cookie_bar_opacity' . $banner_choice ];
		$the_options['background_border_width']              = $the_options[ 'cookie_bar_border_width' . $banner_choice ];
		$the_options['background_border_style']              = $the_options[ 'border_style' . $banner_choice ];
		$the_options['background_border_color']              = $the_options[ 'cookie_border_color' . $banner_choice ];
		$the_options['background_border_radius']             = $the_options[ 'cookie_bar_border_radius' . $banner_choice ];
		$the_options['font_family']                          = $the_options[ 'cookie_font' . $banner_choice ];
		$the_options['button_accept_is_on']                  = $the_options[ 'button_accept_is_on' . $banner_choice ];
		$the_options['button_accept_text']                   = $the_options[ 'button_accept_text' . $banner_choice ];
		$the_options['button_accept_button_size']            = $the_options[ 'button_accept_button_size' . $banner_choice ];
		$the_options['button_accept_action']                 = $the_options[ 'button_accept_action' . $banner_choice ];
		$the_options['button_accept_url']                    = $the_options[ 'button_accept_url' . $banner_choice ];
		$the_options['button_accept_as_button']              = $the_options[ 'button_accept_as_button' . $banner_choice ];
		$the_options['button_accept_new_win']                = $the_options[ 'button_accept_new_win' . $banner_choice ];
		$the_options['button_accept_button_color']           = $the_options[ 'button_accept_button_color' . $banner_choice ];
		$the_options['button_accept_button_opacity']         = $the_options[ 'button_accept_button_opacity' . $banner_choice ];
		$the_options['button_accept_button_border_style']    = $the_options[ 'button_accept_button_border_style' . $banner_choice ];
		$the_options['button_accept_button_border_color']    = $the_options[ 'button_accept_button_border_color' . $banner_choice ];
		$the_options['button_accept_button_border_width']    = $the_options[ 'button_accept_button_border_width' . $banner_choice ];
		$the_options['button_accept_button_border_radius']   = $the_options[ 'button_accept_button_border_radius' . $banner_choice ];
		$the_options['button_accept_link_color']             = $the_options[ 'button_accept_link_color' . $banner_choice ];
		$the_options['button_decline_is_on']                 = $the_options[ 'button_decline_is_on' . $banner_choice ];
		$the_options['button_decline_text']                  = $the_options[ 'button_decline_text' . $banner_choice ];
		$the_options['button_decline_link_color']            = $the_options[ 'button_decline_link_color' . $banner_choice ];
		$the_options['button_decline_as_button']             = $the_options[ 'button_decline_as_button' . $banner_choice ];
		$the_options['button_decline_button_color']          = $the_options[ 'button_decline_button_color' . $banner_choice ];
		$the_options['button_decline_button_opacity']        = $the_options[ 'button_decline_button_opacity' . $banner_choice ];
		$the_options['button_decline_button_border_style']   = $the_options[ 'button_decline_button_border_style' . $banner_choice ];
		$the_options['button_decline_button_border_color']   = $the_options[ 'button_decline_button_border_color' . $banner_choice ];
		$the_options['button_decline_button_border_width']   = $the_options[ 'button_decline_button_border_width' . $banner_choice ];
		$the_options['button_decline_button_border_radius']  = $the_options[ 'button_decline_button_border_radius' . $banner_choice ];
		$the_options['button_decline_button_size']           = $the_options[ 'button_decline_button_size' . $banner_choice ];
		$the_options['button_decline_action']                = $the_options[ 'button_decline_action' . $banner_choice ];
		$the_options['button_decline_url']                   = $the_options[ 'button_decline_url' . $banner_choice ];
		$the_options['button_decline_new_win']               = $the_options[ 'button_decline_new_win' . $banner_choice ];
		$the_options['button_settings_is_on']                = $the_options[ 'button_settings_is_on' . $banner_choice ];
		$the_options['button_settings_as_popup']             = $the_options[ 'button_settings_as_popup' . $banner_choice ];
		$the_options['button_settings_layout_skin']          = $the_options[ 'button_settings_layout_skin' . $banner_choice ];
		$the_options['button_settings_text']                 = $the_options[ 'button_settings_text' . $banner_choice ];
		$the_options['button_settings_link_color']           = $the_options[ 'button_settings_link_color' . $banner_choice ];
		$the_options['button_settings_as_button']            = $the_options[ 'button_settings_as_button' . $banner_choice ];
		$the_options['button_settings_button_color']         = $the_options[ 'button_settings_button_color' . $banner_choice ];
		$the_options['button_settings_button_opacity']       = $the_options[ 'button_settings_button_opacity' . $banner_choice ];
		$the_options['button_settings_button_border_style']  = $the_options[ 'button_settings_button_border_style' . $banner_choice ];
		$the_options['button_settings_button_border_color']  = $the_options[ 'button_settings_button_border_color' . $banner_choice ];
		$the_options['button_settings_button_border_width']  = $the_options[ 'button_settings_button_border_width' . $banner_choice ];
		$the_options['button_settings_button_border_radius'] = $the_options[ 'button_settings_button_border_radius' . $banner_choice ];
		$the_options['button_settings_button_size']          = $the_options[ 'button_settings_button_size' . $banner_choice ];
		$the_options['button_settings_display_cookies']      = $the_options[ 'button_settings_display_cookies' . $banner_choice ];
		$the_options['button_confirm_text']                  = $the_options[ 'button_confirm_text' . $banner_choice ];
		$the_options['button_confirm_link_color']            = $the_options[ 'button_confirm_link_color' . $banner_choice ];
		$the_options['button_confirm_button_color']          = $the_options[ 'button_confirm_button_color' . $banner_choice ];
		$the_options['button_confirm_button_opacity']        = $the_options[ 'button_confirm_button_opacity' . $banner_choice ];
		$the_options['button_confirm_button_border_style']   = $the_options[ 'button_confirm_button_border_style' . $banner_choice ];
		$the_options['button_confirm_button_border_color']   = $the_options[ 'button_confirm_button_border_color' . $banner_choice ];
		$the_options['button_confirm_button_border_width']   = $the_options[ 'button_confirm_button_border_width' . $banner_choice ];
		$the_options['button_confirm_button_border_radius']  = $the_options[ 'button_confirm_button_border_radius' . $banner_choice ];
		$the_options['button_confirm_button_size']           = $the_options[ 'button_confirm_button_size' . $banner_choice ];
		$the_options['button_cancel_text']                   = $the_options[ 'button_cancel_text' . $banner_choice ];
		$the_options['button_cancel_link_color']             = $the_options[ 'button_cancel_link_color' . $banner_choice ];
		$the_options['button_cancel_button_color']           = $the_options[ 'button_cancel_button_color' . $banner_choice ];
		$the_options['button_cancel_button_opacity']         = $the_options[ 'button_cancel_button_opacity' . $banner_choice ];
		$the_options['button_cancel_button_border_style']    = $the_options[ 'button_cancel_button_border_style' . $banner_choice ];
		$the_options['button_cancel_button_border_color']    = $the_options[ 'button_cancel_button_border_color' . $banner_choice ];
		$the_options['button_cancel_button_border_width']    = $the_options[ 'button_cancel_button_border_width' . $banner_choice ];
		$the_options['button_cancel_button_border_radius']   = $the_options[ 'button_cancel_button_border_radius' . $banner_choice ];
		$the_options['button_cancel_button_size']            = $the_options[ 'button_cancel_button_size' . $banner_choice ];
		$the_options['button_donotsell_text']                = $the_options[ 'button_donotsell_text' . $banner_choice ];
		$the_options['button_donotsell_link_color']          = $the_options[ 'button_donotsell_link_color' . $banner_choice ];
		$the_options['button_accept_all_is_on']              = $the_options[ 'button_accept_all_is_on' . $banner_choice ];
		$the_options['button_accept_all_text']               = $the_options[ 'button_accept_all_text' . $banner_choice ];
		$the_options['button_accept_all_link_color']         = $the_options[ 'button_accept_all_link_color' . $banner_choice ];
		$the_options['button_accept_all_as_button']          = $the_options[ 'button_accept_all_as_button' . $banner_choice ];
		$the_options['button_accept_all_action']             = $the_options[ 'button_accept_all_action' . $banner_choice ];
		$the_options['button_accept_all_url']                = $the_options[ 'button_accept_all_url' . $banner_choice ];
		$the_options['button_accept_all_new_win']            = $the_options[ 'button_accept_all_new_win' . $banner_choice ];
		$the_options['button_accept_all_button_color']       = $the_options[ 'button_accept_all_button_color' . $banner_choice ];
		$the_options['button_accept_all_button_size']        = $the_options[ 'button_accept_all_button_size' . $banner_choice ];
		$the_options['button_accept_all_btn_border_style']   = $the_options[ 'button_accept_all_btn_border_style' . $banner_choice ];
		$the_options['button_accept_all_btn_border_color']   = $the_options[ 'button_accept_all_btn_border_color' . $banner_choice ];
		$the_options['button_accept_all_btn_opacity']        = $the_options[ 'button_accept_all_btn_opacity' . $banner_choice ];
		$the_options['button_accept_all_btn_border_width']   = $the_options[ 'button_accept_all_btn_border_width' . $banner_choice ];
		$the_options['button_accept_all_btn_border_radius']  = $the_options[ 'button_accept_all_btn_border_radius' . $banner_choice ];

		// resetting ab testing settings and analytics
		$ab_options                        = get_option( 'wpl_ab_options' );
		$ab_options ['ab_testing_enabled'] = false;
		$ab_options ['ab_testing_period']  = '30';
		$ab_options ['necessary1']         = 0;
		$ab_options ['marketing1']         = 0;
		$ab_options ['analytics1']         = 0;
		$ab_options ['DNT1']               = 0;
		$ab_options ['noChoice1']          = 0;
		$ab_options ['noWarning1']         = 0;
		$ab_options ['necessary2']         = 0;
		$ab_options ['marketing2']         = 0;
		$ab_options ['analytics2']         = 0;
		$ab_options ['DNT2']               = 0;
		$ab_options ['noChoice2']          = 0;
		$ab_options ['noWarning2']         = 0;
		update_option( 'wpl_ab_options', $ab_options );
		return $the_options;
	}

	/**
	 * Function to display gdpr review notice on admin page.
	 *
	 * @return void
	 */
	public function gdpr_admin_review_notice() {
		$gdpr_review_option_exists = get_option( 'gdpr_review_pending' );
		switch ( $gdpr_review_option_exists ) {
			case '0':
				$check_for_review_transient = get_transient( 'gdpr_review_transient' );
				if ( false === $check_for_review_transient ) {
					set_transient( 'gdpr_review_transient', 'Review Pending', 2592000 );
					update_option( 'gdpr_review_pending', '1', true );
				}
				break;
			case '1':
				$check_for_review_transient = get_transient( 'gdpr_review_transient' );
				if ( false === $check_for_review_transient ) {
					wp_enqueue_style( $this->plugin_name . 'review-notice' );
					printf(
						'
						<div class="gdpr-review-notice updated">
						<form method="post" action="%2$s" id="review_form">
							<div class="gdpr-review-notice-text-container">
								<p><span>%3$s<strong>Cookie Consent fro WP</strong>.%4$s</span></p>
								<button class="gdpr-review-dismiss-btn" style="border: none;padding:0;background: none;color: #2271b1;"href="%2$s"><i class="dashicons dashicons-dismiss"></i>%5$s</button>
							</div>
							<div class="gdpr-review-btns-container">
								<button class="gdpr-review-btns gdpr-review-rate-us-btn"><a href="%1$s" target="_blank">%6$s<i class="dashicons dashicons-thumbs-up"></i></a></button>
								<button class="gdpr-review-btns gdpr-review-already-done-btn" href="%2$s" >%7$s<i class="dashicons dashicons-smiley"></i></button>
							</div>
							<input type="hidden" id="gdpr_review_nonce" name="gdpr_review_nonce" value="' . esc_attr( wp_create_nonce( 'gdpr_review' ) ) . '" />
						</form>
						</div>
						',
						esc_url( 'https://wordpress.org/support/plugin/gdpr-cookie-consent/reviews/' ),
						esc_url( get_admin_url() . '?already_done=1' ),
						esc_html__( 'Hey, we hope you are enjoying managing cookies with ', 'gdpr' ),
						esc_html__( ' Could you please write us a review and give it a 5- star rating on WordPress? Just to help us spread the word and boost our motivation.', 'gdpr' ),
						esc_html__( 'Dismiss', 'gdpr' ),
						esc_html__( 'Rate Us', 'gdpr' ),
						esc_html__( 'I already did', 'gdpr' )
					);
				}
				break;
			case '2':
				break;
			default:
				break;
		}
	}
	/**
	 * Function to check the user's input on gdpr review notice.
	 *
	 * @return void
	 */
	public function gdpr_review_already_done() {
		$dnd = '';
		if ( isset( $_POST['gdpr_review_nonce'] ) && check_admin_referer( 'gdpr_review', 'gdpr_review_nonce' ) ) {
			if ( isset( $_GET['already_done'] ) && ! empty( $_GET['already_done'] ) ) {
				$dnd = sanitize_text_field( wp_unslash( $_GET['already_done'] ) );
			}
			if ( '1' === $dnd ) {
				update_option( 'gdpr_review_pending', '2', true );
			}
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
			<form id="wpl-dnsmpd-filter-consent-log" method="get" action="<?php echo esc_url( admin_url( 'admin.php?page=gdpr-cookie-consent#consent_logs' ) ); ?>">
				<div class="wpl-heading-export-consentlogs">
					<div class="consent-log-heading-export">
						<h1 class="wp-heading"><?php esc_html_e( 'Consent Logs', 'gdpr-cookie-consent' ); ?></h1>
						<a href="<?php echo esc_url_raw( plugins_url( 'public/modules/consent-logs/csv.php', __DIR__ ) . '?nonce=' . wp_create_nonce( 'wpl_csv_nonce' ) ); ?>" target="_blank" class="data-req-export-button"><?php esc_html_e( 'Export as CSV', 'gdpr-cookie-consent' ); ?></a>
					</div>
					<div class="consent-log-search-log"> 
						<?php $consent_logs->search_box( __( 'Search Logs', 'gdpr-cookie-consent' ), 'gdpr-cookie-consent' ); ?> 
					</div>
				</div>
			<?php
				$consent_logs->display();
			?>
			<input type="hidden" name="page" value="gdpr-cookie-consent"/>
			</form>
		</div>
			<?php
			$content                  = ob_get_clean();
			$args                     = array(
				'page'    => 'do-not-sell-my-personal-information',
				'content' => $content,
			);
			$allowed_consent_log_html = array(
				'div'    => array(
					'class' => array(),
					'id'    => array(),
					'style' => array(),
				),
				'h1'     => array(
					'class' => array(),
				),
				'form'   => array(
					'id'     => array(),
					'method' => array(),
					'action' => array(),
				),
				'img'    => array(
					'class' => array(),
					'src'   => array(),
					'alt'   => array(),
					'id'    => array(),
				),
				'p'      => array(
					'class' => array(),
				),
				'label'  => array(
					'class' => array(),
					'for'   => array(),
				),
				'input'  => array(
					'type'        => array(),
					'id'          => array(),
					'name'        => array(),
					'value'       => array(),
					'class'       => array(),
					'placeholder' => array(),
				),
				'a'      => array(
					'href'    => array(),
					'target'  => array(),
					'class'   => array(),
					'onclick' => array(),
				),
				'select' => array(
					'name'  => array(),
					'id'    => array(),
					'class' => array(),
				),
				'option' => array(
					'value'    => array(),
					'selected' => array(),
				),
				'script' => array(
					'type' => array(),
				),
				'thead'  => array(),
				'tr'     => array(),
				'th'     => array(
					'scope' => array(),
					'id'    => array(),
					'class' => array(),
				),
				'span'   => array(
					'class'       => array(),
					'aria-hidden' => array(),
				),
				'tbody'  => array(),
				'td'     => array(
					'class'        => array(),
					'data-colname' => array(),
					'colspan'      => array(),
				),
				'tfoot'  => array(),
				'button' => array(
					'type'  => array(),
					'class' => array(),
				),
				'table'  => array(
					'class' => array(),
				),
				'svg'    => array(
					'width'   => array(),
					'height'  => array(),
					'viewBox' => array(),
					'fill'    => array(),
					'xmlns'   => array(),
				),
				'g'      => array(
					'clip-path' => array(),
				),
				'path'   => array(
					'd'    => array(),
					'fill' => array(),
				),
				'defs'   => array(
					'clipPath' => array(),
				),
				'rect'   => array(),
			);
			echo wp_kses( $this->wpl_get_consent_template( 'gdpr-consent-logs-tab-template.php', $args ), $allowed_consent_log_html );
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
			$contents = wp_remote_get( $file );
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
				<label for="wpl_datarequest_firstname" class="wpl-first-name"><?php echo esc_html__( 'Name', 'gdpr-cookie-consent' ); ?>
					<input type="search" class="datarequest-firstname" value="" placeholder="your first name" id="wpl_datarequest_firstname" name="wpl_datarequest_firstname" >
				</label>
				<div>
					<label for="wpl_datarequest_name"><?php echo esc_html__( 'Name', 'gdpr-cookie-consent' ); ?></label>
					<input type="text" required value="" placeholder="<?php echo esc_html__( 'Your name', 'gdpr-cookie-consent' ); ?>" id="wpl_datarequest_name" name="wpl_datarequest_name">
				</div>
				<div>
					<label for="wpl_datarequest_email"><?php echo esc_html__( 'Email', 'gdpr-cookie-consent' ); ?></label>
					<input type="email" required value="" placeholder="<?php echo esc_html__( 'email@email.com', 'gdpr-cookie-consent' ); ?>" id="wpl_datarequest_email" name="wpl_datarequest_email">
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
				<input type="button" id="wpl-datarequest-submit"  value="<?php echo esc_html__( 'Send', 'gdpr-cookie-consent' ); ?>">
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
		$from_email  = isset($the_options['data_req_email_address'])?$the_options['data_req_email_address']:'';
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
			<form id="wpl-dnsmpd-filter-datarequest" method="get" action="<?php echo esc_url( admin_url( 'admin.php?page=gdpr-cookie-consent#data_request' ) ); ?>">
				<div class="wpl-heading-export-datarequest">
					<div class="data-request-heading-export">
						<h1 class="wp-heading"><?php esc_html_e( 'Data Requests', 'gdpr-cookie-consent' ); ?></h1>
						<a href="<?php echo esc_url_raw( plugins_url( 'admin/data-req/csv.php', __DIR__ ) . '?nonce=' . wp_create_nonce( 'wpl_csv_nonce' ) ); ?>" target="_blank" class="data-req-export-button"><?php esc_html_e( 'Export as CSV', 'gdpr-cookie-consent' ); ?></a>
					</div>
					<div class="data-request-search-log"> 
						<?php $datarequests->search_box( __( 'Search Logs', 'gdpr-cookie-consent' ), 'gdpr-cookie-consent' ); ?> 
					</div>
				</div>
				<?php
					$datarequests->display();
				?>
				<input type="hidden" name="page" value="gdpr-cookie-consent"/>
			</form>
		</div>
			<?php

			$content               = ob_get_clean();
			$args                  = array(
				'page'    => 'do-not-sell-my-personal-information',
				'content' => $content,
			);
			$allowed_data_req_html = array(
				'div'    => array(
					'class' => array(),
					'id'    => array(),
					'style' => array(),
				),
				'h1'     => array(
					'class' => array(),
				),
				'form'   => array(
					'id'     => array(),
					'method' => array(),
					'action' => array(),
				),
				'img'    => array(
					'class' => array(),
					'src'   => array(),
					'alt'   => array(),
					'id'    => array(),
				),
				'p'      => array(
					'class' => array(),
				),
				'label'  => array(
					'class' => array(),
					'for'   => array(),
				),
				'input'  => array(
					'type'        => array(),
					'id'          => array(),
					'name'        => array(),
					'value'       => array(),
					'class'       => array(),
					'placeholder' => array(),
				),
				'a'      => array(
					'href'    => array(),
					'target'  => array(),
					'class'   => array(),
					'onclick' => array(),
				),
				'select' => array(
					'name'  => array(),
					'id'    => array(),
					'class' => array(),
				),
				'option' => array(
					'value'    => array(),
					'selected' => array(),
				),
				'script' => array(
					'type' => array(),
				),
				'thead'  => array(),
				'tr'     => array(),
				'th'     => array(
					'scope' => array(),
					'id'    => array(),
					'class' => array(),
				),
				'span'   => array(
					'class'       => array(),
					'aria-hidden' => array(),
				),
				'tbody'  => array(),
				'td'     => array(
					'class'        => array(),
					'data-colname' => array(),
					'colspan'      => array(),
				),
				'tfoot'  => array(),
				'button' => array(
					'type'  => array(),
					'class' => array(),
				),
				'table'  => array(
					'class' => array(),
				),
				'svg'    => array(
					'width'   => array(),
					'height'  => array(),
					'viewBox' => array(),
					'fill'    => array(),
					'xmlns'   => array(),
				),
				'g'      => array(
					'clip-path' => array(),
				),
				'path'   => array(
					'd'    => array(),
					'fill' => array(),
				),
				'defs'   => array(
					'clipPath' => array(),
				),
				'rect'   => array(),
			);
			echo wp_kses( $this->wpl_get_template_data_request( 'gdpr-data-request-tab-template.php', $args ), $allowed_data_req_html );
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

		 // Check if the main menu "WP Legal Pages" is already registered
		 if (!is_admin() || !current_user_can('manage_options')) return;
		$installed_plugins = get_plugins();
		$plugin_name                   = 'wplegalpages/wplegalpages.php';
		$legal_pages_installed     = isset( $installed_plugins['wplegalpages/wplegalpages.php'] ) ? true : false;
		$gdpr_installed     = isset( $installed_plugins['gdpr-cookie-consent/gdpr-cookie-consent.php'] ) ? true : false;
		$is_legalpages_active = is_plugin_active( $plugin_name );
		$plugin_name_gdpr                   = 'gdpr-cookie-consent/gdpr-cookie-consent.php';
		$is_gdpr_active = is_plugin_active( $plugin_name_gdpr );
		$callback_function = $is_legalpages_active ?  array( $this, 'wp_legalpages_new_admin_screen' ) : array( $this, 'wp_legal_pages_install_activate_screen' );
		 if (empty($GLOBALS['admin_page_hooks']['wp-legal-pages'])) {
			global $admin_page_hooks;
			add_menu_page(
				__( 'WP Legal Pages', 'wp-legal-pages' ), // Page title
				__( 'WP Legal Pages', 'wp-legal-pages' ), // Menu title
				'manage_options',                        // Capability
				'wp-legal-pages',                        // Menu slug
				$callback_function,            // Icon URL (choose an icon from the WordPress Dashicons library)
				GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/wp_legalpages_dashicon_1.png',
				67                                       // Position
			);
		}
		if(!$is_legalpages_active){
			add_submenu_page(
				'wp-legal-pages', // Parent slug (same as main menu slug)
				__( 'Dashboard', 'gdpr-cookie-consent' ),  // Page title
				__( 'Dashboard', 'gdpr-cookie-consent' ),     // Dashboard page title
				'manage_options',   // Capability
				'wplp-dashboard', // Menu slug
				array( $this, 'gdpr_cookie_consent_new_admin_dashboard_screen' ), // Callback function
				1
			);
		}
		 if(!$legal_pages_installed  || ($legal_pages_installed && !$is_legalpages_active)){
			
			// Add the "Cookie Consent" sub-menu under "WP Legal Pages"
			add_submenu_page(
				'wp-legal-pages', // Parent slug (same as main menu slug)
				__( 'WP Cookie Consent', 'gdpr-cookie-consent' ),  // Page title
				__( 'Legal Pages', 'gdpr-cookie-consent' ),     // Menu title
				'manage_options',   // Capability
				'legal-pages', // Menu slug
				array( $this, 'wp_legal_pages_install_activate_screen' ),
				2
			);
		}	
		if(!$is_legalpages_active){
			add_submenu_page(
				'wp-legal-pages', // Parent slug (same as main menu slug)
				__( 'WP Cookie Consent', 'gdpr-cookie-consent' ),  // Page title
				__( 'Cookie Consent', 'gdpr-cookie-consent' ),     // Menu title
				'manage_options',   // Capability
				'gdpr-cookie-consent', // Menu slug
				array( $this, 'gdpr_cookie_consent_new_admin_screen' ), // Callback function
				3
				
			);
		}
		if(!$is_legalpages_active){
			add_submenu_page(
				'wp-legal-pages', // Parent slug (same as main menu slug)
				__( 'Help', 'gdpr-cookie-consent' ),  // Page title
				__( 'Help', 'gdpr-cookie-consent' ),     // Dashboard page title
				'manage_options',   // Capability
				'wplp-dashboard#help-page', // Menu slug
				array( $this, 'gdpr_help_page_content' ), // Callback function
				999
			);
		}

		// Check if $_GET['scan_url'] is set
		$scan_url_value = isset( $_GET['scan_url'] ) ? $_GET['scan_url'] : '';

		// Check if the key exists in the options table
		if ( get_option( 'gdpr_single_page_scan_url' ) !== false ) {
			// Update the existing option
			update_option( 'gdpr_single_page_scan_url', $scan_url_value );
		} else {
			// Add a new option
			add_option( 'gdpr_single_page_scan_url', $scan_url_value );
		}
		
		if($legal_pages_installed || $gdpr_installed){
			remove_submenu_page('wp-legal-pages', 'wp-legal-pages');
		}
	}
	
	public function gdpr_reorder_admin_menu(){
		global $submenu;
	
		if (isset($submenu['wp-legal-pages'])) {
			// Extract the "Help" menu
			$help_menu = null;
			foreach ($submenu['wp-legal-pages'] as $key => $item) {
				if ($item[2] === 'wplp-dashboard#help-page') {
					$help_menu = $item;
					unset($submenu['wp-legal-pages'][$key]);
					break;
				}
			}
	
			// Re-add "Help" menu at the end
			if ($help_menu) {
				$submenu['wp-legal-pages'][] = $help_menu;
			}
		}
	}
	/**
	 *  Callback function for adding and removing the scanning loader from cookie consent sub menu.
	 *
	 * @since 3.6.6
	 */
	function add_svg_to_menu_item() {
		?>
		 <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Add or remove the GIF dynamically based on localStorage
            function updateScanGif() {
                var logoUrl = "<?php echo plugin_dir_url(__FILE__) . 'images/auto_scan_loader.gif'; ?>";
                var menuItem = $('#toplevel_page_wp-legal-pages .wp-submenu li a[href="admin.php?page=gdpr-cookie-consent"]');

                // Check the localStorage value
                var scanInProgress = localStorage.getItem('auto_scan_process_started') === 'true';

                if (scanInProgress) {
                    // Add the GIF if it doesn't already exist
                    if (!menuItem.find('img').length) {
                        menuItem.prepend('<img src="' + logoUrl + '" style="height: 20px; margin-right: 10px;" />');
                    }
                } else {
                    // Remove the GIF if it exists
                    menuItem.find('img').remove();
                }
            }

            // Initial check on page load
            updateScanGif();

            // Polling mechanism to check for changes in localStorage
            setInterval(function() {
                updateScanGif();
            }, 1000); // Check every second
        });
    </script>
		<?php
	}
	/**
	 * Registers menu options, hooked into admin_menu.
	 *
	 * @since 3.2.0
	 */
	public function gdpr_quick_toolbar_menu( $wp_admin_bar ) {

		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		// cookie banner enable
		$is_banner_active = $the_options['is_on'];
		// script blocker enable
		$is_script_blocker_active = $the_options['is_script_blocker_on'];

		$enabled_label  = '<span style="color:#05E900; font-size:13px;">&#11044;</span>';
		$disabled_label = '<span style="color:#E10101; font-size:13px;;">&#11044;</span>';

		// Add parent menu item
		$args = array(
			'id'    => 'gdpr-quick-menu',
			'title' => 'WP Cookie Consent <span class="custom-icon" style="float:right;width:22px !important;height:22px !important;margin: 5px 5px 0 !important;"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.36305 18.2675C7.43268 18.739 8.57557 18.9748 9.79172 18.9748C11.0079 18.9742 12.1508 18.7384 13.2204 18.2675C14.29 17.7965 15.2205 17.1532 16.0117 16.3376C16.8023 15.522 17.4286 14.572 17.8904 13.4877C18.3523 12.4033 18.5832 11.2478 18.5832 10.0211C18.5838 9.85589 18.5803 9.7024 18.5727 9.56058C18.565 9.41875 18.5539 9.26556 18.5392 9.101C17.5728 9.07108 16.844 8.65219 16.3528 7.84433C15.8617 7.03648 15.8066 6.19122 16.1875 5.30856C15.5434 5.53297 14.9172 5.57037 14.3088 5.42077C13.7004 5.27116 13.1764 4.99799 12.7369 4.60124C12.2973 4.20509 11.9676 3.70781 11.7478 3.1094C11.528 2.51099 11.4841 1.87518 11.616 1.20196C11.2789 1.12716 10.9493 1.0748 10.6269 1.04488C10.3046 1.01496 9.98953 1 9.68183 1C8.40707 0.999403 7.23487 1.25732 6.16524 1.77375C5.09561 2.29018 4.17983 2.97087 3.4179 3.81583C2.65597 4.66138 2.06255 5.62273 1.63763 6.69987C1.2127 7.77701 1.00024 8.87659 1.00024 9.99862C1.00083 11.2403 1.23175 12.4072 1.69301 13.4993C2.15427 14.5914 2.78052 15.5414 3.57175 16.3493C4.36299 17.1565 5.29342 17.7959 6.36305 18.2675Z" fill="white"/><ellipse cx="5.10827" cy="6.64684" rx="1.75451" ry="1.79137" fill="#171C1F"/><ellipse cx="7.11088" cy="14.1328" rx="1.40361" ry="1.43309" fill="#171C1F"/><ellipse cx="4.05556" cy="10.8357" rx="0.701803" ry="0.716547" fill="#171C1F"/><circle cx="9.72125" cy="8.8703" r="0.877254" fill="#171C1F"/><ellipse cx="14.9546" cy="10.2109" rx="1.40361" ry="1.43309" fill="#171C1F"/><circle cx="12.5134" cy="14.7998" r="1.31588" fill="#171C1F"/><ellipse cx="9.5458" cy="4.00341" rx="0.701803" ry="0.716547" fill="#171C1F"/></svg></span>',
			'href'  => admin_url( 'admin.php?page=gdpr-cookie-consent' ), // Add your custom URL here
			'meta'  => array(
				'class'  => 'gdpr-quick-menu-item',
				'target' => '', // Add target attribute if needed
			),
		);

		$wp_admin_bar->add_node( $args );

		$args = array(
			'id'     => 'gdpr-quick-menu-item-1',
			'title'  => 'Scan this Page',
			'parent' => 'gdpr-quick-menu',
			'href'   => admin_url( 'admin.php?page=gdpr-cookie-consent&scan_url=' ) . get_permalink() . '#cookie_settings#cookie_list#discovered_cookies',
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

		$banner_title = 'Cookie Banner : ' . ( $is_banner_active ? 'Enabled ' . $enabled_label : 'Disabled ' . $disabled_label );

		$args = array(
			'id'     => 'gdpr-quick-menu-item-3',
			'title'  => $banner_title,
			'parent' => 'gdpr-quick-menu',
			'href'   => '',
		);
		$wp_admin_bar->add_node( $args );

		$script_blocker_title = 'Script Blocker : ' . ( $is_script_blocker_active ? 'Enabled ' . $enabled_label : 'Disabled ' . $disabled_label );
		$args                 = array(
			'id'     => 'gdpr-quick-menu-item-4',
			'title'  => $script_blocker_title,
			'parent' => 'gdpr-quick-menu',
			'href'   => '',
		);
		$wp_admin_bar->add_node( $args );
	}

	public function gdpr_help_page_content() {

			include_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'admin/gdpr-help-page.php';			
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
				<form id="wpl-dnsmpd-filter" method="get" action="<?php echo esc_url( admin_url( 'admin.php?page=gdpr-cookie-consent#policy_data' ) ); ?>">
					<div class="wpl-heading-export-consentlogs">
						<div class="policy-data-heading-export">
							<h1 class="wp-heading"><?php esc_html_e( 'Policy Data', 'gdpr-cookie-consent' ); ?></h1>
							<a href="<?php echo esc_url( admin_url( 'admin-post.php?action=gdpr_policies_export.csv' ) ); ?>" target="_blank" class="data-req-export-button"><?php esc_html_e( 'Export As CSV', 'gdpr-cookie-consent' ); ?></a>
							<a href="<?php echo esc_url( admin_url( 'edit.php?page=gdpr-policies-import' )); ?>" target="_blank" class="data-req-export-button"><?php esc_html_e( 'Import From CSV', 'gdpr-cookie-consent' ); ?></a>
							<a href="<?php echo esc_url_raw( admin_url( 'post-new.php?post_type=gdprpolicies' ) ); ?>" target="_blank" class="data-req-export-button"><?php esc_html_e( 'Add New', 'gdpr-cookie-consent' ); ?></a>
						</div>
						<div class="policy-data-search-log"> 
							<?php $policy_data->search_box( __( 'Search Logs', 'gdpr-cookie-consent' ), 'gdpr-cookie-consent' ); ?> 
						</div>
					</div>
					<?php
						// $policy_data->search_box( __( 'Search Policy Data', 'gdpr-cookie-consent' ), 'gdpr-cookie-consent' );
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
				// as the content is already escaped so there is no need to excape the $this.
				echo $this->wpl_get_template_policy_data( 'gdpr-policy-data-tab-template.php', $args ); // phpcs:ignore
	}

	/**
	 * Get a template for data request based on filename, overridable in the theme directory.
	 *
	 * @param string $filename The name of the template file.
	 * @param array  $args     An array of arguments to pass to the template.
	 * @param string $path     The path to the template file (optional).
	 * @return string The content of the template.
	 */
	public function wpl_get_template_data_request( $filename, $args = array(), $path = false ) {
		$file = GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'admin/partials/gdpr-data-request-tab-template.php';

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
			$contents = wp_remote_get( $file );
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
		// fetching the setting for paid plan.
		$settings = new GDPR_Cookie_Consent_Settings();
		$api_user_plan          = $settings->get_plan();
		if ( $api_user_plan == 'free' ) {
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
		if ( ! get_option( 'gdpr_version_number' ) ) {
			update_option( 'gdpr_version_number', GDPR_COOKIE_CONSENT_VERSION );
		} elseif ( get_option( 'gdpr_version_number' ) !== GDPR_COOKIE_CONSENT_VERSION ) {
				update_option( 'gdpr_version_number', GDPR_COOKIE_CONSENT_VERSION );
		}
		// Check if the key exists in the options table
		if ( get_option( 'gdpr_no_of_page_scan' ) == false ) {
			add_option( 'gdpr_no_of_page_scan', 0 );
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
			$prev_gdpr_option['bar_heading_text']             = '';
			$prev_gdpr_option['show_again']                   = true;
			$prev_gdpr_option['is_script_blocker_on']         = false;
			$prev_gdpr_option['auto_hide']                    = false;
			$prev_gdpr_option['auto_banner_initialize']       = false;
			$prev_gdpr_option['auto_scroll']                  = false;
			$prev_gdpr_option['show_again_position']          = 'right';
			$prev_gdpr_option['show_again_text']              = 'Cookie Settings';
			$prev_gdpr_option['show_again_margin']            = '5%';
			$prev_gdpr_option['auto_hide_delay']              = '10000';
			$prev_gdpr_option['auto_banner_initialize_delay'] = '10000';
			$prev_gdpr_option['show_again_div_id']            = '#gdpr-cookie-consent-show-again';
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
						'css'              => 'color:#000000;background-color:#ffffff;text-align:justify;',
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
							'css'                  => 'background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#118635',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
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
							'css'                  => 'background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#118635',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
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
						'css'              => 'color:#1e3d59;background-color:#e8ddbb;text-align:justify;',
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
							'css'                  => 'background-color:#c1540c;color:#ffffff;display:block;width:100%;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#c1540c',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#c1540c',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:#252525;color:#ffffff;display:block;width:100%;margin:0.5rem auto 0 auto;',
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
							'css'        => 'color:#c1540c;',
							'link_color' => '#c1540c',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#c1540c;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#c1540c',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
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
							'css'        => 'color:#c1540c;',
							'link_color' => '#c1540c',
						),
					),
					'navy_blue_center' => array(
						'name'             => 'banner-navy_blue_center',
						'css'              => 'color:#e5e5e5;background-color:#2a3e71;text-align:center;',
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
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;min-width:124px;margin:0 0.5rem 0 0;border: 1px solid #369ee3;',
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
						'css'              => 'color:#000000;background-color:#f4f4f4;text-align:justify;',
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
							'css'                  => 'background-color:#C1263E;color:#ffffff;display:block;width:100%;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#C1263E',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#C1263E',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:#111111;color:#ffffff;display:block;width:100%;margin:0.5rem auto 0 auto;',
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
							'css'        => 'color:#C1263E;',
							'link_color' => '#C1263E',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#C1263E;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#C1263E',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#C1263E',
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
							'css'        => 'color:#C1263E;',
							'link_color' => '#C1263E',
						),
					),
					'dark_row'         => array(
						'name'             => 'banner-dark_row',
						'css'              => 'color:#ffffff;background-color:#323742;text-align:center;',
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
							'css'                  => 'background-color:#2b806a;color:#ffffff;display:block;max-width:fit-content;min-width:124px;margin:0.5rem auto 0 auto;border:1px solid 4570dc',
							'link_color'           => '#ffffff',
							'button_color'         => '#2b806a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(50, 55, 66, 0);color:#2b806a;display:block;max-width:fit-content;margin:0.5rem auto 0 auto;border:1px solid #2b806a;',
							'link_color'           => '#2b806a',
							'button_color'         => '#323742',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#2b806a;',
							'link_color' => '#2b806a',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#2b806a;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#2b806a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#323742;color:#2b806a;margin:0 0.5rem 0 0',
							'link_color'           => '#2b806a',
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
							'css'        => 'color:#2b806a;',
							'link_color' => '#2b806a',
						),
					),
					'grey_center'      => array(
						'name'             => 'banner-grey_center',
						'css'              => 'color:#000000;background-color:#f4f4f4;text-align:center;',
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
							'css'        => 'color:#ac4008;',
							'link_color' => '#ac4008',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#ac4008;color:#ffffff;min-width:5rem;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#ac4008',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#ac4008',
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
							'css'                  => 'background-color:#ac4008;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#ac4008',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
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
							'css'        => 'color:#ac4008;',
							'link_color' => '#ac4008',
						),
					),
					'dark'             => array(
						'name'             => 'banner-dark',
						'css'              => 'color:#ffffff;background-color:#262626;text-align:justify;',
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
							'css'                  => 'background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0;border:1px solid #4570dc;',
							'link_color'           => '#ffffff',
							'button_color'         => '#4570dc',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#4570dc',
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
							'css'        => 'color:#4570dc;',
							'link_color' => '#4570dc',
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
							'css'                  => 'background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#4570dc',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#4570dc',
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
							'css'        => 'color:#4570dc;',
							'link_color' => '#4570dc',
						),
					),
				),
				'popup'  => array(
					'default'          => array(
						'name'             => 'popup-default',
						'css'              => 'color:#000000;background-color:#ffffff;text-align:justify;',
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
							'css'                  => 'background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#118635',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
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
							'css'                  => 'background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#118635',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
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
						'css'              => 'color:#ffffff;background-color:#262626;text-align:justify;',
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
							'css'                  => 'background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0;border:1px solid #4570dc;',
							'link_color'           => '#ffffff',
							'button_color'         => '#4570dc',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#4570dc',
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
							'css'        => 'color:#4570dc;',
							'link_color' => '#4570dc',
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
							'css'                  => 'background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#4570dc',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#4570dc',
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
							'css'        => 'color:#4570dc;',
							'link_color' => '#4570dc',
						),
					),
					'almond_column'    => array(
						'name'             => 'popup-almond_column',
						'css'              => 'color:#1e3d59;background-color:#e8ddbb;text-align:justify;',
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
							'css'                  => 'background-color:#c1540c;color:#ffffff;display:block;width:100%;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#c1540c',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#c1540c',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:#252525;color:#ffffff;display:block;width:100%;margin:0.5rem auto 0 auto;',
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
							'css'        => 'color:#c1540c;',
							'link_color' => '#c1540c',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#c1540c;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#c1540c',
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
							'css'        => 'color:#c1540c;',
							'link_color' => '#c1540c',
						),
					),
					'navy_blue_center' => array(
						'name'             => 'popup-navy_blue_center',
						'css'              => 'color:#e5e5e5;background-color:#2a3e71;text-align:center;',
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
							'css'                  => 'background-color:#369ee3;color:#e5e5e5;min-width:124px;margin:0 0.5rem 0 0;border: 1px solid #369ee3;',
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
							'button_border_color'  => '#118635',
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
						'css'              => 'color:#ffffff;background-color:#323742;text-align:center;',
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
							'css'                  => 'background-color:#2b806a;color:#ffffff;display:block;max-width:fit-content;min-width:124px;margin:0.5rem auto 0 auto;border:1px solid 4570dc',
							'link_color'           => '#ffffff',
							'button_color'         => '#2b806a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(50, 55, 66, 0);color:#2b806a;display:block;max-width:fit-content;margin:0.5rem auto 0 auto;border:1px solid #2b806a;',
							'link_color'           => '#2b806a',
							'button_color'         => '#323742',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#2b806a;',
							'link_color' => '#2b806a',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#2b806a;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#2b806a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#323742;color:#2b806a;margin:0 0.5rem 0 0',
							'link_color'           => '#2b806a',
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
							'css'        => 'color:#2b806a;',
							'link_color' => '#2b806a',
						),
					),
					'grey_center'      => array(
						'name'             => 'popup-grey_center',
						'css'              => 'color:#000000;background-color:#f4f4f4;text-align:center;',
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
							'css'        => 'color:#ac4008;',
							'link_color' => '#ac4008',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#ac4008;color:#ffffff;min-width:5rem;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#ac4008',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#ac4008',
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
							'css'                  => 'background-color:#ac4008;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#ac4008',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#ac4008',
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
							'css'        => 'color:#ac4008;',
							'link_color' => '#ac4008',
						),
					),
					'navy_blue_box'    => array(
						'name'             => 'popup-navy_blue_box',
						'css'              => 'color:#e5e5e5;background-color:#2a3e71;text-align:justify;border-radius:15px;',
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
						'css'              => 'color:#000000;background-color:#f4f4f4;text-align:justify;border:1px solid #111111',
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
							'css'                  => 'background-color:#C1263E;color:#ffffff;display:block;max-width:fit-content;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#C1263E',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#C1263E',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:#111111;color:#ffffff;display:block;max-width:fit-content;margin:0.5rem auto 0 auto;',
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
							'css'        => 'color:#C1263E;',
							'link_color' => '#C1263E',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#C1263E;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#C1263E',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#C1263E',
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
							'css'        => 'color:#C1263E;',
							'link_color' => '#C1263E',
						),
					),
					'navy_blue_square' => array(
						'name'             => 'popup-navy_blue_square',
						'css'              => 'color:#e5e5e5;background-color:#2a3e71;text-align:justify;',
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
						'css'              => 'color:#000000;background-color:#ffffff;text-align:justify;',
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
							'css'                  => 'background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#118635',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
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
							'css'                  => 'background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#118635',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
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
						'css'              => 'color:#ffffff;background-color:#262626;text-align:justify;',
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
							'css'                  => 'background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0;border:1px solid #4570dc;',
							'link_color'           => '#ffffff',
							'button_color'         => '#4570dc',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#4570dc',
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
							'css'        => 'color:#4570dc;',
							'link_color' => '#4570dc',
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
							'css'                  => 'background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#4570dc',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#4570dc',
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
							'css'        => 'color:#4570dc;',
							'link_color' => '#4570dc',
						),
					),
					'almond_column'    => array(
						'name'             => 'widget-almond_column',
						'css'              => 'color:#1e3d59;background-color:#e8ddbb;text-align:justify;',
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
							'css'                  => 'background-color:#c1540c;color:#ffffff;display:block;width:100%;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#c1540c',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#c1540c',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:#252525;color:#ffffff;display:block;width:100%;margin:0.5rem auto 0 auto;',
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
							'css'        => 'color:#c1540c;',
							'link_color' => '#c1540c',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#c1540c;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#c1540c',
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
							'css'        => 'color:#c1540c;',
							'link_color' => '#c1540c',
						),
					),
					'navy_blue_box'    => array(
						'name'             => 'widget-navy_blue_box',
						'css'              => 'color:#e5e5e5;background-color:#2a3e71;text-align:justify;border-radius:15px;',
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
						'css'              => 'color:#ffffff;background-color:#323742;text-align:center;',
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
							'css'                  => 'background-color:#2b806a;color:#ffffff;display:block;max-width:fit-content;min-width:124px;margin:0.5rem auto 0 auto;border:1px solid 4570dc',
							'link_color'           => '#ffffff',
							'button_color'         => '#2b806a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:rgba(50, 55, 66, 0);color:#2b806a;display:block;max-width:fit-content;margin:0.5rem auto 0 auto;border:1px solid #2b806a;',
							'link_color'           => '#2b806a',
							'button_color'         => '#323742',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'color:#2b806a;',
							'link_color' => '#2b806a',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#2b806a;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#2b806a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#323742;color:#2b806a;margin:0 0.5rem 0 0',
							'link_color'           => '#2b806a',
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
							'css'        => 'color:#2b806a;',
							'link_color' => '#2b806a',
						),
					),
					'navy_blue_center' => array(
						'name'             => 'widget-navy_blue_center',
						'css'              => 'color:#e5e5e5;background-color:#2a3e71;text-align:center;',
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
							'button_border_color'  => '#118635',
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
						'css'              => 'color:#000000;background-color:#f4f4f4;text-align:justify;border: 1px solid #111111;',
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
							'css'                  => 'background-color:#C1263E;color:#ffffff;display:block;max-width:fit-content;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#C1263E',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#C1263E',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'background-color:#111111;color:#ffffff;display:block;max-width:fit-content;margin:0.5rem auto 0 auto;',
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
							'css'        => 'color:#C1263E;',
							'link_color' => '#C1263E',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'background-color:#C1263E;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#C1263E',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#C1263E',
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
							'css'        => 'color:#C1263E;',
							'link_color' => '#C1263E',
						),
					),
					'grey_center'      => array(
						'name'             => 'widget-grey_center',
						'css'              => 'color:#000000;background-color:#f4f4f4;text-align:center;',
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
							'css'        => 'color:#AC4008;',
							'link_color' => '#AC4008',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'background-color:#AC4008;color:#ffffff;min-width:5rem;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#AC4008',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#AC4008',
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
							'css'                  => 'background-color:#AC4008;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#AC4008',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#AC4008',
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
							'css'        => 'color:#AC4008;',
							'link_color' => '#AC4008',
						),
					),
					'navy_blue_square' => array(
						'name'             => 'widget-navy_blue_square',
						'css'              => 'color:#e5e5e5;background-color:#2a3e71;text-align:justify;',
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
				'modal_banner' => array(
					'default'          => array(
						'name'             => 'banner-default',
						'css'              => 'font-size:10.21px;max-width:500px;color:#000000;background-color:#ffffff;text-align:justify;box-shadow: 0px 2px 20px 0px #042a4b1c;',
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
							'css'                  => 'font-size:10.21px;background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#118635',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
							'button_border_radius' => '0',
						),
						'decline'          => array(
							'text'                 => __( 'Decline', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:14px;color:#007cba;',
							'link_color' => '#007cba',
						),
						'settings'         => array(
							'text'                 => __( 'Cookie Settings', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#007cba;color:#ffffff;float:right;',
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
							'css'                  => 'font-size:10.21px;background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#118635',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#007cba;',
							'link_color' => '#007cba',
						),
					),
					'almond_column'    => array(
						'name'             => 'banner-almond_column',
						'css'              => 'font-size:10.21px;max-width:500px;color:#1e3d59;background-color:#e8ddbb;text-align:justify;',
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
							'css'                  => 'font-size:10.21px;background-color:#c1540c;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#c1540c',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#c1540c',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#252525;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
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
							'css'        => 'font-size:14px;color:#c1540c;',
							'link_color' => '#c1540c',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#c1540c;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#c1540c',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#c1540c;',
							'link_color' => '#c1540c',
						),
					),
					'navy_blue_center' => array(
						'name'             => 'banner-navy_blue_center',
						'css'              => 'font-size:10.21px;max-width:500px;color:#e5e5e5;background-color:#2a3e71;text-align:center;',
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
							'css'        => 'font-size:14px;color:#369ee3;',
							'link_color' => '#369ee3',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0;border: 1px solid #369ee3;',
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
							'css'                  => 'font-size:10.21px;background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;',
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
							'css'                  => 'font-size:10.21px;background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
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
							'css'                  => 'font-size:10.21px;background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#369ee3;',
							'link_color' => '#369ee3',
						),
					),
					'grey_column'      => array(
						'name'             => 'banner-grey_column',
						'css'              => 'font-size:10.21px;max-width:500px;color:#000000;background-color:#f4f4f4;text-align:justify;',
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
							'css'                  => 'font-size:10.21px;background-color:#C1263E;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#C1263E',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#C1263E',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#111111;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
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
							'css'        => 'font-size:14px;color:#C1263E;',
							'link_color' => '#C1263E',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#C1263E;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#C1263E',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#C1263E',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#111111;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#C1263E;',
							'link_color' => '#C1263E',
						),
					),
					'dark_row'         => array(
						'name'             => 'banner-dark_row',
						'css'              => 'font-size:10.21px;max-width:500px;color:#ffffff;background-color:#323742;text-align:center;',
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
							'css'                  => 'font-size:10.21px;background-color:#2b806a;color:#ffffff;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid 4570dc',
							'link_color'           => '#ffffff',
							'button_color'         => '#2b806a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:rgba(50, 55, 66, 0);color:#2b806a;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid #2b806a;',
							'link_color'           => '#2b806a',
							'button_color'         => '#323742',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'font-size:14px;color:#2b806a;',
							'link_color' => '#2b806a',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#2b806a;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#2b806a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#323742;color:#2b806a;margin:0 0.5rem 0 0',
							'link_color'           => '#2b806a',
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
							'css'        => 'font-size:10.21px;color:#2b806a;',
							'link_color' => '#2b806a',
						),
					),
					'grey_center'      => array(
						'name'             => 'banner-grey_center',
						'css'              => 'font-size:10.21px;max-width:500px;color:#000000;background-color:#f4f4f4;text-align:center;',
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
							'css'        => 'font-size:14px;color:#ac4008;',
							'link_color' => '#ac4008',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#ac4008;color:#ffffff;min-width:5rem;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#ac4008',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#ac4008',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#252525;color:#ffffff;',
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
							'css'                  => 'font-size:10.21px;background-color:#ac4008;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#ac4008',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#ac4008;',
							'link_color' => '#ac4008',
						),
					),
					'dark'             => array(
						'name'             => 'banner-dark',
						'css'              => 'font-size:10.21px;max-width:500px;color:#ffffff;background-color:#262626;text-align:justify;',
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
							'css'                  => 'font-size:10.21px;background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0;border:1px solid #4570dc;',
							'link_color'           => '#ffffff',
							'button_color'         => '#4570dc',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#4570dc',
							'button_border_radius' => '0',
						),
						'decline'          => array(
							'text'                 => 'Decline',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#808080;color:#ffffff;float:right;border:1px solid #808080;',
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
							'css'        => 'font-size:14px;color:#4570dc;',
							'link_color' => '#4570dc',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:rgba(38, 38, 38, 0);color:#808080;float:right;margin:0 0.5rem 0 0;border:1px solid #808080',
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
							'css'                  => 'font-size:10.21px;background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#4570dc',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#4570dc',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#808080;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#4570dc;',
							'link_color' => '#4570dc',
						),
					),
				),
				'modal_popup'  => array(
					'default'          => array(
						'name'             => 'popup-default',
						'css'              => 'font-size:10.21px;color:#000000;background-color:#ffffff;text-align:justify;box-shadow: 0px 2px 20px 0px #042a4b1c;',
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
							'css'                  => 'font-size:10.21px;background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#118635',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
							'button_border_radius' => '0',
						),
						'decline'          => array(
							'text'                 => __( 'Decline', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:14px;color:#007cba;',
							'link_color' => '#007cba',
						),
						'settings'         => array(
							'text'                 => __( 'Cookie Settings', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#007cba;color:#ffffff;float:right;',
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
							'css'                  => 'font-size:10.21px;background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#118635',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#007cba;',
							'link_color' => '#007cba',
						),
					),
					'dark'             => array(
						'name'             => 'popup-dark',
						'css'              => 'font-size:10.21px;color:#ffffff;background-color:#262626;text-align:justify;',
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
							'css'                  => 'font-size:10.21px;background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0;border:1px solid #4570dc;',
							'link_color'           => '#ffffff',
							'button_color'         => '#4570dc',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#4570dc',
							'button_border_radius' => '0',
						),
						'decline'          => array(
							'text'                 => 'Decline',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#808080;color:#ffffff;float:none;border:1px solid #808080;',
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
							'css'        => 'font-size:14px;color:#4570dc;',
							'link_color' => '#4570dc',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:rgba(38, 38, 38, 0);color:#808080;float:right;margin:0 0.5rem 0 0;border:1px solid #808080',
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
							'css'                  => 'font-size:10.21px;background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#4570dc',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#4570dc',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#808080;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#4570dc;',
							'link_color' => '#4570dc',
						),
					),
					'almond_column'    => array(
						'name'             => 'popup-almond_column',
						'css'              => 'font-size:10.21px;color:#1e3d59;background-color:#e8ddbb;text-align:justify;',
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
							'css'                  => 'font-size:10.21px;background-color:#c1540c;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#c1540c',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#c1540c',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#252525;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
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
							'css'        => 'font-size:14px;color:#c1540c;',
							'link_color' => '#c1540c',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#c1540c;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#c1540c',
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
							'css'                  => 'font-size:10.21px;background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#c1540c;',
							'link_color' => '#c1540c',
						),
					),
					'navy_blue_center' => array(
						'name'             => 'popup-navy_blue_center',
						'css'              => 'font-size:10.21px;color:#e5e5e5;background-color:#2a3e71;text-align:center;',
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
							'css'        => 'font-size:14px;color:#369ee3;',
							'link_color' => '#369ee3',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;',
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
							'css'                  => 'font-size:10.21px;background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;',
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
							'css'                  => 'font-size:10.21px;background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#369ee3;',
							'link_color' => '#369ee3',
						),
					),
					'dark_row'         => array(
						'name'             => 'popup-dark_row',
						'css'              => 'font-size:10.21px;color:#ffffff;background-color:#323742;text-align:center;',
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
							'css'                  => 'font-size:10.21px;background-color:#2b806a;color:#ffffff;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid 4570dc',
							'link_color'           => '#ffffff',
							'button_color'         => '#2b806a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:rgba(50, 55, 66, 0);color:#2b806a;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid #2b806a;',
							'link_color'           => '#2b806a',
							'button_color'         => '#323742',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'font-size:14px;color:#2b806a;',
							'link_color' => '#2b806a',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#2b806a;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#2b806a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#323742;color:#2b806a;margin:0 0.5rem 0 0',
							'link_color'           => '#2b806a',
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
							'css'        => 'font-size:10.21px;color:#2b806a;',
							'link_color' => '#2b806a',
						),
					),
					'grey_center'      => array(
						'name'             => 'popup-grey_center',
						'css'              => 'font-size:10.21px;color:#000000;background-color:#f4f4f4;text-align:center;',
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
							'css'        => 'font-size:14px;color:#ac4008;',
							'link_color' => '#ac4008',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#ac4008;color:#ffffff;min-width:5rem;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#ac4008',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#ac4008',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#252525;color:#ffffff;',
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
							'css'                  => 'font-size:10.21px;background-color:#ac4008;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#ac4008',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#ac4008',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#ac4008;',
							'link_color' => '#ac4008',
						),
					),
					'navy_blue_box'    => array(
						'name'             => 'popup-navy_blue_box',
						'css'              => 'font-size:10.21px;color:#e5e5e5;background-color:#2a3e71;text-align:justify;border-radius:15px;',
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
							'css'        => 'font-size:14px;color:#369ee3;',
							'link_color' => '#369ee3',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;width:fit-content;',
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
							'css'                  => 'font-size:10.21px;background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;width:fit-content;',
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
							'css'                  => 'font-size:10.21px;background-color:#de7834;color:#e5e5e5;margin:0 0.5rem 0 0',
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
							'css'                  => 'font-size:10.21px;background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#369ee3;',
							'link_color' => '#369ee3',
						),
					),
					'grey_column'      => array(
						'name'             => 'popup-grey_column',
						'css'              => 'font-size:10.21px;color:#000000;background-color:#f4f4f4;text-align:justify;border:1px solid #111111',
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
							'css'                  => 'font-size:10.21px;background-color:#C1263E;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#C1263E',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#C1263E',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#111111;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
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
							'css'        => 'font-size:14px;color:#C1263E;',
							'link_color' => '#C1263E',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#C1263E;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#C1263E',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#C1263E',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#111111;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#C1263E;',
							'link_color' => '#C1263E',
						),
					),
					'navy_blue_square' => array(
						'name'             => 'popup-navy_blue_square',
						'css'              => 'font-size:10.21px;color:#e5e5e5;background-color:#2a3e71;text-align:justify;',
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
							'css'                  => 'font-size:10.21px;background-color:rgba(54,158,227,0);color:#e5e5e5;width:41%;margin:0 0.5rem 0 0;min-width:5rem;border:1px solid #369ee3;',
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
							'css'                  => 'font-size:10.21px;background-color:rgba(54,158,227,0);color:#e5e5e5;width:41%;min-width:5rem;float:right;border:1px solid #369ee3;',
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
							'css'                  => 'font-size:10.21px;background-color:#369ee3;color:#e5e5e5;width:100%;margin:1rem auto 0 auto;min-width:5rem;',
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
							'css'                  => 'font-size:10.21px;background-color:#de7834;color:#e5e5e5;margin:0 0.5rem 0 0',
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
							'css'                  => 'font-size:10.21px;background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#007cba;',
							'link_color' => '#007cba',
						),
					),
				),
				'modal_widget' => array(
					'default'          => array(
						'name'             => 'widget-default',
						'css'              => 'font-size:10.21px;color:#000000;background-color:#ffffff;text-align:justify;box-shadow: 0px 2px 20px 0px #042a4b1c;',
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
							'css'                  => 'font-size:10.21px;background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#118635',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
							'button_border_radius' => '0',
						),
						'decline'          => array(
							'text'                 => __( 'Decline', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:14px;color:#007cba;',
							'link_color' => '#007cba',
						),
						'settings'         => array(
							'text'                 => __( 'Cookie Settings', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#007cba;color:#ffffff;float:right;',
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
							'css'                  => 'font-size:10.21px;background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#118635',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#007cba;',
							'link_color' => '#007cba',
						),
					),
					'dark'             => array(
						'name'             => 'widget-dark',
						'css'              => 'font-size:10.21px;color:#ffffff;background-color:#262626;text-align:justify;',
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
							'css'                  => 'font-size:10.21px;background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0;border:1px solid #4570dc;',
							'link_color'           => '#ffffff',
							'button_color'         => '#4570dc',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#4570dc',
							'button_border_radius' => '0',
						),
						'decline'          => array(
							'text'                 => 'Decline',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#808080;color:#ffffff;float:none;border:1px solid #808080;',
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
							'css'        => 'font-size:14px;color:#4570dc;',
							'link_color' => '#4570dc',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:rgba(38, 38, 38, 0);color:#808080;float:right;margin:0 0.5rem 0 0;border:1px solid #808080',
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
							'css'                  => 'font-size:10.21px;background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#4570dc',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#4570dc',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#808080;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#4570dc;',
							'link_color' => '#4570dc',
						),
					),
					'almond_column'    => array(
						'name'             => 'widget-almond_column',
						'css'              => 'font-size:10.21px;color:#1e3d59;background-color:#e8ddbb;text-align:justify;',
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
							'css'                  => 'font-size:10.21px;background-color:#c1540c;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#c1540c',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#c1540c',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#252525;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
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
							'css'        => 'font-size:14px;color:#c1540c;',
							'link_color' => '#c1540c',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#c1540c;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#c1540c',
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
							'css'                  => 'font-size:10.21px;background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#c1540c;',
							'link_color' => '#c1540c',
						),
					),
					'navy_blue_box'    => array(
						'name'             => 'widget-navy_blue_box',
						'css'              => 'font-size:10.21px;color:#e5e5e5;background-color:#2a3e71;text-align:justify;border-radius:15px;',
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
							'css'        => 'font-size:14px;color:#369ee3;',
							'link_color' => '#369ee3',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;width:fit-content;',
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
							'css'                  => 'font-size:10.21px;background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;width:fit-content;',
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
							'css'                  => 'font-size:10.21px;background-color:#de7834;color:#e5e5e5;margin:0 0.5rem 0 0',
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
							'css'                  => 'font-size:10.21px;background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#369ee3;',
							'link_color' => '#369ee3',
						),
					),
					'dark_row'         => array(
						'name'             => 'widget-dark_row',
						'css'              => 'font-size:10.21px;color:#ffffff;background-color:#323742;text-align:center;',
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
							'css'                  => 'font-size:10.21px;background-color:#2b806a;color:#ffffff;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid 4570dc',
							'link_color'           => '#ffffff',
							'button_color'         => '#2b806a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:rgba(50, 55, 66, 0);color:#2b806a;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid #2b806a;',
							'link_color'           => '#2b806a',
							'button_color'         => '#323742',
							'button_size'          => 'medium',
							'button_opacity'       => '0',
							'button_border_style'  => 'solid',
							'button_border_width'  => '1',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'readmore'         => array(
							'text'       => 'Read More',
							'as_button'  => false,
							'css'        => 'font-size:14px;color:#2b806a;',
							'link_color' => '#2b806a',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#2b806a;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#2b806a',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#2b806a',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#323742;color:#2b806a;margin:0 0.5rem 0 0',
							'link_color'           => '#2b806a',
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
							'css'        => 'font-size:10.21px;color:#2b806a;',
							'link_color' => '#2b806a',
						),
					),
					'navy_blue_center' => array(
						'name'             => 'widget-navy_blue_center',
						'css'              => 'font-size:10.21px;color:#e5e5e5;background-color:#2a3e71;text-align:center;',
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
							'css'        => 'font-size:14px;color:#369ee3;',
							'link_color' => '#369ee3',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;',
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
							'css'                  => 'font-size:10.21px;background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;',
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
							'css'                  => 'font-size:10.21px;background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
							'link_color'           => '#e5e5e5',
							'button_color'         => '#369ee3',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#118635',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#369ee3;',
							'link_color' => '#369ee3',
						),
					),
					'grey_column'      => array(
						'name'             => 'widget-grey_column',
						'css'              => 'font-size:10.21px;color:#000000;background-color:#f4f4f4;text-align:justify;border: 1px solid #111111;',
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
							'css'                  => 'font-size:10.21px;background-color:#C1263E;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
							'link_color'           => '#ffffff',
							'button_color'         => '#C1263E',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#C1263E',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#111111;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
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
							'css'        => 'font-size:14px;color:#C1263E;',
							'link_color' => '#C1263E',
						),
						'confirm'          => array(
							'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#C1263E;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#C1263E',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#C1263E',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#111111;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#C1263E;',
							'link_color' => '#C1263E',
						),
					),
					'grey_center'      => array(
						'name'             => 'widget-grey_center',
						'css'              => 'font-size:10.21px;color:#000000;background-color:#f4f4f4;text-align:center;',
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
							'css'        => 'font-size:14px;color:#AC4008;',
							'link_color' => '#AC4008',
						),
						'accept'           => array(
							'text'                 => 'Accept',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#AC4008;color:#ffffff;min-width:5rem;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#AC4008',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#AC4008',
							'button_border_radius' => '0',
						),
						'settings'         => array(
							'text'                 => 'Cookie Settings',
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#252525;color:#ffffff;',
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
							'css'                  => 'font-size:10.21px;background-color:#AC4008;color:#ffffff;margin:0 0.5rem 0 0',
							'link_color'           => '#ffffff',
							'button_color'         => '#AC4008',
							'button_size'          => 'medium',
							'button_opacity'       => '1',
							'button_border_style'  => 'none',
							'button_border_width'  => '0',
							'button_border_color'  => '#AC4008',
							'button_border_radius' => '0',
						),
						'cancel'           => array(
							'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
							'as_button'            => true,
							'css'                  => 'font-size:10.21px;background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#AC4008;',
							'link_color' => '#AC4008',
						),
					),
					'navy_blue_square' => array(
						'name'             => 'widget-navy_blue_square',
						'css'              => 'font-size:10.21px;color:#e5e5e5;background-color:#2a3e71;text-align:justify;',
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
							'css'                  => 'font-size:10.21px;background-color:rgba(54,158,227,0);color:#e5e5e5;width:41%;margin:0 0.5rem 0 0;min-width:5rem;border:1px solid #369ee3;',
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
							'css'                  => 'font-size:10.21px;background-color:rgba(54,158,227,0);color:#e5e5e5;width:41%;min-width:5rem;float:right;border:1px solid #369ee3;',
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
							'css'                  => 'font-size:10.21px;background-color:#369ee3;color:#e5e5e5;width:100%;margin:1rem auto 0 auto;min-width:5rem;',
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
							'css'                  => 'font-size:10.21px;background-color:#de7834;color:#e5e5e5;margin:0 0.5rem 0 0',
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
							'css'                  => 'font-size:10.21px;background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
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
							'css'        => 'font-size:10.21px;color:#007cba;',
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
		// Call the is_connected() method from the instantiated object to check if the user is connected.
		$is_user_connected = $this->settings->is_connected();
		$api_user_plan = $this->settings->get_plan();

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
				'api_user_plan' => $api_user_plan,
				'documentation_url' => 'https://wplegalpages.com/docs/wp-cookie-consent/',
				'faq_url' => 'https://wplegalpages.com/docs/wp-cookie-consent/faqs/faq-2/',
				'support_url' => $support_url,
				'upgrade_url' => 'https://club.wpeka.com/product/wp-gdpr-cookie-consent/?utm_source=plugin&utm_medium=gdpr&utm_campaign=help-mascot_&utm_content=upgrade-to-pro',
				'is_user_connected' => $is_user_connected,
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
				'label' => 'Abkhazian',
				'code'  => 'ab',
			),
			array(
				'label' => 'Afar',
				'code'  => 'aa',
			),
			array(
				'label' => 'Afrikaans',
				'code'  => 'af',
			),
			array(
				'label' => 'Albanian',
				'code'  => 'sq',
			),
			array(
				'label' => 'Amharic',
				'code'  => 'am',
			),
			array(
				'label' => 'Arabic',
				'code'  => 'ar',
			),
			array(
				'label' => 'Armenian',
				'code'  => 'hy',
			),
			array(
				'label' => 'Azerbaijani',
				'code'  => 'az',
			),
			array(
				'label' => 'Basque',
				'code'  => 'eu',
			),
			array(
				'label' => 'Belarusian',
				'code'  => 'be',
			),
			array(
				'label' => 'Bengali',
				'code'  => 'bn',
			),
			array(
				'label' => 'Bosnian',
				'code'  => 'bs',
			),
			array(
				'label' => 'Bulgarian',
				'code'  => 'bg',
			),
			array(
				'label' => 'Catalan',
				'code'  => 'ca',
			),
			array(
				'label' => 'Corsican',
				'code'  => 'co',
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
				'label' => 'Esperanto',
				'code'  => 'eo',
			),
			array(
				'label' => 'Finnish',
				'code'  => 'fi',
			),
			array(
				'label' => 'French',
				'code'  => 'fr',
			),
			array(
				'label' => 'Frisian',
				'code'  => 'fy',
			),
			array(
				'label' => 'Galician',
				'code'  => 'gl',
			),
			array(
				'label' => 'Georgian',
				'code'  => 'ka',
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
				'label' => 'Gujarati',
				'code'  => 'gu',
			),
			array(
				'label' => 'Hausa',
				'code'  => 'ha',
			),
			array(
				'label' => 'Hebrew',
				'code'  => 'he',
			),
			array(
				'label' => 'Hindi',
				'code'  => 'hi',
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
				'label' => 'Igbo',
				'code'  => 'ig',
			),
			array(
				'label' => 'Indonesian',
				'code'  => 'id',
			),
			array(
				'label' => 'Irish',
				'code'  => 'ga',
			),
			array(
				'label' => 'Italian',
				'code'  => 'it',
			),
			array(
				'label' => 'Japanese',
				'code'  => 'ja',
			),
			array(
				'label' => 'Kannada',
				'code'  => 'kn',
			),
			array(
				'label' => 'Kazakh',
				'code'  => 'kk',
			),
			array(
				'label' => 'Kirghiz',
				'code'  => 'ky',
			),
			array(
				'label' => 'Korean',
				'code'  => 'ko',
			),
			array(
				'label' => 'Kurdish',
				'code'  => 'ku',
			),
			array(
				'label' => 'Laothian',
				'code'  => 'lo',
			),
			array(
				'label' => 'Latvian',
				'code'  => 'lv',
			),
			array(
				'label' => 'Luxembourgish',
				'code'  => 'lb',
			),
			array(
				'label' => 'Macedonian',
				'code'  => 'mk',
			),
			array(
				'label' => 'Malagasy',
				'code'  => 'mg',
			),
			array(
				'label' => 'Malay',
				'code'  => 'ms',
			),
			array(
				'label' => 'Malayalam',
				'code'  => 'ml',
			),
			array(
				'label' => 'Maltese',
				'code'  => 'mt',
			),
			array(
				'label' => 'Maori',
				'code'  => 'mi',
			),
			array(
				'label' => 'Marathi',
				'code'  => 'mr',
			),
			array(
				'label' => 'Mongolian',
				'code'  => 'mn',
			),
			array(
				'label' => 'Nepali',
				'code'  => 'ne',
			),
			array(
				'label' => 'Norwegian',
				'code'  => 'no',
			),
			array(
				'label' => 'Oriya',
				'code'  => 'or',
			),
			array(
				'label' => 'Pashto',
				'code'  => 'ps',
			),
			array(
				'label' => 'Persian',
				'code'  => 'fa',
			),
			array(
				'label' => 'Polish',
				'code'  => 'po',
			),
			array(
				'label' => 'Portuguese',
				'code'  => 'pt',
			),
			array(
				'label' => 'Punjabi',
				'code'  => 'pa',
			),
			array(
				'label' => 'Romanian',
				'code'  => 'ro',
			),
			array(
				'label' => 'Russian',
				'code'  => 'ru',
			),
			array(
				'label' => 'Samoan',
				'code'  => 'sm',
			),
			array(
				'label' => 'Scots Gaelic',
				'code'  => 'gd',
			),
			array(
				'label' => 'Sesotho',
				'code'  => 'st',
			),
			array(
				'label' => 'Shona',
				'code'  => 'sn',
			),
			array(
				'label' => 'Sindhi',
				'code'  => 'sd',
			),
			array(
				'label' => 'Singhalese',
				'code'  => 'si',
			),
			array(
				'label' => 'Slovak',
				'code'  => 'sk',
			),
			array(
				'label' => 'Slovenian',
				'code'  => 'sl',
			),
			array(
				'label' => 'Somali',
				'code'  => 'so',
			),
			array(
				'label' => 'Spanish',
				'code'  => 'es',
			),
			array(
				'label' => 'Sudanese',
				'code'  => 'su',
			),
			array(
				'label' => 'Swahili',
				'code'  => 'sw',
			),
			array(
				'label' => 'Swedish',
				'code'  => 'sv',
			),
			array(
				'label' => 'Tagalog',
				'code'  => 'tl',
			),
			array(
				'label' => 'Tajik',
				'code'  => 'tg',
			),
			array(
				'label' => 'Tamil',
				'code'  => 'ta',
			),
			array(
				'label' => 'Telugu',
				'code'  => 'te',
			),
			array(
				'label' => 'Thai',
				'code'  => 'th',
			),
			array(
				'label' => 'Turkish',
				'code'  => 'tr',
			),
			array(
				'label' => 'Ukrainian',
				'code'  => 'uk',
			),
			array(
				'label' => 'Urdu',
				'code'  => 'ur',
			),
			array(
				'label' => 'Uzbek',
				'code'  => 'uz',
			),
			array(
				'label' => 'Vietnamese',
				'code'  => 'vi',
			),
			array(
				'label' => 'Welsh',
				'code'  => 'cy',
			),
			array(
				'label' => 'Xhosa',
				'code'  => 'xh',
			),
			array(
				'label' => 'Yiddish',
				'code'  => 'yi',
			),
			array(
				'label' => 'Yoruba',
				'code'  => 'yo',
			),
			array(
				'label' => 'Zulu',
				'code'  => 'zu',
			),
			array(
				'label' => 'Cebuano',
				'code'  => 'ceb',
			),
			array(
				'label' => 'Chinese (Simplified)',
				'code'  => 'zh-cn',
			),
			array(
				'label' => 'Chinese (Traditional)',
				'code'  => 'zh-tw',
			),
			array(
				'label' => 'Estonian',
				'code'  => 'et',
			),
			array(
				'label' => 'Haitian Creole',
				'code'  => 'ht',
			),
			array(
				'label' => 'Hawaiian',
				'code'  => 'haw',
			),
			array(
				'label' => 'Hmong',
				'code'  => 'hmn',
			),
			array(
				'label' => 'Javanese',
				'code'  => 'jw',
			),
			array(
				'label' => 'Khmer',
				'code'  => 'km',
			),
			array(
				'label' => 'Latin',
				'code'  => 'la',
			),
			array(
				'label' => 'Lithuanian',
				'code'  => 'lt',
			),
			array(
				'label' => 'Myanmar (Burmese)',
				'code'  => 'my',
			),
			array(
				'label' => 'Serbian',
				'code'  => 'sr',
			),
			array(
				'label' => 'Uyghur',
				'code'  => 'ug',
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
		$geo_countries     = isset( $geo_countries ) ? $geo_countries : array();
		$response          = wp_remote_get( plugin_dir_url( __FILE__ ) . 'data/countries.json', array( 'sslverify' => false ) );
		$json_data         = wp_remote_retrieve_body( $response );
		$geo_countries     = json_decode( $json_data, true );
		$list_of_countries = array();
		$index             = 0;
		foreach ( $geo_countries as $code => $country ) {
			$list_of_countries[ $index ] = array(
				'label' => $country['name'], // Use the country name as the label
				'code'  => $country['code'],    // Use the country code as the code
			);
			++$index; // Increment the index
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

		$geo_options = get_option( 'wpl_geo_options' );
		$ab_options  = get_option( 'wpl_ab_options' );
		if ( ! is_array( $ab_options ) ) {
			$ab_options = array();
		}
		if ( ! is_array( $geo_options ) ) {
			$geo_options = array();
		}
		if ( ! isset( $geo_options['database_prefix'] ) ) {
			$geo_options['database_prefix']     = wp_generate_password( 32, false, false );
			update_option( 'wpl_geo_options', $geo_options );
		}
		if ( ! isset( $geo_options['enable_geotargeting'] ) ) {
			$geo_options['enable_geotargeting'] = false;
			update_option( 'wpl_geo_options', $geo_options );
		}
		
		wp_enqueue_style( 'gdpr-cookie-consent-integrations' );

		// Require the class file for gdpr cookie consent api framework settings.
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';

		// Instantiate a new object of the GDPR_Cookie_Consent_Settings class.
		$this->settings = new GDPR_Cookie_Consent_Settings();

		// Call the is_connected() method from the instantiated object to check if the user is connected.
		$is_user_connected = $this->settings->is_connected();
		wp_localize_script(
			$this->plugin_name . '-main',
			'settings_obj',
			array(
				'nonce'   						   => wp_create_nonce( 'wpl_save_script_nonce' ), // Generate nonce
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
				'auto_generated_banner_nonce'      => wp_create_nonce( 'auto_generated_banner_nonce' ),
				// added nonce for.
				'import_settings_nonce'            => wp_create_nonce( 'import_settings' ),
				// for pages.
				'list_of_pages'                    => $list_of_pages,
				// for sites.
				'list_of_sites'                    => is_multisite() ? $list_of_sites : null,
				'ab_options'                       => $ab_options,
				'geo_options'                      => $geo_options,
				'is_user_connected'                => $is_user_connected,
				'gdpr_no_of_page_scan'             => get_option( 'gdpr_no_of_page_scan' ),
				// for countries.
				'list_of_countries'                => $list_of_countries,
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
		$ab_options = get_option( 'wpl_ab_options' );
		if($ab_options['ab_testing_enabled'] === 'false' || $ab_options['ab_testing_enabled'] === false  ){
			$options = array(
				__( 'ePrivacy', 'gdpr-cookie-consent' )    => 'eprivacy',
				__( 'GDPR', 'gdpr-cookie-consent' )        => 'gdpr',
				__( 'CCPA', 'gdpr-cookie-consent' )        => 'ccpa',
				__( 'LGPD', 'gdpr-cookie-consent' )        => 'lgpd',
				__( 'GDPR & CCPA', 'gdpr-cookie-consent' ) => 'both',
			);
		}else{
			$options = array(
				__( 'ePrivacy', 'gdpr-cookie-consent' )    => 'eprivacy',
				__( 'GDPR', 'gdpr-cookie-consent' )        => 'gdpr',
				__( 'CCPA', 'gdpr-cookie-consent' )        => 'ccpa',
				__( 'LGPD', 'gdpr-cookie-consent' )        => 'lgpd',
			);
		}
		
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

			$the_options                          = Gdpr_Cookie_Consent::gdpr_get_settings();
			$the_options['is_on']                 = isset( $_POST['gcc-cookie-enable'] ) && ( true === $_POST['gcc-cookie-enable'] || 'true' === $_POST['gcc-cookie-enable'] ) ? 'true' : 'true';
			$the_options['cookie_usage_for']      = isset( $_POST['gcc-gdpr-policy'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-gdpr-policy'] ) ) : 'gdpr';
			$the_options['cookie_bar_as']         = isset( $_POST['show-cookie-as'] ) ? sanitize_text_field( wp_unslash( $_POST['show-cookie-as'] ) ) : 'banner';
			$the_options['button_accept_is_on']   = isset( $_POST['gcc-cookie-accept-enable'] ) && ( true === $_POST['gcc-cookie-accept-enable'] || 'true' === $_POST['gcc-cookie-accept-enable'] ) ? 'true' : 'true';
			$the_options['show_again']            = isset( $_POST['gcc-revoke-consent-enable'] ) && ( true === $_POST['gcc-revoke-consent-enable'] || 'true' === $_POST['gcc-revoke-consent-enable'] ) ? 'true' : 'true';
			$the_options['button_decline_is_on']  = isset( $_POST['gcc-cookie-decline-enable'] ) && ( true === $_POST['gcc-cookie-decline-enable'] || 'true' === $_POST['gcc-cookie-decline-enable'] ) ? 'true' : 'true';
			$the_options['button_settings_is_on'] = isset( $_POST['gcc-cookie-settings-enable'] ) && ( true === $_POST['gcc-cookie-settings-enable'] || 'true' === $_POST['gcc-cookie-settings-enable'] ) ? 'true' : 'true';
			// revoke consent text color.
			$the_options['button_revoke_consent_text_color']       = isset( $_POST['gcc-revoke-consent-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-text-color'] ) ) : '';
			$the_options['button_revoke_consent_background_color'] = isset( $_POST['gcc-revoke-consent-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-background-color'] ) ) : '';
			$the_options['notify_position_vertical']           = isset( $_POST['gcc-gdpr-cookie-position'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-gdpr-cookie-position'] ) ) : 'bottom';
			$the_options['notify_position_horizontal']         = isset( $_POST['gcc-gdpr-cookie-widget-position'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-gdpr-cookie-widget-position'] ) ) : 'left';
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
						
						if(!$the_options['is_eu_on']){
							$this->auto_update_maxminddb();
							$this->download_maxminddb();
						}
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
						if(!$the_options['is_ccpa_on'] ){
							$this->auto_update_maxminddb();
							$this->download_maxminddb();
						}
						$the_options['is_ccpa_on'] = 'true';
					}
				}
				// for World wide.
				if ( isset( $_POST['gcc-worldwide-enable'] ) ) {
					if ( 'no' === $_POST['gcc-worldwide-enable'] ) {
						$the_options['is_worldwide_on'] = 'false';
					} elseif ( 'false' == $_POST['gcc-worldwide-enable'] ) {
						$the_options['is_worldwide_on'] = 'false';
					} else {
						if(!$the_options['is_worldwide_on']){
							$this->disable_auto_update_maxminddb();
						}
						$the_options['is_worldwide_on'] = 'true';
					}
				}
				// For select country dropdown.
				if ( isset( $_POST['gcc-select-countries-enable'] ) ) {
					if ( 'no' === $_POST['gcc-select-countries-enable'] ) {
						$the_options['is_selectedCountry_on'] = 'false';
					} elseif ( 'false' == $_POST['gcc-select-countries-enable'] ) {
						$the_options['is_selectedCountry_on'] = 'false';
					} else {
						if(!$the_options['is_selectedCountry_on']){
							$this->auto_update_maxminddb();
							$this->download_maxminddb();
						}
						$the_options['is_selectedCountry_on'] = 'true';
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

			// Show the first 5 templates
			$templates_to_show = array_slice( $templates, 0, 5 );
			foreach ( $templates_to_show as $key => $template ) :
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
									<p v-html ="gdpr_message"></p>
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
										<p><a style="<?php echo esc_attr( $template['readmore']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{button_readmore_text}}</a></p>
									<?php endif; ?>
								<?php else : ?>
									<p><?php if ( $the_options['cookie_usage_for'] === 'gdpr' || $the_options['cookie_usage_for'] === 'both' ) : ?>
									<p v-html ="gdpr_message"></p>
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
											<a style="<?php echo esc_attr( $template['readmore']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{button_readmore_text}}</a>
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
										<a style="<?php echo esc_attr( $template['decline']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ decline_text }}</a>
										<?php endif; ?>
										<?php
										if ( isset( $template['settings'] ) ) :
											$class = '';
											if ( $template['settings']['as_button'] ) :
												$class = 'btn btn-sm';
											endif;
											?>
										<a style="<?php echo esc_attr( $template['settings']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ settings_text }}</a>
										<?php endif; ?>
										<?php
										if ( isset( $template['accept'] ) ) :
											$class = '';
											if ( $template['accept']['as_button'] ) :
												$class = 'btn btn-sm';
											endif;
											?>
										<a style="<?php echo esc_attr( $template['accept']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ accept_text }}</a>
										<?php endif; ?>
								<?php else : ?>
									<?php
									if ( isset( $template['accept'] ) ) :
										$class = '';
										if ( $template['accept']['as_button'] ) :
											$class = 'btn btn-sm';
										endif;
										?>
										<a style="<?php echo esc_attr( $template['accept']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ accept_text }}</a>
									<?php endif; ?>
									<?php
									if ( isset( $template['decline'] ) ) :
										$class = '';
										if ( $template['decline']['as_button'] ) :
											$class = 'btn btn-sm';
										endif;
										?>
										<a style="<?php echo esc_attr( $template['decline']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ decline_text }}</a>
									<?php endif; ?>
									<?php
									if ( isset( $template['settings'] ) ) :
										$class = '';
										if ( $template['settings']['as_button'] ) :
											$class = 'btn btn-sm';
										endif;
										?>
										<a style="<?php echo esc_attr( $template['settings']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ settings_text }}</a>
									<?php endif; ?>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
				<p class="gdpr-configuration-line-divider"></p>
				<?php endforeach; ?>
			</div>

			
			<!-- Modal for the show more templates -->
			<c-card-body class="show-more-modal-card-container">
				<c-row>
				<c-col class="col-sm-12 show-more-modal-card-container-column">
					<c-button class="show-more-cookie-design" @click="show_more_cookie_design_popup=true">
						<span class="show-more-design-text">
							<?php esc_attr_e( 'More Templates' ); ?>
						</span>
					</c-button>
				</c-col>
				</c-row>
			
				<c-modal
					title="More Template Designs"
					:show.sync="show_more_cookie_design_popup"
					size="lg"
					:close-on-backdrop="closeOnBackdrop"
					:centered="centered"
					class="more-design-modal-container"
				>
					<c-row>
					<?php $this->get_more_templates_modal_data($name); ?>
					</c-row>
				</c-modal>
			</c-card-body>

			<?php
	}

	// Show the modal based on the type of the template
	public function get_more_templates_modal_data($name) {
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		$templates = [];
		if($name === 'banner') {
			$templates = $this->get_templates( 'modal_banner' );
		} elseif ($name === 'popup') {
			$templates = $this->get_templates( 'modal_popup' );
		} elseif ($name === 'widget') {
			$templates = $this->get_templates( 'modal_widget' );
		}
		$checked = $the_options['banner_template'];

		$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
		$the_options    = Gdpr_Cookie_Consent::gdpr_get_settings();
	
		foreach ( $templates as $key => $template ) {
			$column = false !== strpos( $template['name'], 'column' );
			$square = false !== strpos( $template['name'], 'square' );
			?>
			<div class="modal-template-field gdpr-<?php echo esc_attr( $template['name'] ); ?>">
				<div class="modal-left-field">
					<c-input type="radio" name="<?php echo esc_attr( $name ) . '_template_field'; ?>" value="<?php echo esc_attr( $template['name'] ); ?>" @change="onTemplateChange"
					<?php if ( $template['name'] === $checked ) {
						echo ':checked="true"';
					} ?>
					>
				</div>
				<div class="modal-right-field" style="<?php echo esc_attr( $template['css'] ); ?>">
					<div class="modal-right-field-content <?php $this->is_column_or_square($column) ?>">
						<div class="modal-group-description">
							<?php
							$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
							if ( ! empty( $get_banner_img ) ) {
								?>
								<img class="modal_logo_image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
								<?php
							}
							?>
							<?php if ( in_array( $the_options['cookie_usage_for'], ['gdpr', 'both'], true ) ) : ?>
								<h3 v-if="gdpr_message_heading.length>0">{{ gdpr_message_heading }}</h3>
							<?php elseif ( $the_options['cookie_usage_for'] === 'lgpd' ) : ?>
								<h3 v-if="lgpd_message_heading.length>0">{{ lgpd_message_heading }}</h3>
							<?php endif; ?>
	
							<?php if ( $column ) : ?>
								<?php if ( in_array( $the_options['cookie_usage_for'], ['gdpr', 'both'], true ) ) : ?>
									<p v-html="gdpr_message"></p>
								<?php elseif ( $the_options['cookie_usage_for'] === 'lgpd' ) : ?>
									<p>{{ lgpd_message }}</p>
								<?php endif; ?>
								<?php if ( isset( $template['readmore'] ) ) :
									$class = $template['readmore']['as_button'] ? 'btn btn-sm' : '';
									?>
									<p><a style="<?php echo esc_attr( $template['readmore']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ button_readmore_text }}</a></p>
								<?php endif; ?>
							<?php else : ?>
								<p>
									<?php if ( in_array( $the_options['cookie_usage_for'], ['gdpr', 'both'], true ) ) : ?>
										<p v-html="gdpr_message"></p>
									<?php elseif ( $the_options['cookie_usage_for'] === 'lgpd' ) : ?>
										<p>{{ lgpd_message }}</p>
									<?php endif; ?>
									<?php if ( isset( $template['readmore'] ) ) :
										$class = $template['readmore']['as_button'] ? 'btn btn-sm' : '';
										?>
										<a style="<?php echo esc_attr( $template['readmore']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ button_readmore_text }}</a>
									<?php endif; ?>
								</p>
							<?php endif; ?>
						</div>
	
						<div class="modal-group-buttons">
							<?php if ( $square ) : ?>
								<?php if ( isset( $template['decline'] ) ) :
									$class = $template['decline']['as_button'] ? 'btn btn-sm' : '';
									?>
									<a style="<?php echo esc_attr( $template['decline']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ decline_text }}</a>
								<?php endif; ?>
								<?php if ( isset( $template['settings'] ) ) :
									$class = $template['settings']['as_button'] ? 'btn btn-sm' : '';
									?>
									<a style="<?php echo esc_attr( $template['settings']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ settings_text }}</a>
								<?php endif; ?>
								<?php if ( isset( $template['accept'] ) ) :
									$class = $template['accept']['as_button'] ? 'btn btn-sm' : '';
									?>
									<a style="<?php echo esc_attr( $template['accept']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ accept_text }}</a>
								<?php endif; ?>
							<?php else : ?>
								<?php if ( isset( $template['accept'] ) ) :
									$class = $template['accept']['as_button'] ? 'btn btn-sm' : '';
									?>
									<a style="<?php echo esc_attr( $template['accept']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ accept_text }}</a>
								<?php endif; ?>
								<?php if ( isset( $template['decline'] ) ) :
									$class = $template['decline']['as_button'] ? 'btn btn-sm' : '';
									?>
									<a style="<?php echo esc_attr( $template['decline']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ decline_text }}</a>
								<?php endif; ?>
								<?php if ( isset( $template['settings'] ) ) :
									$class = $template['settings']['as_button'] ? 'btn btn-sm' : '';
									?>
									<a style="<?php echo esc_attr( $template['settings']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ settings_text }}</a>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}

	public function is_column_or_square(bool $column) {
		if($column) {
			echo "modal-right-field-content-column";
		} else {
			echo "modal-right-field-content-square";
		}
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
					<c-row>
						<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Cookie Bar Template', 'gdpr-cookie-consent' ); ?></div></c-col>
					</c-row>
					<c-row v-show="!show_banner_template">
						<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cookie Templates', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Use a pre-built template to style your Cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
						<c-col class="col-sm-8">
							<div role="group" class="form-group">
								<span class="gdpr-cookie-consent-description"><?php esc_attr_e( 'To preview the pre-built templates below, simply choose a template and then click the "Save Changes" button. Please note that this action will replace your current banner settings.', 'gdpr-cookie-consent' ); ?></span>
							</div>
						</c-col>
					</c-row>
					<c-row v-show="show_banner_template">
						<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cookie Templates', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Use a pre-built template to style your Cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
						<c-col class="col-sm-8">
							<div role="group" class="form-group">
								<span class="gdpr-cookie-consent-description"><?php esc_attr_e( 'To preview the pre-built templates below, simply choose a template and then click the "Save Changes" button. Please note that this action will replace your current banner settings.', 'gdpr-cookie-consent' ); ?></span>
							</div>
						</c-col>
					</c-row>
					<c-row>
						<c-col class="col-sm-4 relative"><label><?php esc_attr_e( 'Auto Generate Banner', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enable this setting to automatically generate a cookie banner that matches your website\'s color theme, ensuring seamless integration with your design.
						', 'gdpr-cookie-consent' ); ?>"></tooltip></label></label>
						</c-col>
						<c-col class="col-sm-8">
						<input type="button" name="gcc-cookie-consent-auto_generated_banner" value="<?php  
							if (isset($the_options['is_banner_auto_generated']) && 
							($the_options['is_banner_auto_generated'] === 'true' ||  
							 $the_options['is_banner_auto_generated'] === true ||  
							 $the_options['is_banner_auto_generated'] === '1')) {
							echo 'Generate Again';
							} else {
								echo 'Generate Now';
							}
						?>"  id="gdpr-cookie-consent-auto_generated_banner" @click="onSwitchAutoGeneratedBanner" :disabled="processof_auto_template_generated">
						</c-col>
						<div>
							<!-- Show dashicon if the database value is true -->
							<?php $the_options    = Gdpr_Cookie_Consent::gdpr_get_settings();
								if (isset($the_options['is_banner_auto_generated']) && 
								($the_options['is_banner_auto_generated'] === 'true' ||  
								 $the_options['is_banner_auto_generated'] === true ||  
								 $the_options['is_banner_auto_generated'] === '1')) { ?>
								<span 
									id="gdpr-auto-generated-banner-tick" 
									class="dashicons dashicons-saved"
								></span>
							<?php } ?>
						</div>
					</c-row>
					<c-row>
						<c-col class="col-sm-4"></c-col>
						<c-col class="col-sm-8">
								<div role="group" class="form-group">
									<span class="gdpr-cookie-consent-description"><?php esc_attr_e( 'To preview the auto generated template, simply click on auto generate template button above and then click the "Save Changes" button.', 'gdpr-cookie-consent' ); ?></span>
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
							<c-col class="col-sm-3">
								<input type="hidden" name="gdpr-popup-template" v-model="popup_template">
							</c-col>
							<c-col class="col-sm-9">
					<?php $this->print_template_boxes( 'popup', $this->get_templates( 'popup' ), $the_options['popup_template'] ); ?>
							</c-col>
						</c-row>
						<c-row v-show="show_widget_template">
							<c-col class="col-sm-3">
								<input type="hidden" name="gdpr-widget-template" v-model="widget_template">
							</c-col>
							<c-col class="col-sm-9">
					<?php $this->print_template_boxes( 'widget', $this->get_templates( 'widget' ), $the_options['widget_template'] ); ?>
							</c-col>
					</c-row>
					<input type="hidden" name="gdpr-template" v-model="template">
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
			$the_options    = Gdpr_Cookie_Consent::gdpr_get_settings();
			$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' );
			$ab_options     = get_option( 'wpl_ab_options' );
			if ( ! $ab_options ) {
				$ab_options = array();
			}
			
			// Get the current A/B testing period value
			$current_ab_testing_value = isset($ab_options['ab_testing_period']) ? $ab_options['ab_testing_period'] : '';

			// Set the new A/B testing period value from POST
			$ab_options['ab_testing_period'] = isset($_POST['ab_testing_period_text_field']) ? sanitize_text_field(wp_unslash($_POST['ab_testing_period_text_field'])) : '';

			// Get the updated A/B testing period value
			$updated_ab_testing_value = isset($ab_options['ab_testing_period']) ? $ab_options['ab_testing_period'] : '';

			// Check if the value of the A/B testing period has changed
			if ($current_ab_testing_value !== $updated_ab_testing_value) {

				// Get the transient expiration time if the transient already exists
				$transient_name = '_transient_timeout_gdpr_ab_testing_transient';
				$expiration_time = get_option($transient_name);
				
				// Check if the transient exists (the value is retrieved)
				if ($expiration_time) {
					// Convert the expiration time to a human-readable format
					$expiration_time = date('Y-m-d H:i:s', $expiration_time);
					
					// Get the current date and time
					$current_date_time = date('Y-m-d H:i:s');

					// Calculate the difference in time between the current time and the expiration time
					$current_time_unix = strtotime($current_date_time);
					$expiration_time_unix = strtotime($expiration_time);
					
					// Calculate the remaining time in seconds
					$remaining_time_seconds = $expiration_time_unix - $current_time_unix;

					// Calculate the remaining days
					$remaining_days = ceil($remaining_time_seconds / (60 * 60 * 24));

					// If the user changes the days value, update the transient expiration time
					$new_expiration_time_seconds = ($updated_ab_testing_value * 24 * 60 * 60); // New expiration time in seconds
					
					// If the new expiration time is longer or shorter, update the transient accordingly
					if ($remaining_days != $updated_ab_testing_value) {
						$new_expiration_timestamp = $current_time_unix + $new_expiration_time_seconds;
						set_transient(
							'gdpr_ab_testing_transient',
							array(
								'value'         => 'A/B Testing Period',
								'creation_time' => time(),
							),
							$new_expiration_time_seconds
						);
					}
				} else {
					// If the transient doesn't exist, create it with the new expiration time
					$new_expiration_time_seconds = ($updated_ab_testing_value * 24 * 60 * 60);
					set_transient(
						'gdpr_ab_testing_transient',
						array(
							'value'         => 'A/B Testing Period',
							'creation_time' => time(),
						),
						$new_expiration_time_seconds
					);
				}
			}


			if (isset($_POST['gcc-ab-testing-enable']) 
			&& ($_POST['gcc-ab-testing-enable'] === true || $_POST['gcc-ab-testing-enable'] === 'true') 
			&& (!isset($ab_options['ab_testing_enabled']) 
				|| $ab_options['ab_testing_enabled'] === 'false' 
				|| $ab_options['ab_testing_enabled'] === false)) {
				$ab_options ['necessary1']  = 0;
				$ab_options ['marketing1']  = 0;
				$ab_options ['analytics1']  = 0;
				$ab_options ['DNT1']        = 0;
				$ab_options ['noChoice1']   = 0;
				$ab_options ['noWarning1']  = 0;
				$ab_options ['necessary2']  = 0;
				$ab_options ['marketing2']  = 0;
				$ab_options ['analytics2']  = 0;
				$ab_options ['DNT2']        = 0;
				$ab_options ['noChoice2']   = 0;
				$ab_options ['noWarning2']  = 0;
				$ab_transient_creation_time = time();
				set_transient(
					'gdpr_ab_testing_transient',
					array(
						'value'         => 'A/B Testing Period',
						'creation_time' => $ab_transient_creation_time,
					),
					(int) $ab_options['ab_testing_period'] * 24 * 60 * 60
				);
			}

			$the_options['lang_selected'] = isset( $_POST['select-banner-lan'] ) ? sanitize_text_field( wp_unslash( $_POST['select-banner-lan'] ) ) : 'en';
			//check if new consent version number is greater than the one in db, if yes, update the time stamp.
			if(isset($the_options['consent_version']) && isset($_POST['gcc-consent-renew-enable']) && $_POST['gcc-consent-renew-enable'] > $the_options['consent_version']){
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
			}
			//consent version for renew consent
			$the_options['consent_version'] = isset($the_options['consent_version']) ? (isset($_POST['gcc-consent-renew-enable']) ? sanitize_text_field( wp_unslash( $_POST['gcc-consent-renew-enable'] ) ) : $the_options['consent_version']) : 1;
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
			$the_options['cookie_bar1_name']                   = isset( $_POST['gdpr-cookie_bar1_name'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie_bar1_name'] ) ) : 'Test Banner A';
			$the_options['default_cookie_bar']                 = isset( $_POST['gdpr-default_cookie_bar'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-default_cookie_bar'] ) ) : true;
			$the_options['cookie_bar_color1']                  = isset( $_POST['gdpr-cookie-bar-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-color1'] ) ) : '#ffffff';
			$the_options['cookie_text_color1']                 = isset( $_POST['gdpr-cookie-text-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-text-color1'] ) ) : '#000000';
			$the_options['cookie_bar_opacity1']                = isset( $_POST['gdpr-cookie-bar-opacity1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-opacity1'] ) ) : '0.80';
			$the_options['cookie_bar_border_width1']           = isset( $_POST['gdpr-cookie-bar-border-width1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-border-width1'] ) ) : '0';
			$the_options['border_style1']                      = isset( $_POST['gdpr-cookie-border-style1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-border-style1'] ) ) : 'none';
			$the_options['cookie_border_color1']               = isset( $_POST['gdpr-cookie-border-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-border-color1'] ) ) : '#ffffff';
			$the_options['cookie_bar_border_radius1']          = isset( $_POST['gdpr-cookie-bar-border-radius1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-border-radius1'] ) ) : '0';
			$the_options['cookie_font1']                       = isset( $_POST['gdpr-cookie-font1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-font1'] ) ) : 'inherit';
			$the_options['button_accept_is_on']                = isset( $_POST['gcc-cookie-accept-enable'] ) && ( true === $_POST['gcc-cookie-accept-enable'] || 'true' === $_POST['gcc-cookie-accept-enable'] ) ? 'true' : 'false';
			$the_options['button_accept_text']                 = isset( $_POST['button_accept_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_accept_text_field'] ) ) : 'Accept';
			$the_options['button_accept_button_size']          = isset( $_POST['gdpr-cookie-accept-size'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-size'] ) ) : 'medium';
			$the_options['button_accept_action']               = isset( $_POST['gdpr-cookie-accept-action'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-action'] ) ) : '#cookie_action_close_header';
			$the_options['button_accept_url']                  = isset( $_POST['gdpr-cookie-accept-url'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-url'] ) ) : '#';
			$the_options['button_accept_as_button']            = isset( $_POST['gdpr-cookie-accept-as'] ) && ( true === $_POST['gdpr-cookie-accept-as'] || 'true' === $_POST['gdpr-cookie-accept-as'] ) ? 'true' : 'false';
			$the_options['button_accept_new_win']              = isset( $_POST['gdpr-cookie-url-new-window'] ) && ( true === $_POST['gdpr-cookie-url-new-window'] || 'true' === $_POST['gdpr-cookie-url-new-window'] ) ? 'true' : 'false';
			if($the_options['auto_generated_background_color'] = ""){
				$the_options['button_accept_button_color']         = isset( $_POST['gdpr-cookie-accept-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-background-color'] ) ) : '#18a300';
				$the_options['button_accept_button_border_color']  = isset( $_POST['gdpr-cookie-accept-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-color'] ) ) : '#18a300';
				$the_options['button_decline_link_color']             = isset( $_POST['gdpr-cookie-decline-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-text-color'] ) ) : '#ffffff';
				$the_options['button_decline_button_border_color']    = isset( $_POST['gdpr-cookie-decline-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-color'] ) ) : '#333333';
				$the_options['button_settings_link_color']            = isset( $_POST['gdpr-cookie-settings-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-text-color'] ) ) : '#ffffff';
				$the_options['button_settings_button_border_color']   = isset( $_POST['gdpr-cookie-settings-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-color'] ) ) : '#333333';
				$the_options['button_settings_button_color']          = isset( $_POST['gdpr-cookie-settings-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-background-color'] ) ) : '#333333';
				$the_options['button_decline_button_color']           = isset( $_POST['gdpr-cookie-decline-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-background-color'] ) ) : '#333333';
				$the_options['button_decline_button_border_style']    = isset( $_POST['gdpr-cookie-decline-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-style'] ) ) : 'none';
				$the_options['button_decline_button_border_width']    = isset( $_POST['gdpr-cookie-decline-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-width'] ) ) : '0';
				$the_options['button_settings_button_border_style']   = isset( $_POST['gdpr-cookie-settings-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-style'] ) ) : 'none';
				$the_options['button_settings_button_border_width']   = isset( $_POST['gdpr-cookie-settings-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-width'] ) ) : '0';
				// A-B Testing Banner 1 
				$the_options['button_accept_button_color1']         = isset( $_POST['gdpr-cookie-accept-background-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-background-color1'] ) ) : '#18a300';
				$the_options['button_accept_button_border_color1']  = isset( $_POST['gdpr-cookie-accept-border-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-color1'] ) ) : '#18a300';
				$the_options['button_decline_link_color1']            = isset( $_POST['gdpr-cookie-decline-text-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-text-color1'] ) ) : '#ffffff';
				$the_options['button_decline_button_border_color1']   = isset( $_POST['gdpr-cookie-decline-border-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-color1'] ) ) : '#333333';
				$the_options['button_settings_link_color1']           = isset( $_POST['gdpr-cookie-settings-text-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-text-color1'] ) ) : '#ffffff';
				$the_options['button_settings_button_border_color1']  = isset( $_POST['gdpr-cookie-settings-border-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-color1'] ) ) : '#333333';
				$the_options['button_settings_button_color1']         = isset( $_POST['gdpr-cookie-settings-background-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-background-color1'] ) ) : '#333333';
				$the_options['button_decline_button_color1']          = isset( $_POST['gdpr-cookie-decline-background-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-background-color1'] ) ) : '#333333';
				$the_options['button_decline_button_border_style1']   = isset( $_POST['gdpr-cookie-decline-border-style1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-style1'] ) ) : 'none';
				$the_options['button_decline_button_border_width1']   = isset( $_POST['gdpr-cookie-decline-border-width1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-width1'] ) ) : '0';
				$the_options['button_settings_button_border_style1']  = isset( $_POST['gdpr-cookie-settings-border-style1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-style1'] ) ) : 'none';
				$the_options['button_settings_button_border_width1']  = isset( $_POST['gdpr-cookie-settings-border-width1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-width1'] ) ) : '0';
				// A-B Testing Banner 2 
				$the_options['button_accept_button_color2']         = isset( $_POST['gdpr-cookie-accept-background-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-background-color2'] ) ) : '#18a300';
				$the_options['button_accept_button_border_color2']    = isset( $_POST['gdpr-cookie-accept-border-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-color2'] ) ) : '#18a300';
				$the_options['button_decline_link_color2']            = isset( $_POST['gdpr-cookie-decline-text-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-text-color2'] ) ) : '#ffffff';
				$the_options['button_decline_button_border_color2']   = isset( $_POST['gdpr-cookie-decline-border-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-color2'] ) ) : '#333333';
				$the_options['button_settings_link_color2']           = isset( $_POST['gdpr-cookie-settings-text-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-text-color2'] ) ) : '#ffffff';
				$the_options['button_settings_button_border_color2']  = isset( $_POST['gdpr-cookie-settings-border-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-color2'] ) ) : '#333333';
				$the_options['button_settings_button_color2']         = isset( $_POST['gdpr-cookie-settings-background-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-background-color2'] ) ) : '#333333';
				$the_options['button_decline_button_color2']          = isset( $_POST['gdpr-cookie-decline-background-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-background-color2'] ) ) : '#333333';
				$the_options['button_decline_button_border_style2']   = isset( $_POST['gdpr-cookie-decline-border-style2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-style2'] ) ) : 'none';
				$the_options['button_decline_button_border_width2']   = isset( $_POST['gdpr-cookie-decline-border-width2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-width2'] ) ) : '0';
				$the_options['button_settings_button_border_style2']  = isset( $_POST['gdpr-cookie-settings-border-style2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-style2'] ) ) : 'none';
				$the_options['button_settings_button_border_width2']  = isset( $_POST['gdpr-cookie-settings-border-width2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-width2'] ) ) : '0';
			}
			$the_options['button_accept_button_opacity']       = isset( $_POST['gdpr-cookie-accept-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-opacity'] ) ) : '1';
			$the_options['button_accept_button_border_style']  = isset( $_POST['gdpr-cookie-accept-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-style'] ) ) : 'none';
			$the_options['button_accept_button_border_width']  = isset( $_POST['gdpr-cookie-accept-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-width'] ) ) : '0';
			$the_options['button_accept_button_border_radius'] = isset( $_POST['gdpr-cookie-accept-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-radius'] ) ) : '0';
			$the_options['button_accept_link_color']           = isset( $_POST['gdpr-cookie-accept-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-text-color'] ) ) : '#ffffff';
			$the_options['button_accept_is_on1']              = isset( $_POST['gcc-cookie-accept-enable1'] ) && ( true === $_POST['gcc-cookie-accept-enable1'] || 'true' === $_POST['gcc-cookie-accept-enable1'] ) ? 'true' : 'false';
			$the_options['button_accept_all_is_on1']              = isset( $_POST['gcc-cookie-accept-all-enable1'] ) && ( true === $_POST['gcc-cookie-accept-all-enable1'] || 'true' === $_POST['gcc-cookie-accept-all-enable1'] ) ? 'true' : 'false';
			$the_options['button_accept_text1']                 = isset( $_POST['button_accept_text_field1'] ) ? sanitize_text_field( wp_unslash( $_POST['button_accept_text_field1'] ) ) : 'Accept';
			$the_options['button_accept_button_size1']          = isset( $_POST['gdpr-cookie-accept-size1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-size1'] ) ) : 'medium';
			$the_options['button_accept_action1']               = isset( $_POST['gdpr-cookie-accept-action1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-action1'] ) ) : '#cookie_action_close_header';
			$the_options['button_accept_url1']                  = isset( $_POST['gdpr-cookie-accept-url1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-url1'] ) ) : '#';
			$the_options['button_accept_as_button1']            = isset( $_POST['gdpr-cookie-accept-as1'] ) && ( true === $_POST['gdpr-cookie-accept-as1'] || 'true' === $_POST['gdpr-cookie-accept-as1'] ) ? 'true' : 'false';
			$the_options['button_accept_new_win1']              = isset( $_POST['gdpr-cookie-url-new-window1'] ) && ( true === $_POST['gdpr-cookie-url-new-window1'] || 'true' === $_POST['gdpr-cookie-url-new-window1'] ) ? 'true' : 'false';
			$the_options['button_accept_button_opacity1']       = isset( $_POST['gdpr-cookie-accept-opacity1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-opacity1'] ) ) : '1';
			$the_options['button_accept_button_border_style1']  = isset( $_POST['gdpr-cookie-accept-border-style1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-style1'] ) ) : 'none';
			$the_options['button_accept_button_border_width1']  = isset( $_POST['gdpr-cookie-accept-border-width1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-width1'] ) ) : '0';
			$the_options['button_accept_button_border_radius1'] = isset( $_POST['gdpr-cookie-accept-border-radius1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-radius1'] ) ) : '0';
			$the_options['button_accept_link_color1']           = isset( $_POST['gdpr-cookie-accept-text-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-text-color1'] ) ) : '#ffffff';

			// Multiple Legislation POST data.
			$the_options['multiple_legislation_cookie_bar_color1'] = isset( $_POST['gdpr-multiple-legislation-cookie-bar-color1']) ? sanitize_text_field( (wp_unslash($_POST['gdpr-multiple-legislation-cookie-bar-color1']))) : '#ffffff';
			$the_options['multiple_legislation_cookie_bar_color2'] = isset( $_POST['gdpr-multiple-legislation-cookie-bar-color2']) ? sanitize_text_field( (wp_unslash($_POST['gdpr-multiple-legislation-cookie-bar-color2']))) : '#ffffff';
			$the_options['multiple_legislation_cookie_bar_opacity1'] = isset( $_POST['gdpr-multiple-legislation-cookie-bar-opacity1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-multiple-legislation-cookie-bar-opacity1'] ) ) : '0.80';
			$the_options['multiple_legislation_cookie_bar_opacity2'] = isset( $_POST['gdpr-multiple-legislation-cookie-bar-opacity2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-multiple-legislation-cookie-bar-opacity2'] ) ) : '0.80';
			$the_options['multiple_legislation_cookie_text_color1'] = isset( $_POST['gdpr-multiple-legislation-cookie-text-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-multiple-legislation-cookie-text-color1'] ) ) : '#000000';
			$the_options['multiple_legislation_cookie_text_color2'] = isset( $_POST['gdpr-multiple-legislation-cookie-text-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-multiple-legislation-cookie-text-color2'] ) ) : '#000000';
			$the_options['multiple_legislation_border_style1'] = isset( $_POST['gdpr-multiple-legislation-cookie-border-style1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-multiple-legislation-cookie-border-style1'] ) ) : 'none';
			$the_options['multiple_legislation_border_style2'] = isset( $_POST['gdpr-multiple-legislation-cookie-border-style2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-multiple-legislation-cookie-border-style2'] ) ) : 'none';
			$the_options['multiple_legislation_cookie_bar_border_width1'] = isset( $_POST['gdpr-multiple-legislation-cookie-bar-border-width1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-multiple-legislation-cookie-bar-border-width1'] ) ) : '0';
			$the_options['multiple_legislation_cookie_bar_border_width2'] = isset( $_POST['gdpr-multiple-legislation-cookie-bar-border-width2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-multiple-legislation-cookie-bar-border-width2'] ) ) : '0';
			$the_options['multiple_legislation_cookie_border_color1'] = isset( $_POST['gdpr-multiple-legislation-cookie-border-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-multiple-legislation-cookie-border-color1'] ) ) : '#ffffff';
			$the_options['multiple_legislation_cookie_border_color2'] = isset( $_POST['gdpr-multiple-legislation-cookie-border-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-multiple-legislation-cookie-border-color2'] ) ) : '#ffffff';
			$the_options['multiple_legislation_cookie_bar_border_radius1'] = isset( $_POST['gdpr-multiple-legislation-cookie-bar-border-radius1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-multiple-legislation-cookie-bar-border-radius1'] ) ) : '0';
			$the_options['multiple_legislation_cookie_bar_border_radius2'] = isset( $_POST['gdpr-multiple-legislation-cookie-bar-border-radius2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-multiple-legislation-cookie-bar-border-radius2'] ) ) : '0';
			$the_options['multiple_legislation_cookie_font1'] = isset( $_POST['gdpr-multiple-legislation-cookie-font1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-multiple-legislation-cookie-font1'] ) ) : 'inherit';
			$the_options['multiple_legislation_cookie_font2'] = isset( $_POST['gdpr-multiple-legislation-cookie-font2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-multiple-legislation-cookie-font2'] ) ) : 'inherit';
			
			$the_options['notify_message_eprivacy']             = isset( $_POST['notify_message_eprivacy_field'] ) ? wp_kses(
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
			$the_options['bar_heading_text']                    = isset( $_POST['bar_heading_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['bar_heading_text_field'] ) ) : '';
			$the_options['bar_heading_lgpd_text']               = isset( $_POST['bar_heading_text_lgpd_field'] ) ? sanitize_text_field( wp_unslash( $_POST['bar_heading_text_lgpd_field'] ) ) : '';

			// custom css.
			$the_options['gdpr_css_text'] = isset( $_POST['gdpr_css_text_field'] ) ? wp_kses( wp_unslash( $_POST['gdpr_css_text_field'] ), array(), array( 'style' => array() ) ) : '';
			$css_file_path                = ABSPATH . 'wp-content/plugins/gdpr-cookie-consent/public/css/gdpr-cookie-consent-public-custom.css';
			// custom css min file.
			$css_min_file_path = ABSPATH . 'wp-content/plugins/gdpr-cookie-consent/public/css/gdpr-cookie-consent-public-custom.min.css';

			$css_code_to_add = $the_options['gdpr_css_text'];

			// Allow us to easily interact with the filesystem.
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
			global $wp_filesystem;

			if ( ! $wp_filesystem->put_contents( $css_file_path, $css_code_to_add, FS_CHMOD_FILE ) ) {
				// Handle error.
			}

			// Writing the CSS code to the minified CSS file.
			if ( ! $wp_filesystem->put_contents( $css_min_file_path, $css_code_to_add, FS_CHMOD_FILE ) ) {
				// Handle error.
			}

			$encode_css                   = $this->encode_css( $the_options['gdpr_css_text'] );
			$the_options['gdpr_css_text'] = $encode_css;
			if(($the_options['is_iabtcf_on'] == false && $_POST['gcc-iabtcf-enable'] == "true") || ($_POST['gcc-iabtcf-enable'] == "false")){
				$the_options['notify_message']      = 
				isset( $_POST['notify_message_field'] ) ? wp_kses(
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
							'data-toggle' => array(),
							'data-target' => array(), 
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
				$the_options['about_message']                        = isset( $_POST['about_message_field'] ) ? sanitize_text_field( wp_unslash( $_POST['about_message_field'] ) ) : "Cookies are small text files that can be used by websites to make a user's experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.";
			}
			$the_options['notify_message_lgpd'] = isset( $_POST['notify_message_lgpd_field'] ) ? wp_kses(
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
			if(($the_options['is_iabtcf_on'] == false && $_POST['gcc-iabtcf-enable'] == "true") || ($the_options['is_iabtcf_on'] == true && $_POST['gcc-iabtcf-enable'] == "false")){
				$the_options = $this->changeLanguage($the_options);
			}
			$the_options['is_iabtcf_on']                       = isset( $_POST['gcc-iabtcf-enable'] ) && ( true === $_POST['gcc-iabtcf-enable'] || 'true' === $_POST['gcc-iabtcf-enable'] ) ? 'true' : 'false';
			$the_options['is_dynamic_lang_on']                   = isset( $_POST['gcc-dynamic-lang-enable'] ) && ( true === $_POST['gcc-dynamic-lang-enable'] || 'true' === $_POST['gcc-dynamic-lang-enable'] ) ? 'true' : 'false';
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
			$the_options['auto_generated_banner']               = isset( $_POST['gcc-auto-generated-banner'] ) && ( true === $_POST['gcc-auto-generated-banner'] || 'true' === $_POST['gcc-auto-generated-banner'] ) ? 'true' : 'false';
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
			$the_options['header_scripts']                        = isset( $_POST['gcc-header-scripts'] ) ? wp_unslash( $_POST['gcc-header-scripts'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$the_options['body_scripts']                          = isset( $_POST['gcc-body-scripts'] ) ? wp_unslash( $_POST['gcc-body-scripts'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$the_options['footer_scripts']                        = isset( $_POST['gcc-footer-scripts'] ) ? wp_unslash( $_POST['gcc-footer-scripts'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$the_options['button_decline_is_on']                  = isset( $_POST['gcc-cookie-decline-enable'] ) && ( true === $_POST['gcc-cookie-decline-enable'] || 'true' === $_POST['gcc-cookie-decline-enable'] ) ? 'true' : 'false';
			$the_options['button_decline_text']                   = isset( $_POST['button_decline_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_decline_text_field'] ) ) : 'Decline';
			$the_options['button_decline_as_button']              = isset( $_POST['gdpr-cookie-decline-as'] ) && ( true === $_POST['gdpr-cookie-decline-as'] || 'true' === $_POST['gdpr-cookie-decline-as'] ) ? 'true' : 'false';
			$the_options['button_decline_button_opacity']         = isset( $_POST['gdpr-cookie-decline-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-opacity'] ) ) : '1';
			$the_options['button_decline_button_border_radius']   = isset( $_POST['gdpr-cookie-decline-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-radius'] ) ) : '0';
			$the_options['button_decline_button_size']            = isset( $_POST['gdpr-cookie-decline-size'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-size'] ) ) : 'medium';
			$the_options['button_decline_action']                 = isset( $_POST['gdpr-cookie-decline-action'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-action'] ) ) : '#cookie_action_close_header_reject';
			$the_options['button_decline_url']                    = isset( $_POST['gdpr-cookie-decline-url'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-url'] ) ) : '#';
			$the_options['button_decline_new_win']                = isset( $_POST['gdpr-cookie-decline-url-new-window'] ) && ( true === $_POST['gdpr-cookie-decline-url-new-window'] || 'true' === $_POST['gdpr-cookie-decline-url-new-window'] ) ? 'true' : 'false';
			$the_options['button_decline_is_on1']                 = isset( $_POST['gcc-cookie-decline-enable1'] ) && ( true === $_POST['gcc-cookie-decline-enable1'] || 'true' === $_POST['gcc-cookie-decline-enable1'] ) ? 'true' : 'false';
			$the_options['button_decline_text1']                  = isset( $_POST['button_decline_text_field1'] ) ? sanitize_text_field( wp_unslash( $_POST['button_decline_text_field1'] ) ) : 'Decline';
			$the_options['button_decline_as_button1']             = isset( $_POST['gdpr-cookie-decline-as1'] ) && ( true === $_POST['gdpr-cookie-decline-as1'] || 'true' === $_POST['gdpr-cookie-decline-as1'] ) ? 'true' : 'false';
			$the_options['button_decline_button_opacity1']        = isset( $_POST['gdpr-cookie-decline-opacity1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-opacity1'] ) ) : '1';
			$the_options['button_decline_button_border_radius1']  = isset( $_POST['gdpr-cookie-decline-border-radius1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-radius1'] ) ) : '0';
			$the_options['button_decline_button_size1']           = isset( $_POST['gdpr-cookie-decline-size1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-size1'] ) ) : 'medium';
			$the_options['button_decline_action1']                = isset( $_POST['gdpr-cookie-decline-action1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-action1'] ) ) : '#cookie_action_close_header_reject';
			$the_options['button_decline_url1']                   = isset( $_POST['gdpr-cookie-decline-url1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-url1'] ) ) : '#';
			$the_options['button_decline_new_win1']               = isset( $_POST['gdpr-cookie-decline-url-new-window1'] ) && ( true === $_POST['gdpr-cookie-decline-url-new-window1'] || 'true' === $_POST['gdpr-cookie-decline-url-new-window1'] ) ? 'true' : 'false';
			$the_options['button_settings_is_on']                 = isset( $_POST['gcc-cookie-settings-enable'] ) && ( true === $_POST['gcc-cookie-settings-enable'] || 'true' === $_POST['gcc-cookie-settings-enable'] ) ? 'true' : 'false';
			$the_options['button_settings_as_popup']              = isset( $_POST['gdpr-cookie-settings-layout'] ) && ( true === $_POST['gdpr-cookie-settings-layout'] || 'true' === $_POST['gdpr-cookie-settings-layout'] ) ? 'true' : 'false';
			$the_options['button_settings_layout_skin']           = isset( $_POST['gdpr-cookie-layout-skin'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-layout-skin'] ) ) : 'layout-default';
			$the_options['button_settings_text']                  = isset( $_POST['button_settings_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_settings_text_field'] ) ) : 'Cookie Settings';
			$the_options['button_settings_as_button']             = isset( $_POST['gdpr-cookie-settings-as'] ) && ( true === $_POST['gdpr-cookie-settings-as'] || 'true' === $_POST['gdpr-cookie-settings-as'] ) ? 'true' : 'false';
			$the_options['button_settings_button_opacity']        = isset( $_POST['gdpr-cookie-settings-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-opacity'] ) ) : '1';
			$the_options['button_settings_button_border_radius']  = isset( $_POST['gdpr-cookie-settings-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-radius'] ) ) : '0';
			$the_options['button_settings_button_size']           = isset( $_POST['gdpr-cookie-settings-size'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-size'] ) ) : 'medium';
			$the_options['button_settings_display_cookies']       = isset( $_POST['gcc-cookie-on-frontend'] ) && ( true === $_POST['gcc-cookie-on-frontend'] || 'true' === $_POST['gcc-cookie-on-frontend'] ) ? 'true' : 'false';
			$the_options['button_settings_is_on1']                = isset( $_POST['gcc-cookie-settings-enable1'] ) && ( true === $_POST['gcc-cookie-settings-enable1'] || 'true' === $_POST['gcc-cookie-settings-enable1'] ) ? 'true' : 'false';
			$the_options['button_settings_as_popup1']             = isset( $_POST['gdpr-cookie-settings-layout1'] ) && ( true === $_POST['gdpr-cookie-settings-layout1'] || 'true' === $_POST['gdpr-cookie-settings-layout1'] ) ? 'true' : 'false';
			$the_options['button_settings_layout_skin1']          = isset( $_POST['gdpr-cookie-layout-skin1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-layout-skin1'] ) ) : 'layout-default';
			$the_options['button_settings_text1']                 = isset( $_POST['button_settings_text_field1'] ) ? sanitize_text_field( wp_unslash( $_POST['button_settings_text_field1'] ) ) : 'Cookie Settings';
			$the_options['button_settings_as_button1']            = isset( $_POST['gdpr-cookie-settings-as1'] ) && ( true === $_POST['gdpr-cookie-settings-as1'] || 'true' === $_POST['gdpr-cookie-settings-as1'] ) ? 'true' : 'false';
			$the_options['button_settings_button_opacity1']       = isset( $_POST['gdpr-cookie-settings-opacity1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-opacity1'] ) ) : '1';
			$the_options['button_settings_button_border_radius1'] = isset( $_POST['gdpr-cookie-settings-border-radius1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-radius1'] ) ) : '0';
			$the_options['button_settings_button_size1']          = isset( $_POST['gdpr-cookie-settings-size1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-size1'] ) ) : 'medium';
			$the_options['button_settings_display_cookies1']      = isset( $_POST['gcc-cookie-on-frontend1'] ) && ( true === $_POST['gcc-cookie-on-frontend1'] || 'true' === $_POST['gcc-cookie-on-frontend1'] ) ? 'true' : 'false';
			$the_options['button_confirm_text']                   = isset( $_POST['button_confirm_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_confirm_text_field'] ) ) : 'Confirm';
			$the_options['button_confirm_link_color']             = isset( $_POST['gdpr-cookie-confirm-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-text-color'] ) ) : '#ffffff';
			$the_options['button_confirm_button_color']           = isset( $_POST['gdpr-cookie-confirm-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-background-color'] ) ) : '#18a300';
			$the_options['button_confirm_button_opacity']         = isset( $_POST['gdpr-cookie-confirm-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-opacity'] ) ) : '1';
			$the_options['button_confirm_button_border_style']    = isset( $_POST['gdpr-cookie-confirm-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-style'] ) ) : 'none';
			$the_options['button_confirm_button_border_color']    = isset( $_POST['gdpr-cookie-confirm-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-color'] ) ) : '#18a300';
			$the_options['button_confirm_button_border_width']    = isset( $_POST['gdpr-cookie-confirm-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-width'] ) ) : '0';
			$the_options['button_confirm_button_border_radius']   = isset( $_POST['gdpr-cookie-confirm-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-radius'] ) ) : '0';
			$the_options['button_confirm_button_size']            = isset( $_POST['gdpr-cookie-confirm-size'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-size'] ) ) : 'medium';
			$the_options['button_cancel_text']                    = isset( $_POST['button_cancel_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_cancel_text_field'] ) ) : 'Cancel';
			$the_options['button_cancel_link_color']              = isset( $_POST['gdpr-cookie-cancel-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-text-color'] ) ) : '#ffffff';
			$the_options['button_cancel_button_color']            = isset( $_POST['gdpr-cookie-cancel-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-background-color'] ) ) : '#333333';
			$the_options['button_cancel_button_opacity']          = isset( $_POST['gdpr-cookie-cancel-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-opacity'] ) ) : '1';
			$the_options['button_cancel_button_border_style']     = isset( $_POST['gdpr-cookie-cancel-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-style'] ) ) : 'none';
			$the_options['button_cancel_button_border_color']     = isset( $_POST['gdpr-cookie-cancel-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-color'] ) ) : '#333333';
			$the_options['button_cancel_button_border_width']     = isset( $_POST['gdpr-cookie-cancel-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-width'] ) ) : '0';
			$the_options['button_cancel_button_border_radius']    = isset( $_POST['gdpr-cookie-cancel-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-radius'] ) ) : '0';
			$the_options['button_cancel_button_size']             = isset( $_POST['gdpr-cookie-cancel-size'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-size'] ) ) : 'medium';
			$the_options['button_donotsell_text']                 = isset( $_POST['button_donotsell_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_donotsell_text_field'] ) ) : 'Do Not Sell My Personal Information';
			$the_options['button_donotsell_link_color']           = isset( $_POST['gdpr-cookie-opt-out-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-opt-out-text-color'] ) ) : '#359bf5';
			$the_options['button_confirm_text1']                  = isset( $_POST['button_confirm_text_field1'] ) ? sanitize_text_field( wp_unslash( $_POST['button_confirm_text_field1'] ) ) : 'Confirm';
			$the_options['button_confirm_link_color1']            = isset( $_POST['gdpr-cookie-confirm-text-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-text-color1'] ) ) : '#ffffff';
			$the_options['button_confirm_button_color1']          = isset( $_POST['gdpr-cookie-confirm-background-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-background-color1'] ) ) : '#18a300';
			$the_options['button_confirm_button_opacity1']        = isset( $_POST['gdpr-cookie-confirm-opacity1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-opacity1'] ) ) : '1';
			$the_options['button_confirm_button_border_style1']   = isset( $_POST['gdpr-cookie-confirm-border-style1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-style1'] ) ) : 'none';
			$the_options['button_confirm_button_border_color1']   = isset( $_POST['gdpr-cookie-confirm-border-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-color1'] ) ) : '#18a300';
			$the_options['button_confirm_button_border_width1']   = isset( $_POST['gdpr-cookie-confirm-border-width1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-width1'] ) ) : '0';
			$the_options['button_confirm_button_border_radius1']  = isset( $_POST['gdpr-cookie-confirm-border-radius1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-radius1'] ) ) : '0';
			$the_options['button_confirm_button_size1']           = isset( $_POST['gdpr-cookie-confirm-size1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-size1'] ) ) : 'medium';
			$the_options['button_cancel_text1']                   = isset( $_POST['button_cancel_text_field1'] ) ? sanitize_text_field( wp_unslash( $_POST['button_cancel_text_field1'] ) ) : 'Cancel';
			$the_options['button_cancel_link_color1']             = isset( $_POST['gdpr-cookie-cancel-text-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-text-color1'] ) ) : '#ffffff';
			$the_options['button_cancel_button_color1']           = isset( $_POST['gdpr-cookie-cancel-background-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-background-color1'] ) ) : '#333333';
			$the_options['button_cancel_button_opacity1']         = isset( $_POST['gdpr-cookie-cancel-opacity1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-opacity1'] ) ) : '1';
			$the_options['button_cancel_button_border_style1']    = isset( $_POST['gdpr-cookie-cancel-border-style1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-style1'] ) ) : 'none';
			$the_options['button_cancel_button_border_color1']    = isset( $_POST['gdpr-cookie-cancel-border-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-color1'] ) ) : '#333333';
			$the_options['button_cancel_button_border_width1']    = isset( $_POST['gdpr-cookie-cancel-border-width1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-width1'] ) ) : '0';
			$the_options['button_cancel_button_border_radius1']   = isset( $_POST['gdpr-cookie-cancel-border-radius1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-radius1'] ) ) : '0';
			$the_options['button_cancel_button_size1']            = isset( $_POST['gdpr-cookie-cancel-size1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-size1'] ) ) : 'medium';
			$the_options['button_donotsell_text1']                = isset( $_POST['button_donotsell_text_field1'] ) ? sanitize_text_field( wp_unslash( $_POST['button_donotsell_text_field1'] ) ) : 'Do Not Sell My Personal Information';
			$the_options['button_donotsell_link_color1']          = isset( $_POST['gdpr-cookie-opt-out-text-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-opt-out-text-color1'] ) ) : '#359bf5';
			$the_options['button_accept_all_is_on']               = isset( $_POST['gcc-cookie-accept-all-enable'] ) && ( true === $_POST['gcc-cookie-accept-enable'] || 'true' === $_POST['gcc-cookie-accept-all-enable'] ) ? 'true' : 'false';
			$the_options['button_accept_all_text']                = isset( $_POST['button_accept_all_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_accept_all_text_field'] ) ) : 'Accept All';
			$the_options['button_accept_all_link_color']          = isset( $_POST['gdpr-cookie-accept-all-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-text-color'] ) ) : '#ffffff';
			$the_options['button_accept_all_as_button']           = isset( $_POST['gdpr-cookie-accept-all-as'] ) && ( true === $_POST['gdpr-cookie-accept-all-as'] || 'true' === $_POST['gdpr-cookie-accept-all-as'] ) ? 'true' : 'false';
			$the_options['button_accept_all_action']              = isset( $_POST['gdpr-cookie-accept-all-action'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-action'] ) ) : '#cookie_action_close_header';
			$the_options['button_accept_all_url']                 = isset( $_POST['gdpr-cookie-accept-all-url'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-url'] ) ) : '#';
			$the_options['button_accept_all_new_win']             = isset( $_POST['gdpr-cookie-accept-all-new-window'] ) && ( true === $_POST['gdpr-cookie-accept-all-new-window'] || 'true' === $_POST['gdpr-cookie-accept-all-new-window'] ) ? 'true' : 'false';
			$the_options['button_accept_all_button_color']        = isset( $_POST['gdpr-cookie-accept-all-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-background-color'] ) ) : '#18a300';
			$the_options['button_accept_all_button_size']         = isset( $_POST['gdpr-cookie-accept-all-size'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-size'] ) ) : 'medium';
			$the_options['button_accept_all_btn_border_style']    = isset( $_POST['gdpr-cookie-accept-all-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-style'] ) ) : 'none';
			$the_options['button_accept_all_btn_border_color']    = isset( $_POST['gdpr-cookie-accept-all-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-color'] ) ) : '#18a300';
			$the_options['button_accept_all_btn_opacity']         = isset( $_POST['gdpr-cookie-accept-all-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-opacity'] ) ) : '1';
			$the_options['button_accept_all_btn_border_width']    = isset( $_POST['gdpr-cookie-accept-all-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-width'] ) ) : '0';
			$the_options['button_accept_all_btn_border_radius']   = isset( $_POST['gdpr-cookie-accept-all-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-radius'] ) ) : '0';
			$the_options['button_accept_is_on1']              = isset( $_POST['gcc-cookie-accept-enable1'] ) && ( true === $_POST['gcc-cookie-accept-enable1'] || 'true' === $_POST['gcc-cookie-accept-enable1'] ) ? 'true' : 'false';
			$the_options['button_accept_all_is_on1']              = isset( $_POST['gcc-cookie-accept-all-enable1'] ) && ( true === $_POST['gcc-cookie-accept-all-enable1'] || 'true' === $_POST['gcc-cookie-accept-all-enable1'] ) ? 'true' : 'false';
			$the_options['button_accept_all_text1']               = isset( $_POST['button_accept_all_text_field1'] ) ? sanitize_text_field( wp_unslash( $_POST['button_accept_all_text_field1'] ) ) : 'Accept All';
			$the_options['button_accept_all_link_color1']         = isset( $_POST['gdpr-cookie-accept-all-text-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-text-color1'] ) ) : '#ffffff';
			$the_options['button_accept_all_as_button1']          = isset( $_POST['gdpr-cookie-accept-all-as1'] ) && ( true === $_POST['gdpr-cookie-accept-all-as1'] || 'true' === $_POST['gdpr-cookie-accept-all-as1'] ) ? 'true' : 'false';
			$the_options['button_accept_all_action1']             = isset( $_POST['gdpr-cookie-accept-all-action1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-action1'] ) ) : '#cookie_action_close_header';
			$the_options['button_accept_all_url1']                = isset( $_POST['gdpr-cookie-accept-all-url1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-url1'] ) ) : '#';
			$the_options['button_accept_all_new_win1']            = isset( $_POST['gdpr-cookie-accept-all-new-window1'] ) && ( true === $_POST['gdpr-cookie-accept-all-new-window1'] || 'true' === $_POST['gdpr-cookie-accept-all-new-window1'] ) ? 'true' : 'false';
			$the_options['button_accept_all_button_color1']       = isset( $_POST['gdpr-cookie-accept-all-background-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-background-color1'] ) ) : '#18a300';
			$the_options['button_accept_all_button_size1']        = isset( $_POST['gdpr-cookie-accept-all-size1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-size1'] ) ) : 'medium';
			$the_options['button_accept_all_btn_border_style1']   = isset( $_POST['gdpr-cookie-accept-all-border-style1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-style1'] ) ) : 'none';
			$the_options['button_accept_all_btn_border_color1']   = isset( $_POST['gdpr-cookie-accept-all-border-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-color1'] ) ) : '#18a300';
			$the_options['button_accept_all_btn_opacity1']        = isset( $_POST['gdpr-cookie-accept-all-opacity1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-opacity1'] ) ) : '1';
			$the_options['button_accept_all_btn_border_width1']   = isset( $_POST['gdpr-cookie-accept-all-border-width1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-width1'] ) ) : '0';
			$the_options['button_accept_all_btn_border_radius1']  = isset( $_POST['gdpr-cookie-accept-all-border-radius1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-radius1'] ) ) : '0';
			$the_options['cookie_bar2_name']                      = isset( $_POST['gdpr-cookie_bar2_name'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie_bar2_name'] ) ) : 'Test Banner A';
			$the_options['cookie_bar_color2']                     = isset( $_POST['gdpr-cookie-bar-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-color2'] ) ) : '#ffffff';
			$the_options['cookie_text_color2']                    = isset( $_POST['gdpr-cookie-text-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-text-color2'] ) ) : '#000000';
			$the_options['cookie_bar_opacity2']                   = isset( $_POST['gdpr-cookie-bar-opacity2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-opacity2'] ) ) : '0.80';
			$the_options['cookie_bar_border_width2']              = isset( $_POST['gdpr-cookie-bar-border-width2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-border-width2'] ) ) : '0';
			$the_options['border_style2']                         = isset( $_POST['gdpr-cookie-border-style2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-border-style2'] ) ) : 'none';
			$the_options['cookie_border_color2']                  = isset( $_POST['gdpr-cookie-border-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-border-color2'] ) ) : '#ffffff';
			$the_options['cookie_bar_border_radius2']             = isset( $_POST['gdpr-cookie-bar-border-radius2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-border-radius2'] ) ) : '0';
			$the_options['cookie_font2']                          = isset( $_POST['gdpr-cookie-font2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-font2'] ) ) : 'inherit';
			$the_options['button_accept_is_on2']                  = isset( $_POST['gcc-cookie-accept-enable2'] ) && ( true === $_POST['gcc-cookie-accept-enable2'] || 'true' === $_POST['gcc-cookie-accept-enable2'] ) ? 'true' : 'false';
			$the_options['button_accept_text2']                   = isset( $_POST['button_accept_text_field2'] ) ? sanitize_text_field( wp_unslash( $_POST['button_accept_text_field2'] ) ) : 'Accept';
			$the_options['button_accept_button_size2']            = isset( $_POST['gdpr-cookie-accept-size2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-size2'] ) ) : 'medium';
			$the_options['button_accept_action2']                 = isset( $_POST['gdpr-cookie-accept-action2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-action2'] ) ) : '#cookie_action_close_header';
			$the_options['button_accept_url2']                    = isset( $_POST['gdpr-cookie-accept-url2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-url2'] ) ) : '#';
			$the_options['button_accept_as_button2']              = isset( $_POST['gdpr-cookie-accept-as2'] ) && ( true === $_POST['gdpr-cookie-accept-as2'] || 'true' === $_POST['gdpr-cookie-accept-as2'] ) ? 'true' : 'false';
			$the_options['button_accept_new_win2']                = isset( $_POST['gdpr-cookie-url-new-window2'] ) && ( true === $_POST['gdpr-cookie-url-new-window2'] || 'true' === $_POST['gdpr-cookie-url-new-window2'] ) ? 'true' : 'false';
			$the_options['button_accept_button_opacity2']         = isset( $_POST['gdpr-cookie-accept-opacity2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-opacity2'] ) ) : '1';
			$the_options['button_accept_button_border_style2']    = isset( $_POST['gdpr-cookie-accept-border-style2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-style2'] ) ) : 'none';
			$the_options['button_accept_button_border_width2']    = isset( $_POST['gdpr-cookie-accept-border-width2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-width2'] ) ) : '0';
			$the_options['button_accept_button_border_radius2']   = isset( $_POST['gdpr-cookie-accept-border-radius2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-radius2'] ) ) : '0';
			$the_options['button_accept_link_color2']             = isset( $_POST['gdpr-cookie-accept-text-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-text-color2'] ) ) : '#ffffff';
			$the_options['button_decline_is_on2']                 = isset( $_POST['gcc-cookie-decline-enable2'] ) && ( true === $_POST['gcc-cookie-decline-enable2'] || 'true' === $_POST['gcc-cookie-decline-enable2'] ) ? 'true' : 'false';
			$the_options['button_decline_text2']                  = isset( $_POST['button_decline_text_field2'] ) ? sanitize_text_field( wp_unslash( $_POST['button_decline_text_field2'] ) ) : 'Decline';
			$the_options['button_decline_as_button2']             = isset( $_POST['gdpr-cookie-decline-as2'] ) && ( true === $_POST['gdpr-cookie-decline-as2'] || 'true' === $_POST['gdpr-cookie-decline-as2'] ) ? 'true' : 'false';
			$the_options['button_decline_button_opacity2']        = isset( $_POST['gdpr-cookie-decline-opacity2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-opacity2'] ) ) : '1';
			$the_options['button_decline_button_border_radius2']  = isset( $_POST['gdpr-cookie-decline-border-radius2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-radius2'] ) ) : '0';
			$the_options['button_decline_button_size2']           = isset( $_POST['gdpr-cookie-decline-size2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-size2'] ) ) : 'medium';
			$the_options['button_decline_action2']                = isset( $_POST['gdpr-cookie-decline-action2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-action2'] ) ) : '#cookie_action_close_header_reject';
			$the_options['button_decline_url2']                   = isset( $_POST['gdpr-cookie-decline-url2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-url2'] ) ) : '#';
			$the_options['button_decline_new_win2']               = isset( $_POST['gdpr-cookie-decline-url-new-window2'] ) && ( true === $_POST['gdpr-cookie-decline-url-new-window2'] || 'true' === $_POST['gdpr-cookie-decline-url-new-window2'] ) ? 'true' : 'false';
			$the_options['button_settings_is_on2']                = isset( $_POST['gcc-cookie-settings-enable2'] ) && ( true === $_POST['gcc-cookie-settings-enable2'] || 'true' === $_POST['gcc-cookie-settings-enable2'] ) ? 'true' : 'false';
			$the_options['button_settings_as_popup2']             = isset( $_POST['gdpr-cookie-settings-layout2'] ) && ( true === $_POST['gdpr-cookie-settings-layout2'] || 'true' === $_POST['gdpr-cookie-settings-layout2'] ) ? 'true' : 'false';
			$the_options['button_settings_layout_skin2']          = isset( $_POST['gdpr-cookie-layout-skin2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-layout-skin2'] ) ) : 'layout-default';
			$the_options['button_settings_text2']                 = isset( $_POST['button_settings_text_field2'] ) ? sanitize_text_field( wp_unslash( $_POST['button_settings_text_field2'] ) ) : 'Cookie Settings';
			$the_options['button_settings_as_button2']            = isset( $_POST['gdpr-cookie-settings-as2'] ) && ( true === $_POST['gdpr-cookie-settings-as2'] || 'true' === $_POST['gdpr-cookie-settings-as2'] ) ? 'true' : 'false';
			$the_options['button_settings_button_opacity2']       = isset( $_POST['gdpr-cookie-settings-opacity2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-opacity2'] ) ) : '1';
			$the_options['button_settings_button_border_radius2'] = isset( $_POST['gdpr-cookie-settings-border-radius2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-radius2'] ) ) : '0';
			$the_options['button_settings_button_size2']          = isset( $_POST['gdpr-cookie-settings-size2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-size2'] ) ) : 'medium';
			$the_options['button_settings_display_cookies2']      = isset( $_POST['gcc-cookie-on-frontend2'] ) && ( true === $_POST['gcc-cookie-on-frontend2'] || 'true' === $_POST['gcc-cookie-on-frontend2'] ) ? 'true' : 'false';
			$the_options['button_confirm_text2']                  = isset( $_POST['button_confirm_text_field2'] ) ? sanitize_text_field( wp_unslash( $_POST['button_confirm_text_field2'] ) ) : 'Confirm';
			$the_options['button_confirm_link_color2']            = isset( $_POST['gdpr-cookie-confirm-text-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-text-color2'] ) ) : '#ffffff';
			$the_options['button_confirm_button_color2']          = isset( $_POST['gdpr-cookie-confirm-background-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-background-color2'] ) ) : '#18a300';
			$the_options['button_confirm_button_opacity2']        = isset( $_POST['gdpr-cookie-confirm-opacity2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-opacity2'] ) ) : '1';
			$the_options['button_confirm_button_border_style2']   = isset( $_POST['gdpr-cookie-confirm-border-style2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-style2'] ) ) : 'none';
			$the_options['button_confirm_button_border_color2']   = isset( $_POST['gdpr-cookie-confirm-border-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-color2'] ) ) : '#18a300';
			$the_options['button_confirm_button_border_width2']   = isset( $_POST['gdpr-cookie-confirm-border-width2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-width2'] ) ) : '0';
			$the_options['button_confirm_button_border_radius2']  = isset( $_POST['gdpr-cookie-confirm-border-radius2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-radius2'] ) ) : '0';
			$the_options['button_confirm_button_size2']           = isset( $_POST['gdpr-cookie-confirm-size2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-size2'] ) ) : 'medium';
			$the_options['button_cancel_text2']                   = isset( $_POST['button_cancel_text_field2'] ) ? sanitize_text_field( wp_unslash( $_POST['button_cancel_text_field2'] ) ) : 'Cancel';
			$the_options['button_cancel_link_color2']             = isset( $_POST['gdpr-cookie-cancel-text-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-text-color2'] ) ) : '#ffffff';
			$the_options['button_cancel_button_color2']           = isset( $_POST['gdpr-cookie-cancel-background-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-background-color2'] ) ) : '#333333';
			$the_options['button_cancel_button_opacity2']         = isset( $_POST['gdpr-cookie-cancel-opacity2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-opacity2'] ) ) : '1';
			$the_options['button_cancel_button_border_style2']    = isset( $_POST['gdpr-cookie-cancel-border-style2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-style2'] ) ) : 'none';
			$the_options['button_cancel_button_border_color2']    = isset( $_POST['gdpr-cookie-cancel-border-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-color2'] ) ) : '#333333';
			$the_options['button_cancel_button_border_width2']    = isset( $_POST['gdpr-cookie-cancel-border-width2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-width2'] ) ) : '0';
			$the_options['button_cancel_button_border_radius2']   = isset( $_POST['gdpr-cookie-cancel-border-radius2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-radius2'] ) ) : '0';
			$the_options['button_cancel_button_size2']            = isset( $_POST['gdpr-cookie-cancel-size2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-size2'] ) ) : 'medium';
			$the_options['button_donotsell_text2']                = isset( $_POST['button_donotsell_text_field2'] ) ? sanitize_text_field( wp_unslash( $_POST['button_donotsell_text_field2'] ) ) : 'Do Not Sell My Personal Information';
			$the_options['button_donotsell_link_color2']          = isset( $_POST['gdpr-cookie-opt-out-text-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-opt-out-text-color2'] ) ) : '#359bf5';
			$the_options['button_accept_all_is_on2']              = isset( $_POST['gcc-cookie-accept-all-enable2'] ) && ( true === $_POST['gcc-cookie-accept-enable2'] || 'true' === $_POST['gcc-cookie-accept-all-enable2'] ) ? 'true' : 'false';
			$the_options['button_accept_all_text2']               = isset( $_POST['button_accept_all_text_field2'] ) ? sanitize_text_field( wp_unslash( $_POST['button_accept_all_text_field2'] ) ) : 'Accept All';
			$the_options['button_accept_all_link_color2']         = isset( $_POST['gdpr-cookie-accept-all-text-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-text-color2'] ) ) : '#ffffff';
			$the_options['button_accept_all_as_button2']          = isset( $_POST['gdpr-cookie-accept-all-as2'] ) && ( true === $_POST['gdpr-cookie-accept-all-as2'] || 'true' === $_POST['gdpr-cookie-accept-all-as2'] ) ? 'true' : 'false';
			$the_options['button_accept_all_action2']             = isset( $_POST['gdpr-cookie-accept-all-action2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-action2'] ) ) : '#cookie_action_close_header';
			$the_options['button_accept_all_url2']                = isset( $_POST['gdpr-cookie-accept-all-url2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-url2'] ) ) : '#';
			$the_options['button_accept_all_new_win2']            = isset( $_POST['gdpr-cookie-accept-all-new-window2'] ) && ( true === $_POST['gdpr-cookie-accept-all-new-window2'] || 'true' === $_POST['gdpr-cookie-accept-all-new-window2'] ) ? 'true' : 'false';
			$the_options['button_accept_all_button_color2']       = isset( $_POST['gdpr-cookie-accept-all-background-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-background-color2'] ) ) : '#18a300';
			$the_options['button_accept_all_button_size2']        = isset( $_POST['gdpr-cookie-accept-all-size2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-size2'] ) ) : 'medium';
			$the_options['button_accept_all_btn_border_style2']   = isset( $_POST['gdpr-cookie-accept-all-border-style2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-style2'] ) ) : 'none';
			$the_options['button_accept_all_btn_border_color2']   = isset( $_POST['gdpr-cookie-accept-all-border-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-color2'] ) ) : '#18a300';
			$the_options['button_accept_all_btn_opacity2']        = isset( $_POST['gdpr-cookie-accept-all-opacity2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-opacity2'] ) ) : '1';
			$the_options['button_accept_all_btn_border_width2']   = isset( $_POST['gdpr-cookie-accept-all-border-width2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-width2'] ) ) : '0';
			$the_options['button_accept_all_btn_border_radius2']  = isset( $_POST['gdpr-cookie-accept-all-border-radius2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-radius2'] ) ) : '0';
			// data reqs fields.
			$the_options['data_req_email_address'] = isset( $_POST['data_req_email_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['data_req_email_text_field'] ) ) : '';
			$the_options['data_req_subject']       = isset( $_POST['data_req_subject_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['data_req_subject_text_field'] ) ) : 'We have received your request';
			// revoke consent text color.
			$the_options['button_revoke_consent_text_color']       = isset( $_POST['gcc-revoke-consent-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-text-color'] ) ) : '';
			$the_options['button_revoke_consent_background_color'] = isset( $_POST['gcc-revoke-consent-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-background-color'] ) ) : '';
			if ( ! $the_options['data_req_subject'] ) {
				$the_options['data_req_subject'] = 'We have received your request';
			}

			$the_options['data_req_editor_message'] = isset( $_POST['data_req_mail_content_text_field'] ) ? htmlentities( $_POST['data_req_mail_content_text_field'] ) : '';

			if ( $the_options['data_req_editor_message'] == '' ) {
				$the_options['data_req_editor_message'] = '&lt;p&gt;Hi {name}&lt;/p&gt;&lt;p&gt;We have received your request on {blogname}. Depending on the specific request and legal obligations we might follow-up.&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;Kind regards,&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;{blogname}&lt;/p&gt;';
			}
			// revoke consent text color.
			$the_options['button_revoke_consent_text_color']       = isset( $_POST['gcc-revoke-consent-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-text-color'] ) ) : '';
			$the_options['button_revoke_consent_background_color'] = isset( $_POST['gcc-revoke-consent-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-background-color'] ) ) : '';

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
				$selected_countries             = array();
				$selected_countries             = isset( $_POST['gcc-selected-countries'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-selected-countries'] ) ) ) : '';
				// storing id of pages in database.
				$the_options['select_countries'] = $selected_countries;
				// For EU.
				if ( isset( $_POST['gcc-eu-enable'] ) ) {
					if ( 'no' === $_POST['gcc-eu-enable'] ) {
						$the_options['is_eu_on'] = 'false';
					} elseif ( 'false' == $_POST['gcc-eu-enable'] ) {
						$the_options['is_eu_on'] = 'false';
					} else {
						
						if(!$the_options['is_eu_on']){
							$this->auto_update_maxminddb();
							$this->download_maxminddb();
						}
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
						if(!$the_options['is_ccpa_on'] ){
							$this->auto_update_maxminddb();
							$this->download_maxminddb();
						}
						$the_options['is_ccpa_on'] = 'true';
					}
				}
				// for World wide.
				if ( isset( $_POST['gcc-worldwide-enable'] ) ) {
					if ( 'no' === $_POST['gcc-worldwide-enable'] ) {
						$the_options['is_worldwide_on'] = 'false';
					} elseif ( 'false' == $_POST['gcc-worldwide-enable'] ) {
						$the_options['is_worldwide_on'] = 'false';
					} else {
						if(!$the_options['is_worldwide_on']){
							$this->disable_auto_update_maxminddb();
						}
						$the_options['is_worldwide_on'] = 'true';
					}
				}
				// For select country dropdown.
				if ( isset( $_POST['gcc-select-countries-enable'] ) ) {
					if ( 'no' === $_POST['gcc-select-countries-enable'] ) {
						$the_options['is_selectedCountry_on'] = 'false';
					} elseif ( 'false' == $_POST['gcc-select-countries-enable'] ) {
						$the_options['is_selectedCountry_on'] = 'false';
					} else {
						if(!$the_options['is_selectedCountry_on']){
							$this->auto_update_maxminddb();
							$this->download_maxminddb();
						}
						$the_options['is_selectedCountry_on'] = 'true';
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
				$the_options = $this->changeLanguage($the_options);				
			}
	
			if (isset($_POST['gcc-ab-testing-enable']) 
				&& ($_POST['gcc-ab-testing-enable'] === 'false' || $_POST['gcc-ab-testing-enable'] === false) 
				&& isset($ab_options['ab_testing_enabled']) 
				&& ($ab_options['ab_testing_enabled'] === 'true' || $ab_options['ab_testing_enabled'] === true)) {
				$ab_options['ab_testing_period'] = '30';
				delete_transient( 'gdpr_ab_testing_transient' );
				$the_options = $this->wpl_set_default_ab_testing_banner( $the_options, $the_options['default_cookie_bar'] === true || $the_options['default_cookie_bar'] === 'true' ? '1' : '2' );
			}
			
			$ab_options['ab_testing_enabled'] = isset( $_POST['gcc-ab-testing-enable'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-ab-testing-enable'] ) ) : 'false';
			update_option( 'wpl_ab_options', $ab_options );
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
	 * Ajax callback for A-B Testing value.
	 */
	public function gdpr_cookie_consent_ab_testing_enable(){
			$the_options    = Gdpr_Cookie_Consent::gdpr_get_settings();
			$ab_options     = get_option( 'wpl_ab_options' );
			if ( ! $ab_options ) {
				$ab_options = array();
			}
			if (isset($_POST['gcc-ab-testing-enable']) 
			&& ($_POST['gcc-ab-testing-enable'] === true || $_POST['gcc-ab-testing-enable'] === 'true') 
			&& (!isset($ab_options['ab_testing_enabled']) 
				|| $ab_options['ab_testing_enabled'] === 'false' 
				|| $ab_options['ab_testing_enabled'] === false)) {
				$ab_options ['necessary1']  = 0;
				$ab_options ['marketing1']  = 0;
				$ab_options ['analytics1']  = 0;
				$ab_options ['DNT1']        = 0;
				$ab_options ['noChoice1']   = 0;
				$ab_options ['noWarning1']  = 0;
				$ab_options ['necessary2']  = 0;
				$ab_options ['marketing2']  = 0;
				$ab_options ['analytics2']  = 0;
				$ab_options ['DNT2']        = 0;
				$ab_options ['noChoice2']   = 0;
				$ab_options ['noWarning2']  = 0;
				$ab_options['ab_testing_period'] = 30;
				$ab_transient_creation_time = time();
				set_transient(
					'gdpr_ab_testing_transient',
					array(
						'value'         => 'A/B Testing Period',
						'creation_time' => $ab_transient_creation_time,
					),
					(int) $ab_options['ab_testing_period'] * 24 * 60 * 60
				);
				$the_options['default_cookie_bar']                 = isset( $_POST['gdpr-default_cookie_bar'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-default_cookie_bar'] ) ) : true;
				update_option('wpl_ab_options',$ab_options);
				update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
			}
			if (isset($_POST['gcc-ab-testing-enable']) 
				&& ($_POST['gcc-ab-testing-enable'] === 'false' || $_POST['gcc-ab-testing-enable'] === false) 
				&& isset($ab_options['ab_testing_enabled']) 
				&& ($ab_options['ab_testing_enabled'] === 'true' || $ab_options['ab_testing_enabled'] === true)) {
				$ab_options['ab_testing_period'] = '30';
				delete_transient( 'gdpr_ab_testing_transient' );
				$the_options = $this->wpl_set_default_ab_testing_banner( $the_options, $the_options['default_cookie_bar'] === true || $the_options['default_cookie_bar'] === 'true' ? '1' : '2' );
				update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
			}
			// $ab_options['ab_testing_period'] = isset( $_POST['ab_testing_period_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['ab_testing_period_text_field'] ) ) : '';
			$ab_options['ab_testing_enabled'] = isset( $_POST['gcc-ab-testing-enable'] ) ? ($_POST['gcc-ab-testing-enable'] === true || $_POST['gcc-ab-testing-enable']==='true' || $_POST['gcc-ab-testing-enable'] === 1 ? 'true' :'false')  : 'false';
			update_option( 'wpl_ab_options', $ab_options );
			wp_send_json_success( array( 'form_options_saved' => true ) );
	}
	/**
	 * Function to change the language
	 */
	public function changeLanguage($the_options){
		$translations_file = get_site_url() . '/wp-content/plugins/gdpr-cookie-consent/admin/translations/translations.json';
				$translations      = wp_remote_get( $translations_file );

				// Log the entire response
				if ( is_wp_error( $translations ) ) {
				} else {
					$body = wp_remote_retrieve_body( $translations );

					$translations = json_decode( $body, true );

					if ( $translations === null ) {
					}
				}
				// Define an array of text keys to translate.
				$text_keys_to_translate = array(
					'dash_notify_message_eprivacy',
					'dash_notify_message_lgpd',
					'dash_button_readmore_text',
					'dash_button_accept_text',
					'dash_button_accept_all_text',
					'dash_button_decline_text',
					'dash_about_message',
					'dash_about_message_iabtcf',
					'dash_about_message_lgpd',
					'dash_notify_message',
					'dash_notify_message_iabtcf',
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
					if($text_key === 'dash_button_accept_text' || $text_key === 'dash_button_accept_all_text' || $text_key === 'dash_button_decline_text' || $text_key === 'dash_button_settings_text' || $text_key === 'dash_button_donotsell_text' || $text_key === 'dash_button_confirm_text' || $text_key === 'dash_button_cancel_text'){
						$the_options[ $stripped_string ] = $translated_text;
						$the_options[ $stripped_string . '1' ] = $translated_text;
						$the_options[ $stripped_string . '2' ] = $translated_text;
					}
					else if(($text_key === 'dash_about_message_iabtcf' || $text_key === 'dash_notify_message_iabtcf') && ($_POST['gcc-iabtcf-enable'] === true || $_POST['gcc-iabtcf-enable'] === "true" || $_POST['gcc-iabtcf-enable'] === 1)){
						$stripped_string                 = str_replace( '_iabtcf', '', $stripped_string );
						$the_options[ $stripped_string ] = $translated_text;
			
						
					}
					else if(($text_key === 'dash_about_message' || $text_key === 'dash_notify_message') && (!$_POST['gcc-iabtcf-enable'] || $_POST['gcc-iabtcf-enable'] === false || $_POST['gcc-iabtcf-enable'] === "false" || $_POST['gcc-iabtcf-enable'] === 0)){

						$the_options[ $stripped_string ] = $translated_text;
						
					}
					else if($text_key !== 'dash_about_message_iabtcf' && $text_key !== 'dash_notify_message_iabtcf' && $text_key !== 'dash_about_message' && $text_key !== 'dash_notify_message'){
					
						$the_options[ $stripped_string ] = $translated_text;
					}

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
							// First, ensure the table has the correct character set and collation (utf8mb4).
							
							$table_name = $wpdb->prefix . 'gdpr_cookie_scan_categories';
							
							// Check if the table exists and get the current collation.
							$charset_check = $wpdb->get_row( "SHOW TABLE STATUS LIKE '$table_name'" );
							
							// If the table exists and the collation is not utf8mb4_unicode_ci, alter the table.
							if ( isset( $charset_check->Collation ) && $charset_check->Collation !== 'utf8mb4_unicode_ci' ) {
								$alter_table_sql = "ALTER TABLE `$table_name`
													CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
								$wpdb->query( $alter_table_sql );
							}
							$sanitized_category_descriptions = addslashes( $translated_category_descriptions[ $category_i_d ] );
							$sanitized_category_names = addslashes( $translated_category_names[ $category_i_d ] );
							
							// Update the table with the translated values.
							$wpdb->query(
								$wpdb->prepare(
									'UPDATE `' . $wpdb->prefix . 'gdpr_cookie_scan_categories`
									SET `gdpr_cookie_category_description` = %s,
										`gdpr_cookie_category_name` = %s
									WHERE `id_gdpr_cookie_category` = %d',
									$sanitized_category_descriptions,
									$sanitized_category_names,
									$category_i_d
								)
							);
						}
					}
				}
		return $the_options;
	}

	/**
	 * Function to set transient for auto-update
	 */
	public function auto_update_maxminddb(){
		
		if ( ! wp_next_scheduled( 'update_maxmind_db_event' ) ) {
			//This product includes GeoLite2 data created by MaxMind, available from https://www.maxmind.com. The data is licensed under the Creative Commons Attribution-ShareAlike 4.0 International License.
			wp_schedule_event( time(), 'weekly', 'update_maxmind_db_event' );
		}
	}

	/**
	 * Disable auto update 
	 */
	function disable_auto_update_maxminddb() {
		
		$timestamp = wp_next_scheduled( 'update_maxmind_db_event' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'update_maxmind_db_event' );
		}
	}
	/** 
	 * Function to download the maxmind database
	 */
	public function download_maxminddb(){
		$uploads_dir   = wp_upload_dir();
		//This product includes GeoLite2 data created by MaxMind, available from https://www.maxmind.com. The data is licensed under the Creative Commons Attribution-ShareAlike 4.0 International License.
		$database_path = trailingslashit( $uploads_dir['basedir'] ) . 'gdpr_uploads/GeoLite2-City.mmdb';
		if (file_exists($database_path)) {
			// Get the file's last modified time
			$last_modified_time = filemtime($database_path);

			// Calculate the time 7 days ago
			$seven_days_ago = strtotime('-7 days');

			// Check if the file was modified within the last 7 days
			if ($last_modified_time <= $seven_days_ago) {
				try {
					$response = wp_remote_post(
						GDPR_API_URL . 'get_maxmind_db',
							array(
								'body' => array(
									'action' => 'download_maxmind_db'
								),
								'timeout' => 20
							)
					);

					if (is_wp_error($response)) {
						error_log('Error in response: ' . $response->get_error_message());
					} else {
						$status_code = wp_remote_retrieve_response_code($response);
						if (200 === $status_code) {
							$file_data = wp_remote_retrieve_body($response);
							if(file_exists($database_path)) wp_delete_file($database_path);
							file_put_contents($database_path, $file_data);
						}
					}
				} catch (Exception $e) {
					error_log('Error: ' . $e->getMessage());
				}
			} else {
				
			}
		} else {
			try {
					$response = wp_remote_post(
						GDPR_API_URL . 'get_maxmind_db',
							array(
								'body' => array(
									'action' => 'download_maxmind_db'
								),
								'timeout' => 20
							)
					);

					if (is_wp_error($response)) {
						error_log('Error in response: ' . $response->get_error_message());
					} else {
						$status_code = wp_remote_retrieve_response_code($response);
						if (200 === $status_code) {
							$file_data = wp_remote_retrieve_body($response);
							if(file_exists($database_path)) wp_delete_file($database_path);
							file_put_contents($database_path, $file_data);
						}
					}
				} catch (Exception $e) {
					error_log('Error: ' . $e->getMessage());
				}
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
				'documentation_url' => 'https://wplegalpages.com/docs/wp-cookie-consent/',
				'faq_url'           => 'https://wplegalpages.com/docs/wp-cookie-consent/faqs/faq-2/',
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
				'label' => 'Abkhazian',
				'code'  => 'ab',
			),
			array(
				'label' => 'Afar',
				'code'  => 'aa',
			),
			array(
				'label' => 'Afrikaans',
				'code'  => 'af',
			),
			array(
				'label' => 'Albanian',
				'code'  => 'sq',
			),
			array(
				'label' => 'Amharic',
				'code'  => 'am',
			),
			array(
				'label' => 'Arabic',
				'code'  => 'ar',
			),
			array(
				'label' => 'Armenian',
				'code'  => 'hy',
			),
			array(
				'label' => 'Azerbaijani',
				'code'  => 'az',
			),
			array(
				'label' => 'Basque',
				'code'  => 'eu',
			),
			array(
				'label' => 'Belarusian',
				'code'  => 'be',
			),
			array(
				'label' => 'Bengali',
				'code'  => 'bn',
			),
			array(
				'label' => 'Bosnian',
				'code'  => 'bs',
			),
			array(
				'label' => 'Bulgarian',
				'code'  => 'bg',
			),
			array(
				'label' => 'Catalan',
				'code'  => 'ca',
			),
			array(
				'label' => 'Corsican',
				'code'  => 'co',
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
				'label' => 'Esperanto',
				'code'  => 'eo',
			),
			array(
				'label' => 'Finnish',
				'code'  => 'fi',
			),
			array(
				'label' => 'French',
				'code'  => 'fr',
			),
			array(
				'label' => 'Frisian',
				'code'  => 'fy',
			),
			array(
				'label' => 'Galician',
				'code'  => 'gl',
			),
			array(
				'label' => 'Georgian',
				'code'  => 'ka',
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
				'label' => 'Gujarati',
				'code'  => 'gu',
			),
			array(
				'label' => 'Hausa',
				'code'  => 'ha',
			),
			array(
				'label' => 'Hebrew',
				'code'  => 'he',
			),
			array(
				'label' => 'Hindi',
				'code'  => 'hi',
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
				'label' => 'Igbo',
				'code'  => 'ig',
			),
			array(
				'label' => 'Indonesian',
				'code'  => 'id',
			),
			array(
				'label' => 'Irish',
				'code'  => 'ga',
			),
			array(
				'label' => 'Italian',
				'code'  => 'it',
			),
			array(
				'label' => 'Japanese',
				'code'  => 'ja',
			),
			array(
				'label' => 'Kannada',
				'code'  => 'kn',
			),
			array(
				'label' => 'Kazakh',
				'code'  => 'kk',
			),
			array(
				'label' => 'Kirghiz',
				'code'  => 'ky',
			),
			array(
				'label' => 'Korean',
				'code'  => 'ko',
			),
			array(
				'label' => 'Kurdish',
				'code'  => 'ku',
			),
			array(
				'label' => 'Laothian',
				'code'  => 'lo',
			),
			array(
				'label' => 'Latvian',
				'code'  => 'lv',
			),
			array(
				'label' => 'Luxembourgish',
				'code'  => 'lb',
			),
			array(
				'label' => 'Macedonian',
				'code'  => 'mk',
			),
			array(
				'label' => 'Malagasy',
				'code'  => 'mg',
			),
			array(
				'label' => 'Malay',
				'code'  => 'ms',
			),
			array(
				'label' => 'Malayalam',
				'code'  => 'ml',
			),
			array(
				'label' => 'Maltese',
				'code'  => 'mt',
			),
			array(
				'label' => 'Maori',
				'code'  => 'mi',
			),
			array(
				'label' => 'Marathi',
				'code'  => 'mr',
			),
			array(
				'label' => 'Mongolian',
				'code'  => 'mn',
			),
			array(
				'label' => 'Nepali',
				'code'  => 'ne',
			),
			array(
				'label' => 'Norwegian',
				'code'  => 'no',
			),
			array(
				'label' => 'Oriya',
				'code'  => 'or',
			),
			array(
				'label' => 'Pashto',
				'code'  => 'ps',
			),
			array(
				'label' => 'Persian',
				'code'  => 'fa',
			),
			array(
				'label' => 'Polish',
				'code'  => 'po',
			),
			array(
				'label' => 'Portuguese',
				'code'  => 'pt',
			),
			array(
				'label' => 'Punjabi',
				'code'  => 'pa',
			),
			array(
				'label' => 'Romanian',
				'code'  => 'ro',
			),
			array(
				'label' => 'Russian',
				'code'  => 'ru',
			),
			array(
				'label' => 'Samoan',
				'code'  => 'sm',
			),
			array(
				'label' => 'Scots Gaelic',
				'code'  => 'gd',
			),
			array(
				'label' => 'Sesotho',
				'code'  => 'st',
			),
			array(
				'label' => 'Shona',
				'code'  => 'sn',
			),
			array(
				'label' => 'Sindhi',
				'code'  => 'sd',
			),
			array(
				'label' => 'Singhalese',
				'code'  => 'si',
			),
			array(
				'label' => 'Slovak',
				'code'  => 'sk',
			),
			array(
				'label' => 'Slovenian',
				'code'  => 'sl',
			),
			array(
				'label' => 'Somali',
				'code'  => 'so',
			),
			array(
				'label' => 'Spanish',
				'code'  => 'es',
			),
			array(
				'label' => 'Sudanese',
				'code'  => 'su',
			),
			array(
				'label' => 'Swahili',
				'code'  => 'sw',
			),
			array(
				'label' => 'Swedish',
				'code'  => 'sv',
			),
			array(
				'label' => 'Tagalog',
				'code'  => 'tl',
			),
			array(
				'label' => 'Tajik',
				'code'  => 'tg',
			),
			array(
				'label' => 'Tamil',
				'code'  => 'ta',
			),
			array(
				'label' => 'Telugu',
				'code'  => 'te',
			),
			array(
				'label' => 'Thai',
				'code'  => 'th',
			),
			array(
				'label' => 'Turkish',
				'code'  => 'tr',
			),
			array(
				'label' => 'Ukrainian',
				'code'  => 'uk',
			),
			array(
				'label' => 'Urdu',
				'code'  => 'ur',
			),
			array(
				'label' => 'Uzbek',
				'code'  => 'uz',
			),
			array(
				'label' => 'Vietnamese',
				'code'  => 'vi',
			),
			array(
				'label' => 'Welsh',
				'code'  => 'cy',
			),
			array(
				'label' => 'Xhosa',
				'code'  => 'xh',
			),
			array(
				'label' => 'Yiddish',
				'code'  => 'yi',
			),
			array(
				'label' => 'Yoruba',
				'code'  => 'yo',
			),
			array(
				'label' => 'Zulu',
				'code'  => 'zu',
			),
			array(
				'label' => 'Cebuano',
				'code'  => 'ceb',
			),
			array(
				'label' => 'Chinese (Simplified)',
				'code'  => 'zh-cn',
			),
			array(
				'label' => 'Chinese (Traditional)',
				'code'  => 'zh-tw',
			),
			array(
				'label' => 'Estonian',
				'code'  => 'et',
			),
			array(
				'label' => 'Haitian Creole',
				'code'  => 'ht',
			),
			array(
				'label' => 'Hawaiian',
				'code'  => 'haw',
			),
			array(
				'label' => 'Hmong',
				'code'  => 'hmn',
			),
			array(
				'label' => 'Javanese',
				'code'  => 'jw',
			),
			array(
				'label' => 'Khmer',
				'code'  => 'km',
			),
			array(
				'label' => 'Latin',
				'code'  => 'la',
			),
			array(
				'label' => 'Lithuanian',
				'code'  => 'lt',
			),
			array(
				'label' => 'Myanmar (Burmese)',
				'code'  => 'my',
			),
			array(
				'label' => 'Serbian',
				'code'  => 'sr',
			),
			array(
				'label' => 'Uyghur',
				'code'  => 'ug',
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
	public function wp_legal_pages_install_activate_screen() { 
		$legalpages_install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=wplegalpages' ), 'install-plugin_wplegalpages' );
		$plugin_name                   = 'wplegalpages/wplegalpages.php';
		$legalpages_activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin_name . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin_name );
		$installed_plugins = get_plugins();
		$is_legalpages_installed     = isset( $installed_plugins['wplegalpages/wplegalpages.php'] ) ? true : false;
		?>
		<div class="gdpr-install-activate-screen">
			<img id="gdpr-install-activate-img"src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/legal-pages-install-banner.jpg'; ?>" alt="WP Cookie Consent Logo">
			<div class="gdpr-popup-container">
			<p class="gdpr-plugin-install-activation-text">
			<?php esc_html_e( 'WP Legal Pages is currently inactive. Please install and activate the plugin to start generating your legal documents.', 'gdpr-cookie-consent' ); ?>
			</p>
								<?php 
				if(!$is_legalpages_installed) { ?>
				<a style="width:26%;" href="<?php echo esc_url($legalpages_install_url); ?>">
					<button id="gdpr-install-activate-btn"><?php esc_html_e('Install Now','gdpr-cookie-consent') ?></button>
				</a> 
				<?php }
				else { 
					?>
					<a style="width:26%;" href="<?php echo esc_url($legalpages_activation_url); ?>">
					<button id="gdpr-install-activate-btn"><?php esc_html_e('Activate Now','gdpr-cookie-consent') ?></button>
					</a> 
				<?php } ?>
       		 </div>
		</div>
	<?php } 

	/* Admin Dashboard Screen New */

	public function gdpr_cookie_consent_new_admin_dashboard_screen() {
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
		$template_parts_background = '';
		if ( true === $the_options['is_on'] ) {
			$template = $the_options['template'];
			if ( 'none' !== $template ) {
				$template_parts = explode( '-', $template );
				$template       = array_pop( $template_parts );
			}
			$the_options['template_parts'] = $template;
			if ( in_array( $template, array( 'navy_blue_center', 'navy_blue_box', 'navy_blue_square' ), true ) ) {
				$template_parts_background = '#1c2e5a';
			} elseif ( in_array( $template, array( 'almond_column' ), true ) ) {
				$template_parts_background = '#FCF5DF';
			} elseif ( in_array( $template, array( 'grey_column', 'grey_center' ), true ) ) {
				$template_parts_background = '#f4f4f4';
			} elseif ( in_array( $template, array( 'dark' ), true ) ) {
				$template_parts_background = '#000000';
			} elseif ( in_array( $template, array( 'dark_row' ), true ) ) {
				$template_parts_background = '#36423f';
			} else {
				$template_parts_background = '#ffffff';
			}
		}
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
				'ajaxurl'                    => admin_url( 'admin-ajax.php' ),
				'gdprurl'                    => GDPR_URL,
				'siteurl'                    => site_url(),
				'admin_url'                  => admin_url(),
				'is_pro_activated'           => $pro_is_activated,
				'is_data_req_on'             => $data_reqs_on,
				'is_consent_log_on'          => $consent_log_on,
				'gdpr_app_url'               => GDPR_APP_URL,
				'_ajax_nonce'                => wp_create_nonce( 'gdpr-cookie-consent' ),
				'is_user_connected'          => $is_user_connected,
				'background'                 => $template_parts_background,
				'button_accept_button_color' => $the_options['button_accept_button_color'],
				'is_iabtcf_on'               => $the_options['is_iabtcf_on'],
				'cookie_bar_as'			     => $the_options['cookie_bar_as'],
				'button_settings_as_popup'	 =>$the_options['button_settings_as_popup'],
			)
		);
		?>
		<style>
			.gdpr_messagebar_detail .category-group .category-item .description-container .group-toggle .checkbox input:checked+label,
			.gdpr_messagebar_detail .category-group .category-item .inner-description-container .group-toggle .checkbox input:checked+label,
			.gdpr_messagebar_detail .category-group .toggle-group .checkbox input:checked+label {
				background: <?php echo esc_attr( $the_options['button_accept_button_color'] ); ?> !important;
			}
			.gdprmodal-dialog .gdprmodal-footer button {
				background: <?php echo esc_attr( $the_options['button_accept_button_color'] ); ?> ;
			}

			.gdpr_messagebar_detail .gdprmodal-dialog .gdprmodal-header .close,
			#gdpr-ccpa-gdprmodal .gdprmodal-dialog .gdprmodal-body .close {
				background-color: <?php echo esc_attr( $the_options['button_accept_button_color'] ); ?> ;
			}
			
		</style>
		<?php
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'admin/partials/gdpr-cookie-consent-main-dashboard.php';
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
		$template_parts_background = '';
		if ( true === $the_options['is_on'] ) {
			$template = $the_options['template'];
			if ( 'none' !== $template ) {
				$template_parts = explode( '-', $template );
				$template       = array_pop( $template_parts );
			}
			$the_options['template_parts'] = $template;
			if ( in_array( $template, array( 'navy_blue_center', 'navy_blue_box', 'navy_blue_square' ), true ) ) {
				$template_parts_background = '#1c2e5a';
			} elseif ( in_array( $template, array( 'almond_column' ), true ) ) {
				$template_parts_background = '#FCF5DF';
			} elseif ( in_array( $template, array( 'grey_column', 'grey_center' ), true ) ) {
				$template_parts_background = '#f4f4f4';
			} elseif ( in_array( $template, array( 'dark' ), true ) ) {
				$template_parts_background = '#000000';
			} elseif ( in_array( $template, array( 'dark_row' ), true ) ) {
				$template_parts_background = '#36423f';
			} else {
				$template_parts_background = '#ffffff';
			}
		}
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
				'ajaxurl'                    => admin_url( 'admin-ajax.php' ),
				'gdprurl'                    => GDPR_URL,
				'siteurl'                    => site_url(),
				'admin_url'                  => admin_url(),
				'is_pro_activated'           => $pro_is_activated,
				'is_data_req_on'             => $data_reqs_on,
				'is_consent_log_on'          => $consent_log_on,
				'gdpr_app_url'               => GDPR_APP_URL,
				'_ajax_nonce'                => wp_create_nonce( 'gdpr-cookie-consent' ),
				'is_user_connected'          => $is_user_connected,
				'background'                 => $template_parts_background,
				'button_accept_button_color' => $the_options['button_accept_button_color'],
				'is_iabtcf_on'               => $the_options['is_iabtcf_on'],
				'cookie_bar_as'			     => $the_options['cookie_bar_as'],
				'button_settings_as_popup'	 => $the_options['button_settings_as_popup'],
				'first_time_installed' 		 => get_option('gdpr_first_time_installed', false),
			)
		);
		?>
		<style>
			.gdpr_messagebar_detail .category-group .category-item .description-container .group-toggle .checkbox input:checked+label,
			.gdpr_messagebar_detail .category-group .category-item .inner-description-container .group-toggle .checkbox input:checked+label,
			.gdpr_messagebar_detail .category-group .toggle-group .checkbox input:checked+label {
				background: <?php echo esc_attr( $the_options['button_accept_button_color'] ); ?> !important;
			}
			.gdprmodal-dialog .gdprmodal-footer button {
				background: <?php echo esc_attr( $the_options['button_accept_button_color'] ); ?> ;
			}

			.gdpr_messagebar_detail .gdprmodal-dialog .gdprmodal-header .close,
			#gdpr-ccpa-gdprmodal .gdprmodal-dialog .gdprmodal-body .close {
				background-color: <?php echo esc_attr( $the_options['button_accept_button_color'] ); ?> ;
			}
			
		</style>
		<?php
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
		$legal_pages_installed    = isset( $installed_plugins['wplegalpages/wplegalpages.php'] ) ? '1' : '0';
		$is_cookie_on      = isset( $cookie_options['is_on'] ) ? $cookie_options['is_on'] : '1';
		
		$cookie_usage_for  = $cookie_options['cookie_usage_for'];
		if ( $is_cookie_on == 'true' ) {
			$is_cookie_on = true;
		}
		$page_view_options = get_option("wpl_page_views");
		$total_page_views = get_option("wpl_total_page_views");
		$is_pro_active     = get_option( 'wpl_pro_active' );
		$api_key_activated = '';
		$api_key_activated = get_option( 'wc_am_client_wpl_cookie_consent_activated' );


		// if pro is active then fetch $max_mind_integrated from pro otherwise from free.
		

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
		$cookie_scan_url     = $admin_url . 'admin.php?page=gdpr-cookie-consent#cookie_settings#cookie_list#discovered_cookies';
		$plugin_page_url     = $admin_url . 'plugins.php';
		$key_activate_url    = $admin_url . 'admin.php?page=gdpr-cookie-consent#activation_key';
		$legalpages_install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=wplegalpages' ), 'install-plugin_wplegalpages' );
		$create_legalpages_url = $admin_url . 'admin.php?page=legal-pages';
		$consent_log_url     = $admin_url . 'admin.php?page=gdpr-cookie-consent#consent_logs';
		$cookie_design_url   = $admin_url . 'admin.php?page=gdpr-cookie-consent#cookie_settings#gdpr_design';
		$cookie_template_url = $admin_url . 'admin.php?page=gdpr-cookie-consent#cookie_settings#configuration';
		$script_blocker_url  = $admin_url . 'admin.php?page=gdpr-cookie-consent#cookie_settings#script_blocker';
		$third_party_url     = $admin_url . 'admin.php?page=gdpr-cookie-consent#policy_data';
		$documentation_url   = 'https://wplegalpages.com/docs/wp-cookie-consent/';
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
		$bypass_log   = get_option( 'wpl_cl_bypass' );
		$the_options         = Gdpr_Cookie_Consent::gdpr_get_settings();
		wp_enqueue_style( $this->plugin_name . '-dashboard' );
		wp_enqueue_script( $this->plugin_name . '-dashboard' );
		wp_localize_script(
			$this->plugin_name . '-dashboard',
			'dashboard_options',
			array(
				'the_options'           => $the_options,
               'ajaxurl'               => admin_url( 'admin-ajax.php' ),
				'active_plugins'        => $active_plugins,
				'showing_cookie_notice' => $is_cookie_on,
				'pro_installed'         => $pro_installed,
				'legal_pages_installed' => $legal_pages_installed,
				'pro_activated'         => $is_pro_active,
				'last_scanned'          => $last_scanned_details,
				'show_cookie_url'       => $show_cookie_url,
				'language_url'          => $language_url,
				'cookie_scan_url'       => $cookie_scan_url,
				'plugin_page_url'       => $plugin_page_url,
				'gdpr_pro_url'          => $gdpr_pro_url,
				'documentation_url'     => $documentation_url,
				'free_support_url'      => $free_support_url,
				'pro_support_url'       => $pro_support_url,
				'videos_url'            => $videos_url,
				'key_activate_url'      => $key_activate_url,
				'create_legalpages_url' => $create_legalpages_url,
				'legalpages_install_url'=> $legalpages_install_url,
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
				'bypass_log'            => $bypass_log,
				'is_user_connected'     => $is_user_connected,
				'cookie_policy'			=> $cookie_usage_for,
				'page_view_options' 	=> $page_view_options,
				'total_page_views'		=> $total_page_views
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
			$translations_file = get_site_url() . '/wp-content/plugins/gdpr-cookie-consent/admin/translations/translations.json';
			$translations      = wp_remote_get( $translations_file );
			$translations      = json_decode( wp_remote_retrieve_body( $translations ), true );

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
						$sanitized_category_descriptions = addslashes( $translated_category_descriptions[ $category_i_d ] );
						$sanitized_category_names = addslashes( $translated_category_names[ $category_i_d ] );
						// Update the table with the translated values.
						$wpdb->query(
							$wpdb->prepare(
								'UPDATE `' . $wpdb->prefix . 'gdpr_cookie_scan_categories`
								SET `gdpr_cookie_category_description` = %s,
									`gdpr_cookie_category_name` = %s
								WHERE `id_gdpr_cookie_category` = %d',
								$sanitized_category_descriptions,
								$sanitized_category_names,
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

			// Allow us to easily interact with the filesystem.
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
			global $wp_filesystem;

			if ( ! $wp_filesystem->put_contents( $css_file_path, $css_code_to_add, FS_CHMOD_FILE ) ) {
				// Handle error.
			}

			// Writing the CSS code to the minified CSS file.
			if ( ! $wp_filesystem->put_contents( $css_min_file_path, $css_code_to_add, FS_CHMOD_FILE ) ) {
				// Handle error.
			}

			$the_options                            = Gdpr_Cookie_Consent::gdpr_get_default_settings();
			$the_options['data_req_editor_message'] = '&lt;p&gt;Hi {name}&lt;/p&gt;&lt;p&gt;We have received your request on {blogname}. Depending on the specific request and legal obligations we might follow-up.&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;Kind regards,&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;{blogname}&lt;/p&gt;';
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
			$ab_option                         = get_option( 'wpl_ab_options' );
			$ab_options ['ab_testing_enabled'] = false;
			$ab_options ['ab_testing_period']  = '30';
			$ab_options ['necessary1']         = 0;
			$ab_options ['marketing1']         = 0;
			$ab_options ['analytics1']         = 0;
			$ab_options ['DNT1']               = 0;
			$ab_options ['noChoice1']          = 0;
			$ab_options ['noWarning1']         = 0;
			$ab_options ['necessary2']         = 0;
			$ab_options ['marketing2']         = 0;
			$ab_options ['analytics2']         = 0;
			$ab_options ['DNT2']               = 0;
			$ab_options ['noChoice2']          = 0;
			$ab_options ['noWarning2']         = 0;
			update_option( 'wpl_ab_options', $ab_options );
			delete_transient( 'gdpr_ab_testing_transient' );
			wp_send_json_success( array( 'restore_default_saved' => true ) );
		}
	}

	public function gdpr_cookie_consent_ajax_auto_generated_banner() {
		// Log to check if the function is being called
		$the_options    = Gdpr_Cookie_Consent::gdpr_get_settings();
		// Retrieve the data from the AJAX POST request
		if (isset($_POST['background_color'])) {
			$background_color = sanitize_text_field($_POST['background_color']);
			
			$the_options['auto_generated_background_color'] = $background_color;
			$the_options['button_accept_button_color'] = $the_options['auto_generated_background_color'];
			$the_options['button_accept_button_border_color'] = $the_options['auto_generated_background_color'];
			$the_options['button_decline_link_color'] = $the_options['auto_generated_background_color'];
			$the_options['button_decline_button_border_color'] = $the_options['auto_generated_background_color'];
			$the_options['button_settings_link_color']  = $the_options['auto_generated_background_color'];
			$the_options['button_settings_button_border_color']  = $the_options['auto_generated_background_color'];
			$the_options['button_decline_button_color']   = '#ffffff';
			$the_options['button_settings_button_color']   = '#ffffff';
			$the_options['button_decline_button_border_style'] = 'solid';
			$the_options['button_decline_button_border_width'] = '1';
			$the_options['button_settings_button_border_style'] = 'solid';
			$the_options['button_settings_button_border_width'] = '1';
			// Ab testing values.
			// Banner 1
			$the_options['button_accept_button_color1'] = $the_options['auto_generated_background_color'];
			$the_options['button_accept_button_border_color1'] = $the_options['auto_generated_background_color'];
			$the_options['button_decline_link_color1'] = $the_options['auto_generated_background_color'];
			$the_options['button_decline_button_border_color1'] = $the_options['auto_generated_background_color'];
			$the_options['button_settings_link_color1']  = $the_options['auto_generated_background_color'];
			$the_options['button_settings_button_border_color1']  = $the_options['auto_generated_background_color'];
			$the_options['button_decline_button_color1']   = '#ffffff';
			$the_options['button_settings_button_color1']   = '#ffffff';
			$the_options['button_decline_button_border_style1'] = 'solid';
			$the_options['button_decline_button_border_width1'] = '1';
			$the_options['button_settings_button_border_style1'] = 'solid';
			$the_options['button_settings_button_border_width1'] = '1';

			// Banner 2
			$the_options['button_accept_button_color2'] = $the_options['auto_generated_background_color'];
			$the_options['button_accept_button_border_color2'] = $the_options['auto_generated_background_color'];
			$the_options['button_decline_link_color2'] = $the_options['auto_generated_background_color'];
			$the_options['button_decline_button_border_color2'] = $the_options['auto_generated_background_color'];
			$the_options['button_settings_link_color2']  = $the_options['auto_generated_background_color'];
			$the_options['button_settings_button_border_color2']  = $the_options['auto_generated_background_color'];
			$the_options['button_decline_button_color2']   = '#ffffff';
			$the_options['button_settings_button_color2']   = '#ffffff';
			$the_options['button_decline_button_border_style2'] = 'solid';
			$the_options['button_decline_button_border_width2'] = '1';
			$the_options['button_settings_button_border_style2'] = 'solid';
			$the_options['button_settings_button_border_width2'] = '1';
			// Log the received background color for debugging
		}else{
			$the_options['auto_generated_background_color'] = "";
		}

		$the_options['is_banner_auto_generated'] = sanitize_text_field($_POST['is_auto_generated_banner_done']);
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
	}
	
	/* Added endpoint to send dashboard data from plugin to the saas appwplp server */
	public function gdpr_send_data_to_dashboard_appwplp_server(WP_REST_Request $request  ){		
		$current_user = wp_get_current_user();
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		//$cookie_scan_settings = apply_filters( 'gdpr_settings_cookie_scan_values', '' );
		$cookie_scan_class = new Gdpr_Cookie_Consent_Cookie_Scanner(); 
		$cookie_scan_settings = $cookie_scan_class->wpl_settings_cookie_scan_values();
		$default_settings = '';
		
		// Require the class file for gdpr cookie consent api framework settings.
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';

		// Instantiate a new object of the GDPR_Cookie_Consent_Settings class.
		$this->settings = new GDPR_Cookie_Consent_Settings();
		$user_email_id         = $this->settings->get_email();


		// Call the is_connected() method from the instantiated object to check if the user is connected.
		$is_user_connected = $this->settings->is_connected();

		/**
		 * Total No of scanned cookies.
		 */
		if ( ! empty( $cookie_scan_settings ) ) {
			$total_no_of_found_cookies = $cookie_scan_settings['scan_cookie_list']['total'];
		} else {
			$total_no_of_found_cookies = 0;
		}

		/**
		 * Total No of cookie categories.
		 */
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
			$number_of_categories = count( $unique_categories );
		} else {
			$number_of_categories = 0;
		}

		global $wpdb;


		$total_scanned_pages = get_option('gdpr_last_scan') . " Pages";


		// Get the current selected policy name
		$cookie_usage_for = $the_options['cookie_usage_for'];
		$gdpr_policy = '';
		if($cookie_usage_for == 'eprivacy'){
			$gdpr_policy = 'ePrivacy';
		}elseif($cookie_usage_for == 'both'){
			$gdpr_policy = 'GDPR & CCPA';
		}else{
			$gdpr_policy = strtoupper($cookie_usage_for);
		}

		$locationStatus = "";
		if ($the_options['is_worldwide_on']===true || $the_options['is_worldwide_on']==="true" || $the_options['is_worldwide_on']===1 ) {
			$locationStatus = "worldwide" ;
		}
		if ($the_options['is_eu_on']===true || $the_options['is_eu_on']==="true" || $the_options['is_eu_on']===1 ){
			$locationStatus = "EU Countries & UK";
		}
		if ($the_options['is_ccpa_on']===true || $the_options['is_ccpa_on']==="true" || $the_options['is_ccpa_on']===1 ){
			$locationStatus = "United States";
		}
		if ($the_options['is_selectedCountry_on']===true || $the_options['is_selectedCountry_on']==="true" || $the_options['is_selectedCountry_on']===1 ){
			$locationStatus = ($locationStatus == "") ? "United States" : $locationStatus . ", Other selected countries";
		}

		
		$last_scan_time = $cookie_scan_settings['last_scan']['created_at'];

		$active_plugins = $this->gdpr_cookie_consent_active_plugins();
		return rest_ensure_response(
			array(
				'success' => true,
				'last_scan_time'             	   => $last_scan_time,
				'schedule_scan_when'               => isset( $the_options['schedule_scan_when'] ) ? $the_options['schedule_scan_when'] : null,
				'is_user_connected'                => $is_user_connected,
				'total_no_of_found_cookies'        => $total_no_of_found_cookies,
				'total_scanned_pages'              => $total_scanned_pages,
				'number_of_categories'             => $number_of_categories,
				'wpl_cl_decline'                   => get_option( 'wpl_cl_decline' ),
				'wpl_cl_accept'                    => get_option( 'wpl_cl_accept' ),
				'wpl_cl_partially_accept'          => get_option( 'wpl_cl_partially_accept' ),
				'wpl_cl_bypass'                    => get_option( 'wpl_cl_bypass' ),
				'wpl_page_views'				   => get_option( 'wpl_page_views' ),
				'total_page_views'				   => get_option('wpl_total_page_views'),
				'ignore_count'					   => get_option('wpl_total_ignore_count') === false ? 0 : get_option('wpl_total_ignore_count'),
				'client_site_is_on'				   => $the_options['is_on'],
				'active_plugins'				   => $active_plugins,
				'client_site_url'                  => get_site_url(),
				'cookie_usage_for'                 => $gdpr_policy,
				'user_email_id'					   => $user_email_id,
				'location_status'				   => $locationStatus,
			)
		);
	}
	// Register the REST API route for pie chart and cookie summary data from plugin to the saas appwplp server 

	public function register_gdpr_dashboard_route() {
		global $is_user_connected, $api_user_plan; // Make global variables accessible
		$this->settings = new GDPR_Cookie_Consent_Settings();
		
		$is_user_connected = $this->settings->is_connected();
		

		register_rest_route(
			'gdpr/v2', // Namespace
			'/get_user_dashboard_data', 
			array(
				'methods'  => 'POST',
				'callback' => array($this, 'gdpr_send_data_to_dashboard_appwplp_server'), // Function to handle the request
				'permission_callback' => function() use ($is_user_connected) {
					// Check if user is connected and the API plan is valid
					if ($is_user_connected) {
						return true; // Allow access
					}
					return new WP_Error('rest_forbidden', 'Unauthorized access', array('status' => 401));
				},
			)
		);
		
	}
	//Function to register the Import CSV page - Policy data
	function register_gdpr_policies_import_page() {
		// This adds a page, even if it's not visible in the admin menu
		add_submenu_page(
			null,  // This makes the page hidden in the menu
			__( 'GDPR Policies Import', 'gdpr-cookie-consent' ),
			__( 'GDPR Policies Import', 'gdpr-cookie-consent' ),
			'manage_options',  // Capability required
			'gdpr-policies-import',
			array( $this, 'gdpr_policies_import_page')
		);
	}

	// Function to remove the admin notices in policy data.
	public function gdpr_remove_admin_notices() {
		if (isset($_GET['page']) && $_GET['page'] === 'gdpr-cookie-consent') {
			remove_all_actions('admin_notices');
			remove_all_actions('all_admin_notices');
		}
	}
	/* Callback function for new unified Dashboard page */
	public function gdpr_cookie_consent_unified_dashboard( $legal_pages_installed, $gdpr_installed, $is_legalpages_active, $is_gdpr_active) {
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
		$template_parts_background = '';
		if ( true === $the_options['is_on'] ) {
			$template = $the_options['template'];
			if ( 'none' !== $template ) {
				$template_parts = explode( '-', $template );
				$template       = array_pop( $template_parts );
			}
			$the_options['template_parts'] = $template;
			if ( in_array( $template, array( 'navy_blue_center', 'navy_blue_box', 'navy_blue_square' ), true ) ) {
				$template_parts_background = '#1c2e5a';
			} elseif ( in_array( $template, array( 'almond_column' ), true ) ) {
				$template_parts_background = '#FCF5DF';
			} elseif ( in_array( $template, array( 'grey_column', 'grey_center' ), true ) ) {
				$template_parts_background = '#f4f4f4';
			} elseif ( in_array( $template, array( 'dark' ), true ) ) {
				$template_parts_background = '#000000';
			} elseif ( in_array( $template, array( 'dark_row' ), true ) ) {
				$template_parts_background = '#36423f';
			} else {
				$template_parts_background = '#ffffff';
			}
		}
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
				'ajaxurl'                    => admin_url( 'admin-ajax.php' ),
				'gdprurl'                    => GDPR_URL,
				'siteurl'                    => site_url(),
				'admin_url'                  => admin_url(),
				'is_pro_activated'           => $pro_is_activated,
				'is_data_req_on'             => $data_reqs_on,
				'is_consent_log_on'          => $consent_log_on,
				'gdpr_app_url'               => GDPR_APP_URL,
				'_ajax_nonce'                => wp_create_nonce( 'gdpr-cookie-consent' ),
				'is_user_connected'          => $is_user_connected,
				'background'                 => $template_parts_background,
				'button_accept_button_color' => $the_options['button_accept_button_color'],
				'is_iabtcf_on'               => $the_options['is_iabtcf_on'],
				'cookie_bar_as'			     => $the_options['cookie_bar_as'],
				'button_settings_as_popup'	 =>$the_options['button_settings_as_popup'],
			)
		);
		?>
		<style>
			.gdpr_messagebar_detail .category-group .category-item .description-container .group-toggle .checkbox input:checked+label,
			.gdpr_messagebar_detail .category-group .category-item .inner-description-container .group-toggle .checkbox input:checked+label,
			.gdpr_messagebar_detail .category-group .toggle-group .checkbox input:checked+label {
				background: <?php echo esc_attr( $the_options['button_accept_button_color'] ); ?> !important;
			}
			.gdprmodal-dialog .gdprmodal-footer button {
				background: <?php echo esc_attr( $the_options['button_accept_button_color'] ); ?> ;
			}

			.gdpr_messagebar_detail .gdprmodal-dialog .gdprmodal-header .close,
			#gdpr-ccpa-gdprmodal .gdprmodal-dialog .gdprmodal-body .close {
				background-color: <?php echo esc_attr( $the_options['button_accept_button_color'] ); ?> ;
			}
			
		</style>
		<?php
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'admin/partials/gdpr-cookie-consent-main-dashboard.php';
	
	}

	// Plugin Installation code

	public function gdpr_wplp_install_plugin_ajax_handler() {
    // Check nonce for security
	check_ajax_referer( 'gdpr-cookie-consent', '_ajax_nonce' );


    // Load necessary WordPress plugin installer classes
    if ( ! class_exists( 'Plugin_Upgrader' ) ) {
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    }
    if ( ! function_exists( 'plugins_api' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    }

    $plugin_slug = sanitize_text_field( $_POST['plugin_slug'] ); // Plugin slug from AJAX request

    // Get plugin information
    $api = plugins_api(
        'plugin_information',
        array(
            'slug'   => $plugin_slug,
            'fields' => array(
                'sections' => false,
            ),
        )
    );

    if ( is_wp_error( $api ) ) {
        wp_send_json_error( array( 'message' => $api->get_error_message() ) );
    }

    // Install the plugin
    $upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
    $result   = $upgrader->install( $api->download_link );

    if ( is_wp_error( $result ) ) {
        wp_send_json_error( array( 'message' => $result->get_error_message() ) );
    }

    // Activate the plugin
    $activate = activate_plugin( $plugin_slug . '/' . $plugin_slug . '.php' );
    if ( is_wp_error( $activate ) ) {
        wp_send_json_error( array( 'message' => $activate->get_error_message() ) );
    }

    // Success response
    wp_send_json_success( array( 'message' => __( 'Plugin installed and activated successfully.', 'gdpr-cookie-consent' ) ) );
}
public function gdpr_support_request_handler() {
    // Verify nonce for security
    if (!isset($_POST['gdpr_nonce']) || !wp_verify_nonce($_POST['gdpr_nonce'], 'gdpr_support_request_nonce')) {
        wp_send_json_error(['message' => 'Security check failed.']);
    }

    // Sanitize and validate input
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $message = sanitize_textarea_field($_POST['message']);

    // Support email details
    $to = "hello@wpeka.com"; // Replace with your support email
    $subject = "Support Request from $name";
    $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
    $headers = ['Reply-To: ' . $email];

    // Send the email and respond with JSON
    if (wp_mail($to, $subject, $body, $headers)) {
        wp_send_json_success(['message' => 'Your message has been sent successfully.']);
    } else {
        wp_send_json_error(['message' => 'There was an error sending your message. Please try again later.']);
    }
}


}
