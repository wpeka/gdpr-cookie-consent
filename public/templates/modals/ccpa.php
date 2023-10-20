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
			<div class="gdprmodal-body"><p>
			<?php
			$category_name = esc_html( $category['optout_text'] );
			// Translators: %s is a placeholder for the category name.
			echo esc_html( sprintf( __( 'Category: %s', 'gdpr-cookie-consent' ), $category_name ) );
			?>
					<button type="button" class="gdpr_action_button close dashicons dashicons-dismiss" data-dismiss="gdprmodal" data-gdpr_action="ccpa_close"><span class="close dashicons dashicons-dismiss">Close</span></button></p>
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
				<button id="cookie_action_cancel" type="button" class="
				<?php
				$category_name = esc_html( $category['button-cancle_classes'] );
				// Translators: %s is a placeholder for the category name.
				echo esc_html( sprintf( __( 'Category: %s', 'gdpr-cookie-consent' ), $category_name ) );
				?>
	" data-gdpr_action="cancel" data-dismiss="gdprmodal">
	<?php
	$category_name = esc_html( $category['button_cancle_text'] );
	// Translators: %s is a placeholder for the category name.
	echo esc_html( sprintf( __( 'Category: %s', 'gdpr-cookie-consent' ), $category_name ) );
	?>
	</button>
				<button id="cookie_action_confirm" type="button" class="
				<?php
				$category_name = esc_html( $category['button_confirm_classes'] );
				// Translators: %s is a placeholder for the category name.
				echo esc_html( sprintf( __( 'Category: %s', 'gdpr-cookie-consent' ), $category_name ) );
				?>
	" data-gdpr_action="confirm" data-dismiss="gdprmodal">
	<?php
	$category_name = esc_html( $category['button_confirm_text'] );
	// Translators: %s is a placeholder for the category name.
	echo esc_html( sprintf( __( 'Category: %s', 'gdpr-cookie-consent' ), $category_name ) );
	?>
	</button>
			</div>
		</div>
	</div>
</div>
