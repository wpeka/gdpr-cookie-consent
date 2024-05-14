/**
 * Resources
 * https://github.com/InteractiveAdvertisingBureau/iabtcf-es
 * */

 import {CmpApi} from '@iabtcf/cmpapi';

 import {TCModel, TCString, GVL, Segment} from '@iabtcf/core';
 import UsprivacyString from '../ccpa/src/uspapi.js';
 const cmplzCMP = 332;
 const cmplzCMPVersion = 1;
//  const cmplzIsServiceSpecific = cmplz_tcf.isServiceSpecific == 1 ? true : false;
 const cmplzExistingLanguages = ['gl','eu','bg', 'ca', 'cs', 'da', 'de', 'el', 'es', 'et', 'fi', 'fr', 'hr', 'hu', 'it', 'ja', 'lt', 'lv', 'mt', 'nl', 'no', 'pl', 'pt', 'ro', 'ru', 'sk', 'sl', 'sr', 'sv', 'tr', 'zh',];
 let langCount = cmplzExistingLanguages.length;
 let cmplz_html_lang_attr =  document.documentElement.lang.length ? document.documentElement.lang.toLowerCase() : 'en';
 let cmplzLanguage = 'en';
 for (let i = 0; i < langCount; i++) {
	 let cmplzLocale = cmplzExistingLanguages[i];
	 //nb_no should be matched on no, not nb
	 if ( cmplz_html_lang_attr==='nb-no' ) {
		 cmplzLanguage = 'no';
		 break;
	 }
	 //needs to be exact match, as for example "ca" (catalan) occurs in "fr-ca", which should only match on "fr"
	 if ( cmplz_html_lang_attr.indexOf(cmplzLocale)===0 ) {
		 cmplzLanguage = cmplzLocale;
		 break;
	 }
 }
 if (cmplzLanguage==='eu') cmplzLanguage='eus';
 let cmplzLanguageJson;
 let dataCategories = [];
 let ACVendors = [];
//  let useAcVendors = cmplz_tcf.ac_mode;

 let onOptOutPolicyPage = document.getElementById('cmplz-tcf-us-vendor-container') !== null;
 let onOptInPolicyPage = document.getElementById('cmplz-tcf-vendor-container') !== null;

 /**
 * initialize the __tcfapi function and post message
 * https://github.com/InteractiveAdvertisingBureau/iabtcf-es/tree/master/modules/stub
 */

let ACVendorsUrl = cmplz_tcf.cmp_url + 'cmp/vendorlist/additional-consent-providers.csv';
let purposesUrl = cmplz_tcf.cmp_url+'cmp/vendorlist'+'/purposes-'+cmplzLanguage+'.json';
if (!cmplzExistingLanguages.includes(cmplzLanguage)) {
	cmplzLanguage = 'en';
	purposesUrl = cmplz_tcf.cmp_url + 'cmp/vendorlist' + '/vendor-list.json';
}

/**
 * Get a cookie by name
 * @param name
 * @returns {string}
 */

function cmplz_tcf_get_cookie(name) {
	if ( typeof document === "undefined" ) {
		return "";
	}
	let prefix = typeof complianz !== "undefined" ? complianz.prefix : 'cmplz_';
	const value = "; " + document.cookie;
	const parts = value.split("; " + prefix + name + "=");
	if ( parts.length === 2 ) {
		return parts.pop().split(";").shift();
	}
	return "";
}

/**
 * Add an event
 * @param event
 * @param selector
 * @param callback
 * @param context
 */
function cmplz_tcf_add_event(event, selector, callback, context) {
	document.addEventListener(event, e => {
		if ( e.target.closest(selector) ) {
			callback(e);
		}
	});
}

/**
 * Check if the element is hidden
 * @param el
 * @returns {boolean}
 */
function is_hidden(el) {
	return (el.offsetParent === null)
}

let tcModelLoadedResolve;
let tcfLanguageLoadedResolve;

let tcModelLoaded = new Promise(function(resolve, reject){
	tcModelLoadedResolve = resolve;
});
let tcfLanguageLoaded = new Promise(function(resolve, reject){
	tcfLanguageLoadedResolve = resolve;
});


const purposesPromise = fetch(purposesUrl, {
	method: "GET",
})
	.then(response => response.json())
	.then(data => {
		cmplzLanguageJson = data;
	})
	.catch(error => {
		console.log('Error:', error);
	});

Promise.all([acVendorsPromise, purposesPromise]).then(() => {
	tcfLanguageLoadedResolve();
});


bannerDataLoaded.then(()=>{

});

tcfLanguageLoaded.then(()=>{
	const storedTCString = cmplzGetTCString();
	const ACString = cmplzGetACString();
	GVL.baseUrl = cmplz_tcf.cmp_url + "cmp/vendorlist";
	dataCategories = cmplzLanguageJson.dataCategories;
	let gvl = new GVL(cmplzLanguageJson);
	let sourceGvl = gvl.clone();
	let tcModel = new TCModel(gvl);
	tcModel.publisherCountryCode = cmplz_tcf.publisherCountryCode;
	tcModel.version = 2;
	tcModel.cmpId = cmplzCMP;
	tcModel.cmpVersion = cmplzCMPVersion;
	tcModel.isServiceSpecific = cmplzIsServiceSpecific;
	tcModel.UseNonStandardStacks = 0; //A CMP that services multiple publishers sets this value to 0
	const cmpApi = new CmpApi(cmplzCMP, cmplzCMPVersion, cmplzIsServiceSpecific, {
		//https://github.com/InteractiveAdvertisingBureau/iabtcf-es/tree/master/modules/cmpapi#built-in-and-custom-commands
		'getTCData': (next, tcData, success) => {
			// tcData will be constructed via the TC string and can be added to here
			if ( tcData ) {
				tcData.addtlConsent = ACString;
			}

			// pass data along
			next(tcData, success);
		}
	});


	tcModel.gvl.readyPromise.then(() => {
		const json = tcModel.gvl.getJson();
		let vendors = json.vendors;
		let vendorIds = cmplzFilterVendors(vendors);
		tcModel.gvl.narrowVendorsTo(vendorIds);

		//update model with given consents
		try {
			tcModel = TCString.decode(storedTCString, tcModel);

			//update tcmodel to ensure gdpr applies is set
			cmplzSetTCString(tcModel, cmplzUIVisible() );
			ACVendors = updateACVendorsWithConsent(ACString, ACVendors);
		} catch (err) {}

		//get the given consents from the Google Extended vendors
		tcModelLoadedResolve();
	});

	Promise.all([bannerDataLoaded, tcModelLoaded]).then(()=> {
		insertVendorsInPolicy(tcModel.gvl.vendors, ACVendors);
		if (complianz.consenttype === 'optin'){
			if (cmplz_tcf.debug) console.log(tcModel);
			let date = new Date();
			/**
			 * If the TC String was created over a year ago, we clear it.
			 */
			if (Date.parse(tcModel.created) < date.getTime() - 365 * 24 * 60 * 60 * 1000) {
				cmplzSetTCString(null, cmplzUIVisible() );
			} else {
				cmplzSetTCString(tcModel, cmplzUIVisible() );
			}
		} else {
			if (cmplz_tcf.debug) console.log("not an optin tcf region");
			cmplzSetTCString(null, false );
		}
	});

	Promise.all([bannerLoaded, tcModelLoaded, tcfLanguageLoaded]).then(()=> {
		configureOptinBanner();
	});

	revoke.then(reload => {
		if (cmplz_is_tcf_region(complianz.region)) {
			revokeAllVendors(reload);
		}
	});


	/**
	 * Get vendors who only have one of these purposes
	 * @param type
	 * @param vendors
	 * @param category_purposes
	 * @returns {[]}
	 */
	// function cmplzFilterVendorsBy(type, vendors, category_purposes) {
	// 	let output = [];
	// 	for (let key in vendors) {
	// 		if (vendors.hasOwnProperty(key)) {
	// 			const vendor = vendors[key];
	// 			//for each vendor purpose, check if it exists in the category purposes list. If not, don't add this vendor
	// 			let allPurposesAreCategoryPurpose = true;
	// 			const vendorProperties = vendor[type];
	// 			for (let p_key in vendorProperties) {
	// 				if (vendorProperties.hasOwnProperty(p_key)) {
	// 					const purpose = vendorProperties[p_key];
	// 					const inPurposeArray = category_purposes.includes(purpose);
	// 					if (!inPurposeArray) {
	// 						allPurposesAreCategoryPurpose = false;
	// 					}
	// 				}
	// 			}
	// 			const inOutPutArray = output.includes(vendor.id);
	// 			if (!inOutPutArray && allPurposesAreCategoryPurpose) {
	// 				output.push(vendor.id);
	// 			}
	// 		}
	// 	}
	// 	return output;
	// }

	/**
	 * Get thet TC String
	 * @returns {string}
	 */
	function cmplzGetTCString() {
		let user_policy_id = cmplz_tcf_get_cookie('policy_id');
		if ( !user_policy_id || (typeof complianz!=='undefined' && complianz.current_policy_id !== user_policy_id)  ) {
			if (localStorage.cmplz_tcf_consent) localStorage.removeItem('cmplz_tcf_consent');
		}
		return window.localStorage.getItem('cmplz_tcf_consent');
	}
	/**
	 * Set the tc string, and update the api if needed
	 * @param tcModel
	 * @param uiVisible
	 */
	function cmplzSetTCString( tcModel, uiVisible ) {
		cmplzSetACString();
		let encodedTCString = null;
		if ( tcModel ) {
			tcModel.created = cmplzRemoveTime(tcModel.lastUpdated);
			tcModel.lastUpdated = cmplzRemoveTime(tcModel.lastUpdated);
			encodedTCString = TCString.encode(tcModel);
		}
		// __tcfapi('getTCData', 2, (tcData, success) => {}, [tcModel.vendors]);

		cmpApi.update(encodedTCString, uiVisible);
		window.localStorage.setItem('cmplz_tcf_consent', encodedTCString);
	}

});
