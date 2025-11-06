{*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}

{if !isset($ajax) || !$ajax}
<div class="panel form-horizontal" id="customer_cart_details">
{/if}
	<div class="panel-heading">
		<i class="icon-shopping-cart"></i>
		{l s='Cart Details'}
	</div>
	<div class="row">
		<div class="col-lg-12 table-responsive">
			<table class="table" id="customer_cart_details_table">
				{if isset($cart_detail_data) && $cart_detail_data}
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
							<th><span class="title_box">{l s='Extra Services / Fees (tax excl)'}</span></th>
							{* <th><span class="title_box">{l s='Total Rooms Price (tax excl)'}</span></th> *}
							<th><span class="title_box">{l s='Total Price (tax excl)'}</span></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						{assign var=curr_id value=$cart->id_currency|intval}
						{foreach from=$cart_detail_data item=data}
							<tr  data-id-booking-data="{$data.id}" data-id-product="{$data.id_product}" data-id-room="{$data.id_room}" data-date-from="{$data.date_from}" data-date-to="{$data.date_to}" >
								<td>{$data.room_num|escape:'html':'UTF-8'} {hook h='displayRoomNumAfter' data=$data type='adminOrder'}</td>
								<td><img src="{$data.image_link|escape:'html':'UTF-8'}" title="Room image" /></td>
								<td>
									<p>{$data.room_type|escape:'html':'UTF-8'}</p>
								</td>
								{assign var="is_full_date" value=($show_full_date && ($data['date_from']|date_format:'%D' == $data['date_to']|date_format:'%D'))}
								<td>{dateFormat date=$data.date_from full=$is_full_date} - {dateFormat date=$data.date_to full=$is_full_date}</td>
								{if $occupancy_required_for_booking}
									<td>
										<div class="dropdown">
											<button class="booking_guest_occupancy btn btn-default btn-left btn-block input-occupancy" type="button">
												<span>
													{if $data['adults']}{$data['adults']}{/if} {if $data['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if {$data['children']}}, {$data['children']} {if $data['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if}
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
																	<input type="number" class="form-control num_occupancy num_adults" name="occupancy[0][adults]" value="{$data['adults']}" min="1">
																</div>
															</div>
															<div class="col-xs-6 occupancy_count_block">
																<div class="col-sm-12">
																	<label>{l s='Children'} <span class="label-desc-txt"></span></label>
																	<input type="number" class="form-control num_occupancy num_children" name="occupancy[0][children]" value="{$data['children']}" min="0">
																	({l s='Below'}  {$max_child_age|escape:'htmlall':'UTF-8'} {l s='years'})
																</div>
															</div>
														</div>
														<p style="display:none;"><span class="text-danger occupancy-input-errors"></span></p>
														<div class="row children_age_info_block" {if !isset($data['child_ages']) || !$data['child_ages']}style="display:none"{/if}>
															<div class="col-sm-12">
																<label class="col-sm-12">{l s='All Children'}</label>
																<div class="col-sm-12">
																	<div class="row children_ages">
																		{if isset($data['child_ages']) && $data['child_ages']}
																			{foreach $data['child_ages'] as $childAge}
																				<p class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
																					<select class="guest_child_age room_occupancies" name="occupancy[0][child_ages][]">
																						<option value="-1" {if $childAge == -1}selected{/if}>{l s='Select age'}</option>
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
									{assign var=shown_room_type_price value=$data.feature_price_tax_excl}
									<div class="input-group">
										<input type="text" class="room_unit_price" value="{Tools::ps_round($shown_room_type_price, $smarty.const._PS_PRICE_DISPLAY_PRECISION_)|escape:'html':'UTF-8'}">
										<span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
									</div>
								</td>
								<td>
                                    {displayPrice price=($data.demand_price + $data.additional_service_price + $data.additional_services_auto_add_price)|escape:'html':'UTF-8'}
								</td>
								{* <td class="cart_line_total_rooms_price" id="cart_detail_data_price_{$data.id|escape:'html':'UTF-8'}">
									{displayPrice price=$data.amt_with_qty}</td> *}
								<td class="cart_line_total_price">
									{if (isset($data.extra_demands) && $data.extra_demands) || (isset($data.additional_service) && $data.additional_service)}
										{displayPrice price=($data.amt_with_qty + $data.additional_services_auto_add_price + $data.demand_price +  $data.additional_service_price)|escape:'html':'UTF-8'}
									{else}
										{displayPrice price=$data.amt_with_qty|escape:'html':'UTF-8'}
									{/if}
								</td>
								<td>
									<button class="delete_hotel_cart_data btn btn-danger" data-id_room="{$data.id_room|escape:'html':'UTF-8'}" data-id_product="{$data.id_product|escape:'html':'UTF-8'}" data-id="{$data.id|escape:'html':'UTF-8'}" data-id_cart="{$data.id_cart|escape:'html':'UTF-8'}" data-date_to="{$data.date_to|escape:'html':'UTF-8'}" data-date_from="{$data.date_from|escape:'html':'UTF-8'}">
										<i class="icon-trash"></i>&nbsp;{l s='Delete'}
									</button>
                                    <br />
                                    <a href="#" id_hotel_cart_booking="{$data.id|escape:'html':'UTF-8'}" id_room="{$data.id_room|escape:'html':'UTF-8'}" date_from="{$data.date_from|escape:'html':'UTF-8'}" date_to="{$data.date_to|escape:'html':'UTF-8'}" id_product="{$data.id_product|escape:'html':'UTF-8'}" id_cart="{$data.id_cart|escape:'html':'UTF-8'}" class="open_rooms_extra_demands btn btn-success" title="{l s='Click here to add or remove the extra services of this room type.'}">
                                        <i class="icon-pencil"></i>&nbsp;{l s='Services'}
                                    </a>
								</td>
							</tr>
						{/foreach}
					</tbody>
				{/if}
			</table>
		</div>
	</div>
{if !isset($ajax) || !$ajax}
</div>

{* Modal for extra demands *}
{* <div class="modal" tabindex="-1" role="dialog" id="rooms_type_extra_demands">

</div> *}

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
	#rooms_extra_demands .rooms_extra_demands_head {
		margin-bottom: 18px;}
	#rooms_extra_demands .room_demand_block {
		margin-bottom: 15px;
		color: #333;}
    #room_type_service_product_desc #back_to_service_btn {
		display: none;}
    #add_new_room_services_block {
		display: none;}
</style>
{/if}
