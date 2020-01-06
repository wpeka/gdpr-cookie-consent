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
					<th scope="row"><label for="is_on_field"><?php esc_attr_e( 'Cookie Bar is currently', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="is_on_field_yes" name="is_on_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['is_on'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="is_on_field_no" name="is_on_field" class="styled" value="false" <?php echo ( false === $the_options['is_on'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="is_ticked_field"><?php esc_attr_e( 'Autotick for Non-Necessary Cookies', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="is_ticked_field_yes" name="is_ticked_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['is_ticked'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="is_ticked_field_no" name="is_ticked_field" class="styled" value="false" <?php echo ( false === $the_options['is_ticked'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="show_credits_field"><?php esc_attr_e( 'Show Credits', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="show_credits_field_yes" name="show_credits_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['show_credits'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="show_credits_field_no" name="show_credits_field" class="styled" value="false" <?php echo ( false === $the_options['show_credits'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="bar_heading_text_field"><?php esc_attr_e( 'Message Heading', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="bar_heading_text_field" value="<?php echo esc_attr( $the_options['bar_heading_text'] ); ?>" />
						<span class="gdpr_form_help"><?php esc_attr_e( 'Leave it blank, If you do not need a heading', 'gdpr-cookie-consent' ); ?>
				</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="notify_message_field"><?php esc_attr_e( 'Message', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<textarea name="notify_message_field" class="vvv_textbox"><?php echo wp_kses( apply_filters( 'format_to_edit', stripslashes( $the_options['notify_message'] ) ), Gdpr_Cookie_Consent::gdpr_allowed_html(), Gdpr_Cookie_Consent::gdpr_allowed_protocols() ); ?></textarea>
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
