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
     resetDescriptions();
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

function resetDescriptions() {
    $(document).find('.htlRoomTypeDescText').each(function() {
        $(this).addClass('htlRoomTypeDescTextContainer');
        $(this).html($(this).parent().find('.htlRoomTypeDescOriginal').html());
    });
}

function applyChangesToDescription() {
    $(document).find('.htlRoomTypeDescText').each(function() {
        // checking if the display:overFlow has been applied to the description.
        if ($(this).prop('scrollHeight') - $(this).prop('clientHeight') > 14) {
            // Only show read more and read less text if the text is wrapped.
            initToggleOptions($(this)[0]);
        }
    });
}

function initToggleOptions(target) {
    // Getting the number of displayed characters
    var show_char = getVisibleWords(target);
    var ellipses = "... ";
    var content = $(target).html();
    show_char = show_char - (readMoreText.length + ellipses.length + 5); // adding extra 5 chars of space
    if (content.trim().length > show_char) {
        // Dividing the text into two parts so that we are able to differentiate the displayed text from hidden text.
        var visibleText = content.trim().substr(0, show_char);
        var hiddenText = content.trim().substr(show_char - content.trim().length);
        // Adding the read more and read less text with the ellipses to the original text.
        var html = visibleText + "<span class='truncated'>" + ellipses + "</span><span class='read-extra-text'>"+ readMoreText +"</span><span class='truncated' style='display:none'>" + hiddenText + "</span><span class='read-extra-text' style='display:none'> "+ readLessText +"</span></span>";
        $(target).html(html);
    }
}

function getVisibleWords(target) {
    var style = window.getComputedStyle(target, null);
    var width = parseInt(style.getPropertyValue("width"));
    var lines = 3; // since we are using line clamp to display only three lines.
    var text = $(target).text();
    var words = text.split(/\s+/); // Split text into words based on whitespace
    var visibleWords = [];
    var currentLineWidth = 0;

    for (var i = 0; i < words.length; i++) {
        var word = words[i];
        var wordWidth = getTextWidth(word, style);

        if (currentLineWidth + wordWidth <= width) {
            visibleWords.push(word);
            currentLineWidth += wordWidth + getTextWidth(' ', style); // Add space width
        } else {
            lines--;
            if (lines <= 0) break;

            visibleWords.push(word);
            currentLineWidth = wordWidth + getTextWidth(' ', style); // Start a new line with the current word
        }
    }

    return visibleWords.join(' ').length;
}

function getTextWidth(text, style) {
    var canvas = document.createElement('canvas');
    var context = canvas.getContext('2d');
    context.font = `${style.getPropertyValue("font-weight")} ${style.getPropertyValue("font-size")} ${style.getPropertyValue("font-family")}`;
    return context.measureText(text).width;
}
