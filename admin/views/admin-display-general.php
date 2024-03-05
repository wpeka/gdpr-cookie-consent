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
$general_sub_tab = array(
	'settings-general'   => __( 'General', 'gdpr-cookie-consent' ),
	'show-again-general' => __( 'Show Again Tab', 'gdpr-cookie-consent' ),
	'other-general'      => __( 'Other', 'gdpr-cookie-consent' ),
);
$general_sub_tab = apply_filters( 'gdprcookieconsent_general_sub_tabs', $general_sub_tab );
?>
<div class="gdpr-cookie-consent-tab-content" data-id="<?php echo esc_attr( $target_id ); ?>">
	<ul class="gdpr_sub_tab">
		<?php foreach ( $general_sub_tab as $key => $value ) : ?>
			<li data-target="<?php echo esc_html( $key ); ?>"><a><?php echo esc_html( $value ); ?></a></li>
		<?php endforeach; ?>
	</ul>
	<div class="gdpr_sub_tab_container">
		<div class="gdpr_sub_tab_content" data-id="settings-general" style="display:block;">
			<p></p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="is_on_field"><?php esc_attr_e( 'Cookie Bar is currently', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="is_on_field_yes" name="is_on_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['is_on'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="is_on_field_no" name="is_on_field" class="styled" value="false" <?php echo ( false === $the_options['is_on'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="cookie_usage_for_field"><?php esc_attr_e( 'Cookie Bar Usage for', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="cookie_usage_for_field" class="vvv_combobox gdpr_form_toggle gdpr_tab_form_toggle gdpr_nav_form_toggle" gdpr_frm_tgl-target="gdpr_usage_option" gdpr_tab_frm_tgl-target="gdpr_usage_option">
							<?php $this->print_combobox_options( $this->get_cookie_usage_for_options(), $the_options['cookie_usage_for'] ); ?>
						</select>
					</td>
				</tr>
				<?php
				do_action( 'gdpr_module_settings_cookie_usage_for' );
				?>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_usage_option" gdpr_frm_tgl-val="ccpa">
					<th scope="row"><label for="is_ccpa_iab_on_field"><?php esc_attr_e( 'Enable IAB Transparency and Consent Framework (TCF)', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="is_ccpa_iab_on_field_yes" name="is_ccpa_iab_on_field" class="styled wpl_bar_on" value="true" <?php echo ( true === $the_options['is_ccpa_iab_on'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="is_ccpa_iab_on_field_no" name="is_ccpa_iab_on_field" class="styled" value="false" <?php echo ( false === $the_options['is_ccpa_iab_on'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
						<span class="gdpr_form_help"><?php esc_attr_e( 'Compatibility for the customization of advertising tracking preferences in case of CCPA.', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_usage_option" gdpr_frm_tgl-val="gdpr">
					<th scope="row"><label for="bar_heading_text_field"><?php esc_attr_e( 'Message Heading', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="bar_heading_text_field" value="<?php echo esc_attr( $the_options['bar_heading_text'] ); ?>" />
						<span class="gdpr_form_help"><?php esc_attr_e( 'Leave it blank, If you do not need a heading', 'gdpr-cookie-consent' ); ?></span>
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_usage_option" gdpr_frm_tgl-val="eprivacy">
					<th scope="row"><label for="notify_message_eprivacy_field"><?php esc_attr_e( 'Message', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<textarea id="notify_message_eprivacy_field" name="notify_message_eprivacy_field" class="vvv_textbox"><?php echo wp_kses( apply_filters( 'format_to_edit', stripslashes( $the_options['notify_message_eprivacy'] ) ), Gdpr_Cookie_Consent::gdpr_allowed_html(), Gdpr_Cookie_Consent::gdpr_allowed_protocols() ); ?></textarea>
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_usage_option" gdpr_frm_tgl-val="gdpr">
					<th scope="row"><label for="notify_message_field"><?php esc_attr_e( 'GDPR Message', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<textarea id="notify_message_field" name="notify_message_field" class="vvv_textbox"><?php echo wp_kses( apply_filters( 'format_to_edit', stripslashes( $the_options['notify_message'] ) ), Gdpr_Cookie_Consent::gdpr_allowed_html(), Gdpr_Cookie_Consent::gdpr_allowed_protocols() ); ?></textarea>
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_usage_option" gdpr_frm_tgl-val="ccpa">
					<th scope="row"><label for="notify_message_ccpa_field"><?php esc_attr_e( 'CCPA Message', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<textarea id="notify_message_ccpa_field" name="notify_message_ccpa_field" class="vvv_textbox"><?php echo wp_kses( apply_filters( 'format_to_edit', stripslashes( $the_options['notify_message_ccpa'] ) ), Gdpr_Cookie_Consent::gdpr_allowed_html(), Gdpr_Cookie_Consent::gdpr_allowed_protocols() ); ?></textarea>
					</td>
				</tr>
				<?php
				// general settings form fields for module.
				do_action( 'gdpr_module_settings_general' );
				?>
			</table>
		</div>
		<div class="gdpr_sub_tab_content" data-id="show-again-general" gdpr_tab_frm_tgl-id="gdpr_usage_option" gdpr_tab_frm_tgl-val="gdpr" gdpr_tab_frm_tgl-val1="eprivacy">
			<p></p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="show_again_field"><?php esc_attr_e( 'Show Again Tab', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="show_again_field_yes" name="show_again_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['show_again'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="show_again_field_no" name="show_again_field" class="styled" value="false" <?php echo ( false === $the_options['show_again'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top" >
					<th scope="row"><label for="show_again_position_field"><?php esc_attr_e( 'Show Again Tab Position', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="show_again_position_field" class="vvv_combobox">
							<?php
							if ( 'left' === $the_options['show_again_position'] ) {
								?>
								<option value="left" selected="selected"><?php echo esc_attr__( 'Left', 'gdpr-cookie-consent' ); ?></option>
								<option value="right"><?php echo esc_attr__( 'Right', 'gdpr-cookie-consent' ); ?></option>
							<?php } else { ?>
								<option value="left"><?php echo esc_attr__( 'Left', 'gdpr-cookie-consent' ); ?></option>
								<option value="right" selected="selected"><?php echo esc_attr__( 'Right', 'gdpr-cookie-consent' ); ?></option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="show_again_margin_field"><?php esc_attr_e( 'Show Again Tab Margin (in percent)', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="number" step="1" min="0" max="100" name="show_again_margin_field" value="<?php echo esc_html( stripslashes( $the_options['show_again_margin'] ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="show_again_text_field"><?php esc_attr_e( 'Show Again Text', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="show_again_text_field" value="<?php echo esc_html( stripslashes( $the_options['show_again_text'] ) ); ?>" />
					</td>
				</tr>
				<?php
				// general settings form fields for module.
				do_action( 'gdpr_module_show_again_general' );
				?>
			</table>
		</div>
		<div class="gdpr_sub_tab_content" data-id="other-general">
			<p></p>
			<table class="form-table">
				<?php
				do_action( 'gdpr_module_before_other_general' );
				?>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_usage_option" gdpr_frm_tgl-val="gdpr">
					<th scope="row"><label for="is_ticked_field"><?php esc_attr_e( 'Autotick for Non-Necessary Cookies', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="is_ticked_field_yes" name="is_ticked_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['is_ticked'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="is_ticked_field_no" name="is_ticked_field" class="styled" value="false" <?php echo ( false === $the_options['is_ticked'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_usage_option" gdpr_frm_tgl-val="gdpr" gdpr_frm_tgl-val1="eprivacy">
					<th scope="row"><label for="auto_hide_field"><?php esc_attr_e( 'Auto Hide (Accept)', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="auto_hide_field_yes" gdpr_frm_tgl-target="gdpr_auto_hide" name="auto_hide_field" class="styled gdpr_bar_on gdpr_form_toggle" value="true" <?php echo ( true === $the_options['auto_hide'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="auto_hide_field_no" gdpr_frm_tgl-target="gdpr_auto_hide" name="auto_hide_field" class="styled gdpr_form_toggle" value="false" <?php echo ( false === $the_options['auto_hide'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_auto_hide" gdpr_frm_tgl-val="true">
					<th scope="row"><label for="auto_hide_delay_field"><?php esc_attr_e( 'Auto Hide Delay (in Milliseconds)', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="number" step="1000" min="5000" max="60000" name="auto_hide_delay_field" value="<?php echo esc_html( stripslashes( $the_options['auto_hide_delay'] ) ); ?>" />
						<span class="gdpr_form_help"><?php esc_attr_e( 'Specify milliseconds e.g. 5000 = 5 seconds', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_usage_option" gdpr_frm_tgl-val="gdpr" gdpr_frm_tgl-val1="eprivacy">
					<th scope="row"><label for="auto_scroll_field"><?php esc_attr_e( 'Auto Scroll (Accept)', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="auto_scroll_field_yes" gdpr_frm_tgl-target="gdpr_auto_scroll" name="auto_scroll_field" class="styled gdpr_form_toggle gdpr_bar_on" value="true" <?php echo ( true === $the_options['auto_scroll'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="auto_scroll_field_no" gdpr_frm_tgl-target="gdpr_auto_scroll" name="auto_scroll_field" class="styled gdpr_form_toggle" value="false" <?php echo ( false === $the_options['auto_scroll'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
						<span class="gdpr_form_help"><?php esc_attr_e( 'Use this option with discretion especially if you serve EU', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_auto_scroll" gdpr_frm_tgl-val="true">
					<th scope="row"><label for="auto_scroll_offset_field"><?php esc_attr_e( 'Auto Scroll Offset (in percent)', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="number" step="1" min="10" max="100" name="auto_scroll_offset_field" value="<?php echo esc_html( stripslashes( $the_options['auto_scroll_offset'] ) ); ?>" />
						<span class="gdpr_form_help"><?php esc_attr_e( 'Consent will be assumed after user scrolls more than the specified page height', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_usage_option" gdpr_frm_tgl-val="gdpr" gdpr_frm_tgl-val1="eprivacy">
					<th scope="row"><label for="auto_scroll_reload_field"><?php esc_attr_e( 'Reload after Scroll Accept', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="auto_scroll_reload_yes" name="auto_scroll_reload_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['auto_scroll_reload'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="auto_scroll_reload_no" name="auto_scroll_reload_field" class="styled" value="false" <?php echo ( false === $the_options['auto_scroll_reload'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="accept_reload_field"><?php esc_attr_e( 'Reload after Accept', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="accept_reload_yes" name="accept_reload_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['accept_reload'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="accept_reload_no" name="accept_reload_field" class="styled" value="false" <?php echo ( false === $the_options['accept_reload'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_usage_option" gdpr_frm_tgl-val="gdpr" gdpr_frm_tgl-val1="eprivacy">
					<th scope="row"><label for="decline_reload_field"><?php esc_attr_e( 'Reload after Decline', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="decline_reload_yes" name="decline_reload_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['decline_reload'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="decline_reload_no" name="decline_reload_field" class="styled" value="false" <?php echo ( false === $the_options['decline_reload'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<?php if ( ! $is_pro_active ) :
					$the_options = get_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
					?>
					<tr valign="top" gdpr_frm_tgl-id="gdpr_usage_option" gdpr_frm_tgl-val="gdpr" gdpr_frm_tgl-val1="eprivacy">
						<th scope="row"><label for="restrict_posts_field"><?php esc_attr_e( 'Restrict Pages and/or Posts', 'gdpr-cookie-consent' ); ?></label></th>
						<td>
							<select style="width: 25rem;" class="restrict_posts" name="restrict_posts_field[]" id="restrict_posts" multiple="multiple" data-allow-clear=true>
							<?php
							$get_pages = get_pages();
							$get_posts = get_posts();
							echo "<optgroup label='Pages'>";
							if ( is_array( $get_pages ) ) {
								foreach ( $get_pages as $item ) {
									echo '<option value=' . esc_html( $item->ID ) . '' . selected( in_array( $item->ID, $the_options['restrict_posts'] ) ) . '>' . esc_html( $item->post_title ) . '</option>'; // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
								}
							}
							echo '<optgroup />';
							echo "<optgroup label='Posts'>";
							if ( is_array( $get_posts ) ) {
								foreach ( $get_posts as $item ) {
									echo '<option value=' . esc_html( $item->ID ) . '' . selected( in_array( $item->ID, $the_options['restrict_posts'] ) ) . '>' . esc_html( $item->post_title ) . '</option>'; // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
								}
							}
							echo '<optgroup />';
							?>
							</select>
							<span class="gdpr_form_help"><?php esc_attr_e( 'Restrict Pages and/or Posts during scanning of your website for cookies.', 'gdpr-cookie-consent' ); ?>
						</td>
					</tr>
					<?php endif ?>
					<?php
					// general settings form fields for module.
					do_action( 'gdpr_module_other_general' );
					?>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_usage_option" gdpr_frm_tgl-val="gdpr" gdpr_frm_tgl-val1="eprivacy">
					<th scope="row"><label for="cookie_expiry_field"><?php esc_attr_e( 'Cookie Expiry', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="cookie_expiry_field" class="vvv_combobox">
							<?php $this->print_combobox_options( $this->get_cookie_expiry_options(), $the_options['cookie_expiry'] ); ?>
						</select>
						<span class="gdpr_form_help"><?php esc_attr_e( 'The amount of time that the cookie should be stored for.', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="delete_on_deactivation_field"><?php esc_attr_e( 'Delete Plugin data on Deactivation', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="delete_on_deactivation_field_yes" name="delete_on_deactivation_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['delete_on_deactivation'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="delete_on_deactivation_field_no" name="delete_on_deactivation_field" class="styled" value="false" <?php echo ( false === $the_options['delete_on_deactivation'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
						<span class="gdpr_form_help"><?php esc_attr_e( 'Enable if you want all plugin data to be deleted on deactivation.', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="show_credits_field"><?php esc_attr_e( 'Show Credits', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="show_credits_field_yes" name="show_credits_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['show_credits'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="show_credits_field_no" name="show_credits_field" class="styled" value="false" <?php echo ( false === $the_options['show_credits'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
			</table>
		</div>
		<?php do_action( 'gdpr_settings_general_tab' ); ?>
	</div>
	<?php
	require 'admin-display-save-button.php';
	?>
</div>
