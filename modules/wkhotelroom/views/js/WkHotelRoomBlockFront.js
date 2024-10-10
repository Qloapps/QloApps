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

$(document).ready(function() {
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
