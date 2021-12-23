<?php
/**
 * Provide a admin area view for the cookie list.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://club.wpeka.com
 * @since      2.1.0
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/admin/modules/cookie-custom/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}?>
<div class="gdpr-update-custom-cookie-form" v-for="cookie in post_cookie_list" :key="cookie['id_gdpr_cookie_post_cookies']">
	<input type="hidden" value="cookie['id_gdpr_cookie_post_cookies]" >
	<c-row class="gdpr-custom-cookie-box">
		<c-col class="col-sm-2 gdpr-custom-cookie-letter-box"><span class="gdpr-custom-cookie-letter">{{cookie['name'][0]}}</span></c-col>
		<c-col class="col-sm-10">
			<c-row class="table-rows">
				<c-col class="col-sm-4 table-cols-left"><c-input placeholder="Cookie Name" v-model="cookie['name']" ></c-input></c-col>
				<c-col class="col-sm-4 table-cols"><c-input placeholder="Cookie Domain" v-model="cookie['domain']" ></c-input></c-col>
				<c-col class="col-sm-4 table-cols"><c-input :placeholder="custom_cookie_duration_placeholder" :disabled="cookie['enable_duration']" v-model="cookie['duration']" ></c-input></c-col>
			</c-row>
			<c-row class="table-rows">
				<c-col class="col-sm-6 table-cols-left"><v-select class="gdpr-custom-cookie-select form-group" :reduce="label => label.code + ',' + cookie['id_gdpr_cookie_post_cookies']" :options="custom_cookie_categories" v-model="cookie['category']" @input="onSelectUpdateCookieCategory"></v-select></c-col>
				<input type="hidden" name="gdpr-custom-cookie-category" v-model="cookie['category_id']">
				<c-col class="col-sm-6 table-cols"><v-select class="gdpr-custom-cookie-select form-group" :reduce="label => label.code + ',' + cookie['id_gdpr_cookie_post_cookies']" :options="custom_cookie_types" v-model="cookie['type_name']" @input="onSelectUpdateCookieType"></v-select></c-col>
				<input type="hidden" name="gdpr-custom-cookie-type" v-model="cookie['type']">
			</c-row>
			<c-row class="table-rows">
				<c-col class="col-sm-12 table-cols-left"><c-textarea placeholder="Cookie Purpose" name="gdpr-cookie-consent-custom-cookie-purpose" v-model="cookie['description']" ></c-textarea></c-col>
			</c-row>
		</c-col>
		<c-col class="col-sm-3"></c-col>
		<c-col class="col-sm-9 gdpr-custom-cookie-links">
			<a class="gdpr-custom-cookie-link gdpr-custom-save-cookie" @click="onDeleteCustomCookie(cookie['id_gdpr_cookie_post_cookies'])"><?php esc_attr_e( 'Delete Cookie', 'gdpr-cookie-consent' ); ?></a>
		</c-col>
	</c-row>
</div>
<c-button color="info" @click="saveCookieUpdateSettings"><span>Save All Cookies</span></c-button>
