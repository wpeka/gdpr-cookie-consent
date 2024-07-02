/**
 * Frontend JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 * @author     wpeka <https://club.wpeka.com>
 */

import { TCModel, TCString, GVL } from '@iabtechlabtcf/core';
// const { TCModel, TCString, GVL } = require('@iabtechlabtcf/core');

// GVL.baseUrl = "http://localhost:8888/wordpress/";
// GVL.baseUrl = "https://app.wplegalpages.com/";
// GVL.baseUrl = "https://wplegalpages.com/vendor-list.json";
GVL.baseUrl = "https://923b74fe37.nxcli.io/rgh/";

GVL.baseUrl = iabtcf.ajax_url
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
  console.log( "All vendor data" )
  console.log( iabtcf.consentdata)
 
});

// create a new TC string
const tcModel = new TCModel(gvl);
var encodedString="default tc string...";
tcModel.cmpId = 2; // test id 
tcModel.cmpVersion = 1; // test version 
(function( $ ) {
	'use strict';
/**
*  the IAB requires CMPs to host their own vendor-list.json files.  This must
*  be set before creating any instance of the GVL class.
*/

$( '.gdpr_action_button' ).click(
    function(e){
        var elm                      = $( this );
        var button_action            = elm.attr( 'data-gdpr_action' );
        if (button_action == 'accept') {
        
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
            encodedString = TCString.encode(tcModel);
            iabtcf.consentdata.tcString = encodedString
            console.log("Here")
            console.log(iabtcf.consentdata)
            console.log(encodedString); // TC string encoded begins with 'C'
            $.post('classic.php', { 
                iabtcfConsentData:  iabtcf.consentdata
                }); 
           });

           
        }
    }
);
$( ".vendor-all-switch-handler" ).click(
    function() {
        $( ".vendor-all-switch-handler", this );
        if ( $( this ).is(":checked")) {
            $( ".vendor-switch-handler" ).prop( 'checked', true );
            iabtcf.consentdata.consent = ["12345"];
            iabtcf.consentdata.legint = ["12345"];

            for( var i=0; i < iabtcf.consentdata.allvendorIds.length; i++){
                iabtcf.consentdata.consent.push(iabtcf.consentdata.allvendorIds[i])
            }
            
            for( var i=0; i < iabtcf.consentdata.allVendorsWithLegint.length; i++){
                iabtcf.consentdata.legint.push(iabtcf.consentdata.allVendorsWithLegint[i])
            }
        }
        else {
            $( ".vendor-switch-handler" ).prop( 'checked', false );
            iabtcf.consentdata.allVendorsSelected = false
            // while(iabtcf.consentdata.consent.length){
            //     iabtcf.consentdata.consent.pop();
            // }
            // iabtcf.consentdata.consent.unshift("12345")
            iabtcf.consentdata.consent = ["12345"]
            iabtcf.consentdata.legint = ["12345"]
        }
        console.log(iabtcf.consentdata)		
    }
);
$( ".vendor-switch-handler.consent-switch" ).click(
    function() {
        var val = $( this ).val()
        if ( $( this ).is(":checked")) {
            iabtcf.consentdata.consent.push(val);
            $( this ).prop( 'checked', true );
        }
        else {
            $( this ).prop( 'checked', false );
            $( ".vendor-all-switch-handler" ).prop( 'checked', false );
            iabtcf.consentdata.consent.splice(iabtcf.consentdata.consent.indexOf(val), 1);						
        }
        console.log("From tcf.js")
        // console.log(parseInt(val))
        console.log(iabtcf.consentdata)					
    }
);
$( ".vendor-switch-handler.legint-switch" ).click(
    function() {
        var val = $( this ).val()
        if ( $( this ).is(":checked")) {
            iabtcf.consentdata.legint.push(val);
            $( this ).prop( 'checked', true );
        }
        else {
            $( this ).prop( 'checked', false );
            $( ".vendor-all-switch-handler" ).prop( 'checked', false );
            iabtcf.consentdata.legint.splice(iabtcf.consentdata.legint.indexOf(val), 1);						
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
            iabtcf.consentdata.purpose_consent = ["12345"]
            iabtcf.consentdata.purpose_legint = ["12345"]
            for( var i=0; i < iabtcf.consentdata.allPurposeIds.length; i++){
                iabtcf.consentdata.purpose_consent.push(iabtcf.consentdata.allPurposeIds[i])
            }
            for( var i=0; i < iabtcf.consentdata.allPurposesWithLegint.length; i++){
                iabtcf.consentdata.purpose_legint.push(iabtcf.consentdata.allPurposesWithLegint[i])
            }
        }
        else {
            $( ".purposes-switch-handler" ).prop( 'checked', false );
            // while(iabtcf.consentdata.purpose_consent.length){
            //     iabtcf.consentdata.purpose_consent.pop();
            // }
            // iabtcf.consentdata.purpose_consent.unshift(12345)
            iabtcf.consentdata.purpose_consent = ["12345"]
            iabtcf.consentdata.purpose_legint = ["12345"]
        }
        console.log(iabtcf.consentdata)	
    }
);

$( ".purposes-switch-handler.consent-switch" ).click(
    function() {
        var val = $( this ).val()
        if ( $( this ).is(":checked")) {
            iabtcf.consentdata.purpose_consent.push(val);
            $( this ).prop( 'checked', true );
        }
        else {
            $( this ).prop( 'checked', false );
            $( ".purposes-all-switch-handler" ).prop( 'checked', false );
            iabtcf.consentdata.purpose_consent.splice(iabtcf.consentdata.purpose_consent.indexOf(val), 1);						
        }
        console.log(iabtcf.consentdata)
    }
);

$( ".purposes-switch-handler.legint-switch" ).click(
    function() {
        var val = $( this ).val()
        if ( $( this ).is(":checked")) {
            iabtcf.consentdata.purpose_legint.push(val);
            $( this ).prop( 'checked', true );
        }
        else {
            $( this ).prop( 'checked', false );
            $( ".purposes-all-switch-handler" ).prop( 'checked', false );
            iabtcf.consentdata.purpose_legint.splice(iabtcf.consentdata.purpose_legint.indexOf(val), 1);						
        }
        console.log(iabtcf.consentdata)
    }
);

$( ".special-features-all-switch-handler" ).click(
    function() {
        $( ".special-features-all-switch-handler", this );
        if ( $( this ).is(":checked")) {
            $( ".special-features-switch-handler" ).prop( 'checked', true );
            iabtcf.consentdata.feature_consent = [12345]
            for( var i=0; i < iabtcf.consentdata.allSpecialFeatureIds.length; i++){
                iabtcf.consentdata.feature_consent.push(iabtcf.consentdata.allSpecialFeatureIds[i])
            }
        }
        else {
            $( ".special-features-switch-handler" ).prop( 'checked', false );
            while(iabtcf.consentdata.feature_consent.length){
                iabtcf.consentdata.feature_consent.pop();
            }
            iabtcf.consentdata.feature_consent.unshift(12345)
        }
        console.log(iabtcf.consentdata)	
    }
);

$( ".special-features-switch-handler.consent-switch" ).click(
    function() {
        var val = $( this ).val()
        if ( $( this ).is(":checked")) {
            iabtcf.consentdata.feature_consent.push(val);
            $( this ).prop( 'checked', true );
        }
        else {
            $( this ).prop( 'checked', false );
            $( ".special-features-all-switch-handler" ).prop( 'checked', false );
            iabtcf.consentdata.feature_consent.splice(iabtcf.consentdata.feature_consent.indexOf(val), 1);						
        }
        console.log(iabtcf.consentdata)
    }
);
})( jQuery );







