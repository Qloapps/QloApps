/**
* Since 2010 Webkul.
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

function manageFieldMaximumAttempts(state) {
    if (state) {
        $('[name="PS_ALLOW_EMP_MAX_ATTEMPTS"]').closest('.form-group').show(200);
    } else {
        $('[name="PS_ALLOW_EMP_MAX_ATTEMPTS"]').closest('.form-group').hide(200);
    }
}

$(document).ready(function() {
    // manage fields visibility
    $(document).on('change', '[name="PS_SHOP_ENABLE"]', function () {
        if (parseInt($('[name="PS_SHOP_ENABLE"]:checked').val())) {
            $('[name="PS_ALLOW_EMP"]').closest('.form-group').hide(200);
            manageFieldMaximumAttempts(false);
        } else {
            $('[name="PS_ALLOW_EMP"]').closest('.form-group').show(200);
            manageFieldMaximumAttempts(parseInt($('[name="PS_ALLOW_EMP"]:checked').val()));
        }
    });

    $(document).on('change', '[name="PS_ALLOW_EMP"]', function () {
        manageFieldMaximumAttempts(parseInt($('[name="PS_ALLOW_EMP"]:checked').val()));
    });
});
