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
{l s='Products ordered:' pdf='true'}<br/>

<table class="product small" width="100%" cellpadding="4" cellspacing="0">

	<thead>
	<tr>
		<th class="product header small" width="14%">{l s='Reference' pdf='true'}</th>
		<th class="product header small" width="21%">{l s='Designation' pdf='true'}</th>
		<th class="product header small" width="5%">{l s='Qty' pdf='true'}</th>
		<th class="product header small" width="10%">{l s='Unit Price TE' pdf='true'}</th>
		<th class="product header small" width="11%">{l s='Total TE' pdf='true'} <br /> {l s='Before discount' pdf='true'}</th>
		<th class="product header small" width="9%">{l s='Discount Rate' pdf='true'}</th>
		<th class="product header small" width="11%">{l s='Total TE' pdf='true'} <br /> {l s='After discount' pdf='true'}</th>
		<th class="product header small" width="9%">{l s='Tax rate' pdf='true'}</th>
		<th class="product header small" width="10%">{l s='Total TI' pdf='true'}</th>
	</tr>
	</thead>

	<tbody>

	{foreach $supply_order_details as $supply_order_detail}
		{cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
		<tr class="product {$bgcolor_class}">
			<td class="product left">
				{$supply_order_detail->supplier_reference}
			</td>
			<td class="product left">
				{$supply_order_detail->name}
			</td>
			<td  class="product right">
				{$supply_order_detail->quantity_expected}
			</td>
			<td  class="product right">
				{$currency->prefix} {$supply_order_detail->unit_price_te} {$currency->suffix}
			</td>
			<td  class="product right">
				{$currency->prefix} {$supply_order_detail->price_te} {$currency->suffix}
			</td>
			<td  class="product right">
				{$supply_order_detail->discount_rate}
			</td>
			<td  class="product right">
				{$currency->prefix} {$supply_order_detail->price_with_discount_te} {$currency->suffix}
			</td>
			<td  class="product right">
				{$supply_order_detail->tax_rate}
			</td>
			<td  class="product right">
				{$currency->prefix} {$supply_order_detail->price_ti} {$currency->suffix}
			</td>
		</tr>
	{/foreach}

	</tbody>

</table>
