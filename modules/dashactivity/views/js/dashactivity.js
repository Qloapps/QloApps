/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

function pie_chart_trends(widget_name, chart_details)
{
	nv.addGraph(function() {
		var chart = nv.models.pieChart()
			.x(function(d) { return d.key })
			.y(function(d) { return d.y })
			.color(d3.scale.category10().range())
			.valueFormat(d3.format(".0f"))
			.donut(true)
			.showLabels(false)
			.showLegend(false)
			.donutRatio(0.4);

		d3.select("#dash_traffic_chart2 svg")
			.datum(chart_details.data)
			.transition().duration(1200)
			.call(chart);

		nv.utils.windowResize(chart.update);

		return chart;
	});
}

$(document).ready(function() {
	if (typeof date_subtitle === 'undefined') {
		date_subtitle = '(from %s to %s)';
	}

	if (typeof date_format === 'undefined') {
		date_format = 'Y-mm-dd';
	}

	$('#date-start').change(function() {
		start = Date.parseDate($('#date-start').val(), 'Y-m-d');
		end = Date.parseDate($('#date-end').val(), 'Y-m-d');

		$('#customers-newsletters-subtitle').html(sprintf(date_subtitle, start.format(date_format), end.format(date_format)));
		$('#traffic-subtitle').html(sprintf(date_subtitle, start.format(date_format), end.format(date_format)));
	});

	$('#date-end').change(function() {
		start = Date.parseDate($('#date-start').val(), 'Y-m-d');
		end = Date.parseDate($('#date-end').val(), 'Y-m-d');

		$('#customers-newsletters-subtitle').html(sprintf(date_subtitle, start.format(date_format), end.format(date_format)));
		$('#traffic-subtitle').html(sprintf(date_subtitle, start.format(date_format), end.format(date_format)));
	});
});
