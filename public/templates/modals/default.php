<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://club.wpeka.com
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 */

$data = Gdpr_Cookie_Consent::gdpr_get_vendors();
	$iabtcf_consent_data = Gdpr_Cookie_Consent::gdpr_get_iabtcf_vendor_consent_data();
	
	$consent_data = isset( $iabtcf_consent_data["consent"] ) ? $iabtcf_consent_data["consent"] : [];
	$legint_data = isset( $iabtcf_consent_data["legint"] ) ? $iabtcf_consent_data["legint"] : [];
	$purpose_consent_data = isset( $iabtcf_consent_data["purpose_consent"] ) ? $iabtcf_consent_data["purpose_consent"] : [];
	$purpose_legint_data = isset( $iabtcf_consent_data["purpose_legint"] ) ? $iabtcf_consent_data["purpose_legint"] : [];
	$feature_consent_data = isset( $iabtcf_consent_data["feature_consent"] ) ? $iabtcf_consent_data["feature_consent"] : [];
	$allVendors = isset( $iabtcf_consent_data["allvendorIds"] ) ? $iabtcf_consent_data["allvendorIds"] : [];
	$allSpecialFeatures = isset( $iabtcf_consent_data["allSpecialFeatureIds"] ) ? $iabtcf_consent_data["allSpecialFeatureIds"] : [];
	$allVendorsFlag = false;
	foreach ( $data->vendors as $vendor ) {
		if ( in_array($vendor->id, $consent_data) ) {
			if( $vendor->legIntPurposes ) {
				if ( ! in_array($vendor->id, $legint_data) ) {
					$allVendorsFlag = false;
					break;
				}
			}
			$allVendorsFlag = true;
		}
		else {
			$allVendorsFlag = false;
			break;
		}
	}
	$allFeaturesFlag = false;
?>
<div class="gdprmodal gdprfade" id="gdpr-gdprmodal" role="dialog" data-keyboard="false" data-backdrop="<?php echo esc_html( $cookie_data['backdrop'] ); ?>">
	<div class="gdprmodal-dialog gdprmodal-dialog-centered">
		<!-- Modal content-->
		<div class="gdprmodal-content <?php if( $the_options['is_iabtcf_on']) echo "iabtcf";?>">
			<div class="gdprmodal-header">
				<button type="button" class="gdpr_action_button close" data-dismiss="gdprmodal" data-gdpr_action="close">
					<span class="dashicons dashicons-no">&#10005</span>
				</button>
			</div>
			<div class="gdprmodal-body nvg">
				<div class="gdpr-details-content">
					<div class="gdpr-groups-container">
						<?php
							if ( $the_options['cookie_usage_for']==='gdpr' ) :?>
								<div class="gdpr-about-cookies"><?php echo $the_options['is_iabtcf_on'] ? esc_html__( $cookie_data['dash_about_message_iabtcf'], 'gdpr-cookie-consent' ) : esc_html__( $cookie_data['dash_about_message'], 'gdpr-cookie-consent' ); // phpcs:ignore ?></div>
							<?php elseif (  $the_options['cookie_usage_for']==='lgpd') :?>
								<div class="gdpr-about-cookies"><?php echo esc_html__( $cookie_data['dash_about_message_lgpd'], 'gdpr-cookie-consent' ); // phpcs:ignore ?></div>
							<?php elseif ( $the_options['cookie_usage_for']==='both' ) :?>
								<div class="gdpr-about-cookies"><?php echo $the_options['is_iabtcf_on'] ? esc_html__( $cookie_data['dash_about_message_iabtcf'], 'gdpr-cookie-consent' ) : esc_html__( $cookie_data['dash_about_message'], 'gdpr-cookie-consent' ); // phpcs:ignore ?></div>
							<?php endif; 
								?>
							<ul class="gdpr-iab-navbar">
								<?php if ( $the_options['is_iabtcf_on']) { ?>
									<li class="gdpr-iab-navbar-item" id="gdprIABTabCategory"><button class="gdpr-iab-navbar-button active"><?php echo esc_html__('Cookie Categories','gdpr-cookie-consent')?><span class="dashicons dashicons-arrow-right-alt2"></span></button></li>
									<li class="gdpr-iab-navbar-item" id="gdprIABTabFeatures"><button class="gdpr-iab-navbar-button"><?php echo esc_html__('Purposes and Features','gdpr-cookie-consent')?><span class="dashicons dashicons-arrow-right-alt2"></span></button></li>
									<li class="gdpr-iab-navbar-item" id="gdprIABTabVendors"><button class="gdpr-iab-navbar-button"><?php echo esc_html__('Vendors','gdpr-cookie-consent')?><span class="dashicons dashicons-arrow-right-alt2"></span></button></li>
								<?php } ?>
							</ul>
							<div class="">
								<ul class="cat category-group outer tabContainer">
									<?php
									foreach ( $cookie_data['categories'] as $category ) {
										?>
										<li class="category-item">
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
										<div class="gdpr-column gdpr-category-toggle <?php echo esc_html( $the_options['template_parts'] ); ?>">
											<div class="gdpr-columns">
												<span class="dashicons dashicons-arrow-down-alt2"></span>
												<a href="#" class="btn category-header" tabindex="0"><?php echo esc_html__( $category['gdpr_cookie_category_name'], 'gdpr-cookie-consent' ); // phpcs:ignore ?></a>
											</div>
										</div>
										<div class="description-container hide">
											<div class="group-description" tabindex="0"><?php echo esc_html__( $category['gdpr_cookie_category_description'], 'gdpr-cookie-consent' ); // phpcs:ignore ?></div>
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
											?>
										</div>
										<hr>
									</li>
										<?php
									}
									?>
								</ul>
								<?php if ( $the_options['is_iabtcf_on']) {?>
								<ul class="category-group outer feature-group tabContainer">
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
												
										<div class="gdpr-column gdpr-category-toggle <?php echo esc_html( $the_options['template_parts'] ); ?>">
											<div class="gdpr-columns">
												<span class="dashicons dashicons-arrow-down-alt2"></span>
												<a href="#" class="btn category-header" tabindex="0"><?php echo esc_html__( $value, 'gdpr-cookie-consent' ); // phpcs:ignore ?></a>
											</div>
										</div>
										<div class="description-container hide">
														<ul class="category-group  feature-group tabContainer">
														<?php 
														
														
														foreach ( $values as $key => $value ) {
															?>
															<li class="category-item">
															<hr>
															<?php
																		
																		if( $display ) {
																		?>
																	<div class="toggle-group bottom-toggle">
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
															<div class="inner-gdpr-column gdpr-category-toggle container-toggle <?php echo esc_html( $the_options['template_parts'] ); ?>">
																<div class="inner-gdpr-columns">
																	<span class="dashicons dashicons-arrow-down-alt2"></span>
																	<a href="#" class="btn category-header <?php echo esc_html($classnames)?>" tabindex="0"><?php echo esc_html__( $value->name, 'gdpr-cookie-consent' ); // phpcs:ignore ?></a>
																</div>
															</div>
															<div class="inner-description-container hide">
																<div class="group-description" tabindex="0">

																<!-- Uncomment this later -->
																	<div class="gdpr-ad-purpose-details">
																		<p class="gdpr-ad-purpose-details-desc"><?php echo esc_html__( $value->description, 'gdpr-cookie-consent' );?></p>
																		<?php if($value->illustrations) {?>
																		<div class="gdpr-ad-purpose-illustrations">
																			<p class="gdpr-ad-purpose-illustrations-title"><?php echo esc_html__( "Illustrations", 'gdpr-cookie-consent' );  ?></p>
																			<ul class="gdpr-ad-purpose-illustrations-desc">
																				<?php 
																				$illustrations = $value->illustrations;
																				foreach ( $illustrations as $key => $value ) { ?>
																				<li><?php echo esc_html__( $value, 'gdpr-cookie-consent' );  ?></li>
																				<?php } ?>
																			</ul>
																		</div>
																		<?php } ?>
																		<p class="gdpr-ad-purpose-vendor-count-wrapper">
																			<?php
																				if(!$legInt) echo "Number of vendors seeking consent: ".$count[$key];
																				else echo "Number of Vendors seeking consent or relying on legitimate interest: ".$count[$key]+$legintcount[$key];
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
										<hr>
									</li>
										<?php
									}
									?>
								</ul>
								<ul class="category-group outer vendor-group tabContainer">
									<?php
									$vendors = ["Third Party Vendors"];
									foreach ( $vendors as $vendor ) {
										?>
										
										<li class="category-item">
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
												
												<div class="gdpr-column gdpr-category-toggle <?php echo esc_html( $the_options['template_parts'] ); ?>">
													<div class="gdpr-columns">
														<span class="dashicons dashicons-arrow-down-alt2"></span>
														<a href="#" class="btn category-header vendors" tabindex="0"><?php echo esc_html__( $vendor, 'gdpr-cookie-consent' ); // phpcs:ignore ?></a>
													</div>
												</div>
												<div class="description-container hide">
																<ul class="category-group  vendor-group tabContainer">
																
																<?php
																

																$vendordata  = $data->vendors;
																
																foreach ( $vendordata as $key=>$vendor ) {
																	?>
																	<li class="category-item">
																	<hr>
																			<div class="toggle-group bottom-toggle">
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
																			
																	<div class="inner-gdpr-column gdpr-category-toggle <?php echo esc_html( $the_options['template_parts'] ); ?>">
																		<div class="inner-gdpr-columns">
																			<span class="dashicons dashicons-arrow-down-alt2"></span>
																			<a href="#" class="btn category-header vendors" tabindex="0"><?php echo esc_html__( $vendor->name, 'gdpr-cookie-consent' ); // phpcs:ignore ?></a>
																		</div>
																	</div>
																	<div class="inner-description-container hide">
																		<div class="group-description" tabindex="0">
																			<div class="gdpr-ad-purpose-details">
																				<div class="gdpr-vendor-wrapper">
																					<p class="gdpr-vendor-privacy-link">
																						<span class="gdpr-vendor-privacy-link-title"><?php echo esc_html("Privacy Policy: ", "gdpr-cookie-consent");?></span>
																						<a href=<?php echo $vendor->urls[0]->privacy;?> target="_blank" rel="noopener noreferrer" aria-label="Privacy Policy"><?php echo $vendor->urls[0]->privacy;?></a>
																					</p>
																					<p class="gdpr-vendor-legitimate-link">
																						<span class="gdpr-vendor-legitimate-link-title"><?php echo esc_html("Legitimate Interest Claim: ", "gdpr-cookie-consent");?></span>
																						<a href=<?php echo isset($vendor->urls[0]->legIntClaim)? $vendor->urls[0]->legIntClaim : esc_html("#", "gdpr-cookie-consent");?> target="_blank" rel="noopener noreferrer" aria-label="Legitimate Interest Claim"><?php echo isset($vendor->urls[0]->legIntClaim)? $vendor->urls[0]->legIntClaim : esc_html("Not Available", "gdpr-cookie-consent");?></a>
																					</p>
																					<p class="gdpr-vendor-data-retention-section">
																						<span class="gdpr-vendor-data-retention-value"><?php echo esc_html("Data Retention Period: ", "gdpr-cookie-consent");echo isset($vendor->dataRetention->stdRetention) ? $vendor->dataRetention->stdRetention : esc_html("Not Available", "gdpr-cookie-consent");echo esc_html(" Days", "gdpr-cookie-consent");?></span>
																					</p>
																					<div class="gdpr-vendor-purposes-section">
																						<p class="gdpr-vendor-purposes-title"><?php echo esc_html("Purposes (Consent) ", "gdpr-cookie-consent");?></p>
																						<ul class="gdpr-vendor-purposes-list">
																							<?php foreach ( $vendor->purposes as $key => $value ) {?>
																							<li><?php echo esc_html__( $data->purposes[$value-1]->name, 'gdpr-cookie-consent' );  ?></li>
																							<?php } ?>
																						</ul>
																					</div>
																					<?php if( $vendor->legIntPurposes ) { ?>
																						<div class="gdpr-vendor-purposes-legint-section">
																							<p class="gdpr-vendor-purposes-legint-title"><?php echo esc_html("Purposes (Legitimate Interest) ", "gdpr-cookie-consent");?></p>
																							<ul class="gdpr-vendor-purposes-legint-list">
																								<?php foreach ( $vendor->legIntPurposes as $key => $value ) {?>
																								<li><?php echo esc_html__( $data->purposes[$value-1]->name, 'gdpr-cookie-consent' );  ?></li>
																								<?php } ?>
																							</ul>
																						</div>

																					<?php } ?>
																					<div class="gdpr-vendor-special-purposes-section">
																					<p class="gdpr-vendor-special-purposes-title"><?php echo esc_html("Special Purposes ", "gdpr-cookie-consent");?></p>
																						<ul class="gdpr-vendor-special-purposes-list">
																							<?php foreach ( $vendor->specialPurposes as $key => $value ) {?>
																							<li><?php echo esc_html__( $data->specialPurposes[$value-1]->name, 'gdpr-cookie-consent' );  ?></li>
																							<?php } ?>
																						</ul>
																					</div>
																					<div class="gdpr-vendor-features-section">
																					<p class="gdpr-vendor-features-title"><?php echo esc_html("Features ", "gdpr-cookie-consent");?></p>
																						<ul class="gdpr-vendor-features-list">
																							<?php foreach ( $vendor->features as $key => $value ) {?>
																							<li><?php echo esc_html__( $data->features[$value-1]->name, 'gdpr-cookie-consent' );  ?></li>
																							<?php } ?>
																						</ul>
																					</div>
																					<div class="gdpr-vendor-category-section">
																					<p class="gdpr-vendor-category-title"><?php echo esc_html("Data Categories ", "gdpr-cookie-consent");?></p>
																						<ul class="gdpr-vendor-category-list">
																							<?php foreach ( $vendor->dataDeclaration as $key => $value ) {?>
																							<li><?php echo esc_html__( $data->dataCategories[$value-1]->name, 'gdpr-cookie-consent' );  ?></li>
																							<?php } ?>
																						</ul>
																					</div>
																					<div class="gdpr-vendor-storage-section">
																					<p class="gdpr-vendor-storage-title"><?php echo esc_html("Device Storage Overview ", "gdpr-cookie-consent");?></p>
																						<ul class="gdpr-vendor-storage-list">
																							<li><?php echo esc_html__( "Tracking method: ".($vendor->usesCookies && $vendor->usesNonCookieAccess ? "Cookie and others" : ($vendor->usesCookies ? "Cookie" : ($vendor->usesNonCookieAccess ? "Others" : ""))), 'gdpr-cookie-consent' );  ?></li>	
																							<li><?php echo esc_html__( "Maximum duration of Cookies: ".$vendor->cookieMaxAgeSeconds/(60 * 60 * 24). " days", 'gdpr-cookie-consent' );  ?></li>	
																							<li><?php echo esc_html__( $vendor->cookieRefresh ? "Cookie lifetime is being refreshed" : "Cookie lifetime is not refreshed", 'gdpr-cookie-consent' );  ?></li>	
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
										<hr>
									</li>
										<?php
									}
									?>
								</ul>
								<?php } ?>
							</div>
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
				<button id="cookie_action_save" type="button" class="gdpr_action_button btn" data-gdpr_action="accept" data-dismiss="gdprmodal"><?php 
				echo esc_html( $cookie_data['save_button'] ); ?></button>
			</div>
		</div>
	</div>
</div>