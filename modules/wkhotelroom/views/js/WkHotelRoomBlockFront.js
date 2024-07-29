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
        cleanDescriptions();
        applyChangesToDescription();
    });

    $(document).on('click', '.read-extra-text', function(e) {
        e.preventDefault();
        $(e.target).closest(".htlRoomTypeDescText").find(".read-extra-text").toggle();
        $(e.target).closest(".htlRoomTypeDescText").find(".truncated").toggle();
        if ($(e.target).closest(".htlRoomTypeDescText").hasClass('htlRoomTypeDescTextContainer')){
            $(e.target).closest(".htlRoomTypeDescText").removeClass('htlRoomTypeDescTextContainer');
        } else {
            $(e.target).closest(".htlRoomTypeDescText").addClass('htlRoomTypeDescTextContainer');
        }
    });
});
function cleanDescriptions() {
    $(document).find('.htlRoomTypeDescText').each(function() {
        $(this).addClass('htlRoomTypeDescTextContainer');
        $(this).html($(this).parent().find('.htlRoomTypeDescOriginal').html());
    });
}

function applyChangesToDescription() {
    $(document).find('.htlRoomTypeDescText').each(function() {
        // checking if the display:overFlow has been applied to the description.
        if ($(this).prop('scrollHeight') - $(this).prop('clientHeight') > 15) {
            // Only show read more and read less text if the text is wrapped.
            initToggleOptions($(this)[0]);
        }
    });
}

function initToggleOptions(target) {
    // Getting the number of displayed characters
    var show_char = getChars(target);
    var ellipses = "... ";
    var content = $(target).html();
    if (content.trim().length > show_char) {
        // Dividing the text into two parts so that we are able to differentiate the displayed text from hidden text.
        var a = content.trim().substr(0, show_char);
        var b = content.trim().substr(show_char - content.trim().length);
        // Adding the read more and read less text with the ellipses to the original text.
        var html = a + "<span class='truncated'>" + ellipses + "</span><span class='read-extra-text'>"+ readMoreText +"</span><span class='truncated' style='display:none'>" + b + "</span><span class='read-extra-text' style='display:none'> "+ readLessText +"</span></span>";
        $(target).html(html);
    }
}

// Getting the number of characters displayed to the user.
function getChars(target) {
    var style = window.getComputedStyle(target, null);
    // Getting the size of the container displayed to the user, this will ignore the overflow: hidden text.
    var line_height = parseInt(style.getPropertyValue("line-height"));
    var font_size = parseInt(style.getPropertyValue("font-size"));
    var height = parseInt(style.getPropertyValue("height"));
    var width = parseInt(style.getPropertyValue("width"));

    if(isNaN(line_height)) line_height = font_size * 1.2;
    // Counting the number of the lines displayed to the user by the display:overFlow property, since this can vary depending the screen size.
    var lines = Math.ceil(height / line_height) + 1;
    // Counting the number of the characters in the lines
    var displayedCharacters = $(target).text().split('').slice(0, lines * (width / parseInt(font_size))).join('').length;

    if ($(document).width() > 991) {
        displayedCharacters += 30;
    } else if ($(document).width() > 768) {
        displayedCharacters += 34;
    } else if ($(document).width() > 570) {
        displayedCharacters += 25;
    } else if ($(document).width() > 444) {
        displayedCharacters += 20;
    } else {
        displayedCharacters += 15;
    }

    return displayedCharacters;
}