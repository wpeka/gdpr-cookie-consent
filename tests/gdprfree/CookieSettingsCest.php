<?php
/**
 * Automation test cases for cookie list and script blocker tab of GDPR cookie consent plugin
 * 
 * @category AutomationTests
 * @package  WordPress_WPlegalpages_Pro_Plugin
 * @author   WPEKA <hello@wpeka.com>
 * @license  GPL v3
 * @link     https://club.wpeka.com
 */
use Facebook\WebDriver\WebDriverBy;
use Page\Acceptance\LoginPage;
use Page\Gdprfree\CookieSettings;

/**
 * Core class used for cookie list and script blocker tab of GDPR cookie consent plugin
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
class CookieSettingsCest
{
    /**
     * Test to check whether user is able to add cookie
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $cookieSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function addCookie(GdprfreeTester $I, LoginPage $loginPage, CookieSettings $cookieSettings)
    { 
        $loginPage->userLogin($I);

        $I->waitForElement($cookieSettings->gdprCookieConset, 20);
        $I->moveMouseOver($cookieSettings->gdprCookieConset);
        $I->click($cookieSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($cookieSettings->cookieList);
        $I->waitForText('Custom Cookies', 20);
        $I->fillField(['name' => 'gdpr-cookie-consent-custom-cookie-name'], $cookieSettings->cookieNameValue);
        $I->fillField(['name' => 'gdpr-cookie-consent-custom-cookie-domain'], $cookieSettings->cookieDomainValue);
        $I->fillField(['name' => 'gdpr-cookie-consent-custom-cookie-days'], $cookieSettings->cookieDaysValue);
        $I->click($cookieSettings->cookieType);
        $I->wait(2);
        $I->click($cookieSettings->cookieTypeValue);
        $I->wait(2);
        $I->click($cookieSettings->cookieStorage);
        $I->wait(1);
        $I->click($cookieSettings->cookieStorageValue);
        $I->fillField(['name' => 'gdpr-cookie-consent-custom-cookie-purpose'], $cookieSettings->cookiePurposeValue);
        $I->click($cookieSettings->save);
        $I->wait(2);
        $I->click($cookieSettings->GDPR);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        $I->click($cookieSettings->cookieSettingsButton2);
        $I->wait(3);
        $I->click($cookieSettings->analytics);
        $I->waitForText('Analytics cookies help website owners to understand how visitors interact with websites by collecting and reporting information anonymously.', 20);

        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to edit cookie
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $cookieSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function editCookie(GdprfreeTester $I, LoginPage $loginPage, CookieSettings $cookieSettings)
    { 
        $loginPage->userLogin($I);

        $I->waitForElement($cookieSettings->gdprCookieConset, 20);
        $I->moveMouseOver($cookieSettings->gdprCookieConset);
        $I->click($cookieSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($cookieSettings->cookieList);
        $I->waitForText('Custom Cookies', 20);
        $I->fillField($cookieSettings->editedCookieName, $cookieSettings->cookieNameEditedValue);
        $I->wait(1);
        $I->fillField($cookieSettings->editedCookieDomain, $cookieSettings->editedCookieDomainValue);
        $I->fillField($cookieSettings->editedCookieDays, $cookieSettings->cookieDaysEditedValue);
        $I->click($cookieSettings->cookieEditedType);
        $I->wait(2);
        $I->click($cookieSettings->cookieTypeEditedValue);
        $I->wait(2);
        $I->click($cookieSettings->cookieEditedStorage);
        $I->wait(1);
        $I->click($cookieSettings->cookieEditedStorageValue);
        $I->fillField($cookieSettings->editedCookiePurpose, $cookieSettings->editedCookiePurposeValue);
        $I->click($cookieSettings->saveAllChanges);
        $I->wait(2);
        $I->click($cookieSettings->GDPR);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        $I->click($cookieSettings->cookieSettingsButton2);
        $I->wait(3);
        $I->click($cookieSettings->preferences);
        $I->waitForText('Preference cookies enable a website to remember information that changes the way the website', 20);

        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to delete cookie
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $cookieSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function deleteCookie(GdprfreeTester $I, LoginPage $loginPage, CookieSettings $cookieSettings)
    { 
        $loginPage->userLogin($I);

        $I->waitForElement($cookieSettings->gdprCookieConset, 20);
        $I->moveMouseOver($cookieSettings->gdprCookieConset);
        $I->click($cookieSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($cookieSettings->cookieList);
        $I->waitForText('Custom Cookies', 20);
        $I->click($cookieSettings->delete);
        $I->wait(2);
        $I->click($cookieSettings->GDPR);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        $I->click($cookieSettings->cookieSettingsButton2);
        $I->wait(3);
        $I->click($cookieSettings->analytics);
        $I->waitForText('We do not use cookies of this type', 20);

        $loginPage->userLogout($I);
    } 

    /**
     * Test to check whether user is able to add header script
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $cookieSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function headerScript(GdprfreeTester $I, LoginPage $loginPage, CookieSettings $cookieSettings)
    { 
        $loginPage->userLogin($I);

        $I->waitForElement($cookieSettings->gdprCookieConset, 20);
        $I->moveMouseOver($cookieSettings->gdprCookieConset);
        $I->click($cookieSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($cookieSettings->scriptBlocker);
        $I->waitForText('Script Blocker Settings', 20);
        $I->click($cookieSettings->enableButton);
        $I->fillField(['name' => 'gcc-header-scripts'], $cookieSettings->headerScriptValue);
        $I->click($cookieSettings->saveChanges);
        $I->wait(2);
        $I->click($cookieSettings->GDPR);
        $I->click($cookieSettings->accept);
        $I->reloadPage();
        $I->wait(3);
        $I->acceptPopup();
        $I->wait(2);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        
        $loginPage->userLogout($I);
    } 

     /**
      * Test to check whether user is able to add body script
      *
      * @param $I              variable of GdprfreeTester
      * @param $loginPage      Used to login and logout from the page.
      * @param $cookieSettings consist of selectors.
      * 
      * @return void
      * 
      * @since 1.0
      */
    public function bodyScript(GdprfreeTester $I, LoginPage $loginPage, CookieSettings $cookieSettings)
    { 
        $loginPage->userLogin($I);

        $I->waitForElement($cookieSettings->gdprCookieConset, 20);
        $I->moveMouseOver($cookieSettings->gdprCookieConset);
        $I->click($cookieSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($cookieSettings->scriptBlocker);
        $I->waitForText('Script Blocker Settings', 20);
        $I->clearField(['name' => 'gcc-header-scripts']);
        $I->fillField(['name' => 'gcc-body-scripts'], $cookieSettings->bodyScriptValue);
        $I->click($cookieSettings->saveChanges);
        $I->wait(2);
        $I->click($cookieSettings->GDPR);
        $I->click($cookieSettings->accept);
        $I->reloadPage();
        $I->wait(3);
        $I->acceptPopup();
        $I->wait(2);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to add footer script
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $cookieSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function footerScript(GdprfreeTester $I, LoginPage $loginPage, CookieSettings $cookieSettings)
    { 
        $loginPage->userLogin($I);

        $I->waitForElement($cookieSettings->gdprCookieConset, 20);
        $I->moveMouseOver($cookieSettings->gdprCookieConset);
        $I->click($cookieSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->click($cookieSettings->scriptBlocker);
        $I->waitForText('Script Blocker Settings', 20);
        $I->clearField(['name' => 'gcc-body-scripts']);
        $I->fillField(['name' => 'gcc-footer-scripts'], $cookieSettings->bodyScriptValue);
        $I->click($cookieSettings->saveChanges);
        $I->wait(2);
        $I->click($cookieSettings->GDPR);
        $I->click($cookieSettings->accept);
        $I->reloadPage();
        $I->wait(3);
        $I->acceptPopup();
        $I->wait(2);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        
        $loginPage->userLogout($I);
    }
}
