/**
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

function setAmenitiesWrapperHeight() {
	// for width
	var window_width = $(window).width();
	var home_block_width = $('.home_amenities_wrapper').width() + 30;
	var width_in_neg = ((window_width - home_block_width)/2);

	// for height
	amenities_block_top = $('.home_amenities_wrapper .home_block_desc_wrapper').height() + 50;
	$('.homeAmenitiesBlock').css({'left': -width_in_neg, 'right': -width_in_neg, 'top': amenities_block_top});

	// home_amenities_wrapper height
	var homeAmenitiesBlockHeight = $('.homeAmenitiesBlock').height();
	$('.home_amenities_wrapper').css('height', (homeAmenitiesBlockHeight + amenities_block_top));
}

function setAmenitiesDescContHeight() {
	$('.amenity_desc_cont').each(function () {
		var amenityDescParentHeight = $(this).parent('div.amenity_content').height();
		$(this).css('height', amenityDescParentHeight);
	});
}

$(document).ready(function() {
	if ($('.homeAmenitiesBlock').length) {
		setAmenitiesDescContHeight();
	}
});

$(window).load(function() {
	if ($('.home_amenities_wrapper').length) {
		setAmenitiesWrapperHeight();
	}
});

$(window).resize(function() {
	if ($('.home_amenities_wrapper').length) {
		setAmenitiesWrapperHeight();
	}

	if ($('.homeAmenitiesBlock').length) {
		$('.amenity_desc_cont').css('height', 0);
		setAmenitiesDescContHeight();
	}
});
