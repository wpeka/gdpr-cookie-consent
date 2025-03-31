/**
 * Frontend JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 * @author     wpeka <https://club.wpeka.com>
 */

import { CmpApi } from "@iabtechlabtcf/cmpapi";
import { TCModel, TCString, GVL } from "@iabtechlabtcf/core";
import * as cmpstub from "@iabtechlabtcf/stub";

//test cmp id and version
const cmpId = 449;
const cmpVersion = 1;
cmpstub();

//functions to handle tc string cookie
var GDPR_Cookie = {
  set: function (name, value, days) {
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      var expires = "; expires=" + date.toUTCString();
    } else {
      var expires = "";
    }
    document.cookie =
      name + "=" + encodeURIComponent(value) + expires + "; path=/";
  },
  read: function (name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    var ca_length = ca.length;
    for (var i = 0; i < ca_length; i++) {
      var c = ca[i];
      while (c.charAt(0) == " ") {
        c = c.substring(1, c.length);
      }
      if (c.indexOf(nameEQ) === 0) {
        return decodeURIComponent(c.substring(nameEQ.length, c.length));
      }
    }
    return null;
  },
  exists: function (name) {
    return this.read(name) !== null;
  },
  getallcookies: function () {
    var pairs = document.cookie.split(";");
    var cookieslist = {};
    var pairs_length = pairs.length;
    for (var i = 0; i < pairs_length; i++) {
      var pair = pairs[i].split("=");
      cookieslist[(pair[0] + "").trim()] = unescape(pair[1]);
    }
    return cookieslist;
  },
  erase: function (name) {
    this.set(name, "", -10);
  },
};

//url of the location where vendor-list.json is hosted
GVL.baseUrl = "https://appwplegalpages.b-cdn.net/";

const gvl = new GVL();
//tcf api definition provided by iab to handle and read the tcstring by vendors and validator

//object to store consent provided by user
let user_iab_consent = {};
let user_gacm_consent = [];
user_iab_consent.purpose_consent = [];
user_iab_consent.purpose_legint = [];
user_iab_consent.legint = [];
user_iab_consent.consent = [];
user_iab_consent.feature_consent = [];

//tcModel that is encoded to form the tcString
const tcModel = new TCModel();
var encodedString = "default tc string...";
var acString = "";
const eventListeners = [];
//initializaation of cmp api instance that is used to read tcString by vendors and validator
let cmpApi;

//if tc string cookie for consent by user exists, read it, decode it and strore in the object
if (GDPR_Cookie.exists("wpl_tc_string")) {
  const decoded_consent = TCString.decode(GDPR_Cookie.read("wpl_tc_string"));
  user_iab_consent.purpose_consent = Array.from(
    decoded_consent.purposeConsents.set_
  );
  user_iab_consent.purpose_legint = Array.from(
    decoded_consent.purposeLegitimateInterests.set_
  );
  user_iab_consent.legint = Array.from(
    decoded_consent.vendorLegitimateInterests.set_
  );
  user_iab_consent.consent = Array.from(decoded_consent.vendorConsents.set_);
  user_iab_consent.feature_consent = Array.from(
    decoded_consent.specialFeatureOptins.set_
  );
}
if (GDPR_Cookie.exists("IABTCF_AddtlConsent")) {
  const [specVersion, consentedPart, disclosedPart] = GDPR_Cookie.read(
    "IABTCF_AddtlConsent"
  ).split("~");

  const consentedIds = consentedPart.split(".").map(Number);

  user_gacm_consent = consentedIds;
  if (user_gacm_consent.length == 1 && Number(user_gacm_consent[0]) == 0)
    user_gacm_consent = [];
}
//function to set switches of selected consents to on
(function ($) {
  // Select all input elements using the class
  $(".vendor-switch-handler.consent-switch").each(function () {
    // Get the value of the current element
    const value = $(this).val();

    // Check if the value is in the user_iab_consent.consent array
    if (user_iab_consent.consent.includes(Number(value))) {
      $(this).prop("checked", true); // Mark as checked
    } else {
      $(this).prop("checked", false); // Ensure it is unchecked
    }
  });
  $(".vendor-switch-handler.legint-switch").each(function () {
    const value = $(this).val();

    if (user_iab_consent.legint.includes(Number(value))) {
      $(this).prop("checked", true);
    } else {
      $(this).prop("checked", false);
    }
  });
  $(".purposes-switch-handler.consent-switch").each(function () {
    const value = $(this).val();

    if (user_iab_consent.purpose_consent.includes(Number(value))) {
      $(this).prop("checked", true);
    } else {
      $(this).prop("checked", false);
    }
  });
  $(".purposes-switch-handler.legint-switch").each(function () {
    const value = $(this).val();

    if (user_iab_consent.purpose_legint.includes(Number(value))) {
      $(this).prop("checked", true);
    } else {
      $(this).prop("checked", false);
    }
  });
  $(".special-features-switch-handler.consent-switch").each(function () {
    const value = $(this).val();

    if (user_iab_consent.feature_consent.includes(Number(value))) {
      $(this).prop("checked", true);
    } else {
      $(this).prop("checked", false);
    }
  });
  $(".vendor-all-switch-handler").each(function () {
    let flag = true;
    //venors which do no need consent and thier consent is not getting turned on when we turn on all vendors on swiitch
    const invlaid_vendor_consents = [
      46, 56, 63, 83, 126, 203, 205, 278, 279, 297, 308, 336, 415, 431, 466,
      502, 509, 551, 572, 597, 612, 706, 729, 751, 762, 772, 801, 838, 845, 853,
      872, 883, 892, 898, 911, 927, 925, 950, 953, 969, 1005, 1013, 1014, 1019,
      1041, 1044, 1075, 1129, 1160, 1169, 1170, 1172, 1187, 1203, 1204, 1208,
      1217, 1219, 1225, 1228, 1234, 1247, 1253, 1259, 1275, 1277, 1278, 1280,
      1285, 1284, 1300, 1302, 1306, 1307, 1308, 1310, 1311, 1333,
    ];
    // Loop through each element in allVendors
    for (let i = 0; i < iabtcf.data.allvendors.length; i++) {
      const vendor = iabtcf.data.allvendors[i];
      // Check if the vendor exists in the consentArray
      if (
        !user_iab_consent.consent.includes(vendor) &&
        !invlaid_vendor_consents.includes(vendor)
      ) {
        flag = false;
        break;
      }
    }
    for (let i = 0; i < iabtcf.data.allLegintVendors.length; i++) {
      const vendor = iabtcf.data.allLegintVendors[i];

      // Check if the vendor exists in the consentArray
      if (!user_iab_consent.legint.includes(vendor)) {
        flag = false;
        break;
      }
    }
    // If all vendors exist in consentArray and legitimate Array, return true
    if (flag) $(this).prop("checked", true);
    else $(this).prop("checked", false);
  });

  $(".purposes-all-switch-handler").each(function () {
    let flag = true;
    for (let i = 0; i < iabtcf.data.allPurposes.length; i++) {
      const purpose = iabtcf.data.allPurposes[i];
      if (!user_iab_consent.purpose_consent.includes(purpose)) {
        flag = false;
        break;
      }
    }
    for (let i = 0; i < iabtcf.data.allLegintPurposes.length; i++) {
      const vendor = iabtcf.data.allLegintPurposes[i];
      if (!user_iab_consent.purpose_legint.includes(vendor)) {
        flag = false;
        break;
      }
    }
    if (flag) $(this).prop("checked", true);
    else $(this).prop("checked", false);
  });

  $(".special-features-all-switch-handler").each(function () {
    let flag = true;
    for (let i = 0; i < iabtcf.data.allSpecialFeatures.length; i++) {
      const feature = iabtcf.data.allSpecialFeatures[i];
      if (!user_iab_consent.feature_consent.includes(feature)) {
        flag = false;
        break;
      }
    }
    if (flag) $(this).prop("checked", true);
    else $(this).prop("checked", false);
  });

  $(".gacm-vendor-switch-handler.consent-switch").each(function () {
    // Get the value of the current element
    const value = $(this).val();

    // Check if the value is in the user_iab_consent.consent array
    if (user_gacm_consent.includes(Number(value))) {
      $(this).prop("checked", true); // Mark as checked
    } else {
      $(this).prop("checked", false); // Ensure it is unchecked
    }
  });

  $(".gacm-vendor-all-switch-handler").each(function () {
    let flag = true;
    for (let i = 0; i < iabtcf.gacm_data.length - 1; i++) {
      const feature = iabtcf.gacm_data[i][0];
      if (!user_gacm_consent.includes(Number(feature))) {
        flag = false;
        break;
      }
    }
    if (flag) $(this).prop("checked", true);
    else $(this).prop("checked", false);
  });
})(jQuery);

//function to setup gvl once it has returned the promise and resolved it
gvl.readyPromise.then(() => {
  try {
    tcModel.gvl = gvl;
    tcModel.tcfPolicyVersion = gvl.tcfPolicyVersion;
    tcModel.publisherCountryCode = "IN";
    tcModel.version = 2;
    tcModel.cmpId = cmpId;
    tcModel.cmpVersion = cmpVersion;
    tcModel.gdprApplies = true;
    tcModel.isServiceSpecific = false;
    //initializing the cmp api
    if (tcModel && tcModel.gvl) {
      cmpApi = new CmpApi(cmpId, cmpVersion, false, {
        getTCData: (next, tcData, status) => {
          /*
           * If using with 'removeEventListener' command, add a check to see if tcData is not a boolean. */
          if (typeof tcData !== "boolean") {
            // tcData will be constructed via the TC string and can be added to here
            tcData.addtlConsent = acString;
          }

          // pass data and status along
          next(tcData, status);
        },
      });
    } else {
      console.error("GVL or TCModel is not ready");
    }
    if (GDPR_Cookie.exists("wpl_tc_string")) {
      updateTCModel();
    }
  } catch (error) {
    console.error("Error during CMP initialization:", error);
  }
});

function updateTCModel() {
  try {
    if (user_iab_consent.consent) {
      tcModel.vendorConsents.forEach((value, vendorId) => {
        tcModel.vendorConsents.unset(vendorId);
      });
      tcModel.vendorConsents.set(user_iab_consent.consent.map(Number));
    }
    if (user_iab_consent.legint) {
      tcModel.vendorLegitimateInterests.forEach((value, vendorId) => {
        tcModel.vendorLegitimateInterests.unset(vendorId);
      });
      tcModel.vendorLegitimateInterests.set(
        user_iab_consent.legint.map(Number)
      );
    }
    if (user_iab_consent.purpose_consent) {
      tcModel.purposeConsents.forEach((value, purposeId) => {
        tcModel.purposeConsents.unset(purposeId);
      });
      tcModel.purposeConsents.set(user_iab_consent.purpose_consent.map(Number));
    }
    if (user_iab_consent.purpose_legint) {
      tcModel.purposeLegitimateInterests.forEach((value, purposeId) => {
        tcModel.purposeLegitimateInterests.unset(purposeId);
      });
      tcModel.purposeLegitimateInterests.set(
        user_iab_consent.purpose_legint.map(Number)
      );
    }
    if (user_iab_consent.feature_consent) {
      tcModel.specialFeatureOptins.forEach((value, featureId) => {
        tcModel.specialFeatureOptins.unset(featureId);
      });
      tcModel.specialFeatureOptins.set(
        user_iab_consent.feature_consent.map(Number)
      );
    }
    //creating ac string for google additional consent mode

    // Part 1: Specification version number
    var specVersion = "2";
    // Part 3: List of user-consented vendors (ATP IDs from user_gacm_consent array)
    var consentedIds = user_gacm_consent.join(".");
    // Part 5: List of disclosed vendors (from gacm_data that are NOT in user_gacm_consent)
    var disclosedVendors = iabtcf.gacm_data
      .map((vendor) => vendor[0]) // Extract only the 0th element (vendor ID)
      .filter((vendorId) => !user_gacm_consent.includes(Number(vendorId)));
    var disclosedIds = disclosedVendors.join(".");

    // Create the AC string
    acString = `${specVersion}~${consentedIds}~dv.${disclosedIds}`;
    tcModel.addtlConsent = acString;
    // Encode the updated tcModel
    encodedString = TCString.encode(tcModel);
    //setting the cookie
    GDPR_Cookie.set("wpl_tc_string", encodedString, 365);
    GDPR_Cookie.set("IABTCF_AddtlConsent", acString, 365);

    user_iab_consent.tcString = encodedString;
    tcModel.tcString = encodedString;
    tcModel.addtlConsent = acString;

    // Update the CMP state with the new TC string so that validator, vendors know about update and can read it
    cmpApi.update(encodedString, true);
  } catch (error) {
    console.error("Error updating TCModel:", error);
  }
}

//function to update tcModel, generate tcString once user clicks on reject/decline button in consent banner
function rejectTCModel() {
  try {
    tcModel.vendorConsents.forEach((value, vendorId) => {
      tcModel.vendorConsents.unset(vendorId);
    });

    tcModel.vendorLegitimateInterests.forEach((value, vendorId) => {
      tcModel.vendorLegitimateInterests.unset(vendorId);
    });

    tcModel.purposeConsents.forEach((value, purposeId) => {
      tcModel.purposeConsents.unset(purposeId);
    });

    tcModel.purposeLegitimateInterests.forEach((value, purposeId) => {
      tcModel.purposeLegitimateInterests.unset(purposeId);
    });

    tcModel.specialFeatureOptins.forEach((value, featureId) => {
      tcModel.specialFeatureOptins.unset(featureId);
    });

    //creating ac string for google additional consent mode

    var specVersion = "2";

    user_gacm_consent = [];

    // Part 5: List of disclosed vendors (from gacm_data that are NOT in user_gacm_consent)
    var disclosedVendors = iabtcf.gacm_data.map((vendor) => vendor[0]);
    var disclosedIds = disclosedVendors.join(".");

    // Create the AC string
    acString = `${specVersion}~~dv.${disclosedIds}`;
    tcModel.addtlConsent = acString;

    // Encode the updated tcModel
    encodedString = TCString.encode(tcModel);
    user_iab_consent.tcString = encodedString;
    tcModel.tcString = encodedString;
    GDPR_Cookie.set("wpl_tc_string", encodedString, 365);
    GDPR_Cookie.set("IABTCF_AddtlConsent", acString, 365);

    // Update the CMP state with the new TC string so that validator, vendors know about update and can read it
    cmpApi.update(encodedString, true);
    jQuery(".vendor-switch-handler").prop("checked", false);
    jQuery(".gacm-vendor-switch-handler").prop("checked", false);
    jQuery(".gacm-vendor-all-switch-handler").prop("checked", false);
    jQuery(".vendor-all-switch-handler").prop("checked", false);
  } catch (error) {
    console.error("Error updating TCModel:", error);
  }
}

(function ($) {
  "use strict";
  /**
   *  the IAB requires CMPs to host their own vendor-list.json files.  This must
   *  be set before creating any instance of the GVL class.
   */

  $(".gdpr_action_button").click(function (e) {
    var elm = $(this);
    var button_action = elm.attr("data-gdpr_action");
    if (button_action == "accept") {
      tcModel.gvl.readyPromise.then(() => {
        updateTCModel();
      });
    }
    if (button_action == "reject") {
      tcModel.gvl.readyPromise.then(() => {
        rejectTCModel();
      });
    }
  });

  $(".vendor-all-switch-handler").click(function () {
    $(".vendor-all-switch-handler", this);
    if ($(this).is(":checked")) {
      $(".vendor-switch-handler").prop("checked", true);
      user_iab_consent.consent = [];
      user_iab_consent.legint = [];

      for (var i = 0; i < iabtcf.data.allvendors.length; i++) {
        user_iab_consent.consent.push(iabtcf.data.allvendors[i]);
      }

      for (var i = 0; i < iabtcf.data.allLegintVendors.length; i++) {
        user_iab_consent.legint.push(iabtcf.data.allLegintVendors[i]);
      }
    } else {
      $(".vendor-switch-handler").prop("checked", false);
      user_iab_consent.consent = [];
      user_iab_consent.legint = [];
    }
  });
  $(".vendor-switch-handler.consent-switch").click(function () {
    var val = $(this).val();
    if ($(this).is(":checked")) {
      user_iab_consent.consent.push(Number(val));
      $(this).prop("checked", true);
    } else {
      $(this).prop("checked", false);
      $(".vendor-all-switch-handler").prop("checked", false);
      user_iab_consent.consent.splice(
        user_iab_consent.consent.indexOf(Number(val)),
        1
      );
    }
  });
  $(".vendor-switch-handler.legint-switch").click(function () {
    var val = $(this).val();
    if ($(this).is(":checked")) {
      user_iab_consent.legint.push(Number(val));
      $(this).prop("checked", true);
    } else {
      $(this).prop("checked", false);
      $(".vendor-all-switch-handler").prop("checked", false);
      user_iab_consent.legint.splice(
        user_iab_consent.legint.indexOf(Number(val)),
        1
      );
    }
  });
  $(".purposes-all-switch-handler").click(function () {
    $(".purposes-all-switch-handler", this);
    if ($(this).is(":checked")) {
      $(".purposes-switch-handler").prop("checked", true);
      user_iab_consent.purpose_consent = [];
      user_iab_consent.purpose_legint = [];
      for (var i = 0; i < iabtcf.data.allPurposes.length; i++) {
        user_iab_consent.purpose_consent.push(iabtcf.data.allPurposes[i]);
      }
      for (var i = 0; i < iabtcf.data.allLegintPurposes.length; i++) {
        user_iab_consent.purpose_legint.push(iabtcf.data.allLegintPurposes[i]);
      }
    } else {
      $(".purposes-switch-handler").prop("checked", false);
      user_iab_consent.purpose_consent = [];
      user_iab_consent.purpose_legint = [];
    }
  });

  $(".purposes-switch-handler.consent-switch").click(function () {
    var val = $(this).val();
    if ($(this).is(":checked")) {
      user_iab_consent.purpose_consent.push(Number(val));
      $(this).prop("checked", true);
    } else {
      $(this).prop("checked", false);
      $(".purposes-all-switch-handler").prop("checked", false);
      user_iab_consent.purpose_consent.splice(
        user_iab_consent.purpose_consent.indexOf(Number(val)),
        1
      );
    }
  });

  $(".purposes-switch-handler.legint-switch").click(function () {
    var val = $(this).val();
    if ($(this).is(":checked")) {
      user_iab_consent.purpose_legint.push(Number(val));
      $(this).prop("checked", true);
    } else {
      $(this).prop("checked", false);
      $(".purposes-all-switch-handler").prop("checked", false);
      user_iab_consent.purpose_legint.splice(
        user_iab_consent.purpose_legint.indexOf(Number(val)),
        1
      );
    }
  });

  $(".special-features-all-switch-handler").click(function () {
    $(".special-features-all-switch-handler", this);
    if ($(this).is(":checked")) {
      $(".special-features-switch-handler").prop("checked", true);
      user_iab_consent.feature_consent = [];
      for (var i = 0; i < iabtcf.data.allSpecialFeatures.length; i++) {
        user_iab_consent.feature_consent.push(
          iabtcf.data.allSpecialFeatures[i]
        );
      }
    } else {
      $(".special-features-switch-handler").prop("checked", false);
      while (user_iab_consent.feature_consent.length) {
        user_iab_consent.feature_consent.pop();
      }
    }
  });

  $(".special-features-switch-handler.consent-switch").click(function () {
    var val = $(this).val();
    if ($(this).is(":checked")) {
      user_iab_consent.feature_consent.push(Number(val));
      $(this).prop("checked", true);
    } else {
      $(this).prop("checked", false);
      $(".special-features-all-switch-handler").prop("checked", false);
      user_iab_consent.feature_consent.splice(
        user_iab_consent.feature_consent.indexOf(Number(val)),
        1
      );
    }
  });
  $(".gacm-vendor-all-switch-handler").click(function () {
    $(".gacm-vendor-all-switch-handler", this);
    if ($(this).is(":checked")) {
      $(".gacm-vendor-switch-handler").prop("checked", true);
      user_gacm_consent = [];

      for (var i = 0; i < iabtcf.gacm_data.length - 1; i++) {
        user_gacm_consent.push(Number(iabtcf.gacm_data[i][0]));
      }
    } else {
      $(".gacm-vendor-switch-handler").prop("checked", false);
      user_gacm_consent = [];
    }
  });
  $(".gacm-vendor-switch-handler.consent-switch").click(function () {
    var val = $(this).val();
    if ($(this).is(":checked")) {
      user_gacm_consent.push(Number(val));
      $(this).prop("checked", true);
    } else {
      $(this).prop("checked", false);
      $(".gacm-vendor-all-switch-handler").prop("checked", false);
      user_gacm_consent.splice(user_gacm_consent.indexOf(Number(val)), 1);
    }
  });
})(jQuery);
