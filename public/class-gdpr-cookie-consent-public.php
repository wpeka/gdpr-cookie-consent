<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://club.wpeka.com
 * @since      1.0
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent_Public {


	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Supported languages.
	 *
	 * @var array
	 */
	private $supported_languages = array( 'fr', 'en', 'nl', 'bg', 'cs', 'da', 'de', 'es', 'hr', 'is', 'sl', 'gr','hu','po' );

	/**
	 * Public module list, Module folder and main file must be same as that of module name.
	 * Please check the `public_modules` method for more details.
	 *
	 * @since 1.0
	 * @access private
	 * @var array $modules Admin module list.
	 */
	private $modules = array();
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
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		if ( ! shortcode_exists( 'wpl_cookie_details' ) ) {
			add_shortcode( 'wpl_cookie_details', array( $this, 'gdprcookieconsent_shortcode_cookie_details' ) );         // a shortcode [wpl_cookie_details].
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gdpr-cookie-consent-public' . GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name . '-custom', plugin_dir_url( __FILE__ ) . 'css/gdpr-cookie-consent-public-custom' . GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
		wp_register_script( $this->plugin_name . '-bootstrap-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap/bootstrap.bundle.js', array( 'jquery' ), $this->version, true );
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gdpr-cookie-consent-public' . GDPR_CC_SUFFIX . '.js#async', array( 'jquery' ), $this->version, true );
	}

	/**
	 * Returns JSON object containing the settings for the main script.
	 *
	 * @param Array $slim_settings Slim settings.
	 * @return mixed
	 */
	public function wplcookieconsent_json_settings( $slim_settings ) {
		$slim_settings['maxmind_integrated'] = get_option( 'wpl_pro_maxmind_integrated', '1' );
		return $slim_settings;
	}
	/**
	 * Returns updated array with the consent renew values
	 *
	 * @since 2.11.1
	 */
	public function gdpr_renew_consent_bar() {
		check_ajax_referer( 'wpl_consent_renew_nonce', 'security' );

		global $wpdb;

		if ( ! empty( $_POST['arrayValue'] ) ) {

			$returned_array_from_public_js = $_POST['arrayValue'];

			foreach ( $returned_array_from_public_js as $data ) {
				$post_id       = $data['post_id'];
				$consent_value = $data['consent_value'];

				// Check if the post ID exists in the database.
				$post_exists = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT ID FROM {$wpdb->prefix}posts WHERE ID = %d",
						$post_id
					)
				);

				if ( $post_exists ) {
					// Update '_wplconsentlogs_ip' and '_wpl_renew_consent' meta values.
					update_post_meta( $post_id, '_wplconsentlogs_ip', $data['ip_value'] );

					// Check if '_wpl_renew_consent' meta key exists, and add if it doesn't.
					$existing_renew_consent = get_post_meta( $post_id, '_wpl_renew_consent', true );
					if ( $existing_renew_consent == '' ) {
						add_post_meta( $post_id, '_wpl_renew_consent', $consent_value, true );
					} else {
						// Update '_wpl_renew_consent' meta value if it exists.
						update_post_meta( $post_id, '_wpl_renew_consent', $consent_value );
					}
				}
			}

			wp_send_json_success( $returned_array_from_public_js );

		}
	}
	/**
	 * Returns cookie consent bar status.
	 *
	 * @since 2.0
	 */
	public function show_cookie_consent_bar() {
		update_option( 'gdpr_settings_enabled', 0 );
		$return_array = array(
			'eu_status'   => 'on',
			'ccpa_status' => 'on',
		);
		$the_options  = Gdpr_Cookie_Consent::gdpr_get_settings();
		$geo_options  = get_option( 'wpl_geo_options' );
		if ( '2' === get_option( 'wpl_pro_maxmind_integrated' ) && isset( $geo_options['enable_geotargeting'] ) && 'true' === $geo_options['enable_geotargeting'] ) {
			if ( boolval( true ) === boolval( $the_options['is_eu_on'] ) ) {
				// check if eu country.
				$geoip      = new Gdpr_Cookie_Consent_Geo_Ip();
				$eu_country = $geoip->wpl_is_eu_country();
				if ( isset( $eu_country ) && true !== $eu_country ) {
					$return_array['eu_status'] = 'off';
				}
			}
			if ( boolval( true ) === boolval( $the_options['is_ccpa_on'] ) ) {
				// check if ccpa country.
				$geoip        = new Gdpr_Cookie_Consent_Geo_Ip();
				$ccpa_country = $geoip->wpl_is_ccpa_country();
				if ( isset( $ccpa_country ) && true !== $ccpa_country ) {
					$return_array['ccpa_status'] = 'off';
				}
			}
			if ( 'gdpr' === $the_options['cookie_usage_for'] ) {
				$return_array['ccpa_status'] = 'off';
			}
			if ( 'ccpa' === $the_options['cookie_usage_for'] ) {
				$return_array['eu_status'] = 'off';
			}
			if ( 'eprivacy' === $the_options['cookie_usage_for'] ) {
				$return_array['eu_status'] = 'on';
			}
		}
		wp_send_json( $return_array );
		wp_die();
	}
	/**
	 * Register public modules
	 *
	 * @since 1.0
	 */
	public function public_modules() {
		$initialize_flag     = false;
		$active_flag         = false;
		$non_active_flag     = false;
		$gdpr_public_modules = get_option( 'gdpr_public_modules' );
		if ( false === $gdpr_public_modules ) {
			$gdpr_public_modules = array();
			$initialize_flag     = true;
		}
		foreach ( $this->modules as $module ) {
			$is_active = 1;
			if ( isset( $gdpr_public_modules[ $module ] ) ) {
				$is_active = $gdpr_public_modules[ $module ]; // checking module status.
				if ( 1 === $is_active ) {
					$active_flag = true;
				}
			} else {
				$active_flag                    = true;
				$gdpr_public_modules[ $module ] = 1; // default status is active.
			}
			$module_file = plugin_dir_path( __FILE__ ) . "modules/$module/class-gdpr-cookie-consent-$module.php";
			if ( file_exists( $module_file ) && 1 === $is_active ) {
				self::$existing_modules[] = $module; // this is for module_exits checking.
				require_once $module_file;
			} else {
				$non_active_flag                = true;
				$gdpr_public_modules[ $module ] = 0;
			}
		}
		if ( $initialize_flag || ( $active_flag && $non_active_flag ) ) {
			$out = array();
			foreach ( $gdpr_public_modules as $k => $m ) {
				if ( in_array( $k, $this->modules, true ) ) {
					$out[ $k ] = $m;
				}
			}
			update_option( 'gdpr_public_modules', $out );
		}
	}

	/**
	 * Removes leading # characters from a string.
	 *
	 * @since 1.0
	 * @param string $str String from hash to be removed.
	 *
	 * @return bool|string
	 */
	public static function gdprcookieconsent_remove_hash( $str ) {
		if ( '#' === $str[0] ) {
			$str = substr( $str, 1, strlen( $str ) );
		} else {
			return $str;
		}
		return self::gdprcookieconsent_remove_hash( $str );
	}

	/**
	 * Parse enqueue url for async parameter.
	 *
	 * @since 1.8.5
	 * @param string $url URL.
	 * @return mixed|string
	 */
	public function gdprcookieconsent_clean_async_url( $url ) {
		if ( strpos( $url, '#async' ) === false ) {
			return $url;
		} elseif ( is_admin() ) {
			return str_replace( '#async', '', $url );
		} else {
			return str_replace( '#async', '', $url ) . "' async='async";
		}
	}
	/**
	 * Translator function to convert the public facing side texts
	 *
	 * @param string $text Text .
	 * @param array  $translations Translation.
	 * @param string $target_language Target Language.
	 */
	public function translate_text( $text, $translations, $target_language ) {
		// Assuming $text is the key for the translation in the JSON file.
		if ( isset( $translations[ $text ][ $target_language ] ) ) {
			return $translations[ $text ][ $target_language ];
		} else {
			// Return the original text if no translation is found.
			return $text;
		}
	}
	/**
	 * Registered rest end point to get the current banner options form database.
	 */
	public function gdpr_cookie_data_endpoint() {
		register_rest_route(
			'custom/v1',
			'/gdpr-data/',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'gdpr_get_settings_new' ),
			)
		);
	}

	/**
	 * Fetch Settings from database.
	 *
	 *  @param array $data Data.
	 */
	public function gdpr_get_settings_new( $data ) {
		// Your logic to get GDPR settings.
		$gdpr_data = get_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );

		// Return the data.
		return rest_ensure_response( $gdpr_data );
	}

	/**
	 * Outputs the cookie control script in the footer.
	 * This function should be attached to the wp_footer action hook.
	 *
	 * @since 1.0
	 */
	public function gdprcookieconsent_inject_gdpr_script() {
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		if ( true === $the_options['is_on'] ) {
			if ( 'ccpa' === $the_options['cookie_usage_for'] || 'both' === $the_options['cookie_usage_for'] ) {
				wp_enqueue_script( $this->plugin_name . '-uspapi', plugin_dir_url( __FILE__ ) . 'js/iab/uspapi.js', array( 'jquery' ), $this->version, false );
			}
			wp_enqueue_style( $this->plugin_name );
			wp_enqueue_style( $this->plugin_name . '-custom' );
			wp_enqueue_script( $this->plugin_name . '-bootstrap-js' );
			wp_enqueue_script( $this->plugin_name );
			wp_localize_script(
				$this->plugin_name,
				'log_obj',
				array(
					'ajax_url'              => admin_url( 'admin-ajax.php' ),
					'consent_logging_nonce' => wp_create_nonce( 'wpl_consent_logging_nonce' ),
					'consent_renew_nonce'   => wp_create_nonce( 'wpl_consent_renew_nonce' ),
				)
			);
			add_filter( 'clean_url', array( $this, 'gdprcookieconsent_clean_async_url' ) );
			$timber           = new Timber\Timber();
			$gdpr_message     = '';
			$ccpa_message     = '';
			$lgpd_message     = '';
			$eprivacy_message = '';
			// Output the HTML in the footer.
			if ( 'eprivacy' === $the_options['cookie_usage_for'] ) {
				$eprivacy_message               = nl2br( $the_options['notify_message_eprivacy'] );
				$the_options['eprivacy_notify'] = true;
			}
			if ( 'gdpr' === $the_options['cookie_usage_for'] ) {
				$gdpr_message               = nl2br( $the_options['notify_message'] );
				$the_options['gdpr_notify'] = true;
			}
			if ( 'ccpa' === $the_options['cookie_usage_for'] ) {
				$ccpa_message                  = nl2br( $the_options['notify_message_ccpa'] );
				$the_options['ccpa_notify']    = true;
				$the_options['optout_text']    = nl2br( $the_options['optout_text'] );
				$the_options['confirm_button'] = __( 'Confirm', 'gdpr-cookie-consent' );
				$the_options['cancel_button']  = __( 'Cancel', 'gdpr-cookie-consent' );
			}
			if ( 'lgpd' === $the_options['cookie_usage_for'] ) {
				$lgpd_message               = nl2br( $the_options['notify_message_lgpd'] );
				$the_options['lgpd_notify'] = true;
			}
			if ( 'both' === $the_options['cookie_usage_for'] ) {
				$gdpr_message                  = nl2br( $the_options['notify_message'] );
				$ccpa_message                  = nl2br( $the_options['notify_message_ccpa'] );
				$the_options['gdpr_notify']    = true;
				$the_options['ccpa_notify']    = true;
				$the_options['optout_text']    = nl2br( $the_options['optout_text'] );
				$the_options['confirm_button'] = __( 'Confirm', 'gdpr-cookie-consent' );
				$the_options['cancel_button']  = __( 'Cancel', 'gdpr-cookie-consent' );
			}
			$about_message      = stripslashes( nl2br( $the_options['about_message'] ) );
			$about_message_lgpd = stripslashes( nl2br( $the_options['about_message_lgpd'] ) );
			$eprivacy_message   = stripslashes( $eprivacy_message );
			$gdpr_message       = stripslashes( $gdpr_message );
			$ccpa_message       = stripslashes( $ccpa_message );
			$lgpd_message       = stripslashes( $lgpd_message );
			$eprivacy_str       = $eprivacy_message;
			$gdpr_str           = $gdpr_message;
			$ccpa_str           = $ccpa_message;
			$lgpd_str           = $lgpd_message;
			$head               = $the_options['bar_heading_text'];
			$head               = trim( stripslashes( $head ) );
			$head_lgpd          = $the_options['bar_heading_lgpd_text'];
			$head_lgpd          = trim( stripslashes( $head_lgpd ) );
			$template           = $the_options['template'];
			if ( 'none' !== $template ) {
				$template_parts = explode( '-', $template );
				$template       = array_pop( $template_parts );
			}
			$the_options['template_parts'] = $template;
			if ( in_array( $template, array( 'navy_blue_center', 'navy_blue_box', 'navy_blue_square' ), true ) ) {
				$template_parts_background = '#354e8e';
			} elseif ( in_array( $template, array( 'almond_column' ), true ) ) {
				$template_parts_background = '#f2ecd8';
			} elseif ( in_array( $template, array( 'grey_column', 'grey_center' ), true ) ) {
				$template_parts_background = '#e0e0e0';
			} elseif ( in_array( $template, array( 'dark' ), true ) ) {
				$template_parts_background = '#3a3a3a';
			} elseif ( in_array( $template, array( 'dark_row' ), true ) ) {
				$template_parts_background = '#434a58';
			} else {
				$template_parts_background = '#ebebeb';
			}
			wp_localize_script( $this->plugin_name, 'background_obj', array( 'background' => $template_parts_background ) );

			if ( false !== strpos( $template, 'center' ) ) {
				$template = 'center';
			} elseif ( false !== strpos( $template, 'box' ) ) {
				$template = 'box';
			} elseif ( false !== strpos( $template, 'square' ) ) {
				$template = 'square';
			} elseif ( false !== strpos( $template, 'row' ) ) {
				$template = 'row';
			} elseif ( false !== strpos( $template, 'column' ) ) {
				$template = 'column';
			} else {
				$template = 'default';
			}
			$the_options['skin_template']        = 'skins/' . $template . '.php';
			$the_options['container_class']      = $the_options['cookie_usage_for'] . ' gdpr-' . $the_options['cookie_bar_as'] . ' gdpr-' . $template . ' ' . $the_options['template'];
			$layout                              = $the_options['button_settings_layout_skin'];
			$layout_parts                        = explode( '-', $layout );
			$layout_skin                         = array_pop( $layout_parts );
			$the_options['container_class']     .= ' layout-' . $layout_skin;
			$the_options['layout_skin_template'] = 'modals/' . $layout_skin . '.php';

			$current_theme = wp_get_theme();
			if ( isset( $current_theme->template ) ) {
				$the_options['theme_class'] = 'theme-' . $current_theme->template;
			}

			$the_options['eprivacy_str']              = $eprivacy_str;
			$the_options['gdpr_str']                  = $gdpr_str;
			$the_options['ccpa_str']                  = $ccpa_str;
			$the_options['lgpd_str']                  = $lgpd_str;
			$the_options['head']                      = $head;
			$the_options['head_lgpd']                 = $head_lgpd;
			$the_options['version']                   = $this->version;
			$the_options['show_again_container_id']   = $this->gdprcookieconsent_remove_hash( $the_options['show_again_div_id'] );
			$the_options['container_id']              = $this->gdprcookieconsent_remove_hash( $the_options['notify_div_id'] );
			$the_options['button_accept_action_id']   = $this->gdprcookieconsent_remove_hash( $the_options['button_accept_action'] );
			$the_options['button_readmore_action_id'] = $this->gdprcookieconsent_remove_hash( $the_options['button_readmore_action'] );
			$the_options['button_decline_action_id']  = $this->gdprcookieconsent_remove_hash( $the_options['button_decline_action'] );
			$the_options['button_settings_action_id'] = $this->gdprcookieconsent_remove_hash( $the_options['button_settings_action'] );

			$the_options['backdrop'] = $the_options['popup_overlay'] ? 'static' : 'false';

			$wpl_pro_active = get_option( 'wpl_pro_active' );
			if ( $wpl_pro_active ) {
				$credit_link_href = 'https://club.wpeka.com/product/wp-gdpr-cookie-consent/?utm_source=gdpr&utm_medium=show-credits&utm_campaign=link&utm_content=powered-by-gdpr';
			} else {
				$credit_link_href = 'https://wordpress.org/plugins/gdpr-cookie-consent/?utm_source=gdpr&utm_medium=show-credits&utm_campaign=link&utm_content=powered-by-gdpr';
			}
			$credit_link_text = __( 'WP Cookie consent', 'gdpr-cookie-consent' );

			$credit_link = sprintf(
				/* translators: 1: GDPR Cookie Consent Plugin*/
				__( 'Powered by %s', 'gdpr-cookie-consent' ),
				'<a href="' . esc_url( $credit_link_href ) . '" id="cookie_credit_link" rel="nofollow noopener" target="_blank">' . $credit_link_text . '</a>'
			);

			$button_readmore_url_link = '';
			if ( true === $the_options['button_readmore_url_type'] ) {
				if ( true === $the_options['button_readmore_wp_page'] ) {
					$button_readmore_url_link = get_privacy_policy_url();
				}
				if ( empty( $button_readmore_url_link ) ) {
					if ( '0' !== $the_options['button_readmore_page'] ) {
						$button_readmore_url_link = get_page_link( $the_options['button_readmore_page'] );
					} else {
						$button_readmore_url_link = '#';
					}
				}
			} else {
				$button_readmore_url_link = $the_options['button_readmore_url'];
			}
			$the_options['button_readmore_url_link'] = $button_readmore_url_link;

			$the_options['button_accept_all_classes'] = 'gdpr_action_button ';
			if ( $the_options['button_accept_all_as_button'] ) {
				switch ( $the_options['button_accept_all_button_size'] ) {
					case 'medium':
						$the_options['button_accept_all_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_accept_all_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_accept_all_classes'] .= 'btn btn-sm';
						break;
				}
			}
			$the_options['button_accept_classes'] = 'gdpr_action_button ';
			if ( $the_options['button_accept_as_button'] ) {
				switch ( $the_options['button_accept_button_size'] ) {
					case 'medium':
						$the_options['button_accept_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_accept_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_accept_classes'] .= 'btn btn-sm';
						break;
				}
			}
			$the_options['button_readmore_classes'] = '';
			if ( $the_options['button_readmore_as_button'] ) {
				$the_options['button_readmore_classes'] .= 'gdpr_action_button_link ';
				switch ( $the_options['button_readmore_button_size'] ) {
					case 'medium':
						$the_options['button_readmore_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_readmore_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_readmore_classes'] .= 'btn btn-sm';
						break;
				}
			} else {
				$the_options['button_readmore_classes'] = 'gdpr_link_button';
			}
			$the_options['button_decline_classes'] = 'gdpr_action_button ';
			if ( $the_options['button_decline_as_button'] ) {
				switch ( $the_options['button_decline_button_size'] ) {
					case 'medium':
						$the_options['button_decline_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_decline_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_decline_classes'] .= 'btn btn-sm';
						break;
				}
			}
			$the_options['button_settings_classes'] = 'gdpr_action_button ';
			if ( $the_options['button_settings_as_button'] ) {
				switch ( $the_options['button_settings_button_size'] ) {
					case 'medium':
						$the_options['button_settings_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_settings_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_settings_classes'] .= 'btn btn-sm';
						break;
				}
			}
			$the_options['button_donotsell_classes'] = 'gdpr_action_button gdpr_link_button';
			$the_options['button_confirm_classes']   = 'gdpr_action_button ';
			if ( $the_options['button_accept_as_button'] ) {
				switch ( $the_options['button_confirm_button_size'] ) {
					case 'medium':
						$the_options['button_confirm_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_confirm_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_confirm_classes'] .= 'btn btn-sm';
						break;
				}
			}
			$the_options['button_cancel_classes'] = 'gdpr_action_button ';
			if ( $the_options['button_cancel_as_button'] ) {
				switch ( $the_options['button_cancel_button_size'] ) {
					case 'medium':
						$the_options['button_cancel_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_cancel_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_cancel_classes'] .= 'btn btn-sm';
						break;
				}
			}
			$categories      = Gdpr_Cookie_Consent_Cookie_Custom::get_categories( true );
			$cookies         = $this->get_cookies();
			$categories_data = array();
			// The array returned by json_decode is being sanitised by function gdpr_cookie_consent_sanitize_decoded_function.
			$preference_cookies = isset( $_COOKIE['wpl_user_preference'] ) ? json_decode( stripslashes( wp_unslash( $_COOKIE['wpl_user_preference'] ) ), true ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( '' !== $preference_cookies ) {
				$preference_cookies = $this->gdpr_cookie_consent_sanitize_decoded_json( $preference_cookies );
			}
			$viewed_cookie                = isset( $_COOKIE['wpl_viewed_cookie'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['wpl_viewed_cookie'] ) ) : '';
			$the_options['viewed_cookie'] = $viewed_cookie;
			foreach ( $categories as $category ) {
				$total     = 0;
				$temp      = array();
				$json_temp = array();
				foreach ( $cookies as $cookie ) {
					if ( $cookie['category_id'] === $category['id_gdpr_cookie_category'] ) {
						++$total;
						$temp[]                = $cookie;
						$cookie['description'] = str_replace( '"', '\"', $cookie['description'] );
						$json_temp[]           = $cookie;
					}
				}
				$category['data']  = $temp;
				$category['total'] = $total;
				if ( isset( $preference_cookies[ $category['gdpr_cookie_category_slug'] ] ) && 'yes' === $preference_cookies[ $category['gdpr_cookie_category_slug'] ] ) {
					$category['is_ticked'] = true;
				} else {
					$category['is_ticked'] = false;
				}
				$categories_data[]      = $category;
				$category['data']       = $json_temp;
				$categories_json_data[] = $category;
			}

			if ( true === $the_options['button_settings_is_on'] || true === $the_options['button_accept_all_is_on'] || true === $the_options['button_accept_is_on'] ) {
				$cookie_data                      = array();
				$cookie_data['categories']        = $categories_data;
				$cookie_data['msg']               = $about_message;
				$cookie_data['lgpd']              = $about_message_lgpd;
				$cookie_data['show_credits']      = $the_options['show_credits'];
				$cookie_data['credits']           = $the_options['show_credits'] ? $credit_link : '';
				$cookie_data['backdrop']          = $the_options['backdrop'];
				$cookie_data['about']             = __( 'About Cookies', 'gdpr-cookie-consent' );
				$cookie_data['declaration']       = __( 'Cookie Declaration', 'gdpr-cookie-consent' );
				$cookie_data['always']            = __( 'Always Active', 'gdpr-cookie-consent' );
				$cookie_data['save_button']       = __( 'Save And Accept', 'gdpr-cookie-consent' );
				$cookie_data['name']              = __( 'Name', 'gdpr-cookie-consent' );
				$cookie_data['domain']            = __( 'Domain', 'gdpr-cookie-consent' );
				$cookie_data['purpose']           = __( 'Purpose', 'gdpr-cookie-consent' );
				$cookie_data['expiry']            = __( 'Expiry', 'gdpr-cookie-consent' );
				$cookie_data['type']              = __( 'Type', 'gdpr-cookie-consent' );
				$cookie_data['cookies_not_found'] = __( 'We do not use cookies of this type.', 'gdpr-cookie-consent' );
				$cookie_data['consent_notice']    = __( 'I consent to the use of following cookies:', 'gdpr-cookie-consent' );
				$the_options['cookie_data']       = $cookie_data;

				// language translation based on the selected language for the public facing.
				if ( isset( $the_options['lang_selected'] ) && in_array( $the_options['lang_selected'], $this->supported_languages ) ) {

					// Load and decode translations from JSON file.
					$translations_file = plugin_dir_path( __FILE__ ) . 'translations/public-translations.json';
					$translations      = json_decode( file_get_contents( $translations_file ), true );

					// Define an array of text keys to translate.
					$text_keys_to_translate = array(
						'about',
						'declaration',
						'always',
						'save_button',
						'name',
						'domain',
						'purpose',
						'expiry',
						'type',
						'cookies_not_found',
						'consent_notice',
					);

					// Determine the target language based on the POST value.
					$target_language = $the_options['lang_selected'];

					// Loop through the text keys and translate them.
					foreach ( $text_keys_to_translate as $text_key ) {
						$translated_text = $this->translate_text( $text_key, $translations, $target_language );

						$cookie_data[ $text_key ] = $translated_text;
					}

					$the_options['cookie_data'] = $cookie_data;
				}
			}

			$the_options['credits'] = $the_options['show_credits'] ? $credit_link : '';
			include plugin_dir_path( __FILE__ ) . 'templates/default.php';
			?>
			<style>
				.gdpr_messagebar_detail .category-group .category-item .description-container .group-toggle .checkbox input:checked+label:after,
				.gdpr_messagebar_detail.layout-classic .category-group .toggle-group .checkbox input:checked+label:after {
					background: <?php echo esc_attr( $the_options['button_accept_button_color'] ); ?> !important;
				}

				.gdpr_messagebar_detail .gdprmodal-dialog .gdprmodal-header .close,
				#gdpr-ccpa-gdprmodal .gdprmodal-dialog .gdprmodal-body .close {
					color: <?php echo esc_attr( $the_options['button_accept_button_color'] ); ?> !important;
				}
			</style>
			<?php

			// fetching the values of post id, ip and consent and mapping them to a array.

			global $wpdb;

			$meta_key_cl_ip            = '_wplconsentlogs_ip';
			$meta_key_cl_renew_consent = '_wpl_renew_consent';
			$trash_meta_key            = '_wp_trash_meta_status';
			$trash_meta_value          = 'publish';

			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT pm1.post_id, pm1.meta_value AS ip_value, pm2.meta_value AS consent_value
					 FROM {$wpdb->prefix}postmeta AS pm1
					 LEFT JOIN {$wpdb->prefix}postmeta AS pm2 ON pm1.post_id = pm2.post_id
					 WHERE pm1.meta_key = %s
					 AND pm2.meta_key = %s
					 AND pm1.post_id NOT IN (
						 SELECT post_id
						 FROM {$wpdb->prefix}postmeta
						 WHERE meta_key = %s AND meta_value = %s
					 )",
					$meta_key_cl_ip,
					$meta_key_cl_renew_consent,
					$trash_meta_key,
					$trash_meta_value
				)
			);

			$gdpr_post_meta_values_array = array();

			foreach ( $results as $result ) {
				$gdpr_post_meta_values_array[] = array(
					'post_id'       => $result->post_id,
					'ip_value'      => $result->ip_value,
					'consent_value' => $result->consent_value,
				);
			}

			$the_options['ip_and_consent_renew'] = $gdpr_post_meta_values_array;

			$user_ip = $this->wpl_get_user_ip(); // get the current user's IP.

			// make null if consent forward in of.
			$currentid                     = get_current_blog_id();
			$the_options['select_sites']   = is_array( $the_options['select_sites'] ) ? $the_options['select_sites'] : array();
			$the_options['select_sites'][] = $currentid;

			if ( $the_options['consent_forward'] !== true ) {
				$the_options['select_sites'] = null;
			}
			$cookies_list_data = array(
				'gdpr_cookies_list'       => str_replace( "'", "\'", wp_json_encode( $categories_json_data ) ),
				'gdpr_cookiebar_settings' => wp_json_encode( Gdpr_Cookie_Consent::gdpr_get_json_settings() ),
				'gdpr_consent_renew'      => $the_options['ip_and_consent_renew'],
				'gdpr_user_ip'            => $user_ip,
				'gdpr_do_not_track'       => $the_options['do_not_track_on'],
				'gdpr_select_pages'       => $the_options['select_pages'],
				'gdpr_select_sites'       => $the_options['select_sites'],
				'consent_forwarding'      => $the_options['consent_forward'],
			);

			wp_localize_script( $this->plugin_name, 'gdpr_cookies_obj', $cookies_list_data );
		}
	}
	/**
	 * Returns IP address of the user for consent log.
	 *
	 * @since 1.1
	 * @return string
	 *
	 * @phpcs:disable
	 */
	public function wpl_get_user_ip()
	{
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		} elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		} elseif (isset($_SERVER['HTTP_FORWARDED'])) {
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		} else {
			$ipaddress = 'UNKNOWN';
		}
		return $ipaddress;
	}

	/**
	 * Returns sanitised array.
	 *
	 * @since 2.1.2
	 * @param array $input_array The input array to sanitize.
	 * @return array
	 */
	public function gdpr_cookie_consent_sanitize_decoded_json($input_array = array())
	{
		// Initialize the new array that will hold the sanitize values.
		$return_array = array();

		// Loop through the input and sanitize each of the values.
		foreach ($input_array as $key => $val) {
			$return_array[$key] = sanitize_text_field($val);
		}

		return $return_array;
	}

	/**
	 * Returns scanned and custom cookies.
	 *
	 * @since 1.0
	 * @return array
	 */
	public function get_cookies()
	{
		$cookies_array = array();
		$cookie_custom = new Gdpr_Cookie_Consent_Cookie_Custom();
		$cookies_array = $cookie_custom->get_cookies();
		$cookies_array = apply_filters('gdprcookieconsent_cookies', $cookies_array);
		return $cookies_array;
	}

	/**
	 * Returns policy data for shortcode wpl_cookie_details.
	 *
	 * @return string|void
	 */
	public function gdprcookieconsent_shortcode_cookie_details()
	{
		if (is_admin()) {
			return;
		}
		$args                = array(
			'numberposts' => -1,
			'post_type'   => 'gdprpolicies',
		);
		$wp_legalpolicy_data = get_posts($args);
		$content             = '';
		if (is_array($wp_legalpolicy_data) && !empty($wp_legalpolicy_data)) {
			$content .= '<p>For further information on how we use cookies, please refer to the table below.</p>';
			$content .= "<div class='wp_legalpolicy' style='overflow-x:scroll;overflow:auto;'>";
			$content .= '<table style="width:100%;margin:0 auto;border-collapse:collapse;">';
			$content .= '<thead>';
			$content .= '<th>Third Party Companies</th><th>Purpose</th><th>Applicable Privacy/Cookie Policy Link</th>';
			$content .= '</thead>';
			$content .= '<tbody>';
			foreach ($wp_legalpolicy_data as $policypost) {
				$content .= '<tr>';
				$content .= '<td>' . $policypost->post_title . '</td>';
				$content .= '<td>' . $policypost->post_content . '</td>';
				$links    = get_post_meta($policypost->ID, '_gdpr_policies_links_editor');
				$content .= '<td>' . $links[0] . '</td>';
				$content .= '</tr>';
			}
			$content .= '</tbody></table></div>';
		}
		return $content;
	}

	/**
	 * Template redirect for header, body and footer scripts.
	 *
	 * @since 1.9.0
	 */
	public function gdprcookieconsent_template_redirect()
	{
		global $post;

		if (is_admin() || defined('DOING_AJAX') || defined('DOING_CRON')) {
			return;
		}

		$viewed_cookie = isset($_COOKIE['wpl_viewed_cookie']) ? sanitize_text_field(wp_unslash($_COOKIE['wpl_viewed_cookie'])) : '';
		$the_options   = GDPR_Cookie_Consent::gdpr_get_settings();

		$body_open_supported = function_exists('wp_body_open') && version_compare(get_bloginfo('version'), '5.2', '>=');

		$disable_blocker = get_option('wpl_bypass_script_blocker');

		if ((is_singular() && $post) || is_home()) {
			if (($the_options['is_script_blocker_on'] && 'yes' === $viewed_cookie) || (!$the_options['is_script_blocker_on']) || $disable_blocker) {
				add_action('wp_head', array($this, 'gdprcookieconsent_output_header'));
				if ($body_open_supported) {
					add_action('wp_body_open', array($this, 'gdprcookieconsent_output_body'));
				}
				add_action('wp_footer', array($this, 'gdprcookieconsent_output_footer'));
			}
		}
	}

	/**
	 * Output header scripts.
	 *
	 * @since 1.9.0
	 */
	public function gdprcookieconsent_output_header()
	{
		$the_options    = GDPR_Cookie_Consent::gdpr_get_settings();
		$header_scripts = $the_options['header_scripts'];
		if ($header_scripts) {
			// After referring to the competitor WordPress.org plugins, we are following the same approach.
			echo "\r\n" . wp_unslash($header_scripts) . "\r\n";
		}
	}

	/**
	 * Output body scripts.
	 *
	 * @since 1.9.0
	 */
	public function gdprcookieconsent_output_body()
	{
		$the_options  = GDPR_Cookie_Consent::gdpr_get_settings();
		$body_scripts = $the_options['body_scripts'];
		if ($body_scripts) {
			// After referring to the competitor WordPress.org plugins, we are following the same approach.
			echo "\r\n" . wp_unslash($body_scripts) . "\r\n";
		}
	}

	/**
	 * Output footer scripts.
	 *
	 * @since 1.9.0
	 */
	public function gdprcookieconsent_output_footer()
	{
		$the_options    = GDPR_Cookie_Consent::gdpr_get_settings();
		$footer_scripts = $the_options['footer_scripts'];
		if ($footer_scripts) {
			// After referring to the competitor WordPress.org plugins, we are following the same approach.
			echo "\r\n" . wp_unslash($footer_scripts) . "\r\n";
		}
	}
}
