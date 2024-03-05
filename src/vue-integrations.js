import Vue from 'vue';
import CoreuiVue from '@coreui/vue';
import '@coreui/coreui/dist/css/coreui.min.css';

Vue.use(CoreuiVue);

const j = jQuery.noConflict();

var gen = new Vue({
    el: '#wpl-cookie-consent-integrations-page',
    data() {
        return {
          labelIcon: {
            labelOn: '\u2713',
            labelOff: '\u2715'
          },
          enable_geotargeting   : integrations_obj.geo_options.hasOwnProperty('enable_geotargeting') && (true === integrations_obj.geo_options['enable_geotargeting'] || 'true' === integrations_obj.geo_options['enable_geotargeting'] ) ? true : false,
          database_file_path    : integrations_obj.geo_options.hasOwnProperty('database_file_path') ? integrations_obj.geo_options['database_file_path'] : '',
          maxmind_license_key   : integrations_obj.geo_options.hasOwnProperty('maxmind_license_key') ? integrations_obj.geo_options['maxmind_license_key'] : '',
          alert_message         : 'Maxmind Key Integrated',
          maxmind_register_link : 'https://www.maxmind.com/en/geolite2/signup',
          document_link         : 'https://club.wpeka.com/docs/wp-cookie-consent/',
          video_link            : 'https://www.youtube.com/embed/hrfSoFjEpzQ',
          support_link          : 'https://club.wpeka.com/my-account/?utm_source=gdpr&utm_medium=plugin&utm_campaign=integrations',
		    }
    },
    methods: {
      OnEnableGeotargeting() {
        this.enable_geotargeting = !this.enable_geotargeting;
      },
      onSubmitIntegrations() {
        let that = this;
        if( this.maxmind_license_key === '' && this.enable_geotargeting ) {
            this.alert_message = 'Please enter a valid license key';
            j("#wpl-cookie-consent-integrations-alert").css('background-color', '#e55353' );	
            j("#wpl-cookie-consent-integrations-alert").fadeIn(400);	
            j("#wpl-cookie-consent-integrations-alert").fadeOut(2500);
            return;
        }
        var spinner = j('.wpl_integrations_spinner');
        spinner.show();
				spinner.css( { visibility: 'visible' } );
        j('#wpl-cookie-consent-overlay').css('display', 'block');
        var dataV = j("#wpl-cookie-consent-integrations-form").serialize();
        jQuery.ajax({
            type: 'POST',
            url: integrations_obj.ajax_url,
            data: dataV + '&action=wpl_cookie_consent_integrations_settings',
        }).done(function (data) {
          if( data.success ) {
            that.alert_message = that.enable_geotargeting ? 'Maxmind Integrated' : 'Settings Saved';
            j("#wpl-cookie-consent-integrations-alert").css('background-color', '#72b85c' );	
            j("#wpl-cookie-consent-integrations-alert").fadeIn(400);	
            j("#wpl-cookie-consent-integrations-alert").fadeOut(2500);
          } else {
            that.alert_message = 'Please enter a valid license key';
            j("#wpl-cookie-consent-integrations-alert").css('background-color', '#e55353' );	
            j("#wpl-cookie-consent-integrations-alert").fadeIn(400);	
            j("#wpl-cookie-consent-integrations-alert").fadeOut(2500);
          }
          j('#wpl-cookie-consent-overlay').css('display', 'none');
          spinner.css( { visibility: 'hidden' } );
			    spinner.hide();
          location.reload();
        });
      }
    },
    mounted() {
      j('#wpl-cookie-consent-integrations-loader').css('display','none');
      var spinner = j('.wpl_integrations_spinner');
      spinner.css( { visibility: 'hidden' } );
			spinner.hide();
	}
})