$(document).ready(function(){
	$(".wk_paypal_email").submit(function(){
		var reg = /^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z])+([a-zA-Z])+/;
		var wk_paypal_email = $("#wk_paypal_email").val();
		if(wk_paypal_email=='') 
		{
			alert(blank_error);
			$('#wk_paypal_email').focus();
			return false;
		} 
		else if (!reg.test(wk_paypal_email))
		{
			alert(invalid_error);
			$('#wk_paypal_email').focus();
			return false;
		}
	});
});