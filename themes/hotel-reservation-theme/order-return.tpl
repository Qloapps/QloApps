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

{block name='order_return'}
	{capture name=path}
		<li class="breadcrumb-item"><a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account'}</a></li>
		<li class="breadcrumb-item"><a href="{$link->getPageLink('order-follow', true)|escape:'html':'UTF-8'}">{l s='Booking refund requests'}</a></li>
		<li class="breadcrumb-item"><span class="navigation_page">{l s='Refund details'}</span></li>
	{/capture}

	{block name='errors'}
		{include file="./errors.tpl"}
	{/block}
	<div class="container-md qlo-mobile-reponsive-table">
		<div class="d-flex justify-content-center">
			<div class="qlo-account-card card col-12 p-5 mb-md-4">
				{block name='order_return_heading'}
					<h1 class="page-heading text-center text-bolder h5 font-weight-bold mb-4">{l s='Booking Refund Requests'}</h1>
				{/block}
				{block name='order_return_detail'}
					{if $refundReqBookings}
						<div class="table-responsive wk-datatable-wrapper mb-4">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>{l s='Room type'}</th>
										<th>{l s='Hotel'}</th>
										<th>{l s='Duration'}</th>
										<th>{l s='Num rooms'}</th>
										<th>{l s='Extra services price (tax incl.)'}</th>
										{if $isRefundCompleted}
											<th>{l s='Refund amount'}</th>
										{/if}
									</tr>
								</thead>
								<tbody>
									{foreach from=$refundReqBookings item=$booking}
										<tr>
											<td data-label="{l s='Room type'}">{$booking['room_type_name']|escape:'htmlall':'UTF-8'}</td>
											<td data-label="{l s='Hotel'}">{$booking['hotel_name']|escape:'htmlall':'UTF-8'}</td>
											{assign var="is_full_date" value=($show_full_date && ($booking['date_from']|date_format:'%D' == $booking['date_to']|date_format:'%D'))}
											<td data-label="{l s='Duration'}">{dateFormat date=$booking['date_from'] full=$is_full_date} {l s='To'} {dateFormat date=$booking['date_to'] full=$is_full_date}</td>
											<td data-label="{l s='Num rooms'}">{if isset($booking['num_rooms'])}{$booking['num_rooms']|intval}{else}1{/if}</td>
											<td data-label="{l s='Extra services price (tax incl.)'}">{displayPrice price=$booking['extra_service_total_price_tax_incl'] currency=$orderCurrency['id']}</td>
											{if $isRefundCompleted}
												<td data-label="{l s='Refund amount'}">{displayPrice price=$booking['refunded_amount'] currency=$orderCurrency['id']}</td>
											{/if}
										</tr>
									{/foreach}
								</tbody>
							</table>
						</div>
					{/if}
					{if $refundReqProducts}
						<div class="table-responsive wk-datatable-wrapper mb-4">
							<table class="table table-borderd">
								<thead>
									<tr>
										<th>{l s='Product name'}</th>
										<th>{l s='Quantity'}</th>
										<th>{l s='Total price (tax incl.)'}</th>
										{if $isRefundCompleted}
											<th>{l s='Refund amount'}</th>
										{/if}
									</tr>
								</thead>
								<tbody>
									{foreach from=$refundReqProducts item=$product}
										<tr>
											<td data-label="{l s='Product name'}">{$product['name']|escape:'htmlall':'UTF-8'}{if isset($product['option_name']) && $product['option_name']} : {$product['option_name']}{/if}</td>
											<td data-label="{l s='Quantity'}">{if $product['allow_multiple_quantity']}{$product['quantity']|escape:'htmlall':'UTF-8'}{else}--{/if}</td>
											<td data-label="{l s='Total price (tax incl.)'}">{displayPrice price=$product['total_price_tax_incl'] currency=$orderCurrency['id']}</td>
											{if $isRefundCompleted}
												<td data-label="{l s='Refund amount'}">{displayPrice price=$product['refunded_amount'] currency=$orderCurrency['id']}</td>
											{/if}
										</tr>
									{/foreach}
								</tbody>
							</table>
						</div>
					{/if}
					{if !$refundReqBookings && !$refundReqProducts}
						<div class="alert alert-warning mb-4">{l s='No refund request details found.'}</div>
					{/if}
				{/block}

				{block name='order_return_current_status'}
					<div class="form-group row">
						<div class="col-md-3 col-6">
							<strong>{l s='Current refunded state'}</strong>
						</div>
						<div class="col-md-9 col-6">
							<span class="badge wk-badge"{if isset($currentStateInfo['color']) && $currentStateInfo['color']} style="background-color:{$currentStateInfo['color']|escape:'html':'UTF-8'}"{/if}>
								{$currentStateInfo['name']|escape:'html':'UTF-8'}
							</span>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-3 col-6">
							<strong>{l s='Request date'}</strong>
						</div>
						<div class="col-md-9 col-6">
							{$orderReturnInfo['date_add']|date_format:"%d-%m-%Y %I:%M %p"}
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-3 col-6">
							<strong>{l s='Way of payment'}</strong>
						</div>
						<div class="col-md-9 col-6">
							{if $orderInfo['is_advance_payment']}{l s='Advance Payment'}{else}{l s='Full Payment'}{/if}
						</div>
					</div>
					{if $currentStateInfo['refunded']}
						<div class="form-group row">
							<div class="col-md-3 col-6">
								<strong>{l s='Refunded amount' mod='hotelreservationsystem'}</strong>
							</div>
							<div class="col-md-9 col-6">
								{displayPrice price=$orderReturnInfo['refunded_amount'] currency=$orderInfo['id_currency']}
							</div>
						</div>
					{/if}
					<div class="form-group row">
						<div class="col-md-3 col-6">
							<strong>{l s='Total order amount'}</strong>
						</div>
						<div class="col-md-9 col-6">
							{displayPrice price=$orderInfo['total_paid_tax_incl'] currency=$orderInfo['id_currency']}
						</div>
					</div>
					{if isset($orderReturnInfo['return_type'])}
						{if $orderReturnInfo['return_type'] == OrderReturn::RETURN_TYPE_CART_RULE}
							<div class="form-group row">
								<div class="col-md-3 col-6">
									<strong>{l s='Voucher' mod='hotelreservationsystem'}</strong>
								</div>
								<div class="col-md-9 col-6">
									<a class="link" href="{$link->getPageLink('discount')}" target="_blank">
										{$voucher|escape:'html':'UTF-8'}
									</a>
								</div>
							</div>
						{elseif $orderReturnInfo['return_type'] == OrderReturn::RETURN_TYPE_ORDER_SLIP}
							<div class="form-group row">
								<div class="col-md-3 col-6">
									<strong>{l s='Credit Slip' mod='hotelreservationsystem'}</strong>
								</div>
								<div class="col-md-9 col-6">
									<a class="link" href="{$link->getPageLink('order-slip')}" target="_blank">
										#{Configuration::get('PS_CREDIT_SLIP_PREFIX', $lang_id)}{$orderReturnInfo['id_return_type']|string_format:"%06d"}
									</a>
								</div>
							</div>
						{/if}
					{/if}
					{if $orderReturnInfo['payment_mode'] != '' && $orderReturnInfo['id_transaction'] != ''}
						<div class="form-group row">
							<div class="col-md-3 col-6">
								<strong>{l s='Payment mode' mod='hotelreservationsystem'}</strong>
							</div>
							<div class="col-md-9 col-6">
								{$orderReturnInfo['payment_mode']|escape:'html':'UTF-8'}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-3 col-6">
								<strong>{l s='Transaction ID' mod='hotelreservationsystem'}</strong>
							</div>
							<div class="col-md-9 col-6">
								{$orderReturnInfo['id_transaction']|escape:'html':'UTF-8'}
							</div>
						</div>
					{/if}
				{/block}
			</div>
		</div>
		{block name='order_return_footer_links'}
			<ul class="footer_links clearfix">
				<li><a class="link text-secondary" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"><span><i class="icon-angles-left"></i> {l s='Back to your account'}</span></a></li>
			</ul>
		{/block}
	</div>

{/block}
