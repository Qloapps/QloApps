{**
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

{block name='cart_booking_services'}
	<section>
		{if isset($roomTypeServiceProducts) && $roomTypeServiceProducts}
			{block name='cart_booking_services_tabs'}
				<ul class="nav nav-tabs">
					<li class="active"><a href="#room_type_service_product_desc" data-toggle="tab">{l s='Services'}</a></li>
				</ul>
			{/block}
			{block name='cart_booking_services_tabs_content'}
				<div class="tab-content">
					{block name='cart_booking_services_tab_content'}
						{if isset($roomTypeServiceProducts) && $roomTypeServiceProducts}
							<div id="room_type_service_product_desc" class="tab-pane active">
								<div class="rooms_extra_services_head">
									<p class="rooms_extra_services_text">{l s='Add below services to the rooms for better hotel experience'}</p>
								</div>
								{assign var=roomCount value=1}
								{foreach $cartRooms as $key => $cartRoom}
									<div class="card accordion">
										<div class="row accordion-section">
											<div class="col-sm-12 additional_service_header">
												<a class="accordion-section-title {if $roomCount == 1}active{/if}" href="#accordion_service_{$key|escape:'html':'UTF-8'}">
													{l s='Room'} {$roomCount|escape:'html':'UTF-8'}&nbsp;
													<span>({if {$cartRoom['adults']} <= 9}0{$cartRoom['adults']}{else}{$cartRoom['adults']}{/if} {if $cartRoom['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}, {if {$cartRoom['children']} <= 9}0{$cartRoom['children']}{else}{$cartRoom['children']}{/if} {if $cartRoom['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if})</span>
												</a>
											</div>
											<div id="accordion_service_{$key|escape:'html':'UTF-8'}" class=" col-sm-12 room_service_detail accordion-section-content {if $roomCount == 1}open{/if}" {if $roomCount == 1}style="display: block;"{/if}>
												{if isset($roomTypeServiceProducts) && $roomTypeServiceProducts}
													{foreach $roomTypeServiceProducts as $product}
														{if isset($cartRoom['selected_service']) && $cartRoom['selected_service'] && ($product['id_product']|array_key_exists:$cartRoom['selected_service'])}
															{assign var='serviceSelected' value=true}
															{$product.price_tax_incl = $cartRoom['selected_service'][$product['id_product']]['unit_price_tax_incl']}
															{$product.price_tax_exc = $cartRoom['selected_service'][$product['id_product']]['unit_price_tax_excl']}
														{else}
															{assign var='serviceSelected' value=false}
														{/if}
														<div class="row room_service_block">
															<div class="col-xs-8">
																<div class="row">
																	<div class="col-xs-2">
																		{if $product.available_for_order}
																			<p class="checkbox">
																				<input data-id_cart_booking="{$cartRoom['id']}" value="{$product['id_product']|escape:'html':'UTF-8'}" type="checkbox" class="change_room_type_service_product" {if $serviceSelected}checked{/if} />
																			</p>
																		{/if}
																	</div>
																	<div class="col-xs-10">
																		<p>{$product['name']|escape:'html':'UTF-8'}</p>
																		{if $product.allow_multiple_quantity}
																			<div class="qty_container">
																				<input type="text" class="form-control qty" id="qty_{$product.id_product}" name="room_service_product_qty_{$product.id_product}" data-id-product="{$product.id_product}" data-max_quantity="{$product.max_quantity}" value="{if $serviceSelected}{$cartRoom['selected_service'][$product['id_product']]['quantity']}{else}1{/if}">
																				<input type="hidden" class="qty_hidden" id="qty_{$product.id_product}_hidden" value="{if $serviceSelected}{$cartRoom['selected_service'][$product['id_product']]['quantity']}{else}0{/if}">
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
																	<span class="pull-right">{if !$priceDisplay}{convertPrice price=$product.price_tax_incl}{else}{convertPrice price=$product.price_tax_exc}{/if}{if $product.price_calculation_method == Product::PRICE_CALCULATION_METHOD_PER_DAY}{l s='/Night'}{/if}</span>

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
					{/block}
				</div>
			{/block}
		{/if}
	</section>
{/block}
