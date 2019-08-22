/**
 * Module CookieScanner JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin/modules
 * @author     wpeka <https://club.wpeka.com>
 */

(function( $ ) {
	$(
		function() {
			var GDPR_cookie_custom = {
				Set:function()
			{
					$( document ).on(
						'keyup',
						'.form-table .cookie-duration-field',
						function(){
							$( this ).val( $( this ).val().replace( /\D/, "" ) );
						}
					);
					$( document ).on(
						'click',
						'.update_admin_cookies',
						function(){
							var parent         = $( this ).parents( '.gdpr-cookie-consent-tab-content' );
							var content_parent = parent.find( '.gdpr_cookie_sub_tab_container .gdpr_cookie_sub_tab_content:visible' )
							var decision       = content_parent.data( 'id' );
							var data_parent    = '';
							if (decision == 'custom-cookies') {
								var error           = false;
								data_parent         = content_parent.find( '#post_cookie_list' );
								var cntr            = data_parent.children( '.post-cookie-list' );
								var cookie_post_arr = Array();
								cntr.each(
									function(){
										var elm       = $( this ).children( '.right' ).eq( 0 );
										var pattern   = /^((http|https):\/\/)?([a-zA-Z0-9_][-_a-zA-Z0-9]{0,62}\.)+([a-zA-Z0-9]{1,10})$/gm;
										var cid       = elm.find( 'input[type="hidden"]' ).val();
										var cname     = elm.find( 'input[name="cookie_name_field_' + cid + '"]' ).val();
										var cdomain   = elm.find( 'input[name="cookie_domain_field_' + cid + '"]' ).val();
										var cduration = elm.find( 'input[name="cookie_duration_field_' + cid + '"]' ).val();
										var ccategory = elm.find( 'select[name="cookie_category_field_' + cid + '"] :selected' ).val();
										var ctype     = elm.find( 'select[name="cookie_type_field_' + cid + '"] :selected' ).val();
										var cdesc     = elm.find( 'textarea[name="cookie_description_field_' + cid + '"]' ).val();
										if ( ! cname) {
											gdpr_notify_msg.error( 'Please fill in these mandatory fields : Cookie Name' );
											elm.find( 'input[name="cookie_name_field_' + cid + '"]' ).focus();
											error = true;
										}
										if ( ! cdomain) {
											gdpr_notify_msg.error( 'Please fill in these mandatory fields : Cookie Domain' );
											elm.find( 'input[name="cookie_domain_field_' + cid + '"]' ).focus();
											error = true;
										} else {
											if ( ! pattern.test( cdomain )) {
												gdpr_notify_msg.error( 'Cookie Domain is not valid.' );
												elm.find( 'input[name="cookie_domain_field_' + cid + '"]' ).focus();
												error = true;
											}
										}
										if ( ! cduration) {
											gdpr_notify_msg.error( 'Please fill in these mandatory fields : Cookie Duration' );
											elm.find( 'input[name="cookie_duration_field_' + cid + '"]' ).focus();
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
								);
								if (error) {
									return false;
								}
								GDPR_cookie_custom.updatePostCookie( cookie_post_arr );
							}
						}
					);

					$( document ).on(
						'click',
						'.gdpr_delete_post_cookie',
						function(){
							var parent    = $( this ).parents( 'div.post-cookie-list' );
							var cookie_id = parent.find( 'input[type="hidden"]' ).val();
							GDPR_cookie_custom.deletePostCookie( cookie_id );
						}
					);

					$( '.gdpr_save_cookie' ).click(
						function(){
							var parent         = $( this ).parents( 'div.add-cookie' );
							var gdpr_addcookie = parent.find( 'input[name="gdpr_addcookie"]' ).val();
							if (gdpr_addcookie == 1) {
								var pattern   = /^((http|https):\/\/)?([a-zA-Z0-9_][-_a-zA-Z0-9]{0,62}\.)+([a-zA-Z0-9]{1,10})$/gm;
								var cname     = parent.find( 'input[name="cookie_name_field"]' ).val();
								var cdomain   = parent.find( 'input[name="cookie_domain_field"]' ).val();
								var cduration = parent.find( 'input[name="cookie_duration_field"]' ).val();
								var ccategory = parent.find( 'select[name="cookie_category_field"] :selected' ).val();
								var ctype     = parent.find( 'select[name="cookie_type_field"] :selected' ).val();
								var cdesc     = parent.find( 'textarea[name="cookie_description_field"]' ).val();
								if ( ! cname) {
									gdpr_notify_msg.error( 'Please fill in these mandatory fields : Cookie Name' );
									parent.find( 'input[name="cookie_name_field"]' ).focus();
									return false;
								}
								if ( ! cdomain) {
									gdpr_notify_msg.error( 'Please fill in these mandatory fields : Cookie Domain' );
									parent.find( 'input[name="cookie_domain_field"]' ).focus();
									return false;
								} else {
									if ( ! pattern.test( cdomain )) {
										gdpr_notify_msg.error( 'Cookie domain is not valid.' );
										parent.find( 'input[name="cookie_domain_field"]' ).focus();
										return false;
									}
								}
								if ( ! cduration) {
									gdpr_notify_msg.error( 'Please fill in these mandatory fields : Cookie Duration' );
									parent.find( 'input[name="cookie_duration_field"]' ).focus();
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
							}
							GDPR_cookie_custom.savePostCookie( cookie_arr );
						}
					);
				},
				deletePostCookie:function(cookie_id) {
					var data = {
						action: 'gdpr_cookie_custom',
						security: gdprcookieconsent_cookie_custom.nonces.gdpr_cookie_custom,
						gdpr_custom_action:'delete_post_cookie',
						cookie_id: cookie_id,
					};

					$.ajax(
						{
							url: gdprcookieconsent_cookie_custom.ajax_url,
							data: data,
							dataType:'json',
							type: 'POST',
							success: function (data)
						{
								if (data.response === true) {
									gdpr_notify_msg.success( data.message );
									GDPR_cookie_custom.showPostCookieList();
								} else {
									gdpr_notify_msg.error( data.message );
								}
							},
							error:function()
						{
								gdpr_notify_msg.error( data.message );
							}
						}
					);
				},
				updatePostCookie:function(cookie_arr) {
					var data = {
						action: 'gdpr_cookie_custom',
						security: gdprcookieconsent_cookie_custom.nonces.gdpr_cookie_custom,
						gdpr_custom_action:'update_post_cookie',
						cookie_arr: cookie_arr,
					};
					$.ajax(
						{
							url: gdprcookieconsent_cookie_custom.ajax_url,
							data: data,
							dataType:'json',
							type: 'POST',
							success: function (data)
						{
								if (data.response === true) {
									gdpr_notify_msg.success( data.message );
									GDPR_cookie_custom.showPostCookieList();
								} else {
									gdpr_notify_msg.error( data.message );
								}
							},
							error:function()
						{
								gdpr_notify_msg.error( data.message );
							}
						}
					);
				},
				savePostCookie:function(cookie_arr) {
					var data = {
						action: 'gdpr_cookie_custom',
						security: gdprcookieconsent_cookie_custom.nonces.gdpr_cookie_custom,
						gdpr_custom_action:'save_post_cookie',
						cookie_arr: cookie_arr,
					};

					$.ajax(
						{
							url: gdprcookieconsent_cookie_custom.ajax_url,
							data: data,
							dataType:'json',
							type: 'POST',
							success: function (data)
						{
								if (data.response === true) {
									gdpr_notify_msg.success( data.message );
									GDPR_cookie_custom.hideCookieForm();
									GDPR_cookie_custom.showPostCookieList();
								} else {
									gdpr_notify_msg.error( data.message );
								}
							},
							error:function()
						{
								gdpr_notify_msg.error( data.message );
							}
						}
					);
				},
				hideCookieForm:function() {
					$( '.form-table.add-cookie' ).find( 'input' ).val( '' );
					$( '.form-table.add-cookie' ).find( 'select' ).val( '' );
					$( '.form-table.add-cookie' ).find( 'textarea' ).val( '' );
					$( '.form-table.add-cookie' ).find( 'input[type="hidden"]' ).remove();
					$( '.form-table.add-cookie' ).parent().find( '.gdpr_postbar' ).show();
					$( '.form-table.add-cookie' ).hide();
				},

				showPostCookieList:function() {
					var data           = {
						action: 'gdpr_cookie_custom',
						security: gdprcookieconsent_cookie_custom.nonces.gdpr_cookie_custom,
						gdpr_custom_action:'post_cookie_list',
					};
					var postcookielist = $( '#post_cookie_list' );

					$.ajax(
						{
							url: gdprcookieconsent_cookie_custom.ajax_url,
							data: data,
							dataType:'json',
							type: 'POST',
							success: function (data)
						{
								if (data.response === true) {
									postcookielist.html( data.content );
								} else {
									GDPR_cookie_custom.serverUnavailable( postcookielist,data.message );
								}
							},
							error:function()
						{
								GDPR_cookie_custom.showErrorScreen( gdprcookieconsent_cookie_custom.labels.error );
							}
						}
					);
				},
			}

			GDPR_cookie_custom.Set();
		}
	);
})( jQuery );
