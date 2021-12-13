import Vue from 'vue';
import CoreuiVue from '@coreui/vue';
import vSelect from 'vue-select';
import { VueEditor } from "vue2-editor";
import '@coreui/coreui/dist/css/coreui.min.css';
import 'vue-select/dist/vue-select.css';

import { cilPencil, cilSettings, cilInfo, cibGoogleKeep } from '@coreui/icons';
Vue.use(CoreuiVue);
Vue.component('v-select', vSelect);
Vue.component('vue-editor', VueEditor);

const j = jQuery.noConflict();

var gen = new Vue({
    el: '#gdpr-cookie-consent-settings-app',
    data() {
        return {
            labelIcon: {
                labelOn: '\u2713',
                labelOff: '\u2715'
            },
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
            }
            else if(this.gdpr_policy === 'ccpa') {
                this.is_ccpa = true;
                this.is_eprivacy = false;
                this.is_gdpr = false;
                this.show_visitor_conditions = true;
            }
            else if(this.gdpr_policy === 'gdpr') {
                this.is_gdpr = true;
                this.is_ccpa = false;
                this.is_eprivacy = false;
                if( settings_obj.is_pro_active ) {
                    this.show_visitor_conditions = true;
                }
            }
            else {
                this.is_eprivacy = true;
                this.is_gdpr = false;
                this.is_ccpa = false;
                this.show_visitor_conditions = false;
            }
        },
        onSwitchCookieEnable() {
            this.cookie_is_on = !this.cookie_is_on;
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
        cookiePolicyChange( value ) {
            if(value === 'both') {
                this.is_ccpa = true;
                this.is_gdpr = true;
                this.is_eprivacy = false;
                this.show_visitor_conditions = true;
            }
            else if(value === 'ccpa') {
                this.is_ccpa = true;
                this.is_eprivacy = false;
                this.is_gdpr = false;
                this.show_visitor_conditions = true;
            }
            else if(value === 'gdpr') {
                this.is_gdpr = true;
                this.is_ccpa = false;
                this.is_eprivacy = false;
                if( settings_obj.is_pro_active ) {
                    this.show_visitor_conditions = true;
                }
            }
            else {
                this.is_eprivacy = true;
                this.is_gdpr = false;
                this.is_ccpa = false;
                this.show_visitor_conditions = false;
            }
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
        this.setValues();
    },
    icons: { cilPencil, cilSettings, cilInfo, cibGoogleKeep }
})