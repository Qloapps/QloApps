{**
* 2010-2023 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2023 Webkul IN
* @license LICENSE.txt
*}

<section>
	{if (isset($selectedRoomDemands) && $selectedRoomDemands) || (isset($roomTypeServiceProducts) && $roomTypeServiceProducts)}
		<ul class="nav nav-tabs">
			{if isset($selectedRoomDemands) && $selectedRoomDemands}
				<li class="active"><a href="#room_type_demands_desc" data-toggle="tab">{l s='Facilities'}</a></li>
			{/if}
			{if isset($roomTypeServiceProducts) && $roomTypeServiceProducts}
				<li{if !isset($selectedRoomDemands) || !$selectedRoomDemands} class="active"{/if}><a href="#room_type_service_product_desc" data-toggle="tab">{l s='Services'}</a></li>
			{/if}
		</ul>
		<div class="tab-content">
			{if isset($selectedRoomDemands) && $selectedRoomDemands}
				<div id="room_type_demands_desc" class="tab-pane active">
					<div class="rooms_extra_demands_head">
						<p class="rooms_extra_demands_text">{l s='Add below facilities to the rooms for better hotel experience'}</p>
					</div>
					{assign var=roomCount value=1}
					{foreach $selectedRoomDemands as $key => $roomDemand}
						<div class="card accordion">
							<div class="row accordion-section">
								<div class="col-sm-12 demand_header">
									<a class="accordion-section-title {if $roomCount == 1}active{/if}" href="#accordion_demand_{$key|escape:'html':'UTF-8'}">
										{l s='Room'} {$roomCount|escape:'html':'UTF-8'}&nbsp;
										<span>({if {$roomDemand['adults']} <= 9}0{$roomDemand['adults']}{else}{$roomDemand['adults']}{/if} {if $roomDemand['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}, {if {$roomDemand['children']} <= 9}0{$roomDemand['children']}{else}{$roomDemand['children']}{/if} {if $roomDemand['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if})</span>
									</a>
								</div>
								<div id="accordion_demand_{$key|escape:'html':'UTF-8'}" class="room_demand_detail col-sm-12 accordion-section-content {if $roomCount == 1}open{/if}" {if $roomCount == 1}style="display: block;"{/if}>
									{if isset($roomTypeDemands) && $roomTypeDemands}
										{foreach $roomTypeDemands as $idGlobalDemand => $demand}
											<div class="row room_demand_block">
												<div class="col-xs-8">
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
													<div class="col-xs-4">
														<p><span class="pull-right extra_demand_option_price">{if isset($selected_adv_option) && isset($demand['adv_option'][$selected_adv_option]['price'])}{convertPrice price = $demand['adv_option'][$selected_adv_option]['price']|escape:'html':'UTF-8'}{else}{convertPrice price = $demand['price']|escape:'html':'UTF-8'}{/if}</span></p>
													</div>
												</div>
											{/foreach}
										{/if}
								</div>
							</div>
						</div>
						{assign var=roomCount value=$roomCount+1}
					{/foreach}
				</div>
			{/if}

			{if isset($roomTypeServiceProducts) && $roomTypeServiceProducts}
				<div id="room_type_service_product_desc" class="tab-pane{if !isset($selectedRoomDemands) || !$selectedRoomDemands} active{/if}">
					<div class="rooms_extra_demands_head">
						<p class="rooms_extra_demands_text">{l s='Add below services to the rooms for better hotel experience'}</p>
					</div>
					{assign var=roomCount value=1}
					{foreach $selectedRoomServiceProduct as $key => $cartRoom}
						<div class="card accordion">
							<div class="row accordion-section">
								<div class="col-sm-12 demand_header">
									<a class="accordion-section-title {if $roomCount == 1}active{/if}" href="#accordion_service_{$key|escape:'html':'UTF-8'}">
										{l s='Room'} {$roomCount|escape:'html':'UTF-8'}&nbsp;
										<span>({if {$cartRoom['adults']} <= 9}0{$cartRoom['adults']}{else}{$cartRoom['adults']}{/if} {if $cartRoom['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}, {if {$cartRoom['children']} <= 9}0{$cartRoom['children']}{else}{$cartRoom['children']}{/if} {if $cartRoom['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if})</span>
									</a>
								</div>
								<div id="accordion_service_{$key|escape:'html':'UTF-8'}" class=" col-sm-12 room_demand_detail accordion-section-content {if $roomCount == 1}open{/if}" {if $roomCount == 1}style="display: block;"{/if}>
									{if isset($roomTypeServiceProducts) && $roomTypeServiceProducts}
										{foreach $roomTypeServiceProducts as $product}
											<div class="row room_demand_block">
												<div class="col-xs-8">
													<div class="row">
														<div class="col-xs-2">
															{if $product.available_for_order}
																<p class="checkbox">
																	<input data-id_cart_booking="{$cartRoom['htl_cart_booking_id']}" value="{$product['id_product']|escape:'html':'UTF-8'}" type="checkbox" class="change_room_type_service_product" {if  isset($cartRoom['selected_products']) && $cartRoom['selected_products'] && ($product['id_product']|in_array:$cartRoom['selected_products'])}checked{/if} />
																</p>
															{/if}
														</div>
														<div class="col-xs-10">
															<p>{$product['name']|escape:'html':'UTF-8'}</p>
															{if $product.allow_multiple_quantity}
																<div class="qty_container">
																	<input type="text" class="form-control qty" id="qty_{$product.id_product}" name="service_product_qty_{$product.id_product}" data-id-product="{$product.id_product}" value="{if  isset($cartRoom['selected_products']) && $cartRoom['selected_products'] && ($product['id_product']|in_array:$cartRoom['selected_products'])}{$cartRoom['selected_products_info'][$product['id_product']]['quantity']}{else}1{/if}">
																	<div class="qty_controls">
																		<a href="#" class="qty_up"><span><i class="icon-plus"></i></span></a>
																		<a href="#" class="qty_down"><span><i class="icon-minus"></i></span></a>
																	</div>
																</div>
															{/if}
														</div>
													</div>
												</div>
												<div class="col-xs-4">
													{if ($product.show_price && !isset($restricted_country_mode)) || isset($groups)}
														<span class="pull-right">{if !$priceDisplay}{convertPrice price=$product.price_tax_incl}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>
													{/if}
												</div>
											</div>
										{/foreach}
									{/if}
								</div>
							</div>
						</div>
						{assign var=roomCount value=$roomCount+1}
					{/foreach}
				<div>
			{/if}
		</div>
	{/if}
</section>
