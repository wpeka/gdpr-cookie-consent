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
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gdpr-cookie-consent-admin.css', array(), $this->version, 'all' );

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

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gdpr-cookie-consent-admin.js', array( 'jquery', 'wp-color-picker', 'gdprcookieconsent_cookie_custom' ), $this->version, false );

	}

	/**
	 * Register admin modules
	 *
	 * @since 1.0
	 */
	public function admin_modules() {
		$gdpr_admin_modules = get_option( 'gdpr_admin_modules' );
		if ( false === $gdpr_admin_modules ) {
			$gdpr_admin_modules = array();
		}
		foreach ( $this->modules as $module ) {
			$is_active = 1;
			if ( isset( $gdpr_admin_modules[ $module ] ) ) {
				$is_active = $gdpr_admin_modules[ $module ]; // checking module status.
			} else {
				$gdpr_admin_modules[ $module ] = 1; // default status is active.
			}
			$module_file = plugin_dir_path( __FILE__ ) . "modules/$module/class-gdpr-cookie-consent-$module.php";
			if ( file_exists( $module_file ) && 1 === $is_active ) {
				self::$existing_modules[] = $module; // this is for module_exits checking.
				require_once $module_file;
			} else {
				$gdpr_admin_modules[ $module ] = 0;
			}
		}
		$out = array();
		foreach ( $gdpr_admin_modules as $k => $m ) {
			if ( in_array( $k, $this->modules, true ) ) {
				$out[ $k ] = $m;
			}
		}
		update_option( 'gdpr_admin_modules', $out );
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
		$gdpr_shortcode_content  = '<h2>' . __( 'Cookie Bar Shortcodes', 'gdpr-cookie-consent' ) . '</h2>' .
									__( 'You can enter the shortcodes in the "message" field of the Cookie Consent bar. They add nicely formatted buttons and/or links into the cookie bar, without you having to add any HTML.', 'gdpr-cookie-consent' ) .
									'<ul>' .
									'<li>' .
									'<div style="font-weight: bold;">[wpl_cookie_button]</div>' .
									__( 'This is the "accept cookies button" you customize below.', 'gdpr-cookie-consent' ) .
									'</li>' .
									'<li>' .
									'<div style="font-weight: bold;">[wpl_cookie_reject]</div>' .
									__( 'This is the cookie decline button shortcode.', 'gdpr-cookie-consent' ) .
									'</li>' .
									'<li>' .
									'<div style="font-weight: bold;">[wpl_cookie_link]</div>' .
									__( 'This is the "read more" link you customize above.', 'gdpr-cookie-consent' ) .
									'</li>' .
									'<li>' .
									'<div style="font-weight: bold;">[wpl_cookie_settings]</div>' .
									__( 'This is the cookie settings button shortcode.', 'gdpr-cookie-consent' ) .
									'</li>' .
									'<li>' .
									'<div style="font-weight: bold;">[wpl_cookie_details]</div>' .
									__( 'Use this shortcode to display third-party cookie details on your privacy or cookie policy pages.', 'gdpr-cookie-consent' ) .
									'</li>';
		$gdpr_shortcode_content  = apply_filters( 'gdprcookieconsent_help_content', $gdpr_shortcode_content );
		$gdpr_shortcode_content .= '</ul>';
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
								'<p>' . __( 'If you need help understanding, using, or extending GDPR Cookie Consent Plugin,', 'gdpr-cookie-consent' ) . ' <a href="https://docs.wpeka.com/wp-gdpr-cookie-consent/" target="_blank">' . __( 'please read our documentation.', 'gdpr-cookie-consent' ) . '</a> ' . __( 'You will find all kinds of resources including snippets, tutorials and more.', 'gdpr-cookie-consent' ) . '</p>' .
								'<p>' . __( 'For further assistance with GDPR Cookie Consent plugin you can use the', 'gdpr-cookie-consent' ) . ' <a href="https://wordpress.org/support/plugin/gdpr-cookie-consent" target="_blank">' . __( 'community forum.', 'gdpr-cookie-consent' ) . '</a> ' . __( 'If you need help with premium extensions sold by WPEka', 'gdpr-cookie-consent' ) . ' <a href="http://wpeka.freshdesk.com/" target="_blank">' . __( 'use our helpdesk.', 'gdpr-cookie-consent' ) . '</a></p>',
			)
		);
		$screen->add_help_tab(
			array(
				'id'      => 'gdprcookieconsent_bugs_tab',
				'title'   => __( 'Found a bug?', 'gdpr-cookie-consent' ),
				'content' => '<h2>' . __( 'Found a bug?', 'gdpr-cookie-consent' ) . '</h2>' .
								'<p>' . __( 'If you find a bug within GDPR Cookie Consent plugin you can create a ticket via', 'gdpr-cookie-consent' ) . ' <a href="http://wpeka.freshdesk.com/" target="_blank">' . __( 'our helpdesk.', 'gdpr-cookie-consent' ) . '</a></p>',
			)
		);
		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'gdpr-cookie-consent' ) . '</strong></p>' .
					'<p><a href="https://club.wpeka.com/product/wp-gdpr-cookie-consent/" target="_blank">' . __( 'About GDPR Cookie Consent', 'gdpr-cookie-consent' ) . '</a></p>' .
					'<p><a href="https://wordpress.org/support/plugin/gdpr-cookie-consent/" target="_blank">' . __( 'WordPress.org project', 'gdpr-cookie-consent' ) . '</a></p>' .
					'<p><a href="https://club.wpeka.com/category/plugins/?orderby=popularity" target="_blank">' . __( 'WPEka Plugins', 'gdpr-cookie-consent' ) . '</a></p>'
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
		$footer = '<span id="footer-thankyou">' .
					'If you like <b>GDPR Cookie Consent</b>, please leave us a  <a href="https://wordpress.org/support/plugin/gdpr-cookie-consent/reviews?rate=5#new-post" target="_blank" aria-label="' . esc_attr__( 'five star', 'gdpr-cookie-consent' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. A huge thanks in advance!' .
					'</span>';
		return $footer;
	}

	/**
	 * Registers menu options, hooked into admin_menu.
	 *
	 * @since 1.0
	 */
	public function admin_menu() {
		add_menu_page( 'GDPR Cookie Consent', __( 'GDPR Cookie Consent', 'gdpr-cookie-consent' ), 'manage_options', 'gdpr-cookie-consent', array( $this, 'admin_settings_page' ), GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/gdpr_icon.png', 67 );
		add_submenu_page( 'gdpr-cookie-consent', __( 'Cookie Settings', 'gdpr-cookie-consent' ), __( 'Cookie Settings', 'gdpr-cookie-consent' ), 'manage_options', 'gdpr-cookie-consent', array( $this, 'admin_settings_page' ) );
		add_submenu_page( 'gdpr-cookie-consent', __( 'Policy Data', 'gdpr-cookie-consent' ), __( 'Policy Data', 'gdpr-cookie-consent' ), 'manage_options', 'edit.php?post_type=' . GDPR_POLICY_DATA_POST_TYPE );
		add_submenu_page( '', __( 'Import Policies', 'gdpr-cookie-consent' ), __( 'Import Policies', 'gdpr-cookie-consent' ), 'manage_options', 'gdpr-policies-import', array( $this, 'gdpr_policies_import_page' ) );
	}

	/**
	 * Loads wpeka CSS.
	 *
	 * @since 1.0
	 */
	public function wpeka_load_styles_account_gdpr() {
		wp_enqueue_style( 'plugin_option_styles', GDPR_COOKIE_CONSENT_PLUGIN_URL . '/wpeka/options/lib/css/options-style.css', '', $this->version );

	}

	/**
	 * Admin settings page.
	 *
	 * @since 1.0
	 */
	public function admin_settings_page() {
		wp_enqueue_style( $this->plugin_name );
		wp_enqueue_script( $this->plugin_name );
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

				foreach ( $the_options as $key => $value ) {
					if ( isset( $_POST[ $key . '_field' ] ) ) {
						// Store sanitised values only.
						$the_options[ $key ] = Gdpr_Cookie_Consent::gdpr_sanitise_settings( $key, wp_unslash( $_POST[ $key . '_field' ] ) ); // phpcs:ignore input var ok, CSRF ok, sanitization ok.
					}
				}
				update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
				echo '<div class="updated"><p><strong>' . esc_attr__( 'Settings Updated.', 'gdpr-cookie-consent' ) . '</strong></p></div>';
			}
		}
		if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) ) === 'xmlhttprequest' ) {
			exit();
		}
		require_once plugin_dir_path( __FILE__ ) . 'partials/gdpr-cookie-consent-admin-display.php';
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
	 * Returns button sizes, used when printing admin form.
	 *
	 * @since 1.0
	 * @return array
	 */
	public function get_button_sizes() {
		$sizes = array(
			__( 'Extra Large', 'gdpr-cookie-consent' ) => 'super',
			__( 'Large', 'gdpr-cookie-consent' )       => 'large',
			__( 'Medium', 'gdpr-cookie-consent' )      => 'medium',
			__( 'Small', 'gdpr-cookie-consent' )       => 'small',
		);
		$sizes = apply_filters( 'gdprcookieconsent_sizes', $sizes );
		return $sizes;
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
			wp_die( esc_attr( 'You do not have sufficient permissions to access this page.', 'gdpr-cookie-consent' ) );
		}
		include plugin_dir_path( __FILE__ ) . 'views/gdpr-policies-import-page.php';
	}

}
