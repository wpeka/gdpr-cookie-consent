<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pro_is_activated = get_option( 'wpl_pro_active', false );

require_once GDPR_COOKIE_CONSENT_PLUGIN_PATH . 'includes/settings/class-gdpr-cookie-consent-settings.php';

$this->settings = new GDPR_Cookie_Consent_Settings();

$is_user_connected      = $this->settings->is_connected();
$api_user_email         = $this->settings->get_email();
$api_user_site_key      = $this->settings->get_website_key();
$api_user_plan          = $this->settings->get_plan();
?>
<div class="gdpr-cookie-consent-app-container" id="gdpr-cookie-consent-advanced-settings">
    <c-container class="gdpr-cookie-consent-settings-container gdpr-cookie-consent-advanced-settings-container">
        <c-form id="gcc-save-advanced-settings-form" method="post" spellcheck="false" class="gdpr-cookie-consent-settings-form">
            <input type="hidden" name="gcc_settings_form_nonce_advanced" value="<?php echo esc_attr( wp_create_nonce( 'gcc-settings-form-nonce-advanced' ) ); ?>"/>
            <div class="gdpr-cookie-consent-settings-content">
                <div id="gdpr-cookie-consent-save-settings-alert-adv">{{success_error_message}}</div>
				<div id="gdpr-cookie-consent-updating-settings-alert-adv">Updating Setting</div>
 
                <c-tabs variant="pills" ref="active_tab_adv" class="gdpr-cookie-consent-settings-nav">
					<!-- Consent Settings Start -->
                    <c-tab href="#advanced_settings#consent" class="consent-settings" title="<?php esc_attr_e( 'Consent Behavior', 'gdpr-cookie-consent' ); ?>" id="gdpr-cookie-consent-consent-settings" >   
						<c-card class="consent_card">
							<div class="gdpr-preview-publish-btn gdpr-preview-publish-btn-adv">
								<c-button :disabled="save_loading" class="gdpr-publish-btn" @click="saveCookieSettings">{{ save_loading ? '<?php esc_html_e( 'Saving...', 'gdpr-cookie-consent' ); ?>' : '<?php esc_html_e( 'Save Changes', 'gdpr-cookie-consent' ); ?>' }}</c-button>
							</div>

                            <c-card-body>
								<c-row>
									<c-col class="col-sm-32">
										<div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Consent Behavior Settings', 'gdpr-cookie-consent' ); ?></div>
									</c-col>
								</c-row>
                                <!-- Consent  Forwarding -->
								<?php
								if ( ! $is_pro_active ) :
									$currentid = get_current_blog_id();
									if ( is_multisite() ) { ?>
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
									} else { ?>
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
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Enable Consent Logging', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enable to log user’s consent.', 'gdpr-cookie-consent' ); ?>"></tooltip><div class="consent-log-readmore-container">
											<a class="consent-log-readmore" href="https://wplegalpages.com/docs/wplp-docs/features-walkthrough/cookie-consent/#h-consent-logs" target="_blank">
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

								<c-row>
								<c-col class="col-sm-4"><label><?php esc_attr_e( 'Reload After Accept', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'If enabled web page will be refreshed automatically once cookie settings are accepted.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
								<c-col class="col-sm-8">
									<c-switch v-bind="labelIcon" v-model="accept_reload" id="gdpr-cookie-consent-accept-reload" variant="3d"  color="success" :checked="accept_reload" v-on:update:checked="onSwitchAcceptReload"></c-switch>
									<input type="hidden" name="gcc-accept-reload" v-model="accept_reload">
								</c-col>
								</c-row>
								<c-row  v-show="show_revoke_card">
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Reload After Reject All', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'If enabled web page will be refreshed automatically once cookie settings are declined.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="decline_reload" id="gdpr-cookie-consent-decline-reload" variant="3d"  color="success" :checked="decline_reload" v-on:update:checked="onSwitchDeclineReload"></c-switch>
										<input type="hidden" name="gcc-decline-reload" v-model="decline_reload">
									</c-col>
								</c-row>
								
								<!--  Anonymize IP address - All laws-->
								<c-row>
									<c-col class="col-sm-4 relative">
										<label>
											<?php esc_attr_e( 'Anonymize IP Address', 'gdpr-cookie-consent' ); ?>
											<tooltip text="<?php esc_html_e( 'Masks visitor IP addresses in consent logs to enhance privacy compliance. Higher masking reduces geo-location accuracy.', 'gdpr-cookie-consent' ); ?>"></tooltip>
										</label>
									</c-col>
									<c-col class="col-sm-8">
										<div class="gdpr-disabled-export-settings">
										<c-switch 
											v-bind="labelIcon" 
											v-model="ip_anonymization_on" 
											id="gdpr-cookie-consent-ip-anonymization" 
											variant="3d" 
											color="success" 
											:checked="ip_anonymization_on" 
											:disabled="!logging_on"
											style="!logging_on ? 'cursor:not-allowed' : ''"
											v-on:update:checked="onSwitchIpAnonymization">
										</c-switch>
										 <p v-if="!logging_on" class="gdpr-export-message">
											<?php esc_attr_e( 'To use this feature, turn on "Enable Consent Logging"', 'gdpr-cookie-consent' ); ?>
										</p>
										<input type="hidden" name="gcc-ip-anonymization-enabled" v-model="ip_anonymization_on">
									</div>
									</c-col>
								</c-row>

								<!-- IP Masking Level-->
								<c-row v-show="logging_on && ip_anonymization_on"> 
									<c-col class="col-sm-4">
										<label><?php esc_html_e( 'Select how many bytes to mask', 'gdpr-cookie-consent' ); ?></label>
									</c-col>
									<c-col class="col-sm-8">
										<v-select
											class="form-group"
											id="gdpr-cookie-consent-ip-masking-level"
											:reduce="label => label.code"
											:options="ip_masking_options"
											v-model="ip_masking_level"
											:searchable="false">
										</v-select>
										<input type="hidden" name="gcc-ip-masking-level" v-model="ip_masking_level">
									</c-col>
								</c-row>
                            </c-card-body>
                        </c-card>
                    </c-tab> 
                    
                    <!-- Additional Settings Start -->
                    <c-tab href="#advanced_settings#additional" class="additional-settings" title="<?php esc_attr_e( 'Cookie & Privacy', 'gdpr-cookie-consent' ); ?>" id="gdpr-cookie-consent-additional-settings" >
                        <c-card class="additional_card">
							<div class="gdpr-preview-publish-btn gdpr-preview-publish-btn-adv">
								<c-button :disabled="save_loading" class="gdpr-publish-btn" @click="saveCookieSettings">{{ save_loading ? '<?php esc_html_e( 'Saving...', 'gdpr-cookie-consent' ); ?>' : '<?php esc_html_e( 'Save Changes', 'gdpr-cookie-consent' ); ?>' }}</c-button>
							</div>

                            <c-card-body>
								<c-row>
									<c-col class="col-sm-32">
										<div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Cookie & Privacy Settings', 'gdpr-cookie-consent' ); ?></div>
									</c-col>
								</c-row>
                                <!-- Extra Settings -->             
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Delete Plugin Data on Deactivation', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="col-sm-8">
										<c-switch v-bind="labelIcon" v-model="delete_on_deactivation" id="gdpr-cookie-consent-delete-on-deactivation" variant="3d"  color="success" :checked="delete_on_deactivation" v-on:update:checked="onSwitchDeleteOnDeactivation"></c-switch>
										<input type="hidden" name="gcc-delete-on-deactivation" v-model="delete_on_deactivation">
									</c-col>
								</c-row>
								<c-row>
									<c-col class="col-sm-4"><label><?php esc_attr_e( 'Hide Credits', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'If you are happy with the product and want to share credit with the developer, you can display credits under the Preferences.', 'gdpr-cookie-consent' ); ?>"></tooltip>  <a href="https://wplegalpages.com/pricing?utm_source=wp_cookie_consent&utm_medium=hide_credits&utm_campaign=plugin_upgrade" class="probadge bg-badge"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 640" fill="#f5af2f"> <path d="M345 151.2C354.2 143.9 360 132.6 360 120C360 97.9 342.1 80 320 80C297.9 80 280 97.9 280 120C280 132.6 285.9 143.9 295 151.2L226.6 258.8C216.6 274.5 195.3 278.4 180.4 267.2L120.9 222.7C125.4 216.3 128 208.4 128 200C128 177.9 110.1 160 88 160C65.9 160 48 177.9 48 200C48 221.8 65.5 239.6 87.2 240L119.8 457.5C124.5 488.8 151.4 512 183.1 512L456.9 512C488.6 512 515.5 488.8 520.2 457.5L552.8 240C574.5 239.6 592 221.8 592 200C592 177.9 574.1 160 552 160C529.9 160 512 177.9 512 200C512 208.4 514.6 216.3 519.1 222.7L459.7 267.3C444.8 278.5 423.5 274.6 413.5 258.9L345 151.2z"/><path d="M180 550H460" fill="none" stroke="#f5af2f" stroke-width="28" stroke-linecap="round"/></svg></a></label></c-col>
									<c-col class="col-sm-8">
										<div class="gdpr-disabled-show-credits">
										<?php 
											$is_disabled = (!$is_user_connected || $api_user_plan === 'free');
										?>
										<c-switch v-bind="labelIcon" v-model="show_credits" id="gdpr-cookie-consent-show-credits" variant="3d"  color="success" :checked="<?php echo $is_disabled ? 'false' : '!show_credits'; ?>" v-on:update:checked="onSwitchShowCredits"  <?php echo $is_disabled ? 'disabled' : ''; ?>></c-switch>
										<input type="hidden" name="gcc-show-credits" v-model="show_credits">
										<?php if ($is_disabled): ?>
											<p class="gdpr-show_credits_message">
												<?php esc_attr_e( 'To enable this feature, connect to your pro account', 'gdpr-cookie-consent' ); ?>
											</p>
										<?php endif; ?>
									</div>
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
												<input type="hidden" name="gcc-enable-safe" v-model="enable_safe">					
                                                <c-switch  v-bind="labelIcon " id="gdpr-cookie-consent-enable-safe" variant="3d" color="success" :checked="enable_safe" v-on:update:checked="onSwitchEnableSafe" v-model="enable_safe"></c-switch>
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

                    <!-- Export/Import Settings Start -->
                    <c-tab href="#advanced_settings#export_import" class="export_import" title="<?php esc_attr_e( 'Import/Export', 'gdpr-cookie-consent' ); ?>" id="gdpr-cookie-consent-export-import-settings" >
                        <c-card class="export_import_card">
							<div class="gdpr-preview-publish-btn gdpr-preview-publish-btn-adv">
								<c-button :disabled="save_loading" class="gdpr-publish-btn" @click="saveCookieSettings">{{ save_loading ? '<?php esc_html_e( 'Saving...', 'gdpr-cookie-consent' ); ?>' : '<?php esc_html_e( 'Save Changes', 'gdpr-cookie-consent' ); ?>' }}</c-button>
							</div>

                            <c-card-body>
                                <!-- Export Settings Label -->
								<c-row>
									<c-col class="col-sm-32">
										<div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Import/Export Settings', 'gdpr-cookie-consent' ); ?></div>
									</c-col>
								</c-row>

							    <c-row class="mb-3" >
							    	<c-col class="col-sm-4">
							    		<label class="mb-0"><?php esc_attr_e( 'Export Settings ', 'gdpr-cookie-consent' ); ?><tooltip text="<?php esc_html_e( ' You can use this to export your settings to another site. ', 'gdpr-cookie-consent' ); ?>"></tooltip>  <a href="https://wplegalpages.com/pricing?utm_source=wp_cookie_consent&utm_medium=export_settings&utm_campaign=plugin_upgrade" class="probadge bg-badge"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 640" fill="#f5af2f"> <path d="M345 151.2C354.2 143.9 360 132.6 360 120C360 97.9 342.1 80 320 80C297.9 80 280 97.9 280 120C280 132.6 285.9 143.9 295 151.2L226.6 258.8C216.6 274.5 195.3 278.4 180.4 267.2L120.9 222.7C125.4 216.3 128 208.4 128 200C128 177.9 110.1 160 88 160C65.9 160 48 177.9 48 200C48 221.8 65.5 239.6 87.2 240L119.8 457.5C124.5 488.8 151.4 512 183.1 512L456.9 512C488.6 512 515.5 488.8 520.2 457.5L552.8 240C574.5 239.6 592 221.8 592 200C592 177.9 574.1 160 552 160C529.9 160 512 177.9 512 200C512 208.4 514.6 216.3 519.1 222.7L459.7 267.3C444.8 278.5 423.5 274.6 413.5 258.9L345 151.2z"/><path d="M180 550H460" fill="none" stroke="#f5af2f" stroke-width="28" stroke-linecap="round"/></svg></a></label>
							    	</c-col>
							    	<c-col class="col-sm-8">
										<div class="gdpr-disabled-export-settings">
							    		<c-button id="export-settings-configuration" color="info" variant="outline" @click="exportsettings" <?php echo (!$is_user_connected || $api_user_plan === 'free') ? 'disabled' : ''; ?> style="<?php echo (!$is_user_connected || $api_user_plan === 'free') ? 'cursor:not-allowed' : ''; ?>"><?php esc_html_e( 'Export', 'gdpr-cookie-consent' ); ?></c-button>
										<?php if ( ! $is_user_connected || $api_user_plan === 'free' ) : ?>
											<p class="gdpr-export-message">
												<?php esc_attr_e( 'To use this feature, connect to your pro account', 'gdpr-cookie-consent' ); ?>
											</p>
										<?php endif; ?>
										</div>
							    	</c-col>
							    </c-row>
							    <c-row class="mb-3 pb-3" >
							    	<c-col class="col-sm-4" style="flex-direction:column;align-items:baseline;position: relative;">
							    		<div style="display:flex" >
							    			<label style="margin-bottom:0;cursor:pointer"><?php esc_attr_e( 'Import Settings', 'gdpr-cookie-consent' ); ?>  <a href="https://wplegalpages.com/pricing?utm_source=wp_cookie_consent&utm_medium=import_settings&utm_campaign=plugin_upgrade" class="probadge bg-badge"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 640 640" fill="#f5af2f"> <path d="M345 151.2C354.2 143.9 360 132.6 360 120C360 97.9 342.1 80 320 80C297.9 80 280 97.9 280 120C280 132.6 285.9 143.9 295 151.2L226.6 258.8C216.6 274.5 195.3 278.4 180.4 267.2L120.9 222.7C125.4 216.3 128 208.4 128 200C128 177.9 110.1 160 88 160C65.9 160 48 177.9 48 200C48 221.8 65.5 239.6 87.2 240L119.8 457.5C124.5 488.8 151.4 512 183.1 512L456.9 512C488.6 512 515.5 488.8 520.2 457.5L552.8 240C574.5 239.6 592 221.8 592 200C592 177.9 574.1 160 552 160C529.9 160 512 177.9 512 200C512 208.4 514.6 216.3 519.1 222.7L459.7 267.3C444.8 278.5 423.5 274.6 413.5 258.9L345 151.2z"/><path d="M180 550H460" fill="none" stroke="#f5af2f" stroke-width="28" stroke-linecap="round"/></svg></a></label>
							    			<?php
							    			$plugin_version = defined( 'GDPR_COOKIE_CONSENT_VERSION' ) ? GDPR_COOKIE_CONSENT_VERSION : '';
							    			if ( version_compare( $plugin_version, '2.5.2', '<=' ) ) {
							    				if ( ! $is_pro_active ) :
							    					?>
							    			        <div class="gdpr-pro-label" style="margin-bottom:0;margin-top:3px;" >
							    						<div class="gdpr-pro-label-text">Pro</div>
							    					</div>
							    						<?php endif; 
                                            } ?>
							    		</div>
							    		<div style="font-size: 10px;" v-if="selectedFile">{{ selectedFile.name }} <span style="color:#00CF21;font-weight:500;margin-left:5px" > Uploaded </span> <span style="color: #8996AD;text-decoration:underline;margin-left:5px;position:absolute" class="remove-button" @click="removeFile">Remove</span> </div>
							    		<div style="font-size: 10px;" v-else>No File Chosen</div>
							    	</c-col>
							    	<c-col class="col-sm-6" id="import-btn-container">
										<div class="gdpr-disabled-import-settings">
                                        <label style="margin-bottom:0; font-size:0.875rem;<?php
                                        echo version_compare( $plugin_version, '2.5.2', '<=' ) ? ( ! $is_pro_active ? 'color:#D8DBE0;' : 'color:#3399ff;' ) : 'color:#3399ff;';  if (!$is_user_connected  || $api_user_plan === 'free') {
											echo 'text-decoration:none;color:#D8DBE0;font-weight:normal;cursor:not-allowed;';
										} else {
											echo 'cursor:pointer;';
										}
										
                                        ?> text-decoration:underline;margin: right 10px ;padding-left:42px;margin-top:6px;" for="fileInput"  <?php echo (!$is_user_connected || !$api_user_plan === 'free')? 'onclick="return false;"' : ''; ?>>Choose file</label>
										 <?php if ( ! $is_user_connected || $api_user_plan === 'free') : ?>
											<p class="gdpr-import-message">
												<?php esc_attr_e( 'To use this feature, connect to your pro account', 'gdpr-cookie-consent' ); ?>
											</p>
										<?php endif; ?>
									</div>
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
                            </c-card-body>
                        </c-card>
                    </c-tab> 
					<?php if ( $is_user_connected && ! $pro_is_activated ) : ?>
					<c-tab href="#advanced_settings#connection" class="connection" title="<?php esc_attr_e( 'Connection', 'gdpr-cookie-consent' ); ?>" id="gdpr-cookie-consent-connection-settings" >
						<c-card class="connection_card gdpr-cookie-consent-settings-cookie-notice-top">
							<c-card-body class="gdpr-connection-card-body" >
								<!-- Connection -->	
								<div class="gdpr-connect-information">
									<div class="gdpr-connection-success-tick">
										<div class="gdpr-connection-success-img"><img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/check_ring.svg'; ?>" alt="API Connection Success Mark"></div>
										<div class="gdpr-connection-success-descreption"><?php esc_html_e( 'Your website is connected to WPLP Cookie Consent', 'gdpr-cookie-consent' ); ?></div>
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