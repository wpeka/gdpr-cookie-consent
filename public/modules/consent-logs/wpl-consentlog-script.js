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
  cookieData
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
  const necessaryCookies = filterCookiesByCategory(cookieData, "Necessary");
  const analyticsCookies = filterCookiesByCategory(cookieData, "Analytics");
  const marketingCookies = filterCookiesByCategory(cookieData, "Marketing");
  const unclassifiedCookies = filterCookiesByCategory(
    cookieData,
    "Unclassified"
  );
  const preferencesCookies = filterCookiesByCategory(cookieData, "Preferences");

  const websiteUrl = window.location.hostname;
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF("p", "mm", "a4"); // Create A4 size PDF
  const pageWidth = doc.internal.pageSize.getWidth();
  const fontSizeHeading = 24;
  const text = "Proof of Consent";

  // Calculate the width of the text
  const textWidth =
    (doc.getStringUnitWidth(text) * fontSizeHeading) / doc.internal.scaleFactor;

  // Calculate the X coordinate to center-align the text
  const centerX = (doc.internal.pageSize.getWidth() - textWidth) / 2;

  // Set the font size and position the text
  doc.setFontSize(fontSizeHeading);
  doc.text(text, centerX, 20);
  doc.setTextColor(0, 0, 0); // Set text color to black
  // Other text
  const fontSizeText = 11;
  doc.setFontSize(fontSizeText);
  const tableData = [
    ["Consent Date", date],
    ["Website URL",websiteUrl],
    ["IP Address", ipAddress],
    ["Country", country],
    ["Consent Status", consentStatus],
  ];
  if (siteaddress) {
    tableData.push(["Forwarded From", siteaddress]);
  }

  // Create the table using autoTable
  doc.autoTable({
    startY: 30, // Position of the table
    // head: [["Field", "Value"]], // Table header
    body: tableData, // Table data
    theme: "grid", // Table theme (grid-like borders)
    styles: {
      lineColor: [0, 0, 0], // Black border color
      lineWidth: 0.2, // Border width
      textColor: [0, 0, 0], // Black text color
      fontSize: 11, // Font size for table content
    },
    columnStyles: {
      0: { cellWidth: 50, halign: "left", textColor: [0, 0, 0] }, // Left column (Field)
      1: { halign: "left", textColor: [0, 0, 0] }, // Right column (Value)
    },
  });

  if(!siteaddress) {
    // Subheading for Cookie Details
    const fontSizeSubheading = 16;
    doc.setFontSize(fontSizeSubheading);
    doc.setFont(undefined, "bold");
    
    // Center align "Cookie Consent Details"
    const pageWidth = doc.internal.pageSize.width;
    const cookieHeading = "Cookie Consent Details:";
    // const textWidth = doc.getTextWidth(cookieHeading);
    doc.text(cookieHeading, 15, 85);
    // doc.text(cookieHeading, (pageWidth - textWidth) / 2, 85); // Center the text on the page
    
    doc.setFont(undefined, "normal"); // Reset font type to normal
    doc.setFontSize(fontSizeText);
    if (preferences) {
      let startY = 100; // Initial startY for content
    
      // Check and display status for Necessary
      if (preferences.necessary === "yes") {
        doc.setFont(undefined, "bold");
        doc.text("Necessary:", 15, startY); // Adjusted x-coordinate from 12 to 15
        doc.setFont(undefined, "normal");
        doc.text("Always Active", 40, startY); // Adjusted x-coordinate from 32 to 40
      } else {
        doc.setFont(undefined, "bold");
        doc.text("Necessary:", 15, startY); // Adjusted x-coordinate from 10 to 15
        doc.setFont(undefined, "normal");
        doc.text("Not Active", 38, startY); // Adjusted x-coordinate from 32 to 40
      }
      // Align the table with the previous table
      doc.autoTable({
        head: [["Duration", "Cookie", "Description"]],
        body: [
          ["wpl_user_preference", "1 year", "WP Cookie Consent Preferences."],
        ],
        startY: startY + 5, // Position below the Necessary text
        theme: "grid",
        styles: {
          fontSize: fontSizeText,
          halign: "left",
          valign: "middle",
          lineWidth: 0.2,
        },
        columnStyles: {
          0: { cellWidth: 65 },
          1: { cellWidth: 50 },
          2: { cellWidth: 69 },
        },
        headStyles: {
          fillColor: [164, 194, 244],
          textColor: [0, 0, 0],
          halign: "left",
          fontSize: fontSizeText,
        },
        bodyStyles: {
          textColor: [0, 0, 0],
        },
        tableWidth: "wrap",
      });
      startY = doc.previousAutoTable.finalY + 10;
      if (necessaryCookies.length > 0){
        doc.autoTable({
          head: [["Duration", "Cookie", "Description"]],
          body: analyticsCookies, // Using dynamic body data
          startY: startY + 5, // Position below the Necessary text
          theme: "grid", // Add grid lines to the table
          styles: {
            fontSize: fontSizeText,
            halign: "left", // Align text to the left
            valign: "middle", // Vertical alignment for text
            lineWidth: 0.2, // Set line width for the table grid
          },
          columnStyles: {
            0: { cellWidth: 65 }, // Adjust the first column width
            1: { cellWidth: 50 }, // Adjust the second column width
            2: { cellWidth: 69 }, // Adjust the third column width
          },
          headStyles: {
            fillColor: [164, 194, 244], // Header background color
            textColor: [0, 0, 0], // Header text color
            halign: "left", // Align header text to the left
            fontSize: fontSizeText,
          },
          bodyStyles: {
            textColor: [0, 0, 0], // Body text color
          },
          tableWidth: "wrap", // Let the table wrap to fit the content
          margin: { left: 10, top: 0 }, // Align with the previous content
        });
        startY = doc.previousAutoTable.finalY + 10; // Update startY for further content
      }
      // Check and display status for Analytics
      if (preferences.analytics === "yes") {
        doc.setFont(undefined, "bold");
        doc.text("Analytics:", 15, startY);
        doc.setFont(undefined, "normal");
        doc.text("Active", 40, startY);
      } else {
        doc.setFont(undefined, "bold");
        doc.text("Analytics:", 15, startY);
        doc.setFont(undefined, "normal");
        doc.text("Not Active", 38, startY);
      }
      if (analyticsCookies.length > 0) {
        doc.autoTable({
          head: [["Duration", "Cookie", "Description"]],
          body: analyticsCookies, // Using dynamic body data
          startY: startY + 5, // Position below the Necessary text
          theme: "grid", // Add grid lines to the table
          styles: {
            fontSize: fontSizeText,
            halign: "left", // Align text to the left
            valign: "middle", // Vertical alignment for text
            lineWidth: 0.2, // Set line width for the table grid
          },
          columnStyles: {
            0: { cellWidth: 65 }, // Adjust the first column width
            1: { cellWidth: 50 }, // Adjust the second column width
            2: { cellWidth: 69 }, // Adjust the third column width
          },
          headStyles: {
            fillColor: [164, 194, 244], // Header background color
            textColor: [0, 0, 0], // Header text color
            halign: "left", // Align header text to the left
            fontSize: fontSizeText,
          },
          bodyStyles: {
            textColor: [0, 0, 0], // Body text color
          },
          tableWidth: "wrap", // Let the table wrap to fit the content
          margin: { left: 10, top: 0 }, // Align with the previous content
        });
        startY = doc.previousAutoTable.finalY + 10;
      } else {
        doc.autoTable({
          head: [["Duration", "Cookie", "Description"]],
          body: [
            ["-", "-", "-"],
          ],
          startY: startY + 5, // Position below the Necessary text
          theme: "grid",
          styles: {
            fontSize: fontSizeText,
            halign: "left",
            valign: "middle",
            lineWidth: 0.2,
          },
          columnStyles: {
            0: { cellWidth: 65 },
            1: { cellWidth: 50 },
            2: { cellWidth: 69 },
          },
          headStyles: {
            fillColor: [164, 194, 244],
            textColor: [0, 0, 0],
            halign: "left",
            fontSize: fontSizeText,
          },
          bodyStyles: {
            textColor: [0, 0, 0],
          },
          tableWidth: "wrap",
        });
        startY = doc.previousAutoTable.finalY + 10;
      }

      // Check and display status for Marketing
      if (preferences.marketing === "yes") {
        doc.setFont(undefined, "bold");
        doc.text("Marketing:", 15, startY);
        doc.setFont(undefined, "normal");
        doc.text(" Active", 40, startY);
      } else {
        doc.setFont(undefined, "bold");
        doc.text("Marketing:", 15, startY);
        doc.setFont(undefined, "normal");
        doc.text("Not Active", 38, startY);
      }
      if (marketingCookies.length > 0) {
        doc.autoTable({
          head: [["Duration", "Cookie", "Description"]],
          body: marketingCookies, // Using dynamic body data
          startY: startY + 5, // Position below the Necessary text
          theme: "grid", // Add grid lines to the table
          styles: {
            fontSize: fontSizeText,
            halign: "left", // Align text to the left
            valign: "middle", // Vertical alignment for text
            lineWidth: 0.2, // Set line width for the table grid
          },
          columnStyles: {
            0: { cellWidth: 65 }, // Adjust the first column width
            1: { cellWidth: 50 }, // Adjust the second column width
            2: { cellWidth: 69 }, // Adjust the third column width
          },
          headStyles: {
            fillColor: [164, 194, 244], // Header background color
            textColor: [0, 0, 0], // Header text color
            halign: "left", // Align header text to the left
            fontSize: fontSizeText,
          },
          bodyStyles: {
            textColor: [0, 0, 0], // Body text color
          },
          tableWidth: "wrap", // Let the table wrap to fit the content
          margin: { left: 10, top: 0 }, // Align with the previous content
        });
        startY = doc.previousAutoTable.finalY + 10;
      } else {
        doc.autoTable({
          head: [["Duration", "Cookie", "Description"]],
          body: [
            ["-", "-", "-"],
          ],
          startY: startY + 5, // Position below the Necessary text
          theme: "grid",
          styles: {
            fontSize: fontSizeText,
            halign: "left",
            valign: "middle",
            lineWidth: 0.2,
          },
          columnStyles: {
            0: { cellWidth: 65 },
            1: { cellWidth: 50 },
            2: { cellWidth: 69 },
          },
          headStyles: {
            fillColor: [164, 194, 244],
            textColor: [0, 0, 0],
            halign: "left",
            fontSize: fontSizeText,
          },
          bodyStyles: {
            textColor: [0, 0, 0],
          },
          tableWidth: "wrap",
        });
        startY = doc.previousAutoTable.finalY + 10;
      }

      // Check and display status for Preferences
      if (preferences.preferences === "yes") {
        doc.setFont(undefined, "bold");
        doc.text("Preferences:", 15, startY);
        doc.setFont(undefined, "normal");
        doc.text("Active", 40, startY);
      } else {
        doc.setFont(undefined, "bold");
        doc.text("Preferences:", 15, startY);
        doc.setFont(undefined, "normal");
        doc.text("Not Active", 40, startY);
      }
      if (preferencesCookies.length > 0) {
        doc.autoTable({
          head: [["Duration", "Cookie", "Description"]],
          body: preferencesCookies, // Using dynamic body data
          startY: startY + 5, // Position below the Necessary text
          theme: "grid", // Add grid lines to the table
          styles: {
            fontSize: fontSizeText,
            halign: "left", // Align text to the left
            valign: "middle", // Vertical alignment for text
            lineWidth: 0.2, // Set line width for the table grid
          },
          columnStyles: {
            0: { cellWidth: 65 }, // Adjust the first column width
            1: { cellWidth: 50 }, // Adjust the second column width
            2: { cellWidth: 69 }, // Adjust the third column width
          },
          headStyles: {
            fillColor: [164, 194, 244], // Header background color
            textColor: [0, 0, 0], // Header text color
            halign: "left", // Align header text to the left
            fontSize: fontSizeText,
          },
          bodyStyles: {
            textColor: [0, 0, 0], // Body text color
          },
          tableWidth: "wrap", // Let the table wrap to fit the content
          margin: { left: 10, top: 0 }, // Align with the previous content
        });
        startY = doc.previousAutoTable.finalY + 10;
      } else {
        doc.autoTable({
          head: [["Duration", "Cookie", "Description"]],
          body: [
            ["-", "-", "-"],
          ],
          startY: startY + 5, // Position below the Necessary text
          theme: "grid",
          styles: {
            fontSize: fontSizeText,
            halign: "left",
            valign: "middle",
            lineWidth: 0.2,
          },
          columnStyles: {
            0: { cellWidth: 65 },
            1: { cellWidth: 50 },
            2: { cellWidth: 69 },
          },
          headStyles: {
            fillColor: [164, 194, 244],
            textColor: [0, 0, 0],
            halign: "left",
            fontSize: fontSizeText,
          },
          bodyStyles: {
            textColor: [0, 0, 0],
          },
          tableWidth: "wrap",
        });
        startY = doc.previousAutoTable.finalY + 10;
      }

      // Check and display status for Unclassified
     if (preferences.unclassified === "yes") {
        doc.setFont(undefined, "bold");
        doc.text("Unclassified:", 15, startY);
        doc.setFont(undefined, "normal");
        doc.text("Active", 40, startY);
      } else {
        doc.setFont(undefined, "bold");
        doc.text("Unclassified:", 15, startY);
        doc.setFont(undefined, "normal");
        doc.text("Not Active", 40, startY);
      }

      if (unclassifiedCookies.length > 0) {
        doc.autoTable({
          head: [["Duration", "Cookie", "Description"]],
          body: unclassifiedCookies, // Using dynamic body data
          startY: startY + 5, // Position below the Necessary text
          theme: "grid", // Add grid lines to the table
          styles: {
            fontSize: fontSizeText,
            halign: "left", // Align text to the left
            valign: "middle", // Vertical alignment for text
            lineWidth: 0.2, // Set line width for the table grid
          },
          columnStyles: {
            0: { cellWidth: 65 }, // Adjust the first column width
            1: { cellWidth: 50 }, // Adjust the second column width
            2: { cellWidth: 69 }, // Adjust the third column width
          },
          headStyles: {
            fillColor: [164, 194, 244], // Header background color
            textColor: [0, 0, 0], // Header text color
            halign: "left", // Align header text to the left
            fontSize: fontSizeText,
          },
          bodyStyles: {
            textColor: [0, 0, 0], // Body text color
          },
          tableWidth: "wrap", // Let the table wrap to fit the content
          margin: { left: 10, top: 0 }, // Align with the previous content
        });
        startY = doc.previousAutoTable.finalY + 10;
      } else {
        doc.autoTable({
          head: [["Duration", "Cookie", "Description"]],
          body: [
            ["-", "-", "-"],
          ],
          startY: startY + 5, // Position below the Necessary text
          theme: "grid",
          styles: {
            fontSize: fontSizeText,
            halign: "left",
            valign: "middle",
            lineWidth: 0.2,
          },
          columnStyles: {
            0: { cellWidth: 65 },
            1: { cellWidth: 50 },
            2: { cellWidth: 69 },
          },
          headStyles: {
            fillColor: [164, 194, 244],
            textColor: [0, 0, 0],
            halign: "left",
            fontSize: fontSizeText,
          },
          bodyStyles: {
            textColor: [0, 0, 0],
          },
          tableWidth: "wrap",
        });
        startY = doc.previousAutoTable.finalY + 10;
      }
    }
    doc.setFont(undefined, "bold"); // Set font type to bold
    doc.text("TC String:", 15, 260);
    doc.setFont(undefined, "normal"); // Reset font type to normal
    
    // Define the box position and size
    const boxX = 15;
    const boxY = 265; // Adjust the Y position as needed
    const boxWidth = 185; // Width of the box
    const boxHeight = 15; // Height of the box
    
    // Draw the rectangle
    doc.rect(boxX, boxY, boxWidth, boxHeight);
    
    // Add the wrapped text inside the box
    doc.text(tcString, boxX + 5, boxY + 5); // Adjust position to fit text inside box
  }
  doc.save("generated-pdf.pdf");
}
