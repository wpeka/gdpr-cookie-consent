<?php
/**
 * Automation test cases for design settings tab of GDPR cookie consent plugin
 * 
 * @category AutomationTests
 * @package  WordPress_WPlegalpages_Pro_Plugin
 * @author   WPEKA <hello@wpeka.com>
 * @license  GPL v3
 * @link     https://club.wpeka.com
 */
use Facebook\WebDriver\WebDriverBy;
use Page\Acceptance\LoginPage;
use Page\Gdprfree\DesignSettings;

/**
 * Core class used for design settings tab of GDPR cookie consent plugin
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
class DesignSettingsCest
{
    /**
     * Test to check whether user is able to set the color of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function cookieBarColor(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->fillField($designSettings->cookieBarColor, $designSettings->cookieBarColorValue);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set the opacity of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function cookieBarOpacity(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->fillField($designSettings->cookieBarOpacity, $designSettings->cookieBarOpacityValue);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set the text color of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function cookieBarTextColor(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->fillField($designSettings->cookieBarTextColor, $designSettings->cookieBarTextColorValue);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set the border style,width,color and radius of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function cookieBarBorderSettings(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->borderStyle);
        $I->click($designSettings->borderStyleValue);
        $I->fillField($designSettings->borderWidth, $designSettings->borderWidthValue);
        $I->fillField($designSettings->borderColor, $designSettings->borderColorValue);
        $I->fillField($designSettings->borderRadius, $designSettings->borderRadiusValue);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

     /**
      * Test to check whether user is able to disable the accept button of cookie bar
      *
      * @param $I              variable of GdprfreeTester
      * @param $loginPage      Used to login and logout from the page.
      * @param $designSettings consist of selectors.
      * 
      * @return void
      * 
      * @since 1.0
      */
    public function disableAcceptButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->acceptButton);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to enable the accept button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function enableAcceptButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->acceptButton);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to change the text of accept button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function changeTextOfAcceptButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptBtn);
        $I->fillField($designSettings->textAcceptBtn, $designSettings->textAcceptBtnValue);
        $I->click($designSettings->done);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to change the text color of accept button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function changeTextColorOfAcceptButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptBtn);
        $I->fillField($designSettings->textColorAcceptBtn, $designSettings->textColorAcceptBtnValue);
        $I->click($designSettings->done);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to select between button or link for accept button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function showAsLinkForAcceptButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptBtn);
        $I->click($designSettings->showAsOptionForAcceptBtn);
        $I->click($designSettings->showAsOptionForAcceptBtnValue);
        $I->click($designSettings->done);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set the background color for accept button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function bgColorForAcceptButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptBtn);
        $I->click($designSettings->showAsOptionForAcceptBtn);
        $I->click($designSettings->showAsOptionForAcceptBtnValue2);
        $I->fillField($designSettings->bgColorAcceptBtn, $designSettings->bgColorAcceptBtnValue);
        $I->click($designSettings->done);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set the background opacity for accept button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function bgOpacityForAcceptButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptBtn);
        $I->fillField($designSettings->bgOpacityAcceptBtn, $designSettings->bgOpacityAcceptBtnValue);
        $I->click($designSettings->done);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set the border color, style, width and radius for accept button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function borderSettingsForAcceptButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptBtn);
        $I->click($designSettings->borderStyleAcceptBtn);
        $I->click($designSettings->borderStyleAcceptBtnValue);
        $I->fillField($designSettings->borderWidthAcceptBtn, $designSettings->borderWidthAcceptBtnValue);
        $I->fillField($designSettings->borderRadiusAcceptBtn, $designSettings->borderRadiusAcceptBtnValue);
        $I->fillField($designSettings->borderColorAcceptBtn, $designSettings->borderColorAcceptBtnValue);
        $I->click($designSettings->done);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set the button size for accept button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function btnSizeForAcceptButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptBtn);
        $I->click($designSettings->btnSizeAcceptBtn);
        $I->click($designSettings->btnSizeAcceptBtnValue);
        $I->click($designSettings->done);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }
    
    /**
     * Test to check whether user is able to select the close header option for accept button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function closeHeaderForAcceptButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptBtn);
        $I->wait(1);
        $I->click($designSettings->actionAcceptBtn);
        $I->click($designSettings->actionAcceptBtnValue);
        $I->click($designSettings->done);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        $I->click($designSettings->acceptBtn);
        
        $loginPage->userLogout($I);
    }
    
    /**
     * Test to check whether user is able to select the open in URL action for accept button of cookie bar(Link should open in new window)
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function openInUrlOnNewWindowForAcceptButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptBtn);
        $I->wait(1);
        $I->click($designSettings->actionAcceptBtn);
        $I->click($designSettings->actionAcceptBtnValue2);
        $I->click($designSettings->openUrlInNewWindow);
        $I->click($designSettings->openUrlInNewWindowValue);
        $I->fillField($designSettings->url, $designSettings->urlValue);
        $I->click($designSettings->done);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        $I->click($designSettings->acceptBtn);
        $I->switchToNextTab();
        $I->waitForText('Google offered in', 20);
        $I->switchToPreviousTab();
        
        $loginPage->userLogout($I);
    }
    
     /**
      * Test to check whether user is able to select the open in URL action for accept button of cookie bar(Link should open in current window)
      *
      * @param $I              variable of GdprfreeTester
      * @param $loginPage      Used to login and logout from the page.
      * @param $designSettings consist of selectors.
      * 
      * @return void
      * 
      * @since 1.0
      */
    public function openInUrlOnCurrentWindowForAcceptButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptBtn);
        $I->wait(1);
        $I->click($designSettings->actionAcceptBtn);
        $I->click($designSettings->actionAcceptBtnValue2);
        $I->click($designSettings->openUrlInNewWindow);
        $I->click($designSettings->openUrlInNewWindowValue2);
        $I->fillField($designSettings->url, $designSettings->urlValue);
        $I->click($designSettings->done);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        $I->click($designSettings->acceptBtn);
        $I->waitForText('Google offered in', 20);
        $I->amOnPage('/');

        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to disable the acceptall button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function disableAcceptAllButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->acceptAllButton);
        $I->wait(1);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to enable the acceptall button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function enableAcceptAllButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->acceptAllButton);
        $I->wait(1);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to change the text of acceptall button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function changeTextOfAcceptAllButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptAllBtn);
        $I->wait(1);
        $I->fillField($designSettings->textAcceptAllBtn, $designSettings->textAcceptAllBtnValue);
        $I->click($designSettings->done2);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

     /**
      * Test to check whether user is able to change the text color of accept all button of cookie bar
      *
      * @param $I              variable of GdprfreeTester
      * @param $loginPage      Used to login and logout from the page.
      * @param $designSettings consist of selectors.
      * 
      * @return void
      * 
      * @since 1.0
      */
    public function changeTextColorOfAcceptAllButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptAllBtn);
        $I->fillField($designSettings->textColorAcceptAllBtn, $designSettings->textColorAcceptAllBtnValue);
        $I->click($designSettings->done2);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to select between button or link for acceptall button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function showAsLinkForAcceptAllButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptAllBtn);
        $I->click($designSettings->showAsOptionForAcceptAllBtn);
        $I->click($designSettings->showAsOptionForAcceptAllBtnValue);
        $I->click($designSettings->done2);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set the background color for accept all button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function bgColorForAcceptAllButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptAllBtn);
        $I->wait(1);
        $I->click($designSettings->showAsOptionForAcceptAllBtn);
        $I->click($designSettings->showAsOptionForAcceptAllBtnValue2);
        $I->fillField($designSettings->bgColorAcceptAllBtn, $designSettings->bgColorAcceptAllBtnValue);
        $I->click($designSettings->done2);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set the background opacity for acceptall button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function bgOpacityForAcceptAllButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptAllBtn);
        $I->fillField($designSettings->bgOpacityAcceptAllBtn, $designSettings->bgOpacityAcceptAllBtnValue);
        $I->click($designSettings->done2);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set the border color, style, width and radius for accept all button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function borderSettingsForAcceptAllButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptAllBtn);
        $I->click($designSettings->borderStyleAcceptAllBtn);
        $I->click($designSettings->borderStyleAcceptAllBtnValue);
        $I->fillField($designSettings->borderWidthAcceptAllBtn, $designSettings->borderWidthAcceptAllBtnValue);
        $I->fillField($designSettings->borderRadiusAcceptAllBtn, $designSettings->borderRadiusAcceptAllBtnValue);
        $I->fillField($designSettings->borderColorAcceptAllBtn, $designSettings->borderColorAcceptAllBtnValue);
        $I->click($designSettings->done2);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set the button size for acceptall button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function btnSizeForAcceptAllButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptAllBtn);
        $I->click($designSettings->btnSizeAcceptAllBtn);
        $I->waitForElement($designSettings->btnSizeAcceptAllBtnValue);
        $I->click($designSettings->btnSizeAcceptAllBtnValue);
        $I->click($designSettings->done2);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }
    
    /**
     * Test to check whether user is able to select the close header action for acceptall button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function closeHeaderForAcceptAllButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptAllBtn);
        $I->wait(1);
        $I->click($designSettings->actionAcceptAllBtn);
        $I->click($designSettings->actionAcceptAllBtnValue);
        $I->click($designSettings->done2);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        $I->click($designSettings->acceptAllBtn);
        
        $loginPage->userLogout($I);
    }
    
    /**
     * Test to check whether user is able to select the open in URL action for acceptall button of cookie bar(Link should open in new window)
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function openInUrlOnNewWindowForAcceptAllButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptAllBtn);
        $I->wait(1);
        $I->click($designSettings->actionAcceptAllBtn);
        $I->click($designSettings->actionAcceptAllBtnValue2);
        $I->click($designSettings->openUrlInNewWindowForAcceptAllBtn);
        $I->click($designSettings->openUrlInNewWindowForAcceptAllBtnValue);
        $I->fillField($designSettings->urlforAcceptAllButton, $designSettings->urlforAcceptAllButtonValue);
        $I->click($designSettings->done2);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        $I->click($designSettings->acceptAllBtn);
        $I->switchToNextTab();
        $I->waitForText('Google offered in', 20);
        $I->switchToPreviousTab();
        
        $loginPage->userLogout($I);
    }
    
     /**
      * Test to check whether user is able to select the open in URL action for acceptall button of cookie bar(Link should open in current window)
      *
      * @param $I              variable of GdprfreeTester
      * @param $loginPage      Used to login and logout from the page.
      * @param $designSettings consist of selectors.
      * 
      * @return void
      * 
      * @since 1.0
      */
    public function openInUrlOnCurrentWindowForAcceptAllButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureAcceptAllBtn);
        $I->wait(1);
        $I->click($designSettings->actionAcceptAllBtn);
        $I->click($designSettings->actionAcceptAllBtnValue2);
        $I->click($designSettings->openUrlInNewWindowForAcceptAllBtn);
        $I->click($designSettings->openUrlInNewWindowForAcceptAllBtnValue2);
        $I->fillField($designSettings->urlforAcceptAllButton, $designSettings->urlforAcceptAllButtonValue);
        $I->click($designSettings->done2);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        $I->click($designSettings->acceptBtn);
        $I->waitForText('Google offered in', 20);
        $I->amOnPage('/');

        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to disable the decline button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function disableDeclineButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->declineButton);
        $I->wait(1);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to enable the decline button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function enableDeclineButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->declineButton);
        $I->wait(1);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to change the text of decline button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function changeTextOfDeclineButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureDeclineBtn);
        $I->wait(1);
        $I->fillField($designSettings->textDeclineBtn, $designSettings->textDeclineBtnValue);
        $I->click($designSettings->done3);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

     /**
      * Test to check whether user is able to change the text color of decline button of cookie bar
      *
      * @param $I              variable of GdprfreeTester
      * @param $loginPage      Used to login and logout from the page.
      * @param $designSettings consist of selectors.
      * 
      * @return void
      * 
      * @since 1.0
      */
    public function changeTextColorOfDeclineButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureDeclineBtn);
        $I->fillField($designSettings->textColorDeclineBtn, $designSettings->textColorDeclineBtnValue);
        $I->click($designSettings->done3);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to select between button or link for decline button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function showAsLinkForDeclineButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureDeclineBtn);
        $I->click($designSettings->showAsOptionForDeclineBtn);
        $I->click($designSettings->showAsOptionForDeclineBtnValue);
        $I->click($designSettings->done3);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set the background color for decline button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function bgColorForDeclineButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureDeclineBtn);
        $I->wait(1);
        $I->click($designSettings->showAsOptionForDeclineBtn);
        $I->click($designSettings->showAsOptionForDeclineBtnValue2);
        $I->fillField($designSettings->bgColorDeclineBtn, $designSettings->bgColorDeclineBtnValue);
        $I->click($designSettings->done3);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set the background opacity for decline button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function bgOpacityForDeclineButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureDeclineBtn);
        $I->fillField($designSettings->bgOpacityDeclineBtn, $designSettings->bgOpacityDeclineBtnValue);
        $I->click($designSettings->done3);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set the border color, style, width and radius for decline button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function borderSettingsForDeclineButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureDeclineBtn);
        $I->click($designSettings->borderStyleDeclineBtn);
        $I->click($designSettings->borderStyleDeclineBtnValue);
        $I->fillField($designSettings->borderWidthDeclineBtn, $designSettings->borderWidthDeclineBtnValue);
        $I->fillField($designSettings->borderRadiusDeclineBtn, $designSettings->borderRadiusDeclineBtnValue);
        $I->fillField($designSettings->borderColorDeclineBtn, $designSettings->borderColorDeclineBtnValue);
        $I->click($designSettings->done3);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set the button size for decline button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function btnSizeForDeclineButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureDeclineBtn);
        $I->click($designSettings->btnSizeDeclineBtn);
        $I->waitForElement($designSettings->btnSizeDeclineBtnValue);
        $I->click($designSettings->btnSizeDeclineBtnValue);
        $I->click($designSettings->done3);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }
    
    /**
     * Test to check whether user is able to select the close header action for decline button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function closeHeaderForDeclineButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureDeclineBtn);
        $I->wait(1);
        $I->click($designSettings->actionDeclineBtn);
        $I->click($designSettings->actionDeclineBtnValue);
        $I->click($designSettings->done3);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        $I->click($designSettings->declineBtn);
        
        $loginPage->userLogout($I);
    }
    
    /**
     * Test to check whether user is able to select the open in URL action for decline button of cookie bar(Link should open in new window)
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function openInUrlOnNewWindowForDeclineButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureDeclineBtn);
        $I->wait(1);
        $I->click($designSettings->actionDeclineBtn);
        $I->click($designSettings->actionDeclineBtnValue2);
        $I->click($designSettings->openUrlInNewWindowForDeclineBtn);
        $I->click($designSettings->openUrlInNewWindowForDeclineBtnValue);
        $I->fillField($designSettings->urlforDeclineButton, $designSettings->urlforDeclineButtonValue);
        $I->click($designSettings->done3);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        $I->click($designSettings->declineBtn);
        $I->switchToNextTab();
        $I->waitForText('Google offered in', 20);
        $I->switchToPreviousTab();
        
        $loginPage->userLogout($I);
    }
    
     /**
      * Test to check whether user is able to select the open in URL action for decline button of cookie bar(Link should open in current window)
      *
      * @param $I              variable of GdprfreeTester
      * @param $loginPage      Used to login and logout from the page.
      * @param $designSettings consist of selectors.
      * 
      * @return void
      * 
      * @since 1.0
      */
    public function openInUrlOnCurrentWindowForDeclineButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureDeclineBtn);
        $I->wait(1);
        $I->click($designSettings->actionDeclineBtn);
        $I->click($designSettings->actionDeclineBtnValue2);
        $I->click($designSettings->openUrlInNewWindowForDeclineBtn);
        $I->click($designSettings->openUrlInNewWindowForDeclineBtnValue2);
        $I->fillField($designSettings->urlforDeclineButton, $designSettings->urlforDeclineButtonValue);
        $I->click($designSettings->done3);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        $I->click($designSettings->acceptBtn);
        $I->waitForText('Google offered in', 20);
        $I->amOnPage('/');

        $loginPage->userLogout($I);
    }

     /**
      * Test to check whether user is able to disable the settings button of cookie bar
      *
      * @param $I              variable of GdprfreeTester
      * @param $loginPage      Used to login and logout from the page.
      * @param $designSettings consist of selectors.
      * 
      * @return void
      * 
      * @since 1.0
      */
    public function disableSettingsButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->settingsButton);
        $I->wait(1);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to enable the settings button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function enableSettingsButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->settingsButton);
        $I->wait(1);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to change the text of settings button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function changeTextOfSettingsButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureSettingsBtn);
        $I->wait(1);
        $I->fillField($designSettings->textSettingsBtn, $designSettings->textSettingsBtnValue);
        $I->click($designSettings->done4);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

     /**
      * Test to check whether user is able to change the text color of settings button of cookie bar
      *
      * @param $I              variable of GdprfreeTester
      * @param $loginPage      Used to login and logout from the page.
      * @param $designSettings consist of selectors.
      * 
      * @return void
      * 
      * @since 1.0
      */
    public function changeTextColorOfSettingsButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureSettingsBtn);
        $I->fillField($designSettings->textColorSettingsBtn, $designSettings->textColorSettingsBtnValue);
        $I->click($designSettings->done4);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to select between button or link for settings button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function showAsLinkForSettingsButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureSettingsBtn);
        $I->click($designSettings->showAsOptionForSettingsBtn);
        $I->click($designSettings->showAsOptionForSettingsBtnValue);
        $I->click($designSettings->done4);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set the background color for settings button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function bgColorForSettingsButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureSettingsBtn);
        $I->wait(1);
        $I->click($designSettings->showAsOptionForSettingsBtn);
        $I->click($designSettings->showAsOptionForSettingsBtnValue2);
        $I->fillField($designSettings->bgColorSettingsBtn, $designSettings->bgColorSettingsBtnValue);
        $I->click($designSettings->done4);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set the background opacity for settings button of cookie bar
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $designSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function bgOpacityForSettingsButton(GdprfreeTester $I, LoginPage $loginPage, DesignSettings $designSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($designSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($designSettings->gdprCookieConsent);
        $I->click($designSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($designSettings->design);
        $I->waitForText('Cookie Bar Body Design', 20);
        $I->click($designSettings->configureSettingsBtn);
        $I->fillField($designSettings->bgOpacitySettingsBtn, $designSettings->bgOpacitySettingsBtnValue);
        $I->click($designSettings->done4);
        $I->scrollTo($designSettings->saveChanges);
        $I->click($designSettings->saveChanges);
        $I->wait(2);
        $I->click($designSettings->GDPR);
        $I->waitForText('This website uses cookies to improve your experience.', 20);
        $I->see('This website uses cookies to improve your experience.');
        
        $loginPage->userLogout($I);
    }
}
