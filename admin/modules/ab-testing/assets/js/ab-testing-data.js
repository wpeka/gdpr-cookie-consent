jQuery(document).ready(function ($) {
    const $abTestingContainer = $('#ab-testing-container');
    $.ajax({
        url: ab_testing_ajax.ajax_url,
        method: 'POST',
        data: {
            action: 'wpl_ab_testing_tab',
        },
        success: function (response) {
            $('.ab_test_data_wait_loader_container').css("display","none");
            if (window.gen && typeof window.gen.refreshABTestingData === 'function') {
                window.gen.refreshABTestingData(response.data.html);
            } else {
                console.error('Vue instance not found or refreshCookieScannerData method missing.');
            }
            setTimeout(function(){window.integrate_ab_testing_auth()},1000);
        },
        error: function () {
            $abTestingContainer.html('<p>Error loading cookie scanner data.</p>');
        }
    });
});