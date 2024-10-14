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
$no_of_pages_scan       = get_option( 'gdpr_no_of_page_scan' );
$total_pages_scan_limit = 100;

if ( $api_user_plan == 'free' ) {
	$total_pages_scan_limit = 100;
} else {
	$total_pages_scan_limit = 20000;
}

$gdpr_no_of_page_scan_left       = $total_pages_scan_limit - get_option( 'gdpr_no_of_page_scan' );
$remaining_percentage_scan_limit = ( get_option( 'gdpr_no_of_page_scan' ) / $total_pages_scan_limit ) * 100;

?>
<div id="gdpr-before-mount" style="top:0;left:0;right:0;left:0;height:100%;width:100%;position:fixed;background-color:white;z-index:999"></div>
<div class="gdpr-cookie-consent-app-container" id="gdpr-cookie-consent-settings-app">
	<!-- main preview container -->
		<!-- Banner preferences skins  -->
		<div v-if="show_cookie_as === 'banner'">
		<div v-show="banner_template == 'banner-default'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/default.php'; ?>
		</div>
		<div v-show="banner_template == 'banner-almond_column'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/banner_almond.php'; ?>
		</div>
		<div v-show="banner_template == 'banner-navy_blue_center'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/navy_blue_center.php'; ?>
		</div>
		<div v-show="banner_template == 'banner-grey_column'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/grey_column.php'; ?>
		</div>
		<div v-show="banner_template == 'banner-grey_center'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/grey_center.php'; ?>
		</div>
		<div v-show="banner_template == 'banner-dark_row'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/dark_row.php'; ?>
		</div>
		<div v-show="banner_template == 'banner-dark'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/dark.php'; ?>
		</div>
	</div>
	<!-- widget preferences skins  -->
	<div v-if="show_cookie_as === 'widget'">
		<div v-show="widget_template == 'widget-default'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/default.php'; ?>
		</div>
		<div v-show="widget_template == 'widget-almond_column'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/banner_almond.php'; ?>
		</div>
		<div v-show="widget_template == 'widget-navy_blue_center' || widget_template == 'widget-navy_blue_box'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/navy_blue_center.php'; ?>
		</div>
		<div v-show="widget_template == 'widget-navy_blue_square' ">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/navy_blue_square.php'; ?>
		</div>
		<div v-show="widget_template == 'widget-grey_column'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/grey_column.php'; ?>
		</div>
		<div v-show="widget_template == 'widget-grey_center'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/grey_center.php'; ?>
		</div>
		<div v-show="widget_template == 'widget-dark_row'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/dark_row.php'; ?>
		</div>
		<div v-show="widget_template == 'widget-dark'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/dark.php'; ?>
		</div>

	</div>
	<!-- popup preferences skins  -->
	<div v-if="show_cookie_as === 'popup'">
		<div v-show="popup_template == 'popup-default'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/default.php'; ?>
		</div>
		<div v-show="popup_template == 'popup-almond_column'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/banner_almond.php'; ?>
		</div>
		<div v-show="popup_template == 'popup-navy_blue_center' || popup_template == 'popup-navy_blue_box' ">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/navy_blue_center.php'; ?>
		</div>
		<div v-show="popup_template == 'popup-navy_blue_square' ">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/navy_blue_square.php'; ?>
		</div>
		<div v-show="popup_template == 'popup-grey_column'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/grey_column.php'; ?>
		</div>
		<div v-show="popup_template == 'popup-grey_center'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/grey_center.php'; ?>
		</div>
		<div v-show="popup_template == 'popup-dark_row'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/dark_row.php'; ?>
		</div>
		<div v-show="popup_template == 'popup-dark'">
		<?php require plugin_dir_path( __FILE__ ) . 'templates/skin/dark.php'; ?>
		</div>

	</div>

	<!-- adding divs conditionally for popup preview -->
	
	<div v-if="show_cookie_as === 'popup' && !ab_testing_enabled" class="banner-preview-modal gdprfade" :class="{'hide-popup':!banner_preview_is_on , 'overlay':cookie_add_overlay}">
				<div class="banner-preview-modal-dialog banner-preview-modal-dialog-centered">
					<!-- Modal content for Popup Preview-->
					<div class="banner-preview-modal-content">
						<div class="banner-preview-modal-body">

							<div v-show="banner_preview_is_on" id="banner-preview-main-container" class="banner-preview-main-container" :class="{ 'banner-top': cookie_position == 'top' && show_cookie_as == 'banner' ,'banner-bottom': cookie_position != 'top' && show_cookie_as == 'banner', 'banner-preview': show_cookie_as == 'banner','widget-preview': show_cookie_as == 'widget','widget-left': cookie_widget_position == 'left' && show_cookie_as == 'widget','widget-right': cookie_widget_position != 'left' && show_cookie_as == 'widget','popup-preview': show_cookie_as == 'popup','popup-almond_column_preview':popup_template == 'popup-almond_column' && show_cookie_as == 'popup','popup-default_preview':popup_template == 'popup-default' && show_cookie_as == 'popup','popup-navy_blue_center_preview':popup_template == 'popup-navy_blue_center' && show_cookie_as == 'popup','popup-grey_column_preview':popup_template == 'popup-grey_column' && show_cookie_as == 'popup' && gdpr_policy != 'ccpa','popup-dark_row_preview':popup_template == 'popup-dark_row' && show_cookie_as == 'popup','popup-grey_center_preview':popup_template == 'popup-grey_center' && show_cookie_as == 'popup','popup-dark_preview':popup_template == 'popup-dark' && show_cookie_as == 'popup','popup-navy_blue_box_preview':popup_template == 'popup-navy_blue_box' && show_cookie_as == 'popup','popup-navy_blue_square_preview':popup_template == 'popup-navy_blue_square' && show_cookie_as == 'popup' }" :style="{ 'background-color': `${cookie_bar_color}${Math.floor(cookie_bar_opacity * 255).toString(16).toUpperCase()}`,'border-style': border_style, 'border-width': cookie_bar_border_width + 'px', 'border-color': cookie_border_color, 'border-radius': cookie_bar_border_radius+'px', color:cookie_text_color}" >

							<div class="gdpr_messagebar_content_preview" :class="{ 'widget-msg-content-preview': show_cookie_as == 'widget','banner-msg-content-preview': show_cookie_as == 'banner'}" style="max-width: 825px;">

									<!-- logo image for popup -->
									<div v-show="!['popup-grey_column','popup-almond_column'].includes(popup_template)" class="gdpr_logo_container">
									<?php
										$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
									if ( ! empty( $get_banner_img ) ) {
										?>
										<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
											<?php
									}
									?>
									</div>

									<div class="group-description-preview" tabindex="0" :class="{'ccpa-group-description': is_ccpa && gdpr_policy != 'both' }">
										<!-- logo image for popup -->
										<div v-show="['popup-grey_column','popup-almond_column'].includes(popup_template)" class="gdpr_logo_container">
										<?php
											$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
										if ( ! empty( $get_banner_img ) ) {
											?>
											<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
												<?php
										}
										?>
										</div>

										<h3 v-show="is_gdpr && !is_ccpa" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font , 'color': 'inherit'
											}">{{gdpr_message_heading}}</h3>
										<h3 v-show="is_lgpd" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font , 'color': 'inherit' }">{{lgpd_message_heading}}</h3>
										<h3 v-show="is_gdpr && is_ccpa" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font , 'color': 'inherit' }">{{gdpr_message_heading}}</h3>
										<p v-show="is_gdpr" v-html="gdpr_message" class="gdpr_preview" :style="{ fontFamily: cookie_font }">
										
										</p>
										<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
										<p v-show="is_lgpd" class="gdpr_preview" :style="{ fontFamily: cookie_font }">{{lgpd_message}}
										<br v-show="popup_template == 'popup-almond_column' && show_cookie_as == 'popup'">
										<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
										</p>
										<p v-show="is_eprivacy" class="gdpr_preview" :style="{ fontFamily: cookie_font }">{{eprivacy_message}}
										<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
										</p>
										<p v-show="is_ccpa" class="ccpa_preview_msg" :class="{'ccpa-center-text':show_cookie_as == 'banner' && gdpr_policy != 'both' }" :style="{ fontFamily: cookie_font }" >{{ccpa_message}} <a href="#" class="ccpa_link_button_preview" :style="{'color':opt_out_text_color,fontFamily: cookie_font}">{{opt_out_text}}</a>
										</p>
									</div>
									<div v-show="['gdpr','eprivacy','both','lgpd'].includes(gdpr_policy)" class="group-description-buttons-preview" id="default_buttons_preview"
										:class=" {'widget-button-container':
										((cookie_accept_all_on && cookie_settings_on) ||
										(cookie_accept_on && cookie_accept_all_on) ||
										(cookie_accept_all_on && cookie_decline_on) ||
										(cookie_accept_all_on && cookie_decline_on && cookie_settings_on) ||
										(cookie_accept_all_on && cookie_decline_on && cookie_settings_on && cookie_accept_on) ||
										(cookie_accept_all_on && !cookie_accept_on && !cookie_decline_on && !cookie_settings_on)) && show_cookie_as == 'popup' && popup_template != 'popup-dark_row' && popup_template != 'popup-grey_center' && popup_template != 'popup-navy_blue_square','eprivay-remove-flex': gdpr_policy == 'eprivacy','popup-navy_blue_box_flex':popup_template == 'popup-navy_blue_box' && show_cookie_as == 'popup' && gdpr_policy != 'ccpa'  }"
										:style=" { 'margin-bottom': ( 2*accept_border_width ) + 'px' } " >
											<!-- accept button preview configuration  -->
											<a v-show="cookie_accept_on && popup_template != 'popup-navy_blue_square'" id="cookie_action_accept_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_as_button,'button-as-link':!accept_as_button,  'btn-lg': accept_as_button && accept_size === 'large','btn-sm': accept_as_button && accept_size === 'small','widget-accept-preview': show_cookie_as == 'widget','popup-accept-preview': show_cookie_as == 'popup' }" aria-label="Accept" href="#":style="{ color:accept_text_color,'border-style': accept_style, 'border-width': accept_as_button ? accept_border_width + 'px':'0', 'border-color': accept_border_color, 'border-radius': accept_border_radius+'px','background-color': accept_as_button ? `${accept_background_color}${Math.floor(accept_opacity * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font
											}" >{{ accept_text }}</a>
											<!-- accept all button preview configuration  -->
											<a v-show="cookie_accept_all_on && popup_template != 'popup-navy_blue_square'" id="cookie_action_accept_all_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_all_as_button,'button-as-link':!accept_all_as_button,  'btn-lg': accept_all_as_button && accept_all_size === 'large','btn-sm': accept_all_as_button && accept_all_size === 'small','widget-accept-all-preview': show_cookie_as == 'widget','popup-accept-all-preview': show_cookie_as == 'popup' }" aria-label="Accept All" href="#" :style="{ color:accept_all_text_color,'border-style': accept_all_style, 'border-width': accept_all_as_button ? accept_all_border_width + 'px':'0', 'border-color': accept_all_border_color, 'border-radius': accept_all_border_radius+'px','background-color': accept_all_as_button ? `${accept_all_background_color}${Math.floor(accept_all_opacity * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font
											}"  >{{ accept_all_text }}</a>

											<!-- Decline button preview configuration  -->
											<a v-show="cookie_decline_on && popup_template != 'popup-navy_blue_square'" id="cookie_action_reject_preview" class="gdpr_action_button_preview btn" :class="{ 'btn': decline_as_button,'button-as-link':!decline_as_button,  'btn-lg': decline_as_button && decline_size === 'large','btn-sm': decline_as_button && decline_size === 'small','widget-decline-preview': show_cookie_as == 'widget','popup-decline-preview': show_cookie_as == 'popup' }" aria-label="Reject"
											:style="{ color:decline_text_color,'border-style': decline_style, 'border-width': decline_as_button ? decline_border_width + 'px':'0', 'border-color': decline_border_color, 'border-radius': decline_border_radius+'px','background-color': decline_as_button ? `${decline_background_color}${Math.floor(decline_opacity * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font
											}" >{{ decline_text }}</a>

											<!-- Setting button preview configuration  -->
											<a v-show="cookie_settings_on && popup_template != 'popup-navy_blue_square'" id="cookie_action_settings_preview" class="gpdr_cookie_settings_btn gdpr_action_button_preview btn" :class="{ 'btn': settings_as_button,'button-as-link':!settings_as_button,'btn-lg': settings_as_button && settings_size === 'large','btn-sm': settings_as_button && settings_size === 'small','widget-settings-preview': show_cookie_as == 'widget','widget-cookie-setting-container': (cookie_settings_on && !cookie_accept_all_on && !cookie_accept_on && !cookie_decline_on),'popup-setting-preview': show_cookie_as == 'popup', 'eprivay-hide-setting': gdpr_policy == 'eprivacy' }"   aria-label="Cookie Settings" href="#" :style="{ color:settings_text_color,'border-style': settings_style, 'border-width': settings_as_button ? settings_border_width + 'px':'0', 'border-color': settings_border_color, 'border-radius': settings_border_radius+'px','background-color': settings_as_button ? `${settings_background_color}${Math.floor(settings_opacity * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font
											}" >{{ settings_text }}</a>

											<!-- buttons for navy blue square Pro template  -->
											<!-- Decline button -->
											<a v-show="cookie_decline_on && popup_template == 'popup-navy_blue_square'" id="cookie_action_reject_preview" class="gdpr_action_button_preview btn" :class="{ 'btn': decline_as_button,'button-as-link':!decline_as_button,  'btn-lg': decline_as_button && decline_size === 'large','btn-sm': decline_as_button && decline_size === 'small','widget-decline-preview': show_cookie_as == 'widget','popup-decline-preview': show_cookie_as == 'popup' }" aria-label="Reject"
											:style="{ color:decline_text_color,'border-style': decline_style, 'border-width': decline_as_button ? decline_border_width + 'px':'0', 'border-color': decline_border_color, 'border-radius': decline_border_radius+'px','background-color': decline_as_button ? `${decline_background_color}${Math.floor(decline_opacity * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font
											}" >{{ decline_text }}</a>
											<!-- Setting button -->
											<a v-show="cookie_settings_on && popup_template == 'popup-navy_blue_square'" id="cookie_action_settings_preview" class="gpdr_cookie_settings_btn gdpr_action_button_preview btn" :class="{ 'btn': settings_as_button,'button-as-link':!settings_as_button,'btn-lg': settings_as_button && settings_size === 'large','btn-sm': settings_as_button && settings_size === 'small','widget-settings-preview': show_cookie_as == 'widget','widget-cookie-setting-container': (cookie_settings_on && !cookie_accept_all_on && !cookie_accept_on && !cookie_decline_on),'popup-setting-preview': show_cookie_as == 'popup', 'eprivay-hide-setting': gdpr_policy == 'eprivacy' }"   aria-label="Cookie Settings" href="#" :style="{ color:settings_text_color,'border-style': settings_style, 'border-width': settings_as_button ? settings_border_width + 'px':'0', 'border-color': settings_border_color, 'border-radius': settings_border_radius+'px','background-color': settings_as_button ? `${settings_background_color}${Math.floor(settings_opacity * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font
											}" >{{ settings_text }}</a>
											<!-- accept button   -->
											<a v-show="cookie_accept_on && popup_template == 'popup-navy_blue_square'" id="cookie_action_accept_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_as_button,'button-as-link':!accept_as_button,  'btn-lg': accept_as_button && accept_size === 'large','btn-sm': accept_as_button && accept_size === 'small','widget-accept-preview': show_cookie_as == 'widget','popup-accept-preview': show_cookie_as == 'popup' }" aria-label="Accept" href="#":style="{ color:accept_text_color,'border-style': accept_style, 'border-width': accept_as_button ? accept_border_width + 'px':'0', 'border-color': accept_border_color, 'border-radius': accept_border_radius+'px','background-color': accept_as_button ? `${accept_background_color}${Math.floor(accept_opacity * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font
											}" >{{ accept_text }}</a>

											<!-- accept all  -->
											<a v-show="cookie_accept_all_on && popup_template == 'popup-navy_blue_square'" id="cookie_action_accept_all_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_all_as_button,'button-as-link':!accept_all_as_button,  'btn-lg': accept_all_as_button && accept_all_size === 'large','btn-sm': accept_all_as_button && accept_all_size === 'small','widget-accept-all-preview': show_cookie_as == 'widget','popup-accept-all-preview': show_cookie_as == 'popup' }" aria-label="Accept All" href="#" :style="{ color:accept_all_text_color,'border-style': accept_all_style, 'border-width': accept_all_as_button ? accept_all_border_width + 'px':'0', 'border-color': accept_all_border_color, 'border-radius': accept_all_border_radius+'px','background-color': accept_all_as_button ? `${accept_all_background_color}${Math.floor(accept_all_opacity * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font
											}"  >{{ accept_all_text }}</a>
									</div>
							</div>
							</div>

						</div>
					</div>
				</div>
	</div>
		<!-- for banner and widget preview -->
	<div v-else v-show="banner_preview_is_on && !ab_testing_enabled" id="banner-preview-main-container" class="banner-preview-main-container" :class="{ 'banner-top': cookie_position == 'top' && show_cookie_as == 'banner' ,'banner-bottom': cookie_position != 'top' && show_cookie_as == 'banner', 'banner-preview': show_cookie_as == 'banner','widget-preview': show_cookie_as == 'widget','widget-left': cookie_widget_position == 'left' && show_cookie_as == 'widget','widget-right': cookie_widget_position == 'right' && show_cookie_as == 'widget','widget-top-left': cookie_widget_position == 'top_left' && show_cookie_as == 'widget','widget-top-right': cookie_widget_position == 'top_right' && show_cookie_as == 'widget','popup-preview': show_cookie_as == 'popup','banner-default_preview':banner_template == 'banner-default' && show_cookie_as == 'banner','widget-default_preview':widget_template == 'widget-default' && show_cookie_as == 'widget','banner-almond_column_preview':banner_template == 'banner-almond_column' && show_cookie_as == 'banner','widget-almond_column_preview':widget_template == 'widget-almond_column' && show_cookie_as == 'widget' && gdpr_policy != 'ccpa','banner-navy_blue_center_preview':banner_template == 'banner-navy_blue_center' && show_cookie_as == 'banner','widget-navy_blue_center_preview':widget_template == 'widget-navy_blue_center' && show_cookie_as == 'widget','banner-grey_column_preview':banner_template == 'banner-grey_column' && show_cookie_as == 'banner','widget-grey_column_preview':widget_template == 'widget-grey_column' && show_cookie_as == 'widget' && gdpr_policy != 'ccpa','banner-dark_row_preview':banner_template == 'banner-dark_row' && show_cookie_as == 'banner','widget-dark_row_preview':widget_template == 'widget-dark_row' && show_cookie_as == 'widget','banner-grey_center_preview':banner_template == 'banner-grey_center' && show_cookie_as == 'banner','widget-grey_center_preview':widget_template == 'widget-grey_center' && show_cookie_as == 'widget','banner-dark_preview':banner_template == 'banner-dark' && show_cookie_as == 'banner','widget-dark_preview':widget_template == 'widget-dark' && show_cookie_as == 'widget','widget-navy_blue_box_preview':widget_template == 'widget-navy_blue_box' && show_cookie_as == 'widget','widget-navy_blue_square_preview':widget_template == 'widget-navy_blue_square' && show_cookie_as == 'widget' }" :style="{ 'background-color': `${cookie_bar_color}${Math.floor(cookie_bar_opacity * 255).toString(16).toUpperCase()}`,'border-style': border_style, 'border-width': cookie_bar_border_width + 'px', 'border-color': cookie_border_color, 'border-radius': cookie_bar_border_radius+'px', color:cookie_text_color}" name="gcc-banner-preview">

			<div class="gdpr_messagebar_content_preview" :class="{ 'widget-msg-content-preview': show_cookie_as == 'widget','banner-msg-content-preview': show_cookie_as == 'banner'}" style="max-width: 825px;">
					<!-- logo image for banner and widget  -->
					<div v-show="!['banner-grey_column','banner-almond_column','widget-grey_column','widget-almond_column'].includes(banner_template)" class="gdpr_logo_container">
					<?php
						$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
					if ( ! empty( $get_banner_img ) ) {
						?>
						<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
							<?php
					}
					?>
					</div>

					<div class="group-description-preview" tabindex="0" :class="{'ccpa-group-description': is_ccpa && gdpr_policy != 'both' }">
						<div v-show="['banner-grey_column','banner-almond_column','widget-grey_column','widget-almond_column'].includes(banner_template)" class="gdpr_logo_container">
						<?php
							$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
						if ( ! empty( $get_banner_img ) ) {
							?>
							<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
								<?php
						}
						?>
						</div>

						<h3 v-show="is_gdpr && !is_ccpa" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font , 'color': 'inherit'}">{{gdpr_message_heading}}</h3>
						<h3 v-show="is_lgpd" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font , 'color': 'inherit' }">{{lgpd_message_heading}}</h3>
						<h3 v-show="is_gdpr && is_ccpa" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font , 'color': 'inherit'}">{{gdpr_message_heading}}</h3>

						<p v-show="is_gdpr && iabtcf_is_on"  v-html="gdpr_message" class="gdpr_preview" :style="{ fontFamily: cookie_font }">
							<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
						</p>
						<p v-show="is_gdpr" class="gdpr_preview" :style="{ fontFamily: cookie_font }">{{gdpr_message}}
							<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
						</p>
						<p v-show="is_lgpd" class="gdpr_preview" :style="{ fontFamily: cookie_font }">{{lgpd_message}}<br v-show="widget_template == 'widget-almond_column' && show_cookie_as == 'widget' ">
						<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
						</p>
						<p v-show="is_eprivacy" class="gdpr_preview" :style="{ fontFamily: cookie_font }">{{eprivacy_message}}
						<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
						</p>
						<p v-show="is_ccpa" class="ccpa_preview_msg" :class="{'ccpa-center-text':show_cookie_as == 'banner' && gdpr_policy != 'both' }" :style="{ fontFamily: cookie_font }">{{ccpa_message}} <a href="#" class="ccpa_link_button_preview" :style="{'color':opt_out_text_color,fontFamily: cookie_font}">{{opt_out_text}}</a>
						</p>
					</div>
					<div v-show="['gdpr','eprivacy','both','lgpd'].includes(gdpr_policy)" class="group-description-buttons-preview" id="default_buttons_preview"
						:class=" {'widget-button-container':
						((cookie_accept_all_on && cookie_settings_on) ||
						(cookie_accept_on && cookie_accept_all_on) ||
						(cookie_accept_all_on && cookie_decline_on) ||
						(cookie_accept_all_on && cookie_decline_on && cookie_settings_on) ||
						(cookie_accept_all_on && cookie_decline_on && cookie_settings_on && cookie_accept_on) ||
						(cookie_accept_all_on && !cookie_accept_on && !cookie_decline_on && !cookie_settings_on)) && show_cookie_as == 'widget' && widget_template != 'widget-dark_row' && widget_template != 'widget-grey_center' && widget_template != 'widget-navy_blue_square','eprivay-remove-flex': gdpr_policy == 'eprivacy','widget-navy_blue_box_flex':widget_template == 'widget-navy_blue_box' && show_cookie_as == 'widget' && gdpr_policy != 'ccpa'  }"
						:style=" { 'margin-bottom': ( 2*accept_border_width ) + 'px' } " >
							<!-- accept button preview configuration  -->
							<a v-show="cookie_accept_on && widget_template != 'widget-navy_blue_square'" id="cookie_action_accept_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_as_button,'button-as-link':!accept_as_button,  'btn-lg': accept_as_button && accept_size === 'large','btn-sm': accept_as_button && accept_size === 'small','widget-accept-preview': show_cookie_as == 'widget' }" aria-label="Accept" href="#":style="{ color:accept_text_color,'border-style': accept_style, 'border-width': accept_as_button ? accept_border_width + 'px':'0', 'border-color': accept_border_color, 'border-radius': accept_border_radius+'px','background-color': accept_as_button ? `${accept_background_color}${Math.floor(accept_opacity * 255).toString(16).toUpperCase()}`:'transparent',fontFamily: cookie_font
							}" >{{ accept_text }}</a>

							<!-- accept all button preview configuration  -->
							<a v-show="cookie_accept_all_on && widget_template != 'widget-navy_blue_square'" id="cookie_action_accept_all_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_all_as_button,'button-as-link':!accept_all_as_button,  'btn-lg': accept_all_as_button && accept_all_size === 'large','btn-sm': accept_all_as_button && accept_all_size === 'small','widget-accept-all-preview': show_cookie_as == 'widget' }" aria-label="Accept All" href="#" :style="{ color:accept_all_text_color,'border-style': accept_all_style, 'border-width': accept_all_as_button ? accept_all_border_width + 'px':'0', 'border-color': accept_all_border_color, 'border-radius': accept_all_border_radius+'px','background-color': accept_all_as_button ? `${accept_all_background_color}${Math.floor(accept_all_opacity * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font
							}"  >{{ accept_all_text }}</a>

							<!-- Decline button preview configuration  -->
							<a v-show="cookie_decline_on && widget_template != 'widget-navy_blue_square'" id="cookie_action_reject_preview" class="gdpr_action_button_preview btn" :class="{ 'btn': decline_as_button,'button-as-link':!decline_as_button,  'btn-lg': decline_as_button && decline_size === 'large','btn-sm': decline_as_button && decline_size === 'small','widget-decline-preview': show_cookie_as == 'widget' }" aria-label="Reject"
							:style="{ color:decline_text_color,'border-style': decline_style, 'border-width': decline_as_button ? decline_border_width + 'px':'0', 'border-color': decline_border_color, 'border-radius': decline_border_radius+'px','background-color': decline_as_button ? `${decline_background_color}${Math.floor(decline_opacity * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font
							}"  >{{ decline_text }}</a>

							<!-- Setting button preview configuration  -->
							<a v-show="cookie_settings_on && widget_template != 'widget-navy_blue_square'" id="cookie_action_settings_preview" class="gpdr_cookie_settings_btn gdpr_action_button_preview btn" :class="{ 'btn': settings_as_button,'button-as-link':!settings_as_button,'btn-lg': settings_as_button && settings_size === 'large','btn-sm': settings_as_button && settings_size === 'small','widget-settings-preview': show_cookie_as == 'widget','widget-cookie-setting-container': (cookie_settings_on && !cookie_accept_all_on && !cookie_accept_on && !cookie_decline_on),'eprivay-hide-setting': gdpr_policy == 'eprivacy' }"   aria-label="Cookie Settings" href="#" :style="{ color:settings_text_color,'border-style': settings_style, 'border-width': settings_as_button ? settings_border_width + 'px':'0', 'border-color': settings_border_color, 'border-radius': settings_border_radius+'px','background-color': settings_as_button ? `${settings_background_color}${Math.floor(settings_opacity * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font
							}" >{{ settings_text }}</a>

							<!-- buttons for navy blue square Pro template  -->
							<!-- Decline button -->
							<a v-show="cookie_decline_on && widget_template == 'widget-navy_blue_square'" id="cookie_action_reject_preview" class="gdpr_action_button_preview btn" :class="{ 'btn': decline_as_button,'button-as-link':!decline_as_button,  'btn-lg': decline_as_button && decline_size === 'large','btn-sm': decline_as_button && decline_size === 'small','widget-decline-preview': show_cookie_as == 'widget' }" aria-label="Reject"
							:style="{ color:decline_text_color,'border-style': decline_style, 'border-width': decline_as_button ? decline_border_width + 'px':'0', 'border-color': decline_border_color, 'border-radius': decline_border_radius+'px','background-color': decline_as_button ? `${decline_background_color}${Math.floor(decline_opacity * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font
							}"  >{{ decline_text }}</a>
							<!-- Setting button -->
							<a v-show="cookie_settings_on && widget_template == 'widget-navy_blue_square'" id="cookie_action_settings_preview" class="gpdr_cookie_settings_btn gdpr_action_button_preview btn" :class="{ 'btn': settings_as_button,'button-as-link':!settings_as_button,'btn-lg': settings_as_button && settings_size === 'large','btn-sm': settings_as_button && settings_size === 'small','widget-settings-preview': show_cookie_as == 'widget','widget-cookie-setting-container': (cookie_settings_on && !cookie_accept_all_on && !cookie_accept_on && !cookie_decline_on),'eprivay-hide-setting': gdpr_policy == 'eprivacy' }"   aria-label="Cookie Settings" href="#" :style="{ color:settings_text_color,'border-style': settings_style, 'border-width': settings_as_button ? settings_border_width + 'px':'0', 'border-color': settings_border_color, 'border-radius': settings_border_radius+'px','background-color': settings_as_button ? `${settings_background_color}${Math.floor(settings_opacity * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font
							}" >{{ settings_text }}</a>
							<!-- accept button   -->
							<a v-show="cookie_accept_on && widget_template == 'widget-navy_blue_square'" id="cookie_action_accept_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_as_button,'button-as-link':!accept_as_button,  'btn-lg': accept_as_button && accept_size === 'large','btn-sm': accept_as_button && accept_size === 'small','widget-accept-preview': show_cookie_as == 'widget' }" aria-label="Accept" href="#":style="{ color:accept_text_color,'border-style': accept_style, 'border-width': accept_as_button ? accept_border_width + 'px':'0', 'border-color': accept_border_color, 'border-radius': accept_border_radius+'px','background-color': accept_as_button ? `${accept_background_color}${Math.floor(accept_opacity * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font
							}" >{{ accept_text }}</a>

							<!-- accept all  -->
							<a v-show="cookie_accept_all_on && widget_template == 'widget-navy_blue_square'" id="cookie_action_accept_all_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_all_as_button,'button-as-link':!accept_all_as_button,  'btn-lg': accept_all_as_button && accept_all_size === 'large','btn-sm': accept_all_as_button && accept_all_size === 'small','widget-accept-all-preview': show_cookie_as == 'widget' }" aria-label="Accept All" href="#" :style="{ color:accept_all_text_color,'border-style': accept_all_style, 'border-width': accept_all_as_button ? accept_all_border_width + 'px':'0', 'border-color': accept_all_border_color, 'border-radius': accept_all_border_radius+'px','background-color': accept_all_as_button ? `${accept_all_background_color}${Math.floor(accept_all_opacity * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font
							}"  >{{ accept_all_text }}</a>

					</div>
			</div>
	</div>
	
	<!-- adding divs conditionally for popup preview -->
	
	<div v-if="show_cookie_as === 'popup' && active_test_banner_tab === 1 && ab_testing_enabled" class="banner-preview-modal gdprfade" :class="{'hide-popup':!banner_preview_is_on , 'overlay':cookie_add_overlay}">
				<div class="banner-preview-modal-dialog1 banner-preview-modal-dialog-centered">
					<!-- Modal content for Popup Preview-->
					<div class="banner-preview-modal-content1">
						<div class="banner-preview-modal-body">

							<div v-show="banner_preview_is_on" id="banner-preview-main-container1" class="banner-preview-main-container" :class="{ 'banner-top': cookie_position == 'top' && show_cookie_as == 'banner' ,'banner-bottom': cookie_position != 'top' && show_cookie_as == 'banner', 'banner-preview': show_cookie_as == 'banner','widget-preview': show_cookie_as == 'widget','widget-left': cookie_widget_position == 'left' && show_cookie_as == 'widget','widget-right': cookie_widget_position != 'left' && show_cookie_as == 'widget','popup-preview': show_cookie_as == 'popup','popup-almond_column_preview':popup_template == 'popup-almond_column' && show_cookie_as == 'popup','popup-default_preview':popup_template == 'popup-default' && show_cookie_as == 'popup','popup-navy_blue_center_preview':popup_template == 'popup-navy_blue_center' && show_cookie_as == 'popup','popup-grey_column_preview':popup_template == 'popup-grey_column' && show_cookie_as == 'popup' && gdpr_policy != 'ccpa','popup-dark_row_preview':popup_template == 'popup-dark_row' && show_cookie_as == 'popup','popup-grey_center_preview':popup_template == 'popup-grey_center' && show_cookie_as == 'popup','popup-dark_preview':popup_template == 'popup-dark' && show_cookie_as == 'popup','popup-navy_blue_box_preview':popup_template == 'popup-navy_blue_box' && show_cookie_as == 'popup','popup-navy_blue_square_preview':popup_template == 'popup-navy_blue_square' && show_cookie_as == 'popup' }" :style="{ 'background-color': `${cookie_bar_color1}${Math.floor(cookie_bar_opacity1 * 255).toString(16).toUpperCase()}`,'border-style': border_style1, 'border-width': cookie_bar_border_width1 + 'px', 'border-color': cookie_border_color1, 'border-radius': cookie_bar_border_radius1+'px', color:cookie_text_color1}" >

							<div class="gdpr_messagebar_content_preview" :class="{ 'widget-msg-content-preview': show_cookie_as == 'widget','banner-msg-content-preview': show_cookie_as == 'banner'}" style="max-width: 825px;">

									<!-- logo image for popup -->
									<div v-show="!['popup-grey_column','popup-almond_column'].includes(popup_template)" class="gdpr_logo_container">
									<?php
										$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
									if ( ! empty( $get_banner_img ) ) {
										?>
										<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
											<?php
									}
									?>
									</div>

									<div class="group-description-preview" tabindex="0" :class="{'ccpa-group-description': is_ccpa && gdpr_policy != 'both' }">
										<!-- logo image for popup -->
										<div v-show="['popup-grey_column','popup-almond_column'].includes(popup_template)" class="gdpr_logo_container">
										<?php
											$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
										if ( ! empty( $get_banner_img ) ) {
											?>
											<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
												<?php
										}
										?>
										</div>

										<h3 v-show="is_gdpr && !is_ccpa" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font1 , 'color': 'inherit'
											}">{{gdpr_message_heading}}</h3>
										<h3 v-show="is_lgpd" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font1 , 'color': 'inherit' }">{{lgpd_message_heading}}</h3>
										<h3 v-show="is_gdpr && is_ccpa" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font1 , 'color': 'inherit' }">{{gdpr_message_heading}}</h3>
										<p v-show="is_gdpr" class="gdpr_preview" v-html="gdpr_message" :style="{ fontFamily: cookie_font1 }">
										<br v-show="popup_template == 'popup-almond_column' && show_cookie_as == 'popup'">
										<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
										</p>
										<p v-show="is_lgpd" class="gdpr_preview" :style="{ fontFamily: cookie_font }">{{lgpd_message}}
										<br v-show="popup_template == 'popup-almond_column' && show_cookie_as == 'popup'">
										<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
										</p>
										<p v-show="is_eprivacy" class="gdpr_preview" :style="{ fontFamily: cookie_font }">{{eprivacy_message}}
										<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
										</p>
										<p v-show="is_ccpa" class="ccpa_preview_msg" :class="{'ccpa-center-text':show_cookie_as == 'banner' && gdpr_policy != 'both' }" :style="{ fontFamily: cookie_font1 }" >{{ccpa_message}} <a href="#" class="ccpa_link_button_preview" :style="{'color':opt_out_text_color1,fontFamily: cookie_font1}">{{opt_out_text1}}</a>
										</p>
									</div>
									<div v-show="['gdpr','eprivacy','both','lgpd'].includes(gdpr_policy)" class="group-description-buttons-preview" id="default_buttons_preview"
										:class=" {'widget-button-container':
										((cookie_accept_all_on1 && cookie_settings_on1) ||
										(cookie_accept_on1 && cookie_accept_all_on1) ||
										(cookie_accept_all_on1 && cookie_decline_on1) ||
										(cookie_accept_all_on1 && cookie_decline_on1 && cookie_settings_on1) ||
										(cookie_accept_all_on1 && cookie_decline_on1 && cookie_settings_on1 && cookie_accept_on1) ||
										(cookie_accept_all_on1 && !cookie_accept_on1 && !cookie_decline_on1 && !cookie_settings_on1)) && show_cookie_as == 'popup' && popup_template != 'popup-dark_row' && popup_template != 'popup-grey_center' && popup_template != 'popup-navy_blue_square','eprivay-remove-flex': gdpr_policy == 'eprivacy','popup-navy_blue_box_flex':popup_template == 'popup-navy_blue_box' && show_cookie_as == 'popup' && gdpr_policy != 'ccpa'  }"
										:style=" { 'margin-bottom': ( 2*accept_border_width ) + 'px' } " >
											<!-- accept button preview configuration  -->
											<a v-show="cookie_accept_on1 && popup_template != 'popup-navy_blue_square'" id="cookie_action_accept_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_as_button1,'button-as-link':!accept_as_button1,  'btn-lg': accept_as_button1 && accept_size1 === 'large','btn-sm': accept_as_button1 && accept_size1 === 'small','widget-accept-preview': show_cookie_as == 'widget','popup-accept-preview': show_cookie_as == 'popup' }" aria-label="Accept" href="#":style="{ color:accept_text_color1,'border-style': accept_style1, 'border-width': accept_as_button1 ? accept_border_width1 + 'px':'0', 'border-color': accept_border_color1, 'border-radius': accept_border_radius1+'px','background-color': accept_as_button1 ? `${accept_background_color1}${Math.floor(accept_opacity1 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font1
											}" >{{ accept_text1 }}</a>
											<!-- accept all button preview configuration  -->
											<a v-show="cookie_accept_all_on1 && popup_template != 'popup-navy_blue_square'" id="cookie_action_accept_all_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_all_as_button1,'button-as-link':!accept_all_as_button1,  'btn-lg': accept_all_as_button1 && accept_all_size1 === 'large','btn-sm': accept_all_as_button1 && accept_all_size1 === 'small','widget-accept-all-preview': show_cookie_as == 'widget','popup-accept-all-preview': show_cookie_as == 'popup' }" aria-label="Accept All" href="#" :style="{ color:accept_all_text_color1,'border-style': accept_all_style1, 'border-width': accept_all_as_button1 ? accept_all_border_width1 + 'px':'0', 'border-color': accept_all_border_color1, 'border-radius': accept_all_border_radius1+'px','background-color': accept_all_as_button1 ? `${accept_all_background_color1}${Math.floor(accept_all_opacity1 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font1
											}"  >{{ accept_all_text1 }}</a>

											<!-- Decline button preview configuration  -->
											<a v-show="cookie_decline_on1 && popup_template != 'popup-navy_blue_square'" id="cookie_action_reject_preview" class="gdpr_action_button_preview btn" :class="{ 'btn': decline_as_button1,'button-as-link':!decline_as_button1,  'btn-lg': decline_as_button1 && decline_size1 === 'large','btn-sm': decline_as_button1 && decline_size1 === 'small','widget-decline-preview': show_cookie_as == 'widget','popup-decline-preview': show_cookie_as == 'popup' }" aria-label="Reject"
											:style="{ color:decline_text_color1,'border-style': decline_style1, 'border-width': decline_as_button1 ? decline_border_width1 + 'px':'0', 'border-color': decline_border_color1, 'border-radius': decline_border_radius1+'px','background-color': decline_as_button1 ? `${decline_background_color1}${Math.floor(decline_opacity1 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font1
											}" >{{ decline_text1 }}</a>

											<!-- Setting button preview configuration  -->
											<a v-show="cookie_settings_on1 && popup_template != 'popup-navy_blue_square'" id="cookie_action_settings_preview" class="gpdr_cookie_settings_btn1 gdpr_action_button_preview btn" :class="{ 'btn': settings_as_button1,'button-as-link':!settings_as_button1,'btn-lg': settings_as_button1 && settings_size1 === 'large','btn-sm': settings_as_button1 && settings_size1 === 'small','widget-settings-preview': show_cookie_as == 'widget','widget-cookie-setting-container': (cookie_settings_on && !cookie_accept_all_on && !cookie_accept_on && !cookie_decline_on),'popup-setting-preview': show_cookie_as == 'popup', 'eprivay-hide-setting': gdpr_policy == 'eprivacy' }"   aria-label="Cookie Settings" href="#" :style="{ color:settings_text_color1,'border-style': settings_style1, 'border-width': settings_as_button1 ? settings_border_width1 + 'px':'0', 'border-color': settings_border_color1, 'border-radius': settings_border_radius1+'px','background-color': settings_as_button1 ? `${settings_background_color1}${Math.floor(settings_opacity1 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font1
											}" >{{ settings_text1 }}</a>

											<!-- buttons for navy blue square Pro template  -->
											<!-- Decline button -->
											<a v-show="cookie_decline_on1 && popup_template == 'popup-navy_blue_square'" id="cookie_action_reject_preview" class="gdpr_action_button_preview btn" :class="{ 'btn': decline_as_button1,'button-as-link':!decline_as_button1,  'btn-lg': decline_as_button1 && decline_size1 === 'large','btn-sm': decline_as_button1 && decline_size1 === 'small','widget-decline-preview': show_cookie_as == 'widget','popup-decline-preview': show_cookie_as == 'popup' }" aria-label="Reject"
											:style="{ color:decline_text_color1,'border-style': decline_style1, 'border-width': decline_as_button1 ? decline_border_width1 + 'px':'0', 'border-color': decline_border_color1, 'border-radius': decline_border_radius1+'px','background-color': decline_as_button1 ? `${decline_background_color1}${Math.floor(decline_opacity1 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font1
											}" >{{ decline_text1 }}</a>
											<!-- Setting button -->
											<a v-show="cookie_settings_on1 && popup_template == 'popup-navy_blue_square'" id="cookie_action_settings_preview" class="gpdr_cookie_settings_btn1 gdpr_action_button_preview btn" :class="{ 'btn': settings_as_button1,'button-as-link':!settings_as_button1,'btn-lg': settings_as_button1 && settings_size1 === 'large','btn-sm': settings_as_button1 && settings_size1 === 'small','widget-settings-preview': show_cookie_as == 'widget','widget-cookie-setting-container': (cookie_settings_on && !cookie_accept_all_on && !cookie_accept_on && !cookie_decline_on),'popup-setting-preview': show_cookie_as == 'popup', 'eprivay-hide-setting': gdpr_policy == 'eprivacy' }"   aria-label="Cookie Settings" href="#" :style="{ color:settings_text_color1,'border-style': settings_style1, 'border-width': settings_as_button1 ? settings_border_width1 + 'px':'0', 'border-color': settings_border_color1, 'border-radius': settings_border_radius1+'px','background-color': settings_as_button1 ? `${settings_background_color1}${Math.floor(settings_opacity1 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font1
											}" >{{ settings_text1 }}</a>
											<!-- accept button   -->
											<a v-show="cookie_accept_on1 && popup_template == 'popup-navy_blue_square'" id="cookie_action_accept_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_as_button1,'button-as-link':!accept_as_button1,  'btn-lg': accept_as_button1 && accept_size1 === 'large','btn-sm': accept_as_button1 && accept_size1 === 'small','widget-accept-preview': show_cookie_as == 'widget','popup-accept-preview': show_cookie_as == 'popup' }" aria-label="Accept" href="#":style="{ color:accept_text_color1,'border-style': accept_style1, 'border-width': accept_as_button1 ? accept_border_width1 + 'px':'0', 'border-color': accept_border_color1, 'border-radius': accept_border_radius1+'px','background-color': accept_as_button1 ? `${accept_background_color1}${Math.floor(accept_opacity1 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font1
											}" >{{ accept_text1 }}</a>

											<!-- accept all  -->
											<a v-show="cookie_accept_all_on1 && popup_template == 'popup-navy_blue_square'" id="cookie_action_accept_all_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_all_as_button1,'button-as-link':!accept_all_as_button1, 'btn-lg': accept_all_as_button1 && accept_all_size1 === 'large','btn-sm': accept_all_as_button1 && accept_all_size1 === 'small','widget-accept-all-preview': show_cookie_as == 'widget','popup-accept-all-preview': show_cookie_as == 'popup' }" aria-label="Accept All" href="#" :style="{ color:accept_all_text_color1,'border-style': accept_all_style1, 'border-width': accept_all_as_button1 ? accept_all_border_width1 + 'px':'0', 'border-color': accept_all_border_color1, 'border-radius': accept_all_border_radius1+'px','background-color': accept_all_as_button1 ? `${accept_all_background_color1}${Math.floor(accept_all_opacity1 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font1
											}"  >{{ accept_all_text1 }}</a>
									</div>
							</div>
							</div>

						</div>
					</div>
				</div>
	</div>
	<!-- for banner and widget preview -->
	<div v-else v-show="banner_preview_is_on && active_test_banner_tab === 1 && ab_testing_enabled" id="banner-preview-main-container1" class="banner-preview-main-container" :class="{ 'banner-top': cookie_position == 'top' && show_cookie_as == 'banner' ,'banner-bottom': cookie_position != 'top' && show_cookie_as == 'banner', 'banner-preview': show_cookie_as == 'banner','widget-preview': show_cookie_as == 'widget','widget-left': cookie_widget_position == 'left' && show_cookie_as == 'widget','widget-right': cookie_widget_position == 'right' && show_cookie_as == 'widget','widget-top-left': cookie_widget_position == 'top_left' && show_cookie_as == 'widget','widget-top-right': cookie_widget_position == 'top_right' && show_cookie_as == 'widget','popup-preview': show_cookie_as == 'popup','banner-default_preview':banner_template == 'banner-default' && show_cookie_as == 'banner','widget-default_preview':widget_template == 'widget-default' && show_cookie_as == 'widget','banner-almond_column_preview':banner_template == 'banner-almond_column' && show_cookie_as == 'banner','widget-almond_column_preview':widget_template == 'widget-almond_column' && show_cookie_as == 'widget' && gdpr_policy != 'ccpa','banner-navy_blue_center_preview':banner_template == 'banner-navy_blue_center' && show_cookie_as == 'banner','widget-navy_blue_center_preview':widget_template == 'widget-navy_blue_center' && show_cookie_as == 'widget','banner-grey_column_preview':banner_template == 'banner-grey_column' && show_cookie_as == 'banner','widget-grey_column_preview':widget_template == 'widget-grey_column' && show_cookie_as == 'widget' && gdpr_policy != 'ccpa','banner-dark_row_preview':banner_template == 'banner-dark_row' && show_cookie_as == 'banner','widget-dark_row_preview':widget_template == 'widget-dark_row' && show_cookie_as == 'widget','banner-grey_center_preview':banner_template == 'banner-grey_center' && show_cookie_as == 'banner','widget-grey_center_preview':widget_template == 'widget-grey_center' && show_cookie_as == 'widget','banner-dark_preview':banner_template == 'banner-dark' && show_cookie_as == 'banner','widget-dark_preview':widget_template == 'widget-dark' && show_cookie_as == 'widget','widget-navy_blue_box_preview':widget_template == 'widget-navy_blue_box' && show_cookie_as == 'widget','widget-navy_blue_square_preview':widget_template == 'widget-navy_blue_square' && show_cookie_as == 'widget' }" :style="{ 'background-color': `${cookie_bar_color1}${Math.floor(cookie_bar_opacity1 * 255).toString(16).toUpperCase()}`,'border-style': border_style1, 'border-width': cookie_bar_border_width1 + 'px', 'border-color': cookie_border_color1, 'border-radius': cookie_bar_border_radius1+'px', color:cookie_text_color1}" name="gcc-banner-preview">

			<div class="gdpr_messagebar_content_preview" :class="{ 'widget-msg-content-preview': show_cookie_as == 'widget','banner-msg-content-preview': show_cookie_as == 'banner'}" style="max-width: 825px;">
					<!-- logo image for banner and widget  -->
					<div v-show="!['banner-grey_column','banner-almond_column','widget-grey_column','widget-almond_column'].includes(banner_template)" class="gdpr_logo_container">
					<?php
						$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
					if ( ! empty( $get_banner_img ) ) {
						?>
						<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
							<?php
					}
					?>
					</div>

					<div class="group-description-preview" tabindex="0" :class="{'ccpa-group-description': is_ccpa && gdpr_policy != 'both' }">
						<div v-show="['banner-grey_column','banner-almond_column','widget-grey_column','widget-almond_column'].includes(banner_template)" class="gdpr_logo_container">
						<?php
							$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
						if ( ! empty( $get_banner_img ) ) {
							?>
							<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
								<?php
						}
						?>
						</div>

						<h3 v-show="is_gdpr && !is_ccpa" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font1 , 'color': 'inherit'}">{{gdpr_message_heading}}</h3>
						<h3 v-show="is_lgpd" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font1 , 'color': 'inherit' }">{{lgpd_message_heading}}</h3>
						<h3 v-show="is_gdpr && is_ccpa" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font1 , 'color': 'inherit'}">{{gdpr_message_heading}}</h3>

						<p v-show="is_gdpr" class="gdpr_preview" v-html="gdpr_message" :style="{ fontFamily: cookie_font1 }"><br v-show="widget_template == 'widget-almond_column' && show_cookie_as == 'widget' ">
						<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font1,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
						</p>
						<p v-show="is_lgpd" class="gdpr_preview" :style="{ fontFamily: cookie_font1 }">{{lgpd_message}}<br v-show="widget_template == 'widget-almond_column' && show_cookie_as == 'widget' ">
						<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font1,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
						</p>
						<p v-show="is_eprivacy" class="gdpr_preview" :style="{ fontFamily: cookie_font1 }">{{eprivacy_message}}
						<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font1,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
						</p>
						<p v-show="is_ccpa" class="ccpa_preview_msg" :class="{'ccpa-center-text':show_cookie_as == 'banner' && gdpr_policy != 'both' }" :style="{ fontFamily: cookie_font1 }">{{ccpa_message}} <a href="#" class="ccpa_link_button_preview" :style="{'color':opt_out_text_color1,fontFamily: cookie_font1}">{{opt_out_text1}}</a>
						</p>
					</div>
					<div v-show="['gdpr','eprivacy','both','lgpd'].includes(gdpr_policy)" class="group-description-buttons-preview" id="default_buttons_preview"
						:class=" {'widget-button-container':
						((cookie_accept_all_on1 && cookie_settings_on1) ||
						(cookie_accept_on1 && cookie_accept_all_on1) ||
						(cookie_accept_all_on1 && cookie_decline_on1) ||
						(cookie_accept_all_on1 && cookie_decline_on1 && cookie_settings_on1) ||
						(cookie_accept_all_on1 && cookie_decline_on1 && cookie_settings_on1 && cookie_accept_on1) ||
						(cookie_accept_all_on1 && !cookie_accept_on1 && !cookie_decline_on1 && !cookie_settings_on1)) && show_cookie_as == 'widget' && widget_template != 'widget-dark_row' && widget_template != 'widget-grey_center' && widget_template != 'widget-navy_blue_square','eprivay-remove-flex': gdpr_policy == 'eprivacy','widget-navy_blue_box_flex':widget_template == 'widget-navy_blue_box' && show_cookie_as == 'widget' && gdpr_policy != 'ccpa'  }"
						:style=" { 'margin-bottom': ( 2*accept_border_width1 ) + 'px' } " >
							<!-- accept button preview configuration  -->
							<a v-show="cookie_accept_on1 && widget_template != 'widget-navy_blue_square'" id="cookie_action_accept_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_as_button1,'button-as-link':!accept_as_button1,  'btn-lg': accept_as_button1 && accept_size1 === 'large','btn-sm': accept_as_button1 && accept_size1 === 'small','widget-accept-preview': show_cookie_as == 'widget' }" aria-label="Accept" href="#":style="{ color:accept_text_color1,'border-style': accept_style1, 'border-width': accept_as_button1 ? accept_border_width1 + 'px':'0', 'border-color': accept_border_color1, 'border-radius': accept_border_radius1+'px','background-color': accept_as_button1 ? `${accept_background_color1}${Math.floor(accept_opacity1 * 255).toString(16).toUpperCase()}`:'transparent',fontFamily: cookie_font1
							}" >{{ accept_text1 }}</a>

							<!-- accept all button preview configuration  -->
							<a v-show="cookie_accept_all_on1 && widget_template != 'widget-navy_blue_square'" id="cookie_action_accept_all_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_all_as_button1,'button-as-link':!accept_all_as_button1,  'btn-lg': accept_all_as_button1 && accept_all_size1 === 'large','btn-sm': accept_all_as_button1 && accept_all_size1 === 'small','widget-accept-all-preview': show_cookie_as == 'widget' }" aria-label="Accept All" href="#" :style="{ color:accept_all_text_color1,'border-style': accept_all_style1, 'border-width': accept_all_as_button1 ? accept_all_border_width1 + 'px':'0', 'border-color': accept_all_border_color1, 'border-radius': accept_all_border_radius1+'px','background-color': accept_all_as_button1 ? `${accept_all_background_color1}${Math.floor(accept_all_opacity1 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font1
							}"  >{{ accept_all_text1 }}</a>

							<!-- Decline button preview configuration  -->
							<a v-show="cookie_decline_on1 && widget_template != 'widget-navy_blue_square'" id="cookie_action_reject_preview" class="gdpr_action_button_preview btn" :class="{ 'btn': decline_as_button1,'button-as-link':!decline_as_button1,  'btn-lg': decline_as_button1 && decline_size1 === 'large','btn-sm': decline_as_button1 && decline_size1 === 'small','widget-decline-preview': show_cookie_as == 'widget' }" aria-label="Reject"
							:style="{ color:decline_text_color1,'border-style': decline_style1, 'border-width': decline_as_button1 ? decline_border_width1 + 'px':'0', 'border-color': decline_border_color1, 'border-radius': decline_border_radius1+'px','background-color': decline_as_button1 ? `${decline_background_color1}${Math.floor(decline_opacity1 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font1
							}"  >{{ decline_text1 }}</a>

							<!-- Setting button preview configuration  -->
							<a v-show="cookie_settings_on1 && widget_template != 'widget-navy_blue_square'" id="cookie_action_settings_preview" class="gpdr_cookie_settings_btn1 gdpr_action_button_preview btn" :class="{ 'btn': settings_as_button1,'button-as-link':!settings_as_button1,'btn-lg': settings_as_button1 && settings_size1 === 'large','btn-sm': settings_as_button1 && settings_size1 === 'small','widget-settings-preview': show_cookie_as == 'widget','widget-cookie-setting-container': (cookie_settings_on && !cookie_accept_all_on && !cookie_accept_on && !cookie_decline_on),'eprivay-hide-setting': gdpr_policy == 'eprivacy' }"   aria-label="Cookie Settings" href="#" :style="{ color:settings_text_color1,'border-style': settings_style1, 'border-width': settings_as_button1 ? settings_border_width1 + 'px':'0', 'border-color': settings_border_color1, 'border-radius': settings_border_radius1+'px','background-color': settings_as_button1 ? `${settings_background_color1}${Math.floor(settings_opacity1 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font1
							}" >{{ settings_text1 }}</a>

							<!-- buttons for navy blue square Pro template  -->
							<!-- Decline button -->
							<a v-show="cookie_decline_on1 && widget_template == 'widget-navy_blue_square'" id="cookie_action_reject_preview" class="gdpr_action_button_preview btn" :class="{ 'btn': decline_as_button1,'button-as-link':!decline_as_button1,  'btn-lg': decline_as_button1 && decline_size1 === 'large','btn-sm': decline_as_button1 && decline_size1 === 'small','widget-decline-preview': show_cookie_as == 'widget' }" aria-label="Reject"
							:style="{ color:decline_text_color1,'border-style': decline_style1, 'border-width': decline_as_button1 ? decline_border_width1 + 'px':'0', 'border-color': decline_border_color1, 'border-radius': decline_border_radius1+'px','background-color': decline_as_button1 ? `${decline_background_color1}${Math.floor(decline_opacity1 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font1
							}"  >{{ decline_text1 }}</a>
							<!-- Setting button -->
							<a v-show="cookie_settings_on1 && widget_template == 'widget-navy_blue_square'" id="cookie_action_settings_preview" class="gpdr_cookie_settings_btn1 gdpr_action_button_preview btn" :class="{ 'btn': settings_as_button1,'button-as-link':!settings_as_button1,'btn-lg': settings_as_button1 && settings_size1 === 'large','btn-sm': settings_as_button1 && settings_size1 === 'small','widget-settings-preview': show_cookie_as == 'widget','widget-cookie-setting-container': (cookie_settings_on && !cookie_accept_all_on && !cookie_accept_on && !cookie_decline_on),'eprivay-hide-setting': gdpr_policy == 'eprivacy' }"   aria-label="Cookie Settings" href="#" :style="{ color:settings_text_color1,'border-style': settings_style1, 'border-width': settings_as_button1 ? settings_border_width1 + 'px':'0', 'border-color': settings_border_color1, 'border-radius': settings_border_radius1+'px','background-color': settings_as_button1 ? `${settings_background_color1}${Math.floor(settings_opacity1 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font1
							}" >{{ settings_text1 }}</a>
							<!-- accept button   -->
							<a v-show="cookie_accept_on1 && widget_template == 'widget-navy_blue_square'" id="cookie_action_accept_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_as_button1,'button-as-link':!accept_as_button1,  'btn-lg': accept_as_button1 && accept_size1 === 'large','btn-sm': accept_as_button1 && accept_size1 === 'small','widget-accept-preview': show_cookie_as == 'widget' }" aria-label="Accept" href="#":style="{ color:accept_text_color1,'border-style': accept_style1, 'border-width': accept_as_button1 ? accept_border_width1 + 'px':'0', 'border-color': accept_border_color1, 'border-radius': accept_border_radius1+'px','background-color': accept_as_button1 ? `${accept_background_color1}${Math.floor(accept_opacity1 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font1
							}" >{{ accept_text1 }}</a>

							<!-- accept all  -->
							<a v-show="cookie_accept_all_on1 && widget_template == 'widget-navy_blue_square'" id="cookie_action_accept_all_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_all_as_button1,'button-as-link':!accept_all_as_button1,  'btn-lg': accept_all_as_button1 && accept_all_size1 === 'large','btn-sm': accept_all_as_button1 && accept_all_size1 === 'small','widget-accept-all-preview': show_cookie_as == 'widget' }" aria-label="Accept All" href="#" :style="{ color:accept_all_text_color1,'border-style': accept_all_style1, 'border-width': accept_all_as_button1 ? accept_all_border_width1 + 'px':'0', 'border-color': accept_all_border_color1, 'border-radius': accept_all_border_radius1+'px','background-color': accept_all_as_button1 ? `${accept_all_background_color1}${Math.floor(accept_all_opacity1 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font1
							}"  >{{ accept_all_text1 }}</a>

					</div>
			</div>
	</div>

	<div v-if="show_cookie_as === 'popup' && active_test_banner_tab === 2 && ab_testing_enabled" class="banner-preview-modal gdprfade" :class="{'hide-popup':!banner_preview_is_on , 'overlay':cookie_add_overlay}">
				<div class="banner-preview-modal-dialog2 banner-preview-modal-dialog-centered">
					<!-- Modal content for Popup Preview-->
					<div class="banner-preview-modal-content2">
						<div class="banner-preview-modal-body">

							<div v-show="banner_preview_is_on" id="banner-preview-main-container2" class="banner-preview-main-container" :class="{ 'banner-top': cookie_position == 'top' && show_cookie_as == 'banner' ,'banner-bottom': cookie_position != 'top' && show_cookie_as == 'banner', 'banner-preview': show_cookie_as == 'banner','widget-preview': show_cookie_as == 'widget','widget-left': cookie_widget_position == 'left' && show_cookie_as == 'widget','widget-right': cookie_widget_position != 'left' && show_cookie_as == 'widget','popup-preview': show_cookie_as == 'popup','popup-almond_column_preview':popup_template == 'popup-almond_column' && show_cookie_as == 'popup','popup-default_preview':popup_template == 'popup-default' && show_cookie_as == 'popup','popup-navy_blue_center_preview':popup_template == 'popup-navy_blue_center' && show_cookie_as == 'popup','popup-grey_column_preview':popup_template == 'popup-grey_column' && show_cookie_as == 'popup' && gdpr_policy != 'ccpa','popup-dark_row_preview':popup_template == 'popup-dark_row' && show_cookie_as == 'popup','popup-grey_center_preview':popup_template == 'popup-grey_center' && show_cookie_as == 'popup','popup-dark_preview':popup_template == 'popup-dark' && show_cookie_as == 'popup','popup-navy_blue_box_preview':popup_template == 'popup-navy_blue_box' && show_cookie_as == 'popup','popup-navy_blue_square_preview':popup_template == 'popup-navy_blue_square' && show_cookie_as == 'popup' }" :style="{ 'background-color': `${cookie_bar_color2}${Math.floor(cookie_bar_opacity2 * 255).toString(16).toUpperCase()}`,'border-style': border_style2, 'border-width': cookie_bar_border_width2 + 'px', 'border-color': cookie_border_color2, 'border-radius': cookie_bar_border_radius2+'px', color:cookie_text_color2}" >

							<div class="gdpr_messagebar_content_preview" :class="{ 'widget-msg-content-preview': show_cookie_as == 'widget','banner-msg-content-preview': show_cookie_as == 'banner'}" style="max-width: 825px;">

									<!-- logo image for popup -->
									<div v-show="!['popup-grey_column','popup-almond_column'].includes(popup_template)" class="gdpr_logo_container">
									<?php
										$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
									if ( ! empty( $get_banner_img ) ) {
										?>
										<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
											<?php
									}
									?>
									</div>

									<div class="group-description-preview" tabindex="0" :class="{'ccpa-group-description': is_ccpa && gdpr_policy != 'both' }">
										<!-- logo image for popup -->
										<div v-show="['popup-grey_column','popup-almond_column'].includes(popup_template)" class="gdpr_logo_container">
										<?php
											$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
										if ( ! empty( $get_banner_img ) ) {
											?>
											<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
												<?php
										}
										?>
										</div>

										<h3 v-show="is_gdpr && !is_ccpa" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font2 , 'color': 'inherit'
											}">{{gdpr_message_heading}}</h3>
										<h3 v-show="is_lgpd" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font2 , 'color': 'inherit' }">{{lgpd_message_heading}}</h3>
										<h3 v-show="is_gdpr && is_ccpa" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font2 , 'color': 'inherit' }">{{gdpr_message_heading}}</h3>
										<p v-show="is_gdpr" class="gdpr_preview" v-html="gdpr_message" :style="{ fontFamily: cookie_font2 }">
										<br v-show="popup_template == 'popup-almond_column' && show_cookie_as == 'popup'">
										<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
										</p>
										<p v-show="is_lgpd" class="gdpr_preview" :style="{ fontFamily: cookie_font }">{{lgpd_message}}
										<br v-show="popup_template == 'popup-almond_column' && show_cookie_as == 'popup'">
										<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
										</p>
										<p v-show="is_eprivacy" class="gdpr_preview" :style="{ fontFamily: cookie_font }">{{eprivacy_message}}
										<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
										</p>
										<p v-show="is_ccpa" class="ccpa_preview_msg" :class="{'ccpa-center-text':show_cookie_as == 'banner' && gdpr_policy != 'both' }" :style="{ fontFamily: cookie_font2 }" >{{ccpa_message}} <a href="#" class="ccpa_link_button_preview" :style="{'color':opt_out_text_color2,fontFamily: cookie_font2}">{{opt_out_text2}}</a>
										</p>
									</div>
									<div v-show="['gdpr','eprivacy','both','lgpd'].includes(gdpr_policy)" class="group-description-buttons-preview" id="default_buttons_preview"
										:class=" {'widget-button-container':
										((cookie_accept_all_on2 && cookie_settings_on2) ||
										(cookie_accept_on2 && cookie_accept_all_on2) ||
										(cookie_accept_all_on2 && cookie_decline_on2) ||
										(cookie_accept_all_on2 && cookie_decline_on2 && cookie_settings_on2) ||
										(cookie_accept_all_on2 && cookie_decline_on2 && cookie_settings_on2 && cookie_accept_on2) ||
										(cookie_accept_all_on2 && !cookie_accept_on2 && !cookie_decline_on2 && !cookie_settings_on2)) && show_cookie_as == 'popup' && popup_template != 'popup-dark_row' && popup_template != 'popup-grey_center' && popup_template != 'popup-navy_blue_square','eprivay-remove-flex': gdpr_policy == 'eprivacy','popup-navy_blue_box_flex':popup_template == 'popup-navy_blue_box' && show_cookie_as == 'popup' && gdpr_policy != 'ccpa'  }"
										:style=" { 'margin-bottom': ( 2*accept_border_width ) + 'px' } " >
											<!-- accept button preview configuration  -->
											<a v-show="cookie_accept_on2 && popup_template != 'popup-navy_blue_square'" id="cookie_action_accept_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_as_button2,'button-as-link':!accept_as_button2,  'btn-lg': accept_as_button2 && accept_size2 === 'large','btn-sm': accept_as_button2 && accept_size2 === 'small','widget-accept-preview': show_cookie_as == 'widget','popup-accept-preview': show_cookie_as == 'popup' }" aria-label="Accept" href="#":style="{ color:accept_text_color2,'border-style': accept_style2, 'border-width': accept_as_button2 ? accept_border_width2 + 'px':'0', 'border-color': accept_border_color2, 'border-radius': accept_border_radius2+'px','background-color': accept_as_button2 ? `${accept_background_color2}${Math.floor(accept_opacity2 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font2
											}" >{{ accept_text2 }}</a>
											<!-- accept all button preview configuration  -->
											<a v-show="cookie_accept_all_on2 && popup_template != 'popup-navy_blue_square'" id="cookie_action_accept_all_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_all_as_button2,'button-as-link':!accept_all_as_button2,  'btn-lg': accept_all_as_button2 && accept_all_size2 === 'large','btn-sm': accept_all_as_button2 && accept_all_size2 === 'small','widget-accept-all-preview': show_cookie_as == 'widget','popup-accept-all-preview': show_cookie_as == 'popup' }" aria-label="Accept All" href="#" :style="{ color:accept_all_text_color2,'border-style': accept_all_style2, 'border-width': accept_all_as_button2 ? accept_all_border_width2 + 'px':'0', 'border-color': accept_all_border_color2, 'border-radius': accept_all_border_radius2+'px','background-color': accept_all_as_button2 ? `${accept_all_background_color2}${Math.floor(accept_all_opacity2 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font2
											}"  >{{ accept_all_text2 }}</a>

											<!-- Decline button preview configuration  -->
											<a v-show="cookie_decline_on2 && popup_template != 'popup-navy_blue_square'" id="cookie_action_reject_preview" class="gdpr_action_button_preview btn" :class="{ 'btn': decline_as_button2,'button-as-link':!decline_as_button2,  'btn-lg': decline_as_button2 && decline_size2 === 'large','btn-sm': decline_as_button2 && decline_size2 === 'small','widget-decline-preview': show_cookie_as == 'widget','popup-decline-preview': show_cookie_as == 'popup' }" aria-label="Reject"
											:style="{ color:decline_text_color2,'border-style': decline_style2, 'border-width': decline_as_button2 ? decline_border_width2 + 'px':'0', 'border-color': decline_border_color2, 'border-radius': decline_border_radius2+'px','background-color': decline_as_button2 ? `${decline_background_color2}${Math.floor(decline_opacity2 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font2
											}" >{{ decline_text2 }}</a>

											<!-- Setting button preview configuration  -->
											<a v-show="cookie_settings_on2 && popup_template != 'popup-navy_blue_square'" id="cookie_action_settings_preview" class="gpdr_cookie_settings_btn2 gdpr_action_button_preview btn" :class="{ 'btn': settings_as_button2,'button-as-link':!settings_as_button2,'btn-lg': settings_as_button2 && settings_size2 === 'large','btn-sm': settings_as_button2 && settings_size2 === 'small','widget-settings-preview': show_cookie_as == 'widget','widget-cookie-setting-container': (cookie_settings_on && !cookie_accept_all_on && !cookie_accept_on && !cookie_decline_on),'popup-setting-preview': show_cookie_as == 'popup', 'eprivay-hide-setting': gdpr_policy == 'eprivacy' }"   aria-label="Cookie Settings" href="#" :style="{ color:settings_text_color2,'border-style': settings_style2, 'border-width': settings_as_button2 ? settings_border_width2 + 'px':'0', 'border-color': settings_border_color2, 'border-radius': settings_border_radius2+'px','background-color': settings_as_button2 ? `${settings_background_color2}${Math.floor(settings_opacity2 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font2
											}" >{{ settings_text2 }}</a>

											<!-- buttons for navy blue square Pro template  -->
											<!-- Decline button -->
											<a v-show="cookie_decline_on2 && popup_template == 'popup-navy_blue_square'" id="cookie_action_reject_preview" class="gdpr_action_button_preview btn" :class="{ 'btn': decline_as_button2,'button-as-link':!decline_as_button2,  'btn-lg': decline_as_button2 && decline_size2 === 'large','btn-sm': decline_as_button2 && decline_size2 === 'small','widget-decline-preview': show_cookie_as == 'widget','popup-decline-preview': show_cookie_as == 'popup' }" aria-label="Reject"
											:style="{ color:decline_text_color2,'border-style': decline_style2, 'border-width': decline_as_button2 ? decline_border_width2 + 'px':'0', 'border-color': decline_border_color2, 'border-radius': decline_border_radius2+'px','background-color': decline_as_button2 ? `${decline_background_color2}${Math.floor(decline_opacity2 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font2
											}" >{{ decline_text2 }}</a>
											<!-- Setting button -->
											<a v-show="cookie_settings_on2 && popup_template == 'popup-navy_blue_square'" id="cookie_action_settings_preview" class="gpdr_cookie_settings_btn2 gdpr_action_button_preview btn" :class="{ 'btn': settings_as_button2,'button-as-link':!settings_as_button2,'btn-lg': settings_as_button2 && settings_size2 === 'large','btn-sm': settings_as_button2 && settings_size2 === 'small','widget-settings-preview': show_cookie_as == 'widget','widget-cookie-setting-container': (cookie_settings_on && !cookie_accept_all_on && !cookie_accept_on && !cookie_decline_on),'popup-setting-preview': show_cookie_as == 'popup', 'eprivay-hide-setting': gdpr_policy == 'eprivacy' }"   aria-label="Cookie Settings" href="#" :style="{ color:settings_text_color2,'border-style': settings_style2, 'border-width': settings_as_button2 ? settings_border_width2 + 'px':'0', 'border-color': settings_border_color2, 'border-radius': settings_border_radius2+'px','background-color': settings_as_button2 ? `${settings_background_color2}${Math.floor(settings_opacity2 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font2
											}" >{{ settings_text2 }}</a>
											<!-- accept button   -->
											<a v-show="cookie_accept_on2 && popup_template == 'popup-navy_blue_square'" id="cookie_action_accept_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_as_button2,'button-as-link':!accept_as_button2,  'btn-lg': accept_as_button2 && accept_size2 === 'large','btn-sm': accept_as_button2 && accept_size2 === 'small','widget-accept-preview': show_cookie_as == 'widget','popup-accept-preview': show_cookie_as == 'popup' }" aria-label="Accept" href="#":style="{ color:accept_text_color2,'border-style': accept_style2, 'border-width': accept_as_button2 ? accept_border_width2 + 'px':'0', 'border-color': accept_border_color2, 'border-radius': accept_border_radius2+'px','background-color': accept_as_button2 ? `${accept_background_color2}${Math.floor(accept_opacity2 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font2
											}" >{{ accept_text2 }}</a>

											<!-- accept all  -->
											<a v-show="cookie_accept_all_on2 && popup_template == 'popup-navy_blue_square'" id="cookie_action_accept_all_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_all_as_button2,'button-as-link':!accept_all_as_button2, 'btn-lg': accept_all_as_button2 && accept_all_size2 === 'large','btn-sm': accept_all_as_button2 && accept_all_size2 === 'small','widget-accept-all-preview': show_cookie_as == 'widget','popup-accept-all-preview': show_cookie_as == 'popup' }" aria-label="Accept All" href="#" :style="{ color:accept_all_text_color2,'border-style': accept_all_style2, 'border-width': accept_all_as_button2 ? accept_all_border_width2 + 'px':'0', 'border-color': accept_all_border_color2, 'border-radius': accept_all_border_radius2+'px','background-color': accept_all_as_button2 ? `${accept_all_background_color2}${Math.floor(accept_all_opacity2 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font2
											}"  >{{ accept_all_text2 }}</a>
									</div>
							</div>
							</div>

						</div>
					</div>
				</div>
	</div>
	<!-- for banner and widget preview -->
	<div v-else v-show="banner_preview_is_on && active_test_banner_tab === 2 && ab_testing_enabled" id="banner-preview-main-container2" class="banner-preview-main-container" :class="{ 'banner-top': cookie_position == 'top' && show_cookie_as == 'banner' ,'banner-bottom': cookie_position != 'top' && show_cookie_as == 'banner', 'banner-preview': show_cookie_as == 'banner','widget-preview': show_cookie_as == 'widget','widget-left': cookie_widget_position == 'left' && show_cookie_as == 'widget','widget-right': cookie_widget_position == 'right' && show_cookie_as == 'widget','widget-top-left': cookie_widget_position == 'top_left' && show_cookie_as == 'widget','widget-top-right': cookie_widget_position == 'top_right' && show_cookie_as == 'widget','popup-preview': show_cookie_as == 'popup','banner-default_preview':banner_template == 'banner-default' && show_cookie_as == 'banner','widget-default_preview':widget_template == 'widget-default' && show_cookie_as == 'widget','banner-almond_column_preview':banner_template == 'banner-almond_column' && show_cookie_as == 'banner','widget-almond_column_preview':widget_template == 'widget-almond_column' && show_cookie_as == 'widget' && gdpr_policy != 'ccpa','banner-navy_blue_center_preview':banner_template == 'banner-navy_blue_center' && show_cookie_as == 'banner','widget-navy_blue_center_preview':widget_template == 'widget-navy_blue_center' && show_cookie_as == 'widget','banner-grey_column_preview':banner_template == 'banner-grey_column' && show_cookie_as == 'banner','widget-grey_column_preview':widget_template == 'widget-grey_column' && show_cookie_as == 'widget' && gdpr_policy != 'ccpa','banner-dark_row_preview':banner_template == 'banner-dark_row' && show_cookie_as == 'banner','widget-dark_row_preview':widget_template == 'widget-dark_row' && show_cookie_as == 'widget','banner-grey_center_preview':banner_template == 'banner-grey_center' && show_cookie_as == 'banner','widget-grey_center_preview':widget_template == 'widget-grey_center' && show_cookie_as == 'widget','banner-dark_preview':banner_template == 'banner-dark' && show_cookie_as == 'banner','widget-dark_preview':widget_template == 'widget-dark' && show_cookie_as == 'widget','widget-navy_blue_box_preview':widget_template == 'widget-navy_blue_box' && show_cookie_as == 'widget','widget-navy_blue_square_preview':widget_template == 'widget-navy_blue_square' && show_cookie_as == 'widget' }" :style="{ 'background-color': `${cookie_bar_color2}${Math.floor(cookie_bar_opacity2 * 255).toString(16).toUpperCase()}`,'border-style': border_style2, 'border-width': cookie_bar_border_width2 + 'px', 'border-color': cookie_border_color2, 'border-radius': cookie_bar_border_radius2+'px', color:cookie_text_color2}" name="gcc-banner-preview">

			<div class="gdpr_messagebar_content_preview" :class="{ 'widget-msg-content-preview': show_cookie_as == 'widget','banner-msg-content-preview': show_cookie_as == 'banner'}" style="max-width: 825px;">
					<!-- logo image for banner and widget  -->
					<div v-show="!['banner-grey_column','banner-almond_column','widget-grey_column','widget-almond_column'].includes(banner_template)" class="gdpr_logo_container">
					<?php
						$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
					if ( ! empty( $get_banner_img ) ) {
						?>
						<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
							<?php
					}
					?>
					</div>

					<div class="group-description-preview" tabindex="0" :class="{'ccpa-group-description': is_ccpa && gdpr_policy != 'both' }">
						<div v-show="['banner-grey_column','banner-almond_column','widget-grey_column','widget-almond_column'].includes(banner_template)" class="gdpr_logo_container">
						<?php
							$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
						if ( ! empty( $get_banner_img ) ) {
							?>
							<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
								<?php
						}
						?>
						</div>

						<h3 v-show="is_gdpr && !is_ccpa" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font2 , 'color': 'inherit'}">{{gdpr_message_heading}}</h3>
						<h3 v-show="is_lgpd" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font2 , 'color': 'inherit' }">{{lgpd_message_heading}}</h3>
						<h3 v-show="is_gdpr && is_ccpa" class="gdpr_heading_preview" :style="{ fontFamily: cookie_font2 , 'color': 'inherit'}">{{gdpr_message_heading}}</h3>

						<p v-show="is_gdpr" class="gdpr_preview" v-html="gdpr_message" :style="{ fontFamily: cookie_font2 }"><br v-show="widget_template == 'widget-almond_column' && show_cookie_as == 'widget' ">
						<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font2,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
						</p>
						<p v-show="is_lgpd" class="gdpr_preview" :style="{ fontFamily: cookie_font2 }">{{lgpd_message}}<br v-show="widget_template == 'widget-almond_column' && show_cookie_as == 'widget' ">
						<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font2,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
						</p>
						<p v-show="is_eprivacy" class="gdpr_preview" :style="{ fontFamily: cookie_font2 }">{{eprivacy_message}}
						<a v-show="button_readmore_is_on" id="cookie_action_link_prview" href="#" class="gdpr_link_button_preview"  :class="{ 'btn': button_readmore_as_button,'button-as-link':!button_readmore_as_button,  'btn-lg': button_readmore_as_button && button_readmore_button_size === 'large','btn-sm': button_readmore_as_button && button_readmore_button_size === 'small' }" :style="{ fontFamily: cookie_font2,color:button_readmore_link_color,'border-style': button_readmore_button_border_style, 'border-width': button_readmore_button_border_width ? button_readmore_button_border_width + 'px':'0', 'border-color': button_readmore_button_border_color, 'border-radius': button_readmore_button_border_radius+'px','background-color': button_readmore_as_button ? `${button_readmore_button_color}${Math.floor(button_readmore_button_opacity * 255).toString(16).toUpperCase()}`:'transparent' }">{{button_readmore_text}}</a>
						</p>
						<p v-show="is_ccpa" class="ccpa_preview_msg" :class="{'ccpa-center-text':show_cookie_as == 'banner' && gdpr_policy != 'both' }" :style="{ fontFamily: cookie_font2 }">{{ccpa_message}} <a href="#" class="ccpa_link_button_preview" :style="{'color':opt_out_text_color2,fontFamily: cookie_font2}">{{opt_out_text2}}</a>
						</p>
					</div>
					<div v-show="['gdpr','eprivacy','both','lgpd'].includes(gdpr_policy)" class="group-description-buttons-preview" id="default_buttons_preview"
						:class=" {'widget-button-container':
						((cookie_accept_all_on2 && cookie_settings_on2) ||
						(cookie_accept_on2 && cookie_accept_all_on2) ||
						(cookie_accept_all_on2 && cookie_decline_on2) ||
						(cookie_accept_all_on2 && cookie_decline_on2 && cookie_settings_on2) ||
						(cookie_accept_all_on2 && cookie_decline_on2 && cookie_settings_on2 && cookie_accept_on2) ||
						(cookie_accept_all_on2 && !cookie_accept_on2 && !cookie_decline_on2 && !cookie_settings_on2)) && show_cookie_as == 'widget' && widget_template != 'widget-dark_row' && widget_template != 'widget-grey_center' && widget_template != 'widget-navy_blue_square','eprivay-remove-flex': gdpr_policy == 'eprivacy','widget-navy_blue_box_flex':widget_template == 'widget-navy_blue_box' && show_cookie_as == 'widget' && gdpr_policy != 'ccpa'  }"
						:style=" { 'margin-bottom': ( 2*accept_border_width2 ) + 'px' } " >
							<!-- accept button preview configuration  -->
							<a v-show="cookie_accept_on2 && widget_template != 'widget-navy_blue_square'" id="cookie_action_accept_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_as_button2,'button-as-link':!accept_as_button2,  'btn-lg': accept_as_button2 && accept_size2 === 'large','btn-sm': accept_as_button2 && accept_size2 === 'small','widget-accept-preview': show_cookie_as == 'widget' }" aria-label="Accept" href="#":style="{ color:accept_text_color2,'border-style': accept_style2, 'border-width': accept_as_button2 ? accept_border_width2 + 'px':'0', 'border-color': accept_border_color2, 'border-radius': accept_border_radius2+'px','background-color': accept_as_button2 ? `${accept_background_color2}${Math.floor(accept_opacity2 * 255).toString(16).toUpperCase()}`:'transparent',fontFamily: cookie_font2
							}" >{{ accept_text2 }}</a>

							<!-- accept all button preview configuration  -->
							<a v-show="cookie_accept_all_on2 && widget_template != 'widget-navy_blue_square'" id="cookie_action_accept_all_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_all_as_button2,'button-as-link':!accept_all_as_button2,  'btn-lg': accept_all_as_button2 && accept_all_size2 === 'large','btn-sm': accept_all_as_button2 && accept_all_size2 === 'small','widget-accept-all-preview': show_cookie_as == 'widget' }" aria-label="Accept All" href="#" :style="{ color:accept_all_text_color2,'border-style': accept_all_style2, 'border-width': accept_all_as_button2 ? accept_all_border_width2 + 'px':'0', 'border-color': accept_all_border_color2, 'border-radius': accept_all_border_radius2+'px','background-color': accept_all_as_button2 ? `${accept_all_background_color2}${Math.floor(accept_all_opacity2 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font2
							}"  >{{ accept_all_text2 }}</a>

							<!-- Decline button preview configuration  -->
							<a v-show="cookie_decline_on2 && widget_template != 'widget-navy_blue_square'" id="cookie_action_reject_preview" class="gdpr_action_button_preview btn" :class="{ 'btn': decline_as_button2,'button-as-link':!decline_as_button2,  'btn-lg': decline_as_button2 && decline_size2 === 'large','btn-sm': decline_as_button2 && decline_size2 === 'small','widget-decline-preview': show_cookie_as == 'widget' }" aria-label="Reject"
							:style="{ color:decline_text_color2,'border-style': decline_style2, 'border-width': decline_as_button2 ? decline_border_width2 + 'px':'0', 'border-color': decline_border_color2, 'border-radius': decline_border_radius2+'px','background-color': decline_as_button2 ? `${decline_background_color2}${Math.floor(decline_opacity2 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font2
							}"  >{{ decline_text2 }}</a>

							<!-- Setting button preview configuration  -->
							<a v-show="cookie_settings_on2 && widget_template != 'widget-navy_blue_square'" id="cookie_action_settings_preview" class="gpdr_cookie_settings_btn2 gdpr_action_button_preview btn" :class="{ 'btn': settings_as_button2,'button-as-link':!settings_as_button2,'btn-lg': settings_as_button2 && settings_size2 === 'large','btn-sm': settings_as_button2 && settings_size2 === 'small','widget-settings-preview': show_cookie_as == 'widget','widget-cookie-setting-container': (cookie_settings_on && !cookie_accept_all_on && !cookie_accept_on && !cookie_decline_on),'eprivay-hide-setting': gdpr_policy == 'eprivacy' }"   aria-label="Cookie Settings" href="#" :style="{ color:settings_text_color2,'border-style': settings_style2, 'border-width': settings_as_button2 ? settings_border_width2 + 'px':'0', 'border-color': settings_border_color2, 'border-radius': settings_border_radius2+'px','background-color': settings_as_button2 ? `${settings_background_color2}${Math.floor(settings_opacity2 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font2
							}" >{{ settings_text2 }}</a>

							<!-- buttons for navy blue square Pro template  -->
							<!-- Decline button -->
							<a v-show="cookie_decline_on2 && widget_template == 'widget-navy_blue_square'" id="cookie_action_reject_preview" class="gdpr_action_button_preview btn" :class="{ 'btn': decline_as_button2,'button-as-link':!decline_as_button2,  'btn-lg': decline_as_button2 && decline_size2 === 'large','btn-sm': decline_as_button2 && decline_size2 === 'small','widget-decline-preview': show_cookie_as == 'widget' }" aria-label="Reject"
							:style="{ color:decline_text_color2,'border-style': decline_style2, 'border-width': decline_as_button2 ? decline_border_width2 + 'px':'0', 'border-color': decline_border_color2, 'border-radius': decline_border_radius2+'px','background-color': decline_as_button2 ? `${decline_background_color2}${Math.floor(decline_opacity2 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font2
							}"  >{{ decline_text2 }}</a>
							<!-- Setting button -->
							<a v-show="cookie_settings_on2 && widget_template == 'widget-navy_blue_square'" id="cookie_action_settings_preview" class="gpdr_cookie_settings_btn2 gdpr_action_button_preview btn" :class="{ 'btn': settings_as_button2,'button-as-link':!settings_as_button2,'btn-lg': settings_as_button2 && settings_size2 === 'large','btn-sm': settings_as_button2 && settings_size2 === 'small','widget-settings-preview': show_cookie_as == 'widget','widget-cookie-setting-container': (cookie_settings_on && !cookie_accept_all_on && !cookie_accept_on && !cookie_decline_on),'eprivay-hide-setting': gdpr_policy == 'eprivacy' }"   aria-label="Cookie Settings" href="#" :style="{ color:settings_text_color2,'border-style': settings_style2, 'border-width': settings_as_button2 ? settings_border_width2 + 'px':'0', 'border-color': settings_border_color2, 'border-radius': settings_border_radius2+'px','background-color': settings_as_button2 ? `${settings_background_color2}${Math.floor(settings_opacity2 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font2
							}" >{{ settings_text2 }}</a>
							<!-- accept button   -->
							<a v-show="cookie_accept_on2 && widget_template == 'widget-navy_blue_square'" id="cookie_action_accept_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_as_button2,'button-as-link':!accept_as_button2,  'btn-lg': accept_as_button2 && accept_size2 === 'large','btn-sm': accept_as_button2 && accept_size2 === 'small','widget-accept-preview': show_cookie_as == 'widget' }" aria-label="Accept" href="#":style="{ color:accept_text_color2,'border-style': accept_style2, 'border-width': accept_as_button2 ? accept_border_width2 + 'px':'0', 'border-color': accept_border_color2, 'border-radius': accept_border_radius2+'px','background-color': accept_as_button2 ? `${accept_background_color2}${Math.floor(accept_opacity2 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font2
							}" >{{ accept_text2 }}</a>

							<!-- accept all  -->
							<a v-show="cookie_accept_all_on2 && widget_template == 'widget-navy_blue_square'" id="cookie_action_accept_all_preview" class="gdpr_action_button_preview" :class="{ 'btn': accept_all_as_button2,'button-as-link':!accept_all_as_button2,  'btn-lg': accept_all_as_button2 && accept_all_size2 === 'large','btn-sm': accept_all_as_button2 && accept_all_size2 === 'small','widget-accept-all-preview': show_cookie_as == 'widget' }" aria-label="Accept All" href="#" :style="{ color:accept_all_text_color2,'border-style': accept_all_style2, 'border-width': accept_all_as_button2 ? accept_all_border_width2 + 'px':'0', 'border-color': accept_all_border_color2, 'border-radius': accept_all_border_radius2+'px','background-color': accept_all_as_button2 ? `${accept_all_background_color2}${Math.floor(accept_all_opacity2 * 255).toString(16).toUpperCase()}`:'transparent'  ,fontFamily: cookie_font2
							}"  >{{ accept_all_text2 }}</a>

					</div>
			</div>
	</div>
	
	
	<c-container class="gdpr-cookie-consent-settings-container">

		<c-form id="gcc-save-settings-form" spellcheck="false" class="gdpr-cookie-consent-settings-form">
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
							<img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cookie_setting_preview.svg'; ?>" alt="Cookie Setting preview logo">
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
			</div><hr id="preview-btn-setting-nav-seperator">
			<c-tabs variant="pills" ref="active_tab" class="gdpr-cookie-consent-settings-nav">

				<c-tab title="<?php esc_attr_e( 'Compliances', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#compliances"id="gdpr-cookie-consent-complianz" >
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
								<c-row class="gdpr-cookie-consent-laws-type" v-show="is_gdpr && !is_ccpa">
									<c-col class="col-sm-4"></c-col>
									<c-col class="col-sm-8 ">
									<p class="policy-description">The chosen law template supports various global privacy regulations including GDPR (EU & UK), PIPEDA (Canada), Law 25 (Quebec), POPIA (South Africa), nFADP (Switzerland), Privacy Act (Australia), PDPL (Saudi Arabia), PDPL (Argentina), PDPL (Andorra), and DPA (Faroe Islands).</p>
										<div class="cookie-notice-readmore-container">
												<a class="cookie-notice-readmore" href="https://wplegalpages.com/docs/wp-cookie-consent/settings/gdpr-settings/" target="_blank">
													<?php esc_attr_e( 'Learn more about setting up an GDPR notice', 'gdpr-cookie-consent' ); ?>
												</a>
										</div>
									</c-col>
								</c-row>
								<c-row class="gdpr-cookie-consent-laws-type" v-show="is_ccpa && !is_gdpr">
									<c-col class="col-sm-4"></c-col>
									<c-col class="col-sm-8">
									<p class="policy-description" >The chosen law template supports CCPA/CPRA (California), VCDPA (Virginia), CPA (Colorado), CTDPA (Connecticut), & UCPA (Utah).</p>
										<div class="cookie-notice-readmore-container">
												<a class="cookie-notice-readmore" href="https://wplegalpages.com/docs/wp-cookie-consent/settings/ccpa-settings/" target="_blank">
													<?php esc_attr_e( 'Learn more about setting up an CCPA notice', 'gdpr-cookie-consent' ); ?>
												</a>
										</div>
									</c-col>
								</c-row>
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
								<!-- Visitors Condition -->
								<c-row v-show="!is_eprivacy && !is_lgpd">
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
												if ( !$is_user_connected) :
													?>
													<div><input id="gdpr-visitors-condition-radio-btn-disabled-gdpr"class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-eu-enable" disabled><label><?php esc_attr_e( 'EU Countries & UK', 'gdpr-cookie-consent' ); ?></label></div>
													<p class=" gdpr-eu_visitors_message-gdpr">
														<?php esc_attr_e( 'To enable this feature, connect to your free account', 'gdpr-cookie-consent' ); ?>
													</p>
												<?php else : ?>
													<div><input class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-eu-enable" v-model="selectedRadioGdpr" @click="onSwitchEUEnable($event.target.checked)"><label><?php esc_attr_e( 'EU Countries & UK', 'gdpr-cookie-consent' ); ?></label></div>
														<input type="hidden" name="gcc-eu-enable" v-model="is_eu_on">
												<?php endif; ?>
										</div>
										<div v-show="gdpr_policy === 'ccpa' || gdpr_policy === 'both'">
											<?php
												$geo_options = get_option( 'wpl_geo_options' );
											if ( !$is_user_connected ) :
												?>
													<div><input id="gdpr-visitors-condition-radio-btn-disabled-ccpa"class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-ccpa-enable" disabled><label style="width:114px;"><?php esc_attr_e( 'United States', 'gdpr-cookie-consent' ); ?></label></div>
													<p class=" gdpr-eu_visitors_message-ccpa">
													<?php esc_attr_e( 'To enable this feature, connect to your free account', 'gdpr-cookie-consent' ); ?>
													</p>
					
													
												<?php else : ?>
											<div><input class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-ccpa-enable" v-model="selectedRadioCcpa" @click="onSwitchCCPAEnable($event.target.checked)"><label><?php esc_attr_e( 'United States', 'gdpr-cookie-consent' ); ?></label></div>
													<input type="hidden" name="gcc-ccpa-enable" v-model="is_ccpa_on">
												
												<?php endif; ?>
										</div>
										<div v-show="gdpr_policy === 'gdpr' || gdpr_policy === 'both' || gdpr_policy === 'ccpa'">
											<?php
												$geo_options = get_option( 'wpl_geo_options' );
											if ( !$is_user_connected ) :
												?>
													<div><input class="gdpr-visiotrs-condition-radio-btn" id="gdpr-visitors-condition-radio-btn-disabled-both" type="checkbox" name="gcc-select-countries-enable"disabled><label><?php esc_attr_e( 'Select Countries', 'gdpr-cookie-consent' ); ?></label></div>
													<p class=" gdpr-eu_visitors_message-both">
													<?php esc_attr_e( 'To enable this feature, connect to your free account', 'gdpr-cookie-consent' ); ?>
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
								<!-- Privacy Policy Settings -->
								<c-row v-show="show_revoke_card || is_lgpd">
									<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Privacy Policy Settings', 'gdpr-cookie-consent' ); ?></div></c-col>
								</c-row>
								<c-row v-show="show_revoke_card || is_lgpd">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Privacy Policy Link', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enable this to provide a link to your Privacy & Cookie Policy on your Cookie Notice', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="button_readmore_is_on" id="gdpr-cookie-consent-readmore-is-on" variant="3d"  color="success" :checked="button_readmore_is_on" v-on:update:checked="onSwitchButtonReadMoreIsOn"></c-switch>
										<input type="hidden" name="gcc-readmore-is-on" v-model="button_readmore_is_on">
									</c-col>
								</c-row>
								<c-row v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter the text of the privacy policy button/link.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-8">
										<c-input name="button_readmore_text_field" v-model="button_readmore_text"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="button_readmore_link_color"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-readmore-link-color" type="color" name="gcc-readmore-link-color" v-model="button_readmore_link_color"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Show as', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gcc-readmore-as-button" :reduce="label => label.code" :options="show_as_options" v-model="button_readmore_as_button"></v-select>
										<input type="hidden" name="gcc-readmore-as-button" v-model="button_readmore_as_button">
									</c-col>
								</c-row>
								<div v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on">
									<c-row v-show="button_readmore_as_button">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
											<c-input class="gdpr-color-input" type="text" v-model="button_readmore_button_color"></c-input>
											<c-input class="gdpr-color-select" id="gdpr-readmore-button-color" type="color" name="gcc-readmore-button-color" v-model="button_readmore_button_color"></c-input>
										</c-col>
									</c-row>
									<c-row v-show="button_readmore_as_button">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="button_readmore_button_opacity"></c-input>
											<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-opacity" v-model="button_readmore_button_opacity"></c-input>
										</c-col>
									</c-row>
									<c-row v-show="button_readmore_as_button">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<v-select class="form-group" id="gcc-readmore-button-border-style" :reduce="label => label.code" :options="border_style_options" v-model="button_readmore_button_border_style"></v-select>
											<input type="hidden" name="gcc-readmore-button-border-style" v-model="button_readmore_button_border_style">
										</c-col>
									</c-row>
									<c-row v-show="button_readmore_as_button">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="button_readmore_button_border_width"></c-input>
											<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-border-width" v-model="button_readmore_button_border_width"></c-input>
										</c-col>
									</c-row>
									<c-row v-show="button_readmore_as_button">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick" >
											<c-input class="gdpr-color-input" type="text" v-model="button_readmore_button_border_color"></c-input>
											<c-input class="gdpr-color-select" id="gdpr-readmore-button-border-color" type="color" name="gcc-readmore-button-border-color" v-model="button_readmore_button_border_color"></c-input>
										</c-col>
									</c-row>
									<c-row v-show="button_readmore_as_button">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8 gdpr-color-pick">
											<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="button_readmore_button_border_radius"></c-input>
											<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-border-radius" v-model="button_readmore_button_border_radius"></c-input>
										</c-col>
									</c-row>
									<c-row v-show="button_readmore_as_button">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<v-select class="form-group" id="gcc-readmore-button-size" :reduce="label => label.code" :options="button_size_options" v-model="button_readmore_button_size"></v-select>
											<input type="hidden" name="gcc-readmore-button-size" v-model="button_readmore_button_size">
										</c-col>
									</c-row>
								</div>
								<c-row v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Page or Custom URL', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gcc-readmore-url-type" :reduce="label => label.code" :options="url_type_options" v-model="button_readmore_url_type"></v-select>
										<input type="hidden" name="gcc-readmore-url-type" v-model="button_readmore_url_type">
									</c-col>
								</c-row>
								<div v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on">
									<c-row v-show="button_readmore_url_type">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Page', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<v-select class="form-group"  placeholder="Select Policy Page" id="gcc-readmore-page" :reduce="label => label.code" :options="privacy_policy_options" v-model="readmore_page" @input="onSelectPrivacyPage"></v-select>
											<input type="hidden" name="gcc-readmore-page" v-model="button_readmore_page">
										</c-col>
									</c-row>
									<c-row v-show="button_readmore_url_type">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Sync with WordPress Policy Page', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'If enabled visitor will be redirected to Privacy Policy Page set in WordPress settings irrespective of Page set in the previous setting.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
										<c-col class="col-sm-8">
											<c-switch v-bind="labelIcon" v-model="button_readmore_wp_page" id="gdpr-cookie-consent-readmore-wp-page" variant="3d"  color="success" :checked="button_readmore_wp_page" v-on:update:checked="onSwitchButtonReadMoreWpPage"></c-switch>
											<input type="hidden" name="gcc-readmore-wp-page" v-model="button_readmore_wp_page">
										</c-col>
									</c-row>
									<c-row v-show="!button_readmore_url_type">
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'URL', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col class="col-sm-8">
											<c-input name="gcc-readmore-url" v-model="button_readmore_url"></c-input>
										</c-col>
									</c-row>
								</div>
								<c-row v-show="(show_revoke_card || is_lgpd) && button_readmore_is_on">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Open URL in New Window?', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="button_readmore_new_win" id="gdpr-cookie-consent-readmore-new-win" variant="3d"  color="success" :checked="button_readmore_new_win" v-on:update:checked="onSwitchButtonReadMoreNewWin"></c-switch>
										<input type="hidden" name="gcc-readmore-new-win" v-model="button_readmore_new_win">
									</c-col>
								</c-row>
								<!-- Revoke Consent settings -->
								<c-row v-show="show_revoke_card || is_lgpd">
									<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Revoke Consent', 'gdpr-cookie-consent' ); ?></div></c-col>
								</c-row>
								<c-row v-show="show_revoke_card || is_lgpd">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Revoke Consent', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enable to give user the option to revoke their consent.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="is_revoke_consent_on" id="gdpr-cookie-consent-revoke-consent" variant="3d"  color="success" :checked="is_revoke_consent_on" v-on:update:checked="onSwitchRevokeConsentEnable"></c-switch>
										<input type="hidden" name="gcc-revoke-consent-enable" v-model="is_revoke_consent_on">
									</c-col>
								</c-row>
								<c-row v-show="(show_revoke_card || is_lgpd) && is_revoke_consent_on">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Tab Text', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<c-input name="show_again_text_field" v-model="tab_text"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="(show_revoke_card || is_lgpd) && is_revoke_consent_on">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="button_revoke_consent_text_color"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-readmore-link-color" type="color" name="gcc-revoke-consent-text-color" v-model="button_revoke_consent_text_color"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="(show_revoke_card || is_lgpd) && is_revoke_consent_on">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="button_revoke_consent_background_color"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-readmore-button-color" type="color" name="gcc-revoke-consent-background-color" v-model="button_revoke_consent_background_color"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="(show_revoke_card || is_lgpd) && is_revoke_consent_on">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Tab Position', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-consent-tab-position" :reduce="label => label.code" :options="tab_position_options" v-model="tab_position">
										</v-select>
										<input type="hidden" name="gcc-tab-position" v-model="tab_position">
									</c-col>
								</c-row>
								<c-row v-show="(show_revoke_card || is_lgpd) && is_revoke_consent_on">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Tab margin (in percent)', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<c-input type="number" min="0" max="100" name="gcc-tab-margin" v-model="tab_margin"></c-input>
									</c-col>
								</c-row>
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
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Restrict Pages and/or Posts', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Restrict Pages and/or Posts during scanning of your website for cookies.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-8">
										<v-select  placeholder="Select Pages and Posts" id="gdpr-cookie-consent-restrict-posts" :reduce="label => label.code" class="form-group" :options="list_of_contents" multiple v-model="restrict_array" @input="onPostsSelect"></v-select>
										<input type="hidden" name="gcc-restrict-posts" v-model="restrict_posts">
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
									<input type="hidden" name="gcc-consent-renew-enable" v-model="is_consent_renewed">
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
				<c-tab title="<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#configuration" id="gdpr-cookie-consent-configuration">

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
											<label style="margin-bottom:0; font-size:0.875rem;
											<?php
											echo version_compare( $plugin_version, '2.5.2', '<=' ) ? ( ! $is_pro_active ? 'color:#D8DBE0;' : 'color:#3399ff;' ) : 'color:#3399ff;';
											?>
											text-decoration:underline;margin: right 10px ;padding-left:42px;margin-top:6px;" for="fileInput">Choose file</label>
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
							<c-row class="pt-1 mb-0" >
								<c-col class="col-sm-4">
									<label style="margin-bottom:0"><?php esc_attr_e( 'Reset Settings ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'This will reset all settings to defaults. All data in the WP Cookie Consent plugin will be deleted. ', 'gdpr-cookie-consent' ); ?>">
											</tooltip></label>
								</c-col>
								<c-col class="col-sm-8">
									<c-button id="reset-settings-configuration" color="danger" variant="outline" @click="onClickRestoreButton"><?php esc_html_e( 'Reset to Default', 'gdpr-cookie-consent' ); ?></c-button>
								</c-col>
							</c-row>
						</c-card-body>
					</c-card>
				</c-tab>
				<c-tab title="<?php esc_attr_e( 'Design', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#gdpr_design" id="gdpr-cookie-consent-design">
					
				
					<!-- Desgin Banner preview if A/B Testing is disabled-->

					<c-card v-show="!ab_testing_enabled">
					<c-card class="desgin_card">
						<c-card-body>
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
							$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ? GDPR_COOKIE_CONSENT_VERSION : '';
							if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
								if ( ! $is_pro_active ) {
									?>
								<c-row>
									<c-col class="col-sm-4">
										<label><?php esc_attr_e( 'Upload Logo ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'To preview the logo, simply upload a logo and then click the "Save Changes" button ', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
										<div class="gdpr-pro-label"><div class="gdpr-pro-label-text">Pro</div></div>
									</c-col>
									<c-col class="col-sm-8 ">
										<c-button disabled color="info" class="button" id="image-upload-button" name="image-upload-button" @click="openMediaModal" style="margin: 10px;">
											<?php esc_attr_e( 'Add Image', 'gdpr-cookie-consent' ); ?>
										</c-button>
										<c-button disabled color="info" class="button" id="image-delete-button" @click="deleteSelectedimage" style="margin: 10px; ">
											<?php esc_attr_e( 'Remove Image', 'gdpr-cookie-consent' ); ?>
										</c-button>
										<p class="image-upload-notice" style="margin-left: 10px;">
											<?php esc_attr_e( 'We recommend 50 x 50 pixels.', 'gdpr-cookie-consent' ); ?>
										</p>
										<c-input type="hidden" name="gdpr-cookie-bar-logo-url-holder" id="gdpr-cookie-bar-logo-url-holder"  class="regular-text"> </c-input>
									</c-col>
								</c-row>
									<?php
								}
							} else {
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
							<?php } ?>
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
									<v-select class="form-group" id="gdpr-cookie-accept-as-button" :reduce="label => label.code" :options="accept_as_button_options" v-model="accept_as_button"></v-select>
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
									<v-select class="form-group" id="gdpr-cookie-accept-all-as-button" :reduce="label => label.code" :options="accept_as_button_options" v-model="accept_all_as_button"></v-select>
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
										<v-select class="form-group" id="gdpr-cookie-decline-as-button" :reduce="label => label.code" :options="accept_as_button_options" v-model="decline_as_button"></v-select>
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
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Cookie Settings Layout', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Select the style of the Cookie Settings', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
								</c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-settings-as-button" :reduce="label => label.code" :options="accept_as_button_options" v-model="settings_as_button"></v-select>
									<input type="hidden" name="gdpr-cookie-settings-as" v-model="settings_as_button">
								</c-col>
								<c-col v-show="is_banner" class="col-sm-6" >
									<v-select class="form-group" id="gdpr-cookie-settings-layout" :reduce="label => label.code" :options="settings_layout_options" v-model="settings_layout" @input="cookieLayoutChange"></v-select>
									<input type="hidden" name="gdpr-cookie-settings-layout" v-model="settings_layout">
								</c-col>
								<c-col v-show="!is_banner" class="col-sm-6" >
									<v-select class="form-group" id="gdpr-cookie-settings-layout" :reduce="label => label.code" :options="settings_layout_options_extended" v-model="settings_layout" @input="cookieLayoutChange"></v-select>
									<input type="hidden" name="gdpr-cookie-settings-layout" v-model="settings_layout">
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
							<!-- add custom css card  -->
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
					</c-card>
					</c-card>


					<!-- Desgin Banner preview if A/B Testing is enabled-->
					<c-card class=" desgin_card" v-show="ab_testing_enabled">
						<div class="gdpr-cookie-consent-banner-tabs">
							<c-button class="gdpr-cookie-consent-banner-tab"@click="changeActiveTestBannerTabTo1":class="{ 'gdpr-cookie-consent-banner-tab-active': active_test_banner_tab === 1 }"><?php esc_html_e( isset( $the_options['cookie_bar1_name'] ) ? $the_options['cookie_bar1_name'] : 'Test Banner A', 'gdpr-cookie-consent' ); ?><span v-show="default_cookie_bar === true">(default)</span></c-button>
							<c-button class="gdpr-cookie-consent-banner-tab"@click="changeActiveTestBannerTabTo2":class="{ 'gdpr-cookie-consent-banner-tab-active': active_test_banner_tab === 2 }"><?php esc_html_e( isset( $the_options['cookie_bar2_name'] ) ? $the_options['cookie_bar2_name'] : 'Test Banner B', 'gdpr-cookie-consent' ); ?><span v-show="default_cookie_bar === false">(default)</span></c-button>
						</div>
						<c-card-body v-show="active_test_banner_tab === 1">
								<c-card-body >
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
									<?php
									$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ? GDPR_COOKIE_CONSENT_VERSION : '';
									if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
										if ( ! $is_pro_active ) {
											?>
								<c-row>
									<c-col class="col-sm-4">
										<label><?php esc_attr_e( 'Upload Logo ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'To preview the logo, simply upload a logo and then click the "Save Changes" button ', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
										<div class="gdpr-pro-label"><div class="gdpr-pro-label-text">Pro</div></div>
									</c-col>
									<c-col class="col-sm-8 ">
										<c-button disabled color="info" class="button" id="image-upload-button" name="image-upload-button" @click="openMediaModal" style="margin: 10px;">
											<?php esc_attr_e( 'Add Image', 'gdpr-cookie-consent' ); ?>
										</c-button>
										<c-button disabled color="info" class="button" id="image-delete-button" @click="deleteSelectedimage" style="margin: 10px; ">
											<?php esc_attr_e( 'Remove Image', 'gdpr-cookie-consent' ); ?>
										</c-button>
										<p class="image-upload-notice" style="margin-left: 10px;">
											<?php esc_attr_e( 'We recommend 50 x 50 pixels.', 'gdpr-cookie-consent' ); ?>
										</p>
										<c-input type="hidden" name="gdpr-cookie-bar-logo-url-holder" id="gdpr-cookie-bar-logo-url-holder1"  class="regular-text"> </c-input>
									</c-col>
								</c-row>
											<?php
										}
									} else {
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
										<img id="gdpr-cookie-bar-logo-holder1" name="gdpr-cookie-bar-logo-holder" src="<?php echo esc_url_raw( $get_banner_img ); ?>">
										<p class="image-upload-notice" style="margin-left: 10px; font-size:14px; font-weight:14px;color:#d4d4d8;">
											<?php esc_attr_e( 'We recommend 50 x 50 pixels.', 'gdpr-cookie-consent' ); ?>
										</p>
										<c-input type="hidden" name="gdpr-cookie-bar-logo-url-holder" id="gdpr-cookie-bar-logo-url-holder1"  class="regular-text"> </c-input>
									</c-col>
								</c-row>
									<?php } ?>
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
									<v-select class="form-group" id="gdpr-cookie-accept-as-button1" :reduce="label => label.code" :options="accept_as_button_options" v-model="accept_as_button1"></v-select>
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
									<v-select class="form-group" id="gdpr-cookie-accept-all-as-button1" :reduce="label => label.code" :options="accept_as_button_options" v-model="accept_all_as_button1"></v-select>
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
										<v-select class="form-group" id="gdpr-cookie-decline-as-button1" :reduce="label => label.code" :options="accept_as_button_options" v-model="decline_as_button1"></v-select>
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
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Cookie Settings Layout', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Select the style of the Cookie Settings', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
								</c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-settings-as-button1" :reduce="label => label.code" :options="accept_as_button_options" v-model="settings_as_button1"></v-select>
									<input type="hidden" name="gdpr-cookie-settings-as1" v-model="settings_as_button1">
								</c-col>
								<c-col v-show="is_banner" class="col-sm-6" >
									<v-select class="form-group" id="gdpr-cookie-settings-layout1" :reduce="label => label.code" :options="settings_layout_options" v-model="settings_layout1" @input="cookieLayoutChange1"></v-select>
									<input type="hidden" name="gdpr-cookie-settings-layout1" v-model="settings_layout1">
								</c-col>
								<c-col v-show="!is_banner" class="col-sm-6" >
									<v-select class="form-group" id="gdpr-cookie-settings-layout1" :reduce="label => label.code" :options="settings_layout_options_extended" v-model="settings_layout1" @input="cookieLayoutChange1"></v-select>
									<input type="hidden" name="gdpr-cookie-settings-layout1" v-model="settings_layout1">
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
					<!-- add custom css card  -->
					<?php
					$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ? GDPR_COOKIE_CONSENT_VERSION : '';
					if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
						if ( $is_pro_active ) {
							do_action( 'gdpr_custom_css' );
						} else {
							?>
									<c-card >
									<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Add Your Custom CSS', 'gdpr-cookie-consent' ); ?>

									<div class="gdpr-pro-label absolute" style="top: 10px; right: 750px;"><div class="gdpr-pro-label-text">Pro</div></div>

									</c-card-header>
									<c-card-body>
										<c-col class="col-sm-12">
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
									</c-card-body>
								</c-card>
							<?php
						}
					} else {
						?>
					<c-card >
					<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Add Your Custom CSS', 'gdpr-cookie-consent' ); ?></c-card-header>
					<c-card-body>
						<c-col class="col-sm-12">
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

					</c-card-body>
					</c-card>
									<?php
					}
					?>
							</c-card-body>
						<c-card-body v-show="active_test_banner_tab === 2">
								<c-card-body >
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
										<?php } ?>
									<?php
									$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ? GDPR_COOKIE_CONSENT_VERSION : '';
									if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
										if ( ! $is_pro_active ) {
											?>
										<c-row>
											<c-col class="col-sm-4">
												<label><?php esc_attr_e( 'Upload Logo ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( 'To preview the logo, simply upload a logo and then click the "Save Changes" button ', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
												<div class="gdpr-pro-label"><div class="gdpr-pro-label-text">Pro</div></div>
											</c-col>
											<c-col class="col-sm-8 ">
												<c-button disabled color="info" class="button" id="image-upload-button" name="image-upload-button" @click="openMediaModal" style="margin: 10px;">
													<?php esc_attr_e( 'Add Image', 'gdpr-cookie-consent' ); ?>
												</c-button>
												<c-button disabled color="info" class="button" id="image-delete-button" @click="deleteSelectedimage" style="margin: 10px; ">
													<?php esc_attr_e( 'Remove Image', 'gdpr-cookie-consent' ); ?>
												</c-button>
												<p class="image-upload-notice" style="margin-left: 10px;">
													<?php esc_attr_e( 'We recommend 50 x 50 pixels.', 'gdpr-cookie-consent' ); ?>
												</p>
												<c-input type="hidden" name="gdpr-cookie-bar-logo-url-holder" id="gdpr-cookie-bar-logo-url-holder2"  class="regular-text"> </c-input>
											</c-col>
										</c-row>
											<?php
										}
									} else {
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
												<img id="gdpr-cookie-bar-logo-holder2" name="gdpr-cookie-bar-logo-holder" src="<?php echo esc_url_raw( $get_banner_img ); ?>">
												<p class="image-upload-notice" style="margin-left: 10px;">
													<?php esc_attr_e( 'We recommend 50 x 50 pixels.', 'gdpr-cookie-consent' ); ?>
												</p>
												<c-input type="hidden" name="gdpr-cookie-bar-logo-url-holder" id="gdpr-cookie-bar-logo-url-holder2"  class="regular-text"> </c-input>
											</c-col>
										</c-row>
									<?php } ?>
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
									<v-select class="form-group" id="gdpr-cookie-accept-as-button2" :reduce="label => label.code" :options="accept_as_button_options" v-model="accept_as_button2"></v-select>
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
									<v-select class="form-group" id="gdpr-cookie-accept-all-as-button2" :reduce="label => label.code" :options="accept_as_button_options" v-model="accept_all_as_button2"></v-select>
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
										<v-select class="form-group" id="gdpr-cookie-decline-as-button2" :reduce="label => label.code" :options="accept_as_button_options" v-model="decline_as_button2"></v-select>
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
								<c-col class="col-sm-6">
									<label><?php esc_attr_e( 'Cookie Settings Layout', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Select the style of the Cookie Settings', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
								</c-col>
							</c-row>
							<c-row>
								<c-col class="col-sm-6">
									<v-select class="form-group" id="gdpr-cookie-settings-as-button2" :reduce="label => label.code" :options="accept_as_button_options" v-model="settings_as_button2"></v-select>
									<input type="hidden" name="gdpr-cookie-settings-as2" v-model="settings_as_button2">
								</c-col>
								<c-col v-show="is_banner" class="col-sm-6" >
									<v-select class="form-group" id="gdpr-cookie-settings-layout2" :reduce="label => label.code" :options="settings_layout_options" v-model="settings_layout2" @input="cookieLayoutChange2"></v-select>
									<input type="hidden" name="gdpr-cookie-settings-layout2" v-model="settings_layout2">
								</c-col>
								<c-col v-show="!is_banner" class="col-sm-6" >
									<v-select class="form-group" id="gdpr-cookie-settings-layout2" :reduce="label => label.code" :options="settings_layout_options_extended" v-model="settings_layout2" @input="cookieLayoutChange2"></v-select>
									<input type="hidden" name="gdpr-cookie-settings-layout2" v-model="settings_layout2">
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
					<!-- add custom css card  -->
					<?php
					$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ? GDPR_COOKIE_CONSENT_VERSION : '';
					if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
						if ( $is_pro_active ) {
							do_action( 'gdpr_custom_css' );
						} else {
							?>
									<c-card >
									<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Add Your Custom CSS', 'gdpr-cookie-consent' ); ?>

									<div class="gdpr-pro-label absolute" style="top: 10px; right: 750px;"><div class="gdpr-pro-label-text">Pro</div></div>

									</c-card-header>
									<c-card-body>
										<c-col class="col-sm-12">
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
									</c-card-body>
								</c-card>
							<?php
						}
					} else {
						?>
					<c-card >
					<c-card-header class="gdpr-cookie-consent-design-subheading"><?php esc_html_e( 'Add Your Custom CSS', 'gdpr-cookie-consent' ); ?></c-card-header>
					<c-card-body>
						<c-col class="col-sm-12">
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

					</c-card-body>
					</c-card>
									<?php
					}
					?>
							</c-card-body>	
							
					</c-card>
				</c-tab>
				<c-tab v-show="is_gdpr" title="<?php esc_attr_e( 'Cookie List', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#cookie_list" 	id="gdpr-cookie-consent-cookies-list">
					<div class="gdpr-cookie-list-tabs">
						<c-button class="gdpr-cookie-consent-cookie-list-tab" @click="onChangeCookieListTab":class="{ 'gdpr-cookie-consent-cookie-list-tab-active': cookie_list_tab == true }"><?php esc_html_e( 'Custom Cookies', 'gdpr-cookie-consent' ); ?></c-button>
						<c-button class="gdpr-cookie-consent-cookie-list-tab"  @click="onChangeDiscoveredListTab":class="{ 'gdpr-cookie-consent-cookie-list-tab-active': discovered_cookies_list_tab == true }"><?php esc_html_e( 'Discovered Cookies', 'gdpr-cookie-consent' ); ?></c-button>
						<c-button class="gdpr-cookie-consent-cookie-list-tab"  @click="onChangeScanHistoryTab":class="{ 'gdpr-cookie-consent-cookie-list-tab-active': scan_history_list_tab == true }"><?php esc_html_e( 'Scan History', 'gdpr-cookie-consent' ); ?></c-button>
					</div>
					<div class="gdpr-cookie-list-tabs-container" v-show="cookie_list_tab == true">
						<img class="gdpr-cookie-list-tabs-logo"src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cookie_setting_preview.svg'; ?>" alt="Cookie Setting preview logo">
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
						<?php do_action( 'gdpr_cookie_scanner_card' ); ?>
					</c-card>
					<c-card v-show="scan_history_list_tab == true">
						<?php do_action( 'gdpr_cookie_scanned_history' ); ?>
					</c-card>
				</c-tab>
				<!-- Script Blocker -->
				<?php do_action( 'gdpr_settings_script_blocker_tab' ); ?>
				<!--A/B Testing-->
				<?php do_action( 'gdpr_settings_ab_testing_tab' ); ?>
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
				</c-tabs>
			</div>
		</c-form>
	</c-container>
</div>
<div id="gdpr-mascot-app"></div>
<?php
