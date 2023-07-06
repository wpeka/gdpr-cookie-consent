<?php
/**
 * Automation test cases for compliances settings tab of GDPR cookie consent plugin
 * 
 * @category AutomationTests
 * @package  WordPress_GDPRCookieConsent_Free_Plugin
 * @author   WPEKA <hello@wpeka.com>
 * @license  GPL v3
 * @link     https://club.wpeka.com
 */
use Facebook\WebDriver\WebDriverBy;
use Page\Acceptance\LoginPage;
use Page\Gdprfree\CompliancesSettings;

/**
 * Core class used for compliances settings tab of GDPR cookie consent plugin
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
class CompliancesSettingsCest
{
    /**
     * Test to check whether user is able to select the postion of revoke consent tab(if user selects left option) 
     *
     * @param $I                   variable of GdprfreeTester
     * @param $loginPage           Used to login and logout from the page.
     * @param $compliancesSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function revokeConsentTabPositionToLeft(GdprfreeTester $I, LoginPage $loginPage, CompliancesSettings $compliancesSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($compliancesSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($compliancesSettings->gdprCookieConsent);
        $I->click($compliancesSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->scrollTo($compliancesSettings->revokeConsent);
        $I->click($compliancesSettings->tabPosition);
        $I->wait(1);
        $I->click($compliancesSettings->tabPositionValue);
        $I->scrollTo($compliancesSettings->saveChanges);
        $I->click($compliancesSettings->saveChanges);
        $I->wait(1);
        $I->click($compliancesSettings->GDPR);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        $I->click($compliancesSettings->accept);
        $I->waitForElement($compliancesSettings->cookieSettingsButton);
        
        $loginPage->userLogout($I);
    }

     /**
      * Test to check whether user is able to select the postion of revoke consent tab(if user selects right option) 
      *
      * @param $I                   variable of GdprfreeTester
      * @param $loginPage           Used to login and logout from the page.
      * @param $compliancesSettings consist of selectors.
      * 
      * @return void
      * 
      * @since 1.0
      */
    public function revokeConsentTabPositionToRight(GdprfreeTester $I, LoginPage $loginPage, CompliancesSettings $compliancesSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($compliancesSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($compliancesSettings->gdprCookieConsent);
        $I->click($compliancesSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->scrollTo($compliancesSettings->revokeConsent);
        $I->click($compliancesSettings->tabPosition);
        $I->wait(1);
        $I->click($compliancesSettings->tabPositionValue2);
        $I->scrollTo($compliancesSettings->saveChanges);
        $I->click($compliancesSettings->saveChanges);
        $I->wait(1);
        $I->click($compliancesSettings->GDPR);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        $I->click($compliancesSettings->accept);
        $I->waitForElement($compliancesSettings->cookieSettingsButton);
        
        $loginPage->userLogout($I);
    }

     /**
      * Test to check whether user is able to change the tab margin of revoke consent tab 
      *
      * @param $I                   variable of GdprfreeTester
      * @param $loginPage           Used to login and logout from the page.
      * @param $compliancesSettings consist of selectors.
      * 
      * @return void
      * 
      * @since 1.0
      */
    public function revokeConsentTabMargin(GdprfreeTester $I, LoginPage $loginPage, CompliancesSettings $compliancesSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($compliancesSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($compliancesSettings->gdprCookieConsent);
        $I->click($compliancesSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->scrollTo($compliancesSettings->revokeConsent);
        $I->fillField($compliancesSettings->tabMargin, $compliancesSettings->tabMarginValue);
        $I->scrollTo($compliancesSettings->saveChanges);
        $I->click($compliancesSettings->saveChanges);
        $I->wait(1);
        $I->click($compliancesSettings->GDPR);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        $I->click($compliancesSettings->accept);
        $I->waitForElement($compliancesSettings->cookieSettingsButton);
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to change the tab text of revoke consent tab 
     *
     * @param $I                   variable of GdprfreeTester
     * @param $loginPage           Used to login and logout from the page.
     * @param $compliancesSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function revokeConsentTabText(GdprfreeTester $I, LoginPage $loginPage, CompliancesSettings $compliancesSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($compliancesSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($compliancesSettings->gdprCookieConsent);
        $I->click($compliancesSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->scrollTo($compliancesSettings->revokeConsent);
        $I->fillField($compliancesSettings->tabText, $compliancesSettings->tabTextValue);
        $I->scrollTo($compliancesSettings->saveChanges);
        $I->click($compliancesSettings->saveChanges);
        $I->wait(1);
        $I->click($compliancesSettings->GDPR);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        $I->click($compliancesSettings->accept);
        $I->waitForElement($compliancesSettings->cookieSettingsButton);
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to enable the autotick non-necessary cookies for consent settings 
     *
     * @param $I                   variable of GdprfreeTester
     * @param $loginPage           Used to login and logout from the page.
     * @param $compliancesSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function consentSettingsAutotickNonNecessaryEnable(GdprfreeTester $I, LoginPage $loginPage, CompliancesSettings $compliancesSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($compliancesSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($compliancesSettings->gdprCookieConsent);
        $I->click($compliancesSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->scrollTo($compliancesSettings->revokeConsent);
        $I->click($compliancesSettings->autotickNonNecessaryCookie);
        $I->scrollTo($compliancesSettings->saveChanges);
        $I->click($compliancesSettings->saveChanges);
        $I->wait(1);
        $I->click($compliancesSettings->GDPR);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        $I->click($compliancesSettings->cookieSettings);
        $I->waitForText('I consent to the use of following cookies:', 20);
        $I->see('I consent to the use of following cookies:');

        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to disable the autotick non-necessary cookies for consent settings 
     *
     * @param $I                   variable of GdprfreeTester
     * @param $loginPage           Used to login and logout from the page.
     * @param $compliancesSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function consentSettingsAutotickNonNecessaryDisable(GdprfreeTester $I, LoginPage $loginPage, CompliancesSettings $compliancesSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($compliancesSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($compliancesSettings->gdprCookieConsent);
        $I->click($compliancesSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->scrollTo($compliancesSettings->revokeConsent);
        $I->click($compliancesSettings->autotickNonNecessaryCookie);
        $I->scrollTo($compliancesSettings->saveChanges);
        $I->click($compliancesSettings->saveChanges);
        $I->wait(1);
        $I->click($compliancesSettings->GDPR);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        $I->click($compliancesSettings->cookieSettings);
        $I->waitForText('I consent to the use of following cookies:', 20);
        $I->see('I consent to the use of following cookies:');

        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to enable the auto hide(accept) cookies for consent settings 
     *
     * @param $I                   variable of GdprfreeTester
     * @param $loginPage           Used to login and logout from the page.
     * @param $compliancesSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function consentSettingsAutoHideEnable(GdprfreeTester $I, LoginPage $loginPage, CompliancesSettings $compliancesSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($compliancesSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($compliancesSettings->gdprCookieConsent);
        $I->click($compliancesSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->scrollTo($compliancesSettings->revokeConsent);
        $I->click($compliancesSettings->autoHide);
        $I->fillField($compliancesSettings->autoHideDelay, $compliancesSettings->autoHideDelayValue);
        $I->scrollTo($compliancesSettings->saveChanges);
        $I->click($compliancesSettings->saveChanges);
        $I->wait(1);
        $I->click($compliancesSettings->GDPR);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        $I->wait(5);
        $I->waitForElement($compliancesSettings->cSettings);
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to enable the auto scroll(accept) cookies for consent settings 
     *
     * @param $I                   variable of GdprfreeTester
     * @param $loginPage           Used to login and logout from the page.
     * @param $compliancesSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function consentSettingsAutoScrollEnable(GdprfreeTester $I, LoginPage $loginPage, CompliancesSettings $compliancesSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($compliancesSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($compliancesSettings->gdprCookieConsent);
        $I->click($compliancesSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->scrollTo($compliancesSettings->revokeConsent);
        $I->click($compliancesSettings->autoHide);
        $I->click($compliancesSettings->autoScroll);
        $I->fillField($compliancesSettings->autoScrollOffset, $compliancesSettings->autoScrollOffsetValue);
        $I->scrollTo($compliancesSettings->saveChanges);
        $I->click($compliancesSettings->saveChanges);
        $I->wait(1);
        $I->click($compliancesSettings->GDPR);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        $I->scrollTo($compliancesSettings->archives);
        $I->waitForElement($compliancesSettings->cSettings);
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to enable the reload after auto scroll(accept) cookies for consent settings 
     *
     * @param $I                   variable of GdprfreeTester
     * @param $loginPage           Used to login and logout from the page.
     * @param $compliancesSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function consentSettingsReloadAfterAutoScrollEnable(GdprfreeTester $I, LoginPage $loginPage, CompliancesSettings $compliancesSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($compliancesSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($compliancesSettings->gdprCookieConsent);
        $I->click($compliancesSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->scrollTo($compliancesSettings->revokeConsent);
        $I->click($compliancesSettings->autoScroll);
        $I->click($compliancesSettings->reloadAfterAutoScroll);
        $I->scrollTo($compliancesSettings->saveChanges);
        $I->click($compliancesSettings->saveChanges);
        $I->wait(1);
        $I->click($compliancesSettings->GDPR);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        $I->scrollTo($compliancesSettings->archives);
        $I->waitForElement($compliancesSettings->cSettings);
        
        $loginPage->userLogout($I);
    }
    
    /**
     * Test to check whether user is able to set reload after accept cookie for consent settings 
     *
     * @param $I                   variable of GdprfreeTester
     * @param $loginPage           Used to login and logout from the page.
     * @param $compliancesSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function consentSettingsReloadAfterAccept(GdprfreeTester $I, LoginPage $loginPage, CompliancesSettings $compliancesSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($compliancesSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($compliancesSettings->gdprCookieConsent);
        $I->click($compliancesSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->scrollTo($compliancesSettings->consentSettings);
        $I->click($compliancesSettings->reloadAfterAccept);
        $I->scrollTo($compliancesSettings->saveChanges);
        $I->click($compliancesSettings->saveChanges);
        $I->wait(1);
        $I->click($compliancesSettings->GDPR);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        $I->click($compliancesSettings->accept);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        
        $loginPage->userLogout($I);
    }

    /**
     * Test to check whether user is able to set reload after decline cookie for consent settings 
     *
     * @param $I                   variable of GdprfreeTester
     * @param $loginPage           Used to login and logout from the page.
     * @param $compliancesSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function consentSettingsReloadAfterDecline(GdprfreeTester $I, LoginPage $loginPage, CompliancesSettings $compliancesSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($compliancesSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($compliancesSettings->gdprCookieConsent);
        $I->click($compliancesSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->scrollTo($compliancesSettings->consentSettings);
        $I->click($compliancesSettings->reloadAfterAccept);
        $I->click($compliancesSettings->reloadAfterDecline);
        $I->scrollTo($compliancesSettings->saveChanges);
        $I->click($compliancesSettings->saveChanges);
        $I->wait(1);
        $I->click($compliancesSettings->GDPR);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        $I->click($compliancesSettings->decline);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 30);
        
        $loginPage->userLogout($I);
    }
    
    /**
     * Test to check whether user is able to show credits for extra settings 
     *
     * @param $I                   variable of GdprfreeTester
     * @param $loginPage           Used to login and logout from the page.
     * @param $compliancesSettings consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function extraSettingsShowCredits(GdprfreeTester $I, LoginPage $loginPage, CompliancesSettings $compliancesSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($compliancesSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($compliancesSettings->gdprCookieConsent);
        $I->click($compliancesSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->scrollTo($compliancesSettings->extraSettings);
        $I->click($compliancesSettings->showCredits);
        $I->click($compliancesSettings->saveChanges);
        $I->wait(1);
        $I->click($compliancesSettings->GDPR);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        $I->click($compliancesSettings->cookieSettings);
        $I->waitForText('Powered by GDPR Cookie Consent Plugin', 20);
        $I->see('Powered by GDPR Cookie Consent Plugin');

        $loginPage->userLogout($I);
    }

     /**
      * Test to check whether user is able to reset settings for extra settings 
      *
      * @param $I                   variable of GdprfreeTester
      * @param $loginPage           Used to login and logout from the page.
      * @param $compliancesSettings consist of selectors.
      * 
      * @return void
      * 
      * @since 1.0
      */
    public function extraSettingsResetSettings(GdprfreeTester $I, LoginPage $loginPage, CompliancesSettings $compliancesSettings)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($compliancesSettings->gdprCookieConsent, 20);
        $I->moveMouseOver($compliancesSettings->gdprCookieConsent);
        $I->click($compliancesSettings->settings);
        $I->waitForText('Cookie Notice', 20);
        $I->see('Cookie Notice');
        $I->scrollTo($compliancesSettings->extraSettings);
        $I->click($compliancesSettings->resetSettings);
        $I->acceptPopup();
        $I->wait(3);
        $I->click($compliancesSettings->GDPR);
        $I->waitForText('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 20);
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start writing!');
        $I->click($compliancesSettings->accept);
        $I->waitForElement($compliancesSettings->cookieSettingsButton);

        $loginPage->userLogout($I);
    }
}
