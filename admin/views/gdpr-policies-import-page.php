<?php
/**
 * Provide a admin area view for the import page.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin/views
 * @author     wpeka <https://club.wpeka.com>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<div class="wrap">
	<h2><?php esc_attr_e( 'Import from a CSV file', 'gdpr-cookie-consent' ); ?></h2>
	<?php
	if ( isset( $_GET['import'] ) ) { // phpcs:ignore

		switch ( $_GET['import'] ) { // phpcs:ignore
			case 'file':
				echo '<div class="error"><p><strong>' . esc_attr__( 'Error during file upload.', 'gdpr-cookie-consent' ) . '</strong></p></div>';
				break;
			case 'data':
				echo '<div class="error"><p><strong>' . esc_attr__( 'Cannot extract data from uploaded file or no file was uploaded.', 'gdpr-cookie-consent' ) . '</strong></p></div>';
				break;
			case 'fail':
				echo '<div class="error"><p><strong>' . esc_attr__( 'No posts was successfully imported.', 'gdpr-cookie-consent' ) . '</strong></p></div>';
				break;
			case 'errors':
				echo '<div class="error"><p><strong>' . esc_attr__( 'Some posts were successfully imported but some were not.', 'gdpr-cookie-consent' ) . '</strong></p></div>';
				break;
			case 'success':
				echo '<div class="updated"><p><strong>' . esc_attr__( 'Post import was successful.', 'gdpr-cookie-consent' ) . '</strong></p></div>';
				break;
			default:
				break;
		}
	}
	?>
	<form method="post" action="" enctype="multipart/form-data">
		<?php wp_nonce_field( 'gdpr-policies-import-page', '_wpnonce-gdpr-policies-import-page' ); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="policies_csv"><?php esc_attr_e( 'CSV File', 'gdpr-cookie-consent' ); ?></label></th>
				<td>
					<input type="file" id="policies_csv" name="policies_csv" value="" class="all-options" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"></th>
				<td>
					<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Import', 'gdpr-cookie-consent' ); ?>" />
				</td>
			</tr>
		</table>
	</form>
</div>
