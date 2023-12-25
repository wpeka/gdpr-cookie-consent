var jQuery = jQuery.noConflict();
// jQuery(document).ready(function () {


// 	const isProActivated = gdpr_localize_data.is_pro_activated;


// 	if ( isProActivated ) {
// 		jQuery('.gdpr-cookie-consent-admin-tabs-section').addClass('pro-is-activated');
// 		jQuery('.gdpr-cookie-consent-admin-tab').addClass('pro-is-activated');
// 	}

//     // Hide all tab contents initially except the first one
//     jQuery('.gdpr-cookie-consent-admin-tab-content').not(':first').hide();

// 	// Set Dashboard tab as active by default
//     jQuery('.gdpr-cookie-consent-admin-dashboard-tab').addClass('active-tab');
//     jQuery('#gdpr_dashboard').show();

//     // On tab click, show the corresponding content and hide others
//     jQuery('.gdpr-cookie-consent-admin-tabs').on('click', '.gdpr-cookie-consent-admin-tab', function() {
//         var tabId = jQuery(this).data('tab');

// 		 // Remove active class from all tabs
// 		 jQuery('.gdpr-cookie-consent-admin-tab').removeClass('active-tab');

//         // Hide all tab contents
//         jQuery('.gdpr-cookie-consent-admin-tab-content').hide();

//         // Show the selected tab content
//         jQuery('#' + tabId).show();
// 		jQuery(this).addClass('active-tab');
//     });



// });


jQuery(document).ready(function () {
    const isProActivated = gdpr_localize_data.is_pro_activated;

    if (isProActivated) {
        jQuery('.gdpr-cookie-consent-admin-tabs-section').addClass('pro-is-activated');
        jQuery('.gdpr-cookie-consent-admin-tab').addClass('pro-is-activated');
    }

    // Hide all tab contents initially except the first one
    jQuery('.gdpr-cookie-consent-admin-tab-content').not(':first').hide();

    // On tab click, show the corresponding content and hide others
    jQuery('.gdpr-cookie-consent-admin-tabs').on('click', '.gdpr-cookie-consent-admin-tab', function () {
        var tabId = jQuery(this).data('tab');

        // Remove active class from all tabs
        jQuery('.gdpr-cookie-consent-admin-tab').removeClass('active-tab');

        // Hide all tab contents
        jQuery('.gdpr-cookie-consent-admin-tab-content').hide();

        // Show the selected tab content
        jQuery('#' + tabId).show();
        jQuery(this).addClass('active-tab');

        // Store the active tab ID in sessionStorage
        sessionStorage.setItem('activeTab', tabId);
    });

    // Retrieve the active tab from sessionStorage on page load
    var activeTab = sessionStorage.getItem('activeTab');
    if (activeTab) {
        // Remove active class from all tabs
        jQuery('.gdpr-cookie-consent-admin-tab').removeClass('active-tab');

        // Hide all tab contents
        jQuery('.gdpr-cookie-consent-admin-tab-content').hide();

        // Show the stored active tab content
        jQuery('#' + activeTab).show();
        jQuery('[data-tab="' + activeTab + '"]').addClass('active-tab');
    }

	/////
	//make url dynamic
	// jQuery('#policyDataTabContainer').load('http://localhost/gdprscript/wp-admin/edit.php?post_type=gdprpolicies .wrap');

	// Function to add the a tag after .wp-heading-inline
	function addExportTagPolicy() {
		var newATag = '<a href="http://localhost/gdprscript/wp-admin/admin-post.php?action=gdpr_policies_export.csv" class="policy-export-btn">Export as CSV</a>';
		jQuery('#policyDataTabContainer .wp-heading-inline').after(newATag);
	}
	function addImportTagPolicy() {
		var newATag = '<a href="http://localhost/gdprscript/wp-admin/edit.php?page=gdpr-policies-import" class="policy-import-btn">Import From CSV</a>';
		jQuery('#policyDataTabContainer .wp-heading-inline').after(newATag);
	}

	// Load content into #consentLogDataTabContainer and then add the a tag
	jQuery('#policyDataTabContainer').load('http://localhost/gdprscript/wp-admin/edit.php?post_type=gdprpolicies .wrap', function() {
		// Once content is loaded, check if .wp-heading-inline exists, then add the a tag
		if (jQuery('.wp-heading-inline').length > 0) {
			addExportTagPolicy();
			addImportTagPolicy();
		}
	});



	// Function to add the a tag after .wp-heading-inline
	function addATag() {
		var newATag = '<a href="http://localhost/gdprscript/wp-admin/admin-post.php?action=export.csv&s=" class="consent-logs-export-btn">Export as CSV</a>';
		jQuery('#consentLogDataTabContainer .wp-heading-inline').after(newATag);
	}

	// Load content into #consentLogDataTabContainer and then add the a tag

	if ( isProActivated ) {
		jQuery('#consentLogDataTabContainer').load('http://localhost/gdprscript/wp-admin/edit.php?post_type=wplconsentlogs .wrap', function() {
			// Once content is loaded, check if .wp-heading-inline exists, then add the a tag
			if (jQuery('.wp-heading-inline').length > 0) {
				addATag();
			}
		});
	}

});
