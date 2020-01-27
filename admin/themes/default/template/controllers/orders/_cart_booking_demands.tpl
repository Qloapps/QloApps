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

{if isset($selectedRoomDemands) && $selectedRoomDemands}
	{assign var=roomCount value=1}
	{foreach $selectedRoomDemands as $key => $roomDemand}
		<div class="row room_demands_container">
			<div class="col-sm-12 demand_header">
				<span>
					<i class="icon-bed"></i>
					{if isset($roomDemand['room_num']) && $roomDemand['room_num']}
						{l s='Room'} {$roomDemand['room_num']|escape:'html':'UTF-8'}
					{else}
						{l s='Room'} {$roomCount|escape:'html':'UTF-8'}
					{/if}
				</span>
			</div>
			<div class="col-sm-12 room_demand_detail">
				{if isset($roomTypeDemands) && $roomTypeDemands}
					{foreach $roomTypeDemands as $idGlobalDemand => $demand}
						<div class="row room_demand_block">
							<div class="col-xs-6">
								<div class="row">
									<div class="col-xs-2">
										<input id_cart_booking="{$roomDemand['id']}" value="{$idGlobalDemand|escape:'html':'UTF-8'}" type="checkbox" class="id_room_type_demand" {if  isset($roomDemand['selected_global_demands']) && $roomDemand['selected_global_demands'] && ($idGlobalDemand|in_array:$roomDemand['selected_global_demands'])}checked{/if} />
									</div>
									<div class="col-xs-10 demand_adv_option_block">
										<p>{$demand['name']|escape:'html':'UTF-8'}</p>
										{if isset($demand['adv_option']) && $demand['adv_option']}
											<select class="id_option">
												{foreach $demand['adv_option'] as $idOption => $option}
													{assign var=demand_key value="`$idGlobalDemand`-`$idOption`"}
													<option optionPrice="{$option['price']|escape:'html':'UTF-8'}" value="{$idOption|escape:'html':'UTF-8'}" {if isset($roomDemand['extra_demands'][$demand_key])}selected{/if} key="{$demand_key}">{$option['name']}</option>
													{if isset($roomDemand['extra_demands'][$demand_key])}
														{assign var=selected_adv_option value="$idOption"}
													{/if}
												{/foreach}
											</select>
										{else}
											<input type="hidden" class="id_option" value="0" />
										{/if}
									</div>
								</div>
							</div>
							<div class="col-xs-6">
								<p><span class="pull-right extra_demand_option_price">{if isset($selected_adv_option) && isset($demand['adv_option'][$selected_adv_option]['price'])}{convertPrice price = $demand['adv_option'][$selected_adv_option]['price']|escape:'html':'UTF-8'}{else}{convertPrice price = $demand['price']|escape:'html':'UTF-8'}{/if}</span></p>
							</div>
						</div>
					{/foreach}
				{/if}
			</div>
		</div>
		{assign var=roomCount value=$roomCount+1}
	{/foreach}
{/if}