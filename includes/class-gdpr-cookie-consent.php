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
	public function __construct() {
		if ( defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ) {
			$this->version = GDPR_COOKIE_CONSENT_VERSION;
		} else {
			$this->version = '1.5';
		}
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
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/class-gdpr-cookie-consent-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/class-gdpr-cookie-consent-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'admin/class-gdpr-cookie-consent-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'public/class-gdpr-cookie-consent-public.php';

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
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Gdpr_Cookie_Consent_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu', 5 ); /* Adding admin menu */
		$this->loader->add_action( 'current_screen', $plugin_admin, 'add_tabs', 15 );
		$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'admin_footer_text', 10, 1 );
		/**
		 * Load admin modules
		 */
		$plugin_admin->admin_modules();
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

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
		 * Load admin modules
		 */
		$plugin_public->public_modules();
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'gdprcookieconsent_inject_gdpr_script' );

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
	 * @param int    $target_id Target ID.
	 * @param string $view_file View template file.
	 * @param string $html Html content.
	 * @param array  $variables Variables.
	 * @param int    $need_submit_btn Need submit button flag.
	 * @param string $error_message Error message.
	 */
	public static function gdpr_envelope_settings_tabcontent( $target_id, $view_file = '', $html = '', $variables = array(), $need_submit_btn = 0, $error_message = '' ) {
		$post_cookie_list = array();
		if ( isset( $variables['post_cookie_list'] ) ) {
			$post_cookie_list = $variables['post_cookie_list'];
		}
		$the_options = self::gdpr_get_settings();
		?>
		<div class="gdpr-cookie-consent-tab-content" data-id="<?php echo esc_attr( $target_id ); ?>">
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
			// Allowed:		<a href="" id="" class="" title="" target="">...</a>.
			// Not allowed:	<a href="javascript(...);">...</a>.
			'a'      => array(
				'href'   => array(),
				'id'     => array(),
				'class'  => array(),
				'title'  => array(),
				'target' => array(),
				'rel'    => array(),
				'style'  => array(),
				'data'   => array(),
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
			'animate_speed_hide'       => '500',
			'animate_speed_show'       => '500',
			'background'               => '#FFF',
			'button_1_text'            => 'Accept All Cookies',
			'button_1_selected_text'   => 'Accept Selected Cookies',
			'button_1_url'             => '#',
			'button_1_action'          => '#cookie_action_close_header',
			'button_1_link_color'      => '#fff',
			'button_1_button_color'    => '#18a300',
			'button_1_new_win'         => false,
			'button_1_as_button'       => true,
			'button_1_button_size'     => 'medium',
			'button_2_text'            => 'Read More',
			'button_2_url'             => '#',
			'button_2_action'          => 'CONSTANT_OPEN_URL',
			'button_2_link_color'      => '#359bf5',
			'button_2_button_color'    => '#333',
			'button_2_new_win'         => false,
			'button_2_as_button'       => false,
			'button_2_button_size'     => 'medium',
			'button_3_text'            => 'Decline',
			'button_3_url'             => '#',
			'button_3_action'          => '#cookie_action_close_header_reject',
			'button_3_link_color'      => '#fff',
			'button_3_button_color'    => '#333',
			'button_3_new_win'         => false,
			'button_3_as_button'       => true,
			'button_3_button_size'     => 'medium',
			'font_family'              => 'inherit', // Pick the family, not the easy name (see helper function below).
			'is_on'                    => true,
			'is_ticked'                => false,
			'is_eu_on'                 => false,
			'logging_on'               => false,
			'show_credits'             => false,
			'notify_animate_hide'      => true,
			'notify_animate_show'      => false,
			'notify_message'           => addslashes( 'This website uses cookies to improve your experience. We\'ll assume you\'re ok with this, but you can opt-out if you wish.<br /><br />[wpl_cookie_button margin="5px"][wpl_cookie_decline margin="5px"][wpl_cookie_settings margin="5px"]' ),
			'notify_div_id'            => '#gdpr-cookie-consent-bar',
			'notify_position_vertical' => 'bottom', // 'top' = header | 'bottom' = footer
			'text'                     => '#000',
			'use_color_picker'         => true,
			'bar_heading_text'         => '',
			'cookie_bar_as'            => 'banner',
			'about_message'            => addslashes( ( 'Cookies are small text files that can be used by websites to make a user\'s experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.' ) ),
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
			case 'is_ticked':
			case 'is_eu_on':
			case 'logging_on':
			case 'show_credits':
			case 'notify_animate_hide':
			case 'use_color_picker':
			case 'button_1_new_win':
			case 'button_1_as_button':
			case 'button_2_new_win':
			case 'button_2_as_button':
			case 'button_3_new_win':
			case 'button_3_as_button':
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
			case 'text':
			case 'button_1_link_color':
			case 'button_1_button_color':
			case 'button_2_link_color':
			case 'button_2_button_color':
			case 'button_3_link_color':
			case 'button_3_button_color':
				if ( preg_match( '/^#[a-f0-9]{6}|#[a-f0-9]{3}$/i', $value ) ) {
					// Was: '/^#([0-9a-fA-F]{1,2}){3}$/i' which allowed e.g. '#00dd' (error).
					$ret = $value;
				} else {
					// Failover = assign '#000' (black).
					$ret = '#000';
				}
				break;
			// Allow some HTML, but no JavaScript. Note that deliberately NOT stripping out line breaks here, that's done when sending JavaScript parameter elsewhere.
			case 'about_message':
			case 'notify_message':
			case 'bar_heading_text':
				$ret = wp_kses( $value, self::gdpr_allowed_html(), self::gdpr_allowed_protocols() );
				break;
			// URLs only.
			case 'button_1_url':
			case 'button_2_url':
			case 'button_3_url':
				$ret = esc_url( $value );
				break;
			// Basic sanitisation for all the rest.
			default:
				$ret = sanitize_text_field( $value );
				break;
		}
		if ( ( 'is_eu_on' === $key || 'logging_on' === $key ) && 'fffffff' === $ret ) {
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
	 * Color shift a hex value by a specific percentage factor.
	 *
	 * @since 1.0
	 * @param string  $supplied_hex Any valid hex value. Short forms e.g. #333 accepted.
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
		$rgb_values['R'] = hexdec( $supplied_hex{0} . $supplied_hex{1} );
		$rgb_values['G'] = hexdec( $supplied_hex{2} . $supplied_hex{3} );
		$rgb_values['B'] = hexdec( $supplied_hex{4} . $supplied_hex{5} );

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
		$slim_settings = array(
			'animate_speed_hide'       => $settings['animate_speed_hide'],
			'animate_speed_show'       => $settings['animate_speed_show'],
			'background'               => $settings['background'],
			'button_1_link_color'      => $settings['button_1_link_color'],
			'button_1_button_color'    => $settings['button_1_button_color'],
			'button_1_button_hover'    => ( self::gdpr_su_hex_shift( $settings['button_1_button_color'], 'down', 20 ) ),
			'button_1_as_button'       => $settings['button_1_as_button'],
			'button_1_new_win'         => $settings['button_1_new_win'],
			'button_1_text'            => $settings['button_1_text'],
			'button_1_selected_text'   => $settings['button_1_selected_text'],
			'button_2_link_color'      => $settings['button_2_link_color'],
			'button_2_button_color'    => $settings['button_2_button_color'],
			'button_2_button_hover'    => ( self::gdpr_su_hex_shift( $settings['button_2_button_color'], 'down', 20 ) ),
			'button_2_as_button'       => $settings['button_2_as_button'],
			'button_2_new_win'         => $settings['button_2_new_win'],
			'button_3_link_color'      => $settings['button_3_link_color'],
			'button_3_button_color'    => $settings['button_3_button_color'],
			'button_3_button_hover'    => ( self::gdpr_su_hex_shift( $settings['button_3_button_color'], 'down', 20 ) ),
			'button_3_as_button'       => $settings['button_3_as_button'],
			'button_3_new_win'         => $settings['button_3_new_win'],
			'font_family'              => $settings['font_family'],
			'notify_animate_hide'      => $settings['notify_animate_hide'],
			'notify_animate_show'      => $settings['notify_animate_show'],
			'notify_div_id'            => $settings['notify_div_id'],
			'notify_position_vertical' => $settings['notify_position_vertical'],
			'text'                     => $settings['text'],
			'bar_heading_text'         => $settings['bar_heading_text'],
			'cookie_bar_as'            => $settings['cookie_bar_as'],
			'border_color'             => ( self::gdpr_su_hex_shift( $settings['text'], 'up', 70 ) ),
			'border_active_color'      => $settings['background'],
			'logging_on'               => $settings['logging_on'],
		);
		$slim_settings = apply_filters( 'gdprcookieconsent_json_settings', $slim_settings );
		$str           = wp_json_encode( $slim_settings );

		return $str;
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
