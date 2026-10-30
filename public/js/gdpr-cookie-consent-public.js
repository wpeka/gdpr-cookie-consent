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
  // The consent core (printed at the top of <head>) owns the consent record.
  // This script only renders the banner and hands decisions to it.
  var Core = window.WPLConsent || null;

  function hasGdprDecision() {
    return Core ? Core.hasDecision("optin") : GDPR_Cookie.exists(GDPR_ACCEPT_COOKIE_NAME);
  }
  function hasCcpaDecision() {
    return Core ? Core.hasDecision("optout") : GDPR_Cookie.exists(GDPR_CCPA_COOKIE_NAME);
  }
  function isCcpaOptedOut() {
    if (Core) {
      var record = Core.get();
      return !!(record && record.optout === true) || Core.gpc();
    }
    return GDPR_Cookie.read(GDPR_CCPA_COOKIE_NAME) == "yes";
  }
  function readJsonCookie(name) {
    try {
      return JSON.parse(GDPR_Cookie.read(name));
    } catch (e) {
      return null;
    }
  }
  function updateConsent(change) {
    if (Core) {
      Core.update(change);
    }
  }
  // Category toggles in the banner and the settings modal.
  function collectCategories() {
    var categories = {};
    jQuery(".gdpr_messagebar_detail input, .gdpr_messagebar_body_button").each(function () {
      var key = jQuery(this).val();
      if (key && jQuery(this).is(":checked")) {
        categories[key] = true;
      }
    });
    return categories;
  }
  function hasCategoryToggles() {
    return jQuery(".gdpr_messagebar_detail input.category-switch-handler, .gdpr_messagebar_body_button").length > 0;
  }
  // Tick the toggles from the consent record. Before a decision they start
  // unticked under the opt-in laws: pre-ticked boxes are not consent.
  function syncCategoryToggles() {
    var record = Core ? Core.get() : null;
    var autoTick = GDPR.settings && (GDPR.settings.is_ticked === true || GDPR.settings.is_ticked === "true");
    var optinLaw = !Core || Core.lawKind() !== "optout";
    jQuery(".gdpr_messagebar_detail input, .gdpr_messagebar_body_button").each(function () {
      var key = jQuery(this).val();
      if (!key || key === "necessary" || jQuery(this).attr("type") === "hidden") {
        return;
      }
      var checked;
      if (record && record.categories) {
        checked = record.categories[key] === true;
      } else {
        checked = autoTick && !optinLaw;
      }
      jQuery(this).prop("checked", checked);
    });
  }

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
      // changing the color and background of cookie setting button.
      

      // bypassed consent.
      document.addEventListener("click", function (e) {
        const closeBtn = e.target.closest("#cookie-banner-cancle-img");
        if (closeBtn) {
          e.stopPropagation();
          e.preventDefault();
          GDPR.bypassed_close();
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
      // Do Not Track and Global Privacy Control are applied by the consent core
      // before this script runs; the banner then sees the recorded decision.

      if (
        gdpr_follows_gdpr_branch(this.settings.cookie_usage_for) ||
        this.settings.cookie_usage_for == "eprivacy" ||
        this.settings.cookie_usage_for == "both" ||
        this.settings.cookie_usage_for == "lgpd"
      ) {
        var gdpr_user_preference = readJsonCookie("wpl_user_preference");
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
              if (Core) {
                Core.setLaw(response.render_law);
              }
            }
            // Outside the targeted region there is no banner, and so no consent
            // to wait for: the core releases everything.
            if (Core) {
              Core.setGeo(response.geo_status === "on");
            }

            // For the GDPR & CCPA
            if ("both" == cookie_for) {
              jQuery("#donot_sell_checkbox").prop("checked", isCcpaOptedOut());
              if (hasGdprDecision() && hasCcpaDecision()) {
                if (response.eu_status != "on") {
                  $("#gdpr-cookie-consent-show-again").addClass(
                    "hide_show_again_dnt"
                  );
                }
                GDPR.hideHeader();
              } else if (hasGdprDecision() && !hasCcpaDecision()) {
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
                GDPR.collectAbTestingData("no choice");
              } else if (
                !hasGdprDecision() &&
                hasCcpaDecision() &&
                response.eu_status == "on"
              ) {
                GDPR.displayHeader(
                  false,
                  true,
                  false,
                  force_display_bar,
                  force_display_show_again
                );
                GDPR.collectAbTestingData("no choice");
              } else if (!hasGdprDecision() && !hasCcpaDecision()) {
                GDPR.checkEuAndCCPAStatus(response);
                GDPR.collectAbTestingData("no choice");
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
              if (!hasGdprDecision()) {
                if (response.geo_status === "on") {
                  GDPR.displayHeader();
                } else {
                  $("#gdpr-cookie-consent-bar").addClass("hide_show_again_dnt");
                }
                GDPR.collectAbTestingData("no choice");
              } else {
                if (response.geo_status != "on") {
                  $("#gdpr-cookie-consent-show-again").addClass(
                    "hide_show_again_dnt"
                  );
                }
                GDPR.hideHeader();
              }
            } else if ("ccpa" == cookie_for || "us_state_laws" == cookie_for) {
              if (!hasCcpaDecision()) {
                if (response.geo_status === "on") {
                  GDPR.displayHeader();
                } else {
                  $("#gdpr-cookie-consent-bar").addClass("hide_show_again_dnt");
                  $("#ccpa-cookie-consent-show-again").addClass("hide_show_again_dnt"); //check this
                }
                GDPR.collectAbTestingData("no choice");
              } else {
                jQuery("#donot_sell_checkbox").prop("checked", isCcpaOptedOut());
                if (response.geo_status != "on") {
                  $("#ccpa-cookie-consent-show-again").addClass(
                    "hide_show_again_dnt"
                  );
                }
                GDPR.hideHeader();
              }
            }
          }
        },
        error: function () {
          if (!hasGdprDecision() && !hasCcpaDecision()) {
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
      } else if (response.eu_status == "off" && response.ccpa_status == "on") {
        GDPR.displayHeader(true, false);
      }
      if (response.eu_status == "on" && response.ccpa_status == "on") {
        GDPR.displayHeader(false, false);
      }
      if (response.eu_status == "off" && response.ccpa_status == "off") {
        GDPR.hideHeader(true);
        GDPR.displayHeader(false, false);
      }
    },
    /**
     * A/B testing tally of what the visitor did with the banner. Only the
     * first decision counts, so callers check hasGdprDecision() first.
     */
    collectAbTestingData: function (user_preference) {
      jQuery.ajax({
        url: log_obj.ajax_url,
        type: "POST",
        data: {
          action: "gdpr_collect_abtesting_data_action",
          security: log_obj.consent_logging_nonce,
          chosenBanner: Number(chosenBanner),
          user_preference: user_preference,
        },
        success: function (response) {},
      });
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
          var firstDecision = !hasGdprDecision();
          // Without category toggles (the ePrivacy notice) Accept grants all.
          if (hasCategoryToggles()) {
            updateConsent({ action: "accept", categories: collectCategories() });
          } else {
            updateConsent({ action: "accept", all: true });
          }
          syncCategoryToggles();
          var gdpr_user_preference_arr = readJsonCookie("wpl_user_preference") || {};
          if (firstDecision) {
            GDPR.collectAbTestingData(gdpr_user_preference_arr);
          }
          if (is_gcm_debug_on == 'true' || is_gcm_debug_on == true) { debugUpdateConsentState(); }

          GDPR.accept_close();

          var gdpr_viewed_cookie = GDPR_Cookie.read("wpl_viewed_cookie");
          var event;
          if (gdpr_follows_gdpr_branch(GDPR.settings.cookie_usage_for) || GDPR.settings.cookie_usage_for == "lgpd") {
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
          if (event) {
            window.dispatchEvent(event);
          }
        } else if (button_action == "accept_all") {
          var firstDecision = !hasGdprDecision();
          updateConsent({ action: "accept_all", all: true });
          GDPR.acceptAllCookies();
          var gdpr_user_preference_arr = readJsonCookie("wpl_user_preference") || {};
          if (firstDecision) {
            GDPR.collectAbTestingData(gdpr_user_preference_arr);
          }
          if (is_gcm_debug_on == 'true' || is_gcm_debug_on == true) { debugUpdateConsentState(); }

          GDPR.accept_close();
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
        } else if (button_action == "reject") {
          if (!hasGdprDecision()) {
            GDPR.collectAbTestingData("reject");
          }
          updateConsent({ action: "reject" });
          if (is_gcm_debug_on == 'true' || is_gcm_debug_on == true) { debugUpdateConsentState(); }

          GDPR.reject_close();
          new_window = GDPR.settings.button_decline_new_win ? true : false;
          gdpr_user_preference = readJsonCookie("wpl_user_preference");
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

            var hasGDPR  = hasGdprDecision();
            var hasCCPA  = hasCcpaDecision();

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
          // "Cancel" in the Do Not Sell dialog: the visitor does not opt out.
          if (!hasCcpaDecision()) {
            GDPR.collectAbTestingData({
              necessary: "yes",
              marketing: "yes",
              analytics: "yes",
              preferences: "yes",
              unclassified: "yes",
            });
          }
          jQuery('#donot_sell_checkbox').prop('checked', false);
          updateConsent({ action: "optin", optout: false });
          GDPR.ccpa_cancel_close();
          gdpr_optout_cookie = GDPR_Cookie.read("wpl_optout_cookie");

          event = new CustomEvent("GdprCookieConsentOnCancelOptout", {
            detail: {
              wpl_optout_cookie: gdpr_optout_cookie,
            },
          });
          window.dispatchEvent(event);
        } else if (button_action == "confirm") {
          if (!hasCcpaDecision()) {
            GDPR.collectAbTestingData("reject");
          }
          if (jQuery('#donot_sell_checkbox').is(':checked')) {
            updateConsent({ action: "optout", optout: true });
            GDPR.confirm_close();
          } else {
            updateConsent({ action: "optin", optout: false });
            GDPR.ccpa_cancel_close();
          }

          gdpr_optout_cookie = GDPR_Cookie.read("wpl_optout_cookie");

          event = new CustomEvent("GdprCookieConsentOnOptout", {
            detail: {
              wpl_optout_cookie: gdpr_optout_cookie,
            },
          });
          window.dispatchEvent(event);
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
      // Nothing is written before the visitor decides; the toggles only show
      // the recorded choice.
      syncCategoryToggles();
      // "Accept <category> cookies" links in blocked-content placeholders.
      jQuery(document).on("click", ".wpl_manage_current_consent", function (e) {
        e.preventDefault();
        GDPR.displayHeader();
      });
      jQuery(document).on(
        "click",
        "#gdpr-cookie-consent-show-again",
        function (e) {
          e.preventDefault();
          var hasConsent = hasGdprDecision() || hasCcpaDecision();
          
          if (hasConsent) {
              if (GDPR.settings.cookie_usage_for === "eprivacy") {
                  GDPR.bar_elm.show();
                  GDPR.show_again_elm.hide();
                  return false;
              }

              // GDPR, CCPA, LGPD, GDPR&CCPA: Show the popup modal
              GDPR.bar_elm.hide();
              GDPR.show_again_elm.hide();
                
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

    convertToHex: function (hex, opacity) {
      hex = hex.replace("#", "");
      var r = parseInt(hex.substring(0, 2), 16);
      var g = parseInt(hex.substring(2, 4), 16);
      var b = parseInt(hex.substring(4, 6), 16);
      var result = "rgba(" + r + "," + g + "," + b + "," + opacity + ")";
      return result;
    },

    /**
     * Turn a placeholder string into safe nodes. The string can come from page
     * content (data-wpl-placeholder), so it is parsed in an inert document and
     * only text plus the "manage consent" link are kept.
     */
    buildPlaceholderContent: function (html) {
      var nodes = [];
      if (html === null || html === undefined || html === "") {
        return nodes;
      }
      var parsed = new DOMParser().parseFromString(String(html), "text/html");
      jQuery.each(parsed.body.childNodes, function (i, child) {
        if (
          child.nodeType === 1 &&
          child.tagName === "A" &&
          jQuery(child).hasClass("wpl_manage_current_consent")
        ) {
          nodes.push(
            jQuery("<a>", {
              "class": "wpl_manage_current_consent",
              text: child.textContent,
            })[0]
          );
        } else {
          nodes.push(document.createTextNode(child.textContent));
        }
      });
      return nodes;
    },

    // The *_close helpers only update the banner. The decision has already
    // gone through WPLConsent.update(), which also handles reloads.
    ccpa_cancel_close: function () {
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
      if (this.settings.cookie_usage_for == "both") {
        this.check_ccpa_eu();
      }
      return false;
    },

    confirm_close: function () {
      return this.ccpa_cancel_close();
    },

    accept_close: function () {
      if (this.settings.notify_animate_hide) {
        this.bar_elm.slideUp(this.settings.animate_speed_hide);
      } else {
        this.bar_elm.hide();
      }
      if (this.settings.cookie_bar_as == "popup") {
        $("#gdpr-popup").gdprmodal("hide");
      }
      if (this.settings.show_again) {
        this.show_again_elm.slideDown(this.settings.animate_speed_hide);
      }
      if (this.settings.cookie_usage_for == "both") {
        this.check_ccpa_eu(true, true);
      }
      return false;
    },

    reject_close: function () {
      GDPR.disableAllCookies();
      return this.accept_close();
    },

    // Close X: the visitor dismissed the banner without choosing. Nothing
    // non-necessary is granted, and every signal hears about it.
    bypassed_close: function () {
      var firstDecision = !hasGdprDecision();
      var law = GDPR.settings.cookie_usage_for;
      if (law === "ccpa" || law === "us_state_laws") {
        if (!hasCcpaDecision()) {
          updateConsent({ action: "dismiss", optout: Core ? Core.isOptedOut() : false });
        }
      } else {
        updateConsent({ action: "dismiss" });
      }
      GDPR.disableAllCookies();
      if (firstDecision) {
        GDPR.collectAbTestingData("bypass");
      }
      if (this.settings.notify_animate_hide) {
        this.bar_elm.slideUp(this.settings.animate_speed_hide);
      } else {
        this.bar_elm.hide();
      }
      if (this.settings.cookie_bar_as == "popup") {
        $("#gdpr-popup").gdprmodal("hide");
      }
      if (this.settings.show_again) {
        this.show_again_elm.slideDown(this.settings.animate_speed_hide);
      }
      if (this.settings.cookie_usage_for == "both") {
        var insidebanner = document.getElementById("gdpr-cookie-consent-bar");
        if (insidebanner) {
          insidebanner.style.display = "none";
        }
        if (hasGdprDecision() && !hasCcpaDecision()) {
          this.check_ccpa_eu(true, true);
        }
      }
      return false;
    },

    // Untick the category toggles; the record itself is written by the core.
    disableAllCookies: function () {
      jQuery(".gdpr_messagebar_detail input, .gdpr_messagebar_body_button").each(function () {
        var key = jQuery(this).val();
        if (key && key !== "necessary" && jQuery(this).attr("type") !== "hidden") {
          jQuery(this).prop("checked", false);
        }
      });
    },
    // Tick every category toggle; the record itself is written by the core.
    acceptAllCookies: function () {
      jQuery(".gdpr_messagebar_detail input, .gdpr_messagebar_body_button").each(function () {
        var key = jQuery(this).val();
        if (key && key !== "necessary" && jQuery(this).attr("type") !== "hidden") {
          jQuery(this).prop("checked", true);
        }
      });
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
      if (ccpa_flag || hasCcpaDecision()) {
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
  };

  $(document).ready(function () {
    var settings = JSON.parse(gdpr_cookiebar_settings);
    if (settings["notify_animate_show"]) {
      $("#gdpr-cookie-consent-bar").css("display", "none");
      $("#gdpr-cookie-consent-bar").slideDown(500);
    }
    // Blocking, releasing and cookie cleaning are done by the consent core.
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
