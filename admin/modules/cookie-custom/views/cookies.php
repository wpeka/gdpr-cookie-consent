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
}
$cookie_sub_tab                   = array();
$cookie_sub_tab                   = apply_filters( 'gdprcookieconsent_cookie_sub_tabs', $cookie_sub_tab );
$cookie_sub_tab['custom-cookies'] = __( 'Custom Cookies', 'gdpr-cookie-consent' );
?>
	<ul class="gdpr_cookie_sub_tab">
		<?php foreach ( $cookie_sub_tab as $key => $value ) : ?>
			<li data-target="<?php echo esc_html( $key ); ?>"><a><?php echo esc_html( $value ); ?></a></li>
		<?php endforeach; ?>
	</ul>
	<div class="gdpr_cookie_sub_tab_container">
		<div class="gdpr_cookie_sub_tab_content" data-id="custom-cookies">
			<div class="gdpr_postbar" style="display:none;">
				<a style="text-decoration:underline;cursor:pointer;" class="primary gdpr_add_cookie"><?php esc_attr_e( 'Add New Cookie', 'gdpr-cookie-consent' ); ?></a>
			</div>
			<div class="form-table add-cookie">
				<input type="hidden" name="gdpr_addcookie" value="1">
				<div class="left">
					<span class="cookie-text">C</span>
				</div>
				<div class="right">
					<div class="right-grid-1">
						<div class="input-box"><input type="text" name="cookie_name_field" value="" placeholder="<?php esc_attr_e( 'Cookie Name', 'gdpr-cookie-consent' ); ?>"/></div>
						<div class="input-box"><input type="text" name="cookie_domain_field" value="" placeholder="<?php esc_attr_e( 'Cookie Domain', 'gdpr-cookie-consent' ); ?>" /></div>
					</div>
					<div class="right-grid-2">
						<div class="input-box"><select name="cookie_category_field" class="vvv_combobox">
								<?php Gdpr_Cookie_Consent_Cookie_Custom::print_combobox_options( Gdpr_Cookie_Consent_Cookie_Custom::get_categories(), '' ); ?>
							</select></div>
						<div class="input-box"><select name="cookie_type_field" class="vvv_combobox cookie-type-field">
								<?php Gdpr_Cookie_Consent_Cookie_Custom::print_combobox_options( Gdpr_Cookie_Consent_Cookie_Custom::get_types(), 'HTTP' ); ?>
							</select></div>
						<div class="input-box"><input type="text" name="cookie_duration_field" class="cookie-duration-field" value="" placeholder="<?php esc_attr_e( 'Cookie Duration (in days)', 'gdpr-cookie-consent' ); ?>"/></div>
					</div>
					<div class="right-grid-3">
						<div class="input-box"><textarea name="cookie_description_field" class="vvv_textbox" placeholder="<?php esc_attr_e( 'Cookie Purpose', 'gdpr-cookie-consent' ); ?>"></textarea></div>
					</div>
					<a class="primary pull-right gdpr_delete_cookie"><?php esc_attr_e( 'Cancel', 'gdpr-cookie-consent' ); ?></a>
					<a class="primary pull-right gdpr_save_cookie"><?php esc_attr_e( 'Save', 'gdpr-cookie-consent' ); ?></a>
				</div>
			</div>
			<div id="post_cookie_list">
				<?php require plugin_dir_path( __FILE__ ) . 'custom-cookie-list.php'; ?>
			</div>
		</div>
		<?php
		// cookielist settings form fields for module.
		do_action( 'gdpr_module_settings_cookielist' );
		?>
	</div>
