{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<tr class="product-line-row" data-id_room="{$data.id_room}" data-id_product="{$data.id_product}" data-id_hotel="{$data.id_hotel}" data-date_from="{$data.date_from}" data-date_to="{$data.date_to}" data-product_price="{$data.unit_amt_tax_incl}" data-id_order_detail="{$data.id_order_detail}">
	<td>{$data.room_num}</td>
	<td><img src="{$data.image_link}" title="Room image" class="img-thumbnail"/></td>
	<td><p>{$data.room_type}</p></td>
	<td>
		<a class="order_detail_link" href="{$link->getAdminLink('AdminAddHotel')}&amp;id={$data['id_hotel']}&amp;updatehtl_branch_info" target="_blank">
			<span>{$data['hotel_name']}</span>
		</a>
	</td>
	<td>
		<span class="booking_duration_show">{dateFormat date=$data.date_from} - {dateFormat date=$data.date_to}</span>

		{if $can_edit}
			<div class="booking_duration_edit" style="display:none;">
				<div class="form-group">
					<div class="fixed-width-xl room_check_in_div">
						<div class="input-group">
							<div class="input-group-addon">{l s='Check In'}</div>
							<input type="text" class="form-control edit_product_date_from" name="edit_product[date_from]" value="{$data.date_from|date_format:"%d-%m-%Y"}"/>
							<div class="input-group-addon"><i class="icon-calendar"></i></div>
						</div>
					</div>
					<br/>
					<div class="fixed-width-xl room_check_out_div">
						<div class="input-group">
							<div class="input-group-addon">{l s='Check Out'}</div>
							<input type="text" class="form-control edit_product_date_to" name="edit_product[date_to]" value="{$data.date_to|date_format:"%d-%m-%Y"}"/>
							<div class="input-group-addon"><i class="icon-calendar"></i></div>
						</div>
					</div>
				</div>
			</div>
		{/if}
	</td>
	<td>
		{if $order->with_occupancy && $data['children']}
			<div class="dropdown booking_occupancy_show">
				<a class="order_detail_link" data-toggle="dropdown">
					<span>{if $data['adults']}{$data['adults']}{/if} {if $data['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if {$data['children']}}, {$data['children']} {if $data['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if}</span>
				</a>
				<div class="dropdown-menu well well-sm">
					<label>{l s='Children Ages'}</label>
					{if isset($data['child_ages']) && $data['child_ages']}
						{foreach $data['child_ages'] as $childAge}
							<p class="">
								{l s='Child %s : %s years' sprintf=[$childAge@iteration, $childAge]}
							</p>
						{/foreach}
					{/if}
				</div>
			</div>
		{else}
			<span class="booking_occupancy_show">{if $data['adults']}{$data['adults']}{/if} {if $data['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if {$data['children']}}, {$data['children']} {if $data['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if}</span>
		{/if}
		{if $order->with_occupancy}
			<div class="booking_occupancy_edit" style="display:none;">
				<div class="dropdown">
					<button class="booking_guest_occupancy btn btn-default btn-left btn-block input-occupancy" type="button">
						<span>
							{if $data['adults']}{$data['adults']}{/if} {if $data['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if {$data['children']}}, {$data['children']} {if $data['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if}
						</span>
					</button>
					<div class="dropdown-menu booking_occupancy_wrapper fixed-width-xxl well well-sm">
						<div class="booking_occupancy_inner">
						<input type="hidden" class="max_adults" value="{if isset($data['room_type_info'])}{$data['room_type_info']['max_adults']|escape:'html':'UTF-8'}{/if}">
						<input type="hidden" class="max_children" value="{if isset($data['room_type_info'])}{$data['room_type_info']['max_children']|escape:'html':'UTF-8'}{/if}">
						<input type="hidden" class="max_guests" value="{if isset($data['room_type_info'])}{$data['room_type_info']['max_guests']|escape:'html':'UTF-8'}{/if}">
							<div class="occupancy_info_block" occ_block_index="0">
								<div class="occupancy_info_head col-sm-12"><span class="room_num_wrapper">{l s='Room - 1'}</span></div>
								<div class="row">
									<div class="col-xs-6 occupancy_count_block">
										<div class="col-sm-12">
											<label>{l s='Adults'}</label>
											<input type="number" class="form-control num_occupancy num_adults" name="occupancy[0][adults]" value="{$data['adults']}" min="1"  max="{$data['room_type_info']['max_adults']|escape:'html':'UTF-8'}">
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
								<hr class="occupancy-info-separator">
							</div>
						</div>
					</div>
				</div>
			</div>
		{else}
			<span class="booking_occupancy_edit" style="display:none;">{if $data['adults']}{$data['adults']}{/if} {if $data['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if {$data['children']}}, {$data['children']} {if $data['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if}</span>
		{/if}
	</td>
	<td>
		<span class="room_unit_price_show">
			<p>
				<span class="room_type_current_price">
					{convertPriceWithCurrency price=$data['total_price_tax_excl'] currency=$currency->id}
				</span>
			</p>
			<p class="help-block">{l s='Per day price:'} {convertPriceWithCurrency price=$data['paid_unit_price_tax_excl'] currency=$currency->id}</p>
		</span>
		<div class="room_unit_price_edit" style="display: none;">
			<input class="form-control" type="text" class="room_unit_price" name="room_unit_price" value="{$data['paid_unit_price_tax_excl']}">
			<p class="help-block">{l s='Set per day price'}</p>
		</div>
	</td>
	<td>
		<span class="extra_service_show">
			{convertPriceWithCurrency price=($data['extra_demands_price_te'] + $data['additional_services_price_te'] + $data['convenience_fee_te'] + $data['additional_services_price_auto_add_te']) currency=$currency->id}
			{if ($data['extra_demands_price_te'] + $data['additional_services_price_te'] + $data['convenience_fee_te'] + $data['additional_services_price_auto_add_te']) > 0}
				<a class="order_detail_link open_room_extra_services" href="#" date_from="{$data['date_from']}" date_to="{$data['date_to']}" id_product="{$data['id_product']}" id_room="{$data['id_room']}" id_order="{$order->id}" id_htl_booking="{$data['id']}">
					<i class="icon icon-lg icon-info-circle"></i>
				</a>
			{/if}
		</span>
		<span class="extra_service_edit" style="display: none;">
			<a class="order_detail_link open_room_extra_services" href="#" date_from="{$data['date_from']}" date_to="{$data['date_to']}" id_product="{$data['id_product']}" id_room="{$data['id_room']}" id_order="{$order->id}" id_htl_booking="{$data['id']}">
				{convertPriceWithCurrency price=($data['extra_demands_price_te'] + $data['additional_services_price_te'] + $data['convenience_fee_te'] + $data['additional_services_price_auto_add_te']) currency=$currency->id}
			</a>
		</span>
	</td>
	<td>
		<span class="product_price_show">{convertPriceWithCurrency price=($data['total_room_tax']) currency=$currency->id}</span>
	</td>
	<td>
		<span class="product_price_show">{convertPriceWithCurrency price=($data['total_room_price_ti']) currency=$currency->id}</span>
		{if $can_edit}
		<div class="product_price_edit" style="display:none;">
			<div class="form-group">
				<div class="fixed-width-xl">
					<div class="input-group">
						{if $currency->format % 2}<div class="input-group-addon">{$currency->sign} {l s='tax excl.'}</div>{/if}
						<input type="text" name="product_price_tax_excl" class="edit_product_price_tax_excl edit_product_price" value="{Tools::ps_round($data.original_unit_price_tax_excl, 2)}"/>
						{if !($currency->format % 2)}<div class="input-group-addon">{$currency->sign} {l s='tax excl.'}</div>{/if}
					</div>
				</div>
				<br/>
				<div class="fixed-width-xl">
					<div class="input-group">
						{if $currency->format % 2}<div class="input-group-addon">{$currency->sign} {l s='tax incl.'}</div>{/if}
						<input type="text" name="product_price_tax_incl" class="edit_product_price_tax_incl edit_product_price" value="{Tools::ps_round($data.original_unit_price_tax_incl, 2)}"/>
						{if !($currency->format % 2)}<div class="input-group-addon">{$currency->sign} {l s='tax incl.'}</div>{/if}
					</div>
				</div>
			</div>
		</div>
		{/if}
	</td>
	{if (isset($refundReqBookings) && $refundReqBookings) || (isset($isCancelledRoom) && $isCancelledRoom)}
		<td>
			{if $data.is_cancelled}
				<span class="badge badge-danger">{l s='Cancelled'}</span>
			{elseif isset($data.refund_info) && $data.refund_info}
				<span class="badge" style="background-color:{$data.refund_info.color|escape:'html':'UTF-8'}">{$data.refund_info.name|escape:'html':'UTF-8'}</span>
			{else}
				<span>--</span>
			{/if}
		</td>
		<td>
			{if isset($data.refund_info) && $data.refund_info}
				{convertPriceWithCurrency price=$data.refund_info.refunded_amount currency=$currency->id}
			{/if}
		</td>
	{/if}
	{if ($can_edit && !$order->hasBeenDelivered())}
		<td class="product_invoice" style="display: none;">
		{if sizeof($invoices_collection)}
		<select name="product_invoice" class="edit_product_invoice">
			{foreach from=$invoices_collection item=invoice}
			<option value="{$invoice->id}">
				#{Configuration::get('PS_INVOICE_PREFIX', $current_id_lang, null, $order->id_shop)}{'%06d'|sprintf:$invoice->number}
			</option>
			{/foreach}
		</select>
		{else}
		&nbsp;
		{/if}
		</td>
		<td class="product_action">
			<div class="actions-row">
				<a href="#" class="order_detail_link edit_room_change_link" data-product_line_data="{$data|json_encode|escape}">
					<i class="icon-pencil"></i>
					{l s='Edit'}
				</a>
				<div class="dropdown pull-right">
					<a type="button" class="dropdown-toggle order_detail_link" data-toggle="dropdown" style="padding: 0 8px; margin-left: -15px;">
						<i class="icon-ellipsis-v"></i>
					</a>
					<ul class="dropdown-menu" role="menu">
						<li>
							<a class="room_reallocate_swap" href="#" data-id_order="{$order->id}" data-room_num='{$data.room_num}' data-date_from='{$data.date_from}' data-date_to='{$data.date_to}' data-id_room='{$data.id_room}' data-cust_name='{$data.alloted_cust_name}' data-cust_email='{$data.alloted_cust_email}' data-avail_rm_swap='{$data.avail_rooms_to_swap|@json_encode}' data-avail_rm_realloc='{$data.avail_rooms_to_realloc|@json_encode}'>
								<i class="icon-refresh"></i>
								{l s='Reallocate Room'}
							</a>
						</li>
						<li>
							<a href="#" class="delete_room_line">
								<i class="icon-trash"></i>
								{l s='Delete'}
							</a>
						</li>
					</ul>
				</div>
			</div>
		</td>
	{/if}
</tr>