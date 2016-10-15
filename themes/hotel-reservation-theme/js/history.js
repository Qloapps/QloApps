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
				$.scrollTo(this, 1200);
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


// by webkul , ajax for saving data for refund request
$(document).ready(function(){
	$('body').on('click', '.roomRequestForRefund', function()
	{
		var id_order = $(this).data('id_order');
		var id_product = $(this).data('id_product');
		var id_currency = $(this).data('id_currency');
		var id_customer = $(this).data('id_customer');
		var num_rooms = $(this).data('num_rooms');
		var date_from = $(this).data('date_from');
		var date_to = $(this).data('date_to');
		var amount = $(this).data('amount');
		$current = $(this);

		$.fancybox({
			href: "#reason_fancybox_content",
			width: 600,
		    autoSize : true,
		    autoScale : true,
		    maxWidth : '100%',
			'hideOnContentClick': false,
			beforeLoad: function ()
			{
				$('#cancel_req_id_order').val(id_order);
				$('#cancel_req_id_product').val(id_product);
				$('#cancel_req_id_currency').val(id_currency);
				$('#cancel_req_id_customer').val(id_customer);
				$('#cancel_req_num_rooms').val(num_rooms);
				$('#cancel_req_date_from').val(date_from);
				$('#cancel_req_date_to').val(date_to);
				$('#cancel_req_amount').val(amount);
			},
			beforeClose: function ()
			{
				$('#cancel_req_id_order, #cancel_req_id_product, #cancel_req_id_currency, #cancel_req_id_customer, #cancel_req_num_rooms, #cancel_req_date_from, #cancel_req_date_to, #amount #reasonForRefund, .reasonForRefund').val('');
				$('.cancel_req_amount').hide();
				$('#reasonForRefund').css('border','1px solid #d6d4d4');
				$('.required_err').hide();
			}
		});
	});

	$('body').on('click', '.totalOrdercancellation_btn', function()
	{
		var order_data = $(this).data('order_data');
		var id_currency = $(this).data('id_currency');
		var id_customer = $(this).data('id_customer');
		var id_order = $(this).data('id_order');
		
		$.fancybox({
			href: "#reason_fancybox_content",
			width: 600,
		    autoSize : true,
		    autoScale : true,
		    maxWidth : '100%',
			'hideOnContentClick': false,
			beforeLoad: function ()
			{
				$('#cancel_req_id_currency').val(id_currency);
				$('#cancel_req_id_order').val(id_order);
				$('#cancel_req_id_customer').val(id_customer);
				$('#cancel_req_total_order_data').val(JSON.stringify(order_data));
			},
			beforeClose: function ()
			{
				$('#cancel_req_id_currency, #cancel_req_id_order, #cancel_req_id_customer, #cancel_req_total_order_data, #reasonForRefund').val('');
				$('.cancel_req_amount').hide();
				$('#reasonForRefund').css('border','1px solid #d6d4d4');
				$('.required_err').hide();
			}
		});
	});

	$('body').on('click', '#submit_refund_reason', function()
	{
		var order_data = $('#cancel_req_total_order_data').val();
		if (order_data)
		{
			var cancellation_reason = $('#reasonForRefund').val();
			var id_order = $('#cancel_req_id_order').val();
			var id_currency = $('#cancel_req_id_currency').val();
			var id_customer = $('#cancel_req_id_customer').val();

			if ($('#reasonForRefund').val() == '')
			{
				$('.required_err').show();
				$('#reasonForRefund').css('border','1px solid #AA1F00');
			}
			else
			{
				$('#submit_refund_reason').attr('disabled', 'disabled');
				$.ajax({
			        data:{
			        	total_order_data: order_data,
	    				contentType: "application/json; charset=UTF-8",
			            id_customer : id_customer,
			            id_order : id_order,
						id_currency : id_currency,
						cancellation_reason : cancellation_reason,
						saveTotalOrderRefundInfo : true,
			        },
			        method:'POST',
			        dataType:'json',
			        url:historyUrl,
			        success:function(data)
			        {
			        	if (data.mail_err)
			        	{
			        		alert(mail_sending_err);
			        	}
			        	if (data.status == 'success')
			        	{
			        		$('.totalOrdercancellation_div').hide();
			        		$(".roomRequestForRefund").parent().siblings('.stage_name').html('<p>'+wait_stage_msg+'</p>');
			        		$(".roomRequestForRefund").parent().siblings('.status_name').html('<p>'+pending_state_msg+'</p>');
			        		$(".roomRequestForRefund").parent().html('<p>'+req_sent_msg+'</p>');
			        		$.fancybox.close();
			        	}
			        	else
			        	{
			        		alert(refund_request_sending_error);
			        	}
			        },
			        error: function(XMLHttpRequest, textStatus, errorThrown)
			        {
			            alert(textStatus);
			        }
		    	});
			}
		}
		else
		{
			var id_order = $('#cancel_req_id_order').val();
			var id_room = $('#cancel_req_id_room').val();
			var id_product = $('#cancel_req_id_product').val();
			var id_currency = $('#cancel_req_id_currency').val();
			var id_customer = $('#cancel_req_id_customer').val();
			var num_rooms = $('#cancel_req_num_rooms').val();
			var date_from = $('#cancel_req_date_from').val();
			var date_to = $('#cancel_req_date_to').val();
			var amount = $('#cancel_req_amount').val();
			var cancellation_reason = $('#reasonForRefund').val();

			if ($('#reasonForRefund').val() == '')
			{
				$('.required_err').show();
				$('#reasonForRefund').css('border','1px solid #AA1F00');
			}
			else
			{
				$('#submit_refund_reason').attr('disabled', 'disabled');
				$.ajax({
			        data:{
			            id_order : id_order,
						id_product : id_product,
						id_customer : id_customer,
						id_currency : id_currency,
						num_rooms : num_rooms,
						date_from : date_from,
						date_to : date_to,
						amount : amount,
						cancellation_reason : cancellation_reason,
						saveRefundInfo : true,
			        },
			        method:'POST',
			        dataType:'json',
			        url:historyUrl,
			        success:function(data)
			        {
			        	if (data.mail_err)
			        	{
			        		alert(mail_sending_err);
			        	}

			        	if (data.status == 'success')
			        	{
			        		$('#submit_refund_reason').attr('disabled', false);
			        		if ($(".roomRequestForRefund").length == 1)
			        		{
			        			$('.totalOrdercancellation_div').hide();
			        		}
			        		$(".order_cancel_request_button_"+id_product+'_'+date_from+'_'+date_to).parent().siblings('.stage_name').html('<p>'+wait_stage_msg+'</p>');
			        		$(".order_cancel_request_button_"+id_product+'_'+date_from+'_'+date_to).parent().siblings('.status_name').html('<p>'+pending_state_msg+'</p>');
			        		$(".order_cancel_request_button_"+id_product+'_'+date_from+'_'+date_to).parent().html('<p>'+req_sent_msg+'</p>');
			        		$.fancybox.close();
			        	}
			        	else
			        	{
			        		alert(refund_request_sending_error);
			        	}
			        },
			        error: function(XMLHttpRequest, textStatus, errorThrown)
			        {
			            alert(textStatus);
			        }
		    	});
			}
		}
	});
});

