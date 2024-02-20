/**
 * Frontend JavaScript.
 *
 * @package    WPL_Cookie_Consent
 * @subpackage WPL_Cookie_Consent/admin
 * @author     wpeka <https://club.wpeka.com>
 */
(function ( $ ){
	'use strict';

	jQuery(document).ready(function($) {
		$('#wpl-datarequest-submit').on('click', function() {
			var formData = $('#wpl-datarequest-form').serialize();

			$.ajax({
				type: 'POST',
				url: data_req_obj.ajax_url,
				data: {
					action: 'data_reqs_form_submit',
					form_data: formData,
				},
				success: function(response) {
					var wpl_alert = $('.wpl-datarequest.wpl-alert');

					if (response.success) {
						wpl_alert.find('#wpl-message').html(response.message);
						$('#wpl-datarequest-form').hide();
						wpl_alert.removeClass('wpl-error').addClass('wpl-success').show();
						setTimeout(function() {
							location.reload();
						}, 1800);
					} else {
						wpl_alert.find('#wpl-message').html(response.message);
						wpl_alert.removeClass('wpl-success').addClass('wpl-error').show();
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log('AJAX Error:', errorThrown);
					$('#wpl-message').html('Form submission failed. Please try again.');
					$('#wpl-datarequest-form').show();
					$('#wpl-success-message').hide();
				}
			});
		});
	});


})( jQuery );
