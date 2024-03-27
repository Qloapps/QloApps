{**
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
*}

{if isset($product->id)}
	<div id="product-configuration" class="panel product-tab">
		<input type="hidden" name="submitted_tabs[]" value="Configuration" />
		<h3 class="tab"> <i class="icon-AdminAdmin"></i> {l s='Rooms'}</h3>

		<input type="hidden" id="checkConfSubmit" value="0" name="checkConfSubmit">

		<div class="from-group table-responsive-row clearfix">
			<table class="table hotel-room">
				<thead>
					<tr class="nodrag nodrop">
						<th class="col-sm-2 center">
							<label class="control-label required">
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
						<th class="col-sm-3 center">
							<label class="control-label">
								<span class="label-tooltip" data-toggle="tooltip" data-original-title="{l s='Enter extra information about this room. Leave empty if not required.'}">
									{l s='Extra Information'}
								</span>
							</label>
						</th>
						<th class="col-sm-2 center">
							<label class="control-label">
								<span class="label-tooltip" data-toggle="tooltip" data-original-title="{l s='Set date ranges when room is set to Temporarily Inactive.'}">
									{l s='Disable Dates'}
								</span>
							</label>
						</th>
                        <th class="col-sm-1 center">
                            {l s='--'}
                        </th>
					</tr>
				</thead>
				<tbody>
					{if isset($smarty.post.rooms_info) && is_array($smarty.post.rooms_info) && count($smarty.post.rooms_info)}
						{assign var="rooms_info" value=$smarty.post.rooms_info}
					{elseif isset($htl_room_info) && is_array($htl_room_info) && count($htl_room_info)}
						{assign var="rooms_info" value=$htl_room_info}
					{/if}
					{if isset($rooms_info) && is_array($rooms_info) && count($rooms_info)}
						{foreach from=$rooms_info key=key item=room_info}
							{assign var="var_name_room_info" value="rooms_info[`$key`]"}
							<tr class="room_data_values" data-row-index="{$key}">
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
								<td class="col-sm-3 center">
									<input type="text" class="form-control room_comment" value="{$room_info['comment']}" name="{$var_name_room_info|cat:'[comment]'}">
								</td>
								<td class="col-sm-2 center">
									<a class="btn btn-default deactiveDatesModal {if $room_info['id_status'] != $rm_status['STATUS_TEMPORARY_INACTIVE']['id'] }disabled{/if}" data-toggle="modal" data-target="#deactiveDatesModal" data-id-room="{$room_info['id']}">{if $room_info['id_status'] != $rm_status['STATUS_TEMPORARY_INACTIVE']['id'] }{l s='Add Dates'}{else}{l s='View Dates'}{/if}
									</a>
									<input type="hidden" class="form-control disable_dates_json" name="{$var_name_room_info|cat:'[disable_dates_json]'}" {if $room_info['id_status'] == $rm_status['STATUS_TEMPORARY_INACTIVE']['id']}value="{$room_info['disable_dates_json']|escape:'html':'UTF-8'}"{/if}>
								</td>
								<td class="col-sm-1 center">
									{if isset($room_info['id'])}
                                        <input type="hidden" class="booked-dates" name="{$var_name_room_info|cat:'[booked_dates]'}" value="{$room_info['booked_dates']|escape:'html':'UTF-8'}">
                                        <a href="#" class="view_htl_room btn btn-default" data-toggle="modal" data-target="#room-dates-modal" data-id-room="{$room_info['id']}"><i class="icon-info"></i></a>
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
							<tr class="room_data_values" data-row-index="{$k}">
								<td class="col-sm-1 center">
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
                                <td class="center col-sm-3">
									<input type="text" class="form-control room_comment" name="{$var_name_room_info|cat:'[comment]'}">
                                </td>
								<td class="center col-sm-2">
									<a class="btn btn-default deactiveDatesModal disabled" data-toggle="modal" data-target="#deactiveDatesModal">
										{l s='Add Dates'}
									</a>
									<input type="hidden" class="form-control disable_dates_json" name="{$var_name_room_info|cat:'[disable_dates_json]'}" value="">
								</td>
								<td class="center col-sm-1">
								    {if $k == 1}
										<a href="#" class="remove-rooms-button btn btn-default"><i class="icon-trash"></i></a>
                                    {else}
                                        <a href="#" class="remove-rooms-button btn btn-default disabled"><i class="icon-trash"></i></a>
								    {/if}
                                </td>
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
				<h4 class="modal-title"><i class="icon-calendar"></i>&nbsp; {l s='Disable Dates'}</h4>
			</div>
			<div class="modal-body">
				<div class="text-left errors-wrap" style="display: none;"></div>
				<div class="alert alert-info">
					<p>{l s='Please note that the date chosen for field \'Date To\' is not considered as a blocking date.'}</p>
				</div>
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
                                <th class="col-sm-1 center"></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
					<div class="form-group">
						<div class="col-sm-12">
							<a href="#" class="add_more_room_disable_dates btn btn-default">
                                <i class="icon icon-plus"></i>
                                <span>{l s="Add More"}</span>
                            </a>
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

<div class="modal fade" id="room-dates-modal" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close margin-right-10" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="icon-calendar"></i>&nbsp; {l s='Upcoming bookings'}</h4>
            </div>
            <div class="room-booked-dates-table modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><span>{l s='Order'}</span></th>
                                <th><span>{l s='Date From'}</span></th>
                                <th><span>{l s='Date To'}</span></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close">{l s='Done'}</button>
            </div>
        </div>
    </div>
</div>

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

    $(document).ready(function() {
        const DisableDatesModal = {
            init: function() {
                DisableDatesModal.addNewRow();
                DisableDatesModal.hideErrors();
                DisableDatesModal.removeAllInvalidRowDataMarkers();
            },
            addNewRow: function() {
                $('#deactiveDatesModal tbody').append(this.disableDatesRowHtml);
            },
            populateWithDatesInfo: function(datesInfo) {
                const $this = this;
                $(datesInfo).each(function(i, dateRange) {
                    $('#deactiveDatesModal tbody').append($this.disableDatesRowHtml);
                    const dateRangeRow = $('#deactiveDatesModal tbody tr').last();
                    $(dateRangeRow).find('.disabled_date_from').val(dateRange.date_from);
                    $(dateRangeRow).find('.disabled_date_to').val(dateRange.date_to);
                    $(dateRangeRow).find('.room_disable_reason').val(dateRange.reason);
                });
            },
            validateDisableDates: function(cb) {
                DisableDatesModal.hideErrors();
                DisableDatesModal.removeAllInvalidRowDataMarkers();
                DisableDatesModal.disableRowDeleteActionButtons();

                let idRoom = parseInt($('#deactiveDatesModal .room-disable-dates').attr('data-id-room'));
                idRoom = idRoom == isNaN(idRoom) ? 0 : idRoom;
                const disableDates = Array();
                $('#deactiveDatesModal .room-disable-dates tbody tr').each(function(i, tr) {
                    const date_from = $(tr).find('.disabled_date_from').val().trim();
                    const date_to = $(tr).find('.disabled_date_to').val().trim();
                    disableDates.push({ date_from, date_to });
                });

                $.ajax({
                    url: prod_link,
                    type: 'POST',
                    data: {
                        ajax: true,
                        action: 'validateDisableDates',
                        id_room: idRoom,
                        disable_dates: disableDates,
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.status) {
                            if (typeof cb === 'function') {
                                cb();
                            }
                        } else {
                            DisableDatesModal.showErrors(response.errors);
                            DisableDatesModal.addInvalidRowDataMarkers(response.rows_to_highlight);
                        }
                    },
                    complete: function () {
                        DisableDatesModal.enableRowDeleteActionButtons();
                    }
                });
            },
            showErrors: function(errors) {
                $('#deactiveDatesModal .errors-wrap').html(errors);
                $('#deactiveDatesModal .errors-wrap').show();
            },
            hideErrors: function() {
                $('#deactiveDatesModal .errors-wrap').hide();
                $('#deactiveDatesModal .errors-wrap').html('');
            },
            addInvalidRowDataMarkers: function(rowsToHighlight) {
                rowsToHighlight.map(function (rowIndex) {
                    const tr = $('#deactiveDatesModal .room-disable-dates tbody tr').eq(rowIndex);
                    DisableDatesModal.markRowDataInvalid(tr);
                });
            },
            getDisableDatesInfo: function() {
                const disableDates = Array();
                $('#deactiveDatesModal .room-disable-dates tbody tr').each(function(i, tr) {
                    disableDates.push({
                        date_from: $(tr).find('.disabled_date_from').val().trim(),
                        date_to: $(tr).find('.disabled_date_to').val().trim(),
                        reason: $(tr).find('.room_disable_reason').val().trim(),
                    });
                });

                return disableDates;
            },
            disableRowDeleteActionButtons: function() {
                const disableDates = Array();
                $('#deactiveDatesModal .room-disable-dates .remove-disable-dates-button').addClass('disabled');
            },
            enableRowDeleteActionButtons: function() {
                const disableDates = Array();
                $('#deactiveDatesModal .room-disable-dates .remove-disable-dates-button').removeClass('disabled');
            },
            markRowDataInvalid: function(tr) {
                $(tr).css({ 'outline': '1px solid #D27C82', 'border-radius': '2px' });
            },
            removeAllInvalidRowDataMarkers: function(tr) {
                $('#deactiveDatesModal .room-disable-dates tr').css('outline', '');
                $(tr).css('outline', '');
            },
            removeRowDataMark: function(tr) {
                $(tr).css('outline', '');
            },
            disableDatesRowHtml: `
                <tr class="disabledDatesTr">
                    <td class="col-sm-2 center">
                        <input type="text" class="form-control disabled_date_from" value="" readonly>
                    </td>
                    <td class="col-sm-2 center">
                        <input type="text" class="form-control disabled_date_to" value="" readonly>
                    </td>
                    <td class="center col-sm-6">
                        <input type="text" class="form-control room_disable_reason" value="">
                    </td>
                    <td class="center col-sm-1">
                        <a href="#" class="remove-disable-dates-button btn btn-default"><i class="icon-trash"></i></a>
                    </td>
                </tr>
            `,
        }

        // Disable dates data filling when model open
        $('#deactiveDatesModal').on('show.bs.modal', function(e) {
            const triggerRoomRow = $(e.relatedTarget);
            const roomRowIndex = parseInt($(triggerRoomRow).closest('tr').attr('data-row-index'));
            const idRoom = parseInt($(triggerRoomRow).attr('data-id-room'));
            $('#deactiveDatesModal table.room-disable-dates tbody').html('');
            $('#deactiveDatesModal table.room-disable-dates').attr('data-room-row-index', roomRowIndex);
            $('#deactiveDatesModal table.room-disable-dates').attr('data-id-room', idRoom);
            let disableDates = $(triggerRoomRow).closest('tr').find('.disable_dates_json').val();
            if (!disableDates) {
                DisableDatesModal.init();
                return;
            }

            disableDates = JSON.parse(disableDates);
            DisableDatesModal.hideErrors();
            DisableDatesModal.populateWithDatesInfo(disableDates);
        });

        // Disable dates data filling when model open
        $('#deactiveDatesModal').on('hide.bs.modal', function(e) {
            const disableDates = DisableDatesModal.getDisableDatesInfo();
            const roomRowIndex = parseInt($('#deactiveDatesModal table.room-disable-dates').attr('data-room-row-index'));
            const roomRow = $('#product-configuration .hotel-room tr.room_data_values[data-row-index='+roomRowIndex+']');
            $(roomRow).find('.disable_dates_json').val(JSON.stringify(disableDates));
        });

        // copy json formatted dates to room
        $(document).on('click', '.deactiveDatesModalSubmit', function() {
            DisableDatesModal.validateDisableDates(function () {
                const disableDates = DisableDatesModal.getDisableDatesInfo();
                const roomRowIndex = parseInt($('#deactiveDatesModal table.room-disable-dates').attr('data-room-row-index'));
                const roomRow = $('#product-configuration .hotel-room tr.room_data_values[data-row-index='+roomRowIndex+']');
                $(roomRow).find('.disable_dates_json').val(JSON.stringify(disableDates));
                $('#deactiveDatesModal').modal('hide');
            });
        });

        {literal}
        $('#room-dates-modal').on('show.bs.modal', function(e) {
            const triggerRoom = $(e.relatedTarget);
            $('#room-dates-modal tbody').html('');
            var bookedDates = JSON.parse($(triggerRoom).closest('tr').find('.booked-dates').val());
            if (bookedDates.length) {
                if (bookedDates.length) {
                    $('#room-dates-modal .room-booked-dates-table').show();
                    $(bookedDates).each(function() {
                        $('#room-dates-modal .room-booked-dates-table tbody').append(`<tr>
                            <td><a href="{/literal}{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}{literal}&vieworder&id_order=${this.id_order}" target="_blank">#${this.id_order}</a></td>
                            <td>${this.date_from_formatted}</td>
                            <td>${this.date_to_formatted}</td>
                        </tr>`);
                    });
                }
            } else {
                $('#room-dates-modal .room-booked-dates-table tbody').append(`<tr>
                    <td colspan="3" class="center">{/literal}{l s='No Booking for this room'}{literal}</td>
                </tr>`);
            }
        });
        {/literal}

        // Add new room detail
        $('#add-more-rooms-button').on('click',function() {
            var lengthRooms = parseInt($('.room_data_values').length);

            var prefix = 'rooms_info['+lengthRooms+']';
            html = '<tr class="room_data_values" data-row-index="'+lengthRooms+'">';
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
                html += '<td class="col-sm-3 center">';
                    html += '<input class="form-control" type="text" name="'+prefix+'[floor]">';
                html += '</td>';
                html += '<td class="center col-sm-2">';
                    html += '<a class="btn btn-default deactiveDatesModal disabled" data-toggle="modal" data-target="#deactiveDatesModal">';
                        html += "{l s='Add Dates'}";
                    html += '</a>';
                    html += '<input type="hidden" class="form-control disable_dates_json" name="'+prefix+'[disable_dates_json]">';
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
                dataType: 'JSON',
                data: {
                    ajax:true,
                    action:'deleteHotelRoom',
                    id: id_htl_info,
                },
                success: function (response) {
                    if (response.success) {
                        showSuccessMessage("{l s='Removed successfully'}");
                        $current.closest(".room_data_values").remove();
                    } else {
                        if (response.errors)
                        showErrorMessage(response.errors);
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

        // remove room row
        $(document).on('click','.remove-rooms-button',function(e) {
            e.preventDefault();
            $(this).closest('.room_data_values').remove();
            $('#product-configuration table.hotel-room tr.room_data_values').each(function(iOuter, tr) {
                $(tr).attr('data-row-index', iOuter);
                $(tr).find('input, select').each(function (iInner, inputField) {
                    let fieldName = $(inputField).attr('name');
                    fieldName = fieldName.replace(/[0-9]+/, iOuter)
                    $(inputField).attr('name', fieldName);
                });
            });
        });

        // on changing the room status as disabled for some date range
        $(document).on('change', '.room_status', function(){
            var status_val = $(this).val();
            if (status_val == rm_status.STATUS_TEMPORARY_INACTIVE.id) {
                $(this).closest('.room_data_values').find('.deactiveDatesModal').removeClass('disabled');
            } else {
                $(this).closest('.room_data_values').find('.deactiveDatesModal').addClass('disabled');
            }
        });

        $(document).on('focus', '.disabled_date_from, .disabled_date_to', function () {
            $('.disabled_date_from').datepicker({
                showOtherMonths: true,
                dateFormat: 'yy-mm-dd',
                minDate: 0,
                onSelect: function(selectedDate) {
                    let objDateToMin = $.datepicker.parseDate('yy-mm-dd', selectedDate);
                    objDateToMin.setDate(objDateToMin.getDate() + 1);

                    $(this).closest('tr').find('.disabled_date_to').datepicker('option', 'minDate', objDateToMin);
                },
                onClose: function(selectedDate) {
                    var dateTo = $(this).closest('tr').find('.disabled_date_to').val();
                    if (!dateTo || (dateTo && selectedDate >= dateTo)) {
                        $(this).closest('tr').find('.disabled_date_to').datepicker('show');
                    }
                },
            });

            $('.disabled_date_to').datepicker({
                showOtherMonths: true,
                dateFormat: 'yy-mm-dd',
                minDate: 0,
                beforeShow: function (input, instance) {
                    let dateFrom = $(this).closest('tr').find('.disabled_date_from').val();

                    let objDateToMin = null;
                    if (typeof dateFrom != 'undefined' && dateFrom != '') {
                        objDateToMin = $.datepicker.parseDate('yy-mm-dd', dateFrom);
                    } else {
                        objDateToMin = new Date();
                    }

                    objDateToMin.setDate(objDateToMin.getDate() + 1);
                    $(this).datepicker('option', 'minDate', objDateToMin);
                },
            });
        });

        $('.add_more_room_disable_dates').on('click', function() {
            DisableDatesModal.addNewRow();
        });

        $(document).on('click','.remove-disable-dates-button',function(e) {
            e.preventDefault();
            $(this).closest('tr').remove();
        });
    });
</script>
