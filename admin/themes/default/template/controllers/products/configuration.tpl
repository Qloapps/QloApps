{if isset($product->id)}
	<div id="product-configuration" class="panel product-tab">
		<input type="hidden" name="submitted_tabs[]" value="Configuration" />
		<h3 class="tab"> <i class="icon-AdminAdmin"></i> {l s='Configuration' mod='hotelreservationsystem'}</h3>
		
		{if isset($htl_room_type)}
			<input type="hidden" value="{$htl_room_type['id']}" name="wk_id_room_type">
		{/if}

		<div class="form-group">
			{if isset($htl_room_type)}
				<label class="control-label col-sm-2" for="hotel_place">
					{l s='Hotel' mod='hotelreservationsystem'}
				</label>
				<div class="col-sm-4">
					<input type="text" class="form-control" value="{$htl_full_info['hotel_name']}" readonly>
					<input type="hidden" name="id_hotel" value="{$htl_room_type['id_hotel']}">
				</div>
			{else}
				<label class="control-label col-sm-2" for="hotel_place">
					{l s='Select Hotel' mod='hotelreservationsystem'}
				</label>
				<div class="col-sm-4">
					<select name="id_hotel" id="hotel_place" class="form-control">
						{foreach from=$htl_info item=htl_dtl}
							<option value="{$htl_dtl['id']}" >{$htl_dtl['hotel_name']}</option>
						{/foreach}
					</select>
				</div>
			{/if}
		</div>
		
		<div class="form-group">
			<label class="control-label col-sm-2" for="num_adults">
				{l s='Adults' mod='hotelreservationsystem'}
			</label>
			<div class="col-sm-4">
				<input id="num_adults" type="text" name="num_adults" class="form-control" {if isset($htl_room_type)}value="{$htl_room_type['adult']}"{/if}>
				<input type="hidden" id="checkConfSubmit" value="0" name="checkConfSubmit">
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-sm-2" for="num_child">
				{l s='Childrens' mod='hotelreservationsystem'}
			</label>
			<div class="col-sm-4">
				<input id="num_child" type="text" name="num_child" class="form-control" {if isset($htl_room_type)}value="{$htl_room_type['children']}"{/if}>
			</div>
		</div>

		<div class="from-group table-responsive-row clearfix">
			<table class="table hotel-room">
				<thead>
					<tr class="nodrag nodrop">
						<th class="col-sm-1 center">
							<span>{l s='Room No.' mod='hotelreservationsystem'}</span>
						</th>
						<th class="col-sm-2 center">
							<span>{l s='Floor' mod='hotelreservationsystem'}</span>
						</th>
						<th class="col-sm-2">
							<span>{l s='Status' mod='hotelreservationsystem'}</span>
						</th>
						<th class="col-sm-7 center">
							<span>{l s='Extra Information' mod='hotelreservationsystem'}</span>
						</th>
					</tr>
					{if isset($htl_room_info) && $htl_room_info}
						{foreach from=$htl_room_info key=key item=info}
							<tr class="room_data_values" id="row_index{$key}" data-rowKey="{$key}">
								<td class="col-sm-1 center">
									<input class="form-control" type="text" value="{$info['room_num']}" name="room_num[]">
								</td>
								<td class="col-sm-2 center">
									<input class="form-control" type="text" value="{$info['floor']}" name="room_floor[]">
								</td>
								<td class="col-sm-2 center">
									<select class="form-control room_status" name="room_status[]">
										{foreach from=$rm_status item=room_stauts}
											<option value="{$room_stauts['id']}" {if $info['id_status'] == {$room_stauts['id']}}selected="selected"{/if}>{$room_stauts['status']}</option>
										{/foreach}
									</select>
								</td>
								<td class="center col-sm-6">
									<a class="btn btn-default deactiveDatesModal" data-toggle="modal" data-target="#deactiveDatesModal" {if $info['id_status'] != 3 }style="display: none;"{/if}>{if $info['id_status'] != 3 }{l s='Add Dates' mod='hotelreservationsystem'}{else}{l s='View Dates' mod='hotelreservationsystem'}{/if}
									</a>
									<input type="text" class="form-control room_comment" value="{$info['comment']}" name="room_comment[]" {if $info['id_status'] == 3 }style="display: none;"{/if}>
									<input type="hidden" class="form-control disableDatesJSON" name="disableDatesJSON[]" {if $info['id_status'] == 3}value="{$info['disabled_dates_json']|escape:'html':'UTF-8'}"{/if}>
								</td>
								<td class="center col-sm-1">
									<a href="#" class="rm_htl_room btn btn-default" data-id-htl-info="{$info['id']}"><i class="icon-trash"></i></a>
									<input type="hidden" name="id_room_info[]" value="{$info['id']}">
								</td>
							</tr>
						{/foreach}
					{else}
						{for $k=0 to 1}
							<tr class="room_data_values" id="row_index{$k}" data-rowKey="{$k}">
								<td class="col-sm-2 center">
									<input class="form-control" type="text" name="room_num[]">
								</td>
								<td class="col-sm-2 center">
									<input class="form-control" type="text" name="room_floor[]">
								</td>
								<td class="col-sm-2 center">
									<select class="form-control room_status" name="room_status[]">
										{foreach from=$rm_status item=room_stauts}
											<option value="{$room_stauts['id']}">{$room_stauts['status']}</option>
										{/foreach}
									</select>
								</td>
								<td class="center col-sm-6">
									<a class="deactiveDatesModal" data-toggle="modal" data-target="#deactiveDatesModal" style="display: none;">
										{l s='add Dates' mod='hotelreservationsystem'}
									</a>
									<input type="hidden" class="form-control disableDatesJSON" name="disableDatesJSON" value="0">
									<input type="text" class="form-control room_comment" name="room_comment[]">
								</td>
							</tr>
						{/for}
					{/if}
				</thead>
			</table>
			<div class="form-group">
				<div class="col-sm-12">
					<button id="add-more-rooms-button" class="btn btn-default" type="button" data-size="s" data-style="expand-right">
						<i class="icon-folder-open"></i>
						{l s='Add More Rooms' mod='hotelreservationsystem'}
					</button>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default">
				<i class="process-icon-cancel"></i>
				{l s='Cancel'}
			</a>
			<button type="submit" name="submitAddproduct" class="btn btn-default pull-right checkConfigurationClick" disabled="disabled">
				<i class="process-icon-loading"></i>
				{l s='Save'}
			</button>
			<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right checkConfigurationClick"  disabled="disabled">
				<i class="process-icon-loading"></i>
					{l s='Save and stay'}
			</button>
		</div>
	</div>
{/if}

{*Disable Dates Model*}
<div class="modal fade" id="deactiveDatesModal" tabindex="-1" role="dialog" aria-labelledby="deactiveDatesLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close margin-right-10" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title"><i class="icon-calendar"></i>&nbsp; {l s='Disable dates' mod='hotelreservationsystem'}</h4>
			</div>
			<div class="modal-body">
				<div class="from-group table-responsive-row clearfix">
					<table class="table room-disable-dates">
						<thead>
							<tr class="nodrag nodrop">
								<th class="col-sm-1 center">
									<span>{l s='Date From' mod='hotelreservationsystem'}</span>
								</th>
								<th class="col-sm-2 center">
									<span>{l s='Date To' mod='hotelreservationsystem'}</span>
								</th>
								<th class="col-sm-2 center">
									<span>{l s='Reason' mod='hotelreservationsystem'}</span>
								</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					<div class="form-group">
						<div class="col-sm-12">
							<a href="#" class="add_more_room_disable_dates btn btn-default"><i class="icon icon-plus"></i>{l s="Add More" mod='hotelreservationsystem'}</a>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default deactiveDatesModalSubmit">{l s='Done' mod='hotelreservationsystem'}</button>
			</div>
		</div>
	</div>
</div>
{*END*}

<style>
	.deactiveDatesModal {
		cursor: pointer;
	}

	.hotel-room {
		border: 1px solid #f2f2f2;
		margin-top: 10px;
	}
</style>


<script>
	var prod_link = "{$link->getAdminLink('AdminProducts')}";
	var rm_status = {$rm_status|@json_encode};
	var currentRoomRow = 0;
	var datesMissing = "{$datesMissing}";
	var datesOverlapping = "{$datesOverlapping}";

	$(document).ready(function() {
		// Disable dates data filling when model open
		$('#deactiveDatesModal').on('show.bs.modal', function (e) {
			$('.disabledDatesTr').remove();
			
			var modelTriggerElement = $(e.relatedTarget);
			var html = '';

			var rowKey = modelTriggerElement.closest(".room_data_values").attr('data-rowKey');
			currentRoomRow = rowKey;
			var disableDatesJSON = modelTriggerElement.siblings('input.disableDatesJSON').val();

			if (disableDatesJSON) {
				var disableDatesObj = JSON.parse(disableDatesJSON);
				$.each(disableDatesObj, function(disKey, disabledRange) {
					html += '<tr class="disabledDatesTr">';
						html += '<td class="col-sm-2 center">';
							html += '<input class="disabled_date_from form-control" type="text" value="'+disabledRange.date_from+'" name="disabled_date_from'+rowKey+'[]">';
						html += '</td>';
						html += '<td class="col-sm-2 center">';
							html += '<input class="disabled_date_to form-control" type="text" value="'+disabledRange.date_to+'" name="disabled_date_to'+rowKey+'[]">';
						html += '<td class="center col-sm-6">';
							html += '<input type="text" class="form-control room_disable_reason" value="'+disabledRange.reason+'" name="room_disable_reason'+rowKey+'[]">';
						html += '</td>';
						html += '<td class="center col-sm-1">';
							html += '<a href="#" class="remove-disable-dates-button btn btn-default"><i class="icon-trash"></i></a>';
						html += '</td>';
					html += '</tr>';
				});
			} else {
				html += '<tr class="disabledDatesTr">';
					html += '<td class="col-sm-2 center">';
						html += '<input class="disabled_date_from form-control" type="text" value="" name="disabled_date_from'+rowKey+'[]">';
					html += '</td>';
					html += '<td class="col-sm-2 center">';
						html += '<input class="disabled_date_to form-control" type="text" value="" name="disabled_date_to'+rowKey+'[]">';
					html += '<td class="center col-sm-6">';
						html += '<input type="text" class="form-control room_disable_reason" value="" name="room_disable_reason'+rowKey+'[]">';
					html += '</td>';
					html += '<td class="center col-sm-1">';
						html += '<a href="#" class="remove-disable-dates-button btn btn-default"><i class="icon-trash"></i></a>';
					html += '</td>';
				html += '</tr>';
			}

			$('.room-disable-dates').append(html);
		});

		// Disable dates data save when model open
		$(document).on('click', '.deactiveDatesModalSubmit', function() {
			var disableDates = new Array();
			var error = false;
			$.each($('.disabled_date_from'), function(key, val){
				var date_from =  $(this).val();
				var date_to = $('.disabled_date_to:eq('+key+')').val();
				if (date_from.trim() && date_to.trim()) {
					var obj = {
						'date_from': $(this).val(),
						'date_to': $('.disabled_date_to:eq('+key+')').val(),
						'reason': $('.room_disable_reason:eq('+key+')').val(),
					};
					disableDates.push(obj);
				} else {
					if (!(!date_from.trim() && !date_to.trim())) {
						error = datesMissing;
					}
				}

				$.each(disableDates, function(disKey, disabledRange) {
					if (key != disKey) {
                        if (((date_from < disabledRange.date_from) && (date_to <= disabledRange.date_from)) || ((date_from > disabledRange.date_from) && (date_from >= disabledRange.date_to))) {
                        } else {
                        	error = datesOverlapping;
                        }
                    }
				});
			});

			if (error) {
				alert(error);
				return false;
			} else {
				$('#deactiveDatesModal').modal('hide');
				$("#row_index"+currentRoomRow).find('.disableDatesJSON').val(JSON.stringify(disableDates));
				return true;
			}
		});
		
		// Add new room detail
		$('#add-more-rooms-button').on('click',function() {
			var lengthRooms = $('.room_data_values').length;
			html = '<tr class="room_data_values" id="row_index'+lengthRooms+'" data-rowKey="'+lengthRooms+'">';
				html += '<td class="col-sm-1 center">';
					html += '<input class="form-control" type="text" name="room_num[]">';
				html += '</td>';
				html += '<td class="col-sm-2 center">';
					html += '<input class="form-control" type="text" name="room_floor[]">';
				html += '</td>';
				html += '<td class="col-sm-2 center">';
					html += '<select class="form-control room_status" name="room_status[]">';
						$.each(rm_status, function(key, value)
						{
							html += '<option value="'+value.id+'">'+value.status+'</option>';
						});
					html += '</select>';
				html += '</td>';
				html += '<td class="center col-sm-6">';
					html += '<a class="btn btn-default deactiveDatesModal" data-toggle="modal" data-target="#deactiveDatesModal" style="display: none;">';
						html += "{l s='Add Dates' mod='hotelreservationsystem'}";
					html += '</a>';
					html += '<input type="hidden" class="form-control disableDatesJSON" name="disableDatesJSON" value="0">';
					html += '<input type="text" class="form-control room_comment" name="room_comment[]">';
				html += '</td>';
				html += '<td class="center col-sm-1">';
					html += '<a href="#" class="remove-rooms-button btn btn-default"><i class="icon-trash"></i></a>';
				html += '</td>';
			html += '</tr>';

			$('.hotel-room').append(html);
		});

		// delete room
		$('.rm_htl_room').on('click',function(e) {
			e.preventDefault();

			var id_htl_info = $(this).attr('data-id-htl-info');
			$.ajax({
	            url: prod_link,
	            type: 'POST',
	            dataType: 'text',
	            data: {
	            	ajax:true,
	            	action:'deleteHotelRoom',
	            	id: id_htl_info,
	            },
	            success: function (result)
	            {
	            	if (parseInt(result) == 1)
	            	{
		               	showSuccessMessage("{l s='Remove successful'}");
	            	}
	            }
	        });
			$(this).closest(".room_data_values").remove();
		});

		$(".checkConfigurationClick").on("click", function() {
			$("#checkConfSubmit").val(1);
			return true;
		});

		// New Room Row Remove
		$(document).on('click','.remove-rooms-button',function(e) {
			e.preventDefault();
			$(this).closest(".room_data_values").remove();
		});

		// on changing the room status as disabled for some date range.....
		$(document).on("change", ".room_status", function(){
			var status_val = $(this).val();
			if (status_val == 3) {
				$(this).closest('.room_data_values').find('.room_comment, .deactiveDatesModal').toggle();
			} else {
				$(this).closest('.room_data_values').find('.room_comment').show();
				$(this).closest('.room_data_values').find('.deactiveDatesModal').hide();
			}
		});

		$(document).on("focus", ".disabled_date_from, .disabled_date_to", function () {
			$(".disabled_date_from").datepicker({
		        showOtherMonths: true,
		        dateFormat: 'yy-mm-dd',
		        minDate: 0,
		        //for calender Css
		        onSelect: function(selectedDate) {
		            $(this).closest('tr').find(".disabled_date_to").datepicker("option", "minDate", selectedDate).val('');
		        },
		    });
		    $(".disabled_date_to").datepicker({
		        showOtherMonths: true,
		        dateFormat: 'yy-mm-dd',
		        minDate: 0,
		    });
		});

		$('.add_more_room_disable_dates').on('click',function() {
	    	var rowKey = $(this).closest(".room_data_values").attr('data-rowKey');
			html = '<tr class="disabledDatesTr">';
				html += '<td class="col-sm-2 center">';
					html += '<input class="disabled_date_from form-control" type="text" value="" name="disabled_date_from'+rowKey+'[]">';
				html += '</td>';
				html += '<td class="col-sm-2 center">';
					html += '<input class="disabled_date_to form-control" type="text" value="" name="disabled_date_to'+rowKey+'[]">';
				html += '<td class="center col-sm-6">';
					html += '<input type="text" class="form-control room_disable_reason" value="" name="room_disable_reason'+rowKey+'[]">';
				html += '</td>';
				html += '<td class="center col-sm-1">';
					html += '<a href="#" class="remove-disable-dates-button btn btn-default"><i class="icon-trash"></i></a>';
				html += '</td>';
			html += '</tr>';

			$('.room-disable-dates').append(html);
		});

		$(document).on('click','.remove-disable-dates-button',function(e) {
			e.preventDefault();
			$(this).closest('tr').remove();
		});
	});

</script>