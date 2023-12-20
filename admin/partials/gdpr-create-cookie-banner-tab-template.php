<?php
/**
 * Provide a Wizard view for the admin.
 *
 * This file is used to markup the admin-facing aspects of the plugin (Wizard Page).
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin
 * @author Omendra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_pro = get_option( 'wpl_pro_active', false );

wp_enqueue_script( 'gdpr-cookie-consent' );
wp_enqueue_script( 'gdpr-cookie-consent-vue' );
wp_enqueue_script( 'gdpr-cookie-consent-mascot' );
wp_enqueue_style( 'gdpr-cookie-consent-select2' );
wp_enqueue_script( 'gdpr-cookie-consent-select2' );

wp_localize_script(
	'gdpr-cookie-consent-mascot',
	'mascot_obj',
	array(
		'is_pro'            => $is_pro,
		'documentation_url' => 'https://club.wpeka.com/docs/wp-cookie-consent/',
		'faq_url'           => 'https://club.wpeka.com/docs/wp-cookie-consent/',
		// 'support_url'       => $support_url,
		'upgrade_url'       => 'https://club.wpeka.com/product/wp-gdpr-cookie-consent/?utm_source=plugin&utm_medium=gdpr&utm_campaign=help-mascot_&utm_content=upgrade-to-pro',
	)
);

// Lock out non-admins.
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_attr__( 'You do not have sufficient permission to perform this operation', 'gdpr-cookie-consent' ) );
}
// Get options.
$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
// Check if form has been set.
if ( isset( $_POST['update_admin_settings_form'] ) || ( isset( $_POST['gdpr_settings_ajax_update'] ) ) ) {
	// Check nonce.
	check_admin_referer( 'gdprcookieconsent-update-' . GDPR_COOKIE_CONSENT_SETTINGS_FIELD );
	if ( 'update_admin_settings_form' === $_POST['gdpr_settings_ajax_update'] ) {
		// module settings saving hook.
		do_action( 'gdpr_module_save_settings' );
		// setting manually default value for restrict posts field.
		if ( ! isset( $_POST['restrict_posts_field'] ) ) {
			$_POST['restrict_posts_field'] = array();
		}
		foreach ( $the_options as $key => $value ) {
			if ( isset( $_POST[ $key . '_field' ] ) ) {
				// Store sanitised values only.
				$the_options[ $key ] = Gdpr_Cookie_Consent::gdpr_sanitise_settings( $key, wp_unslash( $_POST[ $key . '_field' ] ) ); // phpcs:ignore
			}
		}
		switch ( $the_options['cookie_bar_as'] ) {
			case 'banner':
				$the_options['template'] = $the_options['banner_template'];
				break;
			case 'popup':
				$the_options['template'] = $the_options['popup_template'];
				break;
			case 'widget':
				$the_options['template'] = $the_options['widget_template'];
				break;
		}
		$the_options = apply_filters( 'gdpr_module_after_save_settings', $the_options );
		update_option( GDPR_COOKIE_CONSENT_SETTINGS_FIELD, $the_options );
		echo '<div class="updated"><p><strong>' . esc_attr__( 'Settings Updated.', 'gdpr-cookie-consent' ) . '</strong></p></div>';
	}
}
if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) ) === 'xmlhttprequest' ) {
	exit();
}
if ( get_option( 'wpl_pro_active' ) && '1' === get_option( 'wpl_pro_active' ) && ( ! get_option( 'wpl_pro_version_number' ) || version_compare( get_option( 'wpl_pro_version_number' ), '2.9.0', '<' ) ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'partials/gdpr-cookie-consent-admin-display.php';
	return;
}
$settings        = Gdpr_Cookie_Consent::gdpr_get_settings();
$gdpr_policies   = self::get_cookie_usage_for_options();
$policies_length = count( $gdpr_policies );
$policy_keys     = array_keys( $gdpr_policies );
$policies        = array();
$is_pro_active   = get_option( 'wpl_pro_active' );
for ( $i = 0; $i < $policies_length; $i++ ) {
	$policies[ $i ] = array(
		'label' => $policy_keys[ $i ],
		'code'  => $gdpr_policies[ $policy_keys[ $i ] ],
	);
}
$cookie_durations        = self::get_cookie_expiry_options();
$cookie_durations_length = count( $cookie_durations );
$cookie_expiry_keys      = array_keys( $cookie_durations );
$cookie_expiry_options   = array();
for ( $i = 0; $i < $cookie_durations_length; $i++ ) {
	$cookie_expiry_options[ $i ] = array(
		'label' => $cookie_expiry_keys[ $i ],
		'code'  => $cookie_durations[ $cookie_expiry_keys[ $i ] ],
	);
}
$position_options           = array();
$position_options[0]        = array(
	'label' => 'Top',
	'code'  => 'top',
);
$position_options[1]        = array(
	'label' => 'Bottom',
	'code'  => 'bottom',
);
$widget_position_options    = array();
$widget_position_options[0] = array(
	'label' => 'Botton Left',
	'code'  => 'left',
);
$widget_position_options[1] = array(
	'label' => 'Bottom Right',
	'code'  => 'right',
);
$widget_position_options[2] = array(
	'label' => 'Top Left',
	'code'  => 'top_left',
);
$widget_position_options[3] = array(
	'label' => 'Top Right',
	'code'  => 'top_right',
);

$show_cookie_as_options       = array();
$show_cookie_as_options[0]    = array(
	'label' => 'Banner',
	'code'  => 'banner',
);
$show_cookie_as_options[1]    = array(
	'label' => 'Popup',
	'code'  => 'popup',
);
$show_cookie_as_options[2]    = array(
	'label' => 'Widget',
	'code'  => 'widget',
);
$show_language_as_options     = array();
$show_language_as_options[0]  = array(
	'label' => 'English',
	'code'  => 'en',
);
$show_language_as_options[1]  = array(
	'label' => 'French',
	'code'  => 'fr',
);
$show_language_as_options[2]  = array(
	'label' => 'Dutch',
	'code'  => 'nl',
);
$show_language_as_options[3]  = array(
	'label' => 'Bulgarian',
	'code'  => 'bg',
);
$show_language_as_options[4]  = array(
	'label' => 'Czech',
	'code'  => 'cs',
);
$show_language_as_options[5]  = array(
	'label' => 'Danish',
	'code'  => 'da',
);
$show_language_as_options[6]  = array(
	'label' => 'German',
	'code'  => 'de',
);
$show_language_as_options[7]  = array(
	'label' => 'Spanish',
	'code'  => 'es',
);
$show_language_as_options[8]  = array(
	'label' => 'Croatian',
	'code'  => 'hr',
);
$show_language_as_options[9]  = array(
	'label' => 'Icelandic',
	'code'  => 'is',
);
$show_language_as_options[10] = array(
	'label' => 'Slovenian',
	'code'  => 'sl',
);
// dropdown option for schedule scan.
$schedule_scan_options    = array();
$schedule_scan_options[0] = array(
	'label' => 'Never',
	'code'  => 'never',
);
$schedule_scan_options[1] = array(
	'label' => 'Only Once',
	'code'  => 'once',
);
$schedule_scan_options[2] = array(
	'label' => 'Monthly',
	'code'  => 'monthly',
);
// dropdown option for schedule scan day.
$schedule_scan_day_options = array();

for ( $day = 0; $day < 31; $day++ ) {
	$label = 'Day ' . ( $day + 1 );
	$code  = 'Day ' . ( $day + 1 );

	$schedule_scan_day_options[] = array(
		'label' => $label,
		'code'  => $code,
	);
}
$on_hide_options         = array();
$on_hide_options[0]      = array(
	'label' => 'Animate',
	'code'  => true,
);
$on_hide_options[1]      = array(
	'label' => 'Disappear',
	'code'  => false,
);
$on_load_options         = array();
$on_load_options[0]      = array(
	'label' => 'Animate',
	'code'  => true,
);
$on_load_options[1]      = array(
	'label' => 'Sticky',
	'code'  => false,
);
$tab_position_options    = array();
$tab_position_options[0] = array(
	'label' => 'Left',
	'code'  => 'left',
);
$tab_position_options[1] = array(
	'label' => 'Right',
	'code'  => 'right',
);
$posts_list              = get_posts();
$pages_list              = get_pages();
$list_of_contents        = array();
$index                   = 0;
foreach ( $posts_list as $post ) {
	$list_of_contents[ $index ] = array(
		'label' => $post->post_title,
		'code'  => $post->ID,
	);
	++$index;
}
foreach ( $pages_list as $page ) {
	$list_of_contents[ $index ] = array(
		'label' => $page->post_title,
		'code'  => $page->ID,
	);
	++$index;
}
// pages for hide banner.
$list_of_pages        = array();
$indx 				  = 0;
foreach ( $pages_list as $page ) {
	$list_of_pages[ $indx ] = array(
		'label' => $page->post_title,
		'code'  => $page->ID,
	);
	++$indx;
}
$show_as_options      = array();
$show_as_options[0]   = array(
	'label' => 'Button',
	'code'  => true,
);
$show_as_options[1]   = array(
	'label' => 'Link',
	'code'  => false,
);
$url_type_options     = array();
$url_type_options[0]  = array(
	'label' => 'Page',
	'code'  => true,
);
$url_type_options[1]  = array(
	'label' => 'Custom URL',
	'code'  => false,
);
$border_styles        = self::get_background_border_styles();
$styles_length        = count( $border_styles );
$styles_keys          = array_keys( $border_styles );
$border_style_options = array();
for ( $i = 0; $i < $styles_length; $i++ ) {
	$border_style_options[ $i ] = array(
		'label' => $styles_keys[ $i ],
		'code'  => $border_styles[ $styles_keys[ $i ] ],
	);
}
$cookie_font  = array();
$cookie_font  = apply_filters( 'gcc_font_options', $cookie_font );
$font_length  = count( $cookie_font );
$font_keys    = array_keys( $cookie_font );
$font_options = array();
for ( $i = 0; $i < $font_length; $i++ ) {
	$font_options[ $i ] = array(
		'label' => $font_keys[ $i ],
		'code'  => $cookie_font[ $font_keys[ $i ] ],
	);
}
$layout_skin         = array();
$layout_skin         = apply_filters( 'gcc_layout_skin_options', $layout_skin );
$layout_length       = count( $layout_skin );
$layout_keys         = array_keys( $layout_skin );
$layout_skin_options = array();

for ( $i = 0; $i < $layout_length; $i++ ) {
	$layout_skin_options[ $i ] = array(
		'label' => $layout_keys[ $i ],
		'code'  => $layout_skin[ $layout_keys[ $i ] ],
	);
}
$privacy_policy_page_options = array();
$index                       = 0;
foreach ( $pages_list as $page ) {
	$privacy_policy_page_options[ $index ] = array(
		'label' => $page->post_title,
		'code'  => $page->ID,
	);
	++$index;
}
$button_sizes        = self::get_button_sizes();
$button_sizes_length = count( $button_sizes );
$button_sizes_keys   = array_keys( $button_sizes );
$button_size_options = array();
for ( $i = 0; $i < $button_sizes_length; $i++ ) {
	$button_size_options[ $i ] = array(
		'label' => $button_sizes_keys[ $i ],
		'code'  => $button_sizes[ $button_sizes_keys[ $i ] ],
	);
}
$button_sizes        = self::get_button_sizes();
$sizes_length        = count( $button_sizes );
$sizes_keys          = array_keys( $button_sizes );
$accept_size_options = array();

for ( $i = 0; $i < $sizes_length; $i++ ) {
	$accept_size_options[ $i ] = array(
		'label' => $sizes_keys[ $i ],
		'code'  => $button_sizes[ $sizes_keys[ $i ] ],
	);
}

$button_actions        = self::get_js_actions();
$action_length         = count( $button_actions );
$action_keys           = array_keys( $button_actions );
$accept_action_options = array();

for ( $i = 0; $i < $action_length; $i++ ) {
	$accept_action_options[ $i ] = array(
		'label' => $action_keys[ $i ],
		'code'  => $button_actions[ $action_keys[ $i ] ],
	);
}
$accept_button_as_options    = array();
$accept_button_as_options[0] = array(
	'label' => 'Button',
	'code'  => true,
);
$accept_button_as_options[1] = array(
	'label' => 'Link',
	'code'  => false,
);
$open_url_options            = array();
$open_url_options[0]         = array(
	'label' => 'Yes',
	'code'  => true,
);
$open_url_options[1]         = array(
	'label' => 'No',
	'code'  => false,
);
$decline_action_options      = array();
$decline_action_options[0]   = array(
	'label' => 'Close Header',
	'code'  => '#cookie_action_close_header_reject',
);
$decline_action_options[1]   = array(
	'label' => 'Open URL',
	'code'  => 'CONSTANT_OPEN_URL',
);

$settings_layout_options             = array();
$settings_layout_options[0]          = array(
	'label' => 'Extented Banner',
	'code'  => false,
);
$settings_layout_options[1]          = array(
	'label' => 'Popup',
	'code'  => true,
);
$settings_layout_options_extended    = array();
$settings_layout_options_extended[0] = end( $settings_layout_options );
$script_blocker_settings             = array();
$cookie_list_settings                = array();
$cookie_scan_settings                = array();
$script_blocker_settings             = apply_filters( 'gdpr_settings_script_blocker_values', '' );
$cookie_list_settings                = apply_filters( 'gdpr_settings_cookie_list_values', '' );
$cookie_scan_settings                = apply_filters( 'gdpr_settings_cookie_scan_values', '' );
wp_localize_script(
	'gdpr-cookie-consent-main',
	'settings_obj',
	array(
		'the_options'                      => $settings,
		'ajaxurl'                          => admin_url( 'admin-ajax.php' ),
		'policies'                         => $policies,
		'position_options'                 => $position_options,
		'show_cookie_as_options'           => $show_cookie_as_options,
		'show_language_as_options'         => $show_language_as_options,
		'schedule_scan_options'            => $schedule_scan_options,
		'schedule_scan_day_options'        => $schedule_scan_day_options,
		'on_hide_options'                  => $on_hide_options,
		'on_load_options'                  => $on_load_options,
		'is_pro_active'                    => $is_pro_active,
		'tab_position_options'             => $tab_position_options,
		'cookie_expiry_options'            => $cookie_expiry_options,
		'list_of_contents'                 => $list_of_contents,
		'border_style_options'             => $border_style_options,
		'show_as_options'                  => $show_as_options,
		'url_type_options'                 => $url_type_options,
		'privacy_policy_options'           => $privacy_policy_page_options,
		'button_size_options'              => $button_size_options,
		'accept_size_options'              => $accept_size_options,
		'accept_action_options'            => $accept_action_options,
		'accept_button_as_options'         => $accept_button_as_options,
		'open_url_options'                 => $open_url_options,
		'widget_position_options'          => $widget_position_options,
		'decline_action_options'           => $decline_action_options,
		'settings_layout_options'          => $settings_layout_options,
		'settings_layout_options_extended' => $settings_layout_options_extended,
		'script_blocker_settings'          => $script_blocker_settings,
		'font_options'                     => $font_options,
		'layout_skin_options'              => $layout_skin_options,
		'cookie_list_settings'             => $cookie_list_settings,
		'cookie_scan_settings'             => $cookie_scan_settings,
		'restore_settings_nonce'           => wp_create_nonce( 'restore_default_settings' ),
		// hide banner.
		'list_of_pages'                    => $list_of_pages,
	)
);

wp_enqueue_script( 'gdpr-cookie-consent-main' );

// enqueue wizard admin style.
wp_enqueue_style( 'gdpr-cookie-consent-wizard' );
/*********************************/

$image_path = GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/';
$is_pro     = get_option( 'wpl_pro_active', false );

/**
 *  Cookie Template card for Pro version.
 *
 * @param string $name name of the template.
 *
 * @param array  $templates list of template settings.
 *
 * @param string $checked name of the selected template.
 *
 * @since 1.0.0
 */
function print_template_boxes( $name, $templates, $checked ) {

	$image_path = GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/';
	?>
	<div class="gdpr-templates-field-container">
		<?php
		foreach ( $templates as $key => $template ) :
			if ( false !== strpos( $template['name'], 'column' ) ) {
				$column = true;
			} else {
				$column = false;
			}
			if ( false !== strpos( $template['name'], 'square' ) ) {
				$square = true;
			} else {
				$square = false;
			}
			?>
		<div class="gdpr-template-field gdpr-<?php echo esc_attr( $template['name'] ); ?>">
			<div class="gdpr-left-field">
			<c-input type="radio" :disabled="disableSwitch" name="<?php echo esc_attr( $name ) . '_template_field'; ?>" value="<?php echo esc_attr( $template['name'] ); ?>" @change="onTemplateChange"
			<?php
			if ( $template['name'] === $checked ) {
				echo ':checked="true"';
			}
			?>
			>
			</div>
			<div class="gdpr-right-field" style="<?php echo esc_attr( $template['css'] ); ?>">
				<div class="gdpr-right-field-content">
					<div class="gdpr-group-description" style="margin-top:20px">
						<h3 v-if="gdpr_message_heading.length>0">{{gdpr_message_heading}}</h3>
						<?php if ( $column ) : ?>
							<p>{{gdpr_message}}</p>
							<?php
							if ( isset( $template['readmore'] ) ) :
								$class = '';
								if ( $template['readmore']['as_button'] ) :
									$class = 'btn btn-sm';
								endif;
								?>
								<p><a style="<?php echo esc_attr( $template['readmore']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['readmore']['text'] ); ?></a></p>
							<?php endif; ?>
						<?php else : ?>
							<p>{{gdpr_message}}
								<?php
								if ( isset( $template['readmore'] ) ) :
									$class = '';
									if ( $template['readmore']['as_button'] ) :
										$class = 'btn btn-sm';
									endif;
									?>
									<a style="<?php echo esc_attr( $template['readmore']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['readmore']['text'] ); ?></a>
								<?php endif; ?>
							</p>
						<?php endif; ?>
					</div>
					<div class="gdpr-group-buttons">
						<?php if ( $square ) : ?>
							<?php
							if ( isset( $template['decline'] ) ) :
								$class = '';
								if ( $template['decline']['as_button'] ) :
									$class = 'btn btn-sm';
								endif;
								?>
								<a style="<?php echo esc_attr( $template['decline']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['decline']['text'] ); ?></a>
							<?php endif; ?>
							<?php
							if ( isset( $template['settings'] ) ) :
								$class = '';
								if ( $template['settings']['as_button'] ) :
									$class = 'btn btn-sm';
								endif;
								?>
								<a style="<?php echo esc_attr( $template['settings']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['settings']['text'] ); ?></a>
							<?php endif; ?>
							<?php
							if ( isset( $template['accept'] ) ) :
								$class = '';
								if ( $template['accept']['as_button'] ) :
									$class = 'btn btn-sm';
								endif;
								?>
								<a style="<?php echo esc_attr( $template['accept']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['accept']['text'] ); ?></a>
							<?php endif; ?>
						<?php else : ?>
							<?php
							if ( isset( $template['accept'] ) ) :
								$class = '';
								if ( $template['accept']['as_button'] ) :
									$class = 'btn btn-sm';
								endif;
								?>
								<a style="<?php echo esc_attr( $template['accept']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['accept']['text'] ); ?></a>
							<?php endif; ?>
							<?php
							if ( isset( $template['decline'] ) ) :
								$class = '';
								if ( $template['decline']['as_button'] ) :
									$class = 'btn btn-sm';
								endif;
								?>
								<a style="<?php echo esc_attr( $template['decline']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['decline']['text'] ); ?></a>
							<?php endif; ?>
							<?php
							if ( isset( $template['settings'] ) ) :
								$class = '';
								if ( $template['settings']['as_button'] ) :
									$class = 'btn btn-sm';
								endif;
								?>
								<a style="<?php echo esc_attr( $template['settings']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['settings']['text'] ); ?></a>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<!-- go pro label image for pro templates-->

					<div class="gdpr-go-pro-label">
								<div class="go-pro-label-image" ><img src="<?php echo esc_url( $image_path ) . 'go-pro.png'; ?>"></div>
					</div>

				</div>
			</div>
		</div>
	<?php endforeach; ?>
	</div>
	<?php
}


/**
 * Function returns list of templates.
 *
 * @since 2.5
 * @param String $template_type Template type.
 * @return array
 */
function get_templates( $template_type ) {
	$templates = apply_filters(
		'gdprcookieconsent_templates',
		array(
			'banner' => array(
				'default'          => array(
					'name'             => 'banner-default',
					'css'              => 'max-width:500px;color:#000000;background-color:#ffffff;text-align:justify;',
					'color'            => '#000000',
					'background_color' => '#ffffff',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#ffffff',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'accept'           => array(
						'text'                 => __( 'Accept', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#66cc66;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#66cc66',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#66cc66',
						'button_border_radius' => '0',
					),
					'decline'          => array(
						'text'                 => __( 'Decline', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ef5454',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#ef5454',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#007cba;',
						'link_color' => '#007cba',
					),
					'settings'         => array(
						'text'                 => __( 'Cookie Settings', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#007cba;color:#ffffff;float:right;',
						'link_color'           => '#ffffff',
						'button_color'         => '#007cba',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#007cba',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#66cc66;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#66cc66',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#66cc66',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ef5454',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#ef5454',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#007cba;',
						'link_color' => '#007cba',
					),
				),
				'almond_column'    => array(
					'name'             => 'banner-almond_column',
					'css'              => 'max-width:500px;color:#1e3d59;background-color:#e8ddbb;text-align:justify;',
					'color'            => '#1e3d59',
					'background_color' => '#e8ddbb',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#e8ddbb',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#de7834',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#de7834',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#de7834;',
						'link_color' => '#de7834',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#de7834',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#66cc66',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#de7834;',
						'link_color' => '#de7834',
					),
				),
				'navy_blue_center' => array(
					'name'             => 'banner-navy_blue_center',
					'css'              => 'max-width:500px;color:#e5e5e5;background-color:#2a3e71;text-align:center;',
					'color'            => '#e5e5e5',
					'background_color' => '#2a3e71',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#2a3e71',
					'border_radius'    => '0',
					'layout'           => 'default',
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#e5e5e5',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
				),
				'grey_column'      => array(
					'name'             => 'banner-grey_column',
					'css'              => 'max-width:500px;color:#000000;background-color:#f4f4f4;text-align:justify;',
					'color'            => '#000000',
					'background_color' => '#f4f4f4',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#f4f4f4',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#e14469;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#e14469',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#e14469',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#111111;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#111111',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#111111',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#e14469;',
						'link_color' => '#e14469',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#e14469;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#e14469',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#e14469',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#111111;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#111111',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#111111',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#e14469;',
						'link_color' => '#e14469',
					),
				),
				'dark_row'         => array(
					'name'             => 'banner-dark_row',
					'css'              => 'max-width:500px;color:#ffffff;background-color:#323742;text-align:center;',
					'color'            => '#ffffff',
					'background_color' => '#323742',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#323742',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#3eaf9a;color:#ffffff;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid 6a8ee7',
						'link_color'           => '#ffffff',
						'button_color'         => '#3eaf9a',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#3eaf9a',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(50, 55, 66, 0);color:#3eaf9a;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid #3eaf9a;',
						'link_color'           => '#3eaf9a',
						'button_color'         => '#323742',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#3eaf9a',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#3eaf9a;',
						'link_color' => '#3eaf9a',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#3eaf9a;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#3eaf9a',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#3eaf9a',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#323742;color:#3eaf9a;margin:0 0.5rem 0 0',
						'link_color'           => '#3eaf9a',
						'button_color'         => '#323742',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#323742',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#3eaf9a;',
						'link_color' => '#3eaf9a',
					),
				),
				'grey_center'      => array(
					'name'             => 'banner-grey_center',
					'css'              => 'max-width:500px;color:#000000;background-color:#f4f4f4;text-align:center;',
					'color'            => '#000000',
					'background_color' => '#f4f4f4',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#f4f4f4',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#de7834;',
						'link_color' => '#de7834',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#ffffff;min-width:5rem;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#de7834',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#de7834',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#de7834',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#66cc66',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#de7834;',
						'link_color' => '#de7834',
					),
				),
				'dark'             => array(
					'name'             => 'banner-dark',
					'css'              => 'max-width:500px;color:#ffffff;background-color:#262626;text-align:justify;',
					'color'            => '#ffffff',
					'background_color' => '#262626',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#262626',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#6a8ee7;color:#ffffff;margin:0 0.5rem 0 0;border:1px solid #6a8ee7;',
						'link_color'           => '#ffffff',
						'button_color'         => '#6a8ee7',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#6a8ee7',
						'button_border_radius' => '0',
					),
					'decline'          => array(
						'text'                 => 'Decline',
						'as_button'            => true,
						'css'                  => 'background-color:#808080;color:#ffffff;float:right;border:1px solid #808080;',
						'link_color'           => '#ffffff',
						'button_color'         => '#808080',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#6a8ee7;',
						'link_color' => '#6a8ee7',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(38, 38, 38, 0);color:#808080;float:right;margin:0 0.5rem 0 0;border:1px solid #808080',
						'link_color'           => '#808080',
						'button_color'         => '#262626',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#6a8ee7;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#6a8ee7',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#6a8ee7',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#808080;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#808080',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#6a8ee7;',
						'link_color' => '#6a8ee7',
					),
				),
			),
			'popup'  => array(
				'default'          => array(
					'name'             => 'popup-default',
					'css'              => 'max-width:350px;color:#000000;background-color:#ffffff;text-align:justify;',
					'color'            => '#000000',
					'background_color' => '#ffffff',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#ffffff',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'accept'           => array(
						'text'                 => __( 'Accept', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#66cc66;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#66cc66',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#66cc66',
						'button_border_radius' => '0',
					),
					'decline'          => array(
						'text'                 => __( 'Decline', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ef5454',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#ef5454',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#007cba;',
						'link_color' => '#007cba',
					),
					'settings'         => array(
						'text'                 => __( 'Cookie Settings', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#007cba;color:#ffffff;float:right;',
						'link_color'           => '#ffffff',
						'button_color'         => '#007cba',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#007cba',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#66cc66;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#66cc66',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#66cc66',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ef5454',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#ef5454',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#007cba;',
						'link_color' => '#007cba',
					),
				),
				'dark'             => array(
					'name'             => 'popup-dark',
					'css'              => 'max-width:350px;color:#ffffff;background-color:#262626;text-align:justify;',
					'color'            => '#ffffff',
					'background_color' => '#262626',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#262626',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#6a8ee7;color:#ffffff;margin:0 0.5rem 0 0;border:1px solid #6a8ee7;',
						'link_color'           => '#ffffff',
						'button_color'         => '#6a8ee7',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#6a8ee7',
						'button_border_radius' => '0',
					),
					'decline'          => array(
						'text'                 => 'Decline',
						'as_button'            => true,
						'css'                  => 'background-color:#808080;color:#ffffff;float:none;border:1px solid #808080;',
						'link_color'           => '#ffffff',
						'button_color'         => '#808080',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#6a8ee7;',
						'link_color' => '#6a8ee7',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(38, 38, 38, 0);color:#808080;float:right;margin:0 0.5rem 0 0;border:1px solid #808080',
						'link_color'           => '#808080',
						'button_color'         => '#262626',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#6a8ee7',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#6a8ee7',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#808080;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#808080',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#6a8ee7;',
						'link_color' => '#6a8ee7',
					),
				),
				'almond_column'    => array(
					'name'             => 'popup-almond_column',
					'css'              => 'max-width:350px;color:#1e3d59;background-color:#e8ddbb;text-align:justify;',
					'color'            => '#1e3d59',
					'background_color' => '#e8ddbb',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#e8ddbb',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#de7834',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#de7834',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#de7834;',
						'link_color' => '#de7834',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#de7834',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#de7834;',
						'link_color' => '#de7834',
					),
				),
				'navy_blue_center' => array(
					'name'             => 'popup-navy_blue_center',
					'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:center;',
					'color'            => '#e5e5e5',
					'background_color' => '#2a3e71',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#2a3e71',
					'border_radius'    => '0',
					'layout'           => 'default',
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#e5e5e5',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#66cc66',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
				),
				'dark_row'         => array(
					'name'             => 'popup-dark_row',
					'css'              => 'max-width:350px;color:#ffffff;background-color:#323742;text-align:center;',
					'color'            => '#ffffff',
					'background_color' => '#323742',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#323742',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#3eaf9a;color:#ffffff;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid 6a8ee7',
						'link_color'           => '#ffffff',
						'button_color'         => '#3eaf9a',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#3eaf9a',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(50, 55, 66, 0);color:#3eaf9a;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid #3eaf9a;',
						'link_color'           => '#3eaf9a',
						'button_color'         => '#323742',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#3eaf9a',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#3eaf9a;',
						'link_color' => '#3eaf9a',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#3eaf9a;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#3eaf9a',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#3eaf9a',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#323742;color:#3eaf9a;margin:0 0.5rem 0 0',
						'link_color'           => '#3eaf9a',
						'button_color'         => '#323742',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#323742',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#3eaf9a;',
						'link_color' => '#3eaf9a',
					),
				),
				'grey_center'      => array(
					'name'             => 'popup-grey_center',
					'css'              => 'max-width:350px;color:#000000;background-color:#f4f4f4;text-align:center;',
					'color'            => '#000000',
					'background_color' => '#f4f4f4',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#f4f4f4',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#de7834;',
						'link_color' => '#de7834',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#ffffff;min-width:5rem;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#de7834',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#de7834',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#de7834',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#de7834',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#de7834;',
						'link_color' => '#de7834',
					),
				),
				'navy_blue_box'    => array(
					'name'             => 'popup-navy_blue_box',
					'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:justify;border-radius:15px;',
					'color'            => '#e5e5e5',
					'background_color' => '#2a3e71',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#2a3e71',
					'border_radius'    => '15',
					'layout'           => 'default',
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;width:100%;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;width:100%;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#e5e5e5',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
				),
				'grey_column'      => array(
					'name'             => 'popup-grey_column',
					'css'              => 'max-width:350px;color:#000000;background-color:#f4f4f4;text-align:justify;border:1px solid #111111',
					'color'            => '#000000',
					'background_color' => '#f4f4f4',
					'opacity'          => '1',
					'border_style'     => 'solid',
					'border_width'     => '1',
					'border_color'     => '#111111',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#e14469;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#e14469',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#e14469',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#111111;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#111111',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#111111',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#e14469;',
						'link_color' => '#e14469',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#e14469;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#e14469',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#e14469',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#111111;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#111111',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#111111',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#e14469;',
						'link_color' => '#e14469',
					),
				),
				'navy_blue_square' => array(
					'name'             => 'popup-navy_blue_square',
					'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:justify;',
					'color'            => '#e5e5e5',
					'background_color' => '#2a3e71',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#2a3e71',
					'border_radius'    => '0',
					'layout'           => 'default',
					'decline'          => array(
						'text'                 => 'Decline',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;width:41%;margin:0 0.5rem 0 0;min-width:5rem;border:1px solid #369ee3;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;width:41%;min-width:5rem;float:right;border:1px solid #369ee3;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;width:100%;margin:1rem auto 0 auto;min-width:5rem;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#007cba;',
						'link_color' => '#007cba',
					),
				),
			),
			'widget' => array(
				'default'          => array(
					'name'             => 'widget-default',
					'css'              => 'max-width:350px;color:#000000;background-color:#ffffff;text-align:justify;',
					'color'            => '#000000',
					'background_color' => '#ffffff',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#ffffff',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'accept'           => array(
						'text'                 => __( 'Accept', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#66cc66;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#66cc66',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#66cc66',
						'button_border_radius' => '0',
					),
					'decline'          => array(
						'text'                 => __( 'Decline', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ef5454',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#ef5454',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#007cba;',
						'link_color' => '#007cba',
					),
					'settings'         => array(
						'text'                 => __( 'Cookie Settings', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#007cba;color:#ffffff;float:right;',
						'link_color'           => '#ffffff',
						'button_color'         => '#007cba',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#007cba',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#66cc66;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#66cc66',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#66cc66',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ef5454',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#ef5454',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#007cba;',
						'link_color' => '#007cba',
					),
				),
				'dark'             => array(
					'name'             => 'widget-dark',
					'css'              => 'max-width:350px;color:#ffffff;background-color:#262626;text-align:justify;',
					'color'            => '#ffffff',
					'background_color' => '#262626',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#262626',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#6a8ee7;color:#ffffff;margin:0 0.5rem 0 0;border:1px solid #6a8ee7;',
						'link_color'           => '#ffffff',
						'button_color'         => '#6a8ee7',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#6a8ee7',
						'button_border_radius' => '0',
					),
					'decline'          => array(
						'text'                 => 'Decline',
						'as_button'            => true,
						'css'                  => 'background-color:#808080;color:#ffffff;float:right;border:1px solid #808080;',
						'link_color'           => '#ffffff',
						'button_color'         => '#808080',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#6a8ee7;',
						'link_color' => '#6a8ee7',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(38, 38, 38, 0);color:#808080;float:right;margin:0 0.5rem 0 0;border:1px solid #808080',
						'link_color'           => '#808080',
						'button_color'         => '#262626',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#6a8ee7',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#6a8ee7',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#808080;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#808080',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#6a8ee7;',
						'link_color' => '#6a8ee7',
					),
				),
				'almond_column'    => array(
					'name'             => 'widget-almond_column',
					'css'              => 'max-width:350px;color:#1e3d59;background-color:#e8ddbb;text-align:justify;',
					'color'            => '#1e3d59',
					'background_color' => '#e8ddbb',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#e8ddbb',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#de7834',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#de7834',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#de7834;',
						'link_color' => '#de7834',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#de7834',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#de7834;',
						'link_color' => '#de7834',
					),
				),
				'navy_blue_box'    => array(
					'name'             => 'widget-navy_blue_box',
					'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:justify;border-radius:15px;',
					'color'            => '#e5e5e5',
					'background_color' => '#2a3e71',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#2a3e71',
					'border_radius'    => '15',
					'layout'           => 'default',
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;width:100%;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;width:100%;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#e5e5e5',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
				),
				'dark_row'         => array(
					'name'             => 'widget-dark_row',
					'css'              => 'max-width:350px;color:#ffffff;background-color:#323742;text-align:center;',
					'color'            => '#ffffff',
					'background_color' => '#323742',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#323742',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#3eaf9a;color:#ffffff;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid 6a8ee7',
						'link_color'           => '#ffffff',
						'button_color'         => '#3eaf9a',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#3eaf9a',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(50, 55, 66, 0);color:#3eaf9a;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid #3eaf9a;',
						'link_color'           => '#3eaf9a',
						'button_color'         => '#323742',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#3eaf9a',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#3eaf9a;',
						'link_color' => '#3eaf9a',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#3eaf9a;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#3eaf9a',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#3eaf9a',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#323742;color:#3eaf9a;margin:0 0.5rem 0 0',
						'link_color'           => '#3eaf9a',
						'button_color'         => '#323742',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#323742',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#3eaf9a;',
						'link_color' => '#3eaf9a',
					),
				),
				'navy_blue_center' => array(
					'name'             => 'widget-navy_blue_center',
					'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:center;',
					'color'            => '#e5e5e5',
					'background_color' => '#2a3e71',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#2a3e71',
					'border_radius'    => '0',
					'layout'           => 'default',
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#e5e5e5',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#66cc66',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
				),
				'grey_column'      => array(
					'name'             => 'widget-grey_column',
					'css'              => 'max-width:350px;color:#000000;background-color:#f4f4f4;text-align:justify;border: 1px solid #111111;',
					'color'            => '#000000',
					'background_color' => '#f4f4f4',
					'opacity'          => '1',
					'border_style'     => 'solid',
					'border_width'     => '1',
					'border_color'     => '#111111',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#e14469;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#e14469',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#e14469',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#111111;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#111111',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#111111',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#e14469;',
						'link_color' => '#e14469',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#e14469;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#e14469',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#e14469',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#111111;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#111111',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#111111',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#e14469;',
						'link_color' => '#e14469',
					),
				),
				'grey_center'      => array(
					'name'             => 'widget-grey_center',
					'css'              => 'max-width:350px;color:#000000;background-color:#f4f4f4;text-align:center;',
					'color'            => '#000000',
					'background_color' => '#f4f4f4',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#f4f4f4',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#de7834;',
						'link_color' => '#de7834',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#ffffff;min-width:5rem;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#de7834',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#de7834',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#de7834',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#de7834',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#de7834;',
						'link_color' => '#de7834',
					),
				),
				'navy_blue_square' => array(
					'name'             => 'widget-navy_blue_square',
					'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:justify;',
					'color'            => '#e5e5e5',
					'background_color' => '#2a3e71',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#2a3e71',
					'border_radius'    => '0',
					'layout'           => 'default',
					'decline'          => array(
						'text'                 => 'Decline',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;width:41%;margin:0 0.5rem 0 0;min-width:5rem;border:1px solid #369ee3;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;width:41%;min-width:5rem;float:right;border:1px solid #369ee3;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;width:100%;margin:1rem auto 0 auto;min-width:5rem;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#007cba;',
						'link_color' => '#007cba',
					),
				),
			),
		)
	);
	return $templates[ $template_type ];
}

/**
 * Wizard Template
 */

?>

<div class="gdpr-wizard-top-container">
	<img class="gdpr-wizard-logo" src="<?php echo esc_url( $image_path ) . 'gdprLogo.png'; ?>">
	<span class="gdpr-main-heading">WP Cookie Consent</span>

</div>

<div class="gdpr-wizard-main-container" id="gdpr-cookie-consent-settings-app-wizard">

<div class="form-container">

<!-- Cross Button  -->

<span id="closeButton" class="close-wizard"></span>

		<!-- form  -->
		<form id="gcc-save-settings-form-wizard" class="gcc-save-wizard-settings-form">
			<input type="hidden" name="gcc_settings_form_nonce_wizard" value="<?php echo esc_attr( wp_create_nonce( 'gcc-settings-form-nonce-wizard' ) ); ?>"/>

			<ul id="progressbar">
			<li class="active" id="step1">
				<div class="progress-step">
					<strong class="progress-bar-label getting-started-progress">Getting Started</strong>
					<div class="container">
					<div class="horizontal-line line-step-1"></div>
					</div>

				</div>
			</li>
			<li class="active"id="step2">
				<div class="progress-step">
					<strong class="progress-bar-label">General</strong>
					<div class="container">
					<div class="horizontal-line line-step-2"></div>
					<img class="step-images selected-step-img" src="<?php echo esc_url( $image_path ) . 'selected-step.png'; ?>">

					</div>
				</div>
			</li>
			<li id="step3">
				<div class="progress-step">
					<strong class="progress-bar-label">Configuration</strong>
					<div class="container">
					<div class="horizontal-line line-step-3"></div>
					<img class="step-images not-selected-step-img" src="<?php echo esc_url( $image_path ) . 'not-selected-step.png'; ?>">

					</div>
				</div>
			</li>
			<li id="step4">
				<div class="progress-step">
					<strong class="progress-bar-label">Finish</strong>
					<div class="container">
					<div class="horizontal-line line-step-4"></div>
					<img class="step-images finish-step-img" src="<?php echo esc_url( $image_path ) . 'finish.png'; ?>">

					</div>
				</div>
			</li>
			</ul>
			<div class="progress">
			<div class="progress-bar"></div>
			</div>
			<br>
			<div class="step-content">

				<!-- First Tab Conetent Start  -->

			<fieldset class="general-tab-content">
			<!-- radio button law  -->

				<div class="select-rule">
					<div class="select-law-rule-label"><label for="gdpr-cookie-consent-policy-type"><?php esc_attr_e( 'Which privacy law or guideline do you want to use as the default for your worldwide visitors?', 'gdpr-cookie-consent' ); ?></label></div>
					<div class="select-law-rule-options">
						<div class="form-group" id="gdpr-cookie-consent-policy-type">
						<label>
							<input type="radio" name="gcc-gdpr-policy" value="gdpr" v-model="gdpr_policy" @change="cookiePolicyChange">
							General Data Protection Regulation
						</label><br>
						<label>
							<input type="radio" name="gcc-gdpr-policy" value="lgpd" v-model="gdpr_policy" @change="cookiePolicyChange">
							General Data Protection Law ( LGPD )
						</label><br>
						<label>
							<input type="radio" name="gcc-gdpr-policy" value="ccpa" v-model="gdpr_policy" @change="cookiePolicyChange">
							The California Consumer Privacy Act
						</label><br>
						<label>
							<input type="radio" name="gcc-gdpr-policy" value="both" v-model="gdpr_policy" @change="cookiePolicyChange">
							GDPR & CCPA
						</label><br>
						<label>
							<input type="radio" name="gcc-gdpr-policy" value="eprivacy" v-model="gdpr_policy" @change="cookiePolicyChange">
							ePrivacy Regulation
						</label><br>
						</div>
						<input type="hidden" name="gcc-gdpr-policy" v-model="gdpr_policy">
					</div>

					<?php
					// if gdpr-pro is enable only then show these options.
					if ( $is_pro ) {

						?>
					<!-- Location Enable/Disbale for different rule  -->
					<div v-show="show_visitor_conditions">

						<div>

							<!-- gdpr geo selection for pro -->
							<c-row class="gdpr-selection gdpr-pro-geo-ques" v-show="is_gdpr" >
									<c-col class="gdpr-selection-label"><label><?php esc_attr_e( 'Show only for EU visitors', 'gdpr-cookie-consent' ); ?> </label></c-col>
									<c-col class="gdpr-options">
										<label class="wizard_eu_safe">
										<?php
										$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();

										if ( $the_options['enable_safe'] === 'true' ) :
											?>
											<label class="display_block">
											<div class="opacity_yes">
												<input class ="display_block"type="radio" name="gcc-eu-enable" value="yes" v-model="selectedRadioGdpr" disabled >
												Yes</div>
												<div class="wizard_eu_safe_message">
													<?php
													esc_attr_e(
														'Safe Mode enabled. Disable it in Compliance settings to manage integrations.',
														'gdpr-cookie-consent'
													);
													?>
												</div>
												</label>
												<?php if ($the_options['enable_safe'] === 'true') : ?>
													<label class="display_block">
														<input type="radio" name="gcc-eu-enable" value="no" v-model="selectedRadioGdpr" @click="onSwitchEUEnable('no')" checked>
														No
													</label>
												<?php endif; ?>
											<?php
										else :
											?>
											<label>
												<input type="radio" name="gcc-eu-enable" value="yes" v-model="selectedRadioGdpr" @click="onSwitchEUEnable('yes')">
												Yes
											</label>
											<label>
											<input type="radio" name="gcc-eu-enable" value="no" v-model="selectedRadioGdpr" @click="onSwitchEUEnable('no')">
											No
											</label>
											<input type="hidden" name="gcc-eu-enable" v-model="is_eu_on">
											<?php
										endif;
										?>
									</c-col>
								</c-row>
							<!-- IAB geo selection for pro -->
								<c-row class="ccpa-iab-selection iab-pro-geo-ques" v-show="is_ccpa" >
									<c-col class="gdpr-selection-label"><label><?php esc_attr_e( 'Enable IAB Transparency and Consent Framework (TCF)', 'gdpr-cookie-consent' ); ?> </label></c-col>
									<c-col class="iab-options">
										<label>
										<input type="radio" name="gcc-iab-enable" value="yes" v-model="selectedRadioIab" @click="onSwitchIABEnable('yes')">
										Yes
										</label>
										<label>
										<input type="radio" name="gcc-iab-enable" value="no" v-model="selectedRadioIab" @click="onSwitchIABEnable('no')">
										No
										</label>
										<input type="hidden" name="gcc-iab-enable" v-model="is_iab_on">
									</c-col>
								</c-row>

							<!-- ccpa geo selection for pro  -->

							<c-row class="ccpa-selection ccpa-pro-geo-ques"  v-show="is_ccpa" >
								<c-col class="ccpa-selection-label"><label><?php esc_attr_e( 'Show only for California visitors', 'gdpr-cookie-consent' ); ?> </label></c-col>
								<c-col class="ccpa-options">
								<label class="wizard_eu_safe">
										<?php
										$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();

										if ( $the_options['enable_safe'] === 'true' ) :
											?>
											<label class="display_block">
												<div class="opacity_yes">
												<input class ="display_block"type="radio" name="gcc-ccpa-enable" value="yes" v-model="selectedRadioCcpa" disabled >
												Yes</div>
												<div class="wizard_eu_safe_message ">
													<?php
													esc_attr_e(
														'Safe Mode enabled. Disable it in Compliance settings to manage integrations.',
														'gdpr-cookie-consent'
													);
													?>
												</div>
												</label>
												<?php if ($the_options['enable_safe'] === 'true') : ?>
													<label class="display_block">
														<input type="radio" name="gcc-ccpa-enable" value="no" v-model="selectedRadioCcpa" @click="onSwitchCCPAEnable('no')" checked>
														No
													</label>
												<?php endif; ?>
											<?php
										else :
											?>
									<label>
									<input type="radio" name="gcc-ccpa-enable" value="yes" v-model="selectedRadioCcpa" @click="onSwitchCCPAEnable('yes')">
									Yes
									</label>
									<label>
									<input type="radio" name="gcc-ccpa-enable" value="no" v-model="selectedRadioCcpa" @click="onSwitchCCPAEnable('no')">
									No
									</label>
									<input type="hidden" name="gcc-ccpa-enable" v-model="is_ccpa_on">
									<?php
										endif;
										?>
								</c-col>
							</c-row>

						</div>
					</div>

						<?php
					} else {

						?>
					<!-- When Pro is not activated  -->
						<!-- gdpr free selection with Pro Tag -->
						<div class="geo-location-ques-container gdpr-free-geo-ques">

								<c-row class="gdpr-selection" v-show="is_gdpr">
										<c-col class="gdpr-selection-label"><label><?php esc_attr_e( 'Show only for EU visitors', 'gdpr-cookie-consent' ); ?> </label></c-col>
										<c-col class="gdpr-options">
											<label class="gdpr-yes-option">
											<input disabled type="radio" name="gcc-eu-enable" value="yes" v-model="selectedRadioGdpr" @click="onSwitchEUEnable('yes')">
											Yes
											</label>
											<label>
											<input type="radio" name="gcc-eu-enable" value="no" v-model="selectedRadioGdpr" @click="onSwitchEUEnable('no')">
											No
											</label>
											<input type="hidden" name="gcc-eu-enable" v-model="is_eu_on">
										</c-col>
								</c-row>
								<div class="gdpr-pro-label" v-show="is_gdpr">
									<div class="gdpr-pro-label-text" >Pro</div>
								</div>
						</div>
						<!-- IAB free selection with Pro Tag  -->
						<div class="geo-location-ques-container iab-free-geo-ques">
							<c-row class="ccpa-iab-selection" v-show="is_ccpa" >
									<c-col class="gdpr-selection-label"><label><?php esc_attr_e( 'Enable IAB Transparency and Consent Framework (TCF)', 'gdpr-cookie-consent' ); ?> </label></c-col>
									<c-col class="iab-options">
										<label>
										<input type="radio" name="gcc-iab-enable" value="yes" v-model="selectedRadioIab" @click="onSwitchIABEnable('yes')">
										Yes
										</label>
										<label>
										<input type="radio" name="gcc-iab-enable" value="no" v-model="selectedRadioIab" @click="onSwitchIABEnable('no')">
										No
										</label>
										<input type="hidden" name="gcc-iab-enable" v-model="is_iab_on">
									</c-col>
								</c-row>
						</div>

						<!--  CCPA free selection with Pro Tag -->
						<div class="geo-location-ques-container ccpa-free-geo-ques">
							<c-row class="ccpa-selection"  v-show="is_ccpa" >
									<c-col class="ccpa-selection-label"><label><?php esc_attr_e( 'Show only for California visitors', 'gdpr-cookie-consent' ); ?> </label></c-col>
									<c-col class="ccpa-options">
										<label class="ccpa-yes-option">
										<input disabled type="radio" name="gcc-ccpa-enable" value="yes" v-model="selectedRadioCcpa" @click="onSwitchCCPAEnable('yes')">
										Yes
										</label>
										<label>
										<input type="radio" name="gcc-ccpa-enable" value="no" v-model="selectedRadioCcpa" @click="onSwitchCCPAEnable('no')">
										No
										</label>
										<input type="hidden" name="gcc-ccpa-enable" v-model="is_ccpa_on">
									</c-col>
								</c-row>
								<div class="gdpr-pro-label" v-show="is_ccpa">
										<div class="gdpr-pro-label-text" >Pro</div>
								</div>
						</div>

						<?php
					}

					?>

				</div>

				<input type="button" name="next-step" class="next-step first-next-step" value="Save & Continue" />

			</fieldset>
			<!-- First Tab Conetent End  -->

			<!-- Second Tab Content Field set Start  -->
			<fieldset class="configure-tab-content">

				<div class="configure-tab-main-container">

						<!-- enable consent log  -->
						<div class="enable-consent-log">

							<div class="enable-consent-log-content">
								<c-col class="enable-consent-log-content-label"><label><?php esc_attr_e( 'Enable Consent Logging', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="enable-consent-log-switch">
									<c-switch v-bind="isGdprProActive ? labelIcon : labelIconNew" v-model="logging_on" id="gdpr-cookie-consent-logging-on" variant="3d"  color="success" :checked="logging_on"  :disabled="disableSwitch" v-on:update:checked="onSwitchLoggingOn"></c-switch>
									<input type="hidden" name="gcc-logging-on" v-model="logging_on">
								</c-col>
							</div>

							<?php
								// if gdpr-pro is disable only then add pro label.
							if ( ! $is_pro ) {

								?>

									<div class="gdpr-pro-label">
										<div class="gdpr-pro-label-text" >Pro</div>
									</div>

								<?php
							}
							?>

						</div>

						<!-- enable/disbale script blocker  -->
						<div class="enable-script-blocker" v-show="gdpr_policy !== 'ccpa'">
							<div class="enable-script-blocker-content">
								<c-col class="enable-script-blocker-label"><label><?php esc_attr_e( 'Script Blocker', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="enable-consent-log-switch">
									<c-switch v-bind="isGdprProActive ? labelIcon : labelIconNew" v-model="is_script_blocker_on" id="gdpr-cookie-consent-script-blocker-on" variant="3d"  color="success" :checked="is_script_blocker_on" :disabled="disableSwitch" v-on:update:checked="onSwitchingScriptBlocker"></c-switch>
									<input type="hidden" name="gcc-script-blocker-on" v-model="is_script_blocker_on">
								</c-col>
							</div>
							<?php
								// if gdpr-pro is disable only then add pro label.
							if ( ! $is_pro ) {

								?>

									<div class="gdpr-pro-label">
										<div class="gdpr-pro-label-text" >Pro</div>
									</div>

								<?php
							}

							?>
						</div>

						<!-- show cookie notice  -->

						<div class="show-cookie-notice">
								<div class="show-cookie-notice-content">
									<c-col class="show-cookie-content-label"><label><?php esc_attr_e( 'Show Cookie Notice as', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="show-cookie-content-dropdown">
										<input type="hidden" name="show-cookie-as" v-model="show_cookie_as">
										<v-select class="form-group" id="gdpr-show-cookie-as" :reduce="label => label.code" :options="show_cookie_as_options" v-model="show_cookie_as"  @input="cookieTypeChange"></v-select>
									</c-col>
								</div>
						</div>

						<!-- show templates  -->

						<div class="show-cookie-template">

							<?php $the_options = Gdpr_Cookie_Consent::gdpr_get_settings(); ?>

							<div v-show="is_gdpr || is_lgpd" class="show-cookie-template-card">
								<c-card-header class="show-cookie-template-label"><?php esc_html_e( 'Choose template for your cookie bar', 'gdpr-cookie-consent' ); ?></c-card-header>
								<c-card-body>
									<!-- banner templates  -->
									<c-row v-show="show_banner_template" class="show-banner-template">
										<c-col class="col-sm-3 left-side-banner-template" >
											<input type="hidden" name="gdpr-banner-template" v-model="banner_template">
										</c-col>
										<div class="">
										<?php print_template_boxes( 'banner', get_templates( 'banner' ), $the_options['banner_template'] ); ?>
										</div>
									</c-row>
									<!-- popup templates  -->
									<c-row v-show="show_popup_template">
										<c-col class="col-sm-4 left-side-popup-template">
											<input type="hidden" name="gdpr-popup-template" v-model="popup_template">
										</c-col>
										<div class="">
										<?php print_template_boxes( 'popup', get_templates( 'popup' ), $the_options['popup_template'] ); ?>
										</div>
									</c-row>
									<!-- widget templates  -->
									<c-row v-show="show_widget_template">
										<c-col class="col-sm-4 left-side-widget-template">
											<input type="hidden" name="gdpr-widget-template" v-model="widget_template">
										</c-col>
										<div class="">
										<?php print_template_boxes( 'widget', get_templates( 'widget' ), $the_options['widget_template'] ); ?>
										</div>
									</c-row>

									<input type="hidden" name="gdpr-template" v-model="template">
								</c-card-body>
							</div>

						</div>

				</div>
				<input type="button" name="next-step" class="next-step second-next-step" value="Save & Continue" />
				<input type="button" name="previous-step" class="previous-step first-previous-step" value="< Go Back" />

			</fieldset>
			<!-- Second Tab Content Field set End  -->

			<!-- third Tab Content Field set start -->
			<fieldset class="finish-tab-content">

			<div class="finish-tab-main-container">

				<div class="thank-you-text">
					<span>Thank you for choosing WP Cookie Consent plugin - the most powerful cookie consent WordPress plugin.</span>
				</div>

				<div class="tab-row">
						<div class="column">
							<div class="tab-card">
								<img class="finish-card-img" src="<?php echo esc_url( $image_path ) . 'help-center.png'; ?>" >
								<div class="card-heading">Help Center</div>
								<div class="card-info">Read the documentation to find answers to your questions</div>
								<div class="learn-more-link help-center-link"> <a href="https://club.wpeka.com/docs/wp-cookie-consent/">Learn More >></a></div>
							</div>
						</div>
						<div class="column">
							<div class="tab-card">
								<img class="finish-card-img" src="<?php echo esc_url( $image_path ) . 'video.png'; ?>" >
								<div class="card-heading">Video Guides</div>
								<div class="card-info">Browse through these video tutorials to learn more about how WP Cookie Consent works.</div>
								<div class="learn-more-link video-guide-link"> <a href="https://club.wpeka.com/docs/wp-cookie-consent/video-guides/video-resources/">Learn More >></a></div>
							</div>
						</div>
						<div class="column">
							<div class="tab-card">
								<img class="finish-card-img" src="<?php echo esc_url( $image_path ) . 'faqs.png'; ?>" >
								<div class="card-heading">FAQs</div>
								<div class="card-info">Find answers to some of the most commonly asked questions.</div>
								<div class="learn-more-link faqs-link"> <a href="https://club.wpeka.com/docs/wp-cookie-consent/faqs/faq-2/">Learn More >></a></div>
							</div>
						</div>

				</div>

			</div>

				<input type="button" name="next-step" @click="saveWizardCookieSettings"  class="submit-button final-next-step" value="Save & Close" />
				<input type="button" name="previous-step" class="previous-step second-previous-step" value="< Go Back" />
			</fieldset>
			</div>
		</form>

	</div>

</div>



<script>

jQuery(document).ready(function () {
	var currentGfgStep, nextGfgStep, previousGfgStep;
	var opacity;
	var current = 2;
	var steps = jQuery("fieldset").length;
	var imagePath = "<?php echo esc_url( $image_path ); ?>";
	var isProActive = "<?php echo esc_url( $is_pro ); ?>";

	setProgressBar(current);

	jQuery(".next-step").click(function () {

		currentGfgStep = jQuery(this).parent();
		nextGfgStep = jQuery(this).parent().next();

		jQuery("#progressbar li").eq(jQuery("fieldset")
			.index(nextGfgStep)).addClass("active");

		nextGfgStep.show();
		currentGfgStep.animate({ opacity: 0 }, {
			step: function (now) {
				opacity = 1 - now;

				currentGfgStep.css({
					'display': 'none',
					'position': 'relative'
				});
				nextGfgStep.css({ 'opacity': opacity });
			},
			duration: 500
		});
		setProgressBar(++current);
	});

	jQuery(".previous-step").click(function () {

		currentGfgStep = jQuery(this).parent();
		previousGfgStep = jQuery(this).parent().prev();

		jQuery("#progressbar li").eq(jQuery("fieldset")
			.index(currentGfgStep)).removeClass("active");

		previousGfgStep.show();

		currentGfgStep.animate({ opacity: 0 }, {
			step: function (now) {
				opacity = 1 - now;

				currentGfgStep.css({
					'display': 'none',
					'position': 'relative'
				});
				previousGfgStep.css({ 'opacity': opacity });
			},
			duration: 500
		});
		setProgressBar(--current);
	});

	//first next button
	jQuery(".first-next-step").click(function () {
		jQuery('.line-step-2').addClass('right-side-line');
		jQuery('.line-step-3').addClass('left-side-line');

		//change the src of the image when clicked on the first next button
		jQuery('.selected-step-img').attr('src', imagePath + 'tick.png');
			jQuery('.not-selected-step-img').attr('src', imagePath + 'selected-step.png');

	});

	//first previous button
	jQuery(".first-previous-step").click(function () {
		jQuery('.line-step-2').removeClass('right-side-line');
		jQuery('.line-step-3').removeClass('left-side-line');

		//change the src of the image back to selected when prvious on the first next button
		jQuery('.selected-step-img').attr('src', imagePath + 'selected-step.png');
			jQuery('.not-selected-step-img').attr('src', imagePath + 'not-selected-step.png');

	});

	//second next button

	jQuery(".second-next-step").click(function () {
		jQuery('.line-step-4').addClass('last-step-right-side');
		jQuery('.line-step-3').addClass('left-side-line');
		jQuery('.line-step-3').addClass('right-side-line');

		jQuery('.not-selected-step-img').attr('src', imagePath + 'tick.png');
			jQuery('.finish-step-img').attr('src', imagePath + 'selected-step.png');


	});

	//second previous button

	jQuery(".second-previous-step").click(function () {
		jQuery('.line-step-4').removeClass('last-step-right-side');
		jQuery('.line-step-3').removeClass('right-side-line');

		jQuery('.finish-step-img').attr('src', imagePath + 'finish.png');
		jQuery('.not-selected-step-img').attr('src', imagePath + 'selected-step.png');

	});

	function setProgressBar(currentStep) {
		var percent = parseFloat(100 / steps) * current;
		percent = percent.toFixed();
		jQuery(".progress-bar")
			.css("width", percent + "%")
	}

	jQuery(".submit").click(function () {
		return false;
	})

	//if gdpr pro is active hide the go-pro image from the templates

	if ( isProActive ) {

		jQuery(".gdpr-go-pro-label").hide()

	}

	//close wizard button

	jQuery("#closeButton").click(function() {
		// Get the admin URL
		var adminUrl = "<?php echo esc_url( admin_url() ); ?>";

		// Redirect to the dashboard submenu
		window.location.href = adminUrl + "/admin.php?page=gdpr-cookie-consent";
	});

	//save and close submission

	jQuery(".submit-button").click(function() {
		// Get the admin URL
		var adminUrl = "<?php echo esc_url( admin_url() ); ?>";

		// Redirect to the dashboard submenu
		window.location.href = adminUrl + "/admin.php?page=gdpr-cookie-consent";
	});



});


</script>


<?php
