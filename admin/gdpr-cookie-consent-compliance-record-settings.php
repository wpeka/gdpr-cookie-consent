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

$the_options       = Gdpr_Cookie_Consent::gdpr_get_settings();
$is_consent_log_on = isset( $the_options['logging_on'] ) ? $this->convert_boolean( $the_options['logging_on'] ) : false;
?>
<div class="gdpr-cookie-consent-app-container" id="gdpr-cookie-consent-compliance-record-settings">
    <c-container class="gdpr-cookie-consent-settings-container gdpr-cookie-consent-compliance-record-settings-container">
        <div class="gdpr-cookie-consent-settings-form">
            <div class="gdpr-cookie-consent-settings-content">
                <div id="gdpr-cookie-consent-save-settings-alert-crd">{{success_error_message}}</div>
                <div id="gdpr-cookie-consent-updating-settings-alert-crd">Updating Setting</div>

                <c-tabs variant="pills" ref="active_tab_crd" class="gdpr-cookie-consent-settings-nav">
					<!-- Consent Logs -->
					<?php if ( $is_consent_log_on ) : ?>
                    <c-tab href="#compliance_records#consent_logs" class="consent-logs" title="<?php esc_attr_e( 'Consent Logs', 'gdpr-cookie-consent' ); ?>" id="gdpr-cookie-consent-consent-log-records" >
                        <c-card class="consent_log_card">
                            <c-card-body>
                                <?php do_action( 'add_consent_log_content' ); ?>
                            </c-card-body>
                        </c-card>
                    </c-tab>
					<?php endif; ?>

                    <!-- Data Requests -->
                    <c-tab href="#compliance_records#data_request" class="data-request" title="<?php esc_attr_e( 'Data Requests', 'gdpr-cookie-consent' ); ?>" id="gdpr-cookie-consent-data-request-records" >
                        <c-card class="data_request_card">
							<div class="gdpr-preview-publish-btn gdpr-preview-publish-btn-crd">
								<c-button :disabled="save_loading" class="gdpr-publish-btn" @click="saveCookieSettings">{{ save_loading ? '<?php esc_html_e( 'Saving...', 'gdpr-cookie-consent' ); ?>' : '<?php esc_html_e( 'Save Changes', 'gdpr-cookie-consent' ); ?>' }}</c-button>
							</div>

                            <c-card-body>
                                <?php do_action( 'add_data_request_content' ); ?>
                            </c-card-body>
                        </c-card>
                    </c-tab>

                    <!-- A/B Testing -->
                    <c-tab href="#compliance_records#ab_testing" class="ab-testing" title="<?php esc_attr_e( 'A/B Testing', 'gdpr-cookie-consent' ); ?>" id="gdpr-cookie-consent-abtesting-records" >
                        <c-card class="abtesting_card">
                            <div class="gdpr-preview-publish-btn gdpr-preview-publish-btn-crd">
                                <c-button :disabled="save_loading" class="gdpr-publish-btn" @click="saveCookieSettings">{{ save_loading ? '<?php esc_html_e( 'Saving...', 'gdpr-cookie-consent' ); ?>' : '<?php esc_html_e( 'Save Changes', 'gdpr-cookie-consent' ); ?>' }}</c-button>
                            </div>

                            <c-card-body>
                                <c-form id="gcc-save-abtesting-settings-form" method="post" spellcheck="false" class="gdpr-cookie-consent-settings-form">
                                    <div id="ab-testing-container-crd">
                                        <div class="ab_test_data_wait_loader_container">
                                            <div class="data_wait_loader"></div>
                                        </div>
                                    </div>
                                </c-form>
                            </c-card-body>
                        </c-card>
                    </c-tab>
                </c-tabs>
            </div>
        </div>
    </c-container>
</div>
