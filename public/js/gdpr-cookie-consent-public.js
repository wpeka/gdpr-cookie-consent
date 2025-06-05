/**
 * Frontend JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 * @author     wpeka <https://club.wpeka.com>
 */

GDPR_ACCEPT_COOKIE_NAME =
  typeof GDPR_ACCEPT_COOKIE_NAME !== "undefined"
    ? GDPR_ACCEPT_COOKIE_NAME
    : "wpl_viewed_cookie";
GDPR_CCPA_COOKIE_NAME =
  typeof GDPR_CCPA_COOKIE_NAME !== "undefined"
    ? GDPR_CCPA_COOKIE_NAME
    : "wpl_optout_cookie";
US_PRIVACY_COOKIE_NAME =
  typeof US_PRIVACY_COOKIE_NAME !== "undefined"
    ? US_PRIVACY_COOKIE_NAME
    : "usprivacy";
GDPR_ACCEPT_COOKIE_EXPIRE =
  typeof GDPR_ACCEPT_COOKIE_EXPIRE !== "undefined"
    ? GDPR_ACCEPT_COOKIE_EXPIRE
    : 365;
GDPR_CCPA_COOKIE_EXPIRE =
  typeof GDPR_CCPA_COOKIE_EXPIRE !== "undefined"
    ? GDPR_CCPA_COOKIE_EXPIRE
    : 365;

(function ($) {
  "use strict";

  /**
   * All of the code for your public-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  var GDPR_Cookie = {
    set: function (name, value, days) {
      var expires = "";
      if (days) {
        var date = new Date();
        date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
        var expires = "; expires=" + date.toUTCString();
      }
      document.cookie =
        name + "=" + encodeURIComponent(value) + expires + "; path=/";
    },
    read: function (name) {
      var nameEQ = name + "=";
      var ca = document.cookie.split(";");
      var ca_length = ca.length;
      for (var i = 0; i < ca_length; i++) {
        var c = ca[i];
        while (c.charAt(0) == " ") {
          c = c.substring(1, c.length);
        }
        if (c.indexOf(nameEQ) === 0) {
          return decodeURIComponent(c.substring(nameEQ.length, c.length));
        }
      }
      return null;
    },
    exists: function (name) {
      return this.read(name) !== null;
    },
    getallcookies: function () {
      var pairs = document.cookie.split(";");
      var cookieslist = {};
      var pairs_length = pairs.length;
      for (var i = 0; i < pairs_length; i++) {
        var pair = pairs[i].split("=");
        cookieslist[(pair[0] + "").trim()] = decodeURIComponent(pair[1]);
      }
      return cookieslist;
    },
    erase: function (name) {
      var domain = window.location.hostname;
      var topDomain = domain.split(".").slice(-2).join(".");
      document.cookie =
        name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
      document.cookie =
        name +
        "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" +
        domain +
        ";";
      document.cookie =
        name +
        "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=." +
        topDomain +
        ";";
    },
  };
  //for our gcm template in google tag manager
  var consentListeners = [];
    window.consentGiven = function (callback){
    consentListeners.push(callback);
  };
  //integration with wp consent api plugin
  window.wp_consent_type = 'optin';
    var event = new CustomEvent('wp_consent_type_defined');
  document.dispatchEvent( event );

  //debug mode for GCM
  function debugConsentState() {
    // Ensure the dataLayer exists and is an array.
    if (!window.dataLayer || !Array.isArray(window.dataLayer)) {
      console.log("Debug: dataLayer is not available.");
      return;
    }

    var defaultPresent = -1;
    var firstTag = -1;
    var updatePresent = -1;

    // Loop over each dataLayer entry.
    for (var i = 0; i < window.dataLayer.length; i++) {
      var entry = window.dataLayer[i];
      // Check if entry is an object and it has a "consent" property.
      if (entry && typeof entry === 'object' && entry[0] == "consent") {
        if (entry[1] === "default") {
          defaultPresent = i;
        }
        if (entry[1] === "update") {
				  updatePresent = i;
				}
      }
      if(entry[0] == undefined && firstTag == -1) firstTag = i;
    }

    // Log results to the console.
    if (defaultPresent == -1) {
      console.log("Debug: The default consent is missing. Make sure you have turned on support GCM, have atleast one default consent value set.");
    } else {
      console.log("Debug: The default consent successfully set to - ", window.dataLayer[defaultPresent][2]);
    }
    // Log results to the console.
		if (updatePresent != -1) {
		  console.log("Debug: The consent successfully updated to - ", window.dataLayer[updatePresent][2]);
		}
    if(defaultPresent != -1 && firstTag != -1 && defaultPresent < firstTag){
      console.log("Debug: Default consent was set in correct order.")
    }
    else{
      console.log("Debug: The default consent was not set in correct order. Make sure you have installed Google tag using scripts in script blocker section and GCM is turned on.")
    }
  }

  function debugUpdateConsentState (){
    if (!window.dataLayer || !Array.isArray(window.dataLayer)) {
      console.log("Debug: dataLayer is not available.");
      return;
    }

    var updatePresent = -1;

    for (var i = 0; i < window.dataLayer.length; i++) {
      var entry = window.dataLayer[i];
      if (entry && typeof entry === 'object' && entry[0] == "consent") {
        if (entry[1] === "update") {
          updatePresent = i;
        }
      }
    }


    // Log results to the console.
    if (updatePresent == -1) {
      console.log("Debug: The update consent did not work correctly. Contact support.");
    } else {
      console.log("Debug: The consent successfully updated to - ", window.dataLayer[updatePresent][2]);
    }
  }


  var gdpr_cookiebar_settings = gdpr_cookies_obj.gdpr_cookiebar_settings;
  var gdpr_ab_options = gdpr_cookies_obj.gdpr_ab_options;
  var gdpr_cookies_list = gdpr_cookies_obj.gdpr_cookies_list;
  var gdpr_consent_renew = gdpr_cookies_obj.gdpr_consent_renew;
  var gdpr_user_ip = gdpr_cookies_obj.gdpr_user_ip;
  var gdpr_do_not_track = gdpr_cookies_obj.gdpr_do_not_track;
  var gdpr_select_pages = gdpr_cookies_obj.gdpr_select_pages;
  var gdpr_select_sites = gdpr_cookies_obj.gdpr_select_sites;
  var consent_forwarding = gdpr_cookies_obj.consent_forwarding;
  var button_revoke_consent_text_color =
    gdpr_cookies_obj.button_revoke_consent_text_color;
  var button_revoke_consent_background_color =
    gdpr_cookies_obj.button_revoke_consent_background_color;
  var chosenBanner = gdpr_cookies_obj.chosenBanner;
  var is_iab_on = gdpr_cookies_obj.is_iabtcf_on;
  var is_gcm_on = gdpr_cookies_obj.is_gcm_on;
  var is_gcm_debug_on = gdpr_cookies_obj.is_gcm_debug_on;  
  // Set the value for the Multiple Legislation Banner Selection
  var multiple_legislation_current_banner = "gdpr";
  var browser_dnt_value = "";
  // Set the browser DNT value
  if (navigator.doNotTrack === "1") {
    // User has enabled Do Not Track
    browser_dnt_value = true;
  } else if (navigator.doNotTrack === "0") {
    browser_dnt_value = false;
  } else {
    browser_dnt_value = false;
  }
   // Run this check when the DOM is ready and when debug mode is on.
   if(is_gcm_debug_on == 'true'){
     document.addEventListener("DOMContentLoaded", function() {
       setTimeout(debugConsentState, 1000);
     });
   }
  var GDPR = {
    allowed_categories: [],
    set: function (args) {
      if (typeof JSON.parse !== "function") {
        console.log(
          "GDPRCookieConsent requires JSON.parse but your browser doesn't support it"
        );
        return;
      }

      this.settings = JSON.parse(args.settings);
      GDPR_ACCEPT_COOKIE_EXPIRE = this.settings.cookie_expiry;
      this.bar_elm = jQuery(this.settings.notify_div_id);
      this.show_again_elm = jQuery(this.settings.show_again_div_id);

      this.details_elm = this.bar_elm.find(".gdpr_messagebar_detail");

      /* buttons */
      this.main_button = jQuery("#cookie_action_accept");
      this.accept_all_button = jQuery("#cookie_action_accept_all");
      this.main_link = jQuery("#cookie_action_link");
      this.vendor_link = jQuery("#vendor-link");
      this.donotsell_link = jQuery("#cookie_donotsell_link");
      this.reject_button = jQuery("#cookie_action_reject");
      this.settings_button = jQuery("#cookie_action_settings");
      this.save_button = jQuery("#cookie_action_save");
      this.credit_link = jQuery("#cookie_credit_link");
      this.confirm_button = jQuery("#cookie_action_confirm");
      this.cancel_button = jQuery("#cookie_action_cancel");

      this.check_ccpa_eu();

      this.attachEvents();
      this.configButtons();

      // changing the color and background of cookie setting button.
      

      // bypassed consent.
      window.addEventListener("load", function () {
        const cancelImg = document.getElementById("cookie-banner-cancle-img");
        if (cancelImg) {
          cancelImg.onclick = function () {
            GDPR.bypassed_close();
            GDPR.logConsent("bypassed");
          };
        }
      });

      // hide banner.
      window.addEventListener("load", function () {
        for (var id = 0; id < gdpr_select_pages.length; id++) {
          var pageToHideBanner = gdpr_select_pages[id];
          if (document.body.classList.contains("page-id-" + pageToHideBanner)) {
            if (
              GDPR.settings.cookie_usage_for == "gdpr" ||
              GDPR.settings.cookie_usage_for == "eprivacy" ||
              GDPR.settings.cookie_usage_for == "both" ||
              GDPR.settings.cookie_usage_for == "lgpd"
            ) {
              var banner = document.getElementById(
                "gdpr-cookie-consent-show-again"
              );
              var insidebanner = document.getElementById(
                "gdpr-cookie-consent-bar"
              );
              if (GDPR.settings.cookie_bar_as == "popup") {
                $("#gdpr-popup").gdprmodal("hide");
              }
              if (banner || insidebanner) {
                banner.style.display = "none";
                insidebanner.style.display = "none";
              }
            } else if (GDPR.settings.cookie_usage_for == "ccpa") {
              if (GDPR.settings.cookie_bar_as == "popup") {
                $("#gdpr-popup").gdprmodal("hide");
              }
              var insidebanner = document.getElementById(
                "gdpr-cookie-consent-bar"
              );
              if (insidebanner) {
                insidebanner.style.display = "none";
              }
            }
          }
        }
      });
      // if DNT request is true then hide the banner and auto decline the consent

      if (gdpr_do_not_track == "true" && browser_dnt_value) {
        // hide the banner
        this.bar_elm.hide();
        // Decline the cookies
        GDPR.reject_close();

        var button_action = "reject";
        var new_window = false;
        new_window = GDPR.settings.button_decline_new_win ? true : false;
        gdpr_user_preference = JSON.parse(
          GDPR_Cookie.read("wpl_user_preference")
        );
        gdpr_viewed_cookie = GDPR_Cookie.read("wpl_viewed_cookie");
        if (GDPR.settings.cookie_usage_for == "gdpr") {
          event = new CustomEvent("GdprCookieConsentOnReject", {
            detail: {
              wpl_user_preference: gdpr_user_preference,
              wpl_viewed_cookie: gdpr_viewed_cookie,
            },
          });
          window.dispatchEvent(event);
        } else if (GDPR.settings.cookie_usage_for == "lgpd") {
          event = new CustomEvent("GdprCookieConsentOnReject", {
            detail: {
              wpl_user_preference: gdpr_user_preference,
              wpl_viewed_cookie: gdpr_viewed_cookie,
            },
          });
          window.dispatchEvent(event);
        } else if (GDPR.settings.cookie_usage_for == "eprivacy") {
          event = new CustomEvent("GdprCookieConsentOnReject", {
            detail: {
              wpl_viewed_cookie: gdpr_viewed_cookie,
            },
          });
          window.dispatchEvent(event);
        } else if (GDPR.settings.cookie_usage_for == "both") {
          GDPR.ccpa_cancel_close();
          var gdpr_optout_cookie = "";
          gdpr_optout_cookie = GDPR_Cookie.read("wpl_optout_cookie");
          event = new CustomEvent("GdprCookieConsentOnCancelOptout", {
            detail: {
              wpl_optout_cookie: gdpr_optout_cookie,
            },
          });
          window.dispatchEvent(event);
          $("#gdpr-cookie-consent-bar").addClass("hide_show_again_dnt");
        }
        // log the consent of user
        GDPR.logConsent(button_action);

        // hide the show again button
        $("#gdpr-cookie-consent-show-again").addClass("hide_show_again_dnt");
      }

      if (
        this.settings.cookie_usage_for == "gdpr" ||
        this.settings.cookie_usage_for == "eprivacy" ||
        this.settings.cookie_usage_for == "both" ||
        this.settings.cookie_usage_for == "lgpd"
      ) {
        if (this.settings.auto_scroll) {
          window.addEventListener("scroll", GDPR.acceptOnScroll, false);
        }
        if (this.settings.auto_click) {
          if (!GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME)) {
            var the_cookie_bar = document.querySelector(
              "#gdpr-cookie-consent-bar"
            );
            var setting_modal = document.querySelector(".gdprmodal-content");
            // Listen for click events on body
            document.body.addEventListener("click", function (event) {
              if (
                !the_cookie_bar.contains(event.target) &&
                (!setting_modal ||
                  (setting_modal && !setting_modal.contains(event.target)))
              ) {
                if (GDPR.settings.button_accept_all_is_on) {
                  GDPR.acceptAllCookies();
                }
                if (GDPR.settings.auto_scroll_reload == true) {
                  window.location.reload();
                }
                GDPR.accept_close();
                GDPR.logConsent("accept");
              }
            });
          }
        }

        var gdpr_user_preference = JSON.parse(
          GDPR_Cookie.read("wpl_user_preference")
        );
        var gdpr_viewed_cookie = GDPR_Cookie.read("wpl_viewed_cookie");
        var event = "";
        if (this.settings.cookie_usage_for == "gdpr") {
          event = new CustomEvent("GdprCookieConsentOnLoad", {
            detail: {
              wpl_user_preference: gdpr_user_preference,
              wpl_viewed_cookie: gdpr_viewed_cookie,
            },
          });
          window.dispatchEvent(event);
        } else if (this.settings.cookie_usage_for == "lgpd") {
          event = new CustomEvent("GdprCookieConsentOnLoad", {
            detail: {
              wpl_user_preference: gdpr_user_preference,
              wpl_viewed_cookie: gdpr_viewed_cookie,
            },
          });
          window.dispatchEvent(event);
        } else if (this.settings.cookie_usage_for == "eprivacy") {
          event = new CustomEvent("GdprCookieConsentOnLoad", {
            detail: {
              wpl_viewed_cookie: gdpr_viewed_cookie,
            },
          });
          window.dispatchEvent(event);
        }
      }
    },
    consent_renew_method: function () {
      const browser_consent_version = GDPR_Cookie.read("consent_version");
      var settings = JSON.parse(gdpr_cookiebar_settings);
      //check if version number doesnt exist or if current version number of visitor is less than the one stored in site database.
      if (
        (browser_consent_version == null &&
          GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME)) ||
        (browser_consent_version !== null &&
          Number(browser_consent_version) < Number(settings["consent_version"]))
      ) {
        var self = this;
        var hideBanner = false;

          if (gdpr_select_pages.length > 0) {
            for (var id = 0; id < gdpr_select_pages.length; id++) {
              var pageToHideBanner = gdpr_select_pages[id];
              if (
                document.body.classList.contains("page-id-" + pageToHideBanner)
              ) {
                hideBanner = true; // Mark that the banner should be hidden on this page

                if (
                  GDPR.settings.cookie_usage_for == "gdpr" ||
                  GDPR.settings.cookie_usage_for == "eprivacy" ||
                  GDPR.settings.cookie_usage_for == "both" ||
                  GDPR.settings.cookie_usage_for == "lgpd"
                ) {
                  var banner = document.getElementById(
                    "gdpr-cookie-consent-show-again"
                  );
                  var insidebanner = document.getElementById(
                    "gdpr-cookie-consent-bar"
                  );
                  if (GDPR.settings.cookie_bar_as == "popup") {
                    $("#gdpr-popup").gdprmodal("hide");
                  }
                  if (banner || insidebanner) {
                    banner.style.display = "none";
                    insidebanner.style.display = "none";
                  }
                } else if (GDPR.settings.cookie_usage_for == "ccpa") {
                  if (GDPR.settings.cookie_bar_as == "popup") {
                    $("#gdpr-popup").gdprmodal("hide");
                  }
                  var insidebanner = document.getElementById(
                    "gdpr-cookie-consent-bar"
                  );
                  if (insidebanner) {
                    insidebanner.style.display = "none";
                  }
                }
                break; // Exit the loop once we find a page that hides the banner
              }
            }
          }
          function userInteracted() {
            // Make the AJAX call
            jQuery.ajax({
              url: log_obj.ajax_url,
              type: "POST",
              data: {
                action: "gdpr_increase_ignore_rate",
                security: log_obj.consent_logging_nonce,
              },
              success: function (response) {},
            });

            // Remove the listeners after interaction
            document.removeEventListener("click", userInteracted);
            document.removeEventListener("scroll", userInteracted);
          }
        //display consent banner again
        if (this.settings.auto_banner_initialize && !hideBanner) {
            setTimeout(function () {
              self.bar_elm.show();
              jQuery.ajax({
                url: log_obj.ajax_url,
                type: "POST",
                data: {
                  action: "gdpr_increase_page_view",
                  security: log_obj.consent_logging_nonce,
                },
                success: function (response) {},
              });
              document.addEventListener("click", userInteracted);
              document.addEventListener("scroll", userInteracted);
            }, this.settings.auto_banner_initialize_delay);
          }

          if (!this.settings.auto_banner_initialize && !hideBanner) {
            self.bar_elm.show();
            jQuery.ajax({
              url: log_obj.ajax_url,
              type: "POST",
              data: {
                action: "gdpr_increase_page_view",
                security: log_obj.consent_logging_nonce,
              },
              success: function (response) {},
            });
            document.addEventListener("click", userInteracted);
            document.addEventListener("scroll", userInteracted);
          }
        jQuery.ajax({
          url: log_obj.ajax_url,
          type: "POST",
          data: {
            action: "gdpr_increase_page_view",
            security: log_obj.consent_logging_nonce,
          },
          success: function (response) {},
        });
        //delete cookies
        GDPR_Cookie.erase(GDPR_ACCEPT_COOKIE_NAME);
        GDPR_Cookie.erase(GDPR_CCPA_COOKIE_NAME);
        GDPR_Cookie.erase(US_PRIVACY_COOKIE_NAME);
        var banner = document.getElementById(
                "gdpr-cookie-consent-show-again"
              );
banner.style.display = "none";
      }
    },
    check_ccpa_eu: function (force_display_bar, force_display_show_again) {
      var data = {
        action: "show_cookie_consent_bar",
      };
      $.ajax({
        type: "post",
        url: log_obj.ajax_url,
        data: data,
        dataType: "json",
        success: function (response) {
          if (response.error) {
            // handle error here.
          } else {
            var geo_flag = true;
            var gdpr_flag = false;
            var ccpa_flag = false;
            var lgpd_flag = false;
            var cookieData = JSON.parse(gdpr_cookiebar_settings);
            var cookie_for = cookieData["cookie_usage_for"];
            // For the GDPR & CCPA
            if ("both" == cookie_for) {
              if (
                GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME) &&
                GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME)
              ) {
                if (response.eu_status != "on") {
                  $("#gdpr-cookie-consent-show-again").addClass(
                    "hide_show_again_dnt"
                  );
                }
                GDPR.hideHeader();
              } else if (
                GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME) &&
                !GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME)
              ) {
                if (response.eu_status != "on") {
                  $("#gdpr-cookie-consent-show-again").addClass(
                    "hide_show_again_dnt"
                  );
                }
                if (response.ccpa_status == "on") {
                  GDPR.displayHeader(
                    true,
                    false,
                    false,
                    force_display_bar,
                    true
                  );
                } else {
                  GDPR.displayHeader(true, true, true, force_display_bar, true);
                }
                //ab-testing-data-collection

                jQuery.ajax({
                  url: log_obj.ajax_url,
                  type: "POST",
                  data: {
                    action: "gdpr_collect_abtesting_data_action",
                    security: log_obj.consent_logging_nonce,
                    chosenBanner: Number(chosenBanner),
                    user_preference: "no choice",
                  },
                  success: function (response) {},
                });
              } else if (
                !GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME) &&
                GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME) &&
                response.eu_status == "on"
              ) {
                GDPR.displayHeader(
                  false,
                  true,
                  false,
                  force_display_bar,
                  force_display_show_again
                );
                if (GDPR.settings.auto_hide) {
                  var banner_delay = GDPR.settings.auto_banner_initialize
                    ? parseInt(GDPR.settings.auto_hide_delay) +
                      parseInt(GDPR.settings.auto_banner_initialize_delay)
                    : GDPR.settings.auto_hide_delay;
                  setTimeout(function () {
                    GDPR.accept_close();
                  }, banner_delay);
                }
                //ab-testing-data-collection

                jQuery.ajax({
                  url: log_obj.ajax_url,
                  type: "POST",
                  data: {
                    action: "gdpr_collect_abtesting_data_action",
                    security: log_obj.consent_logging_nonce,
                    chosenBanner: Number(chosenBanner),
                    user_preference: "no choice",
                  },
                  success: function (response) {},
                });
              } else if (
                !GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME) &&
                !GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME)
              ) {
                GDPR.checkEuAndCCPAStatus(response);
                //ab-testing-data-collection

                jQuery.ajax({
                  url: log_obj.ajax_url,
                  type: "POST",
                  data: {
                    action: "gdpr_collect_abtesting_data_action",
                    security: log_obj.consent_logging_nonce,
                    chosenBanner: Number(chosenBanner),
                    user_preference: "no choice",
                  },
                  success: function (response) {},
                });
              }
            } else if ("gdpr" == cookie_for) {
              if (!GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME)) {
                if (response.eu_status === "on") {
                  GDPR.displayHeader();
                } else {
                  $("#gdpr-cookie-consent-bar").addClass("hide_show_again_dnt");
                }

                //ab-testing-data-collection

                jQuery.ajax({
                  url: log_obj.ajax_url,
                  type: "POST",
                  data: {
                    action: "gdpr_collect_abtesting_data_action",
                    security: log_obj.consent_logging_nonce,
                    chosenBanner: Number(chosenBanner),
                    user_preference: "no choice",
                  },
                  success: function (response) {},
                });
                if (GDPR.settings.auto_hide) {
                  var banner_delay = GDPR.settings.auto_banner_initialize
                    ? parseInt(GDPR.settings.auto_hide_delay) +
                      parseInt(GDPR.settings.auto_banner_initialize_delay)
                    : GDPR.settings.auto_hide_delay;
                  setTimeout(function () {
                    GDPR.accept_close();
                    GDPR.logConsent("accept");
                  }, banner_delay);
                }
              } else {
                if (response.eu_status != "on") {
                  $("#gdpr-cookie-consent-show-again").addClass(
                    "hide_show_again_dnt"
                  );
                }
                GDPR.hideHeader();
              }
            } else if ("lgpd" == cookie_for) {
              if (!GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME)) {
                GDPR.displayHeader();
                //ab-testing-data-collection

                jQuery.ajax({
                  url: log_obj.ajax_url,
                  type: "POST",
                  data: {
                    action: "gdpr_collect_abtesting_data_action",
                    security: log_obj.consent_logging_nonce,
                    chosenBanner: Number(chosenBanner),
                    user_preference: "no choice",
                  },
                  success: function (response) {},
                });
                if (GDPR.settings.auto_hide) {
                  var banner_delay = GDPR.settings.auto_banner_initialize
                    ? parseInt(GDPR.settings.auto_hide_delay) +
                      parseInt(GDPR.settings.auto_banner_initialize_delay)
                    : GDPR.settings.auto_hide_delay;
                  setTimeout(function () {
                    GDPR.accept_close();
                    GDPR.logConsent("accept");
                  }, banner_delay);
                }
              } else {
                GDPR.hideHeader();
              }
            } else if ("ccpa" == cookie_for) {
              if (!GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME)) {
                if (response.ccpa_status === "on") {
                  GDPR.displayHeader();
                } else {
                  $("#gdpr-cookie-consent-bar").addClass("hide_show_again_dnt");
                }

                jQuery.ajax({
                  url: log_obj.ajax_url,
                  type: "POST",
                  data: {
                    action: "gdpr_collect_abtesting_data_action",
                    security: log_obj.consent_logging_nonce,
                    chosenBanner: Number(chosenBanner),
                    user_preference: "no choice",
                  },
                  success: function (response) {},
                });
              } else {
                GDPR.hideHeader();
              }
            } else if ("eprivacy" == cookie_for) {
              if (!GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME)) {
                GDPR.displayHeader();
                //ab-testing-data-collection

                jQuery.ajax({
                  url: log_obj.ajax_url,
                  type: "POST",
                  data: {
                    action: "gdpr_collect_abtesting_data_action",
                    security: log_obj.consent_logging_nonce,
                    chosenBanner: Number(chosenBanner),
                    user_preference: "no choice",
                  },
                  success: function (response) {},
                });
                if (GDPR.settings.auto_hide) {
                  var banner_delay = GDPR.settings.auto_banner_initialize
                    ? parseInt(GDPR.settings.auto_hide_delay) +
                      parseInt(GDPR.settings.auto_banner_initialize_delay)
                    : GDPR.settings.auto_hide_delay;
                  setTimeout(function () {
                    GDPR.accept_close();
                    GDPR.logConsent("accept");
                  }, banner_delay);
                }
              } else {
                GDPR.hideHeader();
              }
            }
            GDPR.consent_renew_method();
          }
        },
      });
    },
    checkEuAndCCPAStatus: function (response) {
      if (response.both_status == "off") {
        $("#gdpr-cookie-consent-bar").addClass("hide_show_again_dnt");
      }
      if (response.eu_status == "on" && response.ccpa_status == "off") {
        GDPR.displayHeader(false, true);
        if (GDPR.settings.auto_hide) {
          var banner_delay = GDPR.settings.auto_banner_initialize
            ? parseInt(GDPR.settings.auto_hide_delay) +
              parseInt(GDPR.settings.auto_banner_initialize_delay)
            : GDPR.settings.auto_hide_delay;
          setTimeout(function () {
            GDPR.accept_close();
          }, banner_delay);
        }
      } else if (response.eu_status == "off" && response.ccpa_status == "on") {
        GDPR.displayHeader(true, false);
      }
      if (response.eu_status == "on" && response.ccpa_status == "on") {
        GDPR.displayHeader(false, false);
        if (GDPR.settings.auto_hide) {
          var banner_delay = GDPR.settings.auto_banner_initialize
            ? parseInt(GDPR.settings.auto_hide_delay) +
              parseInt(GDPR.settings.auto_banner_initialize_delay)
            : GDPR.settings.auto_hide_delay;
          setTimeout(function () {
            GDPR.accept_close();
          }, banner_delay);
        }
      }
      if (response.eu_status == "off" && response.ccpa_status == "off") {
        GDPR.hideHeader(true);
        GDPR.displayHeader(false, false);
      }
    },
    attachEvents: function () {
      jQuery(".gdpr_action_button").click(function (e) {
        e.preventDefault();
        var event = "";
        var gdpr_user_preference = "";
        var gdpr_user_preference_val = "";
        var gdpr_viewed_cookie = "";
        var gdpr_optout_cookie = "";
        var elm = jQuery(this);
        var button_action = elm.attr("data-gdpr_action");
        var open_link =
          elm[0].hasAttribute("href") && elm.attr("href") != "#" ? true : false;
        var new_window = false;
        if (button_action == "accept") {
          var gdpr_user_preference_arr = {};
          var gdpr_user_preference_val = "";

          // Retrieve current user preferences from the cookie
          if (GDPR_Cookie.read("wpl_user_preference")) {
            gdpr_user_preference_arr = JSON.parse(
              GDPR_Cookie.read("wpl_user_preference")
            );
          }

          //variables to store consent for gcm
          var analytics_consent = false, marketing_consent = false, preferences_consent = false;

          // Loop through each input checkbox to update preferences
          jQuery(".gdpr_messagebar_detail input").each(function () {
            var key = jQuery(this).val();

            if (
              jQuery(this).is(":checked") &&
              (key == "analytics" ||
                key == "marketing" ||
                key == "unclassified" ||
                key == "preferences")
            ) {
              gdpr_user_preference_arr[key] = "yes";
              if (!GDPR.allowed_categories.includes(key)) {
                GDPR.allowed_categories.push(key);
              }
            } else if (
              key == "analytics" ||
              key == "marketing" ||
              key == "unclassified" ||
              key == "preferences"
            ) {
              gdpr_user_preference_arr[key] = "no";
              GDPR.allowed_categories = GDPR.allowed_categories.filter(
                function (category) {
                  return category !== key;
                }
              );
            }

            //getting data for gcm
            if(jQuery(this).is(":checked") && key == "analytics") analytics_consent = true;
            if(jQuery(this).is(":checked") && key == "marketing") marketing_consent = true;
            if(jQuery(this).is(":checked") && key == "preferences") preferences_consent = true;
          });
          if (typeof window.wp_set_consent === 'function') {
              if(marketing_consent) wp_set_consent('marketing', 'allow' ) ;
              else wp_set_consent('marketing', 'deny' ) ;
              if(analytics_consent) wp_set_consent('statistics', 'allow' ) ;
              else wp_set_consent('statistics', 'deny' ) ;
              if(analytics_consent) wp_set_consent('statistics-anonymous', 'allow' ) ;
              else wp_set_consent('statistics-anonymous', 'deny' ) ;
              if(preferences_consent) wp_set_consent('preferences', 'allow' ) ;
              else wp_set_consent('preferences', 'deny' ) ;
              wp_set_consent('functional', 'allow' );
          }
          
          if(is_gcm_on == 'true'){
            gtag('consent', 'update', {
              'ad_user_data': marketing_consent ? 'granted' : 'denied',
              'ad_personalization': marketing_consent ? 'granted' : 'denied',
              'ad_storage': marketing_consent ? 'granted' : 'denied',
              'analytics_storage': analytics_consent ? 'granted' : 'denied',
              'functionality_storage': preferences_consent ? 'granted' : 'denied',
              'personalization_storage': preferences_consent ? 'granted' : 'denied',
              'security_storage': 'granted'
            });
          }
          var consent = [];
          consent.marketing = marketing_consent == true ? 'yes' : 'no';
          consent.analytics = analytics_consent == true ? 'yes' : 'no';
          consent.preferences = preferences_consent == true ? 'yes' : 'no';
          consentListeners.forEach(function (callback) {
            callback(consent);
          });
          
          if(is_gcm_debug_on == 'true'){debugUpdateConsentState();}

          // Update the user preference cookie
          gdpr_user_preference_val = JSON.stringify(gdpr_user_preference_arr);
          GDPR_Cookie.set(
            "wpl_user_preference",
            gdpr_user_preference_val,
            GDPR_ACCEPT_COOKIE_EXPIRE
          );

          var gdpr_viewed_cookie = GDPR_Cookie.read("wpl_viewed_cookie");

          if (!GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME)) {
            // Log A/B testing data
            jQuery.ajax({
              url: log_obj.ajax_url,
              type: "POST",
              data: {
                action: "gdpr_collect_abtesting_data_action",
                security: log_obj.consent_logging_nonce,
                chosenBanner: Number(chosenBanner),
                user_preference: gdpr_user_preference_arr,
              },
              success: function (response) {},
            });
          }

          // Trigger accept-close logic
          GDPR.accept_close();
          jQuery.ajax({
              url: log_obj.ajax_url,
              type: "POST",
              data: {
                action: "gdpr_fire_scripts",
                security: log_obj.consent_logging_nonce,
              },
              success: function (response) {
                function executeScript(scriptContent) {
                    var script = document.createElement("script");
                    script.textContent = scriptContent;
                    document.head.appendChild(script);
                }
                function executeBodyScript(scriptContent) {
                    var script = document.createElement("script");
                    script.textContent = scriptContent;
                    document.body.appendChild(script);
                }
                if (response.data.header_scripts) {
                    var tempDiv = document.createElement("div");
                    tempDiv.innerHTML = response.data.header_scripts;
                    tempDiv.querySelectorAll("script").forEach(function (oldScript) {
                        executeScript(oldScript.innerHTML);
                    });
                }

                // Inject Body Scripts
                if (response.data.body_scripts) {
                    var tempDiv = document.createElement("div");
                    tempDiv.innerHTML = response.data.body_scripts;
                    tempDiv.querySelectorAll("script").forEach(function (oldScript) {
                        executeBodyScript(oldScript.innerHTML);
                    });
                }
              },
          });
          // Dispatch appropriate events based on settings
          var event;
          if (GDPR.settings.cookie_usage_for == "gdpr") {
            GDPR_Cookie.set(
              "wpl_user_preference",
              gdpr_user_preference_val,
              GDPR_ACCEPT_COOKIE_EXPIRE
            );
            event = new CustomEvent("GdprCookieConsentOnAccept", {
              detail: {
                wpl_user_preference: gdpr_user_preference_arr,
                wpl_viewed_cookie: gdpr_viewed_cookie,
              },
            });
          } else if (GDPR.settings.cookie_usage_for == "lgpd") {
            GDPR_Cookie.set(
              "wpl_user_preference",
              gdpr_user_preference_val,
              GDPR_ACCEPT_COOKIE_EXPIRE
            );
            event = new CustomEvent("GdprCookieConsentOnAccept", {
              detail: {
                wpl_user_preference: gdpr_user_preference_arr,
                wpl_viewed_cookie: gdpr_viewed_cookie,
              },
            });
          } else if (GDPR.settings.cookie_usage_for == "eprivacy") {
            event = new CustomEvent("GdprCookieConsentOnAccept", {
              detail: {
                wpl_viewed_cookie: gdpr_viewed_cookie,
              },
            });
          }

          if(event){
            window.dispatchEvent(event);
          }

          // Log consent action
          GDPR.logConsent(button_action);
        } else if (button_action == "accept_all") {
          if (typeof window.wp_set_consent === 'function') {
            wp_set_consent('marketing', 'allow' ) ;
            wp_set_consent('statistics', 'allow' ) ;
            wp_set_consent('statistics-anonymous', 'allow' ) ;
            wp_set_consent('preferences', 'allow' ) ;
            wp_set_consent('functional', 'allow' );
          }

          if(is_gcm_on == 'true'){
            gtag('consent', 'update', {
              'ad_user_data': 'granted',
              'ad_personalization': 'granted',
              'ad_storage': 'granted',
              'analytics_storage': 'granted',
              'functionality_storage': 'granted',
              'personalization_storage': 'granted',
              'security_storage': 'granted'
            });
          }
          var consent = [];
          consent.marketing = 'yes';
          consent.analytics = 'yes';
          consent.preferences = 'yes';
          consentListeners.forEach(function (callback) {
            callback(consent);
          });

          if(is_gcm_debug_on == 'true'){debugUpdateConsentState();}

          var cookie_data = {
            necessary: "yes",
            marketing: "yes",
            analytics: "yes",
            preferences: "yes",
            unclassified: "yes",
          };
          if (!GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME)) {
            //ab-testing-data-collection
            jQuery.ajax({
              url: log_obj.ajax_url,
              type: "POST",
              data: {
                action: "gdpr_collect_abtesting_data_action",
                security: log_obj.consent_logging_nonce,
                chosenBanner: Number(chosenBanner),
                user_preference: cookie_data,
              },
              success: function (response) {},
            });
          }
          GDPR.accept_close();
          GDPR.acceptAllCookies();
          jQuery.ajax({
              url: log_obj.ajax_url,
              type: "POST",
              data: {
                action: "gdpr_fire_scripts",
                security: log_obj.consent_logging_nonce,
              },
              success: function (response) {
                function executeScript(scriptContent) {
                    var script = document.createElement("script");
                    script.textContent = scriptContent;
                    document.head.appendChild(script);
                }
                function executeBodyScript(scriptContent) {
                    var script = document.createElement("script");
                    script.textContent = scriptContent;
                    document.body.appendChild(script);
                }
                if (response.data.header_scripts) {
                    var tempDiv = document.createElement("div");
                    tempDiv.innerHTML = response.data.header_scripts;
                    tempDiv.querySelectorAll("script").forEach(function (oldScript) {
                        executeScript(oldScript.innerHTML);
                    });
                }

                // Inject Body Scripts
                if (response.data.body_scripts) {
                    var tempDiv = document.createElement("div");
                    tempDiv.innerHTML = response.data.body_scripts;
                    tempDiv.querySelectorAll("script").forEach(function (oldScript) {
                        executeBodyScript(oldScript.innerHTML);
                    });
                }
              },
          });
          new_window = GDPR.settings.button_accept_all_new_win ? true : false;
          gdpr_viewed_cookie = GDPR_Cookie.read("wpl_viewed_cookie");

          if (GDPR.settings.cookie_usage_for == "gdpr") {
            event = new CustomEvent("GdprCookieConsentOnAcceptAll", {
              detail: {
                wpl_user_preference: gdpr_user_preference_arr,
                wpl_viewed_cookie: gdpr_viewed_cookie,
              },
            });
            window.dispatchEvent(event);
          } else if (GDPR.settings.cookie_usage_for == "eprivacy") {
            event = new CustomEvent("GdprCookieConsentOnAcceptAll", {
              detail: {
                wpl_viewed_cookie: gdpr_viewed_cookie,
              },
            });
            window.dispatchEvent(event);
          }
          GDPR.logConsent(button_action);
        } else if (button_action == "reject") {
          if (!GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME)) {
            //ab-testing-data-collection
            jQuery.ajax({
              url: log_obj.ajax_url,
              type: "POST",
              data: {
                action: "gdpr_collect_abtesting_data_action",
                security: log_obj.consent_logging_nonce,
                chosenBanner: Number(chosenBanner),
                user_preference: "reject",
              },
              success: function (response) {},
            });
          }
          if (typeof window.wp_set_consent === 'function') {
            wp_set_consent('functional', 'allow' );
            wp_set_consent('marketing', 'deny' ) ;
            wp_set_consent('statistics', 'deny' ) ;
            wp_set_consent('statistics-anonymous', 'deny' ) ;
            wp_set_consent('preferences', 'deny' ) ;
          }
          if(is_gcm_on == 'true'){
              gtag('consent', 'update', {
                'ad_user_data': 'denied',
                'ad_personalization': 'denied',
                'ad_storage': 'denied',
                'analytics_storage': 'denied',
                'functionality_storage': 'denied',
                'personalization_storage': 'denied',
                'security_storage': 'granted'
              });
            }
            var consent = [];
            consent.marketing = 'no';
            consent.analytics = 'no';
            consent.preferences = 'no';
            consentListeners.forEach(function (callback) {
              callback(consent);
            });

            if(is_gcm_debug_on == 'true'){debugUpdateConsentState();}


          GDPR.reject_close();
          new_window = GDPR.settings.button_decline_new_win ? true : false;
          gdpr_user_preference = JSON.parse(
            GDPR_Cookie.read("wpl_user_preference")
          );
          gdpr_viewed_cookie = GDPR_Cookie.read("wpl_viewed_cookie");

          if (GDPR.settings.cookie_usage_for == "gdpr") {
            event = new CustomEvent("GdprCookieConsentOnReject", {
              detail: {
                wpl_user_preference: gdpr_user_preference,
                wpl_viewed_cookie: gdpr_viewed_cookie,
              },
            });
            window.dispatchEvent(event);
          } else if (GDPR.settings.cookie_usage_for == "eprivacy") {
            event = new CustomEvent("GdprCookieConsentOnReject", {
              detail: {
                wpl_viewed_cookie: gdpr_viewed_cookie,
              },
            });
            window.dispatchEvent(event);
          }

          GDPR.logConsent(button_action);
        } else if (button_action == "settings") {
          GDPR.bar_elm.slideUp(GDPR.settings.animate_speed_hide);
          if (GDPR.settings.cookie_bar_as == "popup") {
            $("#gdpr-popup").gdprmodal("hide");
          }
          GDPR.show_again_elm.slideUp(GDPR.settings.animate_speed_hide);
        } else if (button_action == "close") {
          GDPR.displayHeader();
          if (
            GDPR.settings.cookie_bar_as === "popup" &&
            GDPR.settings.notify_animate_show !== false
          ) {
            $("#gdpr-cookie-consent-bar").css("display", "none");
            $("#gdpr-cookie-consent-bar").slideDown(500);
          }
        } else if (button_action == "show_settings") {
          GDPR.show_details();
        } else if (button_action == "hide_settings") {
          GDPR.hide_details();
        } else if (button_action == "donotsell") {
          if (
            GDPR.settings.cookie_usage_for == "ccpa" ||
            jQuery(GDPR.settings.notify_div_id).find("p.gdpr").css("display") ==
              "none"
          ) {
            GDPR.hideHeader(true);
          } else {
            GDPR.hideHeader();
          }
          $("#gdpr-ccpa-gdprmodal").gdprmodal("show");
        } else if (button_action == "ccpa_close") {
          GDPR.displayHeader();
        } else if (button_action == "cancel") {
          var cookie_data = {
            necessary: "yes",
            marketing: "yes",
            analytics: "yes",
            preferences: "yes",
            unclassified: "yes",
          };
          if (!GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME)) {
            //ab-testing-data-collection
            jQuery.ajax({
              url: log_obj.ajax_url,
              type: "POST",
              data: {
                action: "gdpr_collect_abtesting_data_action",
                security: log_obj.consent_logging_nonce,
                chosenBanner: Number(chosenBanner),
                user_preference: cookie_data,
              },
              success: function (response) {},
            });
          }
          GDPR.ccpa_cancel_close();
          gdpr_optout_cookie = GDPR_Cookie.read("wpl_optout_cookie");

          event = new CustomEvent("GdprCookieConsentOnCancelOptout", {
            detail: {
              wpl_optout_cookie: gdpr_optout_cookie,
            },
          });
          window.dispatchEvent(event);
          GDPR.logConsent(button_action);
        } else if (button_action == "confirm") {
          if (!GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME)) {
            //ab-testing-data-collection
            jQuery.ajax({
              url: log_obj.ajax_url,
              type: "POST",
              data: {
                action: "gdpr_collect_abtesting_data_action",
                security: log_obj.consent_logging_nonce,
                chosenBanner: Number(chosenBanner),
                user_preference: "reject",
              },
              success: function (response) {},
            });
          }
          GDPR.confirm_close();

          gdpr_optout_cookie = GDPR_Cookie.read("wpl_optout_cookie");

          event = new CustomEvent("GdprCookieConsentOnOptout", {
            detail: {
              wpl_optout_cookie: gdpr_optout_cookie,
            },
          });
          window.dispatchEvent(event);
          GDPR.logConsent(button_action);
        }
        if (open_link) {
          if (elm.attr("target") === "_blank" || new_window) {
            window.open(elm.attr("href"), "_blank");
          } else {
            window.location.href = elm.attr("href");
          }
        }
      });
      jQuery("#vendor-link").click(function (e) {
        e.preventDefault();
        GDPR.bar_elm.slideUp(GDPR.settings.animate_speed_hide);
        if (GDPR.settings.cookie_bar_as == "popup") {
          $("#gdpr-popup").gdprmodal("hide");
        }
        GDPR.show_again_elm.slideUp(GDPR.settings.animate_speed_hide);
        $(
          ".gdpr_messagebar_detail .gdpr-iab-navbar .gdpr-iab-navbar-button.active"
        ).css("color", GDPR.settings.button_accept_button_color);
        $(".gdpr-iab-navbar-button").removeClass("active");
        $(".gdpr-iab-navbar-button").css("color", "inherit");
        $(".tabContainer").css("display", "none");
        $(".vendor-group").css("display", "block");

        $("#gdprIABTabVendors .gdpr-iab-navbar-button").css("border", "none");
        $("#gdprIABTabVendors .gdpr-iab-navbar-button").css(
          "color",
          GDPR.settings.button_accept_button_color
        );
        $("#gdprIABTabVendors .gdpr-iab-navbar-button").addClass("active");
        // switch (this.id) {
        //   case "gdprIABTabCategory":
        //     $(".cat").css("display", "block");
        //     break;
        //   case "gdprIABTabFeatures":
        //     $(".feature-group").css("display", "block");
        //     break;
        //   case "gdprIABTabVendors":
        //     $(".vendor-group").css("display", "block");
        //     break;
        // }
      });
      jQuery(".gdpr_messagebar_detail input").each(function () {
        var key = jQuery(this).val();
        var gdpr_user_preference_arr = {};
        var gdpr_user_preference_val = "";
        if (GDPR_Cookie.read("wpl_user_preference")) {
          gdpr_user_preference_arr = JSON.parse(
            GDPR_Cookie.read("wpl_user_preference")
          );
        }
        if (
          key == "necessary" ||
          (jQuery(this).is(":checked") &&
            (key == "analytics" ||
              key == "marketing" ||
              key == "unclassified" ||
              key == "preferences"))
        ) {
          gdpr_user_preference_arr[key] = "yes";
          GDPR.allowed_categories.push(key);
        } else if (
          key == "analytics" ||
          key == "marketing" ||
          key == "unclassified" ||
          key == "preferences"
        ) {
          gdpr_user_preference_arr[key] = "no";
          var length = GDPR.allowed_categories.length;
          for (var i = 0; i < length; i++) {
            if (GDPR.allowed_categories[i] == key) {
              GDPR.allowed_categories.splice(i, 1);
            }
          }
        }
        gdpr_user_preference_val = JSON.stringify(gdpr_user_preference_arr);
        GDPR_Cookie.set(
          "wpl_user_preference",
          gdpr_user_preference_val,
          GDPR_ACCEPT_COOKIE_EXPIRE
        );
      });
      jQuery(document).on(
        "click",
        "#gdpr-cookie-consent-show-again",
        function (e) {
          e.preventDefault();
          multiple_legislation_current_banner = "gdpr";
          if (
            GDPR.settings.cookie_usage_for == "both" &&
            multiple_legislation_current_banner == "gdpr"
          ) {
            GDPR.bar_elm.hide();
            GDPR.show_again_elm.hide();
          }
          if (GDPR.settings.cookie_usage_for == "both"){
              jQuery(GDPR.settings.notify_div_id).css("background", GDPR.convertToHex(GDPR.settings.multiple_legislation_cookie_bar_color1, GDPR.settings.multiple_legislation_cookie_bar_opacity1));
              jQuery(GDPR.settings.notify_div_id).css("color", GDPR.settings.multiple_legislation_cookie_text_color1);
              jQuery(GDPR.settings.notify_div_id).css("border-style", GDPR.settings.multiple_legislation_border_style1);
              jQuery(GDPR.settings.notify_div_id).css("border-color", GDPR.settings.multiple_legislation_cookie_border_color1);
              jQuery(GDPR.settings.notify_div_id).css("border-width", GDPR.settings.multiple_legislation_cookie_bar_border_width1);
              jQuery(GDPR.settings.notify_div_id).css("border-radius", GDPR.settings.multiple_legislation_cookie_bar_border_radius1);
              jQuery(GDPR.settings.notify_div_id).css("font-family", GDPR.settings.multiple_legislation_cookie_font1);
            }
          
          jQuery(GDPR.settings.notify_div_id).find("p.gdpr").show();
          jQuery(GDPR.settings.notify_div_id).find("h3.gdpr_heading").show();
          jQuery(GDPR.settings.notify_div_id)
            .find(".gdpr.group-description-buttons")
            .show();
          GDPR.displayHeader(false, false, false, true, false, true);
          if (
            GDPR.settings.cookie_bar_as === "popup" &&
            GDPR.settings.notify_animate_show !== false
          ) {
            $("#gdpr-cookie-consent-bar").css("display", "none");
            $("#gdpr-cookie-consent-bar").slideDown(500);
          }
          $(this).hide();
        }
      );

      jQuery(document).on(
        "click",
        "#gdpr_messagebar_detail_body_content_tabs_overview",
        function (e) {
          e.preventDefault();
          var elm = jQuery(this);
          jQuery("#gdpr_messagebar_detail_body_content_tabs")
            .find("a")
            .removeClass(
              "gdpr_messagebar_detail_body_content_tab_item_selected"
            );
          if (
            gdpr_ab_options.ab_testing_enabled === "false" ||
            gdpr_ab_options.ab_testing_enabled === false
          ) {
            elm.addClass(
              "gdpr_messagebar_detail_body_content_tab_item_selected"
            );
          } else {
            if (Number(chosenBanner) === 1) {
              elm.addClass(
                "gdpr_messagebar_detail_body_content_tab_item_selected"
              );
            } else {
              elm.addClass(
                "gdpr_messagebar_detail_body_content_tab_item_selected"
              );
            }
          }

          jQuery("#gdpr_messagebar_detail_body_content_about").hide();
          jQuery("#gdpr_messagebar_detail_body_content_overview").show();
        }
      );
      jQuery(document).on(
        "click",
        "#gdpr_messagebar_detail_body_content_tabs_about",
        function (e) {
          e.preventDefault();
          var elm = jQuery(this);
          jQuery("#gdpr_messagebar_detail_body_content_tabs")
            .find("a")
            .removeClass(
              "gdpr_messagebar_detail_body_content_tab_item_selected"
            );
          if (
            gdpr_ab_options.ab_testing_enabled === "false" ||
            gdpr_ab_options.ab_testing_enabled === false
          ) {
            elm.addClass(
              "gdpr_messagebar_detail_body_content_tab_item_selected"
            );
          } else {
            if (Number(chosenBanner) === 1) {
              elm.addClass(
                "gdpr_messagebar_detail_body_content_tab_item_selected"
              );
            } else {
              elm.addClass(
                "gdpr_messagebar_detail_body_content_tab_item_selected"
              );
            }
          }

          jQuery("#gdpr_messagebar_detail_body_content_overview").hide();
          jQuery("#gdpr_messagebar_detail_body_content_about").show();
        }
      );
      jQuery(document).on(
        "click",
        "#gdpr_messagebar_detail_body_content_overview_cookie_container_types a",
        function (e) {
          e.preventDefault();
          var elm = jQuery(this);
          var prnt = elm.parent();
          if (
            gdpr_ab_options.ab_testing_enabled === "false" ||
            gdpr_ab_options.ab_testing_enabled === false
          ) {
            prnt
              .find("a")
              .removeClass(
                "gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
              );
            elm.addClass(
              "gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
            );
          } else {
            if (Number(chosenBanner) === 1) {
              prnt
                .find("a")
                .removeClass(
                  "gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
                );
              elm.addClass(
                "gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
              );
            } else {
              prnt
                .find("a")
                .removeClass(
                  "gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
                );
            }
          }

          var trgt = jQuery(this).attr("data-target");
          var cntr = prnt.siblings(
            "#gdpr_messagebar_detail_body_content_overview_cookie_container_type_details"
          );
          cntr
            .find(".gdpr_messagebar_detail_body_content_cookie_type_details")
            .hide();
          cntr.find("#" + trgt + "").show();
        }
      );
    },

    configButtons: function () {
      var template = this.settings.template;
      if (
        gdpr_ab_options.ab_testing_enabled === "false" ||
        gdpr_ab_options.ab_testing_enabled === false
      ) {
        if (
          this.settings.cookie_usage_for == "both" &&
          multiple_legislation_current_banner == "gdpr"
        ) {
          
        } else if (
          this.settings.cookie_usage_for == "both" &&
          multiple_legislation_current_banner == "ccpa"
        ) {
                 
        } else {
         
        }
      } else {
        if (Number(chosenBanner) === 1) {
          
          
        } else {
          
        }
      }
    },
    convertToHex: function (hex, opacity) {
      hex = hex.replace("#", "");
      var r = parseInt(hex.substring(0, 2), 16);
      var g = parseInt(hex.substring(2, 4), 16);
      var b = parseInt(hex.substring(4, 6), 16);
      var result = "rgba(" + r + "," + g + "," + b + "," + opacity + ")";
      return result;
    },

    toggleBar: function (force_display_bar, force_display_show_again) {
      if (this.settings.cookie_usage_for == "gdpr") {
        if (!GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME)) {
          //ab-testing-data-collection

          jQuery.ajax({
            url: log_obj.ajax_url,
            type: "POST",
            data: {
              action: "gdpr_collect_abtesting_data_action",
              security: log_obj.consent_logging_nonce,
              chosenBanner: Number(chosenBanner),
              user_preference: "no choice",
            },
            success: function (response) {},
          });
          this.displayHeader();
          if (this.settings.auto_hide) {
            var banner_delay = this.settings.auto_banner_initialize
              ? parseInt(this.settings.auto_hide_delay) +
                parseInt(this.settings.auto_banner_initialize_delay)
              : this.settings.auto_hide_delay;
            setTimeout(function () {
              GDPR.accept_close();
              GDPR.logConsent("accept");
            }, banner_delay);
          }
        } else {
          this.hideHeader();
        }
      } else if (this.settings.cookie_usage_for == "eprivacy") {
        if (!GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME)) {
          //ab-testing-data-collection

          jQuery.ajax({
            url: log_obj.ajax_url,
            type: "POST",
            data: {
              action: "gdpr_collect_abtesting_data_action",
              security: log_obj.consent_logging_nonce,
              chosenBanner: Number(chosenBanner),
              user_preference: "no choice",
            },
            success: function (response) {},
          });
          this.displayHeader();
          if (this.settings.auto_hide) {
            var banner_delay = this.settings.auto_banner_initialize
              ? parseInt(this.settings.auto_hide_delay) +
                parseInt(this.settings.auto_banner_initialize_delay)
              : this.settings.auto_hide_delay;
            setTimeout(function () {
              GDPR.accept_close();
              GDPR.logConsent("accept");
            }, banner_delay);
          }
        } else {
          this.hideHeader();
        }
      } else if (this.settings.cookie_usage_for == "ccpa") {
        if (!GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME)) {
          //ab-testing-data-collection

          jQuery.ajax({
            url: log_obj.ajax_url,
            type: "POST",
            data: {
              action: "gdpr_collect_abtesting_data_action",
              security: log_obj.consent_logging_nonce,
              chosenBanner: Number(chosenBanner),
              user_preference: "no choice",
            },
            success: function (response) {},
          });
          this.displayHeader();
        } else {
          this.hideHeader();
        }
      } else if (this.settings.cookie_usage_for == "both") {
        if (
          GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME) &&
          GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME)
        ) {
          this.hideHeader();
        } else if (
          GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME) &&
          !GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME)
        ) {
          //ab-testing-data-collection

          jQuery.ajax({
            url: log_obj.ajax_url,
            type: "POST",
            data: {
              action: "gdpr_collect_abtesting_data_action",
              security: log_obj.consent_logging_nonce,
              chosenBanner: Number(chosenBanner),
              user_preference: "no choice",
            },
            success: function (response) {},
          });
          this.displayHeader(true, false, force_display_bar, true);
        } else if (
          !GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME) &&
          GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME)
        ) {
          //ab-testing-data-collection

          jQuery.ajax({
            url: log_obj.ajax_url,
            type: "POST",
            data: {
              action: "gdpr_collect_abtesting_data_action",
              security: log_obj.consent_logging_nonce,
              chosenBanner: Number(chosenBanner),
              user_preference: "no choice",
            },
            success: function (response) {},
          });
          this.displayHeader(
            false,
            true,
            force_display_bar,
            force_display_show_again
          );
          if (this.settings.auto_hide) {
            var banner_delay = this.settings.auto_banner_initialize
              ? parseInt(this.settings.auto_hide_delay) +
                parseInt(this.settings.auto_banner_initialize_delay)
              : this.settings.auto_hide_delay;
            setTimeout(function () {
              GDPR.accept_close();
              GDPR.logConsent("accept");
            }, banner_delay);
          }
        } else {
          //ab-testing-data-collection

          jQuery.ajax({
            url: log_obj.ajax_url,
            type: "POST",
            data: {
              action: "gdpr_collect_abtesting_data_action",
              security: log_obj.consent_logging_nonce,
              chosenBanner: Number(chosenBanner),
              user_preference: "no choice",
            },
            success: function (response) {},
          });
          this.displayHeader(
            false,
            false,
            force_display_bar,
            force_display_show_again
          );
          if (this.settings.auto_hide) {
            var banner_delay = this.settings.auto_banner_initialize
              ? parseInt(this.settings.auto_hide_delay) +
                parseInt(this.settings.auto_banner_initialize_delay)
              : this.settings.auto_hide_delay;
            setTimeout(function () {
              GDPR.accept_close();
              GDPR.logConsent("accept");
            }, banner_delay);
          }
        }
        if (
          !GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME) ||
          !GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME)
        ) {
        } else {
          this.hideHeader();
        }
      } else if (this.settings.cookie_usage_for == "lgpd") {
        if (!GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME)) {
          //ab-testing-data-collection

          jQuery.ajax({
            url: log_obj.ajax_url,
            type: "POST",
            data: {
              action: "gdpr_collect_abtesting_data_action",
              security: log_obj.consent_logging_nonce,
              chosenBanner: Number(chosenBanner),
              user_preference: "no choice",
            },
            success: function (response) {},
          });
          this.displayHeader();
          if (this.settings.auto_hide) {
            var banner_delay = this.settings.auto_banner_initialize
              ? parseInt(this.settings.auto_hide_delay) +
                parseInt(this.settings.auto_banner_initialize_delay)
              : this.settings.auto_hide_delay;
            setTimeout(function () {
              GDPR.accept_close();
              GDPR.logConsent("accept");
            }, banner_delay);
          }
        } else {
          this.hideHeader();
        }
      }
    },

    ccpa_cancel_close: function () {
      GDPR_Cookie.set(GDPR_CCPA_COOKIE_NAME, "no", GDPR_CCPA_COOKIE_EXPIRE);
      GDPR_Cookie.set(
        "consent_version",
        this.settings.consent_version,
        GDPR_ACCEPT_COOKIE_EXPIRE
      );
      if (this.settings.is_ccpa_iab_on) {
        GDPR_Cookie.set(
          US_PRIVACY_COOKIE_NAME,
          "1YNY",
          GDPR_CCPA_COOKIE_EXPIRE
        );
      }
      if (this.settings.notify_animate_hide) {
        this.bar_elm.slideUp(this.settings.animate_speed_hide);
      }
      if (this.settings.cookie_bar_as == "popup") {
        $("#gdpr-popup").gdprmodal("hide");
      }
      if (this.settings.accept_reload == true) {
        window.location.reload(true);
      } else {
        if (this.settings.cookie_usage_for == "both") {
          this.check_ccpa_eu();
        }
      }
      return false;
    },

    confirm_close: function () {
      GDPR_Cookie.set(GDPR_CCPA_COOKIE_NAME, "yes", GDPR_CCPA_COOKIE_EXPIRE);
      GDPR_Cookie.set(
        "consent_version",
        this.settings.consent_version,
        GDPR_ACCEPT_COOKIE_EXPIRE
      );
      if (this.settings.is_ccpa_iab_on) {
        GDPR_Cookie.set(
          US_PRIVACY_COOKIE_NAME,
          "1YYY",
          GDPR_CCPA_COOKIE_EXPIRE
        );
      }
      if (this.settings.notify_animate_hide) {
        this.bar_elm.slideUp(this.settings.animate_speed_hide);
      }
      if (this.settings.cookie_bar_as == "popup") {
        $("#gdpr-popup").gdprmodal("hide");
      }
      if (this.settings.accept_reload == true) {
        window.location.reload(true);
      } else {
        if (this.settings.cookie_usage_for == "both") {
          this.check_ccpa_eu();
        }
      }
      return false;
    },

    accept_close: function () {
      GDPR_Cookie.set(
        GDPR_ACCEPT_COOKIE_NAME,
        "yes",
        GDPR_ACCEPT_COOKIE_EXPIRE
      );
      GDPR_Cookie.set(
        "consent_version",
        this.settings.consent_version,
        GDPR_ACCEPT_COOKIE_EXPIRE
      );

      var cookie_pref =
        '{"necessary":"yes","marketing":"yes","analytics":"yes","preferences":"yes","unclassified":"yes"}';
      var gdpr_user_preference = JSON.parse(cookie_pref);
      var gdpr_user_preference_val = JSON.stringify(gdpr_user_preference);

      if (this.settings.notify_animate_hide) {
        this.bar_elm.slideUp(
          this.settings.animate_speed_hide,
          GDPR_Blocker.runScripts
        );
      } else {
        this.bar_elm.hide(GDPR_Blocker.runScripts);
      }
      if (this.settings.cookie_bar_as == "popup") {
        $("#gdpr-popup").gdprmodal("hide");
      }
      this.show_again_elm.slideDown(this.settings.animate_speed_hide);
      if (this.settings.accept_reload == true) {
        // GDPR.logConsent("accept");
        setTimeout(function () {
          window.location.reload();
        }, 1100);
      } else {
        if (this.settings.cookie_usage_for == "both") {
          this.check_ccpa_eu(true, true);
        }
      }
      return false;
    },

    reject_close: function () {
      GDPR.disableAllCookies();
      GDPR_Cookie.set(GDPR_ACCEPT_COOKIE_NAME, "no", GDPR_ACCEPT_COOKIE_EXPIRE);
      GDPR_Cookie.set(
        "consent_version",
        this.settings.consent_version,
        GDPR_ACCEPT_COOKIE_EXPIRE
      );
      if (this.settings.notify_animate_hide) {
        this.bar_elm.slideUp(
          this.settings.animate_speed_hide,
          GDPR_Blocker.runScripts
        );
      } else {
        this.bar_elm.hide(GDPR_Blocker.runScripts);
      }
      if (this.settings.cookie_bar_as == "popup") {
        $("#gdpr-popup").gdprmodal("hide");
      }
      this.show_again_elm.slideDown(this.settings.animate_speed_hide);
      if (
        (this.settings.decline_reload == true && !browser_dnt_value) ||
        (this.settings.decline_reload == true && gdpr_do_not_track == "false")
      ) {
        setTimeout(function () {
          window.location.reload();
        }, 1100);
      } else {
        if (this.settings.cookie_usage_for == "both") {
          this.check_ccpa_eu(true, true);
        }
      }
      return false;
    },

    bypassed_close: function () {
      GDPR.disableAllCookies();
      if (
        GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME) &&
        !GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME)
      ) {
        GDPR_Cookie.set(
          GDPR_CCPA_COOKIE_NAME,
          "unset",
          GDPR_ACCEPT_COOKIE_EXPIRE
        );

        // this.check_ccpa_eu(true, true);
      }
      GDPR_Cookie.set(
        GDPR_ACCEPT_COOKIE_NAME,
        "unset",
        GDPR_ACCEPT_COOKIE_EXPIRE
      );
      GDPR_Cookie.set(
        "consent_version",
        this.settings.consent_version,
        GDPR_ACCEPT_COOKIE_EXPIRE
      );
      jQuery.ajax({
        url: log_obj.ajax_url,
        type: "POST",
        data: {
          action: "gdpr_collect_abtesting_data_action",
          security: log_obj.consent_logging_nonce,
          chosenBanner: Number(chosenBanner),
          user_preference: "bypass",
        },
        success: function (response) {},
      });
      if (this.settings.notify_animate_hide) {
        this.bar_elm.slideUp(
          this.settings.animate_speed_hide,
          GDPR_Blocker.runScripts
        );
      } else {
        this.bar_elm.hide(GDPR_Blocker.runScripts);
      }
      if (this.settings.cookie_bar_as == "popup") {
        $("#gdpr-popup").gdprmodal("hide");
      }
      this.show_again_elm.slideDown(this.settings.animate_speed_hide);
      if (
        (this.settings.decline_reload == true && !browser_dnt_value) ||
        (this.settings.decline_reload == true && gdpr_do_not_track == "false")
      ) {
        setTimeout(function () {
          window.location.reload();
        }, 1100);
      } else {
        if (this.settings.cookie_usage_for == "both") {
          if (GDPR.settings.cookie_bar_as == "popup") {
            $("#gdpr-popup").gdprmodal("hide");
          }
          var insidebanner = document.getElementById("gdpr-cookie-consent-bar");
          if (insidebanner) {
            insidebanner.style.display = "none";
          }
          if (
            GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME) &&
            !GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME)
          ) {
            this.check_ccpa_eu(true, true);
          }
        }
      }
      return false;
    },

    logConsent: function (btn_action) {
      var self = this;
      setTimeout(function () {
        if (!consent_forwarding && self.settings.logging_on) {
          jQuery.ajax({
            url: log_obj.ajax_url,
            type: "POST",
            data: {
              action: "gdpr_log_consent_action",
              security: log_obj.consent_logging_nonce,
              gdpr_user_action: btn_action,
              cookie_list: GDPR_Cookie.getallcookies(),
              currentSite: window.location.href,
              consent_forward: false,
            },
            success: function (response) {},
          });
        }
        // consent forwarding.
        else if (consent_forwarding && self.settings.logging_on) {
          var subSites = gdpr_select_sites;
          // Loop through sub-sites and trigger consent log
          subSites.forEach(function (subSiteId) {
            if (subSiteId != null || subSiteId !== " ") {
              jQuery.ajax({
                type: "POST",
                url: log_obj.ajax_url,
                data: {
                  action: "gdpr_log_consent_action",
                  security: log_obj.consent_logging_nonce,
                  gdpr_user_action: btn_action,
                  cookie_list: GDPR_Cookie.getallcookies(),
                  subSiteId: subSiteId,
                  currentSite: window.location.href,
                  consent_forward: true,
                },
                success: function (response) {},
              });
            }
          });
        }
      }, 1000);
    },

    disableAllCookies: function () {
      var gdpr_user_preference_arr = {};
      var gdpr_user_preference_val = "";
      if (GDPR_Cookie.read("wpl_user_preference")) {
        gdpr_user_preference_arr = JSON.parse(
          GDPR_Cookie.read("wpl_user_preference")
        );
        jQuery.each(gdpr_user_preference_arr, function (key, value) {
          if (key != "necessary") {
            gdpr_user_preference_arr[key] = "no";
            $('.gdpr_messagebar_detail input[value="' + key + '"]').prop(
              "checked",
              false
            );
            var length = GDPR.allowed_categories.length;
            for (var i = 0; i < length; i++) {
              if (GDPR.allowed_categories[i] == key) {
                GDPR.allowed_categories.splice(i, 1);
              }
            }
          }
        });
        gdpr_user_preference_val = JSON.stringify(gdpr_user_preference_arr);
        GDPR_Cookie.set(
          "wpl_user_preference",
          gdpr_user_preference_val,
          GDPR_ACCEPT_COOKIE_EXPIRE
        );
      }
    },
    acceptAllCookies: function () {
      var gdpr_user_preference_arr = {};
      var gdpr_user_preference_val = "";
      if (GDPR_Cookie.read("wpl_user_preference")) {
        gdpr_user_preference_arr = JSON.parse(
          GDPR_Cookie.read("wpl_user_preference")
        );
        jQuery.each(gdpr_user_preference_arr, function (key, value) {
          if (key != "necessary") {
            gdpr_user_preference_arr[key] = "yes";
            $('.gdpr_messagebar_detail input[value="' + key + '"]').prop(
              "checked",
              true
            );

            if (!GDPR.allowed_categories.includes(key)) {
              GDPR.allowed_categories.push(key);
            }
          }
        });
        gdpr_user_preference_val = JSON.stringify(gdpr_user_preference_arr);
        GDPR_Cookie.set(
          "wpl_user_preference",
          gdpr_user_preference_val,
          GDPR_ACCEPT_COOKIE_EXPIRE
        );
      }
    },
    show_details: function () {
      this.details_elm.show();
      this.details_elm.css("border-top-color", GDPR.settings.border_color);
      this.settings_button.attr("data-gdpr_action", "hide_settings");
      jQuery("#gdpr_messagebar_detail_body_content_about").hide();
    },
    hide_details: function () {
      this.details_elm.hide();
      this.settings_button.attr("data-gdpr_action", "show_settings");
    },
    displayHeader: function (
      gdpr_flag,
      ccpa_flag,
      lgpd_flag,
      force_display_bar,
      force_display_show_again,
      user_triggered
    ) {
      user_triggered = (typeof user_triggered === 'undefined') ? false : user_triggered;
      if (!gdpr_flag || !ccpa_flag || !lgpd_flag) {
        var animate_on_load = GDPR.settings.notify_animate_show;
        var self = this;
        if (force_display_bar || animate_on_load) {
          if (this.settings.auto_banner_initialize && !user_triggered) {
            var banner = this.bar_elm;
            var banner_delay = this.settings.auto_banner_initialize_delay;
            var animate_speed_hide = this.settings.animate_speed_hide;
            setTimeout(function () {
              self.bar_elm.slideDown(self.settings.animate_speed_hide);
            }, banner_delay);
          } else {
            self.bar_elm.slideDown(self.settings.animate_speed_hide);
          }
        } else {
          // Check if pages are selected to hide the banner
          var hideBanner = false;

          if (gdpr_select_pages.length > 0) {
            for (var id = 0; id < gdpr_select_pages.length; id++) {
              var pageToHideBanner = gdpr_select_pages[id];
              if (
                document.body.classList.contains("page-id-" + pageToHideBanner)
              ) {
                hideBanner = true; // Mark that the banner should be hidden on this page

                if (
                  GDPR.settings.cookie_usage_for == "gdpr" ||
                  GDPR.settings.cookie_usage_for == "eprivacy" ||
                  GDPR.settings.cookie_usage_for == "both" ||
                  GDPR.settings.cookie_usage_for == "lgpd"
                ) {
                  var banner = document.getElementById(
                    "gdpr-cookie-consent-show-again"
                  );
                  var insidebanner = document.getElementById(
                    "gdpr-cookie-consent-bar"
                  );
                  if (GDPR.settings.cookie_bar_as == "popup") {
                    $("#gdpr-popup").gdprmodal("hide");
                  }
                  if (banner || insidebanner) {
                    banner.style.display = "none";
                    insidebanner.style.display = "none";
                  }
                } else if (GDPR.settings.cookie_usage_for == "ccpa") {
                  if (GDPR.settings.cookie_bar_as == "popup") {
                    $("#gdpr-popup").gdprmodal("hide");
                  }
                  var insidebanner = document.getElementById(
                    "gdpr-cookie-consent-bar"
                  );
                  if (insidebanner) {
                    insidebanner.style.display = "none";
                  }
                }
                break; // Exit the loop once we find a page that hides the banner
              }
            }
          }
          function userInteracted() {
            // Make the AJAX call
            jQuery.ajax({
              url: log_obj.ajax_url,
              type: "POST",
              data: {
                action: "gdpr_increase_ignore_rate",
                security: log_obj.consent_logging_nonce,
              },
              success: function (response) {},
            });

            // Remove the listeners after interaction
            document.removeEventListener("click", userInteracted);
            document.removeEventListener("scroll", userInteracted);
          }

          // Show the banner if it is enabled and no pages are set to hide it
          if (this.settings.auto_banner_initialize && !hideBanner) {
            setTimeout(function () {
              self.bar_elm.show();
              jQuery.ajax({
                url: log_obj.ajax_url,
                type: "POST",
                data: {
                  action: "gdpr_increase_page_view",
                  security: log_obj.consent_logging_nonce,
                },
                success: function (response) {},
              });
              document.addEventListener("click", userInteracted);
              document.addEventListener("scroll", userInteracted);
            }, this.settings.auto_banner_initialize_delay);
          }

          if (!this.settings.auto_banner_initialize && !hideBanner) {
            self.bar_elm.show();
            jQuery.ajax({
              url: log_obj.ajax_url,
              type: "POST",
              data: {
                action: "gdpr_increase_page_view",
                security: log_obj.consent_logging_nonce,
              },
              success: function (response) {},
            });
            document.addEventListener("click", userInteracted);
            document.addEventListener("scroll", userInteracted);
          }
        }
      }

      if (gdpr_flag) {
        jQuery(GDPR.settings.notify_div_id).find("p.gdpr").hide();
        jQuery(GDPR.settings.notify_div_id).find("h3.gdpr_heading").hide();
        jQuery(GDPR.settings.notify_div_id)
          .find(".gdpr.group-description-buttons")
          .hide();

        if (this.settings.auto_banner_initialize) {
          var banner_delay = this.settings.auto_banner_initialize_delay;
          setTimeout(function () {
            if (GDPR.settings.cookie_usage_for == "both"){
              jQuery(GDPR.settings.notify_div_id).css("background", GDPR.convertToHex(GDPR.settings.multiple_legislation_cookie_bar_color2, GDPR.settings.multiple_legislation_cookie_bar_opacity2));
              jQuery(GDPR.settings.notify_div_id).css("color", GDPR.settings.multiple_legislation_cookie_text_color2);
              jQuery(GDPR.settings.notify_div_id).css("border-style", GDPR.settings.multiple_legislation_border_style2);
              jQuery(GDPR.settings.notify_div_id).css("border-color", GDPR.settings.multiple_legislation_cookie_border_color2);
              jQuery(GDPR.settings.notify_div_id).css("border-width", GDPR.settings.multiple_legislation_cookie_bar_border_width2);
              jQuery(GDPR.settings.notify_div_id).css("border-radius", GDPR.settings.multiple_legislation_cookie_bar_border_radius2);
              jQuery(GDPR.settings.notify_div_id).css("font-family", GDPR.settings.multiple_legislation_cookie_font2);
            }
            jQuery(GDPR.settings.notify_div_id).find("p.ccpa").show();
          }, banner_delay);
        } else {
          multiple_legislation_current_banner = "ccpa";
          if (GDPR.settings.cookie_usage_for == "both"){
            jQuery(GDPR.settings.notify_div_id).css("background", GDPR.convertToHex(GDPR.settings.multiple_legislation_cookie_bar_color2, GDPR.settings.multiple_legislation_cookie_bar_opacity2));
            jQuery(GDPR.settings.notify_div_id).css("color", GDPR.settings.multiple_legislation_cookie_text_color2);
            jQuery(GDPR.settings.notify_div_id).css("border-style", GDPR.settings.multiple_legislation_border_style2);
            jQuery(GDPR.settings.notify_div_id).css("border-color", GDPR.settings.multiple_legislation_cookie_border_color2);
            jQuery(GDPR.settings.notify_div_id).css("border-width", GDPR.settings.multiple_legislation_cookie_bar_border_width2);
            jQuery(GDPR.settings.notify_div_id).css("border-radius", GDPR.settings.multiple_legislation_cookie_bar_border_radius2);
            jQuery(GDPR.settings.notify_div_id).css("font-family", GDPR.settings.multiple_legislation_cookie_font2);
          }
          jQuery(GDPR.settings.notify_div_id).find("p.ccpa").show();
        }
      }
      if (lgpd_flag) {
        jQuery(GDPR.settings.notify_div_id).find("p.gdpr").hide();
        jQuery(GDPR.settings.notify_div_id).find("h3.gdpr_heading").hide();
        jQuery(GDPR.settings.notify_div_id)
          .find(".gdpr.group-description-buttons")
          .hide();
        if (this.settings.auto_banner_initialize) {
          var banner_delay = this.settings.auto_banner_initialize_delay;
          setTimeout(function () {
            jQuery(GDPR.settings.notify_div_id).find("p.ccpa").show();
          }, banner_delay);
        } else {
          jQuery(GDPR.settings.notify_div_id).find("p.ccpa").show();
        }
      }
      if (ccpa_flag || GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME)) {
        jQuery(GDPR.settings.notify_div_id).find("p.ccpa").hide();
      }
      if (this.settings.cookie_bar_as == "popup") {
        if (!gdpr_flag || !ccpa_flag || !lgpd_flag) {
          if (this.settings.auto_banner_initialize) {
            var banner_delay = this.settings.auto_banner_initialize_delay;
            setTimeout(function () {
              $("#gdpr-popup").gdprmodal("show");
            }, banner_delay);
          } else {
            $("#gdpr-popup").gdprmodal("show");
          }
        }
      }
      if (
        this.settings.cookie_usage_for == "gdpr" ||
        this.settings.cookie_usage_for == "lgpd" ||
        this.settings.cookie_usage_for == "eprivacy" ||
        this.settings.cookie_usage_for == "both"
      ) {
        if (force_display_show_again) {
          this.show_again_elm.slideDown(this.settings.animate_speed_hide);
        } else {
          this.show_again_elm.slideUp(this.settings.animate_speed_hide);
        }
      }
    },
    hideHeader: function (geo_flag) {
      this.bar_elm.slideUp(this.settings.animate_speed_hide);
      if (!geo_flag) {
        if (this.settings.cookie_bar_as == "popup") {
          $("#gdpr-popup").gdprmodal("hide");
        }
        if (
          this.settings.cookie_usage_for == "gdpr" ||
          this.settings.cookie_usage_for == "eprivacy" ||
          this.settings.cookie_usage_for == "both" ||
          this.settings.cookie_usage_for == "lgpd"
        ) {
          this.show_again_elm.slideDown(this.settings.animate_speed_hide);
        }
      }
    },
    acceptOnScroll: function () {
      var scrollTop = $(window).scrollTop();
      var docHeight = $(document).height();
      var winHeight = $(window).height();
      var scrollPercent = scrollTop / (docHeight - winHeight);
      var scrollPercentRounded = Math.round(scrollPercent * 100);

      if (
        scrollPercentRounded > GDPR.settings.auto_scroll_offset &&
        !GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME)
      ) {
        if (GDPR.settings.button_accept_all_is_on) {
          GDPR.acceptAllCookies();
        }
        if (GDPR.settings.auto_scroll_reload == true) {
          GDPR.accept_close();
          GDPR.logConsent("accept");
          setTimeout(function () {
            window.location.reload();
          }, 1100);
        } else {
          GDPR.accept_close();
          GDPR.logConsent("accept");
        }
        window.removeEventListener("scroll", GDPR.acceptOnScroll, false);
      }
    },
    
  };

  var GDPR_Blocker = {
    blockingStatus: true,
    scriptsLoaded: false,
    set: function (args) {
      if (typeof JSON.parse !== "function") {
        console.log(
          "GDPRCookieConsent requires JSON.parse but your browser doesn't support it"
        );
        return;
      }
      this.cookies = args.cookies;
    },
    removeCookieByCategory: function () {
      if (GDPR_Blocker.blockingStatus == true) {
        var cookiesList = GDPR_Blocker.cookies;
        for (var i = 0; i < cookiesList.length; i++) {
          var cookie = cookiesList[i];
          var current_category = cookie["gdpr_cookie_category_slug"];
          if (GDPR.allowed_categories.indexOf(current_category) === -1) {
            var cookies = cookie["data"];
            if (cookies && cookies.length != 0) {
              for (var c_key in cookies) {
                var c_cookie = cookies[c_key];
                GDPR_Cookie.erase(c_cookie["name"]);
              }
            }
          }
        }
      }
    },
    runScripts: function () {
      var srcReplaceableElms = [
        "iframe",
        "IFRAME",
        "EMBED",
        "embed",
        "OBJECT",
        "object",
        "IMG",
        "img",
      ];
      var genericFuncs = {
        renderByElement: function (callback) {
          scriptFuncs.renderScripts();
          htmlElmFuncs.renderSrcElement();
          callback();
          GDPR_Blocker.scriptsLoaded = true;
        },
        reviewConsent: function () {
          jQuery(document).on(
            "click",
            ".wpl_manage_current_consent",
            function () {
              GDPR.displayHeader();
            }
          );
        },
      };
      var scriptFuncs = {
        scriptsDone: function () {
          var DOMContentLoadedEvent = document.createEvent("Event");
          DOMContentLoadedEvent.initEvent("DOMContentLoaded", true, true);
          window.document.dispatchEvent(DOMContentLoadedEvent);
        },
        seq: function (arr, callback, index) {
          if (typeof index === "undefined") {
            index = 0;
          }

          arr[index](function () {
            index++;
            if (index === arr.length) {
              callback();
            } else {
              scriptFuncs.seq(arr, callback, index);
            }
          });
        },

        insertScript: function ($script, callback) {
          var allowedAttributes = [
            "data-wpl-class",
            "data-wpl-label",
            "data-wpl-placeholder",
            "data-wpl-script-type",
            "data-wpl-src",
          ];
          var scriptType = $script.getAttribute("data-wpl-script-type");
          var elementPosition = $script.getAttribute(
            "data-wpl-element-position"
          );
          var isBlock = $script.getAttribute("data-wpl-block");
          var s = document.createElement("script");
          s.type = "text/plain";
          if ($script.async) {
            s.async = $script.async;
          }
          if ($script.defer) {
            s.defer = $script.defer;
          }
          if ($script.src) {
            s.onload = callback;
            s.onerror = callback;
            s.src = $script.src;
          } else {
            s.textContent = $script.innerText;
          }
          var attrs = jQuery($script).prop("attributes");
          var length = attrs.length;
          for (var ii = 0; ii < length; ++ii) {
            if (attrs[ii].nodeName !== "id") {
              if (allowedAttributes.indexOf(attrs[ii].nodeName) !== -1) {
                s.setAttribute(attrs[ii].nodeName, attrs[ii].value);
              }
            }
          }
          if (GDPR_Blocker.blockingStatus === true) {
            if (
              (GDPR_Cookie.read(GDPR_ACCEPT_COOKIE_NAME) == "yes" &&
                GDPR.allowed_categories.indexOf(scriptType) !== -1) ||
              (GDPR_Cookie.read(GDPR_ACCEPT_COOKIE_NAME) == null &&
                isBlock === "false")
            ) {
              s.setAttribute("data-wpl-consent", "accepted");
              s.type = "text/javascript";
            }
          } else {
            s.type = "text/javascript";
          }
          if ($script.type != s.type) {
            if (elementPosition === "head") {
              document.head.appendChild(s);
              if (!$script.src) {
                callback();
              }
              $script.parentNode.removeChild($script);
            } else {
              document.body.appendChild(s);
              if (!$script.src) {
                callback();
              }
              $script.parentNode.removeChild($script);
            }
          }
        },
        renderScripts: function () {
          var $scripts = document.querySelectorAll(
            'script[data-wpl-class="wpl-blocker-script"]'
          );
          if ($scripts.length > 0) {
            var runList = [];
            var typeAttr;
            Array.prototype.forEach.call($scripts, function ($script) {
              typeAttr = $script.getAttribute("type");
              var elmType = $script.tagName;
              runList.push(function (callback) {
                scriptFuncs.insertScript($script, callback);
              });
            });
            scriptFuncs.seq(runList, scriptFuncs.scriptsDone);
          }
        },
      };
      var htmlElmFuncs = {
        renderSrcElement: function () {
          var blockingElms = document.querySelectorAll(
            '[data-wpl-class="wpl-blocker-script"]'
          );
          var length = blockingElms.length;
          for (var i = 0; i < length; i++) {
            var currentElm = blockingElms[i];
            var elmType = currentElm.tagName;
            if (srcReplaceableElms.indexOf(elmType) !== -1) {
              var elmCategory = currentElm.getAttribute("data-wpl-script-type");
              var isBlock = currentElm.getAttribute("data-wpl-block");
              if (GDPR_Blocker.blockingStatus === true) {
                if (
                  (GDPR_Cookie.read(GDPR_ACCEPT_COOKIE_NAME) == "yes" &&
                    GDPR.allowed_categories.indexOf(elmCategory) !== -1) ||
                  (GDPR_Cookie.read(GDPR_ACCEPT_COOKIE_NAME) != null &&
                    isBlock === "false")
                ) {
                  this.replaceSrc(currentElm);
                } else {
                  this.addPlaceholder(currentElm);
                }
              } else {
                this.replaceSrc(currentElm);
              }
            }
          }
        },
        addPlaceholder: function (htmlElm) {
          if (jQuery(htmlElm).prev(".wpl-iframe-placeholder").length === 0) {
            var htmlElemType = htmlElm.getAttribute("data-wpl-placeholder");
            var htmlElemWidth = htmlElm.getAttribute("width");
            var htmlElemHeight = htmlElm.getAttribute("height");
            if (htmlElemWidth == null) {
              htmlElemWidth = htmlElm.offsetWidth;
            }
            if (htmlElemHeight == null) {
              htmlElemHeight = htmlElm.offsetHeight;
            }
            var pixelPattern = /px/;
            htmlElemWidth = pixelPattern.test(htmlElemWidth)
              ? htmlElemWidth
              : htmlElemWidth + "px";
            htmlElemHeight = pixelPattern.test(htmlElemHeight)
              ? htmlElemHeight
              : htmlElemHeight + "px";
            var addPlaceholder =
              '<div style="width:' +
              htmlElemWidth +
              "; height:" +
              htmlElemHeight +
              ';" class="wpl-iframe-placeholder"><div class="wpl-inner-text">' +
              htmlElemType +
              "</div></div>";
            if (htmlElm.tagName !== "IMG") {
              jQuery(addPlaceholder).insertBefore(htmlElm);
            }
            htmlElm.removeAttribute("src");
            htmlElm.style.display = "none";
          }
        },
        replaceSrc: function (htmlElm) {
          if (!htmlElm.hasAttribute("src")) {
            var htmlElemSrc = htmlElm.getAttribute("data-wpl-src");
            htmlElm.setAttribute("src", htmlElemSrc);
            if (jQuery(htmlElm).prev(".wpl-iframe-placeholder").length > 0) {
              jQuery(htmlElm).prev(".wpl-iframe-placeholder").remove();
            }
            htmlElm.style.display = "block";
          }
        },
      };
      genericFuncs.reviewConsent();
      genericFuncs.renderByElement(GDPR_Blocker.removeCookieByCategory);
    },
  };
  $(document).ready(function () {
    var settings = JSON.parse(gdpr_cookiebar_settings);
    if (settings["notify_animate_show"]) {
      $("#gdpr-cookie-consent-bar").css("display", "none");
      $("#gdpr-cookie-consent-bar").slideDown(500);
    }
    if (typeof gdpr_cookies_list != "undefined") {
      GDPR_Blocker.set({
        cookies: gdpr_cookies_list,
      });
      GDPR_Blocker.runScripts();
    }
    if (typeof gdpr_cookiebar_settings != "undefined") {
      GDPR.set({
        settings: gdpr_cookiebar_settings,
      });
    }
  });

  $(document).ready(function () {
    if ( gdpr_ab_options.ab_testing_enabled === "false" || gdpr_ab_options.ab_testing_enabled === false ) {
      $(
          ".gdpr_messagebar_detail .gdpr-iab-navbar .gdpr-iab-navbar-button.active"
        ).css("color", GDPR.settings.button_accept_all_button_color);
        $(
          ".gdpr_messagebar_detail.layout-classic .gdpr-iab-navbar .gdpr-iab-navbar-button.active"
        ).css(
          "border-bottom",
          "2px solid " + GDPR.settings.button_accept_all_button_color
        );
    } else {
      const accentColor = GDPR.settings["button_accept_all_button_color" + chosenBanner];
      $(
        ".gdpr_messagebar_detail .gdpr-iab-navbar .gdpr-iab-navbar-button.active"
      ).css("color", accentColor);
      $(
        ".gdpr_messagebar_detail.layout-classic .gdpr-iab-navbar .gdpr-iab-navbar-button.active"
      ).css(
        "border-bottom",
        "2px solid " + accentColor

      );
    }
    
    if (is_iab_on) {

    }
    $(".gdpr-iab-navbar-item").click(function () {
      const modalBody = document.querySelector('.gdprmodal-body');

      $(
        ".gdpr_messagebar_detail .gdpr-iab-navbar .gdpr-iab-navbar-button.active"
      ).css("color", GDPR.settings.button_accept_all_button_color);
      $(".gdpr-iab-navbar-item", this);
      $(".tabContainer").css("display", "none");

      switch (this.id) {
        case "gdprIABTabCategory":
          $(".cat").css("display", "block");
          modalBody.style.height = '67vh';
          break;
        case "gdprIABTabFeatures":
          $(".feature-group").css("display", "block");
          modalBody.style.height = '56vh';
          break;
        case "gdprIABTabVendors":
          $(".vendor-group").css("display", "block");
          modalBody.style.height = '60vh';
          break;
      }
      if (!$(this).children(".gdpr-iab-navbar-button").hasClass("active")) {
        $(".gdpr-iab-navbar-button").removeClass("active");
        // $( ".gdpr-iab-navbar-button" ).css( 'color', '#000' );
        $(".gdpr-iab-navbar-button").css("border-bottom", "none");
        $(this).children(".gdpr-iab-navbar-button").addClass("active");
        $(".gdpr-iab-navbar-button").css("color", "inherit");
        
        if ( gdpr_ab_options.ab_testing_enabled === "false" || gdpr_ab_options.ab_testing_enabled === false ) {
          $(this)
            .children(".gdpr-iab-navbar-button.active")
            .css("color", GDPR.settings.button_accept_all_button_color);
          $(this)
            .children(".gdpr-iab-navbar-button.active")
            .css("color", GDPR.settings.button_accept_all_button_color);
          $(this)
            .children(".gdpr-iab-navbar-button.active")
            .css(
              "border-bottom",
              "2px solid " + GDPR.settings.button_accept_all_button_color
            );
        } else {
          const accentColor = GDPR.settings["button_accept_all_button_color" + chosenBanner];
          $(this)
            .children(".gdpr-iab-navbar-button.active")
            .css("color", accentColor);
          $(this)
            .children(".gdpr-iab-navbar-button.active")
            .css("color", accentColor);
          $(this)
            .children(".gdpr-iab-navbar-button.active")
            .css(
              "border-bottom",
              "2px solid " + accentColor);
        }
        
        $(this).siblings(".gdpr-iab-navbar-button").css("display", "none");
      }
      $(
        ".gdpr_messagebar_detail.layout-default .gdpr-iab-navbar .gdpr-iab-navbar-button.active"
      ).css("border", "none");
    });
    $(".gdpr-default-category-toggle.gdpr-column").click(function () {
      $(".gdpr-default-category-toggle.gdpr-column", this);
      if (!$(this).children(".gdpr-columns").hasClass("active-group")) {
        $(".gdpr-columns").removeClass("active-group");
        // $(".gdpr-columns").css("background-color", background_obj.background);
        $(this).children(".gdpr-columns").addClass("active-group");
        
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
        // $(".gdpr-columns").css("background-color", background_obj.background);
        $(".gdpr-columns .gdpr-dropdown-arrow").removeClass("rotated");
        
        $(this).children(".gdpr-columns").addClass("active-group");
        $(this)
          .children(".gdpr-columns")
          .find(".gdpr-dropdown-arrow")
          .addClass("rotated");
        // $( this ).children( ".gdpr-columns" ).find( ".btn.category-header" ).css( "color", button_revoke_consent_text_color );
      } else {
        $(".gdpr-columns").removeClass("active-group");
        $(this)
          .children(".gdpr-columns")
          .find(".gdpr-dropdown-arrow")
          .removeClass("rotated");
      }
      if ($(this).siblings(".description-container").hasClass("hide")) {
        $(".description-container").addClass("hide");
        $(this).siblings(".description-container").removeClass("hide");
      } else {
        $(".description-container").addClass("hide");
      }
    });

    $(".gdpr-category-toggle.inner-gdpr-column").click(function () {
      var heightOfB = $(this).outerHeight() - 23;
      $(".gdpr-category-toggle.inner-gdpr-column", this);
      if (!$(this).children(".inner-gdpr-columns").hasClass("active-group")) {
        $(".inner-gdpr-columns").removeClass("active-group");
        $(".inner-gdpr-columns .gdpr-dropdown-arrow").removeClass("rotated");

        $(this).children(".inner-gdpr-columns").addClass("active-group");
        $(this)
          .children(".inner-gdpr-columns")
          .find(".gdpr-dropdown-arrow")
          .addClass("rotated");
      } else {
        $(".inner-gdpr-columns").removeClass("active-group");
        $(this)
          .children(".inner-gdpr-columns")
          .find(".gdpr-dropdown-arrow")
          .removeClass("rotated");
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
        $(this).children(".inner-gdpr-columns").addClass("active-group");
      }
      if ($(this).siblings(".inner-description-container").hasClass("hide")) {
        $(".inner-description-container").addClass("hide");
        $(this).siblings(".inner-description-container").removeClass("hide");
      }
    });
  });

  // Background color and text color settings for the cookie settings section.
  if (
    gdpr_ab_options.ab_testing_enabled === "false" ||
    gdpr_ab_options.ab_testing_enabled === false
  ) {
    // CSS for the banner when ab testing is disabled.
    
  }
  document.addEventListener("DOMContentLoaded", function () {
    const parent = document.querySelector(
      ".widget-navy_blue_box .gdpr_messagebar_content .gdpr.group-description-buttons"
    );
    if (parent) { 
      const children = parent.children;

      if (
        children.length === 2 &&
        children[0].id === "cookie_action_accept" &&
        children[1].id === "cookie_action_settings"
      ) {
          parent.classList.add("exact-two-anchors");
      }
  }
  });
})(jQuery);
