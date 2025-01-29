<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://club.wpeka.com
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 */

if ( ! empty( $the_options['gdpr_notify'] ) ) {
	$ab_options = get_option('wpl_ab_options');
	?>
	<div class="gdpr_messagebar_content">
		<div class="gdpr_message_bar_column_desc_heading">
		<div class="gdpr_logo_container">
		<?php
		if($ab_options['ab_testing_enabled'] === "false" || $ab_options['ab_testing_enabled'] === false){
			if($the_options['cookie_usage_for'] == 'both'){
				$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELDML1 );
				if ( '' !== $get_banner_img ) {
					?>
						<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
						<?php
				}
			}else{
				$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
				if ( '' !== $get_banner_img ) {
					?>
						<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
						<?php
				}
			}
		}
		else{
			if($ab_options['ab_testing_enabled'] === "true" || $ab_options['ab_testing_enabled'] === true){
				if($chosenBanner == 1) {
					$get_banner_img1 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1 );
					if ( '' !== $get_banner_img1 ) {
						?>
							<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img1 ); ?>" >
							<?php
					}
					}elseif($chosenBanner == 2){
						$get_banner_img2 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD2 );
					if ( '' !== $get_banner_img2 ) {
						?>
							<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img2 ); ?>" >
							<?php
					}
					}
			}
		} ?>
		</div>
		<?php
			if ( '' !== $the_options['head'] ) {
				?>
				<h3 class="gdpr_messagebar_head">
					<?php
				echo esc_html__( $the_options['head'], 'gdpr-cookie-consent' ); //phpcs:ignore
				?>
	</h3>
	<?php
			}
			?>
			<div class="group-description" tabindex="0">
		<p class="gdpr"><?php echo $the_options['is_iabtcf_on'] ? $cookie_data['dash_notify_message_iabtcf']: esc_html__( $cookie_data['dash_notify_message'], '<a><br><em><strong><span><p><i><img><b><div><label>' ); ?>
		<?php
		if ( ! empty( $the_options['button_readmore_is_on'] ) ) {
			?>
			<a id="cookie_action_link" href="<?php echo esc_html( $the_options['button_readmore_url_link'] ); ?>" class="<?php echo esc_html( $the_options['button_readmore_classes'] ); ?>"
			<?php
			if ( ! empty( $the_options['button_readmore_new_win'] ) ) {
				?>
				target="_blank"
				<?php
			}
			?>
	><?php echo esc_html__( $cookie_data['dash_button_readmore_text'], 'gdpr-cookie-consent' ); //phpcs:ignore ?></a></div>
			<?php
		}
		?>
			</p>
			<?php
			if ( ! empty( $the_options['ccpa_notify'] ) ) {
				?>
				<?php 
		if($the_options['cookie_usage_for'] == 'both') { 
			?>
			<p class="ccpa"><?php echo wp_kses_post( $cookie_data['dash_notify_message_ccpa'], '<a><br><em><strong><span><p><i><img><b><div><label>' ); ?>
			<?php
			if ( ! empty( $the_options['button_donotsell_is_on'] ) ) {
				?>
				<a data-toggle="gdprmodal" href="#" class="<?php echo esc_html( $the_options['button_donotsell_classes'] ); ?>" data-gdpr_action="donotsell" id="cookie_donotsell_link"
				><?php echo esc_html__( $the_options['button_donotsell_text1'], 'gdpr-cookie-consent' ); //phpcs:ignore ?></a>
				<?php
			}
			?>
		</p>
		<?php } else { ?>
			<p class="ccpa"><?php echo wp_kses_post( $cookie_data['dash_notify_message_ccpa'], '<a><br><em><strong><span><p><i><img><b><div><label>' ); ?>
			<?php
			if ( ! empty( $the_options['button_donotsell_is_on'] ) ) {
				?>
				<a data-toggle="gdprmodal" href="#" class="<?php echo esc_html( $the_options['button_donotsell_classes'] ); ?>" data-gdpr_action="donotsell" id="cookie_donotsell_link"
				><?php echo esc_html__($cookie_data['dash_button_donotsell_text'], 'gdpr-cookie-consent' ); //phpcs:ignore ?></a>
				<?php
			}
			?>
		</p>
			<?php } ?>
				<?php
			}
			?>
		</div>
		<?php if($ab_options['ab_testing_enabled'] === "false" || $ab_options['ab_testing_enabled'] === false) { ?>
			<?php if($the_options['cookie_usage_for'] == 'both') { ?>
				<div class="gdpr group-description-buttons" id="default_buttons">
					<?php
					if ( ! empty( $the_options['button_accept_is_on1'] ) && $the_options['button_accept_is_on1'] == 'true' ) {
						?>
						<a id="cookie_action_accept" class="<?php echo esc_html( $the_options['button_accept_classes'] ); ?>" tabindex="0" aria-label="Accept"
						<?php
						if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_action'] ) {
							?>
							href="<?php echo esc_html( $the_options['button_accept_url'] ); ?>"
							<?php
							if ( ! empty( $the_options['button_accept_new_win'] ) ) {
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
					data-gdpr_action="accept" ><?php echo esc_html__( $the_options['button_accept_text1'], 'gdpr-cookie-consent' ); //phpcs:ignore ?></a>
						<?php
					}
					if ( ! empty( $the_options['button_accept_all_is_on1'] ) && $the_options['button_accept_all_is_on1'] == 'true' ) {
						
						?>
						<a id="cookie_action_accept_all" class="<?php echo esc_html( $the_options['button_accept_all_classes'] ); ?>" tabindex="0" aria-label="Accept All"
						<?php
						if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_all_action1'] ) {
							?>
							href="<?php echo esc_html( $the_options['button_accept_all_url1'] ); ?>"
							<?php
							if ( ! empty( $the_options['button_accept_all_new_win1'] ) ) {
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
				data-gdpr_action="accept_all" ><?php echo esc_html__( $the_options['button_accept_all_text1'], 'gdpr-cookie-consent' );//phpcs:ignore  ?></a>
						<?php
					}
					if ( ! empty( $the_options['button_decline_is_on1'] ) && $the_options['button_decline_is_on1'] == 'true'  ) {
						?>
						<a id="cookie_action_reject" class="<?php echo esc_html( $the_options['button_decline_classes'] ); ?>" tabindex="0" aria-label="Reject"
						<?php
						if ( 'CONSTANT_OPEN_URL' === $the_options['button_decline_action1'] ) {
							?>
							href="<?php echo esc_html( $the_options['button_decline_url1'] ); ?>"
							<?php
							if ( ! empty( $the_options['button_decline_new_win1'] ) ) {
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
						data-gdpr_action="reject" ><?php echo esc_html__( $the_options['button_decline_text1'], 'gdpr-cookie-consent' ); //phpcs:ignore ?></a>
						<?php
					}
					if ( ! empty( $the_options['button_settings_is_on1'] ) && $the_options['button_settings_is_on1'] == 'true' ) {
						?>
						<a id="cookie_action_settings" class="<?php echo esc_html( $the_options['button_settings_classes'] ); ?>" tabindex="0" aria-label="Cookie Settings" href="#"
						<?php
						if ( ! $the_options['button_settings_as_button1'] ) {
							?>
							href="#"
							<?php
						}
						if ( 'banner' === $the_options['cookie_bar_as'] && ! $the_options['button_settings_as_popup'] ) {
							?>
							data-gdpr_action="show_settings"
							<?php
						} else {
							?>
							data-gdpr_action="settings" data-toggle="gdprmodal" data-target="#gdpr-gdprmodal"
							<?php
						}
						?>
						><?php echo esc_html__( $the_options['button_settings_text1'], 'gdpr-cookie-consent' );//phpcs:ignore  ?></a>
						<?php
					}
					?>
				</div>
			<?php } else {?>
				<div class="gdpr group-description-buttons" id="default_buttons">
				
					<?php
					if ( ! empty( $the_options['button_accept_is_on'] ) ) {
						?>
						<a id="cookie_action_accept" class="<?php echo esc_html( $the_options['button_accept_classes'] ); ?>" tabindex="0" aria-label="Accept"
						<?php
						if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_action'] ) {
							?>
							href="<?php echo esc_html( $the_options['button_accept_url'] ); ?>"
							<?php
							if ( ! empty( $the_options['button_accept_new_win'] ) ) {
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
					data-gdpr_action="accept" ><?php echo esc_html__($cookie_data['dash_button_accept_text'], 'gdpr-cookie-consent' ); //phpcs:ignore ?></a>
						<?php
					}
					if ( ! empty( $the_options['button_accept_all_is_on'] ) ) {
						?>
						<a id="cookie_action_accept_all" class="<?php echo esc_html( $the_options['button_accept_all_classes'] ); ?>" tabindex="0" aria-label="Accept All"
						<?php
						if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_all_action'] ) {
							?>
							href="<?php echo esc_html( $the_options['button_accept_all_url'] ); ?>"
							<?php
							if ( ! empty( $the_options['button_accept_all_new_win'] ) ) {
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
				data-gdpr_action="accept_all" ><?php echo esc_html__($cookie_data['dash_button_accept_all_text'], 'gdpr-cookie-consent' );//phpcs:ignore  ?></a>
						<?php
					}
					if ( ! empty( $the_options['button_decline_is_on'] ) ) {
						?>
						<a id="cookie_action_reject" class="<?php echo esc_html( $the_options['button_decline_classes'] ); ?>" tabindex="0" aria-label="Reject"
						<?php
						if ( 'CONSTANT_OPEN_URL' === $the_options['button_decline_action'] ) {
							?>
							href="<?php echo esc_html( $the_options['button_decline_url'] ); ?>"
							<?php
							if ( ! empty( $the_options['button_decline_new_win'] ) ) {
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
						data-gdpr_action="reject" ><?php echo esc_html__($cookie_data['dash_button_decline_text'], 'gdpr-cookie-consent' ); //phpcs:ignore ?></a>
						<?php
					}
					if ( ! empty( $the_options['button_settings_is_on'] ) ) {
						?>
						<a id="cookie_action_settings" class="<?php echo esc_html( $the_options['button_settings_classes'] ); ?>" tabindex="0" aria-label="Cookie Settings" href="#"
						<?php
						if ( ! $the_options['button_settings_as_button'] ) {
							?>
							href="#"
							<?php
						}
						if ( 'banner' === $the_options['cookie_bar_as'] && ! $the_options['button_settings_as_popup'] ) {
							?>
							data-gdpr_action="show_settings"
							<?php
						} else {
							?>
							data-gdpr_action="settings" data-toggle="gdprmodal" data-target="#gdpr-gdprmodal"
							<?php
						}
						?>
						><?php echo esc_html__($cookie_data['dash_button_settings_text'], 'gdpr-cookie-consent' );//phpcs:ignore  ?></a>
						<?php
					}
					?>
				</div>
				<?php } ?>
		<?php } else { 
			if($chosenBanner == 1) { ?>
				<div class="gdpr group-description-buttons" id="default_buttons">
					
						<?php
						if ( !empty( $the_options['button_accept_is_on1'] ) && ($the_options['button_accept_is_on1'] === "true" || $the_options['button_accept_is_on1'] === "1"  || $the_options['button_accept_is_on1'] === true ) ) {
							?>
							<a id="cookie_action_accept" class="<?php echo esc_html( $the_options['button_accept_classes'] ); ?>" tabindex="0" aria-label="Accept"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_action1'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_accept_url1'] ); ?>"
								<?php
								if ( ! empty( $the_options['button_accept_new_win1'] ) ) {
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
						data-gdpr_action="accept" ><?php echo esc_html__( $the_options['button_accept_text1'], 'gdpr-cookie-consent' ); //phpcs:ignore ?></a>
							<?php
						}
						if ( !empty( $the_options['button_accept_all_is_on1'] ) && ($the_options['button_accept_all_is_on1'] === "true" || $the_options['button_accept_all_is_on1'] === "1" || $the_options['button_accept_all_is_on1'] === true) ) {
							?>
							<a id="cookie_action_accept_all" class="<?php echo esc_html( $the_options['button_accept_all_classes'] ); ?>" tabindex="0" aria-label="Accept All"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_all_action1'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_accept_all_url1'] ); ?>"
								<?php
								if ( ! empty( $the_options['button_accept_all_new_win1'] ) ) {
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
					data-gdpr_action="accept_all" ><?php echo esc_html__( $the_options['button_accept_all_text1'], 'gdpr-cookie-consent' );//phpcs:ignore  ?></a>
							<?php
						}
						if ( !empty( $the_options['button_decline_is_on1'] ) && ($the_options['button_decline_is_on1'] === "true" || $the_options['button_decline_is_on1'] === "1"  || $the_options['button_decline_is_on1'] === true ) ) {
							?>
							<a id="cookie_action_reject" class="<?php echo esc_html( $the_options['button_decline_classes'] ); ?>" tabindex="0" aria-label="Reject"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_decline_action1'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_decline_url1'] ); ?>"
								<?php
								if ( ! empty( $the_options['button_decline_new_win1'] ) ) {
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
							data-gdpr_action="reject" ><?php echo esc_html__( $the_options['button_decline_text1'], 'gdpr-cookie-consent' ); //phpcs:ignore ?></a>
							<?php
						}
						if ( !empty( $the_options['button_settings_is_on1'] ) && ($the_options['button_settings_is_on1'] === "true" || $the_options['button_settings_is_on1'] === "1" || $the_options['button_settings_is_on1'] === true) ) {
							?>
							<a id="cookie_action_settings" class="<?php echo esc_html( $the_options['button_settings_classes'] ); ?>" tabindex="0" aria-label="Cookie Settings" href="#"
							<?php
							if ( ! $the_options['button_settings_as_button1'] ) {
								?>
								href="#"
								<?php
							}
							if ( 'banner' === $the_options['cookie_bar_as'] && ! $the_options['button_settings_as_popup'] ) {
								?>
								data-gdpr_action="show_settings"
								<?php
							} else {
								?>
								data-gdpr_action="settings" data-toggle="gdprmodal" data-target="#gdpr-gdprmodal"
								<?php
							}
							?>
							><?php echo esc_html__( $the_options['button_settings_text1'], 'gdpr-cookie-consent' );//phpcs:ignore  ?></a>
							<?php
						}
						?>
					</div>
		<?php } else { ?>
				<div class="gdpr group-description-buttons" id="default_buttons">
					
						<?php
						if ( ! empty( $the_options['button_accept_is_on2'] ) && ($the_options['button_accept_is_on2'] === "true" || $the_options['button_accept_is_on2'] === "1" || $the_options['button_accept_is_on2'] === true) ) {
							?>
							<a id="cookie_action_accept" class="<?php echo esc_html( $the_options['button_accept_classes'] ); ?>" tabindex="0" aria-label="Accept"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_action2'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_accept_url2'] ); ?>"
								<?php
								if ( ! empty( $the_options['button_accept_new_win2'] ) ) {
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
						data-gdpr_action="accept" ><?php echo esc_html__( $the_options['button_accept_text2'], 'gdpr-cookie-consent' ); //phpcs:ignore ?></a>
							<?php
						}
						if ( ! empty( $the_options['button_accept_all_is_on2'] ) && ($the_options['button_accept_all_is_on2'] === "true" || $the_options['button_accept_all_is_on2'] === "1" || $the_options['button_accept_all_is_on2'] === true) ) {
							?>
							<a id="cookie_action_accept_all" class="<?php echo esc_html( $the_options['button_accept_all_classes'] ); ?>" tabindex="0" aria-label="Accept All"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_all_action2'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_accept_all_url2'] ); ?>"
								<?php
								if ( ! empty( $the_options['button_accept_all_new_win2'] ) ) {
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
					data-gdpr_action="accept_all" ><?php echo esc_html__( $the_options['button_accept_all_text2'], 'gdpr-cookie-consent' );//phpcs:ignore  ?></a>
							<?php
						}
						if ( ! empty( $the_options['button_decline_is_on2'] ) && ($the_options['button_decline_is_on2'] === "true" || $the_options['button_decline_is_on2'] === "1" || $the_options['button_decline_is_on2'] === true) ) {
							?>
							<a id="cookie_action_reject" class="<?php echo esc_html( $the_options['button_decline_classes'] ); ?>" tabindex="0" aria-label="Reject"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_decline_action2'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_decline_url2'] ); ?>"
								<?php
								if ( ! empty( $the_options['button_decline_new_win2'] ) ) {
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
							data-gdpr_action="reject" ><?php echo esc_html__( $the_options['button_decline_text2'], 'gdpr-cookie-consent' ); //phpcs:ignore ?></a>
							<?php
						}
						if ( ! empty( $the_options['button_settings_is_on2'] ) && ($the_options['button_settings_is_on2'] === "true" || $the_options['button_settings_is_on2'] === "1" || $the_options['button_settings_is_on2'] === true) ) {
							?>
							<a id="cookie_action_settings" class="<?php echo esc_html( $the_options['button_settings_classes'] ); ?>" tabindex="0" aria-label="Cookie Settings" href="#"
							<?php
							if ( ! $the_options['button_settings_as_button2'] ) {
								?>
								href="#"
								<?php
							}
							if ( 'banner' === $the_options['cookie_bar_as'] && ! $the_options['button_settings_as_popup'] ) {
								?>
								data-gdpr_action="show_settings"
								<?php
							} else {
								?>
								data-gdpr_action="settings" data-toggle="gdprmodal" data-target="#gdpr-gdprmodal"
								<?php
							}
							?>
							><?php echo esc_html__( $the_options['button_settings_text2'], 'gdpr-cookie-consent' );//phpcs:ignore  ?></a>
							<?php
						}
						?>
					</div>
		<?php }
		} ?>
	</div>
	<?php
	if ( ! empty( $the_options['cookie_data'] ) ) {
		if ( 'banner' === $the_options['cookie_bar_as'] && ! $the_options['button_settings_as_popup'] ) {
			?>
			<div class="gdpr_messagebar_detail" style="display:none;max-width:800px;">
			<?php include plugin_dir_path( __DIR__ ) . '/banners/default.php'; ?>
			</div>
			<?php
		}
	}
}
else if ( ! empty( $the_options['lgpd_notify'] ) ) {
	$ab_options = get_option('wpl_ab_options');
	?>
	<div class="gdpr_messagebar_content">
		<div class="group-description" tabindex="0">
		<div class="gdpr_logo_container">
		<?php
		if($ab_options['ab_testing_enabled'] === "false" || $ab_options['ab_testing_enabled'] === false){
			if($the_options['cookie_usage_for'] == 'both'){
				$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELDML1 );
				if ( '' !== $get_banner_img ) {
					?>
						<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
						<?php
				}
			}else{
				$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
				if ( '' !== $get_banner_img ) {
					?>
						<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
						<?php
				}
			}
		}
		else{
			if($ab_options['ab_testing_enabled'] === "true" || $ab_options['ab_testing_enabled'] === true){
				if($chosenBanner == 1) {
					$get_banner_img1 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1 );
					if ( '' !== $get_banner_img1 ) {
						?>
							<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img1 ); ?>" >
							<?php
					}
					}elseif($chosenBanner == 2){
						$get_banner_img2 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD2 );
					if ( '' !== $get_banner_img2 ) {
						?>
							<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img2 ); ?>" >
							<?php
					}
					}
			}
		} ?>
		</div>
			<?php
			if ( '' !== $the_options['head_lgpd'] ) {
				?>
				<h3 class="gdpr_messagebar_head">
				<?php
				echo esc_html__( $the_options['head_lgpd'], 'gdpr-cookie-consent' ); //phpcs:ignore
				?>
	</h3>
				<?php
			}
			?>
			<p class="lgpd"><?php echo wp_kses_post( $cookie_data['dash_notify_message_lgpd'], '<a><br><em><strong><span><p><i><img><b><div><label>' ); ?></p>
			<?php
			if ( ! empty( $the_options['button_readmore_is_on'] ) ) {
				?>
				<p>
					<a id="cookie_action_link" href="<?php echo esc_html( $the_options['button_readmore_url_link'] ); ?>" class="<?php echo esc_html( $the_options['button_readmore_classes'] ); ?>"
						<?php
						if ( ! empty( $the_options['button_readmore_new_win'] ) ) {
							?>
							target="_blank"
							<?php
						}
						?>
					>
					<?php
					echo esc_html__( $cookie_data['dash_button_readmore_text'], 'gdpr-cookie-consent' ); // phpcs:ignore
					?>
	</a>
				</p>
				<?php
			}
			if ( ! empty( $the_options['ccpa_notify'] ) ) {
				?>
				<p class="ccpa">
				<?php echo wp_kses_post( $cookie_data['dash_notify_message_ccpa'], '<a><br><em><strong><span><p><i><img><b><div><label>' ); ?>
				<?php 
		if($the_options['cookie_usage_for'] == 'both') { ?>
			<p class="ccpa"><?php echo wp_kses_post( $cookie_data['dash_notify_message_ccpa'], '<a><br><em><strong><span><p><i><img><b><div><label>' ); ?>
			<?php
			if ( ! empty( $the_options['button_donotsell_is_on'] ) ) {
				?>
				<a data-toggle="gdprmodal" href="#" class="<?php echo esc_html( $the_options['button_donotsell_classes'] ); ?>" data-gdpr_action="donotsell" id="cookie_donotsell_link"
				><?php echo esc_html__( $the_options['button_donotsell_text1'], 'gdpr-cookie-consent' ); //phpcs:ignore ?></a>
				<?php
			}
			?>
		</p>
		<?php } else { ?>
			<p class="ccpa"><?php echo wp_kses_post( $cookie_data['dash_notify_message_ccpa'], '<a><br><em><strong><span><p><i><img><b><div><label>' ); ?>
			<?php
			if ( ! empty( $the_options['button_donotsell_is_on'] ) ) {
				?>
				<a data-toggle="gdprmodal" href="#" class="<?php echo esc_html( $the_options['button_donotsell_classes'] ); ?>" data-gdpr_action="donotsell" id="cookie_donotsell_link"
				><?php echo esc_html__($cookie_data['dash_button_donotsell_text'], 'gdpr-cookie-consent' ); //phpcs:ignore ?></a>
				<?php
			}
			?>
		</p>
			<?php } ?>
				</p>
				<?php
			}
			?>
		</div>
		<?php if($ab_options['ab_testing_enabled'] === "false" || $ab_options['ab_testing_enabled'] === false) { ?>
		<div class="gdpr group-description-buttons">
			<?php
			if ( ! empty( $the_options['button_accept_is_on'] ) ) {
				?>
				<p>
					<a id="cookie_action_accept" class="<?php echo esc_html( $the_options['button_accept_classes'] ); ?>" tabindex="0" aria-label="Accept"
					<?php
					if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_action'] ) {
						?>
						href="<?php echo esc_html( $the_options['button_accept_url'] ); ?>"
						<?php
						if ( ! empty( $the_options['button_accept_new_win'] ) ) {
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
					data-gdpr_action="accept" >
					<?php
					echo esc_html__( $cookie_data['dash_button_accept_text'], 'gdpr-cookie-consent' ); //phpcs:ignore
					?>
	</a>
				</p>
				<?php
			}
			if ( ! empty( $the_options['button_accept_all_is_on'] ) ) {
				?>
				<p>
					<a id="cookie_action_accept_all" class="<?php echo esc_html( $the_options['button_accept_all_classes'] ); ?>" tabindex="0" aria-label="Accept All"
					<?php
					if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_all_action'] ) {
						?>
						href="<?php echo esc_html( $the_options['button_accept_all_url'] ); ?>"
						<?php
						if ( ! empty( $the_options['button_accept_all_new_win'] ) ) {
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
					data-gdpr_action="accept_all" >
					<?php
					echo esc_html__( $cookie_data['dash_button_accept_all_text'], 'gdpr-cookie-consent' ); //phpcs:ignore
					?>
	</a>
				</p>
				<?php
			}
			if ( ! empty( $the_options['button_decline_is_on'] ) ) {
				?>
				<p>
					<a id="cookie_action_reject" class="<?php echo esc_html( $the_options['button_decline_classes'] ); ?>" tabindex="0" aria-label="Reject"
					<?php
					if ( 'CONSTANT_OPEN_URL' === $the_options['button_decline_action'] ) {
						?>
						href="<?php echo esc_html( $the_options['button_decline_url'] ); ?>'"
						<?php
						if ( ! empty( $the_options['button_decline_new_win'] ) ) {
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
					data-gdpr_action="reject" >
					<?php
					echo esc_html__( $cookie_data['dash_button_decline_text'], 'gdpr-cookie-consent' ); //phpcs:ignore
					?>
	</a>
				</p>
				<?php
			}
			if ( ! empty( $the_options['button_settings_is_on'] ) ) {
				?>
				<p>
					<a id="cookie_action_settings" class="<?php echo esc_html( $the_options['button_settings_classes'] ); ?>" tabindex="0" aria-label="Cookie Settings" href="#"
							<?php
							if ( ! $the_options['button_settings_as_button'] ) {
								?>
								href="#"
								<?php
							}
							if ( 'banner' === $the_options['cookie_bar_as'] && ! $the_options['button_settings_as_popup'] ) {
								?>
								data-gdpr_action="show_settings"
								<?php
							} else {
								?>
								data-gdpr_action="settings" data-toggle="gdprmodal" data-target="#gdpr-gdprmodal"
								<?php
							}
							?>
					>
					<?php
					echo esc_html__( $cookie_data['dash_button_settings_text'], 'gdpr-cookie-consent' ); // phpcs:ignore
					?>
	</a>
				</p>
				<?php
			}
			?>
		</div>
		<?php } else { 
			if($chosenBanner == 1) { ?>
			<div class="gdpr group-description-buttons">
					<?php
					if ( ! empty( $the_options['button_accept_is_on1'] ) && ($the_options['button_accept_is_on1'] === "true"  || $the_options['button_accept_is_on1'] === "1" || $the_options['button_accept_is_on1'] === true) ) {
						?>
						<p>
							<a id="cookie_action_accept" class="<?php echo esc_html( $the_options['button_accept_classes'] ); ?>" tabindex="0" aria-label="Accept"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_action1'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_accept_url1'] ); ?>"
								<?php
								if ( ! empty( $the_options['button_accept_new_win1'] ) ) {
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
							data-gdpr_action="accept" >
							<?php
							echo esc_html__( $the_options['button_accept_text1'], 'gdpr-cookie-consent' ); //phpcs:ignore
							?>
			</a>
						</p>
						<?php
					}
					if ( ! empty( $the_options['button_accept_all_is_on1'] ) && ($the_options['button_accept_all_is_on1'] === "true" || $the_options['button_accept_all_is_on1'] === "1" || $the_options['button_accept_all_is_on1'] === true) ) {
						?>
						<p>
							<a id="cookie_action_accept_all" class="<?php echo esc_html( $the_options['button_accept_all_classes'] ); ?>" tabindex="0" aria-label="Accept All"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_all_action1'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_accept_all_url1'] ); ?>"
								<?php
								if ( ! empty( $the_options['button_accept_all_new_win1'] ) ) {
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
							data-gdpr_action="accept_all" >
							<?php
							echo esc_html__( $the_options['button_accept_all_text1'], 'gdpr-cookie-consent' ); //phpcs:ignore
							?>
			</a>
						</p>
						<?php
					}
					if ( ! empty( $the_options['button_decline_is_on1'] ) && ($the_options['button_decline_is_on1'] === "true" || $the_options['button_decline_is_on1'] === "1" || $the_options['button_decline_is_on1'] === true) ) {
						?>
						<p>
							<a id="cookie_action_reject" class="<?php echo esc_html( $the_options['button_decline_classes'] ); ?>" tabindex="0" aria-label="Reject"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_decline_action1'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_decline_url1'] ); ?>'"
								<?php
								if ( ! empty( $the_options['button_decline_new_win1'] ) ) {
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
							data-gdpr_action="reject" >
							<?php
							echo esc_html__( $the_options['button_decline_text1'], 'gdpr-cookie-consent' ); //phpcs:ignore
							?>
			</a>
						</p>
						<?php
					}
					if ( ! empty( $the_options['button_settings_is_on1'] ) && ($the_options['button_settings_is_on1'] === "true" || $the_options['button_settings_is_on1'] === "1" || $the_options['button_settings_is_on1'] === true) ) {
						?>
						<p>
							<a id="cookie_action_settings" class="<?php echo esc_html( $the_options['button_settings_classes'] ); ?>" tabindex="0" aria-label="Cookie Settings" href="#"
									<?php
									if ( ! $the_options['button_settings_as_button1'] ) {
										?>
										href="#"
										<?php
									}
									if ( 'banner' === $the_options['cookie_bar_as'] && ! $the_options['button_settings_as_popup'] ) {
										?>
										data-gdpr_action="show_settings"
										<?php
									} else {
										?>
										data-gdpr_action="settings" data-toggle="gdprmodal" data-target="#gdpr-gdprmodal"
										<?php
									}
									?>
							>
							<?php
							echo esc_html__( $the_options['button_settings_text1'], 'gdpr-cookie-consent' ); // phpcs:ignore
							?>
			</a>
						</p>
						<?php
					}
					?>
				</div>
		<?php } else { ?>
			<div class="gdpr group-description-buttons">
					<?php
					if ( ! empty( $the_options['button_accept_is_on2'] ) && ($the_options['button_accept_is_on2'] === "true" || $the_options['button_accept_is_on2'] === "1" || $the_options['button_accept_is_on2'] === true) ) {
						?>
						<p>
							<a id="cookie_action_accept" class="<?php echo esc_html( $the_options['button_accept_classes'] ); ?>" tabindex="0" aria-label="Accept"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_action2'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_accept_url2'] ); ?>"
								<?php
								if ( ! empty( $the_options['button_accept_new_win2'] ) ) {
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
							data-gdpr_action="accept" >
							<?php
							echo esc_html__( $the_options['button_accept_text2'], 'gdpr-cookie-consent' ); //phpcs:ignore
							?>
			</a>
						</p>
						<?php
					}
					if ( ! empty( $the_options['button_accept_all_is_on2'] ) && ($the_options['button_accept_all_is_on2'] === "true" || $the_options['button_accept_all_is_on2'] === "1" || $the_options['button_accept_all_is_on2'] === true) ) {
						?>
						<p>
							<a id="cookie_action_accept_all" class="<?php echo esc_html( $the_options['button_accept_all_classes'] ); ?>" tabindex="0" aria-label="Accept All"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_all_action2'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_accept_all_url2'] ); ?>"
								<?php
								if ( ! empty( $the_options['button_accept_all_new_win2'] ) ) {
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
							data-gdpr_action="accept_all" >
							<?php
							echo esc_html__( $the_options['button_accept_all_text2'], 'gdpr-cookie-consent' ); //phpcs:ignore
							?>
			</a>
						</p>
						<?php
					}
					if ( ! empty( $the_options['button_decline_is_on2'] ) && ($the_options['button_decline_is_on2'] === "true" || $the_options['button_decline_is_on2'] === "1" || $the_options['button_decline_is_on2'] === true) ) {
						?>
						<p>
							<a id="cookie_action_reject" class="<?php echo esc_html( $the_options['button_decline_classes'] ); ?>" tabindex="0" aria-label="Reject"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_decline_action2'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_decline_url2'] ); ?>'"
								<?php
								if ( ! empty( $the_options['button_decline_new_win2'] ) ) {
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
							data-gdpr_action="reject" >
							<?php
							echo esc_html__( $the_options['button_decline_text2'], 'gdpr-cookie-consent' ); //phpcs:ignore
							?>
			</a>
						</p>
						<?php
					}
					if ( ! empty( $the_options['button_settings_is_on2'] ) && ( $the_options['button_settings_is_on2'] === "true" || $the_options['button_settings_is_on2'] === "1" || $the_options['button_settings_is_on2'] === true) ) {
						?>
						<p>
							<a id="cookie_action_settings" class="<?php echo esc_html( $the_options['button_settings_classes'] ); ?>" tabindex="0" aria-label="Cookie Settings" href="#"
									<?php
									if ( ! $the_options['button_settings_as_button2'] ) {
										?>
										href="#"
										<?php
									}
									if ( 'banner' === $the_options['cookie_bar_as'] && ! $the_options['button_settings_as_popup'] ) {
										?>
										data-gdpr_action="show_settings"
										<?php
									} else {
										?>
										data-gdpr_action="settings" data-toggle="gdprmodal" data-target="#gdpr-gdprmodal"
										<?php
									}
									?>
							>
							<?php
							echo esc_html__( $the_options['button_settings_text2'], 'gdpr-cookie-consent' ); // phpcs:ignore
							?>
			</a>
						</p>
						<?php
					}
					?>
				</div>
		<?php }
		} ?>
	</div>
	</div>
	<?php
	if ( ! empty( $the_options['cookie_data'] ) ) {
		if ( 'banner' === $the_options['cookie_bar_as'] && ! $the_options['button_settings_as_popup'] ) {
			?>
			<div class="gdpr_messagebar_detail" style="display:none;max-width:800px;">
			<?php include plugin_dir_path( __DIR__ ) . '/banners/default.php'; ?>
			</div>
			<?php
		}
	}
}
 elseif ( ! empty( $the_options['ccpa_notify'] ) ) {
	?>
	<div class="gdpr_messagebar_content">

		<div class="group-description" tabindex="0">

		<!-- Logo Added for CCPA rule 	 -->
		<div class="gdpr_logo_container">
		<?php
		if($ab_options['ab_testing_enabled'] === "false" || $ab_options['ab_testing_enabled'] === false){
			if($the_options['cookie_usage_for'] == 'both'){
				$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELDML2 );
				if ( '' !== $get_banner_img ) {
					?>
						<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
						<?php
				}
			}else{
				$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
				if ( '' !== $get_banner_img ) {
					?>
						<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
						<?php
				}
			}
		}
		else{
			if($ab_options['ab_testing_enabled'] === "true" || $ab_options['ab_testing_enabled'] === true){
				if($chosenBanner == 1) {
					$get_banner_img1 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1 );
					if ( '' !== $get_banner_img1 ) {
						?>
							<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img1 ); ?>" >
							<?php
					}
					}elseif($chosenBanner == 2){
						$get_banner_img2 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD2 );
					if ( '' !== $get_banner_img2 ) {
						?>
							<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img2 ); ?>" >
							<?php
					}
					}
			}
		} ?>
		</div>

		<?php 
		if($the_options['cookie_usage_for'] == 'both') { ?>
			<?php
			if ( ! empty( $the_options['button_donotsell_is_on'] ) ) {
				?>
				<a data-toggle="gdprmodal" href="#" class="<?php echo esc_html( $the_options['button_donotsell_classes'] ); ?>" data-gdpr_action="donotsell" id="cookie_donotsell_link"
				><?php echo esc_html__( $the_options['button_donotsell_text1'], 'gdpr-cookie-consent' ); //phpcs:ignore ?></a>
				<?php
			}
			?>
		</p>
		<?php } else { ?>
			<p class="ccpa"><?php echo wp_kses_post( $cookie_data['dash_notify_message_ccpa'], '<a><br><em><strong><span><p><i><img><b><div><label>' ); ?>
			<?php
			if ( ! empty( $the_options['button_donotsell_is_on'] ) ) {
				?>
				<a data-toggle="gdprmodal" href="#" class="<?php echo esc_html( $the_options['button_donotsell_classes'] ); ?>" data-gdpr_action="donotsell" id="cookie_donotsell_link"
				><?php echo esc_html__(  $cookie_data['dash_button_donotsell_text'], 'gdpr-cookie-consent' ); //phpcs:ignore ?></a>
				<?php
			}
			?>
		</p>
			<?php } ?>
		</div>
	</div>
	<?php
} elseif ( ! empty( $the_options['eprivacy_notify'] ) ) {
	$ab_options = get_option('wpl_ab_options');
	?>
		<div class="gdpr_messagebar_content">
			
		<div class="group-description" tabindex="0">

		<!-- Logo Added for Eprivacy rule 	 -->
		<div class="gdpr_logo_container">
		<?php
		if($ab_options['ab_testing_enabled'] === "false" || $ab_options['ab_testing_enabled'] === false){
			if($the_options['cookie_usage_for'] == 'both'){
				$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELDML1 );
				if ( '' !== $get_banner_img ) {
					?>
						<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
						<?php
				}
			}else{
				$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
				if ( '' !== $get_banner_img ) {
					?>
						<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
						<?php
				}
			}
		}
		else{
			if($ab_options['ab_testing_enabled'] === "true" || $ab_options['ab_testing_enabled'] === true){
				if($chosenBanner == 1) {
					$get_banner_img1 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD1 );
					if ( '' !== $get_banner_img1 ) {
						?>
							<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img1 ); ?>" >
							<?php
					}
					}elseif($chosenBanner == 2){
						$get_banner_img2 = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD2 );
					if ( '' !== $get_banner_img2 ) {
						?>
							<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img2 ); ?>" >
							<?php
					}
					}
			}
		} ?>
		</div>

		<p class="gdpr"><?php echo wp_kses_post( $cookie_data['dash_notify_message_eprivacy'], '<a><br><em><strong><span><p><i><img><b><div><label>' ); ?>
				<?php
				if ( ! empty( $the_options['button_readmore_is_on'] ) ) {
					?>
					<a id="cookie_action_link" href="<?php echo esc_html( $the_options['button_readmore_url_link'] ); ?>" class="<?php echo esc_html( $the_options['button_readmore_classes'] ); ?>"
							<?php
							if ( ! empty( $the_options['button_readmore_new_win'] ) ) {
								?>
								target="_blank"
								<?php
							}
							?>
					>
					<?php
					echo esc_html__( $cookie_data['dash_button_readmore_text'], 'gdpr-cookie-consent' ); // phpcs:ignore
					?>
	</a>
					<?php
				}
				?>
			</p>
		</div>
		<?php if($ab_options['ab_testing_enabled'] === "false" || $ab_options['ab_testing_enabled'] === false) { ?>
		<div class="gdpr group-description-buttons">
			<?php
			if ( ! empty( $the_options['button_accept_is_on'] ) ) {
				?>
				<p>
					<a id="cookie_action_accept" class="<?php echo esc_html( $the_options['button_accept_classes'] ); ?>" tabindex="0" aria-label="Accept"
					<?php
					if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_action'] ) {
						?>
						href="<?php echo esc_html( $the_options['button_accept_url'] ); ?>"
						<?php
						if ( ! empty( $the_options['button_accept_new_win'] ) ) {
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
					data-gdpr_action="accept" >
					<?php
					echo esc_html__( $cookie_data['dash_button_accept_text'], 'gdpr-cookie-consent' ); //phpcs:ignore
					?>
	</a>
				</p>
				<?php
			}
			if ( ! empty( $the_options['button_accept_all_is_on'] ) ) {
				?>
				<p>
					<a id="cookie_action_accept_all" class="<?php echo esc_html( $the_options['button_accept_all_classes'] ); ?>" tabindex="0" aria-label="Accept All"
					<?php
					if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_all_action'] ) {
						?>
						href="<?php echo esc_html( $the_options['button_accept_all_url'] ); ?>"
						<?php
						if ( ! empty( $the_options['button_accept_all_new_win'] ) ) {
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
					data-gdpr_action="accept_all" >
					<?php
					echo esc_html__( $cookie_data['dash_button_accept_all_text'], 'gdpr-cookie-consent' ); //phpcs:ignore
					?>
	</a>
				</p>
				<?php
			}
			if ( ! empty( $the_options['button_decline_is_on'] ) ) {
				?>
				<p>
					<a id="cookie_action_reject" class="<?php echo esc_html( $the_options['button_decline_classes'] ); ?>" tabindex="0" aria-label="Reject"
					<?php
					if ( 'CONSTANT_OPEN_URL' === $the_options['button_decline_action'] ) {
						?>
						href="<?php echo esc_html( $the_options['button_decline_url'] ); ?>'"
						<?php
						if ( ! empty( $the_options['button_decline_new_win'] ) ) {
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
					data-gdpr_action="reject" >
					<?php
					echo esc_html__( $cookie_data['dash_button_decline_text'], 'gdpr-cookie-consent' ); //phpcs:ignore
					?>
	</a>
				</p>
				<?php
			}
			
			?>
		</div>
		<?php } else { 
			if($chosenBanner == 1) { ?>
			<div class="gdpr group-description-buttons">
					<?php
					if ( ! empty( $the_options['button_accept_is_on1'] ) && ($the_options['button_accept_is_on1'] === "true" || $the_options['button_accept_is_on1'] === "1" || $the_options['button_accept_is_on1'] === true) ) {
						?>
						<p>
							<a id="cookie_action_accept" class="<?php echo esc_html( $the_options['button_accept_classes'] ); ?>" tabindex="0" aria-label="Accept"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_action1'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_accept_url1'] ); ?>"
								<?php
								if ( ! empty( $the_options['button_accept_new_win1'] ) ) {
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
							data-gdpr_action="accept" >
							<?php
							echo esc_html__( $the_options['button_accept_text1'], 'gdpr-cookie-consent' ); //phpcs:ignore
							?>
			</a>
						</p>
						<?php
					}
					if ( ! empty( $the_options['button_accept_all_is_on1'] ) && $the_options['button_accept_all_is_on1'] === "true" || $the_options['button_accept_all_is_on1'] === "1" || $the_options['button_accept_all_is_on1'] === true ) {
						?>
						<p>
							<a id="cookie_action_accept_all" class="<?php echo esc_html( $the_options['button_accept_all_classes'] ); ?>" tabindex="0" aria-label="Accept All"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_all_action1'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_accept_all_url1'] ); ?>"
								<?php
								if ( ! empty( $the_options['button_accept_all_new_win1'] ) ) {
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
							data-gdpr_action="accept_all" >
							<?php
							echo esc_html__( $the_options['button_accept_all_text1'], 'gdpr-cookie-consent' ); //phpcs:ignore
							?>
			</a>
						</p>
						<?php
					}
					if ( ! empty( $the_options['button_decline_is_on1'] ) && ($the_options['button_decline_is_on1'] === "true" || $the_options['button_decline_is_on1'] === "1" || $the_options['button_decline_is_on1'] === true ) ) {
						?>
						<p>
							<a id="cookie_action_reject" class="<?php echo esc_html( $the_options['button_decline_classes'] ); ?>" tabindex="0" aria-label="Reject"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_decline_action1'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_decline_url1'] ); ?>'"
								<?php
								if ( ! empty( $the_options['button_decline_new_win1'] ) ) {
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
							data-gdpr_action="reject" >
							<?php
							echo esc_html__( $the_options['button_decline_text1'], 'gdpr-cookie-consent' ); //phpcs:ignore
							?>
			</a>
						</p>
						<?php
					}
					
					?>
				</div>
		<?php } else { ?>
			<div class="gdpr group-description-buttons">
					<?php
					if ( ! empty( $the_options['button_accept_is_on2'] ) && ($the_options['button_accept_is_on2'] === "true" || $the_options['button_accept_is_on2'] === "1" || $the_options['button_accept_is_on2'] === true) ) {
						?>
						<p>
							<a id="cookie_action_accept" class="<?php echo esc_html( $the_options['button_accept_classes'] ); ?>" tabindex="0" aria-label="Accept"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_action2'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_accept_url2'] ); ?>"
								<?php
								if ( ! empty( $the_options['button_accept_new_win2'] ) ) {
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
							data-gdpr_action="accept" >
							<?php
							echo esc_html__( $the_options['button_accept_text2'], 'gdpr-cookie-consent' ); //phpcs:ignore
							?>
			</a>
						</p>
						<?php
					}
					if ( ! empty( $the_options['button_accept_all_is_on2'] ) && ($the_options['button_accept_all_is_on2'] === "true" || $the_options['button_accept_all_is_on2'] === "1" || $the_options['button_accept_all_is_on2'] === true) ) {
						?>
						<p>
							<a id="cookie_action_accept_all" class="<?php echo esc_html( $the_options['button_accept_all_classes'] ); ?>" tabindex="0" aria-label="Accept All"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_accept_all_action2'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_accept_all_url2'] ); ?>"
								<?php
								if ( ! empty( $the_options['button_accept_all_new_win2'] ) ) {
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
							data-gdpr_action="accept_all" >
							<?php
							echo esc_html__( $the_options['button_accept_all_text2'], 'gdpr-cookie-consent' ); //phpcs:ignore
							?>
			</a>
						</p>
						<?php
					}
					if ( ! empty( $the_options['button_decline_is_on2'] ) && ($the_options['button_decline_is_on2'] === "true" || $the_options['button_decline_is_on2'] === "1" || $the_options['button_decline_is_on2'] === true) ) {
						?>
						<p>
							<a id="cookie_action_reject" class="<?php echo esc_html( $the_options['button_decline_classes'] ); ?>" tabindex="0" aria-label="Reject"
							<?php
							if ( 'CONSTANT_OPEN_URL' === $the_options['button_decline_action2'] ) {
								?>
								href="<?php echo esc_html( $the_options['button_decline_url2'] ); ?>'"
								<?php
								if ( ! empty( $the_options['button_decline_new_win2'] ) ) {
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
							data-gdpr_action="reject" >
							<?php
							echo esc_html__( $the_options['button_decline_text2'], 'gdpr-cookie-consent' ); //phpcs:ignore
							?>
			</a>
						</p>
						<?php
					}
					
					?>
				</div>
		<?php }
		} ?>
				<?php
				if ( ! empty( $cookie_data['show_credits'] ) ) {
					if ( ! empty( $cookie_data['credits'] ) ) {
						?>
				<div class="powered-by-credits"><?php echo wp_kses_post( array( 'credits' ) ); ?></div>
						<?php
					}
				}
				?>
	</div>
				<?php
}