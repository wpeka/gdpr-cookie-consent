<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://club.wpeka.com
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 */

?>
<div class="gdprmodal gdprfade" id="gdpr-ccpa-gdprmodal" role="dialog" data-keyboard="false" data-backdrop="<?php echo esc_html( $cookie_data['backdrop'] ); ?>">
	<div class="gdprmodal-dialog gdprmodal-dialog-centered">
		<div class="gdprmodal-content">
			<div class="gdprmodal-body"><p><?php echo esc_html__( $cookie_data['dash_optout_text'], 'gdpr-cookie-consent' ); //phpcs:ignore?>
					<button type="button" class="gdpr_action_button close dashicons dashicons-no" data-dismiss="gdprmodal" data-gdpr_action="ccpa_close"><span class="close dashicons dashicons-no">&#10005</span></button></p>
			</div>
			<div class="gdprmodal-footer">
				<?php
				if ( ! empty( $cookie_data['show_credits'] ) ) {
					if ( ! empty( $cookie_data['credits'] ) ) {
						?>
						<div class="powered-by-credits"><?php echo wp_kses_post( $cookie_data['credits'] ); ?></div>
						<?php
					}
				}
				?>
				<?php if ($the_options['cookie_usage_for'] == 'both') {
				?>
					<button id="cookie_action_cancel" type="button" class="<?php echo esc_html( $the_options['button_cancel_classes'] ); ?>" data-gdpr_action="cancel" data-dismiss="gdprmodal"><?php echo esc_html__( $the_options['button_cancel_text1'], 'gdpr-cookie-consent' );//phpcs:ignore ?></button>
					<button id="cookie_action_confirm" type="button" class="<?php echo esc_html( $the_options['button_confirm_classes'] ); ?>" data-gdpr_action="confirm" data-dismiss="gdprmodal"><?php echo esc_html__( $the_options['button_confirm_text1'], 'gdpr-cookie-consent' );//phpcs:ignore ?></button>
				<?php } else {?>
					<button id="cookie_action_cancel" type="button" class="<?php echo esc_html( $the_options['button_cancel_classes'] ); ?>" data-gdpr_action="cancel" data-dismiss="gdprmodal"><?php echo esc_html__(  $cookie_data['dash_button_cancel_text'], 'gdpr-cookie-consent' );//phpcs:ignore ?></button>
				<button id="cookie_action_confirm" type="button" class="<?php echo esc_html( $the_options['button_confirm_classes'] ); ?>" data-gdpr_action="confirm" data-dismiss="gdprmodal"><?php echo esc_html__( $cookie_data['dash_button_confirm_text'], 'gdpr-cookie-consent' );//phpcs:ignore ?></button>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
