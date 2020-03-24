<?php
/**
 * Provide a admin area view for the buttons tab.
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
$buttons_sub_tab = array(
	'accept-button'    => __( 'Accept Button', 'gdpr-cookie-consent' ),
	'reject-button'    => __( 'Decline Button', 'gdpr-cookie-consent' ),
	'settings-button'  => __( 'Settings Button', 'gdpr-cookie-consent' ),
	'read-more-button' => __( 'Read More Link', 'gdpr-cookie-consent' ),
	'confirm-button'   => __( 'Confirm Button', 'gdpr-cookie-consent' ),
	'cancel-button'    => __( 'Cancel Button', 'gdpr-cookie-consent' ),
	'donotsell-button' => __( 'Optout Link', 'gdpr-cookie-consent' ),
);
$buttons_sub_tab = apply_filters( 'gdprcookieconsent_buttons_sub_tabs', $buttons_sub_tab );
?>
<div class="gdpr-cookie-consent-tab-content" data-id="<?php echo esc_attr( $target_id ); ?>">
	<ul class="gdpr_sub_tab">
		<?php foreach ( $buttons_sub_tab as $key => $value ) : ?>
			<li data-target="<?php echo esc_html( $key ); ?>"><a><?php echo esc_html( $value ); ?></a></li>
		<?php endforeach; ?>
	</ul>
	<div class="gdpr_sub_tab_container">
		<div class="gdpr_sub_tab_content" data-id="accept-button" gdpr_tab_frm_tgl-id="gdpr_usage_option" gdpr_tab_frm_tgl-val="gdpr">
			<p></p>
			<p><?php esc_attr_e( 'This button/link can be customized to either simply close the cookie bar, or follow a link. You can also customize the colors and styles, and show it as a link or a button.', 'gdpr-cookie-consent' ); ?></p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="button_accept_is_on_field"><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="button_accept_is_on_field_yes" name="button_accept_is_on_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['button_accept_is_on'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="button_accept_is_on_field_no" name="button_accept_is_on_field" class="styled" value="false" <?php echo ( false === $the_options['button_accept_is_on'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_accept_text_field"><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_accept_text_field" value="<?php echo esc_html( stripslashes( $the_options['button_accept_text'] ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_accept_link_color_field"><?php esc_attr_e( 'Text color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_accept_link_color_field" id="gdpr-color-link-button-accept" value="<?php echo esc_attr( $the_options['button_accept_link_color'] ); ?>" class="gdpr-color-field" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_accept_as_button_field"><?php esc_attr_e( 'Show as', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" gdpr_frm_tgl-target="gdpr_accept_type" id="button_accept_as_button_field_yes" name="button_accept_as_button_field" class="styled gdpr_form_toggle" value="true" <?php echo ( true === $the_options['button_accept_as_button'] ) ? ' checked="checked"' : ' '; ?> /> <?php esc_attr_e( 'Button', 'gdpr-cookie-consent' ); ?>
						<input type="radio" gdpr_frm_tgl-target="gdpr_accept_type" id="button_accept_as_button_field_no" name="button_accept_as_button_field" class="styled gdpr_form_toggle" value="false" <?php echo ( false === $the_options['button_accept_as_button'] ) ? ' checked="checked"' : ''; ?>  /> <?php esc_attr_e( 'Link', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_accept_type" gdpr_frm_tgl-val="true">
					<th scope="row"><label for="button_accept_button_color_field"><?php esc_attr_e( 'Background color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_accept_button_color_field" id="gdpr-color-btn-button-accept" value="<?php echo esc_attr( $the_options['button_accept_button_color'] ); ?>" class="gdpr-color-field" />
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_accept_type" gdpr_frm_tgl-val="true">
					<th scope="row"><label for="button_accept_button_size_field"><?php esc_attr_e( 'Size', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="button_accept_button_size_field" class="vvv_combobox">
							<?php $this->print_combobox_options( $this->get_button_sizes(), $the_options['button_accept_button_size'] ); ?>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_accept_action_field"><?php esc_attr_e( 'Action', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="button_accept_action_field" id="gdpr-plugin-button-accept-action" class="vvv_combobox gdpr_form_toggle" gdpr_frm_tgl-target="gdpr_accept_action">
							<?php $this->print_combobox_options( $this->get_js_actions(), $the_options['button_accept_action'] ); ?>
						</select>
					</td>
				</tr>
				<tr valign="top" class="gdpr-plugin-row" gdpr_frm_tgl-id="gdpr_accept_action" gdpr_frm_tgl-val="CONSTANT_OPEN_URL">
					<th scope="row"><label for="button_accept_url_field"><?php esc_attr_e( 'URL', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_accept_url_field" id="button_accept_url_field" value="<?php echo esc_attr( $the_options['button_accept_url'] ); ?>" />
						<span class="gdpr_form_help"><?php esc_attr_e( 'Button will only link to URL if Action = Open URL', 'gdpr-cookie-consent' ); ?></span>
					</td>
				</tr>

				<tr valign="top" class="gdpr-plugin-row" gdpr_frm_tgl-id="gdpr_accept_action" gdpr_frm_tgl-val="CONSTANT_OPEN_URL">
					<th scope="row"><label for="button_accept_new_win_field"><?php esc_attr_e( 'Open URL in new window?', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="button_accept_new_win_field_yes" name="button_accept_new_win_field" class="styled" value="true" <?php echo ( true === $the_options['button_accept_new_win'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'Yes', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="button_accept_new_win_field_no" name="button_accept_new_win_field" class="styled" value="false" <?php echo ( false === $the_options['button_accept_new_win'] ) ? ' checked="checked"' : ''; ?> /> <?php esc_attr_e( 'No', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
			</table><!-- end custom button -->
		</div>
		<div class="gdpr_sub_tab_content" data-id="reject-button" gdpr_tab_frm_tgl-id="gdpr_usage_option" gdpr_tab_frm_tgl-val="gdpr">
			<p></p>
			<table class="form-table" >
				<tr valign="top">
					<th scope="row"><label for="button_decline_is_on_field"><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="button_decline_is_on_field_yes" name="button_decline_is_on_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['button_decline_is_on'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="button_decline_is_on_field_no" name="button_decline_is_on_field" class="styled" value="false" <?php echo ( false === $the_options['button_decline_is_on'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_decline_text_field"><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_decline_text_field" value="<?php echo esc_html( stripslashes( $the_options['button_decline_text'] ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_decline_link_color_field"><?php esc_attr_e( 'Text color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_decline_link_color_field" id="gdpr-color-link-button-decline" value="<?php echo esc_attr( $the_options['button_decline_link_color'] ); ?>" class="gdpr-color-field" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_decline_as_button_field"><?php esc_attr_e( 'Show as', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="button_decline_as_button_field_yes" name="button_decline_as_button_field" class="styled gdpr_form_toggle" gdpr_frm_tgl-target="gdpr_reject_type" value="true" <?php echo ( true === $the_options['button_decline_as_button'] ) ? ' checked="checked"' : ' '; ?>  /> <?php esc_attr_e( 'Button', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="button_decline_as_button_field_no" name="button_decline_as_button_field" class="styled gdpr_form_toggle" gdpr_frm_tgl-target="gdpr_reject_type" value="false" <?php echo ( false === $the_options['button_decline_as_button'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'Link', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_reject_type" gdpr_frm_tgl-val="true">
					<th scope="row"><label for="button_decline_button_color_field"><?php esc_attr_e( 'Background color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_decline_button_color_field" id="gdpr-color-btn-button-decline" value="<?php echo esc_attr( $the_options['button_decline_button_color'] ); ?>" class="gdpr-color-field" />
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_reject_type" gdpr_frm_tgl-val="true">
					<th scope="row"><label for="button_decline_button_size_field"><?php esc_attr_e( 'Size', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="button_decline_button_size_field" class="vvv_combobox">
							<?php $this->print_combobox_options( $this->get_button_sizes(), $the_options['button_decline_button_size'] ); ?>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_decline_action_field"><?php esc_attr_e( 'Action', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="button_decline_action_field" id="gdpr-plugin-button-decline-action" class="vvv_combobox gdpr_form_toggle" gdpr_frm_tgl-target="gdpr_reject_action">
							<?php
								$action_list = $this->get_js_actions();
								$action_list[ __( 'Close Header', 'gdpr-cookie-consent' ) ] = '#cookie_action_close_header_reject';
								$this->print_combobox_options( $action_list, $the_options['button_decline_action'] );
							?>
						</select>
					</td>
				</tr>
				<tr valign="top" class="gdpr-plugin-row" gdpr_frm_tgl-id="gdpr_reject_action" gdpr_frm_tgl-val="CONSTANT_OPEN_URL">
					<th scope="row"><label for="button_decline_url_field"><?php esc_attr_e( 'URL', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_decline_url_field" id="button_decline_url_field" value="<?php echo esc_attr( $the_options['button_decline_url'] ); ?>" />
						<span class="gdpr_form_help"><?php esc_attr_e( 'Button will only link to URL if Action = Open URL', 'gdpr-cookie-consent' ); ?></span>
					</td>
				</tr>

				<tr valign="top" class="gdpr-plugin-row" gdpr_frm_tgl-id="gdpr_reject_action" gdpr_frm_tgl-val="CONSTANT_OPEN_URL">
					<th scope="row"><label for="button_decline_new_win_field"><?php esc_attr_e( 'Open URL in new window?', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="button_decline_new_win_field_yes" name="button_decline_new_win_field" class="styled" value="true" <?php echo ( true === $the_options['button_decline_new_win'] ) ? ' checked="checked"' : ''; ?>  /><?php esc_attr_e( 'Yes', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="button_decline_new_win_field_no" name="button_decline_new_win_field" class="styled" value="false" <?php echo ( false === $the_options['button_decline_new_win'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'No', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
			</table><!-- end custom button -->
		</div>
		<div class="gdpr_sub_tab_content" data-id="settings-button" gdpr_tab_frm_tgl-id="gdpr_usage_option" gdpr_tab_frm_tgl-val="gdpr">
			<p></p>
			<table class="form-table" >
				<tr valign="top">
					<th scope="row"><label for="button_settings_is_on_field"><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="button_settings_is_on_field_yes" name="button_settings_is_on_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['button_settings_is_on'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="button_settings_is_on_field_no" name="button_settings_is_on_field" class="styled" value="false" <?php echo ( false === $the_options['button_settings_is_on'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_settings_text_field"><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_settings_text_field" value="<?php echo esc_html( stripslashes( $the_options['button_settings_text'] ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_settings_link_color_field"><?php esc_attr_e( 'Text color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_settings_link_color_field" id="gdpr-color-link-button-settings" value="<?php echo esc_attr( $the_options['button_settings_link_color'] ); ?>" class="gdpr-color-field" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_settings_as_button_field"><?php esc_attr_e( 'Show as', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="button_settings_as_button_field_yes" name="button_settings_as_button_field" class="styled gdpr_form_toggle" gdpr_frm_tgl-target="gdpr_settings_type" value="true" <?php echo ( true === $the_options['button_settings_as_button'] ) ? ' checked="checked"' : ' '; ?>  /> <?php esc_attr_e( 'Button', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="button_settings_as_button_field_no" name="button_settings_as_button_field" class="styled gdpr_form_toggle" gdpr_frm_tgl-target="gdpr_settings_type" value="false" <?php echo ( false === $the_options['button_settings_as_button'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'Link', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_settings_type" gdpr_frm_tgl-val="true">
					<th scope="row"><label for="button_settings_button_color_field"><?php esc_attr_e( 'Background color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_settings_button_color_field" id="gdpr-color-btn-button-settings" value="<?php echo esc_attr( $the_options['button_settings_button_color'] ); ?>" class="gdpr-color-field" />
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_settings_type" gdpr_frm_tgl-val="true">
					<th scope="row"><label for="button_settings_button_size_field"><?php esc_attr_e( 'Size', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="button_settings_button_size_field" class="vvv_combobox">
							<?php $this->print_combobox_options( $this->get_button_sizes(), $the_options['button_settings_button_size'] ); ?>
						</select>
					</td>
				</tr>
				<tr valign="top" class="gdpr-plugin-row" gdpr_frm_tgl-id="gdpr_cookiebar_as" gdpr_frm_tgl-val="banner">
					<th scope="row"><label for="button_settings_as_popup_field"><?php esc_attr_e( 'Cookie Settings Layout', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="button_settings_as_popup_field" class="vvv_combobox">
							<?php
							if ( $the_options['button_settings_as_popup'] ) {
								?>
								<option value="true" selected="selected"><?php echo esc_attr__( 'Popup', 'gdpr-cookie-consent' ); ?></option>
								<option value="false"><?php echo esc_attr__( 'Extended Banner', 'gdpr-cookie-consent' ); ?></option>
							<?php } else { ?>
								<option value="true"><?php echo esc_attr__( 'Popup', 'gdpr-cookie-consent' ); ?></option>
								<option value="false" selected="selected"><?php echo esc_attr__( 'Extended Banner', 'gdpr-cookie-consent' ); ?></option>
								<?php
							}
							?>
						</select>
					</td>
				</tr>
			</table><!-- end custom button -->
		</div>
		<div class="gdpr_sub_tab_content" data-id="read-more-button" gdpr_tab_frm_tgl-id="gdpr_usage_option" gdpr_tab_frm_tgl-val="gdpr">
			<p></p>
			<p><?php esc_attr_e( 'This button/link can be used to provide a link out to your Privacy & Cookie Policy. You can customize it any way you like.', 'gdpr-cookie-consent' ); ?></p>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="button_readmore_is_on_field"><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="button_readmore_is_on_field_yes" name="button_readmore_is_on_field" class="styled gdpr_bar_on" value="true" <?php echo ( true === $the_options['button_readmore_is_on'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'On', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="button_readmore_is_on_field_no" name="button_readmore_is_on_field" class="styled" value="false" <?php echo ( false === $the_options['button_readmore_is_on'] ) ? ' checked="checked" ' : ''; ?> /><?php esc_attr_e( 'Off', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_readmore_text_field"><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_readmore_text_field" value="<?php echo esc_html( stripslashes( $the_options['button_readmore_text'] ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_readmore_link_color_field"><?php esc_attr_e( 'Text color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_readmore_link_color_field" id="gdpr-color-link-button-readmore" value="<?php echo esc_attr( $the_options['button_readmore_link_color'] ); ?>" class="gdpr-color-field" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_readmore_as_button_field"><?php esc_attr_e( 'Show as', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="button_readmore_as_button_field_yes" name="button_readmore_as_button_field" class="styled gdpr_form_toggle" gdpr_frm_tgl-target="gdpr_readmore_type" value="true" <?php echo ( true === $the_options['button_readmore_as_button'] ) ? ' checked="checked"' : ''; ?>  /> <?php esc_attr_e( 'Button', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="button_readmore_as_button_field_no" name="button_readmore_as_button_field" class="styled gdpr_form_toggle" gdpr_frm_tgl-target="gdpr_readmore_type" value="false" <?php echo ( false === $the_options['button_readmore_as_button'] ) ? ' checked="checked"' : ''; ?> /> <?php esc_attr_e( 'Link', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_readmore_type" gdpr_frm_tgl-val="true">
					<th scope="row"><label for="button_readmore_button_color_field"><?php esc_attr_e( 'Background color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_readmore_button_color_field" id="gdpr-color-btn-button-readmore" value="<?php echo esc_attr( $the_options['button_readmore_button_color'] ); ?>" class="gdpr-color-field" />
					</td>
				</tr>
				<tr valign="top" gdpr_frm_tgl-id="gdpr_readmore_type" gdpr_frm_tgl-val="true">
					<th scope="row"><label for="button_readmore_button_size_field"><?php esc_attr_e( 'Size', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="button_readmore_button_size_field" class="vvv_combobox">
							<?php $this->print_combobox_options( $this->get_button_sizes(), $the_options['button_readmore_button_size'] ); ?>
						</select>
					</td>
				</tr>
				<tr valign="top" class="gdpr-plugin-row">
					<th scope="row"><label for="button_readmore_url_field"><?php esc_attr_e( 'URL', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_readmore_url_field" id="button_readmore_url_field" value="<?php echo esc_attr( $the_options['button_readmore_url'] ); ?>" />
					</td>
				</tr>

				<tr valign="top" class="gdpr-plugin-row">
					<th scope="row"><label for="button_decline_new_win_field"><?php esc_attr_e( 'Open URL in new window?', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="button_readmore_new_win_field_yes" name="button_readmore_new_win_field" class="styled" value="true" <?php echo ( true === $the_options['button_readmore_new_win'] ) ? ' checked="checked"' : ''; ?>  /><?php esc_attr_e( 'Yes', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="button_readmore_new_win_field_no" name="button_readmore_new_win_field" class="styled" value="false" <?php echo ( false === $the_options['button_readmore_new_win'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'No', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
			</table><!-- end custom button -->
		</div>
		<div class="gdpr_sub_tab_content" data-id="confirm-button" gdpr_tab_frm_tgl-id="gdpr_usage_option" gdpr_tab_frm_tgl-val="ccpa">
			<p></p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="button_confirm_text_field"><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_confirm_text_field" value="<?php echo esc_html( stripslashes( $the_options['button_confirm_text'] ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_confirm_link_color_field"><?php esc_attr_e( 'Text color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_confirm_link_color_field" id="gdpr-color-link-button-confirm" value="<?php echo esc_attr( $the_options['button_confirm_link_color'] ); ?>" class="gdpr-color-field" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_confirm_button_color_field"><?php esc_attr_e( 'Background color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_confirm_button_color_field" id="gdpr-color-btn-button-confirm" value="<?php echo esc_attr( $the_options['button_confirm_button_color'] ); ?>" class="gdpr-color-field" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_confirm_button_size_field"><?php esc_attr_e( 'Size', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="button_confirm_button_size_field" class="vvv_combobox">
							<?php $this->print_combobox_options( $this->get_button_sizes(), $the_options['button_confirm_button_size'] ); ?>
						</select>
					</td>
				</tr>
			</table><!-- end custom button -->
		</div>
		<div class="gdpr_sub_tab_content" data-id="cancel-button" gdpr_tab_frm_tgl-id="gdpr_usage_option" gdpr_tab_frm_tgl-val="ccpa">
			<p></p>
			<table class="form-table" >
				<tr valign="top">
					<th scope="row"><label for="button_cancel_text_field"><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_cancel_text_field" value="<?php echo esc_html( stripslashes( $the_options['button_cancel_text'] ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_cancel_link_color_field"><?php esc_attr_e( 'Text color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_cancel_link_color_field" id="gdpr-color-link-button-cancel" value="<?php echo esc_attr( $the_options['button_cancel_link_color'] ); ?>" class="gdpr-color-field" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_cancel_button_color_field"><?php esc_attr_e( 'Background color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_cancel_button_color_field" id="gdpr-color-btn-button-cancel" value="<?php echo esc_attr( $the_options['button_cancel_button_color'] ); ?>" class="gdpr-color-field" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_cancel_button_size_field"><?php esc_attr_e( 'Size', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="button_cancel_button_size_field" class="vvv_combobox">
							<?php $this->print_combobox_options( $this->get_button_sizes(), $the_options['button_cancel_button_size'] ); ?>
						</select>
					</td>
				</tr>
			</table><!-- end custom button -->
		</div>
		<div class="gdpr_sub_tab_content" data-id="donotsell-button" gdpr_tab_frm_tgl-id="gdpr_usage_option" gdpr_tab_frm_tgl-val="ccpa">
			<p></p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="button_donotsell_text_field"><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_donotsell_text_field" value="<?php echo esc_html( stripslashes( $the_options['button_donotsell_text'] ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_donotsell_link_color_field"><?php esc_attr_e( 'Text color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_donotsell_link_color_field" id="gdpr-color-link-button-donotsell" value="<?php echo esc_attr( $the_options['button_donotsell_link_color'] ); ?>" class="gdpr-color-field" />
					</td>
				</tr>
			</table><!-- end custom button -->
		</div>
	</div>
	<?php
	require 'admin-display-save-button.php';
	?>
</div>
