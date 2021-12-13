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
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Cookie Notice', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="cookie_is_on" id="gdpr-cookie-consent-cookie-on" variant="3d"  color="success" :checked="cookie_is_on" v-on:update:checked="onSwitchCookieEnable"></c-switch>
										<input type="hidden" name="gcc-cookie-enable" v-model="cookie_is_on">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Select the type of law', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-consent-policy-type" :reduce="label => label.code" :options="policy_options" v-model="gdpr_policy" @input="cookiePolicyChange">
										</v-select>
										<input type="hidden" name="gcc-gdpr-policy" v-model="gdpr_policy">
									</c-col>
								</c-row>
								<c-row v-show="is_gdpr">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Message Heading', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enter the title of your cookie notice. Leave it blank, if you do not need a heading.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-textarea name="gcc-gdpr-msg-heading" v-model="gdpr_message_heading"></c-textarea>
									</c-col>
								</c-row>
								<c-row v-show="is_eprivacy">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Message', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-textarea name="gcc-eprivacy-msg" v-model="eprivacy_message"></c-textarea>
									</c-col>
								</c-row>
								<c-row v-show="is_gdpr">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'GDPR Message', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enter the title of your cookie notice. Leave it blank, if you do not need a heading.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-textarea name="gcc-gdpr-msg" v-model="gdpr_message"></c-textarea>
									</c-col>
								</c-row>
								<c-row v-show="is_ccpa">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'CCPA Message', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enter the title of your cookie notice. Leave it blank, if you do not need a heading.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-textarea name="gcc-ccpa-msg" v-model="ccpa_message"></c-textarea>
									</c-col>
								</c-row>
								<c-row v-show="is_gdpr">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'About Cookies Message', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enter the title of your cookie notice. Leave it blank, if you do not need a heading.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
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
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable IAB Transparency and Consent Framework (TCF)', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="is_iab_on" id="gdpr-cookie-consent-iab-on" variant="3d"  color="success" :checked="is_iab_on" v-on:update:checked="onSwitchIABEnable"></c-switch>
										<input type="hidden" name="gcc-iab-enable" v-model="is_iab_on">
									</c-col>
								</c-row>
								<?php do_action( 'gdpr_enable_visitor_features' ); ?>
							</c-card-body>
						</c-card>
						<c-card v-show="is_gdpr">
							<c-card-header><?php esc_html_e( 'Revoke Consent', 'gdpr-cookie-consent' ); ?></c-card-header>
							<c-card-body>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Show Again Tab', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Enable to give to the user the possibility to revoke their consent.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="is_revoke_consent_on" id="gdpr-cookie-consent-revoke-consent" variant="3d"  color="success" :checked="is_revoke_consent_on" v-on:update:checked="onSwitchRevokeConsentEnable"></c-switch>
										<input type="hidden" name="gcc-revoke-consent-enable" v-model="is_revoke_consent_on">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Tab Position', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
										<v-select class="form-group" id="gdpr-cookie-consent-tab-position" :reduce="label => label.code" :options="tab_position_options" v-model="tab_position">
										</v-select>
										<input type="hidden" name="gcc-tab-position" v-model="tab_position">
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
										<v-select class="form-group" id="gdpr-show-cookie-as" :reduce="label => label.code" :options="show_cookie_as_options" v-model="show_cookie_as"></v-select>
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Position', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
									<v-select class="form-group" id="gdpr-cookie-consent-position" :reduce="label => label.code" :options="cookie_position_options" v-model="cookie_position"></v-select>
									<input type="hidden" name="gcc-gdpr-cookie-position" v-model="cookie_position">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'On hide', 'gdpr-cookie-consent' ); ?> <c-icon name="cib-google-keep" color="primary" v-c-tooltip="'<?php esc_html_e( 'Select the font.', 'gdpr-cookie-consent' ); ?>'"></c-icon></label></c-col>
									<c-col class="col-sm-8">
									<v-select class="form-group" id="gdpr-cookie-consent-on-hide" :reduce="label => label.code" :options="on_hide_options" v-model="on_hide"></v-select>
									<input type="hidden" name="gcc-gdpr-cookie-on-hide" v-model="on_hide">	
									</c-col>
								</c-row>
							</c-card-body>
						</c-card>
						<c-card>
							<c-card-header><?php esc_html_e( 'Cookie Bar body design', 'gdpr-cookie-consent' ); ?></c-card-header>
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
									<c-col class="col-sm-8">
									</c-col>
								</c-row>
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
