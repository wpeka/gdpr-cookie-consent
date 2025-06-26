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
$credit_link_text = __( 'WP Cookie consent', 'gdpr-cookie-consent' );

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
?>

<?php
$gdpr_no_of_page_scan_left       = $total_pages_scan_limit - get_option( 'gdpr_no_of_page_scan' );
$remaining_percentage_scan_limit = ( get_option( 'gdpr_no_of_page_scan' ) / $total_pages_scan_limit ) * 100;

?>
<div id="gdpr-before-mount" style="top:0;left:0;right:0;left:0;height:100%;width:100%;position:fixed;background-color:white;z-index:999"></div>
<div class="gdpr-cookie-consent-app-container" id="gdpr-cookie-consent-settings-app">
	<!-- main preview container -->
	<div v-if="banner_preview_is_on">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/cookie_settings.php'; ?>
	</div>

	<!-- Preview banner code restructure -->
	<div v-show="banner_preview_is_on && show_cookie_as == 'popup'" class="gdpr-popup-overlay">
	</div>
	<?php if ( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true' ) { ?> 
		<!-- AB TESTING ENABLED -->
		<div v-show="banner_preview_is_on" class="notice-container" :class="{ 'notice-type-banner': show_cookie_as == 'banner', 'notice-type-popup': show_cookie_as == 'popup', 'notice-type-widget': show_cookie_as == 'widget', 'banner-top': cookie_position == 'top' && show_cookie_as == 'banner' ,'banner-bottom': cookie_position == 'bottom' && show_cookie_as == 'banner', 'widget-left': cookie_widget_position == 'left' && show_cookie_as == 'widget','widget-right': cookie_widget_position == 'right' && show_cookie_as == 'widget', 'widget-top-right': cookie_widget_position == 'top_right' && show_cookie_as == 'widget', 'widget-top-left': cookie_widget_position == 'top_left' && show_cookie_as == 'widget' }"
		  :style="{
		  	'background-color': this[`cookie_bar_color${active_test_banner_tab}`] + Math.floor(this[`cookie_bar_opacity${active_test_banner_tab}`] * 255).toString(16).toUpperCase(),
			'color': this[`cookie_text_color${active_test_banner_tab}`],
		  	'border-style': this[`border_style${active_test_banner_tab}`],
			'border-width': this[`cookie_bar_border_width${active_test_banner_tab}`] + 'px',
			'border-radius': this[`cookie_bar_border_radius${active_test_banner_tab}`] + 'px',
			'border-color': this[`cookie_border_color${active_test_banner_tab}`]
		  }"
		>
			<div v-show="ab_testing_enabled && ( active_test_banner_tab == 1 || active_test_banner_tab == 2 )" class="notice-content" :class="'notice-template-' + template"
			:style="{
			  'width': '100%',
			  'border-radius': this[`cookie_bar_border_radius${active_test_banner_tab}`] + 'px',
			}"
			>
			<span :style="{ 'border': 'none', 'cursor': 'pointer', 'display':'inline-flex','justify-content': 'center', 'align-items': 'center', 'height':'20px', 'width': '20px', 'position': 'absolute', 'top': (parseInt(this[`cookie_bar_border_radius${active_test_banner_tab}`])/3 + 10) + 'px', 'right': (parseInt(this[`cookie_bar_border_radius${active_test_banner_tab}`])/3 + 10) + 'px', 'border-radius': '50%', 'color': cookieSettingsPopupAccentColor, 'background-color': 'transparent' }" @click="turnOffPreviewBanner">
				<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z" fill="currentColor"/>
				</svg>
			</span>
				<div class="notice-logo-container">
					<div v-if="active_test_banner_tab == 1">
					<?php
						$get_banner_img1 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1 );
						if ( ! empty( $get_banner_img1 ) ) {
						?>
							<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img1 ); ?>"
							  :style="{
							  	'margin-left': json_templates[template]?.['logo']?.['margin-left'],
								'width': json_templates[template]?.['logo']?.['fit-content'],
								'height': json_templates[template]?.['logo']?.['height'],
								'transform': json_templates[template]?.['logo']?.['transform']
							  }" >
						<?php
					}
					?>
					</div>
					<div v-if="active_test_banner_tab == 2">
					<?php
						$get_banner_img2 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD2 );
						if ( ! empty( $get_banner_img2 ) ) {
						?>
							<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img2 ); ?>"
							:style="{
							  	'margin-left': json_templates[template]?.['logo']?.['margin-left'],
								'width': json_templates[template]?.['logo']?.['fit-content'],
								'height': json_templates[template]?.['logo']?.['height'],
								'transform': json_templates[template]?.['logo']?.['transform']
							  }"  >
						<?php
					}
					?>
					</div>
				</div>
				
				<div class="notice-heading-wrapper">
						<h3 :style = "{ 'text-align': json_templates[template]?.['heading']?.['text-align'] }" v-if="gdpr_message_heading.length>0 && is_gdpr">{{gdpr_message_heading}}</h3>
						<h3 :style = "{ 'text-align': json_templates[template]?.['heading']?.['text-align'] }"  v-if="lgpd_message_heading.length>0 && is_lgpd">{{lgpd_message_heading}}</h3>
				</div>	
				<div class="notice-content-body" :class="'notice-template-name-' + json_templates[template]?.name + ' template-' + json_templates[template]?.['static-settings']?.['layout']">
					<p>	
						<span :style= "{'font-family': this[`cookie_font${active_test_banner_tab}`]}" v-show="is_gdpr" v-html ="gdpr_message"></span>
						<span :style= "{'font-family': this[`cookie_font${active_test_banner_tab}`]}" v-show="is_lgpd" v-html ="lgpd_message"></span>
						<span :style= "{'font-family': this[`cookie_font${active_test_banner_tab}`]}" v-show="is_ccpa" v-html ="ccpa_message"></span>
						<span :style= "{'font-family': this[`cookie_font${active_test_banner_tab}`]}" v-show="is_eprivacy" v-html ="eprivacy_message"></span>
						<a  v-if="!is_ccpa" :style="{ 
							'font-family': this[`cookie_font${active_test_banner_tab}`],
							'color':button_readmore_link_color,
							'border-style': button_readmore_as_button ? button_readmore_button_border_style : 'none', 
							'border-width': button_readmore_as_button ? button_readmore_button_border_width + 'px':'0', 
							'border-color': button_readmore_as_button ? button_readmore_button_border_color : 'transparent', 
							'border-radius': button_readmore_as_button ? button_readmore_button_border_radius+'px' : '0px',
							'background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent',
							 ...(button_readmore_as_button ? {
							 'display': 'block',
							 'width': 'fit-content',
							 'margin-top': '5px',
							 'padding': json_templates[template]?.['static-settings']?.[`button_${button_readmore_button_size}_padding`]
							} : { 'display': 'inline-block',
							  }) 
						}" >
							 
							 <span>{{ button_readmore_text }}</span>
						</a>
						<a  v-if="is_ccpa" :style="{'font-family': this[`cookie_font${active_test_banner_tab}`],'color':this[`opt_out_text_color${active_test_banner_tab}`]}"><span>{{ opt_out_text }}</span></a>
					</p>

					<div  v-if="ab_testing_enabled && !is_ccpa" class="notice-buttons-wrapper" :class="'template-' + json_templates[template]?.['static-settings']?.['layout'] + '-buttons'">
						<div  v-show="template != 'blue_full' || ( this[`cookie_decline_on${active_test_banner_tab}`] || (this[`cookie_settings_on${active_test_banner_tab} `] && !is_eprivacy))" class="notice-left-buttons">
							<a v-show="( active_test_banner_tab == 1 || active_test_banner_tab == 2 ) && this[`cookie_decline_on${active_test_banner_tab}`]"
							  href="#"
							  :style="{
								  'background-color': this[`decline_as_button${active_test_banner_tab}`]
								    ? `${this[`decline_background_color${active_test_banner_tab}`]}${Math.floor(this[`decline_opacity${active_test_banner_tab}`] * 255).toString(16).toUpperCase()}`
								    : 'transparent',
  								  'color': this[`decline_text_color${active_test_banner_tab}`],
  								  'border-style': this[`decline_as_button${active_test_banner_tab}`] ? this[`decline_style${active_test_banner_tab}`] : 'none',
    							  'border-width': this[`decline_as_button${active_test_banner_tab}`] ? this[`decline_border_width${active_test_banner_tab}`] + 'px' : '0',
    							  'border-color': this[`decline_as_button${active_test_banner_tab}`] ? this[`decline_border_color${active_test_banner_tab}`] : 'transparent',
    							  'border-radius': this[`decline_as_button${active_test_banner_tab}`] ? this[`decline_border_radius${active_test_banner_tab}`] + 'px' : '0',
    							  'font-family': this[`cookie_font${active_test_banner_tab}`],
								  ...(this[`cookie_decline_on${active_test_banner_tab}`] ? {
  								    'min-width': json_templates[template]['decline_button']['min-width'],
									'width': json_templates[template]['decline_button']?.['width'],
  								    'display': json_templates[template]['decline_button']['display'],
  								    'justify-content': json_templates[template]['decline_button']['justify-content'],
  								    'align-items': json_templates[template]['decline_button']['align-items'],
  								    'text-align': json_templates[template]['decline_button']['text-align'],
									'padding': json_templates[template]['static-settings'][`button_${active_test_banner_tab == '1' ? decline_size1 : decline_size2}_padding`]
  								  } : {})
  								}"
							>
							  {{ this[`decline_text${active_test_banner_tab}`] }}
							</a>

							<a v-show="( active_test_banner_tab == 1 || active_test_banner_tab == 2 ) && this[`cookie_settings_on${active_test_banner_tab} `] && !is_eprivacy"
							  id="cookie_action_settings_preview"
							  href="#"
							  :style="{
								  'background-color': this[`settings_as_button${active_test_banner_tab}`]
								    ? `${this[`settings_background_color${active_test_banner_tab}`]}${Math.floor(this[`settings_opacity${active_test_banner_tab}`] * 255).toString(16).toUpperCase()}`
								    : 'transparent',
  								  'color': this[`settings_text_color${active_test_banner_tab}`],
  								  'border-style': this[`settings_as_button${active_test_banner_tab}`] ? this[`settings_style${active_test_banner_tab}`] : 'none',
    							  'border-width': this[`settings_as_button${active_test_banner_tab}`] ? this[`settings_border_width${active_test_banner_tab}`] + 'px' : '0',
    							  'border-color': this[`settings_as_button${active_test_banner_tab}`] ? this[`settings_border_color${active_test_banner_tab}`] : 'transparent',
    							  'border-radius': this[`settings_as_button${active_test_banner_tab}`] ? this[`settings_border_radius${active_test_banner_tab}`] + 'px' : '0',
    							  'font-family': this[`cookie_font${active_test_banner_tab}`],
								  ...(this[`cookie_settings_on${active_test_banner_tab}`] && !is_eprivacy ? {
  								    'min-width': json_templates[template]?.['settings_button']['min-width'],
									'width': json_templates[template]?.['settings_button']?.['width'],
  								    'display': json_templates[template]?.['settings_button']['display'],
  								    'justify-content': json_templates[template]?.['settings_button']?.['justify-content'],
  								    'align-items': json_templates[template]?.['settings_button']?.['align-items'],
  								    'text-align': json_templates[template]?.['settings_button']?.['text-align'],
									'padding': json_templates[template]?.['static-settings']?.[`button_${active_test_banner_tab == '1' ? settings_size1 : settings_size2}_padding`]
  								  } : {})
  								}"
							>
							  {{ this[`settings_text${active_test_banner_tab}`] }}
							</a>
						</div>

						<div v-show="template != 'blue_full' || ( this[`cookie_accept_on${active_test_banner_tab}`] || this[`cookie_accept_all_on${active_test_banner_tab}`])" class="notice-right-buttons">
							<a v-show="( active_test_banner_tab == 1 || active_test_banner_tab == 2 ) && this[`cookie_accept_on${active_test_banner_tab}`]"
							  href="#"
							  :style="{
								  'background-color': this[`accept_as_button${active_test_banner_tab}`]
								    ? `${this[`accept_background_color${active_test_banner_tab}`]}${Math.floor(this[`accept_opacity${active_test_banner_tab}`] * 255).toString(16).toUpperCase()}`
								    : 'transparent',
  								  'color': this[`accept_text_color${active_test_banner_tab}`],
  								  'border-style': this[`accept_as_button${active_test_banner_tab}`] ? this[`accept_style${active_test_banner_tab}`] : 'none',
    							  'border-width': this[`accept_as_button${active_test_banner_tab}`] ? this[`accept_border_width${active_test_banner_tab}`] + 'px' : '0',
    							  'border-color': this[`accept_as_button${active_test_banner_tab}`] ? this[`accept_border_color${active_test_banner_tab}`] : 'transparent',
    							  'border-radius': this[`accept_as_button${active_test_banner_tab}`] ? this[`accept_border_radius${active_test_banner_tab}`] + 'px' : '0',
    							  'font-family': this[`cookie_font${active_test_banner_tab}`],
								  ...(this[`cookie_accept_on${active_test_banner_tab}`] ? {
  								    'min-width': json_templates[template]?.['accept_button']?.['min-width'],
									'width': json_templates[template]['accept_button']?.['width'],
  								    'display': json_templates[template]?.['accept_button']?.['display'],
  								    'justify-content': json_templates[template]?.['accept_button']?.['justify-content'],
  								    'align-items': json_templates[template]?.['accept_button']?.['align-items'],
  								    'text-align': json_templates[template]?.['accept_button']?.['text-align'],
									'padding': json_templates[template]?.['static-settings']?.[`button_${active_test_banner_tab == '1' ? accept_size1 : accept_size2}_padding`]
  								  } : {})
  								}"
							>
							  {{ this[`accept_text${active_test_banner_tab}`] }}
							</a>

							<a v-show="( active_test_banner_tab == 1 || active_test_banner_tab == 2 ) && this[`cookie_accept_all_on${active_test_banner_tab}`]"
							  href="#"
							  :style="{
								  'background-color': this[`accept_all_as_button${active_test_banner_tab}`]
								    ? `${this[`accept_all_background_color${active_test_banner_tab}`]}${Math.floor(this[`accept_all_opacity${active_test_banner_tab}`] * 255).toString(16).toUpperCase()}`
								    : 'transparent',
  								  'color': this[`accept_all_text_color${active_test_banner_tab}`],
  								  'border-style': this[`accept_all_as_button${active_test_banner_tab}`] ? this[`accept_all_style${active_test_banner_tab}`] : 'none',
    							  'border-width': this[`accept_all_as_button${active_test_banner_tab}`] ? this[`accept_all_border_width${active_test_banner_tab}`] + 'px' : '0',
    							  'border-color': this[`accept_all_as_button${active_test_banner_tab}`] ? this[`accept_all_border_color${active_test_banner_tab}`] : 'transparent',
    							  'border-radius': this[`accept_all_as_button${active_test_banner_tab}`] ? this[`accept_all_border_radius${active_test_banner_tab}`] + 'px' : '0',
    							  'font-family': this[`cookie_font${active_test_banner_tab}`],
								  ...(this[`cookie_accept_all_on${active_test_banner_tab}`] ? {
  								    'min-width': json_templates[template]?.['accept_all_button']?.['min-width'],
									'width': json_templates[template]['accept_all_button']?.['width'],
  								    'display': json_templates[template]?.['accept_all_button']?.['display'],
  								    'justify-content': json_templates[template]?.['accept_all_button']?.['justify-content'],
  								    'align-items': json_templates[template]?.['accept_all_button']?.['align-items'],
  								    'text-align': json_templates[template]?.['accept_all_button']?.['text-align'],
									'padding': json_templates[template]?.['static-settings']?.[`button_${active_test_banner_tab == '1' ? accept_all_size1 : accept_all_size2}_padding`]
  								  } : {})
  								}"
							>
							  {{ this[`accept_all_text${active_test_banner_tab}`] }}
							</a>
						</div>
					</div>			
				</div>
				<div v-show="show_credits" class="powered-by-credits"  :style="{'--popup_accent_color': cookieSettingsPopupAccentColor, 'text-align':'center', 'font-size': '10px', 'margin-bottom':'-10px'}"><?php echo wp_kses_post( $credit_link  ); ?></div>
				
			</div>
		</div>
	<?php } elseif ( $ab_options['ab_testing_enabled'] === false || $ab_options['ab_testing_enabled'] === 'false' ) { ?>
		<div v-if="banner_preview_is_on && gdpr_policy !== 'both'" class="notice-container" :class="{ 'notice-type-banner': show_cookie_as == 'banner', 'notice-type-popup': show_cookie_as == 'popup', 'notice-type-widget': show_cookie_as == 'widget', 'banner-top': cookie_position == 'top' && show_cookie_as == 'banner' ,'banner-bottom': cookie_position == 'bottom' && show_cookie_as == 'banner', 'widget-left': cookie_widget_position == 'left' && show_cookie_as == 'widget','widget-right': cookie_widget_position == 'right' && show_cookie_as == 'widget', 'widget-top-right': cookie_widget_position == 'top_right' && show_cookie_as == 'widget', 'widget-top-left': cookie_widget_position == 'top_left' && show_cookie_as == 'widget' }"
			:style="{
				'background-color': `${cookie_bar_color}${Math.floor(cookie_bar_opacity * 255).toString(16).toUpperCase()}`,
				'color': cookie_text_color,
				'border-style': border_style,
				'border-width': cookie_bar_border_width + 'px',
				'border-radius': cookie_bar_border_radius + 'px',
				'border-color': cookie_border_color
			}"
		>
			<div class="notice-content" :class="'notice-template-' + template"
			  :style="{
			  	'width': '100%',
				'border-radius': cookie_bar_border_radius + 'px',
			  }"
			>
				<span :style="{ 'border': 'none', 'cursor': 'pointer', 'display':'inline-flex','justify-content': 'center', 'align-items': 'center', 'height':'20px', 'width': '20px', 'position': 'absolute', 'top': (parseInt(cookie_bar_border_radius)/3 + 10) + 'px', 'right': (parseInt(cookie_bar_border_radius)/3 + 10) + 'px', 'border-radius': '50%','color': cookieSettingsPopupAccentColor, 'background-color':'transparent' }" @click="turnOffPreviewBanner">
					<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z" fill="currentColor"/>
					</svg>
				</span>
				<div class="notice-logo-container">
				<?php
					$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
					if ( ! empty( $get_banner_img ) ) {
					?>
						<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>"
						:style="{
						  	'margin-left': json_templates[template]?.['logo']?.['margin-left'],
							'width': json_templates[template]?.['logo']?.['fit-content'],
							'height': json_templates[template]?.['logo']?.['height'],
							'transform': json_templates[template]?.['logo']?.['transform']
						  }"  >
					<?php
				}
				?>
				</div>	
				<div class="notice-heading-wrapper">
						<h3 :style = "{ 'text-align': json_templates[template]?.['heading']?.['text-align'], 'font-family': cookie_font }" v-if="gdpr_message_heading.length>0 && is_gdpr">{{gdpr_message_heading}}</h3>
						<h3 :style = "{ 'text-align': json_templates[template]?.['heading']?.['text-align'], 'font-family': cookie_font }"  v-if="lgpd_message_heading.length>0 && is_lgpd">{{lgpd_message_heading}}</h3>
				</div>	
				<div class="notice-content-body" :class="'notice-template-name-' + json_templates[template]?.name + ' template-' + json_templates[template]?.['static-settings']?.['layout']">
					<p>	
						<span :style="{'font-family': cookie_font}" v-show="is_gdpr" v-html ="gdpr_message"></span>
						<span :style="{'font-family': cookie_font}" v-show="is_lgpd" v-html ="lgpd_message"></span>
						<span :style="{'font-family': cookie_font}" v-show="is_ccpa" v-html ="ccpa_message"></span>
						<span :style="{'font-family': cookie_font}" v-show="is_eprivacy" v-html ="eprivacy_message"></span>
						<a v-if="!is_ccpa" :style="{ 
							'font-family': cookie_font,
							'color':button_readmore_link_color,
							'border-style': button_readmore_as_button ? button_readmore_button_border_style : 'none', 
							'border-width': button_readmore_as_button ? button_readmore_button_border_width + 'px':'0', 
							'border-color': button_readmore_as_button ? button_readmore_button_border_color : 'transparent', 
							'border-radius': button_readmore_as_button ? button_readmore_button_border_radius+'px' : '0px',
							'background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent',
							 ...(button_readmore_as_button ? {
							 'display': 'block',
							 'width': 'fit-content',
							 'margin-top': '5px',
							 'padding': json_templates[template]?.['static-settings']?.[`button_${button_readmore_button_size}_padding`]
							} : { 'display': 'inline-block',
							  })
						}" >
							<span>{{ button_readmore_text }}</span>
						</a>
						<a  v-if="is_ccpa" :style="{'font-family': cookie_font,'color': opt_out_text_color}"><span>{{ opt_out_text }}</span></a>
					</p>

					<div v-show="!is_ccpa" class="notice-buttons-wrapper" :class="'template-' + json_templates[template]?.['static-settings']?.['layout'] + '-buttons'">
						<div v-show="template != 'blue_full' || (cookie_decline_on || (cookie_settings_on && !is_eprivacy))" class="notice-left-buttons">
							<a v-show="cookie_decline_on"
							  href="#"
							  :style="{
  								  'background-color': decline_as_button ? `${decline_background_color}${Math.floor(decline_opacity * 255).toString(16).toUpperCase()}` : 'transparent',
  								  'color': decline_text_color,
  								  'border-style': decline_as_button ? decline_style : 'none',
  								  'border-width': decline_as_button ? decline_border_width + 'px' : '0',
  								  'border-color': decline_as_button ? decline_border_color : 'transparent',
  								  'border-radius': decline_as_button ? decline_border_radius + 'px' : '0',
  								  'font-family': cookie_font,
								  ...(cookie_decline_on ? {
  								    'min-width': json_templates[template]?.['decline_button']?.['min-width'],
									'width': json_templates[template]?.['decline_button']?.['width'],
  								    'display': json_templates[template]?.['decline_button']?.['display'],
  								    'justify-content': json_templates[template]?.['decline_button']?.['justify-content'],
  								    'align-items': json_templates[template]?.['decline_button']?.['align-items'],
  								    'text-align': json_templates[template]?.['decline_button']?.['text-align'],
									'padding': json_templates[template]?.['static-settings']?.[`button_${decline_size}_padding`]
  								  } : {})
  								}"
							>
							  {{ decline_text }}
							</a>

							<a v-show="cookie_settings_on && !is_eprivacy" id="cookie_action_settings_preview"
							  href="#"
							  :style="{
  								  'background-color': settings_as_button ? `${settings_background_color}${Math.floor(settings_opacity * 255).toString(16).toUpperCase()}` : 'transparent',
  								  'color': settings_text_color,
  								  'border-style': settings_as_button ? settings_style : 'none',
  								  'border-width': settings_as_button ? settings_border_width + 'px' : '0',
  								  'border-color': settings_as_button ? settings_border_color : 'transparent',
  								  'border-radius': settings_as_button ? settings_border_radius + 'px' : '0',
  								  'font-family': cookie_font,
								  ...(cookie_settings_on && !is_eprivacy ? {
  								    'min-width': json_templates[template]?.['settings_button']?.['min-width'],
									'width': json_templates[template]?.['settings_button']?.['width'],
  								    'display': json_templates[template]?.['settings_button']?.['display'],
  								    'justify-content': json_templates[template]?.['settings_button']?.['justify-content'],
  								    'align-items': json_templates[template]?.['settings_button']?.['align-items'],
  								    'text-align': json_templates[template]?.['settings_button']?.['text-align'],
									'padding': json_templates[template]?.['static-settings']?.[`button_${settings_size}_padding`]
  								  } : {})
  								}"
							>
								{{ settings_text }}
							</a>
						</div>

						<div  v-show="template != 'blue_full' || (cookie_accept_on || cookie_accept_all_on)" class="notice-right-buttons">
							<a v-show="cookie_accept_on" 
							  href="#"
							  :style="{
  								  'background-color': accept_as_button ? `${accept_background_color}${Math.floor(accept_opacity * 255).toString(16).toUpperCase()}` : 'transparent',
  								  'color': accept_text_color,
  								  'border-style': accept_as_button ? accept_style : 'none', 
  								  'border-width': accept_as_button ? accept_border_width + 'px' : '0',
  								  'border-color': accept_as_button ? accept_border_color : 'transparent',
  								  'border-radius': accept_as_button ? accept_border_radius + 'px' : '0',
  								  'font-family': cookie_font,
								  ...(cookie_accept_on ? {
  								    'min-width': json_templates[template]?.['accept_button']?.['min-width'],
									'width': json_templates[template]?.['accept_button']?.['width'],
  								    'display': json_templates[template]?.['accept_button']['display'],
  								    'justify-content': json_templates[template]?.['accept_button']?.['justify-content'],
  								    'align-items': json_templates[template]?.['accept_button']?.['align-items'],
  								    'text-align': json_templates[template]?.['accept_button']?.['text-align'],
									'padding': json_templates[template]?.['static-settings']?.[`button_${accept_size}_padding`]
  								  } : {})
  								}"
							>
								{{ accept_text }}
							</a>

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
								  ...(cookie_accept_all_on ? {
  								    'min-width': json_templates[template]?.['accept_all_button']?.['min-width'],
									'width': json_templates[template]['accept_all_button']?.['width'],
  								    'display': json_templates[template]?.['accept_all_button']?.['display'],
  								    'justify-content': json_templates[template]?.['accept_all_button']?.['justify-content'],
  								    'align-items': json_templates[template]?.['accept_all_button']?.['align-items'],
  								    'text-align': json_templates[template]?.['accept_all_button']?.['text-align'],
									'padding': json_templates[template]?.['static-settings']?.[`button_${accept_all_size}_padding`]
  								  } : {})
  								}"
							>
								{{ accept_all_text }}
							</a>
						</div>
					</div>
				</div>
				<div v-show="show_credits" class="powered-by-credits"  :style="{'--popup_accent_color': cookieSettingsPopupAccentColor, 'text-align':'center', 'font-size': '10px', 'margin-bottom':'-10px'}"><?php echo wp_kses_post($credit_link  ); ?></div>
				
			</div>
		</div>

		<div v-else-if="banner_preview_is_on && gdpr_policy === 'both'" class="notice-container" :class="{ 'notice-type-banner': show_cookie_as == 'banner', 'notice-type-popup': show_cookie_as == 'popup', 'notice-type-widget': show_cookie_as == 'widget', 'banner-top': cookie_position == 'top' && show_cookie_as == 'banner' ,'banner-bottom': cookie_position == 'bottom' && show_cookie_as == 'banner', 'widget-left': cookie_widget_position == 'left' && show_cookie_as == 'widget','widget-right': cookie_widget_position == 'right' && show_cookie_as == 'widget', 'widget-top-right': cookie_widget_position == 'top_right' && show_cookie_as == 'widget', 'widget-top-left': cookie_widget_position == 'top_left' && show_cookie_as == 'widget' }"
			:style="{
				'background-color': active_default_multiple_legislation === 'gdpr' ? `${multiple_legislation_cookie_bar_color1}${Math.floor(multiple_legislation_cookie_bar_opacity1 * 255).toString(16).toUpperCase()}` : `${multiple_legislation_cookie_bar_color2}${Math.floor(multiple_legislation_cookie_bar_opacity2 * 255).toString(16).toUpperCase()}`,
				'color': active_default_multiple_legislation === 'gdpr' ? multiple_legislation_cookie_text_color1 : multiple_legislation_cookie_text_color2,
				'border-style': active_default_multiple_legislation === 'gdpr' ? multiple_legislation_border_style1 : multiple_legislation_border_style2,
				'border-width': active_default_multiple_legislation === 'gdpr' ? multiple_legislation_cookie_bar_border_width1 + 'px' : multiple_legislation_cookie_bar_border_width2 + 'px',
				'border-radius': active_default_multiple_legislation === 'gdpr' ? multiple_legislation_cookie_bar_border_radius1 + 'px' : multiple_legislation_cookie_bar_border_radius2 + 'px',
				'border-color': active_default_multiple_legislation === 'gdpr' ? multiple_legislation_cookie_border_color1 : multiple_legislation_cookie_border_color2,
			}"
		>
			<div class="notice-content" :class="'notice-template-' + template"
			  :style="{
				'width': '100%',
				'border-radius': active_default_multiple_legislation === 'gdpr' ? multiple_legislation_cookie_bar_border_radius1 + 'px' : multiple_legislation_cookie_bar_border_radius2 + 'px',
			  }"
			>
				<span :style="{ 'border': 'none', 'cursor': 'pointer', 'display':'inline-flex','justify-content': 'center', 'align-items': 'center', 'height':'20px', 'width': '20px', 'position': 'absolute', 'top': (parseInt( active_default_multiple_legislation === 'gdpr' ? multiple_legislation_cookie_bar_border_radius1 : multiple_legislation_cookie_bar_border_radius2 )/3 + 10) + 'px', 'right': (parseInt( active_default_multiple_legislation === 'gdpr' ? multiple_legislation_cookie_bar_border_radius1 : multiple_legislation_cookie_bar_border_radius2 )/3 + 10) + 'px', 'border-radius': '50%', 'color': cookieSettingsPopupAccentColor, 'background-color': 'transparent' }" @click="turnOffPreviewBanner">
					<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z" fill="currentColor"/>
					</svg>
				</span>
				<div class="notice-logo-container">
				<?php
					$get_banner_imgml1 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELDML1 );
					if ( ! empty( $get_banner_imgml1 ) ) {
					?>
						<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_imgml1 ); ?>"
						:style="{
						  	'margin-left': json_templates[template]?.['logo']?.['margin-left'],
							'width': json_templates[template]?.['logo']?.['fit-content'],
							'height': json_templates[template]?.['logo']?.['height'],
							'transform': json_templates[template]?.['logo']?.['transform']
						  }"  >
					<?php
					}
					?>
				</div>	
				<div class="notice-heading-wrapper">
						<h3 :style = "{ 'text-align': json_templates[template]?.['heading']?.['text-align'] }" v-if="gdpr_message_heading.length>0">{{gdpr_message_heading}}</h3>
				</div>	
				<div class="notice-content-body" :class="'notice-template-name-' + json_templates[template]?.name + ' template-' + json_templates[template]?.['static-settings']?.['layout']">
					<p>	
						<span :style="{'font-family': multiple_legislation_cookie_font1}" v-show="active_default_multiple_legislation === 'gdpr'" v-html ="gdpr_message"></span>
						<span :style="{'font-family': multiple_legislation_cookie_font2}" v-show="active_default_multiple_legislation === 'ccpa'" v-html ="ccpa_message"></span>
						<a v-if="active_default_multiple_legislation === 'gdpr'" :style="{ 
							'font-family': multiple_legislation_cookie_font1,
							'color':button_readmore_link_color,
							'border-style': button_readmore_as_button ? button_readmore_button_border_style : 'none', 
							'border-width': button_readmore_as_button ? button_readmore_button_border_width + 'px':'0', 
							'border-color': button_readmore_as_button ? button_readmore_button_border_color : 'transparent', 
							'border-radius': button_readmore_as_button ? button_readmore_button_border_radius+'px' : '0px',
							'background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent',
							 ...(button_readmore_as_button ? {
							 'display': 'block',
							 'width': 'fit-content',
							 'margin-top': '5px',
							 'padding': json_templates[template]?.['static-settings']?.[`button_${button_readmore_button_size}_padding`]
							} : { 'display': 'inline-block',
							  })
						}" >
							<span>{{ button_readmore_text }}</span>
							
						</a>
						<a v-if="active_default_multiple_legislation === 'ccpa'" :style="{'font-family': multiple_legislation_cookie_font2, 'color':opt_out_text_color1,}"><span>{{ opt_out_text }}</span></a>
					</p>

					<div v-show="active_default_multiple_legislation === 'gdpr'" class="notice-buttons-wrapper" :class="'template-' + json_templates[template]?.['static-settings']?.['layout'] + '-buttons'">
						<div v-show="template != 'blue_full' || (cookie_decline_on1 || cookie_settings_on1)" class="notice-left-buttons">
							<a v-show="cookie_decline_on1"
							  href="#"
							  :style="{
  								  'background-color': decline_as_button1 ? `${decline_background_color1}${Math.floor(decline_opacity1 * 255).toString(16).toUpperCase()}` : 'transparent',
  								  'color': decline_text_color1,
  								  'border-style': decline_as_button1 ? decline_style1 : 'none',
  								  'border-width': decline_as_button1 ? decline_border_width1 + 'px' : '0',
  								  'border-color': decline_as_button1 ? decline_border_color1 : 'transparent',
  								  'border-radius': decline_as_button1 ? decline_border_radius1 + 'px' : '0',
  								  'font-family': active_default_multiple_legislation === 'gdpr' ? multiple_legislation_cookie_font1 : multiple_legislation_cookie_font2,
								  ...(cookie_decline_on1 ? {
  								    'min-width': json_templates[template]?.['decline_button']?.['min-width'],
									'width': json_templates[template]?.['decline_button']?.['width'],
  								    'display': json_templates[template]?.['decline_button']?.['display'],
  								    'justify-content': json_templates[template]?.['decline_button']?.['justify-content'],
  								    'align-items': json_templates[template]?.['decline_button']?.['align-items'],
  								    'text-align': json_templates[template]?.['decline_button']?.['text-align'],
									'padding': json_templates[template]?.['static-settings']?.[`button_${decline_size}_padding`]
  								  } : {})
  								}"
							>
							  {{ decline_text1 }}
							</a>

							<a v-show="cookie_settings_on1 && !is_eprivacy" id="cookie_action_settings_preview"
							  href="#"
							  :style="{
  								  'background-color': settings_as_button1 ? `${settings_background_color1}${Math.floor(settings_opacity1 * 255).toString(16).toUpperCase()}` : 'transparent',
  								  'color': settings_text_color1,
  								  'border-style': settings_as_button1 ? settings_style1 : 'none',
  								  'border-width': settings_as_button1 ? settings_border_width1 + 'px' : '0',
  								  'border-color': settings_as_button1 ? settings_border_color1 : 'transparent',
  								  'border-radius': settings_as_button1 ? settings_border_radius1 + 'px' : '0',
  								  'font-family': active_default_multiple_legislation === 'gdpr' ? multiple_legislation_cookie_font1 : multiple_legislation_cookie_font2,
								  ...(cookie_settings_on1 ? {
  								    'min-width': json_templates[template]?.['settings_button']?.['min-width'],
									'width': json_templates[template]?.['settings_button']?.['width'],
  								    'display': json_templates[template]?.['settings_button']?.['display'],
  								    'justify-content': json_templates[template]?.['settings_button']?.['justify-content'],
  								    'align-items': json_templates[template]?.['settings_button']?.['align-items'],
  								    'text-align': json_templates[template]?.['settings_button']?.['text-align'],
									'padding': json_templates[template]?.['static-settings']?.[`button_${settings_size}_padding`]
  								  } : {})
  								}"
							>
								{{ settings_text1 }}
							</a>
						</div>

						<div v-show="template != 'blue_full' || (cookie_accept_on1 || cookie_accept_all_on1)" class="notice-right-buttons">
							<a v-show="cookie_accept_on1" 
							  href="#"
							  :style="{
  								  'background-color': accept_as_button1 ? `${accept_background_color1}${Math.floor(accept_opacity1 * 255).toString(16).toUpperCase()}` : 'transparent',
  								  'color': accept_text_color1,
  								  'border-style': accept_as_button1 ? accept_style1 : 'none',
  								  'border-width': accept_as_button1 ? accept_border_width1 + 'px' : '0',
  								  'border-color': accept_as_button1 ? accept_border_color1 : 'transparent',
  								  'border-radius': accept_as_button1 ? accept_border_radius1 + 'px' : '0',
  								  'font-family': active_default_multiple_legislation === 'gdpr' ? multiple_legislation_cookie_font1 : multiple_legislation_cookie_font2,
								  ...(cookie_accept_on1 ? {
  								    'min-width': json_templates[template]?.['accept_button']?.['min-width'],
									'width': json_templates[template]?.['accept_button']?.['width'],
  								    'display': json_templates[template]?.['accept_button']['display'],
  								    'justify-content': json_templates[template]?.['accept_button']?.['justify-content'],
  								    'align-items': json_templates[template]?.['accept_button']?.['align-items'],
  								    'text-align': json_templates[template]?.['accept_button']?.['text-align'],
									'padding': json_templates[template]?.['static-settings']?.[`button_${accept_size}_padding`]
  								  } : {})
  								}"
							>
								{{ accept_text1 }}
							</a>

							<a v-show="cookie_accept_all_on1" 
							  href="#"
							  :style="{
  								  'background-color': accept_all_as_button1 ? `${accept_all_background_color1}${Math.floor(accept_all_opacity1 * 255).toString(16).toUpperCase()}` : 'transparent',
  								  'color': accept_all_text_color1,
  								  'border-style': accept_all_style1,
  								  'border-width': accept_all_border_width1 + 'px',
  								  'border-color': accept_all_border_color1,
  								  'border-radius': accept_all_border_radius1 + 'px',
  								  'font-family': active_default_multiple_legislation === 'gdpr' ? multiple_legislation_cookie_font1 : multiple_legislation_cookie_font2,
								  ...(cookie_accept_all_on1 ? {
  								    'min-width': json_templates[template]?.['accept_all_button']?.['min-width'],
									'width': json_templates[template]['accept_all_button']?.['width'],
  								    'display': json_templates[template]?.['accept_all_button']?.['display'],
  								    'justify-content': json_templates[template]?.['accept_all_button']?.['justify-content'],
  								    'align-items': json_templates[template]?.['accept_all_button']?.['align-items'],
  								    'text-align': json_templates[template]?.['accept_all_button']?.['text-align'],
									'padding': json_templates[template]?.['static-settings']?.[`button_${accept_all_size}_padding`]
  								  } : {})
  								}"
							>
								{{ accept_all_text1 }}
							</a>
						</div>
					</div>
				</div>
				
				<div v-show="show_credits" class="powered-by-credits"  :style="{'--popup_accent_color': cookieSettingsPopupAccentColor, 'text-align':'center', 'font-size': '10px', 'margin-bottom':'-10px'}"><?php echo wp_kses_post( $credit_link  ); ?></div>
					
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
					<div class="popup-title"><span class="gdpr-remaining-scans-title">Remaining Scans: </span><span><?php echo $gdpr_no_of_page_scan_left; ?> / <?php echo $total_pages_scan_limit; ?><span><span> (<?php echo ceil( $remaining_percentage_scan_limit ); ?>%)</span></div>
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
								<input type="hidden" name="gcc-banner-preview-enable" v-model="banner_preview_is_on">
						</div>
						<c-button :disabled="save_loading" class="gdpr-publish-btn" @click="saveCookieSettings">{{ save_loading ? '<?php esc_html_e( 'Saving...', 'gdpr-cookie-consent' ); ?>' : '<?php esc_html_e( 'Save Changes', 'gdpr-cookie-consent' ); ?>' }}</c-button>
					</div>
			</div>
			<hr id="preview-btn-setting-nav-seperator">
			<c-tabs variant="pills" ref="active_tab" class="gdpr-cookie-consent-settings-nav">

			<!-- COMPLIANCES SECTION START -->
				<c-tab title="<?php esc_attr_e( 'General', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#compliances"id="gdpr-cookie-consent-complianz" >
						<!--  Banner preview  -->
						<c-card class="compliances_card">
							<c-card-body>
								<!-- Cookie Notice Section -->
								<c-row>
									<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice-top"><?php esc_html_e( 'Cookie Notice', 'gdpr-cookie-consent' ); ?></div></c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Cookie Notice', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="cookie_is_on" id="gdpr-cookie-consent-cookie-on" variant="3d"  color="success" :checked="cookie_is_on" v-on:update:checked="onSwitchCookieEnable"></c-switch>
										<input type="hidden" name="gcc-cookie-enable" v-model="cookie_is_on">
									</c-col>
								</c-row>
								<c-row v-show="is_gdpr">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Support IAB TCF v2.2', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="iabtcf_is_on" id="gdpr-cookie-consent-iabtcf-on" variant="3d"  color="success" :checked="iabtcf_is_on" v-on:update:checked="onSwitchIabtcfEnable"></c-switch>
										<input type="hidden" name="gcc-iabtcf-enable" v-model="iabtcf_is_on">
									</c-col>
								</c-row>
								<c-row v-show="is_gdpr && iabtcf_is_on">
									<?php if($api_user_plan == "10sites" || $api_user_plan == "3sites" || $api_user_plan == "10Sites" || $api_user_plan == "3Sites") { ?>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Support Google Additional Consent Mode', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="gacm_is_on" id="gdpr-cookie-consent-gacm-on" variant="3d"  color="success" :checked="gacm_is_on" v-on:update:checked="onSwitchGacmEnable"></c-switch>
										<input type="hidden" name="gcc-gacm-enable" v-model="gacm_is_on">
									</c-col>
									<?php } else if($is_user_connected) { ?>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Support Google Additional Consent Mode', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8 gacm-slider">
										<c-switch v-bind="labelIcon" v-model="gacm_is_on" id="gdpr-cookie-consent-gacm-on" variant="3d"  color="success" :checked="gacm_is_on" v-on:update:checked="onSwitchGacmEnable" disabled></c-switch>
										<input type="hidden" name="gcc-gacm-enable" v-model="gacm_is_on">
										<p class=" gdpr-gacm_message-gdpr">
											<?php esc_attr_e( 'To enable this feature, upgrade to a pro plan', 'gdpr-cookie-consent' ); ?>
										</p>
									</c-col>
									<?php } else { ?>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Support Google Additional Consent Mode', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8 gacm-slider">
										<c-switch v-bind="labelIcon" v-model="gacm_is_on" id="gdpr-cookie-consent-gacm-on" variant="3d"  color="success" :checked="gacm_is_on" v-on:update:checked="onSwitchGacmEnable" disabled></c-switch>
										<input type="hidden" name="gcc-gacm-enable" v-model="gacm_is_on">
										<p class=" gdpr-gacm_message-gdpr">
											<?php esc_attr_e( 'To enable this feature, connect to an account and purchase a paid plan.', 'gdpr-cookie-consent' ); ?>
										</p>
									</c-col>
									<?php }?>
								</c-row>
								<c-row v-show="!is_ccpa || is_gdpr">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Support Google Consent Mode(GCM)', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="gcm_is_on" id="gdpr-cookie-consent-gcm-on" variant="3d"  color="success" :checked="gcm_is_on" v-on:update:checked="onSwitchGCMEnable"></c-switch>
										<input type="hidden" name="gcc-gcm-enable" v-model="gcm_is_on">
									</c-col>
								</c-row>
								<c-row v-show="!is_ccpa || is_gdpr" style="margin-top: -30px;"><c-col class="col-sm-4"></c-col><c-col class="col-sm-8"><p style="color:gray; font-weight:400;">Follow the guide <a class="cookie-notice-readmore" href = "https://wplegalpages.com/docs/wp-cookie-consent/how-to-guides/implementing-google-consent-mode-using-wp-cookie-consent" target="_blank">here</a> to correctly implement Google Consent Mode</p></c-col></c-row>
								<c-row v-show="gcm_is_on" style="border-bottom: 1px solid var(--gray-200);">
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
													<th><?php echo esc_html__('Advertisment') ?></th>
													<th><?php echo esc_html__('Analytics') ?></th>
													<th><?php echo esc_html__('User ad data') ?> </th>
													<th><?php echo esc_html__('Ad personalization data') ?> </th>
													<th><?php echo esc_html__('Functional storage') ?> </th>
													<th><?php echo esc_html__('Personalization storage') ?> </th>
													<th><?php echo esc_html__('Security storage') ?> </th>
													<th><?php echo esc_html__('Region') ?></th>
													<th><?php echo esc_html__('Actions') ?></th>
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
													<td style="display: flex; justify-content: center; gap: 5px; border-top: none; border-left: none;"><button @click="edit_region_entry(index, $event)"><img src="<?php echo GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/edit.png';?>"></button><button @click="delete_gcm_data(index, $event)"><img src="<?php echo GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/trash.png';?>"></button></td>
												</tr>
											</tbody>
										</table>
									</c-col>
									<c-col class="col-sm-12">
										<c-button id="add-region-btn" class="btn btn-info" variant="outline" @click="add_region=true"><?php echo esc_html__('+ New Region') ?></c-button>
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
												<div class="optout-settings-main-container">
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
												<c-input name="gdpr-gcm-region" v-model="newRegion.region"></c-input>

											</c-col>
										</c-row>
										<c-row>
											<p class="policy-description" style="text-align: center; width: 100%;"><?php echo esc_html__('In regions, by specifying "All", consent will get applied to all regions. You can specify a comma separated list of region’s ISO-standardised')?> <a href="https://en.wikipedia.org/wiki/ISO_3166-2#:~:text=level%20of%20subdivisions.-,Current%20codes%5Bedit%5D,-The%20following%20table" target="_blank">(ISO 3166-2)</a> <?php echo esc_html__('codes to apply consent to specific regions.')?>
											</p>
										</c-row>
										
												<button type="button" class="done-button-settings" @click="saveGCMDefault"><?php echo esc_html__('Done')?></button></div>
											
										</c-modal>
									</div>
									<c-col class="col-sm-4" style="align-items:start; margin-top: 20px;"><label><?php esc_attr_e( 'Wait for update', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8" style="margin-top: 20px;">
										<c-input name="gcm_wait_for_update_duration_field" v-model="gcm_wait_for_update_duration"></c-input>
										<p class="policy-description">
											<?php echo strip_tags('Number of milliseconds to wait before firing tags that are waiting for consent.', '<p><a><i><em><b><strong>'); ?>
										</p>
									</c-col>
									<c-col class="col-sm-4" style="align-items:start; margin-top: 20px;"><label><?php esc_attr_e( 'Pass ad click information through URLs', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8" style="margin-top: 20px;">
										<c-switch v-bind="labelIcon" v-model="gcm_url_passthrough" id="gdpr-cookie-consent-gcm-url-passthrough" variant="3d"  color="success" :checked="gcm_url_passthrough" v-on:update:checked="onSwitchGCMUrlPass"></c-switch>
										<input type="hidden" name="gcc-gcm-url-pass" v-model="gcm_url_passthrough">
										<p class="policy-description cookie-notice-readmore-container">
											<?php echo strip_tags('When enabled, internal links will include advertising identifiers (such as gclid, dclid, gclsrc, and _gl) in their URLs while awaiting consent.', '<p><a><i><em><b><strong>'); ?>
										</p>
									</c-col>
									<c-col class="col-sm-4" style="align-items:start; margin-top: 20px;"><label><?php esc_attr_e( 'Redact ads data', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8" style="margin-top: 20px;">
										<c-switch v-bind="labelIcon" v-model="gcm_ads_redact" id="gdpr-cookie-consent-gcm-ads-redact" variant="3d"  color="success" :checked="gcm_ads_redact" v-on:update:checked="onSwitchGCMAdsRedact"></c-switch>
										<input type="hidden" name="gcc-gcm-ads-redact" v-model="gcm_ads_redact">
										<p class="policy-description cookie-notice-readmore-container">
											<?php echo strip_tags('When enabled and the default consent state of "Advertisment" cookies is disabled, Google advertising tags will remove all advertising identifiers from the requests, and route traffic through domains that do not use cookies.', '<p><a><i><em><b><strong>'); ?>
										</p>
									</c-col>

									<c-col class="col-sm-4" style="align-items:start; margin-top: 20px;"><label><?php esc_attr_e( 'Enable debug mode', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8" style="margin-top: 20px;">
										<c-switch v-bind="labelIcon" v-model="gcm_debug_mode" id="gdpr-cookie-consent-gcm-debug_mode" variant="3d"  color="success" :checked="gcm_debug_mode" v-on:update:checked="onSwitchGCMDebugMode"></c-switch>
										<input type="hidden" name="gcc-gcm-debug-mode" v-model="gcm_debug_mode">
										<p class="policy-description cookie-notice-readmore-container">
											<?php echo strip_tags('When enabled your browser console will display the GCM default status, update status, and whether default consent was set in correct order.<br>To open the browser console, right click on any webpage, select Inspect -> Console.', '<p><a><i><em><b><strong><br>'); ?>
										</p>
									</c-col>
									<c-col class="col-sm-4" style="align-items:start; margin-top: 20px;"><label><?php esc_attr_e( 'Check GCM Status', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8" style="margin-top: 10px; margin-bottom: 20px;">
										<c-button v-show="gcm_scan_flag === false" id="checkGcmStatusButton" class="btn btn-info" variant="outline" @click="checkGCMStatus">{{ 'Check' }}</c-button>
										<c-button v-show="gcm_scan_flag === true" id="checkGcmStatusLoadingButton" class="btn btn-info" variant="outline" disabled><span class="checkGCMloader"></span>{{ 'Checking Now' }}</c-button>
										<div v-show="gcm_scan_result != ''">
											<p class="gcm_status_success" v-show="gcm_scan_result['gtagExists'] == true && gcm_scan_result['hasConsentDefault'] == true && gcm_scan_result['hasConsentUpdate'] == true && gcm_scan_result['onTime'] == true">No errors detected</p>
											<p class="gcm_status_error" v-show="gcm_scan_result['gtagExists'] == false" >No tag Present</p>
											<p class="gcm_status_error" v-show="gcm_scan_result['hasConsentDefault'] == false" >Default Consent Missing</p>
											<p class="gcm_status_error" v-show="gcm_scan_result['hasConsentUpdate'] == false" >Update Conset Missing</p>
											<p class="gcm_status_error" v-show="gcm_scan_result['onTime'] == false" >Default Consent set too late</p>
											<p v-show="gcm_scan_result['gtagExists'] != true || gcm_scan_result['hasConsentDefault'] != true || gcm_scan_result['hasConsentUpdate'] != true || gcm_scan_result['onTime'] != true" style="color:gray; font-weight:400;">Read the <a class="cookie-notice-readmore" href = "https://wplegalpages.com/docs/wp-cookie-consent/how-to-guides/google-consent-mode-troubleshooting-with-wplp-compliance-platform/" target="_blank">documentation</a> to know more about the errors and how to fix them.</p>
										</div>
									</c-col>
									<?php if($the_options['is_iabtcf_on'] === true || $the_options['is_iabtcf_on'] === "true" || $the_options['is_iabtcf_on'] === 1) : ?>
										<div class="col-sm-12 col" style="display: flex;" v-html="gcm_adver_mode_data" id="gcm-advertiser-mode-container"></div>
										<div id="gcm-advertiser-mode-container-loader"></div>
									<?php endif; ?>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Select the Type of Law', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-consent-policy-type" :reduce="label => label.code" :options="policy_options" v-model="gdpr_policy" @input="cookiePolicyChange" :searchable="false">
										</v-select>
											<input type="hidden" name="gcc-gdpr-policy" v-model="gdpr_policy">
											<div  v-show="is_eprivacy" class="cookie-notice-readmore-container">
											<a class="cookie-notice-readmore" href="https://wplegalpages.com/docs/wp-cookie-consent/settings/eprivacy-settings/" target="_blank">
												<?php esc_attr_e( 'Learn more about setting up an ePrivacy notice', 'gdpr-cookie-consent' ); ?>
											</a>
											</div>
									</c-col>
								</c-row>
								<c-row class="gdpr-cookie-consent-laws-type" v-show="ab_testing_enabled">
									<c-col class="col-sm-4"></c-col>
									<c-col class="col-sm-8">
										<p class="policy-description">
											<?php echo esc_html__('GDPR & CCPA cannot be selected while the Cookie Banner A/B Test is active. Please disable A/B Test to enable this compliance option.', 'gdpr-cookie-consent'); ?>
										</p>
									</c-col>
								</c-row>
								<c-row class="gdpr-cookie-consent-laws-type" v-show="is_gdpr && !is_ccpa">
									<c-col class="col-sm-4"></c-col>
									<c-col class="col-sm-8">
										<p class="policy-description">
											<?php echo esc_html__('The chosen law template supports various global privacy regulations including GDPR (EU & UK), PIPEDA (Canada), Law 25 (Quebec), POPIA (South Africa), nFADP (Switzerland), Privacy Act (Australia), PDPL (Saudi Arabia), PDPL (Argentina), PDPL (Andorra), and DPA (Faroe Islands).', 'gdpr-cookie-consent'); ?>
										</p>
										<div class="cookie-notice-readmore-container">
											<a class="cookie-notice-readmore" href="<?php echo esc_url('https://wplegalpages.com/docs/wp-cookie-consent/settings/gdpr-settings/'); ?>" target="_blank">
												<?php echo esc_html__('Learn more about setting up a GDPR notice', 'gdpr-cookie-consent'); ?>
											</a>
										</div>
									</c-col>
								</c-row>
								<c-row class="gdpr-cookie-consent-laws-type" v-show="is_ccpa && !is_gdpr">
									<c-col class="col-sm-4"></c-col>
									<c-col class="col-sm-8">
										<p class="policy-description">
											<?php echo esc_html__('The chosen law template supports CCPA/CPRA (California), VCDPA (Virginia), CPA (Colorado), CTDPA (Connecticut), & UCPA (Utah).', 'gdpr-cookie-consent'); ?>
										</p>
										<div class="cookie-notice-readmore-container">
											<a class="cookie-notice-readmore" href="<?php echo esc_url('https://wplegalpages.com/docs/wp-cookie-consent/settings/ccpa-settings/'); ?>" target="_blank">
												<?php echo esc_html__('Learn more about setting up a CCPA notice', 'gdpr-cookie-consent'); ?>
											</a>
										</div>
									</c-col>
								</c-row>

								<!-- THIS HAS TO MOVE TO CONTENT AND DESIGN  -->



								<!-- TILL HERE MOVE TO CONTENT AND DESIGN ^^^^^^^ -->

								<!-- Visitors Condition -->
								<c-row v-show=" gdpr_policy === 'gdpr' || gdpr_policy === 'both' || gdpr_policy === 'ccpa'">
									<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Cookie Banner Geo-Targeting', 'gdpr-cookie-consent' ); ?></div></c-col>
								</c-row>
								<div class="gdpr-visitors-condition">
									<div v-show="gdpr_policy === 'gdpr' || gdpr_policy === 'both' || gdpr_policy === 'ccpa'">
										<div><input class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-worldwide-enable"v-model="selectedRadioWorldWide" @click="onSwitchWorldWideEnable"><label><?php esc_attr_e( 'Worldwide', 'gdpr-cookie-consent' ); ?></label></div>
										<div>
											<input type="hidden" name="gcc-worldwide-enable" v-model="is_worldwide_on">
										</div>
									</div>
									<div v-show="gdpr_policy === 'gdpr' || gdpr_policy === 'both'">
											<?php
											$geo_options = get_option( 'wpl_geo_options' );
											 if ( !$is_user_connected || empty($is_user_connected) ) : ?>
												<div class="gdpr-disabled-geo-integration">
													<input id="gdpr-visitors-condition-radio-btn-disabled-gdpr" class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-eu-enable" disabled>
													<label><?php esc_attr_e( 'EU Countries & UK', 'gdpr-cookie-consent' ); ?></label>
												</div>
												<p class="gdpr-eu_visitors_message-gdpr">
													<?php esc_attr_e( 'To enable this feature, connect to your free account', 'gdpr-cookie-consent' ); ?>
												</p>
											<?php elseif ( $the_options['enable_safe'] === true || $the_options['enable_safe'] === 'true' ) : ?>
												<div class="gdpr-disabled-geo-integration">
													<input id="gdpr-visitors-condition-radio-btn-disabled-gdpr" class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-eu-enable" disabled>
													<label><?php esc_attr_e( 'EU Countries & UK', 'gdpr-cookie-consent' ); ?></label>
												</div>
												<p class="gdpr-eu_visitors_message-gdpr">
													<?php esc_attr_e( 'Safe Mode enabled. Disable it in Compliance settings to configure Geo-Targeting settings.', 'gdpr-cookie-consent' ); ?>
												</p>
											<?php else : ?>
												<div>
													<input class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-eu-enable" v-model="is_eu_on" @click="onSwitchEUEnable($event.target.checked)">
													<label><?php esc_attr_e( 'EU Countries & UK', 'gdpr-cookie-consent' ); ?></label>
												</div>
												<input type="hidden" name="gcc-eu-enable" v-model="is_eu_on">
											<?php endif; ?>
									</div>
									<div v-show="gdpr_policy === 'ccpa' || gdpr_policy === 'both'">
										<?php
											$geo_options = get_option( 'wpl_geo_options' );
										if ( !$is_user_connected || empty($is_user_connected) ) :
											?>
											<div class="gdpr-disabled-geo-integration"><input id="gdpr-visitors-condition-radio-btn-disabled-ccpa"class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-ccpa-enable" disabled><label style="width:114px;"><?php esc_attr_e( 'United States', 'gdpr-cookie-consent' ); ?></label></div>
											<p class="gdpr-eu_visitors_message-ccpa">
											<?php esc_attr_e( 'To enable this feature, connect to your free account', 'gdpr-cookie-consent' ); ?>
											</p>
										<?php elseif ( $the_options['enable_safe'] === true || $the_options['enable_safe'] === 'true' ) : ?>
											<div class="gdpr-disabled-geo-integration"><input id="gdpr-visitors-condition-radio-btn-disabled-ccpa"class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-ccpa-enable" disabled><label style="width:114px;"><?php esc_attr_e( 'United States', 'gdpr-cookie-consent' ); ?></label></div>
											<p class="gdpr-eu_visitors_message-ccpa">
												<?php esc_attr_e( 'Safe Mode enabled. Disable it in Compliance settings to configure Geo-Targeting settings.', 'gdpr-cookie-consent' ); ?>
											</p>
										<?php else : ?>
											<div><input class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-ccpa-enable" v-model="is_ccpa_on" @click="onSwitchCCPAEnable($event.target.checked)"><label><?php esc_attr_e( 'United States', 'gdpr-cookie-consent' ); ?></label></div>
											<input type="hidden" name="gcc-ccpa-enable" v-model="is_ccpa_on">
										<?php endif; ?>
									</div>
									<div v-show="gdpr_policy === 'gdpr' || gdpr_policy === 'both' || gdpr_policy === 'ccpa'">
										<?php
											$geo_options = get_option( 'wpl_geo_options' );
										if ( !$is_user_connected || empty($is_user_connected)) :
											?>
											<div class="gdpr-disabled-geo-integration"><input class="gdpr-visiotrs-condition-radio-btn" id="gdpr-visitors-condition-radio-btn-disabled-both" type="checkbox" name="gcc-select-countries-enable" disabled><label><?php esc_attr_e( 'Select Countries', 'gdpr-cookie-consent' ); ?></label></div>
											<p class="gdpr-eu_visitors_message-both">
											<?php esc_attr_e( 'To enable this feature, connect to your free account', 'gdpr-cookie-consent' ); ?>
											</p>
										<?php elseif ( $the_options['enable_safe'] === true || $the_options['enable_safe'] === 'true' ) : ?>
											<div class="gdpr-disabled-geo-integration"><input class="gdpr-visiotrs-condition-radio-btn" id="gdpr-visitors-condition-radio-btn-disabled-both" type="checkbox" name="gcc-select-countries-enable" disabled><label><?php esc_attr_e( 'Select Countries', 'gdpr-cookie-consent' ); ?></label></div>
											<p class="gdpr-eu_visitors_message-both">
												<?php esc_attr_e( 'Safe Mode enabled. Disable it in Compliance settings to configure Geo-Targeting settings.', 'gdpr-cookie-consent' ); ?>
											</p>
										<?php else : ?>
											<div><input class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-select-countries-enable" v-model="selectedRadioCountry" @click="onSwitchSelectedCountryEnable($event.target.checked)"><label><?php esc_attr_e( 'Select Countries', 'gdpr-cookie-consent' ); ?></label></div>
											<input type="hidden" name="gcc-select-countries-enable" v-model="is_selectedCountry_on">
										<?php endif; ?>
									</div>
								</div>
								<div class="select-countries-dropdown" v-show="(is_selectedCountry_on) && ( gdpr_policy === 'gdpr' || gdpr_policy === 'both' || gdpr_policy === 'ccpa' )">
									<v-select id="gdpr-cookie-consent-geotargeting-countries" placeholder="Select Countries":reduce="label => label.code" class="form-group" :options="list_of_countries" multiple v-model="select_countries_array" @input="onCountrySelect"></v-select>
									<input type="hidden" name="gcc-selected-countries" v-model="select_countries">
								</div>
								<p v-show="( gdpr_policy === 'gdpr' || gdpr_policy === 'both' || gdpr_policy === 'ccpa' )" class="maxmind-notice">This product includes GeoLite2 data created by MaxMind, available from <a href="https://www.maxmind.com">https://www.maxmind.com</a>.</p>
								
								<!-- THIS HAS TO MOVE -->
								

								<!-- Cookie Settings  -->
								<c-row>
									<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Consent Settings', 'gdpr-cookie-consent' ); ?></div></c-col>
								</c-row>
								<?php if ( ! $is_pro_active ) : ?>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Consent Logging', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enable to log user’s consent.', 'gdpr-cookie-consent' ); ?>"></tooltip><div class="consent-log-readmore-container">
											<a class="consent-log-readmore" href="https://wplegalpages.com/docs/wp-cookie-consent/settings/consent-logging/" target="_blank">
												<?php esc_attr_e( 'Learn more about consent logging', 'gdpr-cookie-consent' ); ?>
											</a>
											</div></label></c-col>											
										<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="logging_on" id="gdpr-cookie-consent-logging-on" variant="3d"  color="success" :checked="logging_on" v-on:update:checked="onSwitchLoggingOn"></c-switch>
										<input type="hidden" name="gcc-logging-on" v-model="logging_on">
									</c-col>
								</c-row>
								<?php endif; ?>
								<?php if ( $is_pro_active ) : ?>
									<?php do_action( 'gdpr_consent_settings_pro_top' ); ?>
								<?php endif; ?>
								<c-row v-show="is_gdpr">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Autotick for Non-Necessary Cookies ', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Pre-select non-necessary cookie checkboxes.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
								<c-col class="col-sm-8">
									<c-switch v-bind="labelIcon" v-model="autotick" id="gdpr-cookie-consent-autotick" variant="3d"  color="success" :checked="autotick" v-on:update:checked="onSwitchAutotick"></c-switch>
									<input type="hidden" name="gcc-autotick" v-model="autotick">
								</c-col>
								</c-row>
								<c-row v-show="show_revoke_card">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Auto Hide (Accept)', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'If enabled Cookie Bar will be automatically hidden after specified time and cookie preferences will be set as accepted.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
								<c-col class="col-sm-8">
									<c-switch v-bind="labelIcon" v-model="auto_hide" id="gdpr-cookie-consent-auto_hide" variant="3d"  color="success" :checked="auto_hide" v-on:update:checked="onSwitchAutoHide"></c-switch>
									<input type="hidden" name="gcc-auto-hide" v-model="auto_hide">
								</c-col>
								</c-row>
								<c-row v-show="auto_hide&&show_revoke_card">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Auto Hide Delay (in milliseconds)', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8">
									<c-input type="number" min="5000" max="60000" step="1000" name="gcc-auto-hide-delay" v-model="auto_hide_delay"></c-input>
								</c-col>
								</c-row>
								<c-row v-show="show_revoke_card">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Auto Scroll (Accept)', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( ' If enabled, Cookie Bar will automatically hide after the visitor scrolls the webpage and consent will be automatically accepted as Yes.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
								<c-col class="col-sm-8">
									<c-switch v-bind="labelIcon" v-model="auto_scroll" id="gdpr-cookie-consent-auto_scroll" variant="3d"  color="success" :checked="auto_scroll" v-on:update:checked="onSwitchAutoScroll"></c-switch>
									<input type="hidden" name="gcc-auto-scroll" v-model="auto_scroll">
								</c-col>
								</c-row>
								<c-row v-show="auto_scroll">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Auto Scroll Offset (in percent)', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8">
									<c-input type="number" min="1" max="100" name="gcc-auto-scroll-offset" v-model="auto_scroll_offset"></c-input>
								</c-col>
								</c-row>
								<c-row v-show="show_revoke_card">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Auto Click (Accept)', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( ' If enabled, the Cookie Bar will automatically hide when the visitor clicks anywhere on the page, and consent will be accepted as Yes.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
								<c-col class="col-sm-8">
									<c-switch v-bind="labelIcon" v-model="auto_click" id="gdpr-cookie-consent-auto_click" variant="3d"  color="success" :checked="auto_click" v-on:update:checked="onSwitchAutoClick"></c-switch>
									<input type="hidden" name="gcc-auto-click" v-model="auto_click">
								</c-col>
								</c-row>
								<c-row v-show="show_revoke_card">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Reload After Scroll Accept', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'If enabled, the web page will be refreshed automatically once cookie settings are accepted because of scrolling.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
								<c-col class="col-sm-8">
									<c-switch v-bind="labelIcon" v-model="auto_scroll_reload" id="gdpr-cookie-consent-auto-scroll-reload" variant="3d"  color="success" :checked="auto_scroll_reload" v-on:update:checked="onSwitchAutoScrollReload"></c-switch>
									<input type="hidden" name="gcc-auto-scroll-reload" v-model="auto_scroll_reload">
								</c-col>
								</c-row>
								<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Reload After Accept', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'If enabled web page will be refreshed automatically once cookie settings are accepted.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
								<c-col class="col-sm-8">
									<c-switch v-bind="labelIcon" v-model="accept_reload" id="gdpr-cookie-consent-accept-reload" variant="3d"  color="success" :checked="accept_reload" v-on:update:checked="onSwitchAcceptReload"></c-switch>
									<input type="hidden" name="gcc-accept-reload" v-model="accept_reload">
								</c-col>
								</c-row>
								<c-row  v-show="show_revoke_card">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Reload After Decline', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'If enabled web page will be refreshed automatically once cookie settings are declined.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="decline_reload" id="gdpr-cookie-consent-decline-reload" variant="3d"  color="success" :checked="decline_reload" v-on:update:checked="onSwitchDeclineReload"></c-switch>
										<input type="hidden" name="gcc-decline-reload" v-model="decline_reload">
									</c-col>
								</c-row>
								<!-- Do Not Track  -->
								<?php
								$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ? GDPR_COOKIE_CONSENT_VERSION : '';
								if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
									if ( ! $is_pro_active ) :
										?>
								<c-row>
									<c-col class="col-sm-4 relative"><label><?php esc_attr_e( 'Respect Do Not Track & Global Privacy Control', 'gdpr-cookie-consent' ); ?></label>
										<div class="gdpr-pro-label absolute" style="right: 0px;"><div class="gdpr-pro-label-text">Pro</div></div>
									</c-col>
									<c-col class="col-sm-8">
										<c-switch disabled v-bind="isGdprProActive ? labelIcon : labelIconNew" variant="3d" color="success"></c-switch>
									</c-col>
								</c-row>
									<?php endif ?>
									<?php
									do_action( 'gdpr_consent_settings_dnt' ); } else {
									?>
								<c-row>
									<c-col class="col-sm-4 relative"><label><?php esc_attr_e( 'Respect Do Not Track & Global Privacy Control', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind= labelIcon v-model="do_not_track_on" id="gdpr-cookie-do-not-track" variant="3d" color="success" :checked="do_not_track_on" v-on:update:checked="onSwitchDntEnable"></c-switch>
										<input type="hidden" name="gcc-do-not-track" v-model="do_not_track_on">
									</c-col>
								</c-row>
								<?php } ?>
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
													<c-input name="data_req_email_text_field"  placeholder="example@example.com" v-model="data_req_email_address"  id="email-input"></c-input>

												</c-col>
												<!-- email validation script -->
												<script>
													document.addEventListener('DOMContentLoaded', function () {
														// Get the input element and the validation icon element
														var emailInput = document.getElementById('email-input');
														var validationIcon = document.getElementById('validation-icon');

														// Add an event listener on input change
														emailInput.addEventListener('input', function () {
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
													<c-input name="data_req_subject_text_field" placeholder="We have received your request" v-model="data_req_subject" id="subject-input"></c-input>
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
								<!-- Consent  Forwarding -->
								<?php
								if ( ! $is_pro_active ) :
									$currentid = get_current_blog_id();
									if ( is_multisite() ) {
										?>
								<c-row>
									<c-col class="col-sm-4 relative"><label><?php esc_attr_e( 'Consent Forwarding', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'If you have multiple WordPress sites for one organization, you can get user consent on one site, and it will count for selected sites in the network. ', 'gdpr-cookie-consent' ); ?>" style="left:10px;"></tooltip></label></c-col>
									<c-col class="col-sm-8">
										<input type="hidden" name="gcc-consent-forward" v-model="consent_forward">
										<c-switch v-bind="labelIcon" v-model="consent_forward" id="gdpr-cookie-consent-forward" variant="3d" color="success" :checked="consent_forward" v-on:update:checked="onSwitchConsentForward"></c-switch>
									</c-col>
								</c-row>
								<c-row v-show="consent_forward">
									<c-col class="col-sm-4 relative"><label><?php esc_attr_e( 'Forward to', 'gdpr-cookie-consent' ); ?><tooltip text="
										<?php
										esc_html_e(
											'Choose the websites where the user\'s consent from the current site should be sent.
									',
											'gdpr-cookie-consent'
										);
										?>
										"style="left:10px;"></tooltip></label></c-col>
									<c-col class="col-sm-8">
										<v-select id="gdpr-cookie-consent-forward-sites" placeholder="Select sites":reduce="label => label.code" class="form-group" :options="list_of_sites" multiple v-model="select_sites_array" @input="onSiteSelect"></v-select>
										<input type="hidden" name="gcc-selected-sites" v-model="select_sites">
									</c-col>
								</c-row>
										<?php
									} else {
										?>
								<c-row>
									<c-col class="col-sm-4 relative"><label><?php esc_attr_e( 'Consent Forwarding', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'If you have multiple WordPress sites for one organization, you can get user consent on one site, and it will count for selected sites in the network.', 'gdpr-cookie-consent' ); ?>"style="left:10px;"></tooltip></label></c-col>
									<c-col class="col-sm-8">
										<input type="hidden" name="gcc-consent-forward" v-model="consent_forward">
										<div class="consent-multisite">
											<c-switch disabled v-bind="labelIcon" v-model="consent_forward" id="gdpr-cookie-consent-forward" variant="3d" color="success" :checked="consent_forward" v-on:update:checked="onSwitchConsentForward"></c-switch>
											<p class="consent-tooltip">
											<?php
											esc_html_e(
												'This setting is only available for multisite WordPress instances.
									',
												'gdpr-cookie-consent'
											);
											?>
											</p>
										</div>
									</c-col>
								</c-row>
										<?php } ?>
									<?php endif ?>
									<?php if ( $is_pro_active ) : ?>
										<?php do_action( 'gdpr_consent_settings_consent_forward' ); ?>
									<?php endif ?>
									<?php if ( ! $is_pro_active ) : ?>

								<!-- MOVE TILL HERE ^^^^ -->

								<!-- NEWLY CONTENT ADDED -->

								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Restrict Pages and/or Posts', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Restrict Pages and/or Posts during scanning of your website for cookies.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-8">
										<v-select  placeholder="Select Pages and Posts" id="gdpr-cookie-consent-restrict-posts" :reduce="label => label.code" class="form-group" :options="list_of_contents" multiple v-model="restrict_array" @input="onPostsSelect"></v-select>
										<input type="hidden" name="gcc-restrict-posts" v-model="restrict_posts">
									</c-col>
								</c-row>


								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Banner Initialization', 'gdpr-cookie-consent' ); ?></label></c-col>
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

								<!-- NEWLY ADDED CONTENT ^^^ -->

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
								<?php endif ?>
								<?php do_action( 'gdpr_consent_settings_pro_bottom' ); ?>

								<!-- REMOVE EXTRA SETTINGS -->

								<!-- Extra Settings -->
								<c-row>
									<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Extra Settings', 'gdpr-cookie-consent' ); ?></div></c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Delete Plugin Data on Deactivation', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="delete_on_deactivation" id="gdpr-cookie-consent-delete-on-deactivation" variant="3d"  color="success" :checked="delete_on_deactivation" v-on:update:checked="onSwitchDeleteOnDeactivation"></c-switch>
										<input type="hidden" name="gcc-delete-on-deactivation" v-model="delete_on_deactivation">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Show Credits', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'If you are happy with the product and want to share credit with the developer, you can display credits under the Cookie Settings.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="show_credits" id="gdpr-cookie-consent-show-credits" variant="3d"  color="success" :checked="show_credits" v-on:update:checked="onSwitchShowCredits"></c-switch>
										<input type="hidden" name="gcc-show-credits" v-model="show_credits">
									</c-col>
								</c-row>
								<c-row  v-show="show_revoke_card">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cookie Expiry', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'The amount of time that the cookie should be stored for.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-consent-cookie-expiry" :reduce="label => label.code" :options="cookie_expiry_options" v-model="cookie_expiry">
										</v-select>
										<input type="hidden" name="gcc-cookie-expiry" v-model="cookie_expiry">
									</c-col>
								</c-row>
								<?php if ( ! $is_pro_active ) : ?>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Safe Mode for Cookies', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'When safe mode is enabled, all integrations will be disabled temporarily.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
												<input type="hidden" name="gcc-enable-safe" v-model="enable_safe">					<c-switch  v-bind="labelIcon " id="gdpr-cookie-consent-enable-safe" variant="3d" color="success" :checked="enable_safe" v-on:update:checked="onSwitchEnableSafe" v-model="enable_safe"></c-switch>

										</c-col>
								</c-row>
								<?php endif; ?>
								<?php if ( ! $is_pro_active ) : ?>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Share Usage Data', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Allow us to collect anonymous data about how you use the plugin. This helps us identify issues, improve features, and enhance user experience. No personal or sensitive information is ever collected.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<input type="hidden" name="gcc-usage-data" v-model="usage_data"><c-switch  v-bind="labelIcon " id="gdpr-cookie-consent-usage-data" variant="3d" color="success" :checked="usage_data" v-on:update:checked="onSwitchEnableUsageData" v-model="usage_data"></c-switch>
										</c-col>
								</c-row>
								<?php endif; ?>
								<?php if ( $is_pro_active ) : ?>
									<?php do_action( 'gdpr_consent_settings_safe_enable' ); ?>
								<?php endif; ?>
								<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Export Personal Data', 'gdpr-cookie-consent' ); ?> </label></c-col>
								<c-col class="col-sm-8">
										<?php
										$export_personal_data_url = admin_url( 'export-personal-data.php' );
										echo '<a href="' . esc_url( $export_personal_data_url ) . '"target="_blank">';
										?>
										<c-button class="export-btn" >Export</c-button> </a>
								</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Erase Personal Data', 'gdpr-cookie-consent' ); ?> </label></c-col>
									<c-col class="col-sm-8">
										<?php
										$erase_personal_data_url = admin_url( 'erase-personal-data.php' );
										echo '<a href="' . esc_url( $erase_personal_data_url ) . '"target="_blank">';
										?>
										<c-button class="erase-btn" color="danger"variant="outline">Erase</c-button> </a>
										</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Reset Settings', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'This will reset the settings to their default values.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-8">
										<c-button id="reset-settings-btn" color="danger" variant="outline" @click="onClickRestoreButton"><?php esc_html_e( 'Restore to Default', 'gdpr-cookie-consent' ); ?></c-button>
									</c-col>
								</c-row>
							</c-card-body>
						</c-card>
				</c-tab>
			<!-- COMPLIANCES SECTION END -->	

			<!-- CONFIGURATION SECTION START -->
				<c-tab title="<?php esc_attr_e( 'Layout', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#configuration" id="gdpr-cookie-consent-configuration">

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
								</c-row>
							</c-row>
							<c-row style="margin-top:-28px;" v-show="show_cookie_as === 'widget'">
							<!-- notify_position_horizontal -->
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Position', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8">
								<div @click="cookiewidgetPositionChange('left')" style="display: inline-block; cursor: pointer;">
										<div>
										<span id="widget-position-left-icon" :class="{ 'dashicons dashicons-saved': cookie_widget_position === 'left' }"></span>
										</div>
										<img
										id="widget-position-left-id"
										:class="{ 'widget-position-top': cookie_widget_position === 'left' }"
										src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) .'admin/images/widget_bottom_left.svg';?>"
										alt="Bottom_left"
										>
									</div>

									<div @click="cookiewidgetPositionChange('right')" style="display: inline-block; cursor: pointer; padding-left: 18px;">
										<div>
										<span id="widget-position-right-icon" :class="{ 'dashicons dashicons-saved': cookie_widget_position === 'right' }"></span>
										</div>
										<img
										id="widget-position-right-id"
										:class="{ 'widget-position-top': cookie_widget_position === 'right' }"
										src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) .'admin/images/widget_bottom_right.svg';?>"
										alt="Bottom_right"
										>
									</div>

									<div @click="cookiewidgetPositionChange('top_left')" style="display: inline-block; cursor: pointer; padding-left: 18px;">
										<div>
										<span id="widget-position-top_left-icon" :class="{ 'dashicons dashicons-saved': cookie_widget_position === 'top_left' }"></span>
										</div>
										<img
										id="widget-position-top_left-id"
										:class="{ 'widget-position-top': cookie_widget_position === 'top_left' }"
										src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) .'admin/images/widget_top_left.svg';?>"
										alt="Top_left"
										>
									</div>

									<div @click="cookiewidgetPositionChange('top_right')" style="display: inline-block; cursor: pointer; padding-left: 18px;">
										<div>
										<span id="widget-position-top_right-icon" :class="{ 'dashicons dashicons-saved': cookie_widget_position === 'top_right' }"></span>
										</div>
										<img
										id="widget-position-top_right-id"
										:class="{ 'widget-position-top': cookie_widget_position === 'top_right' }"
										src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) .'admin/images/widget_top_right.svg';?>"
										alt="Top_right"
										>
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
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'On Hide', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8">
								<v-select class="form-group" id="gdpr-cookie-consent-on-hide" :reduce="label => label.code" :options="on_hide_options" v-model="on_hide"></v-select>
								<input type="hidden" name="gcc-gdpr-cookie-on-hide" v-model="on_hide">
								</c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'On Load', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8">
								<v-select class="form-group" id="gdpr-cookie-consent-on-load" :reduce="label => label.code" :options="on_load_options" v-model="on_load"></v-select>
								<input type="hidden" name="gcc-gdpr-cookie-on-load" v-model="on_load">
								</c-col>
							</c-row>
							<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Auto-Detect Banner Language ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( " Automatically sets the cookie banner language to match your visitor's preferred browser language, providing a more localized experience. ", 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="dynamic_lang_is_on" id="gdpr-cookie-consent-dynamic-lang-on" variant="3d"  color="success" :checked="dynamic_lang_is_on" v-on:update:checked="onSwitchDynamicLang"></c-switch>
										<input type="hidden" name="gcc-dynamic-lang-enable" v-model="dynamic_lang_is_on">
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
							<!-- Template screens -->
							<?php do_action( 'gdpr_cookie_template' ); ?>

							<!-- MOVE TO COOKIE SETTINGS EXPORT/IMPORT -->

							<!-- Export Settings Label -->
							<c-row>
								<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Settings Export / Import', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<c-row class="mb-3" >
								<c-col class="col-sm-4">
									<label class="mb-0"><?php esc_attr_e( 'Export Settings ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( ' You can use this to export your settings to another site. ', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
								</c-col>
								<c-col class="col-sm-8">
									<c-button id="export-settings-configuration" color="info" variant="outline" @click="exportsettings"><?php esc_html_e( 'Export', 'gdpr-cookie-consent' ); ?></c-button>
								</c-col>
							</c-row>
							<c-row class="mb-3 pb-3" >
									<c-col class="col-sm-4" style="flex-direction:column;align-items:baseline;position: relative;">
										<div style="display:flex" >
											<label style="margin-bottom:0;cursor:pointer"><?php esc_attr_e( 'Import Settings', 'gdpr-cookie-consent' ); ?></label>
											<?php
											$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ? GDPR_COOKIE_CONSENT_VERSION : '';
											if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
												if ( ! $is_pro_active ) :
													?>
											<div class="gdpr-pro-label" style="margin-bottom:0;margin-top:3px;" >
														<div class="gdpr-pro-label-text">Pro</div>
													</div>
														<?php endif; } ?>
										</div>
										<div style="font-size: 10px;" v-if="selectedFile">{{ selectedFile.name }} <span style="color:#00CF21;font-weight:500;margin-left:5px" > Uploaded </span> <span style="color: #8996AD;text-decoration:underline;margin-left:5px;position:absolute" class="remove-button" @click="removeFile">Remove</span> </div>
										<div style="font-size: 10px;" v-else>No File Chosen</div>
										</c-col>
										<c-col class="col-sm-6" id="import-btn-container">
                                            <label style="margin-bottom:0; font-size:0.875rem;<?php
                                            echo version_compare( $plugin_version, '2.5.2', '<=' ) ? ( ! $is_pro_active ? 'color:#D8DBE0;' : 'color:#3399ff;' ) : 'color:#3399ff;';
                                            ?> text-decoration:underline;margin: right 10px ;padding-left:42px;margin-top:6px;" for="fileInput">Choose file</label>
                                            <input style="display: none;" type="file"
                                            <?php
                                            echo version_compare( $plugin_version, '2.5.2', '<=' ) ? ( ! $is_pro_active ? '' : 'disabled' ) : '';
                                            ?>
                                            @change="updateFileName" name="fileInput" accept=".json" id="fileInput">
                                            <c-button variant="outline"class="disable-import-button"
                                            @click="importsettings" id="importButton" disabled>
                                                <?php esc_html_e( 'Import', 'gdpr-cookie-consent' ); ?>
                                            </c-button>
                                        </c-col>

							</c-row>
							<c-row class="pt-1 mb-0">
								<c-col class="col-sm-4">
									<label style="margin-bottom:0"><?php esc_attr_e( 'Reset Settings ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'This will reset all settings to defaults. All data in the WP Cookie Consent plugin will be deleted. ', 'gdpr-cookie-consent' ); ?>">
											</tooltip></label>
								</c-col>
								<c-col class="col-sm-8">
									<c-button id="reset-settings-configuration" color="danger" variant="outline" @click="onClickRestoreButton"><?php esc_html_e( 'Reset to Default', 'gdpr-cookie-consent' ); ?></c-button>
								</c-col>
							</c-row>

							<!-- MOVE TO COOKIE SETTINGS EXPORT/IMPORT ^^^^^ -->

						</c-card-body>
					</c-card>
				</c-tab>
			<!-- CONFIGURATION SECTION START -->
			 
			<!-- CONTENT AND DESIGN SECTION START -->
				<c-tab title="<?php esc_attr_e( 'Content and Design', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#gdpr_design" id="gdpr-cookie-consent-design">

					<!-- Desgin Banner preview if A/B Testing is disabled and GDPR&CCPA both are not selected -->
					<c-card v-show="!ab_testing_enabled && gdpr_policy != 'both'">
					<c-card class="desgin_card">
						<c-card-body>

						<!-- NEWLY ADDED -->
													
						<c-row v-show="is_gdpr">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Message Heading', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Leave it blank, If you do not need a heading.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="bar_heading_text_field" v-model="gdpr_message_heading"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="is_eprivacy">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'ePrivacy Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as ePrivacy notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_eprivacy_field" v-model="eprivacy_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="is_gdpr">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'GDPR Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_field" v-model="gdpr_message" :readonly="iabtcf_is_on"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="is_ccpa">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'CCPA Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as CCPA notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_ccpa_field" v-model="ccpa_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="is_ccpa">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'CCPA Opt-out Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as CCPA notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_ccpa_optout_field" v-model="ccpa_optout_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="is_gdpr">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'About Cookies Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Text shown under "About Cookies" section when users click on "Cookie Settings" button.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea :rows="6" name="about_message_field" v-model="gdpr_about_cookie_message" :readonly="iabtcf_is_on"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="is_lgpd">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Message Heading', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Leave it blank, If you do not need a heading.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="bar_heading_text_lgpd_field" v-model="lgpd_message_heading"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="is_lgpd">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'LGPD Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_lgpd_field" v-model="lgpd_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="is_lgpd">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'About Cookies Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Text shown under "About Cookies" section when users click on "Cookie Settings" button.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea :rows="6" name="about_message_lgpd_field" v-model="lgpd_about_cookie_message"></c-textarea>
							</c-col>
						</c-row>

							<c-row>
								<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Cookie Bar Body Design', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cookie Bar Color', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8 gdpr-color-pick" >
								<c-input class="gdpr-color-input" type="text" v-model="cookie_bar_color"></c-input>
								<c-input class="gdpr-color-select" id="gdpr-cookie-bar-color" type="color" name="gdpr-cookie-bar-color" v-model="cookie_bar_color"></c-input>
								</c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( ' Cookie Bar Opacity', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8 gdpr-color-pick">
								<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="cookie_bar_opacity"></c-input>
								<c-input class="gdpr-slider-input opacity-slider" type="number"  min="0" max="1" step="0.01" name="gdpr-cookie-bar-opacity" v-model="cookie_bar_opacity"></c-input>
								</c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8 gdpr-color-pick" >
								<c-input class="gdpr-color-input" type="text" v-model="cookie_text_color"></c-input>
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
								<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="cookie_bar_border_width"></c-input>
								<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-bar-border-width" v-model="cookie_bar_border_width"></c-input>
								</c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8 gdpr-color-pick" >
								<c-input class="gdpr-color-input" type="text" v-model="cookie_border_color"></c-input>
								<c-input class="gdpr-color-select" id="gdpr-cookie-border-color" type="color" name="gdpr-cookie-border-color" v-model="cookie_border_color"></c-input>
								</c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8 gdpr-color-pick">
								<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="cookie_bar_border_radius"></c-input>
								<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-bar-border-radius" v-model="cookie_bar_border_radius"></c-input>
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
										<label><?php esc_attr_e( 'Upload Logo ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'To preview the logo, simply upload a logo and then click the "Save Changes" button ', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
									</c-col>
									<c-col class="col-sm-8 ">
										<c-button color="info" class="button" id="image-upload-button" name="image-upload-button" @click="openMediaModal" style="margin: 10px;">
											<?php esc_attr_e( 'Add Image', 'gdpr-cookie-consent' ); ?>
										</c-button>
										<c-button color="info" class="button" id="image-delete-button" @click="deleteSelectedimage" style="margin: 10px; ">
											<?php esc_attr_e( 'Remove Image', 'gdpr-cookie-consent' ); ?>
										</c-button>
										<?php
										$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
										?>
										<img id="gdpr-cookie-bar-logo-holder" name="gdpr-cookie-bar-logo-holder" src="<?php echo esc_url_raw( $get_banner_img ); ?>">
										<p class="image-upload-notice" style="margin-left: 10px; font-size:14px; font-weight:14px;color:#d4d4d8;">
											<?php esc_attr_e( 'We recommend 50 x 50 pixels.', 'gdpr-cookie-consent' ); ?>
										</p>
										<c-input type="hidden" name="gdpr-cookie-bar-logo-url-holder" id="gdpr-cookie-bar-logo-url-holder"  class="regular-text"> </c-input>
									</c-col>
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
									<c-row v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on">
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gcc-readmore-as-button" :reduce="label => label.code" :options="show_as_options" v-model="button_readmore_as_button"></v-select>
											<input type="hidden" name="gcc-readmore-as-button" v-model="button_readmore_as_button">
										</c-col>
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gcc-readmore-url-type" :reduce="label => label.code" :options="url_type_options" v-model="button_readmore_url_type"></v-select>
											<input type="hidden" name="gcc-readmore-url-type" v-model="button_readmore_url_type">
										</c-col>
									</c-row>
													
									<div v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on">
										<c-row v-show="button_readmore_as_button" class="gdpr-label-row">
											<c-col class="col-sm-6"><label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label></c-col>
											<c-col class="col-sm-6"><label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label></c-col>	
										</c-row>
										<c-row v-show="button_readmore_as_button">
											<c-col class="col-sm-6 gdpr-color-pick" >
												<c-input class="gdpr-color-input" type="text" v-model="button_readmore_button_color"></c-input>
												<c-input class="gdpr-color-select" id="gdpr-readmore-button-color" type="color" name="gcc-readmore-button-color" v-model="button_readmore_button_color"></c-input>
											</c-col>
											<c-col class="col-sm-6">
												<v-select class="form-group" id="gcc-readmore-button-size" :reduce="label => label.code" :options="button_size_options" v-model="button_readmore_button_size"></v-select>
												<input type="hidden" name="gcc-readmore-button-size" v-model="button_readmore_button_size">
											</c-col>
										</c-row>
										<c-row v-show="button_readmore_as_button" class="gdpr-label-row">
											<c-col class="col-sm-6"><label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label></c-col>
											<c-col class="col-sm-6"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										</c-row>
										<c-row v-show="button_readmore_as_button">
											<c-col class="col-sm-6">
												<v-select class="form-group" id="gcc-readmore-button-border-style" :reduce="label => label.code" :options="border_style_options" v-model="button_readmore_button_border_style"></v-select>
												<input type="hidden" name="gcc-readmore-button-border-style" v-model="button_readmore_button_border_style">
											</c-col>
											<c-col class="col-sm-6 gdpr-color-pick" >
												<c-input class="gdpr-color-input" type="text" v-model="button_readmore_button_border_color"></c-input>
												<c-input class="gdpr-color-select" id="gdpr-readmore-button-border-color" type="color" name="gcc-readmore-button-border-color" v-model="button_readmore_button_border_color"></c-input>
											</c-col>
										</c-row>
										<c-row class="gdpr-label-row">
											<c-col class="col-sm-6" v-show="button_readmore_url_type"><label><?php esc_attr_e( 'Page', 'gdpr-cookie-consent' ); ?></label></c-col>
											<c-col v-show="!button_readmore_url_type" class="col-sm-6"><label><?php esc_attr_e( 'URL', 'gdpr-cookie-consent' ); ?></label></c-col>
											<c-col class="col-sm-3 gdpr-readmore-toggle-row" v-show="button_readmore_url_type"><label><?php esc_attr_e( 'Sync with WordPress Policy Page', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'If enabled visitor will be redirected to Privacy Policy Page set in WordPress settings irrespective of Page set in the previous setting.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
											<c-col class="col-sm-3 gdpr-readmore-toggle-row" v-show="button_readmore_url_type">
												<c-switch v-bind="labelIcon" v-model="button_readmore_wp_page" id="gdpr-cookie-consent-readmore-wp-page" variant="3d"  color="success" :checked="button_readmore_wp_page" v-on:update:checked="onSwitchButtonReadMoreWpPage"></c-switch>
												<input type="hidden" name="gcc-readmore-wp-page" v-model="button_readmore_wp_page">
											</c-col>
										</c-row>
										<c-row>
											<c-col v-show="button_readmore_url_type" class="col-sm-6">
												<v-select class="form-group"  placeholder="Select Policy Page" id="gcc-readmore-page" :reduce="label => label.code" :options="privacy_policy_options" v-model="readmore_page" @input="onSelectPrivacyPage"></v-select>
												<input type="hidden" name="gcc-readmore-page" v-model="button_readmore_page">
											</c-col>
											<c-col class="col-sm-6" v-show="!button_readmore_url_type">
												<c-input name="gcc-readmore-url" v-model="button_readmore_url"></c-input>
											</c-col>
											<c-col class="col-sm-3 gdpr-readmore-toggle-row"><label><?php esc_attr_e( 'Open URL in New Window?', 'gdpr-cookie-consent' ); ?></label></c-col>
											<c-col class="col-sm-3 gdpr-readmore-toggle-row">
												<c-switch v-bind="labelIcon" v-model="button_readmore_new_win" id="gdpr-cookie-consent-readmore-new-win" variant="3d"  color="success" :checked="button_readmore_new_win" v-on:update:checked="onSwitchButtonReadMoreNewWin"></c-switch>
												<input type="hidden" name="gcc-readmore-new-win" v-model="button_readmore_new_win">
											</c-col>
										</c-row>
										<c-row v-show="button_readmore_as_button" class="gdpr-label-row">
											<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label></c-col>
											<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label></c-col>
											<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label></c-col>
										</c-row>
										<c-row v-show="button_readmore_as_button">
											<c-col class="col-sm-4 gdpr-color-pick">
												<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="button_readmore_button_opacity"></c-input>
												<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-opacity" v-model="button_readmore_button_opacity"></c-input>
											</c-col>
											<c-col class="col-sm-4 gdpr-color-pick">
												<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="button_readmore_button_border_width"></c-input>
												<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-border-width" v-model="button_readmore_button_border_width"></c-input>
											</c-col>
											<c-col class="col-sm-4 gdpr-color-pick">
												<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="button_readmore_button_border_radius"></c-input>
												<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-border-radius" v-model="button_readmore_button_border_radius"></c-input>
											</c-col>
										</c-row>	
									</div>

									<button type="button" class="done-button-settings" @click="button_readmore_popup=false">Done</button>
								</div>
							</c-modal>		
						</div>

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

							<!-- Accept Button -->
							<c-row v-show="is_gdpr || is_eprivacy || is_lgpd">
								<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Accept Button', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<c-row v-show="is_gdpr || is_eprivacy || is_lgpd">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-4">
									<c-switch v-bind="labelIcon" v-model="cookie_accept_on" id="gdpr-cookie-consent-cookie" variant="3d"  color="success" :checked="cookie_accept_on" v-on:update:checked="onSwitchCookieAcceptEnable"></c-switch>
									<input type="hidden" name="gcc-cookie-accept-enable" v-model="cookie_accept_on">
								</c-col>
								<c-col class="col-sm-3" v-show="is_gdpr || is_eprivacy || is_lgpd">
									<c-button :disabled="!cookie_accept_on" class="gdpr-configure-button" @click="accept_button_popup=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
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
									<div class="optout-settings-main-container">
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
									<c-input name="button_accept_text_field" v-model="accept_text"></c-input>
								</c-col>
								<c-col class="col-sm-6 gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="accept_text_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-text-color" type="color" name="gdpr-cookie-accept-text-color" v-model="accept_text_color"></c-input>
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
									<v-select class="form-group" id="gdpr-cookie-accept-as-button" :reduce="label => label.code" :options="accept_as_button_options" v-model="accept_as_button"  @input="onButtonChange($event, 'accept')"></v-select>
									<input type="hidden" name="gdpr-cookie-accept-as" v-model="accept_as_button">
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-accept-action" :reduce="label => label.code" :options="accept_action_options" v-model="accept_action"  @input="cookieAcceptChange">
									</v-select>
									<input type="hidden" name="gdpr-cookie-accept-action" v-model="accept_action">
								</c-col>
							</c-row>
							<c-row v-show="is_open_url"  class="gdpr-label-row">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row v-show="is_open_url">
								<c-col class="col-sm-6">
									<c-input name="gdpr-cookie-accept-url" v-model="accept_url"></c-input>
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-url-new-window" :reduce="label => label.code" :options="open_url_options" v-model="open_url"></v-select>
									<input type="hidden" name="gdpr-cookie-url-new-window" v-model="open_url">
								</c-col>
							</c-row>
							<c-row class="gdpr-label-row"  v-show="accept_as_button">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row  v-show="accept_as_button">
								<c-col class="col-sm-6  gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="accept_background_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-background-color" type="color" name="gdpr-cookie-accept-background-color" v-model="accept_background_color"></c-input>
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-accept-size" :reduce="label => label.code" :options="accept_size_options" v-model="accept_size">
									</v-select>
									<input type="hidden" name="gdpr-cookie-accept-size" v-model="accept_size">
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
									<v-select class="form-group" id="gdpr-cookie-accept-border-style" :reduce="label => label.code" :options="border_style_options" v-model="accept_style">
									</v-select>
									<input type="hidden" name="gdpr-cookie-accept-border-style" v-model="accept_style">
								</c-col>
								<c-col class="col-sm-6 gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="accept_border_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-border-color" type="color" name="gdpr-cookie-accept-border-color" v-model="accept_border_color"></c-input>
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
									<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="accept_opacity"></c-input>
									<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-accept-opacity" v-model="accept_opacity"></c-input>
								</c-col>
								<c-col class="col-sm-4 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="accept_border_width"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-border-width" v-model="accept_border_width"></c-input>
								</c-col>
								<c-col class="col-sm-4  gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="accept_border_radius"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-border-radius" v-model="accept_border_radius"></c-input>
								</c-col>
							</c-row>
									<button type="button" class="done-button-settings" @click="accept_button_popup=false">Done</button></div>
								
							</c-modal></div>
							<!-- Accept All Button -->
							<c-row  v-show="is_gdpr || is_eprivacy || is_lgpd">
								<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Accept All Button', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<c-row  v-show="is_gdpr || is_eprivacy || is_lgpd">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-4">
									<c-switch v-bind="labelIcon" v-model="cookie_accept_all_on" id="gdpr-cookie-consent-cookie-acceptall-on" variant="3d"  color="success" :checked="cookie_accept_all_on" v-on:update:checked="onSwitchCookieAcceptAllEnable"></c-switch>
									<input type="hidden" name="gcc-cookie-accept-all-enable" v-model="cookie_accept_all_on">
								</c-col>
								<c-col class="col-sm-3"  v-show="is_gdpr || is_eprivacy || is_lgpd">
									<c-button :disabled="!cookie_accept_all_on" class="gdpr-configure-button" @click="accept_all_button_popup=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
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
							<div class="optout-settings-main-container">
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
									<c-input name="button_accept_all_text_field" v-model="accept_all_text"></c-input>
								</c-col>
								<c-col class="col-sm-6 gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="accept_all_text_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-all-text-color" type="color" name="gdpr-cookie-accept-all-text-color" v-model="accept_all_text_color"></c-input>
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
									<v-select class="form-group" id="gdpr-cookie-accept-all-action" :reduce="label => label.code" :options="accept_action_options" v-model="accept_all_action"  @input="cookieAcceptAllChange">
									</v-select>
									<input type="hidden" name="gdpr-cookie-accept-all-action" v-model="accept_all_action">
								</c-col>
							</c-row>
							<c-row v-show="accept_all_open_url"  class="gdpr-label-row">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row v-show="accept_all_open_url">
								<c-col class="col-sm-6">
									<c-input name="gdpr-cookie-accept-all-url" v-model="accept_all_url"></c-input>
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
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row  v-show="accept_all_as_button">
								<c-col class="col-sm-6  gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="accept_all_background_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-all-background-color" type="color" name="gdpr-cookie-accept-all-background-color" v-model="accept_all_background_color"></c-input>
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-accept-all-size" :reduce="label => label.code" :options="accept_size_options" v-model="accept_all_size">
									</v-select>
									<input type="hidden" name="gdpr-cookie-accept-all-size" v-model="accept_all_size">
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
									<c-input class="gdpr-color-input" type="text" v-model="accept_all_border_color"></c-input>
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
									<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="accept_all_opacity"></c-input>
									<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-accept-all-opacity" v-model="accept_all_opacity"></c-input>
								</c-col>
								<c-col class="col-sm-4 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="accept_all_border_width"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-all-border-width" v-model="accept_all_border_width"></c-input>
								</c-col>
								<c-col class="col-sm-4  gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="accept_all_border_radius"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-all-border-radius" v-model="accept_all_border_radius"></c-input>
								</c-col>
							</c-row>
									<button type="button" class="done-button-settings" @click="accept_all_button_popup=false">Done</button></div>
							</c-modal></div>
							<!-- Decline Button -->
							<c-row v-show="is_gdpr || is_eprivacy || is_lgpd">
								<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Decline Button', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<c-row v-show="is_gdpr || is_eprivacy || is_lgpd"> 
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-4">
									<c-switch v-bind="labelIcon" v-model="cookie_decline_on" id="gdpr-cookie-consent-decline-on" variant="3d"  color="success" :checked="cookie_decline_on" v-on:update:checked="onSwitchCookieDeclineEnable"></c-switch>
									<input type="hidden" name="gcc-cookie-decline-enable" v-model="cookie_decline_on">
								</c-col>
								<c-col class="col-sm-3" v-show="is_gdpr || is_eprivacy || is_lgpd">
									<c-button :disabled="!cookie_decline_on" class="gdpr-configure-button" @click="decline_button_popup=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
							</c-row>
							<div class="opt-out-link-container">
							<c-modal
								:show.sync="decline_button_popup"
								size="lg"
								:close-on-backdrop="closeOnBackdrop"
								:centered="centered"
							>
							<div class="optout-settings-tittle-bar">
								<div class="optout-setting-tittle"><?php esc_attr_e( 'Decline Button', 'gdpr-cookie-consent' ); ?></div>
								<img @click="decline_button_popup=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
								</div>
								<div class="optout-settings-main-container">
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
										<c-input name="button_decline_text_field" v-model="decline_text"></c-input>
									</c-col>
									<c-col class="col-sm-6  gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="decline_text_color"></c-input>
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
									<c-col class="col-sm-6"><v-select class="form-group" id="gdpr-cookie-decline-action" :reduce="label => label.code" :options="decline_action_options" v-model="decline_action" @input="cookieDeclineChange">
										</v-select>
										<input type="hidden" name="gdpr-cookie-decline-action" v-model="decline_action">
									</c-col>
								</c-row>
								<c-row v-show="decline_open_url" class="gdpr-label-row">
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
								</c-row>
								<c-row v-show="decline_open_url">
									<c-col class="col-sm-6">
										<c-input name="gdpr-cookie-decline-url" v-model="decline_url"></c-input>
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
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
								</c-row>
								<c-row v-show="decline_as_button">
									<c-col class="col-sm-6  gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="decline_background_color"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-decline-background-color" type="color" name="gdpr-cookie-decline-background-color" v-model="decline_background_color"></c-input>
									</c-col>
									<c-col class="col-sm-6">
										<v-select class="form-group" id="gdpr-cookie-decline-size" :reduce="label => label.code" :options="accept_size_options" v-model="decline_size">
										</v-select>
										<input type="hidden" name="gdpr-cookie-decline-size" v-model="decline_size">
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
										<c-input class="gdpr-color-input" type="text" v-model="decline_border_color"></c-input>
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
									<c-col class="col-sm-4 gdpr-color-pick"><c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="decline_opacity"></c-input>
										<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-decline-opacity" v-model="decline_opacity"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick"><c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="decline_border_width"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-decline-border-width" v-model="decline_border_width"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="decline_border_radius"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-decline-border-radius" v-model="decline_border_radius"></c-input>
									</c-col>
								</c-row>
										<button type="button" class="done-button-settings" @click="decline_button_popup=false">Done</button></div>
							</c-modal></div>
							<!-- Settings Button -->
							<c-row v-show="is_gdpr || is_lgpd">
								<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Settings Button', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<c-row v-show="is_gdpr || is_lgpd">
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-4">
									<c-switch v-bind="labelIcon" v-model="cookie_settings_on" id="gdpr-cookie-consent-settings-on" variant="3d"  color="success" :checked="cookie_settings_on" v-on:update:checked="onSwitchCookieSettingsEnable"></c-switch>
									<input type="hidden" name="gcc-cookie-settings-enable" v-model="cookie_settings_on">
								</c-col>
								<c-col class="col-sm-3" v-show="is_gdpr || is_lgpd">
									<c-button :disabled="!cookie_settings_on" class="gdpr-configure-button" @click="settings_button_popup=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
									<c-col class="col-sm-4">
										<label><?php esc_attr_e( 'Display Cookies List on Frontend', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-4">
										<c-switch v-bind="labelIcon" v-model="cookie_on_frontend" id="gdpr-cookie-consent-cookie-frontend" variant="3d"  color="success" :checked="cookie_on_frontend" v-on:update:checked="onSwitchCookieOnFrontend"></c-switch>
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
									<img  @click="settings_button_popup=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
								</div>
								<div class="optout-settings-main-container">
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
									<c-input name="button_settings_text_field" v-model="settings_text"></c-input>
								</c-col>
								<c-col class="col-sm-6  gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="settings_text_color"></c-input>
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
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row v-show="settings_as_button" class="gdpr-label-row">
								<c-col class="col-sm-6 gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="settings_background_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-settings-background-color" type="color" name="gdpr-cookie-settings-background-color" v-model="settings_background_color"></c-input>
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-settings-size" :reduce="label => label.code" :options="accept_size_options" v-model="settings_size">
									</v-select>
									<input type="hidden" name="gdpr-cookie-settings-size" v-model="settings_size">
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
									<c-input class="gdpr-color-input" type="text" v-model="settings_border_color"></c-input>
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
									<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="settings_opacity"></c-input>
									<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-settings-opacity" v-model="settings_opacity"></c-input>
								</c-col>
								<c-col class="col-sm-4 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="settings_border_width"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-settings-border-width" v-model="settings_border_width"></c-input>
								</c-col>
								<c-col class="col-sm-4 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="settings_border_radius"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-settings-border-radius" v-model="settings_border_radius"></c-input>
								</c-col>
							</c-row>
								<button  type="button" class="done-button-settings" @click="settings_button_popup=false">Done</button></div>
							</c-modal></div>
							<!-- Confirm button -->
							<c-row v-show="is_ccpa">
								<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Confirm Button', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<c-row v-show="is_ccpa">
								<c-col class="col-sm-8"><label><?php esc_attr_e( 'Confirm Button Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-4" v-show="is_ccpa">
									<c-button class="gdpr-configure-button" @click="confirm_button_popup=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
							</c-row>
							<div class="opt-out-link-container">
								<c-modal
									:show.sync="confirm_button_popup"
									size="lg"
									:close-on-backdrop="closeOnBackdrop"
									:centered="centered"
								>
								<div class="optout-settings-tittle-bar">
									<div class="optout-setting-tittle"><?php esc_attr_e( 'Confirm Button', 'gdpr-cookie-consent' ); ?></div>
									<img  @click="confirm_button_popup=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
								</div>
								<div class="optout-settings-main-container">
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
										<c-input name="button_confirm_text_field" v-model="confirm_text"></c-input>
									</c-col>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="confirm_text_color"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-confirm-text-color" type="color" name="gdpr-cookie-confirm-text-color" v-model="confirm_text_color"></c-input>
									</c-col>
								</c-row>
								<c-row class="gdpr-label-row">
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="confirm_background_color"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-confirm-background-color" type="color" name="gdpr-cookie-confirm-background-color" v-model="confirm_background_color"></c-input>
									</c-col>
									<c-col class="col-sm-6">
										<v-select class="form-group" id="gdpr-cookie-confirm-size" :reduce="label => label.code" :options="accept_size_options" v-model="confirm_size">
										</v-select>
										<input type="hidden" name="gdpr-cookie-confirm-size" v-model="confirm_size">
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
										<c-input class="gdpr-color-input" type="text" v-model="confirm_border_color"></c-input>
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
										<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="confirm_opacity"></c-input>
										<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-confirm-opacity" v-model="confirm_opacity"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="confirm_border_width"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-confirm-border-width" v-model="confirm_border_width"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="confirm_border_radius"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-confirm-border-radius" v-model="confirm_border_radius"></c-input>
									</c-col>
								</c-row>
								<button  type="button" class="done-button-settings" @click="confirm_button_popup=false">Done</button></div>
							</c-modal></div>
							<!-- Cancle button -->
							<c-row  v-show="is_ccpa">
								<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Cancel Button', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<c-row  v-show="is_ccpa">
								<c-col class="col-sm-8"><label><?php esc_attr_e( 'Cancel Button Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-4" v-show="is_ccpa">
									<c-button class="gdpr-configure-button" @click="cancel_button_popup=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
							</c-row>
							<div class="opt-out-link-container">
								<c-modal
									:show.sync="cancel_button_popup"
									size="lg"
									:close-on-backdrop="closeOnBackdrop"
									:centered="centered"
								>
								<div class="optout-settings-tittle-bar">
									<div class="optout-setting-tittle"><?php esc_attr_e( 'Cancle Button', 'gdpr-cookie-consent' ); ?></div>
									<img  @click="cancel_button_popup=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
								</div>
								<div class="optout-settings-main-container">
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
										<c-input name="button_cancel_text_field" v-model="cancel_text"></c-input>
									</c-col>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="cancel_text_color"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-cancel-text-color" type="color" name="gdpr-cookie-cancel-text-color" v-model="cancel_text_color"></c-input>
									</c-col>
								</c-row>
								<c-row class="gdpr-label-row">
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="cancel_background_color"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-cancel-background-color" type="color" name="gdpr-cookie-cancel-background-color" v-model="cancel_background_color"></c-input>
									</c-col>
									<c-col class="col-sm-6">
										<v-select class="form-group" id="gdpr-cookie-cancel-size" :reduce="label => label.code" :options="accept_size_options" v-model="cancel_size">
										</v-select>
										<input type="hidden" name="gdpr-cookie-cancel-size" v-model="cancel_size">
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
										<v-select class="form-group" id="gdpr-cookie-cancel-border-style" :reduce="label => label.code" :options="border_style_options" v-model="cancel_style">
										</v-select>
										<input type="hidden" name="gdpr-cookie-cancel-border-style" v-model="cancel_style">
									</c-col>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="cancel_border_color"></c-input>
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
										<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="cancel_opacity"></c-input>
										<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1"  name="gdpr-cookie-cancel-opacity" v-model="cancel_opacity"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="cancel_border_width"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-cancel-border-width" v-model="cancel_border_width"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="cancel_border_radius"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-cancel-border-radius" v-model="cancel_border_radius"></c-input>
									</c-col>
								</c-row>
										<button  type="button" class="done-button-settings" @click="cancel_button_popup=false">Done</button></div>
							</c-modal></div>
							<!-- Opt-out button -->
							<c-row v-show="is_ccpa">
								<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Opt-out Button', 'gdpr-cookie-consent' ); ?></div></c-col>
							</c-row>
							<c-row v-show="is_ccpa">
								<c-col class="col-sm-8"><label><?php esc_attr_e( 'Opt-out Link Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-4" v-show="is_ccpa">
									<c-button class="gdpr-configure-button" @click="opt_out_link_popup=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
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
									<div class="optout-setting-tittle"><?php esc_attr_e( 'Opt-out Link', 'gdpr-cookie-consent' ); ?></div>
									<img  @click="opt_out_link_popup=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
								</div>
								<div class="optout-settings-main-container">
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
											<c-input name="button_donotsell_text_field" v-model="opt_out_text"></c-input>
										</c-col>
										<c-col class="col-sm-6 gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="opt_out_text_color"></c-input>
											<c-input class="gdpr-color-select" id="gdpr-cookie-opt-out-text-color" type="color" name="gdpr-cookie-opt-out-text-color" v-model="opt_out_text_color"></c-input>
										</c-col>
									</c-row>
									<button  type="button" class="done-button-settings" @click="opt_out_link_popup=false">Done</button>
								</div>
							</c-modal> </div>

					</c-card>
					</c-card>
					<!-- Adding Virat-->
					<!-- Desgin Banner preview if A/B Testing is disabled and GDPR&CCPA both are selected-->
					<c-card class=" desgin_card" v-show="!ab_testing_enabled && gdpr_policy == 'both'">
						<div class="gdpr-cookie-consent-banner-tabs">
							<c-button class="gdpr-cookie-consent-banner-tab"@click="changeActiveMultipleLegislationToGDPR":class="{ 'gdpr-cookie-consent-banner-tab-active': active_default_multiple_legislation === 'gdpr' }"><?php esc_html_e( 'GDPR Banner' , 'gdpr-cookie-consent' ); ?></c-button>
							<c-button class="gdpr-cookie-consent-banner-tab"@click="changeActiveMultipleLegislationToCCPA":class="{ 'gdpr-cookie-consent-banner-tab-active': active_default_multiple_legislation === 'ccpa' }"><?php esc_html_e(  'CCPA Banner' , 'gdpr-cookie-consent' ); ?></c-button>
						</div>
						<c-card-body v-show="active_default_multiple_legislation === 'gdpr'">
						<c-card-body >
									<!-- <c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Consent Banner Title', 'gdpr-cookie-consent' ); ?> </label></c-col>
										<c-col class="col-sm-8">
											<c-input name="gdpr-cookie_bar1_name" v-model="cookie_bar1_name"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Make this banner default', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<c-switch v-bind="labelIcon" v-model="default_cookie_bar" id="gdpr-cookie-consent-default_cookie_bar1" variant="3d"  color="success" :checked="default_cookie_bar" v-on:update:checked="onSwitchDefaultCookieBar"></c-switch>
											<input type="hidden" name="gdpr-default_cookie_bar" v-model="default_cookie_bar">
										</c-col> 
									</c-row> -->
									
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

									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cookie Bar Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="multiple_legislation_cookie_bar_color1"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-multiple-legislation-cookie-bar-color1" type="color" name="gdpr-multiple-legislation-cookie-bar-color1" v-model="multiple_legislation_cookie_bar_color1"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( ' Cookie Bar Opacity', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="multiple_legislation_cookie_bar_opacity1"></c-input>
										<c-input class="gdpr-slider-input opacity-slider" type="number"  min="0" max="1" step="0.01" name="gdpr-multiple-legislation-cookie-bar-opacity1" v-model="multiple_legislation_cookie_bar_opacity1"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="multiple_legislation_cookie_text_color1"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-multiple-legislation-cookie-text-color1" type="color" name="gdpr-multiple-legislation-cookie-text-color1" v-model="multiple_legislation_cookie_text_color1"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Styles', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<v-select class="form-group" id="gdpr-multiple-legislation-cookie-border-style1" :reduce="label => label.code" :options="border_style_options" v-model="multiple_legislation_border_style1">
											</v-select>
											<input type="hidden" name="gdpr-multiple-legislation-cookie-border-style1" v-model="multiple_legislation_border_style1">
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="multiple_legislation_cookie_bar_border_width1"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-multiple-legislation-cookie-bar-border-width1" v-model="multiple_legislation_cookie_bar_border_width1"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="multiple_legislation_cookie_border_color1"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-multiple-legislation-cookie-border-color1" type="color" name="gdpr-multiple-legislation-cookie-border-color1" v-model="multiple_legislation_cookie_border_color1"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="multiple_legislation_cookie_bar_border_radius1"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-multiple-legislation-cookie-bar-border-radius1" v-model="multiple_legislation_cookie_bar_border_radius1"></c-input>
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
													<v-select disabled class="form-group" id="gdpr-cookie-font" :reduce="label => label.code" :options="font_options" v-model="multiple_legislation_cookie_font1">
													</v-select>
													<input type="hidden" name="gdpr-multiple-legislation-cookie-font1" v-model="multiple_legislation_cookie_font1">
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
													<v-select class="form-group" id="gdpr-cookie-font" :reduce="label => label.code" :options="font_options" v-model="multiple_legislation_cookie_font1">
													</v-select>
													<input type="hidden" name="gdpr-multiple-legislation-cookie-font1" v-model="multiple_legislation_cookie_font1	">
												</c-col>
											</c-row>
										<?php } ?>
									<?php
										?>
									<c-row>
									<c-col class="col-sm-4">
											<label><?php esc_attr_e( 'Upload Logo ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'To preview the logo, simply upload a logo and then click the "Save Changes" button ', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
											</c-col>
											<c-col class="col-sm-8 ">
												<c-button color="info" class="button" id="image-upload-button" name="image-upload-buttonML1" @click="openMediaModalML1" style="margin: 10px;">
													<?php esc_attr_e( 'Add Image', 'gdpr-cookie-consent' ); ?>
												</c-button>
												<c-button color="info" class="button" id="image-delete-button" @click="deleteSelectedimageML1" style="margin: 10px; ">
													<?php esc_attr_e( 'Remove Image', 'gdpr-cookie-consent' ); ?>
												</c-button>
												<?php
												$get_banner_imgml = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELDML1 );
												?>
												<img id="gdpr-cookie-bar-logo-holderML1" name="gdpr-cookie-bar-logo-holderML1" src="<?php echo esc_url_raw( $get_banner_imgml ); ?>">
												<p class="image-upload-notice" style="margin-left: 10px;">
													<?php esc_attr_e( 'We recommend 50 x 50 pixels.', 'gdpr-cookie-consent' ); ?>
												</p>
												<c-input type="hidden" name="gdpr-cookie-bar-logo-url-holderML1" id="gdpr-cookie-bar-logo-url-holderML1"  class="regular-text"> </c-input>
									</c-col>
								</c-row>
								</c-card-body>

								<c-card-body>
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
												<c-row v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on">
													<c-col class="col-sm-6">
														<v-select class="form-group" id="gcc-readmore-as-button" :reduce="label => label.code" :options="show_as_options" v-model="button_readmore_as_button"></v-select>
														<input type="hidden" name="gcc-readmore-as-button" v-model="button_readmore_as_button">
													</c-col>
													<c-col class="col-sm-6">
														<v-select class="form-group" id="gcc-readmore-url-type" :reduce="label => label.code" :options="url_type_options" v-model="button_readmore_url_type"></v-select>
														<input type="hidden" name="gcc-readmore-url-type" v-model="button_readmore_url_type">
													</c-col>
												</c-row>

												<div v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on">
													<c-row v-show="button_readmore_as_button" class="gdpr-label-row">
														<c-col class="col-sm-6"><label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label></c-col>
														<c-col class="col-sm-6"><label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label></c-col>	
													</c-row>
													<c-row v-show="button_readmore_as_button">
														<c-col class="col-sm-6 gdpr-color-pick" >
															<c-input class="gdpr-color-input" type="text" v-model="button_readmore_button_color"></c-input>
															<c-input class="gdpr-color-select" id="gdpr-readmore-button-color" type="color" name="gcc-readmore-button-color" v-model="button_readmore_button_color"></c-input>
														</c-col>
														<c-col class="col-sm-6">
															<v-select class="form-group" id="gcc-readmore-button-size" :reduce="label => label.code" :options="button_size_options" v-model="button_readmore_button_size"></v-select>
															<input type="hidden" name="gcc-readmore-button-size" v-model="button_readmore_button_size">
														</c-col>
													</c-row>
													<c-row v-show="button_readmore_as_button" class="gdpr-label-row">
														<c-col class="col-sm-6"><label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label></c-col>
														<c-col class="col-sm-6"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label></c-col>
													</c-row>
													<c-row v-show="button_readmore_as_button">
														<c-col class="col-sm-6">
															<v-select class="form-group" id="gcc-readmore-button-border-style" :reduce="label => label.code" :options="border_style_options" v-model="button_readmore_button_border_style"></v-select>
															<input type="hidden" name="gcc-readmore-button-border-style" v-model="button_readmore_button_border_style">
														</c-col>
														<c-col class="col-sm-6 gdpr-color-pick" >
															<c-input class="gdpr-color-input" type="text" v-model="button_readmore_button_border_color"></c-input>
															<c-input class="gdpr-color-select" id="gdpr-readmore-button-border-color" type="color" name="gcc-readmore-button-border-color" v-model="button_readmore_button_border_color"></c-input>
														</c-col>
													</c-row>
													<c-row class="gdpr-label-row">
														<c-col class="col-sm-6" v-show="button_readmore_url_type"><label><?php esc_attr_e( 'Page', 'gdpr-cookie-consent' ); ?></label></c-col>
														<c-col v-show="!button_readmore_url_type" class="col-sm-6"><label><?php esc_attr_e( 'URL', 'gdpr-cookie-consent' ); ?></label></c-col>
														<c-col class="col-sm-3 gdpr-readmore-toggle-row" v-show="button_readmore_url_type"><label><?php esc_attr_e( 'Sync with WordPress Policy Page', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'If enabled visitor will be redirected to Privacy Policy Page set in WordPress settings irrespective of Page set in the previous setting.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
														<c-col class="col-sm-3 gdpr-readmore-toggle-row" v-show="button_readmore_url_type">
															<c-switch v-bind="labelIcon" v-model="button_readmore_wp_page" id="gdpr-cookie-consent-readmore-wp-page" variant="3d"  color="success" :checked="button_readmore_wp_page" v-on:update:checked="onSwitchButtonReadMoreWpPage"></c-switch>
															<input type="hidden" name="gcc-readmore-wp-page" v-model="button_readmore_wp_page">
														</c-col>
													</c-row>
													<c-row>
														<c-col v-show="button_readmore_url_type" class="col-sm-6">
															<v-select class="form-group"  placeholder="Select Policy Page" id="gcc-readmore-page" :reduce="label => label.code" :options="privacy_policy_options" v-model="readmore_page" @input="onSelectPrivacyPage"></v-select>
															<input type="hidden" name="gcc-readmore-page" v-model="button_readmore_page">
														</c-col>
														<c-col class="col-sm-6" v-show="!button_readmore_url_type">
															<c-input name="gcc-readmore-url" v-model="button_readmore_url"></c-input>
														</c-col>
														<c-col class="col-sm-3 gdpr-readmore-toggle-row"><label><?php esc_attr_e( 'Open URL in New Window?', 'gdpr-cookie-consent' ); ?></label></c-col>
														<c-col class="col-sm-3 gdpr-readmore-toggle-row">
															<c-switch v-bind="labelIcon" v-model="button_readmore_new_win" id="gdpr-cookie-consent-readmore-new-win" variant="3d"  color="success" :checked="button_readmore_new_win" v-on:update:checked="onSwitchButtonReadMoreNewWin"></c-switch>
															<input type="hidden" name="gcc-readmore-new-win" v-model="button_readmore_new_win">
														</c-col>
													</c-row>
													<c-row v-show="button_readmore_as_button" class="gdpr-label-row">
														<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label></c-col>
														<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label></c-col>
														<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label></c-col>
													</c-row>
													<c-row v-show="button_readmore_as_button">
														<c-col class="col-sm-4 gdpr-color-pick">
															<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="button_readmore_button_opacity"></c-input>
															<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-opacity" v-model="button_readmore_button_opacity"></c-input>
														</c-col>
														<c-col class="col-sm-4 gdpr-color-pick">
															<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="button_readmore_button_border_width"></c-input>
															<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-border-width" v-model="button_readmore_button_border_width"></c-input>
														</c-col>
														<c-col class="col-sm-4 gdpr-color-pick">
															<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="button_readmore_button_border_radius"></c-input>
															<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-border-radius" v-model="button_readmore_button_border_radius"></c-input>
														</c-col>
													</c-row>	
												</div>

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
											<c-switch v-bind="labelIcon" v-model="cookie_accept_on1" id="gdpr-cookie-consent-cookie1" variant="3d"  color="success" :checked="cookie_accept_on1" v-on:update:checked="onSwitchCookieAcceptEnable1"></c-switch>
											<input type="hidden" name="gcc-cookie-accept-enable1" v-model="cookie_accept_on1">
										</c-col>
										<c-col class="col-sm-3">
											<c-button :disabled="!cookie_accept_on1" class="gdpr-configure-button" @click="accept_button_popup1=true">
												<span>
													<img class="gdpr-configure-image" :src="configure_image_url.default">
													<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
												</span>
											</c-button>
										</c-col>
									</c-row>
									<div class="opt-out-link-container">
									<c-modal
										title="Accept Button"
										:show.sync="accept_button_popup1"
										size="lg"
										:close-on-backdrop="closeOnBackdrop"
										:centered="centered"
									>
									<div class="optout-settings-tittle-bar">
											<div class="optout-setting-tittle"><?php esc_attr_e( 'Accept Button', 'gdpr-cookie-consent' ); ?></div>
											<img @click="accept_button_popup1=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
											<c-input name="button_accept_text_field1" v-model="accept_text1"></c-input>
										</c-col>
										<c-col class="col-sm-6 gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="accept_text_color1"></c-input>
											<c-input class="gdpr-color-select" id="gdpr-cookie-accept-text-color1" type="color" name="gdpr-cookie-accept-text-color1" v-model="accept_text_color1"></c-input>
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
											<v-select class="form-group" id="gdpr-cookie-accept-as-button1" :reduce="label => label.code" :options="accept_as_button_options" v-model="accept_as_button1" @input="onButtonChange($event, 'accept1')"></v-select>
											<input type="hidden" name="gdpr-cookie-accept-as1" v-model="accept_as_button1">
										</c-col>
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-accept-action1" :reduce="label => label.code" :options="accept_action_options" v-model="accept_action1" 	>
											</v-select>
											<input type="hidden" name="gdpr-cookie-accept-action1" v-model="accept_action1">
										</c-col>
									</c-row>
									<c-row v-show="accept_action1!='#cookie_action_close_header'"  class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row v-show="accept_action1!='#cookie_action_close_header'">
										<c-col class="col-sm-6">
											<c-input name="gdpr-cookie-accept-url1" v-model="accept_url1"></c-input>
										</c-col>
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-url-new-window1" :reduce="label => label.code" :options="open_url_options" v-model="open_url1"></v-select>
											<input type="hidden" name="gdpr-cookie-url-new-window1" v-model="open_url1">
										</c-col>
									</c-row>
									<c-row class="gdpr-label-row"  v-show="accept_as_button1">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row  v-show="accept_as_button1">
										<c-col class="col-sm-6  gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="accept_background_color1"></c-input>
											<c-input class="gdpr-color-select" id="gdpr-cookie-accept-background-color1" type="color" name="gdpr-cookie-accept-background-color1" v-model="accept_background_color1"></c-input>
										</c-col>
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-accept-size1" :reduce="label => label.code" :options="accept_size_options" v-model="accept_size1">
											</v-select>
											<input type="hidden" name="gdpr-cookie-accept-size1" v-model="accept_size1">
										</c-col>
									</c-row>
									<c-row  v-show="accept_as_button1" class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row v-show="accept_as_button1">
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-accept-border-style1" :reduce="label => label.code" :options="border_style_options" v-model="accept_style1">
											</v-select>
											<input type="hidden" name="gdpr-cookie-accept-border-style1" v-model="accept_style1">
										</c-col>
										<c-col class="col-sm-6 gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="accept_border_color1"></c-input>
											<c-input class="gdpr-color-select" id="gdpr-cookie-accept-border-color" type="color" name="gdpr-cookie-accept-border-color1" v-model="accept_border_color1"></c-input>
										</c-col>
									</c-row>
									<c-row v-show="accept_as_button1" class="gdpr-label-row">
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
									<c-row v-show="accept_as_button1">
										<c-col class="col-sm-4  gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="accept_opacity1"></c-input>
											<c-input class="gdpr-slider-input opacity-slider" type="number"  min="0" max="1" step="0.1"  name="gdpr-cookie-accept-opacity1" v-model="accept_opacity1"></c-input>
										</c-col>
										<c-col class="col-sm-4 gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="accept_border_width1"></c-input>
											<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-border-width1" v-model="accept_border_width1"></c-input>
										</c-col>
										<c-col class="col-sm-4  gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="accept_border_radius1"></c-input>
											<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-border-radius1" v-model="accept_border_radius1"></c-input>
										</c-col>
									</c-row>

											<button  class="done-button-settings" @click="accept_button_popup1=false"><span>Done</span></button>

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
											<c-switch v-bind="labelIcon" v-model="cookie_accept_all_on1" id="gdpr-cookie-consent-cookie-acceptall-on1" variant="3d"  color="success" :checked="cookie_accept_all_on1" v-on:update:checked="onSwitchCookieAcceptAllEnable1"></c-switch>
											<input type="hidden" name="gcc-cookie-accept-all-enable1" v-model="cookie_accept_all_on1">
										</c-col>
										<c-col class="col-sm-3">
											<c-button :disabled="!cookie_accept_all_on1" class="gdpr-configure-button" @click="accept_all_button_popup1=true">
												<span>
													<img class="gdpr-configure-image" :src="configure_image_url.default">
													<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
												</span>
											</c-button>
										</c-col>
									</c-row>
									<div class="opt-out-link-container">
									<c-modal
										title="Accept All Button"
										:show.sync="accept_all_button_popup1"
										size="lg"
										:close-on-backdrop="closeOnBackdrop"
										:centered="centered"
									>
									<div class="optout-settings-tittle-bar">
											<div class="optout-setting-tittle"><?php esc_attr_e( 'Accept All Button', 'gdpr-cookie-consent' ); ?></div>
											<img @click="accept_all_button_popup1=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
											<c-input name="button_accept_all_text_field1" v-model="accept_all_text1"></c-input>
										</c-col>
										<c-col class="col-sm-6 gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="accept_all_text_color1"></c-input>
											<c-input class="gdpr-color-select" id="gdpr-cookie-accept-all-text-color1" type="color" name="gdpr-cookie-accept-all-text-color1" v-model="accept_all_text_color1"></c-input>
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
											<v-select class="form-group" id="gdpr-cookie-accept-all-as-button1" :reduce="label => label.code" :options="accept_as_button_options" v-model="accept_all_as_button1" @input="onButtonChange($event, 'accept_all1')"></v-select>
											<input type="hidden" name="gdpr-cookie-accept-all-as1" v-model="accept_all_as_button1">
										</c-col>
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-accept-all-action1" :reduce="label => label.code" :options="accept_action_options" v-model="accept_all_action1" >
											</v-select>
											<input type="hidden" name="gdpr-cookie-accept-all-action1" v-model="accept_all_action1">
										</c-col>
									</c-row>
									<c-row v-show="accept_all_action1!='#cookie_action_close_header'"  class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row v-show="accept_all_action1!='#cookie_action_close_header'">
										<c-col class="col-sm-6">
											<c-input name="gdpr-cookie-accept-all-url1" v-model="accept_all_url1"></c-input>
										</c-col>
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-accept-all-new-window1" :reduce="label => label.code" :options="open_url_options" v-model="accept_all_new_win1"></v-select>
											<input type="hidden" name="gdpr-cookie-accept-all-new-window1" v-model="accept_all_new_win1">
										</c-col>
									</c-row>
									<c-row class="gdpr-label-row"  v-show="accept_all_as_button1">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row  v-show="accept_all_as_button1">
										<c-col class="col-sm-6  gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="accept_all_background_color1"></c-input>
											<c-input class="gdpr-color-select" id="gdpr-cookie-accept-all-background-color1" type="color" name="gdpr-cookie-accept-all-background-color1" v-model="accept_all_background_color1"></c-input>
										</c-col>
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-accept-all-size1" :reduce="label => label.code" :options="accept_size_options" v-model="accept_all_size1">
											</v-select>
											<input type="hidden" name="gdpr-cookie-accept-all-size1" v-model="accept_all_size1">
										</c-col>
									</c-row>
									<c-row  v-show="accept_all_as_button1" class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row v-show="accept_all_as_button1">
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-accept-all-border-style1" :reduce="label => label.code" :options="border_style_options" v-model="accept_all_style1">
											</v-select>
											<input type="hidden" name="gdpr-cookie-accept-all-border-style1" v-model="accept_all_style1">
										</c-col>
										<c-col class="col-sm-6 gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="accept_all_border_color1"></c-input>
											<c-input class="gdpr-color-select" id="gdpr-cookie-accept-all-border-color1" type="color" name="gdpr-cookie-accept-all-border-color1" v-model="accept_all_border_color1"></c-input>
										</c-col>
									</c-row>
									<c-row v-show="accept_all_as_button1" class="gdpr-label-row">
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
									<c-row v-show="accept_all_as_button1">
										<c-col class="col-sm-4  gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="accept_all_opacity1"></c-input>
											<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-accept-all-opacity1" v-model="accept_all_opacity1"></c-input>
										</c-col>
										<c-col class="col-sm-4 gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="accept_all_border_width1"></c-input>
											<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-all-border-width1" v-model="accept_all_border_width1"></c-input>
										</c-col>
										<c-col class="col-sm-4  gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="multiple_legislation_accept_all_border_radius1"></c-input>
											<c-input class="gdpr-slider-input"type="number" name="gdpr-multiple-legislation-cookie-accept-all-border-radius1" v-model="multiple_legislation_accept_all_border_radius1"></c-input>
										</c-col>
									</c-row>
											<button class="done-button-settings" @click="accept_all_button_popup1=false"><span>Done</span></button>

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
											<c-switch v-bind="labelIcon" v-model="cookie_decline_on1" id="gdpr-cookie-consent-decline-on1" variant="3d"  color="success" :checked="cookie_decline_on1" v-on:update:checked="onSwitchCookieDeclineEnable1"></c-switch>
											<input type="hidden" name="gcc-cookie-decline-enable1" v-model="cookie_decline_on1">
										</c-col>
										<c-col class="col-sm-3">
											<c-button :disabled="!cookie_decline_on1" class="gdpr-configure-button" @click="decline_button_popup1=true">
												<span>
													<img class="gdpr-configure-image" :src="configure_image_url.default">
													<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
												</span>
											</c-button>
										</c-col>
									</c-row>
									<div class="opt-out-link-container">
									<c-modal
										title="Decline Button"
										:show.sync="decline_button_popup1"
										size="lg"
										:close-on-backdrop="closeOnBackdrop"
										:centered="centered"
									>
									<div class="optout-settings-tittle-bar">
											<div class="optout-setting-tittle"><?php esc_attr_e( 'Decline Button', 'gdpr-cookie-consent' ); ?></div>
											<img @click="decline_button_popup1=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
												<c-input name="button_decline_text_field1" v-model="decline_text1"></c-input>
											</c-col>
											<c-col class="col-sm-6  gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="decline_text_color1"></c-input>
												<c-input class="gdpr-color-select" id="gdpr-cookie-decline-text-color1" type="color" name="gdpr-cookie-decline-text-color1" v-model="decline_text_color1"></c-input>
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
												<v-select class="form-group" id="gdpr-cookie-decline-as-button1" :reduce="label => label.code" :options="accept_as_button_options" v-model="decline_as_button1" @input="onButtonChange($event, 'decline1')"></v-select>
												<input type="hidden" name="gdpr-cookie-decline-as1" v-model="decline_as_button1">
											</c-col>
											<c-col class="col-sm-6"><v-select class="form-group" id="gdpr-cookie-decline-action1" :reduce="label => label.code" :options="decline_action_options" v-model="decline_action1">
												</v-select>
												<input type="hidden" name="gdpr-cookie-decline-action1" v-model="decline_action1">
											</c-col>
										</c-row>
										<c-row v-show="decline_action1!='#cookie_action_close_header_reject'" class="gdpr-label-row">
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
										</c-row>
										<c-row v-show="decline_action1!='#cookie_action_close_header_reject'">
											<c-col class="col-sm-6">
												<c-input name="gdpr-cookie-decline-url1" v-model="decline_url1"></c-input>
											</c-col>
											<c-col class="col-sm-6">
												<v-select class="form-group" id="gdpr-cookie-decline-url-new-window1" :reduce="label => label.code" :options="open_url_options" v-model="open_decline_url1"></v-select>
												<input type="hidden" name="gdpr-cookie-decline-url-new-window1" v-model="open_decline_url1">
											</c-col>
										</c-row>
										<c-row v-show="decline_as_button1" class="gdpr-label-row">
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
										</c-row>
										<c-row v-show="decline_as_button1">
											<c-col class="col-sm-6  gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="decline_background_color1"></c-input>
												<c-input class="gdpr-color-select" id="gdpr-cookie-decline-background-color1" type="color" name="gdpr-cookie-decline-background-color1" v-model="decline_background_color1"></c-input>
											</c-col>
											<c-col class="col-sm-6">
												<v-select class="form-group" id="gdpr-cookie-decline-size1" :reduce="label => label.code" :options="accept_size_options" v-model="decline_size1">
												</v-select>
												<input type="hidden" name="gdpr-cookie-decline-size1" v-model="decline_size1">
											</c-col>
										</c-row>
										<c-row v-show="decline_as_button1" class="gdpr-label-row">
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
										</c-row>
										<c-row v-show="decline_as_button1">
											<c-col class="col-sm-6">
												<v-select class="form-group" id="gdpr-cookie-decline-border-style1" :reduce="label => label.code" :options="border_style_options" v-model="decline_style1">
												</v-select>
												<input type="hidden" name="gdpr-cookie-decline-border-style1" v-model="decline_style1">
											</c-col>
											<c-col class="col-sm-6  gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="decline_border_color1"></c-input>
												<c-input class="gdpr-color-select" id="gdpr-cookie-decline-border-color1" type="color" name="gdpr-cookie-decline-border-color1" v-model="decline_border_color1"></c-input>
											</c-col>
										</c-row>
										<c-row  v-show="decline_as_button1" class="gdpr-label-row">
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
										<c-row v-show="decline_as_button1">
											<c-col class="col-sm-4 gdpr-color-pick"><c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="decline_opacity1"></c-input>
												<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-decline-opacity1" v-model="decline_opacity1"></c-input>
											</c-col>
											<c-col class="col-sm-4 gdpr-color-pick"><c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="decline_border_width1"></c-input>
												<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-decline-border-width1" v-model="decline_border_width1"></c-input>
											</c-col>
											<c-col class="col-sm-4 gdpr-color-pick">
												<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="decline_border_radius1"></c-input>
												<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-decline-border-radius1" v-model="decline_border_radius1"></c-input>
											</c-col>
										</c-row> 
												<button class="done-button-settings" @click="decline_button_popup1=false"><span>Done</span></button>

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
											<c-switch v-bind="labelIcon" v-model="cookie_settings_on1" id="gdpr-cookie-consent-settings-on1" variant="3d"  color="success" :checked="cookie_settings_on1" v-on:update:checked="onSwitchCookieSettingsEnable1"></c-switch>
											<input type="hidden" name="gcc-cookie-settings-enable1" v-model="cookie_settings_on1">
										</c-col>
										<c-col class="col-sm-3">
											<c-button :disabled="!cookie_settings_on1" class="gdpr-configure-button" @click="settings_button_popup1=true">
												<span>
													<img class="gdpr-configure-image" :src="configure_image_url.default">
													<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
												</span>
											</c-button>
										</c-col>
										<c-col class="col-sm-4">
												<label><?php esc_attr_e( 'Display Cookies List on Frontend', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-4">
												<c-switch v-bind="labelIcon" v-model="cookie_on_frontend1" id="gdpr-cookie-consent-cookie-on-frontend1" variant="3d"  color="success" :checked="cookie_on_frontend1" v-on:update:checked="onSwitchCookieOnFrontend1"></c-switch>
												<input type="hidden" name="gcc-cookie-on-frontend1" v-model="cookie_on_frontend1">
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
										:show.sync="settings_button_popup1"
										size="lg"
										:close-on-backdrop="closeOnBackdrop"
										:centered="centered"
									>
									<div class="optout-settings-tittle-bar">
											<div class="optout-setting-tittle"><?php esc_attr_e( 'Settings Button', 'gdpr-cookie-consent' ); ?></div>
											<img @click="settings_button_popup1=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
											<c-input name="button_settings_text_field1" v-model="settings_text1"></c-input>
										</c-col>
										<c-col class="col-sm-6  gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="settings_text_color1"></c-input>
											<c-input class="gdpr-color-select" id="gdpr-cookie-settings-text-color1" type="color" name="gdpr-cookie-settings-text-color1" v-model="settings_text_color1"></c-input>
										</c-col>
									</c-row>
									<c-row class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-settings-as-button1" :reduce="label => label.code" :options="accept_as_button_options" v-model="settings_as_button1" @input="onButtonChange($event, 'settings1')"></v-select>
											<input type="hidden" name="gdpr-cookie-settings-as1" v-model="settings_as_button1">
										</c-col>
									</c-row>
									<c-row v-show="settings_as_button1" class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row v-show="settings_as_button1" class="gdpr-label-row">
										<c-col class="col-sm-6 gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="settings_background_color1"></c-input>
											<c-input class="gdpr-color-select" id="gdpr-cookie-settings-background-color1" type="color" name="gdpr-cookie-settings-background-color1" v-model="settings_background_color1"></c-input>
										</c-col>
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-settings-size1" :reduce="label => label.code" :options="accept_size_options" v-model="settings_size1">
											</v-select>
											<input type="hidden" name="gdpr-cookie-settings-size1" v-model="settings_size1">
										</c-col>
									</c-row>
									<c-row  v-show="settings_as_button1" class="gdpr-label-row">
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
										<c-col class="col-sm-6">
											<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
										</c-col>
									</c-row>
									<c-row v-show="settings_as_button1">
										<c-col class="col-sm-6">
											<v-select class="form-group" id="gdpr-cookie-settings-border-style1" :reduce="label => label.code" :options="border_style_options" v-model="settings_style1">
											</v-select>
											<input type="hidden" name="gdpr-cookie-settings-border-style1" v-model="settings_style1">
										</c-col>
										<c-col class="col-sm-6 gdpr-color-pick">
											<c-input class="gdpr-color-input" type="text" v-model="settings_border_color"></c-input>
											<c-input class="gdpr-color-select" id="gdpr-cookie-settings-border-color1" type="color" name="gdpr-cookie-settings-border-color1" v-model="settings_border_color1"></c-input>
										</c-col>
									</c-row>
									<c-row v-show="settings_as_button1" class="gdpr-label-row">
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
									<c-row v-show="settings_as_button1">
										<c-col class="col-sm-4 gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="settings_opacity1"></c-input>
											<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-settings-opacity1" v-model="settings_opacity1"></c-input>
										</c-col>
										<c-col class="col-sm-4 gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="settings_border_width1"></c-input>
											<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-settings-border-width1" v-model="settings_border_width1"></c-input>
										</c-col>
										<c-col class="col-sm-4 gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="settings_border_radius1"></c-input>
											<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-settings-border-radius1" v-model="settings_border_radius1"></c-input>
										</c-col>
									</c-row>

												<button class="done-button-settings" @click="settings_button_popup1=false"><span>Done</span></button>

									</c-modal>
									</div>
								</c-card-body>
							</c-card>


						</c-card-body>
						<c-card-body v-show="active_default_multiple_legislation === 'ccpa'">
						<c-card-body >

								<!-- NEWLY ADDED -->
							
									<c-row v-show="is_ccpa">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'CCPA Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as CCPA notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea name="notify_message_ccpa_field" v-model="ccpa_message"></c-textarea>
										</c-col>
									</c-row>
									<c-row v-show="is_ccpa">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'CCPA Opt-out Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as CCPA notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea name="notify_message_ccpa_optout_field" v-model="ccpa_optout_message"></c-textarea>
										</c-col>
									</c-row>

									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cookie Bar Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="multiple_legislation_cookie_bar_color2"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-multiple-legislation-cookie-bar-color2" type="color" name="gdpr-multiple-legislation-cookie-bar-color2" v-model="multiple_legislation_cookie_bar_color2"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( ' Cookie Bar Opacity', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="multiple_legislation_cookie_bar_opacity2"></c-input>
										<c-input class="gdpr-slider-input opacity-slider" type="number"  min="0" max="1" step="0.01" name="gdpr-multiple-legislation-cookie-bar-opacity2" v-model="multiple_legislation_cookie_bar_opacity2"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="multiple_legislation_cookie_text_color2"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-multiple-legislation-cookie-text-color2" type="color" name="gdpr-multiple-legislation-cookie-text-color2" v-model="multiple_legislation_cookie_text_color2"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Styles', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<v-select class="form-group" id="gdpr-multiple-legislation-cookie-border-style2" :reduce="label => label.code" :options="border_style_options" v-model="multiple_legislation_border_style2">
											</v-select>
											<input type="hidden" name="gdpr-multiple-legislation-cookie-border-style2" v-model="multiple_legislation_border_style2">
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="multiple_legislation_cookie_bar_border_width2"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-multiple-legislation-cookie-bar-border-width2" v-model="multiple_legislation_cookie_bar_border_width2"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="multiple_legislation_cookie_border_color2"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-multiple-legislation-cookie-border-color2" type="color" name="gdpr-multiple-legislation-cookie-border-color2" v-model="multiple_legislation_cookie_border_color2"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="multiple_legislation_cookie_bar_border_radius2"></c-input>
										<c-input class="gdpr-slider-input" type="number" name="gdpr-multiple-legislation-cookie-bar-border-radius2" v-model="multiple_legislation_cookie_bar_border_radius2"></c-input>
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
													<v-select disabled class="form-group" id="gdpr-cookie-font" :reduce="label => label.code" :options="font_options" v-model="multiple_legislation_cookie_font2">
													</v-select>
													<input type="hidden" name="gdpr-multiple-legislation-cookie-font2" v-model="multiple_legislation_cookie_font2">
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
													<v-select class="form-group" id="gdpr-cookie-font" :reduce="label => label.code" :options="font_options" v-model="multiple_legislation_cookie_font2">
													</v-select>
													<input type="hidden" name="gdpr-multiple-legislation-cookie-font2" v-model="multiple_legislation_cookie_font2	">
												</c-col>
											</c-row>
										<?php } ?>
										<c-row>
											<c-col class="col-sm-4">
												<label><?php esc_attr_e( 'Upload Logo ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'To preview the logo, simply upload a logo and then click the "Save Changes" button ', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
											</c-col>
											<c-col class="col-sm-8 ">
											<span><?php echo esc_attr_e("The same logo will be used for both laws.");  ?></span>
											</c-col>
										</c-row>

									<c-card  v-show="is_ccpa">
								<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Confirm Button', 'gdpr-cookie-consent' ); ?></c-card-header>
								<c-card-body>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Confirm Button Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<c-button class="gdpr-configure-button" @click="confirm_button_popup1=true">
												<span>
													<img class="gdpr-configure-image" :src="configure_image_url.default">
													<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
												</span>
											</c-button>
										</c-col>
									</c-row>
									<div class="opt-out-link-container">
									<c-modal
										title="Confirm Button"
										:show.sync="confirm_button_popup1"
										size="lg"
										:close-on-backdrop="closeOnBackdrop"
										:centered="centered"
									>
									<div class="optout-settings-tittle-bar">
											<div class="optout-setting-tittle"><?php esc_attr_e( 'Confirm Button', 'gdpr-cookie-consent' ); ?></div>
											<img @click="confirm_button_popup1=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
												<c-input name="button_confirm_text_field1" v-model="confirm_text1"></c-input>
											</c-col>
											<c-col class="col-sm-6 gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="confirm_text_color1"></c-input>
												<c-input class="gdpr-color-select" id="gdpr-cookie-confirm-text-color1" type="color" name="gdpr-cookie-confirm-text-color1" v-model="confirm_text_color1"></c-input>
											</c-col>
										</c-row>
										<c-row class="gdpr-label-row">
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
										</c-row>
										<c-row>
											<c-col class="col-sm-6 gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="confirm_background_color1"></c-input>
												<c-input class="gdpr-color-select" id="gdpr-cookie-confirm-background-color1" type="color" name="gdpr-cookie-confirm-background-color1" v-model="confirm_background_color1"></c-input>
											</c-col>
											<c-col class="col-sm-6">
												<v-select class="form-group" id="gdpr-cookie-confirm-size1" :reduce="label => label.code" :options="accept_size_options" v-model="confirm_size1">
												</v-select>
												<input type="hidden" name="gdpr-cookie-confirm-size1" v-model="confirm_size1">
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
												<v-select class="form-group" id="gdpr-cookie-confirm-border-style1" :reduce="label => label.code" :options="border_style_options" v-model="confirm_style1">
												</v-select>
												<input type="hidden" name="gdpr-cookie-confirm-border-style1" v-model="confirm_style1">
											</c-col>
											<c-col class="col-sm-6 gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="confirm_border_color1"></c-input>
												<c-input class="gdpr-color-select" id="gdpr-cookie-confirm-border-color1" type="color" name="gdpr-cookie-confirm-border-color1" v-model="confirm_border_color1"></c-input>
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
												<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="confirm_opacity1"></c-input>
												<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-confirm-opacity1" v-model="confirm_opacity1"></c-input>
											</c-col>
											<c-col class="col-sm-4 gdpr-color-pick">
												<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="confirm_border_width1"></c-input>
												<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-confirm-border-width1" v-model="confirm_border_width1"></c-input>
											</c-col>
											<c-col class="col-sm-4 gdpr-color-pick">
												<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="confirm_border_radius1"></c-input>
												<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-confirm-border-radius1" v-model="confirm_border_radius1"></c-input>
											</c-col>
										</c-row>
												<button class="done-button-settings" @click="confirm_button_popup1=false"><span>Done</span></button>
											
									</c-modal>
									</div>
								</c-card-body>
							</c-card>
							<c-card v-show="is_ccpa">
								<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Cancel Button', 'gdpr-cookie-consent' ); ?></c-card-header>
								<c-card-body>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cancel Button Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<c-button class="gdpr-configure-button" @click="cancel_button_popup1=true">
												<span>
													<img class="gdpr-configure-image" :src="configure_image_url.default">
													<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
												</span>
											</c-button>
										</c-col>
									</c-row>
									<div class="opt-out-link-container">
									<c-modal
										title="Cancel Button"
										:show.sync="cancel_button_popup1"
										size="lg"
										:close-on-backdrop="closeOnBackdrop"
										:centered="centered"
									>
									<div class="optout-settings-tittle-bar">
											<div class="optout-setting-tittle"><?php esc_attr_e( 'Cancel Button', 'gdpr-cookie-consent' ); ?></div>
											<img @click="cancel_button_popup1=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
												<c-input name="button_cancel_text_field1" v-model="cancel_text1"></c-input>
											</c-col>
											<c-col class="col-sm-6 gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="cancel_text_color1"></c-input>
												<c-input class="gdpr-color-select" id="gdpr-cookie-cancel-text-color1" type="color" name="gdpr-cookie-cancel-text-color1" v-model="cancel_text_color1"></c-input>
											</c-col>
										</c-row>
										<c-row class="gdpr-label-row">
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
											<c-col class="col-sm-6">
												<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
											</c-col>
										</c-row>
										<c-row>
											<c-col class="col-sm-6 gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="cancel_background_color1"></c-input>
												<c-input class="gdpr-color-select" id="gdpr-cookie-cancel-background-color1" type="color" name="gdpr-cookie-cancel-background-color1" v-model="cancel_background_color1"></c-input>
											</c-col>
											<c-col class="col-sm-6">
												<v-select class="form-group" id="gdpr-cookie-cancel-size1" :reduce="label => label.code" :options="accept_size_options" v-model="cancel_size1">
												</v-select>
												<input type="hidden" name="gdpr-cookie-cancel-size1" v-model="cancel_size1">
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
												<v-select class="form-group" id="gdpr-cookie-cancel-border-style1" :reduce="label => label.code" :options="border_style_options" v-model="cancel_style1">
												</v-select>
												<input type="hidden" name="gdpr-cookie-cancel-border-style1" v-model="cancel_style1">
											</c-col>
											<c-col class="col-sm-6 gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="cancel_border_color1"></c-input>
												<c-input class="gdpr-color-select" id="gdpr-cookie-cancel-border-color1" type="color" name="gdpr-cookie-cancel-border-color1" v-model="cancel_border_color1"></c-input>
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
												<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="cancel_opacity1"></c-input>
												<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1"  name="gdpr-cookie-cancel-opacity1" v-model="cancel_opacity1"></c-input>
											</c-col>
											<c-col class="col-sm-4 gdpr-color-pick">
												<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="cancel_border_width1"></c-input>
												<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-cancel-border-width1" v-model="cancel_border_width1"></c-input>
											</c-col>
											<c-col class="col-sm-4 gdpr-color-pick">
												<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="cancel_border_radius1"></c-input>
												<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-cancel-border-radius1" v-model="cancel_border_radius1"></c-input>
											</c-col>
										</c-row>
												<button class="done-button-settings" @click="cancel_button_popup1=false"><span>Done</span></button>
											
									</c-modal>
									</div>
								</c-card-body>
							</c-card>
							<c-card  v-show="is_ccpa">
								<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Opt-out Link', 'gdpr-cookie-consent' ); ?></c-card-header>
								<c-card-body>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Opt-out Link Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<c-button class="gdpr-configure-button" @click="opt_out_link_popup1=true">
												<span>
													<img class="gdpr-configure-image" :src="configure_image_url.default">
													<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
												</span>
											</c-button>
										</c-col>
									</c-row>
									<div class="opt-out-link-container">
									<c-modal
										title="Opt-out Link"
										:show.sync="opt_out_link_popup1"
										size="lg"
										:close-on-backdrop="closeOnBackdrop"
										:centered="centered"
									>
									<div class="optout-settings-tittle-bar">
											<div class="optout-setting-tittle"><?php esc_attr_e( 'Opt Out Link', 'gdpr-cookie-consent' ); ?></div>
											<img @click="opt_out_link_popup1=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
												<c-input name="button_donotsell_text_field1" v-model="opt_out_text1"></c-input>
											</c-col>
											<c-col class="col-sm-6 gdpr-color-pick">
												<c-input class="gdpr-color-input" type="text" v-model="opt_out_text_color1"></c-input>
												<c-input class="gdpr-color-select" id="gdpr-cookie-opt-out-text-color1" type="color" name="gdpr-cookie-opt-out-text-color1" v-model="opt_out_text_color1"></c-input>
											</c-col>
										</c-row>
												<button class="done-button-settings" @click="opt_out_link_popup1=false"><span>Done</span></button>

									</c-modal>
									</div>
								</c-card-body>
							</c-card>
								</c-card-body>
								
										
						</c-card-body>	
							
					</c-card>
					
						<!-- Desgin Banner preview if A/B Testing is enabled  and GDPR&CCPA both are not selected-->
				<c-card  v-show="ab_testing_enabled  && gdpr_policy != 'both'"class=" desgin_card">
					<div class="gdpr-cookie-consent-banner-tabs">
						<c-button class="gdpr-cookie-consent-banner-tab"@click="changeActiveTestBannerTabTo1":class="{ 'gdpr-cookie-consent-banner-tab-active': active_test_banner_tab === 1 }"><?php esc_html_e( isset( $the_options['cookie_bar1_name'] ) ? $the_options['cookie_bar1_name'] : 'Test Banner A', 'gdpr-cookie-consent' ); ?><span v-show="default_cookie_bar === true">(default)</span></c-button>
						<c-button class="gdpr-cookie-consent-banner-tab"@click="changeActiveTestBannerTabTo2":class="{ 'gdpr-cookie-consent-banner-tab-active': active_test_banner_tab === 2 }"><?php esc_html_e( isset( $the_options['cookie_bar2_name'] ) ? $the_options['cookie_bar2_name'] : 'Test Banner B', 'gdpr-cookie-consent' ); ?><span v-show="default_cookie_bar === false">(default)</span></c-button>
					</div>
					<c-card-body v-show="active_test_banner_tab === 1">
							<c-card-body >

													<!-- NEWLY ADDED -->
								
						<c-row v-show="is_gdpr">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Message Heading', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Leave it blank, If you do not need a heading.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="bar_heading_text_field" v-model="gdpr_message_heading"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="is_eprivacy">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'ePrivacy Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as ePrivacy notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_eprivacy_field" v-model="eprivacy_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="is_gdpr">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'GDPR Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_field" v-model="gdpr_message" :readonly="iabtcf_is_on"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="is_ccpa">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'CCPA Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as CCPA notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_ccpa_field" v-model="ccpa_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="is_ccpa">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'CCPA Opt-out Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as CCPA notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_ccpa_optout_field" v-model="ccpa_optout_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="is_gdpr">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'About Cookies Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Text shown under "About Cookies" section when users click on "Cookie Settings" button.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea :rows="6" name="about_message_field" v-model="gdpr_about_cookie_message" :readonly="iabtcf_is_on"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="is_lgpd">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'Message Heading', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Leave it blank, If you do not need a heading.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="bar_heading_text_lgpd_field" v-model="lgpd_message_heading"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="is_lgpd">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'LGPD Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea name="notify_message_lgpd_field" v-model="lgpd_message"></c-textarea>
							</c-col>
						</c-row>
						<c-row v-show="is_lgpd">
							<c-col class="col-sm-4"><label><?php esc_attr_e( 'About Cookies Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Text shown under "About Cookies" section when users click on "Cookie Settings" button.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
							<c-col class="col-sm-8">
								<c-textarea :rows="6" name="about_message_lgpd_field" v-model="lgpd_about_cookie_message"></c-textarea>
							</c-col>
						</c-row>

								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Consent Banner Title', 'gdpr-cookie-consent' ); ?> </label></c-col>
									<c-col class="col-sm-8">
										<c-input name="gdpr-cookie_bar1_name" v-model="cookie_bar1_name"></c-input>
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
									<c-input class="gdpr-color-input" type="text" v-model="cookie_bar_color1"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-bar-color1" type="color" name="gdpr-cookie-bar-color1" v-model="cookie_bar_color1"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( ' Cookie Bar Opacity', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="cookie_bar_opacity1"></c-input>
									<c-input class="gdpr-slider-input opacity-slider" type="number"  min="0" max="1" step="0.01" name="gdpr-cookie-bar-opacity1" v-model="cookie_bar_opacity1"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="cookie_text_color1"></c-input>
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
									<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="cookie_bar_border_width1"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-bar-border-width1" v-model="cookie_bar_border_width1"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="cookie_border_color1"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-border-color1" type="color" name="gdpr-cookie-border-color1" v-model="cookie_border_color1"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="cookie_bar_border_radius1"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-bar-border-radius1" v-model="cookie_bar_border_radius1"></c-input>
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
									<label><?php esc_attr_e( 'Upload Logo ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'To preview the logo, simply upload a logo and then click the "Save Changes" button ', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
								</c-col>
								<c-col class="col-sm-8 ">
								<c-button color="info" class="button" id="image-upload-button1" name="image-upload-button1" @click="openMediaModal1" style="margin: 10px;">
										<?php esc_attr_e( 'Add Image', 'gdpr-cookie-consent' ); ?>
									</c-button>
									<c-button color="info" class="button" id="image-delete-button" @click="deleteSelectedimage1" style="margin: 10px; ">
										<?php esc_attr_e( 'Remove Image', 'gdpr-cookie-consent' ); ?>
									</c-button>
									<?php
								$get_banner_img1 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1 );
									?>
									<img id="gdpr-cookie-bar-logo-holder1" name="gdpr-cookie-bar-logo-holder1" src="<?php echo esc_url_raw( $get_banner_img1 ); ?>">
									<p class="image-upload-notice" style="margin-left: 10px; font-size:14px; font-weight:14px;color:#d4d4d8;">
										<?php esc_attr_e( 'We recommend 50 x 50 pixels.', 'gdpr-cookie-consent' ); ?>
									</p>
									<c-input type="hidden" name="gdpr-cookie-bar-logo-url-holder1" id="gdpr-cookie-bar-logo-url-holder1"  class="regular-text"> </c-input>
								</c-col>
							</c-row>
							</c-card-body>

							<c-card-body>
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
											<c-row v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on">
												<c-col class="col-sm-6">
													<v-select class="form-group" id="gcc-readmore-as-button" :reduce="label => label.code" :options="show_as_options" v-model="button_readmore_as_button"></v-select>
													<input type="hidden" name="gcc-readmore-as-button" v-model="button_readmore_as_button">
												</c-col>
												<c-col class="col-sm-6">
													<v-select class="form-group" id="gcc-readmore-url-type" :reduce="label => label.code" :options="url_type_options" v-model="button_readmore_url_type"></v-select>
													<input type="hidden" name="gcc-readmore-url-type" v-model="button_readmore_url_type">
												</c-col>
											</c-row>

											<div v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on">
												<c-row v-show="button_readmore_as_button" class="gdpr-label-row">
													<c-col class="col-sm-6"><label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label></c-col>
													<c-col class="col-sm-6"><label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label></c-col>	
												</c-row>
												<c-row v-show="button_readmore_as_button">
													<c-col class="col-sm-6 gdpr-color-pick" >
														<c-input class="gdpr-color-input" type="text" v-model="button_readmore_button_color"></c-input>
														<c-input class="gdpr-color-select" id="gdpr-readmore-button-color" type="color" name="gcc-readmore-button-color" v-model="button_readmore_button_color"></c-input>
													</c-col>
													<c-col class="col-sm-6">
														<v-select class="form-group" id="gcc-readmore-button-size" :reduce="label => label.code" :options="button_size_options" v-model="button_readmore_button_size"></v-select>
														<input type="hidden" name="gcc-readmore-button-size" v-model="button_readmore_button_size">
													</c-col>
												</c-row>
												<c-row v-show="button_readmore_as_button" class="gdpr-label-row">
													<c-col class="col-sm-6"><label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label></c-col>
													<c-col class="col-sm-6"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label></c-col>
												</c-row>
												<c-row v-show="button_readmore_as_button">
													<c-col class="col-sm-6">
														<v-select class="form-group" id="gcc-readmore-button-border-style" :reduce="label => label.code" :options="border_style_options" v-model="button_readmore_button_border_style"></v-select>
														<input type="hidden" name="gcc-readmore-button-border-style" v-model="button_readmore_button_border_style">
													</c-col>
													<c-col class="col-sm-6 gdpr-color-pick" >
														<c-input class="gdpr-color-input" type="text" v-model="button_readmore_button_border_color"></c-input>
														<c-input class="gdpr-color-select" id="gdpr-readmore-button-border-color" type="color" name="gcc-readmore-button-border-color" v-model="button_readmore_button_border_color"></c-input>
													</c-col>
												</c-row>
												<c-row class="gdpr-label-row">
													<c-col class="col-sm-6" v-show="button_readmore_url_type"><label><?php esc_attr_e( 'Page', 'gdpr-cookie-consent' ); ?></label></c-col>
													<c-col v-show="!button_readmore_url_type" class="col-sm-6"><label><?php esc_attr_e( 'URL', 'gdpr-cookie-consent' ); ?></label></c-col>
													<c-col class="col-sm-3 gdpr-readmore-toggle-row" v-show="button_readmore_url_type"><label><?php esc_attr_e( 'Sync with WordPress Policy Page', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'If enabled visitor will be redirected to Privacy Policy Page set in WordPress settings irrespective of Page set in the previous setting.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
													<c-col class="col-sm-3 gdpr-readmore-toggle-row" v-show="button_readmore_url_type">
														<c-switch v-bind="labelIcon" v-model="button_readmore_wp_page" id="gdpr-cookie-consent-readmore-wp-page" variant="3d"  color="success" :checked="button_readmore_wp_page" v-on:update:checked="onSwitchButtonReadMoreWpPage"></c-switch>
														<input type="hidden" name="gcc-readmore-wp-page" v-model="button_readmore_wp_page">
													</c-col>
												</c-row>
												<c-row>
													<c-col v-show="button_readmore_url_type" class="col-sm-6">
														<v-select class="form-group"  placeholder="Select Policy Page" id="gcc-readmore-page" :reduce="label => label.code" :options="privacy_policy_options" v-model="readmore_page" @input="onSelectPrivacyPage"></v-select>
														<input type="hidden" name="gcc-readmore-page" v-model="button_readmore_page">
													</c-col>
													<c-col class="col-sm-6" v-show="!button_readmore_url_type">
														<c-input name="gcc-readmore-url" v-model="button_readmore_url"></c-input>
													</c-col>
													<c-col class="col-sm-3 gdpr-readmore-toggle-row"><label><?php esc_attr_e( 'Open URL in New Window?', 'gdpr-cookie-consent' ); ?></label></c-col>
													<c-col class="col-sm-3 gdpr-readmore-toggle-row">
														<c-switch v-bind="labelIcon" v-model="button_readmore_new_win" id="gdpr-cookie-consent-readmore-new-win" variant="3d"  color="success" :checked="button_readmore_new_win" v-on:update:checked="onSwitchButtonReadMoreNewWin"></c-switch>
														<input type="hidden" name="gcc-readmore-new-win" v-model="button_readmore_new_win">
													</c-col>
												</c-row>
												<c-row v-show="button_readmore_as_button" class="gdpr-label-row">
													<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label></c-col>
													<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label></c-col>
													<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label></c-col>
												</c-row>
												<c-row v-show="button_readmore_as_button">
													<c-col class="col-sm-4 gdpr-color-pick">
														<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="button_readmore_button_opacity"></c-input>
														<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-opacity" v-model="button_readmore_button_opacity"></c-input>
													</c-col>
													<c-col class="col-sm-4 gdpr-color-pick">
														<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="button_readmore_button_border_width"></c-input>
														<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-border-width" v-model="button_readmore_button_border_width"></c-input>
													</c-col>
													<c-col class="col-sm-4 gdpr-color-pick">
														<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="button_readmore_button_border_radius"></c-input>
														<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-border-radius" v-model="button_readmore_button_border_radius"></c-input>
													</c-col>
												</c-row>	
											</div>

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
									<c-switch v-bind="labelIcon" v-model="cookie_accept_on1" id="gdpr-cookie-consent-cookie1" variant="3d"  color="success" :checked="cookie_accept_on1" v-on:update:checked="onSwitchCookieAcceptEnable1"></c-switch>
									<input type="hidden" name="gcc-cookie-accept-enable1" v-model="cookie_accept_on1">
								</c-col>
								<c-col class="col-sm-3">
									<c-button :disabled="!cookie_accept_on1" class="gdpr-configure-button" @click="accept_button_popup1=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
							</c-row>
							<div class="opt-out-link-container">
							<c-modal
								title="Accept Button"
								:show.sync="accept_button_popup1"
								size="lg"
								:close-on-backdrop="closeOnBackdrop"
								:centered="centered"
							>
							<div class="optout-settings-tittle-bar">
									<div class="optout-setting-tittle"><?php esc_attr_e( 'Accept Button', 'gdpr-cookie-consent' ); ?></div>
									<img @click="accept_button_popup1=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
									<c-input name="button_accept_text_field1" v-model="accept_text1"></c-input>
								</c-col>
								<c-col class="col-sm-6 gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="accept_text_color1"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-text-color1" type="color" name="gdpr-cookie-accept-text-color1" v-model="accept_text_color1"></c-input>
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
									<v-select class="form-group" id="gdpr-cookie-accept-as-button1" :reduce="label => label.code" :options="accept_as_button_options" v-model="accept_as_button1" @input="onButtonChange($event, 'accept1')"></v-select>
									<input type="hidden" name="gdpr-cookie-accept-as1" v-model="accept_as_button1">
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-accept-action1" :reduce="label => label.code" :options="accept_action_options" v-model="accept_action1" 	>
									</v-select>
									<input type="hidden" name="gdpr-cookie-accept-action1" v-model="accept_action1">
								</c-col>
							</c-row>
							<c-row v-show="accept_action1!='#cookie_action_close_header'"  class="gdpr-label-row">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row v-show="accept_action1!='#cookie_action_close_header'">
								<c-col class="col-sm-6">
									<c-input name="gdpr-cookie-accept-url1" v-model="accept_url1"></c-input>
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-url-new-window1" :reduce="label => label.code" :options="open_url_options" v-model="open_url1"></v-select>
									<input type="hidden" name="gdpr-cookie-url-new-window1" v-model="open_url1">
								</c-col>
							</c-row>
							<c-row class="gdpr-label-row"  v-show="accept_as_button1">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row  v-show="accept_as_button1">
								<c-col class="col-sm-6  gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="accept_background_color1"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-background-color1" type="color" name="gdpr-cookie-accept-background-color1" v-model="accept_background_color1"></c-input>
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-accept-size1" :reduce="label => label.code" :options="accept_size_options" v-model="accept_size1">
									</v-select>
									<input type="hidden" name="gdpr-cookie-accept-size1" v-model="accept_size1">
								</c-col>
							</c-row>
							<c-row  v-show="accept_as_button1" class="gdpr-label-row">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row v-show="accept_as_button1">
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-accept-border-style1" :reduce="label => label.code" :options="border_style_options" v-model="accept_style1">
									</v-select>
									<input type="hidden" name="gdpr-cookie-accept-border-style1" v-model="accept_style1">
								</c-col>
								<c-col class="col-sm-6 gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="accept_border_color1"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-border-color" type="color" name="gdpr-cookie-accept-border-color1" v-model="accept_border_color1"></c-input>
								</c-col>
							</c-row>
							<c-row v-show="accept_as_button1" class="gdpr-label-row">
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
							<c-row v-show="accept_as_button1">
								<c-col class="col-sm-4  gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="accept_opacity1"></c-input>
									<c-input class="gdpr-slider-input opacity-slider" type="number"  min="0" max="1" step="0.1"  name="gdpr-cookie-accept-opacity1" v-model="accept_opacity1"></c-input>
								</c-col>
								<c-col class="col-sm-4 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="accept_border_width1"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-border-width1" v-model="accept_border_width1"></c-input>
								</c-col>
								<c-col class="col-sm-4  gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="accept_border_radius1"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-border-radius1" v-model="accept_border_radius1"></c-input>
								</c-col>
							</c-row>
							
									<button  class="done-button-settings" @click="accept_button_popup1=false"><span>Done</span></button>
								
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
									<c-switch v-bind="labelIcon" v-model="cookie_accept_all_on1" id="gdpr-cookie-consent-cookie-acceptall-on1" variant="3d"  color="success" :checked="cookie_accept_all_on1" v-on:update:checked="onSwitchCookieAcceptAllEnable1"></c-switch>
									<input type="hidden" name="gcc-cookie-accept-all-enable1" v-model="cookie_accept_all_on1">
								</c-col>
								<c-col class="col-sm-3">
									<c-button :disabled="!cookie_accept_all_on1" class="gdpr-configure-button" @click="accept_all_button_popup1=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
							</c-row>
							<div class="opt-out-link-container">
							<c-modal
								title="Accept All Button"
								:show.sync="accept_all_button_popup1"
								size="lg"
								:close-on-backdrop="closeOnBackdrop"
								:centered="centered"
							>
							<div class="optout-settings-tittle-bar">
									<div class="optout-setting-tittle"><?php esc_attr_e( 'Accept All Button', 'gdpr-cookie-consent' ); ?></div>
									<img @click="accept_all_button_popup1=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
									<c-input name="button_accept_all_text_field1" v-model="accept_all_text1"></c-input>
								</c-col>
								<c-col class="col-sm-6 gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="accept_all_text_color1"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-all-text-color1" type="color" name="gdpr-cookie-accept-all-text-color1" v-model="accept_all_text_color1"></c-input>
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
									<v-select class="form-group" id="gdpr-cookie-accept-all-as-button1" :reduce="label => label.code" :options="accept_as_button_options" v-model="accept_all_as_button1" @input="onButtonChange($event, 'accept_all1')"></v-select>
									<input type="hidden" name="gdpr-cookie-accept-all-as1" v-model="accept_all_as_button1">
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-accept-all-action1" :reduce="label => label.code" :options="accept_action_options" v-model="accept_all_action1" >
									</v-select>
									<input type="hidden" name="gdpr-cookie-accept-all-action1" v-model="accept_all_action1">
								</c-col>
							</c-row>
							<c-row v-show="accept_all_action1!='#cookie_action_close_header'"  class="gdpr-label-row">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row v-show="accept_all_action1!='#cookie_action_close_header'">
								<c-col class="col-sm-6">
									<c-input name="gdpr-cookie-accept-all-url1" v-model="accept_all_url1"></c-input>
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-accept-all-new-window1" :reduce="label => label.code" :options="open_url_options" v-model="accept_all_new_win1"></v-select>
									<input type="hidden" name="gdpr-cookie-accept-all-new-window1" v-model="accept_all_new_win1">
								</c-col>
							</c-row>
							<c-row class="gdpr-label-row"  v-show="accept_all_as_button1">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row  v-show="accept_all_as_button1">
								<c-col class="col-sm-6  gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="accept_all_background_color1"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-all-background-color1" type="color" name="gdpr-cookie-accept-all-background-color1" v-model="accept_all_background_color1"></c-input>
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-accept-all-size1" :reduce="label => label.code" :options="accept_size_options" v-model="accept_all_size1">
									</v-select>
									<input type="hidden" name="gdpr-cookie-accept-all-size1" v-model="accept_all_size1">
								</c-col>
							</c-row>
							<c-row  v-show="accept_all_as_button1" class="gdpr-label-row">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row v-show="accept_all_as_button1">
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-accept-all-border-style1" :reduce="label => label.code" :options="border_style_options" v-model="accept_all_style1">
									</v-select>
									<input type="hidden" name="gdpr-cookie-accept-all-border-style1" v-model="accept_all_style1">
								</c-col>
								<c-col class="col-sm-6 gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="accept_all_border_color1"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-all-border-color1" type="color" name="gdpr-cookie-accept-all-border-color1" v-model="accept_all_border_color1"></c-input>
								</c-col>
							</c-row>
							<c-row v-show="accept_all_as_button1" class="gdpr-label-row">
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
							<c-row v-show="accept_all_as_button1">
								<c-col class="col-sm-4  gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="accept_all_opacity1"></c-input>
									<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-accept-all-opacity1" v-model="accept_all_opacity1"></c-input>
								</c-col>
								<c-col class="col-sm-4 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="accept_all_border_width1"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-all-border-width1" v-model="accept_all_border_width1"></c-input>
								</c-col>
								<c-col class="col-sm-4  gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="accept_all_border_radius1"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-all-border-radius1" v-model="accept_all_border_radius1"></c-input>
								</c-col>
							</c-row>
									<button class="done-button-settings" @click="accept_all_button_popup1=false"><span>Done</span></button>
								
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
									<c-switch v-bind="labelIcon" v-model="cookie_decline_on1" id="gdpr-cookie-consent-decline-on1" variant="3d"  color="success" :checked="cookie_decline_on1" v-on:update:checked="onSwitchCookieDeclineEnable1"></c-switch>
									<input type="hidden" name="gcc-cookie-decline-enable1" v-model="cookie_decline_on1">
								</c-col>
								<c-col class="col-sm-3">
									<c-button :disabled="!cookie_decline_on1" class="gdpr-configure-button" @click="decline_button_popup1=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
							</c-row>
							<div class="opt-out-link-container">
							<c-modal
								title="Decline Button"
								:show.sync="decline_button_popup1"
								size="lg"
								:close-on-backdrop="closeOnBackdrop"
								:centered="centered"
							>
							<div class="optout-settings-tittle-bar">
									<div class="optout-setting-tittle"><?php esc_attr_e( 'Decline Button', 'gdpr-cookie-consent' ); ?></div>
									<img @click="decline_button_popup1=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
										<c-input name="button_decline_text_field1" v-model="decline_text1"></c-input>
									</c-col>
									<c-col class="col-sm-6  gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="decline_text_color1"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-decline-text-color1" type="color" name="gdpr-cookie-decline-text-color1" v-model="decline_text_color1"></c-input>
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
										<v-select class="form-group" id="gdpr-cookie-decline-as-button1" :reduce="label => label.code" :options="accept_as_button_options" v-model="decline_as_button1" @input="onButtonChange($event, 'decline1')"></v-select>
										<input type="hidden" name="gdpr-cookie-decline-as1" v-model="decline_as_button1">
									</c-col>
									<c-col class="col-sm-6"><v-select class="form-group" id="gdpr-cookie-decline-action1" :reduce="label => label.code" :options="decline_action_options" v-model="decline_action1">
										</v-select>
										<input type="hidden" name="gdpr-cookie-decline-action1" v-model="decline_action1">
									</c-col>
								</c-row>
								<c-row v-show="decline_action1!='#cookie_action_close_header_reject'" class="gdpr-label-row">
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
								</c-row>
								<c-row v-show="decline_action1!='#cookie_action_close_header_reject'">
									<c-col class="col-sm-6">
										<c-input name="gdpr-cookie-decline-url1" v-model="decline_url1"></c-input>
									</c-col>
									<c-col class="col-sm-6">
										<v-select class="form-group" id="gdpr-cookie-decline-url-new-window1" :reduce="label => label.code" :options="open_url_options" v-model="open_decline_url1"></v-select>
										<input type="hidden" name="gdpr-cookie-decline-url-new-window1" v-model="open_decline_url1">
									</c-col>
								</c-row>
								<c-row v-show="decline_as_button1" class="gdpr-label-row">
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
								</c-row>
								<c-row v-show="decline_as_button1">
									<c-col class="col-sm-6  gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="decline_background_color1"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-decline-background-color1" type="color" name="gdpr-cookie-decline-background-color1" v-model="decline_background_color1"></c-input>
									</c-col>
									<c-col class="col-sm-6">
										<v-select class="form-group" id="gdpr-cookie-decline-size1" :reduce="label => label.code" :options="accept_size_options" v-model="decline_size1">
										</v-select>
										<input type="hidden" name="gdpr-cookie-decline-size1" v-model="decline_size1">
									</c-col>
								</c-row>
								<c-row v-show="decline_as_button1" class="gdpr-label-row">
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
								</c-row>
								<c-row v-show="decline_as_button1">
									<c-col class="col-sm-6">
										<v-select class="form-group" id="gdpr-cookie-decline-border-style1" :reduce="label => label.code" :options="border_style_options" v-model="decline_style1">
										</v-select>
										<input type="hidden" name="gdpr-cookie-decline-border-style1" v-model="decline_style1">
									</c-col>
									<c-col class="col-sm-6  gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="decline_border_color1"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-decline-border-color1" type="color" name="gdpr-cookie-decline-border-color1" v-model="decline_border_color1"></c-input>
									</c-col>
								</c-row>
								<c-row  v-show="decline_as_button1" class="gdpr-label-row">
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
								<c-row v-show="decline_as_button1">
									<c-col class="col-sm-4 gdpr-color-pick"><c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="decline_opacity1"></c-input>
										<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-decline-opacity1" v-model="decline_opacity1"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick"><c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="decline_border_width1"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-decline-border-width1" v-model="decline_border_width1"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="decline_border_radius1"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-decline-border-radius1" v-model="decline_border_radius1"></c-input>
									</c-col>
								</c-row> 
										<button class="done-button-settings" @click="decline_button_popup1=false"><span>Done</span></button>
								
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
									<c-switch v-bind="labelIcon" v-model="cookie_settings_on1" id="gdpr-cookie-consent-settings-on1" variant="3d"  color="success" :checked="cookie_settings_on1" v-on:update:checked="onSwitchCookieSettingsEnable1"></c-switch>
									<input type="hidden" name="gcc-cookie-settings-enable1" v-model="cookie_settings_on1">
								</c-col>
								<c-col class="col-sm-3">
									<c-button :disabled="!cookie_settings_on1" class="gdpr-configure-button" @click="settings_button_popup1=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
								<c-col class="col-sm-4">
										<label><?php esc_attr_e( 'Display Cookies List on Frontend', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-4">
										<c-switch v-bind="labelIcon" v-model="cookie_on_frontend1" id="gdpr-cookie-consent-cookie-on-frontend1" variant="3d"  color="success" :checked="cookie_on_frontend1" v-on:update:checked="onSwitchCookieOnFrontend1"></c-switch>
										<input type="hidden" name="gcc-cookie-on-frontend1" v-model="cookie_on_frontend1">
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
								:show.sync="settings_button_popup1"
								size="lg"
								:close-on-backdrop="closeOnBackdrop"
								:centered="centered"
							>
							<div class="optout-settings-tittle-bar">
									<div class="optout-setting-tittle"><?php esc_attr_e( 'Settings Button', 'gdpr-cookie-consent' ); ?></div>
									<img @click="settings_button_popup1=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
									<c-input name="button_settings_text_field1" v-model="settings_text1"></c-input>
								</c-col>
								<c-col class="col-sm-6  gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="settings_text_color1"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-settings-text-color1" type="color" name="gdpr-cookie-settings-text-color1" v-model="settings_text_color1"></c-input>
								</c-col>
							</c-row>
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-settings-as-button1" :reduce="label => label.code" :options="accept_as_button_options" v-model="settings_as_button1" @input="onButtonChange($event, 'settings1')"></v-select>
									<input type="hidden" name="gdpr-cookie-settings-as1" v-model="settings_as_button1">
								</c-col>
							</c-row>
							<c-row v-show="settings_as_button1" class="gdpr-label-row">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row v-show="settings_as_button1" class="gdpr-label-row">
								<c-col class="col-sm-6 gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="settings_background_color1"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-settings-background-color1" type="color" name="gdpr-cookie-settings-background-color1" v-model="settings_background_color1"></c-input>
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-settings-size1" :reduce="label => label.code" :options="accept_size_options" v-model="settings_size1">
									</v-select>
									<input type="hidden" name="gdpr-cookie-settings-size1" v-model="settings_size1">
								</c-col>
							</c-row>
							<c-row  v-show="settings_as_button1" class="gdpr-label-row">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row v-show="settings_as_button1">
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-settings-border-style1" :reduce="label => label.code" :options="border_style_options" v-model="settings_style1">
									</v-select>
									<input type="hidden" name="gdpr-cookie-settings-border-style1" v-model="settings_style1">
								</c-col>
								<c-col class="col-sm-6 gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="settings_border_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-settings-border-color1" type="color" name="gdpr-cookie-settings-border-color1" v-model="settings_border_color1"></c-input>
								</c-col>
							</c-row>
							<c-row v-show="settings_as_button1" class="gdpr-label-row">
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
							<c-row v-show="settings_as_button1">
								<c-col class="col-sm-4 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="settings_opacity1"></c-input>
									<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-settings-opacity1" v-model="settings_opacity1"></c-input>
								</c-col>
								<c-col class="col-sm-4 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="settings_border_width1"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-settings-border-width1" v-model="settings_border_width1"></c-input>
								</c-col>
								<c-col class="col-sm-4 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="settings_border_radius1"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-settings-border-radius1" v-model="settings_border_radius1"></c-input>
								</c-col>
							</c-row>
							
										<button class="done-button-settings" @click="settings_button_popup1=false"><span>Done</span></button>
								
							</c-modal>
							</div>
						</c-card-body>
					</c-card>
					<c-card  v-show="is_ccpa">
						<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Confirm Button', 'gdpr-cookie-consent' ); ?></c-card-header>
						<c-card-body>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Confirm Button Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8">
									<c-button class="gdpr-configure-button" @click="confirm_button_popup1=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
							</c-row>
							<div class="opt-out-link-container">
							<c-modal
								title="Confirm Button"
								:show.sync="confirm_button_popup1"
								size="lg"
								:close-on-backdrop="closeOnBackdrop"
								:centered="centered"
							>
							<div class="optout-settings-tittle-bar">
									<div class="optout-setting-tittle"><?php esc_attr_e( 'Confirm Button', 'gdpr-cookie-consent' ); ?></div>
									<img @click="confirm_button_popup1=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
										<c-input name="button_confirm_text_field1" v-model="confirm_text1"></c-input>
									</c-col>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="confirm_text_color1"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-confirm-text-color1" type="color" name="gdpr-cookie-confirm-text-color1" v-model="confirm_text_color1"></c-input>
									</c-col>
								</c-row>
								<c-row class="gdpr-label-row">
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="confirm_background_color1"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-confirm-background-color1" type="color" name="gdpr-cookie-confirm-background-color1" v-model="confirm_background_color1"></c-input>
									</c-col>
									<c-col class="col-sm-6">
										<v-select class="form-group" id="gdpr-cookie-confirm-size1" :reduce="label => label.code" :options="accept_size_options" v-model="confirm_size1">
										</v-select>
										<input type="hidden" name="gdpr-cookie-confirm-size1" v-model="confirm_size1">
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
										<v-select class="form-group" id="gdpr-cookie-confirm-border-style1" :reduce="label => label.code" :options="border_style_options" v-model="confirm_style1">
										</v-select>
										<input type="hidden" name="gdpr-cookie-confirm-border-style1" v-model="confirm_style1">
									</c-col>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="confirm_border_color1"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-confirm-border-color1" type="color" name="gdpr-cookie-confirm-border-color1" v-model="confirm_border_color1"></c-input>
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
										<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="confirm_opacity1"></c-input>
										<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-confirm-opacity1" v-model="confirm_opacity1"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="confirm_border_width1"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-confirm-border-width1" v-model="confirm_border_width1"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="confirm_border_radius1"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-confirm-border-radius1" v-model="confirm_border_radius1"></c-input>
									</c-col>
								</c-row>
										<button class="done-button-settings" @click="confirm_button_popup1=false"><span>Done</span></button>
								
							</c-modal>
							</div>
						</c-card-body>
					</c-card>
					<c-card v-show="is_ccpa">
						<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Cancel Button', 'gdpr-cookie-consent' ); ?></c-card-header>
						<c-card-body>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cancel Button Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8">
									<c-button class="gdpr-configure-button" @click="cancel_button_popup1=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
							</c-row>
							<div class="opt-out-link-container">
							<c-modal
								title="Cancel Button"
								:show.sync="cancel_button_popup1"
								size="lg"
								:close-on-backdrop="closeOnBackdrop"
								:centered="centered"
							>
							<div class="optout-settings-tittle-bar">
									<div class="optout-setting-tittle"><?php esc_attr_e( 'Cancel Button', 'gdpr-cookie-consent' ); ?></div>
									<img @click="cancel_button_popup1=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
										<c-input name="button_cancel_text_field1" v-model="cancel_text1"></c-input>
									</c-col>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="cancel_text_color1"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-cancel-text-color1" type="color" name="gdpr-cookie-cancel-text-color1" v-model="cancel_text_color1"></c-input>
									</c-col>
								</c-row>
								<c-row class="gdpr-label-row">
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="cancel_background_color1"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-cancel-background-color1" type="color" name="gdpr-cookie-cancel-background-color1" v-model="cancel_background_color1"></c-input>
									</c-col>
									<c-col class="col-sm-6">
										<v-select class="form-group" id="gdpr-cookie-cancel-size1" :reduce="label => label.code" :options="accept_size_options" v-model="cancel_size1">
										</v-select>
										<input type="hidden" name="gdpr-cookie-cancel-size1" v-model="cancel_size1">
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
										<v-select class="form-group" id="gdpr-cookie-cancel-border-style1" :reduce="label => label.code" :options="border_style_options" v-model="cancel_style1">
										</v-select>
										<input type="hidden" name="gdpr-cookie-cancel-border-style1" v-model="cancel_style1">
									</c-col>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="cancel_border_color1"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-cancel-border-color1" type="color" name="gdpr-cookie-cancel-border-color1" v-model="cancel_border_color1"></c-input>
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
										<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="cancel_opacity1"></c-input>
										<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1"  name="gdpr-cookie-cancel-opacity1" v-model="cancel_opacity1"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="cancel_border_width1"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-cancel-border-width1" v-model="cancel_border_width1"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="cancel_border_radius1"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-cancel-border-radius1" v-model="cancel_border_radius1"></c-input>
									</c-col>
								</c-row>
										<button class="done-button-settings" @click="cancel_button_popup1=false"><span>Done</span></button>
								
							</c-modal>
							</div>
						</c-card-body>
					</c-card>
					<c-card  v-show="is_ccpa">
						<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Opt-out Link', 'gdpr-cookie-consent' ); ?></c-card-header>
						<c-card-body>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Opt-out Link Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8">
									<c-button class="gdpr-configure-button" @click="opt_out_link_popup1=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
							</c-row>
							<div class="opt-out-link-container">
							<c-modal
								title="Opt-out Link"
								:show.sync="opt_out_link_popup1"
								size="lg"
								:close-on-backdrop="closeOnBackdrop"
								:centered="centered"
							>
							<div class="optout-settings-tittle-bar">
									<div class="optout-setting-tittle"><?php esc_attr_e( 'Opt Out Link', 'gdpr-cookie-consent' ); ?></div>
									<img @click="opt_out_link_popup1=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
										<c-input name="button_donotsell_text_field1" v-model="opt_out_text1"></c-input>
									</c-col>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="opt_out_text_color1"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-opt-out-text-color1" type="color" name="gdpr-cookie-opt-out-text-color1" v-model="opt_out_text_color1"></c-input>
									</c-col>
								</c-row>
										<button class="done-button-settings" @click="opt_out_link_popup1=false"><span>Done</span></button>
								
							</c-modal>
							</div>
						</c-card-body>
					</c-card>

							</c-card-body>
						<c-card-body v-show="active_test_banner_tab === 2">
								<c-card-body >

														<!-- NEWLY ADDED -->
								
									<c-row v-show="is_gdpr">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Message Heading', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Leave it blank, If you do not need a heading.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea name="bar_heading_text_field" v-model="gdpr_message_heading"></c-textarea>
										</c-col>
									</c-row>
									<c-row v-show="is_eprivacy">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'ePrivacy Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as ePrivacy notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea name="notify_message_eprivacy_field" v-model="eprivacy_message"></c-textarea>
										</c-col>
									</c-row>
									<c-row v-show="is_gdpr">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'GDPR Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea name="notify_message_field" v-model="gdpr_message" :readonly="iabtcf_is_on"></c-textarea>
										</c-col>
									</c-row>
									<c-row v-show="is_ccpa">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'CCPA Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as CCPA notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea name="notify_message_ccpa_field" v-model="ccpa_message"></c-textarea>
										</c-col>
									</c-row>
									<c-row v-show="is_ccpa">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'CCPA Opt-out Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text you want to display as CCPA notice.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea name="notify_message_ccpa_optout_field" v-model="ccpa_optout_message"></c-textarea>
										</c-col>
									</c-row>
									<c-row v-show="is_gdpr">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'About Cookies Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Text shown under "About Cookies" section when users click on "Cookie Settings" button.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea :rows="6" name="about_message_field" v-model="gdpr_about_cookie_message" :readonly="iabtcf_is_on"></c-textarea>
										</c-col>
									</c-row>
									<c-row v-show="is_lgpd">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Message Heading', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Leave it blank, If you do not need a heading.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea name="bar_heading_text_lgpd_field" v-model="lgpd_message_heading"></c-textarea>
										</c-col>
									</c-row>
									<c-row v-show="is_lgpd">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'LGPD Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea name="notify_message_lgpd_field" v-model="lgpd_message"></c-textarea>
										</c-col>
									</c-row>
									<c-row v-show="is_lgpd">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'About Cookies Message', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Text shown under "About Cookies" section when users click on "Cookie Settings" button.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea :rows="6" name="about_message_lgpd_field" v-model="lgpd_about_cookie_message"></c-textarea>
										</c-col>
									</c-row>
																						
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Consent Banner Title', 'gdpr-cookie-consent' ); ?> </label></c-col>
										<c-col class="col-sm-8">
											<c-input name="gdpr-cookie_bar2_name" v-model="cookie_bar2_name"></c-input>
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
										<c-input class="gdpr-color-input" type="text" v-model="cookie_bar_color2"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-bar-color2" type="color" name="gdpr-cookie-bar-color2" v-model="cookie_bar_color2"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( ' Cookie Bar Opacity', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="cookie_bar_opacity2"></c-input>
										<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.01" name="gdpr-cookie-bar-opacity2" v-model="cookie_bar_opacity2"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="cookie_text_color2"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-text-color2" type="color" name="gdpr-cookie-text-color2" v-model="cookie_text_color2"></c-input>
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
										<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="cookie_bar_border_width2"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-bar-border-width2" v-model="cookie_bar_border_width2"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="cookie_border_color2"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-border-color2" type="color" name="gdpr-cookie-border-color2" v-model="cookie_border_color2"></c-input>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="cookie_bar_border_radius2"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-bar-border-radius2" v-model="cookie_bar_border_radius2"></c-input>
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
												<label><?php esc_attr_e( 'Upload Logo ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'To preview the logo, simply upload a logo and then click the "Save Changes" button ', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
											</c-col>
											<c-col class="col-sm-8 ">
											<c-button color="info" class="button" id="image-upload-button" name="image-upload-button2" @click="openMediaModal2" style="margin: 10px;">
													<?php esc_attr_e( 'Add Image', 'gdpr-cookie-consent' ); ?>
												</c-button>
												<c-button color="info" class="button" id="image-delete-button" @click="deleteSelectedimage2" style="margin: 10px; ">
													<?php esc_attr_e( 'Remove Image', 'gdpr-cookie-consent' ); ?>
												</c-button>
												<?php
												$get_banner_img2 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD2 );												?>
												<img id="gdpr-cookie-bar-logo-holder2" name="gdpr-cookie-bar-logo-holder2" src="<?php echo esc_url_raw( $get_banner_img2 ); ?>">
												<p class="image-upload-notice" style="margin-left: 10px;">
													<?php esc_attr_e( 'We recommend 50 x 50 pixels.', 'gdpr-cookie-consent' ); ?>
												</p>
												<c-input type="hidden" name="gdpr-cookie-bar-logo-url-holder2" id="gdpr-cookie-bar-logo-url-holder2"  class="regular-text"> </c-input>
											</c-col>
										</c-row>
								</c-card-body>
								
								<c-card-body>
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
												<c-row v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on">
													<c-col class="col-sm-6">
														<v-select class="form-group" id="gcc-readmore-as-button" :reduce="label => label.code" :options="show_as_options" v-model="button_readmore_as_button"></v-select>
														<input type="hidden" name="gcc-readmore-as-button" v-model="button_readmore_as_button">
													</c-col>
													<c-col class="col-sm-6">
														<v-select class="form-group" id="gcc-readmore-url-type" :reduce="label => label.code" :options="url_type_options" v-model="button_readmore_url_type"></v-select>
														<input type="hidden" name="gcc-readmore-url-type" v-model="button_readmore_url_type">
													</c-col>
												</c-row>

												<div v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on">
													<c-row v-show="button_readmore_as_button" class="gdpr-label-row">
														<c-col class="col-sm-6"><label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label></c-col>
														<c-col class="col-sm-6"><label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label></c-col>	
													</c-row>
													<c-row v-show="button_readmore_as_button">
														<c-col class="col-sm-6 gdpr-color-pick" >
															<c-input class="gdpr-color-input" type="text" v-model="button_readmore_button_color"></c-input>
															<c-input class="gdpr-color-select" id="gdpr-readmore-button-color" type="color" name="gcc-readmore-button-color" v-model="button_readmore_button_color"></c-input>
														</c-col>
														<c-col class="col-sm-6">
															<v-select class="form-group" id="gcc-readmore-button-size" :reduce="label => label.code" :options="button_size_options" v-model="button_readmore_button_size"></v-select>
															<input type="hidden" name="gcc-readmore-button-size" v-model="button_readmore_button_size">
														</c-col>
													</c-row>
													<c-row v-show="button_readmore_as_button" class="gdpr-label-row">
														<c-col class="col-sm-6"><label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label></c-col>
														<c-col class="col-sm-6"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label></c-col>
													</c-row>
													<c-row v-show="button_readmore_as_button">
														<c-col class="col-sm-6">
															<v-select class="form-group" id="gcc-readmore-button-border-style" :reduce="label => label.code" :options="border_style_options" v-model="button_readmore_button_border_style"></v-select>
															<input type="hidden" name="gcc-readmore-button-border-style" v-model="button_readmore_button_border_style">
														</c-col>
														<c-col class="col-sm-6 gdpr-color-pick" >
															<c-input class="gdpr-color-input" type="text" v-model="button_readmore_button_border_color"></c-input>
															<c-input class="gdpr-color-select" id="gdpr-readmore-button-border-color" type="color" name="gcc-readmore-button-border-color" v-model="button_readmore_button_border_color"></c-input>
														</c-col>
													</c-row>
													<c-row class="gdpr-label-row">
														<c-col class="col-sm-6" v-show="button_readmore_url_type"><label><?php esc_attr_e( 'Page', 'gdpr-cookie-consent' ); ?></label></c-col>
														<c-col v-show="!button_readmore_url_type" class="col-sm-6"><label><?php esc_attr_e( 'URL', 'gdpr-cookie-consent' ); ?></label></c-col>
														<c-col class="col-sm-3 gdpr-readmore-toggle-row" v-show="button_readmore_url_type"><label><?php esc_attr_e( 'Sync with WordPress Policy Page', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'If enabled visitor will be redirected to Privacy Policy Page set in WordPress settings irrespective of Page set in the previous setting.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
														<c-col class="col-sm-3 gdpr-readmore-toggle-row" v-show="button_readmore_url_type">
															<c-switch v-bind="labelIcon" v-model="button_readmore_wp_page" id="gdpr-cookie-consent-readmore-wp-page" variant="3d"  color="success" :checked="button_readmore_wp_page" v-on:update:checked="onSwitchButtonReadMoreWpPage"></c-switch>
															<input type="hidden" name="gcc-readmore-wp-page" v-model="button_readmore_wp_page">
														</c-col>
													</c-row>
													<c-row>
														<c-col v-show="button_readmore_url_type" class="col-sm-6">
															<v-select class="form-group"  placeholder="Select Policy Page" id="gcc-readmore-page" :reduce="label => label.code" :options="privacy_policy_options" v-model="readmore_page" @input="onSelectPrivacyPage"></v-select>
															<input type="hidden" name="gcc-readmore-page" v-model="button_readmore_page">
														</c-col>
														<c-col class="col-sm-6" v-show="!button_readmore_url_type">
															<c-input name="gcc-readmore-url" v-model="button_readmore_url"></c-input>
														</c-col>
														<c-col class="col-sm-3 gdpr-readmore-toggle-row"><label><?php esc_attr_e( 'Open URL in New Window?', 'gdpr-cookie-consent' ); ?></label></c-col>
														<c-col class="col-sm-3 gdpr-readmore-toggle-row">
															<c-switch v-bind="labelIcon" v-model="button_readmore_new_win" id="gdpr-cookie-consent-readmore-new-win" variant="3d"  color="success" :checked="button_readmore_new_win" v-on:update:checked="onSwitchButtonReadMoreNewWin"></c-switch>
															<input type="hidden" name="gcc-readmore-new-win" v-model="button_readmore_new_win">
														</c-col>
													</c-row>
													<c-row v-show="button_readmore_as_button" class="gdpr-label-row">
														<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label></c-col>
														<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label></c-col>
														<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label></c-col>
													</c-row>
													<c-row v-show="button_readmore_as_button">
														<c-col class="col-sm-4 gdpr-color-pick">
															<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="button_readmore_button_opacity"></c-input>
															<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-opacity" v-model="button_readmore_button_opacity"></c-input>
														</c-col>
														<c-col class="col-sm-4 gdpr-color-pick">
															<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="button_readmore_button_border_width"></c-input>
															<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-border-width" v-model="button_readmore_button_border_width"></c-input>
														</c-col>
														<c-col class="col-sm-4 gdpr-color-pick">
															<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="button_readmore_button_border_radius"></c-input>
															<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-border-radius" v-model="button_readmore_button_border_radius"></c-input>
														</c-col>
													</c-row>	
												</div>

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
									<c-switch v-bind="labelIcon" v-model="cookie_accept_on2" id="gdpr-cookie-consent-cookie2" variant="3d"  color="success" :checked="cookie_accept_on2" v-on:update:checked="onSwitchCookieAcceptEnable2"></c-switch>
									<input type="hidden" name="gcc-cookie-accept-enable2" v-model="cookie_accept_on2">
								</c-col>
								<c-col class="col-sm-3">
									<c-button :disabled="!cookie_accept_on2" class="gdpr-configure-button" @click="accept_button_popup2 = true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
							</c-row>
							<div class="opt-out-link-container">
							<c-modal
								title="Accept Button"
								:show.sync="accept_button_popup2"
								size="lg"
								:close-on-backdrop="closeOnBackdrop"
								:centered="centered"
							>
							<div class="optout-settings-tittle-bar">
									<div class="optout-setting-tittle"><?php esc_attr_e( 'Accept Button', 'gdpr-cookie-consent' ); ?></div>
									<img @click="accept_button_popup2=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
									<c-input name="button_accept_text_field2" v-model="accept_text2"></c-input>
								</c-col>
								<c-col class="col-sm-6 gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="accept_text_color2"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-text-color2" type="color" name="gdpr-cookie-accept-text-color2" v-model="accept_text_color2"></c-input>
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
									<v-select class="form-group" id="gdpr-cookie-accept-as-button2" :reduce="label => label.code" :options="accept_as_button_options" v-model="accept_as_button2" @input="onButtonChange($event, 'accept2')"></v-select>
									<input type="hidden" name="gdpr-cookie-accept-as2" v-model="accept_as_button2">
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-accept-action2" :reduce="label => label.code" :options="accept_action_options" v-model="accept_action2" 	>
									</v-select>
									<input type="hidden" name="gdpr-cookie-accept-action2" v-model="accept_action2">
								</c-col>
							</c-row>
							<c-row v-show="accept_action2!='#cookie_action_close_header'"  class="gdpr-label-row">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row v-show="accept_action2!='#cookie_action_close_header'">
								<c-col class="col-sm-6">
									<c-input name="gdpr-cookie-accept-url2" v-model="accept_url2"></c-input>
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-url-new-window2" :reduce="label => label.code" :options="open_url_options" v-model="open_url2"></v-select>
									<input type="hidden" name="gdpr-cookie-url-new-window2" v-model="open_url2">
								</c-col>
							</c-row>
							<c-row class="gdpr-label-row"  v-show="accept_as_button2">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row  v-show="accept_as_button2">
								<c-col class="col-sm-6  gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="accept_background_color2"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-background-color2" type="color" name="gdpr-cookie-accept-background-color2" v-model="accept_background_color2"></c-input>
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-accept-size2" :reduce="label => label.code" :options="accept_size_options" v-model="accept_size2">
									</v-select>
									<input type="hidden" name="gdpr-cookie-accept-size2" v-model="accept_size2">
								</c-col>
							</c-row>
							<c-row  v-show="accept_as_button2" class="gdpr-label-row">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row v-show="accept_as_button2">
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-accept-border-style2" :reduce="label => label.code" :options="border_style_options" v-model="accept_style2">
									</v-select>
									<input type="hidden" name="gdpr-cookie-accept-border-style2" v-model="accept_style2">
								</c-col>
								<c-col class="col-sm-6 gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="accept_border_color2"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-border-color" type="color" name="gdpr-cookie-accept-border-color2" v-model="accept_border_color2"></c-input>
								</c-col>
							</c-row>
							<c-row v-show="accept_as_button2" class="gdpr-label-row">
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
							<c-row v-show="accept_as_button2">
								<c-col class="col-sm-4  gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="accept_opacity2"></c-input>
									<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-accept-opacity2" v-model="accept_opacity2"></c-input>
								</c-col>
								<c-col class="col-sm-4 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="accept_border_width2"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-border-width2" v-model="accept_border_width2"></c-input>
								</c-col>
								<c-col class="col-sm-4  gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="accept_border_radius2"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-border-radius2" v-model="accept_border_radius2"></c-input>
								</c-col>
							</c-row>
									<button class="done-button-settings" @click="accept_button_popup2=false"><span>Done</span></button>
								
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
									<c-switch v-bind="labelIcon" v-model="cookie_accept_all_on2" id="gdpr-cookie-consent-cookie-acceptall-on2" variant="3d"  color="success" :checked="cookie_accept_all_on2" v-on:update:checked="onSwitchCookieAcceptAllEnable2"></c-switch>
									<input type="hidden" name="gcc-cookie-accept-all-enable2" v-model="cookie_accept_all_on2">
								</c-col>
								<c-col class="col-sm-3">
									<c-button :disabled="!cookie_accept_all_on2" class="gdpr-configure-button" @click="accept_all_button_popup2=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
							</c-row>
							<div class="opt-out-link-container">
							<c-modal
								title="Accept All Button"
								:show.sync="accept_all_button_popup2"
								size="lg"
								:close-on-backdrop="closeOnBackdrop"
								:centered="centered"
							>
							<div class="optout-settings-tittle-bar">
									<div class="optout-setting-tittle"><?php esc_attr_e( 'Accept All Button', 'gdpr-cookie-consent' ); ?></div>
									<img @click="accept_all_button_popup2=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
									<c-input name="button_accept_all_text_field2" v-model="accept_all_text2"></c-input>
								</c-col>
								<c-col class="col-sm-6 gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="accept_all_text_color2"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-all-text-color2" type="color" name="gdpr-cookie-accept-all-text-color2" v-model="accept_all_text_color2"></c-input>
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
									<v-select class="form-group" id="gdpr-cookie-accept-all-as-button2" :reduce="label => label.code" :options="accept_as_button_options" v-model="accept_all_as_button2" @input="onButtonChange($event, 'accept_all2')"></v-select>
									<input type="hidden" name="gdpr-cookie-accept-all-as2" v-model="accept_all_as_button2">
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-accept-all-action2" :reduce="label => label.code" :options="accept_action_options" v-model="accept_all_action2" >
									</v-select>
									<input type="hidden" name="gdpr-cookie-accept-all-action2" v-model="accept_all_action2">
								</c-col>
							</c-row>
							<c-row v-show="accept_all_action2!='#cookie_action_close_header'"  class="gdpr-label-row">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row v-show="accept_all_action2!='#cookie_action_close_header'">
								<c-col class="col-sm-6">
									<c-input name="gdpr-cookie-accept-all-url2" v-model="accept_all_url2"></c-input>
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-accept-all-new-window2" :reduce="label => label.code" :options="open_url_options" v-model="accept_all_new_win2"></v-select>
									<input type="hidden" name="gdpr-cookie-accept-all-new-window2" v-model="accept_all_new_win2">
								</c-col>
							</c-row>
							<c-row class="gdpr-label-row"  v-show="accept_all_as_button2">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row  v-show="accept_all_as_button2">
								<c-col class="col-sm-6  gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="accept_all_background_color2"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-all-background-color2" type="color" name="gdpr-cookie-accept-all-background-color2" v-model="accept_all_background_color2"></c-input>
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-accept-all-size2" :reduce="label => label.code" :options="accept_size_options" v-model="accept_all_size2">
									</v-select>
									<input type="hidden" name="gdpr-cookie-accept-all-size2" v-model="accept_all_size2">
								</c-col>
							</c-row>
							<c-row  v-show="accept_all_as_button2" class="gdpr-label-row">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row v-show="accept_all_as_button2">
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-accept-all-border-style2" :reduce="label => label.code" :options="border_style_options" v-model="accept_all_style2">
									</v-select>
									<input type="hidden" name="gdpr-cookie-accept-all-border-style2" v-model="accept_all_style2">
								</c-col>
								<c-col class="col-sm-6 gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="accept_all_border_color2"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-all-border-color2" type="color" name="gdpr-cookie-accept-all-border-color2" v-model="accept_all_border_color2"></c-input>
								</c-col>
							</c-row>
							<c-row v-show="accept_all_as_button2" class="gdpr-label-row">
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
							<c-row v-show="accept_all_as_button2">
								<c-col class="col-sm-4  gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="accept_all_opacity2"></c-input>
									<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-accept-all-opacity2" v-model="accept_all_opacity2"></c-input>
								</c-col>
								<c-col class="col-sm-4 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="accept_all_border_width2"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-all-border-width2" v-model="accept_all_border_width2"></c-input>
								</c-col>
								<c-col class="col-sm-4  gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="accept_all_border_radius2"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-all-border-radius2" v-model="accept_all_border_radius2"></c-input>
								</c-col>
							</c-row>
									<button class="done-button-settings" @click="accept_all_button_popup2=false"><span>Done</span></button>
								
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
									<c-switch v-bind="labelIcon" v-model="cookie_decline_on2" id="gdpr-cookie-consent-decline-on2" variant="3d"  color="success" :checked="cookie_decline_on2" v-on:update:checked="onSwitchCookieDeclineEnable2"></c-switch>
									<input type="hidden" name="gcc-cookie-decline-enable2" v-model="cookie_decline_on2">
								</c-col>
								<c-col class="col-sm-3">
									<c-button :disabled="!cookie_decline_on2" class="gdpr-configure-button" @click="decline_button_popup2=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
							</c-row>
							<div class="opt-out-link-container">
							<c-modal
								title="Decline Button"
								:show.sync="decline_button_popup2"
								size="lg"
								:close-on-backdrop="closeOnBackdrop"
								:centered="centered"
							>
							<div class="optout-settings-tittle-bar">
									<div class="optout-setting-tittle"><?php esc_attr_e( 'Decline Button', 'gdpr-cookie-consent' ); ?></div>
									<img @click="decline_button_popup2=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
										<c-input name="button_decline_text_field2" v-model="decline_text2"></c-input>
									</c-col>
									<c-col class="col-sm-6  gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="decline_text_color2"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-decline-text-color2" type="color" name="gdpr-cookie-decline-text-color2" v-model="decline_text_color2"></c-input>
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
										<v-select class="form-group" id="gdpr-cookie-decline-as-button2" :reduce="label => label.code" :options="accept_as_button_options" v-model="decline_as_button2" @input="onButtonChange($event, 'decline2')"></v-select>
										<input type="hidden" name="gdpr-cookie-decline-as2" v-model="decline_as_button2">
									</c-col>
									<c-col class="col-sm-6"><v-select class="form-group" id="gdpr-cookie-decline-action2" :reduce="label => label.code" :options="decline_action_options" v-model="decline_action2">
										</v-select>
										<input type="hidden" name="gdpr-cookie-decline-action2" v-model="decline_action2">
									</c-col>
								</c-row>
								<c-row v-show="decline_action2!='#cookie_action_close_header_reject'" class="gdpr-label-row">
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
								</c-row>
								<c-row v-show="decline_action2!='#cookie_action_close_header_reject'">
									<c-col class="col-sm-6">
										<c-input name="gdpr-cookie-decline-url2" v-model="decline_url2"></c-input>
									</c-col>
									<c-col class="col-sm-6">
										<v-select class="form-group" id="gdpr-cookie-decline-url-new-window2" :reduce="label => label.code" :options="open_url_options" v-model="open_decline_url2"></v-select>
										<input type="hidden" name="gdpr-cookie-decline-url-new-window2" v-model="open_decline_url2">
									</c-col>
								</c-row>
								<c-row v-show="decline_as_button2" class="gdpr-label-row">
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
								</c-row>
								<c-row v-show="decline_as_button2">
									<c-col class="col-sm-6  gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="decline_background_color2"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-decline-background-color2" type="color" name="gdpr-cookie-decline-background-color2" v-model="decline_background_color2"></c-input>
									</c-col>
									<c-col class="col-sm-6">
										<v-select class="form-group" id="gdpr-cookie-decline-size2" :reduce="label => label.code" :options="accept_size_options" v-model="decline_size2">
										</v-select>
										<input type="hidden" name="gdpr-cookie-decline-size2" v-model="decline_size2">
									</c-col>
								</c-row>
								<c-row v-show="decline_as_button2" class="gdpr-label-row">
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
								</c-row>
								<c-row v-show="decline_as_button2">
									<c-col class="col-sm-6">
										<v-select class="form-group" id="gdpr-cookie-decline-border-style2" :reduce="label => label.code" :options="border_style_options" v-model="decline_style2">
										</v-select>
										<input type="hidden" name="gdpr-cookie-decline-border-style2" v-model="decline_style2">
									</c-col>
									<c-col class="col-sm-6  gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="decline_border_color2"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-decline-border-color2" type="color" name="gdpr-cookie-decline-border-color2" v-model="decline_border_color2"></c-input>
									</c-col>
								</c-row>
								<c-row  v-show="decline_as_button2" class="gdpr-label-row">
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
								<c-row v-show="decline_as_button2">
									<c-col class="col-sm-4 gdpr-color-pick"><c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="decline_opacity2"></c-input>
										<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-decline-opacity2" v-model="decline_opacity2"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick"><c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="decline_border_width2"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-decline-border-width2" v-model="decline_border_width2"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="decline_border_radius2"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-decline-border-radius2" v-model="decline_border_radius2"></c-input>
									</c-col>
								</c-row> 
										<button class="done-button-settings" @click="decline_button_popup2=false"><span>Done</span></button>
								
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
									<c-switch v-bind="labelIcon" v-model="cookie_settings_on2" id="gdpr-cookie-consent-settings-on2" variant="3d"  color="success" :checked="cookie_settings_on2" v-on:update:checked="onSwitchCookieSettingsEnable2"></c-switch>
									<input type="hidden" name="gcc-cookie-settings-enable2" v-model="cookie_settings_on2">
								</c-col>
								<c-col class="col-sm-3">
									<c-button :disabled="!cookie_settings_on2" class="gdpr-configure-button" @click="settings_button_popup2=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
								<c-col class="col-sm-4">
										<label><?php esc_attr_e( 'Display Cookies List on Frontend', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-4">
										<c-switch v-bind="labelIcon" v-model="cookie_on_frontend2" id="gdpr-cookie-consent-cookie-on-frontend2" variant="3d"  color="success" :checked="cookie_on_frontend2" v-on:update:checked="onSwitchCookieOnFrontend2"></c-switch>
										<input type="hidden" name="gcc-cookie-on-frontend2" v-model="cookie_on_frontend2">
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
								:show.sync="settings_button_popup2"
								size="lg"
								:close-on-backdrop="closeOnBackdrop"
								:centered="centered"
							>
							<div class="optout-settings-tittle-bar">
									<div class="optout-setting-tittle"><?php esc_attr_e( 'Settings Button', 'gdpr-cookie-consent' ); ?></div>
									<img @click="settings_button_popup2=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
									<c-input name="button_settings_text_field2" v-model="settings_text2"></c-input>
								</c-col>
								<c-col class="col-sm-6  gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="settings_text_color2"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-settings-text-color2" type="color" name="gdpr-cookie-settings-text-color2" v-model="settings_text_color2"></c-input>
								</c-col>
							</c-row>
							<c-row class="gdpr-label-row">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-settings-as-button2" :reduce="label => label.code" :options="accept_as_button_options" v-model="settings_as_button2" @input="onButtonChange($event, 'settings2')"></v-select>
									<input type="hidden" name="gdpr-cookie-settings-as2" v-model="settings_as_button2">
								</c-col>
							</c-row>
							<c-row v-show="settings_as_button2" class="gdpr-label-row">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row v-show="settings_as_button2" class="gdpr-label-row">
								<c-col class="col-sm-6 gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="settings_background_color2"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-settings-background-color2" type="color" name="gdpr-cookie-settings-background-color2" v-model="settings_background_color2"></c-input>
								</c-col>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-settings-size2" :reduce="label => label.code" :options="accept_size_options" v-model="settings_size2">
									</v-select>
									<input type="hidden" name="gdpr-cookie-settings-size2" v-model="settings_size2">
								</c-col>
							</c-row>
							<c-row  v-show="settings_as_button2" class="gdpr-label-row">
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
							</c-row>
							<c-row v-show="settings_as_button2">
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-settings-border-style2" :reduce="label => label.code" :options="border_style_options" v-model="settings_style2">
									</v-select>
									<input type="hidden" name="gdpr-cookie-settings-border-style2" v-model="settings_style2">
								</c-col>
								<c-col class="col-sm-6 gdpr-color-pick">
									<c-input class="gdpr-color-input" type="text" v-model="settings_border_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-settings-border-color2" type="color" name="gdpr-cookie-settings-border-color2" v-model="settings_border_color2"></c-input>
								</c-col>
							</c-row>
							<c-row v-show="settings_as_button2" class="gdpr-label-row">
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
							<c-row v-show="settings_as_button2">
								<c-col class="col-sm-4 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="settings_opacity2"></c-input>
									<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-settings-opacity2" v-model="settings_opacity2"></c-input>
								</c-col>
								<c-col class="col-sm-4 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="settings_border_width2"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-settings-border-width2" v-model="settings_border_width2"></c-input>
								</c-col>
								<c-col class="col-sm-4 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="settings_border_radius2"></c-input>
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-settings-border-radius2" v-model="settings_border_radius2"></c-input>
								</c-col>
							</c-row>
							
										<button class="done-button-settings" @click="settings_button_popup2=false"><span>Done</span></button>
							
							</c-modal>
							</div>
						</c-card-body>
					</c-card>
					<c-card  v-show="is_ccpa">
						<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Confirm Button', 'gdpr-cookie-consent' ); ?></c-card-header>
						<c-card-body>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Confirm Button Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8">
									<c-button class="gdpr-configure-button" @click="confirm_button_popup2=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
							</c-row>
							<div class="opt-out-link-container">
							<c-modal
								title="Confirm Button"
								:show.sync="confirm_button_popup2"
								size="lg"
								:close-on-backdrop="closeOnBackdrop"
								:centered="centered"
							>
							<div class="optout-settings-tittle-bar">
									<div class="optout-setting-tittle"><?php esc_attr_e( 'Confirm Button', 'gdpr-cookie-consent' ); ?></div>
									<img @click="confirm_button_popup2=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
										<c-input name="button_confirm_text_field2" v-model="confirm_text2"></c-input>
									</c-col>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="confirm_text_color2"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-confirm-text-color2" type="color" name="gdpr-cookie-confirm-text-color2" v-model="confirm_text_color2"></c-input>
									</c-col>
								</c-row>
								<c-row class="gdpr-label-row">
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="confirm_background_color2"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-confirm-background-color2" type="color" name="gdpr-cookie-confirm-background-color2" v-model="confirm_background_color2"></c-input>
									</c-col>
									<c-col class="col-sm-6">
										<v-select class="form-group" id="gdpr-cookie-confirm-size2" :reduce="label => label.code" :options="accept_size_options" v-model="confirm_size2">
										</v-select>
										<input type="hidden" name="gdpr-cookie-confirm-size2" v-model="confirm_size2">
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
										<v-select class="form-group" id="gdpr-cookie-confirm-border-style2" :reduce="label => label.code" :options="border_style_options" v-model="confirm_style2">
										</v-select>
										<input type="hidden" name="gdpr-cookie-confirm-border-style2" v-model="confirm_style2">
									</c-col>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="confirm_border_color2"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-confirm-border-color2" type="color" name="gdpr-cookie-confirm-border-color2" v-model="confirm_border_color2"></c-input>
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
										<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="confirm_opacity2"></c-input>
										<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-confirm-opacity2" v-model="confirm_opacity2"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="confirm_border_width2"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-confirm-border-width2" v-model="confirm_border_width2"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="confirm_border_radius2"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-confirm-border-radius2" v-model="confirm_border_radius2"></c-input>
									</c-col>
								</c-row>
										<button class="done-button-settings" @click="confirm_button_popup2=false"><span>Done</span></button>
								
							</c-modal>
							</div>
						</c-card-body>
					</c-card>
					<c-card v-show="is_ccpa">
						<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Cancel Button', 'gdpr-cookie-consent' ); ?></c-card-header>
						<c-card-body>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cancel Button Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8">
									<c-button class="gdpr-configure-button" @click="cancel_button_popup2=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
							</c-row>
							<div class="opt-out-link-container">
							<c-modal
								title="Cancel Button"
								:show.sync="cancel_button_popup2"
								size="lg"
								:close-on-backdrop="closeOnBackdrop"
								:centered="centered"
							>
							<div class="optout-settings-tittle-bar">
									<div class="optout-setting-tittle"><?php esc_attr_e( 'Cancel Button', 'gdpr-cookie-consent' ); ?></div>
									<img @click="cancel_button_popup2=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
										<c-input name="button_cancel_text_field2" v-model="cancel_text2"></c-input>
									</c-col>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="cancel_text_color2"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-cancel-text-color2" type="color" name="gdpr-cookie-cancel-text-color2" v-model="cancel_text_color2"></c-input>
									</c-col>
								</c-row>
								<c-row class="gdpr-label-row">
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-6">
										<label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="cancel_background_color2"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-cancel-background-color2" type="color" name="gdpr-cookie-cancel-background-color2" v-model="cancel_background_color2"></c-input>
									</c-col>
									<c-col class="col-sm-6">
										<v-select class="form-group" id="gdpr-cookie-cancel-size2" :reduce="label => label.code" :options="accept_size_options" v-model="cancel_size2">
										</v-select>
										<input type="hidden" name="gdpr-cookie-cancel-size2" v-model="cancel_size2">
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
										<v-select class="form-group" id="gdpr-cookie-cancel-border-style2" :reduce="label => label.code" :options="border_style_options" v-model="cancel_style2">
										</v-select>
										<input type="hidden" name="gdpr-cookie-cancel-border-style2" v-model="cancel_style2">
									</c-col>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="cancel_border_color2"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-cancel-border-color2" type="color" name="gdpr-cookie-cancel-border-color2" v-model="cancel_border_color2"></c-input>
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
										<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="cancel_opacity2"></c-input>
										<c-input class="gdpr-slider-input opacity-slider" type="number" min="0" max="1" step="0.1" name="gdpr-cookie-cancel-opacity2" v-model="cancel_opacity2"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="cancel_border_width2"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-cancel-border-width2" v-model="cancel_border_width2"></c-input>
									</c-col>
									<c-col class="col-sm-4 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="cancel_border_radius2"></c-input>
										<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-cancel-border-radius2" v-model="cancel_border_radius2"></c-input>
									</c-col>
								</c-row>
										<button class="done-button-settings" @click="cancel_button_popup2=false"><span>Done</span></button>
								
							</c-modal>
							</div>
						</c-card-body>
					</c-card>
					<c-card  v-show="is_ccpa">
						<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Opt-out Link', 'gdpr-cookie-consent' ); ?></c-card-header>
						<c-card-body>
							<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Opt-out Link Settings', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="col-sm-8">
									<c-button class="gdpr-configure-button" @click="opt_out_link_popup2=true">
										<span>
											<img class="gdpr-configure-image" :src="configure_image_url.default">
											<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>
										</span>
									</c-button>
								</c-col>
							</c-row>
							<div class="opt-out-link-container">
							<c-modal
								title="Opt-out Link"
								:show.sync="opt_out_link_popup2"
								size="lg"
								:close-on-backdrop="closeOnBackdrop"
								:centered="centered"
							>
							<div class="optout-settings-tittle-bar">
									<div class="optout-setting-tittle"><?php esc_attr_e( 'Opt-out Link', 'gdpr-cookie-consent' ); ?></div>
									<img @click="opt_out_link_popup2=false" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
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
										<c-input name="button_donotsell_text_field2" v-model="opt_out_text2"></c-input>
									</c-col>
									<c-col class="col-sm-6 gdpr-color-pick">
										<c-input class="gdpr-color-input" type="text" v-model="opt_out_text_color2"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-cookie-opt-out-text-color2" type="color" name="gdpr-cookie-opt-out-text-color2" v-model="opt_out_text_color2"></c-input>
									</c-col>
								</c-row>
										<button class="done-button-settings" @click="opt_out_link_popup2=false"><span>Done</span></button>
								
							</c-modal>
							</div>
						</c-card-body>
					</c-card>
							</c-card-body>	
							
					</c-card>
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
							/>
						</c-col>
						<?php
					}
					?>
					</div>

				</c-tab>

				<c-tab title="<?php esc_attr_e( 'Cookie Manager', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#cookie_list" 	id="gdpr-cookie-consent-cookies-list" style="position: relative;">
					<div class="gdpr-cookie-list-tabs-container" v-show="cookie_list_tab == true">
						<img class="gdpr-cookie-list-tabs-logo"src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/CookieConsent.png'; ?>" alt="Cookie Setting preview logo">
						<p class="gdpr-cookie-list-tabs-heading"><?php esc_html_e( 'Create a Custom Cookie', 'gdpr-cookie-consent' ); ?></p>
						<p class="gdpr-cookie-list-tabs-sub-heading"><?php esc_html_e( 'Design and personalize a unique cookie to suit your preferences.', 'gdpr-cookie-consent' ); ?>.</p>
						<input type="button" class="gdpr-cookie-list-tabs-popup-btn" value="Create Cookie" @click="showCreateCookiePopup">
					</div>
					<c-card v-show="cookie_list_tab == true"class="cookie_list">
						<div id="popup-container" class="gdpr-cookie-consent-cookies-list-popup" :class="{'show-cookie-list-popup':show_custom_cookie_popup,'popup-overlay':show_custom_cookie_popup}">
							<div class="gdpr-cooki-list-tabs-popup-content">
								<div class="cookie-list-tittle-bar">
									<div></div>
									<div class="cookie-list-tittle" slot="header"><?php esc_attr_e('Create Custom Cookie', 'gdpr-cookie-consent'); ?></div>
									<div><img  @click="showCreateCookiePopup" class="cookie-list-close-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/Close_round.svg'; ?>" alt="Add new entry logo"></div>
								</div>
								<div class="gdpr-add-custom-cookie-form">
									<input type="hidden" name="gdpr_addcookie" value="1">
									<div class="gdpr-custom-cookie-box">
										<!-- <c-col class="col-sm-2 gdpr-custom-cookie-letter-box"><span class="gdpr-custom-cookie-letter">C</span></c-col> -->
										<div class="gdpr-custom-cookie-box-inputs-fields">
											<div class="gdpr-custom-cookie-box-inputs table-rows">
												<div class="col-sm-4 table-cols-left"><label>Cookie Name</label><c-input placeholder="Cookie Name" name="gdpr-cookie-consent-custom-cookie-name" v-model="custom_cookie_name"></c-input></div>	
												<div class="col-sm-4 table-cols"><label>Cookie Domain</label><c-input placeholder="Cookie Domain" name="gdpr-cookie-consent-custom-cookie-domain" v-model="custom_cookie_domain"></c-input></div>
												<div class="col-sm-4 table-cols"><label>Duration (Days/Session)</label><c-input :placeholder="custom_cookie_duration_placeholder" name="gdpr-cookie-consent-custom-cookie-days" v-model="custom_cookie_duration" :disabled="is_custom_cookie_duration_disabled"></c-input></div>
											</div>
											<div class="gdpr-custom-cookie-box-inputs table-rows">
												<div class="col-sm-6 table-cols-left"><v-select class="gdpr-custom-cookie-select form-group" :reduce="label => label.code" :options="custom_cookie_categories" v-model="custom_cookie_category"></v-select></div>
												<input type="hidden" name="gdpr-custom-cookie-category" v-model="custom_cookie_category">
												<div class="col-sm-6 table-cols"><v-select class="gdpr-custom-cookie-select form-group" :reduce="label => label.code" :options="custom_cookie_types" v-model="custom_cookie_type" @input="onSelectCustomCookieType"></v-select></div>
												<input type="hidden" name="gdpr-custom-cookie-type" v-model="custom_cookie_type">
											</div>
											<div class="gdpr-custom-cookie-box-inputs table-rows">
												<div class="col-sm-12 table-cols-left"><label>Cookie Purpose</label><div><textarea placeholder="Cookie Purpose" name="gdpr-cookie-consent-custom-cookie-purpose" v-model="custom_cookie_description" style="height:173px;width:807px;"></textarea></div></div>
											</div>
											<div  class="gdpr-custom-cookie-box-inputs table-rows" class="col-sm-9 gdpr-custom-cookie-links">
											<div class="gdpr-custom-cookie-box-btn">
												<input type="button" @click="onSaveCustomCookie" class="gdpr-custom-cookie-box-save-btn gdpr-custom-cookie-link gdpr-custom-save-cookie" value="Save Cookie">
												<input type="button" @click="showCreateCookiePopup" class="gdpr-custom-cookie-box-cancle-btn" value="Cancel">
											</div>
												<!-- <a class="table-cols-left gdpr-custom-cookie-link gdpr-custom-save-cookie" @click="onSaveCustomCookie"><?php esc_attr_e( 'Save', 'gdpr-cookie-consent' ); ?></a>
												<a class="gdpr-custom-cookie-link" @click="hideCookieForm"><?php esc_attr_e( 'Cancel', 'gdpr-cookie-consent' ); ?></a> -->
											</div>
										</div>
										<!-- <c-col class="col-sm-3"></c-col> -->
									</div>
								</div>
							</div>
						</div>
						<div id="gdpr-custom-cookie-saved" v-if="post_cookie_list_length > 0">
						<?php require plugin_dir_path( __FILE__ ) . 'gdpr-custom-saved-cookie.php'; ?>
						</div>
					</c-card>
					<c-card v-show="discovered_cookies_list_tab == true">
						<div id="cookie-scanner-container" class="cookie-scanner-container">
							<div class="data_wait_loader_container">
								<div class="data_wait_loader"></div>
							</div>
							 <div v-html="cookie_scanner_data"></div>
						</div>
					</c-card>
					<c-card v-show="scan_history_list_tab == true">
						<?php do_action( 'gdpr_cookie_scanned_history' ); ?>
					</c-card>
				</c-tab>
				<c-button class="cookie_scan_dropdown" @click="openCookieDropdown"><i class="cookie_arrow down"></i></c-button>
				
				<div v-show="cookie_scan_dropdown" class="gdpr-cookie-list-tabs">
						<c-button class="gdpr-cookie-consent-cookie-list-tab" @click="onChangeCookieListTab":class="{ 'gdpr-cookie-consent-cookie-list-tab-active': cookie_list_tab == true }"><?php esc_html_e( 'Custom Cookies', 'gdpr-cookie-consent' ); ?></c-button>
						<c-button class="gdpr-cookie-consent-cookie-list-tab"  @click="onChangeDiscoveredListTab":class="{ 'gdpr-cookie-consent-cookie-list-tab-active': discovered_cookies_list_tab == true }"><?php esc_html_e( 'Discovered Cookies', 'gdpr-cookie-consent' ); ?></c-button>
						<c-button class="gdpr-cookie-consent-cookie-list-tab"  @click="onChangeScanHistoryTab":class="{ 'gdpr-cookie-consent-cookie-list-tab-active': scan_history_list_tab == true }"><?php esc_html_e( 'Scan History', 'gdpr-cookie-consent' ); ?></c-button>
				</div>
				<c-card v-show="cookie_scan_dropdown" class="dropdown_background" @click="openCookieDropdown"></c-card> 
				<!-- Script Blocker -->
				<?php do_action( 'gdpr_settings_script_blocker_tab' ); ?>
				<!--A/B Testing-->
				<c-tab title="<?php esc_attr_e( 'A/B Testing', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#ab_testing" id="gdpr-cookie-consent-ab-testing">
					<div id="ab-testing-container">
						<div class="ab_test_data_wait_loader_container">
							<div class="data_wait_loader"></div>
						</div>
						<div v-html="ab_testing_data"></div>
					</div>
				</c-tab>
				<!-- Integration -->
				<c-tab title="<?php esc_attr_e( 'Language', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#language" id="gdpr-cookie-consent-language">
					<c-card class="language-card">
							<c-card-body>
								<c-row>
									<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice-top"><?php esc_html_e( 'Languages', 'gdpr-cookie-consent' ); ?></div></c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Select a language for your cookie consent banner', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<input type="hidden" name="select-banner-lan" v-model="show_language_as">
										<v-select class="form-group" id="gdpr-select-banner-lan" :reduce="label => label.code" :options="show_language_as_options" v-model="show_language_as"  @input="onLanguageChange"></v-select>
									</c-col>
								</c-row>
							</c-card-body>
						</c-card>

				</c-tab>
				<!-- Connection Tab  -->
				<?php if ( $is_user_connected && ! $pro_is_activated ) : ?>
				<c-tab title="<?php esc_attr_e( 'Connection', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#connection" id="gdpr-cookie-consent-connection">
					<c-card class="gdpr-cookie-consent-settings-cookie-notice-top">
							<c-card-body class="gdpr-connection-card-body" >
								<div class="gdpr-connect-information">
									<div class="gdpr-connection-success-tick">
										<div class="gdpr-connection-success-img"><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/check_ring.svg'; ?>" alt="API Connection Success Mark"></div>
										<div class="gdpr-connection-success-descreption"><?php esc_html_e( 'Your website is connected to WP Cookie Consent', 'gdpr-cookie-consent' ); ?></div>
									</div>
									<div class="gdpr-connect-information-section">
										<p class="gpdr-email-info"><span class="gdpr-info-title" ><?php esc_html_e( 'Email : ', 'gdpr-cookie-consent' ); ?></span> <?php echo esc_html( $api_user_email ); ?>  </p>
										<p><span class="gdpr-info-title" ><?php esc_html_e( 'Site Key : ', 'gdpr-cookie-consent' ); ?></span> <?php echo esc_html( $api_user_site_key ); ?>  </p>
										<p><span class="gdpr-info-title" ><?php esc_html_e( 'Plan : ', 'gdpr-cookie-consent' ); ?></span> <?php echo esc_html( $api_user_plan ); ?>  </p>
										<!-- API Disconnect Button  -->
										<div class="api-connection-disconnect-btn" ><?php esc_attr_e( 'Disconnect', 'gdpr-cookie-consent' ); ?></div>
									</div>
								</div>
							</c-card-body>
						</c-card>
				</c-tab>
				<?php endif; ?>
			</c-tabs>
			</div>
		</c-form>
	</c-container>
</div>
<div id="gdpr-mascot-app"></div>
<?php
