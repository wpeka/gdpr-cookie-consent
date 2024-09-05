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
		}
	}
	
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
		$user_ip      = $this->wplgip_get_user_ip();
		$country_code = '';
		if ( $user_ip && 'UNKNOWN' !== $user_ip) {
			try {
				$response_geolocation = wp_remote_post(
						Geolocation_API_URL,
						array(
							'body' => array(
								'user_ip' => $user_ip
							),
						)
					);
				if ( is_wp_error( $response_geolocation ) ) {
                    $country_code = '';
                }

			 	$response_status = wp_remote_retrieve_response_code( $response_geolocation );

				if ( 200 === $response_status ) {
					$country_code = json_decode( wp_remote_retrieve_body( $response_geolocation ) );
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
        return false;
	}

	/**
	 * Check the IP for country of user.
	 *
	 * @since 3.0.0
	 * @return bool
	 *
	 * @phpcs:enable
	 */
	public function wpl_is_selected_country() {
		
		$user_ip      = $this->wplgip_get_user_ip();
        error_log("userip".print_r($user_ip,true));
		$country_code = '';
		if ( $user_ip && 'UNKNOWN' !== $user_ip ) {
			try {
					$response_geolocation = wp_remote_post(
						Geolocation_API_URL,
						array(
							'body' => array(
								'user_ip' => $user_ip
							),
						)
					);
                    error_log("Response:". print_r($response_geolocation, true));
				if ( is_wp_error( $response_geolocation ) ) {
                    $country_code = '';
                }

			 	$response_status = wp_remote_retrieve_response_code( $response_geolocation );

				if ( 200 === $response_status ) {
					$country_code = json_decode( wp_remote_retrieve_body( $response_geolocation ) );
				}
				
			} catch ( \MaxMind\Db\Reader\InvalidDatabaseException $e ) {
				return false;
			}
            error_log("COUNTRY:".print_r($country_code,true));
			return $country_code;
		}
        return false;
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