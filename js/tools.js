/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function formatedNumberToFloat(price, currencyFormat, currencySign)
{
	price = price.replace(currencySign, '');
	if (currencyFormat === 1)
		return parseFloat(price.replace(',', '').replace(' ', ''));
	else if (currencyFormat === 2)
		return parseFloat(price.replace(' ', '').replace(',', '.'));
	else if (currencyFormat === 3)
		return parseFloat(price.replace('.', '').replace(' ', '').replace(',', '.'));
	else if (currencyFormat === 4)
		return parseFloat(price.replace(',', '').replace(' ', ''));
	return price;
}

//return a formatted number
function formatNumber(value, numberOfDecimal, thousenSeparator, virgule)
{
	value = value.toFixed(numberOfDecimal);
	var val_string = value+'';
	var tmp = val_string.split('.');
	var abs_val_string = (tmp.length === 2) ? tmp[0] : val_string;
	var deci_string = ('0.' + (tmp.length === 2 ? tmp[1] : 0)).substr(2);
	var nb = abs_val_string.length;

	for (var i = 1 ; i < 4; i++)
		if (value >= Math.pow(10, (3 * i)))
			abs_val_string = abs_val_string.substring(0, nb - (3 * i)) + thousenSeparator + abs_val_string.substring(nb - (3 * i));

	if (parseInt(numberOfDecimal) === 0)
		return abs_val_string;
	return abs_val_string + virgule + deci_string;
}

function formatCurrency(price, currencyFormat, currencySign, currencyBlank)
{
	// if you modified this function, don't forget to modify the PHP function displayPrice (in the Tools.php class)
	var blank = '';
	price = parseFloat(price).toFixed(10);
	price = ps_round(price, priceDisplayPrecision);
	if (currencyBlank > 0)
		blank = ' ';
	if (currencyFormat == 1)
		return currencySign + blank + formatNumber(price, priceDisplayPrecision, ',', '.');
	if (currencyFormat == 2)
		return (formatNumber(price, priceDisplayPrecision, ' ', ',') + blank + currencySign);
	if (currencyFormat == 3)
		return (currencySign + blank + formatNumber(price, priceDisplayPrecision, '.', ','));
	if (currencyFormat == 4)
		return (formatNumber(price, priceDisplayPrecision, ',', '.') + blank + currencySign);
	if (currencyFormat == 5)
		return (currencySign + blank + formatNumber(price, priceDisplayPrecision, '\'', '.'));
	return price;
}

function ps_round_helper(value, mode)
{
	// From PHP Math.c
	if (value >= 0.0)
	{
		tmp_value = Math.floor(value + 0.5);
		if ((mode == 3 && value == (-0.5 + tmp_value)) ||
			(mode == 4 && value == (0.5 + 2 * Math.floor(tmp_value / 2.0))) ||
			(mode == 5 && value == (0.5 + 2 * Math.floor(tmp_value / 2.0) - 1.0)))
			tmp_value -= 1.0;
	}
	else
	{
		tmp_value = Math.ceil(value - 0.5);
		if ((mode == 3 && value == (0.5 + tmp_value)) ||
			(mode == 4 && value == (-0.5 + 2 * Math.ceil(tmp_value / 2.0))) ||
			(mode == 5 && value == (-0.5 + 2 * Math.ceil(tmp_value / 2.0) + 1.0)))
			tmp_value += 1.0;
	}

	return tmp_value;
}

function ps_log10(value)
{
	return Math.log(value) / Math.LN10;
}

function ps_round_half_up(value, precision)
{
	var mul = Math.pow(10, precision);
	var val = value * mul;

	var next_digit = Math.floor(val * 10) - 10 * Math.floor(val);
	if (next_digit >= 5)
		val = Math.ceil(val);
	else
		val = Math.floor(val);

	return val / mul;
}

function ps_round(value, places)
{
	if (typeof(roundMode) === 'undefined')
		roundMode = 2;
	if (typeof(places) === 'undefined')
		places = 2;

	var method = roundMode;

	if (method === 0)
		return ceilf(value, places);
	else if (method === 1)
		return floorf(value, places);
	else if (method === 2)
		return ps_round_half_up(value, places);
	else if (method == 3 || method == 4 || method == 5)
	{
		// From PHP Math.c
		var precision_places = 14 - Math.floor(ps_log10(Math.abs(value)));
		var f1 = Math.pow(10, Math.abs(places));

		if (precision_places > places && precision_places - places < 15)
		{
			var f2 = Math.pow(10, Math.abs(precision_places));
			if (precision_places >= 0)
				tmp_value = value * f2;
			else
				tmp_value = value / f2;

			tmp_value = ps_round_helper(tmp_value, roundMode);

			/* now correctly move the decimal point */
			f2 = Math.pow(10, Math.abs(places - precision_places));
			/* because places < precision_places */
			tmp_value /= f2;
		}
		else
		{
			/* adjust the value */
			if (places >= 0)
				tmp_value = value * f1;
			else
				tmp_value = value / f1;

			if (Math.abs(tmp_value) >= 1e15)
				return value;
		}

		tmp_value = ps_round_helper(tmp_value, roundMode);
		if (places > 0)
			tmp_value = tmp_value / f1;
		else
			tmp_value = tmp_value * f1;

		return tmp_value;
	}
}

function autoUrl(name, dest)
{
	var loc;
	var id_list;

	id_list = document.getElementById(name);
	loc = id_list.options[id_list.selectedIndex].value;
	if (loc != 0)
		location.href = dest+loc;
	return ;
}

function autoUrlNoList(name, dest)
{
	var loc;

	loc = document.getElementById(name).checked;
	location.href = dest + (loc == true ? 1 : 0);
	return ;
}

/*
** show or hide element e depending on condition show
*/
function toggle(e, show)
{
	e.style.display = show ? '' : 'none';
}

function toggleMultiple(tab)
{
    var len = tab.length;

    for (var i = 0; i < len; i++)
        if (tab[i].style)
            toggle(tab[i], tab[i].style.display == 'none');
}

/**
* Show dynamicaly an element by changing the sytle "display" property
* depending on the option selected in a select.
*
* @param string $select_id id of the select who controls the display
* @param string $elem_id prefix id of the elements controlled by the select
*   the real id must be : 'elem_id'+nb with nb the corresponding number in the
*   select (starting with 0).
*/
function showElemFromSelect(select_id, elem_id)
{
	var select = document.getElementById(select_id);
	for (var i = 0; i < select.length; ++i)
	{
	    var elem = document.getElementById(elem_id + select.options[i].value);
		if (elem != null)
			toggle(elem, i == select.selectedIndex);
	}
}

/**
* Get all div with specified name and for each one (by id), toggle their visibility
*/
function openCloseAllDiv(name, option)
{
	var tab = $('*[name='+name+']');
	for (var i = 0; i < tab.length; ++i)
		toggle(tab[i], option);
}

function toggleDiv(name, option)
{
	$('*[name='+name+']').each(function(){
		if (option == 'open')
		{
			$('#buttonall').data('status', 'close');
			$(this).hide();
		}
		else
		{
			$('#buttonall').data('status', 'open');
			$(this).show();
		}
	})
}

function toggleButtonValue(id_button, text1, text2)
{
	if ($('#'+id_button).find('i').first().hasClass('process-icon-compress'))
	{
		$('#'+id_button).find('i').first().removeClass('process-icon-compress').addClass('process-icon-expand');
		$('#'+id_button).find('span').first().html(text1);
	}
	else
	{
		$('#'+id_button).find('i').first().removeClass('process-icon-expand').addClass('process-icon-compress');
		$('#'+id_button).find('span').first().html(text2);
	}
}


/**
* Toggle the value of the element id_button between text1 and text2
*/
function toggleElemValue(id_button, text1, text2)
{
	var obj = document.getElementById(id_button);
	if (obj)
		obj.value = ((!obj.value || obj.value == text2) ? text1 : text2);
}

function addBookmark(url, title)
{
	if (window.sidebar && window.sidebar.addPanel)
		return window.sidebar.addPanel(title, url, "");
	else if ( window.external && ('AddFavorite' in window.external))
		return window.external.AddFavorite( url, title);
}

function writeBookmarkLink(url, title, text, img)
{
	var insert = '';
	if (img)
		insert = writeBookmarkLinkObject(url, title, '<img src="' + img + '" alt="' + escape(text) + '" title="' + removeQuotes(text) + '" />') + '&nbsp';
	insert += writeBookmarkLinkObject(url, title, text);
	if (window.sidebar || window.opera && window.print || (window.external && ('AddFavorite' in window.external)))
		$('.add_bookmark, #header_link_bookmark').append(insert);
}

function writeBookmarkLinkObject(url, title, insert)
{
	if (window.sidebar || window.external)
		return ('<a href="javascript:addBookmark(\'' + escape(url) + '\', \'' + removeQuotes(title) + '\')">' + insert + '</a>');
	else if (window.opera && window.print)
		return ('<a rel="sidebar" href="' + escape(url) + '" title="' + removeQuotes(title) + '">' + insert + '</a>');
	return ('');
}

function checkCustomizations()
{
	var pattern = new RegExp(' ?filled ?');

	if (typeof customizationFields != 'undefined')
		for (var i = 0; i < customizationFields.length; i++)
		{
			/* If the field is required and empty then we abort */
			if (parseInt(customizationFields[i][1]) == 1 && ($('#' + customizationFields[i][0]).html() == '' ||  $('#' + customizationFields[i][0]).text() != $('#' + customizationFields[i][0]).val()) && !pattern.test($('#' + customizationFields[i][0]).attr('class')))
				return false;
		}
	return true;
}

function emptyCustomizations()
{
	customizationId = null;
	if(typeof(customizationFields) == 'undefined') return;

	$('.customization_block .success').fadeOut(function(){
		$(this).remove();
	});
	$('.customization_block .error').fadeOut(function(){
		$(this).remove();
	});
	for (var i = 0; i < customizationFields.length; i++)
	{
		$('#' + customizationFields[i][0]).html('');
		$('#' + customizationFields[i][0]).val('');
	}
}

function ceilf(value, precision)
{
	if (typeof(precision) === 'undefined')
		precision = 0;
	var precisionFactor = precision === 0 ? 1 : Math.pow(10, precision);
	var tmp = value * precisionFactor;
	var tmp2 = tmp.toString();
	if (tmp2[tmp2.length - 1] === 0)
		return value;
	return Math.ceil(value * precisionFactor) / precisionFactor;
}

function floorf(value, precision)
{
	if (typeof(precision) === 'undefined')
		precision = 0;
	var precisionFactor = precision === 0 ? 1 : Math.pow(10, precision);
	var tmp = value * precisionFactor;
	var tmp2 = tmp.toString();
	if (tmp2[tmp2.length - 1] === 0)
		return value;
	return Math.floor(value * precisionFactor) / precisionFactor;
}

function setCurrency(id_currency)
{
	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: baseDir + 'index.php' + '?rand=' + new Date().getTime(),
		data: 'controller=change-currency&id_currency='+ parseInt(id_currency),
		success: function(msg)
		{
			location.reload(true);
		}
	});
}

function isArrowKey(k_ev)
{
	var unicode=k_ev.keyCode? k_ev.keyCode : k_ev.charCode;
	if (unicode >= 37 && unicode <= 40)
		return true;
	return false;
}

function removeQuotes(value)
{
	value = value.replace(/\\"/g, '');
	value = value.replace(/"/g, '');
	value = value.replace(/\\'/g, '');
	value = value.replace(/'/g, '');

	return value;
}

function sprintf(format)
{
	for(var i=1; i < arguments.length; i++)
		format = format.replace(/%s/, arguments[i]);

	return format;
}

/**
 * Display a MessageBox
 * @param {string} msg
 * @param {string} title (optional)
 */
function fancyMsgBox(msg, title)
{
    if (title) msg = "<h2>" + title + "</h2><p>" + msg + "</p>";
    msg += "<br/><p class=\"submit\" style=\"text-align:right; padding-bottom: 0\"><input class=\"button\" type=\"button\" value=\"OK\" onclick=\"$.fancybox.close();\" /></p>";
	if(!!$.prototype.fancybox)
    	$.fancybox( msg, {'autoDimensions': false, 'autoSize': false, 'width': 500, 'height': 'auto', 'openEffect': 'none', 'closeEffect': 'none'} );
}

/**
 * Display a messageDialog with different buttons including a callback for each one
 * @param {string} question
 * @param {mixed} title Optional title for the dialog box. Send false if you don't want any title
 * @param {object} buttons Associative array containg a list of {buttonCaption: callbackFunctionName, ...}. Use an empty space instead of function name for no callback
 * @param {mixed} otherParams Optional data sent to the callback function
 */
function fancyChooseBox(question, title, buttons, otherParams)
{
    var msg, funcName, action;
	msg = '';
    if (title)
		msg = "<h2>" + title + "</h2><p>" + question + "</p>";
    msg += "<br/><p class=\"submit\" style=\"text-align:right; padding-bottom: 0\">";
    var i = 0;
    for (var caption in buttons) {
        if (!buttons.hasOwnProperty(caption)) continue;
        funcName = buttons[caption];
        if (typeof otherParams == 'undefined') otherParams = 0;
        otherParams = escape(JSON.stringify(otherParams));
        action = funcName ? "$.fancybox.close();window['" + funcName + "'](JSON.parse(unescape('" + otherParams + "')), " + i + ")" : "$.fancybox.close()";
	  msg += '<button type="submit" class="button btn-default button-medium" style="margin-right: 5px;" value="true" onclick="' + action + '" >';
	  msg += '<span>' + caption + '</span></button>'
        i++;
    }
    msg += "</p>";
	if(!!$.prototype.fancybox)
    	$.fancybox(msg, {'autoDimensions': false, 'width': 500, 'height': 'auto', 'openEffect': 'none', 'closeEffect': 'none'});
}

function toggleLayer(whichLayer, flag)
{
	if (!flag)
		$(whichLayer).hide();
	else
		$(whichLayer).show();
}

function openCloseLayer(whichLayer, action)
{
	if (!action)
	{
		if ($(whichLayer).css('display') == 'none')
			$(whichLayer).show();
		else
			$(whichLayer).hide();
	}
	else if (action == 'open')
		$(whichLayer).show();
	else if (action == 'close')
		$(whichLayer).hide();
}

function updateTextWithEffect(jQueryElement, text, velocity, effect1, effect2, newClass)
{
	if(jQueryElement.text() !== text)
	{
		if(effect1 === 'fade')
			jQueryElement.fadeOut(velocity, function(){
				$(this).addClass(newClass);
				if(effect2 === 'fade') $(this).text(text).fadeIn(velocity);
				else if(effect2 === 'slide') $(this).text(text).slideDown(velocity);
					else if(effect2 === 'show')	$(this).text(text).show(velocity, function(){});
			});
		else if(effect1 === 'slide')
			jQueryElement.slideUp(velocity, function(){
				$(this).addClass(newClass);
				if(effect2 === 'fade') $(this).text(text).fadeIn(velocity);
				else if(effect2 === 'slide') $(this).text(text).slideDown(velocity);
					else if(effect2 === 'show')	$(this).text(text).show(velocity);
			});
		else if(effect1 === 'hide')
			jQueryElement.hide(velocity, function(){
				$(this).addClass(newClass);
				if(effect2 === 'fade') $(this).text(text).fadeIn(velocity);
				else if(effect2 === 'slide') $(this).text(text).slideDown(velocity);
					else if(effect2 === 'show')	$(this).text(text).show(velocity);
			});
	}
}
//show a JS debug
function dbg(value)
{
	var active = false;//true for active
	var firefox = true;//true if debug under firefox

	if (active)
		if (firefox)
			console.log(value);
		else
			alert(value);
}

/**
* Function : print_r()
* Arguments: The element  - array,hash(associative array),object
*            The limit - OPTIONAL LIMIT
*            The depth - OPTIONAL
* Returns  : The textual representation of the array.
* This function was inspired by the print_r function of PHP.
* This will accept some data as the argument and return a
* text that will be a more readable version of the
* array/hash/object that is given.
*/
function print_r(element, limit, depth)
{
	depth =	depth?depth:0;
	limit = limit?limit:1;

	returnString = '<ol>';

	for(property in element)
	{
		//Property domConfig isn't accessable
		if (property != 'domConfig')
		{
			returnString += '<li><strong>'+ property + '</strong> <small>(' + (typeof element[property]) +')</small>';

			if (typeof element[property] == 'number' || typeof element[property] == 'boolean')
				returnString += ' : <em>' + element[property] + '</em>';
			if (typeof element[property] == 'string' && element[property])
				returnString += ': <div style="background:#C9C9C9;border:1px solid black; overflow:auto;"><code>' +
									element[property].replace(/</g, '&amp;lt;').replace(/>/g, '&amp;gt;') + '</code></div>';

			if ((typeof element[property] == 'object') && (depth < limit))
				returnString += print_r(element[property], limit, (depth + 1));

			returnString += '</li>';
		}
	}
	returnString += '</ol>';

	if(depth == 0)
	{
		winpop = window.open("", "","width=800,height=600,scrollbars,resizable");
		winpop.document.write('<pre>'+returnString+ '</pre>');
		winpop.document.close();
	}
	return returnString;
}

//verify if value is in the array
function in_array(value, array)
{
	for (var i in array)
		if ((array[i] + '') === (value + ''))
			return true;
	return false;
}

function isCleanHtml(content)
{
	var events = 'onmousedown|onmousemove|onmmouseup|onmouseover|onmouseout|onload|onunload|onfocus|onblur|onchange';
	events += '|onsubmit|ondblclick|onclick|onkeydown|onkeyup|onkeypress|onmouseenter|onmouseleave|onerror|onselect|onreset|onabort|ondragdrop|onresize|onactivate|onafterprint|onmoveend';
	events += '|onafterupdate|onbeforeactivate|onbeforecopy|onbeforecut|onbeforedeactivate|onbeforeeditfocus|onbeforepaste|onbeforeprint|onbeforeunload|onbeforeupdate|onmove';
	events += '|onbounce|oncellchange|oncontextmenu|oncontrolselect|oncopy|oncut|ondataavailable|ondatasetchanged|ondatasetcomplete|ondeactivate|ondrag|ondragend|ondragenter|onmousewheel';
	events += '|ondragleave|ondragover|ondragstart|ondrop|onerrorupdate|onfilterchange|onfinish|onfocusin|onfocusout|onhashchange|onhelp|oninput|onlosecapture|onmessage|onmouseup|onmovestart';
	events += '|onoffline|ononline|onpaste|onpropertychange|onreadystatechange|onresizeend|onresizestart|onrowenter|onrowexit|onrowsdelete|onrowsinserted|onscroll|onsearch|onselectionchange';
	events += '|onselectstart|onstart|onstop';

	var script1 = /<[\s]*script/im;
	var script2 = new RegExp('('+events+')[\s]*=', 'im');
	var script3 = /.*script\:/im;
	var script4 = /<[\s]*(i?frame|embed|object)/im;

	if (script1.test(content) || script2.test(content) || script3.test(content) || script4.test(content))
		return false;

	return true;
}

function getStorageAvailable() {
	test = 'foo';
	storage =  window.localStorage || window.sessionStorage;
	try {
		storage.setItem(test, test);
		storage.removeItem(test);
		return storage;
	}
	catch (error) {
		return null;
	}
}

$(document).ready(function()
{
	// Hide all elements with .hideOnSubmit class when parent form is submit
	$('form').submit(function() {
		$(this).find('.hideOnSubmit').hide();
	});

	$.fn.checkboxChange = function(fnChecked, fnUnchecked) {
		if ($(this).prop('checked') && fnChecked)
			fnChecked.call(this);
		else if(fnUnchecked)
			fnUnchecked.call(this);

		if (!$(this).attr('eventCheckboxChange'))
		{
			$(this).on('change', function() { $(this).checkboxChange(fnChecked, fnUnchecked); });
			$(this).attr('eventCheckboxChange', true);
		}
	};

	// attribute target="_blank" is not W3C compliant
	$('a._blank, a.js-new-window').attr('target', '_blank');
});
