var jQuery = jQuery.noConflict();
jQuery(document).ready(function () {


	const isProActivated = gdpr_localize_data.is_pro_activated;


	if ( isProActivated ) {
		jQuery('.gdpr-cookie-consent-admin-tabs-section').addClass('pro-is-activated');
		jQuery('.gdpr-cookie-consent-admin-tab').addClass('pro-is-activated');
	}

    // Hide all tab contents initially except the first one
    jQuery('.gdpr-cookie-consent-admin-tab-content').not(':first').hide();

	// Set Dashboard tab as active by default
    jQuery('.gdpr-cookie-consent-admin-dashboard-tab').addClass('active-tab');
    jQuery('#gdpr_dashboard').show();

    // On tab click, show the corresponding content and hide others
    jQuery('.gdpr-cookie-consent-admin-tabs').on('click', '.gdpr-cookie-consent-admin-tab', function() {
        var tabId = jQuery(this).data('tab');

		 // Remove active class from all tabs
		 jQuery('.gdpr-cookie-consent-admin-tab').removeClass('active-tab');

        // Hide all tab contents
        jQuery('.gdpr-cookie-consent-admin-tab-content').hide();

        // Show the selected tab content
        jQuery('#' + tabId).show();
		jQuery(this).addClass('active-tab');
    });













});
