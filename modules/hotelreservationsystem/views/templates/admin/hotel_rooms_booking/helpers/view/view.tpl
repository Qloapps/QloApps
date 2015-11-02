{if !isset($ajax_delete)}
<div class="panel col-sm-12">
	<h3 class="tab">
		<i class="icon-info"></i> {l s='Booking Information' mod='hotelreservationsystem'}
		<button type="button" class="btn btn-primary pull-right margin-right-10" id="cart_btn" data-toggle="modal" data-target="#cartModal"><i class="icon-shopping-cart"></i> {l s='Cart' mod='hotelreservationsystem'} <span class="badge" id="cart_record">{$rms_in_cart}</span></button>
	</h3>
	<div class="panel-body padding-0">
		<div class="row">
			<div class="col-sm-4">
				<div class="row box-border margin-right-10">
					<form method="post" action="">
						<div class="row">	
							<div class="form-group col-sm-12">
								<label for="from_date" class="control-label col-sm-4 required">
									<span title="" data-toggle="tooltip" class="label-tooltip">{l s='From' mod='hotelreservationsystem'}</span>
								</label>
								<div class="col-sm-8">
									<input type="text" name="from_date" class="form-control" id="from_date" {if isset($date_from)}value="{$date_from}"{/if}>
									<input type="hidden" name="search_date_from" id="search_date_from" {if isset($date_from)}value="{$date_from}"{/if}>
								</div>
							</div>
							<div class="form-group col-sm-12">
								<label for="to_date" class="control-label col-sm-4 required">
									<span title="" data-toggle="tooltip" class="label-tooltip">{l s='To' mod='hotelreservationsystem'}</span>
								</label>
								<div class="col-sm-8">
									<input type="text" name="to_date" class="form-control" id="to_date" {if isset($date_to)}value="{$date_to}"{/if}>
									<input type="hidden" name="search_date_to" id="search_date_to" {if isset($date_to)}value="{$date_to}"{/if}>
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
												<option value="{$name_val['id']}" {if isset($hotel_id) && ($name_val['id'] == $hotel_id)}selected{/if}>{$name_val['hotel_name']}</option>
											{/foreach}
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
											{foreach $all_room_type as $val_type}
												<option value="{$val_type['id_product']}" {if ($val_type['id_product'] == $room_type)}selected{/if}>{$val_type['room_type']}</option>
											{/foreach}
										{/if}
									</select>
									<input type="hidden" name="search_id_prod" id="search_id_prod" value="{$room_type}">
								</div>
							</div>

							<!-- <div class="form-group col-sm-6">
								<label for="adult" class="control-label col-sm-4 required">
									<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='No of Adults in one room' mod='hotelreservationsystem'}">{l s='Adults' mod='hotelreservationsystem'}</span>
								</label>
								<div class="col-sm-3">
									<input type="text" name="adult" class="form-control" value="{if isset($adult)}{$adult}{else}1{/if}" id="adult">
								</div>
							</div> -->
							<!-- <div class="form-group col-sm-6">
								<label for="children" class="control-label col-sm-4 required">
									<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='No of Children in one room' mod='hotelreservationsystem'}">{l s='children' mod='hotelreservationsystem'}</span>
								</label>
								<div class="col-sm-3">
									<input type="text" name="children" class="form-control" value="{if isset($children)}{$children}{else}0{/if}" id="children">
								</div>
							</div> -->
							
							<!-- <div class="form-group col-sm-6">
								<label for="num_rooms" class="control-label col-sm-4 required">
									<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='No of Rooms' mod='hotelreservationsystem'}">{l s='No of Rooms' mod='hotelreservationsystem'}</span>
								</label>
								<div class="col-sm-3">
									<input type="text" name="num_rooms" class="form-control" value="{if isset($num_rooms)}{$num_rooms}{else}1{/if}" id="num_rooms">
								</div>
							</div> -->
							<div class="col-sm-12">
								<button id="search_hotel_list" name="search_hotel_list" type="submit" class="btn btn-primary pull-right">
									<i class="icon-search"></i>&nbsp&nbsp{l s='Search' mod='hotelreservationsystem'}
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="col-sm-8">
				<div class="row margin-lr-0 box-border calender-main-div">
					<div class="hotel_date col-sm-7">
						<div class="row margin-leftrgt-0">
							<div class="col-sm-12 htl_date_header">
								<div class="col-sm-3">
									<p class="htl_date_disp">{$date_from|date_format:"%d"}</p>
									<span class="htl_month_disp">{$date_from|date_format:"%b"}</span>
								</div>
								<div class="col-sm-1">
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
										<p class="room_cat_data">{if isset($booking_data) && $booking_data}{$booking_data['stats']['total_rooms']}{/if}</p>
									</div>
								</div>
								<hr class="hr_style" />
							</div>
							<div class="col-sm-6">
								<div class="row">
									<div class="col-sm-12 htl_room_cat_data no_border">
										<p class="room_cat_header">{l s='Partially Available' mod='hotelreservationsystem'}</p>
										<p class="room_cat_data" id="num_part">{if isset($booking_data) && $booking_data}{$booking_data['stats']['num_part_avai']}{/if}</p>
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
										<p class="room_cat_data" id="num_avail">{if isset($booking_data) && $booking_data}{$booking_data['stats']['num_avail']}{/if}</p>
									</div>
								</div>
								<hr class="hr_style" />
							</div>
							<div class="col-sm-6">
								<div class="row">
									<div class="col-sm-12 htl_room_cat_data no_border">
										<p class="room_cat_header">{l s='Booked Rooms' mod='hotelreservationsystem'}</p>
										<p class="room_cat_data">{if isset($booking_data) && $booking_data}{$booking_data['stats']['num_booked']}{/if}</p>
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
										<p class="room_cat_data">{if isset($booking_data) && $booking_data}{$booking_data['stats']['num_unavail']}{/if}</p>
									</div>
								</div>
								<hr class="hr_style" />
							</div>
							<div class="col-sm-6">
								<div class="row">
									<div class="col-sm-12 htl_room_cat_data">
										<p class="room_cat_header">{l s='In-Cart Rooms' mod='hotelreservationsystem'}</p>
										<p class="room_cat_data" id="cart_stats">{if isset($booking_data) && $booking_data}{$booking_data['stats']['num_cart']}{/if}</p>
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

		<div class="row margin-div" id="htl_rooms_list">
{/if}
			<div class="col-sm-12">
				<ul class="nav nav-tabs">
					{if isset($booking_data) && $booking_data}
						{foreach from=$booking_data['rm_data'] key=book_k item=book_v}
							<li {if $book_k == 0}class="active"{/if} ><a href="#room_type_{$book_k}" data-toggle="tab">{$book_v['name']}</a></li>
						{/foreach}
					{/if}
				</ul>
				<div class="tab-content panel">
					{if isset($booking_data) && $booking_data}
						{foreach from=$booking_data['rm_data'] key=book_k item=book_v}
							<div id="room_type_{$book_k}" class="tab-pane {if $book_k == 0}active{/if}">
								<ul class="nav nav-tabs">
									<li class="active"><a href="#avail_room_data_{$book_k}" data-toggle="tab">{l s='Available Rooms' mod='hotelreservationsystem'}</a></li>
									<li><a href="#part_room_data_{$book_k}" data-toggle="tab">{l s='Partially Available' mod='hotelreservationsystem'}</a></li>
									<li><a href="#book_room_data_{$book_k}" data-toggle="tab">{l s='Booked Rooms' mod='hotelreservationsystem'}</a></li>
									<li><a href="#unavail_room_data_{$book_k}" data-toggle="tab">{l s='Unavailable Rooms' mod='hotelreservationsystem'}</a></li>
								</ul>
								<div class="tab-content panel">
									<div id="avail_room_data_{$book_k}" class="tab-pane active">
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
															<td>{$avai_v['room_num']}</td>
															<td>{$date_from|date_format:"%d-%b-%G"} {l s='To' mod='hotelreservationsystem'} {$date_to|date_format:"%d-%b-%G"}</td>
															<td>{$avai_v['room_comment']}</td>
															<td>
																<label class="control-label">
																	<input type="radio" value="1" name="bk_type_{$avai_v['id_room']}" data-id-room="{$avai_v['id_room']}" class="avai_bk_type" checked>
																	<span>{l s='Auto Allotment' mod='hotelreservationsystem'}</span>
																</label>
																<label class="control-label">
																	<input type="radio" value="2" name="bk_type_{$avai_v['id_room']}" data-id-room="{$avai_v['id_room']}" class="avai_bk_type">
																	<span>{l s='Manual Allotment' mod='hotelreservationsystem'}</span>
																</label>
																<input type="text" id="comment_{$avai_v['id_room']}" class="form-control avai_comment">
															</td>
															<td>
															{if isset($avai_v['in_current_cart']) && $avai_v['in_current_cart']}
																<button type="button" data-id-cart="{$avai_v['id_cart']}" data-id-cart-book-data="{$avai_v['cart_booking_data_id']}" data-id-product="{$avai_v['id_product']}" data-id-room="{$avai_v['id_room']}" data-id-hotel="{$avai_v['id_hotel']}" data-date-from="{$date_from}" data-date-to ="{$date_to}" class="btn btn-danger avai_delete_cart_data">{l s='Remove' mod='hotelreservationsystem'}</button>
															{else}
																	<button type="button" data-id-cart="" data-id-cart-book-data="" data-id-product="{$avai_v['id_product']}" data-id-room="{$avai_v['id_room']}" data-id-hotel="{$avai_v['id_hotel']}" data-date-from="{$date_from}" data-date-to ="{$date_to}" class="btn btn-primary avai_add_cart">{l s='Add To Cart' mod='hotelreservationsystem'}</button>
																{/if}
															</td>
														</tr>
													{/foreach}
												</tbody>
											</table>
										</div>
									</div>
									<div id="part_room_data_{$book_k}" class="tab-pane">
										<div class="table-responsive">
											<table class="table">
												<thead>
													<tr>
														<th><span class="title_box">{l s='Room No.' mod='hotelreservationsystem'}</span></th>
														<th><span class="title_box">{l s='Duration' mod='hotelreservationsystem'}</span></th>
														<th><span class="title_box">{l s='Allotment Type' mod='hotelreservationsystem'}</span></th>
														<th><span class="title_box">{l s='Action' mod='hotelreservationsystem'}</span></th>
													</tr>
												</thead>
												<tbody>
													{foreach from=$book_v['data']['partially_available'] key=part_k item=part_v}
														<tr>
															<td>{$part_v['room_num']}</td>
															<td>
																{foreach from=$part_v['avai_dates'] key=sub_part_k item=sub_part_v}
																	<div class="row partial_subrow">
																		<p>{$sub_part_v}</p>
																	</div>
																{/foreach}
															</td>
															<td>
																{foreach from=$part_v['avai_dates'] key=sub_part_k item=sub_part_v}
																	<div class="row partial_subrow">
																		<label class="control-label">
																			<input type="radio" value="1" class="par_bk_type" name="bk_type_{$part_v['id_room']}_{$sub_part_k}" data-id-room="{$part_v['id_room']}" data-sub-key="{$sub_part_k}" checked>
																			<span>{l s='Auto Allotment' mod='hotelreservationsystem'}</span>
																		</label>
																		<label class="control-label">
																			<input type="radio" value="2" class="par_bk_type" name="bk_type_{$part_v['id_room']}_{$sub_part_k}" data-id-room="{$part_v['id_room']}" data-sub-key="{$sub_part_k}">
																			<span>{l s='Manual Allotment' mod='hotelreservationsystem'}</span>
																		</label>
																		<input type="text" id="comment_{$part_v['id_room']}_{$sub_part_k}" class="form-control par_comment">
																	</div>
																{/foreach}
															</td>
															<td>
																{foreach from=$part_v['avai_dates'] key=sub_part_k item=sub_part_v}
																	<div class="row partial_subrow">
																	{assign var=date_arr value=" "|explode:$sub_part_v}
																	 {if $part_v['check_cart'][$sub_part_k]['in_current_cart']}
																	 	<button type="button" data-id-cart="{$part_v['check_cart'][$sub_part_k]['id_cart']}" data-id-cart-book-data="{$part_v['check_cart'][$sub_part_k]['cart_booking_data_id']}" data-id-product="{$part_v['id_product']}" data-id-room="{$part_v['id_room']}" data-id-hotel="{$part_v['id_hotel']}" data-date-from="{$date_arr[0]|date_format:'%Y-%m-%d'}" data-date-to ="{$date_arr[2]|date_format:'%Y-%m-%d'}" data-sub-key="{$sub_part_k}" class="btn btn-danger part_delete_cart_data">{l s='Remove' mod='hotelreservationsystem'}</button>
																	 {else}
																				<button type="button" data-id-cart="" data-id-cart-book-data="" data-id-product="{$part_v['id_product']}" data-id-room="{$part_v['id_room']}" data-id-hotel="{$part_v['id_hotel']}" data-date-from="{$date_arr[0]|date_format:'%Y-%m-%d'}" data-date-to ="{$date_arr[2]|date_format:'%Y-%m-%d'}" data-sub-key="{$sub_part_k}" class="btn btn-primary par_add_cart">{l s='Add To Cart' mod='hotelreservationsystem'}</button>
																			{/if}
																		</div>
																{/foreach}
															</td>
														</tr>
													{/foreach}
												</tbody>
											</table>
										</div>
									</div>
									<div id="book_room_data_{$book_k}" class="tab-pane">
										<div class="table-responsive">
											<table class="table">
												<thead>
													<tr>
														<th><span class="title_box">{l s='Room No.' mod='hotelreservationsystem'}</span></th>
														<th><span class="title_box">{l s='Duration' mod='hotelreservationsystem'}</span></th>
														<th><span class="title_box">{l s='Message' mod='hotelreservationsystem'}</span></th>
														<th><span class="title_box">{l s='Allotment Type' mod='hotelreservationsystem'}</span></th>
														<th><span class="title_box">{l s='Reallocate' mod='hotelreservationsystem'}</span></th>
													</tr>
												</thead>
												<tbody>
													{foreach from=$book_v['data']['booked'] key=booked_k item=booked_v}
														<tr>
															<td>{$booked_v['room_num']}</td>
															<td>{$booked_v['date_from']|date_format:"%d-%b-%G"} {l s='To' mod='hotelreservationsystem'} {$booked_v['date_to']|date_format:"%d-%b-%G"}</td>
															<td>{$booked_v['comment']}</td>
															<td>
																<label class="control-label">
																	<input type="radio" value="1" {if $booked_v['booking_type'] == 1}checked{/if} name="bt_type_{$booked_v['id_room']}">
																	<span>{l s='Auto Allotment' mod='hotelreservationsystem'}</span>
																</label>
																<label class="control-label">
																	<input type="radio" value="2" {if $booked_v['booking_type'] == 2}checked{/if} name="bt_type_{$booked_v['id_room']}">
																	<span>{l s='Manual Allotment' mod='hotelreservationsystem'}</span>
																</label>
															</td>
															{if $booked_v['booking_type'] == 1}
															<td>
																<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#mySwappigModal">
																	{l s='Reallocate Room' mod='hotelreservationsystem'}
																</button>

																<!-- Modal -->
																<div class="modal fade" id="mySwappigModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
																  <div class="modal-dialog" role="document">
																    <div class="modal-content">
																      <form method="post" action="">
																	      <div class="modal-header">
																	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
																	        <h4 class="modal-title" id="myModalLabel">{l s='Reallocate Rooms'}</h4>
																	      </div>
																      	<div class="modal-body">
																          <div class="form-group">
																            <label for="curr_room_num" class="control-label">{l s='Current Room Number:' mod='hotelreservationsystem'}</label>
																            <input type="text" class="form-control" id="curr_room_num" name="curr_room_num" value="{$booked_v['room_num']}" readonly="true">
																            <input type="hidden" class="form-control" name="date_from" value="{$booked_v['date_from']}">
																            <input type="hidden" class="form-control" name="date_to" value="{$booked_v['date_to']}">
																            <input type="hidden" class="form-control" name="id_room" value="{$booked_v['id_room']}">
																          </div>
																          <div class="form-group">
																            <label for="swapped_avail_rooms" class="control-label">{l s='Available Rooms To Swap:' mod='hotelreservationsystem'}</label>
																            <div style="width: 195px;">
																							<select class="form-control" name="swapped_avail_rooms" id="swapped_avail_rooms">
																								<option value="" selected="selected">{l s='Select Rooms' mod='hotelreservationsystem'}</option>
																								{if isset($booked_v['avail_rooms_to_swap']) && $booked_v['avail_rooms_to_swap']}
																									{foreach from=$booked_v['avail_rooms_to_swap'] key=room_k item=room_v}
																										<option value="{$room_v['id_room']}" >{$room_v['room_num']}</option>
																									{/foreach}
																								{else}
																									{l s='No rooms available' mod='hotelreservationsystem'}
																								{/if}
																							</select>
																						</div>
																          </div>
																          <div class="form-group">
																            <label for="message-text" class="col-sm-12 control-label">{l s='Currently Alloted Customer Information:' mod='hotelreservationsystem'}</label>
																           	<dl class="well list-detail">
																							<dt>{l s='Name'}</dt>
																							<dd>{$booked_v['alloted_cust_name']}</dd><br>
																							<dt>{l s='Email'}</dt>
																							<dd>{$booked_v['alloted_cust_email']}</dd><br>
																						</dl>
																		      </div>
																      </div>
																	      <div class="modal-footer">
																	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
																	        <input type="submit" name="swap_allocated_rooms" class="btn btn-primary" value="Save">
																	      </div>
																    </form>
																    </div>
																  </div>
																</div>
															</td>
															{/if}
														</tr>
													{/foreach}
												</tbody>
											</table>
										</div>
									</div>
									<div id="unavail_room_data_{$book_k}" class="tab-pane">
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
															<td>{$unavai_v['room_num']}</td>
															<td>{$unavai_v['room_comment']}</td>
														</tr>
													{/foreach}
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						{/foreach}
					{/if}	
				</div>
			</div>
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
									<th>{l s='Room No.' mod='hotelreservationsystem'}</th>
									<th>{l s='Room Type' mod='hotelreservationsystem'}</th>
									<th>{l s='Duration' mod='hotelreservationsystem'}</th>
									<th>{l s='Amount' mod='hotelreservationsystem'}</th>
									<th></th>
								</tr>
							</thead>
							<tbody class="cart_tbody">
							{if isset($cart_bdata)}
								{foreach $cart_bdata as $cart_data}
									<tr>
										<td>{$cart_data['room_num']}</td>
										<td>{$cart_data['room_type']}</td>
										<td>{$cart_data['date_from']|date_format:"%d-%b-%Y"} {l s='To' mod='hotelreservationsystem'} {$cart_data['date_to']|date_format:"%d-%b-%Y"}</td>
										<td>{convertPrice price=$cart_data['amt_with_qty']}</td>
										<td><button class="btn btn-default ajax_cart_delete_data" data-id-product="{$cart_data['id_product']}" data-id-hotel="{$cart_data['id_hotel']}" data-id-cart="{$cart_data['id_cart']}" data-id-cart-book-data="{$cart_data['id_cart_book_data']}" data-date-from="{$cart_data['date_from']}" data-date-to="{$cart_data['date_to']}"><i class='icon-trash'></i></button></td>
									</tr>
								{/foreach}
							{/if}
							</tbody>
						</table>
					</div>
					<div class="row cart_amt_div">
						<table class="table table-responsive">
							<tr>
								<th colspan="2">{l s='Total Amount' mod='hotelreservationsystem'}</th>
								<th colspan="2" class="text-right" id="cart_total_amt">
									{if isset($cart_tamount)}{convertPrice price=$cart_tamount}{else}0{/if}
								</th>
								<th colspan="1"></th>
							</tr>
						</table>
						<!-- <div class='col-sm-8'>{l s='Total Amount' mod='hotelreservationsystem'}</div>
						<div class='col-sm-4' id="cart_total_amt">{if isset($cart_tamount)}{convertPrice price=$cart_tamount}{else}0{/if}</div> -->
					</div>
					<!-- <div class="row row-margin-top">
						<a href="{$link->getAdminLink('AdminOrders')}&amp;addorder&amp;cart_id={$id_cart|intval}&amp;guest_id={$id_guest|intval}" class="btn btn-primary pull-right col-sm-3">
							{l s='Book Now' mod='hotelreservationsystem'}
						</a>
					</div> -->
				</div>
			</div>
			<div class="modal-footer">
				<a href="{$link->getAdminLink('AdminOrders')}&amp;addorder&amp;cart_id={$id_cart|intval}&amp;guest_id={$id_guest|intval}" class="btn btn-primary">
					{l s='Book Now' mod='hotelreservationsystem'}
				</a>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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

	{addJsDef currency_prefix = $currency->prefix}
	{addJsDef currency_suffix = $currency->suffix}
	
	{addJsDef currency_sign = $currency->sign}
	{addJsDef currency_format = $currency->format}
	{addJsDef currency_blank = $currency->blank}

	{addJsDef booking_calendar_data = $booking_calendar_data|@json_encode}
	{addJsDef check_css_condition_var = $check_css_condition_var}
	{addJsDef check_calender_var = $check_calender_var}

{/strip}

{/if}
<script type="text/javascript">

$(document).ready(function()
{
	$('.avai_comment, .par_comment').hide();

    $('.avai_bk_type').on('change', function()
    {
        var id_room = $(this).attr('data-id-room');
        var booking_type = $(this).val();

        if (booking_type == 1)
        {
            $('#comment_'+id_room).hide().val('');
        }
        else if (booking_type == 2)
            $('#comment_'+id_room).show();
    });

    $('.par_bk_type').on('change', function()
    {
        var id_room = $(this).attr('data-id-room');
        var sub_key = $(this).attr('data-sub-key');
        var booking_type = $(this).val();

        if (booking_type == 1)
        {
            $('#comment_'+id_room+'_'+sub_key).hide().val('');
        }
        else if (booking_type == 2)
        {
            $('#comment_'+id_room+'_'+sub_key).show();
        }
    });
});

</script>