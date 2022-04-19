<?php
/**
 * Selectors used in the automation testcases for cookie list and script blocker tab of GDPR cookie consent plugin
 * 
 * @category AutomationTests
 * @package  WordPress_WPlegalpages_Pro_Plugin
 * @author   WPEKA <hello@wpeka.com>
 * @license  GPL v3
 * @link     https://club.wpeka.com
 */
namespace Page\Gdprfree;

/**
 * Core class for all the selectors used in cookie settings of GDPR cookie consent plugin
 * 
 * @category  AutomationTests
 * @package   WordPress_WPlegalpages_Pro_Plugin
 * @author    WPEKA <hello@wpeka.com>
 * @copyright 2022 WPEKA
 * @license   GPL v3
 * @link      https://club.wpeka.com
 * 
 * @since 1.0
 */
class CookieSettings
{
     public $gdprCookieConset = '#toplevel_page_gdpr-cookie-consent > a > div.wp-menu-name';
     public $settings = '#toplevel_page_gdpr-cookie-consent > ul > li:nth-child(3) > a';
     public $cookieList = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(1) > ul > li:nth-child(4) > a';
     public $cookieNameValue = 'My Cookie';
     public $cookieDomainValue = 'https://club.wpeka.com';
     public $cookieDaysValue = '1';
     public $cookieType = '#vs38__combobox > div.vs__selected-options > input';
     public $cookieTypeValue = '#vs38__option-0';
     public $cookieStorage = '#vs39__combobox > div.vs__selected-options > input';
     public $cookieStorageValue = '#vs39__option-0';
     public $cookiePurposeValue = 'cookies Purpose';
     public $save = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div > div > div.gdpr-add-custom-cookie-form > div > div.col-sm-9.gdpr-custom-cookie-links.col > a.gdpr-custom-cookie-link.gdpr-custom-save-cookie';
     public $GDPR = '#wp-admin-bar-site-name > a';
     public $cookieSettingsButton = '#gdpr-cookie-consent-show-again > span';
     public $cookieSettingsButton2 = '#cookie_action_settings';
     public $analytics = '#gdpr_messagebar_detail_body_content_overview_cookie_container_analytics';

     public $editedCookieName = '#gdpr-custom-cookie-saved > div > div > div.col-sm-10.col > div:nth-child(1) > div.col-sm-4.table-cols-left.col > div > input';
     public $cookieNameEditedValue = 'New Cookie';
     public $editedCookieDomain = '#gdpr-custom-cookie-saved > div > div > div.col-sm-10.col > div:nth-child(1) > div:nth-child(2) > div > input';
     public $editedCookieDomainValue = 'https://cyberchimps.com';
     public $editedCookieDays = '#gdpr-custom-cookie-saved > div > div > div.col-sm-10.col > div:nth-child(1) > div:nth-child(3) > div > input';
     public $cookieDaysEditedValue = '2';
     public $editedCookiePurpose = '#gdpr-custom-cookie-saved > div > div > div.col-sm-10.col > div:nth-child(3) > div > div > textarea';
     public $editedCookiePurposeValue = 'Cookie';
     public $cookieTypeEditedValue = '#vs40__option-3';
     public $cookieEditedType = '#vs40__combobox > div.vs__selected-options > input';
     public $cookieEditedStorage = '#vs41__combobox > div.vs__selected-options > input';
     public $cookieEditedStorageValue = '#vs41__option-1';
     public $saveAllChanges = '#gdpr-custom-cookie-saved > button';
     public $preferences = '#gdpr_messagebar_detail_body_content_overview_cookie_container_preferences';
     public $delete = '#gdpr-custom-cookie-saved > div > div > div.col-sm-9.gdpr-custom-cookie-links.col > a';

     public $scriptBlocker = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(1) > ul > li:nth-child(5) > a';
     public $enableButton = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div > div > div:nth-child(1) > div.col-sm-8.col > label > span';
     public $headerScriptValue = '<script> alert("Hello, world!"); </script>';
     public $saveChanges = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-bottom > div > button';
     public $accept = '#cookie_action_accept';
     public $bodyScriptValue = '<script> alert("Hello, world!"); </script>';
}
