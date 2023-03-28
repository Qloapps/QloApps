/**
* 2010-2023 Webkul.
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
* @copyright 2010-2023 Webkul IN
* @license LICENSE.txt
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
