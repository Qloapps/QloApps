/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
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

	if ($('.home_amenities_wrapper').length) {
		setAmenitiesWrapperHeight();
	}
});

$(window).resize(function() {
	if ($('.homeAmenitiesBlock').length) {
		$('.amenity_desc_cont').css('height', 0);
		setAmenitiesDescContHeight();
	}

	if ($('.home_amenities_wrapper').length) {
		setAmenitiesWrapperHeight();
	}
});
