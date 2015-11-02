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
<table id="total-tab" width="100%">

	{if $order_slip->shipping_cost_amount > 0}
		<tr>
			{if $tax_excluded_display}
				<td class="grey" width="70%">{l s='Shipping (Tax Excl.)' pdf='true'}</td>
			{else}
				<td class="grey" width="70%">{l s='Shipping (Tax Incl.)' pdf='true'}</td>
			{/if}
			<td class="white" width="30%">
				- {displayPrice currency=$order->id_currency price=$order_slip->shipping_cost_amount}
			</td>
		</tr>
	{/if}

	{if isset($order_details) && count($order_details) > 0}
		{if (($order->total_paid_tax_incl - $order->total_paid_tax_excl) > 0)}
			{if $tax_excluded_display}
				<tr>
					<td class="grey" width="70%">
						{l s='Product Total (Tax Excl.)' pdf='true'}
					</td>
					<td class="white" width="30%">
						- {displayPrice currency=$order->id_currency price=$order->total_products}
					</td>
				</tr>
			{else}
				<tr>
					<td class="grey" width="70%">
						{l s='Product Total (Tax Incl.)' pdf='true'}
					</td>
					<td class="white" width="30%">
						- {displayPrice currency=$order->id_currency price=$order->total_products_wt}
					</td>
				</tr>
			{/if}
		{else}
			<tr>
				<td class="grey" width="70%">
					{l s='Product Total' pdf='true'}
				</td>
				<td class="white" width="30%">
					- {displayPrice currency=$order->id_currency price=$order->total_products}
				</td>
			</tr>
		{/if}
	{/if}
	
	{if ($order->total_paid_tax_incl - $order->total_paid_tax_excl) > 0}
		<tr>
			<td class="grey" width="70%">
				{l s='Total Tax' pdf='true'}
			</td>
			<td class="white" width="30%">
				- {displayPrice currency=$order->id_currency price=($order->total_paid_tax_incl - $order->total_paid_tax_excl)}
			</td>
		</tr>
	{/if}
	
	<tr class="bold">
		<td class="grey" width="70%">
			{if $tax_excluded_display}{l s='Total (Tax Excl.)' pdf='true'}{else}{l s='Total (Tax Incl.)' pdf='true'}{/if}
		</td>
		<td class="white" width="30%">
			{if $total_cart_rule}
				{assign var=total_paid value=0}
				{if $tax_excluded_display}
					{$total_paid = $order->total_paid_tax_excl - $total_cart_rule}
				{else}
					{$total_paid = $order->total_paid_tax_incl - $total_cart_rule}
				{/if}
				- {displayPrice currency=$order->id_currency price=$total_paid}
			{elseif $amount_choosen}
				- {displayPrice currency=$order->id_currency price=($order_slip->amount+$order_slip->shipping_cost_amount)}
			{else}
				{if $tax_excluded_display}
					- {displayPrice currency=$order->id_currency price=$order->total_paid_tax_excl}
				{else}
					- {displayPrice currency=$order->id_currency price=$order->total_paid_tax_incl}
				{/if}
			{/if}
		</td>
	</tr>

</table>
