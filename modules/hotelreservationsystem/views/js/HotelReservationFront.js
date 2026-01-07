$(document).ready(function() {
    // For Block separator line in home page
	let prevBtn = $('<div>', { class: 'nav-direction-wrapper ' })
		.append($('<div>', { class: 'nav-direction-primary' })
			.append($('<div>', { class: 'nav-direction-secondary' }).append($('<i>', { class: 'icon-angle-left' }))
		)
	);
	let nextBtn = $('<div>', { class: 'nav-direction-wrapper' })
		.append($('<div>', { class: 'nav-direction-primary' })
			.append($('<div>', { class: 'nav-direction-secondary' }).append($('<i>', { class: 'icon-angle-right' }))
		)
	);
	$('.htlDisplayBlock-owlCarousel .owl-carousel').owlCarousel({
		loop: true,
		nav: (HOTEL_BLOCK_NAV_TYPE == SLIDER_NAV_TYPE_BUTTON),
		navSpeed: 1000,
		navText: [prevBtn, nextBtn],
		dots: (HOTEL_BLOCK_NAV_TYPE == SLIDER_NAV_TYPE_DOTS),
		items: 1,
		autoHeight: true,
		autoplay: true,
		autoplaySpeed: 1000,
		autoplayTimeout: 5000,
		autoplayHoverPause: true,
		responsiveClass: true,
		rtl: language_is_rtl,
		padding: 10,
		onInitialized: function (event) {
			var nav = $(event.target).find('.owl-nav');
			nav.find('.owl-prev').attr('tabindex', '-1');
            nav.find('.owl-next').attr('tabindex', '-1');
		},
	});
});