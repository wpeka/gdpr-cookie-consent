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

		add_shortcode( 'wpl_cookie_reject', array( $this, 'gdprcookieconsent_shortcode_reject_button' ) );      // a shortcode [wpl_cookie_reject].
		add_shortcode( 'wpl_cookie_link', array( $this, 'gdprcookieconsent_shortcode_more_link' ) );            // a shortcode [wpl_cookie_link].
		add_shortcode( 'wpl_cookie_button', array( $this, 'gdprcookieconsent_shortcode_main_button' ) );        // a shortcode [wpl_cookie_button].
		add_shortcode( 'wpl_cookie_settings', array( $this, 'gdprcookieconsent_shortcode_settings_button' ) );        // a shortcode [wpl_cookie_settings].
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

		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		if ( true === $the_options['is_on'] ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gdpr-cookie-consent-public.css', array(), $this->version, 'all' );
		}

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

		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		if ( true === $the_options['is_on'] ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gdpr-cookie-consent-public.js', array( 'jquery' ), $this->version, false );
			wp_localize_script( $this->plugin_name, 'log_obj', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		}
	}

	/**
	 * Register public modules
	 *
	 * @since 1.0
	 */
	public function public_modules() {
		$gdpr_public_modules = get_option( 'gdpr_public_modules' );
		if ( false === $gdpr_public_modules ) {
			$gdpr_public_modules = array();
		}
		foreach ( $this->modules as $module ) {
			$is_active = 1;
			if ( isset( $gdpr_public_modules[ $module ] ) ) {
				$is_active = $gdpr_public_modules[ $module ]; // checking module status.
			} else {
				$gdpr_public_modules[ $module ] = 1; // default status is active.
			}
			$module_file = plugin_dir_path( __FILE__ ) . "modules/$module/class-gdpr-cookie-consent-$module.php";
			if ( file_exists( $module_file ) && 1 === $is_active ) {
				self::$existing_modules[] = $module; // this is for module_exits checking.
				require_once $module_file;
			} else {
				$gdpr_public_modules[ $module ] = 0;
			}
		}
		$out = array();
		foreach ( $gdpr_public_modules as $k => $m ) {
			if ( in_array( $k, $this->modules, true ) ) {
				$out[ $k ] = $m;
			}
		}
		update_option( 'gdpr_public_modules', $out );
	}

	/**
	 * Returns HTML for a settings button.
	 *
	 * @since 1.0
	 * @param string $atts Shortcode parameters.
	 *
	 * @return string
	 */
	public function gdprcookieconsent_shortcode_settings_button( $atts ) {
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		$margin      = '';
		$link_tag    = '';
		if ( isset( $atts['margin'] ) ) {
			$margin = $atts['margin'];
		}
		$margin_style = '' !== $margin ? ' margin:' . $margin . '; ' : '';
		$defaults     = Gdpr_Cookie_Consent::gdpr_get_default_settings();
		$settings     = wp_parse_args( Gdpr_Cookie_Consent::gdpr_get_settings(), $defaults );
		update_option( 'gdpr_settings_enabled', 1 );

		$link_tag  = '<a id="gdpr_action_settings" data-gdpr_action="show_settings" class="gdpr-plugin-main-link gdpr_action_button" style="display:inline-block;' . $margin_style . 'margin-right:0!important;" >' . esc_html__( 'Show Details', 'gdpr-cookie-consent' ) . '</a>';
		$link_tag .= '<a id="gdpr_action_hide_settings" data-gdpr_action="hide_settings" class="gdpr-plugin-main-link gdpr_action_button" style="display:none;' . $margin_style . 'margin-right:0!important;" >' . esc_html__( 'Hide Details', 'gdpr-cookie-consent' ) . '</a>';

		return $link_tag;
	}
	/**
	 * Returns HTML for a standard (green, medium sized) 'Reject' button.
	 *
	 * @since 1.0
	 * @param string $atts Shortcode parameters.
	 *
	 * @return string
	 */
	public function gdprcookieconsent_shortcode_reject_button( $atts ) {
		$margin = '';
		if ( isset( $atts['margin'] ) ) {
			$margin = $atts['margin'];
		}
		$margin_style = '' !== $margin ? ' style="margin:' . $margin . ';" ' : '';

		$defaults = Gdpr_Cookie_Consent::gdpr_get_default_settings();
		$settings = wp_parse_args( Gdpr_Cookie_Consent::gdpr_get_settings(), $defaults );

		$classr = '';
		if ( $settings['button_3_as_button'] ) {
			$classr = ' class="' . $settings['button_3_button_size'] . ' gdpr-plugin-button gdpr-plugin-main-button cookie_action_close_header_reject gdpr_action_button"';
		} else {
			$classr = ' class="cookie_action_close_header_reject gdpr_action_button" ';
		}
		$url_reject = ( 'CONSTANT_OPEN_URL' === $settings['button_3_action'] && '#' !== $settings['button_3_url'] ) ? "href='$settings[button_3_url]'" : '';
		$link_tag   = '';
		$link_tag  .= ' <a ' . $url_reject . ' id="' . $this->gdprcookieconsent_remove_hash( $settings['button_3_action'] ) . '" ';
		$link_tag  .= ( $settings['button_3_new_win'] ) ? 'target="_blank" ' : '';
		$link_tag  .= $classr . '  data-gdpr_action="reject"' . $margin_style . '>' . stripslashes( esc_attr( $settings['button_3_text'] ) ) . '</a>';
		return $link_tag;
	}

	/**
	 * Returns HTML for a generic button.
	 *
	 * @since 1.0
	 * @param string $atts Shortcode parameters.
	 *
	 * @return string
	 */
	public function gdprcookieconsent_shortcode_main_button( $atts ) {
		$margin = '';
		if ( isset( $atts['margin'] ) ) {
			$margin = $atts['margin'];
		}
		$margin_style = '' !== $margin ? ' margin:' . $margin . '; ' : '';
		$defaults     = Gdpr_Cookie_Consent::gdpr_get_default_settings();
		$settings     = wp_parse_args( Gdpr_Cookie_Consent::gdpr_get_settings(), $defaults );
		$class        = '';
		if ( $settings['button_1_as_button'] ) {
			$class = ' class="' . $settings['button_1_button_size'] . ' gdpr-plugin-button gdpr-plugin-main-button cookie_action_close_header gdpr_action_button"';
		} else {
			$class = ' class="gdpr-plugin-main-button cookie_action_close_header gdpr_action_button" ';
		}

		// If is action not URL then don't use URL!
		$url       = ( 'CONSTANT_OPEN_URL' === $settings['button_1_action'] && '#' !== $settings['button_1_url'] ) ? "href='$settings[button_1_url]'" : '';
		$link_tag  = '<a ' . $url . ' data-gdpr_action="accept" id="' . $this->gdprcookieconsent_remove_hash( $settings['button_1_action'] ) . '" ';
		$link_tag .= ( $settings['button_1_new_win'] ) ? 'target="_blank" ' : '';
		$link_tag .= $class . ' style="display:inline-block; ' . $margin_style . 'margin-left:0!important;">' . stripslashes( esc_attr( $settings['button_1_text'] ) ) . '</a>';

		return $link_tag;
	}

	/**
	 * Returns HTML for a read more button.
	 *
	 * @since 1.0
	 * @param string $atts Shortcode parameters.
	 *
	 * @return string
	 */
	public function gdprcookieconsent_shortcode_more_link( $atts ) {
		$margin = '';
		if ( isset( $atts['margin'] ) ) {
			$margin = $atts['margin'];
		}
		$margin_style = '' !== $margin ? ' margin:' . $margin . '; ' : '';

		$defaults = Gdpr_Cookie_Consent::gdpr_get_default_settings();
		$settings = wp_parse_args( Gdpr_Cookie_Consent::gdpr_get_settings(), $defaults );

		$classm = '';
		if ( $settings['button_2_as_button'] ) {
			$classm = ' class="' . $settings['button_2_button_size'] . ' gdpr-plugin-button gdpr-plugin-main-link"';
		} else {
			$classm = ' class="gdpr-plugin-main-link" ';
		}

		// If is action not URL then don't use URL!
		$url_more  = ( 'CONSTANT_OPEN_URL' === $settings['button_2_action'] && '#' !== $settings['button_2_url'] ) ? "href='$settings[button_2_url]'" : '';
		$link_tag  = '<a ' . $url_more . ' id="' . $this->gdprcookieconsent_remove_hash( $settings['button_2_action'] ) . '" ';
		$link_tag .= ( $settings['button_2_new_win'] ) ? 'target="_blank" ' : '';
		$link_tag .= $classm . ' style="display:inline-block;' . $margin_style . '" >' . $settings['button_2_text'] . '</a>';
		return $link_tag;
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
		if ( '#' === $str{0} ) {
			$str = substr( $str, 1, strlen( $str ) );
		} else {
			return $str;
		}
		return self::gdprcookieconsent_remove_hash( $str );
	}

	/**
	 * Outputs the cookie control script in the footer.
	 * This function should be attached to the wp_footer action hook.
	 *
	 * @since 1.0
	 */
	public function gdprcookieconsent_inject_gdpr_script() {
		update_option( 'gdpr_settings_enabled', 0 );
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		if ( true === $the_options['is_on'] ) {
			// Output the HTML in the footer.
			$message       = nl2br( $the_options['notify_message'] );
			$about_message = stripslashes( nl2br( $the_options['about_message'] ) );
			$contents      = '';
			$message       = stripslashes( $message );
			$str           = do_shortcode( $message );
			$head          = $the_options['bar_heading_text'];
			$head          = trim( stripslashes( $head ) );
			if ( 1 === get_option( 'gdpr_settings_enabled' ) ) {
				$categories    = Gdpr_Cookie_Consent_Cookie_Custom::get_categories( true );
				$cookies_array = array();
				$cookies       = $this->get_cookies();
				foreach ( $categories as $category ) {
					$total = 0;
					$temp  = array();
					foreach ( $cookies as $cookie ) {
						if ( $cookie['category_id'] === $category['id_gdpr_cookie_category'] ) {
							$total++;
							$temp[] = $cookie;
						}
					}
					$cookies_array[ $category['gdpr_cookie_category_slug'] ]['data']  = $temp;
					$cookies_array[ $category['gdpr_cookie_category_slug'] ]['total'] = $total;
				}
				ob_start();
				include plugin_dir_path( __FILE__ ) . 'views/gdpr-cookie-consent-public-details.php';
				$contents = ob_get_contents();
				ob_end_clean();
			}

			$notify_html  = '<div id="' . $this->gdprcookieconsent_remove_hash( $the_options['notify_div_id'] ) . '" ><div class="gdpr_messagebar_content" >' . ( '' !== $head ? '<h5 class="gdpr_messagebar_head">' . $head . '</h5>' : '' ) . '<div>' . wpautop( $str ) . '</div>';
			$notify_html .= $contents;
			$notify_html .= '</div></div>';
			$notify_html  = apply_filters( 'gdprcookieconsent_gdpr_script', $notify_html );
			require_once plugin_dir_path( __FILE__ ) . 'partials/gdpr-cookie-consent-public-display.php';
		}
	}

	/**
	 * Returns scanned and custom cookies.
	 *
	 * @since 1.0
	 * @return array
	 */
	public function get_cookies() {
		$cookies_array = array();
		$cookie_custom = new Gdpr_Cookie_Consent_Cookie_Custom();
		$cookies_array = $cookie_custom->get_cookies();
		$cookies_array = apply_filters( 'gdprcookieconsent_cookies', $cookies_array );
		return $cookies_array;
	}

}
