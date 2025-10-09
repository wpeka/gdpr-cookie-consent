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
            if (window.ckm && typeof window.ckm.refreshCookieScannerData === 'function') {
                window.ckm.refreshCookieScannerData(response.data.html);
            } else {
                console.error('Vue instance not found or refreshCookieScannerData method missing.');
            }
            setTimeout(function(){window.integrate_cookie_scanner_auth()},1000);
        },
        error: function () {
            $scannerContainer.html('<p>Error loading cookie scanner data.</p>');
        }
    });
});