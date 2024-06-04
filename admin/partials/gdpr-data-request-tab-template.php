<?php

/**
 * Provide a data request tab area view for the WP Cookie Consent plugin
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
<div class="wpl wrap <?php echo esc_attr( $high_contrast ); ?>" id="dataRequestContainer">
	<?php //this header is a placeholder to ensure notices do not end up in the middle of our code ?>
	<div class="wpl-{page}">
		<div class="wpl-content-area">
			{content}
		</div>
	</div>
</div>
