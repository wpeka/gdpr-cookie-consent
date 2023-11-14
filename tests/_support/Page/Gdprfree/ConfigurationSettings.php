<?php
/**
 * Selectors used in the automation testcases for configuration settings tab of GDPR cookie consent plugin
 * 
 * @category AutomationTests
 * @package  WordPress_GDPRCookieConsent_Free_Plugin
 * @author   WPEKA <hello@wpeka.com>
 * @license  GPL v3
 * @link     https://club.wpeka.com
 */
namespace Page\Gdprfree;

/**
 * Core class for all the selectors used in configuration settings tab of GDPR cookie consent plugin
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
class ConfigurationSettings
{
    public $gdprCookieConsent = '#toplevel_page_gdpr-cookie-consent > a > div.wp-menu-name';
    public $settings = '#toplevel_page_gdpr-cookie-consent > ul > li:nth-child(3) > a';
    public $configuration = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(1) > ul > li:nth-child(2) > a';
    public $showCookieNoticeAs = '#vs9__combobox > div.vs__selected-options > input'; 
    public $banner = '#vs9__option-0';
    public $widget = '#vs9__option-2';
    public $popup = '#vs9__option-1';
    public $saveChanges = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-bottom > div > button > span';
    public $GDPR = '#wp-admin-bar-site-name > a';
    public $position = '#vs10__combobox > div.vs__selected-options > input';
    public $top = '#vs10__option-0';
    public $bottom = '#vs10__option-1';
    public $accept = '#cookie_action_accept';
    public $widgetPosition = '#vs11__combobox > div.vs__selected-options > input';
    public $right = '#vs11__option-1';
    public $left = '#vs11__option-0';
    public $addOverlay = '#gcc-save-settings-form > div.gdpr-cookie-consent-settings-content > div.gdpr-cookie-consent-settings-nav > div:nth-child(2) > div > div.tab-pane.active > div > div > div:nth-child(4) > div.col-sm-8.col > label > span';
    public $onHide = '#vs12__combobox > div.vs__selected-options > input';
    public $animate = '#vs12__option-0';
    public $disappear = '#vs12__option-1';
}
