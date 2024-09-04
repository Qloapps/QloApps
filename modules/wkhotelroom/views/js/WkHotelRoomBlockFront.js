/**
* Since 2010 Webkul.
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
*  @copyright Since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
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
