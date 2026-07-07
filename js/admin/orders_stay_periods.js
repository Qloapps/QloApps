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
    var closeTimer = null;

    function buildTooltip(periods) {
        var $tpl  = $('#stay-period-tpl .qlo-tooltip-cont').clone();
        var $body = $tpl.find('.qlo-tip-body').empty();

        $body.append(
            $('<div class="qlo-stay-period qlo-stay-period-header">').append(
                $('<span class="qlo-tip-element-head">').text($('#stay-period-tpl').data('label-dates')),
                $('<span class="qlo-tip-element-value">').text($('#stay-period-tpl').data('label-rooms'))
            )
        );

        $.each(periods, function (_, p) {
            $body.append(
                $('<div class="qlo-stay-period">').append(
                    $('<span class="qlo-tip-element-head">').text(p.from + ' – ' + p.to),
                    $('<span class="qlo-tip-element-value">').text(p.count)
                )
            );
        });
        return $('<div>', {
            'class': 'ui-tooltip ui-widget ui-corner-all ui-widget-content qlo-stay-period-tooltip',
            'role' : 'tooltip'
        }).append(
            $('<div class="ui-tooltip-content">').append($tpl)
        );
    }

    function cancelClose() {
        clearTimeout(closeTimer);
        closeTimer = null;
    }

    function scheduleClose($tip) {
        cancelClose();
        closeTimer = setTimeout(function () {
            $tip.fadeOut(300, function () { $tip.remove(); });
        }, 300);
    }

    $(document).on('mouseenter', '.qlo-stay-period-tip', function () {
        cancelClose();

        var periods = $(this).data('stay-tip');
        if (!periods || !periods.length) { return; }

        $('.qlo-stay-period-tooltip').stop(true).remove();

        var $icon = $(this);
        var $tip  = buildTooltip(periods).appendTo('body').hide().fadeIn(200);

        var iconOffset = $icon.offset();
        $tip.css({
            position : 'absolute',
            zIndex   : 9999,
            top      : iconOffset.top + ($icon.outerHeight() / 2) - ($tip.outerHeight() / 2),
            left     : iconOffset.left + $icon.outerWidth() + 8
        });

        $tip.on('mouseenter', function () {
            cancelClose();
        }).on('mouseleave', function () {
            scheduleClose($tip);
        });

    }).on('mouseleave', '.qlo-stay-period-tip', function () {
        var $tip = $('.qlo-stay-period-tooltip');
        if ($tip.length) {
            scheduleClose($tip);
        }
    });
});
