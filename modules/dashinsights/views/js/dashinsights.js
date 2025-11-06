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

function line_chart_dashinsights(widget_name, chart_details) {
    $('#dashinsights_room_nights svg').html('');
    nv.addGraph(function () {
        var chart = nv.models.lineChart()
            .x(function (d) { return (d !== undefined ? d[0] : 0); })
            .y(function (d) { return (d !== undefined ? parseInt(d[1]) : 0); })
            .forceY([0, 1])
            .margin({
                left: 40,
                right: 30,
            });

        chart.yAxis.scale().domain([0, 1]);
        // create content for the tooltip of chart
        chart.tooltip.contentGenerator((obj, element) => {
            var totalNightsBooked = 0;
            var tooltipContent = '';

            var date = new Date(obj.value * 1000);
            date = date.format(chart_details['date_format']);
            var tooltipLabel = date_txt + ': ' + date;

            var hotelsContent = '';
            $.each( obj.series, function(index, hotelData) {
                totalNightsBooked = totalNightsBooked + parseInt(hotelData.value);

                hotelsContent += '<p>';
                hotelsContent += '<span class="tooltip-data-color" style="background:' + hotelData.color + '">' + '</span>';
                hotelsContent += hotelData.key + ': <b>' + hotelData.value + '</b>';
                hotelsContent += '</p>';
            });

            tooltipContent += '<p>' + total_nights_booked_txt + ': <b>' + totalNightsBooked + '</b></p>';
            tooltipContent += hotelsContent;

            return getTooltipContent(tooltipLabel, tooltipContent);
        });

        // format date values
        chart.xAxis.tickFormat(function (d) {
            date = new Date(d * 1000);
            return date.format(chart_details['date_format']);
        })
        // through this function we are also fixing the x axis date and values alignment issue
        .tickValues(function(values) {
            var indexInterval = (values[0].values.length / 4);
            var dates = [];
            for (var i = 0; i < 4; i++) {
                let dateIndex = Math.round(indexInterval) * i;
                if (values[0].values[dateIndex]) {
                    dates.push(values[0].values[dateIndex]);
                }
            }
            var dates =  dates.map(function(v) {
                return v[0]
            });

            return dates;
        });

        // Y axis values must be integer only
        chart.yAxis.tickFormat(d3.format('d'));

        // initialize chart
        d3.select('#dashinsights_room_nights svg')
            .datum(chart_details.data)
            .call(chart);
        nv.utils.windowResize(chart.update);

        return chart;
    });
}

function multibar_chart_dotw_dashinsights(widget_name, chart_details) {
    $('#dashinsights_days_of_the_week svg').html('');
    nv.addGraph(function() {
        var chart = nv.models.multiBarHorizontalChart()
            .showControls(false)
            .forceY([0, 1])
            .margin({
                left: 40,
                right: 30,
            }
        );

        // create content for the tooltip of chart
        chart.tooltip.contentGenerator((obj, element) => {
            var tooltipContent = '<p>' + hotel_txt + ': <b>' + obj.data.key + '</b></p>';
            tooltipContent += '<p>' + nights_booked_txt + ': <b>' + obj.data.y + '</b> (' + obj.data.percent + '%)</p>';

            return getTooltipContent(obj.data.day, tooltipContent, obj.color);
        });

        chart.xAxis.tickFormat(function (d) {
            return d;
        });

        chart.yAxis.axisLabel(chart_details.axis_labels.y)
        .tickFormat(function (d) {
            return d;
        });
        // Y axis values must be integer only
        chart.yAxis.tickFormat(d3.format('d'));

        d3.select('#dashinsights_days_of_the_week svg')
            .datum(chart_details.data)
            .call(chart);

        nv.utils.windowResize(chart.update);

        return chart;
    });
}

function multibar_chart_los_dashinsights(widget_name, chart_details) {
    $('#dashinsights_length_of_stay svg').html('')
    nv.addGraph(function() {
        var chart = nv.models.multiBarHorizontalChart()
            .showControls(false)
            .forceY([0, 1])
            .margin({
                left: 40,
                right: 30,
            });

        chart.xAxis
            .axisLabel(chart_details.axis_labels.x)
            .tickFormat(function (d) {
                return d;
            }
        );
        chart.yAxis
            .axisLabel(chart_details.axis_labels.y)
            .tickFormat(function (d) {
                return d;
            }
        );
        // Y axis values must be integer only
        chart.yAxis.tickFormat(d3.format('d'));

        // create content for the tooltip of chart
        chart.tooltip.contentGenerator((obj, element) => {
            var tooltipLabel = length_of_stay_txt + ': ' + obj.data.x;

            var tooltipContent = '<p>' + hotel_txt + ': <b>' + obj.data.key + '</b></p>';
            tooltipContent += '<p>' + room_booked_txt + ': <b>' + obj.data.rooms_occupied + '</b> (' + obj.data.percent + '%)</p>';

            return getTooltipContent(tooltipLabel, tooltipContent, obj.color);
        });

        chart.dispatch.stateChange = function () {
            setTimeout(function () {
                positionXAxisLabel();
            }, 10);
        }

        d3.select('#dashinsights_length_of_stay svg')
            .datum(chart_details.data)
            .call(chart);

        positionXAxisLabel();

        nv.utils.windowResize(function() {
            chart.update();
            positionXAxisLabel();
        });

        return chart;
    });

    function positionXAxisLabel() {
        d3.select('#dashinsights_length_of_stay .nv-x .nv-axislabel')
        .attr('transform', d3.transform('rotate(-90) translate(-130, 40)').toString())
        .attr('y', '-70')
        .attr('x', '0');
    }
}
