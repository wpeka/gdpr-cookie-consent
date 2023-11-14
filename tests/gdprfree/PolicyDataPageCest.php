<?php
// phpcs:ignoreFile
/**
 * Automation test cases for Policy Data of GDPR cookie consent plugin
 * 
 * @category AutomationTests
 * @package  WordPress_GDPRCookieConsent_Free_Plugin
 * @author   WPEKA <hello@wpeka.com>
 * @license  GPL v3
 * @link     https://club.wpeka.com
 */
use Facebook\WebDriver\WebDriverBy;
use Page\Acceptance\LoginPage;
use Page\Gdprfree\PolicyDataPage;

/**
 * Core class used for Policy Data of GDPR cookie consent plugin
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
class PolicyDataPageCest
{
    /**
     * Test to Check whether user is able to create a new policy data which he can add on any page
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $policyDataPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function createNewPolicyData(GdprfreeTester $I, LoginPage $loginPage, PolicyDataPage $policyDataPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($policyDataPage->gdprCookieConset, 20);
        $I->moveMouseOver($policyDataPage->gdprCookieConset);
        $I->click($policyDataPage->policyData);
        $I->waitForText('Policy Data', 20);
        $I->see('Policy Data');
        $I->click($policyDataPage->addNew);
        $I->waitForText('Add New Policy Data', 20);
        $I->fillField($policyDataPage->addTitle, $policyDataPage->addTitleValue);
        $I->switchToIFrame($policyDataPage->iframe);
        $I->fillField($policyDataPage->purpose, $policyDataPage->purposeValue);
        $I->switchToIFrame();
        $I->waitForElement($policyDataPage->domain, 20);
        $I->fillField($policyDataPage->domain, $policyDataPage->domainValue);
        $I->click($policyDataPage->addMedia);
        $I->waitForElement($policyDataPage->insertFromURL, 40);
        $I->click($policyDataPage->insertFromURL);
        $I->waitForElement($policyDataPage->urlField, 30);
        $I->fillField($policyDataPage->urlField, $policyDataPage->urlValue);
        $I->waitForElement($policyDataPage->insertInToPost, 30);
        $I->click($policyDataPage->insertInToPost);
        $I->wait(2);
        $I->scrollTo($policyDataPage->publishlabel);
        $I->click($policyDataPage->publish);
        $I->waitForElement($policyDataPage->pageMenu, 20);
        $I->moveMouseOver($policyDataPage->pageMenu);
        $I->click($policyDataPage->allPages);
        $I->click($policyDataPage->addNewPage);
        $I->waitForElement($policyDataPage->addPageTitle, 20);
        $I->fillField($policyDataPage->addPageTitle, $policyDataPage->addPageTitleValue);
        $I->waitForElement($policyDataPage->addicon, 20);
        $I->click($policyDataPage->addicon);
        $I->wait(3);
        $I->fillField($policyDataPage->search, $policyDataPage->searchValue);
        $I->click($policyDataPage->gdprCookieDetail);
        $I->click($policyDataPage->publish1);
        $I->click($policyDataPage->publish2);
        $I->waitForElement($policyDataPage->viewPage, 20);
        $I->click($policyDataPage->viewPage);
        $I->waitForText('Third Party Companies', 20);
        $I->see('Third Party Companies');
       
        $loginPage->userLogout($I);
    }

    /**
     * Test to Check whether user is able to edit a policy data which he can add on any page
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $policyDataPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function editPolicyData(GdprfreeTester $I, LoginPage $loginPage, PolicyDataPage $policyDataPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($policyDataPage->gdprCookieConset, 20);
        $I->moveMouseOver($policyDataPage->gdprCookieConset);
        $I->click($policyDataPage->policyData);
        $I->waitForText('Policy Data', 20);
        $I->see('Policy Data');
        $I->click($policyDataPage->policy1);
        $I->waitForElement($policyDataPage->addTitle, 20);
        $I->fillField($policyDataPage->addTitle, $policyDataPage->editedAddTitleValue);
        $I->switchToIFrame($policyDataPage->iframe);
        $I->fillField($policyDataPage->purpose, $policyDataPage->editedPurposeValue);
        $I->switchToIFrame();
        $I->waitForElement($policyDataPage->domain, 20);
        $I->fillField($policyDataPage->domain, $policyDataPage->domainValue);
        $I->click($policyDataPage->addMedia);
        $I->waitForElement($policyDataPage->insertFromURL, 20);
        $I->click($policyDataPage->insertFromURL);
        $I->waitForElement($policyDataPage->urlField, 20);
        $I->fillField($policyDataPage->urlField, $policyDataPage->urlValue);
        $I->click($policyDataPage->insertInToPost);
        $I->wait(2);
        $I->scrollTo($policyDataPage->publishlabel);
        $I->click($policyDataPage->update);
        $I->click($policyDataPage->gdpr);
        $I->click($policyDataPage->viewPolicyPage);
        $I->waitForText('Policy2', 20);
        $I->see('Policy2');
       
        $loginPage->userLogout($I);
    }

    /**
     * Test to Check whether user is able to quick-edit a policy data which he can add on any page
     *
     * @param $I              variable of GdprfreeTester
     * @param $loginPage      Used to login and logout from the page.
     * @param $policyDataPage consist of selectors.
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function quickEditPolicyData(GdprfreeTester $I, LoginPage $loginPage, PolicyDataPage $policyDataPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($policyDataPage->gdprCookieConset, 20);
        $I->moveMouseOver($policyDataPage->gdprCookieConset);
        $I->click($policyDataPage->policyData);
        $I->waitForText('Policy Data', 20);
        $I->see('Policy Data');
        $I->moveMouseOver($policyDataPage->policy2);
        $I->waitForElement($policyDataPage->quick_Edit, 20);
        $I->click($policyDataPage->quick_Edit);
        $I->waitForElement($policyDataPage->policyTitle, 20);
        $I->fillField($policyDataPage->policyTitle, $policyDataPage->policyTitleValue);
        $I->selectOption($policyDataPage->month, $policyDataPage->monthValue);
        $I->fillField($policyDataPage->date, $policyDataPage->dateValue);
        $I->fillField($policyDataPage->year, $policyDataPage->yearValue);
        $I->fillField($policyDataPage->timeInHour, $policyDataPage->timeInHourValue);
        $I->fillField($policyDataPage->timeInMinute, $policyDataPage->timeInMinuteValue);
        $I->click($policyDataPage->updateButton);
        $I->click($policyDataPage->gdpr);
        $I->click($policyDataPage->viewPolicyPage);
        $I->waitForText('Policy3', 20);
        $I->see('Policy3');
        
        $loginPage->userLogout($I);
    }

     /**
      * Test to Check whether user is able to delete a created policy data 
      *
      * @param $I              variable of GdprfreeTester
      * @param $loginPage      Used to login and logout from the page.
      * @param $policyDataPage consist of selectors.
      * 
      * @return void
      * 
      * @since 1.0
      */
    public function deletePolicyData(GdprfreeTester $I, LoginPage $loginPage, PolicyDataPage $policyDataPage)
    {
        $loginPage->userLogin($I);

        $I->waitForElement($policyDataPage->gdprCookieConset, 20);
        $I->moveMouseOver($policyDataPage->gdprCookieConset);
        $I->click($policyDataPage->policyData);
        $I->waitForText('Policy Data', 20);
        $I->see('Policy Data');
        $I->click($policyDataPage->policy3);
        $I->waitForElement($policyDataPage->moveToTrash, 20);
        $I->click($policyDataPage->moveToTrash);
        $I->click($policyDataPage->gdpr);
        $I->click($policyDataPage->viewPolicyPage);
        $I->wait(2);
        
        $loginPage->userLogout($I);
    }
}
