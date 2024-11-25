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
$ab_options = get_option('wpl_ab_options');
// Instantiate a new object of the GDPR_Cookie_Consent_Settings class.
$this->settings = new GDPR_Cookie_Consent_Settings();

// Call the methods from the instantiated object to get user parameters.
$is_user_connected      = $this->settings->is_connected();

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
			<p class="gdpr-configuration-line-divider"></p>
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
							<?php endif;?>
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
			
			<div class="gdpr-wizard-header-section">
				<div class="gdpr-general-wizard-logo-container"><img class="gdpr-general-wizard-logo" src="<?php echo esc_url( $image_path ) . 'wp-Legal-pages-logo.png'; ?>">
					<span class="gdpr-general-wizard-main-heading">Welcome to WP Cookie Consent</span>
					<p class="gdpr-general-wizard-sub-heading">Follow the guided wizard to get started</p>
				</div>
				<div class="gdpr-wizard-progress-bar">
					<div class="gdpr-wizard-progress-bar-step1">
						<p class="gdpr-wizard-progress-bar-content1">1</p>
					</div>
					<div id="horizontal-line-id"class="horizontal-line"></div>
					<div id="gdpr-wizard-progress-bar-before"class="gdpr-wizard-progress-bar-step2-before">
						<p class="gdpr-wizard-progress-bar-content2-before">2</p>
					</div>
				</div>
				<br>
			</div>
			<div class="gdpr-wizard-thankyou-page">
				<div class="gdpr-general-wizard-thankyou-container"><img class="gdpr-general-wizard-thankyou-checked" src="<?php echo esc_url( $image_path ) . 'wizard-thakyou-checkd.svg'; ?>">
					<span class="gdpr-wizard-thankyou-heading">Congratulations! Your Banner Is Live Now</span>	
					<div class="gdpr-wizard-thankyou-container">
						<input type="button" name="live-preview" class="gdpr-wizard-thankyou-live-preview" value="Live Preview" />
						<input type="button" name="edit-banner" class="gdpr-wizard-thankyou-edit-banner" value="Edit Banner" />
					</div>
				</div>
			</div>
			<div class="step-content">

				<!-- First Tab Conetent Start  -->
				<fieldset class="general-tab-content">
				<!-- radio button law  -->

					<div class="select-rule">
						<div class="general-tab-content-heading">General</div>
						<div class="select-law-rule-label"><label for="gdpr-cookie-consent-policy-type" class="gdpr-cookie-consent-policy-text"><?php esc_attr_e( 'Which privacy law or guideline do you want to use as the default for your worldwide visitors?', 'gdpr-cookie-consent' ); ?></label></div>
						<div class="select-law-rule-options">
							<div class="form-group" id="gdpr-cookie-consent-policy-type">
							<label class="wp-selec-law-container">
								<input type="radio" name="gcc-gdpr-policy" value="gdpr" v-model="gdpr_policy" @change="cookiePolicyChange">
								<span class="wp-select-law-test">General Data Protection Regulation</span>
							</label><br>
							<label class="wp-selec-law-container">
								<input type="radio" name="gcc-gdpr-policy" value="lgpd" v-model="gdpr_policy" @change="cookiePolicyChange">
								<span class="wp-select-law-test">General Data Protection Law ( LGPD )</span>
							</label><br>
							<label class="wp-selec-law-container">
								<input type="radio" name="gcc-gdpr-policy" value="ccpa" v-model="gdpr_policy" @change="cookiePolicyChange">
								<span class="wp-select-law-test">The California Consumer Privacy Act</span>
							</label><br>
							<?php if($ab_options['ab_testing_enabled'] === 'false' || $ab_options['ab_testing_enabled'] === false  ){ ?>
							<label class="wp-selec-law-container">
								<input type="radio" name="gcc-gdpr-policy" value="both" v-model="gdpr_policy" @change="cookiePolicyChange">
								<span class="wp-select-law-test">GDPR & CCPA</span>
							</label><br>
							<?php }?>
							<label class="wp-selec-law-container">
								<input type="radio" name="gcc-gdpr-policy" value="eprivacy" v-model="gdpr_policy" @change="cookiePolicyChange">
								<span class="wp-select-law-test">ePrivacy Regulation</span>
							</label><br>
							<?php if($ab_options['ab_testing_enabled'] === 'true' || $ab_options['ab_testing_enabled'] === true  ){ ?>
							<p class="policy-description">GDPR & CCPA cannot be selected while the Cookie Banner A/B Test is active. Please disable A/B Test to enable this compliance option.</p>
							<?php }?>
							</div>
							<input type="hidden" name="gcc-gdpr-policy" v-model="gdpr_policy">
						</div>
						<div v-show="!is_eprivacy && !is_lgpd">
							<div><div class="select-law-rule-sublabel" id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Cookie Banner Geo-Targeting', 'gdpr-cookie-consent' ); ?></div></div>
						</div>
						<div class="geo-targeting-wizard-section">
						<div v-show="gdpr_policy === 'gdpr' || gdpr_policy === 'both' || gdpr_policy === 'ccpa'">
							<div style="padding-bottom: 10px;display: flex;align-items: center;gap: 4px;"><input class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-worldwide-enable"v-model="selectedRadioWorldWide" @click="onSwitchWorldWideEnable"><label class="wp-select-law-test"><?php esc_attr_e( 'Worldwide', 'gdpr-cookie-consent' ); ?></label></div>
							<div>
								<input type="hidden" name="gcc-worldwide-enable" v-model="is_worldwide_on">
							</div>
						</div>
						<div v-show="gdpr_policy === 'gdpr' || gdpr_policy === 'both'">
							<?php
							$geo_options = get_option( 'wpl_geo_options' );
							if ( !$is_user_connected) :
								?>
								<div style="padding-bottom: 10px;display: flex;align-items: center;gap: 4px;"><input id="gdpr-visitors-condition-radio-btn-disabled-gdpr-wizard"class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-eu-enable" disabled><label class="wp-select-law-test"><?php esc_attr_e( 'EU Countries & UK', 'gdpr-cookie-consent' ); ?></label></div>
								<p class=" gdpr-eu_visitors_message-gdpr">
									<?php esc_attr_e( 'To enable this feature, connect to your free account', 'gdpr-cookie-consent' ); ?>
								</p>
							<?php elseif ( $the_options['enable_safe'] === true || $the_options['enable_safe'] === 'true' ) : ?>
								<div  style="padding-bottom: 10px;display: flex;align-items: center;gap: 4px;" class="gdpr-disabled-geo-integration">
									<input id="gdpr-visitors-condition-radio-btn-disabled-gdpr-wizard" class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-eu-enable" disabled>
									<label><?php esc_attr_e( 'EU Countries & UK', 'gdpr-cookie-consent' ); ?></label>
								</div>
								<p class="gdpr-eu_visitors_message-gdpr">
									<?php esc_attr_e( 'Safe Mode enabled. Disable it in Compliance settings to configure Geo-Targeting settings.', 'gdpr-cookie-consent' ); ?>
								</p>
							<?php else : ?>
								<div style="padding-bottom: 10px;display: flex;align-items: center;gap: 4px;"><input class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-eu-enable" v-model="is_eu_on" @click="onSwitchEUEnable($event.target.checked)"><label class="wp-select-law-test"><?php esc_attr_e( 'EU Countries & UK', 'gdpr-cookie-consent' ); ?></label></div>
								<input type="hidden" name="gcc-eu-enable" v-model="is_eu_on">
							<?php endif; ?>
						</div>
						<div v-show="gdpr_policy === 'ccpa' || gdpr_policy === 'both'">
							<?php
								$geo_options = get_option( 'wpl_geo_options' );
							if ( !$is_user_connected ) :
								?>
								<div style="padding-bottom: 10px;display: flex;align-items: center;gap: 4px;"><input id="gdpr-visitors-condition-radio-btn-disabled-ccpa-wizard"class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-ccpa-enable" disabled><label style="width:114px;" class="wp-select-law-test"><?php esc_attr_e( 'United States', 'gdpr-cookie-consent' ); ?></label></div>
								<p class=" gdpr-eu_visitors_message-ccpa">
								<?php esc_attr_e( 'To enable this feature, connect to your free account', 'gdpr-cookie-consent' ); ?>
								</p>
							<?php elseif ( $the_options['enable_safe'] === true || $the_options['enable_safe'] === 'true' ) : ?>
								<div  style="padding-bottom: 10px;display: flex;align-items: center;gap: 4px;" class="gdpr-disabled-geo-integration"><input id="gdpr-visitors-condition-radio-btn-disabled-ccpa-wizard"class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-ccpa-enable" disabled><label style="width:114px;"><?php esc_attr_e( 'United States', 'gdpr-cookie-consent' ); ?></label></div>
								<p class="gdpr-eu_visitors_message-ccpa">
									<?php esc_attr_e( 'Safe Mode enabled. Disable it in Compliance settings to configure Geo-Targeting settings.', 'gdpr-cookie-consent' ); ?>
								</p>
							<?php else : ?>
								<div style="padding-bottom: 10px;display: flex;align-items: center;gap: 4px;"><input class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-ccpa-enable" v-model="is_ccpa_on" @click="onSwitchCCPAEnable($event.target.checked)"><label class="wp-select-law-test"><?php esc_attr_e( 'United States', 'gdpr-cookie-consent' ); ?></label></div>
								<input type="hidden" name="gcc-ccpa-enable" v-model="is_ccpa_on">
							<?php endif; ?>
						</div>
						<div v-show="gdpr_policy === 'gdpr' || gdpr_policy === 'both' || gdpr_policy === 'ccpa'">
							<?php
								$geo_options = get_option( 'wpl_geo_options' );
							if ( !$is_user_connected ) :
								?>
									<div style="padding-bottom: 10px;display: flex;align-items: center;gap: 4px;"><input class="gdpr-visiotrs-condition-radio-btn" id="gdpr-visitors-condition-radio-btn-disabled-both-wizard" type="checkbox" name="gcc-select-countries-enable"disabled><label class="wp-select-law-test"><?php esc_attr_e( 'Select Countries', 'gdpr-cookie-consent' ); ?></label></div>
									<p class=" gdpr-eu_visitors_message-both">
									<?php esc_attr_e( 'To enable this feature, connect to your free account', 'gdpr-cookie-consent' ); ?>
									</p>
							<?php elseif ( $the_options['enable_safe'] === true || $the_options['enable_safe'] === 'true' ) : ?>
									<div  style="padding-bottom: 10px;display: flex;align-items: center;gap: 4px;" class="gdpr-disabled-geo-integration"><input class="gdpr-visiotrs-condition-radio-btn" id="gdpr-visitors-condition-radio-btn-disabled-both-wizard" type="checkbox" name="gcc-select-countries-enable" disabled><label><?php esc_attr_e( 'Select Countries', 'gdpr-cookie-consent' ); ?></label></div>
									<p class="gdpr-eu_visitors_message-both">
									<?php esc_attr_e( 'Safe Mode enabled. Disable it in Compliance settings to configure Geo-Targeting settings.', 'gdpr-cookie-consent' ); ?>
									</p>
							<?php else : ?>
								<div style="padding-bottom: 10px;display: flex;align-items: center;gap: 4px;"><input class="gdpr-visiotrs-condition-radio-btn" type="checkbox" name="gcc-select-countries-enable" v-model="selectedRadioCountry" @click="onSwitchSelectedCountryEnable($event.target.checked)"><label class="wp-select-law-test"><?php esc_attr_e( 'Select Countries', 'gdpr-cookie-consent' ); ?></label></div>
								<input type="hidden" name="gcc-select-countries-enable" v-model="is_selectedCountry_on">
							<?php endif; ?>
						</div>
						<div style="padding-bottom:10px;" class="select-countries-dropdown" v-show="(is_selectedCountry_on) && ( gdpr_policy === 'gdpr' || gdpr_policy === 'both' || gdpr_policy === 'ccpa' )">
							<v-select id="gdpr-cookie-consent-geotargeting-countries" placeholder="Select Countries":reduce="label => label.code" class="form-group" :options="list_of_countries" multiple v-model="select_countries_array" @input="onCountrySelect"></v-select>
							<input type="hidden" name="gcc-selected-countries" v-model="select_countries">
						</div>	
						</div>					
					</div>

					<input type="button" name="next-step" class="next-step first-next-step" value="Save & Continue" />

				</fieldset>
				<!-- First Tab Conetent End  -->

				<!-- Second Tab Content Field set Start  -->
				<fieldset class="configure-tab-content">

					<div class="configure-tab-main-container">
					<div class="general-tab-content-heading">Configuration</div>
					<p class="gdpr-configuration-line-divider"></p>
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
											<c-button class="wizard-data-request-btn" variant="outline" @click="copyTextToClipboard">{{ shortcode_copied ? 'Shortcode Copied!' : 'Click to Copy' }}</c-button>
										</c-col>
									</c-col>
								</div>
							</div>
							<!-- email box  -->
							<c-row v-show="data_reqs_on" id="gdpr-wizard-data-req-admin-container" >
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

							<!-- Banner’s Layout & Positions -->
							<div class="show-banner-layoout-position">
									<div class="show-cookie-notice-content">
										<c-col class="show-banner-layout-label"><label><?php esc_attr_e( 'Banner’s Layout & Positions', 'gdpr-cookie-consent' ); ?></label></c-col>
										<c-col v-show="show_cookie_as === 'banner'">
											<div
											@click="cookiebannerPositionChange('bottom')"
											style="display: inline-block; cursor: pointer;position:relative;left:-14px;">
											<div>
												<span id="banner-position-bottom-icon"
												class="<?php echo $the_options['notify_position_vertical'] == 'bottom' ? 'dashicons dashicons-saved' : ''; ?>"></span>
											</div>
											<img 
												id="banner-position-bottom-id"
												class="<?php echo $the_options['notify_position_vertical'] == 'bottom' ? 'banner-position-bottom' : ''; ?>" 
												src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/banner_bottom.svg'; ?>" 
												alt="Bottom"
											>
											</div>
											<div
												@click="cookiebannerPositionChange('top')"
												style="display: inline-block; cursor: pointer;position:relative; padding-left:14px;">
												<div>
													<span id="banner-position-top-icon"
													class="<?php echo $the_options['notify_position_vertical'] == 'top' ? 'dashicons dashicons-saved' : ''; ?>" ></span>
												</div>
												<img 
													id="banner-position-top-id"
													class="<?php echo $the_options['notify_position_vertical'] == 'top' ? 'banner-position-top' : ''; ?>" 
													src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/banner_top.svg'; ?>" 
													alt="Top"
												>
											</div>
											<input type="hidden" name="gcc-gdpr-cookie-position" v-model="cookie_position">
										</c-col>
										<c-col v-show="show_cookie_as === 'popup'">
												<img 
													id="popup-position-id"
													src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/pop_layout.svg'; ?>" 
													alt="Top"
												>
										</c-col>
										<c-col  class="gdpr-wizard-widget" v-show="show_cookie_as === 'widget'" style="padding-left:0px;">
													<div @click="cookiewidgetPositionChange('left')" style="display: inline-block; cursor: pointer;position:relative;"class="gdpr-wizard-widget-item">
												<div>
													<span id="widget-position-left-icon"
													class="<?php echo $the_options['notify_position_horizontal'] == 'left' ? 'dashicons dashicons-saved' : ''; ?>" ></span>
												</div>
												<img 
												id="widget-position-left-id"
													class="<?php echo $the_options['notify_position_horizontal'] == 'left' ? 'widget-position-top' : ''; ?>" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/widget_bottom_left.svg'; ?>" alt="Bottom_left">
											</div>
											<div @click="cookiewidgetPositionChange('right')" style="display: inline-block; cursor: pointer;"class="gdpr-wizard-widget-item">
												<div>
													<span id="widget-position-right-icon"
													class="<?php echo $the_options['notify_position_horizontal'] == 'right' ? 'dashicons dashicons-saved' : ''; ?>" ></span>
												</div>
												<img id="widget-position-right-id"
													class="<?php echo $the_options['notify_position_horizontal'] == 'right' ? 'widget-position-top' : ''; ?>" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/widget_bottom_right.svg'; ?>" alt="Bottom_right">
											</div>
											<div @click="cookiewidgetPositionChange('top_left')" style="display: inline-block; cursor: pointer;"class="gdpr-wizard-widget-item">
												<div>
													<span id="widget-position-top_left-icon"
													class="<?php echo $the_options['notify_position_horizontal'] == 'top_left' ? 'dashicons dashicons-saved' : ''; ?>" ></span>
												</div>
												<img id="widget-position-top_left-id"
													class="<?php echo $the_options['notify_position_horizontal'] == 'top_left' ? 'widget-position-top' : ''; ?>" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/widget_top_left.svg'; ?>" alt="Top_left">
											</div>
											<div @click="cookiewidgetPositionChange('top_right')" style="display: inline-block; cursor: pointer;"class="gdpr-wizard-widget-item">
												<div>
													<span id="widget-position-top_right-icon"
													class="<?php echo $the_options['notify_position_horizontal'] == 'top_right' ? 'dashicons dashicons-saved' : ''; ?>" ></span>
												</div>
												<img id="widget-position-top_right-id"
													class="<?php echo $the_options['notify_position_horizontal'] == 'top_right' ? 'widget-position-top' : ''; ?>" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/widget_top_right.svg'; ?>" alt="Top_right">
											</div>
											<input type="hidden" name="gcc-gdpr-cookie-widget-position" v-model="cookie_widget_position">
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
					<input type="button" name="next-step" @click="saveWizardCookieSettings" class="next-step second-next-step" id="gdpr-wizard-finish-btn" value="Finish Setup" />
					<input type="button" name="previous-step" class="previous-step first-previous-step" value="Go Back" />

				</fieldset>
				<!-- Second Tab Content Field set End  -->

				<!-- third Tab Content Field set start -->
				<fieldset class="finish-tab-content" class="gdpr-wizard-thankyou-main-container">

					<div class="gdpr-wizard-help-center">
						<div class="gdpr-help-item">
								<img class="gdpr-other-plugin-image" src="<?php echo esc_url( $image_path ) . 'help-center.svg'; ?>">
							<div class="gdpr-help-content">
							<span class="gdpr-help-caption">
								<?php esc_html_e( 'Help Center', 'gdpr-cookie-consent' ); ?>
							</span>
							<span class="gdpr-help-description">
								<?php esc_html_e( 'Read the documentation to find answers to your questions.', 'gdpr-cookie-consent' ); ?>
							</span>
							<a href="https://wplegalpages.com/docs/wp-cookie-consent/" target="_blank" class="gdpr-help-button"><?php esc_html_e( 'Learn More', 'gdpr-cookie-consent' ); ?> <img class="gdpr-other-plugin-arrow" :src="right_arrow.default"></a>
							</div>
						</div>
						<div class="gdpr-help-item">
								<img class="gdpr-other-plugin-image" src="<?php echo esc_url( $image_path ) . 'video.svg'; ?>">
							<div class="gdpr-help-content">
							<span class="gdpr-help-caption">
								<?php esc_html_e( 'Video Guides', 'gdpr-cookie-consent' ); ?>
							</span>
							<span class="gdpr-help-description">
								<?php esc_html_e( 'Explore video tutorials for insights on WP Cookie Consent functionality.', 'gdpr-cookie-consent' ); ?>
							</span>
							<a href="https://wplegalpages.com/docs/wp-cookie-consent/video-guides/" target="_blank" class="gdpr-help-button"><?php esc_html_e( 'Watch Now', 'gdpr-cookie-consent' ); ?> <img class="gdpr-other-plugin-arrow" :src="right_arrow.default"></a>
							</div>
						</div>
						<div class="gdpr-help-item">
								<img class="gdpr-other-plugin-image" src="<?php echo esc_url( $image_path ) . 'faqs.svg'; ?>">
							<div class="gdpr-help-content">
							<span class="gdpr-help-caption">
								<?php esc_html_e( 'FAQ with Answers', 'gdpr-cookie-consent' ); ?>
							</span>
							<span class="gdpr-help-description">
								<?php esc_html_e( 'Find answers to some of the most commonly asked questions.', 'gdpr-cookie-consent' ); ?>
							</span>
							<a href="https://wplegalpages.com/docs/wp-cookie-consent/faqs/" target="_blank" class="gdpr-help-button"><?php esc_html_e( 'Find Out', 'gdpr-cookie-consent' ); ?> <img class="gdpr-other-plugin-arrow" :src="right_arrow.default"></a>
							</div>
						</div>
					</div>
				</fieldset>

			</div>
		</form>

	</div>

</div>

<?php 
 $frontend_url = get_site_url(); ?>

<script>

jQuery(document).ready(function () {
	var currentGfgStep, nextGfgStep, previousGfgStep;
	var opacity;
	var current = 2;
	var steps = jQuery("fieldset").length;
	var imagePath = "<?php echo esc_url( $image_path ); ?>";
	var isProActive = "<?php echo esc_url( $is_pro ); ?>";

	// initial condition.
	jQuery(".gdpr-wizard-header-section").show();
    jQuery(".gdpr-wizard-thankyou-page").hide();

	jQuery(".next-step").click(function () {

		currentGfgStep = jQuery(this).parent();
		nextGfgStep = jQuery(this).parent().next();

		if (currentGfgStep.index() === 1) {
            jQuery(".gdpr-wizard-header-section").hide();
            jQuery(".gdpr-wizard-thankyou-page").show();
        } else {
            jQuery(".gdpr-wizard-header-section").show();
            jQuery(".gdpr-wizard-thankyou-page").hide();
        }

		jQuery("#gdpr-wizard-progress-bar-before").removeClass("gdpr-wizard-progress-bar-step2-before");
		jQuery("#gdpr-wizard-progress-bar-before").addClass("gdpr-wizard-progress-bar-step2");
		jQuery("#horizontal-line-id").removeClass("horizontal-line");
		jQuery("#horizontal-line-id").addClass("horizontal-line-after");

		nextGfgStep.show();
		currentGfgStep.animate({ opacity: 0 }, {
			step: function (now) {
				opacity = 1 - now;

				currentGfgStep.css({
					'display': 'none',
					'position': 'relative'
				});
				if (currentGfgStep.index() === 1) {
					nextGfgStep.css({ 'display':'flex','justify-content':'center','opacity': opacity });
				}
				else{
					nextGfgStep.css({ 'opacity': opacity });
				}
				
			},
			duration: 500
		});
	});

	jQuery(".previous-step").click(function () {

		currentGfgStep = jQuery(this).parent();
		previousGfgStep = jQuery(this).parent().prev();

		if (currentGfgStep.index() === 2) {
            jQuery(".gdpr-wizard-header-section").show();
            jQuery(".gdpr-wizard-thankyou-page").hide();
        }

		jQuery("#gdpr-wizard-progress-bar-before").removeClass("gdpr-wizard-progress-bar-step2");
		jQuery("#gdpr-wizard-progress-bar-before").addClass("gdpr-wizard-progress-bar-step2-before");
		jQuery("#horizontal-line-id").removeClass("horizontal-line-after");
		jQuery("#horizontal-line-id").addClass("horizontal-line");

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
	});

	jQuery(".submit").click(function () {
		return false;
	})

	//if gdpr pro is active hide the go-pro image from the templates

	if ( isProActive ) {

		jQuery(".gdpr-go-pro-label").hide()

	}

	// edit banner redirection

	jQuery(".gdpr-wizard-thankyou-edit-banner").click(function() {
		// Get the admin URL
		var adminUrl = "<?php echo esc_url( admin_url() ); ?>";

		// Redirect to the dashboard submenu
		window.location.href = adminUrl + "/admin.php?page=gdpr-cookie-consent/#cookie_settings";
	});

	//live preview redirection.

	jQuery('.gdpr-wizard-thankyou-live-preview').on('click', function() {
		window.open('<?php echo esc_url($frontend_url); ?>', '_blank');
	});




});


</script>


<?php
