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

    $('#generateCreditSlip, #generateDiscount').click(function() {
        if ($(this).is(':checked')) {
            $('#generateCreditSlip, #generateDiscount').prop('checked', false);
			$(this).prop('checked', true);
		}
        $('#generateCreditSlip').change();
	});

    $(document).on('keyup', '#order_return_form .table input[name^="refund_amounts"]', function() {
        let refundAmountInputs = $('#order_return_form .table input[name^="refund_amounts"]');

        let disableRefundOptions = false;
        $(refundAmountInputs).each(function(index, element) {
            let val = parseFloat($(element).val().trim());
            if (isNaN(val)) { // if at least one amount input is empty
                disableRefundOptions = true;
                return;
            }
        });

        if (!disableRefundOptions) {
            let hasAllZero = true;
            $(refundAmountInputs).each(function(index, element) {
                let val = parseFloat($(element).val().trim());
                if (val != 0) {
                    hasAllZero = false;
                    return;
                }
            });

            if (hasAllZero) { // if all amount inputs are 0
                disableRefundOptions = true;
            }
        }

        $('#generateCreditSlip, #refundTransactionAmount, #generateDiscount').attr('disabled', disableRefundOptions);
    });
});