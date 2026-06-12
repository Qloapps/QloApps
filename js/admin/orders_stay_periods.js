/**
 * Stay Periods tooltip initialisation for the Admin Orders list.
 * Reads structured JSON from data-stay-tip on each .stay-period-tip icon,
 * clones the hidden #stay-period-tpl template, fills it, and returns it
 * as the jQuery UI tooltip content.
 */
$(document).ready(function () {
    var closeTimer = null;

    function buildTooltip(periods) {
        var $tpl  = $('#stay-period-tpl .tooltip_cont').clone();
        var $body = $tpl.find('.tip-body').empty();

        $body.append(
            $('<div class="stay_period stay_period_header">').append(
                $('<span class="tip_element_head">').text($('#stay-period-tpl').data('label-dates')),
                $('<span class="tip_element_value">').text($('#stay-period-tpl').data('label-rooms'))
            )
        );

        $.each(periods, function (_, p) {
            $body.append(
                $('<div class="stay_period">').append(
                    $('<span class="tip_element_head">').text(p.from + ' – ' + p.to),
                    $('<span class="tip_element_value">').text(p.count)
                )
            );
        });
        return $('<div>', {
            'class': 'ui-tooltip ui-widget ui-corner-all ui-widget-content stay-period-tooltip',
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

    $(document).on('mouseenter', '.stay-period-tip', function () {
        cancelClose();

        var periods = $(this).data('stay-tip');
        if (!periods || !periods.length) { return; }

        $('.stay-period-tooltip').stop(true).remove();

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

    }).on('mouseleave', '.stay-period-tip', function () {
        var $tip = $('.stay-period-tooltip');
        if ($tip.length) {
            scheduleClose($tip);
        }
    });
});
