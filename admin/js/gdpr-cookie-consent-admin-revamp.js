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
        // Remove active class from all tabs

		const substr = 'cookie_settings#';

		if ( tabId.includes(substr) ) {
			tabId = 'cookie_settings'
		}

        jQuery('.gdpr-cookie-consent-admin-tab').removeClass('active-tab');

        // Hide all tab contents
        jQuery('.gdpr-cookie-consent-admin-tab-content').hide();

        // Show the stored active tab content
        jQuery('#' + tabId).show();
        jQuery('[data-tab="' + tabId + '"]').addClass('active-tab');
    }

	// Function to add the a tag after .wp-heading-inline
	function addExportTagPolicy() {
		var exportPolicyUrl = adminUrl + 'admin-post.php?action=gdpr_policies_export.csv';
		var newATag = '<a href="'+ exportPolicyUrl +'" class="policy-export-btn">Export as CSV</a>';
		jQuery('#policyDataTabContainer .wp-heading-inline').after(newATag);
	}
	function addImportTagPolicy() {
		var importPolicyUrl = adminUrl + 'edit.php?page=gdpr-policies-import';
		var newATag = '<a href="' + importPolicyUrl + '" class="policy-import-btn">Import From CSV</a>';
		jQuery('#policyDataTabContainer .wp-heading-inline').after(newATag);
	}

	// Load content into #consentLogDataTabContainer and then add the a tag
	var policyDataLoadUrl = adminUrl + 'edit.php?post_type=gdprpolicies';

	jQuery('#policyDataTabContainer').load( policyDataLoadUrl + ' .wrap', function() {
		// Once content is loaded, check if .wp-heading-inline exists, then add the a tag
		if (jQuery('.wp-heading-inline').length > 0) {
			addExportTagPolicy();
			addImportTagPolicy();
		}
	});

	// Function to add the a tag after .wp-heading-inline
	function addATag() {
		var exportConsentLogUrl = adminUrl + 'admin-post.php?action=export.csv&s=';
		var newATag = '<a href="' + exportConsentLogUrl + '" class="consent-logs-export-btn">Export as CSV</a>';
		jQuery('#consentLogDataTabContainer .wp-heading-inline').after(newATag);
	}

	// Load content into #consentLogDataTabContainer and then add the a tag

	if ( isProActivated ) {

		var consentLogLoadUrl = adminUrl + 'edit.php?post_type=wplconsentlogs';

		jQuery('#consentLogDataTabContainer').load(consentLogLoadUrl+' .wrap', function() {
			// Once content is loaded, check if .wp-heading-inline exists, then add the a tag
			if (jQuery('.wp-heading-inline').length > 0) {
				addATag();
			}
		});
	}

});
