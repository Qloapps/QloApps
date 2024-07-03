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

$(document).ready(function(){
    $(document).on('click', '.open_rooms_extra_services_panel', function(e) {
        var idProduct = $(this).data('id_product');
        var idOrder = $(this).data('id_order');
        var dateFrom = $(this).data('date_from');
        var dateTo = $(this).data('date_to');
        var action = $(this).data('action');
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: action,
            dataType: 'json',
            cache: false,
            data: {
                date_from: dateFrom,
                date_to: dateTo,
                id_product: idProduct,
                id_order: idOrder,
                action: 'getRoomTypeBookingDemands',
                method: 'getRoomTypeBookingDemands',
                ajax: true,
                token: static_token
            },
            success: function(result) {
                if (result.extra_demands) {
                    $('#rooms_extra_services').html('');
                    $('#rooms_extra_services').append(result.extra_demands);
                }
                $.fancybox({
                    href: "#rooms_extra_services",
                    autoSize : true,
                    autoScale : true,
                    maxWidth : '100%',
                    wrapCSS: 'fancybox-extra-services',
                    padding: 0,
                    helpers: {
                        overlay: {
                            css : {background: 'rgba(0, 0, 0, 0.8)'}
                        }
                    },
                    'hideOnContentClick': false,
                    afterClose: function() {
                        if (result.reload) {
                            // reload so that changes prices will reflect everywhere
                            location.reload();
                        }
                    },
                });
            },
        });
    });
});