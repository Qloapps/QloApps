{if isset($product->id) && isset($htl_config)}
	<div id="product-configuration" class="panel product-tab">
		<input type="hidden" name="submitted_tabs[]" value="Booking"/>
		<h3 class="tab"> <i class="icon-info"></i> {l s='Booking Information' mod='hotelreservationsystem'}</h3>

		<div class="form-group">
			<div class="col-sm-1"></div>
			<label for="from_date" class="control-label col-sm-1 required">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Booking Date starts from'}">{l s='From' mod='hotelreservationsystem'}</span>
			</label>
			<div class="col-sm-2">
				<input type="hidden" id="checkTabClick" value="0" name="checkTabClick">
				<input type="text" name="from_date" class="form-control" id="from_date" value="{$date_from}">
			</div>

			<label for="to_date" class="control-label col-sm-1 required">
				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Booking Date upto'}">{l s='To' mod='hotelreservationsystem'}</span>
			</label>
			<div class="col-sm-2">
				<input type="text" name="to_date" class="form-control" id="to_date" value="{$date_to}">
			</div>
				<input type="hidden" id="product_id" name="id_product" value="{$product->id}">
				<input type="hidden" id="hotel_id" name="id_hotel" value="{$rooms_info.id_hotel}">
				<!-- <input type="hidden" id="num_adults" name="num_adults" value="{$rooms_info.adult}">
				<input type="hidden" id="num_children" name="num_children" value="{$rooms_info.children}"> -->
		</div>

		<div class="form-group">
			<div class="col-sm-1"></div>
			<div class="hotel_date col-sm-6">
				<div class="row margin-leftrgt-0">
					<div class="col-sm-12 htl_date_header">
						<div class="col-sm-4">
							<p class="htl_date_disp">{$date_from|date_format:"%d"}</p>
							<span class="htl_month_disp">{$date_from|date_format:"%b"}</span>
						</div>
						<div class="col-sm-2">
							<p class="htl_date_disp">-</p>
						</div>
						<div class="col-sm-3">
							<p class="htl_date_disp">{$date_to|date_format:"%d"}</p>
							<span class="htl_month_disp">{$date_to|date_format:"%b"}</span>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-sm-5 htl_room_data_cont">
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-12 htl_room_cat_data">
								<p class="room_cat_header">{l s='Total Rooms' mod='hotelreservationsystem'}</p>
								<p class="room_cat_data">{$booking_data['stats']['total_rooms']}</p>
							</div>
						</div>
						<hr class="hr_style" />
					</div>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-12 htl_room_cat_data no_border">
								<p class="room_cat_header">{l s='Partially Available' mod='hotelreservationsystem'}</p>
								<p class="room_cat_data">{$booking_data['stats']['num_part_avai']}</p>
							</div>
						</div>
						<hr class="hr_style" />
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-12 htl_room_cat_data">
								<p class="room_cat_header">{l s='Available Rooms' mod='hotelreservationsystem'}</p>
								<p class="room_cat_data">{$booking_data['stats']['num_avail']}</p>
							</div>
						</div>
						<hr class="hr_style" />
					</div>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-12 htl_room_cat_data no_border">
								<p class="room_cat_header">{l s='Booked Rooms' mod='hotelreservationsystem'}</p>
								<p class="room_cat_data">{$booking_data['stats']['num_booked']}</p>
							</div>
						</div>
						<hr class="hr_style" />
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-12 htl_room_cat_data">
								<p class="room_cat_header">{l s='Unavailable Rooms' mod='hotelreservationsystem'}</p>
								<p class="room_cat_data">{$booking_data['stats']['num_unavail']}</p>
							</div>
						</div>
						<hr class="hr_style" />
					</div>
				</div>

				<div class="row">
					<div class="col-sm-6 indi_cont clearfix">
						<div class="color_indicate bg-green"></div>
						<span class="indi_label">{l s='Available Rooms' mod='hotelreservationsystem'}</span>
					</div>
					<div class="col-sm-6 indi_cont clearfix">
						<div class="color_indicate bg-yellow"></div>
						<span class="indi_label">{l s='Partially Available' mod='hotelreservationsystem'}</span>
					</div>
					<div class="col-sm-6 indi_cont clearfix">
						<div class="color_indicate bg-red"></div>
						<span class="indi_label">{l s='Unavailable Rooms' mod='hotelreservationsystem'}</span>
					</div>
					<div class="col-sm-6 indi_cont clearfix">
						<div class="color_indicate bg-gray"></div>
						<span class="indi_label">{l s='Hold For Maintenance' mod='hotelreservationsystem'}</span>
					</div>
				</div>
			</div>
		</div>
		
		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default">
				<i class="process-icon-cancel"></i>
				{l s='Cancel' mod='hotelreservationsystem'}
			</a>
			<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled" id="stayBookingTab">
				<i class="process-icon-loading"></i>
					{l s='Display Bookings'}
			</button>
		</div>
	</div>

	<div class="panel">
		<h3 class="tab"> <i class="icon-list"></i> {l s='LIST OF HOTEL ROOMS' mod='hotelreservationsystem'}</h3>
		<div class="form-group">
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>{l s='Room No.' mod='hotelreservationsystem'}</th>
							<th>{l s='Status' mod='hotelreservationsystem'}</th>
							<th>{l s='Message' mod='hotelreservationsystem'}</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$booking_data['rm_data'][0]['data'] key=b_key item=b_val}
							{if ($b_key == 'available') && !empty($b_val)}
								{foreach from=$b_val key=data_k item=data_v}
									<tr>
										<td>{$data_v['room_num']}</td>
										<td>{l s='Available' mod='hotelreservationsystem'}</td>
										<td>{$data_v['room_comment']}</td>
									</tr>
								{/foreach}
							{elseif ($b_key == 'unavailable') && !empty($b_val)}
								{foreach from=$b_val key=data_k item=data_v}
									<tr>
										<td>{$data_v['room_num']}</td>
										<td>{l s='Unavailable' mod='hotelreservationsystem'}</td>
										<td>{$data_v['room_comment']}</td>
									</tr>
								{/foreach}
							{elseif ($b_key == 'booked') && !empty($b_val)}
								{foreach from=$b_val key=data_k item=data_v}
									<tr>
										<td>{$data_v['room_num']}</td>
										<td>
											{if $data_v['detail'][0]['booking_status'] == 1}
												{l s='Alloted' mod='hotelreservationsystem'}
											{elseif $data_v['detail'][0]['booking_status'] == 2}
												{l s='Checked-in' mod='hotelreservationsystem'}
											{elseif $data_v['detail'][0]['booking_status'] == 3}
												{l s='Checked-out' mod='hotelreservationsystem'}
											{/if}
										</td>
										<td>{$data_v['detail'][0]['comment']}</td>
									</tr>
								{/foreach}
							{elseif ($b_key == 'partially_available') && !empty($b_val)}
								{foreach from=$b_val key=data_k item=data_v}
									<tr>
										<td>{$data_v['room_num']}</td>
										<td>{l s='Partially Available' mod='hotelreservationsystem'}</td>
										<td>{$data_v['comment']}</td>
									</tr>
								{/foreach}
							{/if}
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>
{/if}

<script type="text/javascript">

$(document).ready(function()
{	
	var booking_calendar_data = '{$booking_calendar_data|@json_encode}';
	var check_css_condition_var = '{$check_css_condition_var}';
	var check_calendar_var = '{$check_calendar_var}';

	$("#from_date, #to_date").datepicker(
	{
		dateFormat: 'yy-mm-dd'
	});
	if (booking_calendar_data != 'null' && check_css_condition_var && check_calendar_var)
	{
		if (typeof(booking_calendar_data) != 'undefined')
	    {
	        var calendar_data = JSON.parse(booking_calendar_data);

	        $(".hotel_date").datepicker(
	        {
	            defaultDate: new Date(),
	            dateFormat: 'dd-mm-yy',
	            minDate: 0,
	            onChangeMonthYear: function(year, month)
	            {
	                if (check_calendar_var)
	                    $.ajax({
	                        url: "{$link->getAdminLink('AdminProducts')|addslashes}",
	                        data: {
	                            ajax:true,
	                            action:'ProductRoomsBookingDetailsOnMonthChange',
	                            month:month,
	                            year:year,
	                            id_product:$('#product_id').val(),
	                            id_hotel:$('#hotel_id').val(),
	                            num_adults:$('#num_adults').val(),
	                            num_children:$('#num_children').val(),
	                        },
	                        method:'POST',
	                        async: false,
	                        success: function (result)
	                        {
	                            calendar_data = JSON.parse(result);
	                        },
	                        error: function(XMLHttpRequest, textStatus, errorThrown)
	                        {
	                            alert(textStatus);
	                        }
	                    });
	            },
	            beforeShowDay: function(date)
	            {
	                var currentMonth = date.getMonth() + 1;
	                var currentDate = date.getDate();
	                if (currentMonth < 10)
	                {
	                    currentMonth = '0' + currentMonth;
	                }
	                if (currentDate < 10)
	                {
	                    currentDate = '0' + currentDate;
	                }

	                dmy = date.getFullYear() + "-" + currentMonth + "-" + currentDate;
	                var flag = 0;

	                $.each(calendar_data, function(key, value)
	                {
	                    if (key === dmy)
	                    {
	                        msg = 'Total Available : '+value.stats.num_avail+'&#013;Total Partail Available : '+value.stats.num_part_avai+'&#013;Total Unvailable : '+value.stats.num_unavail+'&#013;Total Booked : '+value.stats.num_booked;
	                        flag = 1;
	                        return 1;
	                    }
	                });
	                if (flag)
	                {
	                    return [true, check_css_condition_var, msg];
	                }
	                else
	                    return [true];
	            }
	        });
	        
	        var count = $("."+check_css_condition_var).length;
	        //$("td."+check_css_condition_var).eq(0).css('border-radius','50% 0 0 50%');
	        $("td."+check_css_condition_var).eq(count-1).css('border-radius','0 50% 50% 0');    
	    }
	    else
	    {
	    	$(".hotel_date").datepicker(
	        {
	            defaultDate: new Date(),
	            dateFormat: 'dd-mm-yy',
	            minDate: 0,
	        });
	    }
	}

    $("#stayBookingTab").on("click", function()
    {
    	$("#checkTabClick").val(1);
    });
});
	
</script>