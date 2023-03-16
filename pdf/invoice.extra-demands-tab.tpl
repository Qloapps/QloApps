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

<br><br>
{if isset($room_extra_demands) && $room_extra_demands}
	<table id="demands-table" class="bordered-table" width="100%" cellpadding="4" cellspacing="0">
		<thead>
			<tr>
				<th colspan="4" class="header">{l s='Additional Facilities Details' pdf='true'}</th>
			</tr>
			<tr>
				<th class="header-left small">{l s='Room Type' pdf='true'}</th>
				<th class="header-left small">{l s='Name' pdf='true'}</th>
				<th class="header-left small">{l s='Tax rate(s)' pdf='true'}</th>
				<th class="header-left small">{l s='Total' pdf='true'} <br /> {l s='(Tax excl.)' pdf='true'}</th>
			</tr>
		</thead>
		{* <tbody>
			{if isset($cart_htl_data)}
				{foreach from=$cart_htl_data key=data_k item=data_v}
					{foreach from=$data_v['date_diff'] key=rm_k item=rm_v}
						{if isset($rm_v['extra_demands']) && $rm_v['extra_demands']}
							{assign var=roomCount value=1}
							{foreach $rm_v['extra_demands'] as $roomDemand}
								{foreach name=demandRow from=$roomDemand['extra_demands'] item=demand}
									<tr class="header small">
										{if $smarty.foreach.demandRow.first}
											<td rowspan="{$roomDemand['extra_demands']|count}">
												{$data_v['name']}<br>
												{$rm_v['data_form']} {l s='to' pdf='true'} {$rm_v['data_to']}<br>
												<strong>{l s='Room' pdf='true'} - {$roomCount}</strong>
											</td>
										{/if}
										<td>
											{$demand['name']}
										</td>
										<td>
											{$demand['extra_demands_tax_label']}
										</td>
										<td>
											{displayPrice currency=$order->id_currency price=$demand['total_price_tax_excl']}
										</td>
									</tr>
								{/foreach}
								{assign var=roomCount value=$roomCount+1}
							{/foreach}
						{/if}
					{/foreach}
				{/foreach}
			{/if}
		</tbody> *}
		<tbody>
				{* {foreach from=$cart_htl_data key=data_k item=data_v}
					{foreach from=$data_v['date_diff'] key=rm_k item=rm_v} *}
						{* {if isset($rm_v['extra_demands']) && $rm_v['extra_demands']} *}
							{assign var=roomCount value=1}
							{foreach $room_extra_demands as $roomDemand}
								{foreach name=demandRow from=$roomDemand['extra_demands'] item=demand}
									<tr class="header small">
										{if $smarty.foreach.demandRow.first}
											<td rowspan="{$roomDemand['extra_demands']|count}">
												{$roomDemand['room_type_name']}<br>
												{dateFormat date=$roomDemand['date_from']} {l s='to' pdf='true'} {dateFormat date=$roomDemand['date_to']}<br>
												<strong>{l s='Room' pdf='true'} - {$roomCount}</strong>
											</td>
										{/if}
										<td>
											{$demand['name']}
										</td>
										<td class="center">
											{$demand['extra_demands_tax_label']}
										</td>
										<td>
											{displayPrice currency=$order->id_currency price=$demand['total_price_tax_excl']}
										</td>
									</tr>
								{/foreach}
								{assign var=roomCount value=$roomCount+1}
							{/foreach}
						{* {/if}
					{/foreach}
				{/foreach} *}
		</tbody>
	</table>
{/if}
<br><br>

{if isset($room_additinal_services) && $room_additinal_services}
	<table id="demands-table" class="bordered-table" width="100%" cellpadding="4" cellspacing="0">
		<thead>
			<tr>
				<th colspan="5" class="header">{l s='Service Details' pdf='true'}</th>
			</tr>
			<tr>
				<th class="header-left small">{l s='Room Type' pdf='true'}</th>
				<th class="header-left small">{l s='Name' pdf='true'}</th>
				<th class="header small">{l s='Tax rate(s)' pdf='true'}</th>
				<th class="header small">{l s='Qty' pdf='true'}</th>
				<th class="header-left small">{l s='Total' pdf='true'} <br /> {l s='(Tax excl.)' pdf='true'}</th>
			</tr>
		</thead>
		<tbody>
				{* {foreach from=$cart_htl_data key=data_k item=data_v}
					{foreach from=$data_v['date_diff'] key=rm_k item=rm_v} *}
						{* {if isset($rm_v['extra_demands']) && $rm_v['extra_demands']} *}
							{assign var=roomCount value=1}
							{foreach $room_additinal_services as $roomService}
								{foreach name=demandRow from=$roomService['additional_services'] item=service}
									<tr class="header small">
										{if $smarty.foreach.demandRow.first}
											<td rowspan="{$roomService['additional_services']|count}">
												{$roomService['room_type_name']}<br>
												{dateFormat date=$roomService['date_from']} {l s='to' pdf='true'} {dateFormat date=$roomService['date_to']}<br>
												<strong>{l s='Room' pdf='true'} - {$roomCount}</strong>
											</td>
										{/if}
										<td>
											{$service['name']}
										</td>
										<td class="center">
											{$service['product_tax_label']}
										</td>
										<td class="center">
											{if $service['allow_multiple_quantity']}
												{$service['quantity']}
											{else}
												{l s='--' pdf='true'}
											{/if}
										</td>
										<td>
											{displayPrice currency=$order->id_currency price=$service['total_price_tax_excl']}
										</td>
									</tr>
								{/foreach}
								{assign var=roomCount value=$roomCount+1}
							{/foreach}
						{* {/if}
					{/foreach}
				{/foreach} *}
		</tbody>
	</table>
{/if}
