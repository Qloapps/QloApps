/**
* 2010-2021 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2021 Webkul IN
* @license LICENSE.txt
*/

$(document).ready(function() {
    $('#pp_refund_type').change(function() {
        var refund_type = $(this).val();
        if (refund_type == 2) {
            $('#pp-amount-block').show();
            $('#pp_refund_amount').attr('required', true);
        } else {
            $('#pp_refund_amount').val('');
            $('#pp-amount-block').hide();
            $('#pp_refund_amount').attr('required', false);
        }
    });

    $(document).on('submit', '#refund_form', function() {
        $('#refund_form_submit_btn').attr('disabled', true);
        return true;
    });
});