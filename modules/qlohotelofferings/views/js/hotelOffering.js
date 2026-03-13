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

$(document).ready(function(){
	let prevBtn = $('<div>', { class: 'nav-direction-wrapper ' })
		.append($('<div>', { class: 'nav-direction-primary' })
			.append($('<div>', { class: 'nav-direction-secondary' }).append($('<i>', { class: 'icon-angle-left' }))
		)
	);
	let nextBtn = $('<div>', { class: 'nav-direction-wrapper' })
		.append($('<div>', { class: 'nav-direction-primary' })
			.append($('<div>', { class: 'nav-direction-secondary' }).append($('<i>', { class: 'icon-angle-right' }))
		)
	);
    $('.htlOfferings-owlCarousel .owl-carousel').owlCarousel({
        loop: true,
		// nav: (HOTEL_TESIMONIAL_BLOCK_NAV_TYPE == SLIDER_NAV_TYPE_BUTTON),
		navSpeed: 1000,
		navText: [prevBtn, nextBtn],
		dots: true,
		items: 1,
		autoHeight: true,
		autoplay: true,
		autoplaySpeed: 1000,
		autoplayTimeout: 5000,
		autoplayHoverPause: true,
		responsiveClass: true,
		rtl: language_is_rtl,
		stagePadding: 50,
		responsive: {
			0: { items: 1, stagePadding: 20 },
			800: { items: 2, stagePadding: 90 }
		},
        onInitialized: function (event) {
			var nav = $(event.target).find('.owl-nav');
			nav.find('.owl-prev').attr('tabindex', '-1');
            nav.find('.owl-next').attr('tabindex', '-1');
		},
    });
});