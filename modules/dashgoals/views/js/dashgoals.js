var dashgoals_data;
var dashgoals_chart;

function bar_chart_goals(widget_name, chart_details)
{
	nv.addGraph(function() {
		dashgoals_data = chart_details.data;

		var chart = nv.models.multiBarChart()
			.stacked(true)
			.showControls(false)
			.tooltipContent(function(key, y, e, graph) {
				if (graph.value == 0)
					return '';

				if (key == 'sales_real') {
					var result = '<div class="tooltip-panel"><div class="tooltip-panel-heading">' + graph.series.title + '</div><strong>' + formatCurrency(parseInt(graph.point.sales), currency_format, currency_sign, currency_blank) + '</strong><br />(' + formatCurrency(parseInt(graph.point.goal), currency_format, currency_sign, currency_blank) + ')<br/>';
					if (graph.point.sales > graph.point.goal)
						result += '<span class="dash_trend dash_trend_up">+';
					else
						result += '<span class="dash_trend dash_trend_down">';
					result += graph.point.goal_diff + '%</span></div>';
					return result;
				} else if (key == 'sales_less') {
					if (graph.point.sales > 0)
						return '<div class="tooltip-panel"><div class="tooltip-panel-heading">' + graph.series.title + '</div><strong>' + graph.series.zone_text + '</strong><br /><span class="dash_trend dash_trend_down">' + formatCurrency(parseInt(graph.point.goal_diff), currency_format, currency_sign, currency_blank) + '</span></div>';
					else
						return '<div class="tooltip-panel"><div class="tooltip-panel-heading">' + graph.series.title + '</div><strong>' + graph.series.zone_text + '</strong><br />(' + formatCurrency(parseInt(graph.point.goal), currency_format, currency_sign, currency_blank) + ')</div>';
				} else if (key == 'sales_more')
					return '<div class="tooltip-panel"><div class="tooltip-panel-heading">' + graph.series.title + '</div><strong>' + graph.series.zone_text + '</strong><br /><span class="dash_trend dash_trend_up">+' + formatCurrency(parseInt(graph.point.goal_diff), currency_format, currency_sign, currency_blank) + '</span></div>';
				else if (key == 'avg_cart_value_real') {
					var result = '<div class="tooltip-panel"><div class="tooltip-panel-heading">' + graph.series.title + '</div><strong>' + formatCurrency(parseInt(graph.point.avg_cart_value), currency_format, currency_sign, currency_blank) + '</strong><br />(' + formatCurrency(parseInt(graph.point.goal), currency_format, currency_sign, currency_blank) + ')<br/>';
					if (graph.point.avg_cart_value > graph.point.goal)
						result += '<span class="dash_trend dash_trend_up">+';
					else
						result += '<span class="dash_trend dash_trend_down">';
					result += graph.point.goal_diff + '%</span></div>';
					return result;
				} else if (key == 'avg_cart_value_less') {
					if (graph.point.avg_cart_value > 0)
						return '<div class="tooltip-panel"><div class="tooltip-panel-heading">' + graph.series.title + '</div><strong>' + graph.series.zone_text + '</strong><br /><span class="dash_trend dash_trend_down">' + formatCurrency(parseInt(graph.point.goal_diff), currency_format, currency_sign, currency_blank) + '</span></div>';
					else
						return '<div class="tooltip-panel"><div class="tooltip-panel-heading">' + graph.series.title + '</div><strong>' + graph.series.zone_text + '</strong><br />(' + formatCurrency(parseInt(graph.point.goal), currency_format, currency_sign, currency_blank) + ')</div>';
				} else if (key == 'avg_cart_value_more')
					return '<div class="tooltip-panel"><div class="tooltip-panel-heading">' + graph.series.title + '</div><strong>' + graph.series.zone_text + '</strong><br /><span class="dash_trend dash_trend_up">+' + formatCurrency(parseInt(graph.point.goal_diff), currency_format, currency_sign, currency_blank) + '</span></div>';
				else if (key == 'traffic_real') {
					var result = '<div class="tooltip-panel"><div class="tooltip-panel-heading">' + graph.series.title + '</div><strong>' + graph.point.traffic + ' ' +graph.series.unit_text+'</strong><br />(' + graph.point.goal + ' ' +graph.series.unit_text+')<br/>';
					if (graph.point.traffic > graph.point.goal)
						result += '<span class="dash_trend dash_trend_up">+';
					else
						result += '<span class="dash_trend dash_trend_down">';
					result += graph.point.goal_diff + '%</span></div>';
					return result;
				} else if (key == 'traffic_less') {
					if (graph.point.traffic > 0)
						return '<div class="tooltip-panel"><div class="tooltip-panel-heading">' + graph.series.title + '</div><strong>' + graph.series.zone_text + '</strong><br /><span class="dash_trend dash_trend_down">' + graph.point.goal_diff + ' ' +graph.series.unit_text+'</span></div>';
					else
						return '<div class="tooltip-panel"><div class="tooltip-panel-heading">' + graph.series.title + '</div><strong>' + graph.series.zone_text + '</strong><br />(' + graph.point.goal + ' ' +graph.series.unit_text+')</div>';
				} else if (key == 'traffic_more')
					return '<div class="tooltip-panel"><div class="tooltip-panel-heading">' + graph.series.title + '</div><strong>' + graph.series.zone_text + '</strong><br /><span class="dash_trend dash_trend_up">+' + graph.point.goal_diff + ' ' +graph.series.unit_text+'</span></div>';
				else if (key == 'conversion_real') {
					var result = '<div class="tooltip-panel"><div class="tooltip-panel-heading">' + graph.series.title + '</div><strong>' + graph.point.conversion + '%</strong><br />(' + graph.point.goal + '%)</strong><br/>';
					if (graph.point.conversion  > graph.point.goal)
						result += '<span class="dash_trend dash_trend_up">+';
					else
						result += '<span class="dash_trend dash_trend_down">';
					result += graph.point.goal_diff + '%</span></div>';
					return result;
				} else if (key == 'conversion_less') {
					if (graph.point.conversion > 0)
						return '<div class="tooltip-panel"><div class="tooltip-panel-heading">' + graph.series.title + '</div><strong>' + graph.series.zone_text + '</strong><br /><span class="dash_trend dash_trend_down">' + graph.point.goal_diff + '%</span></div>';
					else
						return '<div class="tooltip-panel"><div class="tooltip-panel-heading">' + graph.series.title + '</div><strong>' + graph.series.zone_text + '</strong><br />(' + graph.point.goal + '%)</div>';
				} else if (key == 'conversion_more')
					return '<div class="tooltip-panel"><div class="tooltip-panel-heading">' + graph.series.title + '</div><strong>' + graph.series.zone_text + '</strong><br /><span class="dash_trend dash_trend_up">+' + graph.point.goal_diff + '%</span></div>';

			});

		chart.yAxis.tickFormat(d3.format('.1f'));
		dashgoals_chart = chart;

		d3.select('#dash_goals_chart1 svg')
			.datum(chart_details.data)
			.transition()
			.call(chart);

		$('#dash_goals_chart1 .nv-legendWrap').remove();

		nv.utils.windowResize(chart.update);

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

$(document).ready(function() {
	$('.dashgoals_config_input').keyup(function() { dashgoals_calc_sales(); });
	dashgoals_calc_sales();
});
