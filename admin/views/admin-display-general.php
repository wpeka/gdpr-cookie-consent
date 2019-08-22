<?php
/**
 * Provide a admin area view for the general tab.
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
<div class="gdpr-cookie-consent-tab-content" data-id="<?php echo esc_attr( $target_id ); ?>">
	<div class="gdpr_sub_tab_container">
		<div class="gdpr_sub_tab_content" data-id="cookie-bar" style="display:block;">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="is_on_field"><?php esc_attr_e( 'Cookie Bar is currently:', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="is_on_field_yes" name="is_on_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['is_on'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="is_on_field_no" name="is_on_field" class="styled" value="false" <?php echo ( false === $the_options['is_on'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="is_ticked_field"><?php esc_attr_e( 'Autotick for Non-Necessary Cookies:', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="is_ticked_field_yes" name="is_ticked_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['is_ticked'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="is_ticked_field_no" name="is_ticked_field" class="styled" value="false" <?php echo ( false === $the_options['is_ticked'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="show_credits_field"><?php esc_attr_e( 'Show Credits:', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="show_credits_field_yes" name="show_credits_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['show_credits'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="show_credits_field_no" name="show_credits_field" class="styled" value="false" <?php echo ( false === $the_options['show_credits'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="notify_animate_hide_field"><?php esc_attr_e( 'On hide', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="notify_animate_hide_field" class="vvv_combobox">
							<?php
							if ( true === $the_options['notify_animate_hide'] ) {
								echo '<option value="true" selected="selected">' . esc_attr__( 'Animate', 'gdpr-cookie-consent' ) . '</option>';
								echo '<option value="false">' . esc_attr__( 'Disappear', 'gdpr-cookie-consent' ) . '</option>';
							} else {
								echo '<option value="true">' . esc_attr__( 'Animate', 'gdpr-cookie-consent' ) . '</option>';
								echo '<option value="false" selected="selected">' . esc_attr__( 'Disappear', 'gdpr-cookie-consent' ) . '</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<?php
				// general settings form fields for module.
				do_action( 'gdpr_module_settings_general' );
				?>
			</table>
		</div>
	</div>
	<?php
	require 'admin-display-save-button.php';
	?>
</div>
