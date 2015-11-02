$(document).ready(function()
{
	$('#htl_features_link').on('click', function(){
		$('html ,body').animate({
			scrollTop:$('#features_block').offset().top}, 2000);
	});

	$('#htl_testimonial_link').on('click', function(){
		$('html ,body').animate({
			scrollTop:$('#testimonial_block').offset().top}, 2000);
	});
	
	$('#htl_contact_link').on('click', function(){
		$('html ,body').animate({
			scrollTop:$('#footer').offset().top}, 2000);
	});

	$('#htl_our_rooms_link').on('click', function(){
		$('html ,body').animate({
			scrollTop:$('#htmlcontent_home').offset().top}, 2000);
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