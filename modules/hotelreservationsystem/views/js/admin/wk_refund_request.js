/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

$(document).ready(function() {
    $(document).on('change', '#refundTransactionAmount', function() {
		if ($(this).is(':checked')) {
			$(".refund_transaction_fields").show(200);
		} else {
            $(".refund_transaction_fields").hide(200);
        }
    });

    $('#id_refund_state').on('change', function() {
        if ($("#id_refund_state option:selected").attr('refunded') == 1) {
            $(".refunded_state_fields").show(200);
        } else {
            $(".refunded_state_fields").hide(200);
        }
    });

    $('#payment_methods').on('change', function() {
        if ($(this).val() == 0) {
            $(".other_payment_mode").show(200);
        } else {
            $(".other_payment_mode").hide(200);
        }
    });

    $('#refundTransactionAmount, #generateDiscount').click(function() {
        if ($(this).is(':checked')) {
            $('#refundTransactionAmount, #generateDiscount').prop('checked', false);
			$(this).prop('checked', true);
		}
        $('#refundTransactionAmount').change();
	});
});