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

{block name='order_confirmation'}
	{capture name=path}{l s='Order confirmation'}{/capture}
	{block name='order_confirmation_heading'}
		<h1 class="page-heading">{l s='Booking confirmation'} : <span class="bold">{$order->reference}</span></h1>
	{/block}

	{assign var='current_step' value='payment'}
	{block name='order_steps'}
		{include file="$tpl_dir./order-steps.tpl"}
	{/block}

	{block name='errors'}
		{include file="$tpl_dir./errors.tpl"}
	{/block}

	{block name='displayOrderConfirmation'}
		{$HOOK_ORDER_CONFIRMATION}
	{/block}

	<div class="order-confirmation-column">
        {if $HOOK_PAYMENT_RETURN}
            <div class="card">
                <div class="card-body">
                {block name='displayPaymentReturn'}
                    {$HOOK_PAYMENT_RETURN}
                {/block}
                </div>
            </div>
        {/if}
		{if isset($order->id) && $order->id}
			{if $is_guest}
				<p class="cart_navigation exclusive">
				<a class="button-exclusive btn btn-default" href="{$link->getPageLink('guest-tracking', true, NULL, "id_order={$reference_order|urlencode}&email={$email|urlencode}")|escape:'html':'UTF-8'}" title="{l s='Follow my order'}"><i class="icon-chevron-left"></i>{l s='Follow my order'}</a>
				</p>
			{else}
				{if isset($is_free_order) && $is_free_order}
					<p class="alert alert-success">{l s='Your'} {if $total_rooms_booked > 1}{l s='bookings have'}{else}{l s='booking has'}{/if} {l s='been created successfully!'}</p><br />
				{/if}
				{if $any_back_order}
					{if $shw_bo_msg}
						<br>
						<p class="back_o_msg"><strong><sup>*</sup>{l s='Some of your rooms are on back order. Please read the following message for rooms with status on backorder'}</strong></p>
						<p>
							-&nbsp;&nbsp;{$back_ord_msg}
						</p>
					{/if}
				{/if}
				{block name='order_details'}
					<div id="order-detail-content" class="">
						<div class="row">
							<div class="col-md-8 order-product-summary">
								{if isset($cart_htl_data)}
									<div class="card">
										<div class="card-header">{l s='Room Details'}</div>
										<div class="card-body">
											{foreach from=$cart_htl_data key=data_k item=data_v}
												{foreach from=$data_v['date_diff'] key=rm_k item=rm_v}
													<div class="product-detail" data-id-product="{$data_v.id_product}" data-date-diff="{$rm_k}">
														<div class="row">
															<div class="col-xs-3 col-sm-2">
																<a href="{$link->getProductLink($data_v['id_product'])}">
																	<img src="{$data_v['cover_img']}" class="img-responsive"/>
																</a>
															</div>
															<div class="col-xs-9 col-sm-10">
																<div class="row">
																	<div class="col-xs-12">
																		<p class="product-name">
																			<a href="{$link->getProductLink($data_v['id_product'])}">
																				{$data_v['name']}
																			</a>
																		</p>
																	</div>
																</div>

																<div class="row">
																	{assign var="is_full_date" value=($show_full_date && ($rm_v['data_form']|date_format:'%D' == $rm_v['data_to']|date_format:'%D'))}
																	<div class="col-xs-12 col-md-6">
																		<div class="row">
																			<dt class="col-xs-5">{l s='Check-in'}</dt>
																			<dd class="col-xs-7">{dateFormat date=$rm_v.data_form full=$is_full_date}</dd>
																		</div>
																	</div>
																	<div class="col-xs-12 col-md-6">
																		<div class="row">
																			<dt class="col-xs-5">{l s='Check-out'}</dt>
																			<dd class="col-xs-7">{dateFormat date=$rm_v.data_to full=$is_full_date}</dd>
																		</div>
																	</div>
																</div>

																<div class="row">
																	<div class="col-xs-12 col-md-6">
																		<div class="row">
																			<dt class="col-xs-5">{l s='Rooms'}</dt>
																			<dd class="col-xs-7">{$rm_v.num_rm|string_format:'%02d'}</dd>
																		</div>
																	</div>
																	<div class="col-xs-12 col-md-6">
																		<div class="row">
																			<dt class="col-xs-5">{l s='Guests'}</dt>
																			<dd class="col-xs-7">
																				{$rm_v.adults|string_format:'%02d'} {if $rm_v.adults > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if $rm_v.children}, {$rm_v.children|string_format:'%02d'} {if $rm_v.children > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if}
																			</dd>
																		</div>
																	</div>
																</div>
																<div class="row">
																	<div class="col-xs-12 col-md-6">
																		<div class="row">
																			<dt class="col-xs-5">{l s='Extra Services'}</dt>
																			<dd class="col-xs-7">
																				{if (isset($rm_v['extra_demands']) && $rm_v['extra_demands']) || isset($rm_v['additional_services']) && $rm_v['additional_services']}
																						<a data-date_from="{$rm_v['data_form']}" data-date_to="{$rm_v['data_to']}" data-id_product="{$data_v['id_product']}" data-id_order="{$data_v['id_order']}" data-action="{$link->getPageLink('order-detail')}" class="open_rooms_extra_services_panel" href="#rooms_type_extra_services_form">
																				{/if}
																				{if $group_use_tax}
																					{displayWtPriceWithCurrency price=($rm_v['extra_demands_price_ti'] + $rm_v['additional_services_price_ti'])  currency=$objOrderCurrency}
																				{else}
																					{displayWtPriceWithCurrency price=($rm_v['extra_demands_price_te'] + $rm_v['additional_services_price_te']) currency=$objOrderCurrency}
																				{/if}
																				{if (isset($rm_v['extra_demands']) && $rm_v['extra_demands']) || isset($rm_v['additional_services']) && $rm_v['additional_services']}
																					</a>
																				{/if}
																			</dd>
																		</div>
																	</div>
																	<div class="col-xs-12 col-md-6">
																		<div class="row">
																			<dt class="col-xs-5">{l s='Total Price'}</dt>
																			<dd class="col-xs-7">
																				{if $group_use_tax}
																					{displayWtPriceWithCurrency price=($rm_v['amount_tax_incl'] + $rm_v['extra_demands_price_ti'] + $rm_v['additional_services_price_ti'] + $rm_v['additional_services_price_auto_add_ti']) currency=$objOrderCurrency}
																				{else}
																					{displayWtPriceWithCurrency price=($rm_v['amount_tax_excl'] + $rm_v['extra_demands_price_te'] + $rm_v['additional_services_price_te'] +  $rm_v['additional_services_price_auto_add_te']) currency=$objOrderCurrency}
																				{/if}
																				{if (isset($rm_v['extra_demands']) && $rm_v['extra_demands']) || isset($rm_v['additional_services']) && $rm_v['additional_services']}
																					<span class="order-price-info">
																						<img src="{$img_dir}icon/icon-info.svg" />
																					</span>
																					<div class="price-info-container" style="display:none">
																						<div class="price-info-tooltip-cont">
																							<div class="list-row">
																								<div>
																									<p>{l s='Room cost'} : </p>
																								</div>
																								<div class="text-right">
																									<p>
																										{if $group_use_tax}
																											{displayWtPriceWithCurrency price=($rm_v['amount_tax_incl'] + $rm_v['additional_services_price_auto_add_ti']) currency=$objOrderCurrency}
																										{else}
																											{displayWtPriceWithCurrency price=($rm_v['amount_tax_excl'] +  $rm_v['additional_services_price_auto_add_te']) currency=$objOrderCurrency}
																										{/if}
																									</p>
																								</div>
																							</div>
																							<div class="list-row">
																								<div>
																									<p>{l s='Service cost'} : </p>
																								</div>
																								<div class="text-right">
																									<p>
																										{if $group_use_tax}
																											{displayWtPriceWithCurrency price=($rm_v['extra_demands_price_ti'] + $rm_v['additional_services_price_ti'])  currency=$objOrderCurrency}
																										{else}
																											{displayWtPriceWithCurrency price=($rm_v['extra_demands_price_te'] + $rm_v['additional_services_price_te']) currency=$objOrderCurrency}
																										{/if}
																									</p>
																								</div>
																							</div>
																						</div>
																					</div>
																				{/if}
																			</dd>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												{/foreach}
											{/foreach}
										</div>
									</div>
								{/if}
								{if isset($cart_standalone_service_products) || isset($cart_hotel_service_products)}
									<div class="card">
										<div class="card-header">{l s='Product Details'}</div>
										<div class="card-body">
											{if isset($cart_hotel_service_products)}
												{foreach from=$cart_hotel_service_products key=data_k item=product}
													<div class="product-detail" data-id-product="{$product.id_product}">
														<div class="row">
															{block name='order_hotel_product_image'}
																<div class="col-xs-3 col-sm-2">
																	<a href="{$link->getProductLink($product.id_product)|escape:'html':'UTF-8'}" title="{$product.product_name|escape:'html':'UTF-8'}" target="_blank">
																		<img class="img img-responsive img-room-type" src="{$product.cover_img|escape:'html':'UTF-8'}" />
																	</a>
																</div>
															{/block}
															{block name='order_hotel_product_detail'}
																<div class="col-xs-9 col-sm-10 info-wrap">
																	<div class="row">
																		<div class="col-xs-12">
																			<p class="product-name">
																				<a href="{$link->getProductLink($product.id_product)|escape:'html':'UTF-8'}" title="{$product.product_name|escape:'html':'UTF-8'}" target="_blank" class="product-name">
																					{$product.product_name|escape:'html':'UTF-8'}{if isset($product.option_name) && $product.option_name} : {$product.option_name|escape:'html':'UTF-8'}{/if}
																				</a>
																			</p>
                                                                            {if isset($product['hotel_location'])}
                                                                                <p class="hotel-location">
                                                                                    <i class="icon-map-marker"></i> &nbsp;{$product['hotel_location']}
                                                                                </p>
                                                                            {/if}
																		</div>
																		<div class="col-xs-12">
																			<div class="description-list">
																				<dl class="">
																					<div class="row">
																						{if $product.allow_multiple_quantity}
																							<div class="col-xs-12 col-md-6">
																								<div class="row">
																									<dt class="col-xs-5">{l s='Quantity'}</dt>
																									<dd class="col-xs-7">{$product.product_quantity}</dd>
																								</div>
																							</div>
																						{/if}
																						<div class="col-xs-12 col-md-6">
																							<div class="row">
																								<dt class="col-xs-5">{l s='Unit Price'}</dt>
																								<dd class="col-xs-7">
																									{if $group_use_tax}
																										{displayWtPriceWithCurrency price=$product.unit_price_tax_incl  currency=$currency}
																									{else}
																										{displayWtPriceWithCurrency price=$product.unit_price_tax_excl  currency=$currency}
																									{/if}
																								</dd>
																							</div>
																						</div>
																					</div>
																					<div class="row">
																						{if $product.allow_multiple_quantity}
																							<div class="col-xs-12 col-md-6">
																							</div>
																						{/if}
																						<div class="col-xs-12 col-md-6">
																							<div class="row">
																								<dt class="col-xs-5">{l s='Total Pricing'}</dt>
																								<dd class="col-xs-7">
																									{if $group_use_tax}
																										{displayWtPriceWithCurrency price=$product.total_price_tax_incl  currency=$currency}
																									{else}
																										{displayWtPriceWithCurrency price=$product.total_price_tax_excl  currency=$currency}
																									{/if}
																								</dd>
																							</div>
																						</div>
																					</div>
																				</dl>
																			</div>
																		</div>
																	</div>
																</div>
															{/block}
														</div>
													</div>
												{/foreach}
											{/if}
											{if isset($cart_standalone_service_products)}
												{foreach from=$cart_standalone_service_products key=data_k item=product}
													<div class="product-detail" data-id-product="{$product.id_product}">
														<div class="row">
															{block name='order_hotel_product_image'}
																<div class="col-xs-3 col-sm-2">
																	<a href="{$link->getProductLink($product.id_product)|escape:'html':'UTF-8'}" title="{$product.product_name|escape:'html':'UTF-8'}" target="_blank">
																		<img class="img img-responsive img-room-type" src="{$product.cover_img|escape:'html':'UTF-8'}" />
																	</a>
																</div>
															{/block}
															{block name='order_hotel_product_detail'}
																<div class="col-xs-9 col-sm-10 info-wrap">
																	<div class="row">
																		<div class="col-xs-12">
																			<p class="product-name">
																				<a href="{$link->getProductLink($product.id_product)|escape:'html':'UTF-8'}" title="{$product.product_name|escape:'html':'UTF-8'}" target="_blank" class="product-name">
																					{$product.product_name|escape:'html':'UTF-8'}{if isset($product.option_name) && $product.option_name} : {$product.option_name|escape:'html':'UTF-8'}{/if}
																				</a>
																			</p>
																		</div>
																		<div class="col-xs-12">
																			<div class="description-list">
																				<dl class="">
																					<div class="row">
																						{if $product.allow_multiple_quantity}
																							<div class="col-xs-12 col-md-6">
																								<div class="row">
																									<dt class="col-xs-5">{l s='Quantity'}</dt>
																									<dd class="col-xs-7">{$product.product_quantity}</dd>
																								</div>
																							</div>
																						{/if}
																						<div class="col-xs-12 col-md-6">
																							<div class="row">
																								<dt class="col-xs-5">{l s='Unit Price'}</dt>
																								<dd class="col-xs-7">
																									{if $group_use_tax}
																										{displayWtPriceWithCurrency price=$product.unit_price_tax_incl  currency=$currency}
																									{else}
																										{displayWtPriceWithCurrency price=$product.unit_price_tax_excl  currency=$currency}
																									{/if}
																								</dd>
																							</div>
																						</div>
																					</div>
																					<div class="row">
																						{if $product.allow_multiple_quantity}
																							<div class="col-xs-12 col-md-6">
																							</div>
																						{/if}
																						<div class="col-xs-12 col-md-6">
																							<div class="row">
																								<dt class="col-xs-5">{l s='Total Pricing'}</dt>
																								<dd class="col-xs-7">
																									{if $group_use_tax}
																										{displayWtPriceWithCurrency price=$product.total_price_tax_incl  currency=$currency}
																									{else}
																										{displayWtPriceWithCurrency price=$product.total_price_tax_excl  currency=$currency}
																									{/if}
																								</dd>
																							</div>
																						</div>
																					</div>
																				</dl>
																			</div>
																		</div>
																	</div>
																</div>
															{/block}
														</div>
													</div>
												{/foreach}
											{/if}
										</div>
									</div>
								{/if}
							</div>
							<div class="col-md-4">
								{block name='order_detail_payment_details'}
									<div class="card">
										<div class="card-header">
											{l s='Payment Details'}
										</div>
										<div class="card-body">
											<div class="row">
												<label class="col-xs-6 title">{l s='Payment Method'}</label>
												<div class="col-xs-6 text-right value payment-method">
													{* {if $invoice && $invoiceAllowed}
														<span class="icon-pdf"></span>
														<a target="_blank" href="{$link->getPageLink('pdf-invoice', true)}?id_order={$order->id|intval}{if $is_guest}&amp;secure_key={$order->secure_key|escape:'html':'UTF-8'}{/if}" title="{l s='Click here to download invoice.'}">
															<span>{$order->payment|escape:'html':'UTF-8'}</span>
														</a>
													{else} *}
														{$order->payment|escape:'html':'UTF-8'}
													{* {/if} *}
												</div>
											</div>
											<br>
											<div class="row">
												<label class="col-xs-6 title">{l s='Status'}</label>
												<div class="col-xs-6 text-right value status">
													{if isset($order_history[0]) && $order_history[0]}
														<span{if isset($order_history[0].color) && $order_history[0].color} style="background-color:{$order_history[0].color|escape:'html':'UTF-8'}30; border: 1px solid {$order_history[0].color|escape:'html':'UTF-8'};" {/if} class="label">
															{if $order_history[0].id_order_state|in_array:$overbooking_order_states}
																{l s='Order Not Confirmed'}
															{else}
																{$order_history[0].ostate_name|escape:'html':'UTF-8'}
															{/if}
														</span>
													{else}
														<span class="processing">{l s='Processing'}</span>
													{/if}
												</div>
											</div>

											{block name='displayOrderDetailPaymentDetailsRow'}
												{hook h='displayOrderDetailPaymentDetailsRow' id_order=$order->id}
											{/block}
										</div>
									</div>
								{/block}
								{block name='order_detail_payment_summary'}
									<div class="card">
										<div class="card-header">
											{l s='Payment Summary'}
										</div>
										<div class="card-body">
											<div class="prices-breakdown-table">
												<table class="table table-sm table-responsive table-summary">
													<tbody>
														{if isset($cart_htl_data)}
															<tr class="item">
																<td>
																	<strong>{l s='Total Rooms Cost'} {if $use_taxes && $display_tax_label == 1}{if $priceDisplay == 1}{l s='(tax excl.)'}{elseif $priceDisplay == 0}{l s='(tax incl.)'}{/if} {/if}</strong>
																</td>
																<td class="text-right">
																	{if $priceDisplay && $use_tax}
																		<span>{displayWtPriceWithCurrency price=($orderTotalInfo['total_rooms_te'] + $orderTotalInfo['total_services_te'] + $orderTotalInfo['total_auto_add_services_te'] + $orderTotalInfo['total_demands_price_te']) currency=$objOrderCurrency}</span>
																	{else}
																		<span>{displayWtPriceWithCurrency price=($orderTotalInfo['total_rooms_ti'] + $orderTotalInfo['total_services_ti'] + $orderTotalInfo['total_auto_add_services_ti'] + $orderTotalInfo['total_demands_price_ti']) currency=$objOrderCurrency}</span>
																	{/if}
																</td>
															</tr>
														{/if}
														{if (isset($cart_standalone_service_products) && $cart_standalone_service_products) || (isset($cart_hotel_service_products) && $cart_hotel_service_products)}
															<tr class="item">
																<td>
																	<strong>{l s='Total products cost'} {if $use_taxes && $display_tax_label == 1}{if $priceDisplay == 1}{l s='(tax excl.)'}{elseif $priceDisplay == 0}{l s='(tax incl.)'}{/if} {/if}</strong>
																</td>
																<td class="text-right">
																	{if $priceDisplay && $use_tax}
																		<span>{displayWtPriceWithCurrency price=$orderTotalInfo['total_standalone_products_te'] currency=$objOrderCurrency}</span>
																	{else}
																		<span>{displayWtPriceWithCurrency price=$orderTotalInfo['total_standalone_products_ti'] currency=$objOrderCurrency}</span>
																	{/if}
																</td>
															</tr>
														{/if}
														{if $order->total_wrapping > 0}
															<tr class="item">
																<td>
																	<strong>{l s='Total gift wrapping cost'}</strong>
																</td>
																<td class="text-right">
																	<span class="price-wrapping">{displayWtPriceWithCurrency price=($orderTotalInfo['total_wrapping'] * -1) currency=$objOrderCurrency}</span>
																</td>
															</tr>
														{/if}
														{if  $orderTotalInfo['total_convenience_fee_te'] || $orderTotalInfo['total_convenience_fee_ti']}
															<tr class="item">
																<td>
																	<strong>{l s='Total Convenience Fees'} {if $use_taxes && $display_tax_label == 1}{if $priceDisplay == 1}{l s='(tax excl.)'}{elseif $priceDisplay == 0}{l s='(tax incl.)'}{/if} {/if}</strong>
																</td>
																<td class="text-right">
																	{if $priceDisplay && $use_tax}
																		<span>{displayWtPriceWithCurrency price=($orderTotalInfo['total_convenience_fee_te']) currency=$objOrderCurrency}</span>
																	{else}
																		<span>{displayWtPriceWithCurrency price=($orderTotalInfo['total_convenience_fee_ti']) currency=$objOrderCurrency}</span>
																	{/if}
																</td>
															</tr>
														{/if}
														<tr class="item">
															<td>
																<strong>{l s='Total Tax'}</strong>
															</td>
															<td class="text-right">
																<span class="price-discount">{displayWtPriceWithCurrency price=$orderTotalInfo['total_tax_without_discount'] currency=$objOrderCurrency convert=1}</span>
															</td>
														</tr>
														{if $order->total_discounts > 0}
															<tr class="item">
																<td>
																	<strong>{l s='Total Vouchers'}</strong>
																</td>
																<td class="text-right">
																	{if $priceDisplay && $use_tax}
																		<span class="price-discount">{displayWtPriceWithCurrency price=($orderTotalInfo['total_discounts_te'] * -1) currency=$objOrderCurrency convert=1}</span>
																	{else}
																		<span class="price-discount">{displayWtPriceWithCurrency price=($orderTotalInfo['total_discounts'] * -1) currency=$objOrderCurrency convert=1}</span>
																	{/if}
																</td>
															</tr>
														{/if}
														<tr class="totalprice item">
															<td>
																<strong>{l s='Final Booking Total'}</strong>
															</td>
															<td class="text-right">
																<span>{displayWtPriceWithCurrency price=$orderTotalInfo['total_paid'] currency=$objOrderCurrency}</span>
															</td>
														</tr>
														{if $orderTotalInfo['total_paid'] > $orderTotalInfo['total_paid_real']}
															<tr class="item">
																<td>
																	<strong>{l s='Due Amount'}</strong>
																</td>
																<td class="text-right">
																	<span>{displayWtPriceWithCurrency price=($orderTotalInfo['total_paid'] - $orderTotalInfo['total_paid_real']) currency=$objOrderCurrency}</span>
																</td>
															</tr>
														{/if}


													</tbody>
												</table>
											</div>
										</div>
									</div>
								{/block}
							</div>
						</div>
					</div>
				{/block}
				<p>{l s='An email has been sent with this information.'}
					<br /><strong>{l s='Your booking has been received successfully and we are looking forward to welcoming you.'}</strong>
					<br />{l s='If you have questions, comments or concerns, please contact our'} <a class="cust_serv_lnk" href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='expert customer support team.'}</a>
				</p>
				<p class="cart_navigation exclusive">
					<a class="btn" href="{$link->getPageLink('history', true)|escape:'html':'UTF-8'}" title="{l s='Go to your order history page'}"><i class="icon-chevron-left"></i>{l s='View your order history'}</a>
				</p>
			{/if}
		{/if}
	</div>

	{* Fancybox for extra demands*}
	{block name='order_confirmation_room_extra_services'}
		<div style="display:none;" id="rooms_extra_services">
			{* <div id="rooms_type_extra_demands">
				<div class="panel">
					<div class="rooms_extra_demands_head">
						<h3>{l s='Additional Facilities'}</h3>
						<p class="rooms_extra_demands_text">{l s='Below are the additional facilities chosen by you in this booking'}</p>
					</div>
					<div id="room_type_demands_desc"></div>
				</div>
			</div> *}
		</div>
	{/block}
	{block name='order_confirmation_js_vars'}
		{strip}
			{addJsDef historyUrl=$link->getPageLink("orderdetail", true)|escape:'quotes':'UTF-8'}
			{addJsDefL name=req_sent_msg}{l s='Request Sent..' js=1}{/addJsDefL}
			{addJsDefL name=wait_stage_msg}{l s='Waiting' js=1}{/addJsDefL}
			{addJsDefL name=pending_state_msg}{l s='Pending...' js=1}{/addJsDefL}
			{addJsDefL name=mail_sending_err}{l s='Some error occurred while sending mail to the customer' js=1}{/addJsDefL}
			{addJsDefL name=refund_request_sending_error}{l s='Some error occurred while processing request for order cancellation.' js=1}{/addJsDefL}
		{/strip}
	{/block}
{/block}
