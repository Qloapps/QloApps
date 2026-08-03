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
    if (typeof window.initStayPeriodTooltips === 'undefined') {
        window.initStayPeriodTooltips = function () {
            if (typeof $.fn.tooltip === 'undefined' || typeof $.ui === 'undefined' || typeof $.ui.tooltip === 'undefined') {
                return;
            }
            $('.qlo-stay-period-badge').each(function () {
                if ($(this).data('ui-tooltip')) {
                    return;
                }
                $(this).tooltip({
                    items: '.qlo-stay-period-badge',
                    content: function () {
                        var periods = $(this).data('stay-tip');
                        if (!periods || !periods.length) {
                            return '';
                        }
                        var rows = $.map(periods, function (p) {
                            return '<tr><td class="tip_element_value">' + p.from + ' – ' + p.to + '</td>'
                                + '<td class="tip_element_value">' + p.count + '</td></tr>';
                        }).join('');
                        $('#qlo-stay-period-tooltip-tpl tbody').html(rows);
                        return $('#qlo-stay-period-tooltip-tpl').html();
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
    window.initStayPeriodTooltips();
});
