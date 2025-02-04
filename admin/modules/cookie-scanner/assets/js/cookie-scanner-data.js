jQuery(document).ready(function ($) {
    const $scannerContainer = $('#cookie-scanner-container');
    $.ajax({
        url: cookie_scanner_ajax.ajax_url,
        method: 'POST',
        data: {
            action: 'wpl_cookie_scanner_card',
        },
        success: function (response) {
            $('.data_wait_loader_container').css("display","none");
            if (window.gen && typeof window.gen.refreshCookieScannerData === 'function') {
                window.gen.refreshCookieScannerData(response.data.html);
            } else {
                console.error('Vue instance not found or refreshCookieScannerData method missing.');
            }
        },
        error: function () {
            $scannerContainer.html('<p>Error loading cookie scanner data.</p>');
        }
    });
});