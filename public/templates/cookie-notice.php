<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://club.wpeka.com
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


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
	if(!function_exists('hex_to_rgba')){
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
	}
	//temporary
	// $the_options = array();
	// $chosenBanner = 1;
	// $template_object = array();
	// $cookie_data = array();
	// $ab_options = array();
	
//styling for banner

	$ab_testing_enabled = (!isset($ab_options['ab_testing_enabled']) || ($ab_options['ab_testing_enabled'] === "false" || $ab_options['ab_testing_enabled'] === false)) ? "false" : "true";

	$notice_container_styles = "position: fixed; display: none; flex-direction: column; border-radius: {$the_options[($ab_testing_enabled === "true" ? 'cookie_bar_border_radius' . $chosenBanner :  'background_border_radius')]}px;";
	if ( $the_options['cookie_bar_as'] === 'banner' ) { 
		$notice_container_styles .= "left: 15px; {$the_options['notify_position_vertical']}: 15px;"; 
		$notice_container_styles .= "padding-inline: " . ($ab_testing_enabled === "true" ? $the_options['cookie_bar_horizontal_padding' . $chosenBanner] : $the_options["cookie_bar_horizontal_padding"]) . "px;";
		$notice_container_styles .= "padding-block: " . ($ab_testing_enabled === "true" ? $the_options['cookie_bar_vertical_padding' . $chosenBanner] : $the_options["cookie_bar_vertical_padding"]) . "px;";
	} 
	elseif ( $the_options['cookie_bar_as'] === 'popup' ) { 
		$notice_container_styles .= "top:50%; left: 50%; transform: translateX(-50%) translateY(-50%);";
		$notice_container_styles .= "padding: " . ($ab_testing_enabled === "true" ? $the_options['cookie_bar_padding' . $chosenBanner] : $the_options["cookie_bar_padding"]) . "px;";
	} 
	else { switch ( $the_options['notify_position_horizontal'] ) { 
			case 'left': $notice_container_styles .= "left: 15px; bottom: 15px;"; break;
			case 'right': $notice_container_styles .= "right: 15px; bottom: 15px;"; break;
			case 'top_left': $notice_container_styles .= "left: 15px; top: 15px;"; break;
			case 'top_right': $notice_container_styles .= "right: 15px; top: 15px;"; break;
		}
		$notice_container_styles .= "padding: " . ($ab_testing_enabled === "true" ? $the_options['cookie_bar_padding' . $chosenBanner] : $the_options["cookie_bar_padding"]) . "px;";
	}
	$notice_container_styles .= "background: " . hex_to_rgba($ab_testing_enabled === "true" ? $the_options['cookie_bar_color' . $chosenBanner] : $the_options['background'], $ab_testing_enabled === 'true' ? $the_options['cookie_bar_opacity' . $chosenBanner] : $the_options['opacity']) . ";"; 
	$notice_container_styles .= "border-style: " . ($ab_testing_enabled === "true" ? $the_options['border_style' . $chosenBanner] : $the_options["background_border_style"]) . ";"; 
	$notice_container_styles .= "border-color: " . ($ab_testing_enabled === "true" ? $the_options['cookie_border_color' . $chosenBanner] : $the_options["background_border_color"]) . ";"; 
	$notice_container_styles .= "border-width: " . ($ab_testing_enabled === "true" ? $the_options['cookie_bar_border_width' . $chosenBanner] : $the_options["background_border_width"]) . "px;"; 
	$notice_container_styles .= "font-family: " . ($ab_testing_enabled === "true" ? $the_options['cookie_font' . $chosenBanner] : $the_options["font_family"]) . ";"; 
	$notice_container_styles .= "gap: " . ($ab_testing_enabled === "true" ? $the_options['cookie_bar_spacing' . $chosenBanner] : $the_options["cookie_bar_spacing"]) . "px;";
	$notice_container_styles .= "backdrop-filter: blur(" . ($ab_testing_enabled === "true" ? $the_options['cookie_bar_blur' . $chosenBanner] : $the_options["cookie_bar_blur"])*20 . "px);";
	$notice_container_styles .= "box-shadow: " . ($ab_testing_enabled === "true" ? $the_options['cookie_bar_shadow_size' . $chosenBanner] : $the_options["cookie_bar_shadow_size"]) . "px " . ($ab_testing_enabled === "true" ? $the_options['cookie_bar_shadow_size' . $chosenBanner] : $the_options["cookie_bar_shadow_size"]) . "px " . ($ab_testing_enabled === "true" ? $the_options['cookie_bar_shadow_size' . $chosenBanner] * 2 : $the_options["cookie_bar_shadow_size"] * 2) . "px " . hex_to_rgba($ab_testing_enabled === "true" ? $the_options['cookie_bar_shadow_color' . $chosenBanner] : $the_options["cookie_bar_shadow_color"], 0.5) . ";";
	
	$content_law = isset( $content_law ) ? $content_law : $the_options['cookie_usage_for'];

	$suffix =  ($ab_testing_enabled === "true" ? $chosenBanner : '');
	$banner_layouts = json_decode($the_options['banner_layouts'], true);
	$banner_structure = json_decode($the_options['banner_structure'], true);
	if ( ! is_array( $banner_structure ) ) {
		$banner_structure = array();
	}

	$c5_buttons = isset( $banner_structure['c5'] ) && is_array( $banner_structure['c5'] )
		? $banner_structure['c5']
		: array( 'accept_all', 'accept', 'settings' );

	$c6_buttons = isset( $banner_structure['c6'] ) && is_array( $banner_structure['c6'] )
		? $banner_structure['c6']
		: array( 'decline' );

	$is_button_visible = function( $item ) use ( $the_options, $suffix ) {
		switch ( $item ) {
			case 'acceptAll':
			case 'accept_all':
				return filter_var( $the_options['button_accept_all_is_on' . $suffix], FILTER_VALIDATE_BOOLEAN );

			case 'accept':
				return filter_var( $the_options['button_accept_is_on' . $suffix], FILTER_VALIDATE_BOOLEAN );

			case 'decline':
				return ! in_array( $the_options['cookie_usage_for'], array( 'pipeda', 'au_app' ), true )
					&& filter_var( $the_options['button_decline_is_on' . $suffix], FILTER_VALIDATE_BOOLEAN );

			case 'settings':
				return $the_options['cookie_usage_for'] !== 'eprivacy'
					&& filter_var( $the_options['button_settings_is_on' . $suffix], FILTER_VALIDATE_BOOLEAN );

			default:
				return false;
		}
	};

	$visible_c5_items = array_values( array_filter( $c5_buttons, $is_button_visible ) );
	$visible_c6_items = array_values( array_filter( $c6_buttons, $is_button_visible ) );
	$opt_out_style_attr = "color: {$the_options['button_donotsell_link_color' . $suffix]}";

	$logo_style_attr = '';
	foreach ($template_object['logo'] as $key => $value) {
		if($key != 'src') $logo_style_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
	} 

	$heading_style_attr = "";
	$heading_style_attr .= "color: {$the_options['cookie_heading_color' . $suffix]};";
	$heading_style_attr .= "font-size: {$the_options['heading_text_size' . $suffix]}px;";
	$heading_style_attr .= "font-weight: {$the_options['heading_text_weight' . $suffix]};";

	$text_style_attr = "";
	$text_style_attr .= "color: {$the_options['cookie_text_color' . $suffix]};";
	$text_style_attr .= "font-size: {$the_options['cookie_font_size' . $suffix]}px;";
	$text_style_attr .= "font-weight: {$the_options['cookie_text_weight' . $suffix]};";
	$text_style_attr .= "text-align: {$the_options['banner_text_alignment' . $suffix]};";


	$readmore_style_attr = "";
	$readmore_style_attr .= " color: {$the_options['button_readmore_link_color' . $suffix]};";
	if (
		$template_object['name'] === 'blue_full' ||
		$template_object['name'] === 'blue_center' ||
		$template_object['name'] === 'blue_center_column' ||
		$template_object['name'] === 'blue_split' ||
		$template_object['name'] === 'gray' ||
		$template_object['name'] === 'bold' ||
		$template_object['name'] === 'dark'
	) {
		$readmore_style_attr .= "text-decoration: underline;";
	} else {
		$readmore_style_attr .= "text-decoration: none;";
	}
	$readmore_style_attr .= "display: inline-block;";
	
	

	$accept_style_attr = "";
	$accept_style_attr .=  " color: {$the_options["button_accept_link_color" . $suffix]};";
	if ( ! empty( $the_options['button_accept_as_button' . $suffix] ) && filter_var($the_options['button_accept_as_button' . $suffix], FILTER_VALIDATE_BOOLEAN)) {
		$accept_style_attr .= "border-style: {$the_options['button_accept_button_border_style' . $suffix]};";
		$accept_style_attr .= "border-color: {$the_options['button_accept_button_border_color' . $suffix]};";
		$accept_style_attr .= "border-width: {$the_options['button_accept_button_border_width' . $suffix]}px;";
		$accept_style_attr .= "border-radius: {$the_options['button_accept_button_border_radius' . $suffix]}px;";
		$accept_style_attr .= "padding-inline: 16px; padding-block: 8px;";
		$rgba_color = hex_to_rgba($the_options['button_accept_button_color' . $suffix], $the_options['button_accept_button_opacity' . $suffix]);
		$accept_style_attr .= "background: {$rgba_color};";
	}
	$accept_style_attr .= "font-size: {$the_options['button_font_size' . $suffix]}px;";
	$accept_style_attr .= "font-weight: {$the_options['button_text_weight' . $suffix]};";
	$button_accept_button_width = $the_options['button_accept_button_width' . $suffix] === 'full' ? '100%' : 'fit-content';
	$accept_style_attr .= "width: {$button_accept_button_width};";
	$accept_style_attr .= "min-width: {$the_options['button_accept_button_min_width' . $suffix]}px;";


	$accept_all_style_attr = "";
	$accept_all_style_attr .=  " color: {$the_options["button_accept_all_link_color" . $suffix]};";

	if ( ! empty( $the_options['button_accept_all_as_button' . $suffix] ) && filter_var($the_options['button_accept_all_as_button' . $suffix], FILTER_VALIDATE_BOOLEAN)) {
		$accept_all_style_attr .= "border-style: {$the_options['button_accept_all_btn_border_style' . $suffix]};";
		$accept_all_style_attr .= "border-color: {$the_options['button_accept_all_btn_border_color' . $suffix]};";
		$accept_all_style_attr .= "border-width: {$the_options['button_accept_all_btn_border_width' . $suffix]}px;";
		$accept_all_style_attr .= "border-radius: {$the_options['button_accept_all_btn_border_radius' . $suffix]}px;";
		$accept_all_style_attr .= "padding-inline: 16px; padding-block: 8px;";
		$rgba_color = hex_to_rgba($the_options['button_accept_all_button_color' . $suffix], $the_options['button_accept_all_btn_opacity' . $suffix]);
		$accept_all_style_attr .= "background: {$rgba_color};";
	}
	$accept_all_style_attr .= "font-size: {$the_options['button_font_size' . $suffix]}px;";
	$accept_all_style_attr .= "font-weight: {$the_options['button_text_weight' . $suffix]};";
	$button_accept_all_btn_width = $the_options['button_accept_all_btn_width' . $suffix] === 'full' ? '100%' : 'fit-content';
	$accept_all_style_attr .= "width: {$button_accept_all_btn_width};";
	$accept_all_style_attr .= "min-width: {$the_options['button_accept_all_btn_min_width' . $suffix]}px;";

	$vendor_link_color = ( ! empty( $the_options['button_accept_all_as_button' . $suffix] ) && filter_var($the_options['button_accept_all_as_button' . $suffix], FILTER_VALIDATE_BOOLEAN)) ? $the_options['button_accept_all_button_color' . $suffix] : $the_options["button_accept_all_link_color" . $suffix];

	$settings_style_attr ="";
	$settings_style_attr .=  " color: {$the_options["button_settings_link_color" . $suffix]};";
	if ( ! empty( $the_options['button_settings_as_button' . $suffix] ) && filter_var($the_options['button_settings_as_button' . $suffix], FILTER_VALIDATE_BOOLEAN)) {
		$settings_style_attr .= "border-style: {$the_options['button_settings_button_border_style' . $suffix]};";
		$settings_style_attr .= "border-color: {$the_options['button_settings_button_border_color' . $suffix]};";
		$settings_style_attr .= "border-width: {$the_options['button_settings_button_border_width' . $suffix]}px;";
		$settings_style_attr .= "border-radius: {$the_options['button_settings_button_border_radius' . $suffix]}px;";
		$settings_style_attr .= "padding-inline: 16px; padding-block: 8px;";
		$rgba_color = hex_to_rgba($the_options['button_settings_button_color' . $suffix], $the_options['button_settings_button_opacity' . $suffix]);
		$settings_style_attr .= "background: {$rgba_color};";
	}
	$settings_style_attr .= "font-size: {$the_options['button_font_size' . $suffix]}px;";
	$settings_style_attr .= "font-weight: {$the_options['button_text_weight' . $suffix]};";
	$button_settings_button_width = $the_options['button_settings_button_width' . $suffix] === 'full' ? '100%' : 'fit-content';
	$settings_style_attr .= "width: {$button_settings_button_width};";
	$settings_style_attr .= "min-width: {$the_options['button_settings_button_min_width' . $suffix]}px;";


	$decline_style_attr ="";
	$decline_style_attr .=  " color: {$the_options["button_decline_link_color" . $suffix]};";
	if ( ! empty( $the_options['button_decline_as_button' . $suffix] ) && filter_var($the_options['button_decline_as_button' . $suffix], FILTER_VALIDATE_BOOLEAN)) {
		$decline_style_attr .= "border-style: {$the_options['button_decline_button_border_style' . $suffix]};";
		$decline_style_attr .= "border-color: {$the_options['button_decline_button_border_color' . $suffix]};";
		$decline_style_attr .= "border-width: {$the_options['button_decline_button_border_width' . $suffix]}px;";
		$decline_style_attr .= "border-radius: {$the_options['button_decline_button_border_radius' . $suffix]}px;";
		$decline_style_attr .= "padding-inline: 16px; padding-block: 8px;";
		$rgba_color = hex_to_rgba($the_options['button_decline_button_color' . $suffix], $the_options['button_decline_button_opacity' . $suffix]);
		$decline_style_attr .= "background: {$rgba_color};";
	}
	$decline_style_attr .= "font-size: {$the_options['button_font_size' . $suffix]}px;";
	$decline_style_attr .= "font-weight: {$the_options['button_text_weight' . $suffix]};";
	$button_decline_button_width = $the_options['button_decline_button_width' . $suffix] === 'full' ? '100%' : 'fit-content';
	$decline_style_attr .= "width: {$button_decline_button_width};";
	$decline_style_attr .= "min-width: {$the_options['button_decline_button_min_width' . $suffix]}px;";

	$badging_color = $the_options['button_accept_all_button_color' . $suffix] === ($ab_testing_enabled === "true" ? $the_options['cookie_bar_color' . $chosenBanner] : $the_options['background']) ? $template_object['accept_all_button']['background-color'] ?? '' : $the_options['button_accept_all_button_color' . $suffix];
	$decoration_styles_attr = '';
	if(isset($template_object['decoration'])) foreach ($template_object['decoration'] as $key => $value) {
		$decoration_styles_attr .= esc_attr($key) . ':' . esc_attr($value) . ';';
	} 
	$render_cookie_button = function( $button ) use ( $the_options, $suffix, $cookie_data, $decline_style_attr, $settings_style_attr, $accept_style_attr, $accept_all_style_attr ) {
		switch ( $button ) {
			case 'decline':
				if ( ! in_array( $the_options['cookie_usage_for'], array( 'pipeda', 'au_app' ), true )
					&& filter_var( $the_options['button_decline_is_on' . $suffix] ?? false, FILTER_VALIDATE_BOOLEAN ) ) : ?>
					<a id="cookie_action_reject" class="<?php echo esc_html( $the_options['button_decline_classes'] ); ?>" tabindex="0" aria-label="Reject"
						<?php
						if ( 'CONSTANT_OPEN_URL' === $the_options['button_decline_action' . $suffix] ) {
							?>
							href="<?php echo esc_html( $the_options['button_decline_url' . $suffix] ); ?>"
							<?php
							if ( ! empty( $the_options['button_decline_new_win' . $suffix] ) ) {
								?>
								target="_blank"
								<?php
							} else {
								?>
								href="#"
								<?php
							}
						}
						?>
						data-gdpr_action="reject" style="<?php echo esc_attr( $decline_style_attr ); ?>" >
						<?php
						echo isset( $the_options['is_dynamic_lang_on'] ) && ( $the_options['is_dynamic_lang_on'] === 'true' || $the_options['is_dynamic_lang_on'] === true || $the_options['is_dynamic_lang_on'] === '1' )
							? esc_html( $cookie_data['dash_button_decline_text'] )
							: esc_html( $the_options['button_decline_text' . $suffix] ?? '' );
						?>
					</a>
				<?php endif;
				break;

			case 'settings':
				if ( $the_options['cookie_usage_for'] !== 'eprivacy' && ! empty( $the_options['button_settings_is_on' . $suffix] ) && filter_var( $the_options['button_settings_is_on' . $suffix], FILTER_VALIDATE_BOOLEAN ) ) : ?>
					<a id="cookie_action_settings" class="<?php echo esc_html( $the_options['button_settings_classes'] ); ?>" tabindex="0" aria-label="Cookie Settings" href="#"
						data-gdpr_action="settings" data-toggle="gdprmodal" data-target="#gdpr-gdprmodal" style="<?php echo esc_attr( $settings_style_attr ); ?>">
						<?php
						echo isset( $the_options['is_dynamic_lang_on'] ) && ( $the_options['is_dynamic_lang_on'] === 'true' || $the_options['is_dynamic_lang_on'] === true || $the_options['is_dynamic_lang_on'] === '1' )
							? esc_html( $cookie_data['dash_button_settings_text'] )
							: esc_html( $the_options['button_settings_text' . $suffix] ?? '' );
						?>
					</a>
				<?php endif;
				break;

			case 'accept':
				if ( filter_var( $the_options['button_accept_is_on' . $suffix] ?? false, FILTER_VALIDATE_BOOLEAN ) ) : ?>
					<a id="cookie_action_accept" class="<?php echo esc_html( $the_options['button_accept_classes'] ); ?>" tabindex="0" aria-label="Accept"
						<?php
						if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_action' . $suffix] ) {
							?>
							href="<?php echo esc_html( $the_options['button_accept_url' . $suffix] ); ?>"
							<?php
							if ( ! empty( $the_options['button_accept_new_win' . $suffix] ) ) {
								?>
								target="_blank"
								<?php
							}
						} else {
							?>
							href="#"
							<?php
						}
						?>
						data-gdpr_action="accept" style="<?php echo esc_attr( $accept_style_attr ); ?>" >
						<?php
						echo isset( $the_options['is_dynamic_lang_on'] ) && ( $the_options['is_dynamic_lang_on'] === 'true' || $the_options['is_dynamic_lang_on'] === true || $the_options['is_dynamic_lang_on'] === '1' )
							? esc_html( $cookie_data['dash_button_accept_text'] )
							: esc_html( $the_options['button_accept_text' . $suffix] ?? '' );
						?>
					</a>
				<?php endif;
				break;

			case 'accept_all':
			case 'acceptAll':
				if ( filter_var( $the_options['button_accept_all_is_on' . $suffix] , FILTER_VALIDATE_BOOLEAN ) ) : ?>
					<a id="cookie_action_accept_all" class="<?php echo esc_html( $the_options['button_accept_all_classes'] ); ?>" tabindex="0" aria-label="Accept All"
						<?php
						if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_all_action' . $suffix] ) {
							?>
							href="<?php echo esc_html( $the_options['button_accept_all_url' . $suffix] ); ?>"
							<?php
							if ( ! empty( $the_options['button_accept_all_new_win' . $suffix] ) ) {
								?>
								target="_blank"
								<?php
							}
						} else {
							?>
							href="#"
							<?php
						}
						?>
						data-gdpr_action="accept_all" style="<?php echo esc_attr( $accept_all_style_attr ); ?>" >
						<?php
						echo ( isset( $the_options['is_dynamic_lang_on'] ) && ( $the_options['is_dynamic_lang_on'] === 'true' || $the_options['is_dynamic_lang_on'] === true || $the_options['is_dynamic_lang_on'] === '1' ) )
							? esc_html( $cookie_data['dash_button_accept_all_text'] )
							: esc_html( $the_options['button_accept_all_text' . $suffix] ?? '' );
						?>
					</a>
				<?php endif;
				break;
		}
	};
?>

<div id="<?php echo esc_html( $the_options['container_id'] ); ?>" class="<?php echo esc_html( $the_options['container_class'] ); ?> <?php echo esc_html( $the_options['theme_class'] ); ?>"  style="<?php echo esc_attr($notice_container_styles); ?>; --vendor-link-color: <?php echo esc_attr($vendor_link_color); ?>;">	
	<?php if(filter_var( $the_options['bypass_button_is_on' . $suffix] ?? false, FILTER_VALIDATE_BOOLEAN )) { ?>
	<span id="cookie-banner-cancle-img" style="cursor: pointer; display: inline-flex; align-items: center; justify-content: center; position: absolute; top:5px; right: <?php echo esc_attr( 5 + ( (int) $the_options[ ( $ab_testing_enabled === "true" ? 'cookie_bar_spacing' . $chosenBanner : 'cookie_bar_spacing' ) ] ) / 2 + ( (int) $the_options[ ( $ab_testing_enabled === "true" ? 'cookie_bar_border_radius' . $chosenBanner : 'background_border_radius' ) ] ) / 2 ); ?>px; height: 20px; width: 20px; border-radius: 50%; color: <?php echo esc_html($the_options['bypass_button_text_color' . $suffix]);?>; scale: <?php echo esc_html($the_options['bypass_button_size'. $suffix]) == 'lg' ? '115%' : (esc_html($the_options['bypass_button_size'. $suffix]) == 'sm' ? '85%' : '100%'); ?>;">
		<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
			<path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z" fill="currentColor"/>
		</svg>
	</span>
	<?php } ?>
	<div class="gdpr_notice_header" style="flex-direction: <?php echo esc_attr($banner_layouts['c1']['direction'] ?? 'row') == 'col' ? ($banner_structure['c1'][0] === 'logo' ? 'column' : 'column-reverse') : ($banner_structure['c1'][0] === 'logo' ? 'row' : 'row-reverse'); ?>; <?php echo $banner_layouts['c1']['direction'] === 'row' ? 'align-items: center; justify-content: ' . ($banner_layouts['c1']['justify'] ===  'between' ? 'space-between' : esc_attr($banner_layouts['c1']['justify'] ?? '')) : 'align-items: ' . esc_attr($banner_layouts['c1']['justify'] ?? '') ?>">
		<?php
		if($ab_options['ab_testing_enabled'] === "false" || $ab_options['ab_testing_enabled'] === false){
			if(filter_var( $the_options['logo_is_on'] ?? false, FILTER_VALIDATE_BOOLEAN )) {
				$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
				if (!empty($get_banner_img) && filter_var( $the_options['use_uploaded_logo'] ?? false, FILTER_VALIDATE_BOOLEAN )) {
					?>
						<img style = "<?php echo esc_attr($logo_style_attr); ?>" class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
						<?php
				}
				else{
					?><img alt="Logo image" style="<?php echo esc_attr( $logo_style_attr ); ?>" class="gdpr_logo_image" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL . 'includes/templates/logo_images/banner_' . sanitize_file_name( $the_options['default_logo'] ) . '.svg' ); ?>"><?php 
				}
			}
		}
		else{
			if($ab_options['ab_testing_enabled'] === "true" || $ab_options['ab_testing_enabled'] === true){
				if($chosenBanner == 1) {
					if(filter_var( $the_options['logo_is_on1'] ?? false, FILTER_VALIDATE_BOOLEAN )) {
						$get_banner_img1 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1 );
						if (!empty($get_banner_img1) && filter_var( $the_options['use_uploaded_logo1'] ?? false, FILTER_VALIDATE_BOOLEAN )) {
							?>
								<img style = "<?php echo esc_attr($logo_style_attr); ?>" class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img1 ); ?>" >
								<?php
						}
						else{
							?><img alt="Logo image" style="<?php echo esc_attr( $logo_style_attr ); ?>" class="gdpr_logo_image" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL . 'includes/templates/logo_images/banner_' . sanitize_file_name( $the_options['default_logo1'] ) . '.svg' ); ?>"><?php 
						}
					}
				}elseif($chosenBanner == 2){
					if(filter_var( $the_options['logo_is_on2'] ?? false, FILTER_VALIDATE_BOOLEAN )) {
						$get_banner_img2 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD2 );
						if (!empty($get_banner_img2) && filter_var( $the_options['use_uploaded_logo2'] ?? false, FILTER_VALIDATE_BOOLEAN )) {
							?>
								<img style = "<?php echo esc_attr($logo_style_attr); ?>" class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img2 ); ?>" >
								<?php
						}
						else{
							?><img alt="Logo image" style="<?php echo esc_attr( $logo_style_attr ); ?>" class="gdpr_logo_image" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL . 'includes/templates/logo_images/banner_' . sanitize_file_name( $the_options['default_logo2'] ) . '.svg' ); ?>"><?php 
						}
					}
				}
			}
		} ?>
		<?php
		if(filter_var( $the_options['heading_is_on' . $suffix] ?? false, FILTER_VALIDATE_BOOLEAN )) {
			if ( strlen($the_options['bar_heading_text']) > 0) : ?>
				<h3 style = "<?php echo esc_attr($heading_style_attr); ?>" ><?php echo esc_html($the_options['bar_heading_text'] ?? ''); ?></h3>
			<?php endif; 
		} ?>
	</div>

	

	<div class=" gdpr_main_body"  style="display: flex; flex-direction: <?php echo esc_attr($banner_layouts['c2']['direction'] ?? 'row') == 'col' ? ($banner_structure['c2'][0] === 'bannerText' ? 'column' : 'column-reverse') : ($banner_structure['c2'][0] === 'bannerText' ? 'row' : 'row-reverse'); ?>; <?php echo esc_attr("gap: " . ($ab_testing_enabled === "true" ? $the_options['cookie_bar_spacing' . $chosenBanner] : $the_options["cookie_bar_spacing"]) . "px;"); echo esc_attr($banner_layouts['c2']['direction'] == 'row' ? 'align-items:center;' : '');?>" >
		<div class="gdpr-notice-content-body">
			<div style="display: flex; flex-direction: column; gap: 10px;">
				
					
				<p style="<?php echo esc_attr($text_style_attr); ?>"  class = "<?php if($the_options['cookie_usage_for'] === 'both') echo 'gdpr';?>">
					<?php
					// Laws still awaiting their own copy render scaffolding text in place
					// of the message body — see Gdpr_Cookie_Consent::get_law_placeholder_message().
					// Empty for every law that has real copy, so the ladder below is
					// unchanged for GDPR, LGPD and ePrivacy.
					?>
					<?php if ( ! empty( $law_placeholder_message ) ) : ?>
						<span><?php echo esc_html( $law_placeholder_message ); ?></span>
					<?php elseif ( $the_options['cookie_usage_for'] === 'gdpr'  || $the_options['cookie_usage_for'] === 'both' ) : ?>
						<span>
							<?php
							echo wp_kses(
								$the_options['is_iabtcf_on'] 
									? ($cookie_data['dash_notify_message_iabtcf'] ?? '')
									: ($cookie_data['dash_notify_message'] ?? ''),
								[
									'a' => [
										'href'   => [],
										'title' => [],
										'target'=> [],
										'rel'   => [],
										'data-toggle' => [],
										'data-target' => [],
										'id' => [],
									],
									'br'     => [],
									'em'     => [],
									'strong' => [],
									'span'   => [],
									'p'      => [],
									'i'      => [],
									'img'    => [
										'src'   => [],
										'alt'   => [],
										'title'=> [],
									],
									'b'      => [],
									'div'    => [],
									'label'  => [],
								]
							);
							?>
						</span>
						<?php elseif ( $the_options['cookie_usage_for'] === 'lgpd' ) : ?>
						<span>
							<?php
							echo wp_kses(
								$cookie_data['dash_notify_message_lgpd'] ?? '',
								[
									'a'      => [],
									'br'     => [],
									'em'     => [],
									'strong' => [],
									'span'   => [],
									'p'      => [],
									'i'      => [],
									'img'    => [
										'src'   => [],
										'alt'   => [],
										'title'=> [],
									],
									'b'      => [],
									'div'    => [],
									'label'  => [],
								]
							);
							?>
						</span>
						<?php elseif ( $the_options['cookie_usage_for'] === 'ccpa' ) : ?>
						<span>
							<?php
							echo wp_kses(
								$cookie_data['dash_notify_message_ccpa'] ?? '',
								[
									'a'      => [],
									'br'     => [],
									'em'     => [],
									'strong' => [],
									'span'   => [],
									'p'      => [],
									'i'      => [],
									'img'    => [
										'src'   => [],
										'alt'   => [],
										'title'=> [],
									],
									'b'      => [],
									'div'    => [],
									'label'  => [],
								]
							);
							?>
						</span>
						<?php elseif ( $the_options['cookie_usage_for'] === 'us_state_laws' ) : ?>
						<span>
							<?php
							echo wp_kses(
								$cookie_data['dash_notify_message_us_state'] ?? '',
								[
									'a'      => [],
									'br'     => [],
									'em'     => [],
									'strong' => [],
									'span'   => [],
									'p'      => [],
									'i'      => [],
									'img'    => [
										'src'   => [],
										'alt'   => [],
										'title'=> [],
									],
									'b'      => [],
									'div'    => [],
									'label'  => [],
								]
							);
							?>
						</span>
						<?php elseif ( $the_options['cookie_usage_for'] === 'uk_gdpr' ) : ?>
						<span>
							<?php
							echo wp_kses(
								$cookie_data['dash_notify_message_uk_gdpr'] ?? '',
								[
									'a'      => [],
									'br'     => [],
									'em'     => [],
									'strong' => [],
									'span'   => [],
									'p'      => [],
									'i'      => [],
									'img'    => [
										'src'   => [],
										'alt'   => [],
										'title'=> [],
									],
									'b'      => [],
									'div'    => [],
									'label'  => [],
								]
							);
							?>
						</span>
						<?php elseif ( $the_options['cookie_usage_for'] === 'sa_pdpl' ) : ?>
						<span>
							<?php
							echo wp_kses(
								$cookie_data['dash_notify_message_pdpl'] ?? '',
								[
									'a'      => [],
									'br'     => [],
									'em'     => [],
									'strong' => [],
									'span'   => [],
									'p'      => [],
									'i'      => [],
									'img'    => [
										'src'   => [],
										'alt'   => [],
										'title'=> [],
									],
									'b'      => [],
									'div'    => [],
									'label'  => [],
								]
							);
							?>
						</span>
						<?php elseif ( $the_options['cookie_usage_for'] === 'pipeda' ) : ?>
						<span>
							<?php
							echo wp_kses(
								$cookie_data['dash_notify_message_pipeda'] ?? '',
								[
									'a'      => [],
									'br'     => [],
									'em'     => [],
									'strong' => [],
									'span'   => [],
									'p'      => [],
									'i'      => [],
									'img'    => [
										'src'   => [],
										'alt'   => [],
										'title'=> [],
									],
									'b'      => [],
									'div'    => [],
									'label'  => [],
								]
							);
							?>
						</span>
						<?php elseif ( $the_options['cookie_usage_for'] === 'au_app' ) : ?>
						<span>
							<?php
							echo wp_kses(
								$cookie_data['dash_notify_message_app'] ?? '',
								[
									'a'      => [],
									'br'     => [],
									'em'     => [],
									'strong' => [],
									'span'   => [],
									'p'      => [],
									'i'      => [],
									'img'    => [
										'src'   => [],
										'alt'   => [],
										'title'=> [],
									],
									'b'      => [],
									'div'    => [],
									'label'  => [],
								]
							);
							?>
						</span>
						<?php elseif ( $the_options['cookie_usage_for'] === 'eprivacy' ) : ?>
						<span>
							<?php
							echo wp_kses(
								$cookie_data['dash_notify_message_eprivacy'] ?? '',
								[
									'a'      => [],
									'br'     => [],
									'em'     => [],
									'strong' => [],
									'span'   => [],
									'p'      => [],
									'i'      => [],
									'img'    => [
										'src'   => [],
										'alt'   => [],
										'title'=> [],
									],
									'b'      => [],
									'div'    => [],
									'label'  => [],
								]
							);
							?>
						</span>
					<?php endif; ?>
					<?php if ( in_array( $the_options['cookie_usage_for'], array( 'ccpa', 'us_state_laws' ), true ) ) : ?>
						<a style="<?php echo esc_attr($opt_out_style_attr); ?>" data-toggle="gdprmodal" href="#" class="<?php echo esc_html( $the_options['button_donotsell_classes'] ); ?>" data-gdpr_action="donotsell" id="cookie_donotsell_link"
						>
							<?php echo esc_html($cookie_data['dash_button_donotsell_text'], 'gdpr-cookie-consent' ); ?>
						</a>

					<?php elseif( ! in_array( $the_options['cookie_usage_for'], array( 'ccpa', 'us_state_laws' ), true ) &&  ! empty( $the_options['button_readmore_is_on'] ) ) : ?>
						<a style="<?php echo esc_attr($readmore_style_attr); ?>" id="cookie_action_link" href="<?php echo esc_html( $the_options['button_readmore_url_link'] ); ?>" 
						<?php if ( ! empty( $the_options['button_readmore_new_win'] ) ) { ?>
							target="_blank"
						<?php } ?>
						>
							<?php echo esc_html( $cookie_data['dash_button_readmore_text'], 'gdpr-cookie-consent' ); ?>
						</a>
					<?php endif; ?>
				</p>
			</div>
		
		<?php if($the_options['cookie_usage_for'] === 'both') : ?>
			<p style="<?php echo esc_attr($text_style_attr); ?>" class = "ccpa">
				<span>
					<?php
					echo wp_kses(
						$cookie_data['dash_notify_message_ccpa'] ?? '',
						[
							'a'      => [],
							'br'     => [],
							'em'     => [],
							'strong' => [],
							'span'   => [],
							'p'      => [],
							'i'      => [],
							'img'    => [
								'src'   => [],
								'alt'   => [],
								'title'=> [],
							],
							'b'      => [],
							'div'    => [],
							'label'  => [],
						]
					);
					?>
				</span>
				<a style="<?php echo esc_attr($opt_out_style_attr); ?>" data-toggle="gdprmodal" href="#" class="<?php echo esc_html( $the_options['button_donotsell_classes'] ); ?>" data-gdpr_action="donotsell" id="cookie_donotsell_link"
				>	
					<?php echo isset($the_options["is_dynamic_lang_on"]) && ($the_options["is_dynamic_lang_on"] === "true" || $the_options["is_dynamic_lang_on"] === true || $the_options["is_dynamic_lang_on"] === "1")
						? esc_html( $cookie_data['dash_button_donotsell_text'] )
						: esc_html( $the_options['button_donotsell_text'] ?? '', 'gdpr-cookie-consent' ); 
					?>
				</a>
			</p>
		<?php endif; ?>
		</div>
		<?php if ( ! in_array( $the_options['cookie_usage_for'], array( 'ccpa', 'us_state_laws' ), true ) ) : ?>
			<div class="gdpr group-description-buttons cookie_notice_buttons" style="margin-top: <?php echo esc_attr($banner_layouts['c2']['direction'] ?? 'row') == 'col' ? '5px' : '0px'; ?>; display: flex; flex-direction: <?php echo esc_attr($banner_layouts['c4']['direction'] ?? 'row') == 'col' ? 'column' : 'row'; ?>; <?php echo esc_attr("gap: " . ($ab_testing_enabled === "true" ? $the_options['cookie_bar_spacing' . $chosenBanner] * ($the_options['template'] === 'default' ? 0.75 : 2) : $the_options["cookie_bar_spacing"] * ($the_options['template'] === 'default' ? 0.75 : 2)) . "px;"); echo esc_attr($banner_layouts['c2']['direction'] == 'row' ? 'width: 40%' : '');?>">
				<div class="left_buttons" style="display: <?php echo count($visible_c5_items) > 0 ? 'flex' : 'none'; ?>; flex-direction: <?php echo esc_attr($banner_layouts['c5']['direction'] ?? 'row') == 'col' ? 'column' : 'row'; ?>; <?php echo esc_attr("gap: " . ($ab_testing_enabled === "true" ? $the_options['cookie_bar_spacing' . $chosenBanner] : $the_options["cookie_bar_spacing"]) * ($the_options['template'] === 'default' ? 0.75 : 2) . "px;"); ?> <?php echo esc_attr($banner_layouts['c5']['direction'] === 'row' ? 'align-items: center; justify-content: ' . ($banner_layouts['c5']['justify'] ===  'between' ? 'space-between' : ($banner_layouts['c5']['justify'] ?? '')) : 'align-items: ' . ($banner_layouts['c5']['justify'] ?? '')) ?>">
					<?php 
					foreach ( $c5_buttons as $button ) {
						$render_cookie_button( $button );
					}
					?>
				</div>
				<div class="right_buttons" style="display: <?php echo count($visible_c6_items) > 0 ? 'flex' : 'none'; ?>;  flex-direction: <?php echo esc_attr($banner_layouts['c6']['direction'] ?? 'row') == 'col' ? 'column' : 'row'; ?>; <?php echo esc_attr("gap: " . ($ab_testing_enabled === "true" ? $the_options['cookie_bar_spacing' . $chosenBanner] : $the_options["cookie_bar_spacing"]) * ($the_options['template'] === 'default' ? 0.75 : 2) . "px;"); ?> <?php echo esc_attr($banner_layouts['c6']['direction'] === 'row' ? 'align-items: center; justify-content: ' . ($banner_layouts['c6']['justify'] ===  'between' ? 'space-between' : ($banner_layouts['c6']['justify'] ?? '')) : 'align-items: ' . ($banner_layouts['c6']['justify'] ?? '')) ?>">
					<?php  
					foreach ( $c6_buttons as $button ) {
						$render_cookie_button( $button );
					}
					?>
				</div>
			</div>
		<?php endif; ?>
		
	</div>
	<?php
    if ( ! empty( $cookie_data['show_credits'] ) ) {
    ?>
    	<div class="powered-by-credits"  style="--popup_accent_color: <?php echo esc_attr( '#' . ltrim($badging_color, '#') ); ?>; text-align:center; font-size: 10px; margin-top:<?php echo esc_attr(-1 * ($ab_testing_enabled === "true" ? $the_options['cookie_bar_spacing' . $chosenBanner] : $the_options["cookie_bar_spacing"]) + 5); ?>px;">
			<svg width="152" height="18" viewBox="0 0 152 18" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M4.13672 9.49805H1.85742V8.57812H4.13672C4.57812 8.57812 4.93555 8.50781 5.20898 8.36719C5.48242 8.22656 5.68164 8.03125 5.80664 7.78125C5.93555 7.53125 6 7.24609 6 6.92578C6 6.63281 5.93555 6.35742 5.80664 6.09961C5.68164 5.8418 5.48242 5.63477 5.20898 5.47852C4.93555 5.31836 4.57812 5.23828 4.13672 5.23828H2.12109V12.8438H0.990234V4.3125H4.13672C4.78125 4.3125 5.32617 4.42383 5.77148 4.64648C6.2168 4.86914 6.55469 5.17773 6.78516 5.57227C7.01562 5.96289 7.13086 6.41016 7.13086 6.91406C7.13086 7.46094 7.01562 7.92773 6.78516 8.31445C6.55469 8.70117 6.2168 8.99609 5.77148 9.19922C5.32617 9.39844 4.78125 9.49805 4.13672 9.49805ZM8.03906 9.74414V9.60938C8.03906 9.15234 8.10547 8.72852 8.23828 8.33789C8.37109 7.94336 8.5625 7.60156 8.8125 7.3125C9.0625 7.01953 9.36523 6.79297 9.7207 6.63281C10.0762 6.46875 10.4746 6.38672 10.916 6.38672C11.3613 6.38672 11.7617 6.46875 12.1172 6.63281C12.4766 6.79297 12.7812 7.01953 13.0312 7.3125C13.2852 7.60156 13.4785 7.94336 13.6113 8.33789C13.7441 8.72852 13.8105 9.15234 13.8105 9.60938V9.74414C13.8105 10.2012 13.7441 10.625 13.6113 11.0156C13.4785 11.4062 13.2852 11.748 13.0312 12.041C12.7812 12.3301 12.4785 12.5566 12.123 12.7207C11.7715 12.8809 11.373 12.9609 10.9277 12.9609C10.4824 12.9609 10.082 12.8809 9.72656 12.7207C9.37109 12.5566 9.06641 12.3301 8.8125 12.041C8.5625 11.748 8.37109 11.4062 8.23828 11.0156C8.10547 10.625 8.03906 10.2012 8.03906 9.74414ZM9.12305 9.60938V9.74414C9.12305 10.0605 9.16016 10.3594 9.23438 10.6406C9.30859 10.918 9.41992 11.1641 9.56836 11.3789C9.7207 11.5938 9.91016 11.7637 10.1367 11.8887C10.3633 12.0098 10.627 12.0703 10.9277 12.0703C11.2246 12.0703 11.4844 12.0098 11.707 11.8887C11.9336 11.7637 12.1211 11.5938 12.2695 11.3789C12.418 11.1641 12.5293 10.918 12.6035 10.6406C12.6816 10.3594 12.7207 10.0605 12.7207 9.74414V9.60938C12.7207 9.29688 12.6816 9.00195 12.6035 8.72461C12.5293 8.44336 12.416 8.19531 12.2637 7.98047C12.1152 7.76172 11.9277 7.58984 11.7012 7.46484C11.4785 7.33984 11.2168 7.27734 10.916 7.27734C10.6191 7.27734 10.3574 7.33984 10.1309 7.46484C9.9082 7.58984 9.7207 7.76172 9.56836 7.98047C9.41992 8.19531 9.30859 8.44336 9.23438 8.72461C9.16016 9.00195 9.12305 9.29688 9.12305 9.60938ZM16.7754 11.7188L18.4043 6.50391H19.1191L18.9785 7.54102L17.3203 12.8438H16.623L16.7754 11.7188ZM15.6797 6.50391L17.0684 11.7773L17.168 12.8438H16.4355L14.5957 6.50391H15.6797ZM20.6777 11.7363L22.002 6.50391H23.0801L21.2402 12.8438H20.5137L20.6777 11.7363ZM19.2773 6.50391L20.8711 11.6309L21.0527 12.8438H20.3613L18.6562 7.5293L18.5156 6.50391H19.2773ZM26.8242 12.9609C26.3828 12.9609 25.9824 12.8867 25.623 12.7383C25.2676 12.5859 24.9609 12.373 24.7031 12.0996C24.4492 11.8262 24.2539 11.502 24.1172 11.127C23.9805 10.752 23.9121 10.3418 23.9121 9.89648V9.65039C23.9121 9.13477 23.9883 8.67578 24.1406 8.27344C24.293 7.86719 24.5 7.52344 24.7617 7.24219C25.0234 6.96094 25.3203 6.74805 25.6523 6.60352C25.9844 6.45898 26.3281 6.38672 26.6836 6.38672C27.1367 6.38672 27.5273 6.46484 27.8555 6.62109C28.1875 6.77734 28.459 6.99609 28.6699 7.27734C28.8809 7.55469 29.0371 7.88281 29.1387 8.26172C29.2402 8.63672 29.291 9.04688 29.291 9.49219V9.97852H24.5566V9.09375H28.207V9.01172C28.1914 8.73047 28.1328 8.45703 28.0312 8.19141C27.9336 7.92578 27.7773 7.70703 27.5625 7.53516C27.3477 7.36328 27.0547 7.27734 26.6836 7.27734C26.4375 7.27734 26.2109 7.33008 26.0039 7.43555C25.7969 7.53711 25.6191 7.68945 25.4707 7.89258C25.3223 8.0957 25.207 8.34375 25.125 8.63672C25.043 8.92969 25.002 9.26758 25.002 9.65039V9.89648C25.002 10.1973 25.043 10.4805 25.125 10.7461C25.2109 11.0078 25.334 11.2383 25.4941 11.4375C25.6582 11.6367 25.8555 11.793 26.0859 11.9062C26.3203 12.0195 26.5859 12.0762 26.8828 12.0762C27.2656 12.0762 27.5898 11.998 27.8555 11.8418C28.1211 11.6855 28.3535 11.4766 28.5527 11.2148L29.209 11.7363C29.0723 11.9434 28.8984 12.1406 28.6875 12.3281C28.4766 12.5156 28.2168 12.668 27.9082 12.7852C27.6035 12.9023 27.2422 12.9609 26.8242 12.9609ZM31.6406 7.5V12.8438H30.5566V6.50391H31.6113L31.6406 7.5ZM33.6211 6.46875L33.6152 7.47656C33.5254 7.45703 33.4395 7.44531 33.3574 7.44141C33.2793 7.43359 33.1895 7.42969 33.0879 7.42969C32.8379 7.42969 32.6172 7.46875 32.4258 7.54688C32.2344 7.625 32.0723 7.73438 31.9395 7.875C31.8066 8.01562 31.7012 8.18359 31.623 8.37891C31.5488 8.57031 31.5 8.78125 31.4766 9.01172L31.1719 9.1875C31.1719 8.80469 31.209 8.44531 31.2832 8.10938C31.3613 7.77344 31.4805 7.47656 31.6406 7.21875C31.8008 6.95703 32.0039 6.75391 32.25 6.60938C32.5 6.46094 32.7969 6.38672 33.1406 6.38672C33.2188 6.38672 33.3086 6.39648 33.4102 6.41602C33.5117 6.43164 33.582 6.44922 33.6211 6.46875ZM37.1484 12.9609C36.707 12.9609 36.3066 12.8867 35.9473 12.7383C35.5918 12.5859 35.2852 12.373 35.0273 12.0996C34.7734 11.8262 34.5781 11.502 34.4414 11.127C34.3047 10.752 34.2363 10.3418 34.2363 9.89648V9.65039C34.2363 9.13477 34.3125 8.67578 34.4648 8.27344C34.6172 7.86719 34.8242 7.52344 35.0859 7.24219C35.3477 6.96094 35.6445 6.74805 35.9766 6.60352C36.3086 6.45898 36.6523 6.38672 37.0078 6.38672C37.4609 6.38672 37.8516 6.46484 38.1797 6.62109C38.5117 6.77734 38.7832 6.99609 38.9941 7.27734C39.2051 7.55469 39.3613 7.88281 39.4629 8.26172C39.5645 8.63672 39.6152 9.04688 39.6152 9.49219V9.97852H34.8809V9.09375H38.5312V9.01172C38.5156 8.73047 38.457 8.45703 38.3555 8.19141C38.2578 7.92578 38.1016 7.70703 37.8867 7.53516C37.6719 7.36328 37.3789 7.27734 37.0078 7.27734C36.7617 7.27734 36.5352 7.33008 36.3281 7.43555C36.1211 7.53711 35.9434 7.68945 35.7949 7.89258C35.6465 8.0957 35.5312 8.34375 35.4492 8.63672C35.3672 8.92969 35.3262 9.26758 35.3262 9.65039V9.89648C35.3262 10.1973 35.3672 10.4805 35.4492 10.7461C35.5352 11.0078 35.6582 11.2383 35.8184 11.4375C35.9824 11.6367 36.1797 11.793 36.4102 11.9062C36.6445 12.0195 36.9102 12.0762 37.207 12.0762C37.5898 12.0762 37.9141 11.998 38.1797 11.8418C38.4453 11.6855 38.6777 11.4766 38.877 11.2148L39.5332 11.7363C39.3965 11.9434 39.2227 12.1406 39.0117 12.3281C38.8008 12.5156 38.541 12.668 38.2324 12.7852C37.9277 12.9023 37.5664 12.9609 37.1484 12.9609ZM44.877 11.6133V3.84375H45.9668V12.8438H44.9707L44.877 11.6133ZM40.6113 9.74414V9.62109C40.6113 9.13672 40.6699 8.69727 40.7871 8.30273C40.9082 7.9043 41.0781 7.5625 41.2969 7.27734C41.5195 6.99219 41.7832 6.77344 42.0879 6.62109C42.3965 6.46484 42.7402 6.38672 43.1191 6.38672C43.5176 6.38672 43.8652 6.45703 44.1621 6.59766C44.4629 6.73438 44.7168 6.93555 44.9238 7.20117C45.1348 7.46289 45.3008 7.7793 45.4219 8.15039C45.543 8.52148 45.627 8.94141 45.6738 9.41016V9.94922C45.6309 10.4141 45.5469 10.832 45.4219 11.2031C45.3008 11.5742 45.1348 11.8906 44.9238 12.1523C44.7168 12.4141 44.4629 12.6152 44.1621 12.7559C43.8613 12.8926 43.5098 12.9609 43.1074 12.9609C42.7363 12.9609 42.3965 12.8809 42.0879 12.7207C41.7832 12.5605 41.5195 12.3359 41.2969 12.0469C41.0781 11.7578 40.9082 11.418 40.7871 11.0273C40.6699 10.6328 40.6113 10.2051 40.6113 9.74414ZM41.7012 9.62109V9.74414C41.7012 10.0605 41.7324 10.3574 41.7949 10.6348C41.8613 10.9121 41.9629 11.1562 42.0996 11.3672C42.2363 11.5781 42.4102 11.7441 42.6211 11.8652C42.832 11.9824 43.084 12.041 43.377 12.041C43.7363 12.041 44.0312 11.9648 44.2617 11.8125C44.4961 11.6602 44.6836 11.459 44.8242 11.209C44.9648 10.959 45.0742 10.6875 45.1523 10.3945V8.98242C45.1055 8.76758 45.0371 8.56055 44.9473 8.36133C44.8613 8.1582 44.748 7.97852 44.6074 7.82227C44.4707 7.66211 44.3008 7.53516 44.0977 7.44141C43.8984 7.34766 43.6621 7.30078 43.3887 7.30078C43.0918 7.30078 42.8359 7.36328 42.6211 7.48828C42.4102 7.60938 42.2363 7.77734 42.0996 7.99219C41.9629 8.20312 41.8613 8.44922 41.7949 8.73047C41.7324 9.00781 41.7012 9.30469 41.7012 9.62109ZM50.625 3.84375H51.7148V11.6133L51.6211 12.8438H50.625V3.84375ZM55.998 9.62109V9.74414C55.998 10.2051 55.9434 10.6328 55.834 11.0273C55.7246 11.418 55.5645 11.7578 55.3535 12.0469C55.1426 12.3359 54.8848 12.5605 54.5801 12.7207C54.2754 12.8809 53.9258 12.9609 53.5312 12.9609C53.1289 12.9609 52.7754 12.8926 52.4707 12.7559C52.1699 12.6152 51.916 12.4141 51.709 12.1523C51.502 11.8906 51.3359 11.5742 51.2109 11.2031C51.0898 10.832 51.0059 10.4141 50.959 9.94922V9.41016C51.0059 8.94141 51.0898 8.52148 51.2109 8.15039C51.3359 7.7793 51.502 7.46289 51.709 7.20117C51.916 6.93555 52.1699 6.73438 52.4707 6.59766C52.7715 6.45703 53.1211 6.38672 53.5195 6.38672C53.918 6.38672 54.2715 6.46484 54.5801 6.62109C54.8887 6.77344 55.1465 6.99219 55.3535 7.27734C55.5645 7.5625 55.7246 7.9043 55.834 8.30273C55.9434 8.69727 55.998 9.13672 55.998 9.62109ZM54.9082 9.74414V9.62109C54.9082 9.30469 54.8789 9.00781 54.8203 8.73047C54.7617 8.44922 54.668 8.20312 54.5391 7.99219C54.4102 7.77734 54.2402 7.60938 54.0293 7.48828C53.8184 7.36328 53.5586 7.30078 53.25 7.30078C52.9766 7.30078 52.7383 7.34766 52.5352 7.44141C52.3359 7.53516 52.166 7.66211 52.0254 7.82227C51.8848 7.97852 51.7695 8.1582 51.6797 8.36133C51.5938 8.56055 51.5293 8.76758 51.4863 8.98242V10.3945C51.5488 10.668 51.6504 10.9316 51.791 11.1855C51.9355 11.4355 52.127 11.6406 52.3652 11.8008C52.6074 11.9609 52.9062 12.041 53.2617 12.041C53.5547 12.041 53.8047 11.9824 54.0117 11.8652C54.2227 11.7441 54.3926 11.5781 54.5215 11.3672C54.6543 11.1562 54.752 10.9121 54.8145 10.6348C54.877 10.3574 54.9082 10.0605 54.9082 9.74414ZM59.0918 12.1875L60.8555 6.50391H62.0156L59.4727 13.8223C59.4141 13.9785 59.3359 14.1465 59.2383 14.3262C59.1445 14.5098 59.0234 14.6836 58.875 14.8477C58.7266 15.0117 58.5469 15.1445 58.3359 15.2461C58.1289 15.3516 57.8809 15.4043 57.5918 15.4043C57.5059 15.4043 57.3965 15.3926 57.2637 15.3691C57.1309 15.3457 57.0371 15.3262 56.9824 15.3105L56.9766 14.4316C57.0078 14.4355 57.0566 14.4395 57.123 14.4434C57.1934 14.4512 57.2422 14.4551 57.2695 14.4551C57.5156 14.4551 57.7246 14.4219 57.8965 14.3555C58.0684 14.293 58.2129 14.1855 58.3301 14.0332C58.4512 13.8848 58.5547 13.6797 58.6406 13.418L59.0918 12.1875ZM57.7969 6.50391L59.4434 11.4258L59.7246 12.5684L58.9453 12.9668L56.6133 6.50391H57.7969Z" fill="#71717A"/>
			<path d="M88.7969 16.1569V11.1736H90.5713C90.9586 11.1736 91.2794 11.2442 91.5338 11.3853C91.7883 11.5264 91.9787 11.7195 92.1051 11.9644C92.2314 12.2077 92.2946 12.4819 92.2946 12.7869C92.2946 13.0934 92.2306 13.3692 92.1026 13.6142C91.9762 13.8575 91.785 14.0505 91.529 14.1933C91.2746 14.3344 90.9545 14.405 90.5689 14.405H89.3486V13.7675H90.5008C90.7455 13.7675 90.944 13.7253 91.0963 13.6409C91.2486 13.555 91.3604 13.4382 91.4317 13.2905C91.503 13.1429 91.5387 12.975 91.5387 12.7869C91.5387 12.5987 91.503 12.4316 91.4317 12.2856C91.3604 12.1396 91.2478 12.0252 91.0939 11.9425C90.9416 11.8598 90.7406 11.8184 90.4911 11.8184H89.548V16.1569H88.7969Z" fill="#71717A"/>
			<path d="M97.0767 16.1569V11.1736H97.8278V15.5097H100.083V16.1569H97.0767Z" fill="#71717A"/>
			<path d="M105.516 16.1569H104.719L106.511 11.1736H107.378L109.17 16.1569H108.372L106.965 12.0788H106.926L105.516 16.1569ZM105.65 14.2055H108.236V14.8381H105.65V14.2055Z" fill="#71717A"/>
			<path d="M112.956 11.8208V11.1736H116.809V11.8208H115.256V16.1569H114.507V11.8208H112.956Z" fill="#71717A"/>
			<path d="M121.562 16.1569V11.1736H124.649V11.8208H122.313V13.3392H124.428V13.984H122.313V16.1569H121.562Z" fill="#71717A"/>
			<path d="M133.798 13.6653C133.798 14.1973 133.701 14.6548 133.507 15.0376C133.312 15.4188 133.046 15.7125 132.707 15.9185C132.37 16.1229 131.987 16.2251 131.557 16.2251C131.126 16.2251 130.741 16.1229 130.403 15.9185C130.066 15.7125 129.8 15.418 129.605 15.0352C129.411 14.6524 129.314 14.1957 129.314 13.6653C129.314 13.1332 129.411 12.6765 129.605 12.2953C129.8 11.9125 130.066 11.6189 130.403 11.4145C130.741 11.2085 131.126 11.1055 131.557 11.1055C131.987 11.1055 132.37 11.2085 132.707 11.4145C133.046 11.6189 133.312 11.9125 133.507 12.2953C133.701 12.6765 133.798 13.1332 133.798 13.6653ZM133.055 13.6653C133.055 13.2597 132.989 12.9183 132.858 12.6409C132.728 12.3618 132.55 12.151 132.323 12.0082C132.098 11.8638 131.843 11.7917 131.557 11.7917C131.27 11.7917 131.014 11.8638 130.789 12.0082C130.564 12.151 130.386 12.3618 130.254 12.6409C130.125 12.9183 130.06 13.2597 130.06 13.6653C130.06 14.0708 130.125 14.4131 130.254 14.6921C130.386 14.9695 130.564 15.1804 130.789 15.3248C131.014 15.4675 131.27 15.5389 131.557 15.5389C131.843 15.5389 132.098 15.4675 132.323 15.3248C132.55 15.1804 132.728 14.9695 132.858 14.6921C132.989 14.4131 133.055 14.0708 133.055 13.6653Z" fill="#71717A"/>
			<path d="M138.636 16.1569V11.1736H140.411C140.796 11.1736 141.117 11.2401 141.371 11.3731C141.627 11.5061 141.818 11.6903 141.945 11.9255C142.071 12.1591 142.134 12.4292 142.134 12.7358C142.134 13.0407 142.07 13.3092 141.942 13.5412C141.816 13.7715 141.625 13.9508 141.368 14.0789C141.114 14.2071 140.794 14.2712 140.408 14.2712H139.064V13.6239H140.34C140.583 13.6239 140.781 13.589 140.933 13.5193C141.087 13.4495 141.2 13.3481 141.271 13.2151C141.343 13.0821 141.378 12.9223 141.378 12.7358C141.378 12.5476 141.342 12.3846 141.269 12.2467C141.198 12.1088 141.085 12.0033 140.931 11.9303C140.779 11.8557 140.579 11.8184 140.331 11.8184H139.387V16.1569H138.636ZM141.094 13.9086L142.324 16.1569H141.468L140.263 13.9086H141.094Z" fill="#71717A"/>
			<path d="M146.95 11.1736H147.861L149.446 15.0474H149.504L151.089 11.1736H152.001V16.1569H151.286V12.5508H151.24L149.772 16.1496H149.179L147.71 12.5484H147.664V16.1569H146.95V11.1736Z" fill="#71717A"/>
			<path d="M94.2056 4.00665H92.5576C92.5357 3.83746 92.4906 3.68474 92.4225 3.54851C92.3544 3.41228 92.2643 3.29582 92.1522 3.19914C92.0402 3.10246 91.9072 3.02884 91.7534 2.97831C91.6018 2.92557 91.4337 2.8992 91.2491 2.8992C90.9217 2.8992 90.6394 2.97941 90.4021 3.13981C90.1669 3.30021 89.9857 3.53203 89.8582 3.83526C89.733 4.13849 89.6703 4.50544 89.6703 4.93612C89.6703 5.38437 89.7341 5.76011 89.8615 6.06334C89.9912 6.36437 90.1724 6.59179 90.4053 6.74561C90.6405 6.89722 90.9184 6.97303 91.2392 6.97303C91.4194 6.97303 91.5831 6.94996 91.7303 6.90381C91.8798 6.85767 92.0105 6.79065 92.1226 6.70276C92.2368 6.61267 92.3302 6.5039 92.4027 6.37646C92.4774 6.24681 92.5291 6.10069 92.5576 5.93809L94.2056 5.94798C94.1771 6.24681 94.0903 6.54126 93.9453 6.8313C93.8024 7.12135 93.6058 7.38612 93.3553 7.62563C93.1048 7.86294 92.7993 8.05191 92.439 8.19254C92.0808 8.33317 91.6699 8.40348 91.2063 8.40348C90.5954 8.40348 90.0483 8.26944 89.5649 8.00137C89.0837 7.7311 88.7035 7.33778 88.4245 6.82141C88.1454 6.30504 88.0059 5.67661 88.0059 4.93612C88.0059 4.19342 88.1476 3.56389 88.431 3.04752C88.7145 2.53115 89.0979 2.13893 89.5813 1.87086C90.0648 1.60279 90.6064 1.46875 91.2063 1.46875C91.615 1.46875 91.9929 1.52588 92.3401 1.64014C92.6873 1.7522 92.9927 1.917 93.2564 2.13454C93.5201 2.34987 93.7343 2.61465 93.8991 2.92887C94.0639 3.24308 94.1661 3.60234 94.2056 4.00665Z" fill="#71717A"/>
			<path d="M101.331 4.93612C101.331 5.67881 101.188 6.30834 100.902 6.82471C100.616 7.34108 100.23 7.7333 99.7419 8.00137C99.2563 8.26944 98.7114 8.40348 98.1071 8.40348C97.5007 8.40348 96.9546 8.26835 96.469 7.99808C95.9834 7.72781 95.5978 7.33559 95.3121 6.82141C95.0287 6.30504 94.8869 5.67661 94.8869 4.93612C94.8869 4.19342 95.0287 3.56389 95.3121 3.04752C95.5978 2.53115 95.9834 2.13893 96.469 1.87086C96.9546 1.60279 97.5007 1.46875 98.1071 1.46875C98.7114 1.46875 99.2563 1.60279 99.7419 1.87086C100.23 2.13893 100.616 2.53115 100.902 3.04752C101.188 3.56389 101.331 4.19342 101.331 4.93612ZM99.6628 4.93612C99.6628 4.49665 99.6002 4.12531 99.475 3.82208C99.3519 3.51885 99.1739 3.28923 98.941 3.13322C98.7103 2.97721 98.4323 2.8992 98.1071 2.8992C97.7841 2.8992 97.5062 2.97721 97.2732 3.13322C97.0403 3.28923 96.8612 3.51885 96.736 3.82208C96.6129 4.12531 96.5514 4.49665 96.5514 4.93612C96.5514 5.37558 96.6129 5.74693 96.736 6.05016C96.8612 6.35338 97.0403 6.583 97.2732 6.73901C97.5062 6.89502 97.7841 6.97303 98.1071 6.97303C98.4323 6.97303 98.7103 6.89502 98.941 6.73901C99.1739 6.583 99.3519 6.35338 99.475 6.05016C99.6002 5.74693 99.6628 5.37558 99.6628 4.93612Z" fill="#71717A"/>
			<path d="M102.03 1.56104H104.051L105.765 5.74033H105.844L107.558 1.56104H109.578V8.31119H107.989V4.16486H107.933L106.312 8.26835H105.297L103.675 4.14179H103.619V8.31119H102.03V1.56104Z" fill="#71717A"/>
			<path d="M111.147 8.31119V1.56104H113.935C114.441 1.56104 114.877 1.65992 115.244 1.85768C115.613 2.05324 115.897 2.3268 116.097 2.67837C116.297 3.02775 116.397 3.43425 116.397 3.89788C116.397 4.36371 116.295 4.77132 116.091 5.12069C115.889 5.46787 115.6 5.73704 115.224 5.9282C114.848 6.11937 114.402 6.21495 113.886 6.21495H112.165V4.92952H113.583C113.829 4.92952 114.034 4.88668 114.199 4.80098C114.366 4.71529 114.492 4.59553 114.578 4.44172C114.664 4.28571 114.706 4.10443 114.706 3.89788C114.706 3.68914 114.664 3.50896 114.578 3.35734C114.492 3.20353 114.366 3.08488 114.199 3.00138C114.032 2.91788 113.826 2.87613 113.583 2.87613H112.778V8.31119H111.147Z" fill="#71717A"/>
			<path d="M117.387 8.31119V1.56104H119.019V6.98621H121.827V8.31119H117.387Z" fill="#71717A"/>
			<path d="M124.489 1.56104V8.31119H122.857V1.56104H124.489Z" fill="#71717A"/>
			<path d="M127.159 8.31119H125.405L127.683 1.56104H129.855L132.132 8.31119H130.379L128.793 3.26176H128.741L127.159 8.31119ZM126.925 5.65464H130.59V6.89392H126.925V5.65464Z" fill="#71717A"/>
			<path d="M138.79 1.56104V8.31119H137.405L134.719 4.41535H134.676V8.31119H133.045V1.56104H134.449L137.105 5.45029H137.161V1.56104H138.79Z" fill="#71717A"/>
			<path d="M146.128 4.00665H144.48C144.458 3.83746 144.413 3.68474 144.345 3.54851C144.277 3.41228 144.187 3.29582 144.075 3.19914C143.963 3.10246 143.83 3.02884 143.676 2.97831C143.525 2.92557 143.356 2.8992 143.172 2.8992C142.845 2.8992 142.562 2.97941 142.325 3.13981C142.09 3.30021 141.908 3.53203 141.781 3.83526C141.656 4.13849 141.593 4.50544 141.593 4.93612C141.593 5.38437 141.657 5.76011 141.784 6.06334C141.914 6.36437 142.095 6.59179 142.328 6.74561C142.563 6.89722 142.841 6.97303 143.162 6.97303C143.342 6.97303 143.506 6.94996 143.653 6.90381C143.803 6.85767 143.933 6.79065 144.045 6.70276C144.16 6.61267 144.253 6.5039 144.326 6.37646C144.4 6.24681 144.452 6.10069 144.48 5.93809L146.128 5.94798C146.1 6.24681 146.013 6.54126 145.868 6.8313C145.725 7.12135 145.529 7.38612 145.278 7.62563C145.028 7.86294 144.722 8.05191 144.362 8.19254C144.004 8.33317 143.593 8.40348 143.129 8.40348C142.518 8.40348 141.971 8.26944 141.488 8.00137C141.006 7.7311 140.626 7.33778 140.347 6.82141C140.068 6.30504 139.929 5.67661 139.929 4.93612C139.929 4.19342 140.07 3.56389 140.354 3.04752C140.637 2.53115 141.021 2.13893 141.504 1.87086C141.988 1.60279 142.529 1.46875 143.129 1.46875C143.538 1.46875 143.916 1.52588 144.263 1.64014C144.61 1.7522 144.915 1.917 145.179 2.13454C145.443 2.34987 145.657 2.61465 145.822 2.92887C145.987 3.24308 146.089 3.60234 146.128 4.00665Z" fill="#71717A"/>
			<path d="M147.248 8.31119V1.56104H151.954V2.88602H148.879V4.27033H151.714V5.59861H148.879V6.98621H151.954V8.31119H147.248Z" fill="#71717A"/>
			<rect x="67.8223" y="1.57812" width="16.1979" height="14.9048" fill="white"/>
			<path d="M83.6855 0C84.2378 0 84.6855 0.447715 84.6855 1V16.6855C84.6855 17.2378 84.2378 17.6855 83.6855 17.6855H68C67.4477 17.6855 67 17.2378 67 16.6855V1C67 0.447715 67.4477 0 68 0H83.6855ZM71.8037 12.9609C71.0094 12.9611 70.3652 13.6051 70.3652 14.3994C70.3655 15.1936 71.0096 15.8377 71.8037 15.8379C72.598 15.8379 73.242 15.1937 73.2422 14.3994C73.2422 13.605 72.5981 12.9609 71.8037 12.9609ZM78.0479 9.39844V15.8057H79.4004V13.7285H80.5371C81.0285 13.7285 81.4473 13.6374 81.793 13.4561C82.1408 13.2746 82.4066 13.0207 82.5898 12.6953C82.7731 12.37 82.8652 11.9948 82.8652 11.5693C82.8652 11.1439 82.774 10.7687 82.5928 10.4434C82.4137 10.116 82.1539 9.86029 81.8125 9.67676C81.471 9.49119 81.0574 9.3985 80.5723 9.39844H78.0479ZM74.2725 9.24219V15.8037H77.3652V14.5342H75.626V9.24219H74.2725ZM80.3125 10.5059C80.5749 10.5059 80.7911 10.551 80.9619 10.6406C81.1327 10.7282 81.2604 10.8521 81.3438 11.0127C81.429 11.1711 81.4717 11.3567 81.4717 11.5693C81.4717 11.7798 81.429 11.9663 81.3438 12.1289C81.2604 12.2895 81.1327 12.416 80.9619 12.5078C80.7932 12.5974 80.5786 12.6426 80.3184 12.6426H79.4004V10.5059H80.3125ZM71.8037 9.24219C71.0094 9.24235 70.3652 9.88633 70.3652 10.6807C70.3655 11.4748 71.0096 12.119 71.8037 12.1191C72.598 12.1191 73.2419 11.4749 73.2422 10.6807C73.2422 9.88623 72.5981 9.24219 71.8037 9.24219ZM70.4336 8.46582H71.752L72.9678 4.27734H73.0176L74.2363 8.46582H75.5547L77.3848 2.05859H75.9072L74.8486 6.52051H74.792L73.627 2.05859H72.3613L71.1934 6.51074H71.1396L70.0811 2.05859H68.6025L70.4336 8.46582ZM78.0312 8.44824H79.3838V6.37012H80.5205C81.0118 6.37012 81.4307 6.2799 81.7764 6.09863C82.1242 5.91719 82.39 5.66323 82.5732 5.33789C82.7565 5.01258 82.8486 4.6373 82.8486 4.21191C82.8486 3.78642 82.7584 3.41034 82.5771 3.08496C82.3981 2.75769 82.1372 2.50284 81.7959 2.31934C81.4544 2.13376 81.0408 2.04009 80.5557 2.04004H78.0312V8.44824ZM80.2959 3.14746C80.5581 3.14746 80.7746 3.19271 80.9453 3.28223C81.116 3.36977 81.2438 3.49384 81.3271 3.6543C81.4125 3.81281 81.4551 3.99917 81.4551 4.21191C81.455 4.42237 81.4124 4.60892 81.3271 4.77148C81.2438 4.93209 81.1161 5.05862 80.9453 5.15039C80.7767 5.23992 80.5619 5.28516 80.3018 5.28516H79.3838V3.14746H80.2959Z" fill="#71717A"/>
			</svg>
		</div>
    <?php
    }
    ?>
</div>












<?php
if ( ! empty( $the_options['lgpd_notify'] )) {
	if ( ! empty( $the_options['cookie_data'] ) ) {
		?>
			<div class="gdpr_messagebar_detail layout-classic <?php echo esc_html( $the_options['template_parts'] ); ?> <?php echo esc_html( $the_options['theme_class'] ); ?>">
			<?php include plugin_dir_path( __FILE__ ) . 'modals/cookie_settings.php'; ?>
		</div>
		<?php
	}
	if ( ! empty( $the_options['show_again' . $suffix] ) ) {
		?>
		<?php if ( $the_options['show_again_as' . $suffix] === "text" ) { ?>
			<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" style="position: fixed; display:none; bottom: 10px; color: <?php echo esc_html($the_options['button_revoke_consent_text_color' . $suffix]); ?>; background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color' . $suffix]); ?>; <?php if($the_options['show_again_position' . $suffix] === 'right') echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; else echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> border-radius: 5px; box-shadow: 0px 6px 11px gray;">
				<span><?php echo esc_html( $cookie_data['dash_show_again_text'] ); //phpcs:ignore ?></span>
			</div>
			<?php } elseif ( $the_options['show_again_as' . $suffix] === "icon" ) { ?>			
				<?php if ( $the_options['show_again_icon' . $suffix] === "cookie" ) { ?>
					<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" 
						style="
							position: fixed; 
							display:none; 
							bottom: 10px; 
							color: <?php echo esc_html($the_options['button_revoke_consent_text_color' . $suffix]); ?>; 
							background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color' . $suffix]); ?>; 
							<?php if($the_options['show_again_position' . $suffix] === 'right') 
								echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; 
							else 
								echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> 
							border-radius: 50%; 
							height: 50px;
							width: 50px;
							padding: 4px !important;
							box-shadow: 0px 6px 11px gray;"
					>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
							<path fill="currentColor" stroke="currentColor" d="M321.5 91.6C320.7 86.2 316.6 81.8 311.2 81C289.1 77.9 266.6 81.9 246.8 92.4L172.8 131.9C153.1 142.4 137.2 158.9 127.4 179L90.7 254.6C80.9 274.7 77.7 297.5 81.6 319.5L96.1 402.3C100 424.4 110.7 444.6 126.8 460.2L187.1 518.6C203.2 534.2 223.7 544.2 245.8 547.3L328.8 559C350.9 562.1 373.4 558.1 393.2 547.6L467.2 508.1C486.9 497.6 502.8 481.1 512.6 460.9L549.3 385.4C559.1 365.3 562.3 342.5 558.4 320.5C557.5 315.2 553.1 311.2 547.8 310.4C496.3 302.2 455 263.3 443.3 213C441.5 205.4 435.3 199.6 427.6 198.4C373 189.7 329.9 146.4 321.4 91.6zM272 208C289.7 208 304 222.3 304 240C304 257.7 289.7 272 272 272C254.3 272 240 257.7 240 240C240 222.3 254.3 208 272 208zM208 400C208 382.3 222.3 368 240 368C257.7 368 272 382.3 272 400C272 417.7 257.7 432 240 432C222.3 432 208 417.7 208 400zM432 336C449.7 336 464 350.3 464 368C464 385.7 449.7 400 432 400C414.3 400 400 385.7 400 368C400 350.3 414.3 336 432 336z"/>
						</svg>
					</div>
				<?php } elseif ( $the_options['show_again_icon' . $suffix] === "shield" ) { ?>
					<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" 
						style="
							position: fixed; 
							display:none; 
							bottom: 10px; 
							color: <?php echo esc_html($the_options['button_revoke_consent_text_color' . $suffix]); ?>; 
							background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color' . $suffix]); ?>; 
							<?php if($the_options['show_again_position' . $suffix] === 'right') 
								echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; 
							else 
								echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> 
							border-radius: 50%; 
							height: 50px;
							width: 50px;
							padding: 4px !important;
							box-shadow: 0px 6px 11px gray;"
					>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
							<path fill="currentColor" stroke="currentColor" d="M320 64C324.6 64 329.2 65 333.4 66.9L521.8 146.8C543.8 156.1 560.2 177.8 560.1 204C559.6 303.2 518.8 484.7 346.5 567.2C329.8 575.2 310.4 575.2 293.7 567.2C121.3 484.7 80.6 303.2 80.1 204C80 177.8 96.4 156.1 118.4 146.8L306.7 66.9C310.9 65 315.4 64 320 64z"/>
						</svg>
					</div>
				<?php } elseif ( $the_options['show_again_icon' . $suffix] === "gear" ) { ?>
					<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" 
						style="
							position: fixed; 
							display:none; 
							bottom: 10px; 
							color: <?php echo esc_html($the_options['button_revoke_consent_text_color' . $suffix]); ?>; 
							background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color' . $suffix]); ?>; 
							<?php if($the_options['show_again_position' . $suffix] === 'right') 
								echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; 
							else 
								echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> 
							border-radius: 50%; 
							height: 50px;
							width: 50px;
							padding: 4px !important;
							box-shadow: 0px 6px 11px gray;"
					>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
							<path fill="currentColor" stroke="currentColor" d="M259.1 73.5C262.1 58.7 275.2 48 290.4 48L350.2 48C365.4 48 378.5 58.7 381.5 73.5L396 143.5C410.1 149.5 423.3 157.2 435.3 166.3L503.1 143.8C517.5 139 533.3 145 540.9 158.2L570.8 210C578.4 223.2 575.7 239.8 564.3 249.9L511 297.3C511.9 304.7 512.3 312.3 512.3 320C512.3 327.7 511.8 335.3 511 342.7L564.4 390.2C575.8 400.3 578.4 417 570.9 430.1L541 481.9C533.4 495 517.6 501.1 503.2 496.3L435.4 473.8C423.3 482.9 410.1 490.5 396.1 496.6L381.7 566.5C378.6 581.4 365.5 592 350.4 592L290.6 592C275.4 592 262.3 581.3 259.3 566.5L244.9 496.6C230.8 490.6 217.7 482.9 205.6 473.8L137.5 496.3C123.1 501.1 107.3 495.1 99.7 481.9L69.8 430.1C62.2 416.9 64.9 400.3 76.3 390.2L129.7 342.7C128.8 335.3 128.4 327.7 128.4 320C128.4 312.3 128.9 304.7 129.7 297.3L76.3 249.8C64.9 239.7 62.3 223 69.8 209.9L99.7 158.1C107.3 144.9 123.1 138.9 137.5 143.7L205.3 166.2C217.4 157.1 230.6 149.5 244.6 143.4L259.1 73.5zM320.3 400C364.5 399.8 400.2 363.9 400 319.7C399.8 275.5 363.9 239.8 319.7 240C275.5 240.2 239.8 276.1 240 320.3C240.2 364.5 276.1 400.2 320.3 400z"/>
						</svg>
					</div>
				<?php } else {
					$revoke_icon_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_REVOKE_ICON . $suffix );
					
					if (!empty($revoke_icon_img)) { ?>
					<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>"
						style="
							position: fixed; 
							display:none; 
							bottom: 10px;  
							height: 50px;
							width: 50px;
							<?php if($the_options['show_again_position' . $suffix] === 'right') 
								echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; 
							else 
								echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> 
							padding:0 !important;"
					>
						<img 
							width="50"
							height="50"
							style="
								height: 100%;
								width: 100%;
								object-fit: contain;
							" 
							alt="revoke-icon" 
							src="<?php echo esc_url_raw( $revoke_icon_img ); ?>" 
						>
					</div>
					<?php } else { ?>
						<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" 
							style="
								position: fixed; 
								display:none; 
								bottom: 10px; 
								color: <?php echo esc_html($the_options['button_revoke_consent_text_color' . $suffix]); ?>; 
								background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color' . $suffix]); ?>; 
								<?php if($the_options['show_again_position' . $suffix] === 'right') 
									echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; 
								else 
									echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> 
								border-radius: 50%; 
								height: 50px;
								width: 50px;
								padding: 4px !important;
								box-shadow: 0px 6px 11px gray;"
						>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
								<path fill="currentColor" stroke="currentColor" d="M321.5 91.6C320.7 86.2 316.6 81.8 311.2 81C289.1 77.9 266.6 81.9 246.8 92.4L172.8 131.9C153.1 142.4 137.2 158.9 127.4 179L90.7 254.6C80.9 274.7 77.7 297.5 81.6 319.5L96.1 402.3C100 424.4 110.7 444.6 126.8 460.2L187.1 518.6C203.2 534.2 223.7 544.2 245.8 547.3L328.8 559C350.9 562.1 373.4 558.1 393.2 547.6L467.2 508.1C486.9 497.6 502.8 481.1 512.6 460.9L549.3 385.4C559.1 365.3 562.3 342.5 558.4 320.5C557.5 315.2 553.1 311.2 547.8 310.4C496.3 302.2 455 263.3 443.3 213C441.5 205.4 435.3 199.6 427.6 198.4C373 189.7 329.9 146.4 321.4 91.6zM272 208C289.7 208 304 222.3 304 240C304 257.7 289.7 272 272 272C254.3 272 240 257.7 240 240C240 222.3 254.3 208 272 208zM208 400C208 382.3 222.3 368 240 368C257.7 368 272 382.3 272 400C272 417.7 257.7 432 240 432C222.3 432 208 417.7 208 400zM432 336C449.7 336 464 350.3 464 368C464 385.7 449.7 400 432 400C414.3 400 400 385.7 400 368C400 350.3 414.3 336 432 336z"/>
							</svg>
						</div>
					<?php }
				} ?>
		<?php }
	}
}
if ( ! empty( $the_options['gdpr_notify'] )) {
	if ( ! empty( $the_options['cookie_data'] ) ) {
			?>
			<div class="gdpr_messagebar_detail layout-classic <?php echo esc_html( $the_options['template_parts'] ); ?> <?php echo esc_html( $the_options['theme_class'] ); ?>">
			<?php include plugin_dir_path( __FILE__ ) . 'modals/cookie_settings.php'; ?>
		</div>
		<?php
	}
	if ( ! empty( $the_options['show_again' . $suffix] ) ) {
		?>
		<?php if ( $the_options['show_again_as' . $suffix] === "text" ) { ?>
			<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" style="position: fixed; display:none; bottom: 10px; color: <?php echo esc_html($the_options['button_revoke_consent_text_color' . $suffix]); ?>; background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color' . $suffix]); ?>; <?php if($the_options['show_again_position' . $suffix] === 'right') echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; else echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> border-radius: 5px; box-shadow: 0px 6px 11px gray;">
				<span><?php echo esc_html( $cookie_data['dash_show_again_text'] ); //phpcs:ignore ?></span>
			</div>
		<?php } elseif ( $the_options['show_again_as' . $suffix] === "icon" ) { ?>
				<?php if ( $the_options['show_again_icon' . $suffix] === "cookie" ) { ?>
					<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" 
						style="
							position: fixed; 
							display:none; 
							bottom: 10px; 
							color: <?php echo esc_html($the_options['button_revoke_consent_text_color' . $suffix]); ?>; 
							background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color' . $suffix]); ?>; 
							<?php if($the_options['show_again_position' . $suffix] === 'right') 
								echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; 
							else 
								echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> 
							border-radius: 50%; 
							height: 50px;
							width: 50px;
							padding: 4px !important;
							box-shadow: 0px 6px 11px gray;"
					>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
							<path fill="currentColor" stroke="currentColor" d="M321.5 91.6C320.7 86.2 316.6 81.8 311.2 81C289.1 77.9 266.6 81.9 246.8 92.4L172.8 131.9C153.1 142.4 137.2 158.9 127.4 179L90.7 254.6C80.9 274.7 77.7 297.5 81.6 319.5L96.1 402.3C100 424.4 110.7 444.6 126.8 460.2L187.1 518.6C203.2 534.2 223.7 544.2 245.8 547.3L328.8 559C350.9 562.1 373.4 558.1 393.2 547.6L467.2 508.1C486.9 497.6 502.8 481.1 512.6 460.9L549.3 385.4C559.1 365.3 562.3 342.5 558.4 320.5C557.5 315.2 553.1 311.2 547.8 310.4C496.3 302.2 455 263.3 443.3 213C441.5 205.4 435.3 199.6 427.6 198.4C373 189.7 329.9 146.4 321.4 91.6zM272 208C289.7 208 304 222.3 304 240C304 257.7 289.7 272 272 272C254.3 272 240 257.7 240 240C240 222.3 254.3 208 272 208zM208 400C208 382.3 222.3 368 240 368C257.7 368 272 382.3 272 400C272 417.7 257.7 432 240 432C222.3 432 208 417.7 208 400zM432 336C449.7 336 464 350.3 464 368C464 385.7 449.7 400 432 400C414.3 400 400 385.7 400 368C400 350.3 414.3 336 432 336z"/>
						</svg>
					</div>
				<?php } elseif ( $the_options['show_again_icon' . $suffix] === "shield" ) { ?>
					<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" 
						style="
							position: fixed; 
							display:none; 
							bottom: 10px; 
							color: <?php echo esc_html($the_options['button_revoke_consent_text_color' . $suffix]); ?>; 
							background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color' . $suffix]); ?>; 
							<?php if($the_options['show_again_position' . $suffix] === 'right') 
								echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; 
							else 
								echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> 
							border-radius: 50%; 
							height: 50px;
							width: 50px;
							padding: 4px !important;
							box-shadow: 0px 6px 11px gray;"
					>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
							<path fill="currentColor" stroke="currentColor" d="M320 64C324.6 64 329.2 65 333.4 66.9L521.8 146.8C543.8 156.1 560.2 177.8 560.1 204C559.6 303.2 518.8 484.7 346.5 567.2C329.8 575.2 310.4 575.2 293.7 567.2C121.3 484.7 80.6 303.2 80.1 204C80 177.8 96.4 156.1 118.4 146.8L306.7 66.9C310.9 65 315.4 64 320 64z"/>
						</svg>
					</div>
				<?php } elseif ( $the_options['show_again_icon' . $suffix] === "gear" ) { ?>
					<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" 
						style="
							position: fixed; 
							display:none; 
							bottom: 10px; 
							color: <?php echo esc_html($the_options['button_revoke_consent_text_color' . $suffix]); ?>; 
							background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color' . $suffix]); ?>; 
							<?php if($the_options['show_again_position' . $suffix] === 'right') 
								echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; 
							else 
								echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> 
							border-radius: 50%; 
							height: 50px;
							width: 50px;
							padding: 4px !important;
							box-shadow: 0px 6px 11px gray;"
					>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
							<path fill="currentColor" stroke="currentColor" d="M259.1 73.5C262.1 58.7 275.2 48 290.4 48L350.2 48C365.4 48 378.5 58.7 381.5 73.5L396 143.5C410.1 149.5 423.3 157.2 435.3 166.3L503.1 143.8C517.5 139 533.3 145 540.9 158.2L570.8 210C578.4 223.2 575.7 239.8 564.3 249.9L511 297.3C511.9 304.7 512.3 312.3 512.3 320C512.3 327.7 511.8 335.3 511 342.7L564.4 390.2C575.8 400.3 578.4 417 570.9 430.1L541 481.9C533.4 495 517.6 501.1 503.2 496.3L435.4 473.8C423.3 482.9 410.1 490.5 396.1 496.6L381.7 566.5C378.6 581.4 365.5 592 350.4 592L290.6 592C275.4 592 262.3 581.3 259.3 566.5L244.9 496.6C230.8 490.6 217.7 482.9 205.6 473.8L137.5 496.3C123.1 501.1 107.3 495.1 99.7 481.9L69.8 430.1C62.2 416.9 64.9 400.3 76.3 390.2L129.7 342.7C128.8 335.3 128.4 327.7 128.4 320C128.4 312.3 128.9 304.7 129.7 297.3L76.3 249.8C64.9 239.7 62.3 223 69.8 209.9L99.7 158.1C107.3 144.9 123.1 138.9 137.5 143.7L205.3 166.2C217.4 157.1 230.6 149.5 244.6 143.4L259.1 73.5zM320.3 400C364.5 399.8 400.2 363.9 400 319.7C399.8 275.5 363.9 239.8 319.7 240C275.5 240.2 239.8 276.1 240 320.3C240.2 364.5 276.1 400.2 320.3 400z"/>
						</svg>
					</div>
				<?php } else {
					$revoke_icon_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_REVOKE_ICON . $suffix );
					
					if (!empty($revoke_icon_img)) { ?>
					<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>"
						style="
							position: fixed; 
							display:none; 
							bottom: 10px;  
							height: 50px;
							width: 50px;
							<?php if($the_options['show_again_position' . $suffix] === 'right') 
								echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; 
							else 
								echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> 
							padding:0 !important"
					>
						<img 
							width="50"
							height="50"
							style="
								height: 100%;
								width: 100%;
								object-fit: contain;
							" 
							alt="revoke-icon" 
							src="<?php echo esc_url_raw( $revoke_icon_img ); ?>" 
						>
					</div>
					<?php } else { ?>
						<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" 
							style="
								position: fixed; 
								display:none; 
								bottom: 10px; 
								color: <?php echo esc_html($the_options['button_revoke_consent_text_color' . $suffix]); ?>; 
								background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color' . $suffix]); ?>; 
								<?php if($the_options['show_again_position' . $suffix] === 'right') 
									echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; 
								else 
									echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> 
								border-radius: 50%; 
								height: 50px;
								width: 50px;
								padding: 4px !important;
								box-shadow: 0px 6px 11px gray;"
						>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
								<path fill="currentColor" stroke="currentColor" d="M321.5 91.6C320.7 86.2 316.6 81.8 311.2 81C289.1 77.9 266.6 81.9 246.8 92.4L172.8 131.9C153.1 142.4 137.2 158.9 127.4 179L90.7 254.6C80.9 274.7 77.7 297.5 81.6 319.5L96.1 402.3C100 424.4 110.7 444.6 126.8 460.2L187.1 518.6C203.2 534.2 223.7 544.2 245.8 547.3L328.8 559C350.9 562.1 373.4 558.1 393.2 547.6L467.2 508.1C486.9 497.6 502.8 481.1 512.6 460.9L549.3 385.4C559.1 365.3 562.3 342.5 558.4 320.5C557.5 315.2 553.1 311.2 547.8 310.4C496.3 302.2 455 263.3 443.3 213C441.5 205.4 435.3 199.6 427.6 198.4C373 189.7 329.9 146.4 321.4 91.6zM272 208C289.7 208 304 222.3 304 240C304 257.7 289.7 272 272 272C254.3 272 240 257.7 240 240C240 222.3 254.3 208 272 208zM208 400C208 382.3 222.3 368 240 368C257.7 368 272 382.3 272 400C272 417.7 257.7 432 240 432C222.3 432 208 417.7 208 400zM432 336C449.7 336 464 350.3 464 368C464 385.7 449.7 400 432 400C414.3 400 400 385.7 400 368C400 350.3 414.3 336 432 336z"/>
							</svg>
						</div>
					<?php }
				} ?>
		<?php }
	}
}
if ( ! empty( $the_options['eprivacy_notify'] ) ) {
	if ( ! empty( $the_options['show_again' . $suffix] ) ) {
		?>
		<?php if ( $the_options['show_again_as' . $suffix] === "text" ) { ?>
			<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" style="position: fixed; display:none; bottom: 10px; color: <?php echo esc_html($the_options['button_revoke_consent_text_color' . $suffix]); ?>; background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color' . $suffix]); ?>; <?php if($the_options['show_again_position' . $suffix] === 'right') echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; else echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> border-radius: 5px; box-shadow: 0px 6px 11px gray;">
				<span><?php echo esc_html( $cookie_data['dash_show_again_text'] );//phpcs:ignore ?></span>
			</div>
		<?php } elseif ( $the_options['show_again_as' . $suffix] === "icon" ) {?>
				<?php if ( $the_options['show_again_icon' . $suffix] === "cookie" ) { ?>
					<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" 
						style="
							position: fixed; 
							display:none; 
							bottom: 10px; 
							color: <?php echo esc_html($the_options['button_revoke_consent_text_color' . $suffix]); ?>; 
							background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color' . $suffix]); ?>; 
							<?php if($the_options['show_again_position' . $suffix] === 'right') 
								echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; 
							else 
								echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> 
							border-radius: 50%; 
							height: 50px;
							width: 50px;
							padding: 4px !important;
							box-shadow: 0px 6px 11px gray;"
					>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
							<path fill="currentColor" stroke="currentColor" d="M321.5 91.6C320.7 86.2 316.6 81.8 311.2 81C289.1 77.9 266.6 81.9 246.8 92.4L172.8 131.9C153.1 142.4 137.2 158.9 127.4 179L90.7 254.6C80.9 274.7 77.7 297.5 81.6 319.5L96.1 402.3C100 424.4 110.7 444.6 126.8 460.2L187.1 518.6C203.2 534.2 223.7 544.2 245.8 547.3L328.8 559C350.9 562.1 373.4 558.1 393.2 547.6L467.2 508.1C486.9 497.6 502.8 481.1 512.6 460.9L549.3 385.4C559.1 365.3 562.3 342.5 558.4 320.5C557.5 315.2 553.1 311.2 547.8 310.4C496.3 302.2 455 263.3 443.3 213C441.5 205.4 435.3 199.6 427.6 198.4C373 189.7 329.9 146.4 321.4 91.6zM272 208C289.7 208 304 222.3 304 240C304 257.7 289.7 272 272 272C254.3 272 240 257.7 240 240C240 222.3 254.3 208 272 208zM208 400C208 382.3 222.3 368 240 368C257.7 368 272 382.3 272 400C272 417.7 257.7 432 240 432C222.3 432 208 417.7 208 400zM432 336C449.7 336 464 350.3 464 368C464 385.7 449.7 400 432 400C414.3 400 400 385.7 400 368C400 350.3 414.3 336 432 336z"/>
						</svg>
					</div>
				<?php } elseif ( $the_options['show_again_icon' . $suffix] === "shield" ) { ?>
					<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" 
						style="
							position: fixed; 
							display:none; 
							bottom: 10px; 
							color: <?php echo esc_html($the_options['button_revoke_consent_text_color' . $suffix]); ?>; 
							background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color' . $suffix]); ?>; 
							<?php if($the_options['show_again_position' . $suffix] === 'right') 
								echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; 
							else 
								echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> 
							border-radius: 50%; 
							height: 50px;
							width: 50px;
							padding: 4px !important;
							box-shadow: 0px 6px 11px gray;"
					>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
							<path fill="currentColor" stroke="currentColor" d="M320 64C324.6 64 329.2 65 333.4 66.9L521.8 146.8C543.8 156.1 560.2 177.8 560.1 204C559.6 303.2 518.8 484.7 346.5 567.2C329.8 575.2 310.4 575.2 293.7 567.2C121.3 484.7 80.6 303.2 80.1 204C80 177.8 96.4 156.1 118.4 146.8L306.7 66.9C310.9 65 315.4 64 320 64z"/>
						</svg>
					</div>
				<?php } elseif ( $the_options['show_again_icon' . $suffix] === "gear" ) { ?>
					<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" 
						style="
							position: fixed; 
							display:none; 
							bottom: 10px; 
							color: <?php echo esc_html($the_options['button_revoke_consent_text_color' . $suffix]); ?>; 
							background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color' . $suffix]); ?>; 
							<?php if($the_options['show_again_position' . $suffix] === 'right') 
								echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; 
							else 
								echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> 
							border-radius: 50%; 
							height: 50px;
							width: 50px;
							padding: 4px !important;
							box-shadow: 0px 6px 11px gray;"
					>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
							<path fill="currentColor" stroke="currentColor" d="M259.1 73.5C262.1 58.7 275.2 48 290.4 48L350.2 48C365.4 48 378.5 58.7 381.5 73.5L396 143.5C410.1 149.5 423.3 157.2 435.3 166.3L503.1 143.8C517.5 139 533.3 145 540.9 158.2L570.8 210C578.4 223.2 575.7 239.8 564.3 249.9L511 297.3C511.9 304.7 512.3 312.3 512.3 320C512.3 327.7 511.8 335.3 511 342.7L564.4 390.2C575.8 400.3 578.4 417 570.9 430.1L541 481.9C533.4 495 517.6 501.1 503.2 496.3L435.4 473.8C423.3 482.9 410.1 490.5 396.1 496.6L381.7 566.5C378.6 581.4 365.5 592 350.4 592L290.6 592C275.4 592 262.3 581.3 259.3 566.5L244.9 496.6C230.8 490.6 217.7 482.9 205.6 473.8L137.5 496.3C123.1 501.1 107.3 495.1 99.7 481.9L69.8 430.1C62.2 416.9 64.9 400.3 76.3 390.2L129.7 342.7C128.8 335.3 128.4 327.7 128.4 320C128.4 312.3 128.9 304.7 129.7 297.3L76.3 249.8C64.9 239.7 62.3 223 69.8 209.9L99.7 158.1C107.3 144.9 123.1 138.9 137.5 143.7L205.3 166.2C217.4 157.1 230.6 149.5 244.6 143.4L259.1 73.5zM320.3 400C364.5 399.8 400.2 363.9 400 319.7C399.8 275.5 363.9 239.8 319.7 240C275.5 240.2 239.8 276.1 240 320.3C240.2 364.5 276.1 400.2 320.3 400z"/>
						</svg>
					</div>
				<?php } else {
					$revoke_icon_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_REVOKE_ICON . $suffix );
					
					if (!empty($revoke_icon_img)) { ?>
					<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>"
						style="
							position: fixed; 
							display:none; 
							bottom: 10px;  
							<?php if($the_options['show_again_position' . $suffix] === 'right') 
								echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; 
							else 
								echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> 
							height: 50px;
							width: 50px;
							padding:0 !important;"
					>
						<img 
							width="50"
							height="50"
							style="
								height: 100%;
								width: 100%;
								object-fit: contain;
							" 
							alt="revoke-icon" 
							src="<?php echo esc_url_raw( $revoke_icon_img ); ?>" 
						>
					</div>
					<?php } else { ?>
						<div id="<?php echo esc_html( $the_options['show_again_container_id'] ); ?>" 
							style="
								position: fixed; 
								display:none; 
								bottom: 10px; 
								color: <?php echo esc_html($the_options['button_revoke_consent_text_color' . $suffix]); ?>; 
								background-color: <?php echo esc_html($the_options['button_revoke_consent_background_color' . $suffix]); ?>; 
								<?php if($the_options['show_again_position' . $suffix] === 'right') 
									echo "right: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; 
								else 
									echo "left: ". esc_html($the_options['show_again_margin' . $suffix]) . "%;"; ?> 
								border-radius: 50%; 
								height: 50px;
								width: 50px;
								padding: 4px !important;
								box-shadow: 0px 6px 11px gray;"
						>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
								<path fill="currentColor" stroke="currentColor" d="M321.5 91.6C320.7 86.2 316.6 81.8 311.2 81C289.1 77.9 266.6 81.9 246.8 92.4L172.8 131.9C153.1 142.4 137.2 158.9 127.4 179L90.7 254.6C80.9 274.7 77.7 297.5 81.6 319.5L96.1 402.3C100 424.4 110.7 444.6 126.8 460.2L187.1 518.6C203.2 534.2 223.7 544.2 245.8 547.3L328.8 559C350.9 562.1 373.4 558.1 393.2 547.6L467.2 508.1C486.9 497.6 502.8 481.1 512.6 460.9L549.3 385.4C559.1 365.3 562.3 342.5 558.4 320.5C557.5 315.2 553.1 311.2 547.8 310.4C496.3 302.2 455 263.3 443.3 213C441.5 205.4 435.3 199.6 427.6 198.4C373 189.7 329.9 146.4 321.4 91.6zM272 208C289.7 208 304 222.3 304 240C304 257.7 289.7 272 272 272C254.3 272 240 257.7 240 240C240 222.3 254.3 208 272 208zM208 400C208 382.3 222.3 368 240 368C257.7 368 272 382.3 272 400C272 417.7 257.7 432 240 432C222.3 432 208 417.7 208 400zM432 336C449.7 336 464 350.3 464 368C464 385.7 449.7 400 432 400C414.3 400 400 385.7 400 368C400 350.3 414.3 336 432 336z"/>
							</svg>
						</div>
					<?php }
				} ?>
		<?php
		}
	}
}

if ( ! empty( $the_options['ccpa_notify'] ) ) {
	?>
	<div class="ccpa_messagebar_detail layout-classic <?php echo esc_html( $the_options['template_parts'] ); ?>">
		<?php include plugin_dir_path( __FILE__ ) . 'modals/cookie_settings.php'; ?>
	</div>
	<?php
		if ( ! empty( $the_options['ccpa_show_again' . $suffix] ) ) {
		?>
		
		<div id="<?php echo esc_html( $the_options['ccpa_show_again_container_id'] ); ?>" style="position: fixed; display:none; bottom: 10px; color: <?php echo esc_html($the_options['ccpa_button_revoke_consent_text_color' . $suffix]); ?>; background-color: <?php echo esc_html($the_options['ccpa_button_revoke_consent_background_color' . $suffix]); ?>; <?php if($the_options['ccpa_show_again_position' . $suffix] === 'right') echo "right: ". esc_html($the_options['ccpa_show_again_margin' . $suffix]) . "%;"; else echo "left: ". esc_html($the_options['ccpa_show_again_margin' . $suffix]) . "%;"; ?> border-radius: 5px; box-shadow: 0px 6px 11px gray;">
			<?php
		?>
			<span><?php echo esc_html( $cookie_data['ccpa_dash_show_again_text'] );//phpcs:ignore ?></span>
		</div>
		<?php
	}
}