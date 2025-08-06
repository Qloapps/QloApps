/*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(document).ready(function(){
	$('.cart_quantity_up').off('click').on('click', function(e){
		e.preventDefault();
		upQuantity($(this).attr('id').replace('cart_quantity_up_', ''));
		$('#' + $(this).attr('id').replace('_up_', '_down_')).removeClass('disabled');
	});
	$('.cart_quantity_down').off('click').on('click', function(e){
		e.preventDefault();
		downQuantity($(this).attr('id').replace('cart_quantity_down_', ''));
	});
	$('.cart_quantity_delete' ).off('click').on('click', function(e){
		e.preventDefault();
		deleteProductFromSummary($(this).attr('id'));
	});
	$('.cart_address_delivery').on('change', function(e){
		changeAddressDelivery($(this));
	});

	$(document).on('click', '.voucher_name', function(e){
		$('#discount_name').val($(this).data('code'));
	});

	$('.cart_quantity_input').typeWatch({
		highlight: true, wait: 600, captureLength: 0, callback: function(val){
			updateQty(val, true, this.el);
		}
	});

	cleanSelectAddressDelivery();

	refreshDeliveryOptions();

	$('.delivery_option_radio').on('change', function(){
		refreshDeliveryOptions();
	});

	$('#allow_seperated_package').on('click', function(){
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			dataType: 'json',
			data: 'controller=cart&ajax=true'
				+ '&summary=true'
				+ '&allowSeperatedPackage=true'
				+ '&value='
				+ ($(this).prop('checked') ? '1' : '0')
				+ '&token='+static_token
				+ '&allow_refresh=1',
			success: function(jsonData)
			{
				if (jsonData.hasError)
				{
					var errors = '';
					for(var error in jsonData.errors)
						//IE6 bug fix
						if(error !== 'indexOf')
							errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
					if (!!$.prototype.fancybox)
					    $.fancybox.open([
				        {
				            type: 'inline',
				            autoScale: true,
				            minHeight: 30,
				            content: '<p class="fancybox-error">' + errors + '</p>'
				        }],
						{
					        padding: 0
					    });
					else
					    alert(errors);
					$('input[name=quantity_'+ id +']').val($('input[name=quantity_'+ id +'_hidden]').val());
				}
				else
				{
					if (jsonData.refresh)
						window.location.href = window.location.href;
					updateCartSummary(jsonData.summary);
					if (window.ajaxCart != undefined)
						ajaxCart.updateCart(jsonData);
					updateHookShoppingCart(jsonData.HOOK_SHOPPING_CART);
					updateHookShoppingCartExtra(jsonData.HOOK_SHOPPING_CART_EXTRA);
					if (typeof(getCarrierListAndUpdate) !== 'undefined')
						getCarrierListAndUpdate();
					if (typeof(updatePaymentMethodsDisplay) !== 'undefined')
						updatePaymentMethodsDisplay();
				}
			}
		});
	});

	$('#gift').checkboxChange(function(){
		$('#gift_div').show('slow');
	}, function(){
		$('#gift_div').hide('slow');
	});

	$('#enable-multishipping').checkboxChange(
		function(){
			$('.standard-checkout').hide(0);
			$('.multishipping-checkout').show(0);
		},
		function(){
			$('.standard-checkout').show(0);
			$('.multishipping-checkout').hide(0);
		}
	);
});

function cleanSelectAddressDelivery()
{
	if (window.ajaxCart !== undefined)
	{
		//Removing "Ship to an other address" from the address delivery select option if there is not enought address
		$.each($('.cart_address_delivery'), function(it, item)
		{
			var options = $(item).find('option');
			var address_count = 0;

			var ids = $(item).attr('id').split('_');
			var id_product = ids[3];
			var id_product_attribute = ids[4];
			var id_address_delivery = ids[5];

			$.each(options, function(i) {
				if ($(options[i]).val() > 0
					&& ($('#product_' + id_product + '_' + id_product_attribute + '_0_' + $(options[i]).val()).length == 0 // Check the address is not already used for a similare products
						|| id_address_delivery == $(options[i]).val()
					)
				)
					address_count++;
			});

			// Need at least two address to allow skipping products to multiple address
			if (address_count < 2)
				$($(item).find('option[value=-2]')).remove();
			else if($($(item).find('option[value=-2]')).length == 0)
				$(item).append($('<option value="-2">' + ShipToAnOtherAddress + '</option>'));
		});
	}
}

function changeAddressDelivery(obj)
{
	var ids = obj.attr('id').split('_');
	var id_product = ids[3];
	var id_product_attribute = ids[4];
	var old_id_address_delivery = ids[5];
	var new_id_address_delivery = obj.val();

	if (new_id_address_delivery == old_id_address_delivery)
		return;

	if (new_id_address_delivery > 0) // Change the delivery address
	{
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			dataType: 'json',
			data: 'controller=cart&ajax=true&changeAddressDelivery=1&summary=1&id_product=' + id_product
				+ '&id_product_attribute='+id_product_attribute
				+ '&old_id_address_delivery='+old_id_address_delivery
				+ '&new_id_address_delivery='+new_id_address_delivery
				+ '&token='+static_token
				+ '&allow_refresh=1',
			success: function(jsonData)
			{
				if (typeof(jsonData.hasErrors) != 'undefined' && jsonData.hasErrors)
				{
					if (!!$.prototype.fancybox)
					    $.fancybox.open([
				        {
				            type: 'inline',
				            autoScale: true,
				            minHeight: 30,
				            content: '<p class="fancybox-error">' + jsonData.error + '</p>'
				        }],
						{
					        padding: 0
					    });
					else
					    alert(jsonData.error);

					// Reset the old address
					$('#select_address_delivery_' + id_product + '_' + id_product_attribute + '_' + old_id_address_delivery).val(old_id_address_delivery);
				}
				else
				{
					// The product exist
					if ($('#product_' + id_product + '_' + id_product_attribute + '_0_' + new_id_address_delivery).length)
					{
						updateCartSummary(jsonData.summary);
						if (window.ajaxCart != undefined)
							ajaxCart.updateCart(jsonData);
						updateCustomizedDatas(jsonData.customizedDatas);
						updateHookShoppingCart(jsonData.HOOK_SHOPPING_CART);
						updateHookShoppingCartExtra(jsonData.HOOK_SHOPPING_CART_EXTRA);
						if (typeof(getCarrierListAndUpdate) !== 'undefined')
							getCarrierListAndUpdate();

						// @todo reverse the remove order
						// This effect remove the current line, but it's better to remove the other one, and refresshing this one
						$('#product_' + id_product + '_' + id_product_attribute + '_0_' + old_id_address_delivery).remove();

						// @todo improve customization upgrading
						$('.product_' + id_product + '_' + id_product_attribute + '_0_' + old_id_address_delivery).remove();
					}
					if (window.ajaxCart != undefined)
						ajaxCart.updateCart(jsonData);
					updateAddressId(id_product, id_product_attribute, old_id_address_delivery, new_id_address_delivery);
					cleanSelectAddressDelivery();
				}
			}
		});
	}
	else if (new_id_address_delivery == -1) // Adding a new address
		window.location = $($('.address_add a')[0]).attr('href');
	else if (new_id_address_delivery == -2) // Add a new line for this product
	{
		// This test is will not usefull in the future
		if (old_id_address_delivery == 0)
		{
			if (!!$.prototype.fancybox)
			    $.fancybox.open([
		        {
		            type: 'inline',
		            autoScale: true,
		            minHeight: 30,
		            content: '<p class="fancybox-error">' + txtSelectAnAddressFirst + '</p>'
		        }],
				{
			        padding: 0
			    });
			else
			    alert(txtSelectAnAddressFirst);
			return false;
		}

		// Get new address to deliver
		var id_address_delivery = 0;
		var options = $('#select_address_delivery_' + id_product + '_' + id_product_attribute + '_' + old_id_address_delivery + ' option');
		$.each(options, function(i) {
			// Check the address is not already used for a similare products
			if ($(options[i]).val() > 0 && $(options[i]).val() !== old_id_address_delivery && $('#product_' + id_product + '_' + id_product_attribute + '_0_' + $(options[i]).val()).length == 0)
			{
				id_address_delivery = $(options[i]).val();
				return false;
			}
		});

		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			dataType: 'json',
			context: obj,
			data: 'controller=cart'
				+ '&ajax=true&duplicate=true&summary=true'
				+ '&id_product='+id_product
				+ '&id_product_attribute='+id_product_attribute
				+ '&id_address_delivery='+old_id_address_delivery
				+ '&new_id_address_delivery='+id_address_delivery
				+ '&token='+static_token
				+ '&allow_refresh=1',
			success: function(jsonData)
			{
				if (jsonData.error && !!$.prototype.fancybox)
				    $.fancybox.open([
			        {
			            type: 'inline',
			            autoScale: true,
			            minHeight: 30,
			            content: '<p class="fancybox-error">' + jsonData.error + '</p>'
			        }],
					{
				        padding: 0
				    });
				else
				    alert(jsonData.error);

				var line = $('#product_' + id_product + '_' + id_product_attribute + '_0_' + old_id_address_delivery);
				var new_line = line.clone();
				updateAddressId(id_product, id_product_attribute, old_id_address_delivery, id_address_delivery, new_line);
				line.after(new_line);
				new_line.find('input[name=quantity_' + id_product + '_' + id_product_attribute + '_0_' + old_id_address_delivery + '_hidden]')
					.val(1);
				new_line.find('.cart_quantity_input')
					.val(1);
				$('#select_address_delivery_' + id_product + '_' + id_product_attribute + '_' + old_id_address_delivery).val(old_id_address_delivery);
				$('#select_address_delivery_' + id_product + '_' + id_product_attribute + '_' + id_address_delivery).val(id_address_delivery);


				cleanSelectAddressDelivery();

				updateCartSummary(jsonData.summary);
				if (window.ajaxCart !== undefined)
					ajaxCart.updateCart(jsonData);
			}
		});
	}
	return true;
}

function updateAddressId(id_product, id_product_attribute, old_id_address_delivery, id_address_delivery, line)
{
	if (typeof(line) == 'undefined' || line.length == 0)
		line = $('#cart_summary tr[id^=product_' + id_product + '_' + id_product_attribute + '_0_], #cart_summary tr[id^=product_' + id_product + '_' + id_product_attribute + '_nocustom_]');

	$('.product_customization_for_' + id_product + '_' + id_product_attribute + '_' + old_id_address_delivery).each(function(){
		$(this).attr('id', $(this).attr('id').replace(/_\d+$/, '_' + id_address_delivery)).removeClass('product_customization_for_' + id_product + '_' + id_product_attribute + '_' + old_id_address_delivery + ' address_' + old_id_address_delivery).addClass('product_customization_for_' + id_product + '_' + id_product_attribute + '_' + id_address_delivery + ' address_' + id_address_delivery);
		$(this).find('input[name^=quantity_]').each(function(){
			if (typeof($(this).attr('name')) != 'undefined')
				$(this).attr('name', $(this).attr('name').replace(/_\d+(_hidden|)$/, '_' + id_address_delivery));
		});
		$(this).find('a').each(function(){
			if (typeof($(this).attr('href')) != 'undefined')
				$(this).attr('href', $(this).attr('href').replace(/id_address_delivery=\d+/, 'id_address_delivery=' + id_address_delivery));
		});
	});

	line.attr('id', line.attr('id').replace(/_\d+$/, '_' + id_address_delivery)).removeClass('address_' + old_id_address_delivery).addClass('address_' + id_address_delivery).find('span[id^=cart_quantity_custom_], span[id^=total_product_price_], input[name^=quantity_], .cart_quantity_down, .cart_quantity_up, .cart_quantity_delete').each(function(){

		if (typeof($(this).attr('name')) != 'undefined')
			$(this).attr('name', $(this).attr('name').replace(/_\d+(_hidden|)$/, '_' + id_address_delivery));
		if (typeof($(this).attr('id')) != 'undefined')
			$(this).attr('id', $(this).attr('id').replace(/_\d+$/, '_' + id_address_delivery));
		if (typeof($(this).attr('href')) != 'undefined')
			$(this).attr('href', $(this).attr('href').replace(/id_address_delivery=\d+/, 'id_address_delivery=' + id_address_delivery));
	});

	line.find('#select_address_delivery_' + id_product + '_' + id_product_attribute + '_' + old_id_address_delivery).attr('id', 'select_address_delivery_' + id_product + '_' + id_product_attribute + '_' + id_address_delivery);

	if (window.ajaxCart !== undefined)
	{
		$('.cart_block_list dd, .cart_block_list dt').each(function(){
			if (typeof($(this).attr('id')) != 'undefined')
				$(this).attr('id', $(this).attr('id').replace(/_\d+$/, '_' + id_address_delivery));
		});
	}
}

function updateQty(val, cart, el)
{
	var prefix = "";

	if (typeof(cart) == 'undefined' || cart)
		prefix = '#order-detail-content ';
	else
		prefix = '#fancybox-content ';

	var id = $(el).attr('name');

	var exp = new RegExp("^[0-9]+$");

	if (exp.test(val) == true)
	{
		var hidden = $(prefix + 'input[name=' + id + '_hidden]').val();
		var input = $(prefix + 'input[name=' + id + ']').val();
		var QtyToUp = parseInt(input) - parseInt(hidden);

		if (parseInt(QtyToUp) > 0)
			upQuantity(id.replace('quantity_', ''), QtyToUp);
		else if(parseInt(QtyToUp) < 0)
			downQuantity(id.replace('quantity_', ''), QtyToUp);
	}
	else
		$(prefix + 'input[name=' + id + ']').val($(prefix + 'input[name=' + id + '_hidden]').val());

	if (typeof(getCarrierListAndUpdate) !== 'undefined')
		getCarrierListAndUpdate();
}

function deleteProductFromSummary(id)
{
	var customizationId = 0;
	var productId = 0;
	var productAttributeId = 0;
	var id_address_delivery = 0;
	var ids = 0;
	ids = id.split('_');
	productId = parseInt(ids[0]);
	if (typeof(ids[1]) !== 'undefined')
		productAttributeId = parseInt(ids[1]);
	if (typeof(ids[2]) !== 'undefined' && ids[2] !== 'nocustom')
		customizationId = parseInt(ids[2]);
	if (typeof(ids[3]) !== 'undefined')
		id_address_delivery = parseInt(ids[3]);
	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: baseUri + '?rand=' + new Date().getTime(),
		async: true,
		cache: false,
		dataType: 'json',
		data: 'controller=cart'
			+ '&ajax=true&delete=true&summary=true'
			+ '&id_product='+productId
			+ '&ipa='+productAttributeId
			+ '&id_address_delivery='+id_address_delivery
			+ ((customizationId !== 0) ? '&id_customization=' + customizationId : '')
			+ '&token=' + static_token
			+ '&allow_refresh=1',
		success: function(jsonData)
		{
			if (jsonData.hasError)
			{
				var errors = '';
				for(var error in jsonData.errors)
					//IE6 bug fix
					if(error !== 'indexOf')
						errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
									if (!!$.prototype.fancybox)
											$.fancybox.open([
													{
															type: 'inline',
															autoScale: true,
															minHeight: 30,
															content: '<p class="fancybox-error">' + errors + '</p>'
													}],
													{
															padding: 0
													});
									else
											alert(errors);
			}
			else
			{
				if (jsonData.refresh)
				{
					location.reload();
					return;
				}
				if (parseInt(jsonData.summary.products.length) == 0)
				{
					if (typeof(orderProcess) == 'undefined' || orderProcess !== 'order-opc')
						document.location.href = document.location.href; // redirection
					else
					{
						$('#center_column').children().each(function() {
							if ($(this).attr('id') !== 'emptyCartWarning' && $(this).attr('class') !== 'breadcrumb' && $(this).attr('id') !== 'cart_title')
							{
								$(this).fadeOut('slow', function () {
									$(this).remove();
								});
							}
						});
						$('#summary_products_label').remove();
						$('#emptyCartWarning').fadeIn('slow');
					}
				}
				else
				{
					$('#product_' + id).fadeOut('slow', function() {
						$(this).remove();
						cleanSelectAddressDelivery();
						if (!customizationId)
							refreshOddRow();
					});
					var exist = false;
					for (i=0;i<jsonData.summary.products.length;i++)
					{
						if (jsonData.summary.products[i].id_product == productId
							&& jsonData.summary.products[i].id_product_attribute == productAttributeId
							&& jsonData.summary.products[i].id_address_delivery == id_address_delivery
							&& (parseInt(jsonData.summary.products[i].customizationQuantityTotal) > 0))
								exist = true;
					}
					// if all customization removed => delete product line
					if (!exist && customizationId)
						$('#product_' + productId + '_' + productAttributeId + '_0_' + id_address_delivery).fadeOut('slow', function() {
							$(this).remove();
							var line = $('#product_' + productId + '_' + productAttributeId + '_nocustom_' + id_address_delivery);
							if (line.length > 0)
							{
								line.find('input[name^=quantity_], .cart_quantity_down, .cart_quantity_up, .cart_quantity_delete').each(function(){
									if (typeof($(this).attr('name')) != 'undefined')
										$(this).attr('name', $(this).attr('name').replace(/nocustom/, '0'));
									if (typeof($(this).attr('id')) != 'undefined')
										$(this).attr('id', $(this).attr('id').replace(/nocustom/, '0'));
								});
								line.find('span[id^=total_product_price_]').each(function(){
									$(this).attr('id', $(this).attr('id').replace(/_nocustom/, ''));
								});
								line.attr('id', line.attr('id').replace(/nocustom/, '0'));
							}
							refreshOddRow();
						});
				}
				updateCartSummary(jsonData.summary);
				if (window.ajaxCart != undefined)
					ajaxCart.updateCart(jsonData);
				updateCustomizedDatas(jsonData.customizedDatas);
				updateHookShoppingCart(jsonData.HOOK_SHOPPING_CART);
				updateHookShoppingCartExtra(jsonData.HOOK_SHOPPING_CART_EXTRA);
				if (typeof(getCarrierListAndUpdate) !== 'undefined' && jsonData.summary.products.length > 0)
					getCarrierListAndUpdate();
				if (typeof(updatePaymentMethodsDisplay) !== 'undefined')
					updatePaymentMethodsDisplay();
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			if (textStatus !== 'abort')
			{
				var error = "TECHNICAL ERROR: unable to save update quantity \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus;
				if (!!$.prototype.fancybox)
				    $.fancybox.open([
			        {
			            type: 'inline',
			            autoScale: true,
			            minHeight: 30,
			            content: '<p class="fancybox-error">' + error + '</p>'
			        }],
					{
				        padding: 0
				    });
				else
				    alert(error);
			}
		}
	});
}

function refreshOddRow()
{
	var odd_class = 'odd';
	var even_class = 'even';
	$.each($('.cart_item'), function(i, it)
	{
		if (i == 0) // First item
		{
			if ($(this).hasClass('even'))
			{
				odd_class = 'even';
				even_class = 'odd';
			}
			$(this).addClass('first_item');
		}
		if(i % 2)
			$(this).removeClass(odd_class).addClass(even_class);
		else
			$(this).removeClass(even_class).addClass(odd_class);
	});
	$('.cart_item:last-child, .customization:last-child').addClass('last_item');
}

function upQuantity(id, qty)
{
	if (typeof(qty) == 'undefined' || !qty)
		qty = 1;
	var customizationId = 0;
	var productId = 0;
	var productAttributeId = 0;
	var id_address_delivery = 0;
	var ids = 0;
	ids = id.split('_');
	productId = parseInt(ids[0]);
	if (typeof(ids[1]) !== 'undefined')
		productAttributeId = parseInt(ids[1]);
	if (typeof(ids[2]) !== 'undefined' && ids[2] !== 'nocustom')
		customizationId = parseInt(ids[2]);
	if (typeof(ids[3]) !== 'undefined')
		id_address_delivery = parseInt(ids[3]);

	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: baseUri + '?rand=' + new Date().getTime(),
		async: true,
		cache: false,
		dataType: 'json',
		data: 'controller=cart'
			+ '&ajax=true'
			+ '&add=true'
			+ '&getproductprice=true'
			+ '&summary=true'
			+ '&id_product=' + productId
			+ '&ipa=' + productAttributeId
			+ '&id_address_delivery=' + id_address_delivery
			+ ((customizationId !== 0) ? '&id_customization=' + customizationId : '')
			+ '&qty=' + qty
			+ '&token=' + static_token
			+ '&allow_refresh=1',
		success: function(jsonData)
		{
			if (jsonData.hasError)
			{
				var errors = '';
				for(var error in jsonData.errors)
					//IE6 bug fix
					if(error !== 'indexOf')
						errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
				if (!!$.prototype.fancybox)
				    $.fancybox.open([
			        {
			            type: 'inline',
			            autoScale: true,
			            minHeight: 30,
			            content: '<p class="fancybox-error">' + errors + '</p>'
			        }],
					{
				        padding: 0
				    });
				else
				    alert(errors);
				$('input[name=quantity_'+ id +']').val($('input[name=quantity_'+ id +'_hidden]').val());
			}
			else
			{
				if (jsonData.refresh)
					window.location.href = window.location.href;
				updateCartSummary(jsonData.summary);
				if (window.ajaxCart != undefined)
					ajaxCart.updateCart(jsonData);
				if (customizationId !== 0)
					updateCustomizedDatas(jsonData.customizedDatas);
				updateHookShoppingCart(jsonData.HOOK_SHOPPING_CART);
				updateHookShoppingCartExtra(jsonData.HOOK_SHOPPING_CART_EXTRA);
				if (typeof(getCarrierListAndUpdate) !== 'undefined')
					getCarrierListAndUpdate();
				if (typeof(updatePaymentMethodsDisplay) !== 'undefined')
					updatePaymentMethodsDisplay();
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			if (textStatus !== 'abort')
			{
				error = "TECHNICAL ERROR: unable to save update quantity \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus;
				if (!!$.prototype.fancybox)
				    $.fancybox.open([
			        {
			            type: 'inline',
			            autoScale: true,
			            minHeight: 30,
			            content: '<p class="fancybox-error">' + error + '</p>'
			        }],
					{
				        padding: 0
				    });
				else
				    alert(error);
			}
		}
	});
}

function downQuantity(id, qty)
{
	var val = $('input[name=quantity_' + id + ']').val();
	var newVal = val;
	if(typeof(qty) == 'undefined' || !qty)
	{
		qty = 1;
		newVal = val - 1;
	}
	else if (qty < 0)
		qty = -qty;

	var customizationId = 0;
	var productId = 0;
	var productAttributeId = 0;
	var id_address_delivery = 0;
	var ids = 0;

	ids = id.split('_');
	productId = parseInt(ids[0]);
	if (typeof(ids[1]) !== 'undefined')
		productAttributeId = parseInt(ids[1]);
	if (typeof(ids[2]) !== 'undefined' && ids[2] !== 'nocustom')
		customizationId = parseInt(ids[2]);
	if (typeof(ids[3]) !== 'undefined')
		id_address_delivery = parseInt(ids[3]);

	if (newVal > 0 || $('#product_' + id + '_gift').length)
	{
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			dataType: 'json',
			data: 'controller=cart'
				+ '&ajax=true'
				+ '&add=true'
				+ '&getproductprice=true'
				+ '&summary=true'
				+ '&id_product='+productId
				+ '&ipa='+productAttributeId
				+ '&id_address_delivery='+id_address_delivery
				+ '&op=down'
				+ ((customizationId !== 0) ? '&id_customization='+customizationId : '')
				+ '&qty='+qty
				+ '&token='+static_token
				+ '&allow_refresh=1',
			success: function(jsonData)
			{
				if (jsonData.hasError)
				{
					var errors = '';
					for(var error in jsonData.errors)
						//IE6 bug fix
						if(error !== 'indexOf')
							errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
                    if (!!$.prototype.fancybox)
                        $.fancybox.open([
                            {
                                type: 'inline',
                                autoScale: true,
                                minHeight: 30,
                                content: '<p class="fancybox-error">' + errors + '</p>'
                            }],
                            {
                                padding: 0
                            });
                    else
                        alert(errors);
					$('input[name=quantity_' + id + ']').val($('input[name=quantity_' + id + '_hidden]').val());
				}
				else
				{
					if (jsonData.refresh)
						window.location.href = window.location.href;
					updateCartSummary(jsonData.summary);

					if (window.ajaxCart !== undefined)
						ajaxCart.updateCart(jsonData);
					if (customizationId !== 0)
						updateCustomizedDatas(jsonData.customizedDatas);
					updateHookShoppingCart(jsonData.HOOK_SHOPPING_CART);
					updateHookShoppingCartExtra(jsonData.HOOK_SHOPPING_CART_EXTRA);

					if (newVal == 0)
						$('#product_' + id).hide();

					if (typeof(getCarrierListAndUpdate) !== 'undefined')
						getCarrierListAndUpdate();
					if (typeof(updatePaymentMethodsDisplay) !== 'undefined')
						updatePaymentMethodsDisplay();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				if (textStatus !== 'abort')
					alert("TECHNICAL ERROR: unable to save update quantity \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});

	}
	else
	{
		deleteProductFromSummary(id);
	}
}

function updateCartSummary(json)
{
	var i;
	var nbrProducts = 0;
	var product_list = new Array();

	if (typeof json == 'undefined')
		return;

	$('div.alert-danger').fadeOut();

	for (i=0;i<json.products.length;i++)
		product_list[json.products[i].id_product + '_' + json.products[i].id_product_attribute + '_' + json.products[i].id_address_delivery] = json.products[i];

	if (!$('.multishipping-cart:visible').length)
	{
		for (i=0;i<json.gift_products.length;i++)
			if (typeof(product_list[json.gift_products[i].id_product + '_' + json.gift_products[i].id_product_attribute + '_' + json.gift_products[i].id_address_delivery]) !== 'undefined')
				product_list[json.gift_products[i].id_product + '_' + json.gift_products[i].id_product_attribute + '_' + json.gift_products[i].id_address_delivery].quantity -= json.gift_products[i].cart_quantity;
	}
	else
		for (i=0;i<json.gift_products.length;i++)
			if (typeof(product_list[json.gift_products[i].id_product + '_' + json.gift_products[i].id_product_attribute + '_' + json.gift_products[i].id_address_delivery]) == 'undefined')
				product_list[json.gift_products[i].id_product + '_' + json.gift_products[i].id_product_attribute + '_' + json.gift_products[i].id_address_delivery] = json.gift_products[i];

	for (i in product_list)
	{
		// if reduction, we need to show it in the cart by showing the initial price above the current one
		var reduction = product_list[i].reduction_applies;
		var reduction_type = product_list[i].reduction_type;
		var reduction_symbol = '';
		var initial_price_text = '';
		var initial_price = '';

		if (typeof(product_list[i].price_without_quantity_discount) !== 'undefined')
			initial_price = formatCurrency(product_list[i].price_without_quantity_discount, currencyFormat, currencySign, currencyBlank);

		var current_price = '';
		var product_total = '';
		var product_customization_total = '';

		if (priceDisplayMethod !== 0)
		{
			current_price = formatCurrency(product_list[i].price, currencyFormat, currencySign, currencyBlank);
			product_total = product_list[i].total;
			product_customization_total = product_list[i].total_customization;
		}
		else
		{
			current_price = formatCurrency(product_list[i].price_wt, currencyFormat, currencySign, currencyBlank);
			product_total = product_list[i].total_wt;
			product_customization_total = product_list[i].total_customization_wt;
		}

		var current_price_class ='price';
		var price_reduction = '';
		if (reduction && typeof(initial_price) !== 'undefined')
		{
			if (reduction_type == 'amount')
				price_reduction = product_list[i].reduction_formatted;
			else
			{
				var display_price = 0;
				if (priceDisplayMethod !== 0)
					display_price = product_list[i].price;
				else
					display_price = product_list[i].price_wt;

				price_reduction = ps_round((product_list[i].price_without_quantity_discount - display_price)/product_list[i].price_without_quantity_discount * -100);
				reduction_symbol = '%';
			}

			if (initial_price !== '' && product_list[i].price_without_quantity_discount > product_list[i].price)
			{
				initial_price_text = '<li class="price-percent-reduction small">&nbsp;'+price_reduction+reduction_symbol+'&nbsp;</li><li class="old-price">' + initial_price + '</li>';
				current_price_class += ' special-price';
			}
		}

		var key_for_blockcart = product_list[i].id_product + '_' + product_list[i].id_product_attribute + '_' + product_list[i].id_address_delivery;
		var key_for_blockcart_nocustom = product_list[i].id_product + '_' + product_list[i].id_product_attribute + '_' + ((product_list[i].id_customization && product_list[i].quantity_without_customization != product_list[i].quantity)? 'nocustom' : '0') + '_' + product_list[i].id_address_delivery;

		$('#product_price_' + key_for_blockcart).html('<li class="' + current_price_class + '">' + current_price + '</li>' + initial_price_text);
		if (typeof(product_list[i].customizationQuantityTotal) !== 'undefined' && product_list[i].customizationQuantityTotal > 0)
			$('#total_product_price_' + key_for_blockcart).html(formatCurrency(product_customization_total, currencyFormat, currencySign, currencyBlank));
		else
			$('#total_product_price_' + key_for_blockcart).html(formatCurrency(product_total, currencyFormat, currencySign, currencyBlank));
		if (product_list[i].quantity_without_customization != product_list[i].quantity)
			$('#total_product_price_' + key_for_blockcart_nocustom).html(formatCurrency(product_total, currencyFormat, currencySign, currencyBlank));

		$('input[name=quantity_' + key_for_blockcart_nocustom + ']').val(product_list[i].id_customization? product_list[i].quantity_without_customization : product_list[i].cart_quantity);
		$('input[name=quantity_' + key_for_blockcart_nocustom + '_hidden]').val(product_list[i].id_customization? product_list[i].quantity_without_customization : product_list[i].cart_quantity);
		if (typeof(product_list[i].customizationQuantityTotal) !== 'undefined' && product_list[i].customizationQuantityTotal > 0)
			$('#cart_quantity_custom_' + key_for_blockcart).html(product_list[i].customizationQuantityTotal);
		nbrProducts += parseInt(product_list[i].quantity);
	}

	// Update discounts
	var discount_count = 0;
	for(var e in json.discounts)
	{
		discount_count++;
		break;
	}

	if (!discount_count)
	{
		$('.cart_discount').each(function(){$(this).remove();});
		$('.cart_total_voucher').remove();
	}
	else
	{
		if ($('.cart_discount').length == 0)
		{
			location.reload();
			return;
		}

		if (priceDisplayMethod !== 0)
			$('#total_discount').html('-' + formatCurrency(json.total_discounts_tax_exc, currencyFormat, currencySign, currencyBlank));
		else
			$('#total_discount').html('-' + formatCurrency(json.total_discounts, currencyFormat, currencySign, currencyBlank));

		$('.cart_discount').each(function(){
			var idElmt = $(this).attr('id').replace('cart_discount_','');
			var toDelete = true;

			for (var i in json.discounts)
				if (json.discounts[i].id_discount == idElmt)
				{
					if (json.discounts[i].value_real !== '!')
					{
						if (priceDisplayMethod !== 0)
							$('#cart_discount_' + idElmt + ' td.cart_discount_price span.price-discount').html(formatCurrency(json.discounts[i].value_tax_exc * -1, currencyFormat, currencySign, currencyBlank));
						else
							$('#cart_discount_' + idElmt + ' td.cart_discount_price span.price-discount').html(formatCurrency(json.discounts[i].value_real * -1, currencyFormat, currencySign, currencyBlank));
					}
					toDelete = false;
				}
			if (toDelete)
				$('#cart_discount_' + idElmt + ', #cart_total_voucher').fadeTo('fast', 0, function(){ $(this).remove(); });
		});
	}

	// Block cart
	if (typeof(orderProcess) !== 'undefined' && orderProcess == 'order-opc' && !json.is_virtual_cart)
		$('.ajax_cart_shipping_cost').parent().find('.unvisible').show();

	if (json.total_shipping > 0)
	{
		if (priceDisplayMethod !== 0)
		{
			$('.cart_block_shipping_cost').html(formatCurrency(json.total_shipping_tax_exc, currencyFormat, currencySign, currencyBlank));
			$('.cart_block_wrapping_cost').html(formatCurrency(json.total_wrapping_tax_exc, currencyFormat, currencySign, currencyBlank));
			$('.cart_block_total').html(formatCurrency(json.total_price_without_tax, currencyFormat, currencySign, currencyBlank));
		}
		else
		{
			$('.cart_block_shipping_cost').html(formatCurrency(json.total_shipping, currencyFormat, currencySign, currencyBlank));
			$('.cart_block_wrapping_cost').html(formatCurrency(json.total_wrapping, currencyFormat, currencySign, currencyBlank));
			$('.cart_block_total').html(formatCurrency(json.total_price, currencyFormat, currencySign, currencyBlank));
		}
	}
	else
	{
		if (parseFloat(json.total_shipping) > 0)
			$('.ajax_cart_shipping_cost').text(jsonData.shippingCost);
		else if (json.carrier.id == null && typeof(toBeDetermined) !== 'undefined' && !json.free_ship)
			$('.ajax_cart_shipping_cost').html(toBeDetermined);
		else if (typeof(freeShippingTranslation) != 'undefined')
			$('.ajax_cart_shipping_cost').html(freeShippingTranslation);
	}

	$('.cart_block_tax_cost').html(formatCurrency(json.total_tax, currencyFormat, currencySign, currencyBlank));
	$('.ajax_cart_quantity').html(nbrProducts);

	// Cart summary
	$('#summary_products_quantity').html(nbrProducts + ' ' + (nbrProducts > 1 ? txtProducts : txtProduct));
	if (priceDisplayMethod !== 0)
		$('#total_product').html(formatCurrency(json.total_products, currencyFormat, currencySign, currencyBlank));
	else
		$('#total_product').html(formatCurrency(json.total_products_wt, currencyFormat, currencySign, currencyBlank));
	$('#total_price').html(formatCurrency(json.total_price, currencyFormat, currencySign, currencyBlank));
	$('#total_price_without_tax').html(formatCurrency(json.total_price_without_tax, currencyFormat, currencySign, currencyBlank));
	$('#total_tax').html(formatCurrency(json.total_tax, currencyFormat, currencySign, currencyBlank));

	$('.cart_total_delivery').show();
	if (json.total_shipping > 0)
	{
		if (priceDisplayMethod !== 0)
			$('#total_shipping').html(formatCurrency(json.total_shipping_tax_exc, currencyFormat, currencySign, currencyBlank));
		else
			$('#total_shipping').html(formatCurrency(json.total_shipping, currencyFormat, currencySign, currencyBlank));
	}
	else
	{
		if (json.carrier.id != null || json.free_ship)
		{
			$('#total_shipping').html(freeShippingTranslation);
			if (json.is_virtual_cart)
				$('.cart_total_delivery').hide();
		}
		if (!hasDeliveryAddress)
			$('.cart_total_delivery').hide();
	}

	if (json.total_wrapping > 0)
	{
		$('#total_wrapping').html(formatCurrency(json.total_wrapping, currencyFormat, currencySign, currencyBlank));
		$('#total_wrapping').parent().show();
	}
	else
	{
		$('#total_wrapping').html(formatCurrency(json.total_wrapping, currencyFormat, currencySign, currencyBlank));
		$('#total_wrapping').parent().hide();
	}
}

function updateCustomizedDatas(json)
{
	for(var i in json)
		for(var j in json[i])
			for(var k in json[i][j])
				for(var l in json[i][j][k])
				{
					var quantity = json[i][j][k][l]['quantity'];
					$('input[name=quantity_' + i + '_' + j + '_' + l + '_' + k + '_hidden]').val(quantity);
					$('input[name=quantity_' + i + '_' + j + '_' + l + '_' + k + ']').val(quantity);
				}
}

function updateHookShoppingCart(html)
{
	$('#HOOK_SHOPPING_CART').html(html);
	if (typeof initCrossSellingbxSlider !== 'undefined')
		initCrossSellingbxSlider();
}

function updateHookShoppingCartExtra(html)
{
	$('#HOOK_SHOPPING_CART_EXTRA').html(html);
}
function refreshDeliveryOptions()
{
	$.each($('.delivery_option_radio'), function() {
		if ($(this).prop('checked'))
		{
			if ($(this).parent().find('.delivery_option_carrier.not-displayable').length == 0)
				$(this).parent().find('.delivery_option_carrier').show();
			var carrier_id_list = $(this).val().split(',');
			carrier_id_list.pop();
			var it = this;
			$(carrier_id_list).each(function() {
				$(it).closest('.delivery_options').find('input[value="' + this.toString() + '"]').change();
			});
		}
		else
			$(this).parent().find('.delivery_option_carrier').hide();
	});
}

function updateExtraCarrier(id_delivery_option, id_address)
{
	var url = "";

	if(typeof(orderOpcUrl) !== 'undefined')
		url = orderOpcUrl;
	else
		url = orderUrl;

	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: url + '?rand=' + new Date().getTime(),
		async: true,
		cache: false,
		dataType : "json",
		data: 'ajax=true'
			+ '&method=updateExtraCarrier'
			+ '&id_address='+id_address
			+ '&id_delivery_option='+id_delivery_option
			+ '&token='+static_token
			+ '&allow_refresh=1',
		success: function(jsonData)
		{
			$('#HOOK_EXTRACARRIER_' + id_address).html(jsonData['content']);
		}
	});
}
