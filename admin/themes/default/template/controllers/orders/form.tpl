{*
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
*}
<script type="text/javascript">
	{if isset($cart->id) && $cart->id}
		var id_cart = {$cart->id|intval};
	{/if}
	var id_customer = 0;
	var admin_order_tab_link = "{$link->getAdminLink('AdminOrders')|addslashes}";
	var changed_shipping_price = false;
	var shipping_price_selected_carrier = '';
	var current_index = '{$current|escape:'html':'UTF-8'}&token={$token|escape:'html':'UTF-8'}';
	var admin_cart_link = '{$link->getAdminLink('AdminCarts')|addslashes}';
	var cart_quantity = new Array();
	var currencies = new Array();
	var id_currency = '';
	var id_lang = '';
	//var txt_show_carts = '{l s='Show carts and orders for this customer.' js=1}';
	//var txt_hide_carts = '{l s='Hide carts and orders for this customer.' js=1}';
	var defaults_order_state = new Array();
	var customization_errors = false;
	var pic_dir = '{$pic_dir}';
	var currency_format = 5;
	var currency_sign = '';
	var currency_blank = false;
	var priceDisplayPrecision = {$smarty.const._PS_PRICE_DISPLAY_PRECISION_|intval};

	{foreach from=$defaults_order_state key='module' item='id_order_state'}
		defaults_order_state['{$module}'] = '{$id_order_state}';
	{/foreach}
	$(document).ready(function() {

		$('#customer').typeWatch({
			captureLength: 3,
			highlight: true,
			wait: 100,
			callback: function(){ searchCustomers(); }
			});
		$('#product').typeWatch({
			captureLength: 1,
			highlight: true,
			wait: 750,
			callback: function(){ searchProducts(); }
		});
		$('#payment_module_name').change(function() {
			var id_order_state = defaults_order_state[this.value];
			if (typeof(id_order_state) == 'undefined')
				id_order_state = defaults_order_state['other'];
			$('#id_order_state').val(id_order_state);
		});
		$("#id_address_delivery").change(function() {
			updateAddresses();
		});
		$("#id_address_invoice").change(function() {
			updateAddresses();
		});
		$('#id_currency').change(function() {
			updateCurrency();
		});
		$('#id_lang').change(function(){
			updateLang();
		});
		$('#delivery_option,#carrier_recycled_package,#order_gift,#gift_message').change(function() {
			updateDeliveryOption();
		});
		$('#shipping_price').change(function() {
			if ($(this).val() != shipping_price_selected_carrier)
				changed_shipping_price = true;
		});

		$('#payment_module_name').change();
		$.ajaxSetup({ type:"post" });
		$("#voucher").autocomplete('{$link->getAdminLink('AdminCartRules')|addslashes}', {
					minChars: 3,
					max: 15,
					width: 250,
					selectFirst: false,
					scroll: false,
					dataType: "json",
					formatItem: function(data, i, max, value, term) {
						return value;
					},
					parse: function(data) {
						if (!data.found)
							$('#vouchers_err').html('{l s='No voucher was found'}').show();
						else
							$('#vouchers_err').hide();
						var mytab = new Array();
						for (var i = 0; i < data.vouchers.length; i++)
							mytab[mytab.length] = { data: data.vouchers[i], value: data.vouchers[i].name + (data.vouchers[i].code.length > 0 ? ' - ' + data.vouchers[i].code : '')};
						return mytab;
					},
					extraParams: {
						ajax: "1",
						token: "{getAdminToken tab='AdminCartRules'}",
						tab: "AdminCartRules",
						action: "searchCartRuleVouchers"
					}
				}
			)
			.result(function(event, data, formatted) {
				$('#voucher').val(data.name);
				add_cart_rule(data.id_cart_rule);
			});
		{if isset($cart->id) && $cart->id}
			setupCustomer({$cart->id_customer|intval});
			useCart('{$cart->id|intval}');
		{/if}

		$('.delete_product').live('click', function(e) {
			e.preventDefault();
			var to_delete = $(this).attr('rel').split('_');
			deleteProduct(to_delete[1], to_delete[2], to_delete[3]);
		});
		$('.delete_discount').live('click', function(e) {
			e.preventDefault();
			deleteVoucher($(this).attr('rel'));
		});
		$('.use_cart').live('click', function(e) {
			e.preventDefault();
			useCart($(this).attr('rel'));
			return false;
		});

		/*By Webkul to delete the rooms added in the cart*/
		$('body').on('click', '.delete_hotel_cart_data', function(){
			if (confirm("{l s='Are you sure?'}"))
        	{
				$.ajax({
					type:"POST",
					url: "{$link->getAdminLink('AdminOrders')|addslashes}",
					data : {
						ajax: "1",
						action: "deleteRoomProcess",
						del_id: $(this).data('id'),
						id_product: $(this).data('id_product'),
						id_cart: $(this).data('id_cart'),
						id_room: $(this).data('id_room'),
						date_from: $(this).data('date_from'),
						date_to: $(this).data('date_to'),
					},
					dataType:"json",
					success : function(data)
					{
						if (data.status == 'deleted')
						{
							showSuccessMessage("{l s='Remove successful'}");
							if (data.cart_rooms)
								location.reload();
							else
								window.location.href = "{$link->getAdminLink('AdminHotelRoomsBooking',true)}";
						}
						else
						{
							alert("l s='Some error occured.please try again.'}");
						}
					}
				});
				$(this).closest("tr").remove();
			}
		});

		$('body').on('click', '.delete_service_product', function(){
			if (confirm("{l s='Are you sure?'}"))
        	{

				$.ajax({
					type:"POST",
					url: "{$link->getAdminLink('AdminOrders')|addslashes}",
					data : {
						ajax: true,
						action: "deleteProductProcess",
						id_product: $(this).data('id_product'),
						id_cart: $(this).data('id_cart'),
						id_hotel: $(this).data('id_hotel'),
					},
					dataType:"json",
					success : function(data)
					{
						if (data.status == 'deleted')
						{
							showSuccessMessage("{l s='Remove successful'}");
							location.reload();
							{* if (data.cart_rooms)
								location.reload();
							else
								window.location.href = "{$link->getAdminLink('AdminHotelRoomsBooking',true)}"; *}
						}
						else
						{
							alert("l s='Some error occured.please try again.'}");
						}
					}
				});
				$(this).closest("tr").remove();
			}
		});
		/*END*/

		$('input:radio[name="free_shipping"]').on('change',function() {
			var free_shipping = $('input[name=free_shipping]:checked').val();
			$.ajax({
				type:"POST",
				url: "{$link->getAdminLink('AdminCarts')|addslashes}",
				async: true,
				dataType: "json",
				data : {
					ajax: "1",
					token: "{getAdminToken tab='AdminCarts'}",
					tab: "AdminCarts",
					action: "updateFreeShipping",
					id_cart: id_cart,
					id_customer: id_customer,
					'free_shipping': free_shipping
					},
				success : function(res)
				{
					displaySummary(res);
				}
			});
		});

		$('.duplicate_order').live('click', function(e) {
			e.preventDefault();
			duplicateOrder($(this).attr('rel'));
		});
		$('.cart_quantity').live('change', function(e) {
			e.preventDefault();
			if ($(this).val() != cart_quantity[$(this).attr('rel')])
			{
				var product = $(this).attr('rel').split('_');
				updateQty(product[0], product[1], product[2], $(this).val() - cart_quantity[$(this).attr('rel')]);
			}
		});
		$('.increaseqty_product, .decreaseqty_product').live('click', function(e) {
			e.preventDefault();
			var product = $(this).attr('rel').split('_');
			var sign = '';
			if ($(this).hasClass('decreaseqty_product'))
				sign = '-';
			updateQty(product[0], product[1],product[2], sign+1);
		});
		$('#id_product').live('keydown', function(e) {
			$(this).click();
			return true;
		});
		$('#id_product, .id_product_attribute').live('change', function(e) {
			e.preventDefault();
			displayQtyInStock(this.id);
		});
		$('#id_product, .id_product_attribute').live('keydown', function(e) {
			$(this).change();
			return true;
		});
		$(document).on('change', '.room_unit_price', function(e) {
			e.preventDefault();
			var cart_row = $(this).closest('tr');
			var params = {
				id_booking_data: parseInt($(cart_row).attr('data-id-booking-data')),
				id_product: parseInt($(cart_row).attr('data-id-product')),
				id_room: parseInt($(cart_row).attr('data-id-room')),
				date_from: $(cart_row).attr('data-date-from'),
				date_to: $(cart_row).attr('data-date-to'),
				price: new Number($(this).val().replace(",",".")).toFixed(4).toString(),
				id_cart: id_cart
			};
			updateProductPrice(params, cart_row);
		})
		$('#order_message').live('change', function(e) {
			e.preventDefault();
			$.ajax({
				type:"POST",
				url: "{$link->getAdminLink('AdminCarts')|addslashes}",
				async: true,
				dataType: "json",
				data : {
					ajax: "1",
					token: "{getAdminToken tab='AdminCarts'}",
					tab: "AdminCarts",
					action: "updateOrderMessage",
					id_cart: id_cart,
					id_customer: id_customer,
					message: $(this).val()
					},
				success : function(res)
				{
					displaySummary(res);
				}
			});
		});

		resetBind();

		$('#customer').focus();

		$('#submitAddProduct').on('click',function(){
			addProduct();
		});

		$('#product').bind('keypress', function(e) {
			var code = (e.keyCode ? e.keyCode : e.which);
			if(code == 13)
			{
				e.stopPropagation();
				e.preventDefault();
				if ($('#submitAddProduct').length)
					addProduct();
			}
		});

		$('#send_email_to_customer').on('click',function(){
			sendMailToCustomer();
			return false;
		});

		$('#products_found').hide();
		$('#carts').hide();

		$('#customer_part').on('click','button.setup-customer',function(e){
			e.preventDefault();
			setupCustomer($(this).data('customer'));
			updateCurrency();
			$(this).removeClass('setup-customer').addClass('change-customer').html('<i class="icon-refresh"></i>&nbsp;{l s="Change"}').blur();
			$(this).closest('.customerCard').addClass('selected-customer');
			$('.selected-customer .panel-heading').prepend('<i class="icon-ok text-success"></i>');
			$('.customerCard').not('.selected-customer').remove();
			$('#search-customer-form-group').hide();
			//cart id is additionally send in query by webkul
			var query = 'ajax=1&token='+token+'&action=changePaymentMethod&id_customer='+$(this).data('customer')+'&id_cart='+$(this).data('id_cart');
			$.ajax({
				type: 'POST',
				url: admin_order_tab_link,
				headers: { "cache-control": "no-cache" },
				cache: false,
				dataType: 'json',
				data : query,
				success : function(data) {
					if (data.result)
					{
						$('#cart_detail_form').show();//line added by webkul
						$('#payment_module_name').replaceWith(data.view)
					}
				}
			});
		});

		$('#customer_part').on('click','button.change-customer',function(e){
			e.preventDefault();
			$('#search-customer-form-group').show();
			$(this).blur();
		});

		{literal}
			$(document).on('click', '.booking_occupancy_wrapper .remove-room-link', function(e) {
				e.preventDefault();

				booking_occupancy_inner = $(this).closest('.booking_occupancy_inner');
				$(this).closest('.occupancy_info_block').remove();
				$(booking_occupancy_inner).find('.room_num_wrapper').each(function(key, val) {
					$(this).text(room_txt + ' - '+ (key+1) );
				});
				setRoomTypeGuestOccupancy($(booking_occupancy_inner).closest('.booking_occupancy_wrapper'));
			});

			$(document).on('change', '.num_occupancy', function(e) {
				let current_room_occupancy = 0;
				$(this).closest('.occupancy_info_block').find('.num_occupancy').each(function(){
					current_room_occupancy += parseInt($(this).val());
				});
				let max_guests_in_room = $(this).closest(".booking_occupancy_wrapper").find('.max_guests').val();
				let max_allowed_for_current = (max_guests_in_room - current_room_occupancy) + parseInt($(this).val());
				if ($(this).val() > $(this).attr('max')) {
					$(this).val($(this).attr('max'));
				}
				if ($(this).val() > max_allowed_for_current) {
					$(this).val(max_allowed_for_current);
				}
				if ($(this).hasClass('num_children')) {
					var totalChilds = $(this).closest('.occupancy_info_block').find('.guest_child_age').length;
					if (totalChilds < $(this).val()) {
						if (totalChilds < max_child_in_room) {
							$(this).closest('.occupancy_info_block').find('.children_age_info_block').show();
							while ($(this).closest('.occupancy_info_block').find('.guest_child_age').length < $(this).val()) {


								var roomBlockIndex = parseInt($(this).closest('.occupancy_info_block').attr('occ_block_index'));

								var childAgeSelect = '<p class="col-xs-12 col-sm-12 col-md-6 col-lg-6">';
									childAgeSelect += '<select class="guest_child_age room_occupancies" name="occupancy[' +roomBlockIndex+ '][child_ages][]">';
										childAgeSelect += '<option value="-1">' + select_age_txt + '</option>';
										childAgeSelect += '<option value="0">' + under_1_age + '</option>';
										for (let age = 1; age < max_child_age; age++) {
											childAgeSelect += '<option value="'+age+'">'+age+'</option>';
										}
									childAgeSelect += '</select>';
								childAgeSelect += '</p>';
								$(this).closest('.occupancy_info_block').find('.children_ages').append(childAgeSelect);
							}
						}
					} else {
						let child = $(this).val();
						$(this).closest('.occupancy_info_block').find('.guest_child_age').each(function(ind, element) {
							if (child <= ind) {
								$(element).parent().remove();
							}
						});
						if (child == 0) {
							$(this).closest('.occupancy_info_block').find('.children_age_info_block').hide();
						}

					}
				}
				setRoomTypeGuestOccupancy($(this).closest('.booking_occupancy_wrapper'));

			});


			$(document).on('click', '.booking_guest_occupancy', function(e) {
				$(this).parent().toggleClass('open');
			});

			$(document).on('click', function(e) {
				if ($('.booking_occupancy_wrapper:visible').length) {
					var occupancy_wrapper = $('.booking_occupancy_wrapper:visible');
					$(occupancy_wrapper).find(".occupancy_info_block").addClass('selected');
					if (!($(e.target).closest(".booking_occupancy_wrapper").length || $(e.target).closest(".booking_guest_occupancy").length || $(e.target).closest(".ajax_add_to_cart_button").length || $(e.target).closest(".exclusive.book_now_submit").length)) {
						let hasErrors = 0;

						let adults = $(occupancy_wrapper).find(".num_adults").map(function(){return $(this).val();}).get();
						let children = $(occupancy_wrapper).find(".num_children").map(function(){return $(this).val();}).get();
						let child_ages = $(occupancy_wrapper).find(".guest_child_age").map(function(){return $(this).val();}).get();

						// start validating above values
						if (!adults.length || (adults.length != children.length)) {
							hasErrors = 1;
							showErrorMessage(invalid_occupancy_txt);
						} else {
							$(occupancy_wrapper).find('.occupancy_count').removeClass('error_border');

							// validate values of adults and children
							adults.forEach(function (item, index) {
								if (isNaN(item) || parseInt(item) < 1) {
									hasErrors = 1;
									$(occupancy_wrapper).find(".num_adults").eq(index).closest('.occupancy_count_block').find('.occupancy_count').addClass('error_border');
								}
								if (isNaN(children[index])) {
									hasErrors = 1;
									$(occupancy_wrapper).find(".num_children").eq(index).closest('.occupancy_count_block').find('.occupancy_count').addClass('error_border');
								}
							});

							// validate values of selected child ages
							$(occupancy_wrapper).find('.guest_child_age').parent().removeClass('has-error');
							child_ages.forEach(function (age, index) {
								age = parseInt(age);
								if (isNaN(age) || (age < 0) || (age >= parseInt(max_child_age))) {
									hasErrors = 1;
									$(occupancy_wrapper).find(".guest_child_age").eq(index).parent().addClass('has-error');
								}
							});
						}
						if (hasErrors == 0) {
							$(occupancy_wrapper).parent().removeClass('open');
							$(document).trigger( "QloApps:updateRoomOccupancy", [occupancy_wrapper]);
						}
					}
				}
			});

			$(document).on('QloApps:updateRoomOccupancy', function (e, occupancy_wrapper) {
				e.preventDefault();
				let cart_row = $(occupancy_wrapper).closest('tr');
				let occupancy = getBookingOccupancyDetails(cart_row);
				let params = {
					id_cart: id_cart,
					id_booking_data: parseInt($(cart_row).attr('data-id-booking-data')),
					occupancy : occupancy.shift(),
				};
				updateRoomOccupancy(params, cart_row);
			});

		function getBookingOccupancyDetails(bookingform)
		{
			let occupancy = [];
			let selected_occupancy = $(bookingform).find(".occupancy_info_block.selected")
			if (selected_occupancy.length) {
				$(selected_occupancy).each(function(ind, element) {
					if (parseInt($(element).find('.num_adults').val())) {
						let child_ages = [];
						$(element).find('.guest_child_age').each(function(index) {
							if ($(this).val() > -1) {
								child_ages.push($(this).val());
							}
						});
						if ($(element).find('.num_children').val()) {
							if (child_ages.length != $(element).find('.num_children').val()) {
								$(bookingform).find('.booking_occupancy_wrapper').parent().addClass('open');
							}
						}
						occupancy.push({
							'adults': $(element).find('.num_adults').val(),
							'children': $(element).find('.num_children').val(),
							'child_ages': child_ages
						});
					} else {
						$(bookingform).find('.booking_occupancy_wrapper').parent().addClass('open');
					}
				});
			} else {
				$(bookingform).find('.booking_occupancy_wrapper').parent().addClass('open');
			}

			return occupancy;
		}

		function setRoomTypeGuestOccupancy(booking_occupancy_wrapper)
		{
			var adults = 0;
			var children = 0;
			var rooms = $(booking_occupancy_wrapper).find('.occupancy_info_block').length;

			$(booking_occupancy_wrapper).find(".num_adults" ).each(function(key, val) {
				adults += parseInt($(this).val());
			});
			$(booking_occupancy_wrapper).find(".num_children" ).each(function(key, val) {
				children += parseInt($(this).val());
			});

			var guestButtonVal = parseInt(adults) + ' ';
			if (parseInt(adults) > 1) {
				guestButtonVal += adults_txt;
			} else {
				guestButtonVal += adult_txt;
			}
			if (parseInt(children) > 0) {
				if (parseInt(children) > 1) {
					guestButtonVal += ', ' + parseInt(children) + ' ' + children_txt;
				} else {
					guestButtonVal += ', ' + parseInt(children) + ' ' + child_txt;
				}
			}
			// if (parseInt(rooms) > 1) {
			// 	guestButtonVal += ', ' + parseInt(rooms) + ' ' + rooms_txt;
			// } else {
			// 	guestButtonVal += ', ' + parseInt(rooms) + ' ' + room_txt;
			// }
			// console.log($(booking_occupancy_wrapper).siblings('.booking_guest_occupancy > span'));
			$(booking_occupancy_wrapper).siblings('.booking_guest_occupancy').find('span').text(guestButtonVal);
		}
		{/literal}
	});

	function resetBind()
	{
		$('.fancybox').fancybox({
			'type': 'iframe',
			'width': '90%',
			'height': '90%',
		});

		$('.fancybox_customer').fancybox({
			'type': 'iframe',
			'width': '90%',
			'height': '90%',
			'afterClose' : function () {
				searchCustomers();
			}
		});
		/*$("#new_address").fancybox({
			onClosed: useCart(id_cart)
		});*/
	}

	function add_cart_rule(id_cart_rule)
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')|addslashes}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "addVoucher",
				id_cart_rule: id_cart_rule,
				id_cart: id_cart,
				id_customer: id_customer
				},
			success : function(res)
			{
				displaySummary(res);
				$('#voucher').val('');
				var errors = '';
				if (res.errors.length > 0)
				{
					$.each(res.errors, function() {
						errors += this+'<br/>';
					});
					$('#vouchers_err').html(errors).show();
				}
				else
					$('#vouchers_err').hide();
			}
		});
	}

	function updateProductPrice(params, cart_row)
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')|addslashes}",
			async: true,
			dataType: "JSON",
			data: {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "updateProductPrice",
				params: params,
			},
			success : function(response) {
				updateCartLine(response.curr_booking_info, cart_row);
				updateCartSummaryData(response.cart_info);
			}
		});
	}

	function updateRoomOccupancy(params, cart_row)
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')|addslashes}",
			async: true,
			dataType: "JSON",
			data: {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "updateRoomOccupancy",
				params: params,
			},
			success : function(response) {
				updateCartLine(response.curr_booking_info, cart_row);
				updateCartSummaryData(response.cart_info);
			}
		});
	}


	function updateCartLine(data, cart_row) {
		$(cart_row).find('.cart_line_total_rooms_price').html(data.amt_with_qty);
		$(cart_row).find('.cart_line_total_price').html(data.total_price);
	}

	function updateCartSummaryData(summaryData) {
		$('#total_rooms').html(formatCurrency(parseFloat(jsonSummary.summary.total_rooms + jsonSummary.summary.total_extra_demands + jsonSummary.summary.total_additional_services + jsonSummary.summary.total_additional_services_auto_add), currency_format, currency_sign, currency_blank));
		$('#total_vouchers').html(formatCurrency(parseFloat(summaryData.summary.total_discounts_tax_exc), currency_format, currency_sign, currency_blank));
		$('#total_convenience_fees').html(formatCurrency(parseFloat(jsonSummary.summary.convenience_fee), currency_format, currency_sign, currency_blank));
		$('#total_without_taxes').html(formatCurrency(parseFloat(jsonSummary.summary.total_price_without_tax - jsonSummary.summary.convenience_fee), currency_format, currency_sign, currency_blank));
		// $('#total_service_products').html(formatCurrency(parseFloat(jsonSummary.summary.total_service_products), currency_format, currency_sign, currency_blank));
		$('#total_taxes').html(formatCurrency(parseFloat(summaryData.summary.total_tax), currency_format, currency_sign, currency_blank));
		$('#total_with_taxes').html(formatCurrency(parseFloat(summaryData.summary.total_price), currency_format, currency_sign, currency_blank));
	}

	function displayQtyInStock(id)
	{
		var id_product = $('#id_product').val();
		if ($('#ipa_' + id_product + ' option').length)
			var id_product_attribute = $('#ipa_' + id_product).val();
		else
			var id_product_attribute = 0;

		$('#qty_in_stock').html(stock[id_product][id_product_attribute]);
	}

	function duplicateOrder(id_order)
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')|addslashes}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "duplicateOrder",
				id_order: id_order,
				id_customer: id_customer
				},
			success : function(res)
			{
				id_cart = res.cart.id;
				//$('#id_cart').val(id_cart);
				displaySummary(res);
			}
		});
	}

	function useCart(id_new_cart)
	{
		id_cart = id_new_cart;
		//$('#id_cart').val(id_cart);
		//$('#id_cart').val(id_cart);
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')|addslashes}",
			async: false,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "getSummary",
				id_cart: id_cart,
				id_customer: id_customer
				},
			success : function(res)
			{
				displaySummary(res);
			}
		});
	}

	function getSummary()
	{
		useCart(id_cart);
	}

	function deleteVoucher(id_cart_rule)
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')|addslashes}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "deleteVoucher",
				id_cart_rule: id_cart_rule,
				id_cart: id_cart,
				id_customer: id_customer
				},
			success : function(res)
			{
				displaySummary(res);
			}
		});
	}

	function deleteProduct(id_product, id_product_attribute, id_customization)
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')|addslashes}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "deleteProduct",
				id_product: id_product,
				id_product_attribute: id_product_attribute,
				id_customization: id_customization,
				id_cart: id_cart,
				id_customer: id_customer
				},
			success : function(res)
			{
				displaySummary(res);
			}
		});
	}

	function searchCustomers()
	{
		$.ajax({
			type:"POST",
			url : "{$link->getAdminLink('AdminCustomers')}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				tab: "AdminCustomers",
				action: "searchCustomers",
				customer_search: $('#customer').val()},
			success : function(res)
			{
				if(res.found)
				{
					var html = '';
					$.each(res.customers, function() {
						html += '<div class="customerCard col-lg-4">';
						html += '<div class="panel">';
						html += '<div class="panel-heading">'+this.firstname+' '+this.lastname;
						html += '<span class="pull-right">#'+this.id_customer+'</span></div>';
						html += '<span>'+this.email+'</span><br/>';
						html += '<span class="text-muted">'+((this.birthday != '0000-00-00') ? this.birthday : '')+'</span><br/>';
						html += '<div class="panel-footer">';
						html += '<a href="{$link->getAdminLink('AdminCustomers')}&id_customer='+this.id_customer+'&viewcustomer&liteDisplaying=1" class="btn btn-default fancybox"><i class="icon-search"></i> {l s='Details'}</a>';
						html += '<button type="button" data-id_cart="'+id_cart+'" data-customer="'+this.id_customer+'" class="setup-customer btn btn-default pull-right"><i class="icon-arrow-right"></i> {l s='Choose'}</button>';
						html += '</div>';
						html += '</div>';
						html += '</div>';
					});
				}
				else
					html = '<div class="alert alert-warning"><i class="icon-warning-sign"></i>&nbsp;{l s='No customers found'}</div>';
				$('#customers').html(html);
				resetBind();
			}
		});
	}

	function setupCustomer(idCustomer)
	{
		//$('#carts').show();// by webkul
		$('#products_part').show();
		$('#vouchers_part').show();
		//$('#address_part').show();// by webkul
		//$('#carriers_part').show();// by webkul
		$('#summary_part').show();
		var address_link = $('#new_address').attr('href');
		id_customer = idCustomer;
		id_cart = 0;
		{if isset($cart->id) && $cart->id}
			id_cart = "{$cart->id}";
		{/if}
		$('#new_address').attr('href', address_link.replace(/id_customer=[0-9]+/, 'id_customer='+id_customer));
		$.ajax({
			type:"POST",
			url : "{$link->getAdminLink('AdminCarts')|addslashes}",
			async: false,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "searchCarts",
				id_customer: id_customer,
				id_cart: id_cart
			},
			success : function(res)
			{
				if(res.found)
				{
					var html_carts = '';
					var html_orders = '';
					$.each(res.carts, function() {
						html_carts += '<tr>';
						html_carts += '<td>'+this.id_cart+'</td>';
						html_carts += '<td>'+this.date_add+'</td>';
						html_carts += '<td>'+this.total_price+'</td>';
						html_carts += '<td class="text-right">';
						html_carts += '<a title="{l s='View this cart'}" class="fancybox btn btn-default" href="index.php?tab=AdminCarts&id_cart='+this.id_cart+'&viewcart&token={getAdminToken tab='AdminCarts'}&liteDisplaying=1#"><i class="icon-search"></i>&nbsp;{l s="Details"}</a>';
						html_carts += '&nbsp;<a href="#" title="{l s='Use this cart'}" class="use_cart btn btn-default" rel="'+this.id_cart+'"><i class="icon-arrow-right"></i>&nbsp;{l s="Use"}</a>';
						html_carts += '</td>';
						html_carts += '</tr>';
					});

					$.each(res.orders, function() {
						html_orders += '<tr>';
						html_orders += '<td>'+this.id_order+'</td><td>'+this.date_add+'</td><td>'+(this.nb_products ? this.nb_products : '0')+'</td><td>'+this.total_paid_real+'</span></td><td>'+this.payment+'</td><td>'+this.order_state+'</td>';
						html_orders += '<td class="text-right">';
						html_orders += '<a href="{$link->getAdminLink('AdminOrders')}&id_order='+this.id_order+'&vieworder&liteDisplaying=1#" title="{l s='View this order'}" class="fancybox btn btn-default"><i class="icon-search"></i>&nbsp;{l s="Details"}</a>';
						html_orders += '&nbsp;<a href="#" "title="{l s='Duplicate this order'}" class="duplicate_order btn btn-default" rel="'+this.id_order+'"><i class="icon-arrow-right"></i>&nbsp;{l s="Use"}</a>';
						html_orders += '</td>';
						html_orders += '</tr>';
					});
					$('#nonOrderedCarts table tbody').html(html_carts);
					$('#lastOrders table tbody').html(html_orders);
				}
				if (res.id_cart)
				{
					id_cart = res.id_cart;
					//$('#id_cart').val(id_cart);
				}
				displaySummary(res);
				resetBind();
			}
		});
	}

	function updateDeliveryOptionList(delivery_option_list)
	{
		var html = '';
		if (delivery_option_list.length > 0)
		{
			$.each(delivery_option_list, function() {
				html += '<option value="'+this.key+'" '+(($('#delivery_option').val() == this.key) ? 'selected="selected"' : '')+'>'+this.name+'</option>';
			});
			$('#carrier_form').show();
			$('#delivery_option').html(html);
			$('#carriers_err').hide();
		}
		else
		{
			$('#carrier_form').hide();
			$('#carriers_err').show().html('{l s='No carrier can be applied to this order'}');
		}
	}

	function searchProducts()
	{
		$('#products_part').show();
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminOrders')|addslashes}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{$token|escape:'html':'UTF-8'}",
				tab: "AdminOrders",
				action: "searchProducts",
				id_cart: id_cart,
				id_customer: id_customer,
				id_currency: id_currency,
				product_search: $('#product').val()},
			success : function(res)
			{
				var products_found = '';
				var attributes_html = '';
				var customization_html = '';
				stock = {};

				if(res.found)
				{
					if (!customization_errors)
						$('#products_err').addClass('hide');
					else
						customization_errors = false;
					$('#products_found').show();
					products_found += '<label class="control-label col-lg-3">{l s='Product'}</label><div class="col-lg-6"><select id="id_product" onchange="display_product_attributes();display_product_customizations();"></div>';
					attributes_html += '<label class="control-label col-lg-3">{l s='Combination'}</label><div class="col-lg-6">';
					$.each(res.products, function() {
						products_found += '<option '+(this.combinations.length > 0 ? 'rel="'+this.qty_in_stock+'"' : '')+' value="'+this.id_product+'">'+this.name+(this.combinations.length == 0 ? ' - '+this.formatted_price : '')+'</option>';
						attributes_html += '<select class="id_product_attribute" id="ipa_'+this.id_product+'" style="display:none;">';
						var id_product = this.id_product;
						stock[id_product] = new Array();
						if (this.customizable == '1' || this.customizable == '2')
						{
							customization_html += '<div class="bootstrap"><div class="panel"><div class="panel-heading">{l s='Customization'}</div><form id="customization_'+id_product+'" class="id_customization" method="post" enctype="multipart/form-data" action="'+admin_cart_link+'" style="display:none;">';
							customization_html += '<input type="hidden" name="id_product" value="'+id_product+'" />';
							customization_html += '<input type="hidden" name="id_cart" value="'+id_cart+'" />';
							customization_html += '<input type="hidden" name="action" value="updateCustomizationFields" />';
							customization_html += '<input type="hidden" name="id_customer" value="'+id_customer+'" />';
							customization_html += '<input type="hidden" name="ajax" value="1" />';
							$.each(this.customization_fields, function() {
								class_customization_field = "";
								if (this.required == 1){ class_customization_field = 'required' };
								customization_html += '<div class="form-group"><label class="control-label col-lg-3 ' + class_customization_field + '" for="customization_'+id_product+'_'+this.id_customization_field+'">';
								customization_html += this.name+'</label><div class="col-lg-9">';
								if (this.type == 0)
									customization_html += '<input class="form-control customization_field" type="file" name="customization_'+id_product+'_'+this.id_customization_field+'" id="customization_'+id_product+'_'+this.id_customization_field+'">';
								else if (this.type == 1)
									customization_html += '<input class="form-control customization_field" type="text" name="customization_'+id_product+'_'+this.id_customization_field+'" id="customization_'+id_product+'_'+this.id_customization_field+'">';
								customization_html += '</div></div>';
							});
							customization_html += '</form></div></div>';
						}

						$.each(this.combinations, function() {
							attributes_html += '<option rel="'+this.qty_in_stock+'" '+(this.default_on == 1 ? 'selected="selected"' : '')+' value="'+this.id_product_attribute+'">'+this.attributes+' - '+this.formatted_price+'</option>';
							stock[id_product][this.id_product_attribute] = this.qty_in_stock;
						});

						stock[this.id_product][0] = this.stock[0];
						attributes_html += '</select>';
					});
					products_found += '</select></div>';
					$('#products_found #product_list').html(products_found);
					$('#products_found #attributes_list').html(attributes_html);
					$('link[rel="stylesheet"]').each(function (i, element) {
						sheet = $(element).clone();
						$('#products_found #customization_list').contents().find('head').append(sheet);
					});
					$('#products_found #customization_list').contents().find('body').html(customization_html);
					display_product_attributes();
					display_product_customizations();
					$('#id_product').change();
				}
				else
				{
					$('#products_found').hide();
					$('#products_err').html('{l s='No products found'}');
					$('#products_err').removeClass('hide');
				}
				resetBind();
			}
		});
	}

	function display_product_customizations()
	{
		if ($('#products_found #customization_list').contents().find('#customization_'+$('#id_product option:selected').val()).children().length === 0)
			$('#customization_list').hide();
		else
		{
			$('#customization_list').show();
			$('#products_found #customization_list').contents().find('.id_customization').hide();
			$('#products_found #customization_list').contents().find('#customization_'+$('#id_product option:selected').val()).show();
			$('#products_found #customization_list').css('height',$('#products_found #customization_list').contents().find('#customization_'+$('#id_product option:selected').val()).height()+95+'px');
		}
	}

	function display_product_attributes()
	{
		if ($('#ipa_'+$('#id_product option:selected').val()+' option').length === 0)
			$('#attributes_list').hide();
		else
		{
			$('#attributes_list').show();
			$('.id_product_attribute').hide();
			$('#ipa_'+$('#id_product option:selected').val()).show();
		}
	}

	function updateCartProducts(products, gifts, id_address_delivery)
	{
		var cart_content = '';
		$.each(products, function() {
			var id_product = Number(this.id_product);
			var id_product_attribute = Number(this.id_product_attribute);
			cart_quantity[Number(this.id_product)+'_'+Number(this.id_product_attribute)+'_'+Number(this.id_customization)] = this.cart_quantity;
			cart_content += '<tr><td><img src="'+this.image_link+'" title="'+this.name+'" /></td><td>'+this.name+'<br />'+this.attributes_small+'</td><td>'+this.reference+'</td><td><input type="text" rel="'+this.id_product+'_'+this.id_product_attribute+'" class="product_unit_price" value="' + this.numeric_price + '" /></td><td>';
			cart_content += (!this.id_customization ? '<div class="input-group fixed-width-md"><div class="input-group-btn"><a href="#" class="btn btn-default increaseqty_product" rel="'+this.id_product+'_'+this.id_product_attribute+'_'+(this.id_customization ? this.id_customization : 0)+'" ><i class="icon-caret-up"></i></a><a href="#" class="btn btn-default decreaseqty_product" rel="'+this.id_product+'_'+this.id_product_attribute+'_'+(this.id_customization ? this.id_customization : 0)+'"><i class="icon-caret-down"></i></a></div>' : '');
			cart_content += (!this.id_customization ? '<input type="text" rel="'+this.id_product+'_'+this.id_product_attribute+'_'+(this.id_customization ? this.id_customization : 0)+'" class="cart_quantity" value="'+this.cart_quantity+'" />' : '');
			cart_content += (!this.id_customization ? '<div class="input-group-btn"><a href="#" class="delete_product btn btn-default" rel="delete_'+this.id_product+'_'+this.id_product_attribute+'_'+(this.id_customization ? this.id_customization : 0)+'" ><i class="icon-remove text-danger"></i></a></div></div>' : '');
			cart_content += '</td><td>' + formatCurrency(this.numeric_total, currency_format, currency_sign, currency_blank) + '</td></tr>';

			if (this.id_customization && this.id_customization != 0)
			{
				$.each(this.customized_datas[this.id_product][this.id_product_attribute][id_address_delivery], function() {
					var customized_desc = '';
					if (typeof this.datas[1] !== 'undefined' && this.datas[1].length)
					{
						$.each(this.datas[1],function() {
							customized_desc += this.name + ': ' + this.value + '<br />';
							id_customization = this.id_customization;
						});
					}
					if (typeof this.datas[0] !== 'undefined' && this.datas[0].length)
					{
						$.each(this.datas[0],function() {
							customized_desc += this.name + ': <img src="' + pic_dir + this.value + '_small" /><br />';
							id_customization = this.id_customization;
						});
					}
					cart_content += '<tr><td></td><td>'+customized_desc+'</td><td></td><td></td><td>';
					cart_content += '<div class="input-group fixed-width-md"><div class="input-group-btn"><a href="#" class="btn btn-default increaseqty_product" rel="'+id_product+'_'+id_product_attribute+'_'+id_customization+'" ><i class="icon-caret-up"></i></a><a href="#" class="btn btn-default decreaseqty_product" rel="'+id_product+'_'+id_product_attribute+'_'+id_customization+'"><i class="icon-caret-down"></i></a></div>';
					cart_content += '<input type="text" rel="'+id_product+'_'+id_product_attribute+'_'+id_customization +'" class="cart_quantity" value="'+this.quantity+'" />';
					cart_content += '<div class="input-group-btn"><a href="#" class="delete_product btn btn-default" rel="delete_'+id_product+'_'+id_product_attribute+'_'+id_customization+'" ><i class="icon-remove"></i></a></div></div>';
					cart_content += '</td><td></td></tr>';
				});
			}
		});

		$.each(gifts, function() {
			cart_content += '<tr><td><img src="'+this.image_link+'" title="'+this.name+'" /></td><td>'+this.name+'<br />'+this.attributes_small+'</td><td>'+this.reference+'</td>';
			cart_content += '<td>{l s='Gift'}</td><td>'+this.cart_quantity+'</td><td>{l s='Gift'}</td></tr>';
		});
		$('#customer_cart tbody').html(cart_content);
	}

	function updateCartVouchers(vouchers)
	{
		var vouchers_html = '';
		if (typeof(vouchers) == 'object')
			$.each(vouchers, function(){
				if (parseFloat(this.value_real) === 0 && parseInt(this.free_shipping) === 1)
					var value = '{l s='Free shipping'}';
				else
					var value = this.value_real;

				vouchers_html += '<tr><td>'+this.name+'</td><td>'+this.description+'</td><td>'+value+'</td><td class="text-right"><a href="#" class="btn btn-default delete_discount" rel="'+this.id_discount+'"><i class="icon-remove text-danger"></i>&nbsp;{l s='Delete'}</a></td></tr>';
			});
		$('#voucher_list tbody').html($.trim(vouchers_html));
		if ($('#voucher_list tbody').html().length == 0)
			$('#voucher_list').hide();
		else
			$('#voucher_list').show();
	}

	function updateCartPaymentList(payment_list)
	{
		$('#payment_list').html(payment_list);
	}

	function fixPriceFormat(price)
	{
		if(price.indexOf(',') > 0 && price.indexOf('.') > 0) // if contains , and .
			if(price.indexOf(',') < price.indexOf('.')) // if , is before .
				price = price.replace(',','');  // remove ,
		price = price.replace(' ',''); // remove any spaces
		price = price.replace(',','.'); // remove , if price did not cotain both , and .
		return price;
	}

	function displaySummary(jsonSummary) {
		currency_format = jsonSummary.currency.format;
		currency_sign = jsonSummary.currency.sign;
		currency_blank = jsonSummary.currency.blank;
		priceDisplayPrecision = jsonSummary.currency.decimals ? 2 : 0;

		updateCartProducts(jsonSummary.summary.products, jsonSummary.summary.gift_products, jsonSummary.cart.id_address_delivery);
		updateCartVouchers(jsonSummary.summary.discounts);
		updateAddressesList(jsonSummary.addresses, jsonSummary.cart.id_address_delivery, jsonSummary.cart.id_address_invoice);

		if (!jsonSummary.summary.products.length || !jsonSummary.addresses.length || !jsonSummary.delivery_option_list) {
			$('#carriers_part').hide();
		} else {
			$('#carriers_part').hide();
		}

		updateDeliveryOptionList(jsonSummary.delivery_option_list);

		if (jsonSummary.cart.gift == 1) {
			$('#order_gift').attr('checked', true);
		} else {
			$('#carrier_gift').removeAttr('checked');
		}
		if (jsonSummary.cart.recyclable == 1) {
			$('#carrier_recycled_package').attr('checked', true);
		} else {
			$('#carrier_recycled_package').removeAttr('checked');
		}
		if (jsonSummary.free_shipping == 1) {
			$('#free_shipping').attr('checked', true);
		} else {
			$('#free_shipping_off').attr('checked', true);
		}

		$('#gift_message').html(jsonSummary.cart.gift_message);

		if (!changed_shipping_price) {
			$('#shipping_price').html('<b>' + formatCurrency(parseFloat(jsonSummary.summary.total_shipping), currency_format, currency_sign, currency_blank) + '</b>');
		}

		shipping_price_selected_carrier = jsonSummary.summary.total_shipping;

		$('#total_vouchers').html(formatCurrency(parseFloat(jsonSummary.summary.total_discounts_tax_exc), currency_format, currency_sign, currency_blank));
		$('#total_taxes').html(formatCurrency(parseFloat(jsonSummary.summary.total_tax), currency_format, currency_sign, currency_blank));
		$('#total_without_taxes').html(formatCurrency(parseFloat(jsonSummary.summary.total_price_without_tax - jsonSummary.summary.convenience_fee), currency_format, currency_sign, currency_blank));
		$('#total_with_taxes').html(formatCurrency(parseFloat(jsonSummary.summary.total_price), currency_format, currency_sign, currency_blank));
		$('#total_rooms').html(formatCurrency(parseFloat(jsonSummary.summary.total_rooms + jsonSummary.summary.total_extra_demands + jsonSummary.summary.total_additional_services + jsonSummary.summary.total_additional_services_auto_add), currency_format, currency_sign, currency_blank));
		$('#total_convenience_fees').html(formatCurrency(parseFloat(jsonSummary.summary.convenience_fee), currency_format, currency_sign, currency_blank));
		// $('#total_service_products').html(formatCurrency(parseFloat(jsonSummary.summary.total_service_products), currency_format, currency_sign, currency_blank));

		id_currency = jsonSummary.cart.id_currency;
		$('#id_currency option').removeAttr('selected');
		$('#id_currency option[value="'+id_currency+'"]').attr('selected', true);
		id_lang = jsonSummary.cart.id_lang;
		$('#id_lang option').removeAttr('selected');
		$('#id_lang option[value="'+id_lang+'"]').attr('selected', true);
		$('#send_email_to_customer').attr('rel', jsonSummary.link_order);
		if (!jsonSummary.is_backdate_order) {
			$('#go_order_process').show();
			$('#go_order_process').attr('href', jsonSummary.link_order);
		} else {
			$('#go_order_process').hide();
		}
		$('#order_message').val(jsonSummary.order_message);
		resetBind();
	}

	function updateQty(id_product, id_product_attribute, id_customization, qty)
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')|addslashes}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "updateQty",
				id_product: id_product,
				id_product_attribute: id_product_attribute,
				id_customization: id_customization,
				qty: qty,
				id_customer: id_customer,
				id_cart: id_cart,
			},
			success : function(res)
			{
				displaySummary(res);
				var errors = '';
				if (res.errors.length)
				{
					$.each(res.errors, function() {
						errors += this + '<br />';
					});
					$('#products_err').removeClass('hide');
				}
				else
					$('#products_err').addClass('hide');
				$('#products_err').html(errors);
			}
		});
	}

	function resetShippingPrice()
	{
		$('#shipping_price').val(shipping_price_selected_carrier);
		changed_shipping_price = false;
	}

	function addProduct()
	{
		var id_product = $('#id_product option:selected').val();
		$('#products_found #customization_list').contents().find('#customization_'+id_product).submit();

		addProductProcess();
	}

	//Called from form_customization_feedback.tpl
	function customizationProductListener()
	{
		//refresh form customization
		searchProducts();

	}

	function addProductProcess()
	{
		if (customization_errors) {
			$('#products_err').removeClass('hide');
		} else {
			$('#products_err').addClass('hide');
			updateQty($('#id_product').val(), $('#ipa_'+$('#id_product').val()+' option:selected').val(), 0, $('#qty').val());
		}
	}

	function updateCurrency()
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')|addslashes}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "updateCurrency",
				id_currency: $('#id_currency option:selected').val(),
				id_customer: id_customer,
				id_cart: id_cart
				},
			success : function(res)
			{
				$("#customer_cart_details").empty();
				$("#customer_cart_details").append(res.cart_detail_html);

				displaySummary(res);
			}
		});
	}

	function updateLang()
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')|addslashes}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "admincarts",
				action: "updateLang",
				id_lang: $('#id_lang option:selected').val(),
				id_customer: id_customer,
				id_cart: id_cart
				},
			success : function(res)
			{
					displaySummary(res);
			}
		});
	}

	function updateDeliveryOption()
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')|addslashes}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "updateDeliveryOption",
				delivery_option: $('#delivery_option option:selected').val(),
				gift: $('#order_gift').is(':checked')?1:0,
				gift_message: $('#gift_message').val(),
				recyclable: $('#carrier_recycled_package').is(':checked')?1:0,
				id_customer: id_customer,
				id_cart: id_cart
				},
			success : function(res)
			{
				displaySummary(res);
			}
		});
	}

	function sendMailToCustomer()
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminOrders')|addslashes}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminOrders'}",
				tab: "AdminOrders",
				action: "sendMailValidateOrder",
				id_customer: id_customer,
				id_cart: id_cart
				},
			success : function(res)
			{
				if (res.errors)
					$('#send_email_feedback').removeClass('hide').removeClass('alert-success').addClass('alert-danger');
				else
					$('#send_email_feedback').removeClass('hide').removeClass('alert-danger').addClass('alert-success');
				$('#send_email_feedback').html(res.result);
			}
		});
	}

	function updateAddressesList(addresses, id_address_delivery, id_address_invoice)
	{
		var addresses_delivery_options = '';
		var addresses_invoice_options = '';
		var address_invoice_detail = '';
		var address_delivery_detail = '';
		var delivery_address_edit_link = '';
		var invoice_address_edit_link = '';

		$.each(addresses, function() {
			if (this.id_address == id_address_invoice)
			{
				address_invoice_detail = this.formated_address;
				invoice_address_edit_link = "{$link->getAdminLink('AdminAddresses')}&id_address="+this.id_address+"&updateaddress&realedit=1&liteDisplaying=1&submitFormAjax=1#";
			}

			if(this.id_address == id_address_delivery)
			{
				address_delivery_detail = this.formated_address;
				delivery_address_edit_link = "{$link->getAdminLink('AdminAddresses')}&id_address="+this.id_address+"&updateaddress&realedit=1&liteDisplaying=1&submitFormAjax=1#";
			}

			addresses_delivery_options += '<option value="'+this.id_address+'" '+(this.id_address == id_address_delivery ? 'selected="selected"' : '')+'>'+this.alias+'</option>';
			addresses_invoice_options += '<option value="'+this.id_address+'" '+(this.id_address == id_address_invoice ? 'selected="selected"' : '')+'>'+this.alias+'</option>';
		});
		if (addresses.length == 0)
		{
			$('#address_delivery, #address_invoice').hide();
			$("#new_address").show();
		}
		else
		{
			$('#addresses_err').hide();
			$("#new_address").hide();
			$('#address_delivery, #address_invoice').show();
		}

		/*Changed by webkul to make delivery and invoice addresses same*/
		$('#id_address_delivery').html(addresses_delivery_options).hide();
		$('#id_address_invoice').html(addresses_delivery_options).hide();
		$('#address_delivery_detail').html(address_delivery_detail);
		$('#address_invoice_detail').html(address_delivery_detail);
		$('#edit_delivery_address').attr('href', delivery_address_edit_link);
		$('#edit_invoice_address').attr('href', delivery_address_edit_link);
		/*END*/

		/*Original*/
		/*$('#id_address_delivery').html(addresses_delivery_options);
		$('#id_address_invoice').html(addresses_invoice_options);
		$('#address_delivery_detail').html(address_delivery_detail);
		$('#address_invoice_detail').html(address_invoice_detail);
		$('#edit_delivery_address').attr('href', delivery_address_edit_link);
		$('#edit_invoice_address').attr('href', invoice_address_edit_link);*/
	}

	function updateAddresses()
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')|addslashes}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "updateAddresses",
				id_customer: id_customer,
				id_cart: id_cart,
				id_address_delivery: $('#id_address_delivery option:selected').val(),
				id_address_invoice: $('#id_address_invoice option:selected').val()
				},
			success : function(res)
			{
				updateDeliveryOption();
			}
		});
	}

	{* JS for handling extra demands changes *}
	$(document).ready(function() {
		// modalbox for extra demands
		$('body').on('click', '.open_rooms_extra_demands', function() {
			var idProduct = $(this).attr('id_product');
			var idCart = $(this).attr('id_cart');
			var idRoom = $(this).attr('id_room');
			var dateFrom = $(this).attr('date_from');
			var dateTo = $(this).attr('date_to');
			$.ajax({
				type: 'POST',
				dataType: 'JSON',
				headers: {
					"cache-control": "no-cache"
				},
				url: "{$link->getAdminLink('AdminCarts')|addslashes}",
				cache: false,
				data: {
					date_from: dateFrom,
					date_to: dateTo,
					id_room: idRoom,
					id_cart: idCart,
					id_product: idProduct,
					action: 'getRoomTypeCartDemands',
					ajax: true
				},
				success: function(response) {
					if (response.status) {
						$('#customer_cart_details').after(response.html_exta_demands);
						// $('#rooms_type_extra_demands').find('#room_type_demands_desc').html('');
						// $('#rooms_type_extra_demands').find('#room_type_demands_desc').append(response.html_exta_demands);
						$('#rooms_type_extra_demands').modal('show');
					}
				},
			});
		});
		$(document).on('hidden.bs.modal', '#rooms_type_extra_demands', function (e) {
			// reload to make changes reflect everywhere
			location.reload();
		});

		// select/unselect extra demand
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
				dataType: 'JSON',
				headers: {
					"cache-control": "no-cache"
				},
				url: "{$link->getAdminLink('AdminCarts')|addslashes}",
				dataType: 'JSON',
				cache: false,
				data: {
					id_cart_booking: idBookingCart,
					room_demands: JSON.stringify(roomDemands),
					action: 'changeRoomDemands',
					ajax: true
				},
				success: function(response) {
					if (response.status) {
						showSuccessMessage(txtExtraDemandSucc);
					} else {
						showErrorMessage(txtExtraDemandErr);
					}
				}
			});
		});

		// change advanced option of extra demand
		$(document).on('change', '.demand_adv_option_block .id_option', function(e) {
			var option_selected = $(this).find('option:selected');
			var extra_demand_price = option_selected.attr("optionPrice")
			extra_demand_price = parseFloat(extra_demand_price);
			extra_demand_price = formatCurrency(extra_demand_price, currency_format, currency_sign, currency_blank);
			$(this).closest('.room_demand_block').find('.extra_demand_option_price').text(extra_demand_price);
			var roomDemands = [];
			if ($(this).closest('.room_demand_block').find('input:checkbox.id_room_type_demand').is(':checked')) {
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
					dataType: 'JSON',
					headers: {
						"cache-control": "no-cache"
					},
					url: "{$link->getAdminLink('AdminCarts')|addslashes}",
					dataType: 'JSON',
					cache: false,
					data: {
						id_cart_booking: idBookingCart,
						room_demands: JSON.stringify(roomDemands),
						action: 'changeRoomDemands',
						ajax: true
					},
					success: function(response) {
						if (response.status) {
							showSuccessMessage(txtExtraDemandSucc);
						} else {
							showErrorMessage(txtExtraDemandErr);
						}
					}
				});
			}
		});

		$(document).on('click', '.change_room_type_service_product', function() {
			updateServiceProducts(this);
		});

		$(document).on('focusout', '#rooms_type_extra_demands .qty', function(e) {
			var qty_wntd = $(this).val();
			if (qty_wntd == '' || !$.isNumeric(qty_wntd)) {
				$(this).val(1);
			}
			if ($(this).closest('.room_demand_block').find('.change_room_type_service_product').is(':checked')) {
				updateServiceProducts($(this).closest('.room_demand_block').find('.change_room_type_service_product'));
			}
		});

		function updateServiceProducts(element)
		{
			var operator = $(element).is(':checked') ? 'up' : 'down';
			var id_product = $(element).val();
			var id_cart_booking = $(element).data('id_cart_booking');
			var qty = $(element).closest('.room_demand_block').find('input.qty').val();
			if (typeof(qty) == 'undefined') {
				qty = 1;
			}
			$.ajax({
				type: 'POST',
				headers: {
					"cache-control": "no-cache"
				},
				url: "{$link->getAdminLink('AdminOrders')|addslashes}",
				dataType: 'JSON',
				cache: false,
				data: {
					operator: operator,
					id_product: id_product,
					id_cart_booking: id_cart_booking,
					qty: qty,
					action: 'updateServiceProduct',
					ajax: true
				},
				success: function(jsonData) {
					if (!jsonData.hasError) {
						showSuccessMessage(txtExtraDemandSucc);
					} else {
						showErrorMessage(jsonData.errors);

					}
				}
			});

		}
	});
</script>

<div class="leadin">{block name="leadin"}{/block}</div>
{include file='controllers/orders/_current_cart_details_data.tpl'}
	<div class="panel form-horizontal" id="customer_part" {if isset($is_order_created) && $is_order_created}style="display:none;"{/if}>
		<div class="panel-heading">
			<i class="icon-user"></i>
			{l s='Customer'}
		</div>
		<div id="search-customer-form-group" class="form-group">
			<label class="control-label col-lg-3">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Search for an existing customer by typing the first letters of his/her name.'}">
					{l s='Search for a customer'}
				</span>
			</label>
			<div class="col-lg-9">
				<div class="row">
					<div class="col-lg-6">
						<div class="input-group">
							<input type="text" id="customer" value="" />
							<span class="input-group-addon">
								<i class="icon-search"></i>
							</span>
						</div>
					</div>
					<div class="col-lg-6">
						<span class="form-control-static">{l s='Or'}&nbsp;</span>
						<a class="fancybox_customer btn btn-default" href="{$link->getAdminLink('AdminCustomers')|escape:'html':'UTF-8'}&amp;addcustomer&amp;liteDisplaying=1&amp;submitFormAjax=1#">
							<i class="icon-plus-sign-alt"></i>
							{l s='Add new customer'}
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div id="customers"></div>
		</div>
		{*<div id="carts">
			<button type="button" id="show_old_carts" class="btn btn-default pull-right" data-toggle="collapse" data-target="#old_carts_orders">
				<i class="icon-caret-down"></i>
			</button>

			<ul id="old_carts_orders_navtab" class="nav nav-tabs">
				<li class="active">
					<a href="#nonOrderedCarts" data-toggle="tab">
						<i class="icon-shopping-cart"></i>
						{l s='Carts'}
					</a>
				</li>
				<li>
					<a href="#lastOrders" data-toggle="tab">
						<i class="icon-credit-card"></i>
						{l s='Orders'}
					</a>
				</li>
			</ul>
			<div id="old_carts_orders" class="tab-content panel collapse in">
				<div id="nonOrderedCarts" class="tab-pane active">
					<table class="table">
						<thead>
							<tr>
								<th><span class="title_box">{l s='ID'}</span></th>
								<th><span class="title_box">{l s='Date'}</span></th>
								<th><span class="title_box">{l s='Total'}</span></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
				<div id="lastOrders" class="tab-pane">
					<table class="table">
						<thead>
							<tr>
								<th><span class="title_box">{l s='ID'}</span></th>
								<th><span class="title_box">{l s='Date'}</span></th>
								<th><span class="title_box">{l s='Products'}</span></th>
								<th><span class="title_box">{l s='Total paid'}</span></th>
								<th><span class="title_box">{l s='Payment'}</span></th>
								<th><span class="title_box">{l s='Status'}</span></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div> -->*}<!-- by webkul to hide unnessesary content -->
	</div>

<form class="form-horizontal" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;submitAdd{$table|escape:'html':'UTF-8'}=1" method="post" autocomplete="off" style="display:none" id="cart_detail_form">
	<div class="panel" id="products_part" style="display:none;">
		<div class="panel-heading">
			<i class="icon-shopping-cart"></i>
			{l s='Cart'}
		</div>
		<div class="form-group">
			<input type="hidden" value="{$cart->id}" id="id_cart" name="id_cart" />
		</div>
		{*<div class="form-group">
			<label class="control-label col-lg-3">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Search for an existing product by typing the first letters of its name.'}">
					{l s='Search for a product'}
				</span>
			</label>
			<div class="col-lg-9">
				<input type="hidden" value="{$cart->id}" id="id_cart" name="id_cart" />
				<div class="input-group">
					<input type="text" id="product" value="" />
					<span class="input-group-addon">
						<i class="icon-search"></i>
					</span>
				</div>
			</div>
		</div>

		<div id="products_found">
			<hr/>
			<div id="product_list" class="form-group"></div>
			<div id="attributes_list" class="form-group"></div> -->
			<!-- @TODO: please be kind refacto -->
			<div class="form-group">
				<div class="col-lg-9 col-lg-offset-3">
					<iframe id="customization_list" seamless>
						<html>
						<head>
							{if isset($css_files_orders)}
								{foreach from=$css_files_orders key=css_uri item=media}
									<link href="{$css_uri}" rel="stylesheet" type="text/css" media="{$media}" />
								{/foreach}
							{/if}
						</head>
						<body>
						</body>
						</html>
					</iframe>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3" for="qty">{l s='Quantity'}</label>
				<div class="col-lg-9">
					<input type="text" name="qty" id="qty" class="form-control fixed-width-sm" value="1" />
					<p class="help-block">{l s='In stock'} <span id="qty_in_stock"></span></p>
				</div>
			</div>

			<div class="form-group">
				<div class="col-lg-9 col-lg-offset-3">
					<button type="button" class="btn btn-default" id="submitAddProduct" />
					<i class="icon-ok text-success"></i>
					{l s='Add to cart'}
				</div>
			</div>
		</div>

		<div id="products_err" class="hide alert alert-danger"></div>

		<hr/>

		<div class="row">
			<div class="col-lg-12">
				<table class="table" id="customer_cart">
					<thead>
						<tr>
							<th><span class="title_box">{l s='Product'}</span></th>
							<th><span class="title_box">{l s='Description'}</span></th>
							<th><span class="title_box">{l s='Reference'}</span></th>
							<th><span class="title_box">{l s='Unit price'}</span></th>
							<th><span class="title_box">{l s='Quantity'}</span></th>
							<th><span class="title_box">{l s='Price'}</span></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>

		<div class="form-group">
			<div class="col-lg-9 col-lg-offset-3">
				<div class="alert alert-warning">{l s='The prices are without taxes.'}</div>
			</div>
		</div> *}<!-- by webkul to hide unnessesary content -->


		<div class="form-group">
			<label class="control-label col-lg-3" for="id_currency">
				{l s='Currency'}
			</label>
			<script type="text/javascript">
				{foreach from=$currencies item='currency'}
					currencies['{$currency.id_currency}'] = '{$currency.sign}';
				{/foreach}
			</script>
			<div class="col-lg-9">
				<select id="id_currency" name="id_currency">
					{foreach from=$currencies item='currency'}
						<option rel="{$currency.iso_code}" value="{$currency.id_currency}">{$currency.name}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="id_lang">
				{l s='Language'}
			</label>
			<div class="col-lg-9">
				<select id="id_lang" name="id_lang">
					{foreach from=$langs item='lang'}
						<option value="{$lang.id_lang}">{$lang.name}</option>
					{/foreach}
				</select>
			</div>
		</div>
	</div>

	<div class="panel" id="vouchers_part" style="display:none;">
		<div class="panel-heading">
			<i class="icon-ticket"></i>
			{l s='Vouchers'}
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3">
				{l s='Search for a voucher'}
			</label>
			<div class="col-lg-9">
				<div class="row">
					<div class="col-lg-6">
						<div class="input-group">
							<input type="text" id="voucher" value="" />
							<div class="input-group-addon">
								<i class="icon-search"></i>
							</div>
						</div>
					</div>
					<div class="col-lg-6">
						<span class="form-control-static">{l s='Or'}&nbsp;</span>
						<a class="fancybox btn btn-default" href="{$link->getAdminLink('AdminCartRules')|escape:'html':'UTF-8'}&amp;addcart_rule&amp;liteDisplaying=1&amp;submitFormAjax=1#">
							<i class="icon-plus-sign-alt"></i>
							{l s='Add new voucher'}
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<table class="table" id="voucher_list">
				<thead>
					<tr>
						<th><span class="title_box">{l s='Name'}</span></th>
						<th><span class="title_box">{l s='Description'}</span></th>
						<th><span class="title_box">{l s='Value'}</span></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div id="vouchers_err" class="alert alert-warning" style="display:none;"></div>
	</div>

	<div class="panel" id="address_part" style="">
		<div class="panel-heading">
			<i class="icon-envelope"></i>
			{l s='Addresses'}
		</div>
		<div id="addresses_err" class="alert alert-warning" style="display:none;"></div>

		<div class="row">
			<div id="address_delivery" class="col-xs-6 col-sm-6">
				<h4>
					<i class="icon-map-marker"></i>
					{l s='Customer Address'}
				</h4>
				<div class="row-margin-bottom">
					<select id="id_address_delivery" name="id_address_delivery"></select>
				</div>
				<div class="well">
					<a href="" id="edit_delivery_address" class="btn btn-default pull-right fancybox"><i class="icon-pencil"></i> {l s='Edit'}</a>
					<div id="address_delivery_detail"></div>
				</div>
			</div>
			<div id="address_invoice" class="col-lg-6 hidden">
				<h4>
					<i class="icon-file-text"></i>
					{l s='Invoice'}
				</h4>
				<div class="row-margin-bottom">
					<select id="id_address_invoice" name="id_address_invoice"></select>
				</div>
				<div class="well">
					<a href="" id="edit_invoice_address" class="btn btn-default pull-right fancybox"><i class="icon-pencil"></i> {l s='Edit'}</a>
					<div id="address_invoice_detail"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<a class="fancybox btn btn-default" id="new_address" href="{$link->getAdminLink('AdminAddresses')|escape:'html':'UTF-8'}&amp;addaddress&amp;id_customer=42&amp;liteDisplaying=1&amp;submitFormAjax=1#">
					<i class="icon-plus-sign-alt"></i>
					{l s='Add a new address'}
				</a>
			</div>
		</div>
	</div>
	<div class="panel" id="carriers_part" style="display:none;">
		<div class="panel-heading">
			<i class="icon-truck"></i>
			{l s='Shipping'}
		</div>
		<div id="carriers_err" style="display:none;" class="alert alert-warning"></div>
		<div id="carrier_form">
			<div class="form-group">
				<label class="control-label col-lg-3">
					{l s='Delivery option'}
				</label>
				<div class="col-lg-9">
					<select name="delivery_option" id="delivery_option">
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3" for="shipping_price">
					{l s='Shipping price (Tax incl.)'}
				</label>
				<div class="col-lg-9">
					<p id="shipping_price" class="form-control-static" name="shipping_price"></p>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3" for="free_shipping">
					{l s='Free shipping'}
				</label>
				<div class="input-group col-lg-9 fixed-width-lg">
					<span class="switch prestashop-switch">
						<input type="radio" name="free_shipping" id="free_shipping" value="1">
						<label for="free_shipping" class="radioCheck">
							{l s='yes'}
						</label>
						<input type="radio" name="free_shipping" id="free_shipping_off" value="0" checked="checked">
						<label for="free_shipping_off" class="radioCheck">
							{l s='No'}
						</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>

			{if $recyclable_pack}
			<div class="form-group">
				<div class="checkbox col-lg-9 col-offset-3">
					<label for="carrier_recycled_package">
						<input type="checkbox" name="carrier_recycled_package" value="1" id="carrier_recycled_package" />
						{l s='Recycled package'}
					</label>
				</div>
			</div>
			{/if}

			{if $gift_wrapping}
			<div class="form-group">
				<div class="checkbox col-lg-9 col-offset-3">
					<label for="order_gift">
						<input type="checkbox" name="order_gift" id="order_gift" value="1" />
						{l s='Gift'}
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3" for="gift_message">{l s='Gift message'}</label>
				<div class="col-lg-9">
					<textarea id="gift_message" class="form-control" cols="40" rows="4"></textarea>
				</div>
			</div>
			{/if}
		</div>
	</div>
	<div class="panel" id="summary_part" style="display:none;">
		<div class="panel-heading">
			<i class="icon-align-justify"></i>
			{l s='Summary'}
		</div>

		<div id="send_email_feedback" class="hide alert"></div>

		<div id="cart_summary" class="panel row-margin-bottom text-center">
			<div class="row">
				<div class="col-lg-2">
					<div class="data-focus">
						<span>{l s='Total rooms (Tax excl.)'}</span><br/>
						<span id="total_rooms" class="size_l text-success"></span>
					</div>
				</div>
				{* <div class="col-lg-2">
					<div class="data-focus">
						<span>{l s='Total extra services (Tax excl.)'}</span><br/>
						<span id="total_extra_services" class="size_l text-success"></span>
					</div>
				</div> *}
				{* <div class="col-lg-2">
					<div class="data-focus">
						<span>{l s='Total Total service products (Tax excl.)'}</span><br/>
						<span id="total_service_products" class="size_l text-success"></span>
					</div>
				</div> *}
				<div class="col-lg-2">
					<div class="data-focus">
						<span>{l s='Total vouchers (Tax excl.)'}</span><br/>
						<span id="total_vouchers" class="size_l text-danger"></span>
					</div>
				</div>
				<div class="col-lg-2">
					<div class="data-focus">
						<span>{l s='Total (Tax excl.)'}</span><br/>
						<span id="total_without_taxes" class="size_l"></span>
					</div>
				</div>
				<div class="col-lg-2">
					<div class="data-focus">
						<span>{l s='Convenience fees (Tax excl.)'}</span><br/>
						<span id="total_convenience_fees" class="size_l"></span>
					</div>
				</div>
				<div class="col-lg-2">
					<div class="data-focus">
						<span>{l s='Total taxes'}</span><br/>
						<span id="total_taxes" class="size_l"></span>
					</div>
				</div>
				<div class="col-lg-2">
					<div class="data-focus data-focus-primary">
						<span>{l s='Total (Tax incl.)'}</span><br/>
						<span id="total_with_taxes" class="size_l"></span>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="order_message_right col-lg-12">
				<div class="form-group">
					<label class="control-label col-lg-3" for="order_message">{l s='Order message'}</label>
					<div class="col-lg-6">
						<textarea name="order_message" id="order_message" rows="3" cols="45"></textarea>
					</div>
				</div>
				<div class="form-group">
					{if !$PS_CATALOG_MODE}
					<div class="col-lg-9 col-lg-offset-3">
						<a href="javascript:void(0);" id="send_email_to_customer" class="btn btn-default">
							<i class="icon-credit-card"></i>
							{l s='Send an email to the customer with the link to process the payment.'}
						</a>
						<a id="go_order_process" href="" class="btn btn-link _blank">
							{l s='Go on payment page to process the payment.'}
							<i class="icon-external-link"></i>
						</a>
					</div>
					{/if}
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Payment'}</label>
					<div class="col-lg-9">
						<select name="payment_module_name" id="payment_module_name">
							{if !$PS_CATALOG_MODE}
							{foreach from=$payment_modules item='module'}
								<option value="{$module->name}" {if isset($smarty.post.payment_module_name) && $module->name == $smarty.post.payment_module_name}selected="selected"{/if}>{$module->displayName}</option>
							{/foreach}
							{else}
								<option value="boorder">{l s='Back office order'}</option>
							{/if}
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3">{l s='Order status'}</label>
					<div class="col-lg-9">
						<select name="id_order_state" id="id_order_state">
							{foreach from=$order_states item='order_state'}
								<option value="{$order_state.id_order_state}" {if isset($smarty.post.id_order_state) && $order_state.id_order_state == $smarty.post.id_order_state}selected="selected"{/if}>{$order_state.name}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="form-group">
					<div class="col-lg-9 col-lg-offset-3">
						<button type="submit" name="submitAddOrder" class="btn btn-default" />
							<i class="icon-check"></i>
							{l s='Create the order'}
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
{strip}
	{addJsDef max_child_age=$max_child_age}
	{addJsDef max_child_in_room=$max_child_in_room}
	{addJsDefL name='select_age_txt'}{l s='Select age' js=1}{/addJsDefL}
	{addJsDefL name='under_1_age'}{l s='Under 1' js=1}{/addJsDefL}
	{addJsDefL name='room_txt'}{l s='Room' js=1}{/addJsDefL}
	{addJsDefL name='rooms_txt'}{l s='Rooms' js=1}{/addJsDefL}
	{addJsDefL name='remove_txt'}{l s='Remove' js=1}{/addJsDefL}
	{addJsDefL name='adult_txt'}{l s='Adult' js=1}{/addJsDefL}
	{addJsDefL name='adults_txt'}{l s='Adults' js=1}{/addJsDefL}
	{addJsDefL name='child_txt'}{l s='Child' js=1}{/addJsDefL}
	{addJsDefL name='children_txt'}{l s='Children' js=1}{/addJsDefL}
	{addJsDefL name='below_txt'}{l s='Below' js=1}{/addJsDefL}
	{addJsDefL name='years_txt'}{l s='years' js=1}{/addJsDefL}
	{addJsDefL name='all_children_txt'}{l s='All Children' js=1}{/addJsDefL}
	{addJsDefL name='invalid_occupancy_txt'}{l s='Invalid occupancy(adults/children) found.' js=1}{/addJsDefL}
{/strip}

<div id="loader_container">
	<div id="loader"></div>
</div>
