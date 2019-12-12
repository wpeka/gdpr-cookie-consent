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
 * @subpackage Gdpr_Cookie_Consent/public/views
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<div class="gdpr_messagebar_detail" style="display:none;">
	<div><?php esc_attr_e( 'I consent to the use of following cookies:', 'gdpr-cookie-consent' ); ?></div>
	<div id="gdpr_messagebar_body_buttons_select_pane">
		<?php
		foreach ( $categories as $category ) :
			if ( 'unclassified' === $category['gdpr_cookie_category_slug'] ) :
				continue;
			else :
				?>
				<div class="gdpr_messagebar_body_buttons_wrapper">
					<?php if ( 'necessary' === $category['gdpr_cookie_category_slug'] ) : ?>
						<input type="checkbox" id="gdpr_messagebar_body_button_<?php echo esc_attr( $category['gdpr_cookie_category_slug'] ); ?>" class="gdpr_messagebar_body_button gdpr_messagebar_body_button_disabled" disabled="disabled" checked="checked" value="<?php echo esc_attr( $category['gdpr_cookie_category_slug'] ); ?>">
					<?php else : ?>
						<input type="checkbox" id="gdpr_messagebar_body_button_<?php echo esc_attr( $category['gdpr_cookie_category_slug'] ); ?>" class="gdpr_messagebar_body_button"
						<?php
						if ( true === $the_options['is_ticked'] ) {
							echo 'checked';
						}
						?>
					value="<?php echo esc_attr( $category['gdpr_cookie_category_slug'] ); ?>">
					<?php endif; ?>
					<label for="gdpr_messagebar_body_button_<?php echo esc_attr( $category['gdpr_cookie_category_slug'] ); ?>"><?php echo esc_attr__( $category['gdpr_cookie_category_name'], 'gdpr-cookie-consent' ); // phpcs:ignore ?></label>
				</div>
				<?php
			endif;
		endforeach;
		?>
	</div>
	<div id="gdpr_messagebar_detail_body">
		<div id="gdpr_messagebar_detail_body_content_tabs">
			<a id="gdpr_messagebar_detail_body_content_tabs_overview" class="gdpr_messagebar_detail_body_content_tab gdpr_messagebar_detail_body_content_tab_item_selected"><?php esc_attr_e( 'Cookie declaration', 'gdpr-cookie-consent' ); ?></a>
			<a id="gdpr_messagebar_detail_body_content_tabs_about" class="gdpr_messagebar_detail_body_content_tab"><?php esc_attr_e( 'About cookies', 'gdpr-cookie-consent' ); ?></a>
		</div>
		<div id="gdpr_messagebar_detail_body_content">
			<div id="gdpr_messagebar_detail_body_content_overview" style="display:block;">
				<div id="gdpr_messagebar_detail_body_content_overview_cookie_container">
					<div id="gdpr_messagebar_detail_body_content_overview_cookie_container_types">
						<?php
						foreach ( $categories as $category ) :
							$cookies_array[ $category['gdpr_cookie_category_slug'] ]['desc'] = $category['gdpr_cookie_category_description'];
							$total = $cookies_array[ $category['gdpr_cookie_category_slug'] ]['total'];
							$total = $cookies_array[ $category['gdpr_cookie_category_slug'] ]['total'];
							?>
						<a data-target="gdpr_messagebar_detail_body_content_cookie_tabs_<?php echo esc_attr( $category['gdpr_cookie_category_slug'] ); ?>" id="gdpr_messagebar_detail_body_content_overview_cookie_container_<?php echo esc_attr( $category['gdpr_cookie_category_slug'] ); ?>" class="gdpr_messagebar_detail_body_content_overview_cookie_container_types
							<?php if ( 'necessary' === $category['gdpr_cookie_category_slug'] ) : ?>
							gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected<?php endif; ?>"><?php echo esc_attr__( $category['gdpr_cookie_category_name'], 'gdpr-cookie-consent' ) . ' (' . esc_attr( $total ) . ')'; // phpcs:ignore ?></a>
						<?php endforeach; ?>
					</div>
					<div id="gdpr_messagebar_detail_body_content_overview_cookie_container_type_details">
						<?php
						foreach ( $cookies_array as $key => $cookies ) :
							if ( 'necessary' === $key ) {
								$style = 'display:block;';
							} else {
								$style = 'display:none;';
							}
							?>
							<div id="gdpr_messagebar_detail_body_content_cookie_tabs_<?php echo esc_attr( $key ); ?>" style="<?php echo esc_attr( $style ); ?>" class="gdpr_messagebar_detail_body_content_cookie_type_details">
								<div class="gdpr_messagebar_detail_body_content_cookie_type_intro">
									<?php echo esc_attr__( $cookies['desc'], 'gdpr-cookie-consent' ); // phpcs:ignore ?>
								</div>
								<div class="gdpr_messagebar_detail_body_content_cookie_type_table_container">
									<?php if ( $cookies['total'] > 0 ) : ?>
										<table id="gdpr_messagebar_detail_table_<?php echo esc_attr( $key ); ?>" class="gdpr_messagebar_detail_body_content_cookie_type_table">
											<thead>
											<tr>
												<th scope="col"><?php esc_attr_e( 'Name', 'gdpr-cookie-consent' ); ?></th>
												<th scope="col"><?php esc_attr_e( 'Domain', 'gdpr-cookie-consent' ); ?></th>
												<th scope="col"><?php esc_attr_e( 'Purpose', 'gdpr-cookie-consent' ); ?></th>
												<th scope="col"><?php esc_attr_e( 'Expiry', 'gdpr-cookie-consent' ); ?></th>
												<th scope="col"><?php esc_attr_e( 'Type', 'gdpr-cookie-consent' ); ?></th>
											</tr>
											</thead>
											<tbody>
											<?php foreach ( $cookies['data'] as $cookie ) : ?>
												<tr>
													<td title="<?php echo esc_attr( $cookie['name'] ); ?>">
													<?php
													if ( ! empty( $cookie['name'] ) ) {
														echo esc_attr( $cookie['name'] );
													} else {
														esc_attr_e( '---', 'gdpr-cookie-consent' );
													}
													?>
													</td>
													<td title="<?php echo esc_attr( $cookie['domain'] ); ?>">
													<?php
													if ( ! empty( $cookie['domain'] ) ) {
														echo esc_attr( $cookie['domain'] );
													} else {
														esc_attr_e( '---', 'gdpr-cookie-consent' );
													}
													?>
													</td>
													<?php if ( ! empty( $cookie['description'] ) ) : ?>
														<td style="white-space:normal;" title="<?php echo esc_attr( $cookie['description'] ); ?>"><?php echo esc_attr( $cookie['description'] ); ?></td>
													<?php else : ?>
														<td title="<?php esc_attr_e( 'Pending', 'gdpr-cookie-consent' ); ?>"><?php esc_attr_e( 'Pending', 'gdpr-cookie-consent' ); ?></td>
													<?php endif; ?>
													<td title="<?php echo esc_attr( $cookie['duration'] ); ?>">
													<?php
													if ( ! empty( $cookie['duration'] ) ) {
														echo esc_attr( $cookie['duration'] );
													} else {
														esc_attr_e( '---', 'gdpr-cookie-consent' );
													}
													?>
													</td>
													<td title="<?php echo esc_attr( $cookie['type'] ); ?>">
													<?php
													if ( ! empty( $cookie['type'] ) ) {
														echo esc_attr( $cookie['type'] );
													} else {
														esc_attr_e( '---', 'gdpr-cookie-consent' );
													}
													?>
													</td>
												</tr>
											<?php endforeach; ?>
											</tbody>
										</table>
										<?php
									else :
										esc_attr_e( 'We do not use cookies of this type.', 'gdpr-cookie-consent' );
									endif;
									?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<div id="gdpr_messagebar_detail_body_content_about" style="display:none;">
				<?php echo esc_attr__( $about_message, 'gdpr-cookie-consent' ); // phpcs:ignore ?>
			</div>
		</div>
	</div>
	<?php if ( true === $the_options['show_credits'] ) : ?>
	<div class="powered-by-credits"><?php echo esc_attr_e( 'Powered by', 'gdpr-cookie-consent' ); ?> <a href="https://club.wpeka.com/product/wp-gdpr-cookie-consent/" target="_blank"><?php echo esc_attr_e( 'GDPR Cookie Consent', 'gdpr-cookie-consent' ); ?></a></div>
	<?php endif; ?>
</div>
