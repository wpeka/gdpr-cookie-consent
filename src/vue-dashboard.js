import Vue from 'vue';
import CoreuiVue from '@coreui/vue';
import '@coreui/coreui/dist/css/coreui.min.css';
import { VueEllipseProgress } from "vue-ellipse-progress";
import VueApexCharts from 'vue-apexcharts'

Vue.use(CoreuiVue);
Vue.use(VueApexCharts)
Vue.component('vue-ellipse-progress', VueEllipseProgress);
Vue.component('apexchart', VueApexCharts)

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
			language_url: dashboard_options.hasOwnProperty('language_url') ? dashboard_options['language_url'] : '',
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
			faq2_url: 'https://wplegalpages.com/blog/what-you-need-to-know-about-the-eu-cookie-law/?utm_source=plugin&utm_medium=gdpr&utm_campaign=tips-tricks&utm_content=eu-cookie-law',
			faq3_url: 'https://club.wpeka.com/docs/wp-cookie-consent/faqs/faq-2/',
			faq4_url: 'https://wplegalpages.com/blog/california-consumer-privacy-act-become-ccpa-compliant-today/?utm_source=plugin&utm_medium=gdpr&utm_campaign=tips-tricks&utm_content=ccpa-regulations',
			faq5_url: 'https://wplegalpages.com/blog/interactive-advertising-bureau-all-you-need-to-know/?utm_source=plugin&utm_medium=gdpr&utm_campaign=tips-tricks&utm_content=iab',
			cookie_scan_image: require('../admin/images/dashboard-icons/blue/cookie-scan-icon.svg'),
			consent_log_image: require('../admin/images/dashboard-icons/blue/consent-logging-icon.svg'),
			cookie_design_image: require('../admin/images/dashboard-icons/blue/design-icon.svg'),
			cookie_template_image: require('../admin/images/dashboard-icons/blue/templates-icon.svg'),
			settings_image: require('../admin/images/dashboard-icons/blue/settings-icon.svg'),
			cookie_table_image: require('../admin/images/dashboard-icons/blue/cookie-table-icon.svg'),
			geolocation_image: require('../admin/images/dashboard-icons/blue/geolocation-icon.svg'),
			script_blocker_image: require('../admin/images/dashboard-icons/blue/script-blocker-icon.svg'),
			cookie_scan_image_disabled: require('../admin/images/dashboard-icons/gray/cookie-scan-icon.png'),
			consent_log_image_disabled: require('../admin/images/dashboard-icons/gray/consent-logging-icon.png'),
			cookie_design_image_disabled: require('../admin/images/dashboard-icons/gray/design-icon.png'),
			cookie_template_image_disabled: require('../admin/images/dashboard-icons/gray/template-icon.png'),
			geolocation_image_disabled: require('../admin/images/dashboard-icons/gray/geolocation-icon.png'),
			script_blocker_image_disabled: require('../admin/images/dashboard-icons/gray/script-blocker-icon.png'),
			cookie_table_image_disabled: require('../admin/images/dashboard-icons/gray/cookie-table-icon.png'),
			adcenter_icon: require('../admin/images/dashboard-icons/adcenter-icon.png'),
			auction_icon: require('../admin/images/dashboard-icons/wp-auction-icon.png'),
			localplus_icon: require('../admin/images/dashboard-icons/wp-local-plus-icon.png'),
			legalpages_icon: require('../admin/images/dashboard-icons/legalpages-icon.png'),
			video_guide: require('../admin/images/dashboard-icons/video_guide.svg'),
			faq_question: require('../admin/images/dashboard-icons/faq-question.svg'),
			documentation: require('../admin/images/dashboard-icons/documentation.svg'),
			feedback: require('../admin/images/dashboard-icons/feedback.svg'),
			found_bug: require('../admin/images/dashboard-icons/found-bug.svg'),
			survey_funnel_icon: require('../admin/images/dashboard-icons/survey-funnel-icon.png'),
			arrow_icon: require('../admin/images/dashboard-icons/arrow-icon.png'),
			right_arrow: require('../admin/images/dashboard-icons/right-arrow.svg'),
			angle_arrow: require('../admin/images/dashboard-icons/angle-arrow.svg'),
			legalpages_icon: require('../admin/images/dashboard-icons/legalpages-icon.png'),
			survey_funnel_icon: require('../admin/images/dashboard-icons/survey-funnel-icon.png'),
			arrow_icon: require('../admin/images/dashboard-icons/arrow-icon.png'),
			cookie_summary:  require('../admin/images/dashboard-icons/summary/total-cookies.svg'),
			cookie_cat:  require('../admin/images/dashboard-icons/summary/categories.svg'),
			search_icon:  require('../admin/images/dashboard-icons/summary/search-icon.svg'),
			page_icon:  require('../admin/images/dashboard-icons/summary/pages.svg'),
			next_scan_icon:  require('../admin/images/dashboard-icons/summary/next-scan.svg'),
			view_all_logs:  require('../admin/images/dashboard-icons/summary/view-all-logs.png'),
			policy_icon:   require('../admin/images/dashboard-icons/summary/vector.svg'),
			admin_icon: require('../admin/images/dashboard-icons/summary/admin.svg'),
			highlight_variant: 'outline',
			decline_log: dashboard_options.hasOwnProperty('decline_log') ? dashboard_options['decline_log'] : 0,
			accept_log: dashboard_options.hasOwnProperty('accept_log') ? dashboard_options['accept_log'] : 0,
			partially_acc_log: dashboard_options.hasOwnProperty('partially_acc_log') ? dashboard_options['partially_acc_log'] : 0,
			series: [Number(dashboard_options['accept_log']),Number(dashboard_options['decline_log']),Number(dashboard_options['partially_acc_log'])],
			is_user_connected: dashboard_options.hasOwnProperty('is_user_connected') && dashboard_options['is_user_connected'] ===  'true' ? true : false,
			chartOptions: {
				chart: {
				  width: 580,
				  type: 'pie',
				},
				labels: ['Accepted', 'Rejected', 'Partially Accepted'],
				colors: ['#DAF2CB', '#F1C7C7', '#BDF'],
				fill: {
					colors: ['#DAF2CB', '#F1C7C7', '#BDF']
				  },
				responsive: [{
				  breakpoint: 480,
				  options: {
					chart: {
					  width: 200
					},
					legend: {
					  position: 'bottom'
					}
				  }
				}],
				dataLabels: {
					enabled: true,
					enabledOnSeries: undefined,
					textAnchor: 'middle',
					distributed: false,
					style: {
						fontSize: '14px',
						fontWeight: 400,
						colors: ['#333'],
						textAlign: 'center'
					},
					dropShadow: {
						enabled: false,
					}
				  },
				  plotOptions: {
					pie: {
					  dataLabels: {
						offset: -20,
					  },
					}
				  },
				  states: {
					normal: {
						filter: {
							type: 'dark',
							value: 0,
						}
					},
					hover: {
						filter: {
							type: 'dark',
							value: 0.15,
						}
					},
					active: {
						allowMultipleDataPointsSelection: false,
						filter: {
							type: 'dark',
							value: 0.35,
						}
					},
				},

			  },
			  banner_preview: true,
			  banner_preview_is_on:
					  "true" == dashboard_options.the_options["banner_preview_enable"] ||
					  1 === dashboard_options.the_options["banner_preview_enable"]
						? true
						: false,
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
			// if user is connected to the api and last scan has not been performed, set cookie_scanned to true.
			if( this.is_user_connected && this.last_scanned !== 'Perform your first Cookie Scan.' ) {
				this.cookie_scanned = true;
			}
			let count_progress = 0;
			if( ! this.other_plugins_active ) {
				count_progress++;
			}
			if( this.api_key_activated && this.cookie_scanned ) {
				count_progress++;
			}
			// increase progress when user is connected and scan performed.
			if( this.is_user_connected && !this.pro_installed && this.cookie_scanned ) {
				count_progress++;
			}
			if( this.showing_cookie_notice ) {
				count_progress++;
			}
			if( this.api_key_activated ) {
				count_progress++;
			}
			// increase progress when user is connected to the api.
			if( this.is_user_connected && !this.pro_installed ) {
				count_progress++;
			}
			if( this.api_key_activated && this.maxmind_integrated ) {
				count_progress++;
			}
			// increase progress when user is connected to the api and maxmind is integrated.
			if( this.is_user_connected && !this.pro_installed && this.maxmind_integrated ) {
				count_progress++;
			}
			this.progress = (count_progress/ 5 ) * 100;
		},
		onSwitchBannerPreviewEnable() {
			//changing the value of banner_preview_swicth_value enable/disable
			this.banner_preview_is_on = !this.banner_preview_is_on;
		  },
    },
    mounted() {
        j('#gdpr-dashboard-loader').css('display','none');
		this.setValues();
	}
})
