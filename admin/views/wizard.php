<?php
/**
 * Provide a Wizard view for the admin.
 *
 * This file is used to markup the admin-facing aspects of the plugin (Wizard Page).
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin
 * @author Omendra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$image_path = GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/';
$is_pro     = get_option( 'wpl_pro_active', false );

/**
 *  Cookie Template card for Pro version.
 *
 * @param string $name name of the template.
 *
 * @param array  $templates list of template settings.
 *
 * @param string $checked name of the selected template.
 *
 * @since 1.0.0
 */
function print_template_boxes( $name, $templates, $checked ) {

	$image_path = GDPR_COOKIE_CONSENT_PLUGIN_URL . 'admin/images/';
	$is_pro     = get_option( 'wpl_pro_active', false );

	?>
	<div class="gdpr-templates-field-container-wizard">
		<?php
		foreach ( $templates as $key => $template ) :
			if ( false !== strpos( $template['name'], 'column' ) ) {
				$column = true;
			} else {
				$column = false;
			}
			if ( false !== strpos( $template['name'], 'square' ) ) {
				$square = true;
			} else {
				$square = false;
			}
			?>
		
			<?php
			if ( ! $is_pro ) {
				?>
			<div class="gdpr-template-field gdpr-<?php echo esc_attr( $template['name'] ); ?>">
				<div class="gdpr-left-field-wizard">
				<c-input type="radio" :disabled="false" name="<?php echo esc_attr( $name ) . '_template_field'; ?>" value="<?php echo esc_attr( $template['name'] ); ?>" @change="onTemplateChange"
				<?php
				if ( $template['name'] === $checked ) {
					echo ':checked="true"';
				}
				?>
				>
				</div>
				<div class="gdpr-right-field" style="<?php echo esc_attr( $template['css'] ); ?>">
					<div class="gdpr-right-field-content">
						<div class="gdpr-group-description" style="margin-top:20px">
							<h3 v-if="gdpr_message_heading.length>0">{{gdpr_message_heading}}</h3>
							<?php if ( $column ) : ?>
								<p v-html="gdpr_message"></p>
								<?php
								if ( isset( $template['readmore'] ) ) :
									$class = '';
									if ( $template['readmore']['as_button'] ) :
										$class = 'btn btn-sm';
									endif;
									?>
									<p><a style="<?php echo esc_attr( $template['readmore']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{button_readmore_text}}</a></p>
								<?php endif; ?>
							<?php else : ?>
								<p v-html="gdpr_message">
									<?php
									if ( isset( $template['readmore'] ) ) :
										$class = '';
										if ( $template['readmore']['as_button'] ) :
											$class = 'btn btn-sm';
										endif;
										?>
										<a style="<?php echo esc_attr( $template['readmore']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{button_readmore_text}}</a>
									<?php endif; ?>
									</p>
							<?php endif; ?>
						</div>
						<div class="gdpr-group-buttons">
							<?php if ( $square ) : ?>
								<?php
								if ( isset( $template['decline'] ) ) :
									$class = '';
									if ( $template['decline']['as_button'] ) :
										$class = 'btn btn-sm';
									endif;
									?>
									<a style="<?php echo esc_attr( $template['decline']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ decline_text }}</a>
								<?php endif; ?>
								<?php
								if ( isset( $template['settings'] ) ) :
									$class = '';
									if ( $template['settings']['as_button'] ) :
										$class = 'btn btn-sm';
									endif;
									?>
									<a style="<?php echo esc_attr( $template['settings']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ settings_text }}</a>
								<?php endif; ?>
								<?php
								if ( isset( $template['accept'] ) ) :
									$class = '';
									if ( $template['accept']['as_button'] ) :
										$class = 'btn btn-sm';
									endif;
									?>
									<a style="<?php echo esc_attr( $template['accept']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ accept_text }}</a>
								<?php endif; ?>
							<?php else : ?>
								<?php
								if ( isset( $template['accept'] ) ) :
									$class = '';
									if ( $template['accept']['as_button'] ) :
										$class = 'btn btn-sm';
									endif;
									?>
									<a style="<?php echo esc_attr( $template['accept']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ accept_text }}</a>
								<?php endif; ?>
								<?php
								if ( isset( $template['decline'] ) ) :
									$class = '';
									if ( $template['decline']['as_button'] ) :
										$class = 'btn btn-sm';
									endif;
									?>
									<a style="<?php echo esc_attr( $template['decline']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ decline_text }}</a>
								<?php endif; ?>
								<?php
								if ( isset( $template['settings'] ) ) :
									$class = '';
									if ( $template['settings']['as_button'] ) :
										$class = 'btn btn-sm';
									endif;
									?>
									<a style="<?php echo esc_attr( $template['settings']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>">{{ settings_text }}</a>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
				<?php
			} else {
				?>
		<div class="gdpr-template-field gdpr-<?php echo esc_attr( $template['name'] ); ?>">
			<div class="gdpr-left-field-wizard">
			<c-input type="radio" :disabled="disableSwitch" name="<?php echo esc_attr( $name ) . '_template_field'; ?>" value="<?php echo esc_attr( $template['name'] ); ?>" @change="onTemplateChange"
				<?php
				if ( $template['name'] === $checked ) {
					echo ':checked="true"';
				}
				?>
			>
			</div>
			<div class="gdpr-right-field" style="<?php echo esc_attr( $template['css'] ); ?>">
				<div class="gdpr-right-field-content">
					<div class="gdpr-group-description" style="margin-top:20px">
						<h3 v-if="gdpr_message_heading.length>0">{{gdpr_message_heading}}</h3>
						<?php if ( $column ) : ?>
							<p>{{gdpr_message}}</p>
							<?php
							if ( isset( $template['readmore'] ) ) :
								$class = '';
								if ( $template['readmore']['as_button'] ) :
									$class = 'btn btn-sm';
								endif;
								?>
								<p><a style="<?php echo esc_attr( $template['readmore']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['readmore']['text'] ); ?></a></p>
							<?php endif; ?>
						<?php else : ?>
							<p>{{gdpr_message}}
								<?php
								if ( isset( $template['readmore'] ) ) :
									$class = '';
									if ( $template['readmore']['as_button'] ) :
										$class = 'btn btn-sm';
									endif;
									?>
									<a style="<?php echo esc_attr( $template['readmore']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['readmore']['text'] ); ?></a>
								<?php endif; ?>
							</p>
						<?php endif; ?>
					</div>
					<div class="gdpr-group-buttons">
						<?php if ( $square ) : ?>
							<?php
							if ( isset( $template['decline'] ) ) :
								$class = '';
								if ( $template['decline']['as_button'] ) :
									$class = 'btn btn-sm';
								endif;
								?>
								<a style="<?php echo esc_attr( $template['decline']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['decline']['text'] ); ?></a>
							<?php endif; ?>
							<?php
							if ( isset( $template['settings'] ) ) :
								$class = '';
								if ( $template['settings']['as_button'] ) :
									$class = 'btn btn-sm';
								endif;
								?>
								<a style="<?php echo esc_attr( $template['settings']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['settings']['text'] ); ?></a>
							<?php endif; ?>
							<?php
							if ( isset( $template['accept'] ) ) :
								$class = '';
								if ( $template['accept']['as_button'] ) :
									$class = 'btn btn-sm';
								endif;
								?>
								<a style="<?php echo esc_attr( $template['accept']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['accept']['text'] ); ?></a>
							<?php endif; ?>
						<?php else : ?>
							<?php
							if ( isset( $template['accept'] ) ) :
								$class = '';
								if ( $template['accept']['as_button'] ) :
									$class = 'btn btn-sm';
								endif;
								?>
								<a style="<?php echo esc_attr( $template['accept']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['accept']['text'] ); ?></a>
							<?php endif; ?>
							<?php
							if ( isset( $template['decline'] ) ) :
								$class = '';
								if ( $template['decline']['as_button'] ) :
									$class = 'btn btn-sm';
								endif;
								?>
								<a style="<?php echo esc_attr( $template['decline']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['decline']['text'] ); ?></a>
							<?php endif; ?>
							<?php
							if ( isset( $template['settings'] ) ) :
								$class = '';
								if ( $template['settings']['as_button'] ) :
									$class = 'btn btn-sm';
								endif;
								?>
								<a style="<?php echo esc_attr( $template['settings']['css'] ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $template['settings']['text'] ); ?></a>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<!-- go pro label image for pro templates-->

					<div class="gdpr-go-pro-label">
								<div class="go-pro-label-image" ><img src="<?php echo esc_url( $image_path ) . 'go-pro.png'; ?>"></div>
					</div>

				</div>
			</div>
		</div>
				<?php
			}
			?>
	<?php endforeach; ?>
	</div>
	<?php
}


/**
 * Function returns list of templates.
 *
 * @since 2.5
 * @param String $template_type Template type.
 * @return array
 */
function get_templates( $template_type ) {
	$templates = apply_filters(
		'gdprcookieconsent_templates',
		array(
			'banner' => array(
				'default'          => array(
					'name'             => 'banner-default',
					'css'              => 'max-width:500px;color:#000000;background-color:#ffffff;text-align:justify;',
					'color'            => '#000000',
					'background_color' => '#ffffff',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#ffffff',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'accept'           => array(
						'text'                 => __( 'Accept', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#118635',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#118635',
						'button_border_radius' => '0',
					),
					'decline'          => array(
						'text'                 => __( 'Decline', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ef5454',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#ef5454',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#007cba;',
						'link_color' => '#007cba',
					),
					'settings'         => array(
						'text'                 => __( 'Cookie Settings', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#007cba;color:#ffffff;float:right;',
						'link_color'           => '#ffffff',
						'button_color'         => '#007cba',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#007cba',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#118635',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#118635',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ef5454',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#ef5454',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#007cba;',
						'link_color' => '#007cba',
					),
				),
				'almond_column'    => array(
					'name'             => 'banner-almond_column',
					'css'              => 'max-width:500px;color:#1e3d59;background-color:#e8ddbb;text-align:justify;',
					'color'            => '#1e3d59',
					'background_color' => '#e8ddbb',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#e8ddbb',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#c1540c;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#c1540c',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#c1540c',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#c1540c;',
						'link_color' => '#c1540c',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#c1540c;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#c1540c',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#118635',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#c1540c;',
						'link_color' => '#c1540c',
					),
				),
				'navy_blue_center' => array(
					'name'             => 'banner-navy_blue_center',
					'css'              => 'max-width:500px;color:#e5e5e5;background-color:#2a3e71;text-align:center;',
					'color'            => '#e5e5e5',
					'background_color' => '#2a3e71',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#2a3e71',
					'border_radius'    => '0',
					'layout'           => 'default',
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#e5e5e5',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
				),
				'grey_column'      => array(
					'name'             => 'banner-grey_column',
					'css'              => 'max-width:500px;color:#000000;background-color:#f4f4f4;text-align:justify;',
					'color'            => '#000000',
					'background_color' => '#f4f4f4',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#f4f4f4',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#C1263E;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#C1263E',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#C1263E',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#111111;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#111111',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#111111',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#C1263E;',
						'link_color' => '#C1263E',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#C1263E;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#C1263E',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#C1263E',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#111111;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#111111',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#111111',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#C1263E;',
						'link_color' => '#C1263E',
					),
				),
				'dark_row'         => array(
					'name'             => 'banner-dark_row',
					'css'              => 'max-width:500px;color:#ffffff;background-color:#323742;text-align:center;',
					'color'            => '#ffffff',
					'background_color' => '#323742',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#323742',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#2b806a;color:#ffffff;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid 4570dc',
						'link_color'           => '#ffffff',
						'button_color'         => '#2b806a',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#2b806a',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(50, 55, 66, 0);color:#2b806a;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid #2b806a;',
						'link_color'           => '#2b806a',
						'button_color'         => '#323742',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#2b806a',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#2b806a;',
						'link_color' => '#2b806a',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#2b806a;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#2b806a',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#2b806a',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#323742;color:#2b806a;margin:0 0.5rem 0 0',
						'link_color'           => '#2b806a',
						'button_color'         => '#323742',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#323742',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#2b806a;',
						'link_color' => '#2b806a',
					),
				),
				'grey_center'      => array(
					'name'             => 'banner-grey_center',
					'css'              => 'max-width:500px;color:#000000;background-color:#f4f4f4;text-align:center;',
					'color'            => '#000000',
					'background_color' => '#f4f4f4',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#f4f4f4',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#ac4008;',
						'link_color' => '#ac4008',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#ac4008;color:#ffffff;min-width:5rem;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ac4008',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#ac4008',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#ac4008;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ac4008',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#118635',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#ac4008;',
						'link_color' => '#ac4008',
					),
				),
				'dark'             => array(
					'name'             => 'banner-dark',
					'css'              => 'max-width:500px;color:#ffffff;background-color:#262626;text-align:justify;',
					'color'            => '#ffffff',
					'background_color' => '#262626',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#262626',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0;border:1px solid #4570dc;',
						'link_color'           => '#ffffff',
						'button_color'         => '#4570dc',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#4570dc',
						'button_border_radius' => '0',
					),
					'decline'          => array(
						'text'                 => 'Decline',
						'as_button'            => true,
						'css'                  => 'background-color:#808080;color:#ffffff;float:right;border:1px solid #808080;',
						'link_color'           => '#ffffff',
						'button_color'         => '#808080',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#4570dc;',
						'link_color' => '#4570dc',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(38, 38, 38, 0);color:#808080;float:right;margin:0 0.5rem 0 0;border:1px solid #808080',
						'link_color'           => '#808080',
						'button_color'         => '#262626',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#4570dc',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#4570dc',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#808080;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#808080',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#4570dc;',
						'link_color' => '#4570dc',
					),
				),
			),
			'popup'  => array(
				'default'          => array(
					'name'             => 'popup-default',
					'css'              => 'max-width:350px;color:#000000;background-color:#ffffff;text-align:justify;',
					'color'            => '#000000',
					'background_color' => '#ffffff',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#ffffff',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'accept'           => array(
						'text'                 => __( 'Accept', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#118635',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#118635',
						'button_border_radius' => '0',
					),
					'decline'          => array(
						'text'                 => __( 'Decline', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ef5454',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#ef5454',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#007cba;',
						'link_color' => '#007cba',
					),
					'settings'         => array(
						'text'                 => __( 'Cookie Settings', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#007cba;color:#ffffff;float:right;',
						'link_color'           => '#ffffff',
						'button_color'         => '#007cba',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#007cba',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#118635',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#118635',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ef5454',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#ef5454',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#007cba;',
						'link_color' => '#007cba',
					),
				),
				'dark'             => array(
					'name'             => 'popup-dark',
					'css'              => 'max-width:350px;color:#ffffff;background-color:#262626;text-align:justify;',
					'color'            => '#ffffff',
					'background_color' => '#262626',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#262626',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0;border:1px solid #4570dc;',
						'link_color'           => '#ffffff',
						'button_color'         => '#4570dc',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#4570dc',
						'button_border_radius' => '0',
					),
					'decline'          => array(
						'text'                 => 'Decline',
						'as_button'            => true,
						'css'                  => 'background-color:#808080;color:#ffffff;float:none;border:1px solid #808080;',
						'link_color'           => '#ffffff',
						'button_color'         => '#808080',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#4570dc;',
						'link_color' => '#4570dc',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(38, 38, 38, 0);color:#808080;float:right;margin:0 0.5rem 0 0;border:1px solid #808080',
						'link_color'           => '#808080',
						'button_color'         => '#262626',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#4570dc',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#4570dc',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#808080;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#808080',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#4570dc;',
						'link_color' => '#4570dc',
					),
				),
				'almond_column'    => array(
					'name'             => 'popup-almond_column',
					'css'              => 'max-width:350px;color:#1e3d59;background-color:#e8ddbb;text-align:justify;',
					'color'            => '#1e3d59',
					'background_color' => '#e8ddbb',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#e8ddbb',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#c1540c;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#c1540c',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#c1540c',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#c1540c;',
						'link_color' => '#c1540c',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#c1540c;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#c1540c',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#c1540c;',
						'link_color' => '#c1540c',
					),
				),
				'navy_blue_center' => array(
					'name'             => 'popup-navy_blue_center',
					'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:center;',
					'color'            => '#e5e5e5',
					'background_color' => '#2a3e71',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#2a3e71',
					'border_radius'    => '0',
					'layout'           => 'default',
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#e5e5e5',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#118635',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
				),
				'dark_row'         => array(
					'name'             => 'popup-dark_row',
					'css'              => 'max-width:350px;color:#ffffff;background-color:#323742;text-align:center;',
					'color'            => '#ffffff',
					'background_color' => '#323742',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#323742',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#2b806a;color:#ffffff;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid 4570dc',
						'link_color'           => '#ffffff',
						'button_color'         => '#2b806a',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#2b806a',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(50, 55, 66, 0);color:#2b806a;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid #2b806a;',
						'link_color'           => '#2b806a',
						'button_color'         => '#323742',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#2b806a',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#2b806a;',
						'link_color' => '#2b806a',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#2b806a;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#2b806a',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#2b806a',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#323742;color:#2b806a;margin:0 0.5rem 0 0',
						'link_color'           => '#2b806a',
						'button_color'         => '#323742',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#323742',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#2b806a;',
						'link_color' => '#2b806a',
					),
				),
				'grey_center'      => array(
					'name'             => 'popup-grey_center',
					'css'              => 'max-width:350px;color:#000000;background-color:#f4f4f4;text-align:center;',
					'color'            => '#000000',
					'background_color' => '#f4f4f4',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#f4f4f4',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#ac4008;',
						'link_color' => '#ac4008',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#ac4008;color:#ffffff;min-width:5rem;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ac4008',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#ac4008',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#ac4008;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ac4008',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#ac4008',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#ac4008;',
						'link_color' => '#ac4008',
					),
				),
				'navy_blue_box'    => array(
					'name'             => 'popup-navy_blue_box',
					'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:justify;border-radius:15px;',
					'color'            => '#e5e5e5',
					'background_color' => '#2a3e71',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#2a3e71',
					'border_radius'    => '15',
					'layout'           => 'default',
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;width:100%;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;width:100%;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#e5e5e5',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
				),
				'grey_column'      => array(
					'name'             => 'popup-grey_column',
					'css'              => 'max-width:350px;color:#000000;background-color:#f4f4f4;text-align:justify;border:1px solid #111111',
					'color'            => '#000000',
					'background_color' => '#f4f4f4',
					'opacity'          => '1',
					'border_style'     => 'solid',
					'border_width'     => '1',
					'border_color'     => '#111111',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#C1263E;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#C1263E',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#C1263E',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#111111;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#111111',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#111111',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#C1263E;',
						'link_color' => '#C1263E',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#C1263E;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#C1263E',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#C1263E',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#111111;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#111111',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#111111',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#C1263E;',
						'link_color' => '#C1263E',
					),
				),
				'navy_blue_square' => array(
					'name'             => 'popup-navy_blue_square',
					'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:justify;',
					'color'            => '#e5e5e5',
					'background_color' => '#2a3e71',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#2a3e71',
					'border_radius'    => '0',
					'layout'           => 'default',
					'decline'          => array(
						'text'                 => 'Decline',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;width:41%;margin:0 0.5rem 0 0;min-width:5rem;border:1px solid #369ee3;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;width:41%;min-width:5rem;float:right;border:1px solid #369ee3;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;width:100%;margin:1rem auto 0 auto;min-width:5rem;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#007cba;',
						'link_color' => '#007cba',
					),
				),
			),
			'widget' => array(
				'default'          => array(
					'name'             => 'widget-default',
					'css'              => 'max-width:350px;color:#000000;background-color:#ffffff;text-align:justify;',
					'color'            => '#000000',
					'background_color' => '#ffffff',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#ffffff',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'accept'           => array(
						'text'                 => __( 'Accept', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#118635',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#118635',
						'button_border_radius' => '0',
					),
					'decline'          => array(
						'text'                 => __( 'Decline', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ef5454',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#ef5454',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#007cba;',
						'link_color' => '#007cba',
					),
					'settings'         => array(
						'text'                 => __( 'Cookie Settings', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#007cba;color:#ffffff;float:right;',
						'link_color'           => '#ffffff',
						'button_color'         => '#007cba',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#007cba',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#118635;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#118635',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#118635',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#ef5454;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ef5454',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#ef5454',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#007cba;',
						'link_color' => '#007cba',
					),
				),
				'dark'             => array(
					'name'             => 'widget-dark',
					'css'              => 'max-width:350px;color:#ffffff;background-color:#262626;text-align:justify;',
					'color'            => '#ffffff',
					'background_color' => '#262626',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#262626',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0;border:1px solid #4570dc;',
						'link_color'           => '#ffffff',
						'button_color'         => '#4570dc',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#4570dc',
						'button_border_radius' => '0',
					),
					'decline'          => array(
						'text'                 => 'Decline',
						'as_button'            => true,
						'css'                  => 'background-color:#808080;color:#ffffff;float:right;border:1px solid #808080;',
						'link_color'           => '#ffffff',
						'button_color'         => '#808080',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => __( 'Read More', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#4570dc;',
						'link_color' => '#4570dc',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(38, 38, 38, 0);color:#808080;float:right;margin:0 0.5rem 0 0;border:1px solid #808080',
						'link_color'           => '#808080',
						'button_color'         => '#262626',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#4570dc;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#4570dc',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#4570dc',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#808080;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#808080',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#808080',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#4570dc;',
						'link_color' => '#4570dc',
					),
				),
				'almond_column'    => array(
					'name'             => 'widget-almond_column',
					'css'              => 'max-width:350px;color:#1e3d59;background-color:#e8ddbb;text-align:justify;',
					'color'            => '#1e3d59',
					'background_color' => '#e8ddbb',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#e8ddbb',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#c1540c;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#c1540c',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#c1540c',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#c1540c;',
						'link_color' => '#c1540c',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#c1540c;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#c1540c',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#c1540c;',
						'link_color' => '#c1540c',
					),
				),
				'navy_blue_box'    => array(
					'name'             => 'widget-navy_blue_box',
					'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:justify;border-radius:15px;',
					'color'            => '#e5e5e5',
					'background_color' => '#2a3e71',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#2a3e71',
					'border_radius'    => '15',
					'layout'           => 'default',
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;width:100%;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;width:100%;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#e5e5e5',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
				),
				'dark_row'         => array(
					'name'             => 'widget-dark_row',
					'css'              => 'max-width:350px;color:#ffffff;background-color:#323742;text-align:center;',
					'color'            => '#ffffff',
					'background_color' => '#323742',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#323742',
					'border_radius'    => '0',
					'layout'           => 'default',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#2b806a;color:#ffffff;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid 4570dc',
						'link_color'           => '#ffffff',
						'button_color'         => '#2b806a',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#2b806a',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(50, 55, 66, 0);color:#2b806a;display:block;max-width:5rem;margin:0.5rem auto 0 auto;border:1px solid #2b806a;',
						'link_color'           => '#2b806a',
						'button_color'         => '#323742',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#2b806a',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#2b806a;',
						'link_color' => '#2b806a',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#2b806a;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#2b806a',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#2b806a',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#323742;color:#2b806a;margin:0 0.5rem 0 0',
						'link_color'           => '#2b806a',
						'button_color'         => '#323742',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#323742',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#2b806a;',
						'link_color' => '#2b806a',
					),
				),
				'navy_blue_center' => array(
					'name'             => 'widget-navy_blue_center',
					'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:center;',
					'color'            => '#e5e5e5',
					'background_color' => '#2a3e71',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#2a3e71',
					'border_radius'    => '0',
					'layout'           => 'default',
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;min-width:5rem;margin:0 0.5rem 0 0;border: 1px solid #369ee3;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;border:1px solid #e5e5e5;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#e5e5e5',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#118635',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#369ee3;',
						'link_color' => '#369ee3',
					),
				),
				'grey_column'      => array(
					'name'             => 'widget-grey_column',
					'css'              => 'max-width:350px;color:#000000;background-color:#f4f4f4;text-align:justify;border: 1px solid #111111;',
					'color'            => '#000000',
					'background_color' => '#f4f4f4',
					'opacity'          => '1',
					'border_style'     => 'solid',
					'border_width'     => '1',
					'border_color'     => '#111111',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#C1263E;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#C1263E',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#C1263E',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#111111;color:#ffffff;display:block;max-width:10rem;margin:0.5rem auto 0 auto;',
						'link_color'           => '#ffffff',
						'button_color'         => '#111111',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#111111',
						'button_border_radius' => '0',
					),
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#C1263E;',
						'link_color' => '#C1263E',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#C1263E;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#C1263E',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#C1263E',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#111111;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#111111',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#111111',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#C1263E;',
						'link_color' => '#C1263E',
					),
				),
				'grey_center'      => array(
					'name'             => 'widget-grey_center',
					'css'              => 'max-width:350px;color:#000000;background-color:#f4f4f4;text-align:center;',
					'color'            => '#000000',
					'background_color' => '#f4f4f4',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#f4f4f4',
					'border_radius'    => '0',
					'layout'           => 'classic',
					'readmore'         => array(
						'text'       => 'Read More',
						'as_button'  => false,
						'css'        => 'color:#ac4008;',
						'link_color' => '#ac4008',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#ac4008;color:#ffffff;min-width:5rem;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ac4008',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#ac4008',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#ac4008;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#ac4008',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#ac4008',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#252525;color:#ffffff;margin:0 0.5rem 0 0',
						'link_color'           => '#ffffff',
						'button_color'         => '#252525',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#252525',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#ac4008;',
						'link_color' => '#ac4008',
					),
				),
				'navy_blue_square' => array(
					'name'             => 'widget-navy_blue_square',
					'css'              => 'max-width:350px;color:#e5e5e5;background-color:#2a3e71;text-align:justify;',
					'color'            => '#e5e5e5',
					'background_color' => '#2a3e71',
					'opacity'          => '1',
					'border_style'     => 'none',
					'border_width'     => '0',
					'border_color'     => '#2a3e71',
					'border_radius'    => '0',
					'layout'           => 'default',
					'decline'          => array(
						'text'                 => 'Decline',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;width:41%;margin:0 0.5rem 0 0;min-width:5rem;border:1px solid #369ee3;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'settings'         => array(
						'text'                 => 'Cookie Settings',
						'as_button'            => true,
						'css'                  => 'background-color:rgba(54,158,227,0);color:#e5e5e5;width:41%;min-width:5rem;float:right;border:1px solid #369ee3;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '0',
						'button_border_style'  => 'solid',
						'button_border_width'  => '1',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'accept'           => array(
						'text'                 => 'Accept',
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;width:100%;margin:1rem auto 0 auto;min-width:5rem;',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'confirm'          => array(
						'text'                 => __( 'Confirm', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#de7834;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'cancel'           => array(
						'text'                 => __( 'Cancel', 'gdpr-cookie-consent' ),
						'as_button'            => true,
						'css'                  => 'background-color:#369ee3;color:#e5e5e5;margin:0 0.5rem 0 0',
						'link_color'           => '#e5e5e5',
						'button_color'         => '#369ee3',
						'button_size'          => 'medium',
						'button_opacity'       => '1',
						'button_border_style'  => 'none',
						'button_border_width'  => '0',
						'button_border_color'  => '#369ee3',
						'button_border_radius' => '0',
					),
					'donotsell'        => array(
						'text'       => __( 'Do Not Sell My Personal Information', 'gdpr-cookie-consent' ),
						'as_button'  => false,
						'css'        => 'color:#007cba;',
						'link_color' => '#007cba',
					),
				),
			),
		)
	);
	return $templates[ $template_type ];
}

/**
 * Wizard Template
 */

?>

<div class="gdpr-wizard-top-container">
	<img class="gdpr-wizard-logo" src="<?php echo esc_url( $image_path ) . 'gdprLogo.png'; ?>">
	<span class="gdpr-main-heading">WP Cookie Consent</span>

</div>

<div class="gdpr-wizard-main-container" id="gdpr-cookie-consent-settings-app-wizard">

<div class="form-container">

<!-- Cross Button  -->

<span id="closeButton" class="close-wizard"></span>

		<!-- form  -->
		<form id="gcc-save-settings-form-wizard" class="gcc-save-wizard-settings-form">
			<input type="hidden" name="gcc_settings_form_nonce_wizard" value="<?php echo esc_attr( wp_create_nonce( 'gcc-settings-form-nonce-wizard' ) ); ?>"/>

			<ul id="progressbar">
			<li class="active" id="step1">
				<div class="progress-step">
					<strong class="progress-bar-label getting-started-progress">Getting Started</strong>
					<div class="container">
					<div class="horizontal-line line-step-1"></div>
					</div>

				</div>
			</li>
			<li class="active"id="step2">
				<div class="progress-step">
					<strong class="progress-bar-label">General</strong>
					<div class="container">
					<div class="horizontal-line line-step-2"></div>
					<img class="step-images selected-step-img" src="<?php echo esc_url( $image_path ) . 'selected-step.png'; ?>">

					</div>
				</div>
			</li>
			<li id="step3">
				<div class="progress-step">
					<strong class="progress-bar-label">Configuration</strong>
					<div class="container">
					<div class="horizontal-line line-step-3"></div>
					<img class="step-images not-selected-step-img" src="<?php echo esc_url( $image_path ) . 'not-selected-step.png'; ?>">

					</div>
				</div>
			</li>
			<li id="step4">
				<div class="progress-step">
					<strong class="progress-bar-label">Finish</strong>
					<div class="container">
					<div class="horizontal-line line-step-4"></div>
					<img class="step-images finish-step-img" src="<?php echo esc_url( $image_path ) . 'finish.png'; ?>">

					</div>
				</div>
			</li>
			</ul>
			<div class="progress">
			<div class="progress-bar"></div>
			</div>
			<br>
			<div class="step-content">

				<!-- First Tab Conetent Start  -->

			<fieldset class="general-tab-content">
			<!-- radio button law  -->

				<div class="select-rule">
					<div class="select-law-rule-label"><label for="gdpr-cookie-consent-policy-type"><?php esc_attr_e( 'Which privacy law or guideline do you want to use as the default for your worldwide visitors?', 'gdpr-cookie-consent' ); ?></label></div>
					<div class="select-law-rule-options">
						<div class="form-group" id="gdpr-cookie-consent-policy-type">
						<label>
							<input type="radio" name="gcc-gdpr-policy" value="gdpr" v-model="gdpr_policy" @change="cookiePolicyChange">
							General Data Protection Regulation
						</label><br>
						<label>
							<input type="radio" name="gcc-gdpr-policy" value="lgpd" v-model="gdpr_policy" @change="cookiePolicyChange">
							General Data Protection Law ( LGPD )
						</label><br>
						<label>
							<input type="radio" name="gcc-gdpr-policy" value="ccpa" v-model="gdpr_policy" @change="cookiePolicyChange">
							The California Consumer Privacy Act
						</label><br>
						<label>
							<input type="radio" name="gcc-gdpr-policy" value="both" v-model="gdpr_policy" @change="cookiePolicyChange">
							GDPR & CCPA
						</label><br>
						<label>
							<input type="radio" name="gcc-gdpr-policy" value="eprivacy" v-model="gdpr_policy" @change="cookiePolicyChange">
							ePrivacy Regulation
						</label><br>
						</div>
						<input type="hidden" name="gcc-gdpr-policy" v-model="gdpr_policy">
					</div>

					<?php
					// if gdpr-pro is enable only then show these options.
					if ( $is_pro ) {

						?>
					<!-- Location Enable/Disbale for different rule  -->
					<div v-show="show_visitor_conditions">

						<div>

							<!-- gdpr geo selection for pro -->
							<c-row class="gdpr-selection gdpr-pro-geo-ques" v-show="is_gdpr" >
									<c-col class="gdpr-selection-label"><label><?php esc_attr_e( 'Show only for EU visitors', 'gdpr-cookie-consent' ); ?> </label></c-col>
									<c-col class="gdpr-options">
										<label class="wizard_eu_safe">
										<?php
										$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
										$geo_options = get_option( 'wpl_geo_options' );
					
										?>
											
											<label>
												<input type="radio" name="gcc-eu-enable" value="yes" v-model="selectedRadioGdpr" @click="onSwitchEUEnable('yes')">
												Yes
											</label>
											<label>
												<input type="radio" name="gcc-eu-enable" value="no" v-model="selectedRadioGdpr" @click="onSwitchEUEnable('no')">
												No
											</label>
											<input type="hidden" name="gcc-eu-enable" v-model="is_eu_on">
										
									</c-col>
								</c-row>
							<!-- IAB geo selection for pro -->
								<c-row class="ccpa-iab-selection iab-pro-geo-ques" v-show="is_ccpa" >
									<c-col class="gdpr-selection-label"><label><?php esc_attr_e( 'Enable IAB Transparency and Consent Framework (TCF)', 'gdpr-cookie-consent' ); ?> </label></c-col>
									<c-col class="iab-options">
										<label>
										<input type="radio" name="gcc-iab-enable" value="yes" v-model="selectedRadioIab" @click="onSwitchIABEnable('yes')">
										Yes
										</label>
										<label>
										<input type="radio" name="gcc-iab-enable" value="no" v-model="selectedRadioIab" @click="onSwitchIABEnable('no')">
										No
										</label>
										<input type="hidden" name="gcc-iab-enable" v-model="is_iab_on">
									</c-col>
								</c-row>

							<!-- ccpa geo selection for pro  -->

							<c-row class="ccpa-selection ccpa-pro-geo-ques"  v-show="is_ccpa" >
								<c-col class="ccpa-selection-label"><label><?php esc_attr_e( 'Show only for California visitors', 'gdpr-cookie-consent' ); ?> </label></c-col>
								<c-col class="ccpa-options">
								<label class="wizard_eu_safe">
										<?php
										$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
										$geo_options = get_option( 'wpl_geo_options' );
											?>
											
											<label>
												<input type="radio" name="gcc-ccpa-enable" value="yes" v-model="selectedRadioCcpa" @click="onSwitchCCPAEnable('yes')">
												Yes
											</label>
											<label>
												<input type="radio" name="gcc-ccpa-enable" value="no" v-model="selectedRadioCcpa" @click="onSwitchCCPAEnable('no')">
												No
											</label>
											<input type="hidden" name="gcc-ccpa-enable" v-model="is_ccpa_on">
										
								</c-col>
							</c-row>

						</div>
					</div>

						<?php
					} else {

						?>
						<div>
						<c-row class="gdpr-selection gdpr-pro-geo-ques" v-show="is_gdpr" >
								<c-col class="gdpr-selection-label"><label><?php esc_attr_e( 'Show only for EU visitors', 'gdpr-cookie-consent' ); ?> </label></c-col>
								<c-col class="gdpr-options">
									<label class="wizard_eu_safe">
									<?php
									$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();

									$geo_options = get_option( 'wpl_geo_options' );
										?>
											<label>
												<input type="radio" name="gcc-eu-enable" value="yes" v-model="selectedRadioGdpr" @click="onSwitchEUEnable('yes')">
												Yes
											</label>
											<label>
												<input type="radio" name="gcc-eu-enable" value="no" v-model="selectedRadioGdpr" @click="onSwitchEUEnable('no')">
												No
											</label>
											<input type="hidden" name="gcc-eu-enable" v-model="is_eu_on">
										
								</c-col>
							</c-row>
						<!-- IAB geo selection for pro -->
							<c-row class="ccpa-iab-selection iab-pro-geo-ques" v-show="is_ccpa" >
								<c-col class="gdpr-selection-label"><label><?php esc_attr_e( 'Enable IAB Transparency and Consent Framework (TCF)', 'gdpr-cookie-consent' ); ?> </label></c-col>
								<c-col class="iab-options">
									<label>
									<input type="radio" name="gcc-iab-enable" value="yes" v-model="selectedRadioIab" @click="onSwitchIABEnable('yes')">
									Yes
									</label>
									<label>
									<input type="radio" name="gcc-iab-enable" value="no" v-model="selectedRadioIab" @click="onSwitchIABEnable('no')">
									No
									</label>
									<input type="hidden" name="gcc-iab-enable" v-model="is_iab_on">
								</c-col>
							</c-row>

						<!-- ccpa geo selection for pro  -->

						<c-row class="ccpa-selection ccpa-pro-geo-ques"  v-show="is_ccpa" >
							<c-col class="ccpa-selection-label"><label><?php esc_attr_e( 'Show only for California visitors', 'gdpr-cookie-consent' ); ?> </label></c-col>
							<c-col class="ccpa-options">
							<label class="wizard_eu_safe">
									<?php
									$the_options = Gdpr_Cookie_Consent::gdpr_get_settings();
									$geo_options = get_option( 'wpl_geo_options' );
										?>
										
										<label>
											<input type="radio" name="gcc-ccpa-enable" value="yes" v-model="selectedRadioCcpa" @click="onSwitchCCPAEnable('yes')">
											Yes
										</label>
										<label>
											<input type="radio" name="gcc-ccpa-enable" value="no" v-model="selectedRadioCcpa" @click="onSwitchCCPAEnable('no')">
											No
										</label>
										<input type="hidden" name="gcc-ccpa-enable" v-model="is_ccpa_on">
									
							</c-col>
						</c-row>
						</div>				
						<?php } ?>
											
				</div>

				<input type="button" name="next-step" class="next-step first-next-step" value="Save & Continue" />

			</fieldset>
			<!-- First Tab Conetent End  -->

			<!-- Second Tab Content Field set Start  -->
			<fieldset class="configure-tab-content">

				<div class="configure-tab-main-container">

						<!-- enable consent log  -->
						<div class="enable-consent-log">

							<div class="enable-consent-log-content">
								<c-col class="enable-consent-log-content-label"><label><?php esc_attr_e( 'Enable Consent Logging', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="enable-consent-log-switch">
									<c-switch v-bind="	 labelIcon " v-model="logging_on" id="gdpr-cookie-consent-logging" variant="3d"  color="success" :checked="logging_on"  :disabled="disableSwitch" v-on:update:checked="onSwitchLoggingOn"></c-switch>
									<input type="hidden" name="gcc-logging-on" v-model="logging_on">
								</c-col>
							</div>
						</div>

						<!-- enable/disbale script blocker  -->
						<div class="enable-script-blocker" v-show="gdpr_policy !== 'ccpa'">
							<div class="enable-script-blocker-content">
								<c-col class="enable-script-blocker-label"><label><?php esc_attr_e( 'Script Blocker', 'gdpr-cookie-consent' ); ?></label></c-col>
								<c-col class="enable-consent-log-switch">
									<c-switch v-bind="labelIcon" v-model="is_script_blocker_on" id="gdpr-cookie-consent-script-blocker" variant="3d"  color="success" :checked="is_script_blocker_on"  v-on:update:checked="onSwitchingScriptBlocker"></c-switch>
									<input type="hidden" name="gcc-script-blocker-on" v-model="is_script_blocker_on">
								</c-col>
							</div>
						</div>

						<!-- Respect do not track  -->
						<div class="enable-respect-do-not-track">
							<div class="enable-respect-do-not-track-content">
								<c-col class="enable-respect-do-not-track-label"><label><?php esc_attr_e( 'Respect Do Not Track & Global Privacy Control', 'gdpr-cookie-consent' ); ?></label>
								</c-col>
								<c-col class="enable-respect-do-not-track-switch">
									<c-switch v-bind= labelIcon v-model="do_not_track_on" id="gdpr-cookie-do-not-track" variant="3d" color="success" :checked="do_not_track_on" v-on:update:checked="onSwitchDntEnable"></c-switch>
									<input type="hidden" name="gcc-do-not-track" v-model="do_not_track_on">
								</c-col>
							</div>
						</div>

						<!-- Data Request -->
						<div class="enable-data-request">
							<div class="enable-data-request-content">
								<c-col class="enable-respect-data-request-label"><label><?php esc_attr_e( 'Enable Data Request Form', 'gdpr-cookie-consent' ); ?><tooltip class="gdpr_data_req_tooltip" text="<?php esc_html_e( 'Enable to add data request form to your Privacy Statement.', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
								</c-col>
								<c-col class="enable-data-request-switch">
									<c-switch v-bind="labelIcon " v-model="data_reqs_on" id="gdpr-cookie-data-reqs" variant="3d" color="success" :checked="data_reqs_on" v-on:update:checked="onSwitchDataReqsEnable"></c-switch>
									<input type="hidden" name="gcc-data_reqs" v-model="data_reqs_on">
								</c-col>
							</div>
						</div>

						<div class="enable-data-request">
							<div class="enable-data-request-content">
								<c-col v-show="data_reqs_on">
									<c-col class="enable-respect-data-request-shortcode-label"><label><?php esc_attr_e( 'Shortcode for Data Request', 'gdpr-cookie-consent' ); ?><tooltip class="gdpr-sc-tooltip" text="<?php esc_html_e( 'You can use this Shortcode [wpl_data_request] to display the data request form on any page', 'gdpr-cookie-consent' ); ?>"></tooltip></label>
									</c-col>
									<c-col class="enable-data-request-switch">
										<c-button class="btn btn-info" variant="outline" @click="copyTextToClipboard">{{ shortcode_copied ? 'Shortcode Copied!' : 'Click to Copy' }}</c-button>
									</c-col>
								</c-col>
							</div>
						</div>
						<!-- email box  -->
						<c-row v-show="data_reqs_on" id="gdpr-data-req-admin-container" >
										<div class="gdpr-data-req-main-container">

											<div class="gdpr-data-req-email-container">
												<!-- notification sender email  -->
												<div class="gdpr-data-req-sender-email">
													<c-col class="col-sm-12">
														<span>Notification Sender Email Address</span>
													</c-col>
													<!-- notification sender email text box  -->
													<c-col class="col-sm-12 gdpr-data-req-sender-email-input">
														<div id="validation-icon">
															<!-- Default state with the right tick -->
															<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" height="15" width="15" >
																<path fill="#00CF21"d="M438.6 105.4C451.1 117.9 451.1 138.1 438.6 150.6L182.6 406.6C170.1 419.1 149.9 419.1 137.4 406.6L9.372 278.6C-3.124 266.1-3.124 245.9 9.372 233.4C21.87 220.9 42.13 220.9 54.63 233.4L159.1 338.7L393.4 105.4C405.9 92.88 426.1 92.88 438.6 105.4H438.6z"></path>
															</svg>
														</div>
														<c-input name="data_req_email_text_field"  placeholder="example@example.com" v-model="data_req_email_address"  id="email-input"></c-input>

													</c-col>
													<!-- email validation script -->
													<script>
														document.addEventListener('DOMContentLoaded', function () {
															// Get the input element and the validation icon element
															var emailInput = document.getElementById('email-input');
															var validationIcon = document.getElementById('validation-icon');

															// Add an event listener on input change
															emailInput.addEventListener('input', function () {
																// Validate the email format using a regular expression
																var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
																var isValidEmail = emailPattern.test(emailInput.value);

																// Update the validation icon based on validity
																validationIcon.innerHTML = isValidEmail
																	? '<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" height="15" width="15"><path fill="#00CF21" d="M438.6 105.4C451.1 117.9 451.1 138.1 438.6 150.6L182.6 406.6C170.1 419.1 149.9 419.1 137.4 406.6L9.372 278.6C-3.124 266.1-3.124 245.9 9.372 233.4C21.87 220.9 42.13 220.9 54.63 233.4L159.1 338.7L393.4 105.4C405.9 92.88 426.1 92.88 438.6 105.4H438.6z"></path></svg>'
																	: '<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" height="15" width="15"><path fill="red" d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z"></path></svg>';

																// Adjust the padding-right property based on the presence of the icon
																emailInput.style.paddingRight = isValidEmail ? '30px' : '0';
															});
														});
													</script>
												</div>

												<div class="gdpr-data-req-email-subject">
													<!-- notification email subject  -->
													<c-col class="col-sm-12">
														<span>Notification Email Subject</span>
													</c-col>
													<!-- notification email subject text box  -->
													<c-col class="col-sm-12 gdpr-data-req-subject-input">
														<div id="validation-icon-subject">
															<!-- Default state with the right tick -->
															<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" height="15" width="15" >
																<path fill="#00CF21" d="M438.6 105.4C451.1 117.9 451.1 138.1 438.6 150.6L182.6 406.6C170.1 419.1 149.9 419.1 137.4 406.6L9.372 278.6C-3.124 266.1-3.124 245.9 9.372 233.4C21.87 220.9 42.13 220.9 54.63 233.4L159.1 338.7L393.4 105.4C405.9 92.88 426.1 92.88 438.6 105.4H438.6z"></path>
															</svg>
														</div>
														<c-input name="data_req_subject_text_field" placeholder="We have received your request" v-model="data_req_subject" id="subject-input"></c-input>
													</c-col>
												</div>

												<div class="gdpr-data-req-email-content">
													<!-- notification email content  -->
													<c-col class="col-sm-12">
														<span>Notification Email Content</span>
													</c-col>
												</div>

												<div class="gdpr-data-req-email-editor">
													<c-col class="col-sm-12">
														<div class="gdpr-add-media-link-icon">
															<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
															<path d="M14 10L10 14" stroke="#3399FF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
															<path d="M16 13L18 11C19.3807 9.61929 19.3807 7.38071 18 6V6C16.6193 4.61929 14.3807 4.61929 13 6L11 8M8 11L6 13C4.61929 14.3807 4.61929 16.6193 6 18V18C7.38071 19.3807 9.61929 19.3807 11 18L13 16" stroke="#3399FF" stroke-width="1.5" stroke-linecap="round"/>
															</svg>
														</div>
														<c-button id="add-media-button" class="gdpr-renew-now-btn pro" variant="outline" @click="onClickAddMedia"><span><?php esc_html_e( 'Add Media', 'gdpr-cookie-consent' ); ?></span></c-button>

													</c-col>
													<!-- notification text box  -->
													<c-col class="col-sm-12">
														<vue-editor name="data_req_mail_content_text_field" v-model="data_req_editor_message"></vue-editor>
														<input type="hidden" name="data_req_mail_content_text_field" v-model="data_req_editor_message">
													</c-col>
												</div>
											</div>

										</div>


									</c-row>

						<!-- show cookie notice  -->
						<div class="show-cookie-notice">
								<div class="show-cookie-notice-content">
									<c-col class="show-cookie-content-label"><label><?php esc_attr_e( 'Show Cookie Notice as', 'gdpr-cookie-consent' ); ?></label></c-col>
									<c-col class="show-cookie-content-dropdown">
										<input type="hidden" name="show-cookie-as" v-model="show_cookie_as">
										<v-select class="form-group" id="gdpr-show-cookie-as" :reduce="label => label.code" :options="show_cookie_as_options" v-model="show_cookie_as"  @input="cookieTypeChange"></v-select>
									</c-col>
								</div>
						</div>

						<!-- show templates  -->

						<div class="show-cookie-template">

							<?php $the_options = Gdpr_Cookie_Consent::gdpr_get_settings(); ?>

							<div v-show="is_gdpr || is_lgpd" class="show-cookie-template-card">
								<c-card-header class="show-cookie-template-label"><?php esc_html_e( 'Choose template for your cookie bar', 'gdpr-cookie-consent' ); ?></c-card-header>
								<c-card-body>
									<!-- banner templates  -->
									<c-row v-show="show_banner_template" class="show-banner-template">
										<c-col class="col-sm-3 left-side-banner-template" >
											<input type="hidden" name="gdpr-banner-template" v-model="banner_template">
											<input type="hidden" name="gcc-revoke-consent-text-color" v-model="button_revoke_consent_text_color">
											<input type="hidden" name="gcc-revoke-consent-background-color" v-model="button_revoke_consent_background_color">
										</c-col>
										<div class="">
										<?php print_template_boxes( 'banner', get_templates( 'banner' ), $the_options['banner_template'] ); ?>
										</div>
									</c-row>
									<!-- popup templates  -->
									<c-row v-show="show_popup_template">
										<c-col class="col-sm-4 left-side-popup-template">
											<input type="hidden" name="gdpr-popup-template" v-model="popup_template">
											<input type="hidden" name="gcc-revoke-consent-text-color" v-model="button_revoke_consent_text_color">
											<input type="hidden" name="gcc-revoke-consent-background-color" v-model="button_revoke_consent_background_color">
										</c-col>
										<div class="">
										<?php print_template_boxes( 'popup', get_templates( 'popup' ), $the_options['popup_template'] ); ?>
										</div>
									</c-row>
									<!-- widget templates  -->
									<c-row v-show="show_widget_template">
										<c-col class="col-sm-4 left-side-widget-template">
											<input type="hidden" name="gdpr-widget-template" v-model="widget_template">
											<input type="hidden" name="gcc-revoke-consent-text-color" v-model="button_revoke_consent_text_color">
											<input type="hidden" name="gcc-revoke-consent-background-color" v-model="button_revoke_consent_background_color">
										</c-col>
										<div class="">
										<?php print_template_boxes( 'widget', get_templates( 'widget' ), $the_options['widget_template'] ); ?>
										</div>
									</c-row>

									<input type="hidden" name="gdpr-template" v-model="template">
								</c-card-body>
							</div>

						</div>

				</div>
				<input type="button" name="next-step" class="next-step second-next-step" value="Save & Continue" />
				<input type="button" name="previous-step" class="previous-step first-previous-step" value="< Go Back" />

			</fieldset>
			<!-- Second Tab Content Field set End  -->

			<!-- third Tab Content Field set start -->
			<fieldset class="finish-tab-content">

			<div class="finish-tab-main-container">

				<div class="thank-you-text">
					<span>Thank you for choosing WP Cookie Consent plugin - the most powerful cookie consent WordPress plugin.</span>
				</div>

				<div class="tab-row">
						<div class="column">
							<div class="tab-card">
								<img class="finish-card-img" src="<?php echo esc_url( $image_path ) . 'help-center.png'; ?>" >
								<div class="card-heading">Help Center</div>
								<div class="card-info">Read the documentation to find answers to your questions</div>
								<div class="learn-more-link help-center-link"> <a href="https://club.wpeka.com/docs/wp-cookie-consent/">Learn More >></a></div>
							</div>
						</div>
						<div class="column">
							<div class="tab-card">
								<img class="finish-card-img" src="<?php echo esc_url( $image_path ) . 'video.png'; ?>" >
								<div class="card-heading">Video Guides</div>
								<div class="card-info">Browse through these video tutorials to learn more about how WP Cookie Consent works.</div>
								<div class="learn-more-link video-guide-link"> <a href="https://club.wpeka.com/docs/wp-cookie-consent/video-guides/video-resources/">Learn More >></a></div>
							</div>
						</div>
						<div class="column">
							<div class="tab-card">
								<img class="finish-card-img" src="<?php echo esc_url( $image_path ) . 'faqs.png'; ?>" >
								<div class="card-heading">FAQs</div>
								<div class="card-info">Find answers to some of the most commonly asked questions.</div>
								<div class="learn-more-link faqs-link"> <a href="https://club.wpeka.com/docs/wp-cookie-consent/faqs/faq-2/">Learn More >></a></div>
							</div>
						</div>

				</div>

			</div>

				<input type="button" name="next-step" @click="saveWizardCookieSettings"  class="submit-button final-next-step" value="Save & Close" />
				<input type="button" name="previous-step" class="previous-step second-previous-step" value="< Go Back" />
			</fieldset>
			</div>
		</form>

	</div>

</div>



<script>

jQuery(document).ready(function () {
	var currentGfgStep, nextGfgStep, previousGfgStep;
	var opacity;
	var current = 2;
	var steps = jQuery("fieldset").length;
	var imagePath = "<?php echo esc_url( $image_path ); ?>";
	var isProActive = "<?php echo esc_url( $is_pro ); ?>";

	setProgressBar(current);

	jQuery(".next-step").click(function () {

		currentGfgStep = jQuery(this).parent();
		nextGfgStep = jQuery(this).parent().next();

		jQuery("#progressbar li").eq(jQuery("fieldset")
			.index(nextGfgStep)).addClass("active");

		nextGfgStep.show();
		currentGfgStep.animate({ opacity: 0 }, {
			step: function (now) {
				opacity = 1 - now;

				currentGfgStep.css({
					'display': 'none',
					'position': 'relative'
				});
				nextGfgStep.css({ 'opacity': opacity });
			},
			duration: 500
		});
		setProgressBar(++current);
	});

	jQuery(".previous-step").click(function () {

		currentGfgStep = jQuery(this).parent();
		previousGfgStep = jQuery(this).parent().prev();

		jQuery("#progressbar li").eq(jQuery("fieldset")
			.index(currentGfgStep)).removeClass("active");

		previousGfgStep.show();

		currentGfgStep.animate({ opacity: 0 }, {
			step: function (now) {
				opacity = 1 - now;

				currentGfgStep.css({
					'display': 'none',
					'position': 'relative'
				});
				previousGfgStep.css({ 'opacity': opacity });
			},
			duration: 500
		});
		setProgressBar(--current);
	});

	//first next button
	jQuery(".first-next-step").click(function () {
		jQuery('.line-step-2').addClass('right-side-line');
		jQuery('.line-step-3').addClass('left-side-line');

		//change the src of the image when clicked on the first next button
		jQuery('.selected-step-img').attr('src', imagePath + 'tick.png');
			jQuery('.not-selected-step-img').attr('src', imagePath + 'selected-step.png');

	});

	//first previous button
	jQuery(".first-previous-step").click(function () {
		jQuery('.line-step-2').removeClass('right-side-line');
		jQuery('.line-step-3').removeClass('left-side-line');

		//change the src of the image back to selected when prvious on the first next button
		jQuery('.selected-step-img').attr('src', imagePath + 'selected-step.png');
			jQuery('.not-selected-step-img').attr('src', imagePath + 'not-selected-step.png');

	});

	//second next button

	jQuery(".second-next-step").click(function () {
		jQuery('.line-step-4').addClass('last-step-right-side');
		jQuery('.line-step-3').addClass('left-side-line');
		jQuery('.line-step-3').addClass('right-side-line');

		jQuery('.not-selected-step-img').attr('src', imagePath + 'tick.png');
			jQuery('.finish-step-img').attr('src', imagePath + 'selected-step.png');


	});

	//second previous button

	jQuery(".second-previous-step").click(function () {
		jQuery('.line-step-4').removeClass('last-step-right-side');
		jQuery('.line-step-3').removeClass('right-side-line');

		jQuery('.finish-step-img').attr('src', imagePath + 'finish.png');
		jQuery('.not-selected-step-img').attr('src', imagePath + 'selected-step.png');

	});

	function setProgressBar(currentStep) {
		var percent = parseFloat(100 / steps) * current;
		percent = percent.toFixed();
		jQuery(".progress-bar")
			.css("width", percent + "%")
	}

	jQuery(".submit").click(function () {
		return false;
	})

	//if gdpr pro is active hide the go-pro image from the templates

	if ( isProActive ) {

		jQuery(".gdpr-go-pro-label").hide()

	}

	//close wizard button

	jQuery("#closeButton").click(function() {
		// Get the admin URL
		var adminUrl = "<?php echo esc_url( admin_url() ); ?>";

		// Redirect to the dashboard submenu
		window.location.href = adminUrl + "/admin.php?page=gdpr-cookie-consent";
	});

	//save and close submission

	jQuery(".submit-button").click(function() {
		// Get the current hash
		var hash = window.location.hash;


		// Reload the current page with the hash
		window.location.href = window.location.href;

		// Delay redirecting to the new URL after reloading the page
		setTimeout(function() {
			// Get the admin URL
			var adminUrl = "<?php echo esc_url( admin_url() ); ?>";

			// Redirect to the dashboard submenu after a delay
			window.location.href = adminUrl + "/admin.php?page=gdpr-cookie-consent";
		}, 1000); // Change the delay time as needed
});




});


</script>


<?php
