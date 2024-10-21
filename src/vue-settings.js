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
import Datepicker from "vuejs-datepicker";
import VueApexCharts from "vue-apexcharts";
// Main JS (in UMD format)
import VueTimepicker from "vue2-timepicker";
// CSS
import "vue2-timepicker/dist/VueTimepicker.css";

// Import AceEditor
import AceEditor from "vuejs-ace-editor";

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
Vue.component("datepicker", Datepicker);
Vue.component("vue-timepicker", VueTimepicker);
Vue.component("aceeditor", AceEditor);
Vue.component("ab-testing-chart", AB_Testing_Chart);

const j = jQuery.noConflict();
// new Vue({
//   el: "#ab-testing-chart", // Ensure this matches the ID in your HTML
//   //   components: {
//   //     ComparisonChart,
//   //   },
// });
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
      is_lang_changed: false,
      is_iabtcf_changed: false,
      is_logo_removed: false,
      save_loading: false,
      edit_discovered_cookie: {},
      edit_discovered_cookie_on: false,
      appendField: ".gdpr-cookie-consent-settings-container",
      configure_image_url: require("../admin/images/configure-icon.png"),
      progress_bar: require("../admin/images/progress_bar.svg"),
      edit_discovered_cookies_img: require("../admin/images/edit-discovered-cookies.svg"),
      close_round_img: require("../admin/images/Close_round.svg"),
      account_connection: require("../admin/images/account_connection.svg"),
      closeOnBackdrop: true,
      centered: true,
      accept_button_popup: false,
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
      schedule_scan_show: false,
      show_custom_cookie_popup: false,
      scan_in_progress: false,
      is_consent_renewed:
        "true" == settings_obj.the_options["consent_renew_enable"] ||
        1 === settings_obj.the_options["consent_renew_enable"]
          ? true
          : false,
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
      iabtcf_msg: `We and our <a id = "vendor-link" href = "#" data-toggle = "gdprmodal" data-target = "#gdpr-gdprmodal">836 partners</a> use cookies and other tracking technologies to improve your experience on our website. We may store and/or access information on a device and process personal data, such as your IP address and browsing data, for personalised advertising and content, advertising and content measurement, audience research and services development. Additionally, we may utilize precise geolocation data and identification through device scanning.\n\nPlease note that your consent will be valid across all our subdomains. You can change or withdraw your consent at any time by clicking the “Cookie Settings” button at the bottom of your screen. We respect your choices and are committed to providing you with a transparent and secure browsing experience.`,
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
      selectedRadioGdpr:
        settings_obj.the_options.hasOwnProperty("is_eu_on") &&
        (true === settings_obj.the_options["is_eu_on"] ||
          1 === settings_obj.the_options["is_eu_on"])
          ? true
          : false,
      selectedRadioCcpa:
        settings_obj.the_options.hasOwnProperty("is_ccpa_on") &&
        (true === settings_obj.the_options["is_ccpa_on"] ||
          1 === settings_obj.the_options["is_ccpa_on"])
          ? true
          : false,
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
      tab_position_options: settings_obj.tab_position_options,
      tab_position: settings_obj.the_options.hasOwnProperty(
        "show_again_position"
      )
        ? settings_obj.the_options["show_again_position"]
        : "right",
      tab_margin: settings_obj.the_options.hasOwnProperty("show_again_margin")
        ? settings_obj.the_options["show_again_margin"]
        : "5",
      tab_text: settings_obj.the_options.hasOwnProperty("show_again_text")
        ? settings_obj.the_options["show_again_text"]
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
      show_credits:
        settings_obj.the_options.hasOwnProperty("show_credits") &&
        (true === settings_obj.the_options["show_credits"] ||
          1 === settings_obj.the_options["show_credits"])
          ? true
          : false,
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
      button_readmore_button_size: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_size"
      )
        ? settings_obj.the_options["button_readmore_button_size"]
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
      accept_size_options: settings_obj.accept_size_options,
      accept_size: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_size"
      )
        ? settings_obj.the_options["button_accept_button_size"]
        : "medium",
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
      decline_size: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_size"
      )
        ? settings_obj.the_options["button_decline_button_size"]
        : "medium",
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
      settings_layout_options: settings_obj.settings_layout_options,
      settings_layout_options_extended:
        settings_obj.settings_layout_options_extended,
      settings_layout:
        settings_obj.the_options.hasOwnProperty("button_settings_as_popup") &&
        (true === settings_obj.the_options["button_settings_as_popup"] ||
          1 === settings_obj.the_options["button_settings_as_popup"])
          ? true
          : false,
      is_banner: this.show_cookie_as === "banner" ? true : false,
      layout_skin_options: settings_obj.layout_skin_options,
      layout_skin: settings_obj.the_options.hasOwnProperty(
        "button_settings_layout_skin"
      )
        ? settings_obj.the_options["button_settings_layout_skin"]
        : "layout-default",
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
      settings_size: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_size"
      )
        ? settings_obj.the_options["button_settings_button_size"]
        : "medium",
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
      confirm_size: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_size"
      )
        ? settings_obj.the_options["button_confirm_button_size"]
        : "medium",
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
      cancel_size: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_size"
      )
        ? settings_obj.the_options["button_cancel_button_size"]
        : "medium",
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
      accept_size1: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_size1"
      )
        ? settings_obj.the_options["button_accept_button_size1"]
        : "medium",
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
      accept_all_size1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_button_size1"
      )
        ? settings_obj.the_options["button_accept_all_button_size1"]
        : "medium",
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
      decline_size1: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_size1"
      )
        ? settings_obj.the_options["button_decline_button_size1"]
        : "medium",
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

      settings_layout1:
        settings_obj.the_options.hasOwnProperty("button_settings_as_popup1") &&
        (true === settings_obj.the_options["button_settings_as_popup1"] ||
          1 === settings_obj.the_options["button_settings_as_popup1"] ||
          "true" === settings_obj.the_options["button_settings_as_popup1"])
          ? true
          : false,
      layout_skin1: settings_obj.the_options.hasOwnProperty(
        "button_settings_layout_skin1"
      )
        ? settings_obj.the_options["button_settings_layout_skin1"]
        : "layout-default",
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
      settings_size1: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_size1"
      )
        ? settings_obj.the_options["button_settings_button_size1"]
        : "medium",
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
      confirm_size1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_size1"
      )
        ? settings_obj.the_options["button_confirm_button_size1"]
        : "medium",
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
      cancel_size1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_size1"
      )
        ? settings_obj.the_options["button_cancel_button_size1"]
        : "medium",
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
      accept_size2: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_size2"
      )
        ? settings_obj.the_options["button_accept_button_size2"]
        : "medium",
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
      accept_all_size2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_button_size2"
      )
        ? settings_obj.the_options["button_accept_all_button_size2"]
        : "medium",
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
      decline_size2: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_size2"
      )
        ? settings_obj.the_options["button_decline_button_size2"]
        : "medium",
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

      settings_layout2:
        settings_obj.the_options.hasOwnProperty("button_settings_as_popup2") &&
        (true === settings_obj.the_options["button_settings_as_popup2"] ||
          1 === settings_obj.the_options["button_settings_as_popup2"] ||
          "true" === settings_obj.the_options["button_settings_as_popup2"])
          ? true
          : false,
      layout_skin2: settings_obj.the_options.hasOwnProperty(
        "button_settings_layout_skin2"
      )
        ? settings_obj.the_options["button_settings_layout_skin2"]
        : "layout-default",
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
      settings_size2: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_size2"
      )
        ? settings_obj.the_options["button_settings_button_size2"]
        : "medium",
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
      confirm_size2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_size2"
      )
        ? settings_obj.the_options["button_confirm_button_size2"]
        : "medium",
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
      cancel_size2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_size2"
      )
        ? settings_obj.the_options["button_cancel_button_size2"]
        : "medium",
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
        : "banner-default",
      banner_template: settings_obj.the_options.hasOwnProperty(
        "banner_template"
      )
        ? settings_obj.the_options["banner_template"]
        : "banner-default",
      popup_template: settings_obj.the_options.hasOwnProperty("popup_template")
        ? settings_obj.the_options["popup_template"]
        : "popup-default",
      widget_template: settings_obj.the_options.hasOwnProperty(
        "widget_template"
      )
        ? settings_obj.the_options["widget_template"]
        : "widget-default",
      show_banner_template:
        settings_obj.the_options.hasOwnProperty("show_cookie_as") &&
        settings_obj.the_options["show_cookie_as"] === "banner"
          ? true
          : false,
      show_popup_template:
        settings_obj.the_options.hasOwnProperty("show_cookie_as") &&
        settings_obj.the_options["show_cookie_as"] === "popup"
          ? true
          : false,
      show_widget_template:
        settings_obj.the_options.hasOwnProperty("show_cookie_as") &&
        settings_obj.the_options["show_cookie_as"] === "widget"
          ? true
          : false,
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
      accept_all_size: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_button_size"
      )
        ? settings_obj.the_options["button_accept_all_button_size"]
        : "medium",
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
      reload_onSelect_law: false,
      reload_onSafeMode: false,
      // hide banner.
      select_pages: settings_obj.the_options.hasOwnProperty("select_pages")
        ? settings_obj.the_options["select_pages"]
        : [],
      select_pages_array: [],
      list_of_pages: settings_obj.list_of_pages,
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
      is_selectedCountry_on:
        settings_obj.the_options.hasOwnProperty("is_selectedCountry_on") &&
        (true === settings_obj.the_options["is_selectedCountry_on"] ||
          1 === settings_obj.the_options["is_selectedCountry_on"])
          ? true
          : false,
      is_worldwide_on:
        settings_obj.the_options.hasOwnProperty("is_worldwide_on") &&
        (true === settings_obj.the_options["is_worldwide_on"] ||
          1 === settings_obj.the_options["is_worldwide_on"])
          ? true
          : false,
      selectedRadioWorldWide:
        settings_obj.the_options.hasOwnProperty("is_worldwide_on") &&
        (true === settings_obj.the_options["is_worldwide_on"] ||
          1 === settings_obj.the_options["is_worldwide_on"])
          ? true
          : false,
      list_of_countries: settings_obj.list_of_countries,
      select_countries: settings_obj.the_options.hasOwnProperty(
        "select_countries"
      )
        ? settings_obj.the_options["select_countries"]
        : [],
      select_countries_array: [],
      show_Select_Country: false,
      selectedRadioCountry:
        settings_obj.the_options.hasOwnProperty("is_selectedCountry_on") &&
        (true === settings_obj.the_options["is_selectedCountry_on"] ||
          1 === settings_obj.the_options["is_selectedCountry_on"])
          ? true
          : false,
      cookie_list_tab: true,
      discovered_cookies_list_tab: false,
      scan_history_list_tab: false,
      preview_cookie_declaration: true,
      preview_about_cookie: false,
      preview_necessary: true,
      preview_marketing: false,
      preview_analysis: false,
      preview_preference: false,
      preview_unclassified: false,
    };
  },

  methods: {
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
      if (this.show_cookie_as === "banner") {
        this.show_banner_template = true;
        this.show_popup_template = false;
        this.show_widget_template = false;
      } else if (this.show_cookie_as === "popup") {
        this.show_banner_template = false;
        this.show_popup_template = true;
        this.show_widget_template = false;
      } else if (this.show_cookie_as === "widget") {
        this.show_banner_template = false;
        this.show_popup_template = false;
        this.show_widget_template = true;
      }
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
      let navLinks = j(".nav-link").map(function () {
        return this.getAttribute("href");
      });
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
      //changing the value of banner_preview_swicth_value enable/disable
      this.banner_preview_is_on = !this.banner_preview_is_on;
    },
    onSwitchDntEnable() {
      //changing the value of do_not_track_on enable/disable
      this.do_not_track_on = !this.do_not_track_on;
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
    onSwitchEUEnable(value) {
      this.is_eu_on = !this.is_eu_on;
      if (value) {
        this.selectedRadioGdpr = value === true ? true : false;
      }
    },
    onSwitchCCPAEnable(value) {
      this.is_ccpa_on = !this.is_ccpa_on;
      if (value) {
        this.selectedRadioCcpa = value === true ? true : false;
      }
    },
    onSwitchWorldWideEnable() {
      this.selectedRadioWorldWide = "yes";
      this.selectedRadioGdpr = false;
      this.selectedRadioCountry = false;
      this.selectedRadioCcpa = false;
      this.is_worldwide_on = true;
      this.is_eu_on = false;
      this.is_selectedCountry_on = false;
      this.is_ccpa_on = false;
    },
    onSwitchEUEnable(isChecked) {
      if (isChecked) {
        this.selectedRadioGdpr = true;
        this.selectedRadioWorldWide = false;
        this.is_eu_on = true;
        this.is_worldwide_on = false;
      } else {
        this.selectedRadioGdpr = false;
        this.is_eu_on = false;
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
      }
    },
    onSwitchCCPAEnable(isChecked) {
      if (isChecked) {
        this.selectedRadioCcpa = true;
        this.selectedRadioWorldWide = false;
        this.is_ccpa_on = true;
        this.is_worldwide_on = false;
      } else {
        this.selectedRadioCcpa = false;
        this.is_ccpa_on = false;
      }
    },
    onCountrySelect(value) {
      this.select_countries = this.select_countries_array.join(",");
    },
    closeModal() {
      this.showModal = false;
    },
    showSelectCountryForm() {
      this.show_Select_Country = !this.show_Select_Country;
    },
    onEnablesafeSwitch() {
      if (this.enable_safe === "true") {
        this.is_eu_on = "no";
      } else {
        this.is_eu_on = "yes";
      }
    },
    onEnablesafeSwitchCCPA() {
      if (this.enable_safe === "true") {
        this.is_ccpa_on = "no";
      } else {
        this.is_ccpa_on = "yes";
      }
    },
    onSwitchRevokeConsentEnable() {
      this.is_revoke_consent_on = !this.is_revoke_consent_on;
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
    //consent forward.
    onSwitchConsentForward() {
      this.consent_forward = !this.consent_forward;
    },
    onSwitchEnableSafe() {
      this.onEnablesafeSwitch();
      this.onEnablesafeSwitchCCPA();
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
    onClickRenewConsent() {
      this.is_consent_renewed = true;
      this.success_error_message = "User Consent Renewed";
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
      if (value === "banner") {
        this.is_banner = true;
        this.show_banner_template = true;
        this.show_popup_template = false;
        this.show_widget_template = false;
        this.template = this.banner_template;
      } else {
        this.is_banner = false;
        if (value === "popup") {
          this.show_banner_template = false;
          this.show_popup_template = true;
          this.show_widget_template = false;
          this.template = this.popup_template;
        } else if (value === "widget") {
          this.show_banner_template = false;
          this.show_popup_template = false;
          this.show_widget_template = true;
          this.template = this.widget_template;
        }
      }
    },
    cookiebannerPositionChange(position) {
      this.cookie_position = position;
    },
    cookiewidgetPositionChange(value) {
      this.cookie_widget_position = value;
    },
    onTemplateChange(value) {
      if (this.show_cookie_as === "banner") {
        this.banner_template = value;
        this.template = value;
        if (this.banner_template == "banner-dark_row") {
          this.cookie_bar_color = "#323742";
          this.cookie_bar_color1 = "#323742";
          this.cookie_bar_color2 = "#323742";
          this.accept_background_color = "#3EAF9A";
          this.accept_all_background_color = "#3EAF9A";
          jQuery(".gdpr_preview").css("color", "white");
          this.decline_background_color = "#333333";
          this.settings_background_color = "#323742";
          this.settings_border_width = "1";
          this.settings_border_color = "#3EAF9A";
          this.settings_style = "solid";
          this.button_readmore_link_color = "#3EAF9A";
          this.settings_text_color = "#3EAF9A";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#323742";
          this.cookie_text_color1 = "#ffffff";
          this.accept_text_color1 = "#ffffff";
          this.accept_background_color1 = "#3EAF9A";
          this.accept_all_background_color1 = "#3EAF9A";
          this.decline_background_color1 = "#333333";
          this.settings_background_color1 = "#323742";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#3EAF9A";
          this.settings_style1 = "solid";
          this.button_readmore_link_color1 = "#3EAF9A";
          this.settings_text_color1 = "#3EAF9A";
          this.cookie_text_color2 = "#ffffff";
          this.accept_text_color2 = "#ffffff";
          this.accept_background_color2 = "#3EAF9A";
          this.accept_all_background_color2 = "#3EAF9A";
          this.decline_background_color2 = "#333333";
          this.settings_background_color2 = "#323742";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#3EAF9A";
          this.settings_style2 = "solid";
          this.button_readmore_link_color2 = "#3EAF9A";
          this.settings_text_color2 = "#3EAF9A";
        } else if (this.banner_template == "banner-almond_column") {
          this.cookie_bar_color = "#E8DDBB";
          this.accept_background_color = "#DE7834";
          this.accept_all_background_color = "#DE7834";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.settings_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.button_revoke_consent_text_color = "#306189";
          this.button_revoke_consent_background_color = "#E8DDBB";
          this.cookie_text_color1 = "#111111";
          this.accept_text_color1 = "#ffffff";
          this.cookie_text_color2 = "#111111";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color1 = "#E8DDBB";
          this.accept_background_color1 = "#DE7834";
          this.accept_all_background_color1 = "#DE7834";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_bar_color2 = "#E8DDBB";
          this.accept_background_color2 = "#DE7834";
          this.accept_all_background_color2 = "#DE7834";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
        } else if (this.banner_template == "banner-grey_center") {
          this.cookie_bar_color = "#F4F4F4";
          this.accept_background_color = "#DE7834";
          this.accept_all_background_color = "#DE7834";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.cookie_text_color1 = "#111111";
          this.accept_text_color1 = "#ffffff";
          this.cookie_text_color2 = "#111111";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color1 = "#F4F4F4";
          this.accept_background_color1 = "#DE7834";
          this.accept_all_background_color1 = "#DE7834";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_bar_color2 = "#F4F4F4";
          this.accept_background_color2 = "#DE7834";
          this.accept_all_background_color2 = "#DE7834";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
          this.button_readmore_link_color = "#DE7834";
          this.settings_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#F4F4F4";
        } else if (this.banner_template == "banner-grey_column") {
          this.cookie_bar_color = "#F4F4F4";
          this.accept_background_color = "#E14469";
          this.accept_all_background_color = "#E14469";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.button_readmore_link_color = "#E14469";
          this.settings_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_color1 = "#F4F4F4";
          this.cookie_text_color1 = "#1E3D59";
          this.accept_background_color1 = "#E14469";
          this.accept_all_background_color1 = "#E14469";
          this.accept_text_color1 = "#ffffff";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_text_color2 = "#1E3D59";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#F4F4F4";
          this.accept_background_color2 = "#E14469";
          this.accept_all_background_color2 = "#E14469";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#F4F4F4";
        } else if (this.banner_template == "banner-navy_blue_center") {
          this.cookie_bar_color = "#2A3E71";
          this.accept_background_color = "#369EE3";
          this.accept_all_background_color = "#369EE3";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#2A3E71";
          this.settings_border_width = "1";
          this.settings_border_color = "#FFFFFF";
          this.settings_style = "solid";
          this.cookie_text_color1 = "#ffffff";
          this.accept_text_color1 = "#ffffff";
          this.cookie_bar_color1 = "#2A3E71";
          this.accept_background_color1 = "#369EE3";
          this.accept_all_background_color1 = "#369EE3";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#2A3E71";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#FFFFFF";
          this.settings_style1 = "solid";
          this.cookie_text_color2 = "#ffffff";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#2A3E71";
          this.accept_background_color2 = "#369EE3";
          this.accept_all_background_color2 = "#369EE3";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#2A3E71";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#FFFFFF";
          this.settings_style2 = "solid";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#369EE3";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#2A3E71";
        } else if (this.banner_template == "banner-default") {
          this.cookie_bar_color = "#FFFFFF";
          this.accept_background_color = "#66CC66";
          this.accept_all_background_color = "#66CC66";
          this.decline_background_color = "#EF5454";
          this.settings_background_color = "#007CBA";
          this.settings_style = "none";
          this.cookie_bar_color1 = "#FFFFFF";
          this.cookie_text_color1 = "#111111";
          this.accept_background_color1 = "#66CC66";
          this.accept_all_background_color1 = "#66CC66";
          this.decline_background_color1 = "#EF5454";
          this.settings_background_color1 = "#007CBA";
          this.settings_style1 = "none";
          this.cookie_bar_color2 = "#FFFFFF";
          this.cookie_text_color1 = "#111111";
          this.accept_background_color2 = "#66CC66";
          this.accept_all_background_color2 = "#66CC66";
          this.decline_background_color2 = "#EF5454";
          this.settings_background_color2 = "#007CBA";
          this.settings_style2 = "none";
          jQuery(".gdpr_preview").css("color", "#3c4b64");
          this.button_readmore_link_color = "#007CBA";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#FFFFFF";
        } else if (this.banner_template == "banner-dark") {
          this.cookie_bar_color = "#262626";
          this.accept_background_color = "#6A8EE7";
          this.accept_all_background_color = "#6A8EE7";
          this.decline_background_color = "#808080";
          this.settings_background_color = "#262626";
          this.settings_border_width = "1";
          this.settings_border_color = "#808080";
          this.settings_style = "solid";
          this.cookie_bar_color1 = "#262626";
          this.cookie_text_color1 = "#ffffff";
          this.accept_background_color1 = "#6A8EE7";
          this.accept_all_background_color1 = "#6A8EE7";
          this.decline_background_color1 = "#808080";
          this.settings_background_color1 = "#262626";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#808080";
          this.settings_style1 = "solid";
          this.settings_text_color1 = "#808080";
          this.cookie_bar_color2 = "#262626";
          this.cookie_text_color2 = "#ffffff";
          this.accept_background_color2 = "#6A8EE7";
          this.accept_all_background_color2 = "#6A8EE7";
          this.decline_background_color2 = "#808080";
          this.settings_background_color2 = "#262626";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#808080";
          this.settings_style2 = "solid";
          this.settings_text_color2 = "#808080";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#6A8EE7";
          this.settings_text_color = "#808080";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#262626";
        }
      } else if (this.show_cookie_as === "popup") {
        this.popup_template = value;
        this.template = value;
        if (this.popup_template == "popup-dark_row") {
          this.cookie_bar_color = "#323742";
          this.accept_background_color = "#3EAF9A";
          this.accept_all_background_color = "#3EAF9A";
          jQuery(".gdpr_preview").css("color", "white");
          this.decline_background_color = "#333333";
          this.settings_background_color = "#323742";
          this.settings_border_width = "1";
          this.settings_border_color = "#3EAF9A";
          this.settings_style = "solid";
          this.decline_style = "none";
          this.button_readmore_link_color = "#3EAF9A";
          this.settings_text_color = "#3EAF9A";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#323742";
          this.cookie_bar_color1 = "#323742";
          this.cookie_text_color1 = "#ffffff";
          this.accept_background_color1 = "#3EAF9A";
          this.accept_all_background_color1 = "#3EAF9A";
          this.decline_background_color1 = "#333333";
          this.settings_background_color1 = "#323742";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#3EAF9A";
          this.settings_style1 = "solid";
          this.decline_style1 = "none";
          this.settings_text_color1 = "#3EAF9A";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_color2 = "#323742";
          this.cookie_text_color2 = "#ffffff";
          this.accept_background_color2 = "#3EAF9A";
          this.accept_all_background_color2 = "#3EAF9A";
          this.decline_background_color2 = "#333333";
          this.settings_background_color2 = "#323742";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#3EAF9A";
          this.settings_style2 = "solid";
          this.decline_style2 = "none";
          this.settings_text_color2 = "#3EAF9A";
          this.cookie_bar_border_radius2 = "0";
        } else if (this.popup_template == "popup-almond_column") {
          this.cookie_bar_color = "#E8DDBB";
          this.accept_background_color = "#DE7834";
          this.accept_all_background_color = "#DE7834";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.settings_style = "none";
          this.decline_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.cookie_bar_color1 = "#E8DDBB";
          this.cookie_text_color1 = "#ffffff";
          this.accept_background_color1 = "#DE7834";
          this.accept_all_background_color1 = "#DE7834";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.decline_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_color2 = "#E8DDBB";
          this.cookie_text_color2 = "#ffffff";
          this.accept_background_color2 = "#DE7834";
          this.accept_all_background_color2 = "#DE7834";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.decline_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
          this.cookie_bar_border_radius2 = "0";
          this.button_revoke_consent_text_color = "#306189";
          this.button_revoke_consent_background_color = "#E8DDBB";
        } else if (this.popup_template == "popup-grey_center") {
          this.cookie_bar_color = "#F4F4F4";
          this.accept_background_color = "#DE7834";
          this.accept_all_background_color = "#DE7834";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.button_readmore_link_color = "#DE7834";
          this.settings_style = "none";
          this.decline_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#F4F4F4";
          this.cookie_text_color1 = "#111111";
          this.accept_text_color1 = "#ffffff";
          this.cookie_text_color2 = "#111111";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color1 = "#F4F4F4";
          this.accept_background_color1 = "#DE7834";
          this.accept_all_background_color1 = "#DE7834";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_color2 = "#F4F4F4";
          this.accept_background_color2 = "#DE7834";
          this.accept_all_background_color2 = "#DE7834";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
          this.cookie_bar_border_radius2 = "0";
        } else if (this.popup_template == "popup-grey_column") {
          this.cookie_bar_color = "#F4F4F4";
          this.accept_background_color = "#E14469";
          this.accept_all_background_color = "#E14469";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.button_readmore_link_color = "#E14469";
          this.settings_style = "none";
          this.decline_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.cookie_bar_color1 = "#F4F4F4";
          this.cookie_text_color1 = "#1E3D59";
          this.accept_background_color1 = "#E14469";
          this.accept_all_background_color1 = "#E14469";
          this.accept_text_color1 = "#ffffff";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_text_color2 = "#1E3D59";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#F4F4F4";
          this.accept_background_color2 = "#E14469";
          this.accept_all_background_color2 = "#E14469";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#F4F4F4";
        } else if (this.popup_template == "popup-navy_blue_center") {
          this.cookie_bar_color = "#2A3E71";
          this.accept_background_color = "#369EE3";
          this.accept_all_background_color = "#369EE3";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#2A3E71";
          this.settings_border_width = "1";
          this.settings_border_color = "#FFFFFF";
          this.settings_style = "solid";
          this.decline_style = "none";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#369EE3";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#2A3E71";
          this.cookie_text_color1 = "#ffffff";
          this.accept_text_color1 = "#ffffff";
          this.cookie_bar_color1 = "#2A3E71";
          this.accept_background_color1 = "#369EE3";
          this.accept_all_background_color1 = "#369EE3";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#2A3E71";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#FFFFFF";
          this.settings_style1 = "solid";
          this.cookie_text_color2 = "#ffffff";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#2A3E71";
          this.accept_background_color2 = "#369EE3";
          this.accept_all_background_color2 = "#369EE3";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#2A3E71";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#FFFFFF";
          this.settings_style2 = "solid";
        } else if (this.popup_template == "popup-default") {
          this.cookie_bar_color = "#FFFFFF";
          this.accept_background_color = "#66CC66";
          this.accept_all_background_color = "#66CC66";
          this.decline_background_color = "#EF5454";
          this.settings_background_color = "#007CBA";
          this.settings_style = "none";
          this.decline_style = "none";
          jQuery(".gdpr_preview").css("color", "#3c4b64");
          this.button_readmore_link_color = "#007CBA";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#FFFFFF";
          this.cookie_bar_color1 = "#FFFFFF";
          this.cookie_text_color1 = "#111111";
          this.accept_background_color1 = "#66CC66";
          this.accept_all_background_color1 = "#66CC66";
          this.decline_background_color1 = "#EF5454";
          this.settings_background_color1 = "#007CBA";
          this.settings_style1 = "none";
          this.cookie_bar_color2 = "#FFFFFF";
          this.cookie_text_color2 = "#111111";
          this.accept_background_color2 = "#66CC66";
          this.accept_all_background_color2 = "#66CC66";
          this.decline_background_color2 = "#EF5454";
          this.settings_background_color2 = "#007CBA";
          this.settings_style2 = "none";
        } else if (this.popup_template == "popup-dark") {
          this.cookie_bar_color = "#262626";
          this.accept_background_color = "#6A8EE7";
          this.accept_all_background_color = "#6A8EE7";
          this.decline_background_color = "#808080";
          this.settings_background_color = "#262626";
          this.settings_border_width = "1";
          this.settings_border_color = "#808080";
          this.settings_style = "solid";
          this.decline_style = "none";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#6A8EE7";
          this.settings_text_color = "#808080";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#262626";
          this.cookie_bar_color1 = "#262626";
          this.cookie_text_color1 = "#ffffff";
          this.accept_background_color1 = "#6A8EE7";
          this.accept_all_background_color1 = "#6A8EE7";
          this.decline_background_color1 = "#808080";
          this.settings_background_color1 = "#262626";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#808080";
          this.settings_style1 = "solid";
          this.settings_text_color1 = "#808080";
          this.cookie_bar_color2 = "#262626";
          this.cookie_text_color2 = "#ffffff";
          this.accept_background_color2 = "#6A8EE7";
          this.accept_all_background_color2 = "#6A8EE7";
          this.decline_background_color2 = "#808080";
          this.settings_background_color2 = "#262626";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#808080";
          this.settings_style2 = "solid";
          this.settings_text_color2 = "#808080";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
        } else if (this.popup_template == "popup-navy_blue_square") {
          this.cookie_bar_color = "#2A3E71";
          this.accept_background_color = "#369EE3";
          this.accept_all_background_color = "#369EE3";
          this.decline_background_color = "#2A3E71";
          this.settings_background_color = "#2A3E71";
          this.settings_border_width = "1";
          this.settings_border_color = "#369EE3";
          this.settings_style = "solid";
          this.decline_style = "solid";
          this.decline_border_width = "1";
          this.decline_border_color = "#369EE3";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#369EE3";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#2A3E71";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.cookie_text_color1 = "#ffffff";
          this.accept_text_color1 = "#ffffff";
          this.cookie_bar_color1 = "#2A3E71";
          this.accept_background_color1 = "#369EE3";
          this.accept_all_background_color1 = "#369EE3";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#2A3E71";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#FFFFFF";
          this.settings_style1 = "solid";
          this.cookie_text_color2 = "#ffffff";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#2A3E71";
          this.accept_background_color2 = "#369EE3";
          this.accept_all_background_color2 = "#369EE3";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#2A3E71";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#FFFFFF";
          this.settings_style2 = "solid";
        } else if (this.popup_template == "popup-navy_blue_box") {
          this.cookie_bar_color = "#2A3E71";
          this.accept_background_color = "#369EE3";
          this.accept_all_background_color = "#369EE3";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#2A3E71";
          this.settings_border_width = "1";
          this.settings_border_color = "#FFFFFF";
          this.settings_style = "solid";
          this.decline_style = "none";
          this.cookie_bar_border_radius = "15";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#369EE3";
          this.settings_text_color = "#FFFFFF";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#2A3E71";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.cookie_text_color1 = "#ffffff";
          this.accept_text_color1 = "#ffffff";
          this.cookie_bar_color1 = "#2A3E71";
          this.accept_background_color1 = "#369EE3";
          this.accept_all_background_color1 = "#369EE3";
          this.decline_background_color1 = "#2A3E71";
          this.decline_border_width1 = "1";
          this.decline_border_color1 = "#FFFFFF";
          this.settings_background_color1 = "#2A3E71";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#FFFFFF";
          this.settings_style1 = "solid";
          this.cookie_text_color2 = "#ffffff";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#2A3E71";
          this.accept_background_color2 = "#369EE3";
          this.accept_all_background_color2 = "#369EE3";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#2A3E71";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#FFFFFF";
          this.settings_style2 = "solid";
        }
      } else if (this.show_cookie_as === "widget") {
        this.widget_template = value;
        this.template = value;
        if (this.widget_template == "widget-dark_row") {
          this.cookie_bar_color = "#323742";
          this.accept_background_color = "#3EAF9A";
          this.accept_all_background_color = "#3EAF9A";
          jQuery(".gdpr_preview").css("color", "white");
          this.decline_background_color = "#333333";
          this.settings_background_color = "#323742";
          this.settings_border_width = "1";
          this.settings_border_color = "#3EAF9A";
          this.settings_style = "solid";
          this.decline_style = "none";
          this.button_readmore_link_color = "#3EAF9A";
          this.settings_text_color = "#3EAF9A";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#323742";
          this.cookie_bar_color1 = "#323742";
          this.cookie_text_color1 = "#ffffff";
          this.accept_background_color1 = "#3EAF9A";
          this.accept_all_background_color1 = "#3EAF9A";
          this.decline_background_color1 = "#333333";
          this.settings_background_color1 = "#323742";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#3EAF9A";
          this.settings_style1 = "solid";
          this.decline_style1 = "none";
          this.settings_text_color1 = "#3EAF9A";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_color2 = "#323742";
          this.cookie_text_color2 = "#ffffff";
          this.accept_background_color2 = "#3EAF9A";
          this.accept_all_background_color2 = "#3EAF9A";
          this.decline_background_color2 = "#333333";
          this.settings_background_color2 = "#323742";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#3EAF9A";
          this.settings_style2 = "solid";
          this.decline_style2 = "none";
          this.settings_text_color2 = "#3EAF9A";
          this.cookie_bar_border_radius2 = "0";
        } else if (this.widget_template == "widget-almond_column") {
          this.cookie_bar_color = "#E8DDBB";
          this.accept_background_color = "#DE7834";
          this.accept_all_background_color = "#DE7834";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.settings_style = "none";
          this.decline_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#306189";
          this.button_revoke_consent_background_color = "#E8DDBB";
          this.cookie_bar_color1 = "#E8DDBB";
          this.cookie_text_color1 = "#ffffff";
          this.accept_background_color1 = "#DE7834";
          this.accept_all_background_color1 = "#DE7834";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.decline_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_color2 = "#E8DDBB";
          this.cookie_text_color2 = "#ffffff";
          this.accept_background_color2 = "#DE7834";
          this.accept_all_background_color2 = "#DE7834";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.decline_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
          this.cookie_bar_border_radius2 = "0";
        } else if (this.widget_template == "widget-grey_center") {
          this.cookie_bar_color = "#F4F4F4";
          this.accept_background_color = "#DE7834";
          this.accept_all_background_color = "#DE7834";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.button_readmore_link_color = "#DE7834";
          this.settings_style = "none";
          this.decline_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#F4F4F4";
          this.cookie_text_color1 = "#111111";
          this.accept_text_color1 = "#ffffff";
          this.cookie_text_color2 = "#111111";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color1 = "#F4F4F4";
          this.accept_background_color1 = "#DE7834";
          this.accept_all_background_color1 = "#DE7834";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_color2 = "#F4F4F4";
          this.accept_background_color2 = "#DE7834";
          this.accept_all_background_color2 = "#DE7834";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
          this.cookie_bar_border_radius2 = "0";
        } else if (this.widget_template == "widget-grey_column") {
          this.cookie_bar_color = "#F4F4F4";
          this.accept_background_color = "#E14469";
          this.accept_all_background_color = "#E14469";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.button_readmore_link_color = "#E14469";
          this.settings_style = "none";
          this.decline_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#F4F4F4";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.cookie_bar_color1 = "#F4F4F4";
          this.cookie_text_color1 = "#1E3D59";
          this.accept_background_color1 = "#E14469";
          this.accept_all_background_color1 = "#E14469";
          this.accept_text_color1 = "#ffffff";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_text_color2 = "#1E3D59";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#F4F4F4";
          this.accept_background_color2 = "#E14469";
          this.accept_all_background_color2 = "#E14469";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
        } else if (this.widget_template == "widget-navy_blue_center") {
          this.cookie_bar_color = "#2A3E71";
          this.accept_background_color = "#369EE3";
          this.accept_all_background_color = "#369EE3";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#2A3E71";
          this.settings_border_width = "1";
          this.settings_border_color = "#FFFFFF";
          this.settings_style = "solid";
          this.decline_style = "none";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#369EE3";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#2A3E71";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.cookie_text_color1 = "#ffffff";
          this.accept_text_color1 = "#ffffff";
          this.cookie_bar_color1 = "#2A3E71";
          this.accept_background_color1 = "#369EE3";
          this.accept_all_background_color1 = "#369EE3";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#2A3E71";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#FFFFFF";
          this.settings_style1 = "solid";
          this.cookie_text_color2 = "#ffffff";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#2A3E71";
          this.accept_background_color2 = "#369EE3";
          this.accept_all_background_color2 = "#369EE3";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#2A3E71";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#FFFFFF";
          this.settings_style2 = "solid";
        } else if (this.widget_template == "widget-default") {
          this.cookie_bar_color = "#FFFFFF";
          this.accept_background_color = "#66CC66";
          this.accept_all_background_color = "#66CC66";
          this.decline_background_color = "#EF5454";
          this.settings_background_color = "#007CBA";
          this.settings_style = "none";
          this.decline_style = "none";
          jQuery(".gdpr_preview").css("color", "#3c4b64");
          this.button_readmore_link_color = "#007CBA";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#FFFFFF";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.cookie_bar_color1 = "#FFFFFF";
          this.cookie_text_color1 = "#111111";
          this.accept_background_color1 = "#66CC66";
          this.accept_all_background_color1 = "#66CC66";
          this.decline_background_color1 = "#EF5454";
          this.settings_background_color1 = "#007CBA";
          this.settings_style1 = "none";
          this.cookie_bar_color2 = "#FFFFFF";
          this.cookie_text_color2 = "#111111";
          this.accept_background_color2 = "#66CC66";
          this.accept_all_background_color2 = "#66CC66";
          this.decline_background_color2 = "#EF5454";
          this.settings_background_color2 = "#007CBA";
          this.settings_style2 = "none";
        } else if (this.widget_template == "widget-dark") {
          this.cookie_bar_color = "#262626";
          this.accept_background_color = "#6A8EE7";
          this.accept_all_background_color = "#6A8EE7";
          this.decline_background_color = "#808080";
          this.settings_background_color = "#262626";
          this.settings_border_width = "1";
          this.settings_border_color = "#808080";
          this.settings_style = "solid";
          this.decline_style = "none";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#6A8EE7";
          this.settings_text_color = "#808080";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#262626";
          this.cookie_bar_color1 = "#262626";
          this.cookie_text_color1 = "#ffffff";
          this.accept_background_color1 = "#6A8EE7";
          this.accept_all_background_color1 = "#6A8EE7";
          this.decline_background_color1 = "#808080";
          this.settings_background_color1 = "#262626";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#808080";
          this.settings_style1 = "solid";
          this.settings_text_color1 = "#808080";
          this.cookie_bar_color2 = "#262626";
          this.cookie_text_color2 = "#ffffff";
          this.accept_background_color2 = "#6A8EE7";
          this.accept_all_background_color2 = "#6A8EE7";
          this.decline_background_color2 = "#808080";
          this.settings_background_color2 = "#262626";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#808080";
          this.settings_style2 = "solid";
          this.settings_text_color2 = "#808080";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
        } else if (this.widget_template == "widget-navy_blue_square") {
          this.cookie_bar_color = "#2A3E71";
          this.accept_background_color = "#369EE3";
          this.accept_all_background_color = "#369EE3";
          this.decline_background_color = "#2A3E71";
          this.settings_background_color = "#2A3E71";
          this.settings_border_width = "1";
          this.settings_border_color = "#369EE3";
          this.settings_style = "solid";
          this.decline_style = "solid";
          this.decline_border_width = "1";
          this.decline_border_color = "#369EE3";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#369EE3";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#2A3E71";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.cookie_text_color1 = "#ffffff";
          this.accept_text_color1 = "#ffffff";
          this.cookie_bar_color1 = "#2A3E71";
          this.accept_background_color1 = "#369EE3";
          this.accept_all_background_color1 = "#369EE3";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#2A3E71";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#FFFFFF";
          this.settings_style1 = "solid";
          this.cookie_text_color2 = "#ffffff";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#2A3E71";
          this.accept_background_color2 = "#369EE3";
          this.accept_all_background_color2 = "#369EE3";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#2A3E71";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#FFFFFF";
          this.settings_style2 = "solid";
        } else if (this.widget_template == "widget-navy_blue_box") {
          this.cookie_bar_color = "#2A3E71";
          this.accept_background_color = "#369EE3";
          this.accept_all_background_color = "#369EE3";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#2A3E71";
          this.settings_border_width = "1";
          this.settings_border_color = "#FFFFFF";
          this.settings_style = "solid";
          this.decline_style = "none";
          this.cookie_bar_border_radius = "15";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#369EE3";
          this.settings_text_color = "#FFFFFF";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#2A3E71";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.cookie_text_color1 = "#ffffff";
          this.accept_text_color1 = "#ffffff";
          this.cookie_bar_color1 = "#2A3E71";
          this.accept_background_color1 = "#369EE3";
          this.accept_all_background_color1 = "#369EE3";
          this.decline_background_color1 = "#2A3E71";
          this.decline_border_width1 = "1";
          this.decline_border_color1 = "#FFFFFF";
          this.settings_background_color1 = "#2A3E71";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#FFFFFF";
          this.settings_style1 = "solid";
          this.cookie_text_color2 = "#ffffff";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#2A3E71";
          this.accept_background_color2 = "#369EE3";
          this.accept_all_background_color2 = "#369EE3";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#2A3E71";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#FFFFFF";
          this.settings_style2 = "solid";
        }
      }
      this.is_template_changed = true;
    },
    onLanguageChange() {
      this.is_lang_changed = true;
    },
    cookieLayoutChange(value) {
      if (value) {
        this.settings_layout = true;
      } else {
        this.settings_layout = false;
      }
    },
    cookieLayoutChange1(value) {
      if (value) {
        this.settings_layout1 = true;
      } else {
        this.settings_layout1 = false;
      }
    },
    cookieLayoutChange2(value) {
      if (value) {
        this.settings_layout2 = true;
      } else {
        this.settings_layout2 = false;
      }
    },
    onSwitchButtonReadMoreIsOn() {
      this.button_readmore_is_on = !this.button_readmore_is_on;
    },
    onSwitchButtonReadMoreWpPage() {
      this.button_readmore_wp_page = !this.button_readmore_wp_page;
    },
    onSwitchButtonReadMoreNewWin() {
      this.button_readmore_new_win = !this.button_readmore_new_win;
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
      } else if (value === "ccpa") {
        this.is_ccpa = true;
        this.is_eprivacy = false;
        this.is_gdpr = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = true;
        this.show_revoke_card = false;
        this.iabtcf_is_on = false;
      } else if (value === "gdpr") {
        this.is_gdpr = true;
        this.is_ccpa = false;
        this.is_eprivacy = false;
        this.is_lgpd = false;
        this.show_revoke_card = true;
        this.show_visitor_conditions = true;
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
    onSwitchDefaultCookieBar() {
      this.default_cookie_bar = !this.default_cookie_bar;
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
      this.ab_testing_period = "30";
      this.cookie_bar_color = "#ffffff";
      this.cookie_bar_opacity = "0.80";
      this.cookie_bar_border_width = "0";
      this.border_style = "none";
      this.cookie_border_color = "#ffffff";
      this.cookie_bar_border_radius = "0";
      this.template = "banner-default";
      this.banner_template = "banner-default";
      this.popup_template = "popup-default";
      this.widget_template = "widget-default";
      this.accept_text = "Accept";
      this.accept_url = "#";
      this.accept_action = "#cookie_action_close_header";
      this.accept_text_color = "#ffffff";
      this.accept_background_color = "#18a300";
      this.open_url = false;
      this.iabtcf_is_on = false;
      this.accept_as_button = true;
      this.accept_size = "medium";
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
      this.accept_all_size = "medium";
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
      this.button_readmore_button_size = "medium";
      this.button_readmore_is_on = true;
      this.button_readmore_url_type = true;
      this.button_readmore_wp_page = false;
      this.button_readmore_page = "0";
      this.button_readmore_button_opacity = "1";
      this.button_readmore_button_border_width = "0";
      this.button_readmore_button_border_style = "none";
      this.button_readmore_button_border_color = "#333333";
      this.button_readmore_button_border_radius = "0";
      this.decline_text = "Decline";
      this.decline_url = "#";
      this.decline_action = "#cookie_action_settings";
      this.decline_text_color = "#ffffff";
      this.decline_background_color = "#333333";
      this.open_decline_url = false;
      this.decline_as_button = true;
      this.decline_size = "medium";
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
      this.settings_size = "medium";
      this.cookie_settings_on = true;
      this.cookie_on_frontend = true;
      this.settings_layout = false;
      this.layout_skin = "layout-default";
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
      this.confirm_size = "medium";
      this.confirm_opacity = "1";
      this.confirm_border_width = "0";
      this.confirm_style = "none";
      this.confirm_border_color = "#18a300";
      this.confirm_border_radius = "0";
      this.cancel_text = "Cancel";
      this.cancel_background_color = "#333333";
      this.cancel_text_color = "#ffffff";
      this.cancel_size = "medium";
      this.cancel_opacity = "1";
      this.cancel_border_width = "0";
      this.cancel_style = "none";
      this.cancel_border_color = "#333333";
      this.cancel_border_radius = "0";
      this.cookie_bar_color1 = "#ffffff";
      this.cookie_bar_opacity1 = "0.80";
      this.cookie_bar_border_width1 = "0";
      this.cookie_font1 = "inherit";
      this.cookie_text_color1 = "#000000";
      this.border_style1 = "none";
      this.cookie_border_color1 = "#ffffff";
      this.cookie_bar_border_radius1 = "0";
      this.accept_text1 = "Accept";
      this.accept_url1 = "#";
      this.accept_action1 = "#cookie_action_close_header";
      this.accept_text_color1 = "#ffffff";
      this.accept_background_color1 = "#18a300";
      this.open_url1 = false;
      this.accept_as_button1 = true;
      this.accept_size1 = "medium";
      this.cookie_accept_on1 = true;
      this.accept_opacity1 = "1";
      this.accept_border_width1 = "0";
      this.accept_style1 = "none";
      this.accept_border_color1 = "#18a300";
      this.accept_border_radius1 = "0";
      this.accept_all_button_popup1 = false;
      this.accept_all_text1 = "Accept All";
      this.accept_all_url1 = "#";
      this.accept_all_action1 = "#cookie_action_close_header";
      this.accept_all_text_color1 = "#ffffff";
      this.accept_all_background_color1 = "#18a300";
      this.accept_all_new_win1 = false;
      this.accept_all_as_button1 = true;
      this.accept_all_size1 = "medium";
      this.cookie_accept_all_on1 = false;
      this.accept_all_opacity1 = "1";
      this.accept_all_border_width1 = "0";
      this.accept_all_style1 = "none";
      this.accept_all_border_color1 = "#18a300";
      this.accept_all_border_radius1 = "0";
      this.decline_text1 = "Decline";
      this.decline_url1 = "#";
      this.decline_action1 = "#cookie_action_settings";
      this.decline_text_color1 = "#ffffff";
      this.decline_background_color1 = "#333333";
      this.open_decline_url1 = false;
      this.decline_as_button1 = true;
      this.decline_size1 = "medium";
      this.cookie_decline_on1 = true;
      this.decline_opacity1 = "1";
      this.decline_border_width1 = "0";
      this.decline_style1 = "none";
      this.decline_border_color1 = "#333333";
      this.decline_border_radius1 = "0";
      this.settings_text1 = "Cookie Settings";
      this.settings_text_color1 = "#ffffff";
      this.settings_background_color1 = "#333333";
      this.settings_as_button1 = true;
      this.settings_size1 = "medium";
      this.cookie_settings_on1 = true;
      this.cookie_on_frontend1 = true;
      this.settings_layout1 = false;
      this.layout_skin1 = "layout-default";
      this.settings_opacity1 = "1";
      this.settings_border_width1 = "0";
      this.settings_style1 = "none";
      this.settings_border_color1 = "#333333";
      this.settings_border_radius1 = "0";
      this.opt_out_text1 = "Do Not Sell My Personal Information";
      this.opt_out_text_color1 = "#359bf5";
      this.confirm_text1 = "Confirm";
      this.confirm_background_color1 = "#18a300";
      this.confirm_text_color1 = "#ffffff";
      this.confirm_size1 = "medium";
      this.confirm_opacity1 = "1";
      this.confirm_border_width1 = "0";
      this.confirm_style1 = "none";
      this.confirm_border_color1 = "#18a300";
      this.confirm_border_radius1 = "0";
      this.cancel_text1 = "Cancel";
      this.cancel_background_color1 = "#333333";
      this.cancel_text_color1 = "#ffffff";
      this.cancel_size1 = "medium";
      this.cancel_opacity1 = "1";
      this.cancel_border_width1 = "0";
      this.cancel_style1 = "none";
      this.cancel_border_color1 = "#333333";
      this.cancel_border_radius1 = "0";
      this.cookie_bar_color2 = "#ffffff";
      this.cookie_font2 = "inherit";
      this.cookie_text_color2 = "#000000";
      this.cookie_bar_opacity2 = "0.80";
      this.cookie_bar_border_width2 = "0";
      this.border_style2 = "none";
      this.cookie_border_color2 = "#ffffff";
      this.cookie_bar_border_radius2 = "0";
      this.accept_text2 = "Accept";
      this.accept_url2 = "#";
      this.accept_action2 = "#cookie_action_close_header";
      this.accept_text_color2 = "#ffffff";
      this.accept_background_color2 = "#18a300";
      this.open_url2 = false;
      this.accept_as_button2 = true;
      this.accept_size2 = "medium";
      this.cookie_accept_on2 = true;
      this.accept_opacity2 = "1";
      this.accept_border_width2 = "0";
      this.accept_style2 = "none";
      this.accept_border_color2 = "#18a300";
      this.accept_border_radius2 = "0";
      this.accept_all_button_popup2 = false;
      this.accept_all_text2 = "Accept All";
      this.accept_all_url2 = "#";
      this.accept_all_action2 = "#cookie_action_close_header";
      this.accept_all_text_color2 = "#ffffff";
      this.accept_all_background_color2 = "#18a300";
      this.accept_all_new_win2 = false;
      this.accept_all_as_button2 = true;
      this.accept_all_size2 = "medium";
      this.cookie_accept_all_on2 = false;
      this.accept_all_opacity2 = "1";
      this.accept_all_border_width2 = "0";
      this.accept_all_style2 = "none";
      this.accept_all_border_color2 = "#18a300";
      this.accept_all_border_radius2 = "0";
      this.decline_text2 = "Decline";
      this.decline_url2 = "#";
      this.decline_action2 = "#cookie_action_settings";
      this.decline_text_color2 = "#ffffff";
      this.decline_background_color2 = "#333333";
      this.open_decline_url2 = false;
      this.decline_as_button2 = true;
      this.decline_size2 = "medium";
      this.cookie_decline_on2 = true;
      this.decline_opacity2 = "1";
      this.decline_border_width2 = "0";
      this.decline_style2 = "none";
      this.decline_border_color2 = "#333333";
      this.decline_border_radius2 = "0";
      this.settings_text2 = "Cookie Settings";
      this.settings_text_color2 = "#ffffff";
      this.settings_background_color2 = "#333333";
      this.settings_as_button2 = true;
      this.settings_size2 = "medium";
      this.cookie_settings_on2 = true;
      this.cookie_on_frontend2 = true;
      this.settings_layout2 = false;
      this.layout_skin2 = "layout-default";
      this.settings_opacity2 = "1";
      this.settings_border_width2 = "0";
      this.settings_style2 = "none";
      this.settings_border_color2 = "#333333";
      this.settings_border_radius2 = "0";
      this.opt_out_text2 = "Do Not Sell My Personal Information";
      this.opt_out_text_color2 = "#359bf5";
      this.confirm_text2 = "Confirm";
      this.confirm_background_color2 = "#18a300";
      this.confirm_text_color2 = "#ffffff";
      this.confirm_size2 = "medium";
      this.confirm_opacity2 = "1";
      this.confirm_border_width2 = "0";
      this.confirm_style2 = "none";
      this.confirm_border_color2 = "#18a300";
      this.confirm_border_radius2 = "0";
      this.cancel_text2 = "Cancel";
      this.cancel_background_color2 = "#333333";
      this.cancel_text_color2 = "#ffffff";
      this.cancel_size2 = "medium";
      this.cancel_opacity2 = "1";
      this.cancel_border_width2 = "0";
      this.cancel_style2 = "none";
      this.cancel_border_color2 = "#333333";
      this.cancel_border_radius2 = "0";
      this.cookie_font = "inherit";
      this.cookie_is_on = true;
      this.is_eu_on = false;
      this.is_ccpa_on = false;
      this.is_iab_on = false;
      this.selectedRadioIab = "no";
      this.selectedRadioGdpr = "no";
      this.selectedRadioCcpa = "no";
      this.selectedRadioLgpd = "no";
      this.logging_on = true;
      this.show_credits = false;
      this.autotick = false;
      this.is_revoke_consent_on = true;
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
      this.tab_text = "Cookie Settings";
      this.tab_margin = "5";
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
      this.is_selectedCountry_on = false;
      this.selectedRadioWorldWide = false;
      this.is_worldwide_on = false;
      this.list_of_countries = [];
      this.select_countries = [];
      this.select_countries_array = [];
      this.show_Select_Country = false;
      this.cookie_font1 = "inherit";
      this.cookie_text_color1 = "#000000";
      this.cookie_font2 = "inherit";
      this.cookie_text_color2 = "#000000";
      this.cookie_list_tab = true;
      this.discovered_cookies_list_tab = false;
      this.scan_history_list_tab = false;
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
    onChangeCookieListTab() {
      this.cookie_list_tab = true;
      this.discovered_cookies_list_tab = false;
      this.scan_history_list_tab = false;
      window.location.hash = "#cookie_settings#cookie_list#custom_cookie";
    },
    onChangeDiscoveredListTab() {
      this.cookie_list_tab = false;
      this.discovered_cookies_list_tab = true;
      this.scan_history_list_tab = false;
      window.location.hash = "#cookie_settings#cookie_list#discovered_cookies";
    },
    onChangeScanHistoryTab() {
      this.cookie_list_tab = false;
      this.discovered_cookies_list_tab = false;
      this.scan_history_list_tab = true;
      window.location.hash = "#cookie_settings#cookie_list#scan_history";
    },
    activateTabFromHash() {
      const hash = window.location.hash;
      if (hash === "#cookie_settings#cookie_list#custom_cookie") {
        this.onChangeCookieListTab();
      } else if (hash === "#cookie_settings#cookie_list#discovered_cookies") {
        this.onChangeDiscoveredListTab();
      } else if (hash === "#cookie_settings#cookie_list#scan_history") {
        this.onChangeScanHistoryTab();
      }
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

    openMediaModal() {
      var image_frame = wp.media({
        title: "Select Media from here",
        multiple: false,
        library: {
          type: "image",
        },
      });
      jQuery("#image-upload-button")
        .unbind()
        .click(
          image_frame.on("close", function () {
            var selection = image_frame.state().get("selection");
            selection.each(function (attachment) {
              jQuery("#gdpr-cookie-bar-logo-holder").attr(
                "src",
                attachment.attributes.url
              );
              jQuery("#gdpr-cookie-bar-logo-holder1").attr(
                "src",
                attachment.attributes.url
              );
              jQuery("#gdpr-cookie-bar-logo-holder2").attr(
                "src",
                attachment.attributes.url
              );
              jQuery("#gdpr-cookie-bar-logo-url-holder").attr(
                "value",
                attachment.attributes.url
              );
              jQuery("#gdpr-cookie-bar-logo-url-holder1").attr(
                "value",
                attachment.attributes.url
              );
              jQuery("#gdpr-cookie-bar-logo-url-holder2").attr(
                "value",
                attachment.attributes.url
              );
            });
          }),
          image_frame.open()
        );
    },
    deleteSelectedimage() {
      jQuery("#gdpr-cookie-bar-logo-holder").removeAttr("src");
      jQuery("#gdpr-cookie-bar-logo-holder1").removeAttr("src");
      jQuery("#gdpr-cookie-bar-logo-holder2").removeAttr("src");
      jQuery("#gdpr-cookie-bar-logo-url-holder").attr("value", "");
      jQuery("#gdpr-cookie-bar-logo-url-holder1").attr("value", "");
      jQuery("#gdpr-cookie-bar-logo-url-holder2").attr("value", "");

      this.is_logo_removed = true;
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
    onSwitchABTestingEnable() {
      this.ab_testing_enabled = !this.ab_testing_enabled;
      if (this.ab_testing_enabled === false) this.active_test_banner_tab = 1;
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
  },
  icons: { cilPencil, cilSettings, cilInfo, cibGoogleKeep },
});
var gen = new Vue({
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
      is_logo_removed: false,
      appendField: ".gdpr-cookie-consent-settings-container",
      configure_image_url: require("../admin/images/configure-icon.png"),
      closeOnBackdrop: true,
      centered: true,
      accept_button_popup: false,
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
      iabtcf_msg: `We and our <a id = "vendor-link" href = "#" data-toggle = "gdprmodal" data-target = "#gdpr-gdprmodal">836 partners</a> use cookies and other tracking technologies to improve your experience on our website. We may store and/or access information on a device and process personal data, such as your IP address and browsing data, for personalised advertising and content, advertising and content measurement, audience research and services development. Additionally, we may utilize precise geolocation data and identification through device scanning.\n\nPlease note that your consent will be valid across all our subdomains. You can change or withdraw your consent at any time by clicking the “Consent Preferences” button at the bottom of your screen. We respect your choices and are committed to providing you with a transparent and secure browsing experience.`,
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
      selectedRadioGdpr:
        settings_obj.the_options.hasOwnProperty("is_eu_on") &&
        (true === settings_obj.the_options["is_eu_on"] ||
          1 === settings_obj.the_options["is_eu_on"])
          ? "yes"
          : "no",
      selectedRadioCcpa:
        settings_obj.the_options.hasOwnProperty("is_ccpa_on") &&
        (true === settings_obj.the_options["is_ccpa_on"] ||
          1 === settings_obj.the_options["is_ccpa_on"])
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
      tab_position_options: settings_obj.tab_position_options,
      tab_position: settings_obj.the_options.hasOwnProperty(
        "show_again_position"
      )
        ? settings_obj.the_options["show_again_position"]
        : "right",
      tab_margin: settings_obj.the_options.hasOwnProperty("show_again_margin")
        ? settings_obj.the_options["show_again_margin"]
        : "5",
      tab_text: settings_obj.the_options.hasOwnProperty("show_again_text")
        ? settings_obj.the_options["show_again_text"]
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
      show_credits:
        settings_obj.the_options.hasOwnProperty("show_credits") &&
        (true === settings_obj.the_options["show_credits"] ||
          1 === settings_obj.the_options["show_credits"])
          ? true
          : false,
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
      button_readmore_button_size: settings_obj.the_options.hasOwnProperty(
        "button_readmore_button_size"
      )
        ? settings_obj.the_options["button_readmore_button_size"]
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
      accept_size_options: settings_obj.accept_size_options,
      accept_size: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_size"
      )
        ? settings_obj.the_options["button_accept_button_size"]
        : "medium",
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
      decline_size: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_size"
      )
        ? settings_obj.the_options["button_decline_button_size"]
        : "medium",
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
      settings_layout_options: settings_obj.settings_layout_options,
      settings_layout_options_extended:
        settings_obj.settings_layout_options_extended,
      settings_layout:
        settings_obj.the_options.hasOwnProperty("button_settings_as_popup") &&
        (true === settings_obj.the_options["button_settings_as_popup"] ||
          1 === settings_obj.the_options["button_settings_as_popup"])
          ? true
          : false,
      is_banner: this.show_cookie_as === "banner" ? true : false,
      layout_skin_options: settings_obj.layout_skin_options,
      layout_skin: settings_obj.the_options.hasOwnProperty(
        "button_settings_layout_skin"
      )
        ? settings_obj.the_options["button_settings_layout_skin"]
        : "layout-default",
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
      settings_size: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_size"
      )
        ? settings_obj.the_options["button_settings_button_size"]
        : "medium",
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
      confirm_size: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_size"
      )
        ? settings_obj.the_options["button_confirm_button_size"]
        : "medium",
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
      cancel_size: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_size"
      )
        ? settings_obj.the_options["button_cancel_button_size"]
        : "medium",
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
      accept_size1: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_size1"
      )
        ? settings_obj.the_options["button_accept_button_size1"]
        : "medium",
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
      accept_all_size1: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_button_size1"
      )
        ? settings_obj.the_options["button_accept_all_button_size1"]
        : "medium",
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
      decline_size1: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_size1"
      )
        ? settings_obj.the_options["button_decline_button_size1"]
        : "medium",
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

      settings_layout1:
        settings_obj.the_options.hasOwnProperty("button_settings_as_popup1") &&
        (true === settings_obj.the_options["button_settings_as_popup1"] ||
          1 === settings_obj.the_options["button_settings_as_popup1"] ||
          "true" === settings_obj.the_options["button_settings_as_popup1"])
          ? true
          : false,
      layout_skin1: settings_obj.the_options.hasOwnProperty(
        "button_settings_layout_skin1"
      )
        ? settings_obj.the_options["button_settings_layout_skin1"]
        : "layout-default",
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
      settings_size1: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_size1"
      )
        ? settings_obj.the_options["button_settings_button_size1"]
        : "medium",
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
      confirm_size1: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_size1"
      )
        ? settings_obj.the_options["button_confirm_button_size1"]
        : "medium",
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
      cancel_size1: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_size1"
      )
        ? settings_obj.the_options["button_cancel_button_size1"]
        : "medium",
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
      accept_size2: settings_obj.the_options.hasOwnProperty(
        "button_accept_button_size2"
      )
        ? settings_obj.the_options["button_accept_button_size2"]
        : "medium",
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
      accept_all_size2: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_button_size2"
      )
        ? settings_obj.the_options["button_accept_all_button_size2"]
        : "medium",
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
      decline_size2: settings_obj.the_options.hasOwnProperty(
        "button_decline_button_size2"
      )
        ? settings_obj.the_options["button_decline_button_size2"]
        : "medium",
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

      settings_layout2:
        settings_obj.the_options.hasOwnProperty("button_settings_as_popup2") &&
        (true === settings_obj.the_options["button_settings_as_popup2"] ||
          1 === settings_obj.the_options["button_settings_as_popup2"] ||
          "true" === settings_obj.the_options["button_settings_as_popup2"])
          ? true
          : false,
      layout_skin2: settings_obj.the_options.hasOwnProperty(
        "button_settings_layout_skin2"
      )
        ? settings_obj.the_options["button_settings_layout_skin2"]
        : "layout-default",
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
      settings_size2: settings_obj.the_options.hasOwnProperty(
        "button_settings_button_size2"
      )
        ? settings_obj.the_options["button_settings_button_size2"]
        : "medium",
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
      confirm_size2: settings_obj.the_options.hasOwnProperty(
        "button_confirm_button_size2"
      )
        ? settings_obj.the_options["button_confirm_button_size2"]
        : "medium",
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
      cancel_size2: settings_obj.the_options.hasOwnProperty(
        "button_cancel_button_size2"
      )
        ? settings_obj.the_options["button_cancel_button_size2"]
        : "medium",
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
        : "banner-default",
      banner_template: settings_obj.the_options.hasOwnProperty(
        "banner_template"
      )
        ? settings_obj.the_options["banner_template"]
        : "banner-default",
      popup_template: settings_obj.the_options.hasOwnProperty("popup_template")
        ? settings_obj.the_options["popup_template"]
        : "popup-default",
      widget_template: settings_obj.the_options.hasOwnProperty(
        "widget_template"
      )
        ? settings_obj.the_options["widget_template"]
        : "widget-default",
      show_banner_template:
        settings_obj.the_options.hasOwnProperty("show_cookie_as") &&
        settings_obj.the_options["show_cookie_as"] === "banner"
          ? true
          : false,
      show_popup_template:
        settings_obj.the_options.hasOwnProperty("show_cookie_as") &&
        settings_obj.the_options["show_cookie_as"] === "popup"
          ? true
          : false,
      show_widget_template:
        settings_obj.the_options.hasOwnProperty("show_cookie_as") &&
        settings_obj.the_options["show_cookie_as"] === "widget"
          ? true
          : false,
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
      accept_all_size: settings_obj.the_options.hasOwnProperty(
        "button_accept_all_button_size"
      )
        ? settings_obj.the_options["button_accept_all_button_size"]
        : "medium",
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
      right_arrow: require("../admin/images/dashboard-icons/right-arrow.svg"),
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
      if (this.show_cookie_as === "banner") {
        this.show_banner_template = true;
        this.show_popup_template = false;
        this.show_widget_template = false;
      } else if (this.show_cookie_as === "popup") {
        this.show_banner_template = false;
        this.show_popup_template = true;
        this.show_widget_template = false;
      } else if (this.show_cookie_as === "widget") {
        this.show_banner_template = false;
        this.show_popup_template = false;
        this.show_widget_template = true;
      }
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
      //changing the value of banner_preview_swicth_value enable/disable
      this.banner_preview_is_on = !this.banner_preview_is_on;
    },
    onSwitchDntEnable() {
      //changing the value of do_not_track_on enable/disable
      this.do_not_track_on = !this.do_not_track_on;
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
    onSwitchEUEnable(value) {
      this.is_eu_on = !this.is_eu_on;
      if (value) {
        this.selectedRadioGdpr = value === "yes" ? "yes" : "no";
      }
    },
    onSwitchCCPAEnable(value) {
      this.is_ccpa_on = !this.is_ccpa_on;
      if (value) {
        this.selectedRadioCcpa = value === "yes" ? "yes" : "no";
      }
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
    onEnablesafeSwitch() {
      if (this.enable_safe === "true") {
        this.is_eu_on = "no";
      } else {
        this.is_eu_on = "yes";
      }
    },
    onEnablesafeSwitchCCPA() {
      if (this.enable_safe === "true") {
        this.is_ccpa_on = "no";
      } else {
        this.is_ccpa_on = "yes";
      }
    },
    onSwitchRevokeConsentEnable() {
      this.is_revoke_consent_on = !this.is_revoke_consent_on;
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
      this.onEnablesafeSwitchCCPA();
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
      if (value === "banner") {
        this.is_banner = true;
        this.show_banner_template = true;
        this.show_popup_template = false;
        this.show_widget_template = false;
        this.template = this.banner_template;
      } else {
        this.is_banner = false;
        if (value === "popup") {
          this.show_banner_template = false;
          this.show_popup_template = true;
          this.show_widget_template = false;
          this.template = this.popup_template;
        } else if (value === "widget") {
          this.show_banner_template = false;
          this.show_popup_template = false;
          this.show_widget_template = true;
          this.template = this.widget_template;
        }
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
    onTemplateChange(value) {
      if (this.show_cookie_as === "banner") {
        this.banner_template = value;
        this.template = value;
        if (this.banner_template == "banner-dark_row") {
          this.cookie_bar_color = "#323742";
          this.cookie_bar_color1 = "#323742";
          this.cookie_bar_color2 = "#323742";
          this.accept_background_color = "#3EAF9A";
          this.accept_all_background_color = "#3EAF9A";
          jQuery(".gdpr_preview").css("color", "white");
          this.decline_background_color = "#333333";
          this.settings_background_color = "#323742";
          this.settings_border_width = "1";
          this.settings_border_color = "#3EAF9A";
          this.settings_style = "solid";
          this.button_readmore_link_color = "#3EAF9A";
          this.settings_text_color = "#3EAF9A";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#323742";
          this.cookie_text_color1 = "#ffffff";
          this.accept_text_color1 = "#ffffff";
          this.accept_background_color1 = "#3EAF9A";
          this.accept_all_background_color1 = "#3EAF9A";
          this.decline_background_color1 = "#333333";
          this.settings_background_color1 = "#323742";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#3EAF9A";
          this.settings_style1 = "solid";
          this.button_readmore_link_color1 = "#3EAF9A";
          this.settings_text_color1 = "#3EAF9A";
          this.cookie_text_color2 = "#ffffff";
          this.accept_text_color2 = "#ffffff";
          this.accept_background_color2 = "#3EAF9A";
          this.accept_all_background_color2 = "#3EAF9A";
          this.decline_background_color2 = "#333333";
          this.settings_background_color2 = "#323742";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#3EAF9A";
          this.settings_style2 = "solid";
          this.button_readmore_link_color2 = "#3EAF9A";
          this.settings_text_color2 = "#3EAF9A";
        } else if (this.banner_template == "banner-almond_column") {
          this.cookie_bar_color = "#E8DDBB";
          this.accept_background_color = "#DE7834";
          this.accept_all_background_color = "#DE7834";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.settings_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.button_revoke_consent_text_color = "#306189";
          this.button_revoke_consent_background_color = "#E8DDBB";
          this.cookie_text_color1 = "#111111";
          this.accept_text_color1 = "#ffffff";
          this.cookie_text_color2 = "#111111";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color1 = "#E8DDBB";
          this.accept_background_color1 = "#DE7834";
          this.accept_all_background_color1 = "#DE7834";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_bar_color2 = "#E8DDBB";
          this.accept_background_color2 = "#DE7834";
          this.accept_all_background_color2 = "#DE7834";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
        } else if (this.banner_template == "banner-grey_center") {
          this.cookie_bar_color = "#F4F4F4";
          this.accept_background_color = "#DE7834";
          this.accept_all_background_color = "#DE7834";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.cookie_text_color1 = "#111111";
          this.accept_text_color1 = "#ffffff";
          this.cookie_text_color2 = "#111111";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color1 = "#F4F4F4";
          this.accept_background_color1 = "#DE7834";
          this.accept_all_background_color1 = "#DE7834";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_bar_color2 = "#F4F4F4";
          this.accept_background_color2 = "#DE7834";
          this.accept_all_background_color2 = "#DE7834";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
          this.button_readmore_link_color = "#DE7834";
          this.settings_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#F4F4F4";
        } else if (this.banner_template == "banner-grey_column") {
          this.cookie_bar_color = "#F4F4F4";
          this.accept_background_color = "#E14469";
          this.accept_all_background_color = "#E14469";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.button_readmore_link_color = "#E14469";
          this.settings_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_color1 = "#F4F4F4";
          this.cookie_text_color1 = "#1E3D59";
          this.accept_background_color1 = "#E14469";
          this.accept_all_background_color1 = "#E14469";
          this.accept_text_color1 = "#ffffff";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_text_color2 = "#1E3D59";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#F4F4F4";
          this.accept_background_color2 = "#E14469";
          this.accept_all_background_color2 = "#E14469";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#F4F4F4";
        } else if (this.banner_template == "banner-navy_blue_center") {
          this.cookie_bar_color = "#2A3E71";
          this.accept_background_color = "#369EE3";
          this.accept_all_background_color = "#369EE3";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#2A3E71";
          this.settings_border_width = "1";
          this.settings_border_color = "#FFFFFF";
          this.settings_style = "solid";
          this.cookie_text_color1 = "#ffffff";
          this.accept_text_color1 = "#ffffff";
          this.cookie_bar_color1 = "#2A3E71";
          this.accept_background_color1 = "#369EE3";
          this.accept_all_background_color1 = "#369EE3";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#2A3E71";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#FFFFFF";
          this.settings_style1 = "solid";
          this.cookie_text_color2 = "#ffffff";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#2A3E71";
          this.accept_background_color2 = "#369EE3";
          this.accept_all_background_color2 = "#369EE3";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#2A3E71";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#FFFFFF";
          this.settings_style2 = "solid";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#369EE3";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#2A3E71";
        } else if (this.banner_template == "banner-default") {
          this.cookie_bar_color = "#FFFFFF";
          this.accept_background_color = "#66CC66";
          this.accept_all_background_color = "#66CC66";
          this.decline_background_color = "#EF5454";
          this.settings_background_color = "#007CBA";
          this.settings_style = "none";
          this.cookie_bar_color1 = "#FFFFFF";
          this.cookie_text_color1 = "#111111";
          this.accept_background_color1 = "#66CC66";
          this.accept_all_background_color1 = "#66CC66";
          this.decline_background_color1 = "#EF5454";
          this.settings_background_color1 = "#007CBA";
          this.settings_style1 = "none";
          this.cookie_bar_color2 = "#FFFFFF";
          this.cookie_text_color1 = "#111111";
          this.accept_background_color2 = "#66CC66";
          this.accept_all_background_color2 = "#66CC66";
          this.decline_background_color2 = "#EF5454";
          this.settings_background_color2 = "#007CBA";
          this.settings_style2 = "none";
          jQuery(".gdpr_preview").css("color", "#3c4b64");
          this.button_readmore_link_color = "#007CBA";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#FFFFFF";
        } else if (this.banner_template == "banner-dark") {
          this.cookie_bar_color = "#262626";
          this.accept_background_color = "#6A8EE7";
          this.accept_all_background_color = "#6A8EE7";
          this.decline_background_color = "#808080";
          this.settings_background_color = "#262626";
          this.settings_border_width = "1";
          this.settings_border_color = "#808080";
          this.settings_style = "solid";
          this.cookie_bar_color1 = "#262626";
          this.cookie_text_color1 = "#ffffff";
          this.accept_background_color1 = "#6A8EE7";
          this.accept_all_background_color1 = "#6A8EE7";
          this.decline_background_color1 = "#808080";
          this.settings_background_color1 = "#262626";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#808080";
          this.settings_style1 = "solid";
          this.settings_text_color1 = "#808080";
          this.cookie_bar_color2 = "#262626";
          this.cookie_text_color2 = "#ffffff";
          this.accept_background_color2 = "#6A8EE7";
          this.accept_all_background_color2 = "#6A8EE7";
          this.decline_background_color2 = "#808080";
          this.settings_background_color2 = "#262626";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#808080";
          this.settings_style2 = "solid";
          this.settings_text_color2 = "#808080";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#6A8EE7";
          this.settings_text_color = "#808080";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#262626";
        }
      } else if (this.show_cookie_as === "popup") {
        this.popup_template = value;
        this.template = value;
        if (this.popup_template == "popup-dark_row") {
          this.cookie_bar_color = "#323742";
          this.accept_background_color = "#3EAF9A";
          this.accept_all_background_color = "#3EAF9A";
          jQuery(".gdpr_preview").css("color", "white");
          this.decline_background_color = "#333333";
          this.settings_background_color = "#323742";
          this.settings_border_width = "1";
          this.settings_border_color = "#3EAF9A";
          this.settings_style = "solid";
          this.decline_style = "none";
          this.button_readmore_link_color = "#3EAF9A";
          this.settings_text_color = "#3EAF9A";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#323742";
          this.cookie_bar_color1 = "#323742";
          this.cookie_text_color1 = "#ffffff";
          this.accept_background_color1 = "#3EAF9A";
          this.accept_all_background_color1 = "#3EAF9A";
          this.decline_background_color1 = "#333333";
          this.settings_background_color1 = "#323742";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#3EAF9A";
          this.settings_style1 = "solid";
          this.decline_style1 = "none";
          this.settings_text_color1 = "#3EAF9A";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_color2 = "#323742";
          this.cookie_text_color2 = "#ffffff";
          this.accept_background_color2 = "#3EAF9A";
          this.accept_all_background_color2 = "#3EAF9A";
          this.decline_background_color2 = "#333333";
          this.settings_background_color2 = "#323742";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#3EAF9A";
          this.settings_style2 = "solid";
          this.decline_style2 = "none";
          this.settings_text_color2 = "#3EAF9A";
          this.cookie_bar_border_radius2 = "0";
        } else if (this.popup_template == "popup-almond_column") {
          this.cookie_bar_color = "#E8DDBB";
          this.accept_background_color = "#DE7834";
          this.accept_all_background_color = "#DE7834";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.settings_style = "none";
          this.decline_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.cookie_bar_color1 = "#E8DDBB";
          this.cookie_text_color1 = "#ffffff";
          this.accept_background_color1 = "#DE7834";
          this.accept_all_background_color1 = "#DE7834";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.decline_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_color2 = "#E8DDBB";
          this.cookie_text_color2 = "#ffffff";
          this.accept_background_color2 = "#DE7834";
          this.accept_all_background_color2 = "#DE7834";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.decline_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
          this.cookie_bar_border_radius2 = "0";
          this.button_revoke_consent_text_color = "#306189";
          this.button_revoke_consent_background_color = "#E8DDBB";
        } else if (this.popup_template == "popup-grey_center") {
          this.cookie_bar_color = "#F4F4F4";
          this.accept_background_color = "#DE7834";
          this.accept_all_background_color = "#DE7834";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.button_readmore_link_color = "#DE7834";
          this.settings_style = "none";
          this.decline_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#F4F4F4";
          this.cookie_text_color1 = "#111111";
          this.accept_text_color1 = "#ffffff";
          this.cookie_text_color2 = "#111111";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color1 = "#F4F4F4";
          this.accept_background_color1 = "#DE7834";
          this.accept_all_background_color1 = "#DE7834";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_color2 = "#F4F4F4";
          this.accept_background_color2 = "#DE7834";
          this.accept_all_background_color2 = "#DE7834";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
          this.cookie_bar_border_radius2 = "0";
        } else if (this.popup_template == "popup-grey_column") {
          this.cookie_bar_color = "#F4F4F4";
          this.accept_background_color = "#E14469";
          this.accept_all_background_color = "#E14469";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.button_readmore_link_color = "#E14469";
          this.settings_style = "none";
          this.decline_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.cookie_bar_color1 = "#F4F4F4";
          this.cookie_text_color1 = "#1E3D59";
          this.accept_background_color1 = "#E14469";
          this.accept_all_background_color1 = "#E14469";
          this.accept_text_color1 = "#ffffff";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_text_color2 = "#1E3D59";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#F4F4F4";
          this.accept_background_color2 = "#E14469";
          this.accept_all_background_color2 = "#E14469";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#F4F4F4";
        } else if (this.popup_template == "popup-navy_blue_center") {
          this.cookie_bar_color = "#2A3E71";
          this.accept_background_color = "#369EE3";
          this.accept_all_background_color = "#369EE3";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#2A3E71";
          this.settings_border_width = "1";
          this.settings_border_color = "#FFFFFF";
          this.settings_style = "solid";
          this.decline_style = "none";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#369EE3";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#2A3E71";
          this.cookie_text_color1 = "#ffffff";
          this.accept_text_color1 = "#ffffff";
          this.cookie_bar_color1 = "#2A3E71";
          this.accept_background_color1 = "#369EE3";
          this.accept_all_background_color1 = "#369EE3";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#2A3E71";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#FFFFFF";
          this.settings_style1 = "solid";
          this.cookie_text_color2 = "#ffffff";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#2A3E71";
          this.accept_background_color2 = "#369EE3";
          this.accept_all_background_color2 = "#369EE3";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#2A3E71";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#FFFFFF";
          this.settings_style2 = "solid";
        } else if (this.popup_template == "popup-default") {
          this.cookie_bar_color = "#FFFFFF";
          this.accept_background_color = "#66CC66";
          this.accept_all_background_color = "#66CC66";
          this.decline_background_color = "#EF5454";
          this.settings_background_color = "#007CBA";
          this.settings_style = "none";
          this.decline_style = "none";
          jQuery(".gdpr_preview").css("color", "#3c4b64");
          this.button_readmore_link_color = "#007CBA";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#FFFFFF";
          this.cookie_bar_color1 = "#FFFFFF";
          this.cookie_text_color1 = "#111111";
          this.accept_background_color1 = "#66CC66";
          this.accept_all_background_color1 = "#66CC66";
          this.decline_background_color1 = "#EF5454";
          this.settings_background_color1 = "#007CBA";
          this.settings_style1 = "none";
          this.cookie_bar_color2 = "#FFFFFF";
          this.cookie_text_color2 = "#111111";
          this.accept_background_color2 = "#66CC66";
          this.accept_all_background_color2 = "#66CC66";
          this.decline_background_color2 = "#EF5454";
          this.settings_background_color2 = "#007CBA";
          this.settings_style2 = "none";
        } else if (this.popup_template == "popup-dark") {
          this.cookie_bar_color = "#262626";
          this.accept_background_color = "#6A8EE7";
          this.accept_all_background_color = "#6A8EE7";
          this.decline_background_color = "#808080";
          this.settings_background_color = "#262626";
          this.settings_border_width = "1";
          this.settings_border_color = "#808080";
          this.settings_style = "solid";
          this.decline_style = "none";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#6A8EE7";
          this.settings_text_color = "#808080";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#262626";
          this.cookie_bar_color1 = "#262626";
          this.cookie_text_color1 = "#ffffff";
          this.accept_background_color1 = "#6A8EE7";
          this.accept_all_background_color1 = "#6A8EE7";
          this.decline_background_color1 = "#808080";
          this.settings_background_color1 = "#262626";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#808080";
          this.settings_style1 = "solid";
          this.settings_text_color1 = "#808080";
          this.cookie_bar_color2 = "#262626";
          this.cookie_text_color2 = "#ffffff";
          this.accept_background_color2 = "#6A8EE7";
          this.accept_all_background_color2 = "#6A8EE7";
          this.decline_background_color2 = "#808080";
          this.settings_background_color2 = "#262626";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#808080";
          this.settings_style2 = "solid";
          this.settings_text_color2 = "#808080";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
        } else if (this.popup_template == "popup-navy_blue_square") {
          this.cookie_bar_color = "#2A3E71";
          this.accept_background_color = "#369EE3";
          this.accept_all_background_color = "#369EE3";
          this.decline_background_color = "#2A3E71";
          this.settings_background_color = "#2A3E71";
          this.settings_border_width = "1";
          this.settings_border_color = "#369EE3";
          this.settings_style = "solid";
          this.decline_style = "solid";
          this.decline_border_width = "1";
          this.decline_border_color = "#369EE3";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#369EE3";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#2A3E71";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.cookie_text_color1 = "#ffffff";
          this.accept_text_color1 = "#ffffff";
          this.cookie_bar_color1 = "#2A3E71";
          this.accept_background_color1 = "#369EE3";
          this.accept_all_background_color1 = "#369EE3";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#2A3E71";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#FFFFFF";
          this.settings_style1 = "solid";
          this.cookie_text_color2 = "#ffffff";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#2A3E71";
          this.accept_background_color2 = "#369EE3";
          this.accept_all_background_color2 = "#369EE3";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#2A3E71";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#FFFFFF";
          this.settings_style2 = "solid";
        } else if (this.popup_template == "popup-navy_blue_box") {
          this.cookie_bar_color = "#2A3E71";
          this.accept_background_color = "#369EE3";
          this.accept_all_background_color = "#369EE3";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#2A3E71";
          this.settings_border_width = "1";
          this.settings_border_color = "#FFFFFF";
          this.settings_style = "solid";
          this.decline_style = "none";
          this.cookie_bar_border_radius = "15";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#369EE3";
          this.settings_text_color = "#FFFFFF";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#2A3E71";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.cookie_text_color1 = "#ffffff";
          this.accept_text_color1 = "#ffffff";
          this.cookie_bar_color1 = "#2A3E71";
          this.accept_background_color1 = "#369EE3";
          this.accept_all_background_color1 = "#369EE3";
          this.decline_background_color1 = "#2A3E71";
          this.decline_border_width1 = "1";
          this.decline_border_color1 = "#FFFFFF";
          this.settings_background_color1 = "#2A3E71";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#FFFFFF";
          this.settings_style1 = "solid";
          this.cookie_text_color2 = "#ffffff";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#2A3E71";
          this.accept_background_color2 = "#369EE3";
          this.accept_all_background_color2 = "#369EE3";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#2A3E71";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#FFFFFF";
          this.settings_style2 = "solid";
        }
      } else if (this.show_cookie_as === "widget") {
        this.widget_template = value;
        this.template = value;
        if (this.widget_template == "widget-dark_row") {
          this.cookie_bar_color = "#323742";
          this.accept_background_color = "#3EAF9A";
          this.accept_all_background_color = "#3EAF9A";
          jQuery(".gdpr_preview").css("color", "white");
          this.decline_background_color = "#333333";
          this.settings_background_color = "#323742";
          this.settings_border_width = "1";
          this.settings_border_color = "#3EAF9A";
          this.settings_style = "solid";
          this.decline_style = "none";
          this.button_readmore_link_color = "#3EAF9A";
          this.settings_text_color = "#3EAF9A";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#323742";
          this.cookie_bar_color1 = "#323742";
          this.cookie_text_color1 = "#ffffff";
          this.accept_background_color1 = "#3EAF9A";
          this.accept_all_background_color1 = "#3EAF9A";
          this.decline_background_color1 = "#333333";
          this.settings_background_color1 = "#323742";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#3EAF9A";
          this.settings_style1 = "solid";
          this.decline_style1 = "none";
          this.settings_text_color1 = "#3EAF9A";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_color2 = "#323742";
          this.cookie_text_color2 = "#ffffff";
          this.accept_background_color2 = "#3EAF9A";
          this.accept_all_background_color2 = "#3EAF9A";
          this.decline_background_color2 = "#333333";
          this.settings_background_color2 = "#323742";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#3EAF9A";
          this.settings_style2 = "solid";
          this.decline_style2 = "none";
          this.settings_text_color2 = "#3EAF9A";
          this.cookie_bar_border_radius2 = "0";
        } else if (this.widget_template == "widget-almond_column") {
          this.cookie_bar_color = "#E8DDBB";
          this.accept_background_color = "#DE7834";
          this.accept_all_background_color = "#DE7834";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.settings_style = "none";
          this.decline_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#306189";
          this.button_revoke_consent_background_color = "#E8DDBB";
          this.cookie_bar_color1 = "#E8DDBB";
          this.cookie_text_color1 = "#ffffff";
          this.accept_background_color1 = "#DE7834";
          this.accept_all_background_color1 = "#DE7834";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.decline_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_color2 = "#E8DDBB";
          this.cookie_text_color2 = "#ffffff";
          this.accept_background_color2 = "#DE7834";
          this.accept_all_background_color2 = "#DE7834";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.decline_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
          this.cookie_bar_border_radius2 = "0";
        } else if (this.widget_template == "widget-grey_center") {
          this.cookie_bar_color = "#F4F4F4";
          this.accept_background_color = "#DE7834";
          this.accept_all_background_color = "#DE7834";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.button_readmore_link_color = "#DE7834";
          this.settings_style = "none";
          this.decline_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#F4F4F4";
          this.cookie_text_color1 = "#111111";
          this.accept_text_color1 = "#ffffff";
          this.cookie_text_color2 = "#111111";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color1 = "#F4F4F4";
          this.accept_background_color1 = "#DE7834";
          this.accept_all_background_color1 = "#DE7834";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_color2 = "#F4F4F4";
          this.accept_background_color2 = "#DE7834";
          this.accept_all_background_color2 = "#DE7834";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
          this.cookie_bar_border_radius2 = "0";
        } else if (this.widget_template == "widget-grey_column") {
          this.cookie_bar_color = "#F4F4F4";
          this.accept_background_color = "#E14469";
          this.accept_all_background_color = "#E14469";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#252525";
          jQuery(".gdpr_preview").css("color", "rgb(30, 61, 89)");
          this.button_readmore_link_color = "#E14469";
          this.settings_style = "none";
          this.decline_style = "none";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#F4F4F4";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.cookie_bar_color1 = "#F4F4F4";
          this.cookie_text_color1 = "#1E3D59";
          this.accept_background_color1 = "#E14469";
          this.accept_all_background_color1 = "#E14469";
          this.accept_text_color1 = "#ffffff";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#252525";
          this.settings_style1 = "none";
          this.settings_text_color1 = "#FFFFFF";
          this.cookie_text_color2 = "#1E3D59";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#F4F4F4";
          this.accept_background_color2 = "#E14469";
          this.accept_all_background_color2 = "#E14469";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#252525";
          this.settings_style2 = "none";
          this.settings_text_color2 = "#FFFFFF";
        } else if (this.widget_template == "widget-navy_blue_center") {
          this.cookie_bar_color = "#2A3E71";
          this.accept_background_color = "#369EE3";
          this.accept_all_background_color = "#369EE3";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#2A3E71";
          this.settings_border_width = "1";
          this.settings_border_color = "#FFFFFF";
          this.settings_style = "solid";
          this.decline_style = "none";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#369EE3";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#2A3E71";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.cookie_text_color1 = "#ffffff";
          this.accept_text_color1 = "#ffffff";
          this.cookie_bar_color1 = "#2A3E71";
          this.accept_background_color1 = "#369EE3";
          this.accept_all_background_color1 = "#369EE3";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#2A3E71";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#FFFFFF";
          this.settings_style1 = "solid";
          this.cookie_text_color2 = "#ffffff";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#2A3E71";
          this.accept_background_color2 = "#369EE3";
          this.accept_all_background_color2 = "#369EE3";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#2A3E71";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#FFFFFF";
          this.settings_style2 = "solid";
        } else if (this.widget_template == "widget-default") {
          this.cookie_bar_color = "#FFFFFF";
          this.accept_background_color = "#66CC66";
          this.accept_all_background_color = "#66CC66";
          this.decline_background_color = "#EF5454";
          this.settings_background_color = "#007CBA";
          this.settings_style = "none";
          this.decline_style = "none";
          jQuery(".gdpr_preview").css("color", "#3c4b64");
          this.button_readmore_link_color = "#007CBA";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#000000";
          this.button_revoke_consent_background_color = "#FFFFFF";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.cookie_bar_color1 = "#FFFFFF";
          this.cookie_text_color1 = "#111111";
          this.accept_background_color1 = "#66CC66";
          this.accept_all_background_color1 = "#66CC66";
          this.decline_background_color1 = "#EF5454";
          this.settings_background_color1 = "#007CBA";
          this.settings_style1 = "none";
          this.cookie_bar_color2 = "#FFFFFF";
          this.cookie_text_color2 = "#111111";
          this.accept_background_color2 = "#66CC66";
          this.accept_all_background_color2 = "#66CC66";
          this.decline_background_color2 = "#EF5454";
          this.settings_background_color2 = "#007CBA";
          this.settings_style2 = "none";
        } else if (this.widget_template == "widget-dark") {
          this.cookie_bar_color = "#262626";
          this.accept_background_color = "#6A8EE7";
          this.accept_all_background_color = "#6A8EE7";
          this.decline_background_color = "#808080";
          this.settings_background_color = "#262626";
          this.settings_border_width = "1";
          this.settings_border_color = "#808080";
          this.settings_style = "solid";
          this.decline_style = "none";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#6A8EE7";
          this.settings_text_color = "#808080";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#262626";
          this.cookie_bar_color1 = "#262626";
          this.cookie_text_color1 = "#ffffff";
          this.accept_background_color1 = "#6A8EE7";
          this.accept_all_background_color1 = "#6A8EE7";
          this.decline_background_color1 = "#808080";
          this.settings_background_color1 = "#262626";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#808080";
          this.settings_style1 = "solid";
          this.settings_text_color1 = "#808080";
          this.cookie_bar_color2 = "#262626";
          this.cookie_text_color2 = "#ffffff";
          this.accept_background_color2 = "#6A8EE7";
          this.accept_all_background_color2 = "#6A8EE7";
          this.decline_background_color2 = "#808080";
          this.settings_background_color2 = "#262626";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#808080";
          this.settings_style2 = "solid";
          this.settings_text_color2 = "#808080";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
        } else if (this.widget_template == "widget-navy_blue_square") {
          this.cookie_bar_color = "#2A3E71";
          this.accept_background_color = "#369EE3";
          this.accept_all_background_color = "#369EE3";
          this.decline_background_color = "#2A3E71";
          this.settings_background_color = "#2A3E71";
          this.settings_border_width = "1";
          this.settings_border_color = "#369EE3";
          this.settings_style = "solid";
          this.decline_style = "solid";
          this.decline_border_width = "1";
          this.decline_border_color = "#369EE3";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#369EE3";
          this.settings_text_color = "#FFFFFF";
          this.cookie_bar_border_radius = "0";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#2A3E71";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.cookie_text_color1 = "#ffffff";
          this.accept_text_color1 = "#ffffff";
          this.cookie_bar_color1 = "#2A3E71";
          this.accept_background_color1 = "#369EE3";
          this.accept_all_background_color1 = "#369EE3";
          this.decline_background_color1 = "#252525";
          this.settings_background_color1 = "#2A3E71";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#FFFFFF";
          this.settings_style1 = "solid";
          this.cookie_text_color2 = "#ffffff";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#2A3E71";
          this.accept_background_color2 = "#369EE3";
          this.accept_all_background_color2 = "#369EE3";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#2A3E71";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#FFFFFF";
          this.settings_style2 = "solid";
        } else if (this.widget_template == "widget-navy_blue_box") {
          this.cookie_bar_color = "#2A3E71";
          this.accept_background_color = "#369EE3";
          this.accept_all_background_color = "#369EE3";
          this.decline_background_color = "#252525";
          this.settings_background_color = "#2A3E71";
          this.settings_border_width = "1";
          this.settings_border_color = "#FFFFFF";
          this.settings_style = "solid";
          this.decline_style = "none";
          this.cookie_bar_border_radius = "15";
          jQuery(".gdpr_preview").css("color", "rgb(255, 255, 255)");
          this.button_readmore_link_color = "#369EE3";
          this.settings_text_color = "#FFFFFF";
          this.button_revoke_consent_text_color = "#FFFFFF";
          this.button_revoke_consent_background_color = "#2A3E71";
          this.cookie_bar_border_radius1 = "0";
          this.cookie_bar_border_radius2 = "0";
          this.cookie_text_color1 = "#ffffff";
          this.accept_text_color1 = "#ffffff";
          this.cookie_bar_color1 = "#2A3E71";
          this.accept_background_color1 = "#369EE3";
          this.accept_all_background_color1 = "#369EE3";
          this.decline_background_color1 = "#2A3E71";
          this.decline_border_width1 = "1";
          this.decline_border_color1 = "#FFFFFF";
          this.settings_background_color1 = "#2A3E71";
          this.settings_border_width1 = "1";
          this.settings_border_color1 = "#FFFFFF";
          this.settings_style1 = "solid";
          this.cookie_text_color2 = "#ffffff";
          this.accept_text_color2 = "#ffffff";
          this.cookie_bar_color2 = "#2A3E71";
          this.accept_background_color2 = "#369EE3";
          this.accept_all_background_color2 = "#369EE3";
          this.decline_background_color2 = "#252525";
          this.settings_background_color2 = "#2A3E71";
          this.settings_border_width2 = "1";
          this.settings_border_color2 = "#FFFFFF";
          this.settings_style2 = "solid";
        }
      }
      this.is_template_changed = true;
    },
    onLanguageChange() {
      this.is_lang_changed = true;
    },
    cookieLayoutChange(value) {
      if (value) {
        this.settings_layout = true;
      } else {
        this.settings_layout = false;
      }
    },
    onSwitchButtonReadMoreIsOn() {
      this.button_readmore_is_on = !this.button_readmore_is_on;
    },
    onSwitchButtonReadMoreWpPage() {
      this.button_readmore_wp_page = !this.button_readmore_wp_page;
    },
    onSwitchButtonReadMoreNewWin() {
      this.button_readmore_new_win = !this.button_readmore_new_win;
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
    onSelectPrivacyPage(value) {
      this.button_readmore_page = value;
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
      } else if (value === "ccpa") {
        this.is_ccpa = true;
        this.is_eprivacy = false;
        this.is_gdpr = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = true;
        this.show_revoke_card = false;
      } else if (value === "gdpr") {
        this.is_gdpr = true;
        this.is_ccpa = false;
        this.is_eprivacy = false;
        this.is_lgpd = false;
        this.show_revoke_card = true;
        this.show_visitor_conditions = true;
      } else if (value === "lgpd") {
        this.is_ccpa = false;
        this.is_eprivacy = false;
        this.is_gdpr = false;
        this.is_lgpd = true;
        this.show_revoke_card = true;
        this.show_visitor_conditions = true;
      } else {
        this.is_eprivacy = true;
        this.is_gdpr = false;
        this.is_ccpa = false;
        this.is_lgpd = false;
        this.show_visitor_conditions = false;
        this.show_revoke_card = true;
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
      this.cookie_bar_opacity = "0.80";
      this.cookie_bar_border_width = "0";
      this.border_style = "none";
      this.cookie_border_color = "#ffffff";
      this.cookie_bar_border_radius = "0";
      this.template = "banner-default";
      this.banner_template = "banner-default";
      this.popup_template = "popup-default";
      this.widget_template = "widget-default";
      this.accept_text = "Accept";
      this.accept_url = "#";
      this.accept_action = "#cookie_action_close_header";
      this.accept_text_color = "#ffffff";
      this.accept_background_color = "#18a300";
      this.open_url = false;
      this.accept_as_button = true;
      this.accept_size = "medium";
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
      this.accept_all_size = "medium";
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
      this.button_readmore_button_size = "medium";
      this.button_readmore_is_on = true;
      this.button_readmore_url_type = true;
      this.button_readmore_wp_page = false;
      this.button_readmore_page = "0";
      this.button_readmore_button_opacity = "1";
      this.button_readmore_button_border_width = "0";
      this.button_readmore_button_border_style = "none";
      this.button_readmore_button_border_color = "#333333";
      this.button_readmore_button_border_radius = "0";
      this.iabtcf_is_on = false;
      this.decline_text = "Decline";
      this.decline_url = "#";
      this.decline_action = "#cookie_action_settings";
      this.decline_text_color = "#ffffff";
      this.decline_background_color = "#333333";
      this.open_decline_url = false;
      this.decline_as_button = true;
      this.decline_size = "medium";
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
      this.settings_size = "medium";
      this.cookie_settings_on = true;
      this.cookie_on_frontend = true;
      this.settings_layout = false;
      this.layout_skin = "layout-default";
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
      this.confirm_size = "medium";
      this.confirm_opacity = "1";
      this.confirm_border_width = "0";
      this.confirm_style = "none";
      this.confirm_border_color = "#18a300";
      this.confirm_border_radius = "0";
      this.cancel_text = "Cancel";
      this.cancel_background_color = "#333333";
      this.cancel_text_color = "#ffffff";
      this.cancel_size = "medium";
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
      this.selectedRadioGdpr = "no";
      this.selectedRadioCcpa = "no";
      this.selectedRadioLgpd = "no";
      this.logging_on = true;
      this.show_credits = false;
      this.autotick = false;
      this.is_revoke_consent_on = true;
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
      this.tab_text = "Cookie Settings";
      this.tab_margin = "5";
      this.auto_hide_delay = "10000";
      this.auto_banner_initialize = "10000";
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
    openMediaModal() {
      var image_frame = wp.media({
        title: "Select Media from here",
        multiple: false,
        library: {
          type: "image",
        },
      });
      jQuery("#image-upload-button")
        .unbind()
        .click(
          image_frame.on("close", function () {
            var selection = image_frame.state().get("selection");
            selection.each(function (attachment) {
              jQuery("#gdpr-cookie-bar-logo-holder").attr(
                "src",
                attachment.attributes.url
              );
              jQuery("#gdpr-cookie-bar-logo-holder1").attr(
                "src",
                attachment.attributes.url
              );
              jQuery("#gdpr-cookie-bar-logo-holder2").attr(
                "src",
                attachment.attributes.url
              );
              jQuery("#gdpr-cookie-bar-logo-url-holder").attr(
                "value",
                attachment.attributes.url
              );
              jQuery("#gdpr-cookie-bar-logo-url-holder1").attr(
                "value",
                attachment.attributes.url
              );
              jQuery("#gdpr-cookie-bar-logo-url-holder2").attr(
                "value",
                attachment.attributes.url
              );
            });
          }),
          image_frame.open()
        );
    },
    deleteSelectedimage() {
      jQuery("#gdpr-cookie-bar-logo-holder").removeAttr("src");
      jQuery("#gdpr-cookie-bar-logo-holder1").removeAttr("src");
      jQuery("#gdpr-cookie-bar-logo-holder2").removeAttr("src");
      jQuery("#gdpr-cookie-bar-logo-url-holder").attr("value", "");
      jQuery("#gdpr-cookie-bar-logo-url-holder1").attr("value", "");
      jQuery("#gdpr-cookie-bar-logo-url-holder2").attr("value", "");

      this.is_logo_removed = true;
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
  icons: { cilPencil, cilSettings, cilInfo, cibGoogleKeep },
});
