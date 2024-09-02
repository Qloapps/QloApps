/**
* Copyright since 2007 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright since 2007 Webkul IN
* @license LICENSE.txt
*/

$(document).ready(function() {

    // For Block separator line in home page
    if ($(".home_block_container").length) {
        var window_width = $(window).width();
        var home_block_width = $(".home_block_container").width();
        var width_in_neg = ((window_width - home_block_width) / 2);
        $(".home_block_seperator").css({
            "left": -width_in_neg,
            "right": -width_in_neg
        });
        $(window).resize(function() {
            var window_width = $(window).width();
            var home_block_width = $(".home_block_container").width();
            var width_in_neg = ((window_width - home_block_width) / 2);
            $(".home_block_seperator").css({
                "left": -width_in_neg,
                "right": -width_in_neg
            });
        });
    }
});