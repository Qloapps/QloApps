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
	    nav: htlInteriorSliderArrows,
	    navText: [
	        '<div class="nav-direction-wrapper"><div class="nav-direction-primary"><div class="nav-direction-secondary"><i class="icon-angle-left"></i></div></div></div>',
	        '<div class="nav-direction-wrapper"><div class="nav-direction-primary"><div class="nav-direction-secondary"><i class="icon-angle-right"></i></div></div></div>'
	    ],
	    dots: htlInteriorSliderDots,
	    items: 1,
	    margin: 8,
	    responsive: {576: {items: 2}},
	    autoHeight:true,
	    autoplay: htlInteriorSliderAutoplay,
	    autoplaySpeed:1000,
	    autoplayTimeout: htlInteriorSliderInterval,
		autoplayHoverPause:true,
	    responsiveClass:true,
		rtl: language_is_rtl
	});

	$('.interiorbox').on('mouseenter', function(){
		$(this).find('div.interiorHoverBlockWrapper').show();
	});
	$('.interiorbox').on('mouseleave', function(){
		$(this).find('div.interiorHoverBlockWrapper').hide();
	});

    // gallery lightbox: fancybox with a thumbnail strip and round prev/next buttons at the bottom
    var $thumbs = null;

    function buildThumbs() {
        var group = $.fancybox.group || [];
        if (group.length < 2) {
            return;
        }
        $thumbs = $('<div class="htl-gal-thumbs"><button type="button" class="htl-gal-thumbs__nav htl-gal-thumbs__prev" aria-label="Previous"><i class="icon-angle-left" aria-hidden="true"></i></button><div class="htl-gal-thumbs__track"></div><button type="button" class="htl-gal-thumbs__nav htl-gal-thumbs__next" aria-label="Next"><i class="icon-angle-right" aria-hidden="true"></i></button></div>');
        var $track = $thumbs.find('.htl-gal-thumbs__track');
        $.each(group, function(i, item) {
            $('<button type="button" class="htl-gal-thumbs__item" tabindex="-1"></button>')
                .attr('aria-label', (i + 1) + ' / ' + group.length)
                .append($('<img alt="">').attr('src', item.href))
                .on('click', function() { this.blur(); $.fancybox.jumpto(i); })
                .appendTo($track);
        });
        $thumbs.find('.htl-gal-thumbs__prev').on('click', function() { this.blur(); $.fancybox.prev(); });
        $thumbs.find('.htl-gal-thumbs__next').on('click', function() { this.blur(); $.fancybox.next(); });
        $thumbs.appendTo('body');
    }

    function markActiveThumb() {
        if (!$thumbs) {
            return;
        }
        var $items = $thumbs.find('.htl-gal-thumbs__item').removeClass('is-active');
        var $active = $items.eq($.fancybox.current.index).addClass('is-active');
        var track = $thumbs.find('.htl-gal-thumbs__track').get(0);
        if ($active.length && track) {
            track.scrollLeft = $active.get(0).offsetLeft - (track.clientWidth - $active.outerWidth()) / 2;
        }
    }

    // options of the dark gallery with a thumbnail strip
    var thumbsGalleryOptions = {
        padding: 0,
        margin: [48, 48, 190, 48],
        wrapCSS: 'htl-gallery-fancybox',
        arrows: false,
        helpers: {overlay: {locked: false, css: {background: 'rgba(43, 43, 43, 0.9)'}}},
        beforeLoad: function() {
            var compact = window.innerWidth < 768 || window.innerHeight < 500;
            this.margin = window.innerHeight < 500 ? [36, 12, 84, 12] : (compact ? [48, 12, 110, 12] : [48, 48, 190, 48]);
        },
        afterLoad: function() {
            // photos are shown as large as the screen allows (up to 589px), not at their natural size
            var smallW = window.innerWidth < 768;
            var shortH = window.innerHeight < 500;
            var maxH = Math.min(window.innerHeight - (shortH ? 120 : (smallW ? 158 : 238)), 589);
            var maxW = window.innerWidth - (shortH || smallW ? 24 : 96);
            var ratio = this.width / this.height;
            var height = maxH;
            var width = height * ratio;
            if (width > maxW) {
                width = maxW;
                height = width / ratio;
            }
            this.width = Math.round(width);
            this.height = Math.round(height);
        },
        beforeShow: function() {
            $('.fancybox-overlay').addClass('htl-gallery-overlay');
            if (!$thumbs) {
                buildThumbs();
            }
        },
        afterShow: markActiveThumb,
        afterClose: function() {
            if ($thumbs) {
                $thumbs.remove();
                $thumbs = null;
            }
        }
    };

    // options of the light gallery with the next and previous arrows on the photo
    var lightGalleryOptions = {
        wrapCSS: 'htl-light-fancybox',
        maxWidth: 700,
        nextEffect: 'fade',
        prevEffect: 'fade',
        helpers: {overlay: {locked: false}}
    };

    /**
     * Opens the photos of a slider in a lightbox when one is clicked.
     * Usage: $('.owl-carousel').interiorBoxGallery({thumbs: true});
     * thumbs true: dark gallery with a thumbnail strip, false: light gallery with arrows.
     */
    $.fn.interiorBoxGallery = function(options) {
        var settings = $.extend({thumbs: true, itemSelector: '.interiorbox[href]'}, options);

        return this.each(function() {
            var $slider = $(this);
            // photos are listed once, the slider also holds cloned copies when it loops
            $slider.on('click', settings.itemSelector, function(e) {
                e.preventDefault();
                var items = [];
                var index = 0;
                var clicked = $(e.currentTarget).attr('href');
                $slider.find('.owl-item:not(.cloned) ' + settings.itemSelector).not('.interiorbox-filler').each(function(i) {
                    items.push({href: $(this).attr('href'), title: $(this).attr('title') || ''});
                    if ($(this).attr('href') === clicked) {
                        index = i;
                    }
                });
                $.fancybox.open(items, $.extend(true, {
                    index: index,
                    type: 'image',
                    openEffect: 'fade',
                    closeEffect: 'fade',
                    helpers: {title: {type: 'outside'}}
                }, settings.thumbs ? thumbsGalleryOptions : lightGalleryOptions));
            });
        });
    };

    $('.htlInterior-owlCarousel .owl-carousel').interiorBoxGallery({thumbs: true});
});
