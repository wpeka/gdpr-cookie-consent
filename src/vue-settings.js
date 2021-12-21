import Vue from 'vue';
import CoreuiVue from '@coreui/vue';
import vSelect from 'vue-select';
import { VueEditor } from "vue2-editor";
import '@coreui/coreui/dist/css/coreui.min.css';
import 'vue-select/dist/vue-select.css';
import VueModal from '@kouts/vue-modal'
import '@kouts/vue-modal/dist/vue-modal.css';

import { cilPencil, cilSettings, cilInfo, cibGoogleKeep } from '@coreui/icons';
Vue.use(CoreuiVue);
Vue.component('v-select', vSelect);
Vue.component('vue-editor', VueEditor);
Vue.component('v-modal', VueModal);

const j = jQuery.noConflict();

var gen = new Vue({
    el: '#gdpr-cookie-consent-settings-app',
    data() {
        return {
            labelIcon: {
                labelOn: '\u2713',
                labelOff: '\u2715'
            },
            appendField: ".gdpr-cookie-consent-settings-container",
            show_script_blocker: false,
            scripts_list_total: settings_obj.script_blocker_settings.hasOwnProperty('scripts_list') ? settings_obj.script_blocker_settings.scripts_list['total'] : 0,
            scripts_list_data: settings_obj.script_blocker_settings.hasOwnProperty('scripts_list') ? settings_obj.script_blocker_settings.scripts_list['data'] : [],
            category_list_options: settings_obj.script_blocker_settings.hasOwnProperty('category_list') ? settings_obj.script_blocker_settings['category_list'] : [],
            cookie_is_on: settings_obj.the_options.hasOwnProperty('is_on') && (true === settings_obj.the_options['is_on'] || 1 === settings_obj.the_options['is_on'] ) ? true : false,
            policy_options: settings_obj.policies,
            gdpr_policy: settings_obj.the_options.hasOwnProperty('cookie_usage_for') ? settings_obj.the_options['cookie_usage_for'] : 'gdpr',
            is_gdpr: this.gdpr_policy === 'gdpr' || this.gdpr_policy === 'both' ? true : false,
            is_ccpa: this.gdpr_policy === 'ccpa' || this.gdpr_policy === 'both' ? true : false,
            is_eprivacy: this.gdpr_policy === 'eprivacy' ? true : false,
            eprivacy_message: settings_obj.the_options.hasOwnProperty('notify_message_eprivacy') ? this.stripSlashes(settings_obj.the_options['notify_message_eprivacy']) : "This website uses cookies to improve your experience. We'll assume you're ok with this, but you can opt-out if you wish.",
            gdpr_message_heading: settings_obj.the_options.hasOwnProperty('bar_heading_text') ? this.stripSlashes(settings_obj.the_options['bar_heading_text']) : "",
            gdpr_message: settings_obj.the_options.hasOwnProperty('notify_message') ? this.stripSlashes(settings_obj.the_options['notify_message']) : "This website uses cookies to improve your experience. We'll assume you're ok with this, but you can opt-out if you wish.",
            gdpr_about_cookie_message: settings_obj.the_options.hasOwnProperty('about_message') ? this.stripSlashes(settings_obj.the_options['about_message']) : "Cookies are small text files that can be used by websites to make a user's experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.",
            ccpa_message: settings_obj.the_options.hasOwnProperty('notify_message_ccpa') ? this.stripSlashes(settings_obj.the_options['notify_message_ccpa']) : "In case of sale of your personal information, you may opt out by using the link",
            show_visitor_conditions: this.is_ccpa || ( this.is_gdpr && settings_obj.is_pro_active ) ? true : false,
            is_iab_on: settings_obj.the_options.hasOwnProperty('is_ccpa_iab_on') && (true === settings_obj.the_options['is_ccpa_iab_on'] || 1 === settings_obj.the_options['is_ccpa_iab_on'] ) ? true : false,
            is_eu_on: settings_obj.the_options.hasOwnProperty('is_eu_on') && (true === settings_obj.the_options['is_eu_on'] || 1 === settings_obj.the_options['is_eu_on'] ) ? true : false,
            is_ccpa_on: settings_obj.the_options.hasOwnProperty('is_ccpa_on') && (true === settings_obj.the_options['is_ccpa_on'] || 1 === settings_obj.the_options['is_ccpa_on'] ) ? true : false,
            is_revoke_consent_on: settings_obj.the_options.hasOwnProperty('show_again') && (true === settings_obj.the_options['show_again'] || 1 === settings_obj.the_options['show_again'] ) ? true : false,
            tab_position_options: settings_obj.tab_position_options,
            tab_position: settings_obj.the_options.hasOwnProperty('show_again_position') ? settings_obj.the_options['show_again_position'] : 'right',
            tab_margin: settings_obj.the_options.hasOwnProperty('show_again_margin') ? settings_obj.the_options['show_again_margin'] : '5',
            tab_text: settings_obj.the_options.hasOwnProperty('show_again_text') ? settings_obj.the_options['show_again_text'] : 'Cookie Settings',
            show_revoke_card: this.is_gdpr || this.is_eprivacy,
            autotick: settings_obj.the_options.hasOwnProperty('is_ticked') && (true === settings_obj.the_options['is_ticked'] || 1 === settings_obj.the_options['is_ticked'] ) ? true : false,
            auto_hide: settings_obj.the_options.hasOwnProperty('auto_hide') && (true === settings_obj.the_options['auto_hide'] || 1 === settings_obj.the_options['auto_hide'] ) ? true : false,
            auto_hide_delay: settings_obj.the_options.hasOwnProperty('auto_hide_delay') ? settings_obj.the_options['auto_hide_delay'] : '10000',
            auto_scroll: settings_obj.the_options.hasOwnProperty('auto_scroll') && (true === settings_obj.the_options['auto_scroll'] || 1 === settings_obj.the_options['auto_scroll'] ) ? true : false,
            auto_scroll_offset: settings_obj.the_options.hasOwnProperty('auto_scroll_offset') ? settings_obj.the_options['auto_scroll_offset'] : '10',
            auto_scroll_reload: settings_obj.the_options.hasOwnProperty('auto_scroll_reload') && (true === settings_obj.the_options['auto_scroll_reload'] || 1 === settings_obj.the_options['auto_scroll_reload'] ) ? true : false,
            accept_reload: settings_obj.the_options.hasOwnProperty('accept_reload') && (true === settings_obj.the_options['accept_reload'] || 1 === settings_obj.the_options['accept_reload'] ) ? true : false,
            decline_reload: settings_obj.the_options.hasOwnProperty('decline_reload') && (true === settings_obj.the_options['decline_reload'] || 1 === settings_obj.the_options['decline_reload'] ) ? true : false,
            delete_on_deactivation: settings_obj.the_options.hasOwnProperty('delete_on_deactivation') && (true === settings_obj.the_options['delete_on_deactivation'] || 1 === settings_obj.the_options['delete_on_deactivation'] ) ? true : false,
            show_credits: settings_obj.the_options.hasOwnProperty('show_credits') && (true === settings_obj.the_options['show_credits'] || 1 === settings_obj.the_options['show_credits'] ) ? true : false,
            cookie_expiry_options: settings_obj.cookie_expiry_options,
            cookie_expiry: settings_obj.the_options.hasOwnProperty('cookie_expiry') ? settings_obj.the_options['cookie_expiry'] : '365',
            show_credits: settings_obj.the_options.hasOwnProperty('show_credits') && (true === settings_obj.the_options['show_credits'] || 1 === settings_obj.the_options['show_credits'] ) ? true : false,
            logging_on: settings_obj.the_options.hasOwnProperty('logging_on') && (true === settings_obj.the_options['logging_on'] || 1 === settings_obj.the_options['logging_on'] ) ? true : false,
            list_of_contents: settings_obj.list_of_contents,
            restrict_posts: settings_obj.the_options.hasOwnProperty('restrict_posts') ? settings_obj.the_options['restrict_posts'] : [],
            restrict_array: [],
            button_readmore_is_on: settings_obj.the_options.hasOwnProperty('button_readmore_is_on') && (true === settings_obj.the_options['button_readmore_is_on'] || 1 === settings_obj.the_options['button_readmore_is_on'] ) ? true : false,
            button_readmore_text: settings_obj.the_options.hasOwnProperty('button_readmore_text') ? settings_obj.the_options['button_readmore_text'] : 'Read More',
            button_readmore_link_color: settings_obj.the_options.hasOwnProperty('button_readmore_link_color') ? settings_obj.the_options['button_readmore_link_color'] : '#359bf5',
            show_as_options: settings_obj.show_as_options,
            button_readmore_as_button: settings_obj.the_options.hasOwnProperty('button_readmore_as_button') && (true === settings_obj.the_options['button_readmore_as_button'] || 1 === settings_obj.the_options['button_readmore_as_button'] ) ? true : false,
            url_type_options: settings_obj.url_type_options,
            button_readmore_url_type: settings_obj.the_options.hasOwnProperty('button_readmore_url_type') && (false === settings_obj.the_options['button_readmore_url_type'] || 0 === settings_obj.the_options['button_readmore_url_type'] ) ? false : true,
            privacy_policy_options: settings_obj.privacy_policy_options,
            button_readmore_page: settings_obj.the_options.hasOwnProperty('button_readmore_page') ? settings_obj.the_options['button_readmore_page'] : '0',
            readmore_page: '',
            button_readmore_wp_page: settings_obj.the_options.hasOwnProperty('button_readmore_wp_page') && (true === settings_obj.the_options['button_readmore_wp_page'] || 1 === settings_obj.the_options['button_readmore_wp_page'] ) ? true : false,
            button_readmore_new_win: settings_obj.the_options.hasOwnProperty('button_readmore_new_win') && (true === settings_obj.the_options['button_readmore_new_win'] || 1 === settings_obj.the_options['button_readmore_new_win'] ) ? true : false,
            button_readmore_url: settings_obj.the_options.hasOwnProperty('button_readmore_url') ? settings_obj.the_options['button_readmore_url'] : '#',
            button_readmore_button_color: settings_obj.the_options.hasOwnProperty('button_readmore_button_color') ? settings_obj.the_options['button_readmore_button_color'] : '#000000',
            button_readmore_button_opacity: settings_obj.the_options.hasOwnProperty('button_readmore_button_opacity') ? settings_obj.the_options['button_readmore_button_opacity'] : '1',
            button_readmore_button_border_style: settings_obj.the_options.hasOwnProperty('button_readmore_button_border_style') ? settings_obj.the_options['button_readmore_button_border_style'] : 'none',
            button_readmore_button_border_width: settings_obj.the_options.hasOwnProperty('button_readmore_button_border_width') ? settings_obj.the_options['button_readmore_button_border_width'] : '0',
            button_readmore_button_border_color: settings_obj.the_options.hasOwnProperty('button_readmore_button_border_color') ? settings_obj.the_options['button_readmore_button_border_color'] : '#000000',
            button_readmore_button_border_radius: settings_obj.the_options.hasOwnProperty('button_readmore_button_border_radius') ? settings_obj.the_options['button_readmore_button_border_radius'] : '0',
            button_readmore_button_size: settings_obj.the_options.hasOwnProperty('button_readmore_button_size') ? settings_obj.the_options['button_readmore_button_size'] : 'medium',
            button_size_options: settings_obj.button_size_options,
            button_readmore_button_size: settings_obj.the_options.hasOwnProperty('button_readmore_button_size') ? settings_obj.the_options['button_readmore_button_size'] : 'medium',
            show_cookie_as_options: settings_obj.show_cookie_as_options,
            show_cookie_as: settings_obj.the_options.hasOwnProperty('cookie_bar_as') ? settings_obj.the_options['cookie_bar_as'] : 'banner',
            cookie_position_options: settings_obj.position_options,
            cookie_position: settings_obj.the_options.hasOwnProperty('notify_position_vertical') ? settings_obj.the_options['notify_position_vertical'] : 'bottom',
            cookie_widget_position_options: settings_obj.widget_position_options,
            cookie_widget_position: settings_obj.the_options.hasOwnProperty('notify_position_horizontal') ? settings_obj.the_options['notify_position_horizontal'] : 'left',
            cookie_add_overlay: settings_obj.the_options.hasOwnProperty('popup_overlay') && (true === settings_obj.the_options['popup_overlay'] || 1 === settings_obj.the_options['popup_overlay'] ) ? true : false,
            
            on_hide_options: settings_obj.on_hide_options,
            cookie_bar_color: settings_obj.the_options.hasOwnProperty('background') ? settings_obj.the_options['background'] : '#ffffff',
            on_hide: settings_obj.the_options.hasOwnProperty('notify_animate_hide') && ( true === settings_obj.the_options['notify_animate_hide'] || 1 === settings_obj.the_options['notify_animate_hide'] ) ? true : false,
            cookie_text_color: settings_obj.the_options.hasOwnProperty('text') ? settings_obj.the_options['text'] : '#000000',
            cookie_bar_opacity: settings_obj.the_options.hasOwnProperty('opacity') ? settings_obj.the_options['opacity'] : '0.80',
            cookie_bar_border_width: settings_obj.the_options.hasOwnProperty('background_border_width') ? settings_obj.the_options['background_border_width'] : '0.80',
            border_style_options: settings_obj.border_style_options,
            border_style: settings_obj.the_options.hasOwnProperty('background_border_style') ? settings_obj.the_options['background_border_style'] : 'none',
            cookie_border_color: settings_obj.the_options.hasOwnProperty('background_border_color') ? settings_obj.the_options['background_border_color'] : '#ffffff',
            cookie_bar_border_radius: settings_obj.the_options.hasOwnProperty('background_border_radius') ? settings_obj.the_options['background_border_radius'] : '0',
            font_options: settings_obj.font_options,
            cookie_font: settings_obj.the_options.hasOwnProperty('font_family') ? settings_obj.the_options['font_family'] : 'inherit',
            cookie_accept_on: settings_obj.the_options.hasOwnProperty('button_accept_is_on') && (true === settings_obj.the_options['button_accept_is_on'] || 1 === settings_obj.the_options['button_accept_is_on'] ) ? true : false,
            accept_text: settings_obj.the_options.hasOwnProperty('button_accept_text') ? settings_obj.the_options['button_accept_text'] : 'Accept',
            accept_text_color: settings_obj.the_options.hasOwnProperty('button_accept_link_color') ? settings_obj.the_options['button_accept_link_color'] : '#ffffff',
            accept_size_options : settings_obj.accept_size_options,
            accept_size: settings_obj.the_options.hasOwnProperty('button_accept_button_size') ? settings_obj.the_options['button_accept_button_size'] : 'medium',
            accept_action_options : settings_obj.accept_action_options,
            accept_action: settings_obj.the_options.hasOwnProperty('button_accept_action') ? settings_obj.the_options['button_accept_action'] : '#cookie_action_close_header',
            accept_url: settings_obj.the_options.hasOwnProperty('button_accept_url') ? settings_obj.the_options['button_accept_url'] : '#',
            is_open_url: this.accept_action === '#cookie_action_close_header' ? false : true,
            accept_as_button_options: settings_obj.accept_button_as_options,
            accept_as_button: settings_obj.the_options.hasOwnProperty('button_accept_as_button') && ( true === settings_obj.the_options['button_accept_as_button'] || 1 === settings_obj.the_options['button_accept_as_button'] ) ? true : false,
            open_url_options: settings_obj.open_url_options,
            open_url: settings_obj.the_options.hasOwnProperty('button_accept_new_win') && ( true === settings_obj.the_options['button_accept_new_win'] || 1 === settings_obj.the_options['button_accept_new_win'] ) ? true : false,
            accept_background_color: settings_obj.the_options.hasOwnProperty('button_accept_button_color') ? settings_obj.the_options['button_accept_button_color'] : '#18a300',
            accept_opacity: settings_obj.the_options.hasOwnProperty('button_accept_button_opacity') ? settings_obj.the_options['button_accept_button_opacity'] : '1',
            accept_style: settings_obj.the_options.hasOwnProperty('button_accept_button_border_style') ? settings_obj.the_options['button_accept_button_border_style'] : 'none',
            accept_border_color: settings_obj.the_options.hasOwnProperty('button_accept_button_border_color') ? settings_obj.the_options['button_accept_button_border_color'] : '#18a300',
            accept_border_width: settings_obj.the_options.hasOwnProperty('button_accept_button_border_width') ? settings_obj.the_options['button_accept_button_border_width'] : '0',
            accept_border_radius: settings_obj.the_options.hasOwnProperty('button_accept_button_border_radius') ? settings_obj.the_options['button_accept_button_border_radius'] : '0',
            cookie_decline_on: settings_obj.the_options.hasOwnProperty('button_decline_is_on') && (true === settings_obj.the_options['button_decline_is_on'] || 1 === settings_obj.the_options['button_decline_is_on'] ) ? true : false,
            decline_text: settings_obj.the_options.hasOwnProperty('button_decline_text') ? settings_obj.the_options['button_decline_text'] : 'Decline',
            decline_text_color: settings_obj.the_options.hasOwnProperty('button_decline_link_color') ? settings_obj.the_options['button_decline_link_color'] : '#ffffff',
            decline_as_button: settings_obj.the_options.hasOwnProperty('button_decline_as_button') && ( true === settings_obj.the_options['button_decline_as_button'] || 1 === settings_obj.the_options['button_decline_as_button'] ) ? true : false,
            decline_background_color: settings_obj.the_options.hasOwnProperty('button_decline_button_color') ? settings_obj.the_options['button_decline_button_color'] : '#333333',
            decline_opacity: settings_obj.the_options.hasOwnProperty('button_decline_button_opacity') ? settings_obj.the_options['button_decline_button_opacity'] : '1',
            decline_style: settings_obj.the_options.hasOwnProperty('button_decline_button_border_style') ? settings_obj.the_options['button_decline_button_border_style'] : 'none',
            decline_border_color: settings_obj.the_options.hasOwnProperty('button_decline_button_border_color') ? settings_obj.the_options['button_decline_button_border_color'] : '#333333',
            decline_border_width: settings_obj.the_options.hasOwnProperty('button_decline_button_border_width') ? settings_obj.the_options['button_decline_button_border_width'] : '0',
            decline_border_radius: settings_obj.the_options.hasOwnProperty('button_decline_button_border_radius') ? settings_obj.the_options['button_decline_button_border_radius'] : '0',
            decline_size: settings_obj.the_options.hasOwnProperty('button_decline_button_size') ? settings_obj.the_options['button_decline_button_size'] : 'medium',
            decline_action: settings_obj.the_options.hasOwnProperty('button_decline_action') ? settings_obj.the_options['button_decline_action'] : '#cookie_action_close_header_reject',
            decline_action_options : settings_obj.decline_action_options,
            decline_open_url: this.decline_action === '#cookie_action_close_header_reject' ? false : true,
            decline_url: settings_obj.the_options.hasOwnProperty('button_decline_url') ? settings_obj.the_options['button_decline_url'] : '#',
            open_decline_url: settings_obj.the_options.hasOwnProperty('button_decline_new_win') && ( true === settings_obj.the_options['button_decline_new_win'] || 1 === settings_obj.the_options['button_decline_new_win'] ) ? true : false,
            cookie_settings_on: settings_obj.the_options.hasOwnProperty('button_settings_is_on') && (true === settings_obj.the_options['button_settings_is_on'] || 1 === settings_obj.the_options['button_settings_is_on'] ) ? true : false,
            settings_layout_options: settings_obj.settings_layout_options,
            settings_layout_options_extended: settings_obj.settings_layout_options_extended,
            settings_layout: settings_obj.the_options.hasOwnProperty('button_settings_as_popup') && ( true === settings_obj.the_options['button_settings_as_popup'] || 1 === settings_obj.the_options['button_settings_as_popup'] ) ? true : false,
            is_banner : this.show_cookie_as === 'banner' ? true :false,
            layout_skin_options: settings_obj.layout_skin_options,
            layout_skin: settings_obj.the_options.hasOwnProperty('button_settings_layout_skin') ? settings_obj.the_options['button_settings_layout_skin'] : 'layout-default',
            settings_text: settings_obj.the_options.hasOwnProperty('button_settings_text') ? settings_obj.the_options['button_settings_text'] : 'Cookie Settings',
            settings_text_color: settings_obj.the_options.hasOwnProperty('button_settings_link_color') ? settings_obj.the_options['button_settings_link_color'] : '#ffffff',
            settings_as_button: settings_obj.the_options.hasOwnProperty('button_settings_as_button') && ( true === settings_obj.the_options['button_settings_as_button'] || 1 === settings_obj.the_options['button_settings_as_button'] ) ? true : false,
            settings_background_color: settings_obj.the_options.hasOwnProperty('button_settings_button_color') ? settings_obj.the_options['button_settings_button_color'] : '#333333',
            settings_opacity: settings_obj.the_options.hasOwnProperty('button_settings_button_opacity') ? settings_obj.the_options['button_settings_button_opacity'] : '1',
            settings_style: settings_obj.the_options.hasOwnProperty('button_settings_button_border_style') ? settings_obj.the_options['button_settings_button_border_style'] : 'none',
            settings_border_color: settings_obj.the_options.hasOwnProperty('button_settings_button_border_color') ? settings_obj.the_options['button_settings_button_border_color'] : '#333333',
            settings_border_width: settings_obj.the_options.hasOwnProperty('button_settings_button_border_width') ? settings_obj.the_options['button_settings_button_border_width'] : '0',
            settings_border_radius: settings_obj.the_options.hasOwnProperty('button_settings_button_border_radius') ? settings_obj.the_options['button_settings_button_border_radius'] : '0',
            settings_size: settings_obj.the_options.hasOwnProperty('button_settings_button_size') ? settings_obj.the_options['button_settings_button_size'] : 'medium',
            cookie_on_frontend: settings_obj.the_options.hasOwnProperty('button_settings_display_cookies') && (true === settings_obj.the_options['button_settings_display_cookies'] || 1 === settings_obj.the_options['button_settings_display_cookies'] ) ? true : false,
            confirm_text: settings_obj.the_options.hasOwnProperty('button_confirm_text') ? settings_obj.the_options['button_confirm_text'] : 'Confirm',
            confirm_text_color: settings_obj.the_options.hasOwnProperty('button_confirm_link_color') ? settings_obj.the_options['button_confirm_link_color'] : '#ffffff',
            confirm_background_color: settings_obj.the_options.hasOwnProperty('button_confirm_button_color') ? settings_obj.the_options['button_confirm_button_color'] : '#18a300',
            confirm_opacity: settings_obj.the_options.hasOwnProperty('button_confirm_button_opacity_field') ? settings_obj.the_options['button_confirm_button_opacity_field'] : '1',
            confirm_style: settings_obj.the_options.hasOwnProperty('button_confirm_button_border_style') ? settings_obj.the_options['button_confirm_button_border_style'] : 'none',
            confirm_border_color: settings_obj.the_options.hasOwnProperty('button_confirm_button_border_color') ? settings_obj.the_options['button_confirm_button_border_color'] : '#18a300',
            confirm_border_width: settings_obj.the_options.hasOwnProperty('button_confirm_button_border_width') ? settings_obj.the_options['button_confirm_button_border_width'] : '0',
            confirm_border_radius: settings_obj.the_options.hasOwnProperty('button_confirm_button_border_radius') ? settings_obj.the_options['button_confirm_button_border_radius'] : '0',
            confirm_size: settings_obj.the_options.hasOwnProperty('button_confirm_button_size') ? settings_obj.the_options['button_confirm_button_size'] : 'medium',            
            cancel_text: settings_obj.the_options.hasOwnProperty('button_cancel_text') ? settings_obj.the_options['button_cancel_text'] : 'Cancel',
            cancel_text_color: settings_obj.the_options.hasOwnProperty('button_cancel_link_color') ? settings_obj.the_options['button_cancel_link_color'] : '#ffffff',
            cancel_background_color: settings_obj.the_options.hasOwnProperty('button_cancel_button_color') ? settings_obj.the_options['button_cancel_button_color'] : '#333333',
            cancel_opacity: settings_obj.the_options.hasOwnProperty('button_cancel_button_opacity_field') ? settings_obj.the_options['button_cancel_button_opacity_field'] : '1',
            cancel_style: settings_obj.the_options.hasOwnProperty('button_cancel_button_border_style') ? settings_obj.the_options['button_cancel_button_border_style'] : 'none',
            cancel_border_color: settings_obj.the_options.hasOwnProperty('button_cancel_button_border_color') ? settings_obj.the_options['button_cancel_button_border_color'] : '#333333',
            cancel_border_width: settings_obj.the_options.hasOwnProperty('button_cancel_button_border_width') ? settings_obj.the_options['button_cancel_button_border_width'] : '0',
            cancel_border_radius: settings_obj.the_options.hasOwnProperty('button_cancel_button_border_radius') ? settings_obj.the_options['button_cancel_button_border_radius'] : '0',
            cancel_size: settings_obj.the_options.hasOwnProperty('button_cancel_button_size') ? settings_obj.the_options['button_cancel_button_size'] : 'medium',
            opt_out_text: settings_obj.the_options.hasOwnProperty('button_donotsell_text') ? settings_obj.the_options['button_donotsell_text'] : 'Do Not Sell My Personal Information',
            opt_out_text_color: settings_obj.the_options.hasOwnProperty('button_donotsell_link_color') ? settings_obj.the_options['button_donotsell_link_color'] : '#359bf5',
            is_script_blocker_on: settings_obj.the_options.hasOwnProperty('is_script_blocker_on') && (true === settings_obj.the_options['is_script_blocker_on'] || 1 === settings_obj.the_options['is_script_blocker_on'] ) ? true : false,
            header_scripts: settings_obj.the_options.hasOwnProperty('header_scripts') ? this.stripSlashes(settings_obj.the_options['header_scripts']) : "",
            body_scripts: settings_obj.the_options.hasOwnProperty('body_scripts') ? this.stripSlashes(settings_obj.the_options['body_scripts']) : "",
            footer_scripts: settings_obj.the_options.hasOwnProperty('footer_scripts') ? this.stripSlashes(settings_obj.the_options['footer_scripts']) : "",
            success_error_message: '',
        }
    },
    methods: {
        stripSlashes( value ) {
            return value.replace(/\\(.)/mg, "$1");
        },
        setValues() {
            if(this.gdpr_policy === 'both') {
                this.is_ccpa = true;
                this.is_gdpr = true;
                this.is_eprivacy = false;
                this.show_visitor_conditions = true;
                this.show_revoke_card = true;
            }
            else if(this.gdpr_policy === 'ccpa') {
                this.is_ccpa = true;
                this.is_eprivacy = false;
                this.is_gdpr = false;
                this.show_visitor_conditions = true;
                this.show_revoke_card = false;
            }
            else if(this.gdpr_policy === 'gdpr') {
                this.is_gdpr = true;
                this.is_ccpa = false;
                this.is_eprivacy = false;
                this.show_revoke_card = true;
                if( settings_obj.is_pro_active ) {
                    this.show_visitor_conditions = true;
                }
            }
            else {
                this.is_eprivacy = true;
                this.is_gdpr = false;
                this.is_ccpa = false;
                this.show_visitor_conditions = false;
                this.show_revoke_card = true;
            }
            for(let i=0; i<this.list_of_contents.length; i++) {
                if( this.restrict_posts.includes(this.list_of_contents[i].code.toString()) ) {
                    this.restrict_array.push(this.list_of_contents[i])
                }
            }
            for( let i=0;i<this.privacy_policy_options.length; i++ ) {
                if( this.button_readmore_page == this.privacy_policy_options[i].code ) {
                    this.readmore_page = this.privacy_policy_options[i].label;
                    break;
                }
            }
            for( let i=0; i<this.scripts_list_total; i++ ) {
                this.scripts_list_data[i]['script_status'] = Boolean( parseInt( this.scripts_list_data[i]['script_status'] ) );
                for( let j=0; j<this.category_list_options.length; j++) {
                    if( this.category_list_options[j].code === this.scripts_list_data[i]['script_category'].toString() ) {
                        this.scripts_list_data[i]['script_category_label'] = this.category_list_options[j].label;
                        break;
                    }
                }
            }
            let navLinks = j('.nav-link').map(function () {
                return this.getAttribute('href');
            });
            for (let i = 0; i < navLinks.length; i++) {
                let re = new RegExp(navLinks[i]);
                if (window.location.href.match(re)) {
                    this.$refs.active_tab.activeTabIndex = i;
                    break;
                }
            }
            if(this.accept_action === "#cookie_action_close_header"){
                this.is_open_url = false;
            }else{
                this.is_open_url = true;
            }
            if(this.decline_action === "#cookie_action_close_header_reject"){
                this.decline_open_url = false;
            }else{
                this.decline_open_url = true;
            }
            if(this.show_cookie_as === 'banner') {
                this.is_banner = true;
            }
            else{
                this.is_banner = false;
            }
        },
        onSwitchCookieEnable() {
            this.cookie_is_on = !this.cookie_is_on;
        },
        onSwitchCookieAcceptEnable() {
            this.cookie_accept_on = !this.cookie_accept_on;
        },
        onSwitchIABEnable() {
            this.is_iab_on = !this.is_iab_on;
        },
        onSwitchEUEnable() {
            this.is_eu_on = !this.is_eu_on;
        },
        onSwitchCCPAEnable() {
            this.is_ccpa_on = !this.is_ccpa_on;
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
        onSwitchAutoScroll() {
            this.auto_scroll = !this.auto_scroll;
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
        onSwitchShowCredits() {
            this.show_credits = !this.show_credits;
        },
        onSwitchLoggingOn() {
            this.logging_on = !this.logging_on;
        },
        cookieAcceptChange( value ) {
            if(value === '#cookie_action_close_header') {
                this.is_open_url = false;
            }
            else{
                this.is_open_url = true;
            }
        }, 
        cookieDeclineChange( value ){
            if(value === '#cookie_action_close_header_reject') {
                this.decline_open_url = false;
            }
            else{
                this.decline_open_url = true;
            }
        },
        cookieTypeChange( value ){
            if(value === 'banner') {
                this.is_banner = true;
            }
            else{
                this.is_banner = false;
            }
        },
        cookieLayoutChange( value ){
            if(value) {
                this.settings_layout = true;
            }
            else{
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
        onSwitchScriptBlocker(){
            this.is_script_blocker_on = !this.is_script_blocker_on;
        },
        onPostsSelect(value){
            let temp_array = [];
            for(let i=0; i<value.length; i++) {
                temp_array[i] = value[i];
            }
            this.restrict_posts = temp_array;
        },
        onSelectPrivacyPage(value){
            this.button_readmore_page = value;
        },
        cookiePolicyChange( value ) {
            if(value === 'both') {
                this.is_ccpa = true;
                this.is_gdpr = true;
                this.is_eprivacy = false;
                this.show_visitor_conditions = true;
                this.show_revoke_card = true;
            }
            else if(value === 'ccpa') {
                this.is_ccpa = true;
                this.is_eprivacy = false;
                this.is_gdpr = false;
                this.show_visitor_conditions = true;
                this.show_revoke_card = false;
            }
            else if(value === 'gdpr') {
                this.is_gdpr = true;
                this.is_ccpa = false;
                this.is_eprivacy = false;
                this.show_revoke_card = true;
                if( settings_obj.is_pro_active ) {
                    this.show_visitor_conditions = true;
                }
            }
            else {
                this.is_eprivacy = true;
                this.is_gdpr = false;
                this.is_ccpa = false;
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
        onSwitchCookieSettingsEnable(){
            this.cookie_settings_on = !this.cookie_settings_on;
        },
        onSwitchCookieOnFrontend(){
            this.cookie_on_frontend = !this.cookie_on_frontend;
        },
        showScriptBlockerForm() {
            this.show_script_blocker = !this.show_script_blocker;
        },
        saveCookieSettings() {
            var that = this;
            var dataV = jQuery("#gcc-save-settings-form").serialize();
            jQuery.ajax({
                type: 'POST',
                url: settings_obj.ajaxurl,
                data: dataV + '&action=gcc_save_admin_settings',
            }).done(function (data) {
                that.success_error_message = 'Settings Saved';
                j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#72b85c' );
                j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            }); 
        },
        onSwitchScriptBlocker( script_id ) {
            j("#gdpr-cookie-consent-updating-settings-alert").fadeIn(200);
            j("#gdpr-cookie-consent-updating-settings-alert").fadeOut(2000);
            var that = this;
            this.scripts_list_data[script_id-1]['script_status'] = !this.scripts_list_data[script_id - 1]['script_status'];
            var status = '1';
            if( this.scripts_list_data[script_id-1]['script_status'] ) {
                status = '1';
            }
            else{
                status = '0';
            }
            var data = {
                action: 'wpl_script_blocker',
                security: settings_obj.script_blocker_settings.nonces.wpl_script_blocker,
                wpl_script_action:'update_script_status',
                status: status,
                id: script_id,
            };
            jQuery.ajax({
                url: settings_obj.script_blocker_settings.ajax_url,
                data: data,
                dataType:'json',
                type: 'POST',
                success: function (data)
                {
                    if (data.response === true) {
                        that.success_error_message = data.message;
                        j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#72b85c' );
                        j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                        j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                    } else {
                        that.success_error_message = data.message;
                        j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                        j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                        j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                    }
                },
                error:function()
                {
                    that.success_error_message = data.message;
                    j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                    j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                    j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                }
            });
        },
        onScriptCategorySelect(values){
            var that = this;
            var category_code = values.split(',')[0];
            var script_id = values.split(',')[1];
            for( let i=0; i<this.category_list_options.length; i++ ) {
                if( this.category_list_options[i]['code'] === category_code ) {
                    this.scripts_list_data[script_id-1]['script_category'] = this.category_list_options[i].code;
                    this.scripts_list_data[script_id-1]['script_category_label'] = this.category_list_options[i].label;
                    break;
                }
            }
            var data     = {
                action: 'wpl_script_blocker',
                security: settings_obj.script_blocker_settings.nonces.wpl_script_blocker,
                wpl_script_action:'update_script_category',
                category: category_code,
                id: script_id,
            };
            jQuery.ajax(
                {
                    url: settings_obj.script_blocker_settings.ajax_url,
                    data: data,
                    dataType:'json',
                    type: 'POST',
                    success: function (data)
                    {
                        if (data.response === true) {
                            that.success_error_message = data.message;
                            j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#72b85c' );
                            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                        } else {
                            that.success_error_message = data.message;
                            j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                        }
                    },
                    error:function()
                    {
                        that.success_error_message = data.message;
                        j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                        j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                        j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                    }
                }
            );
        },
    },
    mounted() {
        this.setValues();
    },
    icons: { cilPencil, cilSettings, cilInfo, cibGoogleKeep }
})