<div class="gdpr-cookie-consent-app-container" id="gdpr-cookie-consent-advanced-settings">
    <c-container class="gdpr-cookie-consent-settings-container">
        <c-form id="gcc-save-advanced-settings-form" method="post" spellcheck="false" class="gdpr-cookie-consent-settings-form">
            <input type="hidden" name="gcc_settings_form_nonce_advanced" value="<?php echo esc_attr( wp_create_nonce( 'gcc-settings-form-nonce-advanced' ) ); ?>"/>
            <div class="gdpr-cookie-consent-settings-content">
                <div id="gdpr-cookie-consent-save-settings-alert">{{success_error_message}}</div>
				<div id="gdpr-cookie-consent-updating-settings-alert">Updating Setting</div>

                <div class="gdpr-banner-preview-save-btn">
                    <div>insert something here?</div>
                    <div class="gdpr-preview-publish-btn">
						<c-button :disabled="save_loading" class="gdpr-publish-btn" @click="saveAdvancedCookieSettings">{{ save_loading ? '<?php esc_html_e( 'Saving...', 'gdpr-cookie-consent' ); ?>' : '<?php esc_html_e( 'Save Changes', 'gdpr-cookie-consent' ); ?>' }}</c-button>
					</div>
                </div>

                <hr id="preview-btn-setting-nav-seperator">

                <c-tabs variant="pills" ref="active_tab" class="gdpr-cookie-consent-settings-nav">
                    <!-- Consent Settings Start -->
                    <c-tab title="<?php esc_attr_e( 'Consent Settings', 'gdpr-cookie-consent' ); ?>" href="#advanced_settings#consent" id="gdpr-cookie-consent-complianz" >
                        <c-card class="consent_card">
                            <c-card-body>
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
                            </c-card-body>
                        </c-card>
                    </c-tab> 
                    
                    <!-- Additional Settings Start -->
                    <c-tab title="<?php esc_attr_e( 'Additional Settings', 'gdpr-cookie-consent' ); ?>" href="#advanced_settings#additional" id="gdpr-cookie-consent-complianz" >
                        <c-card class="additional_card">
                            <c-card-body>
                                <!-- Extra Settings -->             
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
                    <c-tab title="<?php esc_attr_e( 'Cookie Settings Export/Import', 'gdpr-cookie-consent' ); ?>" href="#advanced_settings#export_import" id="gdpr-cookie-consent-complianz" >
                        <c-card class="export_import_card">
                            <c-card-body>
                                <!-- Export Settings Label -->

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
							    						<?php endif; 
                                            } ?>
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
                            </c-card-body>
                        </c-card>
                    </c-tab> 
                </c-tabs>
            </div>
        </c-form>
    </c-container>
</div>