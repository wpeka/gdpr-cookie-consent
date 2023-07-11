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
	?>
	<div class="gdpr_messagebar_content">
	<!-- <div class="gdpr_logo_container" style="display: flex;justify-content: center;"> -->
	<div class="gdpr_logo_container">
		<?php
			$get_banner_img = get_option( GDPR_COOKIE_CONSENT_SETTINGS_LOGO_IMAGE_FIELD );
		if ( '' !== $get_banner_img ) {
			?>
			<img class="gdpr_logo_image" alt="logo-image" src="<?php echo esc_url_raw( $get_banner_img ); ?>" >
				<?php
		}
		?>
		</div>
		<?php
		if ( '' !== $the_options['head'] ) {
			?>
			<h3 class="gdpr_messagebar_head"><?php echo esc_html__( $the_options['head'], 'gdpr-cookie-consent' ); ?></h3>
			<?php
		}
		?>
		<div class="group-description" tabindex="0"><p class="gdpr"><?php echo strip_tags( $the_options['gdpr_str'], '<a><br><em><strong><span><p><i><img><b><div><label>' ); ?>
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
	><?php echo esc_html__( $the_options['button_readmore_text'], 'gdpr-cookie-consent' ); ?></a>
			<?php
		}
		?>
			</p>
			<?php
			if ( ! empty( $the_options['ccpa_notify'] ) ) {
				?>
				<p class="ccpa">
				<?php echo strip_tags( $the_options['ccpa_str'], '<a><br><em><strong><span><p><i><img><b><div><label>' ); ?>
					<?php
					if ( ! empty( $the_options['button_donotsell_is_on'] ) ) {
						?>
						<a data-toggle="gdprmodal" href="#" class="<?php echo esc_html( $the_options['button_donotsell_classes'] ); ?>" data-gdpr_action="donotsell" id="cookie_donotsell_link"
						><?php echo esc_html__( $the_options['button_donotsell_text'], 'gdpr-cookie-consent' ); ?></a>
						<?php
					}
					?>
				</p>
				<?php
			}
			?>
		</div>
		<div class="gdpr group-description-buttons">
				<?php
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
					data-gdpr_action="reject" ><?php echo esc_html__( $the_options['button_decline_text'], 'gdpr-cookie-consent' ); ?></a>
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
					><?php echo esc_html__( $the_options['button_settings_text'], 'gdpr-cookie-consent' ); ?></a>
					<?php
				}
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
					data-gdpr_action="accept" ><?php echo esc_html__( $the_options['button_accept_text'], 'gdpr-cookie-consent' ); ?></a>
					<?php
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
				data-gdpr_action="accept_all" ><?php echo esc_html__( $the_options['button_accept_all_text'], 'gdpr-cookie-consent' ); ?></a>
						<?php
					}
					?>
					<?php
				}
				?>
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
} elseif ( ! empty( $the_options['ccpa_notify'] ) ) {
	?>
	<div class="gdpr_messagebar_content">
		<div class="group-description" tabindex="0"><p class="ccpa"><?php echo strip_tags( $the_options['ccpa_str'], '<a><br><em><strong><span><p><i><img><b><div><label>' ); ?>
				<?php
				if ( ! empty( $the_options['button_donotsell_is_on'] ) ) {
					?>
					<a data-toggle="gdprmodal" href="#" class="<?php echo esc_html( $the_options['button_donotsell_classes'] ); ?>" data-gdpr_action="donotsell" id="cookie_donotsell_link"
					><?php echo esc_html__( $the_options['button_donotsell_text'], 'gdpr-cookie-consent' ); ?></a>
					<?php
				}
				?>
			</p>
		</div>
	</div>
	<?php
} elseif ( ! empty( $the_options['eprivacy_notify'] ) ) {
	?>
	<div class="gdpr_messagebar_content">
		<?php
		if ( '' !== $the_options['head'] ) {
			?>
			<h3 class="gdpr_messagebar_head"><?php echo esc_html__( $the_options['head'], 'gdpr-cookie-consent' ); ?></h3>
			<?php
		}
		?>
		<div class="group-description" tabindex="0"><p class="gdpr"><?php echo strip_tags( $the_options['eprivacy_str'], '<a><br><em><strong><span><p><i><img><b><div><label>' ); ?>
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
					><?php echo esc_html__( $the_options['button_readmore_text'], 'gdpr-cookie-consent' ); ?></a>
					<?php
				}
				?>
			</p>
		</div>
		<div class="gdpr group-description-buttons">
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
				data-gdpr_action="accept" ><?php echo esc_html__( $the_options['button_accept_text'], 'gdpr-cookie-consent' ); ?></a>
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
				data-gdpr_action="accept_all" ><?php echo esc_html__( $the_options['button_accept_all_text'], 'gdpr-cookie-consent' ); ?></a>
				<?php
			}
			if ( ! empty( $the_options['button_decline_is_on'] ) ) {
				?>
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
				data-gdpr_action="reject" ><?php echo esc_html__( $the_options['button_decline_text'], 'gdpr-cookie-consent' ); ?></a>
				<?php
			}
			?>
		</div>
		<?php
		if ( ! empty( $cookie_data['show_credits'] ) ) {
			if ( ! empty( $cookie_data['credits'] ) ) {
				?>
				<div class="powered-by-credits"><?php echo wp_kses_post( $cookie_data['credits'] ); ?></div>
				<?php
			}
		}
		?>
	</div>
	<?php
}
