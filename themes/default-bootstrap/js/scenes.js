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
//global variables
var nb_move_available = null;
var current_move = 0;
var next_scene_is_at_right = true;

$(document).ready(function(){
	/* calcul nb of click to see every scenes */
	var ul_width = parseInt($('#scenes_list ul').width());
	var div_width = parseInt($('#scenes_list').width());
	nb_move_available = Math.ceil((ul_width-div_width)/ul_width)+1;
	if (nb_move_available < 2)
		$('#scenes .next').hide();
	
	/* set serialscroll parameters */
	$('#scenes_list').serialScroll({
		items:'a',
		duration:1000,
		lock:false,
		axis:'x',
		cycle:false,
		force:true,
		lazy:true,
		step:1,
		onBefore:onSceneMove
	});

	$('#scenes_list').trigger( 'goto', 0);

	$('#scenes .popover-button').each(function(){
		var id_product_scene = $(this).data('id_product_scene');
		if (id_product_scene)
		{
			$(this).click(function(e){
				e.preventDefault();
			});
			var htmlContent = $('#scene_products_cluetip_' + id_product_scene).html();
			$(this).popover({
				placement : 'bottom', //placement of the popover. also can use top, bottom, left or right
				trigger:'hover',
				title : false, //this is the top title bar of the popover. add some basic css
				html: 'true', //needed to show html of course
				content : htmlContent  //this is the content of the html box. add the image here or anything you want really.
			});
		}
	});

	$(document).on('click', '.prev', function(e){
		e.preventDefault();
		next_scene_is_at_right = false; 
		$(this).parent().next().trigger('stop').trigger('prev');
	});

	$(document).on('click', '.prev', function(e){
		e.preventDefault();
		next_scene_is_at_right = true;
		$(this).parent().prev().trigger('stop').trigger('next');
	});

	$(document).on('click', '.scene_thumb', function(e){
		e.preventDefault();
		loadScene($(this).attr('data-id_scene'));
	});
});

function loadScene(id_scene)
{
	$('#scenes').find('.screen_scene:visible').fadeTo(300, 0, function(){
		$(this).hide();
		$('#scenes').find('#screen_scene_' + id_scene).css('opacity', '0').show().fadeTo(500, 1);
	});
}

function onSceneMove()
{
	if (next_scene_is_at_right) 
		current_move++;
	else 
		current_move--;
	if (current_move === nb_move_available - 1)	
		$('#scenes .next').fadeOut();
	else 
		$('#scenes .next:hidden').fadeIn().css('display','block');
	if (current_move === 0) 
		$('#scenes .prev').fadeOut().css('display','block');
	else 
		$('#scenes .prev').fadeIn().css('display','block');
	return true;
}
