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
<div>
	<?php echo esc_html( $cookie_data['consent_notice'] ); ?>
</div>
<div id="gdpr_messagebar_body_buttons_select_pane">
	<?php
	foreach ( $cookie_data['categories'] as $category ) {
		?>
		<div class="gdpr_messagebar_body_buttons_wrapper">
			<?php
			if ( 'necessary' === $category['gdpr_cookie_category_slug'] ) {
				?>
				<input type="checkbox" id="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" class="gdpr_messagebar_body_button gdpr_messagebar_body_button_disabled" disabled="disabled" checked="checked" value="<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>">
				<?php
			} else {
				?>
				<input type="checkbox" id="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" class="gdpr_messagebar_body_button" tabindex="0"
				<?php
				if ( ! empty( $the_options['is_ticked'] ) && ! $the_options['viewed_cookie'] ) {
					?>
					checked="checked"
					<?php
				} elseif ( ! empty( $category['is_ticked'] ) ) {
					?>
					checked="checked"
					<?php
				}
				?>
				value="<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>">
				<?php
			}
			?>
			<label for="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>"><?php echo esc_html( $category['gdpr_cookie_category_name'] ); ?></label>
		</div>
		<?php
	}
	?>
</div>
<div id="gdpr_messagebar_detail_body">
	<div id="gdpr_messagebar_detail_body_content_tabs">
		<a id="gdpr_messagebar_detail_body_content_tabs_overview" class="gdpr_messagebar_detail_body_content_tab gdpr_messagebar_detail_body_content_tab_item_selected" tabindex="0" href="#"><?php echo esc_html( $cookie_data['declaration'] ); ?></a>
		<a id="gdpr_messagebar_detail_body_content_tabs_about" class="gdpr_messagebar_detail_body_content_tab" tabindex="0" href="#"><?php echo esc_html( $cookie_data['about'] ); ?></a>
	</div>
	<div id="gdpr_messagebar_detail_body_content">
		<div id="gdpr_messagebar_detail_body_content_overview" style="display:block;">
			<div id="gdpr_messagebar_detail_body_content_overview_cookie_container">
				<div id="gdpr_messagebar_detail_body_content_overview_cookie_container_types">
					<?php
					foreach ( $cookie_data['categories'] as $category ) {
						?>
						<a data-target="gdpr_messagebar_detail_body_content_cookie_tabs_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" id="gdpr_messagebar_detail_body_content_overview_cookie_container_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" class="gdpr_messagebar_detail_body_content_overview_cookie_container_types
						<?php
						if ( 'necessary' === $category['gdpr_cookie_category_slug'] ) {
							?>
							gdpr_messagebar_detail_body_content_overview_cookie_container_type_selected
							<?php
						}
						?>
						" tabindex="0" href="#">
							<?php
							echo esc_html( $category['gdpr_cookie_category_name'] );
							if ( ! empty( $the_options['button_settings_display_cookies'] ) ) {
								echo esc_html( ' (' . $category['total'] . ')' );}
							?>
						</a>
							<?php
					}
					?>
				</div>
				<div id="gdpr_messagebar_detail_body_content_overview_cookie_container_type_details">
					<?php
					foreach ( $cookie_data['categories'] as $category ) {
						?>
						<div id="gdpr_messagebar_detail_body_content_cookie_tabs_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" tabindex="0"
						<?php
						if ( 'necessary' === $category['gdpr_cookie_category_slug'] ) {
							?>
							style="display:block;"
							<?php
						} else {
							?>
							style="display:none;"
							<?php
						}
						?>
							class="gdpr_messagebar_detail_body_content_cookie_type_details">
							<div class="gdpr_messagebar_detail_body_content_cookie_type_intro">
							<?php echo esc_html( $category['gdpr_cookie_category_description'] ); ?>
							</div>
							<?php
							if ( ! empty( $the_options['button_settings_display_cookies'] ) ) {
								?>
								<div class="gdpr_messagebar_detail_body_content_cookie_type_table_container">
									<?php
									if ( $category['total'] > 0 ) {
										?>
										<table id="gdpr_messagebar_detail_table_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" class="gdpr_messagebar_detail_body_content_cookie_type_table">
									<thead>
									<tr>
										<th scope="col"><?php echo esc_html( $cookie_data['name'] ); ?></th>
										<th scope="col"><?php echo esc_html( $cookie_data['domain'] ); ?></th>
										<th scope="col"><?php echo esc_html( $cookie_data['purpose'] ); ?></th>
										<th scope="col"><?php echo esc_html( $cookie_data['expiry'] ); ?></th>
										<th scope="col"><?php echo esc_html( $cookie_data['type'] ); ?></th>
									</tr>
									</thead>
									<tbody>
										<?php
										foreach ( $category['data'] as $cookie ) {
											?>
											<tr>
											<td title="<?php echo esc_html( $cookie_data['name'] ); ?>">
											<?php
											if ( ! empty( $cookie['name'] ) ) {
												echo esc_html( $cookie['name'] );
											} else {
												echo esc_html( '---' );
											}
											?>
											</td>
											<td title="<?php echo esc_html( $cookie_data['domain'] ); ?>">
											<?php
											if ( ! empty( $cookie['domain'] ) ) {
												echo esc_html( $cookie['domain'] );
											} else {
												echo esc_html( '---' );
											}
											?>
											</td>
											<td title="<?php if(! empty( $cookie_data['description'])){echo esc_html( $cookie_data['description'] );}; ?>">
											<?php
											if ( ! empty( $cookie['description'] ) ) {
												echo esc_html( $cookie['description'] );
											} else {
												echo esc_html( '---' );
											}
											?>
											</td>
											<td title="<?php if(! empty( $cookie_data['duration'])){echo esc_html( $cookie_data['duration'] );}; ?>">
											<?php
											if ( ! empty( $cookie['duration'] ) ) {
												echo esc_html( $cookie['duration'] );
											} else {
												echo esc_html( '---' );
											}
											?>
											</td>
											<td title="<?php echo esc_html( $cookie_data['type'] ); ?>">
											<?php
											if ( ! empty( $cookie['type'] ) ) {
												echo esc_html( $cookie['type'] );
											} else {
												echo esc_html( '---' );
											}
											?>
											</td>
										</tr>
											<?php
										}
										?>
									</tbody>
								</table>
										<?php
									} else {
										echo esc_html( $cookie_data['cookies_not_found'] );
									}
									?>
							</div>
								<?php
							}
							?>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<div id="gdpr_messagebar_detail_body_content_about" style="display:none;">
		<?php echo esc_html( $cookie_data['msg'] ); ?>
		</div>
	</div>
</div>
<?php
if ( ! empty( $cookie_data['show_credits'] ) ) {
	?>
	<div class="powered-by-credits"><?php echo wp_kses_post( $cookie_data['credits'] ); ?></div>
	<?php
}
