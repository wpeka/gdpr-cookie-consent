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
<div class="gdprmodal gdprfade" id="gdpr-gdprmodal" role="dialog" data-keyboard="false" data-backdrop="<?php echo esc_html( $cookie_data['backdrop'] ); ?>">
	<div class="gdprmodal-dialog gdprmodal-dialog-centered">
		<!-- Modal content-->
		<div class="gdprmodal-content">
			<div class="gdprmodal-header">
				<button type="button" class="gdpr_action_button close" data-dismiss="gdprmodal" data-gdpr_action="close">
					<span class="dashicons dashicons-dismiss">Close</span>
				</button>
			</div>
			<div class="gdprmodal-body">
				<div class="gdpr-details-content">
					<div class="gdpr-groups-container">
						<ul class="category-group">
							<li class="category-item">
								<div class="gdpr-column gdpr-default-category-toggle">
									<div class="gdpr-columns active-group" tabindex="0">
										<a href="#" class="btn category-header"><?php echo esc_html( $cookie_data['about'] ); ?></a>
									</div>
								</div>
								<div class="description-container">
									<div class="group-toggle">
										<h3 class="category-header" tabindex="0"><?php echo esc_html( $cookie_data['about'] ); ?></h3>
									</div>
									<div class="group-description" tabindex="0"><?php echo esc_html__( $cookie_data['msg'], 'gdpr-cookie-consent' ); //phpcs:ignore?></div>
								</div>
							</li>
							<?php
							foreach ( $cookie_data['categories'] as $category ) {
								?>
								<li class="category-item">
								<div class="gdpr-column gdpr-default-category-toggle  <?php echo esc_html( $the_options['template_parts'] ); ?>">
									<div class="gdpr-columns">
										<a href="#" class="btn category-header" tabindex="0"><?php echo esc_html__( $category['gdpr_cookie_category_name'], 'gdpr-cookie-consent' ); //phpcs:ignore?></a>
									</div>
								</div>
								<div class="description-container hide">
									<div class="group-toggle">
										<h3 class="category-header" tabindex="0"><?php echo esc_html__( $category['gdpr_cookie_category_name'], 'gdpr-cookie-consent' );//phpcs:ignore ?></h3>
										<?php
										if ( 'necessary' === $category['gdpr_cookie_category_slug'] ) {
											?>
											<div class="toggle-group">
												<div class="always-active"><?php echo esc_html( $cookie_data['always'] ); ?></div>
												<input id="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" type="hidden" name="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" value="<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>">
											</div>
											<?php
										} else {
											?>
											<div class="toggle-group">
												<div class="toggle">
													<div class="checkbox">
														<!-- DYNAMICALLY GENERATE Input ID  -->
														<input
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
														id="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" class="category-switch-handler" type="checkbox" name="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" value="<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>">
														<label for="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>">
															<span class="label-text"><?php echo esc_html( $category['gdpr_cookie_category_name'] ); ?></span>
														</label>
														<!-- DYNAMICALLY GENERATE Input ID  -->
													</div>
												</div>
											</div>
											<?php
										}
										?>
									</div>
									<div class="group-description" tabindex="0"><?php echo esc_html__( $category['gdpr_cookie_category_description'], 'gdpr-cookie-consent' ); //phpcs:ignore?></div>
									<!-- sub groups -->
											<?php
											if ( ! empty( $the_options['button_settings_display_cookies'] ) ) {
												?>
										<div class="category-cookies-list-container">
													<?php
													if ( $category['total'] >= '1' ) {
														?>
												<table class="table table-striped">
												<thead class="thead-dark">
												<tr>
													<th><?php echo esc_html( $cookie_data['name'] ); ?></th>
													<th><?php echo esc_html( $cookie_data['domain'] ); ?></th>
													<th><?php echo esc_html( $cookie_data['purpose'] ); ?></th>
													<th><?php echo esc_html( $cookie_data['expiry'] ); ?></th>
													<th><?php echo esc_html( $cookie_data['type'] ); ?></th>
												</tr>
												</thead>
												<tbody>
														<?php
														foreach ( $category['data'] as $cookie ) {
															?>
														<tr>
														<td>
															<?php
															if ( ! empty( $cookie['name'] ) ) {
																echo esc_html( $cookie['name'] );
															} else {
																echo esc_html( '---' );
															}
															?>
															</td>
															<td>
															<?php
															if ( ! empty( $cookie['domain'] ) ) {
																echo esc_html( $cookie['domain'] );
															} else {
																echo esc_html( '---' );
															}
															?>
															</td>
															<td>
															<?php
															if ( ! empty( $cookie['description'] ) ) {
																echo esc_html( $cookie['description'] );
															} else {
																echo esc_html( '---' );
															}
															?>
															</td>
															<td>
															<?php
															if ( ! empty( $cookie['duration'] ) ) {
																echo esc_html( $cookie['duration'] );
															} else {
																echo esc_html( '---' );
															}
															?>
															</td>
															<td>
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
													}
													?>
									</div>
												<?php
											}
											?>
								</div>
							</li>
											<?php
							}
							?>
						</ul>
					</div>
				</div>
			</div>
			<div class="gdprmodal-footer">
				<?php
				if ( ! empty( $cookie_data['show_credits'] ) ) {
					?>
				<div class="powered-by-credits"><?php echo wp_kses_post( $cookie_data['credits'] ); ?></div>
					<?php
				}
				?>
				<button id="cookie_action_save" type="button" class="gdpr_action_button btn" data-gdpr_action="accept" data-dismiss="gdprmodal"><?php echo esc_html( $cookie_data['save_button'] ); ?></button>
			</div>
		</div>
	</div>
</div>
