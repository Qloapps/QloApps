{*
* 2010-2019 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="panel form-horizontal" id="customer_cart_details">
	<div class="panel-heading">
		<i class="icon-shopping-cart"></i>
		{l s='Cart Details'}
	</div>
	<div class="row">
		<div class="col-lg-12 table-responsive">
			<table class="table" id="customer_cart_details_table">
				<thead>
					<tr>
						<th><span class="title_box">{l s='Room No.'}</span></th>
						<th><span class="title_box">{l s='Room Image'}</th>
						<th><span class="title_box">{l s='Room Type'}</span></th>
						<th><span class="title_box">{l s='Duration'}</span></th>
						{if $occupancy_required_for_booking}
							<th><span class="fixed-width-lg title_box">{l s='Occupancy'}</span></th>
						{/if}
						<th><span class="title_box">{l s='Unit Price (tax excl)'}</span></th>
						<th><span class="title_box">{l s='Additinal Facilities (tax excl)'}</span></th>
						<th><span class="title_box">{l s='Total Rooms Price (tax excl)'}</span></th>
						<th><span class="title_box">{l s='Total Price (tax excl)'}</span></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				{if isset($cart_detail_data) && $cart_detail_data}
					{assign var=curr_id value=$cart->id_currency|intval}
					{foreach from=$cart_detail_data item=data}
						<tr  data-id-booking-data="{$data.id}" data-id-product="{$data.id_product}" data-id-room="{$data.id_room}" data-date-from="{$data.date_from}" data-date-to="{$data.date_to}" >
							<td>{$data.room_num|escape:'html':'UTF-8'}</td>
							<td><img src="{$data.image_link|escape:'html':'UTF-8'}" title="Room image" /></td>
							<td>
								<p>{$data.room_type|escape:'html':'UTF-8'}</p>
								{if isset($data.selected_demands) && $data.selected_demands}
									<ul class="extra-demand-list">
									{foreach $data.selected_demands as $selDemand}
										<li>
											{$selDemand.name|escape:'html':'UTF-8'}
										</li>
									{/foreach}
									</ul>
								{/if}
							</td>
							<td>{dateFormat date=$data.date_from} - {dateFormat date=$data.date_to}</td>
							{if $occupancy_required_for_booking}
								<td>
									<div class="dropdown">
										<button class="booking_guest_occupancy btn btn-default btn-left btn-block input-occupancy" type="button">
											<span>
												{if $data['adult']}{$data['adult']}{/if} {if $data['adult'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if {$data['children']}}, {$data['children']} {if $data['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if}
											</span>
										</button>
										<div class="dropdown-menu booking_occupancy_wrapper fixed-width-xxl">
											<div class="booking_occupancy_inner">
												<input type="hidden" class="max_adults" value="{if isset($data['room_type_info'])}{$data['room_type_info']['max_adults']|escape:'html':'UTF-8'}{/if}">
												<input type="hidden" class="max_children" value="{if isset($data['room_type_info'])}{$data['room_type_info']['max_children']|escape:'html':'UTF-8'}{/if}">
												<input type="hidden" class="max_guests" value="{if isset($data['room_type_info'])}{$data['room_type_info']['max_guests']|escape:'html':'UTF-8'}{/if}">
												<div class="occupancy_info_block selected" occ_block_index="0">
													<div class="occupancy_info_head col-sm-12"><span class="room_num_wrapper">{l s='Room - 1'}</span></div>
													<div class="row">
														<div class="col-xs-6 occupancy_count_block">
															<div class="col-sm-12">
																<label>{l s='Adults'}</label>
																<input type="number" class="form-control num_occupancy num_adults" name="occupancy[0][adult]" value="{$data['adult']}" min="1" max="{$data['room_type_info']['max_adults']|escape:'html':'UTF-8'}">
															</div>
														</div>
														<div class="col-xs-6 occupancy_count_block">
															<div class="col-sm-12">
																<label>{l s='Child'} <span class="label-desc-txt"></span></label>
																<input type="number" class="form-control num_occupancy num_children" name="occupancy[0][children]" value="{$data['children']}" min="0" max="{$data['room_type_info']['max_children']|escape:'html':'UTF-8'}">
																({l s='Below'}  {$max_child_age|escape:'htmlall':'UTF-8'} {l s='years'})
															</div>
														</div>
													</div>
													<div class="row children_age_info_block" {if !isset($data['child_ages']) || !$data['child_ages']}style="display:none"{/if}>
														<div class="col-sm-12">
															<label class="col-sm-12">{l s='All Children'}</label>
															<div class="col-sm-12">
																<div class="row children_ages">
																	{if isset($data['child_ages']) && $data['child_ages']}
																		{foreach $data['child_ages'] as $childAge}
																			<p class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
																				<select class="guest_child_age room_occupancies" name="occupancy[0][child_ages][]">
																					<option value="-1" {if $childAge == -1}selected{/if}>{l s='Select 1'}</option>
																					<option value="0" {if $childAge == 0}selected{/if}>{l s='Under 1'}</option>
																					{for $age=1 to ($max_child_age-1)}
																						<option value="{$age|escape:'htmlall':'UTF-8'}" {if $childAge == $age}selected{/if}>{$age|escape:'htmlall':'UTF-8'}</option>
																					{/for}
																				</select>
																			</p>
																		{/foreach}
																	{/if}
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</td>
							{/if}
							<td id="cart_detail_data_unit_price_{$data.id|escape:'html':'UTF-8'}">
								{if $data.feature_price_diff != 0}
									{assign var=shown_room_type_price value=$data.feature_price_tax_excl}
								{else}
									{assign var=shown_room_type_price value=$data.product_price_tax_excl}
								{/if}
								<input type="text" class="room_unit_price" value="{$shown_room_type_price|escape:'html':'UTF-8'}">
							</td>
							<td>
								{if isset($data.extra_demands) && $data.extra_demands}
									{displayPrice price=$data.demand_price|escape:'html':'UTF-8'}
								{else}
									{displayPrice price=0}
								{/if}
							</td>
							<td class="cart_line_total_rooms_price" id="cart_detail_data_price_{$data.id|escape:'html':'UTF-8'}">{displayPrice price=$data.amt_with_qty}</td>
							<td class="cart_line_total_price">
								{if isset($data.extra_demands) && $data.extra_demands}
									{displayPrice price=$data.amt_with_qty|escape:'html':'UTF-8'+$data.demand_price|escape:'html':'UTF-8'}
								{else}
									{displayPrice price=$data.amt_with_qty|escape:'html':'UTF-8'}
								{/if}
							</td>
							<td>
								<button class="delete_hotel_cart_data btn btn-danger" data-id_room={$data.id_room|escape:'html':'UTF-8'} data-id_product={$data.id_product|escape:'html':'UTF-8'} data-id = {$data.id|escape:'html':'UTF-8'} data-id_cart = {$data.id_cart|escape:'html':'UTF-8'} data-date_to = {$data.date_to|escape:'html':'UTF-8'} data-date_from = {$data.date_from|escape:'html':'UTF-8'}>
									<i class="icon-trash"></i>&nbsp;{l s='Delete'}
								</button>
								{if isset($data.extra_demands) && $data.extra_demands}
									<br />
									<a href="#" id_room={$data.id_room|escape:'html':'UTF-8'} date_from="{$data.date_from|escape:'html':'UTF-8'}" date_to="{$data.date_to|escape:'html':'UTF-8'}" id_product="{$data.id_product|escape:'html':'UTF-8'}" id_cart="{$data.id_cart|escape:'html':'UTF-8'}" class="open_rooms_extra_demands btn btn-success" title="{l s='Click here to add or remove the additinal facilities of this room type.'}">
										<i class="icon-plus"></i>&nbsp;{l s='Facilities'}
									</a>
								{/if}
							</td>
						</tr>
					{/foreach}
				{else}
					<tr>
						<td>{l s='No Room Found in the cart.'}</td>
					</tr>
				{/if}
				</tbody>
			</table>
		</div>
	</div>
</div>

{* Modal for extra demands *}
<div class="modal" tabindex="-1" role="dialog" id="rooms_type_extra_demands">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				<h3 class="modal-title">{l s='Additional Facilities'}</h3>
			</div>
			<div class="modal-body" id="rooms_extra_demands">
				<div class="rooms_extra_demands_head">
					<p class="rooms_extra_demands_text">{l s='Add below additional facilities to the room for better hotel experience'}</p>
				</div>
				<div id="room_type_demands_desc"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Close'}</button>
			</div>
		</div>
	</div>
</div>

{strip}
	{addJsDefL name=txtExtraDemandSucc}{l s='Updated Successfully' js=1}{/addJsDefL}
	{addJsDefL name=txtExtraDemandErr}{l s='Some error occurred while updating demands' js=1}{/addJsDefL}
{/strip}

{* Css for handling extra demands changes *}
<style type="text/css">
	#customer_cart_details .extra-demand-list {
		padding-left:15px;}
	#customer_cart_details .delete_hotel_cart_data {
		margin-bottom:2px !important;}
	#customer_cart_details .room_type_old_price {
		text-decoration: line-through;
		color:#979797;
		font-size:12px;}
	/*Extra demands CSS*/
	#rooms_extra_demands {
		font-size: 16px;}
	#rooms_extra_demands .room_demands_container {
		border: 1px solid #ddd;}
	#rooms_extra_demands .demand_header {
		padding: 10px;
		color: #333;
    	border-bottom: 1px solid #ddd;}
	#rooms_extra_demands .rooms_extra_demands_head {
		margin-bottom: 18px;}
	#rooms_extra_demands .room_demand_block {
		margin-bottom: 15px;
		color: #333;
		font-size: 14px;}
	#rooms_extra_demands .room_demand_detail {
		padding: 15px 15px 0px 15px;}
</style>
