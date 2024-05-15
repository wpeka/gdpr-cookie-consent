import {TCModel, TCString, GVL} from '@iabtcf/core';


/**
*  the IAB requires CMPs to host their own vendor-list.json files.  This must
*  be set before creating any instance of the GVL class.
*/
// GVL.baseUrl = "https://923b74fe37.nxcli.io/rgh/";
GVL.baseUrl = "http://localhost:8888/wordpress/";

const gvl = new GVL();

console.log("Here is the GVL object");
console.log(gvl);

// create a new TC string
const tcModel = new TCModel(gvl);
tcModel.cmpId = 2; // test id 
tcModel.cmpVersion = 1; // test version 

console.log("Here is the tcModel object :");
console.log(tcModel);
tcModel.vendorConsents.set([4, 1]);

// Some fields will not be populated until a GVL is loaded
tcModel.gvl.readyPromise.then(() => {


 // Set values on tcModel...


 const encodedString = TCString.encode(tcModel);


 console.log(encodedString); // TC string encoded begins with 'C'


});
