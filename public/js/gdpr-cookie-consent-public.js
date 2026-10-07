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

  // Check if the current window is not the top-level window  -- if the banner is loaded in iframe then don't show the banner - for editing page
    if ( window.self !== window.top ) {
          return;
    }
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
      console.log("Debug: The default consent is missing. Make sure you have turned on support GCM, have atleast one default consent value set. Check documentation at: https://wplegalpages.com/docs/wp-cookie-consent/how-to-guides/implementing-google-consent-mode-using-wp-cookie-consent");
    } else {
      console.log("Debug: The default consent successfully set to - ", window.dataLayer[defaultPresent][2]);
    }
    // Log results to the console.
		if (updatePresent != -1) {
		  console.log("Debug: The consent successfully updated to - ", window.dataLayer[updatePresent][2]);
		}
    if (firstTag == -1) {
		  console.log("Debug: GTM seems to be missing on your site. Check if GTM is installed correctly. ");
		}
    if(defaultPresent != -1 && firstTag != -1 && defaultPresent < firstTag){
      console.log("Debug: Default consent was set in correct order.")
    }
    else{
      console.log("Debug: The default consent was not set in correct order. Make sure everything is setup corretly.")
      console.log("Debug: If Google Tag Gateway is enabled on your website, you don't need to change anything. Just run Google Consent Mode in Advanced Mode. Check documentation at : https://wplegalpages.com/docs/wplp-docs/guides/google-tag-gateway-and-google-consent-mode-in-wplp-cookie-consent")
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


  

  // Laws that take the GDPR arm of every branch in this file.
  //
  // uk_gdpr, pipeda, au_app and sa_pdpl have no banner copy of their own yet, so
  // they render a placeholder notice (see
  // Gdpr_Cookie_Consent::get_law_placeholder_message()) around GDPR's consent
  // cookie, buttons and settings popup. The law code itself is no longer rewritten
  // to "gdpr" server-side — the visitor really is on au_app — so every test that
  // used to read `cookie_usage_for == "gdpr"` has to accept these four as well, or
  // they fall through the ladders and the banner stops responding entirely.
  //
  function gdpr_follows_gdpr_branch(law) {
    return (
      law == "gdpr" ||
      law == "uk_gdpr" ||
      law == "pipeda" ||
      law == "au_app" ||
      law == "sa_pdpl"
    );
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
  var vendor_data = gdpr_cookies_obj.vendor_data;
  var cookieSettingsPopupAccentColor  = gdpr_cookies_obj.cookieSettingsPopupAccentColor;
  var template_parts = gdpr_cookies_obj.template_parts;
  var gdpr_monthly_page_views_percent = Number( gdpr_cookies_obj.gdpr_monthly_page_views_percent );
  var current_vendor_index = 0;
  var next_vendors_loaded = false;
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
  var browser_gpc_value = "";
  if(navigator.globalPrivacyControl === true){
    // User has enabled Global Privacy Control
    browser_gpc_value = true;
  } else if (navigator.globalPrivacyControl === false){
    browser_gpc_value = false;
  }else{
    browser_gpc_value = false;
  }
   // Run this check when the DOM is ready and when debug mode is on.
   if(is_gcm_debug_on == 'true' || is_gcm_debug_on == true){
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
          setTimeout(debugConsentState, 1000);
        });
      } else {
        setTimeout(debugConsentState, 1000);
      }
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
      document.addEventListener("click", function (e) {
        const closeBtn = e.target.closest("#cookie-banner-cancle-img");
        if (closeBtn) {
          e.stopPropagation();
          e.preventDefault();
          GDPR.bypassed_close();
          GDPR.logConsent("bypassed");
        }
      }, true);
      if (is_iab_on) {
        window.addEventListener("load", function () {
          GDPR.render_vendor_list();
        });

        document.querySelector('.gdprmodal-body').addEventListener('scroll', function () {

            var scrollTop = this.scrollTop;
            var scrollHeight = this.scrollHeight;
            var clientHeight = this.clientHeight;
            var vendorTab = document.querySelector('#gdprIABTabVendors .gdpr-iab-navbar-button');
            var vendorRoot = document.querySelector('.iab-vendors-root');
            if (scrollTop + clientHeight >= 0.8 * scrollHeight && vendorTab.classList.contains('active') && vendorRoot.classList.contains('active-group')) {
                if(!next_vendors_loaded) {
                  GDPR.render_vendor_list();
                  next_vendors_loaded = true;
                }
            }
            else if(scrollTop + clientHeight < 0.8 * scrollHeight) next_vendors_loaded = false;

        });
      }
      

      // hide banner.
      // window.addEventListener("load", function () {
        for (var id = 0; id < gdpr_select_pages.length; id++) {
          var pageToHideBanner = gdpr_select_pages[id];
          if (document.body.classList.contains("page-id-" + pageToHideBanner)) {
            if (
              gdpr_follows_gdpr_branch(GDPR.settings.cookie_usage_for) ||
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
                $(".gdprmodal").remove(); // remove the whole modal
              }
              if (banner || insidebanner) {
                banner.style.display = "none";
                $("#gdpr-cookie-consent-show-again").addClass("hide_show_again");
                insidebanner.style.display = "none";
              }
            } else if (GDPR.settings.cookie_usage_for == "ccpa" || GDPR.settings.cookie_usage_for == "us_state_laws") {
              if (GDPR.settings.cookie_bar_as == "popup") {
                $("#gdpr-popup").gdprmodal("hide");  // remove the whole modal
              }
              var banner = document.getElementById(
                "ccpa-cookie-consent-show-again"
              );
              var insidebanner = document.getElementById(
                "gdpr-cookie-consent-bar"
              );
               if (banner || insidebanner) {
               banner.style.display = "none";
                $("#ccpa-cookie-consent-show-again").addClass("hide_show_again");
                insidebanner.style.display = "none";
              }
            }
          }
        }
      // });
      // if DNT request is true then hide the banner and auto decline the consent

      if ((gdpr_do_not_track == "true" && browser_dnt_value ) || (gdpr_do_not_track == "true" && browser_gpc_value)) {
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
        if (gdpr_follows_gdpr_branch(GDPR.settings.cookie_usage_for)) {
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
        } else if (GDPR.settings.cookie_usage_for == "both" || GDPR.settings.cookie_usage_for === "ccpa" || GDPR.settings.cookie_usage_for == "us_state_laws") {
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
      }

      if (
        gdpr_follows_gdpr_branch(this.settings.cookie_usage_for) ||
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
        if (gdpr_follows_gdpr_branch(this.settings.cookie_usage_for)) {
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
    getSubtleColors: function (finalColor, buttonColor) {
        // Use button color for the overlay.
        var color = buttonColor || finalColor;
        var hex = color.replace(/^#/, '');

        // Ignore alpha from #RRGGBBAA.
        if (hex.length === 8) {
            hex = hex.substring(0, 6);
        }

        // Expand #RGB into #RRGGBB.
        if (hex.length === 3) {
            hex = hex
                .split('')
                .map(function (character) {
                    return character + character;
                })
                .join('');
        }

        var r = parseInt(hex.substring(0, 2), 16);
        var g = parseInt(hex.substring(2, 4), 16);
        var b = parseInt(hex.substring(4, 6), 16);

        // Keep 6% of the button color and mix in 94% white.
        var tintStrength = 0.06;

        var overlayR = Math.round(
            255 - (255 - r) * tintStrength
        );

        var overlayG = Math.round(
            255 - (255 - g) * tintStrength
        );

        var overlayB = Math.round(
            255 - (255 - b) * tintStrength
        );

        return (
            '#' +
            [overlayR, overlayG, overlayB]
                .map(function (value) {
                    return value.toString(16).padStart(2, '0');
                })
                .join('')
        );
    },
    render_vendor_list: function () {
      if (!vendor_data || !vendor_data.vendors || current_vendor_index >= vendor_data.vendors.length) return;

          var vendors = vendor_data.vendors;
          var purposes = vendor_data.purposes;
          var specialPurposes = vendor_data.specialPurposes;
          var features = vendor_data.features;
          var dataCategories = vendor_data.dataCategories;

          var ul = document.querySelector(".vendors-list");
          const color = GDPR.settings['background_active_color' + chosenBanner];
          const opacity = parseFloat(GDPR.settings['opacity' + chosenBanner]);

          const opacityHex = Math.floor(opacity * 255)
              .toString(16)
              .padStart(2, '0')
              .toUpperCase();

          const finalColor = color + opacityHex;

          const abTestingEnabled =
              gdpr_ab_options.ab_testing_enabled === true ||
              gdpr_ab_options.ab_testing_enabled === 'true';

          const acceptAllBGColor = abTestingEnabled
              ? GDPR.settings['button_accept_all_button_color' + chosenBanner]
              : GDPR.settings.button_accept_all_button_color;

          // Remove the final two alpha characters from #RRGGBBAA.
          const finalColorWithoutAlpha = finalColor.slice(0, -2);

          const cookieSettingsPopupAccentColor =
              finalColorWithoutAlpha.toUpperCase() === acceptAllBGColor.toUpperCase()
                  ? GDPR.settings.button_accept_all_link_color
                  : acceptAllBGColor;

          const overlay = GDPR.settings.cookie_settings_overlay_color;

          var limit = Math.min(10, vendors.length);

          for (var i = current_vendor_index; i < current_vendor_index + limit && i < vendors.length; i++) {
              var vendor = vendors[i];

              var li = document.createElement("li");
              li.className = "category-item";
              if(i % 2 !== 0) li.style.background = overlay;

              /* HR */

              /* INNER COLUMN */

              var innerColumn = document.createElement("div");
              innerColumn.className = "inner-gdpr-column gdpr-category-toggle " + template_parts;

              var innerColumns = document.createElement("div");
              innerColumns.className = "inner-gdpr-columns";

              /* LEFT SECTION */

              var left = document.createElement("div");
              left.className = "left";

              var arrow = document.createElement("span");
              arrow.className = "gdpr-dropdown-arrow";

              arrow.innerHTML = '<svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.5303 0.53125L5.53027 5.53125L0.530273 0.531251" stroke="currentColor" stroke-width="1.5"/></svg>';

              var header = document.createElement("a");
              header.href = "#";
              header.className = "btn category-header vendors";
              header.tabIndex = 0;
              header.textContent = vendor.name;

              left.appendChild(arrow);
              left.appendChild(header);

              /* RIGHT SECTION */

              var right = document.createElement("div");
              right.className = "right";

              var toggleGroup = document.createElement("div");
              toggleGroup.className = "toggle-group";

              var vendorSwitchWrapper = document.createElement("div");
              vendorSwitchWrapper.className = "vendor-switch-wrapper";

              /* LEGITIMATE INTEREST SWITCH */

              if (vendor.legIntPurposes && vendor.legIntPurposes.length) {

                  var legWrap = document.createElement("div");
                  legWrap.className = "vendor-legitimate-switch-wrapper";

                  var legLabel = document.createElement("div");
                  legLabel.className = "vendor-switch-label";
                  legLabel.textContent = "Legitimate Interest";

                  var toggle = document.createElement("div");
                  toggle.className = "toggle";

                  var checkbox = document.createElement("div");
                  checkbox.className = "checkbox";

                  var input = document.createElement("input");
                  input.id = "gdpr_messagebar_body_button_legint_vendor_" + vendor.id;
                  input.className = "vendor-switch-handler legint-switch " + vendor.id;
                  input.type = "checkbox";
                  input.name = input.id;
                  input.value = vendor.id;

                  var label = document.createElement("label");
                  label.setAttribute("for", input.id);

                  var labelSpan = document.createElement("span");
                  labelSpan.className = "label-text";
                  labelSpan.textContent = vendor.id;

                  label.appendChild(labelSpan);
                  checkbox.appendChild(input);
                  checkbox.appendChild(label);
                  toggle.appendChild(checkbox);

                  legWrap.appendChild(legLabel);
                  legWrap.appendChild(toggle);

                  vendorSwitchWrapper.appendChild(legWrap);
              }

              /* CONSENT SWITCH */

              if (vendor.purposes && vendor.purposes.length) {

                  var consentWrap = document.createElement("div");
                  consentWrap.className = "vendor-consent-switch-wrapper";

                  var consentLabel = document.createElement("div");
                  consentLabel.className = "vendor-switch-label";
                  consentLabel.textContent = "Consent";

                  var toggle2 = document.createElement("div");
                  toggle2.className = "toggle";

                  var checkbox2 = document.createElement("div");
                  checkbox2.className = "checkbox";

                  var input2 = document.createElement("input");
                  input2.id = "gdpr_messagebar_body_button_consent_vendor_" + vendor.id;
                  input2.className = "vendor-switch-handler consent-switch " + vendor.id;
                  input2.type = "checkbox";
                  input2.name = input2.id;
                  input2.value = vendor.id;

                  var label2 = document.createElement("label");
                  label2.setAttribute("for", input2.id);

                  var labelSpan2 = document.createElement("span");
                  labelSpan2.className = "label-text";
                  labelSpan2.textContent = vendor.id;

                  label2.appendChild(labelSpan2);

                  checkbox2.appendChild(input2);
                  checkbox2.appendChild(label2);

                  toggle2.appendChild(checkbox2);

                  consentWrap.appendChild(consentLabel);
                  consentWrap.appendChild(toggle2);

                  vendorSwitchWrapper.appendChild(consentWrap);
              }

              toggleGroup.appendChild(vendorSwitchWrapper);
              right.appendChild(toggleGroup);

              innerColumns.appendChild(left);
              innerColumns.appendChild(right);

              innerColumn.appendChild(innerColumns);

              li.appendChild(innerColumn);

              /* DESCRIPTION CONTAINER */

              var descContainer = document.createElement("div");
              descContainer.className = "inner-description-container hide";

              var groupDesc = document.createElement("div");
              groupDesc.className = "group-description";
              groupDesc.tabIndex = 0;

              var adPurposeDetails = document.createElement("div");
              adPurposeDetails.className = "gdpr-ad-purpose-details";

              var vendorWrapper = document.createElement("div");
              vendorWrapper.className = "gdpr-vendor-wrapper";

              /* PRIVACY POLICY */

              if (vendor.urls && vendor.urls.length) {

                  var privacyP = document.createElement("p");
                  privacyP.className = "gdpr-vendor-privacy-link";

                  

                  var privacyLink = document.createElement("a");
                  privacyLink.href = vendor.urls[0].privacy;
                  privacyLink.target = "_blank";
                  privacyLink.rel = "noopener noreferrer";
                  privacyLink.textContent = 'Privacy Policy';

                  privacyP.appendChild(privacyLink);

                  vendorWrapper.appendChild(privacyP);

                  var legIntClaim = document.createElement("p");
                  legIntClaim.className = "gdpr-vendor-privacy-link";

                  

                  var legIntLink = document.createElement("a");
                  legIntLink.href = vendor.urls[0].legIntClaim;
                  legIntLink.target = "_blank";
                  legIntLink.rel = "noopener noreferrer";
                  legIntLink.textContent = 'Legitimate Interest Claim'

                  legIntClaim.appendChild(legIntLink);

                  vendorWrapper.appendChild(legIntClaim);
              }

              var arrow2 = document.createElement("span");
              arrow2.className = "gdpr-dropdown-arrow";
              arrow2.innerHTML = '<svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.5303 0.53125L5.53027 5.53125L0.530273 0.531251" stroke="currentColor" stroke-width="1.5"/></svg>';

              var more_text = document.createElement("span");
              more_text.className = "gdpr-more-details-text";
              more_text.innerHTML = 'More details';

              var moreDetails = document.createElement("p");
              moreDetails.className = "gdpr-more-details"
              moreDetails.appendChild(arrow2)
              moreDetails.appendChild(more_text)
              moreDetails.setAttribute("role", "button");
              moreDetails.setAttribute("tabindex", "0");
              moreDetails.setAttribute("aria-expanded", "false");

              vendorWrapper.appendChild(moreDetails);

              var detailsWrapper = document.createElement("div");
              detailsWrapper.className = "gdpr-vendor-details-wrapper";
              detailsWrapper.style.display = "none";

              moreDetails.addEventListener("click", function (event) {
                  event.preventDefault();

                  var currentToggle = event.currentTarget;
                  var wrapper = currentToggle.nextElementSibling;

                  if (
                      !wrapper ||
                      !wrapper.classList.contains(
                          "gdpr-vendor-details-wrapper"
                      )
                  ) {
                      return;
                  }

                  var isExpanded =
                      currentToggle.getAttribute("aria-expanded") === "true";

                  var shouldExpand = !isExpanded;

                  wrapper.style.display = shouldExpand
                      ? "block"
                      : "none";

                  var textElement = currentToggle.querySelector(
                      ".gdpr-more-details-text"
                  );

                  if (textElement) {
                      textElement.textContent = shouldExpand
                          ? "Less details"
                          : "More details";
                  }

                  currentToggle.classList.toggle(
                      "is-expanded",
                      shouldExpand
                  );

                  currentToggle.setAttribute(
                      "aria-expanded",
                      shouldExpand ? "true" : "false"
                  );
              });

              /* DATA RETENTION */

              var retention = document.createElement("p");
              retention.className = "gdpr-vendor-data-retention-section";

              var retentionSpan = document.createElement("span");
              retentionSpan.className = "gdpr-vendor-data-retention-value";

              if (vendor.dataRetention && vendor.dataRetention.stdRetention)
                  retentionSpan.textContent = "Data Retention Period: " + vendor.dataRetention.stdRetention + " Days";
              else
                  retentionSpan.textContent = "Data Retention Period: Not Available";

              retention.appendChild(retentionSpan);

              detailsWrapper.appendChild(retention);

              /* PURPOSES */

              if (vendor.purposes && vendor.purposes.length) {

                  var purposeSection = document.createElement("div");
                  purposeSection.className = "gdpr-vendor-purposes-section";

                  var purposeTitle = document.createElement("p");
                  purposeTitle.className = "gdpr-vendor-purposes-title";
                  purposeTitle.textContent = "Purposes (Consent)";

                  var purposeList = document.createElement("ul");
                  purposeList.className = "gdpr-vendor-purposes-list";

                  vendor.purposes.forEach(function (p) {

                      var item = document.createElement("li");
                      item.textContent = purposes[p - 1].name;

                      purposeList.appendChild(item);
                  });

                  purposeSection.appendChild(purposeTitle);
                  purposeSection.appendChild(purposeList);

                  detailsWrapper.appendChild(purposeSection);
              }

              /* LEGITIMATE PURPOSES */

              if (vendor.legIntPurposes && vendor.legIntPurposes.length) {

                  var legSection = document.createElement("div");
                  legSection.className = "gdpr-vendor-purposes-legint-section";

                  var legTitle = document.createElement("p");
                  legTitle.className = "gdpr-vendor-purposes-legint-title";
                  legTitle.textContent = "Purposes (Legitimate Interest)";

                  var legList = document.createElement("ul");
                  legList.className = "gdpr-vendor-purposes-legint-list";

                  vendor.legIntPurposes.forEach(function (p) {

                      var item = document.createElement("li");
                      item.textContent = purposes[p - 1].name;

                      legList.appendChild(item);
                  });

                  legSection.appendChild(legTitle);
                  legSection.appendChild(legList);

                  detailsWrapper.appendChild(legSection);
              }

              /* SPECIAL PURPOSES */

              if (vendor.specialPurposes && vendor.specialPurposes.length) {

                  var spSection = document.createElement("div");
                  spSection.className = "gdpr-vendor-special-purposes-section";

                  var spTitle = document.createElement("p");
                  spTitle.className = "gdpr-vendor-special-purposes-title";
                  spTitle.textContent = "Special Purposes";

                  var spList = document.createElement("ul");
                  spList.className = "gdpr-vendor-special-purposes-list";

                  vendor.specialPurposes.forEach(function (p) {

                      var item = document.createElement("li");
                      item.textContent = specialPurposes[p - 1].name;

                      spList.appendChild(item);
                  });

                  spSection.appendChild(spTitle);
                  spSection.appendChild(spList);

                  detailsWrapper.appendChild(spSection);
              }

              /* FEATURES */

              if (vendor.features && vendor.features.length) {

                  var featureSection = document.createElement("div");
                  featureSection.className = "gdpr-vendor-features-section";

                  var featureTitle = document.createElement("p");
                  featureTitle.className = "gdpr-vendor-features-title";
                  featureTitle.textContent = "Features";

                  var featureList = document.createElement("ul");
                  featureList.className = "gdpr-vendor-features-list";

                  vendor.features.forEach(function (p) {

                      var item = document.createElement("li");
                      item.textContent = features[p - 1].name;

                      featureList.appendChild(item);
                  });

                  featureSection.appendChild(featureTitle);
                  featureSection.appendChild(featureList);

                  detailsWrapper.appendChild(featureSection);
              }

              /* DATA CATEGORIES */

              if (vendor.dataDeclaration && vendor.dataDeclaration.length) {

                  var catSection = document.createElement("div");
                  catSection.className = "gdpr-vendor-category-section";

                  var catTitle = document.createElement("p");
                  catTitle.className = "gdpr-vendor-category-title";
                  catTitle.textContent = "Data Categories";

                  var catList = document.createElement("ul");
                  catList.className = "gdpr-vendor-category-list";

                  vendor.dataDeclaration.forEach(function (p) {

                      var item = document.createElement("li");
                      item.textContent = dataCategories[p - 1].name;

                      catList.appendChild(item);
                  });

                  catSection.appendChild(catTitle);
                  catSection.appendChild(catList);

                  detailsWrapper.appendChild(catSection);
              }

              if (
                  vendor.usesCookies ||
                  vendor.usesNonCookieAccess ||
                  vendor.cookieMaxAgeSeconds !== undefined
              ) {

                  var storageSection = document.createElement("div");
                  storageSection.className = "gdpr-vendor-storage-section";

                  var storageTitle = document.createElement("p");
                  storageTitle.className = "gdpr-vendor-storage-title";
                  storageTitle.textContent = "Device Storage Overview";

                  var storageList = document.createElement("ul");
                  storageList.className = "gdpr-vendor-storage-list";

                  /* Tracking Method */

                  var trackingMethod = "";

                  if (vendor.usesCookies && vendor.usesNonCookieAccess) {
                      trackingMethod = "Cookie and others";
                  } else if (vendor.usesCookies) {
                      trackingMethod = "Cookie";
                  } else if (vendor.usesNonCookieAccess) {
                      trackingMethod = "Others";
                  }

                  if (trackingMethod) {

                      var trackingLi = document.createElement("li");
                      trackingLi.textContent = "Tracking method: " + trackingMethod;

                      storageList.appendChild(trackingLi);
                  }

                  /* Cookie Max Duration */

                  if (vendor.cookieMaxAgeSeconds) {

                      var durationLi = document.createElement("li");

                      var days = Math.floor(vendor.cookieMaxAgeSeconds / (60 * 60 * 24));

                      durationLi.textContent =
                          "Maximum duration of Cookies: " + days + " days";

                      storageList.appendChild(durationLi);
                  }

                  /* Cookie Refresh */

                  var refreshLi = document.createElement("li");

                  if (vendor.cookieRefresh) {
                      refreshLi.textContent = "Cookie lifetime is being refreshed";
                  } else {
                      refreshLi.textContent = "Cookie lifetime is not refreshed";
                  }

                  storageList.appendChild(refreshLi);

                  storageSection.appendChild(storageTitle);
                  storageSection.appendChild(storageList);

                  detailsWrapper.appendChild(storageSection);
              }
              vendorWrapper.appendChild(detailsWrapper);
              adPurposeDetails.appendChild(vendorWrapper);
              groupDesc.appendChild(adPurposeDetails);
              descContainer.appendChild(groupDesc);

              li.appendChild(descContainer);

              ul.appendChild(li);
          }
          current_vendor_index += 10;
          $(document).trigger("wplp_vendors_rendered");
    },
    consent_renew_method: function () {
      const browser_consent_version = GDPR_Cookie.read("consent_version");
      var settings = JSON.parse(gdpr_cookiebar_settings);
      //check if version number doesnt exist or if current version number of visitor is less than the one stored in site database.
      if (
         (browser_consent_version == null &&
        (GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME) || GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME)) //check this
        ) ||
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
                  gdpr_follows_gdpr_branch(GDPR.settings.cookie_usage_for) ||
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
                } else if (GDPR.settings.cookie_usage_for == "ccpa" || GDPR.settings.cookie_usage_for == "us_state_laws") {
                  if (GDPR.settings.cookie_bar_as == "popup") {
                    $("#gdpr-popup").gdprmodal("hide");
                  }
                  var insidebanner = document.getElementById(
                    "gdpr-cookie-consent-bar"
                  );
                  if (insidebanner) {
                    insidebanner.style.display = "none";
                  }
                  $("#ccpa-cookie-consent-show-again").hide();
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
                success: function (response) { },
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
              success: function (response) { },
            });
            document.addEventListener("click", userInteracted);
            document.addEventListener("scroll", userInteracted);
          }
        
        //devare cookies
        GDPR_Cookie.erase(GDPR_ACCEPT_COOKIE_NAME);
        GDPR_Cookie.erase(GDPR_CCPA_COOKIE_NAME);
        GDPR_Cookie.erase(US_PRIVACY_COOKIE_NAME);
        $("#ccpa-cookie-consent-show-again").hide();

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

            // In auto ("Detect Automatically") mode the applicable law is resolved
            // per visitor on the server, so the stored cookie_usage_for is only a
            // fallback. Prefer the law from the geo response; render_law is the
            // same value, kept in the response for older consumers.
            if (
              cookieData["law_selection_mode"] === "auto" &&
              response.auto_mode === "on" &&
              response.render_law
            ) {
              cookie_for = response.render_law;
              GDPR.settings.cookie_usage_for = response.render_law;
              GDPR.resolved_law = response.law;
              
            } 

            

            // For the GDPR & CCPA
            if ("both" == cookie_for) {
              var ccpa_optout = GDPR_Cookie.read(GDPR_CCPA_COOKIE_NAME);
              if(ccpa_optout == "yes") jQuery('#donot_sell_checkbox').prop('checked', true);
              else jQuery('#donot_sell_checkbox').prop('checked', false);
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
            } else if (
              "gdpr" == cookie_for ||
              "eprivacy" == cookie_for ||
              "lgpd" == cookie_for ||
              "uk_gdpr" == cookie_for ||
              "pipeda" == cookie_for ||
              "au_app" == cookie_for ||
              "sa_pdpl" == cookie_for
            ) {
              if (!GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME)) {
                if (response.geo_status === "on") {
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
                if (response.geo_status != "on") {
                  $("#gdpr-cookie-consent-show-again").addClass(
                    "hide_show_again_dnt"
                  );
                }
                GDPR.hideHeader();
              }
            } else if ("ccpa" == cookie_for || "us_state_laws" == cookie_for) {
              if (!GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME)) {
                if (response.geo_status === "on") {
                  GDPR.displayHeader();
                } else {
                  $("#gdpr-cookie-consent-bar").addClass("hide_show_again_dnt");
                  $("#ccpa-cookie-consent-show-again").addClass("hide_show_again_dnt"); //check this
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
                var ccpa_optout = GDPR_Cookie.read(GDPR_CCPA_COOKIE_NAME);
                if (ccpa_optout == "yes") jQuery('#donot_sell_checkbox').prop('checked', true);
                else jQuery('#donot_sell_checkbox').prop('checked', false);
                if (response.geo_status != "on") {
                  $("#ccpa-cookie-consent-show-again").addClass(
                    "hide_show_again_dnt"
                  );
                }
                GDPR.hideHeader();
              }
            }
            GDPR.consent_renew_method();
          }
        },
        error: function () {
          if (
            !GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME) &&
            !GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME)
          ) {
            GDPR.displayHeader();
          }
        },
      });
    },
    checkEuAndCCPAStatus: function (response) {
      if (response.eu_status == "off" && response.ccpa_status == "off") {
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
          
          if(is_gcm_on == 'true' || is_gcm_on == true){
            gtag('consent', 'update', {
              'ad_user_data': marketing_consent ? 'granted' : 'denied',
              'ad_personalization': marketing_consent ? 'granted' : 'denied',
              'ad_storage': marketing_consent ? 'granted' : 'denied',
              'analytics_storage': analytics_consent ? 'granted' : 'denied',
              'functionality_storage': 'granted',
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
          
          if(is_gcm_debug_on == 'true' || is_gcm_debug_on == true){debugUpdateConsentState();}

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
                var data = response.data;

                window.headerScriptsLoaded = false;
                window.footerScriptsLoaded = false;
                window.bodyScriptsLoaded = false;

                function runScript(scriptContent, target) {
                    if (!scriptContent) return;
                    try {
                        var script = document.createElement("script");
                        script.textContent = scriptContent;
                        target.appendChild(script);
                    } catch (e) {
                        console.error('Script error:', e);
                    }
                }

                function extractScripts(htmlString) {
                    if (!htmlString) return [];
                    var tempDiv = document.createElement("div");
                    tempDiv.innerHTML = htmlString;
                    var scripts = [];
                    tempDiv.querySelectorAll("script").forEach(function (s) {
                        scripts.push(s.innerHTML);
                    });
                    return scripts;
                }

                function waitFor(condFn, cb) {
                    setTimeout(function check() {
                        if (condFn()) {
                            cb();
                        } else {
                            setTimeout(check, 50);
                        }
                    }, 0);
                }

                var headerScripts = extractScripts(data.header_scripts);
                var bodyScripts   = extractScripts(data.body_scripts);
                var footerScripts = extractScripts(data.footer_scripts);

                function runHeader() {
                    headerScripts.forEach(function (s) { runScript(s, document.head); });
                    window.headerScriptsLoaded = true;
                }
                function runFooter() {
                    footerScripts.forEach(function (s) { runScript(s, document.body); });
                    window.footerScriptsLoaded = true;
                }
                function runBody() {
                    bodyScripts.forEach(function (s) { runScript(s, document.body); });
                    window.bodyScriptsLoaded = true;
                }

                var bodyWaitsHeader = (data.header_dependency === "Body Scripts");
                var bodyWaitsFooter = (data.footer_dependency === "Body Scripts");
                var headerWaitsFooter = (data.footer_dependency === "Header Scripts");
                var footerWaitsHeader = (data.header_dependency === "Footer Scripts");

                // --- Header (wp_head position) ---
                if (headerWaitsFooter) {
                    // Header has dependency — defer it
                    waitFor(function () { return window.footerScriptsLoaded; }, runHeader);
                } else {
                    // No dependency — run immediately in wp_head position
                    runHeader();
                }

                // --- Body (wp_body_open position) ---
               if (bodyWaitsHeader && bodyWaitsFooter) {
                  if (window.headerScriptsLoaded && window.footerScriptsLoaded) {
                      runBody();
                  } else {
                      waitFor(function () { return window.headerScriptsLoaded && window.footerScriptsLoaded; }, runBody);
                  }
              } else if (bodyWaitsHeader) {
                  if (window.headerScriptsLoaded) {
                      runBody(); // Header already ran synchronously above — run Body now
                  } else {
                      waitFor(function () { return window.headerScriptsLoaded; }, runBody);
                  }
              } else if (bodyWaitsFooter) {
                  waitFor(function () { return window.footerScriptsLoaded; }, runBody);
              } else {
                  runBody();
              }
                
                // --- Footer (wp_footer position) ---
                if (footerWaitsHeader) {
                    waitFor(function () { return window.headerScriptsLoaded; }, runFooter);
                } else {
                    // No dependency — run immediately in wp_footer position
                    runFooter();
              }
              },
          });
          // Dispatch appropriate events based on settings
          var event;
          if (gdpr_follows_gdpr_branch(GDPR.settings.cookie_usage_for)) {
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

          if(is_gcm_on == 'true' || is_gcm_on == true){
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

          if(is_gcm_debug_on == 'true' || is_gcm_debug_on == true){debugUpdateConsentState();}

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
                var data = response.data;

                window.headerScriptsLoaded = false;
                window.footerScriptsLoaded = false;
                window.bodyScriptsLoaded = false;
                function runScript(scriptContent, target) {
                    if (!scriptContent) return;
                    try {
                        var script = document.createElement("script");
                        script.textContent = scriptContent;
                        target.appendChild(script);
                    } catch (e) {
                        console.error('Script error:', e);
                    }
                }

                function extractScripts(htmlString) {
                    if (!htmlString) return [];
                    var tempDiv = document.createElement("div");
                    tempDiv.innerHTML = htmlString;
                    var scripts = [];
                    tempDiv.querySelectorAll("script").forEach(function (s) {
                        scripts.push(s.innerHTML);
                    });
                    return scripts;
                }

                function waitFor(condFn, cb) {
                    setTimeout(function check() {
                        if (condFn()) {
                            cb();
                        } else {
                            setTimeout(check, 50);
                        }
                    }, 0);
                }

                var headerScripts = extractScripts(data.header_scripts);
                var bodyScripts   = extractScripts(data.body_scripts);
                var footerScripts = extractScripts(data.footer_scripts);

                function runHeader() {
                    headerScripts.forEach(function (s) { runScript(s, document.head); });
                    window.headerScriptsLoaded = true;
                }
                function runFooter() {
                    footerScripts.forEach(function (s) { runScript(s, document.body); });
                    window.footerScriptsLoaded = true;
                }
                function runBody() {
                    bodyScripts.forEach(function (s) { runScript(s, document.body); });
                    window.bodyScriptsLoaded = true;
                }

                var bodyWaitsHeader = (data.header_dependency === "Body Scripts");
                var bodyWaitsFooter = (data.footer_dependency === "Body Scripts");
                var headerWaitsFooter = (data.footer_dependency === "Header Scripts");
                var footerWaitsHeader = (data.header_dependency === "Footer Scripts");

                // --- Header (wp_head position) ---
                if (headerWaitsFooter) {
                    // Header has dependency 
                    waitFor(function () { return window.footerScriptsLoaded; }, runHeader);
                } else {
                    // No dependency — run immediately in wp_head position
                    runHeader();
                }

                // --- Body (wp_body_open position) ---
                if (bodyWaitsHeader && bodyWaitsFooter) {
                  if (window.headerScriptsLoaded && window.footerScriptsLoaded) {
                      runBody();
                  } else {
                      waitFor(function () { return window.headerScriptsLoaded && window.footerScriptsLoaded; }, runBody);
                  }
                } else if (bodyWaitsHeader) {
                    if (window.headerScriptsLoaded) {
                        runBody(); // Header already ran synchronously above — run Body now
                    } else {
                        waitFor(function () { return window.headerScriptsLoaded; }, runBody);
                    }
                } else if (bodyWaitsFooter) {
                    waitFor(function () { return window.footerScriptsLoaded; }, runBody);
                } else {
                    runBody();
                }
                
                // --- Footer (wp_footer position) ---
                if (footerWaitsHeader) {
                    waitFor(function () { return window.headerScriptsLoaded; }, runFooter);
                } else {
                    // No dependency — run immediately in wp_footer position
                    runFooter();
                }
              },
          });
          new_window = GDPR.settings.button_accept_all_new_win ? true : false;
          gdpr_viewed_cookie = GDPR_Cookie.read("wpl_viewed_cookie");

          if (gdpr_follows_gdpr_branch(GDPR.settings.cookie_usage_for)) {
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
          if(is_gcm_on == 'true' || is_gcm_on == true){
              gtag('consent', 'update', {
                'ad_user_data': 'denied',
                'ad_personalization': 'denied',
                'ad_storage': 'denied',
                'analytics_storage': 'denied',
                'functionality_storage': 'granted',
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

            if(is_gcm_debug_on == 'true' || is_gcm_debug_on == true){debugUpdateConsentState();}


          GDPR.reject_close();
          new_window = GDPR.settings.button_decline_new_win ? true : false;
          gdpr_user_preference = JSON.parse(
            GDPR_Cookie.read("wpl_user_preference")
          );
          gdpr_viewed_cookie = GDPR_Cookie.read("wpl_viewed_cookie");

          if (gdpr_follows_gdpr_branch(GDPR.settings.cookie_usage_for)) {
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
            setTimeout(function() {
                const backdrop = document.querySelector(".gdprmodal-backdrop");
                const modal = document.querySelector(".gdprmodal");
                
                if (backdrop && modal && backdrop.parentElement !== modal.parentElement) {
                    modal.parentElement.insertBefore(backdrop, modal);
                }
            }, 0);
        } else if (button_action == "close") {
            var law = GDPR.settings.cookie_usage_for;

            var hasGDPR  = GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME);
            var hasCCPA  = GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME) ||
                          GDPR_Cookie.exists(US_PRIVACY_COOKIE_NAME);

            if (gdpr_follows_gdpr_branch(law) || law === "lgpd" || law === "eprivacy") {

                if (hasGDPR) {
                    // Consent already given
                    GDPR.bar_elm.hide();
                    GDPR.show_again_elm.slideDown(GDPR.settings.animate_speed_show);
                } else {
                    // No consent yet show banner
                    GDPR.displayHeader();
                }

            } else if (law === "ccpa" || law === "us_state_laws") {
                    $("#gdpr-cookie-consent-show-again").hide();

                if (hasCCPA) {
                    GDPR.bar_elm.hide();
                    if (GDPR.settings.ccpa_show_again === true || GDPR.settings.ccpa_show_again === "true") {
                      $("#ccpa-cookie-consent-show-again").show();
                    }

                } else {
                    GDPR.displayHeader();
                }

            }
            else if (law === "both") {

                if (hasGDPR && hasCCPA) {
                    // Both accepted
                    GDPR.bar_elm.hide();
                    GDPR.show_again_elm.slideDown(GDPR.settings.animate_speed_show);

                } else if (!hasGDPR) {
                    // GDPR missing
                    multiple_legislation_current_banner = "gdpr";
                    GDPR.displayHeader();

                } else if (!hasCCPA) {
                    // CCPA missing
                    multiple_legislation_current_banner = "ccpa";
                    GDPR.displayHeader(true, false);
                    GDPR.show_again_elm.slideDown(GDPR.settings.animate_speed_show);
                }
            }

          else {
          GDPR.displayHeader();
          if (
            GDPR.settings.cookie_bar_as === "popup" &&
            GDPR.settings.notify_animate_show !== false
          ) {
            $("#gdpr-cookie-consent-bar").css("display", "none");
            $("#gdpr-cookie-consent-bar").slideDown(500);
          }
          }
        } else if (button_action == "show_settings") {
          GDPR.show_details();
        } else if (button_action == "hide_settings") {
          GDPR.hide_details();
        } else if (button_action == "donotsell") {
          if (
            GDPR.settings.cookie_usage_for == "ccpa" || GDPR.settings.cookie_usage_for == "us_state_laws" || 
            jQuery(GDPR.settings.notify_div_id).find("p.gdpr").css("display") ==
              "none"
          ) {
            GDPR.hideHeader(true);
          } else {
            GDPR.hideHeader();
          }
          $("#gdpr-ccpa-gdprmodal").gdprmodal("show");
          setTimeout(function () {
            const backdrops = document.querySelectorAll(".gdprmodal-backdrop");
            const backdrop = backdrops[backdrops.length - 1]; // newest backdrop
            const modal = document.querySelector("#gdpr-ccpa-gdprmodal");
            if (backdrop && modal) {
                modal.parentElement.insertBefore(backdrop, modal);
            }
        }, 0);
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
          jQuery('#donot_sell_checkbox').prop('checked', false);
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
          if (jQuery('#donot_sell_checkbox').is(':checked')) {
              GDPR.confirm_close();
          } else {
              GDPR.ccpa_cancel_close();
          }
          

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
        $(
          ".gdpr_messagebar_detail .gdpr-iab-navbar #gdprIABTabFeatures .gdpr-iab-navbar-button"
        ).css("border-color", 'transparent');
        $(
          ".gdpr_messagebar_detail .gdpr-iab-navbar #gdprIABTabCategory .gdpr-iab-navbar-button"
        ).css("border-color", 'transparent');
        $(
          ".gdpr_messagebar_detail .gdpr-iab-navbar #gdprIABTabVendors .gdpr-iab-navbar-button"
        ).css("border-width", '2px');
        $(
          ".gdpr_messagebar_detail .gdpr-iab-navbar #gdprIABTabVendors .gdpr-iab-navbar-button"
        ).css("border-style", 'solid');
        $(
          ".gdpr_messagebar_detail .gdpr-iab-navbar #gdprIABTabVendors .gdpr-iab-navbar-button"
        ).css(
          "border-color",
          "transparent transparent " + GDPR.settings.button_accept_button_color + " transparent"
        );
        // // switch (this.id) {
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
          (key == "analytics" ||
          key == "marketing" ||
          key == "unclassified" ||
          key == "preferences") && (gdpr_user_preference_arr[key] !== 'yes')
        ) {
          gdpr_user_preference_arr[key] = "no";
          var length = GDPR.allowed_categories.length;
          for (var i = 0; i < length; i++) {
            if (GDPR.allowed_categories[i] == key) {
              GDPR.allowed_categories.splice(i, 1);
            }
          }
        } else if (
          (key == "analytics" ||
          key == "marketing" ||
          key == "unclassified" ||
          key == "preferences") && (gdpr_user_preference_arr[key] == 'yes')
        ) {
          GDPR.allowed_categories.push(key);
          $(this).prop("checked", true);
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
          var hasConsent = GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME) || 
                          GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME) ||
                          GDPR_Cookie.exists(US_PRIVACY_COOKIE_NAME) ||
                          GDPR_Cookie.exists("wpl_user_preference");
          
          if (hasConsent) {
              if (GDPR.settings.cookie_usage_for === "eprivacy") {
                  GDPR.bar_elm.show();
                  GDPR.show_again_elm.hide();
                  return false;
              }

              // GDPR, CCPA, LGPD, GDPR&CCPA: Show the popup modal
              GDPR.bar_elm.hide();
              GDPR.show_again_elm.hide();
                
              jQuery.ajax({
                url: log_obj.ajax_url,
                type: "POST",
                data: {
                    action: "gdpr_increase_page_view",
                    security: log_obj.consent_logging_nonce,
                },
                success: function (response) {}
              });
              // GDPR & CCPA
              if (GDPR.settings.cookie_usage_for === "both") {
                  jQuery(GDPR.settings.notify_div_id).find("p.gdpr").show();
                  jQuery(GDPR.settings.notify_div_id).find("h3.gdpr_heading").show();
                  jQuery(GDPR.settings.notify_div_id).find(".gdpr.group-description-buttons").show();
                  
                  
                  // jQuery(GDPR.settings.notify_div_id).css("background", GDPR.convertToHex(GDPR.settings.multiple_legislation_cookie_bar_color1, GDPR.settings.multiple_legislation_cookie_bar_opacity1));
                  // jQuery(GDPR.settings.notify_div_id).css("color", GDPR.settings.multiple_legislation_cookie_text_color1);
                  // jQuery(GDPR.settings.notify_div_id).css("border-style", GDPR.settings.multiple_legislation_border_style1);
                  // jQuery(GDPR.settings.notify_div_id).css("border-color", GDPR.settings.multiple_legislation_cookie_border_color1);
                  // jQuery(GDPR.settings.notify_div_id).css("border-width", GDPR.settings.multiple_legislation_cookie_bar_border_width1);
                  // jQuery(GDPR.settings.notify_div_id).css("border-radius", GDPR.settings.multiple_legislation_cookie_bar_border_radius1);
                  // jQuery(GDPR.settings.notify_div_id).css("font-family", GDPR.settings.multiple_legislation_cookie_font1);
              }
            
              $("#gdpr-gdprmodal").gdprmodal("show");
              setTimeout(function () {
                const backdrop = document.querySelector(".gdprmodal-backdrop");
                const modal = document.querySelector("#gdpr-gdprmodal");

                if (
                    backdrop &&
                    modal &&
                    backdrop.parentElement !== modal.parentElement
                ) {
                    modal.parentElement.insertBefore(backdrop, modal);
                }
            }, 0);
              return false;
          }
          multiple_legislation_current_banner = "gdpr";
          if (
            GDPR.settings.cookie_usage_for == "both" &&
            multiple_legislation_current_banner == "gdpr"
          ) {
            GDPR.bar_elm.hide();
            GDPR.show_again_elm.hide();
          }
          if (GDPR.settings.cookie_usage_for == "both"){
              // jQuery(GDPR.settings.notify_div_id).css("background", GDPR.convertToHex(GDPR.settings.multiple_legislation_cookie_bar_color1, GDPR.settings.multiple_legislation_cookie_bar_opacity1));
              // jQuery(GDPR.settings.notify_div_id).css("color", GDPR.settings.multiple_legislation_cookie_text_color1);
              // jQuery(GDPR.settings.notify_div_id).css("border-style", GDPR.settings.multiple_legislation_border_style1);
              // jQuery(GDPR.settings.notify_div_id).css("border-color", GDPR.settings.multiple_legislation_cookie_border_color1);
              // jQuery(GDPR.settings.notify_div_id).css("border-width", GDPR.settings.multiple_legislation_cookie_bar_border_width1);
              // jQuery(GDPR.settings.notify_div_id).css("border-radius", GDPR.settings.multiple_legislation_cookie_bar_border_radius1);
              // jQuery(GDPR.settings.notify_div_id).css("font-family", GDPR.settings.multiple_legislation_cookie_font1);
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
      //CCPA
     jQuery(document).on("click", "#ccpa-cookie-consent-show-again", function (e) {
        e.preventDefault();

        // Hide the revoke tab
        $("#ccpa-cookie-consent-show-again").hide();

        if (GDPR.settings.cookie_bar_as === "popup") {
            $("#gdpr-ccpa-gdprmodal").gdprmodal("show");
        } else {
            $("#gdpr-ccpa-gdprmodal").gdprmodal("show");
        }
        setTimeout(function () {
          const backdrop = document.querySelector(".gdprmodal-backdrop");
          const modal = document.querySelector("#gdpr-ccpa-gdprmodal");
          if (backdrop && modal && backdrop.parentElement !== modal.parentElement) {
              modal.parentElement.insertBefore(backdrop, modal);
          }
      }, 0);
    });

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
      if (gdpr_follows_gdpr_branch(this.settings.cookie_usage_for)) {
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
      } else if (this.settings.cookie_usage_for == "ccpa" || this.settings.cookie_usage_for == "us_state_laws") {
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
            if (GDPR.settings.ccpa_show_again === true || GDPR.settings.ccpa_show_again === "true") {
              $("#ccpa-cookie-consent-show-again").show();
          }
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
      if ((GDPR.settings.cookie_usage_for === "ccpa" || GDPR.settings.cookie_usage_for === "us_state_laws") && 
          (GDPR.settings.ccpa_show_again === true || GDPR.settings.ccpa_show_again === "true")) {
          $("#ccpa-cookie-consent-show-again").show();
          
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
      if ((GDPR.settings.cookie_usage_for === "ccpa" || GDPR.settings.cookie_usage_for === "us_state_laws") && 
          (GDPR.settings.ccpa_show_again === true || GDPR.settings.ccpa_show_again === "true")) {
          $("#ccpa-cookie-consent-show-again").show();
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
      if (this.settings.show_again) {
        this.show_again_elm.slideDown(this.settings.animate_speed_hide);
      }
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
      if (this.settings.show_again) {
        this.show_again_elm.slideDown(this.settings.animate_speed_hide);
      }
      if (
        this.settings.decline_reload == true &&
        !(gdpr_do_not_track == "true" && (browser_dnt_value || browser_gpc_value))
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
      if (this.settings.show_again) {
        this.show_again_elm.slideDown(this.settings.animate_speed_hide);
      }
      if (
        this.settings.decline_reload == true &&
        !(gdpr_do_not_track == "true" && (browser_dnt_value || browser_gpc_value))
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
      if(gdpr_monthly_page_views_percent >= 100){
          document.querySelector('#gdpr-cookie-consent-bar').style.display = 'none';
          document.querySelector('#gdpr-cookie-consent-show-again').style.display = 'none';
          return;
      }
      user_triggered = (typeof user_triggered === 'undefined') ? false : user_triggered;
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

          setTimeout(function () {
            jQuery.ajax({
              url: log_obj.ajax_url,
              type: "POST",
              data: {
                action: "gdpr_increase_page_view",
                security: log_obj.consent_logging_nonce,
              },
              success: function (response) { },
            });
            document.addEventListener("click", userInteracted);
            document.addEventListener("scroll", userInteracted);
          }, this.settings.auto_banner_initialize_delay ? this.settings.auto_banner_initialize_delay : 0);
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
                  gdpr_follows_gdpr_branch(GDPR.settings.cookie_usage_for) ||
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
                } else if (GDPR.settings.cookie_usage_for == "ccpa" || GDPR.settings.cookie_usage_for == "us_state_laws") {
                  if (GDPR.settings.cookie_bar_as == "popup") {
                    $("#gdpr-popup").gdprmodal("hide");
                  }
                  var insidebanner = document.getElementById(
                    "gdpr-cookie-consent-bar"
                  );
                  if (insidebanner) {
                    insidebanner.style.display = "none";
                  }
                  //add for CCPA
                  if (GDPR.settings.ccpa_show_again === true || GDPR.settings.ccpa_show_again === "true") {
                      $("#ccpa-cookie-consent-show-again").show();
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
                success: function (response) { },
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
              success: function (response) { },
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
              // jQuery(GDPR.settings.notify_div_id).css("background", GDPR.convertToHex(GDPR.settings.multiple_legislation_cookie_bar_color2, GDPR.settings.multiple_legislation_cookie_bar_opacity2));
              // jQuery(GDPR.settings.notify_div_id).css("color", GDPR.settings.multiple_legislation_cookie_text_color2);
              // jQuery(GDPR.settings.notify_div_id).css("border-style", GDPR.settings.multiple_legislation_border_style2);
              // jQuery(GDPR.settings.notify_div_id).css("border-color", GDPR.settings.multiple_legislation_cookie_border_color2);
              // jQuery(GDPR.settings.notify_div_id).css("border-width", GDPR.settings.multiple_legislation_cookie_bar_border_width2);
              // jQuery(GDPR.settings.notify_div_id).css("border-radius", GDPR.settings.multiple_legislation_cookie_bar_border_radius2);
              // jQuery(GDPR.settings.notify_div_id).css("font-family", GDPR.settings.multiple_legislation_cookie_font2);
            }
            jQuery(GDPR.settings.notify_div_id).find("p.ccpa").show();
          }, banner_delay);
        } else {
          multiple_legislation_current_banner = "ccpa";
          if (GDPR.settings.cookie_usage_for == "both"){
            // jQuery(GDPR.settings.notify_div_id).css("background", GDPR.convertToHex(GDPR.settings.multiple_legislation_cookie_bar_color2, GDPR.settings.multiple_legislation_cookie_bar_opacity2));
            // jQuery(GDPR.settings.notify_div_id).css("color", GDPR.settings.multiple_legislation_cookie_text_color2);
            // jQuery(GDPR.settings.notify_div_id).css("border-style", GDPR.settings.multiple_legislation_border_style2);
            // jQuery(GDPR.settings.notify_div_id).css("border-color", GDPR.settings.multiple_legislation_cookie_border_color2);
            // jQuery(GDPR.settings.notify_div_id).css("border-width", GDPR.settings.multiple_legislation_cookie_bar_border_width2);
            // jQuery(GDPR.settings.notify_div_id).css("border-radius", GDPR.settings.multiple_legislation_cookie_bar_border_radius2);
            // jQuery(GDPR.settings.notify_div_id).css("font-family", GDPR.settings.multiple_legislation_cookie_font2);
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
               setTimeout(function () {
                    const backdrop = document.querySelector(".gdprmodal-backdrop");
                    const modal = document.querySelector("#gdpr-popup");
                    if (backdrop && modal && backdrop.parentElement !== modal.parentElement) {
                        modal.parentElement.insertBefore(backdrop, modal);
                    }
                }, 0);
            }, banner_delay);
          } else {
            $("#gdpr-popup").gdprmodal("show");
            setTimeout(function () {
              const backdrops = document.querySelectorAll(".gdprmodal-backdrop");
              const backdrop = backdrops[backdrops.length - 1];
              const modal = document.querySelector("#gdpr-popup");
              if (backdrop && modal) {
                  modal.parentElement.insertBefore(backdrop, modal);
              }
          }, 0);
          }
        }
      }
      if (
        gdpr_follows_gdpr_branch(this.settings.cookie_usage_for) ||
        this.settings.cookie_usage_for == "lgpd" ||
        this.settings.cookie_usage_for == "eprivacy" ||
        this.settings.cookie_usage_for == "both"
      ) {
        if (force_display_show_again  && this.settings.show_again) {
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
          gdpr_follows_gdpr_branch(this.settings.cookie_usage_for) ||
          this.settings.cookie_usage_for == "eprivacy" ||
          this.settings.cookie_usage_for == "both" ||
          this.settings.cookie_usage_for == "lgpd"
        ) {
          var self = this;
        if (self.settings.show_again) {
          if (self.settings.auto_banner_initialize) {
            setTimeout(function() { //arrow functions dont work in grunt build
              self.show_again_elm.slideDown(self.settings.animate_speed_hide);
            }, self.settings.auto_banner_initialize_delay);
          } else {
            self.show_again_elm.slideDown(self.settings.animate_speed_hide);
          }
        }
        } else if (this.settings.cookie_usage_for == "ccpa" || this.settings.cookie_usage_for == "us_state_laws") {
            var self = this;
            if (self.settings.ccpa_show_again === true || self.settings.ccpa_show_again === "true") {
                if (self.settings.auto_banner_initialize) {
                    setTimeout(function() {
                        $("#ccpa-cookie-consent-show-again").slideDown(self.settings.animate_speed_hide);
                    }, self.settings.auto_banner_initialize_delay);
                } else {
                    $("#ccpa-cookie-consent-show-again").slideDown(self.settings.animate_speed_hide);
            }
        }
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
        var gdpr_user_preference_arr = []
        if (GDPR_Cookie.read("wpl_user_preference")) {
            gdpr_user_preference_arr = JSON.parse(
              GDPR_Cookie.read("wpl_user_preference")
            );
        }
        var cookiesList = JSON.parse(GDPR_Blocker.cookies);
        for (var i = 0; i < cookiesList.length; i++) {
          var cookie = cookiesList[i];
          var current_category = cookie["gdpr_cookie_category_slug"];
          if(current_category == "necessary") continue;
          if ( !gdpr_user_preference_arr.hasOwnProperty(current_category) || gdpr_user_preference_arr[current_category] === "no") {
            var cookies = cookie["data"];
            if (cookies && cookies.length != 0) {
              for (var c_key in cookies) {
                var c_cookie = cookies[c_key];
                if (c_cookie["name"] === "consent_version") {
                  continue; // Skip the consent_version cookie as it is Unclassified
                }
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
              var gdpr_user_preference_arr = []
              if (GDPR_Cookie.read("wpl_user_preference")) {
                  gdpr_user_preference_arr = JSON.parse(
                    GDPR_Cookie.read("wpl_user_preference")
                  );
              }
              var isBlock = currentElm.getAttribute("data-wpl-block");
              if (GDPR_Blocker.blockingStatus === true) {
                if (
                  (GDPR_Cookie.read(GDPR_ACCEPT_COOKIE_NAME) == "yes" && (gdpr_user_preference_arr.hasOwnProperty(elmCategory) && gdpr_user_preference_arr[elmCategory] === "yes")) ||
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
      GDPR.addPlaceholder = htmlElmFuncs.addPlaceholder;

      genericFuncs.reviewConsent();
      genericFuncs.renderByElement(GDPR_Blocker.removeCookieByCategory);
    },
    blockElementorYoutube: function () {
      var ytData          = gdpr_cookies_obj.youtube_embed_category || {};
      var youtubeCategory = ytData.slug || 'preferences';
      var youtubeName     = ytData.name || 'Preferences';
      var placeholderHtml = 'Accept <a class="wpl_manage_current_consent">'
                            + youtubeName +
                            '</a> cookies to view this video.';

      var observer = new MutationObserver( function ( mutations ) {
          mutations.forEach( function ( mutation ) {
              mutation.addedNodes.forEach( function ( node ) {
                  if ( node.nodeType !== 1 ) return;
                  if (
                      node.tagName === 'IFRAME' &&
                      node.src &&
                      node.src.indexOf( 'youtube.com/embed' ) !== -1 &&
                      (
                        node.closest( '[data-e-type="e-youtube"]' ) ||      // YouTube Embed widget
                        node.closest( '.elementor-widget-video' )            // Video widget
                      )
                  ) {
                      var gdpr_user_preference_arr = {};
                      if ( GDPR_Cookie.read( 'wpl_user_preference' ) ) {
                          gdpr_user_preference_arr = JSON.parse(
                              GDPR_Cookie.read( 'wpl_user_preference' )
                          );
                      }
                      var consentGiven =
                          GDPR_Cookie.read( GDPR_ACCEPT_COOKIE_NAME ) === 'yes' &&
                          gdpr_user_preference_arr[ youtubeCategory ] === 'yes';

                      if ( ! consentGiven ) {
                          node.setAttribute( 'data-wpl-src', node.src );
                          node.setAttribute( 'data-wpl-class', 'wpl-blocker-script' );
                          node.setAttribute( 'data-wpl-script-type', youtubeCategory );
                          node.setAttribute( 'data-wpl-placeholder', placeholderHtml );
                          node.removeAttribute( 'src' );
                          node.style.display = 'none';
                          jQuery( '<div style="width:560px;height:315px;" class="wpl-iframe-placeholder"><div class="wpl-inner-text">'
                              + placeholderHtml +
                          '</div></div>' ).insertBefore( node );
                      }
                  }
              });
          });
      });

      if ( document.body ) {
          observer.observe( document.body, { childList: true, subtree: true } );
      } else {
          document.addEventListener( 'DOMContentLoaded', function () {
              observer.observe( document.body, { childList: true, subtree: true } );
          });
      }
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
      if (typeof gdpr_cookies_list != "undefined") {
        GDPR_Blocker.set({
          cookies: gdpr_cookies_list,
        });
        GDPR_Blocker.runScripts();
        GDPR_Blocker.blockElementorYoutube(); // ADD THIS
      }
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
          modalBody.style.height = '445px';
          break;
        case "gdprIABTabFeatures":
          $(".feature-group").css("display", "block");
          modalBody.style.height = '380px';
          break;
        case "gdprIABTabVendors":
          $(".vendor-group").css("display", "block");
          modalBody.style.height = '500px';
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

    $(document).on("click", ".gdpr-category-toggle.inner-gdpr-column", function () {

      if (!$(this).children(".inner-gdpr-columns").hasClass("active-group")) {

        $(".inner-gdpr-columns").removeClass("active-group");
        $(".inner-gdpr-columns .dashicons")
          .removeClass("dashicons-arrow-up-alt2")
          .addClass("dashicons-arrow-down-alt2");

        $(this).children(".inner-gdpr-columns").addClass("active-group");

        $(this)
          .children(".inner-gdpr-columns")
          .find(".dashicons")
          .removeClass("dashicons-arrow-down-alt2")
          .addClass("dashicons-arrow-up-alt2");

      } else {

        $(".inner-gdpr-columns").removeClass("active-group");

        $(this)
          .children(".inner-gdpr-columns")
          .find(".dashicons")
          .removeClass("dashicons-arrow-up-alt2")
          .addClass("dashicons-arrow-down-alt2");
      }

      if ($(this).siblings(".inner-description-container").hasClass("hide")) {

        $(".inner-description-container").addClass("hide");
        $(this).siblings(".inner-description-container").removeClass("hide");

      } else {

        $(".inner-description-container").addClass("hide");
      }

    });


    $(document).on("click", ".gdpr-default-category-toggle.inner-gdpr-column", function () {

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
