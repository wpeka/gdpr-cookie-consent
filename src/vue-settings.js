import Vue from 'vue';
import CoreuiVue from '@coreui/vue';
import vSelect from 'vue-select';
import { VueEditor } from "vue2-editor";
import '@coreui/coreui/dist/css/coreui.min.css';
import 'vue-select/dist/vue-select.css';
import VueModal from '@kouts/vue-modal'
import '@kouts/vue-modal/dist/vue-modal.css';
import Tooltip from './vue-components/tooltip';

import { cilPencil, cilSettings, cilInfo, cibGoogleKeep, cibTreehouse } from '@coreui/icons';
Vue.use(CoreuiVue);
Vue.component('v-select', vSelect);
Vue.component('vue-editor', VueEditor);
Vue.component('v-modal', VueModal);
Vue.component('tooltip', Tooltip);

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
            configure_image_url: require('../admin/images/configure-icon.png'),
            closeOnBackdrop: true,
            centered: true,
            accept_button_popup: false,
            decline_button_popup: false,
            show_script_blocker: false,
            settings_button_popup: false,
            confirm_button_popup: false,
            cancel_button_popup: false,
            opt_out_link_popup: false,
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
            show_visitor_conditions: this.is_ccpa || ( this.is_gdpr && '1' === settings_obj.is_pro_active ) ? true : false,
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
            confirm_opacity: settings_obj.the_options.hasOwnProperty('button_confirm_button_opacity') ? settings_obj.the_options['button_confirm_button_opacity'] : '1',
            confirm_style: settings_obj.the_options.hasOwnProperty('button_confirm_button_border_style') ? settings_obj.the_options['button_confirm_button_border_style'] : 'none',
            confirm_border_color: settings_obj.the_options.hasOwnProperty('button_confirm_button_border_color') ? settings_obj.the_options['button_confirm_button_border_color'] : '#18a300',
            confirm_border_width: settings_obj.the_options.hasOwnProperty('button_confirm_button_border_width') ? settings_obj.the_options['button_confirm_button_border_width'] : '0',
            confirm_border_radius: settings_obj.the_options.hasOwnProperty('button_confirm_button_border_radius') ? settings_obj.the_options['button_confirm_button_border_radius'] : '0',
            confirm_size: settings_obj.the_options.hasOwnProperty('button_confirm_button_size') ? settings_obj.the_options['button_confirm_button_size'] : 'medium',            
            cancel_text: settings_obj.the_options.hasOwnProperty('button_cancel_text') ? settings_obj.the_options['button_cancel_text'] : 'Cancel',
            cancel_text_color: settings_obj.the_options.hasOwnProperty('button_cancel_link_color') ? settings_obj.the_options['button_cancel_link_color'] : '#ffffff',
            cancel_background_color: settings_obj.the_options.hasOwnProperty('button_cancel_button_color') ? settings_obj.the_options['button_cancel_button_color'] : '#333333',
            cancel_opacity: settings_obj.the_options.hasOwnProperty('button_cancel_button_opacity') ? settings_obj.the_options['button_cancel_button_opacity'] : '1',
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
            custom_cookie_categories: settings_obj.cookie_list_settings.hasOwnProperty('cookie_list_categories') ? settings_obj.cookie_list_settings['cookie_list_categories'] : [],
            custom_cookie_types: settings_obj.cookie_list_settings.hasOwnProperty('cookie_list_types') ? settings_obj.cookie_list_settings['cookie_list_types'] : [],
            custom_cookie_category: 1,
            custom_cookie_type: 'HTTP',
            custom_cookie_name: '',
            custom_cookie_domain: '',
            custom_cookie_duration: '',
            custom_cookie_description: '',
            is_custom_cookie_duration_disabled: this.custom_cookie_type === 'HTTP Cookie' ? false : true,
            custom_cookie_duration_placeholder: 'Duration(days/session)',
            post_cookie_list_length: settings_obj.cookie_list_settings.hasOwnProperty('post_cookie_list') ? settings_obj.cookie_list_settings['post_cookie_list']['total'] : 0,
            post_cookie_list: settings_obj.cookie_list_settings.hasOwnProperty('post_cookie_list') ? settings_obj.cookie_list_settings['post_cookie_list']['data'] : [],
            show_custom_form: this.post_cookie_list_length > 0 ? false : true,
            show_add_custom_button: this.post_cookie_list_length > 0 ? true : false,
            scan_cookie_list_length: settings_obj.cookie_scan_settings.hasOwnProperty('scan_cookie_list') ? settings_obj.cookie_scan_settings['scan_cookie_list']['total'] : 0,	
            scan_cookie_list: settings_obj.cookie_scan_settings.hasOwnProperty('scan_cookie_list') ? settings_obj.cookie_scan_settings['scan_cookie_list']['data'] : [],	
            scan_cookie_error_message: settings_obj.cookie_scan_settings.hasOwnProperty('error_message') ? settings_obj.cookie_scan_settings['error_message'] : '',	
            scan_cookie_last_scan: settings_obj.cookie_scan_settings.hasOwnProperty('last_scan') ? settings_obj.cookie_scan_settings['last_scan'] : [],	
            continue_scan:1,	
            pollCount:0,	
            onPrg:0,
            template: settings_obj.the_options.hasOwnProperty('template') ? settings_obj.the_options['template'] : 'banner-default',
            banner_template: settings_obj.the_options.hasOwnProperty('banner_template') ? settings_obj.the_options['banner_template'] : 'banner-default',
            popup_template: settings_obj.the_options.hasOwnProperty('popup_template') ? settings_obj.the_options['popup_template'] : 'popup-default',
            widget_template: settings_obj.the_options.hasOwnProperty('widget_template') ? settings_obj.the_options['widget_template'] : 'widget-default',
            show_banner_template: settings_obj.the_options.hasOwnProperty('show_cookie_as') && settings_obj.the_options['show_cookie_as'] === 'banner' ? true : false,
            show_popup_template: settings_obj.the_options.hasOwnProperty('show_cookie_as') && settings_obj.the_options['show_cookie_as'] === 'popup' ? true : false,
            show_widget_template: settings_obj.the_options.hasOwnProperty('show_cookie_as') && settings_obj.the_options['show_cookie_as'] === 'widget' ? true : false,
            }
    },
    methods: {
        stripSlashes( value ) {
            return value.replace(/\\(.)/mg, "$1");
        },
        setValues() {
            if(this.show_cookie_as === 'banner') {
                this.show_banner_template = true;
                this.show_popup_template = false;
                this.show_widget_template = false;
            }
            else if(this.show_cookie_as === 'popup') {
                this.show_banner_template = false;
                this.show_popup_template = true;
                this.show_widget_template = false;
            }
            else if(this.show_cookie_as === 'widget') {
                this.show_banner_template = false;
                this.show_popup_template = false;
                this.show_widget_template = true;
            }
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
                if( '1' === settings_obj.is_pro_active ) {
                    this.show_visitor_conditions = true;
                }else{
                    this.show_visitor_conditions = false;
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
            if( this.custom_cookie_type === 'HTTP' ) {
                this.is_custom_cookie_duration_disabled = false;
                this.custom_cookie_duration = '';
            }
            else{
                this.is_custom_cookie_duration_disabled = true;
                this.custom_cookie_duration = 'Persistent';
            }
            this.show_custom_form = this.post_cookie_list_length > 0 ? false : true;
            this.show_add_custom_button = this.post_cookie_list_length > 0 ? true : false;
        },
        setPostListValues() {
            for( let i=0; i<this.post_cookie_list_length; i++ ) {
                if(this.post_cookie_list[i]['type'] === 'HTTP') {
                    this.post_cookie_list[i]['enable_duration'] = false;
                }
                else {
                    this.post_cookie_list[i]['enable_duration'] = true;
                }
                for( let j=0; j<this.custom_cookie_types.length; j++) {
                    if( this.custom_cookie_types[j]['code'] === this.post_cookie_list[i]['type'] ) {
                        this.post_cookie_list[i]['type_name'] = this.custom_cookie_types[j].label;
                    }
                }
            }
            this.show_custom_form = this.post_cookie_list_length > 0 ? false : true;
            this.show_add_custom_button = this.post_cookie_list_length > 0 ? true : false;
        },
        setScanListValues() {	
            for( let i=0; i<this.scan_cookie_list_length; i++ ) {	
                for( let j=0; j<this.custom_cookie_types.length; j++) {	
                    if( this.custom_cookie_types[j]['code'] === this.scan_cookie_list[i]['type'] ) {	
                    this.scan_cookie_list[i]['type_name'] = this.custom_cookie_types[j].label;	
                    }	
                }	
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
                this.show_banner_template = true;
                this.show_popup_template  = false;
                this.show_widget_template = false;
                this.template = this.banner_template;
            }
            else{
                this.is_banner = false;
                if( value === 'popup' ) {
                    this.show_banner_template = false;
                    this.show_popup_template  = true;
                    this.show_widget_template = false;
                    this.template = this.popup_template;
                }
                else if( value === 'widget' ) {
                    this.show_banner_template = false;
                    this.show_popup_template  = false;
                    this.show_widget_template = true;
                    this.template = this.widget_template;
                }
            }
        },
        onTemplateChange(value) {
            if( this.show_cookie_as === 'banner' ) {
                this.banner_template = value;
                this.template = value;
            }else if( this.show_cookie_as === 'popup' ) {
                this.popup_template = value;
                this.template = value;
            }
            else if( this.show_cookie_as === 'widget' ) {
                this.widget_template = value;
                this.template = value;
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
        onSwitchingScriptBlocker(){
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
                if( '1' === settings_obj.is_pro_active ) {
                    this.show_visitor_conditions = true;
                }
                else{
                    this.show_visitor_conditions = false;
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
        onSelectCustomCookieType(value){
            if(value !== 'HTTP') {
                this.is_custom_cookie_duration_disabled = true;
                this.custom_cookie_duration = 'Persistent';
            }
            else{
                this.is_custom_cookie_duration_disabled = false;
                this.custom_cookie_duration = '';
            }
        },
        showCustomCookieAddForm() {
            this.show_custom_form = true;
            this.show_add_custom_button = !this.show_add_custom_button;
        },
        onUpdateScannedCookieCategory(value) {
            const id = value.split(',')[1];	
            const cat = value.split(',')[0];	
            for( let i=0;i<this.scan_cookie_list_length;i++){	
                if(this.scan_cookie_list[i]['id_wpl_cookie_scan_cookies'] === id) {	
                    for( let j=0; i<this.custom_cookie_categories.length; j++ ) {	
                        if( this.custom_cookie_categories[j]['code'] === parseInt(cat) ) {	
                        this.scan_cookie_list[i]['category_id'] = this.custom_cookie_categories[j].code;	
                        this.scan_cookie_list[i]['category'] = this.custom_cookie_categories[j].label;	
                        break;	
                        }	
                    }	
                    break;	
                }	
            }
        },	
        updateScannedCookies() {
            var cookie_scan_arr = [];	
            for( let i=0;i <this.scan_cookie_list_length; i++) {
                var cid = this.scan_cookie_list[i]['id_wpl_cookie_scan_cookies'];	
                var ccategory = this.scan_cookie_list[i]['category_id'];	
                var cdesc = this.scan_cookie_list[i]['description'];	
                var cookie_arr = {	
                cid: cid,	
                ccategory: ccategory,	
                cdesc: cdesc,	
                }	
                cookie_scan_arr.push( cookie_arr );	
            }	
            this.updateScanCookie(cookie_scan_arr );	
        },	
        updateScanCookie(cookie_arr) {	
            var that = this;	
            var data = {	
                action: 'wpl_cookie_scanner',	
                security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,	
                wpl_scanner_action:'update_scan_cookie',	
                cookie_arr: cookie_arr,	
            };	
            j.ajax({
                url: settings_obj.cookie_scan_settings.ajax_url,	
                data: data,	
                dataType:'json',	
                type: 'POST',	
                success: function (data) {
                    if (data.response === true) {	
                        that.success_error_message = data.message;	
                        j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#72b85c' );	
                        j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);	
                        j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                        that.showScanCookieList();	
                    } else {	
                        that.success_error_message = data.message;	
                        j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );	
                        j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);	
                        j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);	
                    }	
                },	
                error:function() {	
                    that.success_error_message = data.message;	
                    j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );	
                    j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);	
                    j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);	
                }	
            });	
        },	
        showScanCookieList() {
            var that = this;	
            var data = {	
                action: 'wpl_cookie_scanner',	
                security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,	
                wpl_scanner_action:'get_scanned_cookies_list',	
            };	
            j.ajax({	
                url: settings_obj.cookie_scan_settings.ajax_url,	
                data: data,	
                dataType:'json',	
                type: 'POST',	
                success: function (data) {	
                    if(data.response) {	
                        that.scan_cookie_list_length = data.total;	
                        that.scan_cookie_list = data.data;	
                        that.setScanListValues();	
                    }	
                    else{	
                        that.success_error_message = data.message;	
                        j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );	
                        j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);	
                        j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);	
                    }	
                },	
                error:function() {	
                    that.success_error_message = data.message;	
                    j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );	
                    j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);	
                    j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);	
                }	
            });	
        },
        onSelectUpdateCookieCategory(value) {
            const id = value.split(',')[1];
            const cat = value.split(',')[0];
            for( let i=0;i<this.post_cookie_list_length;i++){
                if(this.post_cookie_list[i]['id_gdpr_cookie_post_cookies'] === id) {
                    for( let j=0; i<this.custom_cookie_categories.length; j++ ) {
                        if( this.custom_cookie_categories[j]['code'] === parseInt(cat) ) {
                            this.post_cookie_list[i]['category_id'] = this.custom_cookie_categories[j].code;
                            this.post_cookie_list[i]['category'] = this.custom_cookie_categories[j].label;
                            break;
                        }
                    }
                    break;
                }
            }
        },
        onSelectUpdateCookieType(value) {
            const id = value.split(',')[1];
            const type_id = value.split(',')[0];
            for( let i=0;i<this.post_cookie_list_length;i++){
                if(this.post_cookie_list[i]['id_gdpr_cookie_post_cookies'] === id) {
                    if(type_id !== 'HTTP') {
                        this.post_cookie_list[i]['enable_duration'] = true;
                        this.post_cookie_list[i]['duration'] = 'Persistent';
                    }
                    else{
                        this.post_cookie_list[i]['enable_duration'] = false;
                        this.post_cookie_list[i]['duration'] = '';
                    }
                    for( let j=0; i<this.custom_cookie_types.length; j++ ) {
                        if( this.custom_cookie_types[j]['code'] === type_id) {
                            this.post_cookie_list[i]['type'] = this.custom_cookie_types[j].code;
                            this.post_cookie_list[i]['type_name'] = this.custom_cookie_types[j].label;
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
        onClickStartScan() {	
            this.continue_scan = 1;
            this.doScan();	
        },
        doScan() {
            var that = this;
            var data    = {
                action: 'wpl_cookie_scanner',
                security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
                wpl_scanner_action:'check_api',
            };
            var scanbar = j( '.wpl_scanbar' );
            scanbar.html( '<span style="float:left; height:40px; line-height:40px;">' + settings_obj.cookie_scan_settings.labels.checking_api + '</span> <img src="' + settings_obj.cookie_scan_settings.loading_gif + '" style="display:inline-block;" />' );
            j.ajax(
                {
                    url: settings_obj.cookie_scan_settings.ajax_url,
                    data: data,
                    dataType:'json',
                    type: 'POST',
                    success: function (data) {
                        scanbar.html( '' );
                        if (data.response === true) {
                            that.scanNow();
                        } else {
                            that.serverUnavailable( scanbar,data.message );
                        }
                    },
                    error:function() {
                        scanbar.html( '' );
                        that.showErrorScreen( settings_obj.cookie_scan_settings.labels.error );
                    }
                }
            );
        },
        scanNow() {
            var html    = this.makeHtml();
			var scanbar = j( '.gdpr_scanbar' );
			scanbar.html( html );
			j( '.gdpr_scanbar_staypage' ).show();
			this.attachScanStop();
			j( '.gdpr_scanlog' ).css( {'display':'block','opacity':0} ).animate(
				{
					'opacity':1,'height':'auto'
				},
				1000
			);
			this.takePages( 0 );
        },
        animateProgressBar(offset,total,msg) {
            var prgElm = j( '.gdpr_progress_bar' );
            var w      = prgElm.width();
            var sp     = 100 / total;
            var sw     = w / total;
            var cw     = sw * offset;
            var cp     = sp * offset;

            cp = cp > 100 ? 100 : cp;
            cp = Math.floor( cp < 1 ? 1 : cp );

            cw = cw > w ? w : cw;
            cw = Math.floor( cw < 1 ? 1 : cw );
            j( '.gdpr_progress_bar_inner' ).stop( true,true ).animate(
                {'width':cw + 'px'},
                300,
                function(){
                    j( '.gdpr_progress_action_main' ).html( msg );
                }
            ).html( cp + '%' );
        },
        appendLogAnimate(data,offset) {
            var that = this;
            if (data.length > offset) {
                offset++;
                var speed = 300 / data.length;
                setTimeout(
                    function(){
                        that.appendLogAnimate( data,offset );
                    },
                    speed
                );
            }
        },
        takePages(offset,limit,total,scan_id) {
            var that = this;
            var data = {
                action: 'wpl_cookie_scanner',
                security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
                wpl_scanner_action:'get_pages',
                offset:offset
            };
            if (limit) {
                data['limit'] = limit;
            }
            if (total) {
                data['total'] = total;
            }
            if (scan_id) {
                data['scan_id'] = scan_id;
            }
            // fake progress.
            this.animateProgressBar( 1,100,settings_obj.cookie_scan_settings.labels.finding );
            j.ajax(
                {
                    url: settings_obj.cookie_scan_settings.ajax_url,
                    data: data,
                    dataType: 'json',
                    type: 'POST',
                    success: function (data) {
                        that.scan_id = typeof data.scan_id != 'undefined' ? data.scan_id : 0;
                        if (that.continue_scan == 0) {
                            return false;
                        }
                        if (typeof data.response != 'undefined' && data.response === true) {
                            that.appendLogAnimate( data.log,0 );
                            var new_offset = parseInt( data.offset ) + parseInt( data.limit );
                            if ((data.total - 1) > new_offset) { // substract 1 from total because of home page.
                                that.takePages( new_offset,data.limit,data.total,data.scan_id );
                            } else {
                                j( '.wpl_progress_action_main' ).html( settings_obj.cookie_scan_settings.labels.scanning );
                                that.scanPages( data.scan_id,0,data.total );
                            }
                        } else {
                            that.showErrorScreen( settings_obj.cookie_scan_settings.labels.error );
                        }
                    },
                    error:function() {
                        if (that.continue_scan == 0) {
                            return false;
                        }
                        that.showErrorScreen( settings_obj.cookie_scan_settings.labels.error );
                    }
                }
            );
        },
        scanPages(scan_id,offset,total) {
            var that = this;
            var scanbar                  = j( '.gdpr_scanbar' );
            this.pollCount = 0;
            var hash                     = Math.random().toString( 36 ).replace( '0.', '' );
            var data                     = {
                action: 'wpl_cookie_scanner',
                security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
                wpl_scanner_action:'scan_pages',
                offset:offset,
                scan_id:scan_id,
                total:total,
                hash:hash
            };
            j.ajax(
                {
                    url: settings_obj.cookie_scan_settings.ajax_url,
                    data: data,
                    dataType: 'json',
                    type: 'POST',
                    success:function(data) {
                        that.scan_id = typeof data.scan_id != 'undefined' ? data.scan_id : 0;
                        if (that.continue_scan == 0) {
                            return false;
                        }
                        if (data.response == true) {
                            that.getScanCookies( scan_id,offset,total,hash );
                        } else {
                            scanbar.html( '' );
                            j( '.wpl_scanbar_staypage' ).hide();
                            that.serverUnavailable( scanbar,data.message );
                        }
                    },
                    error:function(){
                        var current = that;
                        if (that.continue_scan == 0) {
                            return false;
                        }
                        // error and retry function.
                        
                        that.animateProgressBar( offset,total,settings_obj.cookie_scan_settings.labels.retrying );
                        setTimeout(
                            function(){
                                current.scanPages( scan_id,offset,total );
                            },
                            2000
                        );
                    }
                }
            );
        },
        getScanCookies(scan_id,offset,total,hash){
            var that = this;
            var data = {
                action: 'wpl_cookie_scanner',
                security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
                wpl_scanner_action:'get_post_scan_cookies',
                offset:offset,
                scan_id:scan_id,
                total:total,
                hash:hash
            };
            j.ajax(
                {
                    url: settings_obj.cookie_scan_settings.ajax_url,
                    data: data,
                    dataType: 'json',
                    type: 'POST',
                    success:function(data) {
                        if (data.response == true) {
                            var prg_offset = parseInt( offset ) + parseInt( data.total_scanned );
                            var prg_msg    = settings_obj.cookie_scan_settings.labels.scanning + ' ';
                            that.appendLogAnimate( data.log,0 );
                            if (data.continue === true) {
                                that.scanPages( data.scan_id,data.offset,data.total );
                            } else {
                                prg_msg  = settings_obj.cookie_scan_settings.labels.finished;
                                prg_msg += ' (' + settings_obj.cookie_scan_settings.labels.total_cookies_found + ': ' + data.total_cookies + ')';
                                that.showSuccessScreen( prg_msg,scan_id,1 );
                            }
                            that.animateProgressBar( prg_offset,total,prg_msg );
                        } else {
                            var current = that;
                            if (that.pollCount < 10) {
                                that.pollCount++;
                                setTimeout(
                                    function(){
                                        current.getScanCookies( data.scan_id,data.offset,data.total,data.hash );
                                    },
                                    10000
                                );
                            } else {
                                that.showErrorScreen( 'Something went wrong, please scan again' );
                            }
                        }
                    },
                    error:function() {
                        var current = that;
                        if (that.continue_scan == 0) {
                            return false;
                        }
                        if (that.pollCount < 10) {
                            setTimeout(
                                function(){
                                    that.getScanCookies( offset, scan_id, total, hash );
                                },
                                5000
                            );
                        } else {
                            that.showErrorScreen( 'Something went wrong, please scan again' );
                        }
                    }
                }
            );
        },
        makeHtml() {
            return '<div class="gdpr_scanlog">'
            + '<div class="gdpr_progress_action_main">' + settings_obj.cookie_scan_settings.labels.finding + '</div>'
            + '<div class="gdpr_progress_bar">'
            + '<span class="gdpr_progress_bar_inner gdpr_progress_bar_inner_restructured">'
            + '</span>'
            + '</div>'
            + '<div class="gdpr_scanlog_bar"><button type="button" class="btn btn-info pull-right gdpr_stop_scan">' + settings_obj.cookie_scan_settings.labels.stop + '</button></div>'
            + '</div>';
        },
        attachScanStop() {
            var that = this;
            j( '.gdpr_stop_scan' ).click(
                function(){
                    that.stopScan();
                }
            );
        },
        stopScan() {
            if (this.continue_scan == 0) {
                return false;
            }
            if (confirm( settings_obj.cookie_scan_settings.labels.ru_sure )) {
                this.continue_scan = 0;
                this.stoppingScan( this.scan_id );
            }
        },
        stoppingScan(scan_id) {
            var that = this;
            var data = {
                action: 'wpl_cookie_scanner',
                security: settings_obj.cookie_scan_settings.nonces.wpl_cookie_scanner,
                wpl_scanner_action:'stop_scan',
                scan_id:scan_id
            };
            j( '.gdpr_stop_scan' ).html( settings_obj.cookie_scan_settings.labels.stoping ).css( {'opacity':'.5'} );
            j.ajax(
                {
                    url: settings_obj.cookie_scan_settings.ajax_url,
                    data: data,
                    dataType: 'json',
                    type: 'POST',
                    success:function(data) {
                        that.showSuccessScreen( settings_obj.cookie_scan_settings.labels.scanning_stopped,scan_id,data.total );
                    },
                    error:function()
                {
                        // error function.
                        that.showErrorScreen( settings_obj.cookie_scan_settings.labels.error );
                    }
                }
            );
        },
        serverUnavailable:function(elm,msg){
			elm.html( '<div style="background:#ffffff; border:solid 1px #cccccc; color:#333333; padding:5px;">' + msg + '</div>' );
		},
        showErrorScreen:function(error_msg) {
			var html = '<button type="button" class="btn btn-info pull-right gdpr_scan_again" style="margin-left:5px;">' + settings_obj.cookie_scan_settings.labels.scan_again + '</button>';
			j( '.gdpr_scanlog_bar' ).html( html );
			j( '.gdpr_progress_action_main' ).html( error_msg );
			this.success_error_message = error_msg;
            j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
			j( '.gdpr_scanbar_staypage' ).hide();
			this.scanAgain();
		},
        showSuccessScreen(success_msg,scan_id,total) {
            var html = '<button type="button" class="btn btn-info pull-right gdpr_scan_again" style="margin-left:5px;">' + settings_obj.cookie_scan_settings.labels.scan_again + '</button>';
            html    += '<span class="spinner" style="margin-top:5px"></span>';
            j( '.gdpr_scanlog_bar' ).html( html );
            j( '.gdpr_progress_action_main' ).html( success_msg );
            this.success_error_message = success_msg;
            j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#72b85c' );
            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            j( '.gdpr_scanbar_staypage' ).hide();
            this.showScanCookieList();
            this.scanAgain();
        },
        scanAgain() {
            var that = this;
            j( '.gdpr_scan_again' ).unbind( 'click' ).click(
                function(){
                    that.continue_scan = 1;
                    that.scanNow();
                }
            );
        },
        onScriptCategorySelect(values){
            if( !values ) {
                this.success_error_message = 'You must select a category.';
                j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                return false;
            }
            var that = this;
            var category_code = values.split(',')[0];
            var script_id = values.split(',')[1];
            for( let i=0; i<this.category_list_options.length; i++ ) {
                if( this.category_list_options[i]['code'] === category_code ) {
                    if( this.scripts_list_data[script_id-1]['script_category'] === category_code ) {
                        this.success_error_message = 'Category updated successfully';
                        j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#72b85c' );
                        j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                        j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                        return false;
                    }
                    else{
                        this.scripts_list_data[script_id-1]['script_category'] = this.category_list_options[i].code;
                        this.scripts_list_data[script_id-1]['script_category_label'] = this.category_list_options[i].label;
                        break;
                    }
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
        onSaveCustomCookie() {
            var parent         = j( '.gdpr-custom-save-cookie' ).parents( 'div.gdpr-add-custom-cookie-form' );
            var gdpr_addcookie = parent.find( 'input[name="gdpr_addcookie"]' ).val();
            if (gdpr_addcookie == 1) {
                var pattern   = /^((http|https):\/\/)?([a-zA-Z0-9_][-_a-zA-Z0-9]{0,62}\.)+([a-zA-Z0-9]{1,10})$/gm;
                var cname     = this.custom_cookie_name;
                var cdomain   = this.custom_cookie_domain;
                var cduration = this.custom_cookie_duration;
                var ccategory = this.custom_cookie_category;
                var ctype     = this.custom_cookie_type;
                var cdesc     = this.custom_cookie_description;
                if ( ! cname) {
                    this.success_error_message = 'Please fill in these mandatory fields : Cookie Name';
                    parent.find( 'input[name="gdpr-cookie-consent-custom-cookie-name"]' ).focus();
                    j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                    j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                    j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                    return false;
                }
                if ( ! cdomain) {
                    this.success_error_message = 'Please fill in these mandatory fields : Cookie Domain';
                    parent.find( 'input[name="gdpr-cookie-consent-custom-cookie-domain"]' ).focus();
                    j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                    j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                    j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                    return false;
                } else {
                    if ( ! pattern.test( cdomain )) {
                        this.success_error_message = 'Cookie domain is not valid.';
                        parent.find( 'input[name="gdpr-cookie-consent-custom-cookie-domain"]' ).focus();
                        j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                        j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                        j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                        return false;
                    }
                }
                if ( ! cduration) {
                    this.success_error_message = 'Please fill in these mandatory fields : Cookie Duration';
                    parent.find( 'input[name="gdpr-cookie-consent-custom-cookie-duration"]' ).focus();
                    j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                    j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                    j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                    return false;
                }
                if ( ! ctype) {
                    this.success_error_message = 'Please select a Cookie Type';
                    j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                    j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                    j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                    return false;
                }
                if ( ! ccategory) {
                    this.success_error_message = 'Please select a Cookie Category';
                    j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
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
                }
                this.saveCustomPostCookies( cookie_arr );
            }
        },
        saveCookieUpdateSettings(){
            var cookie_post_arr = [];
            var error = false;
            for( let i=0;i <this.post_cookie_list_length; i++) {
                var pattern   = /^((http|https):\/\/)?([a-zA-Z0-9_][-_a-zA-Z0-9]{0,62}\.)+([a-zA-Z0-9]{1,10})$/gm;
                var cid       = this.post_cookie_list[i]['id_gdpr_cookie_post_cookies'];
                var cname     = this.post_cookie_list[i]['name'];
                var cdomain   = this.post_cookie_list[i]['domain'];
                var cduration = this.post_cookie_list[i]['duration'];
                var ccategory = this.post_cookie_list[i]['category_id'];
                var ctype     = this.post_cookie_list[i]['type'];
                var cdesc     = this.post_cookie_list[i]['description'];
                if ( ! cname) {
                    this.success_error_message = 'Please fill in these mandatory fields : Cookie Name';
                    j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                    j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                    j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                    error = true;
                }
                if ( ! cdomain) {
                    this.success_error_message = 'Please fill in these mandatory fields : Cookie Domain';
                    j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                    j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                    j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                    error = true;
                } else {
                    if ( ! pattern.test( cdomain )) {
                        this.success_error_message = 'Cookie Domain is not valid.';
                        j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                        j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                        j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                        error = true;
                    }
                }
                if ( ! cduration) {
                    this.success_error_message = 'Please fill in these mandatory fields : Cookie Duration';
                    j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
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
                }
                cookie_post_arr.push( cookie_arr );
            }
            if (error) {
                return false;
            }
            this.updatePostCookie( cookie_post_arr );
        },
        updatePostCookie:function(cookie_arr) {
            var that = this;
            var data = {
                action: 'gdpr_cookie_custom',
                security: settings_obj.cookie_list_settings.nonces.gdpr_cookie_custom,
                gdpr_custom_action:'update_post_cookie',
                cookie_arr: cookie_arr,
            };
            j.ajax(
                {
                    url: settings_obj.cookie_list_settings.ajax_url,
                    data: data,
                    dataType:'json',
                    type: 'POST',
                    success: function (data) {
                        if (data.response === true) {
                            that.success_error_message = data.message;
                            j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#72b85c' );
                            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                            that.getPostCookieList();
                        } else {
                            that.success_error_message = data.message;
                            j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                        }
                    },
                    error:function() {
                        that.success_error_message = data.message;
                        j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                        j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                        j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                    }
                }
            );
        },
        deletePostCookie(cookie_id) {
            var that = this;
            var data = {
                action: 'gdpr_cookie_custom',
                security: settings_obj.cookie_list_settings.nonces.gdpr_cookie_custom,
                gdpr_custom_action:'delete_post_cookie',
                cookie_id: cookie_id,
            };
            j.ajax(
                {
                    url: settings_obj.cookie_list_settings.ajax_url,
                    data: data,
                    dataType:'json',
                    type: 'POST',
                    success: function (data) {
                        if (data.response === true) {
                            that.success_error_message = data.message;
                            j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#72b85c' );
                            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                            that.getPostCookieList();
                        } else {
                            that.success_error_message = data.message;
                            j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                        }
                    },
                    error:function() {
                        that.success_error_message = data.message;
                        j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                        j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                        j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                    }
                }
            );
        },
        hideCookieForm() {
                this.custom_cookie_name = '';
                this.custom_cookie_domain = '';
                this.custom_cookie_description = '';
                this.custom_cookie_category = 1;
                this.custom_cookie_type = 'HTTP';
                this.show_custom_form = false;
                this.show_add_custom_button = true;
                this.is_custom_cookie_duration_disabled = false;
                this.custom_cookie_duration = '';
        },
        saveCustomPostCookies( cookie_data ) {
            var that = this;
            var data = {
                action: 'gdpr_cookie_custom',
                security: settings_obj.cookie_list_settings.nonces.gdpr_cookie_custom,
                gdpr_custom_action:'save_post_cookie',
                cookie_arr: cookie_data,
            };
            j.ajax(
                {
                    url: settings_obj.cookie_list_settings.ajax_url,
                    data: data,
                    dataType:'json',
                    type: 'POST',
                    success: function (data) {
                        if (data.response === true) {
                            that.success_error_message = data.message;
                            j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#72b85c' );
                            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                            that.hideCookieForm();
                            that.getPostCookieList();
                        } else {
                            that.success_error_message = data.message;
                            j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                        }
                    },
                    error:function() {
                        that.success_error_message = data.message;
                        j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                        j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                        j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                    }
                }
            );
        },
        getPostCookieList:function() {
            var that = this;
            var data           = {
                action: 'gdpr_cookie_custom',
                security: settings_obj.cookie_list_settings.nonces.gdpr_cookie_custom,
                gdpr_custom_action:'get_post_cookies_list',
            };
            j.ajax(
                {
                    url: settings_obj.cookie_list_settings.ajax_url,
                    data: data,
                    dataType:'json',
                    type: 'POST',
                    success: function (data) {
                        if(data.response) {
                            that.post_cookie_list_length = data.total;
                            that.post_cookie_list = data.post_list;
                            that.setPostListValues();
                        }
                        else{
                            that.success_error_message = data.message;
                            j("#gdpr-cookie-consent-save-settings-alert").css('background-color', '#e55353' );
                            j("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
                            j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
                        }
                    },
                    error:function() {
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
        j('#gdpr-before-mount').css('display','none');
        this.setValues();
        this.setPostListValues();
        j('.gdpr-cookie-consent-settings-nav .nav .nav-item .nav-link').on('click', function() {
            let adminbar_height = j('#wpadminbar').height();
            let nav_bar_distance =  j('.gdpr-cookie-consent-settings-nav').offset().top;
            let scrolled_distance = nav_bar_distance - j(window).scrollTop();
            if( scrolled_distance <= adminbar_height ) {
                window.scroll(0, nav_bar_distance-adminbar_height);
            }
        })
        if( this.scan_cookie_list_length > 0 ) {
            this.setScanListValues();	
        }
    },
    icons: { cilPencil, cilSettings, cilInfo, cibGoogleKeep }
})