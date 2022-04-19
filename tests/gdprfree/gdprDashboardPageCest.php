<?php
/**
 * Automation test cases for dashboard page of GDPR cookie consent plugin
 * 
 * @category AutomationTests
 * @package  WordPress_WPlegalpages_Pro_Plugin
 * @author   WPEKA <hello@wpeka.com>
 * @license  GPL v3
 * @link     https://club.wpeka.com
 */
use Facebook\WebDriver\WebDriverBy;
use Page\Acceptance\LoginPage;
use Page\Gdprfree\gdprDashboardPage;

/**
 * Core class used for dashboard page of GDPR cookie consent plugin
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
class GdprDashboardPageCest
{
    /**
     * Test to check UI of the Dashboard page
     *
     * @param $I                 variable of GdprfreeTester
     * @param $loginPage         Used to login and logout from the page.
     * @param $gdprDashboardPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function checkUIOfDashboard(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(2);

        $loginPage->userLogout($I);
    }

    /**
     * Test to check working of 'Documentation' link/button
     *
     * @param $I                 variable of GdprfreeTester
     * @param $loginPage         Used to login and logout from the page.
     * @param $gdprDashboardPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function checkDocumentationButton(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(2);
        $I->click($gdprDashboardPage->documentation);
        $I->wait(2);
        $I->switchToNextTab();
        $I->waitForText('Overview of the Plugin', 20);
        $I->wait(2);
        $I->switchToPreviousTab(); 

        $loginPage->userLogout($I);
    }
    
    /**
     * Test to check working of 'Video guides' link/button
     *
     * @param $I                 variable of GdprfreeTester
     * @param $loginPage         Used to login and logout from the page.
     * @param $gdprDashboardPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function checkVideoGuidesButton(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(2);
        $I->click($gdprDashboardPage->videoGuides);
        $I->wait(2);
        $I->switchToNextTab();
        $I->waitForText('GDPR Cookie Consent Plugin - Tutorial Videos', 20);
        $I->wait(2);
        $I->switchToPreviousTab(); 

        $loginPage->userLogout($I);
    }

    /**
     * Test to check working of 'Support' link/button
     *
     * @param $I                 variable of GdprfreeTester
     * @param $loginPage         Used to login and logout from the page.
     * @param $gdprDashboardPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function checkSupportButton(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(1);
        $I->click($gdprDashboardPage->support);
        $I->wait(2);
        $I->switchToNextTab();
        $I->waitForText('Support', 20);
        $I->wait(2);
        $I->switchToPreviousTab(); 

        $loginPage->userLogout($I);
    }

     /**
      * Test to check working of 'Pro Feature' links
      *
      * @param $I                 variable of GdprfreeTester
      * @param $loginPage         Used to login and logout from the page.
      * @param $gdprDashboardPage consist of selectors.
      * 
      * @return void
      * 
      * @since 1.0
      */
    public function checkProFeatureLinks(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(2);
        $I->click($gdprDashboardPage->proFeature);
        $I->wait(2);
        $I->switchToNextTab();
        $I->waitForText('WordPress Cookie Consent Plugin for GDPR & CCPA', 20);
        $I->wait(2);
        $I->switchToPreviousTab(); 

        $loginPage->userLogout($I);
    }

    /**
     * Test to check working of 'Click here' link
     *
     * @param $I                 variable of GdprfreeTester
     * @param $loginPage         Used to login and logout from the page.
     * @param $gdprDashboardPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function checkClickHereLink(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(2);
        $I->click($gdprDashboardPage->clickHere);
        $I->wait(2);
        $I->switchToNextTab();
        $I->waitForText('Helps you comply with the EU GDPR’s cookie consent and CCPA’s “Do Not Sell” Opt-Out regulations.', 20);
        $I->wait(2);
        $I->switchToPreviousTab(); 

        $loginPage->userLogout($I);
    }

    /**
     * Test to check working of 'Upgrade to Pro' button
     *
     * @param $I                 variable of GdprfreeTester
     * @param $loginPage         Used to login and logout from the page.
     * @param $gdprDashboardPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function checkUpgradeToProButton(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(2);
        $I->moveMouseOver($gdprDashboardPage->consentLog);
        $I->wait(2);
        $I->moveMouseOver($gdprDashboardPage->scanCookie);
        $I->wait(2);
        $I->moveMouseOver($gdprDashboardPage->geoTargeting);
        $I->wait(2);
        $I->moveMouseOver($gdprDashboardPage->bannerTemplate);
        $I->wait(2);
        $I->moveMouseOver($gdprDashboardPage->thirdPartyDetails);
        $I->wait(2);
        $I->click($gdprDashboardPage->upgradeToPro);
        $I->wait(2);
        $I->switchToNextTab();
        $I->waitForText('WordPress Cookie Consent Plugin for GDPR & CCPA', 20);
        $I->wait(2);
        $I->switchToPreviousTab(); 

        $loginPage->userLogout($I);
    }

    /**
     * Test to check working of quick link Settings
     *
     * @param $I                 variable of GdprfreeTester
     * @param $loginPage         Used to login and logout from the page.
     * @param $gdprDashboardPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function checkQuickLinkSettings(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(2);
        $I->click($gdprDashboardPage->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->wait(2);
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check working of quick link Design cookie banner
     *
     * @param $I                 variable of GdprfreeTester
     * @param $loginPage         Used to login and logout from the page.
     * @param $gdprDashboardPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function checkQuickLinkDesignCookieBanner(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(2);
        $I->click($gdprDashboardPage->designCookieBanner);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->wait(2);
        
        $loginPage->userLogout($I);
    }

     /**
      * Test to check working of quick link Script blocker
      *
      * @param $I                 variable of GdprfreeTester
      * @param $loginPage         Used to login and logout from the page.
      * @param $gdprDashboardPage consist of selectors.
      * 
      * @return void
      * 
      * @since 1.0
      */
    public function checkQuickLinkScriptBlocker(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(2);
        $I->click($gdprDashboardPage->scriptBlocker);
        $I->waitForText('Script Blocker Settings', 20);
        $I->wait(2);
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check working of link 'How to activate your License Key?'
     *
     * @param $I                 variable of GdprfreeTester
     * @param $loginPage         Used to login and logout from the page.
     * @param $gdprDashboardPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function checkLinkHowToActivateLicenseKey(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(2);
         $I->scrollTo($gdprDashboardPage->activateLicenseKey);
        $I->wait(2);
        $I->click($gdprDashboardPage->activateLicenseKey);
        $I->wait(2);
        $I->switchToNextTab();
        $I->waitForText('How To Activate The License Key of WPeka Plugins', 20);
        $I->wait(2);
        $I->switchToPreviousTab();
        
        $loginPage->userLogout($I);
    }
   
    /**
     * Test to check working of link 'What you need to know about the EU Cookie law?'
     *
     * @param $I                 variable of GdprfreeTester
     * @param $loginPage         Used to login and logout from the page.
     * @param $gdprDashboardPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function checkLinkEUCookieLaw(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(2);
        $I->scrollTo($gdprDashboardPage->euCookieLaw);
        $I->wait(2);
        $I->click($gdprDashboardPage->euCookieLaw);
        $I->wait(3);
        $I->switchToNextTab();
        $I->waitForText('What you need to know about the EU Cookie Law ?', 20);
        $I->wait(2);
        $I->switchToPreviousTab();
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check working of link 'Frequently asked questions'
     *
     * @param $I                 variable of GdprfreeTester
     * @param $loginPage         Used to login and logout from the page.
     * @param $gdprDashboardPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function checkLinkFrequentlyAskedQuestions(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(2);
        $I->scrollTo($gdprDashboardPage->frequentlyAskedQuestions);
        $I->wait(2);
        $I->click($gdprDashboardPage->frequentlyAskedQuestions);
        $I->wait(3);
        $I->switchToNextTab();
        $I->waitForText('Easily set up a cookie consent banner', 20);
        $I->wait(2);
        $I->switchToPreviousTab();
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check working of link 'What are the CCPA regulations and how we can comply?'
     *
     * @param $I                 variable of GdprfreeTester
     * @param $loginPage         Used to login and logout from the page.
     * @param $gdprDashboardPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function checkLinkCCPARegulations(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(2);
        $I->scrollTo($gdprDashboardPage->CCPARegulations);
        $I->wait(2);
        $I->click($gdprDashboardPage->CCPARegulations);
        $I->wait(3);
        $I->switchToNextTab();
        $I->waitForText('California Consumer Privacy Act: Become CCPA compliant today', 20);
        $I->wait(2);
        $I->switchToPreviousTab();
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check working of link 'WPlegalpages install link'
     *
     * @param $I                 variable of GdprfreeTester
     * @param $loginPage         Used to login and logout from the page.
     * @param $gdprDashboardPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function checkWPLegalPagesInstallLink(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(2);
        $I->scrollTo($gdprDashboardPage->WPLegalPagesInstall);
        $I->wait(2);
        $I->click($gdprDashboardPage->WPLegalPagesInstall);
        $I->wait(3);
        $I->switchToNextTab();
        $I->waitForText('Privacy Policy Generator, Terms & Conditions Generator WordPress Plugin : WPLegalPages', 20);
        $I->wait(2);
        $I->switchToPreviousTab();
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check working of link 'WPAdcenter install link'
     *
     * @param $I                 variable of GdprfreeTester
     * @param $loginPage         Used to login and logout from the page.
     * @param $gdprDashboardPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function checkWPAdcenterInstallLink(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(2);
        $I->scrollTo($gdprDashboardPage->WPAdcenterInstall);
        $I->wait(2);
        $I->click($gdprDashboardPage->WPAdcenterInstall);
        $I->wait(3);
        $I->switchToNextTab();
        $I->waitForText('WP AdCenter – Ad Manager & Adsense Ads', 20);
        $I->wait(2);
        $I->switchToPreviousTab();
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check working of link 'Surveyfunnel install link'
     *
     * @param $I                 variable of GdprfreeTester
     * @param $loginPage         Used to login and logout from the page.
     * @param $gdprDashboardPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function checkSurveyFunnelInstallLink(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(2);
        $I->scrollTo($gdprDashboardPage->surveyFunnelInstall);
        $I->wait(2);
        $I->click($gdprDashboardPage->surveyFunnelInstall);
        $I->wait(3);
        $I->switchToNextTab();
        $I->waitForText('SurveyFunnel – Survey Plugin for WordPress', 20);
        $I->wait(2);
        $I->switchToPreviousTab();
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check working of link 'View all Plugins' button
     *
     * @param $I                 variable of GdprfreeTester
     * @param $loginPage         Used to login and logout from the page.
     * @param $gdprDashboardPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function checkViewAllPluginsButton(GdprfreeTester $I, LoginPage $loginPage, gdprDashboardPage $gdprDashboardPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($gdprDashboardPage->gdprCookieConset, 20);
        $I->moveMouseOver($gdprDashboardPage->gdprCookieConset);
        $I->click($gdprDashboardPage->dashboard);
        $I->waitForText('Your Progress', 20);
        $I->see('Your Progress');
        $I->wait(2);
        $I->scrollTo($gdprDashboardPage->viewAllPlugins);
        $I->wait(2);
        $I->click($gdprDashboardPage->viewAllPlugins);
        $I->wait(3);
        $I->switchToNextTab();
        $I->waitForText('WPeka is home to a diverse set of plugins.', 20);
        $I->wait(2);
        $I->switchToPreviousTab();
        
        $loginPage->userLogout($I);
    }
}
