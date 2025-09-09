<div class="gdpr-cookie-consent-app-container" id="gdpr-cookie-consent-cookie_manager-settings">
    <c-container class="gdpr-cookie-consent-settings-container gdpr-cookie-consent-cookie-manager-container">
        <c-form id="gcc-save-cookie-manager-settings-form" method="post" spellcheck="false" class="gdpr-cookie-consent-settings-form">
            <input type="hidden" name="gcc_settings_form_nonce_cookie_manager" value="<?php echo esc_attr( wp_create_nonce( 'gcc-settings-form-nonce-cookie-manager' ) ); ?>"/>
            
            <div class="gdpr-cookie-consent-settings-content">
                <div id="gdpr-cookie-consent-save-settings-alert">{{success_error_message}}</div>
				<div id="gdpr-cookie-consent-updating-settings-alert">Updating Setting</div>

                <div class="gdpr-banner-preview-save-btn">
                    <div></div>
                    <div class="gdpr-preview-publish-btn">
						<c-button :disabled="save_loading" class="gdpr-publish-btn" @click="saveCookieManagerSettings">{{ save_loading ? '<?php esc_html_e( 'Saving...', 'gdpr-cookie-consent' ); ?>' : '<?php esc_html_e( 'Save Changes', 'gdpr-cookie-consent' ); ?>' }}</c-button>
					</div>
                </div>

                <hr id="preview-btn-setting-nav-seperator">

               <c-tabs variant="pills" ref="active_tab" class="gdpr-cookie-consent-settings-nav">
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
				    								<div class="col-sm-4 table-cols-left"><label for="gdpr-cookie-consent-custom-cookie-name">Cookie Name</label><c-input id="gdpr-cookie-consent-custom-cookie-name" placeholder="Cookie Name" name="gdpr-cookie-consent-custom-cookie-name" v-model="custom_cookie_name"></c-input></div>	
				    								<div class="col-sm-4 table-cols"><label for="gdpr-cookie-consent-custom-cookie-domain">Cookie Domain</label><c-input id="gdpr-cookie-consent-custom-cookie-domain" placeholder="Cookie Domain" name="gdpr-cookie-consent-custom-cookie-domain" v-model="custom_cookie_domain"></c-input></div>
				    								<div class="col-sm-4 table-cols"><label for="gdpr-cookie-consent-custom-cookie-days">Duration (Days/Session)</label><c-input id="gdpr-cookie-consent-custom-cookie-days" :placeholder="custom_cookie_duration_placeholder" name="gdpr-cookie-consent-custom-cookie-days" v-model="custom_cookie_duration" :disabled="is_custom_cookie_duration_disabled"></c-input></div>
				    							</div>
				    							<div class="gdpr-custom-cookie-box-inputs table-rows">
				    								<div class="col-sm-6 table-cols-left"><v-select class="gdpr-custom-cookie-select form-group" :reduce="label => label.code" :options="custom_cookie_categories" v-model="custom_cookie_category"></v-select></div>
				    								<input type="hidden" name="gdpr-custom-cookie-category" v-model="custom_cookie_category">
				    								<div class="col-sm-6 table-cols"><v-select class="gdpr-custom-cookie-select form-group" :reduce="label => label.code" :options="custom_cookie_types" v-model="custom_cookie_type" @input="onSelectCustomCookieType"></v-select></div>
				    								<input type="hidden" name="gdpr-custom-cookie-type" v-model="custom_cookie_type">
				    							</div>
				    							<div class="gdpr-custom-cookie-box-inputs table-rows">
				    								<div class="col-sm-12 table-cols-left"><label for="gdpr-cookie-consent-custom-cookie-purpose">Cookie Purpose</label><div><textarea id="gdpr-cookie-consent-custom-cookie-purpose" placeholder="Cookie Purpose" name="gdpr-cookie-consent-custom-cookie-purpose" v-model="custom_cookie_description" style="height:173px;width:807px;"></textarea></div></div>
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
               </c-tabs>
            </div>
        </c-form>
    </c-container>
</div>