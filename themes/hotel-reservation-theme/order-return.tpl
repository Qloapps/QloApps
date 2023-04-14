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

{capture name=path}
	<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
		{l s='My account'}
	</a>
	<span class="navigation-pipe">
		{$navigationPipe}
	</span>
	<a href="{$link->getPageLink('order-follow', true)|escape:'html':'UTF-8'}">
		{l s='Booking refund requests'}
	</a>
	<span class="navigation-pipe">
		{$navigationPipe}
	</span>
	<span class="navigation_page">
		{l s='Booking refund detail'}
	</span>
{/capture}

{include file="./errors.tpl"}
<div class="panel card">
	<h1 class="page-heading bottom-indent">
		<i class="icon-tasks"></i> &nbsp;{l s='Booking Refund Requests'}
	</h1>
	<div class="table-responsive wk-datatable-wrapper">
		<table class="table table-bordered">
			<tr>
				<th>{l s='Room type'}</th>
				<th>{l s='Hotel'}</th>
				<th>{l s='Duration'}</th>
				<th>{l s='Num rooms'}</th>
				<th>{l s='Total rooms price (tax incl.)'}</th>
				<th>{l s='Extra services price (tax incl.)'}</th>
				{if $isRefundCompleted}
					<th>{l s='Refund amount'}</th>
				{/if}
			</tr>

			{foreach $refundReqBookings as $booking}
				<tr>
					<td>{$booking['room_type_name']|escape:'htmlall':'UTF-8'}</td>
					<td>{$booking['hotel_name']|escape:'htmlall':'UTF-8'}</td>
					<td>{$booking['date_from']|date_format:"%d-%m-%Y"} {l s='To'} {$booking['date_to']|date_format:"%d-%m-%Y"}</td>
					<td>{$booking['num_rooms']|escape:'htmlall':'UTF-8'}</td>
					<td>{displayPrice price=$booking['total_price_tax_incl'] currency=$orderCurrency['id']}</td>
					<td>{displayPrice price=$booking['extra_service_total_price_tax_incl'] currency=$orderCurrency['id']}</td>
					{if $isRefundCompleted}
						<td>
							{displayPrice price=$booking['refunded_amount'] currency=$orderCurrency['id']}
						</td>
					{/if}
				</tr>
			{/foreach}
		</table>
	</div>

	<div class="form-group row">
		<div class="col-md-2 col-sm-3">
			<strong>{l s='Current refund state'} </strong>
		</div>
		<div class="col-sm-9 col-md-10">
			<span class="badge wk-badge" style="background-color:{$currentStateInfo['color']|escape:'html':'UTF-8'}">
				{if $isCanceled}
					{l s='Cancelled'}
				{else}
					{$currentStateInfo['name']|escape:'html':'UTF-8'}
				{/if}
			</span>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-md-2 col-sm-3">
			<strong>{l s='Way of payment'} </strong>
		</div>
		<div class="col-sm-9 col-md-10">
			{if $orderInfo['is_advance_payment']}{l s='Advance Payment'}{else}{l s='Full Payment'}{/if}
		</div>
	</div>
	<div class="form-group row">
		<div class="col-md-2 col-sm-3">
			<strong>{l s='Total order amount'} </strong>
		</div>
		<div class="col-sm-9 col-md-10">
			{displayPrice price=$orderInfo['total_paid_tax_incl'] currency=$orderInfo['id_currency']}
		</div>
	</div>
	<div class="form-group row">
		<div class="col-md-2 col-sm-3">
			<strong>{l s='Request date'} </strong>
		</div>
		<div class="col-sm-9 col-md-10">
			{$orderReturnInfo['date_add']|date_format:"%d-%m-%Y %I:%M %p"}
		</div>
	</div>

	{if $currentStateInfo['refunded']}
		<div class="form-group row">
			<div class="col-md-2 col-sm-3">
				<strong>{l s='Refunded amount' mod='hotelreservationsystem'}</strong>
			</div>
			<div class="col-sm-9 col-md-10">
				{displayPrice price=$orderReturnInfo['refunded_amount'] currency=$orderInfo['id_currency']}
			</div>
		</div>
		{if $orderReturnInfo['payment_mode'] != '' && $orderReturnInfo['id_transaction'] != ''}
			<div class="form-group row">
				<div class="col-md-2 col-sm-3">
					<strong>{l s='Payment mode' mod='hotelreservationsystem'}</strong>
				</div>
				<div class="col-sm-9 col-md-10">
					{$orderReturnInfo['payment_mode']|escape:'html':'UTF-8'}
				</div>
			</div>
			<div class="form-group row">
				<div class="col-md-2 col-sm-3">
					<strong>{l s='Transaction ID' mod='hotelreservationsystem'}</strong>
				</div>
				<div class="col-sm-9 col-md-10">
					{$orderReturnInfo['id_transaction']|escape:'html':'UTF-8'}
				</div>
			</div>
		{/if}
		{if isset($orderReturnInfo['return_type'])}
			{if $orderReturnInfo['return_type'] == OrderReturn::RETURN_TYPE_CART_RULE}
				<div class="form-group row">
					<div class="col-md-2 col-sm-3">
						<strong>{l s='Voucher' mod='hotelreservationsystem'}</strong>
					</div>
					<div class="col-sm-9 col-md-10">
						<a class="link" href="{$link->getPageLink('discount')}" target="_blank">
							{$voucher|escape:'html':'UTF-8'}
						</a>
					</div>
				</div>
			{elseif $orderReturnInfo['return_type'] == OrderReturn::RETURN_TYPE_ORDER_SLIP}
				<div class="form-group row">
					<div class="col-md-2 col-sm-3">
						<strong>{l s='Credit Slip:' mod='hotelreservationsystem'}</strong>
					</div>
					<div class="col-sm-9 col-md-10">
						<a class="btn btn-default btn-sm" href="{$link->getPageLink('order-slip')}">
							{l s='View your creadit slips' mod='hotelreservationsystem'}
						</a>
					</div>
				</div>
			{/if}
		{/if}
	{/if}
</div>

<ul class="footer_links clearfix">
	<li><a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"><span><i class="icon-chevron-left"></i> {l s='Back to your account'}</span></a></li>
	<li><a class="btn btn-default button button-small" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}"><span><i class="icon-chevron-left"></i> {l s='Home'}</span></a></li>
</ul>