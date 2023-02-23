/*
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

/**
 * Handles loading of product tabs
 */
function ProductTabsManager(){
	var self = this;
	this.product_tabs = [];
	this.tabs_to_preload = [];
	this.current_request;
	this.stack_done = [];
	this.page_reloading = false;
	this.has_error_loading_tabs = false;

	/**
	* Show / Hide languages semaphore
	*/
	this.allow_hide_other_languages = true;

	this.setTabs = function(tabs){
		this.product_tabs = tabs;
	}

	/**
	 * Schedule execution of onReady() function for each tab and bind events
	 */
	this.init = function() {
		for (var tab_name in this.product_tabs) {
			if (this.product_tabs[tab_name].onReady !== undefined && this.product_tabs[tab_name] !== this.product_tabs['Pack'])
			{
				this.onLoad(tab_name, this.product_tabs[tab_name].onReady);
			}
		}

		$('.shopList.chzn-done').on('change', function(){
			if (self.current_request)
			{
				self.page_reloading = true;
				self.current_request.abort();
			}
		});

		$(window).on('beforeunload', function() {
			self.page_reloading = true;
		});
	}

	/**
	 * Execute a callback function when a specific tab has finished loading or right now if the tab has already loaded
	 *
	 * @param tab_name name of the tab that is checked for loading
	 * @param callback_function function to call
	 */
	this.onLoad = function (tab_name, callback)
	{
		var container = $('#product-tab-content-' + tab_name);
		// Some containers are not loaded depending on the shop configuration
		if (container.length === 0)
			return;

		// onReady() is always called after the dom has been created for the tab (similar to $(document).ready())
		if (container.hasClass('not-loaded'))
			container.bind('loaded', callback);
		else
			callback();
	}

	/**
	 * Get a single tab or recursively get tabs in stack then display them
	 *
	 * @param string tab_name name of the tab
	 * @param boolean selected is the tab selected
	 */
	this.display = function (tab_name, selected)
	{
		var tab_selector = $("#product-tab-content-" + tab_name);
		$('#product-tab-content-wait').hide();

		// Is the tab already being loaded?
		if (tab_selector.hasClass('not-loaded') && !tab_selector.hasClass('loading'))
		{
			// Mark the tab as being currently loading
			tab_selector.addClass('loading');

			// send $_POST array with the request to be able to retrieve posted data if there was an error while saving product
			var data;
			var send_type = 'GET';
			if (save_error)
			{
				send_type = 'POST';
				data = post_data;
				// set key_tab so that the ajax call returns the display for the current tab
				data.key_tab = tab_name;
			}
			return $.ajax({
				url : $('#link-' + tab_name).attr('href') + '&ajax=1' + ($('#page').length ? '&page=' + parseInt($('#page').val()) : '') + '&rand=' + + new Date().getTime(),
				async : true,
				cache: false, // cache needs to be set to false or IE will cache the page with outdated product values
				type: send_type,
				headers: { "cache-control": "no-cache" },
				data: data,
				timeout: 30000,
				success : function(data)
				{
					tab_selector.html(data).find('.dropdown-toggle').dropdown();
					tab_selector.removeClass('not-loaded');

					if (selected)
					{
						$("#link-"+tab_name).addClass('selected');
						$('#product-tab-content-wait').hide();
						tab_selector.show();
					}
					self.stack_done.push(tab_name);
					tab_selector.trigger('loaded');
				},
				complete : function(data)
				{
					tab_selector.removeClass('loading');
					if (selected)
					{
						tab_selector.trigger('displayed');
					}
				},
				beforeSend : function(data)
				{
					// don't display the loading notification bar
					if (typeof(ajax_running_timeout) !== 'undefined')
						clearTimeout(ajax_running_timeout);
					if (selected) {
						$('#product-tab-content-wait').show();
					}
				}
			});
		}
	}

	/**
	 * Send an ajax call for each tab in the stack
	 *
	 * @param array stack contains tab names as strings
	 */
	this.displayBulk = function(stack){
		this.current_request = this.display(stack[0], false);

		if (this.current_request !== undefined)
		{
			this.current_request.complete(function(request, status) {
				var wrong_statuses = new Array('abort', 'error', 'timeout');
				var wrong_status_code = new Array(400, 401, 403, 404, 405, 406, 408, 410, 413, 429, 499, 500, 502, 503, 504);

				if ((in_array(status, wrong_statuses) || in_array(request.status, wrong_status_code)) && !self.page_reloading) {
					var current_tab = '';
					if (request.responseText !== 'undefined' && request.responseText && request.responseText.length) {
						current_tab = $(request.responseText).filter('.product-tab').attr('id').replace('product-', '');
					}

					jAlert((current_tab ? 'Tab : ' + current_tab : '') + ' (' + (request.status ? request.status + ' ' : '' ) + request.statusText + ')\n' + reload_tab_description, reload_tab_title);
					self.page_reloading = true;
					self.has_error_loading_tabs = true;
					clearTimeout(tabs_running_timeout);
					return false;
				}
				else if (!self.has_error_loading_tabs && (self.stack_done.length === self.tabs_to_preload.length)) {
						$('[name="submitAddproductAndStay"]').each(function() {
							$(this).prop('disabled', false).find('i').removeClass('process-icon-loading').addClass('process-icon-save');
						});
						$('[name="submitAddproduct"]').each(function() {
							$(this).prop('disabled', false).find('i').removeClass('process-icon-loading').addClass('process-icon-save');
						});
						this.allow_hide_other_languages = true;
						clearTimeout(tabs_running_timeout);
						return false;
					}
				return true;
			});
		}
		/*In order to prevent mod_evasive DOSPageInterval (Default 1s)*/
		var time = 0;
		if (mod_evasive) {
			time = 1000;
		}
		var tabs_running_timeout = setTimeout(function(){
			stack.shift();
			if (stack.length > 0) {
				self.displayBulk(stack);
			}
		}, time);
	}
}

// array of product tab objects containing methods and dom bindings
// The ProductTabsManager instance will make sure the onReady() methods of each tabs are executed once the tab has loaded
var product_tabs = [];

/**
 * hide save and save-and-stay buttons
 *
 * @access public
 * @return void
 */
function disableSave()
{
	//$('button[name="submitAddproduct"]').hide();
	//$('button[name="submitAddproductAndStay"]').hide();
}

/**
 * show save and save-and-stay buttons
 *
 * @access public
 * @return void
 */
function enableSave()
{
	$('button[name="submitAddproduct"]').show();
	$('button[name="submitAddproductAndStay"]').show();
}

function handleSaveButtons(e)
{
	msg = [];
	var i = 0;
	// relative to type of product
	if (product_type == product_type_pack)
		msg[i++] = handleSaveButtonsForPack();
	else if (product_type == product_type_pack)
		msg[i++] = handleSaveButtonsForVirtual();
	else
		msg[i++] = handleSaveButtonsForSimple();

	// common for all products
	$("#disableSaveMessage").remove();

	if ($("#name_" + id_lang_default).val() == "" && (!display_multishop_checkboxes || $('input[name=\'multishop_check[name][' + id_lang_default + ']\']').prop('checked')))
		msg[i++] = empty_name_msg;

	// check friendly_url_[defaultlangid] only if name is ok
	else if ($("#link_rewrite_" + id_lang_default).val() == "" && (!display_multishop_checkboxes || $('input[name=\'link_rewrite[name][' + id_lang_default + ']\']').prop('checked')))
		msg[i++] = empty_link_rewrite_msg;

	if (msg.length == 0)
	{
		$("#disableSaveMessage").remove();
		enableSave();
	}
	else
	{
		$("#disableSaveMessage").remove();
		do_not_save = false;
		for (var key in msg)
		{
			if (msg != "")
			{
				if (do_not_save == false)
				{
					$(".leadin").append('<div id="disableSaveMessage" class="alert alert-danger"></div>');
					warnDiv = $("#disableSaveMessage");
					do_not_save = true;
				}
				warnDiv.append('<p id="'+key+'">'+msg[key]+'</p>');
			}
		}
		if (do_not_save)
			disableSave();
		else
			enableSave();
	}
}

function handleSaveButtonsForSimple(){return '';}
function handleSaveButtonsForVirtual(){return '';}

function handleSaveButtonsForPack()
{
	// if no item left in the pack, disable save buttons
	if ($("#inputPackItems").val() == "")
		return empty_pack_msg;
	return '';
}

product_tabs['Seo'] = new function(){
	var self = this;

	this.onReady = function() {
		if ($('#link_rewrite_'+id_lang_default).length)
			if ($('#link_rewrite_'+id_lang_default).val().replace(/^\s+|\s+$/gm,'') == '') {
				updateFriendlyURLByName();
			}

		// Enable writing of the product name when the friendly url field in tab SEO is loaded
		$('.copy2friendlyUrl').removeAttr('disabled');

		displayFlags(languages, id_language, allowEmployeeFormLang);

		if (display_multishop_checkboxes)
			ProductMultishop.checkAllSeo();
	};
}

product_tabs['Prices'] = new function(){
	var self = this;
	// Bind to show/hide new specific price form
	this.toggleSpecificPrice = function (){
		$('#show_specific_price').click(function()
		{
			$('#add_specific_price').slideToggle();

			$('#add_specific_price').append('<input type="hidden" name="submitPriceAddition"/>');

			$('#hide_specific_price').show();
			$('#show_specific_price').hide();
			return false;
		});

		$('#hide_specific_price').click(function()
		{
			$('#add_specific_price').slideToggle();
			$('#add_specific_price').find('input[name=submitPriceAddition]').remove();
			$('#hide_specific_price').hide();
			$('#show_specific_price').show();
			return false;
		});
	};

	/**
	 * Ajax call to delete a specific price
	 *
	 * @param ids
	 * @param token
	 * @param parent
	 */
	this.deleteSpecificPrice = function (url, parent){
		if (typeof url !== 'undefined')
			$.ajax({
				url: url,
				data: {
					ajax: true
				},
				dataType: 'json',
				context: this,
				success: function(data) {
					if (data !== null)
					{
						if (data.status == 'ok')
						{
							showSuccessMessage(data.message);
							parent.remove();
						}
						else
							showErrorMessage(data.message);
					}
				}
			});
	};

	// Bind to delete specific price link
	this.bindDelete = function(){
		$('#specific_prices_list').delegate('a[name="delete_link"]', 'click', function(e){
			e.preventDefault();
			if (confirm(delete_price_rule))
				self.deleteSpecificPrice(this.href, $(this).parents('tr'));
		})
	};

	this.loadInformations = function(select_id, action)
	{
		id_shop = $('#sp_id_shop').val();
		$.ajax({
			url: product_url + '&action='+action+'&ajax=true&id_shop='+id_shop,
			success: function(data) {
				$(select_id + ' option').not(':first').remove();
				$(select_id).append(data);
			}
		});
	}

	this.onReady = function(){
		self.toggleSpecificPrice();
		self.deleteSpecificPrice();
		self.bindDelete();

		$('#sp_id_shop').change(function() {
			self.loadInformations('#sp_id_group','getGroupsOptions');
			self.loadInformations('#spm_currency_0', 'getCurrenciesOptions');
			self.loadInformations('#sp_id_country', 'getCountriesOptions');
		});
		if (display_multishop_checkboxes)
			ProductMultishop.checkAllPrices();
	};
}

product_tabs['Informations'] = new function(){
	var self = this;
	this.bindAvailableForOrder = function (){
		if ($('#active_on').prop('checked'))
		{
			showRedirectProductOptions(false);
			showRedirectProductSelectOptions(false);
		}
		else
			showRedirectProductOptions(true);

		$('#redirect_type').change(function () {
			redirectSelectChange();
		});

		$('input[name="show_at_front"]').on('change', function(){
			if (parseInt($(this).val())) {
				$("#available_for_order_container").show('fast');
				if ($('input[name=available_for_order]:checked').val() == 0) {
					$('#show_price_container').show('fast');
				}
			} else {
				$("#available_for_order_container").hide('fast');
				$('#show_price_container').hide('fast');

			}
		});
		$('input[name=available_for_order]').on('change', function(e) {
			if ($(this).val() == 1) {
				$('#show_price_container').hide('fast');
				$('#allow_multiple_quantity_container').show('fast');
			} else {
				$('#show_price_container').show('fast');
				$('#allow_multiple_quantity_container').hide('fast');
			}
		});

		$('#related_product_autocomplete_input')
			.autocomplete('ajax_products_list.php?exclude_packs=0&excludeVirtuals=0&excludeIds='+id_product, {
				minChars: 1,
				autoFill: true,
				max:20,
				matchContains: true,
				mustMatch:false,
				scroll:false,
				cacheLength:0,
				formatItem: function(item) {
					return item[0]+' - '+item[1];
				}
			}).result(function(e, i){
				if(i != undefined)
					addRelatedProduct(i[1], i[0]);
				$(this).val('');
			});
		 addRelatedProduct(id_product_redirected, product_name_redirected);
	};

	this.bindAutoAddProduct = function (){
		$('input[name="auto_add_to_cart"]').on('change',function(){
			if ($(this).val() == 1) {
				$("#price_addition_type_container").show('fast');
				$("#show_at_front_container").hide('fast');
				// $('#show_at_front_off').prop("checked", true).change();
				$("#available_for_order_container").hide('fast');
				$('#show_price_container').hide('fast');
				$("#allow_multiple_quantity_container").hide('fast');
			} else {
				$("#price_addition_type_container").hide('fast');
				$("#show_at_front_container").show('fast');
				if ($('input[name="show_at_front"]:checked').val() == 1) {
					$("#available_for_order_container").show('fast');
				}
				if ($('input[name=available_for_order]:checked').val() == 0) {
					$('#show_price_container').show('fast');
				}
				if ($('input[name=available_for_order]:checked').val() == 1) {
					$("#allow_multiple_quantity_container").show('fast');
				}
			}
		});
	}

	this.bindTagImage = function (){
		function changeTagImage(){
			var smallImage = $('input[name=smallImage]:checked').attr('value');
			var leftRight = $('input[name=leftRight]:checked').attr('value');
			var imageTypes = $('input[name=imageTypes]:checked').attr('value');
			var tag = '[img-'+smallImage+'-'+leftRight+'-'+imageTypes+']';
			$('#resultImage').val(tag);
		}
		changeTagImage();
		$('#createImageDescription input').change(function(){
			changeTagImage();
		});

		var i = 0;
		$('.addImageDescription').click(function(){
			if (i == 0){
				$('#createImageDescription').animate({
					opacity: 1, height: 'toggle'
					}, 500);
				i = 1;
			}else{
				$('#createImageDescription').animate({
					opacity: 0, height: 'toggle'
					}, 500);
				i = 0;
			}
		});
	};

	this.switchProductType = function(){

		$('#service_product_type').on('change',function(){
			if (parseInt($(this).val()) == with_room_type) {
				$('#associated_hotel_rooms_tree').show('fast');
				$('#show_at_front_container').show('fast');
				$('#product_options').show('fast');
				$('#independent_product_info').hide('fast');
			} else {
				$('#associated_hotel_rooms_tree').hide('fast');
				$('#show_at_front_container').hide('fast');
				$('#product_options').hide('fast');
				$('#independent_product_info').show('fast');
			}
		});

		$('#simple_product').attr('checked', true);

		$('input[name="type_product"]').on('click', function(e)
		{
			// this handle the save button displays and warnings
			handleSaveButtons();
		});
	};
	this.onReady = function(){
		self.bindAvailableForOrder();
		self.bindAutoAddProduct();
		self.bindTagImage();
		self.switchProductType();

		if (display_multishop_checkboxes)
		{
			ProductMultishop.checkAllInformations();
			var active_click = function()
			{
				if (!$('input[name=\'multishop_check[active]\']').prop('checked'))
				{
					$('.draft').hide();
					showOptions(true);
				}
				else
				{
					var checked = $('#active_on').prop('checked');
					toggleDraftWarning(checked);
					showOptions(checked);
				}
			};
			$('input[name=\'multishop_check[active]\']').click(active_click);
			active_click();
		}
	};


}

product_tabs['Images'] = new function(){
	this.onReady = function(){
		displayFlags(languages, id_language, allowEmployeeFormLang);
	}
}

product_tabs['Features'] = new function(){
	this.onReady = function(){
		displayFlags(languages, id_language, allowEmployeeFormLang);
	}
}

/**
 * Update the product image list position buttons
 *
 * @param DOM table imageTable
 */
function refreshImagePositions(imageTable)
{
	var reg = /_[0-9]$/g;
	var up_reg  = new RegExp("imgPosition=[0-9]+&");

	imageTable.find("tbody tr").each(function(i,el) {
		$(el).find("td.positionImage").html(i + 1);
	});
	imageTable.find("tr td.dragHandle a:hidden").show();
	imageTable.find("tr td.dragHandle:first a:first").hide();
	imageTable.find("tr td.dragHandle:last a:last").hide();
}

/**
 * Generic ajax call for actions expecting a json return
 *
 * @param url
 * @param action
 * @param success_callback called if the return status is 'ok' (optional)
 * @param failure_callback called if the return status is not 'ok' (optional)
 */
function ajaxAction (url, action, success_callback, failure_callback){
	$.ajax({
		url: url,
		data: {
			id_product: id_product,
			action: action,
			ajax: true
		},
		dataType: 'json',
		context: this,
		success: function(data) {
			if (data.status == 'ok')
			{
				showSuccessMessage(data.confirmations);
				if (typeof success_callback == 'function')
					success_callback();
			}
			else
			{
				showErrorMessage(data.error);
				if (typeof failure_callback == 'function')
					failure_callback();
			}
		},
		error : function(data){
			showErrorMessage(("[TECHNICAL ERROR]"));
		}
	});
};

var ProductMultishop = new function()
{
	var self = this;
	this.load_tinymce = {};

	this.checkField = function(checked, id, type)
	{
		checked = !checked;
		switch (type)
		{
			case 'tinymce' :
				$('#'+id).attr('disabled', checked);
				if (typeof self.load_tinymce[id] == 'undefined')
					self.load_tinymce[id] = checked;
				else
				{
					if (checked)
						tinyMCE.get(id).hide();
					else
						tinyMCE.get(id).show();
				}
				break;
			case 'radio' :
				$('input[name=\''+id+'\']').attr('disabled', checked);
				break;
			case 'show_price' :
				if ($('input[name=\'available_for_order\']').prop('checked'))
					checked = true;
				$('input[name=\''+id+'\']').attr('disabled', checked);
				break;
			case 'price' :
				$('#priceTE').attr('disabled', checked);
				$('#priceTI').attr('disabled', checked);
				break;
			case 'unit_price' :
				$('#unit_price').attr('disabled', checked);
				$('#unity').attr('disabled', checked);
				break;
			case 'attribute_price_impact' :
				$('#attribute_price_impact').attr('disabled', checked);
				$('#attribute_price').attr('disabled', checked);
				$('#attribute_priceTI').attr('disabled', checked);
				break;
			case 'category_box' :
				$('#'+id+' input[type=checkbox]').attr('disabled', checked);
				if (!checked) {
					$('#check-all-'+id).removeAttr('disabled');
					$('#uncheck-all-'+id).removeAttr('disabled');
				} else {
					$('#check-all-'+id).attr('disabled', 'disabled');
					$('#uncheck-all-'+id).attr('disabled', 'disabled');
				}
				break;
			case 'seo_friendly_url':
				$('#'+id).attr('disabled', checked);
				$('#generate-friendly-url').attr('disabled', checked);
				break;
			case 'uploadable_files':
				$('input[name^=label_0_]').attr('disabled', checked);
				$('#'+id).attr('disabled', checked);
				break;
			case 'text_fields':
				$('input[name^=label_1_]').attr('disabled', checked);
				$('#'+id).attr('disabled', checked);
				break;
			default :
				$('#'+id).attr('disabled', checked);
				break;
		}
	};

	this.checkAllInformations = function()
	{
		ProductMultishop.checkField($('input[name=\'multishop_check[active]\']').prop('checked'), 'active', 'radio');
		ProductMultishop.checkField($('input[name=\'multishop_check[visibility]\']').prop('checked'), 'visibility');
		ProductMultishop.checkField($('input[name=\'multishop_check[available_for_order]\']').prop('checked'), 'available_for_order');
		ProductMultishop.checkField($('input[name=\'multishop_check[show_price]\']').prop('checked'), 'show_price', 'show_price');
		ProductMultishop.checkField($('input[name=\'multishop_check[online_only]\']').prop('checked'), 'online_only');
		ProductMultishop.checkField($('input[name=\'multishop_check[condition]\']').prop('checked'), 'condition');
		$.each(languages, function(k, v)
		{
			ProductMultishop.checkField($('input[name=\'multishop_check[name]['+v.id_lang+']\']').prop('checked'), 'name_'+v.id_lang);
			ProductMultishop.checkField($('input[name=\'multishop_check[description_short]['+v.id_lang+']\']').prop('checked'), 'description_short_'+v.id_lang, 'tinymce');
			ProductMultishop.checkField($('input[name=\'multishop_check[description]['+v.id_lang+']\']').prop('checked'), 'description_'+v.id_lang, 'tinymce');
		});
	};

	this.checkAllPrices = function()
	{
		ProductMultishop.checkField($('input[name=\'multishop_check[wholesale_price]\']').prop('checked'), 'wholesale_price');
		ProductMultishop.checkField($('input[name=\'multishop_check[price]\']').prop('checked'), 'price', 'price');
		ProductMultishop.checkField($('input[name=\'multishop_check[id_tax_rules_group]\']').prop('checked'), 'id_tax_rules_group');
		ProductMultishop.checkField($('input[name=\'multishop_check[unit_price]\']').prop('checked'), 'unit_price', 'unit_price');
		ProductMultishop.checkField($('input[name=\'multishop_check[on_sale]\']').prop('checked'), 'on_sale');
		ProductMultishop.checkField($('input[name=\'multishop_check[ecotax]\']').prop('checked'), 'ecotax');
	};

	this.checkAllSeo = function()
	{
		$.each(languages, function(k, v)
		{
			ProductMultishop.checkField($('input[name=\'multishop_check[meta_title]['+v.id_lang+']\']').prop('checked'), 'meta_title_'+v.id_lang);
			ProductMultishop.checkField($('input[name=\'multishop_check[meta_description]['+v.id_lang+']\']').prop('checked'), 'meta_description_'+v.id_lang);
			ProductMultishop.checkField($('input[name=\'multishop_check[meta_keywords]['+v.id_lang+']\']').prop('checked'), 'meta_keywords_'+v.id_lang);
			ProductMultishop.checkField($('input[name=\'multishop_check[link_rewrite]['+v.id_lang+']\']').prop('checked'), 'link_rewrite_'+v.id_lang, 'seo_friendly_url');
		});
	};
};

var tabs_manager = new ProductTabsManager();
tabs_manager.setTabs(product_tabs);

$(document).ready(function() {
	// The manager schedules the onReady() methods of each tab to be called when the tab is loaded
	tabs_manager.init();
	$("#name_" + id_lang_default + ",#link_rewrite_" + id_lang_default)
		.on("change", function(e) {
			$(this).trigger("handleSaveButtons");
		});
	// bind that custom event
	$("#name_" + id_lang_default + ",#link_rewrite_" + id_lang_default)
		.on("handleSaveButtons", function(e) {
			handleSaveButtons()
		});

	// Pressing enter in an input field should not submit the form
	$('#product_form').delegate('input', 'keypress', function(e) {
			var code = null;
		code = (e.keyCode ? e.keyCode : e.which);
		return (code == 13) ? false : true;
	});

	$('#product_form').submit(function(e) {
		$('#selectedCarriers option').attr('selected', 'selected');
	});
});
