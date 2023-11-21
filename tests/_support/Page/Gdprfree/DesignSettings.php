<?php
// phpcs:ignoreFile
/**
 * Selectors used in the automation testcases for design settings tab of GDPR cookie consent plugin
 * 
 * @category AutomationTests
 * @package  WordPress_GDPRCookieConsent_Free_Plugin
 * @author   WPEKA <hello@wpeka.com>
 * @license  GPL v3
 * @link     https://club.wpeka.com
 */
namespace Page\Gdprfree;

/**
 * Core class for all the selectors used in design settings tab of GDPR cookie consent plugin
 * 
 * @category  AutomationTests
 * @package   WordPress_GDPRCookieConsent_Free_Plugin
 * @author    WPEKA <hello@wpeka.com>
 * @copyright 2022 WPEKA
 * @license   GPL v3
 * @link      https://club.wpeka.com
 * 
 * @since 1.0
 */
class DesignSettings
{
    public $gdprCookieConsent = '#toplevel_page_gdpr-cookie-consent > a > div.wp-menu-name';
    public $settings = '#toplevel_page_gdpr-cookie-consent > ul > li:nth-child(3) > a';
    public $design = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(1) > ul > li:nth-child(3) > a';
    public $cookieBarColor = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(1) > div > div:nth-child(1) > div.col-sm-8.gdpr-color-pick.col > div.gdpr-color-input.form-group > input';
    public $cookieBarColorValue = '#ff12ff';
    public $saveChanges = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-bottom > div > button > span'; 
    public $GDPR = '#wp-admin-bar-site-name > a';
    public $cookieBarOpacity = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(1) > div > div:nth-child(2) > div.col-sm-8.gdpr-color-pick.col > div.gdpr-slider-input.form-group > input';
    public $cookieBarOpacityValue = '0.5';
    public $cookieBarTextColor = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(1) > div > div:nth-child(3) > div.col-sm-8.gdpr-color-pick.col > div.gdpr-color-input.form-group > input';
    public $cookieBarTextColorValue = '#800000';
    public $borderStyle = '#vs14__combobox > div.vs__selected-options > input';
    public $borderStyleValue = '#vs14__option-2';
    public $borderWidth = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(1) > div > div:nth-child(5) > div.col-sm-8.gdpr-color-pick.col > div.gdpr-slider-input.form-group > input';
    public $borderWidthValue = '5';
    public $borderColor = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(1) > div > div:nth-child(6) > div.col-sm-8.gdpr-color-pick.col > div.gdpr-color-input.form-group > input';
    public $borderColorValue = '#000000';
    public $borderRadius = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(1) > div > div:nth-child(7) > div.col-sm-8.gdpr-color-pick.col > div.gdpr-slider-input.form-group > input';
    public $borderRadiusValue = '3';
   
    public $acceptButton = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(2) > div > div.row > div.col-sm-5.col > label > span';
    public $configureAcceptBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(2) > div > div.row > div.col-sm-3.col > button > span > img';
    public $textAcceptBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(2) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(2) > div:nth-child(1) > div > input';
    public $textAcceptBtnValue = 'Accept Button';
    public $done = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(2) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > footer > button > span';
    public $textColorAcceptBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(2) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(2) > div.col-sm-6.gdpr-color-pick.col > div.gdpr-color-input.form-group > input';
    public $textColorAcceptBtnValue = '#000000';
    public $showAsOptionForAcceptBtn = '#vs15__combobox > div.vs__selected-options > input';
    public $showAsOptionForAcceptBtnValue = '#vs15__option-1';
    public $showAsOptionForAcceptBtnValue2 = '#vs15__option-0';
    public $bgColorAcceptBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(2) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(8) > div.col-sm-6.gdpr-color-pick.col > div.gdpr-color-input.form-group > input';
    public $bgColorAcceptBtnValue = '#00ffff';
    public $bgOpacityAcceptBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(2) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(12) > div:nth-child(1) > div.gdpr-slider-input.form-group > input';
    public $bgOpacityAcceptBtnValue = '1';
    public $borderStyleAcceptBtn = '#vs19__combobox > div.vs__selected-options > input';
    public $borderStyleAcceptBtnValue = '#vs19__option-4';
    public $borderWidthAcceptBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(2) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(12) > div:nth-child(2) > div.gdpr-slider-input.form-group > input';
    public $borderWidthAcceptBtnValue = '5';
    public $borderRadiusAcceptBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(2) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(12) > div:nth-child(3) > div.gdpr-slider-input.form-group > input';
    public $borderRadiusAcceptBtnValue = '3';
    public $borderColorAcceptBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(2) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(10) > div.col-sm-6.gdpr-color-pick.col > div.gdpr-color-input.form-group > input';
    public $borderColorAcceptBtnValue = '#000000';
    public $btnSizeAcceptBtn = '#vs18__combobox > div.vs__selected-options > input';
    public $btnSizeAcceptBtnValue = '#vs18__option-1';
    public $actionAcceptBtn = '#vs16__combobox > div.vs__selected-options > input';
    public $actionAcceptBtnValue = '#vs16__option-0';
    public $actionAcceptBtnValue2 = '#vs16__option-1';
    public $acceptBtn = '#cookie_action_accept';
    public $openUrlInNewWindow = '#vs17__combobox > div.vs__selected-options > input';
    public $openUrlInNewWindowValue = '#vs17__option-0';
    public $openUrlInNewWindowValue2 = '#vs17__option-1';
    public $url = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(2) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(6) > div:nth-child(1) > div > input';
    public $urlValue = 'http://google.com';

    public $acceptAllButton = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(3) > div > div.row > div.col-sm-5.col > label > span';
    public $configureAcceptAllBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(3) > div > div.row > div.col-sm-3.col > button > span > img';
    public $textAcceptAllBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(3) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(2) > div:nth-child(1) > div > input';
    public $textAcceptAllBtnValue = 'Accept All Btn';
    public $done2 = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(3) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > footer > button > span';
    public $textColorAcceptAllBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(3) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(2) > div.col-sm-6.gdpr-color-pick.col > div.gdpr-color-input.form-group > input';
    public $textColorAcceptAllBtnValue = '#000000';
    public $showAsOptionForAcceptAllBtn = '#vs20__combobox > div.vs__selected-options > input';
    public $showAsOptionForAcceptAllBtnValue = '#vs20__option-1';
    public $showAsOptionForAcceptAllBtnValue2 = '#vs20__option-0';
    public $bgColorAcceptAllBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(3) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(8) > div.col-sm-6.gdpr-color-pick.col > div.gdpr-color-input.form-group > input';
    public $bgColorAcceptAllBtnValue = '#00ffff';
    public $bgOpacityAcceptAllBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(3) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(12) > div:nth-child(1) > div.gdpr-slider-input.form-group > input';
    public $bgOpacityAcceptAllBtnValue = '1';
    public $borderStyleAcceptAllBtn = '#vs24__combobox > div.vs__selected-options > input';
    public $borderStyleAcceptAllBtnValue = '#vs24__option-4';
    public $borderWidthAcceptAllBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(3) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(12) > div:nth-child(2) > div.gdpr-slider-input.form-group > input';
    public $borderWidthAcceptAllBtnValue = '5';
    public $borderRadiusAcceptAllBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(3) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(12) > div:nth-child(3) > div.gdpr-slider-input.form-group > input';
    public $borderRadiusAcceptAllBtnValue = '3';
    public $borderColorAcceptAllBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(3) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(10) > div.col-sm-6.gdpr-color-pick.col > div.gdpr-color-input.form-group > input';
    public $borderColorAcceptAllBtnValue = '#000000';
    public $btnSizeAcceptAllBtn = '#vs23__combobox > div.vs__selected-options > input';
    public $btnSizeAcceptAllBtnValue = '#vs23__option-1';
    public $actionAcceptAllBtn = '#vs21__combobox > div.vs__selected-options > input';
    public $actionAcceptAllBtnValue = '#vs21__option-0';
    public $actionAcceptAllBtnValue2 = '#vs21__option-1';
    public $acceptAllBtn = '#cookie_action_accept_all';
    public $openUrlInNewWindowForAcceptAllBtn = '#vs22__combobox > div.vs__selected-options > input';
    public $openUrlInNewWindowForAcceptAllBtnValue = '#vs22__option-0';
    public $openUrlInNewWindowForAcceptAllBtnValue2 = '#vs22__option-1'; 
    public $urlforAcceptAllButton = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(3) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(6) > div:nth-child(1) > div > input';
    public $urlforAcceptAllButtonValue = 'http://google.com';

    public $declineButton = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(4) > div > div.row > div.col-sm-5.col > label > span';
    public $configureDeclineBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(4) > div > div.row > div.col-sm-3.col > button > span > img';
    public $textDeclineBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(4) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(2) > div:nth-child(1) > div > input';
    public $textDeclineBtnValue = 'Decline Btn';
    public $done3 = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(4) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > footer > button';
    public $textColorDeclineBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(4) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(2) > div.col-sm-6.gdpr-color-pick.col > div.gdpr-color-input.form-group > input';
    public $textColorDeclineBtnValue = '#000000';
    public $showAsOptionForDeclineBtn = '#vs25__combobox > div.vs__selected-options > input';
    public $showAsOptionForDeclineBtnValue = '#vs25__option-1';
    public $showAsOptionForDeclineBtnValue2 = '#vs25__option-0';
    public $bgColorDeclineBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(4) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(8) > div.col-sm-6.gdpr-color-pick.col > div.gdpr-color-input.form-group > input';
    public $bgColorDeclineBtnValue = '#00ffff';
    public $bgOpacityDeclineBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(4) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(12) > div:nth-child(1) > div.gdpr-slider-input.form-group > input';
    public $bgOpacityDeclineBtnValue = '1';
    public $borderStyleDeclineBtn = '#vs29__combobox > div.vs__selected-options > input';
    public $borderStyleDeclineBtnValue = '#vs29__option-4';
    public $borderWidthDeclineBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(4) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(12) > div:nth-child(2) > div.gdpr-slider-input.form-group > input';
    public $borderWidthDeclineBtnValue = '5';
    public $borderRadiusDeclineBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(4) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(12) > div:nth-child(3) > div.gdpr-slider-input.form-group > input';
    public $borderRadiusDeclineBtnValue = '4';
    public $borderColorDeclineBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(4) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(10) > div.col-sm-6.gdpr-color-pick.col > div.gdpr-color-input.form-group > input';
    public $borderColorDeclineBtnValue = '#000000';
    public $btnSizeDeclineBtn = '#vs28__combobox > div.vs__selected-options > input';
    public $btnSizeDeclineBtnValue = '#vs28__option-1';
    public $actionDeclineBtn = '#vs26__combobox > div.vs__selected-options > input';
    public $actionDeclineBtnValue = '#vs26__option-0';
    public $actionDeclineBtnValue2 = '#vs26__option-1';
    public $declineBtn = '#cookie_action_reject';
    public $openUrlInNewWindowForDeclineBtn = '#vs27__combobox > div.vs__selected-options > input';
    public $openUrlInNewWindowForDeclineBtnValue = '#vs27__option-0';
    public $openUrlInNewWindowForDeclineBtnValue2 = '#vs27__option-1';
    public $urlforDeclineButton = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(4) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(6) > div:nth-child(1) > div > input';
    public $urlforDeclineButtonValue = 'http://google.com';

    public $settingsButton = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(5) > div > div.row > div.col-sm-5.col > label > span';
    public $configureSettingsBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(5) > div > div.row > div.col-sm-3.col > button > span > img';
    public $textSettingsBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(5) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(2) > div:nth-child(1) > div > input';
    public $textSettingsBtnValue = 'Cookie Settings Btn';
    public $done4 = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(5) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > footer > button > span';
    public $textColorSettingsBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(5) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(2) > div.col-sm-6.gdpr-color-pick.col > div.gdpr-color-input.form-group > input';
    public $textColorSettingsBtnValue = '#000000';
    public $showAsOptionForSettingsBtn = '#vs30__combobox > div.vs__selected-options > input';
    public $showAsOptionForSettingsBtnValue = '#vs30__option-1';
    public $showAsOptionForSettingsBtnValue2 = '#vs30__option-0';
    public $bgColorSettingsBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(5) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(6) > div.col-sm-6.gdpr-color-pick.col > div.gdpr-color-input.form-group > input';
    public $bgColorSettingsBtnValue = '#00ffff';
    public $bgOpacitySettingsBtn = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(5) > div > div:nth-child(2) > div.modal.overflow-auto.fade.show.d-block > div > div > div > div:nth-child(10) > div:nth-child(1) > div.gdpr-slider-input.form-group > input';
    public $bgOpacitySettingsBtnValue = '0.5';
}
