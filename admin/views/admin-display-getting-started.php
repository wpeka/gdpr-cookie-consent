<?php
/**
 * Provide a admin area view for the getting started page.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://club.wpeka.com
 * @since      1.0.0
 *
 * @package    Wpadcenter
 * @subpackage Wpadcenter/admin/views
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$pro_link = sprintf(
	/* translators: 1: href link 2: Text */
	'<a href="%s" target="_blank">%s</a>',
	'https://club.wpeka.com/product/wp-gdpr-cookie-consent/?utm_source=gdpr&utm_medium=getting-started&utm_campaign=link&utm_content=pro-version',
	__( 'pro version', 'gdpr-cookie-consent' )
);
?>
<div style="clear:both;"></div>
<div class="wrap gdpr-getting-started">
	<h1><?php esc_attr_e( 'Getting Started', 'gdpr-cookie-consent' ); ?></h1>
	<h2><?php esc_attr_e( '1. Add / Scan Website Cookies', 'gdpr-cookie-consent' ); ?></h2>
	<div>
		<?php if ( get_option( 'wpl_pro_active' ) ) : ?>
			<p><?php esc_attr_e( 'Before you can display the consent notice on your website, you need to add details of the cookies on your website. The plugin automatically scans the website for cookies and adds them.', 'gdpr-cookie-consent' ); ?></p>
		<?php else : ?>
			<p><?php echo sprintf( 'Before you can display the consent notice on your website, you need to add details of the cookies on your website. The %s of the plugin automatically scans the website for cookies and adds them.', $pro_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<?php endif; ?>
		<a style="<?php echo esc_attr( $styles ); ?>" class="button button-primary" href="<?php echo esc_url( admin_url() . 'admin.php?page=gdpr-cookie-consent#gdpr-cookie-consent-cookie-list' ); ?>" target="_blank"><?php esc_attr_e( 'Add Cookie Details', 'gdpr-cookie-consent' ); ?></a>
	</div>
	<h2><?php esc_attr_e( '2. Customize the Cookie Banner Content', 'gdpr-cookie-consent' ); ?></h2>
	<div>
		<p><?php esc_attr_e( 'You can completely customize the cookie banner content to match your website’s content style. Set the consent notice usage to GDPR and control how and when the banner appears to website visitors.', 'gdpr-cookie-consent' ); ?></p>
		<p><?php esc_attr_e( 'Note: Set the usage to CCPA and update the content accordingly to display the CCPA “Do Not Sell” notice.', 'gdpr-cookie-consent' ); ?></p>
		<a class="button button-primary" href="<?php echo esc_url( admin_url() . 'admin.php?page=gdpr-cookie-consent' ); ?>" target="_blank"><?php esc_attr_e( 'Customize Content', 'gdpr-cookie-consent' ); ?></a>
	</div>
	<h2><?php esc_attr_e( '3. Customize the Cookie Banner Design', 'gdpr-cookie-consent' ); ?></h2>
	<div>
		<p><?php esc_attr_e( 'Just like the content you can completely customize the design of your cookie consent banner. Choose from different display styles, colors and animations.', 'gdpr-cookie-consent' ); ?></p>
		<a class="button button-primary" href="<?php echo esc_url( admin_url() . 'admin.php?page=gdpr-cookie-consent#gdpr-cookie-consent-design' ); ?>" target="_blank"><?php esc_attr_e( 'Customize Content', 'gdpr-cookie-consent' ); ?></a>
	</div>
	<h2><?php esc_attr_e( '4. Display the Cookie Details Table in your Cookie / Privacy policy', 'gdpr-cookie-consent' ); ?></h2>
	<div>
		<p><?php esc_attr_e( 'To embed a cookie details table: use shortcode [wpl_cookie_details] to display cookie policy data on the pages.', 'gdpr-cookie-consent' ); ?></p>
	</div>
	<?php if ( get_option( 'wpl_pro_active' ) ) : ?>
		<h2><?php esc_attr_e( '5. Block Third-Party Cookie Scripts', 'gdpr-cookie-consent' ); ?></h2>
	<?php else : ?>
		<h2><?php esc_attr_e( '5. Block Third-Party Cookie Scripts (Pro feature)', 'gdpr-cookie-consent' ); ?></h2>
	<?php endif; ?>
	<div>
		<p><?php esc_attr_e( 'Automatically block known third-party cookies until your website visitor gives their preference. Once the visitor gives preference, the third-party features which depend on these cookies (like youtube videos) will be visible.', 'gdpr-cookie-consent' ); ?></p>
		<?php if ( get_option( 'wpl_pro_active' ) ) : ?>
		<a style="<?php echo esc_attr( $styles ); ?>" class="button button-primary" href="<?php echo esc_url( admin_url() . 'admin.php?page=gdpr-cookie-consent#gdpr-cookie-consent-script-blocker' ); ?>" target="_blank"><?php esc_attr_e( 'Block Scripts', 'gdpr-cookie-consent' ); ?></a>
		<?php else : ?>
		<a class="button button-primary" href="https://club.wpeka.com/product/wp-gdpr-cookie-consent/?utm_source=gdpr&utm_medium=getting-started&utm_campaign=link&utm_content=upgrade-to-pro-version" target="_blank"><?php esc_attr_e( 'Upgrade To Pro Version', 'gdpr-cookie-consent' ); ?></a>
		<?php endif; ?>
	</div>
	<h1><?php esc_attr_e( 'About WP Cookie Consent Plugin For GDPR & CCPA', 'gdpr-cookie-consent' ); ?></h1>
	<div>
		<p><?php esc_attr_e( 'The WordPress Cookie Consent Plugin for GDPR & CCPA plugin helps you comply with the EU GDPR’s cookie consent and CCPA’s “Do Not Sell” opt-out regulations.', 'gdpr-cookie-consent' ); ?></p>
	</div>
	<h2><?php esc_attr_e( 'Important Links', 'gdpr-cookie-consent' ); ?></h2>
	<div>
		<ul>
			<li><a href="https://docs.wpeka.com/wp-gdpr-cookie-consent/?utm_source=gdpr&utm_medium=getting-started&utm_campaign=link&utm_content=documentation" target="_blank"><?php esc_attr_e( 'Documentation', 'gdpr-cookie-consent' ); ?></a></li>
			<?php if ( get_option( 'wpl_pro_active' ) ) : ?>
				<li><a href="https://club.wpeka.com/my-account/orders/?utm_source=gdpr&utm_medium=getting-started&utm_campaign=link&utm_content=support" target="_blank"><?php esc_attr_e( 'Support', 'gdpr-cookie-consent' ); ?></a></li>
			<?php else : ?>
				<li><a href="https://wordpress.org/support/plugin/gdpr-cookie-consent/?utm_source=gdpr&utm_medium=getting-started&utm_campaign=link&utm_content=forums" target="_blank"><?php esc_attr_e( 'Forums', 'gdpr-cookie-consent' ); ?></a></li>
				<li><a href="https://club.wpeka.com/product/wp-gdpr-cookie-consent/?utm_source=gdpr&utm_medium=getting-started&utm_campaign=link&utm_content=upgrade-to-the-pro-version" target="_blank"><?php esc_attr_e( 'Upgrade to the Pro version', 'gdpr-cookie-consent' ); ?></a></li>
			<?php endif; ?>
		</ul>
	</div>
</div>
