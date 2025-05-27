<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://club.wpeka.com
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 */

if ( 'popup' === $the_options['cookie_bar_as'] ) {
	?>
	<div class="gdprmodal gdprfade" id="gdpr-<?php echo esc_html( $the_options['cookie_bar_as'] ); ?>" role="dialog" data-keyboard="false" data-backdrop="<?php echo esc_html( $the_options['backdrop'] ); ?>">
        <div class="gdprmodal-dialog gdprmodal-dialog-centered">
            <!-- Modal content-->
            <div class="gdprmodal-content">
            </div>
        </div>
    </div>
<?php } 
?>








<!-- cookie notice-->
 <?php 
	function hex_to_rgba($hex, $opacity = 1) {
		$hex = str_replace('#', '', $hex);
		if (strlen($hex) === 3) {
			$r = hexdec(str_repeat(substr($hex, 0, 1), 2));
			$g = hexdec(str_repeat(substr($hex, 1, 1), 2));
			$b = hexdec(str_repeat(substr($hex, 2, 1), 2));
		} elseif (strlen($hex) === 6) {
			$r = hexdec(substr($hex, 0, 2));
			$g = hexdec(substr($hex, 2, 2));
			$b = hexdec(substr($hex, 4, 2));
		} else {
			return 'rgba(0,0,0,1)'; // fallback
		}

		return "rgba($r, $g, $b, $opacity)";
	}
//styling for banner
	$notice_container_styles = "position: fixed; display: flex; flex-direction: column; gap: 15px; border-radius: {$the_options['background_border_radius']}px;";

	if ( $the_options['cookie_bar_as'] === 'banner' ) { $notice_container_styles .= "left: 0px; {$the_options['notify_position_vertical']}: 0px;"; } 
	elseif ( $the_options['cookie_bar_as'] === 'popup' ) { 
		$notice_container_styles .= "top:50%; left: 50%; transform: translateX(-50%) translateY(-50%);";
	} 
	else { switch ( $the_options['notify_position_horizontal'] ) { 
			case 'left': $notice_container_styles .= "left: 15px; bottom: 15px;"; break;
			case 'right': $notice_container_styles .= "right: 15px; bottom: 15px;"; break;
			case 'top_left': $notice_container_styles .= "left: 15px; top: 15px;"; break;
			case 'top_right': $notice_container_styles .= "right: 15px; top: 15px;"; break;
		}
		$notice_container_styles .= " box-shadow: 2px 5px 11px 4px #dddddd;";
	}
	$notice_container_styles .= "background: " . hex_to_rgba($the_options['background'], $the_options['opacity']) . ";"; 
	$notice_container_styles .= "color: " . $the_options["text"] . ";"; 

	$logo_style_attr = '';
	foreach ($template_object['logo'] as $key => $value) {
		$logo_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
	} 

	$heading_style_attr = "";
	foreach ($template_object['heading'] as $key => $value) {
		$heading_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
	}  

	$readmore_style_attr .= ($the_options['button_readmore_as_button'] === 'true' ? "background: {$the_options['button_readmore_button_color']};" : "" ) . " color: {$the_options["button_readmore_link_color"]};";
	if ($the_options['button_readmore_as_button'] === 'true' || $the_options['button_readmore_as_button'] === true || $the_options['button_readmore_as_button'] === 1) {
		$padding_key = 'button_' . $the_options['button_readmore_button_size'] . '_padding';
		$padding_value = $template_object['static-settings'][$padding_key];
		$readmore_style_attr .= "display: block; width:fit-content; margin-top: 5px;";
		$readmore_style_attr .= "border-style: {$the_options['button_readmore_button_border_style']};";
		$readmore_style_attr .= "border-color: {$the_options['button_readmore_button_border_color']};";
		$readmore_style_attr .= "border-width: {$the_options['button_readmore_button_border_width']}px;";
		$readmore_style_attr .= "border-radius: {$the_options['button_readmore_button_border_radius']}px;";
		$readmore_style_attr .= "padding: {$padding_value};";
	}
	$opt_out_style_attr = "color: {$the_options['button_donotsell_link_color']}";
?>

<div id="<?php echo esc_html( $the_options['container_id'] ); ?>" class="<?php echo esc_html( $the_options['container_class'] ); ?> <?php echo esc_html( $the_options['theme_class'] ); ?>"  style="<?php echo esc_attr($notice_container_styles); ?>">	
	<span id="cookie-banner-cancle-img" style="cursor: pointer; display: inline-flex; align-items: center; justify-content: center; position: absolute; top:20px; right: 20px; height: 20px; width: 20px; border-radius: 50%; background-color: <?php echo esc_html($the_options['button_accept_button_color']);?>; color: <?php echo esc_html($the_options['button_accept_link_color']);?>;">
		<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
			<path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z" fill="currentColor"/>
		</svg>
	</span>
	<?php
	if($ab_options['ab_testing_enabled'] === "false" || $ab_options['ab_testing_enabled'] === false){
		if($the_options['cookie_usage_for'] == 'both'){
			$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELDML1 );
			if (!empty($get_banner_img)) {
				?>
					<img style = "<?php echo esc_attr($logo_style_attr); ?>" class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
					<?php
			}
		}else{
			$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
			if (!empty($get_banner_img)) {
				?>
					<img style = "<?php echo esc_attr($logo_style_attr); ?>" class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
					<?php
			}
		}
	}
	else{
		if($ab_options['ab_testing_enabled'] === "true" || $ab_options['ab_testing_enabled'] === true){
			if($chosenBanner == 1) {
				$get_banner_img1 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1 );
				if (!empty($get_banner_img1)) {
					?>
						<img style = "<?php echo esc_attr($logo_style_attr); ?>" class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img1 ); ?>" >
						<?php
				}
			}elseif($chosenBanner == 2){
					$get_banner_img2 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD2 );
					if (!empty($get_banner_img2)) {
					?>
						<img style = "<?php echo esc_attr($logo_style_attr); ?>" class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img2 ); ?>" >
						<?php
				}
			}
		}
	} ?>
	<?php
	if ( ($the_options['cookie_usage_for'] === 'gdpr' || $the_options['cookie_usage_for'] === 'both' ) && strlen($the_options['bar_heading_text']) > 0) : ?>
		<h3 style = "<?php echo esc_attr($heading_style_attr); ?>" ><?php echo esc_html($the_options['bar_heading_text']); ?></h3>
	<?php elseif (( $the_options['cookie_usage_for'] === 'lgpd' ) && strlen($the_options['bar_heading_lgpd_text']) > 0) : ?>
		<h3 style = "<?php echo esc_attr($heading_style_attr); ?>" ><?php echo esc_html($the_options['bar_heading_lgpd_text']); ?></h3>
	<?php endif; ?>

	<div class="<?php echo esc_attr($template_object['static-settings']['layout']);?>">
		<p>
			<?php if ( $the_options['cookie_usage_for'] === 'gdpr') : ?>
				<span><?php echo $the_options['is_iabtcf_on'] ? $cookie_data['dash_notify_message_iabtcf']: strip_tags(__( $cookie_data['dash_notify_message']), '<a><br><em><strong><span><p><i><img><b><div><label>' ); ?></span>
				<?php elseif ( $the_options['cookie_usage_for'] === 'lgpd' ) : ?>
				<span><?php echo strip_tags(__(  $cookie_data['dash_notify_message_lgpd']), '<a><br><em><strong><span><p><i><img><b><div><label>' );?></span>
				<?php elseif ( $the_options['cookie_usage_for'] === 'ccpa' ) : ?>
				<span><?php echo strip_tags(__(  $cookie_data['dash_notify_message_ccpa']), '<a><br><em><strong><span><p><i><img><b><div><label>' );?></span>
				<?php elseif ( $the_options['cookie_usage_for'] === 'eprivacy' ) : ?>
				<span><?php echo strip_tags(__(  $cookie_data['dash_notify_message_eprivacy']), '<a><br><em><strong><span><p><i><img><b><div><label>' );?></span>
			<?php endif; ?>
			<?php if ( $the_options['cookie_usage_for'] === 'ccpa' &&  ! empty( $the_options['button_donotsell_is_on'] ) ) : ?>
				<a style="<?php echo esc_attr($opt_out_style_attr); ?>" data-toggle="gdprmodal" href="#" class="<?php echo esc_html( $the_options['button_donotsell_classes'] ); ?>" data-gdpr_action="donotsell" id="cookie_donotsell_link"
				>	
					<?php echo esc_html__($cookie_data['dash_button_donotsell_text'], 'gdpr-cookie-consent' ); ?>
				</a>
					
			<?php elseif( $the_options['cookie_usage_for'] !== 'ccpa' &&  ! empty( $the_options['button_readmore_is_on'] ) ) : ?>
				<a style="<?php echo esc_attr($readmore_style_attr); ?>" id="cookie_action_link" href="<?php echo esc_html( $the_options['button_readmore_url_link'] ); ?>" 
				<?php if ( ! empty( $the_options['button_readmore_new_win'] ) ) { ?>
					target="_blank"
				<?php } ?>
				>
					<?php echo esc_html__( $cookie_data['dash_button_readmore_text'], 'gdpr-cookie-consent' ); ?>
				</a>
			<?php endif; ?>
		</p>
		<?php if ( $the_options['cookie_usage_for'] !== 'ccpa' ) : ?>
			<div class="cookie_notice_buttons <?php echo esc_attr($template_object['static-settings']['layout']) . '-buttons';?>">
				<div class="left_buttons">
					<?php if($template_object["decline_button"]["is_on"]) : ?><a style="<?php echo esc_attr( $decline_style_attr ); ?>">{{ decline_text }}</a><?php endif;?>
					<?php if($template_object["settings_button"]["is_on"]) : ?><a style="<?php echo esc_attr( $settings_style_attr ); ?>">{{ settings_text }}</a><?php endif;?>
				</div>
				<div class="right_buttons">
					<?php if($template_object["accept_button"]["is_on"]) : ?><a style="<?php echo esc_attr( $accept_style_attr ); ?>">{{ accept_text }}</a><?php endif;?>
					<?php if($template_object["accept_all_button"]["is_on"]) : ?><a style="<?php echo esc_attr( $accept_all_style_attr); ?>">{{ accept_all_text }}</a><?php endif;?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>












<?php
if ( ! empty( $the_options['lgpd_notify'] )) {
	if ( ! empty( $the_options['cookie_data'] ) ) {
		if ( 'banner' === $the_options['cookie_bar_as'] && ! $the_options['button_settings_as_popup'] ) {//phpcs:ignore
		} else {
			?>
			<div class="gdpr_messagebar_detail <?php echo esc_html( $the_options['button_settings_layout_skin'] ); ?> <?php echo esc_html( $the_options['template_parts'] ); ?> <?php echo esc_html( $the_options['theme_class'] ); ?>">
			<?php include plugin_dir_path( __FILE__ ) . $the_options['layout_skin_template']; ?>
		</div>
			<?php
		}
	}
	if ( ! empty( $the_options['show_again'] ) ) {
		?>
		<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" style="position: fixed; bottom: 10px; color: <?php echo esc_html($the_options['button_revoke_consent_text_color']); ?>; background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color']); ?>; <?php if($the_options['show_again_position'] === 'right') echo "right: ". esc_html($the_options['show_again_margin']) . "%;"; else echo "left: ". esc_html($the_options['show_again_margin']) . "%;"; ?> border-radius: 5px; box-shadow: 0px 6px 11px gray;">
		<span><?php echo esc_html__( $cookie_data['dash_show_again_text'], 'gdpr-cookie-consent' ); //phpcs:ignore ?></span>
	</div>
		<?php
	}
}
if ( ! empty( $the_options['gdpr_notify'] )) {
	if ( ! empty( $the_options['cookie_data'] ) ) {
		if ( 'banner' === $the_options['cookie_bar_as'] && ! $the_options['button_settings_as_popup'] ) {//phpcs:ignore
		} else {
			?>
			<div class="gdpr_messagebar_detail layout-classic <?php echo esc_html( $the_options['template_parts'] ); ?> <?php echo esc_html( $the_options['theme_class'] ); ?>">
			<?php include plugin_dir_path( __FILE__ ) . 'modals/cookie_settings.php'; ?>
		</div>
			<?php
		}
	}
	if ( ! empty( $the_options['show_again'] ) ) {
		?>
		<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" style="position: fixed; bottom: 10px; color: <?php echo esc_html($the_options['button_revoke_consent_text_color']); ?>; background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color']); ?>; <?php if($the_options['show_again_position'] === 'right') echo "right: ". esc_html($the_options['show_again_margin']) . "%;"; else echo "left: ". esc_html($the_options['show_again_margin']) . "%;"; ?> border-radius: 5px; box-shadow: 0px 6px 11px gray;">
		<span><?php echo esc_html__( $cookie_data['dash_show_again_text'], 'gdpr-cookie-consent' ); //phpcs:ignore ?></span>
	</div>
		<?php
	}
}
if ( ! empty( $the_options['eprivacy_notify'] ) ) {
	if ( ! empty( $the_options['show_again'] ) ) {
		?>
		<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" style="position: fixed; bottom: 10px; color: <?php echo esc_html($the_options['button_revoke_consent_text_color']); ?>; background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color']); ?>; <?php if($the_options['show_again_position'] === 'right') echo "right: ". esc_html($the_options['show_again_margin']) . "%;"; else echo "left: ". esc_html($the_options['show_again_margin']) . "%;"; ?> border-radius: 5px; box-shadow: 0px 6px 11px gray;">
			<span><?php echo esc_html__( $cookie_data['dash_show_again_text'], 'gdpr-cookie-consent' );//phpcs:ignore ?></span>
		</div>
		<?php
	}
}

if ( ! empty( $the_options['ccpa_notify'] ) ) {
	?>
	<div class="ccpa_messagebar_detail layout-classic <?php echo esc_html( $the_options['template_parts'] ); ?>">
		<?php include plugin_dir_path( __FILE__ ) . 'modals/ccpa.php'; ?>
	</div>
	<?php
}