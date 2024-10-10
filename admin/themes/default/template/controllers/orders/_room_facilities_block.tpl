{*
* 2010-2023 Webkul.
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
*  @copyright 2010-2023 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div id="room_type_demands_desc" class="tab-pane active">
	<input type="hidden" value="{$id_booking_detail}" id="id_htl_booking">
	{if isset($orderEdit) && $orderEdit}
		<p class="col-sm-12 facility_nav_btn">
			<button id="btn_new_room_demand" class="btn btn-success"><i class="icon-plus"></i> {l s='Add new facility'}</button>
			<button id="back_to_demands_btn" class="btn btn-default"><i class="icon-arrow-left"></i> {l s='Back'}</button>
		</p>

		{* Already selected room demands *}
		<div class="col-sm-12 room_ordered_demands table-responsive">
			<table class="table">
					<thead>
						<tr>
							<th>{l s='Name'}</th>
							<th>{l s='Unit Price'}</th>
							<th>{l s='Total Price'}</th>
							<th class="text-right">{l s='Action'}</th>
						</tr>
					</thead>
				<tbody>
					{if isset($extraDemands) && $extraDemands}
						{foreach $extraDemands as $roomDemand}
							{foreach $roomDemand['extra_demands'] as $demand}
								<tr>
									<td>{$demand['name']}</td>
									<td>
										{displayPrice price=$demand['unit_price_tax_excl'] currency=$orderCurrency}
										{if $demand['price_calc_method'] == HotelRoomTypeGlobalDemand::WK_PRICE_CALC_METHOD_EACH_DAY}
											{l s='/ night'}
										{/if}
									</td>
									<td>{displayPrice price=$demand['total_price_tax_excl'] currency=$orderCurrency}</td>
									<td><a class="btn btn-danger pull-right del-order-room-demand" href="#" id_booking_demand="{$demand['id_booking_demand']}"><i class="icon-trash"></i></a></td>
								</tr>
							{/foreach}
						{/foreach}
					{else}
						<tr>
							<td colspan="3">
								<i class="icon-warning"></i> {l s='No facilities added yet.'}
							</td>
						</tr>
					{/if}
				</tbody>
			</table>
		</div>

		{* Room demands available for the current editing room*}
		<div class="col-sm-12 room_demands_container">
			<div class="room_demand_detail">
				{if isset($roomTypeDemands) && $roomTypeDemands}
					<table class="table">
						<thead>
							<tr>
								<th></th>
								<th>{l s='Name'}</th>
								<th>{l s='Option'}</th>
								<th class="text-right">{l s='Price'}</th>
							</tr>
						</thead>
						<tbody>
							{foreach $roomTypeDemands as $idGlobalDemand => $demand}
								<tr class="room_demand_block">
									<td>
										<input id_cart_booking="{$roomDemand['id']}" value="{$idGlobalDemand|escape:'html':'UTF-8'}" type="checkbox" class="id_room_type_demand" {if  isset($roomDemand['selected_global_demands']) && $roomDemand['selected_global_demands'] && ($idGlobalDemand|in_array:$roomDemand['selected_global_demands'])}checked{/if} />
									</td>
									<td>
										{$demand['name']|escape:'html':'UTF-8'}
									</td>
									<td class="demand_adv_option_block">
										{if isset($demand['adv_option']) && $demand['adv_option']}
											<select class="id_option">
												{foreach $demand['adv_option'] as $idOption => $option}
													{assign var=demand_key value="`$idGlobalDemand`-`$idOption`"}
													<option optionPrice="{$option['price_tax_excl']|escape:'html':'UTF-8'}" value="{$idOption|escape:'html':'UTF-8'}" {if isset($roomDemand['extra_demands'][$demand_key])}selected{/if} key="{$demand_key}">{$option['name']}</option>
													{if isset($roomDemand['extra_demands'][$demand_key])}
														{assign var=selected_adv_option value="$idOption"}
													{/if}
												{/foreach}
											</select>
										{else}
											{l s='--'}
											<input type="hidden" class="id_option" value="0" />
										{/if}
									</td>
									<td class="text-right">
										<span class="extra_demand_option_price">
											{if isset($selected_adv_option) && isset($demand['adv_option'][$selected_adv_option]['price_tax_excl'])}{convertPrice price = $demand['adv_option'][$selected_adv_option]['price_tax_excl']|escape:'html':'UTF-8'}{else if isset($demand['adv_option']) && $demand['adv_option']}{convertPrice price = $demand['adv_option'][$demand['adv_option']|@key]['price_tax_excl']}{else}{convertPrice price = $demand['price_tax_excl']|escape:'html':'UTF-8'}{/if}
										</span>
										{if $demand['price_calc_method'] == HotelRoomTypeGlobalDemand::WK_PRICE_CALC_METHOD_EACH_DAY}
											{l s='/ night'}
										{/if}
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{else}
					<i class="icon-warning"></i> {l s='No facilities available for this room.'}
				{/if}
			</div>
			<button type="button" id="save_room_demands" class="btn btn-success pull-right"><i class="icon-save"></i> {l s='Save'}</button>
		</div>
	{elseif isset($extraDemands) && $extraDemands}
		{foreach $extraDemands as $roomDemand}
			<div class="row room_demand_detail">
				<table class="table">
					<thead>
						<tr>
							<th>{l s='Name'}</th>
							<th>{l s='Unit Price'}</th>
							<th class="text-right">{l s='Total Price'}</th>
						</tr>
					</thead>
					<tbody>
						{foreach $roomDemand['extra_demands'] as $demand}
							<tr class="room_demand_block">
								<td>{$demand['name']}</td>
								<td>
									{displayPrice price=$demand['unit_price_tax_excl'] currency=$orderCurrency}
									{if $demand['price_calc_method'] == HotelRoomTypeGlobalDemand::WK_PRICE_CALC_METHOD_EACH_DAY}
										{l s='/ night'}
									{/if}
								</td>
								<td class="text-right">{displayPrice price=$demand['total_price_tax_excl'] currency=$orderCurrency}</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		{/foreach}
	{else}
		{l s='No facilities selected!'}
	{/if}
</div>