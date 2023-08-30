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
$is_pro = get_option( 'wpl_pro_active', false );


?>

<div class="gdpr-wizard-top-container">
	<img class="gdpr-wizard-logo" src="<?php echo $image_path.'gdprLogo.png'; ?>">
	<span class="gdpr-main-heading">GDPR Cookie Consent</span>

</div>

<div class="gdpr-wizard-main-container" id="gdpr-cookie-consent-settings-app">

<div class="form-container">

        <form id="form">
          <ul id="progressbar">
            <li class="active" id="step1">
				<div class="progress-step">
					<strong class="progress-bar-label">Getting Started</strong>
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
					<img class="step-images selected-step-img" src="<?php echo $image_path.'selected-step.png'; ?>">

					</div>
				</div>
			</li>
            <li id="step3">
				<div class="progress-step">
					<strong class="progress-bar-label">Configuration</strong>
					<div class="container">
					<div class="horizontal-line line-step-3"></div>
					<img class="step-images not-selected-step-img" src="<?php echo $image_path.'not-selected-step.png'; ?>">

					</div>
				</div>
			</li>
            <li id="step4">
				<div class="progress-step">
					<strong class="progress-bar-label">Finish</strong>
					<div class="container">
					<div class="horizontal-line line-step-4"></div>
					<img class="step-images finish-step-img" src="<?php echo $image_path.'finish.png'; ?>">

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
				// if gdpr-pro is enable only then show these options
				if ( $is_pro ) {

					?>
					<!-- Location Enable/Disbale for different rule  -->
					<div v-show="show_visitor_conditions">

						<div>
							<!-- IAB  -->
							<c-row class="ccpa-iab-selection" v-show="is_ccpa" >
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

						<!-- gdpr  -->
						<c-row class="gdpr-selection" v-show="is_gdpr" >
								<c-col class="gdpr-selection-label"><label><?php esc_attr_e( 'Show only for EU visitors', 'gdpr-cookie-consent' ); ?> </label></c-col>
								<c-col class="gdpr-options">
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

						<!-- ccpa other tab  -->

						<c-row class="ccpa-selection"  v-show="is_ccpa" >
								<c-col class="ccpa-selection-label"><label><?php esc_attr_e( 'Show only for California visitors', 'gdpr-cookie-consent' ); ?> </label></c-col>
								<c-col class="ccpa-options">
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

				}else {

					?>
					<!-- When Pro is not activated  -->
						<!-- gdpr  -->
						<div class="gdpr-free-container">

								<c-row class="gdpr-selection">
										<c-col class="gdpr-selection-label"><label><?php esc_attr_e( 'Show only for EU visitors', 'gdpr-cookie-consent' ); ?> </label></c-col>
										<c-col class="gdpr-options">
											<label class="gdpr-yes-option">
											<input disabled type="radio" name="gcc-eu-enable" value="yes" v-model="selectedRadioGdpr" @click="onSwitchEUEnable('yes')">
											Yes
											</label>
											<label>
											<input type="radio" name="gcc-eu-enable" value="no" v-model="selectedRadioGdpr" @click="onSwitchEUEnable('no')">
											No
											</label>
											<input type="hidden" name="gcc-eu-enable" v-model="is_eu_on">
										</c-col>
								</c-row>
								<div class="gdpr-pro-label">
									<div class="gdpr-pro-label-text" >Pro</div>
								</div>
						</div>
					<?php

				}

				?>

				</div>

              <input type="button" name="next-step" class="next-step first-next-step" value="Save & Continue" />

            </fieldset>
			<!-- First Tab Conetent End  -->

			<!-- Next Field set  -->
            <fieldset>
              <h2>Welcome To Step 3</h2>
              <input type="button" name="next-step" class="next-step second-next-step" value="Final Step" />
              <input type="button" name="previous-step" class="previous-step first-previous-step" value="Previous Step" />
            </fieldset>
            <fieldset>
              <div class="finish">
                <h2 class="text text-center"><strong>FINISHED</strong></h2>
              </div>
              <input type="button" name="previous-step" class="previous-step second-previous-step" value="Previous Step" />
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
	var imagePath = "<?php echo $image_path; ?>";

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

});


</script>
