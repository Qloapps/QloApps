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

$(document).ready(function () {

	 $('#filter_daterange_value').dateRangePicker({
		startDate: $.datepicker.formatDate('dd-mm-yy', new Date()),
		separator : ' to ',
		setValue: function(s,s1,s2)
		{
			if (s1) {
				$('#daterange_value_from').find('span').html(s1);
			} else {
				$(daterange_value_from).find('span').html(
					RangePickerCheckin
				);
			}
			if (s2) {
				$('#daterange_value_to').find('span').html(s2);
			} else {
				$('#daterange_value_to').find('span').html(
					RangePickerCheckin
				);
			}
		},
		endDate: max_order_date,
		customOpenAnimation: function(cb)
		{
			$(this).show(10, cb);
		},
		customCloseAnimation: function(cb)
		{
			$(this).hide(10, cb);
		}
	});
	if (date_from && date_to) {
		$('#filter_daterange_value').data('dateRangePicker').setDateRange(
			$.datepicker.formatDate('dd-mm-yy', $.datepicker.parseDate('yy-mm-dd', date_from)),
			$.datepicker.formatDate('dd-mm-yy', $.datepicker.parseDate('yy-mm-dd', date_to))
		);
	}
	$(".clear_filter").on("click", function () {
		var filter_wrapper = $(this).parents(".layered_filter_heading").siblings(".lf_sub_cont");
		var make_diff = filter_wrapper.children("div"); //use to make difference between checkbox and price slider
		if (make_diff.hasClass("layered_filt")) // for checkbox
		{
			var selected_filter = filter_wrapper.find("div.layered_filt input.filter:checked").prop("checked", false);
			selected_filter.parent("span.checked").removeClass("checked");
			if (selected_filter.length) {
				triggerFilter();
			}
		}
	});

	function triggerFilter(way, sort_by, sort_value, filter_price) {
		if (way === undefined)
			way = 0;
		if (sort_by === undefined && sort_value === undefined) {
			var sort_filter = $(".sort_btn_span[data-sort-value!='0']");
			if (sort_filter.length) {
				sort_by = sort_filter.attr("data-sort-by");
				sort_value = sort_filter.attr("data-sort-value");
			} else {
				sort_by = 0;
				sort_value = 0;
			}
		}

		var filter_data = {};
		filter_data = createFilterObj(filter_data);
		getFilterResult(filter_data, way, sort_by, sort_value);
	}

	var slider_diff = 0;

	$(document).on("click", '.sort_result', function (e) {
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
	$('.filter').on('click', function () {
		triggerFilter();
	});

	function createFilterObj(filter) {
		$('.filter').each(function () {
			if ($(this).is(':checked')) {
				var temp_type = $(this).attr('data-type');
				if (typeof filter[temp_type] != 'undefined') {
					filter[temp_type].push($(this).val());
				} else {
					filter[temp_type] = [];
					filter[temp_type].push($(this).val());
				}
			}
		});

		return filter;
	}

	function getFilterResult(data, way, sort_by, sort_value) {
		if (way && !Object.getOwnPropertyNames(data).length)
			return false;

		if (filter_ajax)
			filter_ajax.abort();

		data = { filter_data: data, ajax: 1, action: 'FilterResults' };

		if (sort_by && sort_value) {
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
});