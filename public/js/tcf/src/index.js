/**
 * Frontend JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 * @author     wpeka <https://club.wpeka.com>
 */
import { TCModel, TCString, GVL } from '@iabtechlabtcf/core';


/**
*  the IAB requires CMPs to host their own vendor-list.json files.  This must
*  be set before creating any instance of the GVL class.
*/
// GVL.baseUrl = "http://localhost:8888/wordpress/";
// GVL.baseUrl = "https://wplegalpages.com/vendor-list.json";
// GVL.baseUrl = "http://localhost:10003"; //divyajeet
GVL.baseUrl = "https://923b74fe37.nxcli.io/rgh/";
// GVL.baseUrl = "https://app.wplegalpages.com/";
// GVL.baseUrl = 'http://test.local/' //Rohan
// GVL.baseUrl = ''

// 
// var request = new XMLHttpRequest();
// request.open('GET', 'https://923b74fe37.nxcli.io/rgh/vendor-list.json', false); 
// request.send(null);

// if (request.status == 0)
//     console.log(request.responseText);
// 
GVL.baseUrl = iabtcf.ajax_url
const gvl = new GVL();
console.log("Here is the GVL object");
// console.log(gvl);

gvl.readyPromise.then(() => {
  // gvl.narrowVendorsTo([1,2,4,6,8,10,11,12,14]);
  console.log(gvl);

  const data = {};
  const vendorMap = gvl.vendors;
  const purposeMap = gvl.purposes;
  const featureMap = gvl.features;
  const dataCategoriesMap = gvl.dataCategories;
  const specialPurposeMap = gvl.specialPurposes;
  const specialFeatureMap = gvl.specialFeatures;
  const purposeVendorMap = gvl.byPurposeVendorMap;

var vendor_array = [], vendor_id_array=[], vendor_legint_id_array=[], data_categories_array = [], nayan = [];
var feature_array = [], special_feature_id_array=[], special_feature_array = [], special_purpose_array = [];
var purpose_id_array=[], purpose_legint_id_array=[], purpose_array = [], purpose_vendor_array = [];
var purpose_vendor_count_array = [], feature_vendor_count_array = [], special_purpose_vendor_count_array = [], special_feature_vendor_count_array = [], legint_purpose_vendor_count_array = [], legint_feature_vendor_count_array = [];
Object.keys(vendorMap).forEach(key => {
  vendor_array.push(vendorMap[key])
  vendor_id_array.push(vendorMap[key].id)
  if(vendorMap[key].legIntPurposes.length)
  vendor_legint_id_array.push(vendorMap[key].id)
});
data.vendors = vendor_array;
data.allvendors = vendor_id_array;
data.allLegintVendors = vendor_legint_id_array;
console.log("vendors with legint")
console.log(vendor_legint_id_array)

Object.keys(featureMap).forEach(key => {
  feature_array.push(featureMap[key]);
  feature_vendor_count_array.push(Object.keys((gvl.getVendorsWithFeature(featureMap[key].id))).length); 
});
data.features = feature_array;
data.featureVendorCount = feature_vendor_count_array;
console.log("feature_vendor_count_array: "+ data.featureVendorCount); 

Object.keys(dataCategoriesMap).forEach(key => {
  // data_categories_array.push(dataCategoriesMap[key]);
  // nayan.push(dataCategoriesMap[key]);
});
data.dataCategories = nayan;
console.log("Data categories here : ");
console.log(data.dataCategories);

var legintCount=0;
const purposeLegint = new Map()
Object.keys(purposeMap).forEach(key => {
  console.log(purposeMap[key])
  purpose_array.push(purposeMap[key]);
  purpose_id_array.push(purposeMap[key].id)
  purpose_vendor_count_array.push(Object.keys((gvl.getVendorsWithConsentPurpose(purposeMap[key].id))).length); 
  legintCount = Object.keys((gvl.getVendorsWithLegIntPurpose(purposeMap[key].id))).length
  legint_purpose_vendor_count_array.push(legintCount); 
  if(legintCount){
    purposeLegint.set(purposeMap[key].id, legintCount)
    purpose_legint_id_array.push(purposeMap[key].id)
  }
  
  // console.log(Object.keys((gvl.getVendorsWithConsentPurpose(purposeMap[key].id))).length);
});console.log("Test purposes with legint : "+purpose_legint_id_array);
data.purposes = purpose_array;
data.allPurposes = purpose_id_array;
data.purposeVendorCount = purpose_vendor_count_array;
data.allLegintPurposes = purpose_legint_id_array;
data.legintPurposeVendorCount = legint_purpose_vendor_count_array;


Object.keys(specialFeatureMap).forEach(key => {
  special_feature_array.push(specialFeatureMap[key]);
  special_feature_id_array.push(specialFeatureMap[key].id)
  special_feature_vendor_count_array.push(Object.keys((gvl.getVendorsWithSpecialFeature(specialFeatureMap[key].id))).length); 
});
data.specialFeatures = special_feature_array;
data.allSpecialFeatures = special_feature_id_array;
data.specialFeatureVendorCount = special_feature_vendor_count_array;
console.log("special_feature_vendor_count_array: "+ data.specialFeatureVendorCount); 


Object.keys(specialPurposeMap).forEach(key => {
  special_purpose_array.push(specialPurposeMap[key]);
  special_purpose_vendor_count_array.push(Object.keys((gvl.getVendorsWithSpecialPurpose(purposeMap[key].id))).length); 
});
data.specialPurposes = special_purpose_array;
data.specialPurposeVendorCount = special_purpose_vendor_count_array;
console.log("special_purpose_vendor_count_array: "+ data.specialPurposeVendorCount); 


Object.keys(purposeVendorMap).forEach(key => purpose_vendor_array.push(purposeVendorMap[key].legInt.size));
data.purposeVendorMap = purpose_vendor_array;
console.log(data);

var xhr = new XMLHttpRequest();
xhr.open("POST", "test.php");
xhr.onreadystatechange = function() { if (xhr.readyState === 4 && xhr.status === 200) { console.log(xhr.responseText); } }
xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xhr.send("json=" + encodeURIComponent(JSON.stringify(data))); 
console.log("Final Data here")
console.log(JSON.stringify(data));
});

// create a new TC string
const tcModel = new TCModel(gvl);
tcModel.cmpId = 2; // test id 
tcModel.cmpVersion = 1; // test version 

// console.log("Here is the tcModel object :");
// console.log(tcModel);
tcModel.vendorConsents.set([4, 1, 8]);
tcModel.vendorLegitimateInterests.set([8,10]);
tcModel.purposeConsents.set([2,4,6,7]);
tcModel.purposeLegitimateInterests.set([2,4,7]);
tcModel.specialFeatureOptins.set([1,2]);

// Some fields will not be populated until a GVL is loaded
tcModel.gvl.readyPromise.then(() => {


 // Set values on tcModel...


 const encodedString = TCString.encode(tcModel);


 console.log(encodedString); // TC string encoded begins with 'C'


});






