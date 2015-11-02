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
$(document).ready(function(){
	resizeCatimg();
});

$(window).resize(function(){
	resizeCatimg();
});

$(document).on('click', '.lnk_more', function(e){
	e.preventDefault();
	$('#category_description_short').hide(); 
	$('#category_description_full').show(); 
	$(this).hide();
});

function resizeCatimg()
{
	var div = $('.content_scene_cat div:first');

	if (div.css('background-image') == 'none')
		return;

	var image = new Image;

	$(image).load(function(){
	    var width  = image.width;
	    var height = image.height;
		var ratio = parseFloat(height / width);
		var calc = Math.round(ratio * parseInt(div.outerWidth(false)));

		div.css('min-height', calc);
	});
	if (div.length)
		image.src = div.css('background-image').replace(/url\("?|"?\)$/ig, '');
}


// The button to increment the product value
$(document).on('click', '.cat_rm_quantity_up', function(e){
    e.preventDefault();
    fieldName = $(this).data('field-qty');
    var currentVal = parseInt($('input[name='+fieldName+']').val());

	/*if (!allowBuyWhenOutOfStock && quantityAvailable > 0)
		quantityAvailableT = quantityAvailable;
	else*/
	quantityAvailableT = $(this).closest('.room_cont').find(".cat_remain_rm_qty_"+$(this).data('room_id_product')).text();
    if (!isNaN(currentVal) && currentVal < quantityAvailableT)
    {
        $('input[name='+fieldName+']').val(currentVal + 1).trigger('keyup');
    }
    else
        $('input[name='+fieldName+']').val(quantityAvailableT);
});

 // The button to decrement the product value
$(document).on('click', '.cat_rm_quantity_down', function(e){
    e.preventDefault();
    fieldName = $(this).data('field-qty');
    var currentVal = parseInt($('input[name='+fieldName+']').val());
    if (!isNaN(currentVal) && currentVal > 1)
        $('input[name='+fieldName+']').val(currentVal - 1).trigger('keyup');
    else
        $('input[name='+fieldName+']').val(1);
});


$(document).on('keyup', '.cat_quantity_wanted', function(e)
{
	var qty_wntd = $(this).val();
	if (qty_wntd == '' || !$.isNumeric(qty_wntd))
	{
		$(this).val(1);
		qty_wntd = $(this).val();
	}
	$(this).val(parseInt(qty_wntd));
	if(parseInt(qty_wntd) < 1 || parseInt(qty_wntd) > parseInt($('.cat_remain_rm_qty_'+$(this).attr('id_room_product')).text()))
	{
		$(this).val($('.cat_remain_rm_qty_'+$(this).attr('id_room_product')).text());
	}
	else if (qty_wntd <= parseInt($('.cat_remain_rm_qty_'+$(this).attr('id_room_product')).text()) && qty_wntd >= 1)
	{
		$cornt_obj = $(this);
		$.ajax({
	    	type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: product_controller_url,
			dataType:'JSON',
			cache: false,
			data: {
				date_from:$('#check_in_time').val(),
				date_to:$('#check_out_time').val(),
				product_quantity_down:1,
				qty:qty_wntd,
				id_product:$(this).attr('id_room_product')
			},
			success: function(result)
			{
				if (result.msg == 'success')
				{
					total_price = result.total_price;
					total_price = parseFloat(total_price);

					price = formatCurrency(total_price, currency_format, currency_sign, currency_blank);

					$('.total_price_block p').text(price);
					$cornt_obj.closest(".rm_qty_cont").siblings(".rm_book_btn").attr('cat_rm_book_nm_days', result.num_days);
					$('.cat_remain_rm_qty_'+$cornt_obj.attr('id_room_product')).text(result.avail_rooms);
				}
				else if (result.msg == 'unavailable_quantity')
				{	
					$cornt_obj.val(result.avail_rooms);
				}
				else
				{
					alert(some_error_cond);					
				}
			}
	    });
	}
});
