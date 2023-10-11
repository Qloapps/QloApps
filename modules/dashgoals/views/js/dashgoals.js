var dashgoals_data;
var dashgoals_chart;

function bar_chart_goals(widget_name, chart_details)
{
	nv.addGraph(function() {
		dashgoals_data = chart_details.data;

		var chart = nv.models.multiBarChart()
			.stacked(true)
            .forceY([0, 1])
			.showControls(false);

        // create content for the tooltip of chart
        chart.tooltip.contentGenerator((obj, element) => {
            return getDashgoalsTooltipContent(obj, chart_details);
        });

		dashgoals_chart = chart;

		d3.select('#dash_goals_chart1 svg')
			.datum(chart_details.data)
			.transition()
			.call(chart);

		$('#dash_goals_chart1 .nv-legendWrap').remove();

		nv.utils.windowResize(chart.update);

        // select first chart to set/format values
        selectDashgoalsChart('sales');

		return chart;
	});
}

function selectDashgoalsChart(type)
{
	if (type !== false)
	{
		$.each(dashgoals_data, function(index, value) {
			if (value.key == type + '_real' || value.key == type + '_more' || value.key == type + '_less')
				value.disabled = false;
			else
				value.disabled = true;
		});
	}

	if (type == 'avg_cart_value' || type == 'sales') {
        dashgoals_chart.yAxis.tickFormat(d3.format('.2f'));

		dashgoals_chart.yAxis.tickFormat(function(d) {
			return formatCurrency(parseFloat(d), currency_format, currency_sign, currency_blank);
		});
	}else if (type == 'conversion') {
        dashgoals_chart.yAxis.tickFormat(d3.format('.2f'));

		dashgoals_chart.yAxis.tickFormat(function(d) {
			return d + ' %';
		});
	}else if (type == 'traffic') {
    	dashgoals_chart.yAxis.tickFormat(d3.format('d'));
    }

	dashgoals_toggleDashConfig();
}

/* 	Refresh dashgoals chart when coming from the config panel
	Called from /js/admin-dashboard.js: toggleDashConfig() */
function dashgoals_toggleDashConfig()
{
	d3.select('#dash_goals_chart1 svg')
		.datum(dashgoals_data)
		.transition()
		.call(dashgoals_chart);
	nv.utils.windowResize(dashgoals_chart.update);
}

/* 	Calculate Sales based on the traffic, average cart value and conversion rate */
function dashgoals_calc_sales()
{
	$('.dashgoals_sales').each(function() {
		var key = $(this).attr('id').substr(16);
		var sales = parseFloat($('#dashgoals_traffic_' + key).val()) * parseFloat($('#dashgoals_avg_cart_value_' + key).val()) * parseFloat($('#dashgoals_conversion_' + key).val()) / 100;
		if (isNaN(sales))
			$(this).text(formatCurrency(0, currency_format, currency_sign, currency_blank));
		else
			$(this).text(formatCurrency(parseInt(sales), currency_format, currency_sign, currency_blank));
	});
}

function dashgoals_changeYear(xward)
{
	var new_year = dashgoals_year;
	if (xward == 'forward')
		new_year = dashgoals_year + 1;
	else if (xward == 'backward')
		new_year = dashgoals_year - 1;

	$.ajax({
		url: dashgoals_ajax_link,
		data: {
			ajax: true,
			action: 'changeconfyear',
			year: new_year
		},
		success : function(result){
			$('#dashgoals_title').text($('#dashgoals_title').text().replace(dashgoals_year, new_year));
			var hide_conf = $('#dashgoals_config').hasClass('hide');
			$('#dashgoals_config').replaceWith(result);
			dashgoals_calc_sales();
			if (!hide_conf)
				$('#dashgoals_config').removeClass('hide');
			$('.dashgoals_config_input').off();
			$('.dashgoals_config_input').keyup(function() { dashgoals_calc_sales(); });
			dashgoals_year = new_year;
			refreshDashboard('dashgoals', false, dashgoals_year);
		}
	});
}

// Get tooltip content for the dashgoals chart
function getDashgoalsTooltipContent(graphDataObject)
{
    var tooltipLabel = graphDataObject.data.title + ' (' + graphDataObject.value + ')';
    var tooltipContent = '';

    var goal = graphDataObject.data.goal;
    var goalDiffrence = graphDataObject.data.goal_diff;
    var currentValue = graphDataObject.data.value;

    if (graphDataObject.data.value_type == VALUE_TYPE_PRICE) {
        goal = formatCurrency(parseFloat(graphDataObject.data.goal), currency_format, currency_sign, currency_blank);
        currentValue = formatCurrency(parseFloat(graphDataObject.data.value), currency_format, currency_sign, currency_blank);
        goalDiffrence = formatCurrency(parseFloat(graphDataObject.data.goal_diff), currency_format, currency_sign, currency_blank);
    } else if (graphDataObject.data.value_type == VALUE_TYPE_PERCENT) {
        goal = graphDataObject.data.goal + ' %';
        currentValue = graphDataObject.data.value + ' %';
    }

    tooltipContent += '<p>' + goal_set_txt + ': <b>' + goal + '</b></p>';

    // some values must be displayed for passed months
    if (graphDataObject.data.is_future_goal == 0) {
        tooltipContent += '<p>' + graphDataObject.data.title + ': <b>' + currentValue + '</b></p>';;

        tooltipContent += '<p>' + goal_diff_txt + ': <b>';
        if (graphDataObject.data.complete == 1) {
            tooltipContent += '<span class="text-success">+ ';
        } else {
            tooltipContent += '<span class="text-danger">- ';
        }

        tooltipContent += goalDiffrence + '</span></b></p>';

        tooltipContent += '<p class="';
        if (graphDataObject.data.complete == 1) {
            tooltipContent += 'text-success"><b><i class="icon-circle-arrow-up"> ';
        } else {
            tooltipContent += 'text-danger"><b><i class="icon-circle-arrow-down"> ';
        }
        tooltipContent += '</i> ' + graphDataObject.data.goal_diff_percent + '%</b></p>';
    }

    return getTooltipContent(tooltipLabel, tooltipContent, graphDataObject.color);
}

$(document).ready(function() {
	$('.dashgoals_config_input').keyup(function() { dashgoals_calc_sales(); });
	dashgoals_calc_sales();
});
