/**
 * buysell.js
 *
 * JavaScript functions provide interactive responses and validation on buy/sell page.
 *
 * wikicoins.com
 *
 * @author     Thomas Knierim
 * @version    CVS: $Id: buysell.js,v 1.0 2006/11/15 12:00:00 tk Exp $
 *
 */

// add a "trim" function to the string object
String.prototype.trim = function() {
  // skip leading and trailing whitespace
  // and return everything in between
   var s = this;
   s = s.replace(/^\s*(.*)/, "$1");
   s = s.replace(/(.*?)\s*$/, "$1");
   return s;
}

// array of auto-formatted, validated text fields
var fields = {
  'buyeradtext':  ['Type Ad Copy Here', '', 't'],
  'buyeramount':  ['$0.00', '$', 'n'],
  'selleradtext': ['Type Ad Copy Here', '', 'n'],
  'selleramount': ['$0.00', '$', 'n'],
  'weightlbs':    ['0 lbs', 'lbs', '0'],
  'weightozs':    ['0 ozs', 'ozs', '0']
};

/**
 * presetFields(): populate fields with preset values from field array
 * @return void
 *
 */
function presetFields() {
  var id = 0;
  for (id in fields) {
    el = document.getElementById(id);
    el.value = fields[id][0];
    el.style.color = "#999999";
  }
}

var popupWindow = null;
var imageID = null;
var imageSrc = 'view/UploadedBut.gif';
var imageName = null;
var imgId = null;

/**
 * popup(): (re-)open a fixed size, scrollable, sizeable popup window
 * @param url string contains the URL to be opened in the popup window
 * @param windowName string DOM identifier of the window
 * return void
 *
 */
function popup(url, windowName,idName) {
	closePopup();
	var options1 =  'width=480,height=300,left=100,top=100,status=no,resizable=yes,scrollbars=yes';
	popupWindow = window.open(url,windowName,options1);
        window.document.buyerAdForm.img_clicked.value = idName;
	return false;
}

function popup1(url, windowName,idName) {
	closePopup();
	var options1 =  'width=480,height=300,left=100,top=100,status=no,resizable=yes,scrollbars=yes';
	popupWindow = window.open(url,windowName,options1);
        window.document.sellerAdForm.img_clicked.value = idName;
	return false;
}

/**
 * focusField(): unset default text when a text field receives focus
 * @param id (string): DOM id of the HTML text(area) element
 * @param defaultText (string): default text to appear if field is empty
 * @return void
 *
 */
function focusField(id) {
  var el = document.getElementById(id);
  el.style.color = "#000000";
  var text = el.value.trim();
  if (text == fields[id][0])
     el.value = "";
}

/**
 * blurField(): reset default text when a text field looses focus
 * @param id (string): DOM id of the HTML text/textarea input element
 * @param defaultText (string): default text to appear if field is empty
 * @return void
 *
 */
function blurField(id) {
  var el = document.getElementById(id);
  var text = el.value.trim();
  if (text.length == 0) {
    el.value = fields[id][0];
    el.style.color = "#999999";
  }
}

/**
 * watchTextLength(): displays an error message if entered text is longer than
 * maxLength and shortens the text accordingly
 * @param id (string): DOM id of the HTML text/textarea input element
 * @param maxLength: maximum allowed length
 * @return void
 *
 */
 function watchTextLength(id, maxLength) {
  var el = document.getElementById(id, maxLength);
  if(el.value.length > maxLength) {
    alert("Please limit the text length to " + maxLength + " characters.");
    el.value = el.value.substr(0, maxLength);
  }
 }
 
 /**
  * formatNumber(): formats a number in a text input element
  * formats the number with two fixed digits and an optional string symbol
  * @param id (string): DOM id of the HTML text/textarea input element
  * @param symbol (string): ($, lbs, ozs, ...)
  * @return (boolean) true if number is valid
  */
  
function formatNumber(id) {
  var result = true;
  var el = document.getElementById(id);
  symbol = fields[id][1];
  var n = 0;
  text = el.value.trim();
  if(text.length > 0) {
    if (text.substring(0,1) == "$")
      text = text.substring(1);
    n = parseFloat(text);
    if (isNaN(n)) {
      n = 0;
      result = false;
    }
    text = n.toFixed(2);
    if (symbol == "$")
      
      text = "$" + text;
    else 
       text = text + " " + symbol;
    el.value = text;
  }
}
function formatNumber1(id) {
  var result = true;
  var el = document.getElementById(id);
  symbol = fields[id][1];
  var n = 0;
  text = el.value.trim();
  if(text.length > 0) {
    if (text.substring(0,1) == "$")
      text = text.substring(1);
    n = parseFloat(text);
    if (isNaN(n)) {
      n = 0;
      result = false;
    }
    text = n;
    if (symbol == "$")
      
      text = "$" + text;
    else 
       text = text + " " + symbol;
    el.value = text;
  }
}
/**
 * validateBuyerAd(): validates and submits buyer ad
 * return (boolean) true if input data is valid
 *
 */
 function validateBuyerAd(buyer_num) {
  var errorMsg = "";
  var retValue = false;
  var flag = 0;
  text = document.getElementById("buyeramount").value.trim();
  if (text.length == 0 || text == fields["buyeramount"][0]) {
    errorMsg = "USD amount must be greater than $0.00.";
    flag = 1;
  }
  text = document.getElementById("buyeradtext").value.trim();
  if (text.length == 0 || text == fields["buyeradtext"][0]) {
    errorMsg = "Buyer ad text may not be left empty.";
	 flag = 1;
  }
  if (errorMsg.length > 0) {
    alert(errorMsg);
    return false;
  }
  
	if ( flag == 0 ){
		if ( buyer_num >= 5 ){
			 alert( "Each user is allowed 5 ads per article.\nYou have exceeded the 5-ad limit." );
			 return false;

		}
		else 
			return true;	
		
	}
  
	
  
  	return false;

}

/**
 * validateSellerAd(): validates and submits seller ad
 * return (boolean) true if input data is valid
 *
 */
 function validateSellerAd(seller_num) {
  var errorMsg = "";
  var lbsSet = false;
  var ozsSet = false;
  var retValue = false;
  var flag = 0;	
  text = document.getElementById("weightlbs").value.trim();
  if (text.length > 0 && text != fields["weightlbs"][0])
    lbsSet = true;
  text = document.getElementById("weightozs").value.trim();
  if (text.length > 0 && text != fields["weightozs"][0])
    ozsSet = true;
  if (!lbsSet && !ozsSet)
    errorMsg = "Shipping weight is not entered yet.";
  text = document.getElementById("selleramount").value.trim();
  if (text.length == 0 || text == fields["selleramount"][0])
    errorMsg = "USD amount must be greater than $0.00.";
  text = document.getElementById("selleradtext").value.trim();
  if (text.length == 0 || text == fields["selleradtext"][0])
    errorMsg = "Seller ad text may not be left empty.";
  if (errorMsg.length > 0) {
	 flag = 1;
    alert(errorMsg);
    return false;
  }

	if ( flag == 0 ){
		if ( seller_num >= 5 ){
			 alert( "Each user is allowed 5 ads per article.\nYou have exceeded the 5-ad limit." );
			 return false;

		} else
			return true;
	
   } 

}

/**
 * closePopup(): close open popup Window
 * @return void
 *
 */
function closePopup(){
        if (popupWindow != null){
		if(!popupWindow.closed)
			popupWindow.close();
	}
}

