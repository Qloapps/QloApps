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
  	$('.htlInterior-owlCarousel .owl-carousel').owlCarousel({
  		loop:true,
	    nav:true,
	    navSpeed:1000,
	    navText:['<div class="nav-direction-wrapper"><div class="nav-direction-primary"><div class="nav-direction-secondary"><i class="icon-angle-left"></i></div></div></div>','<div class="nav-direction-wrapper"><div class="nav-direction-primary"><div class="nav-direction-secondary"><i class="icon-angle-right"></i></div></div></div>'],
	    dots:false,
	    items: 1,
	    autoHeight:true,
	    autoplay:true,
	    autoplaySpeed:1000,
	    autoplayTimeout:5000,
		autoplayHoverPause:true,
	    responsiveClass:true,
		rtl: language_is_rtl,
	    responsive:{
	        1200:{
	            items:3
	        },
	        480:{
	            items:2
	        },
	    }
	});

	$('.interiorbox').on('mouseenter', function(){
		$(this).find('div.interiorHoverBlockWrapper').show();
	});
	$('.interiorbox').on('mouseleave', function(){
		$(this).find('div.interiorHoverBlockWrapper').hide();
	});

    $("div.interiorbox").fancybox({
	    autoDimensions: true,
	    autoScale: true,
	    autoSize: true,
	    centerOnScroll: true,
	    height: 'auto',
	    scrolling: 'auto',
	    width: 'auto',
	    maxWidth: 700,
	    'hideOnContentClick': false,
	    helpers: {
		    overlay: {
		      locked: false
		    }
		}
	});
});