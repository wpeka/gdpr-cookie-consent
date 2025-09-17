<div class="gdpr-cookie-consent-app-container" id="gdpr-cookie-consent-abtesting-settings">
    <c-container class="gdpr-cookie-consent-settings-container gdpr-cookie-consent-abtesting-container">
        <c-form id="gcc-save-abtesting-settings-form" method="post" spellcheck="false" class="gdpr-cookie-consent-settings-form">
            <input type="hidden" name="gcc_settings_form_nonce_abtesting" value="<?php echo esc_attr( wp_create_nonce( 'gcc-settings-form-nonce-abtesting' ) ); ?>"/>
            
            <div class="gdpr-cookie-consent-settings-content">
                <div id="gdpr-cookie-consent-save-settings-alert">{{success_error_message}}</div>
				<div id="gdpr-cookie-consent-updating-settings-alert">Updating Setting</div>

                <div class="gdpr-banner-preview-save-btn">
                    <div></div>
                    <div class="gdpr-preview-publish-btn">
						<c-button :disabled="save_loading" class="gdpr-publish-btn" @click="saveABTestingSettings">{{ save_loading ? '<?php esc_html_e( 'Saving...', 'gdpr-cookie-consent' ); ?>' : '<?php esc_html_e( 'Save Changes', 'gdpr-cookie-consent' ); ?>' }}</c-button>
					</div>
                </div>

                <hr id="preview-btn-setting-nav-seperator">

                <!--A/B Testing-->
                <c-tabs variant="pills" ref="active_tab" class="gdpr-cookie-consent-settings-nav">
                    <c-tab title="<?php esc_attr_e( 'A/B Testing', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#ab_testing" id="gdpr-cookie-consent-ab-testing">
				    	<div id="ab-testing-container">
				    		<div class="ab_test_data_wait_loader_container">
				    			<div class="data_wait_loader"></div>
				    		</div>
				    		<div v-html="ab_testing_data"></div>
				    	</div>
				    </c-tab>
                </c-tabs>
            </div>
        </c-form>
    </c-container>
</div>