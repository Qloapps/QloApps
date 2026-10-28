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

$(document).ready(function()
{
	$(".clear_filter").on("click", function()
	{
		var filter_wrapper = $(this).parents(".layered_filter_heading").siblings(".lf_sub_cont");
		var make_diff = filter_wrapper.children("div"); //use to make difference between checkbox and price slider
		if (make_diff.hasClass("layered_filt") || make_diff.hasClass("layered_filt_category")) // for checkbox (flat or tree)
		{
			var selected_filter = filter_wrapper.find("div.layered_filt input.filter:checked").prop("checked", false);
			selected_filter.parent("span.checked").removeClass("checked");

			if (selected_filter.length)
			{
				triggerFilter();
			}
		}
		else if (make_diff.hasClass("price_filter_subcont")) //for price bar
		{
			ini_price = $("#filter_price_silder").slider("values");
			if ((ini_price[0] - ini_price[1]) != (max_price - min_price))
			{
				$("#filter_price_silder").slider("values", [min_price, max_price]);

				var slider_price_cont = filter_wrapper.find("div.price_filter_subcont");
				slider_price_cont.find("span#filter_price_from").html(formatCurrency(min_price, currencyFormat, currencySign, currencyBlank));
				slider_price_cont.find("span#filter_price_to").html(formatCurrency(max_price, currencyFormat, currencySign, currencyBlank));

				/* -------NOTICE------- */
				//triggerFilter();
				//on need of triggerFilter as it is triggered from base function(from which slider is created)
			}
		}
	});

	function triggerFilter(way, sort_by, sort_value, filter_price)
	{
		if (way === undefined)
	        way = 0;
	    if (filter_price === undefined)
	        filter_price = 1;
	    if (sort_by === undefined && sort_value === undefined)
	    {
	    	var sort_filter = $(".sort_btn_span[data-sort-value!='0']");
	    	if (sort_filter.length)
	    	{
	    		sort_by = sort_filter.attr("data-sort-by");
	    		sort_value = sort_filter.attr("data-sort-value");
	    	}
	    	else
	    	{
		        sort_by = 0;
		        sort_value = 0;
	    	}
	    }

		var filter_data = {};
		filter_data = createFilterObj(filter_data, filter_price);
		getFilterResult(filter_data, way, sort_by, sort_value);
	}

	var slider_diff = 0;

	$("#filter_price_silder").slider(
	{
		range: true,
		min: min_price,
		max: max_price,
		values: [min_price, max_price],
		slide: function(event, ui)
		{
			$("#filter_price_from").html(formatCurrency(ui.values[0], currencyFormat, currencySign, currencyBlank));
			$("#filter_price_to").html(formatCurrency(ui.values[1], currencyFormat, currencySign, currencyBlank));
		},
		change: function(event, ui)
		{
			if (slider_diff != (parseInt(ui.values[0]) - parseInt(ui.values[1])))
			{
				slider_diff = parseInt(ui.values[0]) - parseInt(ui.values[1]);

				triggerFilter();
			}
		}
	});

	$(".sort_result").on("click", function(e)
	{
		e.preventDefault();

		$('.sort_btn_span').attr('data-sort-by', 0);
		$('.sort_btn_span').attr('data-sort-value', 0);

		$('#gst_rating .sort_btn_span').html($('#gst_rating .sort_btn_span').attr('data-sort-for'));

		// select btn data enter
		var sort_text = $(this).html();
		var dp_btn_span = $(this).parents('div.filter_dw_cont').find('button span.sort_btn_span');
		dp_btn_span.html(sort_text);
		dp_btn_span.attr('data-sort-for', sort_text);
		dp_btn_span.attr('data-sort-by', $(this).attr('data-sort-by'));
		dp_btn_span.attr('data-sort-value', $(this).attr('data-value'));

		var sort_by = $(this).attr('data-sort-by');
		var sort_value = $(this).attr('data-value');

		triggerFilter(0, sort_by, sort_value);
	});

	var filter_ajax = '';

	triggerFilter(1, 0, 0, 0);
	$('.filter').on('click', function()
	{
		$(this).parent().toggleClass('checked', this.checked);
		triggerFilter();
	});

	function createFilterObj(filter, filter_price)
	{
		$('.filter').each(function()
		{
			if ($(this).is(':checked'))
			{
				var temp_type = $(this).attr('data-type');
				var feature_group = $(this).attr('data-feature-group');
				if (typeof feature_group != 'undefined')
				{
					// features: values of one feature are grouped (OR inside, AND between features)
					filter[temp_type] = filter[temp_type] || {};
					filter[temp_type][feature_group] = filter[temp_type][feature_group] || [];
					filter[temp_type][feature_group].push($(this).val());
				}
				else if (typeof filter[temp_type] != 'undefined')
				{
					filter[temp_type].push($(this).val());
				}
				else
				{
					filter[temp_type] = [];
					filter[temp_type].push($(this).val());
				}
			}
		});

		if (filter_price)
		{
			var slider_val = $("#filter_price_silder").slider("values");
			filter['price'] = [];
			filter['price'].push(slider_val[0]);
			filter['price'].push(slider_val[1]);
		}

		return filter;
	}

	function getFilterResult(data, way, sort_by, sort_value)
	{
		if (way && !Object.getOwnPropertyNames(data).length)
			return false;

		if (filter_ajax)
			filter_ajax.abort();

		data = { filter_data: data, ajax: 1, action:'FilterResults'};

		if (sort_by && sort_value)
		{
			data.sort_by = sort_by;
			data.sort_value = sort_value;
		}

		filter_ajax = $.ajax({
			url: cat_link,
			type: 'POST',
			dataType: 'JSON',
			data: data,
			success: function (response) {
				if (response.status) {
					$('#category_data_cont').html(response.html_room_type_list);
				}
            }
        });
		return 1;
	}
	var $conts = $('#filter_results > .col-sm-12 > .layered_filter_cont');
	if ($conts.length) {
		var $nav = $('<div class="mf_nav"></div>');
		$conts.each(function () {
			$nav.append($('<button type="button" class="mf_tab"></button>').text($(this).find('.lf_headingmain_wrapper span').first().text()).append('<b></b>'));
		});
		$('#filter_results > .col-sm-12').prepend($nav);

		var $sheet = $('<div class="mf_sheet"><div class="mf_sheet_box"><div class="mf_sheet_head"><span>' + mfSortByTxt + '</span><button type="button">&times;</button></div><ul></ul></div></div>');
		$('.sort_result').each(function () {
			$sheet.find('ul').append($('<li></li>').text($(this).text()).data('source', $(this)));
		});
		var $bar = $('<div class="mf_bar"><button type="button" class="mf_sort"><i class="icon-sort"></i> ' + mfSortTxt + '</button><button type="button" class="mf_filter"><i class="icon-filter"></i> ' + mfFilterTxt + ' <b></b></button></div>');
		$('body').append($sheet, $bar);

		function selectTab(i) {
			$nav.children().removeClass('mf_active').eq(i).addClass('mf_active');
			$conts.removeClass('mf_active').eq(i).addClass('mf_active');
		}
		selectTab(0);

		function updateCounts() {
			var total = 0;
			$conts.each(function (i) {
				var n = $(this).find('input.filter:checked').length;
				var v = $(this).find('#filter_price_silder').length ? $('#filter_price_silder').slider('values') : 0;
				n += v && (v[0] != min_price || v[1] != max_price) ? 1 : 0;
				total += n;
				$nav.children().eq(i).children('b').text(n || '');
			});
			$bar.find('.mf_filter b').text(total || '');
		}
		$(document).on('click change slidechange', '#filter_results', function () { setTimeout(updateCounts, 50); });

		$nav.on('click', '.mf_tab', function () { selectTab($(this).index()); });
		$bar.find('.mf_filter').on('click', function () { $('body').addClass('mf_filter_open'); });
		$bar.find('.mf_sort').on('click', function () { $sheet.addClass('mf_open'); });
		$('.mf_back, .mf_apply').on('click', function () { $('body').removeClass('mf_filter_open'); });
		$('.mf_clear_all').on('click', function () { $('.clear_filter').trigger('click'); });
		$sheet.on('click', function (e) { if (e.target === this || $(e.target).is('button')) $sheet.removeClass('mf_open'); });
		$sheet.on('click', 'li', function () { $(this).addClass('mf_selected').siblings().removeClass('mf_selected').end().data('source').trigger('click'); $sheet.removeClass('mf_open'); });
		$sheet.find('li').first().addClass('mf_selected');
	}
});