{if isset($selectedRoomDemands) && $selectedRoomDemands}
	{assign var=roomCount value=1}
	{foreach $selectedRoomDemands as $key => $roomDemand}
		<div class="card accordion">
			<div class="row accordion-section">
				<div class="col-sm-12 demand_header">
					<a class="accordion-section-title {if $roomCount == 1}active{/if}" href="#accordion_{$key|escape:'html':'UTF-8'}">
						{l s='Room'} {$roomCount|escape:'html':'UTF-8'}&nbsp;
						<span>({if {$roomDemand['adults']} <= 9}0{$roomDemand['adults']}{else}{$roomDemand['adults']}{/if} {if $roomDemand['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}, {if {$roomDemand['children']} <= 9}0{$roomDemand['children']}{else}{$roomDemand['children']}{/if} {if $roomDemand['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if})</span>
					</a>
				</div>
				<div id="accordion_{$key|escape:'html':'UTF-8'}" class="accordion-section-content {if $roomCount == 1}open{/if}" {if $roomCount == 1}style="display: block;"{/if}>
					<div class="col-sm-12 room_demand_detail">
						{if isset($roomTypeDemands) && $roomTypeDemands}
							{foreach $roomTypeDemands as $idGlobalDemand => $demand}
								<div class="row room_demand_block">
									<div class="col-xs-6">
										<div class="row">
											<div class="col-xs-2">
												<p class="checkbox">
													<input id_cart_booking="{$roomDemand['id']}" value="{$idGlobalDemand|escape:'html':'UTF-8'}" type="checkbox" class="id_room_type_demand" {if  isset($roomDemand['selected_global_demands']) && $roomDemand['selected_global_demands'] && ($idGlobalDemand|in_array:$roomDemand['selected_global_demands'])}checked{/if} />
												</p>
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
			</div>
		</div>
		{assign var=roomCount value=$roomCount+1}
	{/foreach}
{/if}