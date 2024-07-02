/*

This function is used for generating the dynamic pdf (Proof of consent) it utilizes js-pdf library
along with autotable to dynamically display proof of consnet.
@since 3.0.0

*/
function generatePDF(
  date,
  ipAddress,
  country,
  consentStatus,
  tcString,
  siteaddress,
  preferences,
  cookieData,
) {
  event.preventDefault();

  // Initialize an array to store filtered analytics cookies
  function filterCookiesByCategory(cookieData, category) {
    const filteredCookies = [];

    // Iterate through the numeric keys in cookieData
    for (const key in cookieData) {
      // Check if the key is numeric
      if (!isNaN(key)) {
        // Get the object at this index
        const cookie = cookieData[key];

        // Check if the object has the specified category
        if (cookie.category === category) {
          // Push an array containing the desired properties to the filtered array
          filteredCookies.push([
            cookie.name,
            cookie.duration,
            cookie.description,
          ]);
        }
      }
    }

    return filteredCookies;
  }

  // Usage example:
  const analyticsCookies = filterCookiesByCategory(cookieData, "Analytics");
  const marketingCookies = filterCookiesByCategory(cookieData, "Marketing");
  const unclassifiedCookies = filterCookiesByCategory(cookieData,"Unclassified");
  const preferencesCookies = filterCookiesByCategory(cookieData, "Preferences");

  const websiteUrl = window.location.hostname;
  const {jsPDF} = window.jspdf;
  const doc = new jsPDF("p", "mm", "a4"); // Create A4 size PDF
  const pageWidth = doc.internal.pageSize.getWidth();
  const fontSizeHeading = 24;
  const text = "Proof of Consent";

  // Calculate the width of the text
  const textWidth = doc.getStringUnitWidth(text) * fontSizeHeading / doc.internal.scaleFactor;

  // Calculate the X coordinate to center-align the text
  const centerX = (doc.internal.pageSize.getWidth() - textWidth) / 2;

  // Set the font size and position the text
  doc.setFontSize(fontSizeHeading);
  doc.text(text, centerX, 20);
  // Other text
  const fontSizeText = 12;
  doc.setFontSize(fontSizeText);

  doc.setFont(undefined, "bold"); // Set font type to bold
  doc.text("Consent Date: ", 10, 35);
  doc.setFont(undefined, "normal"); // Reset font type to normal
  doc.text(date, 40, 35); // Display the URL in normal font

  doc.setFont(undefined, "bold"); // Set font type to bold
  doc.text("Website URL:", 10, 50);
  doc.setFont(undefined, "normal"); // Reset font type to normal
  doc.text(websiteUrl, 39, 50); // Display the URL in normal font

  doc.setFont(undefined, "bold"); // Set font type to bold
  doc.text("IP Address: ", 10, 65);
  doc.setFont(undefined, "normal"); // Reset font type to normal
  doc.text(ipAddress, 35, 65);

  doc.setFont(undefined, "bold"); // Set font type to bold
  doc.text("Country: ", 10, 80);
  doc.setFont(undefined, "normal"); // Reset font type to normal
  doc.text(country, 29, 80);

  doc.setFont(undefined, "bold"); // Set font type to bold
  doc.text("Consent Status:", 10, 95);
  doc.setFont(undefined, "normal"); // Reset font type to normal
  doc.text(consentStatus, 44,95);
 
  doc.setFont(undefined, "bold"); // Set font type to bold
  doc.text("TC String:", 10, 110);
  doc.setFont(undefined, "normal"); // Reset font type to normal
  doc.text(tcString, 32,110);

  if(siteaddress){
    doc.setFont(undefined, "bold"); // Set font type to bold
    doc.text("Forwarded From:", 10, 125);
    doc.setFont(undefined, "normal"); // Reset font type to normal
    doc.text(siteaddress, 48,125);

    const fontSizeSubheading = 16;
  doc.setFontSize(fontSizeSubheading);
  doc.setFont(undefined, "bold");
  doc.text("Cookie Consent Details:", 10, 140);
  doc.setFont(undefined, "normal"); // Reset font type to normal
  doc.setFontSize(fontSizeText);
  if (preferences) {
    let startY = 170; // Initial startY for content

    // Check and display status for Necessary
    if (preferences.necessary === "yes") {
      doc.setFont(undefined, "bold");
      doc.text("Necessary:", 10, startY);
      doc.setFont(undefined, "normal");
      doc.text(" Always Active", 32, startY);
    } else {
      doc.setFont(undefined, "bold");
      doc.text("Necessary:", 10, startY);
      doc.setFont(undefined, "normal");
      doc.text("Not Active", 32, startY);
    }
    doc.autoTable({
      head: [["Cookie", "Duration", "Description"]],
      body: [
        ["wpl_user_preference", "1 year", "WP Cookie Consent Preferences."],
      ],
      startY: startY + 5,
      theme: "grid", // Add grid lines to the table
      styles: {fontSize: fontSizeText},
      margin:{left:10,top:0},
      columnStyles: {
        0: { cellWidth: 45 }, // Set column width for the first column to 30%
        1: { cellWidth: 45 }, // Set column width for the second column to 30%
        2: { cellWidth: 70 }, // Set column width for the third column to 40%
      },
      headStyles : {fillColor:[51,153,255]},
      tableWidth:'wrap'
    });
    startY = doc.previousAutoTable.finalY +10;

    // Check and display status for Analytics
    if (preferences.analytics === "yes") {
      doc.setFont(undefined, "bold");
      doc.text("Analytics:", 10, startY);
      doc.setFont(undefined, "normal");
      doc.text("Active", 31, startY);
    } else {
      doc.setFont(undefined, "bold");
      doc.text("Analytics:", 10, startY);
      doc.setFont(undefined, "normal");
      doc.text("Not Active", 31, startY);
    }
    if (analyticsCookies.length > 0) {
      doc.autoTable({
        head: [["Cookie", "Duration", "Description"]],
        body: analyticsCookies,
        startY: startY + 5,
        theme: "grid", // Add grid lines to the table
        styles: {fontSize: fontSizeText},
        columnStyles: {
            0: { cellWidth: 45 }, // Set column width for the first column to 30%
            1: { cellWidth: 45 }, // Set column width for the second column to 30%
            2: { cellWidth: 70 }, // Set column width for the third column to 40%
          },
        headStyles : {fillColor:[51,153,255]},
        margin:{left:10,top:0},
        tableWidth:'wrap'
      });
      startY = doc.previousAutoTable.finalY + 10 ;
    }else{
      startY += 10;
    }

    // Check and display status for Marketing
    if (preferences.marketing === "yes") {
      doc.setFont(undefined, "bold");
      doc.text("Marketing:", 10, startY);
      doc.setFont(undefined, "normal");
      doc.text(" Active", 32, startY);
    } else {
      doc.setFont(undefined, "bold");
      doc.text("Marketing:", 10, startY);
      doc.setFont(undefined, "normal");
      doc.text("Not Active", 32, startY);
    }
    if (marketingCookies.length > 0) {
      doc.autoTable({
        head: [["Cookie", "Duration", "Description"]],
        body: marketingCookies,
        startY: startY + 5,
        theme: "grid", // Add grid lines to the table
        styles: {fontSize: fontSizeText},
        margin:{left:10,top:0},
        columnStyles: {
            0: { cellWidth: 45 }, // Set column width for the first column to 30%
            1: { cellWidth: 45 }, // Set column width for the second column to 30%
            2: { cellWidth: 70 }, // Set column width for the third column to 40%
          },
          headStyles : {fillColor:[51,153,255]},
          tableWidth:'wrap'
      });
      startY = doc.previousAutoTable.finalY + 10;
    }else{
      startY += 10;
    }

    // Check and display status for Preferences
    if (preferences.preferences === "yes") {
      doc.setFont(undefined, "bold");
      doc.text("Preferences:", 10, startY);
      doc.setFont(undefined, "normal");
      doc.text("Active", 36, startY);
    } else {
      doc.setFont(undefined, "bold");
      doc.text("Preferences:", 10, startY);
      doc.setFont(undefined, "normal");
      doc.text("Not Active", 36, startY);
    }
    if (preferencesCookies.length > 0) {
      doc.autoTable({
        head: [["Cookie", "Duration", "Description"]],
        body: preferencesCookies,
        startY: startY + 5,
        theme: "grid", // Add grid lines to the table
        styles: {fontSize: fontSizeText},
        margin:{left:10,top:0},
        columnStyles: {
            0: { cellWidth: 45 }, // Set column width for the first column to 30%
            1: { cellWidth: 45 }, // Set column width for the second column to 30%
            2: { cellWidth: 70 }, // Set column width for the third column to 40%
          },
          headStyles : {fillColor:[51,153,255]},
          tableWidth:'wrap'
      });
      startY = doc.previousAutoTable.finalY + 10;
    }else{
      startY += 10;
    }

    // Check and display status for Unclassified
        doc.setFont(undefined, "bold");
        doc.text("Unclassified:", 10, startY);
        doc.setFont(undefined, "normal");
        doc.text(preferences.unclassified === "yes" ? "Active" : "Not Active", 37, startY);

    if (unclassifiedCookies.length > 0) {
      doc.autoTable({
        head: [["Cookie", "Duration", "Description"]],
        body: unclassifiedCookies,
        startY: startY + 5,
        theme: "grid", // Add grid lines to the table
        styles: {fontSize: fontSizeText},
        margin:{left:10,top:0},
        columnStyles: {
            0: { cellWidth: 45 }, // Set column width for the first column to 30%
            1: { cellWidth: 45 }, // Set column width for the second column to 30%
            2: { cellWidth: 70 }, // Set column width for the third column to 40%
          },
          headStyles : {fillColor:[51,153,255]},
          tableWidth:'wrap'
      });
      startY = doc.previousAutoTable.finalY + 10;

    }else{
      startY += 10;
    }
  }
  }
  else{
    // Subheading for Cookie Details
  const fontSizeSubheading = 16;
  doc.setFontSize(fontSizeSubheading);
  doc.setFont(undefined, "bold");
  doc.text("Cookie Consent Details:", 10, 125);
  doc.setFont(undefined, "normal"); // Reset font type to normal
  doc.setFontSize(fontSizeText);
  if (preferences) {
    let startY = 135; // Initial startY for content

    // Check and display status for Necessary
    if (preferences.necessary === "yes") {
      doc.setFont(undefined, "bold");
      doc.text("Necessary:", 10, startY);
      doc.setFont(undefined, "normal");
      doc.text(" Always Active", 32, startY);
    } else {
      doc.setFont(undefined, "bold");
      doc.text("Necessary:", 10, startY);
      doc.setFont(undefined, "normal");
      doc.text("Not Active", 32, startY);
    }
    doc.autoTable({
      head: [["Cookie", "Duration", "Description"]],
      body: [
        ["wpl_user_preference", "1 year", "WP Cookie Consent Preferences."],
      ],
      startY: startY + 5,
      theme: "grid", // Add grid lines to the table
      styles: {fontSize: fontSizeText},
      margin:{left:10,top:0},
      columnStyles: {
        0: { cellWidth: 45 }, // Set column width for the first column to 30%
        1: { cellWidth: 45 }, // Set column width for the second column to 30%
        2: { cellWidth: 70 }, // Set column width for the third column to 40%
      },
      headStyles : {fillColor:[51,153,255]},
      tableWidth:'wrap'
    });
    startY = doc.previousAutoTable.finalY +10;

    // Check and display status for Analytics
    if (preferences.analytics === "yes") {
      doc.setFont(undefined, "bold");
      doc.text("Analytics:", 10, startY);
      doc.setFont(undefined, "normal");
      doc.text("Active", 31, startY);
    } else {
      doc.setFont(undefined, "bold");
      doc.text("Analytics:", 10, startY);
      doc.setFont(undefined, "normal");
      doc.text("Not Active", 31, startY);
    }
    if (analyticsCookies.length > 0) {
      doc.autoTable({
        head: [["Cookie", "Duration", "Description"]],
        body: analyticsCookies,
        startY: startY + 5,
        theme: "grid", // Add grid lines to the table
        styles: {fontSize: fontSizeText},
        columnStyles: {
            0: { cellWidth: 45 }, // Set column width for the first column to 30%
            1: { cellWidth: 45 }, // Set column width for the second column to 30%
            2: { cellWidth: 70 }, // Set column width for the third column to 40%
          },
        headStyles : {fillColor:[51,153,255]},
        margin:{left:10,top:0},
        tableWidth:'wrap'
      });
      startY = doc.previousAutoTable.finalY + 10 ;
    }else{
      startY += 10;
    }

    // Check and display status for Marketing
    if (preferences.marketing === "yes") {
      doc.setFont(undefined, "bold");
      doc.text("Marketing:", 10, startY);
      doc.setFont(undefined, "normal");
      doc.text(" Active", 32, startY);
    } else {
      doc.setFont(undefined, "bold");
      doc.text("Marketing:", 10, startY);
      doc.setFont(undefined, "normal");
      doc.text("Not Active", 32, startY);
    }
    if (marketingCookies.length > 0) {
      doc.autoTable({
        head: [["Cookie", "Duration", "Description"]],
        body: marketingCookies,
        startY: startY + 5,
        theme: "grid", // Add grid lines to the table
        styles: {fontSize: fontSizeText},
        margin:{left:10,top:0},
        columnStyles: {
            0: { cellWidth: 45 }, // Set column width for the first column to 30%
            1: { cellWidth: 45 }, // Set column width for the second column to 30%
            2: { cellWidth: 70 }, // Set column width for the third column to 40%
          },
          headStyles : {fillColor:[51,153,255]},
          tableWidth:'wrap'
      });
      startY = doc.previousAutoTable.finalY + 10;
    }else{
      startY += 10;
    }

    // Check and display status for Preferences
    if (preferences.preferences === "yes") {
      doc.setFont(undefined, "bold");
      doc.text("Preferences:", 10, startY);
      doc.setFont(undefined, "normal");
      doc.text("Active", 36, startY);
    } else {
      doc.setFont(undefined, "bold");
      doc.text("Preferences:", 10, startY);
      doc.setFont(undefined, "normal");
      doc.text("Not Active", 36, startY);
    }
    if (preferencesCookies.length > 0) {
      doc.autoTable({
        head: [["Cookie", "Duration", "Description"]],
        body: preferencesCookies,
        startY: startY + 5,
        theme: "grid", // Add grid lines to the table
        styles: {fontSize: fontSizeText},
        margin:{left:10,top:0},
        columnStyles: {
            0: { cellWidth: 45 }, // Set column width for the first column to 30%
            1: { cellWidth: 45 }, // Set column width for the second column to 30%
            2: { cellWidth: 70 }, // Set column width for the third column to 40%
          },
          headStyles : {fillColor:[51,153,255]},
          tableWidth:'wrap'
      });
      startY = doc.previousAutoTable.finalY + 10;
    }else{
      startY += 10;
    }

    // Check and display status for Unclassified
        doc.setFont(undefined, "bold");
        doc.text("Unclassified:", 10, startY);
        doc.setFont(undefined, "normal");
        doc.text(preferences.unclassified === "yes" ? "Active" : "Not Active", 37, startY);

    if (unclassifiedCookies.length > 0) {
      doc.autoTable({
        head: [["Cookie", "Duration", "Description"]],
        body: unclassifiedCookies,
        startY: startY + 5,
        theme: "grid", // Add grid lines to the table
        styles: {fontSize: fontSizeText},
        margin:{left:10,top:0},
        columnStyles: {
            0: { cellWidth: 45 }, // Set column width for the first column to 30%
            1: { cellWidth: 45 }, // Set column width for the second column to 30%
            2: { cellWidth: 70 }, // Set column width for the third column to 40%
          },
          headStyles : {fillColor:[51,153,255]},
          tableWidth:'wrap'
      });
      startY = doc.previousAutoTable.finalY + 10;

    }else{
      startY += 10;
    }
  }
  }
  doc.save("generated-pdf.pdf");
}