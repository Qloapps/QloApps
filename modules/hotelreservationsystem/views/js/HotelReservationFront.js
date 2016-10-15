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