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

	var prevBtn = $('<div>', { class: 'nav-direction-wrapper ' })
		.append($('<div>', { class: 'nav-direction-primary' })
			.append($('<div>', { class: 'nav-direction-secondary' }).append($('<i>', { class: 'icon-angle-left' }))
		)
	);
	var nextBtn = $('<div>', { class: 'nav-direction-wrapper' })
		.append($('<div>', { class: 'nav-direction-primary' })
			.append($('<div>', { class: 'nav-direction-secondary' }).append($('<i>', { class: 'icon-angle-right' }))
		)
	);
	$('.htlDisplayBlock-owlCarousel .owl-carousel').owlCarousel({
		loop:true,
	    nav:false,
	    navSpeed:1000,
		navText: [prevBtn, nextBtn],
	    dots:false,
	    items: 1,
	    autoHeight:true,
	    autoplay:true,
	    autoplaySpeed:1000,
	    autoplayTimeout:5000,
		autoplayHoverPause:true,
	    responsiveClass:true,
		rtl: language_is_rtl,
	});
});