/**
 * Frontend JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 * @author     wpeka <https://club.wpeka.com>
 */

GDPR_ACCEPT_COOKIE_NAME   = (typeof GDPR_ACCEPT_COOKIE_NAME !== 'undefined' ? GDPR_ACCEPT_COOKIE_NAME : 'wpl_viewed_cookie');
GDPR_CCPA_COOKIE_NAME     = (typeof GDPR_CCPA_COOKIE_NAME !== 'undefined' ? GDPR_CCPA_COOKIE_NAME : 'wpl_optout_cookie');
US_PRIVACY_COOKIE_NAME    = (typeof US_PRIVACY_COOKIE_NAME !== 'undefined' ? US_PRIVACY_COOKIE_NAME : 'usprivacy');
GDPR_ACCEPT_COOKIE_EXPIRE = (typeof GDPR_ACCEPT_COOKIE_EXPIRE !== 'undefined' ? GDPR_ACCEPT_COOKIE_EXPIRE : 365);
GDPR_CCPA_COOKIE_EXPIRE   = (typeof GDPR_CCPA_COOKIE_EXPIRE !== 'undefined' ? GDPR_CCPA_COOKIE_EXPIRE : 365);

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
			document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/";
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
					return decodeURIComponent( c.substring( nameEQ.length, c.length ) );
				}
			}
			return null;
		},
		exists: function (name) {
			return (this.read( name ) !== null);
		},
		getallcookies: function() {
			var pairs        = document.cookie.split( ";" );
			var cookieslist  = {};
			var pairs_length = pairs.length;
			for (var i = 0; i < pairs_length; i++) {
				var pair                           = pairs[i].split( "=" );
				cookieslist[(pair[0] + '').trim()] = unescape( pair[1] );
			}
			return cookieslist;
		},
		erase: function (name) {
			this.set( name, "", -10 );
		},
	}

	var gdpr_cookiebar_settings = gdpr_cookies_obj.gdpr_cookiebar_settings;
	var gdpr_cookies_list = gdpr_cookies_obj.gdpr_cookies_list;
	var GDPR = {
		bar_config:{},
		show_config:{},
		allowed_categories:[],
		set:function(args) {
			if (typeof JSON.parse !== "function") {
				console.log( "GDPRCookieConsent requires JSON.parse but your browser doesn't support it" );
				return;
			}

			this.settings             = JSON.parse( args.settings );
			GDPR_ACCEPT_COOKIE_EXPIRE = this.settings.cookie_expiry;
			this.bar_elm              = jQuery( this.settings.notify_div_id );
			this.show_again_elm       = jQuery( this.settings.show_again_div_id );

			this.details_elm = this.bar_elm.find( '.gdpr_messagebar_detail' );

			/* buttons */
			this.main_button       = jQuery( '#cookie_action_accept' );
			this.accept_all_button = jQuery( '#cookie_action_accept_all' );
			this.main_link         = jQuery( '#cookie_action_link' );
			this.donotsell_link    = jQuery( '#cookie_donotsell_link' );
			this.reject_button     = jQuery( '#cookie_action_reject' );
			this.settings_button   = jQuery( '#cookie_action_settings' );
			this.save_button       = jQuery( '#cookie_action_save' );
			this.credit_link       = jQuery( '#cookie_credit_link' );
			this.confirm_button    = jQuery( '#cookie_action_confirm' );
			this.cancel_button     = jQuery( '#cookie_action_cancel' );

			this.configBar();

			if (this.settings.pro_active && this.settings.maxmind_integrated == '2') {
				this.check_ccpa_eu();
			}
			else{
				this.toggleBar();
			}

			this.attachEvents();
			this.configButtons();
			



			if ( this.settings.cookie_usage_for == 'gdpr' || this.settings.cookie_usage_for == 'eprivacy' || this.settings.cookie_usage_for == 'both' ) {
				if ( this.settings.auto_scroll ) {
					window.addEventListener( "scroll", GDPR.acceptOnScroll, false );
				}
				if ( this.settings.auto_click ) {
					if ( ! GDPR_Cookie.exists( GDPR_ACCEPT_COOKIE_NAME ) ) {
						var the_cookie_bar = document.querySelector('#gdpr-cookie-consent-bar');
						var setting_modal = document.querySelector('.gdprmodal-content');
						// Listen for click events on body
						document.body.addEventListener('click', function (event) {
							if ( ! the_cookie_bar.contains( event.target ) && ( (! setting_modal) || (setting_modal && ! setting_modal.contains( event.target ) ) ) ) {
								if ( GDPR.settings.button_accept_all_is_on ){
									GDPR.acceptAllCookies();
								}
								GDPR.accept_close();
								if ( GDPR.settings.auto_scroll_reload == true ) {
									window.location.reload();
								}
							} 
						});
					}
				}
				var gdpr_user_preference = JSON.parse( GDPR_Cookie.read( 'wpl_user_preference' ) );
				var gdpr_viewed_cookie   = GDPR_Cookie.read( 'wpl_viewed_cookie' );
				var event                = '';
				if (this.settings.cookie_usage_for == 'gdpr') {
					event = new CustomEvent(
						'GdprCookieConsentOnLoad',
						{
							detail: {
								'wpl_user_preference': gdpr_user_preference,
								'wpl_viewed_cookie' : gdpr_viewed_cookie,
							}
						}
					);
					window.dispatchEvent( event );
				} else if (this.settings.cookie_usage_for == 'eprivacy') {
					event = new CustomEvent(
						'GdprCookieConsentOnLoad',
						{
							detail: {
								'wpl_viewed_cookie' : gdpr_viewed_cookie
							}
						}
					);
					window.dispatchEvent( event );
				}

			}
		},
		check_ccpa_eu: function( force_display_bar, force_display_show_again ) {

			var data = {
				action: 'show_cookie_consent_bar',
			};
			$.ajax(
				{
					type: 'post',
					url: log_obj.ajax_url,
					data: data,
					dataType: 'json',
					success: function (response) {

						if (response.error) {
							// handle error here.
						} else {
							var geo_flag   = true;
							var gdpr_flag  = false;
							var ccpa_flag  = false;
							var cookieData = JSON.parse( gdpr_cookiebar_settings );
							var cookie_for = cookieData['cookie_usage_for'];
							if ('both' == cookie_for) {
								if (GDPR_Cookie.exists( GDPR_ACCEPT_COOKIE_NAME ) && GDPR_Cookie.exists( GDPR_CCPA_COOKIE_NAME )) {
									GDPR.hideHeader();
								} else if (GDPR_Cookie.exists( GDPR_ACCEPT_COOKIE_NAME ) && ! GDPR_Cookie.exists( GDPR_CCPA_COOKIE_NAME )) {
									if(response.ccpa_status == 'on'){
										GDPR.displayHeader( true, false, force_display_bar, true );

									}
									else{										
										GDPR.displayHeader(true, true, force_display_bar, true );

									}
								} else if ( ! GDPR_Cookie.exists( GDPR_ACCEPT_COOKIE_NAME ) && GDPR_Cookie.exists( GDPR_CCPA_COOKIE_NAME ) && response.eu_status == 'on') {
										GDPR.displayHeader( false, true, force_display_bar, force_display_show_again );
									if ( GDPR.settings.auto_hide ) {
										setTimeout(
											function(){
												GDPR.accept_close();
											},
											GDPR.settings.auto_hide_delay
										);
									}
									
									
								} 
								else if ( ! GDPR_Cookie.exists( GDPR_ACCEPT_COOKIE_NAME ) && ! GDPR_Cookie.exists( GDPR_CCPA_COOKIE_NAME )) {
									GDPR.checkEuAndCCPAStatus(response);
								} 
								
							} else if ('gdpr' == cookie_for) {

								if ( ! GDPR_Cookie.exists( GDPR_ACCEPT_COOKIE_NAME ) ) {
									GDPR.displayHeader();
									if ( GDPR.settings.auto_hide ) {
										setTimeout(
											function(){
												GDPR.accept_close();
											},
											GDPR.settings.auto_hide_delay
										);
									}
								} else {
									GDPR.hideHeader();

								}
							} else if ('ccpa' == cookie_for) {
								if ( ! GDPR_Cookie.exists( GDPR_CCPA_COOKIE_NAME )) {
									GDPR.displayHeader();
								} else {
									GDPR.hideHeader();
								}
							} else if ('eprivacy' == cookie_for) {
								if ( ! GDPR_Cookie.exists( GDPR_ACCEPT_COOKIE_NAME )) {
									GDPR.displayHeader();
									if ( GDPR.settings.auto_hide ) {
										setTimeout(
											function(){
												GDPR.accept_close();
											},
											GDPR.settings.auto_hide_delay
										);
									}
								} else {
									GDPR.hideHeader();
								}

							}
						}
					},
				}
			);
		},
		checkEuAndCCPAStatus:function(response) {

				if (response.eu_status == 'on' && response.ccpa_status == 'off' ) {
					GDPR.displayHeader( false, true );
									if ( GDPR.settings.auto_hide ) {
										setTimeout(
											function(){
												GDPR.accept_close();
											},
											GDPR.settings.auto_hide_delay
										);
									}

				} else if (response.eu_status == 'off' && response.ccpa_status == 'on') {
					GDPR.displayHeader( true, false );
				}
				 if (response.eu_status == 'on' && response.ccpa_status == 'on') {
					GDPR.displayHeader( false, false );
					if ( GDPR.settings.auto_hide ) {
						setTimeout(
							function(){
								GDPR.accept_close();
							},
							GDPR.settings.auto_hide_delay
						);
					}

				}
				 if (response.eu_status == 'off' && response.ccpa_status == 'off') {
					GDPR.hideHeader( true );
					GDPR.displayHeader( false,false );
				}	
		},
		attachEvents:function() {
			jQuery( '.gdpr_action_button' ).click(
				function(e){
					e.preventDefault();
					var event                    = '';
					var gdpr_user_preference     = '';
					var gdpr_user_preference_val = '';
					var gdpr_viewed_cookie       = '';
					var gdpr_optout_cookie       = '';
					var elm                      = jQuery( this );
					var button_action            = elm.attr( 'data-gdpr_action' );
					var open_link                = elm[0].hasAttribute( "href" ) && elm.attr( "href" ) != '#' ? true : false;
					var new_window               = false;
					if (button_action == 'accept') {
						GDPR.accept_close();
						new_window               = GDPR.settings.button_accept_new_win ? true : false;
						gdpr_user_preference     = JSON.parse( GDPR_Cookie.read( 'wpl_user_preference' ) );
						gdpr_user_preference_val = JSON.stringify( gdpr_user_preference );
						gdpr_viewed_cookie       = GDPR_Cookie.read( 'wpl_viewed_cookie' );
						if (GDPR.settings.cookie_usage_for == 'gdpr') {
							GDPR_Cookie.set( 'wpl_user_preference',gdpr_user_preference_val,GDPR_ACCEPT_COOKIE_EXPIRE );
							event = new CustomEvent(
								'GdprCookieConsentOnAccept',
								{
									detail: {
										'wpl_user_preference': gdpr_user_preference,
										'wpl_viewed_cookie' : gdpr_viewed_cookie,
									}
								}
							);
							window.dispatchEvent( event );
						} else if (GDPR.settings.cookie_usage_for == 'eprivacy') {
							event = new CustomEvent(
								'GdprCookieConsentOnAccept',
								{
									detail: {
										'wpl_viewed_cookie' : gdpr_viewed_cookie
									}
								}
							);
							window.dispatchEvent( event );
						}

						GDPR.logConsent( button_action );
					} else if (button_action == 'accept_all') {
						GDPR.accept_close();
						GDPR.acceptAllCookies();
						new_window               = GDPR.settings.button_accept_all_new_win ? true : false;
						gdpr_viewed_cookie       = GDPR_Cookie.read( 'wpl_viewed_cookie' );
						if (GDPR.settings.cookie_usage_for == 'gdpr') {
							event = new CustomEvent(
								'GdprCookieConsentOnAcceptAll',
								{
									detail: {
										'wpl_user_preference': gdpr_user_preference,
										'wpl_viewed_cookie' : gdpr_viewed_cookie,
									}
								}
							);
							window.dispatchEvent( event );
						} else if (GDPR.settings.cookie_usage_for == 'eprivacy') {
							event = new CustomEvent(
								'GdprCookieConsentOnAcceptAll',
								{
									detail: {
										'wpl_viewed_cookie' : gdpr_viewed_cookie
									}
								}
							);
							window.dispatchEvent( event );
						}
						GDPR.logConsent( button_action );
					} 
					 else if (button_action == 'reject') {
						GDPR.reject_close();
						new_window           = GDPR.settings.button_decline_new_win ? true : false;
						gdpr_user_preference = JSON.parse( GDPR_Cookie.read( 'wpl_user_preference' ) );
						gdpr_viewed_cookie   = GDPR_Cookie.read( 'wpl_viewed_cookie' );
						if (GDPR.settings.cookie_usage_for == 'gdpr') {
							event = new CustomEvent(
								'GdprCookieConsentOnReject',
								{
									detail: {
										'wpl_user_preference': gdpr_user_preference,
										'wpl_viewed_cookie' : gdpr_viewed_cookie,
									}
								}
							);
							window.dispatchEvent( event );
						} else if (GDPR.settings.cookie_usage_for == 'eprivacy') {
							event = new CustomEvent(
								'GdprCookieConsentOnReject',
								{
									detail: {
										'wpl_viewed_cookie' : gdpr_viewed_cookie
									}
								}
							);
							window.dispatchEvent( event );
						}

						GDPR.logConsent( button_action );
					} else if ( button_action == 'settings' ) {
						GDPR.bar_elm.slideUp( GDPR.settings.animate_speed_hide );
						if (GDPR.settings.cookie_bar_as == 'popup') {
							$( "#gdpr-popup" ).gdprmodal( 'hide' );
						}
						GDPR.show_again_elm.slideUp( GDPR.settings.animate_speed_hide );
					} else if ( button_action == 'close' ) {
						GDPR.displayHeader();
					} else if (button_action == 'show_settings') {
						GDPR.show_details();
					} else if (button_action == 'hide_settings') {
						GDPR.hide_details();
					} else if (button_action == 'donotsell') {
						if (GDPR.settings.cookie_usage_for == 'ccpa' || jQuery( GDPR.settings.notify_div_id ).find( 'p.gdpr' ).css( 'display' ) == 'none') {
							GDPR.hideHeader( true );
						} else {
							GDPR.hideHeader();
						}
						$( '#gdpr-ccpa-gdprmodal' ).gdprmodal( 'show' );
					} else if (button_action == 'ccpa_close') {
						GDPR.displayHeader();
					} else if (button_action == 'cancel') {
						GDPR.ccpa_cancel_close();
						gdpr_optout_cookie = GDPR_Cookie.read( 'wpl_optout_cookie' );

						event = new CustomEvent(
							'GdprCookieConsentOnCancelOptout',
							{detail: {
								'wpl_optout_cookie' : gdpr_optout_cookie,
								}}
						);
						window.dispatchEvent( event );
						GDPR.logConsent( button_action );
					} else if ( button_action == 'confirm' ) {
						GDPR.confirm_close();
						gdpr_optout_cookie = GDPR_Cookie.read( 'wpl_optout_cookie' );

						event = new CustomEvent(
							'GdprCookieConsentOnOptout',
							{detail: {
								'wpl_optout_cookie' : gdpr_optout_cookie,
								}}
						);
						window.dispatchEvent( event );
						GDPR.logConsent( button_action );
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

			jQuery( '.gdpr_messagebar_detail input' ).each(
				function(){
					var key                      = jQuery( this ).val();
					var gdpr_user_preference_arr = {};
					var gdpr_user_preference_val = '';
					if (GDPR_Cookie.read( 'wpl_user_preference' )) {
						gdpr_user_preference_arr = (JSON.parse( GDPR_Cookie.read( 'wpl_user_preference' ) ));
					}
					if (key == 'necessary' || jQuery( this ).is( ':checked' )) {
						gdpr_user_preference_arr[key] = 'yes';
						GDPR.allowed_categories.push( key );
					} else {
						gdpr_user_preference_arr[key] = 'no';
						var length                    = GDPR.allowed_categories.length;
						for ( var i = 0; i < length; i++) {
							if ( GDPR.allowed_categories[i] == key) {
								GDPR.allowed_categories.splice( i, 1 );
							}
						}
					}
					gdpr_user_preference_val = JSON.stringify( gdpr_user_preference_arr );
					GDPR_Cookie.set( 'wpl_user_preference',gdpr_user_preference_val,GDPR_ACCEPT_COOKIE_EXPIRE );
				}
			);
			jQuery( document ).on(
				'click',
				'#gdpr-cookie-consent-show-again',
				function(e){
					e.preventDefault();
					jQuery( GDPR.settings.notify_div_id ).find( 'p.gdpr' ).show();
					jQuery( GDPR.settings.notify_div_id ).find( '.gdpr.group-description-buttons' ).show();
					GDPR.displayHeader();
					$( this ).hide();
				}
			);
			jQuery( document ).on(
				'click',
				'.gdpr_messagebar_detail input',
				function(){
					var key                      = jQuery( this ).val();
					var gdpr_user_preference_arr = {};
					var gdpr_user_preference_val = '';
					if (GDPR_Cookie.read( 'wpl_user_preference' )) {
						gdpr_user_preference_arr = (JSON.parse( GDPR_Cookie.read( 'wpl_user_preference' ) ));
					}
					if (jQuery( this ).is( ':checked' )) {
						gdpr_user_preference_arr[key] = 'yes';
						GDPR.allowed_categories.push( key );
					} else {
						gdpr_user_preference_arr[key] = 'no';
						var length                    = GDPR.allowed_categories.length;
						for ( var i = 0; i < length; i++) {
							if ( GDPR.allowed_categories[i] == key) {
								GDPR.allowed_categories.splice( i, 1 );
							}
						}
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
					elm.css( 'background-color', GDPR.settings.background_active_color );
					jQuery( '#gdpr_messagebar_detail_body_content_tabs_about' ).css( 'border-bottom-color', GDPR.settings.border_color );
					jQuery( '#gdpr_messagebar_detail_body_content_tabs_about' ).css( 'background-color', GDPR.settings.background_color );
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
					elm.css( 'background-color', GDPR.settings.background_active_color );
					jQuery( '#gdpr_messagebar_detail_body_content_tabs_overview' ).css( 'border-bottom-color', GDPR.settings.border_color );
					jQuery( '#gdpr_messagebar_detail_body_content_tabs_overview' ).css( 'background-color', GDPR.settings.background_color );
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
					prnt.find( 'a' ).css( 'background-color', GDPR.settings.background_color );
					elm.addClass( 'gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected' );
					elm.css( 'border-right-color',GDPR.settings.border_active_color );
					elm.css( 'background-color',GDPR.settings.background_active_color );
					var trgt = jQuery( this ).attr( 'data-target' );
					var cntr = prnt.siblings( '#gdpr_messagebar_detail_body_content_overview_cookie_container_type_details' );
					cntr.find( '.gdpr_messagebar_detail_body_content_cookie_type_details' ).hide();
					cntr.find( '#' + trgt + '' ).show();
				}
			);

		},

		configButtons:function()
		{
			var template = this.settings.template;

			this.settings_button.css( 'color',this.settings.button_settings_link_color );
			if (this.settings.button_settings_as_button) {
				var settings_background = this.convertToHex( this.settings.button_settings_button_color, this.settings.button_settings_button_opacity );
				var settings_border     = this.settings.button_settings_button_border_width + 'px ' + this.settings.button_settings_button_border_style + ' ' + this.settings.button_settings_button_border_color;
				this.settings_button.css( 'border', settings_border );
				this.settings_button.css( 'border-radius',this.settings.button_settings_button_border_radius + 'px' );
				this.settings_button.css( 'background-color',settings_background );
				this.settings_button.hover(
					function () {
						jQuery( this ).css( 'background-color',GDPR.settings.button_settings_button_hover );
					},
					function (){
						jQuery( this ).css( 'background-color',settings_background );
					}
				);
			}

			if (template.includes( 'square' )) {
				this.settings_button.css( 'width', '45%' );
				this.settings_button.css( 'float', 'right' );
				this.settings_button.css( 'margin-right', '-22px' );
			} else if (template.includes( 'row' ) || template.includes( 'column' )) {

			} else if ( template.includes( 'center' ) ) {
				this.settings_button.css( 'margin-right', '0' );
			} else if (template.includes( 'dark' ) ) {
				this.settings_button.css( 'float', 'right' );
			} else {
				this.settings_button.css( 'float', 'right' );
				this.settings_button.css( 'margin-right', '0' );
			}

			if( this.settings.button_accept_all_is_on ) {
				if( template.includes( 'center' ) ) {
					if(template.includes('popup') || template.includes('widget')){
						this.settings_button.css( 'margin-top', '10px' );
					}
				}
				if(template.includes('popup') || template.includes('widget')){
					$( '.gdpr-popup > .gdpr_messagebar_content > #default_buttons' ).css({
						"display": "flex",
						"justify-content" : "space-around"
					} );
				}
			}

			this.main_button.css( 'color',this.settings.button_accept_link_color );
			if (this.settings.button_accept_as_button) {
				var main_background = this.convertToHex( this.settings.button_accept_button_color, this.settings.button_accept_button_opacity );
				var main_border     = this.settings.button_accept_button_border_width + 'px ' + this.settings.button_accept_button_border_style + ' ' + this.settings.button_accept_button_border_color;
				this.main_button.css( 'border', main_border );
				this.main_button.css( 'border-radius',this.settings.button_accept_button_border_radius + 'px' );
				this.main_button.css( 'background-color',main_background );
				this.main_button.hover(
					function () {
						jQuery( this ).css( 'background-color',GDPR.settings.button_accept_button_hover );
					},
					function (){
						jQuery( this ).css( 'background-color',main_background );
					}
				);
			}

			if (template.includes( 'square' )) {
				this.main_button.css( 'width', '100%' );
				this.main_button.css( 'margin','1rem auto 0 auto' );
			}

			this.accept_all_button.css( 'color',this.settings.button_accept_all_link_color );
			if ( this.settings.button_accept_all_as_button ) {
				var main_background = this.convertToHex( this.settings.button_accept_all_button_color, this.settings.button_accept_all_btn_opacity );
				var main_border     = this.settings.button_accept_all_btn_border_width + 'px ' + this.settings.button_accept_all_btn_border_style + ' ' + this.settings.button_accept_all_btn_border_color;
				this.accept_all_button.css( 'border', main_border );
				this.accept_all_button.css( 'border-radius',this.settings.button_accept_all_btn_border_radius + 'px' );
				this.accept_all_button.css( 'background-color',main_background );
				this.accept_all_button.hover(
					function () {
						jQuery( this ).css( 'background-color',GDPR.settings.button_accept_all_button_hover );
					},
					function (){
						jQuery( this ).css( 'background-color',main_background );
					}
				);
			}

			if (template.includes( 'square' )) {
				this.accept_all_button.css( 'width', '100%' );
				this.accept_all_button.css( 'margin','1rem auto 0 auto' );
			}

			this.confirm_button.css( 'color',this.settings.button_confirm_link_color );
			if (this.settings.button_confirm_as_button) {
				var confirm_background = this.convertToHex( this.settings.button_confirm_button_color, this.settings.button_confirm_button_opacity );
				var confirm_border     = this.settings.button_confirm_button_border_width + 'px ' + this.settings.button_confirm_button_border_style + ' ' + this.settings.button_confirm_button_border_color;
				this.confirm_button.css( 'border', confirm_border );
				this.confirm_button.css( 'border-radius',this.settings.button_confirm_button_border_radius + 'px' );
				this.confirm_button.css( 'background-color',confirm_background );
				this.confirm_button.css( 'width','100%' );
				this.confirm_button.hover(
					function () {
						jQuery( this ).css( 'background-color',GDPR.settings.button_confirm_button_hover );
					},
					function (){
						jQuery( this ).css( 'background-color',confirm_background );
					}
				);
			}

			/* [wpl_cookie_link] */
			this.main_link.css( 'color',this.settings.button_readmore_link_color );
			if (this.settings.button_readmore_as_button) {
				var readmore_background = this.convertToHex( this.settings.button_readmore_button_color, this.settings.button_readmore_button_opacity );
				var readmore_border     = this.settings.button_readmore_button_border_width + 'px ' + this.settings.button_readmore_button_border_style + ' ' + this.settings.button_readmore_button_border_color;
				this.main_link.css( 'border', readmore_border );
				this.main_link.css( 'border-radius',this.settings.button_readmore_button_border_radius + 'px' );
				this.main_link.css( 'background-color',readmore_background );
				this.main_link.hover(
					function () {
						jQuery( this ).css( 'background-color',GDPR.settings.button_readmore_button_hover );
					},
					function (){
						jQuery( this ).css( 'background-color',readmore_background );
					}
				);
			}

			this.donotsell_link.css( 'color',this.settings.button_donotsell_link_color );

			this.reject_button.css( 'color',this.settings.button_decline_link_color );
			if (this.settings.button_decline_as_button) {
				var decline_background = this.convertToHex( this.settings.button_decline_button_color, this.settings.button_decline_button_opacity );
				var reject_border      = this.settings.button_decline_button_border_width + 'px ' + this.settings.button_decline_button_border_style + ' ' + this.settings.button_decline_button_border_color;
				this.reject_button.css( 'border', reject_border );
				this.reject_button.css( 'border-radius',this.settings.button_decline_button_border_radius + 'px' );
				this.reject_button.css( 'background-color',decline_background );
				this.reject_button.hover(
					function () {
						jQuery( this ).css( 'background-color',GDPR.settings.button_decline_button_hover );
					},
					function () {
						jQuery( this ).css( 'background-color',decline_background );
					}
				);
			}

			if (template.includes( 'square' )) {
				this.reject_button.css( 'width', '45%' );
			} else if (template.includes( 'dark' ) ) {
				this.reject_button.css( 'float', 'right' );
			}

			this.cancel_button.css( 'color',this.settings.button_cancel_link_color );
			if (this.settings.button_cancel_as_button) {
				var cancel_background = this.convertToHex( this.settings.button_cancel_button_color, this.settings.button_cancel_button_opacity );
				var cancel_border     = this.settings.button_cancel_button_border_width + 'px ' + this.settings.button_cancel_button_border_style + ' ' + this.settings.button_cancel_button_border_color;
				this.cancel_button.css( 'border', cancel_border );
				this.cancel_button.css( 'border-radius',this.settings.button_cancel_button_border_radius + 'px' );
				this.cancel_button.css( 'display', 'inline-block' );
				this.cancel_button.css( 'background-color',cancel_background );
				this.cancel_button.css( 'width','100%' );
				this.cancel_button.hover(
					function () {
						jQuery( this ).css( 'background-color',GDPR.settings.button_cancel_button_hover );
					},
					function () {
						jQuery( this ).css( 'background-color',cancel_background );
					}
				);
			}

			this.save_button.css( 'color',this.settings.button_accept_link_color );
			this.save_button.css( 'background-color',main_background );
			this.save_button.css( 'border', main_border );
			this.save_button.css( 'border-radius',this.settings.button_accept_button_border_radius + 'px' );
			this.save_button.hover(
				function () {
					jQuery( this ).css( 'background-color',GDPR.settings.button_accept_button_hover );
				},
				function (){
					jQuery( this ).css( 'background-color',main_background );
				}
			);
			this.details_elm.find( 'table.gdpr_messagebar_detail_body_content_cookie_type_table tr' ).css( 'border-color', GDPR.settings.border_color );
			this.details_elm.find( '.gdpr_messagebar_detail_body_content_cookie_type_intro' ).css( 'border-color',GDPR.settings.border_color );
			this.details_elm.find( 'a' ).each(
				function(){
					jQuery( this ).css( 'border-color',GDPR.settings.border_color );
					jQuery( this ).css( 'background-color',GDPR.settings.background_color );
				}
			)
			this.details_elm.find( 'a.gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected' ).css( 'border-right-color', GDPR.settings.border_active_color );
			this.details_elm.find( 'a.gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected' ).css( 'background-color', GDPR.settings.background_active_color );
			this.details_elm.find( '#gdpr_messagebar_detail_body_content' ).css( 'border-color', GDPR.settings.border_color );
			this.details_elm.find( '#gdpr_messagebar_detail_body_content_tabs' ).css( 'border-color', GDPR.settings.border_color );
			this.details_elm.find( '#gdpr_messagebar_detail_body_content_tabs .gdpr_messagebar_detail_body_content_tab_item_selected' ).css( 'border-bottom-color', GDPR.settings.border_active_color );
			this.details_elm.find( '#gdpr_messagebar_detail_body_content_tabs .gdpr_messagebar_detail_body_content_tab_item_selected' ).css( 'background-color', GDPR.settings.background_active_color );

			this.credit_link.css( 'color',this.settings.button_readmore_link_color );
			$( '.gdpr-column .gdpr-columns.active-group' ).css( 'background-color', GDPR.settings.button_accept_button_color );
			$( '.gdpr_messagebar_detail .category-group .toggle-group .always-active' ).css( 'color', GDPR.settings.button_accept_button_color );
		},
		convertToHex:function( hex, opacity ) {
			hex   = hex.replace( '#','' );
			var r = parseInt( hex.substring( 0,2 ), 16 );
			var g = parseInt( hex.substring( 2,4 ), 16 );
			var b = parseInt( hex.substring( 4,6 ), 16 );

			var result = 'rgba(' + r + ',' + g + ',' + b + ',' + opacity + ')';
			return result;
		},

		configBar:function() {
			var background = this.convertToHex( this.settings.background, this.settings.opacity );
			var border     = this.settings.background_border_width + 'px ' + this.settings.background_border_style + ' ' + this.settings.background_border_color;

			this.bar_config  = {
				'background-color':background,
				'color':this.settings.text,
				'font-family':this.settings.font_family,
				'box-shadow':this.settings.background + ' 0 0 8px',
				'border': border,
				'border-radius' : this.settings.background_border_radius + 'px'
			};
			this.show_config = {
				'width':'auto',
				'background-color':background,
				'box-shadow':this.settings.background + ' 0 0 8px',
				'color':this.settings.text,
				'font-family':this.settings.font_family,
				'position':'fixed',
				'bottom':'0',
				'border': border,
				'border-radius' : this.settings.background_border_radius + 'px'
			};

			var template = this.settings.template;
			if (template.includes( 'row' ) || template.includes( 'center' )) {
				this.bar_config['text-align'] = 'center';
			} else {
				this.bar_config['text-align'] = 'justify';
			}

			if (this.settings.show_again_position == 'right') {
				this.show_config['right'] = this.settings.show_again_margin + '%';
			} else {
				this.show_config['left'] = this.settings.show_again_margin + '%';
			}
			this.bar_config['position'] = 'fixed';
			if (this.settings.cookie_bar_as == 'banner') {
				this.bar_elm.find( '.gdpr_messagebar_content' ).css( 'max-width','800px' );
				if (this.settings.notify_position_vertical == 'bottom') {
					this.bar_config['bottom'] = '0';
				} else {
					this.bar_config['top'] = '0';
				}
			}
			if (this.settings.cookie_bar_as == 'widget') {
				this.bar_config['bottom'] = '20px';
				this.bar_config['width']  = '35%';
				if (this.settings.notify_position_horizontal == 'left') {
					this.bar_config['left'] = '20px';
				} else {
					this.bar_config['right'] = '20px';
				}
			}
			if (this.settings.cookie_bar_as == 'popup') {
				this.bar_config['border']           = 'unset';
				this.bar_config['border-radius']    = 'unset';
				this.bar_config['position']         = 'unset';
				this.bar_config['box-shadow']       = 'unset';
				this.bar_config['background-color'] = 'unset';
				jQuery( '#gdpr-popup .gdprmodal-content' ).css( 'background-color',background );
				jQuery( '#gdpr-popup .gdprmodal-content' ).css( 'border',border );
				jQuery( '#gdpr-popup .gdprmodal-content' ).css( 'border-radius',this.settings.background_border_radius + 'px' );
				jQuery( '#gdpr-popup .gdprmodal-content' ).css( 'box-shadow',this.settings.background + ' 0 0 8px' );
			}
			this.bar_elm.css( this.bar_config ).hide();
			this.show_again_elm.css( this.show_config ).hide();
		},

		toggleBar:function( force_display_bar, force_display_show_again ) {
			if ( this.settings.cookie_usage_for == 'gdpr' ) {
				if ( ! GDPR_Cookie.exists( GDPR_ACCEPT_COOKIE_NAME )) {
					this.displayHeader();
					if ( this.settings.auto_hide ) {
						setTimeout(
							function(){
								GDPR.accept_close();
							},
							this.settings.auto_hide_delay
						);
					}
				} else {
					this.hideHeader();
				}
			} else if ( this.settings.cookie_usage_for == 'eprivacy' ) {
				if ( ! GDPR_Cookie.exists( GDPR_ACCEPT_COOKIE_NAME )) {
					this.displayHeader();
					if ( this.settings.auto_hide ) {
						setTimeout(
							function(){
								GDPR.accept_close();
							},
							this.settings.auto_hide_delay
						);
					}
				} else {
					this.hideHeader();
				}
			} else if ( this.settings.cookie_usage_for == 'ccpa' ) {
				if ( ! GDPR_Cookie.exists( GDPR_CCPA_COOKIE_NAME )) {
					this.displayHeader();
				} else {
					this.hideHeader();
				}
			} else if (this.settings.cookie_usage_for == 'both') {
				if (GDPR_Cookie.exists( GDPR_ACCEPT_COOKIE_NAME ) && GDPR_Cookie.exists( GDPR_CCPA_COOKIE_NAME )) {
					this.hideHeader();
				} else if (GDPR_Cookie.exists( GDPR_ACCEPT_COOKIE_NAME ) && ! GDPR_Cookie.exists( GDPR_CCPA_COOKIE_NAME )) {

						this.displayHeader( true, false, force_display_bar, true );


				} else if ( ! GDPR_Cookie.exists( GDPR_ACCEPT_COOKIE_NAME ) && GDPR_Cookie.exists( GDPR_CCPA_COOKIE_NAME )) {
					this.displayHeader( false, true, force_display_bar, force_display_show_again );
					if ( this.settings.auto_hide ) {
						setTimeout(
							function(){
								GDPR.accept_close();
							},
							this.settings.auto_hide_delay
						);
					}
				} else {
					this.displayHeader( false, false, force_display_bar, force_display_show_again );
					if ( this.settings.auto_hide ) {
						setTimeout(
							function(){
								GDPR.accept_close();
							},
							this.settings.auto_hide_delay
						);
					}
				}
				if ( ! GDPR_Cookie.exists( GDPR_ACCEPT_COOKIE_NAME ) || ! GDPR_Cookie.exists( GDPR_CCPA_COOKIE_NAME )) {

				} else {
					this.hideHeader();
				}
			}
		},

		ccpa_cancel_close:function() {
			GDPR_Cookie.set( GDPR_CCPA_COOKIE_NAME,'no',GDPR_CCPA_COOKIE_EXPIRE );
			if ( this.settings.is_ccpa_iab_on ) {
				GDPR_Cookie.set( US_PRIVACY_COOKIE_NAME, '1YNY', GDPR_CCPA_COOKIE_EXPIRE );
			}
			if (this.settings.notify_animate_hide) {
				this.bar_elm.slideUp( this.settings.animate_speed_hide );
			}
			if (this.settings.cookie_bar_as == 'popup') {
				$( "#gdpr-popup" ).gdprmodal( 'hide' );
			}
			if ( this.settings.accept_reload == true ) {
				window.location.reload( true );
			}
			else{
				if (this.settings.cookie_usage_for == 'both') {
					if (this.settings.pro_active && this.settings.maxmind_integrated == '2') {
						this.check_ccpa_eu();
					}
					else{
						this.toggleBar();
					}	
				}	
			}
			return false;
		},

		confirm_close:function() {
			GDPR_Cookie.set( GDPR_CCPA_COOKIE_NAME,'yes',GDPR_CCPA_COOKIE_EXPIRE );
			if ( this.settings.is_ccpa_iab_on ) {
				GDPR_Cookie.set( US_PRIVACY_COOKIE_NAME, '1YYY', GDPR_CCPA_COOKIE_EXPIRE );
			}
			if (this.settings.notify_animate_hide) {
				this.bar_elm.slideUp( this.settings.animate_speed_hide );
			}
			if (this.settings.cookie_bar_as == 'popup') {
				$( "#gdpr-popup" ).gdprmodal( 'hide' );
			}
			if ( this.settings.accept_reload == true ) {
				window.location.reload( true );
			}
			else{
				if (this.settings.cookie_usage_for == 'both') {
					if (this.settings.pro_active && this.settings.maxmind_integrated == '2') {
						this.check_ccpa_eu();
					}
					else{
						this.toggleBar();
					}	
				}	
			}
			return false;
		},

		accept_close:function() {
			GDPR_Cookie.set( GDPR_ACCEPT_COOKIE_NAME,'yes',GDPR_ACCEPT_COOKIE_EXPIRE );
			var cookie_pref          = '{"necessary":"yes","marketing":"yes","analytics":"yes","preferences":"yes","unclassified":"yes"}';
			var gdpr_user_preference     = JSON.parse(cookie_pref);
			var gdpr_user_preference_val = JSON.stringify( gdpr_user_preference );

			if (this.settings.notify_animate_hide) {
				this.bar_elm.slideUp( this.settings.animate_speed_hide , GDPR_Blocker.runScripts );
			} else {
				this.bar_elm.hide( GDPR_Blocker.runScripts );
			}
			if (this.settings.cookie_bar_as == 'popup') {
				$( "#gdpr-popup" ).gdprmodal( 'hide' );
			}
			this.show_again_elm.slideDown( this.settings.animate_speed_hide );
			if ( this.settings.accept_reload == true ) {
				window.location.reload( true );
			}
			else{
				if (this.settings.cookie_usage_for == 'both') {
					if (this.settings.pro_active && this.settings.maxmind_integrated == '2') {
						this.check_ccpa_eu(true,true);
					}
					else{
						this.toggleBar(true,true);
					}
				}		
			}
			return false;
		},

		reject_close:function() {
			GDPR.disableAllCookies();
			GDPR_Cookie.set( GDPR_ACCEPT_COOKIE_NAME,'no',GDPR_ACCEPT_COOKIE_EXPIRE );
			if (this.settings.notify_animate_hide) {
				this.bar_elm.slideUp( this.settings.animate_speed_hide, GDPR_Blocker.runScripts );
			} else {
				this.bar_elm.hide( GDPR_Blocker.runScripts );
			}
			if (this.settings.cookie_bar_as == 'popup') {
				$( "#gdpr-popup" ).gdprmodal( 'hide' );
			}
			this.show_again_elm.slideDown( this.settings.animate_speed_hide );
			if ( this.settings.decline_reload == true ) {
				window.location.reload( true );
			}
			else{
				if (this.settings.cookie_usage_for == 'both') {
					if (this.settings.pro_active && this.settings.maxmind_integrated == '2') {
						this.check_ccpa_eu(true,true);
					}
					else{
						this.toggleBar(true,true);
					}
				}			
			}
			return false;
		},

		logConsent:function(btn_action) {
			if ( this.settings.logging_on && this.settings.pro_active ) {
				jQuery.ajax(
					{
						url: log_obj.ajax_url,
						type: 'POST',
						data:{
							action: 'gdpr_log_consent_action',
							security: log_obj.consent_logging_nonce,
							gdpr_user_action:btn_action,
							cookie_list:GDPR_Cookie.getallcookies()
						},
						success:function (response) {
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
							$( '.gdpr_messagebar_detail input[value="' + key + '"]' ).prop( 'checked', false );
							var length = GDPR.allowed_categories.length;
							for ( var i = 0; i < length; i++) {
								if ( GDPR.allowed_categories[i] == key) {
									GDPR.allowed_categories.splice( i, 1 );
								}
							}
						}
					}
				);
				gdpr_user_preference_val = JSON.stringify( gdpr_user_preference_arr );
				GDPR_Cookie.set( 'wpl_user_preference',gdpr_user_preference_val,GDPR_ACCEPT_COOKIE_EXPIRE );
			}
		},
		acceptAllCookies:function() {
			var gdpr_user_preference_arr = {};
			var gdpr_user_preference_val = '';
			if (GDPR_Cookie.read( 'wpl_user_preference' )) {
				gdpr_user_preference_arr = (JSON.parse( GDPR_Cookie.read( 'wpl_user_preference' ) ));
				jQuery.each(
					gdpr_user_preference_arr,
					function(key, value) {
						if (key != 'necessary') {
							gdpr_user_preference_arr[key] = 'yes';
							$( '.gdpr_messagebar_detail input[value="' + key + '"]' ).prop( 'checked', true );
							var length = GDPR.allowed_categories.length;
							for ( var i = 0; i < length; i++) {
								if ( GDPR.allowed_categories[i] == key) {
									GDPR.allowed_categories.splice( i, 1 );
								}
							}
						}
					}
				);
				gdpr_user_preference_val = JSON.stringify( gdpr_user_preference_arr );
				GDPR_Cookie.set( 'wpl_user_preference',gdpr_user_preference_val,GDPR_ACCEPT_COOKIE_EXPIRE );
			}
		},
		show_details:function() {
			this.details_elm.show();
			this.bar_elm.css( 'opacity', 1 );
			this.details_elm.css( 'border-top-color', GDPR.settings.border_color );
			this.settings_button.attr( 'data-gdpr_action', 'hide_settings' );
		},
		hide_details:function() {
			this.details_elm.hide();
			this.bar_elm.css( 'opacity', GDPR.settings.opacity );
			this.settings_button.attr( 'data-gdpr_action', 'show_settings' );
		},
		displayHeader:function(gdpr_flag, ccpa_flag, force_display_bar, force_display_show_again) {
			if( !gdpr_flag || !ccpa_flag){
				var animate_on_load = GDPR.settings.notify_animate_show;
				if(force_display_bar || animate_on_load){
					this.bar_elm.slideDown(this.settings.animate_speed_hide);
				}
				else{
					this.bar_elm.show();
				}
			}

			if (gdpr_flag) {
				jQuery( GDPR.settings.notify_div_id ).find( 'p.gdpr' ).hide();
				jQuery( GDPR.settings.notify_div_id ).find( '.gdpr.group-description-buttons' ).hide();
				jQuery( GDPR.settings.notify_div_id ).find( 'p.ccpa' ).show();
			}
			if (ccpa_flag || GDPR_Cookie.exists( GDPR_CCPA_COOKIE_NAME )) {
				jQuery( GDPR.settings.notify_div_id ).find( 'p.ccpa' ).hide();
			}
			if (this.settings.cookie_bar_as == 'popup') {
				if( !gdpr_flag || !ccpa_flag){
				$( "#gdpr-popup" ).gdprmodal( 'show' );
				}
			}
			if (this.settings.cookie_usage_for == 'gdpr' || this.settings.cookie_usage_for == 'eprivacy' || this.settings.cookie_usage_for == 'both') {
				if(force_display_show_again){
				this.show_again_elm.slideDown( this.settings.animate_speed_hide );
				}
				else{
					this.show_again_elm.slideUp( this.settings.animate_speed_hide );
				}
			}
		},
		hideHeader:function(geo_flag) {
			this.bar_elm.slideUp( this.settings.animate_speed_hide );
			if ( ! geo_flag) {
				if (this.settings.cookie_bar_as == 'popup') {
					$( "#gdpr-popup" ).gdprmodal( 'hide' );
				}
				if ( this.settings.cookie_usage_for == 'gdpr' || this.settings.cookie_usage_for == 'eprivacy' || this.settings.cookie_usage_for == 'both' ) {
					this.show_again_elm.slideDown( this.settings.animate_speed_hide );
				}
			}
		},
		acceptOnScroll:function(){
			var scrollTop            = $( window ).scrollTop();
			var docHeight            = $( document ).height();
			var winHeight            = $( window ).height();
			var scrollPercent        = ( scrollTop ) / ( docHeight - winHeight );
			var scrollPercentRounded = Math.round( scrollPercent * 100 );

			if ( scrollPercentRounded > GDPR.settings.auto_scroll_offset && ! GDPR_Cookie.exists( GDPR_ACCEPT_COOKIE_NAME ) ) {
				if ( GDPR.settings.button_accept_all_is_on ){
					GDPR.acceptAllCookies();
				}
				GDPR.accept_close();
				window.removeEventListener( "scroll",GDPR.acceptOnScroll,false );
				if ( GDPR.settings.auto_scroll_reload == true ) {
					window.location.reload();
				}
			}
		},
	}

	var GDPR_Blocker = {
		blockingStatus: true,
		scriptsLoaded: false,
		set: function(args) {
			if (typeof JSON.parse !== "function") {
				console.log( "GDPRCookieConsent requires JSON.parse but your browser doesn't support it" );
				return;
			}
			this.cookies = JSON.parse( JSON.stringify( args.cookies ) );
		},
		removeCookieByCategory : function() {
			if (GDPR_Blocker.blockingStatus == true) {
				for (var key in GDPR_Blocker.cookies) {
					var cookie           = GDPR_Blocker.cookies[key];
					var current_category = cookie['gdpr_cookie_category_slug'];
					if (GDPR.allowed_categories.indexOf( current_category ) === -1) {
						var cookies = cookie['data'];
						if (cookies && cookies.length != 0) {
							for (var c_key in cookies) {
								var c_cookie = cookies[c_key];
								GDPR_Cookie.erase( c_cookie['name'] );
							}
						}
					}
				}
			}
		},
		runScripts:function() {
			var srcReplaceableElms = ['iframe','IFRAME','EMBED','embed','OBJECT','object','IMG','img'];
			var genericFuncs       = {
				renderByElement: function( callback ) {
					scriptFuncs.renderScripts();
					htmlElmFuncs.renderSrcElement();
					callback();
					GDPR_Blocker.scriptsLoaded = true;
				},
				reviewConsent : function() {
					jQuery( document ).on(
						'click',
						'.wpl_manage_current_consent',
						function(){
								GDPR.displayHeader();
						}
					);
				}
			};
			var scriptFuncs        = {
				scriptsDone:function() {
					var DOMContentLoadedEvent = document.createEvent( 'Event' )
					DOMContentLoadedEvent.initEvent( 'DOMContentLoaded', true, true )
					window.document.dispatchEvent( DOMContentLoadedEvent )
				},
				seq :function(arr, callback, index) {
					if (typeof index === 'undefined') {
						index = 0
					}

					arr[index](
						function () {
							index++
							if (index === arr.length) {
								callback()
							} else {
								scriptFuncs.seq( arr, callback, index )
							}
						}
					)
				},

				insertScript:function($script,callback) {
					var allowedAttributes = [
						'data-wpl-class',
						'data-wpl-label',
						'data-wpl-placeholder',
						'data-wpl-script-type',
						'data-wpl-src'
					];
					var scriptType        = $script.getAttribute( 'data-wpl-script-type' );
					var elementPosition   = $script.getAttribute( 'data-wpl-element-position' );
					var isBlock           = $script.getAttribute( 'data-wpl-block' );
					var s                 = document.createElement( 'script' );
					s.type                = 'text/plain';
					if ($script.async) {
						s.async = $script.async;
					}
					if ($script.defer) {
						s.defer = $script.defer;
					}
					if ($script.src) {
						s.onload  = callback
						s.onerror = callback
						s.src     = $script.src
					} else {
						s.textContent = $script.innerText
					}
					var attrs  = jQuery( $script ).prop( "attributes" );
					var length = attrs.length;
					for (var ii = 0; ii < length; ++ii) {
						if (attrs[ii].nodeName !== 'id') {
							if (allowedAttributes.indexOf( attrs[ii].nodeName ) !== -1) {
								s.setAttribute( attrs[ii].nodeName,attrs[ii].value );
							}
						}
					}
					if (GDPR_Blocker.blockingStatus === true) {
						if (( GDPR_Cookie.read( GDPR_ACCEPT_COOKIE_NAME ) == 'yes' && GDPR.allowed_categories.indexOf( scriptType ) !== -1 ) || ( GDPR_Cookie.read( GDPR_ACCEPT_COOKIE_NAME ) == null && isBlock === 'false') ) {
							s.setAttribute( 'data-wpl-consent','accepted' );
							s.type = 'text/javascript';
						}
					} else {
						s.type = 'text/javascript';
					}
					if ($script.type != s.type) {
						if (elementPosition === 'head') {
							document.head.appendChild( s );
							if ( ! $script.src) {
								callback()
							}
							$script.parentNode.removeChild( $script );
						} else {
							document.body.appendChild( s );
							if ( ! $script.src) {
								callback()
							}
							$script.parentNode.removeChild( $script );
						}
					}
				},
				renderScripts:function() {
					var $scripts = document.querySelectorAll( 'script[data-wpl-class="wpl-blocker-script"]' );
					if ($scripts.length > 0) {
						var runList = []
						var typeAttr
						Array.prototype.forEach.call(
							$scripts,
							function ($script) {
									typeAttr    = $script.getAttribute( 'type' )
									var elmType = $script.tagName;
									runList.push(
										function (callback) {
											scriptFuncs.insertScript( $script, callback )
										}
									)
							}
						)
						scriptFuncs.seq( runList, scriptFuncs.scriptsDone );
					}
				}
			};
			var htmlElmFuncs = {
				renderSrcElement: function() {
					var blockingElms = document.querySelectorAll( '[data-wpl-class="wpl-blocker-script"]' );
					var length       = blockingElms.length;
					for (var i = 0; i < length; i++) {
						var currentElm = blockingElms[i];
						var elmType    = currentElm.tagName;
						if (srcReplaceableElms.indexOf( elmType ) !== -1) {
							var elmCategory = currentElm.getAttribute( 'data-wpl-script-type' );
							var isBlock     = currentElm.getAttribute( 'data-wpl-block' );
							if (GDPR_Blocker.blockingStatus === true) {
								if ((GDPR_Cookie.read( GDPR_ACCEPT_COOKIE_NAME ) == 'yes' && GDPR.allowed_categories.indexOf( elmCategory ) !== -1 ) || ( GDPR_Cookie.read( GDPR_ACCEPT_COOKIE_NAME ) != null && isBlock === 'false') ) {
									this.replaceSrc( currentElm );
								} else {
									this.addPlaceholder( currentElm );
								}
							} else {
								this.replaceSrc( currentElm );
							}
						}
					}
				},
				addPlaceholder:function(htmlElm) {
					if (jQuery( htmlElm ).prev( '.wpl-iframe-placeholder' ).length === 0) {

						var htmlElemType   = htmlElm.getAttribute( 'data-wpl-placeholder' );
						var htmlElemWidth  = htmlElm.getAttribute( 'width' );
						var htmlElemHeight = htmlElm.getAttribute( 'height' );
						if (htmlElemWidth == null) {
							htmlElemWidth = htmlElm.offsetWidth;
						}
						if (htmlElemHeight == null) {
							htmlElemHeight = htmlElm.offsetHeight;
						}
						var pixelPattern   = /px/;
						htmlElemWidth      = ((pixelPattern.test( htmlElemWidth )) ? htmlElemWidth : htmlElemWidth + 'px');
						htmlElemHeight     = ((pixelPattern.test( htmlElemHeight )) ? htmlElemHeight : htmlElemHeight + 'px');
						var addPlaceholder = '<div style="width:' + htmlElemWidth + '; height:' + htmlElemHeight + ';" class="wpl-iframe-placeholder"><div class="wpl-inner-text">' + htmlElemType + '</div></div>';
						if (htmlElm.tagName !== 'IMG') {
							jQuery( addPlaceholder ).insertBefore( htmlElm );
						}
						htmlElm.removeAttribute( 'src' );
						htmlElm.style.display = 'none';
					}
				},
				replaceSrc: function(htmlElm) {
					if ( ! htmlElm.hasAttribute( 'src' )) {
						var htmlElemSrc = htmlElm.getAttribute( 'data-wpl-src' );
						htmlElm.setAttribute( 'src',htmlElemSrc );
						if (jQuery( htmlElm ).prev( '.wpl-iframe-placeholder' ).length > 0) {
							jQuery( htmlElm ).prev( '.wpl-iframe-placeholder' ).remove();
						}
						htmlElm.style.display = 'block';
					}
				}
			};
			genericFuncs.reviewConsent();
			genericFuncs.renderByElement( GDPR_Blocker.removeCookieByCategory );
		}
	}
	$( document ).ready(
		function() {
			var settings = JSON.parse( gdpr_cookiebar_settings );

			if( settings['notify_animate_show'] ) {
				$( '#gdpr-cookie-consent-bar' ).css( 'display', 'none' );
				$( '#gdpr-cookie-consent-bar' ).slideDown( 500 );
			}
			if ('2' == settings['maxmind_integrated']) {
				var data = {
					action: 'show_cookie_consent_bar',
				};

					$.ajax(
						{
							type: 'post',
							url: log_obj.ajax_url,
							data: data,
							dataType: 'json',
							success: function (response) {
								if (response.error) {
									// handle error here.
								} else {
									if (response.eu_status == 'on' || response.ccpa_status == 'on') {
										if (typeof gdpr_cookiebar_settings != 'undefined') {
											GDPR.set(
												{
													settings:gdpr_cookiebar_settings
												}
											);
										}

										if (typeof gdpr_cookies_list != 'undefined') {
											GDPR_Blocker.set(
												{
													cookies:gdpr_cookies_list
												}
											);
											GDPR_Blocker.runScripts();
										}
									}
								}
							}
						}
					);
			} else {
				if (typeof gdpr_cookiebar_settings != 'undefined') {
					GDPR.set(
						{
							settings:gdpr_cookiebar_settings
						}
					);
				}

				if (typeof gdpr_cookies_list != 'undefined') {
					GDPR_Blocker.set(
						{
							cookies:gdpr_cookies_list
						}
					);
					GDPR_Blocker.runScripts();
				}
			}

		}
	);

	$( document ).ready(
		function() {
			$( ".gdpr-default-category-toggle.gdpr-column" ).click(
				function() {
					$( ".gdpr-default-category-toggle.gdpr-column", this );
					if ( ! $( this ).children( ".gdpr-columns" ).hasClass( "active-group" ) ) {
						$( ".gdpr-columns" ).removeClass( "active-group" )
						$( ".gdpr-columns" ).css( 'background-color', background_obj.background );
						$( this ).children( ".gdpr-columns" ).addClass( "active-group" )
						$( this ).children( ".gdpr-columns" ).css( 'background-color', GDPR.settings.button_accept_button_color )
					}
					if ( $( this ).siblings( ".description-container" ).hasClass( "hide" ) ) {
						$( ".description-container" ).addClass( "hide" )
						$( this ).siblings( ".description-container" ).removeClass( "hide" )
					}
				}
			);
			$( ".gdpr-category-toggle.gdpr-column" ).click(
				function() {
					$( ".gdpr-category-toggle.gdpr-column", this );
					if ( ! $( this ).children( ".gdpr-columns" ).hasClass( "active-group" ) ) {
						$( ".gdpr-columns" ).removeClass( "active-group" );
						$( ".gdpr-columns" ).css( 'background-color', background_obj.background );
						$( ".gdpr-columns .dashicons" ).removeClass( "dashicons-minus" );
						$( ".gdpr-columns .dashicons" ).addClass( "dashicons-plus" );
						$( this ).children( ".gdpr-columns" ).addClass( "active-group" );
						$( '.toggle-group' ).find( 'div.always-active' ).css( 'color',GDPR.settings.button_accept_button_color );
						$( this ).siblings( '.toggle-group' ).find( 'div.always-active' ).css( 'color','#ffffff' );
						$( this ).children( ".gdpr-columns" ).css( 'background-color', GDPR.settings.button_accept_button_color );
						$( this ).children( ".gdpr-columns" ).find( ".dashicons" ).removeClass( "dashicons-plus" );
						$( this ).children( ".gdpr-columns" ).find( ".dashicons" ).addClass( "dashicons-minus" );
					} else {
						$( ".gdpr-columns" ).removeClass( "active-group" );
						$( this ).siblings( '.toggle-group' ).find( 'div.always-active' ).css( 'color',GDPR.settings.button_accept_button_color );
						$( ".gdpr-columns" ).css( 'background-color', background_obj.background );
						$( this ).children( ".gdpr-columns" ).find( ".dashicons" ).removeClass( "dashicons-minus" );
						$( this ).children( ".gdpr-columns" ).find( ".dashicons" ).addClass( "dashicons-plus" );
					}
					if ( $( this ).siblings( ".description-container" ).hasClass( "hide" ) ) {
						$( ".description-container" ).addClass( "hide" )
						$( this ).siblings( ".description-container" ).removeClass( "hide" )
					} else {
						$( ".description-container" ).addClass( "hide" )
					}
				}
			);
		}
	);

})( jQuery );
