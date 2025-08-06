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
<div style="font-size: 8pt; color: #444">

<table>
	<tr><td>&nbsp;</td></tr>
</table>

<!-- ADDRESSES -->
<table style="width: 100%">
	<tr>
		<td style="width: 15%"></td>
		<td style="width: 85%">
			{if !empty($delivery_address)}
				<table style="width: 100%">
					<tr>
						<td style="width: 50%">
							<span style="font-weight: bold; font-size: 10pt; color: #9E9F9E">{l s='Delivery Address' pdf='true'}</span><br />
							 {$delivery_address}
						</td>
						<td style="width: 50%">
							<span style="font-weight: bold; font-size: 10pt; color: #9E9F9E">{l s='Billing Address' pdf='true'}</span><br />
							 {$invoice_address}
						</td>
					</tr>
				</table>
			{else}
				<table style="width: 100%">
					<tr>
						<td style="width: 50%">
							<span style="font-weight: bold; font-size: 10pt; color: #9E9F9E">{l s='Billing & Delivery Address.' pdf='true'}</span><br />
							 {$invoice_address}
						</td>
						<td style="width: 50%">

						</td>
					</tr>
				</table>
			{/if}
		</td>
	</tr>
</table>
<!-- / ADDRESSES -->

<div style="line-height: 1pt">&nbsp;</div>

{if $customer->siret or $customer->ape}
<table style="width: 100%">
	<tr>
		<td style="width: 15%"></td>
		<td style="width: 85%">
		{if $customer->siret}
		<b>{l s='Company:' pdf='true'}</b> {$customer->company}
		{/if}
		{if $customer->siret}
		<br />
		<b>{l s='SIRET:' pdf='true'}</b> {$customer->siret}
		{/if}
		{if $customer->ape}
		<br />
		<b>{l s='APE:' pdf='true'}</b> {$customer->ape}
		{/if}
		</td>
		<td style="width: 15%"></td>
	</tr>
</table>
<!-- / B2B -->
{/if}

<div style="line-height: 1pt">&nbsp;</div>

<!-- PRODUCTS TAB -->
<table style="width: 100%">
	<tr>
		<td style="width: 15%; padding-right: 7px; text-align: right; vertical-align: top; font-size: 7pt;">
			<!-- CUSTOMER INFORMATIONS -->
			<b>{l s='Order Number:' pdf='true'}</b><br />
			{$order->getUniqReference()}<br />
			<br />
			<b>{l s='Order Date:' pdf='true'}</b><br />
			{dateFormat date=$order->date_add full=0}<br />
			<br />
			<b>{l s='Payment Method:' pdf='true'}</b><br />
			<table style="width: 100%;">
			{foreach from=$order_invoice->getOrderPaymentCollection() item=payment}
				<tr>
					<td style="width: 50%">{$payment->payment_method}</td>
					<td style="width: 50%">{displayPrice price=$payment->amount currency=$order->id_currency}</td>
				</tr>
			{foreachelse}
				<tr>
					<td>{l s='No payment' pdf='true'}</td>
				</tr>
			{/foreach}
			</table>
			<br />
			<!-- / CUSTOMER INFORMATIONS -->
		</td>
		<td style="width: 85%; text-align: right">
			<table style="width: 100%; font-size: 8pt;">
				<tr style="line-height:4px;">
					{$product_reference_width = 45}
					{if Configuration::get('PS_PDF_IMG_INVOICE')}
						{$product_reference_width = $product_reference_width - 10}
						<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold; width: 10%">{l s='Image' pdf='true'}</td>
					{/if}
					{if !$tax_excluded_display}
						{$product_reference_width = $product_reference_width - 10}
					{/if}
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold; width: {$product_reference_width}%">{l s='Product / Reference' pdf='true'}</td>
					<!-- unit price tax excluded is mandatory -->
					{if !$tax_excluded_display}
						<td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 10%">{l s='Unit Price' pdf='true'} <br />{l s='(Tax Excl.)' pdf='true'}</td>
					{/if}
					<td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 10%">{l s='Unit Price' pdf='true'}</td>
					<td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 10%">{l s='Discount' pdf='true'}</td>
					<td style="background-color: #4D4D4D; color: #FFF; text-align: center; font-weight: bold; width: 10%">{l s='Qty' pdf='true'}</td>
					<td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 15%">{l s='Total' pdf='true'}</td>
				</tr>
				{foreach $order_details as $order_detail}
				{cycle values='#FFF,#DDD' assign=bgcolor}
				<tr style="line-height:6px;background-color:{$bgcolor};" {if Configuration::get('PS_PDF_IMG_INVOICE') && isset($order_detail.image) && $order_detail.image->id && isset($order_detail.image_size)}height="{$order_detail['image_size'][1]}"{/if}>
					{if Configuration::get('PS_PDF_IMG_INVOICE')}
						<td style="text-align: left;">{if isset($order_detail.image) && $order_detail.image->id}{$order_detail.image_tag}{/if}</td>
					{/if}
					<td style="text-align: left; width: {$product_reference_width}%">{$order_detail.product_name}{if isset($order_detail.product_reference) && !empty($order_detail.product_reference)} ({l s='Reference:' pdf='true'} {$order_detail.product_reference}){/if}</td>
					<!-- unit price tax excluded is mandatory -->
					{if !$tax_excluded_display}
						<td style="text-align: right; width: 10%">
						{displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl}
						</td>
					{/if}
					<td style="text-align: right; width: 10%">
					{if $tax_excluded_display}
						{displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl}
					{else}
						{displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_incl}
					{/if}
					</td>
					<td style="text-align: right; width: 10%">
					{if (isset($order_detail.reduction_amount) && $order_detail.reduction_amount > 0)}
						-{displayPrice currency=$order->id_currency price=$order_detail.reduction_amount}
					{elseif (isset($order_detail.reduction_percent) && $order_detail.reduction_percent > 0)}
						-{$order_detail.reduction_percent}%
					{else}
					--
					{/if}
					</td>
					<td style="text-align: center; width: 10%">{$order_detail.product_quantity}</td>
					<td style="width: 15%; text-align: right;  width: 15%">
					{if $tax_excluded_display}
						{displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_excl}
					{else}
						{displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_incl}
					{/if}
					</td>
				</tr>
					{foreach $order_detail.customizedDatas as $customization}
						<tr style="line-height:6px;background-color:{$bgcolor}; ">
							<td style="line-height:3px; text-align: left; width: 60%; vertical-align: top">
								{foreach $customization.datas as $customization_types}
									<blockquote>
									{foreach $customization_types as $customization_infos name=custo_foreach}
										{$customization_infos.name}: {$customization_infos.value}
										{if !$smarty.foreach.custo_foreach.last}<br />
										{else}
										<div style="line-height:0.4pt">&nbsp;</div>
										{/if}
									{/foreach}
									</blockquote>
								{/foreach}
							</td>
							<td style="text-align: right; width: 15%"></td>
							<td style="text-align: center; width: 10%; vertical-align: top">({$customization.quantity})</td>
							<td style="width: 15%; text-align: right;"></td>
						</tr>
					{/foreach}
				{/foreach}
			</table>

			<table style="width: 100%">
				{if (($order_invoice->total_paid_tax_incl - $order_invoice->total_paid_tax_excl) > 0)}
				<tr style="line-height:5px;">
					<td style="width: 85%; text-align: right; font-weight: bold">{l s='Product Total (Tax Excl.)' pdf='true'}</td>
					<td style="width: 15%; text-align: right;">{displayPrice currency=$order->id_currency price=$order_invoice->total_products}</td>
				</tr>

				<tr style="line-height:5px;">
					<td style="width: 85%; text-align: right; font-weight: bold">{l s='Product Total (Tax Incl.)' pdf='true'}</td>
					<td style="width: 15%; text-align: right;">{displayPrice currency=$order->id_currency price=$order_invoice->total_products_wt}</td>
				</tr>
				{else}
				<tr style="line-height:5px;">
					<td style="width: 85%; text-align: right; font-weight: bold">{l s='Product Total' pdf='true'}</td>
					<td style="width: 15%; text-align: right;">{displayPrice currency=$order->id_currency price=$order_invoice->total_products}</td>
				</tr>
				{/if}

				{if $order_invoice->total_discount_tax_incl > 0}
				<tr style="line-height:5px;">
					<td style="text-align: right; font-weight: bold">{l s='Total Vouchers' pdf='true'}</td>
					<td style="width: 15%; text-align: right;">-{displayPrice currency=$order->id_currency price=$order_invoice->total_discount_tax_incl}</td>
				</tr>
				{/if}

				{if $order_invoice->total_wrapping_tax_incl > 0}
				<tr style="line-height:5px;">
					<td style="text-align: right; font-weight: bold">{l s='Wrapping Cost' pdf='true'}</td>
					<td style="width: 15%; text-align: right;">
					{if $tax_excluded_display}
						{displayPrice currency=$order->id_currency price=$order_invoice->total_wrapping_tax_excl}
					{else}
						{displayPrice currency=$order->id_currency price=$order_invoice->total_wrapping_tax_incl}
					{/if}
					</td>
				</tr>
				{/if}

				{if $order_invoice->total_shipping_tax_incl > 0}
				<tr style="line-height:5px;">
					<td style="text-align: right; font-weight: bold">{l s='Shipping Cost' pdf='true'}</td>
					<td style="width: 15%; text-align: right;">
					{if $tax_excluded_display}
						{displayPrice currency=$order->id_currency price=$order_invoice->total_shipping_tax_excl}
					{else}
						{displayPrice currency=$order->id_currency price=$order_invoice->total_shipping_tax_incl}
					{/if}
					</td>
				</tr>
				{/if}

				{if ($order_invoice->total_paid_tax_incl - $order_invoice->total_paid_tax_excl) > 0}
				<tr style="line-height:5px;">
					<td style="text-align: right; font-weight: bold">{l s='Total Tax' pdf='true'}</td>
					<td style="width: 15%; text-align: right;">{displayPrice currency=$order->id_currency price=($order_invoice->total_paid_tax_incl - $order_invoice->total_paid_tax_excl)}</td>
				</tr>
				{/if}

				<tr style="line-height:5px;">
					<td style="text-align: right; font-weight: bold">{l s='Total' pdf='true'}</td>
					<td style="width: 15%; text-align: right;">{displayPrice currency=$order->id_currency price=$order_invoice->total_paid_tax_incl}</td>
				</tr>

				{if $order_invoice->getRestPaid()}
				<tr style="line-height:5px;color:red;">
					<td style="text-align: right; font-weight: bold">{l s='Remaining Amount Due' pdf='true'}</td>
					<td style="width: 15%; text-align: right;">{displayPrice currency=$order->id_currency price=$order_invoice->getRestPaid()}</td>
				</tr>
				{/if}
			</table>

		</td>
	</tr>
</table>
<!-- / PRODUCTS TAB -->

<div style="line-height: 1pt">&nbsp;</div>

{$tax_tab}

{if isset($order_invoice->note) && $order_invoice->note}
<div style="line-height: 1pt">&nbsp;</div>
<table style="width: 100%">
	<tr>
		<td style="width: 15%"></td>
		<td style="width: 85%">{$order_invoice->note|nl2br}</td>
	</tr>
</table>
{/if}

{if isset($HOOK_DISPLAY_PDF)}
<div style="line-height: 1pt">&nbsp;</div>
<table style="width: 100%">
	<tr>
		<td style="width: 15%"></td>
		<td style="width: 85%">{$HOOK_DISPLAY_PDF}</td>
	</tr>
</table>
{/if}

</div>
