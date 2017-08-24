{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
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
			{*{if isset($orders_has_invoice) && $orders_has_invoice && $order->payment != 'Free order'}
				<div class="row totalOrdercancellation_div" {if !$non_requested_rooms}style="display:none;"{/if}>
					<div class="col-xs-12 col-sm-12">
						<p style="text-align:center;"><a class="terms_btn btn btn-default pull-right" href="{$redirect_link_terms}" target="_blank"><i class="icon-file-text large"></i>&nbsp;&nbsp;{l s='Terms & Conditions'}</a></p>
						<button type="button" data-id_order="{$order->id}" data-id_currency="{$order->id_currency}" data-id_customer="{$order->id_customer}" data-order_data='{$cart_htl_data|@json_encode}' name="totalOrdercancellation_btn" class="totalOrdercancellation_btn btn btn-default pull-right" href="#htlRefundReasonForm"><span>{l s='Request Total Order Cancellation'}</span></button>
					</div>
				</div>
			{/if}*}
			<div id="order-detail-content" class="">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="cart_product">{l s='Room Image'}</th>
							<th class="cart_description">{l s='Room Description'}</th>
							<th class="cart_unit">{l s='Unit Price'}</th>
							<th>{l s='Rooms'}</th>
							<th>{l s='Check-in Date'}</th>
							<th>{l s='Check-out Date'}</th>
							<th class="cart_total">{l s='Total'}</th>
							{*{if isset($orders_has_invoice) && $orders_has_invoice}
								<th>{l s='Request Refund'}</th>
							{/if}*}	
							<th>{l s='BackOrder Status'}</th>
						</tr>
					</thead>
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
														<span class="product_original_price {if $rm_v.feature_price_diff > 0}room_type_old_price{/if}" {if $rm_v.feature_price_diff < 0} style="display:none;"{/if}>
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
										{if isset($orders_has_invoice) && $orders_has_invoice && $order->payment != 'Free order'}
											{*<td class="cart_total text-left">
												{if isset($rm_v['stage_name']) && $rm_v['stage_name']}
													<p>{l s="Request Sent.."}</p>
												{else}
													<button data-amount="{$rm_v['amount_tax_incl']}" data-id_order="{$order->id}" data-id_currency="{$order->id_currency}" data-id_customer="{$order->id_customer}" data-id_product="{$data_v['id_product']}" data-num_rooms="{$rm_v['num_rm']}" data-date_from="{$rm_v['data_form']|date_format:"%G-%m-%d"}" type="button" data-date_to="{$rm_v['data_to']|date_format:"%G-%m-%d"}"  name="roomRequestForRefund" class="order_cancel_request_button_{$data_v['id_product']}_{$rm_v['data_form']|date_format:"%G-%m-%d"}_{$rm_v['data_to']|date_format:"%G-%m-%d"} btn btn-default button button-small roomRequestForRefund" href="#htlRefundReasonForm"><span>{l s='Request Refund'}</span></button>
												{/if}
											</td>*}
										{/if}	
										<td class="text-center">
											{if isset($rm_v['is_backorder']) && $rm_v['is_backorder']}
												{l s='On Backorder'}
											{else}
												--
											{/if}
										</td>
									</tr>
								{/foreach}
							{/foreach}
						{/if}
					</tbody>
					<tfoot>
						{if $priceDisplay && $use_tax}
							<tr class="item">
								<td colspan={if isset($orders_has_invoice) && $orders_has_invoice}"7"{else}"6"{/if}></td>
								<td colspan="{if $return_allowed}2{else}1{/if}">
									<strong>{l s='Items (tax excl.)'}</strong>
								</td>
								<td colspan="{if $order->hasProductReturned()}3{else}2{/if}">
									<span class="price">{displayWtPriceWithCurrency price=$orderTotalInfo['total_products_te'] currency=$currency}</span>
								</td>
							</tr>
						{/if}
						<tr class="item">
							<td colspan={if isset($orders_has_invoice) && $orders_has_invoice}"7"{else}"6"{/if}></td>
							<td colspan="{if $return_allowed}2{else}1{/if}">
								<strong>{l s='Items'} {if $use_tax}{l s='(tax incl.)'}{/if} </strong>
							</td>
							<td colspan="{if $order->hasProductReturned()}3{else}2{/if}">
								<span class="price">{displayWtPriceWithCurrency price=$orderTotalInfo['total_products_ti'] currency=$currency}</span>
							</td>
						</tr>
						{if $order->total_discounts > 0}
						<tr class="item">
							<td colspan={if isset($orders_has_invoice) && $orders_has_invoice}"7"{else}"6"{/if}></td>
							<td colspan="{if $return_allowed}2{else}1{/if}">
								<strong>{l s='Total vouchers'}</strong>
							</td>
							<td colspan="{if $order->hasProductReturned()}3{else}2{/if}">
								<span class="price-discount">{displayWtPriceWithCurrency price=$orderTotalInfo['total_discounts'] currency=$currency convert=1}</span>
							</td>
						</tr>
						{/if}
						{if $order->total_wrapping > 0}
						<tr class="item">
							<td colspan={if isset($orders_has_invoice) && $orders_has_invoice}"7"{else}"6"{/if}></td>
							<td colspan="{if $return_allowed}2{else}1{/if}">
								<strong>{l s='Total gift wrapping cost'}</strong>
							</td>
							<td colspan="{if $order->hasProductReturned()}3{else}2{/if}">
								<span class="price-wrapping">{displayWtPriceWithCurrency price=$orderTotalInfo['total_wrapping'] currency=$currency}</span>
							</td>
						</tr>
						{/if}
						<tr class="totalprice item">
							<td colspan={if isset($orders_has_invoice) && $orders_has_invoice}"7"{else}"6"{/if}></td>
							<td colspan="{if $return_allowed}2{else}1{/if}">
								<strong>{l s='Total'}</strong>
							</td>
							<td colspan="{if $order->hasProductReturned()}3{else}2{/if}">
								<span class="price">{displayWtPriceWithCurrency price=$orderTotalInfo['total_paid'] currency=$currency}</span>
							</td>
						</tr>
						{if isset($order_adv_dtl)}
							<tr class="item">
								<td colspan={if isset($orders_has_invoice) && $orders_has_invoice}"7"{else}"6"{/if}></td>
								<td colspan="{if $return_allowed}2{else}1{/if}">
									<strong>{l s='Total Paid'}</strong>
								</td>
								<td colspan="{if $order->hasProductReturned()}3{else}2{/if}">
									<span class="price">{displayWtPriceWithCurrency price=$orderTotalInfo['total_paid_amount'] currency=$currency}</span>
								</td>
							</tr>
							<tr class="item">
								<td colspan={if isset($orders_has_invoice) && $orders_has_invoice}"7"{else}"6"{/if}></td>
								<td colspan="{if $return_allowed}2{else}1{/if}">
									<strong>{l s='Total Due'}</strong>
								</td>
								<td colspan="{if $order->hasProductReturned()}3{else}2{/if}">
									<span class="price">{displayWtPriceWithCurrency price=($orderTotalInfo['total_order_amount'] - $orderTotalInfo['total_paid_amount']) currency=$currency}</span>
								</td>
							</tr>
						{/if}
					</tfoot>
				</table>
			</div>
			<p>{l s='An email has been sent with this information.' mod='bankwire'}
				<br /><strong>{l s='Your order will be sent as soon as we receive payment.' mod='bankwire'}</strong>
				<br />{l s='If you have questions, comments or concerns, please contact our' mod='bankwire'} <a class="cust_serv_lnk" href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='expert customer support team' mod='bankwire'}</a>
			</p>
		</div>
	{/if}
	<p class="cart_navigation exclusive">
		<a class="btn htl-reservation-form-btn-small" href="{$link->getPageLink('history', true)|escape:'html':'UTF-8'}" title="{l s='Go to your order history page'}"><i class="icon-chevron-left"></i>{l s='View your order history'}</a>
	</p>
{/if}


<!-- Fancybox -->
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
				<textarea class="form-control reasonForRefund" rows="2" name="reasonForRefund" placeholder="Write a reason for cancellation of this booking"></textarea>
				<div>
					<p class="fl required required_err" style="color:#AA1F00; display:none"><sup>*</sup> {l s='Required field'}</p><br>
					<p class="fr">
						<button id="submit_refund_reason" name="submit_refund_reason" type="submit" class="btn button button-medium">
							<span>{l s='Submit' mod='marketplace'}</span>
						</button>&nbsp;
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
{strip}
	{addJsDef historyUrl=$link->getPageLink("orderdetail", true)|escape:'quotes':'UTF-8'}
	{addJsDefL name=req_sent_msg}{l s='Request Sent..' js=1}{/addJsDefL}
	{addJsDefL name=wait_stage_msg}{l s='Waitting' js=1}{/addJsDefL}
	{addJsDefL name=pending_state_msg}{l s='Pending...' js=1}{/addJsDefL}
	{addJsDefL name=mail_sending_err}{l s='Some error occurred while sending mail to the customer' js=1}{/addJsDefL}
	{addJsDefL name=refund_request_sending_error}{l s='Some error occurred while processing request for order cancellation.' js=1}{/addJsDefL}
{/strip}