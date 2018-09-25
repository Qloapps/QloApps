/**
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

$(document).ready(function(){
	$(".wk_paypal_email").submit(function(){
		var reg = /^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z])+([a-zA-Z])+/;
		var wk_paypal_email = $("#wk_paypal_email").val();
		if(wk_paypal_email=='') {
			alert(blank_error);
			$('#wk_paypal_email').focus();
			return false;
		} else if (!reg.test(wk_paypal_email)) {
			alert(invalid_error);
			$('#wk_paypal_email').focus();
			return false;
		}
	});
});