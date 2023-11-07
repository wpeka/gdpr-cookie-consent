<?php
// phpcs:ignoreFile
/**
 * Selectors used in the automation testcases for compliances settings tab of GDPR cookie consent plugin
 * 
 * @category AutomationTests
 * @package  WordPress_GDPRCookieConsent_Free_Plugin
 * @author   WPEKA <hello@wpeka.com>
 * @license  GPL v3
 * @link     https://club.wpeka.com
 */
namespace Page\Gdprfree;

/**
 * Core class for all the selectors used in compliances settings tab of GDPR cookie consent plugin
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
class CompliancesSettings
{
    public $gdprCookieConsent = '#toplevel_page_gdpr-cookie-consent > a > div.wp-menu-name';
    public $settings = '#toplevel_page_gdpr-cookie-consent > ul > li:nth-child(3) > a';
    public $revokeConsent = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(4) > header';
    public $tabPosition = '#vs7__combobox > div.vs__selected-options > input';
    public $tabPositionValue = '#vs7__option-0'; 
    public $tabPositionValue2 = '#vs7__option-1';
    public $saveChanges = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-bottom > div > button > span';  
    public $GDPR = '#wp-admin-bar-site-name > a';
    public $accept = '#cookie_action_accept';
    public $cookieSettingsButton = '#gdpr-cookie-consent-show-again > span';

    public $tabMargin = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(4) > div > div:nth-child(3) > div.col-sm-8.col > div > input';
    public $tabMarginValue = '10';

    public $tabText = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(4) > div > div:nth-child(4) > div.col-sm-8.col > div > input';
    public $tabTextValue = 'Cookie Settings Please Click';

    public $consentSettings = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(5) > header';
    public $autotickNonNecessaryCookie = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(5) > div > div:nth-child(1) > div.col-sm-8.col > label > span';
    public $cookieSettings = '#cookie_action_settings';
    public $autoHide = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(5) > div > div:nth-child(2) > div.col-sm-8.col > label > span';
    public $autoHideDelay = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(5) > div > div:nth-child(3) > div.col-sm-8.col > div > input';
    public $autoHideDelayValue = '5000';
    public $cSettings = '#gdpr-cookie-consent-show-again > span';
    public $autoScroll = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(5) > div > div:nth-child(4) > div.col-sm-8.col > label > span';
    public $autoScrollOffset = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(5) > div > div:nth-child(5) > div.col-sm-8.col > div > input';
    public $autoScrollOffsetValue = '5';
    public $archives = 'body > div.footer-nav-widgets-wrapper.header-footer-group > div > aside > div > div.footer-widgets.column-two.grid-item > div:nth-child(1) > div > div > div > h2';
    public $reloadAfterAutoScroll = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(5) > div > div:nth-child(6) > div.col-sm-8.col > label > span';
    public $reloadAfterAccept = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(5) > div > div:nth-child(7) > div.col-sm-8.col > label > span';
    public $reloadAfterDecline = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(5) > div > div:nth-child(8) > div.col-sm-8.col > label > span';
    public $decline = '#cookie_action_reject';

    public $extraSettings = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(6) > header';
    public $resetSettings = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(6) > div > div:nth-child(4) > div.col-sm-8.col > button';
    public $showCredits = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div:nth-child(6) > div > div:nth-child(2) > div.col-sm-8.col > label > span';
}
