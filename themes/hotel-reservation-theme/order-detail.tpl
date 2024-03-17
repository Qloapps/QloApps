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
{if isset($order)}
	<div class="box box-small clearfix">
		{if isset($reorderingAllowed) && $reorderingAllowed}
		<form id="submitReorder" action="{if isset($opc) && $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}" method="post" class="submit">
			<input type="hidden" value="{$order->id}" name="id_order"/>
			<input type="hidden" value="" name="submitReorder"/>

			{* <a href="#" onclick="$(this).closest('form').submit(); return false;" class="button btn btn-default button-medium pull-right"><span>{l s='Reorder'}<i class="icon-chevron-right right"></i></span></a> --><!-- by webkul not to show reorder tab *}
		</form>
		{/if}
		<p class="dark">
			<strong>{l s='Order Reference %s - placed on' sprintf=$order->getUniqReference()} {dateFormat date=$order->date_add full=0}</strong>
		</p>
	</div>
	<div class="info-order box">
		{if $carrier->id}<p><strong class="dark">{l s='Carrier'}</strong> {if $carrier->name == "0"}{$shop_name|escape:'html':'UTF-8'}{else}{$carrier->name|escape:'html':'UTF-8'}{/if}</p>{/if}
		<p><strong class="dark">{l s='Payment method'}</strong> <span class="color-myaccount">{$order->payment|escape:'html':'UTF-8'}</span></p>
		{if $invoice AND $invoiceAllowed}
		<p>
			<i class="icon-file-text"></i>
			<a target="_blank" href="{$link->getPageLink('pdf-invoice', true)}?id_order={$order->id|intval}{if $is_guest}&amp;secure_key={$order->secure_key|escape:'html':'UTF-8'}{/if}">{l s='Download your invoice as a PDF file.'}</a>
		</p>
		{/if}
		{if $order->recyclable}
		<p><i class="icon-repeat"></i>&nbsp;{l s='You have given permission to receive your order in recycled packaging.'}</p>
		{/if}
		{if $order->gift}
			<p><i class="icon-gift"></i>&nbsp;{l s='You have requested gift wrapping for this order.'}</p>
			<p><strong class="dark">{l s='Message'}</strong> {$order->gift_message|nl2br}</p>
		{/if}
	</div>

	{* Advance payment information box *}
	{if $order->is_advance_payment && $order->advance_paid_amount > 0}
		<div class="info-order box">
			<strong><p>{l s='Please pay'} <span class="advance_paid_amount">{displayWtPriceWithCurrency price=$order->advance_paid_amount currency=$currency}</span> {l s='as an Advance Payment amount for the bookings of this order.'}</p></strong>
			<p class="back_o_msg">** {l s='Please ignore if already paid'}</p>
		</div>
	{/if}

	{if count($order_history)}
		<h1 class="page-heading">{l s='Follow your order\'s status step-by-step'}</h1>
		<div class="table_block">
			<table class="detail_step_by_step table table-bordered">
				<thead>
					<tr>
						<th class="first_item">{l s='Date'}</th>
						<th class="last_item">{l s='Status'}</th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$order_history item=state name="orderStates"}
					<tr class="{if $smarty.foreach.orderStates.first}first_item{elseif $smarty.foreach.orderStates.last}last_item{/if} {if $smarty.foreach.orderStates.index % 2}alternate_item{else}item{/if}">
						<td class="step-by-step-date">{dateFormat date=$state.date_add full=0}</td>
						<td>
                            <span{if isset($state.color) && $state.color} style="background-color:{$state.color|escape:'html':'UTF-8'}; border-color:{$state.color|escape:'html':'UTF-8'};"{/if} class="label{if isset($state.color) && Tools::getBrightness($state.color) > 128} dark{/if}">
                                {if $state.id_order_state|in_array:$overbooking_order_states}
                                    {l s='Order Not Confirmed'}
                                {else}
                                    {$state.ostate_name|escape:'html':'UTF-8'}
                                {/if}
                            </span>
                        </td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	{/if}

	{if isset($followup)}
		<p class="bold">{l s='Click the following link to track the delivery of your order'}</p>
		<a href="{$followup|escape:'html':'UTF-8'}">{$followup|escape:'html':'UTF-8'}</a>
	{/if}

	<div class="adresses_bloc">
		<div class="row">
			<div class="col-xs-12 col-sm-12">
				<ul class="address item {if $order->isVirtual()}full_width{/if} box">
					<li><h3 class="page-subheading">{l s='Guest Details'}</h3></li>
					{if $customerGuestDetail}
						{if isset($customerGuestDetail->firstname) && $customerGuestDetail->firstname}
							<li class="row"><div class="col-sm-3 col-md-2 col-xs-6">{l s='Name'}</div><div class="col-sm-9 col-xs-6">{$customerGuestDetail->firstname|escape:'html':'UTF-8'} {$customerGuestDetail->lastname|escape:'html':'UTF-8'}</div></li>
						{/if}
						{if isset($customerGuestDetail->email) && $customerGuestDetail->email}
							<li class="row"><div class="col-sm-3 col-md-2 col-xs-6">{l s='Email'}</div><div class="col-sm-9 col-xs-6">{$customerGuestDetail->email|escape:'html':'UTF-8'}</div></li>
						{/if}
						{if isset($customerGuestDetail->phone) && $customerGuestDetail->phone}
							<li class="row"><div class="col-sm-3 col-md-2 col-xs-6">{l s='Mobile Number'}</div><div class="col-sm-9 col-xs-6">{$customerGuestDetail->phone|escape:'html':'UTF-8'}</div></li>
						{/if}
					{else}
						{if isset($address_invoice->firstname) && $address_invoice->firstname}
							<li class="row"><div class="col-sm-3 col-md-2 col-xs-6">{l s='Name'}</div><div class="col-sm-9 col-xs-6">{$address_invoice->firstname|escape:'html':'UTF-8'} {$address_invoice->lastname|escape:'html':'UTF-8'}</div></li>
						{/if}
						{if isset($guestInformations['email']) && $guestInformations['email']}
							<li class="row"><div class="col-sm-3 col-md-2 col-xs-6">{l s='Email'}</div><div class="col-sm-9 col-xs-6">{$guestInformations['email']|escape:'html':'UTF-8'}</div></li>
						{/if}
						{if isset($address_invoice->phone_mobile) && $address_invoice->phone_mobile}
							<li class="row"><div class="col-sm-3 col-md-2 col-xs-6">{l s='Mobile Number'}</div><div class="col-sm-9 col-xs-6">{$address_invoice->phone_mobile|escape:'html':'UTF-8'}</div></li>
						{/if}
						{if isset($address_invoice->phone) && $address_invoice->phone}
							<li class="row"><div class="col-sm-3 col-md-2 col-xs-6">{l s='Phone Number'}</div><div class="col-sm-9 col-xs-6">{$address_invoice->phone|escape:'html':'UTF-8'}</div></li>
						{/if}
					{/if}
				</ul>
			</div>
		</div>
	</div>

	{$HOOK_ORDERDETAILDISPLAYED}
	{if !$is_guest}<form action="{$link->getPageLink('order-follow', true)|escape:'html':'UTF-8'}" method="post">{/if}

	{if isset($refundReqBookings) && $refundReqBookings}
		<div class="alert alert-warning-light cancel_requests_link_wrapper">
			<i class="icon-info-circle"></i> {l s='You have booking cancelation requests from this order. To see the cancelation request status'} <a target="_blank" href="{$link->getPageLink('order-follow')|escape:'html':'UTF-8'}?id_order={$order->id|escape:'html':'UTF-8'}">{l s='Click Here'}</a>
		</div>
	{/if}

	<div class="row booking-actions-wrap">
		<div class="col-xs-12 col-sm-12">
			{if $refund_allowed}
				{if !$completeRefundRequestOrCancel}
					<a refund_fields_on="0" id="order_refund_request" class="btn btn-default pull-right" href="#" title="{l s='Proceed to refund'}"><span>{l s='Cancel Bookings'}</span></a>
				{/if}

				{if isset($id_cms_refund_policy) && $id_cms_refund_policy}<a target="_blank" class="btn btn-default pull-right refund_policy_link" href="{$link->getCMSLink($id_cms_refund_policy)|escape:'html':'UTF-8'}">{l s='Refund Policies'}</a>{/if}
			{/if}
			{hook h='displayBookingAction' id_order=$order->id}
		</div>
	</div>

	{* Form Refund fields and submit refund *}
	<form id="order-detail-content">

		<div class="table_block table-responsive wk_booking_details_wrapper">
			<table class="table table-bordered">

				<tfoot>
					{assign var=room_price_tax_excl value=$order->getTotalProductsWithoutTaxes(false, true)}
					{assign var=room_price_tax_incl value=$order->getTotalProductsWithTaxes(false, true)}
					{* {assign var=service_products_price_tax_excl value=$order->getTotalProductsWithoutTaxes(false, false, Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE)}
					{assign var=service_products_price_tax_incl value=$order->getTotalProductsWithTaxes(false, false, Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE)} *}
					{assign var=additional_service_price_tax_excl value=($order->getTotalProductsWithoutTaxes(false, false, Product::SERVICE_PRODUCT_WITH_ROOMTYPE) + $total_demands_price_te)}
					{assign var=additional_service_price_tax_incl value=($order->getTotalProductsWithTaxes(false, false, Product::SERVICE_PRODUCT_WITH_ROOMTYPE) + $total_demands_price_ti)}
					{if $priceDisplay && $use_tax && $room_price_tax_excl}
						<tr class="item">
							{if $refund_allowed}
								<td class="standard_refund_fields"></td>
							{/if}
							<td colspan="4"></td>
							<td colspan="3">
								<strong>{l s='Total rooms cost (tax excl.)'}</strong>
							</td>
							<td colspan="2" class="text-right">
								<span class="price">{displayWtPriceWithCurrency price=($room_price_tax_excl + $additional_service_price_tax_excl - $total_convenience_fee_te) currency=$currency}</span>
							</td>
						</tr>
					{else}
						<tr class="item">
							{if $refund_allowed}
								<td class="standard_refund_fields"></td>
							{/if}
							<td colspan="4"></td>
							<td colspan="3">
								<strong>{l s='Total Rooms Cost'} {if $use_tax}{l s='(tax incl.)'}{/if} </strong>
							</td>
							<td colspan="2" class="text-right">
								<span class="price">{displayWtPriceWithCurrency price=($room_price_tax_incl + $additional_service_price_tax_incl - $total_convenience_fee_ti) currency=$currency}</span>
							</td>
						</tr>
					{/if}
					{* {if $priceDisplay && $use_tax && $service_products_price_tax_excl}
						<tr class="item">
							{if $refund_allowed}
								<td class="standard_refund_fields"></td>
							{/if}
							<td colspan="4"></td>
							<td colspan="3">
								<strong>{l s='Total service products cost (tax excl.)'}</strong>
							</td>
							<td colspan="2" class="text-right">
								<span class="price">{displayWtPriceWithCurrency price=$service_products_price_tax_excl currency=$currency}</span>
							</td>
						</tr>
					{/if}
					{if $service_products_price_tax_incl}
						<tr class="item">
							{if $refund_allowed}
								<td class="standard_refund_fields"></td>
							{/if}
							<td colspan="4"></td>
							<td colspan="3">
								<strong>{l s='Total service products cost'} {if $use_tax}{l s='(tax incl.)'}{/if} </strong>
							</td>
							<td colspan="2" class="text-right">
								<span class="price">{displayWtPriceWithCurrency price=$service_products_price_tax_incl currency=$currency}</span>
							</td>
						</tr>
					{/if} *}
					{if $order->total_wrapping > 0}
						<tr class="item">
							{if $refund_allowed}
								<td class="standard_refund_fields"></td>
							{/if}
							<td colspan="4"></td>
							<td colspan="3">
								<strong>{l s='Total gift wrapping cost'}</strong>
							</td>
							<td colspan="2" class="text-right">
								<span class="price-wrapping">{displayWtPriceWithCurrency price=$order->total_wrapping currency=$currency}</span>
							</td>
						</tr>
					{/if}

					{if $order->total_discounts > 0}
						<tr class="item">
							{if $refund_allowed}
								<td class="standard_refund_fields"></td>
							{/if}
							<td colspan="4"></td>
							<td colspan="3">
								<strong>{l s='Total Vouchers'}</strong>
							</td>
							<td colspan="2" class="text-right">
								<span class="price-discount">-{displayWtPriceWithCurrency price=$order->total_discounts currency=$currency convert=1}</span>
							</td>
						</tr>
					{/if}
					{if $priceDisplay && $use_tax && $total_convenience_fee_te}
						<tr class="item">
							{if $refund_allowed}
								<td class="standard_refund_fields"></td>
							{/if}
							<td colspan="4"></td>
							<td colspan="3">
								<strong>{l s='Total Convenience Fees (tax excl.)'}</strong>
							</td>
							<td colspan="2" class="text-right">
								<span class="price">{displayWtPriceWithCurrency price=$total_convenience_fee_te currency=$currency}</span>
							</td>
						</tr>
					{else if $total_convenience_fee_ti}
						<tr class="item">
							{if $refund_allowed}
								<td class="standard_refund_fields"></td>
							{/if}
							<td colspan="4"></td>
							<td colspan="3">
								<strong>{l s='Total Convenience Fees (tax incl.)'}</strong>
							</td>
							<td colspan="2" class="text-right">
								<span class="price">{displayWtPriceWithCurrency price=$total_convenience_fee_ti currency=$currency}</span>
							</td>
						</tr>
					{/if}

					<tr class="totalprice item">
						{if $refund_allowed}
							<td class="standard_refund_fields"></td>
						{/if}
						<td colspan="4"></td>
						<td colspan="3">
							<strong>{l s='Total Tax'}</strong>
						</td>
						<td colspan="2" class="text-right">
							<span class="price">{displayWtPriceWithCurrency price=($order->total_paid - $order->total_paid_tax_excl) currency=$currency}</span>
						</td>
					</tr>
					<tr class="totalprice item">
						{if $refund_allowed}
							<td class="standard_refund_fields"></td>
						{/if}
						<td colspan="4"></td>
						<td colspan="3">
							<strong>{l s='Final Order Total'}</strong>
						</td>
						<td colspan="2" class="text-right">
							<span class="price">{displayWtPriceWithCurrency price=$order->total_paid currency=$currency}</span>
						</td>
					</tr>
					{if $order->total_paid_tax_incl > $order->total_paid_real}
						<tr class="totalprice item">
							{if $refund_allowed}
								<td class="standard_refund_fields"></td>
							{/if}
							<td colspan="4"></td>
							<td colspan="3">
								<strong>{l s='Due Amount'}</strong>
							</td>
							<td colspan="2" class="text-right">
								<span class="price">{displayWtPriceWithCurrency price=($order->total_paid_tax_incl - $order->total_paid_real) currency=$currency}</span>
							</td>
						</tr>
					{/if}
					{* {if isset($order_adv_dtl) && $order_adv_dtl}
						<tr class="item">
							{if $refund_allowed}
								<td class="standard_refund_fields"></td>
							{/if}
							<td colspan="4"></td>
							<td colspan="3">
								<strong>{l s='Advance Paid Amount'}</strong>
							</td>
							<td colspan="2" class="text-right">
								<span class="price">{displayWtPriceWithCurrency price=$order_adv_dtl['total_paid_amount'] currency=$currency}</span>
							</td>
						</tr>
						<tr class="item">
							{if $refund_allowed}
								<td class="standard_refund_fields"></td>
							{/if}
							<td colspan="4"></td>
							<td colspan="3">
								<strong>{l s='Total Due'}</strong>
							</td>
							<td colspan="2" class="text-right">
								<span class="price">{displayWtPriceWithCurrency price=($order_adv_dtl['total_order_amount'] - $order_adv_dtl['total_paid_amount']) currency=$currency}</span>
							</td>
						</tr>
					{/if} *}

					{if isset($refundReqBookings) && $refundReqBookings}
						<tr class="totalprice item">
							{if $refund_allowed}
								<td class="standard_refund_fields"></td>
							{/if}
							<td colspan="4"></td>
							<td colspan="3">
								<strong>*{l s='Refunded Amount'}</strong>
							</td>
							<td colspan="2" class="text-right">
								<span class="price">{displayWtPriceWithCurrency price=$refundedAmount currency=$currency}</span>
							</td>
						</tr>
					{/if}
				</tfoot>

				{if isset($cart_htl_data) && $cart_htl_data}
					<thead>
						<tr>
							{if $refund_allowed}
								<th class="standard_refund_fields"></th>
							{/if}
							<th class="cart_product">{l s='Room Image'}</th>
							<th class="cart_description">{l s='Room Description'}</th>
							<th>{l s='Hotel Name'}</th>
							<th>{l s='Room Capacity'}</th>
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
									{if $refund_allowed}
										<td class="booking_refund_fields standard_refund_fields">
											{if isset($rm_v['ids_htl_booking_detail']) && $rm_v['ids_htl_booking_detail']}
												{foreach $rm_v['ids_htl_booking_detail'] as $key => $id_htl_booking}
													<div class="checkbox">
														<label for="bookings_to_refund">
															<input class="bookings_to_refund" type="checkbox" name="bookings_to_refund[]" value="{$id_htl_booking|escape:'html':'UTF-8'}" {if (isset($refundReqBookings) && ($id_htl_booking|in_array:$refundReqBookings)) || $rm_v['is_refunded']}disabled{/if}/> &nbsp;{l s='Room'}-{$key+1}
														</label>
													</div>
												{/foreach}
											{/if}
										</td>
									{/if}
									<td class="cart_product">
										<a href="{$link->getProductLink($data_v['id_product'])|escape:'html':'UTF-8'}">
											<img src="{$data_v['cover_img']|escape:'html':'UTF-8'}" class="img-responsive"/>
										</a>
									</td>
									<td class="cart_description">
										<p class="product-name">
											<a href="{$link->getProductLink($data_v['id_product'])}">
												{$data_v['name']|escape:'html':'UTF-8'}
											</a>
										</p>
									</td>
									<td>{$data_v['hotel_name']|escape:'html':'UTF-8'}</td>
									<td class="text-center">
										<p>
											{if $rm_v['adults'] <= 9}0{$rm_v['adults']}{else}{$rm_v['adults']}{/if} {if $rm_v['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if $rm_v['children']}, {if $rm_v['children'] <= 9}0{$rm_v['children']}{else} {$rm_v['children']}{/if} {if $rm_v['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if}<br>{if $rm_v['num_rm'] <= 9}0{/if}{$rm_v['num_rm']} {if $rm_v['num_rm'] > 1}{l s='Rooms'}{else}{l s='Room'}{/if}
										</p>
										{if $rm_v['count_cancelled'] > 0}
											<span class="badge badge-danger">{$rm_v['count_cancelled']} {l s='Room(s) Cancelled'}</span>
											<br>
										{/if}
										{if $rm_v['count_refunded'] > 0}
											<span class="badge badge-danger">{$rm_v['count_refunded']} {l s='Room(s) Refunded'}</span>
										{/if}
									</td>
									<td class="text-center">{$rm_v['data_form']|date_format:"%d-%m-%Y"}</td>
									<td class="text-center">{$rm_v['data_to']|date_format:"%d-%m-%Y"}</td>

									<td>
										{if (isset($rm_v['extra_demands']) && $rm_v['extra_demands']) || isset($rm_v['additional_services']) && $rm_v['additional_services']}
											<a data-date_from="{$rm_v['data_form']}" data-date_to="{$rm_v['data_to']}" data-id_product="{$data_v['id_product']}" data-id_order="{$order->id}" data-action="{$link->getPageLink({$page_name})}" class="open_rooms_extra_services_panel" href="#rooms_type_extra_services_form">
										{/if}
										{if $group_use_tax}
											{displayWtPriceWithCurrency price=($rm_v['extra_demands_price_ti'] + $rm_v['additional_services_price_ti'])  currency=$currency}
										{else}
											{displayWtPriceWithCurrency price=($rm_v['extra_demands_price_te'] + $rm_v['additional_services_price_te']) currency=$currency}
										{/if}
										{if (isset($rm_v['extra_demands']) && $rm_v['extra_demands']) || isset($rm_v['additional_services']) && $rm_v['additional_services']}
											</a>
										{/if}
									</td>
									<td class="cart_total text-left">
										{if $group_use_tax}
											{displayWtPriceWithCurrency price=($rm_v['amount_tax_incl'] + $rm_v['extra_demands_price_ti'] + $rm_v['additional_services_price_ti'] + $rm_v['additional_services_price_auto_add_ti']) currency=$currency}
										{else}
											{displayWtPriceWithCurrency price=($rm_v['amount_tax_excl'] + $rm_v['extra_demands_price_te'] + $rm_v['additional_services_price_te'] + $rm_v['additional_services_price_auto_add_te']) currency=$currency}
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
																	{displayWtPriceWithCurrency price=($rm_v['amount_tax_excl'] + $rm_v['additional_services_price_auto_add_te']) currency=$currency}
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
									</td>
								</tr>
							{/foreach}
						{/foreach}
					</tbody>
				{/if}


				{if isset($cart_service_products) && $cart_service_products}
					<thead>
						<tr>
							<th colspan="1">{l s='Image'}</th>
							<th colspan="3">{l s='Name'}</th>
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
								<td class="cart_product" colspan="3">
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
								<td colspan="2">
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
				<tbody>
					{foreach from=$discounts item=discount}
						<tr class="item">
							{if $refund_allowed}
								<td class="standard_refund_fields"></td>
							{/if}
							<td class="text-center">{$discount.name|escape:'html':'UTF-8'}</td>
							<td class="text-center">{l s='Voucher'} {$discount.name|escape:'html':'UTF-8'}</td>
							<td class="text-center" colspan="5"><span class="order_qte_span editable">1</span></td>
							<td class="text-right" colspan="2">{if $discount.value != 0.00}-{/if}{convertPriceWithCurrency price=$discount.value currency=$currency}</td>
							{* {if $refund_allowed}
							<td>&nbsp;</td>
							{/if} *}
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>

		{if $refund_allowed && !$completeRefundRequestOrCancel}
			<div class="alert alert-info-light standard_refund_fields">
				<i class="icon-info-circle"></i> {l s='Select rooms for which you want to cancel bookings. Services for cancelled rooms will be cancelled automatically.'}
			</div>
            <div class="row standard_refund_fields">
                <div class="col-sm-12">
                    <button type="button" id="order_refund_request_submit" class="btn pull-right"><span>{l s='Submit'}</span></button>
                </div>
            </div>
		{/if}
	</form>

	{if $any_back_order}
		{if $shw_bo_msg}
			<p class="back_o_msg"><strong><sup>*</sup>{l s='Some of your rooms are on back order. Please read the following message for rooms with status on backorder'}</strong></p>
			<p>
				-&nbsp;&nbsp;{$back_ord_msg}
			</p>
		{/if}
	{/if}

	{if !$is_guest}</form>{/if}
	{assign var='carriers' value=$order->getShipping()}
	{if $carriers|count > 0 && isset($carriers.0.carrier_name) && $carriers.0.carrier_name}
		<table class="table table-bordered footab">
			<thead>
				<tr>
					<th class="first_item">{l s='Date'}</th>
					<th class="item" data-sort-ignore="true">{l s='Carrier'}</th>
					<th data-hide="phone" class="item">{l s='Weight'}</th>
					<th data-hide="phone" class="item">{l s='Shipping cost'}</th>
					<th data-hide="phone" class="last_item" data-sort-ignore="true">{l s='Tracking number'}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$carriers item=line}
				<tr class="item">
					<td data-value="{$line.date_add|regex_replace:"/[\-\:\ ]/":""}">{dateFormat date=$line.date_add full=0}</td>
					<td>{$line.carrier_name}</td>
					<td data-value="{if $line.weight > 0}{$line.weight|string_format:"%.3f"}{else}0{/if}">{if $line.weight > 0}{$line.weight|string_format:"%.3f"} {Configuration::get('PS_WEIGHT_UNIT')}{else}-{/if}</td>
					<td data-value="{if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}{$line.shipping_cost_tax_incl}{else}{$line.shipping_cost_tax_excl}{/if}">{if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}{displayPrice price=$line.shipping_cost_tax_incl currency=$currency->id}{else}{displayPrice price=$line.shipping_cost_tax_excl currency=$currency->id}{/if}</td>
					<td>
						<span class="shipping_number_show">{if $line.tracking_number}{if $line.url && $line.tracking_number}<a href="{$line.url|replace:'@':$line.tracking_number}">{$line.tracking_number}</a>{else}{$line.tracking_number}{/if}{else}-{/if}</span>
					</td>
				</tr>
				{/foreach}
			</tbody>
		</table>
	{/if}

	{if !$is_guest}
		{if count($messages)}
			<h3 class="page-heading">{l s='Messages'}</h3>
			<div class="table_block">
				<table class="detail_step_by_step table table-bordered">
					<thead>
						<tr>
							<th class="first_item" style="width:150px;">{l s='From'}</th>
							<th class="last_item">{l s='Message'}</th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$messages item=message name="messageList"}
						<tr class="{if $smarty.foreach.messageList.first}first_item{elseif $smarty.foreach.messageList.last}last_item{/if} {if $smarty.foreach.messageList.index % 2}alternate_item{else}item{/if}">
							<td>
								<strong class="dark">
									{if isset($message.elastname) && $message.elastname}
										{$message.efirstname|escape:'html':'UTF-8'} {$message.elastname|escape:'html':'UTF-8'}
									{elseif $message.clastname}
										{$message.cfirstname|escape:'html':'UTF-8'} {$message.clastname|escape:'html':'UTF-8'}
									{else}
										{$shop_name|escape:'html':'UTF-8'}
									{/if}
								</strong>
								<br />
								{dateFormat date=$message.date_add full=1}
							</td>
							<td>{$message.message|escape:'html':'UTF-8'|nl2br}</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
			{/if}
			{if isset($errors) && $errors}
				<div class="alert alert-danger">
					<p>{if $errors|@count > 1}{l s='There are %d errors' sprintf=$errors|@count}{else}{l s='There is %d error' sprintf=$errors|@count}{/if}</p>
					<ol>
					{foreach from=$errors key=k item=error}
						<li>{$error}</li>
					{/foreach}
					</ol>
				</div>
			{/if}
			{if isset($message_confirmation) && $message_confirmation}
			<p class="alert alert-success">
				{l s='Message successfully sent'}
			</p>
			{/if}
			<form action="{$link->getPageLink('order-detail', true)|escape:'html':'UTF-8'}" method="post" class="std" id="sendOrderMessage">
				<h3 class="page-heading bottom-indent">{l s='Add a message'}</h3>
				<p>{l s='If you would like to add a comment about your order, please write it in the field below.'}</p>
				<p class="form-group">
				<label for="id_product">{l s='Room Type'}</label>
					<select name="id_product" class="form-control">
						<option value="0">{l s='-- Choose --'}</option>
						{foreach from=$products item=product name=products}
							{if $product.is_booking_product}
								<option value="{$product.product_id}">{$product.product_name|escape:'html':'UTF-8'}</option>
							{/if}
						{/foreach}
					</select>
				</p>
				<p class="form-group">
					<textarea class="form-control" cols="67" rows="3" name="msgText"></textarea>
				</p>
				<div class="submit">
					<input type="hidden" name="id_order" value="{$order->id|intval|escape:'html':'UTF-8'}" />
					<input type="submit" class="unvisible" name="submitMessage" value="{l s='Send'}"/>
					<button type="submit" name="submitMessage" class="button btn btn-default button-medium"><span>{l s='Send'}<i class="icon-chevron-right right"></i></span></button>
				</div>
			</form>
		{else}
			<p class="alert alert-info"><i class="icon-info-sign"></i> {l s='You cannot request refund with a guest account'}</p>
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

	{* Fancybox *}
	<div style="display: none;" id="reason_fancybox_content">
		<div id="htlRefundReasonForm">
			<h2 class="refund_reason_head">
				{l s='Mention a reason for cancelation'}
			</h2>
			<div class="refundReasonFormContent">
				<input type="hidden" id="bookings_to_refund">
				<textarea class="form-control reasonForRefund" rows="4" name="reasonForRefund" placeholder="{l s='Type here....'}"></textarea>
				<div>
					<p class="required required_err" style="color:#AA1F00; display:none"><sup>*</sup> {l s='Required field'}</p><br>
					<p class="reason_submit_wrapper">
						<button  name="submit_refund_reason" type="button" id="submit_refund_reason" class="btn"  data-id_order="{$order->id|intval|escape:'html':'UTF-8'}"><span>{l s='Submit'}</span></button>
					</p>
				</div>
			</div>
		</div>
	</div>

	<div class="loading_overlay">
		<img src="{$THEME_DIR}img/ajax-loader.gif" class="loading-img"/>
	</div>
