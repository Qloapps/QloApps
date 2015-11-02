$(document).ready(function(){
	$("#date-compare").hide();
	$(".hotel_date").datepicker(
	{
		dateFormat: 'yy-mm-dd'
	});

	$(".ui-state-default").live("mouseenter", function() {
        console.log($(this).text()+"-"+$(".ui-datepicker-month",$(this).parents()).text()+"-"+$(".ui-datepicker-year",$(this).parents()).text());
    });
});