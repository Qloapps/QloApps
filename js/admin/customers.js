/*
* Copyright since 2010 Webkul.
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
*  @copyright Since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

$(document).ready(function() {
    $(document).on('focusout', '#email', function() {
        var email = $.trim($('#email').val());
        if (email != '') {
            $.ajax({
                url: customer_controller_url,
                method: 'POST',
                dataType: 'json',
				data: {
                    ajax : 1,
                    id_customer: id_customer,
                    email: email,
                    action: 'verifyCustomerEmail'
                },
                success: function(response) {
                    if (!response.status) {
                        if ($('#email').closest('.input-group').parent().find('.customer_email_msg').length) {
                            $('.customer_email_msg').text(response.msg);
                        } else {
                            $('#email').closest('.input-group').parent().append('<p class="help-block customer_email_msg">'+ response.msg+'</p>');
                        }

                        $('.customer_email_msg').show();
                    } else {
                        $('.customer_email_msg').hide();
                    }
                }
			});
        }
    });
});