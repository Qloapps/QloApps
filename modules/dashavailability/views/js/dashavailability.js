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

var dashavailability_data;
var dashavailability_chart;

function line_chart_availability(widget_name, chart_details) {
    nv.addGraph(function() {
        var chart = nv.models.lineChart()
            .useInteractiveGuideline(true)
            .x(function(d) { return (d !== undefined ? d[0] : 0); })
            .y(function(d) { return (d !== undefined ? parseInt(d[1]) : 0); })
            .margin({
                left: 30,
                right: 30,
            });

        chart.xAxis.tickFormat(function(d) {
            date = new Date(d * 1000);
            return date.format(chart_details['date_format']);
        });

        chart.yAxis.tickFormat(function(d) {
            return parseInt(d);
        });

        chart.legend.updateState(false);

        dashavailability_data = chart_details.data;
        dashavailability_chart = chart;
        d3.select('#availability_line_chart1 svg')
            .datum(dashavailability_data)
            .call(chart);
        nv.utils.windowResize(chart.update);

        return chart;
    });
}

function refreshAvailabilityBarData() {
    var days = parseInt($('#dashavailability').find('.avail-bar-btn.bar-btn-active').attr('data-days'));
    fetchAvailablityBarData(days, $("#bardate").val());
}

// select fetch bar chart data
function fetchAvailablityBarData(days, date_from = false) {
    if (!date_from) {
        date_from = $("#bardate").val();
    }

    var extra = JSON.stringify({
        date_from: date_from,
        days: days,
    });

    refreshDashboard('dashavailability', true, extra);
}

function availDatePicker() {
    $('#bardate').datepicker('show');
}

$(document).on('click', '.avail-bar-btn', function() {
    $('.avail-bar-btn').removeClass('bar-btn-active');
    $(this).addClass('bar-btn-active');
    refreshAvailabilityBarData();
});

$(document).ready(function() {
    $("#bardate").val($("#date-start").val());
    $(".bar-date").find("strong").text($("#date-start").val());

    $("#bardate").datepicker({
        dateFormat: 'yy-mm-dd',
        beforeShow: function(input, inst) {
            setTimeout(function() {
                inst.dpDiv.css({
                    top: $(".datepicker").offset().top + 35,
                    left: $(".datepicker").offset().left
                });
            }, 0);
        },
        onSelect: function(date) {
            $(".bar-date").find("strong").text(date);
            refreshAvailabilityBarData();
        }
    });
});
