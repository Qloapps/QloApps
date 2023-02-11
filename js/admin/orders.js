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

function updateDiscountForm(discount_form_html)
{
	$('#voucher_form').html(discount_form_html);
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
	$('#add_product_product_total').html(formatCurrency(total, currency_format, currency_sign, currency_blank));
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

function addViewOrderDetailRow(view)
{
	html = $(view);
	html.find('td').hide();
	$('tr#new_invoice').hide();
	$('tr#new_product').hide();

	// Initialize fields
	closeAddProduct();

	$('tr#new_product').before(html);
	html.find('td').each(function() {
		if (!$(this).is('.product_invoice'))
			$(this).fadeIn('slow');
	});
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

function closeAddProduct()
{
	$('tr#new_invoice').hide();
	$('tr#new_product').hide();

	// Initialize fields
	$('tr#new_product select, tr#new_product input').each(function() {
		if (!$(this).is('.button'))
			$(this).val('')
	});
	$('tr#new_invoice select, tr#new_invoice input').val('');
	$('#add_product_product_quantity').val('1');
	$('#add_product_product_attribute_id option').remove();
	$('#add_product_product_attribute_area').hide();
	if (stock_management)
		$('#add_product_product_stock').html('0');
	current_product = null;
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

	$('#add_product').unbind('click').click(function(e) {
		$('.cancel_product_change_link:visible').trigger('click');
		$('.add_product_fields').show();
		//invoice field is hidden because has not to be edited by webkul
		$('#add_product_product_invoice').hide();

		$('.edit_product_fields, .standard_refund_fields, .partial_refund_fields, .order_action').hide();
		$('tr#new_product').slideDown('fast', function () {
			$('tr#new_product td').fadeIn('fast').promise().done(function () {
				$('#add_product_product_name').focus();
				scroll_if_anchor('#new_product', 360);
			});
		});

		e.preventDefault();
	});

	$('#cancelAddProduct').unbind('click').click(function() {
		$('.order_action').not('.standard_refund_fields').show();
		$('tr#new_product td').fadeOut('fast');
	});

	$("#add_product_product_name").autocomplete(admin_order_tab_link,
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
		if (!data)
		{
			$('tr#new_product input, tr#new_product select').each(function() {
				if ($(this).attr('id') != 'add_product_product_name')
					$('tr#new_product input, tr#new_product select, tr#new_product button').attr('disabled', true);
			});
		}
		else
		{
			$('tr#new_product input, tr#new_product select, tr#new_product button').removeAttr('disabled');
			$('tr#new_product .booking_guest_occupancy').removeClass('disabled');
			if (data.room_type_info) {
				$('tr#new_product .max_adults').val(data.room_type_info.max_adults);
				$('tr#new_product .max_children').val(data.room_type_info.max_children);
				$('tr#new_product .max_guests').val(data.room_type_info.max_guests);
				$('tr#new_product .num_adults').attr('max', data.room_type_info.max_adults);
				$('tr#new_product .num_children').attr('max', data.room_type_info.max_children);
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
        	var tr_product = $(this).closest('#new_product');
        	tr_product.find("input.add_product_date_from").val(date_in);
        	tr_product.find("input.add_product_date_to").val(date_out);
			//End

			addProductRefreshTotal();
			if (stock_management)
				$('#add_product_product_stock').html(data.stock[0]);

			if (current_product.combinations.length !== 0)
			{
				// Reset combinations list
				$('select#add_product_product_attribute_id').html('');
				var defaultAttribute = 0;
				$.each(current_product.combinations, function() {
					$('select#add_product_product_attribute_id').append('<option value="'+this.id_product_attribute+'"'+(this.default_on == 1 ? ' selected="selected"' : '')+'>'+this.attributes+'</option>');
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
				$('select#add_product_product_attribute_id').html('');
				// Hide select list
				$('#add_product_product_attribute_area').hide();

				populateWarehouseList(current_product.warehouse_list[0]);
			}
		}
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
		addProductRefreshTotal();
	});

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
		if ($(this).val() < $(this).attr('min')) {
			$(this).val($(this).attr('min'));
		}
		if ($(this).val() > max_allowed_for_current) {
			$(this).val(max_allowed_for_current);
		}
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
		setRoomTypeGuestOccupancy($(this).closest('.booking_occupancy_wrapper'));

	});


	$(document).on('click', '.booking_guest_occupancy:not(.disabled)', function(e) {
		$(this).parent().toggleClass('open');
	});

	$(document).on('click', function(e) {
		if ($('.booking_occupancy_wrapper:visible').length) {
			var occupancy_wrapper = $('.booking_occupancy_wrapper:visible');
			$(occupancy_wrapper).find(".occupancy_info_block").addClass('selected');
			if (!($(e.target).closest(".booking_occupancy_wrapper").length || $(e.target).closest(".booking_guest_occupancy").length || $(e.target).closest(".ajax_add_to_cart_button").length || $(e.target).closest(".exclusive.book_now_submit").length || $(e.target).closest(".remove-room-link").length)) {
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

	$('.booking_occupancy_wrapper .add_new_occupancy_btn').on('click', function(e) {
		e.preventDefault();

		var booking_occupancy_wrapper = $(this).closest('.booking_occupancy_wrapper');
		var occupancy_block = '';
		var roomBlockIndex = parseInt($(booking_occupancy_wrapper).find(".occupancy_info_block").last().attr('occ_block_index'));
		roomBlockIndex += 1;
		let max_child_in_room;
		if ($(this).closest(".booking_occupancy_wrapper").find('.max_children').val()) {
			max_child_in_room = $(this).closest(".booking_occupancy_wrapper").find('.max_children').val();
		} else {
			max_child_in_room = window.max_child_in_room;
		}
		var countRooms = parseInt($(booking_occupancy_wrapper).find('.occupancy_info_block').length);
		countRooms += 1
		occupancy_block += '<div class="occupancy_info_block col-sm-12" occ_block_index="'+roomBlockIndex+'">';
			occupancy_block += '<div class="occupancy_info_head col-sm-12"><label class="room_num_wrapper">'+ room_txt + ' - ' + countRooms + '</label><a class="remove-room-link pull-right" href="#">' + remove_txt + '</a></div>';
			occupancy_block += '<div class="col-sm-12">';
				occupancy_block += '<div class="row">';
					occupancy_block += '<div class="form-group col-xs-6 occupancy_count_block">';
						occupancy_block += '<label>' + adults_txt + '</label>';
						occupancy_block += '<input type="number" class="form-control num_occupancy num_adults" name="occupancy['+roomBlockIndex+'][adults]" value="1" min="1">';
					occupancy_block += '</div>';
					occupancy_block += '<div class="form-group col-xs-6 occupancy_count_block">';
						occupancy_block += '<label>' + child_txt + '<span class="label-desc-txt"></span></label>';
						occupancy_block += '<input type="number" class="form-control num_occupancy num_children" name="occupancy['+roomBlockIndex+'][children]" value="0" min="0" max="'+max_child_in_room+'">(' + below_txt + ' ' + max_child_age + ' ' + years_txt + ')';
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
			occupancy_block += '<hr class="occupancy-info-separator col-sm-12">';
		occupancy_block += '</div>';
		$(booking_occupancy_wrapper).find('.booking_occupancy_inner').append(occupancy_block);

		setRoomTypeGuestOccupancy(booking_occupancy_wrapper);
	});

	$('input#add_product_product_quantity').on('change', function(){
		// updateOccupancyInput()
		addProductRefreshTotal();
	});

	$('#submitAddProduct').unbind('click').click(function(e) {
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
				$('#submitAddProduct').attr('disabled', true);
				var query = 'ajax=1&token='+token+'&action=addProductOnOrder&id_order='+id_order+'&';

				query += $('#add_product_warehouse').serialize()+'&';
				query += $('tr#new_product select, tr#new_product input').serialize();
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
							//addViewOrderDetailRow(data.view);
							/*updateAmounts(data.order);
							updateInvoice(data.invoices);
							updateDocuments(data.documents_html);
							updateShipping(data.shipping_html);
							updateDiscountForm(data.discount_form_html);*/

							// Initialize all events
							init();
							//Added by webkul
							location.reload();
							//End
							/*$('.standard_refund_fields').hide();
							$('.partial_refund_fields').hide();
							$('.order_action').show();*/
						}
						else
							jAlert(data.error);
					},
					error : function(XMLHttpRequest, textStatus, errorThrown) {
						jAlert("Impossible to add the room to the cart.\n\ntextStatus: '" + textStatus + "'\nerrorThrown: '" + errorThrown + "'\nresponseText:\n" + XMLHttpRequest.responseText);
					},
					complete: function() {
						$('#submitAddProduct').removeAttr('disabled');
					}
				});
				ajaxQueries.push(ajax_query);
			}
		}
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

	$('#add_product_product_price_tax_excl').unbind('keyup').keyup(function() {
		var price_tax_excl = parseFloat($(this).val());
		if (price_tax_excl < 0 || isNaN(price_tax_excl))
			price_tax_excl = 0;

		var tax_rate = current_product.tax_rate / 100 + 1;
		$('#add_product_product_price_tax_incl').val(ps_round(price_tax_excl * tax_rate, 2));

		// Update total product
		addProductRefreshTotal();
	});

	$('#add_product_product_price_tax_incl').unbind('keyup').keyup(function() {
		var price_tax_incl = parseFloat($(this).val());
		if (price_tax_incl < 0 || isNaN(price_tax_incl))
			price_tax_incl = 0;

		var tax_rate = current_product.tax_rate / 100 + 1;
		$('#add_product_product_price_tax_excl').val(ps_round(price_tax_incl / tax_rate, 2));

		// Update total product
		addProductRefreshTotal();
	});

	$('.edit_product_change_link').unbind('click').click(function(e) {
		$('.add_product_fields, .standard_refund_fields, .order_action').hide();
		$('.edit_product_fields').show();
		$('.row-editing-warning').hide();
		$('.cancel_product_change_link:visible').trigger('click');
		closeAddProduct();

		/*By webkul*/
		var tr_product = $(this).closest('.product-line-row');
		var id_product = tr_product.data('id_product');
		/*End*/
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
				id_order_detail: element.closest('tr.product-line-row').find('input.edit_product_id_order_detail').val(),
				id_address: id_address,
				id_product : id_product,
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
					}
					element_list.find('td .room_unit_price_show').hide();
					element_list.find('td .room_unit_price_edit').show();
					element_list.find('td .booking_duration_show').hide();
					element_list.find('td .booking_duration_edit').show();
					element_list.find('td .booking_occupancy_show').hide();
					element_list.find('td .booking_occupancy_edit').show();
					//element_list.find('td .product_price_show').hide();
					//element_list.find('td .product_price_edit').show();
					element_list.find('td.cancelCheck').hide();
					element_list.find('td.cancelQuantity').hide();
					//element_list.find('td.product_invoice').show();
					$('td.product_action').attr('colspan', 3);
					$('th.edit_product_fields').show();
					$('th.edit_product_fields').attr('colspan',  2);
					element_list.find('td.product_action').attr('colspan', 1);
					element.parent().children('.edit_product_change_link').parent().hide();
					element.parent().parent().find('button.submitProductChange').show();
					element.parent().parent().find('.cancel_product_change_link').show();

					if (+data.reduction_percent != +0)
						element_list.find('.row-editing-warning').show();

					$('.standard_refund_fields').hide();
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
		element_list.find('td .booking_duration_show').show();
		element_list.find('td .product_price_edit').hide();
		element_list.find('td .booking_duration_edit').hide();
		element_list.find('td .room_unit_price_show').show();
		element_list.find('td .room_unit_price_edit').hide();
		element_list.find('td .booking_occupancy_show').show();
		element_list.find('td .booking_occupancy_edit').hide();
		element_list.find('td.product_invoice').hide();
		element_list.find('td.cancelCheck').show();
		element_list.find('td.cancelQuantity').show();
		element_list.find('.edit_product_change_link').parent().show();
		element_list.find('button.submitProductChange').hide();
		element_list.find('.cancel_product_change_link').hide();
		$('.order_action').show();
		$('.standard_refund_fields').hide();
		e.preventDefault();
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
			var element_list = $('.customized-' + $(this).parent().parent().find('.edit_product_id_order_detail').val());
			/*variables are added to the ajax By webkul*/
			var tr_product = $(this).closest('.product-line-row');
			var id_room = tr_product.data('id_room');
			var id_product = tr_product.data('id_product');
			var id_hotel = tr_product.data('id_hotel');
			var date_from = tr_product.data('date_from');
			var date_to = tr_product.data('date_to');
			var order_detail_id = tr_product.data('order_detail_id');
			var id_order_detail = $(this).closest('.product-line-row').find('td .edit_product_id_order_detail').val();
			//some vaues are added to the query by webkul
			query = 'ajax=1&token='+token+'&action=editProductOnOrder&id_order='+id_order+'&id_room='+id_room+'&id_product='+id_product+'&id_hotel='+id_hotel+'&date_from='+date_from+'&date_to='+date_to+'&order_detail_id='+order_detail_id+'&';

			if (element_list.length) {
				query += element_list.parent().parent().find('input, select, .edit_product_id_order_detail').serialize();
			} else {
				query += element.parent().parent().find('input, select, .edit_product_id_order_detail').serialize();
			}

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
						//Commented By webkul
						//refreshProductLineView(element, data.view);
						/*updateAmounts(data.order);
						updateInvoice(data.invoices);
						updateDocuments(data.documents_html);
						updateDiscountForm(data.discount_form_html);*/

						// Initialize all events
						init();
						/*Added By Webkul*/
						location.reload();
						/*ENd*/
						/*$('.standard_refund_fields').hide();
						$('.partial_refund_fields').hide();
						$('.add_product_fields').hide();
						$('.row-editing-warning').hide();
						$('td.product_action').attr('colspan', 3);*/
					}
					else
						jAlert(data.error);
				}
			});
		}

		return false;
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

	$('.delete_product_line').unbind('click').click(function(e) {
		if (!confirm(txt_confirm))
			return false;
		var tr_product = $(this).closest('.product-line-row');
		var id_room = tr_product.data('id_room');
		var id_product = tr_product.data('id_product');
		var id_hotel = tr_product.data('id_hotel');
		var date_from = tr_product.data('date_from');
		var date_to = tr_product.data('date_to');
		var order_detail_id = tr_product.data('order_detail_id');
		//var id_order_detail = $(this).closest('.product-line-row').find('td .edit_product_id_order_detail').val();
		var query = 'ajax=1&action=deleteProductLine&token='+token+'&id_order='+id_order+'&id_room='+id_room+'&id_product='+id_product+'&id_hotel='+id_hotel+'&date_from='+date_from+'&date_to='+date_to+'&order_detail_id='+order_detail_id;
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
					updateDiscountForm(data.discount_form_html);
					location.reload();
				}
				else
					jAlert(data.error);
			}
		});
		e.preventDefault();
	});


	$('.js-set-payment').unbind('click').click(function(e) {
		var amount = $(this).attr('data-amount');
		$('input[name=payment_amount]').val(amount);
		var id_invoice = $(this).attr('data-id-invoice');
		$('select[name=payment_invoice] option[value='+id_invoice+']').attr('selected', true);
		e.preventDefault();
	});

	$('#add_voucher').unbind('click').click(function(e) {
		$('.order_action').hide();
		$('.panel-vouchers,#voucher_form').show();
		e.preventDefault();
	});

	$('#cancel_add_voucher').unbind('click').click(function(e) {
		$('#voucher_form').hide();
		if (!has_voucher)
			$('.panel-vouchers').hide();
		$('.order_action').show();
		e.preventDefault();
	});

	$('#discount_type').unbind('change').change(function() {
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
		if ($(this).parent().parent().next('tr').is(':visible'))
			$(this).parent().parent().next('tr').hide();
		else
			$(this).parent().parent().next('tr').show();
		e.preventDefault();
	});

	$('#add_new_payment').on('click', function(e) {
		$('#form_add_payment').show('fast');
		$(this).hide();
	});

	$('#cancle_add_payment').on('click', function(e) {
		e.preventDefault();
		$('#form_add_payment').hide('fast');
		$('#add_new_payment').show('fast');
	});

	/*By webkul Code for the datepicker*/
	$(".add_product_date_from").datepicker(
    {
    	showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
		onSelect: function(selectedDate) {
            var date_format = selectedDate.split("-");
            var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
            selectedDate.setDate(selectedDate.getDate() + 1);
            $(".add_product_date_to").datepicker("option", "minDate", selectedDate);
        },
    });

    $(".add_product_date_to").datepicker(
    {
    	showOtherMonths: true,
        dateFormat: 'dd-mm-yy',
        onSelect: function(selectedDate) {
            var date_format = selectedDate.split("-");
            var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
            selectedDate.setDate(selectedDate.getDate() - 1);
            $(".add_product_date_from").datepicker("option", "maxDate", selectedDate);
        }
    });

	$(".edit_product_date_from").datepicker(
		{
			showOtherMonths: true,
			dateFormat: 'dd-mm-yy',
			onSelect: function(selectedDate) {
				var date_format = selectedDate.split("-");
				var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
				selectedDate.setDate(selectedDate.getDate() + 1);
				$(".edit_product_date_to").datepicker("option", "minDate", selectedDate);
			},
		});

		$(".edit_product_date_to").datepicker(
		{
			showOtherMonths: true,
			dateFormat: 'dd-mm-yy',
			onSelect: function(selectedDate) {
				var date_format = selectedDate.split("-");
				var selectedDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(date_format[2], date_format[1] - 1, date_format[0])));
				selectedDate.setDate(selectedDate.getDate() - 1);
				$(".edit_product_date_from").datepicker("option", "maxDate", selectedDate);
			}
		});
	/*End*/
}


/* Refund system script */
var flagRefund = '';

$(document).ready(function() {
	// Used for Cancel bookings/Initiate refund button
	$('#desc-order-standard_refund').click(function() {
		closeAddProduct();
		$('.cancel_product_change_link:visible').trigger('click');
		if (order_discount_price) {
			actualizeTotalRefundVoucher();
        }
        $('.standard_refund_fields, .order_action').fadeIn();
		scroll_if_anchor('#refundForm', 170);
	});

	$('#cancelRefund').click(function() {
		$('.standard_refund_fields').hide();
	});

	$('#desc-order-partial_refund').click(function() {
		$('.cancel_product_change_link:visible').trigger('click');
		closeAddProduct();
		if (flagRefund == 'partial') {
			flagRefund = '';
			$('.partial_refund_fields').hide();
			$('.standard_refund_fields').hide();
		}
		else {
			flagRefund = 'partial';
			$('.standard_refund_fields, .product_action, .order_action').hide();
			$('.product_action').hide();
			$('.partial_refund_fields').fadeIn();
		}

		if (order_discount_price)
			actualizeRefundVoucher();
	});
});

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
