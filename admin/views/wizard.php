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

$image_path = GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/';
$is_pro     = get_option( 'wpl_pro_active', false );
$ab_options = get_option('wpl_ab_options');
// Instantiate a new object of the GDPR_Cookie_Consent_Settings class.
$this->settings = new GDPR_Cookie_Consent_Settings();

// Call the methods from the instantiated object to get user parameters.
$is_user_connected      = $this->settings->is_connected();

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
function print_template_boxes( ) {

	$image_path = GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/';
	$is_pro     = get_option( 'wpl_pro_active', false );
	$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
	$the_options    = Gdpr_Cookie_Consent::gdpr_get_settings();
	$template  = json_decode($the_options['selected_template_json'], true);
	?>
	<div class="gdpr-templates-field-container-wizard">
			<div v-show = "show_cookie_as == 'widget' || show_cookie_as == 'popup' || '<?php echo esc_js($template['name']); ?>' !== 'blue_full'" class="gdpr-template-field gdpr-<?php echo esc_attr( $template['name'] ); ?>">
				
				<?php 

					$styles_attr = '';
					foreach ($template['styles'] as $key => $value) {
						if($key != 'opacity' && $key != 'is_on') $styles_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					} 
					$styles_attr .= "position: relative;";

					$accept_style_attr = '';
					foreach ($template['accept_button'] as $key => $value) {
						if($key != 'opacity' && $key != 'is_on') $accept_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					} 
					$accept_style_attr.= "padding: " . esc_attr($template['static-settings']['button_padding']) . ';';

					$accept_all_style_attr = '';
					foreach ($template['accept_all_button'] as $key => $value) {
						if($key != 'opacity' && $key != 'is_on') $accept_all_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					} 
					$accept_all_style_attr.= "padding: " . esc_attr($template['static-settings']['button_padding']) . ';';

					$decline_style_attr = '';
					foreach ($template['decline_button'] as $key => $value) {
						if($key != 'opacity' && $key != 'is_on') $decline_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					} 
					$decline_style_attr.= "padding: " . esc_attr($template['static-settings']['button_padding']) . ';';

					$settings_style_attr = '';
					foreach ($template['settings_button'] as $key => $value) {
						if($key != 'opacity' && $key != 'is_on') $settings_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					}  
					$settings_style_attr.= "padding: " . esc_attr($template['static-settings']['button_padding']) . ';';
					
					$logo_style_attr = '';
					foreach ($template['logo'] as $key => $value) {
						if($key != 'src') $logo_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					}  

					$readmore_style_attr = '';
					foreach ($template['readmore_button'] as $key => $value) {
						if($key == 'color') $readmore_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					}  
					$heading_style_attr = "";
					foreach ($template['heading'] as $key => $value) {
						$heading_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					}
					if(!isset($template['heading']['color'])) $heading_style_attr.= "color: inherit;";
					$decoration_styles_attr = '';
					if(isset($template['decoration'])) foreach ($template['decoration'] as $key => $value) {
						 $decoration_styles_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
					} 
				?>
				<div :class=" 'gdpr-right-field template-type-' + show_cookie_as ">
						<div style = "<?php echo esc_attr($styles_attr); ?>" class="cookie_notice_content">
							<span style="display: inline-flex; align-items: center; justify-content: center; position: absolute; top:10px; right: 10px; height: 15px; width: 15px; border-radius: 50%; color: <?php echo $template['accept_button']['background-color'] ?>; background-color: transparent;">
								<svg viewBox="0 0 24 24" fill="currentColor" width="15" height="15" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z" fill="currentColor"/>
								</svg>
							</span>
							<?php if($template['logo']['src'] !== '') { ?><img alt="WPCC Logo image" style = "<?php echo esc_attr($logo_style_attr); ?>" class="gdpr_logo_image" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'includes/templates/logo_images/' . $template['logo']['src']; ?>" > 
								<?php }else { ?>
									<p style="height: 20px;"></p>
								<?php } ?>

								<?php if($decoration_styles_attr !== ''){ ?>
									<div  style = "<?php echo esc_attr($decoration_styles_attr); ?>" class="gdpr_banner_decoration"></div>
								<?php } ?>
							<div class="<?php echo esc_attr($template['static-settings']['layout']);?>">
								<div style="display: flex; flex-direction: column; gap: 5px;">
								<?php
											if ( $the_options['cookie_usage_for'] === 'gdpr' || $the_options['cookie_usage_for'] === 'both' ) : ?>
												<h3 style = "<?php echo esc_attr($heading_style_attr); ?>" v-if="gdpr_message_heading.length>0">{{gdpr_message_heading}}</h3>
												<?php if( $template['name'] === 'blue_split' ){ ?><h3 style = "<?php echo esc_attr($heading_style_attr); ?>" v-if="gdpr_message_heading.length===0"><?php echo esc_html("We value your privacy"); ?></h3> <?php } ?>
											<?php elseif ( $the_options['cookie_usage_for'] === 'lgpd' ) : ?>
												<h3 style = "<?php echo esc_attr($heading_style_attr); ?>"  v-if="lgpd_message_heading.length>0">{{lgpd_message_heading}}</h3>
												<?php if( $template['name'] === 'blue_split' ){ ?><h3 style = "<?php echo esc_attr($heading_style_attr); ?>" v-if="lgpd_message_heading.length===0"><?php echo esc_html("We value your privacy"); ?></h3> <?php } ?>
											<?php elseif( $template['name'] === 'blue_split' ) : ?> <h3 style = "<?php echo esc_attr($heading_style_attr); ?>" ><?php echo esc_html("We value your privacy"); ?></h3>
											<?php endif; ?>
									<p>
										<?php if ( $the_options['cookie_usage_for'] === 'gdpr' || $the_options['cookie_usage_for'] === 'both' ) : ?>
											<span v-html ="gdpr_message"></span>
											<?php elseif ( $the_options['cookie_usage_for'] === 'lgpd' ) : ?>
											<span v-html ="lgpd_message"></span>
											<?php elseif ( $the_options['cookie_usage_for'] === 'ccpa' ) : ?>
											<span v-html ="ccpa_message"></span>
											<?php elseif ( $the_options['cookie_usage_for'] === 'eprivacy' ) : ?>
											<span v-html ="eprivacy_message"></span>
										<?php endif; ?>
										<a style = "<?php echo esc_attr($readmore_style_attr); ?>" >
											<?php if ( $the_options['cookie_usage_for'] === 'ccpa' ) : ?>
												{{ opt_out_text }}
											<?php else : ?>
												{{ button_readmore_text }}
											<?php endif; ?>
										</a>
									</p>
								</div>
									<?php if ( $the_options['cookie_usage_for'] !== 'ccpa' ) : ?>
										<div class="cookie_notice_buttons <?php echo esc_attr($template['static-settings']['layout']) . '-buttons';?>">
											<div class="left_buttons">
												<?php if($template["decline_button"]["is_on"]) : ?><a style="<?php echo esc_attr( $decline_style_attr ); ?>">{{ decline_text }}</a><?php endif;?>
												<?php if($template["settings_button"]["is_on"] && $the_options['cookie_usage_for'] !== 'eprivacy') : ?><a style="<?php echo esc_attr( $settings_style_attr ); ?>">{{ settings_text }}</a><?php endif;?>
											</div>
											<div class="right_buttons">
												<?php if($template["accept_button"]["is_on"]) : ?><a style="<?php echo esc_attr( $accept_style_attr ); ?>">{{ accept_text }}</a><?php endif;?>
												<?php if($template["accept_all_button"]["is_on"]) : ?><a style="<?php echo esc_attr( $accept_all_style_attr); ?>">{{ accept_all_text }}</a><?php endif;?>
											</div>
										</div>
									<?php endif; ?>
								</div>
						</div>
					</div>
			</div>
			<p style="color: gray; font-size: 12px; text-align : justify;"><?php echo esc_html("To change the template, navigate to Cookie Settings -> Configuration tab. To modify it further, navigate to Cookie Settings -> Design tab.") ;?> </p>
		</div>
	<?php
}




/**
 * Wizard Template
 */

?>


<div class="gdpr-wizard-main-container" id="gdpr-cookie-consent-settings-app-wizard">

<div class="form-container">

<!-- Cross Button  -->

<span id="closeButton" class="close-wizard"></span>

		<!-- form  -->
		<form id="gcc-save-settings-form-wizard" class="gcc-save-wizard-settings-form">
			<input type="hidden" name="gcc_settings_form_nonce_wizard" value="<?php echo esc_attr( wp_create_nonce( 'gcc-settings-form-nonce-wizard' ) ); ?>"/>
			
			<div class="gdpr-wizard-header-section">
				<div class="gdpr-general-wizard-logo-container"><img class="gdpr-general-wizard-logo" src="<?php echo esc_url( $image_path ) . 'CookieConsent.png'; ?>" alt="WP Cookie Consent General Wizard Logo">
					<span class="gdpr-general-wizard-main-heading">Welcome to WP Cookie Consent</span>
					<p class="gdpr-general-wizard-sub-heading">Follow the guided wizard to get started</p>
				</div>
				<div class="gdpr-wizard-progress-bar">
					<div class="gdpr-wizard-progress-bar-step1">
						<p class="gdpr-wizard-progress-bar-content1">1</p>
					</div>
					<div id="horizontal-line-id"class="horizontal-line"></div>
					<div id="gdpr-wizard-progress-bar-before"class="gdpr-wizard-progress-bar-step2-before">
						<p class="gdpr-wizard-progress-bar-content2-before">2</p>
					</div>
				</div>
				<br>
			</div>
			<div class="gdpr-wizard-thankyou-page">
				<div class="gdpr-general-wizard-thankyou-container"><img class="gdpr-general-wizard-thankyou-checked" src="<?php echo esc_url( $image_path ) . 'wizard-thakyou-checkd.svg'; ?>" alt="WP Cookie Consent Wizard Thank you">
					<span class="gdpr-wizard-thankyou-heading">Congratulations! Your Banner Is Live Now</span>	
					<div class="gdpr-wizard-thankyou-container">
						<input type="button" name="live-preview" class="gdpr-wizard-thankyou-live-preview" value="Live Preview" />
						<input type="button" name="edit-banner" class="gdpr-wizard-thankyou-edit-banner" value="Edit Banner" />
					</div>
				</div>
			</div>
			<div class="step-content">

				<!-- First Tab Conetent Start  -->
				<fieldset class="general-tab-content">
				<!-- radio button law  -->

					<div class="select-rule">
						<div class="general-tab-content-heading">General</div>
						<div class="select-law-rule-label"><label for="gdpr-cookie-consent-policy-type" class="gdpr-cookie-consent-policy-text"><?php esc_attr_e( 'Which privacy law or guideline do you want to use as the default for your worldwide visitors?', 'gdpr-cookie-consent' ); ?></label></div>
						<div class="select-law-rule-options">
							<div class="form-group" id="gdpr-cookie-consent-policy-type">
							<label class="wp-selec-law-container">
								<input type="radio" name="gcc-gdpr-policy" value="gdpr" v-model="gdpr_policy" @change="cookiePolicyChange">
								<span class="wp-select-law-test">General Data Protection Regulation</span>
							</label><br>
							<label class="wp-selec-law-container">
								<input type="radio" name="gcc-gdpr-policy" value="lgpd" v-model="gdpr_policy" @change="cookiePolicyChange">
								<span class="wp-select-law-test">General Data Protection Law ( LGPD )</span>
							</label><br>
							<label class="wp-selec-law-container">
								<input type="radio" name="gcc-gdpr-policy" value="ccpa" v-model="gdpr_policy" @change="cookiePolicyChange">
								<span class="wp-select-law-test">The California Consumer Privacy Act</span>
							</label><br>
							<?php if($ab_options['ab_testing_enabled'] === 'false' || $ab_options['ab_testing_enabled'] === false  ){ ?>
							<label class="wp-selec-law-container">
								<input type="radio" name="gcc-gdpr-policy" value="both" v-model="gdpr_policy" @change="cookiePolicyChange">
								<span class="wp-select-law-test">GDPR & CCPA</span>
							</label><br>
							<?php }?>
							<label class="wp-selec-law-container">
								<input type="radio" name="gcc-gdpr-policy" value="eprivacy" v-model="gdpr_policy" @change="cookiePolicyChange">
								<span class="wp-select-law-test">ePrivacy Regulation</span>
							</label><br>
							<?php if($ab_options['ab_testing_enabled'] === 'true' || $ab_options['ab_testing_enabled'] === true  ){ ?>
							<p class="policy-description">GDPR & CCPA cannot be selected while the Cookie Banner A/B Test is active. Please disable A/B Test to enable this compliance option.</p>
							<?php }?>
							</div>
							<input type="hidden" name="gcc-gdpr-policy" v-model="gdpr_policy">
						</div>
						<c-row v-show=" gdpr_policy === 'gdpr' || gdpr_policy === 'both' || gdpr_policy === 'ccpa'">
							<c-col class="col-sm-32 select-law-rule-sublabel"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Cookie Banner Geo-Targeting', 'gdpr-cookie-consent' ); ?></div></c-col>
						</c-row>
						<c-row v-show="gdpr_policy === 'both'" style="margin-bottom: 5px;">
							<c-col class="col-sm-4 wp-select-law-test"><label><?php esc_attr_e( 'GDPR Banner', 'gdpr-cookie-consent' ); ?></label></c-col>
						</c-row>
						<div style="margin-top: 10px;" v-show="gdpr_policy === 'gdpr' || gdpr_policy === 'both'" class="gdpr-visitors-condition">
							<div>
								<div><input class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-worldwide-enable" v-model="selectedRadioWorldWide" @click="onSwitchWorldWideEnable" id="gcc-worldwide-enable"><label for="gcc-worldwide-enable"><?php esc_attr_e( 'Worldwide', 'gdpr-cookie-consent' ); ?></label></div>
								<div>
									<input type="hidden" name="gcc-worldwide-enable" v-model="is_worldwide_on">
								</div>
							</div>
							<div>
									<?php
									$geo_options = get_option( 'wpl_geo_options' );
									 if ( !$is_user_connected || empty($is_user_connected) ) : ?>
										<div class="gdpr-disabled-geo-integration">
											<input id="gdpr-visitors-condition-radio-btn-disabled-gdpr-wizard" class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-eu-enable" disabled>
											<label><?php esc_attr_e( 'EU Countries & UK', 'gdpr-cookie-consent' ); ?></label>
										</div>
										<p class="gdpr-eu_visitors_message-gdpr">
											<?php esc_attr_e( 'To enable this feature, connect to your free account', 'gdpr-cookie-consent' ); ?>
										</p>
									<?php elseif ( $the_options['enable_safe'] === true || $the_options['enable_safe'] === 'true' ) : ?>
										<div class="gdpr-disabled-geo-integration">
											<input id="gdpr-visitors-condition-radio-btn-disabled-gdpr-wizard" class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-eu-enable" disabled>
											<label><?php esc_attr_e( 'EU Countries & UK', 'gdpr-cookie-consent' ); ?></label>
										</div>
										<p class="gdpr-eu_visitors_message-gdpr">
											<?php esc_attr_e( 'Safe Mode enabled. Disable it in Compliance settings to configure Geo-Targeting settings.', 'gdpr-cookie-consent' ); ?>
										</p>
									<?php else : ?>
										<div>
											<input id="gdpr-eu-id" class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-eu-enable" v-model="is_eu_on" @click="onSwitchEUEnable($event.target.checked)">
											<label for="gdpr-eu-id"><?php esc_attr_e( 'EU Countries & UK', 'gdpr-cookie-consent' ); ?></label>
										</div>
										<input type="hidden" name="gcc-eu-enable" v-model="is_eu_on">
									<?php endif; ?>
							</div>
							<div>
								<?php
									$geo_options = get_option( 'wpl_geo_options' );
								if ( !$is_user_connected || empty($is_user_connected)) :
									?>
									<div class="gdpr-disabled-geo-integration"><input class="gdpr-visiotrs-condition-radio-btn" id="gdpr-visitors-condition-radio-btn-disabled-both-wizard" type="checkbox" name="gcc-select-countries-enable" disabled><label><?php esc_attr_e( 'Select Countries', 'gdpr-cookie-consent' ); ?></label></div>
									<p class="gdpr-eu_visitors_message-both">
									<?php esc_attr_e( 'To enable this feature, connect to your free account', 'gdpr-cookie-consent' ); ?>
									</p>
								<?php elseif ( $the_options['enable_safe'] === true || $the_options['enable_safe'] === 'true' ) : ?>
									<div class="gdpr-disabled-geo-integration"><input class="gdpr-visiotrs-condition-radio-btn" id="gdpr-visitors-condition-radio-btn-disabled-both-wizard" type="checkbox" name="gcc-select-countries-enable" disabled><label><?php esc_attr_e( 'Select Countries', 'gdpr-cookie-consent' ); ?></label></div>
									<p class="gdpr-eu_visitors_message-both">
										<?php esc_attr_e( 'Safe Mode enabled. Disable it in Compliance settings to configure Geo-Targeting settings.', 'gdpr-cookie-consent' ); ?>
									</p>
								<?php else : ?>
									<div><input id="gdpr-select-countries" class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-select-countries-enable" v-model="selectedRadioCountry" @click="onSwitchSelectedCountryEnable($event.target.checked)"><label for="gdpr-select-countries"><?php esc_attr_e( 'Select Countries', 'gdpr-cookie-consent' ); ?></label></div>
									<input type="hidden" name="gcc-select-countries-enable" v-model="is_selectedCountry_on">
								<?php endif; ?>
							</div>
						</div>
						<div class="select-countries-dropdown" v-show="(is_selectedCountry_on) && ( gdpr_policy === 'gdpr' || gdpr_policy === 'both' || gdpr_policy === 'ccpa' )">
							<v-select id="gdpr-cookie-consent-geotargeting-countries" placeholder="Select Countries":reduce="label => label.code" class="form-group" :options="list_of_countries" multiple v-model="select_countries_array" @input="onCountrySelect"></v-select>
							<input type="hidden" name="gcc-selected-countries" v-model="select_countries">
						</div>
						<c-row v-show="gdpr_policy === 'both'" style="margin-bottom: 5px; margin-top: 1.5rem;">
							<c-col class="col-sm-4 wp-select-law-test"><label><?php esc_attr_e( 'CCPA Banner', 'gdpr-cookie-consent' ); ?></label></c-col>
						</c-row>
						<div style="margin-top: 10px;" v-show="gdpr_policy === 'ccpa' || gdpr_policy === 'both'" class="gdpr-visitors-condition">
							<div>
								<div><input class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-worldwide-enable-ccpa" v-model="selectedRadioWorldWideCcpa" @click="onSwitchWorldWideEnableCcpa" id="gcc-worldwide-enable-ccpa"><label for="gcc-worldwide-enable-ccpa"><?php esc_attr_e( 'Worldwide', 'gdpr-cookie-consent' ); ?></label></div>
								<div>
									<input type="hidden" name="gcc-worldwide-enable-ccpa" v-model="is_worldwide_on_ccpa">
								</div>
							</div>
							<div>
								<?php
									$geo_options = get_option( 'wpl_geo_options' );
								if ( !$is_user_connected || empty($is_user_connected) ) :
									?>
									<div class="gdpr-disabled-geo-integration"><input id="gdpr-visitors-condition-radio-btn-disabled-ccpa-wizard"class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-ccpa-enable" disabled><label style="width:114px;"><?php esc_attr_e( 'United States', 'gdpr-cookie-consent' ); ?></label></div>
									<p class="gdpr-eu_visitors_message-ccpa">
									<?php esc_attr_e( 'To enable this feature, connect to your free account', 'gdpr-cookie-consent' ); ?>
									</p>
								<?php elseif ( $the_options['enable_safe'] === true || $the_options['enable_safe'] === 'true' ) : ?>
									<div class="gdpr-disabled-geo-integration"><input id="gdpr-visitors-condition-radio-btn-disabled-ccpa-wizard"class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-ccpa-enable" disabled><label style="width:114px;"><?php esc_attr_e( 'United States', 'gdpr-cookie-consent' ); ?></label></div>
									<p class="gdpr-eu_visitors_message-ccpa">
										<?php esc_attr_e( 'Safe Mode enabled. Disable it in Compliance settings to configure Geo-Targeting settings.', 'gdpr-cookie-consent' ); ?>
									</p>
								<?php else : ?>
									<div><input id="gdpr-united-enabled" class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-ccpa-enable" v-model="is_ccpa_on" @click="onSwitchCCPAEnable($event.target.checked)"><label for="gdpr-united-enabled"><?php esc_attr_e( 'United States', 'gdpr-cookie-consent' ); ?></label></div>
									<input type="hidden" name="gcc-ccpa-enable" v-model="is_ccpa_on">
								<?php endif; ?>
							</div>
							<div>
								<?php
									$geo_options = get_option( 'wpl_geo_options' );
								if ( !$is_user_connected || empty($is_user_connected)) :
									?>
									<div class="gdpr-disabled-geo-integration"><input class="gdpr-visiotrs-condition-radio-btn" id="gdpr-visitors-condition-radio-btn-disabled-both-ccpa-wizard" type="checkbox" name="gcc-select-countries-enable-ccpa" disabled><label><?php esc_attr_e( 'Select Countries', 'gdpr-cookie-consent' ); ?></label></div>
									<p class="gdpr-eu_visitors_message-both-ccpa">
									<?php esc_attr_e( 'To enable this feature, connect to your free account', 'gdpr-cookie-consent' ); ?>
									</p>
								<?php elseif ( $the_options['enable_safe'] === true || $the_options['enable_safe'] === 'true' ) : ?>
									<div class="gdpr-disabled-geo-integration"><input class="gdpr-visiotrs-condition-radio-btn" id="gdpr-visitors-condition-radio-btn-disabled-both-ccpa-wizard" type="checkbox" name="gcc-select-countries-enable-ccpa" disabled><label><?php esc_attr_e( 'Select Countries', 'gdpr-cookie-consent' ); ?></label></div>
									<p class="gdpr-eu_visitors_message-both-ccpa">
										<?php esc_attr_e( 'Safe Mode enabled. Disable it in Compliance settings to configure Geo-Targeting settings.', 'gdpr-cookie-consent' ); ?>
									</p>
								<?php else : ?>
									<div><input id="gdpr-select-countries" class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-select-countries-enable-ccpa" v-model="selectedRadioCountryCcpa" @click="onSwitchSelectedCountryEnableCcpa($event.target.checked)"><label for="gdpr-select-countries-ccpa"><?php esc_attr_e( 'Select Countries', 'gdpr-cookie-consent' ); ?></label></div>
									<input type="hidden" name="gcc-select-countries-enable-ccpa" v-model="is_selectedCountry_on_ccpa">
								<?php endif; ?>
							</div>
						</div>
						<div class="select-countries-dropdown" v-show="(is_selectedCountry_on_ccpa) && ( gdpr_policy === 'gdpr' || gdpr_policy === 'both' || gdpr_policy === 'ccpa' )">
							<v-select id="gdpr-cookie-consent-geotargeting-countries-ccpa" placeholder="Select Countries":reduce="label => label.code" class="form-group" :options="list_of_countries" multiple v-model="select_countries_array_ccpa" @input="onCountrySelectCcpa"></v-select>
							<input type="hidden" name="gcc-selected-countries-ccpa" v-model="select_countries_ccpa">
						</div>				
					</div>

					<input type="button" name="next-step" class="next-step first-next-step" value="Save & Continue" />

				</fieldset>
				<!-- First Tab Conetent End  -->

				<!-- Second Tab Content Field set Start  -->
				<fieldset class="configure-tab-content">

					<div class="configure-tab-main-container">
					<div class="general-tab-content-heading">Configuration</div>
					<p class="gdpr-configuration-line-divider"></p>
							<!-- enable consent log  -->
							<div class="enable-consent-log">

								<div class="enable-consent-log-content">
									<c-col class="enable-consent-log-content-label"><label><?php esc_attr_e( 'Enable Consent Logging', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="enable-consent-log-switch">
										<c-switch v-bind="labelIcon" v-model="logging_on" id="gdpr-cookie-consent-logging" variant="3d"  color="success" :checked="logging_on"  :disabled="disableSwitch" v-on:update:checked="onSwitchLoggingOn"></c-switch>
										<input type="hidden" name="gcc-logging-on" v-model="logging_on">
									</c-col>
								</div>
							</div>

							<!-- enable/disbale script blocker  -->
							<div class="enable-script-blocker" v-show="gdpr_policy !== 'ccpa'">
								<div class="enable-script-blocker-content">
									<c-col class="enable-script-blocker-label"><label><?php esc_attr_e( 'Script Blocker', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="enable-consent-log-switch">
										<c-switch v-bind="labelIcon" v-model="is_script_blocker_on" id="gdpr-cookie-consent-script-blocker" variant="3d"  color="success" :checked="is_script_blocker_on"  v-on:update:checked="onSwitchingScriptBlocker"></c-switch>
										<input type="hidden" name="gcc-script-blocker-on" v-model="is_script_blocker_on">
									</c-col>
								</div>
							</div>

							<!-- Respect do not track  -->
							<div class="enable-respect-do-not-track">
								<div class="enable-respect-do-not-track-content">
									<c-col class="enable-respect-do-not-track-label"><label><?php esc_attr_e( 'Respect Do Not Track & Global Privacy Control', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="enable-respect-do-not-track-switch">
										<c-switch v-bind= labelIcon v-model="do_not_track_on" id="gdpr-cookie-do-not-track" variant="3d" color="success" :checked="do_not_track_on" v-on:update:checked="onSwitchDntEnable"></c-switch>
										<input type="hidden" name="gcc-do-not-track" v-model="do_not_track_on">
									</c-col>
								</div>
							</div>

							<!-- Data Request -->
							<div class="enable-data-request">
								<div class="enable-data-request-content">
									<c-col class="enable-respect-data-request-label"><label><?php esc_attr_e( 'Enable Data Request Form', 'gdpr-cookie-consent' ); ?><tooltip class="gdpr_data_req_tooltip" text="<?php esc_html_e( 'Enable to add data request form to your Privacy Statement.', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
									</c-col>
									<c-col class="enable-data-request-switch">
										<c-switch v-bind="labelIcon " v-model="data_reqs_on" id="gdpr-cookie-data-reqs" variant="3d" color="success" :checked="data_reqs_on" v-on:update:checked="onSwitchDataReqsEnable"></c-switch>
										<input type="hidden" name="gcc-data_reqs" v-model="data_reqs_on">
									</c-col>
								</div>
							</div>

							<div class="enable-data-request">
								<div class="enable-data-request-content">
									<c-col v-show="data_reqs_on">
										<c-col class="enable-respect-data-request-shortcode-label"><label><?php esc_attr_e( 'Shortcode for Data Request', 'gdpr-cookie-consent' ); ?><tooltip class="gdpr-sc-tooltip" text="<?php esc_html_e( 'You can use this Shortcode [wpl_data_request] to display the data request form on any page', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
										</c-col>
										<c-col class="enable-data-request-switch">
											<c-button class="wizard-data-request-btn" variant="outline" @click="copyTextToClipboard">{{ shortcode_copied ? 'Shortcode Copied!' : 'Click to Copy' }}</c-button>
										</c-col>
									</c-col>
								</div>
							</div>
							<!-- email box  -->
							<c-row v-show="data_reqs_on" id="gdpr-wizard-data-req-admin-container" >
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
															<label for="email-input" class="screen-reader-text"><?php esc_attr_e('Email address','gdpr-cookie-consent'); ?></label>
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
															<label for="subject-input" class="screen-reader-text"><?php esc_attr_e('Email Subject'); ?></label>
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

							<!-- Banner’s Layout & Positions -->
							<div class="show-banner-layoout-position">
									<div class="show-cookie-notice-content">
										<c-col class="show-banner-layout-label"><label><?php esc_attr_e( 'Banner’s Layout & Positions', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col v-show="show_cookie_as === 'banner'">
											<div
											@click="cookiebannerPositionChange('bottom')"
											style="display: inline-block; cursor: pointer;position:relative;left:-14px;">
											<div>
												<span id="banner-position-bottom-icon"
												class="<?php echo $the_options['notify_position_vertical'] == 'bottom' ? 'dashicons dashicons-saved' : ''; ?>"></span>
											</div>
											<img 
												id="banner-position-bottom-id"
												class="<?php echo $the_options['notify_position_vertical'] == 'bottom' ? 'banner-position-bottom' : ''; ?>" 
												src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/banner_bottom.svg'; ?>" 
												alt="Bottom"
											>
											</div>
											<div
												@click="cookiebannerPositionChange('top')"
												style="display: inline-block; cursor: pointer;position:relative; padding-left:14px;">
												<div>
													<span id="banner-position-top-icon"
													class="<?php echo $the_options['notify_position_vertical'] == 'top' ? 'dashicons dashicons-saved' : ''; ?>" ></span>
												</div>
												<img 
													id="banner-position-top-id"
													class="<?php echo $the_options['notify_position_vertical'] == 'top' ? 'banner-position-top' : ''; ?>" 
													src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/banner_top.svg'; ?>" 
													alt="Top"
												>
											</div>
											<input type="hidden" name="gcc-gdpr-cookie-position" v-model="cookie_position">
										</c-col>
										<c-col v-show="show_cookie_as === 'popup'">
												<img 
													id="popup-position-id"
													src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/pop_layout.svg'; ?>" 
													alt="Top"
												>
										</c-col>
										<c-col  class="gdpr-wizard-widget" v-show="show_cookie_as === 'widget'" style="padding-left:0px;">
													<div @click="cookiewidgetPositionChange('left')" style="display: inline-block; cursor: pointer;position:relative;"class="gdpr-wizard-widget-item">
												<div>
													<span id="widget-position-left-icon"
													class="<?php echo $the_options['notify_position_horizontal'] == 'left' ? 'dashicons dashicons-saved' : ''; ?>" ></span>
												</div>
												<img 
												id="widget-position-left-id"
													class="<?php echo $the_options['notify_position_horizontal'] == 'left' ? 'widget-position-top' : ''; ?>" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/widget_bottom_left.svg'; ?>" alt="Bottom_left">
											</div>
											<div @click="cookiewidgetPositionChange('right')" style="display: inline-block; cursor: pointer;"class="gdpr-wizard-widget-item">
												<div>
													<span id="widget-position-right-icon"
													class="<?php echo $the_options['notify_position_horizontal'] == 'right' ? 'dashicons dashicons-saved' : ''; ?>" ></span>
												</div>
												<img id="widget-position-right-id"
													class="<?php echo $the_options['notify_position_horizontal'] == 'right' ? 'widget-position-top' : ''; ?>" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/widget_bottom_right.svg'; ?>" alt="Bottom_right">
											</div>
											<div @click="cookiewidgetPositionChange('top_left')" style="display: inline-block; cursor: pointer;"class="gdpr-wizard-widget-item">
												<div>
													<span id="widget-position-top_left-icon"
													class="<?php echo $the_options['notify_position_horizontal'] == 'top_left' ? 'dashicons dashicons-saved' : ''; ?>" ></span>
												</div>
												<img id="widget-position-top_left-id"
													class="<?php echo $the_options['notify_position_horizontal'] == 'top_left' ? 'widget-position-top' : ''; ?>" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/widget_top_left.svg'; ?>" alt="Top_left">
											</div>
											<div @click="cookiewidgetPositionChange('top_right')" style="display: inline-block; cursor: pointer;"class="gdpr-wizard-widget-item">
												<div>
													<span id="widget-position-top_right-icon"
													class="<?php echo $the_options['notify_position_horizontal'] == 'top_right' ? 'dashicons dashicons-saved' : ''; ?>" ></span>
												</div>
												<img id="widget-position-top_right-id"
													class="<?php echo $the_options['notify_position_horizontal'] == 'top_right' ? 'widget-position-top' : ''; ?>" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/widget_top_right.svg'; ?>" alt="Top_right">
											</div>
											<input type="hidden" name="gcc-gdpr-cookie-widget-position" v-model="cookie_widget_position">
										</c-col>
									</div>
							</div>

							<!-- show templates  -->

							

								<?php $the_options = Gdpr_Cookie_Consent::gdpr_get_settings(); ?>

								<div class="show-cookie-template-card">
									<c-card-header class="show-cookie-template-label"><?php esc_html_e( 'Your cookie bar', 'gdpr-cookie-consent' ); ?></c-card-header>
									<c-card-body>
										<!-- banner templates  -->
										<c-row class="show-banner-template">
											<c-col class="col-sm-3 left-side-banner-template" >
												<input type="hidden" name="gdpr-template" v-model="template">
												<input type="hidden" name="gcc-revoke-consent-text-color" v-model="button_revoke_consent_text_color">
												<input type="hidden" name="gcc-revoke-consent-background-color" v-model="button_revoke_consent_background_color">
											</c-col>
											
											<?php print_template_boxes(  ); ?>
											
										</c-row>

										<input type="hidden" name="gdpr-template" v-model="template">
									</c-card-body>
								</div>

							

					</div>
					<input type="button" name="next-step" @click="saveWizardCookieSettings" class="next-step second-next-step" id="gdpr-wizard-finish-btn" value="Finish Setup" />
					<input type="button" name="previous-step" class="previous-step first-previous-step" value="Go Back" />

				</fieldset>
				<!-- Second Tab Content Field set End  -->

				<!-- third Tab Content Field set start -->
				<fieldset class="finish-tab-content" class="gdpr-wizard-thankyou-main-container">

					<div class="gdpr-wizard-help-center">
						<div class="gdpr-help-item">
								<img class="gdpr-other-plugin-image" src="<?php echo esc_url( $image_path ) . 'help-center.svg'; ?>" alt="WPCS Help Center Icon">
							<div class="gdpr-help-content">
							<span class="gdpr-help-caption">
								<?php esc_html_e( 'Help Center', 'gdpr-cookie-consent' ); ?>
							</span>
							<span class="gdpr-help-description">
								<?php esc_html_e( 'Read the documentation to find answers to your questions.', 'gdpr-cookie-consent' ); ?>
							</span>
							<a href="https://wplegalpages.com/docs/wp-cookie-consent/" target="_blank" class="gdpr-help-button"><?php esc_html_e( 'Learn More', 'gdpr-cookie-consent' ); ?> <img class="gdpr-other-plugin-arrow" :src="right_arrow.default" alt="WPCS right arrow icon"></a>
							</div>
						</div>
						<div class="gdpr-help-item">
								<img class="gdpr-other-plugin-image" src="<?php echo esc_url( $image_path ) . 'video.svg'; ?>" alt="WPCS Video icon">
							<div class="gdpr-help-content">
							<span class="gdpr-help-caption">
								<?php esc_html_e( 'Video Guides', 'gdpr-cookie-consent' ); ?>
							</span>
							<span class="gdpr-help-description">
								<?php esc_html_e( 'Explore video tutorials for insights on WP Cookie Consent functionality.', 'gdpr-cookie-consent' ); ?>
							</span>
							<a href="https://wplegalpages.com/docs/wp-cookie-consent/video-guides/" target="_blank" class="gdpr-help-button"><?php esc_html_e( 'Watch Now', 'gdpr-cookie-consent' ); ?> <img class="gdpr-other-plugin-arrow" :src="right_arrow.default" alt="WPCS right arrow icon"></a>
							</div>
						</div>
						<div class="gdpr-help-item">
								<img class="gdpr-other-plugin-image" src="<?php echo esc_url( $image_path ) . 'faqs.svg'; ?>" alt="WPCS Other plugin image">
							<div class="gdpr-help-content">
							<span class="gdpr-help-caption">
								<?php esc_html_e( 'FAQ with Answers', 'gdpr-cookie-consent' ); ?>
							</span>
							<span class="gdpr-help-description">
								<?php esc_html_e( 'Find answers to some of the most commonly asked questions.', 'gdpr-cookie-consent' ); ?>
							</span>
							<a href="https://wplegalpages.com/docs/wplp-docs/guides/" target="_blank" class="gdpr-help-button"><?php esc_html_e( 'Find Out', 'gdpr-cookie-consent' ); ?> <img class="gdpr-other-plugin-arrow" :src="right_arrow.default" alt="WPCS right arrow icon"></a>
							</div>
						</div>
					</div>
				</fieldset>

			</div>
		</form>

	</div>

</div>

<?php 
 $frontend_url = get_site_url(); ?>

<script>

jQuery(document).ready(function () {
	var currentGfgStep, nextGfgStep, previousGfgStep;
	var opacity;
	var current = 2;
	var steps = jQuery("fieldset").length;
	var imagePath = "<?php echo esc_url( $image_path ); ?>";
	var isProActive = "<?php echo esc_url( $is_pro ); ?>";

	// initial condition.
	jQuery(".gdpr-wizard-header-section").show();
    jQuery(".gdpr-wizard-thankyou-page").hide();

	jQuery(".next-step").click(function () {

		currentGfgStep = jQuery(this).parent();
		nextGfgStep = jQuery(this).parent().next();

		if (currentGfgStep.index() === 1) {
            jQuery(".gdpr-wizard-header-section").hide();
            jQuery(".gdpr-wizard-thankyou-page").show();
        } else {
            jQuery(".gdpr-wizard-header-section").show();
            jQuery(".gdpr-wizard-thankyou-page").hide();
        }

		jQuery("#gdpr-wizard-progress-bar-before").removeClass("gdpr-wizard-progress-bar-step2-before");
		jQuery("#gdpr-wizard-progress-bar-before").addClass("gdpr-wizard-progress-bar-step2");
		jQuery("#horizontal-line-id").removeClass("horizontal-line");
		jQuery("#horizontal-line-id").addClass("horizontal-line-after");

		nextGfgStep.show();
		currentGfgStep.animate({ opacity: 0 }, {
			step: function (now) {
				opacity = 1 - now;

				currentGfgStep.css({
					'display': 'none',
					'position': 'relative'
				});
				if (currentGfgStep.index() === 1) {
					nextGfgStep.css({ 'display':'flex','justify-content':'center','opacity': opacity });
				}
				else{
					nextGfgStep.css({ 'opacity': opacity });
				}
				
			},
			duration: 500
		});
	});

	jQuery(".previous-step").click(function () {

		currentGfgStep = jQuery(this).parent();
		previousGfgStep = jQuery(this).parent().prev();

		if (currentGfgStep.index() === 2) {
            jQuery(".gdpr-wizard-header-section").show();
            jQuery(".gdpr-wizard-thankyou-page").hide();
        }

		jQuery("#gdpr-wizard-progress-bar-before").removeClass("gdpr-wizard-progress-bar-step2");
		jQuery("#gdpr-wizard-progress-bar-before").addClass("gdpr-wizard-progress-bar-step2-before");
		jQuery("#horizontal-line-id").removeClass("horizontal-line-after");
		jQuery("#horizontal-line-id").addClass("horizontal-line");

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
	});

	jQuery(".submit").click(function () {
		return false;
	})

	//if gdpr pro is active hide the go-pro image from the templates

	if ( isProActive ) {

		jQuery(".gdpr-go-pro-label").hide()

	}

	// edit banner redirection

	jQuery(".gdpr-wizard-thankyou-edit-banner").click(function() {
		// Get the admin URL
		var adminUrl = "<?php echo esc_url( admin_url() ); ?>";

		// Redirect to the dashboard submenu
		window.location.href = adminUrl + "/admin.php?page=gdpr-cookie-consent/#cookie_settings";
	});

	//live preview redirection.

	jQuery('.gdpr-wizard-thankyou-live-preview').on('click', function() {
		window.open('<?php echo esc_url($frontend_url); ?>', '_blank');
	});




});


</script>


<?php
