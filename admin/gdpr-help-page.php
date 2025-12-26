<?php
/**
 * Provide a admin area view for the settings.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package    Wplegalpages
 * @subpackage Wplegalpages/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="gdpr-cookie-consent-help-app" class="gdpr-cookie-consent-app-container">
<div class="gdpr-cookie-consent-settings-container">
   <div class="wplegalpages-marketing-banner"></div>
   <form  method="post" id="support_form" name="support_form" spellcheck="false" action="admin.php?page=wplp-dashboard#help-page" class="wplegalpages-settings-form">
   <?php wp_nonce_field('gdpr_support_request_nonce', 'gdpr_nonce'); ?>
  
   <input type="hidden" name="action" value="gdpr_support_request">

      <div class="gdpr-cookie-consent-settings-content">
         <div class="gdpr-cookie-consent-settings-nav-help-page">
            <div class="">
               <div class="tab-content">
                  <main class="gdpr-cookie-consent-help-container">
                    <section class="gdpr-cookie-consent-help-header">
                      <h1><?php esc_html_e( 'Help & Resources', 'gdpr-cookie-consent' ); ?></h1>
                      <p><?php esc_html_e( 'Need help or want to explore more? Find everything you need to get the most out of WPLP Compliance Platform.', 'gdpr-cookie-consent' ); ?></p>
                    </section>

                    <section class="gdpr-cookie-consent-help-cards">
                      <div class="gdpr-cookie-consent-help-card">
                        <div class="gdpr-cookie-consent-help-icon">
                          <span>
                            <img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/File_dock_fill.png' ); ?>" alt="<?php esc_attr_e( 'Help Documentation', 'gdpr-cookie-consent' ); ?>">
                          </span>
                        </div>
                        <h3><?php esc_html_e( 'Documentation', 'gdpr-cookie-consent' ); ?></h3>
                        <p><?php esc_html_e( 'Browse our step-by-step guides and articles to help you get started and troubleshoot with ease.', 'gdpr-cookie-consent' ); ?></p>
                        <a href="<?php echo esc_url( 'https://wplegalpages.com/docs/wplp-docs/', 'gdpr-cookie-consent' ); ?>" target="_blank"><?php esc_html_e( "Read Documentation" ); ?><img class="gdpr-other-plugin-arrow" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/dashboard-icons/right-arrow.svg' ); ?>"></a>
                      </div>

                      <div class="gdpr-cookie-consent-help-card">
                        <div class="gdpr-cookie-consent-help-icon">
                          <span>
                            <img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/Video_file_fill.png' ); ?>" alt="<?php esc_attr_e( 'Help Video Tutorials', 'gdpr-cookie-consent' ); ?>">
                          </span>
                        </div>
                        <h3><?php esc_html_e( 'Video Tutorials', 'gdpr-cookie-consent' ); ?></h3>
                        <p><?php esc_html_e( 'Prefer learning by watching? Explore our tutorials to see the plugin in action and learn how to use it effectively.', 'gdpr-cookie-consent' ); ?></p>
                        <a href="<?php echo esc_url( 'https://wplegalpages.com/docs/wp-cookie-consent/video-guides/video-resources/','gdpr-cookie-consent' ); ?>" target="_blank"><?php esc_html_e( "Watch Now" ); ?><img class="gdpr-other-plugin-arrow" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/dashboard-icons/right-arrow.svg' ); ?>"></a>
                      </div>

                      <div class="gdpr-cookie-consent-help-card">
                        <div class="gdpr-cookie-consent-help-icon">
                          <span>
                            <img src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/Subttasks_fill.png' ); ?>" alt="<?php esc_attr_e( 'Help Request Feature', 'gdpr-cookie-consent' ); ?>">
                          </span>
                        </div>
                        <h3><?php esc_html_e( 'Request a Feature', 'gdpr-cookie-consent' ); ?></h3>
                        <p><?php esc_html_e( 'Got an idea that could make the plugin better? We’d love to hear from you.', 'gdpr-cookie-consent' ); ?></p>
                        <a href="<?php echo esc_url( 'https://wplegalpages.com/contact-us/', 'gdpr-cookie-consent' ); ?>" target="_blank"><?php esc_html_e( "Request Now" ); ?><img class="gdpr-other-plugin-arrow" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/dashboard-icons/right-arrow.svg' ); ?>"></a>
                      </div>
                    </section>

                    <section class="gdpr-cookie-consent-help-footer">
                      <h2><?php esc_html_e( 'Need Further Help?', 'gdpr-cookie-consent' ); ?></h2>
                      <p><?php esc_html_e( 'Can’t find what you’re looking for? Escalate your issue to our support team and we’ll get back to you shortly.', 'gdpr-cookie-consent' ); ?></p>
                      <p><?php echo wp_kses_post( __( 'Email us at <a href="mailto:support@wplegalpages.com">support@wplegalpages.com</a>', 'gdpr-cookie-consent' ) ); ?></p>
                    </section>
                  </main>
               </div>
            </div>
         </div>
      </div>
   </form>
</div>
</div>
