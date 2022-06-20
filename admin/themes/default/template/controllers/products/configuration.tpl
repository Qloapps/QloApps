{if isset($product->id)}
	<div id="product-configuration" class="panel product-tab">
		<input type="hidden" name="submitted_tabs[]" value="Configuration" />
		<h3 class="tab"> <i class="icon-AdminAdmin"></i> {l s='Configuration'}</h3>

		<input type="hidden" id="checkConfSubmit" value="0" name="checkConfSubmit">

		<div class="from-group table-responsive-row clearfix">
			<table class="table hotel-room">
				<thead>
					<tr class="nodrag nodrop">
						<th class="col-sm-2 center">
							<label class="control-label">
								<span class="label-tooltip" data-toggle="tooltip" data-original-title="{l s='Enter room number. For eg. A-101, A-102 etc. Invalid characters <>;=#{}'}">
									{l s='Room No.'}
								</span>
							</label>
						</th>
						<th class="col-sm-2 center">
							<label class="control-label">
								<span class="label-tooltip" data-toggle="tooltip" data-original-title="{l s='Enter floor of the room. For eg. First, Second etc. Invalid characters <>;=#{}'}">
									{l s='Floor'}
								</span>
							</label>
						</th>
						<th class="col-sm-2 center">
							<label class="control-label">
								<span class="label-tooltip" data-toggle="tooltip" data-original-title="{l s='Select status of the room.'}">
									{l s='Status'}
								</span>
							</label>
						</th>
						<th class="col-sm-6 center">
							<label class="control-label">
								<span class="label-tooltip" data-toggle="tooltip" data-original-title="{l s='Enter extra information about this room. Leave empty if not required.'}">
									{l s='Extra Information'}
								</span>
							</label>
						</th>
					</tr>
				</thead>
				<tbody>
					{if isset($smarty.post.rooms_info) && is_array($smarty.post.rooms_info) && count($smarty.post.rooms_info)}
						{assign var="rooms_info" value=$smarty.post.rooms_info}
					{elseif isset($htl_room_info) && is_array($htl_room_info) && count($htl_room_info)}
						{assign var="rooms_info" value=$htl_room_info}
					{/if}
					{if is_array($rooms_info) && count($rooms_info)}
						{foreach from=$rooms_info key=key item=room_info}
							{assign var="var_name_room_info" value="rooms_info[`$key`]"}
							<tr class="room_data_values" id="row_index{$key}" data-rowKey="{$key}">
								<td class="col-sm-1 center">
									<input class="form-control" type="text" value="{$room_info['room_num']}" name="{$var_name_room_info|cat:'[room_num]'}">
								</td>
								<td class="col-sm-2 center">
									<input class="form-control" type="text" value="{$room_info['floor']}" name="{$var_name_room_info|cat:'[floor]'}">
								</td>
								<td class="col-sm-2 center">
									<select class="form-control room_status" name="{$var_name_room_info|cat:'[id_status]'}">
										{foreach from=$rm_status item=room_stauts}
											<option value="{$room_stauts['id']}" {if $room_info['id_status'] == {$room_stauts['id']}}selected="selected"{/if}>{$room_stauts['status']}</option>
										{/foreach}
									</select>
								</td>
								<td class="center col-sm-6">
									<a class="btn btn-default deactiveDatesModal" data-toggle="modal" data-target="#deactiveDatesModal" {if $room_info['id_status'] != $rm_status['STATUS_TEMPORARY_INACTIVE']['id'] }style="display: none;"{/if}>{if $room_info['id_status'] != $rm_status['STATUS_TEMPORARY_INACTIVE']['id'] }{l s='Add Dates'}{else}{l s='View Dates'}{/if}
									</a>
									<input type="text" class="form-control room_comment" value="{$room_info['comment']}" name="{$var_name_room_info|cat:'[comment]'}" {if $room_info['id_status'] == $rm_status['STATUS_TEMPORARY_INACTIVE']['id'] }style="display: none;"{/if}>
									<input type="hidden" class="form-control disableDatesJSON" name="{$var_name_room_info|cat:'[disable_dates_json]'}" {if $room_info['id_status'] == $rm_status['STATUS_TEMPORARY_INACTIVE']['id']}value="{$room_info['disable_dates_json']|escape:'html':'UTF-8'}"{/if}>
								</td>
								<td class="center col-sm-1">
									{if isset($room_info['id'])}
										<a href="#" class="rm_htl_room btn btn-default" data-id-htl-info="{$room_info['id']}"><i class="icon-trash"></i></a>
										<input type="hidden" name="{$var_name_room_info|cat:'[id]'}" value="{$room_info['id']}">
									{else}
										<a href="#" class="remove-rooms-button btn btn-default"><i class="icon-trash"></i></a>
									{/if}
								</td>
							</tr>
						{/foreach}
					{else}
						{for $k=0 to 1}
							{assign var="var_name_room_info" value="rooms_info[`$k`]"}
							<tr class="room_data_values" id="row_index{$k}" data-rowKey="{$k}">
								<td class="col-sm-2 center">
									<input class="form-control" type="text" name="{$var_name_room_info|cat:'[room_num]'}">
								</td>
								<td class="col-sm-2 center">
									<input class="form-control" type="text" name="{$var_name_room_info|cat:'[floor]'}">
								</td>
								<td class="col-sm-2 center">
									<select class="form-control room_status" name="{$var_name_room_info|cat:'[id_status]'}">
										{foreach from=$rm_status item=room_stauts}
											<option value="{$room_stauts['id']}">{$room_stauts['status']}</option>
										{/foreach}
									</select>
								</td>
								<td class="center col-sm-6">
									<a class="btn btn-default deactiveDatesModal" data-toggle="modal" data-target="#deactiveDatesModal" style="display: none;">
										{l s='Add Dates'}
									</a>
									<input type="text" class="form-control room_comment" name="{$var_name_room_info|cat:'[comment]'}">
									<input type="hidden" class="form-control disable_dates_json" name="{$var_name_room_info|cat:'[disable_dates_json]'}" value="0">
								</td>
								{if $k == 1}
									<td class="center col-sm-1">
										<a href="#" class="remove-rooms-button btn btn-default"><i class="icon-trash"></i></a>
									</td>
								{/if}
							</tr>
						{/for}
					{/if}
				</tbody>
			</table>
			<div class="form-group">
				<div class="col-sm-12">
					<button id="add-more-rooms-button" class="btn btn-default" type="button" data-size="s" data-style="expand-right">
						<i class="icon icon-plus"></i>
						{l s='Add More Rooms'}
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
				<h4 class="modal-title"><i class="icon-calendar"></i>&nbsp; {l s='Disable dates'}</h4>
			</div>
			<div class="modal-body">
				<div class="from-group table-responsive-row clearfix">
					<table class="table room-disable-dates">
						<thead>
							<tr class="nodrag nodrop">
								<th class="col-sm-1 center">
									<span>{l s='Date From'}</span>
								</th>
								<th class="col-sm-2 center">
									<span>{l s='Date To'}</span>
								</th>
								<th class="col-sm-2 center">
									<span>{l s='Reason'}</span>
								</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					<div class="form-group">
						<div class="col-sm-12">
							<a href="#" class="add_more_room_disable_dates btn btn-default"><i class="icon icon-plus"></i>{l s="Add More"}</a>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default deactiveDatesModalSubmit">{l s='Done'}</button>
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
			var lengthRooms = parseInt($('.room_data_values').length);

			var prefix = 'rooms_info['+lengthRooms+']';
			html = '<tr class="room_data_values" id="row_index'+lengthRooms+'" data-rowKey="'+lengthRooms+'">';
				html += '<td class="col-sm-1 center">';
					html += '<input class="form-control" type="text" name="'+prefix+'[room_num]">';
				html += '</td>';
				html += '<td class="col-sm-2 center">';
					html += '<input class="form-control" type="text" name="'+prefix+'[floor]">';
				html += '</td>';
				html += '<td class="col-sm-2 center">';
					html += '<select class="form-control room_status" name="'+prefix+'[id_status]">';
						$.each(rm_status, function(key, value) {
							html += '<option value="'+value.id+'">'+value.status+'</option>';
						});
					html += '</select>';
				html += '</td>';
				html += '<td class="center col-sm-6">';
					html += '<a class="btn btn-default deactiveDatesModal" data-toggle="modal" data-target="#deactiveDatesModal" style="display: none;">';
						html += "{l s='Add Dates'}";
					html += '</a>';
					html += '<input type="hidden" class="form-control disableDatesJSON" name="'+prefix+'[comment]" value="0">';
					html += '<input type="text" class="form-control room_comment" name="'+prefix+'[disable_dates_json]">';
				html += '</td>';
				html += '<td class="center col-sm-1">';
					html += '<a href="#" class="remove-rooms-button btn btn-default"><i class="icon-trash"></i></a>';
				html += '</td>';
			html += '</tr>';

			$('table.hotel-room tbody').append(html);
		});

		// delete room
		$('.rm_htl_room').on('click',function(e) {
			e.preventDefault();
			var $current = $(this);
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
	            success: function (result) {
	            	if (parseInt(result) == 1) {
		               	showSuccessMessage("{l s='Removed successfully'}");
						$current.closest(".room_data_values").remove();
	            	} else {
						showErrorMessage("{l s='Some error occurred'}");
					}
	            },
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					showErrorMessage("{l s='Some error occurred'}");
				}
	        });
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
			if (status_val == rm_status.STATUS_TEMPORARY_INACTIVE.id) {
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