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
$(document).ready(function()
{
	$('.opc-collapse').on('show.bs.collapse', function () {
		$(this).closest('.card').find('.accordion-left-arrow').addClass('hidden');
		$(this).closest('.card').find('.step-edit').removeClass('hidden');
	});
	$('.opc-collapse').on('hide.bs.collapse', function () {
		$(this).closest('.card').find('.accordion-left-arrow').removeClass('hidden');
		$(this).closest('.card').find('.step-edit').addClass('hidden');
	});

	// BY WEBKUL
	// FOR ADVANCED PAYMENT
	var payment_type = $(".payment_type:checked").val();
	if (payment_type == 1)
		$("#partial_data").hide();
	else if (payment_type == 2)
		$("#partial_data").show();

	$(".payment_type").on('change',function()
	{
		var payment_type = $(".payment_type:checked").val();
		if (payment_type == 1)
			$("#partial_data").slideUp();
		else if (payment_type == 2)
			$("#partial_data").slideDown();
	});

	// GUEST CHECKOUT / NEW ACCOUNT MANAGEMENT
	if ((typeof isLogged == 'undefined' || !isLogged) || (typeof isGuest !== 'undefined' && isGuest))
	{
		if (guestCheckoutEnabled && !isLogged && !isGuest)
		{
			$('#opc_account_choice').show();
			$('#opc_account_form, #opc_invoice_address').hide();

			$(document).on('click', '#opc_createAccount',function(e){
				e.preventDefault();
				$('.is_customer_param').show();
				$('#opc_account_form').slideDown('slow');
				$('#is_new_customer').val('1');
				if (typeof bindUniform !=='undefined')
					bindUniform();
			});
			$(document).on('click', '#opc_guestCheckout', function(e){
				e.preventDefault();
				$('.is_customer_param').hide();
				$('#opc_account_form').slideDown('slow');
				$('#is_new_customer').val('0');
				$('#new_account_title').html(txtInstantCheckout);
				$('#submitAccount').attr({id : 'submitGuestAccount', name : 'submitGuestAccount'});
				if (typeof bindUniform !=='undefined')
					bindUniform();
			});
		}
		else if (isGuest)
		{
			$('.is_customer_param').hide();
			$('#opc_account_form').show('slow');
			$('#is_new_customer').val('0');
			$('#opc_account_choice, #opc_invoice_address').hide();
			$('#new_account_title').html(txtInstantCheckout);
		}
		else
		{
			$('#opc_account_choice').hide();
			$('#is_new_customer').val('1');
			$('.is_customer_param, #opc_account_form').show();
			$('#opc_invoice_address').hide();
		}

		// LOGIN FORM
		$(document).on('click', '#openLoginFormBlock', function(e){
			e.preventDefault();
			$('#openNewAccountBlock').show(200);
			$('.already_registered_block').hide(200);
			$('#login_form_content').slideDown(200);
			$('#new_account_form').slideUp(200);
		});

		$(document).on('click', '#idAccountChoice', function(e) {
			e.preventDefault();
			$('#login_form_content').slideUp(200);
			$('#new_account_form').slideDown(200);
			$('.already_registered_block').show(200);
		});

		// LOGIN FORM SENDING
		$(document).on('click', '#SubmitLogin', function(e)
		{
			e.preventDefault();
            var that = $(this);
			$.ajax({
				type: 'POST',
				headers: { "cache-control": "no-cache" },
				url: authenticationUrl + '?rand=' + new Date().getTime(),
				async: false,
				cache: false,
				dataType : "json",
				data: 'SubmitLogin=true&ajax=true&email='+encodeURIComponent($('#login_email').val())+'&passwd='+encodeURIComponent($('#login_passwd').val())+'&token=' + static_token ,
				success: function(jsonData)
				{
					if (jsonData.hasError)
					{
						var errors = '<b>'+txtThereis+' '+jsonData.errors.length+' '+txtErrors+':</b><ol>';
						for(var error in jsonData.errors)
							//IE6 bug fix
							if(error !== 'indexOf')
								errors += '<li>'+jsonData.errors[error]+'</li>';
						errors += '</ol>';
						$('#opc_login_errors').html(errors).slideDown('slow');
					}
					else
					{
						// update token
						static_token = jsonData.token;
						updateNewAccountToAddressBlock(that.attr('data-adv-api'));
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					if (textStatus !== 'abort')
					{
						error = "TECHNICAL ERROR: unable to send login informations \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus;
			            if (!!$.prototype.fancybox)
			                $.fancybox.open([
			                    {
			                        type: 'inline',
			                        autoScale: true,
			                        minHeight: 30,
			                        content: '<p class="fancybox-error">' + error + '</p>'
			                    }
			                ], {
			                    padding: 0
			                });
			            else
			                alert(error);
					}
				}
			});
		});

		// VALIDATION / CREATION AJAX
		$(document).on('click', '#submitAccount, #submitGuestAccount', function(e){
			e.preventDefault();
			$('#opc_new_account-overlay, #opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeIn('slow')

			var callingFile = '';
            var advApiParam = '';
            var params = '';
			var isTransforming = false;

            if ($(this).attr('data-adv-api')) {
                advApiParam = '&isAdvApi=1';
            }

			if (parseInt($('#opc_id_customer').val()) == 0)
			{
				callingFile = authenticationUrl;
				params = 'submitAccount=true&';
			}
			else
			{
				if ($('#opc_account_form #passwd').val().length > 0) {
					params = 'method=transformGuestAccount&';
					isTransforming = true;
				} else {
					params = 'method=editCustomer&';
				}
			}

			$('#opc_account_form input:visible, #opc_account_form input[type=hidden]').each(function() {
				if ($(this).is('input[type=checkbox]'))
				{
					if ($(this).is(':checked'))
						params += encodeURIComponent($(this).attr('name'))+'=1&';
				}
				else if ($(this).is('input[type=radio]'))
				{
					if ($(this).is(':checked'))
						params += encodeURIComponent($(this).attr('name'))+'='+encodeURIComponent($(this).val())+'&';
				}
				else
					params += encodeURIComponent($(this).attr('name'))+'='+encodeURIComponent($(this).val())+'&';
			});

			$('#opc_account_form select:visible').each(function() {
				params += encodeURIComponent($(this).attr('name'))+'='+encodeURIComponent($(this).val())+'&';
			});
			params += 'alias='+encodeURIComponent($('#alias').val())+'&';
			params += 'other='+encodeURIComponent($('#other').val())+'&';
			params += 'is_new_customer='+encodeURIComponent($('#is_new_customer').val())+'&';
			// Clean the last &
			params = params.substr(0, params.length-1);

			$.ajax({
				type: 'POST',
				headers: { "cache-control": "no-cache" },
				url: callingFile + '?rand=' + new Date().getTime() + advApiParam,
				async: false,
				cache: false,
				dataType : "json",
				data: 'ajax=true&'+params+'&token=' + static_token,
				success: function(jsonData)
				{
					if (jsonData.hasError)
					{
						var tmp = '';
						var i = 0;
						for(var error in jsonData.errors)
							//IE6 bug fix
							if(error !== 'indexOf')
							{
								i = i+1;
								tmp += '<li>'+jsonData.errors[error]+'</li>';
							}
						tmp += '</ol>';
						var errors = '<b>'+txtThereis+' '+i+' '+txtErrors+':</b><ol>'+tmp;
						$('#opc_account_errors').slideUp('fast', function(){
							$(this).html(errors).slideDown('slow', function(){
								$.scrollTo('#opc_account_errors', 800);
							});
						});
					}
					else
					{
						$('#opc_account_errors').slideUp('slow', function(){
							$(this).html('');
						});

						if (isTransforming) {
							location.reload();
						}
					}

					isGuest = parseInt($('#is_new_customer').val()) == 1 ? 0 : 1;
					// update addresses id
					if(jsonData.id_address_delivery !== undefined && jsonData.id_address_delivery > 0)
						$('#opc_id_address_delivery').val(jsonData.id_address_delivery);
					if(jsonData.id_address_invoice !== undefined && jsonData.id_address_invoice > 0)
						$('#opc_id_address_invoice').val(jsonData.id_address_invoice);

					if (jsonData.id_customer !== undefined && jsonData.id_customer !== 0 && jsonData.isSaved)
					{
						// update token
						static_token = jsonData.token;

						// It's not a new customer
						if (PS_CUSTOMER_ADDRESS_CREATION && $('#opc_id_customer').val() !== '0')
							if (!saveAddress('delivery'))
								return false;

						// update id_customer
						$('#opc_id_customer').val(jsonData.id_customer);

						// if ($('#invoice_address:checked').length !== 0)
						// {
						// 	if (!saveAddress('invoice'))
						// 		return false;
						// }

						// update id_customer
						$('#opc_id_customer').val(jsonData.id_customer);

						// force to refresh carrier list
						if (isGuest)
						{
							isLogged = 1;
							$('#opc_account_saved').fadeIn('slow');
							$('#submitAccount').hide();
							updateAddressSelection(advApiParam);
						}
						else
							updateNewAccountToAddressBlock(advApiParam);
					}
					//$('#guest-info-block, #opc_new_account-overlay, #opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeIn('slow');
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					if (textStatus !== 'abort')
					{
						error = "TECHNICAL ERROR: unable to save account \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus;
			            if (!!$.prototype.fancybox)
			                $.fancybox.open([
			                    {
			                        type: 'inline',
			                        autoScale: true,
			                        minHeight: 30,
			                        content: '<p class="fancybox-error">' + error + '</p>'
			                    }
			                ], {
			                    padding: 0
			                });
			            else
			                alert(error);
					}
					$('#opc_new_account-overlay, #opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeIn('slow')
				}
			});
		});
	}

	bindInputs();

	$('#opc_account_form input,select,textarea').change(function() {
		if ($(this).is(':visible'))
		{
			$('#opc_account_saved').fadeOut('slow');
			$('#submitAccount').show();
		}
	});

	// If the multishipping mode is off assure us the checkbox "I want to specify a delivery address for each products I order." is unchecked.
	$('#multishipping_mode_checkbox').attr('checked', false);
	// If the multishipping mode is on, check the box "I want to specify a delivery address for each products I order.".
	if (typeof(multishipping_mode) !== 'undefined' && multishipping_mode)
	{
		$('#multishipping_mode_checkbox').click();
		$('.addressesAreEquals').hide().find('input').attr('checked', false);
	}
	if (typeof(open_multishipping_fancybox) !== 'undefined' && open_multishipping_fancybox)
		$('#link_multishipping_form').click();

	// fancybox for extra bed requirement edit on checkout page
	$('body').on('click', '.open_rooms_extra_demands', function() {
		var idProduct = $(this).attr('id_product');
		var dateFrom = $(this).attr('date_from');
		var dateTo = $(this).attr('date_to');
		$.fancybox({
			href: "#rooms_extra_demands",
		    autoSize : true,
		    autoScale : true,
			maxWidth : '100%',
			'hideOnContentClick': false,
			beforeLoad: function () {
				$.ajax({
					type: 'POST',
					headers: {
						"cache-control": "no-cache"
					},
					url: orderOpcUrl,
					dataType: 'html',
					cache: false,
					data: {
						date_from: dateFrom,
						date_to: dateTo,
						id_product: idProduct,
						method: 'getRoomTypeBookingDemands',
						ajax: true
					},
					success: function(result) {
						$('#rooms_type_extra_demands').find('#room_type_demands_desc').html('');
						$('#rooms_type_extra_demands').find('#room_type_demands_desc').append(result);
					},
				});
			},
			afterClose: function() {
				// reload so that changes prices will reflect everywhere
				location.reload();
			},
		});
	});

	function close_accordion_section() {
        $('.accordion .accordion-section-title').removeClass('active');
        $('.accordion .accordion-section-content').slideUp(300).removeClass('open');
    }

    $(document).on('click', '.accordion-section-title', function(e) {
        // Grab current anchor value
        var currentAttrValue = $(this).attr('href');

        if ($(e.target).is('.active')) {
            close_accordion_section();
            $(this).find('i').removeClass('icon-angle-down');
            $(this).find('i').addClass('icon-angle-left');
        } else {
            close_accordion_section();
            // Add active class to section title
            $(this).addClass('active');
            $(this).find('i').removeClass('icon-angle-left');
            $(this).find('i').addClass('icon-angle-down');
            // Open up the hidden content panel
            $('.accordion ' + currentAttrValue).slideDown(300).addClass('open');
        }
        e.preventDefault();
	});

	$(document).on('click', '.id_room_type_demand', function() {
		var roomDemands = [];
		// get the selected extra demands by customer
		$(this).closest('.room_demand_detail').find('input:checkbox.id_room_type_demand:checked').each(function () {
			roomDemands.push({
				'id_global_demand':$(this).val(),
				'id_option': $(this).closest('.room_demand_block').find('.id_option').val()
			});
		});
		var idBookingCart = $(this).attr('id_cart_booking');
		$.ajax({
			type: 'POST',
			headers: {
				"cache-control": "no-cache"
			},
			url: orderOpcUrl,
			dataType: 'JSON',
			cache: false,
			data: {
				id_cart_booking: idBookingCart,
				room_demands: JSON.stringify(roomDemands),
				method: 'changeRoomDemands',
				ajax: true
			},
			success: function(result) {
				if (result == 1) {
					showSuccessMessage(txtExtraDemandSucc);
				} else {
					showErrorMessage(txtExtraDemandErr);
				}
			}
		});
	});

	$(document).on('change', '.demand_adv_option_block .id_option', function(e) {
        var option_selected = $(this).find('option:selected');
		var extra_demand_price = option_selected.attr("optionPrice")
        extra_demand_price = parseFloat(extra_demand_price);
        extra_demand_price = formatCurrency(extra_demand_price, currency_format, currency_sign, currency_blank);
		$(this).closest('.room_demand_block').find('.extra_demand_option_price').text(extra_demand_price);
		var roomDemands = [];
		// get the selected extra demands by customer
		$(this).closest('.room_demand_detail').find('input:checkbox.id_room_type_demand:checked').each(function () {
			roomDemands.push({
				'id_global_demand':$(this).val(),
				'id_option': $(this).closest('.room_demand_block').find('.id_option').val()
			});
		});
		var idBookingCart = $(this).closest('.room_demand_block').find('.id_room_type_demand').attr('id_cart_booking');
		$.ajax({
			type: 'POST',
			headers: {
				"cache-control": "no-cache"
			},
			url: orderOpcUrl,
			dataType: 'JSON',
			cache: false,
			data: {
				id_cart_booking: idBookingCart,
				room_demands: JSON.stringify(roomDemands),
				method: 'changeRoomDemands',
				ajax: true
			},
			success: function(result) {
				if (result == 1) {
					showSuccessMessage(txtExtraDemandSucc);
				} else {
					showErrorMessage(txtExtraDemandErr);
				}
			}
		});
    });
});

function updateCarrierList(json)
{
	var html = json.carrier_block;
	$('#carrier_area').replaceWith(html);
	bindInputs();
	/* update hooks for carrier module */
	$('#HOOK_BEFORECARRIER').html(json.HOOK_BEFORECARRIER);
}

function updatePaymentMethods(json)
{
	$('#HOOK_TOP_PAYMENT').html(json.HOOK_TOP_PAYMENT);
	$('#opc_payment_methods-content #HOOK_PAYMENT').html(json.HOOK_PAYMENT);
}

function updatePaymentMethodsDisplay()
{
	var checked = '';
	if ($('#cgv:checked').length !== 0)
		checked = 1;
	else
		checked = 0;
	$('#opc_payment_methods-overlay').fadeIn('slow', function(){
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: orderOpcUrl + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			dataType : "json",
			data: 'ajax=true&method=updateTOSStatusAndGetPayments&checked=' + checked + '&token=' + static_token,
			success: function(json)
			{
				updatePaymentMethods(json);
				if (typeof bindUniform !=='undefined')
					bindUniform();
			}
		});
		$(this).fadeOut('slow');
	});
}

function updateAddressSelection(is_adv_api)
{
	var idAddress_delivery = ($('#opc_id_address_delivery').length == 1 ? $('#opc_id_address_delivery').val() : $('#id_address_delivery').val());
	var idAddress_invoice = ($('#opc_id_address_invoice').length == 1 ? $('#opc_id_address_invoice').val() : ($('#addressesAreEquals:checked').length == 1 ? idAddress_delivery : ($('#id_address_invoice').length == 1 ? $('#id_address_invoice').val() : idAddress_delivery)));

	$('#opc_account-overlay').fadeIn('slow');
	$('#opc_delivery_methods-overlay').fadeIn('slow');
	$('#opc_payment_methods-overlay').fadeIn('slow');

	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: orderOpcUrl + '?rand=' + new Date().getTime(),
		async: true,
		cache: false,
		dataType : "json",
		data: 'allow_refresh=1&ajax=true&method=updateAddressesSelected&id_address_delivery=' + idAddress_delivery +
              '&id_address_invoice=' + idAddress_invoice + '&token=' + static_token +
              (is_adv_api ? '&isAdvApi=1' : ''),
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
	                    }
	                ], {
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
				// Update all product keys with the new address id
				$('#cart_summary .address_' + deliveryAddress).each(function() {
					$(this)
						.removeClass('address_' + deliveryAddress)
						.addClass('address_' + idAddress_delivery);
					$(this).attr('id', $(this).attr('id').replace(/_\d+$/, '_' + idAddress_delivery));
					if ($(this).find('.cart_unit ul').length > 0 && $(this).find('.cart_unit ul').attr('id').length > 0)
						$(this).find('.cart_unit ul').attr('id', $(this).find('.cart_unit ul').attr('id').replace(/_\d+$/, '_' + idAddress_delivery));

					if ($(this).find('.cart_total span').length > 0 && $(this).find('.cart_total span').attr('id').length > 0)
						$(this).find('.cart_total span').attr('id', $(this).find('.cart_total span').attr('id').replace(/_\d+$/, '_' + idAddress_delivery));

					if ($(this).find('.cart_quantity_input').length > 0 && $(this).find('.cart_quantity_input').attr('name').length > 0)
					{
						var name = $(this).find('.cart_quantity_input').attr('name')+'_hidden';

						$(this).find('.cart_quantity_input').attr('name', $(this).find('.cart_quantity_input').attr('name').replace(/_\d+$/, '_' + idAddress_delivery));
						if ($(this).find('[name="' + name + '"]').length > 0)
							$(this).find('[name="' + name + '"]').attr('name', name.replace(/_\d+_hidden$/, '_' + idAddress_delivery + '_hidden'));
					}

					if ($(this).find('.cart_quantity_delete').length > 0 && $(this).find('.cart_quantity_delete').attr('id').length > 0)
					{
						$(this).find('.cart_quantity_delete')
							.attr('id', $(this).find('.cart_quantity_delete').attr('id').replace(/_\d+$/, '_' + idAddress_delivery))
							.attr('href', $(this).find('.cart_quantity_delete').attr('href').replace(/id_address_delivery=\d+&/, 'id_address_delivery=' + idAddress_delivery+'&'));
					}

					if ($(this).find('.cart_quantity_down').length > 0 && $(this).find('.cart_quantity_down').attr('id').length > 0)
					{
						$(this).find('.cart_quantity_down')
							.attr('id', $(this).find('.cart_quantity_down').attr('id').replace(/_\d+$/, '_' + idAddress_delivery))
							.attr('href', $(this).find('.cart_quantity_down').attr('href').replace(/id_address_delivery=\d+&/, 'id_address_delivery=' + idAddress_delivery+'&'));
					}

					if ($(this).find('.cart_quantity_up').length > 0 && $(this).find('.cart_quantity_up').attr('id').length > 0)
					{
						$(this).find('.cart_quantity_up')
							.attr('id', $(this).find('.cart_quantity_up').attr('id').replace(/_\d+$/, '_' + idAddress_delivery))
							.attr('href', $(this).find('.cart_quantity_up').attr('href').replace(/id_address_delivery=\d+&/, 'id_address_delivery=' + idAddress_delivery+'&'));
					}
				});

				// Update global var deliveryAddress
				deliveryAddress = idAddress_delivery;
				if (window.ajaxCart !== undefined)
				{
					$('.cart_block_list dd, .cart_block_list dt').each(function(){
						if (typeof($(this).attr('id')) != 'undefined')
							$(this).attr('id', $(this).attr('id').replace(/_\d+$/, '_' + idAddress_delivery));
					});
				}
				updateCarrierList(jsonData.carrier_data);
				updatePaymentMethods(jsonData);
				updateCartSummary(jsonData.summary);
				updateHookShoppingCart(jsonData.HOOK_SHOPPING_CART);
				updateHookShoppingCartExtra(jsonData.HOOK_SHOPPING_CART_EXTRA);
				if ($('#gift-price').length == 1)
					$('#gift-price').html(jsonData.gift_price);
				$('#guest-info-block, #opc_account-overlay, #opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');

				location.reload();
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			if (textStatus !== 'abort')
			{
				error = "TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus;
	            if (!!$.prototype.fancybox)
	                $.fancybox.open([
	                    {
	                        type: 'inline',
	                        autoScale: true,
	                        minHeight: 30,
	                        content: '<p class="fancybox-error">' + error + '</p>'
	                    }
	                ], {
	                    padding: 0
	                });
	            else
	                alert(error);
			}
			$('#guest-info-block, #opc_account-overlay, #opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');
		}
	});
}

function getCarrierListAndUpdate()
{
	$('#opc_delivery_methods-overlay').fadeIn('slow');
	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: orderOpcUrl + '?rand=' + new Date().getTime(),
		async: true,
		cache: false,
		dataType : "json",
		data: 'ajax=true&method=getCarrierList&token=' + static_token,
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
	            {
	                $.fancybox.open([
	                    {
	                        type: 'inline',
	                        autoScale: true,
	                        minHeight: 30,
	                        content: '<p class="fancybox-error">' + errors + '</p>'
	                    }
	                ], {
	                    padding: 0
	                });
	            }
	            else
				{
		            if (!!$.prototype.fancybox)
		                $.fancybox.open([
		                    {
		                        type: 'inline',
		                        autoScale: true,
		                        minHeight: 30,
		                        content: '<p class="fancybox-error">' + errors + '</p>'
		                    }
		                ], {
		                    padding: 0
		                });
		            else
		                alert(errors);
				}
			}
			else
				updateCarrierList(jsonData);
			$('#opc_delivery_methods-overlay').fadeOut('slow');
		}
	});
}

function updateCarrierSelectionAndGift()
{
	var recyclablePackage = 0;
	var gift = 0;
	var giftMessage = '';

	var delivery_option_radio = $('.delivery_option_radio');
	var delivery_option_params = '&';
	$.each(delivery_option_radio, function(i) {
		if ($(this).prop('checked'))
			delivery_option_params += $(delivery_option_radio[i]).attr('name') + '=' + $(delivery_option_radio[i]).val() + '&';
	});
	if (delivery_option_params == '&')
		delivery_option_params = '&delivery_option=&';

	if ($('input#recyclable:checked').length)
		recyclablePackage = 1;
	if ($('input#gift:checked').length)
	{
		gift = 1;
		giftMessage = encodeURIComponent($('#gift_message').val());
	}

	$('#opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');
	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: orderOpcUrl + '?rand=' + new Date().getTime(),
		async: true,
		cache: false,
		dataType : "json",
		data: 'ajax=true&method=updateCarrierAndGetPayments' + delivery_option_params + 'recyclable=' + recyclablePackage + '&gift=' + gift + '&gift_message=' + giftMessage + '&token=' + static_token ,
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
	                    }
	                ], {
	                    padding: 0
	                });
	            else
	                alert(errors);
			}
			else
			{
				updateCartSummary(jsonData.summary);
				updatePaymentMethods(jsonData);
				updateHookShoppingCart(jsonData.summary.HOOK_SHOPPING_CART);
				updateHookShoppingCartExtra(jsonData.summary.HOOK_SHOPPING_CART_EXTRA);
				updateCarrierList(jsonData.carrier_data);
				$('#opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');
				refreshDeliveryOptions();
				if (typeof bindUniform !=='undefined')
					bindUniform();
			}
			$('#opc_new_account').hide();
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			if (textStatus !== 'abort')
				alert("TECHNICAL ERROR: unable to save carrier \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			$('#opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');
		}
	});
}

function confirmFreeOrder()
{
	if ($('#opc_new_account-overlay').length !== 0)
		$('#opc_new_account-overlay').fadeIn('slow');
	else
		$('#opc_account-overlay').fadeIn('slow');
	$('#opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');
	$('#confirmOrder').prop('disabled', 'disabled');
	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: orderOpcUrl + '?rand=' + new Date().getTime(),
		async: true,
		cache: false,
		dataType : "html",
		data: 'ajax=true&method=makeFreeOrder&token=' + static_token ,
		success: function(html)
		{
			$('#confirmOrder').prop('disabled', false);
			var array_split = html.split(':');
			if (array_split[0] == 'freeorder')
			{
				if (isGuest)
					document.location.href = guestTrackingUrl+'?id_order='+encodeURIComponent(array_split[1])+'&email='+encodeURIComponent(array_split[2]);
				else
					document.location.href = historyUrl;
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			if (textStatus !== 'abort')
			{
				error = "TECHNICAL ERROR: unable to confirm the order \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus;
	            if (!!$.prototype.fancybox)
	                $.fancybox.open([
	                    {
	                        type: 'inline',
	                        autoScale: true,
	                        minHeight: 30,
	                        content: '<p class="fancybox-error">' + error + '</p>'
	                    }
	                ], {
	                    padding: 0
	                });
	            else
	                alert(error);
			}
		}
	});
}

function saveAddress(type)
{
	if (type !== 'delivery' && type !== 'invoice')
		return false;

	var params = 'firstname=' + encodeURIComponent($('#firstname' + (type == 'invoice' ? '_invoice' : '')).val()) + '&lastname=' + encodeURIComponent($('#lastname' + (type == 'invoice' ? '_invoice' : '')).val()) + '&';
	if ($('#company' + (type == 'invoice' ? '_invoice' : '')).length)
		params += 'company=' + encodeURIComponent($('#company' + (type == 'invoice' ? '_invoice' : '')).val()) + '&';
	if ($('#vat_number' + (type == 'invoice' ? '_invoice' : '')).length)
		params += 'vat_number='+encodeURIComponent($('#vat_number' + (type == 'invoice' ? '_invoice' : '')).val()) + '&';
	if ($('#dni' + (type == 'invoice' ? '_invoice' : '')).length)
		params += 'dni=' + encodeURIComponent($('#dni' + (type == 'invoice' ? '_invoice' : '')).val())+'&';
	if ($('#address1' + (type == 'invoice' ? '_invoice' : '')).length)
		params += 'address1=' + encodeURIComponent($('#address1' + (type == 'invoice' ? '_invoice' : '')).val()) + '&';
	if ($('#address2' + (type == 'invoice' ? '_invoice' : '')).length)
		params += 'address2=' + encodeURIComponent($('#address2' + (type == 'invoice' ? '_invoice' : '')).val()) + '&';
	if ($('#postcode' + (type == 'invoice' ? '_invoice' : '')).length)
		params += 'postcode=' + encodeURIComponent($('#postcode' + (type == 'invoice' ? '_invoice' : '')).val()) + '&';
	if ($('#city' + (type == 'invoice' ? '_invoice' : '')).length)
		params += 'city=' + encodeURIComponent($('#city' + (type == 'invoice' ? '_invoice' : '')).val()) + '&';
	if ($('#id_country' + (type == 'invoice' ? '_invoice' : '')).length)
		params += 'id_country=' + parseInt($('#id_country' + (type == 'invoice' ? '_invoice' : '')).val()) + '&';
	if ($('#id_state' + (type == 'invoice' ? '_invoice' : '')).length)
		params += 'id_state=' + encodeURIComponent($('#id_state' + (type == 'invoice' ? '_invoice' : '')).val()) + '&';
	if ($('#other' + (type == 'invoice' ? '_invoice' : '')).length)
		params += 'other=' + encodeURIComponent($('#other' + (type == 'invoice' ? '_invoice' : '')).val()) + '&';
	if ($('#phone' + (type == 'invoice' ? '_invoice' : '')).length)
		params += 'phone=' + encodeURIComponent($('#phone' + (type == 'invoice' ? '_invoice' : '')).val()) + '&';
	if ($('#phone_mobile' + (type == 'invoice' ? '_invoice' : '')).length)
		params += 'phone_mobile=' + encodeURIComponent($('#phone_mobile' + (type == 'invoice' ? '_invoice' : '')).val()) + '&';
	if ($('#alias' + (type == 'invoice' ? '_invoice' : '')).length)
		params += 'alias=' + encodeURIComponent($('#alias' + (type == 'invoice' ? '_invoice' : '')).val()) + '&';
	if (type == 'delivery' && $('#opc_id_address_delivery').val() != undefined && parseInt($('#opc_id_address_delivery').val()) > 0)
		params += 'opc_id_address_delivery=' + parseInt($('#opc_id_address_delivery').val()) + '&';
	if (type == 'invoice' && $('#opc_id_address_invoice').val() != undefined && parseInt($('#opc_id_address_invoice').val()) > 0)
		params += 'opc_id_address_invoice=' + parseInt($('#opc_id_address_invoice').val()) + '&';
	// Clean the last &
	params = params.substr(0, params.length-1);

	var result = false;

	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: addressUrl + '?rand=' + new Date().getTime(),
		async: false,
		cache: false,
		dataType : "json",
		data: 'ajax=true&submitAddress=true&type='+type+'&'+params+'&token=' + static_token,
		success: function(jsonData)
		{
			if (jsonData.hasError)
			{
				var tmp = '';
				var i = 0;
				for(var error in jsonData.errors)
					//IE6 bug fix
					if(error !== 'indexOf')
					{
						i = i+1;
						tmp += '<li>'+jsonData.errors[error]+'</li>';
					}
				tmp += '</ol>';
				var errors = '<b>'+txtThereis+' '+i+' '+txtErrors+':</b><ol>'+tmp;
				$('#opc_account_errors').slideUp('fast', function(){
					$(this).html(errors).slideDown('slow', function(){
						$.scrollTo('#opc_account_errors', 800);
					});
				});
				$('#guest-info-block, #opc_account-overlay, #opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');
				result = false;
			}
			else
			{
				// update addresses id
				$('input#opc_id_address_delivery').val(jsonData.id_address_delivery);
				$('input#opc_id_address_invoice').val(jsonData.id_address_invoice);
				result = true;
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			if (textStatus !== 'abort')
			{
				error = "TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus;
	            if (!!$.prototype.fancybox)
	                $.fancybox.open([
	                    {
	                        type: 'inline',
	                        autoScale: true,
	                        minHeight: 30,
	                        content: '<p class="fancybox-error">' + error + '</p>'
	                    }
	                ], {
	                    padding: 0
	                });
	            else
	                alert(error);
			}
			$('#guest-info-block, #opc_account-overlay, #opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');
		}
		});

	return result;
}

function updateNewAccountToAddressBlock(is_adv_api)
{
	//$('#guest-info-block, #opc_account-overlay, #opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');

	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: orderOpcUrl + '?rand=' + new Date().getTime(),
		async: true,
		cache: false,
		dataType : "json",
		data: 'ajax=true&method=getAddressBlockAndCarriersAndPayments&token=' + static_token +
              (is_adv_api ? '&isAdvApi=1' : '') ,
		success: function(json)
		{
			if (json.hasError)
			{
				var errors = '';
				for(var error in json.errors)
					//IE6 bug fix
					if(error !== 'indexOf')
						errors += $('<div />').html(json.errors[error]).text() + "\n";
				alert(errors);
			}
			else
			{
				isLogged = 1;
				if (json.no_address == 1)
					document.location.href = addressUrl;

				$('.guest-info-block').fadeOut('fast', function()
				{
					if (typeof json.formatedAddressFieldsValuesList !== 'undefined' && json.formatedAddressFieldsValuesList )
					{
						formatedAddressFieldsValuesList = json.formatedAddressFieldsValuesList;
					}
					// if (typeof json.order_opc_adress !== 'undefined' && json.order_opc_adress)
					// {
					// 	$('#opc_new_account').html(json.order_opc_adress);
					// }
					// update block user info

					// 1.5 template
					// if (json.block_user_info !== '' && $('#header_user').length == 1)
					// {
					// 	var elt = $(json.block_user_info).find('#header_user_info').html();
					// 	$('#header_user_info').fadeOut('normal', function() {
					// 		$(this).html(elt).fadeIn();
					// 	});
					// }

					// 1.6 temmplate
					if (json.block_user_info_nav !== '' && $('.header_user_info').length == 1)
					{
						$('.header_user_info').fadeOut('normal', function() {
							$(this).html(json.block_user_info_nav).fadeIn();
						});
					}

					$(this).fadeIn('fast', function()
					{
                        if ($('#gift-price').length == 1)
                            $('#gift-price').html(json.gift_price);

						//After login, the products are automatically associated to an address
						// $.each(json.summary.products, function()
						// {
						// 	updateAddressId(this.id_product, this.id_product_attribute, '0', this.id_address_delivery);
						// });
						// updateAddressesDisplay(true);
                        if (typeof is_adv_api === 'undefined' || !is_adv_api)
                        {
                            updateCarrierList(json.carrier_data);
                            updateCarrierSelectionAndGift();
                            updatePaymentMethods(json);
                        }
                        $('#opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');
					});
				});
			}
			location.reload();
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			if (textStatus !== 'abort')
				alert("TECHNICAL ERROR: unable to send login informations \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			$('#opc_delivery_methods-overlay, #opc_payment_methods-overlay').fadeOut('slow');
		}
	});
}

function bindInputs()
{
	// Order message update
	$('#message').blur(function() {
		$('#opc_delivery_methods-overlay').fadeIn('slow');
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: orderOpcUrl + '?rand=' + new Date().getTime(),
			async: false,
			cache: false,
			dataType : "json",
			data: 'ajax=true&method=updateMessage&message=' + encodeURIComponent($('#message').val()) + '&token=' + static_token ,
			success: function(jsonData)
			{
				if (jsonData.hasError)
				{
					var errors = '';
					for(var error in jsonData.errors)
						//IE6 bug fix
						if(error !== 'indexOf')
							errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
					alert(errors);
				}
			else
				$('#opc_delivery_methods-overlay').fadeOut('slow');
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				if (textStatus !== 'abort')
					alert("TECHNICAL ERROR: unable to save message \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
				$('#opc_delivery_methods-overlay').fadeOut('slow');
			}
		});
		if (typeof bindUniform !=='undefined')
			bindUniform();
	});

	// Recyclable checkbox
	$('#recyclable').on('click', function(e){
		updateCarrierSelectionAndGift();
	});

	// Gift checkbox update
	$('#gift').off('click').on('click', function(e){
		if ($('#gift').is(':checked'))
			$('#gift_div').show();
		else
			$('#gift_div').hide();
		updateCarrierSelectionAndGift();
	});

	if ($('#gift').is(':checked'))
		$('#gift_div').show();
	else
		$('#gift_div').hide();

	// Gift message update
	$('#gift_message').on('change', function() {
		updateCarrierSelectionAndGift();
	});

	// Term Of Service (TOS)
	$('#cgv').on('click', function(e){
		updatePaymentMethodsDisplay();
	});
}

function multishippingMode(it)
{
	if ($(it).prop('checked'))
	{
		$('#address_delivery, .address_delivery').hide();
		$('#address_delivery, .address_delivery').parent().hide();
		$('#address_invoice').removeClass('alternate_item').addClass('item');
		$('#multishipping_mode_box').addClass('on');
		$('.addressesAreEquals').hide();
		$('#address_invoice_form').show();

		$(document).on('click', '#link_multishipping_form', function(e){e.preventDefault();});
		$('.address_add a').attr('href', addressMultishippingUrl);

		$(document).on('click', '#link_multishipping_form', function(e){
			if(!!$.prototype.fancybox)
				$.fancybox({
					'openEffect': 'elastic',
					'closeEffect': 'elastic',
					'type': 'ajax',
					'href':     this.href,
					'beforeClose': function(){
						// Reload the cart
						$.ajax({
							type: 'POST',
							headers: { "cache-control": "no-cache" },
							url: orderOpcUrl + '?rand=' + new Date().getTime(),
							data: 'ajax=true&method=cartReload',
							dataType : 'html',
							cache: false,
							success: function(data) {
								$('#cart_summary').replaceWith($(data).find('#cart_summary'));
								$('.cart_quantity_input').typeWatch({ highlight: true, wait: 600, captureLength: 0, callback: function(val) { updateQty(val, true, this.el); } });
							}
						});
						updateCarrierSelectionAndGift();
					},
					'beforeLoad': function(){
						// Removing all ids on the cart to avoid conflic with the new one on the fancybox
						// This action could "break" the cart design, if css rules use ids of the cart
						$.each($('#cart_summary *'), function(it, el) {
							$(el).attr('id', '');
						});
					},
					'afterLoad': function(){
						$('.fancybox-inner .cart_quantity_input').typeWatch({ highlight: true, wait: 600, captureLength: 0, callback: function(val) { updateQty(val, false, this.el);} });
						cleanSelectAddressDelivery();
						$('.fancybox-outer').append($('<div class="multishipping_close_container"><a id="multishipping-close" class="btn btn-default button button-small" href="#"><span>'+CloseTxt+'</span></a></div>'));
						$(document).on('click', '#multishipping-close', function(e){
							var newTotalQty = 0;
							$('.fancybox-inner .cart_quantity_input').each(function(){
								newTotalQty += parseInt($(this).val());
							});
							if (newTotalQty !== totalQty) {
								if(!confirm(QtyChanged)) {
									return false;
								}
							}
							$.fancybox.close();
							return false;
						});
						totalQty = 0;
						$('.fancybox-inner .cart_quantity_input').each(function(){
							totalQty += parseInt($(this).val());
						});
					}
				});
		});
	}
	else
	{
		$('#address_delivery, .address_delivery').show();
		$('#address_invoice').removeClass('item').addClass('alternate_item');
		$('#multishipping_mode_box').removeClass('on');
		$('.addressesAreEquals').show();
		if ($('.addressesAreEquals').find('input:checked').length)
			$('#address_invoice_form').hide();
		else
			$('#address_invoice_form').show();
		$('.address_add a').attr('href', addressUrl);

		// Disable multi address shipping
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: orderOpcUrl + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			data: 'ajax=true&method=noMultiAddressDelivery'
		});

		// Reload the cart
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: orderOpcUrl + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			data: 'ajax=true&method=cartReload',
			dataType : 'html',
			success: function(data) {
				$('#cart_summary').replaceWith($(data).find('#cart_summary'));
			}
		});
	}
	if (typeof bindUniform !=='undefined') {
		bindUniform();
	}
}

$(document).on('click', '.btn-edit-guest-info', function(e) {
	e.preventDefault();
	$('#collapse-guest-info .card-body:nth-child(1)').hide();
	$('#collapse-guest-info .card-body:nth-child(2)').toggleClass('hidden');
});
