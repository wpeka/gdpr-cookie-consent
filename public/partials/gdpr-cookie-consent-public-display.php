<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://club.wpeka.com
 * @since      1.0
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
// if filter is applied.
if ( '' === $notify_html ) {
	return;
}
echo $notify_html // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>

<script type="text/javascript">
	/* <![CDATA[ */
	gdpr_cookiebar_settings='<?php echo Gdpr_Cookie_Consent::gdpr_get_json_settings(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>';
	/* ]]> */
</script>
