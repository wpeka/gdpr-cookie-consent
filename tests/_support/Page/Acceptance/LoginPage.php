<?php
/**
 * Selectors used in the automation testcases for login and logout of GDPR cookie consent plugin
 * 
 * @category AutomationTests
 * @package  WordPress_WPlegalpages_Pro_Plugin
 * @author   WPEKA <hello@wpeka.com>
 * @license  GPL v3
 * @link     https://club.wpeka.com
 */
namespace Page\Acceptance;

/**
 * Core class for all the selectors used in automation testcases for login and logout of GDPR cookie consent plugin
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
class LoginPage
{
    public $userLoginField='#user_login';
    public $userPasswordField='#user_pass';
    public $loginBtn='#wp-submit';
    public $wordpressProfile='//*[@id="wp-admin-bar-my-account"]/a/span';
    public $logOutLink='//*[@id="wp-admin-bar-logout"]/a';

    /**
     * Test to check userlogin function
     *
     * @param $I variable of userlogin function
     * 
     * @return void
     * 
     * @since 1.0
     */
    public function userLogin($I)
    {
        $I->amOnPage('/wp-admin');
        $I->wait(2);
        $I->fillField($this->userLoginField, 'admin');
        $I->fillField($this->userPasswordField, 'password');
        $I->click($this->loginBtn);
    }

     /**
      * Test to check userlogout function
      *
      * @param $I variable of userlogout function
      * 
      * @return void
      * 
      * @since 1.0
      */
    public function userLogout($I)
    {
        $I->moveMouseOver($this->wordpressProfile);
        $I->click($this->logOutLink);
        $I->wait(2);
    }
}
