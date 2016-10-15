$(document).ready(function()
{
	$(".jsNaviLink").on("click", function() {
		var block = $(this).attr('data-block');
		$('html ,body').animate({
			scrollTop:$(block).offset().top}, 2000);
	});
	
	$(".nav_toggle").on("click", function()
	{
		var menu_cont = $("#menu_cont");
		if (menu_cont.hasClass("menu_cont_right"))
			menu_cont.removeClass("menu_cont_right").addClass("menu_cont_left");
	});

	$(".close_navbar").on("click", function()
	{
		var menu_cont = $("#menu_cont");
		if (menu_cont.hasClass("menu_cont_left"))
			menu_cont.removeClass("menu_cont_left").addClass("menu_cont_right");
	});
});