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
	'accept-button'    => __( 'Accept Cookies Button', 'gdpr-cookie-consent' ),
	'reject-button'    => __( 'Decline Button', 'gdpr-cookie-consent' ),
	'read-more-button' => __( 'Read More Link', 'gdpr-cookie-consent' ),
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

		<div class="gdpr_sub_tab_content" data-id="accept-button" style="display:block;">
			<h3><code>[wpl_cookie_button]</code></h3>
			<p><?php esc_attr_e( 'This button/link can be customized to either simply close the cookie bar, or follow a link. You can also customize the colors and styles, and show it as a link or a button.', 'gdpr-cookie-consent' ); ?></p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="button_1_text_field"><?php esc_attr_e( 'Accept All Cookies Text', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_1_text_field" value="<?php echo esc_html( stripslashes( $the_options['button_1_text'] ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_1_selected_text_field"><?php esc_attr_e( 'Accept Selected Cookies Text', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_1_selected_text_field" value="<?php echo esc_html( stripslashes( $the_options['button_1_selected_text'] ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_1_link_color_field"><?php esc_attr_e( 'Text color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<?php
							echo '<input type="text" name="button_1_link_color_field" id="gdpr-color-link-button-1" value="' . esc_attr( $the_options['button_1_link_color'] ) . '" class="gdpr-color-field" />';
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_1_as_button_field"><?php esc_attr_e( 'Show as', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" gdpr_frm_tgl-target="gdpr_accept_type" id="button_1_as_button_field_yes" name="button_1_as_button_field" class="styled gdpr_form_toggle" value="true" <?php echo ( true === $the_options['button_1_as_button'] ) ? ' checked="checked"' : ' '; ?> /> <?php esc_attr_e( 'Button', 'gdpr-cookie-consent' ); ?>
						<input type="radio" gdpr_frm_tgl-target="gdpr_accept_type" id="button_1_as_button_field_no" name="button_1_as_button_field" class="styled gdpr_form_toggle" value="false" <?php echo ( false === $the_options['button_1_as_button'] ) ? ' checked="checked"' : ''; ?>  /> <?php esc_attr_e( 'Link', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top" class="gdpr-indent-15" gdpr_frm_tgl-id="gdpr_accept_type" gdpr_frm_tgl-val="true">
					<th scope="row"><label for="button_1_button_color_field"><?php esc_attr_e( 'Background color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<?php
						echo '<input type="text" name="button_1_button_color_field" id="gdpr-color-btn-button-1" value="' . esc_attr( $the_options['button_1_button_color'] ) . '" class="gdpr-color-field" />';
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_1_action_field"><?php esc_attr_e( 'Action', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="button_1_action_field" id="gdpr-plugin-button-1-action" class="vvv_combobox gdpr_form_toggle" gdpr_frm_tgl-target="gdpr_accept_action">
							<?php $this->print_combobox_options( $this->get_js_actions(), $the_options['button_1_action'] ); ?>
						</select>
					</td>
				</tr>
				<tr valign="top" class="gdpr-plugin-row gdpr-indent-15" gdpr_frm_tgl-id="gdpr_accept_action" gdpr_frm_tgl-val="CONSTANT_OPEN_URL">
					<th scope="row"><label for="button_1_url_field"><?php esc_attr_e( 'URL', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_1_url_field" id="button_1_url_field" value="<?php echo esc_attr( $the_options['button_1_url'] ); ?>" />
						<span class="gdpr_form_help"><?php esc_attr_e( 'Button will only link to URL if Action = Open URL', 'gdpr-cookie-consent' ); ?></span>
					</td>
				</tr>

				<tr valign="top" class="gdpr-plugin-row gdpr-indent-15" gdpr_frm_tgl-id="gdpr_accept_action" gdpr_frm_tgl-val="CONSTANT_OPEN_URL">
					<th scope="row"><label for="button_1_new_win_field"><?php esc_attr_e( 'Open URL in new window?', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="button_1_new_win_field_yes" name="button_1_new_win_field" class="styled" value="true" <?php echo ( true === $the_options['button_1_new_win'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'Yes', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="button_1_new_win_field_no" name="button_1_new_win_field" class="styled" value="false" <?php echo ( false === $the_options['button_1_new_win'] ) ? ' checked="checked"' : ''; ?> /> <?php esc_attr_e( 'No', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_1_button_size_field"><?php esc_attr_e( 'Size', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="button_1_button_size_field" class="vvv_combobox">
							<?php $this->print_combobox_options( $this->get_button_sizes(), $the_options['button_1_button_size'] ); ?>
						</select>
					</td>
				</tr>
			</table><!-- end custom button -->
		</div>

		<div class="gdpr_sub_tab_content" data-id="reject-button">
			<h3><code>[wpl_cookie_decline]</code></h3>
			<table class="form-table" >
				<tr valign="top">
					<th scope="row"><label for="button_3_text_field"><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_3_text_field" value="<?php echo esc_html( stripslashes( $the_options['button_3_text'] ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_3_link_color_field"><?php esc_attr_e( 'Text color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<?php
							echo '<input type="text" name="button_3_link_color_field" id="gdpr-color-link-button-3" value="' . esc_attr( $the_options['button_3_link_color'] ) . '" class="gdpr-color-field" />';
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_3_as_button_field"><?php esc_attr_e( 'Show as', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="button_3_as_button_field_yes" name="button_3_as_button_field" class="styled gdpr_form_toggle" gdpr_frm_tgl-target="gdpr_reject_type" value="true" <?php echo ( true === $the_options['button_3_as_button'] ) ? ' checked="checked"' : ' '; ?>  /> <?php esc_attr_e( 'Button', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="button_3_as_button_field_no" name="button_3_as_button_field" class="styled gdpr_form_toggle" gdpr_frm_tgl-target="gdpr_reject_type" value="false" <?php echo ( false === $the_options['button_3_as_button'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'Link', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top" class="gdpr-indent-15" gdpr_frm_tgl-id="gdpr_reject_type" gdpr_frm_tgl-val="true">
					<th scope="row"><label for="button_3_button_color_field"><?php esc_attr_e( 'Background color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<?php
						echo '<input type="text" name="button_3_button_color_field" id="gdpr-color-btn-button-3" value="' . esc_attr( $the_options['button_3_button_color'] ) . '" class="gdpr-color-field" />';
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_3_action_field"><?php esc_attr_e( 'Action', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="button_3_action_field" id="gdpr-plugin-button-3-action" class="vvv_combobox gdpr_form_toggle" gdpr_frm_tgl-target="gdpr_reject_action">
							<?php
								$action_list = $this->get_js_actions();
								$action_list[ __( 'Close Header', 'gdpr-cookie-consent' ) ] = '#cookie_action_close_header_reject';
								$this->print_combobox_options( $action_list, $the_options['button_3_action'] );
							?>
						</select>
					</td>
				</tr>
				<tr valign="top" class="gdpr-plugin-row" gdpr_frm_tgl-id="gdpr_reject_action" gdpr_frm_tgl-val="CONSTANT_OPEN_URL">
					<th scope="row"><label for="button_3_url_field"><?php esc_attr_e( 'URL', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_3_url_field" id="button_3_url_field" value="<?php echo esc_attr( $the_options['button_3_url'] ); ?>" />
						<span class="gdpr_form_help"><?php esc_attr_e( 'Button will only link to URL if Action = Open URL', 'gdpr-cookie-consent' ); ?></span>
					</td>
				</tr>

				<tr valign="top" class="gdpr-plugin-row" gdpr_frm_tgl-id="gdpr_reject_action" gdpr_frm_tgl-val="CONSTANT_OPEN_URL">
					<th scope="row"><label for="button_3_new_win_field"><?php esc_attr_e( 'Open URL in new window?', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="button_3_new_win_field_yes" name="button_3_new_win_field" class="styled" value="true" <?php echo ( true === $the_options['button_3_new_win'] ) ? ' checked="checked"' : ''; ?>  /><?php esc_attr_e( 'Yes', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="button_3_new_win_field_no" name="button_3_new_win_field" class="styled" value="false" <?php echo ( false === $the_options['button_3_new_win'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'No', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_3_button_size_field"><?php esc_attr_e( 'Size', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="button_3_button_size_field" class="vvv_combobox">
							<?php $this->print_combobox_options( $this->get_button_sizes(), $the_options['button_3_button_size'] ); ?>
						</select>
					</td>
				</tr>
			</table><!-- end custom button -->
		</div>
		<div class="gdpr_sub_tab_content" data-id="read-more-button">
			<h3><code>[wpl_cookie_link]</code></h3>
			<p><?php esc_attr_e( 'This button/link can be used to provide a link out to your Privacy & Cookie Policy. You can customize it any way you like.', 'gdpr-cookie-consent' ); ?></p>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="button_2_text_field"><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_2_text_field" value="<?php echo esc_html( stripslashes( $the_options['button_2_text'] ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_2_link_color_field"><?php esc_attr_e( 'Text color', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<?php
							echo '<input type="text" name="button_2_link_color_field" id="gdpr-color-link-button-2" value="' . esc_attr( $the_options['button_2_link_color'] ) . '" class="gdpr-color-field" />';
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="button_2_as_button_field"><?php esc_attr_e( 'Show as', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="button_2_as_button_field_yes" name="button_2_as_button_field" class="styled gdpr_form_toggle" gdpr_frm_tgl-target="gdpr_readmore_type" value="true" <?php echo ( true === $the_options['button_2_as_button'] ) ? ' checked="checked"' : ''; ?>  /> <?php esc_attr_e( 'Button', 'gdpr-cookie-consent' ); ?>

						<input type="radio" id="button_2_as_button_field_no" name="button_2_as_button_field" class="styled gdpr_form_toggle" gdpr_frm_tgl-target="gdpr_readmore_type" value="false" <?php echo ( false === $the_options['button_2_as_button'] ) ? ' checked="checked"' : ''; ?> /> <?php esc_attr_e( 'Link', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>

				<tr valign="top" class="gdpr-plugin-row">
					<th scope="row"><label for="button_2_url_field"><?php esc_attr_e( 'URL', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="text" name="button_2_url_field" id="button_2_url_field" value="<?php echo esc_attr( $the_options['button_2_as_button'] ); ?>" />
					</td>
				</tr>

				<tr valign="top" class="gdpr-plugin-row">
					<th scope="row"><label for="button_3_new_win_field"><?php esc_attr_e( 'Open URL in new window?', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<input type="radio" id="button_2_new_win_field_yes" name="button_2_new_win_field" class="styled" value="true" <?php echo ( true === $the_options['button_2_new_win'] ) ? ' checked="checked"' : ''; ?>  /><?php esc_attr_e( 'Yes', 'gdpr-cookie-consent' ); ?>
						<input type="radio" id="button_2_new_win_field_no" name="button_2_new_win_field" class="styled" value="false" <?php echo ( false === $the_options['button_2_new_win'] ) ? ' checked="checked"' : ''; ?> /><?php esc_attr_e( 'No', 'gdpr-cookie-consent' ); ?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="button_2_button_size_field"><?php esc_attr_e( 'Size', 'gdpr-cookie-consent' ); ?></label></th>
					<td>
						<select name="button_2_button_size_field" class="vvv_combobox">
							<?php $this->print_combobox_options( $this->get_button_sizes(), $the_options['button_2_button_size'] ); ?>
						</select>
					</td>
				</tr>
			</table><!-- end custom button -->
		</div>

	</div>
	<?php
	require 'admin-display-save-button.php';
	?>
</div>
