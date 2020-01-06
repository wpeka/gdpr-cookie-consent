<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://club.wpeka.com
 * @since      1.0
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$gdpr_admin_view_path = plugin_dir_path( GDPR_COOKIE_CONSENT_PLUGIN_FILENAME ) . 'admin/views/';
?>
<script type="text/javascript">
	var gdpr_settings_success_message='<?php echo esc_attr__( 'Settings updated.', 'gdpr-cookie-consent' ); ?>';
	var gdpr_settings_error_message='<?php echo esc_attr__( 'Unable to update Settings.', 'gdpr-cookie-consent' ); ?>';
</script>
<div style="clear:both;"></div>
<div class="wrap">
	<div class="nav-tab-wrapper wp-clearfix gdpr-cookie-consent-tab-head">
		<?php
		$tab_head_arr = array(
			'gdpr-cookie-consent-general' => __( 'General', 'gdpr-cookie-consent' ),
			'gdpr-cookie-consent-design'  => __( 'Design', 'gdpr-cookie-consent' ),
			'gdpr-cookie-consent-buttons' => __( 'Buttons', 'gdpr-cookie-consent' ),
		);
		Gdpr_Cookie_Consent::gdpr_generate_settings_tabhead( $tab_head_arr );
		?>
	</div>
	<div class="gdpr_settings_left">
	<div class="gdpr-cookie-consent-tab-container">
		<?php
		$display_views_a = array(
			'gdpr-cookie-consent-general' => 'admin-display-general.php',
			'gdpr-cookie-consent-design'  => 'admin-display-design.php',
			'gdpr-cookie-consent-buttons' => 'admin-display-buttons.php',
		);
		?>
		<form method="post" action="
		<?php
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			echo esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );}
		?>
		" id="gdpr_settings_form">
			<input type="hidden" name="gdpr_update_action" value="" id="gdpr_update_action" />
			<?php
			// Set nonce.
			if ( function_exists( 'wp_nonce_field' ) ) {
				wp_nonce_field( 'gdprcookieconsent-update-' . GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
			}
			foreach ( $display_views_a as $target_id => $value ) {
				$display_view = $gdpr_admin_view_path . $value;
				if ( file_exists( $display_view ) ) {
					include $display_view;
				}
			}
			// settings form fields for module.
			do_action( 'gdpr_module_settings_form' );
			?>
		</form>
	</div>
</div>
	<div class="gdpr_settings_right">
		<?php
		require plugin_dir_path( GDPR_COOKIE_CONSENT_PLUGIN_FILENAME ) . 'admin/views/admin-display-promotional.php';
		?>
	</div>
</div>
