<?php
/**
 * Provide a admin area view for the message bar tab.
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
	<table class="form-table">
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
				<?php
					echo '<textarea name="notify_message_field" class="vvv_textbox">';
					echo wp_kses( apply_filters( 'format_to_edit', stripslashes( $the_options['notify_message'] ) ), Gdpr_Cookie_Consent::gdpr_allowed_html(), Gdpr_Cookie_Consent::gdpr_allowed_protocols() ) . '</textarea>';
				?>
				<span class="gdpr_form_help"><?php esc_attr_e( 'Shortcodes allowed: see the Help tab', 'gdpr-cookie-consent' ); ?> <br /><em><?php esc_attr_e( 'Examples: "We use cookies on this website [wpl_cookie_button] to find out how to delete cookies [wpl_cookie_link]."', 'gdpr-cookie-consent' ); ?></em></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="background_field"><?php esc_attr_e( 'Cookie Bar Color', 'gdpr-cookie-consent' ); ?></label></th>
			<td>
				<?php
				echo '<input type="text" name="background_field" id="gdpr-color-background" value="' . esc_attr( $the_options['background'] ) . '" class="gdpr-color-field" data-default-color="#fff" />';
				?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="text_field"><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label></th>
			<td>
				<?php
				echo '<input type="text" name="text_field" id="gdpr-color-text" value="' . esc_attr( $the_options['text'] ) . '" class="gdpr-color-field" data-default-color="#000" />';
				?>
			</td>
		</tr>
		<?php
		// messagebar settings form fields for module.
		do_action( 'gdpr_module_settings_messagebar' );
		?>
	</table>

	<?php
	require 'admin-display-save-button.php';
	?>
</div>
