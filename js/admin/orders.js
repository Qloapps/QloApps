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

var current_product = null;
var ajaxQueries = new Array();

$(document).ready(function() {
	// Init all events
	init();

	$('img.js-disabled-action').css({"opacity":0.5});
});

function stopAjaxQuery() {
	if (typeof(ajaxQueries) == 'undefined')
		ajaxQueries = new Array();
	for(i = 0; i < ajaxQueries.length; i++)
		ajaxQueries[i].abort();
	ajaxQueries = new Array();
}

function updateInvoice(invoices)
{
	// Update select on product edition line
	$('.edit_product_invoice').each(function() {
		var selected = $(this).children('option:selected').val();

		$(this).children('option').remove();
		for(i in invoices)
		{
			// Create new option
			var option = $('<option>'+invoices[i].name+'</option>').attr('value', invoices[i].id);
			if (invoices[i].id == selected)
				option.attr('selected', true);

			$(this).append(option);
		}
	});

	// Update select on product addition line
	$('#add_product_product_invoice').each(function() {
		var parent = $(this).children('optgroup.existing');
		parent.children('option').remove();
		for(i in invoices)
		{
			// Create new option
			var option = $('<option>'+invoices[i].name+'</option>').attr('value', invoices[i].id);

			parent.append(option);
		}
		parent.children('option:first').attr('selected', true);
	});

	// Update select on product addition line
	$('#payment_invoice').each(function() {
		$(this).children('option').remove();
		for(i in invoices)
		{
			// Create new option
			var option = $('<option>'+invoices[i].name+'</option>').attr('value', invoices[i].id);

			$(this).append(option);
		}
	});
}

function updateDocuments(documents_html)
{
	$('#documents_table').attr('id', 'documents_table_old');
	$('#documents_table_old').after(documents_html);
	$('#documents_table_old').remove();
}

function updateShipping(shipping_html)
{
	$('#shipping_table').attr('id', 'shipping_table_old');
	$('#shipping_table_old').after(shipping_html);
	$('#shipping_table_old').remove();
}

function populateWarehouseList(warehouse_list)
{
	$('#add_product_product_warehouse_area').hide();
	if (warehouse_list.length > 1)
	{
		$('#add_product_product_warehouse_area').show();
	}
	var order_warehouse_list = $('#warehouse_list').val().split(',');
	$('#add_product_warehouse').html('');
	var warehouse_selected = false;
	$.each(warehouse_list, function() {
		if (warehouse_selected == false && $.inArray(this.id_warehouse, order_warehouse_list))
			warehouse_selected = this.id_warehouse;

		$('#add_product_warehouse').append($('<option value="' + this.id_warehouse + '">' + this.name + '</option>'));
	});
	if (warehouse_selected)
		$('#add_product_warehouse').val(warehouse_selected);
}



function editProductRefreshTotal(element)
{
	element = element.parent().parent().parent();
	var element_list = [];

	// Customized product
	if(element.hasClass('customized'))
	{
		var element_list = $('.customized-' + element.find('.edit_product_id_order_detail').val());
		element = $(element_list[0]);
	}

	var quantity = parseInt(element.find('td .edit_product_quantity').val());
	if (quantity < 1 || isNaN(quantity))
		quantity = 1;
	if (use_taxes)
		var price = parseFloat(element.find('td .edit_product_price_tax_incl').val());
	else
		var price = parseFloat(element.find('td .edit_product_price_tax_excl').val())

	if (price < 0 || isNaN(price))
		price = 0;

	// Customized product
	if (element_list.length)
	{
		var qty = 0;
		$.each(element_list, function(i, elm) {
			if($(elm).find('.edit_product_quantity').length)
			{
				qty += parseInt($(elm).find('.edit_product_quantity').val());
				subtotal = makeTotalProductCaculation($(elm).find('.edit_product_quantity').val(), price);
				$(elm).find('.total_product').html(formatCurrency(subtotal, currency_format, currency_sign, currency_blank));
			}
		});

		var total = makeTotalProductCaculation(qty, price);
		element.find('td.total_product').html(formatCurrency(total, currency_format, currency_sign, currency_blank));
		element.find('td.productQuantity').html(qty);
	}
	else
	{
		var total = makeTotalProductCaculation(quantity, price);
		element.find('td.total_product').html(formatCurrency(total, currency_format, currency_sign, currency_blank));
	}

}

function makeTotalProductCaculation(quantity, price)
{
	return Math.round(quantity * price * 100) / 100;
}

function refreshProductLineView(element, view)
{
	var new_product_line = $(view);
	new_product_line.find('td').hide();

	var element_list = [];
	if (element.parent().parent().find('.edit_product_id_order_detail').length)
		var element_list = $('.customized-' + element.parent().parent().find('.edit_product_id_order_detail').val());
	if (!element_list.length)
		element_list = $(element.parent().parent());

	var current_product_line = element.parent().parent();
	current_product_line.replaceWith(new_product_line);
	element_list.remove();

	new_product_line.find('td').each(function() {
		if (!$(this).is('.product_invoice'))
			$(this).fadeIn('slow');
	});
}

function updateAmounts(order)
{
	$('#total_products td.amount').fadeOut('slow', function() {
		$(this).html(formatCurrency(parseFloat(order.total_products_wt), currency_format, currency_sign, currency_blank));
		$(this).fadeIn('slow');
	});
	$('#total_discounts td.amount').fadeOut('slow', function() {
		$(this).html(formatCurrency(parseFloat(order.total_discounts_tax_incl), currency_format, currency_sign, currency_blank));
		$(this).fadeIn('slow');
	});
	if (order.total_discounts_tax_incl > 0)
		$('#total_discounts').slideDown('slow');
	$('#total_wrapping td.amount').fadeOut('slow', function() {
		$(this).html(formatCurrency(parseFloat(order.total_wrapping_tax_incl), currency_format, currency_sign, currency_blank));
		$(this).fadeIn('slow');
	});
	if (order.total_wrapping_tax_incl > 0)
		$('#total_wrapping').slideDown('slow');
	$('#total_shipping td.amount').fadeOut('slow', function() {
		$(this).html(formatCurrency(parseFloat(order.total_shipping_tax_incl), currency_format, currency_sign, currency_blank));
		$(this).fadeIn('slow');
	});
	$('#total_order td.amount').fadeOut('slow', function() {
		$(this).html(formatCurrency(parseFloat(order.total_paid_tax_incl), currency_format, currency_sign, currency_blank));
		$(this).fadeIn('slow');
	});
	$('.total_paid').fadeOut('slow', function() {
		$(this).html(formatCurrency(parseFloat(order.total_paid_tax_incl), currency_format, currency_sign, currency_blank));
		$(this).fadeIn('slow');
	});
	$('.alert').slideDown('slow');
	$('#product_number').fadeOut('slow', function() {
		var old_quantity = parseInt($(this).html());
		$(this).html(old_quantity + 1);
		$(this).fadeIn('slow');
	});
	$('#shipping_table .weight').fadeOut('slow', function() {
		$(this).html(order.weight);
		$(this).fadeIn('slow');
	});
}

/**
 * This method allow to initialize all events
 */
function init()
{
	$('#txt_msg').on('keyup', function(){
		var length = $('#txt_msg').val().length;
		if (length > 600) length = '600+';
		$('#nbchars').html(length+'/600');
	});

	$('#newMessage').unbind('click').click(function(e) {
		$(this).hide();
		$('#message').show();
		e.preventDefault();
	});

	$('#cancelMessage').unbind('click').click(function(e) {
		$('#newMessage').show();
		$('#message').hide();
		e.preventDefault();
	});

	$('select#add_product_product_attribute_id').unbind('change');
	$('select#add_product_product_attribute_id').change(function() {
		$('#add_product_product_price_tax_incl').val(current_product.combinations[$(this).val()].price_tax_incl);
		$('#add_product_product_price_tax_excl').val(current_product.combinations[$(this).val()].price_tax_excl);

		populateWarehouseList(current_product.warehouse_list[$(this).val()]);

		addProductRefreshTotal();
		if (stock_management)
			$('#add_product_product_stock').html(current_product.combinations[$(this).val()].qty_in_stock);
	});

	$('.edit_shipping_number_link').unbind('click').click(function(e) {
		$(this).parent().parent().find('.shipping_number_show').hide();
		$(this).parent().find('.shipping_number_edit').show();

		$(this).parent().find('.edit_shipping_number_link').hide();
		$(this).parent().find('.cancel_shipping_number_link').show();
		e.preventDefault();
	});

	$('.cancel_shipping_number_link').unbind('click').click(function(e) {
		$(this).parent().parent().find('.shipping_number_show').show();
		$(this).parent().find('.shipping_number_edit').hide();

		$(this).parent().find('.edit_shipping_number_link').show();
		$(this).parent().find('.cancel_shipping_number_link').hide();
		e.preventDefault();
	});

	$('#add_product_product_invoice').unbind('change').change(function() {
		if ($(this).val() == '0')
			$('#new_invoice').slideDown('slow');
		else
			$('#new_invoice').slideUp('slow');
	});

	$('.js-set-payment').unbind('click').click(function(e) {
		var amount = $(this).attr('data-amount');
		$('input[name=payment_amount]').val(amount);
		var id_invoice = $(this).attr('data-id-invoice');
		$('select[name=payment_invoice] option[value='+id_invoice+']').attr('selected', true);
		e.preventDefault();
	});

	$('#add_voucher').unbind('click').click(function(e) {
		e.preventDefault();
		VoucherModal.show();
	});

	$(document).on('change','#discount_type', function(e) {
		// Percent type
		if ($(this).val() == 1)
		{
			$('#discount_value_field').show();
			$('#discount_currency_sign').hide();
			$('#discount_value_help').hide();
			$('#discount_percent_symbol').show();
		}
		// Amount type
		else if ($(this).val() == 2)
		{
			$('#discount_value_field').show();
			$('#discount_percent_symbol').hide();
			$('#discount_value_help').show();
			$('#discount_currency_sign').show();
		}
		// Free shipping
		else if ($(this).val() == 3)
		{
			$('#discount_value_field').hide();
		}
	});

	$('#discount_all_invoices').unbind('change').change(function() {
		if ($(this).is(':checked'))
			$('select[name=discount_invoice]').attr('disabled', true);
		else
			$('select[name=discount_invoice]').attr('disabled', false);
	});

	$('.open_payment_information').unbind('click').click(function(e) {
		e.preventDefault();
		OrderPaymentDetailModal.show($(this));
	});

	initRoomEvents();
	initProductEvents();
}

function initProductEvents()
{
    $('#add_product').unbind('click').click(function(e) {
		$('.cancel_product_change_link:visible').trigger('click');
		$('.add_product_fields').show();
		$('#customer_products_details').show();
		//invoice field is hidden because has not to be edited by webkul
		$('#add_product_product_invoice').hide();

		$('.edit_product_fields, .partial_refund_fields').hide();
		$('tr#new_normal_product').slideDown('fast', function () {
			$('tr#new_normal_product td').fadeIn('fast', function() {
				$('#add_normal_product_product_name').focus();
				scroll_if_anchor('#new_normal_product');
			});
		});

		e.preventDefault();
	});

	$('#cancelAddNormalProduct').unbind('click').click(function() {
		$('tr#new_normal_product td').fadeOut('fast');
		if (!($('#customer_products_details tbody tr').length > 1)) {
			$('#customer_products_details').hide();
		}
	});

	$("#add_normal_product_product_name").autocomplete(admin_order_tab_link,
		{
			minChars: 3,
			max: 10,
			width: 500,
			selectFirst: false,
			scroll: false,
			dataType: "json",
			highlightItem: true,
			formatItem: function(data, i, max, value, term) {
				return value;
			},
			parse: function(data) {
				var products = new Array();
				if (typeof(data.products) != 'undefined')
					for (var i = 0; i < data.products.length; i++)
						products[i] = { data: data.products[i], value: data.products[i].name };
				return products;
			},
			extraParams: {
				ajax: true,
				token: token,
				action: 'searchProducts',
				booking_product: 0,
				id_lang: id_lang,
				id_currency: id_currency,
				id_address: id_address,
				id_customer: id_customer,
				id_order: id_order,
				product_search: function() { return $('#add_normal_product_product_name').val(); }
			}
		}
	)
	.result(function(event, data, formatted) {
		if (!data)
		{
			$('#new_product input, #new_product select').each(function() {
				if ($(this).attr('id') != 'add_normal_product_product_name')
					$('#new_product input, #new_product select, #new_product button').attr('disabled', true);
			});
		}
		else
		{
			$('#new_product input, #new_product select, #new_product button').removeAttr('disabled');
			$('#new_product .booking_guest_occupancy').removeClass('disabled');

			// Keep product variable
			current_product = data;
			$('#add_normal_product_product_id').val(data.id_product);
			$('#add_normal_product_product_name').val(data.name);
			$('#add_normal_product_price_tax_incl').val(data.price_tax_incl);
			$('#add_product_product_price_tax_excl').val(data.price_tax_excl);
			addProductRefreshTotal();
			if (stock_management)
				$('#add_product_product_stock').html(data.stock[0]);

			if (current_product.combinations.length !== 0)
			{
				// Reset combinations list
				$('select#add_normal_product_attribute_id').html('');
				var defaultAttribute = 0;
				$.each(current_product.combinations, function() {
					$('select#add_normal_product_attribute_id').append('<option value="'+this.id_product_attribute+'"'+(this.default_on == 1 ? ' selected="selected"' : '')+'>'+this.attributes+'</option>');
					if (this.default_on == 1)
					{
						if (stock_management)
							$('#add_product_product_stock').html(this.qty_in_stock);
						defaultAttribute = this.id_product_attribute;
					}
				});
				// Show select list
				$('#add_product_product_attribute_area').show();

				populateWarehouseList(current_product.warehouse_list[defaultAttribute]);
			}
			else
			{
				// Reset combinations list
				$('select#add_normal_product_attribute_id').html('');
				// Hide select list
				$('#add_product_product_attribute_area').hide();

				populateWarehouseList(current_product.warehouse_list[0]);
			}
		}
	});

	$('input#add_product_product_quantity').unbind('keyup').keyup(function() {
		if (stock_management)
		{
			var quantity = parseInt($(this).val());
			if (quantity < 1 || isNaN(quantity))
				quantity = 1;
			var stock_available = parseInt($('#add_product_product_stock').html());
			// stock status update
			if (quantity > stock_available)
				$('#add_product_product_stock').css('font-weight', 'bold').css('color', 'red').css('font-size', '1.2em');
			else
				$('#add_product_product_stock').css('font-weight', 'normal').css('color', 'black').css('font-size', '1em');
		}
		// update occupancy input
		// updateOccupancyInput()
		// total update
		addRoomRefreshTotal();
	});

	$(document).on('keyup', '#add_product_product_price_tax_excl', function() {
		var price_tax_excl = parseFloat($(this).val());
		if (price_tax_excl < 0 || isNaN(price_tax_excl))
			price_tax_excl = 0;

		var tax_rate = current_product.tax_rate / 100 + 1;
		$('#add_product_product_price_tax_incl').val(ps_round(price_tax_excl * tax_rate, 2));

		// Update total product
		addProductRefreshTotal();
	});

	$(document).on('keyup', '#add_product_product_price_tax_incl', function() {
		var price_tax_incl = parseFloat($(this).val());
		if (price_tax_incl < 0 || isNaN(price_tax_incl))
			price_tax_incl = 0;

		var tax_rate = current_product.tax_rate / 100 + 1;
		$('#add_product_product_price_tax_excl').val(ps_round(price_tax_incl / tax_rate, 2));

		// Update total product
		addProductRefreshTotal();
	});

	$(document).on('change', '.num_occupancy', function(e) {
        let elementVal = parseInt($(this).val());
		let current_room_occupancy = 0;
		$(this).closest('.occupancy_info_block').find('.num_occupancy').each(function(){
			current_room_occupancy += parseInt($(this).val());
		});
		let max_guests_in_room = $(this).closest(".booking_occupancy_wrapper").find('.max_guests').val();
		let max_allowed_for_current = (max_guests_in_room - current_room_occupancy) + parseInt($(this).val());
        let haserror = false
		if ($(this).hasClass('num_children')) {
            max_child_in_room = $(this).closest(".booking_occupancy_wrapper").find('.max_children').val();
            if (elementVal > max_child_in_room) {
                $(this).val(max_child_in_room);
                if (elementVal == 1) {
                    showOccupancyError(no_children_allowed_txt, $(this).closest(".occupancy_info_block"));
                    haserror = true;
                } else {
                    showOccupancyError(max_children_txt, $(this).closest(".occupancy_info_block"));
                    haserror = true;
                }
            } else if (elementVal > max_allowed_for_current)  {
                $(this).val(max_allowed_for_current);
                showOccupancyError(max_occupancy_reached_txt, $(this).closest(".occupancy_info_block"));
                haserror = true;
            }
        } else {
            max_adults_in_room = $(this).closest(".booking_occupancy_wrapper").find('.max_adults').val();
            if (elementVal >= max_adults_in_room) {
                $(this).val(max_adults_in_room);
                showOccupancyError(max_adults_txt, $(this).closest(".occupancy_info_block"));
                haserror = true;
            } else if (elementVal > max_allowed_for_current)  {
                $(this).val(max_allowed_for_current);
                showOccupancyError(max_occupancy_reached_txt, $(this).closest(".occupancy_info_block"));
                haserror = true;
            }
        }
        if (!haserror) {
			if ($(this).hasClass('num_children')) {
				var totalChilds = $(this).closest('.occupancy_info_block').find('.guest_child_age').length;
				if (totalChilds < $(this).val()) {
					let max_child_in_room;
					if ($(this).closest(".booking_occupancy_wrapper").find('.max_children').val()) {
						max_child_in_room = $(this).closest(".booking_occupancy_wrapper").find('.max_children').val();
					} else {
						max_child_in_room = window.max_child_in_room;
					}
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
		}
		setRoomTypeGuestOccupancy($(this).closest('.booking_occupancy_wrapper'));
	});

	var errorMsgTime;
    $('.occupancy-input-errors').parent().hide();
    function showOccupancyError(msg, occupancy_info_block)
    {
        var errorMsgBlock = $(occupancy_info_block).find('.occupancy-input-errors')
        $(errorMsgBlock).html(msg).parent().show('fast');
        clearTimeout(errorMsgTime);
        errorMsgTime = setTimeout(function() {
            $(errorMsgBlock).parent().hide('fast');
        }, 1000);
    }

	$(document).on('click', '.booking_guest_occupancy:not(.disabled)', function(e) {
		$(this).parent().toggleClass('open');
	});

	$(document).on('click', function(e) {
		if ($('.booking_occupancy_wrapper:visible').length) {
			var occupancy_wrapper = $('.booking_occupancy_wrapper:visible');
			$(occupancy_wrapper).find(".occupancy_info_block").addClass('selected');
			if (!($(e.target).closest(".booking_guest_occupancy").length || $(e.target).closest(".ajax_add_to_cart_button").length || $(e.target).closest(".exclusive.book_now_submit").length || $(e.target).closest(".remove-room-link").length)) {
				let hasErrors = 0;

				let adults = $(occupancy_wrapper).find(".num_adults").map(function(){return $(this).val();}).get();
				let children = $(occupancy_wrapper).find(".num_children").map(function(){return $(this).val();}).get();
				let child_ages = $(occupancy_wrapper).find(".guest_child_age").map(function(){return $(this).val();}).get();

				// start validating above values
				if (!adults.length || (adults.length != children.length)) {
					hasErrors = 1;
					showErrorMessage(invalid_occupancy_txt);
				} else {
					$(occupancy_wrapper).find('.num_occupancy').removeClass('alert-danger');

					// validate values of adults and children
					adults.forEach(function (item, index) {
						if (item == '' || isNaN(item) || parseInt(item) < 1) {
							hasErrors = 1;
							$(occupancy_wrapper).find(".num_adults").eq(index).closest('.occupancy_count_block').find('.num_adults').addClass('alert-danger');
						}

						if (children[index] == '' || isNaN(children[index])) {
							hasErrors = 1;
							$(occupancy_wrapper).find(".num_children").eq(index).closest('.occupancy_count_block').find('.num_children').addClass('alert-danger');
						}
					});

					// validate values of selected child ages
					$(occupancy_wrapper).find('.guest_child_age').removeClass('alert-danger');
					child_ages.forEach(function (age, index) {
						age = parseInt(age);
						if (isNaN(age) || (age < 0) || (age >= parseInt(max_child_age))) {
							hasErrors = 1;
							$(occupancy_wrapper).find(".guest_child_age").eq(index).addClass('alert-danger');
						}
					});
				}
				if (hasErrors == 0) {
                    // Close the occupancy block only if click element is not occupancy block OR close link of the occupancy block
                    if (!($(e.target).closest(".booking_occupancy_wrapper").length) || $(e.target).hasClass("close_occupancy_link")) {
                        $(occupancy_wrapper).parent().removeClass('open');
                        $(document).trigger( "QloApps:updateRoomOccupancy", [occupancy_wrapper]);
                    }
				}
			}
		}
	});

    // Close occupancy link default redirect action prevent
    $(document).on('click', '.close_occupancy_link', function(e) {
        e.preventDefault();
    });

    $(document).on('click', '.booking_occupancy_wrapper .add_new_occupancy_btn', function(e) {
		e.preventDefault();

		var booking_occupancy_wrapper = $(this).closest('.booking_occupancy_wrapper');
		var roomBlockIndex = parseInt($(booking_occupancy_wrapper).find(".occupancy_info_block").last().attr('occ_block_index'));
		roomBlockIndex += 1;
		var countRooms = parseInt($(booking_occupancy_wrapper).find('.occupancy_info_block').length);
		countRooms += 1

		var occupancy_block = '';
		occupancy_block += '<div class="occupancy_info_block col-sm-12" occ_block_index="'+roomBlockIndex+'">';
            occupancy_block += '<hr class="occupancy-info-separator col-sm-12">';
			occupancy_block += '<div class="occupancy_info_head col-sm-12"><label class="room_num_wrapper">'+ room_txt + ' - ' + countRooms + '</label><a class="remove-room-link pull-right" href="#">' + remove_txt + '</a></div>';
			occupancy_block += '<div class="col-sm-12">';
				occupancy_block += '<div class="row">';
					occupancy_block += '<div class="form-group col-xs-6 occupancy_count_block">';
						occupancy_block += '<label>' + adults_txt + '</label>';
						occupancy_block += '<input type="number" class="form-control num_occupancy num_adults" name="occupancy['+roomBlockIndex+'][adults]" value="1" min="1">';
					occupancy_block += '</div>';
					occupancy_block += '<div class="form-group col-xs-6 occupancy_count_block">';
						occupancy_block += '<label>' + child_txt + '<span class="label-desc-txt"></span></label>';
						occupancy_block += '<input type="number" class="form-control num_occupancy num_children" name="occupancy['+roomBlockIndex+'][children]" value="0" min="0">(' + below_txt + ' ' + max_child_age + ' ' + years_txt + ')';
					occupancy_block += '</div>';
				occupancy_block += '</div>';
				occupancy_block += '<div class="row children_age_info_block"  style="display:none">';
					occupancy_block += '<div class="form-group col-sm-12">';
						occupancy_block += '<label class="">' + all_children_txt + '</label>';
						occupancy_block += '<div class="">';
							occupancy_block += '<div class="row children_ages">';
							occupancy_block += '</div>';
						occupancy_block += '</div>';
					occupancy_block += '</div>';
				occupancy_block += '</div>';
			occupancy_block += '</div>';
		occupancy_block += '</div>';
		$(booking_occupancy_wrapper).find('.booking_occupancy_inner').append(occupancy_block);

		setRoomTypeGuestOccupancy(booking_occupancy_wrapper);
	});

	$('input#add_product_product_quantity').on('change', function(){
		// updateOccupancyInput()
		addProductRefreshTotal();
	});

	$(document).on('click', '.booking_occupancy_wrapper .remove-room-link', function(e) {
		e.preventDefault();

		booking_occupancy_inner = $(this).closest('.booking_occupancy_inner');
		$(this).closest('.occupancy_info_block').remove();
		$(booking_occupancy_inner).find('.room_num_wrapper').each(function(key, val) {
			$(this).text(room_txt + ' - '+ (key+1) );
		});
		setRoomTypeGuestOccupancy($(booking_occupancy_inner).closest('.booking_occupancy_wrapper'));
	});

    $('#submitAddNormalProduct').unbind('click').click(function(e) {
		e.preventDefault();
		stopAjaxQuery();
		var go = true;

		if ($('input#add_normal_product_product_id').val() == 0)
		{
			jAlert(txt_add_product_no_product);
			go = false;
		}

		if ($('input#add_normal_product_quantity').val() == 0)
		{
			jAlert(txt_add_product_no_product_quantity);
			go = false;
		}

		if ($('input#add_normal_product_price_tax_excl').val() == 0)
		{
			jAlert(txt_add_product_no_product_price);
			go = false;
		}

		if (go)
		{
			if (parseInt($('input#add_normal_product_quantity').val()) > parseInt($('#add_normal_product_stock').html()))
				go = confirm(txt_add_product_stock_issue);

			if (go && $('select#add_normal_product_invoice').val() == 0)
				go = confirm(txt_add_product_new_invoice);

			if (go)
			{
				$('#submitAddNormalProduct').attr('disabled', true);
				var query = 'ajax=1&token='+token+'&action=addServiceProductOnOrder&id_order='+id_order+'&';

				// query += $('#add_product_warehouse').serialize()+'&';
				query += $('tr#new_normal_product select, tr#new_normal_product input').serialize();
				if ($('select#add_normal_product_invoice').val() == 0)
					query += '&'+$('tr#new_invoice select, tr#new_invoice input').serialize();

				var ajax_query = $.ajax({
					type: 'POST',
					url: admin_order_tab_link,
					cache: false,
					dataType: 'json',
					data : query,
					success : function(data) {
						if (data.result)
						{
							if (data.refresh)
							{
								location.reload();
								return;
							}
							go = false;
							//commented by webkul
							/*updateAmounts(data.order);
							updateInvoice(data.invoices);
							updateDocuments(data.documents_html);
							updateShipping(data.shipping_html);*/

							// Initialize all events
							init();
							//Added by webkul
							location.reload();
							//End
							// $('.partial_refund_fields').hide();
						}
						else
							jAlert(data.error);
					},
					error : function(XMLHttpRequest, textStatus, errorThrown) {
						jAlert("Impossible to add the room to the cart.\n\ntextStatus: '" + textStatus + "'\nerrorThrown: '" + errorThrown + "'\nresponseText:\n" + XMLHttpRequest.responseText);
					},
					complete: function() {
						$('#submitAddNormalProduct').removeAttr('disabled');
					}
				});
				ajaxQueries.push(ajax_query);
			}
		}
	});

    $('.edit_product_change_link').unbind('click').click(function(e) {
		$('.add_product_fields').hide();
		$('.edit_product_fields').show();
		$('.row-editing-warning').hide();
		$('.cancel_product_change_link:visible').trigger('click');
		var element = $(this);
		$.ajax({
			type: 'POST',
			url: admin_order_tab_link,
			cache: false,
			dataType: 'json',
			data : {
				ajax: 1,
				token: token,
				action: 'loadProductInformation',
				id_order_detail: element.closest('tr.product-line-row').data('id_order_detail'),
				id_address: id_address,
				id_order: id_order
			},
			success : function(data)
			{
				if (data.result)
				{
					current_product = data;

					var element_list = $('.customized-' + element.parents('.product-line-row').find('.edit_product_id_order_detail').val());
					if (!element_list.length)
					{
						element_list = element.parents('.product-line-row');
						element_list.find('td .product_quantity_show').hide();
						element_list.find('td .product_quantity_edit').show();
					}
					else
					{
						element_list.find('td .product_quantity_show').hide();
						element_list.find('td .product_quantity_edit').show();
					}
					element_list.find('td .product_price_show').hide();
					element_list.find('td .product_price_edit').show();
					element_list.find('td.cancelCheck').hide();
					element_list.find('td.cancelQuantity').hide();
					element_list.find('td.product_invoice').show();
					$('td.product_action').attr('colspan', 3);
					$('th.edit_product_fields').show();
					$('th.edit_product_fields').attr('colspan',  2);
					element_list.find('td.product_action').attr('colspan', 1);
					element.parent().children('.edit_product_change_link').parent().hide();
					element.parent().parent().find('button.submitProductChange').show();
					element.parent().parent().find('.cancel_product_change_link').show();

					if (+data.reduction_percent != +0)
						element_list.find('.row-editing-warning').show();

					$('.partial_refund_fields').hide();
				}
				else
					jAlert(data.error);
			}
		});
		e.preventDefault();
	});

	$('.cancel_product_change_link').unbind('click').click(function(e)
	{
		current_product = null;
		$('.edit_product_fields').hide();
		$('.row-editing-warning').hide();
		var element_list = $('.customized-' + $(this).parent().parent().find('.edit_product_id_order_detail').val());
		if (!element_list.length)
			element_list = $($(this).parent().parent());
		element_list.find('td .product_price_show').show();
		element_list.find('td .product_quantity_show').show();
		element_list.find('td .product_price_edit').hide();
		element_list.find('td .product_quantity_edit').hide();
		element_list.find('td.product_invoice').hide();
		element_list.find('td.cancelCheck').show();
		element_list.find('td.cancelQuantity').show();
		element_list.find('.edit_product_change_link').parent().show();
		element_list.find('button.submitProductChange').hide();
		element_list.find('.cancel_product_change_link').hide();
		e.preventDefault();
	});

    $('.edit_product_price_tax_excl').unbind('keyup').keyup(function() {
		var price_tax_excl = parseFloat($(this).val());
		if (price_tax_excl < 0 || isNaN(price_tax_excl))
			price_tax_excl = 0;
		var tax_rate = current_product.tax_rate / 100 + 1;
		$('.edit_product_price_tax_incl:visible').val(ps_round(price_tax_excl * tax_rate, 2));
		// Update total product
		editProductRefreshTotal($(this));
	});

	$('.edit_product_price_tax_incl').unbind('keyup');
	$('.edit_product_price_tax_incl').keyup(function() {
		var price_tax_incl = parseFloat($(this).val());
		if (price_tax_incl < 0 || isNaN(price_tax_incl))
			price_tax_incl = 0;

		var tax_rate = current_product.tax_rate / 100 + 1;
		$('.edit_product_price_tax_excl:visible').val(ps_round(price_tax_incl / tax_rate, 2));
		// Update total product
		editProductRefreshTotal($(this));
	});

	$('.edit_product_quantity').unbind('keyup');
	$('.edit_product_quantity').keyup(function() {
		var quantity = parseInt($(this).val());
		if (quantity < 1 || isNaN(quantity))
			quantity = 1;
		var stock_available = parseInt($(this).parent().parent().parent().find('td.product_stock').html());
		// total update
		editProductRefreshTotal($(this));
	});

    $('button.submitProductChange').unbind('click').click(function(e) {
		e.preventDefault();

		if ($(this).closest('tr.product-line-row').find('td .edit_product_quantity').val() <= 0)
		{
			jAlert(txt_add_product_no_product_quantity);
			return false;
		}
		if ($(this).closest('tr.product-line-row').find('td .edit_product_price').val() <= 0)
		{
			jAlert(txt_add_product_no_product_price);
			return false;
		}
		if (confirm(txt_confirm))
		{
			var element = $(this);
			var id_order_detail = element.closest('tr.product-line-row').data('id_order_detail');
			var tr_product = $(this).closest('tr.product-line-row');

			query = 'ajax=1&token='+token+'&action=editProductOnOrder&id_order='+id_order+'&product_id_order_detail='+id_order_detail+'&';
			query += tr_product.find('input:visible, select:visible').serialize();
			$.ajax({
				type: 'POST',
				url: admin_order_tab_link,
				cache: false,
				dataType: 'json',
				data : query,
				success : function(data)
				{
					if (data.result)
					{
						// refreshProductLineView(element, data.view);
						// updateAmounts(data.order);
						// updateInvoice(data.invoices);
						// updateDocuments(data.documents_html);

						// Initialize all events
						init();
						location.reload();

						// $('.partial_refund_fields').hide();
						// $('.add_product_fields').hide();
						// $('.row-editing-warning').hide();
						// $('td.product_action').attr('colspan', 3);
					}
					else
						jAlert(data.error);
				}
			});
		}

		return false;
	});

    $('.delete_product_line').unbind('click').click(function(e) {
		if (!confirm(txt_confirm))
			return false;
		var tr_product = $(this).closest('.product-line-row');
		var id_order_detail = $(this).closest('.product-line-row').data('id_order_detail');
		var query = 'ajax=1&action=deleteProductLine&token='+token+'&id_order_detail='+id_order_detail+'&id_order='+id_order;

		$.ajax({
			type: 'POST',
			url: admin_order_tab_link,
			cache: false,
			dataType: 'json',
			data : query,
			success : function(data)
			{
				if (data.result)
				{
					tr_product.fadeOut('slow', function() {
						$(this).remove();
					});
					updateAmounts(data.order);
					updateInvoice(data.invoices);
					updateDocuments(data.documents_html);
				}
				else
					jAlert(data.error);
			}
		});
		e.preventDefault();
	});

	function addProductRefreshTotal()
	{
		var quantity = parseInt($('#add_product_product_quantity').val());
		if (quantity < 1|| isNaN(quantity))
			quantity = 1;
		if (use_taxes)
			var price = parseFloat($('#add_product_product_price_tax_incl').val());
		else
			var price = parseFloat($('#add_product_product_price_tax_excl').val());

		if (price < 0 || isNaN(price))
			price = 0;
		var total = makeTotalProductCaculation(quantity, price);
		$('#add_normal_product_total').html(formatCurrency(total, currency_format, currency_sign, currency_blank));
	}
}

function initRoomEvents()
{
	$('#cancelAddProduct').unbind('click').click(function() {
		if (!($('#customer_products_details tbody tr').length > 1)) {
			$('#customer_products_details').hide();
		}
	});

	$('input#add_normal_product_quantity').unbind('keyup').keyup(function() {
		if (stock_management)
		{
			var quantity = parseInt($(this).val());
			if (quantity < 1 || isNaN(quantity))
				quantity = 1;
			var stock_available = parseInt($('#add_normal_product_stock').html());
			// stock status update
			if (quantity > stock_available)
				$('#add_normal_product_stock').css('font-weight', 'bold').css('color', 'red').css('font-size', '1.2em');
			else
				$('#add_normal_product_stock').css('font-weight', 'normal').css('color', 'black').css('font-size', '1em');
		}
		// total update
		addProductRefreshTotal();
	});

	$(document).on('click', '#submitAddProduct', function(e) {
		e.preventDefault();
		stopAjaxQuery();
		var go = true;

		if ($('input#add_product_product_id').val() == 0)
		{
			jAlert(txt_add_product_no_product);
			go = false;
		}

		if ($('input#add_product_product_quantity').val() == 0)
		{
			jAlert(txt_add_product_no_product_quantity);
			go = false;
		}

		if ($('input#add_product_product_price_excl').val() == 0)
		{
			jAlert(txt_add_product_no_product_price);
			go = false;
		}

		if (go)
		{
			if (parseInt($('input#add_product_product_quantity').val()) > parseInt($('#add_product_product_stock').html()))
				go = confirm(txt_add_product_stock_issue);

			if (go && $('select#add_product_product_invoice').val() == 0)
				go = confirm(txt_add_product_new_invoice);

			if (go)
			{
                $('.submitAddRoom').attr('disabled', true);
				$('#submitAddProduct').attr('disabled', true);
				var query = 'ajax=1&token='+token+'&action=addProductOnOrder&id_order='+id_order+'&';

				// query += $('#add_product_warehouse').serialize()+'&';
				query += $('#new_product select, #new_product input').serialize();
				if ($('select#add_product_product_invoice').val() == 0)
					query += '&'+$('tr#new_invoice select, tr#new_invoice input').serialize();

				var ajax_query = $.ajax({
					type: 'POST',
					url: admin_order_tab_link,
					cache: false,
					dataType: 'json',
					data : query,
					success : function(data) {
						if (data.result)
						{
							if (data.refresh)
							{
								location.reload();
								return;
							}
							go = false;
							//commented by webkul
							/*updateAmounts(data.order);
							updateInvoice(data.invoices);
							updateDocuments(data.documents_html);
							updateShipping(data.shipping_html);*/

							// Initialize all events
							init();
							//Added by webkul
							location.reload();
							//End
							// $('.partial_refund_fields').hide();
						}
						else
							jAlert(data.error);
					},
					error : function(XMLHttpRequest, textStatus, errorThrown) {
						jAlert("Impossible to add the room to the cart.\n\ntextStatus: '" + textStatus + "'\nerrorThrown: '" + errorThrown + "'\nresponseText:\n" + XMLHttpRequest.responseText);
					},
					complete: function() {
                        $('.submitAddRoom').removeAttr('disabled');
						$('#submitAddProduct').removeAttr('disabled');
					}
				});
				ajaxQueries.push(ajax_query);
			}
		}
	});

    $('.delete_room_line').unbind('click').click(function(e) {
		if (!confirm(txt_confirm))
			return false;
		var tr_product = $(this).closest('.product-line-row');
		var id_room = tr_product.data('id_room');
		var id_product = tr_product.data('id_product');
		var id_hotel = tr_product.data('id_hotel');
		var date_from = tr_product.data('date_from');
		var date_to = tr_product.data('date_to');
		var id_order_detail = tr_product.data('id_order_detail');
		//var id_order_detail = $(this).closest('.product-line-row').find('td .edit_product_id_order_detail').val();
		var query = 'ajax=1&action=deleteRoomLine&token='+token+'&id_order='+id_order+'&id_room='+id_room+'&id_product='+id_product+'&id_hotel='+id_hotel+'&date_from='+date_from+'&date_to='+date_to+'&id_order_detail='+id_order_detail;
		query += $(this).parent().parent().find('input, select:visible, .edit_product_id_order_detail').serialize();
		$.ajax({
			type: 'POST',
			url: admin_order_tab_link,
			cache: false,
			dataType: 'json',
			data : query,
			success : function(data)
			{
				if (data.result)
				{
					tr_product.fadeOut('slow', function() {
						$(this).remove();
					});
					updateAmounts(data.order);
					updateInvoice(data.invoices);
					updateDocuments(data.documents_html);
					location.reload();
				}
				else
					jAlert(data.error);
			}
		});
		e.preventDefault();
	});

	$('#add_new_payment').on('click', function(e) {
		e.preventDefault();
		OrderPaymentModal.show();
	});

	$('#cancle_add_payment').on('click', function(e) {
		e.preventDefault();
		OrderPaymentModal.hide();
	});
}
function addRoomRefreshTotal() {
	var quantity = parseInt($('#add_product_product_quantity').val());
	if (quantity < 1|| isNaN(quantity))
		quantity = 1;
	if (use_taxes)
		var price = parseFloat($('#add_product_product_price_tax_incl').val());
	else
		var price = parseFloat($('#add_product_product_price_tax_excl').val());

	if (price < 0 || isNaN(price))
		price = 0;
	var total = makeTotalProductCaculation(quantity, price);
	$('#add_product_product_total').html(formatCurrency(total, currency_format, currency_sign, currency_blank));
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
    if (parseInt(rooms) > 1) {
        guestButtonVal += ', ' + parseInt(rooms) + ' ' + rooms_txt;
    }

    $(booking_occupancy_wrapper).siblings('.booking_guest_occupancy').find('span').text(guestButtonVal);
}

/* Refund system script */
var flagRefund = '';

$(document).ready(function() {
	$('#desc-order-partial_refund').click(function() {
		$('.cancel_product_change_link:visible').trigger('click');
		if (flagRefund == 'partial') {
			flagRefund = '';
			$('.partial_refund_fields').hide();
		}
		else {
			flagRefund = 'partial';
			$('.product_action').hide();
			$('.product_action').hide();
			$('.partial_refund_fields').fadeIn();
		}

		if (order_discount_price)
			actualizeRefundVoucher();
	});

    // when choose to add new facilities while additional facilities edit
    $(document).on('click', '#btn_new_room_demand', function() {
        $('.room_demands_container').show();
        $('#save_room_demands').show();
        $('#back_to_demands_btn').show();
        $('.room_ordered_demands').hide();
        $('#btn_new_room_demand').hide();
    });
    // click on back button on created facilities while additional facilities edit
    $(document).on('click', '#back_to_demands_btn', function() {
        $('.room_ordered_demands').show();
        $('#btn_new_room_demand').show();
        $('.room_demands_container').hide();
        $('#save_room_demands').hide();
        $('#back_to_demands_btn').hide();
    });

    $(document).on('click', '#btn_new_room_service', function() {
        $('.room_services_container').show();
        $('#save_service_service').show();
        $('#back_to_service_btn').show();
        $('.room_ordered_services').hide();
        $('#btn_new_room_service').hide();
    });
    // click on back button on created facilities while additional facilities edit
    $(document).on('click', '#back_to_service_btn', function() {
        $('.room_ordered_services').show();
        $('#btn_new_room_service').show();
        $('.room_services_container').hide();
        $('#save_service_service').hide();
        $('#back_to_service_btn').hide();
    });

    $(document).on('change', '#edit-room-booking-modal .room_ordered_services .qty', function(e) {
        let quantityInputField = this;
        let maximumQuantity = parseInt($(quantityInputField).attr('data-max-quantity'));
        let currentQuantity = parseInt($(quantityInputField).val());
        if (currentQuantity > maximumQuantity) {
            $(quantityInputField).siblings('p').show();
        } else {
            $(quantityInputField).siblings('p').hide();
        }
    });

    $(document).on('focusout', '#edit-room-booking-modal .room_ordered_services .qty', function(e) {
        updateAdditionalServices($(this).closest('tr'));
    });

    $(document).on('focusout', '#edit-room-booking-modal .room_ordered_services .unit_price', function(e) {
        updateAdditionalServices($(this).closest('tr'));
    });

    $(document).on('focusout', '#edit-room-booking-modal #add_room_services_form .qty', function(e) {
        var qty_wntd = $(this).val();
        if (qty_wntd == '' || !$.isNumeric(qty_wntd) || qty_wntd < 1) {
            $(this).val(1);
        }
    });

    $(document).on('submit', '#add_room_services_form', function(e) {
        e.preventDefault();
        var form_data = new FormData(this);
        form_data.append('ajax', true);
        form_data.append('action', 'addRoomAdditionalServices');

        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: admin_order_tab_link,
            dataType: 'JSON',
            cache: false,
            data: form_data,
            processData: false,
            contentType: false,
            success: function(jsonData) {
                if (!jsonData.hasError) {
                    if (jsonData.service_panel) {
                        $('#room_type_service_product_desc').replaceWith(jsonData.service_panel);
                    }
                    showSuccessMessage(txtExtraDemandSucc);
                } else {
                    showErrorMessage(jsonData.errors);

                }
            }
        });
    });

    // save room extra demand to the order
    $(document).on('click', '#save_room_demands', function(e) {
        e.preventDefault();

        var idHtlBooking = parseInt($('#edit_product .extra-services-container #id_htl_booking').val());
        if (idHtlBooking) {
            var roomDemands = [];
            // get the selected extra demands by customer
            $(this).closest('#edit_product #room_type_demands_desc').find('input:checkbox.id_room_type_demand:checked').each(function () {
                roomDemands.push({
                    'id_global_demand':$(this).val(),
                    'id_option': $(this).closest('.room_demand_block').find('.id_option').val(),
                    'unit_price': $(this).closest('.room_demand_block').find('.unit_price').val()
                });
            });

            if (roomDemands.length) {
                $.ajax({
                    type: 'POST',
                    headers: {
                        "cache-control": "no-cache"
                    },
                    url: admin_order_tab_link,
                    dataType: 'JSON',
                    cache: false,
                    data: {
                        id_htl_booking: idHtlBooking,
                        room_demands: JSON.stringify(roomDemands),
                        action: 'addRoomExtraDemands',
                        ajax: true
                    },
                    success: function(jsonData) {
                        if (!jsonData.hasError) {
                            showSuccessMessage(txtExtraDemandSucc);
                            if (jsonData.facilities_panel) {
                                $('#room_type_demands_desc').replaceWith(jsonData.facilities_panel);
                            }
                        } else if (jsonData.errors) {
                            showErrorMessage(jsonData.errors);
                        } else {
                            showErrorMessage(txtSomeErr);
                        }
                    }
                });
            } else {
                showErrorMessage(atleastSelectTxt);
            }
        }
    });

    // edit room extra deman
    $(document).on('focusout', '#edit-room-booking-modal .room_ordered_demands .unit_price', function(e) {
        updateRoomDemand($(this).closest('tr'));
    });

    // Delete ordered room booking demand
    $(document).on('click', '.del-order-room-demand', function(e) {
        e.preventDefault();
        if (confirm(txt_confirm)) {
            var idBookingDemand = $(this).attr('id_booking_demand');
            $currentItem = $(this);
            if (idBookingDemand) {
                $.ajax({
                    type: 'POST',
                    headers: {
                        "cache-control": "no-cache"
                    },
                    url: admin_order_tab_link,
                    dataType: 'JSON',
                    cache: false,
                    data: {
                        id_booking_demand: idBookingDemand,
                        action: 'DeleteRoomExtraDemand',
                        ajax: true
                    },
                    success: function(jsonData) {
                        if (jsonData.success) {
                            showSuccessMessage(txtDeleteSucc);
                            if (jsonData.facilities_panel) {
                                $('#room_type_demands_desc').replaceWith(jsonData.facilities_panel);
                            }
                        } else {
                            showErrorMessage(txtSomeErr);
                        }
                    }
                });
            } else {
                showErrorMessage(txtInvalidDemandVal);
            }
        }
    });

    $(document).on('click', '.del_room_additional_service', function(e){
        e.preventDefault();
        if (confirm(txt_confirm)) {
            var idServiceProductOrderDetail = $(this).data('id_room_type_service_product_order_detail');
            $currentItem = $(this);
            if (idServiceProductOrderDetail) {
                $.ajax({
                    type: 'POST',
                    headers: {
                        "cache-control": "no-cache"
                    },
                    url: admin_order_tab_link,
                    dataType: 'JSON',
                    cache: false,
                    data: {
                        id_room_type_service_product_order_detail: idServiceProductOrderDetail,
                        action: 'DeleteRoomAdditionalService',
                        ajax: true
                    },
                    success: function(jsonData) {
                        if (!jsonData.hasError) {
                            if (jsonData.service_panel) {
                                $('#room_type_service_product_desc').replaceWith(jsonData.service_panel);
                            }
                            showSuccessMessage(txtExtraDemandSucc);
                        } else {
                            showErrorMessage(jsonData.errors);

                        }
                    }
                });
            } else {
                showErrorMessage(txtInvalidDemandVal);
            }
        }

    });

    // change advance option of extra demand
    $(document).on('change', '.demand_adv_option_block .id_option', function(e) {
        var option_selected = $(this).find('option:selected');
        var extra_demand_price = option_selected.attr("optionPrice")
        extra_demand_price = parseFloat(extra_demand_price);
        // extra_demand_price = formatCurrency(extra_demand_price, currency_format, currency_sign, currency_blank);
        $(this).closest('.room_demand_block').find('.unit_price').val(extra_demand_price);
    });

    $(".textarea-autosize").autosize();

    var date = new Date();
    var hours = date.getHours();
    if (hours < 10)
        hours = "0" + hours;
    var mins = date.getMinutes();
    if (mins < 10)
        mins = "0" + mins;
    var secs = date.getSeconds();
    if (secs < 10)
        secs = "0" + secs;

    $('.datepicker').datetimepicker({
        prevText: '',
        nextText: '',
        dateFormat: 'yy-mm-dd',
        timeFormat: 'hh:mm:ss',
    });

    // open modal to show extra services of the rooms
    $(document).on('click', '.open_room_extra_services', function(e) {
        e.preventDefault();
        var idProduct = $(this).attr('id_product');
        var idOrder = $(this).attr('id_order');
        var idRoom = $(this).attr('id_room');
        var dateFrom = $(this).attr('date_from');
        var dateTo = $(this).attr('date_to');
        var idHtlBooking = $(this).attr('id_htl_booking');
        var orderEdit = 0;

        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: admin_order_tab_link,
            dataType: 'JSON',
            cache: false,
            data: {
                id_room: idRoom,
                id_product: idProduct,
                id_order: idOrder,
                date_from: dateFrom,
                date_to: dateTo,
                orderEdit: orderEdit,
                action: 'getRoomTypeBookingDemands',
                ajax: true
            },
            success: function(result) {
                if (result.hasError == 1) {
                    showErrorMessage(txtSomeErr);
                } else {
                    $('#footer').next('.bootstrap').append(result.modalHtml);
                    $('#room-extra-demands').modal('show');
                }
            },
        });
    });

    $(document).on('click', '.reallocate_overbooking', function(e) {
        e.preventDefault();
        $('#reallocate_room_' + $(this).attr('id_htl_booking')).trigger('click');
    });

    // BookingDocumentsModal : Room Booking Document processing
    $(document).on('click', '#booking-documents-modal .btn-add-new-document', function() {
        BookingDocumentsModal.addNew();
        BookingDocumentsForm.addNew();
    });

    $(document).on('click', '#booking-documents-modal [data-dismiss="alert"]', function() {
        BookingDocumentsModal.hideErrors();
    });

    $(document).on('change', '.input-booking-document', function(e) {
        e.preventDefault();

        if (!this.files.length) {
            $(this).remove();
            return;
        }

        BookingDocumentsForm.updatePreview();
    });

    $(document).on('click', '#form-add-new-document .btn-add-file', function(e) {
        e.preventDefault();

        BookingDocumentsForm.openFileChooser();
    });

    $(document).on('click', '#form-add-new-document .btn-group-add-new .cancel', function(e) {
        e.preventDefault();

        BookingDocumentsForm.close();
        BookingDocumentsModal.enableAddNewButton();
    });

    $(document).on('click', '.submitDocument', function(e) {
        e.preventDefault();
        BookingDocumentsModal.uploadDocument();
    });

    $(document).on('click', '#booking-documents-modal .documents-list .btn-delete-document', function(e) {
        e.preventDefault();

        if (confirm(txt_booking_document_delete_confirm)) {
            BookingDocumentsModal.deleteDocument(this);
        }
    });

    // Reload when modal is closed to update the number of documents
    $(document).on('hidden.bs.modal', '#booking-documents-modal', function(){
        location.reload();
    });
    // END: BookingDocumentsModal: Processes
    // ======================================

    // Start: VoucherModal: Processes

    $(document).on('click', '.submitVoucher', function(e) {
        e.preventDefault();
        VoucherModal.submit();
    });
    // End: VoucherModal: Processes
    // ======================================

    // Start: OrderPaymentModal: Processes
    $(document).on('click', '.submitOrderPayment', function(e) {
        e.preventDefault();
        OrderPaymentModal.submit();
    });
    // End: OrderPaymentModal: Processes
    // ======================================

    // Start: DocumentNoteModal: Processes
    $(document).on('click', '.add_document_note', function(e) {
        e.preventDefault();
        DocumentNoteModal.show($(this));
    });

    $(document).on('click', '.submitDocumentNote', function(e) {
        e.preventDefault();
        DocumentNoteModal.submit();
    });
    // End: DocumentNoteModal: Processes
    // ======================================

    // Start: TravellerModal: Processes
    // for updating (Traveller) customer guest details
    $(document).on('click', '#edit_guest_details', function(e) {
        e.preventDefault();
        TravellerModal.show();
    });

    $(document).on('click', '.submitTravellerInfo', function(e) {
        e.preventDefault();
        TravellerModal.submit();
    });
    // End: TravellerModal: Processes
    // ======================================

    // Start: RoomStatusModal: Processes
    // for updating Room status
    // toggle date input of check-in checkout dates as per status selected
    $(document).on('change', '.booking_order_status', function() {
        var status = $(this).val();
        if (status == ROOM_STATUS_CHECKED_IN || status == ROOM_STATUS_CHECKED_OUT) {
            $(this).closest('.room_status_info_form').find('.room_status_date').closest('.form-group').show();
        } else {
            $(this).closest('.room_status_info_form').find('.room_status_date').closest('.form-group').hide();
        }
    });

    // open date picker for the date input of check-in checkout dates
    $(document).on('focus', '.room_status_date', function() {
        var dateFrom = $(this).closest('.room_status_info_form').find('[name="date_from"]').val();
        dateFrom = dateFrom.split("-");
        minDate = new Date(dateFrom+'T00:00:00');

        var dateTo = $(this).closest('.room_status_info_form').find('[name="date_to"]').val();
        dateTo = dateTo.split("-");
        maxDate = new Date(dateTo+'T23:59:59');

        $(this).datetimepicker({
            dateFormat: 'dd-mm-yy',
            minDate: minDate,
            maxDate: maxDate,
            dayNamesMin: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
        });
    });

    $(document).on('click', '.open_room_status_form', function(e) {
        e.preventDefault();
        RoomStatusModal.show($(this));
    });

    $(document).on('click', '.submitRoomStatus', function(e) {
        e.preventDefault();
        RoomStatusModal.submit();
    });
    // End: RoomStatusModal: Processes
    // ======================================

    // Start: RoomReallocationModal: Processes
    // For processing room reallocation and swapping
    $(document).on('click', '.room_reallocate_swap', function(e) {
        e.preventDefault();
        RoomReallocationModal.show($(this));
    });

    $(document).on('hidden.bs.modal', '#room-reallocation-modal', function(){
        location.reload();
    });

    /*For reallocating rooms in the modal*/
    $(document).on('click', '#realloc_allocated_rooms', function(){
        RoomReallocationModal.reallocate();
    });
    /*For swaping rooms in the modal*/
    $(document).on('click', '#swap_allocated_rooms', function(){
        RoomReallocationModal.swap();
    });

    // change room type for reallocation
    $(document).on("change", "#realloc_avail_room_type", function(e) {
        e.preventDefault();
        RoomReallocationModal.changeRoomType($(this));
    });
    // End: RoomReallocationModal: Processes
    // ======================================

    // Start: AddRoomBookingModal: Processes
    /*For adding rooms to the order*/
    $(document).on('click', '#add_room', function(e){
        e.preventDefault();
        AddRoomBookingModal.show();
    });

    $(document).on('click', '.submitAddRoom', function(e){
        e.preventDefault();
        AddRoomBookingModal.submit();
    });

    $(document).on('shown.bs.modal', '#add-room-booking-modal', function(){
        $('#new_product #add_product_product_name').focus();
    });
    $(document).on('show.bs.modal', '#add-room-booking-modal', function(){
        if ($('#new_product #add_product_product_id').val() == 0) {
            $('.submitAddRoom').attr('disabled', true);
        }
    });
    // End: AddRoomBookingModal: Processes
    // ======================================

    // Start: EditRoomBookingModal: Processes
    $(document).on('click', '.edit_room_change_link', function(e){
        e.preventDefault();

        EditRoomBookingModal.show(this);
    });

    // submit room edit
    $(document).on('click', '.submitRoomChange', function(e) {
        e.preventDefault();

        if (confirm(txt_confirm)) {
            let query = 'ajax=1&token='+token+'&action=editRoomOnOrder&'+
            $('#edit_product').find('input, select').serialize();

            $.ajax({
                type: 'POST',
                url: admin_order_tab_link,
                cache: false,
                dataType: 'json',
                data : query,
                success : function(data) {
                    if (data.result) {
                        init();
                        location.reload();
                    } else {
                        jAlert(data.error);
                    }
                }
            });
        }

        return false;
    });

    $(document).on('hidden.bs.modal', '#edit-room-booking-modal', function(){
        location.reload();
    });
    // End: EditRoomBookingModal: Processes
    // ======================================

    // Start: CancelRoomBookingModal: Processes
    $(document).on('click', '#page-header-desc-order-cancel', function(e) {
        e.preventDefault();
        CancelRoomBookingModal.show();
    });

    $(document).on('click', '.submitCancelBooking', function(e) {
        e.preventDefault();
        CancelRoomBookingModal.submit();
    });

    $(document).on('click', '#initiateRefund', function(e) {
        if ($.trim($('.cancellation_reason').val()) == '') {
            $('.cancellation_reason').focus().css('border', '1px solid red');
            return false;
        }
    });
    // End: CancelRoomBookingModal: Processes
    // ======================================
});

const BookingDocumentsModal = {
    init: function(idHtlBooking, $this) {
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: admin_order_tab_link,
            dataType: 'JSON',
            cache: false,
            data: 'ajax=true&id_order='+id_order+'&action=InitBookingDocumentsModal',
            success: function(result) {
                if (result.hasError == 0 && result.modalHtml) {
                    $('#footer').next('.bootstrap').append(result.modalHtml);

                    BookingDocumentsModal.currentTr = $this;
                    $('#booking-documents-modal .documents-list').find('[name="id_hotel_booking"]').val(idHtlBooking);
                    BookingDocumentsForm.init();
                    BookingDocumentsModal.reset();
                    BookingDocumentsModal.show(idHtlBooking);
                } else {
                    showErrorMessage(txtSomeErr);
                }
            }
        });

    },
    reset: function() {
        $('#booking-documents-modal .documents-list table tbody').html('');
    },
    show: function(idHtlBooking) {
        $('#form-add-new-document').find('[name="id_htl_booking"]').attr('value', idHtlBooking);
        let data = {
            ajax: true,
            action: 'getBookingDocuments',
            id_htl_booking: parseInt(idHtlBooking),
        };

        $.ajax({
            url: admin_order_tab_link,
            data: data,
            type: 'POST',
            dataType: 'JSON',
            success: function(response) {
                if (response.status) {
                    BookingDocumentsModal.setBodyHtml(response.html);
                }
            },
        });

        $('#booking-documents-modal').modal('show');
    },
    setBodyHtml: function(html) {
        $('#booking-documents-modal .documents-list table tbody').html(html);
    },
    close: function() {
        $('#booking-documents-modal').modal('hide');
    },
    addNew: function() {
        BookingDocumentsModal.hideErrors();
        BookingDocumentsModal.hideAddNewButton();
    },
    beforeSubmit: function(cb) {
        BookingDocumentsModal.hideErrors(cb);
    },
    uploadDocument: function() {
        BookingDocumentsModal.beforeSubmit(function() {
            let formData = new FormData($('form#form-add-new-document').get(0));
            formData.append('ajax', true);
            formData.append('action', 'uploadBookingDocument');
            $.ajax({
                url: admin_order_tab_link,
                data: formData,
                processData: false,
                contentType: false,
                type: 'POST',
                success: function(response) {
                    let jsonResponse = JSON.parse(response);
                    if (jsonResponse.status) {
                        showSuccessMessage(txt_booking_document_upload_success);
                        BookingDocumentsModal.reset();
                        BookingDocumentsForm.reset();
                        BookingDocumentsForm.resetPreview();
                        BookingDocumentsModal.setBodyHtml(jsonResponse.html);
                        BookingDocumentsModal.setDocumentsCount(jsonResponse.num_checkin_documents);
                    } else {
                        BookingDocumentsModal.showErrors(jsonResponse.errors);
                    }
                },
            });
        });
    },
    deleteDocument: function($this) {
        BookingDocumentsModal.hideErrors();
        let idHtlBookingDocument = parseInt($($this).attr('data-id-htl-booking-document'));
        let data = {
            ajax: true,
            action: 'deleteBookingDocument',
            id_htl_booking_document: idHtlBookingDocument,
        };

        $.ajax({
            url: admin_order_tab_link,
            data: data,
            type: 'POST',
            dataType: 'JSON',
            success: function(response) {
                if (response.status) {
                    BookingDocumentsModal.setBodyHtml(response.html);
                    BookingDocumentsModal.setDocumentsCount(response.num_checkin_documents);
                    showSuccessMessage(txt_booking_document_delete_success);
                }
            },
        });
    },
    showErrors: function(errors) {
        $('#booking-documents-modal .errors-wrap').stop().html(errors);
        $('#booking-documents-modal .errors-wrap').show(200);
    },
    hideErrors: function(cb) {
        $('#booking-documents-modal .errors-wrap').hide(200, function() {
            $('#booking-documents-modal .errors-wrap').html('');
            if (typeof cb === 'function') {
                cb();
            }
        });
    },
    enableAddNewButton: function() {
        $('#booking-documents-modal .btn-add-new-document').show(200);
    },
    hideAddNewButton: function() {
        $('#booking-documents-modal .btn-add-new-document').hide();
    },
    setDocumentsCount: function(count) {
        $(BookingDocumentsModal.currentTr).find('.count-documents').html(count);
    },
}

const BookingDocumentsForm = {
    init: function() {
        BookingDocumentsForm.inputHtml = '<input type="file" accept="image/*, .pdf" class="input-booking-document hidden" name="booking_document">';
    },
    reset: function() {
        $('#form-add-new-document').get(0).reset();
    },
    resetPreview: function() {
        $('#form-add-new-document .file-name').val('');
    },
    updatePreview: function() {
        BookingDocumentsForm.resetPreview();
        let input = $('#form-add-new-document').find('.input-file-wrap input');
        let file = $(input).get(0).files[0];
        $('#form-add-new-document .file-name').val(file.name);
    },
    addNew: function() {
        $('#booking-documents-modal #form-add-new-document').show(200);
        $('#form-add-new-document').find('.input-file-wrap').html(BookingDocumentsForm.inputHtml);
    },
    openFileChooser: function() {
        $('#form-add-new-document').find('.input-file-wrap').html(BookingDocumentsForm.inputHtml);
        $('#form-add-new-document').find('.input-file-wrap input').click();
    },
    close: function() {
        $('#booking-documents-modal #form-add-new-document').hide(200);
    },
}

const VoucherModal = {
    show: function() {
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: admin_order_tab_link,
            dataType: 'JSON',
            cache: false,
            data: 'ajax=true&id_order='+id_order+'&action=InitVoucherModal',
            success: function(result) {
                if (result.hasError == 0 && result.modalHtml) {
                    $('#footer').next('.bootstrap').append(result.modalHtml);
                    $('#voucher-modal').modal('show');
                } else {
                    showErrorMessage(txtSomeErr);
                }
            }
        });
    },
    close: function() {
        $('#voucher-modal').modal('hide');
    },
    submit: function() {
        $(document).find('#submitNewVoucher').click();
    }
};

const OrderPaymentModal = {
    show: function() {
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: admin_order_tab_link,
            dataType: 'JSON',
            cache: false,
            data: 'ajax=true&id_order='+id_order+'&action=InitOrderPaymentModal',
            success: function(result) {
                if (result.hasError == 0 && result.modalHtml) {
                    $('#footer').next('.bootstrap').append(result.modalHtml);
                    $('#order-payment-modal').modal('show');
                } else {
                    showErrorMessage(txtSomeErr);
                }
            }
        });
    },
    close: function() {
        $('#order-payment-modal').modal('hide');
    },
    submit: function() {
        $(document).find('#submitAddPayment').click();
    }
};

const OrderPaymentDetailModal = {
    show: function(paymentObj) {
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: admin_order_tab_link,
            dataType: 'JSON',
            cache: false,
            data: 'ajax=true&id_order='+id_order+'&action=InitOrderPaymentDetailModal',
            success: function(result) {
                if (result.hasError == 0 && result.modalHtml) {
                    $('#footer').next('.bootstrap').append(result.modalHtml);

                    $('#payment-detail-modal #payment_date').html(paymentObj.data('payment_date'));
                    $('#payment-detail-modal #payment_method').html(paymentObj.data('payment_method'));
                    $('#payment-detail-modal #payment_source').html(paymentObj.data('payment_source'));
                    $('#payment-detail-modal #transaction_id').html(paymentObj.data('transaction_id'));
                    $('#payment-detail-modal #card_number').html(paymentObj.data('card_number'));
                    $('#payment-detail-modal #card_brand').html(paymentObj.data('card_brand'));
                    $('#payment-detail-modal #card_expiration').html(paymentObj.data('card_expiration'));
                    $('#payment-detail-modal #card_holder').html(paymentObj.data('card_holder'));
                    $('#payment-detail-modal #amount').html(paymentObj.data('amount'));
                    $('#payment-detail-modal #invoice_number').html(paymentObj.data('invoice_number'));

                    $('#payment-detail-modal').modal('show');
                } else {
                    showErrorMessage(txtSomeErr);
                }
            }
        });
    },
    close: function() {
        $('#payment-detail-modal').modal('hide');
    },
};

const DocumentNoteModal = {
    show: function(documentObj) {
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: admin_order_tab_link,
            dataType: 'JSON',
            cache: false,
            data: 'ajax=true&id_order='+id_order+'&action=InitOrderDocumentNoteModal',
            success: function(result) {
                if (result.hasError == 0 && result.modalHtml) {
                    $('#footer').next('.bootstrap').append(result.modalHtml);
                    $('#document-note-modal #id_order_invoice').val(documentObj.data('id_order_invoice'));
                    $('#document-note-modal #editNote').text(documentObj.data('edit_note'));
                    $('#document-note-modal').modal('show');
                } else {
                    showErrorMessage(txtSomeErr);
                }
            }
        });
    },
    close: function() {
        $('#document-note-modal').modal('hide');
    },
    submit: function() {
        $(document).find('#submitEditNote').click();
    }
};

const TravellerModal = {
    show: function() {
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: admin_order_tab_link,
            dataType: 'JSON',
            cache: false,
            data: 'ajax=true&id_order='+id_order+'&action=InitTravellerModal',
            success: function(result) {
                if (result.hasError == 0 && result.modalHtml) {
                    $('#footer').next('.bootstrap').append(result.modalHtml);
                    $('#traveller-modal').modal('show');
                } else {
                    showErrorMessage(txtSomeErr);
                }
            }
        });
    },
    close: function() {
        $('#traveller-modal').modal('hide');
    },
    submit: function() {
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: admin_order_tab_link,
            dataType: 'JSON',
            cache: false,
            data: $('#customer-guest-details-form').serialize()+'&ajax=true&id_order='+id_order+'&action=updateGuestDetails',
            success: function(result) {
                if (result.success) {
                    if (result.msg) {
                        showSuccessMessage(result.msg);
                    }

                    if (result.data.guest_name) {
                        $('#customer-guest-details .gender_name').text(result.data.gender_name);
                    }
                    if (result.data.guest_name) {
                        $('#customer-guest-details .guest_name').text(result.data.guest_name);
                    }
                    if (result.data.guest_email) {
                        $('#customer-guest-details .guest_email a').attr('href', 'mailto:'+result.data.guest_email).html('<i class="icon-envelope-o"></i> ' + result.data.guest_email);
                    }
                    if (result.data.guest_phone) {
                        $('#customer-guest-details .guest_phone a').attr('href', 'tel'+result.data.guest_phone).html('<i class="icon-phone"></i> ' + result.data.guest_phone);
                    }

                    TravellerModal.hide();
                } else if (result.errors) {
                    showErrorMessage(result.errors);
                }
            }
        });
    }
};

const RoomStatusModal = {
    show: function(roomObj) {
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: admin_order_tab_link,
            dataType: 'JSON',
            cache: false,
            data: 'ajax=true&id_order='+id_order+'&action=InitRoomStatusModal',
            success: function(result) {
                if (result.hasError == 0 && result.modalHtml) {
                    $('#footer').next('.bootstrap').append(result.modalHtml);

                    $('#room-status-modal #room_status_id_hotel_booking_detail').val(roomObj.data('id_hotel_booking_detail'));
                    $('#room-status-modal #room_status_date_from').val(roomObj.data('date_from'));
                    $('#room-status-modal #room_status_date_to').val(roomObj.data('date_to'));
                    $('#room-status-modal #room_status_id_room').val(roomObj.data('id_room'));
                    $('#room-status-modal #room_status_id_order').val(roomObj.data('id_order'));
                    $('#room-status-modal .booking_order_status').val(roomObj.data('id_status'));

                    if (roomObj.data('id_status') == result.STATUS_CHECKED_IN) {
                        $('.room_status_info_form .room_status_date').val(roomObj.data('date_to') + ' ' + roomObj.data('check_out_time'));
                    } else {
                        $('.room_status_info_form .room_status_date').val(roomObj.data('date_from') + ' ' + roomObj.data('check_in_time'));
                    }

                    $('#room-status-modal .booking_order_status option:selected').attr('disabled', 'disabled');

                    $('#room-status-modal').modal('show');
                } else if (result.errors) {
                    showErrorMessage();
                }
            }
        });
    },
    close: function() {
        $('#room-status-modal').modal('hide');
    },
    submit: function() {
        $(document).find('#submitbookingOrderStatus').click();
    }
};

const RoomReallocationModal = {
    show: function(roomObj) {
        $(".loading_overlay").show();
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: admin_order_tab_link,
            dataType: 'JSON',
            cache: false,
            data: 'ajax=true&id_order='+id_order+'&action=InitRoomReallocationModal',
            success: function(result) {
                if (result.hasError == 0 && result.modalHtml) {
                    $('#footer').next('.bootstrap').append(result.modalHtml);

                    $(".modal_id_htl_booking").val(roomObj.data('id_htl_booking'));
                    $("input.modal_curr_room_num").val(roomObj.data('room_num'));
                    $("span.modal_curr_room_num").text(roomObj.data('room_num') + ', ' + roomObj.data('room_type_name'));
                    $(".cust_name").text(roomObj.data('cust_name'));
                    $(".cust_email").text(roomObj.data('cust_email'));

                    // reset price difference fields
                    $("#reallocation_price_diff").val(0);
                    $("#reallocation_price_diff_block").hide();
                    $(".realloc_roomtype_change_message").hide();

                    // For Rooms Swapping
                    var json_arr_rm_swp = roomObj.data('avail_rm_swap');
                    if (roomObj.data('avail_rm_swap') != 'false' && json_arr_rm_swp.length != 0) {
                        html = '<select class="form-control" name="swap_avail_rooms" id="swap_avail_rooms">';
                            $.each(json_arr_rm_swp, function(key,val) {
                                html += '<option class="swp_rm_opts" value="'+val.id_hotel_booking+'" >'+val.room_num+'</option>';
                            });
                        html += '</select>';
                        $(".swap_avail_rooms_container").empty().append(html);
                    } else {
                        $(".swap_avail_rooms_container").empty().text(no_swap_rm_avail_txt).addClass('text-danger');;
                    }

                    // For Rooms Reallocation
                    var json_arr_realloc_room_types = roomObj.data('avail_realloc_room_types');
                    if (roomObj.data('avail_realloc_room_types') != 'false' && json_arr_realloc_room_types.length != 0) {
                        var idCurrentRoomType = roomObj.data('id_room_type');
                        var roomsTypesHtml = '<select data-id_htl_booking="' + roomObj.data('id_htl_booking') + '" class="form-control" name="realloc_avail_room_type" id="realloc_avail_room_type">';
                            $.each(json_arr_realloc_room_types, function(key, room_type) {
                                roomsTypesHtml += "<option rooms_available='" + JSON.stringify(room_type.rooms) + "' class='realloc_rm_type_opts' value='" + room_type.id_product + "'";
                                if (idCurrentRoomType == room_type.id_product) {
                                    roomsTypesHtml += ' selected="selected"';
                                }
                                roomsTypesHtml += '>' + room_type.room_type_name + '</option>';
                            });
                            roomsTypesHtml += '</select>';

                        setRoomsForReallocation(json_arr_realloc_room_types[idCurrentRoomType]['rooms']);

                        $(".realloc_avail_room_type_container").empty().append(roomsTypesHtml);
                    } else {
                        $(".realloc_avail_rooms_container").empty().text(no_realloc_rm_avail_txt).addClass('text-danger');
                        $(".realloc_avail_room_type_container").empty().text(no_realloc_rm_type_avail_txt).addClass('text-danger');
                    }

                    $('#room-reallocation-modal').modal('show');
                } else {
                    showErrorMessage(txtSomeErr);
                }

                $(".loading_overlay").hide();
            }
        });
    },
    swap: function() {
        $(".error_text").text('');
        var room_to_swap = $('#swap_avail_rooms').val();
        if (typeof room_to_swap == 'undefined' || room_to_swap == 0) {
            $("#swap_sel_rm_err_p").text(slct_rm_err);
            return false;
        }
    },
    reallocate: function() {
        $(".error_text").text('');
        var room_to_reallocate = $('#realloc_avail_rooms').val();
        var room_type_to_reallocate = $('#realloc_avail_room_type').val();

        if (typeof room_type_to_reallocate == 'undefined' || room_type_to_reallocate == 0) {
            $("#realloc_sel_rm_type_err_p").text(slct_rm_type_err);
            return false;
        }

        if (typeof room_to_reallocate == 'undefined' || room_to_reallocate == 0) {
            $("#realloc_sel_rm_err_p").text(slct_rm_err);
            return false;
        }
    },
    changeRoomType: function(roomTypeObj) {
        $(".loading_overlay").show();
        var idHotelBooking = roomTypeObj.data('id_htl_booking');
        $("#reallocation_price_diff").val(0);
        $("#reallocation_price_diff_block").hide();
        if (parseInt(idHotelBooking) > 0) {
            var optionSelected = roomTypeObj.find('option:selected');
            var roomsAvailable = JSON.parse(optionSelected.attr('rooms_available'));

            // set the rooms of the selceted room type
            setRoomsForReallocation(roomsAvailable);

            // send an ajax for fetching if price has changes in the new room type seleceted
            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: admin_order_tab_link,
                dataType: 'JSON',
                cache: false,
                data: {
                    id_htl_booking: idHotelBooking,
                    id_new_room_type: roomTypeObj.val(),
                    action: 'changeRoomTypeToReallocate',
                    ajax: true
                },
                success: function(result) {
                    if (result.success == 1) {
                        // has room type changed for reallocation
                        if (result.has_room_type_change == 1) {
                            $(".realloc_roomtype_change_message").show();
                            // has room type price changed for reallocation
                            if (result.has_price_changes == 1) {
                                $("#reallocation_price_diff").val(result.price_diff);
                                $("#reallocation_price_diff_block").show();
                            }
                        } else {
                            $(".realloc_roomtype_change_message").hide();
                        }
                        $('#room_type_change_info').empty();
                        if (result.is_changes_present == 1) {
                        }
                    } else if (typeof(result.error) != 'undefinded' && result.error) {
                        showErrorMessage(result.error);
                    } else {
                        showErrorMessage(txtSomeErr);
                    }

                    $(".loading_overlay").hide();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $(".loading_overlay").hide();
                    showErrorMessage(txtSomeErr);
                }
            });
        } else {
            $(".loading_overlay").hide();
            showErrorMessage(txtSomeErr);
            return false;
        }
    },
    close: function() {
        $('#room-reallocation-modal').modal('hide');
    },
    submit: function() {
    }
};

const AddRoomBookingModal = {
    show: function() {
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: admin_order_tab_link,
            dataType: 'JSON',
            cache: false,
            data: 'ajax=true&id_order='+id_order+'&action=initAddRoomBookingModal',
            success: function(result) {
                if (result.hasError == 0 && result.modalHtml) {
                    $('#footer').next('.bootstrap').append(result.modalHtml);

                    $('#new_product').fadeIn('fast').promise().done(function () {
                        $('#add-room-booking-modal').modal('show');

                        $(document).find("#add_product_product_name").autocomplete(admin_order_tab_link,
                            {
                                minChars: 3,
                                max: 10,
                                width: 500,
                                selectFirst: false,
                                scroll: false,
                                dataType: "json",
                                highlightItem: true,
                                formatItem: function(data, i, max, value, term) {
                                    return value;
                                },
                                parse: function(data) {
                                    var products = new Array();
                                    if (typeof(data.products) != 'undefined')
                                        for (var i = 0; i < data.products.length; i++)
                                            products[i] = { data: data.products[i], value: data.products[i].name };
                                    return products;
                                },
                                extraParams: {
                                    ajax: true,
                                    token: token,
                                    action: 'searchProducts',
                                    booking_product: 1,
                                    id_lang: id_lang,
                                    id_currency: id_currency,
                                    id_address: id_address,
                                    id_customer: id_customer,
                                    id_order: id_order,
                                    product_search: function() { return $('#add_product_product_name').val(); }
                                }
                            }
                        )
                        .result(function(event, data, formatted) {
                            if (!data) {
                                $('#new_product input, #new_product select').each(function() {
                                    if ($(this).attr('id') != 'add_product_product_name')
                                        $('#new_product input, #new_product select, #new_product button').attr('disabled', true);
                                });
                            } else {
                                $('#new_product input, #new_product select, #new_product button').removeAttr('disabled');
                                if ($('#new_product .booking_occupancy').length) {
                                    $('#new_product .booking_guest_occupancy').removeClass('disabled');
                                    setRoomTypeGuestOccupancy($('#new_product .booking_occupancy_wrapper'));
                                }
                                if (data.room_type_info) {
                                    $('#new_product .max_adults').val(data.room_type_info.max_adults);
                                    $('#new_product .max_children').val(data.room_type_info.max_children);
                                    $('#new_product .max_guests').val(data.room_type_info.max_guests);
                                    $('#new_product .num_adults').attr('max', data.room_type_info.max_adults);
                                    $('#new_product .num_children').attr('max', data.room_type_info.max_children);
                                }

                                // Keep product variable
                                current_product = data;
                                $('#add_product_product_id').val(data.id_product);
                                $('#add_product_product_name').val(data.name);
                                $('#add_product_product_price_tax_incl').val(data.price_tax_incl);
                                $('#add_product_product_price_tax_excl').val(data.price_tax_excl);

                                //Added by webkul to set curent date in the date fields by default
                                var date_in = $.datepicker.formatDate('dd-mm-yy', new Date());
                                var date_out = $.datepicker.formatDate('dd-mm-yy', new Date(new Date().getTime()+24*60*60*1000));
                                $('#new_product').find("input.add_room_date_from").val(date_in);
                                $('#new_product').find("input.add_room_date_to").val(date_out);
                                //End

                                AddRoomBookingModal.initDatePickers();

                                addRoomRefreshTotal();

                                $('.add_room_fields').show();
                                $('.submitAddRoom').removeAttr('disabled');
                            }
                        });
                    });
                } else {
                    showErrorMessage(txtSomeErr);
                }
            }
        });
    },
    initDatePickers: function () {
        $('.add_room_date_from').datepicker({
            showOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            onSelect: function(selectedDate) {
                let objDateToMin = $.datepicker.parseDate('dd-mm-yy', selectedDate);
                objDateToMin.setDate(objDateToMin.getDate() + 1);

                $('#new_product .add_room_date_to').datepicker('option', 'minDate', objDateToMin);
            },
            beforeShow : function () {
                if(allowBackdateOrder) {
                    var minDate = null;
                } else {
                    var minDate = new Date();
                }
                $(this).datepicker("option", "minDate", minDate);
            }
        });

        $('.add_room_date_to').datepicker({
            showOtherMonths: true,
            dateFormat: 'dd-mm-yy',
            beforeShow : function () {
                var date_from = $.datepicker.parseDate('dd-mm-yy', $(this).closest('.bookingDuration').find('.add_room_date_from').val());
                date_from.setDate(date_from.getDate() + 1);
                $(this).datepicker("option", "minDate", date_from);
            }
        });
    },
    close: function() {
        $('#add-room-booking-modal').modal('hide');
    },
    submit: function() {
        $('#add-room-booking-modal #submitAddProduct').click();
    }
};

const EditRoomBookingModal = {
    show: function(btnEdit) {
        const productLineData = $(btnEdit).attr('data-product_line_data');
        const jsonProductLineData = JSON.parse(productLineData);

        const data = {
            ajax: 1,
            action: 'initEditRoomBookingModal',
            id_order: parseInt(jsonProductLineData.id_order),
            product_line_data: productLineData,
            id_room: parseInt(jsonProductLineData.id_room),
            id_product: parseInt(jsonProductLineData.id_product),
            date_from: jsonProductLineData.date_from,
            date_to: jsonProductLineData.date_to,
            orderEdit: 1,
        };

        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: admin_order_tab_link,
            dataType: 'JSON',
            cache: false,
            data: data,
            success: function(result) {
                if (result.hasError == 0 && result.modalHtml) {
                    $('#footer').next('.bootstrap').append(result.modalHtml);

                    const dateFrom = $.datepicker.formatDate('dd-mm-yy', $.datepicker.parseDate('yy-mm-dd', jsonProductLineData.date_from));
                    const dateTo = $.datepicker.formatDate('dd-mm-yy', $.datepicker.parseDate('yy-mm-dd', jsonProductLineData.date_to));

                    $('#edit_product .edit_product_date_from').attr('value', dateFrom);
                    $('#edit_product .edit_product_date_to').attr('value', dateTo);
                    $('#edit_product .edit_product_date_from_actual').attr('value', jsonProductLineData.date_from);
                    $('#edit_product .edit_product_date_to_actual').attr('value', jsonProductLineData.date_to);
                    $('#edit_product .room_unit_price').val(parseFloat(jsonProductLineData.paid_unit_price_tax_excl));

                    //@todo: Putting below datepicker js outside this not working
                    $('#edit_product .edit_product_date_from').datepicker({
                        showOtherMonths: true,
                        dateFormat: 'dd-mm-yy',
                        altField: '#edit_product .edit_product_date_from_actual',
                        onSelect: function(selectedDate) {
                            let objDateToMin = $.datepicker.parseDate('dd-mm-yy', selectedDate);
                            objDateToMin.setDate(objDateToMin.getDate() + 1);

                            $('#edit_product .edit_product_date_to').datepicker('option', 'minDate', objDateToMin);
                        },
                        beforeShow : function () {
                            if(allowBackdateOrder) {
                                var minDate = null;
                            } else {
                                var minDate = new Date(Math.min($.datepicker.parseDate('dd-mm-yy', $(this).data('min_date')), new Date()));
                            }
                            $(this).datepicker("option", "minDate", minDate);
                        }
                    });

                    $('#edit_product .edit_product_date_to').datepicker({
                        showOtherMonths: true,
                        dateFormat: 'dd-mm-yy',
                        altField: '#edit_product .edit_product_date_to_actual',
                        beforeShow : function () {
                            var date_from = $.datepicker.parseDate('dd-mm-yy', $(this).closest('.form-group').find('.edit_product_date_from').val());
                            date_from.setDate(date_from.getDate() + 1);
                            $(this).datepicker("option", "minDate", date_from);
                        }
                    });

                    $('#edit_product .extra-services-container #id_htl_booking').val(jsonProductLineData.id);
                    $('#edit-room-booking-modal').modal('show');
                } else {
                    showErrorMessage(txtSomeErr);
                }
            }
        });
    }
};

const CancelRoomBookingModal = {
    show: function() {
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: admin_order_tab_link,
            dataType: 'JSON',
            cache: false,
            data: 'ajax=true&id_order='+id_order+'&action=initCancelRoomBookingModal',
            success: function(result) {
                if (result.hasError == 0 && result.modalHtml) {
                    $('#footer').next('.bootstrap').append(result.modalHtml);

                    $('#cancel-room-booking-modal').modal('show');

                } else {
                    showErrorMessage(txtSomeErr);
                }
            }
        });
    },
    close: function() {
        $('#cancel-room-booking-modal').modal('hide');
    },
    submit: function() {
        $('#initiateRefund').click();
    }
};

function updateAdditionalServices(element)
{
    var id_room_type_service_product_order_detail = $(element).data('id_room_type_service_product_order_detail');
    if ($(element).find('.qty').length) {
        var qty = $(element).find('.qty').val();
        if (qty == '' || !$.isNumeric(qty) || qty < 1) {
            $(element).find('.qty').val(1);
            qty = 1;
        }
    } else {
        var qty = 1;
    }

    var unit_price = $(element).find('.unit_price').val();
    if ($.isNumeric(qty)) {
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: admin_order_tab_link,
            dataType: 'JSON',
            cache: false,
            data: {
                id_room_type_service_product_order_detail: id_room_type_service_product_order_detail,
                qty: qty,
                unit_price: unit_price,
                action: 'updateRoomAdditionalServices',
                ajax: true
            },
            success: function(jsonData) {
                if (!jsonData.hasError) {
                    if (jsonData.service_panel) {
                        $('#room_type_service_product_desc').replaceWith(jsonData.service_panel);
                    }
                    showSuccessMessage(txtExtraDemandSucc);
                } else {
                    showErrorMessage(jsonData.errors);

                }
            }
        });
    }

}

function updateRoomDemand(element)
{
    var id_booking_demand = $(element).data('id_booking_demand');
    var unit_price = $(element).find('.unit_price').val();
    $.ajax({
        type: 'POST',
        headers: {
            "cache-control": "no-cache"
        },
        url: admin_order_tab_link,
        dataType: 'JSON',
        cache: false,
        data: {
            id_booking_demand: id_booking_demand,
            unit_price: unit_price,
            action: 'updateRoomExtraDemands',
            ajax: true
        },
        success: function(jsonData) {
            if (!jsonData.hasError) {
                if (jsonData.facilities_panel) {
                    $('#room_type_demands_desc').replaceWith(jsonData.facilities_panel);
                }
                showSuccessMessage(txtExtraDemandSucc);
            } else {
                showErrorMessage(jsonData.errors);

            }
        }
    });
}

function setRoomsForReallocation(roomsAvailable)
{
    if (typeof(roomsAvailable) != 'undefined' && roomsAvailable.length) {
        var roomsHtml = '<select class="form-control" name="realloc_avail_rooms" id="realloc_avail_rooms">';
            roomsHtml += '<option class="realloc_rm_opts" value="0">---- ' + select_room_txt + ' ----</option>';
            $.each(roomsAvailable, function(key, roomInfo) {
                roomsHtml += '<option class="realloc_rm_opts" value="' + roomInfo.id_room + '">' + roomInfo.room_num + '</option>';
            });
        roomsHtml += '</select>';

        $(".realloc_avail_rooms_container").empty().append(roomsHtml);
    } else {
        $(".realloc_avail_rooms_container").empty().text(no_realloc_rm_avail_txt);
    }
}

function checkPartialRefundProductQuantity(it)
{
	if (parseInt($(it).val()) > parseInt($(it).closest('td').find('.partialRefundProductQuantity').val()))
		$(it).val($(it).closest('td').find('.partialRefundProductQuantity').val());
	if (order_discount_price)
		actualizeRefundVoucher();
}

function checkPartialRefundProductAmount(it)
{
	var old_price = $(it).closest('td').find('.partialRefundProductAmount').val();
	if (typeof $(it).val() !== undefined && typeof new_price !== undefined &&
		parseFloat($(it).val()) > parseFloat(old_price))
		$(it).val(old_price);

	if (order_discount_price)
		actualizeRefundVoucher();
}

function actualizeRefundVoucher()
{
	var total = 0.0;
	$('.edit_product_price_tax_incl.edit_product_price').each(function(){
		quantity_refund_product = parseFloat($(this).closest('td').parent().find('td.partial_refund_fields.current-edit').find('input[onchange="checkPartialRefundProductQuantity(this)"]').val());
		if (quantity_refund_product > 0)
		{
			current_amount = parseFloat($(this).closest('td').parent().find('td.partial_refund_fields.current-edit').find('input[onchange="checkPartialRefundProductAmount(this)"]').val()) ?
			parseFloat($(this).closest('td').parent().find('td.partial_refund_fields.current-edit').find('input[onchange="checkPartialRefundProductAmount(this)"]').val())
			: parseFloat($(this).val());
			total += current_amount * quantity_refund_product;
		}
	});
	$('#total_refund_1').remove();
	$('#lab_refund_1').append('<span id="total_refund_1">' + formatCurrency(total, currency_format, currency_sign, currency_blank) + '</span>');
	$('#lab_refund_1').append('<input type="hidden" name="order_discount_price" value=' + order_discount_price + '/>');
	$('#total_refund_2').remove();
	if (parseFloat(total - order_discount_price) > 0.0) {
		document.getElementById('refund_2').disabled = false;
		$('#lab_refund_2').append('<span id="total_refund_2">' + formatCurrency((total - order_discount_price), currency_format, currency_sign, currency_blank) + '</span>');
	}
	else {
		if (document.getElementById('refund_2').checked === true)
			document.getElementById('refund_1').checked = true;
		document.getElementById('refund_2').disabled = true;
		$('#lab_refund_2').append('<span id="total_refund_2">' + errorRefund + '</span>');
	}
}

function actualizeTotalRefundVoucher()
{
	var total = 0.0;
	$('.edit_product_price_tax_incl.edit_product_price').each(function(){
		quantity_refund_product = parseFloat($(this).closest('td').parent().find('td.cancelQuantity').children().val());
		if (typeof quantity_refund_product !== 'undefined' && quantity_refund_product > 0)
			total += $(this).val() * quantity_refund_product;
	});
	$('#total_refund_1').remove();
	$('#lab_refund_total_1').append('<span id="total_refund_1">' + formatCurrency(total, currency_format, currency_sign, currency_blank) + '</span>');
	$('#lab_refund_total_1').append('<input type="hidden" name="order_discount_price" value=' + order_discount_price + '/>');
	$('#total_refund_2').remove();
	if (parseFloat(total - order_discount_price) > 0.0) {
		document.getElementById('refund_total_2').disabled = false;
		$('#lab_refund_total_2').append('<span id="total_refund_2">' + formatCurrency((total - order_discount_price), currency_format, currency_sign, currency_blank) + '</span>');
	}
	else {
		if (document.getElementById('refund_total_2').checked === true)
			document.getElementById('refund_total_1').checked = true;
		document.getElementById('refund_total_2').disabled = true;
		$('#lab_refund_total_2').append('<span id="total_refund_2">' + errorRefund + '</span>');
	}
}

function setCancelQuantity(itself, id_order_detail, quantity)
{
	$('#cancelQuantity_' + id_order_detail).val($(itself).prop('checked') ? quantity : '');
	if (order_discount_price)
		actualizeTotalRefundVoucher();
}

function checkTotalRefundProductQuantity(it)
{
	$(it).parent().parent().find('td.cancelCheck input[type=checkbox]').attr("checked", true);
	if (parseInt($(it).val()) > parseInt($(it).closest('td').find('.partialRefundProductQuantity').val()))
		$(it).val($(it).closest('td').find('.partialRefundProductQuantity').val());
	if (order_discount_price)
		actualizeTotalRefundVoucher();
}