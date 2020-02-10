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

		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		if ( true === $the_options['is_on'] ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gdpr-cookie-consent-public' . GDPR_CC_SUFFIX . '.css', array(), $this->version, 'all' );
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
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gdpr-cookie-consent-public' . GDPR_CC_SUFFIX . '.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name . '-bootstrap-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), $this->version, true );
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
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		if ( true === $the_options['is_on'] ) {
			$timber = new Timber\Timber();
			// Output the HTML in the footer.
			$message       = nl2br( $the_options['notify_message'] );
			$about_message = stripslashes( nl2br( $the_options['about_message'] ) );
			$message       = stripslashes( $message );
			$str           = $message;
			$head          = $the_options['bar_heading_text'];
			$head          = trim( stripslashes( $head ) );
			$default_array = array( 'none', 'default', 'classic' );
			$template      = $the_options['template'];
			if ( 'none' !== $template ) {
				$template_parts = explode( '-', $template );
				$template       = array_pop( $template_parts );
			}
			$the_options['container_class'] = 'gdpr-' . $the_options['cookie_bar_as'] . ' ' . $template;
			if ( in_array( $template, $default_array, true ) ) {
				$template = 'default';
			}
			$template                               = apply_filters( 'gdprcookieconsent_template', $template );
			$the_options['str']                     = $str;
			$the_options['head']                    = $head;
			$the_options['version']                 = $this->version;
			$the_options['show_again_container_id'] = $this->gdprcookieconsent_remove_hash( $the_options['show_again_div_id'] );
			$the_options['container_id']            = $this->gdprcookieconsent_remove_hash( $the_options['notify_div_id'] );
			$the_options['button_1_action_id']      = $this->gdprcookieconsent_remove_hash( $the_options['button_1_action'] );
			$the_options['button_2_action_id']      = $this->gdprcookieconsent_remove_hash( $the_options['button_2_action'] );
			$the_options['button_3_action_id']      = $this->gdprcookieconsent_remove_hash( $the_options['button_3_action'] );
			$the_options['button_4_action_id']      = $this->gdprcookieconsent_remove_hash( $the_options['button_4_action'] );

			$the_options['backdrop'] = $the_options['popup_overlay'] ? 'static' : 'false';

			$the_options['button_1_classes'] = 'gdpr_action_button ';
			if ( $the_options['button_1_as_button'] ) {
				switch ( $the_options['button_1_button_size'] ) {
					case 'medium':
						$the_options['button_1_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_1_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_1_classes'] .= 'btn btn-sm';
						break;
				}
			}
			$the_options['button_2_classes'] = '';
			if ( $the_options['button_2_as_button'] ) {
				switch ( $the_options['button_2_button_size'] ) {
					case 'medium':
						$the_options['button_2_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_2_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_2_classes'] .= 'btn btn-sm';
						break;
				}
			} else {
				$the_options['button_2_classes'] = 'gdpr_link_button';
			}
			$the_options['button_3_classes'] = 'gdpr_action_button ';
			if ( $the_options['button_3_as_button'] ) {
				switch ( $the_options['button_3_button_size'] ) {
					case 'medium':
						$the_options['button_3_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_3_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_3_classes'] .= 'btn btn-sm';
						break;
				}
			}
			$the_options['button_4_classes'] = 'gdpr_action_button ';
			if ( $the_options['button_4_as_button'] ) {
				switch ( $the_options['button_4_button_size'] ) {
					case 'medium':
						$the_options['button_4_classes'] .= 'btn';
						break;
					case 'large':
						$the_options['button_4_classes'] .= 'btn btn-lg';
						break;
					case 'small':
						$the_options['button_4_classes'] .= 'btn btn-sm';
						break;
				}
			}

			$categories                   = Gdpr_Cookie_Consent_Cookie_Custom::get_categories( true );
			$cookies                      = $this->get_cookies();
			$categories_data              = array();
			$preference_cookies           = isset( $_COOKIE['wpl_user_preference'] ) ? json_decode( stripslashes( sanitize_text_field( wp_unslash( $_COOKIE['wpl_user_preference'] ) ) ), true ) : '';
			$viewed_cookie                = isset( $_COOKIE['wpl_viewed_cookie'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['wpl_viewed_cookie'] ) ) : '';
			$the_options['viewed_cookie'] = $viewed_cookie;
			foreach ( $categories as $category ) {
				$total = 0;
				$temp  = array();
				foreach ( $cookies as $cookie ) {
					if ( $cookie['category_id'] === $category['id_gdpr_cookie_category'] ) {
						$total++;
						$temp[] = $cookie;
					}
				}
				$category['data']  = $temp;
				$category['total'] = $total;
				if ( isset( $preference_cookies[ $category['gdpr_cookie_category_slug'] ] ) && 'yes' === $preference_cookies[ $category['gdpr_cookie_category_slug'] ] ) {
					$category['is_ticked'] = true;
				} else {
					$category['is_ticked'] = false;
				}
				$categories_data[] = $category;
			}

			if ( true === $the_options['button_4_is_on'] ) {
				$cookie_data               = array();
				$cookie_data['categories'] = $categories_data;
				$cookie_data['msg']        = $about_message;
				$credit_link               = sprintf(
					/* translators: 1: GDPR Cookie Consent Plugin*/
					__( 'Powered by %1$s', 'gdpr-cookie-consent' ),
					'<a href="https://club.wpeka.com/product/wp-gdpr-cookie-consent/" id="cookie_credit_link" target="_blank">' . __( 'GDPR Cookie Consent Plugin', 'gdpr-cookie-consent' ) . '</a>'
				);
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
			}
			ob_start();
			$notify_html = $timber->render( 'templates/' . $template . '.twig', $the_options );
			ob_end_clean();

			$notify_html = apply_filters( 'gdprcookieconsent_gdpr_script', $notify_html );
			// if filter is applied.
			if ( '' === $notify_html ) {
				return;
			}
			echo $notify_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<script type="text/javascript">
				/* <![CDATA[ */
				gdpr_cookies_list = '<?php echo wp_json_encode( $categories_data ); ?>';
				gdpr_cookiebar_settings='<?php echo Gdpr_Cookie_Consent::gdpr_get_json_settings(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>';
				/* ]]> */
			</script>
			<?php
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

	/**
	 * Returns policy data for shortcode wpl_cookie_details.
	 *
	 * @since 1.9
	 * @return string
	 */
	public function gdprcookieconsent_shortcode_cookie_details() {
		$args                = array(
			'numberposts' => -1,
			'post_type'   => 'gdprpolicies',
		);
		$wp_legalpolicy_data = get_posts( $args );
		$content             = '';
		if ( is_array( $wp_legalpolicy_data ) && ! empty( $wp_legalpolicy_data ) ) {
			$content .= '<p>For further information on how we use cookies, please refer to the table below.</p>';
			$content .= "<div style='overflow-x:scroll;overflow:auto;' class='wp_legalpolicy'>";
			$content .= "<table style='width:100%;margin:0 auto;'>";
			$content .= '<thead>';
			$content .= '<th>Third Party Companies</th><th>Purpose</th><th>Applicable Privacy/Cookie Policy Link</th>';
			$content .= '</thead>';
			$content .= '<tbody>';
			foreach ( $wp_legalpolicy_data as $policypost ) {
				$content .= '<tr>';
				$content .= '<td>' . $policypost->post_title . '</td>';
				$content .= '<td>' . $policypost->post_content . '</td>';
				$links    = get_post_meta( $policypost->ID, '_gdpr_policies_links_editor' );
				$content .= '<td>' . $links[0] . '</td>';
				$content .= '</tr>';
			}
			$content .= '</tbody></table></div>';
		}
		return $content;
	}

}
