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
//show the order-details with ajax
function showOrder(mode, var_content, file)
{
	$.get(
		file,
		((mode === 1) ? {'id_order': var_content, 'ajax': true} : {'id_order_return': var_content, 'ajax': true}),
		function(data)
		{
			$('#block-order-detail').fadeOut('slow', function()
			{
				$(this).html(data);
				$('.footab').footable();
				/* if return is allowed*/
				if ($('#order-detail-content .order_cb').length > 0)
				{
					//return slip : check or uncheck every checkboxes
					$('#order-detail-content th input[type=checkbox]').click(function()
					{
							$('#order-detail-content td input[type=checkbox]').each(function()
							{
								this.checked = $('#order-detail-content th input[type=checkbox]').is(':checked');
								updateOrderLineDisplay(this);
							});
					});
					//return slip : enable or disable 'global' quantity editing
					$('#order-detail-content td input[type=checkbox]').click(function()
					{
						updateOrderLineDisplay(this);
					});
					//return slip : limit quantities
					$('#order-detail-content td .order_qte_input').keyup(function()
					{
						var maxQuantity = parseInt($(this).parent().find('.order_qte_span').text());
						var quantity = parseInt($(this).val());
						if (isNaN($(this).val()) && $(this).val() !== '')
						{
							$(this).val(maxQuantity);
						}
						else
						{
							if (quantity > maxQuantity)
								$(this).val(maxQuantity);
							else if (quantity < 1)
								$(this).val(1);
						}
					});
					// The button to increment the product return value
					$(document).on('click', '.return_quantity_down', function(e){
						e.preventDefault();
						var $input = $(this).parent().parent().find('input');
						var count = parseInt($input.val()) - 1;
						count = count < 1 ? 1 : count;
						$input.val(count);
						$input.change();
					});
					// The button to decrement the product return value
					$(document).on('click', '.return_quantity_up', function(e){
						e.preventDefault();
						var maxQuantity = parseInt($(this).parent().parent().find('.order_qte_span').text());
						var $input = $(this).parent().parent().find('input');
						var count = parseInt($input.val()) + 1;
						count = count > maxQuantity ? maxQuantity : count;
						$input.val(count);
						$input.change();
					});
				}
				//catch the submit event of sendOrderMessage form
				$('form#sendOrderMessage').submit(function(){
					return sendOrderMessage();
			});
			$(this).fadeIn('slow', function() {
				$('html, body').animate({
					scrollTop: $('#block-order-detail').offset().top
				}, 1200);
				initPriceTooltip();
			});
		});
	});
}

function updateOrderLineDisplay(domCheckbox)
{
	var lineQuantitySpan = $(domCheckbox).parent().parent().find('.order_qte_span');
	var lineQuantityInput = $(domCheckbox).parent().parent().find('.order_qte_input');
	var lineQuantityButtons = $(domCheckbox).parent().parent().find('.return_quantity_up, .return_quantity_down');
	if($(domCheckbox).is(':checked'))
	{
		lineQuantitySpan.hide();
		lineQuantityInput.show();
		lineQuantityButtons.show();
	}
	else
	{
		lineQuantityInput.hide();
		lineQuantityButtons.hide();
		lineQuantityInput.val(lineQuantitySpan.text());
		lineQuantitySpan.show();
	}
}

//send a message in relation to the order with ajax
function sendOrderMessage()
{
	paramString = "ajax=true";
	$('#sendOrderMessage').find('input, textarea, select').each(function(){
		paramString += '&' + $(this).attr('name') + '=' + encodeURIComponent($(this).val());
	});

	$.ajax({
		type: "POST",
		headers: { "cache-control": "no-cache" },
		url: $('#sendOrderMessage').attr("action") + '?rand=' + new Date().getTime(),
		data: paramString,
		beforeSend: function(){
			$(".button[name=submitMessage]").prop("disabled", "disabled");
		},
		success: function(msg){
			$('#block-order-detail').fadeOut('slow', function() {
				$(this).html(msg);
				//catch the submit event of sendOrderMessage form
				$('#sendOrderMessage').submit(function(){
					return sendOrderMessage();
				});
				$(this).fadeIn('slow');
	        	$(".button[name=submitMessage]").prop("disabled", false);
			});
		},
		error: function(){
			$(".button[name=submitMessage]").prop("disabled", false);
		}
	});
	return false;
}
function initPriceTooltip()
{
	if ($('.order-price-info').length) {
		$('.order-price-info').each(function() {
			$(this).tooltip({
				content: $(this).closest('td').find('.price-info-container').html(),
				items: "span",
				trigger : 'hover',
                tooltipClass: "price-tootip",
				open: function(event, ui) {
					if (typeof(event.originalEvent) === 'undefined')
					{
						return false;
					}

					var $id = $(ui.tooltip).attr('id');

					// close any lingering tooltips
					if ($('div.ui-tooltip').not('#' + $id).length) {
						return false;
					}

					// ajax function to pull in data and add it to the tooltip goes here
				},
				close: function(event, ui) {
					ui.tooltip.hover(function()
					{
						$(this).stop(true).fadeTo(400, 1);
					},
					function()
					{
						$(this).fadeOut('400', function()
						{
							$(this).remove();
						});
					});
				}
			});
		});
	}
}
$(document).ready(function(){
	var page = $('html, body');
	page.on('mousewheel', function () {
		page.stop();
	});

	// If customer clicks for refund request then toggle refund request fields
	$('body').on('click', '#order_refund_request', function(e) {
		e.preventDefault();
		if ($(this).attr('refund_fields_on') == 0) {
			$('.standard_refund_fields').show();
			$(this).attr('refund_fields_on', 1);
			$(this).addClass('cancel_request_btn');
			$(this).html('<i class="icon-close"></i> ' + cancel_req_txt);
		} else {
			$('.standard_refund_fields').hide();
			$(this).attr('refund_fields_on', 0);
			$(this).removeClass('cancel_request_btn');
			$(this).text(cancel_booking_txt);
		}
	});

	$('body').on('click', '#order_refund_request_submit', function(e) {
		e.preventDefault();

		// get the checked bookings to refund
		var bookings_to_refund = $("input[name='bookings_to_refund[]']:checked").map(function(){return $(this).val();}).get();
		//  check if at least one room is selected for refund request
		if (bookings_to_refund.length) {
			bookings_to_refund = JSON.stringify(bookings_to_refund);

			$.fancybox({
				href: "#reason_fancybox_content",
				minWidth : 500,
				autoScale : true,
				autoSize : true,
				'hideOnContentClick': false,
				beforeLoad: function () {
					$('#htlRefundReasonForm #bookings_to_refund').val(bookings_to_refund);
				},
				beforeClose: function () {
					$('#bookings_to_refund').val('');
					$('.cancel_req_amount').hide();
					$('.reasonForRefund').css('border','1px solid #d6d4d4');
					$('.required_err').hide();
				}
			});
		} else {
			showErrorMessage(no_bookings_selected);
		}
	});

	$('body').on('click', '#submit_refund_reason', function() {
		var bookings_to_refund = $('#htlRefundReasonForm #bookings_to_refund').val();
		if (bookings_to_refund) {
			var cancellation_reason = $('.reasonForRefund').val();
			var id_order = $(this).data('id_order');

			if (cancellation_reason == '') {
				$('.required_err').show();
				$('.reasonForRefund').css('border','1px solid #AA1F00');
			} else {
				$(".loading_overlay").show();

				$('#submit_refund_reason').attr('disabled', 'disabled');
				$.ajax({
			        data:{
						contentType: "application/json; charset=UTF-8",
						id_order: id_order,
			        	bookings_to_refund: bookings_to_refund,
						cancellation_reason : cancellation_reason,
						ajax : true,
						method: 'submitRefundRequest',
			        },
			        method:'POST',
			        dataType:'JSON',
			        url:historyUrl,
			        success:function(data) {
			        	if (data.status == '1') {
							$(".roomRequestForRefund").parent().html('<p>'+req_sent_msg+'</p>');
							showSuccessMessage(refund_request_success_txt);

							$('.bookings_to_refund:checked').prop('disabled', true);

							$.fancybox.close();

							showOrder(1, $("input[name=id_order]").val(), historyUrl)
			        	} else {
							showErrorMessage(refund_request_sending_error);
							$('#submit_refund_reason').attr('disabled', false);
						}
						$(".loading_overlay").hide();
			        },
			        error: function(XMLHttpRequest, textStatus, errorThrown) {
						showErrorMessage(textStatus);
						$('#submit_refund_reason').attr('disabled', false);
						$(".loading_overlay").hide();
			        }
		    	});
			}
		} else {
			showErrorMessage(no_bookings_selected);
		}
	});

	initPriceTooltip();
});

