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

    initPriceTooltip();
});

function initPriceTooltip() {
    if ($('.order-price-info').length) {
        $('.order-price-info').each(function () {
            $(this).tooltip({
                content: $(this).closest('dd').find('.price-info-container').html(),
                items: 'span',
                trigger: 'hover',
                tooltipClass: 'price-tootip',
                open: function (event, ui) {
                    if (typeof (event.originalEvent) === 'undefined') {
                        return false;
                    }

                    var $id = $(ui.tooltip).attr('id');

                    // close any lingering tooltips
                    if ($('div.ui-tooltip').not('#' + $id).length) {
                        return false;
                    }
                },
                close: function (event, ui) {
                    ui.tooltip.hover(function () {
                        $(this).stop(true).fadeTo(400, 1);
                    },
                    function () {
                        $(this).fadeOut('400', function () {
                            $(this).remove();
                        });
                    });
                }
            });
        });
    }
}