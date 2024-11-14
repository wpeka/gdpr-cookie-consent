<?php

/**
 * The WooCommerce API Manager PHP Client Library is designed to be droppped into a WordPress plugin or theme.
 * This version is designed to be used with the WooCommerce API Manager version 2.x.
 *
 * Intellectual Property rights, and copyright, reserved by Todd Lahman, LLC as allowed by law include,
 * but are not limited to, the working concept, function, and behavior of this software,
 * the logical code structure and expression as written.
 *
 * @version       2.7
 * @author        Todd Lahman LLC https://www.toddlahman.com/
 * @copyright     Copyright (c) Todd Lahman LLC (support@toddlahman.com)
 * @package       WooCommerce API Manager plugin and theme library
 * @license       Copyright Todd Lahman LLC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_AM_Client_2_7_WPGDPR' ) ) {
	class WC_AM_Client_2_7_WPGDPR {

		/**
		 * Class args arguments
		 *
		 * @var string
		 */
		public $api_url          = '';
		public $data_key         = '';
		public $file             = '';
		public $plugin_name      = '';
		public $plugin_or_theme  = '';
		public $product_id       = '';
		public $slug             = '';
		public $software_title   = '';
		public $software_version = '';
		public $text_domain      = ''; // For language translation.

		/**
		 * Class properties.
		 *
		 * @var string
		 */
		public $data                              = array();
		public $identifier                        = '';
		public $no_product_id                     = false;
		public $product_id_chosen                 = 0;
		public $wc_am_activated_key               = '';
		public $wc_am_activation_tab_key          = '';
		public $wc_am_api_key_key                 = '';
		public $wc_am_deactivate_checkbox_key     = '';
		public $wc_am_deactivation_tab_key        = '';
		public $wc_am_domain                      = '';
		public $wc_am_instance_id                 = '';
		public $wc_am_instance_key                = '';
		public $wc_am_plugin_name                 = '';
		public $wc_am_product_id                  = '';
		public $wc_am_software_version            = '';
        public $legalpages_activated              = false;

		public function __construct( $file, $product_id, $software_version, $plugin_or_theme, $api_url, $software_title = '', $text_domain = '' ) {
			$this->no_product_id   = empty( $product_id ) ? true : false;
			$this->plugin_or_theme = esc_attr( $plugin_or_theme );

			if ( $this->no_product_id ) {
				$this->identifier        = $this->plugin_or_theme == 'plugin' ? dirname( untrailingslashit( plugin_basename( $file ) ) ) : get_stylesheet();
				$product_id              = strtolower( str_ireplace( array( ' ', '_', '&', '?', '-' ), '_', $this->identifier ) );
				$this->wc_am_product_id  = 'wc_am_product_id_' . $product_id;
				$this->product_id_chosen = get_option( $this->wc_am_product_id );
			} else {
				/**
				 * Preserve the value of $product_id to use for API requests. Pre 2.0 product_id is a string, and >= 2.0 is an integer.
				 */
				if ( is_int( $product_id ) ) {
					$this->product_id = absint( $product_id );
				} else {
					$this->product_id = esc_attr( $product_id );
				}
			}

			// If the product_id was not provided, but was saved by the customer, used the saved product_id.
			if ( empty( $this->product_id ) && ! empty( $this->product_id_chosen ) ) {
				$this->product_id = $this->product_id_chosen;
			}

			$this->file             = $file;
			$this->software_title   = esc_attr( $software_title );
			$this->software_version = esc_attr( $software_version );
			$this->api_url          = esc_url( $api_url );
			$this->text_domain      = esc_attr( $text_domain );
			/**
			 * If the product_id is a pre 2.0 string, format it to be used as an option key, otherwise it will be an integer if >= 2.0.
			 */
			$this->data_key            = 'wc_am_client_' . strtolower( str_ireplace( array( ' ', '_', '&', '?', '-' ), '_', $product_id ) );
			$this->wc_am_activated_key = $this->data_key . '_activated';

			if ( is_admin() ) {
				if ( ! empty( $this->plugin_or_theme ) && $this->plugin_or_theme == 'theme' ) {
					add_action( 'admin_init', array( $this, 'activation' ) );
				}

				if ( ! empty( $this->plugin_or_theme ) && $this->plugin_or_theme == 'plugin' ) {
					add_action( 'admin_init', array( $this, 'activation' ) );
				}

				add_action( 'admin_notices', array( $this, 'check_external_blocking' ) );



				/**
				 * Set all data defaults here
				 */
				$this->wc_am_api_key_key  = $this->data_key . '_api_key';
				$this->wc_am_instance_key = $this->data_key . '_instance';

				/**
				 * Set all admin menu data
				 */
				$this->wc_am_deactivate_checkbox_key     = $this->data_key . '_deactivate_checkbox';
				$this->wc_am_activation_tab_key          = $this->data_key . '_dashboard';
				$this->wc_am_deactivation_tab_key        = $this->data_key . '_deactivation';
				$this->wc_am_settings_menu_title         = $this->software_title . esc_html__( ' Activation', $this->text_domain );
				$this->wc_am_settings_title              = $this->software_title . esc_html__( ' API Key Activation', $this->text_domain );
				$this->wc_am_menu_tab_activation_title   = esc_html__( 'API Key Activation', $this->text_domain );
				$this->wc_am_menu_tab_deactivation_title = esc_html__( 'API Key Deactivation', $this->text_domain );

				/**
				 * Set all software update data here
				 */
				$this->data                    = get_option( $this->data_key );
				$this->wc_am_plugin_name       = $this->plugin_or_theme == 'plugin' ? untrailingslashit( plugin_basename( $this->file ) ) : get_stylesheet(); // same as plugin slug. if a theme use a theme name like 'twentyeleven'
				$this->wc_am_instance_id       =  get_option( $this->wc_am_instance_key ); // Instance ID (unique to each blog activation)

				$instance_option = get_option('wc_am_client_wplegalpages_instance');
					if (!empty($instance_option)) {
   						 $this->wc_am_instance_id = $instance_option;
					}
				/**
				 * Some web hosts have security policies that block the : (colon) and // (slashes) in http://,
				 * so only the host portion of the URL can be sent. For example the host portion might be
				 * www.example.com or example.com. http://www.example.com includes the scheme http,
				 * and the host www.example.com.
				 * Sending only the host also eliminates issues when a client site changes from http to https,
				 * but their activation still uses the original scheme.
				 * To send only the host, use a line like the one below:
				 *
				 * $this->wc_am_domain = str_ireplace( array( 'http://', 'https://' ), '', home_url() ); // blog domain name
				 */
				$this->wc_am_domain           = str_ireplace( array( 'http://', 'https://' ), '', home_url() ); // blog domain name
				$this->wc_am_software_version = $this->software_version; // The software version

				/**
				 * Check for software updates
				 */
				$this->check_for_update();
			}

			/**
			 * Deletes all data if plugin deactivated
			 */
			if ( $this->plugin_or_theme == 'plugin' ) {
				register_deactivation_hook( $this->file, array( $this, 'uninstall' ) );
			}

			if ( $this->plugin_or_theme == 'theme' ) {
				add_action( 'switch_theme', array( $this, 'uninstall' ) );
			}
		}

	
		/**
		 * Generate the default data.
		 */
		public function activation() {
			// Get the instance key and instance option from the database
			$instance_exists = get_option($this->wc_am_instance_key);
			$instance_option = get_option('wc_am_client_wplegalpages_instance');

			// Check if the data key or instance key does not exist
			if (get_option($this->data_key) === false || $instance_exists === false) {
    		// If instance option exists, update the instance key with the instance option
    		if ($instance_option !== false) {
    	    update_option($this->wc_am_instance_key, $instance_option);
    		} 
    		// If instance does not exist and instance option is also false, generate a new instance key
    		else if ($instance_exists === false) {
    	    update_option($this->wc_am_instance_key, wp_generate_password(12, false));
   			 }

    		// Update the deactivate checkbox key and activated key options
    		update_option($this->wc_am_deactivate_checkbox_key, 'on');
    		update_option($this->wc_am_activated_key, 'Deactivated');
			}
		}

		/**
		 * Deletes all data if plugin deactivated
		 */
		public function uninstall() {
			/**
			 * @since 2.5.1
			 *
			 * Filter wc_am_client_uninstall_disable
			 * If set to false uninstall() method will be disabled.
			 */
			if ( apply_filters( 'wc_am_client_uninstall_disable', true ) ) {
				global $blog_id;

				$this->license_key_deactivation();

				// Remove options pre API Manager 2.0
				if ( is_multisite() ) {
					switch_to_blog( $blog_id );

					foreach (
						array(
							$this->wc_am_instance_key,
							$this->wc_am_deactivate_checkbox_key,
							$this->wc_am_activated_key,
						) as $option
					) {

						delete_option( $option );
					}

					restore_current_blog();
				} else {
					foreach (
						array(
							$this->wc_am_instance_key,
							$this->wc_am_deactivate_checkbox_key,
							$this->wc_am_activated_key
						) as $option
					) {

						delete_option( $option );
					}
				}
			}
		}

		/**
		 * Deactivates the license on the API server
		 */
		public function license_key_deactivation() {
			$activation_status = get_option( $this->wc_am_activated_key );
			$api_key           = $this->data[ $this->wc_am_api_key_key ];

			$args = array(
				'api_key' => $api_key,
			);

			if ( $activation_status == 'Activated' && $api_key != '' ) {
				$this->deactivate( $args ); // reset API Key activation
			}
		}

		
		/**
		 * Check for external blocking contstant.
		 */
		public function check_external_blocking() {
			// show notice if external requests are blocked through the WP_HTTP_BLOCK_EXTERNAL constant
			if ( defined( 'WP_HTTP_BLOCK_EXTERNAL' ) && WP_HTTP_BLOCK_EXTERNAL === true ) {
				// check if our API endpoint is in the allowed hosts
				$host = parse_url( $this->api_url, PHP_URL_HOST );

				if ( ! defined( 'WP_ACCESSIBLE_HOSTS' ) || stristr( WP_ACCESSIBLE_HOSTS, $host ) === false ) {
					?>
                    <div class="notice notice-error">
                        <p><?php printf( __( '<b>Warning!</b> You\'re blocking external requests which means you won\'t be able to get %s updates. Please add %s to %s.', $this->text_domain ), $this->software_title, '<strong>' . $host . '</strong>', '<code>WP_ACCESSIBLE_HOSTS</code>' ); ?></p>
                    </div>
					<?php
				}
			}
		}

		

		
		/**
		 * Builds the URL containing the API query string for activation, deactivation, and status requests.
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		public function create_software_api_url( $args ) {
			return add_query_arg( 'wc-api', 'wc-am-api', $this->api_url ) . '&' . http_build_query( $args );
		}

		/**
		 * Sends the request to activate to the API Manager.
		 *
		 * @param array $args
		 *
		 * @return bool|string
		 */
		public function activate( $args, $product_id = 0 ) {

			$this->product_id = $product_id;

			$defaults = array(
				'wc_am_action'          => 'activate',
				'product_id'       => $this->product_id,
				'instance'         => $this->wc_am_instance_id,
				'object'           => $this->wc_am_domain,
				'software_version' => $this->wc_am_software_version
			);

			$args       = wp_parse_args( $defaults, $args );
			
			return $args;
		}

		/**
		 * Sends the request to deactivate to the API Manager.
		 *
		 * @param array $args
		 *
		 * @return bool|string
		 */
		public function deactivate( $args , $product_id = '') {

		if($product_id !== ''){
			$defaults = array(
				'wc_am_action'    => 'deactivate',
				'product_id' => $product_id,
				'instance'   => $this->wc_am_instance_id,
				'object'     => $this->wc_am_domain
			);
		} 
		else{
			$defaults = array(
				'wc_am_action'    => 'deactivate',
				'product_id' => $this->product_id,
				'instance'   => $this->wc_am_instance_id,
				'object'     => $this->wc_am_domain
			);
		}
			$args       = wp_parse_args( $defaults, $args );
			$target_url = esc_url_raw( $this->create_software_api_url( $args ) );
			$request    = wp_safe_remote_post( $target_url, array( 'timeout' => 15 ) );

			if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
				// Request failed
				return false;
			}

			$response = wp_remote_retrieve_body( $request );

			return $response;
		}

		/**
		 * Sends the status check request to the API Manager.
		 *
		 * @return bool|string
		 */
		public function status( $args, $product_id ) {
			$defaults = array(
				'wc_am_action'    => 'status',
				'api_key'      => $this->data ? $this->data[ $this->wc_am_api_key_key ] : $args['api_key'],
				'product_id' => $product_id,
				'instance'   => $this->wc_am_instance_id,
				'object'     => $this->wc_am_domain
			);

			return $defaults;
		}

		/**
		 * Check for software updates.
		 */
		public function check_for_update() {
			$this->plugin_name = $this->wc_am_plugin_name;

			// Slug should be the same as the plugin/theme directory name
			if ( strpos( $this->plugin_name, '.php' ) !== 0 ) {
				$this->slug = dirname( $this->plugin_name );
			} else {
				$this->slug = $this->plugin_name;
			}

			/*********************************************************************
			 * The plugin and theme filters should not be active at the same time
			 *********************************************************************/ /**
			 * More info:
			 * function set_site_transient moved from wp-includes/functions.php
			 * to wp-includes/option.php in WordPress 3.4
			 *
			 * set_site_transient() contains the pre_set_site_transient_{$transient} filter
			 * {$transient} is either update_plugins or update_themes
			 *
			 * Transient data for plugins and themes exist in the Options table:
			 * _site_transient_update_themes
			 * _site_transient_update_plugins
			 */

			// uses the flag above to determine if this is a plugin or a theme update request
			if ( $this->plugin_or_theme == 'plugin' ) {
				/**
				 * Plugin Updates
				 */
				add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_check' ) );
				// Check For Plugin Information to display on the update details page
				add_filter( 'plugins_api', array( $this, 'information_request' ), 10, 3 );
			} elseif ( $this->plugin_or_theme == 'theme' ) {
				/**
				 * Theme Updates
				 */
				add_filter( 'pre_set_site_transient_update_themes', array( $this, 'update_check' ) );

				// Check For Theme Information to display on the update details page
				//add_filter( 'themes_api', array( $this, 'information_request' ), 10, 3 );

			}
		}

		/**
		 * Sends and receives data to and from the server API
		 *
		 * @since  2.0
		 *
		 * @param array $args
		 *
		 * @return bool|string $response
		 */
		public function send_query( $args ) {
			$target_url = esc_url_raw( add_query_arg( 'wc-api', 'wc-am-api', $this->api_url ) . '&' . http_build_query( $args ) );
			$request    = wp_safe_remote_post( $target_url, array( 'timeout' => 15 ) );

			if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
				return false;
			}

			$response = wp_remote_retrieve_body( $request );

			return ! empty( $response ) ? $response : false;
		}

		/**
		 * Check for updates against the remote server.
		 *
		 * @since  2.0
		 *
		 * @param object $transient
		 *
		 * @return object $transient
		 */
		public function update_check( $transient ) {
			if ( empty( $transient->checked ) ) {
				return $transient;
			}

			$args = array(
				'wc_am_action'     => 'update',
				'slug'        => $this->slug,
				'plugin_name' => $this->plugin_name,
				'version'     => $this->wc_am_software_version,
				'product_id'  => $this->product_id,
				'api_key'     =>  (is_array($this->data) && isset($this->data[$this->wc_am_api_key_key])) ? $this->data[$this->wc_am_api_key_key] : '',
				'instance'    => $this->wc_am_instance_id,
			);

			// Check for a plugin update.
			$response = json_decode( $this->send_query( $args ), true );

			if ( isset( $response['data']['error_code'] ) ) {
				add_settings_error( 'wc_am_client_error_text', 'wc_am_client_error', "{$response['data']['error']}", 'error' );
			}

			if (  is_array( $response ) && isset( $response['success'] ) && false !== $response && true === $response['success'] ) {
				// New plugin version from the API.
				$new_ver = (string) $response['data']['package']['new_version'];
				// Current installed plugin version.
				$curr_ver = (string) $this->wc_am_software_version;

				$package = array(
					'id'             => $response['data']['package']['id'],
					'slug'           => $response['data']['package']['slug'],
					'plugin'         => $response['data']['package']['plugin'],
					'new_version'    => $response['data']['package']['new_version'],
					'url'            => $response['data']['package']['url'],
					'tested'         => $response['data']['package']['tested'],
					'package'        => $response['data']['package']['package'],
					'upgrade_notice' => $response['data']['package']['upgrade_notice'],
				);

				if ( isset( $new_ver ) && isset( $curr_ver ) ) {
					if ( $response !== false && version_compare( $new_ver, $curr_ver, '>' ) ) {
						if ( 'plugin' === $this->plugin_or_theme ) {
							$transient->response[ $this->plugin_name ] = (object) $package;
							unset( $transient->no_update[ $this->plugin_name ] );
						} elseif ( 'theme' === $this->plugin_or_theme ) {
							$transient->response[ $this->plugin_name ]['new_version'] = $response['data']['package']['new_version'];
							$transient->response[ $this->plugin_name ]['url']         = $response['data']['package']['url'];
							$transient->response[ $this->plugin_name ]['package']     = $response['data']['package']['package'];
						}
					}
				}
			}

			return $transient;
		}

		/**
		 * API request for informatin.
		 *
		 * If `$action` is 'query_plugins' or 'plugin_information', an object MUST be passed.
		 * If `$action` is 'hot_tags` or 'hot_categories', an array should be passed.
		 *
		 * @param false|object|array $result The result object or array. Default false.
		 * @param string             $action The type of information being requested from the Plugin Install API.
		 * @param object             $args
		 *
		 * @return object
		 */
		public function information_request( $result, $action, $args ) {
			// Check if this plugins API is about this plugin
			if ( isset( $args->slug ) ) {
				if ( $args->slug != $this->slug ) {
					return $result;
				}
			} else {
				return $result;
			}

			$args = array(
				'wc_am_action'     => 'plugininformation',
				'plugin_name' => $this->plugin_name,
				'version'     => $this->wc_am_software_version,
				'product_id'  => $this->product_id,
				'api_key'     => $this->data[ $this->wc_am_api_key_key ],
				'instance'    => $this->wc_am_instance_id,
				'object'      => $this->wc_am_domain,
			);

			$response = unserialize( $this->send_query( $args ) );

			if ( isset( $response ) && is_object( $response ) && $response !== false ) {
				return $response;
			}

			return $result;
		}
	}
}