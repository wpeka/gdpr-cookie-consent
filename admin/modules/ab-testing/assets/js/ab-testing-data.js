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
            if (window.abt && typeof window.abt.refreshABTestingData === 'function') {
                window.abt.refreshABTestingData(response.data.html);
            } else {
                console.error('Vue instance not found or refreshABTestingData method missing.');
            }
            setTimeout(function(){window.integrate_ab_testing_auth()},1000);
        },
        error: function () {
            $abTestingContainer.html('<p>Error loading cookie scanner data.</p>');
        }
    });
});