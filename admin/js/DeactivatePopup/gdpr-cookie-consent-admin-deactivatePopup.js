/**
 * Admin JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin
 * @author     wpeka <https://club.wpeka.com>
 */

jQuery(function ($) {
  'use strict';
  
  $('.gdpr-deactivatePopup-form-wrapper-outer').hide();
  
  // triggering the deactivate button id of the wordpress deactivate button.
  var deactivateLink = $('#the-list').find('[data-slug="gdpr-cookie-consent"] span.deactivate a');
 
  deactivateLink.on('click', function (event) {
      event.preventDefault();
      $('.gdpr-deactivatePopup-form-wrapper-outer').show();
  });
  
  // cancel button.
  $('.cancel_button').on('click',function(){
    $('.cancel_button').hide();
  });

  // to change the appearance of the deactivate button.
  $('#with_data').on('change',function(){
    $('.deactivate-delete-button').show();
    $('.deactivate-button').hide();
  });
  var is_keep_data_selected = false;
  $('#without_data').on('change',function(){
    $('.deactivate-button').show();
    $('.deactivate-delete-button').hide();
    is_keep_data_selected = true;
  });
  var adminUrl = gdpr_localize_deactivatePopup_data.ajaxurl;

  $('.deactivate-delete-button').on('click', function(event) {
    event.preventDefault(); 
    var reason = $('input[name="reason"]:checked').val();
      // Make AJAX request to deactivate the plugin with data.
      $.ajax({
          url: adminUrl, 
          type: 'POST', 
          data: {
              action : 'gdpr_cookie_consent_deactivatePopup', 
              _ajax_nonce : gdpr_localize_deactivatePopup_data._ajax_nonce,
              reason : 'with_data',
          },
          success: function(response) {
              // Reload the page after successful deactivation
              location.reload();
          },
          error: function(error) {
              // Handle AJAX error.
              console.error(error);
          },
          complete: function() {
              location.href = deactivateLink.attr('href');
          }
      });
});
    $('.deactivate-button').on('click', function(event) {
    event.preventDefault();
    var reason = $('input[name="reason"]:checked').val();
        // Make AJAX request to deactivate the plugin without data.
        if(is_keep_data_selected){
        $.ajax({
            url: adminUrl, 
            type: 'POST', 
            data: {
                action : 'gdpr_cookie_consent_deactivatePopup', 
                _ajax_nonce : gdpr_localize_deactivatePopup_data._ajax_nonce,
                reason : 'without_data',
            },
            success: function(response) {
                // Reload the page after successful deactivation
                location.reload();
            },
            error: function(error) {
                // Handle AJAX error.
                console.error(error);
            },
            complete: function() {
                location.href = deactivateLink.attr('href');
            }
        });
        }
        else{
            alert("Please select an option!");
        }
});
});

