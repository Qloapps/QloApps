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
{l s='Summary:' pdf='true'}<br/>

<table id="total-tab" width="100%">

	<tr class="bold">
		<td class="grey" width="70%">{l s='Total TE' pdf='true'} <br /> {l s='(Before discount)' pdf='true'}</td>
		<td class="white" width="30%">
			{$currency->prefix} {$supply_order->total_te} {$currency->suffix}
		</td>
	</tr>
	<tr class="bold">
		<td class="grey" width="70%">{l s='Order Discount' pdf='true'}</td>
		<td class="white" width="30%">
			{$currency->prefix} {$supply_order->discount_value_te} {$currency->suffix}
		</td>
	</tr>
	<tr class="bold">
		<td class="grey" width="70%">{l s='Total TE' pdf='true'} <br /> {l s='(After discount)' pdf='true'}</td>
		<td class="white" width="30%">
			{$currency->prefix} {$supply_order->total_with_discount_te} {$currency->suffix}
		</td>
	</tr>
	<tr class="bold">
		<td class="grey" width="70%">{l s='Tax value' pdf='true'}</td>
		<td class="white" width="30%">
			{$currency->prefix} {$supply_order->total_tax} {$currency->suffix}
		</td>
	</tr>
	<tr class="bold">
		<td class="grey" width="70%">{l s='Total TI' pdf='true'}</td>
		<td class="white" width="30%">
			{$currency->prefix} {$supply_order->total_ti} {$currency->suffix}
		</td>
	</tr>
	<tr class="bold">
		<td class="grey" width="70%">{l s='Total to pay' pdf='true'}</td>
		<td class="white" width="30%">
			{$currency->prefix} {$supply_order->total_ti} {$currency->suffix}
		</td>
	</tr>

</table>
