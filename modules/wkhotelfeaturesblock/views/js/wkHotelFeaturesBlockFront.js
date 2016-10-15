$(document).ready(function(){
	// For Block separator line in home page
	if ($(".home_amenities_wrapper").length) {
	  	// For width
	  	var window_width = $(window).width();
		var home_block_width = $(".home_amenities_wrapper").width() + 30;
		var width_in_neg = ((window_width - home_block_width)/2);
		//For Height()top
		amenities_block_top = $(".home_amenities_wrapper .home_block_desc_wrapper").height() + 50;
		$(".homeAmenitiesBlock").css({"left": -width_in_neg, "right": -width_in_neg, "top": amenities_block_top});
		// home_amenities_wrapper height
		var homeAmenitiesBlockHeight = $(".homeAmenitiesBlock").height();
		$(".home_amenities_wrapper").css("height", (homeAmenitiesBlockHeight + amenities_block_top)); 
		
		$(window).resize(function() {
			// For width
		  	var window_width = $(window).width();
			var home_block_width = $(".home_amenities_wrapper").width() + 30;
			var width_in_neg = ((window_width - home_block_width)/2);

			//For Height(top)
			amenities_block_top = $(".home_amenities_wrapper .home_block_desc_wrapper").height() + 50;
			$(".homeAmenitiesBlock").css({"left": -width_in_neg, "right": -width_in_neg, "top": amenities_block_top});

			// home_amenities_wrapper height
			var homeAmenitiesBlockHeight = $(".homeAmenitiesBlock").height();
			$(".home_amenities_wrapper").css("height", (homeAmenitiesBlockHeight + amenities_block_top)); 
		});
	}

	if ($(".homeAmenitiesBlock").length) {
		$(".amenity_desc_cont").each(function(index) {
			var amenityDescParentHeight = $(this).parent("div.amenity_content").height();
			$(this).css("height", amenityDescParentHeight);
		});

		$(window).resize(function(){
			$(".amenity_desc_cont").css("height", 0);
			$(".amenity_desc_cont").each(function(index) {
				var amenityDescParentHeight = $(this).parent("div.amenity_content").height();
				$(this).css("height", amenityDescParentHeight);
			});
		});
	}
});