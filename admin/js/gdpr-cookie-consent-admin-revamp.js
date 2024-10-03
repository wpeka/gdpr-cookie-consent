var jQuery = jQuery.noConflict();

jQuery(document).ready(function () {
  // localised variable
  const isProActivated = gdpr_localize_data.is_pro_activated;
  const adminUrl = gdpr_localize_data.admin_url;
  const ajaxurl = gdpr_localize_data.ajaxurl;
  const is_user_connected = gdpr_localize_data.is_user_connected;
  if (!isProActivated) {
    jQuery(".gdpr-cookie-consent-admin-tabs-section").addClass(
      "pro-is-activated"
    );
    jQuery(".gdpr-cookie-consent-admin-tab").addClass("pro-is-activated");
  }

  // Hide all tab contents initially except the first one
  jQuery(".gdpr-cookie-consent-admin-tab-content").not(":first").hide();
  jQuery(".gdpr-cookie-consent-admin-dashboard-tab").addClass("active-tab");
  jQuery("#gdpr_dashboard").show();
  // On tab click, show the corresponding content and update URL hash
  jQuery(".gdpr-cookie-consent-admin-tabs").on(
    "click",
    ".gdpr-cookie-consent-admin-tab",
    function (event) {
      event.preventDefault();
      var tabId = jQuery(this).data("tab");

      // Remove active class from all tabs
      jQuery(".gdpr-cookie-consent-admin-tab").removeClass("active-tab");

      // Hide all tab contents
      jQuery(".gdpr-cookie-consent-admin-tab-content").hide();

      // Show the selected tab content
      jQuery("#" + tabId).show();
      jQuery(this).addClass("active-tab");

      // Update URL hash with the tab ID
      history.pushState({}, "", "#" + tabId);
    }
  );

  // Retrieve the active tab from URL hash on page load
  var hash = window.location.hash;

  if (hash) {
    var tabId = hash.substring(1); // Remove '#' from the hash

    const substr = "cookie_settings#";

    if (tabId.includes(substr)) {
      tabId = "cookie_settings";
    }
    // Remove active class from all tabs
    jQuery(".gdpr-cookie-consent-admin-tab").removeClass("active-tab");

    // Hide all tab contents
    jQuery(".gdpr-cookie-consent-admin-tab-content").hide();

    // Show the stored active tab content
    jQuery("#" + tabId).show();
    jQuery('[data-tab="' + tabId + '"]').addClass("active-tab");
  }
  // load the clicked link

  if ("scrollRestoration" in window.history) {
    window.history.scrollRestoration = "manual";
  }

  //scan again link redirection

  jQuery(".gdpr_scan_again_link").on("click", function (e) {
    var linkUrl = jQuery(".gdpr_scan_again_link a").attr("href");

    window.location.assign(linkUrl);
    location.reload();
  });

  //cookie notice configure link redirection

  jQuery(".gdpr_notice_configure_link").on("click", function (e) {
    var linkUrl = jQuery(".gdpr_notice_configure_link a").attr("href");

    window.location.assign(linkUrl);
    location.reload();
  });

  // for free links
  jQuery(".gdpr-quick-link-item.settings_free").on("click", function (e) {
    var linkUrl = jQuery(".gdpr-quick-link-item.settings_free a").attr("href");

    window.location.assign(linkUrl);
    location.reload();
  });
  jQuery(".gdpr-quick-link-item.cookie_banner_free").on("click", function (e) {
    var linkUrl = jQuery(".gdpr-quick-link-item.cookie_banner_free a").attr(
      "href"
    );

    window.location.assign(linkUrl);
    location.reload();
  });
  // for pro links
  jQuery(".gdpr-quick-link-item.settings").on("click", function (e) {
    var linkUrl = jQuery(".gdpr-quick-link-item.settings a").attr("href");

    window.location.assign(linkUrl);
    location.reload();
  });
  jQuery(".gdpr-quick-link-item.consent_logs").on("click", function (e) {
    var linkUrl = jQuery(".gdpr-quick-link-item.consent_logs a").attr("href");

    window.location.assign(linkUrl);
    location.reload();
  });
  jQuery(".gdpr-dashboard-cl-view-all-logs").on("click", function (e) {
    var linkUrl = jQuery(".gdpr-dashboard-cl-view-all-logs span a").attr(
      "href"
    );

    window.location.assign(linkUrl);
    location.reload();
  });
  jQuery(".gdpr-quick-link-item.policy_data").on("click", function (e) {
    var linkUrl = jQuery(".gdpr-quick-link-item.policy_data a").attr("href");

    window.location.assign(linkUrl);
    location.reload();
  });

  jQuery(".gdpr-dashboard-activation-tab ").on("click", function (e) {
    var linkUrl = jQuery(".gdpr-dashboard-activation-tab a").attr("href");

    window.location.assign(linkUrl);
    location.reload();
  });

  jQuery(".gdpr-quick-link-item.scan_cookies").on("click", function (e) {
    var linkUrl = jQuery(".gdpr-quick-link-item.scan_cookies a").attr("href");

    window.location.assign(linkUrl);
    location.reload();
  });
  jQuery(".gdpr-quick-link-item.geo_targeting").on("click", function (e) {
    var linkUrl = jQuery(".gdpr-quick-link-item.geo_targeting a").attr("href");

    window.location.assign(linkUrl);
    location.reload();
  });
  jQuery(".gdpr-quick-link-item.cookie_banner").on("click", function (e) {
    var linkUrl = jQuery(".gdpr-quick-link-item.cookie_banner a").attr("href");

    window.location.assign(linkUrl);
    location.reload();
  });
  jQuery(".gdpr-quick-link-item.banner_template").on("click", function (e) {
    var linkUrl = jQuery(".gdpr-quick-link-item.banner_template a").attr(
      "href"
    );

    window.location.assign(linkUrl);
    location.reload();
  });
  jQuery(".gdpr-quick-link-item.script_blocker").on("click", function (e) {
    var linkUrl = jQuery(".gdpr-quick-link-item.script_blocker a").attr("href");

    window.location.assign(linkUrl);
    location.reload();
  });
  jQuery(".gdpr-quick-link-item.policy_data").on("click", function (e) {
    var linkUrl = jQuery(".gdpr-quick-link-item.policy_data a").attr("href");

    window.location.assign(linkUrl);
    location.reload();
  });

  jQuery(".gdpr-cookie-summary-last-title a").on("click", function (e) {
    var linkUrl = jQuery(".gdpr-cookie-summary-last-title a").attr("href");
    window.location.assign(linkUrl);
    location.reload();
  });

  jQuery(".gdpr-dashboard-scan-now a").on("click", function (e) {
    var linkUrl = jQuery(".gdpr-dashboard-scan-now a").attr("href");
    window.location.assign(linkUrl);
    location.reload();
  });

  //check if data req is on, then show data req tab.
  if (gdpr_localize_data.is_data_req_on == "false") {
    jQuery(".gdpr-cookie-consent-admin-data-request-tab").hide();
  }

  //check if consent log is on, then show consent log tab.
  if (gdpr_localize_data.is_consent_log_on == "false") {
    jQuery(".gdpr-cookie-consent-admin-consent-logs-tab").hide();
  }
  //jquery for paginations for consent log tab
  jQuery("#consentLogDataTabContainer .pagination-links a").each(function () {
    var href = jQuery(this).attr("href");
    href += "#consent_logs";
    jQuery(this).attr("href", href);
  });
  //jquery for paginations for data-req tab
  jQuery("#dataRequestContainer .pagination-links a").each(function () {
    var href = jQuery(this).attr("href");
    href += "#data_request";
    jQuery(this).attr("href", href);
  });
  //jquery for paginations for policy data tab
  jQuery("#policyDataTabContainer .pagination-links a").each(function () {
    var href = jQuery(this).attr("href");
    href += "#policy_data";
    jQuery(this).attr("href", href);
  });

  /**
   * Javascript functionality for SaaS API Framework.
   */

  /**
   * Add an event listener to listen for messages sent from the server.
   */
  window.addEventListener("message", function (event) {
    // Check if the event is originated on server and not successful
    if (event.isTrusted && event.origin === gdpr_localize_data.gdpr_app_url) {
      if (!event.data.success) {
        const scanBtn = jQuery(".scan-now-btn");
        const popup = jQuery("#popup-site-excausted");
        const cancelButton = jQuery(".popup-image");

        popup.fadeIn();

        cancelButton.off("click").on("click", function (e) {
          popup.fadeOut();
        });
      } else {
        gdprStoreAuth(event.data);
      }
    }
  });

  /**
   * modal pop after successfull connection or disconnection
   */

  var fixedBanner = jQuery(".gdpr-cookie-consent-admin-fixed-banner");

  jQuery("#gdpr-wpcc-notice").insertAfter(fixedBanner);
  jQuery("#gdpr-disconnect-wpcc-notice").insertAfter(fixedBanner);

  // check if user is connected, show connection popup
  if (is_user_connected) {
    jQuery("#gdpr-wpcc-notice").removeClass("gdpr-hidden");
    jQuery("#gdpr-wpcc-notice").show();
  } else if (localStorage.getItem("gdprDisconnect") === "true") {
    jQuery("#gdpr-disconnect-wpcc-notice").removeClass("gdpr-hidden");
    jQuery("#gdpr-disconnect-wpcc-notice").show();
  }

  // Check if the 'gdprConnectPopupHide' item in localStorage is set to 'true'.
  if (localStorage.getItem("gdprConnectPopupHide") === "true") {
    jQuery("#gdpr-wpcc-notice").hide();
    jQuery("#gdpr-disconnect-wpcc-notice").hide();
  }

  // Add a click event listener to the element with class 'notice-dismiss'.
  jQuery("#gdpr-wpcc-notice .notice-dismiss").on("click", closeDiv);

  /**
   * Method to close the div.
   */
  function closeDiv() {
    jQuery("#gdpr-wpcc-notice").hide();
    localStorage.setItem("gdprConnectPopupHide", "true");
  }

  // Add a click event listener to the element with class 'notice-dismiss'.
  jQuery("#gdpr-disconnect-wpcc-notice .notice-dismiss").on(
    "click",
    closeDivDisconnect
  );

  /**
   * Method to close the div.
   */
  function closeDivDisconnect() {
    jQuery("#gdpr-disconnect-wpcc-notice").hide();
    localStorage.setItem("gdprConnectPopupHide", "true");
  }

  /**
   * start authentication process
   */

  /**
   * clicked on new account.
   */
  jQuery(".gdpr-start-auth").on("click", gdprStartAuth);
  jQuery(".gdpr-dashboard-start-auth").on("click", gdprStartAuth);

  // clicked for activate pro plugin
  jQuery(document).ready(function ($) {
    $(".gdpr-activate-plugin").on("click", function (e) {
      e.preventDefault();
      // Get the base URL of the current page
      var baseURL = window.location.origin;
      // Construct the URL for plugins.php
      var pluginsPageURL = baseURL + "/wp-admin/plugins.php";
      // Redirect to the plugins.php page
      window.location.href = pluginsPageURL;
    });
  });
  jQuery(document).ready(function ($) {
    $(".gdpr-activate-api-plugin").on("click", function (e) {
      e.preventDefault();
      // Get the base URL of the current page
      var baseURL = window.location.origin;
      // Construct the URL for plugins.php
      var pluginsPageURL =
        baseURL + "/wp-admin/admin.php?page=gdpr-cookie-consent#activation_key";
      // Redirect to the plugins.php page
      window.location.href = pluginsPageURL;
      location.reload();
    });
  });
  jQuery(document).ready(function () {
    var gdprTimer, ccpaTimer, bothTimer;

    // GDPR hover function
    jQuery("#gdpr-visitors-condition-radio-btn-disabled-gdpr").hover(
      function () {
        gdprTimer = setTimeout(function () {
          jQuery(".gdpr-eu_visitors_message-gdpr").css("display", "block");
        }, 250); // 250ms delay
      },
      function () {
        clearTimeout(gdprTimer); // Clear the timer to prevent delayed show
        jQuery(".gdpr-eu_visitors_message-gdpr").css("display", "none");
      }
    );

    // CCPA hover function
    jQuery("#gdpr-visitors-condition-radio-btn-disabled-ccpa").hover(
      function () {
        ccpaTimer = setTimeout(function () {
          jQuery(".gdpr-eu_visitors_message-ccpa").css("display", "block");
        }, 250); // 250ms delay
      },
      function () {
        clearTimeout(ccpaTimer); // Clear the timer to prevent delayed show
        jQuery(".gdpr-eu_visitors_message-ccpa").css("display", "none");
      }
    );

    // Both hover function
    jQuery("#gdpr-visitors-condition-radio-btn-disabled-both").hover(
      function () {
        bothTimer = setTimeout(function () {
          jQuery(".gdpr-eu_visitors_message-both").css("display", "block");
        }, 250); // 250ms delay
      },
      function () {
        clearTimeout(bothTimer); // Clear the timer to prevent delayed show
        jQuery(".gdpr-eu_visitors_message-both").css("display", "none");
      }
    );
  });
  /**
   * Clicked on connect to exiting account.
   */
  jQuery(".api-connect-to-account-btn").on("click", gdprStartAuth);

  /**
   * Function to Start the Authentication Process.
   *
   * @param {*} event
   */
  function gdprStartAuth(event) {
    // Prevent the default action of the event.
    event.preventDefault();

    var is_new_user = this.classList.contains("gdpr-start-auth");

    // Create spinner element
    var spinner = jQuery('<div class="gdpr-spinner"></div>');

    // Append spinner to .gdpr-cookie-consent-connect-api-container div.

    var container = jQuery(".gdpr-cookie-consent-connect-api-container");
    container.css("position", "relative"); // Ensure container has relative positioning.
    container.append(spinner);

    // Make an AJAX request.
    jQuery
      .ajax({
        url: gdpr_localize_data.ajaxurl,
        type: "POST",
        data: {
          action: "gdpr_cookie_consent_app_start_auth",
          _ajax_nonce: gdpr_localize_data._ajax_nonce,
          is_new_user: is_new_user,
        },
        beforeSend: function () {
          // Show spinner before AJAX call starts
          spinner.show();
        },
        complete: function () {
          // Hide spinner after AJAX call completes
          spinner.hide();
        },
      })
      .done(function (response) {
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
          "location=no,width=" +
            popupWidth +
            ",height=" +
            popupHeight +
            ",left=" +
            leftPosition +
            ",top=" +
            topPosition +
            ",scrollbars=0"
        );

        if (null === e) {
          console.log("Failed to open the authentication window");
        } else {
          e.focus(); // Focus on the popup window.
        }
      });
  }

  /**
   * Clicked on connect to exiting account.
   */
  jQuery(".gdpr-cookie-consent-admin-upgrade-button").on("click", gdprPaidAuth);
  jQuery(".cookie-consent-upgrade-to-pro-banner").on("click", gdprPaidAuth);
  /**
   * Store the Authentication Data
   * @param {*} data
   */

  function gdprPaidAuth(event) {
    // Prevent the default action of the event.
    event.preventDefault();

    var is_new_user = this.classList.contains("gdpr-start-auth");

    // Create spinner element
    var spinner = jQuery('<div class="gdpr-spinner"></div>');

    // Append spinner to .gdpr-cookie-consent-connect-api-container div.

    var container = jQuery(".gdpr-cookie-consent-connect-api-container");
    container.css("position", "relative"); // Ensure container has relative positioning.
    container.append(spinner);

    // Make an AJAX request.
    jQuery
      .ajax({
        url: gdpr_localize_data.ajaxurl,
        type: "POST",
        data: {
          action: "gdpr_cookie_consent_app_paid_auth",
          _ajax_nonce: gdpr_localize_data._ajax_nonce,
        },
        beforeSend: function () {
          // Show spinner before AJAX call starts
          spinner.show();
        },
        complete: function () {
          // Hide spinner after AJAX call completes
          spinner.hide();
        },
      })
      .done(function (response) {
        // Get the width and height of the viewport
        var viewportWidth = window.innerWidth;
        var viewportHeight = window.innerHeight;

        // Set the dimensions of the popup
        var popupWidth = 1260;
        var popupHeight = 740;

        // Calculate the position to center the popup
        var leftPosition = (viewportWidth - popupWidth) / 2;
        var topPosition = (viewportHeight - popupHeight) / 2;
        // Open the popup window at the calculated position
        var e = window.open(
          response.data.url,
          "_blank",
          "location=no,width=" +
            popupWidth +
            ",height=" +
            popupHeight +
            ",left=" +
            leftPosition +
            ",top=" +
            topPosition +
            ",scrollbars=0"
        );
        if (null == e) {
          console.log("error while opening the popup window");
        }
      });
  }

  /**
   * Store the Authentication Data
   * @param {*} data
   */
  function gdprStoreAuth(data) {
    // Create spinner element
    var spinner = jQuery('<div class="gdpr-spinner"></div>');
    jQuery("#wpbody-content").append(spinner);

    //Make Ajax Call
    jQuery.ajax({
      type: "POST",
      url: gdpr_localize_data.ajaxurl,
      data: {
        action: "gdpr_cookie_consent_app_store_auth",
        _ajax_nonce: gdpr_localize_data._ajax_nonce,
        response: data.response,
        origin: data.origin,
        no_of_scans: data.no_of_scans ? data.no_of_scans : "",
      },
      success: function (response) {
        // Hide the spinner after the success HTML is loaded
        spinner.hide();

        // remove hidden instance from the local storage
        localStorage.removeItem("gdprConnectPopupHide");
        //remove disconnect from local storage when user connects to the api
        localStorage.removeItem("gdprDisconnect");
        var baseUrl = window.location.origin;
        var relativePath = "/wp-admin/admin.php?page=gdpr-cookie-consent";
        var tabHash = "#cookie_settings#cookie_list"; // Adjust this to your specific hash

        // Construct the full URL
        var fullUrl = baseUrl + relativePath + tabHash;

        //reload the window after settimeout.
        setTimeout(function () {
          window.location.href = fullUrl;
          location.reload();
        }, 100);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // Handle error response
        console.error("AJAX call failed:", textStatus, errorThrown);
        console.error("Response text:", jqXHR.responseText);
      },
    });
  }

  /**
   * click on disconnect button to disconnect api connection.
   */
  jQuery(".api-connection-disconnect-btn").on("click", disconnectAppAuth);

  /**
   * Function to Disconnect the API Connection
   */
  function disconnectAppAuth() {
    // Create spinner element
    var spinner = jQuery('<div class="gdpr-spinner"></div>');

    // Append spinner to .gdpr-connection-tab-card div
    var container = jQuery(".gdpr-connection-tab-card");
    container.css("position", "relative"); // Ensure container has relative positioning
    container.append(spinner);

    //Make Ajax Requests.
    jQuery
      .ajax({
        url: gdpr_localize_data.ajaxurl,
        type: "POST",
        data: {
          action: "gdpr_cookie_consent_app_delete_auth",
          _ajax_nonce: gdpr_localize_data._ajax_nonce,
        },
        beforeSend: function () {
          // Show spinner before AJAX call starts
          spinner.show();
        },
        complete: function () {
          // Hide spinner after AJAX call completes
          spinner.hide();
        },
      })
      .done(function (response) {
        // remove hidden instance from the local storage
        localStorage.removeItem("gdprConnectPopupHide");
        // set the gdprDisconnect to true when user clicks on the disconnect.
        localStorage.setItem("gdprDisconnect", "true");

        //reload the window after settimeout.
        setTimeout(function () {
          location.reload();
        }, 100);
      });
  }

  /**
   * cookie settings perferences functionality
   */
  jQuery(document).ready(function ($) {
    $(".gpdr_cookie_settings_btn").on("click", function (e) {
      e.preventDefault();
      // Fade out the div to the bottom
      $("#banner-preview-main-container").animate(
        {
          opacity: 0,
          height: "toggle",
        },
        "fast",
        function () {
          // Animation complete
          // You can add any additional actions here if needed
        }
      );

      $(".gdpr_messagebar_detail").removeClass("hide-popup");

      // Fade in the .gdpr_messagebar_detail element
      $(".gdpr_messagebar_detail").fadeIn("slow");
    });

    // Handle close button click
    $(".gdpr_action_button.close").on("click", function (e) {
      e.preventDefault();
      // Fade out the .gdpr_messagebar_detail
      $(".gdpr_messagebar_detail").fadeOut("fast", function () {});
      $("#banner-preview-main-container").animate(
        {
          opacity: 1,
          height: "toggle",
        },
        "slow"
      );
    });
  });

  jQuery(document).ready(function ($) {
    $("#cookie_action_settings_preview").click(function () {
      if ("true" === $("#gdpr-cookie-consent-iabtcf-on").attr("value")) {
        $(".gdpr_messagebar_detail .gdpr-about-cookies").css("display", "none");
        $(".gdpr_messagebar_detail .gdpr-about-cookies.iabtcf").css(
          "display",
          "block"
        );
        $(".gdpr_messagebar_detail.layout-classic .gdpr-iab-navbar").css(
          "display",
          "flex"
        );
        $(
          ".gdpr_messagebar_detail.layout-default .gdpr-iab-navbar #gdprIABTabFeatures,.gdpr_messagebar_detail.layout-default .gdpr-iab-navbar #gdprIABTabVendors"
        ).css("display", "list-item");
        // $( ".gdpr_messagebar_detail .category-group.iabtcf-off" ).css( 'display', 'none' );
        $(".gdpr_messagebar_detail .outer-container").css("display", "block");
      }
      if ("false" === $("#gdpr-cookie-consent-iabtcf-on").attr("value")) {
        $(".gdpr_messagebar_detail.layout-classic .gdpr-iab-navbar").css(
          "display",
          "none"
        );
        $(
          ".gdpr_messagebar_detail.layout-default .gdpr-iab-navbar #gdprIABTabFeatures,.gdpr_messagebar_detail.layout-default .gdpr-iab-navbar #gdprIABTabVendors"
        ).css("display", "none");
        $(".gdpr_messagebar_detail .gdpr-about-cookies").css(
          "display",
          "block"
        );
        $(".gdpr_messagebar_detail .gdpr-about-cookies.iabtcf").css(
          "display",
          "none"
        );
        // $( ".gdpr_messagebar_detail .category-group.iabtcf-off" ).css( 'display', 'block' );
        $(".gdpr_messagebar_detail .outer-container").css("display", "none");
      }
    });
    $(".gdpr_messagebar_detail .category-group .category-item hr").css(
      "border-top",
      "1px solid " + gdpr_localize_data.button_accept_button_color
    );
    $(".gdpr_messagebar_detail.dark_row .category-group .category-item hr").css(
      "border-top",
      "1px solid #73DBC0"
    );
    $(
      ".gdpr_messagebar_detail .gdpr-iab-navbar .gdpr-iab-navbar-button.active"
    ).css("color", gdpr_localize_data.button_accept_button_color);
    $(
      ".gdpr_messagebar_detail.layout-classic .gdpr-iab-navbar .gdpr-iab-navbar-button.active"
    ).css(
      "border-bottom",
      "2px solid " + gdpr_localize_data.button_accept_button_color
    );
    $(".gdpr_messagebar_detail.layout-default  .category-group").css(
      "background-color",
      gdpr_localize_data.background
    );
    $(".gdpr_messagebar_detail.layout-default  .category-group.outer").css(
      "border-left",
      "1px solid " + gdpr_localize_data.button_accept_button_color
    );
    $(
      ".gdpr_messagebar_detail.layout-default.dark_row  .category-group.outer"
    ).css("border-left", "1px solid #73DBC0");
    $(
      ".gdpr_messagebar_detail .category-group .toggle-group .always-active"
    ).css("color", gdpr_localize_data.button_accept_button_color);
    $(
      ".gdpr_messagebar_detail .category-group .toggle-group .always-active"
    ).css("color", gdpr_localize_data.button_accept_button_color);
    // gdpr_localize_data.button_accept_button_color = "#00f";cookie_action_save
    $(".gdpr-iab-navbar-item").click(function () {
      $(
        ".gdpr_messagebar_detail .gdpr-iab-navbar .gdpr-iab-navbar-button.active"
      ).css("color", gdpr_localize_data.button_accept_button_color);
      $(".gdpr-iab-navbar-item", this);
      $(".tabContainer").css("display", "none");
      switch (this.id) {
        case "gdprIABTabCategory":
          $(".cat").css("display", "block");
          break;
        case "gdprIABTabFeatures":
          $(".feature-group").css("display", "block");
          break;
        case "gdprIABTabVendors":
          $(".vendor-group").css("display", "block");
          break;
      }
      if (!$(this).children(".gdpr-iab-navbar-button").hasClass("active")) {
        $(".gdpr-iab-navbar-button").removeClass("active");
        $(".gdpr-iab-navbar-button").css("color", "inherit");
        $(".gdpr-iab-navbar-button").css("border-bottom", "none");
        $(this).children(".gdpr-iab-navbar-button").addClass("active");
        $(this)
          .children(".gdpr-iab-navbar-button.active")
          .css("color", gdpr_localize_data.button_accept_button_color);
        $(this)
          .children(".gdpr-iab-navbar-button.active")
          .css(
            "border-bottom",
            "2px solid " + gdpr_localize_data.button_accept_button_color
          );
        $(this)
          .siblings(".gdpr-iab-navbar-button.active")
          .css("display", "none");
      }
      $(
        ".gdpr_messagebar_detail.layout-default .gdpr-iab-navbar .gdpr-iab-navbar-button.active"
      ).css("border", "none");
    });
    $(".gdpr-default-category-toggle.gdpr-column").click(function () {
      $(".gdpr-default-category-toggle.gdpr-column", this);
      if (!$(this).children(".gdpr-columns").hasClass("active-group")) {
        $(".gdpr-columns").removeClass("active-group");
        $(".gdpr-columns").css(
          "background-color",
          gdpr_localize_data.background
        );
        $(this).children(".gdpr-columns").addClass("active-group");
        $(this)
          .children(".gdpr-columns")
          .css(
            "background-color",
            gdpr_localize_data.button_accept_button_color
          );
      }
      if ($(this).siblings(".description-container").hasClass("hide")) {
        $(".description-container").addClass("hide");
        $(this).siblings(".description-container").removeClass("hide");
      }
    });
    $(".gdpr-category-toggle.gdpr-column").click(function () {
      $(".gdpr-category-toggle.gdpr-column", this);
      if (!$(this).children(".gdpr-columns").hasClass("active-group")) {
        $(".gdpr-columns").removeClass("active-group");
        $(".gdpr-columns").css(
          "background-color",
          gdpr_localize_data.background
        );
        $(".gdpr-columns .dashicons").removeClass("dashicons-arrow-up-alt2");
        $(".gdpr-columns .dashicons").addClass("dashicons-arrow-down-alt2");
        $(this).children(".gdpr-columns").addClass("active-group");
        $(".toggle-group")
          .find("div.always-active")
          .css("color", gdpr_localize_data.button_accept_button_color);
        $(this)
          .siblings(".toggle-group")
          .find("div.always-active")
          .css("color", gdpr_localize_data.button_accept_button_color);
        $(this)
          .children(".gdpr-columns")
          .css("background-color", gdpr_localize_data.background);
        $(this)
          .children(".gdpr-columns")
          .find(".dashicons")
          .removeClass("dashicons-arrow-down-alt2");
        $(this)
          .children(".gdpr-columns")
          .find(".dashicons")
          .addClass("dashicons-arrow-up-alt2");
      } else {
        $(".gdpr-columns").removeClass("active-group");
        $(this)
          .siblings(".toggle-group")
          .find("div.always-active")
          .css("color", gdpr_localize_data.button_accept_button_color);
        $(".gdpr-columns").css(
          "background-color",
          gdpr_localize_data.background
        );
        $(this)
          .children(".gdpr-columns")
          .find(".dashicons")
          .removeClass("dashicons-arrow-up-alt2");
        $(this)
          .children(".gdpr-columns")
          .find(".dashicons")
          .addClass("dashicons-arrow-down-alt2");
      }
      if ($(this).siblings(".description-container").hasClass("hide")) {
        $(".description-container").addClass("hide");
        $(this).siblings(".description-container").removeClass("hide");
      } else {
        $(".description-container").addClass("hide");
      }
    });
    $(".gdpr-category-toggle.inner-gdpr-column").click(function () {
      $(".gdpr-category-toggle.inner-gdpr-column", this);
      if (!$(this).children(".inner-gdpr-columns").hasClass("active-group")) {
        $(".inner-gdpr-columns").removeClass("active-group");
        $(".inner-gdpr-columns").css(
          "background-color",
          gdpr_localize_data.background
        );
        $(".inner-gdpr-columns .dashicons").removeClass(
          "dashicons-arrow-up-alt2"
        );
        $(".inner-gdpr-columns .dashicons").addClass(
          "dashicons-arrow-down-alt2"
        );
        $(this).children(".inner-gdpr-columns").addClass("active-group");
        $(".toggle-group")
          .find("div.always-active")
          .css("color", gdpr_localize_data.button_accept_button_color);
        $(this)
          .siblings(".toggle-group")
          .find("div.always-active")
          .css("color", gdpr_localize_data.button_accept_button_color);
        $(this)
          .children(".inner-gdpr-columns")
          .css("background-color", gdpr_localize_data.background);
        $(this)
          .children(".inner-gdpr-columns")
          .find(".dashicons")
          .removeClass("dashicons-arrow-down-alt2");
        $(this)
          .children(".inner-gdpr-columns")
          .find(".dashicons")
          .addClass("dashicons-arrow-up-alt2");
      } else {
        $(".inner-gdpr-columns").removeClass("active-group");
        $(this)
          .siblings(".toggle-group")
          .find("div.always-active")
          .css("color", gdpr_localize_data.button_accept_button_color);
        $(".inner-gdpr-columns").css(
          "background-color",
          gdpr_localize_data.background
        );
        $(this)
          .children(".inner-gdpr-columns")
          .find(".dashicons")
          .removeClass("dashicons-arrow-up-alt2");
        $(this)
          .children(".inner-gdpr-columns")
          .find(".dashicons")
          .addClass("dashicons-arrow-down-alt2");
      }
      if ($(this).siblings(".inner-description-container").hasClass("hide")) {
        $(".inner-description-container").addClass("hide");
        $(this).siblings(".inner-description-container").removeClass("hide");
      } else {
        $(".inner-description-container").addClass("hide");
      }
    });
    $(".gdpr-default-category-toggle.inner-gdpr-column").click(function () {
      $(".gdpr-default-category-toggle.inner-gdpr-column", this);
      if (!$(this).children(".inner-gdpr-columns").hasClass("active-group")) {
        $(".inner-gdpr-columns").removeClass("active-group");
        $(".inner-gdpr-columns").css(
          "background-color",
          gdpr_localize_data.background
        );
        $(this).children(".inner-gdpr-columns").addClass("active-group");
        $(this)
          .children(".inner-gdpr-columns")
          .css(
            "background-color",
            gdpr_localize_data.button_accept_button_color
          );
      }
      if ($(this).siblings(".inner-description-container").hasClass("hide")) {
        $(".inner-description-container").addClass("hide");
        $(this).siblings(".inner-description-container").removeClass("hide");
      }
    });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  jQuery('#data_request input[id="current-page-selector"]').attr(
    "id",
    "current-page-selector-data-request"
  );
  jQuery('#consent_logs input[id="current-page-selector"]').attr(
    "id",
    "current-page-selector-consent_logs"
  );
  jQuery('#policy_data input[id="current-page-selector"]').attr(
    "id",
    "current-page-selector-policy-data"
  );
  jQuery('#wpl-dnsmpd-filter-datarequest input[id="_wpnonce"]').attr(
    "id",
    "wpnonce-wpl-dnsmpd-filter-datarequest"
  );
  jQuery('#wpl-dnsmpd-filter-consent-log input[id="_wpnonce"]').attr(
    "id",
    "wpnonce-wpl-dnsmpd-filter-consent-log"
  );
  jQuery('#wpl-dnsmpd-filter input[id="_wpnonce"]').attr(
    "id",
    "wpnonce-wpl-dnsmpd-filter-policy-data"
  );
  jQuery(
    '#wp-admin-bar-nexcess-mapps-delete-expired-transients input[id="nonce"]'
  ).attr("id", "nonce-delete-expired-transients");
});
