/**
 * Frontend JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 * @author     wpeka <https://club.wpeka.com>
 */

GDPR_ACCEPT_COOKIE_NAME   = (typeof GDPR_ACCEPT_COOKIE_NAME !== 'undefined' ? GDPR_ACCEPT_COOKIE_NAME : 'wpl_viewed_cookie');
GDPR_ACCEPT_COOKIE_EXPIRE = (typeof GDPR_ACCEPT_COOKIE_EXPIRE !== 'undefined' ? GDPR_ACCEPT_COOKIE_EXPIRE : 365);

(function( $ ) {
	'use strict';

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

	var GDPR_Cookie = {
		set: function (name, value, days) {
			if (days) {
				var date = new Date();
				date.setTime( date.getTime() + (days * 24 * 60 * 60 * 1000) );
				var expires = "; expires=" + date.toGMTString();
			} else {
				var expires = "";
			}
			document.cookie = name + "=" + value + expires + "; path=/";
			if (days < 1) {
				var host_name   = window.location.hostname;
				document.cookie = name + "=" + value + expires + "; path=/; domain=." + host_name + ";";
				if (host_name.indexOf( "www" ) != 1) {
					var host_name_withoutwww = host_name.replace( 'www','' );
					document.cookie          = name + "=" + value + expires + "; path=/; domain=" + host_name_withoutwww + ";";
				}
				host_name       = host_name.substring( host_name.lastIndexOf( ".", host_name.lastIndexOf( "." ) - 1 ) );
				document.cookie = name + "=" + value + expires + "; path=/; domain=" + host_name + ";";
			}
		},
		read: function (name) {
			var nameEQ    = name + "=";
			var ca        = document.cookie.split( ';' );
			var ca_length = ca.length;
			for (var i = 0; i < ca_length; i++) {
				var c = ca[i];
				while (c.charAt( 0 ) == ' ') {
					c = c.substring( 1, c.length );
				}
				if (c.indexOf( nameEQ ) === 0) {
					return c.substring( nameEQ.length, c.length );
				}
			}
			return null;
		},
		exists: function (name) {
			return (this.read( name ) !== null);
		},
		getallcookies:function()
		{
			var pairs        = document.cookie.split( ";" );
			var cookieslist  = {};
			var pairs_length = pairs.length;
			for (var i = 0; i < pairs_length; i++) {
				var pair                           = pairs[i].split( "=" );
				cookieslist[(pair[0] + '').trim()] = unescape( pair[1] );
			}
			return cookieslist;
		},
	}

	var GDPR =
		{
			bar_config:{},
			set:function(args)
			{
				if (typeof JSON.parse !== "function") {
					console.log( "GDPRCookieConsent requires JSON.parse but your browser doesn't support it" );
					return;
				}
				this.settings = JSON.parse( args.settings );
				this.bar_elm  = jQuery( this.settings.notify_div_id );

				this.details_elm = jQuery( '.gdpr_messagebar_detail' );

				/* buttons */
				this.main_button            = jQuery( '.gdpr-plugin-main-button' );
				this.main_button_accept_all = jQuery( '.gdpr-plugin-main-button-accept-all' );
				this.main_link              = jQuery( '.gdpr-plugin-main-link' );
				this.reject_link            = jQuery( '.cookie_action_close_header_reject' );
				this.settings_button        = jQuery( '#gdpr_action_settings' );
				this.hide_settings_button   = jQuery( '#gdpr_action_hide_settings' );

				this.configBar();
				this.toggleBar();
				this.attachEvents();
				this.configButtons();
				var gdpr_user_preference = JSON.parse( GDPR_Cookie.read( 'wpl_user_preference' ) );
				var gdpr_viewed_cookie   = GDPR_Cookie.read( 'wpl_viewed_cookie' );
				var event                = new CustomEvent(
					'GdprCookieConsentOnLoad',
					{detail: {
						'wpl_user_preference': gdpr_user_preference,
						'wpl_viewed_cookie' : gdpr_viewed_cookie,
						}}
				);
				window.dispatchEvent( event );
			},
			attachEvents:function()
			{
				jQuery( '.gdpr_action_button' ).click(
					function(e){
						e.preventDefault();
						var event                = '';
						var gdpr_user_preference = '';
						var gdpr_viewed_cookie   = '';
						var elm                  = jQuery( this );
						var button_action        = elm.attr( 'data-gdpr_action' );
						var open_link            = elm[0].hasAttribute( "href" ) && elm.attr( "href" ) != '#' ? true : false;
						var new_window           = false;
						if (button_action == 'accept') {
							GDPR.accept_close();
							new_window           = GDPR.settings.button_1_new_win ? true : false;
							gdpr_user_preference = JSON.parse( GDPR_Cookie.read( 'wpl_user_preference' ) );
							gdpr_viewed_cookie   = GDPR_Cookie.read( 'wpl_viewed_cookie' );
							event                = new CustomEvent(
								'GdprCookieConsentOnAccept',
								{detail: {
									'wpl_user_preference': gdpr_user_preference,
									'wpl_viewed_cookie' : gdpr_viewed_cookie,
									}}
							);
							window.dispatchEvent( event );
							GDPR.logConsent( button_action );
						} else if (button_action == 'accept_all') {
							var gdpr_user_preference_val;
							// check all checkboxes.
							$( '.gdpr_messagebar_body_button:checkbox:enabled' ).prop( 'checked', true );
							GDPR.accept_all_close();
							new_window           = GDPR.settings.button_4_new_win ? true : false;
							gdpr_user_preference = JSON.parse( GDPR_Cookie.read( 'wpl_user_preference' ) );
							// set all values to yes.
							for (var i in gdpr_user_preference) {
								if (Object.hasOwnProperty.call( gdpr_user_preference, i )) {
									gdpr_user_preference[i] = "yes";
								}
							}
							gdpr_user_preference_val = JSON.stringify( gdpr_user_preference );
							GDPR_Cookie.set( 'wpl_user_preference',gdpr_user_preference_val,GDPR_ACCEPT_COOKIE_EXPIRE );
							gdpr_viewed_cookie   = GDPR_Cookie.read( 'wpl_viewed_cookie' );
							gdpr_user_preference = JSON.parse( GDPR_Cookie.read( 'wpl_user_preference' ) );
							event                = new CustomEvent(
								'GdprCookieConsentOnAcceptAll',
								{detail: {
									'wpl_user_preference': gdpr_user_preference,
									'wpl_viewed_cookie' : gdpr_viewed_cookie,
									}}
							);
							window.dispatchEvent( event );
							GDPR.logConsent( button_action );
						} else if (button_action == 'reject') {
							GDPR.reject_close();
							new_window           = GDPR.settings.button_3_new_win ? true : false;
							gdpr_user_preference = JSON.parse( GDPR_Cookie.read( 'wpl_user_preference' ) );
							gdpr_viewed_cookie   = GDPR_Cookie.read( 'wpl_viewed_cookie' );
							event                = new CustomEvent(
								'GdprCookieConsentOnReject',
								{detail: {
									'wpl_user_preference': gdpr_user_preference,
									'wpl_viewed_cookie' : gdpr_viewed_cookie,
									}}
							);
							window.dispatchEvent( event );
							GDPR.logConsent( button_action );
						} else if (button_action == 'show_settings') {
							GDPR.show_details();
						} else if (button_action == 'hide_settings') {
							GDPR.hide_details();
						}
						if (open_link) {
							if (new_window) {
								window.open( elm.attr( "href" ),'_blank' );
							} else {
								window.location.href = elm.attr( "href" );
							}
						}
					}
				);

				jQuery( '#gdpr_messagebar_body_buttons_select_pane input' ).each(
					function(){
						var key                      = jQuery( this ).val();
						var gdpr_user_preference_arr = {};
						var gdpr_user_preference_val = '';
						if (GDPR_Cookie.read( 'wpl_user_preference' )) {
							gdpr_user_preference_arr = (JSON.parse( GDPR_Cookie.read( 'wpl_user_preference' ) ));
						}
						if (jQuery( this ).is( ':checked' )) {
							gdpr_user_preference_arr[key] = 'yes';
						} else {
							gdpr_user_preference_arr[key] = 'no';
						}
						gdpr_user_preference_val = JSON.stringify( gdpr_user_preference_arr );
						GDPR_Cookie.set( 'wpl_user_preference',gdpr_user_preference_val,GDPR_ACCEPT_COOKIE_EXPIRE );
					}
				);
				jQuery( document ).on(
					'click',
					'#gdpr_messagebar_body_buttons_select_pane input',
					function(){
						var key                      = jQuery( this ).val();
						var gdpr_user_preference_arr = {};
						var gdpr_user_preference_val = '';
						if (GDPR_Cookie.read( 'wpl_user_preference' )) {
							gdpr_user_preference_arr = (JSON.parse( GDPR_Cookie.read( 'wpl_user_preference' ) ));
						}
						if (jQuery( this ).is( ':checked' )) {
							gdpr_user_preference_arr[key] = 'yes';
						} else {
							gdpr_user_preference_arr[key] = 'no';
						}
						gdpr_user_preference_val = JSON.stringify( gdpr_user_preference_arr );
						GDPR_Cookie.set( 'wpl_user_preference',gdpr_user_preference_val,GDPR_ACCEPT_COOKIE_EXPIRE );
					}
				);
				jQuery( document ).on(
					'click',
					'#gdpr_messagebar_detail_body_content_tabs_overview',
					function(e){
						e.preventDefault();
						var elm = jQuery( this );
						jQuery( '#gdpr_messagebar_detail_body_content_tabs' ).find( 'a' ).removeClass( 'gdpr_messagebar_detail_body_content_tab_item_selected' );
						elm.addClass( 'gdpr_messagebar_detail_body_content_tab_item_selected' );
						elm.css( 'border-bottom-color', GDPR.settings.border_active_color );
						jQuery( '#gdpr_messagebar_detail_body_content_tabs_about' ).css( 'border-bottom-color', GDPR.settings.border_color );
						jQuery( '#gdpr_messagebar_detail_body_content_about' ).hide();
						jQuery( '#gdpr_messagebar_detail_body_content_overview' ).show();
					}
				);
				jQuery( document ).on(
					'click',
					'#gdpr_messagebar_detail_body_content_tabs_about',
					function(e){
						e.preventDefault();
						var elm = jQuery( this );
						jQuery( '#gdpr_messagebar_detail_body_content_tabs' ).find( 'a' ).removeClass( 'gdpr_messagebar_detail_body_content_tab_item_selected' );
						elm.addClass( 'gdpr_messagebar_detail_body_content_tab_item_selected' );
						elm.css( 'border-bottom-color', GDPR.settings.border_active_color );
						jQuery( '#gdpr_messagebar_detail_body_content_tabs_overview' ).css( 'border-bottom-color', GDPR.settings.border_color );
						jQuery( '#gdpr_messagebar_detail_body_content_overview' ).hide();
						jQuery( '#gdpr_messagebar_detail_body_content_about' ).show();
					}
				);
				jQuery( document ).on(
					'click',
					'#gdpr_messagebar_detail_body_content_overview_cookie_container_types a',
					function(e){
						e.preventDefault();
						var elm  = jQuery( this );
						var prnt = elm.parent();
						prnt.find( 'a' ).removeClass( 'gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected' );
						prnt.find( 'a' ).css( 'border-right-color', GDPR.settings.border_color );
						elm.addClass( 'gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected' );
						elm.css( 'border-right-color',GDPR.settings.border_active_color );
						var trgt = jQuery( this ).attr( 'data-target' );
						var cntr = prnt.siblings( '#gdpr_messagebar_detail_body_content_overview_cookie_container_type_details' );
						cntr.find( '.gdpr_messagebar_detail_body_content_cookie_type_details' ).hide();
						cntr.find( '#' + trgt + '' ).show();
					}
				);

			},

			configButtons:function()
			{
				/*[wpl_cookie_settings]*/
				this.settings_button.css( 'color',this.settings.button_2_link_color );
				this.hide_settings_button.css( 'color',this.settings.button_2_link_color );

				/*[wpl_cookie_button] */
				this.main_button.css( 'color',this.settings.button_1_link_color );
				if (this.settings.button_1_as_button) {
					this.main_button.css( 'background-color',this.settings.button_1_button_color );
					this.main_button.hover(
						function () {
							jQuery( this ).css( 'background-color',GDPR.settings.button_1_button_hover );
						},
						function (){
							jQuery( this ).css( 'background-color',GDPR.settings.button_1_button_color );
						}
					);
				}

				/*[wpl_cookie_accept_all_button] */
				this.main_button_accept_all.css( 'color',this.settings.button_4_link_color );
				if ('false' !== this.settings.button_4_as_button) {
					this.main_button_accept_all.css( 'background-color',this.settings.button_4_button_color );
					this.main_button_accept_all.hover(
						function () {
							jQuery( this ).css( 'background-color',GDPR.settings.button_4_button_hover );
						},
						function (){
							jQuery( this ).css( 'background-color',GDPR.settings.button_4_button_color );
						}
					);
				}

				/* [wpl_cookie_link] */
				this.main_link.css( 'color',this.settings.button_2_link_color );
				if (this.settings.button_2_as_button) {
					this.main_link.css( 'background-color',this.settings.button_2_button_color );
					this.main_link.hover(
						function () {
							jQuery( this ).css( 'background-color',GDPR.settings.button_2_button_hover );
						},
						function (){
							jQuery( this ).css( 'background-color',GDPR.settings.button_2_button_color );
						}
					);
				}

				/* [wpl_cookie_reject] */
				this.reject_link.css( 'color',this.settings.button_3_link_color );
				if (this.settings.button_3_as_button) {
					this.reject_link.css( 'background-color',this.settings.button_3_button_color );
					this.reject_link.hover(
						function () {
							jQuery( this ).css( 'background-color',GDPR.settings.button_3_button_hover );
						},
						function () {
							jQuery( this ).css( 'background-color',GDPR.settings.button_3_button_color );
						}
					);
				}
				this.details_elm.find( 'table.gdpr_messagebar_detail_body_content_cookie_type_table tr' ).css( 'border-color', GDPR.settings.border_color );
				this.details_elm.find( '.gdpr_messagebar_detail_body_content_cookie_type_intro' ).css( 'border-color',GDPR.settings.border_color );
				this.details_elm.find( 'a' ).each(
					function(){
						jQuery( this ).css( 'border-color',GDPR.settings.border_color );
					}
				)
				this.details_elm.find( 'a.gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected' ).css( 'border-right-color', GDPR.settings.border_active_color );
				this.details_elm.find( '#gdpr_messagebar_detail_body_content' ).css( 'border-color', GDPR.settings.border_color );
				this.details_elm.find( '#gdpr_messagebar_detail_body_content_tabs' ).css( 'border-color', GDPR.settings.border_color );
				this.details_elm.find( '#gdpr_messagebar_detail_body_content_tabs .gdpr_messagebar_detail_body_content_tab_item_selected' ).css( 'border-bottom-color', GDPR.settings.border_active_color );
			},

			configBar:function()
			{
				this.bar_config             = {
					'background-color':this.settings.background,
					'color':this.settings.text,
					'font-family':this.settings.font_family
				};
				this.bar_config['position'] = 'fixed';
				this.bar_config['bottom']   = '0';
				this.bar_elm.css( this.bar_config ).hide();
			},

			toggleBar:function()
			{
				if ( ! GDPR_Cookie.exists( GDPR_ACCEPT_COOKIE_NAME )) {
					this.displayHeader();
				} else {
					this.hideHeader();
				}
			},

			accept_close:function()
			{
				GDPR_Cookie.set( GDPR_ACCEPT_COOKIE_NAME,'yes',GDPR_ACCEPT_COOKIE_EXPIRE );
				if (this.settings.notify_animate_hide) {
					this.bar_elm.slideUp( this.settings.animate_speed_hide );
				} else {
					this.bar_elm.hide();
				}
				return false;
			},
			accept_all_close:function()
			{
				GDPR_Cookie.set( GDPR_ACCEPT_COOKIE_NAME,'yes',GDPR_ACCEPT_COOKIE_EXPIRE );
				if (this.settings.notify_animate_hide) {
					this.bar_elm.slideUp( this.settings.animate_speed_hide );
				} else {
					this.bar_elm.hide();
				}
				return false;
			},
			reject_close:function()
			{
				GDPR.disableAllCookies();
				GDPR_Cookie.set( GDPR_ACCEPT_COOKIE_NAME,'no',GDPR_ACCEPT_COOKIE_EXPIRE );
				if (this.settings.notify_animate_hide) {
					this.bar_elm.slideUp( this.settings.animate_speed_hide );
				} else {
					this.bar_elm.hide();
				}
				return false;
			},
			logConsent:function(btn_action) {
				if (this.settings.logging_on) {
					jQuery.ajax(
						{
							url: log_obj.ajax_url,
							type: 'POST',
							data:{
								action: 'gdpr_log_consent_action',
								gdpr_user_action:btn_action,
								cookie_list:GDPR_Cookie.getallcookies()
							},
							success:function (response)
						{

							}
						}
					);
				}
			},
			disableAllCookies:function() {
				var gdpr_user_preference_arr = {};
				var gdpr_user_preference_val = '';
				if (GDPR_Cookie.read( 'wpl_user_preference' )) {
					gdpr_user_preference_arr = (JSON.parse( GDPR_Cookie.read( 'wpl_user_preference' ) ));
					jQuery.each(
						gdpr_user_preference_arr,
						function(key, value) {
							if (key != 'necessary') {
								gdpr_user_preference_arr[key] = 'no';
							}
						}
					);
					gdpr_user_preference_val = JSON.stringify( gdpr_user_preference_arr );
					GDPR_Cookie.set( 'wpl_user_preference',gdpr_user_preference_val,GDPR_ACCEPT_COOKIE_EXPIRE );
				}
			},
			show_details:function() {
				this.details_elm.show();
				this.details_elm.css( 'border-top-color', GDPR.settings.border_color );
				this.settings_button.css( 'display','none' );
				this.hide_settings_button.css( 'display','inline-block' );
			},
			hide_details:function() {
				this.details_elm.hide();
				this.hide_settings_button.css( 'display','none' );
				this.settings_button.css( 'display','inline-block' );
			},
			displayHeader:function()
			{
				this.bar_elm.show();
			},
			hideHeader:function()
			{
				this.bar_elm.slideUp( this.settings.animate_speed_hide );
			},

	}
	$( document ).ready(
		function() {
			if (typeof gdpr_cookiebar_settings != 'undefined') {
				GDPR.set(
					{
						settings:gdpr_cookiebar_settings
					}
				);
			}
		}
	);

})( jQuery );
