import Vue from 'vue';
import CoreuiVue from '@coreui/vue';
import vSelect from 'vue-select';
import '@coreui/coreui/dist/css/coreui.min.css';
import 'vue-select/dist/vue-select.css';

import { cilPencil, cilSettings, cilInfo, cibGoogleKeep } from '@coreui/icons';
Vue.use(CoreuiVue);
Vue.component('v-select', vSelect);

const j = jQuery.noConflict();

var gen = new Vue({
    el: '#gdpr-cookie-consent-settings-app',
    data() {
        return {
            text: 'Welcome to GDPR Cookie Consent',
            labelIcon: {
                labelOn: '\u2713',
                labelOff: '\u2715'
            },
            cookie_is_on: settings_obj.the_options.hasOwnProperty('is_on') && (true === settings_obj.the_options['is_on'] || 1 === settings_obj.the_options['is_on'] ) ? true : false,
            policy_options: settings_obj.policies,
            gdpr_policy: settings_obj.the_options.hasOwnProperty('cookie_usage_for') ? settings_obj.the_options['cookie_usage_for'] : 'gdpr',
            show_cookie_as_options: settings_obj.show_cookie_as_options,
            show_cookie_as: settings_obj.the_options.hasOwnProperty('cookie_bar_as') ? settings_obj.the_options['cookie_bar_as'] : 'banner',
            cookie_position_options: settings_obj.position_options,
            cookie_position: settings_obj.the_options.hasOwnProperty('notify_position_vertical') ? settings_obj.the_options['notify_position_vertical'] : 'bottom',
            on_hide_options: settings_obj.on_hide_options,
            on_hide: settings_obj.the_options.hasOwnProperty('notify_animate_hide') ? settings_obj.the_options['notify_animate_hide'] : '1',
            cookie_bar_color: settings_obj.the_options.hasOwnProperty('background') ? settings_obj.the_options['background'] : '#ffffff',
            //on_hide: settings_obj.the_options.hasOwnProperty('notify_animate_hide') && ( true === settings_obj.the_options['notify_animate_hide'] || 1 === settings_obj.the_options['notify_animate_hide'] ) ? true : false,
            }
    },
    methods: {
        onSwitchCookieEnable() {
            this.cookie_is_on = !this.cookie_is_on;
        },
        saveCookieSettings() {
            jQuery("#gdpr-cookie-consent-save-settings-alert").fadeIn(400);
            var dataV = jQuery("#gcc-save-settings-form").serialize();
            jQuery.ajax({
                type: 'POST',
                url: settings_obj.ajaxurl,
                data: dataV + '&action=gcc_save_admin_settings',
            }).done(function (data) {
                j("#gdpr-cookie-consent-save-settings-alert").fadeOut(2500);
            }); 
        }
    },
    mounted() {
		console.log('Mounted!')
    },
    icons: { cilPencil, cilSettings, cilInfo, cibGoogleKeep }
})