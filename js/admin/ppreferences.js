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
    setFrontOccupancyView();
    $('#PS_FRONT_SEARCH_TYPE').on('change', setFrontOccupancyView);
    setBackOccupancyView();
    $('#PS_BACKOFFICE_SEARCH_TYPE').on('change', setBackOccupancyView);

    function setFrontOccupancyView()
    {
        if ($('#PS_FRONT_SEARCH_TYPE').val() == SEARCH_TYPE_OWS) {
            $('#PS_FRONT_OWS_SEARCH_ALGO_TYPE').closest('.form-group').show('fast');
            $('#PS_FRONT_ROOM_UNIT_SELECTION_TYPE').closest('.form-group').hide('fast');
        } else {
            $('#PS_FRONT_OWS_SEARCH_ALGO_TYPE').closest('.form-group').hide('fast');
            $('#PS_FRONT_ROOM_UNIT_SELECTION_TYPE').closest('.form-group').show('fast');
        }
    }

    function setBackOccupancyView()
    {
        if ($('#PS_BACKOFFICE_SEARCH_TYPE').val() == SEARCH_TYPE_OWS) {
            $('#PS_BACKOFFICE_OWS_SEARCH_ALGO_TYPE').closest('.form-group').show('fast');
            $('#PS_BACKOFFICE_ROOM_BOOKING_TYPE').closest('.form-group').hide('fast');
        } else {
            $('#PS_BACKOFFICE_OWS_SEARCH_ALGO_TYPE').closest('.form-group').hide('fast');
            $('#PS_BACKOFFICE_ROOM_BOOKING_TYPE').closest('.form-group').show('fast');
        }
    }

});
