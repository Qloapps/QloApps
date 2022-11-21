{if !isset($ajax_delete)}
<div class="panel col-sm-12">
	<h3 class="tab">
		<i class="icon-info"></i> {l s='Booking Information' mod='hotelreservationsystem'}
		<button type="button" class="btn btn-primary pull-right margin-right-10" id="cart_btn" data-toggle="modal" data-target="#cartModal"><i class="icon-shopping-cart"></i> {l s='Cart' mod='hotelreservationsystem'} <span class="badge" id="cart_record">{$rms_in_cart}</span></button>
	</h3>
	<div class="panel-body padding-0">
	{if isset($booking_data) && $booking_data}
		<div class="row">
			<div class="col-sm-4">
				<div class="row box-border margin-right-10">
					<form method="post" action="">
						<div class="row">
							<div class="form-group col-sm-12">
								<label for="from_date" class="control-label col-sm-4 required">
									<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Check-In' mod='hotelreservationsystem'}</span>
								</label>
								<div class="col-sm-8">
									<input type="text" name="from_date" class="form-control" id="from_date" {if isset($date_from)}value="{$date_from|escape:'htmlall':'UTF-8'}"{/if}>
									<input type="hidden" name="search_date_from" id="search_date_from" {if isset($date_from)}value="{$date_from|escape:'htmlall':'UTF-8'}"{/if}>
								</div>
							</div>
							<div class="form-group col-sm-12">
								<label for="to_date" class="control-label col-sm-4 required">
									<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Check-Out' mod='hotelreservationsystem'}</span>
								</label>
								<div class="col-sm-8">
									<input type="text" name="to_date" class="form-control" id="to_date" {if isset($date_to)}value="{$date_to|escape:'htmlall':'UTF-8'}"{/if}>
									<input type="hidden" name="search_date_to" id="search_date_to" {if isset($date_to)}value="{$date_to|escape:'htmlall':'UTF-8'}"{/if}>
								</div>
							</div>
							<div class="form-group col-sm-12">
								<label for="hotel_id" class="control-label col-sm-4 required">
									<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Hotel Name' mod='hotelreservationsystem'}</span>
								</label>
								<div class="col-sm-8">
									<select name="hotel_id" class="form-control" id="hotel_id">
										{if isset($hotel_name) && $hotel_name}
											{foreach $hotel_name as $name_val}
												<option value="{$name_val['id']|escape:'htmlall':'UTF-8'}" {if isset($hotel_id) && ($name_val['id'] == $hotel_id)}selected{/if}>{$name_val['hotel_name']|escape:'htmlall':'UTF-8'}</option>
											{/foreach}
										{else}
											{l s='No hotels available' mod='hotelreservationsystem'}
										{/if}
									</select>
								</div>
							</div>
							<div class="form-group col-sm-12">
								<label for="room_type" class="control-label col-sm-4">
									<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Room Type' mod='hotelreservationsystem'}</span>
								</label>
								<div class="col-sm-8">
									<select class="form-control" name="room_type" id="room_type">
										{if isset($room_type)}
											<option value='0' {if ($room_type == 0)}selected{/if}>{l s='All Types' mod='hotelreservationsystem'}</option>
											{if (isset($all_room_type) && $all_room_type)}
												{foreach $all_room_type as $val_type}
													<option value="{$val_type['id_product']|escape:'htmlall':'UTF-8'}" {if ($val_type['id_product'] == $room_type)}selected{/if}>{$val_type['room_type']|escape:'htmlall':'UTF-8'}</option>
												{/foreach}
											{/if}
										{/if}
									</select>
									<input type="hidden" name="search_id_prod" id="search_id_prod" value="{$room_type}">
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
			<div class="col-sm-8">
				<div class="row margin-lr-0 box-border calender-main-div">
					<div class="hotel_date col-sm-12 col-md-7">
						<div class="row margin-leftrgt-0">
							<div class="col-sm-12 htl_date_header">
								<div class="col-sm-3">
									<p class="htl_date_disp">{$date_from|escape:'htmlall':'UTF-8'|date_format:"%d"}</p>
									<span class="htl_month_disp">{$date_from|escape:'htmlall':'UTF-8'|date_format:"%b"}</span>
								</div>
								<div class="col-sm-1">
									<p class="htl_date_disp">-</p>
								</div>
								<div class="col-sm-3">
									<p class="htl_date_disp">{$date_to|escape:'htmlall':'UTF-8'|date_format:"%d"}</p>
									<span class="htl_month_disp">{$date_to|escape:'htmlall':'UTF-8'|date_format:"%b"}</span>
								</div>
							</div>
						</div>
					</div>

					<div class="col-sm-12 col-md-5 htl_room_data_cont">
						<div class="row">
							<div class="col-sm-6">
								<div class="row">
									<div class="col-sm-12 htl_room_cat_data">
										<p class="room_cat_header">{l s='Total Rooms' mod='hotelreservationsystem'}</p>
										<p class="room_cat_data">{if isset($booking_data) && $booking_data}{$booking_data['stats']['total_rooms']|escape:'htmlall':'UTF-8'}{else}00{/if}</p>
									</div>
								</div>
								<hr class="hr_style" />
							</div>
							<div class="col-sm-6">
								<div class="row">
									<div class="col-sm-12 htl_room_cat_data no_border">
										<p class="room_cat_header">{l s='Partially Available' mod='hotelreservationsystem'}</p>
										<p class="room_cat_data" id="num_part">{if isset($booking_data) && $booking_data}{$booking_data['stats']['num_part_avai']|escape:'htmlall':'UTF-8'}{else}00{/if}</p>
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
										<p class="room_cat_data" id="num_avail">{if isset($booking_data) && $booking_data}{$booking_data['stats']['num_avail']|escape:'htmlall':'UTF-8'}{else}00{/if}</p>
									</div>
								</div>
								<hr class="hr_style" />
							</div>
							<div class="col-sm-6">
								<div class="row">
									<div class="col-sm-12 htl_room_cat_data no_border">
										<p class="room_cat_header">{l s='Booked Rooms' mod='hotelreservationsystem'}</p>
										<p class="room_cat_data">{if isset($booking_data) && $booking_data}{$booking_data['stats']['num_booked']|escape:'htmlall':'UTF-8'}{else}00{/if}</p>
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
										<p class="room_cat_data">{if isset($booking_data) && $booking_data}{$booking_data['stats']['num_unavail']|escape:'htmlall':'UTF-8'}{else}00{/if}</p>
									</div>
								</div>
								<hr class="hr_style" />
							</div>
							<div class="col-sm-6">
								<div class="row">
									<div class="col-sm-12 htl_room_cat_data">
										<p class="room_cat_header">{l s='In-Cart Rooms' mod='hotelreservationsystem'}</p>
										<p class="room_cat_data" id="cart_stats">{if isset($booking_data) && $booking_data}{$booking_data['stats']['num_cart']|escape:'htmlall':'UTF-8'}{else}00{/if}</p>
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
							<!-- <div class="col-sm-6 indi_cont clearfix">
								<div class="color_indicate bg-gray"></div>
								<span class="indi_label">{l s='Hold For Maintenance' mod='hotelreservationsystem'}</span>
							</div> -->
						</div>
					</div>
				</div>
			</div>
		</div>
	{else}
		<p class="alert alert-warning">	{l s="No booking information found. Please make sure at least one active hotel and room type must be available." mod="hotelreservationsystem"}</p>
	{/if}
{/if}
	{if isset($booking_data) && $booking_data}
		<div class="row margin-div" id="htl_rooms_list">
			<div class="col-sm-12">
				<ul class="nav nav-tabs">
					{foreach from=$booking_data['rm_data'] key=book_k item=book_v}
						<li {if $book_k == 0}class="active"{/if} ><a href="#room_type_{$book_k}" data-toggle="tab">{$book_v['name']}</a></li>
					{/foreach}
				</ul>
				<div class="tab-content panel">
					{foreach from=$booking_data['rm_data'] key=book_k item=book_v}
						<div id="room_type_{$book_k}" class="tab-pane {if $book_k == 0}active{/if}">
							<ul class="nav nav-tabs">
								<li class="active"><a href="#avail_room_data_{$book_k|escape:'htmlall':'UTF-8'}" data-toggle="tab">{l s='Available Rooms' mod='hotelreservationsystem'}</a></li>
								<li><a href="#part_room_data_{$book_k|escape:'htmlall':'UTF-8'}" data-toggle="tab">{l s='Partially Available' mod='hotelreservationsystem'}</a></li>
								<li><a href="#book_room_data_{$book_k|escape:'htmlall':'UTF-8'}" data-toggle="tab">{l s='Booked Rooms' mod='hotelreservationsystem'}</a></li>
								<li><a href="#unavail_room_data_{$book_k|escape:'htmlall':'UTF-8'}" data-toggle="tab">{l s='Unavailable Rooms' mod='hotelreservationsystem'}</a></li>
							</ul>
							<div class="tab-content panel">
								<div id="avail_room_data_{$book_k|escape:'htmlall':'UTF-8'}" class="tab-pane active">
									<div class="table-responsive">
										<table class="table">
											<thead>
												<tr>
													<th><span class="title_box">{l s='Room No.' mod='hotelreservationsystem'}</span></th>
													<th><span class="title_box">{l s='Duration' mod='hotelreservationsystem'}</span></th>
													<th><span class="title_box">{l s='Message' mod='hotelreservationsystem'}</span></th>
													<th><span class="title_box">{l s='Allotment Type' mod='hotelreservationsystem'}</span></th>
													<th><span class="title_box">{l s='Action' mod='hotelreservationsystem'}</span></th>
												</tr>
											</thead>
											<tbody>
												{foreach from=$book_v['data']['available'] key=avai_k item=avai_v}
													<tr>
														<td>{$avai_v['room_num']|escape:'htmlall':'UTF-8'}</td>
														<td>{dateFormat date=date('Y-m-d', strtotime($date_from)) full=0} - {dateFormat date=date('Y-m-d', strtotime($date_to)) full=0}</td>
														<td>{$avai_v['room_comment']|escape:'htmlall':'UTF-8'}</td>
														<td>
															{foreach $allotment_types as $allotment_type}
																<label class="control-label">
																	<input type="radio" value="{$allotment_type.id_allotment|intval}" name="bk_type_{$avai_v['id_room']|escape:'htmlall':'UTF-8'}" data-id-room="{$avai_v['id_room']|escape:'htmlall':'UTF-8'}" class="avai_bk_type" {if $allotment_type@first}checked="checked"{/if}>
																	<span>{$allotment_type.name|escape:'htmlall':'UTF-8'}</span>
																</label>
															{/foreach}
															<input type="text" id="comment_{$avai_v['id_room']|escape:'htmlall':'UTF-8'}" class="form-control avai_comment" placeholder="{l s='Allotment message' mod='hotelreservationsystem'}">
														</td>
														<td>
															<button type="button" data-id-cart="" data-id-cart-book-data="" data-id-product="{$avai_v['id_product']|escape:'htmlall':'UTF-8'}" data-id-room="{$avai_v['id_room']|escape:'htmlall':'UTF-8'}" data-id-hotel="{$avai_v['id_hotel']}" data-date-from="{$date_from|escape:'htmlall':'UTF-8'|date_format:'%Y-%m-%d'}" data-date-to ="{$date_to|escape:'htmlall':'UTF-8'|date_format:'%Y-%m-%d'}" class="btn btn-primary avai_add_cart">{l s='Add To Cart' mod='hotelreservationsystem'}</button>
														</td>
													</tr>
												{/foreach}
											</tbody>
										</table>
									</div>
								</div>
								<div id="part_room_data_{$book_k|escape:'htmlall':'UTF-8'}" class="tab-pane">
									<div class="table-responsive">
										<table class="table">
											<thead>
												<tr>
													<th><span class="title_box">{l s='Room No.' mod='hotelreservationsystem'}</span></th>
													<th class="text-center"><span class="title_box">{l s='Duration' mod='hotelreservationsystem'}</span></th>
													<th class="text-left"><span class="title_box">{l s='Allotment Type' mod='hotelreservationsystem'}</span></th>
													<th class="text-center"><span class="title_box">{l s='Action' mod='hotelreservationsystem'}</span></th>
												</tr>
											</thead>
											<tbody>
												{foreach from=$book_v['data']['partially_available'] key=part_k item=part_v}
													<tr>
														<td>{$part_v['room_num']|escape:'htmlall':'UTF-8'}</td>
														<td colspan="3">
															<table class="table">
																{foreach from=$part_v['avai_dates'] key=sub_part_k item=sub_part_v}
																	<tr>
																		<td class="text-center">
																		<p>{dateFormat date=date('Y-m-d', strtotime($sub_part_v['date_from'])) full=0} - {dateFormat date=date('Y-m-d', strtotime($sub_part_v['date_to'])) full=0}</p>
																		</td>
																		<td class="text-left">
																			{foreach $allotment_types as $allotment_type}
																				<label class="control-label">
																					<input type="radio" value="{$allotment_type.id_allotment|intval}" class="par_bk_type" name="bk_type_{$part_v['id_room']|escape:'htmlall':'UTF-8'}_{$sub_part_k|escape:'htmlall':'UTF-8'}" data-id-room="{$part_v['id_room']|escape:'htmlall':'UTF-8'}" data-sub-key="{$sub_part_k|escape:'htmlall':'UTF-8'}" {if $allotment_type@first}checked="checked"{/if}>
																					<span>{$allotment_type.name|escape:'htmlall':'UTF-8'}</span>
																				</label>
																			{/foreach}
																			<input type="text" id="comment_{$part_v['id_room']|escape:'htmlall':'UTF-8'}_{$sub_part_k|escape:'htmlall':'UTF-8'}" class="form-control par_comment" placeholder="{l s='Allotment message' mod='hotelreservationsystem'}">
																		</td>
																		<td class="text-center">
																			<button type="button" data-id-cart="" data-id-cart-book-data="" data-id-product="{$part_v['id_product']|escape:'htmlall':'UTF-8'}" data-id-room="{$part_v['id_room']|escape:'htmlall':'UTF-8'}" data-id-hotel="{$part_v['id_hotel']|escape:'htmlall':'UTF-8'}" data-date-from="{$sub_part_v['date_from']|escape:'htmlall':'UTF-8'|date_format:'%Y-%m-%d'}" data-date-to ="{$sub_part_v['date_to']|escape:'htmlall':'UTF-8'|date_format:'%Y-%m-%d'}" data-sub-key="{$sub_part_k|escape:'htmlall':'UTF-8'}" class="btn btn-primary par_add_cart">{l s='Add To Cart' mod='hotelreservationsystem'}</button>
																		</td>
																	</tr>
																{/foreach}
															</table>
														</td>
													</tr>
												{/foreach}
											</tbody>
										</table>
									</div>
								</div>
								<div id="book_room_data_{$book_k|escape:'htmlall':'UTF-8'}" class="tab-pane">
									<div class="table-responsive">
										<table class="table">
											<thead>
												<tr>
													<th><span class="title_box">{l s='Room No.' mod='hotelreservationsystem'}</span></th>
													<th class="text-center"><span class="title_box">{l s='Duration' mod='hotelreservationsystem'}</span></th>
													<th class="text-center"><span class="title_box">{l s='Message' mod='hotelreservationsystem'}</span></th>
													<th class="text-center"><span class="title_box">{l s='Allotment Type' mod='hotelreservationsystem'}</span></th>
													<th><span class="title_box">{l s='Reallocate' mod='hotelreservationsystem'}</span></th>
												</tr>
											</thead>
											<tbody>
												{foreach from=$book_v['data']['booked'] key=booked_k item=booked_v}
													<tr>
														<td>{$booked_v['room_num']|escape:'htmlall':'UTF-8'}</td>
														<td colspan="4">
															<table class="table">
																{foreach from=$booked_v['detail'] key=rm_dtl_k item=rm_dtl_v}
																	<tr>
																	<td class="col-xs-3">{dateFormat date=date('Y-m-d', strtotime($rm_dtl_v['date_from'])) full=0} - {dateFormat date=date('Y-m-d', strtotime($rm_dtl_v['date_to'])) full=0}</td>
																		<td class="col-xs-3">{$rm_dtl_v['comment']|escape:'htmlall':'UTF-8'}</td>
																		<td class="col-xs-3">
																			{if $rm_dtl_v['booking_type'] == HotelBookingDetail::ALLOTMENT_AUTO}{l s='Auto Allotment' mod='hotelreservationsystem'}{elseif $rm_dtl_v['booking_type'] == HotelBookingDetail::ALLOTMENT_MANUAL}{l s='Manual Allotment' mod='hotelreservationsystem'}{/if}
																		</td>
																		<td>
																			{if $rm_dtl_v['booking_type'] == HotelBookingDetail::ALLOTMENT_AUTO}
																				<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#mySwappigModal" data-room_num={$booked_v['room_num']|escape:'htmlall':'UTF-8'} data-date_from={$rm_dtl_v['date_from']|escape:'htmlall':'UTF-8'} data-date_to={$rm_dtl_v['date_to']|escape:'htmlall':'UTF-8'} data-id_room={$booked_v['id_room']|escape:'htmlall':'UTF-8'} data-cust_name="{$rm_dtl_v['alloted_cust_name']|escape:'htmlall':'UTF-8'}" data-cust_email="{$rm_dtl_v['alloted_cust_email']|escape:'htmlall':'UTF-8'}" data-avail_rm_realloc={$rm_dtl_v['avail_rooms_to_realloc']|@json_encode} data-avail_rm_swap={$rm_dtl_v['avail_rooms_to_swap']|@json_encode}>
																					{l s='Reallocate Room' mod='hotelreservationsystem'}
																				</button>
																			{else}
																				--
																			{/if}
																		</td>
																	</tr>
																{/foreach}
															</table>
														</td>
													</tr>
												{/foreach}
											</tbody>
										</table>
									</div>
								</div>
								<div id="unavail_room_data_{$book_k|escape:'htmlall':'UTF-8'}" class="tab-pane">
									<div class="table-responsive">
										<table class="table">
											<thead>
												<tr>
													<th><span class="title_box">{l s='Room No.' mod='hotelreservationsystem'}</span></th>
													<th><span class="title_box">{l s='Message' mod='hotelreservationsystem'}</span></th>
												</tr>
											</thead>
											<tbody>
												{foreach from=$book_v['data']['unavailable'] key=unavai_k item=unavai_v}
													<tr>
														<td>{$unavai_v['room_num']|escape:'htmlall':'UTF-8'}</td>
														<td>{$unavai_v['room_comment']|escape:'htmlall':'UTF-8'}</td>
													</tr>
												{/foreach}
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					{/foreach}
				</div>
			</div>
		{/if}
{if !isset($ajax_delete)}
		</div>
	</div>
</div>
<div class="modal fade" id="cartModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close margin-right-10" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel"><i class="icon-shopping-cart"></i>&nbsp {l s='Cart Options' mod='hotelreservationsystem'}</h4>
			</div>
			<div class="modal-body">
				<div class="row margin-lr-0">
					<div class="cart_table_container">
						<table class="table table-responsive addtocart-table">
							<thead class="cart-table-thead">
								<tr>
									<th class="text-center">{l s='Room No.' mod='hotelreservationsystem'}</th>
									<th class="text-center">{l s='Room Type' mod='hotelreservationsystem'}</th>
									<th class="text-center">{l s='Duration' mod='hotelreservationsystem'}</th>
									<th class="text-center">{l s='Amount (Tax excl.)' mod='hotelreservationsystem'}</th>
									<th></th>
								</tr>
							</thead>
							<tbody class="cart_tbody">
							{if isset($cart_bdata)}
								{foreach $cart_bdata as $cart_data}
									<tr>
										<td class="text-center">{$cart_data['room_num']|escape:'htmlall':'UTF-8'}</td>
										<td class="text-center">{$cart_data['room_type']|escape:'htmlall':'UTF-8'}</td>
										<td class="text-center">{dateFormat date=$cart_data['date_from'] full=0} - {dateFormat date=$cart_data['date_to'] full=0}</td>
										<td class="text-center">{convertPrice price=$cart_data['amt_with_qty']}</td>
										<td class="text-center"><button class="btn btn-default ajax_cart_delete_data" data-id-product="{$cart_data['id_product']|escape:'htmlall':'UTF-8'}" data-id-hotel="{$cart_data['id_hotel']|escape:'htmlall':'UTF-8'}" data-id-cart="{$cart_data['id_cart']|escape:'htmlall':'UTF-8'}" data-id-cart-book-data="{$cart_data['id_cart_book_data']|escape:'htmlall':'UTF-8'}" data-date-from="{$cart_data['date_from']|escape:'htmlall':'UTF-8'}" data-date-to="{$cart_data['date_to']|escape:'htmlall':'UTF-8'}"><i class='icon-trash'></i></button></td>
									</tr>
								{/foreach}
							{/if}
							</tbody>
						</table>
					</div>
					<div class="row cart_amt_div">
						<table class="table table-responsive">
							<tr>
								<th colspan="2">{l s='Total Amount (Tax incl.):' mod='hotelreservationsystem'}</th>
								<th colspan="2" class="text-right" id="cart_total_amt">
									{if isset($cart_tamount)}{convertPrice price=$cart_tamount}{else}0{/if}
								</th>
								<th colspan="1"></th>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="{$link->getAdminLink('AdminOrders')}&amp;addorder&amp;cart_id={$id_cart|escape:'htmlall':'UTF-8'|intval}&amp;guest_id={$id_guest|escape:'htmlall':'UTF-8'|intval}" class="btn btn-primary cart_booking_btn" {if empty($cart_bdata)}disabled="disabled"{/if}>
					{l s='Book Now' mod='hotelreservationsystem'}
				</a>
				<button type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' mod='hotelreservationsystem'}</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal for reallocation of rooms -->
<div class="modal fade" id="mySwappigModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
	<div class="modal-content">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#reallocate_room_tab" aria-controls="reallocate" role="tab" data-toggle="tab">{l s='Room Reallocation' mod='hotelreservationsystem'}</a></li>
			<li role="presentation"><a href="#swap_room_tab" aria-controls="swap" role="tab" data-toggle="tab">{l s='Swap Room' mod='hotelreservationsystem'}</a></li>
			</ul>
		<div class="tab-content panel active">
			<div role="tabpanel" class="tab-pane active" id="reallocate_room_tab">
				<form method="post" action="{$formAction|escape:'htmlall':'UTF-8'}">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="realloc_myModalLabel">{l s='Reallocate Rooms'}</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="curr_room_num" class="control-label">{l s='Current Room Number:' mod='hotelreservationsystem'}</label>
							<input type="text" class="form-control modal_curr_room_num" name="modal_curr_room_num" readonly="true">
							<input type="hidden" class="form-control modal_date_from" name="modal_date_from">
							<input type="hidden" class="form-control modal_date_to" name="modal_date_to">
							<input type="hidden" class="form-control modal_id_room" name="modal_id_room">
						</div>
						<div class="form-group">
							<label for="realloc_avail_rooms" class="control-label">{l s='Available Rooms To Reallocate:' mod='hotelreservationsystem'}</label>
							<div style="width: 195px;">
								<select class="form-control" name="realloc_avail_rooms" id="realloc_avail_rooms">
									<option value="0" selected="selected">{l s='Select Rooms' mod='hotelreservationsystem'}</option>
								</select>
								<p class="error_text" id="realloc_sel_rm_err_p"></p>
							</div>
						</div>
						<div class="form-group">
							<label style="text-decoration:underline;margin-top:5px;" for="message-text" class="col-sm-12 control-label"><i class="icon-info-circle"></i>&nbsp;{l s='Currently Alloted Customer Information:' mod='hotelreservationsystem'}</label>
							<dl class="well list-detail">
								<dt>{l s='Name' mod='hotelreservationsystem'}</dt>
								<dd class="cust_name"></dd><br>
								<dt>{l s='Email' mod='hotelreservationsystem'}</dt>
								<dd class="cust_email"></dd><br>
							</dl>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">{l s="Close" mod="hotelreservationsyatem"}</button>
						<input type="submit" id="realloc_allocated_rooms" name="realloc_allocated_rooms" class="btn btn-primary" value="Reallocate">
					</div>
				</form>
			</div>
			<div role="tabpanel" class="tab-pane" id="swap_room_tab">
				<form method="post" action="">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="swap_myModalLabel">{l s='Swap Rooms' mod='hotelreservationsystem'}</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="swap_curr_room_num" class="control-label">{l s='Current Room Number:' mod='hotelreservationsystem'}</label>
							<input type="text" class="form-control modal_curr_room_num" name="modal_curr_room_num" readonly="true">
							<input type="hidden" class="form-control modal_date_from" name="modal_date_from">
							<input type="hidden" class="form-control modal_date_to" name="modal_date_to">
							<input type="hidden" class="form-control modal_id_room" name="modal_id_room">
						</div>
						<div class="form-group">
							<label for="swap_avail_rooms" class="control-label">{l s='Available Rooms To Swap:' mod='hotelreservationsystem'}</label>
							<div style="width: 195px;">
								<select class="form-control" name="swap_avail_rooms" id="swap_avail_rooms">
									<option value="0" selected="selected">{l s='Select Rooms' mod='hotelreservationsystem'}</option>
								</select>
								<p class="error_text" id="swap_sel_rm_err_p"></p>
							</div>
						</div>
						<div class="form-group">
							<label style="text-decoration:underline;margin-top:5px;" for="message-text" class="col-sm-12 control-label"><i class="icon-info-circle"></i>&nbsp;{l s='Currently Alloted Customer Information:' mod='hotelreservationsystem'}</label>
							<dl class="well list-detail">
								<dt>{l s='Name' mod='hotelreservationsystem'}</dt>
								<dd class="cust_name"></dd><br>
								<dt>{l s='Email' mod='hotelreservationsystem'}</dt>
								<dd class="cust_email"></dd><br>
							</dl>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">{l s="Close" mod="hotelreservationsyatem"}</button>
						<input type="submit" id="swap_allocated_rooms" name="swap_allocated_rooms" class="btn btn-primary" value="Swap">
					</div>
				</form>
			</div>
		</div>
    </div>
  </div>
</div>

{strip}
	{addJsDef rooms_booking_url = $link->getAdminLink('AdminHotelRoomsBooking')}
	{addJsDefL name=opt_select_all}{l s='All Types' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=slt_another_htl}{l s='Select Another Hotel' js=1 mod='hotelreservationsystem'}{/addJsDefL}

	{addJsDefL name=from_date_cond}{l s='From date is required' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=to_date_cond}{l s='To date is required' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=hotel_name_cond}{l s='Hotel Name is required' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=num_rooms_cond}{l s='Number of Rooms is required' js=1 mod='hotelreservationsystem'}{/addJsDefL}

	{addJsDefL name=add_to_cart}{l s='Add To Cart' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=remove}{l s='Remove' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=noRoomTypeAvlTxt}{l s='No room type available.' js=1 mod='hotelreservationsystem'}{/addJsDefL}

	{addJsDef currency_sign = $currency->sign}
	{addJsDef currency_format = $currency->format}
	{addJsDef currency_blank = $currency->blank}

	{addJsDef booking_calendar_data = $booking_calendar_data|@json_encode}
	{addJsDef check_css_condition_var = $check_css_condition_var}
	{addJsDef check_calender_var = $check_calender_var}
	{addJsDefL name=no_rm_avail_txt}{l s='No rooms available.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=slct_rm_err}{l s='Please select a room first.' js=1 mod='hotelreservationsystem'}{/addJsDefL}

{/strip}

{/if}
<script type="text/javascript">

$(document).ready(function() {
	var allotmentTypes = {
		auto: {HotelBookingDetail::ALLOTMENT_AUTO},
		manual: {HotelBookingDetail::ALLOTMENT_MANUAL},
	};

	$('.avai_comment, .par_comment').hide();
	$('.avai_bk_type').on('change', function() {
		var id_room = $(this).attr('data-id-room');
		var booking_type = $(this).val();

		if (booking_type == allotmentTypes.auto) {
			$('#comment_'+id_room).hide().val('');
		} else if (booking_type == allotmentTypes.manual) {
			$('#comment_'+id_room).show();
		}
	});

	$('.par_bk_type').on('change', function() {
		var id_room = $(this).attr('data-id-room');
		var sub_key = $(this).attr('data-sub-key');
		var booking_type = $(this).val();

		if (booking_type == allotmentTypes.auto) {
			$('#comment_'+id_room+'_'+sub_key).hide().val('');
		} else if (booking_type == allotmentTypes.manual) {
			$('#comment_'+id_room+'_'+sub_key).show();
		}
	});
});

</script>
	