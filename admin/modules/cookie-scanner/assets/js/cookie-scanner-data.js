jQuery(document).ready(function ($) {
    // Target the container where the cookie scanner data will be loaded
    const $scannerContainer = $('#cookie-scanner-container');
	console.log("tryiing bhai");
    // Show loading indicator
    $scannerContainer.html('<p>Loading cookie scanner data...</p>');

    // Perform AJAX request to fetch data
    $.ajax({
        url: cookie_scanner_ajax.ajax_url,
        method: 'POST',
        data: {
            action: 'wpl_cookie_scanner_card',
        },
        success: function (response) {
            if (response.success) {
                $scannerContainer.html(response.data.html);
            } else {
                $scannerContainer.html('<p>' + response.data.message + '</p>');
            }
        },
        error: function () {
            $scannerContainer.html('<p>Error loading cookie scanner data.</p>');
        }
    });
});