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

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
<div class="panel">
	{$kpi}
</div>
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<h3><i class="icon-user"></i> {l s='Customer information'}</h3>
			{if $customer->id}
				<a class="btn btn-default pull-right" href="mailto:{$customer->email}"><i class="icon-envelope"></i> {$customer->email}</a>
				<h2>
					{if $customer->id_gender == 1}
					<i class="icon-male"></i>
					{elseif $customer->id_gender == 2}
					<i class="icon-female"></i>
					{else}
					<i class="icon-question"></i>
					{/if}
					<a href="{$link->getAdminLink('AdminCustomers')|escape:'html':'UTF-8'}&amp;id_customer={$customer->id|intval}&amp;viewcustomer">{$customer->firstname} {$customer->lastname}</a></h2>
				<div class="form-horizontal">
					<div class="form-group">
						<label class="col-lg-3 control-label">{l s='Account registration date:'}</label>
						<div class="col-lg-3"><p class="form-control-static">{dateFormat date=$customer->date_add}</p></div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">{l s='Valid orders placed:'}</label>
						<div class="col-lg-3"><p class="form-control-static">{$customer_stats.nb_orders}</p></div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">{l s='Total spent since registration:'}</label>
						<div class="col-lg-3"><p class="form-control-static">{displayWtPriceWithCurrency price=$customer_stats.total_orders currency=$currency}</p></div>
					</div>
				</div>
			{else}
				<h2>{l s='Guest not registered.'}</h2>
			{/if}
		</div>
	</div>
	<div class="col-lg-6">
		<div class="panel">
			<h3><i class="icon-shopping-cart"></i> {l s='Order(s) Information'}</h3>
			{if is_array($cart_orders) && count($cart_orders)}
				{foreach from=$cart_orders item=$cart_order}
					<h2><a href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;id_order={$cart_order.id_order|intval}&amp;vieworder"> {l s='Order #%d' sprintf=$cart_order.id_order|string_format:"%06d"}</a></h2>
				{/foreach}
				{l s='Created on:'} {dateFormat date=$cart_order.date_add}
			{else}
				<h2>{l s='No order was created from this cart.'}</h2>
				{if $customer->id}
					<a class="btn btn-default" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;cart_id={$cart->id|intval}&amp;addorder"><i class="icon-shopping-cart"></i> {l s='Create an order from this cart.'}</a>
				{/if}
			{/if}
		</div>
	</div>
</div>
<div class="panel">
	<h3><i class="icon-archive"></i> {l s='Cart summary'}</h3>
	<div class="row">
		<table class="table" id="orderProducts">
			<thead>
				<tr>
					<th class="fixed-width-xs"><span class="title_box">{l s='Image'}</span></th>
					<th><span class="title_box">{l s='Room Type'}</span></th>
					<th><span class="title_box">{l s='Hotel'}</span></th>
					<th><span class="title_box">{l s='Duration'}</span></th>
					<th><span class="title_box">{l s='occupancy'}</span></th>
					<th><span class="title_box">{l s='Room price'}</span></th>
					<th><span class="title_box">{l s='Extra services'}</span></th>
					<th class="text-right"><span class="title_box">{l s='Total'}</span></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$cart_htl_data item='room'}
					<tr>
						<td><img src="{$room['image_link']}" class="img-responsive" /></td>
						<td>
							<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}&amp;id_product={$room['id_product']}&amp;updateproduct">
								<span class="productName">{$room['room_type']}</span>
							</a>
						</td>
						<td><a href="{$link->getAdminLink('AdminAddHotel')|escape:'html':'UTF-8'}&amp;id={$room['id_hotel']}&amp;updatehtl_branch_info">{$room['room_type_info'].hotel_name}</a></td>
						<td>{dateFormat date=$room['date_from']} - {dateFormat date=$room['date_to']}</td>
						<td>
							<span>
								{if $room['adults']}{$room['adults']}{/if} {if $room['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if {$room['children']}}, {$room['children']} {if $room['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if}
							</span>
						</td>
						<td>{displayWtPriceWithCurrency price=$room['feature_price_tax_excl'] currency=$currency}</td>
						<td>
							{if (isset($room['extra_demands']) && $room['extra_demands']) || (isset($room['additional_service']) && $room['additional_service'])}
								<a href="#" data-toggle="modal" data-target="#rooms_type_extra_demands_{$room['id']}" edit_orde_line="0">
									{displayWtPriceWithCurrency price=($room['demand_price'] + $room['additional_service_price'] + $room['additional_services_auto_add_price'])|escape:'html':'UTF-8' currency=$currency}
								</a>
							{else}
								{displayWtPriceWithCurrency price=0 currency=$currency}
							{/if}
						</td>
						<td class="text-right">
							{if (isset($room['extra_demands']) && $room['extra_demands']) || (isset($room['additional_service']) && $room['additional_service'])}
								{displayWtPriceWithCurrency price=($room['amt_with_qty'] + $room['additional_services_auto_add_price'] + $room['demand_price'] +  $room['additional_service_price'])|escape:'html':'UTF-8' currency=$currency}
							{else}
								{displayWtPriceWithCurrency price=$room['amt_with_qty']|escape:'html':'UTF-8' currency=$currency}
							{/if}
						</td>
					</tr>
					<div class="modal" tabindex="-1" role="dialog" id="rooms_type_extra_demands_{$room['id']}">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-body" id="rooms_extra_demands">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
									<ul class="nav nav-tabs" role="tablist">
										{if isset($room['selected_demands']) && $room['selected_demands']}
											<li role="presentation" class="active"><a href="#room_type_demands_desc_{$room['id']}" aria-controls="facilities" role="tab" data-toggle="tab">{l s='Facilities'}</a></li>
										{/if}
										{if isset($room['selected_services']) && $room['selected_services']}
											<li role="presentation" {if !isset($room['selected_demands']) || !$room['selected_demands']}class="active"{/if}><a href="#room_type_service_product_desc_{$room['id']}" aria-controls="services" role="tab" data-toggle="tab">{l s='Services'}</a></li>
										{/if}
									</ul>
									<div class="tab-content panel">
										{if isset($room['selected_demands']) && $room['selected_demands']}
											<div id="room_type_demands_desc_{$room['id']}" class="tab-pane active">
												<div id="room_type_demands_desc">
													{if isset($room['selected_demands']) && $room['selected_demands']}
														{assign var=roomCount value=1}
														{foreach $room['selected_demands'] as $demand}
															<div class="row room_demands_container">
																<div class="col-sm-12 room_demand_detail">
																	{if isset($room['selected_demands']) && $room['selected_demands']}
																		{foreach $room['extra_demands'] as $idGlobalDemand => $roomDemand}
																			{if $demand.id_global_demand == $idGlobalDemand}
																				<div class="row room_demand_block">
																					<div class="col-xs-6">
																						<div class="row">
																							<div class="col-xs-10 demand_adv_option_block">
																								<p>
																									{$roomDemand['name']|escape:'html':'UTF-8'}<br>
																									{if isset($roomDemand['adv_option']) && $roomDemand['adv_option']}
																										{$roomDemand['adv_option'][$demand['id_option']]['name']}
																									{/if}
																								</p>
																							</div>
																						</div>
																					</div>
																					<div class="col-xs-6">
																						<p><span class="pull-right extra_demand_option_price">{if isset($roomDemand['adv_option']) && $roomDemand['adv_option']}{convertPrice price = $roomDemand['adv_option'][$idGlobalDemand]['price']|escape:'html':'UTF-8'}{else}{convertPrice price = $roomDemand['price']|escape:'html':'UTF-8'}{/if}</span></p>
																					</div>
																				</div>
																			{/if}
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
										{if isset($room['selected_services']) && $room['selected_services']}
											<div id="room_type_service_product_desc_{$room['id']}" class="tab-pane{if !isset($room['selected_demands']) || !$room['selected_demands']} active{/if}">
												<div id="room_type_services_desc">
													{assign var=roomCount value=1}
													<div class="row room_demands_container">
														<div class="col-sm-12 room_demand_detail">
															{if isset($room['selected_services']) && $room['selected_services']}
																{foreach $room['selected_services'] as $service}
																	<div class="row room_demand_block">
																			<div class="col-xs-5">
																				<div class="row">
																					<div class="col-xs-10">
																						<p>{$service['name']|escape:'html':'UTF-8'}</p>
																						{if $service.allow_multiple_quantity}
																							<div class="qty_container">
																							{l s='Quantity:'} {$service.quantity}
																							</div>
																						{/if}
																					</div>
																				</div>
																			</div>
																			<div class="col-xs-3">
																				{if $service['auto_add_to_cart'] && $service['price_addition_type'] == Product::PRICE_ADDITION_TYPE_INDEPENDENT}
																					<span class="badge badge-info label">{l s='Convenience fee'}</span>
																				{/if}
																				{if $service['auto_add_to_cart'] && $service['price_addition_type'] == Product::PRICE_ADDITION_TYPE_WITH_ROOM}
																					<span class="badge badge-info label">{l s='Auto added'}</span>
																				{/if}
																			</div>
																			<div class="col-xs-4">
																				<span class="pull-right">{convertPrice price=$service.total_price}</span>
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
				{/foreach}
			</tbody>
			<tfoot>
				<tr>
					<td colspan="7">{l s='Total cost of room types:'}</td>
					<td class="text-right">{displayWtPriceWithCurrency price=$total_products currency=$currency}</td>
				</tr>
				{if $total_discounts != 0}
				<tr>
					<td colspan="7">{l s='Total value of vouchers:'}</td>
					<td class="text-right">{displayWtPriceWithCurrency price=$total_discounts currency=$currency}</td>
				</tr>
				{/if}
				{if $total_wrapping > 0}
				<tr>
					<td colspan="7">{l s='Total cost of gift wrapping:'}</td>
					<td class="text-right">{displayWtPriceWithCurrency price=$total_wrapping currency=$currency}</td>
				</tr>
				{/if}
				{if $cart->getOrderTotal(true, Cart::ONLY_SHIPPING) > 0}
				<tr>
					<td colspan="7">{l s='Total cost of shipping:'}</td>
					<td class="text-right">{displayWtPriceWithCurrency price=$total_shipping currency=$currency}</td>
				</tr>
				{/if}
				<tr>
					<td colspan="7" class=" success"><strong>{l s='Total:'}</strong></td>
					<td class="text-right success"><strong>{displayWtPriceWithCurrency price=$total_price currency=$currency}</strong></td>
				</tr>
			</tfoot>
		</table>
	</div>
	{if $discounts}
	<div class="clear">&nbsp;</div>
	<div class="row">
		<table class="table">
			<thead>
				<tr>
					<th class="fixed-width-xs"><img src="../img/admin/coupon.gif" alt="{l s='Discounts'}" /></th>
					<th>{l s='Discount name'}</th>
					<th class="text-right fixed-width-md">{l s='Value'}</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$discounts item='discount'}
				<tr>
					<td class="fixed-width-xs">{$discount.id_discount}</td>
					<td><a href="{$link->getAdminLink('AdminCartRules')|escape:'html':'UTF-8'}&amp;id_cart_rule={$discount.id_discount}&amp;updatecart_rule">{$discount.name}</a></td>
					<td class="text-right fixed-width-md">{if (float)$discount.value_real == 0 && (int)$discount.free_shipping == 1}{l s='Free shipping'}{else}- {displayWtPriceWithCurrency price=$discount.value_real currency=$currency}{/if}</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
	{/if}
	<div class="clear">&nbsp;</div>
	<div class="row alert alert-warning">
		{l s='For this particular customer group, prices are displayed as:'} <b>{if $tax_calculation_method == $smarty.const.PS_TAX_EXC}{l s='Tax excluded'}{else}{l s='Tax included'}{/if}</b>
	</div>
{/block}
</div>
