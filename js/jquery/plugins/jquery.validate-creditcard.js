/*
* jQuery creditcard2 extension for the jQuery Validation plugin (http://plugins.jquery.com/project/validate).
* Ported from http://www.braemoor.co.uk/software/creditcard.shtml by John Gardner, with some enhancements.
*
* Author: Jack Killpatrick
* Copyright (c) 2010 iHwy, Inc.
*
* Version 1.0.1 (1/12/2010)
* Tested with jquery 1.2.6, but will probably work with earlier versions.
*
* History:
* 1.0.0 - released 2008-11-17
* 1.0.1 - released 2010-01-12 -> updated card prefixes based on data at: http://en.wikipedia.org/wiki/Credit_card_number and added support for LaserCard
*
* Visit http://www.ihwy.com/labs/jquery-validate-credit-card-extension.aspx for usage information
*
* Dual licensed under the MIT and GPL licenses:
*   http://www.opensource.org/licenses/mit-license.php
*   http://www.gnu.org/licenses/gpl.html
*/

function validateCC(cardNo, cardName)
{
//jQuery.validator.addMethod("creditcard2", function(value, element, param) {
	var cards = new Array();
	cards[0] = { cardName: "Visa", lengths: "13,16", prefixes: "4", checkdigit: true };
	cards[1] = { cardName: "MasterCard", lengths: "16", prefixes: "51,52,53,54,55", checkdigit: true };
	cards[2] = { cardName: "DinersClub", lengths: "14,16", prefixes: "305,36,38,54,55", checkdigit: true };
	cards[3] = { cardName: "CarteBlanche", lengths: "14", prefixes: "300,301,302,303,304,305", checkdigit: true };
	cards[4] = { cardName: "AmEx", lengths: "15", prefixes: "34,37", checkdigit: true };
	cards[5] = { cardName: "Discover", lengths: "16", prefixes: "6011,622,64,65", checkdigit: true };
	cards[6] = { cardName: "JCB", lengths: "16", prefixes: "35", checkdigit: true };
	cards[7] = { cardName: "enRoute", lengths: "15", prefixes: "2014,2149", checkdigit: true };
	cards[8] = { cardName: "Solo", lengths: "16,18,19", prefixes: "6334, 6767", checkdigit: true };
	cards[9] = { cardName: "Switch", lengths: "16,18,19", prefixes: "4903,4905,4911,4936,564182,633110,6333,6759", checkdigit: true };
	cards[10] = { cardName: "Maestro", lengths: "12,13,14,15,16,18,19", prefixes: "5018,5020,5038,6304,6759,6761", checkdigit: true };
	cards[11] = { cardName: "VisaElectron", lengths: "16", prefixes: "417500,4917,4913,4508,4844", checkdigit: true };
	cards[12] = { cardName: "LaserCard", lengths: "16,17,18,19", prefixes: "6304,6706,6771,6709", checkdigit: true };

	var cardType = -1;
	for (var i = 0; i < cards.length; i++) {
		if (cardName.toLowerCase() == cards[i].cardName.toLowerCase()) {
			cardType = i;
			break;
		}
	}
	if (cardType == -1) { return false; } // card type not found

	cardNo = cardNo.replace(/[\s-]/g, ""); // remove spaces and dashes
	if (cardNo.length == 0) { return false; } // no length

	var cardexp = /^[0-9]{13,19}$/;
	if (!cardexp.exec(cardNo)) { return false; } // has chars or wrong length

	cardNo = cardNo.replace(/\D/g, ""); // strip down to digits

	if (cards[cardType].checkdigit) {
		var checksum = 0;
		var mychar = "";
		var j = 1;

		var calc;
		for (i = cardNo.length - 1; i >= 0; i--) {
			calc = Number(cardNo.charAt(i)) * j;
			if (calc > 9) {
				checksum = checksum + 1;
				calc = calc - 10;
			}
			checksum = checksum + calc;
			if (j == 1) { j = 2 } else { j = 1 };
		}

		if (checksum % 10 != 0) { return false; } // not mod10
	}

	var lengthValid = false;
	var prefixValid = false;
	var prefix = new Array();
	var lengths = new Array();

	prefix = cards[cardType].prefixes.split(",");
	for (i = 0; i < prefix.length; i++) {
		var exp = new RegExp("^" + prefix[i]);
		if (exp.test(cardNo)) prefixValid = true;
	}
	if (!prefixValid) { return false; } // invalid prefix

	lengths = cards[cardType].lengths.split(",");
	for (j = 0; j < lengths.length; j++) {
		if (cardNo.length == lengths[j]) lengthValid = true;
	}
	if (!lengthValid) { return false; } // wrong length

	return true;
}
