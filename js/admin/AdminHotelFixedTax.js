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

$(document).ready(function () {
    var DURATION = 300;

    function applyOccupancyState(animate) {
        var show = $('input[name="occupancy_based_price"]:checked').val() === '1';
        if (animate) {
            show ? $('.wk-occupancy-sub-fields').slideDown(DURATION) : $('.wk-occupancy-sub-fields').slideUp(DURATION);
        } else {
            $('.wk-occupancy-sub-fields').toggle(show);
        }
    }

    applyOccupancyState(false);

    $('input[name="occupancy_based_price"]').on('change', function () {
        applyOccupancyState(true);
    });
});
