var dashtrends_data;
var dashtrends_chart;

function line_chart_trends(widget_name, chart_details)
{
    // when there is no data available do not show the chart
    if (chart_details.data[0].values.length <= 1) {
		$('#dash_trends_chart1').hide();
	} else {
        nv.addGraph(function() {
            var chart = nv.models.lineChart()
                .useInteractiveGuideline(true)
                .x(function(d) { return (d !== undefined ? d[0] : 0); })
                .y(function(d) { return (d !== undefined ? parseFloat(d[1]) : 0); })
                .forceY([0, 50])
                .margin({
                    left: 70,
                    right: 30,
                });

            chart.xAxis
            .tickFormat(function(d) {
                date = new Date(d*1000);
                return date.format(chart_details['date_format']);
            })
            // through this function we are also fixing the x axis date and values alignment issue
            .tickValues(function(values) {
                var indexInterval = Math.floor(values[0].values.length / 10);

                var dates = [];
                for (var i = 0; i < 10; i++) {
                    if (values[0].values[(indexInterval+1) * i]) {
                        dates.push(values[0].values[(indexInterval+1) * i]);
                    }
                }
                var dates =  dates.map(function(v) {
                    return v[0]
                });

                return dates;
            });

            first_data = new Array();
            $.each(chart_details.data, function(index, value) {
                if (value.id == 'sales' || value.id == 'sales_compare')
                {
                    if (value.id == 'sales'){
                        $('#dashtrends_toolbar dl:first').addClass('active').css("border-color", value.border_color);
                    }
                    first_data.push(chart_details.data[index]);

                    // create content for the tooltip of chart for sales as first chart is Sales
                    chart.interactiveLayer.tooltip.contentGenerator((obj, element) => {
                        if (typeof obj.series[0] !== 'undefined') {
                            var date = new Date(obj.value * 1000);
                            date = date.format(chart_details['date_format']);

                            tooltipContent = '';
                            tooltipContent += '<p>' + date_txt + ': <b>' + date + '</b></p>';
                            tooltipContent += '<p>' + obj.series[0].key + ': <b>' + formatCurrency(parseFloat(obj.series[0].value), currency_format, currency_sign, currency_blank) + '</b></p>';
                        }

                        return getTooltipContent(obj.series[0].key, tooltipContent, obj.series[0].color);
                    });
                }
            });

            chart.yAxis.tickFormat(function(d) {
                return formatCurrency(parseFloat(d), currency_format, currency_sign, currency_blank);
            });

            dashtrends_data = chart_details;
            dashtrends_chart = chart;
            d3.select('#dash_trends_chart1 svg')
                .datum(first_data)
                .call(chart);
            nv.utils.windowResize(chart.update);

            return chart;
        });
    }
}

function selectDashtrendsChart(element, type)
{
	// $('#dashtrends_toolbar dl').removeClass('active');
	current_charts = new Array();
	$.each(dashtrends_data.data, function(index, value) {
		if (value.id == type || value.id == type + '_compare')
		{
			if (value.id == type)
			{
				$(element).parent().siblings().find('dl').css("border-color", '#fff');
				$(element).css("border-color", value.border_color);
			}

			current_charts.push(dashtrends_data.data[index]);
			value.disabled = false;
		}
	});

    if (type == 'orders' || type == 'visits') {
        dashtrends_chart.yAxis.tickFormat(d3.format('d'));
        dashtrends_chart.forceY([0, 1]);
    } else {
        dashtrends_chart.yAxis.tickFormat(d3.format('.f'));
    }

	if (type == 'sales' || type == 'average_cart_value' || type == 'net_profits') {
        dashtrends_chart.forceY([0, 50]);
		dashtrends_chart.yAxis.tickFormat(function(d) {
			return formatCurrency(parseFloat(d), currency_format, currency_sign, currency_blank);
		});
    }

	if (type == 'conversion_rate') {
        dashtrends_chart.forceY([0, 10]);
		dashtrends_chart.yAxis.tickFormat(function(d) {
			return d3.round(d, 2)+' %';
		});
    }

    // create content for the tooltip of chart for different charts dynamically
    dashtrends_chart.interactiveLayer.tooltip.contentGenerator((obj, element) => {
        if (typeof obj.series[0] !== 'undefined') {
            var date = new Date(obj.value * 1000);
            date = date.format(dashtrends_data['date_format']);

            if (type == 'sales' || type == 'average_cart_value' || type == 'net_profits') {
                var trendValue = formatCurrency(parseFloat(obj.series[0].value), currency_format, currency_sign, currency_blank)
            } else if (type == 'conversion_rate') {
                var trendValue = d3.round(obj.series[0]['data'][1], 2)+'%';
            } else {
                var trendValue = obj.series[0].value;
            }

            tooltipContent = '';
            tooltipContent += '<p>' + date_txt + ': <b>' + date + '</b></p>';
            tooltipContent += '<p>' + obj.series[0].key + ': <b>' + trendValue + '</b></p>';
        }

        return getTooltipContent(obj.series[0].key, tooltipContent, obj.series[0].color);
    });

	d3.select('#dash_trends_chart1 svg')
		.datum(current_charts)
		.call(dashtrends_chart);
}

$(document).ready(function(){
    $("dl").tooltip();
});
