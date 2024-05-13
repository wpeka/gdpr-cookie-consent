<?php

/**
 * Provide a consent log data tab area view for the WP Cookie Consent plugin
 *
 * This file is used to markup the admin-facing aspects of the WP Cookie Consent plugin.
 *
 * @link       https://club.wpeka.com/
 * @since      2.4.1
 *
 * @package gdpr-cookie-consent
 */


?>

<?php $high_contrast = 'wpl-high-contrast' ;?>
<div class="wpl wrap <?php esc_html_e( $high_contrast ) ?>" id="gdprCookieConsent">
	<?php //this header is a placeholder to ensure notices do not end up in the middle of our code ?>
	<div class="wpl-{page}">
		<div class="wpl-content-area" id="consentLogDataTabContainer">
			{content}
		</div>
	</div>
</div>
