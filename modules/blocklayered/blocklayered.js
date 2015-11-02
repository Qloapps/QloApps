/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registred Trademark & Property of PrestaShop SA
*/

var ajaxQueries = new Array();
var ajaxLoaderOn = 0;
var sliderList = new Array();
var slidersInit = false;

$(document).ready(function()
{
	cancelFilter();
	openCloseFilter();
	
	// Click on color
	$('#layered_form input[type=button], #layered_form label.layered_color').live('click', function()
	{
		if (!$('input[name='+$(this).attr('name')+'][type=hidden]').length)
			$('<input />').attr('type', 'hidden').attr('name', $(this).attr('name')).val($(this).data('rel')).appendTo('#layered_form');
		else
			$('input[name='+$(this).attr('name')+'][type=hidden]').remove();
		reloadContent();
	});
	
	// Click on checkbox
	$('#layered_form input[type=checkbox], #layered_form input[type=radio]').live('click', function()
	{
		reloadContent();
	});

	$(document).on('change', '#layered_form .select', function(e)
	{
		reloadContent();
	});
	
	// Changing content of an input text
	$('#layered_form input.layered_input_range').live('keyup', function()
	{
		if ($(this).attr('timeout_id'))
			window.clearTimeout($(this).attr('timeout_id'));

		// IE Hack, setTimeout do not acept the third parameter
		var reference = this;
		
		$(this).attr('timeout_id', window.setTimeout(function(it) {
			if (!$(it).attr('id'))
				it = reference;
			
			var filter = $(it).attr('id').replace(/^layered_(.+)_range_.*$/, '$1');
			
			var value_min = parseInt($('#layered_'+filter+'_range_min').val()); 
			if (isNaN(value_min)) 
				value_min = 0;
			$('#layered_'+filter+'_range_min').val(value_min);

			var value_max = parseInt($('#layered_'+filter+'_range_max').val());
			if (isNaN(value_max)) 
				value_max = 0;
			$('#layered_'+filter+'_range_max').val(value_max);
			
			if (value_max < value_min) {
				$('#layered_'+filter+'_range_max').val($(it).val());
				$('#layered_'+filter+'_range_min').val($(it).val());
			}
			reloadContent();
		}, 500, this));
	});

	$('#layered_block_left .radio').live('click', function() {
		var name = $(this).attr('name');
		$.each($(this).parent().parent().find('input[type=button]'), function (it, item) {
			if ($(item).hasClass('on') && $(item).attr('name') != name) {
				$(item).click();
			}
		});
		return true;
	});
	
	// Click on label
	$('#layered_block_left label a').live({
		click: function() {
			var disable = $(this).parent().parent().find('input').attr('disabled');
			if (disable == ''
			|| typeof(disable) == 'undefined'
			|| disable == false)
			{
				$(this).parent().parent().find('input').click();
				reloadContent();
			}
			return false;
		}
	});
	
	layered_hidden_list = {};
	$('.hide-action').live('click', function() {
		if (typeof(layered_hidden_list[$(this).parent().find('ul').attr('id')]) == 'undefined' || layered_hidden_list[$(this).parent().find('ul').attr('id')] == false)
		{
			layered_hidden_list[$(this).parent().find('ul').attr('id')] = true;
		}
		else
		{
			layered_hidden_list[$(this).parent().find('ul').attr('id')] = false;
		}
		hideFilterValueAction(this);
	});
	$('.hide-action').each(function() {
		hideFilterValueAction(this);
	});

	// To be sure there is no other events attached to the selectProductSort, change the ID
	var id = 1;
	while ($('#selectPrductSort').length) { // Because ids are duplicated
		// Unbind event change on #selectPrductSort
		$('#selectPrductSort').unbind('change');
		$('#selectPrductSort').attr('onchange', '');
		$('#selectPrductSort').addClass('selectProductSort');
		$('#selectPrductSort').attr('id', 'selectPrductSort'+id);
		$('label[for=selectPrductSort]').attr('for', 'selectPrductSort'+id);
		id++;
	}

	while ($('#selectProductSort').length) { // Because ids are duplicated
		// Unbind event change on #selectProductSort
		$('#selectProductSort').unbind('change');
		$('#selectProductSort').attr('onchange', '');
		$('#selectProductSort').addClass('selectProductSort');
		$('#selectProductSort').attr('id', 'selectProductSort'+id);
		$('label[for=selectProductSort]').attr('for', 'selectProductSort'+id);
		id++;
	}
	
	// Since 1.5, event is add to .selectProductSort and not to #selectProductSort
	setTimeout(function() {
		$('.selectProductSort').unbind('change');
	}, 100);
	
	$('.selectProductSort').live('change', function(event) {
		$('.selectProductSort').val($(this).val());
		reloadContent();
	});
	
	// To be sure there is no other events attached to the nb_item, change the ID
	var id = 1;
	while ($('#nb_item').length) { // Because ids are duplicated
		// Unbind event change on #nb_item
		$('#nb_item').unbind('change');
		$('#nb_item').attr('onchange', '');
		$('#nb_item').addClass('nb_item');
		$('#nb_item').attr('id', 'nb_item'+id);
		$('label[for=nb_item]').attr('for', 'nb_item'+id);
		id++;
	}
	$('.nb_item').live('change', function(event) {
		$('.nb_item').val($(this).val());
		reloadContent();
	});
	
	paginationButton();
	initLayered();
});
	
function hideFilterValueAction(it)
{
	if (typeof(layered_hidden_list[$(it).parent().find('ul').attr('id')]) == 'undefined' || layered_hidden_list[$(it).parent().find('ul').attr('id')] == false)
	{
		$(it).parent().find('.hiddable').hide();
		$(it).parent().find('.hide-action.less').hide();
		$(it).parent().find('.hide-action.more').show();
	}
	else
	{
		$(it).parent().find('.hiddable').show();
		$(it).parent().find('.hide-action.less').show();
		$(it).parent().find('.hide-action.more').hide();
	}
}

function addSlider(type, data, unit, format)
{
	sliderList.push({
		type: type,
		data: data,
		unit: unit,
		format: format
	});
}

function initSliders()
{
	$(sliderList).each(function(i, slider){
		$('#layered_'+slider['type']+'_slider').slider(slider['data']);
		
		var from = '';
		var to = '';
		switch (slider['format'])
		{
			case 1:
			case 2:
			case 3:
			case 4:
				from = blocklayeredFormatCurrency($('#layered_'+slider['type']+'_slider').slider('values', 0), slider['format'], slider['unit']);
				to = blocklayeredFormatCurrency($('#layered_'+slider['type']+'_slider').slider('values', 1), slider['format'], slider['unit']);
				break;
			case 5:
				from =  $('#layered_'+slider['type']+'_slider').slider('values', 0)+slider['unit']
				to = $('#layered_'+slider['type']+'_slider').slider('values', 1)+slider['unit'];
				break;
		}
		$('#layered_'+slider['type']+'_range').html(from+' - '+to);
	});
}

function initLayered()
{
	initSliders();
	initLocationChange();
	updateProductUrl();
	if (window.location.href.split('#').length == 2 && window.location.href.split('#')[1] != '')
	{
		var params = window.location.href.split('#')[1];
		reloadContent('&selected_filters='+params);
	}
}

function paginationButton() {
	$('#pagination a').not(':hidden').each(function () {
		if ($(this).attr('href').search(/[&|\?]p=/) == -1)
		    var page = 1;
		else
		    var page = $(this).attr('href').replace(/^.*[&|\?]p=(\d+).*$/, '$1');

		var location = window.location.href.replace(/#.*$/, '');
		$(this).attr('href', location+current_friendly_url.replace(/\/page-(\d+)/, '')+'/page-'+page);
	});
	$('#pagination li').not('.current, .disabled').each(function () {
		var nbPage = 0;
		if ($(this).attr('id') == 'pagination_next')
			nbPage = parseInt($('#pagination li.current').children().html())+ 1;
		else if ($(this).attr('id') == 'pagination_previous')
			nbPage = parseInt($('#pagination li.current').children().html())- 1;
	
		$(this).children().click(function () {
			if (nbPage == 0)
				p = parseInt($(this).html()) + parseInt(nbPage);
			else
				p = nbPage;
			p = '&p='+ p;
			reloadContent(p);
			nbPage = 0;
			return false;
		});
	});
}

function cancelFilter()
{
	$('#enabled_filters a').live('click', function(e)
	{
		if ($(this).data('rel').search(/_slider$/) > 0)
		{
			if ($('#'+$(this).data('rel')).length)
			{
				$('#'+$(this).data('rel')).slider('values' , 0, $('#'+$(this).data('rel')).slider('option' , 'min' ));
				$('#'+$(this).data('rel')).slider('values' , 1, $('#'+$(this).data('rel')).slider('option' , 'max' ));
				$('#'+$(this).data('rel')).slider('option', 'slide')(0,{values:[$('#'+$(this).data('rel')).slider( 'option' , 'min' ), $('#'+$(this).data('rel')).slider( 'option' , 'max' )]});
			}
			else if($('#'+$(this).data('rel').replace(/_slider$/, '_range_min')).length)
			{
				$('#'+$(this).data('rel').replace(/_slider$/, '_range_min')).val($('#'+$(this).data('rel').replace(/_slider$/, '_range_min')).attr('limitValue'));
				$('#'+$(this).data('rel').replace(/_slider$/, '_range_max')).val($('#'+$(this).data('rel').replace(/_slider$/, '_range_max')).attr('limitValue'));
			}
		}
		else
		{
			if ($('option#'+$(this).data('rel')).length)
			{
				$('#'+$(this).data('rel')).parent().val('');
			}
			else
			{
				$('#'+$(this).data('rel')).attr('checked', false);
				$('.'+$(this).data('rel')).attr('checked', false);
				$('#layered_form input[type=hidden][name='+$(this).data('rel')+']').remove();
			}
		}
		reloadContent();
		e.preventDefault();
	});
}

function openCloseFilter()
{
	$('#layered_form span.layered_close a').live('click', function(e)
	{
		if ($(this).html() == '&lt;')
		{
			$('#'+$(this).data('rel')).show();
			$(this).html('v');
			$(this).parent().removeClass('closed');
		}
		else
		{
			$('#'+$(this).data('rel')).hide();
			$(this).html('&lt;');
			$(this).parent().addClass('closed');
		}
		
		e.preventDefault();
	});
}

function stopAjaxQuery() {
	if (typeof(ajaxQueries) == 'undefined')
		ajaxQueries = new Array();
	for(i = 0; i < ajaxQueries.length; i++) {
		if (typeof ajaxQueries[i] != 'undefined')
			ajaxQueries[i].abort();
	}		
	ajaxQueries = new Array();
}

function reloadContent(params_plus)
{
	stopAjaxQuery();
	
	if (!ajaxLoaderOn)
	{
		$('#product_list').prepend($('#layered_ajax_loader').html());
		$('#product_list').css('opacity', '0.7');
		ajaxLoaderOn = 1;
	}
	
	data = $('#layered_form').serialize();
	$('.layered_slider').each( function () {
		var sliderStart = $(this).slider('values', 0);
		var sliderStop = $(this).slider('values', 1);
		if (typeof(sliderStart) == 'number' && typeof(sliderStop) == 'number')
			data += '&'+$(this).attr('id')+'='+sliderStart+'_'+sliderStop;
	});

	$(['price', 'weight']).each(function(it, sliderType)
	{
		if ($('#layered_'+sliderType+'_range_min').length)
		{
			data += '&layered_'+sliderType+'_slider='+$('#layered_'+sliderType+'_range_min').val()+'_'+$('#layered_'+sliderType+'_range_max').val();
		}
	});
	
	$('#layered_form .select option').each( function () {
		if($(this).attr('id') && $(this).parent().val() == $(this).val())
		{
			data += '&'+$(this).attr('id') + '=' + $(this).val();
		}
	});
	
	if ($('.selectProductSort').length && $('.selectProductSort').val())
	{
		if ($('.selectProductSort').val().search(/orderby=/) > 0)
		{
			// Old ordering working
			var splitData = [
				$('.selectProductSort').val().match(/orderby=(\w*)/)[1],
				$('.selectProductSort').val().match(/orderway=(\w*)/)[1]
			];
		}
		else
		{
			// New working for default theme 1.4 and theme 1.5
			var splitData = $('.selectProductSort').val().split(':');
		}
		data += '&orderby='+splitData[0]+'&orderway='+splitData[1];
	}
	if ($('.nb_item').length)
	{
		data += '&n='+$('.nb_item').val();
	}
	
	var slideUp = true;
	if (params_plus == undefined || !(typeof params_plus == 'string'))
	{
		params_plus = '';
		slideUp = false;
	}
	
	// Get nb items per page
	var n = '';
	$('#pagination #nb_item').children().each(function(it, option) {
		if (option.selected)
			n = '&n='+option.value;
	});
	
	ajaxQuery = $.ajax(
	{
		type: 'GET',
		url: baseDir + 'modules/blocklayered/blocklayered-ajax.php',
		data: data+params_plus+n,
		dataType: 'json',
		cache: false, // @todo see a way to use cache and to add a timestamps parameter to refresh cache each 10 minutes for example
		success: function(result)
		{
			$('#layered_block_left').replaceWith(utf8_decode(result.filtersBlock));
			
			$('.category-product-count').html(result.categoryCount);
			
			if (result.productList)
				$('#product_list').replaceWith(utf8_decode(result.productList));
			else
				$('#product_list').html('');

			$('#product_list').css('opacity', '1');
			if ($.browser.msie) // Fix bug with IE8 and aliasing
				$('#product_list').css('filter', '');

			if (result.pagination.search(/[^\s]/) >= 0) {
				if ($(result.pagination).find('ul.pagination').length)
				{
					$('div#pagination').show();
					$('ul.pagination').each(function () {
						$(this).replaceWith($(result.pagination).find('ul.pagination'));
					});
				}
				else if (!$('ul.pagination').length)
				{
					$('div#pagination').show();
					$('div#pagination').each(function () {
						$(this).html($(result.pagination));
					});
				}
				else
				{
					$('ul.pagination').html('');
					$('div#pagination').hide();
				}
			}
			else
			{
				$('ul.pagination').html('');
				$('div#pagination').hide();
			}
			
			paginationButton();
			ajaxLoaderOn = 0;
			
			// On submiting nb items form, relaod with the good nb of items
			$('#pagination form').submit(function() {
				val = $('#pagination #nb_item').val();
				$('#pagination #nb_item').children().each(function(it, option) {
					if (option.value == val)
						$(option).attr('selected', true);
					else
						$(option).removeAttr('selected');
				});
				// Reload products and pagination
				reloadContent();
				return false;
			});
			if (typeof(ajaxCart) != "undefined")
				ajaxCart.overrideButtonsInThePage();
			
			if (typeof(reloadProductComparison) == 'function')
				reloadProductComparison();
			initSliders();
			
			// Currente page url
			if (typeof(current_friendly_url) == 'undefined')
				current_friendly_url = '#';
				
			// Get all sliders value
			$(['price', 'weight']).each(function(it, sliderType)
			{
				if ($('#layered_'+sliderType+'_slider').length)
				{
					// Check if slider is enable & if slider is used
					if(typeof($('#layered_'+sliderType+'_slider').slider('values', 0)) != 'object')
					{
						if ($('#layered_'+sliderType+'_slider').slider('values', 0) != $('#layered_'+sliderType+'_slider').slider('option' , 'min')
						|| $('#layered_'+sliderType+'_slider').slider('values', 1) != $('#layered_'+sliderType+'_slider').slider('option' , 'max'))
							current_friendly_url += '/'+sliderType+'-'+$('#layered_'+sliderType+'_slider').slider('values', 0)+'-'+$('#layered_'+sliderType+'_slider').slider('values', 1)
					}
				}
				else if ($('#layered_'+sliderType+'_range_min').length)
				{
					if ($('#layered_' + sliderType + '_range_min').attr('limitValue') != $('#layered_'+sliderType+'_range_min').val()
                                        || $('#layered_' + sliderType + '_range_max').attr('limitValue') != $('#layered_'+sliderType+'_range_max').val())
						current_friendly_url += '/'+sliderType+'-'+$('#layered_'+sliderType+'_range_min').val()+'-'+$('#layered_'+sliderType+'_range_max').val();
				}
			});
			window.location = current_friendly_url;
			lockLocationChecking = true;
			
			if(slideUp)
				$.scrollTo('#product_list', 400);
			updateProductUrl();
			
			$('.hide-action').each(function() {
				hideFilterValueAction(this);
			});
		}
	});
	ajaxQueries.push(ajaxQuery);
}

function initLocationChange(func, time)
{
	if(!time) time = 500;
	var current_friendly_url = getUrlParams();
	setInterval(function()
	{
		if(getUrlParams() != current_friendly_url && !lockLocationChecking)
		{
			// Don't reload page if current_friendly_url and real url match
			if (current_friendly_url.replace(/^#(\/)?/, '') == getUrlParams().replace(/^#(\/)?/, ''))
				return;
			
			lockLocationChecking = true;
			reloadContent('&selected_filters='+getUrlParams().replace(/^#/, ''));
		}
		else {
			lockLocationChecking = false;
			current_friendly_url = getUrlParams();
		}
	}, time);
}

function getUrlParams()
{
	var params = current_friendly_url;
	if(window.location.href.split('#').length == 2 && window.location.href.split('#')[1] != '')
		params = '#'+window.location.href.split('#')[1];
	return params;
}

function updateProductUrl()
{
	// Adding the filters to URL product
	if (typeof(param_product_url) != 'undefined' && param_product_url != '' && param_product_url !='#') {
		$.each($('ul#product_list li.ajax_block_product .product_img_link,'+
				'ul#product_list li.ajax_block_product h3 a,'+
				'ul#product_list li.ajax_block_product .product_desc a,'+
				'ul#product_list li.ajax_block_product .lnk_view'), function() {
			$(this).attr('href', $(this).attr('href') + param_product_url);
		});
	}
}


/**
 * Copy of the php function utf8_decode()
 */
function utf8_decode (utfstr) {
	var res = '';
	for (var i = 0; i < utfstr.length;) {
		var c = utfstr.charCodeAt(i);

		if (c < 128)
		{
			res += String.fromCharCode(c);
			i++;
		}
		else if((c > 191) && (c < 224))
		{
			var c1 = utfstr.charCodeAt(i+1);
			res += String.fromCharCode(((c & 31) << 6) | (c1 & 63));
			i += 2;
		}
		else
		{
			var c1 = utfstr.charCodeAt(i+1);
			var c2 = utfstr.charCodeAt(i+2);
			res += String.fromCharCode(((c & 15) << 12) | ((c1 & 63) << 6) | (c2 & 63));
			i += 3;
		}
	}
	return res;
}


/**
 * Return a formatted price
 * Copy from tools.js
 */
function blocklayeredFormatCurrency(price, currencyFormat, currencySign, currencyBlank)
{
	// if you modified this function, don't forget to modify the PHP function displayPrice (in the Tools.php class)
	blank = '';
	price = parseFloat(price.toFixed(6));
	price = ps_round(price, priceDisplayPrecision);
	if (currencyBlank > 0)
		blank = ' ';
	if (currencyFormat == 1)
		return currencySign + blank + blocklayeredFormatNumber(price, priceDisplayPrecision, ',', '.');
	if (currencyFormat == 2)
		return (blocklayeredFormatNumber(price, priceDisplayPrecision, ' ', ',') + blank + currencySign);
	if (currencyFormat == 3)
		return (currencySign + blank + blocklayeredFormatNumber(price, priceDisplayPrecision, '.', ','));
	if (currencyFormat == 4)
		return (blocklayeredFormatNumber(price, priceDisplayPrecision, ',', '.') + blank + currencySign);
	return price;
}


/**
 * Return a formatted number
 * Copy from tools.js
 */
function blocklayeredFormatNumber(value, numberOfDecimal, thousenSeparator, virgule)
{
	value = value.toFixed(numberOfDecimal);
	var val_string = value+'';
	var tmp = val_string.split('.');
	var abs_val_string = (tmp.length == 2) ? tmp[0] : val_string;
	var deci_string = ('0.' + (tmp.length == 2 ? tmp[1] : 0)).substr(2);
	var nb = abs_val_string.length;

	for (var i = 1 ; i < 4; i++)
		if (value >= Math.pow(10, (3 * i)))
			abs_val_string = abs_val_string.substring(0, nb - (3 * i)) + thousenSeparator + abs_val_string.substring(nb - (3 * i));

	if (parseInt(numberOfDecimal) == 0)
		return abs_val_string;
	return abs_val_string + virgule + (deci_string > 0 ? deci_string : '00');
}
