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
<div class="gdpr_messagebar_detail layout-default dark_row theme-twentytwentyfour hide-popup">
			<div class="gdprmodal gdprfade gdprshow" id="gdpr-gdprmodal" role="dialog" data-keyboard="false" data-backdrop="false" aria-gdprmodal="true" style="padding-right: 15px; display: block;">
	<div class="gdprmodal-dialog gdprmodal-dialog-centered">
		<!-- Modal content-->
		<div class="gdprmodal-content">
			<div class="gdprmodal-header">
				<button type="button" class="gdpr_action_button close" data-dismiss="gdprmodal" data-gdpr_action="close">
					<span class="dashicons dashicons-no">Close</span>
				</button>
			</div>
			<div class="gdprmodal-body">
				<div class="gdpr-details-content">
					<div class="gdpr-groups-container">
						<ul class="category-group">
							<li class="category-item">
								<div class="gdpr-column gdpr-default-category-toggle">
									<div class="gdpr-columns active-group" tabindex="0" style="background-color: rgb(62, 175, 154);">
										<a href="#" class="btn category-header">About Cookies</a>
									</div>
								</div>
								<div class="description-container">
									<div class="group-toggle">
										<h3 class="category-header" tabindex="0">About Cookies</h3>
									</div>
																		<div class="group-description" tabindex="0">Cookies are small text files that can be used by websites to make a user's experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies we need your permission. This site uses different types of cookies. Some cookies are placed by third party services that appear on our pages.</div>

								</div>
							</li>
															<li class="category-item">
								<div class="gdpr-column gdpr-default-category-toggle  dark_row">
									<div class="gdpr-columns" style="background-color: rgb(67, 74, 88);">
										<a href="#" class="btn category-header" tabindex="0">Necessary</a>
									</div>
								</div>
								<div class="description-container hide">
									<div class="group-toggle">
										<h3 class="category-header" tabindex="0">Necessary</h3>
																					<div class="toggle-group">
												<div class="always-active" style="color: rgb(62, 175, 154);">Always Active</div>
												<input id="gdpr_messagebar_body_button_necessary" type="hidden" name="gdpr_messagebar_body_button_necessary" value="necessary">
											</div>
																				</div>
									<div class="group-description" tabindex="0">Necessary cookies help make a website usable by enabling basic functions like page navigation and access to secure areas of the website. The website cannot function properly without these cookies.</div>
									<!-- sub groups -->
																					<div class="category-cookies-list-container">
																						</div>
																				</div>
							</li>
																			<li class="category-item">
								<div class="gdpr-column gdpr-default-category-toggle  dark_row">
									<div class="gdpr-columns" style="background-color: rgb(67, 74, 88);">
										<a href="#" class="btn category-header" tabindex="0">Marketing</a>
									</div>
								</div>
								<div class="description-container hide">
									<div class="group-toggle">
										<h3 class="category-header" tabindex="0">Marketing</h3>
																					<div class="toggle-group">
												<div class="toggle">
													<div class="checkbox">
														<!-- DYNAMICALLY GENERATE Input ID  -->
														<input id="gdpr_messagebar_body_button_marketing" class="category-switch-handler" type="checkbox" name="gdpr_messagebar_body_button_marketing" value="marketing">
														<label for="gdpr_messagebar_body_button_marketing">
															<span class="label-text">Marketing</span>
														</label>
														<!-- DYNAMICALLY GENERATE Input ID  -->
													</div>
												</div>
											</div>
																				</div>
									<div class="group-description" tabindex="0">Marketing cookies are used to track visitors across websites. The intention is to display ads that are relevant and engaging for the individual user and thereby more valuable for publishers and third party advertisers.</div>
									<!-- sub groups -->
																					<div class="category-cookies-list-container">
																						</div>
																				</div>
							</li>
																			<li class="category-item">
								<div class="gdpr-column gdpr-default-category-toggle  dark_row">
									<div class="gdpr-columns" style="background-color: rgb(67, 74, 88);">
										<a href="#" class="btn category-header" tabindex="0">Analytics</a>
									</div>
								</div>
								<div class="description-container hide">
									<div class="group-toggle">
										<h3 class="category-header" tabindex="0">Analytics</h3>
																					<div class="toggle-group">
												<div class="toggle">
													<div class="checkbox">
														<!-- DYNAMICALLY GENERATE Input ID  -->
														<input id="gdpr_messagebar_body_button_analytics" class="category-switch-handler" type="checkbox" name="gdpr_messagebar_body_button_analytics" value="analytics">
														<label for="gdpr_messagebar_body_button_analytics">
															<span class="label-text">Analytics</span>
														</label>
														<!-- DYNAMICALLY GENERATE Input ID  -->
													</div>
												</div>
											</div>
																				</div>
									<div class="group-description" tabindex="0">Analytics cookies help website owners to understand how visitors interact with websites by collecting and reporting information anonymously.</div>
									<!-- sub groups -->
																					<div class="category-cookies-list-container">
																						</div>
																				</div>
							</li>
																			<li class="category-item">
								<div class="gdpr-column gdpr-default-category-toggle  dark_row">
									<div class="gdpr-columns" style="background-color: rgb(67, 74, 88);">
										<a href="#" class="btn category-header" tabindex="0">Preference</a>
									</div>
								</div>
								<div class="description-container hide">
									<div class="group-toggle">
										<h3 class="category-header" tabindex="0">Preference</h3>
																					<div class="toggle-group">
												<div class="toggle">
													<div class="checkbox">
														<!-- DYNAMICALLY GENERATE Input ID  -->
														<input id="gdpr_messagebar_body_button_preferences" class="category-switch-handler" type="checkbox" name="gdpr_messagebar_body_button_preferences" value="preferences">
														<label for="gdpr_messagebar_body_button_preferences">
															<span class="label-text">Preference</span>
														</label>
														<!-- DYNAMICALLY GENERATE Input ID  -->
													</div>
												</div>
											</div>
																				</div>
									<div class="group-description" tabindex="0">Preference cookies enable a website to remember information that changes the way the website behaves or looks, like your preferred language or the region that you are in.</div>
									<!-- sub groups -->
																					<div class="category-cookies-list-container">
																						</div>
																				</div>
							</li>
																			<li class="category-item">
								<div class="gdpr-column gdpr-default-category-toggle  dark_row">
									<div class="gdpr-columns" style="background-color: rgb(67, 74, 88);">
										<a href="#" class="btn category-header" tabindex="0">Unclassified</a>
									</div>
								</div>
								<div class="description-container hide">
									<div class="group-toggle">
										<h3 class="category-header" tabindex="0">Unclassified</h3>
																					<div class="toggle-group">
												<div class="toggle">
													<div class="checkbox">
														<!-- DYNAMICALLY GENERATE Input ID  -->
														<input id="gdpr_messagebar_body_button_unclassified" class="category-switch-handler" type="checkbox" name="gdpr_messagebar_body_button_unclassified" value="unclassified">
														<label for="gdpr_messagebar_body_button_unclassified">
															<span class="label-text">Unclassified</span>
														</label>
														<!-- DYNAMICALLY GENERATE Input ID  -->
													</div>
												</div>
											</div>
																				</div>
									<div class="group-description" tabindex="0">Unclassified cookies are cookies that we are in the process of classifying, together with the providers of individual cookies.</div>
									<!-- sub groups -->
																					<div class="category-cookies-list-container">
																						</div>
																				</div>
							</li>
																	</ul>
					</div>
				</div>
			</div>
			<div class="gdprmodal-footer">
								<button id="cookie_action_save" type="button" class="gdpr_action_button btn" data-gdpr_action="accept" data-dismiss="gdprmodal" style="color: rgb(255, 255, 255); background-color: rgb(62, 175, 154); border: 1px solid rgb(62, 175, 154); border-radius: 0px;">Save And Accept</button>
			</div>
		</div>
	</div>
</div>
		</div>
