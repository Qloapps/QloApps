<div class="row">
	{if isset($hotel_list) && is_array($hotel_list) && count($hotel_list)}
		<div class="col-sm-4">
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-info"></i> {l s='Booking Form' mod='hotelreservationsystem'}
				</div>
				<div class="panel-body">
					<form method="post" action="" id="room-search-form">
						<div class="row">
							{* <div class="form-group col-sm-12">
								<label for="booking_product" class="control-label col-sm-4 required">
									<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Product type' mod='hotelreservationsystem'}</span>
								</label>
								<div class="col-sm-8">
									<select name="booking_product" class="form-control" id="booking_product">
										<option value="1" {if isset($booking_product) && $booking_product == 1}selected{/if}>{l s='Rooms'}</option>
										<option value="0" {if isset($booking_product) && $booking_product == 0}selected{/if}>{l s='Service Products'}</option>
									</select>
								</div>
							</div> *}
							{hook h='displayAdminRoomsBookingSearchFormFieldsBefore'}
							<div class="form-group col-sm-12">
								<label for="date_from" class="control-label col-sm-4 required">
									<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Check-In' mod='hotelreservationsystem'}</span>
								</label>
								<div class="col-sm-8">
									<input type="text" name="from_date" class="form-control" id="from_date" {if isset($date_from)}value="{$date_from|escape:'htmlall':'UTF-8'|date_format:"%d-%m-%Y"}"{/if} readonly>
									<input type="hidden" name="date_from" id="date_from" {if isset($date_from)}value="{$date_from|escape:'htmlall':'UTF-8'}"{/if}>
									<input type="hidden" name="search_date_from" id="search_date_from" {if isset($date_from)}value="{$date_from|escape:'htmlall':'UTF-8'}"{/if}>
								</div>
							</div>
							<div class="form-group col-sm-12">
								<label for="to_date" class="control-label col-sm-4 required">
									<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Check-Out' mod='hotelreservationsystem'}</span>
								</label>
								<div class="col-sm-8">
									<input type="text" name="to_date" class="form-control" id="to_date" {if isset($date_to)}value="{$date_to|escape:'htmlall':'UTF-8'|date_format:"%d-%m-%Y"}"{/if} readonly>
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
							{if $is_occupancy_wise_search}
								<div class="form-group col-sm-12">
									<label for="occupancy" class="control-label col-sm-4 required">
										<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Occupancy' mod='hotelreservationsystem'}</span>
									</label>
									<div class="col-sm-8">
										<div class="dropdown">
											<button class="booking_guest_occupancy btn btn-default btn-left btn-block input-occupancy" id="search_occupancy" type="button">
												<span class="">{if (isset($occupancy_adults) && $occupancy_adults)}{$occupancy_adults} {if $occupancy_adults > 1}{l s='Adults' mod='hotelreservationsystem'}{else}{l s='Adult' mod='hotelreservationsystem'}{/if}, {if isset($occupancy_children) && $occupancy_children}{$occupancy_children} {if $occupancy_children > 1} {l s='Children' mod='hotelreservationsystem'}{else}{l s='Child' mod='hotelreservationsystem'}{/if}, {/if}{$occupancy|count} {if $occupancy|count > 1}{l s='Rooms' mod='hotelreservationsystem'}{else}{l s='Room' mod='hotelreservationsystem'}{/if}{else}{l s='1 Adult, 1 Room' mod='hotelreservationsystem'}{/if}</span>
											</button>
											<input type="hidden" class="max_avail_type_qty" value="{if isset($total_available_rooms)}	{$total_available_rooms|escape:'html':'UTF-8'}{/if}">
											<div class="dropdown-menu booking_occupancy_wrapper well well-sm">
												<div class="booking_occupancy_inner row">
													{if isset($occupancy) && $occupancy}
														{assign var=countRoom value=1}
														<hr class="occupancy-info-separator col-sm-12">
														{foreach from=$occupancy key=key item=$room_occupancy name=occupancyInfo}
															<div class="occupancy_info_block" occ_block_index="{$key|escape:'htmlall':'UTF-8'}">
																<div class="occupancy_info_head col-sm-12"><label class="room_num_wrapper">{l s='Room' mod='hotelreservationsystem'} - {$countRoom|escape:'htmlall':'UTF-8'} </label>{if !$smarty.foreach.occupancyInfo.first}<a class="remove-room-link pull-right" href="#">{l s='Remove' mod='hotelreservationsystem'}</a>{/if}</div>
																<div class="col-sm-12">
																	<div class="row">
																		<div class="form-group col-xs-6 occupancy_count_block">
																			<label>{l s='Adults' mod='hotelreservationsystem'}</label>
																			<input type="number" class="form-control num_occupancy num_adults" name="occupancy[{$key|escape:'htmlall':'UTF-8'}][adults]" value="{$room_occupancy['adults']|escape:'htmlall':'UTF-8'}" min="1">
																		</div>
																		<div class="form-group col-xs-6 occupancy_count_block">
																			<label>{l s='Children' mod='hotelreservationsystem'} <span class="label-desc-txt"></span></label>
																			<input type="number" class="form-control num_occupancy num_children" name="occupancy[{$key|escape:'htmlall':'UTF-8'}][children]" value="{$room_occupancy['children']|escape:'htmlall':'UTF-8'}" min="0">
																			({l s='Below' mod='hotelreservationsystem'}  {$max_child_age|escape:'htmlall':'UTF-8'} {l s='years' mod='hotelreservationsystem'})
																		</div>
																	</div>
																	<div class="row children_age_info_block" {if !$room_occupancy['children']}style="display:none"{/if}>
																		<div class="form-group col-sm-12">
																			<label class="">{l s='All Children' mod='hotelreservationsystem'}</label>
																			<div class="">
																				<div class="row children_ages">
																					{if isset($room_occupancy['child_ages']) && $room_occupancy['child_ages']}
																						{foreach $room_occupancy['child_ages'] as $childAge}
																							<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
																								<select class="guest_child_age room_occupancies" name="occupancy[{$key|escape:'htmlall':'UTF-8'}][child_ages][]">
																									<option value="-1" {if $childAge == -1}selected{/if}>{l s='Select age' mod='hotelreservationsystem'}</option>
																									<option value="0" {if $childAge == 0}selected{/if}>{l s='Under 1' mod='hotelreservationsystem'}</option>
																									{for $age=1 to ($max_child_age-1)}
																										<option value="{$age|escape:'htmlall':'UTF-8'}" {if $childAge == $age}selected{/if}>{$age|escape:'htmlall':'UTF-8'}</option>
																									{/for}
																								</select>
																							</div>
																						{/foreach}
																					{/if}
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															<hr class="occupancy-info-separator col-sm-12">
															{assign var=countRoom value=$countRoom+1}
														{/foreach}
													{else}
														<div class="occupancy_info_block col-sm-12" occ_block_index="0">
															<div class="occupancy_info_head col-sm-12"><label class="room_num_wrapper">{l s='Room - 1' mod='hotelreservationsystem'}</label></div>
															<div class="col-sm-12">
																<div class="row">
																	<div class="form-group col-xs-6 occupancy_count_block">
																		<label>{l s='Adults' mod='hotelreservationsystem'}</label>
																		<input type="number" class="form-control num_occupancy num_adults" name="occupancy[0][adults]" value="1" min="1">
																	</div>
																	<div class="form-group col-xs-6 occupancy_count_block">
																		<label>{l s='Children' mod='hotelreservationsystem'} <span class="label-desc-txt"></span></label>
																		<input type="number" class="form-control num_occupancy num_children" name="occupancy[0][children]" value="0" min="0">
																		({l s='Below' mod='hotelreservationsystem'}  {$max_child_age|escape:'htmlall':'UTF-8'} {l s='years' mod='hotelreservationsystem'})
																	</div>
																</div>
																<div class="row children_age_info_block" style="display:none">
																	<div class="form-group col-sm-12">
																		<label class="">{l s='All Children' mod='hotelreservationsystem'}</label>
																		<div class="">
																			<div class="row children_ages">
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<hr class="occupancy-info-separator col-sm-12">
													{/if}
												</div>
												<div class="add_occupancy_block col-sm-12">
													<a class="add_new_occupancy_btn" href="#"><i class="icon-plus"></i> <span>{l s='Add Room' mod='hotelreservationsystem'}</span></a>
												</div>
											</div>
										</div>
									</div>
								</div>
							{/if}
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
			{if !isset($booking_product) || (isset($booking_product) && $booking_product == 1)}
				<div class="panel">
					{include file="./_partials/search-stats.tpl"}
				</div>
			{/if}
		</div>
		<div class="col-sm-8">
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-info"></i> {if !isset($booking_product) || (isset($booking_product) && $booking_product == 1)}{l s='Booking Calender' mod='hotelreservationsystem'}{else}{l s='Service Products' mod='hotelreservationsystem'}{/if }
					<button type="button" class="btn btn-primary {if $total_products_in_cart|intval == 0}disabled{/if}" id="cart_btn" data-toggle="modal" data-target="#cartModal"><i class="icon-shopping-cart"></i> {l s='Cart' mod='hotelreservationsystem'} <span class="badge" id="cart_record">{$total_products_in_cart}</span></button>
				</div>
				{if !isset($booking_product) || (isset($booking_product) && $booking_product == 1)}
					<div id='fullcalendar'></div>
					{hook h='displayAdminRoomsBookingCalendarAfter'}
					<div class="row calendar-legend">
						<div class="indi_cont clearfix">
							<span class="color_indicate bg-green"></span>
							<span class="indi_label">{l s='Available Rooms' mod='hotelreservationsystem'}</span>
						</div>
						<div class="indi_cont clearfix">
							<span class="color_indicate bg-yellow"></span>
							<span class="indi_label">{l s='Partially Available' mod='hotelreservationsystem'}</span>
						</div>
						<div class="indi_cont clearfix">
							<span class="color_indicate bg-red"></span>
							<span class="indi_label">{l s='Unavailable Rooms' mod='hotelreservationsystem'}</span>
						</div>
						<div class="indi_cont clearfix">
							<span class="color_indicate bg-blue"></span>
							<span class="indi_label">{l s='Booked Rooms' mod='hotelreservationsystem'}</span>
						</div>
					</div>
				{else}
					<div class="panel-body">
						{include file="./_partials/service-products.tpl"}
					</div>
				{/if}
			</div>
		</div>
		{if !isset($booking_product) || (isset($booking_product) && $booking_product == 1)}
			{if isset($booking_data) && $booking_data}
				{include file="./_partials/booking-rooms.tpl"}
			{/if}
		{/if}
	{else}
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-warning"></i> {l s='No Hotels' mod='hotelreservationsystem'}
			</div>

			<div class="alert alert-warning">
				<p>{l s='No active hotels available for booking.' mod='hotelreservationsystem'}</p>
				<p>{l s='You can manage hotels from ' mod='hotelreservationsystem'} <a href="{$link->getAdminLink('AdminAddHotel')}">{l s='Hotel Reservation System > Manage Hotel' mod='hotelreservationsystem'}</a> {l s='page.' mod='hotelreservationsystem'}</p>
			</div>
		</div>
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
