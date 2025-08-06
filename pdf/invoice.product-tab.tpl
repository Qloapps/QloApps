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

{if isset($cart_htl_data) && $cart_htl_data}
	<table class="product" class="bordered-table" width="100%" cellpadding="4" cellspacing="0">
		<thead>
			<tr>
				<th colspan="{if $display_product_images}9{else}8{/if}" class="header">{l s='Rooms Details' pdf='true'}</th>
			</tr>
			<tr>
				{if $display_product_images}
					<th class="product header small">{l s='Room Image' pdf='true'}</th>
				{/if}
				<th class="product header small">{l s='Room Description' pdf='true'}</th>
				<th class="product header small">{l s='Hotel' pdf='true'}</th>
				<th class="product header small">{l s='Tax Rate(s)' pdf='true'}</th>
				{* {if isset($layout.before_discount)}
					<th class="product header small">{l s='Base price' pdf='true'} <br /> {l s='(Tax excl.)' pdf='true'}</th>
				{/if} *}

				<th class="product header small">{l s='Unit Price' pdf='true'} <br /> {l s='(Tax excl.)' pdf='true'}</th>
				<th class="product header small">{l s='Rooms' pdf='true'}</th>
				<th class="product header small">{l s='Check-in Date' pdf='true'}</th>
				<th class="product header small">{l s='Check-out Date' pdf='true'}</th>
				<th class="product header small">{l s='Total' pdf='true'} <br /> {l s='(Tax excl.)' pdf='true'}</th>
			</tr>
		</thead>
		<tbody>
				{foreach from=$cart_htl_data key=data_k item=data_v}
					{foreach from=$data_v['date_diff'] key=rm_k item=rm_v}
						{cycle values=["color_line_even", "color_line_odd"] assign=bgcolor_class}
						<tr class="product {$bgcolor_class}">
							{if $display_product_images}
								<td class="cart_product">
									<img src="{$data_v['cover_img']}" class="thumbnail" />
								</td>
							{/if}
							<td class="product center">
								<p class="product-name">
									{$data_v['name']}
								</p>
							</td>
							<td class="product center">
								<p>
									{$data_v['hotel_name']}
								</p>
							</td>
							<td class="product center">
								{$data_v['order_detail_tax_label']}
							</td>
							<td class="product center">
								<p class="text-center">
									{displayPrice currency=$order->id_currency price=$rm_v['avg_paid_unit_price_tax_excl']}
								</p>
							</td>
							<td class="product center">
								<p class="text-left">
									{if $rm_v['adults'] <= 9}0{$rm_v['adults']}{else}{$rm_v['adults']}{/if} {if $rm_v['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if $rm_v['children']}, {if $rm_v['children'] <= 9}0{$rm_v['children']}{else} {$rm_v['children']}{/if} {if $rm_v['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if}<br>{if $rm_v['num_rm'] <= 9}0{/if}{$rm_v['num_rm']} {if $rm_v['num_rm'] > 1}{l s='Rooms'}{else}{l s='Room'}{/if}
								</p>
							</td>
							<td class="product center">
								<p>
									{$rm_v['data_form']}
								</p>
							</td>
							<td class="product center">
								<p>
									{$rm_v['data_to']}
								</p>
							</td>
							<td class="product center">
								<p>
									{displayPrice currency=$order->id_currency price=$rm_v['amount']}
								</p>
							</td>
						</tr>
					{/foreach}
				{/foreach}
			{* END PRODUCTS *}
			{* CART RULES *}
			{* {assign var="shipping_discount_tax_incl" value="0"}
			{foreach from=$cart_rules item=cart_rule name="cart_rules_loop"}
				{if $smarty.foreach.cart_rules_loop.first}
				<tr class="discount">
					<th class="header" colspan="{$layout._colCount}">
						{l s='Discounts' pdf='true'}
					</th>
				</tr>
				{/if}
				<tr class="discount">
					<td class="white right" colspan="{$layout._colCount - 1}">
						{$cart_rule.name}
					</td>
					<td class="right white">
						- {displayPrice currency=$order->id_currency price=$cart_rule.value_tax_excl}
					</td>
				</tr>
			{/foreach} *}
		</tbody>
	</table>
{/if}