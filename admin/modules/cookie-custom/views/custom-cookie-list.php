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
}?>
<?php
if ( isset( $post_cookie_list ) && $post_cookie_list['total'] > 0 ) :
	if ( isset( $post_cookie_list['data'] ) && ! empty( $post_cookie_list['data'] ) ) :
		foreach ( $post_cookie_list['data'] as $cookies_arr ) {
			?>
			<div class="form-table post-cookie-list">
				<div class="left">
					<span class="cookie-text"><?php echo esc_attr( strtoupper( substr( stripslashes( $cookies_arr['name'] ), 0, 1 ) ) ); ?></span>
				</div>
				<div class="right">
					<div class="right-grid-1">
						<input type="hidden" name="id_<?php echo esc_attr( $cookies_arr['id_gdpr_cookie_post_cookies'] ); ?>" value="<?php echo esc_attr( $cookies_arr['id_gdpr_cookie_post_cookies'] ); ?>">
						<div class="input-box"><input type="text" name="cookie_name_field_<?php echo esc_attr( $cookies_arr['id_gdpr_cookie_post_cookies'] ); ?>" value="<?php echo esc_attr( stripslashes( $cookies_arr['name'] ) ); ?>" placeholder="<?php esc_attr_e( 'Cookie Name', 'gdpr-cookie-consent' ); ?>" /></div>
						<div class="input-box"><input type="text" name="cookie_domain_field_<?php echo esc_attr( $cookies_arr['id_gdpr_cookie_post_cookies'] ); ?>" value="<?php echo esc_attr( $cookies_arr['domain'] ); ?>" placeholder="<?php esc_attr_e( 'Cookie Domain', 'gdpr-cookie-consent' ); ?>" /></div>
					</div>
					<div class="right-grid-2">
						<div class="input-box"><select name="cookie_category_field_<?php echo esc_attr( $cookies_arr['id_gdpr_cookie_post_cookies'] ); ?>" class="vvv_combobox">
							<?php Gdpr_Cookie_Consent_Cookie_Custom::print_combobox_options( Gdpr_Cookie_Consent_Cookie_Custom::get_categories(), $cookies_arr['category_id'] ); ?>
							</select></div>
						<div class="input-box"><select name="cookie_type_field_<?php echo esc_attr( $cookies_arr['id_gdpr_cookie_post_cookies'] ); ?>" class="vvv_combobox cookie-type-field">
							<?php Gdpr_Cookie_Consent_Cookie_Custom::print_combobox_options( Gdpr_Cookie_Consent_Cookie_Custom::get_types(), $cookies_arr['type'] ); ?>
							</select></div>
						<div class="input-box"><input type="text" name="cookie_duration_field_<?php echo esc_attr( $cookies_arr['id_gdpr_cookie_post_cookies'] ); ?>" value="<?php echo esc_attr( stripslashes( $cookies_arr['duration'] ) ); ?>" class="cookie-duration-field" placeholder="<?php esc_attr_e( 'Cookie Duration', 'gdpr-cookie-consent' ); ?>" /></div>
					</div>
					<div class="right-grid-3">
						<div class="input-box"><textarea name="cookie_description_field_<?php echo esc_attr( $cookies_arr['id_gdpr_cookie_post_cookies'] ); ?>" class="vvv_textbox" placeholder="<?php esc_attr_e( 'Cookie Purpose', 'gdpr-cookie-consent' ); ?>" ><?php echo esc_attr( stripslashes( $cookies_arr['description'] ) ); ?></textarea></div>
					</div>
					<a style="text-decoration:underline;cursor:pointer" class="primary pull-right gdpr_delete_post_cookie"><?php esc_attr_e( 'Delete Cookie', 'gdpr-cookie-consent' ); ?></a>
				</div>
			</div>
	<?php }
endif;
endif;?>
