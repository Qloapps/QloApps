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

$(document).ready(function() {
    $('.clicker.blue').on('click', function() {
        $('.login-form-wrap').toggle(200);
    });

    $('#cancelLogin').on('click', function() {
        $('.login-form-wrap').hide(200);
    });

    // manage dropdowns
    $(document).on('click', function (e) {
        const closestDropdown = $(e.target).closest('.dropdown');

        if (closestDropdown.length) {
            if ($(e.target).closest('.dropdown-toggle').length) {
                $('.dropdown').not(closestDropdown).removeClass('open');
                closestDropdown.toggleClass('open');
            }
        } else {
            $('.dropdown').removeClass('open');
        }
    });
});
