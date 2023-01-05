<div class="row">
	<div class="col-sm-4">
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-info"></i> {l s='Booking Form' mod='hotelreservationsystem'}
			</div>
			<div class="panel-body">
				<form method="post" action="">
					<div class="row">
						<div class="form-group col-sm-12">
							<label for="date_from" class="control-label col-sm-4 required">
								<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Check-In' mod='hotelreservationsystem'}</span>
							</label>
							<div class="col-sm-8">
								<input type="text" name="from_date" class="form-control" id="from_date" {if isset($date_from)}value="{$date_from|escape:'htmlall':'UTF-8'|date_format:"%d-%m-%Y"}"{/if}>
								<input type="hidden" name="date_from" id="date_from" {if isset($date_from)}value="{$date_from|escape:'htmlall':'UTF-8'}"{/if}>
								<input type="hidden" name="search_date_from" id="search_date_from" {if isset($date_from)}value="{$date_from|escape:'htmlall':'UTF-8'}"{/if}>
							</div>
						</div>
						<div class="form-group col-sm-12">
							<label for="to_date" class="control-label col-sm-4 required">
								<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Check-Out' mod='hotelreservationsystem'}</span>
							</label>
							<div class="col-sm-8">
								<input type="text" name="to_date" class="form-control" id="to_date" {if isset($date_to)}value="{$date_to|escape:'htmlall':'UTF-8'|date_format:"%d-%m-%Y"}"{/if}>
								<input type="hidden" name="date_to" id="date_to" {if isset($date_to)}value="{$date_to|escape:'htmlall':'UTF-8'}"{/if}>
								<input type="hidden" name="search_date_to" id="search_date_to" {if isset($date_to)}value="{$date_to|escape:'htmlall':'UTF-8'}"{/if}>
							</div>
						</div>
						<div class="form-group col-sm-12">
							<label for="id_hotel" class="control-label col-sm-4 required">
								<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Hotel Name' mod='hotelreservationsystem'}</span>
							</label>
							<div class="col-sm-8">
								<select name="id_hotel" class="form-control" id="id_hotel">
									{if isset($hotel_list) && $hotel_list}
										{foreach $hotel_list as $name_val}
											<option value="{$name_val['id']|escape:'htmlall':'UTF-8'}" {if isset($id_hotel) && ($name_val['id'] == $id_hotel)}selected{/if}>{$name_val['hotel_name']|escape:'htmlall':'UTF-8'}</option>
										{/foreach}
									{else}
										{l s='No hotels available' mod='hotelreservationsystem'}
									{/if}
								</select>
								<input type="hidden" name="search_id_hotel" id="search_id_hotel" {if isset($id_hotel)}value="{$id_hotel|escape:'htmlall':'UTF-8'}"{/if}>
							</div>
						</div>
						<div class="form-group col-sm-12">
							<label for="id_room_type" class="control-label col-sm-4">
								<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Room Type' mod='hotelreservationsystem'}</span>
							</label>
							<div class="col-sm-8">
								<select class="form-control" name="id_room_type" id="id_room_type">
									{if isset($id_room_type)}
										<option value='0' {if ($id_room_type == 0)}selected{/if}>{l s='All Types' mod='hotelreservationsystem'}</option>
										{if (isset($all_room_type) && $all_room_type)}
											{foreach $all_room_type as $val_type}
												<option value="{$val_type['id_product']|escape:'htmlall':'UTF-8'}" {if ($val_type['id_product'] == $id_room_type)}selected{/if}>{$val_type['room_type']|escape:'htmlall':'UTF-8'}</option>
											{/foreach}
										{/if}
									{/if}
								</select>
								<input type="hidden" name="search_id_room_type" id="search_id_room_type" value="{$id_room_type}">
							</div>
						</div>
						<div class="col-sm-12">
							<button id="search_hotel_list" name="search_hotel_list" type="submit" class="btn btn-primary pull-right">
								<i class="icon-search"></i>&nbsp;&nbsp;{l s='Search' mod='hotelreservationsystem'}
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
        <div class="panel">
            {include file="./_partials/search-stats.tpl"}
        </div>
	</div>
	<div class="col-sm-8">
        <div class="panel">
			<div class="panel-heading">
				<i class="icon-info"></i> {l s='Booking Calender' mod='hotelreservationsystem'}
				<button type="button" class="btn btn-primary" id="cart_btn" data-toggle="modal" data-target="#cartModal"><i class="icon-shopping-cart"></i> {l s='Cart' mod='hotelreservationsystem'} <span class="badge" id="cart_record">{$rms_in_cart}</span></button>
			</div>
            <div id='fullcalendar'></div>
		</div>
	</div>
	{if isset($booking_data) && $booking_data}
        {include file="./_partials/booking-rooms.tpl"}
    {/if}
</div>

<div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	{include file="./_partials/booking-cart.tpl"}
</div>
<div id="date-stats-tooltop" style="display:none">
	<div class="tooltip_cont">
		<div class="tip_header">
			<div class="tip_date"></div>
		</div>
		<div class="tip-body">
			<div class="total_rooms">
				<div class="tip_element_head">{l s='Total Rooms' mod='hotelreservationsystem'}</div>
				<div class="tip_element_value"></div>
			</div>
			<div class="num_avail">
				<div class="tip_element_head">{l s='Total Available' mod='hotelreservationsystem'}</div>
				<div class="tip_element_value"></div>
			</div>
			<div class="num_booked">
				<div class="tip_element_head">{l s='Booked Rooms' mod='hotelreservationsystem'}</div>
				<div class="tip_element_value"></div>
			</div>
			<div class="num_unavail">
				<div class="tip_element_head">{l s='Unavailable Rooms' mod='hotelreservationsystem'}</div>
				<div class="tip_element_value"></div>
			</div>
            <div class="num_part_avai">
				<div class="tip_element_head">{l s='Partially Available Rooms' mod='hotelreservationsystem'}</div>
				<div class="tip_element_value"></div>
			</div>
		</div>
	</div>
</div>
<template id="svg-icon">
<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="8" cy="8" r="8"/><path d="M8.06536 3C8.30937 3 8.51561 3.07032 8.6841 3.21097C8.85839 3.34693 8.94553 3.51336 8.94553 3.71027C8.94553 3.90717 8.85839 4.07595 8.6841 4.2166C8.51561 4.35724 8.30937 4.42757 8.06536 4.42757C7.82135 4.42757 7.6122 4.35724 7.43791 4.2166C7.26362 4.07595 7.17647 3.90717 7.17647 3.71027C7.17647 3.51336 7.26071 3.34693 7.42919 3.21097C7.60349 3.07032 7.81554 3 8.06536 3ZM8.78867 6.3685V11.5443C8.78867 11.9475 8.82353 12.2171 8.89325 12.353C8.96877 12.4843 9.07625 12.5827 9.21569 12.6484C9.36093 12.714 9.62237 12.7468 10 12.7468V13H6.122V12.7468C6.51126 12.7468 6.77269 12.7164 6.90632 12.6554C7.03994 12.5945 7.14452 12.4937 7.22004 12.353C7.30138 12.2124 7.34205 11.9428 7.34205 11.5443V9.06188C7.34205 8.36334 7.3159 7.91092 7.26362 7.70464C7.22295 7.55462 7.15904 7.45148 7.0719 7.39522C6.98475 7.33427 6.86565 7.3038 6.7146 7.3038C6.55192 7.3038 6.35439 7.33896 6.122 7.40928L6 7.15612L8.40523 6.3685H8.78867Z" fill="white"/></svg>
<template>