{*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span><span class="navigation_page">{l s='My vouchers'}</span>{/capture}

<h1 class="page-heading">
	{l s='My vouchers'}
</h1>

{if isset($cart_rules) && $nb_cart_rules}
	<table class="discount table table-bordered footab">
		<thead>
			<tr>
				<th data-sort-ignore="true" class="discount_code first_item">{l s='Code'}</th>
				<th data-sort-ignore="true" class="discount_description item">{l s='Name'}</th>
				<th class="discount_quantity item">{l s='Quantity'}</th>
				<th data-sort-ignore="true" data-hide="phone,tablet" class="discount_value item">{l s='Value'}</th>
				<th data-hide="phone,tablet" class="discount_minimum item">{l s='Minimum'}</th>
				<th data-sort-ignore="true" data-hide="phone,tablet" class="discount_cumulative item">{l s='Cumulative'}</th>
				<th data-hide="phone" class="discount_expiration_date last_item">{l s='Expiration date'}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$cart_rules item=discountDetail name=myLoop}
				<tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
					<td class="discount_code">{$discountDetail.code}</td>
					<td class="discount_description">{$discountDetail.name}</td>
					<td data-value="{$discountDetail.quantity_for_user}" class="discount_quantity">{$discountDetail.quantity_for_user}</td>
					<td class="discount_value">
						{if $discountDetail.reduction_percent > 0}
							{$discountDetail.reduction_percent|escape:'html':'UTF-8'}%
						{/if}
						{if $discountDetail.reduction_amount > 0}
							{if $discountDetail.reduction_percent > 0} + {/if}
							{convertPrice price=$discountDetail.reduction_amount} ({if $discountDetail.reduction_tax == 1}{l s='Tax included'}{else}{l s='Tax excluded'}{/if})
						{/if}
						{if $discountDetail.free_shipping}
							{if $discountDetail.reduction_percent > 0 || $discountDetail.reduction_amount > 0} + {/if}
							{l s='Free shipping'}
						{/if}
						{if $discountDetail.gift_product > 0}
							{if $discountDetail.reduction_percent > 0 || $discountDetail.reduction_amount > 0} + {/if}
							<a href="{$discountDetail.gift_product_link|escape:'html':'UTF-8'}">
								{$discountDetail.gift_product_name|escape:'html':'UTF-8'}
								{if isset($discountDetail.gift_product_attributes) && $discountDetail.gift_product_attributes|count_characters}
								 ({$discountDetail.gift_product_attributes|escape:'html':'UTF-8'})
								{/if}
							</a>
						{/if}
					</td>
					<td class="discount_minimum" data-value="{if $discountDetail.minimal == 0}0{else}{$discountDetail.minimal}{/if}">
						{if $discountDetail.minimal == 0}
							{l s='None'}
						{else}
							{convertPrice price=$discountDetail.minimal} ({if $discountDetail.minimum_amount_tax == 1}{l s='Tax included'}{else}{l s='Tax excluded'}{/if})
						{/if}
					</td>
					<td class="discount_cumulative">
						{if $discountDetail.cumulable == 1}
							<i class="icon-ok icon"></i> {l s='Yes'}
						{else}
							<i class="icon-remove icon"></i> {l s='No'}
						{/if}
					</td>
					<td class="discount_expiration_date" data-value="{$discountDetail.date_to|regex_replace:"/[\-\:\ ]/":""}">
						{dateFormat date=$discountDetail.date_to}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<p class="alert alert-warning">{l s='You do not have any vouchers.'}</p>
{/if}

<ul class="footer_links clearfix">
	<li>
		<a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
			<span>
				<i class="icon-chevron-left"></i> {l s='Back to your account'}
			</span>
		</a>
	</li>
	<li>
		<a class="btn btn-default button button-small" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}">
			<span>
				<i class="icon-chevron-left"></i> {l s='Home'}
			</span>
		</a>
	</li>
</ul>
