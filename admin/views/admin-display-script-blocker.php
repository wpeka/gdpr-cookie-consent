<?php
/**
 * Provide a admin area view for the buttons tab.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link  https://club.wpeka.com
 * @since 1.0
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin/views
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$script_blocker_sub_tab = array(
	'script-blocker-general' => __( 'General', 'gdpr-cookie-consent' ),
);
$script_blocker_sub_tab = apply_filters( 'gdprcookieconsent_script_blocker_sub_tabs', $script_blocker_sub_tab );
?>
<div class="gdpr-cookie-consent-tab-content" data-id="<?php echo esc_attr( $target_id ); ?>">
	<ul class="gdpr_sub_tab">
		<?php foreach ( $script_blocker_sub_tab as $key => $value ) : ?>
			<li data-target="<?php echo esc_html( $key ); ?>"><a><?php echo esc_html( $value ); ?></a></li>
		<?php endforeach; ?>
	</ul>
	<div class="gdpr_sub_tab_container">
		<div class="gdpr_sub_tab_content" data-id="script-blocker-general" style="display:block;">
			<p></p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="is_script_blocker_on_field"><?php esc_attr_e( 'Script Blocker is currently', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="is_script_blocker_on_field_yes" name="is_script_blocker_on_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['is_script_blocker_on'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="is_script_blocker_on_field_no" name="is_script_blocker_on_field" class="styled" value="false" <?php echo ( false === $the_options['is_script_blocker_on'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label><?php esc_attr_e( 'Custom scripts', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<span class="gdpr_form_help"><?php esc_attr_e( 'Enter non functional cookies javascript code here (for e.g. Google Analytics) to be used after the consent is accepted.', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="header_scripts_field"><?php esc_attr_e( 'Header Scripts', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<textarea id="header_scripts_field" name="header_scripts_field" class="vvv_textbox"><?php echo esc_html( htmlentities( stripslashes( $the_options['header_scripts'] ) ) ); ?></textarea>
						<span class="gdpr_form_help"><?php esc_attr_e( 'These scripts will be printed in the head section on all pages and/or posts.', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="body_scripts_field"><?php esc_attr_e( 'Body Scripts', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<textarea id="body_scripts_field" name="body_scripts_field" class="vvv_textbox"><?php echo esc_html( htmlentities( stripslashes( $the_options['body_scripts'] ) ) ); ?></textarea>
						<span class="gdpr_form_help"><?php esc_attr_e( 'These scripts will be printed just below the opening body tag on all pages and/or posts.', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="footer_scripts_field"><?php esc_attr_e( 'Footer Scripts', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<textarea id="footer_scripts_field" name="footer_scripts_field" class="vvv_textbox"><?php echo esc_html( htmlentities( stripslashes( $the_options['footer_scripts'] ) ) ); ?></textarea>
						<span class="gdpr_form_help"><?php esc_attr_e( 'These scripts will be printed above the closing body tag on all pages and/or posts.', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<?php
				// messagebar settings form fields for module.
				do_action( 'gdpr_module_settings_script_blocker' );
				?>
			</table>
		</div>
		<?php do_action( 'gdpr_settings_script_blocker_tab' ); ?>
	</div>
	<?php
	require 'admin-display-save-button.php';
	?>
</div>
