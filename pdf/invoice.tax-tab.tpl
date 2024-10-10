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

<!--  TAX DETAILS -->

{if (isset($tax_breakdowns) && $tax_breakdowns)}
	{assign var=th_rows value=3}
	{if isset($showTaxName) && $showTaxName}
		{assign var=th_rows value=$th_rows+1}
	{/if}
	{if $display_tax_bases_in_breakdowns}
		{assign var=th_rows value=$th_rows+1}
	{/if}
	<table class="bordered-table" id="tax-tab" width="100%">
		<thead>
			<tr>
				<th colspan="{$th_rows}" class="header">{l s='Tax Details' pdf='true'}</th>
			</tr>
			<tr>
				<th class="header-left small">{l s='Tax Detail' pdf='true'}</th>
				{if isset($showTaxName) && $showTaxName}
					<th class="header-left small">{l s='Tax Name' pdf='true'}</th>
				{/if}
				<th class="header-left small">{l s='Tax Rate' pdf='true'}</th>
				{if $display_tax_bases_in_breakdowns}
					<th class="header-left small">{l s='Base price' pdf='true'}</th>
				{/if}
				<th class="header-left small">{l s='Total Tax' pdf='true'}</th>
			</tr>
		</thead>
		<tbody>

		{assign var=has_line value=false}

		{foreach $tax_breakdowns as $label => $bd}
			{assign var=label_printed value=false}

			{foreach $bd as $line}
				{if $line.rate == 0}
					{continue}
				{/if}
				{assign var=has_line value=true}
				<tr class="{if !$label_printed}tr-border-top{/if}">
					{if !$label_printed}
						<td class="white" rowspan="{$bd|count}">
							{if $label == 'additional_services_tax'}
								{l s='Services' pdf='true'}
							{elseif $label == 'room_tax'}
								{l s='Rooms' pdf='true'}
							{elseif $label == 'extra_demands_tax'}
								{l s='Facilities' pdf='true'}
							{elseif $label == 'convenience_fee_tax'}
								{l s='Convenience Fees' pdf='true'}
							{elseif $label == 'service_products_tax'}
								{l s='Service Products' pdf='true'}
							{elseif $label == 'shipping_tax'}
								{l s='Shipping' pdf='true'}
							{elseif $label == 'ecotax_tax'}
								{l s='Ecotax' pdf='true'}
							{elseif $label == 'wrapping_tax'}
								{l s='Wrapping' pdf='true'}
							{/if}
							{assign var=label_printed value=true}
						</td>
					{/if}
					{if isset($showTaxName) && $showTaxName}
						<td class="white">
							{if isset($line.name) && $line.name}
								{$line.name}
							{else}
								{l s='tax' pdf='true'}
							{/if}
						</td>
					{/if}
					<td class="white">
						{$line.rate} %
					</td>

					{if $display_tax_bases_in_breakdowns}
						<td class="white">
							{if isset($is_order_slip) && $is_order_slip}- {/if}
							{displayPrice currency=$order->id_currency price=$line.total_tax_excl}
						</td>
					{/if}

					<td class="white">
						{if isset($is_order_slip) && $is_order_slip}- {/if}
						{displayPrice currency=$order->id_currency price=$line.total_amount}
					</td>
				</tr>
			{/foreach}
		{/foreach}
		{if !$has_line}
			<tr>
				<td class="white" colspan="{if $display_tax_bases_in_breakdowns}4{else}3{/if}">
					{l s='No taxes' pdf='true'}
				</td>
			</tr>
		{/if}

		</tbody>
	</table>
{/if}
<!--  / TAX DETAILS -->