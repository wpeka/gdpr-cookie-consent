/**
 * Frontend JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 * @author     wpeka <https://club.wpeka.com>
 */
import {TCModel, TCString, GVL} from '@iabtechlabtcf/core';


/**
*  the IAB requires CMPs to host their own vendor-list.json files.  This must
*  be set before creating any instance of the GVL class.
*/
GVL.baseUrl = "http://localhost:8888/wordpress/";
const gvl = new GVL();
console.log("Here is the GVL object");
// console.log(gvl);

gvl.readyPromise.then(() => {
  gvl.narrowVendorsTo([1,2,4,6,8,10,11,12,14]);
  console.log(gvl);

  const data = {};
  const vendorMap = gvl.vendors;
  const purposeMap = gvl.purposes;
  const featureMap = gvl.features;
  const specialPurposeMap = gvl.specialPurposes;
  const specialFeatureMap = gvl.specialFeatures;

var vendor_array = [], feature_array = [], purpose_array = [], special_feature_array = [], special_purpose_array = [];
Object.keys(vendorMap).forEach(key => vendor_array.push(vendorMap[key]));
data.vendors = vendor_array;

Object.keys(featureMap).forEach(key => feature_array.push(featureMap[key]));
data.features = feature_array;

Object.keys(purposeMap).forEach(key => purpose_array.push(purposeMap[key]));
data.purposes = purpose_array;

Object.keys(specialFeatureMap).forEach(key => special_feature_array.push(specialFeatureMap[key]));
data.specialFeatures = special_feature_array;

Object.keys(specialPurposeMap).forEach(key => special_purpose_array.push(specialPurposeMap[key]));
data.specialPurposes = special_purpose_array;

console.log(data);

var xhr = new XMLHttpRequest();
xhr.open("POST", "test.php");
xhr.onreadystatechange = function() { if (xhr.readyState === 4 && xhr.status === 200) { console.log(xhr.responseText); } }
xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xhr.send("json=" + encodeURIComponent(JSON.stringify(data))); 
// console.log(JSON.stringify(gvl));
});

// create a new TC string
const tcModel = new TCModel(gvl);
tcModel.cmpId = 2; // test id 
tcModel.cmpVersion = 1; // test version 

// console.log("Here is the tcModel object :");
// console.log(tcModel);
tcModel.vendorConsents.set([4, 1]);

// Some fields will not be populated until a GVL is loaded
tcModel.gvl.readyPromise.then(() => {


 // Set values on tcModel...


 const encodedString = TCString.encode(tcModel);


//  console.log(encodedString); // TC string encoded begins with 'C'


});







