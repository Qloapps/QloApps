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
                        var $badge = $(this);
                        var periods = $badge.data('stay-tip');
                        if (!periods || !periods.length) {
                            return '';
                        }
                        var html = '<div class="tooltip_cont qlo-stay-period-tooltip">';
                        html += '<div class="tip_header"><div class="tip_date">' + $badge.data('label-title') + '</div></div>';
                        html += '<div class="tip-body">';
                        html += '<div class="qlo-stay-period-row qlo-stay-period-row-header">'
                            + '<span class="tip_element_head">' + $badge.data('label-duration') + '</span>'
                            + '<span class="tip_element_head">' + $badge.data('label-rooms') + '</span>'
                            + '</div>';
                        $.each(periods, function (_, p) {
                            html += '<div class="qlo-stay-period-row">'
                                + '<span class="tip_element_value">' + p.from + ' – ' + p.to + '</span>'
                                + '<span class="tip_element_value">' + p.count + '</span>'
                                + '</div>';
                        });
                        html += '</div></div>';
                        return html;
                    },
                    position: { my: 'left top+10', at: 'left bottom', collision: 'flipfit', within: '#content' },
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
