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
    toggleRoomDescriptionExtras();
    $(window).on('resize', function() {
        removePreviousChanges();
        toggleRoomDescriptionExtras();
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

// Function is called to revert the changes made to the description on the window size change.
function removePreviousChanges()
{
    $(document).find('.htlRoomTypeDescText').each(function() {
        //Reverting the changes so we can reapply them according the new screen size.
        $(this).html($(this).parent().find('.htlRoomTypeDescTextOriginal').html());
        if (!$(this).hasClass('htlRoomTypeDescTextContainer')){
            $(this).addClass('htlRoomTypeDescTextContainer');
        }
    });
}
function addExtraTextToDescription(target)
{
    // Getting the number of displayed characters
    var show_char = getChars(target);
    var ellipses = "... ";
    var content = $(target).html();
    if (content.trim().length > show_char) {
        var a = content.trim().substr(0, show_char).replace('<p>' , ''); // getting the text that we want to show to the user, removed the first <p> tag.
        if (content.trim().substr(content.trim().length - 4, content.trim().length) == '</p>')  {
            content = content.trim().substr(0, content.trim().length - 4);// removed the last closing </p> tag.
        }
        var b = content.trim().substr(show_char - content.trim().length) // getting the text that will be toggled
        // Adding the read more and read less text with the ellipses to the original text.
        var html = a + "<span class='truncated'>" + ellipses + "</span><span class='read-extra-text'>"+ readMoreText +"</span><span class='truncated' style='display:none'>" + b + "</span><span class='read-extra-text' style='display:none'> "+ readLessText +"</span></span>";
        $(target).html('<p>' + html+ '</p>');
    }
}
function toggleRoomDescriptionExtras()
{
    $(document).find('.htlRoomTypeDescText').each(function() {
        // checking if the display:overFlow has been applied to the description.
        if ($(this).prop('scrollHeight') - $(this).prop('clientHeight') > 15) {
            // Only show read more and read less text if the text is wrapped.
            addExtraTextToDescription($(this)[0]);
        }
    });
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

    return displayedCharacters + 20;
}
