/*
 * 2007-2014 PrestaShop
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
 *  @copyright  2007-2014 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

function paypal_usa_init()
{
	if ($('.colorSelector').length) {
		$('.colorSelector').each(function() {
			var obj = $(this);
			obj.css('background-color', obj.val());
			$(this).ColorPicker({
				color: '#8f478f',
				onShow: function(colpkr) {
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function(colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function(hsb, hex, rgb) {
					obj.val('#' + hex);
					obj.css('background-color', '#' + hex);
				}
			});
		});
	}

	var height = 0;
	$('.fixCol').each(function() {
		if (height < $(this).height())
			height = $(this).height();
	});

	$('.fixCol').css({'height': $('.fixCol').css('height', height + 40 + 'px')});

	$('.paypal-usa-threecol input:radio, .paypal-usa-onecol input:checkbox').live('click', function() {

		if ($(this).prop('type') === 'radio')
			$('.paypal-usa-product').removeClass('paypal-usa-product-active');
		
		if ($(this).is(':checked')) {
			if ($(this).is('#paypal_usa_payment_advanced, #paypal_usa_payflow_link')) 
				$('#paypal-usa-advanced-settings').parent('form').fadeIn(500);
			
			if ($(this).is('#paypal_usa_express_checkout')) 
				$('#paypal_usa_express_checkout_config').fadeIn(500);
			
			$(this).parent().parent().addClass('paypal-usa-product-active');
		} else {
			if ($(this).is('#paypal_usa_payment_advanced, #paypal_usa_payflow_link')) 
				$('#paypal-usa-advanced-settings').fadeOut(500);
			
			if ($(this).is('#paypal_usa_express_checkout')) 	
				$('#paypal_usa_express_checkout_config').fadeOut(500);
			
			$(this).parent().parent().removeClass('paypal-usa-product-active');
		}
		if ($('#paypal_usa_payment_advanced, #paypal_usa_payflow_link').is(':checked'))
		{
			$('#paypal-usa-advanced-settings').fadeIn(500);
			$('#paypal-usa-advanced-settings').parent('form').fadeIn(500);
		}
		return true;
	});

	$('fieldset input:button').live('click', function() {
		$('input[name=paypal_usa_products]').prop('checked', false);
		$('#paypal_usa_express_checkout').prop('checked', true);
		$('.paypal-usa-product').removeClass('paypal-usa-product-active');
		$('#paypal_usa_express_checkout').parent().parent().addClass('paypal-usa-product-active');
		$('#paypal_usa_express_checkout_config').fadeIn(500);
		$('#paypal-usa-advanced-settings').parent('form').fadeOut(500);
	});
}

$(document).ready(function(){
	paypal_usa_init();
});