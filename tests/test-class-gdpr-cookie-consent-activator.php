<?php
/**
 * Class Test_Gdpr_Cookie_Consent_Activator
 *
 * @package Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/Tests
 */

/**
 * Test for Gdpr_Cookie_Consent_Activator class
 */
class Test_Gdpr_Cookie_Consent_Activator extends WP_UnitTestCase{
    
    public static $gdpr_cookie_consent;

    public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$gdpr_cookie_consent = new Gdpr_Cookie_Consent_Activator();
	}
    /**
	 * Test for activate function
	 */
    public function test_activate(){
        $arr=array(
            'is_on'=>true,
            'button_1_selected_text'=>'abc',
        );
        $arr3=array(
            'is_on'=>true,
            'button_1_text'=> 'abc',
'button_1_url'=> 'abc',
'button_1_action'=> 'abc',
'button_1_link_color'=> 'abc',
'button_1_button_color'=> 'abc',
'button_1_new_win'=> 'abc',
'button_1_as_button'=> 'abc',
'button_1_button_size'=> 'abc',
'button_1_is_on'=> 'abc',
'button_2_text'=> 'abc',
'button_2_url'=> 'abc',
'button_2_action'=> 'abc',
'button_2_link_color'=> 'abc',
'button_2_button_color'=> 'abc',
'button_2_new_win'=> 'abc',
'button_2_as_button'=> 'abc',
'button_2_button_size'=> 'abc',
'button_2_is_on'=> 'abc',
'button_3_text'=> 'abc',
'button_3_url'=> 'abc',
'button_3_action'=> 'abc',
'button_3_link_color'=> 'abc',
'button_3_button_color'=> 'abc',
'button_3_new_win'=> 'abc',
'button_3_as_button'=> 'abc',
'button_decline_button_size'=> 'abc',
'button_3_is_on'=> 'abc',
'button_4_text'=> 'abc',
'button_4_url'=> 'abc',
'button_4_action'=> 'abc',
'button_4_link_color'=> 'abc',
'button_4_button_color'=> 'abc',
'button_4_new_win'=> 'abc',
'button_4_as_button'=> 'abc',
'button_4_button_size'=> 'abc',
'button_4_is_on'=> 'abc',
'button_4_as_popup'=> 'abc',
        );
        update_option('GDPRCookieConsent-1.0',$arr);
        update_option('WPLCookieConsent-1.0',$arr);
        self::$gdpr_cookie_consent->activate();
        $data=get_option(GDPR_COOKIE_CONSENT_SETTINGS_FIELD);
        $this->assertEquals($data,$arr);
        self::$gdpr_cookie_consent->activate();
        $this->assertEquals($data,$arr);
        $arr2=array(
            'notify_div_id'=>'abc',
        );
        update_option(GDPR_COOKIE_CONSENT_SETTINGS_FIELD,$arr2);
        self::$gdpr_cookie_consent->activate();
        $data2=get_option(GDPR_COOKIE_CONSENT_SETTINGS_FIELD);
        $this->assertEquals('#gdpr-cookie-consent-bar',$data2['notify_div_id']);
        update_option( 'GDPRCookieConsent-2.0',$arr );
        update_option( 'GDPRCookieConsent-3.0',$arr );
        update_option( 'GDPRCookieConsent-4.0',$arr3 );
        update_option( 'GDPRCookieConsent-5.0',$arr );
        update_option( 'GDPRCookieConsent-6.0',$arr );
        update_option( 'GDPRCookieConsent-7.0',$arr );
        update_option( 'GDPRCookieConsent-8.0',$arr );
        self::$gdpr_cookie_consent->activate();
        $prev_gdpr_option=get_option('GDPRCookieConsent-2.0');
        $this->assertTrue(!$prev_gdpr_option);
        $prev_gdpr_option=get_option('GDPRCookieConsent-3.0');
        $this->assertTrue(!$prev_gdpr_option);
        $prev_gdpr_option=get_option('GDPRCookieConsent-4.0');
        $this->assertTrue(!$prev_gdpr_option);
        $prev_gdpr_option=get_option('GDPRCookieConsent-5.0');
        $this->assertTrue(!$prev_gdpr_option);
        $prev_gdpr_option=get_option('GDPRCookieConsent-6.0');
        $this->assertTrue(!$prev_gdpr_option);
        $prev_gdpr_option=get_option('GDPRCookieConsent-7.0');
        $this->assertTrue(!$prev_gdpr_option);
        $prev_gdpr_option=get_option('GDPRCookieConsent-8.0');
        $this->assertTrue(!$prev_gdpr_option);
    }
}

?>
