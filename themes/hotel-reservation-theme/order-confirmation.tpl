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

{capture name=path}{l s='Order confirmation'}{/capture}

<h1 class="page-heading">{l s='Order confirmation'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{include file="$tpl_dir./errors.tpl"}

{$HOOK_ORDER_CONFIRMATION}
<div class="box">
	{$HOOK_PAYMENT_RETURN}
	{if isset($order->id) && $order->id}
		{if $is_guest}
			<p>{l s='Your order ID is:'} <span class="bold">{$id_order_formatted}</span> . {l s='Your order ID has been sent via email.'}</p>
		    <p class="cart_navigation exclusive">
			<a class="button-exclusive btn btn-default" href="{$link->getPageLink('guest-tracking', true, NULL, "id_order={$reference_order|urlencode}&email={$email|urlencode}")|escape:'html':'UTF-8'}" title="{l s='Follow my order'}"><i class="icon-chevron-left"></i>{l s='Follow my order'}</a>
		    </p>
		{else}
			{if isset($is_free_order) && $is_free_order}
				<p class="alert alert-success">{l s='Your order on %s is complete.' sprintf=$shop_name}</p><br />
			{/if}
			<p><strong>{l s='Order Status :'}</strong> <span>{l s='Confirmed'}</span></p>
			<p><strong>{l s='Order Reference :'}</strong> <span class="bold">{$order->reference}</span></p>
			{if $any_back_order}
				{if $shw_bo_msg}
					<br>
					<p class="back_o_msg"><strong><sup>*</sup>{l s='Some of your rooms are on back order. Please read the following message for rooms with status on backorder'}</strong></p>
					<p>
						-&nbsp;&nbsp;{$back_ord_msg}
					</p>
				{/if}
			{/if}
			<hr>
			<p><strong>{l s='Order Details -'}</strong></p>
			<div id="order-detail-content" class="">
				<table class="table table-bordered">
					{if isset($cart_htl_data)}
						<thead>
							<tr>
								<th class="cart_product">{l s='Room Image'}</th>
								<th class="cart_description">{l s='Room Description'}</th>
								<th>{l s='Hotel Name'}</th>
								<th>{l s='Rooms'}</th>
								<th>{l s='Check-in Date'}</th>
								<th>{l s='Check-out Date'}</th>
								<th>{l s='Extra Services'}</th>
								<th class="cart_total">{l s='Total'}</th>
							</tr>
						</thead>
						<tbody>
							{foreach from=$cart_htl_data key=data_k item=data_v}
								{foreach from=$data_v['date_diff'] key=rm_k item=rm_v}
									<tr class="table_body">
										<td class="cart_product">
											<a href="{$link->getProductLink($data_v['id_product'])}">
												<img src="{$data_v['cover_img']}" class="img-responsive"/>
											</a>
										</td>
										<td class="cart_description">
											<p class="product-name">
												<a href="{$link->getProductLink($data_v['id_product'])}">
													{$data_v['name']}
												</a>
											</p>
										</td>
										<td>{$data_v['hotel_name']}</td>
										<td class="text-center">
											<p>
												{if $rm_v['adults'] <= 9}0{$rm_v['adults']}{else}{$rm_v['adults']}{/if} {if $rm_v['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if $rm_v['children']}, {if $rm_v['children'] <= 9}0{$rm_v['children']}{else} {$rm_v['children']}{/if} {if $rm_v['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if}<br>{if $rm_v['num_rm'] <= 9}0{/if}{$rm_v['num_rm']} {if $rm_v['num_rm'] > 1}{l s='Rooms'}{else}{l s='Room'}{/if}
											</p>
										</td>
										<td class="text-center">
											<p>
												{$rm_v['data_form']|date_format:"%d-%m-%Y"}
											</p>
										</td>
										<td class="text-center">
											<p>
												{$rm_v['data_to']|date_format:"%d-%m-%Y"}
											</p>
										</td>
										<td>
											<p class="text-center">

												{if (isset($rm_v['extra_demands']) && $rm_v['extra_demands']) || isset($rm_v['additional_services']) && $rm_v['additional_services']}
														<a data-date_from="{$rm_v['data_form']}" data-date_to="{$rm_v['data_to']}" data-id_product="{$data_v['id_product']}" data-id_order="{$order->id}" data-action="{$link->getPageLink('order-detail')}" class="open_rooms_extra_services_panel" href="#rooms_type_extra_services_form">
												{/if}
												{if $group_use_tax}
													{displayWtPriceWithCurrency price=($rm_v['extra_demands_price_ti'] + $rm_v['additional_services_price_ti'])  currency=$currency}
												{else}
													{displayWtPriceWithCurrency price=($rm_v['extra_demands_price_te'] + $rm_v['additional_services_price_te']) currency=$currency}
												{/if}
												{if (isset($rm_v['extra_demands']) && $rm_v['extra_demands']) || isset($rm_v['additional_services']) && $rm_v['additional_services']}
													</a>
												{/if}
											</p>
										</td>
										<td class="cart_total text-left">
											<p class="text-left">
												{if $group_use_tax}
													{displayWtPriceWithCurrency price=($rm_v['amount_tax_incl'] + $rm_v['extra_demands_price_ti'] + $rm_v['additional_services_price_ti'] + $rm_v['additional_services_price_auto_add_ti']) currency=$currency}
												{else}
													{displayWtPriceWithCurrency price=($rm_v['amount_tax_excl'] + $rm_v['extra_demands_price_te'] + $rm_v['additional_services_price_te'] +  $rm_v['additional_services_price_auto_add_te']) currency=$currency}
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
																			{displayWtPriceWithCurrency price=($rm_v['amount_tax_incl'] + $rm_v['additional_services_price_auto_add_ti']) currency=$currency}
																		{else}
																			{displayWtPriceWithCurrency price=($rm_v['amount_tax_excl'] +  $rm_v['additional_services_price_auto_add_te']) currency=$currency}
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
																			{displayWtPriceWithCurrency price=($rm_v['extra_demands_price_ti'] + $rm_v['additional_services_price_ti'])  currency=$currency}
																		{else}
																			{displayWtPriceWithCurrency price=($rm_v['extra_demands_price_te'] + $rm_v['additional_services_price_te']) currency=$currency}
																		{/if}
																	</p>
																</div>
															</div>
														</div>
													</div>
												{/if}
											</p>
										</td>
										{if isset($orders_has_invoice) && $orders_has_invoice && $order->payment != 'Free order'}
										{/if}
										{* <td class="text-center">
											{if isset($rm_v['is_backorder']) && $rm_v['is_backorder']}
												{l s='On Backorder'}
											{else}
												--
											{/if}
										</td> *}
									</tr>
								{/foreach}
							{/foreach}
						</tbody>
					{/if}
					{if isset($cart_service_products)}
						<thead>
							<tr>
								<th colspan="1">{l s='Image'}</th>
								<th colspan="2">{l s='Name'}</th>
								<th colspan="2">{l s='Unit Price'}</th>
								<th colspan="1">{l s='Quantity'}</th>
								<th colspan="2" class="cart_total">{l s='Total'}</th>
							</tr>
						</thead>
						<tbody>
							{foreach from=$cart_service_products key=data_k item=data_v}
								<tr class="table_body">
									<td class="cart_product">
										<a href="{$link->getProductLink($data_v['id_product'])}">
											<img src="{$data_v['cover_img']}" class="img-responsive"/>
										</a>
									</td>
									<td class="cart_product" colspan="2">
										<p class="product-name">
											<a href="{$link->getProductLink($data_v['id_product'])}">
												{$data_v['product_name']}
											</a>
										</p>
									</td>
									<td class="cart_unit" colspan="2">
										<p class="text-center">
											{if $group_use_tax}
												{displayWtPriceWithCurrency price=$data_v['unit_price_tax_incl'] currency=$currency}
												{* {displayPrice price=$data_v['unit_price_tax_incl']|floatval|round:2} *}
											{else}
												{* {displayPrice price=$data_v['unit_price_tax_excl']|floatval|round:2} *}
												{displayWtPriceWithCurrency price=$data_v['unit_price_tax_excl'] currency=$currency}
											{/if}
										</p>
									</td>
									<td>
										<p class="text-center">
											{$data_v['product_quantity']}
										</p>
									</td>
									<td>
										<p class="text-left" colspan="2">
											{if $group_use_tax}
												{displayWtPriceWithCurrency price=$data_v['total_price_tax_incl'] currency=$currency}
											{else}
												{displayWtPriceWithCurrency price=$data_v['total_price_tax_excl'] currency=$currency}
											{/if}
										</p>
									</td>
								</tr>
							{/foreach}
						</tbody>
					{/if}
					<tfoot>
						{if isset($cart_htl_data)}
							{if $priceDisplay && $use_tax}
								<tr class="item">
									<td colspan="3"></td>
									<td colspan="3">
										<strong>{l s='Total Rooms Cost (tax excl.)'}</strong>
									</td>
									<td colspan="2">
										<span>{displayWtPriceWithCurrency price=($orderTotalInfo['total_rooms_te'] + $orderTotalInfo['total_services_te'] + $orderTotalInfo['total_auto_add_services_te'] + $orderTotalInfo['total_demands_price_te']) currency=$currency}</span>
									</td>
								</tr>
							{else}
								<tr class="item">
									<td colspan="3"></td>
									<td colspan="3">
										<strong>{l s='Total Rooms Cost'} {if $use_tax}{l s='(tax incl.)'}{/if} </strong>
									</td>
									<td colspan="2">
										<span>{displayWtPriceWithCurrency price=($orderTotalInfo['total_rooms_ti'] + $orderTotalInfo['total_services_ti'] + $orderTotalInfo['total_auto_add_services_ti'] + $orderTotalInfo['total_demands_price_ti']) currency=$currency}</span>
									</td>
								</tr>
							{/if}
						{/if}
						{* {if isset($cart_service_products) && $cart_service_products}
							{if $priceDisplay && $use_tax}
								<tr class="item">
									<td colspan="3"></td>
									<td colspan="3">
										<strong>{l s='Total service products cost (tax excl.)'}</strong>
									</td>
									<td colspan="2">
										<span>{displayWtPriceWithCurrency price=$orderTotalInfo['total_service_products_te'] currency=$currency}</span>
									</td>
								</tr>
							{/if}
							<tr class="item">
								<td colspan="3"></td>
								<td colspan="3">
									<strong>{l s='Total service products cost'} {if $use_tax}{l s='(tax incl.)'}{/if} </strong>
								</td>
								<td colspan="2">
									<span>{displayWtPriceWithCurrency price=$orderTotalInfo['total_service_products_ti'] currency=$currency}</span>
								</td>
							</tr>
						{/if} *}
						{* {if $orderTotalInfo['total_services_te'] > 0}
							{if $priceDisplay && $use_tax}
								<tr class="item">
									<td colspan="3"></td>
									<td colspan="3">
										<strong>{l s='Total extra services cost (tax excl.)'}</strong>
									</td>
									<td colspan="2">
										<span>{displayWtPriceWithCurrency price=$orderTotalInfo['total_services_te'] currency=$currency}</span>
									</td>
								</tr>
							{/if}
							<tr class="item">
								<td colspan="3"></td>
								<td colspan="3">
									<strong>{l s='Total extra services cost'} {if $use_tax}{l s='(tax incl.)'}{/if}</strong>
								</td>
								<td colspan="2">
									<span>{displayWtPriceWithCurrency price=$orderTotalInfo['total_services_ti'] currency=$currency convert=1}</span>
								</td>
							</tr>
						{/if} *}
						{if $order->total_wrapping > 0}
							<tr class="item">
								<td colspan="3"></td>
								<td colspan="3">
									<strong>{l s='Total gift wrapping cost'}</strong>
								</td>
								<td colspan="2">
									<span class="price-wrapping">{displayWtPriceWithCurrency price=($orderTotalInfo['total_wrapping'] * -1) currency=$currency}</span>
								</td>
							</tr>
						{/if}
						{if $order->total_discounts > 0}
							<tr class="item">
								<td colspan="3"></td>
								<td colspan="3">
									<strong>{l s='Total Vouchers'}</strong>
								</td>
								<td colspan="2">
									{if $priceDisplay && $use_tax}
										<span class="price-discount">{displayWtPriceWithCurrency price=($orderTotalInfo['total_discounts_te'] * -1) currency=$currency convert=1}</span>
									{else}
										<span class="price-discount">{displayWtPriceWithCurrency price=($orderTotalInfo['total_discounts'] * -1) currency=$currency convert=1}</span>
									{/if}
								</td>
							</tr>
						{/if}
						{if $priceDisplay && $use_tax && $orderTotalInfo['total_convenience_fee_te']}
							<tr class="item">
								<td colspan="3"></td>
								<td colspan="3">
									<strong>{l s='Total Convenience Fees (tax excl.)'}</strong>
								</td>
								<td colspan="2">
									<span>{displayWtPriceWithCurrency price=($orderTotalInfo['total_convenience_fee_te']) currency=$currency}</span>
								</td>
							</tr>
						{else if $orderTotalInfo['total_convenience_fee_ti']}
							<tr class="item">
								<td colspan="3"></td>
								<td colspan="3">
									<strong>{l s='Total Convenience Fees'} {if $use_tax}{l s='(tax incl.)'}{/if} </strong>
								</td>
								<td colspan="2">
									<span>{displayWtPriceWithCurrency price=($orderTotalInfo['total_convenience_fee_ti']) currency=$currency}</span>
								</td>
							</tr>
						{/if}
						<tr class="item">
							<td colspan="3"></td>
							<td colspan="3">
								<strong>{l s='Total Tax'}</strong>
							</td>
							<td colspan="2">
								<span class="price-discount">{displayWtPriceWithCurrency price=$orderTotalInfo['total_tax'] currency=$currency convert=1}</span>
							</td>
						</tr>
						<tr class="totalprice item">
							<td colspan="3"></td>
							<td colspan="3">
								<strong>{l s='Final Booking Total'}</strong>
							</td>
							<td colspan="2">
								<span>{displayWtPriceWithCurrency price=$orderTotalInfo['total_paid'] currency=$currency}</span>
							</td>
						</tr>
						{if $orderTotalInfo['total_paid'] > $orderTotalInfo['total_paid_real']}
							<tr class="item">
								<td colspan="3"></td>
								<td colspan="3">
									<strong>{l s='Due Amount'}</strong>
								</td>
								<td colspan="2">
									<span>{displayWtPriceWithCurrency price=($orderTotalInfo['total_paid'] - $orderTotalInfo['total_paid_real']) currency=$currency}</span>
								</td>
							</tr>
						{/if}
					</tfoot>
				</table>
			</div>
			<p>{l s='An email has been sent with this information.'}
				<br /><strong>{l s='Your booking has been received successfully and we are looking forward to welcoming you.'}</strong>
				<br />{l s='If you have questions, comments or concerns, please contact our'} <a class="cust_serv_lnk" href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='expert customer support team.'}</a>
			</p>
		</div>
		<p class="cart_navigation exclusive">
			<a class="btn" href="{$link->getPageLink('history', true)|escape:'html':'UTF-8'}" title="{l s='Go to your order history page'}"><i class="icon-chevron-left"></i>{l s='View your order history'}</a>
		</p>
	{/if}
{/if}

{* Fancybox for extra demands*}
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
{strip}
	{addJsDef historyUrl=$link->getPageLink("orderdetail", true)|escape:'quotes':'UTF-8'}
	{addJsDefL name=req_sent_msg}{l s='Request Sent..' js=1}{/addJsDefL}
	{addJsDefL name=wait_stage_msg}{l s='Waiting' js=1}{/addJsDefL}
	{addJsDefL name=pending_state_msg}{l s='Pending...' js=1}{/addJsDefL}
	{addJsDefL name=mail_sending_err}{l s='Some error occurred while sending mail to the customer' js=1}{/addJsDefL}
	{addJsDefL name=refund_request_sending_error}{l s='Some error occurred while processing request for order cancellation.' js=1}{/addJsDefL}
{/strip}