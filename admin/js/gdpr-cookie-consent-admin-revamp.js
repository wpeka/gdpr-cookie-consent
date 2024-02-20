var jQuery = jQuery.noConflict();

jQuery(document).ready(function () {
    // localised variable
    const isProActivated = gdpr_localize_data.is_pro_activated;
    const adminUrl = gdpr_localize_data.admin_url;
	const ajaxurl = gdpr_localize_data.ajaxurl;
    if (!isProActivated) {
        jQuery('.gdpr-cookie-consent-admin-tabs-section').addClass('pro-is-activated');
        jQuery('.gdpr-cookie-consent-admin-tab').addClass('pro-is-activated');
    }

    // Hide all tab contents initially except the first one
    jQuery('.gdpr-cookie-consent-admin-tab-content').not(':first').hide();
	jQuery('.gdpr-cookie-consent-admin-dashboard-tab').addClass('active-tab');
    jQuery('#gdpr_dashboard').show();

    // On tab click, show the corresponding content and update URL hash
    jQuery('.gdpr-cookie-consent-admin-tabs').on('click', '.gdpr-cookie-consent-admin-tab', function (event) {
		event.preventDefault();
        var tabId = jQuery(this).data('tab');

        // Remove active class from all tabs
        jQuery('.gdpr-cookie-consent-admin-tab').removeClass('active-tab');

        // Hide all tab contents
        jQuery('.gdpr-cookie-consent-admin-tab-content').hide();

        // Show the selected tab content
        jQuery('#' + tabId).show();
        jQuery(this).addClass('active-tab');

        // Update URL hash with the tab ID
		history.pushState({}, '', '#' + tabId);
    });

    // Retrieve the active tab from URL hash on page load
    var hash = window.location.hash;

    if (hash) {
        var tabId = hash.substring(1); // Remove '#' from the hash

		const substr = 'cookie_settings#';

		if ( tabId.includes(substr) ) {
			tabId = 'cookie_settings'
		}
		// Remove active class from all tabs
        jQuery('.gdpr-cookie-consent-admin-tab').removeClass('active-tab');

        // Hide all tab contents
        jQuery('.gdpr-cookie-consent-admin-tab-content').hide();

        // Show the stored active tab content
        jQuery('#' + tabId).show();
        jQuery('[data-tab="' + tabId + '"]').addClass('active-tab');
    }
	// load the clicked link

	if ('scrollRestoration' in window.history) {
		window.history.scrollRestoration = 'manual'
	  }

	// for free links
	jQuery('.gdpr-quick-link-item.settings_free').on('click', function(e) {

		var linkUrl = jQuery('.gdpr-quick-link-item.settings_free a').attr('href');

		window.location.assign(linkUrl);
		location.reload();

	});
	jQuery('.gdpr-quick-link-item.cookie_banner_free').on('click', function(e) {

		var linkUrl = jQuery('.gdpr-quick-link-item.cookie_banner_free a').attr('href');

		window.location.assign(linkUrl);
		location.reload();

	});
	// for pro links
	jQuery('.gdpr-quick-link-item.settings').on('click', function(e) {

		var linkUrl = jQuery('.gdpr-quick-link-item.settings a').attr('href');

		window.location.assign(linkUrl);
		location.reload();
	});
	jQuery('.gdpr-quick-link-item.consent_logs').on('click', function(e) {

		var linkUrl = jQuery('.gdpr-quick-link-item.consent_logs a').attr('href');

		window.location.assign(linkUrl);
		location.reload();
	});
	jQuery('.gdpr-dashboard-cl-view-all-logs').on('click', function(e) {

		var linkUrl = jQuery('.gdpr-dashboard-cl-view-all-logs span a').attr('href');

		window.location.assign(linkUrl);
		location.reload();
	});
	jQuery('.gdpr-quick-link-item.policy_data').on('click', function(e) {

		var linkUrl = jQuery('.gdpr-quick-link-item.policy_data a').attr('href');

		window.location.assign(linkUrl);
		location.reload();
	});

	jQuery('.gdpr-quick-link-item.scan_cookies').on('click', function(e) {

		var linkUrl = jQuery('.gdpr-quick-link-item.scan_cookies a').attr('href');

		window.location.assign(linkUrl);
		location.reload();

	});
	jQuery('.gdpr-quick-link-item.geo_targeting').on('click', function(e) {

		var linkUrl = jQuery('.gdpr-quick-link-item.geo_targeting a').attr('href');

		window.location.assign(linkUrl);
		location.reload();

	});
	jQuery('.gdpr-quick-link-item.cookie_banner').on('click', function(e) {

		var linkUrl = jQuery('.gdpr-quick-link-item.cookie_banner a').attr('href');

		window.location.assign(linkUrl);
		location.reload();

	});
	jQuery('.gdpr-quick-link-item.banner_template').on('click', function(e) {

		var linkUrl = jQuery('.gdpr-quick-link-item.banner_template a').attr('href');

		window.location.assign(linkUrl);
		location.reload();

	});
	jQuery('.gdpr-quick-link-item.script_blocker').on('click', function(e) {

		var linkUrl = jQuery('.gdpr-quick-link-item.script_blocker a').attr('href');

		window.location.assign(linkUrl);
		location.reload();

	});
	jQuery('.gdpr-quick-link-item.policy_data').on('click', function(e) {

		var linkUrl = jQuery('.gdpr-quick-link-item.policy_data a').attr('href');

		window.location.assign(linkUrl);
		location.reload();

	});

	jQuery('.gdpr-cookie-summary-last-title a').on('click', function(e) {

		var linkUrl = jQuery('.gdpr-cookie-summary-last-title a').attr('href');
		window.location.assign(linkUrl);
		location.reload();

	});

	jQuery('.gdpr-dashboard-maxmind-integrate a').on('click', function(e) {

		var linkUrl = jQuery('.gdpr-dashboard-maxmind-integrate a').attr('href');
		window.location.assign(linkUrl);
		location.reload();

	});

	jQuery('.gdpr-dashboard-scan-now a').on('click', function(e) {

		var linkUrl = jQuery('.gdpr-dashboard-scan-now a').attr('href');
		window.location.assign(linkUrl);
		location.reload();

	});

	//check if data req is on, then show data req tab.
	if ( gdpr_localize_data.is_data_req_on == 'false' ) {
		jQuery('.gdpr-cookie-consent-admin-data-request-tab').hide();
	}

	//jquery for paginations for consent log tab
	jQuery('#consentLogDataTabContainer .pagination-links a').each(function() {
        var href = jQuery(this).attr('href');
        href += '#consent_logs';
        jQuery(this).attr('href', href);
    });
	//jquery for paginations for data-req tab
	jQuery('#dataRequestContainer .pagination-links a').each(function() {
        var href = jQuery(this).attr('href');
        href += '#consent_logs';
        jQuery(this).attr('href', href);
    });
	//jquery for paginations for policy data tab
	jQuery('#policyDataTabContainer .pagination-links a').each(function() {
		var href = jQuery(this).attr('href');
		href += '#policy_data';
		jQuery(this).attr('href', href);
	});

	/**
	 * Javascript functionality for SaaS API Framework.
	*/

	/**
	 * Add an event listener to listen for messages sent from the server.
	*/
	window.addEventListener("message", function(event) {
		//event is originated on server
		if ( event.isTrusted && event.origin === gdpr_localize_data.gdpr_app_url ) {
			storeAuth(event.data)
		}
	});

	/**
	 * start authentication process
	*/

	/**
	 * clicked on new account.
	*/
	jQuery('.rst-start-auth').on('click', startAuth );

	/**
	 * Clicked on connect to exiting account.
	*/
	jQuery('.api-connect-to-account-btn').on('click', startAuth );

	/**
	 * Function to Start the Authentication Process.
	 *
	 * @param {*} event
	 */
	function startAuth(event) {

		// Prevent the default action of the event.
		event.preventDefault();

		// Create spinner element
		var spinner = jQuery('<div class="gdpr-spinner"></div>');

		// Append spinner to .gdpr-cookie-consent-connect-api-container div.

		var container = jQuery('.gdpr-cookie-consent-connect-api-container');
		container.css('position', 'relative'); // Ensure container has relative positioning.
		container.append(spinner);

		// Make an AJAX request.
		jQuery.ajax(
			{
				url  : gdpr_localize_data.ajaxurl,
				type : 'POST',
				data : {
					action      : 'gdpr_cookie_consent_app_start_auth',
					_ajax_nonce : gdpr_localize_data._ajax_nonce,
				},
				beforeSend: function() {
					// Show spinner before AJAX call starts
					spinner.show();
				},
				complete: function() {
					// Hide spinner after AJAX call completes
					spinner.hide();
				}
			}
		)
		.done(
			function ( response ) {

				// Get the width and height of the viewport.
				var viewportWidth = window.innerWidth;
				var viewportHeight = window.innerHeight;

				// Set the dimensions of the popup.
				var popupWidth = 367;
				var popupHeight = 650;

				// Calculate the position to center the popup.
				var leftPosition = (viewportWidth - popupWidth) / 2;
				var topPosition = (viewportHeight - popupHeight) / 2;

				// Open the popup window at the calculated position.
				var e = window.open(
				response.data.url,
				"_blank",
				"location=no,width=" + popupWidth + ",height=" + popupHeight + ",left=" + leftPosition + ",top=" + topPosition + ",scrollbars=0"
				);

				if (null === e) {
					console.log('Failed to open the authentication window');
				} else {
					e.focus();// Focus on the popup window.
				}

			}
		);


	}

	/**
	 * Store the Authentication Data
	 * @param {*} data
	*/
	function storeAuth(data) {

		// Create spinner element
		var spinner = jQuery('<div class="gdpr-spinner"></div>');
   		jQuery('#wpbody-content').append(spinner);

		//Make Ajax Call
		jQuery.ajax({
			type: 'POST',
			url: gdpr_localize_data.ajaxurl,
			data: {
				action: 'gdpr_cookie_consent_app_store_auth',
				_ajax_nonce : gdpr_localize_data._ajax_nonce,
				response: data.response,
				origin: data.origin,

			},
			success: function(response) {

				// Hide the spinner after the success HTML is loaded
				spinner.hide();

				// Html content to display sucsess screen after successfull connection.
				var successHtml = '<div id="gdpr_app-connect-success" class="gdpr_app-connect-success">' +
				'<div class="gdpr_app-connect-success-container">' +
				'<div class="gdpr_app-connect-success-icon"></div>' +
				'<div class="gdpr_app-connect-success-message">' +
				'<h2>Your website is connected to WP Cookie Consent</h2>' +
				'<p>You can now continue to manage all your existing settings and access all WP Cookie Consent features from your web app account.</p>' +
				'</div>' +
				'<div class="gdpr_app-connect-success-actions">' +
				'<button id="gdpr_app-connect-success-action" class="rst-button rst-button-medium rst-external-link">Go to the plugin</button>' +
				'</div>' +
				'</div>' +
				'</div>';

				jQuery('#wpbody-content').html(successHtml);

				//reload the window when button is clicked.
				jQuery('#gdpr_app-connect-success-action').on('click', function() {
					location.reload();
				});
				//reload the window after settimeout.
				setTimeout(function() {
					location.reload();
				}, 3000);
			},
			error: function(error) {
				// Handle error response
				console.error('Error sending data to PHP:', error);
			}
		});

	}

	/**
	 * click on disconnect button to disconnect api connection.
	*/
	jQuery('.api-connection-disconnect-btn').on('click', disconnectAppAuth );

	/**
	 * Function to Disconnect the API Connection
	*/
	function disconnectAppAuth () {

		// Create spinner element
		var spinner = jQuery('<div class="gdpr-spinner"></div>');

		// Append spinner to .gdpr-cookie-consent-connect-api-container div
		var container = jQuery('.gdpr-cookie-consent-disconnect-api-container');
		container.css('position', 'relative'); // Ensure container has relative positioning
		container.append(spinner);

		//Make Ajax Requests.
		jQuery.ajax(
			{
				url  : gdpr_localize_data.ajaxurl,
				type : 'POST',
				data : {
					action      : 'gdpr_cookie_consent_app_delete_auth',
					_ajax_nonce : gdpr_localize_data._ajax_nonce,
				},
				beforeSend: function() {
					// Show spinner before AJAX call starts
					spinner.show();
				},
				complete: function() {
					// Hide spinner after AJAX call completes
					spinner.hide();
				}
			}
		).done(
			function ( response ) {

				// Html content to display success screen after disconnect.
				var successHtml = '<div id="gdpr_app-connect-success" class="gdpr_app-connect-success">' +
				'<div class="gdpr_app-connect-success-container">' +
				'<div class="gdpr_app-connect-success-icon"></div>' +
				'<div class="gdpr_app-connect-success-message">' +
				'<h2>Successfully disconnected!!!</h2>' +
				'</div>' +
				'</div>' +
				'</div>';

				jQuery('#wpbody-content').html(successHtml);

				//reload the window after settimeout.
				setTimeout(function() {
					location.reload();
				}, 3000);
			}
		);
	}


});
