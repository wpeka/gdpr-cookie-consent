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
   <?php wp_nonce_field('wplegalpages_support_request_nonce', 'wplegalpages_nonce'); ?>
  
   <input type="hidden" name="action" value="wplegalpages_support_request">

      <div class="gdpr-cookie-consent-settings-content">
         <div class="gdpr-cookie-consent-settings-nav">
            <div class="">
               <ul class="nav nav-pills">
                  <li data-v-0f37f1a8="" class="nav-item" id="wplegalpages-help-general"><a data-v-0f37f1a8="" href="#settings#general" target="_self" class="nav-link active"><?php echo esc_html('Open a Ticket','wplegalpages'); ?></a></li>
				   </ul>
            </div>
            <div class="">
               <div class="tab-content">
                  <div data-v-0f37f1a8="" class="tab-pane active" id="wplegalpages-help-general">
						<h4><?php echo esc_html('Support','wplegalpages'); ?></h4>
						<p><?php echo esc_html('Got a question or need help with our plugins? We are here to help and guide you.','wplegalpages'); ?></p>
                     <div class="card">
                        <div class="card-body">
                           <!-- <div class="row wplegalpages-label-row">
                              <div class="col-sm-6 col"><label><?php echo esc_html('Name','wplegalpages'); ?></label></div>
                              <div class="col-sm-6 col"><label><?php echo esc_html('Email','wplegalpages'); ?></label></div>
                           </div> -->
                           <div class="row">
                              <div class="col-sm-6 col">
                                 <div role="group" class="form-group">
                                    <input type="text" name="sup-name" placeholder="Name" class="form-control">
                                 </div>
                              </div>
                              <div class="col-sm-6 col">
                                 <div role="group" class="form-group">
                                  	<input type="text" name="sup-email" placeholder="Email" class="form-control">
                                    <div data-lastpass-icon-root="" style="position: relative !important; height: 0px !important; width: 0px !important; float: left !important;"></div>
                                 </div>
                              </div>
							   <div class="col-sm-12 col">
                                 <div role="group" class="form-group">
                                  	<textarea rows="10" cols="115" name="sup-message" placeholder="Message" class="form-textarea" ></textarea>
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
<?php 

if (isset($_POST['sup-submit']) ) {
	error_log('in isset');
	// Sanitize and validate input
	$name = sanitize_text_field($_POST['sup-name']);
	error_log('$name='.$name);
	$email = sanitize_email($_POST['sup-email']);
	error_log('email=='.$email);
	$message = sanitize_textarea_field($_POST['sup-message']);
	error_log('message=='.$message);

	// Support email details
	$to = "support@wpeka.com"; // Replace with your support email
	$subject = "Support Request from $name";
	$body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
	$headers = ['Reply-To: ' . $email];

	// Send the email
	if (wp_mail($to, $subject, $body, $headers)) {
		error_log('in wp_mail send==');
		add_action('admin_notices', function() {
			echo '<div class="notice notice-success is-dismissible"><p>Your message has been sent successfully.</p></div>';
		});
	} else {
		error_log('in elseeee wp_mail send==');
		add_action('admin_notices', function() {
			echo '<div class="notice notice-error is-dismissible"><p>There was an error sending your message. Please try again later.</p></div>';
		});
	}
}
?>