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

/**
 * Keeps the items of a room card row (featured features, amenity icons) on one line. Items that do not fit are
 * hidden whole, so a lone separator or icon is never left at the end, and a "more" marker is added at the end.
 *
 * @param {jQuery} $slider slider holding the room cards
 * @param {Object} row
 * @param {string} row.selector  the row element
 * @param {string} row.more      class of the "more" marker
 * @param {function(number): string} row.moreText  text of the marker, gets the number of hidden items
 * @param {boolean} [row.partial]  show the first hidden item shortened with an ellipsis when enough room is left
 */
function fitRoomCardRow($slider, row) {
    var MIN_PARTIAL_WIDTH = 100; // narrowest width a shortened item is shown in

    $slider.find(row.selector).each(function() {
        var $row = $(this);
        var $items = $row.children(':not(.' + row.more + ')').css({display: '', maxWidth: ''});
        var fits = function() {
            return $row.prop('scrollWidth') <= $row.innerWidth();
        };

        $row.children('.' + row.more).remove();
        if (fits()) {
            return;
        }

        // the marker takes room too, so it is added before the items are hidden
        var $more = $('<span class="' + row.more + '"></span>').appendTo($row);
        var shown = $items.length;

        while (shown > 1 && !fits()) {
            $items.eq(--shown).hide();
            $more.html(row.moreText($items.length - shown));
        }

        var $next = $items.eq(shown);
        var $last = $items.eq(shown - 1);
        var room = Math.floor($row.offset().left + $row.innerWidth() - ($last.offset().left + $last.outerWidth(true)));

        if (row.partial && $next.length && room >= MIN_PARTIAL_WIDTH) {
            $more.remove();
            $next.css({display: 'inline-block', maxWidth: room - 4, overflow: 'hidden', textOverflow: 'ellipsis'});
        }
    });
}

/** Fits the featured features and the amenity icons of every room card of the slider. */
function fitRoomCards($slider) {
    fitRoomCardRow($slider, {
        selector: '.htlRoomCardFeatured',
        more: 'htlRoomFeatureMore',
        moreText: function() { return '&hellip;'; },
        partial: true
    });
    fitRoomCardRow($slider, {
        selector: '.htlRoomCardAmen',
        more: 'htlRoomAmenMore',
        moreText: function(hidden) { return '+' + hidden; }
    });
}

$(document).ready(function() {
    $('.htlRoom-owlCarousel .owl-carousel').on('initialized.owl.carousel refreshed.owl.carousel resized.owl.carousel', function() {
        fitRoomCards($(this));
    }).owlCarousel({
        loop: true,
        nav: true,
        navText: ['<i class="icon-angle-left"></i>', '<i class="icon-angle-right"></i>'],
        dots: false,
        autoplay: true,
        autoplaySpeed: 1000,
        margin: 24,
        items: 1,
        rtl: typeof language_is_rtl != 'undefined' && language_is_rtl,
        responsive: {
            768: { items: 2 },
            992: { items: 3 }
        }
    });
    // fonts and icons can change the widths after the page is shown
    $(window).on('load', function() {
        fitRoomCards($('.htlRoom-owlCarousel .owl-carousel'));
    });
    applyChangesToDescription();
    $(window).on('resize', function() {
        resetDescriptionChanges();
        applyChangesToDescription();
    });

    $(document).on('click', '.htlRoomTypeDescReadmore', function(e) {
        e.preventDefault();
        var target = (e.target).closest(".htlRoomTypeDescText");
        $(target).removeClass('htlRoomTypeDescTextContainer');
        $(target).html($(e.target).closest(".hotelRoomDescContainer").find('.htlRoomTypeDescOriginal').html());
        $(target).append($('.htlRoomTypeDescExtras').html());
        $(target).find('.htlRoomTypeDescReadless').show().css('display', 'inline-block');
    });

    $(document).on('click', '.htlRoomTypeDescReadless', function(e) {
        e.preventDefault();
        var target = (e.target).closest(".htlRoomTypeDescText");
        $(target).addClass('htlRoomTypeDescTextContainer');
        initToggleOptions($(target));
    });
});

function resetDescriptionChanges() {
    $(document).find('.htlRoomTypeDescText').each(function() {
        $(this).addClass('htlRoomTypeDescTextContainer');
        $(this).html($(this).parent().find('.htlRoomTypeDescOriginal').html());
    });
}

function applyChangesToDescription() {
    $(document).find('.htlRoomTypeDescText').each(function() {
        // checking if the display:overFlow has been applied to the description. using 14px since font size is 14px.
        if ($(this).prop('scrollHeight') - $(this).prop('clientHeight') > 14) {
            initToggleOptions($(this)[0]);
        }
    });
}

function initToggleOptions(target) {
    truncatedText = truncateText(target);
    $(target).text(truncatedText);
    $(target).append($('.htlRoomTypeDescExtras').html());
    $(target).find('.htlRoomTypeDescReadmore').first().show().css('display', 'inline-block');
}

function truncateText(target) {
    var lineHeight = parseInt($(target).css('line-height'), 10);
    var containerHeight = lineHeight * 3;

    text = $(target).text();
    while ($(target).prop('scrollHeight') > containerHeight) {
        text = text.slice(0, -10);
        $(target).text(text + ' ... ' + $('.htlRoomTypeDescReadmore').first().text());
    }

    return text;
}
