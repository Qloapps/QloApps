/*
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
    if (typeof window.initHotelNamesTooltips === 'undefined') {
        window.initHotelNamesTooltips = function () {
            if (typeof $.fn.tooltip === 'undefined' || typeof $.ui === 'undefined' || typeof $.ui.tooltip === 'undefined') {
                return;
            }
            $('.qlo-hotel-names-badge').each(function () {
                if ($(this).data('ui-tooltip')) {
                    return;
                }
                $(this).tooltip({
                    items: '.qlo-hotel-names-badge',
                    content: function () {
                        var hotels = $(this).data('hotel-names');
                        if (!hotels || !hotels.length) {
                            return '';
                        }
                        var rows = $.map(hotels, function (name) {
                            return '<div class="tip_element_value">' + $('<div>').text(name).html() + '</div>';
                        }).join('');
                        $('#qlo-hotel-names-tooltip .qlo-hotel-names-list').html(rows);
                        return $('#qlo-hotel-names-tooltip').html();
                    },
                    close: function (event, ui) {
                        ui.tooltip.hover(function () {
                            $(this).stop(true).fadeTo(300, 1);
                        },
                        function () {
                            $(this).fadeOut('300', function () {
                                $(this).remove();
                            });
                        });
                    }
                });
            });
        };
    }
    window.initHotelNamesTooltips();
});
