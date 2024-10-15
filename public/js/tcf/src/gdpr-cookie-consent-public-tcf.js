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
GVL.baseUrl = "https://eadn-wc01-12578700.nxedge.io/cdn/rgh/";

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
      const feature = iabtcf.gacm_data[i];
      if (!user_gacm_consent.includes(feature)) {
        flag = false;
        break;
      }
    }
    if (flag) $(this).prop("checked", true);
    else $(this).prop("checked", false);
  });
})(jQuery);

function checkPing() {
  return new Promise((resolve) => {
    if (typeof __tcfapi === "function") {
      __tcfapi("ping", 2, (response) => {
        if (response && response.gdprApplies !== undefined) {
          console.log("Ping response:", response);
          resolve(true);
        } else {
          console.error("Ping response is invalid:", response);
          resolve(false);
        }
      });
    } else {
      console.error("__tcfapi is not defined");
      resolve(false);
    }
  });
}
function checkAddEventListener() {
  return new Promise((resolve) => {
    if (typeof __tcfapi === "function") {
      __tcfapi("addEventListener", 2, (response, success) => {
        if (success) {
          console.log("addEventListener is working:", response);
          resolve(true);
        } else {
          console.error("Failed to add event listener to __tcfapi");
          resolve(false);
        }
      });
    } else {
      console.error("__tcfapi is not defined");
      resolve(false);
    }
  });
}
let eventListener;

function checkRemoveEventListener() {
  return new Promise((resolve) => {
    if (typeof __tcfapi === "function") {
      // Define the event listener once outside the API calls
      eventListener = (response) => {
        console.log(
          "Event listener triggered (should not happen after removal):",
          response
        );
      };

      // Add the event listener
      __tcfapi("addEventListener", 2, (response, success) => {
        if (success) {
          console.log("Event listener added successfully.");

          // Remove the event listener after it was added
          __tcfapi(
            "removeEventListener",
            2,
            (removeSuccess) => {
              if (removeSuccess) {
                console.log("removeEventListener is working.");

                // Simulate event trigger after removal
                setTimeout(() => {
                  __tcfapi(
                    "addEventListener",
                    2,
                    (triggerResponse, triggerSuccess) => {
                      if (!triggerSuccess) {
                        console.log(
                          "Event listener successfully removed and no longer triggers."
                        );
                        resolve(true);
                      } else {
                        console.error(
                          "Event listener was not removed properly."
                        );
                        resolve(false);
                      }
                    },
                    eventListener
                  );
                }, 1000);
              } else {
                console.error(
                  "Failed to remove the event listener from __tcfapi."
                );
                resolve(false);
              }
            },
            eventListener
          ); // Pass the same listener reference
        } else {
          console.error("Failed to add event listener to __tcfapi.");
          resolve(false);
        }
      });
    } else {
      console.error("__tcfapi is not defined.");
      resolve(false);
    }
  });
}

// setTimeout(async () => {
//   const pingSuccess = await checkPing();
//   const addListenerSuccess = await checkAddEventListener();
//   const removeListenerSuccess = await checkRemoveEventListener();

//   if (pingSuccess && addListenerSuccess && removeListenerSuccess) {
//     console.log("All CMP API commands returned a correct response.");
//   } else {
//     console.error(
//       "One or more CMP API commands did not return a correct response."
//     );
//   }
// }, 5000);
const customCommands = {
  getTCData: function (callback) {
    // Your custom logic for getting TC data
    // console.log("Command", command, "version", version, "callback", callback);
    getTCData(callback, ""); // Call your original getTCData function here
  },
};
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
    cmpApi = new CmpApi(cmpId, cmpVersion, false, customCommands);
    if (GDPR_Cookie.exists("wpl_tc_string")) {
      updateTCModel();
    }
  } catch (error) {
    console.error("Error during CMP initialization:", error);
  }
});

window.__tcfapi = function (command, version, callback, parameter = "") {
  switch (command) {
    case "getTCData":
      if (version === 2) {
        getTCData(callback, parameter);
      } else {
        console.warn(`Unsupported version: ${version} for getTCData`);
        callback({}, false);
      }
      break;

    case "ping":
      if (version === 2) {
        callback(
          {
            gdprApplies: tcModel.gdprApplies,
            cmpLoaded: true,
            cmpStatus: "loaded",
            displayStatus: "visible",
          },
          true
        );
      } else {
        console.warn(`Unsupported version: ${version} for ping`);
        callback({}, false);
      }
      break;

    case "addEventListener":
      if (version === 2) {
        addEventListener(callback, parameter);
      } else {
        console.warn(`Unsupported version: ${version} for addEventListener`);
        callback({}, false);
      }
      break;

    case "removeEventListener":
      if (version === 2) {
        removeEventListener(callback, parameter);
      } else {
        console.warn(`Unsupported version: ${version} for removeEventListener`);
        callback({}, false);
      }
      break;

    default:
      console.warn(`Unsupported command: ${command}`);
      callback({}, false);
  }
};

// Ensure the queue exists
window.__tcfapi.queue = [];
window.__tcfapi.loaded = true;

const convertToObject = (set, maxId) => {
  const result = {};
  for (let i = 1; i <= maxId; i++) {
    result[i] = set.has(i);
  }
  return result;
};

function getTCData(callback, parameter) {
  try {
    if (!tcModel || !tcModel.gvl) {
      console.error("TCModel or GVL is not ready.");
      callback({}, false);
      return;
    }

    // Encode the TCModel into a TC string
    const tcString = TCString.encode(tcModel);
    const transformedPurpose = {
      consents: convertToObject(
        tcModel.purposeConsents.set_,
        tcModel.purposeConsents.maxId_
      ),
      legitimateInterests: convertToObject(
        tcModel.purposeLegitimateInterests.set_,
        tcModel.purposeLegitimateInterests.maxId_
      ),
    };
    const transformedVendor = {
      consents: convertToObject(
        tcModel.vendorConsents.set_,
        tcModel.vendorConsents.maxId_
      ),
      legitimateInterests: convertToObject(
        tcModel.vendorLegitimateInterests.set_,
        tcModel.vendorLegitimateInterests.maxId_
      ),
    };
    const transformedPublisher = {
      consents: convertToObject(
        tcModel.publisherConsents.set_,
        tcModel.publisherConsents.maxId_
      ),
      legitimateInterests: convertToObject(
        tcModel.publisherLegitimateInterests.set_,
        tcModel.publisherLegitimateInterests.maxId_
      ),
    };
    // Prepare the TCData object
    const tcData = {
      addtlConsent: acString,
      tcString: tcString,
      tcfPolicyVersion: tcModel.tcfPolicyVersion,
      cmpId: tcModel.cmpId,
      cmpVersion: tcModel.cmpVersion,
      gdprApplies: tcModel.gdprApplies,
      eventStatus: "tcloaded",
      cmpStatus: "loaded",
      isServiceSpecific: tcModel.isServiceSpecific,
      useNonStandardTexts: tcModel.useNonStandardTexts,
      purposeOneTreatment: tcModel.purposeOneTreatment,
      publisherCC: tcModel.publisherCountryCode,
      purpose: transformedPurpose,
      vendor: transformedVendor,
      specialFeatureOptins: convertToObject(
        tcModel.specialFeatureOptins.set_,
        tcModel.specialFeatureOptins.maxId_
      ),
      publisher: transformedPublisher,
    };

    // Return the TCData object
    callback(tcData, true);
  } catch (error) {
    console.error("Error in getTCData:", error);
    callback({}, false);
  }
}

//function to update tcModel, generate tcString once user clicks on accept button in consent banner
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
      .filter((vendorId) => !user_gacm_consent.includes(vendorId));
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
        user_gacm_consent.push(iabtcf.gacm_data[i]);
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
