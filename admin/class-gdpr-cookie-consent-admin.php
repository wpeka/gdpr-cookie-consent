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
	public $settings;
	public $templates_json;

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
			add_action( 'wp_ajax_wpl_gcm_advertiser_mode_data', array( $this, 'wp_settings_gcm_advertiser_mode' ) );
		}
		$pro_is_activated = get_option( 'wpl_pro_active', false );
		if ( ! $pro_is_activated ) {
			if ( ! shortcode_exists( 'wpl_data_request' ) ) {
				add_shortcode( 'wpl_data_request', array( $this, 'wpl_data_reqs_shortcode' ) );         // a shortcode [wpl_data_request].
			}

			if ( class_exists( 'Gdpr_Cookie_Consent' ) ) {
				$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
			}

			add_action( 'admin_post_gdpr_resolve', [ $this, 'wpl_data_req_process_resolve' ] );
			add_action( 'admin_init', array( $this, 'gdpr_migrate_old_template_names_once') );
			add_action('admin_init', function() {
				if (!defined('DOING_AJAX') && !defined('REST_REQUEST')) {
					$this->gdpr_initialise();
				}
			});
			add_action( 'wp_ajax_set_default_test_banner_1', array( $this, 'set_default_banner_1' ) );
			add_action( 'wp_ajax_set_default_test_banner_2', array( $this, 'set_default_banner_2' ) );
			add_action( 'add_data_request_content', array( $this, 'wpl_data_req_process_delete' ) );
			add_action( 'add_data_request_content', array( $this, 'wpl_data_requests_overview' ) );
			add_action('gdpr_cookie_consent_admin_screen', array($this, 'gdpr_cookie_consent_new_admin_screen'));
			add_action('gdpr_cookie_consent_new_admin_dashboard_screen', array($this, 'gdpr_cookie_consent_new_admin_dashboard_screen'));
			add_action('gdpr_help_page_content', array($this, 'gdpr_help_page_content'));
			add_action('refresh_gacm_vendor_list_event', array($this,'get_gacm_data'));
			add_action( 'rest_api_init', array($this, 'allow_cors_for_react_app'));
			add_action('rest_api_init', array($this, 'register_gdpr_dashboard_route'));
			add_action('rest_api_init', array($this, 'wplp_gdpr_generate_api_secret'));
			//For Import CSV option on Policy data page
			add_action( 'admin_menu', array($this,'register_gdpr_policies_import_page') );
			add_action('admin_menu', array($this,'gdpr_reorder_admin_menu'), 999);
			add_action('admin_menu', array($this,'gdpr_remove_dashboard_submenu'),99);
			add_action('admin_notices', array($this,'gdpr_remove_admin_notices'),1);
			add_action('all_admin_notices', array($this,'gdpr_remove_admin_notices'),1);
			//option to store page views
			if(get_option("wpl_page_views") === false) add_option("wpl_page_views", []);
			if(get_option("page_view_notice_message") === false ) add_option( "page_view_notice_message", "");
			if(get_option("wpl_monthly_page_views") === false ) add_option( "wpl_monthly_page_views", 0);
			if(get_option("wpl_total_page_views") === false) add_option("wpl_total_page_views", 0);
			add_action('wp_ajax_install_plugin', array($this, 'gdpr_wplp_install_plugin_ajax_handler'));
			add_action('wp_ajax_gdpr_support_request', array($this, 'gdpr_support_request_handler'));
			add_action('wp_ajax_nopriv_gdpr_support_request', array($this, 'gdpr_support_request_handler'));
				

		}
		
		add_action( 'update_maxmind_db_event', array($this,'download_maxminddb' ));
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
		wp_enqueue_style( $this->plugin_name .'-fonts-css',plugin_dir_url( __FILE__ ) .  'css/gdpr-cookie-consent-fonts'. GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-fonts-css' );
		
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
		wp_enqueue_script($this->plugin_name . 'introjs-js', plugin_dir_url( __FILE__ ) . 'js/intro.min.js', array('jquery'), $this->version, false);
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



	public function set_default_banner_1(){
		$the_options         = Gdpr_Cookie_Consent::gdpr_get_settings();
		$ab_options['ab_testing_enabled'] = 'false';
		$ab_options['ab_testing_auto'] = 'false';
		update_option( 'wpl_ab_options', $ab_options );
		$the_options =  $this->wpl_set_default_ab_testing_banner( $the_options, '1' );
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
	}
	public function set_default_banner_2(){
		$the_options         = Gdpr_Cookie_Consent::gdpr_get_settings();
		$ab_options['ab_testing_enabled'] = 'false';
		$ab_options['ab_testing_auto'] = 'false';
		update_option( 'wpl_ab_options', $ab_options );
		$the_options =  $this->wpl_set_default_ab_testing_banner( $the_options, '2' );
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
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


	public function gdpr_migrate_old_template_names_once() {

		if (get_option('gdpr_template_migration_done')) {
			return;
		}

		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings(); 

		if (! isset($the_options['template'])) {
			return;
		}

		$template = $the_options['template'];
		$prefixes = ['banner-', 'popup-', 'widget-'];

		foreach ($prefixes as $prefix) {
			if (strpos($template, $prefix) === 0) {
				$original_key = substr($template, strlen($prefix));
				$mapped_templates = [
					'default'           => 'default',
					'almond_column'     => 'almond',
					'navy_blue_center'  => 'blue_center',
					'gray_column'       => 'gray_pink',
					'dark_row'          => 'gray',
					'dark'              => 'dark',
					'gray_center'       => 'almond_row',
					'navy_blue_box'     => 'blue_full',
					'navy_blue_square'  => 'blue_center_column',
				];

				if (isset($mapped_templates[$original_key])) {
					$new_template = $mapped_templates[$original_key];

					if ($new_template === 'blue_full' &&
						isset($the_options["cookie_bar_as"]) &&
						$the_options["cookie_bar_as"] === 'banner') {
						$new_template = 'blue_center';
					}

					$the_options['template'] = $new_template;
					update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
				}

				break;
			}
		}

		update_option('gdpr_template_migration_done', true);
	}



	public function gdpr_initialise(){
		// if (!get_option('gdpr_default_template_object')) {
		
			$default_json_path = plugin_dir_path(__FILE__) . '../includes/templates/default_template.json';
			$json_data = file_get_contents($default_json_path);
			$default_template = json_decode($json_data, true); 
			update_option('gdpr_default_template_object', $default_template);
		// }
		$this->settings = new GDPR_Cookie_Consent_Settings();

		// Call the is_connected() method from the instantiated object to check if the user is connected.
		$is_user_connected = $this->settings->is_connected();
		$the_options = get_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
		if(!isset($this->templates_json)){
			if($is_user_connected){
				$response = wp_remote_get(GDPR_API_URL . 'get_templates_json');
				if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
					$json_data = wp_remote_retrieve_body($response);
					$this -> templates_json = json_decode($json_data, true);
					if(!isset($the_options['selected_template_json']) || json_decode($the_options['selected_template_json'], true)['name'] != $the_options['template']) $the_options['selected_template_json'] = ($the_options['template'] == 'default' ? json_encode(get_option('gdpr_default_template_object')) : json_encode($this -> templates_json[$the_options['template']]));
				} else {
					$this -> templates_json = []; // Fallback in case of error
					if(!isset($the_options['selected_template_json']) || json_decode($the_options['selected_template_json'], true)['name'] != $the_options['template']) $the_options['selected_template_json'] = json_encode([]);
				}
			}
			else{
				if($the_options['template'] == 'default'){
					$the_options['selected_template_json'] = json_encode(get_option('gdpr_default_template_object'));
				}
				else{
					$response = wp_remote_get(GDPR_API_URL . 'get_templates_json');
					if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
						$json_data = wp_remote_retrieve_body($response);
						$templates_json = json_decode($json_data, true);
						$this->templates_json = [$the_options['template'] => $templates_json[$the_options['template']]];
						if(!isset($the_options['selected_template_json']) || json_decode($the_options['selected_template_json'], true)['name'] != $the_options['template']) $the_options['selected_template_json'] = json_encode($templates_json[$the_options['template']]);
					} else {
						if(!isset($the_options['selected_template_json']) || json_decode($the_options['selected_template_json'], true)['name'] != $the_options['template']) $the_options['selected_template_json'] = json_encode([]); // Fallback in case of error
					}
				}
			}
		}
		
		
		update_option(GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options);
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

				global $wpdb;
				// Delete fields from database
					delete_option('gdpr_single_page_scan_url');
					delete_option('wc_am_product_id_gdpr_cookie_consent');
					delete_option('wc_am_client_gdpr_cookie_consent');
					delete_option('gdpr_last_scan');
					delete_option('gdpr_settings_enabled');
					delete_option(GDPR_COOKIE_CONSENT_SETTINGS_VENDOR_CONSENT);
					delete_option('_transient_timeout_gdpr_ab_testing_transient');
					delete_option('_transient_gdpr_ab_testing_transient');
					delete_option('_transient_timeout_gdpr_display_message_other_plugin_on_change');
					delete_option('_transient_gdpr_display_message_other_plugin_on_change');
	
					// Delete consent logs from posts table
					
					$posts = $wpdb->get_col("
								SELECT ID FROM {$wpdb->posts} WHERE post_type = 'wplconsentlogs'
							");
						if (!empty($posts)) {
							foreach ($posts as $post_id) {
								// Delete related post meta
								delete_post_meta($post_id, '_wplconsentlogs_ip');
								delete_post_meta($post_id, '_wplconsentlogs_userid');
								delete_post_meta($post_id, '_wplconsentlogs_details');
								delete_post_meta($post_id, '_wplconsentlogs_country');
								delete_post_meta($post_id, '_wplconsentlogs_siteurl');
								delete_post_meta($post_id, '_wplconsentlogs_consent_forward');
								wp_delete_post($post_id, true); 
							}
						}
					// Delete policies data from posts table
					
					$policy_posts = $wpdb->get_col("
								SELECT ID FROM {$wpdb->posts} WHERE post_type = 'gdprpolicies'
							");
						if (!empty($policy_posts)) {
							foreach ($policy_posts as $policy_post_id) {
								delete_post_meta($policy_post_id, '_gdpr_policies_domain');
								delete_post_meta($policy_post_id, '_gdpr_policies_links_editor');
								wp_delete_post($policy_post_id, true); 
							}
						}
				delete_option( 'gdpr_admin_modules' );
				delete_option( 'gdpr_public_modules' );
				delete_option( 'gdpr_version_number' );
				delete_option( '	analytics_activation_redirect_gdpr-cookie-consent' );
				delete_option( 'wpl_logs_admin' );
				delete_option( 'wpl_datarequests_db_version' );
				delete_option( 'wpl_cl_decline' );
				delete_option( 'wpl_page_views' );
				delete_option( 'page_view_notice_message' );
				delete_option( 'wpl_monthly_page_views' );
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
			
			$banner1_noChoice  = array_key_exists( 'noChoice1', $ab_options ) ? $ab_options['noChoice1'] : 0;
			$banner2_noChoice  = array_key_exists( 'noChoice2', $ab_options ) ? $ab_options['noChoice2'] : 0;
			$banner1_accept  = array_key_exists( 'accept1', $ab_options ) ? $ab_options['accept1'] : 0;
			$banner2_accept  = array_key_exists( 'accept2', $ab_options ) ? $ab_options['accept2'] : 0;
			$banner1_acceptAll  = array_key_exists( 'acceptAll1', $ab_options ) ? $ab_options['acceptAll1'] : 0;
			$banner2_acceptAll  = array_key_exists( 'acceptAll2', $ab_options ) ? $ab_options['acceptAll2'] : 0;
			$banner1_reject  = array_key_exists( 'reject1', $ab_options ) ? $ab_options['reject1'] : 0;
			$banner2_reject  = array_key_exists( 'reject2', $ab_options ) ? $ab_options['reject2'] : 0;
			$banner1_bypass  = array_key_exists( 'bypass1', $ab_options ) ? $ab_options['bypass1'] : 0;
			$banner2_bypass  = array_key_exists( 'bypass2', $ab_options ) ? $ab_options['bypass2'] : 0;
			$positive_percentage1 = ($banner1_accept + $banner1_acceptAll) / (($banner1_accept + $banner1_reject + $banner1_bypass + $banner1_acceptAll + $banner1_noChoice) > 0 ? ($banner1_accept + $banner1_reject + $banner1_bypass + $banner1_acceptAll + $banner1_noChoice) : 1);
			$positive_percentage2 = ($banner2_accept + $banner2_acceptAll) / (($banner2_accept + $banner2_reject + $banner2_bypass + $banner2_acceptAll + $banner2_noChoice) > 0 ? ($banner2_accept + $banner2_reject + $banner2_bypass + $banner2_acceptAll + $banner2_noChoice) : 1);
			$negative_percentage1 = ($banner1_reject + $banner1_bypass + $banner1_noChoice) / (($banner1_accept + $banner1_reject + $banner1_bypass + $banner1_acceptAll + $banner1_noChoice) > 0 ? ($banner1_accept + $banner1_reject + $banner1_bypass + $banner1_acceptAll + $banner1_noChoice) : 1);
			$negative_percentage2 = ($banner2_reject + $banner2_bypass + $banner2_noChoice) / (($banner2_accept + $banner2_reject + $banner2_bypass + $banner2_acceptAll + $banner2_noChoice) > 0 ? ($banner2_accept + $banner2_reject + $banner2_bypass + $banner2_acceptAll + $banner2_noChoice) : 1);
			$banner1_performance = $positive_percentage1 - $negative_percentage1;
			$banner2_performance = $positive_percentage2 - $negative_percentage2; 
			$the_options         = Gdpr_Cookie_Consent::gdpr_get_settings();
			if(isset( $ab_options['ab_testing_auto'] ) && ($ab_options['ab_testing_auto'] === true || $ab_options['ab_testing_auto'] === 'true')){
				$ab_options['ab_testing_enabled'] = 'false';
				$ab_options['ab_testing_auto'] = 'false';
				update_option( 'wpl_ab_options', $ab_options );
				if ( $banner1_performance > $banner2_performance ) {
					$the_options =  $this->wpl_set_default_ab_testing_banner( $the_options, '1' );
				} else {
					$the_options =  $this->wpl_set_default_ab_testing_banner( $the_options, '2' );
				}
				update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
			}
			
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
		$the_options['button_decline_action']                = $the_options[ 'button_decline_action' . $banner_choice ];
		$the_options['button_decline_url']                   = $the_options[ 'button_decline_url' . $banner_choice ];
		$the_options['button_decline_new_win']               = $the_options[ 'button_decline_new_win' . $banner_choice ];
		$the_options['button_settings_is_on']                = $the_options[ 'button_settings_is_on' . $banner_choice ];
		$the_options['button_settings_as_popup']             = $the_options[ 'button_settings_as_popup' . $banner_choice ];
		$the_options['button_settings_text']                 = $the_options[ 'button_settings_text' . $banner_choice ];
		$the_options['button_settings_link_color']           = $the_options[ 'button_settings_link_color' . $banner_choice ];
		$the_options['button_settings_as_button']            = $the_options[ 'button_settings_as_button' . $banner_choice ];
		$the_options['button_settings_button_color']         = $the_options[ 'button_settings_button_color' . $banner_choice ];
		$the_options['button_settings_button_opacity']       = $the_options[ 'button_settings_button_opacity' . $banner_choice ];
		$the_options['button_settings_button_border_style']  = $the_options[ 'button_settings_button_border_style' . $banner_choice ];
		$the_options['button_settings_button_border_color']  = $the_options[ 'button_settings_button_border_color' . $banner_choice ];
		$the_options['button_settings_button_border_width']  = $the_options[ 'button_settings_button_border_width' . $banner_choice ];
		$the_options['button_settings_button_border_radius'] = $the_options[ 'button_settings_button_border_radius' . $banner_choice ];
		$the_options['button_settings_display_cookies']      = $the_options[ 'button_settings_display_cookies' . $banner_choice ];
		$the_options['button_confirm_text']                  = $the_options[ 'button_confirm_text' . $banner_choice ];
		$the_options['button_confirm_link_color']            = $the_options[ 'button_confirm_link_color' . $banner_choice ];
		$the_options['button_confirm_button_color']          = $the_options[ 'button_confirm_button_color' . $banner_choice ];
		$the_options['button_confirm_button_opacity']        = $the_options[ 'button_confirm_button_opacity' . $banner_choice ];
		$the_options['button_confirm_button_border_style']   = $the_options[ 'button_confirm_button_border_style' . $banner_choice ];
		$the_options['button_confirm_button_border_color']   = $the_options[ 'button_confirm_button_border_color' . $banner_choice ];
		$the_options['button_confirm_button_border_width']   = $the_options[ 'button_confirm_button_border_width' . $banner_choice ];
		$the_options['button_confirm_button_border_radius']  = $the_options[ 'button_confirm_button_border_radius' . $banner_choice ];
		$the_options['button_cancel_text']                   = $the_options[ 'button_cancel_text' . $banner_choice ];
		$the_options['button_cancel_link_color']             = $the_options[ 'button_cancel_link_color' . $banner_choice ];
		$the_options['button_cancel_button_color']           = $the_options[ 'button_cancel_button_color' . $banner_choice ];
		$the_options['button_cancel_button_opacity']         = $the_options[ 'button_cancel_button_opacity' . $banner_choice ];
		$the_options['button_cancel_button_border_style']    = $the_options[ 'button_cancel_button_border_style' . $banner_choice ];
		$the_options['button_cancel_button_border_color']    = $the_options[ 'button_cancel_button_border_color' . $banner_choice ];
		$the_options['button_cancel_button_border_width']    = $the_options[ 'button_cancel_button_border_width' . $banner_choice ];
		$the_options['button_cancel_button_border_radius']   = $the_options[ 'button_cancel_button_border_radius' . $banner_choice ];
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
		$the_options['button_accept_all_btn_border_style']   = $the_options[ 'button_accept_all_btn_border_style' . $banner_choice ];
		$the_options['button_accept_all_btn_border_color']   = $the_options[ 'button_accept_all_btn_border_color' . $banner_choice ];
		$the_options['button_accept_all_btn_opacity']        = $the_options[ 'button_accept_all_btn_opacity' . $banner_choice ];
		$the_options['button_accept_all_btn_border_width']   = $the_options[ 'button_accept_all_btn_border_width' . $banner_choice ];
		$the_options['button_accept_all_btn_border_radius']  = $the_options[ 'button_accept_all_btn_border_radius' . $banner_choice ];

		// resetting ab testing settings and analytics
		$ab_options                        = get_option( 'wpl_ab_options' );
		$ab_options ['ab_testing_enabled'] = false;
		$ab_options ['ab_testing_period']  = '30';
		$ab_options ['noChoice1']   = 0;
		$ab_options ['noChoice2']   = 0;
		$ab_options ['accept1']   = 0;
		$ab_options ['accept2']   = 0;
		$ab_options ['acceptAll1']   = 0;
		$ab_options ['acceptAll2']   = 0;
		$ab_options ['reject1']   = 0;
		$ab_options ['reject2']   = 0;
		$ab_options ['bypass1']   = 0;
		$ab_options ['bypass2']   = 0;

		// Upload logo. 
		if($banner_choice == '1'){
			$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1 );
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD, $get_banner_img );
		}else{
			$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD2 );
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD, $get_banner_img );
		}
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
								<p><span>%3$s<strong>Cookie Consent for WP</strong>.%4$s</span></p>
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
						esc_html__( ' Could you please write us a review and give it a 5-star rating on WordPress? Just to help us spread the word and boost our motivation.', 'gdpr' ),
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
		if ( isset( $_GET['page'] ) && $_GET['page'] !== 'gdpr-cookie-consent' ) {
			return;
		}
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
						<?php
						$is_user_connected = $this->settings->is_connected();
						$installed_plugins = get_plugins();
						$pro_installed     = isset( $installed_plugins['wpl-cookie-consent/wpl-cookie-consent.php'] ) ? true : false;
						$api_user_plan     = $this->settings->get_plan();
						if ( $is_user_connected == true && ! $pro_installed && $api_user_plan != 'free' ) {
							$api_url           = GDPR_API_URL;
							$updated_api_url = str_replace('/v2/', '/v1/', $api_url);
							$url             = $updated_api_url . 'gdpr_export_consent_log';
							$args            = array(
								'source'     => 'gdpr-cookie-consent',
								'plugin_dir' => plugins_url(),
								'nonce'      => wp_create_nonce( 'wpl_csv_nonce' ),  
							);
							$request_url     = add_query_arg( $args, $url );
							$response        = wp_remote_post( $request_url, array( 'timeout' => 10 ) );
							$status_code     = wp_remote_retrieve_response_code( $response );
							if ( 200 === (int) $status_code ) {
								$body = json_decode( wp_remote_retrieve_body( $response ), true );
								echo wp_kses_post( $body['body'] );
							} else {
								?>
								<span class="data-req-export-button gdpr-not-pro-tooltip"><?php esc_html_e( 'Export as CSV', 'gdpr-cookie-consent' ); ?></span>
								<div class="gdpr-not-pro-tooltip-text"><?php echo esc_html_e( 'This feature is only available in the Pro version. Kindly', 'gdpr-cookie-consent' );?> <a href="<?php echo esc_url( 'https://wplegalpages.com/pricing/?utm_source=wpcookieconsent&utm_medium=consent-log-export-settings' ); ?>" target="_blank"><?php echo esc_html_e( 'UPGRADE', 'gdpr-cookie-consent' ); ?></a> <?php esc_html_e( 'to unlock and use it.', 'gdpr-cookie-consent' ) ?></div>
								<?php
							}
						} else {
							?>
							<span class="data-req-export-button gdpr-not-pro-tooltip"><?php esc_html_e( 'Export as CSV', 'gdpr-cookie-consent' ); ?></span>
							<div class="gdpr-not-pro-tooltip-text"><?php echo esc_html_e( 'This feature is only available in the Pro version. Kindly', 'gdpr-cookie-consent' );?> <a href="<?php echo esc_url( 'https://wplegalpages.com/pricing/?utm_source=wpcookieconsent&utm_medium=consent-log-export-settings' ); ?>" target="_blank"><?php echo esc_html_e( 'UPGRADE', 'gdpr-cookie-consent' ); ?></a> <?php esc_html_e( 'to unlock and use it.', 'gdpr-cookie-consent' ) ?></div>
							<?php
						}
						?>
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
	 * 
	 * function to add advertiser mode html markup in admin settings
	 * @return void
	 */
	public function wp_settings_gcm_advertiser_mode() {
			$pro_is_activated  = get_option( 'wpl_pro_active', false );
			$installed_plugins = get_plugins();
			$pro_installed     = isset( $installed_plugins['wpl-cookie-consent/wpl-cookie-consent.php'] ) ? true : false;
			$pro_is_activated  = get_option( 'wpl_pro_active', false );
			$api_key_activated = get_option( 'wc_am_client_wpl_cookie_consent_activated','' );
			
			// Require the class file for gdpr cookie consent api framework settings.
			require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';
			// Instantiate a new object of the GDPR_Cookie_Consent_Settings class.
			$this->settings = new GDPR_Cookie_Consent_Settings();
			// Call the is_connected() method from the instantiated object to check if the user is connected.
			$is_user_connected = $this->settings->is_connected();
			$api_user_plan     = $this->settings->get_plan();
			$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
			
			if ( ! defined( 'ABSPATH' ) ) {
				exit;
			}
			$response_advertiser_mode = wp_remote_post(
					GDPR_API_URL . 'get_advertiser_mode_data',
					array(
						'body' => array(
							'the_options_enable_safe' => $the_options['enable_safe'],
							'pro_installed'           => $pro_installed,
							'pro_is_activated'        => $pro_is_activated,
							'api_key_activated'       => $api_key_activated,
							'is_user_connected'       => $is_user_connected,
							'api_user_plan'			  => $api_user_plan,
						),
						'timeout' => 60,
					)
				);
			if ( is_wp_error( $response_advertiser_mode ) ) {
			 	$advertiser_mode_text = '';
			}

			 	$response_status = wp_remote_retrieve_response_code( $response_advertiser_mode );

				if (200 === $response_status) {
				$advertiser_mode_text = json_decode(wp_remote_retrieve_body($response_advertiser_mode));
				wp_send_json_success(['html' => $advertiser_mode_text]);
			} else {
				wp_send_json_error(['message' => __('Failed to retrieve data from server.', 'gdpr-cookie-consent')]);
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

    	// Must be logged in
		if ( ! is_user_logged_in() ) {
			wp_die( 'Unauthorized request.' );
		}

		// Only admin can resolve
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Insufficient permissions.' );
		}

		// Validate request + nonce
		if (isset( $_GET['page'] ) && ( $_GET['page'] == 'gdpr-cookie-consent' ) &&
			isset( $_GET['action'] ) &&
			$_GET['action'] === 'resolve' &&
			isset( $_GET['_wpnonce'] ) &&
			wp_verify_nonce( $_GET['_wpnonce'], 'wpl_resolve_request' ) &&
			! empty( $_GET['id'] )
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
   	wp_die( 'Invalid request.' );
}
	/**
	 * Handle delete request.
	 */
	public function wpl_data_req_process_delete() {
		if ( ! current_user_can( 'manage_options' ) ) {
		    wp_die( 'Unauthorized request.' );
		}

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
	public function gdpr_remove_dashboard_submenu() {
		// Define the current version constant
		$current_version = GDPR_COOKIE_CONSENT_VERSION;

		// Target version to hide the submenu
		$target_version = '3.7.0';

		// Check if the current version is below the target version
		if (version_compare($current_version, $target_version, '<')) {
			// Remove the 'Dashboard' submenu
			remove_submenu_page('gdpr-cookie-consent', 'wplp-dashboard');
			remove_submenu_page('gdpr-cookie-consent', 'wplp-dashboard#help-page');
		}
	}
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
				'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTIuODMzMTcgOS4wMjYyOUwwLjM1NzQyMiAwLjM2MTgxNkgyLjM1NTc2TDMuNzg3OTggNi4zODIwOUgzLjg1OThMNS40Mzk4OSAwLjM2MTgxNkg3LjE1MDk1TDguNzI2ODEgNi4zOTQ3OUg4LjgwMjg2TDEwLjIzNTEgMC4zNjE4MTZIMTIuMjMzNEw5Ljc1NzY3IDkuMDI2MjlINy45NzQ3OUw2LjMyNzEgMy4zNjEzOEg2LjI1OTUxTDQuNjE2MDUgOS4wMjYyOUgyLjgzMzE3WiIgZmlsbD0iIzlDQTJBNyIvPgo8cGF0aCBkPSJNMTMuMTE1MiA5LjAwOTY4VjAuMzQ1MjE1SDE2LjUyODlDMTcuMTg1MiAwLjM0NTIxNSAxNy43NDQzIDAuNDcwNzI5IDE4LjIwNjIgMC43MjE3NDNDMTguNjY4MSAwLjk2OTk1MSAxOS4wMjAxIDEuMzE1NDUgMTkuMjYyNCAxLjc1ODI3QzE5LjUwNzQgMi4xOTgyNiAxOS42MyAyLjcwNTk1IDE5LjYzIDMuMjgxMzJDMTkuNjMgMy44NTY2OSAxOS41MDYxIDQuMzY0MzggMTkuMjU4MSA0LjgwNDM3QzE5LjAxMDMgNS4yNDQzNiAxOC42NTEyIDUuNTg3MDUgMTguMTgwOCA1LjgzMjQzQzE3LjcxMzIgNi4wNzc4MSAxNy4xNDcxIDYuMjAwNSAxNi40ODI0IDYuMjAwNUgxNC4zMDY3VjQuNzMyNDVIMTYuMTg2N0MxNi41Mzg3IDQuNzMyNDUgMTYuODI4OSA0LjY3MTggMTcuMDU3IDQuNTUwNTJDMTcuMjg4IDQuNDI2NDMgMTcuNDU5OCA0LjI1NTc5IDE3LjU3MjUgNC4wMzg2MUMxNy42ODggMy44MTg2MiAxNy43NDU2IDMuNTY2MTkgMTcuNzQ1NiAzLjI4MTMyQzE3Ljc0NTYgMi45OTM2MyAxNy42ODggMi43NDI2MSAxNy41NzI1IDIuNTI4MjVDMTcuNDU5OCAyLjMxMTA4IDE3LjI4OCAyLjE0MzI2IDE3LjA1NyAyLjAyNDhDMTYuODI2MSAxLjkwMzUzIDE2LjUzMzIgMS44NDI4OCAxNi4xNzgzIDEuODQyODhIMTQuOTQ0NlY5LjAwOTY4SDEzLjExNTJaIiBmaWxsPSIjOUNBMkE3Ii8+CjxwYXRoIGQ9Ik00LjY4MzQ0IDEzLjk2OTRDNS43NTc3MiAxMy45Njk0IDYuNjI4NiAxMy4wOTg1IDYuNjI4NiAxMi4wMjQzQzYuNjI4NiAxMC45NSA1Ljc1NzcyIDEwLjA3OTEgNC42ODM0NCAxMC4wNzkxQzMuNjA5MTYgMTAuMDc5MSAyLjczODI4IDEwLjk1IDIuNzM4MjggMTIuMDI0M0MyLjczODI4IDEzLjA5ODUgMy42MDkxNiAxMy45Njk0IDQuNjgzNDQgMTMuOTY5NFoiIGZpbGw9IiM5Q0EyQTciLz4KPHBhdGggZD0iTTQuNjgzNDQgMTguOTk5N0M1Ljc1NzcyIDE4Ljk5OTcgNi42Mjg2IDE4LjEyODggNi42Mjg2IDE3LjA1NDVDNi42Mjg2IDE1Ljk4MDMgNS43NTc3MiAxNS4xMDk0IDQuNjgzNDQgMTUuMTA5NEMzLjYwOTE2IDE1LjEwOTQgMi43MzgyOCAxNS45ODAzIDIuNzM4MjggMTcuMDU0NUMyLjczODI4IDE4LjEyODggMy42MDkxNiAxOC45OTk3IDQuNjgzNDQgMTguOTk5N1oiIGZpbGw9IiM5Q0EyQTciLz4KPHBhdGggZD0iTTEzLjEyODkgMTguOTU0VjEwLjI4OTZIMTYuNTQyNkMxNy4xOTg5IDEwLjI4OTYgMTcuNzU4IDEwLjQxNSAxOC4yMTk5IDEwLjY2NkMxOC42ODE4IDEwLjkxNDIgMTkuMDMzOCAxMS4yNTk3IDE5LjI3NiAxMS43MDI1QzE5LjUyMTEgMTIuMTQyNSAxOS42NDM2IDEyLjY1MDIgMTkuNjQzNiAxMy4yMjU2QzE5LjY0MzYgMTMuODAxIDE5LjUxOTcgMTQuMzA4NyAxOS4yNzE5IDE0Ljc0ODdDMTkuMDI0IDE1LjE4ODYgMTguNjY0OSAxNS41MzEzIDE4LjE5NDUgMTUuNzc2N0MxNy43MjcgMTYuMDIyMSAxNy4xNjA5IDE2LjE0NDggMTYuNDk2MSAxNi4xNDQ4SDE0LjMyMDRWMTQuNjc2OEgxNi4yMDA0QzE2LjU1MjUgMTQuNjc2OCAxNi44NDI2IDE0LjYxNjEgMTcuMDcwNyAxNC40OTQ4QzE3LjMwMTcgMTQuMzcwNyAxNy40NzM1IDE0LjIwMDEgMTcuNTg2MSAxMy45ODI5QzE3LjcwMTcgMTMuNzYyOSAxNy43NTkzIDEzLjUxMDUgMTcuNzU5MyAxMy4yMjU2QzE3Ljc1OTMgMTIuOTM4IDE3LjcwMTcgMTIuNjg2OSAxNy41ODYxIDEyLjQ3MjVDMTcuNDczNSAxMi4yNTUzIDE3LjMwMTcgMTIuMDg3NiAxNy4wNzA3IDExLjk2OTFDMTYuODM5OCAxMS44NDc4IDE2LjU0NjggMTEuNzg3MiAxNi4xOTIgMTEuNzg3MkgxNC45NTgzVjE4Ljk1NEgxMy4xMjg5WiIgZmlsbD0iIzlDQTJBNyIvPgo8cGF0aCBkPSJNOC4wMjM1NyAxOC45NTJMOC4wMjM0NCAxMC4wNzkxSDkuODUyOEw5Ljg1MjkyIDE3LjIzNDRIMTIuMjA1NVYxOC45NTJIOC4wMjM1N1oiIGZpbGw9IiM5Q0EyQTciLz4KPC9zdmc+Cg==',
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
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=gdpr-policies-import' )); ?>" target="_blank" class="data-req-export-button"><?php esc_html_e( 'Import From CSV', 'gdpr-cookie-consent' ); ?></a>
							<a href="<?php echo esc_url_raw( admin_url( 'post-new.php?post_type=gdprpolicies' ) ); ?>" target="_blank" class="data-req-export-button"><?php esc_html_e( 'Add New', 'gdpr-cookie-consent' ); ?></a>
						</div>
						<div class="policy-data-search-log"> 
							<?php $policy_data->search_box( __( 'Search Logs', 'gdpr-cookie-consent' ), 'gdpr-cookie-consent' ); ?> 
						</div>
					</div>
					<span>
							<?php esc_html_e('Policy Data shows the third party 3rd Party cookie table.','gdpr-cookie-consent') ?>
							<a href="https://wplegalpages.com/docs/wp-cookie-consent/settings/policy-data/" target="_blank">Learn more here.</a>
						</span>
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
			$prev_gdpr_option['opacity']                    = '1';
			$prev_gdpr_option['template']                   = 'default';
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
			$prev_gdpr_option['is_script_dependency_on']      = false;
			$prev_gdpr_option['header_dependency']			  = '';
			$prev_gdpr_option['footer_dependency']			  = '';
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

		//Script dependency
		$header_dependency_list = array('Body Scripts', 'Footer Scripts');
		$footer_dependency_list = array('Header Scripts', 'Body Scripts');

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
		$gcm_permission_options = array();
		$gcm_permission_options[0] = array(
			'label' => 'Granted',
			'code' => true,
		);
		$gcm_permission_options[1] = array(
			'label' => 'Denied',
			'code' => false,
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
				'templates'     				   => $this -> templates_json,
				'default_template_json'			   => get_option('gdpr_default_template_object'),
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
				'accept_action_options'            => $accept_action_options,
				'accept_button_as_options'         => $accept_button_as_options,
				'gcm_permission_options'		   => $gcm_permission_options,
				'open_url_options'                 => $open_url_options,
				'widget_position_options'          => $widget_position_options,
				'decline_action_options'           => $decline_action_options,
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
				//dependency list
				'header_dependency_list'		   => $header_dependency_list,
				'footer_dependency_list'		   => $footer_dependency_list,
				// for sites.
				'list_of_sites'                    => is_multisite() ? $list_of_sites : null,
				'ab_options'                       => $ab_options,
				'geo_options'                      => $geo_options,
				'is_user_connected'                => $is_user_connected,
				'gdpr_no_of_page_scan'             => get_option( 'gdpr_no_of_page_scan' ),
				// for countries.
				'list_of_countries'                => $list_of_countries,
				'is_usage_tracking_allowed'        => get_option( 'gdpr_usage_tracking_allowed' ),
			)
		);
		wp_enqueue_script( $this->plugin_name . '-main' );
		require_once plugin_dir_path( __FILE__ ) . 'gdpr-cookie-consent-admin-settings.php';
	}
	
	/**
	 * Advanced Settings Page
	 * 
	 * @since 4.0.0
	 */
	public function gdpr_cookie_consent_advanced_settings() {
		$is_user_connected = $this->settings->is_connected();
		$api_user_plan = $this->settings->get_plan();

		$is_pro_active            = get_option( 'wpl_pro_active', false );
		if ( $is_pro_active ) {
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
				$the_options = apply_filters( 'gdpr_module_after_save_settings', $the_options );
				update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
				echo '<div class="updated"><p><strong>' . esc_attr__( 'Settings Updated.', 'gdpr-cookie-consent' ) . '</strong></p></div>';
			}
		}
		if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) ) === 'xmlhttprequest' ) {
			exit();
		}

		require_once plugin_dir_path( __FILE__ ) . 'gdpr-cookie-consent-advanced-settings.php';
	}

	/**
	 * AB Testing Page
	 * 
	 * @since 4.0.0
	 */
	public function gdpr_cookie_consent_abtesting_settings() {
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

		require_once plugin_dir_path( __FILE__ ) . 'gdpr-cookie-consent-abtesting-settings.php';
	}

	/**
	 * Script Blocker Page
	 * 
	 * @since 4.0.0
	 */
	public function gdpr_cookie_consent_script_blocker_settings() {
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
				$the_options = apply_filters( 'gdpr_module_after_save_settings', $the_options );
				update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
				echo '<div class="updated"><p><strong>' . esc_attr__( 'Settings Updated.', 'gdpr-cookie-consent' ) . '</strong></p></div>';
			}
		}
		if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) ) === 'xmlhttprequest' ) {
			exit();
		}

		require_once plugin_dir_path( __FILE__ ) . 'gdpr-cookie-consent-script-blocker-settings.php';
	}

	/**
	 * Cookie Manager Page
	 * 
	 * @since 4.0.0
	 */
	public function gdpr_cookie_consent_cookie_manager_settings() {
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
		
		require_once plugin_dir_path( __FILE__ ) . 'gdpr-cookie-consent-cookie-manager-settings.php';
	}

		/**
	 * Languages Page
	 * 
	 * @since 4.0.0
	 */
	public function gdpr_cookie_consent_language_settings() {
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

		require_once plugin_dir_path( __FILE__ ) . 'gdpr-cookie-consent-language-settings.php';
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
			$content .= '<p>Our website uses cookies from trusted third-party services to improve functionality, analytics, and advertising. Below is a list of third-party cookies that may be set in your browser:</p>';
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
			$the_options['cookie_usage_for']      = isset( $_POST['gcc-gdpr-policy'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-gdpr-policy'] ) ) : 'gdpr';
			$the_options['cookie_bar_as']         = isset( $_POST['show-cookie-as'] ) ? sanitize_text_field( wp_unslash( $_POST['show-cookie-as'] ) ) : 'banner';
			$the_options['button_accept_is_on']   = isset( $_POST['gcc-cookie-accept-enable'] ) && ( true === $_POST['gcc-cookie-accept-enable'] || 'true' === $_POST['gcc-cookie-accept-enable'] ) ? 'true' : 'false';
			$the_options['button_accept_all_is_on']   = isset( $_POST['gcc-cookie-accept-all-enable'] ) && ( true === $_POST['gcc-cookie-accept-all-enable'] || 'true' === $_POST['gcc-cookie-accept-all-enable'] ) ? 'true' : 'false';
			$the_options['button_decline_is_on']  = isset( $_POST['gcc-cookie-decline-enable'] ) && ( true === $_POST['gcc-cookie-decline-enable'] || 'true' === $_POST['gcc-cookie-decline-enable'] ) ? 'true' : 'false';
			$the_options['button_settings_is_on'] = isset( $_POST['gcc-cookie-settings-enable'] ) && ( true === $_POST['gcc-cookie-settings-enable'] || 'true' === $_POST['gcc-cookie-settings-enable'] ) ? 'true' : 'false';
			// revoke consent text color.
			$the_options['button_accept_as_button']        	    = 'true';
			$the_options['button_readmore_as_button']           = 'false';
			$the_options['button_readmore_as_button1']          = 'false';
			$the_options['button_readmore_as_button2']          = 'false';
			$the_options['button_decline_as_button']            = 'true';
			$the_options['button_settings_as_button']           = 'true';
			$the_options['button_accept_all_as_button']         = 'true';
			$the_options['button_revoke_consent_text_color']       = isset( $_POST['gcc-revoke-consent-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-text-color'] ) ) : '';
			$the_options['button_revoke_consent_background_color'] = isset( $_POST['gcc-revoke-consent-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-background-color'] ) ) : '';
			if ( isset( $_POST['gcc-revoke-consent-text-color1'] ) ) {
				$the_options['button_revoke_consent_text_color1'] = sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-text-color1'] ) );
			}
			if ( isset( $_POST['gcc-revoke-consent-background-color1'] ) ) {
				$the_options['button_revoke_consent_background_color1'] = sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-background-color1'] ) );
			}
			if ( isset( $_POST['gcc-revoke-consent-text-color2'] ) ) {
				$the_options['button_revoke_consent_text_color2'] = sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-text-color2'] ) );
			}
			if ( isset( $_POST['gcc-revoke-consent-background-color2'] ) ) {
				$the_options['button_revoke_consent_background_color2'] = sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-background-color2'] ) );
			}
			$the_options['notify_position_vertical']           = isset( $_POST['gcc-gdpr-cookie-position'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-gdpr-cookie-position'] ) ) : 'bottom';
			$the_options['notify_position_horizontal']         = isset( $_POST['gcc-gdpr-cookie-widget-position'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-gdpr-cookie-widget-position'] ) ) : 'left';
			$the_options['background']                         = isset( $_POST['gdpr-cookie-bar-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-color'] ) ) : '#ffffff';
			$the_options['text']                               = isset( $_POST['gdpr-cookie-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-text-color'] ) ) : '#000000';
			$the_options['opacity']                            = isset( $_POST['gdpr-cookie-bar-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-opacity'] ) ) : '1';
			$the_options['background_border_width']            = isset( $_POST['gdpr-cookie-bar-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-border-width'] ) ) : '0';
			$the_options['background_border_style']            = isset( $_POST['gdpr-cookie-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-border-style'] ) ) : 'none';
			$the_options['background_border_color']            = isset( $_POST['gdpr-cookie-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-border-color'] ) ) : '#ffffff';
			$the_options['background_border_radius']           = isset( $_POST['gdpr-cookie-bar-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-border-radius'] ) ) : '0';
			$the_options['font_family']                        = isset( $_POST['gdpr-cookie-font'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-font'] ) ) : 'inherit';
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
			$the_options['button_accept_button_opacity']       = isset( $_POST['gdpr-cookie-accept-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-opacity'] ) ) : '1';
			$the_options['button_accept_button_border_style']  = isset( $_POST['gdpr-cookie-accept-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-style'] ) ) : 'none';
			$the_options['button_accept_button_border_width']  = isset( $_POST['gdpr-cookie-accept-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-width'] ) ) : '0';
			$the_options['button_accept_button_border_radius'] = isset( $_POST['gdpr-cookie-accept-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-radius'] ) ) : '0';
			$the_options['button_accept_link_color']           = isset( $_POST['gdpr-cookie-accept-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-text-color'] ) ) : '#ffffff';
			$the_options['button_readmore_link_color']           = isset( $_POST['gcc-readmore-link-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-link-color'] ) ) : '#359bf5';
			$the_options['button_readmore_button_color']         = isset( $_POST['gcc-readmore-button-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-color'] ) ) : '#000000';
			$the_options['button_readmore_button_opacity']       = isset( $_POST['gcc-readmore-button-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-opacity'] ) ) : '1';
			$the_options['button_readmore_button_border_style']  = isset( $_POST['gcc-readmore-button-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-style'] ) ) : '1';
			$the_options['button_readmore_button_border_width']  = isset( $_POST['gcc-readmore-button-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-width'] ) ) : '0';
			$the_options['button_readmore_button_border_color']  = isset( $_POST['gcc-readmore-button-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-color'] ) ) : '#000000';
			$the_options['button_readmore_button_border_radius'] = isset( $_POST['gcc-readmore-button-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-radius'] ) ) : '0';
			$the_options['button_decline_button_opacity']         = isset( $_POST['gdpr-cookie-decline-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-opacity'] ) ) : '1';
			$the_options['button_decline_button_border_radius']   = isset( $_POST['gdpr-cookie-decline-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-radius'] ) ) : '0';
			$the_options['button_settings_button_opacity']        = isset( $_POST['gdpr-cookie-settings-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-opacity'] ) ) : '1';
			$the_options['button_settings_button_border_radius']  = isset( $_POST['gdpr-cookie-settings-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-radius'] ) ) : '0';
			$the_options['button_confirm_link_color']             = isset( $_POST['gdpr-cookie-confirm-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-text-color'] ) ) : '#ffffff';
			$the_options['button_confirm_button_color']           = isset( $_POST['gdpr-cookie-confirm-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-background-color'] ) ) : '#18a300';
			$the_options['button_confirm_button_opacity']         = isset( $_POST['gdpr-cookie-confirm-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-opacity'] ) ) : '1';
			$the_options['button_confirm_button_border_style']    = isset( $_POST['gdpr-cookie-confirm-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-style'] ) ) : 'none';
			$the_options['button_confirm_button_border_color']    = isset( $_POST['gdpr-cookie-confirm-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-color'] ) ) : '#18a300';
			$the_options['button_confirm_button_border_width']    = isset( $_POST['gdpr-cookie-confirm-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-width'] ) ) : '0';
			$the_options['button_confirm_button_border_radius']   = isset( $_POST['gdpr-cookie-confirm-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-radius'] ) ) : '0';
			$the_options['button_cancel_link_color']              = isset( $_POST['gdpr-cookie-cancel-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-text-color'] ) ) : '#ffffff';
			$the_options['button_cancel_button_color']            = isset( $_POST['gdpr-cookie-cancel-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-background-color'] ) ) : '#333333';
			$the_options['button_cancel_button_opacity']          = isset( $_POST['gdpr-cookie-cancel-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-opacity'] ) ) : '1';
			$the_options['button_cancel_button_border_style']     = isset( $_POST['gdpr-cookie-cancel-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-style'] ) ) : 'none';
			$the_options['button_cancel_button_border_color']     = isset( $_POST['gdpr-cookie-cancel-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-color'] ) ) : '#333333';
			$the_options['button_cancel_button_border_width']     = isset( $_POST['gdpr-cookie-cancel-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-width'] ) ) : '0';
			$the_options['button_cancel_button_border_radius']    = isset( $_POST['gdpr-cookie-cancel-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-radius'] ) ) : '0';
			$the_options['button_donotsell_link_color']           = isset( $_POST['gdpr-cookie-opt-out-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-opt-out-text-color'] ) ) : '#359bf5';
			$the_options['button_accept_all_link_color']          = isset( $_POST['gdpr-cookie-accept-all-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-text-color'] ) ) : '#ffffff';
			$the_options['button_accept_all_button_color']        = isset( $_POST['gdpr-cookie-accept-all-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-background-color'] ) ) : '#18a300';
			$the_options['button_accept_all_btn_border_style']    = isset( $_POST['gdpr-cookie-accept-all-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-style'] ) ) : 'none';
			$the_options['button_accept_all_btn_border_color']    = isset( $_POST['gdpr-cookie-accept-all-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-color'] ) ) : '#18a300';
			$the_options['button_accept_all_btn_opacity']         = isset( $_POST['gdpr-cookie-accept-all-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-opacity'] ) ) : '1';
			$the_options['button_accept_all_btn_border_width']    = isset( $_POST['gdpr-cookie-accept-all-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-width'] ) ) : '0';
			$the_options['button_accept_all_btn_border_radius']   = isset( $_POST['gdpr-cookie-accept-all-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-radius'] ) ) : '0';
			$the_options['button_revoke_consent_text_color']       = isset( $_POST['gcc-revoke-consent-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-text-color'] ) ) : '';
			$the_options['button_revoke_consent_background_color'] = isset( $_POST['gcc-revoke-consent-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-background-color'] ) ) : '';
			
			$the_options['button_accept_as_button1']        	    = 'true';
			$the_options['button_decline_as_button1']            = 'true';
			$the_options['button_settings_as_button1']           = 'true';
			$the_options['button_accept_all_as_button1']         = 'true';
			$the_options['cookie_bar_color1']                  = isset( $_POST['gdpr-cookie-bar-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-color'] ) ) : '#ffffff';
			$the_options['cookie_text_color1']                 = isset( $_POST['gdpr-cookie-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-text-color'] ) ) : '#000000';
			$the_options['cookie_bar_opacity1']                = isset( $_POST['gdpr-cookie-bar-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-opacity'] ) ) : '1';
			$the_options['cookie_bar_border_width1']           = isset( $_POST['gdpr-cookie-bar-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-border-width'] ) ) : '0';
			$the_options['border_style1']			           = isset( $_POST['gdpr-cookie-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-border-style'] ) ) : 'none';
			$the_options['cookie_border_color1']               = isset( $_POST['gdpr-cookie-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-border-color'] ) ) : '#ffffff';
			$the_options['cookie_bar_border_radius1']          = isset( $_POST['gdpr-cookie-bar-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-border-radius'] ) ) : '0';
			$the_options['cookie_font1']                       = isset( $_POST['gdpr-cookie-font'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-font'] ) ) : 'inherit';
			$the_options['button_accept_is_on1']			   = isset( $_POST['gcc-cookie-accept-enable'] ) && ( true === $_POST['gcc-cookie-accept-enable'] || 'true' === $_POST['gcc-cookie-accept-enable'] ) ? 'true' : 'false';
			$the_options['button_accept_all_is_on1']			   = isset( $_POST['gcc-cookie-accept-all-enable'] ) && ( true === $_POST['gcc-cookie-accept-all-enable'] || 'true' === $_POST['gcc-cookie-accept-all-enable'] ) ? 'true' : 'false';
			$the_options['button_decline_is_on1']			       = isset( $_POST['gcc-cookie-decline-enable'] ) && ( true === $_POST['gcc-cookie-decline-enable'] || 'true' === $_POST['gcc-cookie-decline-enable'] ) ? 'true' : 'false';
			$the_options['button_settings_is_on1']			       = isset( $_POST['gcc-cookie-settings-enable'] ) && ( true === $_POST['gcc-cookie-settings-enable'] || 'true' === $_POST['gcc-cookie-settings-enable'] ) ? 'true' : 'false';
			$the_options['button_accept_button_color1']            = isset( $_POST['gdpr-cookie-accept-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-background-color'] ) ) : '#18a300';
			$the_options['button_accept_button_border_color1']     = isset( $_POST['gdpr-cookie-accept-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-color'] ) ) : '#18a300';
			$the_options['button_decline_link_color1']             = isset( $_POST['gdpr-cookie-decline-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-text-color'] ) ) : '#ffffff';
			$the_options['button_decline_button_border_color1']    = isset( $_POST['gdpr-cookie-decline-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-color'] ) ) : '#333333';
			$the_options['button_settings_link_color1']            = isset( $_POST['gdpr-cookie-settings-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-text-color'] ) ) : '#ffffff';
			$the_options['button_settings_button_border_color1']   = isset( $_POST['gdpr-cookie-settings-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-color'] ) ) : '#333333';
			$the_options['button_settings_button_color1']          = isset( $_POST['gdpr-cookie-settings-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-background-color'] ) ) : '#333333';
			$the_options['button_decline_button_color1']           = isset( $_POST['gdpr-cookie-decline-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-background-color'] ) ) : '#333333';
			$the_options['button_decline_button_border_style1']    = isset( $_POST['gdpr-cookie-decline-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-style'] ) ) : 'none';
			$the_options['button_decline_button_border_width1']    = isset( $_POST['gdpr-cookie-decline-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-width'] ) ) : '0';
			$the_options['button_settings_button_border_style1']   = isset( $_POST['gdpr-cookie-settings-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-style'] ) ) : 'none';
			$the_options['button_settings_button_border_width1']   = isset( $_POST['gdpr-cookie-settings-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-width'] ) ) : '0';
			$the_options['button_accept_button_opacity1']       = isset( $_POST['gdpr-cookie-accept-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-opacity'] ) ) : '1';
			$the_options['button_accept_button_border_style1']  = isset( $_POST['gdpr-cookie-accept-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-style'] ) ) : 'none';
			$the_options['button_accept_button_border_width1']  = isset( $_POST['gdpr-cookie-accept-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-width'] ) ) : '0';
			$the_options['button_accept_button_border_radius1'] = isset( $_POST['gdpr-cookie-accept-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-radius'] ) ) : '0';
			$the_options['button_accept_link_color1']           = isset( $_POST['gdpr-cookie-accept-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-text-color'] ) ) : '#ffffff';
			$the_options['button_readmore_link_color1']           = isset( $_POST['gcc-readmore-link-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-link-color1'] ) ) : '#359bf5';
			$the_options['button_readmore_button_color1']         = isset( $_POST['gcc-readmore-button-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-color1'] ) ) : '#000000';
			$the_options['button_readmore_button_opacity1']       = isset( $_POST['gcc-readmore-button-opacity1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-opacity1'] ) ) : '1';
			$the_options['button_readmore_button_border_style1']  = isset( $_POST['gcc-readmore-button-border-style1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-style1'] ) ) : '1';
			$the_options['button_readmore_button_border_width1']  = isset( $_POST['gcc-readmore-button-border-width1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-width1'] ) ) : '0';
			$the_options['button_readmore_button_border_color1']  = isset( $_POST['gcc-readmore-button-border-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-color1'] ) ) : '#000000';
			$the_options['button_readmore_button_border_radius1'] = isset( $_POST['gcc-readmore-button-border-radius1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-radius1'] ) ) : '0';
			$the_options['button_readmore_button_size1']          = isset( $_POST['gcc-readmore-button-size1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-size1'] ) ) : 'medium';
			$the_options['button_decline_button_opacity1']         = isset( $_POST['gdpr-cookie-decline-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-opacity'] ) ) : '1';
			$the_options['button_decline_button_border_radius1']   = isset( $_POST['gdpr-cookie-decline-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-radius'] ) ) : '0';
			$the_options['button_settings_button_opacity1']        = isset( $_POST['gdpr-cookie-settings-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-opacity'] ) ) : '1';
			$the_options['button_settings_button_border_radius1']  = isset( $_POST['gdpr-cookie-settings-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-radius'] ) ) : '0';
			$the_options['button_confirm_link_color1']             = isset( $_POST['gdpr-cookie-confirm-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-text-color'] ) ) : '#ffffff';
			$the_options['button_confirm_button_color1']           = isset( $_POST['gdpr-cookie-confirm-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-background-color'] ) ) : '#18a300';
			$the_options['button_confirm_button_opacity1']         = isset( $_POST['gdpr-cookie-confirm-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-opacity'] ) ) : '1';
			$the_options['button_confirm_button_border_style1']    = isset( $_POST['gdpr-cookie-confirm-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-style'] ) ) : 'none';
			$the_options['button_confirm_button_border_color1']    = isset( $_POST['gdpr-cookie-confirm-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-color'] ) ) : '#18a300';
			$the_options['button_confirm_button_border_width1']    = isset( $_POST['gdpr-cookie-confirm-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-width'] ) ) : '0';
			$the_options['button_confirm_button_border_radius1']   = isset( $_POST['gdpr-cookie-confirm-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-radius'] ) ) : '0';
			$the_options['button_cancel_link_color1']              = isset( $_POST['gdpr-cookie-cancel-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-text-color'] ) ) : '#ffffff';
			$the_options['button_cancel_button_color1']            = isset( $_POST['gdpr-cookie-cancel-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-background-color'] ) ) : '#333333';
			$the_options['button_cancel_button_opacity1']          = isset( $_POST['gdpr-cookie-cancel-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-opacity'] ) ) : '1';
			$the_options['button_cancel_button_border_style1']     = isset( $_POST['gdpr-cookie-cancel-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-style'] ) ) : 'none';
			$the_options['button_cancel_button_border_color1']     = isset( $_POST['gdpr-cookie-cancel-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-color'] ) ) : '#333333';
			$the_options['button_cancel_button_border_width1']     = isset( $_POST['gdpr-cookie-cancel-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-width'] ) ) : '0';
			$the_options['button_cancel_button_border_radius1']    = isset( $_POST['gdpr-cookie-cancel-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-radius'] ) ) : '0';
			$the_options['button_donotsell_link_color1']           = isset( $_POST['gdpr-cookie-opt-out-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-opt-out-text-color'] ) ) : '#359bf5';
			$the_options['button_accept_all_link_color1']          = isset( $_POST['gdpr-cookie-accept-all-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-text-color'] ) ) : '#ffffff';
			$the_options['button_accept_all_button_color1']        = isset( $_POST['gdpr-cookie-accept-all-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-background-color'] ) ) : '#18a300';
			$the_options['button_accept_all_btn_border_style1']    = isset( $_POST['gdpr-cookie-accept-all-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-style'] ) ) : 'none';
			$the_options['button_accept_all_btn_border_color1']    = isset( $_POST['gdpr-cookie-accept-all-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-color'] ) ) : '#18a300';
			$the_options['button_accept_all_btn_opacity1']         = isset( $_POST['gdpr-cookie-accept-all-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-opacity'] ) ) : '1';
			$the_options['button_accept_all_btn_border_width1']    = isset( $_POST['gdpr-cookie-accept-all-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-width'] ) ) : '0';
			$the_options['button_accept_all_btn_border_radius1']   = isset( $_POST['gdpr-cookie-accept-all-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-radius'] ) ) : '0';
			 if ( isset( $_POST['gcc-revoke-consent-text-color1'] ) ) {
				$the_options['button_revoke_consent_text_color1'] = sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-text-color1'] ) );
			}
			if ( isset( $_POST['gcc-revoke-consent-background-color1'] ) ) {
				$the_options['button_revoke_consent_background_color1'] = sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-background-color1'] ) );
			}
			$the_options['button_accept_as_button2']        	    = 'true';
			$the_options['button_decline_as_button2']            = 'true';
			$the_options['button_settings_as_button2']           = 'true';
			$the_options['button_accept_all_as_button2']         = 'true';
			$the_options['cookie_bar_color2']                  = isset( $_POST['gdpr-cookie-bar-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-color'] ) ) : '#ffffff';
			$the_options['cookie_text_color2']                 = isset( $_POST['gdpr-cookie-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-text-color'] ) ) : '#000000';
			$the_options['cookie_bar_opacity2']                = isset( $_POST['gdpr-cookie-bar-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-opacity'] ) ) : '1';
			$the_options['cookie_bar_border_width2']           = isset( $_POST['gdpr-cookie-bar-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-border-width'] ) ) : '0';
			$the_options['border_style2']			           = isset( $_POST['gdpr-cookie-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-border-style'] ) ) : 'none';
			$the_options['cookie_border_color2']               = isset( $_POST['gdpr-cookie-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-border-color'] ) ) : '#ffffff';
			$the_options['cookie_bar_border_radius2']          = isset( $_POST['gdpr-cookie-bar-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-border-radius'] ) ) : '0';
			$the_options['cookie_font2']                       = isset( $_POST['gdpr-cookie-font'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-font'] ) ) : 'inherit';
			$the_options['button_accept_is_on2']			   = isset( $_POST['gcc-cookie-accept-enable'] ) && ( true === $_POST['gcc-cookie-accept-enable'] || 'true' === $_POST['gcc-cookie-accept-enable'] ) ? 'true' : 'false';
			$the_options['button_accept_all_is_on2']			   = isset( $_POST['gcc-cookie-accept-all-enable'] ) && ( true === $_POST['gcc-cookie-accept-all-enable'] || 'true' === $_POST['gcc-cookie-accept-all-enable'] ) ? 'true' : 'false';
			$the_options['button_decline_is_on2']			       = isset( $_POST['gcc-cookie-decline-enable'] ) && ( true === $_POST['gcc-cookie-decline-enable'] || 'true' === $_POST['gcc-cookie-decline-enable'] ) ? 'true' : 'false';
			$the_options['button_settings_is_on2']			       = isset( $_POST['gcc-cookie-settings-enable'] ) && ( true === $_POST['gcc-cookie-settings-enable'] || 'true' === $_POST['gcc-cookie-settings-enable'] ) ? 'true' : 'false';
			$the_options['button_accept_button_color2']         = isset( $_POST['gdpr-cookie-accept-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-background-color'] ) ) : '#18a300';
			$the_options['button_accept_button_border_color2']  = isset( $_POST['gdpr-cookie-accept-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-color'] ) ) : '#18a300';
			$the_options['button_decline_link_color2']             = isset( $_POST['gdpr-cookie-decline-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-text-color'] ) ) : '#ffffff';
			$the_options['button_decline_button_border_color2']    = isset( $_POST['gdpr-cookie-decline-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-color'] ) ) : '#333333';
			$the_options['button_settings_link_color2']            = isset( $_POST['gdpr-cookie-settings-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-text-color'] ) ) : '#ffffff';
			$the_options['button_settings_button_border_color2']   = isset( $_POST['gdpr-cookie-settings-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-color'] ) ) : '#333333';
			$the_options['button_settings_button_color2']          = isset( $_POST['gdpr-cookie-settings-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-background-color'] ) ) : '#333333';
			$the_options['button_decline_button_color2']           = isset( $_POST['gdpr-cookie-decline-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-background-color'] ) ) : '#333333';
			$the_options['button_decline_button_border_style2']    = isset( $_POST['gdpr-cookie-decline-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-style'] ) ) : 'none';
			$the_options['button_decline_button_border_width2']    = isset( $_POST['gdpr-cookie-decline-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-width'] ) ) : '0';
			$the_options['button_settings_button_border_style2']   = isset( $_POST['gdpr-cookie-settings-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-style'] ) ) : 'none';
			$the_options['button_settings_button_border_width2']   = isset( $_POST['gdpr-cookie-settings-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-width'] ) ) : '0';
			$the_options['button_accept_button_opacity2']       = isset( $_POST['gdpr-cookie-accept-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-opacity'] ) ) : '1';
			$the_options['button_accept_button_border_style2']  = isset( $_POST['gdpr-cookie-accept-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-style'] ) ) : 'none';
			$the_options['button_accept_button_border_width2']  = isset( $_POST['gdpr-cookie-accept-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-width'] ) ) : '0';
			$the_options['button_accept_button_border_radius2'] = isset( $_POST['gdpr-cookie-accept-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-border-radius'] ) ) : '0';
			$the_options['button_accept_link_color2']           = isset( $_POST['gdpr-cookie-accept-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-text-color'] ) ) : '#ffffff';
			$the_options['button_readmore_link_color2']           = isset( $_POST['gcc-readmore-link-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-link-color2'] ) ) : '#359bf5';
			$the_options['button_readmore_button_color2']         = isset( $_POST['gcc-readmore-button-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-color2'] ) ) : '#000000';
			$the_options['button_readmore_button_opacity2']       = isset( $_POST['gcc-readmore-button-opacity2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-opacity2'] ) ) : '1';
			$the_options['button_readmore_button_border_style2']  = isset( $_POST['gcc-readmore-button-border-style2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-style2'] ) ) : '1';
			$the_options['button_readmore_button_border_width2']  = isset( $_POST['gcc-readmore-button-border-width2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-width2'] ) ) : '0';
			$the_options['button_readmore_button_border_color2']  = isset( $_POST['gcc-readmore-button-border-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-color2'] ) ) : '#000000';
			$the_options['button_readmore_button_border_radius2'] = isset( $_POST['gcc-readmore-button-border-radius2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-radius2'] ) ) : '0';
			$the_options['button_readmore_button_size2']          = isset( $_POST['gcc-readmore-button-size2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-size'] ) ) : 'medium';
			$the_options['button_decline_button_opacity2']         = isset( $_POST['gdpr-cookie-decline-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-opacity'] ) ) : '1';
			$the_options['button_decline_button_border_radius2']   = isset( $_POST['gdpr-cookie-decline-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-radius'] ) ) : '0';
			$the_options['button_settings_button_opacity2']        = isset( $_POST['gdpr-cookie-settings-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-opacity'] ) ) : '1';
			$the_options['button_settings_button_border_radius2']  = isset( $_POST['gdpr-cookie-settings-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-radius'] ) ) : '0';
			$the_options['button_confirm_link_color2']             = isset( $_POST['gdpr-cookie-confirm-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-text-color'] ) ) : '#ffffff';
			$the_options['button_confirm_button_color2']           = isset( $_POST['gdpr-cookie-confirm-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-background-color'] ) ) : '#18a300';
			$the_options['button_confirm_button_opacity2']         = isset( $_POST['gdpr-cookie-confirm-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-opacity'] ) ) : '1';
			$the_options['button_confirm_button_border_style2']    = isset( $_POST['gdpr-cookie-confirm-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-style'] ) ) : 'none';
			$the_options['button_confirm_button_border_color2']    = isset( $_POST['gdpr-cookie-confirm-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-color'] ) ) : '#18a300';
			$the_options['button_confirm_button_border_width2']    = isset( $_POST['gdpr-cookie-confirm-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-width'] ) ) : '0';
			$the_options['button_confirm_button_border_radius2']   = isset( $_POST['gdpr-cookie-confirm-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-radius'] ) ) : '0';
			$the_options['button_cancel_link_color2']              = isset( $_POST['gdpr-cookie-cancel-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-text-color'] ) ) : '#ffffff';
			$the_options['button_cancel_button_color2']            = isset( $_POST['gdpr-cookie-cancel-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-background-color'] ) ) : '#333333';
			$the_options['button_cancel_button_opacity2']          = isset( $_POST['gdpr-cookie-cancel-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-opacity'] ) ) : '1';
			$the_options['button_cancel_button_border_style2']     = isset( $_POST['gdpr-cookie-cancel-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-style'] ) ) : 'none';
			$the_options['button_cancel_button_border_color2']     = isset( $_POST['gdpr-cookie-cancel-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-color'] ) ) : '#333333';
			$the_options['button_cancel_button_border_width2']     = isset( $_POST['gdpr-cookie-cancel-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-width'] ) ) : '0';
			$the_options['button_cancel_button_border_radius2']    = isset( $_POST['gdpr-cookie-cancel-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-radius'] ) ) : '0';
			$the_options['button_donotsell_link_color2']           = isset( $_POST['gdpr-cookie-opt-out-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-opt-out-text-color'] ) ) : '#359bf5';
			$the_options['button_accept_all_link_color2']          = isset( $_POST['gdpr-cookie-accept-all-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-text-color'] ) ) : '#ffffff';
			$the_options['button_accept_all_button_color2']        = isset( $_POST['gdpr-cookie-accept-all-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-background-color'] ) ) : '#18a300';
			$the_options['button_accept_all_btn_border_style2']    = isset( $_POST['gdpr-cookie-accept-all-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-style'] ) ) : 'none';
			$the_options['button_accept_all_btn_border_color2']    = isset( $_POST['gdpr-cookie-accept-all-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-color'] ) ) : '#18a300';
			$the_options['button_accept_all_btn_opacity2']         = isset( $_POST['gdpr-cookie-accept-all-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-opacity'] ) ) : '1';
			$the_options['button_accept_all_btn_border_width2']    = isset( $_POST['gdpr-cookie-accept-all-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-width'] ) ) : '0';
			$the_options['button_accept_all_btn_border_radius2']   = isset( $_POST['gdpr-cookie-accept-all-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-all-border-radius'] ) ) : '0';
			if ( isset( $_POST['gcc-revoke-consent-text-color2'] ) ) {
				$the_options['button_revoke_consent_text_color2'] = sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-text-color2'] ) );
			}
			if ( isset( $_POST['gcc-revoke-consent-background-color2'] ) ) {
				$the_options['button_revoke_consent_background_color2'] = sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-background-color2'] ) );
			}
			$the_options['multiple_legislation_cookie_bar_color1'] =  isset( $_POST['gdpr-cookie-bar-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-color'] ) ) : '#ffffff';
			$the_options['multiple_legislation_cookie_bar_color2'] =  isset( $_POST['gdpr-cookie-bar-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-color'] ) ) : '#ffffff';
			$the_options['multiple_legislation_cookie_bar_opacity1'] = isset( $_POST['gdpr-cookie-bar-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-opacity'] ) ) : '1';
			$the_options['multiple_legislation_cookie_bar_opacity2'] = isset( $_POST['gdpr-cookie-bar-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-opacity'] ) ) : '1';
			$the_options['multiple_legislation_cookie_text_color1'] =  isset( $_POST['gdpr-cookie-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-text-color'] ) ) : '#000000';
			$the_options['multiple_legislation_cookie_text_color2'] =  isset( $_POST['gdpr-cookie-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-text-color'] ) ) : '#000000';
			$the_options['multiple_legislation_border_style1'] = isset( $_POST['gdpr-cookie-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-border-style'] ) ) : 'none';
			$the_options['multiple_legislation_border_style2'] = isset( $_POST['gdpr-cookie-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-border-style'] ) ) : 'none';
			$the_options['multiple_legislation_cookie_bar_border_width1'] = isset( $_POST['gdpr-cookie-bar-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-border-width'] ) ) : '0';
			$the_options['multiple_legislation_cookie_bar_border_width2'] = isset( $_POST['gdpr-cookie-bar-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-border-width'] ) ) : '0';
			$the_options['multiple_legislation_cookie_border_color1'] = isset( $_POST['gdpr-cookie-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-border-color'] ) ) : '#ffffff';
			$the_options['multiple_legislation_cookie_border_color2'] = isset( $_POST['gdpr-cookie-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-border-color'] ) ) : '#ffffff';
			$the_options['multiple_legislation_cookie_bar_border_radius1'] =  isset( $_POST['gdpr-cookie-bar-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-border-radius'] ) ) : '0';
			$the_options['multiple_legislation_cookie_bar_border_radius2'] =  isset( $_POST['gdpr-cookie-bar-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-border-radius'] ) ) : '0';
			$the_options['multiple_legislation_cookie_font1'] =  isset( $_POST['gdpr-cookie-font'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-font'] ) ) : 'inherit';
			$the_options['multiple_legislation_cookie_font2'] =  isset( $_POST['gdpr-cookie-font'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-font'] ) ) : 'inherit';
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
			$the_options['data_reqs_on'] = isset( $_POST['gcc-data_reqs'] ) && ( true === $_POST['gcc-data_reqs'] || 'true' === $_POST['gcc-data_reqs'] ) ? 'true' : 'false';
			
			if ( ! get_option( 'wpl_pro_active' ) ) {
				$the_options['is_script_blocker_on'] = isset( $_POST['gcc-script-blocker-on'] ) && ( true === $_POST['gcc-script-blocker-on'] || 'true' === $_POST['gcc-script-blocker-on'] ) ? 'true' : 'false';
				$the_options['is_script_dependency_on'] = isset( $_POST['gcc-script-dependency-on'] ) && ( true === $_POST['gcc-script-dependency-on'] || 'true' === $_POST['gcc-script-dependency-on'] ) ? 'true' : 'false';
				$the_options['header_dependency'] = isset( $_POST['gcc-header-dependency'] )? sanitize_text_field( wp_unslash( $_POST['gcc-header-dependency'] ) ): '';
				$the_options['footer_dependency'] = isset( $_POST['gcc-footer-dependency'] )? sanitize_text_field( wp_unslash( $_POST['gcc-footer-dependency'] ) ): '';
				$the_options['enable_safe']          = isset( $_POST['gcc-enable-safe'] ) && ( true === $_POST['gcc-enable-safe'] || 'true' === $_POST['gcc-enable-safe'] ) ? 'true' : 'false';
				$the_options['logging_on']           = isset( $_POST['gcc-logging-on'] ) && ( true === $_POST['gcc-logging-on'] || 'true' === $_POST['gcc-logging-on'] ) ? 'true' : 'false';
				// DO NOT TRACK.
				$the_options['do_not_track_on'] = isset( $_POST['gcc-do-not-track'] ) && ( true === $_POST['gcc-do-not-track'] || 'true' === $_POST['gcc-do-not-track'] ) ? 'true' : 'false';
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
				if ( isset( $_POST['gcc-worldwide-enable-ccpa'] ) ) {
					if ( 'no' === $_POST['gcc-worldwide-enable-ccpa'] ) {
						$the_options['is_worldwide_on_ccpa'] = 'false';
					} elseif ( 'false' == $_POST['gcc-worldwide-enable-ccpa'] ) {
						$the_options['is_worldwide_on_ccpa'] = 'false';
					} else {
						if(!$the_options['is_worldwide_on_ccpa']){
							$this->disable_auto_update_maxminddb();
						}
						$the_options['is_worldwide_on_ccpa'] = 'true';
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
				$selected_countries             = array();
				$selected_countries             = isset( $_POST['gcc-selected-countries'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-selected-countries'] ) ) ) : '';
				$the_options['select_countries'] = $selected_countries;

				if ( isset( $_POST['gcc-select-countries-enable-ccpa'] ) ) {
					if ( 'no' === $_POST['gcc-select-countries-enable-ccpa'] ) {
						$the_options['is_selectedCountry_on_ccpa'] = 'false';
					} elseif ( 'false' == $_POST['gcc-select-countries-enable-ccpa'] ) {
						$the_options['is_selectedCountry_on_ccpa'] = 'false';
					} else {
						if(!$the_options['is_selectedCountry_on_ccpa']){
							$this->auto_update_maxminddb();
							$this->download_maxminddb();
						}
						$the_options['is_selectedCountry_on_ccpa'] = 'true';
					}
				}

				$selected_countries_ccpa             = array();
				$selected_countries_ccpa             = isset( $_POST['gcc-selected-countries-ccpa'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-selected-countries-ccpa'] ) ) ) : '';
				$the_options['select_countries_ccpa'] = $selected_countries_ccpa;
				// storing id of pages in database.
				
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

				$the_options['is_script_dependency_on'] = isset( $_POST['gcc-script-dependency-on'] ) && ( true === $_POST['gcc-script-dependency-on'] || 'true' === $_POST['gcc-script-dependency-on'] ) ? 'true' : 'false';

				// DO NOT TRACK.
				$the_options['do_not_track_on'] = isset( $_POST['gcc-do-not-track'] ) && ( true === $_POST['gcc-do-not-track'] || 'true' === $_POST['gcc-do-not-track'] ) ? 'true' : 'false';

				$the_options['header_dependency'] = isset( $_POST['gcc-header-dependency'] )? sanitize_text_field( wp_unslash( $_POST['gcc-header-dependency'] ) ): '';
				
				$the_options['footer_dependency'] = isset( $_POST['gcc-footer-dependency'] )? sanitize_text_field( wp_unslash( $_POST['gcc-footer-dependency'] ) ): '';
				
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
	 *  Card for a single template
	 */
	public function template_card($the_options, $template) {
		?>
		<div v-show = "show_cookie_as == 'widget' || show_cookie_as == 'popup' || '<?php echo esc_js($template['name']); ?>' !== 'blue_full'" class="gdpr-template-field gdpr-<?php echo esc_attr( $template['name'] ); ?>">
				
				<?php 

					$styles_attr = '';
					foreach ($template['styles'] as $key => $value) {
						if($key != 'opacity' && $key != 'is_on') $styles_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					} 
					$styles_attr .= "position: relative;";

					$accept_style_attr = '';
					foreach ($template['accept_button'] as $key => $value) {
						if($key != 'opacity' && $key != 'is_on') $accept_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					} 
					$accept_style_attr.= "padding: " . esc_attr($template['static-settings']['button_padding']) . ';';

					$accept_all_style_attr = '';
					foreach ($template['accept_all_button'] as $key => $value) {
						if($key != 'opacity' && $key != 'is_on') $accept_all_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					} 
					$accept_all_style_attr.= "padding: " . esc_attr($template['static-settings']['button_padding']) . ';';

					$decline_style_attr = '';
					foreach ($template['decline_button'] as $key => $value) {
						if($key != 'opacity' && $key != 'is_on') $decline_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					} 
					$decline_style_attr.= "padding: " . esc_attr($template['static-settings']['button_padding']) . ';';

					$settings_style_attr = '';
					foreach ($template['settings_button'] as $key => $value) {
						if($key != 'opacity' && $key != 'is_on') $settings_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					}  
					$settings_style_attr.= "padding: " . esc_attr($template['static-settings']['button_padding']) . ';';
					
					$logo_style_attr = '';
					foreach ($template['logo'] as $key => $value) {
						if($key != 'src')  $logo_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					}  

					$readmore_style_attr = '';
					foreach ($template['readmore_button'] as $key => $value) {
						if($key == 'color') $readmore_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					}  
					$heading_style_attr = "";
					foreach ($template['heading'] as $key => $value) {
						$heading_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					}  
					$decoration_styles_attr = '';
					if(isset($template['decoration'])) foreach ($template['decoration'] as $key => $value) {
						 $decoration_styles_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					} 
				?>
				<div :class=" 'gdpr-right-field template-type-' + show_cookie_as ">
						<div style = "<?php echo esc_attr($styles_attr); ?>" class="cookie_notice_content">
							<span style="display: inline-flex; align-items: center; justify-content: center; position: absolute; top:20px; right: 20px; height: 20px; width: 20px; border-radius: 50%;color: <?php echo $template['accept_button']['background-color'] ?>; background-color: transparent">
								<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z" fill="currentColor"/>
								</svg>
							</span>

								<?php if($template['logo']['src'] !== '') { ?><img alt="WPCC Logo image" style = "<?php echo esc_attr($logo_style_attr); ?>" class="gdpr_logo_image" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'includes/templates/logo_images/' . $template['logo']['src']; ?>" >
								<?php }else { ?>
									<!-- <p style="height: 30px;"></p> -->
								<?php } ?>
								<?php if($decoration_styles_attr !== ''){ ?>
									<div  style = "<?php echo esc_attr($decoration_styles_attr); ?>" class="gdpr_banner_decoration"></div>
								<?php } ?>
							
								
										
								
								<div class="<?php echo esc_attr($template['static-settings']['layout']);?>">
									<div style="display: flex; flex-direction: column; gap: 10px;">
										<?php
											if ( $the_options['cookie_usage_for'] === 'gdpr' || $the_options['cookie_usage_for'] === 'both' ) : ?>
												<h3 style = "<?php echo esc_attr($heading_style_attr); ?>" v-if="gdpr_message_heading.length>0">{{gdpr_message_heading}}</h3>
												<?php if( $template['name'] === 'blue_split' ){ ?><h3 style = "<?php echo esc_attr($heading_style_attr); ?>" v-if="gdpr_message_heading.length===0"><?php echo esc_html("We value your privacy"); ?></h3> <?php } ?>
											<?php elseif ( $the_options['cookie_usage_for'] === 'lgpd' ) : ?>
												<h3 style = "<?php echo esc_attr($heading_style_attr); ?>"  v-if="lgpd_message_heading.length>0">{{lgpd_message_heading}}</h3>
												<?php if( $template['name'] === 'blue_split' ){ ?><h3 style = "<?php echo esc_attr($heading_style_attr); ?>" v-if="lgpd_message_heading.length===0"><?php echo esc_html("We value your privacy"); ?></h3> <?php } ?>
											<?php elseif( $template['name'] === 'blue_split' ) : ?> <h3 style = "<?php echo esc_attr($heading_style_attr); ?>" ><?php echo esc_html("We value your privacy"); ?></h3>
											<?php endif; ?>
										<p>
											<?php if ( $the_options['cookie_usage_for'] === 'gdpr' || $the_options['cookie_usage_for'] === 'both' ) : ?>
												<span v-html ="gdpr_message"></span>
												<?php elseif ( $the_options['cookie_usage_for'] === 'lgpd' ) : ?>
												<span v-html ="lgpd_message"></span>
												<?php elseif ( $the_options['cookie_usage_for'] === 'ccpa' ) : ?>
												<span v-html ="ccpa_message"></span>
												<?php elseif ( $the_options['cookie_usage_for'] === 'eprivacy' ) : ?>
												<span v-html ="eprivacy_message"></span>
											<?php endif; ?>
											<a style = "<?php echo esc_attr($readmore_style_attr); ?>" >
												<?php if ( $the_options['cookie_usage_for'] === 'ccpa' ) : ?>
													{{ opt_out_text }}
												<?php else : ?>
													{{ button_readmore_text }}
												<?php endif; ?>
											</a>
										</p>
									</div>
									
									<?php if ( $the_options['cookie_usage_for'] !== 'ccpa' ) : ?>
										<div class="cookie_notice_buttons <?php echo esc_attr($template['static-settings']['layout']) . '-buttons';?>">
											<div class="left_buttons">
												<?php if($template["decline_button"]["is_on"]) : ?><a style="<?php echo esc_attr( $decline_style_attr ); ?>">{{ decline_text }}</a><?php endif;?>
												<?php if($template["settings_button"]["is_on"] && $the_options['cookie_usage_for'] !== 'eprivacy') : ?><a style="<?php echo esc_attr( $settings_style_attr ); ?>">{{ settings_text }}</a><?php endif;?>
											</div>
											<div class="right_buttons">
												<?php if($template["accept_button"]["is_on"]) : ?><a style="<?php echo esc_attr( $accept_style_attr ); ?>">{{ accept_text }}</a><?php endif;?>
												<?php if($template["accept_all_button"]["is_on"]) : ?><a style="<?php echo esc_attr( $accept_all_style_attr); ?>">{{ accept_all_text }}</a><?php endif;?>
											</div>
										</div>
									<?php endif; ?>
								</div>
						</div>
					</div>
			</div>
			<?php 
	}
	/**
	 *  Small card for a single template
	 */
	public function small_template_card($the_options, $template) {
		?>
		<div v-show = "show_cookie_as == 'widget' || show_cookie_as == 'popup' || '<?php echo esc_js($template['name']); ?>' !== 'blue_full'" class="gdpr-template-field-small gdpr-<?php echo esc_attr( $template['name'] ); ?>">
				<div class="gdpr-left-field">
					<c-input type="radio"  name="<?php echo 'template_field'; ?>" :value="'<?php echo esc_attr( $template['name'] ); ?>'" @change="onTemplateChange" :checked="template === '<?php echo esc_attr($template['name']); ?>'">
				</div>
				<?php 

					$styles_attr = '';
					foreach ($template['styles'] as $key => $value) {
						if($key != 'opacity' && $key != 'is_on') $styles_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					} 
					$styles_attr .= "position: relative;";

					$accept_style_attr = '';
					foreach ($template['accept_button'] as $key => $value) {
						if($key != 'opacity' && $key != 'is_on') $accept_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					} 
					$accept_style_attr.= "padding: " . esc_attr($template['static-settings']['button_padding']) . ';';

					$accept_all_style_attr = '';
					foreach ($template['accept_all_button'] as $key => $value) {
						if($key != 'opacity' && $key != 'is_on') $accept_all_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					} 
					$accept_all_style_attr.= "padding: " . esc_attr($template['static-settings']['button_padding']) . ';';

					$decline_style_attr = '';
					foreach ($template['decline_button'] as $key => $value) {
						if($key != 'opacity' && $key != 'is_on') $decline_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					} 
					$decline_style_attr.= "padding: " . esc_attr($template['static-settings']['button_padding']) . ';';

					$settings_style_attr = '';
					foreach ($template['settings_button'] as $key => $value) {
						if($key != 'opacity' && $key != 'is_on') $settings_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					}  
					$settings_style_attr.= "padding: " . esc_attr($template['static-settings']['button_padding']) . ';';
					
					$logo_style_attr = '';
					foreach ($template['logo'] as $key => $value) {
						if($key != 'src') $logo_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					}  

					$readmore_style_attr = '';
					foreach ($template['readmore_button'] as $key => $value) {
						if($key == 'color') $readmore_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					}  
					$heading_style_attr = "";
					foreach ($template['heading'] as $key => $value) {
						$heading_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					}
					$decoration_styles_attr = '';
					if(isset($template['decoration'])) foreach ($template['decoration'] as $key => $value) {
						 $decoration_styles_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					} 
				?>
				<div :class=" 'gdpr-right-field template-type-' + show_cookie_as ">
						<div style = "<?php echo esc_attr($styles_attr); ?>" class="cookie_notice_content">
							<span style="display: inline-flex; align-items: center; justify-content: center; position: absolute; top:20px; right: 20px; height: 20px; width: 20px; border-radius: 50%;color: <?php echo $template['accept_button']['background-color'] ?>; background-color: transparent">
								<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z" fill="currentColor"/>
								</svg>
							</span>


								<?php if($template['logo']['src'] !== '') { ?><img alt="WPCC Logo image" style = "<?php echo esc_attr($logo_style_attr); ?>" class="gdpr_logo_image" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'includes/templates/logo_images/' . $template['logo']['src']; ?>" > 
								<?php }else { ?>
									<p style="height: 20px;"></p>
								<?php } ?>

								<?php if($decoration_styles_attr !== ''){ ?>
									<div  style = "<?php echo esc_attr($decoration_styles_attr); ?>" class="gdpr_banner_decoration"></div>
								<?php } ?>  
							
								
										
								
								<div class="<?php echo esc_attr($template['static-settings']['layout']);?>">
									<div style="display: flex; flex-direction: column; gap: 5px; <?php if($template['name'] === 'bold') echo "height: 100%;"; ?>">
										<?php
											if ( $the_options['cookie_usage_for'] === 'gdpr' || $the_options['cookie_usage_for'] === 'both' ) : ?>
												<h3 style = "<?php echo esc_attr($heading_style_attr); ?>" v-if="gdpr_message_heading.length>0">{{gdpr_message_heading}}</h3>
												<?php if( $template['name'] === 'blue_split' ){ ?><h3 style = "<?php echo esc_attr($heading_style_attr); ?>" v-if="gdpr_message_heading.length===0"><?php echo esc_html("We value your privacy"); ?></h3> <?php } ?>
											<?php elseif ( $the_options['cookie_usage_for'] === 'lgpd' ) : ?>
												<h3 style = "<?php echo esc_attr($heading_style_attr); ?>"  v-if="lgpd_message_heading.length>0">{{lgpd_message_heading}}</h3>
												<?php if( $template['name'] === 'blue_split' ){ ?><h3 style = "<?php echo esc_attr($heading_style_attr); ?>" v-if="lgpd_message_heading.length===0"><?php echo esc_html("We value your privacy"); ?></h3> <?php } ?>
											<?php elseif( $template['name'] === 'blue_split' ) : ?> <h3 style = "<?php echo esc_attr($heading_style_attr); ?>" ><?php echo esc_html("We value your privacy"); ?></h3>
											<?php endif; ?>
										<p>
											<?php if ( $the_options['cookie_usage_for'] === 'gdpr' || $the_options['cookie_usage_for'] === 'both' ) : ?>
												<span v-html ="gdpr_message"></span>
												<?php elseif ( $the_options['cookie_usage_for'] === 'lgpd' ) : ?>
												<span v-html ="lgpd_message"></span>
												<?php elseif ( $the_options['cookie_usage_for'] === 'ccpa' ) : ?>
												<span v-html ="ccpa_message"></span>
												<?php elseif ( $the_options['cookie_usage_for'] === 'eprivacy' ) : ?>
												<span v-html ="eprivacy_message"></span>
											<?php endif; ?>
											<a style = "<?php echo esc_attr($readmore_style_attr); ?>" >
												<?php if ( $the_options['cookie_usage_for'] === 'ccpa' ) : ?>
													{{ opt_out_text }}
												<?php else : ?>
													{{ button_readmore_text }}
												<?php endif; ?>
											</a>
										</p>
									</div>
									
									<?php if ( $the_options['cookie_usage_for'] !== 'ccpa' ) : ?>
										<div class="cookie_notice_buttons <?php echo esc_attr($template['static-settings']['layout']) . '-buttons';?>">
											<div class="left_buttons">
												<?php if($template["decline_button"]["is_on"]) : ?><a style="<?php echo esc_attr( $decline_style_attr ); ?>">{{ decline_text }}</a><?php endif;?>
												<?php if($template["settings_button"]["is_on"] && $the_options['cookie_usage_for'] !== 'eprivacy') : ?><a style="<?php echo esc_attr( $settings_style_attr ); ?>">{{ settings_text }}</a><?php endif;?>
											</div>
											<div class="right_buttons">
												<?php if($template["accept_button"]["is_on"]) : ?><a style="<?php echo esc_attr( $accept_style_attr ); ?>">{{ accept_text }}</a><?php endif;?>
												<?php if($template["accept_all_button"]["is_on"]) : ?><a style="<?php echo esc_attr( $accept_all_style_attr); ?>">{{ accept_all_text }}</a><?php endif;?>
											</div>
										</div>
									<?php endif; ?>
								</div>
						</div>
					</div>
			</div>
			<?php 
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
	public function print_template_boxes() {
		$the_options    = Gdpr_Cookie_Consent::gdpr_get_settings();

		$is_user_connected = $this->settings->is_connected();
		$templates = $this -> templates_json;
		$pro_installed     = isset( $installed_plugins['wpl-cookie-consent/wpl-cookie-consent.php'] ) ? true : false;
		$pro_is_activated  = get_option( 'wpl_pro_active', false );
		$api_key_activated = get_option( 'wc_am_client_wpl_cookie_consent_activated','' );
		$default_template  = get_option('gdpr_default_template_object');
		?>
		<div class="gdpr-templates-field-container">
		<?php	
			$this->template_card($the_options,json_decode($the_options['selected_template_json'], true));
			if(!$is_user_connected) : ?>
				<div class="template_loader_container">
					<div :class=" 'template_loader loader-type-' + show_cookie_as ">
						<img src = "<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/mock_banner_overlay.png'; ?>" class="mock_banner" />
						<div class="wpl-cookie-consent-overlay"></div>
						<?php
						if ( $pro_installed ) :
							?>
							<?php if ( $api_key_activated == 'Deactivated' ) : ?>
							<div class="gdpr-overlay">
								<p class="key-text"><?php esc_html_e( 'To access more templates, please activate your API key.', 'gdpr-cookie-consent' ); ?></p>
								<button class="gdpr-activate-api-plugin"><?php esc_html_e( 'Activate API Key', 'gdpr-cookie-consent' ); ?></button>
							</div>
							<?php endif; ?>
							<?php if ( ! $pro_is_activated ) : ?>
								<div class="gdpr-overlay">
								<p class="key-text"><?php esc_html_e( 'To access more templates, please activate your WP Cookie Consent Pro plugin', 'gdpr-cookie-consent' ); ?></p>
								<button class="gdpr-activate-plugin"><?php esc_html_e( 'Activate Pro Plugin', 'gdpr-cookie-consent' ); ?></button>
								</div> 
							<?php endif; ?>
						<?php endif; ?>
						<!-- API Connection Screen  -->
						<?php if ( ! $is_user_connected && ! $pro_installed ) : ?>
							<div class="gdpr-overlay">
								<img :src="account_connection.default" class="gdpr-cookie-account_connection">
								<p class="enable-text"><?php esc_html_e( 'To access more templates, create your FREE WP Cookie Consent account.', 'gdpr-cookie-consent' ); ?></p>
								<button class="gdpr-start-auth"><?php esc_html_e( 'New? Create an account', 'gdpr-cookie-consent' ); ?></button>
								<p><span class="already-have-acc"><?php esc_html_e( 'Already have an account? ', 'gdpr-cookie-consent' ); ?></span><span class="api-connect-to-account-btn" ><?php esc_html_e( 'Connect your existing account', 'gdpr-cookie-consent' ); ?></span></p>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php else : ?>
				<div class="more_templates_option_container">
					<div class=" more_templates_option ">
						<img src = "<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/banner_designs_templates.png'; ?>"/>
						<p><?php echo esc_html("We have a library of 20+ templates to choose from"); ?></p>
						<button class="more_templates_button" id="more_templates_button"><?php echo esc_html("Explore templates");?></button>
					</div>
					<div id = "template_selection_panel" class="template_selection_panel">
						<div class="template_selection_header">
							<span class="template_selection_panel_close" style="display: inline-flex; cursor: pointer; align-items: center; justify-content: center; height: 20px; width: 20px; border-radius: 50%;background-color: gray; color: white;">
								<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z" fill="currentColor"/>
								</svg>
							</span>
						</div>
						<div class="template_selection_body">
							<?php 
								$this -> small_template_card($the_options, $default_template);
								foreach ( $templates as $key => $template ) : 
									$this -> small_template_card($the_options, $template);
								endforeach; ?>
						</div>
						<div class="template_selection_footer">
							<button class="template_selection_panel_close template_selection_cancel">Cancel</button>
							<button class="template_selection_save" :disabled="save_loading" @click="saveCookieSettings">{{ save_loading ? '<?php esc_html_e( 'Saving...', 'gdpr-cookie-consent' ); ?>' : '<?php esc_html_e( 'Save Changes', 'gdpr-cookie-consent' ); ?>' }}</button>
						</div>
					</div>
					<div id = "template_selection_backface" class="template_selection_backface"></div>
				</div>
			<?php endif; ?>	
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
			<c-card v-show="is_gdpr || is_lgpd || is_ccpa || is_eprivacy">
					<c-row>
						<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_attr_e( 'Cookie Bar Template', 'gdpr-cookie-consent' ); ?></div></c-col>
					</c-row>
					<c-row >
						<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cookie Templates', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_attr_e( 'Use a pre-built template to style your Cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
						<c-col class="col-sm-8">
							<div role="group" class="form-group">
								<span class="gdpr-cookie-consent-description"><?php esc_attr_e( 'To preview the pre-built templates below, simply choose a template and then click the "Save Changes" button. Please note that this action will replace your current banner settings.', 'gdpr-cookie-consent' ); ?></span>
							</div>
						</c-col>
					</c-row>
					<c-row>
						<c-col class="col-sm-4 relative"><label><?php esc_attr_e( 'Auto Generate Banner', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_attr_e( 'Enable this setting to automatically generate a cookie banner that matches your website\'s color theme, ensuring seamless integration with your design.
						', 'gdpr-cookie-consent' ); ?>"></tooltip></label></label>
						</c-col>
						<c-row>
						<c-col class="col-sm-4"></c-col>
						<c-col class="col-sm-8">
								<div role="group" class="form-group">
									<span class="gdpr-cookie-consent-description"><?php esc_html( 'To preview the auto generated template, simply click on auto generate template button above and then click the "Save Changes" button.', 'gdpr-cookie-consent' ); ?></span>
								</div>
							</c-col>
						</c-row>
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
					<c-row style="margin-bottom: 0;"><c-col class="col-sm-4"><label><?php esc_attr_e( 'Your Selected Template', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_attr_e( 'To change, connect Account if not connected and then click on Explore templates button.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col></c-row>
					<c-row>
						<c-col class="col-sm-0">
							<input type="hidden" name="gdpr-template" v-model="template">
						</c-col>
						<c-col class="col-sm-12">
							<?php $this->print_template_boxes(); ?>
						</c-col>
					</c-row>
					<input type="hidden" name="gdpr-template" v-model="template">
			</c-card>
				<?php
	}

	/**
	 * Ajax callback for gcm region form.
	 */
	public function gdpr_cookie_consent_ajax_save_gcm_region(){
		$the_options    = Gdpr_Cookie_Consent::gdpr_get_settings();
		$the_options['gcm_defaults'] = json_encode(json_decode(stripslashes($_POST['regionArray'])));
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
	}


	public function add_every_minute_cron_schedule( $schedules ) {
        if ( ! isset( $schedules['every_minute'] ) ) {
            $schedules['every_minute'] = array(
                'interval' => 60,
                'display'  => __( 'Every Minute' ),
            );
        }
        return $schedules;
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
			$ab_options['ab_testing_auto'] = isset( $_POST['gcc-ab-testing-auto'] ) ? ($_POST['gcc-ab-testing-auto'] === true || $_POST['gcc-ab-testing-auto']==='true' || $_POST['gcc-ab-testing-auto'] === 1 ? 'true' :'false')  : 'false';

			// Get the updated A/B testing period value
			$updated_ab_testing_value = isset($ab_options['ab_testing_period']) ? $ab_options['ab_testing_period'] : '';
			// Handle auto-generated banner reset when template is changed
			$reset_auto_generated = isset($_POST['reset_auto_generated']) ? sanitize_text_field($_POST['reset_auto_generated']) : '0';
			$is_template_changed = isset($_POST['is_template_changed']) ? sanitize_text_field($_POST['is_template_changed']) : '0';
			$auto_generated_banner = isset($_POST['auto_generated_banner']) ? sanitize_text_field($_POST['auto_generated_banner']) : '0';
			$template = isset($_POST['gdpr-template']) ? sanitize_text_field($_POST['gdpr-template']) : 'default';
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
					$new_expiration_time_seconds = ((int) $updated_ab_testing_value * 24 * 60 * 60); // New expiration time in seconds
					
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
					$new_expiration_time_seconds = ((int) $updated_ab_testing_value * 24 * 60 * 60);
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
			
			// Data Reqs.
			$the_options['data_reqs_on'] = isset( $_POST['gcc-data_reqs'] ) && ( true === $_POST['gcc-data_reqs'] || 'true' === $_POST['gcc-data_reqs'] ) ? 'true' : 'false';

			if ( filter_var( $the_options['is_on'], FILTER_VALIDATE_BOOLEAN ) !==  filter_var( $_POST['gcc-cookie-enable'], FILTER_VALIDATE_BOOLEAN ) ) {
				$cookie_banner_status = filter_var( $_POST['gcc-cookie-enable'], FILTER_VALIDATE_BOOLEAN ) ? 'Turned On' : 'Turned Off';
				$data_args = array(
					'Status' => 'Cookie Banner ' . $cookie_banner_status,
				);
				$this->gdpr_send_shared_usage_data( 'GCC Banner Status', $data_args );
			}

			$the_options['is_on']                              = isset( $_POST['gcc-cookie-enable'] ) && ( true === $_POST['gcc-cookie-enable'] || 'true' === $_POST['gcc-cookie-enable'] ) ? 'true' : 'false';
			
			
			$the_options['cookie_usage_for']                   = isset( $_POST['gcc-gdpr-policy'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-gdpr-policy'] ) ) : 'gdpr';
			$the_options['cookie_bar_as']                      = isset( $_POST['show-cookie-as'] ) ? sanitize_text_field( wp_unslash( $_POST['show-cookie-as'] ) ) : 'banner';
			$the_options['cookie_bar_as1']        			   = isset( $_POST['show-cookie-as1'] ) ? sanitize_text_field( wp_unslash( $_POST['show-cookie-as1'] ) ) : 'banner';
			$the_options['cookie_bar_as2']        			   = isset( $_POST['show-cookie-as2'] ) ? sanitize_text_field( wp_unslash( $_POST['show-cookie-as2'] ) ) : 'banner';
			$the_options['notify_position_vertical']           = isset( $_POST['gcc-gdpr-cookie-position'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-gdpr-cookie-position'] ) ) : 'bottom';
			$the_options['notify_position_vertical1']           = isset( $_POST['gcc-gdpr-cookie-position1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-gdpr-cookie-position1'] ) ) : 'bottom';
			$the_options['notify_position_vertical2']           = isset( $_POST['gcc-gdpr-cookie-position2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-gdpr-cookie-position2'] ) ) : 'bottom';
			$the_options['notify_position_horizontal']         = isset( $_POST['gcc-gdpr-cookie-widget-position'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-gdpr-cookie-widget-position'] ) ) : 'left';
			$the_options['notify_position_horizontal1']         = isset( $_POST['gcc-gdpr-cookie-widget-position1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-gdpr-cookie-widget-position1'] ) ) : 'left';
			$the_options['notify_position_horizontal2']         = isset( $_POST['gcc-gdpr-cookie-widget-position2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-gdpr-cookie-widget-position2'] ) ) : 'left';
			$the_options['popup_overlay']                      = isset( $_POST['gdpr-cookie-add-overlay'] ) && ( true === $_POST['gdpr-cookie-add-overlay'] || 'true' === $_POST['gdpr-cookie-add-overlay'] ) ? 'true' : 'false';
			$the_options['popup_overlay1']                      = isset( $_POST['gdpr-cookie-add-overlay1'] ) && ( true === $_POST['gdpr-cookie-add-overlay1'] || 'true' === $_POST['gdpr-cookie-add-overlay1'] ) ? 'true' : 'false';
			$the_options['popup_overlay2']                      = isset( $_POST['gdpr-cookie-add-overlay2'] ) && ( true === $_POST['gdpr-cookie-add-overlay2'] || 'true' === $_POST['gdpr-cookie-add-overlay2'] ) ? 'true' : 'false';
			$the_options['notify_animate_hide']                = isset( $_POST['gcc-gdpr-cookie-on-hide'] ) && ( true === $_POST['gcc-gdpr-cookie-on-hide'] || 'true' === $_POST['gcc-gdpr-cookie-on-hide'] ) ? 'true' : 'false';
			$the_options['notify_animate_hide1']                = isset( $_POST['gcc-gdpr-cookie-on-hide1'] ) && ( true === $_POST['gcc-gdpr-cookie-on-hide1'] || 'true' === $_POST['gcc-gdpr-cookie-on-hide1'] ) ? 'true' : 'false';
			$the_options['notify_animate_hide2']                = isset( $_POST['gcc-gdpr-cookie-on-hide2'] ) && ( true === $_POST['gcc-gdpr-cookie-on-hide2'] || 'true' === $_POST['gcc-gdpr-cookie-on-hide2'] ) ? 'true' : 'false';
			$the_options['notify_animate_show']                = isset( $_POST['gcc-gdpr-cookie-on-load'] ) && ( true === $_POST['gcc-gdpr-cookie-on-load'] || 'true' === $_POST['gcc-gdpr-cookie-on-load'] ) ? 'true' : 'false';
			$the_options['notify_animate_show1']               = isset( $_POST['gcc-gdpr-cookie-on-load1'] ) && ( true === $_POST['gcc-gdpr-cookie-on-load1'] || 'true' === $_POST['gcc-gdpr-cookie-on-load1'] ) ? 'true' : 'false';
			$the_options['notify_animate_show2']               = isset( $_POST['gcc-gdpr-cookie-on-load2'] ) && ( true === $_POST['gcc-gdpr-cookie-on-load2'] || 'true' === $_POST['gcc-gdpr-cookie-on-load2'] ) ? 'true' : 'false';
			$the_options['background']                         = isset( $_POST['gdpr-cookie-bar-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-color'] ) ) : '#ffffff';
			$the_options['text']                               = isset( $_POST['gdpr-cookie-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-text-color'] ) ) : '#000000';
			$the_options['opacity']                            = isset( $_POST['gdpr-cookie-bar-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-bar-opacity'] ) ) : '1';
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
			$the_options['button_accept_action']               = isset( $_POST['gdpr-cookie-accept-action'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-action'] ) ) : '#cookie_action_close_header';
			$the_options['button_accept_url']                  = isset( $_POST['gdpr-cookie-accept-url'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-accept-url'] ) ) : '#';
			$the_options['button_accept_as_button']            = isset( $_POST['gdpr-cookie-accept-as'] ) && ( true === $_POST['gdpr-cookie-accept-as'] || 'true' === $_POST['gdpr-cookie-accept-as'] ) ? 'true' : 'false';
			$the_options['button_accept_new_win']              = isset( $_POST['gdpr-cookie-url-new-window'] ) && ( true === $_POST['gdpr-cookie-url-new-window'] || 'true' === $_POST['gdpr-cookie-url-new-window'] ) ? 'true' : 'false';
			if($auto_generated_banner !== '1'){
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
			$the_options['multiple_legislation_cookie_bar_opacity1'] = isset( $_POST['gdpr-multiple-legislation-cookie-bar-opacity1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-multiple-legislation-cookie-bar-opacity1'] ) ) : '1';
			$the_options['multiple_legislation_cookie_bar_opacity2'] = isset( $_POST['gdpr-multiple-legislation-cookie-bar-opacity2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-multiple-legislation-cookie-bar-opacity2'] ) ) : '1';
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

			if(($the_options['is_gacm_on'] == "false" || $the_options['is_gacm_on'] == false) && $_POST['gcc-gacm-enable'] == "true"){
				if(!get_option(GDPR_COOKIE_CONSENT_SETTINGS_GACM_VENDOR)){
					$this->get_gacm_data();
				}
				$this->activate_gacm_updater();	
			}
			$the_options['is_gacm_on']                       = isset( $_POST['gcc-gacm-enable'] ) && ( true === $_POST['gcc-gacm-enable'] || 'true' === $_POST['gcc-gacm-enable'] ) ? 'true' : 'false';
			if($the_options['is_gacm_on']  == "false" || $the_options['is_gacm_on']  == false){
				$this->deactivate_gacm_updater();
			}
			if(!isset($the_options['gcm_defaults'])){
				$the_options['gcm_defaults'] = json_encode([
					(object)[
						'region' => 'All',
						'ad_storage' => 'denied',
						'analytics_storage' => 'denied',
						'ad_user_data' => 'denied',
						'ad_personalization' => 'denied',
						'functionality_storage' => 'granted',
						'personalization_storage' => 'denied',
						'security_storage' => 'granted',
					]
				]);
				
			}
			$the_options['is_gcm_on']                       	 = isset( $_POST['gcc-gcm-enable'] ) && ( true === $_POST['gcc-gcm-enable'] || 'true' === $_POST['gcc-gcm-enable'] ) ? 'true' : 'false';
			$the_options['gcm_wait_for_update_duration']         = isset( $_POST['gcm_wait_for_update_duration_field'] ) ? sanitize_text_field(wp_unslash($_POST['gcm_wait_for_update_duration_field'])) : '500';
			$the_options['is_gcm_url_passthrough']               = isset( $_POST['gcc-gcm-url-pass'] ) && ( true === $_POST['gcc-gcm-url-pass'] || 'true' === $_POST['gcc-gcm-url-pass'] ) ? 'true' : 'false';
			$the_options['is_gcm_ads_redact']               	 = isset( $_POST['gcc-gcm-ads-redact'] ) && ( true === $_POST['gcc-gcm-ads-redact'] || 'true' === $_POST['gcc-gcm-ads-redact'] ) ? 'true' : 'false';
			$the_options['is_gcm_debug_mode']               	 = isset( $_POST['gcc-gcm-debug-mode'] ) && ( true === $_POST['gcc-gcm-debug-mode'] || 'true' === $_POST['gcc-gcm-debug-mode'] ) ? 'true' : 'false';
			$the_options['is_gcm_advertiser_mode']               = isset( $_POST['gcc-gcm-advertiser-mode'] ) && ( true === $_POST['gcc-gcm-advertiser-mode'] || 'true' === $_POST['gcc-gcm-advertiser-mode'] ) ? 'true' : 'false';
			$the_options['is_iabtcf_on']                         = isset( $_POST['gcc-iabtcf-enable'] ) && ( true === $_POST['gcc-iabtcf-enable'] || 'true' === $_POST['gcc-iabtcf-enable'] ) ? 'true' : 'false';
			$the_options['is_dynamic_lang_on']                   = isset( $_POST['gcc-dynamic-lang-enable'] ) && ( true === $_POST['gcc-dynamic-lang-enable'] || 'true' === $_POST['gcc-dynamic-lang-enable'] ) ? 'true' : 'false';
			$the_options['optout_text']                          = isset( $_POST['notify_message_ccpa_optout_field'] ) ? sanitize_text_field( wp_unslash( $_POST['notify_message_ccpa_optout_field'] ) ) : 'Do you really wish to opt-out?';
			$the_options['is_ccpa_iab_on']                       = isset( $_POST['gcc-iab-enable'] ) && ( true === $_POST['gcc-iab-enable'] || 'true' === $_POST['gcc-iab-enable'] ) ? 'true' : 'false';
			$the_options['show_again']                           = isset( $_POST['gcc-revoke-consent-enable'] ) && ( true === $_POST['gcc-revoke-consent-enable'] || 'true' === $_POST['gcc-revoke-consent-enable'] ) ? 'true' : 'false';
			$the_options['show_again1']                          = isset( $_POST['gcc-revoke-consent-enable1'] ) && ( true === $_POST['gcc-revoke-consent-enable1'] || 'true' === $_POST['gcc-revoke-consent-enable1'] ) ? 'true' : 'false';
			$the_options['show_again2']                          = isset( $_POST['gcc-revoke-consent-enable2'] ) && ( true === $_POST['gcc-revoke-consent-enable2'] || 'true' === $_POST['gcc-revoke-consent-enable2'] ) ? 'true' : 'false';
			$the_options['show_again_position']                  = isset( $_POST['gcc-tab-position'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-tab-position'] ) ) : 'right';
			$the_options['show_again_position1']                 = isset( $_POST['gcc-tab-position1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-tab-position1'] ) ) : 'right';
			$the_options['show_again_position2']                 = isset( $_POST['gcc-tab-position2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-tab-position2'] ) ) : 'right';
			$the_options['show_again_margin']                    = isset( $_POST['gcc-tab-margin'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-tab-margin'] ) ) : '5';
			$the_options['show_again_margin1']                   = isset( $_POST['gcc-tab-margin1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-tab-margin1'] ) ) : '5';
			$the_options['show_again_margin2']                   = isset( $_POST['gcc-tab-margin2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-tab-margin2'] ) ) : '5';
			$the_options['show_again_text']                      = isset( $_POST['show_again_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['show_again_text_field'] ) ) : 'Cookie Settings';
			$the_options['show_again_text1']                     = isset( $_POST['show_again_text_field1'] ) ? sanitize_text_field( wp_unslash( $_POST['show_again_text_field1'] ) ) : 'Cookie Settings';
			$the_options['show_again_text2']                     = isset( $_POST['show_again_text_field2'] ) ? sanitize_text_field( wp_unslash( $_POST['show_again_text_field2'] ) ) : 'Cookie Settings';
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
			$the_options['decline_reload']                       = isset( $_POST['gcc-decline-reload'] ) && ( true === $_POST['gcc-decline-reload'] || 'true' === $_POST['gcc-decline-reload'] ) ? 'true' : 'false';
			$the_options['delete_on_deactivation']               = isset( $_POST['gcc-delete-on-deactivation'] ) && ( true === $_POST['gcc-delete-on-deactivation'] || 'true' === $_POST['gcc-delete-on-deactivation'] ) ? 'true' : 'false';
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

			$the_options['button_readmore_is_on1']               = isset( $_POST['gcc-readmore-is-on1'] ) && ( true === $_POST['gcc-readmore-is-on1'] || 'true' === $_POST['gcc-readmore-is-on1'] ) ? 'true' : 'false';
			$the_options['button_readmore_text1']                = isset( $_POST['button_readmore_text_field1'] ) ? sanitize_text_field( wp_unslash( $_POST['button_readmore_text_field1'] ) ) : 'Read More';
			$the_options['button_readmore_link_color1']          = isset( $_POST['gcc-readmore-link-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-link-color1'] ) ) : '#359bf5';
			$the_options['button_readmore_as_button1']           = isset( $_POST['gcc-readmore-as-button1'] ) && ( true === $_POST['gcc-readmore-as-button1'] || 'true' === $_POST['gcc-readmore-as-button1'] ) ? 'true' : 'false';
			$the_options['button_readmore_url_type1']            = isset( $_POST['gcc-readmore-url-type1'] ) && ( false === $_POST['gcc-readmore-url-type1'] || 'false' === $_POST['gcc-readmore-url-type1'] ) ? 'false' : 'true';
			$the_options['button_readmore_page1']                = isset( $_POST['gcc-readmore-page1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-page1'] ) ) : '0';
			$the_options['button_readmore_wp_page1']             = isset( $_POST['gcc-readmore-wp-page1'] ) && ( true === $_POST['gcc-readmore-wp-page1'] || 'true' === $_POST['gcc-readmore-wp-page1'] ) ? 'true' : 'false';
			$the_options['button_readmore_new_win1']             = isset( $_POST['gcc-readmore-new-win1'] ) && ( true === $_POST['gcc-readmore-new-win1'] || 'true' === $_POST['gcc-readmore-new-win1'] ) ? 'true' : 'false';
			$the_options['button_readmore_url1']                 = isset( $_POST['gcc-readmore-url1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-url1'] ) ) : '#';
			$the_options['button_readmore_button_color1']        = isset( $_POST['gcc-readmore-button-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-color1'] ) ) : '#000000';
			$the_options['button_readmore_button_opacity1']      = isset( $_POST['gcc-readmore-button-opacity1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-opacity1'] ) ) : '1';
			$the_options['button_readmore_button_border_style1'] = isset( $_POST['gcc-readmore-button-border-style1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-style1'] ) ) : '1';
			$the_options['button_readmore_button_border_width1'] = isset( $_POST['gcc-readmore-button-border-width1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-width1'] ) ) : '0';
			$the_options['button_readmore_button_border_color1'] = isset( $_POST['gcc-readmore-button-border-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-color1'] ) ) : '#000000';
			$the_options['button_readmore_button_border_radius1']= isset( $_POST['gcc-readmore-button-border-radius1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-radius1'] ) ) : '0';
			$the_options['button_readmore_button_size1']         = isset( $_POST['gcc-readmore-button-size1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-size1'] ) ) : 'medium';

			$the_options['button_readmore_is_on2']               = isset( $_POST['gcc-readmore-is-on2'] ) && ( true === $_POST['gcc-readmore-is-on2'] || 'true' === $_POST['gcc-readmore-is-on2'] ) ? 'true' : 'false';
			$the_options['button_readmore_text2']                = isset( $_POST['button_readmore_text_field2'] ) ? sanitize_text_field( wp_unslash( $_POST['button_readmore_text_field2'] ) ) : 'Read More';
			$the_options['button_readmore_link_color2']          = isset( $_POST['gcc-readmore-link-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-link-color2'] ) ) : '#359bf5';
			$the_options['button_readmore_as_button2']           = isset( $_POST['gcc-readmore-as-button2'] ) && ( true === $_POST['gcc-readmore-as-button2'] || 'true' === $_POST['gcc-readmore-as-button2'] ) ? 'true' : 'false';
			$the_options['button_readmore_url_type2']            = isset( $_POST['gcc-readmore-url-type2'] ) && ( false === $_POST['gcc-readmore-url-type2'] || 'false' === $_POST['gcc-readmore-url-type2'] ) ? 'false' : 'true';
			$the_options['button_readmore_page2']                = isset( $_POST['gcc-readmore-page2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-page2'] ) ) : '0';
			$the_options['button_readmore_wp_page2']             = isset( $_POST['gcc-readmore-wp-page2'] ) && ( true === $_POST['gcc-readmore-wp-page2'] || 'true' === $_POST['gcc-readmore-wp-page2'] ) ? 'true' : 'false';
			$the_options['button_readmore_new_win2']             = isset( $_POST['gcc-readmore-new-win2'] ) && ( true === $_POST['gcc-readmore-new-win2'] || 'true' === $_POST['gcc-readmore-new-win2'] ) ? 'true' : 'false';
			$the_options['button_readmore_url2']                 = isset( $_POST['gcc-readmore-url2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-url2'] ) ) : '#';
			$the_options['button_readmore_button_color2']        = isset( $_POST['gcc-readmore-button-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-color2'] ) ) : '#000000';
			$the_options['button_readmore_button_opacity2']      = isset( $_POST['gcc-readmore-button-opacity2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-opacity2'] ) ) : '1';
			$the_options['button_readmore_button_border_style2'] = isset( $_POST['gcc-readmore-button-border-style2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-style2'] ) ) : '1';
			$the_options['button_readmore_button_border_width2'] = isset( $_POST['gcc-readmore-button-border-width2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-width2'] ) ) : '0';
			$the_options['button_readmore_button_border_color2'] = isset( $_POST['gcc-readmore-button-border-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-color2'] ) ) : '#000000';
			$the_options['button_readmore_button_border_radius2']= isset( $_POST['gcc-readmore-button-border-radius2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-border-radius2'] ) ) : '0';
			$the_options['button_readmore_button_size2']         = isset( $_POST['gcc-readmore-button-size2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-readmore-button-size2'] ) ) : 'medium';

			// The below phpcs ignore comments have been added after referring competitor wordpress.org plugins.
			$the_options['header_scripts']                        = isset( $_POST['gcc-header-scripts'] ) ? wp_unslash( $_POST['gcc-header-scripts'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$the_options['body_scripts']                          = isset( $_POST['gcc-body-scripts'] ) ? wp_unslash( $_POST['gcc-body-scripts'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$the_options['footer_scripts']                        = isset( $_POST['gcc-footer-scripts'] ) ? wp_unslash( $_POST['gcc-footer-scripts'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$the_options['button_decline_is_on']                  = isset( $_POST['gcc-cookie-decline-enable'] ) && ( true === $_POST['gcc-cookie-decline-enable'] || 'true' === $_POST['gcc-cookie-decline-enable'] ) ? 'true' : 'false';
			$the_options['button_decline_text']                   = isset( $_POST['button_decline_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_decline_text_field'] ) ) : 'Decline';
			$the_options['button_decline_as_button']              = isset( $_POST['gdpr-cookie-decline-as'] ) && ( true === $_POST['gdpr-cookie-decline-as'] || 'true' === $_POST['gdpr-cookie-decline-as'] ) ? 'true' : 'false';
			$the_options['button_decline_button_opacity']         = isset( $_POST['gdpr-cookie-decline-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-opacity'] ) ) : '1';
			$the_options['button_decline_button_border_radius']   = isset( $_POST['gdpr-cookie-decline-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-radius'] ) ) : '0';
			$the_options['button_decline_action']                 = isset( $_POST['gdpr-cookie-decline-action'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-action'] ) ) : '#cookie_action_close_header_reject';
			$the_options['button_decline_url']                    = isset( $_POST['gdpr-cookie-decline-url'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-url'] ) ) : '#';
			$the_options['button_decline_new_win']                = isset( $_POST['gdpr-cookie-decline-url-new-window'] ) && ( true === $_POST['gdpr-cookie-decline-url-new-window'] || 'true' === $_POST['gdpr-cookie-decline-url-new-window'] ) ? 'true' : 'false';
			$the_options['button_decline_is_on1']                 = isset( $_POST['gcc-cookie-decline-enable1'] ) && ( true === $_POST['gcc-cookie-decline-enable1'] || 'true' === $_POST['gcc-cookie-decline-enable1'] ) ? 'true' : 'false';
			$the_options['button_decline_text1']                  = isset( $_POST['button_decline_text_field1'] ) ? sanitize_text_field( wp_unslash( $_POST['button_decline_text_field1'] ) ) : 'Decline';
			$the_options['button_decline_as_button1']             = isset( $_POST['gdpr-cookie-decline-as1'] ) && ( true === $_POST['gdpr-cookie-decline-as1'] || 'true' === $_POST['gdpr-cookie-decline-as1'] ) ? 'true' : 'false';
			$the_options['button_decline_button_opacity1']        = isset( $_POST['gdpr-cookie-decline-opacity1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-opacity1'] ) ) : '1';
			$the_options['button_decline_button_border_radius1']  = isset( $_POST['gdpr-cookie-decline-border-radius1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-border-radius1'] ) ) : '0';
			$the_options['multiple_legislation_accept_all_border_radius1']  = isset( $_POST['gdpr-multiple-legislation-cookie-accept-all-border-radius1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-multiple-legislation-cookie-accept-all-border-radius1'] ) ) : '0';
			$the_options['button_decline_action1']                = isset( $_POST['gdpr-cookie-decline-action1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-action1'] ) ) : '#cookie_action_close_header_reject';
			$the_options['button_decline_url1']                   = isset( $_POST['gdpr-cookie-decline-url1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-url1'] ) ) : '#';
			$the_options['button_decline_new_win1']               = isset( $_POST['gdpr-cookie-decline-url-new-window1'] ) && ( true === $_POST['gdpr-cookie-decline-url-new-window1'] || 'true' === $_POST['gdpr-cookie-decline-url-new-window1'] ) ? 'true' : 'false';
			$the_options['button_settings_is_on']                 = isset( $_POST['gcc-cookie-settings-enable'] ) && ( true === $_POST['gcc-cookie-settings-enable'] || 'true' === $_POST['gcc-cookie-settings-enable'] ) ? 'true' : 'false';
			$the_options['button_settings_as_popup']              = isset( $_POST['gcc-iabtcf-enable'] ) && ( true === $_POST['gcc-iabtcf-enable'] || 'true' === $_POST['gcc-iabtcf-enable'] ) ? 'true' : (isset( $_POST['gdpr-cookie-settings-layout'] ) && ( true === $_POST['gdpr-cookie-settings-layout'] || 'true' === $_POST['gdpr-cookie-settings-layout'] ) ? 'true' : 'false');
			$the_options['button_settings_text']                  = isset( $_POST['button_settings_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_settings_text_field'] ) ) : 'Cookie Settings';
			$the_options['button_settings_as_button']             = isset( $_POST['gdpr-cookie-settings-as'] ) && ( true === $_POST['gdpr-cookie-settings-as'] || 'true' === $_POST['gdpr-cookie-settings-as'] ) ? 'true' : 'false';
			$the_options['button_settings_button_opacity']        = isset( $_POST['gdpr-cookie-settings-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-opacity'] ) ) : '1';
			$the_options['button_settings_button_border_radius']  = isset( $_POST['gdpr-cookie-settings-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-radius'] ) ) : '0';
			$the_options['button_settings_display_cookies']       = isset( $_POST['gcc-cookie-on-frontend'] ) && ( true === $_POST['gcc-cookie-on-frontend'] || 'true' === $_POST['gcc-cookie-on-frontend'] ) ? 'true' : 'false';
			$the_options['button_settings_is_on1']                = isset( $_POST['gcc-cookie-settings-enable1'] ) && ( true === $_POST['gcc-cookie-settings-enable1'] || 'true' === $_POST['gcc-cookie-settings-enable1'] ) ? 'true' : 'false';
			$the_options['button_settings_as_popup1']             = isset( $_POST['gcc-iabtcf-enable'] ) && ( true === $_POST['gcc-iabtcf-enable'] || 'true' === $_POST['gcc-iabtcf-enable'] ) ? 'true' : (isset( $_POST['gdpr-cookie-settings-layout1'] ) && ( true === $_POST['gdpr-cookie-settings-layout1'] || 'true' === $_POST['gdpr-cookie-settings-layout1'] ) ? 'true' : 'false');
			$the_options['button_settings_text1']                 = isset( $_POST['button_settings_text_field1'] ) ? sanitize_text_field( wp_unslash( $_POST['button_settings_text_field1'] ) ) : 'Cookie Settings';
			$the_options['button_settings_as_button1']            = isset( $_POST['gdpr-cookie-settings-as1'] ) && ( true === $_POST['gdpr-cookie-settings-as1'] || 'true' === $_POST['gdpr-cookie-settings-as1'] ) ? 'true' : 'false';
			$the_options['button_settings_button_opacity1']       = isset( $_POST['gdpr-cookie-settings-opacity1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-opacity1'] ) ) : '1';
			$the_options['button_settings_button_border_radius1'] = isset( $_POST['gdpr-cookie-settings-border-radius1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-radius1'] ) ) : '0';
			$the_options['button_settings_display_cookies1']      = isset( $_POST['gcc-cookie-on-frontend1'] ) && ( true === $_POST['gcc-cookie-on-frontend1'] || 'true' === $_POST['gcc-cookie-on-frontend1'] ) ? 'true' : 'false';
			$the_options['button_confirm_text']                   = isset( $_POST['button_confirm_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_confirm_text_field'] ) ) : 'Confirm';
			$the_options['button_confirm_link_color']             = isset( $_POST['gdpr-cookie-confirm-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-text-color'] ) ) : '#ffffff';
			$the_options['button_confirm_button_color']           = isset( $_POST['gdpr-cookie-confirm-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-background-color'] ) ) : '#18a300';
			$the_options['button_confirm_button_opacity']         = isset( $_POST['gdpr-cookie-confirm-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-opacity'] ) ) : '1';
			$the_options['button_confirm_button_border_style']    = isset( $_POST['gdpr-cookie-confirm-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-style'] ) ) : 'none';
			$the_options['button_confirm_button_border_color']    = isset( $_POST['gdpr-cookie-confirm-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-color'] ) ) : '#18a300';
			$the_options['button_confirm_button_border_width']    = isset( $_POST['gdpr-cookie-confirm-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-width'] ) ) : '0';
			$the_options['button_confirm_button_border_radius']   = isset( $_POST['gdpr-cookie-confirm-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-radius'] ) ) : '0';
			$the_options['button_cancel_text']                    = isset( $_POST['button_cancel_text_field'] ) ? sanitize_text_field( wp_unslash( $_POST['button_cancel_text_field'] ) ) : 'Cancel';
			$the_options['button_cancel_link_color']              = isset( $_POST['gdpr-cookie-cancel-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-text-color'] ) ) : '#ffffff';
			$the_options['button_cancel_button_color']            = isset( $_POST['gdpr-cookie-cancel-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-background-color'] ) ) : '#333333';
			$the_options['button_cancel_button_opacity']          = isset( $_POST['gdpr-cookie-cancel-opacity'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-opacity'] ) ) : '1';
			$the_options['button_cancel_button_border_style']     = isset( $_POST['gdpr-cookie-cancel-border-style'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-style'] ) ) : 'none';
			$the_options['button_cancel_button_border_color']     = isset( $_POST['gdpr-cookie-cancel-border-color'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-color'] ) ) : '#333333';
			$the_options['button_cancel_button_border_width']     = isset( $_POST['gdpr-cookie-cancel-border-width'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-width'] ) ) : '0';
			$the_options['button_cancel_button_border_radius']    = isset( $_POST['gdpr-cookie-cancel-border-radius'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-radius'] ) ) : '0';
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
			$the_options['button_cancel_text1']                   = isset( $_POST['button_cancel_text_field1'] ) ? sanitize_text_field( wp_unslash( $_POST['button_cancel_text_field1'] ) ) : 'Cancel';
			$the_options['button_cancel_link_color1']             = isset( $_POST['gdpr-cookie-cancel-text-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-text-color1'] ) ) : '#ffffff';
			$the_options['button_cancel_button_color1']           = isset( $_POST['gdpr-cookie-cancel-background-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-background-color1'] ) ) : '#333333';
			$the_options['button_cancel_button_opacity1']         = isset( $_POST['gdpr-cookie-cancel-opacity1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-opacity1'] ) ) : '1';
			$the_options['button_cancel_button_border_style1']    = isset( $_POST['gdpr-cookie-cancel-border-style1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-style1'] ) ) : 'none';
			$the_options['button_cancel_button_border_color1']    = isset( $_POST['gdpr-cookie-cancel-border-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-color1'] ) ) : '#333333';
			$the_options['button_cancel_button_border_width1']    = isset( $_POST['gdpr-cookie-cancel-border-width1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-width1'] ) ) : '0';
			$the_options['button_cancel_button_border_radius1']   = isset( $_POST['gdpr-cookie-cancel-border-radius1'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-radius1'] ) ) : '0';
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
			$the_options['button_decline_action2']                = isset( $_POST['gdpr-cookie-decline-action2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-action2'] ) ) : '#cookie_action_close_header_reject';
			$the_options['button_decline_url2']                   = isset( $_POST['gdpr-cookie-decline-url2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-decline-url2'] ) ) : '#';
			$the_options['button_decline_new_win2']               = isset( $_POST['gdpr-cookie-decline-url-new-window2'] ) && ( true === $_POST['gdpr-cookie-decline-url-new-window2'] || 'true' === $_POST['gdpr-cookie-decline-url-new-window2'] ) ? 'true' : 'false';
			$the_options['button_settings_is_on2']                = isset( $_POST['gcc-cookie-settings-enable2'] ) && ( true === $_POST['gcc-cookie-settings-enable2'] || 'true' === $_POST['gcc-cookie-settings-enable2'] ) ? 'true' : 'false';
			$the_options['button_settings_as_popup2']             = isset( $_POST['gcc-iabtcf-enable'] ) && ( true === $_POST['gcc-iabtcf-enable'] || 'true' === $_POST['gcc-iabtcf-enable'] ) ? 'true' : (isset( $_POST['gdpr-cookie-settings-layout2'] ) && ( true === $_POST['gdpr-cookie-settings-layout2'] || 'true' === $_POST['gdpr-cookie-settings-layout2'] ) ? 'true' : 'false');
			$the_options['button_settings_text2']                 = isset( $_POST['button_settings_text_field2'] ) ? sanitize_text_field( wp_unslash( $_POST['button_settings_text_field2'] ) ) : 'Cookie Settings';
			$the_options['button_settings_as_button2']            = isset( $_POST['gdpr-cookie-settings-as2'] ) && ( true === $_POST['gdpr-cookie-settings-as2'] || 'true' === $_POST['gdpr-cookie-settings-as2'] ) ? 'true' : 'false';
			$the_options['button_settings_button_opacity2']       = isset( $_POST['gdpr-cookie-settings-opacity2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-opacity2'] ) ) : '1';
			$the_options['button_settings_button_border_radius2'] = isset( $_POST['gdpr-cookie-settings-border-radius2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-settings-border-radius2'] ) ) : '0';
			$the_options['button_settings_display_cookies2']      = isset( $_POST['gcc-cookie-on-frontend2'] ) && ( true === $_POST['gcc-cookie-on-frontend2'] || 'true' === $_POST['gcc-cookie-on-frontend2'] ) ? 'true' : 'false';
			$the_options['button_confirm_text2']                  = isset( $_POST['button_confirm_text_field2'] ) ? sanitize_text_field( wp_unslash( $_POST['button_confirm_text_field2'] ) ) : 'Confirm';
			$the_options['button_confirm_link_color2']            = isset( $_POST['gdpr-cookie-confirm-text-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-text-color2'] ) ) : '#ffffff';
			$the_options['button_confirm_button_color2']          = isset( $_POST['gdpr-cookie-confirm-background-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-background-color2'] ) ) : '#18a300';
			$the_options['button_confirm_button_opacity2']        = isset( $_POST['gdpr-cookie-confirm-opacity2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-opacity2'] ) ) : '1';
			$the_options['button_confirm_button_border_style2']   = isset( $_POST['gdpr-cookie-confirm-border-style2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-style2'] ) ) : 'none';
			$the_options['button_confirm_button_border_color2']   = isset( $_POST['gdpr-cookie-confirm-border-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-color2'] ) ) : '#18a300';
			$the_options['button_confirm_button_border_width2']   = isset( $_POST['gdpr-cookie-confirm-border-width2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-width2'] ) ) : '0';
			$the_options['button_confirm_button_border_radius2']  = isset( $_POST['gdpr-cookie-confirm-border-radius2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-confirm-border-radius2'] ) ) : '0';
			$the_options['button_cancel_text2']                   = isset( $_POST['button_cancel_text_field2'] ) ? sanitize_text_field( wp_unslash( $_POST['button_cancel_text_field2'] ) ) : 'Cancel';
			$the_options['button_cancel_link_color2']             = isset( $_POST['gdpr-cookie-cancel-text-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-text-color2'] ) ) : '#ffffff';
			$the_options['button_cancel_button_color2']           = isset( $_POST['gdpr-cookie-cancel-background-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-background-color2'] ) ) : '#333333';
			$the_options['button_cancel_button_opacity2']         = isset( $_POST['gdpr-cookie-cancel-opacity2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-opacity2'] ) ) : '1';
			$the_options['button_cancel_button_border_style2']    = isset( $_POST['gdpr-cookie-cancel-border-style2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-style2'] ) ) : 'none';
			$the_options['button_cancel_button_border_color2']    = isset( $_POST['gdpr-cookie-cancel-border-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-color2'] ) ) : '#333333';
			$the_options['button_cancel_button_border_width2']    = isset( $_POST['gdpr-cookie-cancel-border-width2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-width2'] ) ) : '0';
			$the_options['button_cancel_button_border_radius2']   = isset( $_POST['gdpr-cookie-cancel-border-radius2'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-cookie-cancel-border-radius2'] ) ) : '0';
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
			$the_options['button_revoke_consent_text_color1']       = isset( $_POST['gcc-revoke-consent-text-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-text-color1'] ) ) : '';
			$the_options['button_revoke_consent_background_color1'] = isset( $_POST['gcc-revoke-consent-background-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-background-color1'] ) ) : '';
			$the_options['button_revoke_consent_text_color2']       = isset( $_POST['gcc-revoke-consent-text-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-text-color2'] ) ) : '';
			$the_options['button_revoke_consent_background_color2'] = isset( $_POST['gcc-revoke-consent-background-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-background-color2'] ) ) : '';
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
			$the_options['button_revoke_consent_text_color1']       = isset( $_POST['gcc-revoke-consent-text-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-text-color1'] ) ) : '';
			$the_options['button_revoke_consent_background_color1'] = isset( $_POST['gcc-revoke-consent-background-color1'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-background-color1'] ) ) : '';
			$the_options['button_revoke_consent_text_color2']       = isset( $_POST['gcc-revoke-consent-text-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-text-color2'] ) ) : '';
			$the_options['button_revoke_consent_background_color2'] = isset( $_POST['gcc-revoke-consent-background-color2'] ) ? sanitize_text_field( wp_unslash( $_POST['gcc-revoke-consent-background-color2'] ) ) : '';

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
				//script dependency
				$the_options['is_script_dependency_on'] = isset( $_POST['gcc-script-dependency-on'] ) && ( true === $_POST['gcc-script-dependency-on'] || 'true' === $_POST['gcc-script-dependency-on'] ) ? 'true' : 'false';
				$the_options['header_dependency'] = isset( $_POST['gcc-header-dependency'] )? sanitize_text_field( wp_unslash( $_POST['gcc-header-dependency'] ) ): '';
				$the_options['footer_dependency'] = isset( $_POST['gcc-footer-dependency'] )? sanitize_text_field( wp_unslash( $_POST['gcc-footer-dependency'] ) ): '';
				// enable safe mode.
				$the_options['enable_safe'] = isset( $_POST['gcc-enable-safe'] ) && ( true === $_POST['gcc-enable-safe'] || 'true' === $_POST['gcc-enable-safe'] ) ? 'true' : 'false';
				$is_usage_tracking_allowed = 'false';
				if ( isset( $_POST['gcc-usage-data'] ) && ( true === $_POST['gcc-usage-data'] || 'true' === $_POST['gcc-usage-data'] ) ) {
					$is_usage_tracking_allowed = 'true';
				}
				update_option( 'gdpr_usage_tracking_allowed', $is_usage_tracking_allowed );

				// consent forwarding.
				$selected_sites                 = array();
				$selected_sites                 = isset( $_POST['gcc-selected-sites'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-selected-sites'] ) ) ) : '';
				$the_options['consent_forward'] = isset( $_POST['gcc-consent-forward'] ) && ( true === $_POST['gcc-consent-forward'] || 'true' === $_POST['gcc-consent-forward'] ) ? 'true' : 'false';
				$the_options['select_sites']    = $selected_sites;
				$selected_countries             = array();
				$selected_countries             = isset( $_POST['gcc-selected-countries'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-selected-countries'] ) ) ) : '';
				// storing id of pages in database.
				$the_options['select_countries'] = $selected_countries;
				$selected_countries_ccpa             = array();
				$selected_countries_ccpa             = isset( $_POST['gcc-selected-countries-ccpa'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-selected-countries-ccpa'] ) ) ) : '';
				// storing id of pages in database.
				$the_options['select_countries_ccpa'] = $selected_countries_ccpa;
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
				if ( isset( $_POST['gcc-worldwide-enable'] ) && ($the_options['cookie_usage_for'] === 'gdpr' || $the_options['cookie_usage_for'] === 'both') ) {
					if ( filter_var( $the_options['is_worldwide_on'], FILTER_VALIDATE_BOOLEAN ) !==  filter_var( $_POST['gcc-worldwide-enable'], FILTER_VALIDATE_BOOLEAN ) ) {
						$is_maxmind_turned_on = filter_var( $_POST['gcc-worldwide-enable'], FILTER_VALIDATE_BOOLEAN ) ? 'Turned Off' : 'Turned On';
						$data_args = array(
							'Status' => 'Maxmind ' . $is_maxmind_turned_on,
						);
						$this->gdpr_send_shared_usage_data( 'GCC Maxmind Status', $data_args );
					}
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
				// for World wide of CCPA Notice.
				if ( isset( $_POST['gcc-worldwide-enable-ccpa'] ) && ($the_options['cookie_usage_for'] === 'ccpa' || $the_options['cookie_usage_for'] === 'both') ) {
					if ( filter_var( $the_options['is_worldwide_on_ccpa'], FILTER_VALIDATE_BOOLEAN ) !==  filter_var( $_POST['gcc-worldwide-enable-ccpa'], FILTER_VALIDATE_BOOLEAN ) ) {
						$is_maxmind_turned_on = filter_var( $_POST['gcc-worldwide-enable-ccpa'], FILTER_VALIDATE_BOOLEAN ) ? 'Turned Off' : 'Turned On';
						$data_args = array(
							'Status' => 'Maxmind ' . $is_maxmind_turned_on,
						);
						$this->gdpr_send_shared_usage_data( 'GCC Maxmind Status', $data_args );
					}
					if ( 'no' === $_POST['gcc-worldwide-enable-ccpa'] ) {
						$the_options['is_worldwide_on_ccpa'] = 'false';
					} elseif ( 'false' == $_POST['gcc-worldwide-enable-ccpa'] ) {
						$the_options['is_worldwide_on_ccpa'] = 'false';
					} else {
						if(!$the_options['is_worldwide_on_ccpa']){
							$this->disable_auto_update_maxminddb();
						}
						$the_options['is_worldwide_on_ccpa'] = 'true';
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
				if ( isset( $_POST['gcc-select-countries-enable-ccpa'] ) ) {
					if ( 'no' === $_POST['gcc-select-countries-enable-ccpa'] ) {
						$the_options['is_selectedCountry_on_ccpa'] = 'false';
					} elseif ( 'false' == $_POST['gcc-select-countries-enable-ccpa'] ) {
						$the_options['is_selectedCountry_on_ccpa'] = 'false';
					} else {
						if(!$the_options['is_selectedCountry_on_ccpa']){
							$this->auto_update_maxminddb();
							$this->download_maxminddb();
						}
						$the_options['is_selectedCountry_on_ccpa'] = 'true';
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
				

				$template      = isset( $_POST['gdpr-template'] ) ? sanitize_text_field( wp_unslash( $_POST['gdpr-template'] ) ) : 'none';
				$cookie_bar_as = $the_options['cookie_bar_as'];
				if ( 'none' !== $template && $saved_options['template'] !== $template ) {
					
					$the_options['template']                     = $template;
					if($template != "default"  && isset($this->templates_json[$template])) $the_options['selected_template_json'] 		 = json_encode($this->templates_json[$template]);
					else $the_options['selected_template_json'] 							 = json_encode(get_option('gdpr_default_template_object'));
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
				$the_options['enable_safe']          = isset( $_POST['gcc-enable-safe'] ) && ( true === $_POST['gcc-enable-safe'] || 'true' === $_POST['gcc-enable-safe'] ) ? 'true' : 'false';
				$the_options['is_script_blocker_on'] = isset( $_POST['gcc-script-blocker-on'] ) && ( true === $_POST['gcc-script-blocker-on'] || 'true' === $_POST['gcc-script-blocker-on'] ) ? 'true' : 'false';
				//Script Dependency
				$the_options['is_script_dependency_on'] = isset( $_POST['gcc-script-dependency-on'] ) && ( true === $_POST['gcc-script-dependency-on'] || 'true' === $_POST['gcc-script-dependency-on'] ) ? 'true' : 'false';
				$the_options['header_dependency'] = isset( $_POST['gcc-header-dependency'] )? sanitize_text_field( wp_unslash( $_POST['gcc-header-dependency'] ) ): '';
				$the_options['footer_dependency'] = isset( $_POST['gcc-footer-dependency'] )? sanitize_text_field( wp_unslash( $_POST['gcc-footer-dependency'] ) ): '';
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
					
					$the_options['template']                     = $template;
					if($template != "default") $the_options['selected_template_json'] 		 = json_encode($this->templates_json[$template]);
					else $the_options['selected_template_json'] = json_encode(get_option('gdpr_default_template_object'));
				}
			}
			// language translation based on the selected language.
			if ( $_POST['lang_changed'] == 'true' && isset( $_POST['select-banner-lan'] ) && in_array( $_POST['select-banner-lan'], $this->supported_languages ) ) {  //phpcs:ignore
				$the_options = $this->changeLanguage($the_options);				
			}
			
			if ( isset( $_POST['logo_removed'] ) && 'true' == $_POST['logo_removed'] ) {
				update_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD, '' );
			}
			if ( isset( $_POST['logo_removed1'] ) && 'true' == $_POST['logo_removed1'] ) {
				update_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1, '' );
			}
			if ( isset( $_POST['logo_removed2'] ) && 'true' == $_POST['logo_removed2'] ) {
				update_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD2, '' );
			}
			if ( isset( $_POST['logo_removedML1'] ) && 'true' == $_POST['logo_removedML1'] ) {
				update_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELDML1, '' );
			}

			if ( isset( $_POST['gdpr-cookie-bar-logo-url-holder'] ) && ! empty( $_POST['gdpr-cookie-bar-logo-url-holder'] ) ) {
				$url = esc_url_raw( wp_unslash( $_POST['gdpr-cookie-bar-logo-url-holder'] ) );
				if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
					update_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD, $url );
				}
			}

			if ( isset( $_POST['gdpr-cookie-bar-logo-url-holder1'] ) && ! empty( $_POST['gdpr-cookie-bar-logo-url-holder1'] ) ) {
				$url = esc_url_raw( wp_unslash( $_POST['gdpr-cookie-bar-logo-url-holder1'] ) );
				if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
					update_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1, $url );
				}
			}

			if ( isset( $_POST['gdpr-cookie-bar-logo-url-holder2'] ) && ! empty( $_POST['gdpr-cookie-bar-logo-url-holder2'] ) ) {
				$url = esc_url_raw( wp_unslash( $_POST['gdpr-cookie-bar-logo-url-holder2'] ) );
				if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
					update_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD2, $url );
				}
			}

			if ( isset( $_POST['gdpr-cookie-bar-logo-url-holderML1'] ) && ! empty( $_POST['gdpr-cookie-bar-logo-url-holderML1'] ) ) {
				$url = esc_url_raw( wp_unslash( $_POST['gdpr-cookie-bar-logo-url-holderML1'] ) );
				if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
					update_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELDML1, $url );
				}
			}
			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );

			wp_send_json_success( array( 'form_options_saved' => true ) );
		}
		
	}

	/**
	 * Ajax callback to save advanced settings.
	 */
	public function gdpr_cookie_consent_ajax_save_advanced_settings() {
		if ( isset( $_POST['gcc_settings_form_nonce_advanced'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gcc_settings_form_nonce_advanced'] ) ), 'gcc-settings-form-nonce-advanced' ) ) {
				return;
			}
			
			$the_options    = Gdpr_Cookie_Consent::gdpr_get_settings();
			$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' );

			if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
				// hide banner.
				$selected_pages = array();
				$selected_pages = isset( $_POST['gcc-selected-pages'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-selected-pages'] ) ) ) : '';
				// storing id of pages in database.
				$the_options['select_pages'] = $selected_pages;
			}

			// DO NOT TRACK.
			$the_options['do_not_track_on'] = isset( $_POST['gcc-do-not-track'] ) && ( true === $_POST['gcc-do-not-track'] || 'true' === $_POST['gcc-do-not-track'] ) ? 'true' : 'false';
			// Consent log
			$the_options['logging_on'] = isset( $_POST['gcc-logging-on'] ) && ( true === $_POST['gcc-logging-on'] || 'true' === $_POST['gcc-logging-on'] ) ? 'true' : 'false';

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
			
			if( !get_option( 'wpl_pro_active' )) {
				// enable safe mode.
				$the_options['enable_safe'] = isset( $_POST['gcc-enable-safe'] ) && ( true === $_POST['gcc-enable-safe'] || 'true' === $_POST['gcc-enable-safe'] ) ? 'true' : 'false';
				$is_usage_tracking_allowed = 'false';
				if ( isset( $_POST['gcc-usage-data'] ) && ( true === $_POST['gcc-usage-data'] || 'true' === $_POST['gcc-usage-data'] ) ) {
					$is_usage_tracking_allowed = 'true';
				}
				update_option( 'gdpr_usage_tracking_allowed', $is_usage_tracking_allowed );
				// consent log.
				$the_options['logging_on'] = isset( $_POST['gcc-logging-on'] ) && ( true === $_POST['gcc-logging-on'] || 'true' === $_POST['gcc-logging-on'] ) ? 'true' : 'false';
				// consent forwarding.
				$selected_sites                 = array();
				$selected_sites                 = isset( $_POST['gcc-selected-sites'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-selected-sites'] ) ) ) : '';
				$the_options['consent_forward'] = isset( $_POST['gcc-consent-forward'] ) && ( true === $_POST['gcc-consent-forward'] || 'true' === $_POST['gcc-consent-forward'] ) ? 'true' : 'false';
				$the_options['select_sites']    = $selected_sites;
				$selected_countries             = array();
				$selected_countries             = isset( $_POST['gcc-selected-countries'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-selected-countries'] ) ) ) : '';	
			}

			if ( get_option( 'wpl_pro_active' ) && get_option( 'wc_am_client_wpl_cookie_consent_activated' ) && 'Activated' === get_option( 'wc_am_client_wpl_cookie_consent_activated' ) ) {
				// consent forward .
				$selected_sites                      = array();
				$selected_sites                      = isset( $_POST['gcc-selected-sites'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_POST['gcc-selected-sites'] ) ) ) : '';
				
				$the_options['restrict_posts']       = $restricted_posts;
				// consent forward .
				$the_options['consent_forward'] = isset( $_POST['gcc-consent-forward'] ) && ( true === $_POST['gcc-consent-forward'] || 'true' === $_POST['gcc-consent-forward'] ) ? 'true' : 'false';
				$the_options['select_sites']    = $selected_sites;	
			}

			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );

			wp_send_json_success( array( 'form_options_saved' => true ) );
		}
	}

	/**
	 * AB Testing callback to save settings.
	 */
	public function gdpr_cookie_consent_ajax_save_abtesting_settings() {
		if ( isset( $_POST['gcc_settings_form_nonce_abtesting'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gcc_settings_form_nonce_abtesting'] ) ), 'gcc-settings-form-nonce-abtesting' ) ) {
				return;
			}

			$this->gdpr_ab_testing_complete();

			wp_send_json_success( array( 'form_options_saved' => true ) );
		}
	}

	/**
	 * Script Blocker callback to save settings.
	 */
	public function gdpr_cookie_consent_ajax_save_script_blocker_settings() {
		if ( isset( $_POST['gcc_settings_form_nonce_script_blocker'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gcc_settings_form_nonce_script_blocker'] ) ), 'gcc-settings-form-nonce-script-blocker' ) ) {
				return;
			}

			$the_options    = Gdpr_Cookie_Consent::gdpr_get_settings();
			$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' );

			$the_options['header_scripts']                        = isset( $_POST['gcc-header-scripts'] ) ? wp_unslash( $_POST['gcc-header-scripts'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$the_options['body_scripts']                          = isset( $_POST['gcc-body-scripts'] ) ? wp_unslash( $_POST['gcc-body-scripts'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$the_options['footer_scripts']                        = isset( $_POST['gcc-footer-scripts'] ) ? wp_unslash( $_POST['gcc-footer-scripts'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			
			if ( ! get_option( 'wpl_pro_active' ) ) {
				// script blocker.
				$the_options['is_script_blocker_on'] = isset( $_POST['gcc-script-blocker-on'] ) && ( true === $_POST['gcc-script-blocker-on'] || 'true' === $_POST['gcc-script-blocker-on'] ) ? 'true' : 'false';
				//script dependency
				$the_options['is_script_dependency_on'] = isset( $_POST['gcc-script-dependency-on'] ) && ( true === $_POST['gcc-script-dependency-on'] || 'true' === $_POST['gcc-script-dependency-on'] ) ? 'true' : 'false';
				$the_options['header_dependency'] = isset( $_POST['gcc-header-dependency'] )? sanitize_text_field( wp_unslash( $_POST['gcc-header-dependency'] ) ): '';
				$the_options['footer_dependency'] = isset( $_POST['gcc-footer-dependency'] )? sanitize_text_field( wp_unslash( $_POST['gcc-footer-dependency'] ) ): '';
				// enable safe mode.
				$the_options['enable_safe'] = isset( $_POST['gcc-enable-safe'] ) && ( true === $_POST['gcc-enable-safe'] || 'true' === $_POST['gcc-enable-safe'] ) ? 'true' : 'false';
				
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

			if ( get_option( 'wpl_pro_active' ) && get_option( 'wc_am_client_wpl_cookie_consent_activated' ) && 'Activated' === get_option( 'wc_am_client_wpl_cookie_consent_activated' ) ) {
				$the_options['is_script_blocker_on'] = isset( $_POST['gcc-script-blocker-on'] ) && ( true === $_POST['gcc-script-blocker-on'] || 'true' === $_POST['gcc-script-blocker-on'] ) ? 'true' : 'false';
				//Script Dependency
				$the_options['is_script_dependency_on'] = isset( $_POST['gcc-script-dependency-on'] ) && ( true === $_POST['gcc-script-dependency-on'] || 'true' === $_POST['gcc-script-dependency-on'] ) ? 'true' : 'false';
				$the_options['header_dependency'] = isset( $_POST['gcc-header-dependency'] )? sanitize_text_field( wp_unslash( $_POST['gcc-header-dependency'] ) ): '';
				$the_options['footer_dependency'] = isset( $_POST['gcc-footer-dependency'] )? sanitize_text_field( wp_unslash( $_POST['gcc-footer-dependency'] ) ): '';
				
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

			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );

			wp_send_json_success( array( 'form_options_saved' => true ) );
		}
	}

	/**
	 * Language callback to save settings.
	 */
	public function gdpr_cookie_consent_ajax_save_language_settings() {
		if ( isset( $_POST['gcc_settings_form_nonce_language'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gcc_settings_form_nonce_language'] ) ), 'gcc-settings-form-nonce-language' ) ) {
				return;
			}

			$the_options    = Gdpr_Cookie_Consent::gdpr_get_settings();
			$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' );

			$the_options['lang_selected'] = isset( $_POST['select-banner-lan'] ) ? sanitize_text_field( wp_unslash( $_POST['select-banner-lan'] ) ) : 'en';

			// language translation based on the selected language.
			if ( isset( $_POST['select-banner-lan'] ) && in_array( $_POST['select-banner-lan'], $this->supported_languages ) ) {  //phpcs:ignore
				$the_options = $this->changeLanguage($the_options);				
			}

			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );

			wp_send_json_success( array( 'form_options_saved' => true ) );
		}
	}

	/**
	 * Cookie Manager callback to save settings.
	 */
	public function gdpr_cookie_consent_ajax_save_cookie_manager_settings() {
		if ( isset( $_POST['gcc_settings_form_nonce_cookie_manager'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gcc_settings_form_nonce_cookie_manager'] ) ), 'gcc-settings-form-nonce-cookie-manager' ) ) {
				return;
			}

			$the_options    = Gdpr_Cookie_Consent::gdpr_get_settings();
			$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' );

			//update values here

			update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );

			wp_send_json_success( array( 'form_options_saved' => true ) );
		}
	}

	/**
	 * Function to enable IAB and download vendor list
	 */
	public function gdpr_cookie_consent_ajax_enable_iab(){
		$received_data = json_decode(stripslashes($_POST['data']));
		update_option(GDPR_COOKIE_CONSENT_SETTINGS_VENDOR, $received_data);
	}

	/**
	 * activate auto updater for gacm vendor data
	 *
	 * @since    3.7.0
	 */
	public function activate_gacm_updater() {
		if (!wp_next_scheduled('refresh_gacm_vendor_list_event')) {
			$one_week_later = time() + (7 * 24 * 60 * 60);
			wp_schedule_event($one_week_later, 'weekly', 'refresh_gacm_vendor_list_event');
		}
	}

	/**
	 * deactivate auto updater for gacm vendor data
	 *
	 * @since    3.7.0
	 */
	public function deactivate_gacm_updater() {
		$timestamp = wp_next_scheduled('refresh_gacm_vendor_list_event');
		if ($timestamp) {
			wp_unschedule_event($timestamp, 'refresh_gacm_vendor_list_event');
		}
	}

	/**
	 * Get the google additional connsent mode vendors and update in db
	 *
	 * @since    3.7.0
	 */
	public function get_gacm_data() {

		$url = 'https://storage.googleapis.com/tcfac/additional-consent-providers.csv';

		// Use file_get_contents to fetch the CSV data
		$data = file_get_contents($url);

		// Check if the data was fetched successfully
		if ($data === false) {
			die('Error fetching data from the URL.');
		}

		// Parse the CSV data
		$rows = array_map('str_getcsv', explode("\n", $data));
		array_shift($rows);
		update_option(GDPR_COOKIE_CONSENT_SETTINGS_GACM_VENDOR,$rows);
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
				$ab_options ['noChoice1']   = 0;
				$ab_options ['noChoice2']   = 0;
				$ab_options ['accept1']   = 0;
				$ab_options ['accept2']   = 0;
				$ab_options ['acceptAll1']   = 0;
				$ab_options ['acceptAll2']   = 0;
				$ab_options ['reject1']   = 0;
				$ab_options ['reject2']   = 0;
				$ab_options ['bypass1']   = 0;
				$ab_options ['bypass2']   = 0;

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

	public function change_option_language( $key, $language ) {
		$translations_file = plugin_dir_path( __FILE__ ) . 'translations/translations.json';

		if ( file_exists( $translations_file ) ) {
			$translations = json_decode( file_get_contents( $translations_file ), true );
		}

		$translated_key = $this->translated_text( $key, $translations, $language );

		return $translated_key;
	}

	/**
	 * Function to change the language
	 */
	public function changeLanguage($the_options){
		$translations_file = plugin_dir_path( __FILE__ ) . 'translations/translations.json';

				if ( file_exists( $translations_file ) ) {
				    $translations = file_get_contents( $translations_file );
				    $translations = json_decode( $translations, true );
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

				$target_language = isset( $the_options['lang_selected'] ) ? $the_options['lang_selected'] : 'en';   //phpcs:ignore
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
								'timeout' => 60
							)
					);
					if (is_wp_error($response)) {
					} else {
						$status_code = wp_remote_retrieve_response_code($response);
						if (200 === $status_code) {
							$file_data = wp_remote_retrieve_body($response);
							if(file_exists($database_path)) wp_delete_file($database_path);
							file_put_contents($database_path, $file_data);
						}
					}
				} catch (Exception $e) {
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
								'timeout' => 60
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

		//Script dependency
		$header_dependency_list = array('Body Scripts', 'Footer Scripts');
		$footer_dependency_list = array('Header Scripts', 'Body Scripts');

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
				'templates'     				   => $this -> templates_json,
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
				'accept_action_options'            => $accept_action_options,
				'accept_button_as_options'         => $accept_button_as_options,
				'open_url_options'                 => $open_url_options,
				'widget_position_options'          => $widget_position_options,
				'decline_action_options'           => $decline_action_options,
				'script_blocker_settings'          => $script_blocker_settings,
				'font_options'                     => $font_options,
				'layout_skin_options'              => $layout_skin_options,
				'cookie_list_settings'             => $cookie_list_settings,
				'cookie_scan_settings'             => $cookie_scan_settings,
				'restore_settings_nonce'           => wp_create_nonce( 'restore_default_settings' ),
				// hide banner.
				'list_of_pages'                    => $list_of_pages,
				//dependency list
				'header_dependency_list'		   => $header_dependency_list,
				'footer_dependency_list'		   => $footer_dependency_list,
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
				$template_parts_background = '#2A3E71';
			} elseif ( in_array( $template, array( 'almond_column' ), true ) ) {
				$template_parts_background = '#FFFFFF';
			} elseif ( in_array( $template, array( 'grey_column', 'grey_center' ), true ) ) {
				$template_parts_background = '#FFFFFF';
			} elseif ( in_array( $template, array( 'dark' ), true ) ) {
				$template_parts_background = '#262626';
			} elseif ( in_array( $template, array( 'dark_row' ), true ) ) {
				$template_parts_background = '#323742';
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
				$template_parts_background = '#2A3E71';
			} elseif ( in_array( $template, array( 'almond_column' ), true ) ) {
				$template_parts_background = '#FFFFFF';
			} elseif ( in_array( $template, array( 'grey_column', 'grey_center' ), true ) ) {
				$template_parts_background = '#FFFFFF';
			} elseif ( in_array( $template, array( 'dark' ), true ) ) {
				$template_parts_background = '#262626';
			} elseif ( in_array( $template, array( 'dark_row' ), true ) ) {
				$template_parts_background = '#323742';
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
		$is_legal_page_exist = $this->gdpr_get_legal_page_generated_count() > 0 ? '1' : '0';
		$all_legal_pages_url = $admin_url . 'admin.php?page=legal-pages#all_legal_pages';
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
				'total_page_views'		=> $total_page_views,
				'is_legalpages_active'  => is_plugin_active( 'wplegalpages/wplegalpages.php' ),
				'is_legal_page_exist'   => $is_legal_page_exist,
				'all_legal_pages_url'   => $all_legal_pages_url,
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
			$this->gdpr_cookie_consent_restore_default_settings();
			wp_send_json_success( array( 'restore_default_saved' => true ) );
		}
	}

	/**
	 * Restore Settings to Default.
	 */
	public function gdpr_cookie_consent_restore_default_settings() {
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
		$ab_options ['noChoice1']   = 0;
		$ab_options ['noChoice2']   = 0;
		$ab_options ['accept1']   = 0;
		$ab_options ['accept2']   = 0;
		$ab_options ['acceptAll1']   = 0;
		$ab_options ['acceptAll2']   = 0;
		$ab_options ['reject1']   = 0;
		$ab_options ['reject2']   = 0;
		$ab_options ['bypass1']   = 0;
		$ab_options ['bypass2']   = 0;
		update_option( 'wpl_ab_options', $ab_options );
		delete_transient( 'gdpr_ab_testing_transient' );
		// Reset the preview banner state to false
		update_option('gdpr_preview_banner_state', 'false');
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
	/**
	 * Function to switch preview banner state
	 */
	public function gdpr_cookie_consent_ajax_switch_preview_banner(){
		$banner_preview_state = sanitize_text_field($_POST['banner_preview_state']);
		$banner_preview_state = ($banner_preview_state === 'true' || $banner_preview_state === true) ? 'true' : 'false';
		
    	update_option('gdpr_preview_banner_state', $banner_preview_state);
		wp_send_json_success( $banner_preview_state );
	}

	/**
	 * Function to get the preview banner state
	 */

	public function gdpr_cookie_consent_ajax_get_preview_banner_state(){
		$state = get_option('gdpr_preview_banner_state', 'false');
		wp_send_json_success($state);
	}
	/**
	 * Function to save the scan schedule
	 */
	public function gdpr_cookie_consent_ajax_save_schedule_scan() {
		$schedule_data = array(
			'schedule_scan_as' => sanitize_text_field($_POST['schedule_scan_as']),
			'schedule_scan_date' => sanitize_text_field($_POST['schedule_scan_date']),
			'schedule_scan_time_value' => sanitize_text_field($_POST['schedule_scan_time_value']),
			'schedule_scan_day' => sanitize_text_field($_POST['schedule_scan_day']),
			'next_scan_is_when' => sanitize_text_field($_POST['next_scan_is_when']),
			'schedule_scan_when' => sanitize_text_field($_POST['next_scan_is_when']),
		);
		update_option('gdpr_scan_schedule_data', $schedule_data);
		wp_send_json_success();
	}

	/**
	 * Function to get the scan schedule
	 */
	public function gdpr_cookie_consent_ajax_get_schedule_scan() {
		$data = get_option('gdpr_scan_schedule_data', array());
		wp_send_json_success($data);
	}
	/**
	 * Function to clear the scan schedule
	 */
	public function gdpr_cookie_consent_ajax_clear_schedule_scan() {
		delete_option('gdpr_scan_schedule_data');
		wp_send_json_success(array('message' => 'Schedule cleared'));
	}

	/* Added endpoint to send dashboard data from plugin to the saas appwplp server */
	public function gdpr_send_data_to_dashboard_appwplp_server(WP_REST_Request $request  ){		
		$current_user = wp_get_current_user();
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		//$cookie_scan_settings = apply_filters( 'gdpr_settings_cookie_scan_values', '' );
		$cookie_scan_class = new Gdpr_Cookie_Consent_Cookie_Scanner(); 
		$cookie_scan_settings = $cookie_scan_class->wpl_settings_cookie_scan_values();
		$default_settings = '';
		$client_site_name = get_bloginfo('name');

		
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

		$is_other_cookie_plugin_activated = $this->gdpr_ensure_no_other_cookie_plugins_activated( $active_plugins );
		
		return rest_ensure_response(
			array(
				'success' => true,
				'last_scan_time'             	   => $last_scan_time,
				$saved_schedule_data = get_option('gdpr_scan_schedule_data', array()),
				$schedule_scan_when = isset($saved_schedule_data['schedule_scan_when']) ? $saved_schedule_data['schedule_scan_when'] : null,
				'schedule_scan_when' => $schedule_scan_when,
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
				'is_other_cookie_plugin_activated' => $is_other_cookie_plugin_activated,
				'client_site_url'                  => get_site_url(),
				'cookie_usage_for'                 => $gdpr_policy,
				'user_email_id'					   => $user_email_id,
				'location_status'				   => $locationStatus,
				'client_site_name'				   => $client_site_name,
				'api_secret' 					   => get_option('wplegalpages_api_secret'),
			)
		);
	}


	/* Added endpoint to send dashboard data from plugin to the saas appwplp React dashboard */
	public function gdpr_send_data_to_dashboard_appwplp_react_app( WP_REST_Request $request ){
		ob_start();

		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		//$cookie_scan_settings = apply_filters( 'gdpr_settings_cookie_scan_values', '' );
		$cookie_scan_class = new Gdpr_Cookie_Consent_Cookie_Scanner(); 
		$cookie_scan_settings = $cookie_scan_class->wpl_settings_cookie_scan_values();
		
		// Require the class file for gdpr cookie consent api framework settings.
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';

		$this->settings = new GDPR_Cookie_Consent_Settings();
		$api_user_plan     = $this->settings->get_plan();
		$product_id = $this->settings->get( 'account', 'product_id' );

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
		
		$last_scan_time = $cookie_scan_settings['last_scan']['created_at'];

		$active_plugins = $this->gdpr_cookie_consent_active_plugins();

		$is_other_cookie_plugin_activated = $this->gdpr_ensure_no_other_cookie_plugins_activated( $active_plugins );
		
		$monthly_scan = 0;
		$monthly_scan_limit = 1e7;
		if( $api_user_plan == 'free' ) {
			$monthly_scan = get_transient( 'gdpr_monthly_scan_limit_exhausted' );
			$monthly_scan = (int) $monthly_scan;
			$monthly_scan_limit = 5;
		}

		$gdpr_pages_scanned = get_option('gdpr_no_of_page_scan', 0);

		$gdpr_monthly_page_views = get_option('wpl_monthly_page_views', 0);

		$saved_schedule_data = get_option('gdpr_scan_schedule_data', array());
		$schedule_scan_when = isset($saved_schedule_data['schedule_scan_when']) ? $saved_schedule_data['schedule_scan_when'] : null;

		$req = new WP_REST_Request();
		$req->set_param('number', 5);
		$req->set_param('offset', 0);

		// Call the function directly
		$result = $this->gdpr_send_consent_log_data_to_react_app($req);

		ob_end_clean();

		return rest_ensure_response(
			array(
				'success' 						   => true,
				'user_plan'						   => $api_user_plan,
				'product_id'                       => $product_id,
				'last_scan_time'             	   => $last_scan_time,
				'schedule_scan_time'               => $schedule_scan_when,
				'total_cookies'                    => $total_no_of_found_cookies,
				'total_scanned_pages'              => $total_scanned_pages,
				'categories'          			   => $number_of_categories,
				'wpl_cl_decline'                   => get_option( 'wpl_cl_decline' ),
				'wpl_cl_accept'                    => get_option( 'wpl_cl_accept' ),
				'wpl_cl_partially_accept'          => get_option( 'wpl_cl_partially_accept' ),
				'wpl_cl_bypass'                    => get_option( 'wpl_cl_bypass' ),
				'wpl_page_views'				   => get_option( 'wpl_page_views' ),
				'total_page_views'				   => get_option('wpl_total_page_views'),
				'site_on'				           => $the_options['is_on'],
				'other_cookie_plugin_activated'    => $is_other_cookie_plugin_activated,
				'law_type'                         => $gdpr_policy,
				'monthly_scan'					   => $monthly_scan,
				'monthly_scan_limit'			   => $monthly_scan_limit,
				'total_pages_scanned'		 	   => $gdpr_pages_scanned,
				'monthly_page_views'			   => $gdpr_monthly_page_views,
				'consent_log_data' 				   => $result['logs'],
				'cookie_banner_created_once'       => get_option('wplp_cookie_banner_created_once'),
			)
		);
	}

	/* Send Wizard section data to React dashboard */
	public function wplp_send_wizard_data_to_react_app( WP_REST_Request $request ) {
		ob_start();

		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();

		$geo_countries     = isset( $geo_countries ) ? $geo_countries : array();
		$response          = wp_remote_get( plugin_dir_url( __FILE__ ) . 'data/countries.json', array( 'sslverify' => false ) );
		$json_data         = wp_remote_retrieve_body( $response );
		$geo_countries     = json_decode( $json_data, true );
		$list_of_countries = array();
		$index             = 0;
		$plan                = $this->settings->get_plan();
		foreach ( $geo_countries as $code => $country ) {
			$list_of_countries[ $index ] = array(
				'label' => $country['name'],
				'code'  => $country['code'],
			);
			++$index;
		}

		$cookie_banner_created_once = get_option('wplp_cookie_banner_created_once');

		if ( $cookie_banner_created_once === false ) {
		    add_option('wplp_cookie_banner_created_once', "false");
		    $cookie_banner_created_once = "false";
		}

		ob_end_clean();

		return rest_ensure_response(
			array(
				'success'							=> true,
				'cookie_usage_for'					=> $the_options['cookie_usage_for'],
				'enable_safe' 						=> $the_options['enable_safe'],
				'plan'								=> $plan,
				
				// Geo-targetting GDPR
				'is_worldwide_on'					=> $the_options['is_worldwide_on'],
				'is_eu_on'							=> $the_options["is_eu_on"],
				'is_selectedCountry_on'				=> $the_options["is_selectedCountry_on"],
				'list_of_countries'					=> $list_of_countries,
				'select_countries'					=> $the_options["select_countries"],

				// Geo-targetting CCPA
				'is_worldwide_on_ccpa'				=> $the_options["is_worldwide_on_ccpa"],
				'is_ccpa_on'						=> $the_options['is_ccpa_on'],
				'is_selectedCountry_on_ccpa'		=> $the_options["is_selectedCountry_on_ccpa"],
				'select_countries_ccpa'				=> $the_options["select_countries_ccpa"],

				'logging_on'						=> $the_options['logging_on'],
				'is_script_blocker_on'				=> $the_options["is_script_blocker_on"],
				'do_not_track_on'					=> $the_options["do_not_track_on"],
				'data_reqs_on'						=> $the_options['data_reqs_on'],
				'data_req_email_address'			=> $the_options['data_req_email_address'],
				'data_req_subject'					=> $the_options['data_req_subject'],
				'data_req_editor_message'			=> $the_options['data_req_editor_message'],
				'cookie_bar_as'						=> $the_options["cookie_bar_as"],
				'notify_position_vertical'			=> $the_options["notify_position_vertical"],
				'notify_position_horizontal'		=> $the_options["notify_position_horizontal"],
				'selected_template_json'			=> $the_options['selected_template_json'],
				'bar_heading_text'					=> $the_options['bar_heading_text'],
				'bar_heading_lgpd_text'				=> $the_options['bar_heading_lgpd_text'],
				'notify_message'					=> $the_options["notify_message"],
				'notify_message_lgpd'				=> $the_options["notify_message_lgpd"],
				'notify_message_ccpa'				=> $the_options["notify_message_ccpa"],
				'notify_message_eprivacy'			=> $the_options["notify_message_eprivacy"],
				'button_readmore_text'				=> $the_options["button_readmore_text"],
				'button_donotsell_text'				=> $the_options["button_donotsell_text"],
				'button_decline_text'				=> $the_options["button_decline_text"],
				'button_settings_text'				=> $the_options["button_settings_text"],
				'button_accept_text'				=> $the_options["button_accept_text"],
				'button_accept_all_text'			=> $the_options["button_accept_all_text"],
				'cookie_banner_created_once'		=> $cookie_banner_created_once,
			)
		);
	}

	/* Send consent log data to React dashboard */
	public function gdpr_send_consent_log_data_to_react_app( WP_REST_Request $request ) {

	    $number  = intval($request->get_param('number')) ?: 50;
	    $offset  = intval($request->get_param('offset')) ?: 0;
	    $ip      = $request->get_param('ip') ?: '';
	    $country = $request->get_param('country') ?: '';

	    $meta_query = [];

	    if ($ip !== '') {
	        $meta_query[] = [
	            'key'     => '_wplconsentlogs_ip',
	            'value'   => $ip,
        		'compare' => 'REGEXP',
	        ];
	    }

	    if ($country !== '') {
	        $meta_query[] = [
	            'key'     => '_wplconsentlogs_country',
	            'value'   => $country,
	            'compare' => 'REGEXP',
	        ];
	    }

	    // Main Query
	    $args = [
	        'post_type'      => 'wplconsentlogs',
	        'post_status'    => 'publish',
	        'posts_per_page' => $number,
	        'offset'         => $offset,
	        'meta_query'     => $meta_query,
	    ];

	    $query = new WP_Query($args);

	    // Count Query (for correct pagination)
	    $count_args = $args;
	    $count_args['posts_per_page'] = -1;
	    $count_args['offset'] = 0;
	    $total_count = (new WP_Query($count_args))->found_posts;

	    $posts = $query->posts;
	    $data = [];

	    foreach ($posts as $post) {
	        $utc = $post->post_date_gmt;
	        $timestamp = get_date_from_gmt($utc, 'U');
	        $formatted_date = date('d/m/Y', $timestamp);

	        $details = get_post_meta($post->ID, '_wplconsentlogs_details', true);
	        $consent_status = $this->calculate_consent_status($details);

			$scanner = new Gdpr_Cookie_Consent_Cookie_Scanner();
			$scan_cookie_list = $scanner->get_scan_cookie_list();

	        $data[] = [
	            'ID'             		=> $post->ID,
	            'ip'             		=> get_post_meta($post->ID, '_wplconsentlogs_ip', true),
	            'country'        		=> get_post_meta($post->ID, '_wplconsentlogs_country', true),
	            'consent_status' 		=> wp_strip_all_tags($consent_status),
	            'date'           		=> $formatted_date,
	        ];
	    }

	    return [
	        'total_records' => $total_count,
	        'logs'          => $data,
	    ];
	}

	private function calculate_consent_status($details) {
	    if (!$details) return '';

	    $viewed = $details['wpl_viewed_cookie'] ?? '';
	    $pref   = $details['wpl_user_preference'] ?? '';
	    $optout = $details['wpl_optout_cookie'] ?? '';

	    $is_all_yes = false;
	    if ($pref) {
	        $decoded = html_entity_decode($pref);
	        $array   = json_decode($decoded, true);

	        if (is_array($array)) {
	            $is_all_yes = !in_array('no', $array, true);
	        }
	    }

	    if ($viewed === 'unset') {
	        return 'Bypassed';
	    } elseif ($optout === 'yes' || $viewed === 'no') {
	        return 'Rejected';
	    } elseif ($is_all_yes || $optout === 'no') {
	        return 'Approved';
	    } else {
	        return 'Partially Accepted';
	    }
	}

	/* Delete consent logs callback for React app */
	public function delete_consent_logs_for_react_app( WP_REST_Request $request ) {
	    $ids = $request->get_param('id');

	    if (!is_array($ids)) {
	        $ids = [$ids];
	    }

	    $deleted = [];
	    
		foreach ($ids as $id) {
	        $id = (int) $id;
	        
			if ($id < 0) continue;

			$post = get_post($id);

			if (!$post) {
				continue;
			}

			if ($post->post_type !== 'wplconsentlogs') {
				continue;
			}

			wp_delete_post($id, true);
			$deleted[] = $id;
	    }

	    return [ 'deleted' => $deleted ];
	}

	public function generate_consent_pdf_for_react_app( WP_REST_Request $request ) {

		$post_id = (int) $request->get_param('id');
    	if (!$post_id) {
    	    return new WP_Error('invalid_id', 'Missing ID', array('status'=>400));
    	}
	
    	$post = get_post($post_id);
    	if (!$post || $post->post_type !== 'wplconsentlogs') {
    	    return new WP_Error('not_found', 'Record not found', array('status'=>404));
    	}
	
		$details = get_post_meta($post_id, '_wplconsentlogs_details', true);
		$tcString      = isset($details['wpl_tc_string']) ?  $details['wpl_tc_string'] : '';
		$acString	   = isset($details['Additional_Consent_String']) ?  $details['Additional_Consent_String'] : '';
	    $consent_status = $this->calculate_consent_status($details);

		$wpl_user_preference = isset( $details['wpl_user_preference'] ) ? json_decode( $details['wpl_user_preference'] ) : '';
		$preferencesDecoded        = '';
		if ( isset( $wpl_user_preference ) && isset( $details['wpl_user_preference'] ) ) {
			$preferencesDecoded = wp_json_encode( $wpl_user_preference );
		}

		$custom              = get_post_custom();
		$forwarded_site_url  = isset( $custom['_wplconsentlogs_siteurl_cf'][0] ) ? $custom['_wplconsentlogs_siteurl_cf'][0] : '';
		$is_consent_status   = isset( $custom['_wplconsentlogs_consent_forward_cf'][0] ) ? $custom['_wplconsentlogs_consent_forward_cf'][0] : '';

		$siteurl             = site_url();
		$siteurl             = trailingslashit( $siteurl );
		
		if ( $siteurl !== $forwarded_site_url ) {
			$siteaddress = $forwarded_site_url;
		} else {
			$siteaddress = $siteurl;
		}
	
    	return array(
    	    'date' 			=> $post->post_date_gmt,
    	    'ip'   			=> get_post_meta($post_id, '_wplconsentlogs_ip', true),
    	    'country' 		=> get_post_meta($post_id, '_wplconsentlogs_country', true),
			'consent_status'=> wp_strip_all_tags($consent_status),
    	    'tcString'		=> $tcString,
    	    'acString' 		=> $acString,
			'siteAddress'	=> $siteaddress,
    	    'preferences' 	=> $preferencesDecoded,
    	    'cookies' 		=> (new Gdpr_Cookie_Consent_Cookie_Scanner())->get_scan_cookie_list()
    	);
	}



	public function gdpr_send_data_requests_to_react_app( WP_REST_Request $request ) {
		$number  = 10;
		$offset  = intval($request->get_param('offset')) ?: 0;
		$email   = $request->get_param('email') ?: '';
		$filter  = $request->get_param('statusFilter') ?: '';
		$all     = $request->get_param('all') ?: false;

		global $wpdb;
		$table_name = $wpdb->prefix . 'wpl_data_req';

		$query  = "SELECT * FROM $table_name";
		$where_clauses = [];
		$params = [];

		// Email filter
		if (!empty($email)) {
			$where_clauses[] = "email LIKE %s";
			$params[] = '%' . $wpdb->esc_like($email) . '%';
		}

		// Status filter
		if ($filter === 'Resolved') {
			$where_clauses[] = "resolved = %d";
			$params[] = 1;
		} elseif ($filter === 'Unresolved') {
			$where_clauses[] = "resolved = %d";
			$params[] = 0;
		}

		$where_sql = '';
		if (!empty($where_clauses)) {
			$where_sql = ' WHERE ' . implode(' AND ', $where_clauses);
		}

		// ✅ Total count
		$count_query = "SELECT COUNT(*) FROM $table_name $where_sql";
		$total_count = !empty($params)
			? $wpdb->get_var($wpdb->prepare($count_query, $params))
			: $wpdb->get_var($count_query);

		// ✅ If ALL is true → return EVERYTHING without pagination
		if ($all) {
			$query .= " ORDER BY ID DESC";
			$prepared_query = !empty($params)
				? $wpdb->prepare($query, $params)
				: $query;
		} 
		// ✅ Otherwise apply pagination
		else {
			$query .= $where_sql . " ORDER BY ID DESC LIMIT %d OFFSET %d";
			$params[] = $number;
			$params[] = $offset;
			$prepared_query = $wpdb->prepare($query, $params);
		}

		$results = $wpdb->get_results($prepared_query, ARRAY_A);

		return [
			'total_records' => $total_count,
			'data_requests' => $results,
		];
	}


	public function gdpr_mark_data_requests_as_resolved( WP_REST_Request $request ) {

		$ids = $request->get_param('ids');

		if ( empty($ids) || !is_array($ids) ) {
			return new WP_Error(
				'invalid_ids',
				'Invalid IDs array',
				['status' => 400]
			);
		}

		$ids = array_map('intval', $ids);
		$placeholders = implode(',', array_fill(0, count($ids), '%d'));

		global $wpdb;
		$table_name = $wpdb->prefix . 'wpl_data_req';

		$query = "UPDATE $table_name SET resolved = 1 WHERE ID IN ($placeholders)";

		$updated = $wpdb->query(
			$wpdb->prepare($query, $ids)
		);

		if ( $updated === false ) {
			return new WP_Error(
				'db_update_failed',
				'Failed to update records',
				['status' => 500]
			);
		}

		return [
			'success'  => true,
			'updated'  => $updated,
			'ids'      => $ids,
			'status'   => 'resolved'
		];
	}

	public function gdpr_delete_data_request_entries( WP_REST_Request $request ) {

		$ids = $request->get_param('ids');

		if ( empty($ids) || !is_array($ids) ) {
			return new WP_Error(
				'invalid_ids',
				'Invalid IDs array',
				['status' => 400]
			);
		}

		$ids = array_map('intval', $ids);
		$placeholders = implode(',', array_fill(0, count($ids), '%d'));

		global $wpdb;
		$table_name = $wpdb->prefix . 'wpl_data_req';

		$query = "DELETE FROM $table_name WHERE ID IN ($placeholders)";

		$deleted = $wpdb->query(
			$wpdb->prepare($query, $ids)
		);

		if ( $deleted === false ) {
			return new WP_Error(
				'db_delete_failed',
				'Failed to delete records',
				['status' => 500]
			);
		}

		return [
			'success' => true,
			'deleted' => $deleted,
			'ids'     => $ids
		];
	}

	public function gdpr_save_changes( WP_REST_Request $request){
		$save_object = $request->get_param('save_object') ?: null;
		
		$geo_target_object = $request->get_param('geo_target_object') ?: null;
		
		$share_usage_data = $request->get_param('share_usage_data') ?: null;
		$cookie_banner_created_once = $request->get_param('cookie_banner_created_once') ?: null;

		$banner_image    = $request->get_param( 'banner_image' );
		$banner_image1   = $request->get_param( 'banner_image1' );
		$banner_image2   = $request->get_param( 'banner_image2' );
		$banner_image_ml = $request->get_param( 'banner_image_ml' );

		$custom_css = $request->get_param( 'gdpr_css_text' );

		$advanced_scripts = $request->get_param( 'advanced_scripts' );

		$whitelist_scripts = $request->get_param( 'whitelist_scripts' );

		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();

		if(!empty($save_object) && is_array($save_object)){
			
			$the_options = array_merge($the_options, $save_object);

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

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$images = array(
			array(
				'value'  => $banner_image,
				'option' => GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD,
			),
			array(
				'value'  => $banner_image1,
				'option' => GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1,
			),
			array(
				'value'  => $banner_image2,
				'option' => GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD2,
			),
			array(
				'value'  => $banner_image_ml,
				'option' => GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELDML1,
			),
		);

		foreach ( $images as $image ) {
			if ( empty( $image['value'] ) ) {
				continue;
			}

			$attachment_id = media_handle_upload( 'image', 0 );

			if ( is_wp_error( $attachment_id ) ) {
				continue;
			}

			$image_url = wp_get_attachment_url( $attachment_id );

			if ( $image_url ) {
				update_option( $image['option'], esc_url_raw( $image_url ) );
			}
		}

		if ( isset( $custom_css ) ) {

			$css_file_location = ABSPATH . 'wp-content/plugins/gdpr-cookie-consent/public/css/';
	
			$css_file_path = $css_file_location . 'gdpr-cookie-consent-public-custom.css';
	
			$css_min_file_path = $css_file_location . 'gdpr-cookie-consent-public-custom.min.css';
	
			// Allow us to easily interact with the filesystem.
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
			global $wp_filesystem;
	
			$wp_filesystem->put_contents( $css_file_path, $custom_css, FS_CHMOD_FILE );
	
			// Writing the CSS code to the minified CSS file.
			$wp_filesystem->put_contents( $css_min_file_path, $custom_css, FS_CHMOD_FILE ) ;
	
			$encode_css                   = $this->encode_css( $the_options['gdpr_css_text'] );
			$the_options['gdpr_css_text'] = $encode_css;
		}

		global $wpdb;
		if ( ! empty( $advanced_scripts ) ) {
			foreach ( $advanced_scripts as $row ) {
				$wpdb->update(
					$advanced_scripts_table,
					array(
						'script_category' => $row['script_category'],
						'script_status'   => $row['script_status'],
					),
					array( 'id' => $row['id'] ),
					array(
						'%d', // script_category
						'%d', // script_status
					),
					array( '%d' )
				);
			}
		}

		if ( ! empty( $whitelist_scripts ) ) {

			$whitelist_scripts = array(
				'whitelist_script' => $whitelist_scripts,
			);

			update_option( 'wpl_options_custom-scripts', $whitelist_scripts );
		}
		
		if(!empty($geo_target_object) && is_array($geo_target_object)){

			$the_options['select_countries'] = isset( $geo_target_object['is_gdpr_selected_countries'] ) ? $geo_target_object['is_gdpr_selected_countries'] : '';
			$the_options['select_countries_ccpa'] = isset( $geo_target_object['is_ccpa_selected_countries'] ) ? $geo_target_object['is_ccpa_selected_countries'] : '';

			if ( isset( $geo_target_object['is_gdpr_worldwide_on'] ) && ($the_options['cookie_usage_for'] === 'gdpr' || $the_options['cookie_usage_for'] === 'both') ) {
				if ( filter_var( $the_options['is_worldwide_on'], FILTER_VALIDATE_BOOLEAN ) !==  filter_var( $geo_target_object['is_gdpr_worldwide_on'], FILTER_VALIDATE_BOOLEAN ) ) {
					$is_maxmind_turned_on = filter_var( $geo_target_object['is_gdpr_worldwide_on'], FILTER_VALIDATE_BOOLEAN ) ? 'Turned Off' : 'Turned On';
					$data_args = array(
						'Status' => 'Maxmind ' . $is_maxmind_turned_on,
					);
					$this->gdpr_send_shared_usage_data( 'GCC Maxmind Status', $data_args );
				}
				if ( !$geo_target_object['is_gdpr_worldwide_on'] ) {
					$the_options['is_worldwide_on'] = 'false';
				} else {
					if(!$the_options['is_worldwide_on']){
						$this->disable_auto_update_maxminddb();
					}
					$the_options['is_worldwide_on'] = 'true';
				}
			}

			if ( isset( $geo_target_object['is_ccpa_worldwide_on'] ) && ($the_options['cookie_usage_for'] === 'ccpa' || $the_options['cookie_usage_for'] === 'both') ) {
				if ( filter_var( $the_options['is_worldwide_on_ccpa'], FILTER_VALIDATE_BOOLEAN ) !==  filter_var( $geo_target_object['is_ccpa_worldwide_on'], FILTER_VALIDATE_BOOLEAN ) ) {
					$is_maxmind_turned_on = filter_var( $geo_target_object['is_ccpa_worldwide_on'], FILTER_VALIDATE_BOOLEAN ) ? 'Turned Off' : 'Turned On';
					$data_args = array(
						'Status' => 'Maxmind ' . $is_maxmind_turned_on,
					);
					$this->gdpr_send_shared_usage_data( 'GCC Maxmind Status', $data_args );
				}
				if ( !$geo_target_object['is_ccpa_worldwide_on'] ) {
					$the_options['is_worldwide_on_ccpa'] = 'false';
				} else {
					if(!$the_options['is_worldwide_on_ccpa']){
						$this->disable_auto_update_maxminddb();
					}
					$the_options['is_worldwide_on_ccpa'] = 'true';
				}
			}

			if ( isset( $geo_target_object['is_gdpr_eu_on'] ) ) {
				if ( !$geo_target_object['is_gdpr_eu_on'] ) {
					$the_options['is_eu_on'] = 'false';
				} else {
					if(!$the_options['is_eu_on'] ){
						$this->auto_update_maxminddb();
						$this->download_maxminddb();
					}
					$the_options['is_eu_on'] = 'true';
				}
			}

			if ( isset( $geo_target_object['is_ccpa_us_on'] ) ) {
				if ( !$geo_target_object['is_ccpa_us_on'] ) {
					$the_options['is_ccpa_on'] = 'false';
				} else {
					if(!$the_options['is_ccpa_on'] ){
						$this->auto_update_maxminddb();
						$this->download_maxminddb();
					}
					$the_options['is_ccpa_on'] = 'true';
				}
			}

			if ( isset( $geo_target_object['is_gdpr_select_countries_on'] ) ) {
				if ( !$geo_target_object['is_gdpr_select_countries_on'] ) {
					$the_options['is_selectedCountry_on'] = 'false';
				} else {
					if(!$the_options['is_selectedCountry_on']){
						$this->auto_update_maxminddb();
						$this->download_maxminddb();
					}
					$the_options['is_selectedCountry_on'] = 'true';
				}
			}

			if ( isset( $geo_target_object['is_ccpa_select_countries_on'] ) ) {
				if ( !$geo_target_object['is_ccpa_select_countries_on'] ) {
					$the_options['is_selectedCountry_on_ccpa'] = 'false';
				} else {
					if(!$the_options['is_selectedCountry_on_ccpa']){
						$this->auto_update_maxminddb();
						$this->download_maxminddb();
					}
					$the_options['is_selectedCountry_on_ccpa'] = 'true';
				}
			}
		}

		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );

		if(!empty($share_usage_data)){
			$is_usage_tracking_allowed = $share_usage_data ? 'true' : 'false';
			update_option( 'gdpr_usage_tracking_allowed', $is_usage_tracking_allowed );
		}
		if(!empty($cookie_banner_created_once)){
			update_option('wplp_cookie_banner_created_once', $cookie_banner_created_once);
		}
		
		return [
			'success' => true,
			'accessed' => true
		];
	}

	/**
	 * Fucntion to disconnect account when site deleted from saas dashboard
	 */
	public function disconnect_account_request(){

		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';
		$settings   = new GDPR_Cookie_Consent_Settings();
		$options    = $settings->get_defaults();
		$product_id = $settings->get( 'account', 'product_id' );

		global $wcam_lib_gdpr;
		$activation_status = get_option( $wcam_lib_gdpr->wc_am_activated_key );

		$args = array(
			'api_key' => $settings->get( 'api', 'token' ),
		);
		update_option( 'wpeka_api_framework_app_settings', $options );

		if ( false !== get_option( 'wplegal_api_framework_app_settings' ) ) {
			update_option( 'wplegal_api_framework_app_settings', $options );
		}

		//changing banner display status to worldwide
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		
		$the_options['is_worldwide_on'] = 'true';
		$the_options['is_selectedCountry_on'] = 'false';
		$the_options['is_eu_on'] = 'false';
		$the_options['is_ccpa_on'] = 'false';
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );

		update_option( 'gdpr_no_of_page_scan', 0 );

		update_option( $wcam_lib_gdpr->wc_am_activated_key, 'Deactivated' );

		if ( isset( $wcam_lib_gdpr->data[ $wcam_lib_gdpr->wc_am_activated_key ] ) ) {
			update_option( $wcam_lib_gdpr->data[ $wcam_lib_gdpr->wc_am_activated_key ], 'Deactivated' );
		}
	}

	/**
	 * Fucntion to update gcm status
	 */
	public function update_gcm_status(WP_REST_Request $request){
		$params = $request->get_json_params();
		$the_options = get_option(GDPR_COOKIE_CONSENT_SETTINGS_FIELD);
		$the_options['wpl_gcm_latest_scan_result'] = wp_json_encode($params);
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );

		$api_user_email = $this->settings->get_email();
		wp_remote_post(
			GDPR_API_URL . 'send_gcm_status_mail',
			array(
				'body' => array(
					'gcm_scan_data'   => $the_options['wpl_gcm_latest_scan_result'],
					'site_admin_mail' => $api_user_email
				),
				'timeout' => 60,
			)
		);

		$the_options['wpl_gcm_latest_scan_result'] = '';
		update_option(GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options);
		delete_transient( 'wpl_gcm_check_is_scanning' );

		return new WP_REST_Response(['status' => 'stored']);
	}

	/**
	 * Fucntion to allow cors for react app
	 */
	public function allow_cors_for_react_app(){
		remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );

		// Add our own permissive CORS headers
		add_filter( 'rest_pre_serve_request', function( $value ) {
			header( 'Access-Control-Allow-Origin: *');
			header( 'Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS' );
			header( 'Access-Control-Allow-Credentials: true' );
			header( 'Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce, Origin, X-Requested-With, Accept' );

			// Handle preflight requests
			if ( $_SERVER['REQUEST_METHOD'] === 'OPTIONS' ) {
				status_header( 200 );
				exit;
			}

			return $value;
		});
	}

	public function permission_callback_for_react_app(WP_REST_Request $request) {
		$this->settings = new GDPR_Cookie_Consent_Settings();

		$master_key = $this->settings->get('api','token');		

		$auth_header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
		if ( ! preg_match('/Bearer\s(\S+)/', $auth_header, $matches) ) {
			return new WP_Error('no_token', 'Authorization token missing.', ['status' => 401]);
		}
		$token = sanitize_text_field($matches[1]);
		// 2. Validate token with central WP site
		$validate = wp_remote_post(
			GDPR_APP_URL . '/wp-json/jwt-auth/v1/token/validate',
			[
				'headers' => [
					'Authorization' => 'Bearer ' . $token,
					'Content-Type'  => 'application/json'
				],
				'timeout' => 15
			]
		);
		if ( is_wp_error($validate) ) {
			return new WP_Error('token_validation_failed', $validate->get_error_message(), ['status' => 401]);
		}
		$code = wp_remote_retrieve_response_code($validate);
		if ( $code !== 200 ) {
			return new WP_Error('invalid_token', 'Token validation failed.', ['status' => 401]);
		}
		// 3. Extract master_key from the request body
		$body = $request->get_json_params();
		$incoming_key = isset($body['master_key']) ? sanitize_text_field($body['master_key']) : '';
		if ( empty($incoming_key) ) {
			return new WP_Error('master_key_missing', 'Master key not provided.', ['status' => 401]);
		}
		if ( $master_key !== $incoming_key ) {
			return new WP_Error('invalid_master_key', 'Master key mismatch.', ['status' => 401]);
		}
		return true; // All good → allow callback
	}

	// Register the REST API route for data from plugin to the saas appwplp server 

	public function register_gdpr_dashboard_route() {
		
		global $is_user_connected, $api_user_plan; // Make global variables accessible
		$this->settings = new GDPR_Cookie_Consent_Settings();
		
		$is_user_connected = $this->settings->is_connected();

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/fetch_wizard_data',
			array(
				'methods'	=> 'POST',
				'callback'	=> array($this, 'wplp_send_wizard_data_to_react_app'),
				'permission_callback'	=> array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/get_consent_logs',
			array(
				'methods' 	=> 'POST',
				'callback' 	=> array($this, 'gdpr_send_consent_log_data_to_react_app'),
				'permission_callback'	=> array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'delete_consent_logs',
			array(
				'methods' 	=> 'POST',
				'callback' 	=> array($this, 'delete_consent_logs_for_react_app'),
				'permission_callback'	=> array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'generate_consent_pdf',
			array(
				'methods' 	=> 'POST',
				'callback' 	=> array($this, 'generate_consent_pdf_for_react_app'),
				'permission_callback'	=> array($this, 'permission_callback_for_react_app'),
			)
		);
		
		register_rest_route(
			'wplp-react-gdpr/v1', //New namespace for React dashboard
			'/get_dashboard-data',
			array(
				'methods'  => 'POST',
				'callback' => array($this, 'gdpr_send_data_to_dashboard_appwplp_react_app'), // Function to handle the request
				'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/get-policy-data',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_send_policy_data_to_saas' ),
				'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/delete-policy-data',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_delete_policy_data' ),
				'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/import-policy-data',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_import_policy_data' ),
				'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/export-policy-data',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'wplp_export_policy_data' ),
				'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/get-data-request-form-fields',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_get_data_request_form_fields' ),
				'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/update-data-request-form-fields',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_update_data_request_form_fields' ),
				'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/get_data_requests',
			array(
				'methods' 	=> 'POST',
				'callback' 	=> array($this, 'gdpr_send_data_requests_to_react_app'),
				'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/resolve_data_request',
			array(
				'methods' 	=> 'POST',
				'callback' 	=> array($this, 'gdpr_mark_data_requests_as_resolved'),
				'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/delete_data_request',
			array(
				'methods' 	=> 'POST',
				'callback' 	=> array($this, 'gdpr_delete_data_request_entries'),
				'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);
		register_rest_route(
			'wplp-react-gdpr/v1',
			'/save_gdpr_changes',
			array(
				'methods' 	=> 'POST',
				'callback' 	=> array($this, 'gdpr_save_changes'),
				'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/get-advanced-settings',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_fetch_advanced_settings' ),
				'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/restore-settings',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_restore_plugin_settings' ),
				'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/export-settings',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_export_plugin_settings' ),
				'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/import-settings',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_import_plugin_settings' ),
				'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/get-general-settings',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_fetch_general_settings' ),
				// 'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/user-consent-renew',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_renew_user_consent' ),
				// 'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/gcm-regions',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_modify_gcm_regions' ),
				// 'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/check-gcm-status',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_check_gcm_status' ),
				// 'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/custom-cookie',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_custom_cookie' ),
				// 'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/cookie-data',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_cookie_data' ),
				// 'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/auto-generate-banner',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_auto_generate_banner' ),
				// 'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/schedule-scan',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_schedule_scan' ),
				// 'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

		register_rest_route(
			'wplp-react-gdpr/v1',
			'/cookie_scan',
			array(
				'methods'  => 'POST',
				'callback' => array( $this, 'gdpr_start_cookie_scan' ),
				// 'permission_callback' => array($this, 'permission_callback_for_react_app'),
			)
		);

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
		register_rest_route(
			'gdpr/v2', // Namespace
			'/delete_activation', 
			array(
				'methods'  => 'POST',
				'callback' => array($this, 'disconnect_account_request'), // Function to handle the request
				'permission_callback' => function() {
        				    
        		    if (current_user_can('manage_options')) {
        		        return true;
        		    }
				
        		    $stored_secret = get_option('wplegalpages_api_secret');
        		    $header_secret = isset($_SERVER['HTTP_X_WPLP_SECRET'])
        		                        ? sanitize_text_field($_SERVER['HTTP_X_WPLP_SECRET'])
        		                        : '';
				
        		    if ($stored_secret && $header_secret && $stored_secret === $header_secret) {
        		        return true;
        		    }
				
        		    return new WP_Error('rest_forbidden', 'Unauthorized access', array('status' => 403));
        		},
			)
		);
		register_rest_route(
			'gdpr/v2', // Namespace
			'/update_gcm_status', 
			array(
				'methods'  => 'POST',
				'callback' => array($this, 'update_gcm_status'), // Function to handle the request
				'permission_callback' => function() use ($is_user_connected) {
					if ($is_user_connected) {
						return true; // Allow access
					}
					return new WP_Error('rest_forbidden', 'Unauthorized access', array('status' => 401));
				},
			)
		);

		$appwplp_namespace  = 'appwplp/v1';

		$appwplp_payment_status_route      = 'wplp_get_payment_status';
		$appwplp_payment_status_full_route = '/' . trim( $appwplp_namespace, '/' ) . '/' . trim( $appwplp_payment_status_route, '/' );

		$appwplp_subscription_status_pending_cancel_route = 'wplp_subscription_status_pending_cancel';
		$appwplp_subscription_status_full_route           = '/' . trim( $appwplp_namespace, '/' ) . '/' . trim( $appwplp_subscription_status_pending_cancel_route, '/' );

		$rest_server = rest_get_server();
		$routes      = $rest_server->get_routes();

		if ( ! array_key_exists( $appwplp_payment_status_full_route, $routes ) ) {
			register_rest_route(
				$appwplp_namespace,
				'/' . $appwplp_payment_status_route,
				array(
					'methods'  => 'POST',
					'callback' => array( $this, 'gdpr_get_wplp_payment_status' ),
					'permission_callback' => function() use ( $is_user_connected ) {
						// Check if user is connected and the API plan is valid.
						if ( $is_user_connected ) {
							return true; // Allow access.
						}
						return new WP_Error( 'rest_forbidden', 'Unauthorized access', array( 'status' => 401 ) );
					},
				)
			);
		}

		if ( ! array_key_exists( $appwplp_subscription_status_full_route, $routes ) ) {
			register_rest_route(
				$appwplp_namespace, '/' .
				$appwplp_subscription_status_pending_cancel_route,
				array(
					'methods'  => 'POST',
					'callback' => array( $this, 'gdpr_set_subscription_payment_pending_cancel' ),
					'permission_callback' => function() use ( $is_user_connected ) {
						// Check if user is connected and the API plan is valid.
						if ( $is_user_connected ) {
							return true; // Allow access.
						}
						return new WP_Error( 'rest_forbidden', 'Unauthorized access', array( 'status' => 401 ) );
					},
				)
			);
		}
	}

	function wplp_gdpr_generate_api_secret() {
	    // Check if secret already exists
	    if ( get_option('wplegalpages_api_secret') ) {
	        return get_option('wplegalpages_api_secret');
	    }

	    // Generate a 32-character alphanumeric secret
	    $secret = wp_generate_password(32, false);
	
	    // Store it in WP options
	    update_option('wplegalpages_api_secret', $secret);

	    return $secret;
	}

	/**
	 * REST API callback to update and store the subscription payment status.
	 *
	 * This endpoint is hit by the main site to inform the client site about the subscription payment status.
	 * It either sets or deletes a transient based on whether the payment is 'completed' or not.
	 *
	 * @param WP_REST_Request $request The REST request object containing the payment status.
	 *
	 * @return WP_REST_Response The response confirming the updated status.
	 */
	public function gdpr_get_wplp_payment_status( WP_REST_Request $request ) {

		$payment_status = $request->get_param( 'payment_status' );

		if ( 'completed' === $payment_status ) {
			delete_transient( 'app_wplp_subscription_payment_status_failed' );
			$message = 'Completed';
		} else {
			set_transient( 'app_wplp_subscription_payment_status_failed', true, 7 * DAY_IN_SECONDS );
			$message = 'Failed';
		}

		return rest_ensure_response(
			array(
				'message' => 'Status Changed to ' . $message,
			)
		);
	}

	/**
	 * REST API callback to update the local subscription status to either 'active' or 'pending-cancel'.
	 *
	 * This endpoint is called by the main site to notify the client site about the subscription status change.
	 * It updates or deletes an option based on the received status.
	 *
	 * @param WP_REST_Request $request The REST request object containing the subscription status.
	 *
	 * @return WP_REST_Response The response confirming the updated status.
	 */
	public function gdpr_set_subscription_payment_pending_cancel( WP_REST_Request $request ) {

		$subscription_status = $request->get_param( 'subscription_status' );

		if ( 'active' === $subscription_status ) {
			delete_option( 'app_wplp_subscription_status_pending_cancel' );
			$message = 'Active';
		} else {
			update_option( 'app_wplp_subscription_status_pending_cancel', 1 );
			$message = 'Pending Cancel';
		}

		return rest_ensure_response(
			array(
				'message' => 'Subscription Status Changed to ' . $message,
			)
		);
	}

	//Function to register the Import CSV page - Policy data
	function register_gdpr_policies_import_page() {
		// This adds a page, even if it's not visible in the admin menu
		add_submenu_page(
			'gdpr-cookie-consent',  // This makes the page hidden in the menu
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
				$template_parts_background = '#2A3E71';
			} elseif ( in_array( $template, array( 'almond_column' ), true ) ) {
				$template_parts_background = '#FFFFFF';
			} elseif ( in_array( $template, array( 'grey_column', 'grey_center' ), true ) ) {
				$template_parts_background = '#FFFFFF';
			} elseif ( in_array( $template, array( 'dark' ), true ) ) {
				$template_parts_background = '#262626';
			} elseif ( in_array( $template, array( 'dark_row' ), true ) ) {
				$template_parts_background = '#323742';
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

	/**
	 * Retrieves the count of published legal pages.
	 *
	 * This function queries for pages that have the 'is_legal' meta key 
	 * and returns the number of such pages.
	 *
	 * @return int The number of legal pages found.
	 */
	public function gdpr_get_legal_page_generated_count() {

		$args = array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'     => 'is_legal',
					'compare' => 'EXISTS',
				)
			),
			'fields'         => 'ids',
		);
		
		$query      = new WP_Query($args);
		$post_count = $query->found_posts;

		wp_reset_postdata();

		return apply_filters( 'gdpr_get_legal_page_generated_count', $post_count );
	}

	public function gdpr_ensure_no_other_cookie_plugins_activated( $active_plugins ) {
		$other_plugins_active = false;
		if ( empty( $active_plugins ) ) return;
		$active_plugins = array_values( $active_plugins ); // Ensure plugins are in an indexed array
		$plugins_length = count( $active_plugins ); // Get the total number of plugins

		for ( $i = 0; $i < $plugins_length; $i++ ) {
			$plugin = $active_plugins[$i];

			// Check if the plugin is not one of the two specific ones
			if (
				!(
					$plugin === "gdpr-cookie-consent/gdpr-cookie-consent.php" ||
					$plugin === "wpl-cookie-consent/wpl-cookie-consent.php"
				)
			) {
				// Check if the plugin name contains one of the keywords
				if (
					strpos($plugin, "cookie") !== false ||
					strpos($plugin, "gdpr") !== false ||
					strpos($plugin, "ccpa") !== false ||
					strpos($plugin, "compliance") !== false
				) {
					$other_plugins_active = true;
					break; // Exit the loop as the condition is met
				}
			}
		}

		if ( $other_plugins_active ) {
			return true;
		}
		return false;
	}

	/**
	 * Determines the operating system of the user based on the HTTP User-Agent string.
	 *
	 * @return string The detected operating system name or 'Unknown OS' if not identified.
	 */
	public function gdpr_get_user_os() {
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
	
		if ( stripos( $user_agent, 'Windows' ) !== false ) return 'Windows';
		if ( stripos( $user_agent, 'Mac' ) !== false ) return 'Mac OS';
		if ( stripos( $user_agent, 'Linux' ) !== false ) return 'Linux';
		if ( stripos( $user_agent, 'Android' ) !== false ) return 'Android';
		if ( stripos( $user_agent, 'iPhone' ) !== false || stripos( $user_agent, 'iPad' ) !== false ) return 'iOS';
	
		return 'Unknown OS';
	}

	/**
	 * Determines the type of device based on the user agent.
	 *
	 * This function inspects the `HTTP_USER_AGENT` server variable to identify whether 
	 * the device is a mobile, tablet, or desktop.
	 *
	 * @return string Returns 'Mobile' if a mobile device is detected, 'Tablet' if a tablet is detected, 
	 *               and 'Desktop' otherwise.
	 */
	public function gdpr_get_device_type() {
		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		if ( preg_match( '/mobile|android|iphone|ipod|blackberry|opera mini|windows phone|webos/i', $user_agent ) ) {
			return 'Mobile';
		}

		if ( preg_match( '/tablet|ipad/i', $user_agent ) ) {
			return 'Tablet';
		}

		return 'Desktop';
	}

	/**
	 * Retrieves the user's IP address.
	 *
	 * This function checks various server variables to determine the user's IP address,
	 * including `HTTP_CLIENT_IP`, `HTTP_X_FORWARDED_FOR`, and `REMOTE_ADDR`.
	 *
	 * @return string The detected IP address of the user.
	 */
	public function gdpr_get_user_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && count( array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) )) > 0 ) {
			$xForwardedFor = $_SERVER['HTTP_X_FORWARDED_FOR'];
			$ipList = array_map('trim', explode(',', $xForwardedFor));

			$ipaddress = filter_var($ipList[0], FILTER_VALIDATE_IP);
			return $ipaddress;
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}

	/**
	 * Retrieves the user's country code based on their IP address using the country.is API.
	 *
	 * @return string|null Two-letter country code (e.g., 'US') or null on failure.
	 */
	public function gdpr_get_user_country() {
		$geoData = wp_remote_get( "https://api.country.is/" );
	
		if ( is_wp_error( $geoData ) ) {
			return null;
		}
	
		$body = wp_remote_retrieve_body( $geoData );
		$data = json_decode( $body, true );
		
		return $data['country'];
	}

	/**
	 * Sends shared usage data if opt-in is allowed.
	 *
	 * @param string $event Event name to be tracked.
	 * @param array  $args  Optional. Additional event-specific data to send.
	 *
	 * @return bool True on successful request, false otherwise or if opt-in is not enabled.
	 */
	public function gdpr_send_shared_usage_data( $event, $args = array() ) {

		if ( ! filter_var( get_option( 'gdpr_usage_tracking_allowed' ), FILTER_VALIDATE_BOOLEAN ) ) {
			return;
		}

		$url     = GDPR_APP_URL . '/wp-json/api/v1/plugin/app_wplp_collect_shared_usage_data';
		$user_id = get_current_user_id();
		
		if ( $user_id ) {
			$user       = get_userdata( $user_id );
			$user_email = $user ? $user->user_email : null;
		} else {
			$user_email = null;
		}
	
		$data = array(
			'event'       => $event,
			'src'         => 'gdpr-cookie-consent',
			'site_url'    => site_url(),
			'email'       => $user_email,
			'os_name'     => $this->gdpr_get_user_os(),
			'device_type' => $this->gdpr_get_device_type(),
			'ip'          => $this->gdpr_get_user_ip(),
			'country'     => $this->gdpr_get_user_country(),
			'time'        => time() * 1000,
			'args'        => $args,
		);

		$response = wp_safe_remote_post(
			$url,
			array(
				'body'    => json_encode($data),
				'headers' => array( 'Content-Type' => 'application/json' ),
				'method'  => 'POST',
				'timeout' => 20,
			)
		);

		if ( 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Register translation strings for extraction only.
	 * Not executed during normal plugin operation.
	 */
	public function gdpr_cookie_consent_register_translation_strings() {
		if ( defined( 'DOING_CRON' ) || wp_doing_ajax() ) {
			return;
		}

		// Never executed, only for translation tools like makepot or Poedit
		if ( false ) {
			__( 'HTTP Cookie', 'gdpr-cookie-consent' );
			__( 'HTML Local Storage', 'gdpr-cookie-consent' );
			__( 'Flash Local Shared Object', 'gdpr-cookie-consent' );
			__( 'Pixel Tracker', 'gdpr-cookie-consent' );
			__( 'IndexedDB', 'gdpr-cookie-consent' );
			__( 'Read More', 'gdpr-cookie-consent' );
			__( 'Decline', 'gdpr-cookie-consent' );
			__( 'Accept', 'gdpr-cookie-consent' );
			__( 'Confirm', 'gdpr-cookie-consent' );
			__( 'Cancel', 'gdpr-cookie-consent' );
			__( 'Necessary', 'gdpr-cookie-consent' );
			__( 'Marketing', 'gdpr-cookie-consent' );
			__( 'Analytics', 'gdpr-cookie-consent' );
			__( 'Preferences', 'gdpr-cookie-consent' );
			__( 'Unclassified', 'gdpr-cookie-consent' );
			__( 'Cookie Settings', 'gdpr-cookie-consent' );
			__( 'Necessary cookies help make a website usable by enabling basic functions like page navigation and access to secure areas of the website. The website cannot function properly without these cookies.', 'gdpr-cookie-consent' );
			__( 'Marketing cookies are used to track visitors across websites. The intention is to display ads that are relevant and engaging for the individual user and thereby more valuable for publishers and third party advertisers.', 'gdpr-cookie-consent' );
			__( 'Analytics cookies help website owners to understand how visitors interact with websites by collecting and reporting information anonymously.', 'gdpr-cookie-consent' );
			__( 'Preference cookies enable a website to remember information that changes the way the website behaves or looks, like your preferred language or the region that you are in.', 'gdpr-cookie-consent' );
			__( 'Unclassified cookies are cookies that we are in the process of classifying, together with the providers of individual cookies.', 'gdpr-cookie-consent' );
			__( 'Cookies are small text files that can be used by websites to make a user\'s experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.', 'gdpr-cookie-consent' );
			__( 'This website uses cookies', 'gdpr-cookie-consent' );
			__( 'This website uses cookies to improve your experience. We\'ll assume you\'re ok with this, but you can opt-out if you wish.', 'gdpr-cookie-consent' );
			__( 'In case of sale of your personal information, you may opt out by using the link', 'gdpr-cookie-consent' );
			__( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' );
			__( 'Do you really wish to opt-out?', 'gdpr-cookie-consent' );
			__( 'Accept All', 'gdpr-cookie-consent' );
		}
	}

	public function gdpr_send_policy_data_to_saas( WP_REST_Request $request ) {
		$limit  = absint( $request->get_param( 'limit' ) ?? 10 );
		$offset = absint( $request->get_param( 'offset' ) ?? 0 );
		$search = sanitize_text_field( $request->get_param( 'search' ) ?? '' );

		$args = array(
			'post_type'      => 'gdprpolicies',
			'post_status'    => array( 'publish', 'draft' ),
			'posts_per_page' => $limit,
			'offset'         => $offset,
			'no_found_rows'  => false,
		);

		if ( ! empty( $search ) ) {
			$args['s'] = $search;
		}

		$query = new WP_Query( $args );

		if ( empty( $query->posts ) ) {
			return rest_ensure_response( [] );
		}

		$data = array();

		if ( $query->have_posts() ) {
			foreach ($query->posts as $post) {
				$post_id = $post->ID;

				$domain = get_post_meta( $post_id, '_gdpr_policies_domain', true );
        		$links  = get_post_meta( $post_id, '_gdpr_policies_links_editor', true );

				$data[] = [
					'id'             => $post_id,
					'company_name'   => get_the_title($post_id),
					'policy_purpose' => wp_trim_words( wp_strip_all_tags( $post->post_content ), 50 ),
					'domain'         => $domain ?: '',
					'links'          => $links ?: '',
				];
			}
		}

		return rest_ensure_response(
			array(
				'data' => $data,
				'total' => (int) $query->found_posts,
			)
		);
	}

	public function gdpr_import_policy_data( WP_REST_Request $request ) {

		$batch = $request->get_param( 'batch' );

		if ( ! $batch ) {
			return new WP_REST_Response( [ 'status' => 'error', 'message' => 'No CSV File Provided.' ], 400 );
		}

		$headers = array(
			'post_title',
			'post_content',
			'post_status',
			'_gdpr_policies_links_editor',
			'_gdpr_policies_domain',
		);

		if ( ! function_exists( 'post_exists' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/post.php' );
		}

		foreach ( $batch as $index => $policy ) {

			$allowed_headers = array_keys( $policy );
			foreach ( $allowed_headers as $header ) {
				if ( ! in_array( $header, $headers, true ) ) {
					return new WP_REST_Response( [ 'status' => 'error', 'message' => 'Invalid CSV File.' ], 400 );
				}
			}

			$post_title   = sanitize_text_field( $policy['post_title'] ?? '' );
			$post_content = wp_kses_post( $policy['post_content'] ?? '' );
			$post_status  = sanitize_text_field( $policy['post_status'] ?? '' );
			$links        = wp_kses_post( $policy['_gdpr_policies_links_editor'] ?? '' );
			$domain       = wp_kses_post( $policy['_gdpr_policies_domain'] ?? '' );

			$post_data = array(
				'post_author'   => 1,
				'post_date'     => gmdate( 'Y-m-d H:i:s', strtotime( 'now' ) ),
				'post_date_gmt' => gmdate( 'Y-m-d H:i:s', strtotime( 'now' ) ),
				'post_content'  => $post_content,
				'post_title'    => $post_title,
				'post_name'     => sanitize_title( $post_title ),
				'post_status'   => $post_status ? $post_status : 'publish',
				'post_parent'   => 0,
				'post_type'     => GDPR_POLICY_DATA_POST_TYPE,
			);

			$post_id = post_exists( $post_title );
			if ( $post_id ) {
				$post_data['ID'] = $post_id; 
				$post_id = wp_update_post( $post_data, true );
			} else {
				$post_id = wp_insert_post( $post_data, true );
			}

			if ( is_wp_error( $post_id ) ) {
				return new WP_REST_Response( [ 'status' => 'error', 'message' => 'Issue Importing Post with title -> ' . $post_title ], 400 );
			} else {
				if ( $post_id ) {
					$links  = maybe_unserialize( $links );
					$domain = maybe_unserialize( $domain );
					update_post_meta( $post_id, '_gdpr_policies_links_editor', $links );
					update_post_meta( $post_id, '_gdpr_policies_domain', $domain );
				}
			}
		}

		return new WP_REST_Response( [ 'status' => true, 'message' => 'Policy Data Imported Successfully', 'batch' => $batch ], 200);
	}

	public function gdpr_delete_policy_data( WP_REST_Request $request ) {

		$policy_ids = $request->get_param( 'policy_ids' );
		
		if ( ! is_array( $policy_ids ) ) {
			return new WP_REST_Response( [ 'status' => 'error', 'message' => 'Incorrect Policy Provided.' ], 400 );
		}

		if ( ! $policy_ids ) {
			return new WP_REST_Response( [ 'status' => 'error', 'message' => 'No Policy Provided.' ], 400 );
		}


		foreach ( $policy_ids as $policy_id ) {
			$policy_id = absint( $policy_id );
			$post = get_post( $policy_id );
			
			if ( ! $post || $post->post_type !== 'gdprpolicies' ) {
				continue; // Skip non-policy posts
			}

			$deleted = wp_delete_post( $policy_id, true );
			if ( ! $deleted ) {
				return new WP_REST_Response( [ 'status' => 'error', 'message' => 'Issue Deleting Policy Data.' ], 400 );
			}
		}

		return new WP_REST_Response( [ 'status' => true, 'message' => 'Policy Data Deleted Successfully.', 'policy_id' => $policy_ids ], 200);
	}

	public function wplp_export_policy_data( WP_REST_Request $request ) {

		include_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'admin/modules/policy-data/class-gdpr-cookie-consent-policy-data.php';

		$policy_data_instance = new GDPR_Cookie_Consent_Policy_Data();

		$upload_dir = wp_upload_dir();
		$file_path = trailingslashit($upload_dir['basedir']) . 'wplp-policy-data-export.csv';
		$file_url  = trailingslashit($upload_dir['baseurl']) . 'wplp-policy-data-export.csv';

		// Open file for writing
		$output = fopen($file_path, 'w');

		// Header row
		fputcsv($output, ['post_title', 'post_content', 'post_status', '_gdpr_policies_links_editor', '_gdpr_policies_domain']);

		$limit = 3000;
		$offset = 0;

		while (true) {
			$posts = get_posts([
				'post_type'      => 'gdprpolicies',
				'post_status'    => array( 'publish', 'draft' ),
				'posts_per_page' => $limit,
				'offset'         => $offset,
				'fields'         => 'ids',
			]);

			if (empty($posts)) {
				break;
			}

			foreach ($posts as $post_id) {
				$row = [
					$policy_data_instance::format_data( sanitize_text_field( get_the_title($post_id) ) ),
					$policy_data_instance::format_data( wp_strip_all_tags( sanitize_textarea_field( get_post_field( 'post_content', $post_id ) ) ) ),
					$policy_data_instance::format_data( get_post_status($post_id) ),
					$policy_data_instance::format_data( sanitize_text_field( get_post_meta($post_id, '_gdpr_policies_links_editor', true) ) ),
					$policy_data_instance::format_data( sanitize_text_field( get_post_meta($post_id, '_gdpr_policies_domain', true) ) ),
				];
				fputcsv($output, $row, ',', '"' );
			}

			$offset += $limit;
		}

		fclose($output);

		return [
			'success' => true,
			'download_url' => $file_url
		];
	}

	public function gdpr_get_data_request_form_fields( WP_REST_Request $request ) {

		$the_options = get_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
		
		return rest_ensure_response(
			array(
				'subject' => $the_options['data_req_subject'],
				'message' => $the_options['data_req_editor_message'] ?? '',
				'email'   => $the_options['data_req_email_address'] ?? '',
				'status'  => $the_options['data_reqs_on'],
			)
		);
	}

	public function gdpr_update_data_request_form_fields( WP_REST_Request $request ) {


		$subject = sanitize_text_field( $request->get_param( 'subject' ) );
		$message = $request->get_param( 'message' );
		if ( $message !== '' && $message !== null ) {
			$message = htmlentities( $message );
		} else {
			$message = '&lt;p&gt;Hi {name}&lt;/p&gt;&lt;p&gt;We have received your request on {blogname}. Depending on the specific request and legal obligations we might follow-up.&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;Kind regards,&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;{blogname}&lt;/p&gt;';
		}
		$email = sanitize_email( $request->get_param( 'email' ) );
		$status = $request->get_param( 'status' );
		$the_options = get_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );

		$the_options['data_req_subject'] = $subject;
		$the_options['data_req_editor_message'] = $message;
		$the_options['data_req_email_address'] = $email;
		$the_options['data_reqs_on'] = $status;
		update_option(GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options);

		return rest_ensure_response(
			array(
				'status' => true,
			)
		);
	}

	public function gdpr_fetch_advanced_settings( WP_REST_Request $request ) {
		$the_options         = get_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
		$is_tracking_allowed = get_option( 'gdpr_usage_tracking_allowed' );
		$plan                = $this->settings->get_plan();

		return rest_ensure_response(
			array(
				'cookie_usage_for'       => $the_options['cookie_usage_for'],
				'plan'                   => $plan,
				'is_multisite'           => is_multisite(),
				'consent_forward'        => $the_options['consent_forward'],
				'logging_on'             => $the_options['logging_on'],
				'is_ticked'              => $the_options['is_ticked'],
				'auto_hide'              => $the_options['auto_hide'],
				'auto_hide_delay'        => $the_options['auto_hide_delay'],
				'auto_scroll'            => $the_options['auto_scroll'],
				'auto_scroll_offset'     => $the_options['auto_scroll_offset'],
				'auto_click'             => $the_options['auto_click'],
				'auto_scroll_reload'     => $the_options['auto_scroll_reload'],
				'accept_reload'          => $the_options['accept_reload'],
				'decline_reload'         => $the_options['decline_reload'],
				'do_not_track_on'        => $the_options['do_not_track_on'],
				'delete_on_deactivation' => $the_options['delete_on_deactivation'],
				'show_credits'           => $plan === 'free' ? true : $the_options['show_credits'],
				'cookie_expiry'          => $the_options['cookie_expiry'],
				'enable_safe'            => $the_options['enable_safe'],
				'is_tracking_allowed'    => $is_tracking_allowed,
			)
		);
	}

	public function gdpr_restore_plugin_settings( WP_REST_Request $request ) {
		$this->gdpr_cookie_consent_restore_default_settings();

		return rest_ensure_response(
			array(
				'status' => true,
			)
		);
	}

	public function gdpr_export_plugin_settings( WP_REST_Request $request ) {
		return rest_ensure_response(
			array(
				'success' => true,
				'data' => get_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD )
			)
		);
	}

	public function gdpr_import_plugin_settings( WP_REST_Request $request ) {

		$settings = $request->get_param( 'settings' );

		$settings = json_decode( $settings, true );

		update_option(GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $settings);

		return rest_ensure_response(
			array(
				'status' => true,
			)
		);
	}

	public function convert_boolean( $value ) {
		if ( is_bool( $value ) ) {
			return $value;
		}

		if ( is_null( $value ) ) {
			return false;
		}

		$value = strtolower( trim( ( string ) $value ) );

		if ( in_array( $value, array( '1', 'true' ), true) ) {
			return true;
		}

		if ( in_array( $value, array( '0', 'false', '' ), true ) ) {
			return false;
		}

		return false;
	}

	public function gdpr_fetch_general_settings( WP_REST_Request $request ) {

		$the_options = get_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
		$language    = $the_options['lang_selected'] ?? 'en';

		if ( empty( $the_options['gcm_defaults'] ) ) {
			$the_options['gcm_defaults'] = json_encode(
				array(
					array(
						'region'                  => 'All',
						'ad_storage'              => 'denied',
						'analytics_storage'       => 'denied',
						'ad_user_data'            => 'denied',
						'ad_personalization'      => 'denied',
						'functionality_storage'   => 'granted',
						'personalization_storage' => 'denied',
						'security_storage'        => 'granted',
					),
				)
			);
		}

		$select_countries = array();
		if ( ! empty( $the_options['select_countries'] ) && count( $the_options['select_countries'] ) > 0 && $the_options['select_countries'][0] !== '' ) {
			$select_countries = $the_options['select_countries'];
		}

		$select_countries_ccpa = array();
		if ( ! empty( $the_options['select_countries_ccpa'] ) && count( $the_options['select_countries_ccpa'] ) > 0 && $the_options['select_countries_ccpa'][0] !== '' ) {
			$select_countries_ccpa = $the_options['select_countries_ccpa'];
		}

		$get_categories = Gdpr_Cookie_Consent_Cookie_Custom::get_categories();

		$cookies_categories = array_map(
			fn ( $label, $value ) => [
				'value' => $value,
				'label' => $label,
			],
			$get_categories,
			array_keys( $get_categories )
		);

		global $wpdb;
		$custom_cookies_list = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'gdpr_cookie_post_cookies ORDER BY id_gdpr_cookie_post_cookies DESC'), ARRAY_A );

		$cookies_table          = $wpdb->prefix . 'wpl_cookie_scan_cookies';
		$cookie_scan            = $wpdb->prefix . 'wpl_cookie_scan';
		$advanced_scripts_table = $wpdb->prefix . 'wpl_cookie_scripts';

		$scanned_cookies = $wpdb->get_results( $wpdb->prepare( 'SELECT id_wpl_cookie_scan_cookies, name, domain, duration, type, category, category_id, description FROM ' . $cookies_table . ' ORDER BY id_wpl_cookie_scan_cookies DESC' ), ARRAY_A );

		$cookie_scan_list = $wpdb->get_results( $wpdb->prepare( 'SELECT id_wpl_cookie_scan, created_at, status, total_url, total_category, total_cookies FROM ' . $cookie_scan . ' ORDER BY id_wpl_cookie_scan DESC' ), ARRAY_A );

		$posts = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type IN ('post', 'page') AND post_status = 'publish'" ), ARRAY_A );

		$advanced_scripts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$advanced_scripts_table}" ), ARRAY_A );

		return rest_ensure_response(
			array(
				'is_on'                                    => $this->convert_boolean( $the_options['is_on'] ),
				'is_iabtcf_on'                             => $this->convert_boolean( $the_options['is_iabtcf_on'] ),
				'is_gacm_on'                               => $this->convert_boolean( $the_options['is_gacm_on'] ),
				'is_gcm_on'                                => $this->convert_boolean( $the_options['is_gcm_on'] ),
				'gcm_defaults'                             => $the_options['gcm_defaults'],
				'gcm_wait_for_update_duration'             => absint( $the_options['gcm_wait_for_update_duration'] ),
				'is_gcm_url_passthrough'                   => $this->convert_boolean( $the_options['is_gcm_url_passthrough'] ),
				'is_gcm_ads_redact'                        => $this->convert_boolean( $the_options['is_gcm_ads_redact'] ),
				'is_gcm_debug_mode'                        => $this->convert_boolean( $the_options['is_gcm_debug_mode'] ),
				'is_gcm_advertiser_mode'                   => $this->convert_boolean( $the_options['is_gcm_advertiser_mode'] ),
				'cookie_usage_for'                         => $the_options['cookie_usage_for'],
				'is_worldwide_on_ccpa'                     => $this->convert_boolean( $the_options['is_worldwide_on_ccpa'] ),
				'is_worldwide_on'                          => $this->convert_boolean( $the_options['is_worldwide_on'] ),
				'is_eu_on'                                 => $this->convert_boolean( $the_options['is_eu_on'] ),
				'is_ccpa_on'                               => $this->convert_boolean( $the_options['is_ccpa_on'] ),
				'is_selectedCountry_on'                    => $this->convert_boolean( $the_options['is_selectedCountry_on'] ),
				'is_selectedCountry_on_ccpa'               => $this->convert_boolean( $the_options['is_selectedCountry_on_ccpa'] ),
				'select_countries'                         => $select_countries,
				'select_countries_ccpa'                    => $select_countries_ccpa,
				'posts'                                    => $posts,
				'restrict_posts'                           => count( $the_options['restrict_posts'] ) === 1 && $the_options['restrict_posts'][0] === '' ? array() : $the_options['restrict_posts'],
				'auto_banner_initialize'                   => $this->convert_boolean( $the_options['auto_banner_initialize'] ),
				'auto_banner_initialize_delay'             => absint( $the_options['auto_banner_initialize_delay'] ),
				'last_renewed'                             => get_option( 'wpl_consent_timestamp' ) ? esc_attr( gmdate( 'F j, Y g:i a T', get_option( 'wpl_consent_timestamp' ) ) ) : '',
				'wpl_gcm_check_is_scanning'                => get_transient( 'wpl_gcm_check_is_scanning' ) ? true : false,
				'wpl_gcm_latest_scan_result'               => $the_options['wpl_gcm_latest_scan_result'] ?? '',
				// eprivacy.
				'notify_message_eprivacy'                  => $the_options['notify_message_eprivacy'],
				// gdpr.
				'bar_heading_text'                         => $the_options['bar_heading_text'],
				'notify_message'                           => $the_options['notify_message'],
				'about_message'                            => $the_options['about_message'],
				// ccpa.
				'notify_message_ccpa'                      => $the_options['notify_message_ccpa'],
				'optout_text'                              => $the_options['optout_text'],
				// lgpd.
				'bar_heading_lgpd_text'                    => $the_options['bar_heading_lgpd_text'],
				'notify_message_lgpd'                      => $the_options['notify_message_lgpd'],
				'about_message_lgpd'                       => $the_options['about_message_lgpd'],

				'buffer_messages'                          => array(
					'iab'     => array(
						'notify_message' => $this->change_option_language( 'dash_notify_message_iabtcf', $language ),
						'about_message'  => $this->change_option_language( 'dash_about_message_iabtcf', $language ),
					),
					'non-iab' => array(
						'notify_message' => $this->change_option_language( 'dash_notify_message', $language ),
						'about_message'  => $this->change_option_language( 'dash_about_message', $language ),
					),
				),

				// Layout.
				'cookie_bar_as'                            => $the_options['cookie_bar_as'],
				'notify_position_vertical'                 => $the_options['notify_position_vertical'] ?? 'bottom',
				'notify_position_horizontal'               => $the_options['notify_position_horizontal'] ?? 'left',
				'popup_overlay'                            => $this->convert_boolean( $the_options['popup_overlay'] ),
				'notify_animate_hide'                      => $this->convert_boolean( $the_options['notify_animate_hide'] ),
				'notify_animate_show'                      => $this->convert_boolean( $the_options['notify_animate_show'] ),
				'is_dynamic_lang_on'                       => empty( $the_options['is_dynamic_lang_on'] ) ? false : $this->convert_boolean( $the_options['is_dynamic_lang_on'] ),
				'select_pages'                             => count( $the_options['select_pages'] ) === 1 && $the_options['select_pages'][0] === '' ? array() : $the_options['select_pages'],
				'selected_template_json'                   => $the_options['selected_template_json'],

				// Test Banner A.
				'cookie_bar1_name'                         => $the_options['cookie_bar1_name'] ?? 'Test Banner A',
				'default_cookie_bar'                       => $the_options['default_cookie_bar'] ?? true,
				'cookie_bar_color1'                        => $the_options['cookie_bar_color1'] ?? '#ffffff',
				'cookie_bar_opacity1'                      => $the_options['cookie_bar_opacity1'] ?? '0.80',
				'cookie_text_color1'                       => $the_options['cookie_text_color1'] ?? '#000000',
				'border_style1'                            => $the_options['border_style1'] ?? 'none',
				'cookie_bar_border_width1'                 => $the_options['cookie_bar_border_width1'] ?? '0',
				'cookie_border_color1'                     => $the_options['cookie_border_color1'] ?? '#ffffff',
				'cookie_bar_border_radius1'                => $the_options['cookie_bar_border_radius1'] ?? '0',
				'cookie_font1'                             => $the_options['cookie_font1'] ?? 'inherit',
				'banner_image1'                            => get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1 ),

				'button_readmore_is_on1'                   => $the_options['button_readmore_is_on1'],
				'button_readmore_text1'                    => $the_options['button_readmore_text1'] ?? 'Read More',
				'button_readmore_link_color1'              => $the_options['button_readmore_link_color1'] ?? '#359bf5',
				'button_readmore_as_button1'               => $the_options['button_readmore_as_button1'],
				'button_readmore_url_type1'                => $the_options['button_readmore_url_type1'],
				'button_readmore_page1'                    => $the_options['button_readmore_page1'] ?? '0',
				'button_readmore_wp_page1'                 => $the_options['button_readmore_wp_page1'],
				'button_readmore_new_win1'                 => $the_options['button_readmore_new_win1'],

				'show_again1'                              => $the_options['show_again1'],
				'show_again_text1'                         => $the_options['show_again_text1'] ?? 'Cookie Settings',
				'button_revoke_consent_text_color1'        => $the_options['button_revoke_consent_text_color1'] ?? '',
				'button_revoke_consent_background_color1'  => $the_options['button_revoke_consent_background_color1'] ?? '',
				'show_again_position1'                     => $the_options['show_again_position1'] ?? 'right',
				'show_again_margin1'                       => $the_options['show_again_margin1'] ?? '5',

				'button_accept_is_on1'                     => $the_options['button_accept_is_on1'],
				'button_accept_text1'                      => $the_options['button_accept_text1'] ?? 'Accept',
				'button_accept_link_color1'                => $the_options['button_accept_link_color1'] ?? '#ffffff',
				'button_accept_as_button1'                 => $the_options['button_accept_as_button1'],
				'button_accept_action1'                    => $the_options['button_accept_action1'] ?? '#cookie_action_close_header',
				'button_accept_button_color1'              => $the_options['button_accept_button_color1'] ?? '#18a300',
				'button_accept_button_border_style1'       => $the_options['button_accept_button_border_style1'] ?? 'none',
				'button_accept_button_border_color1'       => $the_options['button_accept_button_border_color1'] ?? '#18a300',
				'button_accept_button_opacity1'            => $the_options['button_accept_button_opacity1'] ?? '1',
				'button_accept_button_border_width1'       => $the_options['button_accept_button_border_width1'] ?? '0',
				'button_accept_button_border_radius1'      => $the_options['button_accept_button_border_radius1'] ?? '0',

				'button_accept_all_is_on1'                 => $the_options['button_accept_all_is_on1'],
				'button_accept_all_text1'                  => $the_options['button_accept_all_text1'] ?? 'Accept All',
				'button_accept_all_link_color1'            => $the_options['button_accept_all_link_color1'] ?? '#ffffff',
				'button_accept_all_as_button1'             => $the_options['button_accept_all_as_button1'],
				'button_accept_all_action1'                => $the_options['button_accept_all_action1'] ?? '#cookie_action_close_header' ?? '#cookie_action_close_header',
				'button_accept_all_button_color1'          => $the_options['button_accept_all_button_color1'] ?? '#18a300',
				'button_accept_all_btn_border_style1'      => $the_options['button_accept_all_btn_border_style1'] ?? 'none',
				'button_accept_all_btn_border_color1'      => $the_options['button_accept_all_btn_border_color1'] ?? '#18a300',
				'button_accept_all_btn_opacity1'           => $the_options['button_accept_all_btn_opacity1'] ?? '1',
				'button_accept_all_btn_border_width1'      => $the_options['button_accept_all_btn_border_width1'] ?? '0',
				'button_accept_all_btn_border_radius1'     => $the_options['button_accept_all_btn_border_radius1'] ?? '0',

				'button_decline_is_on1'                    => $the_options['button_decline_is_on1'],
				'button_decline_text1'                     => $the_options['button_decline_text1'] ?? 'Decline',
				'button_decline_link_color1'               => $the_options['button_decline_link_color1'] ?? '#ffffff',
				'button_decline_as_button1'                => $the_options['button_decline_as_button1'],
				'button_decline_action1'                   => $the_options['button_decline_action1'] ?? '#cookie_action_close_header_reject',
				'button_decline_button_color1'             => $the_options['button_decline_button_color1'] ?? '#333333',
				'button_decline_button_border_style1'      => $the_options['button_decline_button_border_style1'] ?? 'none',
				'button_decline_button_border_color1'      => $the_options['button_decline_button_border_color1'] ?? '#333333',
				'button_decline_button_opacity1'           => $the_options['button_decline_button_opacity1'] ?? '1',
				'button_decline_button_border_width1'      => $the_options['button_decline_button_border_width1'] ?? '0',
				'button_decline_button_border_radius1'     => $the_options['button_decline_button_border_radius1'] ?? '0',

				'button_settings_is_on1'                   => $the_options['button_settings_is_on1'],
				'button_settings_text1'                    => $the_options['button_settings_text1'],
				'button_settings_link_color1'              => $the_options['button_settings_link_color1'] ?? '#ffffff',
				'button_settings_as_button1'               => $the_options['button_settings_as_button1'],
				'button_settings_button_color1'            => $the_options['button_settings_button_color1'] ?? '#333333',
				'button_settings_button_border_style1'     => $the_options['button_settings_button_border_style1'] ?? 'none',
				'button_settings_button_border_color1'     => $the_options['button_settings_button_border_color1'] ?? '#333333',
				'button_settings_button_opacity1'          => $the_options['button_settings_button_opacity1'] ?? '1',
				'button_settings_button_border_width1'     => $the_options['button_settings_button_border_width1'] ?? '0',
				'button_settings_button_border_radius1'    => $the_options['button_settings_button_border_radius1'] ?? '0',
				'button_settings_display_cookies1'         => $the_options['button_settings_display_cookies1'],

				// Test Banner B.
				'cookie_bar2_name'                         => $the_options['cookie_bar2_name'] ?? 'Test Banner A',
				'cookie_bar_color2'                        => $the_options['cookie_bar_color2'] ?? '#ffffff',
				'cookie_bar_opacity2'                      => $the_options['cookie_bar_opacity2'] ?? '0.80',
				'cookie_text_color2'                       => $the_options['cookie_text_color2'] ?? '#000000',
				'border_style2'                            => $the_options['border_style2'] ?? 'none',
				'cookie_bar_border_width2'                 => $the_options['cookie_bar_border_width2'] ?? '0',
				'cookie_border_color2'                     => $the_options['cookie_border_color2'] ?? '#ffffff',
				'cookie_bar_border_radius2'                => $the_options['cookie_bar_border_radius2'] ?? '0',
				'cookie_font2'                             => $the_options['cookie_font2'] ?? 'inherit',
				'banner_image2'                            => get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1 ),

				'button_readmore_is_on2'                   => $the_options['button_readmore_is_on2'],
				'button_readmore_text2'                    => $the_options['button_readmore_text2'] ?? 'Read More',
				'button_readmore_link_color2'              => $the_options['button_readmore_link_color2'] ?? '#359bf5',
				'button_readmore_as_button2'               => $the_options['button_readmore_as_button2'],
				'button_readmore_url_type2'                => $the_options['button_readmore_url_type2'],
				'button_readmore_page2'                    => $the_options['button_readmore_page2'] ?? '0',
				'button_readmore_wp_page2'                 => $the_options['button_readmore_wp_page2'],
				'button_readmore_new_win2'                 => $the_options['button_readmore_new_win2'],

				'show_again2'                              => $the_options['show_again2'],
				'show_again_text2'                         => $the_options['show_again_text2'] ?? 'Cookie Settings',
				'button_revoke_consent_text_color2'        => $the_options['button_revoke_consent_text_color2'] ?? '',
				'button_revoke_consent_background_color2'  => $the_options['button_revoke_consent_background_color2'] ?? '',
				'show_again_position2'                     => $the_options['show_again_position2'] ?? 'right',
				'show_again_margin2'                       => $the_options['show_again_margin2'] ?? '5',

				'button_accept_is_on2'                     => $the_options['button_accept_is_on2'],
				'button_accept_text2'                      => $the_options['button_accept_text2'] ?? 'Accept',
				'button_accept_link_color2'                => $the_options['button_accept_link_color2'] ?? '#ffffff',
				'button_accept_as_button2'                 => $the_options['button_accept_as_button2'],
				'button_accept_action2'                    => $the_options['button_accept_action2'] ?? '#cookie_action_close_header',
				'button_accept_button_color2'              => $the_options['button_accept_button_color2'] ?? '#18a300',
				'button_accept_button_border_style2'       => $the_options['button_accept_button_border_style2'] ?? 'none',
				'button_accept_button_border_color2'       => $the_options['button_accept_button_border_color2'] ?? '#18a300',
				'button_accept_button_opacity2'            => $the_options['button_accept_button_opacity2'] ?? '1',
				'button_accept_button_border_width2'       => $the_options['button_accept_button_border_width2'] ?? '0',
				'button_accept_button_border_radius2'      => $the_options['button_accept_button_border_radius2'] ?? '0',

				'button_accept_all_is_on2'                 => $the_options['button_accept_all_is_on2'],
				'button_accept_all_text2'                  => $the_options['button_accept_all_text2'] ?? 'Accept All',
				'button_accept_all_link_color2'            => $the_options['button_accept_all_link_color2'] ?? '#ffffff',
				'button_accept_all_as_button2'             => $the_options['button_accept_all_as_button2'],
				'button_accept_all_action2'                => $the_options['button_accept_all_action2'] ?? '#cookie_action_close_header' ?? '#cookie_action_close_header',
				'button_accept_all_button_color2'          => $the_options['button_accept_all_button_color2'] ?? '#18a300',
				'button_accept_all_btn_border_style2'      => $the_options['button_accept_all_btn_border_style2'] ?? 'none',
				'button_accept_all_btn_border_color2'      => $the_options['button_accept_all_btn_border_color2'] ?? '#18a300',
				'button_accept_all_btn_opacity2'           => $the_options['button_accept_all_btn_opacity2'] ?? '1',
				'button_accept_all_btn_border_width2'      => $the_options['button_accept_all_btn_border_width2'] ?? '0',
				'button_accept_all_btn_border_radius2'     => $the_options['button_accept_all_btn_border_radius2'] ?? '0',

				'button_decline_is_on2'                    => $the_options['button_decline_is_on2'],
				'button_decline_text2'                     => $the_options['button_decline_text2'] ?? 'Decline',
				'button_decline_link_color2'               => $the_options['button_decline_link_color2'] ?? '#ffffff',
				'button_decline_as_button2'                => $the_options['button_decline_as_button2'],
				'button_decline_action2'                   => $the_options['button_decline_action2'] ?? '#cookie_action_close_header_reject',
				'button_decline_button_color2'             => $the_options['button_decline_button_color2'] ?? '#333333',
				'button_decline_button_border_style2'      => $the_options['button_decline_button_border_style2'] ?? 'none',
				'button_decline_button_border_color2'      => $the_options['button_decline_button_border_color2'] ?? '#333333',
				'button_decline_button_opacity2'           => $the_options['button_decline_button_opacity2'] ?? '1',
				'button_decline_button_border_width2'      => $the_options['button_decline_button_border_width2'] ?? '0',
				'button_decline_button_border_radius2'     => $the_options['button_decline_button_border_radius2'] ?? '0',

				'button_settings_is_on2'                   => $the_options['button_settings_is_on2'],
				'button_settings_text2'                    => $the_options['button_settings_text2'],
				'button_settings_link_color2'              => $the_options['button_settings_link_color2'] ?? '#ffffff',
				'button_settings_as_button2'               => $the_options['button_settings_as_button2'],
				'button_settings_button_color2'            => $the_options['button_settings_button_color2'] ?? '#333333',
				'button_settings_button_border_style2'     => $the_options['button_settings_button_border_style2'] ?? 'none',
				'button_settings_button_border_color2'     => $the_options['button_settings_button_border_color2'] ?? '#333333',
				'button_settings_button_opacity2'          => $the_options['button_settings_button_opacity2'] ?? '1',
				'button_settings_button_border_width2'     => $the_options['button_settings_button_border_width2'] ?? '0',
				'button_settings_button_border_radius2'    => $the_options['button_settings_button_border_radius2'] ?? '0',
				'button_settings_display_cookies2'         => $the_options['button_settings_display_cookies2'],

				// Content and Design.
				'background'                               => $the_options['background'],
				'opacity'                                  => $the_options['opacity'],
				'text'                                     => $the_options['text'],
				'background_border_style'                  => $the_options['background_border_style'],
				'background_border_width'                  => $the_options['background_border_width'],
				'background_border_color'                  => $the_options['background_border_color'],
				'background_border_radius'                 => $the_options['background_border_radius'],
				'font_family'                              => $the_options['font_family'],
				'banner_image'                             => get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD ),

				'button_readmore_is_on'                    => $the_options['button_readmore_is_on'],
				'button_readmore_text'                     => $the_options['button_readmore_text'] ?? 'Read More',
				'button_readmore_link_color'               => $the_options['button_readmore_link_color'] ?? '#359bf5',
				'button_readmore_as_button'                => $the_options['button_readmore_as_button'],
				'button_readmore_url_type'                 => $the_options['button_readmore_url_type'],
				'button_readmore_page'                     => $the_options['button_readmore_page'] ?? '0',
				'button_readmore_wp_page'                  => $the_options['button_readmore_wp_page'],
				'button_readmore_new_win'                  => $the_options['button_readmore_new_win'],

				'show_again'                               => $the_options['show_again'],
				'show_again_text'                          => $the_options['show_again_text'] ?? 'Cookie Settings',
				'button_revoke_consent_text_color'         => $the_options['button_revoke_consent_text_color'] ?? '',
				'button_revoke_consent_background_color'   => $the_options['button_revoke_consent_background_color'] ?? '',
				'show_again_position'                      => $the_options['show_again_position'] ?? 'right',
				'show_again_margin'                        => $the_options['show_again_margin'] ?? '5',

				'button_accept_is_on'                      => $the_options['button_accept_is_on'],
				'button_accept_text'                       => $the_options['button_accept_text'] ?? 'Accept',
				'button_accept_link_color'                 => $the_options['button_accept_link_color'] ?? '#ffffff',
				'button_accept_as_button'                  => $the_options['button_accept_as_button'],
				'button_accept_action'                     => $the_options['button_accept_action'] ?? '#cookie_action_close_header',
				'button_accept_button_color'               => $the_options['button_accept_button_color'] ?? '#18a300',
				'button_accept_button_border_style'        => $the_options['button_accept_button_border_style'] ?? 'none',
				'button_accept_button_border_color'        => $the_options['button_accept_button_border_color'] ?? '#18a300',
				'button_accept_button_opacity'             => $the_options['button_accept_button_opacity'] ?? '1',
				'button_accept_button_border_width'        => $the_options['button_accept_button_border_width'] ?? '0',
				'button_accept_button_border_radius'       => $the_options['button_accept_button_border_radius'] ?? '0',

				'button_accept_all_is_on'                  => $the_options['button_accept_all_is_on'],
				'button_accept_all_text'                   => $the_options['button_accept_all_text'] ?? 'Accept All',
				'button_accept_all_link_color'             => $the_options['button_accept_all_link_color'] ?? '#ffffff',
				'button_accept_all_as_button'              => $the_options['button_accept_all_as_button'],
				'button_accept_all_action'                 => $the_options['button_accept_all_action'] ?? '#cookie_action_close_header' ?? '#cookie_action_close_header',
				'button_accept_all_button_color'           => $the_options['button_accept_all_button_color'] ?? '#18a300',
				'button_accept_all_btn_border_style'       => $the_options['button_accept_all_btn_border_style'] ?? 'none',
				'button_accept_all_btn_border_color'       => $the_options['button_accept_all_btn_border_color'] ?? '#18a300',
				'button_accept_all_btn_opacity'            => $the_options['button_accept_all_btn_opacity'] ?? '1',
				'button_accept_all_btn_border_width'       => $the_options['button_accept_all_btn_border_width'] ?? '0',
				'button_accept_all_btn_border_radius'      => $the_options['button_accept_all_btn_border_radius'] ?? '0',

				'button_decline_is_on'                     => $the_options['button_decline_is_on'],
				'button_decline_text'                      => $the_options['button_decline_text'] ?? 'Decline',
				'button_decline_link_color'                => $the_options['button_decline_link_color'] ?? '#ffffff',
				'button_decline_as_button'                 => $the_options['button_decline_as_button'],
				'button_decline_action'                    => $the_options['button_decline_action'] ?? '#cookie_action_close_header_reject',
				'button_decline_button_color'              => $the_options['button_decline_button_color'] ?? '#333333',
				'button_decline_button_border_style'       => $the_options['button_decline_button_border_style'] ?? 'none',
				'button_decline_button_border_color'       => $the_options['button_decline_button_border_color'] ?? '#333333',
				'button_decline_button_opacity'            => $the_options['button_decline_button_opacity'] ?? '1',
				'button_decline_button_border_width'       => $the_options['button_decline_button_border_width'] ?? '0',
				'button_decline_button_border_radius'      => $the_options['button_decline_button_border_radius'] ?? '0',

				'button_settings_is_on'                    => $the_options['button_settings_is_on'],
				'button_settings_text'                     => $the_options['button_settings_text'],
				'button_settings_link_color'               => $the_options['button_settings_link_color'] ?? '#ffffff',
				'button_settings_as_button'                => $the_options['button_settings_as_button'],
				'button_settings_button_color'             => $the_options['button_settings_button_color'] ?? '#333333',
				'button_settings_button_border_style'      => $the_options['button_settings_button_border_style'] ?? 'none',
				'button_settings_button_border_color'      => $the_options['button_settings_button_border_color'] ?? '#333333',
				'button_settings_button_opacity'           => $the_options['button_settings_button_opacity'] ?? '1',
				'button_settings_button_border_width'      => $the_options['button_settings_button_border_width'] ?? '0',
				'button_settings_button_border_radius'     => $the_options['button_settings_button_border_radius'] ?? '0',
				'button_settings_display_cookies'          => $the_options['button_settings_display_cookies'],
				'gdpr_css_text'                            => $the_options['gdpr_css_text'] ?? '',

				// CCPA.
				'button_confirm_text'                      => $the_options['button_confirm_text'] ?? 'Confirm',
				'button_confirm_link_color'                => $the_options['button_confirm_link_color'] ?? '#ffffff',
				'button_confirm_button_color'              => $the_options['button_confirm_button_color'] ?? '#18a300',
				'button_confirm_button_border_style'       => $the_options['button_confirm_button_border_style'] ?? 'none',
				'button_confirm_button_border_color'       => $the_options['button_confirm_button_border_color'] ?? '#18a300',
				'button_confirm_button_opacity'            => $the_options['button_confirm_button_opacity'] ?? '1',
				'button_confirm_button_border_width'       => $the_options['button_confirm_button_border_width'] ?? '0',
				'button_confirm_button_border_radius'      => $the_options['button_confirm_button_border_radius'] ?? '0',

				'button_cancel_text'                       => $the_options['button_cancel_text'] ?? 'Cancel',
				'button_cancel_link_color'                 => $the_options['button_cancel_link_color'] ?? '#ffffff',
				'button_cancel_button_color'               => $the_options['button_cancel_button_color'] ?? '#333333',
				'button_cancel_button_border_style'        => $the_options['button_cancel_button_border_style'] ?? 'none',
				'button_cancel_button_border_color'        => $the_options['button_cancel_button_border_color'] ?? '#333333',
				'button_cancel_button_opacity'             => $the_options['button_cancel_button_opacity'] ?? '1',
				'button_cancel_button_border_width'        => $the_options['button_cancel_button_border_width'] ?? '0',
				'button_cancel_button_border_radius'       => $the_options['button_cancel_button_border_radius'] ?? '0',

				'button_donotsell_text'                    => $the_options['button_donotsell_text'] ?? 'Do Not Sell My Personal Information',
				'button_donotsell_link_color'              => $the_options['button_donotsell_link_color'] ?? '#359bf5',

				// GDPR and CCPA (multiple legislation).
				// GDPR:
				'multiple_legislation_cookie_bar_color1'   => $the_options['multiple_legislation_cookie_bar_color1'] ?? '#ffffff',
				'multiple_legislation_cookie_bar_opacity1' => $the_options['multiple_legislation_cookie_bar_opacity1'] ?? '1',
				'multiple_legislation_cookie_text_color1'  => $the_options['multiple_legislation_cookie_text_color1'] ?? '#000000',
				'multiple_legislation_border_style1'       => $the_options['multiple_legislation_border_style1'] ?? 'none',
				'multiple_legislation_cookie_bar_border_width1' => $the_options['multiple_legislation_cookie_bar_border_width1'] ?? '0',
				'multiple_legislation_cookie_border_color1' => $the_options['multiple_legislation_cookie_border_color1'] ?? '#ffffff',
				'multiple_legislation_cookie_bar_border_radius1' => $the_options['multiple_legislation_cookie_bar_border_radius1'] ?? '0',
				'multiple_legislation_cookie_font1'        => $the_options['multiple_legislation_cookie_font1'] ?? 'inherit',
				'banner_image_ml'                          => get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELDML1 ),

				// CCPA:
				'button_confirm_text1'                     => $the_options['button_confirm_text1'] ?? 'Confirm',
				'button_confirm_link_color1'               => $the_options['button_confirm_link_color1'] ?? '#ffffff',
				'button_confirm_button_color1'             => $the_options['button_confirm_button_color1'] ?? '#18a300',
				'button_confirm_button_border_style1'      => $the_options['button_confirm_button_border_style1'] ?? 'none',
				'button_confirm_button_border_color1'      => $the_options['button_confirm_button_border_color1'] ?? '#18a300',
				'button_confirm_button_opacity1'           => $the_options['button_confirm_button_opacity1'] ?? '1',
				'button_confirm_button_border_width1'      => $the_options['button_confirm_button_border_width1'] ?? '0',
				'button_confirm_button_border_radius1'     => $the_options['button_confirm_button_border_radius1'] ?? '0',

				'button_cancel_text1'                      => $the_options['button_cancel_text1'] ?? 'Cancel',
				'button_cancel_link_color1'                => $the_options['button_cancel_link_color1'] ?? '#ffffff',
				'button_cancel_button_color1'              => $the_options['button_cancel_button_color1'] ?? '#333333',
				'button_cancel_button_border_style1'       => $the_options['button_cancel_button_border_style1'] ?? 'none',
				'button_cancel_button_border_color1'       => $the_options['button_cancel_button_border_color1'] ?? '#333333',
				'button_cancel_button_opacity1'            => $the_options['button_cancel_button_opacity1'] ?? '1',
				'gdpr-cookie-cancel-border-width1'         => $the_options['gdpr-cookie-cancel-border-width1'] ?? '0',
				'button_cancel_button_border_radius1'      => $the_options['button_cancel_button_border_radius1'] ?? '0',
				'button_donotsell_text1'                   => $the_options['button_donotsell_text1'] ?? 'Do Not Sell My Personal Information',
				'button_donotsell_link_color1'             => $the_options['button_donotsell_link_color1'] ?? '#359bf5',
				// Cookie Manager.
				'custom_cookies_list'                      => $custom_cookies_list,
				'cookies_categories'                       => $cookies_categories,
				'scanned_cookies'                          => $scanned_cookies,
				'cookie_scan_list'                         => $cookie_scan_list,
				'scan_schedule_data'                       => get_option( 'gdpr_scan_schedule_data' ),
				'scan_in_progress'                         => get_option( 'gdpr_scanning_action_hash' ) ? true : false,
				// Script Blocker.
				'is_script_blocker_on'                     => $the_options['is_script_blocker_on'],
				'header_scripts'                           => $the_options['header_scripts'] ?? '',
				'body_scripts'                             => $the_options['body_scripts'] ?? '',
				'footer_scripts'                           => $the_options['footer_scripts'] ?? '',
				'advanced_scripts'                         => $advanced_scripts,
				'is_script_dependency_on'                  => $the_options['is_script_dependency_on'],
				'header_dependency'                        => $the_options['header_dependency'] ?? '',
				'footer_dependency'                        => $the_options['footer_dependency'] ?? '',
				'whitelist_scripts'                        => get_option( 'wpl_options_custom-scripts' )['whitelist_script'],
			)
		);
	}

	public function gdpr_schedule_scan( WP_REST_Request $request ) {
		$schedule_scan = $request->get_param( 'schedule_scan' );
		if ( empty( $schedule_scan ) ) {
			return new WP_REST_Response(
				array(
					'status'  => 'error',
					'message' => 'Schedule scan data is empty',
				),
				400
			);
		}

		$scan_as           = sanitize_text_field( $schedule_scan['schedule_scan_as'] ?? '' );
		$scan_date         = sanitize_text_field( $schedule_scan['schedule_scan_date'] ?? '' );
		$scan_time_value   = sanitize_text_field( $schedule_scan['schedule_scan_time_value'] ?? '' );
		$scan_day          = sanitize_text_field( $schedule_scan['schedule_scan_day'] ?? '' );
		$next_scan_is_when = sanitize_text_field( $schedule_scan['next_scan_is_when'] ?? '' );
		$scan_when         = sanitize_text_field( $schedule_scan['schedule_scan_when'] ?? '' );

		$schedule_scan_data = array(
			'schedule_scan_as'  => $scan_as,
			'scan_date'         => $scan_date,
			'scan_time_value'   => $scan_time_value,
			'scan_day'          => $scan_day,
			'next_scan_is_when' => $next_scan_is_when,
			'scan_when'         => $scan_when,
		);

		update_option( 'gdpr_scan_schedule_data', $schedule_scan_data );

		return new WP_REST_Response(
			array(
				'status'  => 'success',
				'message' => __( 'Scan Scheduled Successfully!!!', 'gdpr-cookie-consent' ),
			),
			200
		);
	}

	public function gdpr_renew_user_consent( WP_REST_Request $request ) {

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

		return rest_ensure_response(
			array(
				'status' => true,
				'message' => 'Consent Renewed Successfully.',
			)
		);
	}

	public function gdpr_modify_gcm_regions( WP_REST_Request $request ) {

		$regions = $request->get_param( 'regionArray' );

		$the_options                 = Gdpr_Cookie_Consent::gdpr_get_settings();
		$the_options['gcm_defaults'] = json_encode( $regions );

		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );	
	
		return rest_ensure_response(
			array(
				'status' => true,
				'message' => 'Region Updated Successfully.',
			)
		);
	}

	public function gdpr_check_gcm_status( WP_REST_Request $request ) {

		if ( get_transient( 'wpl_gcm_check_is_scanning' ) ) {
			return new WP_REST_Response( [ 'status' => 'success', 'message' => 'Scanning In Progress' ], 200 );
		}

		// check gcm status.
		$wpl_api_url   = 'https://app.wplegalpages.com/wp-json/wplcookies/v2/';
		$site_url      = site_url();
		$response_url  = get_rest_url(null, 'gdpr/v2/update_gcm_status');
		$response      = wp_remote_get( $wpl_api_url . 'get_gcm_status' . '?url=' . $site_url . '&response_url=' . $response_url );
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( 200 !== $response_code ) {
			return new WP_REST_Response( [ 'status' => 'error', 'message' => 'Failed Scanning' ], 400 );
		}

		set_transient( 'wpl_gcm_check_is_scanning', true );

		return new WP_REST_Response( [ 'status' => 'success', 'message' => 'Scanning Started' ], 200 );
	}

	public function gdpr_custom_cookie( WP_REST_Request $request ) {
		$custom_cookie = (array) $request->get_param( 'custom_cookie' );

		$allowed_actions = array( 'insert', 'update', 'delete' );

		$action = $custom_cookie['action'] ?? '';

		if ( ! in_array( $action, $allowed_actions, true ) ) {
			return new WP_REST_Response(
				array(
		            'status'  => 'error',
		            'message' => __( 'This action is not allowed', 'gdpr-cookie-consent' ),
				),
		        500
			);
		}

		$action = 'gdpr_' . $action . '_custom_cookie';

		$response = $this->{$action}( $custom_cookie );

		return new WP_REST_Response(
			array(
				'status'  => $response['status'],
				'message' => $response['message'],
			),
			$response['code']
		);
	}

	public function gdpr_insert_custom_cookie( $params ) {
		
		$cookies_array = $this->gdpr_sanitize_custom_cookie_params( $params );

		if ( isset( $cookies_array['code'] ) ) {
			return $cookies_array;
		}
		
		global $wpdb;
		$post_cookies_table = $wpdb->prefix . 'gdpr_cookie_post_cookies';

		$inserted = $wpdb->insert( $post_cookies_table, $cookies_array );

		if ( ! $inserted ) {
			return array(
				'status'  => 'error',
				'message' => __( 'Failed to add cookie.', 'gdpr-cookie-consent' ),
				'code'    => 400,
			);
		}

		return array(
			'status'  => 'success',
			'message' => __( 'Cookie Saved Successfully!!!', 'gdpr-cookie-consent' ),
			'code'    => 201,
		);
	}

	public function gdpr_sanitize_custom_cookie_params( $params ) {
		$name        = sanitize_text_field( $params['name'] ?? '' );
		$category    = sanitize_text_field( $params['category'] ?? '' );
		$category_id = absint( $params['category_id'] ?? 0 );
		$description = sanitize_text_field( $params['description'] ?? '' );
		$duration    = sanitize_text_field( $params['duration'] ?? '' );
		$type        = sanitize_text_field( $params['type'] ?? '' );
			
		$domain = '';
		if ( ! empty( $params['domain'] ) ) {
			$parsed = wp_parse_url( esc_url_raw( $params['domain'] ) );
			$domain = $parsed['host'] ?? $parsed['path'] ?? '';
		}

		if ( ! $name || ! $domain || ! $category || ! $category_id || ! $duration || ! $type ) {
			return array(
				'status'  => 'error',
				'message' => __( 'Please fill all the required fields.', 'gdpr-cookie-consent' ),
				'code'    => 400,
			);
		}

		return array(
			'name'        => $name,
			'domain'      => $domain,
			'category'    => $category,
			'category_id' => $category_id,
			'type'        => $type,
			'description' => $description,
			'duration'    => $duration,
		);
	}

	public function gdpr_update_custom_cookie( $params ) {
		$cookies_array = $this->gdpr_sanitize_custom_cookie_params( $params );

		$id = absint( $params['id'] ?? 0 );

		if ( $id === 0 ) {
			return array(
				'status'  => 'error',
				'message' => __( 'Invalid ID Provided.', 'gdpr-cookie-consent' ),
				'code'    => 400,
			);
		}

		if ( isset( $cookies_array['code'] ) ) {
			return $cookies_array;
		}
		
		global $wpdb;
		$post_cookies_table = $wpdb->prefix . 'gdpr_cookie_post_cookies';

		$where = array(
			'id_gdpr_cookie_post_cookies' => $id,
		);

		$updated = $wpdb->update( $post_cookies_table, $cookies_array, $where );

		if ( ! $updated ) {
			return array(
				'status'  => 'error',
				'message' => __( 'Failed to update cookie.', 'gdpr-cookie-consent' ),
				'code'    => 400,
			);
		}

		return array(
			'status'  => 'success',
			'message' => __( 'Cookie Updated Successfully!!!', 'gdpr-cookie-consent' ),
			'code'    => 200,
		);
	}

	public function gdpr_delete_custom_cookie( $params ) {
		$id = absint( $params['id'] ?? 0 );

		if ( $id === 0 ) {
			return array(
				'status'  => 'error',
				'message' => __( 'Invalid ID Provided.', 'gdpr-cookie-consent' ),
				'code'    => 400,
			);
		}

		global $wpdb;
		$post_cookies_table = $wpdb->prefix . 'gdpr_cookie_post_cookies';

		$where = array(
			'id_gdpr_cookie_post_cookies' => $id,
		);

		$deleted = $wpdb->delete( $post_cookies_table, $where );

		if ( ! $deleted ) {
			return array(
				'status'  => 'error',
				'message' => __( 'Failed to delete cookie.', 'gdpr-cookie-consent' ),
				'code'    => 400,
			);
		}

		return array(
			'status'  => 'success',
			'message' => __( 'Cookie Deleted Successfully!!!', 'gdpr-cookie-consent' ),
			'code'    => 200,
		);
	}

	public function gdpr_cookie_data( WP_REST_Request $request ) {

		$custom_cookie = (array) $request->get_param( 'cookie_data' );

		$allowed_actions = array( 'edit', 'clear' );

		$action = $custom_cookie['action'] ?? '';

		if ( ! in_array( $action, $allowed_actions, true ) ) {
			return new WP_REST_Response(
				array(
		            'status'  => 'error',
		            'message' => __( 'This action is not allowed', 'gdpr-cookie-consent' ),
				),
		        500
			);
		}	

		$action = 'gdpr_' . $action . '_cookie';

		$response = $this->{$action}( $custom_cookie );

		return new WP_REST_Response(
			array(
				'status'  => $response['status'],
				'message' => $response['message'],
			),
			$response['code']
		);
	}

	public function gdpr_edit_cookie( $params ) {
		$id          = absint( $params['id'] ?? 0 );
		$category    = sanitize_text_field( $params['category'] ?? '' );
		$category_id = absint( $params['category_id'] ?? 0 );
		$description = sanitize_text_field( $params['description'] ?? '' );

		if ( ! $category || ! $category_id ) {
			return array(
				'status'  => 'error',
				'message' => __( 'Please fill all the required fields.', 'gdpr-cookie-consent' ),
				'code'    => 400,
			);
		}

		global $wpdb;
		$post_cookies_table = $wpdb->prefix . 'wpl_cookie_scan_cookies';

		$where = array(
			'id_wpl_cookie_scan_cookies' => $id,
		);

		$cookies_array = array(
			'category'    => $category,
			'category_id' => $category_id,
			'description' => $description,
		);

		$updated = $wpdb->update( $post_cookies_table, $cookies_array, $where );

		if ( ! $updated ) {
			return array(
				'status'  => 'error',
				'message' => __( 'Failed to update cookie.', 'gdpr-cookie-consent' ),
				'code'    => 400,
			);
		}

		return array(
			'status'  => 'success',
			'message' => __( 'Cookie Updated Successfully!!!', 'gdpr-cookie-consent' ),
			'code'    => 200,
		);
	}

	public function gdpr_clear_cookie() {
		global $wpdb;
		$post_cookies_table = $wpdb->prefix . 'wpl_cookie_scan_cookies';

		$cleared = $wpdb->query( "TRUNCATE TABLE {$post_cookies_table}" );

		if ( ! $cleared ) {
			return array(
				'status'  => 'error',
				'message' => __( 'Failed to clear cookies.', 'gdpr-cookie-consent' ),
				'code'    => 400,
			);
		}

		return array(
			'status'  => 'success',
			'message' => __( 'Cookie Cleared Successfully!!!', 'gdpr-cookie-consent' ),
			'code'    => 200,
		);
	}

	function gdpr_auto_generate_banner( WP_REST_Request $request ) {
		$background_color = sanitize_text_field( $request->get_param( 'color' ) ?? '' );
		if ( empty( $background_color ) ) {
			return new WP_REST_Response(
				array(
					'status'  => 'error',
					'message' => 'Empty color received',
				),
				400
			);
		}
		$the_options                                        = Gdpr_Cookie_Consent::gdpr_get_settings();
		$the_options['auto_generated_background_color']     = $background_color;
		$the_options['button_accept_button_color']          = $the_options['auto_generated_background_color'];
		$the_options['button_accept_button_border_color']   = $the_options['auto_generated_background_color'];
		$the_options['button_decline_link_color']           = $the_options['auto_generated_background_color'];
		$the_options['button_decline_button_border_color']  = $the_options['auto_generated_background_color'];
		$the_options['button_settings_link_color']          = $the_options['auto_generated_background_color'];
		$the_options['button_settings_button_border_color'] = $the_options['auto_generated_background_color'];
		$the_options['button_decline_button_color']         = '#ffffff';
		$the_options['button_settings_button_color']        = '#ffffff';
		$the_options['button_decline_button_border_style']  = 'solid';
		$the_options['button_decline_button_border_width']  = '1';
		$the_options['button_settings_button_border_style'] = 'solid';
		$the_options['button_settings_button_border_width'] = '1';
		// Ab testing values.
		// Banner 1.
		$the_options['button_accept_button_color1']          = $the_options['auto_generated_background_color'];
		$the_options['button_accept_button_border_color1']   = $the_options['auto_generated_background_color'];
		$the_options['button_decline_link_color1']           = $the_options['auto_generated_background_color'];
		$the_options['button_decline_button_border_color1']  = $the_options['auto_generated_background_color'];
		$the_options['button_settings_link_color1']          = $the_options['auto_generated_background_color'];
		$the_options['button_settings_button_border_color1'] = $the_options['auto_generated_background_color'];
		$the_options['button_decline_button_color1']         = '#ffffff';
		$the_options['button_settings_button_color1']        = '#ffffff';
		$the_options['button_decline_button_border_style1']  = 'solid';
		$the_options['button_decline_button_border_width1']  = '1';
		$the_options['button_settings_button_border_style1'] = 'solid';
		$the_options['button_settings_button_border_width1'] = '1';

		// Banner 2.
		$the_options['button_accept_button_color2']          = $the_options['auto_generated_background_color'];
		$the_options['button_accept_button_border_color2']   = $the_options['auto_generated_background_color'];
		$the_options['button_decline_link_color2']           = $the_options['auto_generated_background_color'];
		$the_options['button_decline_button_border_color2']  = $the_options['auto_generated_background_color'];
		$the_options['button_settings_link_color2']          = $the_options['auto_generated_background_color'];
		$the_options['button_settings_button_border_color2'] = $the_options['auto_generated_background_color'];
		$the_options['button_decline_button_color2']         = '#ffffff';
		$the_options['button_settings_button_color2']        = '#ffffff';
		$the_options['button_decline_button_border_style2']  = 'solid';
		$the_options['button_decline_button_border_width2']  = '1';
		$the_options['button_settings_button_border_style2'] = 'solid';
		$the_options['button_settings_button_border_width2'] = '1';

		$the_options['is_banner_auto_generated'] = 'true';

		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );

		update_option( 'gdpr_preview_banner_state', 'true' );

		return new WP_REST_Response(
			array(
				'status'  => 'success',
				'message' => 'Banner Generated Successfully!!!',
				'color'   => $background_color,
			),
			200
		);
	}

	public function gdpr_start_cookie_scan( WP_REST_Request $request ) {
		require_once plugin_dir_path( __DIR__ ) . 'admin/modules/cookie-scanner/classes/class-wpl-cookie-consent-cookie-scanner-ajax.php';
		$cookies_scan = new Gdpr_Cookie_Consent_Cookie_Scanner_Ajax();

		$response = $cookies_scan->gdpr_start_cookie_scanning();

		$out = array(
			'success' => $response['status'] === 'success' ? true : false,
			'data'    => array(
				'status'  => $response['status'],
				'message' => $response['message'],
			),
		);

		if ( isset( $response['error'] ) ) {
			$out['data']['error'] = $response['error'];
		}

		if ( isset( $response['server_response'] ) ) {
			$out['data']['server_response'] = $response['server_response'];
		}

		return new WP_REST_Response(
			$out,
			$response['code']
		);
	}
}