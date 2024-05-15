import {TCModel, TCString, GVL} from '@iabtcf/core';


/**
*  the IAB requires CMPs to host their own vendor-list.json files.  This must
*  be set before creating any instance of the GVL class.
*/
// GVL.baseUrl = "https://923b74fe37.nxcli.io/rgh/";
GVL.baseUrl = "http://localhost:8888/wordpress/";

const gvl = new GVL();

console.log("Here");
console.log(gvl);

// create a new TC string
const tcModel = new TCModel(gvl);
tcModel.cmpId = 332;
tcModel.cmpVersion = 1;


// Some fields will not be populated until a GVL is loaded
tcModel.gvl.readyPromise.then(() => {


 // Set values on tcModel...


 const encodedString = TCString.encode(tcModel);


 console.log(encodedString); // TC string encoded begins with 'C'


});
