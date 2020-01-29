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

	var GDPR = {
		bar_config:{},
		show_config:{},
		allowed_categories:[],
		set:function(args) {
			if (typeof JSON.parse !== "function") {
				console.log( "GDPRCookieConsent requires JSON.parse but your browser doesn't support it" );
				return;
			}
			this.settings       = JSON.parse( args.settings );
			this.bar_elm        = jQuery( this.settings.notify_div_id );
			this.show_again_elm = jQuery( this.settings.show_again_div_id );

			this.details_elm = this.bar_elm.find( '.gdpr_messagebar_detail' );

			/* buttons */
			this.main_button     = jQuery( '#cookie_action_accept' );
			this.main_link       = jQuery( '#cookie_action_link' );
			this.reject_button   = jQuery( '#cookie_action_reject' );
			this.settings_button = jQuery( '#cookie_action_settings' );
			this.save_button     = jQuery( '#cookie_action_save' );
			this.credit_link     = jQuery( '#cookie_credit_link' );

			this.configBar();
			this.toggleBar();
			this.attachEvents();
			this.configButtons();
			if ( this.settings.auto_scroll ) {
				window.addEventListener( "scroll", GDPR.acceptOnScroll, false );
			}
			var gdpr_user_preference = JSON.parse( GDPR_Cookie.read( 'wpl_user_preference' ) );
			var gdpr_viewed_cookie   = GDPR_Cookie.read( 'wpl_viewed_cookie' );
			var event                = new CustomEvent(
				'GdprCookieConsentOnLoad',
				{
					detail: {
						'wpl_user_preference': gdpr_user_preference,
						'wpl_viewed_cookie' : gdpr_viewed_cookie,
					}
				}
			);
			window.dispatchEvent( event );
		},
		attachEvents:function() {
			jQuery( '.gdpr_action_button' ).click(
				function(e){
					e.preventDefault();
					var event                    = '';
					var gdpr_user_preference     = '';
					var gdpr_user_preference_val = '';
					var gdpr_viewed_cookie       = '';
					var elm                      = jQuery( this );
					var button_action            = elm.attr( 'data-gdpr_action' );
					var open_link                = elm[0].hasAttribute( "href" ) && elm.attr( "href" ) != '#' ? true : false;
					var new_window               = false;
					if (button_action == 'accept') {
						GDPR.accept_close();
						new_window               = GDPR.settings.button_1_new_win ? true : false;
						gdpr_user_preference     = JSON.parse( GDPR_Cookie.read( 'wpl_user_preference' ) );
						gdpr_user_preference_val = JSON.stringify( gdpr_user_preference );
						GDPR_Cookie.set( 'wpl_user_preference',gdpr_user_preference_val,GDPR_ACCEPT_COOKIE_EXPIRE );
						gdpr_viewed_cookie = GDPR_Cookie.read( 'wpl_viewed_cookie' );
						event              = new CustomEvent(
							'GdprCookieConsentOnAccept',
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
					} else if ( button_action == 'settings' ) {
						GDPR.bar_elm.slideUp( GDPR.settings.animate_speed_hide );
						if (GDPR.settings.cookie_bar_as == 'popup') {
							$( "#gdpr-popup" ).modal( 'hide' );
						}
						GDPR.show_again_elm.slideUp( GDPR.settings.animate_speed_hide );
					} else if ( button_action == 'close' ) {
						GDPR.displayHeader();
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

			this.settings_button.css( 'color',this.settings.button_4_link_color );
			if (this.settings.button_4_as_button) {
				this.settings_button.css( 'background-color',this.settings.button_4_button_color );
				this.settings_button.hover(
					function () {
						jQuery( this ).css( 'background-color',GDPR.settings.button_4_button_hover );
					},
					function (){
						jQuery( this ).css( 'background-color',GDPR.settings.button_4_button_color );
					}
				);
			}

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

			this.reject_button.css( 'color',this.settings.button_3_link_color );
			if (this.settings.button_3_as_button) {
				this.reject_button.css( 'display', 'inline-block' );
				this.reject_button.css( 'background-color',this.settings.button_3_button_color );
				this.reject_button.hover(
					function () {
						jQuery( this ).css( 'background-color',GDPR.settings.button_3_button_hover );
					},
					function () {
						jQuery( this ).css( 'background-color',GDPR.settings.button_3_button_color );
					}
				);
			}

			this.save_button.css( 'color',this.settings.button_1_link_color );
			this.save_button.css( 'background-color',this.settings.button_1_button_color );
			this.save_button.hover(
				function () {
					jQuery( this ).css( 'background-color',GDPR.settings.button_1_button_hover );
				},
				function (){
					jQuery( this ).css( 'background-color',GDPR.settings.button_1_button_color );
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

			this.credit_link.css( 'color',this.settings.button_2_link_color );

		},

		configBar:function() {
			this.bar_config  = {
				'background-color':this.settings.background,
				'color':this.settings.text,
				'font-family':this.settings.font_family
			};
			this.show_config = {
				'width':'auto',
				'background-color':this.settings.background,
				'color':this.settings.text,
				'font-family':this.settings.font_family,
				'position':'fixed',
				'opacity':this.settings.opacity,
				'bottom':'0',
			};
			if (this.settings.show_again_position == 'right') {
				this.show_config['right'] = this.settings.show_again_margin;
			} else {
				this.show_config['left'] = this.settings.show_again_margin;
			}
			this.bar_config['position'] = 'fixed';
			if (this.settings.cookie_bar_as == 'banner') {
				this.bar_config['opacity'] = this.settings.opacity;
				this.bar_elm.find( '.gdpr_messagebar_content' ).css( 'max-width','800px' );
				if (this.settings.notify_position_vertical == 'bottom') {
					this.bar_config['bottom'] = '0';
				} else {
					this.bar_config['top'] = '0';
				}
			}
			if (this.settings.cookie_bar_as == 'widget') {
				this.bar_config['bottom']    = '20px';
				this.bar_config['max-width'] = '450px';
				this.bar_config['opacity']   = this.settings.opacity;
				this.bar_elm.find( '.gdpr_messagebar_content' ).css( 'max-width','400px' );
				if (this.settings.notify_position_horizontal == 'left') {
					this.bar_config['left'] = '20px';
				} else {
					this.bar_config['right'] = '20px';
				}
			}
			if (this.settings.cookie_bar_as == 'popup') {
				this.bar_config['position']   = 'unset';
				this.bar_config['box-shadow'] = 'unset';
				jQuery( '#gdpr-popup .modal-content' ).css( 'opacity',this.settings.opacity );
			}
			this.bar_elm.css( this.bar_config ).hide();
			this.show_again_elm.css( this.show_config ).hide();
		},

		toggleBar:function() {
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
		},

		accept_close:function() {
			GDPR_Cookie.set( GDPR_ACCEPT_COOKIE_NAME,'yes',GDPR_ACCEPT_COOKIE_EXPIRE );
			if (this.settings.notify_animate_hide) {
				this.bar_elm.slideUp( this.settings.animate_speed_hide , GDPR_Blocker.runScripts );
			} else {
				this.bar_elm.hide( GDPR_Blocker.runScripts );
			}
			if (this.settings.cookie_bar_as == 'popup') {
				$( "#gdpr-popup" ).modal( 'hide' );
			}
			this.show_again_elm.slideDown( this.settings.animate_speed_hide );
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
				$( "#gdpr-popup" ).modal( 'hide' );
			}
			this.show_again_elm.slideDown( this.settings.animate_speed_hide );
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
		displayHeader:function() {
			this.bar_elm.show();
			if (this.settings.cookie_bar_as == 'popup') {
				$( "#gdpr-popup" ).modal( 'show' );
			}
			this.show_again_elm.slideUp( this.settings.animate_speed_hide );
		},
		hideHeader:function() {
			this.bar_elm.slideUp( this.settings.animate_speed_hide );
			if (this.settings.cookie_bar_as == 'popup') {
				$( "#gdpr-popup" ).modal( 'hide' );
			}
			this.show_again_elm.slideDown( this.settings.animate_speed_hide );
		},
		acceptOnScroll:function(){
			var scroll = this.scrollY;
			if (scroll > 150 && ! GDPR_Cookie.exists( GDPR_ACCEPT_COOKIE_NAME )) {
				GDPR.accept_close();
				window.removeEventListener( "scroll",GDPR.acceptOnScroll,false );
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
			this.cookies = JSON.parse( args.cookies );
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

					arr[index](function () {
						index++
						if (index === arr.length) {
							callback()
						} else {
							scriptFuncs.seq( arr, callback, index )
						}
					})
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
	);

	$( document ).ready(
		function() {

			$( ".gdpr-column" ).click(
				function() {
					$( ".gdpr-column", this );
					if ( ! $( this ).children( ".gdpr-columns" ).hasClass( "active-group" ) ) {
						$( ".gdpr-columns" ).removeClass( "active-group" )
						$( this ).children( ".gdpr-columns" ).addClass( "active-group" )
					}
					if ( $( this ).siblings( ".description-container" ).hasClass( "hide" ) ) {
						$( ".description-container" ).addClass( "hide" )
						$( this ).siblings( ".description-container" ).removeClass( "hide" )
					}
				}
			);
		}
	);

})( jQuery );
