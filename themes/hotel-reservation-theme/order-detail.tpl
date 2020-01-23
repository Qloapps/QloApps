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
				<td><span{if isset($state.color) && $state.color} style="background-color:{$state.color|escape:'html':'UTF-8'}; border-color:{$state.color|escape:'html':'UTF-8'};"{/if} class="label{if isset($state.color) && Tools::getBrightness($state.color) > 128} dark{/if}">{$state.ostate_name|escape:'html':'UTF-8'}</span></td>
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
				<li><h3 class="page-subheading">{l s='Customer Details'}</h3></li>
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
			</ul>
		</div>
	</div>
</div>
{$HOOK_ORDERDETAILDISPLAYED}
{if !$is_guest}<form action="{$link->getPageLink('order-follow', true)|escape:'html':'UTF-8'}" method="post">{/if}
{if !$is_guest
	&& isset($order_has_invoice)
	&& $order_has_invoice
	&& ($order->payment != 'Free order' || (isset($order_adv_dtl) && $order_adv_dtl && $order_adv_dtl['total_paid_amount'] > 0))}
	<div class="row totalOrdercancellation_div" {if !$non_requested_rooms}style="display:none;"{/if}>
		<div class="col-xs-12 col-sm-12">
			<p style="text-align:center;">
				<a class="terms_btn btn btn-default pull-right" href="{$redirect_link_terms}" target="_blank">
					<i class="icon-file-text large"></i>&nbsp;&nbsp;{l s='Terms & Conditions'}
				</a>
			</p>
			<button type="button" data-id_order="{$order->id}" data-id_currency="{$order->id_currency}" data-id_customer="{$order->id_customer}" data-order_data='{$cart_htl_data|@json_encode}' name="totalOrdercancellation_btn" class="totalOrdercancellation_btn btn btn-default pull-right" href="#htlRefundReasonForm"><span>{l s='Request Total Order Cancellation'}</span></button>
		</div>
	</div>
{/if}
<div id="order-detail-content" class="table_block table-responsive">
	<table class="table table-bordered">
		<thead>
			<tr>
				{* {if $return_allowed}<th class="first_item"><input type="checkbox" /></th>{/if}
				<th class="{if $return_allowed}item{else}first_item{/if}">{l s='Reference'}</th>
				<th class="item">{l s='Product'}</th>
				<th class="item">{l s='Quantity'}</th>
				{if $order->hasProductReturned()}
					<th class="item">{l s='Returned'}</th>
				{/if}
				<th class="item">{l s='Unit price'}</th>
				<th class="last_item">{l s='Total price'}</th> *}

				<th class="cart_product">{l s='Room Image'}</th>
				<th class="cart_description">{l s='Room Description'}</th>
				<th>{l s='Hotel Name'}</th>
				<th>{l s='Room Capacity'}</th>
				<th class="cart_unit">{l s='Unit Price'}</th>
				<th>{l s='Rooms'}</th>
				<th>{l s='Check-in Date'}</th>
				<th>{l s='Check-out Date'}</th>
				<th class="cart_total">{l s='Total'}</th>
				{if !$is_guest
					&& isset($order_has_invoice)
					&& $order_has_invoice
					&& ($order->payment != 'Free order' || (isset($order_adv_dtl) && $order_adv_dtl && $order_adv_dtl['total_paid_amount'] > 0))}
					<th>{l s='Request Refund'}</th>
					<th>{l s='Refund Stage'}</th>
					<th>{l s='Refund Status'}</th>
				{/if}
				{* <th>{l s='Back-Order Status'}</th> *}
			</tr>
		</thead>
		<tfoot>
			{if $priceDisplay && $use_tax}
				<tr class="item">
					<td colspan={if !$is_guest
						&& isset($order_has_invoice)
						&& $order_has_invoice
						&& ($order->payment != 'Free order' || (isset($order_adv_dtl) && $order_adv_dtl && $order_adv_dtl['total_paid_amount'] > 0))}"8"{else}"5"{/if}></td>
					<td colspan="{if $return_allowed}3{else}3{/if}">
						<strong>{l s='Items (tax excl.)'}</strong>
					</td>
					<td colspan="{if $order->hasProductReturned()}5{else}1{/if}" class="text-right">
						<span class="price">{displayWtPriceWithCurrency price=$order->getTotalProductsWithoutTaxes() currency=$currency}</span>
					</td>
				</tr>
			{/if}
			<tr class="item">
				<td colspan={if !$is_guest
					&& isset($order_has_invoice)
					&& $order_has_invoice
					&& ($order->payment != 'Free order' || (isset($order_adv_dtl) && $order_adv_dtl && $order_adv_dtl['total_paid_amount'] > 0))}"8"{else}"5"{/if}></td>
				<td colspan="{if $return_allowed}2{else}3{/if}">
					<strong>{l s='Items'} {if $use_tax}{l s='(tax incl.)'}{/if} </strong>
				</td>
				<td colspan="{if $order->hasProductReturned()}5{else}1{/if}" class="text-right">
					<span class="price">{displayWtPriceWithCurrency price=$order->getTotalProductsWithTaxes() currency=$currency}</span>
				</td>
			</tr>
			{if $total_demands_price_te}
				{if $priceDisplay && $use_tax}
					<tr class="item">
						<td colspan={if !$is_guest
							&& isset($order_has_invoice)
							&& $order_has_invoice
							&& ($order->payment != 'Free order' || (isset($order_adv_dtl) && $order_adv_dtl && $order_adv_dtl['total_paid_amount'] > 0))}"8"{else}"5"{/if}></td>
						<td colspan="{if $return_allowed}3{else}3{/if}">
							<strong>{l s='Additional facilities Cost (tax excl.)'}</strong>
						</td>
						<td colspan="{if $order->hasProductReturned()}5{else}1{/if}" class="text-right">
							<span>{displayWtPriceWithCurrency price=$total_demands_price_te currency=$currency}</span>
						</td>
					</tr>
				{/if}
				<tr class="item">
					<td colspan={if !$is_guest
						&& isset($order_has_invoice)
						&& $order_has_invoice
						&& ($order->payment != 'Free order' || (isset($order_adv_dtl) && $order_adv_dtl && $order_adv_dtl['total_paid_amount'] > 0))}"8"{else}"5"{/if}></td>
					<td colspan="{if $return_allowed}2{else}3{/if}">
						<strong>{l s='Additional facilities Cost (tax incl.)'}</strong>
					</td>
					<td colspan="{if $order->hasProductReturned()}5{else}1{/if}" class="text-right">
						<span>{displayWtPriceWithCurrency price=$total_demands_price_ti currency=$currency convert=1}</span>
					</td>
				</tr>
			{/if}
			{if $order->total_wrapping > 0}
			<tr class="item">
				<td colspan={if !$is_guest
					&& isset($order_has_invoice)
					&& $order_has_invoice
					&& ($order->payment != 'Free order' || (isset($order_adv_dtl) && $order_adv_dtl && $order_adv_dtl['total_paid_amount'] > 0))}"8"{else}"5"{/if}></td>
				<td colspan="{if $return_allowed}2{else}3{/if}">
					<strong>{l s='Total gift wrapping cost'}</strong>
				</td>
				<td colspan="{if $order->hasProductReturned()}5{else}1{/if}" class="text-right">
					<span class="price-wrapping">{displayWtPriceWithCurrency price=$order->total_wrapping currency=$currency}</span>
				</td>
			</tr>
			{/if}

			{if $order->total_discounts > 0}
				<tr class="item">
					<td colspan={if !$is_guest
						&& isset($order_has_invoice)
						&& $order_has_invoice
						&& ($order->payment != 'Free order' || (isset($order_adv_dtl) && $order_adv_dtl && $order_adv_dtl['total_paid_amount'] > 0))}"8"{else}"5"{/if}></td>
					<td colspan="{if $return_allowed}3{else}3{/if}">
						<strong>{l s='Total vouchers'}</strong>
					</td>
					<td colspan="{if $order->hasProductReturned()}5{else}1{/if}" class="text-right">
						<span class="price-discount">-{displayWtPriceWithCurrency price=$order->total_discounts currency=$currency convert=1}</span>
					</td>
				</tr>
			{/if}
			{if isset($order_adv_dtl) && $order_adv_dtl}
				<tr class="item">
					<td colspan={if !$is_guest
					&& isset($order_has_invoice)
					&& $order_has_invoice
					&& ($order->payment != 'Free order' || (isset($order_adv_dtl) && $order_adv_dtl && $order_adv_dtl['total_paid_amount'] > 0))}"8"{else}"5"{/if}></td>
					<td colspan="{if $return_allowed}2{else}3{/if}">
						<strong>{l s='Advance Paid Amount'}</strong>
					</td>
					<td colspan="{if $order->hasProductReturned()}5{else}1{/if}" class="text-right">
						<span class="price">{displayWtPriceWithCurrency price=$order_adv_dtl['total_paid_amount'] currency=$currency}</span>
					</td>
				</tr>
				<tr class="item">
					<td colspan={if !$is_guest
					&& isset($order_has_invoice)
					&& $order_has_invoice
					&& ($order->payment != 'Free order' || (isset($order_adv_dtl) && $order_adv_dtl && $order_adv_dtl['total_paid_amount'] > 0))}"8"{else}"5"{/if}></td>
					<td colspan="{if $return_allowed}2{else}3{/if}">
						<strong>{l s='Total Due'}</strong>
					</td>
					<td colspan="{if $order->hasProductReturned()}5{else}2{/if}" class="text-right">
						<span class="price">{displayWtPriceWithCurrency price=($order_adv_dtl['total_order_amount'] - $order_adv_dtl['total_paid_amount']) currency=$currency}</span>
					</td>
				</tr>
			{/if}
			<tr class="totalprice item">
				<td colspan={if !$is_guest
					&& isset($order_has_invoice)
					&& $order_has_invoice
					&& ($order->payment != 'Free order' || (isset($order_adv_dtl) && $order_adv_dtl && $order_adv_dtl['total_paid_amount'] > 0))}"8"{else}"5"{/if}></td>
				<td colspan="{if $return_allowed}2{else}3{/if}">
					<strong>{l s='Total Paid'}</strong>
				</td>
				<td colspan="{if $order->hasProductReturned()}2{else}1{/if}" class="text-right">
					<span class="price">{displayWtPriceWithCurrency price=$order->total_paid currency=$currency}</span>
				</td>
			</tr>

			{* <tr class="item">
				<td colspan="{if $return_allowed}2{else}1{/if}">
					<strong>{l s='Shipping & handling'} {if $use_tax}{l s='(tax incl.)'}{/if} </strong>
				</td>
				<td colspan="{if $order->hasProductReturned()}5{else}4{/if}">
					<span class="price-shipping">{displayWtPriceWithCurrency price=$order->total_shipping currency=$currency}</span>
				</td>
			</tr> *}
		</tfoot>
		<tbody>
			{if isset($cart_htl_data)}
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
								{if isset($rm_v['extra_demands']) && $rm_v['extra_demands']}
									<p class="room_extra_demands">
										<a date_from="{$rm_v['data_form']}" date_to="{$rm_v['data_to']}" id_product="{$data_v['id_product']}" id_order="{$order->id}" class="open_rooms_extra_demands" href="#rooms_type_extra_demands">
											{l s='Additional Facilities'}
										</a>
									</p>
									<p>
										{if $group_use_tax}
											{displayWtPriceWithCurrency price=$rm_v['extra_demands_price_ti'] currency=$currency}
										{else}
											{displayWtPriceWithCurrency price=$rm_v['extra_demands_price_te'] currency=$currency}
										{/if}
									</p>
								{/if}
							</td>
							<td>{$data_v['hotel_name']}</td>
							<td>
								<p class="text-left">
									{$data_v['adult']} {l s='Adults'}, {$data_v['children']} {l s='Children'}
								</p>
							</td>
							<td class="cart_unit">
								<p class="text-center">
									{if $group_use_tax}
										<p class="text-center">
											<span class="product_original_price {if $rm_v.feature_price_diff>0}room_type_old_price{/if}" {if $rm_v.feature_price_diff < 0} style="display:none;"{/if}>
												{displayWtPriceWithCurrency price=$rm_v['product_price_without_reduction_tax_incl'] currency=$currency}
												{*{convertPrice price=$data_v.unit_price|floatval}*}
											</span>&nbsp;
						                    <span class="room_type_current_price" {if !$rm_v.feature_price_diff}style="display:none;"{/if}>
						                    	{displayWtPriceWithCurrency price=$rm_v['paid_unit_price_tax_incl'] currency=$currency}
												{*{displayPrice price=$rm_v['feature_price']|floatval|round:2}*}
						                    </span>
										</p>
									{else}
										<p class="text-center">
											<span class="product_original_price {if $rm_v.feature_price_diff>0}room_type_old_price{/if}" {if $rm_v.feature_price_diff < 0} style="display:none;"{/if}>
												{displayWtPriceWithCurrency price=$rm_v['product_price_without_reduction_tax_excl'] currency=$currency}
											</span>&nbsp;
						                    <span class="room_type_current_price" {if !$rm_v.feature_price_diff}style="display:none;"{/if}>
						                    	{displayWtPriceWithCurrency price=$rm_v['paid_unit_price_tax_excl'] currency=$currency}
						                    </span>
										</p>
									{/if}
								</p>
							</td>
							<td class="text-center">
								<p>
									{$rm_v['num_rm']}
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
							<td class="cart_total text-left">
								<p class="text-left">
									{if $group_use_tax}
										{displayWtPriceWithCurrency price=$rm_v['amount_tax_incl'] currency=$currency}
									{else}
										{displayWtPriceWithCurrency price=$rm_v['amount_tax_excl'] currency=$currency}
									{/if}
								</p>
							</td>
							{if !$is_guest
								&& isset($order_has_invoice)
								&& $order_has_invoice
								&& ($order->payment != 'Free order' || (isset($order_adv_dtl) && $order_adv_dtl && $order_adv_dtl['total_paid_amount'] > 0))}
								<td class="cart_total text-left">
									{if isset($rm_v['stage_name']) && $rm_v['stage_name']}
										<p>{l s="Request Sent.."}</p>
									{else}
										<button data-amount="{$rm_v['amount_tax_incl']+$rm_v['extra_demands_price']}" data-id_order="{$order->id}" data-id_currency="{$order->id_currency}" data-id_customer="{$order->id_customer}" data-id_product="{$data_v['id_product']}" data-num_rooms="{$rm_v['num_rm']}" data-date_from="{$rm_v['data_form']|date_format:"%G-%m-%d"}" type="button" data-date_to="{$rm_v['data_to']|date_format:"%G-%m-%d"}"  name="roomRequestForRefund" class="order_cancel_request_button_{$data_v['id_product']}_{$rm_v['data_form']|date_format:"%G-%m-%d"}_{$rm_v['data_to']|date_format:"%G-%m-%d"} btn btn-default button button-small roomRequestForRefund" href="#htlRefundReasonForm"><span>{l s='Request Refund'}</span></button>
									{/if}
								</td>
								<td class="text-center stage_name">
									<p>
										{if isset($rm_v['stage_name']) && $rm_v['stage_name']}
											{$rm_v['stage_name']}
										{else}
											--
										{/if}
									</p>
								</td>
								<td class="text-center status_name">
									<p>
										{if $rm_v['stage_name'] == 'Refunded' || $rm_v['stage_name'] == 'Rejected'}
											{l s="Done!"}
										{else if $rm_v['stage_name'] == 'Waiting' || $rm_v['stage_name'] == 'Accepted'}
											{l s="Pending.."}
										{else}
											--
										{/if}
									</p>
								</td>
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
			{/if}

		{* {foreach from=$products item=product name=products}
			{if !isset($product.deleted)}
				{assign var='productId' value=$product.product_id}
				{assign var='productAttributeId' value=$product.product_attribute_id}
				{if isset($product.customizedDatas)}
					{assign var='productQuantity' value=$product.product_quantity-$product.customizationQuantityTotal}
				{else}
					{assign var='productQuantity' value=$product.product_quantity}
				{/if}
				<!-- Customized products -->
				{if isset($product.customizedDatas)}
					<tr class="item">
						{if $return_allowed}<td class="order_cb"></td>{/if}
						<td><label for="cb_{$product.id_order_detail|intval}">{if $product.product_reference}{$product.product_reference|escape:'html':'UTF-8'}{else}--{/if}</label></td>
						<td class="bold">
							<label for="cb_{$product.id_order_detail|intval}">{$product.product_name|escape:'html':'UTF-8'}</label>
						</td>
						<td>
						<input class="order_qte_input form-control grey"  name="order_qte_input[{$smarty.foreach.products.index}]" type="text" size="2" value="{$product.customizationQuantityTotal|intval}" />
							<div class="clearfix return_quantity_buttons">
								<a href="#" class="return_quantity_down btn btn-default button-minus"><span><i class="icon-minus"></i></span></a>
								<a href="#" class="return_quantity_up btn btn-default button-plus"><span><i class="icon-plus"></i></span></a>
							</div>
							<label for="cb_{$product.id_order_detail|intval}"><span class="order_qte_span editable">{$product.customizationQuantityTotal|intval}</span></label></td>
						{if $order->hasProductReturned()}
							<td>
								{$product['qty_returned']}
							</td>
						{/if}
						<td>
							<label class="price" for="cb_{$product.id_order_detail|intval}">
								{if $group_use_tax}
									{convertPriceWithCurrency price=$product.unit_price_tax_incl currency=$currency}
								{else}
									{convertPriceWithCurrency price=$product.unit_price_tax_excl currency=$currency}
								{/if}
							</label>
						</td>
						<td>
							<label class="price" for="cb_{$product.id_order_detail|intval}">
								{if isset($customizedDatas.$productId.$productAttributeId)}
									{if $group_use_tax}
										{convertPriceWithCurrency price=$product.total_customization_wt currency=$currency}
									{else}
										{convertPriceWithCurrency price=$product.total_customization currency=$currency}
									{/if}
								{else}
									{if $group_use_tax}
										{convertPriceWithCurrency price=$product.total_price_tax_incl currency=$currency}
									{else}
										{convertPriceWithCurrency price=$product.total_price_tax_excl currency=$currency}
									{/if}
								{/if}
							</label>
						</td>
					</tr>
					{foreach $product.customizedDatas  as $customizationPerAddress}
						{foreach $customizationPerAddress as $customizationId => $customization}
						<tr class="alternate_item">
							{if $return_allowed}<td class="order_cb"><input type="checkbox" id="cb_{$product.id_order_detail|intval}" name="customization_ids[{$product.id_order_detail|intval}][]" value="{$customizationId|intval}" /></td>{/if}
							<td colspan="2">
							{foreach from=$customization.datas key='type' item='datas'}
								{if $type == $CUSTOMIZE_FILE}
								<ul class="customizationUploaded">
									{foreach from=$datas item='data'}
										<li><img src="{$pic_dir}{$data.value}_small" alt="" class="customizationUploaded" /></li>
									{/foreach}
								</ul>
								{elseif $type == $CUSTOMIZE_TEXTFIELD}
								<ul class="typedText">{counter start=0 print=false}
									{foreach from=$datas item='data'}
										{assign var='customizationFieldName' value="Text #"|cat:$data.id_customization_field}
										<li>{$data.name|default:$customizationFieldName} : {$data.value}</li>
									{/foreach}
								</ul>
								{/if}
							{/foreach}
							</td>
							<td>
								<input class="order_qte_input form-control grey" name="customization_qty_input[{$customizationId|intval}]" type="text" size="2" value="{$customization.quantity|intval}" />
								<div class="clearfix return_quantity_buttons">
									<a href="#" class="return_quantity_down btn btn-default button-minus"><span><i class="icon-minus"></i></span></a>
									<a href="#" class="return_quantity_up btn btn-default button-plus"><span><i class="icon-plus"></i></span></a>
								</div>
								<label for="cb_{$product.id_order_detail|intval}"><span class="order_qte_span editable">{$customization.quantity|intval}</span></label>
							</td>
							<td colspan="2"></td>
						</tr>
						{/foreach}
					{/foreach}
				{/if}
				<!-- Classic products -->
				{if $product.product_quantity > $product.customizationQuantityTotal}
					<tr class="item">
						{if $return_allowed}<td class="order_cb"><input type="checkbox" id="cb_{$product.id_order_detail|intval}" name="ids_order_detail[{$product.id_order_detail|intval}]" value="{$product.id_order_detail|intval}" /></td>{/if}
						<td><label for="cb_{$product.id_order_detail|intval}">{if $product.product_reference}{$product.product_reference|escape:'html':'UTF-8'}{else}--{/if}</label></td>
						<td class="bold">
							<label for="cb_{$product.id_order_detail|intval}">
								{if $product.download_hash && $logable && $product.display_filename != '' && $product.product_quantity_refunded == 0 && $product.product_quantity_return == 0}
									{if isset($is_guest) && $is_guest}
									<a href="{$link->getPageLink('get-file', true, NULL, "key={$product.filename|escape:'html':'UTF-8'}-{$product.download_hash|escape:'html':'UTF-8'}&amp;id_order={$order->id}&secure_key={$order->secure_key}")|escape:'html':'UTF-8'}" title="{l s='Download this product'}">
									{else}
										<a href="{$link->getPageLink('get-file', true, NULL, "key={$product.filename|escape:'html':'UTF-8'}-{$product.download_hash|escape:'html':'UTF-8'}")|escape:'html':'UTF-8'}" title="{l s='Download this product'}">
									{/if}
										<img src="{$img_dir}icon/download_product.gif" class="icon" alt="{l s='Download product'}" />
									</a>
									{if isset($is_guest) && $is_guest}
										<a href="{$link->getPageLink('get-file', true, NULL, "key={$product.filename|escape:'html':'UTF-8'}-{$product.download_hash|escape:'html':'UTF-8'}&id_order={$order->id}&secure_key={$order->secure_key}")|escape:'html':'UTF-8'}" title="{l s='Download this product'}"> {$product.product_name|escape:'html':'UTF-8'} 	</a>
									{else}
									<a href="{$link->getPageLink('get-file', true, NULL, "key={$product.filename|escape:'html':'UTF-8'}-{$product.download_hash|escape:'html':'UTF-8'}")|escape:'html':'UTF-8'}" title="{l s='Download this product'}"> {$product.product_name|escape:'html':'UTF-8'} 	</a>
									{/if}
								{else}
									{$product.product_name|escape:'html':'UTF-8'}
								{/if}
							</label>
						</td>
						<td class="return_quantity">
							<input class="order_qte_input form-control grey" name="order_qte_input[{$product.id_order_detail|intval}]" type="text" size="2" value="{$productQuantity|intval}" />
							<div class="clearfix return_quantity_buttons">
								<a href="#" class="return_quantity_down btn btn-default button-minus"><span><i class="icon-minus"></i></span></a>
								<a href="#" class="return_quantity_up btn btn-default button-plus"><span><i class="icon-plus"></i></span></a>
							</div>
							<label for="cb_{$product.id_order_detail|intval}"><span class="order_qte_span editable">{$productQuantity|intval}</span></label></td>
						{if $order->hasProductReturned()}
							<td>
								{$product['qty_returned']}
							</td>
						{/if}
						<td class="price">
							<label for="cb_{$product.id_order_detail|intval}">
							{if $group_use_tax}
								{convertPriceWithCurrency price=$product.unit_price_tax_incl currency=$currency}
							{else}
								{convertPriceWithCurrency price=$product.unit_price_tax_excl currency=$currency}
							{/if}
							</label>
						</td>
						<td class="price">
							<label for="cb_{$product.id_order_detail|intval}">
							{if $group_use_tax}
								{convertPriceWithCurrency price=$product.total_price_tax_incl currency=$currency}
							{else}
								{convertPriceWithCurrency price=$product.total_price_tax_excl currency=$currency}
							{/if}
							</label>
						</td>
					</tr>
				{/if}
			{/if}
		{/foreach} *}
		{foreach from=$discounts item=discount}
			<tr class="item">
				<td class="text-center">{$discount.name|escape:'html':'UTF-8'}</td>
				<td class="text-center">{l s='Voucher'} {$discount.name|escape:'html':'UTF-8'}</td>
				<td class="text-center" colspan="2">&nbsp;</td>
				<td class="text-center"><span class="order_qte_span editable">1</span></td>
				<td class="text-center" colspan="3">&nbsp;</td>
				<td class="text-center">{if $discount.value != 0.00}-{/if}{convertPriceWithCurrency price=$discount.value currency=$currency}</td>
				{if $return_allowed}
				<td>&nbsp;</td>
				{/if}
			</tr>
		{/foreach}
		</tbody>
	</table>
</div>
{if $any_back_order}
	{if $shw_bo_msg}
		<p class="back_o_msg"><strong><sup>*</sup>{l s='Some of your rooms are on back order. Please read the following message for rooms with status on backorder'}</strong></p>
		<p>
			-&nbsp;&nbsp;{$back_ord_msg}
		</p>
	{/if}
{/if}
{if $return_allowed}
	<div id="returnOrderMessage">
		<h3 class="page-heading bottom-indent">{l s='Merchandise return'}</h3>
		<p>{l s='If you wish to return one or more products, please mark the corresponding boxes and provide an explanation for the return. When complete, click the button below.'}</p>
		<p class="form-group">
			<textarea class="form-control" cols="67" rows="3" name="returnText"></textarea>
		</p>
		<p class="form-group">
			<button type="submit" name="submitReturnMerchandise" class="btn btn-default button button-small"><span>{l s='Make an RMA slip'}<i class="icon-chevron-right right"></i></span></button>
			<input type="hidden" class="hidden" value="{$order->id|intval}" name="id_order" />
		</p>
	</div>
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
					<option value="{$product.product_id}">{$product.product_name}</option>
				{/foreach}
			</select>
		</p>
		<p class="form-group">
			<textarea class="form-control" cols="67" rows="3" name="msgText"></textarea>
		</p>
		<div class="submit">
			<input type="hidden" name="id_order" value="{$order->id|intval}" />
			<input type="submit" class="unvisible" name="submitMessage" value="{l s='Send'}"/>
			<button type="submit" name="submitMessage" class="button btn btn-default button-medium"><span>{l s='Send'}<i class="icon-chevron-right right"></i></span></button>
		</div>
	</form>
{else}
<p class="alert alert-info"><i class="icon-info-sign"></i> {l s='You cannot request refund with a guest account'}</p>
{/if}
{/if}


{* Fancybox for extra demands*}
<div style="display:none;" id="rooms_extra_demands">
	<div id="rooms_type_extra_demands">
		<div class="panel">
			<div class="rooms_extra_demands_head">
				<h3>{l s='Additional Facilities'}</h3>
				<p class="rooms_extra_demands_text">{l s='Below are the additional facilities chosen by you in this booking'}</p>
			</div>
			<div id="room_type_demands_desc"></div>
		</div>
	</div>
</div>

{* Fancybox *}
<div style="display: none;" id="reason_fancybox_content">
	<div id="htlRefundReasonForm">
		<h2 class="refund_reason_head">
			{l s='Write a reason For order cancellation'}
		</h2>
		<div>
			<div class="refundReasonFormContent col-sm-12 col-xs-12">
				<input type="hidden" value="" id="cancel_req_total_order_data">
				<input type="hidden" value="" id="cancel_req_id_room">
				<input type="hidden" value="" id="cancel_req_amount">
				<input type="hidden" value="" id="cancel_req_id_order">
				<input type="hidden" value="" id="cancel_req_id_currency">
				<input type="hidden" value="" id="cancel_req_id_customer">
				<input type="hidden" value="" id="cancel_req_id_product">
				<input type="hidden" value="" id="cancel_req_num_rooms">
				<input type="hidden" value="" id="cancel_req_date_from">
				<input type="hidden" value="" id="cancel_req_date_to">
				<textarea class="form-control reasonForRefund" rows="2" name="reasonForRefund" placeholder="{l s='Write a reason for cancellation of this booking'}"></textarea>
				<div>
					<p class="fl required required_err" style="color:#AA1F00; display:none"><sup>*</sup> {l s='Required field'}</p><br>
					<p class="fr">
						<button id="submit_refund_reason" name="submit_refund_reason" type="submit" class="btn button button-medium">
							<span>{l s='Submit'}</span>
						</button>&nbsp;
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
