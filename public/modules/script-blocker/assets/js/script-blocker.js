/**
 * Module ScriptBlocker JavaScript.
 *
 * @package    Wpl_Cookie_Consent
 * @subpackage Wpl_Cookie_Consent/public/modules
 * @author     wpeka <https://club.wpeka.com>
 */

 (function( $ ) {
	$(
		function() {
			var GDPR_script_blocker = {
				Set:function()
			{
					$( document ).on(
						'change',
						'.script_status',
						function(evn){
							evn.preventDefault();
							var status = '1';
							if ($( this ).prop( "checked" ) == true) {
								status = '1';
							} else if ($( this ).prop( "checked" ) == false) {
								status = '0';
							}
							var parent = $( this ).parents( '.right-grid-6' );
							var id     = parent.find( 'input[name="script_id"]' ).val();
							var data   = {
								action: 'wpl_script_blocker',
								security: wplcookieconsent_script_blocker.nonces.wpl_script_blocker,
								wpl_script_action:'update_script_status',
								status: status,
								id: id,
							};
							$.ajax(
								{
									url: wplcookieconsent_script_blocker.ajax_url,
									data: data,
									dataType:'json',
									type: 'POST',
									success: function (data)
									{
										if (data.response === true) {
											gdpr_notify_msg.success( data.message );
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
						}
					);

					$( document ).on(
						'change',
						'.script_category',
						function(evn){
							evn.preventDefault();
							var category = $( this ). children( "option:selected" ). val();
							var parent   = $( this ).parents( '.right-grid-6' );
							var id       = parent.find( 'input[name="script_id"]' ).val();
							var data     = {
								action: 'wpl_script_blocker',
								security: wplcookieconsent_script_blocker.nonces.wpl_script_blocker,
								wpl_script_action:'update_script_category',
								category: category,
								id: id,
							};
							$.ajax(
								{
									url: wplcookieconsent_script_blocker.ajax_url,
									data: data,
									dataType:'json',
									type: 'POST',
									success: function (data)
									{
										if (data.response === true) {
											gdpr_notify_msg.success( data.message );
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

			GDPR_script_blocker.Set();
		}
	);
})( jQuery );
