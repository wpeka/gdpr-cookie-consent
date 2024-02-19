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
			var WPL_cookie_scanner = {
				continue_scan:1,
				pollCount:0,
				onPrg:0,
				Set:function()
			{
					$( '.gdpr_scan_now' ).click(
						function(){
							WPL_cookie_scanner.continue_scan = 1;
							WPL_cookie_scanner.doScan();
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
							if (decision == 'discovered-cookies') {
								data_parent         = content_parent.find( '#scan_cookie_list' );
								var cntr            = data_parent.children( '.scan-cookie-list' );
								var cookie_scan_arr = Array();
								cntr.each(
									function(){
										var elm        = $( this ).children( '.right' ).eq( 0 );
										var cid        = elm.find( 'input[type=hidden]' ).val();
										var ccategory  = elm.find( 'select[name="cookie_category_field_' + cid + '"] :selected' ).val();
										var cdesc      = elm.find( 'textarea[name="cookie_description_field_' + cid + '"]' ).val();
										var cookie_arr = {
											cid: cid,
											ccategory: ccategory,
											cdesc: cdesc,
										}
										cookie_scan_arr.push( cookie_arr );
									}
								);
								WPL_cookie_scanner.updateScanCookie( cookie_scan_arr );
							}
						}
					);
				},
				updateScanCookie:function(cookie_arr) {
					var data = {
						action: 'wpl_cookie_scanner',
						security: wplcookieconsent_cookie_scanner.nonces.wpl_cookie_scanner,
						wpl_scanner_action:'update_scan_cookie',
						cookie_arr: cookie_arr,
					};
					$.ajax(
						{
							url: wplcookieconsent_cookie_scanner.ajax_url,
							data: data,
							dataType:'json',
							type: 'POST',
							success: function (data)
						{
								if (data.response === true) {
									gdpr_notify_msg.success( data.message );
									WPL_cookie_scanner.showScanCookieList();
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
				deletePostCookie:function(cookie_id) {
					var data = {
						action: 'wpl_cookie_scanner',
						security: wplcookieconsent_cookie_scanner.nonces.wpl_cookie_scanner,
						wpl_scanner_action:'delete_post_cookie',
						cookie_id: cookie_id,
					};

					$.ajax(
						{
							url: wplcookieconsent_cookie_scanner.ajax_url,
							data: data,
							dataType:'json',
							type: 'POST',
							success: function (data)
						{
								if (data.response === true) {
									gdpr_notify_msg.success( data.message );
									WPL_cookie_scanner.showPostCookieList();
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
				doScan:function()
			{
					var data    = {
						action: 'wpl_cookie_scanner',
						security: wplcookieconsent_cookie_scanner.nonces.wpl_cookie_scanner,
						wpl_scanner_action:'check_api',
					};
					var scanbar = $( '.wpl_scanbar' );
					scanbar.html( '<span style="float:left; height:40px; line-height:40px;">' + wplcookieconsent_cookie_scanner.labels.checking_api + '</span> <img src="' + wplcookieconsent_cookie_scanner.loading_gif + '" style="display:inline-block;" />' );
					$.ajax(
						{
							url: wplcookieconsent_cookie_scanner.ajax_url,
							data: data,
							dataType:'json',
							type: 'POST',
							success: function (data)
						{
								scanbar.html( '' );
								if (data.response === true) {
									WPL_cookie_scanner.scanNow();
								} else {
									WPL_cookie_scanner.serverUnavailable( scanbar,data.message );
								}
							},
							error:function()
						{
								scanbar.html( '' );
								WPL_cookie_scanner.showErrorScreen( wplcookieconsent_cookie_scanner.labels.error );
							}
						}
					);
				},
				showScanCookieList:function() {
					var data           = {
						action: 'wpl_cookie_scanner',
						security: wplcookieconsent_cookie_scanner.nonces.wpl_cookie_scanner,
						wpl_scanner_action:'scan_cookie_list',
					};
					var scancookielist = $( '#scan_cookie_list' );

					$.ajax(
						{
							url: wplcookieconsent_cookie_scanner.ajax_url,
							data: data,
							dataType:'json',
							type: 'POST',
							success: function (data)
						{
								if (data.response === true) {
									scancookielist.html( data.content );
								} else {
									WPL_cookie_scanner.serverUnavailable( scancookielist,data.message );
								}
							},
							error:function()
						{
								WPL_cookie_scanner.showErrorScreen( wplcookieconsent_cookie_scanner.labels.error );
							}
						}
					);
				},
				serverUnavailable:function(elm,msg)
			{
					elm.html( '<div style="background:#ffffff; border:solid 1px #cccccc; color:#333333; padding:5px;">' + msg + '</div>' );
				},

				scanAgain:function()
			{
					$( '.gdpr_scan_again' ).unbind( 'click' ).click(
						function(){
							WPL_cookie_scanner.continue_scan = 1;
							WPL_cookie_scanner.scanNow();
						}
					);
				},
				scanNow:function()
			{
					var html    = this.makeHtml();
					var scanbar = $( '.gdpr_scanbar' );
					scanbar.html( html );
					$( '.gdpr_scanbar_staypage' ).show();
					this.attachScanStop();
					$( '.gdpr_scanlog' ).css( {'display':'block','opacity':0} ).animate(
						{
							'opacity':1,'height':'auto'
						},
						1000
					);
					this.takePages( 0 );
				},
				takePages:function(offset,limit,total,scan_id)
			{
					var data = {
						action: 'wpl_cookie_scanner',
						security: wplcookieconsent_cookie_scanner.nonces.wpl_cookie_scanner,
						wpl_scanner_action:'get_pages',
						offset:offset
					};
					if (limit) {
						data['limit'] = limit;
					}
					if (total) {
						data['total'] = total;
					}
					if (scan_id) {
						data['scan_id'] = scan_id;
					}
					// fake progress.
					this.animateProgressBar( 1,100,wplcookieconsent_cookie_scanner.labels.finding );
					$.ajax(
						{
							url: wplcookieconsent_cookie_scanner.ajax_url,
							data: data,
							dataType: 'json',
							type: 'POST',
							success: function (data)
						{
								WPL_cookie_scanner.scan_id = typeof data.scan_id != 'undefined' ? data.scan_id : 0;
								if (WPL_cookie_scanner.continue_scan == 0) {
									return false;
								}
								if (typeof data.response != 'undefined' && data.response === true) {
									WPL_cookie_scanner.appendLogAnimate( data.log,0 );
									var new_offset = parseInt( data.offset ) + parseInt( data.limit );
									if ((data.total - 1) > new_offset) { // substract 1 from total because of home page.
										WPL_cookie_scanner.takePages( new_offset,data.limit,data.total,data.scan_id );
									} else {
										$( '.wpl_progress_action_main' ).html( wplcookieconsent_cookie_scanner.labels.scanning );
										WPL_cookie_scanner.scanPages( data.scan_id,0,data.total );
									}
								} else {
									WPL_cookie_scanner.showErrorScreen( wplcookieconsent_cookie_scanner.labels.error );
								}
							},
							error:function()
						{
								if (WPL_cookie_scanner.continue_scan == 0) {
									return false;
								}
								WPL_cookie_scanner.showErrorScreen( wplcookieconsent_cookie_scanner.labels.error );
							}
						}
					);
				},
				scanPages:function(scan_id,offset,total)
			{
					var scanbar                  = $( '.gdpr_scanbar' );
					WPL_cookie_scanner.pollCount = 0;
					var hash                     = Math.random().toString( 36 ).replace( '0.', '' );
					var data                     = {
						action: 'wpl_cookie_scanner',
						security: wplcookieconsent_cookie_scanner.nonces.wpl_cookie_scanner,
						wpl_scanner_action:'scan_pages',
						offset:offset,
						scan_id:scan_id,
						total:total,
						hash:hash
					};
					$.ajax(
						{
							url: wplcookieconsent_cookie_scanner.ajax_url,
							data: data,
							dataType: 'json',
							type: 'POST',
							success:function(data)
						{
								WPL_cookie_scanner.scan_id = typeof data.scan_id != 'undefined' ? data.scan_id : 0;
								if (WPL_cookie_scanner.continue_scan == 0) {
									return false;
								}
								if (data.response == true) {
									WPL_cookie_scanner.getScanCookies( scan_id,offset,total,hash );
								} else {
									scanbar.html( '' );
									$( '.wpl_scanbar_staypage' ).hide();
									WPL_cookie_scanner.serverUnavailable( scanbar,data.message );
								}
							},
							error:function()
						{
								if (WPL_cookie_scanner.continue_scan == 0) {
									return false;
								}
								// error and retry function.
								WPL_cookie_scanner.animateProgressBar( offset,total,wplcookieconsent_cookie_scanner.labels.retrying );
								setTimeout(
									function(){
										WPL_cookie_scanner.scanPages( scan_id,offset,total );
									},
									2000
								);
							}
						}
					);
				},
				getScanCookies:function(scan_id,offset,total,hash)
				{
					var data = {
						action: 'wpl_cookie_scanner',
						security: wplcookieconsent_cookie_scanner.nonces.wpl_cookie_scanner,
						wpl_scanner_action:'get_post_scan_cookies',
						offset:offset,
						scan_id:scan_id,
						total:total,
						hash:hash
					};
					$.ajax(
						{
							url: wplcookieconsent_cookie_scanner.ajax_url,
							data: data,
							dataType: 'json',
							type: 'POST',
							success:function(data)
							{
								if (data.response == true) {
									var prg_offset = parseInt( offset ) + parseInt( data.total_scanned );
									var prg_msg    = wplcookieconsent_cookie_scanner.labels.scanning + ' ';
									WPL_cookie_scanner.appendLogAnimate( data.log,0 );
									if (data.continue === true) {
										WPL_cookie_scanner.scanPages( data.scan_id,data.offset,data.total );
									} else {
										prg_msg  = wplcookieconsent_cookie_scanner.labels.finished;
										prg_msg += ' (' + wplcookieconsent_cookie_scanner.labels.total_cookies_found + ': ' + data.total_cookies + ')';
										WPL_cookie_scanner.showSuccessScreen( prg_msg,scan_id,1 );
									}
									WPL_cookie_scanner.animateProgressBar( prg_offset,total,prg_msg );
								} else {
									if (WPL_cookie_scanner.pollCount < 10) {
										WPL_cookie_scanner.pollCount++;
										setTimeout(
											function(){
												WPL_cookie_scanner.getScanCookies( data.scan_id,data.offset,data.total,data.hash );
											},
											10000
										);
									} else {
										WPL_cookie_scanner.showErrorScreen( 'Something went wrong, please scan again' );
									}
								}
							},
							error:function()
							{
								if (WPL_cookie_scanner.continue_scan == 0) {
									return false;
								}
								if (WPL_cookie_scanner.pollCount < 10) {
									setTimeout(
										function(){
											WPL_cookie_scanner.getScanCookies( offset, scan_id, total, hash );
										},
										5000
									);
								} else {
									WPL_cookie_scanner.showErrorScreen( 'Something went wrong, please scan again' );
								}
							}
						}
					);
				},
				animateProgressBar:function(offset,total,msg)
			{
					var prgElm = $( '.gdpr_progress_bar' );
					var w      = prgElm.width();
					var sp     = 100 / total;
					var sw     = w / total;
					var cw     = sw * offset;
					var cp     = sp * offset;

					cp = cp > 100 ? 100 : cp;
					cp = Math.floor( cp < 1 ? 1 : cp );

					cw = cw > w ? w : cw;
					cw = Math.floor( cw < 1 ? 1 : cw );
					$( '.gdpr_progress_bar_inner' ).stop( true,true ).animate(
						{'width':cw + 'px'},
						300,
						function(){
							$( '.gdpr_progress_action_main' ).html( msg );
						}
					).html( cp + '%' );
				},
				makeHtml:function()
			{
					return '<div class="gdpr_scanlog">'
					+ '<div class="gdpr_progress_action_main">' + wplcookieconsent_cookie_scanner.labels.finding + '</div>'
					+ '<div class="gdpr_progress_bar">'
					+ '<span class="gdpr_progress_bar_inner">'
					+ '</span>'
					+ '</div>'
					+ '<div class="gdpr_scanlog_bar"><a class="button-primary pull-right gdpr_stop_scan">' + wplcookieconsent_cookie_scanner.labels.stop + '</a></div>'
					+ '</div>';
				},
				appendLogAnimate:function(data,offset)
			{
					if (data.length > offset) {

						offset++;
						var speed = 300 / data.length;
						setTimeout(
							function(){
								WPL_cookie_scanner.appendLogAnimate( data,offset );
							},
							speed
						);
					}
				},
				showErrorScreen:function(error_msg)
			{
					var html = '<a class="button-primary pull-right gdpr_scan_again" style="margin-left:5px;">' + wplcookieconsent_cookie_scanner.labels.scan_again + '</a>';
					$( '.gdpr_scanlog_bar' ).html( html );
					$( '.gdpr_progress_action_main' ).html( error_msg );
					gdpr_notify_msg.error( error_msg );
					$( '.gdpr_scanbar_staypage' ).hide();
					this.scanAgain();
				},
				showSuccessScreen:function(success_msg,scan_id,total)
			{
					var html = '<a class="button-primary pull-right gdpr_scan_again" style="margin-left:5px;">' + wplcookieconsent_cookie_scanner.labels.scan_again + '</a>';
					html    += '<span class="spinner" style="margin-top:5px"></span>';
					$( '.gdpr_scanlog_bar' ).html( html );
					$( '.gdpr_progress_action_main' ).html( success_msg );
					gdpr_notify_msg.success( success_msg );
					$( '.gdpr_scanbar_staypage' ).hide();
					this.showScanCookieList();
					this.scanAgain();
				},

				hideProgressbarAndLog:function()
			{
					if ($( '.gdpr_scanlog' ).length > 0) {
						$( '.gdpr_progress_bar' ).hide();
						$( '.gdpr_scan_success_bottom' ).html( '' );
					}
				},
				attachScanStop:function()
			{
					$( '.gdpr_stop_scan' ).click(
						function(){
							WPL_cookie_scanner.stopScan();
						}
					);
				},
				stopingScan:function(scan_id)
			{
					var data = {
						action: 'wpl_cookie_scanner',
						security: wplcookieconsent_cookie_scanner.nonces.wpl_cookie_scanner,
						wpl_scanner_action:'stop_scan',
						scan_id:scan_id
					};
					$( '.gdpr_stop_scan' ).html( wplcookieconsent_cookie_scanner.labels.stoping ).css( {'opacity':'.5'} );
					$.ajax(
						{
							url: wplcookieconsent_cookie_scanner.ajax_url,
							data: data,
							dataType: 'json',
							type: 'POST',
							success:function(data)
						{
								WPL_cookie_scanner.showSuccessScreen( wplcookieconsent_cookie_scanner.labels.scanning_stopped,scan_id,data.total );
							},
							error:function()
						{
								// error function.
								WPL_cookie_scanner.showErrorScreen( wplcookieconsent_cookie_scanner.labels.error );
							}
						}
					);
				},
				stopScan:function()
			{
					if (WPL_cookie_scanner.continue_scan == 0) {
						return false;
					}
					if (confirm( wplcookieconsent_cookie_scanner.labels.ru_sure )) {
						WPL_cookie_scanner.continue_scan = 0;
						this.stopingScan( WPL_cookie_scanner.scan_id );
					}
				}
			}

			WPL_cookie_scanner.Set();
		}
	);
})( jQuery );
