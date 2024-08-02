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
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
                <div class="modal-title">
                    <div class="row">
                        <div class="disabled-dates-modal-title"><i class="icon-calendar"></i>&nbsp; {l s='Disable Dates'} <span class="disabled-dates-modal-room-num"></span></div>
                        <div class="pull-right">
                            <button type="submit" class="btn btn-success margin-rt-10 add_new_dates">{l s='Add Dates'}</button>
                            <button type="submit" class="btn btn-danger margin-rt-20 remove_dates_btn">{l s='Remove Dates'}</button>
                            <button type="button" class="close margin-rt-10" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
			</div>
			<div class="modal-body">
                <div class="text-left messages-wrap" style="display: none;"></div>
                <div class="text-left room_not_saved_warning" style="display: none;">
                    <div class="alert alert-warning">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <ul class="list-unstyled">
                            <li>{l s='Please save this room first to add dates.'}</li>
                        </ul>
                    </div>
                </div>
                <div id="disabled_dates_form" class="panel" hidden>
                    <div class="disabled_dates_form_container">
                        <input type="hidden" class="id_disabled_date">
                        <input type="hidden" class="event_id">
                        <div class="form-group panel-heading col-xs-12">
                            <div class="form-title-text add_title">{l s='Add Dates'}</div>
                            <div class="form-title-text update_title">{l s='Update Dates'}</div>
                            <div class="form-title-text remove_title">{l s='Remove Dates'}</div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6 date_from_container">
                                <label class="control-label" for="disabled_date_from">
                                    <span>{l s='Date From'}</span>
                                </label>
                                <div>
                                    <div class="input-group">
                                        <input type="text" class="form-control disabled_date_from" name="disabled_date_from" value="" readonly>
                                        <div class="input-group-addon"><i class="icon-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 date_to_container">
                                <label class="control-label" for="disabled_date_to">
                                    <span>{l s='Date To'}</span>
                                </label>
                                <div>
                                    <div class="input-group">
                                        <input type="textarea" class="form-control disabled_date_to" name="disabled_date_to" value="" readonly>
                                        <div class="input-group-addon"><i class="icon-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-xs-12">
                            <label class="control-label" for="room_disable_reason">
                                <span>{l s='Reason'}</span>
                            </label>
                            <div class="input-group col-xs-12">
                                <textarea class="form-control room_disable_reason" name="room_disable_reason" value=""></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
    				    <button type="button" class="btn btn-default pull-left close_disabled_dates_form">{l s='Close'}</button>
    				    <button type="button" class="btn btn-primary pull-right submit_disabled_date">{l s='Submit'}</button>
    				    <button type="button" class="btn btn-primary pull-right submit_remove_date">{l s='Remove'}</button>
                    </div>
                </div>
                <div id="disabled_dates_full_calendar"></div>
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
{* needs some design*}
<div class="hidden">
    <div id="tooltip_info_block">
        <div class="tooltip_container tooltip_info_block">
            <div class="tooltip_title"></div>
            <div class="tooltip_content">
                <div class="row col-xs-7">
                    <div class="tooltip_label">{l s='Disabled Duration'} </div>
                    <div>
                        <span class="tooltip_date_from"></span> - <span class="tooltip_date_to"></span>
                    </div>
                </div>
                <div class="row col-xs-5">
                    <div class="tooltip_label">{l s='Disabled on'} </div>
                    <div<span class="tooltip_date_add"></span>
                </div>
                <div class="row col-xs-12"><div class="tooltip_label">{l s='Event Id'} </div><span class="tooltip_event_id_module"></span></div>
                <div><div class="tooltip_label col-xs-12">{l s='Reason'}</div><span class="tooltip_reason col-xs-12"></span></div>
            </div>
        </div>
    </div>
    <div id="tooltip_action_block">
        <div class="tooltip_container tooltip_action_block">
            <div class="tooltip_title">{l s='Select Action'}
                <button type="button" class="close margin-right-10" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="tooltip_content">
                <ul class="disabled_dates_actions">
                    <li class="remove_selected_dates btn btn-default">
                        <span class="remove_selected_dates">
                            <i class="icon-check"></i>
                            {l s='Make Room Available'}
                        </span>
                    </li>
                    <li class="add_selected_dates btn btn-default">
                        <span class="add_selected_dates">
                            <i class="icon-ban"></i>
                            {l s='Disable Room'}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
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
        var count = 0;
        var eventDates = {};
        const dateToday = new Date("{date('Y-m-d')}");
        const calendar = new FullCalendar.Calendar($('#disabled_dates_full_calendar').get(0), {
            initialView: 'dayGridMonth',
            initialDate: '{date('Y-m-d', time())}',
            dayMaxEventRows: true,
            selectable: true,
            unselectAuto: true,
            eventTextColor: '#333333',
            selectAllow: function(info) {
                $('#disabled_dates_full_calendar .tooltip_container').remove();
                let idRoom = parseInt($('#deactiveDatesModal').attr('data-id-room'));
                if (isNaN(idRoom)) {
                    return false;
                }
                var date_start = new Date(info.startStr);
                if (date_start < dateToday) {
                    return false;
                }

                return true;
            },
            eventDidMount: function(info) {
                // setting background display color
                updateEventDates(info.event, true);
                // will be used to get this particular event
                $(info.el).find('.fc-event-title').attr('data-event_id', info.event.id);
                if (info.isEnd) {
                    var is_deleteable = info.event.extendedProps.is_deleteable;
                    var is_editable = info.event.extendedProps.is_editable;
                    if (is_deleteable) {
                        $(info.el).find('.fc-event-title-container').append('<i class="icon-trash pull-right delete_disabled_dates"></i>');
                    }

                    if (is_editable) {
                        $(info.el).find('.fc-event-title-container').append('<i class="icon-pencil pull-right edit_disabled_dates"></i>');
                    }


                }

                var dateFrom = info.event.extendedProps.date_from_formatted;
                var event_date_to = new Date(info.event.extendedProps.date_to_formatted);
                // setting the date_to to -1 days since the full calendar does not includes the date to
                event_date_to.setDate(event_date_to.getDate() - 1);
                var dateTo = $.datepicker.formatDate('yy-mm-dd', event_date_to);
                var reason = info.event.extendedProps.reason;
                var event_title = info.event.extendedProps.event_title;
                var id_module_event = info.event.extendedProps.id_module_event;
                var module_event_url = info.event.extendedProps.module_event_url;
                var dateAdd = info.event.extendedProps.date_add;
                $('#tooltip_info_block .tooltip_date_from').text(dateFrom);
                $('#tooltip_info_block .tooltip_date_to').text(dateTo);
                $('#tooltip_info_block .tooltip_date_add').text(dateAdd);
                $('#tooltip_info_block .tooltip_reason').parent().hide();
                if (reason != '') {
                    $('#tooltip_info_block .tooltip_reason').text(reason).parent().show();
                }

                if (event_title != '') {
                    event_title = '<span class="tooltip_content_label">'+event_title+'</span>';
                    $('#tooltip_info_block .tooltip_title').html(event_title).show();
                } else {
                    $('#tooltip_info_block .tooltip_title').html('').hide();
                }

                if (id_module_event) {
                    var event_html = '(#'+id_module_event+')';
                    if (module_event_url != '') {
                        event_html = '<a target="_blank" href="'+module_event_url+'">'+ event_html + '</a>';
                    } else {
                        event_html = '<span>'+ event_html + '</span>';
                    }

                    $('#tooltip_info_block .tooltip_event_id_module').html(event_html).parent().show();
                } else {
                    $('#tooltip_info_block .tooltip_event_id_module').html('').parent().hide();
                }

                $('#tooltip_info_block .tooltip_container').attr('data-tooltip-id', info.event.id + '-'+ count);
                var html = $('#tooltip_info_block').html();
                var options = {
                    title: ' ',
                    html: true,
                    template: html,
                    container: $('#disabled_dates_full_calendar').closest('div'),
                    delay: {
                        show: 600,
                        hide: 300
                    },
                    placement: 'auto'
                }

                $(info.el).addClass('event-id-' + info.event.id);
                $(info.el).find('.fc-event-main-frame').tooltip(options);
                $(info.el).find('.fc-event-main-frame').attr('id', 'tooltip-id-' + info.event.id + '-' + count);
                count = count+1;
                if (info.isStart) {
                    $(info.el).find('.fc-event-title').addClass('display_title');
                } else {
                    $(info.el).find('.fc-event-title').addClass('hide_title');
                }

                $('.hide_title').text('');
            },
            eventWillUnmount: function(info) {
                updateEventDates(info.event, false);
            },
            select: function(info) {
                var selectedElement = $('.fc-daygrid-bg-harness').last();
                DisableDatesModal.resetDisabledDatesForm();
                DisableDatesModal.resetButtons();
                var data = {
                    disabled_date_from : info.startStr,
                    disabled_date_to : info.endStr,
                    id_disabled_date : '',
                    room_disable_reason : '',
                    event_id : ''
                };
                DisableDatesModal.setDisabledDateFormData(data);
                var html = $('#tooltip_action_block').html();
                var options = {
                    title: ' ',
                    html: true,
                    template: html,
                    trigger: 'click',
                    container: $('#disabled_dates_full_calendar').closest('div'),
                    delay: {
                        show: 600,
                        hide: 400
                    },
                    placement: 'auto|bottom',
                }
                $(selectedElement).tooltip(options);
                // since we are hiding the form after 200, if we show before hiding the position get wrong for the tooltip
                setTimeout(() => {
                    $(selectedElement).tooltip('show');
                }, 200);
            },
            unselect: function(){
                // since this will also work incase we select any action
                setTimeout(() => {
                    $('#disabled_dates_full_calendar .tooltip_action_block').remove();
                }, 1);
            }
        });
        function updateEventDates(event, add) {
            if (event.start && event.end) {
                let dateFrom = new Date(event.start);
                let endDate = new Date(event.end);
                dateFrom.setDate(dateFrom.getDate() + 1)
                let startDate = dateFrom;
                // Loop through all the days the event spans
                for (let date = startDate; date <= endDate; date.setDate(date.getDate() + 1)) {
                    let dateString = date.toISOString().split('T')[0];
                    if (add) {
                        if (!eventDates[dateString]) {
                            eventDates[dateString] = 0;
                        }

                        eventDates[dateString]++;
                    } else {
                        eventDates[dateString]--;
                        if (eventDates[dateString] <= 0) {
                            delete eventDates[dateString];
                        }
                    }
                }
            } else if (event.start) {
                let dateFrom = new Date(event.start);
                dateFrom.setDate(dateFrom.getDate() + 1);
                let dateString = dateFrom.toISOString().split('T')[0];
                if (add) {
                    if (!eventDates[dateString]) {
                        eventDates[dateString] = 0;
                    }

                    eventDates[dateString]++;
                } else {
                    eventDates[dateString]--;
                    if (eventDates[dateString] <= 0) {
                        delete eventDates[dateString];
                    }
                }
            }

            updateDayHighlights();
        }
        function updateDayHighlights() {
            const today = new Date().toISOString().split('T')[0];
            $('.fc-daygrid-day').each(function() {
                let dateString = $(this).data('date');
                if (dateString !== today) {
                    if (eventDates[dateString]) {
                        $(this).addClass('highlight-event-day');
                    } else {
                        $(this).removeClass('highlight-event-day');
                    }
                }
            });
        }
        $(document).on('mouseenter', '.tooltip_info_block', function(){
            var tooltipId = $(this).attr('data-tooltip-id');
            if ($('#tooltip-id-'+tooltipId).length) {
                $('#tooltip-id-'+tooltipId).tooltip('show');
            }
        });
        $(document).on('mouseleave', '.tooltip_info_block', function(){
            $('.fc-event-main-frame').tooltip('hide');
        });
        calendar.render();
        const DisableDatesModal = {
            initEvents: function(datesInfo) {
                if (datesInfo.length) {
                    var events = [];
                    $.each(datesInfo, function(i, v) {
                        var eventId = DisableDatesModal.getUniqueEventId();
                        events.push({
                            'id': DisableDatesModal.getUniqueEventId(),
                            'title': v['reason'],
                            'start': v['date_from'],
                            'end': v['date_to'],
                            'reason': v['reason'],
                            'date_add' : v['date_add'],
                            'id_disabled_date' : v['id'],
                            'is_editable' : v['is_editable'],
                            'event_title' : v['event_title'],
                            'is_deleteable' : v['is_deleteable'],
                            'date_to_formatted': v['date_to'],
                            'date_from_formatted': v['date_from'],
                            'id_module_event' : v['id_module_event'],
                            'module_event_url' : v['module_event_url'],
                            'backgroundColor': '#FFFFFF',
                            'borderColor': '#FFFFFF'
                        });
                    });
                    calendar.addEventSource(events);
                }
                DisableDatesModal.hideMessages();
            },
            disableCalendarActions: function() {
                $('#deactiveDatesModal .add_new_dates').hide();
                $('#deactiveDatesModal .remove_dates_btn').hide();
                $('#deactiveDatesModal .room_not_saved_warning').show();
            },
            enableCalendarActions: function() {
                $('#deactiveDatesModal .add_new_dates').show();
                $('#deactiveDatesModal .remove_dates_btn').show();
                $('#deactiveDatesModal .room_not_saved_warning').hide();
            },
            resetModalInfo: function(tr) {
                var source = calendar.getEventSources();
                if (source.length) {
                    $.each(source, function(i, event) {
                        event.remove();
                    });
                }

                count = 0;

                $('#deactiveDatesModal .disabled-dates-modal-room-num').html('');
                $('#disabled_dates_full_calendar .tooltip_container').remove();
                DisableDatesModal.resetButtons();
                DisableDatesModal.resetDisabledDatesForm();
                calendar.today();
            },
            resetDisabledDatesForm: function () {
                $('#disabled_dates_form .ui-datepicker').hide();
                $('.disabled_date_from').val('');
                $('.disabled_date_to').val('');
                $('.id_disabled_date').val('');
                $('.room_disable_reason').val('');
                $('.event_id').val('');
                $('.room_disable_reason').closest('.form-group').show();
                $('.date_from_container').datepicker("option", "minDate", "{date('Y-m-d')}");
                $('.date_from_container').find('.ui-datepicker').hide();
                $('.date_to_container').find('.ui-datepicker').hide();
                DisableDatesModal.hideDisabledDateForm();
                DisableDatesModal.hideMessages();
            },
            resetButtons: function() {
                $('.add_new_dates').removeClass('triggred');
                $('.remove_dates_btn').removeClass('triggred');
                $('.add_new_dates').removeClass('disabled');
                $('.remove_dates_btn').removeClass('disabled');
            },
            setDisabledDateFormData: function(data) {
                var event_date_to = new Date(data.disabled_date_to);
                // setting the date_to to -1 since the full calendar does not includes the date to
                event_date_to.setDate(event_date_to.getDate() - 1);
                var date_to_formatted = $.datepicker.formatDate('yy-mm-dd', event_date_to);
                $('.disabled_date_from').val(data.disabled_date_from);
                $('.disabled_date_to').val(date_to_formatted);
                $('.date_from_container').datepicker("setDate", data.disabled_date_from);
                $('.date_to_container').datepicker("setDate", date_to_formatted);
                $('.date_to_container').datepicker("option", "minDate", data.disabled_date_from)
                $('.id_disabled_date').val(data.id_disabled_date);
                $('.room_disable_reason').val(data.room_disable_reason);
                $('.event_id').val(data.event_id);
            },
            hideDisabledDateForm: function(){
                $('#disabled_dates_form').hide(200);
                $('.form-title-text').hide(200);
                $('.submit_disabled_date').hide(200);
                $('.submit_remove_date').hide(200);
            },
            displayDisabledDateForm: function(elem) {
                $('#disabled_dates_full_calendar').find('.tooltip_container').remove();
                calendar.unselect();
                $(elem).show(200);
                $('#disabled_dates_form').show(200);
                $('#deactiveDatesModal').animate({ scrollTop: 0 }, 'slow');
            },
            submitValidateDisableDates: function(cb) {
                let idRoom = parseInt($('#deactiveDatesModal').attr('data-id-room'));
                idRoom = idRoom == isNaN(idRoom) ? 0 : idRoom;
                var formElem = $('#disabled_dates_form');
                var dateFrom =  $(formElem).find('.disabled_date_from').val();
                var dateTo = $(formElem).find('.disabled_date_to').val();
                var eventId = parseInt($(formElem).find('.event_id').val());
                var reason = $(formElem).find('.room_disable_reason').val();
                var idProduct = $('[name="id_product"]').val();
                var id_disabled_date = parseInt($(formElem).find('.id_disabled_date').val());
                var event_date_to = new Date(dateTo);
                // setting the date_to +1 since the full calendar does not includes the date to
                event_date_to.setDate(event_date_to.getDate() + 1);
                var dateTo = $.datepicker.formatDate('yy-mm-dd', event_date_to);
                $.ajax({
                    url: prod_link,
                    type: 'POST',
                    data: {
                        ajax: true,
                        action: 'submitValidateDisableDates',
                        id_disabled_date : id_disabled_date,
                        id_product: idProduct,
                        id_room: idRoom,
                        date_from: dateFrom,
                        date_to: dateTo,
                        reason: reason
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.status) {
                            var validatedEvent = {
                                'id' : DisableDatesModal.getUniqueEventId(),
                                'title': reason,
                                'start': dateFrom,
                                'date_from_formatted': dateFrom,
                                'end': dateTo,
                                'date_to_formatted': dateTo,
                                'reason': reason,
                                'id': DisableDatesModal.getUniqueEventId(),
                                'is_deleteable' : 1,
                                'is_editable' : 1,
                                'id_disabled_date': response.id_disabled_date,
                                'event_title' : '',
                                'id_module_event' : '',
                                'module_event_url' : '',
                                'date_add' : "{date('Y-m-d')}",
                                'backgroundColor': '#FFFFFF',
                                'borderColor': '#FFFFFF'
                            }

                            if (!isNaN(eventId)) {
                                var olderEvent = calendar.getEventById(eventId);
                                if (olderEvent) {
                                    validatedEvent.is_deleteable = olderEvent.extendedProps.is_deleteable;
                                    validatedEvent.is_editable = olderEvent.extendedProps.is_editable;
                                    validatedEvent.event_title = olderEvent.extendedProps.event_title;
                                    validatedEvent.id_module_event = olderEvent.extendedProps.id_module_event;
                                    validatedEvent.module_event_url = olderEvent.extendedProps.module_event_url;
                                    olderEvent.remove();
                                }
                            }

                            var event = [];
                            event.push(validatedEvent);
                            calendar.addEventSource(event);
                            DisableDatesModal.resetDisabledDatesForm();
                            DisableDatesModal.resetButtons();
                        }
                        DisableDatesModal.showMessages(response.msg);
                    }
                });
            },
            deleteDisabledDate: function(id_disabled_date) {
                $.ajax({
                    url: prod_link,
                    type: 'POST',
                    data: {
                        ajax: true,
                        action: 'deleteDisabledDate',
                        id_disabled_date : id_disabled_date,
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        DisableDatesModal.showMessages(response.msg);
                    }
                });
            },
            showMessages: function(errors) {
                $('#deactiveDatesModal .messages-wrap').html(errors);
                $('#deactiveDatesModal .messages-wrap').show();
            },
            hideMessages: function() {
                $('#deactiveDatesModal .messages-wrap').hide();
                $('#deactiveDatesModal .messages-wrap').html('');
            },
            getDisableDatesInfo: function() {
                var disableDates = [];
                var events = calendar.getEvents();
                if (events.length) {
                    $.each(events, function(i, event) {
                        var data = {
                            date_from: event.extendedProps.date_from_formatted,
                            date_to: event.extendedProps.date_to_formatted,
                            reason: event.extendedProps.reason,
                            date_add : event.extendedProps.date_add,
                            id : event.extendedProps.id_disabled_date,
                            is_editable : event.extendedProps.is_editable,
                            event_title : event.extendedProps.event_title,
                            is_deleteable : event.extendedProps.is_deleteable,
                            id_module_event : event.extendedProps.id_module_event,
                            module_event_url : event.extendedProps.module_event_url
                        }

                        disableDates.push(data);
                    });
                }

                return disableDates;
            },
            getUniqueEventId: function() {
                var id = Math.floor(Math.random() * 100000);
                var event = calendar.getEventById(id);
                if (event) {
                    return DisableDatesModal.getUniqueEventId()
                }
                return id;
            }
        }

        $('#deactiveDatesModal').on('shown.bs.modal', function(e) {
            calendar.updateSize();
        });
        // Disable dates data filling when model open calendar.
        $('#deactiveDatesModal').on('show.bs.modal', function(e) {
            DisableDatesModal.resetModalInfo();
            const triggerRoomRow = $(e.relatedTarget);
            const idRoom = parseInt($(triggerRoomRow).attr('data-id-room'));
            const roomRowIndex = parseInt($(triggerRoomRow).closest('tr').attr('data-row-index'));
            var room_num = $(triggerRoomRow).closest('tr').find('[name="rooms_info['+roomRowIndex+'][room_num]"]').val();
            $('#deactiveDatesModal').attr('data-room-row-index', roomRowIndex);
            $('#deactiveDatesModal').attr('data-id-room', idRoom);
            let disableDates = $(triggerRoomRow).closest('tr').find('.disable_dates_json').val();
            if ($.trim(room_num) != '') {
                room_num = '( '+'{l s='Room No'}'+' '+room_num+')';
            }

            if (isNaN(idRoom)) {
                DisableDatesModal.disableCalendarActions();
            }

            $('#deactiveDatesModal .disabled-dates-modal-room-num').html(room_num);
            if (!disableDates) {
                return;
            }

            disableDates = JSON.parse(disableDates);
            DisableDatesModal.initEvents(disableDates);
        });

        // Disable dates data filling when model open calender.
        $('#deactiveDatesModal').on('hide.bs.modal', function(e) {
            const disableDates = DisableDatesModal.getDisableDatesInfo();
            const roomRowIndex = parseInt($('#deactiveDatesModal').attr('data-room-row-index'));
            const roomRow = $('#product-configuration .hotel-room tr.room_data_values[data-row-index='+roomRowIndex+']');
            $(roomRow).find('.disable_dates_json').val(JSON.stringify(disableDates));
            DisableDatesModal.resetModalInfo();
            DisableDatesModal.enableCalendarActions();
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
        $(document).on('click', '.remove-rooms-button', function(e) {
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

        $('.date_from_container').datepicker({
            showOtherMonths: true,
            dateFormat: 'yy-mm-dd',
            inline: true,
            minDate: 0,
            onSelect: function(selectedDate) {
                let objDateToMin = $.datepicker.parseDate('yy-mm-dd', selectedDate);
                $(this).find('.disabled_date_from').val(selectedDate);
                objDateToMin.setDate(objDateToMin.getDate());

                $(this).closest('#disabled_dates_form').find('.date_to_container').datepicker('option', 'minDate', objDateToMin);
                var dateTo = $(this).closest('#disabled_dates_form').find('.disabled_date_to').val();
                $(this).find('.ui-datepicker').hide();
                if (!dateTo || (dateTo && selectedDate > dateTo)) {
                    $('.disabled_date_to').val($.datepicker.formatDate('yy-mm-dd', objDateToMin));
                    $('.date_to_container').find('.ui-datepicker').show();
                }
            }
        });

        $('.date_to_container').datepicker({
            showOtherMonths: true,
            dateFormat: 'yy-mm-dd',
            inline: true,
            minDate: 0,
            beforeShow: function (input, instance) {
                let dateFrom = $(this).closest('#disabled_dates_form').find('.disabled_date_from').val();

                let objDateToMin = null;
                if (typeof dateFrom != 'undefined' && dateFrom != '') {
                    objDateToMin = $.datepicker.parseDate('yy-mm-dd', dateFrom);
                } else {
                    objDateToMin = new Date();
                }

                objDateToMin.setDate(objDateToMin.getDate() + 1);
                $(this).datepicker('option', 'minDate', objDateToMin);
            },
            onSelect: function(selectedDate) {
                $(this).find('.disabled_date_to').val(selectedDate);
                $(this).find('.ui-datepicker').hide();
            }
        });
        $(document).on('focus', '.disabled_date_from, .disabled_date_to', function() {
            if ($(this).hasClass('disabled_date_from')) {
                $('.date_to_container').find('.ui-datepicker').hide();
                $('.date_from_container').find('.ui-datepicker').show();
            } else if ($(this).hasClass('disabled_date_to')) {
                $('.date_to_container').find('.ui-datepicker').show();
                $('.date_from_container').find('.ui-datepicker').hide();
            }
        });
        $(document).on('focus click', function(e) {
            if (!$(e.target).closest('.date_from_container, .date_to_container').length
                && ($(e.target).hasClass('disabled_date_from') || $(e.target).hasClass('disabled_date_to'))
            ) {
                $('.ui-datepicker').hide();
            } else {
                if ($(e.target).hasClass('disabled_date_from')) {
                    $('.date_to_container').find('.ui-datepicker').hide();
                    $('.date_from_container').find('.ui-datepicker').show();
                } else if ($(e.target).hasClass('disabled_date_to')) {
                    $('.date_to_container').find('.ui-datepicker').show();
                    $('.date_from_container').find('.ui-datepicker').hide();
                } else if (!$(e.target).parents('.ui-datepicker').length
                    && (!$(e.target).hasClass('ui-corner-all') && !$(e.target).hasClass('ui-icon'))
                ) {
                    $('.ui-datepicker').hide();
                }
            }
        });
        $(document).on('click', '.delete_disabled_dates', function() {
            if (confirm("{l s='Are you sure?'}")) {
                var eventId = parseInt($(this).parent().find('.fc-event-title').attr('data-event_id'));
                var event = calendar.getEventById(eventId);
                $('.event-id-'+eventId).find('.fc-event-main-frame').tooltip('destroy');
                DisableDatesModal.deleteDisabledDate(event.extendedProps.id_disabled_date);
                event.remove();
                var formEventId = parseInt($('.event_id').val());
                if (!isNaN(formEventId) && formEventId == eventId) {
                    DisableDatesModal.resetDisabledDatesForm();
                }
            }
        });
        $(document).on('click', '.edit_disabled_dates', function() {
            var element = $(this).parent().find('.fc-event-title');
            var event = calendar.getEventById($(element).attr('data-event_id'));
            var data = {
                disabled_date_from : event.extendedProps.date_from_formatted,
                disabled_date_to : event.extendedProps.date_to_formatted,
                id_disabled_date : event.extendedProps.id_disabled_date,
                room_disable_reason : event.extendedProps.reason,
                event_id: $(element).attr('data-event_id')
            };

            DisableDatesModal.resetDisabledDatesForm();
            DisableDatesModal.setDisabledDateFormData(data);
            DisableDatesModal.resetButtons();
            DisableDatesModal.displayDisabledDateForm('.update_title');
            $('.submit_disabled_date').show(200);
        });
        $(document).on('click', '.submit_remove_date', function(e) {
            e.preventDefault();
            if (confirm("{l s='Are you sure you want to remove the selected date range?'}")) {
                let idRoom = parseInt($('#deactiveDatesModal').attr('data-id-room'));
                idRoom = idRoom == isNaN(idRoom) ? 0 : idRoom;
                const formElem = $('#disabled_dates_form');
                const dateFrom =  $(formElem).find('.disabled_date_from').val();
                var dateToFormatted = new Date($(formElem).find('.disabled_date_to').val());
                dateToFormatted.setDate(dateToFormatted.getDate() + 1);
                const dateTo = $.datepicker.formatDate('yy-mm-dd', dateToFormatted);
                var data = {
                    ajax: true,
                    action: 'enableSelectedDate',
                    id_room: idRoom,
                    date_from: dateFrom,
                    date_to: dateTo
                }
                $.ajax({
                    url: prod_link,
                    type: 'POST',
                    data: data,
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.status) {
                            DisableDatesModal.resetModalInfo();
                            if (response.new_disabled_dates.length) {
                                var events = [];
                                $.each(response.new_disabled_dates, function(index, value) {
                                    var eventId = DisableDatesModal.getUniqueEventId();
                                    events.push({
                                        'id' : eventId,
                                        'id_disabled_date' : value['id'],
                                        'title': value['reason'],
                                        'start': value['date_from'],
                                        'end': value['date_to'],
                                        'reason': value['reason'],
                                        'date_add' : value['date_add'],
                                        'is_editable' : value['is_editable'],
                                        'event_title' : value['event_title'],
                                        'date_to_formatted': value['date_to'],
                                        'date_from_formatted': value['date_from'],
                                        'is_deleteable' : value['is_deleteable'],
                                        'id_module_event' : value['id_module_event'],
                                        'module_event_url' : value['module_event_url'],
                                        'backgroundColor': '#FFFFFF',
                                        'borderColor': '#FFFFFF'
                                    });
                                });
                                calendar.addEventSource(events);
                            }
                            DisableDatesModal.resetDisabledDatesForm();
                        }
                        DisableDatesModal.showMessages(response.msg);
                    }
                });
            }
        });
        $(document).on('click', '.add_new_dates', function(e){
            e.preventDefault();
            DisableDatesModal.resetDisabledDatesForm();
            DisableDatesModal.displayDisabledDateForm('.add_title');
            $('.submit_disabled_date').show(200);
        });
        $(document).on('click', '.tooltip_action_block .close', function(){
            $(this).closest('.tooltip_action_block').remove();
        });
        $(document).on('click', '.remove_dates_btn', function(e){
            e.preventDefault();
            DisableDatesModal.resetDisabledDatesForm();
            DisableDatesModal.displayDisabledDateForm('.remove_title');
            $('.date_from_container').datepicker("option", "minDate", null);
            $('.room_disable_reason').closest('.form-group').hide();
            $('.submit_remove_date').show(200);
        });
        $(document).on('click', function(e) {
            $('.hide_title').text('');
        });
        $(document).on('click', '.add_selected_dates', function(e){
            e.preventDefault();
            DisableDatesModal.displayDisabledDateForm('.add_title');
            $('.submit_disabled_date').show(200);
        });
        $(document).on('click', '.remove_selected_dates', function(e){
            e.preventDefault();
            $('.room_disable_reason').closest('.form-group').hide();
            $('.date_from_container').datepicker("option", "minDate", null);
            DisableDatesModal.displayDisabledDateForm('.remove_title');
            $('.submit_remove_date').show();
        });
        $(document).on('click', '.submit_disabled_date', function(e){
            e.preventDefault();
            if (confirm("{l s='Are you sure?'}")) {
                $('.add_new_dates').removeClass('triggred');
                $('.room_disable_reason').closest('.form-group').show();
                DisableDatesModal.submitValidateDisableDates();
            }
        });
        $(document).on('click', '.close_action_tooltip', function(){
            $('#disabled_dates_full_calendar .tooltip_container').remove();
        });
        $(document).on('click', '.close_disabled_dates_form', function(){
            DisableDatesModal.resetDisabledDatesForm();
        });
    });
</script>
