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

<div class="modal" tabindex="-1" role="dialog" id="rooms_type_extra_demands">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				<ul class="nav nav-tabs" role="tablist">
					{if isset($selectedRoomDemands) && $selectedRoomDemands}
						<li role="presentation" class="active"><a href="#room_type_demands_desc" aria-controls="facilities" role="tab" data-toggle="tab">{l s='Facilities'}</a></li>
					{/if}
					{if isset($roomTypeServiceProducts) && $roomTypeServiceProducts}
						<li role="presentation" {if !isset($selectedRoomDemands) || !$selectedRoomDemands}class="active"{/if}><a href="#room_type_service_product_desc" aria-controls="services" role="tab" data-toggle="tab">{l s='Services'}</a></li>
					{/if}
				</ul>
			</div>
			<div class="modal-body" id="rooms_extra_demands">
				<div class="tab-content">
					{if isset($selectedRoomDemands) && $selectedRoomDemands}
						<div id="room_type_demands_desc" class="tab-pane active">
							<div class="rooms_extra_demands_head">
								<p class="rooms_extra_demands_text">{l s='Add below facilities to the room for better hotel experience'}</p>
							</div>
							<div id="room_type_demands_desc">
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
																					<option optionPrice="{$option['price_tax_excl']|escape:'html':'UTF-8'}" value="{$idOption|escape:'html':'UTF-8'}" {if isset($roomDemand['extra_demands'][$demand_key])}selected{/if} key="{$demand_key}">{$option['name']}</option>
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
																<p class="pull-right">
																	<span class="extra_demand_option_price">
																		{if isset($selected_adv_option) && isset($demand['adv_option'][$selected_adv_option]['price_tax_excl'])}{convertPrice price = $demand['adv_option'][$selected_adv_option]['price_tax_excl']|escape:'html':'UTF-8'}{else if isset($demand['adv_option']) && $demand['adv_option']}{convertPrice price = $demand['adv_option'][$demand['adv_option']|@key]['price_tax_excl']}{else}{convertPrice price = $demand['price_tax_excl']|escape:'html':'UTF-8'}{/if}
																	</span>
																	{if $demand['price_calc_method'] == HotelRoomTypeGlobalDemand::WK_PRICE_CALC_METHOD_EACH_DAY}
																		{l s='/ night'}
																	{/if}
																</p>
															</div>
														</div>
													{/foreach}
												{/if}
											</div>
										</div>
										{assign var=roomCount value=$roomCount+1}
									{/foreach}
								{/if}
							</div>
						</div>
					{/if}
					{if isset($roomTypeServiceProducts) && $roomTypeServiceProducts}
						<div id="room_type_service_product_desc" class="tab-pane{if !isset($selectedRoomDemands) || !$selectedRoomDemands} active{/if}">
							<div class="rooms_extra_demands_head">
								<p class="rooms_extra_demands_text">{l s='Add below services to the rooms for better hotel experience'}</p>
							</div>
							<div id="room_type_services_desc">
								{assign var=roomCount value=1}
								<div class="row room_demands_container">
									<div class="col-sm-12 demand_header">
										<span>
											<i class="icon-bed"></i>
											{if isset($selectedRoomServiceProduct['room_num']) && $selectedRoomServiceProduct['room_num']}
												{l s='Room'} {$selectedRoomServiceProduct['room_num']|escape:'html':'UTF-8'}
											{else}
												{l s='Room'} {$roomCount|escape:'html':'UTF-8'}
											{/if}
										</span>
									</div>
									<div class="col-sm-12 room_demand_detail">
										{if isset($roomTypeServiceProducts) && $roomTypeServiceProducts}
											{foreach $roomTypeServiceProducts as $product}
												<div class="row room_demand_block">
														<div class="col-xs-5">
															<div class="row">
																<div class="col-xs-2">
																	<input data-id_cart_booking="{$selectedRoomServiceProduct['id']}" value="{$product['id_product']|escape:'html':'UTF-8'}" type="checkbox" class="change_room_type_service_product" {if  isset($selectedRoomServiceProduct['selected_service']) && $selectedRoomServiceProduct['selected_service'] && ($product['id_product']|array_key_exists:$selectedRoomServiceProduct['selected_service'])}checked{/if}/>
																</div>
																<div class="col-xs-10">
																	<p>{$product['name']|escape:'html':'UTF-8'}</p>
																	{if $product.allow_multiple_quantity}
																		<div class="qty_container">
																			<input type="number" class="form-control room_type_service_product_qty qty" id="qty_{$product.id_product}" name="service_product_qty_{$product.id_product}" data-id-product="{$product.id_product}" min="1" value="{if  isset($selectedRoomServiceProduct['selected_service']) && $selectedRoomServiceProduct['selected_service'] && ($product['id_product']|array_key_exists:$selectedRoomServiceProduct['selected_service'])}{$selectedRoomServiceProduct['selected_service'][$product['id_product']]['quantity']}{else}1{/if}">
																		</div>
																	{/if}
																</div>
															</div>
														</div>
														<div class="col-xs-3">
															{if $product['auto_add_to_cart'] && $product['price_addition_type'] == Product::PRICE_ADDITION_TYPE_INDEPENDENT}
																<span class="badge badge-info label">{l s='Convenience fee'}</span>
															{/if}
															{if $product['auto_add_to_cart'] && $product['price_addition_type'] == Product::PRICE_ADDITION_TYPE_WITH_ROOM}
																<span class="badge badge-info label">{l s='Auto added'}</span>
															{/if}
														</div>
														<div class="col-xs-4">
															{if ($product.show_price && !isset($restricted_country_mode)) || isset($groups)}
																<span class="pull-right">{convertPrice price=$product.price_tax_exc}</span>
															{/if}
														</div>
													</div>
											{/foreach}
										{/if}
									</div>
								</div>
							</div>
						</div>
					{/if}
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Close'}</button>
			</div>
		</div>
	</div>
</div>
