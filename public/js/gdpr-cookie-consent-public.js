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
  var GDPR = {
    bar_config: {},
    show_config: {},
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

      this.configBar();
      this.check_ccpa_eu();

      this.attachEvents();
      this.configButtons();

      // changing the color and background of cookie setting button.
      var revoke_color = document.getElementById(
        "gdpr-cookie-consent-show-again"
      );
      // Check if the element exists before applying the style
      if (revoke_color) {
        revoke_color.style.color = button_revoke_consent_text_color;
        revoke_color.style.backgroundColor =
          button_revoke_consent_background_color;
      }

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
          gtag('consent', 'update', {
            'ad_user_data': marketing_consent ? 'granted' : 'denied',
            'ad_personalization': marketing_consent ? 'granted' : 'denied',
            'ad_storage': marketing_consent ? 'granted' : 'denied',
            'analytics_storage': analytics_consent ? 'granted' : 'denied',
            'functionality_storage': preferences_consent ? 'granted' : 'denied',
            'personalization_storage': preferences_consent ? 'granted' : 'denied',
            'security_storage': 'granted'
          });
          
          
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
          gtag('consent', 'update', {
            'ad_user_data': 'granted',
            'ad_personalization': 'granted',
            'ad_storage': 'granted',
            'analytics_storage': 'granted',
            'functionality_storage': 'granted',
            'personalization_storage': 'granted',
            'security_storage': 'granted'
          });
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
                wpl_user_preference: gdpr_user_preference,
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
            gtag('consent', 'update', {
            'ad_user_data': 'denied',
            'ad_personalization': 'denied',
            'ad_storage': 'denied',
            'analytics_storage': 'denied',
            'functionality_storage': 'denied',
            'personalization_storage': 'denied',
            'security_storage': 'granted'
          });
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
          if (new_window) {
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
            var background = GDPR.convertToHex(
              GDPR.settings.multiple_legislation_cookie_bar_color1,
              GDPR.settings.multiple_legislation_cookie_bar_opacity1
            );

            var border =
              GDPR.settings.multiple_legislation_cookie_bar_border_width1 +
              "px " +
              GDPR.settings.multiple_legislation_border_style1 +
              " " +
              GDPR.settings.multiple_legislation_cookie_border_color1;

            GDPR.bar_config = {
              "background-color": background,
              color: GDPR.settings.multiple_legislation_cookie_text_color1,
              "font-family": GDPR.settings.multiple_legislation_cookie_font1,
              "box-shadow": GDPR.settings.background + " 0 0 8px",
              border: border,
              "border-radius":
                GDPR.settings.multiple_legislation_cookie_bar_border_radius1 +
                "px",
            };
            GDPR.show_config = {
              width: "auto",
              "background-color": background,
              "box-shadow": GDPR.settings.background + " 0 0 8px",
              color: GDPR.settings.text,
              "font-family": GDPR.settings.font_family,
              position: "fixed",
              bottom: "0",
              border: border,
              "border-radius": GDPR.settings.background_border_radius + "px",
            };

            var template = GDPR.settings.template;
            if (template.includes("row") || template.includes("center")) {
              GDPR.bar_config["text-align"] = "center";
            } else {
              GDPR.bar_config["text-align"] = "justify";
            }

            if (GDPR.settings.show_again_position == "right") {
              GDPR.show_config["right"] = GDPR.settings.show_again_margin + "%";
            } else {
              GDPR.show_config["left"] = GDPR.settings.show_again_margin + "%";
            }
            GDPR.bar_config["position"] = "fixed";
            if (GDPR.settings.cookie_bar_as == "banner") {
              GDPR.bar_elm
                .find(".gdpr_messagebar_content")
                .css("max-width", "800px");
              if (GDPR.settings.notify_position_vertical == "bottom") {
                GDPR.bar_config["bottom"] = "0";
              } else {
                GDPR.bar_config["top"] = "0";
              }
            }
            if (GDPR.settings.cookie_bar_as == "widget") {
              GDPR.bar_config["width"] = "35%";
              if (GDPR.settings.notify_position_horizontal == "left") {
                GDPR.bar_config["bottom"] = "20px";
                GDPR.bar_config["left"] = "20px";
              } else if (GDPR.settings.notify_position_horizontal == "right") {
                GDPR.bar_config["bottom"] = "20px";
                GDPR.bar_config["right"] = "20px";
              } else if (
                GDPR.settings.notify_position_horizontal == "top_right"
              ) {
                GDPR.bar_config["top"] = "20px";
                GDPR.bar_config["right"] = "20px";
              } else if (
                GDPR.settings.notify_position_horizontal == "top_left"
              ) {
                GDPR.bar_config["top"] = "20px";
                GDPR.bar_config["left"] = "20px";
              }
            }
            if (GDPR.settings.cookie_bar_as == "popup") {
              GDPR.bar_config["border"] = "unset";
              GDPR.bar_config["border-radius"] = "unset";
              GDPR.bar_config["position"] = "unset";
              GDPR.bar_config["box-shadow"] = "unset";
              GDPR.bar_config["background-color"] = "unset";
              jQuery("#gdpr-popup .gdprmodal-content").css(
                "background-color",
                background
              );
              jQuery("#gdpr-popup .gdprmodal-content").css("border", border);
              jQuery("#gdpr-popup .gdprmodal-content").css(
                "border-radius",
                GDPR.settings.background_border_radius + "px"
              );
              jQuery("#gdpr-popup .gdprmodal-content").css(
                "box-shadow",
                GDPR.settings.background + " 0 0 8px"
              );
            }
            GDPR.bar_elm.css(GDPR.bar_config).hide();
            GDPR.show_again_elm.css(GDPR.show_config).hide();
          }
          jQuery(GDPR.settings.notify_div_id).find("p.gdpr").show();
          jQuery(GDPR.settings.notify_div_id)
            .find(".gdpr.group-description-buttons")
            .show();
          GDPR.displayHeader();
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
            elm.css("border-bottom-color", GDPR.settings.border_active_color);
            elm.css("background-color", GDPR.settings.background_active_color);
            jQuery("#gdpr_messagebar_detail_body_content_tabs_about").css(
              "border-bottom-color",
              GDPR.settings.border_color
            );
            jQuery("#gdpr_messagebar_detail_body_content_tabs_about").css(
              "background-color",
              GDPR.settings.background_color
            );
          } else {
            if (Number(chosenBanner) === 1) {
              elm.addClass(
                "gdpr_messagebar_detail_body_content_tab_item_selected"
              );
              elm.css(
                "border-bottom-color",
                GDPR.settings.border_active_color1
              );
              elm.css(
                "background-color",
                GDPR.settings.background_active_color1
              );
              jQuery("#gdpr_messagebar_detail_body_content_tabs_about").css(
                "border-bottom-color",
                GDPR.settings.border_color1
              );
              jQuery("#gdpr_messagebar_detail_body_content_tabs_about").css(
                "background-color",
                GDPR.settings.background_color1
              );
            } else {
              elm.addClass(
                "gdpr_messagebar_detail_body_content_tab_item_selected"
              );
              elm.css(
                "border-bottom-color",
                GDPR.settings.border_active_color2
              );
              elm.css(
                "background-color",
                GDPR.settings.background_active_color2
              );
              jQuery("#gdpr_messagebar_detail_body_content_tabs_about").css(
                "border-bottom-color",
                GDPR.settings.border_color2
              );
              jQuery("#gdpr_messagebar_detail_body_content_tabs_about").css(
                "background-color",
                GDPR.settings.background_color2
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
            elm.css("border-bottom-color", GDPR.settings.border_active_color);
            elm.css("background-color", GDPR.settings.background_active_color);
            jQuery("#gdpr_messagebar_detail_body_content_tabs_overview").css(
              "border-bottom-color",
              GDPR.settings.border_color
            );
            jQuery("#gdpr_messagebar_detail_body_content_tabs_overview").css(
              "background-color",
              GDPR.settings.background_color
            );
          } else {
            if (Number(chosenBanner) === 1) {
              elm.addClass(
                "gdpr_messagebar_detail_body_content_tab_item_selected"
              );
              elm.css(
                "border-bottom-color",
                GDPR.settings.border_active_color1
              );
              elm.css(
                "background-color",
                GDPR.settings.background_active_color1
              );
              jQuery("#gdpr_messagebar_detail_body_content_tabs_overview").css(
                "border-bottom-color",
                GDPR.settings.border_color1
              );
              jQuery("#gdpr_messagebar_detail_body_content_tabs_overview").css(
                "background-color",
                GDPR.settings.background_color1
              );
            } else {
              elm.addClass(
                "gdpr_messagebar_detail_body_content_tab_item_selected"
              );
              elm.css(
                "border-bottom-color",
                GDPR.settings.border_active_color2
              );
              elm.css(
                "background-color",
                GDPR.settings.background_active_color2
              );
              jQuery("#gdpr_messagebar_detail_body_content_tabs_overview").css(
                "border-bottom-color",
                GDPR.settings.border_color2
              );
              jQuery("#gdpr_messagebar_detail_body_content_tabs_overview").css(
                "background-color",
                GDPR.settings.background_color2
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
            prnt
              .find("a")
              .css("border-right-color", GDPR.settings.border_color);
            prnt
              .find("a")
              .css("background-color", GDPR.settings.background_color);
            elm.addClass(
              "gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
            );
            elm.css("border-right-color", GDPR.settings.border_active_color);
            elm.css("background-color", GDPR.settings.background_active_color);
          } else {
            if (Number(chosenBanner) === 1) {
              prnt
                .find("a")
                .removeClass(
                  "gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
                );
              prnt
                .find("a")
                .css("border-right-color", GDPR.settings.border_color1);
              prnt
                .find("a")
                .css("background-color", GDPR.settings.background_color1);
              elm.addClass(
                "gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
              );
              elm.css("border-right-color", GDPR.settings.border_active_color1);
              elm.css(
                "background-color",
                GDPR.settings.background_active_color1
              );
            } else {
              prnt
                .find("a")
                .removeClass(
                  "gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
                );
              prnt
                .find("a")
                .css("border-right-color", GDPR.settings.border_color2);
              prnt
                .find("a")
                .css("background-color", GDPR.settings.background_color2);
              elm.addClass(
                "gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
              );
              elm.css("border-right-color", GDPR.settings.border_active_color2);
              elm.css(
                "background-color",
                GDPR.settings.background_active_color2
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
          this.settings_button.css(
            "color",
            this.settings.button_settings_link_color1
          );
          if (
            this.settings.button_settings_as_button1 === true ||
            this.settings.button_settings_as_button1 === "true"
          ) {
            var settings_background = this.convertToHex(
              this.settings.button_settings_button_color1,
              this.settings.button_settings_button_opacity1
            );
            var settings_border =
              this.settings.button_settings_button_border_width1 +
              "px " +
              this.settings.button_settings_button_border_style1 +
              " " +
              this.settings.button_settings_button_border_color1;
            this.settings_button.css("border", settings_border);
            this.settings_button.css(
              "border-radius",
              this.settings.button_settings_button_border_radius1 + "px"
            );
            this.settings_button.css("background-color", settings_background);
            this.settings_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_settings_button_hover1
                );
              },
              function () {
                jQuery(this).css("background-color", settings_background);
              }
            );
          }

          if (template.includes("square")) {
            this.settings_button.css("width", "40%");
            this.settings_button.css("float", "right");
          } else if ( template.includes("column")) {
            if ($("#cookie_action_reject").length === 0) {
              $(
                "#gdpr-cookie-consent-bar  > .gdpr_messagebar_content > #default_buttons"
              ).css({
                "width":"0"
              });
              $("#gdpr-cookie-consent-bar .group-description-buttons #cookie_action_settings").css({"margin-left":"0"});
            }
          } else if (template.includes("center")) {
            this.settings_button.css("margin-right", "0");
          } else if (template.includes("dark")) {
            this.settings_button.css("float", "right");
          } else {
            this.settings_button.css("float", "right");
            this.settings_button.css("margin-right", "0");
          }

          if (this.settings.button_accept_all_is_on1) {
            if (template.includes("center")) {
              if (template.includes("popup") || template.includes("widget")) {
                $(
                  ".gdpr-popup > .gdpr_messagebar_content > .group-description-buttons > a"
                ).css({
                  "margin-bottom": "10px",
                });
                $(
                  ".gdpr-widget > .gdpr_messagebar_content > .group-description-buttons > a"
                ).css({
                  "margin-bottom": "10px",
                });
              }
            }
            
          }

          this.main_button.css(
            "color",
            this.settings.button_accept_link_color1
          );
          if (
            this.settings.button_accept_as_button1 == true ||
            this.settings.button_accept_as_button1 == "true"
          ) {
            var main_background = this.convertToHex(
              this.settings.button_accept_button_color1,
              this.settings.button_accept_button_opacity1
            );
            var main_border =
              this.settings.button_accept_button_border_width1 +
              "px " +
              this.settings.button_accept_button_border_style1 +
              " " +
              this.settings.button_accept_button_border_color1;
            this.main_button.css("border", main_border);
            this.main_button.css(
              "border-radius",
              this.settings.button_accept_button_border_radius1 + "px"
            );
            this.main_button.css("background-color", main_background);
            this.main_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_accept_button_hover1
                );
              },
              function () {
                jQuery(this).css("background-color", main_background);
              }
            );
          }

          if (template.includes("square")) {
            this.main_button.css("width", "100%");
            this.main_button.css("margin", "1rem auto 0 auto");
          }

          this.accept_all_button.css(
            "color",
            this.settings.button_accept_all_link_color1
          );
          if (
            this.settings.button_accept_all_as_button1 == true ||
            this.settings.button_accept_all_as_button1 == "true"
          ) {
            var accept_all_background = this.convertToHex(
              this.settings.button_accept_all_button_color1,
              this.settings.button_accept_all_btn_opacity1
            );
            var main_border =
              this.settings.button_accept_all_btn_border_width1 +
              "px " +
              this.settings.button_accept_all_btn_border_style1 +
              " " +
              this.settings.button_accept_all_btn_border_color1;
            this.accept_all_button.css("border", main_border);
            this.accept_all_button.css(
              "border-radius",
              this.settings.multiple_legislation_accept_all_border_radius1 + "px"
            );
            this.accept_all_button.css(
              "background-color",
              accept_all_background
            );
            this.accept_all_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_accept_all_button_hover1
                );
              },
              function () {
                jQuery(this).css("background-color", accept_all_background);
              }
            );
          }

          if (template.includes("square")) {
            this.accept_all_button.css("width", "100%");
            this.accept_all_button.css("margin", "1rem auto 0 auto");
          }

          this.confirm_button.css(
            "color",
            this.settings.button_confirm_link_color1
          );
          if (
            this.settings.button_confirm_as_button1 == true ||
            this.settings.button_confirm_as_button1 == "true"
          ) {
            var confirm_background = this.convertToHex(
              this.settings.button_confirm_button_color1,
              this.settings.button_confirm_button_opacity1
            );
            var confirm_border =
              this.settings.button_confirm_button_border_width1 +
              "px " +
              this.settings.button_confirm_button_border_style1 +
              " " +
              this.settings.button_confirm_button_border_color1;
            this.confirm_button.css("border", confirm_border);
            this.confirm_button.css(
              "border-radius",
              this.settings.button_confirm_button_border_radius1 + "px"
            );
            this.confirm_button.css("background-color", confirm_background);
            this.confirm_button.css("width", "100%");
            this.confirm_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_confirm_button_hover1
                );
              },
              function () {
                jQuery(this).css("background-color", confirm_background);
              }
            );
          }

          /* [wpl_cookie_link] */
          this.main_link.css("color", this.settings.button_readmore_link_color);
          this.vendor_link.css(
            "color",
            this.settings.button_readmore_link_color
          );
          if (this.settings.button_readmore_as_button) {
            var readmore_background = this.convertToHex(
              this.settings.button_readmore_button_color,
              this.settings.button_readmore_button_opacity
            );
            var readmore_border =
              this.settings.button_readmore_button_border_width +
              "px " +
              this.settings.button_readmore_button_border_style +
              " " +
              this.settings.button_readmore_button_border_color;
            this.main_link.css("border", readmore_border);
            this.main_link.css(
              "border-radius",
              this.settings.button_readmore_button_border_radius + "px"
            );
            this.main_link.css("background-color", readmore_background);
            this.main_link.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_readmore_button_hover
                );
              },
              function () {
                jQuery(this).css("background-color", readmore_background);
              }
            );
          }

          this.donotsell_link.css(
            "color",
            this.settings.button_donotsell_link_color1
          );

          this.reject_button.css(
            "color",
            this.settings.button_decline_link_color1
          );
          if (
            this.settings.button_decline_as_button1 == true ||
            this.settings.button_decline_as_button1 == "true"
          ) {
            var decline_background = this.convertToHex(
              this.settings.button_decline_button_color1,
              this.settings.button_decline_button_opacity1
            );
            var reject_border =
              this.settings.button_decline_button_border_width1 +
              "px " +
              this.settings.button_decline_button_border_style1 +
              " " +
              this.settings.button_decline_button_border_color1;
            this.reject_button.css("border", reject_border);
            this.reject_button.css(
              "border-radius",
              this.settings.button_decline_button_border_radius1 + "px"
            );
            this.reject_button.css("background-color", decline_background);
            this.reject_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_decline_button_hover1
                );
              },
              function () {
                jQuery(this).css("background-color", decline_background);
              }
            );
          }

          if (template.includes("square")) {
            this.reject_button.css("width", "40%");
          } else if (
            template.includes("dark")
          ) {
            this.reject_button.css("float", "right");
          }

          this.cancel_button.css(
            "color",
            this.settings.button_cancel_link_color1
          );
          if (
            this.settings.button_cancel_as_button1 == true ||
            this.settings.button_cancel_as_button1 == "true"
          ) {
            var cancel_background = this.convertToHex(
              this.settings.button_cancel_button_color1,
              this.settings.button_cancel_button_opacity1
            );
            var cancel_border =
              this.settings.button_cancel_button_border_width1 +
              "px " +
              this.settings.button_cancel_button_border_style1 +
              " " +
              this.settings.button_cancel_button_border_color1;
            this.cancel_button.css("border", cancel_border);
            this.cancel_button.css(
              "border-radius",
              this.settings.button_cancel_button_border_radius1 + "px"
            );
            this.cancel_button.css("display", "inline-block");
            this.cancel_button.css("background-color", cancel_background);
            this.cancel_button.css("width", "100%");
            this.cancel_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_cancel_button_hover1
                );
              },
              function () {
                jQuery(this).css("background-color", cancel_background);
              }
            );
          }

          this.save_button.css(
            "color",
            this.settings.button_accept_link_color1
          );
          this.save_button.css("background-color", main_background);
          this.save_button.css("border", main_border);
          this.save_button.css(
            "border-radius",
            this.settings.button_accept_button_border_radius1 + "px"
          );
          this.save_button.hover(
            function () {
              jQuery(this).css(
                "background-color",
                GDPR.settings.button_accept_button_hover1
              );
            },
            function () {
              jQuery(this).css("background-color", main_background);
            }
          );
          this.details_elm
            .find(
              "table.gdpr_messagebar_detail_body_content_cookie_type_table tr"
            )
            .css("border-color", GDPR.settings.border_color1);
          this.details_elm
            .find(".gdpr_messagebar_detail_body_content_cookie_type_intro")
            .css("border-color", GDPR.settings.border_color1);
          this.details_elm.find("a").each(function () {
            jQuery(this).css("border-color", GDPR.settings.border_color1);
            jQuery(this).css(
              "background-color",
              GDPR.settings.background_color1
            );
          });
          this.details_elm
            .find(
              "a.gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
            )
            .css("border-right-color", GDPR.settings.border_active_color1);
          this.details_elm
            .find(
              "a.gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
            )
            .css("background-color", GDPR.settings.background_active_color1);
          this.details_elm
            .find("#gdpr_messagebar_detail_body_content")
            .css("border-color", GDPR.settings.border_color1);
          this.details_elm
            .find("#gdpr_messagebar_detail_body_content_tabs")
            .css("border-color", GDPR.settings.border_color1);
          this.details_elm
            .find(
              "#gdpr_messagebar_detail_body_content_tabs .gdpr_messagebar_detail_body_content_tab_item_selected"
            )
            .css("border-bottom-color", GDPR.settings.border_active_color1);
          this.details_elm
            .find(
              "#gdpr_messagebar_detail_body_content_tabs .gdpr_messagebar_detail_body_content_tab_item_selected"
            )
            .css("background-color", GDPR.settings.background_active_color1);

          this.credit_link.css(
            "color",
            this.settings.button_readmore_link_color
          );
          $(".gdpr-column .gdpr-columns.active-group").css(
            "background-color",
            GDPR.settings.button_accept_button_color1
          );
          $(
            ".gdpr_messagebar_detail .category-group .toggle-group .always-active"
          ).css("color", GDPR.settings.button_accept_button_color1);
        } else if (
          this.settings.cookie_usage_for == "both" &&
          multiple_legislation_current_banner == "ccpa"
        ) {
          this.settings_button.css(
            "color",
            this.settings.button_settings_link_color2
          );
          if (
            this.settings.button_settings_as_button2 === true ||
            this.settings.button_settings_as_button2 === "true"
          ) {
            var settings_background = this.convertToHex(
              this.settings.button_settings_button_color2,
              this.settings.button_settings_button_opacity2
            );
            var settings_border =
              this.settings.button_settings_button_border_width2 +
              "px " +
              this.settings.button_settings_button_border_style2 +
              " " +
              this.settings.button_settings_button_border_color2;
            this.settings_button.css("border", settings_border);
            this.settings_button.css(
              "border-radius",
              this.settings.button_settings_button_border_radius2 + "px"
            );
            this.settings_button.css("background-color", settings_background);
            this.settings_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_settings_button_hover2
                );
              },
              function () {
                jQuery(this).css("background-color", settings_background);
              }
            );
          }
         
          if (template.includes("square")) {
            this.settings_button.css("width", "40%");
            this.settings_button.css("float", "right");
          } else if (template.includes("column")) {
            if ($("#cookie_action_reject").length === 0) {
              $(
                "#gdpr-cookie-consent-bar  > .gdpr_messagebar_content > #default_buttons"
              ).css({
                "width":"0"
              });
              $("#gdpr-cookie-consent-bar .group-description-buttons #cookie_action_settings").css({"margin-left":"0"});
            }
          } else if (template.includes("center")) {
            this.settings_button.css("margin-right", "0");
          } else if (template.includes("dark")) {
            this.settings_button.css("float", "right");
          } else {
            this.settings_button.css("float", "right");
            this.settings_button.css("margin-right", "0");
          }

          if (this.settings.button_accept_all_is_on2) {
            if (template.includes("center")) {
              if (template.includes("popup") || template.includes("widget")) {
                $(
                  ".gdpr-popup > .gdpr_messagebar_content > .group-description-buttons > a"
                ).css({
                  "margin-bottom": "10px",
                });
                $(
                  ".gdpr-widget > .gdpr_messagebar_content > .group-description-buttons > a"
                ).css({
                  "margin-bottom": "10px",
                });
              }
            }
            
          }

          this.main_button.css(
            "color",
            this.settings.button_accept_link_color2
          );
          if (
            this.settings.button_accept_as_button2 === true ||
            this.settings.button_accept_as_button2 === "true"
          ) {
            var main_background = this.convertToHex(
              this.settings.button_accept_button_color2,
              this.settings.button_accept_button_opacity2
            );
            var main_border =
              this.settings.button_accept_button_border_width2 +
              "px " +
              this.settings.button_accept_button_border_style2 +
              " " +
              this.settings.button_accept_button_border_color2;
            this.main_button.css("border", main_border);
            this.main_button.css(
              "border-radius",
              this.settings.button_accept_button_border_radius2 + "px"
            );
            this.main_button.css("background-color", main_background);
            this.main_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_accept_button_hover2
                );
              },
              function () {
                jQuery(this).css("background-color", main_background);
              }
            );
          }

          if (template.includes("square")) {
            this.main_button.css("width", "100%");
            this.main_button.css("margin", "1rem auto 0 auto");
          }

          this.accept_all_button.css(
            "color",
            this.settings.button_accept_all_link_color2
          );
          if (
            this.settings.button_accept_all_as_button2 === true ||
            this.settings.button_accept_all_as_button2 === "true"
          ) {
            var accept_all_background = this.convertToHex(
              this.settings.button_accept_all_button_color2,
              this.settings.button_accept_all_btn_opacity2
            );
            var main_border =
              this.settings.button_accept_all_btn_border_width2 +
              "px " +
              this.settings.button_accept_all_btn_border_style2 +
              " " +
              this.settings.button_accept_all_btn_border_color2;
            this.accept_all_button.css("border", main_border);
            this.accept_all_button.css(
              "border-radius",
              this.settings.button_accept_all_btn_border_radius2 + "px"
            );
            this.accept_all_button.css(
              "background-color",
              accept_all_background
            );
            this.accept_all_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_accept_all_button_hover2
                );
              },
              function () {
                jQuery(this).css("background-color", accept_all_background);
              }
            );
          }

          if (template.includes("square")) {
            this.accept_all_button.css("width", "100%");
            this.accept_all_button.css("margin", "1rem auto 0 auto");
          }

          this.confirm_button.css(
            "color",
            this.settings.button_confirm_link_color2
          );
          if (
            this.settings.button_confirm_as_button2 === true ||
            this.settings.button_confirm_as_button2 === "true"
          ) {
            var confirm_background = this.convertToHex(
              this.settings.button_confirm_button_color2,
              this.settings.button_confirm_button_opacity2
            );
            var confirm_border =
              this.settings.button_confirm_button_border_width2 +
              "px " +
              this.settings.button_confirm_button_border_style2 +
              " " +
              this.settings.button_confirm_button_border_color2;
            this.confirm_button.css("border", confirm_border);
            this.confirm_button.css(
              "border-radius",
              this.settings.button_confirm_button_border_radius2 + "px"
            );
            this.confirm_button.css("background-color", confirm_background);
            this.confirm_button.css("width", "100%");
            this.confirm_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_confirm_button_hover2
                );
              },
              function () {
                jQuery(this).css("background-color", confirm_background);
              }
            );
          }

          /* [wpl_cookie_link] */
          this.main_link.css("color", this.settings.button_readmore_link_color);
          this.vendor_link.css(
            "color",
            this.settings.button_readmore_link_color
          );
          if (this.settings.button_readmore_as_button) {
            var readmore_background = this.convertToHex(
              this.settings.button_readmore_button_color,
              this.settings.button_readmore_button_opacity
            );
            var readmore_border =
              this.settings.button_readmore_button_border_width +
              "px " +
              this.settings.button_readmore_button_border_style +
              " " +
              this.settings.button_readmore_button_border_color;
            this.main_link.css("border", readmore_border);
            this.main_link.css(
              "border-radius",
              this.settings.button_readmore_button_border_radius + "px"
            );
            this.main_link.css("background-color", readmore_background);
            this.main_link.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_readmore_button_hover
                );
              },
              function () {
                jQuery(this).css("background-color", readmore_background);
              }
            );
          }

          this.donotsell_link.css(
            "color",
            this.settings.button_donotsell_link_color2
          );

          this.reject_button.css(
            "color",
            this.settings.button_decline_link_color
          );
          if (
            this.settings.button_decline_as_button2 === true ||
            this.settings.button_decline_as_button2 === "true"
          ) {
            var decline_background = this.convertToHex(
              this.settings.button_decline_button_color2,
              this.settings.button_decline_button_opacity2
            );
            var reject_border =
              this.settings.button_decline_button_border_width2 +
              "px " +
              this.settings.button_decline_button_border_style2 +
              " " +
              this.settings.button_decline_button_border_color2;
            this.reject_button.css("border", reject_border);
            this.reject_button.css(
              "border-radius",
              this.settings.button_decline_button_border_radius2 + "px"
            );
            this.reject_button.css("background-color", decline_background);
            this.reject_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_decline_button_hover2
                );
              },
              function () {
                jQuery(this).css("background-color", decline_background);
              }
            );
          }

          if (template.includes("square")) {
            this.reject_button.css("width", "40%");
          } else if (
            template.includes("dark")
          ) {
            this.reject_button.css("float", "right");
          }

          this.cancel_button.css(
            "color",
            this.settings.button_cancel_link_color2
          );
          if (
            this.settings.button_cancel_as_button2 === true ||
            this.settings.button_cancel_as_button2 === "true"
          ) {
            var cancel_background = this.convertToHex(
              this.settings.button_cancel_button_color2,
              this.settings.button_cancel_button_opacity2
            );
            var cancel_border =
              this.settings.button_cancel_button_border_width2 +
              "px " +
              this.settings.button_cancel_button_border_style2 +
              " " +
              this.settings.button_cancel_button_border_color2;
            this.cancel_button.css("border", cancel_border);
            this.cancel_button.css(
              "border-radius",
              this.settings.button_cancel_button_border_radius2 + "px"
            );
            this.cancel_button.css("display", "inline-block");
            this.cancel_button.css("background-color", cancel_background);
            this.cancel_button.css("width", "100%");
            this.cancel_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_cancel_button_hover2
                );
              },
              function () {
                jQuery(this).css("background-color", cancel_background);
              }
            );
          }

          this.save_button.css(
            "color",
            this.settings.button_accept_link_color2
          );
          this.save_button.css("background-color", main_background);
          this.save_button.css("border", main_border);
          this.save_button.css(
            "border-radius",
            this.settings.button_accept_button_border_radius2 + "px"
          );
          this.save_button.hover(
            function () {
              jQuery(this).css(
                "background-color",
                GDPR.settings.button_accept_button_hover2
              );
            },
            function () {
              jQuery(this).css("background-color", main_background);
            }
          );
          this.details_elm
            .find(
              "table.gdpr_messagebar_detail_body_content_cookie_type_table tr"
            )
            .css("border-color", GDPR.settings.border_color2);
          this.details_elm
            .find(".gdpr_messagebar_detail_body_content_cookie_type_intro")
            .css("border-color", GDPR.settings.border_color2);
          this.details_elm.find("a").each(function () {
            jQuery(this).css("border-color", GDPR.settings.border_color2);
            jQuery(this).css(
              "background-color",
              GDPR.settings.background_color2
            );
          });
          this.details_elm
            .find(
              "a.gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
            )
            .css("border-right-color", GDPR.settings.border_active_color2);
          this.details_elm
            .find(
              "a.gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
            )
            .css("background-color", GDPR.settings.background_active_color2);
          this.details_elm
            .find("#gdpr_messagebar_detail_body_content")
            .css("border-color", GDPR.settings.border_color2);
          this.details_elm
            .find("#gdpr_messagebar_detail_body_content_tabs")
            .css("border-color", GDPR.settings.border_color2);
          this.details_elm
            .find(
              "#gdpr_messagebar_detail_body_content_tabs .gdpr_messagebar_detail_body_content_tab_item_selected"
            )
            .css("border-bottom-color", GDPR.settings.border_active_color2);
          this.details_elm
            .find(
              "#gdpr_messagebar_detail_body_content_tabs .gdpr_messagebar_detail_body_content_tab_item_selected"
            )
            .css("background-color", GDPR.settings.background_active_color2);

          this.credit_link.css(
            "color",
            this.settings.button_readmore_link_color
          );
          $(".gdpr-column .gdpr-columns.active-group").css(
            "background-color",
            GDPR.settings.button_accept_button_color2
          );
          $(
            ".gdpr_messagebar_detail .category-group .toggle-group .always-active"
          ).css("color", GDPR.settings.button_accept_button_color2);
        } else {
          this.settings_button.css(
            "color",
            this.settings.button_settings_link_color
          );
          if (this.settings.button_settings_as_button) {
            var settings_background = this.convertToHex(
              this.settings.button_settings_button_color,
              this.settings.button_settings_button_opacity
            );
            var settings_border =
              this.settings.button_settings_button_border_width +
              "px " +
              this.settings.button_settings_button_border_style +
              " " +
              this.settings.button_settings_button_border_color;
            this.settings_button.css("border", settings_border);
            this.settings_button.css(
              "border-radius",
              this.settings.button_settings_button_border_radius + "px"
            );
            this.settings_button.css("background-color", settings_background);
            this.settings_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_settings_button_hover
                );
              },
              function () {
                jQuery(this).css("background-color", settings_background);
              }
            );
          }

          if (template.includes("square")) {
            this.settings_button.css("width", "40%");
            this.settings_button.css("float", "right");
          } else if (template.includes("column")) {
            if ($("#cookie_action_reject").length === 0) {
              $(
                "#gdpr-cookie-consent-bar  > .gdpr_messagebar_content > #default_buttons"
              ).css({
                "width":"0"
              });
              $("#gdpr-cookie-consent-bar .group-description-buttons #cookie_action_settings").css({"margin-left":"0"});
            }
          } else if (template.includes("center")) {
            this.settings_button.css("margin-right", "0");
          } else if (template.includes("dark")) {
            this.settings_button.css("float", "right");
          } else {
            this.settings_button.css("float", "right");
            this.settings_button.css("margin-right", "0");
          }

          if (this.settings.button_accept_all_is_on) {
            if (template.includes("center")) {
              if (template.includes("popup") || template.includes("widget")) {
                $(
                  ".gdpr-popup > .gdpr_messagebar_content > .group-description-buttons > a"
                ).css({
                  "margin-bottom": "10px",
                });
                $(
                  ".gdpr-widget > .gdpr_messagebar_content > .group-description-buttons > a"
                ).css({
                  "margin-bottom": "10px",
                });
              }
            }
          
          }

          this.main_button.css("color", this.settings.button_accept_link_color);
          if (this.settings.button_accept_as_button) {
            var main_background = this.convertToHex(
              this.settings.button_accept_button_color,
              this.settings.button_accept_button_opacity
            );
            var main_border =
              this.settings.button_accept_button_border_width +
              "px " +
              this.settings.button_accept_button_border_style +
              " " +
              this.settings.button_accept_button_border_color;
            this.main_button.css("border", main_border);
            this.main_button.css(
              "border-radius",
              this.settings.button_accept_button_border_radius + "px"
            );
            this.main_button.css("background-color", main_background);
            this.main_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_accept_button_hover
                );
              },
              function () {
                jQuery(this).css("background-color", main_background);
              }
            );
          }

          if (template.includes("square")) {
            this.main_button.css("width", "100%");
            this.main_button.css("margin", "1rem auto 0 auto");
          }

          this.accept_all_button.css(
            "color",
            this.settings.button_accept_all_link_color
          );
          if (this.settings.button_accept_all_as_button) {
            var accept_all_background = this.convertToHex(
              this.settings.button_accept_all_button_color,
              this.settings.button_accept_all_btn_opacity
            );
            var main_border =
              this.settings.button_accept_all_btn_border_width +
              "px " +
              this.settings.button_accept_all_btn_border_style +
              " " +
              this.settings.button_accept_all_btn_border_color;
            this.accept_all_button.css("border", main_border);
            this.accept_all_button.css(
              "border-radius",
              this.settings.button_accept_all_btn_border_radius + "px"
            );
            this.accept_all_button.css(
              "background-color",
              accept_all_background
            );
            this.accept_all_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_accept_all_button_hover
                );
              },
              function () {
                jQuery(this).css("background-color", accept_all_background);
              }
            );
          }

          if (template.includes("square")) {
            this.accept_all_button.css("width", "100%");
            this.accept_all_button.css("margin", "1rem auto 0 auto");
          }

          this.confirm_button.css(
            "color",
            this.settings.button_confirm_link_color
          );
          if (this.settings.button_confirm_as_button) {
            var confirm_background = this.convertToHex(
              this.settings.button_confirm_button_color,
              this.settings.button_confirm_button_opacity
            );
            var confirm_border =
              this.settings.button_confirm_button_border_width +
              "px " +
              this.settings.button_confirm_button_border_style +
              " " +
              this.settings.button_confirm_button_border_color;
            this.confirm_button.css("border", confirm_border);
            this.confirm_button.css(
              "border-radius",
              this.settings.button_confirm_button_border_radius + "px"
            );
            this.confirm_button.css("background-color", confirm_background);
            this.confirm_button.css("width", "100%");
            this.confirm_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_confirm_button_hover
                );
              },
              function () {
                jQuery(this).css("background-color", confirm_background);
              }
            );
          }

          /* [wpl_cookie_link] */
          this.main_link.css("color", this.settings.button_readmore_link_color);
          this.vendor_link.css(
            "color",
            this.settings.button_readmore_link_color
          );
          if (this.settings.button_readmore_as_button) {
            var readmore_background = this.convertToHex(
              this.settings.button_readmore_button_color,
              this.settings.button_readmore_button_opacity
            );
            var readmore_border =
              this.settings.button_readmore_button_border_width +
              "px " +
              this.settings.button_readmore_button_border_style +
              " " +
              this.settings.button_readmore_button_border_color;
            this.main_link.css("border", readmore_border);
            this.main_link.css(
              "border-radius",
              this.settings.button_readmore_button_border_radius + "px"
            );
            this.main_link.css("background-color", readmore_background);
            this.main_link.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_readmore_button_hover
                );
              },
              function () {
                jQuery(this).css("background-color", readmore_background);
              }
            );
          }

          this.donotsell_link.css(
            "color",
            this.settings.button_donotsell_link_color
          );

          this.reject_button.css(
            "color",
            this.settings.button_decline_link_color
          );
          if (this.settings.button_decline_as_button) {
            var decline_background = this.convertToHex(
              this.settings.button_decline_button_color,
              this.settings.button_decline_button_opacity
            );
            var reject_border =
              this.settings.button_decline_button_border_width +
              "px " +
              this.settings.button_decline_button_border_style +
              " " +
              this.settings.button_decline_button_border_color;
            this.reject_button.css("border", reject_border);
            this.reject_button.css(
              "border-radius",
              this.settings.button_decline_button_border_radius + "px"
            );
            this.reject_button.css("background-color", decline_background);
            this.reject_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_decline_button_hover
                );
              },
              function () {
                jQuery(this).css("background-color", decline_background);
              }
            );
          }

          if (template.includes("square")) {
            this.reject_button.css("width", "40%");
          } else if (
            template.includes("dark")
          ) {
            this.reject_button.css("float", "right");
          }

          this.cancel_button.css(
            "color",
            this.settings.button_cancel_link_color
          );
          if (this.settings.button_cancel_as_button) {
            var cancel_background = this.convertToHex(
              this.settings.button_cancel_button_color,
              this.settings.button_cancel_button_opacity
            );
            var cancel_border =
              this.settings.button_cancel_button_border_width +
              "px " +
              this.settings.button_cancel_button_border_style +
              " " +
              this.settings.button_cancel_button_border_color;
            this.cancel_button.css("border", cancel_border);
            this.cancel_button.css(
              "border-radius",
              this.settings.button_cancel_button_border_radius + "px"
            );
            this.cancel_button.css("display", "inline-block");
            this.cancel_button.css("background-color", cancel_background);
            this.cancel_button.css("width", "100%");
            this.cancel_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_cancel_button_hover
                );
              },
              function () {
                jQuery(this).css("background-color", cancel_background);
              }
            );
          }

          this.save_button.css("color", this.settings.button_accept_link_color);
          this.save_button.css("background-color", main_background);
          this.save_button.css("border", main_border);
          this.save_button.css(
            "border-radius",
            this.settings.button_accept_button_border_radius + "px"
          );
          this.save_button.hover(
            function () {
              jQuery(this).css(
                "background-color",
                GDPR.settings.button_accept_button_hover
              );
            },
            function () {
              jQuery(this).css("background-color", main_background);
            }
          );
          this.details_elm
            .find(
              "table.gdpr_messagebar_detail_body_content_cookie_type_table tr"
            )
            .css("border-color", GDPR.settings.border_color);
          this.details_elm
            .find(".gdpr_messagebar_detail_body_content_cookie_type_intro")
            .css("border-color", GDPR.settings.border_color);
          this.details_elm.find("a").each(function () {
            jQuery(this).css("border-color", GDPR.settings.border_color);
            jQuery(this).css(
              "background-color",
              GDPR.settings.background_color
            );
          });
          this.details_elm
            .find(
              "a.gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
            )
            .css("border-right-color", GDPR.settings.border_active_color);
          this.details_elm
            .find(
              "a.gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
            )
            .css("background-color", GDPR.settings.background_active_color);
          this.details_elm
            .find("#gdpr_messagebar_detail_body_content")
            .css("border-color", GDPR.settings.border_color);
          this.details_elm
            .find("#gdpr_messagebar_detail_body_content_tabs")
            .css("border-color", GDPR.settings.border_color);
          this.details_elm
            .find(
              "#gdpr_messagebar_detail_body_content_tabs .gdpr_messagebar_detail_body_content_tab_item_selected"
            )
            .css("border-bottom-color", GDPR.settings.border_active_color);
          this.details_elm
            .find(
              "#gdpr_messagebar_detail_body_content_tabs .gdpr_messagebar_detail_body_content_tab_item_selected"
            )
            .css("background-color", GDPR.settings.background_active_color);

          this.credit_link.css(
            "color",
            this.settings.button_readmore_link_color
          );
          $(".gdpr-column .gdpr-columns.active-group").css(
            "background-color",
            GDPR.settings.button_accept_button_color
          );
          $(
            ".gdpr_messagebar_detail .category-group .toggle-group .always-active"
          ).css("color", GDPR.settings.button_accept_button_color);
        }
      } else {
        if (Number(chosenBanner) === 1) {
          this.settings_button.css(
            "color",
            this.settings.button_settings_link_color1
          );
          if (
            this.settings.button_settings_as_button1 === true ||
            this.settings.button_settings_as_button1 === "true"
          ) {
            var settings_background = this.convertToHex(
              this.settings.button_settings_button_color1,
              this.settings.button_settings_button_opacity1
            );
            var settings_border =
              this.settings.button_settings_button_border_width1 +
              "px " +
              this.settings.button_settings_button_border_style1 +
              " " +
              this.settings.button_settings_button_border_color1;
            this.settings_button.css("border", settings_border);
            this.settings_button.css(
              "border-radius",
              this.settings.button_settings_button_border_radius1 + "px"
            );
            this.settings_button.css("background-color", settings_background);
            this.settings_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_settings_button_hover1
                );
              },
              function () {
                jQuery(this).css("background-color", settings_background);
              }
            );
          }
          if (template.includes("square")) {
            this.settings_button.css("width", "40%");
            this.settings_button.css("float", "right");
          } else if ( template.includes("column")) {
            if ($("#cookie_action_reject").length === 0) {
              $(
                "#gdpr-cookie-consent-bar  > .gdpr_messagebar_content > #default_buttons"
              ).css({
                "width":"0"
              });
              $("#gdpr-cookie-consent-bar .group-description-buttons #cookie_action_settings").css({"margin-left":"0"});
            }
          } else if (template.includes("center")) {
            this.settings_button.css("margin-right", "0");
          } else if (template.includes("dark")) {
            this.settings_button.css("float", "right");
          } else {
            this.settings_button.css("float", "right");
            this.settings_button.css("margin-right", "0");
          }
          if (this.settings.button_accept_all_is_on1) {
            if (template.includes("center")) {
              if (template.includes("popup") || template.includes("widget")) {
                $(
                  ".gdpr-popup > .gdpr_messagebar_content > .group-description-buttons > a"
                ).css({
                  "margin-bottom": "10px",
                });
                $(
                  ".gdpr-widget > .gdpr_messagebar_content > .group-description-buttons > a"
                ).css({
                  "margin-bottom": "10px",
                });
              }
            }
            
          }
          this.main_button.css(
            "color",
            this.settings.button_accept_link_color1
          );
          if (
            this.settings.button_accept_as_button1 === true ||
            this.settings.button_accept_as_button1 === "true"
          ) {
            var main_background = this.convertToHex(
              this.settings.button_accept_button_color1,
              this.settings.button_accept_button_opacity1
            );
            var main_border =
              this.settings.button_accept_button_border_width1 +
              "px " +
              this.settings.button_accept_button_border_style1 +
              " " +
              this.settings.button_accept_button_border_color1;
            this.main_button.css("border", main_border);
            this.main_button.css(
              "border-radius",
              this.settings.button_accept_button_border_radius1 + "px"
            );
            this.main_button.css("background-color", main_background);
            this.main_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_accept_button_hover1
                );
              },
              function () {
                jQuery(this).css("background-color", main_background);
              }
            );
          }
          if (template.includes("square")) {
            this.main_button.css("width", "100%");
            this.main_button.css("margin", "1rem auto 0 auto");
          }
          this.accept_all_button.css(
            "color",
            this.settings.button_accept_all_link_color1
          );
          if (
            this.settings.button_accept_all_as_button1 === true ||
            this.settings.button_accept_all_as_button1 === "true"
          ) {
            var accept_all_background = this.convertToHex(
              this.settings.button_accept_all_button_color1,
              this.settings.button_accept_all_btn_opacity1
            );
            var main_border =
              this.settings.button_accept_all_btn_border_width1 +
              "px " +
              this.settings.button_accept_all_btn_border_style1 +
              " " +
              this.settings.button_accept_all_btn_border_color1;
            this.accept_all_button.css("border", main_border);
            this.accept_all_button.css(
              "border-radius",
              this.settings.button_accept_all_btn_border_radius1 + "px"
            );
            this.accept_all_button.css(
              "background-color",
              accept_all_background
            );
            this.accept_all_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_accept_all_button_hover1
                );
              },
              function () {
                jQuery(this).css("background-color", accept_all_background);
              }
            );
          }
          if (template.includes("square")) {
            this.accept_all_button.css("width", "100%");
            this.accept_all_button.css("margin", "1rem auto 0 auto");
          }
          this.confirm_button.css(
            "color",
            this.settings.button_confirm_link_color1
          );
          if (
            this.settings.button_confirm_as_button1 === true ||
            this.settings.button_confirm_as_button1 === "true"
          ) {
            var confirm_background = this.convertToHex(
              this.settings.button_confirm_button_color1,
              this.settings.button_confirm_button_opacity1
            );
            var confirm_border =
              this.settings.button_confirm_button_border_width1 +
              "px " +
              this.settings.button_confirm_button_border_style1 +
              " " +
              this.settings.button_confirm_button_border_color1;
            this.confirm_button.css("border", confirm_border);
            this.confirm_button.css(
              "border-radius",
              this.settings.button_confirm_button_border_radius1 + "px"
            );
            this.confirm_button.css("background-color", confirm_background);
            this.confirm_button.css("width", "100%");
            this.confirm_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_confirm_button_hover1
                );
              },
              function () {
                jQuery(this).css("background-color", confirm_background);
              }
            );
          }
          /* [wpl_cookie_link] */
          this.main_link.css("color", this.settings.button_readmore_link_color);
          this.vendor_link.css(
            "color",
            this.settings.button_readmore_link_color
          );
          if (this.settings.button_readmore_as_button) {
            var readmore_background = this.convertToHex(
              this.settings.button_readmore_button_color,
              this.settings.button_readmore_button_opacity
            );
            var readmore_border =
              this.settings.button_readmore_button_border_width +
              "px " +
              this.settings.button_readmore_button_border_style +
              " " +
              this.settings.button_readmore_button_border_color;
            this.main_link.css("border", readmore_border);
            this.main_link.css(
              "border-radius",
              this.settings.button_readmore_button_border_radius + "px"
            );
            this.main_link.css("background-color", readmore_background);
            this.main_link.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_readmore_button_hover
                );
              },
              function () {
                jQuery(this).css("background-color", readmore_background);
              }
            );
          }
          this.donotsell_link.css(
            "color",
            this.settings.button_donotsell_link_color1
          );
          this.reject_button.css(
            "color",
            this.settings.button_decline_link_color
          );
          if (
            this.settings.button_decline_as_button1 === true ||
            this.settings.button_decline_as_button1 === "true"
          ) {
            var decline_background = this.convertToHex(
              this.settings.button_decline_button_color1,
              this.settings.button_decline_button_opacity1
            );
            var reject_border =
              this.settings.button_decline_button_border_width1 +
              "px " +
              this.settings.button_decline_button_border_style1 +
              " " +
              this.settings.button_decline_button_border_color1;
            this.reject_button.css("border", reject_border);
            this.reject_button.css(
              "border-radius",
              this.settings.button_decline_button_border_radius1 + "px"
            );
            this.reject_button.css("background-color", decline_background);
            this.reject_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_decline_button_hover1
                );
              },
              function () {
                jQuery(this).css("background-color", decline_background);
              }
            );
          }
          if (template.includes("square")) {
            this.reject_button.css("width", "40%");
          } else if (
            template.includes("dark")
          ) {
            this.reject_button.css("float", "right");
          }
          this.cancel_button.css(
            "color",
            this.settings.button_cancel_link_color1
          );
          if (
            this.settings.button_cancel_as_button1 === true ||
            this.settings.button_cancel_as_button1 === "true"
          ) {
            var cancel_background = this.convertToHex(
              this.settings.button_cancel_button_color1,
              this.settings.button_cancel_button_opacity1
            );
            var cancel_border =
              this.settings.button_cancel_button_border_width1 +
              "px " +
              this.settings.button_cancel_button_border_style1 +
              " " +
              this.settings.button_cancel_button_border_color1;
            this.cancel_button.css("border", cancel_border);
            this.cancel_button.css(
              "border-radius",
              this.settings.button_cancel_button_border_radius1 + "px"
            );
            this.cancel_button.css("display", "inline-block");
            this.cancel_button.css("background-color", cancel_background);
            this.cancel_button.css("width", "100%");
            this.cancel_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_cancel_button_hover1
                );
              },
              function () {
                jQuery(this).css("background-color", cancel_background);
              }
            );
          }
          this.save_button.css(
            "color",
            this.settings.button_accept_link_color1
          );
          this.save_button.css("background-color", main_background);
          this.save_button.css("border", main_border);
          this.save_button.css(
            "border-radius",
            this.settings.button_accept_button_border_radius1 + "px"
          );
          this.save_button.hover(
            function () {
              jQuery(this).css(
                "background-color",
                GDPR.settings.button_accept_button_hover1
              );
            },
            function () {
              jQuery(this).css("background-color", main_background);
            }
          );
          this.details_elm
            .find(
              "table.gdpr_messagebar_detail_body_content_cookie_type_table tr"
            )
            .css("border-color", GDPR.settings.border_color1);
          this.details_elm
            .find(".gdpr_messagebar_detail_body_content_cookie_type_intro")
            .css("border-color", GDPR.settings.border_color1);
          this.details_elm.find("a").each(function () {
            jQuery(this).css("border-color", GDPR.settings.border_color1);
            jQuery(this).css(
              "background-color",
              GDPR.settings.background_color1
            );
          });
          this.details_elm
            .find(
              "a.gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
            )
            .css("border-right-color", GDPR.settings.border_active_color1);
          this.details_elm
            .find(
              "a.gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
            )
            .css("background-color", GDPR.settings.background_active_color1);
          this.details_elm
            .find("#gdpr_messagebar_detail_body_content")
            .css("border-color", GDPR.settings.border_color1);
          this.details_elm
            .find("#gdpr_messagebar_detail_body_content_tabs")
            .css("border-color", GDPR.settings.border_color1);
          this.details_elm
            .find(
              "#gdpr_messagebar_detail_body_content_tabs .gdpr_messagebar_detail_body_content_tab_item_selected"
            )
            .css("border-bottom-color", GDPR.settings.border_active_color1);
          this.details_elm
            .find(
              "#gdpr_messagebar_detail_body_content_tabs .gdpr_messagebar_detail_body_content_tab_item_selected"
            )
            .css("background-color", GDPR.settings.background_active_color1);
          this.credit_link.css(
            "color",
            this.settings.button_readmore_link_color
          );
          $(".gdpr-column .gdpr-columns.active-group").css(
            "background-color",
            GDPR.settings.button_accept_button_color1
          );
          $(
            ".gdpr_messagebar_detail .category-group .toggle-group .always-active"
          ).css("color", GDPR.settings.button_accept_button_color1);
        } else {
          this.settings_button.css(
            "color",
            this.settings.button_settings_link_color2
          );
          if (
            this.settings.button_settings_as_button2 === true ||
            this.settings.button_settings_as_button2 === "true"
          ) {
            var settings_background = this.convertToHex(
              this.settings.button_settings_button_color2,
              this.settings.button_settings_button_opacity2
            );
            var settings_border =
              this.settings.button_settings_button_border_width2 +
              "px " +
              this.settings.button_settings_button_border_style2 +
              " " +
              this.settings.button_settings_button_border_color2;
            this.settings_button.css("border", settings_border);
            this.settings_button.css(
              "border-radius",
              this.settings.button_settings_button_border_radius2 + "px"
            );
            this.settings_button.css("background-color", settings_background);
            this.settings_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_settings_button_hover2
                );
              },
              function () {
                jQuery(this).css("background-color", settings_background);
              }
            );
          }
          if (template.includes("square")) {
            this.settings_button.css("width", "40%");
            this.settings_button.css("float", "right");
          } else if (template.includes("column")) {
            if ($("#cookie_action_reject").length === 0) {
              $(
                "#gdpr-cookie-consent-bar  > .gdpr_messagebar_content > #default_buttons"
              ).css({
                "width":"0"
              });
              $("#gdpr-cookie-consent-bar .group-description-buttons #cookie_action_settings").css({"margin-left":"0"});
            }
          } else if (template.includes("center")) {
            this.settings_button.css("margin-right", "0");
          } else if (template.includes("dark")) {
            this.settings_button.css("float", "right");
          } else {
            this.settings_button.css("float", "right");
            this.settings_button.css("margin-right", "0");
          }
          if (this.settings.button_accept_all_is_on2) {
            if (template.includes("center")) {
              if (template.includes("popup") || template.includes("widget")) {
                $(
                  ".gdpr-popup > .gdpr_messagebar_content > .group-description-buttons > a"
                ).css({
                  "margin-bottom": "10px",
                });
                $(
                  ".gdpr-widget > .gdpr_messagebar_content > .group-description-buttons > a"
                ).css({
                  "margin-bottom": "10px",
                });
              }
            }
            
           
          }
          this.main_button.css(
            "color",
            this.settings.button_accept_link_color2
          );
          if (
            this.settings.button_accept_as_button2 === true ||
            this.settings.button_accept_as_button2 === "true"
          ) {
            var main_background = this.convertToHex(
              this.settings.button_accept_button_color2,
              this.settings.button_accept_button_opacity2
            );
            var main_border =
              this.settings.button_accept_button_border_width2 +
              "px " +
              this.settings.button_accept_button_border_style2 +
              " " +
              this.settings.button_accept_button_border_color2;
            this.main_button.css("border", main_border);
            this.main_button.css(
              "border-radius",
              this.settings.button_accept_button_border_radius2 + "px"
            );
            this.main_button.css("background-color", main_background);
            this.main_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_accept_button_hover2
                );
              },
              function () {
                jQuery(this).css("background-color", main_background);
              }
            );
          }
          if (template.includes("square")) {
            this.main_button.css("width", "100%");
            this.main_button.css("margin", "1rem auto 0 auto");
          }
          this.accept_all_button.css(
            "color",
            this.settings.button_accept_all_link_color2
          );
          if (
            this.settings.button_accept_all_as_button2 === true ||
            this.settings.button_accept_all_as_button2 === "true"
          ) {
            var accept_all_background = this.convertToHex(
              this.settings.button_accept_all_button_color2,
              this.settings.button_accept_all_btn_opacity2
            );
            var main_border =
              this.settings.button_accept_all_btn_border_width2 +
              "px " +
              this.settings.button_accept_all_btn_border_style2 +
              " " +
              this.settings.button_accept_all_btn_border_color2;
            this.accept_all_button.css("border", main_border);
            this.accept_all_button.css(
              "border-radius",
              this.settings.button_accept_all_btn_border_radius2 + "px"
            );
            this.accept_all_button.css(
              "background-color",
              accept_all_background
            );
            this.accept_all_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_accept_all_button_hover2
                );
              },
              function () {
                jQuery(this).css("background-color", accept_all_background);
              }
            );
          }
          if (template.includes("square")) {
            this.accept_all_button.css("width", "100%");
            this.accept_all_button.css("margin", "1rem auto 0 auto");
          }
          this.confirm_button.css(
            "color",
            this.settings.button_confirm_link_color2
          );
          if (
            this.settings.button_confirm_as_button2 === true ||
            this.settings.button_confirm_as_button2 === "true"
          ) {
            var confirm_background = this.convertToHex(
              this.settings.button_confirm_button_color2,
              this.settings.button_confirm_button_opacity2
            );
            var confirm_border =
              this.settings.button_confirm_button_border_width2 +
              "px " +
              this.settings.button_confirm_button_border_style2 +
              " " +
              this.settings.button_confirm_button_border_color2;
            this.confirm_button.css("border", confirm_border);
            this.confirm_button.css(
              "border-radius",
              this.settings.button_confirm_button_border_radius2 + "px"
            );
            this.confirm_button.css("background-color", confirm_background);
            this.confirm_button.css("width", "100%");
            this.confirm_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_confirm_button_hover2
                );
              },
              function () {
                jQuery(this).css("background-color", confirm_background);
              }
            );
          }
          /* [wpl_cookie_link] */
          this.main_link.css("color", this.settings.button_readmore_link_color);
          this.vendor_link.css(
            "color",
            this.settings.button_readmore_link_color
          );
          if (this.settings.button_readmore_as_button) {
            var readmore_background = this.convertToHex(
              this.settings.button_readmore_button_color,
              this.settings.button_readmore_button_opacity
            );
            var readmore_border =
              this.settings.button_readmore_button_border_width +
              "px " +
              this.settings.button_readmore_button_border_style +
              " " +
              this.settings.button_readmore_button_border_color;
            this.main_link.css("border", readmore_border);
            this.main_link.css(
              "border-radius",
              this.settings.button_readmore_button_border_radius + "px"
            );
            this.main_link.css("background-color", readmore_background);
            this.main_link.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_readmore_button_hover
                );
              },
              function () {
                jQuery(this).css("background-color", readmore_background);
              }
            );
          }
          this.donotsell_link.css(
            "color",
            this.settings.button_donotsell_link_color2
          );
          this.reject_button.css(
            "color",
            this.settings.button_decline_link_color
          );
          if (
            this.settings.button_decline_as_button2 === true ||
            this.settings.button_decline_as_button2 === "true"
          ) {
            var decline_background = this.convertToHex(
              this.settings.button_decline_button_color2,
              this.settings.button_decline_button_opacity2
            );
            var reject_border =
              this.settings.button_decline_button_border_width2 +
              "px " +
              this.settings.button_decline_button_border_style2 +
              " " +
              this.settings.button_decline_button_border_color2;
            this.reject_button.css("border", reject_border);
            this.reject_button.css(
              "border-radius",
              this.settings.button_decline_button_border_radius2 + "px"
            );
            this.reject_button.css("background-color", decline_background);
            this.reject_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_decline_button_hover2
                );
              },
              function () {
                jQuery(this).css("background-color", decline_background);
              }
            );
          }
          if (template.includes("square")) {
            this.reject_button.css("width", "40%");
          } else if (
            template.includes("dark")
          ) {
            this.reject_button.css("float", "right");
          }
          this.cancel_button.css(
            "color",
            this.settings.button_cancel_link_color2
          );
          if (
            this.settings.button_cancel_as_button2 === true ||
            this.settings.button_cancel_as_button2 === "true"
          ) {
            var cancel_background = this.convertToHex(
              this.settings.button_cancel_button_color2,
              this.settings.button_cancel_button_opacity2
            );
            var cancel_border =
              this.settings.button_cancel_button_border_width2 +
              "px " +
              this.settings.button_cancel_button_border_style2 +
              " " +
              this.settings.button_cancel_button_border_color2;
            this.cancel_button.css("border", cancel_border);
            this.cancel_button.css(
              "border-radius",
              this.settings.button_cancel_button_border_radius2 + "px"
            );
            this.cancel_button.css("display", "inline-block");
            this.cancel_button.css("background-color", cancel_background);
            this.cancel_button.css("width", "100%");
            this.cancel_button.hover(
              function () {
                jQuery(this).css(
                  "background-color",
                  GDPR.settings.button_cancel_button_hover2
                );
              },
              function () {
                jQuery(this).css("background-color", cancel_background);
              }
            );
          }
          this.save_button.css(
            "color",
            this.settings.button_accept_link_color2
          );
          this.save_button.css("background-color", main_background);
          this.save_button.css("border", main_border);
          this.save_button.css(
            "border-radius",
            this.settings.button_accept_button_border_radius2 + "px"
          );
          this.save_button.hover(
            function () {
              jQuery(this).css(
                "background-color",
                GDPR.settings.button_accept_button_hover2
              );
            },
            function () {
              jQuery(this).css("background-color", main_background);
            }
          );
          this.details_elm
            .find(
              "table.gdpr_messagebar_detail_body_content_cookie_type_table tr"
            )
            .css("border-color", GDPR.settings.border_color2);
          this.details_elm
            .find(".gdpr_messagebar_detail_body_content_cookie_type_intro")
            .css("border-color", GDPR.settings.border_color2);
          this.details_elm.find("a").each(function () {
            jQuery(this).css("border-color", GDPR.settings.border_color2);
            jQuery(this).css(
              "background-color",
              GDPR.settings.background_color2
            );
          });
          this.details_elm
            .find(
              "a.gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
            )
            .css("border-right-color", GDPR.settings.border_active_color2);
          this.details_elm
            .find(
              "a.gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected"
            )
            .css("background-color", GDPR.settings.background_active_color2);
          this.details_elm
            .find("#gdpr_messagebar_detail_body_content")
            .css("border-color", GDPR.settings.border_color2);
          this.details_elm
            .find("#gdpr_messagebar_detail_body_content_tabs")
            .css("border-color", GDPR.settings.border_color2);
          this.details_elm
            .find(
              "#gdpr_messagebar_detail_body_content_tabs .gdpr_messagebar_detail_body_content_tab_item_selected"
            )
            .css("border-bottom-color", GDPR.settings.border_active_color2);
          this.details_elm
            .find(
              "#gdpr_messagebar_detail_body_content_tabs .gdpr_messagebar_detail_body_content_tab_item_selected"
            )
            .css("background-color", GDPR.settings.background_active_color2);
          this.credit_link.css(
            "color",
            this.settings.button_readmore_link_color
          );
          $(".gdpr-column .gdpr-columns.active-group").css(
            "background-color",
            GDPR.settings.button_accept_button_color2
          );
          $(
            ".gdpr_messagebar_detail .category-group .toggle-group .always-active"
          ).css("color", GDPR.settings.button_accept_button_color2);
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
    configBar: function () {
      if (
        gdpr_ab_options.ab_testing_enabled === "false" ||
        gdpr_ab_options.ab_testing_enabled === false
      ) {
        if (
          this.settings.cookie_usage_for == "both" &&
          multiple_legislation_current_banner == "gdpr"
        ) {
          var background = this.convertToHex(
            this.settings.multiple_legislation_cookie_bar_color1,
            this.settings.multiple_legislation_cookie_bar_opacity1
          );
          var border =
            this.settings.multiple_legislation_cookie_bar_border_width1 +
            "px " +
            this.settings.multiple_legislation_border_style1 +
            " " +
            this.settings.multiple_legislation_cookie_border_color1;
          this.bar_config = {
            "background-color": background,
            color: this.settings.multiple_legislation_cookie_text_color1,
            "font-family": this.settings.multiple_legislation_cookie_font1,
            "box-shadow": this.settings.background + " 0 0 8px",
            border: border,
            "border-radius":
              this.settings.multiple_legislation_cookie_bar_border_radius1 +
              "px",
          };
          this.show_config = {
            width: "auto",
            "background-color": background,
            "box-shadow": this.settings.background + " 0 0 8px",
            color: this.settings.text,
            "font-family": this.settings.font_family,
            position: "fixed",
            bottom: "0",
            border: border,
            "border-radius": this.settings.background_border_radius + "px",
          };
          var template = this.settings.template;
          if (template.includes("row") || template.includes("center")) {
            this.bar_config["text-align"] = "center";
          } else {
            this.bar_config["text-align"] = "justify";
          }
          if (this.settings.show_again_position == "right") {
            this.show_config["right"] = this.settings.show_again_margin + "%";
          } else {
            this.show_config["left"] = this.settings.show_again_margin + "%";
          }
          this.bar_config["position"] = "fixed";
          if (this.settings.cookie_bar_as == "banner") {
            this.bar_elm
              .find(".gdpr_messagebar_content")
              .css("max-width", "800px");
            if (this.settings.notify_position_vertical == "bottom") {
              this.bar_config["bottom"] = "0";
            } else {
              this.bar_config["top"] = "0";
            }
          }
          if (this.settings.cookie_bar_as == "widget") {
            this.bar_config["width"] = "35%";
            if (this.settings.notify_position_horizontal == "left") {
              this.bar_config["bottom"] = "20px";
              this.bar_config["left"] = "20px";
            } else if (this.settings.notify_position_horizontal == "right") {
              this.bar_config["bottom"] = "20px";
              this.bar_config["right"] = "20px";
            } else if (
              this.settings.notify_position_horizontal == "top_right"
            ) {
              this.bar_config["top"] = "20px";
              this.bar_config["right"] = "20px";
            } else if (this.settings.notify_position_horizontal == "top_left") {
              this.bar_config["top"] = "20px";
              this.bar_config["left"] = "20px";
            }
          }
          if (this.settings.cookie_bar_as == "popup") {
            this.bar_config["border"] = "unset";
            this.bar_config["border-radius"] = "unset";
            this.bar_config["position"] = "unset";
            this.bar_config["box-shadow"] = "unset";
            this.bar_config["background-color"] = "unset";
            jQuery("#gdpr-popup .gdprmodal-content").css(
              "background-color",
              background
            );
            jQuery("#gdpr-popup .gdprmodal-content").css("border", border);
            jQuery("#gdpr-popup .gdprmodal-content").css(
              "border-radius",
              this.settings.background_border_radius + "px"
            );
            jQuery("#gdpr-popup .gdprmodal-content").css(
              "box-shadow",
              this.settings.background + " 0 0 8px"
            );
          }
          this.bar_elm.css(this.bar_config).hide();
          this.show_again_elm.css(this.show_config).hide();
        } else if (
          this.settings.cookie_usage_for == "both" &&
          multiple_legislation_current_banner == "ccpa"
        ) {
          var background = this.convertToHex(
            this.settings.background,
            this.settings.opacity
          );
          var border =
            this.settings.background_border_width +
            "px " +
            this.settings.background_border_style +
            " " +
            this.settings.background_border_color;
          this.bar_config = {
            "background-color": background,
            color: this.settings.text,
            "font-family": this.settings.font_family,
            "box-shadow": this.settings.background + " 0 0 8px",
            border: border,
            "border-radius": this.settings.background_border_radius + "px",
          };
          this.show_config = {
            width: "auto",
            "background-color": background,
            "box-shadow": this.settings.background + " 0 0 8px",
            color: this.settings.text,
            "font-family": this.settings.font_family,
            position: "fixed",
            bottom: "0",
            border: border,
            "border-radius": this.settings.background_border_radius + "px",
          };
          var template = this.settings.template;
          if (template.includes("row") || template.includes("center")) {
            this.bar_config["text-align"] = "center";
          } else {
            this.bar_config["text-align"] = "justify";
          }
          if (this.settings.show_again_position == "right") {
            this.show_config["right"] = this.settings.show_again_margin + "%";
          } else {
            this.show_config["left"] = this.settings.show_again_margin + "%";
          }
          this.bar_config["position"] = "fixed";
          if (this.settings.cookie_bar_as == "banner") {
            this.bar_elm
              .find(".gdpr_messagebar_content")
              .css("max-width", "800px");
            if (this.settings.notify_position_vertical == "bottom") {
              this.bar_config["bottom"] = "0";
            } else {
              this.bar_config["top"] = "0";
            }
          }
          if (this.settings.cookie_bar_as == "widget") {
            this.bar_config["width"] = "35%";
            if (this.settings.notify_position_horizontal == "left") {
              this.bar_config["bottom"] = "20px";
              this.bar_config["left"] = "20px";
            } else if (this.settings.notify_position_horizontal == "right") {
              this.bar_config["bottom"] = "20px";
              this.bar_config["right"] = "20px";
            } else if (
              this.settings.notify_position_horizontal == "top_right"
            ) {
              this.bar_config["top"] = "20px";
              this.bar_config["right"] = "20px";
            } else if (this.settings.notify_position_horizontal == "top_left") {
              this.bar_config["top"] = "20px";
              this.bar_config["left"] = "20px";
            }
          }
          if (this.settings.cookie_bar_as == "popup") {
            this.bar_config["border"] = "unset";
            this.bar_config["border-radius"] = "unset";
            this.bar_config["position"] = "unset";
            this.bar_config["box-shadow"] = "unset";
            this.bar_config["background-color"] = "unset";
            jQuery("#gdpr-popup .gdprmodal-content").css(
              "background-color",
              background
            );
            jQuery("#gdpr-popup .gdprmodal-content").css("border", border);
            jQuery("#gdpr-popup .gdprmodal-content").css(
              "border-radius",
              this.settings.background_border_radius + "px"
            );
            jQuery("#gdpr-popup .gdprmodal-content").css(
              "box-shadow",
              this.settings.background + " 0 0 8px"
            );
          }
          this.bar_elm.css(this.bar_config).hide();
          this.show_again_elm.css(this.show_config).hide();
        } else {
          var background = this.convertToHex(
            this.settings.background,
            this.settings.opacity
          );
          var border =
            this.settings.background_border_width +
            "px " +
            this.settings.background_border_style +
            " " +
            this.settings.background_border_color;
          this.bar_config = {
            "background-color": background,
            color: this.settings.text,
            "font-family": this.settings.font_family,
            "box-shadow": this.settings.background + " 0 0 8px",
            border: border,
            "border-radius": this.settings.background_border_radius + "px",
          };
          this.show_config = {
            width: "auto",
            "background-color": background,
            "box-shadow": this.settings.background + " 0 0 8px",
            color: this.settings.text,
            "font-family": this.settings.font_family,
            position: "fixed",
            bottom: "0",
            border: border,
            "border-radius": this.settings.background_border_radius + "px",
          };
          var template = this.settings.template;
          if (template.includes("row") || template.includes("center")) {
            this.bar_config["text-align"] = "center";
          } else {
            this.bar_config["text-align"] = "justify";
          }
          if (this.settings.show_again_position == "right") {
            this.show_config["right"] = this.settings.show_again_margin + "%";
          } else {
            this.show_config["left"] = this.settings.show_again_margin + "%";
          }
          this.bar_config["position"] = "fixed";
          if (this.settings.cookie_bar_as == "banner") {
            this.bar_elm
              .find(".gdpr_messagebar_content")
              .css("max-width", "800px");
            if (this.settings.notify_position_vertical == "bottom") {
              this.bar_config["bottom"] = "0";
            } else {
              this.bar_config["top"] = "0";
            }
          }
          if (this.settings.cookie_bar_as == "widget") {
            this.bar_config["width"] = "35%";
            if (this.settings.notify_position_horizontal == "left") {
              this.bar_config["bottom"] = "20px";
              this.bar_config["left"] = "20px";
            } else if (this.settings.notify_position_horizontal == "right") {
              this.bar_config["bottom"] = "20px";
              this.bar_config["right"] = "20px";
            } else if (
              this.settings.notify_position_horizontal == "top_right"
            ) {
              this.bar_config["top"] = "20px";
              this.bar_config["right"] = "20px";
            } else if (this.settings.notify_position_horizontal == "top_left") {
              this.bar_config["top"] = "20px";
              this.bar_config["left"] = "20px";
            }
          }
          if (this.settings.cookie_bar_as == "popup") {
            this.bar_config["border"] = "unset";
            this.bar_config["border-radius"] = "unset";
            this.bar_config["position"] = "unset";
            this.bar_config["box-shadow"] = "unset";
            this.bar_config["background-color"] = "unset";
            jQuery("#gdpr-popup .gdprmodal-content").css(
              "background-color",
              background
            );
            jQuery("#gdpr-popup .gdprmodal-content").css("border", border);
            jQuery("#gdpr-popup .gdprmodal-content").css(
              "border-radius",
              this.settings.background_border_radius + "px"
            );
            jQuery("#gdpr-popup .gdprmodal-content").css(
              "box-shadow",
              this.settings.background + " 0 0 8px"
            );
          }
          this.bar_elm.css(this.bar_config).hide();
          this.show_again_elm.css(this.show_config).hide();
        }
      } else {
        if (Number(chosenBanner) === 1) {
          var background = this.convertToHex(
            this.settings.background1,
            this.settings.opacity1
          );
          var border =
            this.settings.background_border_width1 +
            "px " +
            this.settings.background_border_style1 +
            " " +
            this.settings.background_border_color1;
          this.bar_config = {
            "background-color": background,
            color: this.settings.text1,
            "font-family": this.settings.font_family1,
            "box-shadow": this.settings.background + " 0 0 8px",
            border: border,
            "border-radius": this.settings.background_border_radius1 + "px",
          };
          this.show_config = {
            width: "auto",
            "background-color": background,
            "box-shadow": this.settings.background + " 0 0 8px",
            color: this.settings.text1,
            "font-family": this.settings.font_family1,
            position: "fixed",
            bottom: "0",
            border: border,
            "border-radius": this.settings.background_border_radius1 + "px",
          };

          var template = this.settings.template;
          if (template.includes("row") || template.includes("center")) {
            this.bar_config["text-align"] = "center";
          } else {
            this.bar_config["text-align"] = "justify";
          }

          if (this.settings.show_again_position == "right") {
            this.show_config["right"] = this.settings.show_again_margin + "%";
          } else {
            this.show_config["left"] = this.settings.show_again_margin + "%";
          }
          this.bar_config["position"] = "fixed";
          if (this.settings.cookie_bar_as == "banner") {
            this.bar_elm
              .find(".gdpr_messagebar_content")
              .css("max-width", "800px");
            if (this.settings.notify_position_vertical == "bottom") {
              this.bar_config["bottom"] = "0";
            } else {
              this.bar_config["top"] = "0";
            }
          }
          if (this.settings.cookie_bar_as == "widget") {
            this.bar_config["width"] = "35%";
            if (this.settings.notify_position_horizontal == "left") {
              this.bar_config["bottom"] = "20px";
              this.bar_config["left"] = "20px";
            } else if (this.settings.notify_position_horizontal == "right") {
              this.bar_config["bottom"] = "20px";
              this.bar_config["right"] = "20px";
            } else if (
              this.settings.notify_position_horizontal == "top_right"
            ) {
              this.bar_config["top"] = "20px";
              this.bar_config["right"] = "20px";
            } else if (this.settings.notify_position_horizontal == "top_left") {
              this.bar_config["top"] = "20px";
              this.bar_config["left"] = "20px";
            }
          }
          if (this.settings.cookie_bar_as == "popup") {
            this.bar_config["border"] = "unset";
            this.bar_config["border-radius"] = "unset";
            this.bar_config["position"] = "unset";
            this.bar_config["box-shadow"] = "unset";
            this.bar_config["background-color"] = "unset";
            jQuery("#gdpr-popup .gdprmodal-content").css(
              "background-color",
              background
            );
            jQuery("#gdpr-popup .gdprmodal-content").css("border", border);
            jQuery("#gdpr-popup .gdprmodal-content").css(
              "border-radius",
              this.settings.background_border_radius1 + "px"
            );
            jQuery("#gdpr-popup .gdprmodal-content").css(
              "box-shadow",
              this.settings.background + " 0 0 8px"
            );
          }

          this.bar_elm.css(this.bar_config).hide();
          this.show_again_elm.css(this.show_config).hide();
        } else {
          var background = this.convertToHex(
            this.settings.background2,
            this.settings.opacity2
          );
          var border =
            this.settings.background_border_width2 +
            "px " +
            this.settings.background_border_style2 +
            " " +
            this.settings.background_border_color2;

          this.bar_config = {
            "background-color": background,
            color: this.settings.text2,
            "font-family": this.settings.font_family2,
            "box-shadow": this.settings.background + " 0 0 8px",
            border: border,
            "border-radius": this.settings.background_border_radius2 + "px",
          };
          this.show_config = {
            width: "auto",
            "background-color": background,
            "box-shadow": this.settings.background + " 0 0 8px",
            color: this.settings.text2,
            "font-family": this.settings.font_family2,
            position: "fixed",
            bottom: "0",
            border: border,
            "border-radius": this.settings.background_border_radius2 + "px",
          };

          var template = this.settings.template;
          if (template.includes("row") || template.includes("center")) {
            this.bar_config["text-align"] = "center";
          } else {
            this.bar_config["text-align"] = "justify";
          }

          if (this.settings.show_again_position == "right") {
            this.show_config["right"] = this.settings.show_again_margin + "%";
          } else {
            this.show_config["left"] = this.settings.show_again_margin + "%";
          }
          this.bar_config["position"] = "fixed";
          if (this.settings.cookie_bar_as == "banner") {
            this.bar_elm
              .find(".gdpr_messagebar_content")
              .css("max-width", "800px");
            if (this.settings.notify_position_vertical == "bottom") {
              this.bar_config["bottom"] = "0";
            } else {
              this.bar_config["top"] = "0";
            }
          }
          if (this.settings.cookie_bar_as == "widget") {
            this.bar_config["width"] = "35%";
            if (this.settings.notify_position_horizontal == "left") {
              this.bar_config["bottom"] = "20px";
              this.bar_config["left"] = "20px";
            } else if (this.settings.notify_position_horizontal == "right") {
              this.bar_config["bottom"] = "20px";
              this.bar_config["right"] = "20px";
            } else if (
              this.settings.notify_position_horizontal == "top_right"
            ) {
              this.bar_config["top"] = "20px";
              this.bar_config["right"] = "20px";
            } else if (this.settings.notify_position_horizontal == "top_left") {
              this.bar_config["top"] = "20px";
              this.bar_config["left"] = "20px";
            }
          }
          if (this.settings.cookie_bar_as == "popup") {
            this.bar_config["border"] = "unset";
            this.bar_config["border-radius"] = "unset";
            this.bar_config["position"] = "unset";
            this.bar_config["box-shadow"] = "unset";
            this.bar_config["background-color"] = "unset";
            jQuery("#gdpr-popup .gdprmodal-content").css(
              "background-color",
              background
            );
            jQuery("#gdpr-popup .gdprmodal-content").css("border", border);
            jQuery("#gdpr-popup .gdprmodal-content").css(
              "border-radius",
              this.settings.background_border_radius2 + "px"
            );
            jQuery("#gdpr-popup .gdprmodal-content").css(
              "box-shadow",
              this.settings.background + " 0 0 8px"
            );
          }

          this.bar_elm.css(this.bar_config).hide();
          this.show_again_elm.css(this.show_config).hide();
        }
      }
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
    show_details: function () {
      this.details_elm.show();
      this.bar_elm.css("opacity", 1);
      this.details_elm.css("border-top-color", GDPR.settings.border_color);
      this.settings_button.attr("data-gdpr_action", "hide_settings");
      jQuery("#gdpr_messagebar_detail_body_content_about").hide();
    },
    hide_details: function () {
      this.details_elm.hide();
      this.bar_elm.css("opacity", GDPR.settings.opacity);
      this.settings_button.attr("data-gdpr_action", "show_settings");
    },
    displayHeader: function (
      gdpr_flag,
      ccpa_flag,
      lgpd_flag,
      force_display_bar,
      force_display_show_again
    ) {
      if (!gdpr_flag || !ccpa_flag || !lgpd_flag) {
        var animate_on_load = GDPR.settings.notify_animate_show;
        var self = this;
        if (force_display_bar || animate_on_load) {
          if (this.settings.auto_banner_initialize) {
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
        jQuery(GDPR.settings.notify_div_id)
          .find(".gdpr.group-description-buttons")
          .hide();

        if (this.settings.auto_banner_initialize) {
          var banner_delay = this.settings.auto_banner_initialize_delay;
          setTimeout(function () {
            jQuery(GDPR.settings.notify_div_id).find("p.ccpa").show();
          }, banner_delay);
        } else {
          multiple_legislation_current_banner = "ccpa";
          if (
            this.settings.cookie_usage_for == "both" &&
            multiple_legislation_current_banner == "ccpa"
          ) {
            var background = this.convertToHex(
              this.settings.multiple_legislation_cookie_bar_color2,
              this.settings.multiple_legislation_cookie_bar_opacity2
            );
            var border =
              this.settings.multiple_legislation_cookie_bar_border_width2 +
              "px " +
              this.settings.multiple_legislation_border_style2 +
              " " +
              this.settings.multiple_legislation_cookie_border_color2;

            this.bar_config = {
              "background-color": background,
              color: this.settings.multiple_legislation_cookie_text_color2,
              "font-family": this.settings.multiple_legislation_cookie_font2,
              "box-shadow": this.settings.background + " 0 0 8px",
              border: border,
              "border-radius":
                this.settings.multiple_legislation_cookie_bar_border_radius2 +
                "px",
            };
            this.show_config = {
              width: "auto",
              "background-color": background,
              "box-shadow": this.settings.background + " 0 0 8px",
              color: this.settings.text,
              "font-family": this.settings.font_family,
              position: "fixed",
              bottom: "0",
              border: border,
              "border-radius": this.settings.background_border_radius + "px",
            };

            var template = this.settings.template;
            if (template.includes("row") || template.includes("center")) {
              this.bar_config["text-align"] = "center";
            } else {
              this.bar_config["text-align"] = "justify";
            }

            if (this.settings.show_again_position == "right") {
              this.show_config["right"] = this.settings.show_again_margin + "%";
            } else {
              this.show_config["left"] = this.settings.show_again_margin + "%";
            }
            this.bar_config["position"] = "fixed";
            if (this.settings.cookie_bar_as == "banner") {
              this.bar_elm
                .find(".gdpr_messagebar_content")
                .css("max-width", "800px");
              if (this.settings.notify_position_vertical == "bottom") {
                this.bar_config["bottom"] = "0";
              } else {
                this.bar_config["top"] = "0";
              }
            }
            if (this.settings.cookie_bar_as == "widget") {
              this.bar_config["width"] = "35%";
              if (this.settings.notify_position_horizontal == "left") {
                this.bar_config["bottom"] = "20px";
                this.bar_config["left"] = "20px";
              } else if (this.settings.notify_position_horizontal == "right") {
                this.bar_config["bottom"] = "20px";
                this.bar_config["right"] = "20px";
              } else if (
                this.settings.notify_position_horizontal == "top_right"
              ) {
                this.bar_config["top"] = "20px";
                this.bar_config["right"] = "20px";
              } else if (
                this.settings.notify_position_horizontal == "top_left"
              ) {
                this.bar_config["top"] = "20px";
                this.bar_config["left"] = "20px";
              }
            }
            if (this.settings.cookie_bar_as == "popup") {
              this.bar_config["border"] = "unset";
              this.bar_config["border-radius"] = "unset";
              this.bar_config["position"] = "unset";
              this.bar_config["box-shadow"] = "unset";
              this.bar_config["background-color"] = "unset";
              jQuery("#gdpr-popup .gdprmodal-content").css(
                "background-color",
                background
              );
              jQuery("#gdpr-popup .gdprmodal-content").css("border", border);
              jQuery("#gdpr-popup .gdprmodal-content").css(
                "border-radius",
                this.settings.background_border_radius + "px"
              );
              jQuery("#gdpr-popup .gdprmodal-content").css(
                "box-shadow",
                this.settings.background + " 0 0 8px"
              );
            }
            // this.bar_elm.css(this.bar_config).hide();
            // this.show_again_elm.css(this.show_config).hide();
          }
          jQuery(GDPR.settings.notify_div_id).find("p.ccpa").show();
        }
      }
      if (lgpd_flag) {
        jQuery(GDPR.settings.notify_div_id).find("p.gdpr").hide();
        jQuery(GDPR.settings.notify_div_id)
          .find(".gdpr.group-description-buttons")
          .hide();
        // jQuery( GDPR.settings.notify_div_id ).find( 'p.ccpa' ).show();
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
    $(".gdpr_messagebar_detail .category-group .category-item hr").css(
      "border-top",
      "1px solid " + GDPR.settings.button_accept_button_color
    );
    $(".gdpr_messagebar_detail.dark_row .category-group .category-item hr").css(
      "border-top",
      "1px solid #73DBC0"
    );
    $(
      ".gdpr_messagebar_detail .gdpr-iab-navbar .gdpr-iab-navbar-button.active"
    ).css("color", GDPR.settings.button_accept_button_color);
    $(
      ".gdpr_messagebar_detail.layout-classic .gdpr-iab-navbar .gdpr-iab-navbar-button.active"
    ).css(
      "border-bottom",
      "2px solid " + GDPR.settings.button_accept_button_color
    );
    $(".gdpr_messagebar_detail.layout-default  .category-group").css(
      "background-color",
      background_obj.background
    );
    if (is_iab_on) {
      $(".gdpr_messagebar_detail.layout-default  .category-group.outer").css(
        "border-left",
        "1px solid " + GDPR.settings.button_accept_button_color
      );
    }
    $(
      ".gdpr_messagebar_detail.layout-default.dark_row  .category-group.outer"
    ).css("border-left", "1px solid #73DBC0");
    // GDPR.settings.button_accept_button_color = "#00f";
    $(".gdpr-iab-navbar-item").click(function () {
      $(
        ".gdpr_messagebar_detail .gdpr-iab-navbar .gdpr-iab-navbar-button.active"
      ).css("color", GDPR.settings.button_accept_button_color);
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
        // $( ".gdpr-iab-navbar-button" ).css( 'color', '#000' );
        $(".gdpr-iab-navbar-button").css("border-bottom", "none");
        $(this).children(".gdpr-iab-navbar-button").addClass("active");
        $(".gdpr-iab-navbar-button").css("color", "inherit");
        $(this)
          .children(".gdpr-iab-navbar-button.active")
          .css("color", GDPR.settings.button_accept_button_color);
        $(this)
          .children(".gdpr-iab-navbar-button.active")
          .css("color", GDPR.settings.button_accept_button_color);
        $(this)
          .children(".gdpr-iab-navbar-button.active")
          .css(
            "border-bottom",
            "2px solid " + GDPR.settings.button_accept_button_color
          );
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
        $(this)
          .children(".gdpr-columns")
          .css("background-color", GDPR.settings.button_accept_button_color);
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
        $(".gdpr-columns .dashicons").removeClass("dashicons-arrow-up-alt2");
        $(".gdpr-columns .dashicons").addClass("dashicons-arrow-down-alt2");
        $(this).children(".gdpr-columns").addClass("active-group");
        $(".toggle-group")
          .find("div.always-active")
          .css("color", GDPR.settings.button_accept_button_color);
        $(this)
          .siblings(".toggle-group")
          .find("div.always-active")
          .css("color", GDPR.settings.button_accept_button_color);
        // $(this)
        //   .children(".gdpr-columns")
        //   .css("background-color", background_obj.background);
        $(this)
          .children(".gdpr-columns")
          .find(".dashicons")
          .removeClass("dashicons-arrow-down-alt2");
        $(this)
          .children(".gdpr-columns")
          .find(".dashicons")
          .addClass("dashicons-arrow-up-alt2");
        // $( this ).children( ".gdpr-columns" ).find( ".btn.category-header" ).css( "color", button_revoke_consent_text_color );
      } else {
        $(".gdpr-columns").removeClass("active-group");
        $(this)
          .siblings(".toggle-group")
          .find("div.always-active")
          .css("color", GDPR.settings.button_accept_button_color);
        // $(".gdpr-columns").css("background-color", background_obj.background);
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
      var heightOfB = $(this).outerHeight() - 23;
      $(this)
        .siblings(".toggle-group")
        .attr("style", "top: " + heightOfB + "px !important");
      $(".gdpr-category-toggle.inner-gdpr-column", this);
      if (!$(this).children(".inner-gdpr-columns").hasClass("active-group")) {
        $(".inner-gdpr-columns").removeClass("active-group");
        $(".inner-gdpr-columns").css(
          "background-color",
          background_obj.background
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
          .css("color", GDPR.settings.button_accept_button_color);
        $(this)
          .siblings(".toggle-group")
          .find("div.always-active")
          .css("color", GDPR.settings.button_accept_button_color);
        $(this)
          .children(".inner-gdpr-columns")
          .css("background-color", background_obj.background);
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
          .css("color", GDPR.settings.button_accept_button_color);
        $(".inner-gdpr-columns").css(
          "background-color",
          background_obj.background
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
          background_obj.background
        );
        $(this).children(".inner-gdpr-columns").addClass("active-group");
        $(this)
          .children(".inner-gdpr-columns")
          .css("background-color", GDPR.settings.button_accept_button_color);
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
    if (cookie_options.active_law != "both") {
      $(
        ".gdprmodal-header, .gdpr-about-cookies, .gdprmodal-footer, .category-item, .gdpr-columns"
      ).css({
        "background-color": cookie_options.background,
        opacity: cookie_options.opacity,
        color: cookie_options.text,
      });
    }
    if (cookie_options.active_law == "both") {
      $(
        ".gdprmodal-header, .gdpr-about-cookies, .gdprmodal-footer, .category-item, .gdpr-columns"
      ).css({
        "background-color": cookie_options.background_legislation,
        opacity: cookie_options.opacity_legislation,
        color: cookie_options.text_legislation,
      });
    }
  } else {
    if (Number(chosenBanner) === 1) {
      // CSS for the banner A when ab testing is enabled.
      $(
        ".gdprmodal-header, .gdpr-about-cookies, .gdprmodal-footer, .category-item, .gdpr-columns"
      ).css({
        "background-color": cookie_options.background1,
        opacity: cookie_options.opacity1,
        color: cookie_options.text1,
      });
    } else {
      // CSS for the banner B when ab testing is enabled.
      $(
        ".gdprmodal-header, .gdpr-about-cookies, .gdprmodal-footer, .category-item, .gdpr-columns"
      ).css({
        "background-color": cookie_options.background2,
        opacity: cookie_options.opacity2,
        color: cookie_options.text2,
      });
    }
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
