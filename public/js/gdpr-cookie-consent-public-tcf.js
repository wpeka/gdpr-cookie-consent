/**
 * Frontend JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 * @author     wpeka <https://club.wpeka.com>
 */
import {TCModel, TCString, GVL} from '@iabtechlabtcf/core';

(function( $ ) {
/**
*  the IAB requires CMPs to host their own vendor-list.json files.  This must
*  be set before creating any instance of the GVL class.
*/
GVL.baseUrl = "http://localhost:8888/wordpress/";
const gvl = new GVL();
console.log(gvl);

gvl.readyPromise.then(() => {
  gvl.narrowVendorsTo([1,2,4,6,8,10,11,12,14]);
  console.log(gvl);
  if(iabtcf.consentdata.consent === "undefined")
  iabtcf.consentdata.consent=[];
  if(iabtcf.consentdata.legint === "undefined")
  iabtcf.consentdata.legint=[];
  if(iabtcf.consentdata.purpose_consent === "undefined")
  iabtcf.consentdata.purpose_consent=[];
  if(iabtcf.consentdata.purpose_legint === "undefined")
  iabtcf.consentdata.purpose_legint=[];
  if(iabtcf.consentdata.feature_consent === "undefined")
  iabtcf.consentdata.feature_consent=[];
  console.log( iabtcf.consentdata.consent)
  console.log( iabtcf.consentdata.legint)
  console.log( iabtcf.consentdata.purpose_consent)
  console.log( iabtcf.consentdata.purpose_legint)
  console.log( iabtcf.consentdata.feature_consent)
 
});

// create a new TC string
const tcModel = new TCModel(gvl);
tcModel.cmpId = 2; // test id 
tcModel.cmpVersion = 1; // test version 

$( '.gdpr_action_button' ).click(
    function(e){
        var elm                      = $( this );
        var button_action            = elm.attr( 'data-gdpr_action' );
        if (button_action == 'accept') {
        $.post('classic.php', { 
            iabtcfConsentData:  iabtcf.consentdata
        }); 
        
        // Some fields will not be populated until a GVL is loaded
        tcModel.gvl.readyPromise.then(() => {
            console.log( iabtcf.consentdata.consent)
            console.log( iabtcf.consentdata.legint)
            console.log( iabtcf.consentdata.purpose_consent)
            console.log( iabtcf.consentdata.purpose_legint)
            console.log( iabtcf.consentdata.feature_consent)
            tcModel.vendorConsents.set(( iabtcf.consentdata.consent).map(Number));
            tcModel.vendorLegitimateInterests.set((iabtcf.consentdata.legint).map(Number));
            tcModel.purposeConsents.set((iabtcf.consentdata.purpose_consent).map(Number));
            tcModel.purposeLegitimateInterests.set((iabtcf.consentdata.purpose_legint).map(Number));
            tcModel.specialFeatureOptins.set((iabtcf.consentdata.feature_consent).map(Number));
            
            // Set values on tcModel...           
            const encodedString = TCString.encode(tcModel);
            console.log(encodedString); // TC string encoded begins with 'C'
           });
        }
    }
);
$( ".vendor-all-switch-handler" ).click(
    function() {
        $( ".vendor-all-switch-handler", this );
        if ( $( this ).is(":checked")) {
            $( ".vendor-switch-handler" ).prop( 'checked', true );
        }
        else {
            $( ".vendor-switch-handler" ).prop( 'checked', false );
        }
    }
);
$( ".vendor-switch-handler.consent-switch" ).click(
    function() {
        if ( $( this ).is(":checked")) {
            iabtcf.consentdata.consent.push($( this ).val());
            $( this ).prop( 'checked', true );
        }
        else {
            $( this ).prop( 'checked', false );
            $( ".vendor-all-switch-handler" ).prop( 'checked', false );
            iabtcf.consentdata.consent.splice(iabtcf.consentdata.consent.indexOf($( this ).val()), 1);						
        }
        console.log("From tcf.js")
        console.log(iabtcf.consentdata)					
    }
);
$( ".vendor-switch-handler.legint-switch" ).click(
    function() {
        if ( $( this ).is(":checked")) {
            iabtcf.consentdata.legint.push($( this ).val());
            $( this ).prop( 'checked', true );
        }
        else {
            $( this ).prop( 'checked', false );
            $( ".vendor-all-switch-handler" ).prop( 'checked', false );
            iabtcf.consentdata.legint.splice(iabtcf.consentdata.legint.indexOf($( this ).val()), 1);						
        }
        console.log(iabtcf.consentdata)

        // $("#cookie_action_save.gdpr_action_button").attr("vendor-consent-array", consentArray)
        
    }
);
$( ".purposes-all-switch-handler" ).click(
    function() {
        $( ".purposes-all-switch-handler", this );
        if ( $( this ).is(":checked")) {
            $( ".purposes-switch-handler" ).prop( 'checked', true );
        }
        else {
            $( ".purposes-switch-handler" ).prop( 'checked', false );
        }
    }
);

$( ".purposes-switch-handler.consent-switch" ).click(
    function() {
        if ( $( this ).is(":checked")) {
            iabtcf.consentdata.purpose_consent.push($( this ).val());
            $( this ).prop( 'checked', true );
        }
        else {
            $( this ).prop( 'checked', false );
            $( ".purposes-all-switch-handler" ).prop( 'checked', false );
            iabtcf.consentdata.purpose_consent.splice(iabtcf.consentdata.purpose_consent.indexOf($( this ).val()), 1);						
        }
        console.log(iabtcf.consentdata)
    }
);

$( ".purposes-switch-handler.legint-switch" ).click(
    function() {
        if ( $( this ).is(":checked")) {
            iabtcf.consentdata.purpose_legint.push($( this ).val());
            $( this ).prop( 'checked', true );
        }
        else {
            $( this ).prop( 'checked', false );
            $( ".purposes-all-switch-handler" ).prop( 'checked', false );
            iabtcf.consentdata.purpose_legint.splice(iabtcf.consentdata.purpose_legint.indexOf($( this ).val()), 1);						
        }
        console.log(iabtcf.consentdata)
    }
);

$( ".special-features-all-switch-handler" ).click(
    function() {
        $( ".special-features-all-switch-handler", this );
        if ( $( this ).is(":checked")) {
            $( ".special-features-switch-handler" ).prop( 'checked', true );
        }
        else {
            $( ".special-features-switch-handler" ).prop( 'checked', false );
        }
    }
);

$( ".special-features-switch-handler.consent-switch" ).click(
    function() {
        if ( $( this ).is(":checked")) {
            iabtcf.consentdata.feature_consent.push($( this ).val());
            $( this ).prop( 'checked', true );
        }
        else {
            $( this ).prop( 'checked', false );
            $( ".special-features-all-switch-handler" ).prop( 'checked', false );
            iabtcf.consentdata.feature_consent.splice(iabtcf.consentdata.feature_consent.indexOf($( this ).val()), 1);						
        }
        console.log(iabtcf.consentdata)
    }
);
})( jQuery );








