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

	<tr>
		<td class="grey" width="70%">
			{l s='Total Rooms Cost (tax excl.)' pdf='true'}
		</td>
		<td class="white" width="30%">
			{displayPrice currency=$order->id_currency price=$footer.room_price_tax_excl}
		</td>
	</tr>
	{if isset($footer.additional_service_price_tax_excl) && $footer.additional_service_price_tax_excl}
		<tr>
			<td class="grey" width="70%">
				{l s='Extra Services Cost (tax excl.)' pdf='true'}
			</td>
			<td class="white" width="30%">
				{displayPrice currency=$order->id_currency price=($footer.additional_service_price_tax_excl - $footer.total_convenience_fee_te)}
			</td>
		</tr>
	{/if}
	{if isset($footer.total_convenience_fee_te) && $footer.total_convenience_fee_te}
		<tr>
			<td class="grey" width="70%">
				{l s='Convenience Fee (tax excl.)' pdf='true'}
			</td>
			<td class="white" width="30%">
				{displayPrice currency=$order->id_currency price=$footer.total_convenience_fee_te}
			</td>
		</tr>
	{/if}
	{* {if isset($footer.product_taxes) && $footer.product_taxes}
		<tr>
			<td class="grey" width="70%">
				{l s='Rooms Tax' pdf='true'}
			</td>
			<td class="white" width="30%">
				{displayPrice currency=$order->id_currency price=($footer.room_price_tax_incl - $footer.room_price_tax_excl)}
			</td>
		</tr>
	{/if}
	{if isset($footer.additional_service_price_tax_excl) && $footer.additional_service_price_tax_excl}
		<tr>
			<td class="grey" width="70%">
				{l s='Additional service Tax' pdf='true'}
			</td>
			<td class="white" width="30%">
				{displayPrice currency=$order->id_currency price=($footer.additional_service_price_tax_incl - $footer.additional_service_price_tax_excl)}
			</td>
		</tr>
	{/if} *}

	{* <tr>
		<td class="grey" width="70%">
			{l s='Total Service Products cost (tax excl.)' pdf='true'}
		</td>
		<td class="white" width="30%">
			{displayPrice currency=$order->id_currency price=$footer.service_products_price_tax_excl}
		</td>
	</tr> *}

	{* {if isset($footer.service_products_price_tax_excl) && $footer.service_products_price_tax_excl}
		<tr>
			<td class="grey" width="70%">
				{l s='Service Products Tax' pdf='true'}
			</td>
			<td class="white" width="30%">
				{displayPrice currency=$order->id_currency price=($footer.service_products_price_tax_incl - $footer.service_products_price_tax_excl)}
			</td>
		</tr>
	{/if} *}
	<tr class="bold">
		<td class="grey">
			{l s='Total (Tax excl.)' pdf='true'}
		</td>
		<td class="white">
			{displayPrice currency=$order->id_currency price=$footer.total_paid_tax_excl}
		</td>
	</tr>
	{if $footer.total_taxes > 0}
	<tr class="bold">
		<td class="grey">
			{l s='Total Tax' pdf='true'}
		</td>
		<td class="white">
			{displayPrice currency=$order->id_currency price=$footer.total_taxes}
		</td>
	</tr>
	{/if}
	{if $footer.product_discounts_tax_excl > 0}
		<tr>
			<td class="grey" width="70%">
				{l s='Total Discounts' pdf='true'}
			</td>
			<td class="white" width="30%">
				- {displayPrice currency=$order->id_currency price=$footer.product_discounts_tax_excl}
			</td>
		</tr>
	{/if}

	<tr class="bold big">
		<td class="grey">
			{l s='Final Booking Amount' pdf='true'}
		</td>
		<td class="white">
			{displayPrice currency=$order->id_currency price=$footer.total_paid_tax_incl}
		</td>
	</tr>

	{if $order->total_paid - $order->total_paid_real > 0}
		<tr class="bold big">
			<td class="grey">
				{l s='Due Amount' pdf='true'}
			</td>
			<td class="white">
				{displayPrice currency=$order->id_currency price=($order->total_paid - $order->total_paid_real)}
			</td>
		</tr>
	{/if}
</table>
