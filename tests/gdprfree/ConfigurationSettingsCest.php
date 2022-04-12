<?php
/**
 * Automation test cases for configuration settings tab of GDPR cookie consent plugin
 * 
 * @category AutomationTests
 * @package  WordPress_WPlegalpages_Pro_Plugin
 * @author   WPEKA <hello@wpeka.com>
 * @license  GPL v3
 * @link     https://club.wpeka.com
 */
use Facebook\WebDriver\WebDriverBy;
use Page\Acceptance\LoginPage;
use Page\Gdprfree\ConfigurationSettings;

/**
 * Core class used for configuration settings tab of GDPR cookie consent plugin
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
class ConfigurationSettingsCest
{
    /**
     * Test to check whether user is able to see options to display cookie bar
     *
     * @param $I                     variable of GdprfreeTester
     * @param $loginPage             Used to login and logout from the page.
     * @param $configurationSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function showCookieNoticeAs(GdprfreeTester $I, LoginPage $loginPage, configurationSettings $configurationSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($configurationSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($configurationSettings->gdprCookieConsent);
        $I->click($configurationSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($configurationSettings->configuration);
        $I->waitForText('Configure Cookie Bar', 20);
        $I->click($configurationSettings->showCookieNoticeAs);
        $I->waitForElement($configurationSettings->banner, 20);
        $I->click($configurationSettings->banner);
        $I->click($configurationSettings->saveChanges);
        $I->wait(2);
        $I->click($configurationSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        $I->wait(2);
        $I->click($configurationSettings->accept);
        $I->wait(2);
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to set the position of the cookie when user selects the banner option(if user selects position to top)
     *
     * @param $I                     variable of GdprfreeTester
     * @param $loginPage             Used to login and logout from the page.
     * @param $configurationSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function bannerPositionTop(GdprfreeTester $I, LoginPage $loginPage, configurationSettings $configurationSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($configurationSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($configurationSettings->gdprCookieConsent);
        $I->click($configurationSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($configurationSettings->configuration);
        $I->waitForText('Configure Cookie Bar', 20);
        $I->click($configurationSettings->showCookieNoticeAs);
        $I->waitForElement($configurationSettings->banner, 20);
        $I->click($configurationSettings->banner);
        $I->click($configurationSettings->position);
        $I->waitForElement($configurationSettings->top, 20);
        $I->click($configurationSettings->top);
        $I->click($configurationSettings->saveChanges);
        $I->wait(2);
        $I->click($configurationSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        $I->wait(2);
        $I->click($configurationSettings->accept);
        $I->wait(2);

        $loginPage->userLogout($I);
    }

    /**
     * Test to set the position of the cookie when user selects the banner option(if user selects postion to bottom)
     *
     * @param $I                     variable of GdprfreeTester
     * @param $loginPage             Used to login and logout from the page.
     * @param $configurationSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function bannerPositionBottom(GdprfreeTester $I, LoginPage $loginPage, configurationSettings $configurationSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($configurationSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($configurationSettings->gdprCookieConsent);
        $I->click($configurationSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($configurationSettings->configuration);
        $I->waitForText('Configure Cookie Bar', 20);
        $I->click($configurationSettings->showCookieNoticeAs);
        $I->waitForElement($configurationSettings->banner, 20);
        $I->click($configurationSettings->banner);
        $I->click($configurationSettings->position);
        $I->waitForElement($configurationSettings->bottom, 20);
        $I->click($configurationSettings->bottom);
        $I->click($configurationSettings->saveChanges);
        $I->wait(2);
        $I->click($configurationSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

      /**
       * Test to set the position of the cookie when user selects the widget option(if user selects position to right)
       *
       * @param $I                     variable of GdprfreeTester
       * @param $loginPage             Used to login and logout from the page.
       * @param $configurationSettings consist of selectors.
       * 
       * @return void
       * 
       * @since 1.0
       */
    public function widgetPositionToRight(GdprfreeTester $I, LoginPage $loginPage, configurationSettings $configurationSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($configurationSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($configurationSettings->gdprCookieConsent);
        $I->click($configurationSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($configurationSettings->configuration);
        $I->waitForText('Configure Cookie Bar', 20);
        $I->click($configurationSettings->showCookieNoticeAs);
        $I->waitForElement($configurationSettings->widget, 20);
        $I->click($configurationSettings->widget);
        $I->click($configurationSettings->widgetPosition);
        $I->waitForElement($configurationSettings->right, 20);
        $I->click($configurationSettings->right);
        $I->click($configurationSettings->saveChanges);
        $I->wait(2);
        $I->click($configurationSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to set the position of the cookie when user selects the widget option(if user selects position to left)
     *
     * @param $I                     variable of GdprfreeTester
     * @param $loginPage             Used to login and logout from the page.
     * @param $configurationSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function widgetPositionToLeft(GdprfreeTester $I, LoginPage $loginPage, configurationSettings $configurationSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($configurationSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($configurationSettings->gdprCookieConsent);
        $I->click($configurationSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($configurationSettings->configuration);
        $I->waitForText('Configure Cookie Bar', 20);
        $I->click($configurationSettings->showCookieNoticeAs);
        $I->waitForElement($configurationSettings->widget, 20);
        $I->click($configurationSettings->widget);
        $I->click($configurationSettings->widgetPosition);
        $I->waitForElement($configurationSettings->left, 20);
        $I->click($configurationSettings->left);
        $I->click($configurationSettings->saveChanges);
        $I->wait(2);
        $I->click($configurationSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to disable add overlay for the cookie when user selects the popup option
     *
     * @param $I                     variable of GdprfreeTester
     * @param $loginPage             Used to login and logout from the page.
     * @param $configurationSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function popupDisableAddOverlay(GdprfreeTester $I, LoginPage $loginPage, configurationSettings $configurationSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($configurationSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($configurationSettings->gdprCookieConsent);
        $I->click($configurationSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($configurationSettings->configuration);
        $I->waitForText('Configure Cookie Bar', 20);
        $I->click($configurationSettings->showCookieNoticeAs);
        $I->waitForElement($configurationSettings->popup, 20);
        $I->click($configurationSettings->popup);
        $I->click($configurationSettings->addOverlay);
        $I->click($configurationSettings->saveChanges);
        $I->wait(2);
        $I->click($configurationSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to enable add overlay for the cookie when user selects the popup option
     *
     * @param $I                     variable of GdprfreeTester
     * @param $loginPage             Used to login and logout from the page.
     * @param $configurationSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function popupEnableAddOverlay(GdprfreeTester $I, LoginPage $loginPage, configurationSettings $configurationSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($configurationSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($configurationSettings->gdprCookieConsent);
        $I->click($configurationSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($configurationSettings->configuration);
        $I->waitForText('Configure Cookie Bar', 20);
        $I->click($configurationSettings->showCookieNoticeAs);
        $I->waitForElement($configurationSettings->popup, 20);
        $I->click($configurationSettings->popup);
        $I->click($configurationSettings->addOverlay);
        $I->click($configurationSettings->saveChanges);
        $I->wait(2);
        $I->click($configurationSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }
}
