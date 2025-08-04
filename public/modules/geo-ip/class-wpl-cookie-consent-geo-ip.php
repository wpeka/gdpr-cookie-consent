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
        } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && count( array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) )) > 0 ) {
			$xForwardedFor = $_SERVER['HTTP_X_FORWARDED_FOR'];
			$ipList = array_map('trim', explode(',', $xForwardedFor));

			$ipaddress = filter_var($ipList[0], FILTER_VALIDATE_IP);
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED'] ) && count( array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED'] ) )) > 0 ) {
			$xForwarded = $_SERVER['HTTP_X_FORWARDED'];
			$ipList = array_map('trim', explode(',', $xForwarded));

			$ipaddress = filter_var($ipList[0], FILTER_VALIDATE_IP);
		} elseif ( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) && count( array_map('trim', explode(',', $_SERVER['HTTP_FORWARDED_FOR'] ) )) > 0 ) {
			$forwardedFor = $_SERVER['HTTP_FORWARDED_FOR'];
			$ipList = array_map('trim', explode(',', $forwardedFor));

			$ipaddress = filter_var($ipList[0], FILTER_VALIDATE_IP);
		} elseif ( isset( $_SERVER['HTTP_FORWARDED'] ) && count( array_map('trim', explode(',', $_SERVER['HTTP_FORWARDED'] ) )) > 0 ) {
			$forwarded = $_SERVER['HTTP_FORWARDED'];
			$ipList = array_map('trim', explode(',', $forwarded));

			$ipaddress = filter_var($ipList[0], FILTER_VALIDATE_IP);
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
		//This product includes GeoLite2 data created by MaxMind, available from https://www.maxmind.com. The data is licensed under the Creative Commons Attribution-ShareAlike 4.0 International License.
		$database_path = trailingslashit( $uploads_dir['basedir'] ) . 'gdpr_uploads/GeoLite2-City.mmdb';
		
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
		
		$uploads_dir   = wp_upload_dir();
		//This product includes GeoLite2 data created by MaxMind, available from https://www.maxmind.com. The data is licensed under the Creative Commons Attribution-ShareAlike 4.0 International License.
		$database_path = trailingslashit( $uploads_dir['basedir'] ) . 'gdpr_uploads/GeoLite2-City.mmdb';
		
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