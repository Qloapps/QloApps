$(document).ready(function(){
  	$('.htlInterior-owlCarousel .owl-carousel').owlCarousel({
  		loop:true,
	    nav:true,
	    navSpeed:1000,
	    navText:['<div class="nav-direction-wrapper"><div class="nav-direction-primary"><div class="nav-direction-secondary"><i class="icon-angle-left"></i></div></div></div>','<div class="nav-direction-wrapper"><div class="nav-direction-primary"><div class="nav-direction-secondary"><i class="icon-angle-right"></i></div></div></div>'],
	    dots:false,
	    items: 1,
	    autoHeight:true,
	    autoplay:true,
	    autoplaySpeed:1000,
	    autoplayTimeout:5000,
		autoplayHoverPause:true,
	    responsiveClass:true,
	    responsive:{
	        1200:{
	            items:3
	        },
	        480:{
	            items:2
	        },
	    }
	});

	$('.interiorbox').on('mouseenter', function(){
		$(this).find('div.interiorHoverBlockWrapper').show();
	});
	$('.interiorbox').on('mouseleave', function(){
		$(this).find('div.interiorHoverBlockWrapper').hide();
	});

    $("div.interiorbox").fancybox({
	    autoDimensions: true,
	    autoScale: true,
	    autoSize: true,
	    centerOnScroll: true,
	    height: 'auto',
	    scrolling: 'auto',
	    width: 'auto',
	    maxWidth: 700,
	    'hideOnContentClick': false,
	    helpers: {
		    overlay: {
		      locked: false
		    }
		}
	});
});