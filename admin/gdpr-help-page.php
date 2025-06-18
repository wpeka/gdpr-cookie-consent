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
         <div class="gdpr-cookie-consent-settings-nav">
            <div class="">
               <div class="tab-content">
                  <div data-v-0f37f1a8="" class="tab-pane active" id="wplegalpages-help-general">
						<h4><?php echo esc_html('Support','wplegalpages'); ?></h4>
						<p><?php echo esc_html('Got a question or need help with our plugins? We are here to help and guide you.','wplegalpages'); ?></p>
                     <div class="card">
                        <div class="card-body">
                           <div class="row">
                              <div class="col-sm-6 col">
                                 <div role="group" class="form-group">
                                    <label for="sup-name" class="screen-reader-label"><?php esc_attr_e('help name'); ?></label>
                                    <input id="sup-name" type="text" name="sup-name" placeholder="Name" class="form-control">
                                 </div>
                              </div>
                              <div class="col-sm-6 col">
                                 <div role="group" class="form-group">
                                    <label for="sup-email" class="screen-reader-label"><?php esc_attr_e('help email'); ?></label>
                                  	<input id="sup-email" type="text" name="sup-email" placeholder="Email" class="form-control">
                                    <div data-lastpass-icon-root="" style="position: relative !important; height: 0px !important; width: 0px !important; float: left !important;"></div>
                                 </div>
                              </div>
							   <div class="col-sm-12 col">
                                 <div role="group" class="form-group">
                                    <label for="sup-message" class="screen-reader-label"><?php esc_attr_e('help message'); ?></label>
                                  	<textarea id="sup-message" rows="10" cols="115" name="sup-message" placeholder="Message" class="form-textarea" ></textarea>
                                    <div data-lastpass-icon-root="" style="position: relative !important; height: 0px !important; width: 0px !important; float: left !important;"></div>
                                 </div>
                              </div>
                           </div>
                          
                          
                        </div>
                     </div>
                     <div class="gdpr-settings-bottom">
                        <div class="gdpr-help-save-button"><input type="submit" name="sup-submit" class="btn btn-info" value="Submit"></input></div>
                     </div>
                  </div>
                  
                  
               </div>
            </div>
         </div>
      </div>
   </form>
</div>
</div>