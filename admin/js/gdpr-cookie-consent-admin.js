/**
 * Admin JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin
 * @author     wpeka <https://club.wpeka.com>
 */

(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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
	$(
		function() {
			$( '.gdpr-color-field' ).wpColorPicker();

			var gdpr_nav_tab = $( '.gdpr-cookie-consent-tab-head .nav-tab' );
			if (gdpr_nav_tab.length > 0) {
				gdpr_nav_tab.click(
					function(){
						var gdpr_tab_hash = $( this ).attr( 'href' );
						gdpr_nav_tab.removeClass( 'nav-tab-active' );
						$( this ).addClass( 'nav-tab-active' );
						gdpr_tab_hash    = gdpr_tab_hash.charAt( 0 ) == '#' ? gdpr_tab_hash.substring( 1 ) : gdpr_tab_hash;
						var gdpr_tab_elm = $( 'div[data-id="' + gdpr_tab_hash + '"]' );
						$( '.gdpr-cookie-consent-tab-content' ).hide();
						if (gdpr_tab_elm.length > 0) {
							gdpr_tab_elm.fadeIn();
						}
					}
				);
				var location_hash = window.location.hash;
				if (location_hash != "") {
					var gdpr_tab_hash = location_hash.charAt( 0 ) == '#' ? location_hash.substring( 1 ) : location_hash;
					if ( gdpr_tab_hash != "" ) {
						$( 'div[data-id="' + gdpr_tab_hash + '"]' ).show();
						$( 'a[href="#' + gdpr_tab_hash + '"]' ).addClass( 'nav-tab-active' );
					}
				} else {
					gdpr_nav_tab.eq( 0 ).click();
				}
			}
			$( '.gdpr_sub_tab li' ).click(
				function(){
					var trgt = $( this ).attr( 'data-target' );
					var prnt = $( this ).parent( '.gdpr_sub_tab' );
					var ctnr = prnt.siblings( '.gdpr_sub_tab_container' );
					prnt.find( 'li a' ).css( {'color':'#0073aa','cursor':'pointer'} );
					$( this ).find( 'a' ).css( {'color':'unset','cursor':'default'} );
					ctnr.find( '.gdpr_sub_tab_content' ).hide();
					ctnr.find( '.gdpr_sub_tab_content[data-id="' + trgt + '"]' ).fadeIn();
				}
			);
			$( '.gdpr_sub_tab' ).each(
				function(){
					var template   = $( 'input[name="gdpr_template"]' ).val();
					var active_tab = $( '.gdpr-cookie-consent-tab-head .nav-tab.nav-tab-active' ).html();
					var elm        = $( this ).children( 'li' ).eq( 0 );
					if ( active_tab == 'Design' && template && template != 'none' ) {
						elm = $( this ).children( 'li' ).eq( 1 );
					}
					elm.click();
				}
			);
			$( '.gdpr_cookie_sub_tab li' ).click(
				function(){
					var trgt = $( this ).attr( 'data-target' );
					var prnt = $( this ).parent( '.gdpr_cookie_sub_tab' );
					var ctnr = prnt.siblings( '.gdpr_cookie_sub_tab_container' );
					prnt.find( 'li a' ).css( {'color':'#0073aa','cursor':'pointer'} );
					$( this ).find( 'a' ).css( {'color':'unset','cursor':'default'} );
					ctnr.find( '.gdpr_cookie_sub_tab_content' ).hide();
					ctnr.find( '.gdpr_cookie_sub_tab_content[data-id="' + trgt + '"]' ).fadeIn();
				}
			);
			$( '.gdpr_cookie_sub_tab' ).each(
				function(){
					var elm = $( this ).children( 'li' ).eq( 0 );
					elm.click();
				}
			);
			$( '.gdpr_add_cookie' ).click(
				function() {
					gdpr_add_cookie_form();
				}
			);
			$( '.gdpr_delete_cookie' ).click(
				function() {
					gdpr_hide_cookie_form()
				}
			);
			$( document ).on(
				'change',
				'#post_cookie_list .cookie-type-field',
				function(){
					var parent        = $( this ).parents( 'table:first' );
					var cid           = parent.find( 'input[type="hidden"]' ).val();
					var selectedValue = $( this ).find( ":selected" ).val();
					if (selectedValue == 'HTTP') {
						parent.find( 'input[name="cookie_duration_field_' + cid + '"]' ).val( '' );
						parent.find( 'input[name="cookie_duration_field_' + cid + '"]' ).removeAttr( 'disabled' );
					} else {
						parent.find( 'input[name="cookie_duration_field_' + cid + '"]' ).val( 'Persistent' );
						parent.find( 'input[name="cookie_duration_field_' + cid + '"]' ).attr( 'disabled','disabled' );
					}
				}
			);
			$( '.form-table.add-cookie' ).find( '.cookie-type-field' ).on(
				'change',
				function() {
					var selectedValue = $( this ).find( ":selected" ).val();
					if (selectedValue == 'HTTP') {
						$( '.form-table.add-cookie' ).find( 'input[name="cookie_duration_field"]' ).val( '' );
						$( '.form-table.add-cookie' ).find( 'input[name="cookie_duration_field"]' ).removeAttr( 'disabled' );
					} else {
						$( '.form-table.add-cookie' ).find( 'input[name="cookie_duration_field"]' ).val( 'Persistent' );
						$( '.form-table.add-cookie' ).find( 'input[name="cookie_duration_field"]' ).attr( 'disabled','disabled' );
					}
				}
			);
			$( '#gdpr_settings_form' ).submit(
				function(e){
					var submit_action = $( '#gdpr_update_action' ).val();
					e.preventDefault();
					var data       = $( this ).serialize();
					var url        = $( this ).attr( 'action' );
					var spinner    = $( this ).find( '.spinner' );
					var submit_btn = $( this ).find( 'input[type="submit"]' );
					spinner.css( {'visibility':'visible'} );
					submit_btn.css( {'opacity':'.5','cursor':'default'} ).prop( 'disabled',true );
					$.ajax(
						{
							url:url,
							type:'POST',
							data:data + '&gdpr_settings_ajax_update=' + submit_action,
							success:function(data)
						{
								spinner.css( {'visibility':'hidden'} );
								submit_btn.css( {'opacity':'1','cursor':'pointer'} ).prop( 'disabled',false );
								var flag = $( 'input[name="gdpr_template_updated"]' ).val();

								gdpr_notify_msg.success( gdpr_settings_success_message );

								gdpr_bar_active_msg();
								if ( flag && flag == '1' ) {
									setTimeout(
										function(){
											window.location.reload();
										},
										1000
									);
								} else {
									$( 'input[name="gdpr_template"]' ).val( 'none' )
									$( 'select[name="banner_template_field"]' ).val( 'none' );
									$( 'select[name="popup_template_field"]' ).val( 'none' );
									$( 'select[name="widget_template_field"]' ).val( 'none' );
									$( '.gdpr-template-type' ).hide();
								}
							},
							error:function ()
						{
								spinner.css( {'visibility':'hidden'} );
								submit_btn.css( {'opacity':'1','cursor':'pointer'} ).prop( 'disabled',false );

								gdpr_notify_msg.error( gdpr_settings_error_message );

							}
						}
					);
				}
			);

			function gdpr_add_cookie_form() {
				$( '.form-table.add-cookie' ).parent().find( '.gdpr_postbar' ).hide();
				$( '.form-table.add-cookie' ).show();
				$( '.form-table.add-cookie' ).find( 'input[type="hidden"]' ).remove();
				$( '.form-table.add-cookie' ).append( '<input type="hidden" name="gdpr_addcookie" value="1">' );
			}
			function gdpr_hide_cookie_form() {
				$( '.form-table.add-cookie' ).find( 'input' ).val( '' );
				$( '.form-table.add-cookie' ).find( 'select[name="cookie_type_field"]' ).val( 'HTTP' );
				$( '.form-table.add-cookie' ).find( 'textarea' ).val( '' );
				$( '.form-table.add-cookie' ).find( 'input[type="hidden"]' ).remove();
				$( '.form-table.add-cookie' ).parent().find( '.gdpr_postbar' ).show();
				$( '.form-table.add-cookie' ).hide();
			}
			function gdpr_scroll_accept_er()
			{
				if ($( '[name="cookie_bar_as_field"] option:selected' ).val() == 'popup' && $( '[name="popup_overlay_field"]:checked' ).val() == 'true' && $( '[name="scroll_close_field"]:checked' ).val() == 'true') {
					$( '.gdpr_scroll_accept_er' ).show();
				} else {
					$( '.gdpr_scroll_accept_er' ).hide();
				}
			}
			gdpr_scroll_accept_er();
			$( '[name="cookie_bar_as_field"]' ).change(
				function(){
					gdpr_scroll_accept_er();
				}
			);
			$( '[name="popup_overlay_field"], [name="scroll_close_field"]' ).click(
				function(){
					gdpr_scroll_accept_er();
				}
			);

			function gdpr_bar_active_msg()
			{
				$( '.gdpr_bar_state tr' ).hide();
				if ($( 'input[type="radio"].gdpr_bar_on' ).is( ':checked' )) {
					$( '.gdpr_bar_state tr.gdpr_bar_on' ).show();
				} else {
					$( '.gdpr_bar_state tr.gdpr_bar_off' ).show();
				}
			}
			var gdpr_tab_form_toggler = {
				set:function() {
					$( 'select.gdpr_tab_form_toggle' ).each(
						function(){
							gdpr_tab_form_toggler.toggle( $( this ) );
						}
					);
					$( 'select.gdpr_tab_form_toggle' ).change(
						function(){
							gdpr_tab_form_toggler.toggle( $( this ) );
						}
					);
				},
				toggle:function(elm) {
					var vl            = elm.val();
					var gdpr_tab_head = $( '.gdpr-cookie-consent-tab-head' );
					if ( vl == 'gdpr' || vl == 'both') {
						gdpr_tab_head.find( "a[href='#gdpr-cookie-consent-cookie-list']" ).show();
						gdpr_tab_head.find( "a[href='#gdpr-cookie-consent-script-blocker']" ).show();
					} else if ( vl == 'ccpa' ) {
						gdpr_tab_head.find( "a[href='#gdpr-cookie-consent-cookie-list']" ).hide();
						gdpr_tab_head.find( "a[href='#gdpr-cookie-consent-script-blocker']" ).hide();
					}
					var trgt = elm.attr( 'gdpr_tab_frm_tgl-target' );
					$( '[gdpr_tab_frm_tgl-id="' + trgt + '"]' ).hide();
					var target_ids = $( '[gdpr_tab_frm_tgl-id="' + trgt + '"]' ).filter(
						function(){
							return $( '[gdpr_tab_frm_tgl-id="' + trgt + '"]' );
						}
					);
					target_ids.each(
						function(){
							var target_id  = $( this ).attr( 'data-id' );
							var target_elm = $( "li[data-target='" + target_id + "']" );
							if (target_id == 'design-template') {
								$( "li[data-target='design-general']" ).trigger( 'click' );
							}
							target_elm.hide();
						}
					);

					var selcted_trget = $( '[gdpr_tab_frm_tgl-id="' + trgt + '"]' ).filter(
						function(){
							if (vl == 'both') {
								return $( this ).attr( 'gdpr_tab_frm_tgl-val' ) == 'gdpr' || $( this ).attr( 'gdpr_tab_frm_tgl-val' ) == 'ccpa';
							} else {
								return $( this ).attr( 'gdpr_tab_frm_tgl-val' ) == vl;
							}
						}
					);
					selcted_trget.each(
						function(){
							var target_id  = $( this ).attr( 'data-id' );
							var target_elm = $( "li[data-target='" + target_id + "']" );
							target_elm.show();
							if ( ( vl == 'both' && target_id == 'accept-button' ) || ( ( vl == 'gdpr' && target_id == 'accept-button' ) ) || ( vl == 'ccpa' && target_id == 'confirm-button' ) ) {
								target_elm.css( 'border-left','none' );
								target_elm.css( 'padding-left','0px' );
								target_elm.trigger( 'click' );
							} else {
								target_elm.css( 'border-left', 'solid 1px #ccc' );
							}
							// $(this).show();
						}
					);
				}
			}
			gdpr_tab_form_toggler.set();
			var gdpr_form_toggler     =
			{
				set:function()
				{
					$( '.gdpr-template-type' ).hide();
					var template = $( 'input[name="gdpr_template"]' ).val();
					$( '.gdpr-template-type.' + template ).show();
					$( 'select[name="banner_template_field"]' ).change(
						function() {
							var vl = $( this ).val();
							$( 'input[name="gdpr_template_updated"]' ).val( '1' );
							$( '.gdpr-template-type' ).hide();
							$( '.gdpr-template-type.' + vl ).show();
						}
					)
					$( 'select[name="popup_template_field"]' ).change(
						function() {
							var vl = $( this ).val();
							$( 'input[name="gdpr_template_updated"]' ).val( '1' );
							$( '.gdpr-template-type' ).hide();
							$( '.gdpr-template-type.' + vl ).show();
						}
					)
					$( 'select[name="widget_template_field"]' ).change(
						function() {
							var vl = $( this ).val();
							$( 'input[name="gdpr_template_updated"]' ).val( '1' );
							$( '.gdpr-template-type' ).hide();
							$( '.gdpr-template-type.' + vl ).show();
						}
					)
					$( 'select.gdpr_form_toggle' ).each(
						function(){
							gdpr_form_toggler.toggle( $( this ) );
						}
					);
					$( 'input[type="radio"].gdpr_form_toggle' ).each(
						function(){
							if ($( this ).is( ':checked' )) {
								gdpr_form_toggler.toggle( $( this ) );
							}
						}
					);
					$( 'select.gdpr_form_toggle' ).change(
						function(){
							gdpr_form_toggler.toggle( $( this ) );
						}
					);
					$( 'input[type="radio"].gdpr_form_toggle' ).click(
						function(){
							if ($( this ).is( ':checked' )) {
								gdpr_form_toggler.toggle( $( this ) );
							}
						}
					);
				},
				toggle:function(elm)
				{
					var vl       = elm.val();
					var gdpr_val = $( '[gdpr_frm_tgl-val="' + vl + '"] select[name="' + vl + '_template_field"]' ).val();
					if (vl == 'banner') {
						$( '.gdpr-template-type' ).hide();
						$( '.gdpr-template-type.' + gdpr_val ).show();
					}
					if (vl == 'popup') {
						$( '.gdpr-template-type' ).hide();
						$( '.gdpr-template-type.' + gdpr_val ).show();
					}
					if (vl == 'widget') {
						$( '.gdpr-template-type' ).hide();
						$( '.gdpr-template-type.' + gdpr_val ).show();
					}
					var trgt = elm.attr( 'gdpr_frm_tgl-target' );
					$( '[gdpr_frm_tgl-id="' + trgt + '"]' ).hide();
					var selcted_trget = $( '[gdpr_frm_tgl-id="' + trgt + '"]' ).filter(
						function(){
							if (vl == 'both') {
								return $( this ).attr( 'gdpr_frm_tgl-val' ) == 'gdpr' || $( this ).attr( 'gdpr_frm_tgl-val' ) == 'ccpa';
							} else {
								return $( this ).attr( 'gdpr_frm_tgl-val' ) == vl;
							}
						}
					);
					selcted_trget.show();
				}
			}

			gdpr_form_toggler.set();

			$( document ).on(
				'change',
				'input[name="is_eu_on_field"]',
				function(){
					if (this.value == 'true') {
						$( '.gdpr-maxmind-notice' ).show();
					} else {
						if ($( 'input[name="is_ccpa_on_field"]:checked' ).val() == 'false') {
							$( '.gdpr-maxmind-notice' ).hide();
						}
					}
				}
			);
			$( document ).on(
				'change',
				'input[name="is_ccpa_on_field"]',
				function(){
					if (this.value == 'true') {
						$( '.gdpr-maxmind-notice' ).show();
					} else {
						if ($( 'input[name="is_eu_on_field"]:checked' ).val() == 'false') {
							$( '.gdpr-maxmind-notice' ).hide();
						}
					}
				}
			);

		}
	);

})( jQuery );
var gdpr_notify_msg =
	{
		error:function(message)
		{
			var er_elm = jQuery( '<div class="notify_msg" style="background:#d9534f; border:solid 1px #dd431c;">' + message + '</div>' );
			this.setNotify( er_elm );
		},
		success:function(message)
		{
			var suss_elm = jQuery( '<div class="notify_msg" style="background:#5cb85c; border:solid 1px #2bcc1c;">' + message + '</div>' );
			this.setNotify( suss_elm );
		},
		setNotify:function(elm)
		{
			jQuery( 'body' ).append( elm );
			elm.stop( true,true ).animate( {'opacity':1,'top':'50px'},1000 );
			setTimeout(
				function(){
					elm.animate(
						{'opacity':0,'top':'100px'},
						1000,
						function(){
							elm.remove();
						}
					);
				},
				3000
			);
		}
	}
	function gdpr_store_settings_btn_click(vl)
	{
		document.getElementById( 'gdpr_update_action' ).value = vl;
	}
	function gdpr_print_value(src, trgt)
	{
		var source   = document.getElementById( src );
		var target   = document.getElementById( trgt );
		target.value = source.value;
	}
