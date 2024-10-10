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
