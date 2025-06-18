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
<div class="gdpr_messagebar_detail layout-default navy_blue_square theme-twentytwentyfour hide-popup">
			<div class="gdprmodal gdprfade gdprshow" id="gdpr-gdprmodal" role="dialog" data-keyboard="false" data-backdrop="false" aria-gdprmodal="true" style="padding-right: 15px; display: block;">
	<div class="gdprmodal-dialog gdprmodal-dialog-centered">
		<!-- Modal content-->
		<div class="gdprmodal-content">
			<div class="gdprmodal-header">
				<button type="button" class="gdpr_action_button close" data-dismiss="gdprmodal" data-gdpr_action="close" aria-label="Close modal">
					<span class="dashicons dashicons-no"></span>
				</button>
			</div>
			<div class="gdprmodal-body">
				<div class="gdpr-details-content">
				<div class="gdpr-groups-container">
                     <div class="gdpr-about-cookies">Cookies are small text files that can be used by websites to make a user's experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.</div>
					      <div class="gdpr-about-cookies iabtcf">Customize your consent preferences for Cookie Categories and advertising tracking preferences for Purposes & Features and Vendors below. You can give granular consent for each Third Party Vendor. Most vendors require consent for personal data processing, while some rely on legitimate interest. However, you have the right to object to their use of legitimate interest. The choices you make regarding the purposes and entities listed in this notice are saved in a cookie named wpl_tc_string for a maximum duration of 12 months.</div>
                     <ul class="gdpr-iab-navbar">
                        <li class="gdpr-iab-navbar-item" id="gdprIABTabCategory"><button class="gdpr-iab-navbar-button active">Cookie Categories</button></li>
                        <li class="gdpr-iab-navbar-item" id="gdprIABTabFeatures"><button class="gdpr-iab-navbar-button">Purposes and Features</button></li>
                        <li class="gdpr-iab-navbar-item" id="gdprIABTabVendors"><button class="gdpr-iab-navbar-button">Vendors</button></li>
                     </ul>
                     <ul class="cat category-group tabContainer">
                        <li class="category-item">
                           <div class="toggle-group">
                              <div class="always-active">Always Active</div>
                              <input id="gdpr_messagebar_body_button_necessary" type="hidden" name="gdpr_messagebar_body_button_necessary" value="necessary">
                           </div>
                           <div class="gdpr-column gdpr-category-toggle default">
                              <div class="gdpr-columns">
                                 <span class="dashicons dashicons-arrow-down-alt2"></span>
                                 <a href="#" class="btn category-header" tabindex="0">Necessary</a>
                              </div>
                           </div>
                           <div class="description-container hide">
                              <div class="group-description" tabindex="0">Necessary cookies help make a website usable by enabling basic functions like page navigation and access to secure areas of the website. The website cannot function properly without these cookies.</div>
                              <!-- sub groups -->
                              <div class="category-cookies-list-container">
                              </div>
                           </div>
                           <hr>
                        </li>
                        <li class="category-item">
                           <div class="toggle-group">
                              <div class="toggle">
                                 <div class="checkbox">
                                    <!-- DYNAMICALLY GENERATE Input ID  -->
                                    <input id="gdpr_messagebar_body_button_marketing_navy_blue_square" class="category-switch-handler" type="checkbox" name="gdpr_messagebar_body_button_marketing_navy_blue_square" value="marketing">
                                    <label for="gdpr_messagebar_body_button_marketing_navy_blue_square">
                                    <span class="label-text">Marketing</span>
                                    </label>
                                    <!-- DYNAMICALLY GENERATE Input ID  -->
                                 </div>
                              </div>
                           </div>
                           <div class="gdpr-column gdpr-category-toggle default">
                              <div class="gdpr-columns">
                                 <span class="dashicons dashicons-arrow-down-alt2"></span>
                                 <a href="#" class="btn category-header" tabindex="0">Marketing</a>
                              </div>
                           </div>
                           <div class="description-container hide">
                              <div class="group-description" tabindex="0">Marketing cookies are used to track visitors across websites. The intention is to display ads that are relevant and engaging for the individual user and thereby more valuable for publishers and third party advertisers.</div>
                              <!-- sub groups -->
                              <div class="category-cookies-list-container">
                              </div>
                           </div>
                           <hr>
                        </li>
                        <li class="category-item">
                           <div class="toggle-group">
                              <div class="toggle">
                                 <div class="checkbox">
                                    <!-- DYNAMICALLY GENERATE Input ID  -->
                                    <input id="gdpr_messagebar_body_button_analytics_navy_blue_square" class="category-switch-handler" type="checkbox" name="gdpr_messagebar_body_button_analytics_navy_blue_square" value="analytics">
                                    <label for="gdpr_messagebar_body_button_analytics_navy_blue_square">
                                    <span class="label-text">Analytics</span>
                                    </label>
                                    <!-- DYNAMICALLY GENERATE Input ID  -->
                                 </div>
                              </div>
                           </div>
                           <div class="gdpr-column gdpr-category-toggle default">
                              <div class="gdpr-columns">
                                 <span class="dashicons dashicons-arrow-down-alt2"></span>
                                 <a href="#" class="btn category-header" tabindex="0">Analytics</a>
                              </div>
                           </div>
                           <div class="description-container hide">
                              <div class="group-description" tabindex="0">Analytics cookies help website owners to understand how visitors interact with websites by collecting and reporting information anonymously.</div>
                              <!-- sub groups -->
                              <div class="category-cookies-list-container">
                              </div>
                           </div>
                           <hr>
                        </li>
                        <li class="category-item">
                           <div class="toggle-group">
                              <div class="toggle">
                                 <div class="checkbox">
                                    <!-- DYNAMICALLY GENERATE Input ID  -->
                                    <input id="gdpr_messagebar_body_button_preferences_navy_blue_square" class="category-switch-handler" type="checkbox" name="gdpr_messagebar_body_button_preferences_navy_blue_square" value="preferences">
                                    <label for="gdpr_messagebar_body_button_preferences_navy_blue_square">
                                    <span class="label-text">Preference</span>
                                    </label>
                                    <!-- DYNAMICALLY GENERATE Input ID  -->
                                 </div>
                              </div>
                           </div>
                           <div class="gdpr-column gdpr-category-toggle default">
                              <div class="gdpr-columns">
                                 <span class="dashicons dashicons-arrow-down-alt2"></span>
                                 <a href="#" class="btn category-header" tabindex="0">Preference</a>
                              </div>
                           </div>
                           <div class="description-container hide">
                              <div class="group-description" tabindex="0">Preference cookies enable a website to remember information that changes the way the website behaves or looks, like your preferred language or the region that you are in.</div>
                              <!-- sub groups -->
                              <div class="category-cookies-list-container">
                              </div>
                           </div>
                           <hr>
                        </li>
                        <li class="category-item">
                           <div class="toggle-group">
                              <div class="toggle">
                                 <div class="checkbox">
                                    <!-- DYNAMICALLY GENERATE Input ID  -->
                                    <input id="gdpr_messagebar_body_button_unclassified_navy_blue_square" class="category-switch-handler" type="checkbox" name="gdpr_messagebar_body_button_unclassified_navy_blue_square" value="unclassified">
                                    <label for="gdpr_messagebar_body_button_unclassified_navy_blue_square">
                                    <span class="label-text">Unclassified</span>
                                    </label>
                                    <!-- DYNAMICALLY GENERATE Input ID  -->
                                 </div>
                              </div>
                           </div>
                           <div class="gdpr-column gdpr-category-toggle default">
                              <div class="gdpr-columns">
                                 <span class="dashicons dashicons-arrow-down-alt2"></span>
                                 <a href="#" class="btn category-header" tabindex="0">Unclassified</a>
                              </div>
                           </div>
                           <div class="description-container hide">
                              <div class="group-description" tabindex="0">Unclassified cookies are cookies that we are in the process of classifying, together with the providers of individual cookies.</div>
                              <!-- sub groups -->
                              <div class="category-cookies-list-container">
                              </div>
                           </div>
                           <hr>
                        </li>
                     </ul>
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
                                       id="gdpr_messagebar_body_button_navy_blue_square" class="<?php echo esc_html($classnames);?>-all-switch-handler" type="checkbox" name="gdpr_messagebar_body_button_navy_blue_square" >
                                    <label for="gdpr_messagebar_body_button_navy_blue_square">
                                    <span class="label-text"></span>
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
                              <ul class="category-group feature-group tabContainer">
                                 <?php 
                                    foreach ( $values as $key => $value ) {
                                    	?>
                                 <li class="category-item">
                                    <hr>
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
                                                      id="gdpr_messagebar_body_button_navy_blue_square_consent_<?php echo esc_html($classnames)?>_<?php echo esc_html($value->id); ?>"
                                                      class="<?php echo esc_html($classnames)?>-switch-handler <?php echo esc_html("consent-switch", "gdpr-cookie-consent");?> <?php echo esc_html($value->id);?>"
                                                      type="checkbox" 
                                                      name="gdpr_messagebar_body_button_navy_blue_square_consent_<?php echo esc_html($classnames)?>_<?php echo esc_html($value->id); ?>"
                                                      value=<?php echo esc_html( $value->id ); ?> >
                                                   <label for="gdpr_messagebar_body_button_navy_blue_square_consent_<?php echo esc_html($classnames)?>_<?php echo esc_html($value->id); ?>">
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
                                    <div class="inner-gdpr-column gdpr-category-toggle <?php echo esc_html( $the_options['template_parts'] ); ?>">
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
                     <ul class="category-group vendor-group tabContainer">
                        <?php
                           $vendors = ["IAB Certified Third Party Vendors"];
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
                                       id="gdpr_messagebar_body_button_navy_blue_square" 
                                       class="vendor-all-switch-handler" 
                                       type="checkbox" 
                                       name="gdpr_messagebar_body_button_navy_blue_square" 
                                       value="<?php echo esc_html( is_array($data->allvendors) ? implode(',', $data->allvendors) : $data->allvendors ); ?>">
                                    <label for="gdpr_messagebar_body_button_navy_blue_square">
                                    <span class="label-text"></span>
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
                              <ul class="category-group vendor-group tabContainer">
                                 <?php
                                    $vendordata  = $data->vendors;
                                    
                                    foreach ( $vendordata as $key=>$vendor ) {
                                    	
                                    	?>
                                 <li class="category-item">
                                    <hr>
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
                                                      id="gdpr_messagebar_body_button_navy_blue_square_consent_vendor_<?php echo esc_html($vendor->id);?>" 
                                                      class="vendor-switch-handler <?php echo esc_html("consent-switch", "gdpr-cookie-consent");?> <?php echo esc_html($vendor->id);?>" 
                                                      type="checkbox" 
                                                      name="gdpr_messagebar_body_button_navy_blue_square_consent_vendor_<?php echo esc_html($vendor->id);?>" 
                                                      value=<?php echo esc_html( $vendor->id ); ?>>
                                                   <label for="gdpr_messagebar_body_button_navy_blue_square_consent_vendor_<?php echo esc_html($vendor->id);?>">
                                                   <span class="label-text"><?php echo esc_html( $vendor->id ); ?></span>
                                                   </label>
                                                   <!-- DYNAMICALLY GENERATE Input ID  -->
                                                </div>
                                             </div>
                                          </div>
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
                     <?php 
						if($the_options['is_gacm_on']==="true" || $the_options['is_gacm_on'] === true) {?>
							<ul class="category-group vendor-group tabContainer">
							<?php
						    $vendors = ["Google Ad Technology Providers"];
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
															id="gdpr_messagebar_body_button_navy_blue_square" 
															class="gacm-vendor-all-switch-handler" 
															type="checkbox" 
															name="gdpr_messagebar_body_button_navy_blue_square" 
															value=<?php echo esc_html( $data->allvendors ); ?>>
															<label for="gdpr_messagebar_body_button_navy_blue_square">
																<span class="label-text"></span>
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
																
																<?php foreach ( $gacm_data as $vendor ) {
																	if($vendor[0] != null) {
																		?>
																		<li class="category-item">
																		<hr>
																				<div class="toggle-group bottom-toggle">
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
																									id="gdpr_messagebar_body_button_navy_blue_square_consent_vendor_<?php echo esc_html($vendor[0]);?>" 
																									class="vendor-switch-handler <?php echo esc_html("consent-switch", "gdpr-cookie-consent");?> <?php echo esc_html($vendor[0]);?>" 
																									type="checkbox" 
																									name="gdpr_messagebar_body_button_navy_blue_square_consent_vendor_<?php echo esc_html($vendor[0]);?>" 
																									value=<?php echo esc_html( $vendor[0]); ?>>
																									<label for="gdpr_messagebar_body_button_navy_blue_square_consent_vendor_<?php echo esc_html($vendor[0]);?>">
																										<span class="label-text"><?php echo esc_html( $vendor[0] ); ?></span>
																									</label>
																									<!-- DYNAMICALLY GENERATE Input ID  -->
																								</div>
																							</div>
																						</div>
																					</div>
																			</div>
																				
																		<div class="inner-gdpr-column gdpr-category-toggle <?php echo esc_html( $the_options['template_parts'] ); ?>">
																			<div class="inner-gdpr-columns">
																				<span class="dashicons dashicons-arrow-down-alt2"></span>
																				<a href="#" class="btn category-header vendors" tabindex="0"><?php echo esc_html__( $vendor[1], 'gdpr-cookie-consent' ); // phpcs:ignore ?></a>
																			</div>
																		</div>
																		<div class="inner-description-container hide">
																			<div class="group-description" tabindex="0">
																				<div class="gdpr-ad-purpose-details">
																					<div class="gdpr-vendor-wrapper">
																						<p class="gdpr-vendor-privacy-link">
																							<span class="gdpr-vendor-privacy-link-title"><?php echo esc_html("Privacy Policy: ", "gdpr-cookie-consent");?></span>
																							<a href=<?php echo $vendor[2];?> target="_blank" rel="noopener noreferrer" aria-label="Privacy Policy"><?php echo $vendor[2];?></a>
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
			<div class="gdprmodal-footer">
								<button id="cookie_action_save" type="button" class="gdpr_action_button btn" data-gdpr_action="accept" data-dismiss="gdprmodal" style="color: rgb(255, 255, 255); background-color: rgb(54, 158, 227); border: 0px none rgb(54, 158, 227); border-radius: 0px;">Save And Accept</button>
			</div>
		</div>
	</div>
</div>
		</div>
