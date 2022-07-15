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

function loadPack() {
	var container = $('#product-pack-container');
	var id_product = $('input[name=id_product]').first().val();
	var data;
	$.ajax({
		url : "index.php?controller=AdminProducts" + "&token=" + token + "&id_product=" + id_product + "&action=Pack" + "&updateproduct" + "&ajax=1" + '&rand=' + new Date().getTime(),
		async : true,
		cache: false, // cache needs to be set to false or IE will cache the page with outdated product values
		type: 'GET',
		headers: { "cache-control": "no-cache" },
		data: data,
		success : function(data){
			$('#product-pack-container').html(data);
			product_tabs['Pack'].onReady();
		}
	});
}

// array of product tab objects containing methods and dom bindings
// The ProductTabsManager instance will make sure the onReady() methods of each tabs are executed once the tab has loaded
var product_tabs = [];

product_tabs['Customization'] = new function(){
	this.onReady = function(){
		if (display_multishop_checkboxes)
		ProductMultishop.checkAllCustomization();
	}
}
product_tabs['Combinations'] = new function(){
	var self = this;
	this.bindEdit = function(){
		$('table.configuration').delegate('a.edit', 'click', function(e){
			e.preventDefault();
			e.stopPropagation();
			editProductAttribute(this.href, $(this).closest('tr'));
		});

		function editProductAttribute (url, parent){
			$.ajax({
				url: url,
				data: {
					id_product: id_product,
					ajax: true,
					action: 'editProductAttribute'
				},
				context: document.body,
				dataType: 'json',
				context: this,
				success: function(data) {
					// color the selected line
					parent.siblings().removeClass('selected-line');
					parent.addClass('selected-line');

					$('#add_new_combination').show();
					$('#attribute_quantity').show();
					$('#product_att_list').html('');
					self.removeButtonCombination('update');
					scroll_if_anchor('#add_new_combination');
					var wholesale_price = Math.abs(data[0]['wholesale_price']);
					var price = data[0]['price'];
					var weight = data[0]['weight'];
					var unit_impact = data[0]['unit_price_impact'];
					var reference = data[0]['reference'];
					var ean = data[0]['ean13'];
					var quantity = data[0]['quantity'];
					var image = false;
					var product_att_list = new Array();
					for(i=0;i<data.length;i++)
					{
						product_att_list.push(data[i]['group_name']+' : '+data[i]['attribute_name']);
						product_att_list.push(data[i]['id_attribute']);
					}

					var id_product_attribute = data[0]['id_product_attribute'];
					var default_attribute = data[0]['default_on'];
					var eco_tax = data[0]['ecotax'];
					var upc = data[0]['upc'];
					var minimal_quantity = data[0]['minimal_quantity'];
					var available_date = data[0]['available_date'];

					if (wholesale_price != 0 && wholesale_price > 0)
					{
						$("#attribute_wholesale_price_full").show();
						$("#attribute_wholesale_price_blank").hide();
					}
					else
					{
						$("#attribute_wholesale_price_full").hide();
						$("#attribute_wholesale_price_blank").show();
					}
					self.fillCombination(
						wholesale_price,
						price,
						weight,
						unit_impact,
						reference,
						ean,
						quantity,
						image,
						product_att_list,
						id_product_attribute,
						default_attribute,
						eco_tax,
						upc,
						minimal_quantity,
						available_date
					);
					calcImpactPriceTI();
				}
			});
		}
	};

	this.defaultProductAttribute = function(url, item){
		$.ajax({
			url: url,
			data: {
				id_product: id_product,
				action: 'defaultProductAttribute',
				ajax: true
			},
			context: document.body,
			dataType: 'json',
			context: this,
			success: function(data) {
				if (data.status == 'ok')
				{
					showSuccessMessage(data.message);
					$('.highlighted').removeClass('highlighted');
					$(item).closest('tr').addClass('highlighted');
				}
				else
					showErrorMessage(data.message);
			}
		});
	};

	this.bindDefault = function(){
		$('table.configuration').delegate('a.default', 'click', function(e){
			e.preventDefault();
			self.defaultProductAttribute(this.href, this);
		});
	};

	this.deleteProductAttribute = function(url, parent){
		$.ajax({
			url: url,
			data: {
				id_product: id_product,
				action: 'deleteProductAttribute',
				ajax: true
			},
			context: document.body,
			dataType: 'json',
			context: this,
			success: function(data) {
				if (data.status == 'ok')
				{
					showSuccessMessage(data.message);
					parent.remove();
					if (data.id_product_attribute)
						if (data.attribute)
						{
							var td = $('#qty_' + data.id_product_attribute);
							td.attr('id', 'qty_0');
							td.children('input').val('0').attr('name', 'qty_0');
							td.next('td').text(data.attribute[0].name);
						}
						else
							$('#qty_' + data.id_product_attribute).parent().hide();
				}
				else
					showErrorMessage(data.message);
			}
		});
	};

	this.bindDelete = function() {
		$('table.configuration').delegate('a.delete', 'click', function(e){
			e.preventDefault();
			self.deleteProductAttribute(this.href, $(this).closest('tr'));
		});
	};

	this.removeButtonCombination = function(item)
	{
		$('#add_new_combination').show();
		$('#desc-product-newCombination').children('i').first().removeClass('process-icon-new');
		$('#desc-product-newCombination').children('i').first().addClass('process-icon-minus');
		$('#desc-product-newCombination').children('span').first().html(msg_cancel_combination);
		$('id_product_attribute').val(0);
		self.init_elems();
	};

	this.addButtonCombination = function(item)
	{
		$('#add_new_combination').hide();
		$('#desc-product-newCombination').children('i').first().removeClass('process-icon-minus');
		$('#desc-product-newCombination').children('i').first().addClass('process-icon-new');
		$('#desc-product-newCombination').children('span').first().html(msg_new_combination);
	};

	this.bindToggleAddCombination = function (){
		$('#desc-product-newCombination').click(function(e) {
			e.preventDefault();

			if ($(this).children('i').first().hasClass('process-icon-new'))
				self.removeButtonCombination('add');
			else
			{
				self.addButtonCombination('add');
				$('#id_product_attribute').val(0);
			}
		});
	};

	this.fillCombination = function(wholesale_price, price_impact, weight_impact, unit_impact, reference,
	ean, quantity, image, old_attr, id_product_attribute, default_attribute, eco_tax, upc, minimal_quantity, available_date)
	{
		var link = '';
		self.init_elems();
		$('#stock_mvt_attribute').show();
		$('#initial_stock_attribute').hide();
		$('#attribute_quantity').html(quantity);
		$('#attribute_quantity').show();
		$('#attr_qty_stock').show();

		$('#attribute_minimal_quantity').val(minimal_quantity);

		getE('attribute_reference').value = reference;

		getE('attribute_ean13').value = ean;
		getE('attribute_upc').value = upc;
		getE('attribute_wholesale_price').value = Math.abs(wholesale_price);
		getE('attribute_price').value = ps_round(Math.abs(price_impact), 2);
		getE('attribute_priceTEReal').value = Math.abs(price_impact);
		getE('attribute_weight').value = Math.abs(weight_impact);
		getE('attribute_unity').value = Math.abs(unit_impact);
		if ($('#attribute_ecotax').length != 0)
			getE('attribute_ecotax').value = eco_tax;

		if (default_attribute == 1)
			getE('attribute_default').checked = true;
		else
			getE('attribute_default').checked = false;

		if (price_impact < 0)
		{
			getE('attribute_price_impact').options[getE('attribute_price_impact').selectedIndex].value = -1;
			getE('attribute_price_impact').selectedIndex = 2;
		}
		else if (!price_impact)
		{
			getE('attribute_price_impact').options[getE('attribute_price_impact').selectedIndex].value = 0;
			getE('attribute_price_impact').selectedIndex = 0;
		}
		else if (price_impact > 0)
		{
			getE('attribute_price_impact').options[getE('attribute_price_impact').selectedIndex].value = 1;
			getE('attribute_price_impact').selectedIndex = 1;
		}
		if (weight_impact < 0)
		{
			getE('attribute_weight_impact').options[getE('attribute_weight_impact').selectedIndex].value = -1;
			getE('attribute_weight_impact').selectedIndex = 2;
		}
		else if (!weight_impact)
		{
			getE('attribute_weight_impact').options[getE('attribute_weight_impact').selectedIndex].value = 0;
			getE('attribute_weight_impact').selectedIndex = 0;
		}
		else if (weight_impact > 0)
		{
			getE('attribute_weight_impact').options[getE('attribute_weight_impact').selectedIndex].value = 1;
			getE('attribute_weight_impact').selectedIndex = 1;
		}
		if (unit_impact < 0)
		{
			getE('attribute_unit_impact').options[getE('attribute_unit_impact').selectedIndex].value = -1;
			getE('attribute_unit_impact').selectedIndex = 2;
		}
		else if (!unit_impact)
		{
			getE('attribute_unit_impact').options[getE('attribute_unit_impact').selectedIndex].value = 0;
			getE('attribute_unit_impact').selectedIndex = 0;
		}
		else if (unit_impact > 0)
		{
			getE('attribute_unit_impact').options[getE('attribute_unit_impact').selectedIndex].value = 1;
			getE('attribute_unit_impact').selectedIndex = 1;
		}

		$("#add_new_combination").show();

		/* Reset all combination images */
		combinationImages = $('#id_image_attr').find("input[id^=id_image_attr_]");
		combinationImages.each(function() {
			this.checked = false;
		});

		/* Check combination images */
		if (typeof(combination_images[id_product_attribute]) != 'undefined')
			for (i = 0; i < combination_images[id_product_attribute].length; i++)
				$('#id_image_attr_' + combination_images[id_product_attribute][i]).attr('checked', true);
		check_impact();
		check_weight_impact();
		check_unit_impact();

		var elem = getE('product_att_list');

		for (var i = 0; i < old_attr.length; i++)
		{
			var opt = document.createElement('option');
			opt.text = old_attr[i++];
			opt.value = old_attr[i];
			try {
				elem.add(opt, null);
			}
			catch(ex) {
				elem.add(opt);
			}
		}
		getE('id_product_attribute').value = id_product_attribute;

		$('#available_date_attribute').val(available_date);
	};

	this.init_elems = function()
	{
		var impact = getE('attribute_price_impact');
		var impact2 = getE('attribute_weight_impact');
		var elem = getE('product_att_list');

		if (elem.length)
			for (i = elem.length - 1; i >= 0; i--)
				if (elem[i])
					elem.remove(i);

		$('input[name="id_image_attr[]"]').each(function (){
			$(this).attr('checked', false);
		});

		$('#attribute_default').attr('checked', false);

		getE('attribute_price_impact').selectedIndex = 0;
		getE('attribute_weight_impact').selectedIndex = 0;
		getE('attribute_unit_impact').selectedIndex = 0;
		$('#span_unit_impact').hide();
		$('#unity_third').html($('#unity_second').html());

		if ($('#unity').is())
			if ($('#unity').get(0).value.length > 0)
				$('#tr_unit_impact').show();
			else
				$('#tr_unit_impact').hide();
		try
		{
			if (impact.options[impact.selectedIndex].value == 0)
				$('#span_impact').hide();
			if (impact2.options[impact.selectedIndex].value == 0)
				getE('span_weight_impact').style.display = 'none';
		}
		catch (e)
		{
			$('#span_impact').hide();
			getE('span_weight_impact').style.display = 'none';
		}
	};

	this.onReady = function(){
		self.bindEdit();
		self.bindDefault();
		self.bindDelete();
		self.bindToggleAddCombination();
		if (display_multishop_checkboxes)
			ProductMultishop.checkAllCombinations();
	};
}

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

product_tabs['Associations'] = new function(){
	var self = this;
	this.initAccessoriesAutocomplete = function (){
		$('#product_autocomplete_input')
			.autocomplete('ajax_products_list.php?exclude_packs=0&excludeVirtuals=0', {
				minChars: 1,
				autoFill: true,
				max:20,
				matchContains: true,
				mustMatch:false,
				scroll:false,
				cacheLength:0,
				formatItem: function(item) {
					return item[1]+' - '+item[0];
				}
			}).result(self.addAccessory);

		$('#product_autocomplete_input').setOptions({
			extraParams: {
				excludeIds : self.getAccessoriesIds()
			}
		});
	};

	this.getAccessoriesIds = function()
	{
		if ($('#inputAccessories').val() === undefined)
			return id_product;
		return id_product + ',' + $('#inputAccessories').val().replace(/\-/g,',');
	}

	this.addAccessory = function(event, data, formatted)
	{
		if (data == null)
			return false;
		var productId = data[1];
		var productName = data[0];

		var $divAccessories = $('#divAccessories');
		var $inputAccessories = $('#inputAccessories');
		var $nameAccessories = $('#nameAccessories');

		/* delete product from select + add product line to the div, input_name, input_ids elements */
		$divAccessories.html($divAccessories.html() + '<div class="form-control-static"><button type="button" class="delAccessory btn btn-default" name="' + productId + '"><i class="icon-remove text-danger"></i></button>&nbsp;'+ productName +'</div>');
		$nameAccessories.val($nameAccessories.val() + productName + '¤');
		$inputAccessories.val($inputAccessories.val() + productId + '-');
		$('#product_autocomplete_input').val('');
		$('#product_autocomplete_input').setOptions({
			extraParams: {excludeIds : self.getAccessoriesIds()}
		});
	};

	this.delAccessory = function(id)
	{
		var div = getE('divAccessories');
		var input = getE('inputAccessories');
		var name = getE('nameAccessories');

		// Cut hidden fields in array
		var inputCut = input.value.split('-');
		var nameCut = name.value.split('¤');

		if (inputCut.length != nameCut.length)
			return jAlert('Bad size');

		// Reset all hidden fields
		input.value = '';
		name.value = '';
		div.innerHTML = '';
		for (i in inputCut)
		{
			// If empty, error, next
			if (!inputCut[i] || !nameCut[i])
				continue ;

			// Add to hidden fields no selected products OR add to select field selected product
			if (inputCut[i] != id)
			{
				input.value += inputCut[i] + '-';
				name.value += nameCut[i] + '¤';
				div.innerHTML += '<div class="form-control-static"><button type="button" class="delAccessory btn btn-default" name="' + inputCut[i] +'"><i class="icon-remove text-danger"></i></button>&nbsp;' + nameCut[i] + '</div>';
			}
			else
				$('#selectAccessories').append('<option selected="selected" value="' + inputCut[i] + '-' + nameCut[i] + '">' + inputCut[i] + ' - ' + nameCut[i] + '</option>');
		}

		$('#product_autocomplete_input').setOptions({
			extraParams: {excludeIds : self.getAccessoriesIds()}
		});
	};

	/**
	 * Update the manufacturer select element with the list of existing manufacturers
	 */
	this.getManufacturers = function(){
		$.ajax({
				url: 'ajax-tab.php',
				cache: false,
				dataType: 'json',
				data: {
					ajaxProductManufacturers:"1",
					ajax : '1',
					token : token,
					controller : 'AdminProducts',
					action : 'productManufacturers'
				},
				success: function(j) {
					var options;
					if (j) {
						for (var i = 0; i < j.length; i++) {
							options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
						}
					}
					$('select#id_manufacturer').chosen({width: '250px'}).append(options).trigger("chosen:updated");
				},
				error: function(XMLHttpRequest, textStatus, errorThrown)
				{
					$("select#id_manufacturer").replaceWith("<p id=\"id_manufacturer\">[TECHNICAL ERROR] ajaxProductManufacturers: "+textStatus+"</p>");
				}
		});
	};

	this.onReady = function(){
		self.initAccessoriesAutocomplete();
		self.getManufacturers();
		$('#divAccessories').delegate('.delAccessory', 'click', function(){
			self.delAccessory($(this).attr('name'));
		});
		if (display_multishop_checkboxes)
			ProductMultishop.checkAllAssociations();
	};
}

product_tabs['Attachments'] = new function(){
	var self = this;
	this.bindAttachmentEvents = function (){
		$("#addAttachment").on('click', function() {
			$("#selectAttachment2 option:selected").each(function(){
				var val = $('#arrayAttachments').val();
				var tab = val.split(',');
				for (var i=0; i < tab.length; i++)
					if (tab[i] == $(this).val())
						return false;
				$('#arrayAttachments').val(val+$(this).val()+',');
			});
			return !$("#selectAttachment2 option:selected").remove().appendTo("#selectAttachment1");
		});
		$("#removeAttachment").on('click', function() {
			$("#selectAttachment1 option:selected").each(function(){
				var val = $('#arrayAttachments').val();
				var tab = val.split(',');
				var tabs = '';
				for (var i=0; i < tab.length; i++)
					if (tab[i] != $(this).val())
					{
						tabs = tabs+','+tab[i];
						$('#arrayAttachments').val(tabs);
					}
			});
			return !$("#selectAttachment1 option:selected").remove().appendTo("#selectAttachment2");
		});
		$("#product").submit(function() {
			$("#selectAttachment1 option").each(function(i) {
				$(this).attr("selected", "selected");
			});
		});
	};

	this.onReady = function(){
		self.bindAttachmentEvents();
	};
}

product_tabs['Shipping'] = new function(){
	var self = this;

	this.bindCarriersEvents = function (){
		$("#addCarrier").on('click', function() {
			$('#availableCarriers option:selected').each( function() {
					$('#selectedCarriers').append("<option value='"+$(this).val()+"'>"+$(this).text()+"</option>");
				$(this).remove();
			});
			$('#selectedCarriers option').prop('selected', true);

			if ($('#selectedCarriers').find("option").length == 0)
				$('#no-selected-carries-alert').show();
			else
				$('#no-selected-carries-alert').hide();
		});

		$("#removeCarrier").on('click', function() {
			$('#selectedCarriers option:selected').each( function() {
				$('#availableCarriers').append("<option value='"+$(this).val()+"'>"+$(this).text()+"</option>");
				$(this).remove();
			});
			$('#selectedCarriers option').prop('selected', true);

			if ($('#selectedCarriers').find("option").length == 0)
				$('#no-selected-carries-alert').show();
			else
				$('#no-selected-carries-alert').hide();
		});
	};

	this.onReady = function(){
		self.bindCarriersEvents();
	};
}

product_tabs['Informations'] = new function(){
	var self = this;
	this.bindAvailableForOrder = function (){
		$("#available_for_order").click(function()
		{
			if ($(this).is(':checked') || ($('input[name=\'multishop_check[show_price]\']').length && !$('input[name=\'multishop_check[show_price]\']').prop('checked')))
			{
				$('#show_price').attr('checked', true);
				$('#show_price').attr('disabled', true);
			}
			else
			{
				$('#show_price').attr('disabled', false);
			}
		});

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
		if (product_type == product_type_pack)
		{
			$('#pack_product').attr('checked', true);
		}
		else if (product_type == product_type_virtual)
		{
			$('#virtual_product').attr('checked', true);
			$('#condition').attr('disabled', true);
			$('#condition option[value=new]').attr('selected', true);
		}
		else
		{
			$('#simple_product').attr('checked', true);
		}

		$('input[name="type_product"]').on('click', function(e)
		{
			// Reset settings
			$('a[id*="VirtualProduct"]').hide();

			$('#product-pack-container').hide();

			$('div.is_virtual_good').hide();
			$('#is_virtual').val(0);
			tabs_manager.onLoad('VirtualProduct', function(){
				$('#is_virtual_good').removeAttr('checked');
			});

			product_type = $(this).val();
			$('#warn_virtual_combinations').hide();
			$('#warn_pack_combinations').hide();
			// until a product is added in the pack
			// if product is PTYPE_PACK, save buttons will be disabled
			if (product_type == product_type_pack)
			{
				if (has_combinations)
				{
					$('#simple_product').attr('checked', true);
					$('#warn_pack_combinations').show();
				}
				else
				{
					$('#product-pack-container').show();
					// If the pack tab has not finished loaded the changes will be made when the loading event is triggered
					$("#product-tab-content-Pack").bind('loaded', function(){
						$('#ppack').val(1).attr('checked', true).attr('disabled', true);
					});
					$("#product-tab-content-Quantities").bind('loaded', function(){
						$('.stockForVirtualProduct').show();
					});

					$('a[id*="Combinations"]').hide();
					$('a[id*="Shipping"]').show();

					$('#condition').removeAttr('disabled');
					$('#condition option[value=new]').removeAttr('selected');
					$('.stockForVirtualProduct').show();
					// if pack is enabled, if you choose pack, automatically switch to pack page
				}
			}
			else if (product_type == product_type_virtual)
			{
				if (has_combinations)
				{
					$('#simple_product').attr('checked', true);
					$('#warn_virtual_combinations').show();
				}
				else
				{
					$('a[id*="VirtualProduct"]').show();
					$('#is_virtual').val(1);

					tabs_manager.onLoad('VirtualProduct', function(){
						$('#is_virtual_good').attr('checked', true);
						$('#virtual_good').show();
					});

					tabs_manager.onLoad('Quantities', function(){
						$('.stockForVirtualProduct').hide();
					});

					$('a[id*="Combinations"]').hide();
					$('a[id*="Shipping"]').hide();

					tabs_manager.onLoad('Informations', function(){
						$('#condition').attr('disabled', true);
						$('#condition option[value=refurbished]').removeAttr('selected');
						$('#condition option[value=used]').removeAttr('selected');
					});
				}
			}
			else
			{
				// 3rd case : product_type is PTYPE_SIMPLE (0)
				$('a[id*="Combinations"]').show();
				$('a[id*="Shipping"]').show();
				$('#condition').removeAttr('disabled');
				$('#condition option[value=new]').removeAttr('selected');
				$('.stockForVirtualProduct').show();
			}
			// this handle the save button displays and warnings
			handleSaveButtons();
		});
	};
	this.onReady = function(){
		loadPack();
		self.bindAvailableForOrder();
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

product_tabs['Pack'] = new function() {
	var self = this;

	this.bindPackEvents = function () {

		$('.delPackItem').on('click', function() {
			delPackItem($(this).data('delete'), $(this).data('delete-attr'));
		});

		function productFormatResult(item) {
			itemTemplate = "<div class='media'>";
			itemTemplate += "<div class='pull-left'>";
			itemTemplate += "<img class='media-object' width='40' src='" + item.image + "' alt='" + item.name + "'>";
			itemTemplate += "</div>";
			itemTemplate += "<div class='media-body'>";
			itemTemplate += "<h4 class='media-heading'>" + item.name + "</h4>";
			itemTemplate += "<span>REF: " + item.ref + "</span>";
			itemTemplate += "</div>";
			itemTemplate += "</div>";
			return itemTemplate;
		}

		function productFormatSelection(item) {
			return item.name;
		}

		var selectedProduct;
		$('#curPackItemName').select2({
			placeholder: search_product_msg,
			minimumInputLength: 2,
			width: '100%',
			dropdownCssClass: "bootstrap",
			ajax: {
				url: "ajax_products_list.php",
				dataType: 'json',
				data: function (term) {
					return {
						q: term,
						packItself: $('input[name=\'id_product\']').val()
					};
				},
				results: function (data) {
					var excludeIds = getSelectedIds();
					var returnIds = new Array();
					if (data) {
						for (var i = data.length - 1; i >= 0; i--) {
							var is_in = 0;
							for (var j = 0; j < excludeIds.length; j ++) {
								if (data[i].id == excludeIds[j][0] && (typeof data[i].id_product_attribute == 'undefined' || data[i].id_product_attribute == excludeIds[j][1]))
									is_in = 1;
							}
							if (!is_in)
								returnIds.push(data[i]);
						}
						return {
							results: returnIds
						}
					} else {
						return {
							results: []
						}
					}
				}
			},
			formatResult: productFormatResult,
			formatSelection: productFormatSelection,
		})
		.on("select2-selecting", function(e) {
			selectedProduct = e.object
		});

		$('#add_pack_item').on('click', addPackItem);

		function addPackItem() {

			if (selectedProduct) {
				selectedProduct.qty = $('#curPackItemQty').val();
				if (selectedProduct.id == '' || selectedProduct.name == '' && $('#curPackItemQty').valid()) {
					error_modal(error_heading_msg, msg_select_one);
					return false;
				} else if (selectedProduct.qty == '' || !$('#curPackItemQty').valid() || isNaN($('#curPackItemQty').val()) ) {
					error_modal(error_heading_msg, msg_set_quantity);
					return false;
				}

				if (typeof selectedProduct.id_product_attribute === 'undefined')
					selectedProduct.id_product_attribute = 0;

				var divContent = $('#divPackItems').html();
				divContent += '<li class="product-pack-item media-product-pack" data-product-name="' + selectedProduct.name + '" data-product-qty="' + selectedProduct.qty + '" data-product-id="' + selectedProduct.id + '" data-product-id-attribute="' + selectedProduct.id_product_attribute + '">';
				divContent += '<img class="media-product-pack-img" src="' + selectedProduct.image +'"/>';
				divContent += '<span class="media-product-pack-title">' + selectedProduct.name + '</span>';
				divContent += '<span class="media-product-pack-ref">REF: ' + selectedProduct.ref + '</span>';
				divContent += '<span class="media-product-pack-quantity"><span class="text-muted">x</span> ' + selectedProduct.qty + '</span>';
				divContent += '<button type="button" class="btn btn-default delPackItem media-product-pack-action" data-delete="' + selectedProduct.id + '" data-delete-attr="' + selectedProduct.id_product_attribute + '"><i class="icon-trash"></i></button>';
				divContent += '</li>';

				// QTYxID-QTYxID
				// @todo : it should be better to create input for each items and each qty
				// instead of only one separated by x, - and ¤
				var line = selectedProduct.qty + 'x' + selectedProduct.id + 'x' + selectedProduct.id_product_attribute;
				var lineDisplay = selectedProduct.qty + 'x ' + selectedProduct.name;

				$('#divPackItems').html(divContent);
				$('#inputPackItems').val($('#inputPackItems').val() + line  + '-');
				$('#namePackItems').val($('#namePackItems').val() + lineDisplay + '¤');

				$('.delPackItem').on('click', function(e){
					e.preventDefault();
					e.stopPropagation();
					delPackItem($(this).data('delete'), $(this).data('delete-attr'));
				})
				selectedProduct = null;
				$('#curPackItemName').select2("val", "");
				$('.pack-empty-warning').hide();
			} else {
				error_modal(error_heading_msg, msg_select_one);
				return false;
			}
		}

		function delPackItem(id, id_attribute) {

			var reg = new RegExp('-', 'g');
			var regx = new RegExp('x', 'g');

			var input = $('#inputPackItems');
			var name = $('#namePackItems');

			var inputCut = input.val().split(reg);
			var nameCut = name.val().split(new RegExp('¤', 'g'));

			input.val(null);
			name.val(null);
			for (var i = 0; i < inputCut.length; ++i)
				if (inputCut[i]) {
					var inputQty = inputCut[i].split(regx);
					if (inputQty[1] != id || inputQty[2] != id_attribute) {
						input.val( input.val() + inputCut[i] + '-' );
						name.val( name.val() + nameCut[i] + '¤');
					}
				}

			var elem = $('.product-pack-item[data-product-id="' + id + '"][data-product-id-attribute="' + id_attribute + '"]');
			elem.remove();

			if ($('.product-pack-item').length === 0){
				$('.pack-empty-warning').show();
			}
		}

		function getSelectedIds()
		{
			var reg = new RegExp('-', 'g');
			var regx = new RegExp('x', 'g');

			var input = $('#inputPackItems');

			if (input.val() === undefined)
				return '';

			var inputCut = input.val().split(reg);

			var ints = new Array();

			for (var i = 0; i < inputCut.length; ++i)
			{
				var in_ints = new Array();
				if (inputCut[i]) {
					var inputQty = inputCut[i].split(regx);
					in_ints[0] = inputQty[1];
					in_ints[1] = inputQty[2];
				}
				ints[i] = in_ints;
			}

			return ints;
		}
	};

	this.onReady = function(){
		self.bindPackEvents();
	}
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

product_tabs['Quantities'] = new function(){
	var self = this;
	this.ajaxCall = function(data){
		data.ajaxProductQuantity = 1;
		data.id_product = id_product;
		data.token = token;
		data.ajax = 1;
		data.controller = "AdminProducts";
		data.action = "productQuantity";

		$.ajax({
			type: "POST",
			url: "ajax-tab.php",
			data: data,
			dataType: 'json',
			async : true,
			beforeSend: function(xhr, settings)
			{
				$('.product_quantities_button').attr('disabled', 'disabled');
			},
			complete: function(xhr, status)
			{
				$('.product_quantities_button').removeAttr('disabled');
			},
			success: function(msg)
			{
				if (msg.error)
				{
					showErrorMessage(msg.error);
					return;
				}
				showSuccessMessage(quantities_ajax_success);
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				if (textStatus != 'error' || errorThrown != '')
					showErrorMessage(textStatus + ': ' + errorThrown);
			}
		});
	};

	this.refreshQtyAvailabilityForm = function()
	{
		if ($('#depends_on_stock_0').prop('checked'))
		{
			$('.available_quantity').find('input').show();
			$('.available_quantity').find('span').hide();
		}
		else
		{
			$('.available_quantity').find('input').hide();
			$('.available_quantity').find('span').show();
		}
	};

	this.onReady = function(){
		$('#available_date').datepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd'
		});

		$('.depends_on_stock').click(function(e)
		{
			self.refreshQtyAvailabilityForm();
			self.ajaxCall( { actionQty: 'depends_on_stock', value: $(this).val() } );
			if($(this).val() == 0)
				$('.available_quantity input').trigger('change');
		});

		$('.advanced_stock_management').click(function(e)
		{
			var val = 0;
			if ($(this).prop('checked'))
				val = 1;

			self.ajaxCall({actionQty: 'advanced_stock_management', value: val});
			if (val == 1)
			{
				$(this).val(1);
				$('#depends_on_stock_1').attr('disabled', false);
			}
			else
			{
				$(this).val(0);
				$('#depends_on_stock_1').attr('disabled', true);
				$('#depends_on_stock_0').attr('checked', true);
				self.ajaxCall({actionQty: 'depends_on_stock', value: 0});
				self.refreshQtyAvailabilityForm();
			}
			self.refreshQtyAvailabilityForm();
		});

		$('.available_quantity').find('input').change(function(e, init_val)
		{
			self.ajaxCall({actionQty: 'set_qty', id_product_attribute: $(this).parent().attr('id').split('_')[1], value: $(this).val()});
		});

		$('.out_of_stock').click(function(e)
		{
			self.refreshQtyAvailabilityForm();
			self.ajaxCall({actionQty: 'out_of_stock', value: $(this).val()});
		});
		if (display_multishop_checkboxes)
			ProductMultishop.checkAllQuantities();

		$('.pack_stock_type').click(function(e)
		{
			self.refreshQtyAvailabilityForm();
			self.ajaxCall({actionQty: 'pack_stock_type', value: $(this).val()});
		});

		self.refreshQtyAvailabilityForm();
	};
}

product_tabs['Suppliers'] = new function(){
	var self = this;

	this.manageDefaultSupplier = function() {
		var default_is_set = false;
		var availables_radio_buttons = [];
		var radio_buttons = $('input[name="default_supplier"]');

		for (i=0; i<radio_buttons.length; i++)
		{
			var item = $(radio_buttons[i]);

			if (item.is(':disabled'))
			{
				if (item.is(':checked'))
				{
					item.removeAttr("checked");
				}
			}

			if (item.is(':checked'))
			{
				default_is_set = true;
			}
		}

		if (!default_is_set)
		{
			for (i=0; i<radio_buttons.length; i++)
			{
				var item = $(radio_buttons[i]);

				if (item.is(':disabled') == false)
				{
					item.attr("checked", true);
				}
			}
		}
	};

	this.onReady = function(){
		$('.supplierCheckBox').on('click', function() {
			var check = $(this);
			var checkbox = $('#default_supplier_'+check.val());

			if (this.checked)
			{
				// enable default radio button associated
				checkbox.removeAttr('disabled');
			}
			else
			{
				// disable default radio button associated
				checkbox.attr('disabled', true);
			}

			//manage default supplier check
			self.manageDefaultSupplier();
		});
	};
}

product_tabs['VirtualProduct'] = new function(){
	var self = this;

	this.onReady = function(){
		$(".datepicker").datepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd'
		});

		if ($('#is_virtual_good').prop('checked'))
		{
			$('#virtual_good').show();
		}

		$('.is_virtual_good').hide();

		if ( $('input[name=is_virtual_file]:checked').val() == 1)
			$('#is_virtual_file_product').show();
		else
			$('#is_virtual_file_product').hide();

		$('input[name=is_virtual_file]').on('change', function(e) {
			if ($(this).val() == 1)
				$('#is_virtual_file_product').show();
			else
				$('#is_virtual_file_product').hide();
		});

		// Bind file deletion
		$(('#product-tab-content-VirtualProduct')).delegate('a.delete_virtual_product', 'click', function(e){
			e.preventDefault();
			if (confirm(delete_this_file))
			{
				if (!$('#virtual_product_id').val())
				{
					$('#upload_input').show();
					$('#virtual_product_name').val('');
					$('#virtual_product_file').val('');
					$('#upload-confirmation').hide().find('span').remove();
				}
				else
				{
					var object = this;
					ajaxAction(this.href, 'deleteVirtualProduct', function(){
						$(object).closest('tr').remove();
						$('#upload_input').show();
						$('#virtual_product_name').val('');
						$('#virtual_product_file').val('');
						$('#virtual_product_id').remove();
					});
				}

			}
		});
	}
}

product_tabs['Warehouses'] = new function(){
	var self = this;

	this.onReady = function(){
		$('.check_all_warehouse').click(function() {
			//get all checkboxes of current warehouse
			var checkboxes = $('input[name*="'+$(this).val()+'"]');
			var checked = false;

			for (i=0; i<checkboxes.length; i++)
			{
				var item = $(checkboxes[i]);

				if (item.is(':checked'))
				{
					item.removeAttr("checked");
				}
				else
				{
					item.attr("checked", true);
					checked = true;
				}
			}

			if (checked)
				$(this).find('i').removeClass('icon-check-sign').addClass('icon-check-empty');
			else
				$(this).find('i').removeClass('icon-check-empty').addClass('icon-check-sign');
		});
	};
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
			case 'attribute_weight_impact' :
				$('#attribute_weight_impact').attr('disabled', checked);
				$('#attribute_weight').attr('disabled', checked);
				break;
			case 'attribute_unit_impact' :
				$('#attribute_unit_impact').attr('disabled', checked);
				$('#attribute_unity').attr('disabled', checked);
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

	this.checkAllQuantities = function()
	{
		$.each(languages, function(k, v)
		{
			ProductMultishop.checkField($('input[name=\'multishop_check[minimal_quantity]\']').prop('checked'), 'minimal_quantity');
			ProductMultishop.checkField($('input[name=\'multishop_check[available_later]['+v.id_lang+']\']').prop('checked'), 'available_later_'+v.id_lang);
			ProductMultishop.checkField($('input[name=\'multishop_check[available_now]['+v.id_lang+']\']').prop('checked'), 'available_now_'+v.id_lang);
			ProductMultishop.checkField($('input[name=\'multishop_check[available_date]\']').prop('checked'), 'available_date');
		});
	};

	this.checkAllAssociations = function()
	{
		ProductMultishop.checkField($('input[name=\'multishop_check[id_category_default]\']').prop('checked'), 'id_category_default');
		ProductMultishop.checkField($('input[name=\'multishop_check[id_category_default]\']').prop('checked'), 'associated-categories-tree', 'category_box');
	};

	this.checkAllCustomization = function()
	{
		ProductMultishop.checkField($('input[name=\'multishop_check[uploadable_files]\']').prop('checked'), 'uploadable_files', 'uploadable_files');
		ProductMultishop.checkField($('input[name=\'multishop_check[text_fields]\']').prop('checked'), 'text_fields', 'text_fields');
	};

	this.checkAllCombinations = function()
	{
		ProductMultishop.checkField($('input[name=\'multishop_check[attribute_wholesale_price]\']').prop('checked'), 'attribute_wholesale_price');
		ProductMultishop.checkField($('input[name=\'multishop_check[attribute_price_impact]\']').prop('checked'), 'attribute_price_impact', 'attribute_price_impact');
		ProductMultishop.checkField($('input[name=\'multishop_check[attribute_weight_impact]\']').prop('checked'), 'attribute_weight_impact', 'attribute_weight_impact');
		ProductMultishop.checkField($('input[name=\'multishop_check[attribute_unit_impact]\']').prop('checked'), 'attribute_unit_impact', 'attribute_unit_impact');
		ProductMultishop.checkField($('input[name=\'multishop_check[attribute_ecotax]\']').prop('checked'), 'attribute_ecotax');
		ProductMultishop.checkField($('input[name=\'multishop_check[attribute_minimal_quantity]\']').prop('checked'), 'attribute_minimal_quantity');
		ProductMultishop.checkField($('input[name=\'multishop_check[available_date_attribute]\']').prop('checked'), 'available_date_attribute');
		ProductMultishop.checkField($('input[name=\'multishop_check[attribute_default]\']').prop('checked'), 'attribute_default');
	};
};

var tabs_manager = new ProductTabsManager();
tabs_manager.setTabs(product_tabs);

function initDuplicateRoomType(idProduct) {
	$('#modal-duplicate-options').modal('show');
	$('#modal-duplicate-options #duplicate_id_product').attr('value', idProduct);
	var url = 'index.php?controller=AdminProducts&token=' + token;
	var data = {
		ajax: true,
		action: 'getIdHotelByIdProduct',
		id_product: parseInt(idProduct),
	};
	
	$.post(url, data,
		function(response) {
			if (response.status == 'success') {
				var idHotel = response.id_hotel;
				$('#modal-duplicate-options #duplicate_id_hotel option[value="'+idHotel+'"]').attr('selected', 'selected');
			}
		},
		'JSON'
	);
}

$(document).ready(function() {
	id_lang_default = default_language;
	// The manager schedules the onReady() methods of each tab to be called when the tab is loaded
	tabs_manager.init();
	updateCurrentText();
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
		$('#selectAttachment1 option').attr('selected', 'selected');
	});

	$('#modal-duplicate-options .submit-duplicate').on('click', function (e) {
		$('#modal-duplicate-options form.duplicate-options').submit();
	});
});
