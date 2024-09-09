/**
 * Frontend JavaScript.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public
 * @author     wpeka <https://club.wpeka.com>
 */
import { TCModel, TCString, GVL } from "@iabtechlabtcf/core";

/**
 *  the IAB requires CMPs to host their own vendor-list.json files.  This must
 *  be set before creating any instance of the GVL class.
 */
GVL.baseUrl = "https://923b74fe37.nxcli.io/rgh/";

const gvl = new GVL();

gvl.readyPromise.then(() => {
  const data = {};
  const vendorMap = gvl.vendors;
  const purposeMap = gvl.purposes;
  const featureMap = gvl.features;
  const dataCategoriesMap = gvl.dataCategories;
  const specialPurposeMap = gvl.specialPurposes;
  const specialFeatureMap = gvl.specialFeatures;
  const purposeVendorMap = gvl.byPurposeVendorMap;

  var vendor_array = [],
    vendor_id_array = [],
    vendor_legint_id_array = [],
    data_categories_array = [],
    nayan = [];
  var feature_array = [],
    special_feature_id_array = [],
    special_feature_array = [],
    special_purpose_array = [];
  var purpose_id_array = [],
    purpose_legint_id_array = [],
    purpose_array = [],
    purpose_vendor_array = [];
  var purpose_vendor_count_array = [],
    feature_vendor_count_array = [],
    special_purpose_vendor_count_array = [],
    special_feature_vendor_count_array = [],
    legint_purpose_vendor_count_array = [],
    legint_feature_vendor_count_array = [];
  Object.keys(vendorMap).forEach((key) => {
    vendor_array.push(vendorMap[key]);
    vendor_id_array.push(vendorMap[key].id);
    if (vendorMap[key].legIntPurposes.length)
      vendor_legint_id_array.push(vendorMap[key].id);
  });
  data.vendors = vendor_array;
  data.allvendors = vendor_id_array;
  data.allLegintVendors = vendor_legint_id_array;

  Object.keys(featureMap).forEach((key) => {
    feature_array.push(featureMap[key]);
    feature_vendor_count_array.push(
      Object.keys(gvl.getVendorsWithFeature(featureMap[key].id)).length
    );
  });
  data.features = feature_array;
  data.featureVendorCount = feature_vendor_count_array;
  data.dataCategories = nayan;

  Object.keys(dataCategoriesMap).forEach((key) => {
    data_categories_array.push(dataCategoriesMap[key]);
  });
  data.dataCategories = data_categories_array;

  var legintCount = 0;
  const purposeLegint = new Map();
  Object.keys(purposeMap).forEach((key) => {
    purpose_array.push(purposeMap[key]);
    purpose_id_array.push(purposeMap[key].id);
    purpose_vendor_count_array.push(
      Object.keys(gvl.getVendorsWithConsentPurpose(purposeMap[key].id)).length
    );
    legintCount = Object.keys(
      gvl.getVendorsWithLegIntPurpose(purposeMap[key].id)
    ).length;
    legint_purpose_vendor_count_array.push(legintCount);
    if (legintCount) {
      purposeLegint.set(purposeMap[key].id, legintCount);
      purpose_legint_id_array.push(purposeMap[key].id);
    }
  });
  data.purposes = purpose_array;
  data.allPurposes = purpose_id_array;
  data.purposeVendorCount = purpose_vendor_count_array;
  data.allLegintPurposes = purpose_legint_id_array;
  data.legintPurposeVendorCount = legint_purpose_vendor_count_array;

  Object.keys(specialFeatureMap).forEach((key) => {
    special_feature_array.push(specialFeatureMap[key]);
    special_feature_id_array.push(specialFeatureMap[key].id);
    special_feature_vendor_count_array.push(
      Object.keys(gvl.getVendorsWithSpecialFeature(specialFeatureMap[key].id))
        .length
    );
  });
  data.specialFeatures = special_feature_array;
  data.allSpecialFeatures = special_feature_id_array;
  data.specialFeatureVendorCount = special_feature_vendor_count_array;

  Object.keys(specialPurposeMap).forEach((key) => {
    special_purpose_array.push(specialPurposeMap[key]);
    special_purpose_vendor_count_array.push(
      Object.keys(gvl.getVendorsWithSpecialPurpose(purposeMap[key].id)).length
    );
  });
  data.specialPurposes = special_purpose_array;
  data.specialPurposeVendorCount = special_purpose_vendor_count_array;

  Object.keys(purposeVendorMap).forEach((key) =>
    purpose_vendor_array.push(purposeVendorMap[key].legInt.size)
  );
  data.purposeVendorMap = purpose_vendor_array;
  data.secret_key = "sending_vendor_data";
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "test.php");
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
    }
  };
  xhr.setRequestHeader("Content-type", "application/json;charset=UTF-8");
  xhr.send(JSON.stringify(data));
});

// create a new TC string
const tcModel = new TCModel(gvl);
tcModel.cmpId = 1000; // test id
tcModel.cmpVersion = 1; // test version

tcModel.vendorConsents.set([4, 1, 8]);
tcModel.vendorLegitimateInterests.set([8, 10]);
tcModel.purposeConsents.set([2, 4, 6, 7]);
tcModel.purposeLegitimateInterests.set([2, 4, 7]);
tcModel.specialFeatureOptins.set([1, 2]);
tcModel.gvl.readyPromise.then(() => {
  const encodedString = TCString.encode(tcModel); // TC string encoded begins with 'C'
});
