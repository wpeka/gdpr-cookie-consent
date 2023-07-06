<?php
/**
 * Selectors used in the automation testcases for dashboard page of GDPR cookie consent plugin
 * 
 * @category AutomationTests
 * @package  WordPress_GDPRCookieConsent_Free_Plugin
 * @author   WPEKA <hello@wpeka.com>
 * @license  GPL v3
 * @link     https://club.wpeka.com
 */
namespace Page\Gdprfree;

/**
 * Core class for all the selectors used in automation testcases for dashboard page of GDPR cookie consent plugin
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
class GdprDashboardPage
{
    public $gdprCookieConset = '#toplevel_page_gdpr-cookie-consent > a > div.wp-menu-name';
    public $dashboard = '#toplevel_page_gdpr-cookie-consent > ul > li.wp-first-item > a'; 
    public $documentation = '#gdpr-cookie-consent-dashboard-page > div > div.card.gdpr-progress-bar-card > div > div:nth-child(1) > div.col-sm-6.gdpr-progress-bar-buttons.col > a:nth-child(1) > button';
    public $videoGuides = '#gdpr-cookie-consent-dashboard-page > div > div.card.gdpr-progress-bar-card > div > div:nth-child(1) > div.col-sm-6.gdpr-progress-bar-buttons.col > a:nth-child(2) > button';
    public $support = '#gdpr-cookie-consent-dashboard-page > div > div.card.gdpr-progress-bar-card > div > div:nth-child(1) > div.col-sm-6.gdpr-progress-bar-buttons.col > a:nth-child(4) > button';
    public $proFeature = '#gdpr-cookie-consent-dashboard-page > div > div.card.gdpr-progress-bar-card > div > div:nth-child(2) > div.col-sm-7.gdpr-progress-list-column.col > div:nth-child(2) > span:nth-child(2) > a';
    public $clickHere = '#gdpr-cookie-consent-dashboard-page > div > div.card.gdpr-progress-bar-card > div > div:nth-child(2) > div.col-sm-7.gdpr-progress-list-column.col > div:nth-child(4) > span:nth-child(2) > a';
    public $consentLog = '#gdpr-cookie-consent-dashboard-page > div > div.card.gdpr-dashboard-quick-links-card > div > div:nth-child(2) > span:nth-child(4) > button > img';
    public $scanCookie = '#gdpr-cookie-consent-dashboard-page > div > div.card.gdpr-dashboard-quick-links-card > div > div:nth-child(2) > span:nth-child(5) > button > img';
    public $geoTargeting = '#gdpr-cookie-consent-dashboard-page > div > div.card.gdpr-dashboard-quick-links-card > div > div:nth-child(2) > span:nth-child(6) > button > img';
    public $bannerTemplate = '#gdpr-cookie-consent-dashboard-page > div > div.card.gdpr-dashboard-quick-links-card > div > div:nth-child(2) > span:nth-child(7) > button > img';
    public $thirdPartyDetails = '#gdpr-cookie-consent-dashboard-page > div > div.card.gdpr-dashboard-quick-links-card > div > div:nth-child(2) > span:nth-child(8) > button > img';
    public $upgradeToPro = '#gdpr-cookie-consent-dashboard-page > div > div.card.gdpr-dashboard-quick-links-card > header > span:nth-child(2) > a:nth-child(1) > button';
    public $settings = '#gdpr-cookie-consent-dashboard-page > div > div.card.gdpr-dashboard-quick-links-card > div > div:nth-child(2) > span:nth-child(1) > a > img';
    public $designCookieBanner = '#gdpr-cookie-consent-dashboard-page > div > div.card.gdpr-dashboard-quick-links-card > div > div:nth-child(2) > span:nth-child(2) > a > img';
    public $scriptBlocker = '#gdpr-cookie-consent-dashboard-page > div > div.card.gdpr-dashboard-quick-links-card > div > div:nth-child(2) > span:nth-child(3) > a > img';
    public $activateLicenseKey = '#gdpr-cookie-consent-dashboard-page > div > div.gdpr-dashboard-promotional-cards > div:nth-child(1) > div > div:nth-child(1) > a';
    public $euCookieLaw = '#gdpr-cookie-consent-dashboard-page > div > div.gdpr-dashboard-promotional-cards > div:nth-child(1) > div > div:nth-child(2) > a';
    public $frequentlyAskedQuestions = '#gdpr-cookie-consent-dashboard-page > div > div.gdpr-dashboard-promotional-cards > div:nth-child(1) > div > div:nth-child(3) > a';
    public $CCPARegulations = '#gdpr-cookie-consent-dashboard-page > div > div.gdpr-dashboard-promotional-cards > div:nth-child(1) > div > div:nth-child(4) > a';
    public $WPLegalPagesInstall = '#gdpr-cookie-consent-dashboard-page > div > div.gdpr-dashboard-promotional-cards > div:nth-child(2) > div > div:nth-child(1) > div:nth-child(1) > a';
    public $WPAdcenterInstall = '#gdpr-cookie-consent-dashboard-page > div > div.gdpr-dashboard-promotional-cards > div:nth-child(2) > div > div:nth-child(1) > div:nth-child(2) > a';
    public $surveyFunnelInstall = '#gdpr-cookie-consent-dashboard-page > div > div.gdpr-dashboard-promotional-cards > div:nth-child(2) > div > div:nth-child(1) > div:nth-child(3) > a';
    public $viewAllPlugins = '#gdpr-cookie-consent-dashboard-page > div > div.gdpr-dashboard-promotional-cards > div:nth-child(2) > div > div.row.gdpr-dashboard-all-plugins-row > a > button';
}
