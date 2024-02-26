<?php
/**
 * Provide a admin area view for the cookie list.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://club.wpeka.com
 * @since      3.0.0
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin/modules/cookie-custom/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<p></p>
<div class="form-table script-blocker-list">
		<table class="right">
			<tr class="right-grid-6 header">
				<th>Enabled</th>
				<th>Name</th>
				<th>Description</th>
				<th>Category</th>
			</tr>
			<?php
			if ( isset( $scripts_list ) && $scripts_list['total'] > 0 ) :
				if ( isset( $scripts_list['data'] ) && ! empty( $scripts_list['data'] ) ) :
					$i = 0;
					foreach ( $scripts_list['data'] as $script ) {
						$i++;
						$class = 0 === $i % 2 ? 'even' : '';
						?>
						<tr class="right-grid-6 <?php echo esc_attr( $class ); ?>">
							<input type="hidden" name="script_id" value="<?php echo esc_attr( $script['id'] ); ?>">
							<td class="script_status_switch"><label class="switch">
									<input type="checkbox" name="script_status" class="script_status"
										<?php
										if ( '1' === $script['script_status'] ) {
											echo 'checked';}
										?>
									>
									<span class="slider round"></span>
								</label></td>
							<td><?php echo esc_attr( $script['script_title'] ); ?></td>
							<td><?php echo esc_attr( $script['script_description'] ); ?></td>
							<td><select name="script_category" class="vvv_combobox script_category">
									<?php foreach ( $category_list as $key => $category ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>"
											<?php
											if ( (string) $key === $script['script_category'] ) {
												echo 'selected';
											}
											?>
										><?php echo esc_attr( $category ); ?></option>
									<?php endforeach; ?>
								</select></td>
						</tr>
						<?php
					}
				endif;
			endif;
			?>
		</table>
	</div>
