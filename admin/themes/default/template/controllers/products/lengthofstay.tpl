{if isset($product->id)}
	<div id="product-lengthofstay" class="panel product-tab">
		<h3 class="tab"><i class="icon-calendar"></i> {l s='Length Of Stay'}</h3>
		<input type="hidden" name="submitted_tabs[]" value="LengthOfStay" />

		<div class="panel-content">
			<div class="alert alert-info">
				{l s='Please read below important points regarding length of stay management'}
				<ul>
					<li>{l s='Set 1 day for setting no limit on Minimum length of stay'}</li>
					<li>{l s='Set 0 day for setting no limit on Maximum length of stay'}</li>
					<li>{l s='Enable "Length of stay for date ranges" for setting values of \"Minimum length of stay\" and \"Maximum length of stay\" according to different date ranges'}</li>
					<li>{l s='Length of stays (minimum|maximum) of all the dates which does not fall under the date ranges of \"Length of stay for date ranges\", will be taken from the global values of length of stays of this room type.'}</li>
					<li>{l s='\"Date to\" will not be included in the date range of \"Length of stay for date ranges\".'}</li>
				</ul>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3 required" for="min_los">
					<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Enter minimum length of stay for the hotel. set 1 day for setting no limit on minimum length of stay.'}">{l s='Minimum length of stay'}</span>
				</label>
				<div class="col-sm-2">
					<div class="input-group">
						<input type="text" id="min_los" name="min_los" value="{if isset($smarty.post.min_los)}{$smarty.post.min_los|escape:'html':'UTF-8'}{elseif isset($roomTypeInfo['min_los']) && $roomTypeInfo['min_los']}{$roomTypeInfo['min_los']|escape:'html':'UTF-8'}{else}1{/if}">
						<span class="input-group-addon">{l s='Day(s)'}</span>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3 required" for="max_los">
					<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Enter maximum length of stay for the hotel. set 0 day for setting no limit on maximum length of stay.'}">{l s='Maximum length of stay'}</span>
				</label>
				<div class="col-sm-2">
					<div class="input-group">
						<input type="text" id="max_los" name="max_los" value="{if isset($smarty.post.max_los)}{$smarty.post.max_los|escape:'html':'UTF-8'}{elseif isset($roomTypeInfo['max_los'])}{$roomTypeInfo['max_los']|escape:'html':'UTF-8'}{else}0{/if}">
						<span class="input-group-addon">{l s='Day(s)'}</span>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="active_restriction_dates" class="control-label col-sm-3">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Enable, if you want set minimum|maximum length of stay according to different date ranges.'}">{l s='Length of stay for date ranges'}</span>
			</label>
			<div class="col-sm-9">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" value="1" id="active_restriction_dates_on" name="active_restriction_dates" {if isset($smarty.post.active_restriction_dates) && $smarty.post.active_restriction_dates}checked="checked"{elseif isset($roomTypeInfo['restrictionDataRange']) && $roomTypeInfo['restrictionDataRange']}checked="checked"{/if}>
					<label for="active_restriction_dates_on">{l s='Yes'}</label>

					<input type="radio" value="0" id="active_restriction_dates_off" name="active_restriction_dates" {if isset($smarty.post.active_restriction_dates)}{if !$smarty.post.active_restriction_dates}checked="checked"{/if}{elseif isset($roomTypeInfo['restrictionDataRange'])}{if  !$roomTypeInfo['restrictionDataRange']}checked="checked"{/if}{else}checked="checked"{/if}>
					<label for="active_restriction_dates_off">{l s='No'}</label>

					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>

		<div id="restriction_dates_container" class="form-group table-responsive" {if isset($smarty.post.active_restriction_dates)}{if !$smarty.post.active_restriction_dates}style="display:none;"{/if}{elseif isset($roomTypeInfo['restrictionDataRange'])}{if !$roomTypeInfo['restrictionDataRange']}style="display:none;"{/if}{/if}>
			<table id="restriction_dates_table" class="table table-striped">
				<thead>
					<th>{l s='Date From'}</th>
					<th>{l s='Date To'}</th>
					<th>{l s='Minimum length of stay'}</th>
					<th>{l s='Maximum length of stay'}</th>
					<th></th>
				</thead>
				<tbody>
					{if isset($smarty.post.active_restriction_dates) && $smarty.post.active_restriction_dates && isset($smarty.post.restriction_date_from) && $smarty.post.restriction_date_from}
						{assign var="restrictionDataRange" value=[]}
						{foreach from=$smarty.post.restriction_date_from item=restrictionDateFromVal key=restrictionKey}
							{assign var="restrictionDataRangeTemp" value=['date_from' => $restrictionDateFromVal, 'date_to' => $smarty.post.restriction_date_to[$restrictionKey], 'min_los' => $smarty.post.restriction_min_los[$restrictionKey], 'max_los' => $smarty.post.restriction_max_los[$restrictionKey]]}

							{if isset($smarty.post.id_rt_restriction) && isset($smarty.post.id_rt_restriction[$restrictionKey])}
								{$restrictionDataRangeTemp['id_rt_restriction'] = $smarty.post.id_rt_restriction[$restrictionKey]}
							{/if}

							{$restrictionDataRange[] = $restrictionDataRangeTemp}
						{/foreach}
					{elseif isset($roomTypeInfo['restrictionDataRange']) && $roomTypeInfo['restrictionDataRange']}
						{assign var="restrictionDataRange" value=$roomTypeInfo['restrictionDataRange']}
					{else}
						{assign var="restrictionDataRange" value=[['date_from' => '', 'date_to' => '', 'min_los' => '', 'max_los' => '']]}
					{/if}

					{foreach from=$restrictionDataRange item=restrictionInfo}
						<tr>
							<td>
								<div class="input-group">
									<input class="form-control restriction_date date_from_restriction" type="text" name="restriction_date_from[]" value="{$restrictionInfo['date_from']|date_format:'%d-%m-%Y'}" readonly>
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
								</div>
							</td>
							<td>
								<div class="input-group">
									<input class="form-control restriction_date date_to_restriction" type="text" name="restriction_date_to[]" value="{$restrictionInfo['date_to']|date_format:'%d-%m-%Y'}" readonly>
									<span class="input-group-addon"><i class="icon-calendar"></i></span>
								</div>
							</td>
							<td>
								<div class="input-group">
									<input class="form-control" type="text" name="restriction_min_los[]" value="{$restrictionInfo['min_los']}">
									<span class="input-group-addon">{l s='day(s)'}</span>
								</div>
							</td>
							<td>
								<div class="input-group">
									<input class="form-control" type="text" name="restriction_max_los[]" value="{$restrictionInfo['max_los']}">
									<span class="input-group-addon">{l s='day(s)'}</span>
								</div>
							</td>
							<td>
								{if isset($restrictionInfo['id_rt_restriction'])}
									<input type="hidden" name="id_rt_restriction[]" value="{$restrictionInfo['id_rt_restriction']}">
								{/if}
								<a href="#" class="btn btn-default delete_date_restriction" {if isset($restrictionInfo['id_rt_restriction'])}data-id-rt-restriction="{$restrictionInfo['id_rt_restriction']}"{/if}><i class="icon-trash"></i></a>
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
			<div class="row">
				<div class="col-sm-12">
					<a href="#" id="add_los_restriction" class="btn btn-default"><i class="icon icon-plus"></i> {l s='Add more length of stays'}</a>
				</div>
			</div>
		</div>

		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default">
				<i class="process-icon-cancel"></i>
				{l s='Cancel'}
			</a>
			<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled">
				<i class="process-icon-loading"></i>
				{l s='Save'}
			</button>
			<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"  disabled="disabled">
				<i class="process-icon-loading"></i>
					{l s='Save and stay'}
			</button>
		</div>
	</div>
{/if}

<script>
	var days_txt = "{l s='day(s)' js=1}";
	var adminProductLink = "{$link->getAdminlink('AdminProducts')}";
	var someErrorText = "{l s='Something went wrong. Please try again.' js=1}";

	// console.log($.datepicker.parseDate( "dd-mm-yy", "15-06-2022" ));

	$(document).ready(function() {
		$("input[name='active_restriction_dates']").on('change', function () {
			if (parseInt($(this).val())) {
				$("#restriction_dates_container").show(200);
			} else {
				$("#restriction_dates_container").hide(200);
			}
		});

		$(document).on("click", ".delete_date_restriction",function(e) {
			e.preventDefault();

			if (typeof $(this).attr('data-id-rt-restriction') !== 'undefined' && $(this).attr('data-id-rt-restriction') !== false) {
				var currentElement = $(this);
				$.ajax({
					url: adminProductLink,
					type: 'POST',
					dataType: 'json',
					data: {
						ajax: true,
						action:'deleteRoomTypeLengthOfStayRestriction',
						id_rt_restriction: currentElement.attr('data-id-rt-restriction'),
					},
					success: function (result) {
						if (typeof result.success !== 'undefined' && result.success !== false) {
							showSuccessMessage(result.success);
							currentElement.closest('tr').remove();
						} else if (typeof result.error !== 'undefined' && result.error !== false) {
							showErrorMessage(result.error);
						}
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						showErrorMessage(someErrorText);
					}
				});
			} else {
				$(this).closest('tr').remove();
			}
		});

		$('#add_los_restriction').on('click',function(e) {
			e.preventDefault();

			var html = '';
			html += '<tr>';
				html += '<td>';
					html += '<div class="input-group">';
						html += '<input class="form-control restriction_date date_from_restriction" type="text" name="restriction_date_from[]" value="" readonly>';
						html += '<span class="input-group-addon"><i class="icon-calendar"></i></span>';
					html += '</div>';
				html += '</td>';
				html += '<td>';
					html += '<div class="input-group">';
						html += '<input class="form-control restriction_date date_to_restriction" type="text" name="restriction_date_to[]" value="" readonly>';
						html += '<span class="input-group-addon"><i class="icon-calendar"></i></span>';
					html += '</div>';
				html += '</td>';
				html += '<td>';
					html += '<div class="input-group">';
						html += '<input class="form-control" type="text" name="restriction_min_los[]" value="">';
						html += '<span class="input-group-addon">'+ days_txt +'</span>';
					html += '</div>';
				html += '</td>';
				html += '<td>';
					html += '<div class="input-group">';
						html += '<input class="form-control" type="text" name="restriction_max_los[]" value="">';
						html += '<span class="input-group-addon">'+ days_txt +'</span>';
					html += '</div>';
				html += '</td>';
				html += '<td>';
					html += '<a href="#" class="btn btn-default delete_date_restriction"><i class="icon-trash"></i></a>';
				html += '</td>';
			html += '</tr>';

			$('#restriction_dates_table tbody').append(html);
			$(this).blur();
		});

		$(document).on('focus', '.restriction_date', function() {
			var dateFormat = "dd-mm-yy";
			$(this).datepicker({
				defaultDate: new Date(),
				dateFormat: dateFormat,
				minDate: 0,
				beforeShow: function (input, instance) {
					if ($(input).hasClass('date_to_restriction') ) {
						var dateFrom = $(this).closest('tr').find('td div input.date_from_restriction').val();
						if (typeof dateFrom !== 'undefined' && dateFrom !== false && dateFrom != '') {
							// This can also work but more testing needed: var date = new Date($.datepicker.parseDate(dateFormat, dateFrom));
							var minDateTo = new Date($.datepicker.formatDate('yy-mm-dd', $.datepicker.parseDate(dateFormat, dateFrom)));
							minDateTo.setDate(minDateTo.getDate() + 1);

							$(this).datepicker("option", "minDate", minDateTo);
						}
					}
				},
				beforeShowDay: function (currentDate) {
					let dateRange = [];

					$(this).closest('tr').siblings().each(function( index ) {
						var dateFrom = $(this).find('td div input.date_from_restriction').val();
						var dateTo = $(this).find('td div input.date_to_restriction').val();
						if ((typeof dateFrom !== 'undefined' && dateFrom !== false && dateFrom != '') && (typeof dateTo !== 'undefined' && dateTo !== false && dateTo != '')) {
							for (let index = $.datepicker.parseDate(dateFormat, dateFrom); index < $.datepicker.parseDate(dateFormat, dateTo); index.setDate(index.getDate() + 1)) {
								dateRange.push($.datepicker.formatDate(dateFormat, index));
							}
						}
					});

					let dateString = $.datepicker.formatDate(dateFormat, currentDate);
					return [dateRange.indexOf(dateString) == -1];
				},
				onClose: function (dateText, instance) {
					if ($(this).hasClass('date_from_restriction')) {
						if (dateText != "") {
							var dateFrom = $.datepicker.parseDate(dateFormat, dateText);

							var dateTo = $(this).closest('tr').find('td div input.date_to_restriction').val();
							dateTo = $.datepicker.parseDate(dateFormat, dateTo);

							if (dateFrom >= dateTo) {
								// In this case empty date_to, so that user have to again select the date for date_to field
								$(this).closest('tr').find('td div input.date_to_restriction').val("");
							}
						}
					}
				}
			});
		});
	});
</script>