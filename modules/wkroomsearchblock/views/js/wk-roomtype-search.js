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
    $(document).on('click', '.modify_roomtype_search_btn', function() {
        $('.header-rmsearch-wrapper').show();
        $('.header-rmsearch-details-wrapper').hide();
    });
    $(document).on('click', '.close_room_serach_wrapper', function(event) {
        event.preventDefault();
        $('.header-rmsearch-wrapper').hide();
        $('.header-rmsearch-details-wrapper').show();
    });
});