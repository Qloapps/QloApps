/**
 * 2010-2021 Webkul.
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
 * @copyright 2010-2021 Webkul IN
 * @license LICENSE.txt
 */

function pie_chart_occupancy(widget_name, chart_details) {
    $('#availablePieChart svg').html('');
    occupacygraph = nv.addGraph(function() {
        var chart = nv.models.pieChart()
            .x(function(d) { return d.label })
            .y(function(d) { return d.value })
            .showLabels(true)
            .showLegend(false)
            .labelThreshold(.01)
            .labelType('percent')
            .donut(true)
            .donutRatio(0.35)
            .color(['#A569DF', '#56CE56', '#FF655C']);

        // create content for the tooltip of chart
        chart.tooltip.contentGenerator((obj, element) => {
            var tooltipContent = '<p><b>' + obj.data.value + '</b> (' + obj.data.percent + '%)<p>';
            return getTooltipContent(obj.data.label, tooltipContent, obj.color);
        });

        d3.select('#availablePieChart svg')
            .datum(chart_details.data)
            .transition().duration(350)
            .call(chart);
        nv.utils.windowResize(chart.update);

        return chart;
    });
}

$(document).ready(function() {
    $('#date-start').change(function() {
        start = Date.parseDate($('#date-start').val(), 'Y-m-d');
        end = Date.parseDate($('#date-end').val(), 'Y-m-d');

        if (end.getDate() == start.getDate()) {
            end.setDate(end.getDate() + 1);
        }

        $('#dashoccupancy_date_range').html(sprintf(date_subtitle, start.format(date_format), end.format(date_format)));
    });

    $('#date-end').change(function() {
        start = Date.parseDate($('#date-start').val(), 'Y-m-d');
        end = Date.parseDate($('#date-end').val(), 'Y-m-d');

        if (end.getDate() == start.getDate()) {
            end.setDate(end.getDate() + 1);
        }

        $('#dashoccupancy_date_range').html(sprintf(date_subtitle, start.format(date_format), end.format(date_format)));
    });
});
