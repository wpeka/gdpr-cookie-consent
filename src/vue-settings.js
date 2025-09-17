import Vue, { createApp } from "vue";
import CoreuiVue from "@coreui/vue";
import vSelect from "vue-select";
import { VueEditor, Quill } from "vue2-editor";
import "@coreui/coreui/dist/css/coreui.min.css";
import "vue-select/dist/vue-select.css";
import VueModal from "@kouts/vue-modal";
import "@kouts/vue-modal/dist/vue-modal.css";
import AB_Testing_Chart from "./vue-components/AB_Testing_Chart";
import Tooltip from "./vue-components/tooltip";
import VueApexCharts from "vue-apexcharts";
// Main JS (in UMD format)
import VueTimepicker from "vue2-timepicker";
import { TCModel, TCString, GVL } from "@iabtechlabtcf/core";
// CSS
import "vue2-timepicker/dist/VueTimepicker.css";
import VueIntro from "vue-introjs";
Vue.use(VueIntro);
import "intro.js/introjs.css";
// Import AceEditor
import AceEditor, { data } from "vuejs-ace-editor";

import {
  cilPencil,
  cilSettings,
  cilInfo,
  cibGoogleKeep,
  cibTreehouse,
} from "@coreui/icons";

Vue.use(CoreuiVue);
Vue.use(VueApexCharts);
Vue.component("apexchart", VueApexCharts);
Vue.component("v-select", vSelect);
Vue.component("vue-editor", VueEditor);
Vue.component("v-modal", VueModal);
Vue.component("tooltip", Tooltip);
Vue.component("vue-timepicker", VueTimepicker);
Vue.component("aceeditor", AceEditor);
Vue.component("ab-testing-chart", AB_Testing_Chart);

const j = jQuery.noConflict();
var gen = new Vue({
  el: "#gdpr-cookie-consent-settings-app",
  data() {
    return {
      labelIcon: {},
      labelIconNew: {
        labelOn: "\u2713",
        labelOff: "\uD83D\uDD12",
      },

      isGdprProActive: "1" === settings_obj.is_pro_active,
      disableSwitch: false,
      is_template_changed: false,
      is_auto_template_generated: false,
      processof_auto_template_generated: false,
      is_lang_changed: false,
      is_iabtcf_changed: false,
      is_logo_removed: false,
      is_logo_removed1: false,
      is_logo_removed2: false,
      is_logo_removedML1: false,
      is_logo_added: false,
      save_loading: false,
      edit_discovered_cookie: {},
      edit_discovered_cookie_on: false,
      cookie_scanner_data: '',
      ab_testing_data: '',
      gcm_adver_mode_data: '',
      gcm_scan_flag: false,
      json_templates: settings_obj.templates,
      default_template_json: settings_obj.default_template_json,
      pollingInterval: '',
      appendField: ".gdpr-cookie-consent-settings-container",
      configure_image_url: require("../admin/images/configure-icon.png"),
      progress_bar: require("../admin/images/progress_bar.svg"),
      edit_discovered_cookies_img: require("../admin/images/edit-discovered-cookies.svg"),
      close_round_img: require("../admin/images/Close_round.svg"),
      account_connection: require("../admin/images/account_connection.svg"),
      pluginBasePath: '/wp-content/plugins/gdpr-cookie-consent/includes/templates/logo_images/',
      closeOnBackdrop: true,
      centered: true,
      edit_region: false,
      add_region: false,
      accept_button_popup: false,
      button_readmore_popup: false,
      button_readmore_popup1: false,
      button_readmore_popup2: false,
      revoke_consent_popup: false,
      revoke_consent_popup1: false,
      revoke_consent_popup2: false,
      accept_all_button_popup: false,
      decline_button_popup: false,
      show_script_blocker: false,
      settings_button_popup: false,
      confirm_button_popup: false,
      cancel_button_popup: false,
      opt_out_link_popup: false,
      accept_button_popup1: false,
      accept_all_button_popup1: false,
      decline_button_popup1: false,
      settings_button_popup1: false,
      confirm_button_popup1: false,
      cancel_button_popup1: false,
      opt_out_link_popup1: false,
      accept_button_popup2: false,
      accept_all_button_popup2: false,
      decline_button_popup2: false,
      settings_button_popup2: false,
      confirm_button_popup2: false,
      cancel_button_popup2: false,
      opt_out_link_popup2: false,
      show_more_cookie_design_popup: false,
      show_more_cookie_design_popup: false,
      schedule_scan_show: false,
      show_custom_cookie_popup: false,
      scan_in_progress: false,

      gcm_scan_result: settings_obj.ab_options.hasOwnProperty("wpl_gcm_latest_scan_result")
        ? settings_obj.ab_options["wpl_gcm_latest_scan_result"]
        : "",
      consent_version: settings_obj.the_options.hasOwnProperty(
        "consent_version"
      )
        ? this.stripSlashes(settings_obj.the_options["consent_version"])
        : 1,
      scripts_list_total: settings_obj.script_blocker_settings.hasOwnProperty(
        "scripts_list"
      )
        ? settings_obj.script_blocker_settings.scripts_list["total"]
        : 0,
      scripts_list_data: settings_obj.script_blocker_settings.hasOwnProperty(
        "scripts_list"
      )
        ? settings_obj.script_blocker_settings.scripts_list["data"]
        : [],
      category_list_options:
        settings_obj.script_blocker_settings.hasOwnProperty("category_list")
          ? settings_obj.script_blocker_settings["category_list"]
          : [],

      cookie_is_on:
        settings_obj.the_options.hasOwnProperty("is_on") &&
        (true === settings_obj.the_options["is_on"] ||
          1 === settings_obj.the_options["is_on"])
          ? true
          : false,
      iabtcf_is_on:
        settings_obj.the_options.hasOwnProperty("is_iabtcf_on") &&
        (true === settings_obj.the_options["is_iabtcf_on"] ||
          1 === settings_obj.the_options["is_iabtcf_on"])
          ? true
          : false,
      gacm_is_on:
        settings_obj.the_options.hasOwnProperty("is_gacm_on") &&
        (true === settings_obj.the_options["is_gacm_on"] ||
          1 === settings_obj.the_options["is_gacm_on"] ||
          "true" === settings_obj.the_options["is_gacm_on"])
          ? true
          : false,
      gacm_key: settings_obj.ab_options.hasOwnProperty("gacm_key")
        ? settings_obj.ab_options["gacm_key"]
        : "",
      iabtcf_msg: `We and our <a id = "vendor-link" href = "#" data-toggle = "gdprmodal" data-target = "#gdpr-gdprmodal">836 partners</a> use cookies and other tracking technologies to improve your experience on our website. We may store and/or access information on a device and process personal data, such as your IP address and browsing data, for personalised advertising and content, advertising and content measurement, audience research and services development. Additionally, we may utilize precise geolocation data and identification through device scanning.\n\nPlease note that your consent will be valid across all our subdomains. You can change or withdraw your consent at any time by clicking the “Cookie Settings” button at the bottom of your screen. We respect your choices and are committed to providing you with a transparent and secure browsing experience.`,
      gcm_is_on: settings_obj.the_options.hasOwnProperty("is_gcm_on") && 
        (true === settings_obj.the_options["is_gcm_on"] || 
          "true" === settings_obj.the_options["is_gcm_on"] ||
          1 === settings_obj.the_options["is_gcm_on"])
          ? true
          : false,
      gcm_wait_for_update_duration: settings_obj.the_options.hasOwnProperty(
        "gcm_wait_for_update_duration"
      )
        ? settings_obj.the_options["gcm_wait_for_update_duration"]
        : "500",
      gcm_url_passthrough: settings_obj.the_options.hasOwnProperty("is_gcm_url_passthrough") && 
        (true === settings_obj.the_options["is_gcm_url_passthrough"] ||
          "true" === settings_obj.the_options["is_gcm_url_passthrough"] ||
          1 === settings_obj.the_options["is_gcm_url_passthrough"])
          ? true
          : false,
      gcm_ads_redact: settings_obj.the_options.hasOwnProperty("is_gcm_ads_redact") && 
        (true === settings_obj.the_options["is_gcm_ads_redact"] ||
          "true" === settings_obj.the_options["is_gcm_ads_redact"] ||
          1 === settings_obj.the_options["is_gcm_ads_redact"])
          ? true
          : false,
      gcm_debug_mode: settings_obj.the_options.hasOwnProperty("is_gcm_debug_mode") && 
        (true === settings_obj.the_options["is_gcm_debug_mode"] ||
          "true" === settings_obj.the_options["is_gcm_debug_mode"] ||
          1 === settings_obj.the_options["is_gcm_debug_mode"])
          ? true
          : false,
      gcm_advertiser_mode: settings_obj.the_options.hasOwnProperty("is_gcm_advertiser_mode") && 
        (true === settings_obj.the_options["is_gcm_advertiser_mode"] ||
          "true" === settings_obj.the_options["is_gcm_advertiser_mode"] ||
          1 === settings_obj.the_options["is_gcm_advertiser_mode"])
          ? true
          : false,
      regions: settings_obj.the_options.hasOwnProperty('gcm_defaults') ? JSON.parse(settings_obj.the_options["gcm_defaults"]) : [
        {
          region: 'All',
          ad_storage: 'denied',
          analytics_storage: 'denied',
          ad_user_data: 'denied',
          ad_personalization: 'denied',
          functionality_storage: 'granted',
          personalization_storage: 'denied',
          security_storage: 'granted'
        },
      ],
      newRegion: {
        region: 'All',
        ad_storage: false,
        analytics_storage: false,
        ad_user_data: false,
        ad_personalization: false,
        functionality_storage: true,
        personalization_storage: false,
        security_storage: true
      },
      dynamic_lang_is_on:
        settings_obj.the_options.hasOwnProperty("is_dynamic_lang_on") &&
        (true === settings_obj.the_options["is_dynamic_lang_on"] ||
          1 === settings_obj.the_options["is_dynamic_lang_on"] ||
          "true" === settings_obj.the_options["is_dynamic_lang_on"])
          ? true
          : false,
      banner_preview_is_on:
        "true" == settings_obj.the_options["banner_preview_enable"] ||
        1 === settings_obj.the_options["banner_preview_enable"]
          ? true
          : false,
      policy_options: settings_obj.policies,
      gdpr_policy: settings_obj.the_options.hasOwnProperty("cookie_usage_for")
        ? settings_obj.the_options["cookie_usage_for"]
        : "gdpr",
      is_gdpr:
        this.gdpr_policy === "gdpr" || this.gdpr_policy === "both"
          ? true
          : false,
      is_ccpa:
        this.gdpr_policy === "ccpa" || this.gdpr_policy === "both"
          ? true
          : false,
      is_lgpd: this.gdpr_policy === "lgpd" ? true : false,
      is_eprivacy: this.gdpr_policy === "eprivacy" ? true : false,
      eprivacy_message: settings_obj.the_options.hasOwnProperty(
        "notify_message_eprivacy"
      )
        ? this.stripSlashes(settings_obj.the_options["notify_message_eprivacy"])
        : "This website uses cookies to improve your experience. We'll assume you're ok with this, but you can opt-out if you wish.",
      gdpr_message_heading: settings_obj.the_options.hasOwnProperty(
        "bar_heading_text"
      )
        ? this.stripSlashes(settings_obj.the_options["bar_heading_text"])
        : "",
      lgpd_message_heading: settings_obj.the_options.hasOwnProperty(
        "bar_heading_lgpd_text"
      )
        ? this.stripSlashes(settings_obj.the_options["bar_heading_lgpd_text"])
        : "",
      gdpr_message: settings_obj.the_options.hasOwnProperty("notify_message")
        ? this.stripSlashes(settings_obj.the_options["notify_message"])
        : "This website uses cookies to improve your experience. We'll assume you're ok with this, but you can opt-out if you wish.",
      lgpd_message: settings_obj.the_options.hasOwnProperty(
        "notify_message_lgpd"
      )
        ? this.stripSlashes(settings_obj.the_options["notify_message_lgpd"])
        : "This website uses cookies for technical and other purposes as specified in the cookie policy. We'll assume you're ok with this, but you can opt-out if you wish.",
      gdpr_about_cookie_message: settings_obj.the_options.hasOwnProperty(
        "about_message"
      )
        ? this.stripSlashes(settings_obj.the_options["about_message"])
        : "Cookies are small text files that can be used by websites to make a user's experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.",
      lgpd_about_cookie_message: settings_obj.the_options.hasOwnProperty(
        "about_message_lgpd"
      )
        ? this.stripSlashes(settings_obj.the_options["about_message_lgpd"])
        : "Cookies are small text files that can be used by websites to make a user's experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.",
      ccpa_message: settings_obj.the_options.hasOwnProperty(
        "notify_message_ccpa"
      )
        ? this.stripSlashes(settings_obj.the_options["notify_message_ccpa"])
        : "In case of sale of your personal information, you may opt out by using the link",
      ccpa_optout_message: settings_obj.the_options.hasOwnProperty(
        "optout_text"
      )
        ? this.stripSlashes(settings_obj.the_options["optout_text"])
        : "Do you really wish to opt-out?",
      show_visitor_conditions:
        this.is_ccpa || (this.is_gdpr && "1" === settings_obj.is_pro_active)
          ? true
          : false,
      selectedRadioIab:
        settings_obj.the_options.hasOwnProperty("is_ccpa_iab_on") &&
        (true === settings_obj.the_options["is_ccpa_iab_on"] ||
          1 === settings_obj.the_options["is_ccpa_iab_on"])
          ? "yes"
          : "no",
      is_iab_on:
        settings_obj.the_options.hasOwnProperty("is_ccpa_iab_on") &&
        (true === settings_obj.the_options["is_ccpa_iab_on"] ||
          1 === settings_obj.the_options["is_ccpa_iab_on"])
          ? true
          : false,
      is_eu_on:
        settings_obj.the_options.hasOwnProperty("is_eu_on") &&
        (true === settings_obj.the_options["is_eu_on"] ||
          1 === settings_obj.the_options["is_eu_on"])
          ? true
          : false,
      is_ccpa_on:
        settings_obj.the_options.hasOwnProperty("is_ccpa_on") &&
        (true === settings_obj.the_options["is_ccpa_on"] ||
          1 === settings_obj.the_options["is_ccpa_on"])
          ? true
          : false,
      is_revoke_consent_on:
        settings_obj.the_options.hasOwnProperty("show_again") &&
        (true === settings_obj.the_options["show_again"] ||
          1 === settings_obj.the_options["show_again"])
          ? true
          : false,
      is_revoke_consent_on1:
      settings_obj.the_options.hasOwnProperty("show_again1") &&
      (true === settings_obj.the_options["show_again1"] ||
        1 === settings_obj.the_options["show_again1"])
        ? true
        : false,
      is_revoke_consent_on2:
      settings_obj.the_options.hasOwnProperty("show_again2") &&
      (true === settings_obj.the_options["show_again2"] ||
        1 === settings_obj.the_options["show_again2"])
        ? true
        : false,
      tab_position_options: settings_obj.tab_position_options,
      tab_position: settings_obj.the_options.hasOwnProperty(
        "show_again_position"
      )
        ? settings_obj.the_options["show_again_position"]
        : "right",
      tab_position1: settings_obj.the_options.hasOwnProperty(
        "show_again_position1"
      )
        ? settings_obj.the_options["show_again_position1"]
        : "right",
      tab_position2: settings_obj.the_options.hasOwnProperty(
        "show_again_position2"
      )
        ? settings_obj.the_options["show_again_position2"]
        : "right",
      tab_margin: settings_obj.the_options.hasOwnProperty("show_again_margin")
        ? settings_obj.the_options["show_again_margin"]
        : "5",
      tab_margin1: settings_obj.the_options.hasOwnProperty("show_again_margin1")
        ? settings_obj.the_options["show_again_margin1"]
        : "5",
      tab_margin2: settings_obj.the_options.hasOwnProperty("show_again_margin2")
        ? settings_obj.the_options["show_again_margin2"]
        : "5",
      tab_text: settings_obj.the_options.hasOwnProperty("show_again_text")
        ? settings_obj.the_options["show_again_text"]
        : "Cookie Settings",
      tab_text1: settings_obj.the_options.hasOwnProperty("show_again_text1")
        ? settings_obj.the_options["show_again_text1"]
        : "Cookie Settings",
      tab_text2: settings_obj.the_options.hasOwnProperty("show_again_text2")
        ? settings_obj.the_options["show_again_text2"]
        : "Cookie Settings",
      show_revoke_card: this.is_gdpr || this.is_eprivacy,
      autotick:
        settings_obj.the_options.hasOwnProperty("is_ticked") &&
        (true === settings_obj.the_options["is_ticked"] ||
          1 === settings_obj.the_options["is_ticked"])
          ? true
          : false,
      auto_hide:
        settings_obj.the_options.hasOwnProperty("auto_hide") &&
        (true === settings_obj.the_options["auto_hide"] ||
          1 === settings_obj.the_options["auto_hide"])
          ? true
          : false,
      auto_hide_delay: settings_obj.the_options.hasOwnProperty(
        "auto_hide_delay"
      )
        ? settings_obj.the_options["auto_hide_delay"]
        : "10000",
      auto_banner_initialize:
        settings_obj.the_options.hasOwnProperty("auto_banner_initialize") &&
        (true === settings_obj.the_options["auto_banner_initialize"] ||
          1 === settings_obj.the_options["auto_banner_initialize"])
          ? true
          : false,
      auto_generated_banner:
        settings_obj.the_options.hasOwnProperty("auto_generated_banner") &&
        (true === settings_obj.the_options["auto_generated_banner"] ||
          1 === settings_obj.the_options["auto_generated_banner"])
          ? true
          : false,
      auto_banner_initialize_delay: settings_obj.the_options.hasOwnProperty(
        "auto_banner_initialize_delay"
      )
        ? settings_obj.the_options["auto_banner_initialize_delay"]
        : "10000",
      auto_scroll:
        settings_obj.the_options.hasOwnProperty("auto_scroll") &&
        (true === settings_obj.the_options["auto_scroll"] ||
          1 === settings_obj.the_options["auto_scroll"])
          ? true
          : false,
      auto_click:
        settings_obj.the_options.hasOwnProperty("auto_click") &&
        (true === settings_obj.the_options["auto_click"] ||
          1 === settings_obj.the_options["auto_click"])
          ? true
          : false,
      auto_scroll_offset: settings_obj.the_options.hasOwnProperty(
        "auto_scroll_offset"
      )
        ? settings_obj.the_options["auto_scroll_offset"]
        : "10",
      auto_scroll_reload:
        settings_obj.the_options.hasOwnProperty("auto_scroll_reload") &&
        (true === settings_obj.the_options["auto_scroll_reload"] ||
          1 === settings_obj.the_options["auto_scroll_reload"])
          ? true
          : false,
      accept_reload:
        settings_obj.the_options.hasOwnProperty("accept_reload") &&
        (true === settings_obj.the_options["accept_reload"] ||
          1 === settings_obj.the_options["accept_reload"])
          ? true
          : false,
      decline_reload:
        settings_obj.the_options.hasOwnProperty("decline_reload") &&
        (true === settings_obj.the_options["decline_reload"] ||
          1 === settings_obj.the_options["decline_reload"])
          ? true
          : false,
      delete_on_deactivation:
        settings_obj.the_options.hasOwnProperty("delete_on_deactivation") &&
        (true === settings_obj.the_options["delete_on_deactivation"] ||
          1 === settings_obj.the_options["delete_on_deactivation"])
          ? true
          : false,
      show_credits:
        settings_obj.the_options.hasOwnProperty("show_credits") &&
        (true === settings_obj.the_options["show_credits"] ||
          1 === settings_obj.the_options["show_credits"])
          ? true
          : false,
      cookie_expiry_options: settings_obj.cookie_expiry_options,
      cookie_expiry: settings_obj.the_options.hasOwnProperty("cookie_expiry")
        ? settings_obj.the_options["cookie_expiry"]
        : "365",
      logging_on:
        settings_obj.the_options.hasOwnProperty("logging_on") &&
        (true === settings_obj.the_options["logging_on"] ||
          1 === settings_obj.the_options["logging_on"])
          ? true
          : false,
      list_of_contents: settings_obj.list_of_contents,
      restrict_posts: settings_obj.the_options.hasOwnProperty("restrict_posts")
        ? settings_obj.the_options["restrict_posts"]
        : [],
      restrict_array: [],
      button_readmore_is_on:
        settings_obj.the_options.hasOwnProperty("button_readmore_is_on") &&
        (true === settings_obj.the_options["button_readmore_is_on"] ||
          1 === settings_obj.the_options["button_readmore_is_on"])
          ? true
          : false,
      button_readmore_is_on1:
        settings_obj.the_options.hasOwnProperty("button_readmore_is_on1") &&
        (true === settings_obj.the_options["button_readmore_is_on1"] ||
          1 === settings_obj.the_options["button_readmore_is_on1"])
          ? true
          : false,
      button_readmore_is_on2:
      settings_obj.the_options.hasOwnProperty("button_readmore_is_on2") &&
      (true === settings_obj.the_options["button_readmore_is_on2"] ||
        1 === settings_obj.the_options["button_readmore_is_on2"])
        ? true
        : false,
      button_readmore_text: settings_obj.the_options.hasOwnProperty(
        "button_readmore_text"
      )
        ? settings_obj.the_options["button_readmore_text"]
        : "Read More",
      button_readmore_link_color: settings_obj.the_options.hasOwnProperty(
        "button_readmore_link_color"
      )
        ? settings_obj.the_options["button_readmore_link_color"]
        : "#359bf5",
      show_as_options: settings_obj.show_as_options,
      button_readmore_as_button:
        settings_obj.the_options.hasOwnProperty("button_readmore_as_button") &&
        (true === settings_obj.the_options["button_readmore_as_button"] ||
          1 === settings_obj.the_options["button_readmore_as_button"])
          ? true
          : false,
      url_type_options: settings_obj.url_type_options,
      button_readmore_url_type:
        settings_obj.the_options.hasOwnProperty("button_readmore_url_type") &&
        (false === settings_obj.the_options["button_readmore_url_type"] ||
          0 === settings_obj.the_options["button_readmore_url_type"])
          ? false
          : true,
      privacy_policy_options: settings_obj.privacy_policy_options,
      button_readmore_page: settings_obj.the_options.hasOwnProperty(
        "button_readmore_page"
      )
        ? settings_obj.the_options["button_readmore_page"]
        : "0",
      readmore_page: "",
      readmore_page1: "",
      readmore_page2: "",
      button_readmore_wp_page:
        settings_obj.the_options.hasOwnProperty("button_readmore_wp_page") &&
        (true === settings_obj.the_options["button_readmore_wp_page"] ||
          1 === settings_obj.the_options["button_readmore_wp_page"])
          ? true
          : false,
      button_readmore_new_win:
        settings_obj.the_options.hasOwnProperty("button_readmore_new_win") &&
        (true === settings_obj.the_options["button_readmore_new_win"] ||
          1 === settings_obj.the_options["button_readmore_new_win"])
          ? true
          : false,
      button_readmore_url: settings_obj.the_options.hasOwnProperty(
        "button_readmore_url"
      )
        ? settings_obj.the_options["button_readmore_url"]
        : "#",
      button_readmore_button_color: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_color"
      )
        ? settings_obj.the_options["button_readmore_button_color"]
        : "#000000",
      button_readmore_button_opacity: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_opacity"
      )
        ? settings_obj.the_options["button_readmore_button_opacity"]
        : "1",
      button_readmore_button_border_style:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_style"
        )
          ? settings_obj.the_options["button_readmore_button_border_style"]
          : "none",
      button_readmore_button_border_width:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_width"
        )
          ? settings_obj.the_options["button_readmore_button_border_width"]
          : "0",
      button_readmore_button_border_color:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_color"
        )
          ? settings_obj.the_options["button_readmore_button_border_color"]
          : "#000000",
      button_readmore_button_border_radius:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_radius"
        )
          ? settings_obj.the_options["button_readmore_button_border_radius"]
          : "0",
      button_readmore_button_size: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_size"
      )
        ? settings_obj.the_options["button_readmore_button_size"]
        : "medium",
      button_size_options: settings_obj.button_size_options,
      banner_preview: true,
      show_cookie_as_options: settings_obj.show_cookie_as_options,
      show_language_as_options: settings_obj.show_language_as_options,
      schedule_scan_options: settings_obj.schedule_scan_options,
      schedule_scan_as: settings_obj.the_options.hasOwnProperty(
        "schedule_scan_type"
      )
        ? settings_obj.the_options["schedule_scan_type"]
        : "never", //schedule scan type
      schedule_scan_day_options: settings_obj.schedule_scan_day_options,
      schedule_scan_day: settings_obj.the_options.hasOwnProperty("scan_day")
        ? settings_obj.the_options["scan_day"]
        : "Day 1", //scan day
      schedule_scan_time_value: settings_obj.the_options.hasOwnProperty(
        "scan_time"
      )
        ? settings_obj.the_options["scan_time"]
        : "8:00 PM", //scan time
      schedule_scan_date: settings_obj.the_options.hasOwnProperty("scan_date")
        ? settings_obj.the_options["scan_date"]
        : new Date(), //scan date
      next_scan_is_when: settings_obj.the_options.hasOwnProperty(
        "schedule_scan_when"
      )
        ? settings_obj.the_options["schedule_scan_when"]
        : "Not Scheduled", //next scan when
      show_language_as: settings_obj.the_options.hasOwnProperty("lang_selected")
        ? settings_obj.the_options["lang_selected"]
        : "en",
      show_cookie_as: settings_obj.the_options.hasOwnProperty("cookie_bar_as")
        ? settings_obj.the_options["cookie_bar_as"]
        : "banner",
      cookie_position_options: settings_obj.position_options,
      cookie_position: settings_obj.the_options.hasOwnProperty(
        "notify_position_vertical"
      )
        ? settings_obj.the_options["notify_position_vertical"]
        : "bottom",
      cookie_widget_position_options: settings_obj.widget_position_options,
      cookie_widget_position: settings_obj.the_options.hasOwnProperty(
        "notify_position_horizontal"
      )
        ? settings_obj.the_options["notify_position_horizontal"]
        : "left",
      cookie_add_overlay:
        settings_obj.the_options.hasOwnProperty("popup_overlay") &&
        (true === settings_obj.the_options["popup_overlay"] ||
          1 === settings_obj.the_options["popup_overlay"])
          ? true
          : false,
      on_hide_options: settings_obj.on_hide_options,
      on_load_options: settings_obj.on_load_options,
      cookie_bar_color: settings_obj.the_options.hasOwnProperty("background")
        ? settings_obj.the_options["background"]
        : "#ffffff",
      on_hide:
        settings_obj.the_options.hasOwnProperty("notify_animate_hide") &&
        (true === settings_obj.the_options["notify_animate_hide"] ||
          1 === settings_obj.the_options["notify_animate_hide"])
          ? true
          : false,
      on_load:
        settings_obj.the_options.hasOwnProperty("notify_animate_show") &&
        (true === settings_obj.the_options["notify_animate_show"] ||
          1 === settings_obj.the_options["notify_animate_show"])
          ? true
          : false,
      cookie_text_color: settings_obj.the_options.hasOwnProperty("text")
        ? settings_obj.the_options["text"]
        : "#000000",
      cookie_bar_opacity: settings_obj.the_options.hasOwnProperty("opacity")
        ? settings_obj.the_options["opacity"]
        : "0.80",
      cookie_bar_border_width: settings_obj.the_options.hasOwnProperty(
        "background_border_width"
      )
        ? settings_obj.the_options["background_border_width"]
        : "0",
      border_style_options: settings_obj.border_style_options,
      border_style: settings_obj.the_options.hasOwnProperty(
        "background_border_style"
      )
        ? settings_obj.the_options["background_border_style"]
        : "none",
      cookie_border_color: settings_obj.the_options.hasOwnProperty(
        "background_border_color"
      )
        ? settings_obj.the_options["background_border_color"]
        : "#ffffff",
      cookie_bar_border_radius: settings_obj.the_options.hasOwnProperty(
        "background_border_radius"
      )
        ? settings_obj.the_options["background_border_radius"]
        : "0",
      font_options: settings_obj.font_options,
      cookie_font: settings_obj.the_options.hasOwnProperty("font_family")
        ? settings_obj.the_options["font_family"]
        : "inherit",
      cookie_accept_on:
        settings_obj.the_options.hasOwnProperty("button_accept_is_on") &&
        (true === settings_obj.the_options["button_accept_is_on"] ||
          1 === settings_obj.the_options["button_accept_is_on"])
          ? true
          : false,
      accept_text: settings_obj.the_options.hasOwnProperty("button_accept_text")
        ? settings_obj.the_options["button_accept_text"]
        : "Accept",
      accept_text_color: settings_obj.the_options.hasOwnProperty(
        "button_accept_link_color"
      )
        ? settings_obj.the_options["button_accept_link_color"]
        : "#ffffff",
      accept_action_options: settings_obj.accept_action_options,
      accept_action: settings_obj.the_options.hasOwnProperty(
        "button_accept_action"
      )
        ? settings_obj.the_options["button_accept_action"]
        : "#cookie_action_close_header",
      accept_url: settings_obj.the_options.hasOwnProperty("button_accept_url")
        ? settings_obj.the_options["button_accept_url"]
        : "#",
      is_open_url:
        this.accept_action === "#cookie_action_close_header" ? false : true,
      accept_as_button_options: settings_obj.accept_button_as_options,
      gcm_permission_options: settings_obj.gcm_permission_options,
      accept_as_button:
        settings_obj.the_options.hasOwnProperty("button_accept_as_button") &&
        (true === settings_obj.the_options["button_accept_as_button"] ||
          1 === settings_obj.the_options["button_accept_as_button"])
          ? true
          : false,
      open_url_options: settings_obj.open_url_options,
      open_url:
        settings_obj.the_options.hasOwnProperty("button_accept_new_win") &&
        (true === settings_obj.the_options["button_accept_new_win"] ||
          1 === settings_obj.the_options["button_accept_new_win"])
          ? true
          : false,
      accept_background_color: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_color"
      )
        ? settings_obj.the_options["button_accept_button_color"]
        : "#18a300",
      accept_opacity: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_opacity"
      )
        ? settings_obj.the_options["button_accept_button_opacity"]
        : "1",
      accept_style: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_style"
      )
        ? settings_obj.the_options["button_accept_button_border_style"]
        : "none",
      accept_border_color: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_color"
      )
        ? settings_obj.the_options["button_accept_button_border_color"]
        : "#18a300",
      accept_border_width: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_width"
      )
        ? settings_obj.the_options["button_accept_button_border_width"]
        : "0",
      accept_border_radius: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_radius"
      )
        ? settings_obj.the_options["button_accept_button_border_radius"]
        : "0",
      cookie_decline_on:
        settings_obj.the_options.hasOwnProperty("button_decline_is_on") &&
        (true === settings_obj.the_options["button_decline_is_on"] ||
          1 === settings_obj.the_options["button_decline_is_on"])
          ? true
          : false,
      decline_text: settings_obj.the_options.hasOwnProperty(
        "button_decline_text"
      )
        ? settings_obj.the_options["button_decline_text"]
        : "Decline",
      decline_text_color: settings_obj.the_options.hasOwnProperty(
        "button_decline_link_color"
      )
        ? settings_obj.the_options["button_decline_link_color"]
        : "#ffffff",
      decline_as_button:
        settings_obj.the_options.hasOwnProperty("button_decline_as_button") &&
        (true === settings_obj.the_options["button_decline_as_button"] ||
          1 === settings_obj.the_options["button_decline_as_button"])
          ? true
          : false,
      decline_background_color: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_color"
      )
        ? settings_obj.the_options["button_decline_button_color"]
        : "#333333",
      decline_opacity: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_opacity"
      )
        ? settings_obj.the_options["button_decline_button_opacity"]
        : "1",
      decline_style: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_style"
      )
        ? settings_obj.the_options["button_decline_button_border_style"]
        : "none",
      decline_border_color: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_color"
      )
        ? settings_obj.the_options["button_decline_button_border_color"]
        : "#333333",
      decline_border_width: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_width"
      )
        ? settings_obj.the_options["button_decline_button_border_width"]
        : "0",
      decline_border_radius: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_radius"
      )
        ? settings_obj.the_options["button_decline_button_border_radius"]
        : "0",
      decline_action: settings_obj.the_options.hasOwnProperty(
        "button_decline_action"
      )
        ? settings_obj.the_options["button_decline_action"]
        : "#cookie_action_close_header_reject",
      decline_action_options: settings_obj.decline_action_options,
      decline_open_url:
        this.decline_action === "#cookie_action_close_header_reject"
          ? false
          : true,
      decline_url: settings_obj.the_options.hasOwnProperty("button_decline_url")
        ? settings_obj.the_options["button_decline_url"]
        : "#",
      open_decline_url:
        settings_obj.the_options.hasOwnProperty("button_decline_new_win") &&
        (true === settings_obj.the_options["button_decline_new_win"] ||
          1 === settings_obj.the_options["button_decline_new_win"])
          ? true
          : false,
      cookie_settings_on:
        settings_obj.the_options.hasOwnProperty("button_settings_is_on") &&
        (true === settings_obj.the_options["button_settings_is_on"] ||
          1 === settings_obj.the_options["button_settings_is_on"])
          ? true
          : false,
      is_banner: this.show_cookie_as === "banner" ? true : false,
      
      settings_text: settings_obj.the_options.hasOwnProperty(
        "button_settings_text"
      )
        ? settings_obj.the_options["button_settings_text"]
        : "Cookie Settings",
      settings_text_color: settings_obj.the_options.hasOwnProperty(
        "button_settings_link_color"
      )
        ? settings_obj.the_options["button_settings_link_color"]
        : "#ffffff",
      settings_as_button:
        settings_obj.the_options.hasOwnProperty("button_settings_as_button") &&
        (true === settings_obj.the_options["button_settings_as_button"] ||
          1 === settings_obj.the_options["button_settings_as_button"])
          ? true
          : false,
      settings_background_color: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_color"
      )
        ? settings_obj.the_options["button_settings_button_color"]
        : "#333333",
      settings_opacity: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_opacity"
      )
        ? settings_obj.the_options["button_settings_button_opacity"]
        : "1",
      settings_style: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_style"
      )
        ? settings_obj.the_options["button_settings_button_border_style"]
        : "none",
      settings_border_color: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_color"
      )
        ? settings_obj.the_options["button_settings_button_border_color"]
        : "#333333",
      settings_border_width: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_width"
      )
        ? settings_obj.the_options["button_settings_button_border_width"]
        : "0",
      settings_border_radius: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_radius"
      )
        ? settings_obj.the_options["button_settings_button_border_radius"]
        : "0",
      cookie_on_frontend:
        settings_obj.the_options.hasOwnProperty(
          "button_settings_display_cookies"
        ) &&
        (true === settings_obj.the_options["button_settings_display_cookies"] ||
          1 === settings_obj.the_options["button_settings_display_cookies"])
          ? true
          : false,
      confirm_text: settings_obj.the_options.hasOwnProperty(
        "button_confirm_text"
      )
        ? settings_obj.the_options["button_confirm_text"]
        : "Confirm",
      confirm_text_color: settings_obj.the_options.hasOwnProperty(
        "button_confirm_link_color"
      )
        ? settings_obj.the_options["button_confirm_link_color"]
        : "#ffffff",
      confirm_background_color: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_color"
      )
        ? settings_obj.the_options["button_confirm_button_color"]
        : "#18a300",
      confirm_opacity: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_opacity"
      )
        ? settings_obj.the_options["button_confirm_button_opacity"]
        : "1",
      confirm_style: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_style"
      )
        ? settings_obj.the_options["button_confirm_button_border_style"]
        : "none",
      confirm_border_color: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_color"
      )
        ? settings_obj.the_options["button_confirm_button_border_color"]
        : "#18a300",
      confirm_border_width: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_width"
      )
        ? settings_obj.the_options["button_confirm_button_border_width"]
        : "0",
      confirm_border_radius: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_radius"
      )
        ? settings_obj.the_options["button_confirm_button_border_radius"]
        : "0",
      cancel_text: settings_obj.the_options.hasOwnProperty("button_cancel_text")
        ? settings_obj.the_options["button_cancel_text"]
        : "Cancel",
      cancel_text_color: settings_obj.the_options.hasOwnProperty(
        "button_cancel_link_color"
      )
        ? settings_obj.the_options["button_cancel_link_color"]
        : "#ffffff",
      cancel_background_color: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_color"
      )
        ? settings_obj.the_options["button_cancel_button_color"]
        : "#333333",
      cancel_opacity: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_opacity"
      )
        ? settings_obj.the_options["button_cancel_button_opacity"]
        : "1",
      cancel_style: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_style"
      )
        ? settings_obj.the_options["button_cancel_button_border_style"]
        : "none",
      cancel_border_color: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_color"
      )
        ? settings_obj.the_options["button_cancel_button_border_color"]
        : "#333333",
      cancel_border_width: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_width"
      )
        ? settings_obj.the_options["button_cancel_button_border_width"]
        : "0",
      cancel_border_radius: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_radius"
      )
        ? settings_obj.the_options["button_cancel_button_border_radius"]
        : "0",
      opt_out_text: settings_obj.the_options.hasOwnProperty(
        "button_donotsell_text"
      )
        ? settings_obj.the_options["button_donotsell_text"]
        : "Do Not Sell My Personal Information",
      opt_out_text_color: settings_obj.the_options.hasOwnProperty(
        "button_donotsell_link_color"
      )
        ? settings_obj.the_options["button_donotsell_link_color"]
        : "#359bf5",

      cookie_bar1_name: settings_obj.the_options.hasOwnProperty(
        "cookie_bar1_name"
      )
        ? settings_obj.the_options["cookie_bar1_name"]
        : "Test Banner A",
      default_cookie_bar:
        settings_obj.the_options.hasOwnProperty("default_cookie_bar") &&
        (true == settings_obj.the_options["default_cookie_bar"] ||
          "true" == settings_obj.the_options["default_cookie_bar"] ||
          1 == settings_obj.the_options["default_cookie_bar"])
          ? true
          : false,

      active_test_banner_tab:
        settings_obj.the_options.hasOwnProperty("default_cookie_bar") &&
        (true == settings_obj.the_options["default_cookie_bar"] ||
          "true" == settings_obj.the_options["default_cookie_bar"] ||
          1 == settings_obj.the_options["default_cookie_bar"])
          ? 1
          : 2,
      // Multiple Legislation Data
      active_default_multiple_legislation: "gdpr",
      multiple_legislation_cookie_bar_color1:
        settings_obj.the_options.hasOwnProperty(
          "multiple_legislation_cookie_bar_color1"
        )
          ? settings_obj.the_options["multiple_legislation_cookie_bar_color1"]
          : "#ffffff",
      multiple_legislation_cookie_bar_color2:
        settings_obj.the_options.hasOwnProperty(
          "multiple_legislation_cookie_bar_color2"
        )
          ? settings_obj.the_options["multiple_legislation_cookie_bar_color2"]
          : "#ffffff",
      multiple_legislation_cookie_bar_opacity1:
        settings_obj.the_options.hasOwnProperty(
          "multiple_legislation_cookie_bar_opacity1"
        )
          ? settings_obj.the_options["multiple_legislation_cookie_bar_opacity1"]
          : "1",
      multiple_legislation_cookie_bar_opacity2:
        settings_obj.the_options.hasOwnProperty(
          "multiple_legislation_cookie_bar_opacity2"
        )
          ? settings_obj.the_options["multiple_legislation_cookie_bar_opacity2"]
          : "1",
      multiple_legislation_cookie_text_color1:
        settings_obj.the_options.hasOwnProperty(
          "multiple_legislation_cookie_text_color1"
        )
          ? settings_obj.the_options["multiple_legislation_cookie_text_color1"]
          : "#000000",
      multiple_legislation_cookie_text_color2:
        settings_obj.the_options.hasOwnProperty(
          "multiple_legislation_cookie_text_color2"
        )
          ? settings_obj.the_options["multiple_legislation_cookie_text_color2"]
          : "#000000",
      multiple_legislation_border_style1:
        settings_obj.the_options.hasOwnProperty(
          "multiple_legislation_border_style1"
        )
          ? settings_obj.the_options["multiple_legislation_border_style1"]
          : "none",
      multiple_legislation_border_style2:
        settings_obj.the_options.hasOwnProperty(
          "multiple_legislation_border_style2"
        )
          ? settings_obj.the_options["multiple_legislation_border_style2"]
          : "none",
      multiple_legislation_cookie_bar_border_width1:
        settings_obj.the_options.hasOwnProperty(
          "multiple_legislation_cookie_bar_border_width1"
        )
          ? settings_obj.the_options[
              "multiple_legislation_cookie_bar_border_width1"
            ]
          : "0",
      multiple_legislation_cookie_bar_border_width2:
        settings_obj.the_options.hasOwnProperty(
          "multiple_legislation_cookie_bar_border_width2"
        )
          ? settings_obj.the_options[
              "multiple_legislation_cookie_bar_border_width2"
            ]
          : "0",
      multiple_legislation_cookie_border_color1:
        settings_obj.the_options.hasOwnProperty(
          "multiple_legislation_cookie_border_color1"
        )
          ? settings_obj.the_options[
              "multiple_legislation_cookie_border_color1"
            ]
          : "#ffffff",
      multiple_legislation_cookie_border_color2:
        settings_obj.the_options.hasOwnProperty(
          "multiple_legislation_cookie_border_color2"
        )
          ? settings_obj.the_options[
              "multiple_legislation_cookie_border_color2"
            ]
          : "#ffffff",
      multiple_legislation_cookie_bar_border_radius1:
        settings_obj.the_options.hasOwnProperty(
          "multiple_legislation_cookie_bar_border_radius1"
        )
          ? settings_obj.the_options[
              "multiple_legislation_cookie_bar_border_radius1"
            ]
          : "0",
      multiple_legislation_cookie_bar_border_radius2:
        settings_obj.the_options.hasOwnProperty(
          "multiple_legislation_cookie_bar_border_radius2"
        )
          ? settings_obj.the_options[
              "multiple_legislation_cookie_bar_border_radius2"
            ]
          : "0",
      multiple_legislation_cookie_font1:
        settings_obj.the_options.hasOwnProperty(
          "multiple_legislation_cookie_font1"
        )
          ? settings_obj.the_options["multiple_legislation_cookie_font1"]
          : "inherit",
      multiple_legislation_cookie_font2:
        settings_obj.the_options.hasOwnProperty(
          "multiple_legislation_cookie_font2"
        )
          ? settings_obj.the_options["multiple_legislation_cookie_font2"]
          : "inherit",
      multiple_legislation_accept_all_border_radius1:
        settings_obj.the_options.hasOwnProperty(
          "multiple_legislation_accept_all_border_radius1"
        )
          ? settings_obj.the_options[
              "multiple_legislation_accept_all_border_radius1"
            ]
          : "0",
      cookie_bar_color1: settings_obj.the_options.hasOwnProperty(
        "cookie_bar_color1"
      )
        ? settings_obj.the_options["cookie_bar_color1"]
        : "#ffffff",
      cookie_text_color1: settings_obj.the_options.hasOwnProperty(
        "cookie_text_color1"
      )
        ? settings_obj.the_options["cookie_text_color1"]
        : "#000000",
      cookie_bar_opacity1: settings_obj.the_options.hasOwnProperty(
        "cookie_bar_opacity1"
      )
        ? settings_obj.the_options["cookie_bar_opacity1"]
        : "0.80",
      cookie_bar_border_width1: settings_obj.the_options.hasOwnProperty(
        "cookie_bar_border_width1"
      )
        ? settings_obj.the_options["cookie_bar_border_width1"]
        : "0",
      border_style1: settings_obj.the_options.hasOwnProperty("border_style1")
        ? settings_obj.the_options["border_style1"]
        : "none",
      cookie_border_color1: settings_obj.the_options.hasOwnProperty(
        "cookie_border_color1"
      )
        ? settings_obj.the_options["cookie_border_color1"]
        : "#ffffff",
      cookie_bar_border_radius1: settings_obj.the_options.hasOwnProperty(
        "cookie_bar_border_radius1"
      )
        ? settings_obj.the_options["cookie_bar_border_radius1"]
        : "0",
      cookie_font1: settings_obj.the_options.hasOwnProperty("cookie_font1")
        ? settings_obj.the_options["cookie_font1"]
        : "inherit",
      cookie_accept_on1:
        settings_obj.the_options.hasOwnProperty("button_accept_is_on1") &&
        (false === settings_obj.the_options["button_accept_is_on1"] ||
          0 === settings_obj.the_options["button_accept_is_on1"] ||
          "false" === settings_obj.the_options["button_accept_is_on1"])
          ? false
          : true,
      accept_text1: settings_obj.the_options.hasOwnProperty(
        "button_accept_text1"
      )
        ? settings_obj.the_options["button_accept_text1"]
        : "Accept",
      accept_text_color1: settings_obj.the_options.hasOwnProperty(
        "button_accept_link_color1"
      )
        ? settings_obj.the_options["button_accept_link_color1"]
        : "#ffffff",
      accept_action1: settings_obj.the_options.hasOwnProperty(
        "button_accept_action1"
      )
        ? settings_obj.the_options["button_accept_action1"]
        : "#cookie_action_close_header",
      accept_url1: settings_obj.the_options.hasOwnProperty("button_accept_url1")
        ? settings_obj.the_options["button_accept_url1"]
        : "#",
      accept_as_button1:
        settings_obj.the_options.hasOwnProperty("button_accept_as_button1") &&
        (false === settings_obj.the_options["button_accept_as_button1"] ||
          0 === settings_obj.the_options["button_accept_as_button1"] ||
          "false" === settings_obj.the_options["button_accept_as_button1"])
          ? false
          : true,
      open_url1:
        settings_obj.the_options.hasOwnProperty("button_accept_new_win1") &&
        (true === settings_obj.the_options["button_accept_new_win1"] ||
          1 === settings_obj.the_options["button_accept_new_win1"] ||
          "true" === settings_obj.the_options["button_accept_new_win1"])
          ? true
          : false,
      accept_background_color1: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_color1"
      )
        ? settings_obj.the_options["button_accept_button_color1"]
        : "#18a300",
      accept_opacity1: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_opacity1"
      )
        ? settings_obj.the_options["button_accept_button_opacity1"]
        : "1",
      accept_style1: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_style1"
      )
        ? settings_obj.the_options["button_accept_button_border_style1"]
        : "none",
      accept_border_color1: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_color1"
      )
        ? settings_obj.the_options["button_accept_button_border_color1"]
        : "#18a300",
      accept_border_width1: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_width1"
      )
        ? settings_obj.the_options["button_accept_button_border_width1"]
        : "0",
      accept_border_radius1: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_radius1"
      )
        ? settings_obj.the_options["button_accept_button_border_radius1"]
        : "0",
      cookie_accept_all_on1:
        settings_obj.the_options.hasOwnProperty("button_accept_all_is_on1") &&
        (true === settings_obj.the_options["button_accept_all_is_on1"] ||
          1 === settings_obj.the_options["button_accept_all_is_on1"] ||
          "true" === settings_obj.the_options["button_accept_all_is_on1"])
          ? true
          : false,
      accept_all_text1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_text1"
      )
        ? settings_obj.the_options["button_accept_all_text1"]
        : "Accept All",
      accept_all_text_color1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_link_color1"
      )
        ? settings_obj.the_options["button_accept_all_link_color1"]
        : "#ffffff",
      accept_all_as_button1:
        settings_obj.the_options.hasOwnProperty(
          "button_accept_all_as_button1"
        ) &&
        (false === settings_obj.the_options["button_accept_all_as_button1"] ||
          0 === settings_obj.the_options["button_accept_all_as_button1"] ||
          "false" === settings_obj.the_options["button_accept_all_as_button1"])
          ? false
          : true,
      accept_all_action1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_action1"
      )
        ? settings_obj.the_options["button_accept_all_action1"]
        : "#cookie_action_close_header",
      accept_all_url1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_url1"
      )
        ? settings_obj.the_options["button_accept_all_url1"]
        : "#",
      accept_all_new_win1:
        settings_obj.the_options.hasOwnProperty("button_accept_all_new_win1") &&
        (true === settings_obj.the_options["button_accept_all_new_win1"] ||
          1 === settings_obj.the_options["button_accept_all_new_win1"] ||
          "true" === settings_obj.the_options["button_accept_all_new_win1"])
          ? true
          : false,
      accept_all_background_color1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_button_color1"
      )
        ? settings_obj.the_options["button_accept_all_button_color1"]
        : "#18a300",
      accept_all_style1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_style1"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_style1"]
        : "none",
      accept_all_border_color1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_color1"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_color1"]
        : "#18a300",
      accept_all_opacity1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_opacity1"
      )
        ? settings_obj.the_options["button_accept_all_btn_opacity1"]
        : "1",
      accept_all_border_width1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_width1"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_width1"]
        : "0",
      accept_all_border_radius1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_radius1"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_radius1"]
        : "0",

      cookie_decline_on1:
        settings_obj.the_options.hasOwnProperty("button_decline_is_on1") &&
        (false === settings_obj.the_options["button_decline_is_on1"] ||
          0 === settings_obj.the_options["button_decline_is_on1"] ||
          "false" === settings_obj.the_options["button_decline_is_on1"])
          ? false
          : true,
      decline_text1: settings_obj.the_options.hasOwnProperty(
        "button_decline_text1"
      )
        ? settings_obj.the_options["button_decline_text1"]
        : "Decline",
      decline_text_color1: settings_obj.the_options.hasOwnProperty(
        "button_decline_link_color1"
      )
        ? settings_obj.the_options["button_decline_link_color1"]
        : "#ffffff",
      decline_as_button1:
        settings_obj.the_options.hasOwnProperty("button_decline_as_button1") &&
        (false === settings_obj.the_options["button_decline_as_button1"] ||
          0 === settings_obj.the_options["button_decline_as_button1"] ||
          "false" === settings_obj.the_options["button_decline_as_button1"])
          ? false
          : true,
      decline_background_color1: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_color1"
      )
        ? settings_obj.the_options["button_decline_button_color1"]
        : "#333333",
      decline_opacity1: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_opacity1"
      )
        ? settings_obj.the_options["button_decline_button_opacity1"]
        : "1",
      decline_style1: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_style1"
      )
        ? settings_obj.the_options["button_decline_button_border_style1"]
        : "none",
      decline_border_color1: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_color1"
      )
        ? settings_obj.the_options["button_decline_button_border_color1"]
        : "#333333",
      decline_border_width1: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_width1"
      )
        ? settings_obj.the_options["button_decline_button_border_width1"]
        : "0",
      decline_border_radius1: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_radius1"
      )
        ? settings_obj.the_options["button_decline_button_border_radius1"]
        : "0",
      decline_action1: settings_obj.the_options.hasOwnProperty(
        "button_decline_action1"
      )
        ? settings_obj.the_options["button_decline_action1"]
        : "#cookie_action_close_header_reject",

      decline_url1: settings_obj.the_options.hasOwnProperty(
        "button_decline_url1"
      )
        ? settings_obj.the_options["button_decline_url1"]
        : "#",
      open_decline_url1:
        settings_obj.the_options.hasOwnProperty("button_decline_new_win1") &&
        (true === settings_obj.the_options["button_decline_new_win1"] ||
          1 === settings_obj.the_options["button_decline_new_win1"] ||
          "true" === settings_obj.the_options["button_decline_new_win1"])
          ? true
          : false,

      cookie_settings_on1:
        settings_obj.the_options.hasOwnProperty("button_settings_is_on1") &&
        (false === settings_obj.the_options["button_settings_is_on1"] ||
          0 === settings_obj.the_options["button_settings_is_on1"] ||
          "false" === settings_obj.the_options["button_settings_is_on1"])
          ? false
          : true,

      settings_text1: settings_obj.the_options.hasOwnProperty(
        "button_settings_text1"
      )
        ? settings_obj.the_options["button_settings_text1"]
        : "Cookie Settings",
      settings_text_color1: settings_obj.the_options.hasOwnProperty(
        "button_settings_link_color1"
      )
        ? settings_obj.the_options["button_settings_link_color1"]
        : "#ffffff",
      settings_as_button1:
        settings_obj.the_options.hasOwnProperty("button_settings_as_button1") &&
        (false === settings_obj.the_options["button_settings_as_button1"] ||
          0 === settings_obj.the_options["button_settings_as_button1"] ||
          "false" === settings_obj.the_options["button_settings_as_button1"])
          ? false
          : true,
      settings_background_color1: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_color1"
      )
        ? settings_obj.the_options["button_settings_button_color1"]
        : "#333333",
      settings_opacity1: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_opacity1"
      )
        ? settings_obj.the_options["button_settings_button_opacity1"]
        : "1",
      settings_style1: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_style1"
      )
        ? settings_obj.the_options["button_settings_button_border_style1"]
        : "none",
      settings_border_color1: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_color1"
      )
        ? settings_obj.the_options["button_settings_button_border_color1"]
        : "#333333",
      settings_border_width1: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_width1"
      )
        ? settings_obj.the_options["button_settings_button_border_width1"]
        : "0",
      settings_border_radius1: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_radius1"
      )
        ? settings_obj.the_options["button_settings_button_border_radius1"]
        : "0",
      cookie_on_frontend1:
        settings_obj.the_options.hasOwnProperty(
          "button_settings_display_cookies1"
        ) &&
        (true ===
          settings_obj.the_options["button_settings_display_cookies1"] ||
          1 === settings_obj.the_options["button_settings_display_cookies1"] ||
          "true" ===
            settings_obj.the_options["button_settings_display_cookies1"])
          ? true
          : false,
      confirm_text1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_text1"
      )
        ? settings_obj.the_options["button_confirm_text1"]
        : "Confirm",
      confirm_text_color1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_link_color1"
      )
        ? settings_obj.the_options["button_confirm_link_color1"]
        : "#ffffff",
      confirm_background_color1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_color1"
      )
        ? settings_obj.the_options["button_confirm_button_color1"]
        : "#18a300",
      confirm_opacity1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_opacity1"
      )
        ? settings_obj.the_options["button_confirm_button_opacity1"]
        : "1",
      confirm_style1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_style1"
      )
        ? settings_obj.the_options["button_confirm_button_border_style1"]
        : "none",
      confirm_border_color1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_color1"
      )
        ? settings_obj.the_options["button_confirm_button_border_color1"]
        : "#18a300",
      confirm_border_width1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_width1"
      )
        ? settings_obj.the_options["button_confirm_button_border_width1"]
        : "0",
      confirm_border_radius1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_radius1"
      )
        ? settings_obj.the_options["button_confirm_button_border_radius1"]
        : "0",
      cancel_text1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_text1"
      )
        ? settings_obj.the_options["button_cancel_text1"]
        : "Cancel",
      cancel_text_color1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_link_color1"
      )
        ? settings_obj.the_options["button_cancel_link_color1"]
        : "#ffffff",
      cancel_background_color1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_color1"
      )
        ? settings_obj.the_options["button_cancel_button_color1"]
        : "#333333",
      cancel_opacity1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_opacity1"
      )
        ? settings_obj.the_options["button_cancel_button_opacity1"]
        : "1",
      cancel_style1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_style1"
      )
        ? settings_obj.the_options["button_cancel_button_border_style1"]
        : "none",
      cancel_border_color1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_color1"
      )
        ? settings_obj.the_options["button_cancel_button_border_color1"]
        : "#333333",
      cancel_border_width1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_width1"
      )
        ? settings_obj.the_options["button_cancel_button_border_width1"]
        : "0",
      cancel_border_radius1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_radius1"
      )
        ? settings_obj.the_options["button_cancel_button_border_radius1"]
        : "0",
      opt_out_text1: settings_obj.the_options.hasOwnProperty(
        "button_donotsell_text1"
      )
        ? settings_obj.the_options["button_donotsell_text1"]
        : "Do Not Sell My Personal Information",
      opt_out_text_color1: settings_obj.the_options.hasOwnProperty(
        "button_donotsell_link_color1"
      )
        ? settings_obj.the_options["button_donotsell_link_color1"]
        : "#359bf5",
      button_readmore_text1: settings_obj.the_options.hasOwnProperty(
        "button_readmore_text1"
      )
        ? settings_obj.the_options["button_readmore_text1"]
        : "Read More",
      button_readmore_link_color1: settings_obj.the_options.hasOwnProperty(
        "button_readmore_link_color1"
      )
        ? settings_obj.the_options["button_readmore_link_color1"]
        : "#359bf5",
      button_readmore_as_button1:
        settings_obj.the_options.hasOwnProperty("button_readmore_as_button1") &&
        (true === settings_obj.the_options["button_readmore_as_button1"] ||
          1 === settings_obj.the_options["button_readmore_as_button1"])
          ? true
          : false,
      button_readmore_url_type1:
        settings_obj.the_options.hasOwnProperty("button_readmore_url_type1") &&
        (false === settings_obj.the_options["button_readmore_url_type1"] ||
          0 === settings_obj.the_options["button_readmore_url_type1"])
          ? false
          : true,
      button_readmore_page1: settings_obj.the_options.hasOwnProperty(
        "button_readmore_page1"
      )
        ? settings_obj.the_options["button_readmore_page1"]
        : "0",
      button_readmore_wp_page1:
        settings_obj.the_options.hasOwnProperty("button_readmore_wp_page1") &&
        (true === settings_obj.the_options["button_readmore_wp_page1"] ||
          1 === settings_obj.the_options["button_readmore_wp_page1"])
          ? true
          : false,
      button_readmore_new_win1:
        settings_obj.the_options.hasOwnProperty("button_readmore_new_win1") &&
        (true === settings_obj.the_options["button_readmore_new_win1"] ||
          1 === settings_obj.the_options["button_readmore_new_win1"])
          ? true
          : false,
      button_readmore_url1: settings_obj.the_options.hasOwnProperty(
        "button_readmore_url1"
      )
        ? settings_obj.the_options["button_readmore_url1"]
        : "#",
      button_readmore_button_color1: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_color1"
      )
        ? settings_obj.the_options["button_readmore_button_color1"]
        : "#000000",
      button_readmore_button_opacity1: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_opacity1"
      )
        ? settings_obj.the_options["button_readmore_button_opacity1"]
        : "1",
      button_readmore_button_border_style1:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_style1"
        )
          ? settings_obj.the_options["button_readmore_button_border_style1"]
          : "none",
      button_readmore_button_border_width1:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_width1"
        )
          ? settings_obj.the_options["button_readmore_button_border_width1"]
          : "0",
      button_readmore_button_border_color1:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_color1"
        )
          ? settings_obj.the_options["button_readmore_button_border_color1"]
          : "#000000",
      button_readmore_button_border_radius1:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_radius1"
        )
          ? settings_obj.the_options["button_readmore_button_border_radius1"]
          : "0",
      button_readmore_button_size1: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_size1"
      )
        ? settings_obj.the_options["button_readmore_button_size1"]
        : "medium",  
      cookie_bar2_name: settings_obj.the_options.hasOwnProperty(
        "cookie_bar2_name"
      )
        ? settings_obj.the_options["cookie_bar2_name"]
        : "Test Banner B",

      cookie_bar_color2: settings_obj.the_options.hasOwnProperty(
        "cookie_bar_color2"
      )
        ? settings_obj.the_options["cookie_bar_color2"]
        : "#ffffff",
      cookie_text_color2: settings_obj.the_options.hasOwnProperty(
        "cookie_text_color2"
      )
        ? settings_obj.the_options["cookie_text_color2"]
        : "#000000",
      cookie_bar_opacity2: settings_obj.the_options.hasOwnProperty(
        "cookie_bar_opacity2"
      )
        ? settings_obj.the_options["cookie_bar_opacity2"]
        : "0.80",
      cookie_bar_border_width2: settings_obj.the_options.hasOwnProperty(
        "cookie_bar_border_width2"
      )
        ? settings_obj.the_options["cookie_bar_border_width2"]
        : "0",
      border_style2: settings_obj.the_options.hasOwnProperty("border_style2")
        ? settings_obj.the_options["border_style2"]
        : "none",
      cookie_border_color2: settings_obj.the_options.hasOwnProperty(
        "cookie_border_color2"
      )
        ? settings_obj.the_options["cookie_border_color2"]
        : "#ffffff",
      cookie_bar_border_radius2: settings_obj.the_options.hasOwnProperty(
        "cookie_bar_border_radius2"
      )
        ? settings_obj.the_options["cookie_bar_border_radius2"]
        : "0",
      cookie_font2: settings_obj.the_options.hasOwnProperty("cookie_font2")
        ? settings_obj.the_options["cookie_font2"]
        : "inherit",
      cookie_accept_on2:
        settings_obj.the_options.hasOwnProperty("button_accept_is_on2") &&
        (false === settings_obj.the_options["button_accept_is_on2"] ||
          0 === settings_obj.the_options["button_accept_is_on2"] ||
          "false" === settings_obj.the_options["button_accept_is_on2"])
          ? false
          : true,
      accept_text2: settings_obj.the_options.hasOwnProperty(
        "button_accept_text2"
      )
        ? settings_obj.the_options["button_accept_text2"]
        : "Accept",
      accept_text_color2: settings_obj.the_options.hasOwnProperty(
        "button_accept_link_color2"
      )
        ? settings_obj.the_options["button_accept_link_color2"]
        : "#ffffff",
      accept_action2: settings_obj.the_options.hasOwnProperty(
        "button_accept_action2"
      )
        ? settings_obj.the_options["button_accept_action2"]
        : "#cookie_action_close_header",
      accept_url2: settings_obj.the_options.hasOwnProperty("button_accept_url2")
        ? settings_obj.the_options["button_accept_url2"]
        : "#",
      accept_as_button2:
        settings_obj.the_options.hasOwnProperty("button_accept_as_button2") &&
        (false === settings_obj.the_options["button_accept_as_button2"] ||
          0 === settings_obj.the_options["button_accept_as_button2"] ||
          "false" === settings_obj.the_options["button_accept_as_button2"])
          ? false
          : true,
      open_url2:
        settings_obj.the_options.hasOwnProperty("button_accept_new_win2") &&
        (true === settings_obj.the_options["button_accept_new_win2"] ||
          1 === settings_obj.the_options["button_accept_new_win2"] ||
          "true" === settings_obj.the_options["button_accept_new_win2"])
          ? true
          : false,
      accept_background_color2: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_color2"
      )
        ? settings_obj.the_options["button_accept_button_color2"]
        : "#18a300",
      accept_opacity2: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_opacity2"
      )
        ? settings_obj.the_options["button_accept_button_opacity2"]
        : "1",
      accept_style2: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_style2"
      )
        ? settings_obj.the_options["button_accept_button_border_style2"]
        : "none",
      accept_border_color2: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_color2"
      )
        ? settings_obj.the_options["button_accept_button_border_color2"]
        : "#18a300",
      accept_border_width2: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_width2"
      )
        ? settings_obj.the_options["button_accept_button_border_width2"]
        : "0",
      accept_border_radius2: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_radius2"
      )
        ? settings_obj.the_options["button_accept_button_border_radius2"]
        : "0",
      cookie_accept_all_on2:
        settings_obj.the_options.hasOwnProperty("button_accept_all_is_on2") &&
        (true === settings_obj.the_options["button_accept_all_is_on2"] ||
          1 === settings_obj.the_options["button_accept_all_is_on2"] ||
          "true" === settings_obj.the_options["button_accept_all_is_on2"])
          ? true
          : false,
      accept_all_text2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_text2"
      )
        ? settings_obj.the_options["button_accept_all_text2"]
        : "Accept All",
      accept_all_text_color2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_link_color2"
      )
        ? settings_obj.the_options["button_accept_all_link_color2"]
        : "#ffffff",
      accept_all_as_button2:
        settings_obj.the_options.hasOwnProperty(
          "button_accept_all_as_button2"
        ) &&
        (false === settings_obj.the_options["button_accept_all_as_button2"] ||
          0 === settings_obj.the_options["button_accept_all_as_button2"] ||
          "false" === settings_obj.the_options["button_accept_all_as_button2"])
          ? false
          : true,
      accept_all_action2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_action2"
      )
        ? settings_obj.the_options["button_accept_all_action2"]
        : "#cookie_action_close_header",
      accept_all_url2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_url2"
      )
        ? settings_obj.the_options["button_accept_all_url2"]
        : "#",
      accept_all_new_win2:
        settings_obj.the_options.hasOwnProperty("button_accept_all_new_win2") &&
        (true === settings_obj.the_options["button_accept_all_new_win2"] ||
          1 === settings_obj.the_options["button_accept_all_new_win2"] ||
          "true" === settings_obj.the_options["button_accept_all_new_win2"])
          ? true
          : false,
      accept_all_background_color2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_button_color2"
      )
        ? settings_obj.the_options["button_accept_all_button_color2"]
        : "#18a300",
      accept_all_style2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_style2"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_style2"]
        : "none",
      accept_all_border_color2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_color2"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_color2"]
        : "#18a300",
      accept_all_opacity2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_opacity2"
      )
        ? settings_obj.the_options["button_accept_all_btn_opacity2"]
        : "1",
      accept_all_border_width2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_width2"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_width2"]
        : "0",
      accept_all_border_radius2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_radius2"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_radius2"]
        : "0",

      cookie_decline_on2:
        settings_obj.the_options.hasOwnProperty("button_decline_is_on2") &&
        (false === settings_obj.the_options["button_decline_is_on2"] ||
          0 === settings_obj.the_options["button_decline_is_on2"] ||
          "false" === settings_obj.the_options["button_decline_is_on2"])
          ? false
          : true,
      decline_text2: settings_obj.the_options.hasOwnProperty(
        "button_decline_text2"
      )
        ? settings_obj.the_options["button_decline_text2"]
        : "Decline",
      decline_text_color2: settings_obj.the_options.hasOwnProperty(
        "button_decline_link_color2"
      )
        ? settings_obj.the_options["button_decline_link_color2"]
        : "#ffffff",
      decline_as_button2:
        settings_obj.the_options.hasOwnProperty("button_decline_as_button2") &&
        (false === settings_obj.the_options["button_decline_as_button2"] ||
          0 === settings_obj.the_options["button_decline_as_button2"] ||
          "false" === settings_obj.the_options["button_decline_as_button2"])
          ? false
          : true,
      decline_background_color2: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_color2"
      )
        ? settings_obj.the_options["button_decline_button_color2"]
        : "#333333",
      decline_opacity2: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_opacity2"
      )
        ? settings_obj.the_options["button_decline_button_opacity2"]
        : "1",
      decline_style2: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_style2"
      )
        ? settings_obj.the_options["button_decline_button_border_style2"]
        : "none",
      decline_border_color2: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_color2"
      )
        ? settings_obj.the_options["button_decline_button_border_color2"]
        : "#333333",
      decline_border_width2: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_width2"
      )
        ? settings_obj.the_options["button_decline_button_border_width2"]
        : "0",
      decline_border_radius2: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_radius2"
      )
        ? settings_obj.the_options["button_decline_button_border_radius2"]
        : "0",
      decline_action2: settings_obj.the_options.hasOwnProperty(
        "button_decline_action2"
      )
        ? settings_obj.the_options["button_decline_action2"]
        : "#cookie_action_close_header_reject",

      decline_url2: settings_obj.the_options.hasOwnProperty(
        "button_decline_url2"
      )
        ? settings_obj.the_options["button_decline_url2"]
        : "#",
      open_decline_url2:
        settings_obj.the_options.hasOwnProperty("button_decline_new_win2") &&
        (true === settings_obj.the_options["button_decline_new_win2"] ||
          1 === settings_obj.the_options["button_decline_new_win2"] ||
          "true" === settings_obj.the_options["button_decline_new_win2"])
          ? true
          : false,

      cookie_settings_on2:
        settings_obj.the_options.hasOwnProperty("button_settings_is_on2") &&
        (false === settings_obj.the_options["button_settings_is_on2"] ||
          1 === settings_obj.the_options["button_settings_is_on2"] ||
          "false" === settings_obj.the_options["button_settings_is_on2"])
          ? false
          : true,

      
      settings_text2: settings_obj.the_options.hasOwnProperty(
        "button_settings_text2"
      )
        ? settings_obj.the_options["button_settings_text2"]
        : "Cookie Settings",
      settings_text_color2: settings_obj.the_options.hasOwnProperty(
        "button_settings_link_color2"
      )
        ? settings_obj.the_options["button_settings_link_color2"]
        : "#ffffff",
      settings_as_button2:
        settings_obj.the_options.hasOwnProperty("button_settings_as_button2") &&
        (false === settings_obj.the_options["button_settings_as_button2"] ||
          0 === settings_obj.the_options["button_settings_as_button2"] ||
          "false" === settings_obj.the_options["button_settings_as_button2"])
          ? false
          : true,
      settings_background_color2: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_color2"
      )
        ? settings_obj.the_options["button_settings_button_color2"]
        : "#333333",
      settings_opacity2: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_opacity2"
      )
        ? settings_obj.the_options["button_settings_button_opacity2"]
        : "1",
      settings_style2: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_style2"
      )
        ? settings_obj.the_options["button_settings_button_border_style2"]
        : "none",
      settings_border_color2: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_color2"
      )
        ? settings_obj.the_options["button_settings_button_border_color2"]
        : "#333333",
      settings_border_width2: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_width2"
      )
        ? settings_obj.the_options["button_settings_button_border_width2"]
        : "0",
      settings_border_radius2: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_radius2"
      )
        ? settings_obj.the_options["button_settings_button_border_radius2"]
        : "0",
      cookie_on_frontend2:
        settings_obj.the_options.hasOwnProperty(
          "button_settings_display_cookies2"
        ) &&
        (true ===
          settings_obj.the_options["button_settings_display_cookies2"] ||
          1 === settings_obj.the_options["button_settings_display_cookies2"] ||
          "true" ===
            settings_obj.the_options["button_settings_display_cookies2"])
          ? true
          : false,
      confirm_text2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_text2"
      )
        ? settings_obj.the_options["button_confirm_text2"]
        : "Confirm",
      confirm_text_color2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_link_color2"
      )
        ? settings_obj.the_options["button_confirm_link_color2"]
        : "#ffffff",
      confirm_background_color2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_color2"
      )
        ? settings_obj.the_options["button_confirm_button_color2"]
        : "#18a300",
      confirm_opacity2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_opacity2"
      )
        ? settings_obj.the_options["button_confirm_button_opacity2"]
        : "1",
      confirm_style2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_style2"
      )
        ? settings_obj.the_options["button_confirm_button_border_style2"]
        : "none",
      confirm_border_color2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_color2"
      )
        ? settings_obj.the_options["button_confirm_button_border_color2"]
        : "#18a300",
      confirm_border_width2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_width2"
      )
        ? settings_obj.the_options["button_confirm_button_border_width2"]
        : "0",
      confirm_border_radius2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_radius2"
      )
        ? settings_obj.the_options["button_confirm_button_border_radius2"]
        : "0",
      cancel_text2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_text2"
      )
        ? settings_obj.the_options["button_cancel_text2"]
        : "Cancel",
      cancel_text_color2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_link_color2"
      )
        ? settings_obj.the_options["button_cancel_link_color2"]
        : "#ffffff",
      cancel_background_color2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_color2"
      )
        ? settings_obj.the_options["button_cancel_button_color2"]
        : "#333333",
      cancel_opacity2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_opacity2"
      )
        ? settings_obj.the_options["button_cancel_button_opacity2"]
        : "1",
      cancel_style2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_style2"
      )
        ? settings_obj.the_options["button_cancel_button_border_style2"]
        : "none",
      cancel_border_color2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_color2"
      )
        ? settings_obj.the_options["button_cancel_button_border_color2"]
        : "#333333",
      cancel_border_width2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_width2"
      )
        ? settings_obj.the_options["button_cancel_button_border_width2"]
        : "0",
      cancel_border_radius2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_radius2"
      )
        ? settings_obj.the_options["button_cancel_button_border_radius2"]
        : "0",
      opt_out_text2: settings_obj.the_options.hasOwnProperty(
        "button_donotsell_text2"
      )
        ? settings_obj.the_options["button_donotsell_text2"]
        : "Do Not Sell My Personal Information",
      opt_out_text_color2: settings_obj.the_options.hasOwnProperty(
        "button_donotsell_link_color2"
      )
        ? settings_obj.the_options["button_donotsell_link_color2"]
        : "#359bf5",
      button_readmore_text2: settings_obj.the_options.hasOwnProperty(
        "button_readmore_text2"
      )
        ? settings_obj.the_options["button_readmore_text2"]
        : "Read More",
      button_readmore_link_color2: settings_obj.the_options.hasOwnProperty(
        "button_readmore_link_color2"
      )
        ? settings_obj.the_options["button_readmore_link_color2"]
        : "#359bf5",
      button_readmore_as_button2:
        settings_obj.the_options.hasOwnProperty("button_readmore_as_button2") &&
        (true === settings_obj.the_options["button_readmore_as_button2"] ||
          1 === settings_obj.the_options["button_readmore_as_button2"])
          ? true
          : false,
      button_readmore_url_type2:
        settings_obj.the_options.hasOwnProperty("button_readmore_url_type2") &&
        (false === settings_obj.the_options["button_readmore_url_type2"] ||
          0 === settings_obj.the_options["button_readmore_url_type2"])
          ? false
          : true,
      button_readmore_page2: settings_obj.the_options.hasOwnProperty(
        "button_readmore_page2"
      )
        ? settings_obj.the_options["button_readmore_page2"]
        : "0",
      button_readmore_wp_page2:
        settings_obj.the_options.hasOwnProperty("button_readmore_wp_page2") &&
        (true === settings_obj.the_options["button_readmore_wp_page2"] ||
          1 === settings_obj.the_options["button_readmore_wp_page2"])
          ? true
          : false,
      button_readmore_new_win2:
        settings_obj.the_options.hasOwnProperty("button_readmore_new_win2") &&
        (true === settings_obj.the_options["button_readmore_new_win2"] ||
          1 === settings_obj.the_options["button_readmore_new_win2"])
          ? true
          : false,
      button_readmore_url2: settings_obj.the_options.hasOwnProperty(
        "button_readmore_url2"
      )
        ? settings_obj.the_options["button_readmore_url2"]
        : "#",
      button_readmore_button_color2: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_color2"
      )
        ? settings_obj.the_options["button_readmore_button_color2"]
        : "#000000",
      button_readmore_button_opacity2: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_opacity2"
      )
        ? settings_obj.the_options["button_readmore_button_opacity2"]
        : "1",
      button_readmore_button_border_style2:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_style2"
        )
          ? settings_obj.the_options["button_readmore_button_border_style2"]
          : "none",
      button_readmore_button_border_width2:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_width2"
        )
          ? settings_obj.the_options["button_readmore_button_border_width2"]
          : "0",
      button_readmore_button_border_color2:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_color2"
        )
          ? settings_obj.the_options["button_readmore_button_border_color2"]
          : "#000000",
      button_readmore_button_border_radius2:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_radius2"
        )
          ? settings_obj.the_options["button_readmore_button_border_radius2"]
          : "0",
      button_readmore_button_size2: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_size2"
      )
        ? settings_obj.the_options["button_readmore_button_size2"]
        : "medium",  
      is_script_blocker_on:
        settings_obj.the_options.hasOwnProperty("is_script_blocker_on") &&
        (true === settings_obj.the_options["is_script_blocker_on"] ||
          1 === settings_obj.the_options["is_script_blocker_on"])
          ? true
          : false,
      header_scripts: settings_obj.the_options.hasOwnProperty("header_scripts")
        ? this.stripSlashes(settings_obj.the_options["header_scripts"])
        : "",
      body_scripts: settings_obj.the_options.hasOwnProperty("body_scripts")
        ? this.stripSlashes(settings_obj.the_options["body_scripts"])
        : "",
      footer_scripts: settings_obj.the_options.hasOwnProperty("footer_scripts")
        ? this.stripSlashes(settings_obj.the_options["footer_scripts"])
        : "",
      success_error_message: "",
      custom_cookie_categories:
        settings_obj.cookie_list_settings.hasOwnProperty(
          "cookie_list_categories"
        )
          ? settings_obj.cookie_list_settings["cookie_list_categories"]
          : [],
      custom_cookie_types: settings_obj.cookie_list_settings.hasOwnProperty(
        "cookie_list_types"
      )
        ? settings_obj.cookie_list_settings["cookie_list_types"]
        : [],
      custom_cookie_category: 1,
      custom_cookie_type: "HTTP",
      custom_cookie_name: "",
      custom_cookie_domain: "",
      custom_cookie_duration: "",
      custom_cookie_description: "",
      is_custom_cookie_duration_disabled:
        this.custom_cookie_type === "HTTP Cookie" ? false : true,
      custom_cookie_duration_placeholder: "Duration(days/session)",
      post_cookie_list_length: settings_obj.cookie_list_settings.hasOwnProperty(
        "post_cookie_list"
      )
        ? settings_obj.cookie_list_settings["post_cookie_list"]["total"]
        : 0,
      post_cookie_list: settings_obj.cookie_list_settings.hasOwnProperty(
        "post_cookie_list"
      )
        ? settings_obj.cookie_list_settings["post_cookie_list"]["data"]
        : [],
      show_custom_form: this.post_cookie_list_length > 0 ? false : true,
      show_add_custom_button: this.post_cookie_list_length > 0 ? true : false,
      scan_cookie_list_length: settings_obj.cookie_scan_settings.hasOwnProperty(
        "scan_cookie_list"
      )
        ? settings_obj.cookie_scan_settings["scan_cookie_list"]["total"]
        : 0,
      scan_cookie_list: settings_obj.cookie_scan_settings.hasOwnProperty(
        "scan_cookie_list"
      )
        ? settings_obj.cookie_scan_settings["scan_cookie_list"]["data"]
        : [],
      scan_cookie_error_message:
        settings_obj.cookie_scan_settings.hasOwnProperty("error_message")
          ? settings_obj.cookie_scan_settings["error_message"]
          : "",
      scan_cookie_last_scan: settings_obj.cookie_scan_settings.hasOwnProperty(
        "last_scan"
      )
        ? settings_obj.cookie_scan_settings["last_scan"]
        : [],
      continue_scan: 1,
      pollCount: 0,
      onPrg: 0,
      selected_template_json : settings_obj.the_options.hasOwnProperty("selected_template_json") 
        ? JSON.parse(settings_obj.the_options['selected_template_json'])
        : [],
      template: settings_obj.the_options.hasOwnProperty("template")
        ? settings_obj.the_options["template"]
        : "default",
      cookie_accept_all_on:
        settings_obj.the_options.hasOwnProperty("button_accept_all_is_on") &&
        (true === settings_obj.the_options["button_accept_all_is_on"] ||
          1 === settings_obj.the_options["button_accept_all_is_on"])
          ? true
          : false,
      accept_all_text: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_text"
      )
        ? settings_obj.the_options["button_accept_all_text"]
        : "Accept All",
      accept_all_text_color: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_link_color"
      )
        ? settings_obj.the_options["button_accept_all_link_color"]
        : "#ffffff",
      accept_all_as_button_options: settings_obj.accept_button_as_options,
      accept_all_as_button:
        settings_obj.the_options.hasOwnProperty(
          "button_accept_all_as_button"
        ) &&
        (true === settings_obj.the_options["button_accept_all_as_button"] ||
          1 === settings_obj.the_options["button_accept_all_as_button"])
          ? true
          : false,
      accept_all_action: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_action"
      )
        ? settings_obj.the_options["button_accept_all_action"]
        : "#cookie_action_close_header",
      accept_all_url: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_url"
      )
        ? settings_obj.the_options["button_accept_all_url"]
        : "#",
      accept_all_open_url:
        this.accept_all_action === "#cookie_action_close_header" ? false : true,
      accept_all_new_win:
        settings_obj.the_options.hasOwnProperty("button_accept_all_new_win") &&
        (true === settings_obj.the_options["button_accept_all_new_win"] ||
          1 === settings_obj.the_options["button_accept_all_new_win"])
          ? true
          : false,
      accept_all_background_color: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_button_color"
      )
        ? settings_obj.the_options["button_accept_all_button_color"]
        : "#18a300",
      accept_all_style: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_style"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_style"]
        : "none",
      accept_all_border_color: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_color"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_color"]
        : "#18a300",
      accept_all_opacity: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_opacity"
      )
        ? settings_obj.the_options["button_accept_all_btn_opacity"]
        : "1",
      accept_all_border_width: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_width"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_width"]
        : "0",
      accept_all_border_radius: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_radius"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_radius"]
        : "0",
      //custom css
      gdpr_css_text: settings_obj.the_options.hasOwnProperty("gdpr_css_text")
        ? this.decodeCSS(settings_obj.the_options["gdpr_css_text"])
        : "",
      gdpr_css_text_free: "/*Your CSS here*/",

      //Do not track
      do_not_track_on:
        "true" == settings_obj.the_options["do_not_track_on"] ||
        1 === settings_obj.the_options["do_not_track_on"]
          ? true
          : false,
      //import file selected
      selectedFile: "",
      //Consent Log
      consent_log_switch_clicked: false,
      // Data Request.
      data_reqs_on:
        "true" == settings_obj.the_options["data_reqs_on"] ||
        1 === settings_obj.the_options["data_reqs_on"] ||
        "1" == settings_obj.the_options["data_reqs_on"]
          ? true
          : false,
      shortcode_copied: false,
      data_reqs_switch_clicked: false,
      data_req_email_address: settings_obj.the_options.hasOwnProperty(
        "data_req_email_address"
      )
        ? settings_obj.the_options["data_req_email_address"]
        : "",
      data_req_subject: settings_obj.the_options.hasOwnProperty(
        "data_req_subject"
      )
        ? settings_obj.the_options["data_req_subject"]
        : "We have received your request",
      data_req_editor_message: settings_obj.the_options.hasOwnProperty(
        "data_req_editor_message"
      )
        ? this.decodeHTMLString(
            settings_obj.the_options["data_req_editor_message"]
          )
        : "",
      enable_safe:
        settings_obj.the_options.hasOwnProperty("enable_safe") &&
        ("true" === settings_obj.the_options["enable_safe"] ||
          1 === settings_obj.the_options["enable_safe"])
          ? true
          : false,
      usage_data: settings_obj.hasOwnProperty("is_usage_tracking_allowed")
      ? ("true" === settings_obj["is_usage_tracking_allowed"] )
      : "false",
      reload_onSelect_law: false,
      reload_onSafeMode: false,
      // hide banner.
      select_pages: settings_obj.the_options.hasOwnProperty("select_pages")
        ? settings_obj.the_options["select_pages"]
        : [],
      select_pages_array: [],
      list_of_pages: settings_obj.list_of_pages,
      
      //script dependency
      is_script_dependency_on:
      settings_obj.the_options.hasOwnProperty("is_script_dependency_on") &&
      (true === settings_obj.the_options["is_script_dependency_on"] ||
        1 === settings_obj.the_options["is_script_dependency_on"])
        ? true
        : false,
      header_dependency: settings_obj.the_options.hasOwnProperty("header_dependency")
        ? settings_obj.the_options["header_dependency"]
        : '',
      header_dependency_list: settings_obj.header_dependency_list,
      header_dependency_map: {
        'Body Scripts': false,
        'Footer Scripts': false,
      },
      footer_dependency: settings_obj.the_options.hasOwnProperty("footer_dependency")
        ? settings_obj.the_options["footer_dependency"]
        : '',
      footer_dependency_selected: null,
      footer_dependency_list: settings_obj.footer_dependency_list,
      footer_dependency_map: {
        'Header Scripts': false,
        'Body Scripts': false,
      },
      
      // consent forward .
      consent_forward:
        settings_obj.the_options.hasOwnProperty("consent_forward") &&
        (true === settings_obj.the_options["consent_forward"] ||
          1 === settings_obj.the_options["consent_forward"])
          ? true
          : false,
      select_sites: settings_obj.the_options.hasOwnProperty("select_sites")
        ? settings_obj.the_options["select_sites"]
        : [],
      select_sites_array: [],
      list_of_sites: settings_obj.list_of_sites,
      pluginVersion:
        typeof GDPR_COOKIE_CONSENT_VERSION !== "undefined"
          ? GDPR_COOKIE_CONSENT_VERSION
          : "",

      ab_testing_enabled:
        settings_obj.ab_options.hasOwnProperty("ab_testing_enabled") &&
        (true === settings_obj.ab_options["ab_testing_enabled"] ||
          "true" === settings_obj.ab_options["ab_testing_enabled"])
          ? true
          : false,
      ab_testing_period: settings_obj.ab_options.hasOwnProperty(
        "ab_testing_period"
      )
        ? settings_obj.ab_options["ab_testing_period"]
        : "30",
      ab_testing_auto:
        settings_obj.ab_options.hasOwnProperty("ab_testing_auto") &&
        (true === settings_obj.ab_options["ab_testing_auto"] ||
          "true" === settings_obj.ab_options["ab_testing_auto"])
          ? true
          : false,
      enable_geotargeting:
        settings_obj.geo_options.hasOwnProperty("enable_geotargeting") &&
        (true === settings_obj.geo_options["enable_geotargeting"] ||
          "true" === settings_obj.geo_options["enable_geotargeting"])
          ? true
          : false,
      database_file_path: settings_obj.geo_options.hasOwnProperty(
        "database_file_path"
      )
        ? settings_obj.geo_options["database_file_path"]
        : "",
      alert_message: "Maxmind Key Integrated",
      document_link: "https://club.wpeka.com/docs/wp-cookie-consent/",
      video_link: "https://www.youtube.com/embed/hrfSoFjEpzQ",
      support_link:
        "https://club.wpeka.com/my-account/?utm_source=gdpr&utm_medium=plugin&utm_campaign=integrations",
      // revoke consent text color.
      button_revoke_consent_text_color: settings_obj.the_options.hasOwnProperty(
        "button_revoke_consent_text_color"
      )
        ? settings_obj.the_options["button_revoke_consent_text_color"]
        : "",
      button_revoke_consent_background_color:
        settings_obj.the_options.hasOwnProperty(
          "button_revoke_consent_background_color"
        )
          ? settings_obj.the_options["button_revoke_consent_background_color"]
          : "",
      button_revoke_consent_text_color1: settings_obj.the_options.hasOwnProperty(
        "button_revoke_consent_text_color1"
      )
        ? settings_obj.the_options["button_revoke_consent_text_color1"]
        : "",
      button_revoke_consent_background_color1:
        settings_obj.the_options.hasOwnProperty(
          "button_revoke_consent_background_color1"
        )
          ? settings_obj.the_options["button_revoke_consent_background_color1"]
          : "",
      button_revoke_consent_text_color2: settings_obj.the_options.hasOwnProperty(
        "button_revoke_consent_text_color2"
      )
        ? settings_obj.the_options["button_revoke_consent_text_color2"]
        : "",
      button_revoke_consent_background_color2:
        settings_obj.the_options.hasOwnProperty(
          "button_revoke_consent_background_color2"
        )
          ? settings_obj.the_options["button_revoke_consent_background_color2"]
          : "",
      is_selectedCountry_on:
        settings_obj.the_options.hasOwnProperty("is_selectedCountry_on") &&
        (true === settings_obj.the_options["is_selectedCountry_on"] ||
          1 === settings_obj.the_options["is_selectedCountry_on"])
          ? true
          : false,
      is_selectedCountry_on_ccpa:
        settings_obj.the_options.hasOwnProperty("is_selectedCountry_on_ccpa") &&
        (true === settings_obj.the_options["is_selectedCountry_on_ccpa"] ||
          1 === settings_obj.the_options["is_selectedCountry_on_ccpa"])
          ? true
          : false,
      is_worldwide_on:
        settings_obj.the_options.hasOwnProperty("is_worldwide_on") &&
        (true === settings_obj.the_options["is_worldwide_on"] ||
          1 === settings_obj.the_options["is_worldwide_on"])
          ? true
          : false,
      is_worldwide_on_ccpa:
        settings_obj.the_options.hasOwnProperty("is_worldwide_on_ccpa") &&
        (true === settings_obj.the_options["is_worldwide_on_ccpa"] ||
          1 === settings_obj.the_options["is_worldwide_on_ccpa"])
          ? true
          : false,
      selectedRadioWorldWide:
        settings_obj.the_options.hasOwnProperty("is_worldwide_on") &&
        (true === settings_obj.the_options["is_worldwide_on"] ||
          1 === settings_obj.the_options["is_worldwide_on"])
          ? true
          : false,
      selectedRadioWorldWideCcpa:
        settings_obj.the_options.hasOwnProperty("is_worldwide_on_ccpa") &&
        (true === settings_obj.the_options["is_worldwide_on_ccpa"] ||
          1 === settings_obj.the_options["is_worldwide_on_ccpa"])
          ? true
          : false,
      list_of_countries: settings_obj.list_of_countries,
      select_countries: settings_obj.the_options.hasOwnProperty(
        "select_countries"
      )
        ? settings_obj.the_options["select_countries"]
        : [],
      select_countries_ccpa: settings_obj.the_options.hasOwnProperty(
        "select_countries_ccpa"
      )
        ? settings_obj.the_options["select_countries_ccpa"]
        : [],
      select_countries_array: [],
      select_countries_array_ccpa: [],
      show_Select_Country: false,
      selectedRadioCountry:
        settings_obj.the_options.hasOwnProperty("is_selectedCountry_on") &&
        (true === settings_obj.the_options["is_selectedCountry_on"] ||
          1 === settings_obj.the_options["is_selectedCountry_on"])
          ? true
          : false,
      selectedRadioCountryCcpa:
        settings_obj.the_options.hasOwnProperty("is_selectedCountry_on_ccpa") &&
        (true === settings_obj.the_options["is_selectedCountry_on_ccpa"] ||
          1 === settings_obj.the_options["is_selectedCountry_on_ccpa"])
          ? true
          : false,
      cookie_list_tab: true,
      cookie_scan_dropdown: false,
      discovered_cookies_list_tab: false,
      scan_history_list_tab: false,
      preview_cookie_declaration: true,
      preview_about_cookie: false,
      preview_necessary: true,
      preview_marketing: false,
      preview_analysis: false,
      preview_preference: false,
      preview_unclassified: false,

      isCategoryActive: true,
      isFeaturesActive: false,
      isVendorsActive: false,
      cookieSettingsPopupAccentColor: ''
    };
  },
  computed: {
    computedBackgroundColor() {
      const color = this.ab_testing_enabled
        ? this[`cookie_bar_color${this.active_test_banner_tab}`]
        : this.gdpr_policy === 'both'
          ? this.active_default_multiple_legislation === 'gdpr'
            ? this.multiple_legislation_cookie_bar_color1 
            : this.multiple_legislation_cookie_bar_color2
          : this.cookie_bar_color

      const opacity = this.ab_testing_enabled
        ? this[`cookie_bar_opacity${this.active_test_banner_tab}`]
        : this.gdpr_policy === 'both'
          ? this.active_default_multiple_legislation === 'gdpr'
            ? this.multiple_legislation_cookie_bar_opacity1
            : this.multiple_legislation_cookie_bar_opacity2
          : this.cookie_bar_opacity;

      const finalColor = color + Math.floor(opacity * 255).toString(16).toUpperCase();
      const acceptAllBGColor = this.ab_testing_enabled ? ( this.active_test_banner_tab === 1 ? this.accept_all_background_color1 : this.accept_all_background_color2 ) : this.accept_all_background_color;
      if(this.is_ccpa == true && this.is_gdpr == false){
        if( this.ab_testing_enabled ){
          this.cookieSettingsPopupAccentColor = this.active_test_banner_tab === 1 ? this.opt_out_text_color1 : this.opt_out_text_color2;
        } else {
          this.cookieSettingsPopupAccentColor = this.opt_out_text_color;
        }
      }
      else if( finalColor.toUpperCase().slice(0, -2) === acceptAllBGColor.toUpperCase() ) {
        if( this.ab_testing_enabled ){
          this.cookieSettingsPopupAccentColor = this.active_test_banner_tab === 1 ? this.accept_all_text_color1 : this.accept_all_text_color2;
        } else if(this.is_gdpr == true && this.is_ccpa == true){
          this.cookieSettingsPopupAccentColor = this.accept_all_text_color1;
        } else {
          this.cookieSettingsPopupAccentColor = this.accept_all_text_color;
        }
      } else {
        this.cookieSettingsPopupAccentColor =  (this.is_gdpr == true && this.is_ccpa == true) ? this.accept_all_background_color1 : acceptAllBGColor;
      }

      return finalColor;
    }
  },
  methods: {  
    refreshCookieScannerData(html) {
      this.cookie_scanner_data = html;
      const container = document.querySelector('#cookie-scanner-container');
      this.$nextTick(() => {
                new Vue({
                    el: container,
                    data: this.$data, // Reuse the existing Vue instance data
                    methods: this.$options.methods, // Reuse the existing methods
                    mounted: this.$options.mounted, // Reuse the original mounted logic
                    icons: this.$options.icons, // Optionally reuse created lifecycle hook
                });
            });
    },
    refreshABTestingData(html) {
      this.ab_testing_data = html;
      const container = document.querySelector('#ab-testing-container');
      this.$nextTick(() => {
                new Vue({
                    el: container,
                    data: this.$data, // Reuse the existing Vue instance data
                    methods: this.$options.methods, // Reuse the existing methods
                    mounted: this.$options.mounted, // Reuse the original mounted logic
                    icons: this.$options.icons, // Optionally reuse created lifecycle hook
                });
            });
    },
    refreshGCMAdvertiserModeData(html) {
      this.gcm_adver_mode_data = html;
      const container = document.querySelector('#gcm-advertiser-mode-container');
      this.$nextTick(() => {
                new Vue({
                    el: container,
                    data: this.$data, // Reuse the existing Vue instance data
                    methods: this.$options.methods, // Reuse the existing methods
                    mounted: this.$options.mounted, // Reuse the original mounted logic
                    icons: this.$options.icons, // Optionally reuse created lifecycle hook
                });
            });
    },
    isPluginVersionLessOrEqual(version) {
      return this.pluginVersion && this.pluginVersion <= version;
    },
    stripSlashes(value) {
      return value.replace(/\\(.)/gm, "$1");
    },
    copyTextToClipboard() {
      const textToCopy = "[wpl_data_request]";
      const textArea = document.createElement("textarea");
      textArea.value = textToCopy;
      document.body.appendChild(textArea);
      textArea.select();
      document.execCommand("copy");
      document.body.removeChild(textArea);
      this.shortcode_copied = true;
      setTimeout(() => {
        this.shortcode_copied = false;
      }, 1500);
    },
    decodeHTMLString(encodedString) {
      var doc = new DOMParser().parseFromString(encodedString, "text/html");
      return doc.documentElement.textContent.replace(/\\/g, "");
    },
    decodeCSS(encodedCSS) {
      const lines = encodedCSS.split("\\r\\n");
      let decodedCSS = "";
      let currentIndent = 0;

      for (const line of lines) {
        const trimmedLine = line.trim();

        if (trimmedLine === "") continue; // Skip empty lines

        if (trimmedLine.startsWith("}") && currentIndent > 0) {
          currentIndent--;
        }

        decodedCSS += "  ".repeat(currentIndent) + trimmedLine + "\n";

        if (trimmedLine.endsWith("{")) {
          currentIndent++;
        }
      }

      return decodedCSS;
    },
    setValues() {
      if (this.gdpr_policy === "both") {
        this.is_ccpa = true;
        this.is_gdpr = true;
        this.is_eprivacy = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = true;
        this.show_revoke_card = true;
      } else if (this.gdpr_policy === "ccpa") {
        this.is_ccpa = true;
        this.is_eprivacy = false;
        this.is_gdpr = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = true;
        this.show_revoke_card = false;
      } else if (this.gdpr_policy === "gdpr") {
        this.is_gdpr = true;
        this.is_ccpa = false;
        this.is_eprivacy = false;
        this.is_lgpd = false;
        this.show_revoke_card = true;
        this.show_visitor_conditions = true;
      } else if (this.gdpr_policy === "lgpd") {
        this.is_gdpr = false;
        this.is_ccpa = false;
        this.is_lgpd = true;
        this.is_eprivacy = false;
        this.show_revoke_card = true;
        this.show_visitor_conditions = false;
      } else {
        this.is_eprivacy = true;
        this.is_gdpr = false;
        this.is_ccpa = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = false;
        this.show_revoke_card = true;
      }
      for (let i = 0; i < this.list_of_contents.length; i++) {
        if (
          this.restrict_posts.includes(this.list_of_contents[i].code.toString())
        ) {
          this.restrict_array.push(this.list_of_contents[i]);
        }
      }
      for (let i = 0; i < this.privacy_policy_options.length; i++) {
        if (this.button_readmore_page == this.privacy_policy_options[i].code) {
          this.readmore_page = this.privacy_policy_options[i].label;
          break;
        }
      }
      for (let i = 0; i < this.privacy_policy_options.length; i++) {
        if (this.button_readmore_page1 == this.privacy_policy_options[i].code) {
          this.readmore_page1 = this.privacy_policy_options[i].label;
          break;
        }
      }
      for (let i = 0; i < this.privacy_policy_options.length; i++) {
        if (this.button_readmore_page2 == this.privacy_policy_options[i].code) {
          this.readmore_page2 = this.privacy_policy_options[i].label;
          break;
        }
      }
      for (let i = 0; i < this.scripts_list_total; i++) {
        for (let j = 0; j < this.category_list_options.length; j++) {
          if (
            this.category_list_options[j].code ===
            this.scripts_list_data[i]["script_category"].toString()
          ) {
            this.scripts_list_data[i]["script_category_label"] =
              this.category_list_options[j].label;
            break;
          }
        }
      }
      let navLinks = j(".nav-link").map(function () {
        return this.getAttribute("href");
      });
      if(this.$refs.active_tab === undefined) this.$refs.active_tab = {};
      for (let i = 0; i < navLinks.length; i++) {
        let re = new RegExp(navLinks[i]);
        if (window.location.href.match(re)) {
          this.$refs.active_tab.activeTabIndex = i;
          break;
        }
      }
      if (this.accept_action === "#cookie_action_close_header") {
        this.is_open_url = false;
      } else {
        this.is_open_url = true;
      }
      if (this.accept_all_action === "#cookie_action_close_header") {
        this.accept_all_open_url = false;
      } else {
        this.accept_all_open_url = true;
      }
      if (this.decline_action === "#cookie_action_close_header_reject") {
        this.decline_open_url = false;
      } else {
        this.decline_open_url = true;
      }
      if (this.show_cookie_as === "banner") {
        this.is_banner = true;
      } else {
        this.is_banner = false;
      }
      if (this.custom_cookie_type === "HTTP") {
        this.is_custom_cookie_duration_disabled = false;
        this.custom_cookie_duration = "";
      } else {
        this.is_custom_cookie_duration_disabled = true;
        this.custom_cookie_duration = "Persistent";
      }
      this.show_custom_form = this.post_cookie_list_length > 0 ? false : true;
      this.show_add_custom_button =
        this.post_cookie_list_length > 0 ? true : false;

      this.disableSwitch = false;
      // multiple entries of geo targeting countries.
      for (let i = 0; i < this.list_of_countries.length; i++) {
        if (this.select_countries.includes(this.list_of_countries[i].code)) {
          this.select_countries_array.push(this.list_of_countries[i]);
        }
      }
      for (let i = 0; i < this.list_of_countries.length; i++) {
        if (this.select_countries_ccpa.includes(this.list_of_countries[i].code)) {
          this.select_countries_array_ccpa.push(this.list_of_countries[i]);
        }
      }

      // multiple entries for hide banner.
      for (let i = 0; i < this.list_of_pages.length; i++) {
        if (this.select_pages.includes(this.list_of_pages[i].code.toString())) {
          this.select_pages_array.push(this.list_of_pages[i]);
        }
      }
      if (this.list_of_sites && this.list_of_sites.length) {
        // multiple entries for the consent forward .
        for (let i = 0; i < this.list_of_sites.length; i++) {
          if (
            this.select_sites.includes(this.list_of_sites[i].code.toString())
          ) {
            this.select_sites_array.push(this.list_of_sites[i]);
          }
        }
      }
    },
    editorInit: function () {
      require("brace/ext/language_tools"); //language extension prerequsite...
      require("brace/mode/html");
      require("brace/mode/javascript"); //language
      require("brace/mode/less");
      require("brace/mode/css");
      require("brace/theme/monokai");
      require("brace/snippets/css"); //snippet
    },
    setPostListValues() {
      for (let i = 0; i < this.post_cookie_list_length; i++) {
        if (this.post_cookie_list[i]["type"] === "HTTP") {
          this.post_cookie_list[i]["enable_duration"] = false;
        } else {
          this.post_cookie_list[i]["enable_duration"] = true;
        }
        for (let j = 0; j < this.custom_cookie_types.length; j++) {
          if (
            this.custom_cookie_types[j]["code"] ===
            this.post_cookie_list[i]["type"]
          ) {
            this.post_cookie_list[i]["type_name"] =
              this.custom_cookie_types[j].label;
          }
        }
      }
      this.show_custom_form = this.post_cookie_list_length > 0 ? false : true;
      this.show_add_custom_button =
        this.post_cookie_list_length > 0 ? true : false;
    },
    setScanListValues() {
      for (let i = 0; i < this.scan_cookie_list_length; i++) {
        for (let j = 0; j < this.custom_cookie_types.length; j++) {
          if (
            this.custom_cookie_types[j]["code"] ===
            this.scan_cookie_list[i]["type"]
          ) {
            this.scan_cookie_list[i]["type_name"] =
              this.custom_cookie_types[j].label;
          }
        }
      }
    },
    onClickPreviewCookieDeclaration() {
      this.preview_cookie_declaration = true;
      this.preview_about_cookie = false;
      this.preview_necessary = true;
      this.preview_marketing = false;
      this.preview_analysis = false;
      this.preview_preference = false;
      this.preview_unclassified = false;
    },
    onClickPreviewAboutCookie() {
      this.preview_about_cookie = true;
      this.preview_cookie_declaration = false;
      this.preview_necessary = false;
      this.preview_marketing = false;
      this.preview_analysis = false;
      this.preview_preference = false;
      this.preview_unclassified = false;
    },
    onSwitchPreviewNecessary() {
      this.preview_necessary = true;
      this.preview_marketing = false;
      this.preview_analysis = false;
      this.preview_preference = false;
      this.preview_unclassified = false;
    },
    onSwitchPreviewMarketing() {
      this.preview_necessary = false;
      this.preview_marketing = true;
      this.preview_analysis = false;
      this.preview_preference = false;
      this.preview_unclassified = false;
    },
    onSwitchPreviewAnalysis() {
      this.preview_necessary = false;
      this.preview_marketing = false;
      this.preview_analysis = true;
      this.preview_preference = false;
      this.preview_unclassified = false;
    },
    onSwitchPreviewPreference() {
      this.preview_necessary = false;
      this.preview_marketing = false;
      this.preview_analysis = false;
      this.preview_preference = true;
      this.preview_unclassified = false;
    },
    onSwitchPreviewUnclassified() {
      this.preview_necessary = false;
      this.preview_marketing = false;
      this.preview_analysis = false;
      this.preview_preference = false;
      this.preview_unclassified = true;
    },
    onSwitchCookieEnable() {
      this.cookie_is_on = !this.cookie_is_on;
    },
    onSwitchBannerPreviewEnable() {
      this.isCategoryActive = true;
      this.isFeaturesActive = false;
      this.isVendorsActive = false;
      //changing the value of banner_preview_swicth_value enable/disable
      this.banner_preview_is_on = !this.banner_preview_is_on;
    },
    onSwitchDntEnable() {
      //changing the value of do_not_track_on enable/disable
      this.do_not_track_on = !this.do_not_track_on;
    },
    onSwitchDataReqsEnable() {
      //changing the value of data_reqs_on enable/disable
      this.data_reqs_on = !this.data_reqs_on;
      this.data_reqs_switch_clicked = true;
    },
    onSwitchIabtcfEnable() {
      this.iabtcf_is_on = !this.iabtcf_is_on;
      if (this.iabtcf_is_on) {
        this.gdpr_message = `We and our <a id = "vendor-link" href = "#" data-toggle = "gdprmodal" data-target = "#gdpr-gdprmodal">836 partners</a> use cookies and other tracking technologies to improve your experience on our website. We may store and/or access information on a device and process personal data, such as your IP address and browsing data, for personalised advertising and content, advertising and content measurement, audience research and services development. Additionally, we may utilize precise geolocation data and identification through device scanning.\n\nPlease note that your consent will be valid across all our subdomains. You can change or withdraw your consent at any time by clicking the “Cookie Settings” button at the bottom of your screen. We respect your choices and are committed to providing you with a transparent and secure browsing experience.`;
        this.gdpr_about_cookie_message =
          "Customize your consent preferences for Cookie Categories and advertising tracking preferences for Purposes & Features and Vendors below. You can give granular consent for each Third Party Vendor. Most vendors require consent for personal data processing, while some rely on legitimate interest. However, you have the right to object to their use of legitimate interest. The choices you make regarding the purposes and entities listed in this notice are saved in a cookie named wpl_tc_string for a maximum duration of 12 months.";
      } else {
        this.gdpr_message =
          "This website uses cookies to improve your experience. We'll assume you're ok with this, but you can opt-out if you wish.";
        this.gdpr_about_cookie_message =
          "Cookies are small text files that can be used by websites to make a user's experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.";
      }
      this.is_iabtcf_changed = true;
    },
    onSwitchGCMEnable(){
      this.gcm_is_on = !this.gcm_is_on;
    },
    onSwitchGCMUrlPass(){
      this.gcm_url_passthrough = !this.gcm_url_passthrough;
    },
    onSwitchGCMAdsRedact(){
      this.gcm_ads_redact = !this.gcm_ads_redact;
    },
    onSwitchGCMDebugMode(){
      this.gcm_debug_mode = !this.gcm_debug_mode;
    },
    checkGCMStatus(){
      var that = this;
      var data = {
        action: "wpl_check_gcm_status",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
      };
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          that.gcm_scan_flag = true;
          that.pollingInterval = setInterval(that.fetchGCMStatus, 10000);
        },
        error: function (e) {
          console.log(e);
        },
      });
    },
    fetchGCMStatus() {
      var that = this;
      jQuery.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        type: "POST",
        data: {
          action: "wpl_get_gcm_status"
        },
        success: (response) => {
          if (response.success) {
            that.gcm_scan_flag = false;
            clearInterval(that.pollingInterval); 

            that.gcm_scan_result = response.data;
          } else {
          }
        },
        error: (e) => {
          console.error(e);
          that.gcm_scan_flag = false;
          that.success_error_message = "Some error occured";
          j("#gdpr-cookie-consent-save-settings-alert").css({
            "background-color": "#72b85c",
            "z-index": "10000",
          });
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        }
      });
    },
    onSwitchGCMAdvertiserMode(){
      this.gcm_advertiser_mode = !this.gcm_advertiser_mode;
    },
    onSwitchDynamicLang() {
      this.dynamic_lang_is_on = !this.dynamic_lang_is_on;
    },
    onSwitchGacmEnable() {
      this.gacm_is_on = !this.gacm_is_on;
    },
    onSwitchCookieAcceptEnable() {
      this.cookie_accept_on = !this.cookie_accept_on;
    },
    onSwitchCookieAcceptEnable1() {
      this.cookie_accept_on1 = !this.cookie_accept_on1;
    },
    onSwitchCookieAcceptAllEnable() {
      this.cookie_accept_all_on = !this.cookie_accept_all_on;
    },
    onSwitchCookieAcceptAllEnable1() {
      this.cookie_accept_all_on1 = !this.cookie_accept_all_on1;
    },
    onSwitchCookieAcceptEnable2() {
      this.cookie_accept_on2 = !this.cookie_accept_on2;
    },
    onSwitchCookieAcceptAllEnable2() {
      this.cookie_accept_all_on2 = !this.cookie_accept_all_on2;
    },
    onSwitchReloadLaw() {
      this.reload_onSelect_law = !this.reload_onSelect_law;
      this.reload_onSelect_law = true;
    },
    onSwitchIABEnable(value) {
      this.is_iab_on = !this.is_iab_on;
      if (value) {
        this.selectedRadioIab = value === "yes" ? "yes" : "no";
      }
    },
    onSwitchWorldWideEnable() {
      this.selectedRadioWorldWide = "yes";
      this.selectedRadioCountry = false;
      this.is_worldwide_on = true;
      this.is_eu_on = false;
      this.is_selectedCountry_on = false;
    },
    onSwitchWorldWideEnableCcpa() {
      this.selectedRadioWorldWideCcpa = "yes";
      this.selectedRadioCountryCcpa = false;
      this.is_worldwide_on_ccpa = true;
      this.is_selectedCountry_on_ccpa = false;
      this.is_ccpa_on = false;
    },
    onSwitchEUEnable(isChecked) {
      if (isChecked) {
        this.selectedRadioWorldWide = false;
        this.is_eu_on = true;
        this.is_worldwide_on = false;
      } else {
        this.is_eu_on = false;
        if (this.is_selectedCountry_on != true) {
          this.selectedRadioWorldWide = "yes";
        }
      }
    },
    onSwitchSelectedCountryEnable(isChecked) {
      if (isChecked) {
        this.is_selectedCountry_on = true;
        this.selectedRadioCountry = true;
        this.selectedRadioWorldWide = false;
        this.is_worldwide_on = false;
      } else {
        this.is_selectedCountry_on = false;
        this.selectedRadioCountry = false;
        if (this.is_eu_on != true) {
          this.selectedRadioWorldWide = "yes";
        }
      }
    },
    onSwitchSelectedCountryEnableCcpa(isChecked) {
      if (isChecked) {
        this.is_selectedCountry_on_ccpa = true;
        this.selectedRadioCountryCcpa = true;
        this.selectedRadioWorldWideCcpa = false;
        this.is_worldwide_on_ccpa = false;
      } else {
        this.is_selectedCountry_on_ccpa = false;
        this.selectedRadioCountryCcpa = false;
        if (this.is_ccpa_on != true) {
          this.selectedRadioWorldWideCcpa = "yes";
        }
      }
    },
    onSwitchCCPAEnable(isChecked) {
      if (isChecked) {
        this.selectedRadioWorldWideCcpa = false;
        this.is_ccpa_on = true;
        this.is_worldwide_on_ccpa = false;
      } else {
        this.is_ccpa_on = false;
        if (this.is_selectedCountry_on_ccpa != true) {
          this.selectedRadioWorldWideCcpa = "yes";
        }
      }
    },
    onCountrySelect(value) {
      this.select_countries = this.select_countries_array.join(",");
    },
    onCountrySelectCcpa(value) {
      this.select_countries_ccpa = this.select_countries_array_ccpa.join(",");
      
    },
    showSelectCountryForm() {
      this.show_Select_Country = !this.show_Select_Country;
    },
    closeModal() {
      this.showModal = false;
    },
    onEnablesafeSwitch() {
      if (this.enable_safe === "true") {
        this.is_worldwide_on = true;
        this.is_worldwide_on_ccpa = true;
        this.is_eu_on = false;
        this.is_ccpa_on = false;
        this.selectedRadioCountry = false;
        this.selectedRadioCountryCcpa = false;
      } else {
        this.is_worldwide_on = true;
        this.is_worldwide_on_ccpa = true;
        this.is_eu_on = false;
        this.is_ccpa_on = false;
        this.selectedRadioCountry = false;
        this.selectedRadioCountryCcpa = false;
      }
    },
    onSwitchRevokeConsentEnable() {
      this.is_revoke_consent_on = !this.is_revoke_consent_on;
    },
    onSwitchRevokeConsentEnable1() {
      this.is_revoke_consent_on1 = !this.is_revoke_consent_on1;
    },
    onSwitchRevokeConsentEnable2() {
      this.is_revoke_consent_on2 = !this.is_revoke_consent_on2;
    },
    onSwitchAutotick() {
      this.autotick = !this.autotick;
    },
    onSwitchAutoHide() {
      this.auto_hide = !this.auto_hide;
    },
    onSwitchAutoBannerInitialize() {
      this.auto_banner_initialize = !this.auto_banner_initialize;
    },
    onSwitchAutoGeneratedBanner() {
      this.processof_auto_template_generated = true;
      this.is_auto_template_generated = true;
      // var data = !this.auto_generated_banner;
      // this.auto_generated_banner = !this.auto_generated_banner;

      // // Check if the auto_generated_banner is being switched on
      // if (data) {
      // Open a new tab
      let newTab = window.open(window.location.origin, "_blank");

      // Wait for the new tab to load and fetch the required data
      newTab.onload = function () {
        // Array to store elements with a background color
        const elementsWithButton = [];

        // Get all elements in the new tab
        const allElements = newTab.document.querySelectorAll("*");

        // Loop through each element
        allElements.forEach((element) => {
          // Check if the element is a button, an anchor tag, or has a class containing "button"
          if (
            element.tagName.toLowerCase() === "button" ||
            element.tagName.toLowerCase() === "a" ||
            Array.from(element.classList).some((className) =>
              className.toLowerCase().includes("button")
            )
          ) {
            // Ignore elements where any class name starts with "gdpr_"
            const hasGdprClass = Array.from(element.classList).some(
              (className) => className.startsWith("gdpr_")
            );
            if (hasGdprClass) return; // Skip this element if it has a "gdpr_" class

            // Get computed styles for the element
            const computedStyles = getComputedStyle(element);

            // Check if a background color is applied
            const backgroundColor = computedStyles.backgroundColor;

            if (
              backgroundColor &&
              backgroundColor !== "rgba(0, 0, 0, 0)" &&
              backgroundColor !== "transparent"
            ) {
              elementsWithButton.push({
                tag: element.tagName.toLowerCase(), // The tag name (button, a, etc.)
                classes: Array.from(element.classList), // The classes applied to the element
                backgroundColor: backgroundColor, // The computed background color
              });
            }
          }
        });

        // Function to convert RGB(A) to Hex
        const rgbToHex = (rgb) => {
          const rgba = rgb.match(/\d+/g); // Extract the numeric values
          const r = parseInt(rgba[0], 10).toString(16).padStart(2, "0");
          const g = parseInt(rgba[1], 10).toString(16).padStart(2, "0");
          const b = parseInt(rgba[2], 10).toString(16).padStart(2, "0");
          return `#${r}${g}${b}`.toUpperCase(); // Return as hex code in uppercase
        };

        // Function to find the most used background color
        const getMostUsedBackgroundColor = (elements) => {
          const colorCounts = {};

          // Count occurrences of each background color
          elements.forEach((item) => {
            const color = item.backgroundColor;
            if (color) {
              colorCounts[color] = (colorCounts[color] || 0) + 1;
            }
          });

          // Find the color with the highest count
          let mostUsedColor = null;
          let maxCount = 0;
          let isTie = false;

          for (const [color, count] of Object.entries(colorCounts)) {
            if (count > maxCount) {
              mostUsedColor = color;
              maxCount = count;
              isTie = false; // Reset tie flag when a higher count is found
            } else if (count === maxCount) {
              isTie = true; // A tie exists if another color has the same count
            }
          }

          // Handle ties: If all counts are the same or a tie exists, pick any one color
          if (isTie) {
            mostUsedColor = Object.keys(colorCounts)[0]; // Pick the first color
          }

          return mostUsedColor;
        };

        // Find the most used background color
        let mostUsedColor = getMostUsedBackgroundColor(elementsWithButton);

        // Convert the most used color to hex
        const hexColor = rgbToHex(mostUsedColor);

        // Close the new tab after getting the data
        newTab.close();
        // Send the hex color via AJAX
        jQuery.ajax({
          url: settings_obj.ajaxurl,
          type: "POST",
          dataType: "json",
          data: {
            action: "gcc_auto_generated_banner",
            background_color: hexColor,
            is_auto_generated_banner_done: true, // Send the hex color
          },
          success: function (response) {},
          error: function (error) {
            console.error("Error:", error);
          },
        });
      };
      this.banner_preview_is_on = true;
      // }
    },

    onSwitchAutoScroll() {
      this.auto_scroll = !this.auto_scroll;
    },
    onSwitchAutoClick() {
      this.auto_click = !this.auto_click;
    },
    onSwitchAutoScrollReload() {
      this.auto_scroll_reload = !this.auto_scroll_reload;
    },
    onSwitchAcceptReload() {
      this.accept_reload = !this.accept_reload;
    },
    onSwitchDeclineReload() {
      this.decline_reload = !this.decline_reload;
    },
    onSwitchDeleteOnDeactivation() {
      this.delete_on_deactivation = !this.delete_on_deactivation;
    },
    //consent forward.
    onSwitchConsentForward() {
      this.consent_forward = !this.consent_forward;
    },
    onSwitchEnableSafe() {
      this.onEnablesafeSwitch();
      this.onSwitchReloadSafeMode();
      this.enable_safe = !this.enable_safe;
    },
    onSwitchEnableUsageData() {
      this.usage_data = !this.usage_data;
    },
    onSwitchReloadSafeMode() {
      this.reload_onSafeMode = !this.reload_onSafeMode;
      this.reload_onSafeMode = true;
    },
    onSwitchShowCredits() {
      this.show_credits = !this.show_credits;
    },
    onSwitchLoggingOn() {
      this.logging_on = !this.logging_on;
      this.consent_log_switch_clicked = true;
    },
    onClickRenewConsent() {
      this.consent_version = Number(this.consent_version) + 1;
      this.success_error_message = "User Consent Renewed. Save Changes Please.";
      j("#gdpr-cookie-consent-save-settings-alert").css(
        "background-color",
        "#72b85c"
      );
      j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
      j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
    },
    onClickAddMedia() {
      // Get the button element
      jQuery(document).ready(function ($) {
        var frame = wp.media({
          title: "Select or Upload Media",
          button: {
            text: "Use this media",
          },
          multiple: false, // Set to false if selecting only one file
        });

        frame.open();

        frame.on("select", function () {
          var selection = frame.state().get("selection");

          selection.map(function (attachment) {
            var attachmentURL = attachment.attributes.url;
            var attachmentType = attachment.attributes.type;
            var attachmentFileName = attachment.attributes.filename;

            var editor = $("#quill-container .ql-editor")[0];
            var quillInstance = editor.__quill || editor.parentNode.__quill;

            if (attachmentType === "application" || attachmentType === "text") {
              var link = $("<a>")
                .attr("href", attachmentURL)
                .text(attachmentFileName);
              quillInstance.root.appendChild(link[0]);
              quillInstance.root.appendChild($("<br>")[0]);
            } else {
              quillInstance.insertEmbed(
                quillInstance.getLength(),
                "image",
                attachmentURL
              );
            }
          });
        });
      });
    },
    cookieAcceptChange(value) {
      if (value === "#cookie_action_close_header") {
        this.is_open_url = false;
      } else {
        this.is_open_url = true;
      }
    },

    cookieAcceptAllChange(value) {
      if (value === "#cookie_action_close_header") {
        this.accept_all_open_url = false;
      } else {
        this.accept_all_open_url = true;
      }
    },
    cookieDeclineChange(value) {
      if (value === "#cookie_action_close_header_reject") {
        this.decline_open_url = false;
      } else {
        this.decline_open_url = true;
      }
    },
    cookieTypeChange(value) {
      this.processof_auto_template_generated = false;
      if (value === "banner") {
        if(this.template == 'blue_full') this.template = 'blue_center';
        this.is_banner = true;
      } else {
        this.is_banner = false;
      }
    },
    cookiebannerPositionChange(position) {
      this.cookie_position = position;
    },
    cookiewidgetPositionChange(value) {
      this.cookie_widget_position = value;
    },
    selectTab(tabName) {
      this.isCategoryActive  = (tabName === 'category');
      this.isFeaturesActive  = (tabName === 'features');
      this.isVendorsActive   = (tabName === 'vendors');
    },
    turnOffPreviewBanner() {
      this.banner_preview_is_on = false;
    },
    onTemplateChange(value) {
      console.log("DODODO template: ", this.json_templates);
      this.template = value;
      this.auto_generated_banner = false;
      let selectedTemplate
      if(value == "default"){
        selectedTemplate = this.default_template_json;
      }
      else{
        selectedTemplate = this.json_templates[value];
      }
      this.cookie_bar_color =                       selectedTemplate['styles']['background-color'];
      this.cookie_bar_opacity =                     selectedTemplate['styles']['opacity'];
      this.cookie_text_color =                      selectedTemplate['styles']['color'];
      this.border_style =                           selectedTemplate['styles']['border-style'];
      this.cookie_bar_border_width =                selectedTemplate['styles']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.cookie_border_color =                    selectedTemplate['styles']['border-color'];
      this.cookie_bar_border_radius =               selectedTemplate['styles']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.cookie_font =                            selectedTemplate['styles']['font-family'];
      this.cookie_accept_on =                       selectedTemplate['accept_button']['is_on'];
      this.accept_as_button =                       true;
      this.accept_text_color =                      selectedTemplate['accept_button']['color'];
      this.accept_background_color =                selectedTemplate['accept_button']['background-color'];
      this.accept_style =                           selectedTemplate['accept_button']['border-style'];
      this.accept_border_color =                    selectedTemplate['accept_button']['border-color'];
      this.accept_opacity =                         selectedTemplate['accept_button']['opacity'];
      this.accept_border_width =                    selectedTemplate['accept_button']['border-width'].substring(0, selectedTemplate['accept_button']['border-width'].length - 2);
      this.accept_border_radius =                   selectedTemplate['accept_button']['border-radius'].substring(0, selectedTemplate['accept_button']['border-radius'].length - 2);
      this.cookie_decline_on =                      selectedTemplate['decline_button']['is_on'];
      this.decline_as_button =                      true;
      this.decline_text_color =                     selectedTemplate['decline_button']['color'];
      this.decline_background_color =               selectedTemplate['decline_button']['background-color'];
      this.decline_style =                          selectedTemplate['decline_button']['border-style'];
      this.decline_border_color =                   selectedTemplate['decline_button']['border-color'];
      this.decline_opacity =                        selectedTemplate['decline_button']['opacity'];
      this.decline_border_width =                   selectedTemplate['decline_button']['border-width'].substring(0, selectedTemplate['decline_button']['border-width'].length - 2);
      this.decline_border_radius =                  selectedTemplate['decline_button']['border-radius'].substring(0, selectedTemplate['decline_button']['border-radius'].length - 2);
      this.cookie_accept_all_on =                   selectedTemplate['accept_all_button']['is_on'];
      this.accept_all_as_button =                   true;
      this.accept_all_text_color =                  selectedTemplate['accept_all_button']['color'];
      this.accept_all_background_color =            selectedTemplate['accept_all_button']['background-color'];
      this.accept_all_style =                       selectedTemplate['accept_all_button']['border-style'];
      this.accept_all_border_color =                selectedTemplate['accept_all_button']['border-color'];
      this.accept_all_opacity =                     selectedTemplate['accept_all_button']['opacity'];
      this.accept_all_border_width =                selectedTemplate['accept_all_button']['border-width'].substring(0, selectedTemplate['accept_all_button']['border-width'].length - 2);
      this.accept_all_border_radius =               selectedTemplate['accept_all_button']['border-radius'].substring(0, selectedTemplate['accept_all_button']['border-radius'].length - 2);
      this.cookie_settings_on =                     selectedTemplate['settings_button']['is_on'];
      this.settings_as_button =                     true;
      this.settings_text_color =                    selectedTemplate['settings_button']['color'];
      this.settings_background_color =              selectedTemplate['settings_button']['background-color'];
      this.settings_style =                         selectedTemplate['settings_button']['border-style'];
      this.settings_border_color =                  selectedTemplate['settings_button']['border-color'];
      this.settings_opacity =                       selectedTemplate['settings_button']['opacity'];
      this.settings_border_width =                  selectedTemplate['settings_button']['border-width'].substring(0, selectedTemplate['settings_button']['border-width'].length - 2);
      this.settings_border_radius =                 selectedTemplate['settings_button']['border-radius'].substring(0, selectedTemplate['settings_button']['border-radius'].length - 2);
      this.button_readmore_link_color =             selectedTemplate['readmore_button']['color'];
      this.button_readmore_button_color =           selectedTemplate['readmore_button']['background-color'];
      this.button_readmore_button_opacity =         selectedTemplate['readmore_button']['opacity'];
      this.button_readmore_button_border_style =    selectedTemplate['readmore_button']['border-style'];
      this.button_readmore_button_border_color =    selectedTemplate['readmore_button']['border-color'];
      this.button_readmore_button_border_radius =   selectedTemplate['readmore_button']['border-radius'].substring(0, selectedTemplate['readmore_button']['border-radius'].length - 2);
      this.button_readmore_button_border_width =    selectedTemplate['readmore_button']['border-width'].substring(0, selectedTemplate['readmore_button']['border-width'].length - 2);
      this.opt_out_text_color =                     selectedTemplate['opt_out_button']['color'];
      this.button_revoke_consent_text_color =       selectedTemplate['revoke_consent_button']['color'];
      this.button_revoke_consent_background_color = selectedTemplate['revoke_consent_button']['background-color'];
      //ab testing banners settings
      
      this.cookie_bar_color1 =                       selectedTemplate['styles']['background-color'];
      this.cookie_bar_opacity1 =                     selectedTemplate['styles']['opacity'];
      this.cookie_text_color1 =                      selectedTemplate['styles']['color'];
      this.border_style1 =                           selectedTemplate['styles']['border-style'];
      this.cookie_bar_border_width1 =                selectedTemplate['styles']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.cookie_border_color1 =                    selectedTemplate['styles']['border-color'];
      this.cookie_bar_border_radius1 =               selectedTemplate['styles']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.cookie_font1 =                            selectedTemplate['styles']['font-family'];
      this.cookie_accept_on1 =                       selectedTemplate['accept_button']['is_on'];
      this.accept_as_button1 =                       true;
      this.accept_text_color1 =                      selectedTemplate['accept_button']['color'];
      this.accept_background_color1 =                selectedTemplate['accept_button']['background-color'];
      this.accept_style1 =                           selectedTemplate['accept_button']['border-style'];
      this.accept_border_color1 =                    selectedTemplate['accept_button']['border-color'];
      this.accept_opacity1 =                         selectedTemplate['accept_button']['opacity'];
      this.accept_border_width1 =                    selectedTemplate['accept_button']['border-width'].substring(0, selectedTemplate['accept_button']['border-width'].length - 2);
      this.accept_border_radius1 =                   selectedTemplate['accept_button']['border-radius'].substring(0, selectedTemplate['accept_button']['border-radius'].length - 2);
      this.cookie_decline_on1 =                      selectedTemplate['decline_button']['is_on'];
      this.decline_as_button1 =                      true;
      this.decline_text_color1 =                     selectedTemplate['decline_button']['color'];
      this.decline_background_color1 =               selectedTemplate['decline_button']['background-color'];
      this.decline_style1 =                          selectedTemplate['decline_button']['border-style'];
      this.decline_border_color1 =                   selectedTemplate['decline_button']['border-color'];
      this.decline_opacity1 =                        selectedTemplate['decline_button']['opacity'];
      this.decline_border_width1 =                   selectedTemplate['decline_button']['border-width'].substring(0, selectedTemplate['decline_button']['border-width'].length - 2);
      this.decline_border_radius1 =                  selectedTemplate['decline_button']['border-radius'].substring(0, selectedTemplate['decline_button']['border-radius'].length - 2);
      this.cookie_accept_all_on1 =                   selectedTemplate['accept_all_button']['is_on'];
      this.accept_all_as_button1 =                   true;
      this.accept_all_text_color1 =                  selectedTemplate['accept_all_button']['color'];
      this.accept_all_background_color1 =            selectedTemplate['accept_all_button']['background-color'];
      this.accept_all_style1 =                       selectedTemplate['accept_all_button']['border-style'];
      this.accept_all_border_color1 =                selectedTemplate['accept_all_button']['border-color'];
      this.accept_all_opacity1 =                     selectedTemplate['accept_all_button']['opacity'];
      this.accept_all_border_width1 =                selectedTemplate['accept_all_button']['border-width'].substring(0, selectedTemplate['accept_all_button']['border-width'].length - 2);
      this.accept_all_border_radius1 =               selectedTemplate['accept_all_button']['border-radius'].substring(0, selectedTemplate['accept_all_button']['border-radius'].length - 2);
      this.cookie_settings_on1 =                     selectedTemplate['settings_button']['is_on'];
      this.settings_as_button1 =                     true;
      this.settings_text_color1 =                    selectedTemplate['settings_button']['color'];
      this.settings_background_color1 =              selectedTemplate['settings_button']['background-color'];
      this.settings_style1 =                         selectedTemplate['settings_button']['border-style'];
      this.settings_border_color1 =                  selectedTemplate['settings_button']['border-color'];
      this.settings_opacity1 =                       selectedTemplate['settings_button']['opacity'];
      this.settings_border_width1 =                  selectedTemplate['settings_button']['border-width'].substring(0, selectedTemplate['settings_button']['border-width'].length - 2);
      this.settings_border_radius1 =                 selectedTemplate['settings_button']['border-radius'].substring(0, selectedTemplate['settings_button']['border-radius'].length - 2);
      this.opt_out_text_color1 =                     selectedTemplate['opt_out_button']['color'];
      this.button_readmore_link_color1 =             selectedTemplate['readmore_button']['color'];
      this.button_readmore_button_color1 =           selectedTemplate['readmore_button']['background-color'];
      this.button_readmore_button_opacity1 =         selectedTemplate['readmore_button']['opacity'];
      this.button_readmore_button_border_style1 =    selectedTemplate['readmore_button']['border-style'];
      this.button_readmore_button_border_color1 =    selectedTemplate['readmore_button']['border-color'];
      this.button_readmore_button_border_radius1 =   selectedTemplate['readmore_button']['border-radius'].substring(0, selectedTemplate['readmore_button']['border-radius'].length - 2);
      this.button_readmore_button_border_width1 =    selectedTemplate['readmore_button']['border-width'].substring(0, selectedTemplate['readmore_button']['border-width'].length - 2);
      this.button_revoke_consent_text_color1 =       selectedTemplate['revoke_consent_button']['color'];
      this.button_revoke_consent_background_color1 = selectedTemplate['revoke_consent_button']['background-color'];

      this.cookie_bar_color2 =                       selectedTemplate['styles']['background-color'];
      this.cookie_bar_opacity2 =                     selectedTemplate['styles']['opacity'];
      this.cookie_text_color2 =                      selectedTemplate['styles']['color'];
      this.border_style2 =                           selectedTemplate['styles']['border-style'];
      this.cookie_bar_border_width2 =                selectedTemplate['styles']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.cookie_border_color2 =                    selectedTemplate['styles']['border-color'];
      this.cookie_bar_border_radius2 =               selectedTemplate['styles']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.cookie_font2 =                            selectedTemplate['styles']['font-family'];
      this.cookie_accept_on2 =                       selectedTemplate['accept_button']['is_on'];
      this.accept_as_button2 =                       true;
      this.accept_text_color2 =                      selectedTemplate['accept_button']['color'];
      this.accept_background_color2 =                selectedTemplate['accept_button']['background-color'];
      this.accept_style2 =                           selectedTemplate['accept_button']['border-style'];
      this.accept_border_color2 =                    selectedTemplate['accept_button']['border-color'];
      this.accept_opacity2 =                         selectedTemplate['accept_button']['opacity'];
      this.accept_border_width2 =                    selectedTemplate['accept_button']['border-width'].substring(0, selectedTemplate['accept_button']['border-width'].length - 2);
      this.accept_border_radius2 =                   selectedTemplate['accept_button']['border-radius'].substring(0, selectedTemplate['accept_button']['border-radius'].length - 2);
      this.cookie_decline_on2 =                      selectedTemplate['decline_button']['is_on'];
      this.decline_as_button2 =                      true;
      this.decline_text_color2 =                     selectedTemplate['decline_button']['color'];
      this.decline_background_color2 =               selectedTemplate['decline_button']['background-color'];
      this.decline_style2 =                          selectedTemplate['decline_button']['border-style'];
      this.decline_border_color2 =                   selectedTemplate['decline_button']['border-color'];
      this.decline_opacity2 =                        selectedTemplate['decline_button']['opacity'];
      this.decline_border_width2 =                   selectedTemplate['decline_button']['border-width'].substring(0, selectedTemplate['decline_button']['border-width'].length - 2);
      this.decline_border_radius2 =                  selectedTemplate['decline_button']['border-radius'].substring(0, selectedTemplate['decline_button']['border-radius'].length - 2);
      this.cookie_accept_all_on2 =                   selectedTemplate['accept_all_button']['is_on'];
      this.accept_all_as_button2 =                   true;
      this.accept_all_text_color2 =                  selectedTemplate['accept_all_button']['color'];
      this.accept_all_background_color2 =            selectedTemplate['accept_all_button']['background-color'];
      this.accept_all_style2 =                       selectedTemplate['accept_all_button']['border-style'];
      this.accept_all_border_color2 =                selectedTemplate['accept_all_button']['border-color'];
      this.accept_all_opacity2 =                     selectedTemplate['accept_all_button']['opacity'];
      this.accept_all_border_width2 =                selectedTemplate['accept_all_button']['border-width'].substring(0, selectedTemplate['accept_all_button']['border-width'].length - 2);
      this.accept_all_border_radius2 =               selectedTemplate['accept_all_button']['border-radius'].substring(0, selectedTemplate['accept_all_button']['border-radius'].length - 2);
      this.cookie_settings_on2 =                     selectedTemplate['settings_button']['is_on'];
      this.settings_as_button2 =                     true;
      this.settings_text_color2 =                    selectedTemplate['settings_button']['color'];
      this.settings_background_color2 =              selectedTemplate['settings_button']['background-color'];
      this.settings_style2 =                         selectedTemplate['settings_button']['border-style'];
      this.settings_border_color2 =                  selectedTemplate['settings_button']['border-color'];
      this.settings_opacity2 =                       selectedTemplate['settings_button']['opacity'];
      this.settings_border_width2 =                  selectedTemplate['settings_button']['border-width'].substring(0, selectedTemplate['settings_button']['border-width'].length - 2);
      this.settings_border_radius2 =                 selectedTemplate['settings_button']['border-radius'].substring(0, selectedTemplate['settings_button']['border-radius'].length - 2);
      this.opt_out_text_color2 =                     selectedTemplate['opt_out_button']['color'];
      this.button_readmore_link_color2 =             selectedTemplate['readmore_button']['color'];
      this.button_readmore_button_color2 =           selectedTemplate['readmore_button']['background-color'];
      this.button_readmore_button_opacity2 =         selectedTemplate['readmore_button']['opacity'];
      this.button_readmore_button_border_style2 =    selectedTemplate['readmore_button']['border-style'];
      this.button_readmore_button_border_color2 =    selectedTemplate['readmore_button']['border-color'];
      this.button_readmore_button_border_radius2 =   selectedTemplate['readmore_button']['border-radius'].substring(0, selectedTemplate['readmore_button']['border-radius'].length - 2);
      this.button_readmore_button_border_width2 =    selectedTemplate['readmore_button']['border-width'].substring(0, selectedTemplate['readmore_button']['border-width'].length - 2);
      this.button_revoke_consent_text_color2 =       selectedTemplate['revoke_consent_button']['color'];
      this.button_revoke_consent_background_color2 = selectedTemplate['revoke_consent_button']['background-color'];

      // Multiple Legislation
      this.multiple_legislation_cookie_bar_color1 =         selectedTemplate["styles"]["background-color"];
      this.multiple_legislation_cookie_bar_border_radius1 = selectedTemplate['styles']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.multiple_legislation_cookie_text_color1 =        selectedTemplate['styles']['color'];
      this.multiple_legislation_cookie_bar_opacity1 =       selectedTemplate['styles']['opacity'];
      this.multiple_legislation_border_style1 =             selectedTemplate['styles']['border-style'];
      this.multiple_legislation_cookie_bar_border_width1 =  selectedTemplate['styles']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.multiple_legislation_cookie_border_color1 =      selectedTemplate['styles']['border-color'];
      this.multiple_legislation_cookie_font1 =              selectedTemplate['styles']['font-family'];

      this.multiple_legislation_cookie_bar_color2 =         selectedTemplate["styles"]["background-color"];
      this.multiple_legislation_cookie_bar_border_radius2 = selectedTemplate['styles']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.multiple_legislation_cookie_text_color2 =        selectedTemplate['styles']['color'];
      this.multiple_legislation_cookie_bar_opacity2 =       selectedTemplate['styles']['opacity'];
      this.multiple_legislation_border_style2 =             selectedTemplate['styles']['border-style'];
      this.multiple_legislation_cookie_bar_border_width2 =  selectedTemplate['styles']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.multiple_legislation_cookie_border_color2 =      selectedTemplate['styles']['border-color'];
      this.multiple_legislation_cookie_font2 =              selectedTemplate['styles']['font-family'];

      //CCPA popup buttons
      this.confirm_button_popup =                     selectedTemplate['accept_all_button']['is_on'];
      this.confirm_text_color =                       selectedTemplate['accept_all_button']['color'];
      this.confirm_background_color =                 selectedTemplate['accept_all_button']['background-color'];
      this.confirm_opacity =                          selectedTemplate['accept_all_button']['opacity'];
      this.confirm_style =                            selectedTemplate['accept_all_button']['border-style'];
      this.confirm_border_color =                     selectedTemplate['accept_all_button']['border-color'];
      this.confirm_border_radius =                    selectedTemplate['accept_all_button']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.confirm_border_width =                     selectedTemplate['accept_all_button']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.cancel_button_popup =                      selectedTemplate['decline_button']['is_on'];
      this.cancel_text_color =                        selectedTemplate['decline_button']['color'];
      this.cancel_background_color =                  selectedTemplate['decline_button']['background-color'];
      this.cancel_opacity =                           selectedTemplate['decline_button']['opacity'];
      this.cancel_style =                             selectedTemplate['decline_button']['border-style'];
      this.cancel_border_color =                      selectedTemplate['decline_button']['border-color'];
      this.cancel_border_radius =                     selectedTemplate['decline_button']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.cancel_border_width =                      selectedTemplate['decline_button']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);

      this.confirm_button_popup1 =                     selectedTemplate['accept_all_button']['is_on'];
      this.confirm_text_color1 =                       selectedTemplate['accept_all_button']['color'];
      this.confirm_background_color1 =                 selectedTemplate['accept_all_button']['background-color'];
      this.confirm_opacity1 =                          selectedTemplate['accept_all_button']['opacity'];
      this.confirm_style1 =                            selectedTemplate['accept_all_button']['border-style'];
      this.confirm_border_color1 =                     selectedTemplate['accept_all_button']['border-color'];
      this.confirm_border_radius1 =                    selectedTemplate['accept_all_button']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.confirm_border_width1 =                     selectedTemplate['accept_all_button']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.cancel_button_popup1 =                      selectedTemplate['decline_button']['is_on'];
      this.cancel_text_color1 =                        selectedTemplate['decline_button']['color'];
      this.cancel_background_color1 =                  selectedTemplate['decline_button']['background-color'];
      this.cancel_opacity1 =                           selectedTemplate['decline_button']['opacity'];
      this.cancel_style1 =                             selectedTemplate['decline_button']['border-style'];
      this.cancel_border_color1 =                      selectedTemplate['decline_button']['border-color'];
      this.cancel_border_radius1 =                     selectedTemplate['decline_button']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.cancel_border_width1 =                      selectedTemplate['decline_button']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);

      this.confirm_button_popup2 =                     selectedTemplate['accept_all_button']['is_on'];
      this.confirm_text_color2 =                       selectedTemplate['accept_all_button']['color'];
      this.confirm_background_color2 =                 selectedTemplate['accept_all_button']['background-color'];
      this.confirm_opacity2 =                          selectedTemplate['accept_all_button']['opacity'];
      this.confirm_style2 =                            selectedTemplate['accept_all_button']['border-style'];
      this.confirm_border_color2 =                     selectedTemplate['accept_all_button']['border-color'];
      this.confirm_border_radius2 =                    selectedTemplate['accept_all_button']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.confirm_border_width2 =                     selectedTemplate['accept_all_button']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.cancel_button_popup2 =                      selectedTemplate['decline_button']['is_on'];
      this.cancel_text_color2 =                        selectedTemplate['decline_button']['color'];
      this.cancel_background_color2 =                  selectedTemplate['decline_button']['background-color'];
      this.cancel_opacity2 =                           selectedTemplate['decline_button']['opacity'];
      this.cancel_style2 =                             selectedTemplate['decline_button']['border-style'];
      this.cancel_border_color2 =                      selectedTemplate['decline_button']['border-color'];
      this.cancel_border_radius2 =                     selectedTemplate['decline_button']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.cancel_border_width2 =                      selectedTemplate['decline_button']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);

      this.is_template_changed = true;
    },
    onLanguageChange() {
      this.is_lang_changed = true;
    },
    onSwitchButtonReadMoreIsOn() {
      this.button_readmore_is_on = !this.button_readmore_is_on;
    },
    onSwitchButtonReadMoreIsOn1() {
      this.button_readmore_is_on1 = !this.button_readmore_is_on1;
    },
    onSwitchButtonReadMoreIsOn2() {
      this.button_readmore_is_on2 = !this.button_readmore_is_on2;
    },
    onSwitchButtonReadMoreWpPage() {
      this.button_readmore_wp_page = !this.button_readmore_wp_page;
    },
    onSwitchButtonReadMoreWpPage1() {
      this.button_readmore_wp_page1 = !this.button_readmore_wp_page1;
    },
    onSwitchButtonReadMoreWpPage2() {
      this.button_readmore_wp_page2 = !this.button_readmore_wp_page2;
    },
    onSwitchButtonReadMoreNewWin() {
      this.button_readmore_new_win = !this.button_readmore_new_win;
    },
    onSwitchButtonReadMoreNewWin1() {
      this.button_readmore_new_win1 = !this.button_readmore_new_win1;
    },
    onSwitchButtonReadMoreNewWin2() {
      this.button_readmore_new_win2 = !this.button_readmore_new_win2;
    },
    onSwitchingScriptBlocker() {
      this.is_script_blocker_on = !this.is_script_blocker_on;
    },
    onPostsSelect(value) {
      let temp_array = [];
      for (let i = 0; i < value.length; i++) {
        temp_array[i] = value[i];
      }
      this.restrict_posts = temp_array;
    },
    onPageSelect(value) {
      let dummy_array = [];
      for (let i = 0; i < value.length; i++) {
        dummy_array[i] = value[i];
      }
      this.select_pages = dummy_array;
    },
    onSwitchingScriptDependency() {
      this.is_script_dependency_on = !this.is_script_dependency_on;

      if( this.is_script_dependency_on === false ){
        this.header_dependency = null;
        this.footer_dependency = null;
      }
    },
    onHeaderDependencySelect(value) {
      
      this.header_dependency_map.body = false;
      this.header_dependency_map.footer = false;

      if (this.header_dependency) {
        this.header_dependency_map[this.header_dependency] = true;
        this.header_dependency = this.header_dependency;
      } else {
        this.header_dependency = '';
      }
    },
    onFooterDependencySelect(value) {
      
      this.footer_dependency_map.header = false;
      this.footer_dependency_map.body = false;

      if (this.footer_dependency) {
        this.footer_dependency_map[this.footer_dependency] = true;
        this.footer_dependency = this.footer_dependency;
      } else {
        this.footer_dependency = '';
      }
    },
    onSiteSelect(value) {
      let tmp_array = [];
      for (let i = 0; i < value.length; i++) {
        tmp_array[i] = value[i];
      }
      this.select_sites = tmp_array;
    },
    onSelectPrivacyPage(value) {
      this.button_readmore_page = value;
    },
    onSelectPrivacyPage1(value) {
      this.button_readmore_page1 = value;
    },
    onSelectPrivacyPage2(value) {
      this.button_readmore_page2 = value;
    },
    onButtonChange(value, modelKey){
      if (modelKey == 'accept') {
        if (value == false && this.accept_text_color == this.cookie_bar_color) {
          [this.accept_text_color, this.accept_background_color] = [this.accept_background_color, this.accept_text_color];
        } else if (value == true && this.accept_background_color == this.cookie_bar_color) {
          [this.accept_text_color, this.accept_background_color] = [this.accept_background_color, this.accept_text_color];
        }
      } else if (modelKey == 'accept_all') {
        if (value == false && this.accept_all_text_color == this.cookie_bar_color) {
          [this.accept_all_text_color, this.accept_all_background_color] = [this.accept_all_background_color, this.accept_all_text_color];
        } else if (value == true && this.accept_all_background_color == this.cookie_bar_color) {
          [this.accept_all_text_color, this.accept_all_background_color] = [this.accept_all_background_color, this.accept_all_text_color];
        }
      } else if (modelKey == 'decline') {
        if (value == false && this.decline_text_color == this.cookie_bar_color) {
          [this.decline_text_color, this.decline_background_color] = [this.decline_background_color, this.decline_text_color];
        } else if (value == true && this.decline_background_color == this.cookie_bar_color) {
          [this.decline_text_color, this.decline_background_color] = [this.decline_background_color, this.decline_text_color];
        }
      } else if (modelKey == 'settings') {
        if (value == false && this.settings_text_color == this.cookie_bar_color) {
          [this.settings_text_color, this.settings_background_color] = [this.settings_background_color, this.settings_text_color];
        } else if (value == true && this.settings_background_color == this.cookie_bar_color) {
          [this.settings_text_color, this.settings_background_color] = [this.settings_background_color, this.settings_text_color];
        }
      } else if (modelKey == 'accept1') {
        if (value == false && this.accept_text_color1 == this.cookie_bar_color1) {
          [this.accept_text_color1, this.accept_background_color1] = [this.accept_background_color1, this.accept_text_color1];
        } else if (value == true && this.accept_background_color1 == this.cookie_bar_color1) {
          [this.accept_text_color1, this.accept_background_color1] = [this.accept_background_color1, this.accept_text_color1];
        }
      } else if (modelKey == 'accept_all1') {
        if (value == false && this.accept_all_text_color1 == this.cookie_bar_color1) {
          [this.accept_all_text_color1, this.accept_all_background_color1] = [this.accept_all_background_color1, this.accept_all_text_color1];
        } else if (value == true && this.accept_all_background_color1 == this.cookie_bar_color1) {
          [this.accept_all_text_color1, this.accept_all_background_color1] = [this.accept_all_background_color1, this.accept_all_text_color1];
        }
      } else if (modelKey == 'decline1') {
        if (value == false && this.decline_text_color1 == this.cookie_bar_color1) {
          [this.decline_text_color1, this.decline_background_color1] = [this.decline_background_color1, this.decline_text_color1];
        } else if (value == true && this.decline_background_color1 == this.cookie_bar_color1) {
          [this.decline_text_color1, this.decline_background_color1] = [this.decline_background_color1, this.decline_text_color1];
        }
      } else if (modelKey == 'settings1') {
        if (value == false && this.settings_text_color1 == this.cookie_bar_color1) {
          [this.settings_text_color1, this.settings_background_color1] = [this.settings_background_color1, this.settings_text_color1];
        } else if (value == true && this.settings_background_color1 == this.cookie_bar_color1) {
          [this.settings_text_color1, this.settings_background_color1] = [this.settings_background_color1, this.settings_text_color1];
        }
      } else if (modelKey == 'accept2') {
        if (value == false && this.accept_text_color2 == this.cookie_bar_color2) {
          [this.accept_text_color2, this.accept_background_color2] = [this.accept_background_color2, this.accept_text_color2];
        } else if (value == true && this.accept_background_color2 == this.cookie_bar_color2) {
          [this.accept_text_color2, this.accept_background_color2] = [this.accept_background_color2, this.accept_text_color2];
        }
      } else if (modelKey == 'accept_all2') {
        if (value == false && this.accept_all_text_color2 == this.cookie_bar_color2) {
          [this.accept_all_text_color2, this.accept_all_background_color2] = [this.accept_all_background_color2, this.accept_all_text_color2];
        } else if (value == true && this.accept_all_background_color2 == this.cookie_bar_color2) {
          [this.accept_all_text_color2, this.accept_all_background_color2] = [this.accept_all_background_color2, this.accept_all_text_color2];
        }
      } else if (modelKey == 'decline2') {
        if (value == false && this.decline_text_color2 == this.cookie_bar_color2) {
          [this.decline_text_color2, this.decline_background_color2] = [this.decline_background_color2, this.decline_text_color2];
        } else if (value == true && this.decline_background_color2 == this.cookie_bar_color2) {
          [this.decline_text_color2, this.decline_background_color2] = [this.decline_background_color2, this.decline_text_color2];
        }
      } else if (modelKey == 'settings2') {
        if (value == false && this.settings_text_color2 == this.cookie_bar_color2) {
          [this.settings_text_color2, this.settings_background_color2] = [this.settings_background_color2, this.settings_text_color2];
        } else if (value == true && this.settings_background_color2 == this.cookie_bar_color2) {
          [this.settings_text_color2, this.settings_background_color2] = [this.settings_background_color2, this.settings_text_color2];
        }
      }


    },
    cookiePolicyChange(value) {
      this.onSwitchReloadLaw();
      if (this.gdpr_policy) {
        value = this.gdpr_policy;
      }
      if (value === "both") {
        this.is_ccpa = true;
        this.is_gdpr = true;
        this.is_eprivacy = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = true;
        this.show_revoke_card = true;
        //visitors condition.
        this.selectedRadioWorldWide = "yes";
        this.selectedRadioWorldWideCcpa = "yes";
        this.is_worldwide_on = true;
        this.is_worldwide_on_ccpa = true;
        this.is_eu_on = false;
        this.is_ccpa_on = false;
        this.selectedRadioCountry = false;
        this.selectedRadioCountryCcpa = false;
        this.is_selectedCountry_on = false;
        this.is_selectedCountry_on_ccpa = false;
      } else if (value === "ccpa") {
        this.is_ccpa = true;
        this.is_eprivacy = false;
        this.is_gdpr = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = true;
        this.show_revoke_card = false;
        this.iabtcf_is_on = false;
        this.gcm_is_on = false;
        //visitors condition.
        this.selectedRadioWorldWide = "yes";
        this.selectedRadioWorldWideCcpa = "yes";
        this.is_worldwide_on = true;
        this.is_worldwide_on_ccpa = true;
        this.is_eu_on = false;
        this.is_ccpa_on = false;
        this.selectedRadioCountry = false;
        this.selectedRadioCountryCcpa = false;
        this.is_selectedCountry_on = false;
        this.is_selectedCountry_on_ccpa = false;
        this.gacm_is_on = false;
      } else if (value === "gdpr") {
        this.is_gdpr = true;
        this.is_ccpa = false;
        this.is_eprivacy = false;
        this.is_lgpd = false;
        this.show_revoke_card = true;
        this.show_visitor_conditions = true;
        this.selectedRadioWorldWide = "yes";
        this.selectedRadioWorldWideCcpa = "yes";
        this.is_worldwide_on = true;
        this.is_worldwide_on_ccpa = true;
        this.is_eu_on = false;
        this.is_ccpa_on = false;
        this.selectedRadioCountry = false;
        this.selectedRadioCountryCcpa = false;
        this.is_selectedCountry_on = false;
        this.is_selectedCountry_on_ccpa = false;
      } else if (value === "lgpd") {
        this.is_ccpa = false;
        this.is_eprivacy = false;
        this.is_gdpr = false;
        this.is_lgpd = true;
        this.show_revoke_card = true;
        this.show_visitor_conditions = true;
        this.iabtcf_is_on = false;
        this.gacm_is_on = false;
      } else {
        this.is_eprivacy = true;
        this.is_gdpr = false;
        this.is_ccpa = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = false;
        this.show_revoke_card = true;
        this.iabtcf_is_on = false;
        this.gacm_is_on = false;
      }
      if (this.iabtcf_is_on) {
        this.gdpr_message = `We and our <a id = "vendor-link" href = "#" data-toggle = "gdprmodal" data-target = "#gdpr-gdprmodal">836 partners</a> use cookies and other tracking technologies to improve your experience on our website. We may store and/or access information on a device and process personal data, such as your IP address and browsing data, for personalised advertising and content, advertising and content measurement, audience research and services development. Additionally, we may utilize precise geolocation data and identification through device scanning.\n\nPlease note that your consent will be valid across all our subdomains. You can change or withdraw your consent at any time by clicking the “Cookie Settings” button at the bottom of your screen. We respect your choices and are committed to providing you with a transparent and secure browsing experience.`;
        this.gdpr_about_cookie_message =
          "Customize your consent preferences for Cookie Categories and advertising tracking preferences for Purposes & Features and Vendors below. You can give granular consent for each Third Party Vendor. Most vendors require consent for personal data processing, while some rely on legitimate interest. However, you have the right to object to their use of legitimate interest. The choices you make regarding the purposes and entities listed in this notice are saved in a cookie named wpl_tc_string for a maximum duration of 12 months.";
      } else {
        this.gdpr_message =
          "This website uses cookies to improve your experience. We'll assume you're ok with this, but you can opt-out if you wish.";
        this.gdpr_about_cookie_message =
          "Cookies are small text files that can be used by websites to make a user's experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.";
      }

      this.success_error_message = "Law Updated. Save changes please before progressing further.";
      j("#gdpr-cookie-consent-save-settings-alert").css(
        "background-color",
        "#72b85c"
      );
      j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
      j("#gdpr-cookie-consent-save-settings-alert").fadeOut(5000);
    },

    onSwitchDefaultCookieBar() {
      this.default_cookie_bar = !this.default_cookie_bar;
    },
    onSwitchDefaultMultipleLegislation() {
      if (this.active_default_multiple_legislation === "gdpr") {
        this.active_default_multiple_legislation = "ccpa";
      } else {
        this.active_default_multiple_legislation = "gdpr";
      }
    },
    onSwitchAddOverlay() {
      this.cookie_add_overlay = !this.cookie_add_overlay;
    },
    onSwitchCookieDeclineEnable() {
      this.cookie_decline_on = !this.cookie_decline_on;
    },
    onSwitchCookieDeclineEnable1() {
      this.cookie_decline_on1 = !this.cookie_decline_on1;
    },
    onSwitchCookieSettingsEnable() {
      this.cookie_settings_on = !this.cookie_settings_on;
    },
    onSwitchCookieSettingsEnable1() {
      this.cookie_settings_on1 = !this.cookie_settings_on1;
    },
    onSwitchCookieOnFrontend() {
      this.cookie_on_frontend = !this.cookie_on_frontend;
    },
    onSwitchCookieOnFrontend1() {
      this.cookie_on_frontend1 = !this.cookie_on_frontend1;
    },
    onSwitchCookieDeclineEnable2() {
      this.cookie_decline_on2 = !this.cookie_decline_on2;
    },
    onSwitchCookieSettingsEnable2() {
      this.cookie_settings_on2 = !this.cookie_settings_on2;
    },
    onSwitchCookieOnFrontend2() {
      this.cookie_on_frontend2 = !this.cookie_on_frontend2;
    },
    showScriptBlockerForm() {
      this.show_script_blocker = !this.show_script_blocker;
    },
    openCustomCookieTab() {
      this.cookie_list_tab = true;
      this.scan_history_list_tab = false;
      this.showCreateCookiePopup();
    },
    openDiscoveredScanTab() {
      this.discovered_cookies_list_tab = true;
      this.scan_history_list_tab = false;
      this.onClickStartScan();
    },
    showCreateCookiePopup() {
      this.show_custom_cookie_popup = !this.show_custom_cookie_popup;
    },
    editCookie(cookie) {
      this.edit_discovered_cookie_on = true;
      this.edit_discovered_cookie = { ...cookie };
    },
    hideCreateCookiePopup() {
      this.edit_discovered_cookie_on = false;
    },
    onSelectCustomCookieType(value) {
      if (value !== "HTTP") {
        this.is_custom_cookie_duration_disabled = true;
        this.custom_cookie_duration = "Persistent";
      } else {
        this.is_custom_cookie_duration_disabled = false;
        this.custom_cookie_duration = "";
      }
    },
    showCustomCookieAddForm() {
      this.show_custom_form = true;
      this.show_add_custom_button = !this.show_add_custom_button;
    },
    onUpdateScannedCookieCategory(value) {
      const id = value.split(",")[1];
      const cat = value.split(",")[0];
      for (let i = 0; i < this.scan_cookie_list_length; i++) {
        if (this.scan_cookie_list[i]["id_wpl_cookie_scan_cookies"] == id) {
          for (let j = 0; j < this.custom_cookie_categories.length; j++) {
            if (
              parseInt(this.custom_cookie_categories[j]["code"]) ==
              parseInt(cat)
            ) {
              this.scan_cookie_list[i]["category_id"] =
                this.custom_cookie_categories[j].code;
              this.scan_cookie_list[i]["category"] =
                this.custom_cookie_categories[j].label;
              break;
            }
          }
          break;
        }
      }
    },
    updateScannedCookies() {
      var cookie_scan_arr = [];
      const cookieIndex = this.scan_cookie_list.findIndex(
        (cookie) =>
          cookie.id_wpl_cookie_scan_cookies ===
          this.edit_discovered_cookie.id_wpl_cookie_scan_cookies
      );

      if (cookieIndex !== -1) {
        this.scan_cookie_list[cookieIndex].description =
          this.edit_discovered_cookie.description;
      }
      for (let i = 0; i < this.scan_cookie_list_length; i++) {
        var cid = this.scan_cookie_list[i]["id_wpl_cookie_scan_cookies"];
        var ccategory = this.scan_cookie_list[i]["category_id"];
        var cdesc = this.scan_cookie_list[i]["description"];
        var cookie_arr = {
          cid: cid,
          ccategory: ccategory,
          cdesc: cdesc,
        };
        cookie_scan_arr.push(cookie_arr);
      }
      this.updateScanCookie(cookie_scan_arr);
    },
    scheduleScanShow() {
      this.schedule_scan_show = true;
    },
    scheduleScanHide() {
      this.schedule_scan_show = false;
    },
    scanTypeChange(value) {
      this.schedule_scan_as = value;
    },
    scanTimeChange(value) {
      this.schedule_scan_time_value = value;
    },
    scanDateChange(value) {
      this.schedule_scan_date = value;
    },
    scanDayChange(value) {
      this.schedule_scan_day = value;
    },
    updateScanCookie(cookie_arr) {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "update_scan_cookie",
        cookie_arr: cookie_arr,
      };
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response === true) {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            that.hideCreateCookiePopup();
            that.showScanCookieList();
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    showScanCookieList() {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "get_scanned_cookies_list",
      };
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response) {
            that.scan_cookie_list_length = data.total;
            that.scan_cookie_list = data.data;
            that.setScanListValues();
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    onSelectUpdateCookieCategory(value) {
      const id = value.split(",")[1];
      const cat = value.split(",")[0];
      for (let i = 0; i < this.post_cookie_list_length; i++) {
        if (this.post_cookie_list[i]["id_gdpr_cookie_post_cookies"] === id) {
          for (let j = 0; i < this.custom_cookie_categories.length; j++) {
            if (this.custom_cookie_categories[j]["code"] === parseInt(cat)) {
              this.post_cookie_list[i]["category_id"] =
                this.custom_cookie_categories[j].code;
              this.post_cookie_list[i]["category"] =
                this.custom_cookie_categories[j].label;
              break;
            }
          }
          break;
        }
      }
    },
    onSelectUpdateCookieType(value) {
      const id = value.split(",")[1];
      const type_id = value.split(",")[0];
      for (let i = 0; i < this.post_cookie_list_length; i++) {
        if (this.post_cookie_list[i]["id_gdpr_cookie_post_cookies"] === id) {
          if (type_id !== "HTTP") {
            this.post_cookie_list[i]["enable_duration"] = true;
            this.post_cookie_list[i]["duration"] = "Persistent";
          } else {
            this.post_cookie_list[i]["enable_duration"] = false;
            this.post_cookie_list[i]["duration"] = "";
          }
          for (let j = 0; i < this.custom_cookie_types.length; j++) {
            if (this.custom_cookie_types[j]["code"] === type_id) {
              this.post_cookie_list[i]["type"] =
                this.custom_cookie_types[j].code;
              this.post_cookie_list[i]["type_name"] =
                this.custom_cookie_types[j].label;
              break;
            }
          }
          break;
        }
      }
    },
    onDeleteCustomCookie(cookie_id) {
      this.deletePostCookie(cookie_id);
    },
    onClickRestoreButton() {
      let answer = confirm(
        "Are you sure you want to reset to default settings?"
      );
      if (answer) {
        this.restoreDefaultSettings();
      }
    },
    updateFileName(event) {
      this.selectedFile = event.target.files[0];
      document.getElementById("importButton").disabled = false;
      document.getElementById("importButton").classList.remove("disabled");
      document
        .getElementById("importButton")
        .classList.remove("disable-import-button");
      document.getElementById("importButton").add("#importButton");
      document
        .getElementById("importButton")
        .classList.remove("disable-import-button");
      document.getElementById("importButton").remove("#importButton");
    },
    removeFile() {
      this.selectedFile = null;
      document.getElementById("fileInput").value = "";
      document.getElementById("importButton").disabled = true;
      document
        .getElementById("importButton")
        .classList.add("disable-import-button");
    },
    exportsettings() {
      const siteAddress = window.location.origin;

      // Make an AJAX request to fetch data from the custom endpoint
      fetch(siteAddress + "/wp-json/custom/v1/gdpr-data/")
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then((data) => {
          // Process the fetched data

          // Create a copy of the settings object
          const settingsCopy = { ...data };

          // Check if gdpr_text_css is not empty
          if (settingsCopy.gdpr_text_css !== "") {
            const text_css = settingsCopy.gdpr_css_text;

            // Decode the gdpr_text_css property before exporting
            const final_css = text_css.replace(/\\r\\n/g, "\n");
            settingsCopy.gdpr_css_text = final_css;
          }

          // Convert the settings object to JSON with indentation
          const settingsJSON = JSON.stringify(
            JSON.stringify(settingsCopy, null, 2)
          );

          // Create a Blob containing the JSON data
          const blob = new Blob([settingsJSON], { type: "application/json" });

          // Create a download link for the Blob
          const url = URL.createObjectURL(blob);
          const a = document.createElement("a");
          a.href = url;
          a.download = "wpeka-banner-settings.json";

          // Trigger a click on the link to initiate the download
          a.click();

          // Release the object URL to free up resources
          URL.revokeObjectURL(url);
        })
        .catch((error) => {
          console.error("There was a problem with the fetch operation:", error);
        });
    },
    importsettings() {
      var that = this;
      var fileInput = document.getElementById("fileInput");
      var file = fileInput.files[0];

      if (file) {
        var reader = new FileReader();
        document.getElementById("importButton").disabled = true;
        document
          .getElementById("importButton")
          .classList.add("disable-import-button");
        reader.onload = function (event) {
          var jsonData = event.target.result;
          try {
            const parsedData = JSON.parse(JSON.parse(jsonData));
            var data = {
              action: "gcc_update_imported_settings",
              security: settings_obj.import_settings_nonce,
              settings: parsedData,
            };
            jQuery.ajax({
              url: settings_obj.ajaxurl,
              data: data,
              dataType: "json",
              type: "POST",
              success: function (data) {
                if (data.success === true) {
                  setTimeout(function addsettings() {
                    window.location.reload();
                  }, 7000);

                  that.success_error_message =
                    "Your file has been imported successfully. Please click on the Save Changes button to make the changes.";
                  j("#gdpr-cookie-consent-save-settings-alert").css(
                    "background-color",
                    "#72b85c"
                  );
                  j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                  j("#gdpr-cookie-consent-save-settings-alert").fadeOut(7000);
                } else {
                  that.success_error_message = "Please try again.";
                  j("#gdpr-cookie-consent-save-settings-alert").css(
                    "background-color",
                    "#72b85c"
                  );
                  j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                  j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                }
              },
              error: function () {
                that.success_error_message = "Please try again.";
                j("#gdpr-cookie-consent-save-settings-alert").css(
                  "background-color",
                  "#72b85c"
                );
                j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
              },
            });
          } catch (e) {
            console.error("Error parsing JSON data:", e);
          }
        };

        reader.readAsText(file);
      } else {
        console.error("No file selected");
      }
    },
    restoreDefaultSettings() {
      this.ab_testing_enabled = false;
      this.ab_testing_auto = false;
      this.ab_testing_period = "30";
      this.gacm_key = "";
      this.template = "default";
      this.accept_text = "Accept";
      this.accept_url = "#";
      this.accept_action = "#cookie_action_close_header";
      this.open_url = false;
      this.iabtcf_is_on = false;
      this.gcm_is_on = false;
      this.gcm_wait_for_update_duration = '500';
      this.gcm_url_passthrough = false;
      this.gcm_ads_redact = false;
      this.gcm_debug_mode = false;
      this.gcm_advertiser_mode = false;
      this.dynamic_lang_is_on = false;
      this.gacm_is_on = false;
      this.accept_all_button_popup = false;
      this.accept_all_text = "Accept All";
      this.accept_all_url = "#";
      this.accept_all_action = "#cookie_action_close_header";
      this.accept_all_new_win = false;
      this.accept_all_as_button = true;
      this.button_readmore_text = "Read More";
      this.button_readmore_url = "#";
      this.button_readmore_new_win = false;
      this.button_readmore_as_button = false;
      this.button_readmore_is_on = true;
      this.button_readmore_url_type = true;
      this.button_readmore_wp_page = false;
      this.button_readmore_page = "0";
      
      this.button_readmore_text1 = "Read More";
      this.button_readmore_url1 = "#";
      this.button_readmore_new_win1 = false;
      this.button_readmore_as_button1 = false;
      this.button_readmore_button_size1 = "medium";
      this.button_readmore_is_on1 = true;
      this.button_readmore_url_type1 = true;
      this.button_readmore_wp_page1 = false;
      this.button_readmore_page1 = "0";

      this.button_readmore_text2 = "Read More";
      this.button_readmore_url2 = "#";
      this.button_readmore_new_win2 = false;
      this.button_readmore_as_button2 = false;
      this.button_readmore_button_size2 = "medium";
      this.button_readmore_is_on2 = true;
      this.button_readmore_url_type2 = true;
      this.button_readmore_wp_page2 = false;
      this.button_readmore_page2 = "0";

      this.decline_text = "Decline";
      this.decline_url = "#";
      this.decline_action = "#cookie_action_settings";
      this.open_decline_url = false;
      this.decline_as_button = true;
      this.settings_text = "Cookie Settings";
      this.settings_as_button = true;
      this.cookie_on_frontend = true;
      this.opt_out_text = "Do Not Sell My Personal Information";
      this.confirm_text = "Confirm";
      this.cancel_text = "Cancel";
      this.accept_text1 = "Accept";
      this.accept_url1 = "#";
      this.accept_action1 = "#cookie_action_close_header";
      this.open_url1 = false;
      this.accept_as_button1 = true;
      this.accept_all_button_popup1 = false;
      this.accept_all_text1 = "Accept All";
      this.accept_all_url1 = "#";
      this.accept_all_action1 = "#cookie_action_close_header";
      this.accept_all_new_win1 = false;
      this.accept_all_as_button1 = true;
      this.decline_text1 = "Decline";
      this.decline_url1 = "#";
      this.decline_action1 = "#cookie_action_settings";
      this.open_decline_url1 = false;
      this.decline_as_button1 = true;
      this.settings_text1 = "Cookie Settings";
      this.settings_as_button1 = true;
      this.cookie_on_frontend1 = true;
      this.opt_out_text1 = "Do Not Sell My Personal Information";
      this.confirm_text1 = "Confirm";
      this.cancel_text1 = "Cancel";
      this.accept_text2 = "Accept";
      this.accept_url2 = "#";
      this.accept_action2 = "#cookie_action_close_header";
      this.open_url2 = false;
      this.accept_as_button2 = true;
      this.accept_all_button_popup2 = false;
      this.accept_all_text2 = "Accept All";
      this.accept_all_url2 = "#";
      this.accept_all_action2 = "#cookie_action_close_header";
      this.accept_all_new_win2 = false;
      this.accept_all_as_button2 = true;
      this.decline_text2 = "Decline";
      this.decline_url2 = "#";
      this.decline_action2 = "#cookie_action_settings";
      this.open_decline_url2 = false;
      this.decline_as_button2 = true;
      this.settings_text2 = "Cookie Settings";
      this.settings_as_button2 = true;
      this.cookie_on_frontend2 = true;
      this.opt_out_text2 = "Do Not Sell My Personal Information";
      this.confirm_text2 = "Confirm";
      this.cancel_text2 = "Cancel";
      this.cookie_is_on = true;
      this.is_eu_on = false;
      this.is_ccpa_on = false;
      this.is_iab_on = false;
      this.selectedRadioIab = "no";
      this.logging_on = true;
      this.show_credits = true;
      this.autotick = false;
      this.is_revoke_consent_on = true;
      this.is_revoke_consent_on1 = true;
      this.is_revoke_consent_on2 = true;
      this.is_script_blocker_on = false;
      this.auto_hide = false;
      this.auto_banner_initialize = false;
      this.auto_scroll = false;
      this.auto_click = false;
      this.auto_scroll_reload = false;
      this.accept_reload = false;
      this.decline_reload = false;
      this.delete_on_deactivation = false;
      this.tab_position = "right";
      this.tab_position1 = "right";
      this.tab_position2 = "right";
      this.tab_text = "Cookie Settings";
      this.tab_text1 = "Cookie Settings";
      this.tab_text2 = "Cookie Settings";
      this.tab_margin = "5";
      this.tab_margin1 = "5";
      this.tab_margin2 = "5";
      this.auto_hide_delay = "10000";
      this.auto_banner_initialize_delay = "10000";
      this.auto_scroll_offset = "10";
      this.cookie_expiry = "365";
      this.on_hide = true;
      this.on_load = false;
      this.gdpr_message =
        "This website uses cookies to improve your experience. We'll assume you're ok with this, but you can opt-out if you wish.";
      this.lgpd_message =
        "This website uses cookies for technical and other purposes as specified in the cookie policy. We'll assume you're ok with this, but you can opt-out if you wish.";
      this.eprivacy_message =
        "This website uses cookies to improve your experience. We'll assume you're ok with this, but you can opt-out if you wish.";
      this.ccpa_message =
        "In case of sale of your personal information, you may opt out by using the link";
      this.ccpa_optout_message = "Do you really wish to opt-out?";
      this.cookie_position = "bottom";
      this.cookie_widget_position = "left";
      this.cookie_text_color = "#000000";
      this.gdpr_message_heading = "";
      this.lgpd_message_heading = "";
      this.show_cookie_as = "banner";
      this.gdpr_policy = "gdpr";
      this.cookie_add_overlay = true;
      this.gdpr_about_cookie_message =
        "Cookies are small text files that can be used by websites to make a user's experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.";
      this.lgpd_about_cookie_message =
        "Cookies are small text files that can be used by websites to make a user's experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.";
      this.header_scripts = "";
      this.body_scripts = "";
      this.footer_scripts = "";
      this.restrict_posts = [];
      // array for hide banner.
      this.select_pages = [];
      this.banner_preview_is_on = false;
      this.show_language_as = "en";
      this.gdpr_css_text = "";
      this.gdpr_css_text_free = "/*Your CSS here*/";
      this.do_not_track_on = false;
      this.data_reqs_on = true;
      this.data_req_email_address = "";
      this.data_req_subject = "We have received your request";
      // consent forward.
      this.consent_forward = false;
      this.select_sites = [];
      this.selectedRadioCountry = false;
      this.selectedRadioCountryCcpa = false;
      this.is_selectedCountry_on = false;
      this.is_selectedCountry_on_ccpa = false;
      this.selectedRadioWorldWide = true;
      this.selectedRadioWorldWideCcpa = true;
      this.is_worldwide_on = true;
      this.is_worldwide_on_ccpa = true;
      this.list_of_countries = [];
      this.select_countries = [];
      this.select_countries_ccpa = [];
      this.select_countries_array = [];
      this.select_countries_array_ccpa = [];
      this.show_Select_Country = false;
      this.cookie_font1 = "inherit";
      this.cookie_text_color1 = "#000000";
      this.multiple_legislation_cookie_text_color1 = "#000000";
      this.cookie_font2 = "inherit";
      this.cookie_text_color2 = "#000000";
      this.cookie_list_tab = true;
      this.cookie_scan_dropdown = false;
      this.discovered_cookies_list_tab = false;
      this.scan_history_list_tab = false;
      // Script dependency
      this.is_script_dependency_on = false;
      this.header_dependency = '';
      this.footer_dependency = '';




      //styles
      const selectedTemplate = this.default_template_json;
      console.log("DODODO in restoreDefaultSettings gen template: ", this.default_template_json);
      this.cookie_bar_color =                       selectedTemplate['styles']['background-color'];
      this.cookie_bar_opacity =                     selectedTemplate['styles']['opacity'];
      this.cookie_text_color =                      selectedTemplate['styles']['color'];
      this.border_style =                           selectedTemplate['styles']['border-style'];
      this.cookie_bar_border_width =                selectedTemplate['styles']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.cookie_border_color =                    selectedTemplate['styles']['border-color'];
      this.cookie_bar_border_radius =               selectedTemplate['styles']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.cookie_font =                            selectedTemplate['styles']['font-family'];
      this.cookie_accept_on =                       selectedTemplate['accept_button']['is_on'];
      this.accept_as_button =                       true;
      this.accept_text_color =                      selectedTemplate['accept_button']['color'];
      this.accept_background_color =                selectedTemplate['accept_button']['background-color'];
      this.accept_style =                           selectedTemplate['accept_button']['border-style'];
      this.accept_border_color =                    selectedTemplate['accept_button']['border-color'];
      this.accept_opacity =                         selectedTemplate['accept_button']['opacity'];
      this.accept_border_width =                    selectedTemplate['accept_button']['border-width'].substring(0, selectedTemplate['accept_button']['border-width'].length - 2);
      this.accept_border_radius =                   selectedTemplate['accept_button']['border-radius'].substring(0, selectedTemplate['accept_button']['border-radius'].length - 2);
      this.cookie_decline_on =                      selectedTemplate['decline_button']['is_on'];
      this.decline_as_button =                      true;
      this.decline_text_color =                     selectedTemplate['decline_button']['color'];
      this.decline_background_color =               selectedTemplate['decline_button']['background-color'];
      this.decline_style =                          selectedTemplate['decline_button']['border-style'];
      this.decline_border_color =                   selectedTemplate['decline_button']['border-color'];
      this.decline_opacity =                        selectedTemplate['decline_button']['opacity'];
      this.decline_border_width =                   selectedTemplate['decline_button']['border-width'].substring(0, selectedTemplate['decline_button']['border-width'].length - 2);
      this.decline_border_radius =                  selectedTemplate['decline_button']['border-radius'].substring(0, selectedTemplate['decline_button']['border-radius'].length - 2);
      this.cookie_accept_all_on =                   selectedTemplate['accept_all_button']['is_on'];
      this.accept_all_as_button =                   true;
      this.accept_all_text_color =                  selectedTemplate['accept_all_button']['color'];
      this.accept_all_background_color =            selectedTemplate['accept_all_button']['background-color'];
      this.accept_all_style =                       selectedTemplate['accept_all_button']['border-style'];
      this.accept_all_border_color =                selectedTemplate['accept_all_button']['border-color'];
      this.accept_all_opacity =                     selectedTemplate['accept_all_button']['opacity'];
      this.accept_all_border_width =                selectedTemplate['accept_all_button']['border-width'].substring(0, selectedTemplate['accept_all_button']['border-width'].length - 2);
      this.accept_all_border_radius =               selectedTemplate['accept_all_button']['border-radius'].substring(0, selectedTemplate['accept_all_button']['border-radius'].length - 2);
      this.cookie_settings_on =                     selectedTemplate['settings_button']['is_on'];
      this.settings_as_button =                     true;
      this.settings_text_color =                    selectedTemplate['settings_button']['color'];
      this.settings_background_color =              selectedTemplate['settings_button']['background-color'];
      this.settings_style =                         selectedTemplate['settings_button']['border-style'];
      this.settings_border_color =                  selectedTemplate['settings_button']['border-color'];
      this.settings_opacity =                       selectedTemplate['settings_button']['opacity'];
      this.settings_border_width =                  selectedTemplate['settings_button']['border-width'].substring(0, selectedTemplate['settings_button']['border-width'].length - 2);
      this.settings_border_radius =                 selectedTemplate['settings_button']['border-radius'].substring(0, selectedTemplate['settings_button']['border-radius'].length - 2);
      this.button_readmore_link_color =             selectedTemplate['readmore_button']['color'];
      this.button_readmore_button_color =           selectedTemplate['readmore_button']['background-color'];
      this.button_readmore_button_opacity =         selectedTemplate['readmore_button']['opacity'];
      this.button_readmore_button_border_style =    selectedTemplate['readmore_button']['border-style'];
      this.button_readmore_button_border_color =    selectedTemplate['readmore_button']['border-color'];
      this.button_readmore_button_border_radius =   selectedTemplate['readmore_button']['border-radius'].substring(0, selectedTemplate['readmore_button']['border-radius'].length - 2);
      this.button_readmore_button_border_width =    selectedTemplate['readmore_button']['border-width'].substring(0, selectedTemplate['readmore_button']['border-width'].length - 2);
      this.opt_out_text_color =                     selectedTemplate['opt_out_button']['color'];
      this.button_revoke_consent_text_color =       selectedTemplate['revoke_consent_button']['color'];
      this.button_revoke_consent_background_color = selectedTemplate['revoke_consent_button']['background-color'];
      //ab testing banners settings
      
      this.cookie_bar_color1 =                       selectedTemplate['styles']['background-color'];
      this.cookie_bar_opacity1 =                     selectedTemplate['styles']['opacity'];
      this.cookie_text_color1 =                      selectedTemplate['styles']['color'];
      this.border_style1 =                           selectedTemplate['styles']['border-style'];
      this.cookie_bar_border_width1 =                selectedTemplate['styles']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.cookie_border_color1 =                    selectedTemplate['styles']['border-color'];
      this.cookie_bar_border_radius1 =               selectedTemplate['styles']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.cookie_font1 =                            selectedTemplate['styles']['font-family'];
      this.cookie_accept_on1 =                       selectedTemplate['accept_button']['is_on'];
      this.accept_as_button1 =                       true;
      this.accept_text_color1 =                      selectedTemplate['accept_button']['color'];
      this.accept_background_color1 =                selectedTemplate['accept_button']['background-color'];
      this.accept_style1 =                           selectedTemplate['accept_button']['border-style'];
      this.accept_border_color1 =                    selectedTemplate['accept_button']['border-color'];
      this.accept_opacity1 =                         selectedTemplate['accept_button']['opacity'];
      this.accept_border_width1 =                    selectedTemplate['accept_button']['border-width'].substring(0, selectedTemplate['accept_button']['border-width'].length - 2);
      this.accept_border_radius1 =                   selectedTemplate['accept_button']['border-radius'].substring(0, selectedTemplate['accept_button']['border-radius'].length - 2);
      this.cookie_decline_on1 =                      selectedTemplate['decline_button']['is_on'];
      this.decline_as_button1 =                      true;
      this.decline_text_color1 =                     selectedTemplate['decline_button']['color'];
      this.decline_background_color1 =               selectedTemplate['decline_button']['background-color'];
      this.decline_style1 =                          selectedTemplate['decline_button']['border-style'];
      this.decline_border_color1 =                   selectedTemplate['decline_button']['border-color'];
      this.decline_opacity1 =                        selectedTemplate['decline_button']['opacity'];
      this.decline_border_width1 =                   selectedTemplate['decline_button']['border-width'].substring(0, selectedTemplate['decline_button']['border-width'].length - 2);
      this.decline_border_radius1 =                  selectedTemplate['decline_button']['border-radius'].substring(0, selectedTemplate['decline_button']['border-radius'].length - 2);
      this.cookie_accept_all_on1 =                   selectedTemplate['accept_all_button']['is_on'];
      this.accept_all_as_button1 =                   true;
      this.accept_all_text_color1 =                  selectedTemplate['accept_all_button']['color'];
      this.accept_all_background_color1 =            selectedTemplate['accept_all_button']['background-color'];
      this.accept_all_style1 =                       selectedTemplate['accept_all_button']['border-style'];
      this.accept_all_border_color1 =                selectedTemplate['accept_all_button']['border-color'];
      this.accept_all_opacity1 =                     selectedTemplate['accept_all_button']['opacity'];
      this.accept_all_border_width1 =                selectedTemplate['accept_all_button']['border-width'].substring(0, selectedTemplate['accept_all_button']['border-width'].length - 2);
      this.accept_all_border_radius1 =               selectedTemplate['accept_all_button']['border-radius'].substring(0, selectedTemplate['accept_all_button']['border-radius'].length - 2);
      this.cookie_settings_on1 =                     selectedTemplate['settings_button']['is_on'];
      this.settings_as_button1 =                     true;
      this.settings_text_color1 =                    selectedTemplate['settings_button']['color'];
      this.settings_background_color1 =              selectedTemplate['settings_button']['background-color'];
      this.settings_style1 =                         selectedTemplate['settings_button']['border-style'];
      this.settings_border_color1 =                  selectedTemplate['settings_button']['border-color'];
      this.settings_opacity1 =                       selectedTemplate['settings_button']['opacity'];
      this.settings_border_width1 =                  selectedTemplate['settings_button']['border-width'].substring(0, selectedTemplate['settings_button']['border-width'].length - 2);
      this.settings_border_radius1 =                 selectedTemplate['settings_button']['border-radius'].substring(0, selectedTemplate['settings_button']['border-radius'].length - 2);
      this.opt_out_text_color1 =                     selectedTemplate['opt_out_button']['color'];

      this.cookie_bar_color2 =                       selectedTemplate['styles']['background-color'];
      this.cookie_bar_opacity2 =                     selectedTemplate['styles']['opacity'];
      this.cookie_text_color2 =                      selectedTemplate['styles']['color'];
      this.border_style2 =                           selectedTemplate['styles']['border-style'];
      this.cookie_bar_border_width2 =                selectedTemplate['styles']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.cookie_border_color2 =                    selectedTemplate['styles']['border-color'];
      this.cookie_bar_border_radius2 =               selectedTemplate['styles']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.cookie_font2 =                            selectedTemplate['styles']['font-family'];
      this.cookie_accept_on2 =                       selectedTemplate['accept_button']['is_on'];
      this.accept_as_button2 =                       true;
      this.accept_text_color2 =                      selectedTemplate['accept_button']['color'];
      this.accept_background_color2 =                selectedTemplate['accept_button']['background-color'];
      this.accept_style2 =                           selectedTemplate['accept_button']['border-style'];
      this.accept_border_color2 =                    selectedTemplate['accept_button']['border-color'];
      this.accept_opacity2 =                         selectedTemplate['accept_button']['opacity'];
      this.accept_border_width2 =                    selectedTemplate['accept_button']['border-width'].substring(0, selectedTemplate['accept_button']['border-width'].length - 2);
      this.accept_border_radius2 =                   selectedTemplate['accept_button']['border-radius'].substring(0, selectedTemplate['accept_button']['border-radius'].length - 2);
      this.cookie_decline_on2 =                      selectedTemplate['decline_button']['is_on'];
      this.decline_as_button2 =                      true;
      this.decline_text_color2 =                     selectedTemplate['decline_button']['color'];
      this.decline_background_color2 =               selectedTemplate['decline_button']['background-color'];
      this.decline_style2 =                          selectedTemplate['decline_button']['border-style'];
      this.decline_border_color2 =                   selectedTemplate['decline_button']['border-color'];
      this.decline_opacity2 =                        selectedTemplate['decline_button']['opacity'];
      this.decline_border_width2 =                   selectedTemplate['decline_button']['border-width'].substring(0, selectedTemplate['decline_button']['border-width'].length - 2);
      this.decline_border_radius2 =                  selectedTemplate['decline_button']['border-radius'].substring(0, selectedTemplate['decline_button']['border-radius'].length - 2);
      this.cookie_accept_all_on2 =                   selectedTemplate['accept_all_button']['is_on'];
      this.accept_all_as_button2 =                   true;
      this.accept_all_text_color2 =                  selectedTemplate['accept_all_button']['color'];
      this.accept_all_background_color2 =            selectedTemplate['accept_all_button']['background-color'];
      this.accept_all_style2 =                       selectedTemplate['accept_all_button']['border-style'];
      this.accept_all_border_color2 =                selectedTemplate['accept_all_button']['border-color'];
      this.accept_all_opacity2 =                     selectedTemplate['accept_all_button']['opacity'];
      this.accept_all_border_width2 =                selectedTemplate['accept_all_button']['border-width'].substring(0, selectedTemplate['accept_all_button']['border-width'].length - 2);
      this.accept_all_border_radius2 =               selectedTemplate['accept_all_button']['border-radius'].substring(0, selectedTemplate['accept_all_button']['border-radius'].length - 2);
      this.cookie_settings_on2 =                     selectedTemplate['settings_button']['is_on'];
      this.settings_as_button2 =                     true;
      this.settings_text_color2 =                    selectedTemplate['settings_button']['color'];
      this.settings_background_color2 =              selectedTemplate['settings_button']['background-color'];
      this.settings_style2 =                         selectedTemplate['settings_button']['border-style'];
      this.settings_border_color2 =                  selectedTemplate['settings_button']['border-color'];
      this.settings_opacity2 =                       selectedTemplate['settings_button']['opacity'];
      this.settings_border_width2 =                  selectedTemplate['settings_button']['border-width'].substring(0, selectedTemplate['settings_button']['border-width'].length - 2);
      this.settings_border_radius2 =                 selectedTemplate['settings_button']['border-radius'].substring(0, selectedTemplate['settings_button']['border-radius'].length - 2);
      this.opt_out_text_color2 =                     selectedTemplate['opt_out_button']['color'];

      // Multiple Legislation
      this.multiple_legislation_cookie_bar_color1 =         selectedTemplate["styles"]["background-color"];
      this.multiple_legislation_cookie_bar_border_radius1 = selectedTemplate['styles']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.multiple_legislation_cookie_text_color1 =        selectedTemplate['styles']['color'];
      this.multiple_legislation_cookie_bar_opacity1 =       selectedTemplate['styles']['opacity'];
      this.multiple_legislation_border_style1 =             selectedTemplate['styles']['border-style'];
      this.multiple_legislation_cookie_bar_border_width1 =  selectedTemplate['styles']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.multiple_legislation_cookie_border_color1 =      selectedTemplate['styles']['border-color'];
      this.multiple_legislation_cookie_font1 =              selectedTemplate['styles']['font-family'];

      this.multiple_legislation_cookie_bar_color2 =         selectedTemplate["styles"]["background-color"];
      this.multiple_legislation_cookie_bar_border_radius2 = selectedTemplate['styles']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.multiple_legislation_cookie_text_color2 =        selectedTemplate['styles']['color'];
      this.multiple_legislation_cookie_bar_opacity2 =       selectedTemplate['styles']['opacity'];
      this.multiple_legislation_border_style2 =             selectedTemplate['styles']['border-style'];
      this.multiple_legislation_cookie_bar_border_width2 =  selectedTemplate['styles']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.multiple_legislation_cookie_border_color2 =      selectedTemplate['styles']['border-color'];
      this.multiple_legislation_cookie_font2 =              selectedTemplate['styles']['font-family'];



      var data = {
        action: "gcc_restore_default_settings",
        security: settings_obj.restore_settings_nonce,
      };
      var that = this;
      jQuery.ajax({
        url: settings_obj.ajaxurl,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.success === true) {
            that.success_error_message = "Settings reset to default";
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            location.reload();
          } else {
            that.success_error_message = "Please try again.";
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = "Please try again.";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#72b85c"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    changeActiveTestBannerTabTo1() {
      if (this.active_test_banner_tab === 2) this.active_test_banner_tab = 1;
    },
    changeActiveTestBannerTabTo2() {
      if (this.active_test_banner_tab === 1) this.active_test_banner_tab = 2;
    },
    // Multiple Legislation Default Settings
    changeActiveMultipleLegislationToGDPR() {
      if (this.active_default_multiple_legislation === "ccpa")
        this.active_default_multiple_legislation = "gdpr";
    },
    changeActiveMultipleLegislationToCCPA() {
      if (this.active_default_multiple_legislation === "gdpr")
        this.active_default_multiple_legislation = "ccpa";
    },
    openCookieDropdown(){
      const dropdownarrow = document.querySelector('.cookie_arrow')
      if(this.cookie_scan_dropdown){
        dropdownarrow.classList.remove('up');
        dropdownarrow.classList.add('down');
      }
      else{
        dropdownarrow.classList.remove('down');
        dropdownarrow.classList.add('up');
      }
      this.cookie_scan_dropdown = !this.cookie_scan_dropdown;
    },
    onChangeCookieListTab() {
      this.cookie_list_tab = true;
      this.discovered_cookies_list_tab = false;
      this.scan_history_list_tab = false;
      this.cookie_scan_dropdown = !this.cookie_scan_dropdown;
      const dropdownarrow = document.querySelector('.cookie_arrow')
      dropdownarrow.classList.remove('up');
      dropdownarrow.classList.add('down');
      const tabLink = document.querySelector("a[href='#cookie_settings#cookie_list']");
        if (tabLink) {
            tabLink.click();
        }
      window.location.hash = "#cookie_settings#cookie_list#custom_cookie";
    },
    onChangeDiscoveredListTab() {
      this.cookie_list_tab = false;
      this.discovered_cookies_list_tab = true;
      this.scan_history_list_tab = false;
      this.cookie_scan_dropdown = !this.cookie_scan_dropdown;
      const dropdownarrow = document.querySelector('.cookie_arrow')
      dropdownarrow.classList.remove('up');
      dropdownarrow.classList.add('down');
      const tabLink = document.querySelector("a[href='#cookie_settings#cookie_list']");
        if (tabLink) {
            tabLink.click();
        }
      window.location.hash = "#cookie_settings#cookie_list#discovered_cookies";
    },
    onChangeScanHistoryTab() {
      this.cookie_list_tab = false;
      this.discovered_cookies_list_tab = false;
      this.scan_history_list_tab = true;
      this.cookie_scan_dropdown = !this.cookie_scan_dropdown;
      const dropdownarrow = document.querySelector('.cookie_arrow')
      dropdownarrow.classList.remove('up');
      dropdownarrow.classList.add('down');
      const tabLink = document.querySelector("a[href='#cookie_settings#cookie_list']");
        if (tabLink) {
            tabLink.click();
        }
      window.location.hash = "#cookie_settings#cookie_list#scan_history";
    },
    activateTabFromHash() {
      const hash = window.location.hash;
      if (hash === "#cookie_settings#cookie_list#custom_cookie") {
        this.cookie_scan_dropdown = !this.cookie_scan_dropdown;
        this.onChangeCookieListTab();
      } else if (hash === "#cookie_settings#cookie_list#discovered_cookies") {
        this.cookie_scan_dropdown = !this.cookie_scan_dropdown;
        this.onChangeDiscoveredListTab();
      } else if (hash === "#cookie_settings#cookie_list#scan_history") {
        this.cookie_scan_dropdown = !this.cookie_scan_dropdown;
        this.onChangeScanHistoryTab();
      }
    },
    async saveCookieSettings() {
        this.save_loading = true;
        // When Pro is activated set the values in the aceeditor
        if (this.isGdprProActive) {
          //intializing the acecode editor
          var editor = ace.edit("aceEditor");
          //getting the value of editor
          var code = editor.getValue();
          //setting the value
          this.gdpr_css_text = code;
          editor.setValue(this.gdpr_css_text);
        }
        if(this.is_iabtcf_changed && this.iabtcf_is_on){
          try {
              await this.fetchIABData(); // now REALLY waits for ajax done
          } catch (err) {
              console.error("Failed to save IAB Data", err);
          }
        }
        var that = this;
        var dataV = jQuery("#gcc-save-settings-form").serialize();
        jQuery
          .ajax({
            type: "POST",
            url: settings_obj.ajaxurl,
            data:
              dataV +
              "&action=gcc_save_admin_settings" +
              "&lang_changed=" +
              that.is_lang_changed +
              "&logo_removed=" +
              that.is_logo_removed +"&logo_removed1=" + that.is_logo_removed1 +"&logo_removed2="+ that.is_logo_removed2 +"&logo_removedML1=" + that.is_logo_removedML1 +
              "&gdpr_css_text_field=" +
              that.gdpr_css_text,
          })
          .done(function (data) {
            that.success_error_message = "Settings Saved";
            j("#gdpr-cookie-consent-save-settings-alert").css({
              "background-color": "#72b85c",
              "z-index": "10000",
            });
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            if (that.is_template_changed) {
              that.is_template_changed = false;
              location.reload();
            }
            if (that.is_iabtcf_changed) {
              that.is_iabtcf_changed = false;
              location.reload();
            }
            if (that.is_lang_changed) {
              that.is_lang_changed = false;
              location.reload();
            }
            if (that.data_reqs_switch_clicked == true) {
              that.data_reqs_switch_clicked = false;
              location.reload();
            }
            if (that.consent_log_switch_clicked == true) {
              that.consent_log_switch_clicked = false;
              location.reload();
            }
            if (that.reload_onSelect_law == true) {
              that.reload_onSelect_law = false;
              location.reload();
            }
            if (that.reload_onSafeMode == true) {
              that.reload_onSafeMode = false;
              location.reload();
            }
            if (that.is_logo_removed == true) {
              that.is_logo_removed = false;
              location.reload();
            }
            if (that.is_logo_removed1 == true) {
              that.is_logo_removed1 = false;
              location.reload();
            }
            if (that.is_logo_removed2 == true) {
              that.is_logo_removed2 = false;
              location.reload();
            }
            if (that.is_logo_removedML1 == true) {
              that.is_logo_removedML1 = false;
              location.reload();
            }
            if (that.is_logo_added == true) {
              that.is_logo_added = false;
              location.reload();
            }
            that.save_loading = false;
          })
          .fail(function () {
            that.save_loading = false;
          });
      },
    async fetchIABData(){
      var that = this;
      GVL.baseUrl = "https://appwplegalpages.b-cdn.net/";
      const gvl = new GVL();
      return gvl.readyPromise.then(() => {
      
        let data = {};
        let vendorMap = gvl.vendors;
        let purposeMap = gvl.purposes;
        let featureMap = gvl.features;
        let dataCategoriesMap = gvl.dataCategories;
        let specialPurposeMap = gvl.specialPurposes;
        let specialFeatureMap = gvl.specialFeatures;
        let purposeVendorMap = gvl.byPurposeVendorMap;

        var vendor_array = [],
          vendor_id_array = [],
          vendor_legint_id_array = [],
          data_categories_array = [],
          nayan = [];
        var feature_array = [],
          special_feature_id_array = [],
          special_feature_array = [],
          special_purpose_array = [];
        var purpose_id_array = [],
          purpose_legint_id_array = [],
          purpose_array = [],
          purpose_vendor_array = [];
        var purpose_vendor_count_array = [],
          feature_vendor_count_array = [],
          special_purpose_vendor_count_array = [],
          special_feature_vendor_count_array = [],
          legint_purpose_vendor_count_array = [],
          legint_feature_vendor_count_array = [];
        Object.keys(vendorMap).forEach((key) => {
          vendor_array.push(vendorMap[key]);
          vendor_id_array.push(vendorMap[key].id);
          if (vendorMap[key].legIntPurposes.length)
            vendor_legint_id_array.push(vendorMap[key].id);
        });
        data.vendors = vendor_array;
        data.allvendors = vendor_id_array;
        data.allLegintVendors = vendor_legint_id_array;

        Object.keys(featureMap).forEach((key) => {
          feature_array.push(featureMap[key]);
          feature_vendor_count_array.push(
            Object.keys(gvl.getVendorsWithFeature(featureMap[key].id)).length
          );
        });
        data.features = feature_array;
        data.featureVendorCount = feature_vendor_count_array;
        data.dataCategories = nayan;

        Object.keys(dataCategoriesMap).forEach((key) => {
          data_categories_array.push(dataCategoriesMap[key]);
        });
        data.dataCategories = data_categories_array;

        var legintCount = 0;
        const purposeLegint = new Map();
        Object.keys(purposeMap).forEach((key) => {
          purpose_array.push(purposeMap[key]);
          purpose_id_array.push(purposeMap[key].id);
          purpose_vendor_count_array.push(
            Object.keys(gvl.getVendorsWithConsentPurpose(purposeMap[key].id)).length
          );
          legintCount = Object.keys(
            gvl.getVendorsWithLegIntPurpose(purposeMap[key].id)
          ).length;
          legint_purpose_vendor_count_array.push(legintCount);
          if (legintCount) {
            purposeLegint.set(purposeMap[key].id, legintCount);
            purpose_legint_id_array.push(purposeMap[key].id);
          }
        });
        data.purposes = purpose_array;
        data.allPurposes = purpose_id_array;
        data.purposeVendorCount = purpose_vendor_count_array;
        data.allLegintPurposes = purpose_legint_id_array;
        data.legintPurposeVendorCount = legint_purpose_vendor_count_array;

        Object.keys(specialFeatureMap).forEach((key) => {
          special_feature_array.push(specialFeatureMap[key]);
          special_feature_id_array.push(specialFeatureMap[key].id);
          special_feature_vendor_count_array.push(
            Object.keys(gvl.getVendorsWithSpecialFeature(specialFeatureMap[key].id))
              .length
          );
        });
        data.specialFeatures = special_feature_array;
        data.allSpecialFeatures = special_feature_id_array;
        data.specialFeatureVendorCount = special_feature_vendor_count_array;

        Object.keys(specialPurposeMap).forEach((key) => {
          special_purpose_array.push(specialPurposeMap[key]);
          special_purpose_vendor_count_array.push(
            Object.keys(gvl.getVendorsWithSpecialPurpose(purposeMap[key].id)).length
          );
        });
        data.specialPurposes = special_purpose_array;
        data.specialPurposeVendorCount = special_purpose_vendor_count_array;

        Object.keys(purposeVendorMap).forEach((key) =>
          purpose_vendor_array.push(purposeVendorMap[key].legInt.size)
        );
        data.purposeVendorMap = purpose_vendor_array;
        data.secret_key = "sending_vendor_data";
        return new Promise(function (resolve, reject) {
          jQuery
            .ajax({
              type: "POST",
              url: settings_obj.ajaxurl,
              data: {
                data: JSON.stringify(data), 
                action: "gcc_enable_iab" 
              },
              dataType: "json",
            })
            .done(function (data) {
              resolve(data);
            })
            .fail(function (e) {
              that.save_loading = false;
              reject(e);
            });
        });
      });

    },
    openMediaModal() {
      var image_frame = wp.media({
        title: "Select Media for Image 1",
        multiple: false,
        library: {
          type: "image",
        },
      });
      // Open the media modal
      image_frame.open();

      // Handle the selection
      image_frame.on("select", function () {
        var selection1 = image_frame.state().get("selection").first().toJSON();

        // Update Image 1 holder and hidden input
        jQuery("#gdpr-cookie-bar-logo-holder").attr("src", selection1.url);
        jQuery("#gdpr-cookie-bar-logo-url-holder").val(selection1.url);
        alert("Please click on save changes to update the image on the banner");
      });
      this.is_logo_added = true;
    },
    deleteSelectedimage() {
      jQuery("#gdpr-cookie-bar-logo-holder").removeAttr("src");
      jQuery("#gdpr-cookie-bar-logo-url-holder").attr("value", "");
      this.is_logo_removed = true;
    },
    openMediaModal1() {
      var image_frame = wp.media({
        title: "Select Media for Image 1",
        multiple: false,
        library: {
          type: "image",
        },
      });
    
      // Open the media modal
      image_frame.open();
    
      // Handle the selection
      image_frame.on("select", function () {
        var selection1 = image_frame.state().get("selection").first().toJSON();
    
        // Update Image 1 holder and hidden input
        jQuery("#gdpr-cookie-bar-logo-holder1").attr("src", selection1.url);
        jQuery("#gdpr-cookie-bar-logo-url-holder1").val(selection1.url);
        alert("Please click on save changes to update the image on the banner");
      });
      this.is_logo_added = true;
    }, 
    deleteSelectedimage1() {
      jQuery("#gdpr-cookie-bar-logo-holder1").removeAttr("src");
      jQuery("#gdpr-cookie-bar-logo-url-holder1").attr("value", "");
      this.is_logo_removed1 = true;
    },
    openMediaModal2() {
      var image_frame = wp.media({
        title: "Select Media for Image 2",
        multiple: false,
        library: {
          type: "image",
        },
      });
    
      // Open the media modal
      image_frame.open();
    
      // Handle the selection
      image_frame.on("select", function () {
        var selection2 = image_frame.state().get("selection").first().toJSON();
    
        // Update Image 2 holder and hidden input
        jQuery("#gdpr-cookie-bar-logo-holder2").attr("src", selection2.url);
        jQuery("#gdpr-cookie-bar-logo-url-holder2").val(selection2.url);
        alert("Please click on save changes to update the image on the banner");
      });
      this.is_logo_added = true;
    },    
    deleteSelectedimage2() {
      jQuery("#gdpr-cookie-bar-logo-holder2").removeAttr("src");
      jQuery("#gdpr-cookie-bar-logo-url-holder2").attr("value", "");
      this.is_logo_removed2 = true;
    },
    openMediaModalML1() {
      var image_frame = wp.media({
        title: "Select Media for Image 1",
        multiple: false,
        library: {
          type: "image",
        },
      });
    
      // Open the media modal
      image_frame.open();
    
      // Handle the selection
      image_frame.on("select", function () {
        var selection1 = image_frame.state().get("selection").first().toJSON();
    
        // Update Image 1 holder and hidden input
        jQuery("#gdpr-cookie-bar-logo-holderML1").attr("src", selection1.url);
        jQuery("#gdpr-cookie-bar-logo-url-holderML1").val(selection1.url);
        alert("Please click on save changes to update the image on the banner");
      });
      this.is_logo_added = true;
    },    
    deleteSelectedimageML1() {
      jQuery("#gdpr-cookie-bar-logo-holderML1").removeAttr("src");
      jQuery("#gdpr-cookie-bar-logo-url-holderML1").attr("value", "");
      this.is_logo_removedML1 = true;
    },
    onSwitchScriptBlocker(script_id) {
      j("#gdpr-cookie-consent-updating-settings-alert").fadeIn(200);
      j("#gdpr-cookie-consent-updating-settings-alert").fadeOut(2000);
      var that = this;
      this.scripts_list_data[script_id - 1]["script_status"] =
        !this.scripts_list_data[script_id - 1]["script_status"];
      var status = "1";
      if (this.scripts_list_data[script_id - 1]["script_status"]) {
        status = "1";
      } else {
        status = "0";
      }
      var data = {
        action: "wpl_script_blocker",
        security:
          settings_obj.script_blocker_settings.nonces.wpl_script_blocker,
        wpl_script_action: "update_script_status",
        status: status,
        id: script_id,
      };
      jQuery.ajax({
        url: settings_obj.script_blocker_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response === true) {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    onStartScheduleScan() {
      this.schedule_scan_show = false; //make it false to close the popup

      if (this.schedule_scan_as == "once") {
        //execute schedule scan once
        this.scheduleScanOnce();

        //set value for the Next Scan Details when Once
        const dateObject = new Date(this.schedule_scan_date);
        const formattedDate = dateObject.toLocaleDateString("en-US", {
          year: "numeric",
          month: "short",
          day: "numeric",
        });
        this.next_scan_is_when = formattedDate;
      } else if (this.schedule_scan_as == "monthly") {
        //execute scan schedule monthly
        this.scanMonthly();

        //set value for the Next Scan Details when Monthly

        // Get the day of the month when the scan should run
        const dayString = this.schedule_scan_day;
        const dayNumber = parseInt(dayString.replace("Day ", ""), 10);
        const targetDayOfMonth = dayNumber;

        // Assuming this.schedule_scan_day contains the day of the month (1 to 31)
        const dayOfMonth = parseInt(
          this.schedule_scan_day.replace("Day ", ""),
          10
        );

        if (isNaN(dayOfMonth) || dayOfMonth < 1 || dayOfMonth > 31) {
          console.error("Invalid day of the month:", dayOfMonth);
        } else {
          // Get the current date and day of the month
          const currentDate = new Date();
          const currentDayOfMonth = currentDate.getDate();

          // Get the selected day of the month for scanning
          const targetDayOfMonth = dayOfMonth;

          // Get the number of days in the current month
          const currentYear = currentDate.getFullYear();
          const currentMonth = currentDate.getMonth() + 1; // Month is zero-based, so we add 1
          const daysInCurrentMonth = new Date(
            currentYear,
            currentMonth,
            0
          ).getDate();

          // Calculate the next scan date based on the current date and the selected day of the month
          let nextScanDate;
          if (
            dayOfMonth > daysInCurrentMonth ||
            currentDayOfMonth > dayOfMonth
          ) {
            // If the selected day exceeds the number of days in the current month
            // or if the current day is greater than the selected day,
            // set the next scan date to the selected day of the month in the next month
            nextScanDate = new Date(
              currentYear,
              currentMonth,
              targetDayOfMonth
            );
          } else {
            // If the current day of the month is less than or equal to the selected day of the month,
            // set the next scan date to the selected day of the month in the current month
            nextScanDate = new Date(
              currentYear,
              currentMonth - 1,
              targetDayOfMonth
            );
          }

          // Format the next scan date as needed (e.g., 'Mar 2, 2023')
          const formattedDate = nextScanDate.toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
          });
          this.next_scan_is_when = formattedDate;
        }
      } else if (this.schedule_scan_as == "never") {
        this.next_scan_is_when = "Not Scheduled";
      }
    },
    scheduleScanOnce() {
      // Define the date and time when you want the function to execute
      let targetDate = new Date(this.schedule_scan_date);

      // Parse the time entered by the user and handle both 12-hour and 24-hour formats
      const timeParts = this.schedule_scan_time_value.split(":");
      let hours = parseInt(timeParts[0], 10);
      const minutes = parseInt(timeParts[1], 10);

      // Check if the time is in 12-hour format (e.g., "01:03 AM")
      if (
        this.schedule_scan_time_value.toUpperCase().includes("PM") &&
        hours < 12
      ) {
        hours += 12;
      } else if (
        this.schedule_scan_time_value.toUpperCase().includes("AM") &&
        hours === 12
      ) {
        hours = 0;
      }

      // Set the hours and minutes in the target date
      targetDate.setHours(hours);
      targetDate.setMinutes(minutes);

      // Calculate the time difference between now and the target date
      const timeUntilExecution = targetDate - new Date();

      // Check if the target date is in the future
      if (timeUntilExecution > 0) {
        // Use setTimeout to delay the execution of scan
        setTimeout(() => {
          // start the scanning here
          this.onClickStartScan();
        }, timeUntilExecution);
      } else {
        // if the target date is in the past
        alert("Selected date is in the past. Please select a vaild date.");
        this.schedule_scan_show = true;
      }
    },
    scanMonthly() {
      // Get the day of the month when the scan should run
      const dayString = this.schedule_scan_day;
      const dayNumber = parseInt(dayString.replace("Day ", ""), 10);
      const targetDayOfMonth = dayNumber;

      if (
        isNaN(targetDayOfMonth) ||
        targetDayOfMonth <= 0 ||
        targetDayOfMonth > 31
      ) {
        alert("Invalid day of the month:", this.schedule_scan_day);
        return; // Exit if the day is invalid
      }

      // Define the time (hours and minutes)
      const timeParts = this.schedule_scan_time_value.split(":");
      let hours = parseInt(timeParts[0], 10);
      const minutes = parseInt(timeParts[1], 10);

      // Check if the time is in 12-hour format (e.g., "01:03 AM")
      if (
        this.schedule_scan_time_value.toUpperCase().includes("PM") &&
        hours < 12
      ) {
        hours += 12;
      } else if (
        this.schedule_scan_time_value.toUpperCase().includes("AM") &&
        hours === 12
      ) {
        hours = 0;
      }
      // Define a function to check and run the scan when the conditions are met
      const checkAndRunScan = () => {
        const currentDate = new Date();
        const currentDayOfMonth = currentDate.getDate();
        const currentHours = currentDate.getHours();
        const currentMinutes = currentDate.getMinutes();

        if (
          currentDayOfMonth === targetDayOfMonth &&
          currentHours === hours &&
          currentMinutes === minutes
        ) {
          // The conditions are met; execute the scan
          this.onClickStartScan();
        }
      };

      // Set an interval to check if the conditions for running the scan are met
      setInterval(checkAndRunScan, 60000);
    },
    onClickStartScan(singlePageScan = false) {
      this.scan_in_progress = true;
      this.continue_scan = 1;
      this.doScan(singlePageScan);
    },
    doScan(singlePageScan = false) {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "check_api",
      };
      var scanbar = j(".wpl_scanbar");
      scanbar.html(
        '<span style="float:left; height:40px; line-height:40px;">' +
          settings_obj.cookie_scan_settings.labels.checking_api +
          '</span> <img src="' +
          settings_obj.cookie_scan_settings.loading_gif +
          '" style="display:inline-block;" />'
      );
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          scanbar.html("");
          if (data.response === true) {
            that.scanNow(singlePageScan);
          } else {
            that.serverUnavailable(scanbar, data.message);
          }
        },
        error: function () {
          scanbar.html("");
          that.showErrorScreen(settings_obj.cookie_scan_settings.labels.error);
        },
      });
    },
    scanNow(singlePageScan = false) {
      var html = this.makeHtml();
      var scanbar = j(".gdpr_scanbar");
      scanbar.html(html);
      j(".gdpr_scanbar_staypage").css({ display: "flex" }).show();
      this.attachScanStop();
      j(".gdpr_scanlog").css({ display: "block", opacity: 0 }).animate(
        {
          opacity: 1,
          height: "auto",
        },
        1000
      );
      this.takePages(0, 0, 0, 0, singlePageScan);
    },
    animateProgressBar(offset, total, msg) {
      var prgElm = j(".gdpr_progress_bar");
      var w = prgElm.width();
      var sp = 100 / total;
      var sw = w / total;
      var cw = sw * offset;
      var cp = sp * offset;

      cp = cp > 100 ? 100 : cp;
      cp = Math.floor(cp < 1 ? 1 : cp);

      cw = cw > w ? w : cw;
      cw = Math.floor(cw < 1 ? 1 : cw);
      j(".gdpr_progress_bar_inner")
        .stop(true, true)
        .animate({ width: cw + "px" }, 300, function () {
          j(".gdpr_progress_action_main").html(msg);
        })
        .html(cp + "%");
    },
    appendLogAnimate(data, offset) {
      var that = this;
      if (data.length > offset) {
        offset++;
        var speed = 300 / data.length;
        setTimeout(function () {
          that.appendLogAnimate(data, offset);
        }, speed);
      }
    },
    takePages(offset, limit, total, scan_id, singlePageScan = false) {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "get_pages",
        offset: offset,
      };
      if (limit) {
        data["limit"] = limit;
      }
      if (total) {
        data["total"] = total;
      }
      if (scan_id) {
        data["scan_id"] = scan_id;
      }
      // Fake progress.
      this.animateProgressBar(
        1,
        100,
        settings_obj.cookie_scan_settings.labels.finding
      );
      jQuery.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (that.isGdprProActive) {
            that.scan_id =
              typeof data.scan_id != "undefined" ? data.scan_id : 0;
            if (that.continue_scan == 0) {
              return false;
            }
            if (typeof data.response != "undefined" && data.response === true) {
              that.appendLogAnimate(data.log, 0);
              var new_offset = parseInt(data.offset) + parseInt(data.limit);
              if (data.total - 1 > new_offset) {
                // substract 1 from total because of home page.
                that.takePages(
                  new_offset,
                  data.limit,
                  data.total,
                  data.scan_id
                );
              } else {
                j(".wpl_progress_action_main").html(
                  settings_obj.cookie_scan_settings.labels.scanning
                );
                that.scanPages(data.scan_id, 0, data.total);
              }
            } else {
              that.showErrorScreen(
                settings_obj.cookie_scan_settings.labels.error
              );
            }
          } else {
            const urlParams = new URLSearchParams(window.location.search);
            const scanUrlParam = urlParams.get("scan_url");
            var ndata = {
              action: "wpl_cookie_scanner_view_capabilities",
              security:
                settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
              no_of_scan: singlePageScan == true ? 1 : data.log.length,
              offset: offset,
              scan_id: scan_id ? scan_id : 0,
              total_pages: data.total,
            };
            jQuery
              .ajax({
                url: settings_obj.cookie_scan_settings.ajax_url,
                data: ndata,
                dataType: "json",
                type: "POST",
              })
              .done(function (response) {
                if (
                  response.success &&
                  response.data.connection_status === "active"
                ) {
                  that.scan_id = data.scan_id !== undefined ? data.scan_id : 0;
                  if (that.continue_scan == 0) {
                    return false;
                  }
                  if (data.response === true) {
                    that.appendLogAnimate(data.log, 0);
                    var new_offset =
                      parseInt(data.offset) + parseInt(data.limit);
                    if (data.total - 1 > new_offset) {
                      // subtract 1 from total because of home page.
                      that.takePages(
                        new_offset,
                        data.limit,
                        data.total,
                        data.scan_id
                      );
                    } else {
                      jQuery(".wpl_progress_action_main").html(
                        settings_obj.cookie_scan_settings.labels.scanning
                      );
                      that.scanPages(data.scan_id, 0, data.total);
                    }
                  } else {
                    that.showErrorScreen(
                      settings_obj.cookie_scan_settings.labels.error
                    );
                  }
                } else {
                  that.showScanNowPopup();
                }
              })
              .fail(function () {
                if (that.continue_scan == 0) {
                  return false;
                }
                that.showErrorScreen(
                  settings_obj.cookie_scan_settings.labels.error
                );
              });
          }
        },
        error: function () {
          if (that.continue_scan == 0) {
            return false;
          }
          that.showErrorScreen(settings_obj.cookie_scan_settings.labels.error);
        },
      });
    },

    showScanNowPopup() {
      this.$nextTick(() => {
        const scanBtn = jQuery(".scan-now-btn");
        const popup = jQuery("#popup-site-excausted");
        const cancelButton = jQuery(".popup-image");
        popup.fadeIn();
        cancelButton.off("click").on("click", function (e) {
          popup.fadeOut();
          localStorage.setItem("auto_scan_process_started", "false");
          window.location.reload();
        });
      });
    },
    scanPages(scan_id, offset, total) {
      var that = this;
      var scanbar = j(".gdpr_scanbar");
      this.pollCount = 0;
      var hash = Math.random().toString(36).replace("0.", "");
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "scan_pages",
        offset: offset,
        scan_id: scan_id,
        total: total,
        hash: hash,
      };
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          that.scan_id = typeof data.scan_id != "undefined" ? data.scan_id : 0;
          if (that.continue_scan == 0) {
            return false;
          }
          if (data.response == true) {
            this.scan_in_progress = false;
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.delete("auto_scan");
            that.getScanCookies(scan_id, offset, total, hash);
          } else {
            scanbar.html("");
            j(".wpl_scanbar_staypage").hide();
            that.serverUnavailable(scanbar, data.message);
          }
        },
        error: function () {
          var current = that;
          if (that.continue_scan == 0) {
            return false;
          }
          // error and retry function.

          that.animateProgressBar(
            offset,
            total,
            settings_obj.cookie_scan_settings.labels.retrying
          );
          setTimeout(function () {
            current.scanPages(scan_id, offset, total);
          }, 2000);
        },
      });
    },
    getScanCookies(scan_id, offset, total, hash) {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "get_post_scan_cookies",
        offset: offset,
        scan_id: scan_id,
        total: total,
        hash: hash,
      };
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response == true) {
            var prg_offset = parseInt(offset) + parseInt(data.total_scanned);
            var prg_msg =
              settings_obj.cookie_scan_settings.labels.scanning + " ";
            that.appendLogAnimate(data.log, 0);
            if (data.continue === true) {
              that.scanPages(data.scan_id, data.offset, data.total);
            } else {
              prg_msg = settings_obj.cookie_scan_settings.labels.finished;
              prg_msg +=
                " (" +
                settings_obj.cookie_scan_settings.labels.total_cookies_found +
                ": " +
                data.total_cookies +
                ")";
              that.showSuccessScreen(prg_msg, scan_id, 1);
            }
            that.animateProgressBar(prg_offset, total, prg_msg);
          } else {
            var current = that;
            if (that.pollCount < 10) {
              that.pollCount++;
              setTimeout(function () {
                current.getScanCookies(
                  data.scan_id,
                  data.offset,
                  data.total,
                  data.hash
                );
              }, 10000);
            } else {
              that.showErrorScreen("Something went wrong, please scan again");
            }
          }
        },
        error: function () {
          var current = that;
          if (that.continue_scan == 0) {
            return false;
          }
          if (that.pollCount < 10) {
            setTimeout(function () {
              that.getScanCookies(offset, scan_id, total, hash);
            }, 5000);
          } else {
            that.showErrorScreen("Something went wrong, please scan again");
          }
        },
      });
    },
    makeHtml() {
      return (
        '<div class="gdpr_scanlog">' +
        '<div class="gdpr_progress_bar">' +
        '<span class="gdpr_progress_bar_inner gdpr_progress_bar_inner_restructured">' +
        "</span>" +
        "</div>" +
        '<div class="gdpr_progress_action_main">' +
        settings_obj.cookie_scan_settings.labels.finding +
        "</div>" +
        '<div class="gdpr_scanlog_bar"><button type="button" class="pull-right gdpr_stop_scan">' +
        settings_obj.cookie_scan_settings.labels.stop +
        "</button></div>" +
        "</div>"
      );
    },
    attachScanStop() {
      var that = this;
      j(".gdpr_stop_scan").click(function () {
        that.stopScan();
      });
    },
    stopScan() {
      if (this.continue_scan == 0) {
        return false;
      }
      if (confirm(settings_obj.cookie_scan_settings.labels.ru_sure)) {
        this.continue_scan = 0;
        this.stoppingScan(this.scan_id);
      }
    },
    stoppingScan(scan_id) {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "stop_scan",
        scan_id: scan_id,
      };
      j(".gdpr_stop_scan")
        .html(settings_obj.cookie_scan_settings.labels.stoping)
        .css({ opacity: ".5" });
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          that.showSuccessScreen(
            settings_obj.cookie_scan_settings.labels.scanning_stopped,
            scan_id,
            data.total
          );
        },
        error: function () {
          // error function.
          that.showErrorScreen(settings_obj.cookie_scan_settings.labels.error);
        },
      });
    },
    serverUnavailable: function (elm, msg) {
      elm.html(
        '<div style="background:#ffffff; border:solid 1px #cccccc; color:#333333; padding:5px;">' +
          msg +
          "</div>"
      );
    },
    showErrorScreen: function (error_msg) {
      var html =
        '<button type="button" class="pull-right gdpr_scan_again" style="margin-left:5px;">' +
        settings_obj.cookie_scan_settings.labels.scan_again +
        "</button>";
      j(".gdpr_scanlog_bar").html(html);
      j(".gdpr_progress_action_main").html(error_msg);
      this.success_error_message = error_msg;
      j("#gdpr-cookie-consent-save-settings-alert").css(
        "background-color",
        "#e55353"
      );
      j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
      j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
      j(".gdpr_scanbar_staypage").hide();
      this.scanAgain();
    },
    showSuccessScreen(success_msg, scan_id, total) {
      var html =
        '<button type="button" class="pull-right gdpr_scan_again" style="margin-left:5px;">' +
        settings_obj.cookie_scan_settings.labels.scan_again +
        "</button>";
      html += '<span class="spinner" style="margin-top:5px"></span>';
      j(".gdpr_scanlog_bar").html(html);
      j(".gdpr_progress_action_main").html(success_msg);
      this.success_error_message = success_msg;
      j("#gdpr-cookie-consent-save-settings-alert").css(
        "background-color",
        "#72b85c"
      );
      j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
      j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
      j(".gdpr_scanbar_staypage").hide();
      this.showScanCookieList();
      this.scanAgain();
      setTimeout(() => {
        localStorage.setItem("auto_scan_process_started", "false");
        window.location.reload();
      }, 3000);
    },
    scanAgain() {
      var that = this;
      j(".gdpr_scan_again")
        .unbind("click")
        .click(function () {
          that.continue_scan = 1;
          that.scanNow();
        });
    },
    onClickDeleteCookie() {
      var that = this;
      var data = {
        action: "wpl_cookies_deletion",
      };
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          that.showSuccessScreen("Cookies Cleared Successfully!");
          window.location.reload();
        },
        error: function () {
          // error function.
          that.showErrorScreen("Some error occuered");
        },
      });
    },
    onScriptCategorySelect(values) {
      if (!values) {
        this.success_error_message = "You must select a category.";
        j("#gdpr-cookie-consent-save-settings-alert").css(
          "background-color",
          "#e55353"
        );
        j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
        j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        return false;
      }
      var that = this;
      var category_code = values.split(",")[0];
      var script_id = values.split(",")[1];
      for (let i = 0; i < this.category_list_options.length; i++) {
        if (this.category_list_options[i]["code"] === category_code) {
          if (
            this.scripts_list_data[script_id - 1]["script_category"] ===
            category_code
          ) {
            this.success_error_message = "Category updated successfully";
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            return false;
          } else {
            this.scripts_list_data[script_id - 1]["script_category"] =
              this.category_list_options[i].code;
            this.scripts_list_data[script_id - 1]["script_category_label"] =
              this.category_list_options[i].label;
            break;
          }
        }
      }
      var data = {
        action: "wpl_script_blocker",
        security:
          settings_obj.script_blocker_settings.nonces.wpl_script_blocker,
        wpl_script_action: "update_script_category",
        category: category_code,
        id: script_id,
      };
      jQuery.ajax({
        url: settings_obj.script_blocker_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response === true) {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    onSaveCustomCookie() {
      var parent = j(".gdpr-custom-save-cookie").parents(
        "div.gdpr-add-custom-cookie-form"
      );
      var gdpr_addcookie = parent.find('input[name="gdpr_addcookie"]').val();
      if (gdpr_addcookie == 1) {
        var pattern =
          /^((http|https):\/\/)?([a-zA-Z0-9_][-_a-zA-Z0-9]{0,62}\.)+([a-zA-Z0-9]{1,10})$/gm;
        var cname = this.custom_cookie_name;
        var cdomain = this.custom_cookie_domain;
        var cduration = this.custom_cookie_duration;
        var ccategory = this.custom_cookie_category;
        var ctype = this.custom_cookie_type;
        var cdesc = this.custom_cookie_description;
        if (!cname) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Name";
          parent
            .find('input[name="gdpr-cookie-consent-custom-cookie-name"]')
            .focus();
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          return false;
        }
        if (!cdomain) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Domain";
          parent
            .find('input[name="gdpr-cookie-consent-custom-cookie-domain"]')
            .focus();
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          return false;
        } else {
          if (!pattern.test(cdomain)) {
            this.success_error_message = "Cookie domain is not valid.";
            parent
              .find('input[name="gdpr-cookie-consent-custom-cookie-domain"]')
              .focus();
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            return false;
          }
        }
        if (!cduration) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Duration";
          parent
            .find('input[name="gdpr-cookie-consent-custom-cookie-duration"]')
            .focus();
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          return false;
        }
        if (!ctype) {
          this.success_error_message = "Please select a Cookie Type";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          return false;
        }
        if (!ccategory) {
          this.success_error_message = "Please select a Cookie Category";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          return false;
        }
        var cookie_arr = {
          cname: cname,
          cdomain: cdomain,
          cduration: cduration,
          ccategory: ccategory,
          ctype: ctype,
          cdesc: cdesc,
        };
        this.saveCustomPostCookies(cookie_arr);
      }
    },
    saveCookieUpdateSettings() {
      var cookie_post_arr = [];
      var error = false;
      for (let i = 0; i < this.post_cookie_list_length; i++) {
        var pattern =
          /^((http|https):\/\/)?([a-zA-Z0-9_][-_a-zA-Z0-9]{0,62}\.)+([a-zA-Z0-9]{1,10})$/gm;
        var cid = this.post_cookie_list[i]["id_gdpr_cookie_post_cookies"];
        var cname = this.post_cookie_list[i]["name"];
        var cdomain = this.post_cookie_list[i]["domain"];
        var cduration = this.post_cookie_list[i]["duration"];
        var ccategory = this.post_cookie_list[i]["category_id"];
        var ctype = this.post_cookie_list[i]["type"];
        var cdesc = this.post_cookie_list[i]["description"];
        if (!cname) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Name";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          error = true;
        }
        if (!cdomain) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Domain";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          error = true;
        } else {
          if (!pattern.test(cdomain)) {
            this.success_error_message = "Cookie Domain is not valid.";
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            error = true;
          }
        }
        if (!cduration) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Duration";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          error = true;
        }
        var cookie_arr = {
          cid: cid,
          cname: cname,
          cdomain: cdomain,
          cduration: cduration,
          ccategory: ccategory,
          ctype: ctype,
          cdesc: cdesc,
        };
        cookie_post_arr.push(cookie_arr);
      }
      if (error) {
        return false;
      }
      this.updatePostCookie(cookie_post_arr);
    },
    updatePostCookie: function (cookie_arr) {
      var that = this;
      var data = {
        action: "gdpr_cookie_custom",
        security: settings_obj.cookie_list_settings.nonces.gdpr_cookie_custom,
        gdpr_custom_action: "update_post_cookie",
        cookie_arr: cookie_arr,
      };
      j.ajax({
        url: settings_obj.cookie_list_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response === true) {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            that.getPostCookieList();
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    deletePostCookie(cookie_id) {
      var that = this;
      var data = {
        action: "gdpr_cookie_custom",
        security: settings_obj.cookie_list_settings.nonces.gdpr_cookie_custom,
        gdpr_custom_action: "delete_post_cookie",
        cookie_id: cookie_id,
      };
      j.ajax({
        url: settings_obj.cookie_list_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response === true) {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            that.getPostCookieList();
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    hideCookieForm() {
      this.custom_cookie_name = "";
      this.custom_cookie_domain = "";
      this.custom_cookie_description = "";
      this.custom_cookie_category = 1;
      this.custom_cookie_type = "HTTP";
      this.show_custom_form = false;
      this.show_add_custom_button = true;
      this.is_custom_cookie_duration_disabled = false;
      this.custom_cookie_duration = "";
      this.show_custom_cookie_popup = false;
    },
    saveCustomPostCookies(cookie_data) {
      var that = this;
      var data = {
        action: "gdpr_cookie_custom",
        security: settings_obj.cookie_list_settings.nonces.gdpr_cookie_custom,
        gdpr_custom_action: "save_post_cookie",
        cookie_arr: cookie_data,
      };
      j.ajax({
        url: settings_obj.cookie_list_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response === true) {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            that.hideCookieForm();
            that.getPostCookieList();
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    getPostCookieList: function () {
      var that = this;
      var data = {
        action: "gdpr_cookie_custom",
        security: settings_obj.cookie_list_settings.nonces.gdpr_cookie_custom,
        gdpr_custom_action: "get_post_cookies_list",
      };
      j.ajax({
        url: settings_obj.cookie_list_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response) {
            that.post_cookie_list_length = data.total;
            that.post_cookie_list = data.post_list;
            that.setPostListValues();
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    saveGCMDefault(){
      var that = this;
      var newObj = {
        region: that.newRegion.region,
        ad_storage: that.newRegion.ad_storage ? 'granted' : 'denied',
        analytics_storage: that.newRegion.analytics_storage ? 'granted' : 'denied',
        ad_user_data: that.newRegion.ad_user_data ? 'granted' : 'denied',
        ad_personalization: that.newRegion.ad_personalization ? 'granted' : 'denied',
        functionality_storage: that.newRegion.functionality_storage ? 'granted' : 'denied',
        personalization_storage: that.newRegion.personalization_storage ? 'granted' : 'denied',
        security_storage: that.newRegion.security_storage ? 'granted' : 'denied'
      };
      if(that.edit_region == false){
        that.regions.push(Object.assign({}, newObj));
      }
      else{
        that.regions[that.edit_region - 1] = newObj;
      }
      jQuery
        .ajax({
          type: "POST",
          url: settings_obj.ajaxurl,
          data: "regionArray=" + JSON.stringify(that.regions) + "&action=gcc_save_gcm_region_settings",
        })
        .done(function (data) {
          if(that.edit_region == false){
            that.success_error_message = "Region added";
          }
          else{
            that.success_error_message = "Region edited successfully";
          }
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#72b85c"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          that.edit_region = false;
      });
      this.add_region = false;
      this.newRegion = {
        region: 'All',
        ad_storage: false,
        analytics_storage: false,
        ad_user_data: false,
        ad_personalization: false,
        functionality_storage: false,
        personalization_storage: false,
        security_storage: true
      }
      
    },
    close_region_popup(){
      this.add_region = false;
      this.edit_region = false;
      this.newRegion = {
        region: 'All',
        ad_storage: false,
        analytics_storage: false,
        ad_user_data: false,
        ad_personalization: false,
        functionality_storage: false,
        personalization_storage: false,
        security_storage: true
      }
    },
    delete_gcm_data(index, event){
      event.preventDefault();
      var that = this;
      if(that.regions.length > 1){
        that.regions.splice(index, 1);
        jQuery
        .ajax({
          type: "POST",
          url: settings_obj.ajaxurl,
          data: "regionArray=" + JSON.stringify(that.regions) + "&action=gcc_save_gcm_region_settings",
        })
        .done(function (data) {
          that.success_error_message = "Region removed";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#72b85c"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        });
      }
      else{
        that.success_error_message = "You need atleast 1 default setting";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#72b85c"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
      }
    },
    edit_region_entry(index, event){
      event.preventDefault();
      this.newRegion = {
        region: this.regions[index].region,
        ad_storage: this.regions[index].ad_storage == 'granted' ? true : false,
        analytics_storage: this.regions[index].analytics_storage == 'granted' ? true : false,
        ad_user_data: this.regions[index].ad_user_data == 'granted' ? true : false,
        ad_personalization: this.regions[index].ad_personalization == 'granted' ? true : false,
        functionality_storage: this.regions[index].functionality_storage == 'granted' ? true : false,
        personalization_storage: this.regions[index].personalization_storage == 'granted' ? true : false,
        security_storage: true
      }
      this.edit_region = index + 1;
      this.add_region = true;
    },
    onSwitchABTestingEnable() {
      j("#gdpr-cookie-consent-updating-settings-alert")
        .fadeIn(200)
        .fadeOut(2000);
      this.ab_testing_enabled = !this.ab_testing_enabled;
      this.cookie_on_frontend1 = true;
      this.cookie_on_frontend2 = true;
      if (this.ab_testing_enabled === false) this.active_test_banner_tab = 1;

      var dataV = jQuery("#gcc-save-settings-form").serialize();
      // Make the AJAX request to save the new state
      jQuery
        .ajax({
          type: "POST",
          url: settings_obj.ajaxurl,
          data: {
            action: "ab_testing_enable",
            "gcc-ab-testing-enable": this.ab_testing_enabled, // Add the key with the updated value
          },
        })
        .done(function (data) {
          window.location.reload();
          // Show success message
          that.success_error_message = "Settings Saved";
          j("#gdpr-cookie-consent-save-settings-alert")
            .css("background-color", "#72b85c")
            .fadeIn(400)
            .fadeOut(2500, function () {
              // Optionally reload the page or perform other actions
            });
        })
        .fail(function (error) {
          console.error("AJAX call failed:", error);
          alert(
            "An error occurred while saving the settings. Please try again."
          );
        });
    },
    onSwitchABTestingAuto() {
      this.ab_testing_auto = !this.ab_testing_auto;
    },
    OnEnableGeotargeting() {
      this.enable_geotargeting = !this.enable_geotargeting;
    },
    onSubmitIntegrations() {
      let that = this;

      jQuery
        .ajax({
          type: "POST",
          url: settings_obj.ajaxurl,
          data: dataV + "&action=wpl_cookie_consent_integrations_settings",
        })
        .done(function (data) {
          if (data.success) {
            that.alert_message = that.enable_geotargeting
              ? "Maxmind Integrated"
              : "Settings Saved";
            j("#wpl-cookie-consent-integrations-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#wpl-cookie-consent-integrations-alert").fadeIn(400);
            j("#wpl-cookie-consent-integrations-alert").fadeOut(2500);
          } else {
            that.alert_message = "Please enter a valid license key";
            j("#wpl-cookie-consent-integrations-alert").css(
              "background-color",
              "#e55353"
            );
            j("#wpl-cookie-consent-integrations-alert").fadeIn(400);
            j("#wpl-cookie-consent-integrations-alert").fadeOut(2500);
          }
          j("#wpl-cookie-consent-overlay").css("display", "none");
          spinner.css({ visibility: "hidden" });
          spinner.hide();
          location.reload();
        });
    },
  },
  mounted() {
    if (window.vueMounted) return; // Prevent duplicate execution
    window.vueMounted = true; // Mark as mounted
    j("#gdpr-before-mount").css("display", "none");

    if (settings_obj.is_user_connected) {
      if (performance.navigation.type !== 1) {
        const urlParams = new URLSearchParams(window.location.search);
        const scanUrlParam = urlParams.get("scan_url");
        // Check if the 'scan' parameter is present and has the value '1'
        if (scanUrlParam) {
          // Run the onClickStartScan() method
          const singlePageScan = true;
          this.onClickStartScan(singlePageScan);
        }
      }
    }
    // restore the sub tab of the cookie list.
    this.activateTabFromHash();
    this.setValues();
    this.setPostListValues();
    j(".gdpr-cookie-consent-settings-nav .nav .nav-item .nav-link").on(
      "click",
      function () {
        let adminbar_height = j("#wpadminbar").height();
        let nav_bar_distance = j(".gdpr-cookie-consent-settings-nav").offset()
          .top;
        let scrolled_distance = nav_bar_distance - j(window).scrollTop();
        if (scrolled_distance <= adminbar_height) {
          window.scroll(0, nav_bar_distance - adminbar_height);
        }
      }
    );
    if (this.scan_cookie_list_length > 0) {
      this.setScanListValues();
    }
    //Make AceEditor ReadOnly for the Free
    if (this.isPluginVersionLessOrEqual("2.5.2")) {
      if (!this.isGdprProActive) {
        var editor = ace.edit("aceEditorFree");
        editor.setValue(this.gdpr_css_text_free);
        editor.setReadOnly(true);
      }
    }

    //preventing pricing page popup on entering in input field in whitelist scripts section
    jQuery(document).on(
      "keydown",
      ".wpl_name.wpl-whitelist-name-field",
      function (event) {
        if (event.key === "Enter") {
          event.preventDefault();
        }
      }
    );
    jQuery(document).on(
      "keydown",
      ".wpl-whitelist-plus-script-field",
      function (event) {
        if (event.key === "Enter") {
          event.preventDefault();
        }
      }
    );

    // Add a new input field for whitelist
    jQuery(document).on("click", ".wpl_add_url", function () {
      let container_div = jQuery(this).closest("div");
      let templ = jQuery(".wpl-url-template").get(0).innerHTML;
      container_div.append(templ);
    });
    // Remove new input field for whitelist
    jQuery(document).on("click", ".wpl_remove_url", function () {
      let container_div = jQuery(this).closest("div");
      container_div.remove();
    });
    // Remove and save the whole tab for whitelist script
    jQuery(document).on("click", ".wpl_script_save", wpl_script_save);
    function wpl_script_save() {
      var btn = jQuery(this);
      var btn_html = btn.html();

      var container_div = btn.closest(".wpl-panel");
      var type = "whitelist_script";
      var action = btn.data("action");
      var id = btn.data("id");
      if (action == "save" || action == "remove") {
        btn.html(
          '<div class="wpl-loader"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>'
        );
      }

      // Values
      var data = {};
      container_div.find(":input").each(function () {
        if (jQuery(this).attr("type") === "button") return;
        if (typeof jQuery(this).attr("name") === "undefined") return;
        if (!jQuery(this).data("name")) return;
        if (jQuery(this).attr("type") === "checkbox") {
          data[jQuery(this).data("name")] = jQuery(this).is(":checked");
        } else if (jQuery(this).attr("type") === "radio") {
          if (jQuery(this).is(":checked")) {
            data[jQuery(this).data("name")] = jQuery(this).val();
          }
        } else if (jQuery(this).data("name") === "urls") {
          let curValue = data[jQuery(this).data("name")];
          if (typeof curValue === "undefined") curValue = [];
          curValue.push(jQuery(this).val());
          data[jQuery(this).data("name")] = curValue;
        } else if (jQuery(this).data("name") === "dependency") {
          //key value arrays with string keys aren't stringified to json.
          let curValue = data[jQuery(this).data("name")];
          if (typeof curValue === "undefined") curValue = [];
          curValue.push(jQuery(this).data("url") + "|:|" + jQuery(this).val());
          data[jQuery(this).data("name")] = curValue;
        } else {
          data[jQuery(this).data("name")] = jQuery(this).val();
        }
      });
      jQuery.ajax({
        type: "POST",
        url: settings_obj.ajaxurl,
        data: {
          action: "wpl_script_save",
          "wpl-save": true,
          type: type,
          button_action: action,
          id: id,
          data: JSON.stringify(data),
          _wpnonce: settings_obj.nonce, // Pass the nonce
        },
        success: function (response) {
          if (response.success) {
            if (action === "save") {
              btn.html(btn_html);
            }
            if (action === "remove") {
              container_div.remove();
              btn.html(btn_html);
            }
          }
        },
      });
    }

    //add new tab
    jQuery(document).on("click", ".wpl_script_add", wpl_script_add);
    function wpl_script_add() {
      var btn = jQuery(this);
      var btn_html = btn.html();
      var type = "whitelist_script";
      btn.html(
        '<div class="wpl-loader"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>'
      );

      jQuery
        .ajax({
          type: "POST",
          url: settings_obj.ajaxurl,
          data: {
            action: "wpl_script_add",
            type: type,
          },
          success: function (response) {
            if (response.success) {
              btn.before(response.html);
              btn.html(btn_html);
            }
          },
        })
        .done(function (data) {
          //
        });
    }
    j("#wpl-cookie-consent-integrations-loader").css("display", "none");
    var spinner = j(".wpl_integrations_spinner");
    spinner.css({ visibility: "hidden" });
    spinner.hide();
    // automatic start scanning.
    const urlParams = new URLSearchParams(window.location.search);
    if (
      urlParams.get("auto_scan") === "true" &&
      !this.scan_in_progress &&
      !localStorage.getItem("auto_scan_executed")
    ) {
      // Mark that the 'auto_scan' process has been executed
      localStorage.setItem("auto_scan_executed", "true");
      localStorage.setItem("auto_scan_process_started", "true");
      // Trigger the scan
      this.onClickStartScan();
    }

    //For fixing quill js buttons accessibility issues
    this.$nextTick(() => {
      const quillLabels = {
        "ql-bold": "Bold",
        "ql-italic": "Italic",
        "ql-underline": "Underline",
        "ql-code-block": "Code Block",
        "ql-strike": "Strikethrough",
        "ql-link": "Insert Link",
        "ql-image": "Insert Image",
        "ql-list": "List",
        "ql-clean": "Remove Formatting",
        "ql-align": "Align Text",
        "ql-blockquote": "Blockquote",
        "ql-indent": "Indent Text",
        "ql-video": "Insert Video",
        "ql-header": "Header",
        "ql-color": "Text Color",
        "ql-background": "Background Color",
        "ql-preview": "Preview",
      };

      Object.entries(quillLabels).forEach(([className, label]) => {
        const buttons = document.querySelectorAll(`.ql-toolbar .${className}`);
        buttons.forEach((button) => {
          button.setAttribute("aria-label", label);
          button.setAttribute("title", label);
        });
      });

      // Fix for Ace Editor’s textarea
      const observer = new MutationObserver(() => {
        const aceInput = document.querySelector(".ace_text-input");
        if (aceInput) {
          aceInput.setAttribute("aria-hidden", "true");
          aceInput.setAttribute("tabindex", "-1");
          aceInput.setAttribute("role", "presentation");
          aceInput.removeAttribute("aria-label"); // optional, but removes confusion
          aceInput.removeAttribute("title"); // in case any tooltips are there

          observer.disconnect();
        }
      });

      observer.observe(document.body, { childList: true, subtree: true });
      setTimeout(() => observer.disconnect(), 10000);

      // First: For ab_testing_period_text_field
      const abInterval = setInterval(() => {
        const inputs = document.querySelectorAll(
          'input[name="ab_testing_period_text_field"]'
        );

        inputs.forEach((input) => {
          if (
            !input.hasAttribute("aria-label") &&
            !input.hasAttribute("aria-labelledby")
          ) {
            input.setAttribute("aria-label", "A/B Testing Period");
          }
        });

        if (inputs.length) clearInterval(abInterval);
      }, 300);

      setTimeout(() => clearInterval(abInterval), 7000); // safety timeout

      // Second: For display-time inputs
      const timeInterval = setInterval(() => {
        const timeInputs = document.querySelectorAll("input.display-time");

        timeInputs.forEach((input) => {
          if (
            !input.hasAttribute("aria-label") &&
            !input.hasAttribute("aria-labelledby")
          ) {
            input.setAttribute("aria-label", "Choose time");
          }
        });

        if (timeInputs.length) clearInterval(timeInterval);
      }, 300);

      setTimeout(() => clearInterval(timeInterval), 7000);
    });
  },
  icons: { cilPencil, cilSettings, cilInfo, cibGoogleKeep },
});
window.gen = gen;

var app = new Vue({
  el: "#gdpr-cookie-consent-settings-app-wizard",
  data() {
    return {
      labelIcon: {},
      labelIconNew: {
        labelOn: "\u2713",
        labelOff: "\uD83D\uDD12",
      },
      isGdprProActive: "1" === settings_obj.is_pro_active,
      disableSwitch: false,
      is_template_changed: false,
      is_lang_changed: false,
      json_templates: settings_obj.templates,
      is_logo_removed: false,
      appendField: ".gdpr-cookie-consent-settings-container",
      configure_image_url: require("../admin/images/configure-icon.png"),
      pluginBasePath: '/wp-content/plugins/gdpr-cookie-consent/includes/templates/logo_images/',
      closeOnBackdrop: true,
      centered: true,
      accept_button_popup: false,
      button_readmore_popup: false,
      button_readmore_popup1: false,
      button_readmore_popup2: false,
      revoke_consent_popup: false,
      revoke_consent_popup1: false,
      revoke_consent_popup2: false,
      accept_all_button_popup: false,
      decline_button_popup: false,
      show_script_blocker: false,
      settings_button_popup: false,
      confirm_button_popup: false,
      cancel_button_popup: false,
      opt_out_link_popup: false,
      schedule_scan_show: false,
      scripts_list_total: settings_obj.script_blocker_settings.hasOwnProperty(
        "scripts_list"
      )
        ? settings_obj.script_blocker_settings.scripts_list["total"]
        : 0,
      scripts_list_data: settings_obj.script_blocker_settings.hasOwnProperty(
        "scripts_list"
      )
        ? settings_obj.script_blocker_settings.scripts_list["data"]
        : [],
      category_list_options:
        settings_obj.script_blocker_settings.hasOwnProperty("category_list")
          ? settings_obj.script_blocker_settings["category_list"]
          : [],
      cookie_is_on:
        settings_obj.the_options.hasOwnProperty("is_on") &&
        (true === settings_obj.the_options["is_on"] ||
          1 === settings_obj.the_options["is_on"])
          ? true
          : false,
      iabtcf_is_on:
        settings_obj.the_options.hasOwnProperty("is_iabtcf_on") &&
        (true === settings_obj.the_options["is_iabtcf_on"] ||
          1 === settings_obj.the_options["is_iabtcf_on"])
          ? true
          : false,
      gacm_is_on:
        settings_obj.the_options.hasOwnProperty("is_gacm_on") &&
        (true === settings_obj.the_options["is_gacm_on"] ||
          1 === settings_obj.the_options["is_gacm_on"])
          ? true
          : false,
      iabtcf_msg: `We and our <a id = "vendor-link" href = "#" data-toggle = "gdprmodal" data-target = "#gdpr-gdprmodal">836 partners</a> use cookies and other tracking technologies to improve your experience on our website. We may store and/or access information on a device and process personal data, such as your IP address and browsing data, for personalised advertising and content, advertising and content measurement, audience research and services development. Additionally, we may utilize precise geolocation data and identification through device scanning.\n\nPlease note that your consent will be valid across all our subdomains. You can change or withdraw your consent at any time by clicking the “Consent Preferences” button at the bottom of your screen. We respect your choices and are committed to providing you with a transparent and secure browsing experience.`,
      gcm_is_on: settings_obj.the_options.hasOwnProperty("is_gcm_on") && 
        (true === settings_obj.the_options["is_gcm_on"] ||
          1 === settings_obj.the_options["is_gcm_on"])
          ? true
          : false,
      gcm_wait_for_update_duration: settings_obj.the_options.hasOwnProperty(
        "gcm_wait_for_update_duration"
      )
        ? settings_obj.the_options["gcm_wait_for_update_duration"]
        : "500",
      gcm_url_passthrough: settings_obj.the_options.hasOwnProperty("is_gcm_url_passthrough") && 
        (true === settings_obj.the_options["is_gcm_url_passthrough"] ||
          1 === settings_obj.the_options["is_gcm_url_passthrough"])
          ? true
          : false,
      gcm_ads_redact: settings_obj.the_options.hasOwnProperty("is_gcm_ads_redact") && 
        (true === settings_obj.the_options["is_gcm_ads_redact"] ||
          1 === settings_obj.the_options["is_gcm_ads_redact"])
          ? true
          : false,
      gcm_debug_mode: settings_obj.the_options.hasOwnProperty("is_gcm_debug_mode") && 
        (true === settings_obj.the_options["is_gcm_debug_mode"] ||
          "true" === settings_obj.the_options["is_gcm_debug_mode"] ||
          1 === settings_obj.the_options["is_gcm_debug_mode"])
          ? true
          : false,
      gcm_advertiser_mode: settings_obj.the_options.hasOwnProperty("is_gcm_advertiser_mode") && 
        (true === settings_obj.the_options["is_gcm_advertiser_mode"] ||
          "true" === settings_obj.the_options["is_gcm_advertiser_mode"] ||
          1 === settings_obj.the_options["is_gcm_advertiser_mode"])
          ? true
          : false,
      banner_preview_is_on:
        "true" == settings_obj.the_options["banner_preview_enable"] ||
        1 === settings_obj.the_options["banner_preview_enable"]
          ? true
          : false,
      policy_options: settings_obj.policies,
      gdpr_policy: settings_obj.the_options.hasOwnProperty("cookie_usage_for")
        ? settings_obj.the_options["cookie_usage_for"]
        : "gdpr",
      is_gdpr:
        this.gdpr_policy === "gdpr" || this.gdpr_policy === "both"
          ? true
          : false,
      is_ccpa:
        this.gdpr_policy === "ccpa" || this.gdpr_policy === "both"
          ? true
          : false,
      is_lgpd: this.gdpr_policy === "lgpd" ? true : false,
      is_eprivacy: this.gdpr_policy === "eprivacy" ? true : false,
      eprivacy_message: settings_obj.the_options.hasOwnProperty(
        "notify_message_eprivacy"
      )
        ? this.stripSlashes(settings_obj.the_options["notify_message_eprivacy"])
        : "This website uses cookies to improve your experience. We'll assume you're ok with this, but you can opt-out if you wish.",
      gdpr_message_heading: settings_obj.the_options.hasOwnProperty(
        "bar_heading_text"
      )
        ? this.stripSlashes(settings_obj.the_options["bar_heading_text"])
        : "",
      lgpd_message_heading: settings_obj.the_options.hasOwnProperty(
        "bar_heading_lgpd_text"
      )
        ? this.stripSlashes(settings_obj.the_options["bar_heading_lgpd_text"])
        : "",
      gdpr_message: settings_obj.the_options.hasOwnProperty("notify_message")
        ? this.stripSlashes(settings_obj.the_options["notify_message"])
        : "This website uses cookies to improve your experience. We'll assume you're ok with this, but you can opt-out if you wish.",
      lgpd_message: settings_obj.the_options.hasOwnProperty(
        "notify_message_lgpd"
      )
        ? this.stripSlashes(settings_obj.the_options["notify_message_lgpd"])
        : "This website uses cookies for technical and other purposes as specified in the cookie policy. We'll assume you're ok with this, but you can opt-out if you wish.",
      gdpr_about_cookie_message: settings_obj.the_options.hasOwnProperty(
        "about_message"
      )
        ? this.stripSlashes(settings_obj.the_options["about_message"])
        : "Cookies are small text files that can be used by websites to make a user's experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.",
      lgpd_about_cookie_message: settings_obj.the_options.hasOwnProperty(
        "about_message_lgpd"
      )
        ? this.stripSlashes(settings_obj.the_options["about_message_lgpd"])
        : "Cookies are small text files that can be used by websites to make a user's experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.",
      ccpa_message: settings_obj.the_options.hasOwnProperty(
        "notify_message_ccpa"
      )
        ? this.stripSlashes(settings_obj.the_options["notify_message_ccpa"])
        : "In case of sale of your personal information, you may opt out by using the link",
      ccpa_optout_message: settings_obj.the_options.hasOwnProperty(
        "optout_text"
      )
        ? this.stripSlashes(settings_obj.the_options["optout_text"])
        : "Do you really wish to opt-out?",
      show_visitor_conditions:
        this.is_ccpa || (this.is_gdpr && "1" === settings_obj.is_pro_active)
          ? true
          : false,
      selectedRadioIab:
        settings_obj.the_options.hasOwnProperty("is_ccpa_iab_on") &&
        (true === settings_obj.the_options["is_ccpa_iab_on"] ||
          1 === settings_obj.the_options["is_ccpa_iab_on"])
          ? "yes"
          : "no",
      is_iab_on:
        settings_obj.the_options.hasOwnProperty("is_ccpa_iab_on") &&
        (true === settings_obj.the_options["is_ccpa_iab_on"] ||
          1 === settings_obj.the_options["is_ccpa_iab_on"])
          ? true
          : false,
      is_eu_on:
        settings_obj.the_options.hasOwnProperty("is_eu_on") &&
        (true === settings_obj.the_options["is_eu_on"] ||
          1 === settings_obj.the_options["is_eu_on"])
          ? true
          : false,
      is_ccpa_on:
        settings_obj.the_options.hasOwnProperty("is_ccpa_on") &&
        (true === settings_obj.the_options["is_ccpa_on"] ||
          1 === settings_obj.the_options["is_ccpa_on"])
          ? true
          : false,
      is_revoke_consent_on:
        settings_obj.the_options.hasOwnProperty("show_again") &&
        (true === settings_obj.the_options["show_again"] ||
          1 === settings_obj.the_options["show_again"])
          ? true
          : false,
      is_revoke_consent_on1:
      settings_obj.the_options.hasOwnProperty("show_again1") &&
      (true === settings_obj.the_options["show_again1"] ||
        1 === settings_obj.the_options["show_again1"])
        ? true
        : false,
      is_revoke_consent_on2:
      settings_obj.the_options.hasOwnProperty("show_again2") &&
      (true === settings_obj.the_options["show_again2"] ||
        1 === settings_obj.the_options["show_again2"])
        ? true
        : false,
      tab_position_options: settings_obj.tab_position_options,
      tab_position: settings_obj.the_options.hasOwnProperty(
        "show_again_position"
      )
        ? settings_obj.the_options["show_again_position"]
        : "right",
      tab_position1: settings_obj.the_options.hasOwnProperty(
        "show_again_position1"
      )
        ? settings_obj.the_options["show_again_position1"]
        : "right",
      tab_position2: settings_obj.the_options.hasOwnProperty(
        "show_again_position2"
      )
        ? settings_obj.the_options["show_again_position2"]
        : "right",
      tab_margin: settings_obj.the_options.hasOwnProperty("show_again_margin")
        ? settings_obj.the_options["show_again_margin"]
        : "5",
      tab_margin1: settings_obj.the_options.hasOwnProperty("show_again_margin1")
        ? settings_obj.the_options["show_again_margin1"]
        : "5",
      tab_margin2: settings_obj.the_options.hasOwnProperty("show_again_margin2")
        ? settings_obj.the_options["show_again_margin2"]
        : "5",
      tab_text: settings_obj.the_options.hasOwnProperty("show_again_text")
        ? settings_obj.the_options["show_again_text"]
        : "Cookie Settings",
      tab_text1: settings_obj.the_options.hasOwnProperty("show_again_text1")
        ? settings_obj.the_options["show_again_text1"]
        : "Cookie Settings",
      tab_text2: settings_obj.the_options.hasOwnProperty("show_again_text2")
        ? settings_obj.the_options["show_again_text2"]
        : "Cookie Settings",
      show_revoke_card: this.is_gdpr || this.is_eprivacy,
      autotick:
        settings_obj.the_options.hasOwnProperty("is_ticked") &&
        (true === settings_obj.the_options["is_ticked"] ||
          1 === settings_obj.the_options["is_ticked"])
          ? true
          : false,
      auto_hide:
        settings_obj.the_options.hasOwnProperty("auto_hide") &&
        (true === settings_obj.the_options["auto_hide"] ||
          1 === settings_obj.the_options["auto_hide"])
          ? true
          : false,
      auto_hide_delay: settings_obj.the_options.hasOwnProperty(
        "auto_hide_delay"
      )
        ? settings_obj.the_options["auto_hide_delay"]
        : "10000",
      auto_banner_initialize:
        settings_obj.the_options.hasOwnProperty("auto_banner_initialize") &&
        (true === settings_obj.the_options["auto_banner_initialize"] ||
          1 === settings_obj.the_options["auto_banner_initialize"])
          ? true
          : false,
      auto_banner_initialize_delay: settings_obj.the_options.hasOwnProperty(
        "auto_banner_initialize_delay"
      )
        ? settings_obj.the_options["auto_banner_initialize_delay"]
        : "10000",
      auto_scroll:
        settings_obj.the_options.hasOwnProperty("auto_scroll") &&
        (true === settings_obj.the_options["auto_scroll"] ||
          1 === settings_obj.the_options["auto_scroll"])
          ? true
          : false,
      auto_click:
        settings_obj.the_options.hasOwnProperty("auto_click") &&
        (true === settings_obj.the_options["auto_click"] ||
          1 === settings_obj.the_options["auto_click"])
          ? true
          : false,
      auto_scroll_offset: settings_obj.the_options.hasOwnProperty(
        "auto_scroll_offset"
      )
        ? settings_obj.the_options["auto_scroll_offset"]
        : "10",
      auto_scroll_reload:
        settings_obj.the_options.hasOwnProperty("auto_scroll_reload") &&
        (true === settings_obj.the_options["auto_scroll_reload"] ||
          1 === settings_obj.the_options["auto_scroll_reload"])
          ? true
          : false,
      accept_reload:
        settings_obj.the_options.hasOwnProperty("accept_reload") &&
        (true === settings_obj.the_options["accept_reload"] ||
          1 === settings_obj.the_options["accept_reload"])
          ? true
          : false,
      decline_reload:
        settings_obj.the_options.hasOwnProperty("decline_reload") &&
        (true === settings_obj.the_options["decline_reload"] ||
          1 === settings_obj.the_options["decline_reload"])
          ? true
          : false,
      delete_on_deactivation:
        settings_obj.the_options.hasOwnProperty("delete_on_deactivation") &&
        (true === settings_obj.the_options["delete_on_deactivation"] ||
          1 === settings_obj.the_options["delete_on_deactivation"])
          ? true
          : false,
      show_credits:
        settings_obj.the_options.hasOwnProperty("show_credits") &&
        (true === settings_obj.the_options["show_credits"] ||
          1 === settings_obj.the_options["show_credits"])
          ? true
          : false,
      cookie_expiry_options: settings_obj.cookie_expiry_options,
      cookie_expiry: settings_obj.the_options.hasOwnProperty("cookie_expiry")
        ? settings_obj.the_options["cookie_expiry"]
        : "365",
      logging_on:
        settings_obj.the_options.hasOwnProperty("logging_on") &&
        (true === settings_obj.the_options["logging_on"] ||
          1 === settings_obj.the_options["logging_on"])
          ? true
          : false,
      list_of_contents: settings_obj.list_of_contents,
      restrict_posts: settings_obj.the_options.hasOwnProperty("restrict_posts")
        ? settings_obj.the_options["restrict_posts"]
        : [],
      restrict_array: [],
      button_readmore_is_on:
        settings_obj.the_options.hasOwnProperty("button_readmore_is_on") &&
        (true === settings_obj.the_options["button_readmore_is_on"] ||
          1 === settings_obj.the_options["button_readmore_is_on"])
          ? true
          : false,
      button_readmore_is_on1:
      settings_obj.the_options.hasOwnProperty("button_readmore_is_on1") &&
      (true === settings_obj.the_options["button_readmore_is_on1"] ||
        1 === settings_obj.the_options["button_readmore_is_on1"])
        ? true
        : false,
      button_readmore_is_on2:
      settings_obj.the_options.hasOwnProperty("button_readmore_is_on2") &&
      (true === settings_obj.the_options["button_readmore_is_on2"] ||
        1 === settings_obj.the_options["button_readmore_is_on2"])
        ? true
        : false,
      button_readmore_text: settings_obj.the_options.hasOwnProperty(
        "button_readmore_text"
      )
        ? settings_obj.the_options["button_readmore_text"]
        : "Read More",
      button_readmore_link_color: settings_obj.the_options.hasOwnProperty(
        "button_readmore_link_color"
      )
        ? settings_obj.the_options["button_readmore_link_color"]
        : "#359bf5",
      button_readmore_text1: settings_obj.the_options.hasOwnProperty(
        "button_readmore_text1"
      )
        ? settings_obj.the_options["button_readmore_text1"]
        : "Read More",
      button_readmore_link_color1: settings_obj.the_options.hasOwnProperty(
        "button_readmore_link_color1"
      )
        ? settings_obj.the_options["button_readmore_link_color1"]
        : "#359bf5",  
      button_readmore_text2: settings_obj.the_options.hasOwnProperty(
        "button_readmore_text2"
      )
        ? settings_obj.the_options["button_readmore_text2"]
        : "Read More",
      button_readmore_link_color2: settings_obj.the_options.hasOwnProperty(
        "button_readmore_link_color2"
      )
        ? settings_obj.the_options["button_readmore_link_color2"]
        : "#359bf5",
      show_as_options: settings_obj.show_as_options,
      button_readmore_as_button:
        settings_obj.the_options.hasOwnProperty("button_readmore_as_button") &&
        (true === settings_obj.the_options["button_readmore_as_button"] ||
          1 === settings_obj.the_options["button_readmore_as_button"])
          ? true
          : false,
      button_readmore_as_button1:
      settings_obj.the_options.hasOwnProperty("button_readmore_as_button1") &&
      (true === settings_obj.the_options["button_readmore_as_button1"] ||
        1 === settings_obj.the_options["button_readmore_as_button1"])
        ? true
        : false,  
      button_readmore_as_button2:
      settings_obj.the_options.hasOwnProperty("button_readmore_as_button2") &&
      (true === settings_obj.the_options["button_readmore_as_button2"] ||
        1 === settings_obj.the_options["button_readmore_as_button2"])
        ? true
        : false,
      url_type_options: settings_obj.url_type_options,
      button_readmore_url_type:
        settings_obj.the_options.hasOwnProperty("button_readmore_url_type") &&
        (false === settings_obj.the_options["button_readmore_url_type"] ||
          0 === settings_obj.the_options["button_readmore_url_type"])
          ? false
          : true,
      button_readmore_url_type1:
        settings_obj.the_options.hasOwnProperty("button_readmore_url_type1") &&
        (false === settings_obj.the_options["button_readmore_url_type1"] ||
          0 === settings_obj.the_options["button_readmore_url_type1"])
          ? false
          : true,
      button_readmore_url_type2:
        settings_obj.the_options.hasOwnProperty("button_readmore_url_type2") &&
        (false === settings_obj.the_options["button_readmore_url_type2"] ||
          0 === settings_obj.the_options["button_readmore_url_type2"])
          ? false
          : true,
      privacy_policy_options: settings_obj.privacy_policy_options,
      button_readmore_page: settings_obj.the_options.hasOwnProperty(
        "button_readmore_page"
      )
        ? settings_obj.the_options["button_readmore_page"]
        : "0",
      readmore_page: "",
      button_readmore_wp_page:
        settings_obj.the_options.hasOwnProperty("button_readmore_wp_page") &&
        (true === settings_obj.the_options["button_readmore_wp_page"] ||
          1 === settings_obj.the_options["button_readmore_wp_page"])
          ? true
          : false,
      button_readmore_new_win:
        settings_obj.the_options.hasOwnProperty("button_readmore_new_win") &&
        (true === settings_obj.the_options["button_readmore_new_win"] ||
          1 === settings_obj.the_options["button_readmore_new_win"])
          ? true
          : false,
      button_readmore_url: settings_obj.the_options.hasOwnProperty(
        "button_readmore_url"
      )
        ? settings_obj.the_options["button_readmore_url"]
        : "#",
      button_readmore_button_color: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_color"
      )
        ? settings_obj.the_options["button_readmore_button_color"]
        : "#000000",
      button_readmore_button_opacity: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_opacity"
      )
        ? settings_obj.the_options["button_readmore_button_opacity"]
        : "1",
      button_readmore_button_border_style:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_style"
        )
          ? settings_obj.the_options["button_readmore_button_border_style"]
          : "none",
      button_readmore_button_border_width:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_width"
        )
          ? settings_obj.the_options["button_readmore_button_border_width"]
          : "0",
      button_readmore_button_border_color:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_color"
        )
          ? settings_obj.the_options["button_readmore_button_border_color"]
          : "#000000",
      button_readmore_button_border_radius:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_radius"
        )
          ? settings_obj.the_options["button_readmore_button_border_radius"]
          : "0",
      button_readmore_button_size: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_size"
      )
        ? settings_obj.the_options["button_readmore_button_size"]
        : "medium",
      button_readmore_page1: settings_obj.the_options.hasOwnProperty(
        "button_readmore_page1"
      )
        ? settings_obj.the_options["button_readmore_page1"]
        : "0",
      readmore_page1: "",
      button_readmore_wp_page1:
        settings_obj.the_options.hasOwnProperty("button_readmore_wp_page1") &&
        (true === settings_obj.the_options["button_readmore_wp_page1"] ||
          1 === settings_obj.the_options["button_readmore_wp_page1"])
          ? true
          : false,
      button_readmore_new_win1:
        settings_obj.the_options.hasOwnProperty("button_readmore_new_win1") &&
        (true === settings_obj.the_options["button_readmore_new_win1"] ||
          1 === settings_obj.the_options["button_readmore_new_win1"])
          ? true
          : false,
      button_readmore_url1: settings_obj.the_options.hasOwnProperty(
        "button_readmore_url1"
      )
        ? settings_obj.the_options["button_readmore_url1"]
        : "#",
      button_readmore_button_color1: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_color1"
      )
        ? settings_obj.the_options["button_readmore_button_color1"]
        : "#000000",
      button_readmore_button_opacity1: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_opacity1"
      )
        ? settings_obj.the_options["button_readmore_button_opacity1"]
        : "1",
      button_readmore_button_border_style1:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_style1"
        )
          ? settings_obj.the_options["button_readmore_button_border_style1"]
          : "none",
      button_readmore_button_border_width1:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_width1"
        )
          ? settings_obj.the_options["button_readmore_button_border_width1"]
          : "0",
      button_readmore_button_border_color1:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_color1"
        )
          ? settings_obj.the_options["button_readmore_button_border_color1"]
          : "#000000",
      button_readmore_button_border_radius1:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_radius1"
        )
          ? settings_obj.the_options["button_readmore_button_border_radius1"]
          : "0",
      button_readmore_button_size1: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_size1"
      )
        ? settings_obj.the_options["button_readmore_button_size1"]
        : "medium",  
      button_readmore_page2: settings_obj.the_options.hasOwnProperty(
        "button_readmore_page2"
      )
        ? settings_obj.the_options["button_readmore_page2"]
        : "0",
      readmore_page2: "",
      button_readmore_wp_page2:
        settings_obj.the_options.hasOwnProperty("button_readmore_wp_page2") &&
        (true === settings_obj.the_options["button_readmore_wp_page2"] ||
          1 === settings_obj.the_options["button_readmore_wp_page2"])
          ? true
          : false,
      button_readmore_new_win2:
        settings_obj.the_options.hasOwnProperty("button_readmore_new_win2") &&
        (true === settings_obj.the_options["button_readmore_new_win2"] ||
          1 === settings_obj.the_options["button_readmore_new_win2"])
          ? true
          : false,
      button_readmore_url2: settings_obj.the_options.hasOwnProperty(
        "button_readmore_url2"
      )
        ? settings_obj.the_options["button_readmore_url2"]
        : "#",
      button_readmore_button_color2: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_color2"
      )
        ? settings_obj.the_options["button_readmore_button_color2"]
        : "#000000",
      button_readmore_button_opacity2: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_opacity2"
      )
        ? settings_obj.the_options["button_readmore_button_opacity2"]
        : "1",
      button_readmore_button_border_style2:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_style2"
        )
          ? settings_obj.the_options["button_readmore_button_border_style2"]
          : "none",
      button_readmore_button_border_width2:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_width2"
        )
          ? settings_obj.the_options["button_readmore_button_border_width2"]
          : "0",
      button_readmore_button_border_color2:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_color2"
        )
          ? settings_obj.the_options["button_readmore_button_border_color2"]
          : "#000000",
      button_readmore_button_border_radius2:
        settings_obj.the_options.hasOwnProperty(
          "button_readmore_button_border_radius2"
        )
          ? settings_obj.the_options["button_readmore_button_border_radius2"]
          : "0",
      button_readmore_button_size2: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_size2"
      )
        ? settings_obj.the_options["button_readmore_button_size2"]
        : "medium",    
      button_size_options: settings_obj.button_size_options,
      button_readmore_button_size: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_size"
      )
        ? settings_obj.the_options["button_readmore_button_size"]
        : "medium",
      button_readmore_button_size1: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_size1"
      )
        ? settings_obj.the_options["button_readmore_button_size1"]
        : "medium",
      button_readmore_button_size2: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_size2"
      )
        ? settings_obj.the_options["button_readmore_button_size2"]
        : "medium",      
      banner_preview: true,
      show_cookie_as_options: settings_obj.show_cookie_as_options,
      show_language_as_options: settings_obj.show_language_as_options,
      schedule_scan_options: settings_obj.schedule_scan_options,
      schedule_scan_as: settings_obj.the_options.hasOwnProperty(
        "schedule_scan_type"
      )
        ? settings_obj.the_options["schedule_scan_type"]
        : "never", //schedule scan type
      schedule_scan_day_options: settings_obj.schedule_scan_day_options,
      schedule_scan_day: settings_obj.the_options.hasOwnProperty("scan_day")
        ? settings_obj.the_options["scan_day"]
        : "Day 1", //scan day
      schedule_scan_time_value: settings_obj.the_options.hasOwnProperty(
        "scan_time"
      )
        ? settings_obj.the_options["scan_time"]
        : "8:00 PM", //scan time
      schedule_scan_date: settings_obj.the_options.hasOwnProperty("scan_date")
        ? settings_obj.the_options["scan_date"]
        : new Date(), //scan date
      next_scan_is_when: settings_obj.the_options.hasOwnProperty(
        "schedule_scan_when"
      )
        ? settings_obj.the_options["schedule_scan_when"]
        : "Not Scheduled", //next scan when
      show_language_as: settings_obj.the_options.hasOwnProperty("lang_selected")
        ? settings_obj.the_options["lang_selected"]
        : "en",
      show_cookie_as: settings_obj.the_options.hasOwnProperty("cookie_bar_as")
        ? settings_obj.the_options["cookie_bar_as"]
        : "banner",
      cookie_position_options: settings_obj.position_options,
      cookie_position: settings_obj.the_options.hasOwnProperty(
        "notify_position_vertical"
      )
        ? settings_obj.the_options["notify_position_vertical"]
        : "bottom",
      cookie_widget_position_options: settings_obj.widget_position_options,
      cookie_widget_position: settings_obj.the_options.hasOwnProperty(
        "notify_position_horizontal"
      )
        ? settings_obj.the_options["notify_position_horizontal"]
        : "left",
      cookie_add_overlay:
        settings_obj.the_options.hasOwnProperty("popup_overlay") &&
        (true === settings_obj.the_options["popup_overlay"] ||
          1 === settings_obj.the_options["popup_overlay"])
          ? true
          : false,
      on_hide_options: settings_obj.on_hide_options,
      on_load_options: settings_obj.on_load_options,
      cookie_bar_color: settings_obj.the_options.hasOwnProperty("background")
        ? settings_obj.the_options["background"]
        : "#ffffff",
      on_hide:
        settings_obj.the_options.hasOwnProperty("notify_animate_hide") &&
        (true === settings_obj.the_options["notify_animate_hide"] ||
          1 === settings_obj.the_options["notify_animate_hide"])
          ? true
          : false,
      on_load:
        settings_obj.the_options.hasOwnProperty("notify_animate_show") &&
        (true === settings_obj.the_options["notify_animate_show"] ||
          1 === settings_obj.the_options["notify_animate_show"])
          ? true
          : false,
      cookie_text_color: settings_obj.the_options.hasOwnProperty("text")
        ? settings_obj.the_options["text"]
        : "#000000",
      cookie_bar_opacity: settings_obj.the_options.hasOwnProperty("opacity")
        ? settings_obj.the_options["opacity"]
        : "0.80",
      cookie_bar_border_width: settings_obj.the_options.hasOwnProperty(
        "background_border_width"
      )
        ? settings_obj.the_options["background_border_width"]
        : "0",
      border_style_options: settings_obj.border_style_options,
      border_style: settings_obj.the_options.hasOwnProperty(
        "background_border_style"
      )
        ? settings_obj.the_options["background_border_style"]
        : "none",
      cookie_border_color: settings_obj.the_options.hasOwnProperty(
        "background_border_color"
      )
        ? settings_obj.the_options["background_border_color"]
        : "#ffffff",
      cookie_bar_border_radius: settings_obj.the_options.hasOwnProperty(
        "background_border_radius"
      )
        ? settings_obj.the_options["background_border_radius"]
        : "0",
      font_options: settings_obj.font_options,
      cookie_font: settings_obj.the_options.hasOwnProperty("font_family")
        ? settings_obj.the_options["font_family"]
        : "inherit",
      cookie_accept_on:
        settings_obj.the_options.hasOwnProperty("button_accept_is_on") &&
        (true === settings_obj.the_options["button_accept_is_on"] ||
          1 === settings_obj.the_options["button_accept_is_on"])
          ? true
          : false,
      accept_text: settings_obj.the_options.hasOwnProperty("button_accept_text")
        ? settings_obj.the_options["button_accept_text"]
        : "Accept",
      accept_text_color: settings_obj.the_options.hasOwnProperty(
        "button_accept_link_color"
      )
        ? settings_obj.the_options["button_accept_link_color"]
        : "#ffffff",
      accept_action_options: settings_obj.accept_action_options,
      accept_action: settings_obj.the_options.hasOwnProperty(
        "button_accept_action"
      )
        ? settings_obj.the_options["button_accept_action"]
        : "#cookie_action_close_header",
      accept_url: settings_obj.the_options.hasOwnProperty("button_accept_url")
        ? settings_obj.the_options["button_accept_url"]
        : "#",
      is_open_url:
        this.accept_action === "#cookie_action_close_header" ? false : true,
      accept_as_button_options: settings_obj.accept_button_as_options,
      accept_as_button:
        settings_obj.the_options.hasOwnProperty("button_accept_as_button") &&
        (true === settings_obj.the_options["button_accept_as_button"] ||
          1 === settings_obj.the_options["button_accept_as_button"])
          ? true
          : false,
      open_url_options: settings_obj.open_url_options,
      open_url:
        settings_obj.the_options.hasOwnProperty("button_accept_new_win") &&
        (true === settings_obj.the_options["button_accept_new_win"] ||
          1 === settings_obj.the_options["button_accept_new_win"])
          ? true
          : false,
      accept_background_color: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_color"
      )
        ? settings_obj.the_options["button_accept_button_color"]
        : "#18a300",
      accept_opacity: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_opacity"
      )
        ? settings_obj.the_options["button_accept_button_opacity"]
        : "1",
      accept_style: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_style"
      )
        ? settings_obj.the_options["button_accept_button_border_style"]
        : "none",
      accept_border_color: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_color"
      )
        ? settings_obj.the_options["button_accept_button_border_color"]
        : "#18a300",
      accept_border_width: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_width"
      )
        ? settings_obj.the_options["button_accept_button_border_width"]
        : "0",
      accept_border_radius: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_radius"
      )
        ? settings_obj.the_options["button_accept_button_border_radius"]
        : "0",
      cookie_decline_on:
        settings_obj.the_options.hasOwnProperty("button_decline_is_on") &&
        (true === settings_obj.the_options["button_decline_is_on"] ||
          1 === settings_obj.the_options["button_decline_is_on"])
          ? true
          : false,
      decline_text: settings_obj.the_options.hasOwnProperty(
        "button_decline_text"
      )
        ? settings_obj.the_options["button_decline_text"]
        : "Decline",
      decline_text_color: settings_obj.the_options.hasOwnProperty(
        "button_decline_link_color"
      )
        ? settings_obj.the_options["button_decline_link_color"]
        : "#ffffff",
      decline_as_button:
        settings_obj.the_options.hasOwnProperty("button_decline_as_button") &&
        (true === settings_obj.the_options["button_decline_as_button"] ||
          1 === settings_obj.the_options["button_decline_as_button"])
          ? true
          : false,
      decline_background_color: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_color"
      )
        ? settings_obj.the_options["button_decline_button_color"]
        : "#333333",
      decline_opacity: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_opacity"
      )
        ? settings_obj.the_options["button_decline_button_opacity"]
        : "1",
      decline_style: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_style"
      )
        ? settings_obj.the_options["button_decline_button_border_style"]
        : "none",
      decline_border_color: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_color"
      )
        ? settings_obj.the_options["button_decline_button_border_color"]
        : "#333333",
      decline_border_width: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_width"
      )
        ? settings_obj.the_options["button_decline_button_border_width"]
        : "0",
      decline_border_radius: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_radius"
      )
        ? settings_obj.the_options["button_decline_button_border_radius"]
        : "0",
      decline_action: settings_obj.the_options.hasOwnProperty(
        "button_decline_action"
      )
        ? settings_obj.the_options["button_decline_action"]
        : "#cookie_action_close_header_reject",
      decline_action_options: settings_obj.decline_action_options,
      decline_open_url:
        this.decline_action === "#cookie_action_close_header_reject"
          ? false
          : true,
      decline_url: settings_obj.the_options.hasOwnProperty("button_decline_url")
        ? settings_obj.the_options["button_decline_url"]
        : "#",
      open_decline_url:
        settings_obj.the_options.hasOwnProperty("button_decline_new_win") &&
        (true === settings_obj.the_options["button_decline_new_win"] ||
          1 === settings_obj.the_options["button_decline_new_win"])
          ? true
          : false,
      cookie_settings_on:
        settings_obj.the_options.hasOwnProperty("button_settings_is_on") &&
        (true === settings_obj.the_options["button_settings_is_on"] ||
          1 === settings_obj.the_options["button_settings_is_on"])
          ? true
          : false,
      is_banner: this.show_cookie_as === "banner" ? true : false,
      
      settings_text: settings_obj.the_options.hasOwnProperty(
        "button_settings_text"
      )
        ? settings_obj.the_options["button_settings_text"]
        : "Cookie Settings",
      settings_text_color: settings_obj.the_options.hasOwnProperty(
        "button_settings_link_color"
      )
        ? settings_obj.the_options["button_settings_link_color"]
        : "#ffffff",
      settings_as_button:
        settings_obj.the_options.hasOwnProperty("button_settings_as_button") &&
        (true === settings_obj.the_options["button_settings_as_button"] ||
          1 === settings_obj.the_options["button_settings_as_button"])
          ? true
          : false,
      settings_background_color: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_color"
      )
        ? settings_obj.the_options["button_settings_button_color"]
        : "#333333",
      settings_opacity: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_opacity"
      )
        ? settings_obj.the_options["button_settings_button_opacity"]
        : "1",
      settings_style: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_style"
      )
        ? settings_obj.the_options["button_settings_button_border_style"]
        : "none",
      settings_border_color: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_color"
      )
        ? settings_obj.the_options["button_settings_button_border_color"]
        : "#333333",
      settings_border_width: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_width"
      )
        ? settings_obj.the_options["button_settings_button_border_width"]
        : "0",
      settings_border_radius: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_radius"
      )
        ? settings_obj.the_options["button_settings_button_border_radius"]
        : "0",
      cookie_on_frontend:
        settings_obj.the_options.hasOwnProperty(
          "button_settings_display_cookies"
        ) &&
        (true === settings_obj.the_options["button_settings_display_cookies"] ||
          1 === settings_obj.the_options["button_settings_display_cookies"])
          ? true
          : false,
      confirm_text: settings_obj.the_options.hasOwnProperty(
        "button_confirm_text"
      )
        ? settings_obj.the_options["button_confirm_text"]
        : "Confirm",
      confirm_text_color: settings_obj.the_options.hasOwnProperty(
        "button_confirm_link_color"
      )
        ? settings_obj.the_options["button_confirm_link_color"]
        : "#ffffff",
      confirm_background_color: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_color"
      )
        ? settings_obj.the_options["button_confirm_button_color"]
        : "#18a300",
      confirm_opacity: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_opacity"
      )
        ? settings_obj.the_options["button_confirm_button_opacity"]
        : "1",
      confirm_style: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_style"
      )
        ? settings_obj.the_options["button_confirm_button_border_style"]
        : "none",
      confirm_border_color: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_color"
      )
        ? settings_obj.the_options["button_confirm_button_border_color"]
        : "#18a300",
      confirm_border_width: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_width"
      )
        ? settings_obj.the_options["button_confirm_button_border_width"]
        : "0",
      confirm_border_radius: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_radius"
      )
        ? settings_obj.the_options["button_confirm_button_border_radius"]
        : "0",
      cancel_text: settings_obj.the_options.hasOwnProperty("button_cancel_text")
        ? settings_obj.the_options["button_cancel_text"]
        : "Cancel",
      cancel_text_color: settings_obj.the_options.hasOwnProperty(
        "button_cancel_link_color"
      )
        ? settings_obj.the_options["button_cancel_link_color"]
        : "#ffffff",
      cancel_background_color: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_color"
      )
        ? settings_obj.the_options["button_cancel_button_color"]
        : "#333333",
      cancel_opacity: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_opacity"
      )
        ? settings_obj.the_options["button_cancel_button_opacity"]
        : "1",
      cancel_style: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_style"
      )
        ? settings_obj.the_options["button_cancel_button_border_style"]
        : "none",
      cancel_border_color: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_color"
      )
        ? settings_obj.the_options["button_cancel_button_border_color"]
        : "#333333",
      cancel_border_width: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_width"
      )
        ? settings_obj.the_options["button_cancel_button_border_width"]
        : "0",
      cancel_border_radius: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_radius"
      )
        ? settings_obj.the_options["button_cancel_button_border_radius"]
        : "0",
      opt_out_text: settings_obj.the_options.hasOwnProperty(
        "button_donotsell_text"
      )
        ? settings_obj.the_options["button_donotsell_text"]
        : "Do Not Sell My Personal Information",
      opt_out_text_color: settings_obj.the_options.hasOwnProperty(
        "button_donotsell_link_color"
      )
        ? settings_obj.the_options["button_donotsell_link_color"]
        : "#359bf5",

      cookie_bar_color1: settings_obj.the_options.hasOwnProperty(
        "cookie_bar_color1"
      )
        ? settings_obj.the_options["cookie_bar_color1"]
        : "#ffffff",
      cookie_text_color1: settings_obj.the_options.hasOwnProperty(
        "cookie_text_color1"
      )
        ? settings_obj.the_options["cookie_text_color1"]
        : "#000000",
      cookie_bar_opacity1: settings_obj.the_options.hasOwnProperty(
        "cookie_bar_opacity1"
      )
        ? settings_obj.the_options["cookie_bar_opacity1"]
        : "0.80",
      cookie_bar_border_width1: settings_obj.the_options.hasOwnProperty(
        "cookie_bar_border_width1"
      )
        ? settings_obj.the_options["cookie_bar_border_width1"]
        : "0",
      border_style1: settings_obj.the_options.hasOwnProperty("border_style1")
        ? settings_obj.the_options["border_style1"]
        : "none",
      cookie_border_color1: settings_obj.the_options.hasOwnProperty(
        "cookie_border_color1"
      )
        ? settings_obj.the_options["cookie_border_color1"]
        : "#ffffff",
      cookie_bar_border_radius1: settings_obj.the_options.hasOwnProperty(
        "cookie_bar_border_radius1"
      )
        ? settings_obj.the_options["cookie_bar_border_radius1"]
        : "0",
      cookie_font1: settings_obj.the_options.hasOwnProperty("cookie_font1")
        ? settings_obj.the_options["cookie_font1"]
        : "inherit",
      cookie_accept_on1:
        settings_obj.the_options.hasOwnProperty("button_accept_is_on1") &&
        (false === settings_obj.the_options["button_accept_is_on1"] ||
          0 === settings_obj.the_options["button_accept_is_on1"] ||
          "false" === settings_obj.the_options["button_accept_is_on1"])
          ? false
          : true,
      accept_text1: settings_obj.the_options.hasOwnProperty(
        "button_accept_text1"
      )
        ? settings_obj.the_options["button_accept_text1"]
        : "Accept",
      accept_text_color1: settings_obj.the_options.hasOwnProperty(
        "button_accept_link_color1"
      )
        ? settings_obj.the_options["button_accept_link_color1"]
        : "#ffffff",
      accept_action1: settings_obj.the_options.hasOwnProperty(
        "button_accept_action1"
      )
        ? settings_obj.the_options["button_accept_action1"]
        : "#cookie_action_close_header",
      accept_url1: settings_obj.the_options.hasOwnProperty("button_accept_url1")
        ? settings_obj.the_options["button_accept_url1"]
        : "#",
      accept_as_button1:
        settings_obj.the_options.hasOwnProperty("button_accept_as_button1") &&
        (false === settings_obj.the_options["button_accept_as_button1"] ||
          0 === settings_obj.the_options["button_accept_as_button1"] ||
          "false" === settings_obj.the_options["button_accept_as_button1"])
          ? false
          : true,
      open_url1:
        settings_obj.the_options.hasOwnProperty("button_accept_new_win1") &&
        (true === settings_obj.the_options["button_accept_new_win1"] ||
          1 === settings_obj.the_options["button_accept_new_win1"] ||
          "true" === settings_obj.the_options["button_accept_new_win1"])
          ? true
          : false,
      accept_background_color1: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_color1"
      )
        ? settings_obj.the_options["button_accept_button_color1"]
        : "#18a300",
      accept_opacity1: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_opacity1"
      )
        ? settings_obj.the_options["button_accept_button_opacity1"]
        : "1",
      accept_style1: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_style1"
      )
        ? settings_obj.the_options["button_accept_button_border_style1"]
        : "none",
      accept_border_color1: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_color1"
      )
        ? settings_obj.the_options["button_accept_button_border_color1"]
        : "#18a300",
      accept_border_width1: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_width1"
      )
        ? settings_obj.the_options["button_accept_button_border_width1"]
        : "0",
      accept_border_radius1: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_radius1"
      )
        ? settings_obj.the_options["button_accept_button_border_radius1"]
        : "0",
      cookie_accept_all_on1:
        settings_obj.the_options.hasOwnProperty("button_accept_all_is_on1") &&
        (true === settings_obj.the_options["button_accept_all_is_on1"] ||
          1 === settings_obj.the_options["button_accept_all_is_on1"] ||
          "true" === settings_obj.the_options["button_accept_all_is_on1"])
          ? true
          : false,
      accept_all_text1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_text1"
      )
        ? settings_obj.the_options["button_accept_all_text1"]
        : "Accept All",
      accept_all_text_color1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_link_color1"
      )
        ? settings_obj.the_options["button_accept_all_link_color1"]
        : "#ffffff",
      accept_all_as_button1:
        settings_obj.the_options.hasOwnProperty(
          "button_accept_all_as_button1"
        ) &&
        (false === settings_obj.the_options["button_accept_all_as_button1"] ||
          0 === settings_obj.the_options["button_accept_all_as_button1"] ||
          "false" === settings_obj.the_options["button_accept_all_as_button1"])
          ? false
          : true,
      accept_all_action1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_action1"
      )
        ? settings_obj.the_options["button_accept_all_action1"]
        : "#cookie_action_close_header",
      accept_all_url1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_url1"
      )
        ? settings_obj.the_options["button_accept_all_url1"]
        : "#",
      accept_all_new_win1:
        settings_obj.the_options.hasOwnProperty("button_accept_all_new_win1") &&
        (true === settings_obj.the_options["button_accept_all_new_win1"] ||
          1 === settings_obj.the_options["button_accept_all_new_win1"] ||
          "true" === settings_obj.the_options["button_accept_all_new_win1"])
          ? true
          : false,
      accept_all_background_color1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_button_color1"
      )
        ? settings_obj.the_options["button_accept_all_button_color1"]
        : "#18a300",
      accept_all_style1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_style1"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_style1"]
        : "none",
      accept_all_border_color1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_color1"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_color1"]
        : "#18a300",
      accept_all_opacity1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_opacity1"
      )
        ? settings_obj.the_options["button_accept_all_btn_opacity1"]
        : "1",
      accept_all_border_width1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_width1"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_width1"]
        : "0",
      accept_all_border_radius1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_radius1"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_radius1"]
        : "0",

      cookie_decline_on1:
        settings_obj.the_options.hasOwnProperty("button_decline_is_on1") &&
        (false === settings_obj.the_options["button_decline_is_on1"] ||
          0 === settings_obj.the_options["button_decline_is_on1"] ||
          "false" === settings_obj.the_options["button_decline_is_on1"])
          ? false
          : true,
      decline_text1: settings_obj.the_options.hasOwnProperty(
        "button_decline_text1"
      )
        ? settings_obj.the_options["button_decline_text1"]
        : "Decline",
      decline_text_color1: settings_obj.the_options.hasOwnProperty(
        "button_decline_link_color1"
      )
        ? settings_obj.the_options["button_decline_link_color1"]
        : "#ffffff",
      decline_as_button1:
        settings_obj.the_options.hasOwnProperty("button_decline_as_button1") &&
        (false === settings_obj.the_options["button_decline_as_button1"] ||
          0 === settings_obj.the_options["button_decline_as_button1"] ||
          "false" === settings_obj.the_options["button_decline_as_button1"])
          ? false
          : true,
      decline_background_color1: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_color1"
      )
        ? settings_obj.the_options["button_decline_button_color1"]
        : "#333333",
      decline_opacity1: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_opacity1"
      )
        ? settings_obj.the_options["button_decline_button_opacity1"]
        : "1",
      decline_style1: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_style1"
      )
        ? settings_obj.the_options["button_decline_button_border_style1"]
        : "none",
      decline_border_color1: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_color1"
      )
        ? settings_obj.the_options["button_decline_button_border_color1"]
        : "#333333",
      decline_border_width1: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_width1"
      )
        ? settings_obj.the_options["button_decline_button_border_width1"]
        : "0",
      decline_border_radius1: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_radius1"
      )
        ? settings_obj.the_options["button_decline_button_border_radius1"]
        : "0",
      decline_action1: settings_obj.the_options.hasOwnProperty(
        "button_decline_action1"
      )
        ? settings_obj.the_options["button_decline_action1"]
        : "#cookie_action_close_header_reject",

      decline_url1: settings_obj.the_options.hasOwnProperty(
        "button_decline_url1"
      )
        ? settings_obj.the_options["button_decline_url1"]
        : "#",
      open_decline_url1:
        settings_obj.the_options.hasOwnProperty("button_decline_new_win1") &&
        (true === settings_obj.the_options["button_decline_new_win1"] ||
          1 === settings_obj.the_options["button_decline_new_win1"] ||
          "true" === settings_obj.the_options["button_decline_new_win1"])
          ? true
          : false,

      cookie_settings_on1:
        settings_obj.the_options.hasOwnProperty("button_settings_is_on1") &&
        (false === settings_obj.the_options["button_settings_is_on1"] ||
          0 === settings_obj.the_options["button_settings_is_on1"] ||
          "false" === settings_obj.the_options["button_settings_is_on1"])
          ? false
          : true,

      settings_text1: settings_obj.the_options.hasOwnProperty(
        "button_settings_text1"
      )
        ? settings_obj.the_options["button_settings_text1"]
        : "Cookie Settings",
      settings_text_color1: settings_obj.the_options.hasOwnProperty(
        "button_settings_link_color1"
      )
        ? settings_obj.the_options["button_settings_link_color1"]
        : "#ffffff",
      settings_as_button1:
        settings_obj.the_options.hasOwnProperty("button_settings_as_button1") &&
        (false === settings_obj.the_options["button_settings_as_button1"] ||
          0 === settings_obj.the_options["button_settings_as_button1"] ||
          "false" === settings_obj.the_options["button_settings_as_button1"])
          ? false
          : true,
      settings_background_color1: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_color1"
      )
        ? settings_obj.the_options["button_settings_button_color1"]
        : "#333333",
      settings_opacity1: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_opacity1"
      )
        ? settings_obj.the_options["button_settings_button_opacity1"]
        : "1",
      settings_style1: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_style1"
      )
        ? settings_obj.the_options["button_settings_button_border_style1"]
        : "none",
      settings_border_color1: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_color1"
      )
        ? settings_obj.the_options["button_settings_button_border_color1"]
        : "#333333",
      settings_border_width1: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_width1"
      )
        ? settings_obj.the_options["button_settings_button_border_width1"]
        : "0",
      settings_border_radius1: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_radius1"
      )
        ? settings_obj.the_options["button_settings_button_border_radius1"]
        : "0",
      confirm_text1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_text1"
      )
        ? settings_obj.the_options["button_confirm_text1"]
        : "Confirm",
      confirm_text_color1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_link_color1"
      )
        ? settings_obj.the_options["button_confirm_link_color1"]
        : "#ffffff",
      confirm_background_color1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_color1"
      )
        ? settings_obj.the_options["button_confirm_button_color1"]
        : "#18a300",
      confirm_opacity1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_opacity1"
      )
        ? settings_obj.the_options["button_confirm_button_opacity1"]
        : "1",
      confirm_style1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_style1"
      )
        ? settings_obj.the_options["button_confirm_button_border_style1"]
        : "none",
      confirm_border_color1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_color1"
      )
        ? settings_obj.the_options["button_confirm_button_border_color1"]
        : "#18a300",
      confirm_border_width1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_width1"
      )
        ? settings_obj.the_options["button_confirm_button_border_width1"]
        : "0",
      confirm_border_radius1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_radius1"
      )
        ? settings_obj.the_options["button_confirm_button_border_radius1"]
        : "0",
      cancel_text1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_text1"
      )
        ? settings_obj.the_options["button_cancel_text1"]
        : "Cancel",
      cancel_text_color1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_link_color1"
      )
        ? settings_obj.the_options["button_cancel_link_color1"]
        : "#ffffff",
      cancel_background_color1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_color1"
      )
        ? settings_obj.the_options["button_cancel_button_color1"]
        : "#333333",
      cancel_opacity1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_opacity1"
      )
        ? settings_obj.the_options["button_cancel_button_opacity1"]
        : "1",
      cancel_style1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_style1"
      )
        ? settings_obj.the_options["button_cancel_button_border_style1"]
        : "none",
      cancel_border_color1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_color1"
      )
        ? settings_obj.the_options["button_cancel_button_border_color1"]
        : "#333333",
      cancel_border_width1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_width1"
      )
        ? settings_obj.the_options["button_cancel_button_border_width1"]
        : "0",
      cancel_border_radius1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_radius1"
      )
        ? settings_obj.the_options["button_cancel_button_border_radius1"]
        : "0",
      opt_out_text1: settings_obj.the_options.hasOwnProperty(
        "button_donotsell_text1"
      )
        ? settings_obj.the_options["button_donotsell_text1"]
        : "Do Not Sell My Personal Information",
      opt_out_text_color1: settings_obj.the_options.hasOwnProperty(
        "button_donotsell_link_color1"
      )
        ? settings_obj.the_options["button_donotsell_link_color1"]
        : "#359bf5",
      cookie_bar2_name: settings_obj.the_options.hasOwnProperty(
        "cookie_bar2_name"
      )
        ? settings_obj.the_options["cookie_bar2_name"]
        : "Test Banner B",

      cookie_bar_color2: settings_obj.the_options.hasOwnProperty(
        "cookie_bar_color2"
      )
        ? settings_obj.the_options["cookie_bar_color2"]
        : "#ffffff",
      cookie_text_color2: settings_obj.the_options.hasOwnProperty(
        "cookie_text_color2"
      )
        ? settings_obj.the_options["cookie_text_color2"]
        : "#000000",
      cookie_bar_opacity2: settings_obj.the_options.hasOwnProperty(
        "cookie_bar_opacity2"
      )
        ? settings_obj.the_options["cookie_bar_opacity2"]
        : "0.80",
      cookie_bar_border_width2: settings_obj.the_options.hasOwnProperty(
        "cookie_bar_border_width2"
      )
        ? settings_obj.the_options["cookie_bar_border_width2"]
        : "0",
      border_style2: settings_obj.the_options.hasOwnProperty("border_style2")
        ? settings_obj.the_options["border_style2"]
        : "none",
      cookie_border_color2: settings_obj.the_options.hasOwnProperty(
        "cookie_border_color2"
      )
        ? settings_obj.the_options["cookie_border_color2"]
        : "#ffffff",
      cookie_bar_border_radius2: settings_obj.the_options.hasOwnProperty(
        "cookie_bar_border_radius2"
      )
        ? settings_obj.the_options["cookie_bar_border_radius2"]
        : "0",
      cookie_font2: settings_obj.the_options.hasOwnProperty("cookie_font2")
        ? settings_obj.the_options["cookie_font2"]
        : "inherit",
      cookie_accept_on2:
        settings_obj.the_options.hasOwnProperty("button_accept_is_on2") &&
        (false === settings_obj.the_options["button_accept_is_on2"] ||
          0 === settings_obj.the_options["button_accept_is_on2"] ||
          "false" === settings_obj.the_options["button_accept_is_on2"])
          ? false
          : true,
      accept_text2: settings_obj.the_options.hasOwnProperty(
        "button_accept_text2"
      )
        ? settings_obj.the_options["button_accept_text2"]
        : "Accept",
      accept_text_color2: settings_obj.the_options.hasOwnProperty(
        "button_accept_link_color2"
      )
        ? settings_obj.the_options["button_accept_link_color2"]
        : "#ffffff",
      accept_action2: settings_obj.the_options.hasOwnProperty(
        "button_accept_action2"
      )
        ? settings_obj.the_options["button_accept_action2"]
        : "#cookie_action_close_header",
      accept_url2: settings_obj.the_options.hasOwnProperty("button_accept_url2")
        ? settings_obj.the_options["button_accept_url2"]
        : "#",
      accept_as_button2:
        settings_obj.the_options.hasOwnProperty("button_accept_as_button2") &&
        (false === settings_obj.the_options["button_accept_as_button2"] ||
          0 === settings_obj.the_options["button_accept_as_button2"] ||
          "false" === settings_obj.the_options["button_accept_as_button2"])
          ? false
          : true,
      open_url2:
        settings_obj.the_options.hasOwnProperty("button_accept_new_win2") &&
        (true === settings_obj.the_options["button_accept_new_win2"] ||
          1 === settings_obj.the_options["button_accept_new_win2"] ||
          "true" === settings_obj.the_options["button_accept_new_win2"])
          ? true
          : false,
      accept_background_color2: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_color2"
      )
        ? settings_obj.the_options["button_accept_button_color2"]
        : "#18a300",
      accept_opacity2: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_opacity2"
      )
        ? settings_obj.the_options["button_accept_button_opacity2"]
        : "1",
      accept_style2: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_style2"
      )
        ? settings_obj.the_options["button_accept_button_border_style2"]
        : "none",
      accept_border_color2: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_color2"
      )
        ? settings_obj.the_options["button_accept_button_border_color2"]
        : "#18a300",
      accept_border_width2: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_width2"
      )
        ? settings_obj.the_options["button_accept_button_border_width2"]
        : "0",
      accept_border_radius2: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_border_radius2"
      )
        ? settings_obj.the_options["button_accept_button_border_radius2"]
        : "0",
      cookie_accept_all_on2:
        settings_obj.the_options.hasOwnProperty("button_accept_all_is_on2") &&
        (true === settings_obj.the_options["button_accept_all_is_on2"] ||
          1 === settings_obj.the_options["button_accept_all_is_on2"] ||
          "true" === settings_obj.the_options["button_accept_all_is_on2"])
          ? true
          : false,
      accept_all_text2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_text2"
      )
        ? settings_obj.the_options["button_accept_all_text2"]
        : "Accept All",
      accept_all_text_color2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_link_color2"
      )
        ? settings_obj.the_options["button_accept_all_link_color2"]
        : "#ffffff",
      accept_all_as_button2:
        settings_obj.the_options.hasOwnProperty(
          "button_accept_all_as_button2"
        ) &&
        (false === settings_obj.the_options["button_accept_all_as_button2"] ||
          0 === settings_obj.the_options["button_accept_all_as_button2"] ||
          "false" === settings_obj.the_options["button_accept_all_as_button2"])
          ? false
          : true,
      accept_all_action2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_action2"
      )
        ? settings_obj.the_options["button_accept_all_action2"]
        : "#cookie_action_close_header",
      accept_all_url2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_url2"
      )
        ? settings_obj.the_options["button_accept_all_url2"]
        : "#",
      accept_all_new_win2:
        settings_obj.the_options.hasOwnProperty("button_accept_all_new_win2") &&
        (true === settings_obj.the_options["button_accept_all_new_win2"] ||
          1 === settings_obj.the_options["button_accept_all_new_win2"] ||
          "true" === settings_obj.the_options["button_accept_all_new_win2"])
          ? true
          : false,
      accept_all_background_color2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_button_color2"
      )
        ? settings_obj.the_options["button_accept_all_button_color2"]
        : "#18a300",
      accept_all_style2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_style2"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_style2"]
        : "none",
      accept_all_border_color2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_color2"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_color2"]
        : "#18a300",
      accept_all_opacity2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_opacity2"
      )
        ? settings_obj.the_options["button_accept_all_btn_opacity2"]
        : "1",
      accept_all_border_width2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_width2"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_width2"]
        : "0",
      accept_all_border_radius2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_radius2"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_radius2"]
        : "0",

      cookie_decline_on2:
        settings_obj.the_options.hasOwnProperty("button_decline_is_on2") &&
        (false === settings_obj.the_options["button_decline_is_on2"] ||
          0 === settings_obj.the_options["button_decline_is_on2"] ||
          "false" === settings_obj.the_options["button_decline_is_on2"])
          ? false
          : true,
      decline_text2: settings_obj.the_options.hasOwnProperty(
        "button_decline_text2"
      )
        ? settings_obj.the_options["button_decline_text2"]
        : "Decline",
      decline_text_color2: settings_obj.the_options.hasOwnProperty(
        "button_decline_link_color2"
      )
        ? settings_obj.the_options["button_decline_link_color2"]
        : "#ffffff",
      decline_as_button2:
        settings_obj.the_options.hasOwnProperty("button_decline_as_button2") &&
        (false === settings_obj.the_options["button_decline_as_button2"] ||
          0 === settings_obj.the_options["button_decline_as_button2"] ||
          "false" === settings_obj.the_options["button_decline_as_button2"])
          ? false
          : true,
      decline_background_color2: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_color2"
      )
        ? settings_obj.the_options["button_decline_button_color2"]
        : "#333333",
      decline_opacity2: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_opacity2"
      )
        ? settings_obj.the_options["button_decline_button_opacity2"]
        : "1",
      decline_style2: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_style2"
      )
        ? settings_obj.the_options["button_decline_button_border_style2"]
        : "none",
      decline_border_color2: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_color2"
      )
        ? settings_obj.the_options["button_decline_button_border_color2"]
        : "#333333",
      decline_border_width2: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_width2"
      )
        ? settings_obj.the_options["button_decline_button_border_width2"]
        : "0",
      decline_border_radius2: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_border_radius2"
      )
        ? settings_obj.the_options["button_decline_button_border_radius2"]
        : "0",
      decline_action2: settings_obj.the_options.hasOwnProperty(
        "button_decline_action2"
      )
        ? settings_obj.the_options["button_decline_action2"]
        : "#cookie_action_close_header_reject",

      decline_url2: settings_obj.the_options.hasOwnProperty(
        "button_decline_url2"
      )
        ? settings_obj.the_options["button_decline_url2"]
        : "#",
      open_decline_url2:
        settings_obj.the_options.hasOwnProperty("button_decline_new_win2") &&
        (true === settings_obj.the_options["button_decline_new_win2"] ||
          1 === settings_obj.the_options["button_decline_new_win2"] ||
          "true" === settings_obj.the_options["button_decline_new_win2"])
          ? true
          : false,

      cookie_settings_on2:
        settings_obj.the_options.hasOwnProperty("button_settings_is_on2") &&
        (false === settings_obj.the_options["button_settings_is_on2"] ||
          1 === settings_obj.the_options["button_settings_is_on2"] ||
          "false" === settings_obj.the_options["button_settings_is_on2"])
          ? false
          : true,

      settings_text2: settings_obj.the_options.hasOwnProperty(
        "button_settings_text2"
      )
        ? settings_obj.the_options["button_settings_text2"]
        : "Cookie Settings",
      settings_text_color2: settings_obj.the_options.hasOwnProperty(
        "button_settings_link_color2"
      )
        ? settings_obj.the_options["button_settings_link_color2"]
        : "#ffffff",
      settings_as_button2:
        settings_obj.the_options.hasOwnProperty("button_settings_as_button2") &&
        (false === settings_obj.the_options["button_settings_as_button2"] ||
          0 === settings_obj.the_options["button_settings_as_button2"] ||
          "false" === settings_obj.the_options["button_settings_as_button2"])
          ? false
          : true,
      settings_background_color2: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_color2"
      )
        ? settings_obj.the_options["button_settings_button_color2"]
        : "#333333",
      settings_opacity2: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_opacity2"
      )
        ? settings_obj.the_options["button_settings_button_opacity2"]
        : "1",
      settings_style2: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_style2"
      )
        ? settings_obj.the_options["button_settings_button_border_style2"]
        : "none",
      settings_border_color2: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_color2"
      )
        ? settings_obj.the_options["button_settings_button_border_color2"]
        : "#333333",
      settings_border_width2: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_width2"
      )
        ? settings_obj.the_options["button_settings_button_border_width2"]
        : "0",
      settings_border_radius2: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_border_radius2"
      )
        ? settings_obj.the_options["button_settings_button_border_radius2"]
        : "0",
      confirm_text2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_text2"
      )
        ? settings_obj.the_options["button_confirm_text2"]
        : "Confirm",
      confirm_text_color2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_link_color2"
      )
        ? settings_obj.the_options["button_confirm_link_color2"]
        : "#ffffff",
      confirm_background_color2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_color2"
      )
        ? settings_obj.the_options["button_confirm_button_color2"]
        : "#18a300",
      confirm_opacity2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_opacity2"
      )
        ? settings_obj.the_options["button_confirm_button_opacity2"]
        : "1",
      confirm_style2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_style2"
      )
        ? settings_obj.the_options["button_confirm_button_border_style2"]
        : "none",
      confirm_border_color2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_color2"
      )
        ? settings_obj.the_options["button_confirm_button_border_color2"]
        : "#18a300",
      confirm_border_width2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_width2"
      )
        ? settings_obj.the_options["button_confirm_button_border_width2"]
        : "0",
      confirm_border_radius2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_border_radius2"
      )
        ? settings_obj.the_options["button_confirm_button_border_radius2"]
        : "0",
      cancel_text2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_text2"
      )
        ? settings_obj.the_options["button_cancel_text2"]
        : "Cancel",
      cancel_text_color2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_link_color2"
      )
        ? settings_obj.the_options["button_cancel_link_color2"]
        : "#ffffff",
      cancel_background_color2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_color2"
      )
        ? settings_obj.the_options["button_cancel_button_color2"]
        : "#333333",
      cancel_opacity2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_opacity2"
      )
        ? settings_obj.the_options["button_cancel_button_opacity2"]
        : "1",
      cancel_style2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_style2"
      )
        ? settings_obj.the_options["button_cancel_button_border_style2"]
        : "none",
      cancel_border_color2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_color2"
      )
        ? settings_obj.the_options["button_cancel_button_border_color2"]
        : "#333333",
      cancel_border_width2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_width2"
      )
        ? settings_obj.the_options["button_cancel_button_border_width2"]
        : "0",
      cancel_border_radius2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_border_radius2"
      )
        ? settings_obj.the_options["button_cancel_button_border_radius2"]
        : "0",
      opt_out_text2: settings_obj.the_options.hasOwnProperty(
        "button_donotsell_text2"
      )
        ? settings_obj.the_options["button_donotsell_text2"]
        : "Do Not Sell My Personal Information",
      opt_out_text_color2: settings_obj.the_options.hasOwnProperty(
        "button_donotsell_link_color2"
      )
        ? settings_obj.the_options["button_donotsell_link_color2"]
        : "#359bf5",

      is_script_blocker_on:
        settings_obj.the_options.hasOwnProperty("is_script_blocker_on") &&
        (true === settings_obj.the_options["is_script_blocker_on"] ||
          1 === settings_obj.the_options["is_script_blocker_on"])
          ? true
          : false,
      header_scripts: settings_obj.the_options.hasOwnProperty("header_scripts")
        ? this.stripSlashes(settings_obj.the_options["header_scripts"])
        : "",
      body_scripts: settings_obj.the_options.hasOwnProperty("body_scripts")
        ? this.stripSlashes(settings_obj.the_options["body_scripts"])
        : "",
      footer_scripts: settings_obj.the_options.hasOwnProperty("footer_scripts")
        ? this.stripSlashes(settings_obj.the_options["footer_scripts"])
        : "",
      success_error_message: "",
      custom_cookie_categories:
        settings_obj.cookie_list_settings.hasOwnProperty(
          "cookie_list_categories"
        )
          ? settings_obj.cookie_list_settings["cookie_list_categories"]
          : [],
      custom_cookie_types: settings_obj.cookie_list_settings.hasOwnProperty(
        "cookie_list_types"
      )
        ? settings_obj.cookie_list_settings["cookie_list_types"]
        : [],
      custom_cookie_category: 1,
      custom_cookie_type: "HTTP",
      custom_cookie_name: "",
      custom_cookie_domain: "",
      custom_cookie_duration: "",
      custom_cookie_description: "",
      is_custom_cookie_duration_disabled:
        this.custom_cookie_type === "HTTP Cookie" ? false : true,
      custom_cookie_duration_placeholder: "Duration(days/session)",
      post_cookie_list_length: settings_obj.cookie_list_settings.hasOwnProperty(
        "post_cookie_list"
      )
        ? settings_obj.cookie_list_settings["post_cookie_list"]["total"]
        : 0,
      post_cookie_list: settings_obj.cookie_list_settings.hasOwnProperty(
        "post_cookie_list"
      )
        ? settings_obj.cookie_list_settings["post_cookie_list"]["data"]
        : [],
      show_custom_form: this.post_cookie_list_length > 0 ? false : true,
      show_add_custom_button: this.post_cookie_list_length > 0 ? true : false,
      scan_cookie_list_length: settings_obj.cookie_scan_settings.hasOwnProperty(
        "scan_cookie_list"
      )
        ? settings_obj.cookie_scan_settings["scan_cookie_list"]["total"]
        : 0,
      scan_cookie_list: settings_obj.cookie_scan_settings.hasOwnProperty(
        "scan_cookie_list"
      )
        ? settings_obj.cookie_scan_settings["scan_cookie_list"]["data"]
        : [],
      scan_cookie_error_message:
        settings_obj.cookie_scan_settings.hasOwnProperty("error_message")
          ? settings_obj.cookie_scan_settings["error_message"]
          : "",
      scan_cookie_last_scan: settings_obj.cookie_scan_settings.hasOwnProperty(
        "last_scan"
      )
        ? settings_obj.cookie_scan_settings["last_scan"]
        : [],
      continue_scan: 1,
      pollCount: 0,
      onPrg: 0,
      template: settings_obj.the_options.hasOwnProperty("template")
        ? settings_obj.the_options["template"]
        : "default",
      cookie_accept_all_on:
        settings_obj.the_options.hasOwnProperty("button_accept_all_is_on") &&
        (true === settings_obj.the_options["button_accept_all_is_on"] ||
          1 === settings_obj.the_options["button_accept_all_is_on"])
          ? true
          : false,
      accept_all_text: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_text"
      )
        ? settings_obj.the_options["button_accept_all_text"]
        : "Accept All",
      accept_all_text_color: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_link_color"
      )
        ? settings_obj.the_options["button_accept_all_link_color"]
        : "#ffffff",
      accept_all_as_button_options: settings_obj.accept_button_as_options,
      accept_all_as_button:
        settings_obj.the_options.hasOwnProperty(
          "button_accept_all_as_button"
        ) &&
        (true === settings_obj.the_options["button_accept_all_as_button"] ||
          1 === settings_obj.the_options["button_accept_all_as_button"])
          ? true
          : false,
      accept_all_action: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_action"
      )
        ? settings_obj.the_options["button_accept_all_action"]
        : "#cookie_action_close_header",
      accept_all_url: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_url"
      )
        ? settings_obj.the_options["button_accept_all_url"]
        : "#",
      accept_all_open_url:
        this.accept_all_action === "#cookie_action_close_header" ? false : true,
      accept_all_new_win:
        settings_obj.the_options.hasOwnProperty("button_accept_all_new_win") &&
        (true === settings_obj.the_options["button_accept_all_new_win"] ||
          1 === settings_obj.the_options["button_accept_all_new_win"])
          ? true
          : false,
      accept_all_background_color: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_button_color"
      )
        ? settings_obj.the_options["button_accept_all_button_color"]
        : "#18a300",
      accept_all_style: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_style"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_style"]
        : "none",
      accept_all_border_color: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_color"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_color"]
        : "#18a300",
      accept_all_opacity: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_opacity"
      )
        ? settings_obj.the_options["button_accept_all_btn_opacity"]
        : "1",
      accept_all_border_width: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_width"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_width"]
        : "0",
      accept_all_border_radius: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_btn_border_radius"
      )
        ? settings_obj.the_options["button_accept_all_btn_border_radius"]
        : "0",
      //custom css
      gdpr_css_text: settings_obj.the_options.hasOwnProperty("gdpr_css_text")
        ? this.decodeCSS(settings_obj.the_options["gdpr_css_text"])
        : "",
      gdpr_css_text_free: "/*Your CSS here*/",
      //Do not track
      do_not_track_on:
        "true" == settings_obj.the_options["do_not_track_on"] ||
        1 === settings_obj.the_options["do_not_track_on"]
          ? true
          : false,
      //import file selected
      selectedFile: "",
      //Consent Log
      consent_log_switch_clicked: false,
      // Data Request
      data_reqs_on:
        "true" == settings_obj.the_options["data_reqs_on"] ||
        1 === settings_obj.the_options["data_reqs_on"] ||
        "1" == settings_obj.the_options["data_reqs_on"]
          ? true
          : false,
      shortcode_copied: false,
      data_reqs_switch_clicked: false,
      data_req_email_address: settings_obj.the_options.hasOwnProperty(
        "data_req_email_address"
      )
        ? settings_obj.the_options["data_req_email_address"]
        : "",
      data_req_subject: settings_obj.the_options.hasOwnProperty(
        "data_req_subject"
      )
        ? settings_obj.the_options["data_req_subject"]
        : "We have received your request",
      data_req_editor_message: settings_obj.the_options.hasOwnProperty(
        "data_req_editor_message"
      )
        ? this.decodeHTMLString(
            settings_obj.the_options["data_req_editor_message"]
          )
        : "",
      enable_safe:
        settings_obj.the_options.hasOwnProperty("enable_safe") &&
        ("true" === settings_obj.the_options["enable_safe"] ||
          1 === settings_obj.the_options["enable_safe"])
          ? true
          : false,
      reload_onSelect_law: false,
      reload_onSafeMode: false,
      // hide banner..
      select_pages: settings_obj.the_options.hasOwnProperty("select_pages")
        ? settings_obj.the_options["select_pages"]
        : [],
      select_pages_array: [],
      list_of_pages: settings_obj.list_of_pages,
      
      //script dependency
      is_script_dependency_on:
      settings_obj.the_options.hasOwnProperty("is_script_dependency_on") &&
      (true === settings_obj.the_options["is_script_dependency_on"] ||
        1 === settings_obj.the_options["is_script_dependency_on"])
        ? true
        : false,
      header_dependency: settings_obj.the_options.hasOwnProperty("header_dependency")
        ? settings_obj.the_options["header_dependnecy"]
        : '',
      header_dependency_list: settings_obj.header_dependency_list,
      header_dependency_map: {
        'Body Scripts': false,
        'Footer Scripts': false,
      },
      footer_dependency: settings_obj.the_options.hasOwnProperty("footer_dependency")
        ? settings_obj.the_options["footer_dependency"]
        : '',
      footer_dependency_selected: null,
      footer_dependency_list: settings_obj.footer_dependency_list,
      footer_dependency_map: {
        'Header Scripts': false,
        'Body Scripts': false,
      },
      
      // revoke consent text color.
      button_revoke_consent_text_color: settings_obj.the_options.hasOwnProperty(
        "button_revoke_consent_text_color"
      )
        ? settings_obj.the_options["button_revoke_consent_text_color"]
        : "",
      button_revoke_consent_background_color:
        settings_obj.the_options.hasOwnProperty(
          "button_revoke_consent_background_color"
        )
          ? settings_obj.the_options["button_revoke_consent_background_color"]
          : "",
      button_revoke_consent_text_color1: settings_obj.the_options.hasOwnProperty(
        "button_revoke_consent_text_color1"
      )
        ? settings_obj.the_options["button_revoke_consent_text_color1"]
        : "",
      button_revoke_consent_background_color1:
        settings_obj.the_options.hasOwnProperty(
          "button_revoke_consent_background_color1"
        )
          ? settings_obj.the_options["button_revoke_consent_background_color1"]
          : "",
      button_revoke_consent_text_color2: settings_obj.the_options.hasOwnProperty(
        "button_revoke_consent_text_color2"
      )
        ? settings_obj.the_options["button_revoke_consent_text_color2"]
        : "",
      button_revoke_consent_background_color2:
        settings_obj.the_options.hasOwnProperty(
          "button_revoke_consent_background_color2"
        )
          ? settings_obj.the_options["button_revoke_consent_background_color2"]
          : "",
      right_arrow: require("../admin/images/dashboard-icons/right-arrow.svg"),
      is_selectedCountry_on:
        settings_obj.the_options.hasOwnProperty("is_selectedCountry_on") &&
        (true === settings_obj.the_options["is_selectedCountry_on"] ||
          1 === settings_obj.the_options["is_selectedCountry_on"])
          ? true
          : false,
      is_selectedCountry_on_ccpa:
        settings_obj.the_options.hasOwnProperty("is_selectedCountry_on_ccpa") &&
        (true === settings_obj.the_options["is_selectedCountry_on_ccpa"] ||
          1 === settings_obj.the_options["is_selectedCountry_on_ccpa"])
          ? true
          : false,
      is_worldwide_on:
        settings_obj.the_options.hasOwnProperty("is_worldwide_on") &&
        (true === settings_obj.the_options["is_worldwide_on"] ||
          1 === settings_obj.the_options["is_worldwide_on"])
          ? true
          : false,
      is_worldwide_on_ccpa:
        settings_obj.the_options.hasOwnProperty("is_worldwide_on_ccpa") &&
        (true === settings_obj.the_options["is_worldwide_on_ccpa"] ||
          1 === settings_obj.the_options["is_worldwide_on_ccpa"])
          ? true
          : false,
      selectedRadioWorldWide:
        settings_obj.the_options.hasOwnProperty("is_worldwide_on") &&
        (true === settings_obj.the_options["is_worldwide_on"] ||
          1 === settings_obj.the_options["is_worldwide_on"])
          ? true
          : false,
      selectedRadioWorldWideCcpa:
        settings_obj.the_options.hasOwnProperty("is_worldwide_on_ccpa") &&
        (true === settings_obj.the_options["is_worldwide_on_ccpa"] ||
          1 === settings_obj.the_options["is_worldwide_on_ccpa"])
          ? true
          : false,
      list_of_countries: settings_obj.list_of_countries,
      select_countries: settings_obj.the_options.hasOwnProperty(
        "select_countries"
      )
        ? settings_obj.the_options["select_countries"]
        : [],
      select_countries_ccpa: settings_obj.the_options.hasOwnProperty(
        "select_countries_ccpa"
      )
        ? settings_obj.the_options["select_countries_ccpa"]
        : [],
      select_countries_array: [],
      select_countries_array_ccpa: [],
      show_Select_Country: false,
      selectedRadioCountry:
        settings_obj.the_options.hasOwnProperty("is_selectedCountry_on") &&
        (true === settings_obj.the_options["is_selectedCountry_on"] ||
          1 === settings_obj.the_options["is_selectedCountry_on"])
          ? true
          : false,
      selectedRadioCountryCcpa:
        settings_obj.the_options.hasOwnProperty("is_selectedCountry_on_ccpa") &&
        (true === settings_obj.the_options["is_selectedCountry_on_ccpa"] ||
          1 === settings_obj.the_options["is_selectedCountry_on_ccpa"])
          ? true
          : false,      
      isCategoryActive: true,
      isFeaturesActive: false,
      isVendorsActive: false,
      cookieSettingsPopupAccentColor: ''
    };
  },
  methods: {
    stripSlashes(value) {
      return value.replace(/\\(.)/gm, "$1");
    },
    copyTextToClipboard() {
      const textToCopy = "[wpl_data_request]";
      const textArea = document.createElement("textarea");
      textArea.value = textToCopy;
      document.body.appendChild(textArea);
      textArea.select();
      document.execCommand("copy");
      document.body.removeChild(textArea);
      this.shortcode_copied = true;
      setTimeout(() => {
        this.shortcode_copied = false;
      }, 1500);
    },
    decodeHTMLString(encodedString) {
      var doc = new DOMParser().parseFromString(encodedString, "text/html");
      return doc.documentElement.textContent.replace(/\\/g, "");
    },
    decodeCSS(encodedCSS) {
      const lines = encodedCSS.split("\\r\\n");
      let decodedCSS = "";
      let currentIndent = 0;

      for (const line of lines) {
        const trimmedLine = line.trim();

        if (trimmedLine === "") continue; // Skip empty lines

        if (trimmedLine.startsWith("}") && currentIndent > 0) {
          currentIndent--;
        }

        decodedCSS += "  ".repeat(currentIndent) + trimmedLine + "\n";

        if (trimmedLine.endsWith("{")) {
          currentIndent++;
        }
      }

      return decodedCSS;
    },
    setValues() {
      if (this.gdpr_policy === "both") {
        this.is_ccpa = true;
        this.is_gdpr = true;
        this.is_eprivacy = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = true;
        this.show_revoke_card = true;
      } else if (this.gdpr_policy === "ccpa") {
        this.is_ccpa = true;
        this.is_eprivacy = false;
        this.is_gdpr = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = true;
        this.show_revoke_card = false;
      } else if (this.gdpr_policy === "gdpr") {
        this.is_gdpr = true;
        this.is_ccpa = false;
        this.is_eprivacy = false;
        this.is_lgpd = false;
        this.show_revoke_card = true;
        this.show_visitor_conditions = true;
      } else if (this.gdpr_policy === "lgpd") {
        this.is_gdpr = false;
        this.is_ccpa = false;
        this.is_lgpd = true;
        this.is_eprivacy = false;
        this.show_revoke_card = true;
        this.show_visitor_conditions = false;
      } else {
        this.is_eprivacy = true;
        this.is_gdpr = false;
        this.is_ccpa = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = false;
        this.show_revoke_card = true;
      }
      for (let i = 0; i < this.list_of_contents.length; i++) {
        if (
          this.restrict_posts.includes(this.list_of_contents[i].code.toString())
        ) {
          this.restrict_array.push(this.list_of_contents[i]);
        }
      }
      for (let i = 0; i < this.privacy_policy_options.length; i++) {
        if (this.button_readmore_page == this.privacy_policy_options[i].code) {
          this.readmore_page = this.privacy_policy_options[i].label;
          break;
        }
      }
      for (let i = 0; i < this.list_of_countries.length; i++) {
        if (this.select_countries.includes(this.list_of_countries[i].code)) {
          this.select_countries_array.push(this.list_of_countries[i]);
        }
      }
      for (let i = 0; i < this.list_of_countries.length; i++) {
        if (this.select_countries_ccpa.includes(this.list_of_countries[i].code)) {
          this.select_countries_array_ccpa.push(this.list_of_countries[i]);
        }
      }
      for (let i = 0; i < this.scripts_list_total; i++) {
        this.scripts_list_data[i]["script_status"] = Boolean(
          parseInt(this.scripts_list_data[i]["script_status"])
        );
        for (let j = 0; j < this.category_list_options.length; j++) {
          if (
            this.category_list_options[j].code ===
            this.scripts_list_data[i]["script_category"].toString()
          ) {
            this.scripts_list_data[i]["script_category_label"] =
              this.category_list_options[j].label;
            break;
          }
        }
      }
      if (this.accept_action === "#cookie_action_close_header") {
        this.is_open_url = false;
      } else {
        this.is_open_url = true;
      }
      if (this.accept_all_action === "#cookie_action_close_header") {
        this.accept_all_open_url = false;
      } else {
        this.accept_all_open_url = true;
      }
      if (this.decline_action === "#cookie_action_close_header_reject") {
        this.decline_open_url = false;
      } else {
        this.decline_open_url = true;
      }
      if (this.show_cookie_as === "banner") {
        this.is_banner = true;
      } else {
        this.is_banner = false;
      }
      if (this.custom_cookie_type === "HTTP") {
        this.is_custom_cookie_duration_disabled = false;
        this.custom_cookie_duration = "";
      } else {
        this.is_custom_cookie_duration_disabled = true;
        this.custom_cookie_duration = "Persistent";
      }
      this.show_custom_form = this.post_cookie_list_length > 0 ? false : true;
      this.show_add_custom_button =
        this.post_cookie_list_length > 0 ? true : false;

      this.disableSwitch = false;

      // multiple entries for hide banner.
      for (let i = 0; i < this.list_of_pages.length; i++) {
        if (this.select_pages.includes(this.list_of_pages[i].code.toString())) {
          this.select_pages_array.push(this.list_of_pages[i]);
        }
      }
    },
    editorInit: function () {
      require("brace/ext/language_tools"); //language extension prerequsite...
      require("brace/mode/html");
      require("brace/mode/javascript"); //language
      require("brace/mode/less");
      require("brace/mode/css");
      require("brace/theme/monokai");
      require("brace/snippets/css"); //snippet
    },
    setPostListValues() {
      for (let i = 0; i < this.post_cookie_list_length; i++) {
        if (this.post_cookie_list[i]["type"] === "HTTP") {
          this.post_cookie_list[i]["enable_duration"] = false;
        } else {
          this.post_cookie_list[i]["enable_duration"] = true;
        }
        for (let j = 0; j < this.custom_cookie_types.length; j++) {
          if (
            this.custom_cookie_types[j]["code"] ===
            this.post_cookie_list[i]["type"]
          ) {
            this.post_cookie_list[i]["type_name"] =
              this.custom_cookie_types[j].label;
          }
        }
      }
      this.show_custom_form = this.post_cookie_list_length > 0 ? false : true;
      this.show_add_custom_button =
        this.post_cookie_list_length > 0 ? true : false;
    },
    setScanListValues() {
      for (let i = 0; i < this.scan_cookie_list_length; i++) {
        for (let j = 0; j < this.custom_cookie_types.length; j++) {
          if (
            this.custom_cookie_types[j]["code"] ===
            this.scan_cookie_list[i]["type"]
          ) {
            this.scan_cookie_list[i]["type_name"] =
              this.custom_cookie_types[j].label;
          }
        }
      }
    },
    onSwitchCookieEnable() {
      this.cookie_is_on = !this.cookie_is_on;
    },
    onSwitchBannerPreviewEnable() {
      this.isCategoryActive = true;
      this.isFeaturesActive = false;
      this.isVendorsActive = false;
      //changing the value of banner_preview_swicth_value enable/disable
      this.banner_preview_is_on = !this.banner_preview_is_on;
    },
    onSwitchDntEnable() {
      //changing the value of do_not_track_on enable/disable
      this.do_not_track_on = !this.do_not_track_on;
    },
    onSwitchDataReqsEnable() {
      //changing the value of data_reqs_on enable/disable
      this.data_reqs_on = !this.data_reqs_on;
      this.data_reqs_switch_clicked = true;
    },
    onSwitchCookieAcceptEnable() {
      this.cookie_accept_on = !this.cookie_accept_on;
    },
    onSwitchCookieAcceptAllEnable() {
      this.cookie_accept_all_on = !this.cookie_accept_all_on;
    },
    onSwitchReloadLaw() {
      this.reload_onSelect_law = !this.reload_onSelect_law;
      this.reload_onSelect_law = true;
    },
    onSwitchIABEnable(value) {
      this.is_iab_on = !this.is_iab_on;
      if (value) {
        this.selectedRadioIab = value === "yes" ? "yes" : "no";
      }
    },
    onSwitchWorldWideEnable() {
      this.selectedRadioWorldWide = "yes";
      this.selectedRadioCountry = false;
      this.is_worldwide_on = true;
      this.is_eu_on = false;
      this.is_selectedCountry_on = false;
    },
    onSwitchWorldWideEnableCcpa() {
      this.selectedRadioWorldWideCcpa = "yes";
      this.selectedRadioCountryCcpa = false;
      this.is_worldwide_on_ccpa = true;
      this.is_selectedCountry_on_ccpa = false;
      this.is_ccpa_on = false;
    },
    onSwitchEUEnable(isChecked) {
      if (isChecked) {
        // this.selectedRadioGdpr = true;
        this.selectedRadioWorldWide = false;
        this.is_eu_on = true;
        this.is_worldwide_on = false;
      } else {
        // this.selectedRadioGdpr = false;
        this.is_eu_on = false;
        if (this.is_selectedCountry_on != true) {
          this.selectedRadioWorldWide = "yes";
        }
      }
    },
    onSwitchSelectedCountryEnable(isChecked) {
      if (isChecked) {
        this.is_selectedCountry_on = true;
        this.selectedRadioCountry = true;
        this.selectedRadioWorldWide = false;
        this.is_worldwide_on = false;
      } else {
        this.is_selectedCountry_on = false;
        this.selectedRadioCountry = false;
        if (this.is_eu_on != true && this.is_ccpa_on != true) {
          this.selectedRadioWorldWide = "yes";
        }
      }
    },
    onSwitchSelectedCountryEnableCcpa(isChecked) {
      if (isChecked) {
        this.is_selectedCountry_on_ccpa = true;
        this.selectedRadioCountryCcpa = true;
        this.selectedRadioWorldWideCcpa = false;
        this.is_worldwide_on_ccpa = false;
      } else {
        this.is_selectedCountry_on_ccpa = false;
        this.selectedRadioCountryCcpa = false;
        if (this.is_ccpa_on != true) {
          this.selectedRadioWorldWideCcpa = "yes";
        }
      }
    },
    onSwitchCCPAEnable(isChecked) {
      if (isChecked) {
        this.selectedRadioWorldWideCcpa = false;
        this.is_ccpa_on = true;
        this.is_worldwide_on_ccpa = false;
      } else {
        this.is_ccpa_on = false;
        if (this.is_selectedCountry_on != true) {
          this.selectedRadioWorldWideCcpa = "yes";
        }
      }
    },
    onCountrySelect(value) {
      this.select_countries = this.select_countries_array.join(",");
    },
    onCountrySelectCcpa(value) {
      this.select_countries_ccpa = this.select_countries_array_ccpa.join(",");
    },
    showSelectCountryForm() {
      this.show_Select_Country = !this.show_Select_Country;
    },
    onSwitchIabtcfEnable() {
      this.iabtcf_is_on = !this.iabtcf_is_on;
      if (this.iabtcf_is_on) {
        this.gdpr_message = `We and our <a id = "vendor-link" href = "#" data-toggle = "gdprmodal" data-target = "#gdpr-gdprmodal">836 partners</a> use cookies and other tracking technologies to improve your experience on our website. We may store and/or access information on a device and process personal data, such as your IP address and browsing data, for personalised advertising and content, advertising and content measurement, audience research and services development. Additionally, we may utilize precise geolocation data and identification through device scanning.\n\nPlease note that your consent will be valid across all our subdomains. You can change or withdraw your consent at any time by clicking the “Cookie Settings” button at the bottom of your screen. We respect your choices and are committed to providing you with a transparent and secure browsing experience.`;
        this.gdpr_about_cookie_message =
          "Customize your consent preferences for Cookie Categories and advertising tracking preferences for Purposes & Features and Vendors below. You can give granular consent for each Third Party Vendor. Most vendors require consent for personal data processing, while some rely on legitimate interest. However, you have the right to object to their use of legitimate interest. The choices you make regarding the purposes and entities listed in this notice are saved in a cookie named wpl_tc_string for a maximum duration of 12 months.";
      } else {
        this.gdpr_message =
          "This website uses cookies to improve your experience. We'll assume you're ok with this, but you can opt-out if you wish.";
        this.gdpr_about_cookie_message =
          "Cookies are small text files that can be used by websites to make a user's experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.";
      }
    },
    onSwitchGCMEnable(){
      this.gcm_is_on = !this.gcm_is_on;
    },
    onSwitchGCMUrlPass(){
      this.gcm_url_passthrough = !this.gcm_url_passthrough;
    },
    onSwitchGCMAdsRedact(){
      this.gcm_ads_redact = !this.gcm_ads_redact;
    },
    onSwitchGCMDebugMode(){
      this.gcm_debug_mode = !this.gcm_debug_mode;
    },
    checkGCMStatus(){
    },
    onSwitchGCMAdvertiserMode(){
      this.gcm_advertiser_mode = !this.gcm_advertiser_mode;
    },
    onSwitchGacmEnable() {
      this.gacm_is_on = !this.gacm_is_on;
    },
    onEnablesafeSwitch() {
      if (this.enable_safe === "true") {
        this.is_worldwide_on = true;
        this.is_worldwide_on_ccpa = true;
        this.is_eu_on = false;
        this.is_ccpa_on = false;
        this.selectedRadioCountry = false;
        this.selectedRadioCountryCcpa = false;
      } else {
        this.is_worldwide_on = true;
        this.is_worldwide_on_ccpa = true;
        this.is_eu_on = false;
        this.is_ccpa_on = false;
        this.selectedRadioCountry = false;
        this.selectedRadioCountryCcpa = false;
      }
    },
    onSwitchRevokeConsentEnable() {
      this.is_revoke_consent_on = !this.is_revoke_consent_on;
    },
    onSwitchRevokeConsentEnable1() {
      this.is_revoke_consent_on1 = !this.is_revoke_consent_on1;
    },
    onSwitchRevokeConsentEnable2() {
      this.is_revoke_consent_on2 = !this.is_revoke_consent_on2;
    },
    onSwitchAutotick() {
      this.autotick = !this.autotick;
    },
    onSwitchAutoHide() {
      this.auto_hide = !this.auto_hide;
    },
    onSwitchAutoBannerInitialize() {
      this.auto_banner_initialize = !this.auto_banner_initialize;
    },
    onSwitchAutoScroll() {
      this.auto_scroll = !this.auto_scroll;
    },
    onSwitchAutoClick() {
      this.auto_click = !this.auto_click;
    },
    onSwitchAutoScrollReload() {
      this.auto_scroll_reload = !this.auto_scroll_reload;
    },
    onSwitchAcceptReload() {
      this.accept_reload = !this.accept_reload;
    },
    onSwitchDeclineReload() {
      this.decline_reload = !this.decline_reload;
    },
    onSwitchDeleteOnDeactivation() {
      this.delete_on_deactivation = !this.delete_on_deactivation;
    },
    onSwitchEnableSafe() {
      this.onEnablesafeSwitch();
      this.onSwitchReloadSafeMode();
      this.enable_safe = !this.enable_safe;
    },
    onSwitchReloadSafeMode() {
      this.reload_onSafeMode = !this.reload_onSafeMode;
      this.reload_onSafeMode = true;
    },
    onSwitchShowCredits() {
      this.show_credits = !this.show_credits;
    },
    onSwitchLoggingOn() {
      this.logging_on = !this.logging_on;
      this.consent_log_switch_clicked = true;
    },
    onClickAddMedia() {
      // Get the button element
      jQuery(document).ready(function ($) {
        var frame = wp.media({
          title: "Select or Upload Media",
          button: {
            text: "Use this media",
          },
          multiple: false, // Set to false if selecting only one file
        });

        frame.open();

        frame.on("select", function () {
          var selection = frame.state().get("selection");

          selection.map(function (attachment) {
            var attachmentURL = attachment.attributes.url;
            var attachmentType = attachment.attributes.type;
            var attachmentFileName = attachment.attributes.filename;

            var editor = $("#quill-container .ql-editor")[0];
            var quillInstance = editor.__quill || editor.parentNode.__quill;

            if (attachmentType === "application" || attachmentType === "text") {
              var link = $("<a>")
                .attr("href", attachmentURL)
                .text(attachmentFileName);
              quillInstance.root.appendChild(link[0]);
              quillInstance.root.appendChild($("<br>")[0]);
            } else {
              quillInstance.insertEmbed(
                quillInstance.getLength(),
                "image",
                attachmentURL
              );
            }
          });
        });
      });
    },
    cookieAcceptChange(value) {
      if (value === "#cookie_action_close_header") {
        this.is_open_url = false;
      } else {
        this.is_open_url = true;
      }
    },
    cookieAcceptAllChange(value) {
      if (value === "#cookie_action_close_header") {
        this.accept_all_open_url = false;
      } else {
        this.accept_all_open_url = true;
      }
    },
    cookieDeclineChange(value) {
      if (value === "#cookie_action_close_header_reject") {
        this.decline_open_url = false;
      } else {
        this.decline_open_url = true;
      }
    },
    cookieTypeChange(value) {
      this.processof_auto_template_generated = false;
      if (value === "banner") {
        if(this.template == 'blue_full') this.template = 'blue_center';
        this.is_banner = true;
      } else {
        this.is_banner = false;
      }
    },
    cookiebannerPositionChange(position) {
      // Ensure jQuery is available and use no-conflict mode if necessary
      jQuery(document).ready(
        function ($) {
          if (position === "top") {
            this.cookie_position = "top";
            $("#banner-position-top-id").addClass("banner-position-top");
            $("#banner-position-bottom-id").removeClass(
              "banner-position-bottom"
            );
            //icons
            $("#banner-position-top-icon").addClass(
              "dashicons dashicons-saved"
            );
            $("#banner-position-bottom-icon").removeClass(
              "dashicons dashicons-saved"
            );
          } else if (position === "bottom") {
            this.cookie_position = "bottom";
            $("#banner-position-bottom-id").addClass("banner-position-bottom");
            $("#banner-position-top-id").removeClass("banner-position-top");
            //icons
            $("#banner-position-bottom-icon").addClass(
              "dashicons dashicons-saved"
            );
            $("#banner-position-top-icon").removeClass(
              "dashicons dashicons-saved"
            );
          }
        }.bind(this)
      ); // Bind 'this' to ensure it refers to the Vue instance
    },
    cookiewidgetPositionChange(value) {
      jQuery(document).ready(
        function ($) {
          if (value === "left") {
            this.cookie_widget_position = "left";
            $("#widget-position-left-id").addClass("widget-position-top");
            $("#widget-position-right-id").removeClass("widget-position-top");
            $("#widget-position-top_left-id").removeClass(
              "widget-position-top"
            );
            $("#widget-position-top_right-id").removeClass(
              "widget-position-top"
            );
            // icon
            $("#widget-position-left-icon").addClass(
              "dashicons dashicons-saved"
            );
            $("#widget-position-right-icon").removeClass(
              "dashicons dashicons-saved"
            );
            $("#widget-position-top_left-icon").removeClass(
              "dashicons dashicons-saved"
            );
            $("#widget-position-top_right-icon").removeClass(
              "dashicons dashicons-saved"
            );
          } else if (value === "right") {
            this.cookie_widget_position = "right";
            $("#widget-position-right-id").addClass("widget-position-top");
            $("#widget-position-left-id").removeClass("widget-position-top");
            $("#widget-position-top_left-id").removeClass(
              "widget-position-top"
            );
            $("#widget-position-top_right-id").removeClass(
              "widget-position-top"
            );
            //icons
            $("#widget-position-right-icon").addClass(
              "dashicons dashicons-saved"
            );
            $("#widget-position-left-icon").removeClass(
              "dashicons dashicons-saved"
            );
            $("#widget-position-top_left-icon").removeClass(
              "dashicons dashicons-saved"
            );
            $("#widget-position-top_right-icon").removeClass(
              "dashicons dashicons-saved"
            );
          } else if (value === "top_left") {
            this.cookie_widget_position = "top_left";
            $("#widget-position-top_left-id").addClass("widget-position-top");
            $("#widget-position-right-id").removeClass("widget-position-top");
            $("#widget-position-left-id").removeClass("widget-position-top");
            $("#widget-position-top_right-id").removeClass(
              "widget-position-top"
            );
            //icons
            $("#widget-position-top_left-icon").addClass(
              "dashicons dashicons-saved"
            );
            $("#widget-position-right-icon").removeClass(
              "dashicons dashicons-saved"
            );
            $("#widget-position-left-icon").removeClass(
              "dashicons dashicons-saved"
            );
            $("#widget-position-top_right-icon").removeClass(
              "dashicons dashicons-saved"
            );
          } else if (value === "top_right") {
            this.cookie_widget_position = "top_right";
            $("#widget-position-top_right-id").addClass("widget-position-top");
            $("#widget-position-right-id").removeClass("widget-position-top");
            $("#widget-position-left-id").removeClass("widget-position-top");
            $("#widget-position-top_left-id").removeClass(
              "widget-position-top"
            );
            //icons
            $("#widget-position-top_right-icon").addClass(
              "dashicons dashicons-saved"
            );
            $("#widget-position-right-icon").removeClass(
              "dashicons dashicons-saved"
            );
            $("#widget-position-top_left-icon").removeClass(
              "dashicons dashicons-saved"
            );
            $("#widget-position-left-icon").removeClass(
              "dashicons dashicons-saved"
            );
          }
        }.bind(this)
      );
    },
    selectTab(tabName) {
      this.isCategoryActive  = (tabName === 'category');
      this.isFeaturesActive  = (tabName === 'features');
      this.isVendorsActive   = (tabName === 'vendors');
    },
    turnOffPreviewBanner() {
      this.banner_preview_is_on = false;
    },
    onTemplateChange(value) {
      this.template = value;
      const selectedTemplate = this.json_templates[value];
      this.cookie_bar_color =                       selectedTemplate['styles']['background-color'];
      this.cookie_bar_opacity =                     selectedTemplate['styles']['opacity'];
      this.cookie_text_color =                      selectedTemplate['styles']['color'];
      this.border_style =                           selectedTemplate['styles']['border-style'];
      this.cookie_bar_border_width =                selectedTemplate['styles']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.cookie_border_color =                    selectedTemplate['styles']['border-color'];
      this.cookie_bar_border_radius =               selectedTemplate['styles']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.cookie_font =                            selectedTemplate['styles']['font-family'];
      this.cookie_accept_on =                       selectedTemplate['accept_button']['is_on'];
      this.accept_as_button =                       true;
      this.accept_text_color =                      selectedTemplate['accept_button']['color'];
      this.accept_background_color =                selectedTemplate['accept_button']['background-color'];
      this.accept_style =                           selectedTemplate['accept_button']['border-style'];
      this.accept_border_color =                    selectedTemplate['accept_button']['border-color'];
      this.accept_opacity =                         selectedTemplate['accept_button']['opacity'];
      this.accept_border_width =                    selectedTemplate['accept_button']['border-width'].substring(0, selectedTemplate['accept_button']['border-width'].length - 2);
      this.accept_border_radius =                   selectedTemplate['accept_button']['border-radius'].substring(0, selectedTemplate['accept_button']['border-radius'].length - 2);
      this.cookie_decline_on =                      selectedTemplate['decline_button']['is_on'];
      this.decline_as_button =                      true;
      this.decline_text_color =                     selectedTemplate['decline_button']['color'];
      this.decline_background_color =               selectedTemplate['decline_button']['background-color'];
      this.decline_style =                          selectedTemplate['decline_button']['border-style'];
      this.decline_border_color =                   selectedTemplate['decline_button']['border-color'];
      this.decline_opacity =                        selectedTemplate['decline_button']['opacity'];
      this.decline_border_width =                   selectedTemplate['decline_button']['border-width'].substring(0, selectedTemplate['decline_button']['border-width'].length - 2);
      this.decline_border_radius =                  selectedTemplate['decline_button']['border-radius'].substring(0, selectedTemplate['decline_button']['border-radius'].length - 2);
      this.cookie_accept_all_on =                   selectedTemplate['accept_all_button']['is_on'];
      this.accept_all_as_button =                   true;
      this.accept_all_text_color =                  selectedTemplate['accept_all_button']['color'];
      this.accept_all_background_color =            selectedTemplate['accept_all_button']['background-color'];
      this.accept_all_style =                       selectedTemplate['accept_all_button']['border-style'];
      this.accept_all_border_color =                selectedTemplate['accept_all_button']['border-color'];
      this.accept_all_opacity =                     selectedTemplate['accept_all_button']['opacity'];
      this.accept_all_border_width =                selectedTemplate['accept_all_button']['border-width'].substring(0, selectedTemplate['accept_all_button']['border-width'].length - 2);
      this.accept_all_border_radius =               selectedTemplate['accept_all_button']['border-radius'].substring(0, selectedTemplate['accept_all_button']['border-radius'].length - 2);
      this.cookie_settings_on =                     selectedTemplate['settings_button']['is_on'];
      this.settings_as_button =                     true;
      this.settings_text_color =                    selectedTemplate['settings_button']['color'];
      this.settings_background_color =              selectedTemplate['settings_button']['background-color'];
      this.settings_style =                         selectedTemplate['settings_button']['border-style'];
      this.settings_border_color =                  selectedTemplate['settings_button']['border-color'];
      this.settings_opacity =                       selectedTemplate['settings_button']['opacity'];
      this.settings_border_width =                  selectedTemplate['settings_button']['border-width'].substring(0, selectedTemplate['settings_button']['border-width'].length - 2);
      this.settings_border_radius =                 selectedTemplate['settings_button']['border-radius'].substring(0, selectedTemplate['settings_button']['border-radius'].length - 2);
      this.button_readmore_link_color =             selectedTemplate['readmore_button']['color'];
      this.button_readmore_button_color =           selectedTemplate['readmore_button']['background-color'];
      this.button_readmore_button_opacity =         selectedTemplate['readmore_button']['opacity'];
      this.button_readmore_button_border_style =    selectedTemplate['readmore_button']['border-style'];
      this.button_readmore_button_border_color =    selectedTemplate['readmore_button']['border-color'];
      this.button_readmore_button_border_radius =   selectedTemplate['readmore_button']['border-radius'].substring(0, selectedTemplate['readmore_button']['border-radius'].length - 2);
      this.button_readmore_button_border_width =    selectedTemplate['readmore_button']['border-width'].substring(0, selectedTemplate['readmore_button']['border-width'].length - 2);
      this.opt_out_text_color =                     selectedTemplate['opt_out_button']['color'];
      this.button_revoke_consent_text_color =       selectedTemplate['revoke_consent_button']['color'];
      this.button_revoke_consent_background_color = selectedTemplate['revoke_consent_button']['background-color'];
      //ab testing banners settings
      
      this.cookie_bar_color1 =                       selectedTemplate['styles']['background-color'];
      this.cookie_bar_opacity1 =                     selectedTemplate['styles']['opacity'];
      this.cookie_text_color1 =                      selectedTemplate['styles']['color'];
      this.border_style1 =                           selectedTemplate['styles']['border-style'];
      this.cookie_bar_border_width1 =                selectedTemplate['styles']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.cookie_border_color1 =                    selectedTemplate['styles']['border-color'];
      this.cookie_bar_border_radius1 =               selectedTemplate['styles']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.cookie_font1 =                            selectedTemplate['styles']['font-family'];
      this.cookie_accept_on1 =                       selectedTemplate['accept_button']['is_on'];
      this.accept_as_button1 =                       true;
      this.accept_text_color1 =                      selectedTemplate['accept_button']['color'];
      this.accept_background_color1 =                selectedTemplate['accept_button']['background-color'];
      this.accept_style1 =                           selectedTemplate['accept_button']['border-style'];
      this.accept_border_color1 =                    selectedTemplate['accept_button']['border-color'];
      this.accept_opacity1 =                         selectedTemplate['accept_button']['opacity'];
      this.accept_border_width1 =                    selectedTemplate['accept_button']['border-width'].substring(0, selectedTemplate['accept_button']['border-width'].length - 2);
      this.accept_border_radius1 =                   selectedTemplate['accept_button']['border-radius'].substring(0, selectedTemplate['accept_button']['border-radius'].length - 2);
      this.cookie_decline_on1 =                      selectedTemplate['decline_button']['is_on'];
      this.decline_as_button1 =                      true;
      this.decline_text_color1 =                     selectedTemplate['decline_button']['color'];
      this.decline_background_color1 =               selectedTemplate['decline_button']['background-color'];
      this.decline_style1 =                          selectedTemplate['decline_button']['border-style'];
      this.decline_border_color1 =                   selectedTemplate['decline_button']['border-color'];
      this.decline_opacity1 =                        selectedTemplate['decline_button']['opacity'];
      this.decline_border_width1 =                   selectedTemplate['decline_button']['border-width'].substring(0, selectedTemplate['decline_button']['border-width'].length - 2);
      this.decline_border_radius1 =                  selectedTemplate['decline_button']['border-radius'].substring(0, selectedTemplate['decline_button']['border-radius'].length - 2);
      this.cookie_accept_all_on1 =                   selectedTemplate['accept_all_button']['is_on'];
      this.accept_all_as_button1 =                   true;
      this.accept_all_text_color1 =                  selectedTemplate['accept_all_button']['color'];
      this.accept_all_background_color1 =            selectedTemplate['accept_all_button']['background-color'];
      this.accept_all_style1 =                       selectedTemplate['accept_all_button']['border-style'];
      this.accept_all_border_color1 =                selectedTemplate['accept_all_button']['border-color'];
      this.accept_all_opacity1 =                     selectedTemplate['accept_all_button']['opacity'];
      this.accept_all_border_width1 =                selectedTemplate['accept_all_button']['border-width'].substring(0, selectedTemplate['accept_all_button']['border-width'].length - 2);
      this.accept_all_border_radius1 =               selectedTemplate['accept_all_button']['border-radius'].substring(0, selectedTemplate['accept_all_button']['border-radius'].length - 2);
      this.cookie_settings_on1 =                     selectedTemplate['settings_button']['is_on'];
      this.settings_as_button1 =                     true;
      this.settings_text_color1 =                    selectedTemplate['settings_button']['color'];
      this.settings_background_color1 =              selectedTemplate['settings_button']['background-color'];
      this.settings_style1 =                         selectedTemplate['settings_button']['border-style'];
      this.settings_border_color1 =                  selectedTemplate['settings_button']['border-color'];
      this.settings_opacity1 =                       selectedTemplate['settings_button']['opacity'];
      this.settings_border_width1 =                  selectedTemplate['settings_button']['border-width'].substring(0, selectedTemplate['settings_button']['border-width'].length - 2);
      this.settings_border_radius1 =                 selectedTemplate['settings_button']['border-radius'].substring(0, selectedTemplate['settings_button']['border-radius'].length - 2);
      this.opt_out_text_color1 =                     selectedTemplate['opt_out_button']['color'];
      this.button_readmore_link_color1 =             selectedTemplate['readmore_button']['color'];
      this.button_readmore_button_color1 =           selectedTemplate['readmore_button']['background-color'];
      this.button_readmore_button_opacity1 =         selectedTemplate['readmore_button']['opacity'];
      this.button_readmore_button_border_style1 =    selectedTemplate['readmore_button']['border-style'];
      this.button_readmore_button_border_color1 =    selectedTemplate['readmore_button']['border-color'];
      this.button_readmore_button_border_radius1 =   selectedTemplate['readmore_button']['border-radius'].substring(0, selectedTemplate['readmore_button']['border-radius'].length - 2);
      this.button_readmore_button_border_width1 =    selectedTemplate['readmore_button']['border-width'].substring(0, selectedTemplate['readmore_button']['border-width'].length - 2);
      this.button_revoke_consent_text_color1 =       selectedTemplate['revoke_consent_button']['color'];
      this.button_revoke_consent_background_color1 = selectedTemplate['revoke_consent_button']['background-color'];

      this.cookie_bar_color2 =                       selectedTemplate['styles']['background-color'];
      this.cookie_bar_opacity2 =                     selectedTemplate['styles']['opacity'];
      this.cookie_text_color2 =                      selectedTemplate['styles']['color'];
      this.border_style2 =                           selectedTemplate['styles']['border-style'];
      this.cookie_bar_border_width2 =                selectedTemplate['styles']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.cookie_border_color2 =                    selectedTemplate['styles']['border-color'];
      this.cookie_bar_border_radius2 =               selectedTemplate['styles']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.cookie_font2 =                            selectedTemplate['styles']['font-family'];
      this.cookie_accept_on2 =                       selectedTemplate['accept_button']['is_on'];
      this.accept_as_button2 =                       true;
      this.accept_text_color2 =                      selectedTemplate['accept_button']['color'];
      this.accept_background_color2 =                selectedTemplate['accept_button']['background-color'];
      this.accept_style2 =                           selectedTemplate['accept_button']['border-style'];
      this.accept_border_color2 =                    selectedTemplate['accept_button']['border-color'];
      this.accept_opacity2 =                         selectedTemplate['accept_button']['opacity'];
      this.accept_border_width2 =                    selectedTemplate['accept_button']['border-width'].substring(0, selectedTemplate['accept_button']['border-width'].length - 2);
      this.accept_border_radius2 =                   selectedTemplate['accept_button']['border-radius'].substring(0, selectedTemplate['accept_button']['border-radius'].length - 2);
      this.cookie_decline_on2 =                      selectedTemplate['decline_button']['is_on'];
      this.decline_as_button2 =                      true;
      this.decline_text_color2 =                     selectedTemplate['decline_button']['color'];
      this.decline_background_color2 =               selectedTemplate['decline_button']['background-color'];
      this.decline_style2 =                          selectedTemplate['decline_button']['border-style'];
      this.decline_border_color2 =                   selectedTemplate['decline_button']['border-color'];
      this.decline_opacity2 =                        selectedTemplate['decline_button']['opacity'];
      this.decline_border_width2 =                   selectedTemplate['decline_button']['border-width'].substring(0, selectedTemplate['decline_button']['border-width'].length - 2);
      this.decline_border_radius2 =                  selectedTemplate['decline_button']['border-radius'].substring(0, selectedTemplate['decline_button']['border-radius'].length - 2);
      this.cookie_accept_all_on2 =                   selectedTemplate['accept_all_button']['is_on'];
      this.accept_all_as_button2 =                   true;
      this.accept_all_text_color2 =                  selectedTemplate['accept_all_button']['color'];
      this.accept_all_background_color2 =            selectedTemplate['accept_all_button']['background-color'];
      this.accept_all_style2 =                       selectedTemplate['accept_all_button']['border-style'];
      this.accept_all_border_color2 =                selectedTemplate['accept_all_button']['border-color'];
      this.accept_all_opacity2 =                     selectedTemplate['accept_all_button']['opacity'];
      this.accept_all_border_width2 =                selectedTemplate['accept_all_button']['border-width'].substring(0, selectedTemplate['accept_all_button']['border-width'].length - 2);
      this.accept_all_border_radius2 =               selectedTemplate['accept_all_button']['border-radius'].substring(0, selectedTemplate['accept_all_button']['border-radius'].length - 2);
      this.cookie_settings_on2 =                     selectedTemplate['settings_button']['is_on'];
      this.settings_as_button2 =                     true;
      this.settings_text_color2 =                    selectedTemplate['settings_button']['color'];
      this.settings_background_color2 =              selectedTemplate['settings_button']['background-color'];
      this.settings_style2 =                         selectedTemplate['settings_button']['border-style'];
      this.settings_border_color2 =                  selectedTemplate['settings_button']['border-color'];
      this.settings_opacity2 =                       selectedTemplate['settings_button']['opacity'];
      this.settings_border_width2 =                  selectedTemplate['settings_button']['border-width'].substring(0, selectedTemplate['settings_button']['border-width'].length - 2);
      this.settings_border_radius2 =                 selectedTemplate['settings_button']['border-radius'].substring(0, selectedTemplate['settings_button']['border-radius'].length - 2);
      this.opt_out_text_color2 =                     selectedTemplate['opt_out_button']['color'];
      this.button_readmore_link_color2 =             selectedTemplate['readmore_button']['color'];
      this.button_readmore_button_color2 =           selectedTemplate['readmore_button']['background-color'];
      this.button_readmore_button_opacity2 =         selectedTemplate['readmore_button']['opacity'];
      this.button_readmore_button_border_style2 =    selectedTemplate['readmore_button']['border-style'];
      this.button_readmore_button_border_color2 =    selectedTemplate['readmore_button']['border-color'];
      this.button_readmore_button_border_radius2 =   selectedTemplate['readmore_button']['border-radius'].substring(0, selectedTemplate['readmore_button']['border-radius'].length - 2);
      this.button_readmore_button_border_width2 =    selectedTemplate['readmore_button']['border-width'].substring(0, selectedTemplate['readmore_button']['border-width'].length - 2);
      this.button_revoke_consent_text_color2 =       selectedTemplate['revoke_consent_button']['color'];
      this.button_revoke_consent_background_color2 = selectedTemplate['revoke_consent_button']['background-color'];

      // Multiple Legislation
      this.multiple_legislation_cookie_bar_color1 =         selectedTemplate["styles"]["background-color"];
      this.multiple_legislation_cookie_bar_border_radius1 = selectedTemplate['styles']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.multiple_legislation_cookie_text_color1 =        selectedTemplate['styles']['color'];
      this.multiple_legislation_cookie_bar_opacity1 =       selectedTemplate['styles']['opacity'];
      this.multiple_legislation_border_style1 =             selectedTemplate['styles']['border-style'];
      this.multiple_legislation_cookie_bar_border_width1 =  selectedTemplate['styles']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.multiple_legislation_cookie_border_color1 =      selectedTemplate['styles']['border-color'];
      this.multiple_legislation_cookie_font1 =              selectedTemplate['styles']['font-family'];

      this.multiple_legislation_cookie_bar_color2 =         selectedTemplate["styles"]["background-color"];
      this.multiple_legislation_cookie_bar_border_radius2 = selectedTemplate['styles']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.multiple_legislation_cookie_text_color2 =        selectedTemplate['styles']['color'];
      this.multiple_legislation_cookie_bar_opacity2 =       selectedTemplate['styles']['opacity'];
      this.multiple_legislation_border_style2 =             selectedTemplate['styles']['border-style'];
      this.multiple_legislation_cookie_bar_border_width2 =  selectedTemplate['styles']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.multiple_legislation_cookie_border_color2 =      selectedTemplate['styles']['border-color'];
      this.multiple_legislation_cookie_font2 =              selectedTemplate['styles']['font-family'];

      //CCPA popup buttons
      this.confirm_button_popup =                     selectedTemplate['accept_all_button']['is_on'];
      this.confirm_text_color =                       selectedTemplate['accept_all_button']['color'];
      this.confirm_background_color =                 selectedTemplate['accept_all_button']['background-color'];
      this.confirm_opacity =                          selectedTemplate['accept_all_button']['opacity'];
      this.confirm_style =                            selectedTemplate['accept_all_button']['border-style'];
      this.confirm_border_color =                     selectedTemplate['accept_all_button']['border-color'];
      this.confirm_border_radius =                    selectedTemplate['accept_all_button']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.confirm_border_width =                     selectedTemplate['accept_all_button']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.cancel_button_popup =                      selectedTemplate['decline_button']['is_on'];
      this.cancel_text_color =                        selectedTemplate['decline_button']['color'];
      this.cancel_background_color =                  selectedTemplate['decline_button']['background-color'];
      this.cancel_opacity =                           selectedTemplate['decline_button']['opacity'];
      this.cancel_style =                             selectedTemplate['decline_button']['border-style'];
      this.cancel_border_color =                      selectedTemplate['decline_button']['border-color'];
      this.cancel_border_radius =                     selectedTemplate['decline_button']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.cancel_border_width =                      selectedTemplate['decline_button']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);

      this.confirm_button_popup1 =                     selectedTemplate['accept_all_button']['is_on'];
      this.confirm_text_color1 =                       selectedTemplate['accept_all_button']['color'];
      this.confirm_background_color1 =                 selectedTemplate['accept_all_button']['background-color'];
      this.confirm_opacity1 =                          selectedTemplate['accept_all_button']['opacity'];
      this.confirm_style1 =                            selectedTemplate['accept_all_button']['border-style'];
      this.confirm_border_color1 =                     selectedTemplate['accept_all_button']['border-color'];
      this.confirm_border_radius1 =                    selectedTemplate['accept_all_button']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.confirm_border_width1 =                     selectedTemplate['accept_all_button']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.cancel_button_popup1 =                      selectedTemplate['decline_button']['is_on'];
      this.cancel_text_color1 =                        selectedTemplate['decline_button']['color'];
      this.cancel_background_color1 =                  selectedTemplate['decline_button']['background-color'];
      this.cancel_opacity1 =                           selectedTemplate['decline_button']['opacity'];
      this.cancel_style1 =                             selectedTemplate['decline_button']['border-style'];
      this.cancel_border_color1 =                      selectedTemplate['decline_button']['border-color'];
      this.cancel_border_radius1 =                     selectedTemplate['decline_button']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.cancel_border_width1 =                      selectedTemplate['decline_button']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);

      this.confirm_button_popup2 =                     selectedTemplate['accept_all_button']['is_on'];
      this.confirm_text_color2 =                       selectedTemplate['accept_all_button']['color'];
      this.confirm_background_color2 =                 selectedTemplate['accept_all_button']['background-color'];
      this.confirm_opacity2 =                          selectedTemplate['accept_all_button']['opacity'];
      this.confirm_style2 =                            selectedTemplate['accept_all_button']['border-style'];
      this.confirm_border_color2 =                     selectedTemplate['accept_all_button']['border-color'];
      this.confirm_border_radius2 =                    selectedTemplate['accept_all_button']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.confirm_border_width2 =                     selectedTemplate['accept_all_button']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);
      this.cancel_button_popup2 =                      selectedTemplate['decline_button']['is_on'];
      this.cancel_text_color2 =                        selectedTemplate['decline_button']['color'];
      this.cancel_background_color2 =                  selectedTemplate['decline_button']['background-color'];
      this.cancel_opacity2 =                           selectedTemplate['decline_button']['opacity'];
      this.cancel_style2 =                             selectedTemplate['decline_button']['border-style'];
      this.cancel_border_color2 =                      selectedTemplate['decline_button']['border-color'];
      this.cancel_border_radius2 =                     selectedTemplate['decline_button']['border-radius'].substring(0, selectedTemplate['styles']['border-radius'].length - 2);
      this.cancel_border_width2 =                      selectedTemplate['decline_button']['border-width'].substring(0, selectedTemplate['styles']['border-width'].length - 2);

      this.is_template_changed = true;
    },
    onLanguageChange() {
      this.is_lang_changed = true;
    },
    onSwitchButtonReadMoreIsOn() {
      this.button_readmore_is_on = !this.button_readmore_is_on;
    },
    onSwitchButtonReadMoreIsOn1() {
      this.button_readmore_is_on1 = !this.button_readmore_is_on1;
    },
    onSwitchButtonReadMoreIsOn2() {
      this.button_readmore_is_on2 = !this.button_readmore_is_on2;
    },
    onSwitchButtonReadMoreWpPage() {
      this.button_readmore_wp_page = !this.button_readmore_wp_page;
    },
    onSwitchButtonReadMoreWpPage1() {
      this.button_readmore_wp_page1 = !this.button_readmore_wp_page1;
    },
    onSwitchButtonReadMoreWpPage2() {
      this.button_readmore_wp_page2 = !this.button_readmore_wp_page2;
    },
    onSwitchButtonReadMoreNewWin() {
      this.button_readmore_new_win = !this.button_readmore_new_win;
    },
    onSwitchButtonReadMoreNewWin1() {
      this.button_readmore_new_win1 = !this.button_readmore_new_win1;
    },
    onSwitchButtonReadMoreNewWin2() {
      this.button_readmore_new_win2 = !this.button_readmore_new_win2;
    },
    onSwitchingScriptBlocker() {
      this.is_script_blocker_on = !this.is_script_blocker_on;
    },
    onPostsSelect(value) {
      let temp_array = [];
      for (let i = 0; i < value.length; i++) {
        temp_array[i] = value[i];
      }
      this.restrict_posts = temp_array;
    },
    onPageSelect(value) {
      let dummy_array = [];
      for (let i = 0; i < value.length; i++) {
        dummy_array[i] = value[i];
      }
      this.select_pages = dummy_array;
    },
    onSwitchingScriptDependency() {
      this.is_script_dependency_on = !this.is_script_dependency_on;

      if( this.is_script_dependency_on === false ){
        this.header_dependency = null;
        this.footer_dependency = null;
      }
    },
    onHeaderDependencySelect(value) {
      
      this.header_dependency_map.body = false;
      this.header_dependency_map.footer = false;

      if (this.header_dependency) {
        this.header_dependency_map[this.header_dependency] = true;
        this.header_dependency = this.header_dependency;
      } else {
        this.header_dependency = '';
      }
    },
    onFooterDependencySelect(value) {
      
      this.footer_dependency_map.header = false;
      this.footer_dependency_map.body = false;

      if (this.footer_dependency) {
        this.footer_dependency_map[this.footer_dependency] = true;
        this.footer_dependency = this.footer_dependency;
      } else {
        this.footer_dependency = '';
      }
    },
    onSelectPrivacyPage(value) {
      this.button_readmore_page = value;
    },
    onSelectPrivacyPage1(value) {
      this.button_readmore_page1 = value;
    },
    onSelectPrivacyPage2(value) {
      this.button_readmore_page2 = value;
    },
    cookiePolicyChange(value) {
      this.onSwitchReloadLaw();
      if (this.gdpr_policy) {
        value = this.gdpr_policy;
      }
      if (value === "both") {
        this.is_ccpa = true;
        this.is_gdpr = true;
        this.is_eprivacy = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = true;
        this.show_revoke_card = true;
        //visitors condition.
        this.selectedRadioWorldWide = "yes";
        this.selectedRadioWorldWideCcpa = "yes";
        this.is_worldwide_on = true;
        this.is_worldwide_on_ccpa = true;
        this.is_eu_on = false;
        this.is_ccpa_on = false;
        this.selectedRadioCountry = false;
        this.selectedRadioCountryCcpa = false;
        this.is_selectedCountry_on = false;
        this.is_selectedCountry_on_ccpa = false;
      } else if (value === "ccpa") {
        this.is_ccpa = true;
        this.is_eprivacy = false;
        this.is_gdpr = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = true;
        this.show_revoke_card = false;
        this.iabtcf_is_on = false;
        this.gcm_is_on = false;
        //visitors condition.
        this.selectedRadioWorldWide = "yes";
        this.selectedRadioWorldWideCcpa = "yes";
        this.is_worldwide_on = true;
        this.is_worldwide_on_ccpa = true;
        this.is_eu_on = false;
        this.is_ccpa_on = false;
        this.selectedRadioCountry = false;
        this.selectedRadioCountryCcpa = false;
        this.is_selectedCountry_on = false;
        this.is_selectedCountry_on_ccpa = false;
      } else if (value === "gdpr") {
        this.is_gdpr = true;
        this.is_ccpa = false;
        this.is_eprivacy = false;
        this.is_lgpd = false;
        this.show_revoke_card = true;
        this.show_visitor_conditions = true;
        this.selectedRadioWorldWide = "yes";
        this.selectedRadioWorldWideCcpa = "yes";
        this.is_worldwide_on = true;
        this.is_worldwide_on_ccpa = true;
        this.is_eu_on = false;
        this.is_ccpa_on = false;
        this.selectedRadioCountry = false;
        this.selectedRadioCountryCcpa = false;
        this.is_selectedCountry_on = false;
        this.is_selectedCountry_on_ccpa = false;
      } else if (value === "lgpd") {
        this.is_ccpa = false;
        this.is_eprivacy = false;
        this.is_gdpr = false;
        this.is_lgpd = true;
        this.show_revoke_card = true;
        this.show_visitor_conditions = true;
        this.iabtcf_is_on = false;
      } else {
        this.is_eprivacy = true;
        this.is_gdpr = false;
        this.is_ccpa = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = false;
        this.show_revoke_card = true;
        this.iabtcf_is_on = false;
      }
    },
    onSwitchAddOverlay() {
      this.cookie_add_overlay = !this.cookie_add_overlay;
    },
    onSwitchCookieDeclineEnable() {
      this.cookie_decline_on = !this.cookie_decline_on;
    },
    onSwitchCookieSettingsEnable() {
      this.cookie_settings_on = !this.cookie_settings_on;
    },
    onSwitchCookieOnFrontend() {
      this.cookie_on_frontend = !this.cookie_on_frontend;
    },
    showScriptBlockerForm() {
      this.show_script_blocker = !this.show_script_blocker;
    },
    onSelectCustomCookieType(value) {
      if (value !== "HTTP") {
        this.is_custom_cookie_duration_disabled = true;
        this.custom_cookie_duration = "Persistent";
      } else {
        this.is_custom_cookie_duration_disabled = false;
        this.custom_cookie_duration = "";
      }
    },
    showCustomCookieAddForm() {
      this.show_custom_form = true;
      this.show_add_custom_button = !this.show_add_custom_button;
    },
    onUpdateScannedCookieCategory(value) {
      const id = value.split(",")[1];
      const cat = value.split(",")[0];
      for (let i = 0; i < this.scan_cookie_list_length; i++) {
        if (this.scan_cookie_list[i]["id_wpl_cookie_scan_cookies"] === id) {
          for (let j = 0; i < this.custom_cookie_categories.length; j++) {
            if (this.custom_cookie_categories[j]["code"] === parseInt(cat)) {
              this.scan_cookie_list[i]["category_id"] =
                this.custom_cookie_categories[j].code;
              this.scan_cookie_list[i]["category"] =
                this.custom_cookie_categories[j].label;
              break;
            }
          }
          break;
        }
      }
    },
    updateScannedCookies() {
      var cookie_scan_arr = [];
      for (let i = 0; i < this.scan_cookie_list_length; i++) {
        var cid = this.scan_cookie_list[i]["id_wpl_cookie_scan_cookies"];
        var ccategory = this.scan_cookie_list[i]["category_id"];
        var cdesc = this.scan_cookie_list[i]["description"];
        var cookie_arr = {
          cid: cid,
          ccategory: ccategory,
          cdesc: cdesc,
        };
        cookie_scan_arr.push(cookie_arr);
      }
      this.updateScanCookie(cookie_scan_arr);
    },
    scheduleScanShow() {
      this.schedule_scan_show = true;
    },
    scheduleScanHide() {
      this.schedule_scan_show = false;
    },
    scanTypeChange(value) {
      this.schedule_scan_as = value;
    },
    scanTimeChange(value) {
      this.schedule_scan_time_value = value;
    },
    scanDateChange(value) {
      this.schedule_scan_date = value;
    },
    scanDayChange(value) {
      this.schedule_scan_day = value;
    },
    updateScanCookie(cookie_arr) {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "update_scan_cookie",
        cookie_arr: cookie_arr,
      };
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response === true) {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            that.showScanCookieList();
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    showScanCookieList() {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "get_scanned_cookies_list",
      };
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response) {
            that.scan_cookie_list_length = data.total;
            that.scan_cookie_list = data.data;
            that.setScanListValues();
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    onSelectUpdateCookieCategory(value) {
      const id = value.split(",")[1];
      const cat = value.split(",")[0];
      for (let i = 0; i < this.post_cookie_list_length; i++) {
        if (this.post_cookie_list[i]["id_gdpr_cookie_post_cookies"] === id) {
          for (let j = 0; i < this.custom_cookie_categories.length; j++) {
            if (this.custom_cookie_categories[j]["code"] === parseInt(cat)) {
              this.post_cookie_list[i]["category_id"] =
                this.custom_cookie_categories[j].code;
              this.post_cookie_list[i]["category"] =
                this.custom_cookie_categories[j].label;
              break;
            }
          }
          break;
        }
      }
    },
    onSelectUpdateCookieType(value) {
      const id = value.split(",")[1];
      const type_id = value.split(",")[0];
      for (let i = 0; i < this.post_cookie_list_length; i++) {
        if (this.post_cookie_list[i]["id_gdpr_cookie_post_cookies"] === id) {
          if (type_id !== "HTTP") {
            this.post_cookie_list[i]["enable_duration"] = true;
            this.post_cookie_list[i]["duration"] = "Persistent";
          } else {
            this.post_cookie_list[i]["enable_duration"] = false;
            this.post_cookie_list[i]["duration"] = "";
          }
          for (let j = 0; i < this.custom_cookie_types.length; j++) {
            if (this.custom_cookie_types[j]["code"] === type_id) {
              this.post_cookie_list[i]["type"] =
                this.custom_cookie_types[j].code;
              this.post_cookie_list[i]["type_name"] =
                this.custom_cookie_types[j].label;
              break;
            }
          }
          break;
        }
      }
    },
    onDeleteCustomCookie(cookie_id) {
      this.deletePostCookie(cookie_id);
    },
    onClickRestoreButton() {
      let answer = confirm(
        "Are you sure you want to reset to default settings?"
      );
      if (answer) {
        this.restoreDefaultSettings();
      }
    },
    updateFileName(event) {
      this.selectedFile = event.target.files[0];
    },
    removeFile() {
      this.selectedFile = null;
      document.getElementById("fileInput").value = "";
    },
    exportsettings() {
      const siteAddress = window.location.origin;

      // Make an AJAX request to fetch data from the custom endpoint
      fetch(siteAddress + "/wp-json/custom/v1/gdpr-data/")
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then((data) => {
          // Process the fetched data

          // Create a copy of the settings object
          const settingsCopy = { ...data };

          // Check if gdpr_text_css is not empty
          if (settingsCopy.gdpr_text_css !== "") {
            const text_css = settingsCopy.gdpr_css_text;

            // Decode the gdpr_text_css property before exporting
            const final_css = text_css.replace(/\\r\\n/g, "\n");
            settingsCopy.gdpr_css_text = final_css;
          }

          // Convert the settings object to JSON with indentation
          const settingsJSON = JSON.stringify(
            JSON.stringify(settingsCopy, null, 2)
          );

          // Create a Blob containing the JSON data
          const blob = new Blob([settingsJSON], { type: "application/json" });

          // Create a download link for the Blob
          const url = URL.createObjectURL(blob);
          const a = document.createElement("a");
          a.href = url;
          a.download = "wpeka-banner-settings.json";

          // Trigger a click on the link to initiate the download
          a.click();

          // Release the object URL to free up resources
          URL.revokeObjectURL(url);
        })
        .catch((error) => {
          console.error("There was a problem with the fetch operation:", error);
        });
    },
    importsettings() {
      var that = this;
      var fileInput = document.getElementById("fileInput");
      var file = fileInput.files[0];

      if (file) {
        var reader = new FileReader();

        reader.onload = function (event) {
          var jsonData = event.target.result;
          try {
            const parsedData = JSON.parse(JSON.parse(jsonData));
            var data = {
              action: "gcc_update_imported_settings",
              security: settings_obj.import_settings_nonce,
              settings: parsedData,
            };
            jQuery.ajax({
              url: settings_obj.ajaxurl,
              data: data,
              dataType: "json",
              type: "POST",
              success: function (data) {
                if (data.success === true) {
                  that.success_error_message =
                    "Settings imported successfully.";
                  j("#gdpr-cookie-consent-save-settings-alert").css(
                    "background-color",
                    "#72b85c"
                  );
                  j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                  j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                  window.location.reload();
                } else {
                  that.success_error_message = "Please try again.";
                  j("#gdpr-cookie-consent-save-settings-alert").css(
                    "background-color",
                    "#72b85c"
                  );
                  j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                  j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                }
              },
              error: function () {
                that.success_error_message = "Please try again.";
                j("#gdpr-cookie-consent-save-settings-alert").css(
                  "background-color",
                  "#72b85c"
                );
                j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
              },
            });
          } catch (e) {
            console.error("Error parsing JSON data:", e);
          }
        };

        reader.readAsText(file);
      } else {
        console.error("No file selected");
      }
    },
    restoreDefaultSettings() {
      this.cookie_bar_color = "#ffffff";
      this.cookie_bar_opacity = "1";
      this.cookie_bar_border_width = "0";
      this.border_style = "none";
      this.cookie_border_color = "#ffffff";
      this.cookie_bar_border_radius = "0";
      this.template = "default";
      this.accept_text = "Accept";
      this.accept_url = "#";
      this.accept_action = "#cookie_action_close_header";
      this.accept_text_color = "#ffffff";
      this.accept_background_color = "#18a300";
      this.open_url = false;
      this.accept_as_button = true;
      this.cookie_accept_on = true;
      this.accept_opacity = "1";
      this.accept_border_width = "0";
      this.accept_style = "none";
      this.accept_border_color = "#18a300";
      this.accept_border_radius = "0";
      this.accept_all_button_popup = false;
      this.accept_all_text = "Accept All";
      this.accept_all_url = "#";
      this.accept_all_action = "#cookie_action_close_header";
      this.accept_all_text_color = "#ffffff";
      this.accept_all_background_color = "#18a300";
      this.accept_all_new_win = false;
      this.accept_all_as_button = true;
      this.cookie_accept_all_on = false;
      this.accept_all_opacity = "1";
      this.accept_all_border_width = "0";
      this.accept_all_style = "none";
      this.accept_all_border_color = "#18a300";
      this.accept_all_border_radius = "0";
      this.button_readmore_text = "Read More";
      this.button_readmore_url = "#";
      this.button_readmore_link_color = "#359bf5";
      this.button_readmore_button_color = "#333333";
      this.button_readmore_new_win = false;
      this.button_readmore_as_button = false;
      this.button_readmore_is_on = true;
      this.button_readmore_url_type = true;
      this.button_readmore_wp_page = false;
      this.button_readmore_page = "0";
      this.button_readmore_button_opacity = "1";
      this.button_readmore_button_border_width = "0";
      this.button_readmore_button_border_style = "none";
      this.button_readmore_button_border_color = "#333333";
      this.button_readmore_button_border_radius = "0";
      
      this.button_readmore_text1 = "Read More";
      this.button_readmore_url1 = "#";
      this.button_readmore_link_color1 = "#359bf5";
      this.button_readmore_button_color1 = "#333333";
      this.button_readmore_new_win1 = false;
      this.button_readmore_as_button1 = false;
      this.button_readmore_button_size1 = "medium";
      this.button_readmore_is_on1 = true;
      this.button_readmore_url_type1 = true;
      this.button_readmore_wp_page1 = false;
      this.button_readmore_page1 = "0";
      this.button_readmore_button_opacity1 = "1";
      this.button_readmore_button_border_width1 = "0";
      this.button_readmore_button_border_style1 = "none";
      this.button_readmore_button_border_color1 = "#333333";
      this.button_readmore_button_border_radius1 = "0";

      this.button_readmore_text2 = "Read More";
      this.button_readmore_url2 = "#";
      this.button_readmore_link_color2 = "#359bf5";
      this.button_readmore_button_color2 = "#333333";
      this.button_readmore_new_win2 = false;
      this.button_readmore_as_button2 = false;
      this.button_readmore_button_size2 = "medium";
      this.button_readmore_is_on2 = true;
      this.button_readmore_url_type2 = true;
      this.button_readmore_wp_page2 = false;
      this.button_readmore_page2 = "0";
      this.button_readmore_button_opacity2 = "1";
      this.button_readmore_button_border_width2 = "0";
      this.button_readmore_button_border_style2 = "none";
      this.button_readmore_button_border_color2 = "#333333";
      this.button_readmore_button_border_radius2 = "0";
      
      this.iabtcf_is_on = false;
      this.gcm_is_on = false;
      this.gcm_wait_for_update_duration = '500';
      this.gcm_ads_redact = false;
      this.gcm_debug_mode = false;
      this.gcm_advertiser_mode = false;
      this.gcm_url_passthrough = false;
      this.gacm_is_on = false;
      this.decline_text = "Decline";
      this.decline_url = "#";
      this.decline_action = "#cookie_action_settings";
      this.decline_text_color = "#ffffff";
      this.decline_background_color = "#333333";
      this.open_decline_url = false;
      this.decline_as_button = true;
      this.cookie_decline_on = true;
      this.decline_opacity = "1";
      this.decline_border_width = "0";
      this.decline_style = "none";
      this.decline_border_color = "#333333";
      this.decline_border_radius = "0";
      this.settings_text = "Cookie Settings";
      this.settings_text_color = "#ffffff";
      this.settings_background_color = "#333333";
      this.settings_as_button = true;
      this.cookie_settings_on = true;
      this.cookie_on_frontend = true;
      this.settings_opacity = "1";
      this.settings_border_width = "0";
      this.settings_style = "none";
      this.settings_border_color = "#333333";
      this.settings_border_radius = "0";
      this.opt_out_text = "Do Not Sell My Personal Information";
      this.opt_out_text_color = "#359bf5";
      this.confirm_text = "Confirm";
      this.confirm_background_color = "#18a300";
      this.confirm_text_color = "#ffffff";
      this.confirm_opacity = "1";
      this.confirm_border_width = "0";
      this.confirm_style = "none";
      this.confirm_border_color = "#18a300";
      this.confirm_border_radius = "0";
      this.cancel_text = "Cancel";
      this.cancel_background_color = "#333333";
      this.cancel_text_color = "#ffffff";
      this.cancel_opacity = "1";
      this.cancel_border_width = "0";
      this.cancel_style = "none";
      this.cancel_border_color = "#333333";
      this.cancel_border_radius = "0";
      this.cookie_font = "inherit";
      this.cookie_is_on = true;
      this.is_eu_on = false;
      this.is_ccpa_on = false;
      this.is_iab_on = false;
      this.selectedRadioIab = "no";
      this.selectedRadioLgpd = "no";
      this.logging_on = true;
      this.show_credits = true;
      this.autotick = false;
      this.is_revoke_consent_on = true;
      this.is_revoke_consent_on1 = true;
      this.is_revoke_consent_on2 = true;
      this.is_script_blocker_on = false;
      this.auto_hide = false;
      this.auto_banner_initialize = false;
      this.auto_scroll = false;
      this.auto_click = false;
      this.auto_scroll_reload = false;
      this.accept_reload = false;
      this.decline_reload = false;
      this.delete_on_deactivation = false;
      this.tab_position = "right";
      this.tab_position1 = "right";
      this.tab_position2 = "right";
      this.tab_text = "Cookie Settings";
      this.tab_text1 = "Cookie Settings";
      this.tab_text2 = "Cookie Settings";
      this.tab_margin = "5";
      this.tab_margin1 = "5";
      this.tab_margin2 = "5";
      this.auto_hide_delay = "10000";
      this.auto_banner_initialize_delay = "10000";
      this.auto_scroll_offset = "10";
      this.cookie_expiry = "365";
      this.on_hide = true;
      this.on_load = false;
      this.gdpr_message =
        "This website uses cookies to improve your experience. We'll assume you're ok with this, but you can opt-out if you wish.";
      this.lgpd_message =
        "This website uses cookies for technical and other purposes as specified in the cookie policy. We'll assume you're ok with this, but you can opt-out if you wish.";
      this.eprivacy_message =
        "This website uses cookies to improve your experience. We'll assume you're ok with this, but you can opt-out if you wish.";
      this.ccpa_message =
        "In case of sale of your personal information, you may opt out by using the link";
      this.ccpa_optout_message = "Do you really wish to opt-out?";
      this.cookie_position = "bottom";
      this.cookie_widget_position = "left";
      this.cookie_widget_position1 = "left";
      this.cookie_widget_position2 = "left";
      this.cookie_text_color = "#000000";
      this.gdpr_message_heading = "";
      this.lgpd_message_heading = "";
      this.show_cookie_as = "banner";
      this.gdpr_policy = "gdpr";
      this.cookie_add_overlay = true;
      this.gdpr_about_cookie_message =
        "Cookies are small text files that can be used by websites to make a user's experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.";
      this.lgpd_about_cookie_message =
        "Cookies are small text files that can be used by websites to make a user's experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.";
      this.header_scripts = "";
      this.body_scripts = "";
      this.footer_scripts = "";
      this.restrict_posts = [];
      // array for hide banner.
      this.select_pages = [];
      this.banner_preview_is_on = false;
      this.show_language_as = "en";
      this.gdpr_css_text = "";
      this.gdpr_css_text_free = "/*Your CSS here*/";
      this.do_not_track_on = false;
      this.data_reqs_on = true;
      this.data_req_email_address = "";
      this.data_req_subject = "We have received your request";
      // Script Dependency
      this.is_script_dependency_on = false;
      this.header_dependency = '';
      this.footer_dependency = '';
      var data = {
        action: "gcc_restore_default_settings",
        security: settings_obj.restore_settings_nonce,
      };
      var that = this;
      jQuery.ajax({
        url: settings_obj.ajaxurl,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.success === true) {
            that.success_error_message = "Settings reset to default";
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            location.reload();
          } else {
            that.success_error_message = "Please try again.";
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = "Please try again.";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#72b85c"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    saveCookieSettings() {
      this.save_loading = true;
      // When Pro is activated set the values in the aceeditor
      if (this.isGdprProActive) {
        //intializing the acecode editor
        var editor = ace.edit("aceEditor");
        //getting the value of editor
        var code = editor.getValue();
        //setting the value
        this.gdpr_css_text = code;
        editor.setValue(this.gdpr_css_text);
      }

      var that = this;
      var dataV = jQuery("#gcc-save-settings-form").serialize();
      jQuery
        .ajax({
          type: "POST",
          url: settings_obj.ajaxurl,
          data:
            dataV +
            "&action=gcc_save_admin_settings" +
            "&lang_changed=" +
            that.is_lang_changed +
            "&logo_removed=" +
            that.is_logo_removed +
            "&gdpr_css_text_field=" +
            that.gdpr_css_text,
        })
        .done(function (data) {
          that.success_error_message = "Settings Saved";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#72b85c"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          if (that.is_template_changed) {
            that.is_template_changed = false;
            location.reload();
          }
          if (that.is_lang_changed) {
            that.is_lang_changed = false;
            location.reload();
          }
          that.is_logo_removed = false;
          if (that.data_reqs_switch_clicked == true) {
            that.data_reqs_switch_clicked = false;
            location.reload();
          }
          if (that.consent_log_switch_clicked == true) {
            that.consent_log_switch_clicked = false;
            location.reload();
          }
          if (that.reload_onSelect_law == true) {
            that.reload_onSelect_law = false;
            location.reload();
          }
          if (that.reload_onSafeMode == true) {
            that.reload_onSafeMode = false;
            location.reload();
          }
          that.save_loading = false;
        })
        .fail(function () {
          that.save_loading = false;
        });
    },
    //method to save wizard form settings
    saveWizardCookieSettings() {
      var that = this;
      var dataV = jQuery(".gcc-save-wizard-settings-form").serialize();
      
      dataV += "&gcc-cookie-accept-enable=" + encodeURIComponent(that.cookie_accept_on);
      dataV += "&gcc-cookie-accept-all-enable=" + encodeURIComponent(that.cookie_accept_all_on);
      dataV += "&gcc-cookie-decline-enable=" + encodeURIComponent(that.cookie_decline_on);
      dataV += "&gcc-cookie-settings-enable=" + encodeURIComponent(that.cookie_settings_on);
      dataV += "&gdpr-cookie-bar-color=" + encodeURIComponent(that.cookie_bar_color);
      dataV += "&gdpr-cookie-text-color=" + encodeURIComponent(that.cookie_text_color);
      dataV += "&gdpr-cookie-bar-opacity=" + encodeURIComponent(that.cookie_bar_opacity);
      dataV += "&gdpr-cookie-bar-border-width=" + encodeURIComponent(that.cookie_bar_border_width);
      dataV += "&gdpr-cookie-border-style=" + encodeURIComponent(that.border_style);
      dataV += "&gdpr-cookie-border-color=" + encodeURIComponent(that.cookie_border_color);
      dataV += "&gdpr-cookie-bar-border-radius=" + encodeURIComponent(that.cookie_bar_border_radius);
      dataV += "&gdpr-cookie-font=" + encodeURIComponent(that.cookie_font);
      dataV += "&gdpr-cookie-accept-background-color=" + encodeURIComponent(that.accept_background_color);
      dataV += "&gdpr-cookie-accept-border-color=" + encodeURIComponent(that.accept_border_color);
      dataV += "&gdpr-cookie-decline-text-color=" + encodeURIComponent(that.decline_text_color);
      dataV += "&gdpr-cookie-decline-border-color=" + encodeURIComponent(that.decline_border_color);
      dataV += "&gdpr-cookie-settings-text-color=" + encodeURIComponent(that.settings_text_color);
      dataV += "&gdpr-cookie-settings-border-color=" + encodeURIComponent(that.settings_border_color);
      dataV += "&gdpr-cookie-settings-background-color=" + encodeURIComponent(that.settings_background_color);
      dataV += "&gdpr-cookie-decline-background-color=" + encodeURIComponent(that.decline_background_color);
      dataV += "&gdpr-cookie-decline-border-style=" + encodeURIComponent(that.decline_style);
      dataV += "&gdpr-cookie-decline-border-width=" + encodeURIComponent(that.decline_border_width);
      dataV += "&gdpr-cookie-settings-border-style=" + encodeURIComponent(that.settings_style);
      dataV += "&gdpr-cookie-settings-border-width=" + encodeURIComponent(that.settings_border_width);
      dataV += "&gdpr-cookie-accept-opacity=" + encodeURIComponent(that.accept_opacity);
      dataV += "&gdpr-cookie-accept-border-style=" + encodeURIComponent(that.accept_style);
      dataV += "&gdpr-cookie-accept-border-width=" + encodeURIComponent(that.accept_border_width);
      dataV += "&gdpr-cookie-accept-border-radius=" + encodeURIComponent(that.accept_border_radius);
      dataV += "&gdpr-cookie-accept-text-color=" + encodeURIComponent(that.accept_text_color);
      dataV += "&gcc-readmore-link-color=" + encodeURIComponent(that.button_readmore_link_color);
      dataV += "&gcc-readmore-button-color=" + encodeURIComponent(that.button_readmore_button_color);
      dataV += "&gcc-readmore-button-opacity=" + encodeURIComponent(that.button_readmore_button_opacity);
      dataV += "&gcc-readmore-button-border-style=" + encodeURIComponent(that.button_readmore_button_border_style);
      dataV += "&gcc-readmore-button-border-width=" + encodeURIComponent(that.button_readmore_button_border_width);
      dataV += "&gcc-readmore-button-border-color=" + encodeURIComponent(that.button_readmore_button_border_color);
      dataV += "&gcc-readmore-button-border-radius=" + encodeURIComponent(that.button_readmore_button_border_radius);
      dataV += "&gdpr-cookie-decline-opacity=" + encodeURIComponent(that.decline_opacity);
      dataV += "&gdpr-cookie-decline-border-radius=" + encodeURIComponent(that.decline_border_radius);
      dataV += "&gdpr-cookie-settings-opacity=" + encodeURIComponent(that.settings_opacity);
      dataV += "&gdpr-cookie-settings-border-radius=" + encodeURIComponent(that.settings_border_radius);
      dataV += "&gdpr-cookie-confirm-text-color=" + encodeURIComponent(that.confirm_text_color);
      dataV += "&gdpr-cookie-confirm-background-color=" + encodeURIComponent(that.confirm_background_color);
      dataV += "&gdpr-cookie-confirm-opacity=" + encodeURIComponent(that.confirm_opacity);
      dataV += "&gdpr-cookie-confirm-border-style=" + encodeURIComponent(that.confirm_style);
      dataV += "&gdpr-cookie-confirm-border-color=" + encodeURIComponent(that.confirm_border_color);
      dataV += "&gdpr-cookie-confirm-border-width=" + encodeURIComponent(that.confirm_border_width);
      dataV += "&gdpr-cookie-confirm-border-radius=" + encodeURIComponent(that.confirm_border_radius);
      dataV += "&gdpr-cookie-cancel-text-color=" + encodeURIComponent(that.cancel_text_color);
      dataV += "&gdpr-cookie-cancel-background-color=" + encodeURIComponent(that.cancel_background_color);
      dataV += "&gdpr-cookie-cancel-opacity=" + encodeURIComponent(that.cancel_opacity);
      dataV += "&gdpr-cookie-cancel-border-style=" + encodeURIComponent(that.cancel_style);
      dataV += "&gdpr-cookie-cancel-border-color=" + encodeURIComponent(that.cancel_border_color);
      dataV += "&gdpr-cookie-cancel-border-width=" + encodeURIComponent(that.cancel_border_width);
      dataV += "&gdpr-cookie-cancel-border-radius=" + encodeURIComponent(that.cancel_border_radius);
      dataV += "&gdpr-cookie-opt-out-text-color=" + encodeURIComponent(that.opt_out_text_color);
      dataV += "&gdpr-cookie-accept-all-text-color=" + encodeURIComponent(that.accept_all_text_color);
      dataV += "&gdpr-cookie-accept-all-background-color=" + encodeURIComponent(that.accept_all_background_color);
      dataV += "&gdpr-cookie-accept-all-border-style=" + encodeURIComponent(that.accept_all_style);
      dataV += "&gdpr-cookie-accept-all-border-color=" + encodeURIComponent(that.accept_all_border_color);
      dataV += "&gdpr-cookie-accept-all-opacity=" + encodeURIComponent(that.accept_all_opacity);
      dataV += "&gdpr-cookie-accept-all-border-width=" + encodeURIComponent(that.accept_all_border_width);
      dataV += "&gdpr-cookie-accept-all-border-radius=" + encodeURIComponent(that.accept_all_border_radius);
      dataV += "&gcc-revoke-consent-text-color=" + encodeURIComponent(that.button_revoke_consent_text_color);
      dataV += "&gcc-revoke-consent-background-color=" + encodeURIComponent(that.button_revoke_consent_background_color);
      jQuery
        .ajax({
          type: "POST",
          url: settings_obj.ajaxurl,
          data: dataV + "&action=gcc_save_wizard_settings",
        })
        .done(function (data) {
          that.success_error_message = "Settings Saved";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#72b85c"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        });
    },
    onSwitchScriptBlocker(script_id) {
      j("#gdpr-cookie-consent-updating-settings-alert").fadeIn(200);
      j("#gdpr-cookie-consent-updating-settings-alert").fadeOut(2000);
      var that = this;
      this.scripts_list_data[script_id - 1]["script_status"] =
        !this.scripts_list_data[script_id - 1]["script_status"];
      var status = "1";
      if (this.scripts_list_data[script_id - 1]["script_status"]) {
        status = "1";
      } else {
        status = "0";
      }
      var data = {
        action: "wpl_script_blocker",
        security:
          settings_obj.script_blocker_settings.nonces.wpl_script_blocker,
        wpl_script_action: "update_script_status",
        status: status,
        id: script_id,
      };
      jQuery.ajax({
        url: settings_obj.script_blocker_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response === true) {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    onStartScheduleScan() {
      this.schedule_scan_show = false; //make it false to close the popup

      if (this.schedule_scan_as == "once") {
        //execute schedule scan once
        this.scheduleScanOnce();

        //set value for the Next Scan Details when Once
        const dateObject = new Date(this.schedule_scan_date);
        const formattedDate = dateObject.toLocaleDateString("en-US", {
          year: "numeric",
          month: "short",
          day: "numeric",
        });
        this.next_scan_is_when = formattedDate;
      } else if (this.schedule_scan_as == "monthly") {
        //execute scan schedule monthly
        this.scanMonthly();

        //set value for the Next Scan Details when Monthly

        // Get the day of the month when the scan should run
        const dayString = this.schedule_scan_day;
        const dayNumber = parseInt(dayString.replace("Day ", ""), 10);
        const targetDayOfMonth = dayNumber;

        // Assuming this.schedule_scan_day contains the day of the month (1 to 31)
        const dayOfMonth = targetDayOfMonth;

        if (isNaN(dayOfMonth) || dayOfMonth < 1 || dayOfMonth > 31) {
          console.error("Invalid day of the month:", dayOfMonth);
        } else {
          // Get the current date
          const currentDate = new Date();

          // Set the day of the month to the specified value
          currentDate.setDate(dayOfMonth);

          // Get the current year and month
          const currentYear = currentDate.getFullYear();
          const currentMonth = currentDate.getMonth();

          // Create a new date with the same day of the month, current year, and month
          const newDate = new Date(currentYear, currentMonth, dayOfMonth);

          // Format the date as needed (e.g., 'Oct 10 2023')
          const formattedDate = newDate.toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
          });
          this.next_scan_is_when = formattedDate;
        }
      } else if (this.schedule_scan_as == "never") {
        this.next_scan_is_when = "Not Scheduled";
      }
    },
    scheduleScanOnce() {
      // Define the date and time when you want the function to execute
      let targetDate = new Date(this.schedule_scan_date);

      // Parse the time entered by the user and handle both 12-hour and 24-hour formats
      const timeParts = this.schedule_scan_time_value.split(":");
      let hours = parseInt(timeParts[0], 10);
      const minutes = parseInt(timeParts[1], 10);

      // Check if the time is in 12-hour format (e.g., "01:03 AM")
      if (
        this.schedule_scan_time_value.toUpperCase().includes("PM") &&
        hours < 12
      ) {
        hours += 12;
      } else if (
        this.schedule_scan_time_value.toUpperCase().includes("AM") &&
        hours === 12
      ) {
        hours = 0;
      }

      // Set the hours and minutes in the target date
      targetDate.setHours(hours);
      targetDate.setMinutes(minutes);

      // Calculate the time difference between now and the target date
      const timeUntilExecution = targetDate - new Date();

      // Check if the target date is in the future
      if (timeUntilExecution > 0) {
        // Use setTimeout to delay the execution of scan
        setTimeout(() => {
          // start the scanning here
          this.onClickStartScan();
        }, timeUntilExecution);
      } else {
        // if the target date is in the past
        alert("Selected date is in the past. Please select a vaild date.");
        this.schedule_scan_show = true;
      }
    },
    scanMonthly() {
      // Get the day of the month when the scan should run
      const dayString = this.schedule_scan_day;
      const dayNumber = parseInt(dayString.replace("Day ", ""), 10);
      const targetDayOfMonth = dayNumber;

      if (
        isNaN(targetDayOfMonth) ||
        targetDayOfMonth <= 0 ||
        targetDayOfMonth > 31
      ) {
        alert("Invalid day of the month:", this.schedule_scan_day);
        return; // Exit if the day is invalid
      }

      // Define the time (hours and minutes)
      const timeParts = this.schedule_scan_time_value.split(":");
      let hours = parseInt(timeParts[0], 10);
      const minutes = parseInt(timeParts[1], 10);

      // Check if the time is in 12-hour format (e.g., "01:03 AM")
      if (
        this.schedule_scan_time_value.toUpperCase().includes("PM") &&
        hours < 12
      ) {
        hours += 12;
      } else if (
        this.schedule_scan_time_value.toUpperCase().includes("AM") &&
        hours === 12
      ) {
        hours = 0;
      }
      // Define a function to check and run the scan when the conditions are met
      const checkAndRunScan = () => {
        const currentDate = new Date();
        const currentDayOfMonth = currentDate.getDate();
        const currentHours = currentDate.getHours();
        const currentMinutes = currentDate.getMinutes();

        if (
          currentDayOfMonth === targetDayOfMonth &&
          currentHours === hours &&
          currentMinutes === minutes
        ) {
          // The conditions are met; execute the scan
          this.onClickStartScan();
        }
      };

      // Set an interval to check if the conditions for running the scan are met
      setInterval(checkAndRunScan, 60000);
    },
    onClickStartScan() {
      this.continue_scan = 1;
      this.doScan();
    },
    doScan() {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "check_api",
      };
      var scanbar = j(".wpl_scanbar");
      scanbar.html(
        '<span style="float:left; height:40px; line-height:40px;">' +
          settings_obj.cookie_scan_settings.labels.checking_api +
          '</span> <img src="' +
          settings_obj.cookie_scan_settings.loading_gif +
          '" style="display:inline-block;" />'
      );
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          scanbar.html("");
          if (data.response === true) {
            that.scanNow();
          } else {
            that.serverUnavailable(scanbar, data.message);
          }
        },
        error: function () {
          scanbar.html("");
          that.showErrorScreen(settings_obj.cookie_scan_settings.labels.error);
        },
      });
    },
    scanNow() {
      var html = this.makeHtml();
      var scanbar = j(".gdpr_scanbar");
      scanbar.html(html);
      j(".gdpr_scanbar_staypage").css({ display: "flex" }).show();
      this.attachScanStop();
      j(".gdpr_scanlog").css({ display: "block", opacity: 0 }).animate(
        {
          opacity: 1,
          height: "auto",
        },
        1000
      );
      this.takePages(0);
    },
    animateProgressBar(offset, total, msg) {
      var prgElm = j(".gdpr_progress_bar");
      var w = prgElm.width();
      var sp = 100 / total;
      var sw = w / total;
      var cw = sw * offset;
      var cp = sp * offset;

      cp = cp > 100 ? 100 : cp;
      cp = Math.floor(cp < 1 ? 1 : cp);

      cw = cw > w ? w : cw;
      cw = Math.floor(cw < 1 ? 1 : cw);
      j(".gdpr_progress_bar_inner")
        .stop(true, true)
        .animate({ width: cw + "px" }, 300, function () {
          j(".gdpr_progress_action_main").html(msg);
        })
        .html(cp + "%");
    },
    appendLogAnimate(data, offset) {
      var that = this;
      if (data.length > offset) {
        offset++;
        var speed = 300 / data.length;
        setTimeout(function () {
          that.appendLogAnimate(data, offset);
        }, speed);
      }
    },
    takePages(offset, limit, total, scan_id) {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "get_pages",
        offset: offset,
      };
      if (limit) {
        data["limit"] = limit;
      }
      if (total) {
        data["total"] = total;
      }
      if (scan_id) {
        data["scan_id"] = scan_id;
      }
      // fake progress.
      this.animateProgressBar(
        1,
        100,
        settings_obj.cookie_scan_settings.labels.finding
      );
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          that.scan_id = typeof data.scan_id != "undefined" ? data.scan_id : 0;
          if (that.continue_scan == 0) {
            return false;
          }
          if (typeof data.response != "undefined" && data.response === true) {
            that.appendLogAnimate(data.log, 0);
            var new_offset = parseInt(data.offset) + parseInt(data.limit);
            if (data.total - 1 > new_offset) {
              // substract 1 from total because of home page.
              that.takePages(new_offset, data.limit, data.total, data.scan_id);
            } else {
              j(".wpl_progress_action_main").html(
                settings_obj.cookie_scan_settings.labels.scanning
              );
              that.scanPages(data.scan_id, 0, data.total);
            }
          } else {
            that.showErrorScreen(
              settings_obj.cookie_scan_settings.labels.error
            );
          }
        },
        error: function () {
          if (that.continue_scan == 0) {
            return false;
          }
          that.showErrorScreen(settings_obj.cookie_scan_settings.labels.error);
        },
      });
    },
    scanPages(scan_id, offset, total) {
      var that = this;
      var scanbar = j(".gdpr_scanbar");
      this.pollCount = 0;
      var hash = Math.random().toString(36).replace("0.", "");
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "scan_pages",
        offset: offset,
        scan_id: scan_id,
        total: total,
        hash: hash,
      };
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          that.scan_id = typeof data.scan_id != "undefined" ? data.scan_id : 0;
          if (that.continue_scan == 0) {
            return false;
          }
          if (data.response == true) {
            that.getScanCookies(scan_id, offset, total, hash);
          } else {
            scanbar.html("");
            j(".wpl_scanbar_staypage").hide();
            that.serverUnavailable(scanbar, data.message);
          }
        },
        error: function () {
          var current = that;
          if (that.continue_scan == 0) {
            return false;
          }
          // error and retry function.

          that.animateProgressBar(
            offset,
            total,
            settings_obj.cookie_scan_settings.labels.retrying
          );
          setTimeout(function () {
            current.scanPages(scan_id, offset, total);
          }, 2000);
        },
      });
    },
    getScanCookies(scan_id, offset, total, hash) {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "get_post_scan_cookies",
        offset: offset,
        scan_id: scan_id,
        total: total,
        hash: hash,
      };
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response == true) {
            var prg_offset = parseInt(offset) + parseInt(data.total_scanned);
            var prg_msg =
              settings_obj.cookie_scan_settings.labels.scanning + " ";
            that.appendLogAnimate(data.log, 0);
            if (data.continue === true) {
              that.scanPages(data.scan_id, data.offset, data.total);
            } else {
              prg_msg = settings_obj.cookie_scan_settings.labels.finished;
              prg_msg +=
                " (" +
                settings_obj.cookie_scan_settings.labels.total_cookies_found +
                ": " +
                data.total_cookies +
                ")";
              that.showSuccessScreen(prg_msg, scan_id, 1);
            }
            that.animateProgressBar(prg_offset, total, prg_msg);
          } else {
            var current = that;
            if (that.pollCount < 10) {
              that.pollCount++;
              setTimeout(function () {
                current.getScanCookies(
                  data.scan_id,
                  data.offset,
                  data.total,
                  data.hash
                );
              }, 10000);
            } else {
              that.showErrorScreen("Something went wrong, please scan again");
            }
          }
        },
        error: function () {
          var current = that;
          if (that.continue_scan == 0) {
            return false;
          }
          if (that.pollCount < 10) {
            setTimeout(function () {
              that.getScanCookies(offset, scan_id, total, hash);
            }, 5000);
          } else {
            that.showErrorScreen("Something went wrong, please scan again");
          }
        },
      });
    },
    makeHtml() {
      return (
        '<div class="gdpr_scanlog">' +
        '<div class="gdpr_progress_bar">' +
        '<span class="gdpr_progress_bar_inner gdpr_progress_bar_inner_restructured">' +
        "</span>" +
        "</div>" +
        '<div class="gdpr_progress_action_main">' +
        settings_obj.cookie_scan_settings.labels.finding +
        "</div>" +
        '<div class="gdpr_scanlog_bar"><button type="button" class="pull-right gdpr_stop_scan">' +
        settings_obj.cookie_scan_settings.labels.stop +
        "</button></div>" +
        "</div>"
      );
    },
    attachScanStop() {
      var that = this;
      j(".gdpr_stop_scan").click(function () {
        that.stopScan();
      });
    },
    stopScan() {
      if (this.continue_scan == 0) {
        return false;
      }
      if (confirm(settings_obj.cookie_scan_settings.labels.ru_sure)) {
        this.continue_scan = 0;
        this.stoppingScan(this.scan_id);
      }
    },
    stoppingScan(scan_id) {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "stop_scan",
        scan_id: scan_id,
      };
      j(".gdpr_stop_scan")
        .html(settings_obj.cookie_scan_settings.labels.stoping)
        .css({ opacity: ".5" });
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          that.showSuccessScreen(
            settings_obj.cookie_scan_settings.labels.scanning_stopped,
            scan_id,
            data.total
          );
        },
        error: function () {
          // error function.
          that.showErrorScreen(settings_obj.cookie_scan_settings.labels.error);
        },
      });
    },
    serverUnavailable: function (elm, msg) {
      elm.html(
        '<div style="background:#ffffff; border:solid 1px #cccccc; color:#333333; padding:5px;">' +
          msg +
          "</div>"
      );
    },
    showErrorScreen: function (error_msg) {
      var html =
        '<button type="button" class="pull-right gdpr_scan_again" style="margin-left:5px;">' +
        settings_obj.cookie_scan_settings.labels.scan_again +
        "</button>";
      j(".gdpr_scanlog_bar").html(html);
      j(".gdpr_progress_action_main").html(error_msg);
      this.success_error_message = error_msg;
      j("#gdpr-cookie-consent-save-settings-alert").css(
        "background-color",
        "#e55353"
      );
      j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
      j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
      j(".gdpr_scanbar_staypage").hide();
      this.scanAgain();
    },
    showSuccessScreen(success_msg, scan_id, total) {
      var html =
        '<button type="button" class="pull-right gdpr_scan_again" style="margin-left:5px;">' +
        settings_obj.cookie_scan_settings.labels.scan_again +
        "</button>";
      html += '<span class="spinner" style="margin-top:5px"></span>';
      j(".gdpr_scanlog_bar").html(html);
      j(".gdpr_progress_action_main").html(success_msg);
      this.success_error_message = success_msg;
      j("#gdpr-cookie-consent-save-settings-alert").css(
        "background-color",
        "#72b85c"
      );
      j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
      j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
      j(".gdpr_scanbar_staypage").hide();
      this.showScanCookieList();
      this.scanAgain();
    },
    scanAgain() {
      var that = this;
      j(".gdpr_scan_again")
        .unbind("click")
        .click(function () {
          that.continue_scan = 1;
          that.scanNow();
        });
    },
    onScriptCategorySelect(values) {
      if (!values) {
        this.success_error_message = "You must select a category.";
        j("#gdpr-cookie-consent-save-settings-alert").css(
          "background-color",
          "#e55353"
        );
        j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
        j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        return false;
      }
      var that = this;
      var category_code = values.split(",")[0];
      var script_id = values.split(",")[1];
      for (let i = 0; i < this.category_list_options.length; i++) {
        if (this.category_list_options[i]["code"] === category_code) {
          if (
            this.scripts_list_data[script_id - 1]["script_category"] ===
            category_code
          ) {
            this.success_error_message = "Category updated successfully";
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            return false;
          } else {
            this.scripts_list_data[script_id - 1]["script_category"] =
              this.category_list_options[i].code;
            this.scripts_list_data[script_id - 1]["script_category_label"] =
              this.category_list_options[i].label;
            break;
          }
        }
      }
      var data = {
        action: "wpl_script_blocker",
        security:
          settings_obj.script_blocker_settings.nonces.wpl_script_blocker,
        wpl_script_action: "update_script_category",
        category: category_code,
        id: script_id,
      };
      jQuery.ajax({
        url: settings_obj.script_blocker_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response === true) {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    onSaveCustomCookie() {
      var parent = j(".gdpr-custom-save-cookie").parents(
        "div.gdpr-add-custom-cookie-form"
      );
      var gdpr_addcookie = parent.find('input[name="gdpr_addcookie"]').val();
      if (gdpr_addcookie == 1) {
        var pattern =
          /^((http|https):\/\/)?([a-zA-Z0-9_][-_a-zA-Z0-9]{0,62}\.)+([a-zA-Z0-9]{1,10})$/gm;
        var cname = this.custom_cookie_name;
        var cdomain = this.custom_cookie_domain;
        var cduration = this.custom_cookie_duration;
        var ccategory = this.custom_cookie_category;
        var ctype = this.custom_cookie_type;
        var cdesc = this.custom_cookie_description;
        if (!cname) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Name";
          parent
            .find('input[name="gdpr-cookie-consent-custom-cookie-name"]')
            .focus();
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          return false;
        }
        if (!cdomain) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Domain";
          parent
            .find('input[name="gdpr-cookie-consent-custom-cookie-domain"]')
            .focus();
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          return false;
        } else {
          if (!pattern.test(cdomain)) {
            this.success_error_message = "Cookie domain is not valid.";
            parent
              .find('input[name="gdpr-cookie-consent-custom-cookie-domain"]')
              .focus();
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            return false;
          }
        }
        if (!cduration) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Duration";
          parent
            .find('input[name="gdpr-cookie-consent-custom-cookie-duration"]')
            .focus();
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          return false;
        }
        if (!ctype) {
          this.success_error_message = "Please select a Cookie Type";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          return false;
        }
        if (!ccategory) {
          this.success_error_message = "Please select a Cookie Category";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          return false;
        }
        var cookie_arr = {
          cname: cname,
          cdomain: cdomain,
          cduration: cduration,
          ccategory: ccategory,
          ctype: ctype,
          cdesc: cdesc,
        };
        this.saveCustomPostCookies(cookie_arr);
      }
    },
    saveCookieUpdateSettings() {
      var cookie_post_arr = [];
      var error = false;
      for (let i = 0; i < this.post_cookie_list_length; i++) {
        var pattern =
          /^((http|https):\/\/)?([a-zA-Z0-9_][-_a-zA-Z0-9]{0,62}\.)+([a-zA-Z0-9]{1,10})$/gm;
        var cid = this.post_cookie_list[i]["id_gdpr_cookie_post_cookies"];
        var cname = this.post_cookie_list[i]["name"];
        var cdomain = this.post_cookie_list[i]["domain"];
        var cduration = this.post_cookie_list[i]["duration"];
        var ccategory = this.post_cookie_list[i]["category_id"];
        var ctype = this.post_cookie_list[i]["type"];
        var cdesc = this.post_cookie_list[i]["description"];
        if (!cname) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Name";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          error = true;
        }
        if (!cdomain) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Domain";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          error = true;
        } else {
          if (!pattern.test(cdomain)) {
            this.success_error_message = "Cookie Domain is not valid.";
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            error = true;
          }
        }
        if (!cduration) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Duration";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          error = true;
        }
        var cookie_arr = {
          cid: cid,
          cname: cname,
          cdomain: cdomain,
          cduration: cduration,
          ccategory: ccategory,
          ctype: ctype,
          cdesc: cdesc,
        };
        cookie_post_arr.push(cookie_arr);
      }
      if (error) {
        return false;
      }
      this.updatePostCookie(cookie_post_arr);
    },
    updatePostCookie: function (cookie_arr) {
      var that = this;
      var data = {
        action: "gdpr_cookie_custom",
        security: settings_obj.cookie_list_settings.nonces.gdpr_cookie_custom,
        gdpr_custom_action: "update_post_cookie",
        cookie_arr: cookie_arr,
      };
      j.ajax({
        url: settings_obj.cookie_list_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response === true) {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            that.getPostCookieList();
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    deletePostCookie(cookie_id) {
      var that = this;
      var data = {
        action: "gdpr_cookie_custom",
        security: settings_obj.cookie_list_settings.nonces.gdpr_cookie_custom,
        gdpr_custom_action: "delete_post_cookie",
        cookie_id: cookie_id,
      };
      j.ajax({
        url: settings_obj.cookie_list_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response === true) {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            that.getPostCookieList();
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    hideCookieForm() {
      this.custom_cookie_name = "";
      this.custom_cookie_domain = "";
      this.custom_cookie_description = "";
      this.custom_cookie_category = 1;
      this.custom_cookie_type = "HTTP";
      this.show_custom_form = false;
      this.show_add_custom_button = true;
      this.is_custom_cookie_duration_disabled = false;
      this.custom_cookie_duration = "";
    },
    saveCustomPostCookies(cookie_data) {
      var that = this;
      var data = {
        action: "gdpr_cookie_custom",
        security: settings_obj.cookie_list_settings.nonces.gdpr_cookie_custom,
        gdpr_custom_action: "save_post_cookie",
        cookie_arr: cookie_data,
      };
      j.ajax({
        url: settings_obj.cookie_list_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response === true) {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            that.hideCookieForm();
            that.getPostCookieList();
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    getPostCookieList: function () {
      var that = this;
      var data = {
        action: "gdpr_cookie_custom",
        security: settings_obj.cookie_list_settings.nonces.gdpr_cookie_custom,
        gdpr_custom_action: "get_post_cookies_list",
      };
      j.ajax({
        url: settings_obj.cookie_list_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response) {
            that.post_cookie_list_length = data.total;
            that.post_cookie_list = data.post_list;
            that.setPostListValues();
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
  },
  mounted() {
    j("#gdpr-before-mount").css("display", "none");
    this.setValues();
    this.setPostListValues();
    if (this.scan_cookie_list_length > 0) {
      this.setScanListValues();
    }
  },
  computed: {
    computedBackgroundColor() {
      const color = this.ab_testing_enabled
        ? this[`cookie_bar_color${this.active_test_banner_tab}`]
        : this.gdpr_policy === 'both'
          ? this.active_default_multiple_legislation === 'gdpr'
            ? this.multiple_legislation_cookie_bar_color1 
            : this.multiple_legislation_cookie_bar_color2
          : this.cookie_bar_color

      const opacity = this.ab_testing_enabled
        ? this[`cookie_bar_opacity${this.active_test_banner_tab}`]
        : this.gdpr_policy === 'both'
          ? this.active_default_multiple_legislation === 'gdpr'
            ? this.multiple_legislation_cookie_bar_opacity1
            : this.multiple_legislation_cookie_bar_opacity2
          : this.cookie_bar_opacity;

      const finalColor = color + Math.floor(opacity * 255).toString(16).toUpperCase();
      const acceptAllBGColor = this.ab_testing_enabled ? ( this.active_test_banner_tab === 1 ? this.accept_all_background_color1 : this.accept_all_background_color2 ) : this.accept_all_background_color;

      if( finalColor.toUpperCase().slice(0, -2) === acceptAllBGColor.toUpperCase() ) {
        if( this.ab_testing_enabled ){
          this.cookieSettingsPopupAccentColor = this.active_test_banner_tab === 1 ? this.accept_all_text_color1 : this.accept_all_text_color2;
        } else {
          this.cookieSettingsPopupAccentColor = this.accept_all_text_color;
        }
      } else {
        this.cookieSettingsPopupAccentColor =  acceptAllBGColor;
      }

      return finalColor;
    }
  },
  icons: { cilPencil, cilSettings, cilInfo, cibGoogleKeep },
});

var adv = new Vue({
  el: "#gdpr-cookie-consent-advanced-settings",
  data() {
    return {
      labelIcon: {},
      labelIconNew: {
        labelOn: "\u2713",
        labelOff: "\uD83D\uDD12",
      },
      save_loading: false,
      success_error_message: "",
      logging_on:
        settings_obj.the_options.hasOwnProperty("logging_on") &&
        (true === settings_obj.the_options["logging_on"] ||
          1 === settings_obj.the_options["logging_on"])
          ? true
          : false,
      consent_log_switch_clicked: false,
      gdpr_policy: settings_obj.the_options.hasOwnProperty("cookie_usage_for")
        ? settings_obj.the_options["cookie_usage_for"]
        : "gdpr",
      is_gdpr:
        this.gdpr_policy === "gdpr" || this.gdpr_policy === "both"
          ? true
          : false,
      is_ccpa:
        this.gdpr_policy === "ccpa" || this.gdpr_policy === "both"
          ? true
          : false,
      is_lgpd: this.gdpr_policy === "lgpd" ? true : false,
      is_eprivacy: this.gdpr_policy === "eprivacy" ? true : false,
      autotick:
        settings_obj.the_options.hasOwnProperty("is_ticked") &&
        (true === settings_obj.the_options["is_ticked"] ||
          1 === settings_obj.the_options["is_ticked"])
          ? true
          : false,
      show_revoke_card: this.is_gdpr || this.is_eprivacy,
      show_visitor_conditions:
        this.is_ccpa || (this.is_gdpr && "1" === settings_obj.is_pro_active)
          ? true
          : false,
      auto_hide:
        settings_obj.the_options.hasOwnProperty("auto_hide") &&
        (true === settings_obj.the_options["auto_hide"] ||
          1 === settings_obj.the_options["auto_hide"])
          ? true
          : false,
      auto_hide_delay: settings_obj.the_options.hasOwnProperty(
        "auto_hide_delay"
      )
        ? settings_obj.the_options["auto_hide_delay"]
        : "10000",
      auto_scroll:
        settings_obj.the_options.hasOwnProperty("auto_scroll") &&
        (true === settings_obj.the_options["auto_scroll"] ||
          1 === settings_obj.the_options["auto_scroll"])
          ? true
          : false,
      auto_scroll_offset: settings_obj.the_options.hasOwnProperty(
        "auto_scroll_offset"
      )
        ? settings_obj.the_options["auto_scroll_offset"]
        : "10",
      auto_click:
        settings_obj.the_options.hasOwnProperty("auto_click") &&
        (true === settings_obj.the_options["auto_click"] ||
          1 === settings_obj.the_options["auto_click"])
          ? true
          : false,
      auto_scroll_reload:
        settings_obj.the_options.hasOwnProperty("auto_scroll_reload") &&
        (true === settings_obj.the_options["auto_scroll_reload"] ||
          1 === settings_obj.the_options["auto_scroll_reload"])
          ? true
          : false,
      accept_reload:
        settings_obj.the_options.hasOwnProperty("accept_reload") &&
        (true === settings_obj.the_options["accept_reload"] ||
          1 === settings_obj.the_options["accept_reload"])
          ? true
          : false,
      decline_reload:
        settings_obj.the_options.hasOwnProperty("decline_reload") &&
        (true === settings_obj.the_options["decline_reload"] ||
          1 === settings_obj.the_options["decline_reload"])
          ? true
          : false,
      isGdprProActive: "1" === settings_obj.is_pro_active,
      do_not_track_on:
        "true" == settings_obj.the_options["do_not_track_on"] ||
        1 === settings_obj.the_options["do_not_track_on"]
          ? true
          : false,
      consent_forward:
        settings_obj.the_options.hasOwnProperty("consent_forward") &&
        (true === settings_obj.the_options["consent_forward"] ||
          1 === settings_obj.the_options["consent_forward"])
          ? true
          : false,
      list_of_sites: settings_obj.list_of_sites,
      select_sites: settings_obj.the_options.hasOwnProperty("select_sites")
        ? settings_obj.the_options["select_sites"]
        : [],
      select_sites_array: [],
      delete_on_deactivation:
        settings_obj.the_options.hasOwnProperty("delete_on_deactivation") &&
        (true === settings_obj.the_options["delete_on_deactivation"] ||
          1 === settings_obj.the_options["delete_on_deactivation"])
          ? true
          : false,
      show_credits:
        settings_obj.the_options.hasOwnProperty("show_credits") &&
        (true === settings_obj.the_options["show_credits"] ||
          1 === settings_obj.the_options["show_credits"])
          ? true
          : false,
      cookie_expiry_options: settings_obj.cookie_expiry_options,
      cookie_expiry: settings_obj.the_options.hasOwnProperty("cookie_expiry")
        ? settings_obj.the_options["cookie_expiry"]
        : "365",
      enable_safe:
        settings_obj.the_options.hasOwnProperty("enable_safe") &&
        ("true" === settings_obj.the_options["enable_safe"] ||
          1 === settings_obj.the_options["enable_safe"])
          ? true
          : false,
      is_worldwide_on:
        settings_obj.the_options.hasOwnProperty("is_worldwide_on") &&
        (true === settings_obj.the_options["is_worldwide_on"] ||
          1 === settings_obj.the_options["is_worldwide_on"])
          ? true
          : false,
      is_eu_on:
        settings_obj.the_options.hasOwnProperty("is_eu_on") &&
        (true === settings_obj.the_options["is_eu_on"] ||
          1 === settings_obj.the_options["is_eu_on"])
          ? true
          : false,
      selectedRadioCountry:
        settings_obj.the_options.hasOwnProperty("is_selectedCountry_on") &&
        (true === settings_obj.the_options["is_selectedCountry_on"] ||
          1 === settings_obj.the_options["is_selectedCountry_on"])
          ? true
          : false,
      is_ccpa_on:
        settings_obj.the_options.hasOwnProperty("is_ccpa_on") &&
        (true === settings_obj.the_options["is_ccpa_on"] ||
          1 === settings_obj.the_options["is_ccpa_on"])
          ? true
          : false,
      reload_onSafeMode: false,
      usage_data: settings_obj.hasOwnProperty("is_usage_tracking_allowed")
        ? ("true" === settings_obj["is_usage_tracking_allowed"] )
        : "false",
      selectedFile: "",
    };
  },
  methods: {
    setValues() {
      if (this.gdpr_policy === "both") {
        this.is_ccpa = true;
        this.is_gdpr = true;
        this.is_eprivacy = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = true;
        this.show_revoke_card = true;
      } else if (this.gdpr_policy === "ccpa") {
        this.is_ccpa = true;
        this.is_eprivacy = false;
        this.is_gdpr = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = true;
        this.show_revoke_card = false;
      } else if (this.gdpr_policy === "gdpr") {
        this.is_gdpr = true;
        this.is_ccpa = false;
        this.is_eprivacy = false;
        this.is_lgpd = false;
        this.show_revoke_card = true;
        this.show_visitor_conditions = true;
      } else if (this.gdpr_policy === "lgpd") {
        this.is_gdpr = false;
        this.is_ccpa = false;
        this.is_lgpd = true;
        this.is_eprivacy = false;
        this.show_revoke_card = true;
        this.show_visitor_conditions = false;
      } else {
        this.is_eprivacy = true;
        this.is_gdpr = false;
        this.is_ccpa = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = false;
        this.show_revoke_card = true;
      }

      if (this.list_of_sites && this.list_of_sites.length) {
        // multiple entries for the consent forward .
        for (let i = 0; i < this.list_of_sites.length; i++) {
          if (
            this.select_sites.includes(this.list_of_sites[i].code.toString())
          ) {
            this.select_sites_array.push(this.list_of_sites[i]);
          }
        }
      }
    },
    saveAdvancedCookieSettings() {
      console.log("Saving advanced cookie settings...");
      this.save_loading = true;
      
      var that = this;
      var dataV = jQuery("#gcc-save-advanced-settings-form").serialize();
      jQuery
        .ajax({
          type: "POST",
          url: settings_obj.ajaxurl,
          data: dataV + "&action=gcc_save_advanced_settings",
        })
        .done(function (data) {
          that.success_error_message = "Settings Saved.";
          console.log("Succcess error message: ", that.success_error_message);
          j("#gdpr-cookie-consent-save-settings-alert").css({
              "background-color": "#72b85c",
              "z-index": "10000",
          });
          console.log("style:", j("#gdpr-cookie-consent-save-settings-alert")[0].style.cssText);
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);

          if (that.consent_log_switch_clicked == true) {
            that.consent_log_switch_clicked = false;
            location.reload();
          }
          if (that.reload_onSafeMode == true) {
            that.reload_onSafeMode = false;
            location.reload();
          }
          that.save_loading = false;
        })
        .fail(function () {
          that.save_loading = false;
        });
    },
    onSwitchLoggingOn() {
      this.logging_on = !this.logging_on;
      this.consent_log_switch_clicked = true;
    },
    onSwitchAutotick() {
      this.autotick = !this.autotick;
    },
    onSwitchAutoHide() {
      this.auto_hide = !this.auto_hide;
    },
    onSwitchAutoScroll() {
      this.auto_scroll = !this.auto_scroll;
    },
    onSwitchAutoClick() {
      this.auto_click = !this.auto_click;
    },
    onSwitchAutoScrollReload() {
      this.auto_scroll_reload = !this.auto_scroll_reload;
    },
    onSwitchAcceptReload() {
      this.accept_reload = !this.accept_reload;
    },
    onSwitchDeclineReload() {
      this.decline_reload = !this.decline_reload;
    },
    onSwitchDntEnable() {
      this.do_not_track_on = !this.do_not_track_on;
    },
    onSwitchConsentForward() {
      this.consent_forward = !this.consent_forward;
    },
    onSiteSelect(value) {
      let tmp_array = [];
      for (let i = 0; i < value.length; i++) {
        tmp_array[i] = value[i];
      }
      this.select_sites = tmp_array;
    },
    onSwitchDeleteOnDeactivation() {
      this.delete_on_deactivation = !this.delete_on_deactivation;
    },
    onSwitchShowCredits() {
      this.show_credits = !this.show_credits;
    },
    onEnablesafeSwitch() {
      if (this.enable_safe === "true") {
        this.is_worldwide_on = true;
        this.is_eu_on = false;
        this.selectedRadioCountry = false;
      } else {
        this.is_worldwide_on = true;
        this.is_eu_on = false;
        this.selectedRadioCountry = false;
      }
    },
    onEnablesafeSwitchCCPA() {
      if (this.enable_safe === "true") {
        this.is_worldwide_on = true;
        this.is_ccpa_on = false;
        this.selectedRadioCountry = false;
      } else {
        this.is_worldwide_on = true;
        this.is_ccpa_on = false;
        this.selectedRadioCountry = false;
      }
    },
    onSwitchReloadSafeMode() {
      this.reload_onSafeMode = !this.reload_onSafeMode;
      this.reload_onSafeMode = true;
    },
    onSwitchEnableSafe() {
      this.onEnablesafeSwitch();
      this.onEnablesafeSwitchCCPA();
      this.onSwitchReloadSafeMode();
      this.enable_safe = !this.enable_safe;
    },
    onSwitchEnableUsageData() {
      this.usage_data = !this.usage_data;
    },
    onClickRestoreButton() {
      let answer = confirm(
        "Are you sure you want to reset to default settings?"
      );
      if (answer) {
        this.restoreDefaultSettings();
      }
    },
    removeFile() {
      this.selectedFile = null;
      document.getElementById("fileInput").value = "";
      document.getElementById("importButton").disabled = true;
      document
        .getElementById("importButton")
        .classList.add("disable-import-button");
    },
    updateFileName(event) {
      this.selectedFile = event.target.files[0];
      document.getElementById("importButton").disabled = false;
      document.getElementById("importButton").classList.remove("disabled");
      document
        .getElementById("importButton")
        .classList.remove("disable-import-button");
      document.getElementById("importButton").add("#importButton");
      document
        .getElementById("importButton")
        .classList.remove("disable-import-button");
      document.getElementById("importButton").remove("#importButton");
    },
    exportsettings() {
      const siteAddress = window.location.origin;

      // Make an AJAX request to fetch data from the custom endpoint
      fetch(siteAddress + "/wp-json/custom/v1/gdpr-data/")
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then((data) => {
          // Process the fetched data

          // Create a copy of the settings object
          const settingsCopy = { ...data };

          // Check if gdpr_text_css is not empty
          if (settingsCopy.gdpr_text_css !== "") {
            const text_css = settingsCopy.gdpr_css_text;

            // Decode the gdpr_text_css property before exporting
            const final_css = text_css.replace(/\\r\\n/g, "\n");
            settingsCopy.gdpr_css_text = final_css;
          }

          // Convert the settings object to JSON with indentation
          const settingsJSON = JSON.stringify(
            JSON.stringify(settingsCopy, null, 2)
          );

          // Create a Blob containing the JSON data
          const blob = new Blob([settingsJSON], { type: "application/json" });

          // Create a download link for the Blob
          const url = URL.createObjectURL(blob);
          const a = document.createElement("a");
          a.href = url;
          a.download = "wpeka-banner-settings.json";

          // Trigger a click on the link to initiate the download
          a.click();

          // Release the object URL to free up resources
          URL.revokeObjectURL(url);
        })
        .catch((error) => {
          console.error("There was a problem with the fetch operation:", error);
        });
    },
    importsettings() {
      var that = this;
      var fileInput = document.getElementById("fileInput");
      var file = fileInput.files[0];

      if (file) {
        var reader = new FileReader();
        document.getElementById("importButton").disabled = true;
        document
          .getElementById("importButton")
          .classList.add("disable-import-button");
        reader.onload = function (event) {
          var jsonData = event.target.result;
          try {
            const parsedData = JSON.parse(JSON.parse(jsonData));
            var data = {
              action: "gcc_update_imported_settings",
              security: settings_obj.import_settings_nonce,
              settings: parsedData,
            };
            jQuery.ajax({
              url: settings_obj.ajaxurl,
              data: data,
              dataType: "json",
              type: "POST",
              success: function (data) {
                if (data.success === true) {
                  setTimeout(function addsettings() {
                    window.location.reload();
                  }, 7000);

                  that.success_error_message =
                    "Your file has been imported successfully. Please click on the Save Changes button to make the changes.";
                  j("#gdpr-cookie-consent-save-settings-alert").css(
                    "background-color",
                    "#72b85c"
                  );
                  j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                  j("#gdpr-cookie-consent-save-settings-alert").fadeOut(7000);
                } else {
                  that.success_error_message = "Please try again.";
                  j("#gdpr-cookie-consent-save-settings-alert").css(
                    "background-color",
                    "#72b85c"
                  );
                  j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                  j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                }
              },
              error: function () {
                that.success_error_message = "Please try again.";
                j("#gdpr-cookie-consent-save-settings-alert").css(
                  "background-color",
                  "#72b85c"
                );
                j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
              },
            });
          } catch (e) {
            console.error("Error parsing JSON data:", e);
          }
        };

        reader.readAsText(file);
      } else {
        console.error("No file selected");
      }
    },
    restoreDefaultSettings(){
      this.logging_on = true;
      this.autotick = false;
      this.gdpr_policy = "gdpr";
      this.auto_hide = false;
      this.auto_hide_delay = "10000";
      this.auto_scroll = false;
      this.auto_scroll_offset = "10";
      this.auto_click = false;
      this.auto_scroll_reload = false;
      this.accept_reload = false;
      this.decline_reload = false;
      this.do_not_track_on = false;
      this.consent_forward = false;
      this.select_sites = [];
      this.delete_on_deactivation = false;
      this.show_credits = true;
      this.cookie_expiry = "365";
      this.is_worldwide_on = false;
      this.is_eu_on = false;
      this.selectedRadioCountry = false;
      this.is_ccpa_on = false;
    }
  },
  mounted() {
    j("#gdpr-before-mount").css("display", "none");
    this.setValues();
  }
});

var abt = new Vue({
  el: "#gdpr-cookie-consent-abtesting-settings",
  data() {
    return {
      labelIcon: {},
      labelIconNew: {
        labelOn: "\u2713",
        labelOff: "\uD83D\uDD12",
      },
      save_loading: false,
      success_error_message: "",
      ab_testing_data: '',
      account_connection: require("../admin/images/account_connection.svg"),
      gdpr_policy: settings_obj.the_options.hasOwnProperty("cookie_usage_for")
        ? settings_obj.the_options["cookie_usage_for"]
        : "gdpr",
      is_gdpr:
        this.gdpr_policy === "gdpr" || this.gdpr_policy === "both"
          ? true
          : false,
      is_ccpa:
        this.gdpr_policy === "ccpa" || this.gdpr_policy === "both"
          ? true
          : false,
      is_lgpd: this.gdpr_policy === "lgpd" ? true : false,
      is_eprivacy: this.gdpr_policy === "eprivacy" ? true : false,
      show_revoke_card: this.is_gdpr || this.is_eprivacy,
      show_visitor_conditions:
        this.is_ccpa || (this.is_gdpr && "1" === settings_obj.is_pro_active)
          ? true
          : false,
      ab_testing_enabled:
        settings_obj.ab_options.hasOwnProperty("ab_testing_enabled") &&
        (true === settings_obj.ab_options["ab_testing_enabled"] ||
          "true" === settings_obj.ab_options["ab_testing_enabled"])
          ? true
          : false,
      cookie_on_frontend1:
        settings_obj.the_options.hasOwnProperty(
          "button_settings_display_cookies1"
        ) &&
        (true ===
          settings_obj.the_options["button_settings_display_cookies1"] ||
          1 === settings_obj.the_options["button_settings_display_cookies1"] ||
          "true" ===
            settings_obj.the_options["button_settings_display_cookies1"])
          ? true
          : false,
      cookie_on_frontend2:
        settings_obj.the_options.hasOwnProperty(
          "button_settings_display_cookies2"
        ) &&
        (true ===
          settings_obj.the_options["button_settings_display_cookies2"] ||
          1 === settings_obj.the_options["button_settings_display_cookies2"] ||
          "true" ===
            settings_obj.the_options["button_settings_display_cookies2"])
          ? true
          : false,
      active_test_banner_tab:
        settings_obj.the_options.hasOwnProperty("default_cookie_bar") &&
        (true == settings_obj.the_options["default_cookie_bar"] ||
          "true" == settings_obj.the_options["default_cookie_bar"] ||
          1 == settings_obj.the_options["default_cookie_bar"])
          ? 1
          : 2,
      ab_testing_period: settings_obj.ab_options.hasOwnProperty(
        "ab_testing_period"
      )
        ? settings_obj.ab_options["ab_testing_period"]
        : "30",
      ab_testing_auto:
        settings_obj.ab_options.hasOwnProperty("ab_testing_auto") &&
        (true === settings_obj.ab_options["ab_testing_auto"] ||
          "true" === settings_obj.ab_options["ab_testing_auto"])
          ? true
          : false,
      default_cookie_bar:
        settings_obj.the_options.hasOwnProperty("default_cookie_bar") &&
        (true == settings_obj.the_options["default_cookie_bar"] ||
          "true" == settings_obj.the_options["default_cookie_bar"] ||
          1 == settings_obj.the_options["default_cookie_bar"])
          ? true
          : false,
    }
  },
  methods: {
    setValues() {
      if (this.gdpr_policy === "both") {
        this.is_ccpa = true;
        this.is_gdpr = true;
        this.is_eprivacy = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = true;
        this.show_revoke_card = true;
      } else if (this.gdpr_policy === "ccpa") {
        this.is_ccpa = true;
        this.is_eprivacy = false;
        this.is_gdpr = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = true;
        this.show_revoke_card = false;
      } else if (this.gdpr_policy === "gdpr") {
        this.is_gdpr = true;
        this.is_ccpa = false;
        this.is_eprivacy = false;
        this.is_lgpd = false;
        this.show_revoke_card = true;
        this.show_visitor_conditions = true;
      } else if (this.gdpr_policy === "lgpd") {
        this.is_gdpr = false;
        this.is_ccpa = false;
        this.is_lgpd = true;
        this.is_eprivacy = false;
        this.show_revoke_card = true;
        this.show_visitor_conditions = false;
      } else {
        this.is_eprivacy = true;
        this.is_gdpr = false;
        this.is_ccpa = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = false;
        this.show_revoke_card = true;
      }
    },
    refreshABTestingData(html) {
      console.log("DODODO Refreshing AB Testing Data for abt");
      this.ab_testing_data = html;
      const container = document.querySelector('#ab-testing-container');
      this.$nextTick(() => {
                new Vue({
                    el: container,
                    template: html,
                    data: this.$data, // Reuse the existing Vue instance data
                    methods: this.$options.methods, // Reuse the existing methods
                    mounted: this.$options.mounted, // Reuse the original mounted logic
                });
            });
    },
    onSwitchABTestingEnable() {
      j("#gdpr-cookie-consent-updating-settings-alert")
        .fadeIn(200)
        .fadeOut(2000);
      this.ab_testing_enabled = !this.ab_testing_enabled;
      this.cookie_on_frontend1 = true;
      this.cookie_on_frontend2 = true;
      if (this.ab_testing_enabled === false) this.active_test_banner_tab = 1;

      var dataV = jQuery("#gcc-save-settings-form").serialize();
      // Make the AJAX request to save the new state
      jQuery
        .ajax({
          type: "POST",
          url: settings_obj.ajaxurl,
          data: {
            action: "ab_testing_enable",
            "gcc-ab-testing-enable": this.ab_testing_enabled, // Add the key with the updated value
          },
        })
        .done(function (data) {
          window.location.reload();
          // Show success message
          this.success_error_message = "Settings Saved";
          j("#gdpr-cookie-consent-save-settings-alert")
            .css("background-color", "#72b85c")
            .fadeIn(400)
            .fadeOut(2500, function () {
              // Optionally reload the page or perform other actions
            });
        })
        .fail(function (error) {
          console.error("AJAX call failed:", error);
          alert(
            "An error occurred while saving the settings. Please try again."
          );
        });
    },
    onSwitchCookieOnFrontend1() {
      this.cookie_on_frontend1 = !this.cookie_on_frontend1;
    },
    onSwitchCookieOnFrontend2() {
      this.cookie_on_frontend2 = !this.cookie_on_frontend2;
    },
    changeActiveTestBannerTabTo1() {
      if (this.active_test_banner_tab === 2) this.active_test_banner_tab = 1;
    },
    changeActiveTestBannerTabTo2() {
      if (this.active_test_banner_tab === 1) this.active_test_banner_tab = 2;
    },
    onSwitchABTestingAuto() {
      this.ab_testing_auto = !this.ab_testing_auto;
    },
    saveABTestingSettings() {
      console.log("DODODO Saving AB Testing Settings...");
      this.save_loading = true;

      var that = this;
      var dataV = jQuery("#gcc-save-abtesting-settings-form").serialize();
      jQuery
        .ajax({
          type: "POST",
          url: settings_obj.ajaxurl,
          data: dataV + "&action=gcc_save_abtesting_settings",
        })
        .done(function (data) {
          that.success_error_message = "Settings Saved.";
          j("#gdpr-cookie-consent-save-settings-alert").css({
              "background-color": "#72b85c",
              "z-index": "10000",
          });
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);

          that.save_loading = false;
        })
        .fail(function () {
          that.save_loading = false;
        });
    },
    restoreDefaultSettings() {
      this.ab_testing_enabled = false;
      this.cookie_on_frontend1 = true;
      this.cookie_on_frontend2 = true;
      this.gdpr_policy = "gdpr";
      this.ab_testing_period = "30";
      this.ab_testing_auto = false;
    }
  },
  mounted() {
    j("#gdpr-before-mount").css("display", "none");
    this.setValues();

    //For fixing quill js buttons accessibility issues
    this.$nextTick(() => {
      const quillLabels = {
        "ql-bold": "Bold",
        "ql-italic": "Italic",
        "ql-underline": "Underline",
        "ql-code-block": "Code Block",
        "ql-strike": "Strikethrough",
        "ql-link": "Insert Link",
        "ql-image": "Insert Image",
        "ql-list": "List",
        "ql-clean": "Remove Formatting",
        "ql-align": "Align Text",
        "ql-blockquote": "Blockquote",
        "ql-indent": "Indent Text",
        "ql-video": "Insert Video",
        "ql-header": "Header",
        "ql-color": "Text Color",
        "ql-background": "Background Color",
        "ql-preview": "Preview",
      };

      Object.entries(quillLabels).forEach(([className, label]) => {
        const buttons = document.querySelectorAll(`.ql-toolbar .${className}`);
        buttons.forEach((button) => {
          button.setAttribute("aria-label", label);
          button.setAttribute("title", label);
        });
      });

      // Fix for Ace Editor’s textarea
      const observer = new MutationObserver(() => {
        const aceInput = document.querySelector(".ace_text-input");
        if (aceInput) {
          aceInput.setAttribute("aria-hidden", "true");
          aceInput.setAttribute("tabindex", "-1");
          aceInput.setAttribute("role", "presentation");
          aceInput.removeAttribute("aria-label"); // optional, but removes confusion
          aceInput.removeAttribute("title"); // in case any tooltips are there

          observer.disconnect();
        }
      });

      observer.observe(document.body, { childList: true, subtree: true });
      setTimeout(() => observer.disconnect(), 10000);

      // First: For ab_testing_period_text_field
      const abInterval = setInterval(() => {
        const inputs = document.querySelectorAll(
          'input[name="ab_testing_period_text_field"]'
        );

        inputs.forEach((input) => {
          if (
            !input.hasAttribute("aria-label") &&
            !input.hasAttribute("aria-labelledby")
          ) {
            input.setAttribute("aria-label", "A/B Testing Period");
          }
        });

        if (inputs.length) clearInterval(abInterval);
      }, 300);

      setTimeout(() => clearInterval(abInterval), 7000); // safety timeout

      // Second: For display-time inputs
      const timeInterval = setInterval(() => {
        const timeInputs = document.querySelectorAll("input.display-time");

        timeInputs.forEach((input) => {
          if (
            !input.hasAttribute("aria-label") &&
            !input.hasAttribute("aria-labelledby")
          ) {
            input.setAttribute("aria-label", "Choose time");
          }
        });

        if (timeInputs.length) clearInterval(timeInterval);
      }, 300);

      setTimeout(() => clearInterval(timeInterval), 7000);
    });
  },
});
window.abt = abt;

var scb = new Vue({
  el: "#gdpr-cookie-consent-script_blocker-settings",
  data() {
    return {
      labelIcon: {},
      labelIconNew: {
        labelOn: "\u2713",
        labelOff: "\uD83D\uDD12",
      },
      save_loading: false,
      success_error_message: "",
      gdpr_policy: settings_obj.the_options.hasOwnProperty("cookie_usage_for")
        ? settings_obj.the_options["cookie_usage_for"]
        : "gdpr",
      appendField: ".gdpr-cookie-consent-settings-container",
      show_revoke_card: this.is_gdpr || this.is_eprivacy,
      enable_safe:
        settings_obj.the_options.hasOwnProperty("enable_safe") &&
        ("true" === settings_obj.the_options["enable_safe"] ||
          1 === settings_obj.the_options["enable_safe"])
          ? true
          : false,
      is_script_blocker_on:
        settings_obj.the_options.hasOwnProperty("is_script_blocker_on") &&
        (true === settings_obj.the_options["is_script_blocker_on"] ||
          1 === settings_obj.the_options["is_script_blocker_on"])
          ? true
          : false,
      show_script_blocker: false,
      scripts_list_total: settings_obj.script_blocker_settings.hasOwnProperty(
        "scripts_list"
      )
        ? settings_obj.script_blocker_settings.scripts_list["total"]
        : 0,
      scripts_list_data: settings_obj.script_blocker_settings.hasOwnProperty(
        "scripts_list"
      )
        ? settings_obj.script_blocker_settings.scripts_list["data"]
        : [],
      category_list_options:
        settings_obj.script_blocker_settings.hasOwnProperty("category_list")
          ? settings_obj.script_blocker_settings["category_list"]
          : [],
      header_scripts: settings_obj.the_options.hasOwnProperty("header_scripts")
        ? this.stripSlashes(settings_obj.the_options["header_scripts"])
        : "",
      body_scripts: settings_obj.the_options.hasOwnProperty("body_scripts")
        ? this.stripSlashes(settings_obj.the_options["body_scripts"])
        : "",
      footer_scripts: settings_obj.the_options.hasOwnProperty("footer_scripts")
        ? this.stripSlashes(settings_obj.the_options["footer_scripts"])
        : "",
      is_gdpr:
        this.gdpr_policy === "gdpr" || this.gdpr_policy === "both"
          ? true
          : false,
      is_script_dependency_on:
      settings_obj.the_options.hasOwnProperty("is_script_dependency_on") &&
      (true === settings_obj.the_options["is_script_dependency_on"] ||
        1 === settings_obj.the_options["is_script_dependency_on"])
        ? true
        : false,
      header_dependency: settings_obj.the_options.hasOwnProperty("header_dependency")
        ? settings_obj.the_options["header_dependency"]
        : '',
      header_dependency_list: settings_obj.header_dependency_list,
      header_dependency_map: {
        'Body Scripts': false,
        'Footer Scripts': false,
      },
      footer_dependency: settings_obj.the_options.hasOwnProperty("footer_dependency")
        ? settings_obj.the_options["footer_dependency"]
        : '',
      footer_dependency_selected: null,
      footer_dependency_list: settings_obj.footer_dependency_list,
      footer_dependency_map: {
        'Header Scripts': false,
        'Body Scripts': false,
      },
    }
  },
  methods: {
    setValues() {
      if (this.gdpr_policy === "both") {
        this.is_ccpa = true;
        this.is_gdpr = true;
        this.is_eprivacy = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = true;
        this.show_revoke_card = true;
      } else if (this.gdpr_policy === "ccpa") {
        this.is_ccpa = true;
        this.is_eprivacy = false;
        this.is_gdpr = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = true;
        this.show_revoke_card = false;
      } else if (this.gdpr_policy === "gdpr") {
        this.is_gdpr = true;
        this.is_ccpa = false;
        this.is_eprivacy = false;
        this.is_lgpd = false;
        this.show_revoke_card = true;
        this.show_visitor_conditions = true;
      } else if (this.gdpr_policy === "lgpd") {
        this.is_gdpr = false;
        this.is_ccpa = false;
        this.is_lgpd = true;
        this.is_eprivacy = false;
        this.show_revoke_card = true;
        this.show_visitor_conditions = false;
      } else {
        this.is_eprivacy = true;
        this.is_gdpr = false;
        this.is_ccpa = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = false;
        this.show_revoke_card = true;
      }

      for (let i = 0; i < this.scripts_list_total; i++) {
        this.scripts_list_data[i]["script_status"] = Boolean(
          parseInt(this.scripts_list_data[i]["script_status"])
        );
        for (let j = 0; j < this.category_list_options.length; j++) {
          if (
            this.category_list_options[j].code ===
            this.scripts_list_data[i]["script_category"].toString()
          ) {
            this.scripts_list_data[i]["script_category_label"] =
              this.category_list_options[j].label;
            break;
          }
        }
      }
    },
    stripSlashes(value) {
      return value.replace(/\\(.)/gm, "$1");
    },
    onSwitchingScriptBlocker() {
      this.is_script_blocker_on = !this.is_script_blocker_on;
    },
    showScriptBlockerForm() {
      console.log("DODODO inside scb's showScriptBlockerForm()");
      console.log("DODODO current value: ", this.show_script_blocker);
      this.show_script_blocker = !this.show_script_blocker;
      console.log("DODODO new value: ", this.show_script_blocker);
    },
    onSwitchingScriptDependency() {
      this.is_script_dependency_on = !this.is_script_dependency_on;

      if( this.is_script_dependency_on === false ){
        this.header_dependency = null;
        this.footer_dependency = null;
      }
    },
    onHeaderDependencySelect(value) {
      
      this.header_dependency_map.body = false;
      this.header_dependency_map.footer = false;

      if (this.header_dependency) {
        this.header_dependency_map[this.header_dependency] = true;
        this.header_dependency = this.header_dependency;
      } else {
        this.header_dependency = '';
      }
    },
    onFooterDependencySelect(value) {
      
      this.footer_dependency_map.header = false;
      this.footer_dependency_map.body = false;

      if (this.footer_dependency) {
        this.footer_dependency_map[this.footer_dependency] = true;
        this.footer_dependency = this.footer_dependency;
      } else {
        this.footer_dependency = '';
      }
    },
    onSwitchScriptBlocker(script_id) {
      j("#gdpr-cookie-consent-updating-settings-alert").fadeIn(200);
      j("#gdpr-cookie-consent-updating-settings-alert").fadeOut(2000);
      var that = this;
      this.scripts_list_data[script_id - 1]["script_status"] =
        !this.scripts_list_data[script_id - 1]["script_status"];
      var status = "1";
      if (this.scripts_list_data[script_id - 1]["script_status"]) {
        status = "1";
      } else {
        status = "0";
      }
      var data = {
        action: "wpl_script_blocker",
        security:
          settings_obj.script_blocker_settings.nonces.wpl_script_blocker,
        wpl_script_action: "update_script_status",
        status: status,
        id: script_id,
      };
      jQuery.ajax({
        url: settings_obj.script_blocker_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response === true) {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    onScriptCategorySelect(values) {
      if (!values) {
        this.success_error_message = "You must select a category.";
        j("#gdpr-cookie-consent-save-settings-alert").css(
          "background-color",
          "#e55353"
        );
        j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
        j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        return false;
      }
      var that = this;
      var category_code = values.split(",")[0];
      var script_id = values.split(",")[1];
      for (let i = 0; i < this.category_list_options.length; i++) {
        if (this.category_list_options[i]["code"] === category_code) {
          if (
            this.scripts_list_data[script_id - 1]["script_category"] ===
            category_code
          ) {
            this.success_error_message = "Category updated successfully";
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            return false;
          } else {
            this.scripts_list_data[script_id - 1]["script_category"] =
              this.category_list_options[i].code;
            this.scripts_list_data[script_id - 1]["script_category_label"] =
              this.category_list_options[i].label;
            break;
          }
        }
      }
      var data = {
        action: "wpl_script_blocker",
        security:
          settings_obj.script_blocker_settings.nonces.wpl_script_blocker,
        wpl_script_action: "update_script_category",
        category: category_code,
        id: script_id,
      };
      jQuery.ajax({
        url: settings_obj.script_blocker_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response === true) {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    saveScriptBlockerSettings() {
      console.log("DODODO Saving Script Blocker Settings...");
      this.save_loading = true;

      var that = this;
      var dataV = jQuery("#gcc-save-script-blocker-settings-form").serialize();
      jQuery
        .ajax({
          type: "POST",
          url: settings_obj.ajaxurl,
          data: dataV + "&action=gcc_save_script_blocker_settings",
        })
        .done(function (data) {
          that.success_error_message = "Settings Saved.";
          console.log("Succcess error message: ", that.success_error_message);
          j("#gdpr-cookie-consent-save-settings-alert").css({
              "background-color": "#72b85c",
              "z-index": "10000",
          });
          console.log("style:", j("#gdpr-cookie-consent-save-settings-alert")[0].style.cssText);
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);

          that.save_loading = false;
        })
        .fail(function () {
          that.save_loading = false;
        });
    }
  },
  mounted() {
    j("#gdpr-before-mount").css("display", "none");
    this.setValues();
  }
});

var lang = new Vue({
  el: "#gdpr-cookie-consent-language-settings", 
  data() {
    return {
      labelIcon: {},
      labelIconNew: {
        labelOn: "\u2713",
        labelOff: "\uD83D\uDD12",
      },
      save_loading: false,
      success_error_message: "",
      is_lang_changed: false,
      show_language_as: settings_obj.the_options.hasOwnProperty("lang_selected")
        ? settings_obj.the_options["lang_selected"]
        : "en",
      show_language_as_options: settings_obj.show_language_as_options,
    }
  },
  methods: {
    saveLanguageSettings() {
      console.log("DODODO Saving Language Settings...");
      this.save_loading = true; 

      var that = this;
      var dataV = jQuery("#gcc-save-language-settings-form").serialize();
      jQuery
        .ajax({
          type: "POST",
          url: settings_obj.ajaxurl,
          data:
            dataV + "&action=gcc_save_language_settings"
        })
        .done(function(data) {
          console.log("DODODO cookie manager save response:", data);
          that.success_error_message = "Settings Saved";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#72b85c"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          
          if (that.is_lang_changed) {
            that.is_lang_changed = false;
            location.reload();
          }

          that.save_loading = false;
        })
        .fail(function() {
          that.save_loading = false;
        });
    },
    onLanguageChange() {
      this.is_lang_changed = true;
    },
  }
});

var ckm = new Vue({
  el: "#gdpr-cookie-consent-cookie_manager-settings",
  data() {
    return {
      labelIcon: {},
      labelIconNew: {
        labelOn: "\u2713",
        labelOff: "\uD83D\uDD12",
      },
      save_loading: false,
      success_error_message: "",
      cookie_scanner_data: '',
      cookie_list_tab: true,
      show_custom_cookie_popup: false,
      custom_cookie_categories:
        settings_obj.cookie_list_settings.hasOwnProperty(
          "cookie_list_categories"
        )
          ? settings_obj.cookie_list_settings["cookie_list_categories"]
          : [],
      custom_cookie_types: settings_obj.cookie_list_settings.hasOwnProperty(
        "cookie_list_types"
      )
        ? settings_obj.cookie_list_settings["cookie_list_types"]
        : [],
      custom_cookie_category: 1,
      custom_cookie_type: "HTTP",
      custom_cookie_name: "",
      custom_cookie_domain: "",
      custom_cookie_duration: "",
      custom_cookie_description: "",
      is_custom_cookie_duration_disabled: this.custom_cookie_type === "HTTP Cookie" ? false : true,
      custom_cookie_duration_placeholder: "Duration(days/session)",
      scan_history_list_tab: true,
      post_cookie_list_length: settings_obj.cookie_list_settings.hasOwnProperty(
        "post_cookie_list"
      )
        ? settings_obj.cookie_list_settings["post_cookie_list"]["total"]
        : 0,
      post_cookie_list: settings_obj.cookie_list_settings.hasOwnProperty(
        "post_cookie_list"
      )
        ? settings_obj.cookie_list_settings["post_cookie_list"]["data"]
        : [],
      show_custom_form: this.post_cookie_list_length > 0 ? false : true,
      show_add_custom_button: this.post_cookie_list_length > 0 ? true : false,
      scan_cookie_list_length: settings_obj.cookie_scan_settings.hasOwnProperty(
        "scan_cookie_list"
      )
        ? settings_obj.cookie_scan_settings["scan_cookie_list"]["total"]
        : 0,
      scan_cookie_list: settings_obj.cookie_scan_settings.hasOwnProperty(
        "scan_cookie_list"
      )
        ? settings_obj.cookie_scan_settings["scan_cookie_list"]["data"]
        : [],
      scan_cookie_error_message:
        settings_obj.cookie_scan_settings.hasOwnProperty("error_message")
          ? settings_obj.cookie_scan_settings["error_message"]
          : "",
      scan_cookie_last_scan: settings_obj.cookie_scan_settings.hasOwnProperty(
        "last_scan"
      )
        ? settings_obj.cookie_scan_settings["last_scan"]
        : [],
      continue_scan: 1,
      pollCount: 0,
      onPrg: 0,
      discovered_cookies_list_tab: true,
      scan_in_progress: false,
      schedule_scan_show: false,
      schedule_scan_options: settings_obj.schedule_scan_options,
      schedule_scan_as: settings_obj.the_options.hasOwnProperty(
        "schedule_scan_type"
      )
        ? settings_obj.the_options["schedule_scan_type"]
        : "never",
        schedule_scan_day_options: settings_obj.schedule_scan_day_options,
      schedule_scan_day: settings_obj.the_options.hasOwnProperty("scan_day")
        ? settings_obj.the_options["scan_day"]
        : "Day 1",
      schedule_scan_time_value: settings_obj.the_options.hasOwnProperty(
        "scan_time"
      )
        ? settings_obj.the_options["scan_time"]
        : "8:00 PM",
      schedule_scan_date: settings_obj.the_options.hasOwnProperty("scan_date")
        ? settings_obj.the_options["scan_date"]
        : new Date(),
      next_scan_is_when: settings_obj.the_options.hasOwnProperty(
        "schedule_scan_when"
      )
        ? settings_obj.the_options["schedule_scan_when"]
        : "Not Scheduled", 
    }
  },
  methods: {
    setValues() {
      if (this.custom_cookie_type === "HTTP") {
        this.is_custom_cookie_duration_disabled = false;
        this.custom_cookie_duration = "";
      } else {
        this.is_custom_cookie_duration_disabled = true;
        this.custom_cookie_duration = "Persistent";
      }

      this.show_custom_form = this.post_cookie_list_length > 0 ? false : true;
      this.show_add_custom_button = this.post_cookie_list_length > 0 ? true : false;

    },
    refreshCookieScannerData(html) {
      this.cookie_scanner_data = html;
      const container = document.querySelector('#cookie-scanner-container');
      this.$nextTick(() => {
                new Vue({
                    el: container,
                    data: this.$data, // Reuse the existing Vue instance data
                    methods: this.$options.methods, // Reuse the existing methods
                    mounted: this.$options.mounted, // Reuse the original mounted logic
                    icons: this.$options.icons, // Optionally reuse created lifecycle hook
                });
            });
    },
    openCustomCookieTab() {
      this.cookie_list_tab = true;
      this.scan_history_list_tab = false;
      this.showCreateCookiePopup();
    },
    openDiscoveredScanTab() {
      this.discovered_cookies_list_tab = true;
      this.scan_history_list_tab = false;
      this.onClickStartScan();
    },
    showCreateCookiePopup() {
      this.show_custom_cookie_popup = !this.show_custom_cookie_popup;
    },
    hideCookieForm() {
      this.custom_cookie_name = "";
      this.custom_cookie_domain = "";
      this.custom_cookie_description = "";
      this.custom_cookie_category = 1;
      this.custom_cookie_type = "HTTP";
      this.show_custom_form = false;
      this.show_add_custom_button = true;
      this.is_custom_cookie_duration_disabled = false;
      this.custom_cookie_duration = "";
      this.show_custom_cookie_popup = false;
    },
    onSelectCustomCookieType(value) {
      if (value !== "HTTP") {
        this.is_custom_cookie_duration_disabled = true;
        this.custom_cookie_duration = "Persistent";
      } else {
        this.is_custom_cookie_duration_disabled = false;
        this.custom_cookie_duration = "";
      }
    },
    onSaveCustomCookie() {
      console.log("DODODO onSaveCustomCookie()");
      var parent = j(".gdpr-custom-save-cookie").parents(
        "div.gdpr-add-custom-cookie-form"
      );
      var gdpr_addcookie = parent.find('input[name="gdpr_addcookie"]').val();
      console.log("DODODO gdpr_addcookie: ", gdpr_addcookie);
      if (gdpr_addcookie == 1) {
        var pattern =
          /^((http|https):\/\/)?([a-zA-Z0-9_][-_a-zA-Z0-9]{0,62}\.)+([a-zA-Z0-9]{1,10})$/gm;
        var cname = this.custom_cookie_name;
        var cdomain = this.custom_cookie_domain;
        var cduration = this.custom_cookie_duration;
        var ccategory = this.custom_cookie_category;
        var ctype = this.custom_cookie_type;
        var cdesc = this.custom_cookie_description;
        if (!cname) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Name";
          parent
            .find('input[name="gdpr-cookie-consent-custom-cookie-name"]')
            .focus();
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          return false;
        }
        if (!cdomain) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Domain";
          parent
            .find('input[name="gdpr-cookie-consent-custom-cookie-domain"]')
            .focus();
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          return false;
        } else {
          if (!pattern.test(cdomain)) {
            this.success_error_message = "Cookie domain is not valid.";
            parent
              .find('input[name="gdpr-cookie-consent-custom-cookie-domain"]')
              .focus();
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            return false;
          }
        }
        if (!cduration) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Duration";
          parent
            .find('input[name="gdpr-cookie-consent-custom-cookie-duration"]')
            .focus();
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          return false;
        }
        if (!ctype) {
          this.success_error_message = "Please select a Cookie Type";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          return false;
        }
        if (!ccategory) {
          this.success_error_message = "Please select a Cookie Category";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          return false;
        }
        var cookie_arr = {
          cname: cname,
          cdomain: cdomain,
          cduration: cduration,
          ccategory: ccategory,
          ctype: ctype,
          cdesc: cdesc,
        };
        this.saveCustomPostCookies(cookie_arr);
      }
    },
    saveCookieUpdateSettings() {
      var cookie_post_arr = [];
      var error = false;
      for (let i = 0; i < this.post_cookie_list_length; i++) {
        var pattern =
          /^((http|https):\/\/)?([a-zA-Z0-9_][-_a-zA-Z0-9]{0,62}\.)+([a-zA-Z0-9]{1,10})$/gm;
        var cid = this.post_cookie_list[i]["id_gdpr_cookie_post_cookies"];
        var cname = this.post_cookie_list[i]["name"];
        var cdomain = this.post_cookie_list[i]["domain"];
        var cduration = this.post_cookie_list[i]["duration"];
        var ccategory = this.post_cookie_list[i]["category_id"];
        var ctype = this.post_cookie_list[i]["type"];
        var cdesc = this.post_cookie_list[i]["description"];
        if (!cname) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Name";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          error = true;
        }
        if (!cdomain) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Domain";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          error = true;
        } else {
          if (!pattern.test(cdomain)) {
            this.success_error_message = "Cookie Domain is not valid.";
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            error = true;
          }
        }
        if (!cduration) {
          this.success_error_message =
            "Please fill in these mandatory fields : Cookie Duration";
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          error = true;
        }
        var cookie_arr = {
          cid: cid,
          cname: cname,
          cdomain: cdomain,
          cduration: cduration,
          ccategory: ccategory,
          ctype: ctype,
          cdesc: cdesc,
        };
        cookie_post_arr.push(cookie_arr);
      }
      if (error) {
        return false;
      }
      this.updatePostCookie(cookie_post_arr);
    },
    onUpdateScannedCookieCategory(value) {
      const id = value.split(",")[1];
      const cat = value.split(",")[0];
      for (let i = 0; i < this.scan_cookie_list_length; i++) {
        if (this.scan_cookie_list[i]["id_wpl_cookie_scan_cookies"] == id) {
          for (let j = 0; j < this.custom_cookie_categories.length; j++) {
            if (
              parseInt(this.custom_cookie_categories[j]["code"]) ==
              parseInt(cat)
            ) {
              this.scan_cookie_list[i]["category_id"] =
                this.custom_cookie_categories[j].code;
              this.scan_cookie_list[i]["category"] =
                this.custom_cookie_categories[j].label;
              break;
            }
          }
          break;
        }
      }
    },
    onSelectUpdateCookieCategory(value) {
      const id = value.split(",")[1];
      const cat = value.split(",")[0];
      for (let i = 0; i < this.post_cookie_list_length; i++) {
        if (this.post_cookie_list[i]["id_gdpr_cookie_post_cookies"] === id) {
          for (let j = 0; i < this.custom_cookie_categories.length; j++) {
            if (this.custom_cookie_categories[j]["code"] === parseInt(cat)) {
              this.post_cookie_list[i]["category_id"] =
                this.custom_cookie_categories[j].code;
              this.post_cookie_list[i]["category"] =
                this.custom_cookie_categories[j].label;
              break;
            }
          }
          break;
        }
      }
    },
    onSelectUpdateCookieType(value) {
      const id = value.split(",")[1];
      const type_id = value.split(",")[0];
      for (let i = 0; i < this.post_cookie_list_length; i++) {
        if (this.post_cookie_list[i]["id_gdpr_cookie_post_cookies"] === id) {
          if (type_id !== "HTTP") {
            this.post_cookie_list[i]["enable_duration"] = true;
            this.post_cookie_list[i]["duration"] = "Persistent";
          } else {
            this.post_cookie_list[i]["enable_duration"] = false;
            this.post_cookie_list[i]["duration"] = "";
          }
          for (let j = 0; i < this.custom_cookie_types.length; j++) {
            if (this.custom_cookie_types[j]["code"] === type_id) {
              this.post_cookie_list[i]["type"] =
                this.custom_cookie_types[j].code;
              this.post_cookie_list[i]["type_name"] =
                this.custom_cookie_types[j].label;
              break;
            }
          }
          break;
        }
      }
    },
    setPostListValues() {
      for (let i = 0; i < this.post_cookie_list_length; i++) {
        if (this.post_cookie_list[i]["type"] === "HTTP") {
          this.post_cookie_list[i]["enable_duration"] = false;
        } else {
          this.post_cookie_list[i]["enable_duration"] = true;
        }
        for (let j = 0; j < this.custom_cookie_types.length; j++) {
          if (
            this.custom_cookie_types[j]["code"] ===
            this.post_cookie_list[i]["type"]
          ) {
            this.post_cookie_list[i]["type_name"] =
              this.custom_cookie_types[j].label;
          }
        }
      }
      this.show_custom_form = this.post_cookie_list_length > 0 ? false : true;
      this.show_add_custom_button =
        this.post_cookie_list_length > 0 ? true : false;
    },
    setScanListValues() {
      for (let i = 0; i < this.scan_cookie_list_length; i++) {
        for (let j = 0; j < this.custom_cookie_types.length; j++) {
          if (
            this.custom_cookie_types[j]["code"] ===
            this.scan_cookie_list[i]["type"]
          ) {
            this.scan_cookie_list[i]["type_name"] =
              this.custom_cookie_types[j].label;
          }
        }
      }
    },
    onChangeCookieListTab() {
      this.cookie_list_tab = true;
      this.discovered_cookies_list_tab = false;
      this.scan_history_list_tab = false;
      this.cookie_scan_dropdown = !this.cookie_scan_dropdown;
      const dropdownarrow = document.querySelector('.cookie_arrow')
      dropdownarrow.classList.remove('up');
      dropdownarrow.classList.add('down');
      const tabLink = document.querySelector("a[href='#cookie_settings#cookie_list']");
        if (tabLink) {
            tabLink.click();
        }
      window.location.hash = "#cookie_settings#cookie_list#custom_cookie";
    },
    onChangeDiscoveredListTab() {
      this.cookie_list_tab = false;
      this.discovered_cookies_list_tab = true;
      this.scan_history_list_tab = false;
      this.cookie_scan_dropdown = !this.cookie_scan_dropdown;
      const dropdownarrow = document.querySelector('.cookie_arrow')
      dropdownarrow.classList.remove('up');
      dropdownarrow.classList.add('down');
      const tabLink = document.querySelector("a[href='#cookie_settings#cookie_list']");
        if (tabLink) {
            tabLink.click();
        }
      window.location.hash = "#cookie_settings#cookie_list#discovered_cookies";
    },
    onChangeScanHistoryTab() {
      this.cookie_list_tab = false;
      this.discovered_cookies_list_tab = false;
      this.scan_history_list_tab = true;
      this.cookie_scan_dropdown = !this.cookie_scan_dropdown;
      const dropdownarrow = document.querySelector('.cookie_arrow')
      dropdownarrow.classList.remove('up');
      dropdownarrow.classList.add('down');
      const tabLink = document.querySelector("a[href='#cookie_settings#cookie_list']");
        if (tabLink) {
            tabLink.click();
        }
      window.location.hash = "#cookie_settings#cookie_list#scan_history";
    },
    activateTabFromHash() {
      const hash = window.location.hash;
      if (hash === "#cookie_settings#cookie_list#custom_cookie") {
        this.cookie_scan_dropdown = !this.cookie_scan_dropdown;
        this.onChangeCookieListTab();
      } else if (hash === "#cookie_settings#cookie_list#discovered_cookies") {
        this.cookie_scan_dropdown = !this.cookie_scan_dropdown;
        this.onChangeDiscoveredListTab();
      } else if (hash === "#cookie_settings#cookie_list#scan_history") {
        this.cookie_scan_dropdown = !this.cookie_scan_dropdown;
        this.onChangeScanHistoryTab();
      }
    },
    async saveCookieSettings() {
        this.save_loading = true;
        // When Pro is activated set the values in the aceeditor
        if (this.isGdprProActive) {
          //intializing the acecode editor
          var editor = ace.edit("aceEditor");
          //getting the value of editor
          var code = editor.getValue();
          //setting the value
          this.gdpr_css_text = code;
          editor.setValue(this.gdpr_css_text);
        }
        if(this.is_iabtcf_changed && this.iabtcf_is_on){
          try {
              await this.fetchIABData(); // now REALLY waits for ajax done
          } catch (err) {
              console.error("Failed to save IAB Data", err);
          }
        }
        var that = this;
        var dataV = jQuery("#gcc-save-settings-form").serialize();
        jQuery
          .ajax({
            type: "POST",
            url: settings_obj.ajaxurl,
            data:
              dataV +
              "&action=gcc_save_admin_settings" +
              "&lang_changed=" +
              that.is_lang_changed +
              "&logo_removed=" +
              that.is_logo_removed +"&logo_removed1=" + that.is_logo_removed1 +"&logo_removed2="+ that.is_logo_removed2 +"&logo_removedML1=" + that.is_logo_removedML1 +
              "&gdpr_css_text_field=" +
              that.gdpr_css_text,
          })
          .done(function (data) {
            that.success_error_message = "Settings Saved";
            j("#gdpr-cookie-consent-save-settings-alert").css({
              "background-color": "#72b85c",
              "z-index": "10000",
            });
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            if (that.is_template_changed) {
              that.is_template_changed = false;
              location.reload();
            }
            if (that.is_iabtcf_changed) {
              that.is_iabtcf_changed = false;
              location.reload();
            }
            if (that.is_lang_changed) {
              that.is_lang_changed = false;
              location.reload();
            }
            if (that.data_reqs_switch_clicked == true) {
              that.data_reqs_switch_clicked = false;
              location.reload();
            }
            if (that.consent_log_switch_clicked == true) {
              that.consent_log_switch_clicked = false;
              location.reload();
            }
            if (that.reload_onSelect_law == true) {
              that.reload_onSelect_law = false;
              location.reload();
            }
            if (that.reload_onSafeMode == true) {
              that.reload_onSafeMode = false;
              location.reload();
            }
            if (that.is_logo_removed == true) {
              that.is_logo_removed = false;
              location.reload();
            }
            if (that.is_logo_removed1 == true) {
              that.is_logo_removed1 = false;
              location.reload();
            }
            if (that.is_logo_removed2 == true) {
              that.is_logo_removed2 = false;
              location.reload();
            }
            if (that.is_logo_removedML1 == true) {
              that.is_logo_removedML1 = false;
              location.reload();
            }
            if (that.is_logo_added == true) {
              that.is_logo_added = false;
              location.reload();
            }
            that.save_loading = false;
          })
          .fail(function () {
            that.save_loading = false;
          });
    },
    onStartScheduleScan() {
      this.schedule_scan_show = false; //make it false to close the popup

      if (this.schedule_scan_as == "once") {
        //execute schedule scan once
        this.scheduleScanOnce();

        //set value for the Next Scan Details when Once
        const dateObject = new Date(this.schedule_scan_date);
        const formattedDate = dateObject.toLocaleDateString("en-US", {
          year: "numeric",
          month: "short",
          day: "numeric",
        });
        this.next_scan_is_when = formattedDate;
      } else if (this.schedule_scan_as == "monthly") {
        //execute scan schedule monthly
        this.scanMonthly();

        //set value for the Next Scan Details when Monthly

        // Get the day of the month when the scan should run
        const dayString = this.schedule_scan_day;
        const dayNumber = parseInt(dayString.replace("Day ", ""), 10);
        const targetDayOfMonth = dayNumber;

        // Assuming this.schedule_scan_day contains the day of the month (1 to 31)
        const dayOfMonth = parseInt(
          this.schedule_scan_day.replace("Day ", ""),
          10
        );

        if (isNaN(dayOfMonth) || dayOfMonth < 1 || dayOfMonth > 31) {
          console.error("Invalid day of the month:", dayOfMonth);
        } else {
          // Get the current date and day of the month
          const currentDate = new Date();
          const currentDayOfMonth = currentDate.getDate();

          // Get the selected day of the month for scanning
          const targetDayOfMonth = dayOfMonth;

          // Get the number of days in the current month
          const currentYear = currentDate.getFullYear();
          const currentMonth = currentDate.getMonth() + 1; // Month is zero-based, so we add 1
          const daysInCurrentMonth = new Date(
            currentYear,
            currentMonth,
            0
          ).getDate();

          // Calculate the next scan date based on the current date and the selected day of the month
          let nextScanDate;
          if (
            dayOfMonth > daysInCurrentMonth ||
            currentDayOfMonth > dayOfMonth
          ) {
            // If the selected day exceeds the number of days in the current month
            // or if the current day is greater than the selected day,
            // set the next scan date to the selected day of the month in the next month
            nextScanDate = new Date(
              currentYear,
              currentMonth,
              targetDayOfMonth
            );
          } else {
            // If the current day of the month is less than or equal to the selected day of the month,
            // set the next scan date to the selected day of the month in the current month
            nextScanDate = new Date(
              currentYear,
              currentMonth - 1,
              targetDayOfMonth
            );
          }

          // Format the next scan date as needed (e.g., 'Mar 2, 2023')
          const formattedDate = nextScanDate.toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
          });
          this.next_scan_is_when = formattedDate;
        }
      } else if (this.schedule_scan_as == "never") {
        this.next_scan_is_when = "Not Scheduled";
      }
    },
    scheduleScanOnce() {
      // Define the date and time when you want the function to execute
      let targetDate = new Date(this.schedule_scan_date);

      // Parse the time entered by the user and handle both 12-hour and 24-hour formats
      const timeParts = this.schedule_scan_time_value.split(":");
      let hours = parseInt(timeParts[0], 10);
      const minutes = parseInt(timeParts[1], 10);

      // Check if the time is in 12-hour format (e.g., "01:03 AM")
      if (
        this.schedule_scan_time_value.toUpperCase().includes("PM") &&
        hours < 12
      ) {
        hours += 12;
      } else if (
        this.schedule_scan_time_value.toUpperCase().includes("AM") &&
        hours === 12
      ) {
        hours = 0;
      }

      // Set the hours and minutes in the target date
      targetDate.setHours(hours);
      targetDate.setMinutes(minutes);

      // Calculate the time difference between now and the target date
      const timeUntilExecution = targetDate - new Date();

      // Check if the target date is in the future
      if (timeUntilExecution > 0) {
        // Use setTimeout to delay the execution of scan
        setTimeout(() => {
          // start the scanning here
          this.onClickStartScan();
        }, timeUntilExecution);
      } else {
        // if the target date is in the past
        alert("Selected date is in the past. Please select a vaild date.");
        this.schedule_scan_show = true;
      }
    },
    scanMonthly() {
      // Get the day of the month when the scan should run
      const dayString = this.schedule_scan_day;
      const dayNumber = parseInt(dayString.replace("Day ", ""), 10);
      const targetDayOfMonth = dayNumber;

      if (
        isNaN(targetDayOfMonth) ||
        targetDayOfMonth <= 0 ||
        targetDayOfMonth > 31
      ) {
        alert("Invalid day of the month:", this.schedule_scan_day);
        return; // Exit if the day is invalid
      }

      // Define the time (hours and minutes)
      const timeParts = this.schedule_scan_time_value.split(":");
      let hours = parseInt(timeParts[0], 10);
      const minutes = parseInt(timeParts[1], 10);

      // Check if the time is in 12-hour format (e.g., "01:03 AM")
      if (
        this.schedule_scan_time_value.toUpperCase().includes("PM") &&
        hours < 12
      ) {
        hours += 12;
      } else if (
        this.schedule_scan_time_value.toUpperCase().includes("AM") &&
        hours === 12
      ) {
        hours = 0;
      }
      // Define a function to check and run the scan when the conditions are met
      const checkAndRunScan = () => {
        const currentDate = new Date();
        const currentDayOfMonth = currentDate.getDate();
        const currentHours = currentDate.getHours();
        const currentMinutes = currentDate.getMinutes();

        if (
          currentDayOfMonth === targetDayOfMonth &&
          currentHours === hours &&
          currentMinutes === minutes
        ) {
          // The conditions are met; execute the scan
          this.onClickStartScan();
        }
      };

      // Set an interval to check if the conditions for running the scan are met
      setInterval(checkAndRunScan, 60000);
    },
    onClickStartScan(singlePageScan = false) {
      this.scan_in_progress = true;
      this.continue_scan = 1;
      this.doScan(singlePageScan);
    },
    doScan(singlePageScan = false) {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "check_api",
      };
      var scanbar = j(".wpl_scanbar");
      scanbar.html(
        '<span style="float:left; height:40px; line-height:40px;">' +
          settings_obj.cookie_scan_settings.labels.checking_api +
          '</span> <img src="' +
          settings_obj.cookie_scan_settings.loading_gif +
          '" style="display:inline-block;" />'
      );
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          scanbar.html("");
          if (data.response === true) {
            that.scanNow(singlePageScan);
          } else {
            that.serverUnavailable(scanbar, data.message);
          }
        },
        error: function () {
          scanbar.html("");
          that.showErrorScreen(settings_obj.cookie_scan_settings.labels.error);
        },
      });
    },
    scanNow(singlePageScan = false) {
      var html = this.makeHtml();
      var scanbar = j(".gdpr_scanbar");
      scanbar.html(html);
      j(".gdpr_scanbar_staypage").css({ display: "flex" }).show();
      this.attachScanStop();
      j(".gdpr_scanlog").css({ display: "block", opacity: 0 }).animate(
        {
          opacity: 1,
          height: "auto",
        },
        1000
      );
      this.takePages(0, 0, 0, 0, singlePageScan);
    },
    animateProgressBar(offset, total, msg) {
      var prgElm = j(".gdpr_progress_bar");
      var w = prgElm.width();
      var sp = 100 / total;
      var sw = w / total;
      var cw = sw * offset;
      var cp = sp * offset;

      cp = cp > 100 ? 100 : cp;
      cp = Math.floor(cp < 1 ? 1 : cp);

      cw = cw > w ? w : cw;
      cw = Math.floor(cw < 1 ? 1 : cw);
      j(".gdpr_progress_bar_inner")
        .stop(true, true)
        .animate({ width: cw + "px" }, 300, function () {
          j(".gdpr_progress_action_main").html(msg);
        })
        .html(cp + "%");
    },
    appendLogAnimate(data, offset) {
      var that = this;
      if (data.length > offset) {
        offset++;
        var speed = 300 / data.length;
        setTimeout(function () {
          that.appendLogAnimate(data, offset);
        }, speed);
      }
    },
    takePages(offset, limit, total, scan_id, singlePageScan = false) {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "get_pages",
        offset: offset,
      };
      if (limit) {
        data["limit"] = limit;
      }
      if (total) {
        data["total"] = total;
      }
      if (scan_id) {
        data["scan_id"] = scan_id;
      }
      // Fake progress.
      this.animateProgressBar(
        1,
        100,
        settings_obj.cookie_scan_settings.labels.finding
      );
      jQuery.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (that.isGdprProActive) {
            that.scan_id =
              typeof data.scan_id != "undefined" ? data.scan_id : 0;
            if (that.continue_scan == 0) {
              return false;
            }
            if (typeof data.response != "undefined" && data.response === true) {
              that.appendLogAnimate(data.log, 0);
              var new_offset = parseInt(data.offset) + parseInt(data.limit);
              if (data.total - 1 > new_offset) {
                // substract 1 from total because of home page.
                that.takePages(
                  new_offset,
                  data.limit,
                  data.total,
                  data.scan_id
                );
              } else {
                j(".wpl_progress_action_main").html(
                  settings_obj.cookie_scan_settings.labels.scanning
                );
                that.scanPages(data.scan_id, 0, data.total);
              }
            } else {
              that.showErrorScreen(
                settings_obj.cookie_scan_settings.labels.error
              );
            }
          } else {
            const urlParams = new URLSearchParams(window.location.search);
            const scanUrlParam = urlParams.get("scan_url");
            var ndata = {
              action: "wpl_cookie_scanner_view_capabilities",
              security:
                settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
              no_of_scan: singlePageScan == true ? 1 : data.log.length,
              offset: offset,
              scan_id: scan_id ? scan_id : 0,
              total_pages: data.total,
            };
            jQuery
              .ajax({
                url: settings_obj.cookie_scan_settings.ajax_url,
                data: ndata,
                dataType: "json",
                type: "POST",
              })
              .done(function (response) {
                if (
                  response.success &&
                  response.data.connection_status === "active"
                ) {
                  that.scan_id = data.scan_id !== undefined ? data.scan_id : 0;
                  if (that.continue_scan == 0) {
                    return false;
                  }
                  if (data.response === true) {
                    that.appendLogAnimate(data.log, 0);
                    var new_offset =
                      parseInt(data.offset) + parseInt(data.limit);
                    if (data.total - 1 > new_offset) {
                      // subtract 1 from total because of home page.
                      that.takePages(
                        new_offset,
                        data.limit,
                        data.total,
                        data.scan_id
                      );
                    } else {
                      jQuery(".wpl_progress_action_main").html(
                        settings_obj.cookie_scan_settings.labels.scanning
                      );
                      that.scanPages(data.scan_id, 0, data.total);
                    }
                  } else {
                    that.showErrorScreen(
                      settings_obj.cookie_scan_settings.labels.error
                    );
                  }
                } else {
                  that.showScanNowPopup();
                }
              })
              .fail(function () {
                if (that.continue_scan == 0) {
                  return false;
                }
                that.showErrorScreen(
                  settings_obj.cookie_scan_settings.labels.error
                );
              });
          }
        },
        error: function () {
          if (that.continue_scan == 0) {
            return false;
          }
          that.showErrorScreen(settings_obj.cookie_scan_settings.labels.error);
        },
      });
    },
    showScanNowPopup() {
      this.$nextTick(() => {
        const scanBtn = jQuery(".scan-now-btn");
        const popup = jQuery("#popup-site-excausted");
        const cancelButton = jQuery(".popup-image");
        popup.fadeIn();
        cancelButton.off("click").on("click", function (e) {
          popup.fadeOut();
          localStorage.setItem("auto_scan_process_started", "false");
          window.location.reload();
        });
      });
    },
    scanPages(scan_id, offset, total) {
      var that = this;
      var scanbar = j(".gdpr_scanbar");
      this.pollCount = 0;
      var hash = Math.random().toString(36).replace("0.", "");
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "scan_pages",
        offset: offset,
        scan_id: scan_id,
        total: total,
        hash: hash,
      };
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          that.scan_id = typeof data.scan_id != "undefined" ? data.scan_id : 0;
          if (that.continue_scan == 0) {
            return false;
          }
          if (data.response == true) {
            this.scan_in_progress = false;
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.delete("auto_scan");
            that.getScanCookies(scan_id, offset, total, hash);
          } else {
            scanbar.html("");
            j(".wpl_scanbar_staypage").hide();
            that.serverUnavailable(scanbar, data.message);
          }
        },
        error: function () {
          var current = that;
          if (that.continue_scan == 0) {
            return false;
          }
          // error and retry function.

          that.animateProgressBar(
            offset,
            total,
            settings_obj.cookie_scan_settings.labels.retrying
          );
          setTimeout(function () {
            current.scanPages(scan_id, offset, total);
          }, 2000);
        },
      });
    },
    getScanCookies(scan_id, offset, total, hash) {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "get_post_scan_cookies",
        offset: offset,
        scan_id: scan_id,
        total: total,
        hash: hash,
      };
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response == true) {
            var prg_offset = parseInt(offset) + parseInt(data.total_scanned);
            var prg_msg =
              settings_obj.cookie_scan_settings.labels.scanning + " ";
            that.appendLogAnimate(data.log, 0);
            if (data.continue === true) {
              that.scanPages(data.scan_id, data.offset, data.total);
            } else {
              prg_msg = settings_obj.cookie_scan_settings.labels.finished;
              prg_msg +=
                " (" +
                settings_obj.cookie_scan_settings.labels.total_cookies_found +
                ": " +
                data.total_cookies +
                ")";
              that.showSuccessScreen(prg_msg, scan_id, 1);
            }
            that.animateProgressBar(prg_offset, total, prg_msg);
          } else {
            var current = that;
            if (that.pollCount < 10) {
              that.pollCount++;
              setTimeout(function () {
                current.getScanCookies(
                  data.scan_id,
                  data.offset,
                  data.total,
                  data.hash
                );
              }, 10000);
            } else {
              that.showErrorScreen("Something went wrong, please scan again");
            }
          }
        },
        error: function () {
          var current = that;
          if (that.continue_scan == 0) {
            return false;
          }
          if (that.pollCount < 10) {
            setTimeout(function () {
              that.getScanCookies(offset, scan_id, total, hash);
            }, 5000);
          } else {
            that.showErrorScreen("Something went wrong, please scan again");
          }
        },
      });
    },
    makeHtml() {
      return (
        '<div class="gdpr_scanlog">' +
        '<div class="gdpr_progress_bar">' +
        '<span class="gdpr_progress_bar_inner gdpr_progress_bar_inner_restructured">' +
        "</span>" +
        "</div>" +
        '<div class="gdpr_progress_action_main">' +
        settings_obj.cookie_scan_settings.labels.finding +
        "</div>" +
        '<div class="gdpr_scanlog_bar"><button type="button" class="pull-right gdpr_stop_scan">' +
        settings_obj.cookie_scan_settings.labels.stop +
        "</button></div>" +
        "</div>"
      );
    },
    attachScanStop() {
      var that = this;
      j(".gdpr_stop_scan").click(function () {
        that.stopScan();
      });
    },
    stopScan() {
      if (this.continue_scan == 0) {
        return false;
      }
      if (confirm(settings_obj.cookie_scan_settings.labels.ru_sure)) {
        this.continue_scan = 0;
        this.stoppingScan(this.scan_id);
      }
    },
    stoppingScan(scan_id) {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "stop_scan",
        scan_id: scan_id,
      };
      j(".gdpr_stop_scan")
        .html(settings_obj.cookie_scan_settings.labels.stoping)
        .css({ opacity: ".5" });
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          that.showSuccessScreen(
            settings_obj.cookie_scan_settings.labels.scanning_stopped,
            scan_id,
            data.total
          );
        },
        error: function () {
          // error function.
          that.showErrorScreen(settings_obj.cookie_scan_settings.labels.error);
        },
      });
    },
    serverUnavailable: function (elm, msg) {
      elm.html(
        '<div style="background:#ffffff; border:solid 1px #cccccc; color:#333333; padding:5px;">' +
          msg +
          "</div>"
      );
    },
    showErrorScreen: function (error_msg) {
      var html =
        '<button type="button" class="pull-right gdpr_scan_again" style="margin-left:5px;">' +
        settings_obj.cookie_scan_settings.labels.scan_again +
        "</button>";
      j(".gdpr_scanlog_bar").html(html);
      j(".gdpr_progress_action_main").html(error_msg);
      this.success_error_message = error_msg;
      j("#gdpr-cookie-consent-save-settings-alert").css(
        "background-color",
        "#e55353"
      );
      j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
      j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
      j(".gdpr_scanbar_staypage").hide();
      this.scanAgain();
    },
    showSuccessScreen(success_msg, scan_id, total) {
      var html =
        '<button type="button" class="pull-right gdpr_scan_again" style="margin-left:5px;">' +
        settings_obj.cookie_scan_settings.labels.scan_again +
        "</button>";
      html += '<span class="spinner" style="margin-top:5px"></span>';
      j(".gdpr_scanlog_bar").html(html);
      j(".gdpr_progress_action_main").html(success_msg);
      this.success_error_message = success_msg;
      j("#gdpr-cookie-consent-save-settings-alert").css(
        "background-color",
        "#72b85c"
      );
      j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
      j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
      j(".gdpr_scanbar_staypage").hide();
      this.showScanCookieList();
      this.scanAgain();
      setTimeout(() => {
        localStorage.setItem("auto_scan_process_started", "false");
        window.location.reload();
      }, 3000);
    },
    showScanCookieList() {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "get_scanned_cookies_list",
      };
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response) {
            that.scan_cookie_list_length = data.total;
            that.scan_cookie_list = data.data;
            that.setScanListValues();
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    scanAgain() {
      var that = this;
      j(".gdpr_scan_again")
        .unbind("click")
        .click(function () {
          that.continue_scan = 1;
          that.scanNow();
        });
    },
    onClickDeleteCookie() {
      var that = this;
      var data = {
        action: "wpl_cookies_deletion",
      };
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          that.showSuccessScreen("Cookies Cleared Successfully!");
          window.location.reload();
        },
        error: function () {
          // error function.
          that.showErrorScreen("Some error occuered");
        },
      });
    },
    saveCustomPostCookies(cookie_data) {
      console.log("DODODO saveCustomPostCookies()");
      var that = this;
      var data = {
        action: "gdpr_cookie_custom",
        security: settings_obj.cookie_list_settings.nonces.gdpr_cookie_custom,
        gdpr_custom_action: "save_post_cookie",
        cookie_arr: cookie_data,
      };
      j.ajax({
        url: settings_obj.cookie_list_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response === true) {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            that.hideCookieForm();
            that.getPostCookieList();
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
    onDeleteCustomCookie(cookie_id) {
      this.deletePostCookie(cookie_id);
    },
        updateScannedCookies() {
      var cookie_scan_arr = [];
      for (let i = 0; i < this.scan_cookie_list_length; i++) {
        var cid = this.scan_cookie_list[i]["id_wpl_cookie_scan_cookies"];
        var ccategory = this.scan_cookie_list[i]["category_id"];
        var cdesc = this.scan_cookie_list[i]["description"];
        var cookie_arr = {
          cid: cid,
          ccategory: ccategory,
          cdesc: cdesc,
        };
        cookie_scan_arr.push(cookie_arr);
      }
      this.updateScanCookie(cookie_scan_arr);
    },
    scheduleScanShow() {
      this.schedule_scan_show = true;
    },
    scheduleScanHide() {
      this.schedule_scan_show = false;
    },
    scanTypeChange(value) {
      this.schedule_scan_as = value;
    },
    scanTimeChange(value) {
      this.schedule_scan_time_value = value;
    },
    scanDateChange(value) {
      this.schedule_scan_date = value;
    },
    scanDayChange(value) {
      this.schedule_scan_day = value;
    },
    updateScanCookie(cookie_arr) {
      var that = this;
      var data = {
        action: "wpl_cookie_scanner",
        security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
        wpl_scanner_action: "update_scan_cookie",
        cookie_arr: cookie_arr,
      };
      j.ajax({
        url: settings_obj.cookie_scan_settings.ajax_url,
        data: data,
        dataType: "json",
        type: "POST",
        success: function (data) {
          if (data.response === true) {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#72b85c"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            that.showScanCookieList();
          } else {
            that.success_error_message = data.message;
            j("#gdpr-cookie-consent-save-settings-alert").css(
              "background-color",
              "#e55353"
            );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
          }
        },
        error: function () {
          that.success_error_message = data.message;
          j("#gdpr-cookie-consent-save-settings-alert").css(
            "background-color",
            "#e55353"
          );
          j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
          j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
        },
      });
    },
  }
});
window.ckm = ckm;