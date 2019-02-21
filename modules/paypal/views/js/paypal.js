/*
* 2007-2018 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

{if $use_paypal_in_context}
	window.paypalCheckoutReady = function() {
	        paypal.checkout.setup("{$PayPal_in_context_checkout_merchant_id}", {
	            environment: {if $PAYPAL_SANDBOX}"sandbox"{else}"production"{/if},
	            click: function(event) {
	                event.preventDefault();

	                paypal.checkout.initXO();
	                updateFormDatas();
				    var str = '';
					if($('.paypal_payment_form input[name="id_product"]').length > 0)
						str += '&id_product='+$('.paypal_payment_form input[name="id_product"]').val();
					if($('.paypal_payment_form input[name="quantity"]').length > 0)
						str += '&quantity='+$('.paypal_payment_form input[name="quantity"]').val();
					if($('.paypal_payment_form input[name="id_p_attr"]').length > 0)
						str += '&id_p_attr='+$('.paypal_payment_form input[name="id_p_attr"]').val();

	                $.support.cors = true;
	                $.ajax({
	                    url: "{$base_dir_ssl}modules/paypal/express_checkout/payment.php",
	                    type: "GET",
	                    data: '&ajax=1&onlytoken=1&express_checkout='+$('input[name="express_checkout"]').val()+'&current_shop_url='+$('input[name="current_shop_url"]').val()+'&bn='+$('input[name="bn"]').val()+str,   
	                    async: true,
	                    crossDomain: true,

	                    
	                    success: function (token) {
	                        var url = paypal.checkout.urlPrefix +token;
	                    
	                        paypal.checkout.startFlow(url);
	                    },
	                    error: function (responseData, textStatus, errorThrown) {
	                        alert("Error in ajax post"+responseData.statusText);
	                    
	                        paypal.checkout.closeFlow();
	                    }
	                });
	            },
	            button: ['paypal_process_payment', 'payment_paypal_express_checkout']
	        });
	    };
{/if}
{literal}

function updateFormDatas()
{
	var nb = $('#quantity_wanted').val();
	var id = $('#idCombination').val();

	$('.paypal_payment_form input[name=quantity]').val(nb);
	$('.paypal_payment_form input[name=id_p_attr]').val(id);
}
	
$(document).ready( function() {

	if($('#in_context_checkout_enabled').val() != 1)
	{
		$('#payment_paypal_express_checkout').click(function() {
			$('#paypal_payment_form_cart').submit();
			return false;
		});
	}


	var jquery_version = $.fn.jquery.split('.');
	if(jquery_version[0]>=1 && jquery_version[1] >= 7)
	{
		$('body').on('submit',".paypal_payment_form", function () {
			updateFormDatas();
		});
	}
	else {
		$('.paypal_payment_form').live('submit', function () {
			updateFormDatas();
		});
	}

	function displayExpressCheckoutShortcut() {
		var id_product = $('input[name="id_product"]').val();
		var id_product_attribute = $('input[name="id_product_attribute"]').val();
		$.ajax({
			type: "GET",
			url: baseDir+'/modules/paypal/express_checkout/ajax.php',
			data: { get_qty: "1", id_product: id_product, id_product_attribute: id_product_attribute },
			cache: false,
			success: function(result) {
				if (result == '1') {
					$('#container_express_checkout').slideDown();
				} else {
					$('#container_express_checkout').slideUp();
				}
				return true;
			}
		});
	}

	$('select[name^="group_"]').change(function () {
		setTimeout(function(){displayExpressCheckoutShortcut()}, 500);
	});

	$('.color_pick').click(function () {
		setTimeout(function(){displayExpressCheckoutShortcut()}, 500);
	});

	if($('body#product').length > 0)
		setTimeout(function(){displayExpressCheckoutShortcut()}, 500);
	
	{/literal}
	{if isset($paypal_authorization)}
	{literal}
	
		/* 1.5 One page checkout*/
		var qty = $('.qty-field.cart_quantity_input').val();
		$('.qty-field.cart_quantity_input').after(qty);
		$('.qty-field.cart_quantity_input, .cart_total_bar, .cart_quantity_delete, #cart_voucher *').remove();
		
		var br = $('.cart > a').prev();
		br.prev().remove();
		br.remove();
		$('.cart.ui-content > a').remove();
		
		var gift_fieldset = $('#gift_div').prev();
		var gift_title = gift_fieldset.prev();
		$('#gift_div, #gift_mobile_div').remove();
		gift_fieldset.remove();
		gift_title.remove();
		
	{/literal}
	{/if}
	{if isset($paypal_confirmation)}
	{literal}
		
		$('#container_express_checkout').hide();
		if(jquery_version[0] >= 1 && jquery_version[1] >= 7)
		{
			$('body').on('click',"#cgv", function () {
				if ($('#cgv:checked').length != 0)
					$(location).attr('href', '{/literal}{$paypal_confirmation}{literal}');
			});
		}
		else {
			$('#cgv').live('click', function () {
				if ($('#cgv:checked').length != 0)
					$(location).attr('href', '{/literal}{$paypal_confirmation}{literal}');
			});

			/* old jQuery compatibility */
			$('#cgv').click(function () {
				if ($('#cgv:checked').length != 0)
					$(location).attr('href', '{/literal}{$paypal_confirmation}{literal}');
			});
		}

	{/literal}
	{else if isset($paypal_order_opc)}

	{literal}


		var jquery_version = $.fn.jquery.split('.');
		if(jquery_version[0]>=1 && jquery_version[1] >= 7)
		{
			$('body').on('click','#cgv', function() {
				if ($('#cgv:checked').length != 0)
					checkOrder();
			});
		}
		else
		{
			$('#cgv').live('click', function() {
				if ($('#cgv:checked').length != 0)
					checkOrder();
			});

			/* old jQuery compatibility */
			$('#cgv').click(function() {
				if ($('#cgv:checked').length != 0)
					checkOrder();
			});
		}

	{/literal}

	{/if}
	{literal}

	var modulePath = 'modules/paypal';
	var subFolder = '/integral_evolution';
	{/literal}
	{if $ssl_enabled}
		var baseDirPP = baseDir.replace('http:', 'https:');
	{else}
		var baseDirPP = baseDir;
	{/if}
	{literal}
	var fullPath = baseDirPP + modulePath + subFolder;
	var confirmTimer = false;
		
	if ($('form[target="hss_iframe"]').length == 0) {
		if ($('select[name^="group_"]').length > 0)
			displayExpressCheckoutShortcut();
		return false;
	} else {
		checkOrder();
	}

	function checkOrder() {
		if(confirmTimer == false)
			confirmTimer = setInterval(getOrdersCount, 1000);
	}

	{/literal}{if isset($id_cart)}{literal}
	function getOrdersCount() {


		$.get(
			fullPath + '/confirm.php',
			{ id_cart: '{/literal}{$id_cart}{literal}' },
			function (data) {
				if ((typeof(data) != 'undefined') && (data > 0)) {
					clearInterval(confirmTimer);
					window.location.replace(fullPath + '/submit.php?id_cart={/literal}{$id_cart}{literal}');
					$('p.payment_module, p.cart_navigation').hide();
				}
			}
		);
	}
	{/literal}{/if}{literal}
});

{/literal}
