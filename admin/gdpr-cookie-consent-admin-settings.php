<?php
/**
 * Provide a admin area view for the settings.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$baseurl = '';
if ( isset( $_SERVER['PHP_SELF'] ) ) {
	$baseurl = esc_url_raw( wp_unslash( $_SERVER['PHP_SELF'] ) );
}
// check if pro is activated

$pro_is_activated = get_option( 'wpl_pro_active', false );

// Require the class file for gdpr cookie consent api framework settings.
require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';

// Instantiate a new object of the GDPR_Cookie_Consent_Settings class.
$this->settings = new GDPR_Cookie_Consent_Settings();

// Call the methods from the instantiated object to get user parameters.
$is_user_connected      = $this->settings->is_connected();
$api_user_email         = $this->settings->get_email();
$api_user_site_key      = $this->settings->get_website_key();
$api_user_plan          = $this->settings->get_plan();
if ( $pro_is_activated ) {
	$credit_link_href = 'https://club.wpeka.com/product/wp-gdpr-cookie-consent/?utm_source=gdpr&utm_medium=show-credits&utm_campaign=link&utm_content=powered-by-gdpr';
} else {
	$credit_link_href = 'https://wordpress.org/plugins/gdpr-cookie-consent/?utm_source=gdpr&utm_medium=show-credits&utm_campaign=link&utm_content=powered-by-gdpr';
}
$credit_link_text = __( 'WPLP Compliance Platform', 'gdpr-cookie-consent' );

$credit_link = sprintf(
	/* translators: 1: GDPR Cookie Consent Plugin*/
	__( 'Powered by %s', 'gdpr-cookie-consent' ),
	'<a href="' . esc_url( $credit_link_href ) . '" id="cookie_credit_link" rel="nofollow noopener" target="_blank">' . $credit_link_text . '</a>'
);
$no_of_pages_scan       = get_option( 'gdpr_no_of_page_scan' );
$total_pages_scan_limit = 100;
$template_view_type = $the_options['cookie_bar_as'];
$active_banner = 1;

if ( $api_user_plan == 'free' ) {
	$total_pages_scan_limit = 100;
} else {
	$total_pages_scan_limit = 20000;
}
$banner_layouts = json_decode($the_options['banner_layouts'], true);
$banner_structure = json_decode($the_options['banner_structure'], true);
$c5_buttons = isset( $banner_structure['c5'] ) && is_array( $banner_structure['c5'] )
		? $banner_structure['c5']
		: array( 'accept_all', 'accept', 'settings' );

$c6_buttons = isset( $banner_structure['c6'] ) && is_array( $banner_structure['c6'] )
		? $banner_structure['c6']
		: array( 'decline' );
if ( ! function_exists( 'wplp_render_notice_button' ) ) {
	function wplp_render_notice_button( $button ) {
		$button = is_string( $button ) ? $button : '';

		// Supports: accept_all, acceptAll, accept-all
		$button = str_replace( '-', '_', $button );
		$button = strtolower( $button );

		if ( 'acceptall' === $button ) {
			$button = 'accept_all';
		}

		switch ( $button ) {
			case 'decline':
				?>
				<a v-show="cookie_decline_on && !((!is_auto_mode && (is_pipeda || is_au_app)) || (is_auto_mode && (banner_edit_law === 'pipeda' || banner_edit_law === 'au_app')))"
				  href="#"
				  :style="{
						  'background-color': decline_as_button ? `${decline_background_color}${Math.floor(decline_opacity * 255).toString(16).toUpperCase()}` : 'transparent',
						  'color': decline_text_color,
						  'border-style': decline_as_button ? decline_style : 'none',
						  'border-width': decline_as_button ? decline_border_width + 'px' : '0',
						  'border-color': decline_as_button ? decline_border_color : 'transparent',
						  'border-radius': decline_as_button ? decline_border_radius + 'px' : '0',
						  'font-family': cookie_font,
						  'padding' : '8px 16px',
						  'font-size': button_font_size + 'px',
						  'font-weight': button_text_weight,
						  'width': decline_button_width === 'fit' ? 'fit-content' : '100%',
						  'min-width': decline_button_min_width + 'px',
						}"
				>
				  {{ decline_text }}
				</a>
				<?php
				break;

			case 'settings':
				?>
				<a v-show="cookie_settings_on && !((!is_auto_mode && is_eprivacy) || (is_auto_mode && banner_edit_law === 'eprivacy'))" id="cookie_action_settings_preview"
				  href="#"
				  :style="{
						  'background-color': settings_as_button ? `${settings_background_color}${Math.floor(settings_opacity * 255).toString(16).toUpperCase()}` : 'transparent',
						  'color': settings_text_color,
						  'border-style': settings_as_button ? settings_style : 'none',
						  'border-width': settings_as_button ? settings_border_width + 'px' : '0',
						  'border-color': settings_as_button ? settings_border_color : 'transparent',
						  'border-radius': settings_as_button ? settings_border_radius + 'px' : '0',
						  'font-family': cookie_font,
						  'padding' : '8px 16px',
						  'font-size': button_font_size + 'px',
						  'font-weight': button_text_weight,
						  'width': settings_button_width === 'fit' ? 'fit-content' : '100%',
						  'min-width': settings_button_min_width + 'px',
						}"
				>
					{{ settings_text }}
				</a>
				<?php
				break;

			case 'accept':
				?>
				<a v-show="cookie_accept_on && !((!is_auto_mode && (is_pipeda || is_au_app)) || (is_auto_mode && (banner_edit_law === 'pipeda' || banner_edit_law === 'au_app')))" 
				  href="#" 
				  :style="{
						  'background-color': accept_as_button ? `${accept_background_color}${Math.floor(accept_opacity * 255).toString(16).toUpperCase()}` : 'transparent',
						  'color': accept_text_color,
						  'border-style': accept_as_button ? accept_style : 'none', 
						  'border-width': accept_as_button ? accept_border_width + 'px' : '0',
						  'border-color': accept_as_button ? accept_border_color : 'transparent',
						  'border-radius': accept_as_button ? accept_border_radius + 'px' : '0',
						  'font-family': cookie_font,
						  'padding' : '8px 16px',
						  'font-size': button_font_size + 'px',
						  'font-weight': button_text_weight,
						  'width': accept_button_width === 'fit' ? 'fit-content' : '100%',
						  'min-width': accept_button_min_width + 'px',
						}"
				>
					{{ accept_text }}
				</a>
				<?php
				break;

			case 'accept_all':
				?>
				<a v-show="cookie_accept_all_on" 
				  href="#"
				  :style="{
						  'background-color': accept_all_as_button ? `${accept_all_background_color}${Math.floor(accept_all_opacity * 255).toString(16).toUpperCase()}` : 'transparent',
						  'color': accept_all_text_color,
						  'border-style': accept_all_as_button ? accept_all_style : 'none',
						  'border-width': accept_all_as_button ? accept_all_border_width + 'px' : '0',
						  'border-color': accept_all_as_button ? accept_all_border_color : 'transparent',
						  'border-radius': accept_all_as_button ? accept_all_border_radius + 'px' : '0',
						  'font-family': cookie_font,
						  'padding' : '8px 16px',
						  'font-size': button_font_size + 'px',
						  'font-weight': button_text_weight,
						  'width': accept_all_btn_width === 'fit' ? 'fit-content' : '100%',
						  'min-width': accept_all_btn_min_width + 'px',
						}"
				>
					{{ accept_all_text }}
				</a>
				<?php
				break;
		}
	}
}	
if ( ! function_exists( 'wplp_render_notice_button_ab_test' ) ) {
	function wplp_render_notice_button_ab_test( $button ) {
		$button = is_string( $button ) ? $button : '';

		// Supports: accept_all, acceptAll, accept-all
		$button = str_replace( '-', '_', $button );
		$button = strtolower( $button );

		if ( 'acceptall' === $button ) {
			$button = 'accept_all';
		}

		switch ( $button ) {
			case 'decline':
				?>
				<a v-show="this[`cookie_decline_on${active_test_banner_tab}`] && !((!is_auto_mode && (is_pipeda || is_au_app)) || (is_auto_mode && (banner_edit_law === 'pipeda' || banner_edit_law === 'au_app')))"
				  href="#"
				  :style="{
						  'background-color': this[`decline_as_button${active_test_banner_tab}`] ? `${this[`decline_background_color${active_test_banner_tab}`]}${Math.floor(this[`decline_opacity${active_test_banner_tab}`] * 255).toString(16).toUpperCase()}` : 'transparent',
						  'color': this[`decline_text_color${active_test_banner_tab}`],
						  'border-style': this[`decline_as_button${active_test_banner_tab}`] ? this[`decline_style${active_test_banner_tab}`] : 'none',
						  'border-width': this[`decline_as_button${active_test_banner_tab}`] ? this[`decline_border_width${active_test_banner_tab}`] + 'px' : '0',
						  'border-color': this[`decline_as_button${active_test_banner_tab}`] ? this[`decline_border_color${active_test_banner_tab}`] : 'transparent',
						  'border-radius': this[`decline_as_button${active_test_banner_tab}`] ? this[`decline_border_radius${active_test_banner_tab}`] + 'px' : '0',
						  'font-family': this[`cookie_font${active_test_banner_tab}`],
						  'padding' : '8px 16px',
						  'font-size': this[`button_font_size${active_test_banner_tab}`] + 'px',
						  'font-weight': this[`button_text_weight${active_test_banner_tab}`],
						  'width': this[`decline_button_width${active_test_banner_tab}`] === 'fit' ? 'fit-content' : '100%',
						  'min-width': this[`decline_button_min_width${active_test_banner_tab}`] + 'px',
						}"
				>
				  {{ decline_text }}
				</a>
				<?php
				break;

			case 'settings':
				?>
				<a v-show="this[`cookie_settings_on${active_test_banner_tab}`] && !((!is_auto_mode && is_eprivacy) || (is_auto_mode && banner_edit_law === 'eprivacy'))" id="cookie_action_settings_preview"
				  href="#"
				  :style="{
						  'background-color': this[`settings_as_button${active_test_banner_tab}`] ? `${this[`settings_background_color${active_test_banner_tab}`]}${Math.floor(this[`settings_opacity${active_test_banner_tab}`] * 255).toString(16).toUpperCase()}` : 'transparent',
						  'color': this[`settings_text_color${active_test_banner_tab}`],
						  'border-style': this[`settings_as_button${active_test_banner_tab}`] ? this[`settings_style${active_test_banner_tab}`] : 'none',
						  'border-width': this[`settings_as_button${active_test_banner_tab}`] ? this[`settings_border_width${active_test_banner_tab}`] + 'px' : '0',
						  'border-color': this[`settings_as_button${active_test_banner_tab}`] ? this[`settings_border_color${active_test_banner_tab}`] : 'transparent',
						  'border-radius': this[`settings_as_button${active_test_banner_tab}`] ? this[`settings_border_radius${active_test_banner_tab}`] + 'px' : '0',
						  'font-family': this[`cookie_font${active_test_banner_tab}`],
						  'padding' : '8px 16px',
						  'font-size': this[`button_font_size${active_test_banner_tab}`] + 'px',
						  'font-weight': this[`button_text_weight${active_test_banner_tab}`],
						  'width': this[`settings_button_width${active_test_banner_tab}`] === 'fit' ? 'fit-content' : '100%',
						  'min-width': this[`settings_button_min_width${active_test_banner_tab}`] + 'px',
						}"
				>
					{{ settings_text }}
				</a>
				<?php
				break;

			case 'accept':
				?>
				<a v-show="this[`cookie_accept_on${active_test_banner_tab}`] && !((!is_auto_mode && (is_pipeda || is_au_app)) || (is_auto_mode && (banner_edit_law === 'pipeda' || banner_edit_law === 'au_app')))" 
				  href="#"
				  :style="{
						  'background-color': this[`accept_as_button${active_test_banner_tab}`] ? `${this[`accept_background_color${active_test_banner_tab}`]}${Math.floor(this[`accept_opacity${active_test_banner_tab}`] * 255).toString(16).toUpperCase()}` : 'transparent',
						  'color': this[`accept_text_color${active_test_banner_tab}`],
						  'border-style': this[`accept_as_button${active_test_banner_tab}`] ? this[`accept_style${active_test_banner_tab}`] : 'none', 
						  'border-width': this[`accept_as_button${active_test_banner_tab}`] ? this[`accept_border_width${active_test_banner_tab}`] + 'px' : '0',
						  'border-color': this[`accept_as_button${active_test_banner_tab}`] ? this[`accept_border_color${active_test_banner_tab}`] : 'transparent',
						  'border-radius': this[`accept_as_button${active_test_banner_tab}`] ? this[`accept_border_radius${active_test_banner_tab}`] + 'px' : '0',
						  'font-family': this[`cookie_font${active_test_banner_tab}`],
						  'padding' : '8px 16px',
						  'font-size': this[`button_font_size${active_test_banner_tab}`] + 'px',
						  'font-weight': this[`button_text_weight${active_test_banner_tab}`],
						  'width': this[`accept_button_width${active_test_banner_tab}`] === 'fit' ? 'fit-content' : '100%',
						  'min-width': this[`accept_button_min_width${active_test_banner_tab}`] + 'px',
						}"
				>
					{{ accept_text }}
				</a>
				<?php
				break;

			case 'accept_all':
				?>
				<a v-show="this[`cookie_accept_all_on${active_test_banner_tab}`]" 
				  href="#"
				  :style="{
						  'background-color': this[`accept_all_as_button${active_test_banner_tab}`] ? `${this[`accept_all_background_color${active_test_banner_tab}`]}${Math.floor(this[`accept_all_opacity${active_test_banner_tab}`] * 255).toString(16).toUpperCase()}` : 'transparent',
						  'color': this[`accept_all_text_color${active_test_banner_tab}`],
						  'border-style': this[`accept_all_as_button${active_test_banner_tab}`] ? this[`accept_all_style${active_test_banner_tab}`] : 'none',
						  'border-width': this[`accept_all_as_button${active_test_banner_tab}`] ? this[`accept_all_border_width${active_test_banner_tab}`] + 'px' : '0',
						  'border-color': this[`accept_all_as_button${active_test_banner_tab}`] ? this[`accept_all_border_color${active_test_banner_tab}`] : 'transparent',
						  'border-radius': this[`accept_all_as_button${active_test_banner_tab}`] ? this[`accept_all_border_radius${active_test_banner_tab}`] + 'px' : '0',
						  'font-family': this[`cookie_font${active_test_banner_tab}`],
						  'padding' : '8px 16px',
						  'font-size': this[`button_font_size${active_test_banner_tab}`] + 'px',
						  'font-weight': this[`button_text_weight${active_test_banner_tab}`],
						  'width': this[`accept_all_btn_width${active_test_banner_tab}`] === 'fit' ? 'fit-content' : '100%',
						  'min-width': this[`accept_all_btn_min_width${active_test_banner_tab}`] + 'px',
						}"
				>
					{{ accept_all_text }}
				</a>
				<?php
				break;
		}
	}
}	
?>

<?php
$gdpr_no_of_page_scan_left       = $total_pages_scan_limit - get_option( 'gdpr_no_of_page_scan' );
$remaining_percentage_scan_limit = ( get_option( 'gdpr_no_of_page_scan' ) / $total_pages_scan_limit ) * 100;

?>
<div class="gdpr-cookie-consent-app-container" id="gdpr-cookie-consent-settings-app" >
	<!-- main preview container -->
	<div v-if="banner_preview_is_on">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/cookie_settings.php'; ?>
	</div>

	<!-- Preview banner code restructure -->
	<div v-show="banner_preview_is_on && show_cookie_as == 'popup'" class="gdpr-popup-overlay">
	</div>
	<?php if ( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true' ) { ?> 
		<!-- AB TESTING ENABLED -->
		<div v-show="banner_preview_is_on" class="notice-container" :class="{ 'notice-type-banner': show_cookie_as == 'banner', 'notice-type-popup': show_cookie_as == 'popup', 'notice-type-widget': show_cookie_as == 'widget', 'banner-top': cookie_position == 'top' && show_cookie_as == 'banner' ,'banner-bottom': cookie_position == 'bottom' && show_cookie_as == 'banner', 'widget-left': cookie_widget_position == 'left' && show_cookie_as == 'widget','widget-right': cookie_widget_position == 'right' && show_cookie_as == 'widget', 'widget-top-right': cookie_widget_position == 'top_right' && show_cookie_as == 'widget', 'widget-top-left': cookie_widget_position == 'top_left' && show_cookie_as == 'widget', 'new_default_banner': template == 'new_default' }"
		  :style="{
		  	'background-color': this[`cookie_bar_color${active_test_banner_tab}`] + Math.floor(this[`cookie_bar_opacity${active_test_banner_tab}`] * 255).toString(16).toUpperCase(),
			'--vendor-link-color': this[`accept_all_as_button${active_test_banner_tab}`] ? `${this[`accept_all_background_color${active_test_banner_tab}`]}` : this[`accept_all_text_color${active_test_banner_tab}`],
			'color': this[`cookie_text_color${active_test_banner_tab}`],
		  	'border-style': this[`border_style${active_test_banner_tab}`],
			'border-width': this[`cookie_bar_border_width${active_test_banner_tab}`] + 'px',
			'border-radius': this[`cookie_bar_border_radius${active_test_banner_tab}`] + 'px',
			'border-color': this[`cookie_border_color${active_test_banner_tab}`],
			'padding': show_cookie_as != 'banner' ? this[`cookie_bar_padding${active_test_banner_tab}`] + 'px' : undefined,
			'padding-inline': show_cookie_as == 'banner' ? this[`cookie_bar_horizontal_padding${active_test_banner_tab}`] + 'px' : undefined,
			'padding-block': show_cookie_as == 'banner' ? this[`cookie_bar_vertical_padding${active_test_banner_tab}`] + 'px' : undefined,
			'gap': this[`cookie_bar_spacing${active_test_banner_tab}`] + 'px',
			'backdrop-filter': cookie_bar_blur > 0 ? `blur(${this[`cookie_bar_blur${active_test_banner_tab}`] * 20}px)` : undefined,
			'box-shadow': `${this[`cookie_bar_shadow_size${active_test_banner_tab}`]}px ${this[`cookie_bar_shadow_size${active_test_banner_tab}`]}px ${this[`cookie_bar_shadow_size${active_test_banner_tab}`]*2}px ${this[`cookie_bar_shadow_color${active_test_banner_tab}`]}${Math.floor(0.5 * 255).toString(16).toUpperCase()}`,
			...(is_us_state_laws ? { 'padding-bottom': '35px' } : {})
			}"
		>
			
			<span v-if="this[`bypass_button_is_on${active_test_banner_tab}`]" :style="{ 'border': 'none', 'cursor': 'pointer', 'display':'inline-flex','justify-content': 'center', 'align-items': 'center', 'height':'20px', 'width': '20px', 'position': 'absolute', 'top': '5px', 'right': (parseInt(this[`cookie_bar_border_radius${active_test_banner_tab}`])/3 + 10) + 'px', 'border-radius': '50%','color': this[`bypass_button_text_color${active_test_banner_tab}`], 'background-color':'transparent', 'scale': this[`bypass_button_size${active_test_banner_tab}`] == 'lg' ? '110%' : this[`bypass_button_size${active_test_banner_tab}`] == 'sm' ? '90%' : '100%' }" @click="turnOffPreviewBanner">
				<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z" fill="currentColor"/>
				</svg>
			</span>
			<div class="notice-content-header" style="width: 100%; flex-direction: <?php echo esc_attr($banner_layouts['c1']['direction'] ?? 'row') == 'col' ? ($banner_structure['c1'][0] === 'logo' ? 'column' : 'column-reverse') : ($banner_structure['c1'][0] === 'logo' ? 'row' : 'row-reverse'); ?>; <?php echo $banner_layouts['c1']['direction'] === 'row' ? 'align-items: center; justify-content: ' . ($banner_layouts['c1']['justify'] ===  'between' ? 'space-between' : esc_attr($banner_layouts['c1']['justify'] ?? '')) : 'align-items: ' . esc_attr($banner_layouts['c1']['justify'] ?? '') ?>">
				<div class="notice-logo-container">
					<div v-if="logo_is_on1 && active_test_banner_tab == 1">
					<?php
						$get_banner_img1 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1 );
						if ( ! empty( $get_banner_img1 ) ) {
						?>
							<img v-if="use_uploaded_logo1" class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img1 ); ?>"
							  :style="{
							  	'margin-left': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['margin-left'],
								'width': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['fit-content'],
								'height': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['height'],
								'transform': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['transform'],
								'position': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['position'],
								'z-index': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['z-index']
							  }" >
						<?php
						}
					?>
					<img v-if="!use_uploaded_logo1" alt="Logo image" class="gdpr_logo_image" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL . 'includes/templates/logo_images/banner_' . sanitize_file_name( $the_options['default_logo1'] ) . '.svg' ); ?>">
					</div>
					<div v-if="logo_is_on2 && active_test_banner_tab == 2">
					<?php
						$get_banner_img2 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD2 );
						if ( ! empty( $get_banner_img2 ) ) {
						?>
							<img v-if="use_uploaded_logo2" class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img2 ); ?>"
							:style="{
							  	'margin-left': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['margin-left'],
								'width': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['fit-content'],
								'height': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['height'],
								'transform': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['transform'],
								'position': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['position'],
								'z-index': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['z-index']
							  }"  >
						<?php
						}
					?>
					<img v-if="!use_uploaded_logo2" alt="Logo image" class="gdpr_logo_image" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL . 'includes/templates/logo_images/banner_' . sanitize_file_name( $the_options['default_logo2'] ) . '.svg' ); ?>">
					</div>
				</div>
				<div class="notice-heading-wrapper" v-if="this[`heading_is_on${active_test_banner_tab}`]" :style = "{
					'color': this[`cookie_heading_color${active_test_banner_tab}`],
					'font-size': this[`heading_text_size${active_test_banner_tab}`] + 'px',
					'font-weight': this[`heading_text_weight${active_test_banner_tab}`],
				}">
					<h3 v-if="gdpr_message_heading.length>0">{{gdpr_message_heading}}</h3>
				</div>	
			</div>

				
				
				<div class="notice-content-body" style="width: 100%;" :class="'notice-template-name-' + (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.name + ' template-' + (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['static-settings']?.['layout']">
					<div style="display: flex; flex-direction: column; gap:10px;">
						<p :style="{
							'color': this[`cookie_text_color${active_test_banner_tab}`],
							'font-size': this[`cookie_font_size${active_test_banner_tab}`] + 'px',
							'font-weight': this[`cookie_text_weight${active_test_banner_tab}`],
							'text-align': this[`banner_text_alignment${active_test_banner_tab}`],
						}">	
							<span :style="{'font-family': this[`cookie_font${active_test_banner_tab}`]}" v-show="(!is_auto_mode && is_gdpr) || (is_auto_mode && banner_edit_law === 'gdpr')" v-html ="gdpr_message"></span>
							<span :style="{'font-family': this[`cookie_font${active_test_banner_tab}`]}" v-show="(!is_auto_mode && is_lgpd) || (is_auto_mode && banner_edit_law === 'lgpd')" v-html ="lgpd_message"></span>
							<span :style="{'font-family': this[`cookie_font${active_test_banner_tab}`]}" v-show="(!is_auto_mode && is_eprivacy) || (is_auto_mode && banner_edit_law === 'eprivacy')" v-html ="eprivacy_message"></span>
							<span :style="{'font-family': this[`cookie_font${active_test_banner_tab}`]}" v-show="(!is_auto_mode && is_uk_gdpr) || (is_auto_mode && banner_edit_law === 'uk_gdpr')" v-html ="uk_gdpr_message"></span>
							<span :style="{'font-family': this[`cookie_font${active_test_banner_tab}`]}" v-show="(!is_auto_mode && is_sa_pdpl) || (is_auto_mode && banner_edit_law === 'sa_pdpl')" v-html ="pdpl_message"></span>
							<span :style="{'font-family': this[`cookie_font${active_test_banner_tab}`]}" v-show="(!is_auto_mode && is_pipeda) || (is_auto_mode && banner_edit_law === 'pipeda')" v-html ="pipeda_message"></span>
							<span :style="{'font-family': this[`cookie_font${active_test_banner_tab}`]}" v-show="(!is_auto_mode && is_au_app) || (is_auto_mode && banner_edit_law === 'au_app')" v-html ="app_message"></span>
							<span :style="{'font-family': this[`cookie_font${active_test_banner_tab}`]}" v-show="(!is_auto_mode && is_us_state_laws && us_state_laws_edit_law === 'ccpa') || (is_auto_mode && banner_edit_law === 'us_state_laws' && us_state_laws_edit_law === 'ccpa')" v-html ="ccpa_message"></span>
							<span :style="{'font-family': this[`cookie_font${active_test_banner_tab}`]}" v-show="(!is_auto_mode && is_us_state_laws && us_state_laws_edit_law === 'default_opt_out') || (is_auto_mode && banner_edit_law === 'us_state_laws' && us_state_laws_edit_law === 'default_opt_out')" v-html ="default_opt_out_message"></span>
							<span :style="{'font-family': this[`cookie_font${active_test_banner_tab}`]}" v-show="(!is_auto_mode && is_us_state_laws && us_state_laws_edit_law === 'pure_opt_out') || (is_auto_mode && banner_edit_law === 'us_state_laws' && us_state_laws_edit_law === 'pure_opt_out')" v-html ="pure_opt_out_message"></span>
							<a v-if="this[`button_readmore_is_on${active_test_banner_tab}`] && !((!is_auto_mode && is_us_state_laws) || (is_auto_mode && banner_edit_law === 'us_state_laws'))" :style="{
								'font-family': this[`cookie_font${active_test_banner_tab}`],
								'color':this[`button_readmore_link_color${active_test_banner_tab}`],
								'cursor':'pointer',
							}" >
								<span>{{ button_readmore_text }}</span>
							</a>
							<a id="cookie_action_opt_out_preview" v-if="(!is_auto_mode && is_us_state_laws) || (is_auto_mode && banner_edit_law === 'us_state_laws')" :style="{'font-family': this[`cookie_font${active_test_banner_tab}`],'color': this[`opt_out_text_color${active_test_banner_tab}`],'cursor':'pointer'}"><span>{{ opt_out_text }}</span></a>
						</p>
					</div>
					

					<div v-if="ab_testing_enabled && !((!is_auto_mode && is_us_state_laws) || (is_auto_mode && banner_edit_law === 'us_state_laws'))" class="notice-buttons-wrapper" :style="{'gap': this[`cookie_bar_spacing${active_test_banner_tab}`] * 2 + 'px'}" style="display: flex; flex-direction: <?php echo esc_attr($banner_layouts['c4']['direction'] ?? 'row') == 'col' ? 'column' : 'row'; ?>; <?php echo $banner_layouts['c2']['direction'] == 'row' ? 'width: 40%' : '' ;?>; margin-top: <?php echo esc_attr($banner_layouts['c2']['direction'] ?? 'row') == 'col' ? '5px' : '0px'; ?>;">
					<div class="notice-left-buttons" :style="{'gap': this[`cookie_bar_spacing${active_test_banner_tab}`] * 2 + 'px', display: this[`visible_c5_items${active_test_banner_tab}`].length > 0 ? 'flex' : 'none', width: '100%'}" style=" flex-direction: <?php echo esc_attr($banner_layouts['c5']['direction'] ?? 'row') == 'col' ? 'column' : 'row'; ?>; <?php echo $banner_layouts['c5']['direction'] === 'row' ? 'align-items: center; justify-content: ' . ($banner_layouts['c5']['justify'] ===  'between' ? 'space-between' : esc_attr($banner_layouts['c5']['justify'] ?? '')) : 'align-items: ' . esc_attr($banner_layouts['c5']['justify'] ?? '') ?>">
						
						<?php
						foreach ( $c5_buttons as $button ) {
							wplp_render_notice_button_ab_test( $button );
						}
						?>
						
					</div>

					<div class="notice-right-buttons" :style="{'gap': this[`cookie_bar_spacing${active_test_banner_tab}`] * 2 + 'px', display: this[`visible_c6_items${active_test_banner_tab}`].length > 0 ? 'flex' : 'none', width: '100%'}" style=" flex-direction: <?php echo esc_attr($banner_layouts['c6']['direction'] ?? 'row') == 'col' ? 'column' : 'row'; ?>; <?php echo $banner_layouts['c6']['direction'] === 'row' ? 'align-items: center; justify-content: ' . ($banner_layouts['c6']['justify'] ===  'between' ? 'space-between' : esc_attr($banner_layouts['c6']['justify'] ?? '')) : 'align-items: ' . esc_attr($banner_layouts['c6']['justify'] ?? '') ?>">
						
						<?php
						foreach ( $c6_buttons as $button ) {
							wplp_render_notice_button_ab_test( $button );
						}
						?>

						
					</div>
				</div>			
				</div>
				<div v-show="show_credits" class="powered-by-credits"  :style="{'--popup_accent_color': cookieSettingsPopupAccentColor, 'text-align':'center', 'font-size': '10px', 'margin-bottom':'-10px'}"><svg width="152" height="18" viewBox="0 0 152 18" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M4.13672 9.49805H1.85742V8.57812H4.13672C4.57812 8.57812 4.93555 8.50781 5.20898 8.36719C5.48242 8.22656 5.68164 8.03125 5.80664 7.78125C5.93555 7.53125 6 7.24609 6 6.92578C6 6.63281 5.93555 6.35742 5.80664 6.09961C5.68164 5.8418 5.48242 5.63477 5.20898 5.47852C4.93555 5.31836 4.57812 5.23828 4.13672 5.23828H2.12109V12.8438H0.990234V4.3125H4.13672C4.78125 4.3125 5.32617 4.42383 5.77148 4.64648C6.2168 4.86914 6.55469 5.17773 6.78516 5.57227C7.01562 5.96289 7.13086 6.41016 7.13086 6.91406C7.13086 7.46094 7.01562 7.92773 6.78516 8.31445C6.55469 8.70117 6.2168 8.99609 5.77148 9.19922C5.32617 9.39844 4.78125 9.49805 4.13672 9.49805ZM8.03906 9.74414V9.60938C8.03906 9.15234 8.10547 8.72852 8.23828 8.33789C8.37109 7.94336 8.5625 7.60156 8.8125 7.3125C9.0625 7.01953 9.36523 6.79297 9.7207 6.63281C10.0762 6.46875 10.4746 6.38672 10.916 6.38672C11.3613 6.38672 11.7617 6.46875 12.1172 6.63281C12.4766 6.79297 12.7812 7.01953 13.0312 7.3125C13.2852 7.60156 13.4785 7.94336 13.6113 8.33789C13.7441 8.72852 13.8105 9.15234 13.8105 9.60938V9.74414C13.8105 10.2012 13.7441 10.625 13.6113 11.0156C13.4785 11.4062 13.2852 11.748 13.0312 12.041C12.7812 12.3301 12.4785 12.5566 12.123 12.7207C11.7715 12.8809 11.373 12.9609 10.9277 12.9609C10.4824 12.9609 10.082 12.8809 9.72656 12.7207C9.37109 12.5566 9.06641 12.3301 8.8125 12.041C8.5625 11.748 8.37109 11.4062 8.23828 11.0156C8.10547 10.625 8.03906 10.2012 8.03906 9.74414ZM9.12305 9.60938V9.74414C9.12305 10.0605 9.16016 10.3594 9.23438 10.6406C9.30859 10.918 9.41992 11.1641 9.56836 11.3789C9.7207 11.5938 9.91016 11.7637 10.1367 11.8887C10.3633 12.0098 10.627 12.0703 10.9277 12.0703C11.2246 12.0703 11.4844 12.0098 11.707 11.8887C11.9336 11.7637 12.1211 11.5938 12.2695 11.3789C12.418 11.1641 12.5293 10.918 12.6035 10.6406C12.6816 10.3594 12.7207 10.0605 12.7207 9.74414V9.60938C12.7207 9.29688 12.6816 9.00195 12.6035 8.72461C12.5293 8.44336 12.416 8.19531 12.2637 7.98047C12.1152 7.76172 11.9277 7.58984 11.7012 7.46484C11.4785 7.33984 11.2168 7.27734 10.916 7.27734C10.6191 7.27734 10.3574 7.33984 10.1309 7.46484C9.9082 7.58984 9.7207 7.76172 9.56836 7.98047C9.41992 8.19531 9.30859 8.44336 9.23438 8.72461C9.16016 9.00195 9.12305 9.29688 9.12305 9.60938ZM16.7754 11.7188L18.4043 6.50391H19.1191L18.9785 7.54102L17.3203 12.8438H16.623L16.7754 11.7188ZM15.6797 6.50391L17.0684 11.7773L17.168 12.8438H16.4355L14.5957 6.50391H15.6797ZM20.6777 11.7363L22.002 6.50391H23.0801L21.2402 12.8438H20.5137L20.6777 11.7363ZM19.2773 6.50391L20.8711 11.6309L21.0527 12.8438H20.3613L18.6562 7.5293L18.5156 6.50391H19.2773ZM26.8242 12.9609C26.3828 12.9609 25.9824 12.8867 25.623 12.7383C25.2676 12.5859 24.9609 12.373 24.7031 12.0996C24.4492 11.8262 24.2539 11.502 24.1172 11.127C23.9805 10.752 23.9121 10.3418 23.9121 9.89648V9.65039C23.9121 9.13477 23.9883 8.67578 24.1406 8.27344C24.293 7.86719 24.5 7.52344 24.7617 7.24219C25.0234 6.96094 25.3203 6.74805 25.6523 6.60352C25.9844 6.45898 26.3281 6.38672 26.6836 6.38672C27.1367 6.38672 27.5273 6.46484 27.8555 6.62109C28.1875 6.77734 28.459 6.99609 28.6699 7.27734C28.8809 7.55469 29.0371 7.88281 29.1387 8.26172C29.2402 8.63672 29.291 9.04688 29.291 9.49219V9.97852H24.5566V9.09375H28.207V9.01172C28.1914 8.73047 28.1328 8.45703 28.0312 8.19141C27.9336 7.92578 27.7773 7.70703 27.5625 7.53516C27.3477 7.36328 27.0547 7.27734 26.6836 7.27734C26.4375 7.27734 26.2109 7.33008 26.0039 7.43555C25.7969 7.53711 25.6191 7.68945 25.4707 7.89258C25.3223 8.0957 25.207 8.34375 25.125 8.63672C25.043 8.92969 25.002 9.26758 25.002 9.65039V9.89648C25.002 10.1973 25.043 10.4805 25.125 10.7461C25.2109 11.0078 25.334 11.2383 25.4941 11.4375C25.6582 11.6367 25.8555 11.793 26.0859 11.9062C26.3203 12.0195 26.5859 12.0762 26.8828 12.0762C27.2656 12.0762 27.5898 11.998 27.8555 11.8418C28.1211 11.6855 28.3535 11.4766 28.5527 11.2148L29.209 11.7363C29.0723 11.9434 28.8984 12.1406 28.6875 12.3281C28.4766 12.5156 28.2168 12.668 27.9082 12.7852C27.6035 12.9023 27.2422 12.9609 26.8242 12.9609ZM31.6406 7.5V12.8438H30.5566V6.50391H31.6113L31.6406 7.5ZM33.6211 6.46875L33.6152 7.47656C33.5254 7.45703 33.4395 7.44531 33.3574 7.44141C33.2793 7.43359 33.1895 7.42969 33.0879 7.42969C32.8379 7.42969 32.6172 7.46875 32.4258 7.54688C32.2344 7.625 32.0723 7.73438 31.9395 7.875C31.8066 8.01562 31.7012 8.18359 31.623 8.37891C31.5488 8.57031 31.5 8.78125 31.4766 9.01172L31.1719 9.1875C31.1719 8.80469 31.209 8.44531 31.2832 8.10938C31.3613 7.77344 31.4805 7.47656 31.6406 7.21875C31.8008 6.95703 32.0039 6.75391 32.25 6.60938C32.5 6.46094 32.7969 6.38672 33.1406 6.38672C33.2188 6.38672 33.3086 6.39648 33.4102 6.41602C33.5117 6.43164 33.582 6.44922 33.6211 6.46875ZM37.1484 12.9609C36.707 12.9609 36.3066 12.8867 35.9473 12.7383C35.5918 12.5859 35.2852 12.373 35.0273 12.0996C34.7734 11.8262 34.5781 11.502 34.4414 11.127C34.3047 10.752 34.2363 10.3418 34.2363 9.89648V9.65039C34.2363 9.13477 34.3125 8.67578 34.4648 8.27344C34.6172 7.86719 34.8242 7.52344 35.0859 7.24219C35.3477 6.96094 35.6445 6.74805 35.9766 6.60352C36.3086 6.45898 36.6523 6.38672 37.0078 6.38672C37.4609 6.38672 37.8516 6.46484 38.1797 6.62109C38.5117 6.77734 38.7832 6.99609 38.9941 7.27734C39.2051 7.55469 39.3613 7.88281 39.4629 8.26172C39.5645 8.63672 39.6152 9.04688 39.6152 9.49219V9.97852H34.8809V9.09375H38.5312V9.01172C38.5156 8.73047 38.457 8.45703 38.3555 8.19141C38.2578 7.92578 38.1016 7.70703 37.8867 7.53516C37.6719 7.36328 37.3789 7.27734 37.0078 7.27734C36.7617 7.27734 36.5352 7.33008 36.3281 7.43555C36.1211 7.53711 35.9434 7.68945 35.7949 7.89258C35.6465 8.0957 35.5312 8.34375 35.4492 8.63672C35.3672 8.92969 35.3262 9.26758 35.3262 9.65039V9.89648C35.3262 10.1973 35.3672 10.4805 35.4492 10.7461C35.5352 11.0078 35.6582 11.2383 35.8184 11.4375C35.9824 11.6367 36.1797 11.793 36.4102 11.9062C36.6445 12.0195 36.9102 12.0762 37.207 12.0762C37.5898 12.0762 37.9141 11.998 38.1797 11.8418C38.4453 11.6855 38.6777 11.4766 38.877 11.2148L39.5332 11.7363C39.3965 11.9434 39.2227 12.1406 39.0117 12.3281C38.8008 12.5156 38.541 12.668 38.2324 12.7852C37.9277 12.9023 37.5664 12.9609 37.1484 12.9609ZM44.877 11.6133V3.84375H45.9668V12.8438H44.9707L44.877 11.6133ZM40.6113 9.74414V9.62109C40.6113 9.13672 40.6699 8.69727 40.7871 8.30273C40.9082 7.9043 41.0781 7.5625 41.2969 7.27734C41.5195 6.99219 41.7832 6.77344 42.0879 6.62109C42.3965 6.46484 42.7402 6.38672 43.1191 6.38672C43.5176 6.38672 43.8652 6.45703 44.1621 6.59766C44.4629 6.73438 44.7168 6.93555 44.9238 7.20117C45.1348 7.46289 45.3008 7.7793 45.4219 8.15039C45.543 8.52148 45.627 8.94141 45.6738 9.41016V9.94922C45.6309 10.4141 45.5469 10.832 45.4219 11.2031C45.3008 11.5742 45.1348 11.8906 44.9238 12.1523C44.7168 12.4141 44.4629 12.6152 44.1621 12.7559C43.8613 12.8926 43.5098 12.9609 43.1074 12.9609C42.7363 12.9609 42.3965 12.8809 42.0879 12.7207C41.7832 12.5605 41.5195 12.3359 41.2969 12.0469C41.0781 11.7578 40.9082 11.418 40.7871 11.0273C40.6699 10.6328 40.6113 10.2051 40.6113 9.74414ZM41.7012 9.62109V9.74414C41.7012 10.0605 41.7324 10.3574 41.7949 10.6348C41.8613 10.9121 41.9629 11.1562 42.0996 11.3672C42.2363 11.5781 42.4102 11.7441 42.6211 11.8652C42.832 11.9824 43.084 12.041 43.377 12.041C43.7363 12.041 44.0312 11.9648 44.2617 11.8125C44.4961 11.6602 44.6836 11.459 44.8242 11.209C44.9648 10.959 45.0742 10.6875 45.1523 10.3945V8.98242C45.1055 8.76758 45.0371 8.56055 44.9473 8.36133C44.8613 8.1582 44.748 7.97852 44.6074 7.82227C44.4707 7.66211 44.3008 7.53516 44.0977 7.44141C43.8984 7.34766 43.6621 7.30078 43.3887 7.30078C43.0918 7.30078 42.8359 7.36328 42.6211 7.48828C42.4102 7.60938 42.2363 7.77734 42.0996 7.99219C41.9629 8.20312 41.8613 8.44922 41.7949 8.73047C41.7324 9.00781 41.7012 9.30469 41.7012 9.62109ZM50.625 3.84375H51.7148V11.6133L51.6211 12.8438H50.625V3.84375ZM55.998 9.62109V9.74414C55.998 10.2051 55.9434 10.6328 55.834 11.0273C55.7246 11.418 55.5645 11.7578 55.3535 12.0469C55.1426 12.3359 54.8848 12.5605 54.5801 12.7207C54.2754 12.8809 53.9258 12.9609 53.5312 12.9609C53.1289 12.9609 52.7754 12.8926 52.4707 12.7559C52.1699 12.6152 51.916 12.4141 51.709 12.1523C51.502 11.8906 51.3359 11.5742 51.2109 11.2031C51.0898 10.832 51.0059 10.4141 50.959 9.94922V9.41016C51.0059 8.94141 51.0898 8.52148 51.2109 8.15039C51.3359 7.7793 51.502 7.46289 51.709 7.20117C51.916 6.93555 52.1699 6.73438 52.4707 6.59766C52.7715 6.45703 53.1211 6.38672 53.5195 6.38672C53.918 6.38672 54.2715 6.46484 54.5801 6.62109C54.8887 6.77344 55.1465 6.99219 55.3535 7.27734C55.5645 7.5625 55.7246 7.9043 55.834 8.30273C55.9434 8.69727 55.998 9.13672 55.998 9.62109ZM54.9082 9.74414V9.62109C54.9082 9.30469 54.8789 9.00781 54.8203 8.73047C54.7617 8.44922 54.668 8.20312 54.5391 7.99219C54.4102 7.77734 54.2402 7.60938 54.0293 7.48828C53.8184 7.36328 53.5586 7.30078 53.25 7.30078C52.9766 7.30078 52.7383 7.34766 52.5352 7.44141C52.3359 7.53516 52.166 7.66211 52.0254 7.82227C51.8848 7.97852 51.7695 8.1582 51.6797 8.36133C51.5938 8.56055 51.5293 8.76758 51.4863 8.98242V10.3945C51.5488 10.668 51.6504 10.9316 51.791 11.1855C51.9355 11.4355 52.127 11.6406 52.3652 11.8008C52.6074 11.9609 52.9062 12.041 53.2617 12.041C53.5547 12.041 53.8047 11.9824 54.0117 11.8652C54.2227 11.7441 54.3926 11.5781 54.5215 11.3672C54.6543 11.1562 54.752 10.9121 54.8145 10.6348C54.877 10.3574 54.9082 10.0605 54.9082 9.74414ZM59.0918 12.1875L60.8555 6.50391H62.0156L59.4727 13.8223C59.4141 13.9785 59.3359 14.1465 59.2383 14.3262C59.1445 14.5098 59.0234 14.6836 58.875 14.8477C58.7266 15.0117 58.5469 15.1445 58.3359 15.2461C58.1289 15.3516 57.8809 15.4043 57.5918 15.4043C57.5059 15.4043 57.3965 15.3926 57.2637 15.3691C57.1309 15.3457 57.0371 15.3262 56.9824 15.3105L56.9766 14.4316C57.0078 14.4355 57.0566 14.4395 57.123 14.4434C57.1934 14.4512 57.2422 14.4551 57.2695 14.4551C57.5156 14.4551 57.7246 14.4219 57.8965 14.3555C58.0684 14.293 58.2129 14.1855 58.3301 14.0332C58.4512 13.8848 58.5547 13.6797 58.6406 13.418L59.0918 12.1875ZM57.7969 6.50391L59.4434 11.4258L59.7246 12.5684L58.9453 12.9668L56.6133 6.50391H57.7969Z" fill="#71717A"/>
				<path d="M88.7969 16.1569V11.1736H90.5713C90.9586 11.1736 91.2794 11.2442 91.5338 11.3853C91.7883 11.5264 91.9787 11.7195 92.1051 11.9644C92.2314 12.2077 92.2946 12.4819 92.2946 12.7869C92.2946 13.0934 92.2306 13.3692 92.1026 13.6142C91.9762 13.8575 91.785 14.0505 91.529 14.1933C91.2746 14.3344 90.9545 14.405 90.5689 14.405H89.3486V13.7675H90.5008C90.7455 13.7675 90.944 13.7253 91.0963 13.6409C91.2486 13.555 91.3604 13.4382 91.4317 13.2905C91.503 13.1429 91.5387 12.975 91.5387 12.7869C91.5387 12.5987 91.503 12.4316 91.4317 12.2856C91.3604 12.1396 91.2478 12.0252 91.0939 11.9425C90.9416 11.8598 90.7406 11.8184 90.4911 11.8184H89.548V16.1569H88.7969Z" fill="#71717A"/>
				<path d="M97.0767 16.1569V11.1736H97.8278V15.5097H100.083V16.1569H97.0767Z" fill="#71717A"/>
				<path d="M105.516 16.1569H104.719L106.511 11.1736H107.378L109.17 16.1569H108.372L106.965 12.0788H106.926L105.516 16.1569ZM105.65 14.2055H108.236V14.8381H105.65V14.2055Z" fill="#71717A"/>
				<path d="M112.956 11.8208V11.1736H116.809V11.8208H115.256V16.1569H114.507V11.8208H112.956Z" fill="#71717A"/>
				<path d="M121.562 16.1569V11.1736H124.649V11.8208H122.313V13.3392H124.428V13.984H122.313V16.1569H121.562Z" fill="#71717A"/>
				<path d="M133.798 13.6653C133.798 14.1973 133.701 14.6548 133.507 15.0376C133.312 15.4188 133.046 15.7125 132.707 15.9185C132.37 16.1229 131.987 16.2251 131.557 16.2251C131.126 16.2251 130.741 16.1229 130.403 15.9185C130.066 15.7125 129.8 15.418 129.605 15.0352C129.411 14.6524 129.314 14.1957 129.314 13.6653C129.314 13.1332 129.411 12.6765 129.605 12.2953C129.8 11.9125 130.066 11.6189 130.403 11.4145C130.741 11.2085 131.126 11.1055 131.557 11.1055C131.987 11.1055 132.37 11.2085 132.707 11.4145C133.046 11.6189 133.312 11.9125 133.507 12.2953C133.701 12.6765 133.798 13.1332 133.798 13.6653ZM133.055 13.6653C133.055 13.2597 132.989 12.9183 132.858 12.6409C132.728 12.3618 132.55 12.151 132.323 12.0082C132.098 11.8638 131.843 11.7917 131.557 11.7917C131.27 11.7917 131.014 11.8638 130.789 12.0082C130.564 12.151 130.386 12.3618 130.254 12.6409C130.125 12.9183 130.06 13.2597 130.06 13.6653C130.06 14.0708 130.125 14.4131 130.254 14.6921C130.386 14.9695 130.564 15.1804 130.789 15.3248C131.014 15.4675 131.27 15.5389 131.557 15.5389C131.843 15.5389 132.098 15.4675 132.323 15.3248C132.55 15.1804 132.728 14.9695 132.858 14.6921C132.989 14.4131 133.055 14.0708 133.055 13.6653Z" fill="#71717A"/>
				<path d="M138.636 16.1569V11.1736H140.411C140.796 11.1736 141.117 11.2401 141.371 11.3731C141.627 11.5061 141.818 11.6903 141.945 11.9255C142.071 12.1591 142.134 12.4292 142.134 12.7358C142.134 13.0407 142.07 13.3092 141.942 13.5412C141.816 13.7715 141.625 13.9508 141.368 14.0789C141.114 14.2071 140.794 14.2712 140.408 14.2712H139.064V13.6239H140.34C140.583 13.6239 140.781 13.589 140.933 13.5193C141.087 13.4495 141.2 13.3481 141.271 13.2151C141.343 13.0821 141.378 12.9223 141.378 12.7358C141.378 12.5476 141.342 12.3846 141.269 12.2467C141.198 12.1088 141.085 12.0033 140.931 11.9303C140.779 11.8557 140.579 11.8184 140.331 11.8184H139.387V16.1569H138.636ZM141.094 13.9086L142.324 16.1569H141.468L140.263 13.9086H141.094Z" fill="#71717A"/>
				<path d="M146.95 11.1736H147.861L149.446 15.0474H149.504L151.089 11.1736H152.001V16.1569H151.286V12.5508H151.24L149.772 16.1496H149.179L147.71 12.5484H147.664V16.1569H146.95V11.1736Z" fill="#71717A"/>
				<path d="M94.2056 4.00665H92.5576C92.5357 3.83746 92.4906 3.68474 92.4225 3.54851C92.3544 3.41228 92.2643 3.29582 92.1522 3.19914C92.0402 3.10246 91.9072 3.02884 91.7534 2.97831C91.6018 2.92557 91.4337 2.8992 91.2491 2.8992C90.9217 2.8992 90.6394 2.97941 90.4021 3.13981C90.1669 3.30021 89.9857 3.53203 89.8582 3.83526C89.733 4.13849 89.6703 4.50544 89.6703 4.93612C89.6703 5.38437 89.7341 5.76011 89.8615 6.06334C89.9912 6.36437 90.1724 6.59179 90.4053 6.74561C90.6405 6.89722 90.9184 6.97303 91.2392 6.97303C91.4194 6.97303 91.5831 6.94996 91.7303 6.90381C91.8798 6.85767 92.0105 6.79065 92.1226 6.70276C92.2368 6.61267 92.3302 6.5039 92.4027 6.37646C92.4774 6.24681 92.5291 6.10069 92.5576 5.93809L94.2056 5.94798C94.1771 6.24681 94.0903 6.54126 93.9453 6.8313C93.8024 7.12135 93.6058 7.38612 93.3553 7.62563C93.1048 7.86294 92.7993 8.05191 92.439 8.19254C92.0808 8.33317 91.6699 8.40348 91.2063 8.40348C90.5954 8.40348 90.0483 8.26944 89.5649 8.00137C89.0837 7.7311 88.7035 7.33778 88.4245 6.82141C88.1454 6.30504 88.0059 5.67661 88.0059 4.93612C88.0059 4.19342 88.1476 3.56389 88.431 3.04752C88.7145 2.53115 89.0979 2.13893 89.5813 1.87086C90.0648 1.60279 90.6064 1.46875 91.2063 1.46875C91.615 1.46875 91.9929 1.52588 92.3401 1.64014C92.6873 1.7522 92.9927 1.917 93.2564 2.13454C93.5201 2.34987 93.7343 2.61465 93.8991 2.92887C94.0639 3.24308 94.1661 3.60234 94.2056 4.00665Z" fill="#71717A"/>
				<path d="M101.331 4.93612C101.331 5.67881 101.188 6.30834 100.902 6.82471C100.616 7.34108 100.23 7.7333 99.7419 8.00137C99.2563 8.26944 98.7114 8.40348 98.1071 8.40348C97.5007 8.40348 96.9546 8.26835 96.469 7.99808C95.9834 7.72781 95.5978 7.33559 95.3121 6.82141C95.0287 6.30504 94.8869 5.67661 94.8869 4.93612C94.8869 4.19342 95.0287 3.56389 95.3121 3.04752C95.5978 2.53115 95.9834 2.13893 96.469 1.87086C96.9546 1.60279 97.5007 1.46875 98.1071 1.46875C98.7114 1.46875 99.2563 1.60279 99.7419 1.87086C100.23 2.13893 100.616 2.53115 100.902 3.04752C101.188 3.56389 101.331 4.19342 101.331 4.93612ZM99.6628 4.93612C99.6628 4.49665 99.6002 4.12531 99.475 3.82208C99.3519 3.51885 99.1739 3.28923 98.941 3.13322C98.7103 2.97721 98.4323 2.8992 98.1071 2.8992C97.7841 2.8992 97.5062 2.97721 97.2732 3.13322C97.0403 3.28923 96.8612 3.51885 96.736 3.82208C96.6129 4.12531 96.5514 4.49665 96.5514 4.93612C96.5514 5.37558 96.6129 5.74693 96.736 6.05016C96.8612 6.35338 97.0403 6.583 97.2732 6.73901C97.5062 6.89502 97.7841 6.97303 98.1071 6.97303C98.4323 6.97303 98.7103 6.89502 98.941 6.73901C99.1739 6.583 99.3519 6.35338 99.475 6.05016C99.6002 5.74693 99.6628 5.37558 99.6628 4.93612Z" fill="#71717A"/>
				<path d="M102.03 1.56104H104.051L105.765 5.74033H105.844L107.558 1.56104H109.578V8.31119H107.989V4.16486H107.933L106.312 8.26835H105.297L103.675 4.14179H103.619V8.31119H102.03V1.56104Z" fill="#71717A"/>
				<path d="M111.147 8.31119V1.56104H113.935C114.441 1.56104 114.877 1.65992 115.244 1.85768C115.613 2.05324 115.897 2.3268 116.097 2.67837C116.297 3.02775 116.397 3.43425 116.397 3.89788C116.397 4.36371 116.295 4.77132 116.091 5.12069C115.889 5.46787 115.6 5.73704 115.224 5.9282C114.848 6.11937 114.402 6.21495 113.886 6.21495H112.165V4.92952H113.583C113.829 4.92952 114.034 4.88668 114.199 4.80098C114.366 4.71529 114.492 4.59553 114.578 4.44172C114.664 4.28571 114.706 4.10443 114.706 3.89788C114.706 3.68914 114.664 3.50896 114.578 3.35734C114.492 3.20353 114.366 3.08488 114.199 3.00138C114.032 2.91788 113.826 2.87613 113.583 2.87613H112.778V8.31119H111.147Z" fill="#71717A"/>
				<path d="M117.387 8.31119V1.56104H119.019V6.98621H121.827V8.31119H117.387Z" fill="#71717A"/>
				<path d="M124.489 1.56104V8.31119H122.857V1.56104H124.489Z" fill="#71717A"/>
				<path d="M127.159 8.31119H125.405L127.683 1.56104H129.855L132.132 8.31119H130.379L128.793 3.26176H128.741L127.159 8.31119ZM126.925 5.65464H130.59V6.89392H126.925V5.65464Z" fill="#71717A"/>
				<path d="M138.79 1.56104V8.31119H137.405L134.719 4.41535H134.676V8.31119H133.045V1.56104H134.449L137.105 5.45029H137.161V1.56104H138.79Z" fill="#71717A"/>
				<path d="M146.128 4.00665H144.48C144.458 3.83746 144.413 3.68474 144.345 3.54851C144.277 3.41228 144.187 3.29582 144.075 3.19914C143.963 3.10246 143.83 3.02884 143.676 2.97831C143.525 2.92557 143.356 2.8992 143.172 2.8992C142.845 2.8992 142.562 2.97941 142.325 3.13981C142.09 3.30021 141.908 3.53203 141.781 3.83526C141.656 4.13849 141.593 4.50544 141.593 4.93612C141.593 5.38437 141.657 5.76011 141.784 6.06334C141.914 6.36437 142.095 6.59179 142.328 6.74561C142.563 6.89722 142.841 6.97303 143.162 6.97303C143.342 6.97303 143.506 6.94996 143.653 6.90381C143.803 6.85767 143.933 6.79065 144.045 6.70276C144.16 6.61267 144.253 6.5039 144.326 6.37646C144.4 6.24681 144.452 6.10069 144.48 5.93809L146.128 5.94798C146.1 6.24681 146.013 6.54126 145.868 6.8313C145.725 7.12135 145.529 7.38612 145.278 7.62563C145.028 7.86294 144.722 8.05191 144.362 8.19254C144.004 8.33317 143.593 8.40348 143.129 8.40348C142.518 8.40348 141.971 8.26944 141.488 8.00137C141.006 7.7311 140.626 7.33778 140.347 6.82141C140.068 6.30504 139.929 5.67661 139.929 4.93612C139.929 4.19342 140.07 3.56389 140.354 3.04752C140.637 2.53115 141.021 2.13893 141.504 1.87086C141.988 1.60279 142.529 1.46875 143.129 1.46875C143.538 1.46875 143.916 1.52588 144.263 1.64014C144.61 1.7522 144.915 1.917 145.179 2.13454C145.443 2.34987 145.657 2.61465 145.822 2.92887C145.987 3.24308 146.089 3.60234 146.128 4.00665Z" fill="#71717A"/>
				<path d="M147.248 8.31119V1.56104H151.954V2.88602H148.879V4.27033H151.714V5.59861H148.879V6.98621H151.954V8.31119H147.248Z" fill="#71717A"/>
				<rect x="67.8223" y="1.57812" width="16.1979" height="14.9048" fill="white"/>
				<path d="M83.6855 0C84.2378 0 84.6855 0.447715 84.6855 1V16.6855C84.6855 17.2378 84.2378 17.6855 83.6855 17.6855H68C67.4477 17.6855 67 17.2378 67 16.6855V1C67 0.447715 67.4477 0 68 0H83.6855ZM71.8037 12.9609C71.0094 12.9611 70.3652 13.6051 70.3652 14.3994C70.3655 15.1936 71.0096 15.8377 71.8037 15.8379C72.598 15.8379 73.242 15.1937 73.2422 14.3994C73.2422 13.605 72.5981 12.9609 71.8037 12.9609ZM78.0479 9.39844V15.8057H79.4004V13.7285H80.5371C81.0285 13.7285 81.4473 13.6374 81.793 13.4561C82.1408 13.2746 82.4066 13.0207 82.5898 12.6953C82.7731 12.37 82.8652 11.9948 82.8652 11.5693C82.8652 11.1439 82.774 10.7687 82.5928 10.4434C82.4137 10.116 82.1539 9.86029 81.8125 9.67676C81.471 9.49119 81.0574 9.3985 80.5723 9.39844H78.0479ZM74.2725 9.24219V15.8037H77.3652V14.5342H75.626V9.24219H74.2725ZM80.3125 10.5059C80.5749 10.5059 80.7911 10.551 80.9619 10.6406C81.1327 10.7282 81.2604 10.8521 81.3438 11.0127C81.429 11.1711 81.4717 11.3567 81.4717 11.5693C81.4717 11.7798 81.429 11.9663 81.3438 12.1289C81.2604 12.2895 81.1327 12.416 80.9619 12.5078C80.7932 12.5974 80.5786 12.6426 80.3184 12.6426H79.4004V10.5059H80.3125ZM71.8037 9.24219C71.0094 9.24235 70.3652 9.88633 70.3652 10.6807C70.3655 11.4748 71.0096 12.119 71.8037 12.1191C72.598 12.1191 73.2419 11.4749 73.2422 10.6807C73.2422 9.88623 72.5981 9.24219 71.8037 9.24219ZM70.4336 8.46582H71.752L72.9678 4.27734H73.0176L74.2363 8.46582H75.5547L77.3848 2.05859H75.9072L74.8486 6.52051H74.792L73.627 2.05859H72.3613L71.1934 6.51074H71.1396L70.0811 2.05859H68.6025L70.4336 8.46582ZM78.0312 8.44824H79.3838V6.37012H80.5205C81.0118 6.37012 81.4307 6.2799 81.7764 6.09863C82.1242 5.91719 82.39 5.66323 82.5732 5.33789C82.7565 5.01258 82.8486 4.6373 82.8486 4.21191C82.8486 3.78642 82.7584 3.41034 82.5771 3.08496C82.3981 2.75769 82.1372 2.50284 81.7959 2.31934C81.4544 2.13376 81.0408 2.04009 80.5557 2.04004H78.0312V8.44824ZM80.2959 3.14746C80.5581 3.14746 80.7746 3.19271 80.9453 3.28223C81.116 3.36977 81.2438 3.49384 81.3271 3.6543C81.4125 3.81281 81.4551 3.99917 81.4551 4.21191C81.455 4.42237 81.4124 4.60892 81.3271 4.77148C81.2438 4.93209 81.1161 5.05862 80.9453 5.15039C80.7767 5.23992 80.5619 5.28516 80.3018 5.28516H79.3838V3.14746H80.2959Z" fill="#71717A"/>
				</svg>
				</div>
		</div>
	<?php } elseif ( $ab_options['ab_testing_enabled'] === false || $ab_options['ab_testing_enabled'] === 'false' ) { ?>
		<div v-if="banner_preview_is_on " class="notice-container" :class="{ 'notice-type-banner': show_cookie_as == 'banner', 'notice-type-popup': show_cookie_as == 'popup', 'notice-type-widget': show_cookie_as == 'widget', 'banner-top': cookie_position == 'top' && show_cookie_as == 'banner' ,'banner-bottom': cookie_position == 'bottom' && show_cookie_as == 'banner', 'widget-left': cookie_widget_position == 'left' && show_cookie_as == 'widget','widget-right': cookie_widget_position == 'right' && show_cookie_as == 'widget', 'widget-top-right': cookie_widget_position == 'top_right' && show_cookie_as == 'widget', 'widget-top-left': cookie_widget_position == 'top_left' && show_cookie_as == 'widget', 'new_default_banner': template == 'new_default' }"
			:style="{
				'background-color': `${cookie_bar_color}${Math.floor(cookie_bar_opacity * 255).toString(16).toUpperCase()}`,
				'--vendor-link-color': this[`accept_all_as_button${active_test_banner_tab}`] ? `${this[`accept_all_background_color${active_test_banner_tab}`]}` : this[`accept_all_text_color${active_test_banner_tab}`],
				'color': cookie_text_color,
				'border-style': border_style,
				'border-width': cookie_bar_border_width + 'px',
				'border-radius': cookie_bar_border_radius + 'px',
				'border-color': cookie_border_color,
				'padding': show_cookie_as != 'banner' ? cookie_bar_padding + 'px' : undefined,
				'padding-inline': show_cookie_as == 'banner' ? cookie_bar_horizontal_padding + 'px' : undefined,
				'padding-block': show_cookie_as == 'banner' ? cookie_bar_vertical_padding + 'px' : undefined,
				'gap': cookie_bar_spacing + 'px',
				'backdrop-filter': cookie_bar_blur > 0 ? `blur(${cookie_bar_blur * 20}px)` : undefined,
				'box-shadow': `${cookie_bar_shadow_size}px ${cookie_bar_shadow_size}px ${cookie_bar_shadow_size*2}px ${cookie_bar_shadow_color}${Math.floor(0.5 * 255).toString(16).toUpperCase()}`,
				...(is_us_state_laws ? { 'padding-bottom': '35px' } : {})
			}"
		>
			
			<span v-if="bypass_button_is_on" :style="{ 'border': 'none', 'cursor': 'pointer', 'display':'inline-flex','justify-content': 'center', 'align-items': 'center', 'height':'20px', 'width': '20px', 'position': 'absolute', 'top': '5px', 'right': (parseInt(cookie_bar_border_radius)/3 + 10) + 'px', 'border-radius': '50%','color': bypass_button_text_color, 'background-color':'transparent', 'scale': bypass_button_size == 'lg' ? '110%' : bypass_button_size == 'sm' ? '90%' : '100%' }" @click="turnOffPreviewBanner">
				<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z" fill="currentColor"/>
				</svg>
			</span>
			<div class="notice-content-header" style="width: 100%; flex-direction: <?php echo esc_attr($banner_layouts['c1']['direction'] ?? 'row') == 'col' ? ($banner_structure['c1'][0] === 'logo' ? 'column' : 'column-reverse') : ($banner_structure['c1'][0] === 'logo' ? 'row' : 'row-reverse'); ?>; <?php echo $banner_layouts['c1']['direction'] === 'row' ? 'align-items: center; justify-content: ' . ($banner_layouts['c1']['justify'] ===  'between' ? 'space-between' : esc_attr($banner_layouts['c1']['justify'] ?? '')) : 'align-items: ' . esc_attr($banner_layouts['c1']['justify'] ?? '') ?>">
				<div v-if="logo_is_on" class="notice-logo-container">
				<?php
					$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
					if ( ! empty( $get_banner_img ) ) {
					?>
						<img v-if="use_uploaded_logo" class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>"
						:style="{
							'margin-left': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['margin-left'],
							'width': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['fit-content'],
							'height': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['height'],
							'transform': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['transform'],
								'position': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['position'],
								'z-index': (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['logo']?.['z-index']
						}"  >
					<?php
				}
				?>
				<img v-if="!use_uploaded_logo" alt="Logo image" class="gdpr_logo_image" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL . 'includes/templates/logo_images/banner_' . sanitize_file_name( $the_options['default_logo'] ) . '.svg' ); ?>">
				</div>	
				<div v-if="heading_is_on" class="notice-heading-wrapper" :style = "{
					'color': cookie_heading_color,
					'font-size': heading_text_size + 'px',
					'font-weight': heading_text_weight,
				}">
						<h3 v-if="gdpr_message_heading.length>0">{{gdpr_message_heading}}</h3>
				</div>
			</div>
			

			
					
			<div class="notice-content-body" :style="{'gap': cookie_bar_spacing + 'px'}" style="width: 100%; flex-direction: <?php echo esc_attr($banner_layouts['c2']['direction'] == 'col' ? ($banner_structure['c2'][0] === 'bannerText' ? 'column' : 'column-reverse') : ($banner_structure['c2'][0] === 'bannerText' ? 'row' : 'row-reverse')); ?>;" :class="'notice-template-name-' + (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.name + ' template-' + (template == 'default' || template == 'new_default' ? default_template_json : json_templates[template])?.['static-settings']?.['layout']">
				<div style="display: flex; flex-direction: column; gap:10px;">
					<p :style="{
						'color': cookie_text_color,
						'font-size': cookie_font_size + 'px',
						'font-weight': cookie_text_weight,
						'text-align': banner_text_alignment,
					}">	
						<span :style="{'font-family': cookie_font}" v-show="(!is_auto_mode && is_gdpr) || (is_auto_mode && banner_edit_law === 'gdpr')" v-html ="gdpr_message"></span>
						<span :style="{'font-family': cookie_font}" v-show="(!is_auto_mode && is_lgpd) || (is_auto_mode && banner_edit_law === 'lgpd')" v-html ="lgpd_message"></span>
						<span :style="{'font-family': cookie_font}" v-show="(!is_auto_mode && is_eprivacy) || (is_auto_mode && banner_edit_law === 'eprivacy')" v-html ="eprivacy_message"></span>
						<span :style="{'font-family': cookie_font}" v-show="(!is_auto_mode && is_uk_gdpr) || (is_auto_mode && banner_edit_law === 'uk_gdpr')" v-html ="uk_gdpr_message"></span>
						<span :style="{'font-family': cookie_font}" v-show="(!is_auto_mode && is_sa_pdpl) || (is_auto_mode && banner_edit_law === 'sa_pdpl')" v-html ="pdpl_message"></span>
						<span :style="{'font-family': cookie_font}" v-show="(!is_auto_mode && is_pipeda) || (is_auto_mode && banner_edit_law === 'pipeda')" v-html ="pipeda_message"></span>
						<span :style="{'font-family': cookie_font}" v-show="(!is_auto_mode && is_au_app) || (is_auto_mode && banner_edit_law === 'au_app')" v-html ="app_message"></span>
						<span :style="{'font-family': cookie_font}" v-show="(!is_auto_mode && is_us_state_laws && us_state_laws_edit_law === 'ccpa') || (is_auto_mode && banner_edit_law === 'us_state_laws' && us_state_laws_edit_law === 'ccpa')" v-html ="ccpa_message"></span>
						<span :style="{'font-family': cookie_font}" v-show="(!is_auto_mode && is_us_state_laws && us_state_laws_edit_law === 'default_opt_out') || (is_auto_mode && banner_edit_law === 'us_state_laws' && us_state_laws_edit_law === 'default_opt_out')" v-html ="default_opt_out_message"></span>
						<span :style="{'font-family': cookie_font}" v-show="(!is_auto_mode && is_us_state_laws && us_state_laws_edit_law === 'pure_opt_out') || (is_auto_mode && banner_edit_law === 'us_state_laws' && us_state_laws_edit_law === 'pure_opt_out')" v-html ="pure_opt_out_message"></span>
						<a v-if="button_readmore_is_on && !((!is_auto_mode && is_us_state_laws) || (is_auto_mode && banner_edit_law === 'us_state_laws'))" :style="{
							'font-family': cookie_font,
							'color':button_readmore_link_color,
							'cursor':'pointer',
						}" >
							<span>{{ button_readmore_text }}</span>
						</a>
						<a id="cookie_action_opt_out_preview" v-if="(!is_auto_mode && is_us_state_laws) || (is_auto_mode && banner_edit_law === 'us_state_laws')" :style="{'font-family': cookie_font,'color': opt_out_text_color,'cursor':'pointer'}"><span>{{ opt_out_text }}</span></a>
					</p>
				</div>
				

				<div v-if="!((!is_auto_mode && is_us_state_laws) || (is_auto_mode && banner_edit_law === 'us_state_laws'))" class="notice-buttons-wrapper" :style="{'gap': cookie_bar_spacing * 2 + 'px'}" style="display: flex; flex-direction: <?php echo esc_attr($banner_layouts['c4']['direction'] ?? 'row') == 'col' ? 'column' : 'row'; ?>; <?php echo $banner_layouts['c2']['direction'] == 'row' ? 'width: 40%' : '' ;?>; margin-top: <?php echo esc_attr($banner_layouts['c2']['direction'] ?? 'row') == 'col' ? '5px' : '0px'; ?>;">
					<div class="notice-left-buttons" :style="{'gap': cookie_bar_spacing * 2 + 'px', display: visible_c5_items.length > 0 ? 'flex' : 'none', width: '100%'}" style=" flex-direction: <?php echo esc_attr($banner_layouts['c5']['direction'] ?? 'row') == 'col' ? 'column' : 'row'; ?>; <?php echo $banner_layouts['c5']['direction'] === 'row' ? 'align-items: center; justify-content: ' . ($banner_layouts['c5']['justify'] ===  'between' ? 'space-between' : esc_attr($banner_layouts['c5']['justify'] ?? '')) : 'align-items: ' . esc_attr($banner_layouts['c5']['justify'] ?? '') ?>">
						
						<?php
						foreach ( $c5_buttons as $button ) {
							wplp_render_notice_button( $button );
						}
						?>
						
					</div>

					<div class="notice-right-buttons" :style="{'gap': cookie_bar_spacing * 2 + 'px', display: visible_c6_items.length > 0 ? 'flex' : 'none', width: '100%'}" style=" flex-direction: <?php echo esc_attr($banner_layouts['c6']['direction'] ?? 'row') == 'col' ? 'column' : 'row'; ?>; <?php echo $banner_layouts['c6']['direction'] === 'row' ? 'align-items: center; justify-content: ' . ($banner_layouts['c6']['justify'] ===  'between' ? 'space-between' : esc_attr($banner_layouts['c6']['justify'] ?? '')) : 'align-items: ' . esc_attr($banner_layouts['c6']['justify'] ?? '') ?>">
						
						<?php
						foreach ( $c6_buttons as $button ) {
							wplp_render_notice_button( $button );
						}
						?>

						
					</div>
				</div>
			</div>
			<div v-show="show_credits" class="powered-by-credits"  :style="{'--popup_accent_color': cookieSettingsPopupAccentColor, 'text-align':'center', 'font-size': '10px', 'margin-bottom':'-10px'}">
			<svg width="152" height="18" viewBox="0 0 152 18" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M4.13672 9.49805H1.85742V8.57812H4.13672C4.57812 8.57812 4.93555 8.50781 5.20898 8.36719C5.48242 8.22656 5.68164 8.03125 5.80664 7.78125C5.93555 7.53125 6 7.24609 6 6.92578C6 6.63281 5.93555 6.35742 5.80664 6.09961C5.68164 5.8418 5.48242 5.63477 5.20898 5.47852C4.93555 5.31836 4.57812 5.23828 4.13672 5.23828H2.12109V12.8438H0.990234V4.3125H4.13672C4.78125 4.3125 5.32617 4.42383 5.77148 4.64648C6.2168 4.86914 6.55469 5.17773 6.78516 5.57227C7.01562 5.96289 7.13086 6.41016 7.13086 6.91406C7.13086 7.46094 7.01562 7.92773 6.78516 8.31445C6.55469 8.70117 6.2168 8.99609 5.77148 9.19922C5.32617 9.39844 4.78125 9.49805 4.13672 9.49805ZM8.03906 9.74414V9.60938C8.03906 9.15234 8.10547 8.72852 8.23828 8.33789C8.37109 7.94336 8.5625 7.60156 8.8125 7.3125C9.0625 7.01953 9.36523 6.79297 9.7207 6.63281C10.0762 6.46875 10.4746 6.38672 10.916 6.38672C11.3613 6.38672 11.7617 6.46875 12.1172 6.63281C12.4766 6.79297 12.7812 7.01953 13.0312 7.3125C13.2852 7.60156 13.4785 7.94336 13.6113 8.33789C13.7441 8.72852 13.8105 9.15234 13.8105 9.60938V9.74414C13.8105 10.2012 13.7441 10.625 13.6113 11.0156C13.4785 11.4062 13.2852 11.748 13.0312 12.041C12.7812 12.3301 12.4785 12.5566 12.123 12.7207C11.7715 12.8809 11.373 12.9609 10.9277 12.9609C10.4824 12.9609 10.082 12.8809 9.72656 12.7207C9.37109 12.5566 9.06641 12.3301 8.8125 12.041C8.5625 11.748 8.37109 11.4062 8.23828 11.0156C8.10547 10.625 8.03906 10.2012 8.03906 9.74414ZM9.12305 9.60938V9.74414C9.12305 10.0605 9.16016 10.3594 9.23438 10.6406C9.30859 10.918 9.41992 11.1641 9.56836 11.3789C9.7207 11.5938 9.91016 11.7637 10.1367 11.8887C10.3633 12.0098 10.627 12.0703 10.9277 12.0703C11.2246 12.0703 11.4844 12.0098 11.707 11.8887C11.9336 11.7637 12.1211 11.5938 12.2695 11.3789C12.418 11.1641 12.5293 10.918 12.6035 10.6406C12.6816 10.3594 12.7207 10.0605 12.7207 9.74414V9.60938C12.7207 9.29688 12.6816 9.00195 12.6035 8.72461C12.5293 8.44336 12.416 8.19531 12.2637 7.98047C12.1152 7.76172 11.9277 7.58984 11.7012 7.46484C11.4785 7.33984 11.2168 7.27734 10.916 7.27734C10.6191 7.27734 10.3574 7.33984 10.1309 7.46484C9.9082 7.58984 9.7207 7.76172 9.56836 7.98047C9.41992 8.19531 9.30859 8.44336 9.23438 8.72461C9.16016 9.00195 9.12305 9.29688 9.12305 9.60938ZM16.7754 11.7188L18.4043 6.50391H19.1191L18.9785 7.54102L17.3203 12.8438H16.623L16.7754 11.7188ZM15.6797 6.50391L17.0684 11.7773L17.168 12.8438H16.4355L14.5957 6.50391H15.6797ZM20.6777 11.7363L22.002 6.50391H23.0801L21.2402 12.8438H20.5137L20.6777 11.7363ZM19.2773 6.50391L20.8711 11.6309L21.0527 12.8438H20.3613L18.6562 7.5293L18.5156 6.50391H19.2773ZM26.8242 12.9609C26.3828 12.9609 25.9824 12.8867 25.623 12.7383C25.2676 12.5859 24.9609 12.373 24.7031 12.0996C24.4492 11.8262 24.2539 11.502 24.1172 11.127C23.9805 10.752 23.9121 10.3418 23.9121 9.89648V9.65039C23.9121 9.13477 23.9883 8.67578 24.1406 8.27344C24.293 7.86719 24.5 7.52344 24.7617 7.24219C25.0234 6.96094 25.3203 6.74805 25.6523 6.60352C25.9844 6.45898 26.3281 6.38672 26.6836 6.38672C27.1367 6.38672 27.5273 6.46484 27.8555 6.62109C28.1875 6.77734 28.459 6.99609 28.6699 7.27734C28.8809 7.55469 29.0371 7.88281 29.1387 8.26172C29.2402 8.63672 29.291 9.04688 29.291 9.49219V9.97852H24.5566V9.09375H28.207V9.01172C28.1914 8.73047 28.1328 8.45703 28.0312 8.19141C27.9336 7.92578 27.7773 7.70703 27.5625 7.53516C27.3477 7.36328 27.0547 7.27734 26.6836 7.27734C26.4375 7.27734 26.2109 7.33008 26.0039 7.43555C25.7969 7.53711 25.6191 7.68945 25.4707 7.89258C25.3223 8.0957 25.207 8.34375 25.125 8.63672C25.043 8.92969 25.002 9.26758 25.002 9.65039V9.89648C25.002 10.1973 25.043 10.4805 25.125 10.7461C25.2109 11.0078 25.334 11.2383 25.4941 11.4375C25.6582 11.6367 25.8555 11.793 26.0859 11.9062C26.3203 12.0195 26.5859 12.0762 26.8828 12.0762C27.2656 12.0762 27.5898 11.998 27.8555 11.8418C28.1211 11.6855 28.3535 11.4766 28.5527 11.2148L29.209 11.7363C29.0723 11.9434 28.8984 12.1406 28.6875 12.3281C28.4766 12.5156 28.2168 12.668 27.9082 12.7852C27.6035 12.9023 27.2422 12.9609 26.8242 12.9609ZM31.6406 7.5V12.8438H30.5566V6.50391H31.6113L31.6406 7.5ZM33.6211 6.46875L33.6152 7.47656C33.5254 7.45703 33.4395 7.44531 33.3574 7.44141C33.2793 7.43359 33.1895 7.42969 33.0879 7.42969C32.8379 7.42969 32.6172 7.46875 32.4258 7.54688C32.2344 7.625 32.0723 7.73438 31.9395 7.875C31.8066 8.01562 31.7012 8.18359 31.623 8.37891C31.5488 8.57031 31.5 8.78125 31.4766 9.01172L31.1719 9.1875C31.1719 8.80469 31.209 8.44531 31.2832 8.10938C31.3613 7.77344 31.4805 7.47656 31.6406 7.21875C31.8008 6.95703 32.0039 6.75391 32.25 6.60938C32.5 6.46094 32.7969 6.38672 33.1406 6.38672C33.2188 6.38672 33.3086 6.39648 33.4102 6.41602C33.5117 6.43164 33.582 6.44922 33.6211 6.46875ZM37.1484 12.9609C36.707 12.9609 36.3066 12.8867 35.9473 12.7383C35.5918 12.5859 35.2852 12.373 35.0273 12.0996C34.7734 11.8262 34.5781 11.502 34.4414 11.127C34.3047 10.752 34.2363 10.3418 34.2363 9.89648V9.65039C34.2363 9.13477 34.3125 8.67578 34.4648 8.27344C34.6172 7.86719 34.8242 7.52344 35.0859 7.24219C35.3477 6.96094 35.6445 6.74805 35.9766 6.60352C36.3086 6.45898 36.6523 6.38672 37.0078 6.38672C37.4609 6.38672 37.8516 6.46484 38.1797 6.62109C38.5117 6.77734 38.7832 6.99609 38.9941 7.27734C39.2051 7.55469 39.3613 7.88281 39.4629 8.26172C39.5645 8.63672 39.6152 9.04688 39.6152 9.49219V9.97852H34.8809V9.09375H38.5312V9.01172C38.5156 8.73047 38.457 8.45703 38.3555 8.19141C38.2578 7.92578 38.1016 7.70703 37.8867 7.53516C37.6719 7.36328 37.3789 7.27734 37.0078 7.27734C36.7617 7.27734 36.5352 7.33008 36.3281 7.43555C36.1211 7.53711 35.9434 7.68945 35.7949 7.89258C35.6465 8.0957 35.5312 8.34375 35.4492 8.63672C35.3672 8.92969 35.3262 9.26758 35.3262 9.65039V9.89648C35.3262 10.1973 35.3672 10.4805 35.4492 10.7461C35.5352 11.0078 35.6582 11.2383 35.8184 11.4375C35.9824 11.6367 36.1797 11.793 36.4102 11.9062C36.6445 12.0195 36.9102 12.0762 37.207 12.0762C37.5898 12.0762 37.9141 11.998 38.1797 11.8418C38.4453 11.6855 38.6777 11.4766 38.877 11.2148L39.5332 11.7363C39.3965 11.9434 39.2227 12.1406 39.0117 12.3281C38.8008 12.5156 38.541 12.668 38.2324 12.7852C37.9277 12.9023 37.5664 12.9609 37.1484 12.9609ZM44.877 11.6133V3.84375H45.9668V12.8438H44.9707L44.877 11.6133ZM40.6113 9.74414V9.62109C40.6113 9.13672 40.6699 8.69727 40.7871 8.30273C40.9082 7.9043 41.0781 7.5625 41.2969 7.27734C41.5195 6.99219 41.7832 6.77344 42.0879 6.62109C42.3965 6.46484 42.7402 6.38672 43.1191 6.38672C43.5176 6.38672 43.8652 6.45703 44.1621 6.59766C44.4629 6.73438 44.7168 6.93555 44.9238 7.20117C45.1348 7.46289 45.3008 7.7793 45.4219 8.15039C45.543 8.52148 45.627 8.94141 45.6738 9.41016V9.94922C45.6309 10.4141 45.5469 10.832 45.4219 11.2031C45.3008 11.5742 45.1348 11.8906 44.9238 12.1523C44.7168 12.4141 44.4629 12.6152 44.1621 12.7559C43.8613 12.8926 43.5098 12.9609 43.1074 12.9609C42.7363 12.9609 42.3965 12.8809 42.0879 12.7207C41.7832 12.5605 41.5195 12.3359 41.2969 12.0469C41.0781 11.7578 40.9082 11.418 40.7871 11.0273C40.6699 10.6328 40.6113 10.2051 40.6113 9.74414ZM41.7012 9.62109V9.74414C41.7012 10.0605 41.7324 10.3574 41.7949 10.6348C41.8613 10.9121 41.9629 11.1562 42.0996 11.3672C42.2363 11.5781 42.4102 11.7441 42.6211 11.8652C42.832 11.9824 43.084 12.041 43.377 12.041C43.7363 12.041 44.0312 11.9648 44.2617 11.8125C44.4961 11.6602 44.6836 11.459 44.8242 11.209C44.9648 10.959 45.0742 10.6875 45.1523 10.3945V8.98242C45.1055 8.76758 45.0371 8.56055 44.9473 8.36133C44.8613 8.1582 44.748 7.97852 44.6074 7.82227C44.4707 7.66211 44.3008 7.53516 44.0977 7.44141C43.8984 7.34766 43.6621 7.30078 43.3887 7.30078C43.0918 7.30078 42.8359 7.36328 42.6211 7.48828C42.4102 7.60938 42.2363 7.77734 42.0996 7.99219C41.9629 8.20312 41.8613 8.44922 41.7949 8.73047C41.7324 9.00781 41.7012 9.30469 41.7012 9.62109ZM50.625 3.84375H51.7148V11.6133L51.6211 12.8438H50.625V3.84375ZM55.998 9.62109V9.74414C55.998 10.2051 55.9434 10.6328 55.834 11.0273C55.7246 11.418 55.5645 11.7578 55.3535 12.0469C55.1426 12.3359 54.8848 12.5605 54.5801 12.7207C54.2754 12.8809 53.9258 12.9609 53.5312 12.9609C53.1289 12.9609 52.7754 12.8926 52.4707 12.7559C52.1699 12.6152 51.916 12.4141 51.709 12.1523C51.502 11.8906 51.3359 11.5742 51.2109 11.2031C51.0898 10.832 51.0059 10.4141 50.959 9.94922V9.41016C51.0059 8.94141 51.0898 8.52148 51.2109 8.15039C51.3359 7.7793 51.502 7.46289 51.709 7.20117C51.916 6.93555 52.1699 6.73438 52.4707 6.59766C52.7715 6.45703 53.1211 6.38672 53.5195 6.38672C53.918 6.38672 54.2715 6.46484 54.5801 6.62109C54.8887 6.77344 55.1465 6.99219 55.3535 7.27734C55.5645 7.5625 55.7246 7.9043 55.834 8.30273C55.9434 8.69727 55.998 9.13672 55.998 9.62109ZM54.9082 9.74414V9.62109C54.9082 9.30469 54.8789 9.00781 54.8203 8.73047C54.7617 8.44922 54.668 8.20312 54.5391 7.99219C54.4102 7.77734 54.2402 7.60938 54.0293 7.48828C53.8184 7.36328 53.5586 7.30078 53.25 7.30078C52.9766 7.30078 52.7383 7.34766 52.5352 7.44141C52.3359 7.53516 52.166 7.66211 52.0254 7.82227C51.8848 7.97852 51.7695 8.1582 51.6797 8.36133C51.5938 8.56055 51.5293 8.76758 51.4863 8.98242V10.3945C51.5488 10.668 51.6504 10.9316 51.791 11.1855C51.9355 11.4355 52.127 11.6406 52.3652 11.8008C52.6074 11.9609 52.9062 12.041 53.2617 12.041C53.5547 12.041 53.8047 11.9824 54.0117 11.8652C54.2227 11.7441 54.3926 11.5781 54.5215 11.3672C54.6543 11.1562 54.752 10.9121 54.8145 10.6348C54.877 10.3574 54.9082 10.0605 54.9082 9.74414ZM59.0918 12.1875L60.8555 6.50391H62.0156L59.4727 13.8223C59.4141 13.9785 59.3359 14.1465 59.2383 14.3262C59.1445 14.5098 59.0234 14.6836 58.875 14.8477C58.7266 15.0117 58.5469 15.1445 58.3359 15.2461C58.1289 15.3516 57.8809 15.4043 57.5918 15.4043C57.5059 15.4043 57.3965 15.3926 57.2637 15.3691C57.1309 15.3457 57.0371 15.3262 56.9824 15.3105L56.9766 14.4316C57.0078 14.4355 57.0566 14.4395 57.123 14.4434C57.1934 14.4512 57.2422 14.4551 57.2695 14.4551C57.5156 14.4551 57.7246 14.4219 57.8965 14.3555C58.0684 14.293 58.2129 14.1855 58.3301 14.0332C58.4512 13.8848 58.5547 13.6797 58.6406 13.418L59.0918 12.1875ZM57.7969 6.50391L59.4434 11.4258L59.7246 12.5684L58.9453 12.9668L56.6133 6.50391H57.7969Z" fill="#71717A"/>
			<path d="M88.7969 16.1569V11.1736H90.5713C90.9586 11.1736 91.2794 11.2442 91.5338 11.3853C91.7883 11.5264 91.9787 11.7195 92.1051 11.9644C92.2314 12.2077 92.2946 12.4819 92.2946 12.7869C92.2946 13.0934 92.2306 13.3692 92.1026 13.6142C91.9762 13.8575 91.785 14.0505 91.529 14.1933C91.2746 14.3344 90.9545 14.405 90.5689 14.405H89.3486V13.7675H90.5008C90.7455 13.7675 90.944 13.7253 91.0963 13.6409C91.2486 13.555 91.3604 13.4382 91.4317 13.2905C91.503 13.1429 91.5387 12.975 91.5387 12.7869C91.5387 12.5987 91.503 12.4316 91.4317 12.2856C91.3604 12.1396 91.2478 12.0252 91.0939 11.9425C90.9416 11.8598 90.7406 11.8184 90.4911 11.8184H89.548V16.1569H88.7969Z" fill="#71717A"/>
			<path d="M97.0767 16.1569V11.1736H97.8278V15.5097H100.083V16.1569H97.0767Z" fill="#71717A"/>
			<path d="M105.516 16.1569H104.719L106.511 11.1736H107.378L109.17 16.1569H108.372L106.965 12.0788H106.926L105.516 16.1569ZM105.65 14.2055H108.236V14.8381H105.65V14.2055Z" fill="#71717A"/>
			<path d="M112.956 11.8208V11.1736H116.809V11.8208H115.256V16.1569H114.507V11.8208H112.956Z" fill="#71717A"/>
			<path d="M121.562 16.1569V11.1736H124.649V11.8208H122.313V13.3392H124.428V13.984H122.313V16.1569H121.562Z" fill="#71717A"/>
			<path d="M133.798 13.6653C133.798 14.1973 133.701 14.6548 133.507 15.0376C133.312 15.4188 133.046 15.7125 132.707 15.9185C132.37 16.1229 131.987 16.2251 131.557 16.2251C131.126 16.2251 130.741 16.1229 130.403 15.9185C130.066 15.7125 129.8 15.418 129.605 15.0352C129.411 14.6524 129.314 14.1957 129.314 13.6653C129.314 13.1332 129.411 12.6765 129.605 12.2953C129.8 11.9125 130.066 11.6189 130.403 11.4145C130.741 11.2085 131.126 11.1055 131.557 11.1055C131.987 11.1055 132.37 11.2085 132.707 11.4145C133.046 11.6189 133.312 11.9125 133.507 12.2953C133.701 12.6765 133.798 13.1332 133.798 13.6653ZM133.055 13.6653C133.055 13.2597 132.989 12.9183 132.858 12.6409C132.728 12.3618 132.55 12.151 132.323 12.0082C132.098 11.8638 131.843 11.7917 131.557 11.7917C131.27 11.7917 131.014 11.8638 130.789 12.0082C130.564 12.151 130.386 12.3618 130.254 12.6409C130.125 12.9183 130.06 13.2597 130.06 13.6653C130.06 14.0708 130.125 14.4131 130.254 14.6921C130.386 14.9695 130.564 15.1804 130.789 15.3248C131.014 15.4675 131.27 15.5389 131.557 15.5389C131.843 15.5389 132.098 15.4675 132.323 15.3248C132.55 15.1804 132.728 14.9695 132.858 14.6921C132.989 14.4131 133.055 14.0708 133.055 13.6653Z" fill="#71717A"/>
			<path d="M138.636 16.1569V11.1736H140.411C140.796 11.1736 141.117 11.2401 141.371 11.3731C141.627 11.5061 141.818 11.6903 141.945 11.9255C142.071 12.1591 142.134 12.4292 142.134 12.7358C142.134 13.0407 142.07 13.3092 141.942 13.5412C141.816 13.7715 141.625 13.9508 141.368 14.0789C141.114 14.2071 140.794 14.2712 140.408 14.2712H139.064V13.6239H140.34C140.583 13.6239 140.781 13.589 140.933 13.5193C141.087 13.4495 141.2 13.3481 141.271 13.2151C141.343 13.0821 141.378 12.9223 141.378 12.7358C141.378 12.5476 141.342 12.3846 141.269 12.2467C141.198 12.1088 141.085 12.0033 140.931 11.9303C140.779 11.8557 140.579 11.8184 140.331 11.8184H139.387V16.1569H138.636ZM141.094 13.9086L142.324 16.1569H141.468L140.263 13.9086H141.094Z" fill="#71717A"/>
			<path d="M146.95 11.1736H147.861L149.446 15.0474H149.504L151.089 11.1736H152.001V16.1569H151.286V12.5508H151.24L149.772 16.1496H149.179L147.71 12.5484H147.664V16.1569H146.95V11.1736Z" fill="#71717A"/>
			<path d="M94.2056 4.00665H92.5576C92.5357 3.83746 92.4906 3.68474 92.4225 3.54851C92.3544 3.41228 92.2643 3.29582 92.1522 3.19914C92.0402 3.10246 91.9072 3.02884 91.7534 2.97831C91.6018 2.92557 91.4337 2.8992 91.2491 2.8992C90.9217 2.8992 90.6394 2.97941 90.4021 3.13981C90.1669 3.30021 89.9857 3.53203 89.8582 3.83526C89.733 4.13849 89.6703 4.50544 89.6703 4.93612C89.6703 5.38437 89.7341 5.76011 89.8615 6.06334C89.9912 6.36437 90.1724 6.59179 90.4053 6.74561C90.6405 6.89722 90.9184 6.97303 91.2392 6.97303C91.4194 6.97303 91.5831 6.94996 91.7303 6.90381C91.8798 6.85767 92.0105 6.79065 92.1226 6.70276C92.2368 6.61267 92.3302 6.5039 92.4027 6.37646C92.4774 6.24681 92.5291 6.10069 92.5576 5.93809L94.2056 5.94798C94.1771 6.24681 94.0903 6.54126 93.9453 6.8313C93.8024 7.12135 93.6058 7.38612 93.3553 7.62563C93.1048 7.86294 92.7993 8.05191 92.439 8.19254C92.0808 8.33317 91.6699 8.40348 91.2063 8.40348C90.5954 8.40348 90.0483 8.26944 89.5649 8.00137C89.0837 7.7311 88.7035 7.33778 88.4245 6.82141C88.1454 6.30504 88.0059 5.67661 88.0059 4.93612C88.0059 4.19342 88.1476 3.56389 88.431 3.04752C88.7145 2.53115 89.0979 2.13893 89.5813 1.87086C90.0648 1.60279 90.6064 1.46875 91.2063 1.46875C91.615 1.46875 91.9929 1.52588 92.3401 1.64014C92.6873 1.7522 92.9927 1.917 93.2564 2.13454C93.5201 2.34987 93.7343 2.61465 93.8991 2.92887C94.0639 3.24308 94.1661 3.60234 94.2056 4.00665Z" fill="#71717A"/>
			<path d="M101.331 4.93612C101.331 5.67881 101.188 6.30834 100.902 6.82471C100.616 7.34108 100.23 7.7333 99.7419 8.00137C99.2563 8.26944 98.7114 8.40348 98.1071 8.40348C97.5007 8.40348 96.9546 8.26835 96.469 7.99808C95.9834 7.72781 95.5978 7.33559 95.3121 6.82141C95.0287 6.30504 94.8869 5.67661 94.8869 4.93612C94.8869 4.19342 95.0287 3.56389 95.3121 3.04752C95.5978 2.53115 95.9834 2.13893 96.469 1.87086C96.9546 1.60279 97.5007 1.46875 98.1071 1.46875C98.7114 1.46875 99.2563 1.60279 99.7419 1.87086C100.23 2.13893 100.616 2.53115 100.902 3.04752C101.188 3.56389 101.331 4.19342 101.331 4.93612ZM99.6628 4.93612C99.6628 4.49665 99.6002 4.12531 99.475 3.82208C99.3519 3.51885 99.1739 3.28923 98.941 3.13322C98.7103 2.97721 98.4323 2.8992 98.1071 2.8992C97.7841 2.8992 97.5062 2.97721 97.2732 3.13322C97.0403 3.28923 96.8612 3.51885 96.736 3.82208C96.6129 4.12531 96.5514 4.49665 96.5514 4.93612C96.5514 5.37558 96.6129 5.74693 96.736 6.05016C96.8612 6.35338 97.0403 6.583 97.2732 6.73901C97.5062 6.89502 97.7841 6.97303 98.1071 6.97303C98.4323 6.97303 98.7103 6.89502 98.941 6.73901C99.1739 6.583 99.3519 6.35338 99.475 6.05016C99.6002 5.74693 99.6628 5.37558 99.6628 4.93612Z" fill="#71717A"/>
			<path d="M102.03 1.56104H104.051L105.765 5.74033H105.844L107.558 1.56104H109.578V8.31119H107.989V4.16486H107.933L106.312 8.26835H105.297L103.675 4.14179H103.619V8.31119H102.03V1.56104Z" fill="#71717A"/>
			<path d="M111.147 8.31119V1.56104H113.935C114.441 1.56104 114.877 1.65992 115.244 1.85768C115.613 2.05324 115.897 2.3268 116.097 2.67837C116.297 3.02775 116.397 3.43425 116.397 3.89788C116.397 4.36371 116.295 4.77132 116.091 5.12069C115.889 5.46787 115.6 5.73704 115.224 5.9282C114.848 6.11937 114.402 6.21495 113.886 6.21495H112.165V4.92952H113.583C113.829 4.92952 114.034 4.88668 114.199 4.80098C114.366 4.71529 114.492 4.59553 114.578 4.44172C114.664 4.28571 114.706 4.10443 114.706 3.89788C114.706 3.68914 114.664 3.50896 114.578 3.35734C114.492 3.20353 114.366 3.08488 114.199 3.00138C114.032 2.91788 113.826 2.87613 113.583 2.87613H112.778V8.31119H111.147Z" fill="#71717A"/>
			<path d="M117.387 8.31119V1.56104H119.019V6.98621H121.827V8.31119H117.387Z" fill="#71717A"/>
			<path d="M124.489 1.56104V8.31119H122.857V1.56104H124.489Z" fill="#71717A"/>
			<path d="M127.159 8.31119H125.405L127.683 1.56104H129.855L132.132 8.31119H130.379L128.793 3.26176H128.741L127.159 8.31119ZM126.925 5.65464H130.59V6.89392H126.925V5.65464Z" fill="#71717A"/>
			<path d="M138.79 1.56104V8.31119H137.405L134.719 4.41535H134.676V8.31119H133.045V1.56104H134.449L137.105 5.45029H137.161V1.56104H138.79Z" fill="#71717A"/>
			<path d="M146.128 4.00665H144.48C144.458 3.83746 144.413 3.68474 144.345 3.54851C144.277 3.41228 144.187 3.29582 144.075 3.19914C143.963 3.10246 143.83 3.02884 143.676 2.97831C143.525 2.92557 143.356 2.8992 143.172 2.8992C142.845 2.8992 142.562 2.97941 142.325 3.13981C142.09 3.30021 141.908 3.53203 141.781 3.83526C141.656 4.13849 141.593 4.50544 141.593 4.93612C141.593 5.38437 141.657 5.76011 141.784 6.06334C141.914 6.36437 142.095 6.59179 142.328 6.74561C142.563 6.89722 142.841 6.97303 143.162 6.97303C143.342 6.97303 143.506 6.94996 143.653 6.90381C143.803 6.85767 143.933 6.79065 144.045 6.70276C144.16 6.61267 144.253 6.5039 144.326 6.37646C144.4 6.24681 144.452 6.10069 144.48 5.93809L146.128 5.94798C146.1 6.24681 146.013 6.54126 145.868 6.8313C145.725 7.12135 145.529 7.38612 145.278 7.62563C145.028 7.86294 144.722 8.05191 144.362 8.19254C144.004 8.33317 143.593 8.40348 143.129 8.40348C142.518 8.40348 141.971 8.26944 141.488 8.00137C141.006 7.7311 140.626 7.33778 140.347 6.82141C140.068 6.30504 139.929 5.67661 139.929 4.93612C139.929 4.19342 140.07 3.56389 140.354 3.04752C140.637 2.53115 141.021 2.13893 141.504 1.87086C141.988 1.60279 142.529 1.46875 143.129 1.46875C143.538 1.46875 143.916 1.52588 144.263 1.64014C144.61 1.7522 144.915 1.917 145.179 2.13454C145.443 2.34987 145.657 2.61465 145.822 2.92887C145.987 3.24308 146.089 3.60234 146.128 4.00665Z" fill="#71717A"/>
			<path d="M147.248 8.31119V1.56104H151.954V2.88602H148.879V4.27033H151.714V5.59861H148.879V6.98621H151.954V8.31119H147.248Z" fill="#71717A"/>
			<rect x="67.8223" y="1.57812" width="16.1979" height="14.9048" fill="white"/>
			<path d="M83.6855 0C84.2378 0 84.6855 0.447715 84.6855 1V16.6855C84.6855 17.2378 84.2378 17.6855 83.6855 17.6855H68C67.4477 17.6855 67 17.2378 67 16.6855V1C67 0.447715 67.4477 0 68 0H83.6855ZM71.8037 12.9609C71.0094 12.9611 70.3652 13.6051 70.3652 14.3994C70.3655 15.1936 71.0096 15.8377 71.8037 15.8379C72.598 15.8379 73.242 15.1937 73.2422 14.3994C73.2422 13.605 72.5981 12.9609 71.8037 12.9609ZM78.0479 9.39844V15.8057H79.4004V13.7285H80.5371C81.0285 13.7285 81.4473 13.6374 81.793 13.4561C82.1408 13.2746 82.4066 13.0207 82.5898 12.6953C82.7731 12.37 82.8652 11.9948 82.8652 11.5693C82.8652 11.1439 82.774 10.7687 82.5928 10.4434C82.4137 10.116 82.1539 9.86029 81.8125 9.67676C81.471 9.49119 81.0574 9.3985 80.5723 9.39844H78.0479ZM74.2725 9.24219V15.8037H77.3652V14.5342H75.626V9.24219H74.2725ZM80.3125 10.5059C80.5749 10.5059 80.7911 10.551 80.9619 10.6406C81.1327 10.7282 81.2604 10.8521 81.3438 11.0127C81.429 11.1711 81.4717 11.3567 81.4717 11.5693C81.4717 11.7798 81.429 11.9663 81.3438 12.1289C81.2604 12.2895 81.1327 12.416 80.9619 12.5078C80.7932 12.5974 80.5786 12.6426 80.3184 12.6426H79.4004V10.5059H80.3125ZM71.8037 9.24219C71.0094 9.24235 70.3652 9.88633 70.3652 10.6807C70.3655 11.4748 71.0096 12.119 71.8037 12.1191C72.598 12.1191 73.2419 11.4749 73.2422 10.6807C73.2422 9.88623 72.5981 9.24219 71.8037 9.24219ZM70.4336 8.46582H71.752L72.9678 4.27734H73.0176L74.2363 8.46582H75.5547L77.3848 2.05859H75.9072L74.8486 6.52051H74.792L73.627 2.05859H72.3613L71.1934 6.51074H71.1396L70.0811 2.05859H68.6025L70.4336 8.46582ZM78.0312 8.44824H79.3838V6.37012H80.5205C81.0118 6.37012 81.4307 6.2799 81.7764 6.09863C82.1242 5.91719 82.39 5.66323 82.5732 5.33789C82.7565 5.01258 82.8486 4.6373 82.8486 4.21191C82.8486 3.78642 82.7584 3.41034 82.5771 3.08496C82.3981 2.75769 82.1372 2.50284 81.7959 2.31934C81.4544 2.13376 81.0408 2.04009 80.5557 2.04004H78.0312V8.44824ZM80.2959 3.14746C80.5581 3.14746 80.7746 3.19271 80.9453 3.28223C81.116 3.36977 81.2438 3.49384 81.3271 3.6543C81.4125 3.81281 81.4551 3.99917 81.4551 4.21191C81.455 4.42237 81.4124 4.60892 81.3271 4.77148C81.2438 4.93209 81.1161 5.05862 80.9453 5.15039C80.7767 5.23992 80.5619 5.28516 80.3018 5.28516H79.3838V3.14746H80.2959Z" fill="#71717A"/>
			</svg>
			</div>
				
		</div>

		
	<?php } ?>
	
	<c-container class="gdpr-cookie-consent-settings-container">
		<c-form id="gcc-save-settings-form" method="post" spellcheck="false" class="gdpr-cookie-consent-settings-form">
			<input type="hidden" name="gcc_settings_form_nonce" value="<?php echo esc_attr( wp_create_nonce( 'gcc-settings-form-nonce' ) ); ?>"/>
			<div class="gdpr-cookie-consent-settings-content">

				<div id="gdpr-cookie-consent-save-settings-alert">{{success_error_message}}</div>
				<div id="gdpr-cookie-consent-updating-settings-alert">Updating Setting</div>
				<div id="popup-site-excausted" class="popup-overlay">
				<div class="popup-content">
				<div class="popup-header">
				<div class="popup-title"><span class="gdpr-remaining-scans-title">Remaining Scans: </span><span><?php echo esc_html( $gdpr_no_of_page_scan_left ); ?> / <?php echo esc_html( $total_pages_scan_limit ); ?></span><span> (<?php echo esc_html( ceil( $remaining_percentage_scan_limit ) ); ?>%)</span></div>
					<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Right Corner Image" class="popup-image">
				</div>

						<div class="popup-body">
						<h2>Attention! Cookie Scan Limit Exceeded.</h2>
						<p>You've reached the maximum number of free cookie scans for your account.</p>
						<p>To scan more, you'll need to upgrade to a premium plan.</p>
						<button class="gdpr-cookie-consent-admin-upgrade-button upgrade-button">Upgrade to PRO</button>
					</div>
				</div>
			</div>
			
			<div class="gdpr-banner-preview-save-btn">
				<div class="gdpr-banner-preview-logo-text">
					<div class="gdpr-banner-preview-logo">
						<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/CookieConsent.png'; ?>" alt="Cookie Setting preview logo">
					</div>
					<div class="gdpr-banner-preview-text">
						<span id="gdpr-banner-preview-cookie-banner-title">
							<?php esc_html_e( 'Your Site\'s Cookie Banner', 'gdpr-cookie-consent' ); ?>
						</span><br>
						<span id="gdpr-banner-preview-cookie-banner-description">
							<?php esc_html_e( 'The banner currently displayed on your website.', 'gdpr-cookie-consent' ); ?>
						</span>
					</div>
				</div>
				<div class="gdpr-preview-publish-btn">
					<div class="gdpr-preview-toggle-btn">
						<label class="gdpr-btn-label"><?php esc_attr_e( 'Preview Banner', 'gdpr-cookie-consent' ); ?></label>
							<c-switch class="gdpr-btn-switch" v-model="banner_preview_is_on" id="gdpr-banner-preview" variant="3d"  color="success" :checked="banner_preview_is_on" v-on:update:checked="onSwitchBannerPreviewEnable"></c-switch>
					</div>
					<c-button :disabled="save_loading" class="gdpr-publish-btn" @click="saveCookieSettings">{{ save_loading ? '<?php esc_html_e( 'Saving...', 'gdpr-cookie-consent' ); ?>' : '<?php esc_html_e( 'Save Changes', 'gdpr-cookie-consent' ); ?>' }}</c-button>
				</div>
			</div>
			<hr id="preview-btn-setting-nav-seperator">
			<c-tabs variant="pills" ref="active_tab" class="gdpr-cookie-consent-settings-nav">

			<!-- COMPLIANCES SECTION START -->
				<c-tab title="<?php esc_attr_e( 'General', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#general"id="gdpr-cookie-consent-complianz" >
						<!--  Banner preview  -->
						<c-card class="compliances_card">
							<c-card-body>
								<!-- Cookie Notice Section -->
								<c-row>
									<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice-top"><?php esc_html_e( 'Cookie Notice', 'gdpr-cookie-consent' ); ?></div></c-col>
								</c-row>
								<?php $gdpr_monthly_page_views = get_option('wpl_monthly_page_views', 0);
									$gdpr_monthly_page_views_percent = 0;
									if ( 'free' === $api_user_plan ) { 
										$gdpr_monthly_page_views_percent = ( ( $gdpr_monthly_page_views ) / 20000 ) * 100;
									} else if ( '3sites' === $api_user_plan ) {
										$gdpr_monthly_page_views_percent = ( ( $gdpr_monthly_page_views ) / 100000 ) * 100;
									}
								?>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Cookie Notice', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<label for="gdpr-cookie-consent-cookie-on" class="screen-reader-text"><?php esc_attr_e( 'Enable Cookie Consent', 'gdpr-cookie-consent' ); ?></label>
										<div class="gdpr-disabled-cookie-notice-integration">
										<c-switch v-bind="labelIcon" v-model="cookie_is_on" id="gdpr-cookie-consent-cookie-on" variant="3d"  color="success" :checked="<?php echo $gdpr_monthly_page_views_percent === 100 ? 'false' : 'cookie_is_on'; ?>" v-on:update:checked="onSwitchCookieEnable" <?php echo $gdpr_monthly_page_views_percent === 100 ? 'disabled' : ''; ?>></c-switch>
										<input type="hidden" name="gcc-cookie-enable" v-model="cookie_is_on">
										<?php if ($gdpr_monthly_page_views_percent === 100): ?>
											<p class="gdpr-cookie_notice_message">
												<?php esc_attr_e( 'Page views limit exhausted, upgrade to continue.', 'gdpr-cookie-consent' ); ?>
											</p>
										<?php endif; ?>
										</div>
									</c-col>
								</c-row>
								
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Select the Type of Law', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">

										<div class="gcc-law-mode-toggle">
											<label class="gcc-radio" :class="{ 'gcc-radio-disabled': disabled_for_free }">
												<input type="radio" value="auto" v-model="law_selection_mode"
													:disabled="disabled_for_free"
													@change="onLawModeChange">
												<?php esc_attr_e( 'Detect Automatically', 'gdpr-cookie-consent' ); ?>
												<small><?php esc_attr_e( '(based on visitor location)', 'gdpr-cookie-consent' ); ?></small>
												<a href="https://wplegalpages.com/pricing?utm_source=wp_cookie_consent&utm_medium=law_detector&utm_campaign=plugin_upgrade" v-if="disabled_for_free" class="probadge bg-badge"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 640" fill="#f5af2f"> <path d="M345 151.2C354.2 143.9 360 132.6 360 120C360 97.9 342.1 80 320 80C297.9 80 280 97.9 280 120C280 132.6 285.9 143.9 295 151.2L226.6 258.8C216.6 274.5 195.3 278.4 180.4 267.2L120.9 222.7C125.4 216.3 128 208.4 128 200C128 177.9 110.1 160 88 160C65.9 160 48 177.9 48 200C48 221.8 65.5 239.6 87.2 240L119.8 457.5C124.5 488.8 151.4 512 183.1 512L456.9 512C488.6 512 515.5 488.8 520.2 457.5L552.8 240C574.5 239.6 592 221.8 592 200C592 177.9 574.1 160 552 160C529.9 160 512 177.9 512 200C512 208.4 514.6 216.3 519.1 222.7L459.7 267.3C444.8 278.5 423.5 274.6 413.5 258.9L345 151.2z"/><path d="M180 550H460" fill="none" stroke="#f5af2f" stroke-width="28" stroke-linecap="round"/></svg></a>
											</label>
											<label class="gcc-radio">
												<input type="radio" value="manual" v-model="law_selection_mode" @change="onLawModeChange">
												<?php esc_attr_e( 'Choose Manually', 'gdpr-cookie-consent' ); ?>
											</label>
										</div>

										<v-select v-if="law_selection_mode === 'manual'"
											class="form-group gcc-law-select"
											id="gdpr-cookie-consent-policy-type"
											:reduce="option => option.code"
											:options="policy_options"
											:selectable="option => isOptionSelectable(option)"
											label="label"
											v-model="gdpr_policy"
											@input="cookiePolicyChange"
											:searchable="false">
											<template #option="option">
												<span class="gcc-flag">{{ option.flag }}</span> {{ option.label }}
												<a href="https://wplegalpages.com/pricing?utm_source=wp_cookie_consent&utm_medium=pro_law&utm_campaign=plugin_upgrade" v-if="isProOnlyLaw(option.code) && disabled_for_free" class="probadge bg-badge"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 640" fill="#f5af2f"> <path d="M345 151.2C354.2 143.9 360 132.6 360 120C360 97.9 342.1 80 320 80C297.9 80 280 97.9 280 120C280 132.6 285.9 143.9 295 151.2L226.6 258.8C216.6 274.5 195.3 278.4 180.4 267.2L120.9 222.7C125.4 216.3 128 208.4 128 200C128 177.9 110.1 160 88 160C65.9 160 48 177.9 48 200C48 221.8 65.5 239.6 87.2 240L119.8 457.5C124.5 488.8 151.4 512 183.1 512L456.9 512C488.6 512 515.5 488.8 520.2 457.5L552.8 240C574.5 239.6 592 221.8 592 200C592 177.9 574.1 160 552 160C529.9 160 512 177.9 512 200C512 208.4 514.6 216.3 519.1 222.7L459.7 267.3C444.8 278.5 423.5 274.6 413.5 258.9L345 151.2z"/><path d="M180 550H460" fill="none" stroke="#f5af2f" stroke-width="28" stroke-linecap="round"/></svg></a>
											</template>
											<template #selected-option="option">
												<span class="gcc-flag">{{ option.flag }}</span> {{ option.label }}
											</template>
										</v-select>

										<input type="hidden" name="gcc-law-selection-mode" v-model="law_selection_mode">
										<input type="hidden" name="gcc-gdpr-policy" v-model="gdpr_policy">
									</c-col>
								</c-row>
								<c-row class="gdpr-cookie-consent-laws-type" v-show="law_selection_mode === 'auto'">
									<c-col class="col-sm-4"></c-col>
									<c-col class="col-sm-8">
										<p class="policy-description">
											<?php echo esc_html__( "We'll show each visitor the consent banner for their region's law automatically — no manual selection needed. You can still customize wording per law from the Content and Design section.", 'gdpr-cookie-consent' ); ?>
										</p>
									</c-col>
								</c-row>

								<c-row class="gdpr-cookie-consent-laws-type" v-show="law_selection_mode === 'manual' && is_gdpr">
									<c-col class="col-sm-4"></c-col>
									<c-col class="col-sm-8">
										<p class="policy-description">
											<?php echo esc_html__( 'Covers the EU General Data Protection Regulation.', 'gdpr-cookie-consent' ); ?>
										</p>
										<div class="cookie-notice-readmore-container">
											<a class="cookie-notice-readmore" href="<?php echo esc_url( 'https://wplegalpages.com/blog/gdpr/' ); ?>" target="_blank">
												<?php echo esc_html__( 'Learn more about setting up a GDPR notice', 'gdpr-cookie-consent' ); ?>
											</a>
										</div>
									</c-col>
								</c-row>

								<c-row class="gdpr-cookie-consent-laws-type" v-show="law_selection_mode === 'manual' && is_us_state_laws">
									<c-col class="col-sm-4"></c-col>
									<c-col class="col-sm-8">
										<p class="policy-description">
											<?php echo esc_html__( "Shows the right banner for each visitor's state — covers CCPA/CPRA (California), VCDPA (Virginia), CPA (Colorado), CTDPA (Connecticut), and UCPA (Utah).", 'gdpr-cookie-consent' ); ?>
										</p>
										<div class="cookie-notice-readmore-container">
											<a class="cookie-notice-readmore" href="<?php echo esc_url( 'https://wplegalpages.com/blog/ccpa/' ); ?>" target="_blank">
												<?php echo esc_html__( 'Learn more about setting up a US State Laws notice', 'gdpr-cookie-consent' ); ?>
											</a>
										</div>
									</c-col>
								</c-row>

								<c-row class="gdpr-cookie-consent-laws-type" v-show="law_selection_mode === 'manual' && is_eprivacy">
									<c-col class="col-sm-4"></c-col>
									<c-col class="col-sm-8">
										<div class="cookie-notice-readmore-container">
											<a class="cookie-notice-readmore" href="https://wplegalpages.com/blog/eprivacy-directive-vs-gdpr/" target="_blank">
												<?php esc_attr_e( 'Learn more about setting up an ePrivacy notice', 'gdpr-cookie-consent' ); ?>
											</a>
										</div>
									</c-col>
								</c-row>


								<!-- THIS HAS TO MOVE TO CONTENT AND DESIGN  -->



								<!-- TILL HERE MOVE TO CONTENT AND DESIGN ^^^^^^^ -->

								<!-- Visitors Condition -->
								<c-row>
									<c-col class="col-sm-32"><div style="display: flex; align-items: center; gap:3px;" id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Cookie Banner Geo-Targeting', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Display different cookie banners based on the visitor’s region.', 'gdpr-cookie-consent' ); ?>"></tooltip>  <a href="https://wplegalpages.com/pricing?utm_source=wp_cookie_consent&utm_medium=geo_targetting&utm_campaign=plugin_upgrade" style="margin-bottom:3px;" class="probadge bg-badge"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 640" fill="#f5af2f"> <path d="M345 151.2C354.2 143.9 360 132.6 360 120C360 97.9 342.1 80 320 80C297.9 80 280 97.9 280 120C280 132.6 285.9 143.9 295 151.2L226.6 258.8C216.6 274.5 195.3 278.4 180.4 267.2L120.9 222.7C125.4 216.3 128 208.4 128 200C128 177.9 110.1 160 88 160C65.9 160 48 177.9 48 200C48 221.8 65.5 239.6 87.2 240L119.8 457.5C124.5 488.8 151.4 512 183.1 512L456.9 512C488.6 512 515.5 488.8 520.2 457.5L552.8 240C574.5 239.6 592 221.8 592 200C592 177.9 574.1 160 552 160C529.9 160 512 177.9 512 200C512 208.4 514.6 216.3 519.1 222.7L459.7 267.3C444.8 278.5 423.5 274.6 413.5 258.9L345 151.2z"/><path d="M180 550H460" fill="none" stroke="#f5af2f" stroke-width="28" stroke-linecap="round"/></svg></a></div></c-col>
								</c-row>
								<div class="gdpr-visitors-condition">
									<div>
										<div><input class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-worldwide-enable" v-model="selectedRadioWorldWide" @click="onSwitchWorldWideEnable" id="gcc-worldwide-enable"><label for="gcc-worldwide-enable"><?php esc_attr_e( 'Show to users from all countries', 'gdpr-cookie-consent' ); ?></label></div>
										<div>
											<input type="hidden" name="gcc-worldwide-enable" v-model="is_worldwide_on">
										</div>
									</div>
									<div>
											<?php
											$geo_options = get_option( 'wpl_geo_options' );
											 if ( !$is_user_connected || empty($is_user_connected) || $api_user_plan === 'free') : ?>
												<div v-show="is_auto_mode ? false : !!region_label" class="gdpr-disabled-geo-integration">
													<input id="gdpr-visitors-condition-radio-btn-disabled-gdpr" class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-region-enable" disabled>
													<label style="display: inline-flex; align-items: center; gap: 3px;" for="gdpr-visitors-condition-radio-btn-disabled-gdpr">{{ region_label }} <a href="https://wplegalpages.com/pricing?utm_source=wp_cookie_consent&utm_medium=geo_targetting&utm_campaign=plugin_upgrade" style="margin-bottom: 4px;"  class="probadge bg-badge"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 640" fill="#f5af2f"> <path d="M345 151.2C354.2 143.9 360 132.6 360 120C360 97.9 342.1 80 320 80C297.9 80 280 97.9 280 120C280 132.6 285.9 143.9 295 151.2L226.6 258.8C216.6 274.5 195.3 278.4 180.4 267.2L120.9 222.7C125.4 216.3 128 208.4 128 200C128 177.9 110.1 160 88 160C65.9 160 48 177.9 48 200C48 221.8 65.5 239.6 87.2 240L119.8 457.5C124.5 488.8 151.4 512 183.1 512L456.9 512C488.6 512 515.5 488.8 520.2 457.5L552.8 240C574.5 239.6 592 221.8 592 200C592 177.9 574.1 160 552 160C529.9 160 512 177.9 512 200C512 208.4 514.6 216.3 519.1 222.7L459.7 267.3C444.8 278.5 423.5 274.6 413.5 258.9L345 151.2z"/><path d="M180 550H460" fill="none" stroke="#f5af2f" stroke-width="28" stroke-linecap="round"/></svg></a></label>
												</div>
												<p class="gdpr-law_region_visitors_message-gdpr">
													<?php esc_attr_e( 'To enable this feature, connect to your pro account', 'gdpr-cookie-consent' ); ?>
												</p>
											<?php elseif ( $the_options['enable_safe'] === true || $the_options['enable_safe'] === 'true' ) : ?>
												<div v-show="is_auto_mode ? false : !!region_label" class="gdpr-disabled-geo-integration">
													<input id="gdpr-visitors-condition-radio-btn-disabled-gdpr" class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-region-enable" disabled>
													<label for="gdpr-visitors-condition-radio-btn-disabled-gdpr">{{ region_label }}</label>
												</div>
												<p class="gdpr-law_region_visitors_message-gdpr">
													<?php esc_attr_e( 'Safe Mode enabled. Disable it in Advanced > Cookie & Privacy to configure Geo-Targeting settings.', 'gdpr-cookie-consent' ); ?>
												</p>
											<?php else : ?>
												<div v-show="is_auto_mode ? false : !!region_label">
													<input id="gdpr-region-id" class="gdpr-visiotrs-condition-radio-btn" type="checkbox"
														name="gcc-region-enable" v-model="is_law_region_on" @click="onSwitchRegionEnable($event.target.checked)">
													<label for="gdpr-region-id">Show only to users from {{ region_label }}</label>
												</div>
												<input type="hidden" name="gcc-region-enable" v-model="is_law_region_on">
											<?php endif; ?>
									</div>
									<div>
										<?php
											$geo_options = get_option( 'wpl_geo_options' );
										if ( !$is_user_connected || empty($is_user_connected) || $api_user_plan === 'free') :
											?>
											<div class="gdpr-disabled-geo-integration"><input class="gdpr-visiotrs-condition-radio-btn" id="gdpr-visitors-condition-radio-btn-disabled-both" type="checkbox" name="gcc-select-countries-enable" disabled><label  style="display: inline-flex; align-items: center; gap: 3px;"><?php esc_attr_e( 'Show to specific countries', 'gdpr-cookie-consent' ); ?><a href="https://wplegalpages.com/pricing?utm_source=wp_cookie_consent&utm_medium=geo_targetting&utm_campaign=plugin_upgrade" style="margin-bottom: 4px;"  class="probadge bg-badge"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 640" fill="#f5af2f"> <path d="M345 151.2C354.2 143.9 360 132.6 360 120C360 97.9 342.1 80 320 80C297.9 80 280 97.9 280 120C280 132.6 285.9 143.9 295 151.2L226.6 258.8C216.6 274.5 195.3 278.4 180.4 267.2L120.9 222.7C125.4 216.3 128 208.4 128 200C128 177.9 110.1 160 88 160C65.9 160 48 177.9 48 200C48 221.8 65.5 239.6 87.2 240L119.8 457.5C124.5 488.8 151.4 512 183.1 512L456.9 512C488.6 512 515.5 488.8 520.2 457.5L552.8 240C574.5 239.6 592 221.8 592 200C592 177.9 574.1 160 552 160C529.9 160 512 177.9 512 200C512 208.4 514.6 216.3 519.1 222.7L459.7 267.3C444.8 278.5 423.5 274.6 413.5 258.9L345 151.2z"/><path d="M180 550H460" fill="none" stroke="#f5af2f" stroke-width="28" stroke-linecap="round"/></svg></a></label></div>
											<p class="gdpr-eu_visitors_message-both">
											<?php esc_attr_e( 'To enable this feature, connect to your pro account', 'gdpr-cookie-consent' ); ?>
											</p>
										<?php elseif ( $the_options['enable_safe'] === true || $the_options['enable_safe'] === 'true' ) : ?>
											<div class="gdpr-disabled-geo-integration"><input class="gdpr-visiotrs-condition-radio-btn" id="gdpr-visitors-condition-radio-btn-disabled-both" type="checkbox" name="gcc-select-countries-enable" disabled><label><?php esc_attr_e( 'Show to specific countries', 'gdpr-cookie-consent' ); ?></label></div>
											<p class="gdpr-eu_visitors_message-both">
												<?php esc_attr_e( 'Safe Mode enabled. Disable it in Advanced > Cookie & Privacy to configure Geo-Targeting settings.', 'gdpr-cookie-consent' ); ?>
											</p>
										<?php else : ?>
											<div><input id="gdpr-select-countries" class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-select-countries-enable" v-model="selectedRadioCountry" @click="onSwitchSelectedCountryEnable($event.target.checked)"><label for="gdpr-select-countries"><?php esc_attr_e( 'Show to specific countries', 'gdpr-cookie-consent' ); ?></label></div>
											<input type="hidden" name="gcc-select-countries-enable" v-model="is_selectedCountry_on">
										<?php endif; ?>
									</div>
								</div>
								<div class="select-countries-dropdown" v-show="(is_selectedCountry_on)">
									<v-select id="gdpr-cookie-consent-geotargeting-countries" placeholder="Select Countries":reduce="label => label.code" class="form-group" :options="list_of_countries" multiple v-model="select_countries_array" @input="onCountrySelect"></v-select>
									<input type="hidden" name="gcc-selected-countries" v-model="select_countries">
								</div>
								<p class="maxmind-notice">This product includes GeoLite2 data created by MaxMind, available from <a href="https://www.maxmind.com">https://www.maxmind.com</a>.</p>

								<c-row></c-row>

								<c-row>
									<c-col class="col-sm-4 relative">
										<label>
											<?php esc_attr_e( 'Respect Do Not Track & Global Privacy Control', 'gdpr-cookie-consent' ); ?>  
											<tooltip v-if="!is_us_state_laws" text="<?php esc_html_e( 'Automatically deny cookies for users who have enabled DNT or GPC in their browser settings.', 'gdpr-cookie-consent'); ?>"></tooltip>
											<tooltip v-if="is_us_state_laws" text="<?php esc_html_e( 'Automatically deny cookies for users who have enabled DNT or GPC in their browser settings.(Cumpolsary for US State Laws)', 'gdpr-cookie-consent'); ?>"></tooltip>
										</label>
									</c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind= labelIcon v-model="do_not_track_on" :disabled="is_us_state_laws" id="gdpr-cookie-do-not-track" variant="3d" color="success" :checked="do_not_track_on" v-on:update:checked="onSwitchDntEnable"></c-switch>
										<input type="hidden" name="gcc-do-not-track" v-model="do_not_track_on">
									</c-col>
								</c-row>

								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Auto-Detect Banner Language ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( " Automatically sets the cookie banner language to match your visitor's preferred browser language, providing a more localized experience. ", 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="dynamic_lang_is_on" id="gdpr-cookie-consent-dynamic-lang-on" variant="3d"  color="success" :checked="dynamic_lang_is_on" v-on:update:checked="onSwitchDynamicLang"></c-switch>
										<input type="hidden" name="gcc-dynamic-lang-enable" v-model="dynamic_lang_is_on">
									</c-col>
								</c-row>

								<c-row>
									<c-col
										class="col-sm-4"
										:class="{'gdpr_disabled_langauge' : dynamic_lang_is_on}"
									>
										<label>
											<?php esc_attr_e( 'Select a language for your cookie consent banner', 'gdpr-cookie-consent' ); ?>

											
										
										<tooltip
											v-if="dynamic_lang_is_on"
											text="<?php esc_attr_e( 'You cannot manually set the banner language while Auto-Detect Banner Language is enabled. The banner language will be automatically selected based on the site visitor\'s browser language preference.', 'gdpr-cookie-consent' ); ?>"
										></tooltip>

										<tooltip
											v-else
											text="<?php esc_attr_e( 'Select the language used for your cookie banner content. Changing the language will override any changes you have made to the banner text.', 'gdpr-cookie-consent' ); ?>"
										></tooltip></label>
									</c-col>
									<c-col class="col-sm-8">
										<input type="hidden" name="select-banner-lan" v-model="show_language_as">
										<v-select class="form-group" id="gdpr-select-banner-lan" :reduce="label => label.code" :disabled="dynamic_lang_is_on" :options="show_language_as_options" v-model="show_language_as"  @input="onLanguageChange"></v-select>
									</c-col>
								</c-row>

								<!-- For hide banner -->
								<?php
								$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ? GDPR_COOKIE_CONSENT_VERSION : '';
								if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
									if ( ! $is_pro_active ) :
										?>
										<c-row>
											<c-col class="col-sm-4 relative"><label><?php esc_attr_e( 'Hide cookie banner on specific pages', 'gdpr-cookie-consent' ); ?></label>
												<div class="gdpr-pro-label absolute" style="top: -1.5px;" ><div class="gdpr-pro-label-text">Pro</div></div>
											</c-col>
											<c-col class="col-sm-8">
											<v-select disabled id="gdpr-cookie-consent-hide-banner" :reduce="label => label.code" class="form-group" :options="list_of_pages" multiple></v-select>
											<input type="hidden" name="gcc-selected-pages">
											</c-col>
										</c-row>
									<?php endif ?>
									<?php
									do_action( 'gdpr_hide_pages' );
								} else {
									?>
										<c-row>
											<c-col class="col-sm-4 relative"><label><?php esc_attr_e( 'Hide cookie banner on specific pages', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-8">
												<v-select id="gdpr-cookie-consent-hide-banner" placeholder="Select pages":reduce="label => label.code" class="form-group" :options="list_of_pages" multiple v-model="select_pages_array" @input="onPageSelect"></v-select>
												<input type="hidden" name="gcc-selected-pages" v-model="select_pages">
											</c-col>
										</c-row>
								<?php } ?>

								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Restrict Pages and/or Posts', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Restrict Pages and/or Posts during scanning of your website for cookies.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-8">
										<v-select  placeholder="Select Pages and Posts" id="gdpr-cookie-consent-restrict-posts" :reduce="label => label.code" class="form-group" :options="list_of_contents" multiple v-model="restrict_array" @input="onPostsSelect"></v-select>
										<input type="hidden" name="gcc-restrict-posts" v-model="restrict_posts">
									</c-col>
								</c-row>


								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Banner Initialization', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Control when the cookie banner should appear, immediately on page load or after user interaction.', 'gdpr-cookie-consent'); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="auto_banner_initialize" id="gdpr-cookie-consent-auto_initialize" variant="3d"  color="success" :checked="auto_banner_initialize" v-on:update:checked="onSwitchAutoBannerInitialize"></c-switch>
										<input type="hidden" name="gcc-auto-banner-initialize" v-model="auto_banner_initialize">
									</c-col>
								</c-row>
								<c-row v-show="auto_banner_initialize">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Banner Initialization Delay (in milliseconds)', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<c-input type="number" min="0" max="60000" step="1000" name="gcc-auto-banner-initialize-delay" v-model="auto_banner_initialize_delay"></c-input>
									</c-col>
								</c-row>

								<!-- renew consent free  -->
								<c-row>
									<c-col class="col-sm-4 relative"><label><?php esc_attr_e( 'Renew User Consent', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( "If you modify your website's data collection methods, such as manually introducing new cookies or revising your cookie policy/banner message, we strongly advise renewing the consents granted by your existing users. Taking this step will prompt the cookie banner to reappear for all users who had previously provided consent", 'gdpr-cookie-consent' ); ?>"></tooltip>
									</label>
									</c-col>
									<c-col class="col-sm-8">
									<c-button class="gdpr-renew-now-btn pro" variant="outline" @click="onClickRenewConsent">
										<?php esc_html_e( 'Renew Now', 'gdpr-cookie-consent' ); ?>
										<img  id="renew-consent-img"src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/renew-arrow.svg'; ?>" alt="Renew consentlogo">
									</c-button>
									<input type="hidden" name="gcc-consent-renew-enable" v-model="consent_version">
									<!-- last renewed  -->
									<div class="gdpr-last-renew-container">
										<div class="gdpr-last-renew-label">
										Last renewed :
										</div>
										<div class="gdpr-last-renew-details">
										<?php
										$last_renewed_at = get_option( 'wpl_consent_timestamp' );
										if ( $last_renewed_at ) {
											echo esc_attr( gmdate( 'F j, Y g:i a T', get_option( 'wpl_consent_timestamp' ) ) );
										} else {
											echo esc_attr_e( ' Not renewed yet', 'gdpr-cookie-consent' );
										}
										?>
										</div>
									</div>
									</c-col>
								</c-row>
								
								<!-- Data Requests  -->
								<?php if ( ! $is_pro_active ) { ?>
								<c-row>
									<c-col class="col-sm-4 relative"><label><?php esc_attr_e( 'Enable Data Request Form', 'gdpr-cookie-consent' ); ?><tooltip class="gdpr_data_req_tooltip" text="<?php esc_html_e( 'Enable to add data request form to your Privacy Statement.', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
									</c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon " v-model="data_reqs_on" id="gdpr-cookie-data-reqs" variant="3d" color="success" :checked="data_reqs_on" v-on:update:checked="onSwitchDataReqsEnable"></c-switch>
										<input type="hidden" name="gcc-data_reqs" v-model="data_reqs_on">
									</c-col>
								</c-row>
								<!-- clipboard for shortcode to copy  -->
								<c-row v-show="data_reqs_on">
									<c-col class="col-sm-4 relative"><label><?php esc_attr_e( 'Shortcode for Data Request', 'gdpr-cookie-consent' ); ?><tooltip class="gdpr-sc-tooltip" text="<?php esc_html_e( 'You can use this Shortcode [wpl_data_request] to display the data request form on any page', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
									</c-col>
									<c-col class="col-sm-8">
										<c-button id="data-request-btn" class="btn btn-info" variant="outline" @click="copyTextToClipboard">{{ shortcode_copied ? 'Shortcode Copied!' : 'Click to Copy' }}</c-button>
									</c-col>
								</c-row>

								<!-- email box  -->
								<c-row v-show="data_reqs_on" id="gdpr-data-req-admin-container" >
									<div class="gdpr-data-req-main-container">

										<div class="gdpr-data-req-email-container">
											<!-- notification sender email  -->
											<div class="gdpr-data-req-sender-email">
												<c-col class="col-sm-12">
													<span>Notification Sender Email Address</span>
												</c-col>
												<!-- notification sender email text box  -->
												<c-col class="col-sm-12 gdpr-data-req-sender-email-input">
													<div id="validation-icon">
														<!-- Default state with the right tick -->
														<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" height="15" width="15" >
															<path fill="#00CF21"d="M438.6 105.4C451.1 117.9 451.1 138.1 438.6 150.6L182.6 406.6C170.1 419.1 149.9 419.1 137.4 406.6L9.372 278.6C-3.124 266.1-3.124 245.9 9.372 233.4C21.87 220.9 42.13 220.9 54.63 233.4L159.1 338.7L393.4 105.4C405.9 92.88 426.1 92.88 438.6 105.4H438.6z"></path>
														</svg>
													</div>
													<c-input name="data_req_email_text_field"  placeholder="example@example.com" v-model="data_req_email_address"  id="email-input" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>

												</c-col>
												<!-- email validation script -->
												<script>
													document.addEventListener('DOMContentLoaded', function () {
														// Get the input element and the validation icon element
														var emailInput = document.getElementById('email-input');
														var validationIcon = document.getElementById('validation-icon');

														// Add an event listener on input change
														if(emailInput !== null)emailInput.addEventListener('input', function () {
															// Validate the email format using a regular expression
															var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
															var isValidEmail = emailPattern.test(emailInput.value);

															// Update the validation icon based on validity
															validationIcon.innerHTML = isValidEmail
																? '<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" height="15" width="15"><path fill="#00CF21" d="M438.6 105.4C451.1 117.9 451.1 138.1 438.6 150.6L182.6 406.6C170.1 419.1 149.9 419.1 137.4 406.6L9.372 278.6C-3.124 266.1-3.124 245.9 9.372 233.4C21.87 220.9 42.13 220.9 54.63 233.4L159.1 338.7L393.4 105.4C405.9 92.88 426.1 92.88 438.6 105.4H438.6z"></path></svg>'
																: '<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" height="15" width="15"><path fill="red" d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z"></path></svg>';

															// Adjust the padding-right property based on the presence of the icon
															emailInput.style.paddingRight = isValidEmail ? '30px' : '0';
														});
													});
												</script>
											</div>

											<div class="gdpr-data-req-email-subject">
												<!-- notification email subject  -->
												<c-col class="col-sm-12">
													<span>Notification Email Subject</span>
												</c-col>
												<!-- notification email subject text box  -->
												<c-col class="col-sm-12 gdpr-data-req-subject-input">
													<div id="validation-icon-subject">
														<!-- Default state with the right tick -->
														<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" height="15" width="15" >
															<path fill="#00CF21" d="M438.6 105.4C451.1 117.9 451.1 138.1 438.6 150.6L182.6 406.6C170.1 419.1 149.9 419.1 137.4 406.6L9.372 278.6C-3.124 266.1-3.124 245.9 9.372 233.4C21.87 220.9 42.13 220.9 54.63 233.4L159.1 338.7L393.4 105.4C405.9 92.88 426.1 92.88 438.6 105.4H438.6z"></path>
														</svg>
													</div>
													<c-input name="data_req_subject_text_field" placeholder="We have received your request" v-model="data_req_subject" id="subject-input" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
												</c-col>
											</div>

											<div class="gdpr-data-req-email-content">
												<!-- notification email content  -->
												<c-col class="col-sm-12">
													<span>Notification Email Content</span>
												</c-col>
											</div>

											<div class="gdpr-data-req-email-editor">
												<c-col class="col-sm-12">
													<div class="gdpr-add-media-link-icon">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<path d="M14 10L10 14" stroke="#3399FF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
														<path d="M16 13L18 11C19.3807 9.61929 19.3807 7.38071 18 6V6C16.6193 4.61929 14.3807 4.61929 13 6L11 8M8 11L6 13C4.61929 14.3807 4.61929 16.6193 6 18V18C7.38071 19.3807 9.61929 19.3807 11 18L13 16" stroke="#3399FF" stroke-width="1.5" stroke-linecap="round"/>
														</svg>
													</div>
													<c-button id="add-media-button" class="gdpr-renew-now-btn pro" variant="outline" @click="onClickAddMedia"><span><?php esc_html_e( 'Add Media', 'gdpr-cookie-consent' ); ?></span></c-button>

												</c-col>
												<!-- notification text box  -->
												<c-col class="col-sm-12">
													<vue-editor name="data_req_mail_content_text_field" v-model="data_req_editor_message"></vue-editor>
													<input type="hidden" name="data_req_mail_content_text_field" v-model="data_req_editor_message">
												</c-col>
											</div>
										</div>

									</div>


								</c-row>

								<?php } ?>

								<?php do_action( 'gdpr_consent_settings_data_reqs' ); ?>
								
								<c-row class="gdpr_ad_tech_heading">
									<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice-top"><?php esc_html_e( 'Ad-tech Integrations', 'gdpr-cookie-consent' ); ?></div></c-col>
									<c-button class="gdpr_ad_tech_dropdown_arrow" :style="{ transform: ad_tech_expanded ? 'rotate(180deg)' : null }" @click="onSwitchAdTechExpanded">
										<svg
											class="gdpr-subnav-chevron"
											width="30"
											height="30"
											viewBox="0 0 20 20"
											fill="none"
											xmlns="http://www.w3.org/2000/svg"
										>
											<path
												d="M15 12.5L10 7.5L5 12.5"
												stroke="currentColor"
												stroke-width="2"
												stroke-linecap="round"
												stroke-linejoin="round"
											></path>
										</svg>
									</c-button>
									<span>IAB TCF v2.3 • Google Consent Mode (per-region consent table, wait-for-upgrade, redact ads data, debug tools)</span>
								</c-row>
								
								<c-row v-show="is_gdpr && ad_tech_expanded">
									<c-col class="col-sm-4"><label style="display: flex; align-items: center; gap: 3px;"><?php esc_attr_e( 'Support IAB TCF v2.3', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enable this to show a consent banner that complies with IAB Europe’s Transparency and Consent Framework v2.3 for ad personalization and tracking.', 'gdpr-cookie-consent'  ); ?>"></tooltip>  <a style="margin-bottom: 3px;" href="https://wplegalpages.com/pricing?utm_source=wp_cookie_consent&utm_medium=iab_tcf&utm_campaign=plugin_upgrade" class="probadge bg-badge"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 640" fill="#f5af2f"> <path d="M345 151.2C354.2 143.9 360 132.6 360 120C360 97.9 342.1 80 320 80C297.9 80 280 97.9 280 120C280 132.6 285.9 143.9 295 151.2L226.6 258.8C216.6 274.5 195.3 278.4 180.4 267.2L120.9 222.7C125.4 216.3 128 208.4 128 200C128 177.9 110.1 160 88 160C65.9 160 48 177.9 48 200C48 221.8 65.5 239.6 87.2 240L119.8 457.5C124.5 488.8 151.4 512 183.1 512L456.9 512C488.6 512 515.5 488.8 520.2 457.5L552.8 240C574.5 239.6 592 221.8 592 200C592 177.9 574.1 160 552 160C529.9 160 512 177.9 512 200C512 208.4 514.6 216.3 519.1 222.7L459.7 267.3C444.8 278.5 423.5 274.6 413.5 258.9L345 151.2z"/><path d="M180 550H460" fill="none" stroke="#f5af2f" stroke-width="28" stroke-linecap="round"/></svg></a></label></c-col>
									<c-col class="col-sm-8">
										<?php 
											$is_disabled = (!$is_user_connected || $api_user_plan === 'free');
										?>
										<label for="gdpr-cookie-consent-iabtcf-on" class="screen-reader-text"><?php esc_attr_e( 'IAB On','gdpr-cookie-consent'); ?></label>
										<div class="gdpr-disabled-iab-integration">
										<c-switch v-bind="labelIcon" v-model="iabtcf_is_on" id="gdpr-cookie-consent-iabtcf-on" variant="3d"  color="success" :checked="<?php echo $is_disabled ? 'false' : 'iabtcf_is_on'; ?>" v-on:update:checked="onSwitchIabtcfEnable"  <?php echo $is_disabled ? 'disabled' : ''; ?>></c-switch>
										<input type="hidden" name="gcc-iabtcf-enable" v-model="iabtcf_is_on">
										<?php if (!$is_user_connected): ?>
											<p class="gdpr-iab_message">
												<?php esc_attr_e( 'To enable this feature, connect to your pro account', 'gdpr-cookie-consent' ); ?>
											</p>
										<?php endif; ?>
										</div>
									</c-col>
								</c-row>
								<c-row v-show="is_gdpr && iabtcf_is_on && ad_tech_expanded">
									<?php if (strtolower($api_user_plan) !== 'free') { ?>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Support Google Additional Consent Mode', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<label for="gdpr-cookie-consent-gacm-on" class="screen-reader-text"><?php esc_attr_e( 'gdpr-cookie-consent-gacm-on','gdpr-cookie-consent'); ?></label>
										<c-switch v-bind="labelIcon" v-model="gacm_is_on" id="gdpr-cookie-consent-gacm-on" variant="3d"  color="success" :checked="gacm_is_on" v-on:update:checked="onSwitchGacmEnable"></c-switch>
										<input type="hidden" name="gcc-gacm-enable" v-model="gacm_is_on">
									</c-col>
									<?php } else if($is_user_connected) { ?>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Support Google Additional Consent Mode', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8 gacm-slider">
										<label for="gdpr-cookie-consent-gacm-on" class="screen-reader-text"><?php esc_attr_e( 'gdpr-cookie-consent-gacm-on','gdpr-cookie-consent'); ?></label>
										<c-switch v-bind="labelIcon" v-model="gacm_is_on" id="gdpr-cookie-consent-gacm-on" variant="3d"  color="success" :checked="gacm_is_on" v-on:update:checked="onSwitchGacmEnable" disabled></c-switch>
										<input type="hidden" name="gcc-gacm-enable" v-model="gacm_is_on">
										<p class=" gdpr-gacm_message-gdpr">
											<?php esc_attr_e( 'To enable this feature, upgrade to a pro plan', 'gdpr-cookie-consent' ); ?>
										</p>
									</c-col>
									<?php } else { ?>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Support Google Additional Consent Mode', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8 gacm-slider">
										<label for="gdpr-cookie-consent-gacm-on" class="screen-reader-text"><?php esc_attr_e( 'gdpr-cookie-consent-gacm-on','gdpr-cookie-consent'); ?></label>
										<c-switch v-bind="labelIcon" v-model="gacm_is_on" id="gdpr-cookie-consent-gacm-on" variant="3d"  color="success" :checked="gacm_is_on" v-on:update:checked="onSwitchGacmEnable" disabled></c-switch>
										<input type="hidden" name="gcc-gacm-enable" v-model="gacm_is_on">
										<p class=" gdpr-gacm_message-gdpr">
											<?php esc_attr_e( 'To enable this feature, connect to an account and purchase a paid plan.', 'gdpr-cookie-consent' ); ?>
										</p>
									</c-col>
									<?php }?>
								</c-row>
								<div :class="{ 'gdpr-gcm-card': gcm_is_on }" v-show="ad_tech_expanded">
									<c-row v-show="!is_us_state_laws || is_gdpr">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Support Google Consent Mode(GCM)', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Activate this to integrate Google Consent Mode and control how Google tags behave based on user consent.', 'gdpr-cookie-consent'  ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<label for="gdpr-cookie-consent-gcm-on" class="screen-reader-text"><?php esc_attr_e( 'gdpr-cookie-consent-gcm-on','gdpr-cookie-consent'); ?></label>
											<c-switch v-bind="labelIcon" v-model="gcm_is_on" id="gdpr-cookie-c:onsent-gcm-on" variant="3d"  color="success" :checked="gcm_is_on" v-on:update:checked="onSwitchGCMEnable"></c-switch>
											<input type="hidden" name="gcc-gcm-enable" v-model="gcm_is_on">
										</c-col>
									</c-row>
									<c-row v-show="!is_us_state_laws || is_gdpr" style="margin-top: -30px;"><c-col class="col-sm-4"></c-col><c-col class="col-sm-8"><p style="color:gray; font-weight:400;">Follow the guide <a class="cookie-notice-readmore" href = "https://wplegalpages.com/docs/wplp-docs/guides/implementing-google-consent-mode-using-wp-cookie-consent/" target="_blank">here</a> to correctly implement Google Consent Mode</p></c-col></c-row>
									<c-row v-show="gcm_is_on" style="margin-bottom: 0px;">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Default consent settings', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-12">
											<p class="policy-description">
												<?php echo esc_html__("The default consent state, 'Denied', will apply until consent is recieved. You can customize the default consent states for users in different geographical regions. ", 'gdpr-cookie-consent'); ?>
											</p>
										</c-col>
										<c-col class="col-sm-12">
											<table class="gcm-table">
												<thead>
													<tr>
														<th><?php echo esc_html__( 'Advertisement', 'gdpr-cookie-consent' ); ?></th>
														<th><?php echo esc_html__( 'Analytics', 'gdpr-cookie-consent' ); ?></th>
														<th><?php echo esc_html__( 'User ad data', 'gdpr-cookie-consent' ); ?></th>
														<th><?php echo esc_html__( 'Ad personalization data', 'gdpr-cookie-consent' ); ?></th>
														<th><?php echo esc_html__( 'Functional storage', 'gdpr-cookie-consent' ); ?></th>
														<th><?php echo esc_html__( 'Personalization storage', 'gdpr-cookie-consent' ); ?></th>
														<th><?php echo esc_html__( 'Security storage', 'gdpr-cookie-consent' ); ?></th>
														<th><?php echo esc_html__( 'Region', 'gdpr-cookie-consent' ); ?></th>
														<th><?php echo esc_html__( 'Actions', 'gdpr-cookie-consent' ); ?></th>
													</tr>
												</thead>
												<tbody v-for="(regionObj, index) in regions" :key="index">
													<tr>
														<td>{{ regionObj.ad_storage }}</td>
														<td>{{ regionObj.analytics_storage }}</td>
														<td>{{ regionObj.ad_user_data }}</td>
														<td>{{ regionObj.ad_personalization }}</td>
														<td>{{ regionObj.functionality_storage }}</td>
														<td>{{ regionObj.personalization_storage }}</td>
														<td>{{ regionObj.security_storage }}</td>
														<td>{{ regionObj.region }}</td>
														<td style="display: flex; justify-content: center; gap: 5px; border-top: none; border-left: none;"><button @click="edit_region_entry(index, $event)"><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/edit.png' ); ?>" alt="WPCS Edit image"></button><button @click="delete_gcm_data(index, $event)"><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/trash.png' ); ?>" alt="WPCS Trash icon"></button></td>
													</tr>
												</tbody>
											</table>
										</c-col>
										<c-col class="col-sm-12">
											<c-button id="add-region-btn" class="btn btn-info" variant="outline" @click="add_region=true"><?php echo esc_html__( '+ New Region', 'gdpr-cookie-consent' ); ?></c-button>
										</c-col>
										<div class="opt-out-link-container">
											<c-modal
												title="New Region"
												:show.sync="add_region"
												size="lg"
												:close-on-backdrop="closeOnBackdrop"
												:centered="centered"
											>
											<div class="optout-settings-tittle-bar">
													<div class="optout-setting-tittle"><?php esc_attr_e( 'New Region', 'gdpr-cookie-consent' ); ?></div>
													<img @click="close_region_popup" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
													</div>
													<div class="optout-settings-main-container" style="margin-top:20px">
											<c-row class="gdpr-label-row">
												<c-col class="col-sm-6">
													<label><?php esc_attr_e( 'Advertisment', 'gdpr-cookie-consent' ); ?></label>
												</c-col>
												<c-col class="col-sm-6">
													<label><?php esc_attr_e( 'Analytics', 'gdpr-cookie-consent' ); ?></label>
												</c-col>
											</c-row>
											<c-row>
												<c-col class="col-sm-6">
													<v-select class="form-group" id="gdpr-gcm-ad-permission" :reduce="label => label.code" :options="gcm_permission_options" v-model="newRegion.ad_storage"></v-select>
													<input type="hidden" name="gdpr-gcm-ad-permission" v-model="newRegion.ad_storage">
												</c-col>
												<c-col class="col-sm-6">
													<v-select class="form-group" id="gdpr-gcm-analytics-permission" :reduce="label => label.code" :options="gcm_permission_options" v-model="newRegion.analytics_storage"></v-select>
													<input type="hidden" name="gdpr-gcm-analytics-permission" v-model="newRegion.analytics_storage">
												</c-col>
											</c-row>
											<c-row  class="gdpr-label-row">
												<c-col class="col-sm-6">
													<label><?php esc_attr_e( 'User ad data', 'gdpr-cookie-consent' ); ?></label>
												</c-col>
												<c-col class="col-sm-6">
													<label><?php esc_attr_e( 'Ad personalization data', 'gdpr-cookie-consent' ); ?>	</label>
												</c-col>
											</c-row>
											<c-row>
												<c-col class="col-sm-6">
													<v-select class="form-group" id="gdpr-gcm-user-ad-permission" :reduce="label => label.code" :options="gcm_permission_options" v-model="newRegion.ad_user_data"></v-select>
													<input type="hidden" name="gdpr-gcm-user-ad-permission" v-model="newRegion.ad_user_data">
												</c-col>
												<c-col class="col-sm-6">
													<v-select class="form-group" id="gdpr-gcm-ad-personalization-permission" :reduce="label => label.code" :options="gcm_permission_options" v-model="newRegion.ad_personalization"></v-select>
													<input type="hidden" name="gdpr-gcm-ad-personalization-permission" v-model="newRegion.ad_personalization">
												</c-col>
											</c-row>
											<c-row   class="gdpr-label-row">
												<c-col class="col-sm-6">
													<label><?php esc_attr_e( 'Functional storage', 'gdpr-cookie-consent' ); ?></label>
												</c-col>
												<c-col class="col-sm-6">
													<label><?php esc_attr_e( 'Personalization storage', 'gdpr-cookie-consent' ); ?></label>
												</c-col>
											</c-row>
											<c-row >
												<c-col class="col-sm-6">
													<v-select class="form-group" id="gdpr-gcm-functional-permission" :reduce="label => label.code" :options="gcm_permission_options" v-model="newRegion.functionality_storage"></v-select>
													<input type="hidden" name="gdpr-gcm-functional-permission" v-model="newRegion.functionality_storage">
												</c-col>
												<c-col class="col-sm-6">
													<v-select class="form-group" id="gdpr-gcm-personalization-permission" :reduce="label => label.code" :options="gcm_permission_options" v-model="newRegion.personalization_storage"></v-select>
													<input type="hidden" name="gdpr-gcm-personalization-permission" v-model="newRegion.personalization_storage">
												</c-col>
											</c-row>
											<c-row   class="gdpr-label-row">
												<c-col class="col-sm-6">
													<label><?php esc_attr_e( 'Security storage', 'gdpr-cookie-consent' ); ?></label>
												</c-col>
												<c-col class="col-sm-6">
													<label><?php esc_attr_e( 'Regions', 'gdpr-cookie-consent' ); ?></label>
												</c-col>
											</c-row>
											<c-row >
												<c-col class="col-sm-6">
													<v-select class="form-group" id="gdpr-gcm-security-permission" :reduce="label => label.code" :options="gcm_permission_options" v-model="newRegion.security_storage"></v-select>
													<input type="hidden" name="gdpr-gcm-security-permission" v-model="newRegion.security_storage">
												</c-col>
												<c-col class="col-sm-6">
													<label for="gdpr-gcm-region" class="screen-reader-text"><?php esc_attr_e( 'GCM Region', 'gdpr-cookie-consent' ); ?></label>
													<c-input id="gdpr-gcm-region" name="gdpr-gcm-region" v-model="newRegion.region"></c-input>

												</c-col>
											</c-row>
											<c-row>
												<p class="policy-description" style="text-align: center; width: 100%;"><?php echo esc_html__('In regions, by specifying "All", consent will get applied to all regions. You can specify a comma separated list of region’s ISO-standardised' , 'gdpr-cookie-consent' )?> <a href="https://en.wikipedia.org/wiki/ISO_3166-2#:~:text=level%20of%20subdivisions.-,Current%20codes%5Bedit%5D,-The%20following%20table" target="_blank">(ISO 3166-2)</a> <?php echo esc_html__('codes to apply consent to specific regions.', 'gdpr-cookie-consent' )?>
												</p>
											</c-row>
											
													<button type="button" class="done-button-settings" @click="saveGCMDefault"><?php echo esc_html__('Done','gdpr-cookie-consent' )?></button></div>
												
											</c-modal>
										</div>
										<c-col class="col-sm-4" style="align-items:start; margin-top: -35px;"><label><?php esc_attr_e( 'Wait for update', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8" style="margin-top: 20px;">
											<label for="gcm_wait_for_update_duration_field" class="screen-reader-text"><?php esc_attr_e('GCM wait for update', 'gdpr-cookie-consent' ); ?></label>
											<c-input id="gcm_wait_for_update_duration_field" name="gcm_wait_for_update_duration_field" v-model="gcm_wait_for_update_duration"></c-input>
											<p class="policy-description">
												<?php 
													echo wp_kses(
														'Number of milliseconds to wait before firing tags that are waiting for consent.',
														array(
															'p'      => array(),
															'a'      => array(
																'href'   => true,
																'title'  => true,
																'target' => true,
															),
															'i'      => array(),
															'em'     => array(),
															'b'      => array(),
															'strong' => array(),
														)
													); 
												?>
											</p>
										</c-col>
										<c-col class="col-sm-4" style="align-items:start; margin-top: -50px;"><label><?php esc_attr_e( 'Pass ad click information through URLs', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8" style="margin-top: 20px;">
											<label for="gdpr-cookie-consent-gcm-url-passthrough" class="screen-reader-text"><?php esc_attr_e( 'gdpr-cookie-consent-gcm-url-passthrough','gdpr-cookie-consent'); ?></label>
											<c-switch v-bind="labelIcon" v-model="gcm_url_passthrough" id="gdpr-cookie-consent-gcm-url-passthrough" variant="3d"  color="success" :checked="gcm_url_passthrough" v-on:update:checked="onSwitchGCMUrlPass"></c-switch>
											<input type="hidden" name="gcc-gcm-url-pass" v-model="gcm_url_passthrough">
											<p class="policy-description cookie-notice-readmore-container">
												<?php 
													echo wp_kses(
														'When enabled, internal links will include advertising identifiers (such as gclid, dclid, gclsrc, and _gl) in their URLs while awaiting consent.',
														array(
															'p'      => array(),
															'a'      => array(
																'href'   => true,
																'title'  => true,
																'target' => true,
															),
															'i'      => array(),
															'em'     => array(),
															'b'      => array(),
															'strong' => array(),
														)
													); 
													?>
											</p>
										</c-col>
										<c-col class="col-sm-4" style="align-items:start; margin-top: -75px;"><label><?php esc_attr_e( 'Redact ads data', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8" style="margin-top: 20px;">
											<label for="gdpr-cookie-consent-gcm-ads-redact" class="screen-reader-text"><?php esc_attr_e( 'gdpr-cookie-consent-gcm-ads-redact','gdpr-cookie-consent'); ?></label>
											<c-switch v-bind="labelIcon" v-model="gcm_ads_redact" id="gdpr-cookie-consent-gcm-ads-redact" variant="3d"  color="success" :checked="gcm_ads_redact" v-on:update:checked="onSwitchGCMAdsRedact"></c-switch>
											<input type="hidden" name="gcc-gcm-ads-redact" v-model="gcm_ads_redact">
											<p class="policy-description cookie-notice-readmore-container">
												<?php
													echo wp_kses(
														'When enabled and the default consent state of "Advertisment" cookies is disabled, Google advertising tags will remove all advertising identifiers from the requests, and route traffic through domains that do not use cookies.',
														[
															'p'      => [],
															'a'      => [ 'href' => [], 'target' => [], 'rel' => [] ],
															'i'      => [],
															'em'     => [],
															'b'      => [],
															'strong' => [],
														]
													);
												?>
											</p>
										</c-col>

									<c-col class="col-sm-4" style="align-items:start; margin-top: -75px;"><label><?php esc_attr_e( 'Enable debug mode', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8" style="margin-top: 20px;">
										<label for="gdpr-cookie-consent-gcm-debug_mode" class="screen-reader-text"><?php esc_attr_e( 'gdpr-cookie-consent-gcm-debug_mode','gdpr-cookie-consent'); ?></label>
										<c-switch v-bind="labelIcon" v-model="gcm_debug_mode" id="gdpr-cookie-consent-gcm-debug_mode" variant="3d"  color="success" :checked="gcm_debug_mode" v-on:update:checked="onSwitchGCMDebugMode"></c-switch>
										<input type="hidden" name="gcc-gcm-debug-mode" v-model="gcm_debug_mode">
										<p class="policy-description cookie-notice-readmore-container">
											<?php
												echo wp_kses(
													'When enabled your browser console will display the GCM default status, update status, and whether default consent was set in correct order.<br>To open the browser console, right click on any webpage, select Inspect -> Console.',
													[
														'br'     => [],
														'p'      => [],
														'a'      => [
															'href'   => true,
															'title'  => true,
															'target' => true,
														],
														'i'      => [],
														'em'     => [],
														'b'      => [],
														'strong' => [],
													]
												);
											?>
										</p>
									</c-col>
									<c-col class="col-sm-4" style="align-items:start; margin-top: 10px;"><label><?php esc_attr_e( 'Check GCM Status', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8" style="margin-top: 10px; margin-bottom: 20px;">
										<c-button v-show="gcm_scan_flag === false" id="checkGcmStatusButton" class="btn btn-info" variant="outline" @click="checkGCMStatus">{{ 'Check' }}</c-button>
										<c-button v-show="gcm_scan_flag === true" id="checkGcmStatusLoadingButton" class="btn btn-info" variant="outline" disabled><span class="checkGCMloader"></span>{{ 'Checking Now' }}</c-button>
										<div v-show="gcm_scan_result != ''">
											<p class="gcm_status_success" v-show="gcm_scan_result['gtagExists'] == true && gcm_scan_result['hasConsentDefault'] == true && gcm_scan_result['hasConsentUpdate'] == true && gcm_scan_result['onTime'] == true">No errors detected</p>
											<p class="gcm_status_error" v-show="gcm_scan_result['gtagExists'] == false" >No tag Present</p>
											<p class="gcm_status_error" v-show="gcm_scan_result['hasConsentDefault'] == false" >Default Consent Missing</p>
											<p class="gcm_status_error" v-show="gcm_scan_result['hasConsentUpdate'] == false" >Update Conset Missing</p>
											<p class="gcm_status_error" v-show="gcm_scan_result['onTime'] == false" >Default Consent set too late</p>
											<p v-show="gcm_scan_result['gtagExists'] != true || gcm_scan_result['hasConsentDefault'] != true || gcm_scan_result['hasConsentUpdate'] != true || gcm_scan_result['onTime'] != true" style="color:gray; font-weight:400;">Read the <a class="cookie-notice-readmore" href = "https://wplegalpages.com/docs/wplp-docs/guides/google-consent-mode-troubleshooting-with-wplp-compliance-platform/" target="_blank">documentation</a> to know more about the errors and how to fix them.</p>
										</div>
									</c-col>
									<?php if($the_options['is_iabtcf_on'] === true || $the_options['is_iabtcf_on'] === "true" || $the_options['is_iabtcf_on'] === 1) : ?>
										<div class="col-sm-12 col" style="display: flex;" v-html="gcm_adver_mode_data" id="gcm-advertiser-mode-container"></div>
										<div id="gcm-advertiser-mode-container-loader"></div>
									<?php endif; ?>
								</c-row>
								</div>
								






								<?php do_action( 'gdpr_consent_settings_pro_bottom' ); ?>								
							</c-card-body>
						</c-card>
				</c-tab>

			<!-- CONFIGURATION SECTION START -->
				<c-tab title="<?php esc_attr_e( 'Layout', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#layout" id="gdpr-cookie-consent-configuration">

					<!-- Configure Banner preview  -->
					<c-card class="configuration_card">
						<c-card-body>
							<c-row>
								<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Configure Cookie Bar', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Show Cookie Notice as', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8">
									<input type="hidden" name="show-cookie-as" v-model="show_cookie_as">
									<v-select class="form-group" id="gdpr-show-cookie-as" :reduce="label => label.code" :options="show_cookie_as_options" v-model="show_cookie_as"  @input="cookieTypeChange"></v-select>
								</c-col>
							</c-row>
							<c-row style="margin-top:-28px;"v-show="show_cookie_as === 'banner'">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Position', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8">
								<div @click="cookiebannerPositionChange('bottom')" style="display: inline-block; cursor: pointer;position:relative;">
									<div>
									<span id="banner-position-bottom-icon" :class="{ 'dashicons dashicons-saved': cookie_position === 'bottom' }"></span>
									</div>
									<img 
									id="banner-position-bottom-id"
									:class="{ 'banner-position-bottom': cookie_position === 'bottom' }"
									src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/banner_bottom.svg'; ?>"
									alt="Bottom"
									>
								</div>
								<div @click="cookiebannerPositionChange('top')" style="display: inline-block; cursor: pointer;position:relative; padding-left:24px;">
									<div>
									<span id="banner-position-top-icon" :class="{ 'dashicons dashicons-saved': cookie_position === 'top' }"></span>
									</div>
									<img 
									id="banner-position-top-id"
									:class="{ 'banner-position-top': cookie_position === 'top' }"
									src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/banner_top.svg'; ?>"
									alt="Top"
									>
								</div>
								<input type="hidden" name="gcc-gdpr-cookie-position" v-model="cookie_position">
								</c-col>
							</c-row>
							<c-row style="margin-top:-28px;" v-show="show_cookie_as === 'widget'">
							<!-- notify_position_horizontal -->
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Position', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8">
							<div style="display: flex; align-items: flex-start; gap: 18px;">

									<div @click="cookiewidgetPositionChange('left')" style="position: relative; cursor: pointer;">
										<span id="widget-position-left-icon" :class="{ 'dashicons dashicons-saved': cookie_widget_position === 'left' }" style="position: absolute; top: 0; left: 0; z-index: 1;">
										</span>
										<img
										id="widget-position-left-id"
										:class="{ 'widget-position-top': cookie_widget_position === 'left' }"
										src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) .'admin/images/widget_bottom_left.svg';?>"
										alt="Bottom_left"
										>
									</div>

									<div @click="cookiewidgetPositionChange('right')" style="position: relative; cursor: pointer;">
										<span id="widget-position-right-icon" :class="{ 'dashicons dashicons-saved': cookie_widget_position === 'right' }" style="position: absolute; top: 0; left: 0; z-index: 1;"></span>
										<img
										id="widget-position-right-id"
										:class="{ 'widget-position-top': cookie_widget_position === 'right' }"
										src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) .'admin/images/widget_bottom_right.svg';?>"
										alt="Bottom_right"
										>
									</div>

									<div @click="cookiewidgetPositionChange('top_left')" style="position: relative; cursor: pointer;">
										<span id="widget-position-top_left-icon" :class="{ 'dashicons dashicons-saved': cookie_widget_position === 'top_left' }" style="position: absolute; top: 0; left: 0; z-index: 1;"></span>
										<img
										id="widget-position-top_left-id"
										:class="{ 'widget-position-top': cookie_widget_position === 'top_left' }"
										src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) .'admin/images/widget_top_left.svg';?>"
										alt="Top_left"
										>
									</div>

									<div @click="cookiewidgetPositionChange('top_right')" style="position: relative; cursor: pointer;">
										<span id="widget-position-top_right-icon" :class="{ 'dashicons dashicons-saved': cookie_widget_position === 'top_right' }" style="position: absolute; top: 0; left: 0; z-index: 1;"></span>
										<img
										id="widget-position-top_right-id"
										:class="{ 'widget-position-top': cookie_widget_position === 'top_right' }"
										src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) .'admin/images/widget_top_right.svg';?>"
										alt="Top_right"
										>
									</div>
								</div>
								<input type="hidden" name="gcc-gdpr-cookie-widget-position" v-model="cookie_widget_position">
								</c-col>
							</c-row>
							<c-row v-show="show_cookie_as === 'popup'">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Add Overlay', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8">
									<c-switch v-bind="labelIcon" v-model="cookie_accept_on" id="gdpr-cookie-add-overlay" variant="3d"  color="success" :checked="cookie_add_overlay" v-on:update:checked="onSwitchAddOverlay"></c-switch>
									<input type="hidden" name="gdpr-cookie-add-overlay" v-model="cookie_add_overlay">
								</c-col>
							</c-row>
							
							

							
							<!-- Template screens -->
							<?php do_action( 'gdpr_cookie_template' ); ?>
						</c-card-body>
					</c-card>
				</c-tab>
			<!-- CONFIGURATION SECTION START -->
			 
			<!-- CONTENT AND DESIGN SECTION START -->
				<c-tab title="<?php esc_attr_e( 'Content and Design', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#gdpr_design" id="gdpr-cookie-consent-design">
				<div class="gdpr-design-tab-layout" :class="{ 'panel-open': cookie_bar_settings_open }">
        			<div class="gdpr-design-left-column">

					<!-- Desgin Banner preview if A/B Testing is disabled and GDPR&CCPA both are not selected -->
					<c-card v-show="!ab_testing_enabled && gdpr_policy != 'both'" class="desgin_card">
						<c-card-body>

						<!-- law selectors for editing -->
						 <c-row>
							<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Banner Settings', 'gdpr-cookie-consent' ); ?></div></c-col>
						</c-row>
						<c-row v-show="is_auto_mode">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Select a privacy law', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'Select the privacy law whose banner message you want to customize.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<v-select class="form-group" id="gdpr-cookie-bar-edit-law" :reduce="label => label.code" :options="policy_options" :selectable="option => isOptionSelectable(option)" label="label" v-model="banner_edit_law">
									<template #option="option">
										<span class="gcc-flag">{{ option.flag }}</span> {{ option.label }}
										<span v-if="isProOnlyLaw(option.code) && disabled_for_free" class="probadge bg-badge"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 640" fill="#f5af2f"> <path d="M345 151.2C354.2 143.9 360 132.6 360 120C360 97.9 342.1 80 320 80C297.9 80 280 97.9 280 120C280 132.6 285.9 143.9 295 151.2L226.6 258.8C216.6 274.5 195.3 278.4 180.4 267.2L120.9 222.7C125.4 216.3 128 208.4 128 200C128 177.9 110.1 160 88 160C65.9 160 48 177.9 48 200C48 221.8 65.5 239.6 87.2 240L119.8 457.5C124.5 488.8 151.4 512 183.1 512L456.9 512C488.6 512 515.5 488.8 520.2 457.5L552.8 240C574.5 239.6 592 221.8 592 200C592 177.9 574.1 160 552 160C529.9 160 512 177.9 512 200C512 208.4 514.6 216.3 519.1 222.7L459.7 267.3C444.8 278.5 423.5 274.6 413.5 258.9L345 151.2z"/><path d="M180 550H460" fill="none" stroke="#f5af2f" stroke-width="28" stroke-linecap="round"/></svg></span>
									</template>
									<template #selected-option="option">
										<span class="gcc-flag">{{ option.flag }}</span> {{ option.label }}
									</template>
								</v-select>
								<input type="hidden" name="gdpr-cookie-banner-edit-law" v-model="banner_edit_law">
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_us_state_laws) || (is_auto_mode && banner_edit_law === 'us_state_laws')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Select a banner type', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'Choose which type of opt-out banner you want to customize.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<v-select class="form-group" id="gdpr-cookie-bar-edit-law" :reduce="label => label.code" :options="us_policy_options" v-model="us_state_laws_edit_law">
								</v-select>
								<input type="hidden" name="gdpr-cookie-us-state-laws-edit-law" v-model="us_state_laws_edit_law">
							</c-col>
						</c-row>
						
						<!-- Message Heading -->
						<c-row >
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Message Heading', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Leave it blank, If you do not need a heading.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="bar_heading_text_field" v-model="gdpr_message_heading"  @input="onHeadingInput"></c-textarea>
							</c-col>
						</c-row>

						<!-- ePrivacy -->
						<c-row v-show="(!is_auto_mode && is_eprivacy) || (is_auto_mode && banner_edit_law === 'eprivacy')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'ePrivacy Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as ePrivacy notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_eprivacy_field" v-model="eprivacy_message"></c-textarea>
							</c-col>
						</c-row>

						<!-- GDPR, UK-GDPR, PDPL PIPEDA, APP -->
						<c-row v-show="(!is_auto_mode && is_gdpr) || (is_auto_mode && banner_edit_law === 'gdpr')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'GDPR Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_field" v-model="gdpr_message" :readonly="iabtcf_is_on"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_uk_gdpr) || (is_auto_mode && banner_edit_law === 'uk_gdpr')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'UK GDPR Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_uk_gdpr_field" v-model="uk_gdpr_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_sa_pdpl) || (is_auto_mode && banner_edit_law === 'sa_pdpl')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Saudi Arabia PDPL Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_pdpl_field" v-model="pdpl_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_pipeda) || (is_auto_mode && banner_edit_law === 'pipeda')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'PIPEDA Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_pipeda_field" v-model="pipeda_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_au_app) || (is_auto_mode && banner_edit_law === 'au_app')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Australia(APP) Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_app_field" v-model="app_message"></c-textarea>
							</c-col>
						</c-row>

						<c-row v-show="(!is_auto_mode && (is_gdpr || is_au_app || is_pipeda || is_sa_pdpl || is_uk_gdpr)) || (is_auto_mode && (banner_edit_law === 'gdpr' || banner_edit_law === 'uk_gdpr' || banner_edit_law === 'sa_pdpl' || banner_edit_law === 'pipeda' || banner_edit_law === 'au_app'))">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'About Cookies Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Text shown under "About Cookies" section when users click on "Preferences" button.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea :rows="6" name="about_message_field" v-model="gdpr_about_cookie_message" :readonly="iabtcf_is_on"></c-textarea>
							</c-col>
						</c-row>

						<!-- US State Laws -->
						<c-row v-show="(!is_auto_mode && is_us_state_laws && us_state_laws_edit_law === 'ccpa') || (is_auto_mode && banner_edit_law === 'us_state_laws' && us_state_laws_edit_law === 'ccpa')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'CCPA Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as CCPA notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_ccpa_field" v-model="ccpa_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_us_state_laws && us_state_laws_edit_law === 'default_opt_out') || (is_auto_mode && banner_edit_law === 'us_state_laws' && us_state_laws_edit_law === 'default_opt_out')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Banner Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as banner notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_ccpa_field" v-model="default_opt_out_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_us_state_laws && us_state_laws_edit_law === 'pure_opt_out') || (is_auto_mode && banner_edit_law === 'us_state_laws' && us_state_laws_edit_law === 'pure_opt_out')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Banner Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as banner notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_ccpa_field" v-model="pure_opt_out_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_us_state_laws) || (is_auto_mode && banner_edit_law === 'us_state_laws')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'CCPA Opt-out Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as CCPA notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_ccpa_optout_field" v-model="ccpa_optout_message"></c-textarea>
							</c-col>
						</c-row>
						
						<!-- LGPD -->
						<c-row v-show="(!is_auto_mode && is_lgpd) || (is_auto_mode && banner_edit_law === 'lgpd')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'LGPD Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_lgpd_field" v-model="lgpd_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_lgpd) || (is_auto_mode && banner_edit_law === 'lgpd')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'About Cookies Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Text shown under "About Cookies" section when users click on "Preferences" button.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea :rows="6" name="about_message_lgpd_field" v-model="lgpd_about_cookie_message"></c-textarea>
							</c-col>
						</c-row>
						<div style="display: flex; justify-content: flex-end; margin-top: 10px;">
							<p class="design-settings-text"><?php esc_html_e("Design Settings", "gdpr-cookie-banner")?></p>
						<c-button class="gdpr-cookie-bar-settings-icon" :style="(cookie_bar_settings_open) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}"  @click="openConfigurationPanel('cookie_bar_settings_open')">
							<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g clip-path="url(#clip0_4634_794)">
								<path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/>
								</g>
								<defs>
								<clipPath id="clip0_4634_794">
								<rect width="30" height="30" fill="white" transform="translate(10 10)"/>
								</clipPath>
								</defs>
							</svg>
						</c-button>
						</div>
						</c-card-body>
					
					</c-card>
					
					<c-card v-show="!ab_testing_enabled && gdpr_policy != 'both'" class="desgin_card">
						<c-card-body>
						<!-- Privacy Policy Settings -->
						 <c-row>
							<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Button Settings', 'gdpr-cookie-consent' ); ?></div></c-col>
						</c-row>	
						<c-row v-show="is_auto_mode || show_revoke_card || is_lgpd">
							<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Privacy Policy Settings', 'gdpr-cookie-consent' ); ?></div></c-col>
						</c-row>
						<c-row class="privacy-policy-row" v-show="is_auto_mode || show_revoke_card || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Privacy Policy Link', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enable this to provide a link to your Privacy & Cookie Policy on your Cookie Notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-1">
								<c-switch v-bind="labelIcon" v-model="button_readmore_is_on" id="gdpr-cookie-consent-readmore-is-on" variant="3d"  color="success" :checked="button_readmore_is_on" v-on:update:checked="onSwitchButtonReadMoreIsOn"></c-switch>
								<input type="hidden" name="gcc-readmore-is-on" v-model="button_readmore_is_on">
							</c-col>
							<c-col class="col-sm-6">
								<c-input :disabled="!button_readmore_is_on" name="button_readmore_text_field" v-model="button_readmore_text" placeholder="<?php esc_attr_e( 'Enter link text', 'gdpr-cookie-consent' ); ?>"></c-input>
							</c-col>
							<c-col class="col-sm-1">
									<c-button :disabled="!button_readmore_is_on" class="gdpr-configure-button" :style="(button_readmore_popup) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('button_readmore_popup')">
											<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>
									</c-button>
								</c-col>
						</c-row>

						<!-- Revoke Consent settings -->
						<c-row v-show="is_auto_mode || show_revoke_card || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl">
							<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Revoke Consent', 'gdpr-cookie-consent' ); ?></div></c-col>
						</c-row>
						<c-row  class="privacy-policy-row" v-show="is_auto_mode || show_revoke_card || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Revoke Consent', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enable to give user the option to revoke their consent.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-1">
								<c-switch v-bind="labelIcon" v-model="is_revoke_consent_on" id="gdpr-cookie-consent-revoke-consent" variant="3d"  color="success" :checked="is_revoke_consent_on" v-on:update:checked="onSwitchRevokeConsentEnable"></c-switch>
								<input type="hidden" name="gcc-revoke-consent-enable" v-model="is_revoke_consent_on">
							</c-col>
							<c-col class="col-sm-6 gdpr-input-col">
								<c-input :disabled="!is_revoke_consent_on" name="show_again_text_field" v-model="tab_text"></c-input>
							</c-col>
							<c-col class="col-sm-1">
								<c-button :disabled="!is_revoke_consent_on" class="gdpr-configure-button" :style="(revoke_consent_popup) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('revoke_consent_popup')">
									<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>
								</c-button>
							</c-col>
						</c-row>


						<!-- Accept Button -->
						<c-row v-show="is_auto_mode || is_gdpr || is_eprivacy || is_lgpd || is_uk_gdpr || is_sa_pdpl">
							<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Accept Button', 'gdpr-cookie-consent' ); ?></div></c-col>
						</c-row>
						<c-row  class="privacy-policy-row" v-show="is_auto_mode || is_gdpr || is_eprivacy || is_lgpd || is_uk_gdpr || is_sa_pdpl">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
							<c-col class="col-sm-1">
								<c-switch v-bind="labelIcon" v-model="cookie_accept_on" id="gdpr-cookie-consent-cookie" variant="3d"  color="success" :checked="cookie_accept_on" v-on:update:checked="onSwitchCookieAcceptEnable"></c-switch>
								<input type="hidden" name="gcc-cookie-accept-enable" v-model="cookie_accept_on">
							</c-col>
							<c-col class="col-sm-6">
								<label
									for="button_accept_text_field"
									class="screen-reader-text"
								>
									<?php esc_attr_e( 'button accept text field', 'gdpr-cookie-consent' ); ?>
								</label>

								<c-input
									:disabled="!cookie_accept_on"
									id="button_accept_text_field"
									name="button_accept_text_field"
									v-model="accept_text"
								></c-input>
							</c-col>
							<c-col class="col-sm-1" v-show="is_auto_mode || is_gdpr || is_eprivacy || is_lgpd || is_uk_gdpr || is_sa_pdpl">
								<c-button :disabled="!cookie_accept_on" class="gdpr-configure-button" :style="(accept_button_popup) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}"  @click="openConfigurationPanel('accept_button_popup')">
									<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>
								</c-button>
							</c-col>
						</c-row>
							<!-- Accept All Button -->
							<c-row  v-show="is_auto_mode || is_gdpr || is_eprivacy || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl">
								<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Accept All Button', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<c-row class="privacy-policy-row" v-show="is_auto_mode || is_gdpr || is_eprivacy || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-1">
									<c-switch v-bind="labelIcon" v-model="cookie_accept_all_on" id="gdpr-cookie-consent-cookie-acceptall-on" variant="3d"  color="success" :checked="cookie_accept_all_on" v-on:update:checked="onSwitchCookieAcceptAllEnable"></c-switch>
									<input type="hidden" name="gcc-cookie-accept-all-enable" v-model="cookie_accept_all_on">
								</c-col>
								<c-col class="col-sm-6">
									<label
										for="button_accept_all_text_field"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'button accept all text field', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										:disabled =!cookie_accept_all_on
										id="button_accept_all_text_field"
										name="button_accept_all_text_field"
										v-model="accept_all_text"
									></c-input>
								</c-col>
								<c-col class="col-sm-1"  v-show="is_auto_mode || is_gdpr || is_eprivacy || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl">
									<c-button :disabled="!cookie_accept_all_on" class="gdpr-configure-button" :style="(accept_all_button_popup) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}"  @click="openConfigurationPanel('accept_all_button_popup')">
										<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>
									</c-button>
								</c-col>
							</c-row>
							<!-- Decline Button -->
							<c-row v-show="is_auto_mode || is_gdpr || is_eprivacy || is_lgpd || is_uk_gdpr || is_sa_pdpl">
								<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Reject All Button', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<c-row class="privacy-policy-row" v-show="is_auto_mode || is_gdpr || is_eprivacy || is_lgpd || is_uk_gdpr || is_sa_pdpl"> 
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-1">
									<c-switch v-bind="labelIcon" v-model="cookie_decline_on" id="gdpr-cookie-consent-decline-on" variant="3d"  color="success" :checked="cookie_decline_on" v-on:update:checked="onSwitchCookieDeclineEnable"></c-switch>
									<input type="hidden" name="gcc-cookie-decline-enable" v-model="cookie_decline_on">
								</c-col>
								<c-col class="col-sm-6">
									<label
										for="button_decline_text_field"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'button decline text field', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										:disabled =!cookie_decline_on
										id="button_decline_text_field"
										name="button_decline_text_field"
										v-model="decline_text"
									></c-input>
								</c-col>
								<c-col class="col-sm-1" v-show="is_auto_mode || is_gdpr || is_eprivacy || is_lgpd || is_uk_gdpr || is_sa_pdpl">
									<c-button :disabled="!cookie_decline_on" class="gdpr-configure-button" :style="(decline_button_popup) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('decline_button_popup')">
										<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>
									</c-button>
								</c-col>
							</c-row>
							<!-- Settings Button -->
							<c-row v-show="is_auto_mode || is_gdpr || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl">
								<c-col  class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Preferences Button', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<c-row class="privacy-policy-row" v-show="is_auto_mode || is_gdpr || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-1">
									<c-switch v-bind="labelIcon" v-model="cookie_settings_on" id="gdpr-cookie-consent-settings-on" variant="3d"  color="success" :checked="cookie_settings_on" v-on:update:checked="onSwitchCookieSettingsEnable"></c-switch>
									<input type="hidden" name="gcc-cookie-settings-enable" v-model="cookie_settings_on">
								</c-col>
								<c-col class="col-sm-6">
									<label for="button_settings_text_field" class="screen-reader-text">
										<?php esc_attr_e( 'button settings text field', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										:disabled =!cookie_settings_on
										id="button_settings_text_field"
										name="button_settings_text_field"
										v-model="settings_text"
									></c-input>
								</c-col>
								<c-col class="col-sm-1" v-show="is_auto_mode || is_gdpr || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl">
									<c-button :disabled="!cookie_settings_on" class="gdpr-configure-button" :style="(settings_button_popup) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('settings_button_popup')">
										<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>
									</c-button>
								</c-col>
									<c-col class="col-sm-4">
										<label><?php esc_attr_e( 'Display Cookies List on Frontend', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-4">
										<c-switch v-bind="labelIcon" v-model="cookie_on_frontend" id="gdpr-cookie-consent-cookie-frontend" variant="3d"  color="success" :checked="cookie_on_frontend" v-on:update:checked="onSwitchCookieOnFrontend" :disabled="!cookie_settings_on"></c-switch>
										<input type="hidden" name="gcc-cookie-on-frontend" v-model="cookie_on_frontend">
									</c-col>
									<c-col class="col-sm-4">
										<?php do_action( 'gdpr_cookie_layout_skin_label' ); ?>
									</c-col>
									<c-col class="col-sm-4">
										<?php do_action( 'gdpr_cookie_layout_skin_markup' ); ?>
									</c-col>
							</c-row>
							<!-- Confirm button -->
							<c-row v-show="is_auto_mode || is_us_state_laws">
								<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Save Preferences Button', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<c-row class="privacy-policy-row" v-show="is_auto_mode || is_us_state_laws">
								<c-col class="col-sm-5"><label><?php esc_attr_e( 'Save Preferences Button Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-6">
									<label
										for="button_confirm_text_field"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'button confirm text field', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="button_confirm_text_field"
										name="button_confirm_text_field"
										v-model="confirm_text"
									></c-input>
								</c-col>
								<c-col class="col-sm-1" v-show="is_auto_mode || is_us_state_laws">
									<c-button class="gdpr-configure-button" :style="(confirm_button_popup) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('confirm_button_popup')"><svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>
									</c-button>
								</c-col>
							</c-row>
							<!-- Cancle button -->
							<c-row  v-show="is_auto_mode || is_us_state_laws">
								<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Cancel Button', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<c-row class="privacy-policy-row" v-show="is_auto_mode || is_us_state_laws">
								<c-col class="col-sm-5"><label><?php esc_attr_e( 'Cancel Button Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-6">
									<label for="button_cancel_text_field" class="screen-reader-text">
										<?php esc_attr_e( 'button cancel text field', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="button_cancel_text_field"
										name="button_cancel_text_field"
										v-model="cancel_text"
									></c-input>
								</c-col>
								<c-col class="col-sm-1" v-show="is_auto_mode || is_us_state_laws">
									<c-button class="gdpr-configure-button" :style="(cancel_button_popup) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('cancel_button_popup')">
										<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

									</c-button>
								</c-col>
							</c-row>
							<!-- Opt-out button -->
							<c-row v-show="is_auto_mode || is_us_state_laws">
								<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Opt-out Button', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<c-row class="privacy-policy-row" v-show="is_auto_mode || is_us_state_laws">
								<c-col class="col-sm-5"><label><?php esc_attr_e( 'Opt-out Link Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-6">
									<label
										for="button_donotsell_text_field"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'button donotsell text field', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="button_donotsell_text_field"
										name="button_donotsell_text_field"
										v-model="opt_out_text"
									></c-input>
								</c-col>
								<c-col class="col-sm-1" v-show="is_auto_mode || is_us_state_laws">
									<c-button class="gdpr-configure-button"  :style="(opt_out_link_popup) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('opt_out_link_popup')">
										<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

									</c-button>
								</c-col>
							</c-row>
							<!-- Revoke Consent settings for CCPA -->
							 <c-row v-show="is_auto_mode || is_us_state_laws" class="gdpr-label-row">
								<c-col v-show="is_auto_mode || is_us_state_laws" class="col-sm-32">
									<div id="gdpr-cookie-consent-settings-cookie-notice">
										<?php esc_html_e( 'Revoke Consent(US State Laws)', 'gdpr-cookie-consent' ); ?>
									</div>
								</c-col>
							</c-row>
							<c-row class="privacy-policy-row" v-show="is_auto_mode || is_us_state_laws">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Enable Revoke Consent(US State Laws)', 'gdpr-cookie-consent' ); ?>
										<tooltip text="<?php esc_html_e( 'Enable to give user the option to revoke their consent.', 'gdpr-cookie-consent' ); ?>"></tooltip>
									</label>
								</c-col>
								<c-col class="col-sm-1">
									<c-switch 
										v-bind="labelIcon" 
										v-model="is_ccpa_revoke_consent_on" 
										id="gdpr-cookie-consent-ccpa-revoke-consent" 
										variant="3d" 
										color="success" 
										:checked="is_ccpa_revoke_consent_on" 
										v-on:update:checked="onSwitchCcpaRevokeConsentEnable">
									</c-switch>
									<input type="hidden" name="gcc-ccpa-revoke-consent-enable" v-model="is_ccpa_revoke_consent_on">
								</c-col>

								<c-col class="col-sm-6">
									<c-input
										name="ccpa_show_again_text_field"
										v-model="ccpa_tab_text"
									></c-input>
								</c-col>
								<c-col class="col-sm-1">
									<c-button :disabled="!is_ccpa_revoke_consent_on" class="gdpr-configure-button" :style="(ccpa_revoke_consent_popup) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('ccpa_revoke_consent_popup')">
										<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

									</c-button>
								</c-col>
							</c-row>

							</c-card-body>
						</c-card>
					</div>
					<div class="gdpr-design-right-column">
					<c-card  v-if="cookie_bar_settings_open" v-show="!ab_testing_enabled && gdpr_policy != 'both'" class="desgin_card">
						<c-card-body>
							

							<c-row>
								<c-col class="col-sm-8"><div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Cookie Bar Body Design', 'gdpr-cookie-consent' ); ?></div></c-col>
								<c-col class="col-sm-4"><button type="button" class="cookie-bar-settings-close" @click="cookie_bar_settings_open = false" aria-label="<?php esc_attr_e( 'Close Cookie Bar Settings', 'gdpr-cookie-consent' ); ?>">
								<svg class="connect-info-close" data-target="connect-info-container" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18" stroke="#3A3A41" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M6 6L18 18" stroke="#3A3A41" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
							</button></c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cookie Bar Color', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8 gdpr-color-pick" >
								<c-input class="gdpr-color-input" type="text" v-model="cookie_bar_color"  aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
								<c-input class="gdpr-color-select" id="gdpr-cookie-bar-color" type="color" name="gdpr-cookie-bar-color" v-model="cookie_bar_color"   aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
								</c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( ' Cookie Bar Opacity', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8 gdpr-color-pick">
								<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="cookie_bar_opacity" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
								<label for="gdpr-cookie-bar-opacity" class="screen-reader-text"><?php esc_attr_e('gdpr cookie bar opacity', 'gdpr-cookie-consent'); ?></label>
								<c-input id="gdpr-cookie-bar-opacity" class="gdpr-slider-input opacity-slider" type="number"  min="0" max="1" step="0.01" name="gdpr-cookie-bar-opacity" v-model="cookie_bar_opacity"></c-input>
								</c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8 gdpr-color-pick" >
								<c-input class="gdpr-color-input" type="text" v-model="cookie_text_color"   aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
								<label for="gdpr-cookie-text-color" class="screen-reader-text"><?php esc_attr_e('Email address', 'gdpr-cookie-consent'); ?></label>
								<c-input class="gdpr-color-select" id="gdpr-cookie-text-color" type="color" name="gdpr-cookie-text-color" v-model="cookie_text_color"></c-input>
								</c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Styles', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8">
									<v-select class="form-group" id="gdpr-cookie-border-style" :reduce="label => label.code" :options="border_style_options" v-model="border_style">
									</v-select>
									<input type="hidden" name="gdpr-cookie-border-style" v-model="border_style">
								</c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8 gdpr-color-pick">
								<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="cookie_bar_border_width" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
								<label for="gdpr-cookie-bar-border-width" class="screen-reader-text"><?php esc_attr_e('gdpr cookie bar border width', 'gdpr-cookie-consent'); ?></label>
								<c-input id="gdpr-cookie-bar-border-width" class="gdpr-slider-input"type="number" name="gdpr-cookie-bar-border-width" v-model="cookie_bar_border_width"></c-input>
								</c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8 gdpr-color-pick">
								<c-input class="gdpr-color-input" type="text" v-model="cookie_border_color"></c-input>
								<c-input class="gdpr-color-select" id="gdpr-cookie-border-color" type="color" name="gdpr-cookie-border-color" v-model="cookie_border_color"  aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
								</c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8 gdpr-color-pick">
								<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="cookie_bar_border_radius" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
								<label for="gdpr-cookie-bar-border-radius" class="screen-reader-text"><?php esc_attr_e('gdpr cookie bar border radius', 'gdpr-cookie-consent'); ?></label>
								<c-input id="gdpr-cookie-bar-border-radius" class="gdpr-slider-input"type="number" name="gdpr-cookie-bar-border-radius" v-model="cookie_bar_border_radius"></c-input>
								</c-col>
							</c-row>
							<?php
							$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ? GDPR_COOKIE_CONSENT_VERSION : '';
							if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
								if ( ! $is_pro_active ) :
									?>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Font', 'gdpr-cookie-consent' ); ?></label>
											<div class="gdpr-pro-label"><div class="gdpr-pro-label-text">Pro</div></div>
										</c-col>
										<c-col class="col-sm-8">
											<v-select disabled class="form-group" id="gdpr-cookie-font" :reduce="label => label.code" :options="font_options" v-model="cookie_font">
											</v-select>
											<input type="hidden" name="gdpr-cookie-font" v-model="cookie_font">
										</c-col>
									</c-row>
								<?php endif ?>
								<?php
								do_action( 'gdpr_cookie_font' );
							} else {
								?>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Font', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<v-select class="form-group" id="gdpr-cookie-font" :reduce="label => label.code" :options="font_options" v-model="cookie_font">
											</v-select>
											<input type="hidden" name="gdpr-cookie-font" v-model="cookie_font">
										</c-col>
									</c-row>
								<?php } ?>
							<?php

							// Add Logo Image 
								?>
								<c-row>
									<c-col class="col-sm-4">
										<label><?php esc_attr_e( 'Upload Logo ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'To preview the logo, simply upload a logo and then click the "Save Changes" button ', 'gdpr-cookie-consent' ); ?>"></tooltip><a href="https://wplegalpages.com/pricing?utm_source=wp_cookie_consent&utm_medium=upload_logo&utm_campaign=plugin_upgrade" style="margin-left:5px;" class="probadge bg-badge"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 640" fill="#f5af2f"> <path d="M345 151.2C354.2 143.9 360 132.6 360 120C360 97.9 342.1 80 320 80C297.9 80 280 97.9 280 120C280 132.6 285.9 143.9 295 151.2L226.6 258.8C216.6 274.5 195.3 278.4 180.4 267.2L120.9 222.7C125.4 216.3 128 208.4 128 200C128 177.9 110.1 160 88 160C65.9 160 48 177.9 48 200C48 221.8 65.5 239.6 87.2 240L119.8 457.5C124.5 488.8 151.4 512 183.1 512L456.9 512C488.6 512 515.5 488.8 520.2 457.5L552.8 240C574.5 239.6 592 221.8 592 200C592 177.9 574.1 160 552 160C529.9 160 512 177.9 512 200C512 208.4 514.6 216.3 519.1 222.7L459.7 267.3C444.8 278.5 423.5 274.6 413.5 258.9L345 151.2z"/><path d="M180 550H460" fill="none" stroke="#f5af2f" stroke-width="28" stroke-linecap="round"/></svg></a></label>
									</c-col>
									<c-col class="col-sm-8 ">
										<c-button color="info" class="button" id="image-upload-button" name="image-upload-button" @click="openMediaModal" style="margin: 10px;" <?php echo $is_disabled ? 'disabled' : ''; ?>>
											<?php esc_attr_e( 'Add Image', 'gdpr-cookie-consent' ); ?>
										</c-button>
										<c-button color="info" class="button" id="image-delete-button" @click="deleteSelectedimage" style="margin: 10px; ">
											<?php esc_attr_e( 'Remove Image', 'gdpr-cookie-consent' ); ?>
										</c-button>
										<?php
										$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
										?>
										<img id="gdpr-cookie-bar-logo-holder" name="gdpr-cookie-bar-logo-holder" src="<?php echo esc_url_raw( $get_banner_img ); ?>" alt="">
										<p class="image-upload-notice" style="margin-left: 10px; font-size:14px; font-weight:14px;color:#d4d4d8;">
											<?php esc_attr_e( 'We recommend 50 x 50 pixels.', 'gdpr-cookie-consent' ); ?>
										</p>
										<c-input type="hidden" name="gdpr-cookie-bar-logo-url-holder" id="gdpr-cookie-bar-logo-url-holder"  class="regular-text"> </c-input>
									</c-col>
								</c-row>
						</c-card-body>
					</c-card>
					<div
					v-show="button_readmore_popup"
					class="gdpr-privacy-policy-settings-panel">
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle"><?php esc_attr_e( 'Privacy Policy Settings', 'gdpr-cookie-consent' ); ?></div>
							<img @click="button_readmore_popup=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
						</div>

						<div class="optout-settings-main-container" >
							<div v-show="(is_auto_mode || show_revoke_card || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl) && button_readmore_is_on">
								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="button_readmore_link_color"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-readmore-link-color" type="color" name="gcc-readmore-link-color" v-model="button_readmore_link_color"></c-input>
									</c-col>
								</c-row>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label><?php esc_attr_e( 'Show as', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-8">
										<v-select
											class="form-group"
											id="gcc-readmore-as-button"
											:reduce="label => label.code"
											:options="show_as_options"
											v-model="button_readmore_as_button"
										></v-select>
										<input type="hidden" name="gcc-readmore-as-button" v-model="button_readmore_as_button">
									</c-col>
								</c-row>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label><?php esc_attr_e( 'Page or Custom URL', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-8">
										<v-select
											class="form-group"
											id="gcc-readmore-url-type"
											:reduce="label => label.code"
											:options="url_type_options"
											v-model="button_readmore_url_type"
										></v-select>
										<input type="hidden" name="gcc-readmore-url-type" v-model="button_readmore_url_type">
									</c-col>
								</c-row>

								<div v-show="button_readmore_as_button">

									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="button_readmore_button_color"></c-input>
											<c-input class="gdpr-color-select" id="gdpr-readmore-button-color" type="color" name="gcc-readmore-button-color" v-model="button_readmore_button_color"></c-input>
										</c-col>
									</c-row>

									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-8">
											<v-select
												class="form-group"
												id="gcc-readmore-button-size"
												:reduce="label => label.code"
												:options="button_size_options"
												v-model="button_readmore_button_size"
											></v-select>
											<input type="hidden" name="gcc-readmore-button-size" v-model="button_readmore_button_size">
										</c-col>
									</c-row>

									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-8">
											<v-select
												class="form-group"
												id="gcc-readmore-button-border-style"
												:reduce="label => label.code"
												:options="border_style_options"
												v-model="button_readmore_button_border_style"
											></v-select>
											<input type="hidden" name="gcc-readmore-button-border-style" v-model="button_readmore_button_border_style">
										</c-col>
									</c-row>

									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="button_readmore_button_border_color"></c-input>
											<c-input class="gdpr-color-select" id="gdpr-readmore-button-border-color" type="color" name="gcc-readmore-button-border-color" v-model="button_readmore_button_border_color"></c-input>
										</c-col>
									</c-row>

								</div>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label v-show="button_readmore_url_type"><?php esc_attr_e( 'Page', 'gdpr-cookie-consent' ); ?></label>
										<label v-show="!button_readmore_url_type"><?php esc_attr_e( 'URL', 'gdpr-cookie-consent' ); ?></label>
									</c-col>

									<c-col class="col-sm-8">
										<div v-show="button_readmore_url_type">
											<v-select
												class="form-group"
												placeholder="Select Policy Page"
												id="gcc-readmore-page"
												:reduce="label => label.code"
												:options="privacy_policy_options"
												v-model="readmore_page"
												@input="onSelectPrivacyPage"
											></v-select>
											<input type="hidden" name="gcc-readmore-page" v-model="button_readmore_page">
										</div>

										<c-input
											v-show="!button_readmore_url_type"
											name="gcc-readmore-url"
											v-model="button_readmore_url"
										></c-input>
									</c-col>
								</c-row>

								<c-row v-show="button_readmore_url_type" class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Sync with WordPress Policy Page', 'gdpr-cookie-consent' ); ?>
											<tooltip text="<?php esc_html_e( 'If enabled visitor will be redirected to Privacy Policy Page set in WordPress settings irrespective of Page set in the previous setting.', 'gdpr-cookie-consent' ); ?>"></tooltip>
										</label>
									</c-col>
									<c-col class="col-sm-8 gdpr-readmore-toggle-row">
										<c-switch
											v-bind="labelIcon"
											v-model="button_readmore_wp_page"
											id="gdpr-cookie-consent-readmore-wp-page"
											variant="3d"
											color="success"
											:checked="button_readmore_wp_page"
											v-on:update:checked="onSwitchButtonReadMoreWpPage"
										></c-switch>
										<input type="hidden" name="gcc-readmore-wp-page" v-model="button_readmore_wp_page">
									</c-col>
								</c-row>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label><?php esc_attr_e( 'Open URL in New Window?', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-8 gdpr-readmore-toggle-row">
										<c-switch
											v-bind="labelIcon"
											v-model="button_readmore_new_win"
											id="gdpr-cookie-consent-readmore-new-win"
											variant="3d"
											color="success"
											:checked="button_readmore_new_win"
											v-on:update:checked="onSwitchButtonReadMoreNewWin"
										></c-switch>
										<input type="hidden" name="gcc-readmore-new-win" v-model="button_readmore_new_win">
									</c-col>
								</c-row>

								<div v-show="button_readmore_as_button">

									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input
												class="gdpr-slider-select"
												type="range"
												min="0"
												max="1"
												step="0.01"
												v-model="button_readmore_button_opacity"
											></c-input>
											<c-input
												class="gdpr-slider-input"
												type="number"
												name="gcc-readmore-button-opacity"
												v-model="button_readmore_button_opacity"
											></c-input>
										</c-col>
									</c-row>

									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input
												class="gdpr-slider-select"
												type="range"
												min="0"
												max="10"
												step="0.5"
												v-model="button_readmore_button_border_width"
											></c-input>
											<c-input
												class="gdpr-slider-input"
												type="number"
												name="gcc-readmore-button-border-width"
												v-model="button_readmore_button_border_width"
											></c-input>
										</c-col>
									</c-row>

									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input
												class="gdpr-slider-select"
												type="range"
												min="0"
												max="100"
												step="0.5"
												v-model="button_readmore_button_border_radius"
											></c-input>
											<c-input
												class="gdpr-slider-input"
												type="number"
												name="gcc-readmore-button-border-radius"
												v-model="button_readmore_button_border_radius"
											></c-input>
										</c-col>
									</c-row>

								</div>

							</div>

						</div>
					</div>		
					<div
						v-show="revoke_consent_popup"
						class="gdpr-revoke-consent-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Revoke Consent Settings', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="revoke_consent_popup=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">

							<div
								v-show="(is_auto_mode || show_revoke_card || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl) && is_revoke_consent_on"
							>
								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8 gdpr-color-pick">
										<c-input
											class="gdpr-color-input"
											type="text"
											v-model="button_revoke_consent_text_color"
										></c-input>

										<c-input
											class="gdpr-color-select"
											id="gdpr-readmore-link-color"
											type="color"
											name="gcc-revoke-consent-text-color"
											v-model="button_revoke_consent_text_color"
										></c-input>
									</c-col>
								</c-row>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8 gdpr-color-pick">
										<c-input
											class="gdpr-color-input"
											type="text"
											v-model="button_revoke_consent_background_color"
										></c-input>

										<c-input
											class="gdpr-color-select"
											id="gdpr-readmore-button-color"
											type="color"
											name="gcc-revoke-consent-background-color"
											v-model="button_revoke_consent_background_color"
										></c-input>
									</c-col>
								</c-row>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Tab Position', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8">
										<v-select
											class="form-group"
											id="gdpr-cookie-consent-tab-position"
											:reduce="label => label.code"
											:options="tab_position_options"
											v-model="tab_position"
										></v-select>

										<input
											type="hidden"
											name="gcc-tab-position"
											v-model="tab_position"
										>
									</c-col>
								</c-row>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Tab margin (in percent)', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8">
										<c-input
											type="number"
											min="0"
											max="100"
											name="gcc-tab-margin"
											v-model="tab_margin"
										></c-input>
									</c-col>
								</c-row>
							</div>

						</div>
					</div>
					<div
						v-show="accept_button_popup"
						class="gdpr-accept-button-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Accept Button', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="accept_button_popup=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_text_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-accept-text-color"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie accept text color', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-accept-text-color"
										type="color"
										name="gdpr-cookie-accept-text-color"
										v-model="accept_text_color"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-as-button"
										:reduce="label => label.code"
										:options="accept_as_button_options"
										v-model="accept_as_button"
										@input="onButtonChange($event, 'accept')"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-as"
										v-model="accept_as_button"
									>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Action ', 'gdpr-cookie-consent' ); ?>

										<tooltip
											text="<?php esc_html_e( 'Select action to do once the user clicks on button.', 'gdpr-cookie-consent' ); ?>"
										></tooltip>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-action"
										:reduce="label => label.code"
										:options="accept_action_options"
										v-model="accept_action"
										@input="cookieAcceptChange"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-action"
										v-model="accept_action"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="is_open_url"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8">
									<label
										for="gdpr-cookie-accept-url"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie accept url', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-accept-url"
										name="gdpr-cookie-accept-url"
										v-model="accept_url"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="is_open_url"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-url-new-window"
										:reduce="label => label.code"
										:options="open_url_options"
										v-model="open_url"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-url-new-window"
										v-model="open_url"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_background_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-accept-background-color"
										type="color"
										name="gdpr-cookie-accept-background-color"
										v-model="accept_background_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-border-style"
										:reduce="label => label.code"
										:options="border_style_options"
										v-model="accept_style"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-border-style"
										v-model="accept_style"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_border_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										type="color"
										name="gdpr-cookie-accept-border-color"
										v-model="accept_border_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="1"
										step="0.01"
										v-model="accept_opacity"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-accept-opacity"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie accept opacity', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-accept-opacity"
										class="gdpr-slider-input opacity-slider"
										type="number"
										min="0"
										max="1"
										step="0.1"
										name="gdpr-cookie-accept-opacity"
										v-model="accept_opacity"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="10"
										step="0.5"
										v-model="accept_border_width"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-accept-border-width"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie accept border width', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-accept-border-width"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-accept-border-width"
										v-model="accept_border_width"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="100"
										step="0.5"
										v-model="accept_border_radius"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-accept-border-radius"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie accept border radius', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-accept-border-radius"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-accept-border-radius"
										v-model="accept_border_radius"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="accept_all_button_popup"
						class="gdpr-accept-all-button-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<template v-if="is_auto_mode || is_gdpr || is_eprivacy || is_lgpd || is_uk_gdpr || is_sa_pdpl">
									<?php esc_attr_e( 'Accept All Button', 'gdpr-cookie-consent' ); ?>
								</template>

								<template v-else-if="is_pipeda">
									<?php esc_attr_e( 'Accept Button', 'gdpr-cookie-consent' ); ?>
								</template>

								<template v-else>
									<?php esc_attr_e( 'Got it Button', 'gdpr-cookie-consent' ); ?>
								</template>
							</div>

							<img
								@click="accept_all_button_popup=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_all_text_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-accept-all-text-color"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie accept all text color', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-accept-all-text-color"
										type="color"
										name="gdpr-cookie-accept-all-text-color"
										v-model="accept_all_text_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-all-as-button"
										:reduce="label => label.code"
										:options="accept_as_button_options"
										v-model="accept_all_as_button"
										@input="onButtonChange($event, 'accept_all')"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-all-as"
										v-model="accept_all_as_button"
									>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Action ', 'gdpr-cookie-consent' ); ?>

										<tooltip
											text="<?php esc_html_e( 'Select action to do once the user clicks on button.', 'gdpr-cookie-consent' ); ?>"
										></tooltip>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-all-action"
										:reduce="label => label.code"
										:options="accept_action_options"
										v-model="accept_all_action"
										@input="cookieAcceptAllChange"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-all-action"
										v-model="accept_all_action"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_open_url"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<label
										for="gdpr-cookie-accept-all-url"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie accept all url', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-accept-all-url"
										name="gdpr-cookie-accept-all-url"
										v-model="accept_all_url"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_open_url"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-all-new-window"
										:reduce="label => label.code"
										:options="open_url_options"
										v-model="accept_all_new_win"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-all-new-window"
										v-model="accept_all_new_win"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_all_background_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-accept-all-background-color"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie accept all background color', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-accept-all-background-color"
										type="color"
										name="gdpr-cookie-accept-all-background-color"
										v-model="accept_all_background_color"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-all-border-style"
										:reduce="label => label.code"
										:options="border_style_options"
										v-model="accept_all_style"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-all-border-style"
										v-model="accept_all_style"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_all_border_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-accept-all-border-color"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie accept all border color', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-accept-all-border-color"
										type="color"
										name="gdpr-cookie-accept-all-border-color"
										v-model="accept_all_border_color"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="1"
										step="0.01"
										v-model="accept_all_opacity"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-accept-all-opacity"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'Email', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-accept-all-opacity"
										class="gdpr-slider-input opacity-slider"
										type="number"
										min="0"
										max="1"
										step="0.1"
										name="gdpr-cookie-accept-all-opacity"
										v-model="accept_all_opacity"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="10"
										step="0.5"
										v-model="accept_all_border_width"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-accept-all-border-width"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie accept all border width', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-accept-all-border-width"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-accept-all-border-width"
										v-model="accept_all_border_width"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="100"
										step="0.5"
										v-model="accept_all_border_radius"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-accept-all-border-radius"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie accept all border radius', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-accept-all-border-radius"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-accept-all-border-radius"
										v-model="accept_all_border_radius"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="decline_button_popup"
						class="gdpr-decline-button-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<template>
									<?php esc_attr_e( 'Reject All Button', 'gdpr-cookie-consent' ); ?>
								</template>
							</div>

							<img
								@click="decline_button_popup=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="decline_text_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-decline-text-color"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie decline text color', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-decline-text-color"
										type="color"
										name="gdpr-cookie-decline-text-color"
										v-model="decline_text_color"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-decline-as-button"
										:reduce="label => label.code"
										:options="accept_as_button_options"
										v-model="decline_as_button"
										@input="onButtonChange($event, 'decline')"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-decline-as"
										v-model="decline_as_button"
									>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Action ', 'gdpr-cookie-consent' ); ?>

										<tooltip
											text="<?php esc_html_e( 'Select action to do once the user clicks on the button', 'gdpr-cookie-consent' ); ?>"
										></tooltip>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-decline-action"
										:reduce="label => label.code"
										:options="decline_action_options"
										v-model="decline_action"
										@input="cookieDeclineChange"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-decline-action"
										v-model="decline_action"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_open_url"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8">
									<label
										for="gdpr-cookie-decline-url"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie decline url', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-decline-url"
										name="gdpr-cookie-decline-url"
										v-model="decline_url"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_open_url"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-decline-url-new-window"
										:reduce="label => label.code"
										:options="open_url_options"
										v-model="open_decline_url"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-decline-url-new-window"
										v-model="open_decline_url"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="decline_background_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-decline-background-color"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie decline background color', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-decline-background-color"
										type="color"
										name="gdpr-cookie-decline-background-color"
										v-model="decline_background_color"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-decline-border-style"
										:reduce="label => label.code"
										:options="border_style_options"
										v-model="decline_style"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-decline-border-style"
										v-model="decline_style"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="decline_border_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-decline-border-color"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie decline border color', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-decline-border-color"
										type="color"
										name="gdpr-cookie-decline-border-color"
										v-model="decline_border_color"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="1"
										step="0.01"
										v-model="decline_opacity"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-decline-opacity"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie decline opacity', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-decline-opacity"
										class="gdpr-slider-input opacity-slider"
										type="number"
										min="0"
										max="1"
										step="0.1"
										name="gdpr-cookie-decline-opacity"
										v-model="decline_opacity"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="10"
										step="0.5"
										v-model="decline_border_width"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-decline-border-width"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie decline border width', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-decline-border-width"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-decline-border-width"
										v-model="decline_border_width"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="100"
										step="0.5"
										v-model="decline_border_radius"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-decline-border-radius"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie decline border radius', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-decline-border-radius"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-decline-border-radius"
										v-model="decline_border_radius"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="settings_button_popup"
						class="gdpr-settings-button-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<template>
									<?php esc_attr_e( 'Preferences Button', 'gdpr-cookie-consent' ); ?>
								</template>
							</div>

							<img
								@click="settings_button_popup=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="settings_text_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label for="gdpr-cookie-settings-text-color" class="screen-reader-text">
										<?php esc_attr_e( 'gdpr cookie settings text color', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-settings-text-color"
										type="color"
										name="gdpr-cookie-settings-text-color"
										v-model="settings_text_color"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-settings-as-button"
										:reduce="label => label.code"
										:options="accept_as_button_options"
										v-model="settings_as_button"
										@input="onButtonChange($event, 'settings')"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-settings-as"
										v-model="settings_as_button"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="settings_background_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label for="gdpr-cookie-settings-background-color" class="screen-reader-text">
										<?php esc_attr_e( 'gdpr cookie settings background color', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-settings-background-color"
										type="color"
										name="gdpr-cookie-settings-background-color"
										v-model="settings_background_color"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-settings-border-style"
										:reduce="label => label.code"
										:options="border_style_options"
										v-model="settings_style"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-settings-border-style"
										v-model="settings_style"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="settings_border_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label for="gdpr-cookie-settings-border-color" class="screen-reader-text">
										<?php esc_attr_e( 'gdpr cookie settings border color', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-settings-border-color"
										type="color"
										name="gdpr-cookie-settings-border-color"
										v-model="settings_border_color"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="1"
										step="0.01"
										v-model="settings_opacity"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label for="gdpr-cookie-settings-opacity" class="screen-reader-text">
										<?php esc_attr_e( 'gdpr cookie settings opacity', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-settings-opacity"
										class="gdpr-slider-input opacity-slider"
										type="number"
										min="0"
										max="1"
										step="0.1"
										name="gdpr-cookie-settings-opacity"
										v-model="settings_opacity"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="10"
										step="0.5"
										v-model="settings_border_width"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label for="gdpr-cookie-settings-border-width" class="screen-reader-text">
										<?php esc_attr_e( 'gdpr cookie settings border width', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-settings-border-width"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-settings-border-width"
										v-model="settings_border_width"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="100"
										step="0.5"
										v-model="settings_border_radius"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label for="gdpr-cookie-settings-border-radius" class="screen-reader-text">
										<?php esc_attr_e( 'gdpr cookie settings border radius', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-settings-border-radius"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-settings-border-radius"
										v-model="settings_border_radius"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="confirm_button_popup"
						class="gdpr-confirm-button-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Save Preferences Button', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="confirm_button_popup=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="confirm_text_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-confirm-text-color"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie confirm text color', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-confirm-text-color"
										type="color"
										name="gdpr-cookie-confirm-text-color"
										v-model="confirm_text_color"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="confirm_background_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-confirm-background-color"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie confirm background color', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-confirm-background-color"
										type="color"
										name="gdpr-cookie-confirm-background-color"
										v-model="confirm_background_color"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-confirm-border-style"
										:reduce="label => label.code"
										:options="border_style_options"
										v-model="confirm_style"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-confirm-border-style"
										v-model="confirm_style"
									>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="confirm_border_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-confirm-border-color"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie confirm border color', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-confirm-border-color"
										type="color"
										name="gdpr-cookie-confirm-border-color"
										v-model="confirm_border_color"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="1"
										step="0.01"
										v-model="confirm_opacity"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-confirm-opacity"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie confirm opacity', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-confirm-opacity"
										class="gdpr-slider-input opacity-slider"
										type="number"
										min="0"
										max="1"
										step="0.1"
										name="gdpr-cookie-confirm-opacity"
										v-model="confirm_opacity"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="10"
										step="0.5"
										v-model="confirm_border_width"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-confirm-border-width"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie confirm border width', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-confirm-border-width"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-confirm-border-width"
										v-model="confirm_border_width"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="100"
										step="0.5"
										v-model="confirm_border_radius"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-confirm-border-radius"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie confirm border radius', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-confirm-border-radius"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-confirm-border-radius"
										v-model="confirm_border_radius"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="cancel_button_popup"
						class="gdpr-cancel-button-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Cancel Button', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="cancel_button_popup=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="cancel_text_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label for="gdpr-cookie-cancel-text-color" class="screen-reader-text">
										<?php esc_attr_e( 'gdpr cookie cancel text color', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-cancel-text-color"
										type="color"
										name="gdpr-cookie-cancel-text-color"
										v-model="cancel_text_color"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="cancel_background_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label for="gdpr-cookie-cancel-background-color" class="screen-reader-text">
										<?php esc_attr_e( 'gdpr cookie cancel background color', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-cancel-background-color"
										type="color"
										name="gdpr-cookie-cancel-background-color"
										v-model="cancel_background_color"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-cancel-border-style"
										:reduce="label => label.code"
										:options="border_style_options"
										v-model="cancel_style"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-cancel-border-style"
										v-model="cancel_style"
									>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="cancel_border_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label for="gdpr-cookie-cancel-border-color" class="screen-reader-text">
										<?php esc_attr_e( 'gdpr cookie cancel border color', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-cancel-border-color"
										type="color"
										name="gdpr-cookie-cancel-border-color"
										v-model="cancel_border_color"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="1"
										step="0.01"
										v-model="cancel_opacity"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label for="gdpr-cookie-cancel-opacity" class="screen-reader-text">
										<?php esc_attr_e( 'gdpr cookie cancel opacity', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-cancel-opacity"
										class="gdpr-slider-input opacity-slider"
										type="number"
										min="0"
										max="1"
										step="0.1"
										name="gdpr-cookie-cancel-opacity"
										v-model="cancel_opacity"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="10"
										step="0.5"
										v-model="cancel_border_width"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label for="gdpr-cookie-cancel-border-width" class="screen-reader-text">
										<?php esc_attr_e( 'gdpr cookie cancel border width', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-cancel-border-width"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-cancel-border-width"
										v-model="cancel_border_width"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="100"
										step="0.5"
										v-model="cancel_border_radius"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label for="gdpr-cookie-cancel-border-radius" class="screen-reader-text">
										<?php esc_attr_e( 'gdpr cookie cancel border radius', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-cancel-border-radius"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-cancel-border-radius"
										v-model="cancel_border_radius"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="opt_out_link_popup"
						class="gdpr-opt-out-link-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Opt-out Link', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="opt_out_link_popup=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="opt_out_text_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-opt-out-text-color"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie opt out text color', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-opt-out-text-color"
										type="color"
										name="gdpr-cookie-opt-out-text-color"
										v-model="opt_out_text_color"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="ccpa_revoke_consent_popup"
						class="gdpr-ccpa-revoke-consent-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Revoke Consent Settings(US State Laws)', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="ccpa_revoke_consent_popup=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

					<div class="optout-settings-main-container">
							<c-row
								v-show="is_auto_mode || is_us_state_laws"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="ccpa_button_revoke_consent_text_color"
									></c-input>

									<c-input
										class="gdpr-color-select"
										type="color"
										name="gcc-ccpa-revoke-consent-text-color"
										v-model="ccpa_button_revoke_consent_text_color"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="is_auto_mode || is_us_state_laws"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="ccpa_button_revoke_consent_background_color"
									></c-input>

									<c-input
										class="gdpr-color-select"
										type="color"
										name="gcc-ccpa-revoke-consent-background-color"
										v-model="ccpa_button_revoke_consent_background_color"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="is_auto_mode || is_us_state_laws"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Tab Position', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="ccpa-cookie-consent-tab-position"
										:reduce="label => label.code"
										:options="tab_position_options"
										v-model="ccpa_tab_position"
									></v-select>

									<input
										type="hidden"
										name="gcc-ccpa-tab-position"
										v-model="ccpa_tab_position"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="is_auto_mode || is_us_state_laws"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Tab margin (in percent)', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<c-input
										type="number"
										min="0"
										max="100"
										name="gcc-ccpa-tab-margin"
										v-model="ccpa_tab_margin"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					
						</div>
					</div>
					<!-- Adding Virat-->
					<!-- Desgin Banner preview if A/B Testing is disabled and GDPR&CCPA both are selected-->
					<c-card class="desgin_card" v-show="!ab_testing_enabled && gdpr_policy == 'both' && active_default_multiple_legislation === 'gdpr'">
						<c-card-body v-show="active_default_multiple_legislation === 'gdpr'">

						<div class="gdpr-cookie-consent-banner-tabs" style="display:flex; flex-direction:column">
							<c-row>
								<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Banner Settings', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<div style="display:flex">
								<c-button class="gdpr-cookie-consent-banner-tab"@click="changeActiveMultipleLegislationToGDPR":class="{ 'gdpr-cookie-consent-banner-tab-active': active_default_multiple_legislation === 'gdpr' }"><?php esc_html_e( 'GDPR Banner' , 'gdpr-cookie-consent' ); ?></c-button>
								<c-button class="gdpr-cookie-consent-banner-tab"@click="changeActiveMultipleLegislationToCCPA":class="{ 'gdpr-cookie-consent-banner-tab-active': active_default_multiple_legislation === 'ccpa' }"><?php esc_html_e(  'CCPA Banner' , 'gdpr-cookie-consent' ); ?></c-button>
							</div>
						</div>
							<c-card-body >
									<!-- NEWLY ADDED -->
							
									<c-row v-show="is_gdpr">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Message Heading', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Leave it blank, If you do not need a heading.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea name="bar_heading_text_field" v-model="gdpr_message_heading"></c-textarea>
										</c-col>
									</c-row>
									<c-row v-show="is_gdpr">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'GDPR Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea name="notify_message_field" v-model="gdpr_message" :readonly="iabtcf_is_on"></c-textarea>
										</c-col>
									</c-row>
									<c-row v-show="is_gdpr">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'About Cookies Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Text shown under "About Cookies" section when users click on "Cookie Settings" button.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea :rows="6" name="about_message_field" v-model="gdpr_about_cookie_message" :readonly="iabtcf_is_on"></c-textarea>
										</c-col>
									</c-row>
							</c-card-body>
						</c-card-body>

					</c-card>
					<c-card class="desgin_card" v-show="!ab_testing_enabled && gdpr_policy == 'both' && active_default_multiple_legislation === 'gdpr'">
						<c-card-body v-show="active_default_multiple_legislation === 'gdpr'">
									<c-row>
										<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Cookie Bar Body Design', 'gdpr-cookie-consent' ); ?></div></c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cookie Bar Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="cookie_bar_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<label for="gdpr-multiple-legislation-cookie-bar-color1" class="screen-reader-text"><?php esc_attr_e('gdpr multiple legislation cookie bar color1', 'gdpr-cookie-consent'); ?></label>
										<c-input class="gdpr-color-select" id="gdpr-multiple-legislation-cookie-bar-color1" type="color" name="gdpr-cookie-bar-color" v-model="cookie_bar_color"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( ' Cookie Bar Opacity', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="cookie_bar_opacity" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<label for="gdpr-multiple-legislation-cookie-bar-opacity1" class="screen-reader-text"><?php esc_attr_e('gdpr multiple legislation cookie bar opacity1', 'gdpr-cookie-consent'); ?></label>
										<c-input id="gdpr-multiple-legislation-cookie-bar-opacity1" class="gdpr-slider-input opacity-slider" type="number"  min="0" max="1" step="0.01" name="gdpr-cookie-bar-opacity" v-model="cookie_bar_opacity"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="cookie_text_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<label for="gdpr-multiple-legislation-cookie-text-color1" class="screen-reader-text"><?php esc_attr_e('gdpr multiple legislation cookie text color1', 'gdpr-cookie-consent'); ?></label>
										<c-input class="gdpr-color-select" id="gdpr-multiple-legislation-cookie-text-color1" type="color" name="gdpr-cookie-text-color" v-model="cookie_text_color"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Styles', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<v-select class="form-group" id="gdpr-multiple-legislation-cookie-border-style1" :reduce="label => label.code" :options="border_style_options" v-model="border_style">
											</v-select>
											<input type="hidden" name="gdpr-border-style" v-model="border_style">
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="cookie_bar_border_width" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<label for="gdpr-multiple-legislation-cookie-bar-border-width1" class="screen-reader-text"><?php esc_attr_e('gdpr multiple legislation cookie bar border width1', 'gdpr-cookie-consent'); ?></label>
										<c-input id="gdpr-multiple-legislation-cookie-bar-border-width1" class="gdpr-slider-input"type="number" name="gdpr-bar-border-width" v-model="cookie_bar_border_width"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="cookie_border_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<label for="gdpr-multiple-legislation-cookie-border-color1" class="screen-reader-text"><?php esc_attr_e('gdpr multiple legislation cookie border color1', 'gdpr-cookie-consent'); ?></label>
										<c-input class="gdpr-color-select" id="gdpr-multiple-legislation-cookie-border-color1" type="color" name="gdpr-cookie-border-color" v-model="cookie_border_color"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="cookie_bar_border_radius" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<label for="gdpr-multiple-legislation-cookie-bar-border-radius1" class="screen-reader-text"><?php esc_attr_e('gdpr multiple legislation cookie bar border radius1', 'gdpr-cookie-consent'); ?></label>
										<c-input id="gdpr-multiple-legislation-cookie-bar-border-radius1" class="gdpr-slider-input"type="number" name="gdpr-cookie-bar-border-radius" v-model="cookie_bar_border_radius"></c-input>
										</c-col>
									</c-row>
									<?php
									$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ? GDPR_COOKIE_CONSENT_VERSION : '';
									if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
										if ( ! $is_pro_active ) :
											?>
											<c-row>
												<c-col class="col-sm-4"><label><?php esc_attr_e( 'Font', 'gdpr-cookie-consent' ); ?></label>
													<div class="gdpr-pro-label"><div class="gdpr-pro-label-text">Pro</div></div>
												</c-col>
												<c-col class="col-sm-8">
													<v-select disabled class="form-group" id="gdpr-cookie-font" :reduce="label => label.code" :options="font_options" v-model="cookie_font">
													</v-select>
													<input type="hidden" name="gdpr-cookie-font" v-model="cookie_font">
												</c-col>
											</c-row>
										<?php endif ?>
										<?php
										do_action( 'gdpr_cookie_font' );
									} else {
										?>
											<c-row>
												<c-col class="col-sm-4"><label><?php esc_attr_e( 'Font', 'gdpr-cookie-consent' ); ?></label></c-col>
												<c-col class="col-sm-8">
													<v-select class="form-group" id="gdpr-cookie-font" :reduce="label => label.code" :options="font_options" v-model="cookie_font">
													</v-select>
													<input type="hidden" name="gdpr-cookie-font" v-model="cookie_font	">
												</c-col>
											</c-row>
										<?php } ?>
									<?php
										?>
									<c-row>
									<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Upload Logo ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'To preview the logo, simply upload a logo and then click the "Save Changes" button ', 'gdpr-cookie-consent' ); ?>"></tooltip><a href="https://wplegalpages.com/pricing?utm_source=wp_cookie_consent&utm_medium=upload_logo&utm_campaign=plugin_upgrade" style="margin-left:5px;"  class="probadge bg-badge"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 640" fill="#f5af2f"> <path d="M345 151.2C354.2 143.9 360 132.6 360 120C360 97.9 342.1 80 320 80C297.9 80 280 97.9 280 120C280 132.6 285.9 143.9 295 151.2L226.6 258.8C216.6 274.5 195.3 278.4 180.4 267.2L120.9 222.7C125.4 216.3 128 208.4 128 200C128 177.9 110.1 160 88 160C65.9 160 48 177.9 48 200C48 221.8 65.5 239.6 87.2 240L119.8 457.5C124.5 488.8 151.4 512 183.1 512L456.9 512C488.6 512 515.5 488.8 520.2 457.5L552.8 240C574.5 239.6 592 221.8 592 200C592 177.9 574.1 160 552 160C529.9 160 512 177.9 512 200C512 208.4 514.6 216.3 519.1 222.7L459.7 267.3C444.8 278.5 423.5 274.6 413.5 258.9L345 151.2z"/><path d="M180 550H460" fill="none" stroke="#f5af2f" stroke-width="28" stroke-linecap="round"/></svg></a></label>
											</c-col>
											<c-col class="col-sm-8 ">
												<c-button color="info" class="button" id="image-upload-button" name="image-upload-button" @click="openMediaModal" style="margin: 10px;" <?php echo $is_disabled ? 'disabled' : ''; ?>>
													<?php esc_attr_e( 'Add Image', 'gdpr-cookie-consent' ); ?>
												</c-button>
												<c-button color="info" class="button" id="image-delete-button" @click="deleteSelectedimage" style="margin: 10px; ">
													<?php esc_attr_e( 'Remove Image', 'gdpr-cookie-consent' ); ?>
												</c-button>
												<?php
												$get_banner_imgml = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
												?>
												<img alt="" id="gdpr-cookie-bar-logo-holder" name="gdpr-cookie-bar-logo-holder" src="<?php echo esc_url_raw( $get_banner_imgml ); ?>">
												<p class="image-upload-notice" style="margin-left: 10px;">
													<?php esc_attr_e( 'We recommend 50 x 50 pixels.', 'gdpr-cookie-consent' ); ?>
												</p>
												<c-input type="hidden" name="gdpr-cookie-bar-logo-url-holder" id="gdpr-cookie-bar-logo-url-holder"  class="regular-text"> </c-input>
									</c-col>
								</c-row>
								</c-card-body>

								</c-card>
							<c-card class="desgin_card" v-show="!ab_testing_enabled && gdpr_policy == 'both' && active_default_multiple_legislation === 'gdpr'">
        						<c-card-body v-show="active_default_multiple_legislation === 'gdpr'">
								<c-card-body>
									<c-row>
										<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Button Settings', 'gdpr-cookie-consent' ); ?></div></c-col>
									</c-row>	
									<!-- Privacy Policy Settings -->
									<c-row v-show="show_revoke_card || is_lgpd">
										<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Privacy Policy Settings', 'gdpr-cookie-consent' ); ?></div></c-col>
									</c-row>
									<c-row v-show="show_revoke_card || is_lgpd">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Privacy Policy Link', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enable this to provide a link to your Privacy & Cookie Policy on your Cookie Notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-4">
											<c-switch v-bind="labelIcon" v-model="button_readmore_is_on" id="gdpr-cookie-consent-readmore-is-on" variant="3d"  color="success" :checked="button_readmore_is_on" v-on:update:checked="onSwitchButtonReadMoreIsOn"></c-switch>
											<input type="hidden" name="gcc-readmore-is-on" v-model="button_readmore_is_on">
										</c-col>

										<c-col class="col-sm-3">
												<c-button :disabled="!button_readmore_is_on" class="gdpr-configure-button" @click="button_readmore_popup=true">
													<span>
														<img class="gdpr-configure-image" :src="configure_image_url.default">
														<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
													</span>
												</c-button>
											</c-col>
									</c-row>
									<div class="opt-out-link-container">
										<c-modal
												title="Policy Privacy Settings"
												:show.sync="button_readmore_popup"
												size="lg"
												:close-on-backdrop="closeOnBackdrop"
												:centered="centered"
											>
											<div class="optout-settings-tittle-bar">
												<div class="optout-setting-tittle"><?php esc_attr_e( 'Privacy Policy Settings', 'gdpr-cookie-consent' ); ?></div>
												<img @click="button_readmore_popup=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
											</div>

											<div class="optout-settings-main-container">
												<c-row v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on" class="gdpr-label-row">
													<c-col class="col-sm-6"><label><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text of the privacy policy button/link.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
													<c-col class="col-sm-6"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label></c-col>
												</c-row>
												<c-row v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on">
													<c-col class="col-sm-6">
														<c-input name="button_readmore_text_field" v-model="button_readmore_text"></c-input>
													</c-col>
													<c-col class="col-sm-6 gdpr-color-pick" >
														<c-input class="gdpr-color-input" type="text" v-model="button_readmore_link_color"></c-input>
														<c-input class="gdpr-color-select" id="gdpr-readmore-link-color" type="color" name="gcc-readmore-link-color" v-model="button_readmore_link_color"></c-input>
													</c-col>
												</c-row>
												<c-row v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on" class="gdpr-label-row">
													<c-col class="col-sm-6"><label><?php esc_attr_e( 'Show as', 'gdpr-cookie-consent' ); ?></label></c-col>
													<c-col class="col-sm-6"><label><?php esc_attr_e( 'Page or Custom URL', 'gdpr-cookie-consent' ); ?></label></c-col>
												</c-row>
												

												<button type="button" class="done-button-settings" @click="button_readmore_popup=false">Done</button>
											</div>
										</c-modal>		
									</div>
								</c-card-body>

								<c-card-body>
									<!-- Revoke Consent settings -->
									<c-row v-show="show_revoke_card || is_lgpd">
										<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Revoke Consent', 'gdpr-cookie-consent' ); ?></div></c-col>
									</c-row>
									<c-row v-show="show_revoke_card || is_lgpd">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Revoke Consent', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enable to give user the option to revoke their consent.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-4">
											<c-switch v-bind="labelIcon" v-model="is_revoke_consent_on" id="gdpr-cookie-consent-revoke-consent" variant="3d"  color="success" :checked="is_revoke_consent_on" v-on:update:checked="onSwitchRevokeConsentEnable"></c-switch>
											<input type="hidden" name="gcc-revoke-consent-enable" v-model="is_revoke_consent_on">
										</c-col>

										<c-col class="col-sm-3">
											<c-button :disabled="!is_revoke_consent_on" class="gdpr-configure-button" @click="revoke_consent_popup=true">
												<span>
													<img class="gdpr-configure-image" :src="configure_image_url.default">
													<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
												</span>
											</c-button>
										</c-col>
									</c-row>

									<div class="opt-out-link-container">
										<c-modal
												title="Revoke Consent Settings"
												:show.sync="revoke_consent_popup"
												size="lg"
												:close-on-backdrop="closeOnBackdrop"
												:centered="centered"
											>
											<div class="optout-settings-tittle-bar">
												<div class="optout-setting-tittle"><?php esc_attr_e( 'Revoke Consent Settings', 'gdpr-cookie-consent' ); ?></div>
												<img @click="revoke_consent_popup=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
											</div>

											<div class="optout-settings-main-container">
												<c-row v-show="(show_revoke_card || is_lgpd) && is_revoke_consent_on" class="gdpr-label-row">
													<c-col class="col-sm-6"><label><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?></label></c-col>
													<c-col class="col-sm-6"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label></c-col>

												</c-row>
												<c-row v-show="(show_revoke_card || is_lgpd) && is_revoke_consent_on">
													<c-col class="col-sm-6">
														<c-input name="show_again_text_field" v-model="tab_text"></c-input>
													</c-col>
													<c-col class="col-sm-6 gdpr-color-pick" >
														<c-input class="gdpr-color-input" type="text" v-model="button_revoke_consent_text_color"></c-input>
														<c-input class="gdpr-color-select" id="gdpr-readmore-link-color" type="color" name="gcc-revoke-consent-text-color" v-model="button_revoke_consent_text_color"></c-input>
													</c-col>
												</c-row>
												<c-row v-show="(show_revoke_card || is_lgpd) && is_revoke_consent_on" class="gdpr-label-row">
													<c-col class="col-sm-6"><label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label></c-col>
													<c-col class="col-sm-6"><label><?php esc_attr_e( 'Tab Position', 'gdpr-cookie-consent' ); ?></label></c-col>
												</c-row>
												<c-row v-show="(show_revoke_card || is_lgpd) && is_revoke_consent_on">
													<c-col class="col-sm-6 gdpr-color-pick" >
														<c-input class="gdpr-color-input" type="text" v-model="button_revoke_consent_background_color"></c-input>
														<c-input class="gdpr-color-select" id="gdpr-readmore-button-color" type="color" name="gcc-revoke-consent-background-color" v-model="button_revoke_consent_background_color"></c-input>
													</c-col>
													<c-col class="col-sm-6">
														<v-select class="form-group" id="gdpr-cookie-consent-tab-position" :reduce="label => label.code" :options="tab_position_options" v-model="tab_position">
														</v-select>
														<input type="hidden" name="gcc-tab-position" v-model="tab_position">
													</c-col>
												</c-row>
												<c-row v-show="(show_revoke_card || is_lgpd) && is_revoke_consent_on" class="gdpr-label-row">
													<c-col class="col-sm-3"><label><?php esc_attr_e( 'Tab margin (in percent)', 'gdpr-cookie-consent' ); ?></label></c-col>
													<c-col class="col-sm-9">
														<c-input type="number" min="0" max="100" name="gcc-tab-margin" v-model="tab_margin"></c-input>
													</c-col>
												</c-row>
												<button type="button" class="done-button-settings" @click="revoke_consent_popup=false">Done</button>
											</div>
										</c-modal>
									</div>
								</c-card-body>
								
								<c-card v-show="is_gdpr || is_eprivacy || is_lgpd">
								<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Accept Button', 'gdpr-cookie-consent' ); ?></c-card-header>
								<c-card-body>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-4">
											<c-switch v-bind="labelIcon" v-model="cookie_accept_on" id="gdpr-cookie-consent-cookie1" variant="3d"  color="success" :checked="cookie_accept_on" v-on:update:checked="onSwitchCookieAcceptEnable"></c-switch>
											<input type="hidden" name="gcc-cookie-accept-enable" v-model="cookie_accept_on">
										</c-col>
										<c-col class="col-sm-3">
											<c-button :disabled="!cookie_accept_on" class="gdpr-configure-button" @click="accept_button_popup=true">
												<span>
													<img class="gdpr-configure-image" :src="configure_image_url.default" alt="WPCS Configure Logo icon">
													<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
												</span>
											</c-button>
										</c-col>
									</c-row>
									<div class="opt-out-link-container">
									<c-modal
										title="Accept Button"
										:show.sync="accept_button_popup"
										size="lg"
										:close-on-backdrop="closeOnBackdrop"
										:centered="centered"
									>
									<div class="optout-settings-tittle-bar">
											<div class="optout-setting-tittle"><?php esc_attr_e( 'Accept Button', 'gdpr-cookie-consent' ); ?></div>
											<img @click="accept_button_popup=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
											</div>
									<c-row class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-6">
											<label for="button_accept_text_fieldnum1" class="screen-reader-text"><?php esc_attr_e('button accept text field1', 'gdpr-cookie-consent'); ?></label>
											<c-input id="button_accept_text_fieldnum1" name="button_accept_text_field" v-model="accept_text"></c-input>
										</c-col>
										<c-col class="col-sm-6 gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="accept_text_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-accept-text-colorvar1" class="screen-reader-text"><?php esc_attr_e('gdpr cookie accept text color1', 'gdpr-cookie-consent'); ?></label>
											<c-input class="gdpr-color-select" id="gdpr-cookie-accept-text-colorvar1" type="color" name="gdpr-cookie-accept-text-color" v-model="accept_text_color"></c-input>
										</c-col>
									</c-row>
									<c-row  class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Action ', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Select action to do once the user clicks on button.', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-accept-as-button1" :reduce="label => label.code" :options="accept_as_button_options" v-model="accept_as_button" @input="onButtonChange($event, 'accept')"></v-select>
											<input type="hidden" name="gdpr-cookie-accept-as" v-model="accept_as_button">
										</c-col>
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-accept-action" :reduce="label => label.code" :options="accept_action_options" v-model="accept_action" 	>
											</v-select>
											<input type="hidden" name="gdpr-cookie-accept-action" v-model="accept_action">
										</c-col>
									</c-row>
									<c-row v-show="accept_action!='#cookie_action_close_header'"  class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row v-show="accept_action1='#cookie_action_close_header'">
										<c-col class="col-sm-6">
											<c-input id="gdpr-cookie-accept-url1" name="gdpr-cookie-accept-url" v-model="accept_url" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										</c-col>
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-url-new-window1" :reduce="label => label.code" :options="open_url_options" v-model="open_url"></v-select>
											<input type="hidden" name="gdpr-cookie-url-new-window" v-model="open_url">
										</c-col>
									</c-row>
									<c-row class="gdpr-label-row"  v-show="accept_as_button">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										
									</c-row>
									<c-row  v-show="accept_as_button">
										<c-col class="col-sm-6  gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="accept_background_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-accept-background-color1" class="screen-reader-text"><?php esc_attr_e('gdpr cookie accept background color1', 'gdpr-cookie-consent'); ?></label>
											<c-input class="gdpr-color-select" id="gdpr-cookie-accept-background-color1" type="color" name="gdpr-cookie-accept-background-color" v-model="accept_background_color"></c-input>
										</c-col>
										
									</c-row>
									<c-row  v-show="accept_as_button" class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row v-show="accept_as_button">
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-accept-border-style1" :reduce="label => label.code" :options="border_style_options" v-model="accept_style">
											</v-select>
											<input type="hidden" name="gdpr-cookie-accept-border-style" v-model="accept_style">
										</c-col>
										<c-col class="col-sm-6 gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="accept_border_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<c-input class="gdpr-color-select" type="color" name="gdpr-cookie-accept-border-color" v-model="accept_border_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										</c-col>
									</c-row>
									<c-row v-show="accept_as_button" class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row v-show="accept_as_button">
										<c-col class="col-sm-4  gdpr-color-pick">
 											<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="accept_opacity" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-accept-opacity" class="screen-reader-text"><?php esc_attr_e('gdpr cookie accept opacity', 'gdpr-cookie-consent'); ?></label>
											<c-input id="gdpr-cookie-accept-opacity" class="gdpr-slider-input opacity-slider" type="number"  min="0" max="1" step="0.1"  name="gdpr-cookie-accept-opacity" v-model="accept_opacity"></c-input>
										</c-col>
										<c-col class="col-sm-4 gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="accept_border_width" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-accept-border-width" class="screen-reader-text"><?php esc_attr_e('gdpr cookie accept border width', 'gdpr-cookie-consent'); ?></label>
											<c-input id="gdpr-cookie-accept-border-width" class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-border-width" v-model="accept_border_width"></c-input>
										</c-col>
										<c-col class="col-sm-4  gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="accept_border_radius" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-accept-border-radius" class="screen-reader-text"><?php esc_attr_e('gdpr cookie accept border radius', 'gdpr-cookie-consent'); ?></label>
											<c-input id="gdpr-cookie-accept-border-radius" class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-border-radius" v-model="accept_border_radius"></c-input>
										</c-col>
									</c-row>

											<button  class="done-button-settings" @click="accept_button_popup=false"><span>Done</span></button>

									</c-modal>
									</div>
								</c-card-body>
								</c-card>
								<c-card v-show="is_gdpr || is_eprivacy || is_lgpd">
								<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Accept All Button', 'gdpr-cookie-consent' ); ?></c-card-header>
								<c-card-body>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-4">
											<c-switch v-bind="labelIcon" v-model="cookie_accept_all_on" id="gdpr-cookie-consent-cookie-acceptall-on" variant="3d"  color="success" :checked="cookie_accept_all_on" v-on:update:checked="onSwitchCookieAcceptAllEnable"></c-switch>
											<input type="hidden" name="gcc-cookie-accept-all-enable" v-model="cookie_accept_all_on">
										</c-col>
										<c-col class="col-sm-3">
											<c-button :disabled="!cookie_accept_all_on" class="gdpr-configure-button" @click="accept_all_button_popup=true">
												<span>
													<img class="gdpr-configure-image" :src="configure_image_url.default" alt="WPCS Configure Logo icon">
													<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
												</span>
											</c-button>
										</c-col>
									</c-row>
									<div class="opt-out-link-container">
									<c-modal
										title="Accept All Button"
										:show.sync="accept_all_button_popup"
										size="lg"
										:close-on-backdrop="closeOnBackdrop"
										:centered="centered"
									>
									<div class="optout-settings-tittle-bar">
											<div class="optout-setting-tittle"><?php esc_attr_e( 'Accept All Button', 'gdpr-cookie-consent' ); ?></div>
											<img @click="accept_all_button_popup=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
											</div>
									<c-row class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-6">
											<label for="button_accept_all_text_field" class="screen-reader-text"><?php esc_attr_e('button accept all text field', 'gdpr-cookie-consent'); ?></label>
											<c-input id="button_accept_all_text_field" name="button_accept_all_text_field" v-model="accept_all_text"></c-input>
										</c-col>
										<c-col class="col-sm-6 gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="accept_all_text_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-accept-all-text-color" class="screen-reader-text"><?php esc_attr_e('gdpr cookie accept all text color', 'gdpr-cookie-consent'); ?></label>
											<c-input class="gdpr-color-select" id="gdpr-cookie-accept-all-text-color" type="color" name="gdpr-cookie-accept-all-text-color" v-model="accept_all_text_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										</c-col>
									</c-row>
									<c-row  class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Action ', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Select action to do once the user clicks on button.', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-accept-all-as-button" :reduce="label => label.code" :options="accept_as_button_options" v-model="accept_all_as_button" @input="onButtonChange($event, 'accept_all')"></v-select>
											<input type="hidden" name="gdpr-cookie-accept-all-as" v-model="accept_all_as_button">
										</c-col>
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-accept-all-action" :reduce="label => label.code" :options="accept_action_options" v-model="accept_all_action" >
											</v-select>
											<input type="hidden" name="gdpr-cookie-accept-all-action" v-model="accept_all_action">
										</c-col>
									</c-row>
									<c-row v-show="accept_all_action!='#cookie_action_close_header'"  class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row v-show="accept_all_action!='#cookie_action_close_header'">
										<c-col class="col-sm-6">
											<label for="gdpr-cookie-accept-all-url" class="screen-reader-text"><?php esc_attr_e('gdpr cookie accept all url', 'gdpr-cookie-consent'); ?></label>
											<c-input id="gdpr-cookie-accept-all-url" name="gdpr-cookie-accept-all-url" v-model="accept_all_url"></c-input>
										</c-col>
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-accept-all-new-window" :reduce="label => label.code" :options="open_url_options" v-model="accept_all_new_win"></v-select>
											<input type="hidden" name="gdpr-cookie-accept-all-new-window" v-model="accept_all_new_win">
										</c-col>
									</c-row>
									<c-row class="gdpr-label-row"  v-show="accept_all_as_button">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										
									</c-row>
									<c-row  v-show="accept_all_as_button">
										<c-col class="col-sm-6  gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="accept_all_background_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-accept-all-background-color" class="screen-reader-text"><?php esc_attr_e('gdpr cookie accept all background color', 'gdpr-cookie-consent'); ?></label>
											<c-input class="gdpr-color-select" id="gdpr-cookie-accept-all-background-color" type="color" name="gdpr-cookie-accept-all-background-color" v-model="accept_all_background_color"></c-input>
										</c-col>
										
									</c-row>
									<c-row  v-show="accept_all_as_button" class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row v-show="accept_all_as_button">
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-accept-all-border-style" :reduce="label => label.code" :options="border_style_options" v-model="accept_all_style">
											</v-select>
											<input type="hidden" name="gdpr-cookie-accept-all-border-style" v-model="accept_all_style">
										</c-col>
										<c-col class="col-sm-6 gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="accept_all_border_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-accept-all-border-color" class="screen-reader-text"><?php esc_attr_e('gdpr cookie accept all border color', 'gdpr-cookie-consent'); ?></label>
											<c-input class="gdpr-color-select" id="gdpr-cookie-accept-all-border-color" type="color" name="gdpr-cookie-accept-all-border-color" v-model="accept_all_border_color"></c-input>
										</c-col>
									</c-row>
									<c-row v-show="accept_all_as_button" class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row v-show="accept_all_as_button">
										<c-col class="col-sm-4  gdpr-color-pick">
 											<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="accept_all_opacity" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-accept-all-opacity" class="screen-reader-text"><?php esc_attr_e('gdpr cookie accept all opacity', 'gdpr-cookie-consent'); ?></label>
											<c-input id="gdpr-cookie-accept-all-opacity" class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-accept-all-opacity" v-model="accept_all_opacity"></c-input>
										</c-col>
										<c-col class="col-sm-4 gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="accept_all_border_width" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-accept-all-border-width" class="screen-reader-text"><?php esc_attr_e('gdpr cookie accept all border width', 'gdpr-cookie-consent'); ?></label>
											<c-input id="gdpr-cookie-accept-all-border-width" class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-all-border-width" v-model="accept_all_border_width"></c-input>
										</c-col>
										<c-col class="col-sm-4  gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="accept_all_border_radius" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-accept-all-border-radius" class="screen-reader-text"><?php esc_attr_e('gdpr multiple legislation cookie accept all border radius', 'gdpr-cookie-consent'); ?></label>
											<c-input id="gdpr-cookie-accept-all-border-radius" class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-all-border-radius" v-model="accept_all_border_radius"></c-input>
										</c-col>
									</c-row>
											<button class="done-button-settings" @click="accept_all_button_popup=false"><span>Done</span></button>

									</c-modal>
									</div>
								</c-card-body>
							</c-card>
							<c-card v-show="is_gdpr || is_eprivacy || is_lgpd">
								<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Decline Button', 'gdpr-cookie-consent' ); ?></c-card-header>
								<c-card-body>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-4">
											<c-switch v-bind="labelIcon" v-model="cookie_decline_on" id="gdpr-cookie-consent-decline-on" variant="3d"  color="success" :checked="cookie_decline_on" v-on:update:checked="onSwitchCookieDeclineEnable"></c-switch>
											<input type="hidden" name="gcc-cookie-decline-enable" v-model="cookie_decline_on">
										</c-col>
										<c-col class="col-sm-3">
											<c-button :disabled="!cookie_decline_on" class="gdpr-configure-button" @click="decline_button_popup=true">
												<span>
													<img class="gdpr-configure-image" :src="configure_image_url.default" alt="WPCS Configure Logo icon">
													<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
												</span>
											</c-button>
										</c-col>
									</c-row>
									<div class="opt-out-link-container">
									<c-modal
										title="Decline Button"
										:show.sync="decline_button_popup"
										size="lg"
										:close-on-backdrop="closeOnBackdrop"
										:centered="centered"
									>
									<div class="optout-settings-tittle-bar">
											<div class="optout-setting-tittle"><?php esc_attr_e( 'Decline Button', 'gdpr-cookie-consent' ); ?></div>
											<img @click="decline_button_popup=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
											</div>
										<c-row class="gdpr-label-row">
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
										</c-row>
										<c-row>
											<c-col class="col-sm-6">
												<label for="button_decline_text_field" class="screen-reader-text"><?php esc_attr_e('button decline text field', 'gdpr-cookie-consent'); ?></label>
												<c-input id="button_decline_text_field" name="button_decline_text_field" v-model="decline_text"></c-input>
											</c-col>
											<c-col class="col-sm-6  gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="decline_text_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
												<label for="gdpr-cookie-decline-text-color" class="screen-reader-text"><?php esc_attr_e('gdpr cookie decline text color', 'gdpr-cookie-consent'); ?></label>
												<c-input class="gdpr-color-select" id="gdpr-cookie-decline-text-color" type="color" name="gdpr-cookie-decline-text-color" v-model="decline_text_color"></c-input>
											</c-col>
										</c-row>
										<c-row class="gdpr-label-row">
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Action ', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Select action to do once the user clicks on the button', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
											</c-col>
										</c-row>
										<c-row>
											<c-col class="col-sm-6">
												<v-select class="form-group" id="gdpr-cookie-decline-as-button" :reduce="label => label.code" :options="accept_as_button_options" v-model="decline_as_button" @input="onButtonChange($event, 'decline')"></v-select>
												<input type="hidden" name="gdpr-cookie-decline-as" v-model="decline_as_button">
											</c-col>
											<c-col class="col-sm-6"><v-select class="form-group" id="gdpr-cookie-decline-action" :reduce="label => label.code" :options="decline_action_options" v-model="decline_action">
												</v-select>
												<input type="hidden" name="gdpr-cookie-decline-action" v-model="decline_action">
											</c-col>
										</c-row>
										<c-row v-show="decline_action!='#cookie_action_close_header_reject'" class="gdpr-label-row">
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
										</c-row>
										<c-row v-show="decline_action!='#cookie_action_close_header_reject'">
											<c-col class="col-sm-6">
												<label for="gdpr-cookie-decline-url" class="screen-reader-text"><?php esc_attr_e('gdpr cookie decline url', 'gdpr-cookie-consent'); ?></label>
												<c-input id="gdpr-cookie-decline-url" name="gdpr-cookie-decline-url" v-model="decline_url"></c-input>
											</c-col>
											<c-col class="col-sm-6">
												<v-select class="form-group" id="gdpr-cookie-decline-url-new-window" :reduce="label => label.code" :options="open_url_options" v-model="open_decline_url"></v-select>
												<input type="hidden" name="gdpr-cookie-decline-url-new-window" v-model="open_decline_url">
											</c-col>
										</c-row>
										<c-row v-show="decline_as_button" class="gdpr-label-row">
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											
										</c-row>
										<c-row v-show="decline_as_button">
											<c-col class="col-sm-6  gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="decline_background_color"></c-input>
												<label for="gdpr-cookie-decline-background-color" class="screen-reader-text"><?php esc_attr_e('gdpr cookie decline background color', 'gdpr-cookie-consent'); ?></label>
												<c-input class="gdpr-color-select" id="gdpr-cookie-decline-background-color" type="color" name="gdpr-cookie-decline-background-color" v-model="decline_background_color"></c-input>
											</c-col>
											
										</c-row>
										<c-row v-show="decline_as_button" class="gdpr-label-row">
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
										</c-row>
										<c-row v-show="decline_as_button">
											<c-col class="col-sm-6">
												<v-select class="form-group" id="gdpr-cookie-decline-border-style" :reduce="label => label.code" :options="border_style_options" v-model="decline_style">
												</v-select>
												<input type="hidden" name="gdpr-cookie-decline-border-style" v-model="decline_style">
											</c-col>
											<c-col class="col-sm-6  gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="decline_border_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
												<label for="gdpr-cookie-decline-border-color" class="screen-reader-text"><?php esc_attr_e('gdpr cookie decline border color', 'gdpr-cookie-consent'); ?></label>
												<c-input class="gdpr-color-select" id="gdpr-cookie-decline-border-color" type="color" name="gdpr-cookie-decline-border-color" v-model="decline_border_color"></c-input>
											</c-col>
										</c-row>
										<c-row  v-show="decline_as_button" class="gdpr-label-row">
											<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
										</c-row>
										<c-row v-show="decline_as_button">
											<c-col class="col-sm-4 gdpr-color-pick"><c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="decline_opacity" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-decline-opacity" class="screen-reader-text"><?php esc_attr_e('gdpr cookie decline opacity', 'gdpr-cookie-consent'); ?></label>
												<c-input id="gdpr-cookie-decline-opacity" class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-decline-opacity" v-model="decline_opacity"></c-input>
											</c-col>
											<c-col class="col-sm-4 gdpr-color-pick"><c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="decline_border_width" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-decline-border-width" class="screen-reader-text"><?php esc_attr_e('gdpr cookie decline border width', 'gdpr-cookie-consent'); ?></label>
												<c-input id="gdpr-cookie-decline-border-width" class="gdpr-slider-input"type="number" name="gdpr-cookie-decline-border-width" v-model="decline_border_width"></c-input>
											</c-col>
											<c-col class="col-sm-4 gdpr-color-pick">
												<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="decline_border_radius" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
												<label for="gdpr-cookie-decline-border-radius" class="screen-reader-text"><?php esc_attr_e('gdpr cookie decline border radius', 'gdpr-cookie-consent'); ?></label>
												<c-input id="gdpr-cookie-decline-border-radius" class="gdpr-slider-input"type="number" name="gdpr-cookie-decline-border-radius" v-model="decline_border_radius"></c-input>
											</c-col>
										</c-row> 
												<button class="done-button-settings" @click="decline_button_popup=false"><span>Done</span></button>

									</c-modal>
									</div>
								</c-card-body>
							</c-card>
							<c-card v-show="is_gdpr || is_lgpd">
								<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Settings Button', 'gdpr-cookie-consent' ); ?></c-card-header>
								<c-card-body>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-4">
											<c-switch v-bind="labelIcon" v-model="cookie_settings_on" id="gdpr-cookie-consent-settings-on" variant="3d"  color="success" :checked="cookie_settings_on" v-on:update:checked="onSwitchCookieSettingsEnable"></c-switch>
											<input type="hidden" name="gcc-cookie-settings-enable" v-model="cookie_settings_on">
										</c-col>
										<c-col class="col-sm-3">
											<c-button :disabled="!cookie_settings_on" class="gdpr-configure-button" @click="settings_button_popup=true">
												<span>
													<img class="gdpr-configure-image" :src="configure_image_url.default" alt="WPCS Configure Logo icon">
													<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
												</span>
											</c-button>
										</c-col>
										<c-col class="col-sm-4">
												<label><?php esc_attr_e( 'Display Cookies List on Frontend', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-4">
												<c-switch v-bind="labelIcon" v-model="cookie_on_frontend" id="gdpr-cookie-consent-cookie-on-frontend" variant="3d" color="success" :checked="cookie_on_frontend" v-on:update:checked="onSwitchCookieOnFrontend" :disabled="!cookie_settings_on"></c-switch>
												<input type="hidden" name="gcc-cookie-on-frontend" v-model="cookie_on_frontend">
											</c-col>
											<c-col class="col-sm-4">
												<?php do_action( 'gdpr_cookie_layout_skin_label' ); ?>
											</c-col>
											<c-col class="col-sm-4">
												<?php do_action( 'gdpr_cookie_layout_skin_markup' ); ?>
											</c-col>

									</c-row>
									<div class="opt-out-link-container">
									<c-modal
										title="Settings Button"
										:show.sync="settings_button_popup"
										size="lg"
										:close-on-backdrop="closeOnBackdrop"
										:centered="centered"
									>
									<div class="optout-settings-tittle-bar">
											<div class="optout-setting-tittle"><?php esc_attr_e( 'Settings Button', 'gdpr-cookie-consent' ); ?></div>
											<img @click="settings_button_popup=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
											</div>
									<c-row class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-6">
											<label for="button_settings_text_field" class="screen-reader-text"><?php esc_attr_e('button settings text field', 'gdpr-cookie-consent'); ?></label>
											<c-input id="button_settings_text_field" name="button_settings_text_field" v-model="settings_text"></c-input>
										</c-col>
										<c-col class="col-sm-6  gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="settings_text_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-settings-text-color" class="screen-reader-text"><?php esc_attr_e('gdpr cookie settings text color', 'gdpr-cookie-consent'); ?></label>
											<c-input class="gdpr-color-select" id="gdpr-cookie-settings-text-color" type="color" name="gdpr-cookie-settings-text-color" v-model="settings_text_color"></c-input>
										</c-col>
									</c-row>
									<c-row class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-settings-as-button" :reduce="label => label.code" :options="accept_as_button_options" v-model="settings_as_button" @input="onButtonChange($event, 'settings')"></v-select>
											<input type="hidden" name="gdpr-cookie-settings-as" v-model="settings_as_button">
										</c-col>
									</c-row>
									<c-row v-show="settings_as_button" class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										
									</c-row>
									<c-row v-show="settings_as_button" class="gdpr-label-row">
										<c-col class="col-sm-6 gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="settings_background_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-settings-background-color" class="screen-reader-text"><?php esc_attr_e('gdpr cookie settings background color', 'gdpr-cookie-consent'); ?></label>
											<c-input class="gdpr-color-select" id="gdpr-cookie-settings-background-color" type="color" name="gdpr-cookie-settings-background-color" v-model="settings_background_color"></c-input>
										</c-col>
										
									</c-row>
									<c-row  v-show="settings_as_button" class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row v-show="settings_as_button">
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-settings-border-style" :reduce="label => label.code" :options="border_style_options" v-model="settings_style">
											</v-select>
											<input type="hidden" name="gdpr-cookie-settings-border-style" v-model="settings_style">
										</c-col>
										<c-col class="col-sm-6 gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="settings_border_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-settings-border-color" class="screen-reader-text"><?php esc_attr_e('gdpr cookie settings border color', 'gdpr-cookie-consent'); ?></label>
											<c-input class="gdpr-color-select" id="gdpr-cookie-settings-border-color" type="color" name="gdpr-cookie-settings-border-color" v-model="settings_border_color"></c-input>
										</c-col>
									</c-row>
									<c-row v-show="settings_as_button" class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row v-show="settings_as_button">
										<c-col class="col-sm-4 gdpr-color-pick">
 											<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="settings_opacity" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-settings-opacity" class="screen-reader-text"><?php esc_attr_e('gdpr cookie settings opacity', 'gdpr-cookie-consent'); ?></label>
											<c-input id="gdpr-cookie-settings-opacity" class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-settings-opacity" v-model="settings_opacity"></c-input>
										</c-col>
										<c-col class="col-sm-4 gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="settings_border_width" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-settings-border-width" class="screen-reader-text"><?php esc_attr_e('gdpr cookie settings border width', 'gdpr-cookie-consent'); ?></label>
											<c-input id="gdpr-cookie-settings-border-width" class="gdpr-slider-input"type="number" name="gdpr-cookie-settings-border-width" v-model="settings_border_width"></c-input>
										</c-col>
										<c-col class="col-sm-4 gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="settings_border_radius" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
											<label for="gdpr-cookie-settings-border-radius" class="screen-reader-text"><?php esc_attr_e('gdpr cookie settings border radius', 'gdpr-cookie-consent'); ?></label>
											<c-input id="gdpr-cookie-settings-border-radius" class="gdpr-slider-input"type="number" name="gdpr-cookie-settings-border-radius" v-model="settings_border_radius"></c-input>
										</c-col>
									</c-row>

												<button class="done-button-settings" @click="settings_button_popup=false"><span>Done</span></button>

									</c-modal>
									</div>
								</c-card-body>
							</c-card>


						</c-card-body>
					</c-card>
					<c-card class=" desgin_card" v-show="!ab_testing_enabled && gdpr_policy == 'both'">
						<c-card-body v-show="active_default_multiple_legislation === 'ccpa'">

						<div class="gdpr-cookie-consent-banner-tabs" style="display:flex; flex-direction:column;">
							<c-row>
								<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Banner Settings', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<div style="display:flex">
								<c-button class="gdpr-cookie-consent-banner-tab"@click="changeActiveMultipleLegislationToGDPR":class="{ 'gdpr-cookie-consent-banner-tab-active': active_default_multiple_legislation === 'gdpr' }"><?php esc_html_e( 'GDPR Banner' , 'gdpr-cookie-consent' ); ?></c-button>
								<c-button class="gdpr-cookie-consent-banner-tab"@click="changeActiveMultipleLegislationToCCPA":class="{ 'gdpr-cookie-consent-banner-tab-active': active_default_multiple_legislation === 'ccpa' }"><?php esc_html_e(  'CCPA Banner' , 'gdpr-cookie-consent' ); ?></c-button>
							</div>
						</div>
						<c-card-body >
								<!-- NEWLY ADDED -->
							
									<c-row v-show="is_auto_mode || is_us_state_laws">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'CCPA Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as CCPA notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea name="notify_message_ccpa_field" v-model="ccpa_message"></c-textarea>
										</c-col>
									</c-row>
									<c-row v-show="is_auto_mode || is_us_state_laws">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'CCPA Opt-out Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as CCPA notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea name="notify_message_ccpa_optout_field" v-model="ccpa_optout_message"></c-textarea>
										</c-col>
									</c-row>
						</c-card-body>
						</c-card-body>
					</c-card>
						<c-card class=" desgin_card" v-show="!ab_testing_enabled && gdpr_policy == 'both'">
							<c-card-body v-show="active_default_multiple_legislation === 'ccpa'">

									<c-row>
										<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Cookie Bar Body Design', 'gdpr-cookie-consent' ); ?></div></c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cookie Bar Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="cookie_bar_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<label for="gdpr-cookie-bar-color" class="screen-reader-text"><?php esc_attr_e('gdpr  cookie bar color', 'gdpr-cookie-consent'); ?></label>
										<c-input class="gdpr-color-select" id="gdpr-cookie-bar-color" type="color" name="gdpr-cookie-bar-color" v-model="cookie_bar_color"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( ' Cookie Bar Opacity', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="cookie_bar_opacity" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<label for="gdpr-cookie-bar-opacity" class="screen-reader-text"><?php esc_attr_e('gdpr cookie bar opacity', 'gdpr-cookie-consent'); ?></label>
										<c-input id="gdpr-cookie-bar-opacity" class="gdpr-slider-input opacity-slider" type="number"  min="0" max="1" step="0.01" name="gdpr-cookie-bar-opacity" v-model="cookie_bar_opacity"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="cookie_text_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<label for="gdpr-text-color" class="screen-reader-text"><?php esc_attr_e('gdpr text color', 'gdpr-cookie-consent'); ?></label>
										<c-input class="gdpr-color-select" id="gdpr-cookie-text-color" type="color" name="gdpr-cookie-text-color" v-model="cookie_text_color"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Styles', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<v-select class="form-group" id="gdpr-cookie-border-style" :reduce="label => label.code" :options="border_style_options" v-model="border_style">
											</v-select>
											<input type="hidden" name="gdpr-cookie-border-style" v-model="border_style">
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="cookie_bar_border_width" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<label for="gdpr-cookie-bar-border-width" class="screen-reader-text"><?php esc_attr_e('gdpr cookie bar border width', 'gdpr-cookie-consent'); ?></label>
										<c-input id="gdpr-cookie-bar-border-width" class="gdpr-slider-input"type="number" name="gdpr-cookie-bar-border-width" v-model="cookie_bar_border_width"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="cookie_border_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<label for="gdpr-cookie-border-color" class="screen-reader-text"><?php esc_attr_e('gdpr cookie border color', 'gdpr-cookie-consent'); ?></label>
										<c-input class="gdpr-color-select" id="gdpr-cookie-border-color" type="color" name="gdpr-cookie-border-color" v-model="cookie_border_color"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="cookie_bar_border_radius" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<label for="gdpr-cookie-bar-border-radius" class="screen-reader-text"><?php esc_attr_e('gdpr cookie bar border radius', 'gdpr-cookie-consent'); ?></label>
										<c-input id="gdpr-cookie-bar-border-radius" class="gdpr-slider-input" type="number" name="gdpr-cookie-bar-border-radius" v-model="cookie_bar_border_radius"></c-input>
										</c-col>
									</c-row>
									<?php
									$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ? GDPR_COOKIE_CONSENT_VERSION : '';
									if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
										if ( ! $is_pro_active ) :
											?>
											<c-row>
												<c-col class="col-sm-4"><label><?php esc_attr_e( 'Font', 'gdpr-cookie-consent' ); ?></label>
													<div class="gdpr-pro-label"><div class="gdpr-pro-label-text">Pro</div></div>
												</c-col>
												<c-col class="col-sm-8">
													<v-select disabled class="form-group" id="gdpr-cookie-font" :reduce="label => label.code" :options="font_options" v-model="cookie_font">
													</v-select>
													<input type="hidden" name="gdpr-cookie-font" v-model="cookie_font">
												</c-col>
											</c-row>
										<?php endif ?>
										<?php
										do_action( 'gdpr_cookie_font' );
									} else {
										?>
											<c-row>
												<c-col class="col-sm-4"><label><?php esc_attr_e( 'Font', 'gdpr-cookie-consent' ); ?></label></c-col>
												<c-col class="col-sm-8">
													<v-select class="form-group" id="gdpr-cookie-font" :reduce="label => label.code" :options="font_options" v-model="cookie_font">
													</v-select>
													<input type="hidden" name="gdpr-cookie-font" v-model="cookie_font">
												</c-col>
											</c-row>
										<?php } ?>
										<c-row>
											<c-col class="col-sm-4">
												<label><?php esc_attr_e( 'Upload Logo ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'To preview the logo, simply upload a logo and then click the "Save Changes" button ', 'gdpr-cookie-consent' ); ?>"></tooltip><a href="https://wplegalpages.com/pricing?utm_source=wp_cookie_consent&utm_medium=upload_logo&utm_campaign=plugin_upgrade" style="margin-left:5px;"  class="probadge bg-badge"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 640" fill="#f5af2f"> <path d="M345 151.2C354.2 143.9 360 132.6 360 120C360 97.9 342.1 80 320 80C297.9 80 280 97.9 280 120C280 132.6 285.9 143.9 295 151.2L226.6 258.8C216.6 274.5 195.3 278.4 180.4 267.2L120.9 222.7C125.4 216.3 128 208.4 128 200C128 177.9 110.1 160 88 160C65.9 160 48 177.9 48 200C48 221.8 65.5 239.6 87.2 240L119.8 457.5C124.5 488.8 151.4 512 183.1 512L456.9 512C488.6 512 515.5 488.8 520.2 457.5L552.8 240C574.5 239.6 592 221.8 592 200C592 177.9 574.1 160 552 160C529.9 160 512 177.9 512 200C512 208.4 514.6 216.3 519.1 222.7L459.7 267.3C444.8 278.5 423.5 274.6 413.5 258.9L345 151.2z"/><path d="M180 550H460" fill="none" stroke="#f5af2f" stroke-width="28" stroke-linecap="round"/></svg></a></label>
											</c-col>
											<c-col class="col-sm-8 ">
											<span><?php echo esc_attr_e("The same logo will be used for both laws.", 'gdpr-cookie-consent');  ?></span>
											</c-col>
										</c-row>
									</c-card-body>
								</c-card>
						<c-card class=" desgin_card" v-show="!ab_testing_enabled && gdpr_policy == 'both'">
						<c-card-body v-show="active_default_multiple_legislation === 'ccpa'">

									<c-card  v-show="is_us_state_laws">
								<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Save Prefernces Button', 'gdpr-cookie-consent' ); ?></c-card-header>
								<c-card-body>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Save Prefernces Button Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<c-button class="gdpr-configure-button" @click="confirm_button_popup=true">
												<span>
													<img class="gdpr-configure-image" :src="configure_image_url.default" alt="WPCS Configure Logo icon">
													<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
												</span>
											</c-button>
										</c-col>
									</c-row>
									<div class="opt-out-link-container">
									<c-modal
										title="Save Prefernces Button"
										:show.sync="confirm_button_popup"
										size="lg"
										:close-on-backdrop="closeOnBackdrop"
										:centered="centered"
									>
									<div class="optout-settings-tittle-bar">
											<div class="optout-setting-tittle"><?php esc_attr_e( 'Save Prefernces Button', 'gdpr-cookie-consent' ); ?></div>
											<img @click="confirm_button_popup=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
											</div>
										<c-row class="gdpr-label-row">
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
										</c-row>
										<c-row>
											<c-col class="col-sm-6">
												<label for="button_confirm_text_field" class="screen-reader-text"><?php esc_attr_e('button confirm text field', 'gdpr-cookie-consent'); ?></label>
												<c-input id="button_confirm_text_field" name="button_confirm_text_field" v-model="confirm_text"></c-input>
											</c-col>
											<c-col class="col-sm-6 gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="confirm_text_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
												<label for="gdpr-cookie-confirm-text-color" class="screen-reader-text"><?php esc_attr_e('gdpr cookie confirm text color', 'gdpr-cookie-consent'); ?></label>
												<c-input class="gdpr-color-select" id="gdpr-cookie-confirm-text-color" type="color" name="gdpr-cookie-confirm-text-color" v-model="confirm_text_color"></c-input>
											</c-col>
										</c-row>
										<c-row class="gdpr-label-row">
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											
										</c-row>
										<c-row>
											<c-col class="col-sm-6 gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="confirm_background_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
												<label for="gdpr-cookie-confirm-background-color" class="screen-reader-text"><?php esc_attr_e('gdpr cookie confirm background color', 'gdpr-cookie-consent'); ?></label>
												<c-input class="gdpr-color-select" id="gdpr-cookie-confirm-background-color" type="color" name="gdpr-cookie-confirm-background-color" v-model="confirm_background_color"></c-input>
											</c-col>
											
										</c-row>
										<c-row class="gdpr-label-row">
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
										</c-row>
										<c-row>
											<c-col class="col-sm-6">
												<v-select class="form-group" id="gdpr-cookie-confirm-border-style" :reduce="label => label.code" :options="border_style_options" v-model="confirm_style">
												</v-select>
												<input type="hidden" name="gdpr-cookie-confirm-border-style" v-model="confirm_style">
											</c-col>
											<c-col class="col-sm-6 gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="confirm_border_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
												<label for="gdpr-cookie-confirm-border-color" class="screen-reader-text"><?php esc_attr_e('gdpr cookie confirm border color', 'gdpr-cookie-consent'); ?></label>
												<c-input class="gdpr-color-select" id="gdpr-cookie-confirm-border-color" type="color" name="gdpr-cookie-confirm-border-color" v-model="confirm_border_color"></c-input>
											</c-col>
										</c-row>
										<c-row class="gdpr-label-row">
											<c-col class="col-sm-4">
												<label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-4">
												<label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-4">
												<label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
										</c-row>
										<c-row>
											<c-col class="col-sm-4 gdpr-color-pick">
 												<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="confirm_opacity" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
												<label for="gdpr-cookie-confirm-opacity" class="screen-reader-text"><?php esc_attr_e('gdpr cookie confirm opacity', 'gdpr-cookie-consent'); ?></label>
												<c-input id="gdpr-cookie-confirm-opacity" class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-confirm-opacity" v-model="confirm_opacity"></c-input>
											</c-col>
											<c-col class="col-sm-4 gdpr-color-pick">
												<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="confirm_border_width" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
												<label for="gdpr-cookie-confirm-border-width" class="screen-reader-text"><?php esc_attr_e('gdpr cookie confirm border width', 'gdpr-cookie-consent'); ?></label>
												<c-input id="gdpr-cookie-confirm-border-width" class="gdpr-slider-input"type="number" name="gdpr-cookie-confirm-border-width" v-model="confirm_border_width"></c-input>
											</c-col>
											<c-col class="col-sm-4 gdpr-color-pick">
												<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="confirm_border_radius" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
												<label for="gdpr-cookie-confirm-border-radius" class="screen-reader-text"><?php esc_attr_e('gdpr cookie confirm border radius', 'gdpr-cookie-consent'); ?></label>
												<c-input id="gdpr-cookie-confirm-border-radius" class="gdpr-slider-input"type="number" name="gdpr-cookie-confirm-border-radius" v-model="confirm_border_radius"></c-input>
											</c-col>
										</c-row>
												<button class="done-button-settings" @click="confirm_button_popup=false"><span>Done</span></button>
											
									</c-modal>
									</div>
								</c-card-body>
							</c-card>
							<c-card v-show="is_us_state_laws">
								<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Cancel Button', 'gdpr-cookie-consent' ); ?></c-card-header>
								<c-card-body>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cancel Button Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<c-button class="gdpr-configure-button" @click="cancel_button_popup=true">
												<span>
													<img class="gdpr-configure-image" :src="configure_image_url.default" alt="WPCS Configure Logo icon">
													<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
												</span>
											</c-button>
										</c-col>
									</c-row>
									<div class="opt-out-link-container">
									<c-modal
										title="Cancel Button"
										:show.sync="cancel_button_popup"
										size="lg"
										:close-on-backdrop="closeOnBackdrop"
										:centered="centered"
									>
									<div class="optout-settings-tittle-bar">
											<div class="optout-setting-tittle"><?php esc_attr_e( 'Cancel Button', 'gdpr-cookie-consent' ); ?></div>
											<img @click="cancel_button_popup=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
											</div>
										<c-row class="gdpr-label-row">
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
										</c-row>
										<c-row>
											<c-col class="col-sm-6">
												<label for="button_cancel_text_field" class="screen-reader-text"><?php esc_attr_e('button cancel text field', 'gdpr-cookie-consent'); ?></label>
												<c-input id="button_cancel_text_field" name="button_cancel_text_field" v-model="cancel_text"></c-input>
											</c-col>
											<c-col class="col-sm-6 gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="cancel_text_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
												<label for="gdpr-cookie-cancel-text-color" class="screen-reader-text"><?php esc_attr_e('gdpr cookie cancel text color', 'gdpr-cookie-consent'); ?></label>
												<c-input class="gdpr-color-select" id="gdpr-cookie-cancel-text-color" type="color" name="gdpr-cookie-cancel-text-color" v-model="cancel_text_color"></c-input>
											</c-col>
										</c-row>
										<c-row class="gdpr-label-row">
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											
										</c-row>
										<c-row>
											<c-col class="col-sm-6 gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="cancel_background_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
												<label for="gdpr-cookie-cancel-background-color" class="screen-reader-text"><?php esc_attr_e('gdpr cookie cancel background color', 'gdpr-cookie-consent'); ?></label>
												<c-input class="gdpr-color-select" id="gdpr-cookie-cancel-background-color" type="color" name="gdpr-cookie-cancel-background-color" v-model="cancel_background_color"></c-input>
											</c-col>
											
										</c-row>
										<c-row class="gdpr-label-row">
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
										</c-row>
										<c-row>
											<c-col class="col-sm-6">
												<v-select class="form-group" id="gdpr-cookie-cancel-border-style" :reduce="label => label.code" :options="border_style_options" v-model="cancel_style1">
												</v-select>
												<input type="hidden" name="gdpr-cookie-cancel-border-style" v-model="cancel_style">
											</c-col>
											<c-col class="col-sm-6 gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="cancel_border_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
												<label for="gdpr-cookie-cancel-border-color" class="screen-reader-text"><?php esc_attr_e('gdpr cookie cancel border color', 'gdpr-cookie-consent'); ?></label>
												<c-input class="gdpr-color-select" id="gdpr-cookie-cancel-border-color" type="color" name="gdpr-cookie-cancel-border-color" v-model="cancel_border_color"></c-input>
											</c-col>
										</c-row>
										<c-row class="gdpr-label-row">
											<c-col class="col-sm-4">
												<label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-4">
												<label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-4">
												<label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
										</c-row>
										<c-row>
											<c-col class="col-sm-4 gdpr-color-pick">
 												<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="cancel_opacity" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
												<label for="gdpr-cookie-cancel-opacity" class="screen-reader-text"><?php esc_attr_e('gdpr cookie cancel opacity', 'gdpr-cookie-consent'); ?></label>
												<c-input id="gdpr-cookie-cancel-opacity" class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1"  name="gdpr-cookie-cancel-opacity" v-model="cancel_opacity"></c-input>
											</c-col>
											<c-col class="col-sm-4 gdpr-color-pick">
												<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="cancel_border_width" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
												<label for="gdpr-cookie-cancel-border-width" class="screen-reader-text"><?php esc_attr_e('gdpr cookie cancel border width', 'gdpr-cookie-consent'); ?></label>
												<c-input id="gdpr-cookie-cancel-border-width" class="gdpr-slider-input"type="number" name="gdpr-cookie-cancel-border-width" v-model="cancel_border_width"></c-input>
											</c-col>
											<c-col class="col-sm-4 gdpr-color-pick">
												<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="cancel_border_radius" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
												<label for="gdpr-cookie-cancel-border-radius" class="screen-reader-text"><?php esc_attr_e('gdpr cookie cancel border radius', 'gdpr-cookie-consent'); ?></label>
												<c-input id="gdpr-cookie-cancel-border-radius" class="gdpr-slider-input"type="number" name="gdpr-cookie-cancel-border-radius" v-model="cancel_border_radius"></c-input>
											</c-col>
										</c-row>
												<button class="done-button-settings" @click="cancel_button_popup=false"><span>Done</span></button>
											
									</c-modal>
									</div>
								</c-card-body>
							</c-card>
							<c-card  v-show="is_us_state_laws">
								<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Opt-out Link', 'gdpr-cookie-consent' ); ?></c-card-header>
								<c-card-body>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Opt-out Link Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<c-button class="gdpr-configure-button" @click="opt_out_link_popup=true">
												<span>
													<img class="gdpr-configure-image" :src="configure_image_url.default" alt="WPCS Configure Logo icon">
													<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
												</span>
											</c-button>
										</c-col>
									</c-row>
									<div class="opt-out-link-container">
									<c-modal
										title="Opt-out Link"
										:show.sync="opt_out_link_popup"
										size="lg"
										:close-on-backdrop="closeOnBackdrop"
										:centered="centered"
									>
									<div class="optout-settings-tittle-bar">
											<div class="optout-setting-tittle"><?php esc_attr_e( 'Opt Out Link', 'gdpr-cookie-consent' ); ?></div>
											<img @click="opt_out_link_popup=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
											</div>
										<c-row class="gdpr-label-row">
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
										</c-row>
										<c-row>
											<c-col class="col-sm-6">
												<label for="button_donotsell_text_field" class="screen-reader-text"><?php esc_attr_e('button donotsell text field', 'gdpr-cookie-consent'); ?></label>
												<c-input id="button_donotsell_text_field" name="button_donotsell_text_field" v-model="opt_out_text"></c-input>
											</c-col>
											<c-col class="col-sm-6 gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="opt_out_text_color" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
												<label for="gdpr-cookie-opt-out-text-color" class="screen-reader-text"><?php esc_attr_e('gdpr cookie opt out text color', 'gdpr-cookie-consent'); ?></label>
												<c-input class="gdpr-color-select" id="gdpr-cookie-opt-out-text-color" type="color" name="gdpr-cookie-opt-out-text-color" v-model="opt_out_text_color"></c-input>
											</c-col>
										</c-row>
												<button class="done-button-settings" @click="opt_out_link_popup=false"><span>Done</span></button>

									</c-modal>
									</div>
								</c-card-body>
							</c-card>
								</c-card-body>
								
										
							</c-card>
					
			<!-- Desgin Banner preview if A/B Testing is enabled  and GDPR&CCPA both are not selected-->
				<div class="gdpr-design-tab-layout" :class="{ 'panel-open': cookie_bar_settings_open1 }">
					<div class="gdpr-design-left-column">
					<c-card  v-show="ab_testing_enabled && gdpr_policy!='both'" class="design_card">
					<c-card-body>
						<!-- NEWLY ADDED -->
						<!-- Message Heading -->
						<c-row>
							<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Banner Settings', 'gdpr-cookie-consent' ); ?></div></c-col>
						</c-row>
						
						<c-row v-show="is_auto_mode">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Select a privacy law', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'Select the privacy law whose banner message you want to customize.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<v-select class="form-group" id="gdpr-cookie-bar-edit-law" :reduce="label => label.code" :options="policy_options" :selectable="option => isOptionSelectable(option)" label="label" v-model="banner_edit_law">
									<template #option="option">
										<span class="gcc-flag">{{ option.flag }}</span> {{ option.label }}
										<span v-if="isProOnlyLaw(option.code) && disabled_for_free" class="probadge bg-badge"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 640" fill="#f5af2f"> <path d="M345 151.2C354.2 143.9 360 132.6 360 120C360 97.9 342.1 80 320 80C297.9 80 280 97.9 280 120C280 132.6 285.9 143.9 295 151.2L226.6 258.8C216.6 274.5 195.3 278.4 180.4 267.2L120.9 222.7C125.4 216.3 128 208.4 128 200C128 177.9 110.1 160 88 160C65.9 160 48 177.9 48 200C48 221.8 65.5 239.6 87.2 240L119.8 457.5C124.5 488.8 151.4 512 183.1 512L456.9 512C488.6 512 515.5 488.8 520.2 457.5L552.8 240C574.5 239.6 592 221.8 592 200C592 177.9 574.1 160 552 160C529.9 160 512 177.9 512 200C512 208.4 514.6 216.3 519.1 222.7L459.7 267.3C444.8 278.5 423.5 274.6 413.5 258.9L345 151.2z"/><path d="M180 550H460" fill="none" stroke="#f5af2f" stroke-width="28" stroke-linecap="round"/></svg></span>
									</template>
									<template #selected-option="option">
										<span class="gcc-flag">{{ option.flag }}</span> {{ option.label }}
									</template>
								</v-select>
								<input type="hidden" name="gdpr-cookie-banner-edit-law" v-model="banner_edit_law">
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_us_state_laws) || (is_auto_mode && banner_edit_law === 'us_state_laws')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Select a banner type', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'Choose which type of opt-out banner you want to customize.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<v-select class="form-group" id="gdpr-cookie-bar-edit-law" :reduce="label => label.code" :options="us_policy_options" v-model="us_state_laws_edit_law">
								</v-select>
								<input type="hidden" name="gdpr-cookie-us-state-laws-edit-law" v-model="us_state_laws_edit_law">
							</c-col>
						</c-row>
						<c-row >
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Message Heading', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Leave it blank, If you do not need a heading.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="bar_heading_text_field" v-model="gdpr_message_heading"  @input="onHeadingInput"></c-textarea>
							</c-col>
						</c-row>
						<!-- ePrivacy -->
						<c-row v-show="(!is_auto_mode && is_eprivacy) || (is_auto_mode && banner_edit_law === 'eprivacy')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'ePrivacy Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as ePrivacy notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_eprivacy_field" v-model="eprivacy_message"></c-textarea>
							</c-col>
						</c-row>
						<!-- GDPR, UK-GDPR, PDPL PIPEDA, APP -->
						<c-row v-show="(!is_auto_mode && is_gdpr) || (is_auto_mode && banner_edit_law === 'gdpr')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'GDPR Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_field" v-model="gdpr_message" :readonly="iabtcf_is_on"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_uk_gdpr) || (is_auto_mode && banner_edit_law === 'uk_gdpr')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'UK GDPR Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_uk_gdpr_field" v-model="uk_gdpr_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_sa_pdpl) || (is_auto_mode && banner_edit_law === 'sa_pdpl')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Saudi Arabia PDPL Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_pdpl_field" v-model="pdpl_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_pipeda) || (is_auto_mode && banner_edit_law === 'pipeda')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'PIPEDA Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_pipeda_field" v-model="pipeda_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_au_app) || (is_auto_mode && banner_edit_law === 'au_app')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Australia(APP) Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_app_field" v-model="app_message"></c-textarea>
							</c-col>
						</c-row>

						<c-row v-show="(!is_auto_mode && (is_gdpr || is_au_app || is_pipeda || is_sa_pdpl || is_uk_gdpr)) || (is_auto_mode && (banner_edit_law === 'gdpr' || banner_edit_law === 'uk_gdpr' || banner_edit_law === 'sa_pdpl' || banner_edit_law === 'pipeda' || banner_edit_law === 'au_app'))">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'About Cookies Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Text shown under "About Cookies" section when users click on "Preferences" button.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea :rows="6" name="about_message_field" v-model="gdpr_about_cookie_message" :readonly="iabtcf_is_on"></c-textarea>
							</c-col>
						</c-row>

						<!-- US State Laws -->
						<c-row v-show="(!is_auto_mode && is_us_state_laws && us_state_laws_edit_law === 'ccpa') || (is_auto_mode && banner_edit_law === 'us_state_laws' && us_state_laws_edit_law === 'ccpa')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'CCPA Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as CCPA notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_ccpa_field" v-model="ccpa_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_us_state_laws && us_state_laws_edit_law === 'default_opt_out') || (is_auto_mode && banner_edit_law === 'us_state_laws' && us_state_laws_edit_law === 'default_opt_out')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Banner Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as banner notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_ccpa_field" v-model="default_opt_out_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_us_state_laws && us_state_laws_edit_law === 'pure_opt_out') || (is_auto_mode && banner_edit_law === 'us_state_laws' && us_state_laws_edit_law === 'pure_opt_out')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Banner Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as banner notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_ccpa_field" v-model="pure_opt_out_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_us_state_laws) || (is_auto_mode && banner_edit_law === 'us_state_laws')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'CCPA Opt-out Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as CCPA notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_ccpa_optout_field" v-model="ccpa_optout_message"></c-textarea>
							</c-col>
						</c-row>
						
						<!-- LGPD -->
						<c-row v-show="(!is_auto_mode && is_lgpd) || (is_auto_mode && banner_edit_law === 'lgpd')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'LGPD Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_lgpd_field" v-model="lgpd_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="(!is_auto_mode && is_lgpd) || (is_auto_mode && banner_edit_law === 'lgpd')">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'About Cookies Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Text shown under "About Cookies" section when users click on "Preferences" button.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea :rows="6" name="about_message_lgpd_field" v-model="lgpd_about_cookie_message"></c-textarea>
							</c-col>
						</c-row>
						<div style="display: flex; justify-content: flex-end; margin-top: 10px;">
							<p class="design-settings-text"><?php esc_html_e("Design Settings", "gdpr-cookie-banner")?></p>
						<c-button class="gdpr-cookie-bar-settings-icon" :style="(cookie_bar_settings_open1) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}"   @click="openConfigurationPanel('cookie_bar_settings_open1')">
							<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g clip-path="url(#clip0_4634_794)">
								<path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/>
								</g>
								<defs>
								<clipPath id="clip0_4634_794">
								<rect width="30" height="30" fill="white" transform="translate(10 10)"/>
								</clipPath>
								</defs>
							</svg>
						</c-button>
									</div>
					</c-card-body>
				</c-card>
				
				<c-card  v-show="ab_testing_enabled  && gdpr_policy != 'both'  && active_test_banner_tab === 1"class=" desgin_card">
							<c-card-body>
								<c-row>
									<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Button Settings', 'gdpr-cookie-consent' ); ?></div></c-col>
								</c-row>	
								<!-- Privacy Policy Settings -->
								<c-row v-show="show_revoke_card || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl">
									<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Privacy Policy Settings', 'gdpr-cookie-consent' ); ?></div></c-col>
								</c-row>
								<c-row class="privacy-policy-row" v-show="show_revoke_card || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Privacy Policy Link', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enable this to provide a link to your Privacy & Cookie Policy on your Cookie Notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-1">
										<c-switch v-bind="labelIcon" v-model="button_readmore_is_on1" id="gdpr-cookie-consent-readmore-is-on" variant="3d"  color="success" :checked="button_readmore_is_on1" v-on:update:checked="onSwitchButtonReadMoreIsOn1"></c-switch>
										<input type="hidden" name="gcc-readmore-is-on1" v-model="button_readmore_is_on1">
									</c-col>
									<c-col class="col-sm-6">
										<c-input
											:disabled = "!button_readmore_is_on1"
											name="button_readmore_text_field1"
											v-model="button_readmore_text1"
										></c-input>
									</c-col>
									<c-col class="col-sm-1">
											<c-button :disabled="!button_readmore_is_on1" class="gdpr-configure-button" :style="(button_readmore_popup2 || button_readmore_popup1) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('button_readmore_popup1')">
										<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

											</c-button>
										</c-col>
								</c-row>
							</c-card-body>

							<c-card-body>
								<!-- Revoke Consent settings -->
								<c-row v-show="show_revoke_card || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl">
									<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Revoke Consent', 'gdpr-cookie-consent' ); ?></div></c-col>
								</c-row>
								<c-row class="privacy-policy-row" v-show="show_revoke_card || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Revoke Consent', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enable to give user the option to revoke their consent.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-1">
										<c-switch v-bind="labelIcon" v-model="is_revoke_consent_on1" id="gdpr-cookie-consent-revoke-consent" variant="3d"  color="success" :checked="is_revoke_consent_on1" v-on:update:checked="onSwitchRevokeConsentEnable1"></c-switch>
										<input type="hidden" name="gcc-revoke-consent-enable1" v-model="is_revoke_consent_on1">
									</c-col>
									
									<c-col class="col-sm-6">
										<c-input
											:disabled = "!is_revoke_consent_on1"
											name="show_again_text_field1"
											v-model="tab_text1"
										></c-input>
									</c-col>
									<c-col class="col-sm-1">
										<c-button :disabled="!is_revoke_consent_on1" class="gdpr-configure-button" :style="(revoke_consent_popup2 || revoke_consent_popup1) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('revoke_consent_popup1')">
										<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

										</c-button>
									</c-col>
								</c-row>

					</c-card-body>
							
					<c-card v-show="is_gdpr || is_eprivacy || is_lgpd">
						<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Accept Button', 'gdpr-cookie-consent' ); ?></c-card-header>
						<c-card-body>
							<c-row class="privacy-policy-row">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-1">
									<c-switch v-bind="labelIcon" v-model="cookie_accept_on1" id="gdpr-cookie-consent-cookie1" variant="3d"  color="success" :checked="cookie_accept_on1" v-on:update:checked="onSwitchCookieAcceptEnable1"></c-switch>
									<input type="hidden" name="gcc-cookie-accept-enable1" v-model="cookie_accept_on1">
								</c-col>
								<c-col class="col-sm-6">
									<label
										for="button_accept_text_fieldvar1"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'button_accept_text_field1', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										:disabled = "!cookie_accept_on1"
										id="button_accept_text_fieldvar1"
										name="button_accept_text_field1"
										v-model="accept_text1"
									></c-input>
								</c-col>
								<c-col class="col-sm-1">
									<c-button :disabled="!cookie_accept_on1" class="gdpr-configure-button" :style="(accept_button_popup2 || accept_button_popup1) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('accept_button_popup1')">
										<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

									</c-button>
								</c-col>
							</c-row>
					</c-card-body>
					</c-card>
					<c-card v-show="is_gdpr || is_eprivacy || is_lgpd">
						<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Accept All Button', 'gdpr-cookie-consent' ); ?></c-card-header>
						<c-card-body>
							<c-row  class="privacy-policy-row">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-1">
									<c-switch v-bind="labelIcon" v-model="cookie_accept_all_on1" id="gdpr-cookie-consent-cookie-acceptall-on1" variant="3d"  color="success" :checked="cookie_accept_all_on1" v-on:update:checked="onSwitchCookieAcceptAllEnable1"></c-switch>
									<input type="hidden" name="gcc-cookie-accept-all-enable1" v-model="cookie_accept_all_on1">
								</c-col>
								<c-col class="col-sm-6">
									<c-input
										:disabled = "!cookie_accept_all_on1"
										name="button_accept_all_text_field1"
										v-model="accept_all_text1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
								<c-col class="col-sm-1">
									<c-button :disabled="!cookie_accept_all_on1" class="gdpr-configure-button" :style="(accept_all_button_popup2 || accept_all_button_popup1) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('accept_all_button_popup1')">
										<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

									</c-button>
								</c-col>
							</c-row>
						</c-card-body>
					</c-card>
					<c-card v-show="is_gdpr || is_eprivacy || is_lgpd">
						<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Reject All Button', 'gdpr-cookie-consent' ); ?></c-card-header>
						<c-card-body>
							<c-row  class="privacy-policy-row">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-1">
									<c-switch v-bind="labelIcon" v-model="cookie_decline_on1" id="gdpr-cookie-consent-decline-on1" variant="3d"  color="success" :checked="cookie_decline_on1" v-on:update:checked="onSwitchCookieDeclineEnable1"></c-switch>
									<input type="hidden" name="gcc-cookie-decline-enable1" v-model="cookie_decline_on1">
								</c-col>
								<c-col class="col-sm-6">
									<c-input
										:disabled = "!cookie_decline_on1"
										name="button_decline_text_field1"
										v-model="decline_text1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
								<c-col class="col-sm-1">
									<c-button :disabled="!cookie_decline_on1" class="gdpr-configure-button" :style="(decline_button_popup2 || decline_button_popup1) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('decline_button_popup1')">
										<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

									</c-button>
								</c-col>
							</c-row>
						</c-card-body>
					</c-card>
					<c-card v-show="is_gdpr || is_lgpd">
						<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Preferences Button', 'gdpr-cookie-consent' ); ?></c-card-header>
						<c-card-body>
							<c-row class="privacy-policy-row">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-1">
									<c-switch v-bind="labelIcon" v-model="cookie_settings_on1" id="gdpr-cookie-consent-settings-on1" variant="3d"  color="success" :checked="cookie_settings_on1" v-on:update:checked="onSwitchCookieSettingsEnable1"></c-switch>
									<input type="hidden" name="gcc-cookie-settings-enable1" v-model="cookie_settings_on1">
								</c-col>
								<c-col class="col-sm-6">
									<c-input
										:disabled = "!cookie_settings_on1"
										name="button_settings_text_field1"
										v-model="settings_text1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
								<c-col class="col-sm-1">
									<c-button :disabled="!cookie_settings_on1" class="gdpr-configure-button" :style="(settings_button_popup1 || settings_button_popup2) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('settings_button_popup1')">
										<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

									</c-button>
								</c-col>
								<c-col class="col-sm-4">
										<label><?php esc_attr_e( 'Display Cookies List on Frontend', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-4">
										<c-switch v-bind="labelIcon" v-model="cookie_on_frontend1" id="gdpr-cookie-consent-cookie-on-frontend1" variant="3d"  color="success" :checked="cookie_on_frontend1" v-on:update:checked="onSwitchCookieOnFrontend1" :disabled="!cookie_settings_on1"></c-switch>
										<input type="hidden" name="gcc-cookie-on-frontend1" v-model="cookie_on_frontend1">
									</c-col>
									<c-col class="col-sm-4">
										<?php do_action( 'gdpr_cookie_layout_skin_label' ); ?>
									</c-col>
									<c-col class="col-sm-4">
										<?php do_action( 'gdpr_cookie_layout_skin_markup' ); ?>
									</c-col>

							</c-row>
						</c-card-body>
					</c-card>
					<c-card  v-show="is_auto_mode || is_us_state_laws">
						<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Save Prefernces Button', 'gdpr-cookie-consent' ); ?></c-card-header>
						<c-card-body>
							<c-row class="privacy-policy-row">
								<c-col class="col-sm-5"><label><?php esc_attr_e( 'Save Prefernces Button Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-6">
										<c-input
											name="button_confirm_text_field1"
											v-model="confirm_text1"
											aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
										></c-input>
									</c-col>
								<c-col class="col-sm-1">
									<c-button class="gdpr-configure-button" :style="(confirm_button_popup1 || confirm_button_popup2) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('confirm_button_popup1')">
										<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

									</c-button>
								</c-col>
							</c-row>
						</c-card-body>
					</c-card>
					<c-card v-show="is_auto_mode || is_us_state_laws">
						<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Cancel Button', 'gdpr-cookie-consent' ); ?></c-card-header>
						<c-card-body>
							<c-row  class="privacy-policy-row">
								<c-col class="col-sm-5"><label><?php esc_attr_e( 'Cancel Button Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-6">
									<c-input
										name="button_cancel_text_field1"
										v-model="cancel_text1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
								<c-col class="col-sm-1">
									<c-button class="gdpr-configure-button" :style="(cancel_button_popup1 || cancel_button_popup2) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('cancel_button_popup1')">
										<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

									</c-button>
								</c-col>
							</c-row>
						</c-card-body>
					</c-card>
					<c-card  v-show="is_auto_mode || is_us_state_laws">
						<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Opt-out Link', 'gdpr-cookie-consent' ); ?></c-card-header>
						<c-card-body>
							<c-row  class="privacy-policy-row">
								<c-col class="col-sm-5"><label><?php esc_attr_e( 'Opt-out Link Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-6">
									<c-input
										name="button_donotsell_text_field1"
										v-model="opt_out_text1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
								<c-col class="col-sm-1">
									<c-button class="gdpr-configure-button" :style="(opt_out_link_popup1 || opt_out_link_popup2) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}"  @click="openConfigurationPanel('opt_out_link_popup1')">
										<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

									</c-button>
								</c-col>
							</c-row>
						</c-card-body>
					</c-card>
						
					<!-- Revoke Consent settings for CCPA -->
					<c-card v-show="is_auto_mode || is_us_state_laws">
						<c-card-header class="gdpr-cookie-consent-design-subheading">
							<?php esc_html_e( 'Revoke Consent', 'gdpr-cookie-consent' ); ?>
						</c-card-header>
						<c-card-body>
						<c-row class="privacy-policy-row">
							<c-col class="col-sm-4">
								<label>
									<?php esc_attr_e( 'Enable Revoke Consent', 'gdpr-cookie-consent' ); ?>
									<tooltip text="<?php esc_html_e( 'Enable to give user the option to revoke their consent.', 'gdpr-cookie-consent' ); ?>"></tooltip>
								</label>
							</c-col>
							<c-col class="col-sm-1">
								<c-switch 
									v-bind="labelIcon" 
									v-model="is_ccpa_revoke_consent_on1" 
									id="ccpa-cookie-consent-revoke-consent1" 
									variant="3d" 
									color="success" 
									:checked="is_ccpa_revoke_consent_on1" 
									v-on:update:checked="onSwitchCcpaRevokeConsentEnable1">
								</c-switch>
								<input type="hidden" name="gcc-ccpa-revoke-consent-enable1" v-model="is_ccpa_revoke_consent_on1">
							</c-col>
							<c-col class="col-sm-6">
								<c-input
									:disabled="!is_ccpa_revoke_consent_on1"
									name="ccpa_show_again_text_field1"
									v-model="ccpa_tab_text1"
								></c-input>
							</c-col>
							<c-col class="col-sm-1">
								<c-button :disabled="!is_ccpa_revoke_consent_on1" class="gdpr-configure-button" :style="(ccpa_revoke_consent_popup2 || ccpa_revoke_consent_popup1) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('ccpa_revoke_consent_popup1')">
										<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

								</c-button>
							</c-col>
						</c-row>

					</c-card-body>
				
				</c-card>
			</c-card>
			<c-card v-show="ab_testing_enabled  && gdpr_policy != 'both'  && active_test_banner_tab === 2"class=" desgin_card">
				<c-card-body>
								<c-row>
									<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Button Settings', 'gdpr-cookie-consent' ); ?></div></c-col>
								</c-row>	
								<!-- Privacy Policy Settings -->
								<c-row v-show="show_revoke_card || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl">
									<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Privacy Policy Settings', 'gdpr-cookie-consent' ); ?></div></c-col>
								</c-row>
								<c-row class="privacy-policy-row" v-show="show_revoke_card || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Privacy Policy Link', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enable this to provide a link to your Privacy & Cookie Policy on your Cookie Notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-1">
										<c-switch v-bind="labelIcon" v-model="button_readmore_is_on2" id="gdpr-cookie-consent-readmore-is-on" variant="3d"  color="success" :checked="button_readmore_is_on2" v-on:update:checked="onSwitchButtonReadMoreIsOn2"></c-switch>
										<input type="hidden" name="gcc-readmore-is-on2" v-model="button_readmore_is_on2">
									</c-col>
									<c-col class="col-sm-6">
										<c-input
											:disabled="!button_readmore_is_on2"
											name="button_readmore_text_field2"
											v-model="button_readmore_text2"
										></c-input>
									</c-col>
									<c-col class="col-sm-1">
											<c-button :disabled="!button_readmore_is_on2" class="gdpr-configure-button" :style="(button_readmore_popup1 || button_readmore_popup2) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('button_readmore_popup2')">
												<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

											</c-button>
										</c-col>
								</c-row>
							</c-card-body>

							<c-card-body>
								<!-- Revoke Consent settings -->
								<c-row v-show="show_revoke_card || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl">
									<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Revoke Consent', 'gdpr-cookie-consent' ); ?></div></c-col>
								</c-row>
								<c-row class="privacy-policy-row" v-show="show_revoke_card || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Revoke Consent', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enable to give user the option to revoke their consent.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-1">
										<c-switch v-bind="labelIcon" v-model="is_revoke_consent_on2" id="gdpr-cookie-consent-revoke-consent" variant="3d"  color="success" :checked="is_revoke_consent_on2" v-on:update:checked="onSwitchRevokeConsentEnable2"></c-switch>
										<input type="hidden" name="gcc-revoke-consent-enable2" v-model="is_revoke_consent_on2">
									</c-col>
									<c-col class="col-sm-6">
										<c-input
											:disabled="!is_revoke_consent_on2"
											name="show_again_text_field2"
											v-model="tab_text2"
										></c-input>
									</c-col>
									<c-col class="col-sm-1">
										<c-button :disabled="!is_revoke_consent_on2" class="gdpr-configure-button" :style="(revoke_consent_popup2 || revoke_consent_popup1) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('revoke_consent_popup2')">
									<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

										</c-button>
									</c-col>
								</c-row>
											
							</c-card-body>

									<c-card v-show="is_gdpr || is_eprivacy || is_lgpd">
					<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Accept Button', 'gdpr-cookie-consent' ); ?></c-card-header>
					<c-card-body>
						<c-row class="privacy-policy-row">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
							<c-col class="col-sm-1">
								<c-switch v-bind="labelIcon" v-model="cookie_accept_on2" id="gdpr-cookie-consent-cookie2" variant="3d"  color="success" :checked="cookie_accept_on2" v-on:update:checked="onSwitchCookieAcceptEnable2"></c-switch>
								<input type="hidden" name="gcc-cookie-accept-enable2" v-model="cookie_accept_on2">
							</c-col>

							<c-col class="col-sm-6">
								<c-input
									:disabled="!cookie_accept_on2"
									name="button_accept_text_field2"
									v-model="accept_text2"
									aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
								></c-input>
							</c-col>
							<c-col class="col-sm-1">
								<c-button :disabled="!cookie_accept_on2" class="gdpr-configure-button" :style="(accept_button_popup2 || accept_button_popup1) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('accept_button_popup2')">
									<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

								</c-button>
							</c-col>
						</c-row>
					</c-card-body>
				</c-card>
				<c-card v-show="is_gdpr || is_eprivacy || is_lgpd">
					<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Accept All Button', 'gdpr-cookie-consent' ); ?></c-card-header>
					<c-card-body>
						<c-row class="privacy-policy-row">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
							<c-col class="col-sm-1">
								<c-switch v-bind="labelIcon" v-model="cookie_accept_all_on2" id="gdpr-cookie-consent-cookie-acceptall-on2" variant="3d"  color="success" :checked="cookie_accept_all_on2" v-on:update:checked="onSwitchCookieAcceptAllEnable2"></c-switch>
								<input type="hidden" name="gcc-cookie-accept-all-enable2" v-model="cookie_accept_all_on2">
							</c-col>
							<c-col class="col-sm-6">
								<c-input
									:disabled="!cookie_accept_all_on2"
									name="button_accept_all_text_field2"
									v-model="accept_all_text2"
									aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
								></c-input>
							</c-col>
							<c-col class="col-sm-1">
								<c-button :disabled="!cookie_accept_all_on2" class="gdpr-configure-button" :style="(accept_all_button_popup2 || accept_all_button_popup1) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('accept_all_button_popup2')">
									<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

								</c-button>
							</c-col>
						</c-row>
					</c-card-body>
				</c-card>
				<c-card v-show="is_gdpr || is_eprivacy || is_lgpd">
					<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Reject All Button', 'gdpr-cookie-consent' ); ?></c-card-header>
					<c-card-body>
						<c-row class="privacy-policy-row">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
							<c-col class="col-sm-1">
								<c-switch v-bind="labelIcon" v-model="cookie_decline_on2" id="gdpr-cookie-consent-decline-on2" variant="3d"  color="success" :checked="cookie_decline_on2" v-on:update:checked="onSwitchCookieDeclineEnable2"></c-switch>
								<input type="hidden" name="gcc-cookie-decline-enable2" v-model="cookie_decline_on2">
							</c-col>
							<c-col class="col-sm-6">
								<label
									for="button_decline_text_field2"
									class="screen-reader-text"
								>
									<?php esc_attr_e( 'button decline text field2', 'gdpr-cookie-consent' ); ?>
								</label>

								<c-input
									id="button_decline_text_field2"
									name="button_decline_text_field2"
									v-model="decline_text2"
									:disabled="!cookie_decline_on2"
								></c-input>
							</c-col>
							<c-col class="col-sm-1">
								<c-button :disabled="!cookie_decline_on2" class="gdpr-configure-button" :style="(decline_button_popup2 || decline_button_popup1) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}"  @click="openConfigurationPanel('decline_button_popup2')">
									<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

								</c-button>
							</c-col>
						</c-row>
					</c-card-body>
				</c-card>
				<c-card v-show="is_gdpr || is_lgpd">
					<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Preferences Button', 'gdpr-cookie-consent' ); ?></c-card-header>
					<c-card-body>
						<c-row class="privacy-policy-row">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
							<c-col class="col-sm-1">
								<c-switch v-bind="labelIcon" v-model="cookie_settings_on2" id="gdpr-cookie-consent-settings-on2" variant="3d"  color="success" :checked="cookie_settings_on2" v-on:update:checked="onSwitchCookieSettingsEnable2"></c-switch>
								<input type="hidden" name="gcc-cookie-settings-enable2" v-model="cookie_settings_on2">
							</c-col>
							<c-col class="col-sm-6">
								<label
									for="button_settings_text_field2"
									class="screen-reader-text"
								>
									<?php esc_attr_e( 'button settings text field2', 'gdpr-cookie-consent' ); ?>
								</label>

								<c-input
									:disabled="!cookie_settings_on2"
									id="button_settings_text_field2"
									name="button_settings_text_field2"
									v-model="settings_text2"
								></c-input>
							</c-col>
							<c-col class="col-sm-1">
								<c-button :disabled="!cookie_settings_on2" class="gdpr-configure-button" :style="(settings_button_popup2 || settings_button_popup1) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('settings_button_popup2')">
									<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

								</c-button>
							</c-col>
							<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Display Cookies List on Frontend', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-4">
									<c-switch v-bind="labelIcon" v-model="cookie_on_frontend2" id="gdpr-cookie-consent-cookie-on-frontend2" variant="3d"  color="success" :checked="cookie_on_frontend2" v-on:update:checked="onSwitchCookieOnFrontend2" :disabled="!cookie_settings_on2"></c-switch>
									<input type="hidden" name="gcc-cookie-on-frontend2" v-model="cookie_on_frontend2">
								</c-col>
								<c-col class="col-sm-4">
									<?php do_action( 'gdpr_cookie_layout_skin_label' ); ?>
								</c-col>
								<c-col class="col-sm-4">
									<?php do_action( 'gdpr_cookie_layout_skin_markup' ); ?>
								</c-col>

								
						</c-row>
					</c-card-body>
				</c-card>
				<c-card  v-show="is_auto_mode || is_us_state_laws">
					<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Save Prefernces Button', 'gdpr-cookie-consent' ); ?></c-card-header>
					<c-card-body>
						<c-row class="privacy-policy-row">
							<c-col class="col-sm-5"><label><?php esc_attr_e( 'Save Prefernces Button Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
							<c-col class="col-sm-6">
								<label
									for="button_confirm_text_field2"
									class="screen-reader-text"
								>
									<?php esc_attr_e( 'button confirm text field2', 'gdpr-cookie-consent' ); ?>
								</label>

								<c-input
									id="button_confirm_text_field2"
									name="button_confirm_text_field2"
									v-model="confirm_text2"
								></c-input>
							</c-col>
							<c-col class="col-sm-1">
								<c-button class="gdpr-configure-button" :style="(confirm_button_popup2 || confirm_button_popup1) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('confirm_button_popup2')">
									<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

								</c-button>
							</c-col>
						</c-row>
					</c-card-body>
				</c-card>
				<c-card v-show="is_auto_mode || is_us_state_laws">
					<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Cancel Button', 'gdpr-cookie-consent' ); ?></c-card-header>
					<c-card-body>
						<c-row class="privacy-policy-row">
							<c-col class="col-sm-5"><label><?php esc_attr_e( 'Cancel Button Settings', 'gdpr-cookie-consent' ); ?></label></c-col>

							<c-col class="col-sm-6">
								<label
									for="button_cancel_text_field2"
									class="screen-reader-text"
								>
									<?php esc_attr_e( 'button cancel text field2', 'gdpr-cookie-consent' ); ?>
								</label>

								<c-input
									id="button_cancel_text_field2"
									name="button_cancel_text_field2"
									v-model="cancel_text2"
								></c-input>
							</c-col>
							<c-col class="col-sm-1">
								<c-button :style="(cancel_button_popup2 || cancel_button_popup1) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" class="gdpr-configure-button" @click="openConfigurationPanel('cancel_button_popup2')">
									<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

								</c-button>
							</c-col>
						</c-row>
					</c-card-body>
				</c-card>
				<c-card  v-show="is_auto_mode || is_us_state_laws">
					<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Opt-out Link', 'gdpr-cookie-consent' ); ?></c-card-header>
					<c-card-body>
						<c-row class="privacy-policy-row">
							<c-col class="col-sm-5"><label><?php esc_attr_e( 'Opt-out Link Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
							<c-col class="col-sm-6">
								<label
									for="button_donotsell_text_field2"
									class="screen-reader-text"
								>
									<?php esc_attr_e( 'button donotsell text field2', 'gdpr-cookie-consent' ); ?>
								</label>

								<c-input
									id="button_donotsell_text_field2"
									name="button_donotsell_text_field2"
									v-model="opt_out_text2"
								></c-input>
							</c-col>
							<c-col class="col-sm-1">
								<c-button class="gdpr-configure-button" :style="(opt_out_link_popup2 || opt_out_link_popup1) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('opt_out_link_popup2')">
									<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>

								</c-button>
							</c-col>
						</c-row>
					</c-card-body>
				</c-card>
				<!-- Revoke Consent settings for CCPA -->
				<c-card v-show="is_auto_mode || is_us_state_laws">
					<c-card-header class="gdpr-cookie-consent-design-subheading">
						<?php esc_html_e( 'Revoke Consent', 'gdpr-cookie-consent' ); ?>
					</c-card-header>
					<c-card-body>
					<c-row class="privacy-policy-row">
						<c-col class="col-sm-4">
							<label>
								<?php esc_attr_e( 'Enable Revoke Consent', 'gdpr-cookie-consent' ); ?>
								<tooltip text="<?php esc_html_e( 'Enable to give user the option to revoke their consent.', 'gdpr-cookie-consent' ); ?>"></tooltip>
							</label>
						</c-col>
						<c-col class="col-sm-1">
							<c-switch 
								v-bind="labelIcon" 
								v-model="is_ccpa_revoke_consent_on2" 
								id="ccpa-cookie-consent-revoke-consent2" 
								variant="3d" 
								color="success" 
								:checked="is_ccpa_revoke_consent_on2" 
								v-on:update:checked="onSwitchCcpaRevokeConsentEnable2">
							</c-switch>
							<input type="hidden" name="gcc-ccpa-revoke-consent-enable2" v-model="is_ccpa_revoke_consent_on2">
						</c-col>
						<c-col class="col-sm-6">
							<c-input
								:disabled ="!is_ccpa_revoke_consent_on2"
								name="ccpa_show_again_text_field2"
								v-model="ccpa_tab_text2"
							></c-input>
						</c-col>
						<c-col class="col-sm-1">
							<c-button :disabled="!is_ccpa_revoke_consent_on2" class="gdpr-configure-button" :style="(ccpa_revoke_consent_popup2 || ccpa_revoke_consent_popup1) ? { backgroundColor: '#EAF4FF !important', color: '#2F80ED !important' } : {}" @click="openConfigurationPanel('ccpa_revoke_consent_popup2')">
									<svg width="40" height="40" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4634_793)"><path d="M14.1462 30.3445C11.8737 32.5765 13.7937 34.8685 10.7757 38.263C9.41368 39.7975 16.3827 39.3295 19.8342 35.9365C21.2997 34.495 20.8857 32.398 19.3152 30.853C17.7447 29.311 15.6102 28.903 14.1462 30.3445ZM39.3102 10.9885C38.1462 9.84253 25.2417 20.182 21.4017 23.9575C19.4952 25.8325 18.8592 26.8375 18.2757 27.5875C18.0207 27.916 18.3582 28.015 18.5067 28.0915C19.2627 28.4785 19.7907 28.8355 20.4732 29.506C21.1572 30.1765 21.5217 30.6955 21.9117 31.4395C21.9912 31.5865 22.0932 31.9165 22.4247 31.6675C23.1897 31.093 24.2112 30.466 26.1177 28.594C29.9592 24.82 40.4772 12.133 39.3102 10.9885Z" fill="currentColor"/></g><defs><clipPath id="clip0_4634_793"><rect width="30" height="30" fill="white" transform="translate(10 10)"/></clipPath></defs></svg>
							</c-button>
						</c-col>
					</c-row>

					</c-card-body>
				</c-card>
			</c-card>

			</div>
			<div class="gdpr-cookie-consent-banner-tabs" v-show="ab_testing_enabled">
				<c-button class="gdpr-cookie-consent-banner-tab"@click="changeActiveTestBannerTabTo1":class="{ 'gdpr-cookie-consent-banner-tab-active': active_test_banner_tab === 1 }"> <?php if ( ! empty( $the_options['cookie_bar1_name'] ) ) {echo esc_html( $the_options['cookie_bar1_name'] );} else {esc_html_e( 'Test Banner A', 'gdpr-cookie-consent' );}?><span v-show="default_cookie_bar === true"><?php esc_html_e( '(default)', 'gdpr-cookie-consent' );  ?></span></c-button>
				<c-button class="gdpr-cookie-consent-banner-tab"@click="changeActiveTestBannerTabTo2":class="{ 'gdpr-cookie-consent-banner-tab-active': active_test_banner_tab === 2 }"><?php if ( ! empty( $the_options['cookie_bar2_name'] ) ) {echo esc_html( $the_options['cookie_bar2_name'] );} else {esc_html_e( 'Test Banner B', 'gdpr-cookie-consent' );}?><span v-show="default_cookie_bar === false"><?php esc_html_e( '(default)', 'gdpr-cookie-consent' ); ?></span></c-button>
			</div>
			<div class="gdpr-design-right-column">
				
				<c-card v-if="cookie_bar_settings_open1" v-show="ab_testing_enabled  && gdpr_policy != 'both' && active_test_banner_tab === 1"class=" desgin_card">
						<c-card-body v-show="active_test_banner_tab === 1">
							<c-row>
								<c-col class="col-sm-8"><div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Cookie Bar Body Design', 'gdpr-cookie-consent' ); ?></div></c-col>
								<c-col class="col-sm-4"><button type="button" class="cookie-bar-settings-close" @click="cookie_bar_settings_open1 = false" aria-label="<?php esc_attr_e( 'Close Cookie Bar Settings', 'gdpr-cookie-consent' ); ?>">
								<svg class="connect-info-close" data-target="connect-info-container" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18" stroke="#3A3A41" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M6 6L18 18" stroke="#3A3A41" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
							</button></c-col>
							</c-row>
							
							<c-card-body >
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Consent Banner Title', 'gdpr-cookie-consent' ); ?> </label></c-col>
									<c-col class="col-sm-8">
										<label for="gdpr-cookie_bar1_name" class="screen-reader-text"><?php esc_attr_e('gdpr cookie bar1 name', 'gdpr-cookie-consent'); ?></label>
										<c-input id="gdpr-cookie_bar1_name" name="gdpr-cookie_bar1_name" v-model="cookie_bar1_name"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Make this banner default', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="default_cookie_bar" id="gdpr-cookie-consent-default_cookie_bar1" variant="3d"  color="success" :checked="default_cookie_bar" v-on:update:checked="onSwitchDefaultCookieBar"></c-switch>
										<input type="hidden" name="gdpr-default_cookie_bar" v-model="default_cookie_bar">
									</c-col> 
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cookie Bar Color', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="cookie_bar_color1" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
									<label for="gdpr-cookie-bar-color1" class="screen-reader-text"><?php esc_attr_e('gdpr-cookie-bar-color1','gdpr-cookie-consent'); ?></label>
									<c-input class="gdpr-color-select" id="gdpr-cookie-bar-color1" type="color" name="gdpr-cookie-bar-color1" v-model="cookie_bar_color1"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( ' Cookie Bar Opacity', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="cookie_bar_opacity1" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
									<label for="gdpr-cookie-bar-opacity1" class="screen-reader-text"><?php esc_attr_e('gdpr-cookie-bar-opacity1','gdpr-cookie-consent'); ?></label>
									<c-input id="gdpr-cookie-bar-opacity1" class="gdpr-slider-input opacity-slider" type="number"  min="0" max="1" step="0.01" name="gdpr-cookie-bar-opacity1" v-model="cookie_bar_opacity1"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="cookie_text_color1" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
									<label for="gdpr-cookie-text-color1" class="screen-reader-text"><?php esc_attr_e('gdpr-cookie-text-color1','gdpr-cookie-consent'); ?></label>
									<c-input class="gdpr-color-select" id="gdpr-cookie-text-color1" type="color" name="gdpr-cookie-text-color1" v-model="cookie_text_color1"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Styles', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-border-style1" :reduce="label => label.code" :options="border_style_options" v-model="border_style1">
										</v-select>
										<input type="hidden" name="gdpr-cookie-border-style1" v-model="border_style1">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="cookie_bar_border_width1" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
									<label for="gdpr-cookie-bar-border-width1" class="screen-reader-text"><?php esc_attr_e('gdpr-cookie-bar-border-width1','gdpr-cookie-consent'); ?></label>
									<c-input id="gdpr-cookie-bar-border-width1" class="gdpr-slider-input"type="number" name="gdpr-cookie-bar-border-width1" v-model="cookie_bar_border_width1"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="cookie_border_color1" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
									<label for="gdpr-cookie-border-color1" class="screen-reader-text"><?php esc_attr_e('gdpr-cookie-border-color1','gdpr-cookie-consent'); ?></label>
									<c-input class="gdpr-color-select" id="gdpr-cookie-border-color1" type="color" name="gdpr-cookie-border-color1" v-model="cookie_border_color1"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="cookie_bar_border_radius1" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
									<label for="gdpr-cookie-bar-border-radius1" class="screen-reader-text"><?php esc_attr_e('gdpr-cookie-bar-border-radius1','gdpr-cookie-consent'); ?></label>
									<c-input id="gdpr-cookie-bar-border-radius1" class="gdpr-slider-input"type="number" name="gdpr-cookie-bar-border-radius1" v-model="cookie_bar_border_radius1"></c-input>
									</c-col>
								</c-row>
								<?php
								$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ? GDPR_COOKIE_CONSENT_VERSION : '';
								if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
									if ( ! $is_pro_active ) :
										?>
										<c-row>
											<c-col class="col-sm-4"><label><?php esc_attr_e( 'Font', 'gdpr-cookie-consent' ); ?></label>
												<div class="gdpr-pro-label"><div class="gdpr-pro-label-text">Pro</div></div>
											</c-col>
											<c-col class="col-sm-8">
												<v-select disabled class="form-group" id="gdpr-cookie-font" :reduce="label => label.code" :options="font_options" v-model="cookie_font1">
												</v-select>
												<input type="hidden" name="gdpr-cookie-font1" v-model="cookie_font1">
											</c-col>
										</c-row>
									<?php endif ?>
									<?php
									do_action( 'gdpr_cookie_font' );
								} else {
									?>
										<c-row>
											<c-col class="col-sm-4"><label><?php esc_attr_e( 'Font', 'gdpr-cookie-consent' ); ?></label></c-col>
											<c-col class="col-sm-8">
												<v-select class="form-group" id="gdpr-cookie-font" :reduce="label => label.code" :options="font_options" v-model="cookie_font1">
												</v-select>
												<input type="hidden" name="gdpr-cookie-font1" v-model="cookie_font1	">
											</c-col>
										</c-row>
									<?php } ?>
							<c-row>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Upload Logo ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'To preview the logo, simply upload a logo and then click the "Save Changes" button ', 'gdpr-cookie-consent' ); ?>"></tooltip> <a href="https://wplegalpages.com/pricing?utm_source=wp_cookie_consent&utm_medium=upload_logo&utm_campaign=plugin_upgrade" style="margin-left:5px;"  class="probadge bg-badge"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 640" fill="#f5af2f"> <path d="M345 151.2C354.2 143.9 360 132.6 360 120C360 97.9 342.1 80 320 80C297.9 80 280 97.9 280 120C280 132.6 285.9 143.9 295 151.2L226.6 258.8C216.6 274.5 195.3 278.4 180.4 267.2L120.9 222.7C125.4 216.3 128 208.4 128 200C128 177.9 110.1 160 88 160C65.9 160 48 177.9 48 200C48 221.8 65.5 239.6 87.2 240L119.8 457.5C124.5 488.8 151.4 512 183.1 512L456.9 512C488.6 512 515.5 488.8 520.2 457.5L552.8 240C574.5 239.6 592 221.8 592 200C592 177.9 574.1 160 552 160C529.9 160 512 177.9 512 200C512 208.4 514.6 216.3 519.1 222.7L459.7 267.3C444.8 278.5 423.5 274.6 413.5 258.9L345 151.2z"/><path d="M180 550H460" fill="none" stroke="#f5af2f" stroke-width="28" stroke-linecap="round"/></svg></a></label>
								</c-col>
								<c-col class="col-sm-8 ">
								<c-button color="info" class="button" id="image-upload-button1" name="image-upload-button1" @click="openMediaModal1" style="margin: 10px;" <?php echo $is_disabled ? 'disabled' : ''; ?>>
										<?php esc_attr_e( 'Add Image', 'gdpr-cookie-consent' ); ?>
									</c-button>
									<c-button color="info" class="button" id="image-delete-button" @click="deleteSelectedimage1" style="margin: 10px; ">
										<?php esc_attr_e( 'Remove Image', 'gdpr-cookie-consent' ); ?>
									</c-button>
									<?php
								$get_banner_img1 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1 );
									?>
									<img id="gdpr-cookie-bar-logo-holder1" name="gdpr-cookie-bar-logo-holder1" src="<?php echo esc_url_raw( $get_banner_img1 ); ?>" alt="">
									<p class="image-upload-notice" style="margin-left: 10px; font-size:14px; font-weight:14px;color:#d4d4d8;">
										<?php esc_attr_e( 'We recommend 50 x 50 pixels.', 'gdpr-cookie-consent' ); ?>
									</p>
									<c-input type="hidden" name="gdpr-cookie-bar-logo-url-holder1" id="gdpr-cookie-bar-logo-url-holder1"  class="regular-text"> </c-input>
								</c-col>
							</c-row>
							</c-card-body>
					</c-card-body>
				</c-card>
				
				<c-card v-if="cookie_bar_settings_open1" v-show="ab_testing_enabled  && gdpr_policy != 'both' && active_test_banner_tab === 2"class=" desgin_card">
						<c-card-body v-show="active_test_banner_tab === 2">
							<c-row>
								<c-col class="col-sm-8"><div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Cookie Bar Body Design', 'gdpr-cookie-consent' ); ?></div></c-col>
								<c-col class="col-sm-4"><button type="button" class="cookie-bar-settings-close" @click="cookie_bar_settings_open1 = false" aria-label="<?php esc_attr_e( 'Close Cookie Bar Settings', 'gdpr-cookie-consent' ); ?>">
								<svg class="connect-info-close" data-target="connect-info-container" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18" stroke="#3A3A41" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M6 6L18 18" stroke="#3A3A41" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
							</button></c-col>
							</c-row>
							
								<c-card-body >														
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Consent Banner Title', 'gdpr-cookie-consent' ); ?> </label></c-col>
										<c-col class="col-sm-8">
											<c-input name="gdpr-cookie_bar2_name" v-model="cookie_bar2_name" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Make this banner default', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<c-switch v-bind="labelIcon" v-model="default_cookie_bar" id="gdpr-cookie-consent-default_cookie_bar2" variant="3d"  color="success" :checked="!default_cookie_bar" v-on:update:checked="onSwitchDefaultCookieBar"></c-switch>
											<input type="hidden" name="gdpr-default_cookie_bar" v-model="default_cookie_bar">
										</c-col> 
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cookie Bar Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="cookie_bar_color2" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-bar-color2" type="color" name="gdpr-cookie-bar-color2" v-model="cookie_bar_color2" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( ' Cookie Bar Opacity', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="cookie_bar_opacity2" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.01" name="gdpr-cookie-bar-opacity2" v-model="cookie_bar_opacity2" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="cookie_text_color2" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-text-color2" type="color" name="gdpr-cookie-text-color2" v-model="cookie_text_color2" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Styles', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<v-select class="form-group" id="gdpr-cookie-border-style2" :reduce="label => label.code" :options="border_style_options" v-model="border_style2">
											</v-select>
											<input type="hidden" name="gdpr-cookie-border-style2" v-model="border_style2">
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="cookie_bar_border_width2" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-bar-border-width2" v-model="cookie_bar_border_width2" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="cookie_border_color2" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-border-color2" type="color" name="gdpr-cookie-border-color2" v-model="cookie_border_color2" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="cookie_bar_border_radius2" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-bar-border-radius2" v-model="cookie_bar_border_radius2" aria-label="<?php esc_attr_e('GDPR Cookie input fields data', 'gdpr-cookie-consent'); ?>"></c-input>
										</c-col>
									</c-row>
									<?php
									$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ? GDPR_COOKIE_CONSENT_VERSION : '';
									if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
										if ( ! $is_pro_active ) :
											?>
											<c-row>
												<c-col class="col-sm-4"><label><?php esc_attr_e( 'Font', 'gdpr-cookie-consent' ); ?></label>
													<div class="gdpr-pro-label"><div class="gdpr-pro-label-text">Pro</div></div>
												</c-col>
												<c-col class="col-sm-8">
													<v-select disabled class="form-group" id="gdpr-cookie-font" :reduce="label => label.code" :options="font_options" v-model="cookie_font2">
													</v-select>
													<input type="hidden" name="gdpr-cookie-font2" v-model="cookie_font2">
												</c-col>
											</c-row>
										<?php endif ?>
										<?php
										do_action( 'gdpr_cookie_font' );
									} else {
										?>
											<c-row>
												<c-col class="col-sm-4"><label><?php esc_attr_e( 'Font', 'gdpr-cookie-consent' ); ?></label></c-col>
												<c-col class="col-sm-8">
													<v-select class="form-group" id="gdpr-cookie-font" :reduce="label => label.code" :options="font_options" v-model="cookie_font2">
													</v-select>
													<input type="hidden" name="gdpr-cookie-font2" v-model="cookie_font2	">
												</c-col>
											</c-row>
										<?php }
										?>
										<c-row>
											<c-col class="col-sm-4">
												<label><?php esc_attr_e( 'Upload Logo ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'To preview the logo, simply upload a logo and then click the "Save Changes" button ', 'gdpr-cookie-consent' ); ?>"></tooltip><a href="https://wplegalpages.com/pricing?utm_source=wp_cookie_consent&utm_medium=upload_logo&utm_campaign=plugin_upgrade" style="margin-left:5px;"  class="probadge bg-badge"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 640" fill="#f5af2f"> <path d="M345 151.2C354.2 143.9 360 132.6 360 120C360 97.9 342.1 80 320 80C297.9 80 280 97.9 280 120C280 132.6 285.9 143.9 295 151.2L226.6 258.8C216.6 274.5 195.3 278.4 180.4 267.2L120.9 222.7C125.4 216.3 128 208.4 128 200C128 177.9 110.1 160 88 160C65.9 160 48 177.9 48 200C48 221.8 65.5 239.6 87.2 240L119.8 457.5C124.5 488.8 151.4 512 183.1 512L456.9 512C488.6 512 515.5 488.8 520.2 457.5L552.8 240C574.5 239.6 592 221.8 592 200C592 177.9 574.1 160 552 160C529.9 160 512 177.9 512 200C512 208.4 514.6 216.3 519.1 222.7L459.7 267.3C444.8 278.5 423.5 274.6 413.5 258.9L345 151.2z"/><path d="M180 550H460" fill="none" stroke="#f5af2f" stroke-width="28" stroke-linecap="round"/></svg></a></label>
											</c-col>
											<c-col class="col-sm-8 ">
											<c-button color="info" class="button" id="image-upload-button" name="image-upload-button2" @click="openMediaModal2" style="margin: 10px;" <?php echo $is_disabled ? 'disabled' : ''; ?>>
													<?php esc_attr_e( 'Add Image', 'gdpr-cookie-consent' ); ?>
												</c-button>
												<c-button color="info" class="button" id="image-delete-button" @click="deleteSelectedimage2" style="margin: 10px; ">
													<?php esc_attr_e( 'Remove Image', 'gdpr-cookie-consent' ); ?>
												</c-button>
												<?php
												$get_banner_img2 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD2 );												?>
												<img id="gdpr-cookie-bar-logo-holder2" name="gdpr-cookie-bar-logo-holder2" src="<?php echo esc_url_raw( $get_banner_img2 ); ?>" alt="">
												<p class="image-upload-notice" style="margin-left: 10px;">
													<?php esc_attr_e( 'We recommend 50 x 50 pixels.', 'gdpr-cookie-consent' ); ?>
												</p>
												<c-input type="hidden" name="gdpr-cookie-bar-logo-url-holder2" id="gdpr-cookie-bar-logo-url-holder2"  class="regular-text"> </c-input>
											</c-col>
										</c-row>
								</c-card-body>
						</c-card-body>
				</c-card>
				<div
						v-show="button_readmore_popup1"
							class="gdpr-privacy-policy-settings-panel"
							>
							<div class="optout-settings-tittle-bar">
								<div class="optout-setting-tittle">
									<?php esc_attr_e( 'Privacy Policy Settings', 'gdpr-cookie-consent' ); ?>
								</div>

								<img
									@click="button_readmore_popup1=false; button_readmore_popup2=false"
									class="add-new-entry-img"
									src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
									alt="Close"
								>
							</div>

							<div class="optout-settings-main-container">

								<div v-show="(show_revoke_card || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl) && button_readmore_is_on1">
									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input
												class="gdpr-color-input"
												type="text"
												v-model="button_readmore_link_color1"
											></c-input>

											<c-input
												class="gdpr-color-select"
												id="gdpr-readmore-link-color"
												type="color"
												name="gcc-readmore-link-color1"
												v-model="button_readmore_link_color1"
											></c-input>
										</c-col>
									</c-row>

									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Show as', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8">
											<v-select
												class="form-group"
												id="gcc-readmore-as-button"
												:reduce="label => label.code"
												:options="show_as_options"
												v-model="button_readmore_as_button1"
											></v-select>

											<input
												type="hidden"
												name="gcc-readmore-as-button1"
												v-model="button_readmore_as_button1"
											>
										</c-col>
									</c-row>

									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Page or Custom URL', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8">
											<v-select
												class="form-group"
												id="gcc-readmore-url-type"
												:reduce="label => label.code"
												:options="url_type_options"
												v-model="button_readmore_url_type1"
											></v-select>

											<input
												type="hidden"
												name="gcc-readmore-url-type1"
												v-model="button_readmore_url_type1"
											>
										</c-col>
									</c-row>

									<c-row
										v-show="button_readmore_as_button1"
										class="gdpr-label-row"
									>
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input
												class="gdpr-color-input"
												type="text"
												v-model="button_readmore_button_color1"
											></c-input>

											<c-input
												class="gdpr-color-select"
												id="gdpr-readmore-button-color"
												type="color"
												name="gcc-readmore-button-color1"
												v-model="button_readmore_button_color1"
											></c-input>
										</c-col>
									</c-row>

									<c-row
										v-show="button_readmore_as_button1"
										class="gdpr-label-row"
									>
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8">
											<v-select
												class="form-group"
												id="gcc-readmore-button-size"
												:reduce="label => label.code"
												:options="button_size_options"
												v-model="button_readmore_button_size1"
											></v-select>

											<input
												type="hidden"
												name="gcc-readmore-button-size1"
												v-model="button_readmore_button_size1"
											>
										</c-col>
									</c-row>

									<c-row
										v-show="button_readmore_as_button1"
										class="gdpr-label-row"
									>
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8">
											<v-select
												class="form-group"
												id="gcc-readmore-button-border-style"
												:reduce="label => label.code"
												:options="border_style_options"
												v-model="button_readmore_button_border_style1"
											></v-select>

											<input
												type="hidden"
												name="gcc-readmore-button-border-style1"
												v-model="button_readmore_button_border_style1"
											>
										</c-col>
									</c-row>

									<c-row
										v-show="button_readmore_as_button1"
										class="gdpr-label-row"
									>
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input
												class="gdpr-color-input"
												type="text"
												v-model="button_readmore_button_border_color1"
											></c-input>

											<c-input
												class="gdpr-color-select"
												id="gdpr-readmore-button-border-color"
												type="color"
												name="gcc-readmore-button-border-color1"
												v-model="button_readmore_button_border_color1"
											></c-input>
										</c-col>
									</c-row>

									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label v-show="button_readmore_url_type1">
												<?php esc_attr_e( 'Page', 'gdpr-cookie-consent' ); ?>
											</label>

											<label v-show="!button_readmore_url_type1">
												<?php esc_attr_e( 'URL', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8">
											<div v-show="button_readmore_url_type1">
												<v-select
													class="form-group"
													placeholder="Select Policy Page"
													id="gcc-readmore-page"
													:reduce="label => label.code"
													:options="privacy_policy_options"
													v-model="readmore_page1"
													@input="onSelectPrivacyPage1"
												></v-select>

												<input
													type="hidden"
													name="gcc-readmore-page1"
													v-model="button_readmore_page1"
												>
											</div>

											<c-input
												v-show="!button_readmore_url_type1"
												name="gcc-readmore-url1"
												v-model="button_readmore_url1"
											></c-input>
										</c-col>
									</c-row>

									<c-row
										v-show="button_readmore_url_type1"
										class="gdpr-label-row"
									>
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Sync with WordPress Policy Page', 'gdpr-cookie-consent' ); ?>

												<tooltip text="<?php esc_html_e( 'If enabled visitor will be redirected to Privacy Policy Page set in WordPress settings irrespective of Page set in the previous setting.', 'gdpr-cookie-consent' ); ?>"></tooltip>
											</label>
										</c-col>

										<c-col class="col-sm-8 gdpr-readmore-toggle-row">
											<c-switch
												v-bind="labelIcon"
												v-model="button_readmore_wp_page1"
												id="gdpr-cookie-consent-readmore-wp-page"
												variant="3d"
												color="success"
												:checked="button_readmore_wp_page1"
												v-on:update:checked="onSwitchButtonReadMoreWpPage1"
											></c-switch>

											<input
												type="hidden"
												name="gcc-readmore-wp-page1"
												v-model="button_readmore_wp_page1"
											>
										</c-col>
									</c-row>

									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Open URL in New Window?', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8 gdpr-readmore-toggle-row">
											<c-switch
												v-bind="labelIcon"
												v-model="button_readmore_new_win1"
												id="gdpr-cookie-consent-readmore-new-win"
												variant="3d"
												color="success"
												:checked="button_readmore_new_win1"
												v-on:update:checked="onSwitchButtonReadMoreNewWin1"
											></c-switch>

											<input
												type="hidden"
												name="gcc-readmore-new-win1"
												v-model="button_readmore_new_win1"
											>
										</c-col>
									</c-row>

									<c-row
										v-show="button_readmore_as_button1"
										class="gdpr-label-row"
									>
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input
												class="gdpr-slider-select"
												type="range"
												min="0"
												max="1"
												step="0.01"
												v-model="button_readmore_button_opacity1"
											></c-input>

											<c-input
												class="gdpr-slider-input"
												type="number"
												name="gcc-readmore-button-opacity1"
												v-model="button_readmore_button_opacity1"
											></c-input>
										</c-col>
									</c-row>

									<c-row
										v-show="button_readmore_as_button1"
										class="gdpr-label-row"
									>
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input
												class="gdpr-slider-select"
												type="range"
												min="0"
												max="10"
												step="0.5"
												v-model="button_readmore_button_border_width1"
											></c-input>

											<c-input
												class="gdpr-slider-input"
												type="number"
												name="gcc-readmore-button-border-width1"
												v-model="button_readmore_button_border_width1"
											></c-input>
										</c-col>
									</c-row>

									<c-row
										v-show="button_readmore_as_button1"
										class="gdpr-label-row"
									>
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input
												class="gdpr-slider-select"
												type="range"
												min="0"
												max="100"
												step="0.5"
												v-model="button_readmore_button_border_radius1"
											></c-input>

											<c-input
												class="gdpr-slider-input"
												type="number"
												name="gcc-readmore-button-border-radius1"
												v-model="button_readmore_button_border_radius1"
											></c-input>
										</c-col>
									</c-row>

								</div>

							</div>
					</div>		
					<div
						v-show="revoke_consent_popup1"
						class="gdpr-revoke-consent-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Revoke Consent Settings', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="revoke_consent_popup1=false; revoke_consent_popup2=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">

							<div
								v-show="(show_revoke_card || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl) && is_revoke_consent_on1"
							>
								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8 gdpr-color-pick">
										<c-input
											class="gdpr-color-input"
											type="text"
											v-model="button_revoke_consent_text_color1"
										></c-input>

										<c-input
											class="gdpr-color-select"
											id="gdpr-readmore-link-color"
											type="color"
											name="gcc-revoke-consent-text-color1"
											v-model="button_revoke_consent_text_color1"
										></c-input>
									</c-col>
								</c-row>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8 gdpr-color-pick">
										<c-input
											class="gdpr-color-input"
											type="text"
											v-model="button_revoke_consent_background_color1"
										></c-input>

										<c-input
											class="gdpr-color-select"
											id="gdpr-readmore-button-color"
											type="color"
											name="gcc-revoke-consent-background-color1"
											v-model="button_revoke_consent_background_color1"
										></c-input>
									</c-col>
								</c-row>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Tab Position', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8">
										<v-select
											class="form-group"
											id="gdpr-cookie-consent-tab-position"
											:reduce="label => label.code"
											:options="tab_position_options"
											v-model="tab_position1"
										></v-select>

										<input
											type="hidden"
											name="gcc-tab-position1"
											v-model="tab_position1"
										>
									</c-col>
								</c-row>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Tab margin (in percent)', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8">
										<c-input
											type="number"
											min="0"
											max="100"
											name="gcc-tab-margin1"
											v-model="tab_margin1"
										></c-input>
									</c-col>
								</c-row>

							</div>

						</div>
					</div>
					<div
						v-show="accept_button_popup1"
						class="gdpr-accept-button-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Accept Button', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="accept_button_popup1=false; accept_button_popup2=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_text_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-accept-text-colornum1"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie accept text color1', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-accept-text-colornum1"
										type="color"
										name="gdpr-cookie-accept-text-color1"
										v-model="accept_text_color1"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-as-button1"
										:reduce="label => label.code"
										:options="accept_as_button_options"
										v-model="accept_as_button1"
										@input="onButtonChange($event, 'accept1')"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-as1"
										v-model="accept_as_button1"
									>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Action ', 'gdpr-cookie-consent' ); ?>

										<tooltip
											text="<?php esc_html_e( 'Select action to do once the user clicks on button.', 'gdpr-cookie-consent' ); ?>"
										></tooltip>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-action1"
										:reduce="label => label.code"
										:options="accept_action_options"
										v-model="accept_action1"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-action1"
										v-model="accept_action1"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_action1!='#cookie_action_close_header'"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<c-input
										name="gdpr-cookie-accept-url1"
										v-model="accept_url1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_action1!='#cookie_action_close_header'"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-url-new-window1"
										:reduce="label => label.code"
										:options="open_url_options"
										v-model="open_url1"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-url-new-window1"
										v-model="open_url1"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_background_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-accept-background-color1"
										type="color"
										name="gdpr-cookie-accept-background-color1"
										v-model="accept_background_color1"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-border-style1"
										:reduce="label => label.code"
										:options="border_style_options"
										v-model="accept_style1"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-border-style1"
										v-model="accept_style1"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_border_color1"
									></c-input>

									<c-input
										class="gdpr-color-select"
										type="color"
										name="gdpr-cookie-accept-border-color1"
										v-model="accept_border_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="1"
										step="0.01"
										v-model="accept_opacity1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input opacity-slider"
										type="number"
										min="0"
										max="1"
										step="0.1"
										name="gdpr-cookie-accept-opacity1"
										v-model="accept_opacity1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="10"
										step="0.5"
										v-model="accept_border_width1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-accept-border-width1"
										v-model="accept_border_width1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="100"
										step="0.5"
										v-model="accept_border_radius1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-accept-border-radius1"
										v-model="accept_border_radius1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="accept_all_button_popup1"
						class="gdpr-accept-all-button-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Accept All Button', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="accept_all_button_popup1=false; accept_all_button_popup2=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_all_text_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-accept-all-text-color1"
										type="color"
										name="gdpr-cookie-accept-all-text-color1"
										v-model="accept_all_text_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-all-as-button1"
										:reduce="label => label.code"
										:options="accept_as_button_options"
										v-model="accept_all_as_button1"
										@input="onButtonChange($event, 'accept_all1')"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-all-as1"
										v-model="accept_all_as_button1"
									>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Action ', 'gdpr-cookie-consent' ); ?>

										<tooltip
											text="<?php esc_html_e( 'Select action to do once the user clicks on button.', 'gdpr-cookie-consent' ); ?>"
										></tooltip>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-all-action1"
										:reduce="label => label.code"
										:options="accept_action_options"
										v-model="accept_all_action1"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-all-action1"
										v-model="accept_all_action1"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_action1!='#cookie_action_close_header'"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8">
									<c-input
										name="gdpr-cookie-accept-all-url1"
										v-model="accept_all_url1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_action1!='#cookie_action_close_header'"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-all-new-window1"
										:reduce="label => label.code"
										:options="open_url_options"
										v-model="accept_all_new_win1"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-all-new-window1"
										v-model="accept_all_new_win1"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_all_background_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-accept-all-background-color1"
										type="color"
										name="gdpr-cookie-accept-all-background-color1"
										v-model="accept_all_background_color1"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-all-border-style1"
										:reduce="label => label.code"
										:options="border_style_options"
										v-model="accept_all_style1"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-all-border-style1"
										v-model="accept_all_style1"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_all_border_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-accept-all-border-color1"
										type="color"
										name="gdpr-cookie-accept-all-border-color1"
										v-model="accept_all_border_color1"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="1"
										step="0.01"
										v-model="accept_all_opacity1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input opacity-slider"
										type="number"
										min="0"
										max="1"
										step="0.1"
										name="gdpr-cookie-accept-all-opacity1"
										v-model="accept_all_opacity1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="10"
										step="0.5"
										v-model="accept_all_border_width1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-accept-all-border-width1"
										v-model="accept_all_border_width1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="100"
										step="0.5"
										v-model="accept_all_border_radius1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-accept-all-border-radius1"
										v-model="accept_all_border_radius1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="decline_button_popup1"
						class="gdpr-decline-button-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Reject All Button', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="decline_button_popup1=false; decline_button_popup2=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="decline_text_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-decline-text-color1"
										type="color"
										name="gdpr-cookie-decline-text-color1"
										v-model="decline_text_color1"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-decline-as-button1"
										:reduce="label => label.code"
										:options="accept_as_button_options"
										v-model="decline_as_button1"
										@input="onButtonChange($event, 'decline1')"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-decline-as1"
										v-model="decline_as_button1"
									>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Action ', 'gdpr-cookie-consent' ); ?>

										<tooltip
											text="<?php esc_html_e( 'Select action to do once the user clicks on the button', 'gdpr-cookie-consent' ); ?>"
										></tooltip>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-decline-action1"
										:reduce="label => label.code"
										:options="decline_action_options"
										v-model="decline_action1"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-decline-action1"
										v-model="decline_action1"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_action1!='#cookie_action_close_header_reject'"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8">
									<c-input
										name="gdpr-cookie-decline-url1"
										v-model="decline_url1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_action1!='#cookie_action_close_header_reject'"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-decline-url-new-window1"
										:reduce="label => label.code"
										:options="open_url_options"
										v-model="open_decline_url1"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-decline-url-new-window1"
										v-model="open_decline_url1"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="decline_background_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-decline-background-color1"
										type="color"
										name="gdpr-cookie-decline-background-color1"
										v-model="decline_background_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-decline-border-style1"
										:reduce="label => label.code"
										:options="border_style_options"
										v-model="decline_style1"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-decline-border-style1"
										v-model="decline_style1"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="decline_border_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-decline-border-color1"
										type="color"
										name="gdpr-cookie-decline-border-color1"
										v-model="decline_border_color1"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="1"
										step="0.01"
										v-model="decline_opacity1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input opacity-slider"
										type="number"
										min="0"
										max="1"
										step="0.1"
										name="gdpr-cookie-decline-opacity1"
										v-model="decline_opacity1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="10"
										step="0.5"
										v-model="decline_border_width1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-decline-border-width1"
										v-model="decline_border_width1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="100"
										step="0.5"
										v-model="decline_border_radius1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-decline-border-radius1"
										v-model="decline_border_radius1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="settings_button_popup1"
						class="gdpr-settings-button-settings-panel"
					    >
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Preferences Button', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="settings_button_popup1=false; settings_button_popup2=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="settings_text_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-settings-text-color1"
										type="color"
										name="gdpr-cookie-settings-text-color1"
										v-model="settings_text_color1"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-settings-as-button1"
										:reduce="label => label.code"
										:options="accept_as_button_options"
										v-model="settings_as_button1"
										@input="onButtonChange($event, 'settings1')"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-settings-as1"
										v-model="settings_as_button1"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="settings_background_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-settings-background-color1"
										type="color"
										name="gdpr-cookie-settings-background-color1"
										v-model="settings_background_color1"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-settings-border-style1"
										:reduce="label => label.code"
										:options="border_style_options"
										v-model="settings_style1"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-settings-border-style1"
										v-model="settings_style1"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="settings_border_color"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-settings-border-color1"
										type="color"
										name="gdpr-cookie-settings-border-color1"
										v-model="settings_border_color1"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="1"
										step="0.01"
										v-model="settings_opacity1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input opacity-slider"
										type="number"
										min="0"
										max="1"
										step="0.1"
										name="gdpr-cookie-settings-opacity1"
										v-model="settings_opacity1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="10"
										step="0.5"
										v-model="settings_border_width1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-settings-border-width1"
										v-model="settings_border_width1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button1"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="100"
										step="0.5"
										v-model="settings_border_radius1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-settings-border-radius1"
										v-model="settings_border_radius1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="confirm_button_popup1"
						class="gdpr-confirm-button-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Save Prefernces Button', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="confirm_button_popup1=false; confirm_button_popup2=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="confirm_text_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-confirm-text-color1"
										type="color"
										name="gdpr-cookie-confirm-text-color1"
										v-model="confirm_text_color1"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="confirm_background_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-confirm-background-color1"
										type="color"
										name="gdpr-cookie-confirm-background-color1"
										v-model="confirm_background_color1"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-confirm-border-style1"
										:reduce="label => label.code"
										:options="border_style_options"
										v-model="confirm_style1"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-confirm-border-style1"
										v-model="confirm_style1"
									>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="confirm_border_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-confirm-border-color1"
										type="color"
										name="gdpr-cookie-confirm-border-color1"
										v-model="confirm_border_color1"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="1"
										step="0.01"
										v-model="confirm_opacity1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input opacity-slider"
										type="number"
										min="0"
										max="1"
										step="0.1"
										name="gdpr-cookie-confirm-opacity1"
										v-model="confirm_opacity1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="10"
										step="0.5"
										v-model="confirm_border_width1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-confirm-border-width1"
										v-model="confirm_border_width1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="100"
										step="0.5"
										v-model="confirm_border_radius1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-confirm-border-radius1"
										v-model="confirm_border_radius1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="cancel_button_popup1"
						class="gdpr-cancel-button-settings-panel"
					    >
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Cancel Button', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="cancel_button_popup1=false; cancel_button_popup2=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="cancel_text_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-cancel-text-color1"
										type="color"
										name="gdpr-cookie-cancel-text-color1"
										v-model="cancel_text_color1"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="cancel_background_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-cancel-background-color1"
										type="color"
										name="gdpr-cookie-cancel-background-color1"
										v-model="cancel_background_color1"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-cancel-border-style1"
										:reduce="label => label.code"
										:options="border_style_options"
										v-model="cancel_style1"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-cancel-border-style1"
										v-model="cancel_style1"
									>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="cancel_border_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-cancel-border-color1"
										type="color"
										name="gdpr-cookie-cancel-border-color1"
										v-model="cancel_border_color1"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="1"
										step="0.01"
										v-model="cancel_opacity1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input opacity-slider"
										type="number"
										min="0"
										max="1"
										step="0.1"
										name="gdpr-cookie-cancel-opacity1"
										v-model="cancel_opacity1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="10"
										step="0.5"
										v-model="cancel_border_width1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-cancel-border-width1"
										v-model="cancel_border_width1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="100"
										step="0.5"
										v-model="cancel_border_radius1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-cancel-border-radius1"
										v-model="cancel_border_radius1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="opt_out_link_popup1"
						class="gdpr-opt-out-link-settings-panel"
					    >
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Opt Out Link', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="opt_out_link_popup1=false; opt_out_link_popup2=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="opt_out_text_color1"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-opt-out-text-color1"
										type="color"
										name="gdpr-cookie-opt-out-text-color1"
										v-model="opt_out_text_color1"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="ccpa_revoke_consent_popup1"
						class="gdpr-ccpa-revoke-consent-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'CCPA Revoke Consent Settings', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="ccpa_revoke_consent_popup1=false; ccpa_revoke_consent_popup2=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row
								v-show="is_auto_mode || is_us_state_laws"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="ccpa_button_revoke_consent_text_color1"
									></c-input>

									<c-input
										class="gdpr-color-select"
										type="color"
										name="gcc-ccpa-revoke-consent-text-color1"
										v-model="ccpa_button_revoke_consent_text_color1"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="is_auto_mode || is_us_state_laws"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="ccpa_button_revoke_consent_background_color1"
									></c-input>

									<c-input
										class="gdpr-color-select"
										type="color"
										name="gcc-ccpa-revoke-consent-background-color1"
										v-model="ccpa_button_revoke_consent_background_color1"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="is_auto_mode || is_us_state_laws"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Tab Position', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="ccpa-cookie-consent-tab-position1"
										:reduce="label => label.code"
										:options="tab_position_options"
										v-model="ccpa_tab_position1"
									></v-select>

									<input
										type="hidden"
										name="gcc-ccpa-tab-position1"
										v-model="ccpa_tab_position1"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="is_auto_mode || is_us_state_laws"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Tab margin (in percent)', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<c-input
										type="number"
										min="0"
										max="100"
										name="gcc-ccpa-tab-margin1"
										v-model="ccpa_tab_margin1"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="button_readmore_popup2"
						class="gdpr-privacy-policy-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Privacy Policy Settings', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="button_readmore_popup2=false; button_readmore_popup1=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

					<div class="optout-settings-main-container">
							<div
								v-show="(show_revoke_card || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl) && button_readmore_is_on2"
							 	>
								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8 gdpr-color-pick">
										<c-input
											class="gdpr-color-input"
											type="text"
											v-model="button_readmore_link_color2"
										></c-input>

										<c-input
											class="gdpr-color-select"
											id="gdpr-readmore-link-color"
											type="color"
											name="gcc-readmore-link-color2"
											v-model="button_readmore_link_color2"
										></c-input>
									</c-col>
								</c-row>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Show as', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8">
										<v-select
											class="form-group"
											id="gcc-readmore-as-button"
											:reduce="label => label.code"
											:options="show_as_options"
											v-model="button_readmore_as_button2"
										></v-select>

										<input
											type="hidden"
											name="gcc-readmore-as-button2"
											v-model="button_readmore_as_button2"
										>
									</c-col>
								</c-row>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Page or Custom URL', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8">
										<v-select
											class="form-group"
											id="gcc-readmore-url-type"
											:reduce="label => label.code"
											:options="url_type_options"
											v-model="button_readmore_url_type2"
										></v-select>

										<input
											type="hidden"
											name="gcc-readmore-url-type2"
											v-model="button_readmore_url_type2"
										>
									</c-col>
								</c-row>

								<c-row
									v-show="button_readmore_as_button2"
									class="gdpr-label-row"
								>
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8 gdpr-color-pick">
										<c-input
											class="gdpr-color-input"
											type="text"
											v-model="button_readmore_button_color2"
										></c-input>

										<c-input
											class="gdpr-color-select"
											id="gdpr-readmore-button-color"
											type="color"
											name="gcc-readmore-button-color2"
											v-model="button_readmore_button_color2"
										></c-input>
									</c-col>
								</c-row>

								<c-row
									v-show="button_readmore_as_button2"
									class="gdpr-label-row"
								>
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8">
										<v-select
											class="form-group"
											id="gcc-readmore-button-size"
											:reduce="label => label.code"
											:options="button_size_options"
											v-model="button_readmore_button_size2"
										></v-select>

										<input
											type="hidden"
											name="gcc-readmore-button-size2"
											v-model="button_readmore_button_size2"
										>
									</c-col>
								</c-row>

								<c-row
									v-show="button_readmore_as_button2"
									class="gdpr-label-row"
								>
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8">
										<v-select
											class="form-group"
											id="gcc-readmore-button-border-style"
											:reduce="label => label.code"
											:options="border_style_options"
											v-model="button_readmore_button_border_style2"
										></v-select>

										<input
											type="hidden"
											name="gcc-readmore-button-border-style2"
											v-model="button_readmore_button_border_style2"
										>
									</c-col>
								</c-row>

								<c-row
									v-show="button_readmore_as_button2"
									class="gdpr-label-row"
								>
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8 gdpr-color-pick">
										<c-input
											class="gdpr-color-input"
											type="text"
											v-model="button_readmore_button_border_color2"
										></c-input>

										<c-input
											class="gdpr-color-select"
											id="gdpr-readmore-button-border-color"
											type="color"
											name="gcc-readmore-button-border-color2"
											v-model="button_readmore_button_border_color2"
										></c-input>
									</c-col>
								</c-row>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label v-show="button_readmore_url_type2">
											<?php esc_attr_e( 'Page', 'gdpr-cookie-consent' ); ?>
										</label>

										<label v-show="!button_readmore_url_type2">
											<?php esc_attr_e( 'URL', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8">
										<div v-show="button_readmore_url_type2">
											<v-select
												class="form-group"
												placeholder="Select Policy Page"
												id="gcc-readmore-page"
												:reduce="label => label.code"
												:options="privacy_policy_options"
												v-model="readmore_page2"
												@input="onSelectPrivacyPage2"
											></v-select>

											<input
												type="hidden"
												name="gcc-readmore-page2"
												v-model="button_readmore_page2"
											>
										</div>

										<c-input
											v-show="!button_readmore_url_type2"
											name="gcc-readmore-url2"
											v-model="button_readmore_url2"
										></c-input>
									</c-col>
								</c-row>

								<c-row
									v-show="button_readmore_url_type2"
									class="gdpr-label-row"
								>
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Sync with WordPress Policy Page', 'gdpr-cookie-consent' ); ?>

											<tooltip text="<?php esc_html_e( 'If enabled visitor will be redirected to Privacy Policy Page set in WordPress settings irrespective of Page set in the previous setting.', 'gdpr-cookie-consent' ); ?>"></tooltip>
										</label>
									</c-col>

									<c-col class="col-sm-8 gdpr-readmore-toggle-row">
										<c-switch
											v-bind="labelIcon"
											v-model="button_readmore_wp_page2"
											id="gdpr-cookie-consent-readmore-wp-page"
											variant="3d"
											color="success"
											:checked="button_readmore_wp_page2"
											v-on:update:checked="onSwitchButtonReadMoreWpPage2"
										></c-switch>

										<input
											type="hidden"
											name="gcc-readmore-wp-page2"
											v-model="button_readmore_wp_page2"
										>
									</c-col>
								</c-row>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Open URL in New Window?', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8 gdpr-readmore-toggle-row">
										<c-switch
											v-bind="labelIcon"
											v-model="button_readmore_new_win2"
											id="gdpr-cookie-consent-readmore-new-win"
											variant="3d"
											color="success"
											:checked="button_readmore_new_win2"
											v-on:update:checked="onSwitchButtonReadMoreNewWin2"
										></c-switch>

										<input
											type="hidden"
											name="gcc-readmore-new-win2"
											v-model="button_readmore_new_win2"
										>
									</c-col>
								</c-row>

								<c-row
									v-show="button_readmore_as_button2"
									class="gdpr-label-row"
								>
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8 gdpr-color-pick">
										<c-input
											class="gdpr-slider-select"
											type="range"
											min="0"
											max="1"
											step="0.01"
											v-model="button_readmore_button_opacity2"
										></c-input>

										<c-input
											class="gdpr-slider-input"
											type="number"
											name="gcc-readmore-button-opacity2"
											v-model="button_readmore_button_opacity2"
										></c-input>
									</c-col>
								</c-row>

								<c-row
									v-show="button_readmore_as_button2"
									class="gdpr-label-row"
								>
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8 gdpr-color-pick">
										<c-input
											class="gdpr-slider-select"
											type="range"
											min="0"
											max="10"
											step="0.5"
											v-model="button_readmore_button_border_width2"
										></c-input>

										<c-input
											class="gdpr-slider-input"
											type="number"
											name="gcc-readmore-button-border-width2"
											v-model="button_readmore_button_border_width2"
										></c-input>
									</c-col>
								</c-row>

								<c-row
									v-show="button_readmore_as_button2"
									class="gdpr-label-row"
								>
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8 gdpr-color-pick">
										<c-input
											class="gdpr-slider-select"
											type="range"
											min="0"
											max="100"
											step="0.5"
											v-model="button_readmore_button_border_radius2"
										></c-input>

										<c-input
											class="gdpr-slider-input"
											type="number"
											name="gcc-readmore-button-border-radius2"
											v-model="button_readmore_button_border_radius2"
										></c-input>
									</c-col>
								</c-row>

							</div>

						</div>
					</div>		
					<div
						v-show="revoke_consent_popup2"
						class="gdpr-revoke-consent-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Revoke Consent Settings', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="revoke_consent_popup2=false; revoke_consent_popup1=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">

							<div
								v-show="(show_revoke_card || is_lgpd || is_uk_gdpr || is_pipeda || is_au_app || is_sa_pdpl) && is_revoke_consent_on2"
							>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8 gdpr-color-pick">
										<c-input
											class="gdpr-color-input"
											type="text"
											v-model="button_revoke_consent_text_color2"
										></c-input>

										<c-input
											class="gdpr-color-select"
											id="gdpr-readmore-link-color"
											type="color"
											name="gcc-revoke-consent-text-color2"
											v-model="button_revoke_consent_text_color2"
										></c-input>
									</c-col>
								</c-row>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8 gdpr-color-pick">
										<c-input
											class="gdpr-color-input"
											type="text"
											v-model="button_revoke_consent_background_color2"
										></c-input>

										<c-input
											class="gdpr-color-select"
											id="gdpr-readmore-button-color"
											type="color"
											name="gcc-revoke-consent-background-color2"
											v-model="button_revoke_consent_background_color2"
										></c-input>
									</c-col>
								</c-row>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Tab Position', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8">
										<v-select
											class="form-group"
											id="gdpr-cookie-consent-tab-position"
											:reduce="label => label.code"
											:options="tab_position_options"
											v-model="tab_position2"
										></v-select>

										<input
											type="hidden"
											name="gcc-tab-position2"
											v-model="tab_position2"
										>
									</c-col>
								</c-row>

								<c-row class="gdpr-label-row">
									<c-col class="col-sm-4">
										<label>
											<?php esc_attr_e( 'Tab margin (in percent)', 'gdpr-cookie-consent' ); ?>
										</label>
									</c-col>

									<c-col class="col-sm-8">
										<c-input
											type="number"
											min="0"
											max="100"
											name="gcc-tab-margin2"
											v-model="tab_margin2"
										></c-input>
									</c-col>
								</c-row>

							</div>

						</div>
					</div>
					<div
						v-show="accept_button_popup2"
						class="gdpr-accept-button-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Accept Button', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="accept_button_popup2=false; accept_button_popup1=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_text_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-accept-text-color2"
										type="color"
										name="gdpr-cookie-accept-text-color2"
										v-model="accept_text_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-as-button2"
										:reduce="label => label.code"
										:options="accept_as_button_options"
										v-model="accept_as_button2"
										@input="onButtonChange($event, 'accept2')"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-as2"
										v-model="accept_as_button2"
									>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Action ', 'gdpr-cookie-consent' ); ?>

										<tooltip
											text="<?php esc_html_e( 'Select action to do once the user clicks on button.', 'gdpr-cookie-consent' ); ?>"
										></tooltip>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-action2"
										:reduce="label => label.code"
										:options="accept_action_options"
										v-model="accept_action2"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-action2"
										v-model="accept_action2"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_action2!='#cookie_action_close_header'"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<c-input
										name="gdpr-cookie-accept-url2"
										v-model="accept_url2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_action2!='#cookie_action_close_header'"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-url-new-window2"
										:reduce="label => label.code"
										:options="open_url_options"
										v-model="open_url2"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-url-new-window2"
										v-model="open_url2"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_background_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-accept-background-color2"
										type="color"
										name="gdpr-cookie-accept-background-color2"
										v-model="accept_background_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-border-style2"
										:reduce="label => label.code"
										:options="border_style_options"
										v-model="accept_style2"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-border-style2"
										v-model="accept_style2"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_border_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-accept-border-color2"
										type="color"
										name="gdpr-cookie-accept-border-color2"
										v-model="accept_border_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="1"
										step="0.01"
										v-model="accept_opacity2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input opacity-slider"
										type="number"
										min="0"
										max="1"
										step="0.1"
										name="gdpr-cookie-accept-opacity2"
										v-model="accept_opacity2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="10"
										step="0.5"
										v-model="accept_border_width2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-accept-border-width2"
										v-model="accept_border_width2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="100"
										step="0.5"
										v-model="accept_border_radius2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-accept-border-radius2"
										v-model="accept_border_radius2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="accept_all_button_popup2"
						class="gdpr-accept-all-button-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Accept All Button', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="accept_all_button_popup2=false; accept_all_button_popup1=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_all_text_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-accept-all-text-color2"
										type="color"
										name="gdpr-cookie-accept-all-text-color2"
										v-model="accept_all_text_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-all-as-button2"
										:reduce="label => label.code"
										:options="accept_as_button_options"
										v-model="accept_all_as_button2"
										@input="onButtonChange($event, 'accept_all2')"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-all-as2"
										v-model="accept_all_as_button2"
									>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Action ', 'gdpr-cookie-consent' ); ?>

										<tooltip
											text="<?php esc_html_e( 'Select action to do once the user clicks on button.', 'gdpr-cookie-consent' ); ?>"
										></tooltip>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-all-action2"
										:reduce="label => label.code"
										:options="accept_action_options"
										v-model="accept_all_action2"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-all-action2"
										v-model="accept_all_action2"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_action2!='#cookie_action_close_header'"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<label
										for="gdpr-cookie-accept-all-url2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr-cookie-accept-all-url2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-accept-all-url2"
										name="gdpr-cookie-accept-all-url2"
										v-model="accept_all_url2"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_action2!='#cookie_action_close_header'"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-all-new-window2"
										:reduce="label => label.code"
										:options="open_url_options"
										v-model="accept_all_new_win2"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-all-new-window2"
										v-model="accept_all_new_win2"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_all_background_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-accept-all-background-color2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr-cookie-accept-all-background-color2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-accept-all-background-color2"
										type="color"
										name="gdpr-cookie-accept-all-background-color2"
										v-model="accept_all_background_color2"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-accept-all-border-style2"
										:reduce="label => label.code"
										:options="border_style_options"
										v-model="accept_all_style2"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-accept-all-border-style2"
										v-model="accept_all_style2"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="accept_all_border_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-accept-all-border-color2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr-cookie-accept-all-border-color2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-accept-all-border-color2"
										type="color"
										name="gdpr-cookie-accept-all-border-color2"
										v-model="accept_all_border_color2"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="1"
										step="0.01"
										v-model="accept_all_opacity2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-accept-all-opacity2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr-cookie-accept-all-opacity2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-accept-all-opacity2"
										class="gdpr-slider-input opacity-slider"
										type="number"
										min="0"
										max="1"
										step="0.1"
										name="gdpr-cookie-accept-all-opacity2"
										v-model="accept_all_opacity2"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="10"
										step="0.5"
										v-model="accept_all_border_width2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-accept-all-border-width2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr-cookie-accept-all-border-width2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-accept-all-border-width2"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-accept-all-border-width2"
										v-model="accept_all_border_width2"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="accept_all_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="100"
										step="0.5"
										v-model="accept_all_border_radius2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-accept-all-border-radius2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr-cookie-accept-all-border-radius2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-accept-all-border-radius2"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-accept-all-border-radius2"
										v-model="accept_all_border_radius2"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="decline_button_popup2"
						class="gdpr-decline-button-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Reject All Button', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="decline_button_popup2=false; decline_button_popup1=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="decline_text_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-decline-text-color2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie decline text color2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-decline-text-color2"
										type="color"
										name="gdpr-cookie-decline-text-color2"
										v-model="decline_text_color2"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-decline-as-button2"
										:reduce="label => label.code"
										:options="accept_as_button_options"
										v-model="decline_as_button2"
										@input="onButtonChange($event, 'decline2')"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-decline-as2"
										v-model="decline_as_button2"
									>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Action ', 'gdpr-cookie-consent' ); ?>

										<tooltip
											text="<?php esc_html_e( 'Select action to do once the user clicks on the button', 'gdpr-cookie-consent' ); ?>"
										></tooltip>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-decline-action2"
										:reduce="label => label.code"
										:options="decline_action_options"
										v-model="decline_action2"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-decline-action2"
										v-model="decline_action2"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_action2!='#cookie_action_close_header_reject'"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<label
										for="gdpr-cookie-decline-url2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie decline url2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-decline-url2"
										name="gdpr-cookie-decline-url2"
										v-model="decline_url2"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_action2!='#cookie_action_close_header_reject'"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-decline-url-new-window2"
										:reduce="label => label.code"
										:options="open_url_options"
										v-model="open_decline_url2"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-decline-url-new-window2"
										v-model="open_decline_url2"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="decline_background_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-decline-background-color2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie decline background color2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-decline-background-color2"
										type="color"
										name="gdpr-cookie-decline-background-color2"
										v-model="decline_background_color2"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-decline-border-style2"
										:reduce="label => label.code"
										:options="border_style_options"
										v-model="decline_style2"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-decline-border-style2"
										v-model="decline_style2"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="decline_border_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-decline-border-color2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie decline border color2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-decline-border-color2"
										type="color"
										name="gdpr-cookie-decline-border-color2"
										v-model="decline_border_color2"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="1"
										step="0.01"
										v-model="decline_opacity2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-decline-opacity2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie decline opacity2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-decline-opacity2"
										class="gdpr-slider-input opacity-slider"
										type="number"
										min="0"
										max="1"
										step="0.1"
										name="gdpr-cookie-decline-opacity2"
										v-model="decline_opacity2"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="10"
										step="0.5"
										v-model="decline_border_width2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-decline-border-width2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie decline border width2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-decline-border-width2"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-decline-border-width2"
										v-model="decline_border_width2"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="decline_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="100"
										step="0.5"
										v-model="decline_border_radius2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-decline-border-radius2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie decline border radius2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-decline-border-radius2"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-decline-border-radius2"
										v-model="decline_border_radius2"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="settings_button_popup2"
						class="gdpr-settings-button-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Prefences Button', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="settings_button_popup2=false; settings_button_popup1=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="settings_text_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-settings-text-color2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie settings text color2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-settings-text-color2"
										type="color"
										name="gdpr-cookie-settings-text-color2"
										v-model="settings_text_color2"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-settings-as-button2"
										:reduce="label => label.code"
										:options="accept_as_button_options"
										v-model="settings_as_button2"
										@input="onButtonChange($event, 'settings2')"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-settings-as2"
										v-model="settings_as_button2"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="settings_background_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-settings-background-color2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie settings background color2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-settings-background-color2"
										type="color"
										name="gdpr-cookie-settings-background-color2"
										v-model="settings_background_color2"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-settings-border-style2"
										:reduce="label => label.code"
										:options="border_style_options"
										v-model="settings_style2"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-settings-border-style2"
										v-model="settings_style2"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="settings_border_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-settings-border-color2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie settings border color2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-settings-border-color2"
										type="color"
										name="gdpr-cookie-settings-border-color2"
										v-model="settings_border_color2"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="1"
										step="0.01"
										v-model="settings_opacity2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-settings-opacity2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie settings opacity2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-settings-opacity2"
										class="gdpr-slider-input opacity-slider"
										type="number"
										min="0"
										max="1"
										step="0.1"
										name="gdpr-cookie-settings-opacity2"
										v-model="settings_opacity2"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="10"
										step="0.5"
										v-model="settings_border_width2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-settings-border-width2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie settings border width2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-settings-border-width2"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-settings-border-width2"
										v-model="settings_border_width2"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="settings_as_button2"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="100"
										step="0.5"
										v-model="settings_border_radius2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-settings-border-radius2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie settings border radius2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-settings-border-radius2"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-settings-border-radius2"
										v-model="settings_border_radius2"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
								v-show="confirm_button_popup2"
								class="gdpr-confirm-button-settings-panel"
								>
								<div class="optout-settings-tittle-bar">
									<div class="optout-setting-tittle">
										<?php esc_attr_e( 'Save Prefernces Button', 'gdpr-cookie-consent' ); ?>
									</div>

									<img
										@click="confirm_button_popup2=false; confirm_button_popup1=false"
										class="add-new-entry-img"
										src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
										alt="Close"
									>
								</div>

								<div class="optout-settings-main-container">
									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input
												class="gdpr-color-input"
												type="text"
												v-model="confirm_text_color2"
												aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
											></c-input>

											<label
												for="gdpr-cookie-confirm-text-color2"
												class="screen-reader-text"
											>
												<?php esc_attr_e( 'gdpr cookie confirm text color2', 'gdpr-cookie-consent' ); ?>
											</label>

											<c-input
												class="gdpr-color-select"
												id="gdpr-cookie-confirm-text-color2"
												type="color"
												name="gdpr-cookie-confirm-text-color2"
												v-model="confirm_text_color2"
											></c-input>
										</c-col>
									</c-row>

									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input
												class="gdpr-color-input"
												type="text"
												v-model="confirm_background_color2"
												aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
											></c-input>

											<label
												for="gdpr-cookie-confirm-background-color2"
												class="screen-reader-text"
											>
												<?php esc_attr_e( 'gdpr cookie confirm background color2', 'gdpr-cookie-consent' ); ?>
											</label>

											<c-input
												class="gdpr-color-select"
												id="gdpr-cookie-confirm-background-color2"
												type="color"
												name="gdpr-cookie-confirm-background-color2"
												v-model="confirm_background_color2"
											></c-input>
										</c-col>
									</c-row>

									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8">
											<v-select
												class="form-group"
												id="gdpr-cookie-confirm-border-style2"
												:reduce="label => label.code"
												:options="border_style_options"
												v-model="confirm_style2"
											></v-select>

											<input
												type="hidden"
												name="gdpr-cookie-confirm-border-style2"
												v-model="confirm_style2"
											>
										</c-col>
									</c-row>

									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input
												class="gdpr-color-input"
												type="text"
												v-model="confirm_border_color2"
												aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
											></c-input>

											<label
												for="gdpr-cookie-confirm-border-color2"
												class="screen-reader-text"
											>
												<?php esc_attr_e( 'gdpr cookie confirm border color2', 'gdpr-cookie-consent' ); ?>
											</label>

											<c-input
												class="gdpr-color-select"
												id="gdpr-cookie-confirm-border-color2"
												type="color"
												name="gdpr-cookie-confirm-border-color2"
												v-model="confirm_border_color2"
											></c-input>
										</c-col>
									</c-row>

									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input
												class="gdpr-slider-select"
												type="range"
												min="0"
												max="1"
												step="0.01"
												v-model="confirm_opacity2"
												aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
											></c-input>

											<label
												for="gdpr-cookie-confirm-opacity2"
												class="screen-reader-text"
											>
												<?php esc_attr_e( 'gdpr cookie confirm opacity2', 'gdpr-cookie-consent' ); ?>
											</label>

											<c-input
												id="gdpr-cookie-confirm-opacity2"
												class="gdpr-slider-input opacity-slider"
												type="number"
												min="0"
												max="1"
												step="0.1"
												name="gdpr-cookie-confirm-opacity2"
												v-model="confirm_opacity2"
											></c-input>
										</c-col>
									</c-row>

									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input
												class="gdpr-slider-select"
												type="range"
												min="0"
												max="10"
												step="0.5"
												v-model="confirm_border_width2"
												aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
											></c-input>

											<label
												for="gdpr-cookie-confirm-border-width2"
												class="screen-reader-text"
											>
												<?php esc_attr_e( 'gdpr cookie confirm border width2', 'gdpr-cookie-consent' ); ?>
											</label>

											<c-input
												id="gdpr-cookie-confirm-border-width2"
												class="gdpr-slider-input"
												type="number"
												name="gdpr-cookie-confirm-border-width2"
												v-model="confirm_border_width2"
											></c-input>
										</c-col>
									</c-row>

									<c-row class="gdpr-label-row">
										<c-col class="col-sm-4">
											<label>
												<?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?>
											</label>
										</c-col>

										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input
												class="gdpr-slider-select"
												type="range"
												min="0"
												max="100"
												step="0.5"
												v-model="confirm_border_radius2"
												aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
											></c-input>

											<label
												for="gdpr-cookie-confirm-border-radius2"
												class="screen-reader-text"
											>
												<?php esc_attr_e( 'gdpr cookie confirm border radius2', 'gdpr-cookie-consent' ); ?>
											</label>

											<c-input
												id="gdpr-cookie-confirm-border-radius2"
												class="gdpr-slider-input"
												type="number"
												name="gdpr-cookie-confirm-border-radius2"
												v-model="confirm_border_radius2"
											></c-input>
										</c-col>
									</c-row>

								</div>
							</div>
					<div
						v-show="cancel_button_popup2"
						class="gdpr-cancel-button-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Cancel Button', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="cancel_button_popup2=false; cancel_button_popup1=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>
					<div class="optout-settings-main-container">
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="cancel_text_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-cancel-text-color2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie cancel text color2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-cancel-text-color2"
										type="color"
										name="gdpr-cookie-cancel-text-color2"
										v-model="cancel_text_color2"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="cancel_background_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-cancel-background-color2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie cancel background color2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-cancel-background-color2"
										type="color"
										name="gdpr-cookie-cancel-background-color2"
										v-model="cancel_background_color2"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="gdpr-cookie-cancel-border-style2"
										:reduce="label => label.code"
										:options="border_style_options"
										v-model="cancel_style2"
									></v-select>

									<input
										type="hidden"
										name="gdpr-cookie-cancel-border-style2"
										v-model="cancel_style2"
									>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="cancel_border_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-cancel-border-color2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie cancel border color2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-cancel-border-color2"
										type="color"
										name="gdpr-cookie-cancel-border-color2"
										v-model="cancel_border_color2"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="1"
										step="0.01"
										v-model="cancel_opacity2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-cancel-opacity2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie cancel opacity2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-cancel-opacity2"
										class="gdpr-slider-input opacity-slider"
										type="number"
										min="0"
										max="1"
										step="0.1"
										name="gdpr-cookie-cancel-opacity2"
										v-model="cancel_opacity2"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="10"
										step="0.5"
										v-model="cancel_border_width2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-cancel-border-width2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie cancel border width2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-cancel-border-width2"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-cancel-border-width2"
										v-model="cancel_border_width2"
									></c-input>
								</c-col>
							</c-row>

							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-slider-select"
										type="range"
										min="0"
										max="100"
										step="0.5"
										v-model="cancel_border_radius2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-cancel-border-radius2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie cancel border radius2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										id="gdpr-cookie-cancel-border-radius2"
										class="gdpr-slider-input"
										type="number"
										name="gdpr-cookie-cancel-border-radius2"
										v-model="cancel_border_radius2"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="opt_out_link_popup2"
						class="gdpr-opt-out-link-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'Opt-out Link', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="opt_out_link_popup2=false; opt_out_link_popup1=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="opt_out_text_color2"
										aria-label="<?php esc_attr_e( 'GDPR Cookie input fields data', 'gdpr-cookie-consent' ); ?>"
									></c-input>

									<label
										for="gdpr-cookie-opt-out-text-color2"
										class="screen-reader-text"
									>
										<?php esc_attr_e( 'gdpr cookie opt out text color2', 'gdpr-cookie-consent' ); ?>
									</label>

									<c-input
										class="gdpr-color-select"
										id="gdpr-cookie-opt-out-text-color2"
										type="color"
										name="gdpr-cookie-opt-out-text-color2"
										v-model="opt_out_text_color2"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
					<div
						v-show="ccpa_revoke_consent_popup2"
						class="gdpr-ccpa-revoke-consent-settings-panel"
						>
						<div class="optout-settings-tittle-bar">
							<div class="optout-setting-tittle">
								<?php esc_attr_e( 'CCPA Revoke Consent Settings', 'gdpr-cookie-consent' ); ?>
							</div>

							<img
								@click="ccpa_revoke_consent_popup2=false; ccpa_revoke_consent_popup1=false"
								class="add-new-entry-img"
								src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>"
								alt="Close"
							>
						</div>

						<div class="optout-settings-main-container">
							<c-row
								v-show="is_auto_mode || is_us_state_laws"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="ccpa_button_revoke_consent_text_color2"
									></c-input>

									<c-input
										class="gdpr-color-select"
										type="color"
										name="gcc-ccpa-revoke-consent-text-color2"
										v-model="ccpa_button_revoke_consent_text_color2"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="is_auto_mode || is_us_state_laws"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8 gdpr-color-pick">
									<c-input
										class="gdpr-color-input"
										type="text"
										v-model="ccpa_button_revoke_consent_background_color2"
									></c-input>

									<c-input
										class="gdpr-color-select"
										type="color"
										name="gcc-ccpa-revoke-consent-background-color2"
										v-model="ccpa_button_revoke_consent_background_color2"
									></c-input>
								</c-col>
							</c-row>

							<c-row
								v-show="is_auto_mode || is_us_state_laws"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Tab Position', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<v-select
										class="form-group"
										id="ccpa-cookie-consent-tab-position2"
										:reduce="label => label.code"
										:options="tab_position_options"
										v-model="ccpa_tab_position2"
									></v-select>

									<input
										type="hidden"
										name="gcc-ccpa-tab-position2"
										v-model="ccpa_tab_position2"
									>
								</c-col>
							</c-row>

							<c-row
								v-show="is_auto_mode || is_us_state_laws"
								class="gdpr-label-row"
							>
								<c-col class="col-sm-4">
									<label>
										<?php esc_attr_e( 'Tab margin (in percent)', 'gdpr-cookie-consent' ); ?>
									</label>
								</c-col>

								<c-col class="col-sm-8">
									<c-input
										type="number"
										min="0"
										max="100"
										name="gcc-ccpa-tab-margin2"
										v-model="ccpa_tab_margin2"
									></c-input>
								</c-col>
							</c-row>

						</div>
					</div>
				 	</div>						
			<!-- </div> -->
		</div>
				</c-tab>

			<!-- CUSTOM CSS START -->
				<c-tab title="<?php esc_attr_e( 'Custom CSS', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#custom_css" id="gdpr-cookie-consent-custom-css">
					<div class="card-body">
					<?php
					$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ? GDPR_COOKIE_CONSENT_VERSION : '';
					if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
						if ( $is_pro_active ) {
							do_action( 'gdpr_custom_css' );
						} else {
							?>
										<c-row>
											<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Add Your Custom CSS', 'gdpr-cookie-consent' ); ?></div></c-col>
										</c-row>
										<c-col class="col-sm-12" style="padding-left:0px;">
											<aceeditor
												id = "aceEditorFree"
												v-model="gdpr_css_text_free"
												@init="editorInit"
												lang="css"
												theme="monokai"
												width="100%"
												height="300px"
												:options="{
													enableBasicAutocompletion: true,
													enableLiveAutocompletion: true,
													fontSize: 14,
													highlightActiveLine: true,
													enableSnippets: true,
													showLineNumbers: true,
													tabSize: 2,
													showPrintMargin: false,
													showGutter: true,
												}"
												@keydown.native.stop
											/>
										</c-col>
							<?php
						}
					} else {
						?>
						<c-row>
							<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Add Your Custom CSS', 'gdpr-cookie-consent' ); ?></div></c-col>
						</c-row>
						<c-col class="col-sm-12" style="padding-left:0px;">
							<aceeditor
								id = "aceEditor"
								name="gdpr_css_text_field"
								v-model="gdpr_css_text"
								@init="editorInit"
								lang="css"
								theme="monokai"
								width="100%"
								height="300px"
								:options="{
									enableBasicAutocompletion: true,
									enableLiveAutocompletion: true,
									fontSize: 14,
									highlightActiveLine: true,
									enableSnippets: true,
									showLineNumbers: true,
									tabSize: 2,
									showPrintMargin: false,
									showGutter: true,
								}"
								@keydown.native.stop
							/>
						</c-col>
						<?php
					}
					?>
					</div>

				</c-tab>

				

			</c-tabs>
			</div>
		</c-form>
	</c-container>
</div>
<div id="gdpr-mascot-app"></div>
<?php
