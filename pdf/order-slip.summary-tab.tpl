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

<table id="summary-tab" width="100%">
	<tr>
		<th class="header small" valign="middle">{l s='Order Reference' pdf='true'}</th>
		<th class="header small" valign="middle">{l s='Order Date' pdf='true'}</th>
		{if $addresses.invoice->vat_number}
			<th class="header small" valign="middle">{l s='VAT Number' pdf='true'}</th>
		{/if}
	</tr>
	<tr>
		<td class="center small white">{$order->getUniqReference()}</td>
		<td class="center small white">{dateFormat date=$order->date_add full=0}</td>
		{if $addresses.invoice->vat_number}
			<td class="center small white">
				{$addresses.invoice->vat_number}
			</td>
		{/if}
	</tr>
</table>
