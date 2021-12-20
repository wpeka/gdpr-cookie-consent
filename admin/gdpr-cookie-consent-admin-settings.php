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
?>
<div class="gdpr-cookie-consent-app-container" id="gdpr-cookie-consent-settings-app">
	<c-container class="gdpr-cookie-consent-settings-container">
		<c-form id="gcc-save-settings-form" spellcheck="false" class="gdpr-cookie-consent-settings-form">
			<input type="hidden" name="gcc_settings_form_nonce" value="<?php echo wp_create_nonce( 'gcc-settings-form-nonce' ); ?>"/>
			<div class="gdpr-cookie-consent-settings-top">
				<div class="gdpr-cookie-consent-save-button">
					<c-button color="info" @click="saveCookieSettings"><span>Save Changes</span></c-button>
				</div>
			</div>
			<div class="gdpr-cookie-consent-settings-content">
				<div id="gdpr-cookie-consent-save-settings-alert">Settings saved</div>	
				<c-tabs variant="pills" ref="active_tab" class="gdpr-cookie-consent-settings-nav">
					<c-tab title="<?php esc_attr_e( 'Compliances', 'gdpr-cookie-consent' ); ?>" href="#compliances">
						<c-card>
							<c-card-header><?php esc_html_e( 'Cookie Notice', 'gdpr-cookie-consent' ); ?></c-card-header>
							<c-card-body>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Cookie Notice', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Turn this on to enable cookie bar on your website.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="cookie_is_on" id="gdpr-cookie-consent-cookie-on" variant="3d"  color="success" :checked="cookie_is_on" v-on:update:checked="onSwitchCookieEnable"></c-switch>
										<input type="hidden" name="gcc-cookie-enable" v-model="cookie_is_on">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Select the Type of Law', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the compliance category.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-consent-policy-type" :reduce="label => label.code" :options="policy_options" v-model="gdpr_policy" @input="cookiePolicyChange">
										</v-select>
										<input type="hidden" name="gcc-gdpr-policy" v-model="gdpr_policy">
									</c-col>
								</c-row>
								<c-row v-show="is_gdpr">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Message Heading', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Leave it blank, If you do not need a heading.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-textarea name="gcc-gdpr-msg-heading" v-model="gdpr_message_heading"></c-textarea>
									</c-col>
								</c-row>
								<c-row v-show="is_eprivacy">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Message', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enter the text you want to display as ePrivacy notice.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-textarea name="gcc-eprivacy-msg" v-model="eprivacy_message"></c-textarea>
									</c-col>
								</c-row>
								<c-row v-show="is_gdpr">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'GDPR Message', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enter the message you want to display on your cookie notice', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-textarea name="gcc-gdpr-msg" v-model="gdpr_message"></c-textarea>
									</c-col>
								</c-row>
								<c-row v-show="is_ccpa">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'CCPA Message', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enter the text you want to display as CCPA notice.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-textarea name="gcc-ccpa-msg" v-model="ccpa_message"></c-textarea>
									</c-col>
								</c-row>
								<c-row v-show="is_gdpr">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'About Cookies Message', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Text shown under "About Cookies" section when users click on "Cookie Settings" button.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-textarea :rows="6" name="gcc-gdpr-about-cookie-msg" v-model="gdpr_about_cookie_message"></c-textarea>
									</c-col>
								</c-row>
							</c-card-body>
						</c-card>
						<c-card v-show="show_visitor_conditions">
							<c-card-header><?php esc_html_e( 'Enable Visitor Conditions', 'gdpr-cookie-consent' ); ?></c-card-header>
							<c-card-body>
								<c-row v-show="is_ccpa">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable IAB Transparency and Consent Framework (TCF)', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enable compatibility for the customization of advertising tracking preferences in case of CCPA.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="is_iab_on" id="gdpr-cookie-consent-iab-on" variant="3d"  color="success" :checked="is_iab_on" v-on:update:checked="onSwitchIABEnable"></c-switch>
										<input type="hidden" name="gcc-iab-enable" v-model="is_iab_on">
									</c-col>
								</c-row>
								<?php do_action( 'gdpr_enable_visitor_features' ); ?>
							</c-card-body>
						</c-card>
						<c-card v-show="show_revoke_card">
							<c-card-header><?php esc_html_e( 'Privacy Policy Settings', 'gdpr-cookie-consent' ); ?></c-card-header>
							<c-card-body>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Privacy Policy Link', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enable this to provide a link to your Privacy & Cookie Policy on your Cookie Notice', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="button_readmore_is_on" id="gdpr-cookie-consent-readmore-is-on" variant="3d"  color="success" :checked="button_readmore_is_on" v-on:update:checked="onSwitchButtonReadMoreIsOn"></c-switch>
										<input type="hidden" name="gcc-readmore-is-on" v-model="button_readmore_is_on">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enter the text of the privacy policy button/link.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-input name="gcc-readmore-text" v-model="button_readmore_text"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the colour of the text.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="button_readmore_link_color"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-readmore-link-color" type="color" name="gcc-readmore-link-color" v-model="button_readmore_link_color"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Show as', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Choose whether to show as a button or link.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gcc-readmore-as-button" :reduce="label => label.code" :options="show_as_options" v-model="button_readmore_as_button"></v-select>
										<input type="hidden" name="gcc-readmore-as-button" v-model="button_readmore_as_button">	
									</c-col>
								</c-row>
								<c-row v-show="button_readmore_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select background color .', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="button_readmore_button_color"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-readmore-button-color" type="color" name="gcc-readmore-button-color" v-model="button_readmore_button_color"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="button_readmore_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select background opacity.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="button_readmore_button_opacity"></c-input> 
										<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-opacity" v-model="button_readmore_button_opacity"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="button_readmore_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select border style .', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gcc-readmore-button-border-style" :reduce="label => label.code" :options="border_style_options" v-model="button_readmore_button_border_style"></v-select>
										<input type="hidden" name="gcc-readmore-button-border-style" v-model="button_readmore_button_border_style">	
									</c-col>
								</c-row>
								<c-row v-show="button_readmore_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select border width.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="button_readmore_button_border_width"></c-input> 
										<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-border-width" v-model="button_readmore_button_border_width"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="button_readmore_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select border color.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
										<c-input class="gdpr-color-input" type="text" v-model="button_readmore_button_border_color"></c-input>
										<c-input class="gdpr-color-select" id="gdpr-readmore-button-border-color" type="color" name="gcc-readmore-button-border-color" v-model="button_readmore_button_border_color"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="button_readmore_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select border radius.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
										<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="button_readmore_button_border_radius"></c-input> 
										<c-input class="gdpr-slider-input"type="number" name="gcc-readmore-button-border-radius" v-model="button_readmore_button_border_radius"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="button_readmore_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Size', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gcc-readmore-button-size" :reduce="label => label.code" :options="button_size_options" v-model="button_readmore_button_size"></v-select>
										<input type="hidden" name="gcc-readmore-button-size" v-model="button_readmore_button_size">	
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Page or Custom URL', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select any existing privacy policy page or enter page URL.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gcc-readmore-url-type" :reduce="label => label.code" :options="url_type_options" v-model="button_readmore_url_type"></v-select>
										<input type="hidden" name="gcc-readmore-url-type" v-model="button_readmore_url_type">	
									</c-col>
								</c-row>
								<c-row v-show="button_readmore_url_type">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Page', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select any existing Privacy policy page.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group"  placeholder="Select Policy Page" id="gcc-readmore-page" :reduce="label => label.code" :options="privacy_policy_options" v-model="readmore_page" @input="onSelectPrivacyPage"></v-select>
										<input type="hidden" name="gcc-readmore-page" v-model="button_readmore_page">	
									</c-col>
								</c-row>
								<c-row v-show="button_readmore_url_type">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Synchronize with WordPress Policy Page', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'If enabled visitor will be redirected to Privacy Policy Page set in WordPress settings irrespective of Page set in the previous setting.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="button_readmore_wp_page" id="gdpr-cookie-consent-readmore-wp-page" variant="3d"  color="success" :checked="button_readmore_wp_page" v-on:update:checked="onSwitchButtonReadMoreWpPage"></c-switch>
										<input type="hidden" name="gcc-readmore-wp-page" v-model="button_readmore_wp_page">
									</c-col>
								</c-row>
								<c-row v-show="!button_readmore_url_type">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'URL', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enter Page URL.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-input name="gcc-readmore-url" v-model="button_readmore_url"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Open URL in New Window?', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Choose whether you want the URL to open in a new window.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="button_readmore_new_win" id="gdpr-cookie-consent-readmore-new-win" variant="3d"  color="success" :checked="button_readmore_new_win" v-on:update:checked="onSwitchButtonReadMoreNewWin"></c-switch>
										<input type="hidden" name="gcc-readmore-new-win" v-model="button_readmore_new_win">
									</c-col>
								</c-row>
							</c-card-body>
						</c-card>
						<c-card v-show="show_revoke_card">
							<c-card-header><?php esc_html_e( 'Revoke Consent', 'gdpr-cookie-consent' ); ?></c-card-header>
							<c-card-body>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Revoke Consent', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enable to give user the option to revoke their consent.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="is_revoke_consent_on" id="gdpr-cookie-consent-revoke-consent" variant="3d"  color="success" :checked="is_revoke_consent_on" v-on:update:checked="onSwitchRevokeConsentEnable"></c-switch>
										<input type="hidden" name="gcc-revoke-consent-enable" v-model="is_revoke_consent_on">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Tab Position', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select whether the tab position will be on the left or right side of the website.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-consent-tab-position" :reduce="label => label.code" :options="tab_position_options" v-model="tab_position">
										</v-select>
										<input type="hidden" name="gcc-tab-position" v-model="tab_position">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Tab margin (in percent)', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Specify margin in pixels or percentage.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-input type="number" min="0" max="100" name="gcc-tab-margin" v-model="tab_margin"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Tab Text', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'The text to be displayed on the revoke consent tab.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-input name="gcc-tab-text" v-model="tab_text"></c-input>
									</c-col>
								</c-row>
							</c-card-body>
						</c-card>
						<c-card>
							<c-card-header><?php esc_html_e( 'Consent Settings', 'gdpr-cookie-consent' ); ?></c-card-header>
							<c-card-body>
								<?php do_action( 'gdpr_consent_settings_pro_top' ); ?>
								<c-row v-show="is_gdpr">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Autotick for Non-Necessary Cookies ', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Pre-select non-necessary cookie checkboxes.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="autotick" id="gdpr-cookie-consent-autotick" variant="3d"  color="success" :checked="autotick" v-on:update:checked="onSwitchAutotick"></c-switch>
										<input type="hidden" name="gcc-autotick" v-model="autotick">
									</c-col>
								</c-row>
								<c-row v-show="show_revoke_card">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Auto Hide (Accept)', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'If enabled Cookie Bar will be automatically hidden after specified time and cookie preferences will be set as accepted.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="auto_hide" id="gdpr-cookie-consent-auto_hide" variant="3d"  color="success" :checked="auto_hide" v-on:update:checked="onSwitchAutoHide"></c-switch>
										<input type="hidden" name="gcc-auto-hide" v-model="auto_hide">
									</c-col>
								</c-row>
								<c-row v-show="auto_hide">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Auto Hide Delay (in milliseconds)', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Time after which Cookie Bar will be automatically hidden.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-input type="number" min="5000" max="60000" step="1000" name="gcc-auto-hide-delay" v-model="auto_hide_delay"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="show_revoke_card">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Auto Scroll (Accept)', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( ' If enabled, Cookie Bar will automatically hide after the visitor scrolls the webpage and consent will be automatically accepted as Yes.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="auto_scroll" id="gdpr-cookie-consent-auto_scroll" variant="3d"  color="success" :checked="auto_scroll" v-on:update:checked="onSwitchAutoScroll"></c-switch>
										<input type="hidden" name="gcc-auto-scroll" v-model="auto_scroll">
									</c-col>
								</c-row>
								<c-row v-show="auto_scroll">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Auto Scroll Offset (in percent)', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Auto Scroll setting will affect after this much percentage of webpage is scrolled.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-input type="number" min="1" max="100" name="gcc-auto-scroll-offset" v-model="auto_scroll_offset"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="show_revoke_card">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Reload After Scroll Accept', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'If enabled, the web page will be refreshed automatically once cookie settings are accepted because of scrolling.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="auto_scroll_reload" id="gdpr-cookie-consent-auto-scroll-reload" variant="3d"  color="success" :checked="auto_scroll_reload" v-on:update:checked="onSwitchAutoScrollReload"></c-switch>
										<input type="hidden" name="gcc-auto-scroll-reload" v-model="auto_scroll_reload">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Reload After Accept', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'If enabled web page will be refreshed automatically once cookie settings are accepted.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="accept_reload" id="gdpr-cookie-consent-accept-reload" variant="3d"  color="success" :checked="accept_reload" v-on:update:checked="onSwitchAcceptReload"></c-switch>
										<input type="hidden" name="gcc-accept-reload" v-model="accept_reload">
									</c-col>
								</c-row>
								<c-row  v-show="show_revoke_card">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Reload After Decline', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'If enabled web page will be refreshed automatically once cookie settings are declined.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="decline_reload" id="gdpr-cookie-consent-decline-reload" variant="3d"  color="success" :checked="decline_reload" v-on:update:checked="onSwitchDeclineReload"></c-switch>
										<input type="hidden" name="gcc-decline-reload" v-model="decline_reload">
									</c-col>
								</c-row>
								<?php do_action( 'gdpr_consent_settings_pro_bottom' ); ?>
							</c-card-body>
						</c-card>
						<c-card>
							<c-card-header><?php esc_html_e( 'Extra Settings', 'gdpr-cookie-consent' ); ?></c-card-header>
							<c-card-body>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Delete Plugin Data on Deactivation', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enable if you want all plugin data to be deleted on deactivation.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="delete_on_deactivation" id="gdpr-cookie-consent-delete-on-deactivation" variant="3d"  color="success" :checked="delete_on_deactivation" v-on:update:checked="onSwitchDeleteOnDeactivation"></c-switch>
										<input type="hidden" name="gcc-delete-on-deactivation" v-model="delete_on_deactivation">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Show Credits', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'If you are happy with the product and want to share credit with the developer, you can display credits under the Cookie Settings.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="show_credits" id="gdpr-cookie-consent-show-credits" variant="3d"  color="success" :checked="show_credits" v-on:update:checked="onSwitchShowCredits"></c-switch>
										<input type="hidden" name="gcc-show-credits" v-model="show_credits">
									</c-col>
								</c-row>
								<c-row  v-show="show_revoke_card">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cookie Expiry', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'The amount of time that the cookie should be stored for.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-consent-cookie-expiry" :reduce="label => label.code" :options="cookie_expiry_options" v-model="cookie_expiry">
										</v-select>
										<input type="hidden" name="gcc-cookie-expiry" v-model="cookie_expiry">
									</c-col>
								</c-row>
							</c-card-body>
						</c-card>
					</c-tab>
					<c-tab title="<?php esc_attr_e( 'Configuration', 'gdpr-cookie-consent' ); ?>" href="#configuration">
						<c-card>
							<c-card-header><?php esc_html_e( 'Configure Cookie Bar', 'gdpr-cookie-consent' ); ?></c-card-header>
							<c-card-body>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Show Cookie Notice as', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<input type="hidden" name="show-cookie-as" v-model="show_cookie_as">
										<v-select class="form-group" id="gdpr-show-cookie-as" :reduce="label => label.code" :options="show_cookie_as_options" v-model="show_cookie_as"  @input="cookieTypeChange"></v-select>
									</c-col>
								</c-row>
								<c-row v-show="show_cookie_as === 'banner'">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Position', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
									<v-select class="form-group" id="gdpr-cookie-consent-position" :reduce="label => label.code" :options="cookie_position_options" v-model="cookie_position"></v-select>
									<input type="hidden" name="gcc-gdpr-cookie-position" v-model="cookie_position">
									</c-col>
								</c-row>
								<c-row v-show="show_cookie_as === 'widget'">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Position', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
									<v-select class="form-group" id="gcc-gdpr-cookie-widget-position" :reduce="label => label.code" :options="cookie_widget_position_options" v-model="cookie_widget_position"></v-select>
									<input type="hidden" name="gcc-gdpr-cookie-widget-position" v-model="cookie_widget_position">
									</c-col>
								</c-row>
								<c-row v-show="show_cookie_as === 'popup'">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Add Overlay', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="cookie_accept_on" id="gdpr-cookie-add-overlay" variant="3d"  color="success" :checked="cookie_add_overlay" v-on:update:checked="onSwitchAddOverlay"></c-switch>
										<input type="hidden" name="gdpr-cookie-add-overlay" v-model="cookie_add_overlay">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'On Hide', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
									<v-select class="form-group" id="gdpr-cookie-consent-on-hide" :reduce="label => label.code" :options="on_hide_options" v-model="on_hide"></v-select>
									<input type="hidden" name="gcc-gdpr-cookie-on-hide" v-model="on_hide">	
									</c-col>
								</c-row>
							</c-card-body>
						</c-card>
						<c-card>
							<c-card-header><?php esc_html_e( 'Cookie Bar Body Design', 'gdpr-cookie-consent' ); ?></c-card-header>
							<c-card-body>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cookie Bar Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="cookie_bar_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-bar-color" type="color" name="gdpr-cookie-bar-color" v-model="cookie_bar_color"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( ' Cookie Bar Opacity', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="cookie_bar_opacity"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-bar-opacity" v-model="cookie_bar_opacity"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="cookie_text_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-text-color" type="color" name="gdpr-cookie-text-color" v-model="cookie_text_color"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Styles', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-border-style" :reduce="label => label.code" :options="border_style_options" v-model="border_style">
										</v-select>
										<input type="hidden" name="gdpr-cookie-border-style" v-model="border_style">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="cookie_bar_border_width"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-bar-border-width" v-model="cookie_bar_border_width"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="cookie_border_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-border-color" type="color" name="gdpr-cookie-border-color" v-model="cookie_border_color"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="cookie_bar_border_radius"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-bar-border-radius" v-model="cookie_bar_border_radius"></c-input>
									</c-col>
								</c-row>	
							</c-card-body>
						</c-card>
						<c-card>
							<c-card-header><?php esc_html_e( 'Accept Button', 'gdpr-cookie-consent' ); ?></c-card-header>
							<c-card-body>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="cookie_accept_on" id="gdpr-cookie-consent-cookie-on" variant="3d"  color="success" :checked="cookie_accept_on" v-on:update:checked="onSwitchCookieAcceptEnable"></c-switch>
										<input type="hidden" name="gcc-cookie-accept-enable" v-model="cookie_accept_on">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-input name="gcc-accept-text" v-model="accept_text"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="accept_text_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-text-color" type="color" name="gdpr-cookie-accept-text-color" v-model="accept_text_color"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8" >
										<v-select class="form-group" id="gdpr-cookie-accept-as-button" :reduce="label => label.code" :options="accept_as_button_options" v-model="accept_as_button"></v-select>
										<input type="hidden" name="gdpr-cookie-accept-as" v-model="accept_as_button">
									</c-col>
								</c-row>
								<c-row v-show="accept_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="accept_background_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-background-color" type="color" name="gdpr-cookie-accept-background-color" v-model="accept_background_color"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="accept_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="accept_opacity"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-opacity" v-model="accept_opacity"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="accept_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-accept-border-style" :reduce="label => label.code" :options="border_style_options" v-model="accept_style">
										</v-select>
										<input type="hidden" name="gdpr-cookie-accept-border-style" v-model="accept_style">
									</c-col>
								</c-row>
								<c-row v-show="accept_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="accept_border_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-accept-border-color" type="color" name="gdpr-cookie-accept-border-color" v-model="accept_border_color"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="accept_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="accept_border_width"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-border-width" v-model="accept_border_width"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="accept_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="accept_border_radius"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-accept-border-radius" v-model="accept_border_radius"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="accept_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Button Size', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the size of button.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-accept-size" :reduce="label => label.code" :options="accept_size_options" v-model="accept_size">
										</v-select>
										<input type="hidden" name="gdpr-cookie-accept-size" v-model="accept_size">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Action ', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-accept-action" :reduce="label => label.code" :options="accept_action_options" v-model="accept_action"  @input="cookieAcceptChange">
										</v-select>
										<input type="hidden" name="gdpr-cookie-accept-action" v-model="accept_action">
									</c-col>
								</c-row>
								<c-row v-show="is_open_url">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enter Page URL.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-input name="gdpr-cookie-accept-url" v-model="accept_url"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="is_open_url">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
									<v-select class="form-group" id="gdpr-cookie-url-new-window" :reduce="label => label.code" :options="open_url_options" v-model="open_url"></v-select>
									<input type="hidden" name="gdpr-cookie-url-new-window" v-model="open_url">
								</c-row>
							</c-card-body>
						</c-card>
						<c-card>
							<c-card-header><?php esc_html_e( 'Decline Button', 'gdpr-cookie-consent' ); ?></c-card-header>
							<c-card-body>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="cookie_decline_on" id="gdpr-cookie-consent-decline-on" variant="3d"  color="success" :checked="cookie_decline_on" v-on:update:checked="onSwitchCookieDeclineEnable"></c-switch>
										<input type="hidden" name="gcc-cookie-decline-enable" v-model="cookie_decline_on">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-input name="gcc-decline-text" v-model="decline_text"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="decline_text_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-decline-text-color" type="color" name="gdpr-cookie-decline-text-color" v-model="decline_text_color"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8" >
										<v-select class="form-group" id="gdpr-cookie-decline-as-button" :reduce="label => label.code" :options="accept_as_button_options" v-model="decline_as_button"></v-select>
										<input type="hidden" name="gdpr-cookie-decline-as" v-model="decline_as_button">
									</c-col>
								</c-row>
								<c-row v-show="decline_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="decline_background_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-decline-background-color" type="color" name="gdpr-cookie-decline-background-color" v-model="decline_background_color"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="decline_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="decline_opacity"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-decline-opacity" v-model="decline_opacity"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="decline_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-decline-border-style" :reduce="label => label.code" :options="border_style_options" v-model="decline_style">
										</v-select>
										<input type="hidden" name="gdpr-cookie-decline-border-style" v-model="decline_style">
									</c-col>
								</c-row>
								<c-row v-show="decline_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="decline_border_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-decline-border-color" type="color" name="gdpr-cookie-decline-border-color" v-model="decline_border_color"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="decline_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="decline_border_width"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-decline-border-width" v-model="decline_border_width"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="decline_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="decline_border_radius"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-decline-border-radius" v-model="decline_border_radius"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="decline_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Size', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-decline-size" :reduce="label => label.code" :options="accept_size_options" v-model="decline_size">
										</v-select>
										<input type="hidden" name="gdpr-cookie-decline-size" v-model="decline_size">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Action ', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-decline-action" :reduce="label => label.code" :options="decline_action_options" v-model="decline_action" @input="cookieDeclineChange">
										</v-select>
										<input type="hidden" name="gdpr-cookie-decline-action" v-model="decline_action">
									</c-col>
								</c-row>
								<c-row v-show="decline_open_url">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'URL ', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-input name="gdpr-cookie-decline-url" v-model="decline_url"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="decline_open_url">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Open URL in new window', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
									<v-select class="form-group" id="gdpr-cookie-decline-url-new-window" :reduce="label => label.code" :options="open_url_options" v-model="open_decline_url"></v-select>
									<input type="hidden" name="gdpr-cookie-decline-url-new-window" v-model="open_decline_url">
								</c-row>
							</c-card-body>
						</c-card>
						<c-card>
							<c-card-header><?php esc_html_e( 'Settings Button', 'gdpr-cookie-consent' ); ?></c-card-header>
							<c-card-body>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="cookie_settings_on" id="gdpr-cookie-consent-settings-on" variant="3d"  color="success" :checked="cookie_settings_on" v-on:update:checked="onSwitchCookieSettingsEnable"></c-switch>
										<input type="hidden" name="gcc-cookie-settings-enable" v-model="cookie_settings_on">
									</c-col>
								</c-row>
								<c-row v-show="is_banner">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cookie Settings Layout', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8" >
										<v-select class="form-group" id="gdpr-cookie-settings-layout" :reduce="label => label.code" :options="settings_layout_options" v-model="settings_layout"></v-select>
										<input type="hidden" name="gdpr-cookie-settings-layout" v-model="settings_layout">
									</c-col>
								</c-row>
								<c-row v-show="!is_banner">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Cookie Settings Layout', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8" >
										<v-select class="form-group" id="gdpr-cookie-settings-layout" :reduce="label => label.code" :options="settings_layout_options_extended" v-model="settings_layout"></v-select>
										<input type="hidden" name="gdpr-cookie-settings-layout" v-model="settings_layout">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-input name="gcc-settings-text" v-model="settings_text"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="settings_text_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-settings-text-color" type="color" name="gdpr-cookie-settings-text-color" v-model="settings_text_color"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Show As', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8" >
										<v-select class="form-group" id="gdpr-cookie-settings-as-button" :reduce="label => label.code" :options="accept_as_button_options" v-model="settings_as_button"></v-select>
										<input type="hidden" name="gdpr-cookie-settings-as" v-model="settings_as_button">
									</c-col>
								</c-row>
								<c-row v-show="settings_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="settings_background_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-settings-background-color" type="color" name="gdpr-cookie-settings-background-color" v-model="settings_background_color"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="settings_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="settings_opacity"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-settings-opacity" v-model="settings_opacity"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="settings_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-settings-border-style" :reduce="label => label.code" :options="border_style_options" v-model="settings_style">
										</v-select>
										<input type="hidden" name="gdpr-cookie-settings-border-style" v-model="settings_style">
									</c-col>
								</c-row>
								<c-row v-show="settings_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="settings_border_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-settings-border-color" type="color" name="gdpr-cookie-settings-border-color" v-model="settings_border_color"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="settings_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="settings_border_width"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-settings-border-width" v-model="settings_border_width"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="settings_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="settings_border_radius"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-settings-border-radius" v-model="settings_border_radius"></c-input>
									</c-col>
								</c-row>
								<c-row v-show="settings_as_button">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Size', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-settings-size" :reduce="label => label.code" :options="accept_size_options" v-model="settings_size">
										</v-select>
										<input type="hidden" name="gdpr-cookie-settings-size" v-model="settings_size">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Display Cookies List on Frontend', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="cookie_on_frontend" id="gdpr-cookie-consent-cookie-on-frontend" variant="3d"  color="success" :checked="cookie_on_frontend" v-on:update:checked="onSwitchCookieOnFrontend"></c-switch>
										<input type="hidden" name="gcc-cookie-on-frontend" v-model="cookie_on_frontend">
									</c-col>
								</c-row>
							</c-card-body>
						</c-card>
						<c-card>
							<c-card-header><?php esc_html_e( 'Confirm Button', 'gdpr-cookie-consent' ); ?></c-card-header>
							<c-card-body>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-input name="gcc-confirm-text" v-model="confirm_text"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="confirm_text_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-confirm-text-color" type="color" name="gdpr-cookie-confirm-text-color" v-model="confirm_text_color"></c-input>
									</c-col>
								</c-row>
								<c-row >
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="confirm_background_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-confirm-background-color" type="color" name="gdpr-cookie-confirm-background-color" v-model="confirm_background_color"></c-input>
									</c-col>
								</c-row>
								<c-row >
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="confirm_opacity"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-confirm-opacity" v-model="confirm_opacity"></c-input>
									</c-col>
								</c-row>
								<c-row >
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-confirm-border-style" :reduce="label => label.code" :options="border_style_options" v-model="confirm_style">
										</v-select>
										<input type="hidden" name="gdpr-cookie-confirm-border-style" v-model="confirm_style">
									</c-col>
								</c-row>
								<c-row >
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="confirm_border_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-confirm-border-color" type="color" name="gdpr-cookie-confirm-border-color" v-model="settings_border_color"></c-input>
									</c-col>
								</c-row>
								<c-row >
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="confirm_border_width"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-confirm-border-width" v-model="confirm_border_width"></c-input>
									</c-col>
								</c-row>
								<c-row >
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="confirm_border_radius"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-confirm-border-radius" v-model="confirm_border_radius"></c-input>
									</c-col>
								</c-row>
								<c-row >
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Size', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-confirm-size" :reduce="label => label.code" :options="accept_size_options" v-model="confirm_size">
										</v-select>
										<input type="hidden" name="gdpr-cookie-confirm-size" v-model="confirm_size">
									</c-col>
								</c-row>
							</c-card-body>
						</c-card>
						<c-card>
							<c-card-header><?php esc_html_e( 'Cancel Button', 'gdpr-cookie-consent' ); ?></c-card-header>
							<c-card-body>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-input name="gcc-cancel-text" v-model="cancel_text"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="cancel_text_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-cancel-text-color" type="color" name="gdpr-cookie-cancel-text-color" v-model="cancel_text_color"></c-input>
									</c-col>
								</c-row>
								<c-row >
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="cancel_background_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-cancel-background-color" type="color" name="gdpr-cookie-cancel-background-color" v-model="cancel_background_color"></c-input>
									</c-col>
								</c-row>
								<c-row >
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Background Opacity', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="1" step="0.01" v-model="cancel_opacity"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-cancel-opacity" v-model="cancel_opacity"></c-input>
									</c-col>
								</c-row>
								<c-row >
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Style', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-cancel-border-style" :reduce="label => label.code" :options="border_style_options" v-model="cancel_style">
										</v-select>
										<input type="hidden" name="gdpr-cookie-cancel-border-style" v-model="cancel_style">
									</c-col>
								</c-row>
								<c-row >
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="cancel_border_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-cancel-border-color" type="color" name="gdpr-cookie-cancel-border-color" v-model="settings_border_color"></c-input>
									</c-col>
								</c-row>
								<c-row >
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Width', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="10" step="0.5" v-model="cancel_border_width"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-cancel-border-width" v-model="cancel_border_width"></c-input>
									</c-col>
								</c-row>
								<c-row >
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Border Radius', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick">
									<c-input class="gdpr-slider-select" type="range" min="0" max="100" step="0.5" v-model="cancel_border_radius"></c-input> 
									<c-input class="gdpr-slider-input"type="number" name="gdpr-cookie-cancel-border-radius" v-model="cancel_border_radius"></c-input>
									</c-col>
								</c-row>
								<c-row >
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Size', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-cancel-size" :reduce="label => label.code" :options="accept_size_options" v-model="cancel_size">
										</v-select>
										<input type="hidden" name="gdpr-cookie-cancel-size" v-model="cancel_size">
									</c-col>
								</c-row>
							</c-card-body>
						</c-card>
						<c-card>
							<c-card-header><?php esc_html_e( 'Opt-out link', 'gdpr-cookie-consent' ); ?></c-card-header>
							<c-card-body>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-input name="gcc-opt-out-text" v-model="opt_out_text"></c-input>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Text Color', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8 gdpr-color-pick" >
									<c-input class="gdpr-color-input" type="text" v-model="opt_out_text_color"></c-input>
									<c-input class="gdpr-color-select" id="gdpr-cookie-opt-out-text-color" type="color" name="gdpr-cookie-opt-out-text-color" v-model="opt_out_text_color"></c-input>
									</c-col>
								</c-row>	
							</c-card-body>
						</c-card>
					</c-tab>
					<c-tab v-show="show_revoke_card" title="<?php esc_attr_e( 'Script Blocker', 'gdpr-cookie-consent' ); ?>" href="#script_blocker">
						<c-card>
								<c-card-header><?php esc_html_e( 'Script Blocker Settings', 'gdpr-cookie-consent' ); ?></c-card-header>
								<c-card-body>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Script Blocker is Currently', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enable to turn on the script blocker.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
										<c-col class="col-sm-8">
											<c-switch v-bind="labelIcon" v-model="is_script_blocker_on" id="gdpr-cookie-consent-script-blocker-on" variant="3d"  color="success" :checked="is_script_blocker_on" v-on:update:checked="onSwitchScriptBlocker"></c-switch>
											<input type="hidden" name="gcc-script-blocker-on" v-model="is_script_blocker_on">
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Custom Scripts', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
										<c-col class="col-sm-8">
											<div role="group" class="form-group">
											<span class="gdpr-cookie-consent-description"><?php esc_attr_e( 'Enter non functional cookies javascript code here (for e.g. Google Analytics) to be used after the consent is accepted.', 'gdpr-cookie-consent' ); ?></span>
											</div>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Header Scripts', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enter the title of your cookie notice. Leave it blank, if you do not need a heading.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea :rows="4" name="gcc-header-scripts" v-model="header_scripts"></c-textarea>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Body Scripts', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enter the title of your cookie notice. Leave it blank, if you do not need a heading.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea :rows="4" name="gcc-body-scripts" v-model="body_scripts"></c-textarea>
										</c-col>
									</c-row>
									<c-row>
										<c-col class="col-sm-4"><label><?php esc_attr_e( 'Footer Scripts', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enter the title of your cookie notice. Leave it blank, if you do not need a heading.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
										<c-col class="col-sm-8">
											<c-textarea :rows="4" name="gcc-footer-scripts" v-model="footer_scripts"></c-textarea>
										</c-col>
									</c-row>
									<?php do_action( 'gdpr_settings_script_blocker_card' ); ?>
								</c-card-body>
						</c-card>
					</c-tab>		
				</c-tabs>
			</div>
			<div class="gdpr-cookie-consent-settings-bottom">
				<div class="gdpr-cookie-consent-save-button">
					<c-button color="info" @click="saveCookieSettings"><span>Save Changes</span></c-button>
				</div>
			</div>
		</c-form>
	</c-container>
</div>
<?php
