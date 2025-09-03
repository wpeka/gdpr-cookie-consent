<div class="gdpr-cookie-consent-app-container" id="gdpr-cookie-consent-language-settings">
    <c-container class="gdpr-cookie-consent-settings-container">
        <c-form id="gcc-save-language-settings-form" method="post" spellcheck="false" class="gdpr-cookie-consent-settings-form">
            <input type="hidden" name="gcc_settings_form_nonce_language" value="<?php echo esc_attr( wp_create_nonce( 'gcc-settings-form-nonce-language' ) ); ?>"/>
            
            <div class="gdpr-cookie-consent-settings-content">
                <div id="gdpr-cookie-consent-save-settings-alert">{{success_error_message}}</div>
				<div id="gdpr-cookie-consent-updating-settings-alert">Updating Setting</div>

                <div class="gdpr-banner-preview-save-btn">
                    <div></div>
                    <div class="gdpr-preview-publish-btn">
						<c-button :disabled="save_loading" class="gdpr-publish-btn" @click="saveLanguageSettings">{{ save_loading ? '<?php esc_html_e( 'Saving...', 'gdpr-cookie-consent' ); ?>' : '<?php esc_html_e( 'Save Changes', 'gdpr-cookie-consent' ); ?>' }}</c-button>
					</div>
                </div>

                <hr id="preview-btn-setting-nav-seperator">

               <c-tabs variant="pills" ref="active_tab" class="gdpr-cookie-consent-settings-nav">
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
               </c-tabs>
            </div>
        </c-form>
    </c-container>
</div>