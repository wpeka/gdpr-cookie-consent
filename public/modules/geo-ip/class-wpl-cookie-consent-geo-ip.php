<?php
/**
 * The geo ip functionality of the plugin.
 *
 * @link       https://club.wpeka.com/
 * @since      3.0.0
 *
 * @package    Wpl_Cookie_Consent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use GeoIp2\Database\Reader;
/**
 * The frontend-specific functionality for geo ip.
 *
 * @package    Wpl_Cookie_Consent
 * @subpackage Wpl_Cookie_Consent/public/modules
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent_Geo_Ip {

	/**
	 * Display errors.
	 *
	 * @var array $errors Display errors.
	 */
	private static $errors = array();
	/**
	 * Gdpr_Cookie_Consent_Geo_Ip constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		if ( Gdpr_Cookie_Consent::is_request( 'admin' ) ) {
			add_action( 'gdpr_module_settings_cookie_usage_for', array( $this, 'wplgip_cookie_usage_for_general' ), 5 );
			// add_action( 'admin_menu', array( $this, 'wplgip_admin_menu' ), 15 );
			add_action('gdpr_setting_integration_tab',array( $this, 'wp_settings_integration_tab' ) );
		}
	}
	/**
	 * Maxming geo integration settings form.
	 *
	 * @since 3.0.2
	 */
	public function wp_settings_integration_tab(){ ?>
		<c-tab title="<?php esc_attr_e( 'Integration edcw', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#integration">
			<?Php
				$pro_is_activated  = get_option( 'wpl_pro_active', false );
				$installed_plugins = get_plugins();
				$pro_installed     = isset( $installed_plugins['wpl-cookie-consent/wpl-cookie-consent.php'] ) ? true : false;
				$pro_is_activated = get_option( 'wpl_pro_active', false );
				$api_key_activated = '';
				$api_key_activated = get_option( 'wc_am_client_wpl_cookie_consent_activated' );
				// Require the class file for gdpr cookie consent api framework settings.
				require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';

				// Instantiate a new object of the GDPR_Cookie_Consent_Settings class.
				$this->settings = new GDPR_Cookie_Consent_Settings();
				// Call the is_connected() method from the instantiated object to check if the user is connected.
				$is_user_connected = $this->settings->is_connected();

				$class_for_blur_content = $is_user_connected ? '' : 'gdpr-blur-background'; // Add a class for styling purposes.

				$class_for_card_body_blur_content = $is_user_connected ? '' : 'gdpr-body-blur-background'; // Add a class for styling purposes.

				$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
				$geo_options = get_option( 'wpl_geo_options' );


				$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
				$geo_options = get_option( 'wpl_geo_options' );

				$enable_value = $the_options['enable_safe'] === 'true' ? 'overlay-integration-style' : '';
				if ( ! $geo_options['enable_geotargeting'] ) {
					$geo_options['enable_geotargeting'] = 'false';
				}
				$enable        = $geo_options['enable_geotargeting'];
				$enable_value1 = $geo_options['enable_geotargeting'] === 'false' ? 'overlay-integration-style__disable' : '';
				if ( ! defined( 'ABSPATH' ) ) {
					exit;
				}
				$response_maxmind = wp_remote_post(
					GDPR_API_URL . 'get_maxmind_data',
					array(
						'body' => array(
							'the_options_enable_safe'          => $the_options['enable_safe'],
							'pro_installed'                    => $pro_installed,
							'pro_is_activated'                  => $pro_is_activated,
							'api_key_activated'                 => $api_key_activated,
							'is_user_connected'                => $is_user_connected,
							'class_for_blur_content'           => $class_for_blur_content,
							'class_for_card_body_blur_content' => $class_for_card_body_blur_content,
							'wpl_pro_active'                   => $geo_options,
							'enable_geotargeting'              => $geo_options['enable_geotargeting'],
							'enable_safe'					   => $the_options['enable_safe'],
							'enable_value2'                    => $the_options['enable_safe'] === 'true' ? 'overlay-integration-style' : '',
							'enable_value1'                    => $geo_options['enable_geotargeting'] === 'false' ? 'overlay-integration-style__disable' : '',
						),
					)
				);
				if ( is_wp_error( $response_maxmind ) ) {
					$maxmind_text = '';
				}

				$response_status = wp_remote_retrieve_response_code( $response_maxmind );


				if ( 200 === $response_status ) {
					$maxmind_text = json_decode( wp_remote_retrieve_body( $response_maxmind ) );
				}
				?>
				<?php echo $maxmind_text; ?>
		</c-tab>
	<?php }
	/**
	 * Add error message.
	 *
	 * @param string $error_msg Error message.
	 */
	public static function add_error( $error_msg ) {
		self::$errors[] = $error_msg;
	}

	/**
	 * Display error messages.
	 */
	public static function show_errors() {
		if ( count( self::$errors ) > 0 ) {
			foreach ( self::$errors as $error ) {
				echo '<div class="notice notice-error"><p><strong>' . esc_html( $error ) . '</strong></p></div>';
			}
		}
		self::$errors = array();
	}

	/**
	 * Admin menu page.
	 */
	// public function wplgip_admin_menu() {
	// if ( version_compare( WPL_COOKIE_CONSENT_VERSION, '2.9.1', '<' ) ) {
	// add_submenu_page( 'gdpr-cookie-consent', 'Integrations', __( 'Integrations', 'gdpr-cookie-consent' ), 'manage_options', 'gdpr-integrations', array( $this, 'wplgip_integrations' ) );
	// }
	// }

	/**
	 * MaxMind Geolocation integration menu callback.
	 */
	public function wplgip_integrations() {
		wp_enqueue_style( 'gdpr-cookie-consent' );
		$geo_options = get_option( 'wpl_geo_options' );
		if ( ! isset( $geo_options['database_prefix'] ) ) {
			$geo_options['maxmind_license_key'] = '';
			$geo_options['database_prefix']     = wp_generate_password( 32, false );
			update_option( 'wpl_geo_options', $geo_options );
		}
		$uploads_dir                       = wp_upload_dir();
		$geo_options['database_file_path'] = trailingslashit( $uploads_dir['basedir'] ) . 'gdpr_uploads/' . $geo_options['database_prefix'] . '-GeoLite2-City.mmdb';
		if ( isset( $_POST['maxmind_license_submit'] ) ) {
			check_admin_referer( 'wpl-update-maxmind-license' );
			$license_key = isset( $_POST['maxmind_license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['maxmind_license_key'] ) ) : '';
			$license_key = is_null( $license_key ) ? '' : $license_key;
			$license_key = trim( stripslashes( $license_key ) );
			if ( ! empty( $license_key ) ) {
				$license_key = $this->validate_maxmind_license_key( $license_key );
			}
			$geo_options['maxmind_license_key'] = $license_key;
			update_option( 'wpl_geo_options', $geo_options );
		}
		require_once plugin_dir_path( __FILE__ ) . 'views/integrations.php';
	}

	/**
	 * Validate license key and update geolocation database.
	 *
	 * @param string $license_key Maxmind lincense key.
	 * @return string|void
	 */
	public function validate_maxmind_license_key( $license_key ) {
		// Check the license key by attempting to download the Geolocation database.
		if ( '' === $license_key ) {
			update_option( 'wpl_pro_maxmind_integrated', '1' );
			return;
		}
		$tmp_database_path = $this->download_maxmind_database( $license_key );
		if ( is_wp_error( $tmp_database_path ) ) {
			self::add_error( $tmp_database_path->get_error_message() );
			update_option( 'wpl_pro_maxmind_integrated', '1' );
			return;
		}
		return $this->update_maxmind_database( $tmp_database_path, $license_key );
	}

	/**
	 * Update geolocation database.
	 *
	 * @param null   $new_database_path New database path.
	 * @param string $license_key License key.
	 * @return string|void
	 */
	public function update_maxmind_database( $new_database_path = null, $license_key = '' ) {
		$uploads_dir = wp_upload_dir();
		$geo_options = get_option( 'wpl_geo_options' );
		if ( isset( $geo_options['database_prefix'] ) && ! empty( $geo_options['database_prefix'] ) ) {
			$target_database_path = trailingslashit( $uploads_dir['basedir'] ) . 'gdpr_uploads/' . $geo_options['database_prefix'] . '-GeoLite2-City.mmdb';
		}
		// Allow us to easily interact with the filesystem.
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
		global $wp_filesystem;

		// If there's no database path, we can't store the database.
		if ( empty( $target_database_path ) ) {
			update_option( 'wpl_pro_maxmind_integrated', '1' );
			return;
		}

		if ( $wp_filesystem->exists( $target_database_path ) ) {
			$wp_filesystem->delete( $target_database_path );
		}

		if ( isset( $new_database_path ) ) {
			$tmp_database_path = $new_database_path;
		} else {
			// We can't download a database if there's no license key configured.
			if ( empty( $license_key ) ) {
				update_option( 'wpl_pro_maxmind_integrated', '1' );
				return;
			}

			$tmp_database_path = $this->download_database( $license_key );
			if ( is_wp_error( $tmp_database_path ) ) {
				self::add_error( $tmp_database_path->get_error_message() );
				update_option( 'wpl_pro_maxmind_integrated', '1' );
				return;
			}
		}

		// Move the new database into position.
		$wp_filesystem->move( $tmp_database_path, $target_database_path, true );
		$wp_filesystem->delete( dirname( $tmp_database_path ) );
		update_option( 'wpl_pro_maxmind_integrated', '2' );
		return $license_key;
	}

	/**
	 * Download geolocation database.
	 *
	 * @param string $license_key License key.
	 * @return string|WP_Error
	 */
	public function download_maxmind_database( $license_key ) {
		$download_uri = add_query_arg(
			array(
				'edition_id'  => 'GeoLite2-City',
				'license_key' => rawurlencode( $this->wplgip_clean( $license_key ) ),
				'suffix'      => 'tar.gz',
			),
			'https://download.maxmind.com/app/geoip_download'
		);

		// Needed for the download_url call right below.
		require_once ABSPATH . 'wp-admin/includes/file.php';

		$tmp_archive_path = download_url( esc_url_raw( $download_uri ) );
		if ( is_wp_error( $tmp_archive_path ) ) {
			// Transform the error into something more informative.
			$error_data = $tmp_archive_path->get_error_data();
			if ( isset( $error_data['code'] ) ) {
				switch ( $error_data['code'] ) {
					case 401:
						return new WP_Error(
							'wplgip_maxmind_license_key',
							__( 'The MaxMind license key is invalid. If you have recently created this key, you may need to wait for it to become active.', 'gdpr-cookie-consent' )
						);
				}
			}

			return new WP_Error( 'wplgip_maxmind_database_download', __( 'Failed to download the MaxMind database.', 'gdpr-cookie-consent' ) );
		}

		// Extract the database from the archive.
		try {
			$file = new PharData( $tmp_archive_path );

			$tmp_database_path = trailingslashit( dirname( $tmp_archive_path ) ) . trailingslashit( $file->current()->getFilename() ) . 'GeoLite2-City.mmdb';

			$file->extractTo(
				dirname( $tmp_archive_path ),
				trailingslashit( $file->current()->getFilename() ) . 'GeoLite2-City.mmdb',
				true
			);
		} catch ( Exception $exception ) {
			return new WP_Error( 'wplgip_maxmind_database_archive', $exception->getMessage() );
		} finally {
			// Remove the archive since we only care about a single file in it.
			unlink( $tmp_archive_path );
		}

		return $tmp_database_path;
	}

	/**
	 * Settings for Cookies About message under General Tab.
	 *
	 * @since 3.0.0
	 */
	public function wplgip_cookie_usage_for_general() {
		$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
		?>
		<tr valign="top" gdpr_frm_tgl-id="gdpr_usage_option" gdpr_frm_tgl-val="gdpr">
			<th scope="row"><label for="is_eu_on_field"><?php esc_attr_e( 'Show only for EU visitors', 'gdpr-cookie-consent' ); ?></label></th>
			<td>
				<input type="radio" id="is_eu_on_field_yes" name="is_eu_on_field" class="styled wpl_bar_on" value="true" <?php echo ( true === $the_options['is_eu_on'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
				<input type="radio" id="is_eu_on_field_no" name="is_eu_on_field" class="styled" value="false" <?php echo ( false === $the_options['is_eu_on'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
				<span class="gdpr_form_help"><?php esc_attr_e( 'GDPR message will be displayed to only EU visitors if enabled.', 'gdpr-cookie-consent' ); ?>
			</td>
		</tr>
		<tr valign="top" gdpr_frm_tgl-id="gdpr_usage_option" gdpr_frm_tgl-val="ccpa">
			<th scope="row"><label for="is_ccpa_on_field"><?php esc_attr_e( 'Show only for CCPA visitors', 'gdpr-cookie-consent' ); ?></label></th>
			<td>
				<input type="radio" id="is_ccpa_on_field_yes" name="is_ccpa_on_field" class="styled wpl_bar_on" value="true" <?php echo ( true === $the_options['is_ccpa_on'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
				<input type="radio" id="is_ccpa_on_field_no" name="is_ccpa_on_field" class="styled" value="false" <?php echo ( false === $the_options['is_ccpa_on'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
				<span class="gdpr_form_help"><?php esc_attr_e( 'CCPA message will be displayed to only CCPA visitors if enabled.', 'gdpr-cookie-consent' ); ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Returns IP address of the user.
	 *
	 * @since 3.0.0
	 * @return string
	 *
	 * @phpcs:disable
	 */
	public function wplgip_get_user_ip() {
		if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		} elseif( isset($_SERVER['HTTP_CF_CONNECTING_IP']) ) {
            $ipaddress = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		} elseif ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		} elseif ( isset( $_SERVER['HTTP_FORWARDED'] ) ) {
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		} else {
			$ipaddress = 'UNKNOWN';
		}
		return $ipaddress;
	}

	/**
	 * Check the IP for eu country.
	 *
	 * @since 3.0.0
	 * @return bool
	 * @phpcs:enable
	 */
	public function wpl_is_eu_country() {
		$uploads_dir   = wp_upload_dir();
		$geo_options   = get_option( 'wpl_geo_options' );
		$database_path = '';
		if ( isset( $geo_options['database_prefix'] ) && ! empty( $geo_options['database_prefix'] ) ) {
			$database_path = trailingslashit( $uploads_dir['basedir'] ) . 'gdpr_uploads/' . $geo_options['database_prefix'] . '-GeoLite2-City.mmdb';
		}
		$user_ip      = $this->wplgip_get_user_ip();
		$country_code = '';
		if ( $user_ip && 'UNKNOWN' !== $user_ip && ! empty( $database_path ) ) {
			try {
				$reader = new Reader( $database_path );
				try {
					$record       = $reader->city( $user_ip );
					$country_code = $record->country->isoCode;
				} catch ( \GeoIp2\Exception\AddressNotFoundException $e ) {
					return false;
				}
			} catch ( \MaxMind\Db\Reader\InvalidDatabaseException $e ) {
				return false;
			}
			if ( in_array( $country_code, Gdpr_Cookie_Consent::get_eu_countries(), true ) ) {
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * Check the IP for ccpa country.
	 *
	 * @since 3.0.0
	 * @return bool
	 *
	 * @phpcs:enable
	 */
	public function wpl_is_ccpa_country() {
		$uploads_dir   = wp_upload_dir();
		$geo_options   = get_option( 'wpl_geo_options' );
		$database_path = '';
		if ( isset( $geo_options['database_prefix'] ) && ! empty( $geo_options['database_prefix'] ) ) {
			$database_path = trailingslashit( $uploads_dir['basedir'] ) . 'gdpr_uploads/' . $geo_options['database_prefix'] . '-GeoLite2-City.mmdb';
		}
		$user_ip      = $this->wplgip_get_user_ip();
		$country_code = '';
		$state_code   = '';
		if ( $user_ip && 'UNKNOWN' !== $user_ip && ! empty( $database_path ) ) {
			try {
				$reader = new Reader( $database_path );
				try {
					$record        = $reader->city( $user_ip );
					$country_code  = $record->country->isoCode;
					$sub_divisions = $record->subdivisions;
					if ( $sub_divisions && isset( $sub_divisions[0] ) ) {
						$state_code = $sub_divisions[0]->isoCode;
					}
				} catch ( \GeoIp2\Exception\AddressNotFoundException $e ) {
					return false;
				}
			} catch ( \MaxMind\Db\Reader\InvalidDatabaseException $e ) {
				return false;
			}
			if ( in_array( $country_code, Gdpr_Cookie_Consent::get_ccpa_countries(), true ) && 'CA' === $state_code ) {
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * Clean variables.
	 *
	 * @param string $var variable.
	 * @return array|string
	 */
	public function wplgip_clean( $var ) {
		if ( is_array( $var ) ) {
			return array_map( 'wplgip_clean', $var );
		} else {
			return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
		}
	}
}
new Gdpr_Cookie_Consent_Geo_Ip();
