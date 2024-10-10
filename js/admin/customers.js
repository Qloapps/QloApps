/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

$(document).ready(function() {
    $(document).on('focusout', '#email', function() {
        var email = $.trim($('#email').val());
        if (email != '') {
            $('.customer_email_msg').hide();
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
                            $('#email').closest('.input-group').parent().append('<p class="text-danger customer_email_msg">'+ response.msg+'</p>');
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
