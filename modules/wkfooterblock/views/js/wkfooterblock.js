$(document).ready(function(){
	$('#htl_sss_link').on('click', function(){
		$('html ,body').animate({
			scrollTop:$('#htmlcontent_home').offset().top}, 2000);
	});

});