<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://club.wpeka.com
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

 
	$data = Gdpr_Cookie_Consent::gdpr_get_all_vendors();
	$iabtcf_consent_data = Gdpr_Cookie_Consent::gdpr_get_iabtcf_vendor_consent_data();
	$gacm_data = Gdpr_Cookie_Consent::gdpr_get_gacm_vendors();
	$gacm_consent_data = isset( $iabtcf_consent_data["gacm_consent"]) ? $iabtcf_consent_data["gacm_consent"] : [];
	$allGacmVendorsFlag = false;
	$consent_data = isset( $iabtcf_consent_data["consent"] ) ? $iabtcf_consent_data["consent"] : [];
	$legint_data = isset( $iabtcf_consent_data["legint"] ) ? $iabtcf_consent_data["legint"] : [];
	$purpose_consent_data = isset( $iabtcf_consent_data["purpose_consent"] ) ? $iabtcf_consent_data["purpose_consent"] : [];
	$purpose_legint_data = isset( $iabtcf_consent_data["purpose_legint"] ) ? $iabtcf_consent_data["purpose_legint"] : [];
	$feature_consent_data = isset( $iabtcf_consent_data["feature_consent"] ) ? $iabtcf_consent_data["feature_consent"] : [];
	$allVendors = isset( $iabtcf_consent_data["allvendorIds"] ) ? $iabtcf_consent_data["allvendorIds"] : [];
	$allSpecialFeatures = isset( $iabtcf_consent_data["allSpecialFeatureIds"] ) ? $iabtcf_consent_data["allSpecialFeatureIds"] : [];
	$allVendorsFlag = false;	//flag for all vendors toggle button
	$allFeaturesFlag = false;

    $top_value = ( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true') ? intval( $the_options[ 'cookie_bar_border_radius' . $chosenBanner ] ) / 3 + 10 : intval($the_options['background_border_radius']) / 3 + 10;

	$abTesting = $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true';
	
	$color = $abTesting
	    ? $the_options['cookie_bar_color' . $chosenBanner]
	    : $the_options['background'];

	$opacity = $abTesting
	    ? $the_options['cookie_bar_opacity' . $chosenBanner]
	    : $the_options['opacity'];


	$opacityHex = strtoupper(str_pad(dechex((int) floor($opacity * 255)), 2, '0', STR_PAD_LEFT));
    $finalColor = strtoupper($color . $opacityHex);
    
	$acceptAllBGColor = ( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true') ? $the_options[ 'button_accept_all_button_color' . $chosenBanner ] : ( $the_options['cookie_usage_for'] === 'both' ? $the_options[ 'button_accept_all_button_color1'] : $the_options['button_accept_all_button_color'] );

	$acceptAllBGColor = $abTesting
		? $the_options[ 'button_accept_all_button_color' . $chosenBanner ]
		: ( $the_options['cookie_usage_for'] === 'both' 
			? $the_options[ 'button_accept_all_button_color1'] 
			: $the_options['button_accept_all_button_color'] );

	if (strtoupper(substr($finalColor, 0, -2)) === strtoupper($acceptAllBGColor)) {
        $cookieSettingsPopupAccentColor = $the_options['button_accept_all_link_color'];
    } else {
        $cookieSettingsPopupAccentColor = $acceptAllBGColor;
    }

?>

<?php if( $the_options['cookie_usage_for'] !== "ccpa" || $the_options['cookie_usage_for'] === "both" ) { ?>

<div class="gdprmodal gdprfade" id="gdpr-gdprmodal" role="dialog" data-keyboard="false" data-backdrop="<?php echo esc_html( $cookie_data['backdrop'] ); ?>" >
	<div class="gdprmodal-dialog gdprmodal-dialog-centered">
		<!-- Modal content-->
		<div class="gdprmodal-content" 
        style="
            background-color: <?php 
				echo esc_html($the_options['cookie_usage_for'] === "both" 
					? strtoupper($the_options['multiple_legislation_cookie_bar_color1'] . strtoupper( str_pad(dechex((int) floor($the_options['multiple_legislation_cookie_bar_opacity1'] * 255)), 2, '0', STR_PAD_LEFT) ))
					: $finalColor
				)?>;
            color: <?php 
				echo esc_html(( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
			 		? ( $the_options[ 'cookie_text_color' . $chosenBanner ] )
			 		: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['multiple_legislation_cookie_text_color1'] : $the_options['text'] )
				); ?>;
            border-style: <?php 
				echo esc_html(( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
			 		? ( $the_options[ 'border_style' . $chosenBanner ] )
			 		: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['multiple_legislation_border_style1'] : $the_options['background_border_style'] )
			 	); ?>;
            border-width: <?php 
				echo esc_html(( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
			 		? ( $the_options[ 'cookie_bar_border_width' . $chosenBanner ] . 'px' )
			 		: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['multiple_legislation_cookie_bar_border_width1'] : $the_options['background_border_width'] . 'px' )
				); ?>;
            border-radius: <?php 
				echo esc_html(( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
			 		? ( $the_options[ 'cookie_bar_border_radius' . $chosenBanner ] . 'px' )
			 		: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['multiple_legislation_cookie_bar_border_radius1'] : $the_options['background_border_radius'] . 'px')
			 	); ?>;
            border-color: <?php 
				echo esc_html(( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
			 		? ( $the_options[ 'cookie_border_color' . $chosenBanner ] )
			 		: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['multiple_legislation_cookie_border_color1'] : $the_options['background_border_color'] )
				); ?>;
			font-family: <?php 
				echo esc_html(( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
			 		? ( $the_options[ 'cookie_font' . $chosenBanner ] )
			 		: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['multiple_legislation_cookie_font1'] : $the_options['font_family'] )
				); ?>;
        ">
			<div class="gdprmodal-header">
            
				<button type="button" class="gdpr_action_button close" data-dismiss="gdprmodal" data-gdpr_action="close" 
                style="cursor: pointer; display: inline-flex; align-items: center; justify-content: center; position: absolute; top:20px; right: <?php echo 20 + ((int)$the_options[($ab_testing_enabled === "true" ? 'cookie_bar_border_radius' . $chosenBanner : ($the_options['cookie_usage_for'] === 'both' ? 'multiple_legislation_cookie_bar_border_radius1' : 'background_border_radius'))]) / 2;?>px; height: 20px; width: 20px; border-radius: 50%; color: <?php echo $the_options['cookie_usage_for'] == 'ccpa' ?  esc_html($the_options['button_donotsell_link_color' . $suffix]) : ((bool)$the_options['button_accept_all_as_button' . $suffix] === 'true' || (bool)$the_options['button_accept_all_as_button' . $suffix] === true || (bool)$the_options['button_accept_all_as_button' . $suffix] === 1 ? esc_html($the_options['button_accept_all_button_color' . $suffix]) : esc_html($the_options["button_accept_all_link_color" . $suffix]));?>;background-color: transparent;">
					<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z" fill="currentColor"/>
					</svg>
				</button>
			</div>
			<div class="gdprmodal-body classic classic-nvg" style="scrollbar-color: <?php echo esc_html( $cookieSettingsPopupAccentColor ); ?> transparent;">
				<div class="gdpr-details-content">
					<div class="gdpr-groups-container">
                 		<?php if ( $the_options['cookie_usage_for']==='gdpr' ) :?>
								<div class="gdpr-about-cookies"><?php echo $the_options['is_iabtcf_on'] ? esc_html__( $cookie_data['dash_about_message_iabtcf'], 'gdpr-cookie-consent' ) : esc_html__( $cookie_data['dash_about_message'], 'gdpr-cookie-consent' ); // phpcs:ignore ?></div>
							<?php elseif (  $the_options['cookie_usage_for']==='lgpd') :?>
								<div class="gdpr-about-cookies"><?php echo esc_html__( $cookie_data['dash_about_message_lgpd'], 'gdpr-cookie-consent' ); // phpcs:ignore ?></div>
							<?php elseif ( $the_options['cookie_usage_for']==='both' ) :?>
								<div class="gdpr-about-cookies"><?php echo $the_options['is_iabtcf_on'] ? esc_html__( $cookie_data['dash_about_message_iabtcf'], 'gdpr-cookie-consent' ) : esc_html__( $cookie_data['dash_about_message'], 'gdpr-cookie-consent' ); // phpcs:ignore ?></div>
							<?php endif; 
							if($the_options['is_gcm_on'] == 'true') : ?>
								<div class="gdpr-about-cookies"><?php echo esc_html("For more information on how Google's third party cookies operate and handle your data, see: "); // phpcs:ignore ?><a style="color: <?php echo $the_options['cookie_usage_for'] == 'ccpa' ?  esc_html($the_options['button_donotsell_link_color' . $suffix]) : ((bool)$the_options['button_accept_all_as_button' . $suffix] === 'true' || (bool)$the_options['button_accept_all_as_button' . $suffix] === true || (bool)$the_options['button_accept_all_as_button' . $suffix] === 1 ? esc_html($the_options['button_accept_all_button_color' . $suffix]) : esc_html($the_options["button_accept_all_link_color" . $suffix]));?>;" href="https://business.safety.google/privacy" target="blank"><?php echo esc_html("Google's Privacy Policy"); ?></a></div>
							<?php endif;
						if ( $the_options['is_iabtcf_on']) :
						?>
						<ul class="gdpr-iab-navbar">
							<li class="gdpr-iab-navbar-item" id="gdprIABTabCategory"><button class="gdpr-iab-navbar-button active">Cookie Categories</button></li>
							<li class="gdpr-iab-navbar-item" id="gdprIABTabFeatures"><button class="gdpr-iab-navbar-button">Purposes and Features</button></li>
							<li class="gdpr-iab-navbar-item" id="gdprIABTabVendors"><button class="gdpr-iab-navbar-button">Vendors</button></li>
						</ul>
						<?php endif; ?>
						<ul class="cat category-group tabContainer">
							<?php
							foreach ( $cookie_data['categories'] as $category ) {
								?>
								<li class="category-item">
								
								<div class="gdpr-column gdpr-category-toggle <?php echo esc_html( $the_options['template_parts'] ); ?>">
									<div class="gdpr-columns">
										
									 <div class="left">
									 	<span class="gdpr-dropdown-arrow">
											<svg width="25px" height="25px" viewBox="0 0 24.00 24.00" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
										</span>
										<a href="#" class="btn category-header" tabindex="0"><?php echo esc_html__( $category['gdpr_cookie_category_name'], 'gdpr-cookie-consent' ); // phpcs:ignore ?></a>
									 </div>
										
									<div class="right">
										<?php
										if ( 'necessary' === $category['gdpr_cookie_category_slug'] ) {
											?>
											<div class="toggle-group">
												<div class="always-active" style="color: <?php echo esc_html( $cookieSettingsPopupAccentColor ); ?>"><?php echo esc_html( $cookie_data['always'] ); ?></div>
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
														id="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" 
														class="category-switch-handler" type="checkbox" name="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" value="<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>">
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
									
									</div>
								</div>
								<div class="description-container hide">
									<div class="group-description" tabindex="0"><?php echo esc_html( $category['gdpr_cookie_category_description'] ); // phpcs:ignore ?></div>
									<!-- sub groups -->
									<?php
									if( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true' ) {
										if ( ( $the_options['button_settings_display_cookies' . $chosenBanner] === true || $the_options['button_settings_display_cookies' . $chosenBanner] === 'true' ) ) {
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
															<tr><td>
																<?php
																if ( $cookie['name'] ) {
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
																</td></tr>
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
									} else {
										//check if law is gdpr&ccpa (both)
										if ( $the_options['cookie_usage_for'] === 'both'){
											if ( ( $the_options['button_settings_display_cookies1'] === true || $the_options['button_settings_display_cookies1'] === 'true' ) ) {
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
																<tr><td>
																	<?php
																	if ( $cookie['name'] ) {
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
																	</td></tr>
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
										}else{
											if ( ( $the_options['button_settings_display_cookies'] === true || $the_options['button_settings_display_cookies'] === 'true' ) ) {
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
																<tr><td>
																	<?php
																	if ( $cookie['name'] ) {
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
																	</td></tr>
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
										}
									}
									?>
								</div>
								<hr style="
                                    margin-top: 10px;
                                    border-top: 1px solid <?php echo esc_attr( $cookieSettingsPopupAccentColor ); ?>;
                                ">
							</li>
								<?php
							}
							?>
						</ul>
						
						<?php if ( $the_options['is_iabtcf_on']) : ?>
						<ul class="category-group feature-group tabContainer">
							<?php
						    $values = ["Purposes", "Special Purposes","Features","Special Features"];
							foreach ( $values as $value ) {
								$display=false;
								$classnames = "";
								$allToggleFlag = false;
								switch($value){
									case "Purposes":
										$values  = $data->purposes;
										$purposeLegIntMap = $data->purposeVendorMap; 
										$count = $data->purposeVendorCount;
										$legintcount = $data->legintPurposeVendorCount;
										$display = true;
										$consentArray = $purpose_consent_data;
										$displayLegint = true;
										$classnames = "purposes";
										$allToggleFlag = false;	//flag for all purposes toggle button
										foreach ( $values as $key => $purpose ) {
											if ( in_array($purpose->id, $purpose_consent_data) ) {
												if( in_array($purpose->id, $data->allLegintPurposes) ) {
													if ( ! in_array($purpose->id, $purpose_legint_data) ) {
														$allToggleFlag = false;
														break;
													}
												}
												$allToggleFlag = true;
											}
											else {
												$allToggleFlag = false;
												break;
											}
										}
										break;
									case "Features":
										$values  = $data->features;
										$count = $data->featureVendorCount;
										$classnames = "features";
										break;
									case "Special Purposes":
										$values  = $data->specialPurposes;
										$count = $data->specialPurposeVendorCount;
										$classnames = "special-purposes";
										break;
									case "Special Features":
										$values  = $data->specialFeatures;
										$count = $data->specialFeatureVendorCount;
										$display = true;
										$allToggleFlag = $allFeaturesFlag;
										$consentArray = $feature_consent_data;
										$displayLegint = false;
										$classnames = "special-features";
										$allToggleFlag = false;	//flag for all purposes toggle button
										foreach ( $allSpecialFeatures as $feature ) {
											if ( in_array($feature, $feature_consent_data) ) {
												$allToggleFlag = true;
											}
											else {
												$allToggleFlag = false;
												break;
											}
										}
										break;
								}				
												
								?>
								<li class="category-item">
									
										
									<div class="gdpr-column gdpr-category-toggle <?php echo esc_html( $the_options['template_parts'] ); ?>">
										<div class="gdpr-columns">
											
										<div class="left">
											<span class="gdpr-dropdown-arrow">
												<svg width="25px" height="25px" viewBox="0 0 24.00 24.00" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
											</span>
											<a href="#" class="btn category-header" tabindex="0"><?php echo esc_html__( $value, 'gdpr-cookie-consent' ); // phpcs:ignore ?></a>
										</div>
										
										<div class="right">
										<?php
											if( $display ) {
											?>
												<div class="toggle-group">
													<div class="toggle">
														<div class="checkbox">
															<!-- DYNAMICALLY GENERATE Input ID  -->
															<input 
															<?php
															if ( $allToggleFlag ) {
																?>
																checked="checked"
																<?php
															} 
															?>
															id="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" class="<?php echo esc_html($classnames);?>-all-switch-handler" type="checkbox" name="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" value=<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>>
															<label for="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>">
																<span class="label-text"><?php echo esc_html( $category['gdpr_cookie_category_name'] ); ?></span>
															</label>
															<!-- DYNAMICALLY GENERATE Input ID  -->
														</div>
													</div>
												</div>
											<?php } ?>
										</div>
											
										
										</div>
									</div>
									<div class="description-container hide">
													<ul class="category-group feature-group tabContainer">
													<?php 
													
													
													foreach ( $values as $key => $value ) {
														?>
														<li class="category-item">
														<hr style="
                                                            margin-top: 10px;
                                                            border-top: 1px solid <?php echo esc_attr( $cookieSettingsPopupAccentColor ); ?>;
                                                        ">
														
														<div class="inner-gdpr-column gdpr-category-toggle <?php echo esc_html( $the_options['template_parts'] ); ?>">
															<div class="inner-gdpr-columns">
																
															<div class="left">
																<span class="gdpr-dropdown-arrow">
																	<svg width="25px" height="25px" viewBox="0 0 24.00 24.00" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
																</span>
																<a href="#" class="btn category-header <?php echo esc_html($classnames)?>" tabindex="0"><?php echo esc_html__( $value->name, 'gdpr-cookie-consent' ); // phpcs:ignore ?></a>
															</div>

															<div class="right">
															<?php
																	
																	if( $display ) {
																	?>
																	<div class="toggle-group">
																		<div class="<?php echo esc_html($classnames)?>-switch-wrapper">
																			<?php
																			$legInt = false;
																			if( $purposeLegIntMap[$key] && $displayLegint) {
																				$legInt = true;
																			?>
																				<div class="purposes-legitimate-switch-wrapper">
																					<div class="purposes-switch-label">Legitimate Interest</div>
																					<div class="toggle">
																						<div class="checkbox">
																							<!-- DYNAMICALLY GENERATE Input ID  -->
																							<input 
																							<?php
																							if ( in_array($value->id, $purpose_legint_data) ) {
																								?>
																								checked="checked"
																								<?php
																							}
																							?>
																							id="gdpr_messagebar_body_button_legint_purpose_<?php echo esc_html($value->id); ?>" 
																							class="purposes-switch-handler <?php echo esc_html("legint-switch", "gdpr-cookie-consent");?> <?php echo esc_html($value->id);?>"  
																							type="checkbox" 
																							name="gdpr_messagebar_body_button_legint_purpose_<?php echo esc_html($value->id); ?>" 
																							value=<?php echo esc_html( $value->id ); ?>>
																							<label for="gdpr_messagebar_body_button_legint_purpose_<?php echo esc_html($value->id); ?>" >
																								<span class="label-text"><?php echo esc_html( $value->id ); ?></span>
																							</label>
																							<!-- DYNAMICALLY GENERATE Input ID  -->
																						</div>
																					</div>
																				</div>
																				<?php }?>
																				<div class="<?php echo esc_html($classnames)?>-consent-switch-wrapper">
																					<div class="<?php echo esc_html($classnames)?>-switch-label">Consent</div>
																					<div class="toggle">
																						<div class="checkbox">
																							<!-- DYNAMICALLY GENERATE Input ID  -->
																							<input 
																							<?php
																							if ( in_array($value->id, $consentArray) ) {
																								?>
																								checked="checked"
																								<?php
																							} 
																							?>
																							id="gdpr_messagebar_body_button_consent_<?php echo esc_html($classnames)?>_<?php echo esc_html($value->id); ?>"
																							class="<?php echo esc_html($classnames)?>-switch-handler <?php echo esc_html("consent-switch", "gdpr-cookie-consent");?> <?php echo esc_html($value->id);?>"
																							type="checkbox" 
																							name="gdpr_messagebar_body_button_consent_<?php echo esc_html($classnames)?>_<?php echo esc_html($value->id); ?>"
																							value=<?php echo esc_html( $value->id ); ?> >
																							<label for="gdpr_messagebar_body_button_consent_<?php echo esc_html($classnames)?>_<?php echo esc_html($value->id); ?>">
																								<span class="label-text"><?php echo esc_html( $value->id ); ?></span>
																							</label>
																							<!-- DYNAMICALLY GENERATE Input ID  -->
																						</div>
																					</div>
																				</div>
																		</div>
																	</div>
																<?php
																	}
																?>
															</div>
															
															</div>
														</div>
														<div class="inner-description-container hide">
															<div class="group-description" tabindex="0">

															<!-- Uncomment this later -->
																<div class="gdpr-ad-purpose-details">
																	<p class="gdpr-ad-purpose-details-desc"><?php echo esc_html( $value->description );?></p>
																	<?php if($value->illustrations) {?>
																	<div class="gdpr-ad-purpose-illustrations">
																		<p class="gdpr-ad-purpose-illustrations-title"><?php echo esc_html__( "Illustrations", 'gdpr-cookie-consent' );  ?></p>
																		<ul class="gdpr-ad-purpose-illustrations-desc">
																			<?php 
																			$illustrations = $value->illustrations;
																			foreach ( $illustrations as $key => $value ) { ?>
																			<li><?php echo esc_html( $value );  ?></li>
																			<?php } ?>
																		</ul>
																	</div>
																	<?php } ?>
																	<p class="gdpr-ad-purpose-vendor-count-wrapper">
																		<?php
																			if ( ! $legInt ) {
																				/* translators: %d: number of vendors */
																				printf(esc_html__( 'Number of vendors seeking consent: %d', 'gdpr-cookie-consent' ), (int) $count[ $key ]);
																			} else {
																				/* translators: %d: number of vendors */
																				printf(esc_html__( 'Number of vendors seeking consent or relying on legitimate interest: %d', 'gdpr-cookie-consent' ), (int) ( $count[ $key ] + $legintcount[ $key ] ));
																			}
																		?>
																	</p>	
																</div>


															</div>
														</div>
														
													</li>
														<?php
													}
													?>
												</ul>
									</div>
									<hr style="
                                        margin-top: 10px;
                                        border-top: 1px solid <?php echo esc_attr( $cookieSettingsPopupAccentColor ); ?>;
                                    ">
								</li>
								<?php
							}
							?>
						</ul>
						<ul class="category-group vendor-group tabContainer">
							<?php
						    $vendors = ["IAB Certified Third Party Vendors"];
							foreach ( $vendors as $vendor ) {
										?>
										
										<li class="category-item">
												
												
												<div class="gdpr-column gdpr-category-toggle <?php echo esc_html( $the_options['template_parts'] ); ?>">
													<div class="gdpr-columns">
														<div class="left">
														<span class="gdpr-dropdown-arrow">
															<svg width="25px" height="25px" viewBox="0 0 24.00 24.00" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
														</span>
														<a href="#" class="btn category-header vendors" tabindex="0"><?php echo esc_html__( $vendor, 'gdpr-cookie-consent' ); // phpcs:ignore ?></a>
														</div>
													
														<div class="right">
															<div class="toggle-group">
																<div class="toggle">
																	<div class="checkbox">
																		<!-- DYNAMICALLY GENERATE Input ID  -->
																		<input 
																		<?php
																		if ( $allVendorsFlag ) {
																			?>
																			checked="checked"
																			<?php
																		} 
																		?>
																		id="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" 
																		class="vendor-all-switch-handler" 
																		type="checkbox" 
																		name="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" 
																		value="<?php echo esc_html( is_array($data->allvendors) ? implode(',', $data->allvendors) : $data->allvendors ); ?>">
																		<label for="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>">
																			<span class="label-text"><?php echo esc_html( $category['gdpr_cookie_category_name'] ); ?></span>
																		</label>
																		<!-- DYNAMICALLY GENERATE Input ID  -->
																	</div>
																</div>
															</div>
														</div>
														
													</div>
												</div>
												<div class="description-container hide">
																<ul class="category-group  vendor-group tabContainer">
																
																<?php
																

																$vendordata  = $data->vendors;
																
																foreach ( $vendordata as $key=>$vendor ) {
																	?>
																	<li class="category-item">
																	<hr style="
                                                                        margin-top: 10px;
                                                                        border-top: 1px solid <?php echo esc_attr( $cookieSettingsPopupAccentColor ); ?>;
                                                                    ">
																			
																			
																	<div class="inner-gdpr-column gdpr-category-toggle <?php echo esc_html( $the_options['template_parts'] ); ?>">
																		<div class="inner-gdpr-columns">
																		<div class="left">
																			<span class="gdpr-dropdown-arrow">
																				<svg width="25px" height="25px" viewBox="0 0 24.00 24.00" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
																			</span>
																			<a href="#" class="btn category-header vendors" tabindex="0"><?php echo esc_html__( $vendor->name, 'gdpr-cookie-consent' ); // phpcs:ignore ?></a>
																		</div>

																		<div class="right">
																		<div class="toggle-group">
																				<div class="vendor-switch-wrapper">
																				<?php
																				if( $vendor->legIntPurposes ) {
																				?>
																					<div class="vendor-legitimate-switch-wrapper">
																						<div class="vendor-switch-label">Legitimate Interest</div>
																						<div class="toggle">
																							<div class="checkbox">
																								<!-- DYNAMICALLY GENERATE Input ID  -->
																								<input 
																								<?php
																								if ( in_array($vendor->id, $legint_data) ) {
																									?>
																									checked="checked"
																									<?php
																								} 
																								?>
																								id="gdpr_messagebar_body_button_legint_vendor_<?php echo esc_html($vendor->id);?>" 
																								class="vendor-switch-handler <?php echo esc_html("legint-switch", "gdpr-cookie-consent");?> <?php echo esc_html($vendor->id);?>"  
																								type="checkbox" 
																								name="gdpr_messagebar_body_button_legint_vendor_<?php echo esc_html($vendor->id);?>" 
																								value=<?php echo esc_html( $vendor->id ); ?>>
																								<label for="gdpr_messagebar_body_button_legint_vendor_<?php echo esc_html($vendor->id);?>">
																									<span class="label-text"><?php echo esc_html($vendor->id);?></span>
																								</label>
																								<!-- DYNAMICALLY GENERATE Input ID  -->
																							</div>
																						</div>
																					</div>
																					<?php }?>
																					<?php
																				if( $vendor->purposes ) {
																				?>
																					<div class="vendor-consent-switch-wrapper">
																						<div class="vendor-switch-label">Consent</div>
																						<div class="toggle">
																							<div class="checkbox">
																								<!-- DYNAMICALLY GENERATE Input ID  -->
																								<input 
																								<?php 

																								if ( in_array($vendor->id, $consent_data) ) {
																									?>
																									checked="checked"
																									<?php
																								}
																								?>
																								id="gdpr_messagebar_body_button_consent_vendor_<?php echo esc_html($vendor->id);?>" 
																								class="vendor-switch-handler <?php echo esc_html("consent-switch", "gdpr-cookie-consent");?> <?php echo esc_html($vendor->id);?>" 
																								type="checkbox" 
																								name="gdpr_messagebar_body_button_consent_vendor_<?php echo esc_html($vendor->id);?>" 
																								value=<?php echo esc_html( $vendor->id ); ?>>
																								<label for="gdpr_messagebar_body_button_consent_vendor_<?php echo esc_html($vendor->id);?>">
																									<span class="label-text"><?php echo esc_html( $vendor->id ); ?></span>
																								</label>
																								<!-- DYNAMICALLY GENERATE Input ID  -->
																							</div>
																						</div>
																					</div>
																				<?php }?>
																				</div>
																		</div>
																		</div>
																		
																		</div>
																	</div>
																	<div class="inner-description-container hide">
																		<div class="group-description" tabindex="0">
																			<div class="gdpr-ad-purpose-details">
																				<div class="gdpr-vendor-wrapper">
																					<p class="gdpr-vendor-privacy-link">
																						<span class="gdpr-vendor-privacy-link-title"><?php echo esc_html("Privacy Policy: ", "gdpr-cookie-consent");?></span>
																						<a href="<?php echo esc_url($vendor->urls[0]->privacy);?>" target="_blank" rel="noopener noreferrer" aria-label="Privacy Policy"><?php echo esc_html($vendor->urls[0]->privacy);?></a>
																					</p>
																					<p class="gdpr-vendor-legitimate-link">
																						<span class="gdpr-vendor-legitimate-link-title"><?php echo esc_html("Legitimate Interest Claim: ", "gdpr-cookie-consent");?></span>
																						<a href="<?php echo isset( $vendor->urls[0]->legIntClaim ) ? esc_url( $vendor->urls[0]->legIntClaim ) : '#'; ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Legitimate Interest Claim', 'gdpr-cookie-consent' ); ?>"><?php echo isset($vendor->urls[0]->legIntClaim)? esc_html( $vendor->urls[0]->legIntClaim ) : esc_html__("Not Available", "gdpr-cookie-consent");?></a>
																					</p>
																					<p class="gdpr-vendor-data-retention-section">
																						<span class="gdpr-vendor-data-retention-value"><?php echo esc_html("Data Retention Period: ", "gdpr-cookie-consent");echo isset($vendor->dataRetention->stdRetention) ? esc_html( $vendor->dataRetention->stdRetention ) : esc_html__("Not Available", "gdpr-cookie-consent");echo esc_html__(" Days", "gdpr-cookie-consent");?></span>
																					</p>
																					<div class="gdpr-vendor-purposes-section">
																						<p class="gdpr-vendor-purposes-title"><?php echo esc_html("Purposes (Consent) ", "gdpr-cookie-consent");?></p>
																						<ul class="gdpr-vendor-purposes-list">
																							<?php foreach ( $vendor->purposes as $key => $value ) {?>
																							<li><?php echo esc_html( $data->purposes[$value-1]->name );  ?></li>
																							<?php } ?>
																						</ul>
																					</div>
																					<?php if( $vendor->legIntPurposes ) { ?>
																						<div class="gdpr-vendor-purposes-legint-section">
																							<p class="gdpr-vendor-purposes-legint-title"><?php echo esc_html("Purposes (Legitimate Interest) ", "gdpr-cookie-consent");?></p>
																							<ul class="gdpr-vendor-purposes-legint-list">
																								<?php foreach ( $vendor->legIntPurposes as $key => $value ) {?>
																								<li><?php echo esc_html( $data->purposes[$value-1]->name );  ?></li>
																								<?php } ?>
																							</ul>
																						</div>

																					<?php } ?>
																					<div class="gdpr-vendor-special-purposes-section">
																					<p class="gdpr-vendor-special-purposes-title"><?php echo esc_html("Special Purposes ", "gdpr-cookie-consent");?></p>
																						<ul class="gdpr-vendor-special-purposes-list">
																							<?php foreach ( $vendor->specialPurposes as $key => $value ) {?>
																							<li><?php echo esc_html( $data->specialPurposes[$value-1]->name );  ?></li>
																							<?php } ?>
																						</ul>
																					</div>
																					<div class="gdpr-vendor-features-section">
																					<p class="gdpr-vendor-features-title"><?php echo esc_html("Features ", "gdpr-cookie-consent");?></p>
																						<ul class="gdpr-vendor-features-list">
																							<?php foreach ( $vendor->features as $key => $value ) {?>
																							<li><?php echo esc_html( $data->features[$value-1]->name );  ?></li>
																							<?php } ?>
																						</ul>
																					</div>
																					<div class="gdpr-vendor-category-section">
																					<p class="gdpr-vendor-category-title"><?php echo esc_html("Data Categories ", "gdpr-cookie-consent");?></p>
																						<ul class="gdpr-vendor-category-list">
																							<?php foreach ( $vendor->dataDeclaration as $key => $value ) {?>
																							<li><?php echo esc_html( $data->dataCategories[$value-1]->name );  ?></li>
																							<?php } ?>
																						</ul>
																					</div>
																					<div class="gdpr-vendor-storage-section">
																					<p class="gdpr-vendor-storage-title"><?php echo esc_html("Device Storage Overview ", "gdpr-cookie-consent");?></p>
																						<ul class="gdpr-vendor-storage-list">
																							<?php
																								$tracking_method = '';

																								if ( $vendor->usesCookies && $vendor->usesNonCookieAccess ) {
																									$tracking_method = __( 'Cookie and others', 'gdpr-cookie-consent' );
																								} elseif ( $vendor->usesCookies ) {
																									$tracking_method = __( 'Cookie', 'gdpr-cookie-consent' );
																								} elseif ( $vendor->usesNonCookieAccess ) {
																									$tracking_method = __( 'Others', 'gdpr-cookie-consent' );
																								}
																							/* translators: %s: tracking method name */
																							echo esc_html( sprintf(__( 'Tracking method: %s', 'gdpr-cookie-consent' ), $tracking_method));
																							?>
																							<li><?php 
																								/* translators: %d: maximum cookie duration in days */
																								echo esc_html(sprintf(__( 'Maximum duration of Cookies: %d days', 'gdpr-cookie-consent' ), intval( $vendor->cookieMaxAgeSeconds / ( 60 * 60 * 24 ) )));?>
																							</li>	
																							<li><?php echo $vendor->cookieRefresh ? esc_html__( "Cookie lifetime is being refreshed", 'gdpr-cookie-consent' ) : esc_html__( "Cookie lifetime is not refreshed", 'gdpr-cookie-consent' );  ?></li>	
																						</ul>
																					</div>
																					<div class="gdpr-vendor-storage-overview-section"></div>
																					<div class="gdpr-vendor-storage-disclosure-section"></div>
																				</div>
																			</div>
																		</div>
																	</div>
																	
																</li>
																	<?php
																}
																?>
															</ul>
												</div>
										<hr style="
                                            margin-top: 10px;
                                            border-top: 1px solid <?php echo esc_attr( $cookieSettingsPopupAccentColor ); ?>;
                                        ">
									</li>
										<?php
									}
							?>
						</ul>
						<?php 
						if($the_options['is_gacm_on']==="true" || $the_options['is_gacm_on'] === true) {?>
							<ul class="category-group vendor-group tabContainer">
							<?php
						    $vendors = ["Google's Ad Tech Providers"];
							foreach ( $vendors as $vendor ) {
										?>
										
										<li class="category-item">
												
												
												<div class="gdpr-column gdpr-category-toggle <?php echo esc_html( $the_options['template_parts'] ); ?>">
													<div class="gdpr-columns">
													<div class="left">
													<span class="gdpr-dropdown-arrow">
														<svg width="25px" height="25px" viewBox="0 0 24.00 24.00" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
													</span>
														<a href="#" class="btn category-header vendors" tabindex="0"><?php echo esc_html__( $vendor, 'gdpr-cookie-consent' ); // phpcs:ignore ?></a>
													</div>
													
													<div class="right">
													<div class="toggle-group">
															<div class="toggle">
																<div class="checkbox">
																	<!-- DYNAMICALLY GENERATE Input ID  -->
																	<input 
																	<?php
																	if ( $allVendorsFlag ) {
																		?>
																		checked="checked"
																		<?php
																	} 
																	?>
																	id="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" 
																	class="gacm-vendor-all-switch-handler" 
																	type="checkbox" 
																	name="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>" 
																	value=<?php echo esc_html( $data->allvendors ); ?>>
																	<label for="gdpr_messagebar_body_button_<?php echo esc_html( $category['gdpr_cookie_category_slug'] ); ?>">
																		<span class="label-text"><?php echo esc_html( $category['gdpr_cookie_category_name'] ); ?></span>
																	</label>
																	<!-- DYNAMICALLY GENERATE Input ID  -->
																</div>
															</div>
														</div>
													</div>

														
													</div>
												</div>
												<div class="description-container hide">
																<ul class="category-group  vendor-group tabContainer">
																
																<?php foreach ( $gacm_data as $vendor ) {
																	if($vendor[0] != null) {
																		?>
																		<li class="category-item">
																		<hr style="
                                                                            margin-top: 10px;
                                                                            border-top: 1px solid <?php echo esc_attr( $cookieSettingsPopupAccentColor ); ?>;
                                                                        ">
																				
																				
																		<div class="inner-gdpr-column gdpr-category-toggle <?php echo esc_html( $the_options['template_parts'] ); ?>">
																			<div class="inner-gdpr-columns">
																			
																			<div class="left">
																			<span class="gdpr-dropdown-arrow">
																				<svg width="25px" height="25px" viewBox="0 0 24.00 24.00" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
																			</span>
																				<a href="#" class="btn category-header vendors" tabindex="0"><?php echo esc_html__( $vendor[1], 'gdpr-cookie-consent' ); // phpcs:ignore ?></a>
																			</div>

																			<div class="right">
																			<div class="toggle-group">
																					<div class="vendor-switch-wrapper">
																						<div class="vendor-consent-switch-wrapper">
																							<div class="vendor-switch-label">Consent</div>
																							<div class="toggle">
																								<div class="checkbox">
																									<!-- DYNAMICALLY GENERATE Input ID  -->
																									<input 
																									<?php 

																									if ( in_array($vendor[0], $gacm_consent_data) ) {
																										?>
																										checked="checked"
																										<?php
																									}	
																									?>
																									id="gdpr_messagebar_body_button_consent_vendor_<?php echo esc_html($vendor[0]);?>" 
																									class="gacm-vendor-switch-handler <?php echo esc_html("consent-switch", "gdpr-cookie-consent");?> <?php echo esc_html($vendor[0]);?>" 
																									type="checkbox" 
																									name="gdpr_messagebar_body_button_consent_vendor_<?php echo esc_html($vendor[0]);?>" 
																									value=<?php echo esc_html( $vendor[0]); ?>>
																									<label for="gdpr_messagebar_body_button_consent_vendor_<?php echo esc_html($vendor[0]);?>">
																										<span class="label-text"><?php echo esc_html( $vendor[0] ); ?></span>
																									</label>
																									<!-- DYNAMICALLY GENERATE Input ID  -->
																								</div>
																							</div>
																						</div>
																					</div>
																			</div>
																			</div>
																			
																			</div>
																		</div>
																		<div class="inner-description-container hide">
																			<div class="group-description" tabindex="0">
																				<div class="gdpr-ad-purpose-details">
																					<div class="gdpr-vendor-wrapper">
																						<p class="gdpr-vendor-privacy-link">
																							<span class="gdpr-vendor-privacy-link-title"><?php echo esc_html("Privacy Policy: ", "gdpr-cookie-consent");?></span>
																							<a href=<?php echo esc_url( $vendor[2] );;?> target="_blank" rel="noopener noreferrer" aria-label="Privacy Policy"><?php echo esc_html($vendor[2]);?></a>
																						</p>
																						
																						<div class="gdpr-vendor-storage-overview-section"></div>
																						<div class="gdpr-vendor-storage-disclosure-section"></div>
																					</div>
																				</div>
																			</div>
																		</div>
																		
																	</li>
																		<?php
										}}?>
															</ul>
												</div>
										<hr style="
                                            margin-top: 10px;
                                            border-top: 1px solid <?php echo esc_attr( $cookieSettingsPopupAccentColor ); ?>;
                                        ">
									</li>
										<?php
									}
							?>
						</ul>

						<?php } ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="gdprmodal-footer" style="--popup_accent_color: <?php echo esc_html( '#' . ltrim($cookieSettingsPopupAccentColor, '#') ); ?>;">
				<?php
				if ( ! empty( $cookie_data['show_credits'] ) ) {
					?>
				<div class="powered-by-credits" style="margin-left: <?php echo esc_attr( $top_value ); ?>px;"><?php echo wp_kses_post( $cookie_data['credits'] ); ?></div>
					<?php
				}
				?>
				<button id="cookie_action_save" type="button" class="gdpr_action_button btn" data-gdpr_action="accept" data-dismiss="gdprmodal"
					style="
						background-color: <?php 
							echo esc_html(
							( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
							 	? ( $the_options[ 'button_accept_all_button_color' . $chosenBanner ] )
							 	: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_accept_all_button_color1'] : $the_options['button_accept_all_button_color'] )
							); ?>;
						color: <?php 
							echo esc_html(
								( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
						 		? ( $the_options[ 'button_accept_all_link_color' . $chosenBanner ] )
						 		: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_accept_all_link_color1'] : $the_options['button_accept_all_link_color'] )
							); ?>;
						border-style: <?php 
							echo esc_html(( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
						 		? ( $the_options[ 'button_accept_all_btn_border_style' . $chosenBanner ] )
						 		: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_accept_all_btn_border_style1'] : $the_options['button_accept_all_btn_border_style'] )
						 	); ?>;
						border-width: <?php 
							echo esc_html(( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
						 		? ( $the_options[ 'button_accept_all_btn_border_width' . $chosenBanner ] . 'px' )	
						 		: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_accept_all_btn_border_width1'] : $the_options['button_accept_all_btn_border_width'] )
						 	); ?>px;
						border-color: <?php 
							echo esc_html(( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
						 		? ( $the_options[ 'button_accept_all_btn_border_color' . $chosenBanner ] )
						 		: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_accept_all_btn_border_color1'] : $the_options['button_accept_all_btn_border_color'] )
						 	); ?>;
						border-radius: <?php 
							echo esc_html(( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
						 		? ( $the_options[ 'button_accept_all_btn_border_radius' . $chosenBanner ] . 'px' )
						 		: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_accept_all_btn_border_radius1'] : $the_options['button_accept_all_btn_border_radius'] )
						 	); ?>px;
						padding: 12px 29px;
						margin-right: <?php echo esc_attr( $top_value ); ?>px;
					"><?php echo esc_html( $cookie_data['save_button'] ); ?></button>
			</div>
		</div>
	</div>
</div>

<?php } 

if( $the_options['cookie_usage_for'] === "ccpa" || $the_options['cookie_usage_for'] === "both" ) { ?>

<div class="gdprmodal gdprfade" id="gdpr-ccpa-gdprmodal" role="dialog" data-keyboard="false" data-backdrop="<?php echo esc_html( $cookie_data['backdrop'] ); ?>">
	<div class="gdprmodal-dialog gdprmodal-dialog-centered">
		<div class="gdprmodal-content"
		style="
            background-color: <?php 
				echo esc_html($the_options['cookie_usage_for'] === "both" 
					? strtoupper($the_options['multiple_legislation_cookie_bar_color2'] . strtoupper( str_pad(dechex((int) floor($the_options['multiple_legislation_cookie_bar_opacity2'] * 255)), 2, '0', STR_PAD_LEFT) ))
					: $finalColor
				)?>;
            color: <?php 
				echo esc_html(( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
			 		? ( $the_options[ 'cookie_text_color' . $chosenBanner ] )
			 		: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['multiple_legislation_cookie_text_color2'] : $the_options['text'] )
				); ?>;
            border-style: <?php 
				echo esc_html(( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
			 		? ( $the_options[ 'border_style' . $chosenBanner ] )
			 		: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['multiple_legislation_border_style2'] : $the_options['background_border_style'] )
			 	); ?>;
            border-width: <?php 
				echo esc_html(( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
			 		? ( $the_options[ 'cookie_bar_border_width' . $chosenBanner ] . 'px' )
			 		: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['multiple_legislation_cookie_bar_border_width2'] : $the_options['background_border_width'] . 'px' )
				); ?>;
            border-radius: <?php 
				echo esc_html(( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
			 		? ( $the_options[ 'cookie_bar_border_radius' . $chosenBanner ] . 'px' )
			 		: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['multiple_legislation_cookie_bar_border_radius2'] : $the_options['background_border_radius'] . 'px')
			 	); ?>;
            border-color: <?php 
				echo esc_html(( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
			 		? ( $the_options[ 'cookie_border_color' . $chosenBanner ] )
			 		: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['multiple_legislation_cookie_border_color2'] : $the_options['background_border_color'] )
				); ?>;
        ">
			<div class="gdprmodal-header">
			<button type="button" class="gdpr_action_button close" data-dismiss="gdprmodal" data-gdpr_action="close" 
                style="
                    border: none;
                    height: 20px;
                    width: 20px;
                    position: absolute;
                    top: <?php echo esc_html($top_value); ?>px;
                    right: <?php echo esc_html($top_value); ?>px;
                    border-radius: 50%;
                    color: <?php echo esc_html( $cookieSettingsPopupAccentColor ); ?>;
					background-color: transparent;
                ">
					<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 5.29289C5.68342 4.90237 6.31658 4.90237 6.70711 5.29289L12 10.5858L17.2929 5.29289C17.6834 4.90237 18.3166 4.90237 18.7071 5.29289C19.0976 5.68342 19.0976 6.31658 18.7071 6.70711L13.4142 12L18.7071 17.2929C19.0976 17.6834 19.0976 18.3166 18.7071 18.7071C18.3166 19.0976 17.6834 19.0976 17.2929 18.7071L12 13.4142L6.70711 18.7071C6.31658 19.0976 5.68342 19.0976 5.29289 18.7071C4.90237 18.3166 4.90237 17.6834 5.29289 17.2929L10.5858 12L5.29289 6.70711C4.90237 6.31658 4.90237 5.68342 5.29289 5.29289Z" fill="currentColor"/>
					</svg>
				</button>
			</div>

			<div class="gdprmodal-body"><p style="line-height: 25px;"><?php echo esc_html__( $cookie_data['dash_optout_text'], 'gdpr-cookie-consent' ); //phpcs:ignore?>
					</p>
			</div>
			<div class="gdprmodal-footer" style="--popup_accent_color: <?php echo esc_html( '#' . ltrim($cookieSettingsPopupAccentColor, '#') ); ?>;">
				<div class="gdprmodal-footer-buttons">
					<button id="cookie_action_cancel" type="button" class="<?php echo esc_html( $the_options['button_cancel_classes'] ); ?>" data-gdpr_action="cancel" data-dismiss="gdprmodal"
					style="
						background-color: <?php
							echo esc_html(( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
							? ( $the_options[ 'button_cancel_button_color' . $chosenBanner ] )
							: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_cancel_button_color1'] : $the_options['button_cancel_button_color'] )
						); ?>;
						color: <?php 
							echo esc_html( ( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
							? ( $the_options[ 'button_cancel_link_color' . $chosenBanner ] )
							: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_cancel_link_color1'] : $the_options['button_cancel_link_color'] ) 
						); ?>;
						border-style: <?php
							echo esc_html(  ( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
							? ( $the_options[ 'button_cancel_button_border_style' . $chosenBanner ] )
							: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_cancel_button_border_style1'] : $the_options['button_cancel_button_border_style'] )
						); ?>;
						border-width: <?php 
							echo esc_html( ( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
							? ( $the_options[ 'button_cancel_button_border_width' . $chosenBanner ] )
							: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_cancel_button_border_width1'] : $the_options['button_cancel_button_border_width'] )
						); ?>px;
						border-color: <?php 
							echo esc_html( ( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
							? ( $the_options[ 'button_cancel_button_border_color' . $chosenBanner ] )
							: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_cancel_button_border_color1'] : $the_options['button_cancel_button_border_color'] )
						); ?>;
						border-radius: <?php 
							echo esc_html( ( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
							? ( $the_options[ 'button_cancel_button_border_radius' . $chosenBanner ] )
							: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_cancel_button_border_radius1'] : $the_options['button_cancel_button_border_radius'] ) 
						); ?>px;
						padding: 12px 29px;
						width: 100%;
					"><?php echo esc_html__( $the_options['button_cancel_text1'], 'gdpr-cookie-consent' );//phpcs:ignore ?></button>
					<button id="cookie_action_confirm" type="button" class="<?php echo esc_html( $the_options['button_confirm_classes'] ); ?>" data-gdpr_action="confirm" data-dismiss="gdprmodal"
					style="
						background-color: <?php
							echo esc_html(( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
							? ( $the_options[ 'button_confirm_button_color' . $chosenBanner ] )
							: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_confirm_button_color1'] : $the_options['button_confirm_button_color'] )
						); ?>;
						color: <?php 
							echo esc_html( ( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
							? ( $the_options[ 'button_confirm_link_color' . $chosenBanner ] )
							: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_confirm_link_color1'] : $the_options['button_confirm_link_color'] ) 
						); ?>;
						border-style: <?php
							echo esc_html(  ( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
							? ( $the_options[ 'button_confirm_button_border_style' . $chosenBanner ] )
							: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_confirm_button_border_style1'] : $the_options['button_confirm_button_border_style'] )
						); ?>;
						border-width: <?php 
							echo esc_html( ( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
							? ( $the_options[ 'button_confirm_button_border_width' . $chosenBanner ] )
							: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_confirm_button_border_width1'] : $the_options['button_confirm_button_border_width'] )
						); ?>px;
						border-color: <?php 
							echo esc_html( ( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
							? ( $the_options[ 'button_confirm_button_border_color' . $chosenBanner ] )
							: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_confirm_button_border_color1'] : $the_options['button_confirm_button_border_color'] )
						); ?>;
						border-radius: <?php 
							echo esc_html( ( $ab_options['ab_testing_enabled'] === true || $ab_options['ab_testing_enabled'] === 'true')
							? ( $the_options[ 'button_confirm_button_border_radius' . $chosenBanner ] )
							: ( $the_options['cookie_usage_for'] === 'both' ? $the_options['button_confirm_button_border_radius1'] : $the_options['button_confirm_button_border_radius'] ) 
						); ?>px;
						padding: 12px 29px;
						width: 100%;
					"><?php echo esc_html__( $the_options['button_confirm_text1'], 'gdpr-cookie-consent' );//phpcs:ignore ?></button>
				</div>
				

				<?php
				if ( ! empty( $cookie_data['show_credits'] ) ) {
					if ( ! empty( $cookie_data['credits'] ) ) {
						?>
						<div class="powered-by-credits"><?php echo wp_kses_post( $cookie_data['credits'] ); ?></div>
						<?php
					}
				}
				?>
			</div>
		</div>
	</div>
</div>

<?php } ?>