<?php
/**
 * The admin-facing functionality of the plugin in preview.
 *
 * @link       https://club.wpeka.com
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


?>

<div class="gdpr_messagebar_body_heading_extended ">
    <?php echo esc_html__('I consent to the use of the following cookies:', 'gdpr-cookie-consent'); ?>
</div>
<div id="gdpr_messagebar_body_buttons_select_pane_extended">

    <div class="gdpr_messagebar_body_buttons_wrapper_extended">
        <input type="checkbox" id="gdpr_messagebar_body_button_necessary_extended" value="necessary">        
        <label for="gdpr_messagebar_body_button_necessary_extended"><?php echo esc_html__('Necessary', 'gdpr-cookie-consent'); ?></label>
    </div>
    <div class="gdpr_messagebar_body_buttons_wrapper_extended">
        <input type="checkbox" id="gdpr_messagebar_body_button_marketing_extended" value="marketing">        
        <label for="gdpr_messagebar_body_button_marketing_extended"><?php echo esc_html__('Marketing', 'gdpr-cookie-consent'); ?></label>
    </div>
    <div class="gdpr_messagebar_body_buttons_wrapper_extended">
        <input type="checkbox" id="gdpr_messagebar_body_button_analytics_extended" value="analytics">        
        <label for="gdpr_messagebar_body_button_analytics_extended"><?php echo esc_html__('Analytics', 'gdpr-cookie-consent'); ?></label>
    </div>
    <div class="gdpr_messagebar_body_buttons_wrapper_extended">
        <input type="checkbox" id="gdpr_messagebar_body_button_preference_extended" value="preference">        
        <label for="gdpr_messagebar_body_button_preference_extended"><?php echo esc_html__('Preference', 'gdpr-cookie-consent'); ?></label>
    </div>
    <div class="gdpr_messagebar_body_buttons_wrapper_extended">
        <input type="checkbox" id="gdpr_messagebar_body_button_unclassified_extended" value="unclassified">        
        <label for="gdpr_messagebar_body_button_unclassified_extended"><?php echo esc_html__('Unclassified', 'gdpr-cookie-consent'); ?></label>
    </div>
</div>
<div id="gdpr_messagebar_detail_body">
<div id="gdpr_messagebar_detail_body_content_tabs_extended">
    <button id='gdpr_messagebar_detail_body_content_tabs_overview_extended' :class="{'gdpr_messagebar_detail_body_content_overview_extended_active':preview_cookie_declaration}"  @click=onClickPreviewCookieDeclaration><?php echo esc_html__('Cookie Declaration', 'gdpr-cookie-consent'); ?></button>
    <button id="gdpr_messagebar_detail_body_content_tabs_about_extended" :class="{'gdpr_messagebar_detail_body_content_tabs_about_extended_active':preview_about_cookie}" @click=onClickPreviewAboutCookie><?php echo esc_html__('About Cookies', 'gdpr-cookie-consent'); ?></button>
</div>
<div id="gdpr_messagebar_detail_body_content_extended">
    <div id="gdpr_messagebar_detail_body_content_overview_extended" style="display:block;">
        <div id="gdpr_messagebar_detail_body_content_overview_cookie_container_extended">
            <div v-show="preview_cookie_declaration" id="gdpr_messagebar_detail_body_content_overview_cookie_container_types_extended">
                <button id="gdpr_messagebar_detail_body_content_overview_cookie_container_necessary_extended" :class="{'gdpr_messagebar_detail_body_content_overview_cookie_container_necessary_extended_active':preview_necessary}" @click="onSwitchPreviewNecessary"><?php echo esc_html__('Necessary', 'gdpr-cookie-consent'); ?> (0)</button>
                <button id="gdpr_messagebar_detail_body_content_overview_cookie_container_marketing_extended" :class="{'gdpr_messagebar_detail_body_content_overview_cookie_container_marketing_extended_active':preview_marketing}" @click="onSwitchPreviewMarketing"><?php echo esc_html__('Marketing', 'gdpr-cookie-consent'); ?> (0)</button>
                <button id="gdpr_messagebar_detail_body_content_overview_cookie_container_analytics_extended" :class="{'gdpr_messagebar_detail_body_content_overview_cookie_container_analytics_extended_active':preview_analysis}" @click="onSwitchPreviewAnalysis"><?php echo esc_html__('Analytics', 'gdpr-cookie-consent'); ?> (0)</button>
                <button id="gdpr_messagebar_detail_body_content_overview_cookie_container_preference_extended" :class="{'gdpr_messagebar_detail_body_content_overview_cookie_container_preference_extended_active':preview_preference}" @click="onSwitchPreviewPreference"><?php echo esc_html__('Preference', 'gdpr-cookie-consent'); ?> (0)</button>
                <button id="gdpr_messagebar_detail_body_content_overview_cookie_container_unclassified_extended" :class="{'gdpr_messagebar_detail_body_content_overview_cookie_container_unclassified_extended_active':preview_unclassified}" @click="onSwitchPreviewUnclassified"><?php echo esc_html__('Unclassified', 'gdpr-cookie-consent'); ?> (0)</button>
            </div>
            <div id="gdpr_messagebar_detail_body_content_overview_cookie_container_type_details_extended" :class="{'gdpr_messagebar_detail_body_content_overview_cookie_container_type_details_extended_active':preview_unclassified || preview_necessary || preview_marketing || preview_analysis || preview_preference}">
                <div v-show="preview_necessary" id="gdpr_messagebar_detail_body_content_cookie_tabs_necessary_extended">
                    <div class="gdpr_messagebar_detail_body_content_cookie_type_intro_extended">
                        <div><?php echo esc_html__('Necessary cookies help make a website usable by enabling basic functions like page navigation and access to secure areas of the website. The website cannot function properly without these cookies.', 'gdpr-cookie-consent'); ?></div>
                        <div class="gdpr_messagebar_detail_body_content_cookie_type_table_container_extended"><?php echo esc_html__('We do not use cookies of this type.', 'gdpr-cookie-consent'); ?></div>
                    </div>
                </div>
                <div v-show="preview_marketing" id="gdpr_messagebar_detail_body_content_cookie_tabs_marketing_extended">
                    <div class="gdpr_messagebar_detail_body_content_cookie_type_intro_extended">
                        <div><?php echo esc_html__('Marketing cookies are used to track visitors across websites. The intention is to display ads that are relevant and engaging for the individual user and thereby more valuable for publishers and third-party advertisers.', 'gdpr-cookie-consent'); ?></div>
                        <div class="gdpr_messagebar_detail_body_content_cookie_type_table_container_extended"><?php echo esc_html__('We do not use cookies of this type.', 'gdpr-cookie-consent'); ?></div>
                    </div>
                </div>
                <div v-show="preview_analysis" id="gdpr_messagebar_detail_body_content_cookie_tabs_analytics_extended">
                    <div class="gdpr_messagebar_detail_body_content_cookie_type_intro_extended">
                        <div><?php echo esc_html__('Analytics cookies help website owners understand how visitors interact with websites by collecting and reporting information anonymously.', 'gdpr-cookie-consent'); ?></div>
                        <div class="gdpr_messagebar_detail_body_content_cookie_type_table_container_extended"><?php echo esc_html__('We do not use cookies of this type.', 'gdpr-cookie-consent'); ?></div>
                    </div>
                </div>
                <div v-show="preview_preference" id="gdpr_messagebar_detail_body_content_cookie_tabs_preferences_extended">
                    <div class="gdpr_messagebar_detail_body_content_cookie_type_intro_extended">
                        <div><?php echo esc_html__('Preference cookies enable a website to remember information that changes the way the website behaves or looks, like your preferred language or the region that you are in.', 'gdpr-cookie-consent'); ?></div>
                        <div class="gdpr_messagebar_detail_body_content_cookie_type_table_container_extended"><?php echo esc_html__('We do not use cookies of this type.', 'gdpr-cookie-consent'); ?></div>
                    </div>
                </div>
                <div v-show="preview_unclassified" id="gdpr_messagebar_detail_body_content_cookie_tabs_unclassified_extended">
                    <div class="gdpr_messagebar_detail_body_content_cookie_type_intro_extended">
                        <div><?php echo esc_html__('Unclassified cookies are cookies that we are in the process of classifying, together with the providers of individual cookies.', 'gdpr-cookie-consent'); ?></div>
                        <div class="gdpr_messagebar_detail_body_content_cookie_type_table_container_extended"><?php echo esc_html__('We do not use cookies of this type.', 'gdpr-cookie-consent'); ?></div>
                    </div>
                </div>
            </div>
            <div v-show="preview_about_cookie" id="gdpr_messagebar_detail_body_content_about_extended" :class="{'gdpr_messagebar_detail_body_content_overview_cookie_container_type_details_extended_active':preview_about_cookie}">
                <?php echo esc_html__('Cookies are small text files that can be used by websites to make a user\'s experience more efficient. The law states that we can store cookies on your device if they are strictly necessary for the operation of this site. For all other types of cookies, we need your permission. This site uses different types of cookies. Some cookies are placed by third-party services that appear on our pages.', 'gdpr-cookie-consent'); ?>
            </div>
        </div>
    </div>
</div>
</div>
