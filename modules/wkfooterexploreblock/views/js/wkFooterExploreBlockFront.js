$(document).ready(function()
{
	$(".jsFooterTraverseBlock").on("click", function() {
		var block = $(this).attr('data-block');
		$('html ,body').animate({
			scrollTop:$(block).offset().top}, 2000);
	});
});