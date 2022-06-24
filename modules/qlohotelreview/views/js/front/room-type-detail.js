/**
* 2010-2022 Webkul.
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
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
*/

function initRaty(path) {
    $('.room_hotel_name_block .raty').html(''); // reset first to avoid star duplications
    $.extend($.raty, { path: path });
    $('.room_hotel_name_block .raty').raty({readOnly: true, hints: null, noRatedMsg: '0'});
}

$(document).ready(function () {
    if (typeof qlo_hotel_review_rtd_js_vars === 'object' && qlo_hotel_review_rtd_js_vars.raty_img_path) {
        initRaty(qlo_hotel_review_rtd_js_vars.raty_img_path);
    }
});
