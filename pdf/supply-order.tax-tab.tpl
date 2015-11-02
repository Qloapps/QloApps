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
{l s='Taxes:' pdf='true'}<br/>

<table id="tax-tab" width="100%">
	<thead>
		<tr>
			<th class="header-right small">{l s='Base TE' pdf='true'}</th>
			<th class="header-right small">{l s='Tax Rate' pdf='true'}</th>
			<th class="header-right small">{l s='Tax Value' pdf='true'}</th>
		</tr>
	</thead>
	<tbody>
		{assign var=has_line value=false}

		{foreach $tax_order_summary as $entry}
			{assign var=has_line value=true}
			<tr>
				<td class="right white">{$currency->prefix} {$entry['base_te']} {$currency->suffix}</td>
				<td class="right white">{$entry['tax_rate']}</td>
				<td class="right white">{$currency->prefix} {$entry['total_tax_value']} {$currency->suffix}</td>
			</tr>
		{/foreach}

		{if !$has_line}
		<tr>
			<td class="white center" colspan="3">
				{l s='No taxes' pdf='true'}
			</td>
		</tr>
		{/if}

	</tbody>
</table>
