<?php
/**
 * Provide a admin area view for the cookie list.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://club.wpeka.com
 * @since      1.0
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin/modules/cookie-custom/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div style="clear:both;"></div>
<div class="wrap">
	<?php self::show_errors(); ?>
	<div class="gdpr_settings_left">
		<form method="post">
			<?php
			if ( function_exists( 'wp_nonce_field' ) ) {
				wp_nonce_field( 'wpl-update-maxmind-license' );
			}
			?>
			<div class="gdpr-cookie-consent-tab-container">
				<div class="gdpr_sub_tab_container">
					<div class="gdpr_sub_tab_content" style="display:block;">
						<h1><?php esc_attr_e( 'MaxMind Geolocation Integration', 'gdpr-cookie-consent' ); ?></h1>
						<?php self::show_errors(); ?>
						<span class="gdpr_form_help"><?php esc_attr_e( 'An integration for utilizing MaxMind to do Geolocation lookups.', 'gdpr-cookie-consent' ); ?></span>
						<table class="form-table">
							<tr valign="top">
								<th scope="row"><label for="maxmind_license_key"><?php esc_attr_e( 'MaxMind License Key', 'gdpr-cookie-consent' ); ?></label></th>
								<td>
									<input type="password" name="maxmind_license_key" value="<?php echo esc_attr( $geo_options['maxmind_license_key'] ); ?>" />
									<span class="gdpr_form_help"><?php esc_attr_e( 'The key that will be used when dealing with MaxMind Geolocation services.', 'gdpr-cookie-consent' ); ?></span>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="database_file_path"><?php esc_attr_e( 'Database File Path', 'gdpr-cookie-consent' ); ?></label></th>
								<td>
									<input readonly type="text" name="database_file_path" value="<?php echo esc_attr( $geo_options['database_file_path'] ); ?>" />
									<span class="gdpr_form_help"><?php esc_attr_e( 'The location that the MaxMind database should be stored. By default, the integration will automatically save the database here.', 'gdpr-cookie-consent' ); ?></span>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div style="clear: both;"></div>
				<div class="gdpr-plugin-toolbar bottom">
					<div class="left">
					</div>
					<div class="right">
						<input type="submit" class="button-primary" name="maxmind_license_submit" style="float:right;" value="<?php esc_attr_e( 'Save', 'gdpr-cookie-consent' ); ?>">
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="gdpr_settings_right">
		<?php
		if ( defined( 'GDPR_COOKIE_CONSENT_PLUGIN_FILENAME' ) ) {
			require plugin_dir_path( GDPR_COOKIE_CONSENT_PLUGIN_FILENAME ) . 'admin/views/admin-display-promotional.php';
		}
		?>
	</div>
</div>
