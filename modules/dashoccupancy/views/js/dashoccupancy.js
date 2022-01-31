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

// PIE CHART CODE
function generateAvailablityPieChart(chartData) {
    nv.addGraph(function() {
        var chart = nv.models.pieChart()
            .x(function(d) { return d.label })
            .y(function(d) { return d.value })
            .showLabels(true)
            .showLegend(false)
            .labelThreshold(.01)
            .labelType("percent")
            .donut(true)
            .donutRatio(0.35)
            .color(["#A569DF", "#56CE56", "#FF655C"]);

        d3.select("#availablePieChart svg")
            .datum(chartData)
            .transition().duration(350)
            .call(chart);
        nv.utils.windowResize(chart.update);
        return chart;
    });
}

$(document).ready(function() {
    if (typeof date_occupancy_range === "undefined")
        var date_occupancy_range = '(from %s to %s)';

    if (typeof date_occupancy_avail_format === "undefined")
        var date_occupancy_avail_format = 'Y-mm-dd';

    $('#date-start').change(function() {
        start = Date.parseDate($('#date-start').val(), 'Y-m-d');
        end = Date.parseDate($('#date-end').val(), 'Y-m-d');
        $('#dashoccupancy_date_range').html(sprintf(date_occupancy_range, start.format(date_occupancy_avail_format), end.format(date_occupancy_avail_format)));
    });

    $('#date-end').change(function() {
        start = Date.parseDate($('#date-start').val(), 'Y-m-d');
        end = Date.parseDate($('#date-end').val(), 'Y-m-d');

        $('#dashoccupancy_date_range').html(sprintf(date_occupancy_range, start.format(date_occupancy_avail_format), end.format(date_occupancy_avail_format)));
    });
});