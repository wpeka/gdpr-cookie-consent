var jQuery = jQuery.noConflict();

jQuery(document).ready(function () {
    // localised variable
    const isProActivated = gdpr_localize_data.is_pro_activated;
    const adminUrl = gdpr_localize_data.admin_url;

    if (isProActivated) {
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


});
