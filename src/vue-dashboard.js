import Vue from 'vue';
import CoreuiVue from '@coreui/vue';
import '@coreui/coreui/dist/css/coreui.min.css';
import { VueEllipseProgress } from "vue-ellipse-progress";

Vue.use(CoreuiVue);
Vue.component('vue-ellipse-progress', VueEllipseProgress);

const j = jQuery.noConflict();

var gen = new Vue({
    el: '#gdpr-cookie-consent-dashboard-page',
    data() {
        return {
			showing_cookie_notice: dashboard_options.hasOwnProperty('showing_cookie_notice') && dashboard_options['showing_cookie_notice'] === '1' ? true : false,
			pro_activated: dashboard_options.hasOwnProperty('pro_activated') && dashboard_options['pro_activated'] === '1' ? true : false,
			pro_installed: dashboard_options.hasOwnProperty('pro_installed') && dashboard_options['pro_installed'] === '1' ? true : false,
			maxmind_integrated: dashboard_options.hasOwnProperty('maxmind_integrated') && dashboard_options['maxmind_integrated'] === '2' ? true : false,
			last_scanned: dashboard_options.hasOwnProperty('last_scanned') ? dashboard_options['last_scanned'] : 'Website not scanned for Cookies.',
			active_plugins: dashboard_options.hasOwnProperty('active_plugins') ? dashboard_options['active_plugins'] : [],
			other_plugins_active: false,
			api_key_activated: dashboard_options.hasOwnProperty('api_key_activated') && dashboard_options['api_key_activated'] === 'Activated' ? true : false,
			cookie_scanned: false,
			progress: 0,
			show_cookie_url: dashboard_options.hasOwnProperty('show_cookie_url') ? dashboard_options['show_cookie_url'] : '',
			maxmind_url: dashboard_options.hasOwnProperty('maxmind_url') ? dashboard_options['maxmind_url'] : '',
			cookie_scan_url: dashboard_options.hasOwnProperty('cookie_scan_url') ? dashboard_options['cookie_scan_url'] : '',
			plugin_page_url: dashboard_options.hasOwnProperty('plugin_page_url') ? dashboard_options['plugin_page_url'] : '',
			consent_log_url: dashboard_options.hasOwnProperty('consent_log_url') ? dashboard_options['consent_log_url'] : '',
			cookie_design_url: dashboard_options.hasOwnProperty('cookie_design_url') ? dashboard_options['cookie_design_url'] : '',
			cookie_template_url: dashboard_options.hasOwnProperty('cookie_template_url') ? dashboard_options['cookie_template_url'] : '',
			script_blocker_url: dashboard_options.hasOwnProperty('script_blocker_url') ? dashboard_options['script_blocker_url'] : '',
			third_party_url: dashboard_options.hasOwnProperty('third_party_url') ? dashboard_options['third_party_url'] : '',
			legalpages_url: dashboard_options.hasOwnProperty('legalpages_url') ? dashboard_options['legalpages_url'] : '',
			adcenter_url: dashboard_options.hasOwnProperty('adcenter_url') ? dashboard_options['adcenter_url'] : '',
			survey_funnel_url: dashboard_options.hasOwnProperty('survey_funnel_url') ? dashboard_options['survey_funnel_url'] : '',
			gdpr_pro_url: dashboard_options.hasOwnProperty('gdpr_pro_url') ? dashboard_options['gdpr_pro_url'] : '',
			documentation_url: dashboard_options.hasOwnProperty('documentation_url') ? dashboard_options['documentation_url'] : '',
			free_support_url: dashboard_options.hasOwnProperty('free_support_url') ? dashboard_options['free_support_url'] : '',
			pro_support_url: dashboard_options.hasOwnProperty('pro_support_url') ? dashboard_options['pro_support_url'] : '',
			videos_url: dashboard_options.hasOwnProperty('videos_url') ? dashboard_options['videos_url'] : '',
			key_activate_url: dashboard_options.hasOwnProperty('key_activate_url') ? dashboard_options['key_activate_url'] : '',
			all_plugins_url: 'https://profiles.wordpress.org/wpeka-club/#content-plugins',
			faq1_url: 'https://youtu.be/ZESzSKnUkOg',
			faq2_url: 'https://wplegalpages.com/blog/what-you-need-to-know-about-the-eu-cookie-law/?utm_source=gdpr&utm_medium=dashboard&utm_campaign=tips',
			faq3_url: 'https://wplegalpages.com/wordpress-cookie-consent-eprivacy-gdpr/?utm_source=gdpr&utm_medium=dashboard&utm_campaign=tips',
			faq4_url: 'https://wplegalpages.com/blog/california-consumer-privacy-act-become-ccpa-compliant-today/?utm_source=gdpr&utm_medium=dashboard&utm_campaign=tips',
			faq5_url: 'https://wplegalpages.com/blog/interactive-advertising-bureau-all-you-need-to-know/?utm_source=gdpr&utm_medium=dashboard&utm_campaign=tips',
			cookie_scan_image: require('../admin/images/dashboard-icons/blue/cookie-scan-icon.png'),
			consent_log_image: require('../admin/images/dashboard-icons/blue/consent-logging-icon.png'),
			cookie_design_image: require('../admin/images/dashboard-icons/blue/design-icon.png'),
			cookie_template_image: require('../admin/images/dashboard-icons/blue/templates-icon.png'),
			settings_image: require('../admin/images/dashboard-icons/blue/settings-icon.png'),
			cookie_table_image: require('../admin/images/dashboard-icons/blue/cookie-table-icon.png'),
			geolocation_image: require('../admin/images/dashboard-icons/blue/geolocation-icon.png'),
			script_blocker_image: require('../admin/images/dashboard-icons/blue/script-blocker-icon.png'),
			cookie_scan_image_disabled: require('../admin/images/dashboard-icons/gray/cookie-scan-icon.png'),
			consent_log_image_disabled: require('../admin/images/dashboard-icons/gray/consent-logging-icon.png'),
			cookie_design_image_disabled: require('../admin/images/dashboard-icons/gray/design-icon.png'),
			cookie_template_image_disabled: require('../admin/images/dashboard-icons/gray/template-icon.png'),
			geolocation_image_disabled: require('../admin/images/dashboard-icons/gray/geolocation-icon.png'),
			script_blocker_image_disabled: require('../admin/images/dashboard-icons/gray/script-blocker-icon.png'),
			cookie_table_image_disabled: require('../admin/images/dashboard-icons/gray/cookie-table-icon.png'),
			adcenter_icon: require('../admin/images/dashboard-icons/adcenter-icon.png'),
			legalpages_icon: require('../admin/images/dashboard-icons/legalpages-icon.png'),
			survey_funnel_icon: require('../admin/images/dashboard-icons/survey-funnel-icon.png'),
			arrow_icon: require('../admin/images/dashboard-icons/arrow-icon.png'),
			highlight_variant: 'outline',
		}
    },
    methods: {
		setValues() {
			this.active_plugins = Object.values(this.active_plugins);
			let plugins_length = this.active_plugins.length;
			for(let i = 0; i < plugins_length; i++) {
				let plugin = this.active_plugins[i];
				if( ! ( 'gdpr-cookie-consent/gdpr-cookie-consent.php' === plugin || 'wpl-cookie-consent/wpl-cookie-consent.php' === plugin ) ) {
					if( plugin.indexOf('cookie') !== -1 || plugin.indexOf('gdpr') !== -1 || plugin.indexOf('ccpa') !== -1 || plugin.indexOf('compliance') !== -1 ) {
						this.other_plugins_active = true;
						break;
					}
				}
			}
			if( this.pro_activated && this.last_scanned !== 'Perform your first Cookie Scan.' ) {
				this.cookie_scanned = true;
			}
			let count_progress = 0;
			if( ! this.other_plugins_active ) {
				count_progress++;
			}
			if( this.api_key_activated && this.cookie_scanned ) {
				count_progress++;
			}
			if( this.showing_cookie_notice ) {
				count_progress++;
			}
			if( this.api_key_activated ) {
				count_progress++;
			}
			if( this.api_key_activated && this.maxmind_integrated ) {
				count_progress++;
			}
			this.progress = (count_progress/ 5 ) * 100;
		}
    },
    mounted() {
        j('#gdpr-dashboard-loader').css('display','none');
		this.setValues();
	}
})