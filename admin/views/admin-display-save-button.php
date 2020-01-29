<?php
/**
 * Provide a admin area view for the save button.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://club.wpeka.com
 * @since      1.0
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin/views
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<div style="clear: both;"></div>
<div class="gdpr-plugin-toolbar bottom">
	<?php if ( isset( $need_submit_btn ) && 1 === $need_submit_btn ) { ?>
		<div class="left">
			<a class="button-primary update_admin_cookies" style="float:left;" ><?php esc_attr_e( 'Save All Cookies', 'gdpr-cookie-consent' ); ?></a>
			<span class="spinner" style="margin-top:9px"></span>
		</div>
		<div class="right"></div>
		<?php
	} elseif ( ! isset( $need_submit_btn ) || 2 !== $need_submit_btn ) {
		?>
		<div class="left"></div>
		<div class="right">
			<input type="submit" name="update_admin_settings_form" value="<?php esc_attr_e( 'Update', 'gdpr-cookie-consent' ); ?>" class="button-primary" style="float:right;" onClick="return gdpr_store_settings_btn_click(this.name)" />
			<span class="spinner" style="margin-top:9px"></span>
		</div>
	<?php } ?>
</div>
