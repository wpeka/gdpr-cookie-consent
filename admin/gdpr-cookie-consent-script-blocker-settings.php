<div class="gdpr-cookie-consent-app-container" id="gdpr-cookie-consent-script_blocker-settings">
    <c-container class="gdpr-cookie-consent-settings-container gdpr-cookie-consent-script-blocker-container">
        <c-form id="gcc-save-script-blocker-settings-form" method="post" spellcheck="false" class="gdpr-cookie-consent-settings-form">
            <input type="hidden" name="gcc_settings_form_nonce_script_blocker" value="<?php echo esc_attr( wp_create_nonce( 'gcc-settings-form-nonce-script-blocker' ) ); ?>"/>
            
            <div class="gdpr-cookie-consent-settings-content">
                <div id="gdpr-cookie-consent-save-settings-alert">{{success_error_message}}</div>
				<div id="gdpr-cookie-consent-updating-settings-alert">Updating Setting</div>

                <div class="gdpr-banner-preview-save-btn">
                    <div></div>
                    <div class="gdpr-preview-publish-btn">
						<c-button :disabled="save_loading" class="gdpr-publish-btn" @click="saveScriptBlockerSettings">{{ save_loading ? '<?php esc_html_e( 'Saving...', 'gdpr-cookie-consent' ); ?>' : '<?php esc_html_e( 'Save Changes', 'gdpr-cookie-consent' ); ?>' }}</c-button>
					</div>
                </div>

                <hr id="preview-btn-setting-nav-seperator">

               <c-tabs variant="pills" ref="active_tab" class="gdpr-cookie-consent-settings-nav">
                    <?php do_action( 'gdpr_settings_script_blocker_tab' ); ?>
               </c-tabs>
            </div>
        </c-form>
    </c-container>
</div>