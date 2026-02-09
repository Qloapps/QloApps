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

<script type="text/javascript">
$(function() {
	$('#content .panel').highlight('{$query}');
});
</script>

{if $query}
	<h2>
	{if isset($nb_results) && $nb_results == 0}
		<h2>{l s='There are no results matching your query "%s".' sprintf=$query}</h2>
	{elseif isset($nb_results) && $nb_results == 1}
		{l s='1 result matches your query "%s".' sprintf=$query}
	{elseif isset($nb_results)}
		{l s='%d results match your query "%s".' sprintf=[$nb_results|intval, $query]}
	{/if}
	</h2>
{/if}

{if $query && isset($nb_results) && $nb_results}

	{if isset($features)}
	<div class="panel">
		<h3>
			{if $features|@count == 1}
				{l s='1 feature'}
			{else}
				{l s='%d features' sprintf=$features|@count}
			{/if}
		</h3>
		<table class="table">
			<tbody>
			{foreach $features key=key item=feature}
				{foreach $feature key=k item=val name=feature_list}
					<tr>
						<td><a href="{$val.link}"{if $smarty.foreach.feature_list.first}><strong>{$key}</strong>{/if}</a></td>
						<td><a href="{$val.link}">{$val.value}</a></td>
					</tr>
				{/foreach}
			{/foreach}
			</tbody>
		</table>
	</div>
	{/if}

	{if isset($modules) && $modules}
	<div class="panel">
		<h3>
			{if $modules|@count == 1}
				{l s='1 module'}
			{else}
				{l s='%d modules' sprintf=$modules|@count}
			{/if}
		</h3>
		<table class="table">
			<tbody>
			{foreach $modules key=key item=module}
				<tr>
					<td><a href="{$module->linkto|escape:'html':'UTF-8'}"><strong>{$module->displayName}</strong></a></td>
					<td><a href="{$module->linkto|escape:'html':'UTF-8'}">{$module->description}</a></td>
				</tr>
			{/foreach}
		</tbody>
		</table>
	</div>
	{/if}

	{if isset($categories) && $categories}
	<div class="panel">
		<h3>
			{if $categories|@count == 1}
				{l s='1 category'}
			{else}
				{l s='%d categories' sprintf=$categories|@count}
			{/if}
		</h3>
		<table class="table" style="border-spacing : 0; border-collapse : collapse;">
			{foreach $categories key=key item=category}
				<tr>
					<td>{$category}</td>
				</tr>
			{/foreach}
		</table>
	</div>
	{/if}

	{if isset($num_hotels) && $num_hotels}
		<div class="panel">
			<h3>
				{if $num_hotels == 1}
					{l s='1 Hotel'}
				{else}
					{l s='%d Hotels' sprintf=$num_hotels}
				{/if}
			</h3>
			{$hotels}
		</div>
	{/if}

	{if isset($num_hotel_features) && $num_hotel_features}
		<div class="panel">
			<h3>
				{if $num_hotel_features == 1}
					{l s='1 hotel feature'}
				{else}
					{l s='%d hotel features' sprintf=$num_hotel_features}
				{/if}
			</h3>
			{$hotel_features}
		</div>
	{/if}

	{if isset($num_products) && $num_products}
		<div class="panel">
			<h3>
				{if $num_products == 1}
					{l s='1 room type'}
				{else}
					{l s='%d room types' sprintf=$num_products}
				{/if}
			</h3>
			{$products}
		</div>
	{/if}

	{if isset($num_catalog_features) && $num_catalog_features}
		<div class="panel">
			<h3>
				{if $num_catalog_features == 1}
					{l s='1 feature'}
				{else}
					{l s='%d features' sprintf=$num_catalog_features}
				{/if}
			</h3>
			{$catalog_features}
		</div>
	{/if}

	{if isset($num_service_products) && $num_service_products}
		<div class="panel">
			<h3>
				{if $num_service_products == 1}
					{l s='1 service product'}
				{else}
					{l s='%d service products' sprintf=$num_service_products}
				{/if}
			</h3>
			{$service_products}
		</div>
	{/if}

	{if isset($num_global_demands) && $num_global_demands}
		<div class="panel">
			<h3>
				{if $num_global_demands == 1}
					{l s='1 global demand'}
				{else}
					{l s='%d global demands' sprintf=$num_global_demands}
				{/if}
			</h3>
			{$global_demands}
		</div>
	{/if}

	{if isset($num_refund_rules) && $num_refund_rules}
		<div class="panel">
			<h3>
				{if $num_refund_rules == 1}
					{l s='1 refund rule'}
				{else}
					{l s='%d refund rules' sprintf=$num_refund_rules}
				{/if}
			</h3>
			{$refund_rules}
		</div>
	{/if}

	{if isset($num_customers) && $num_customers}
		<div class="panel">
			<h3>
				{if $num_customers == 1}
					{l s='1 customer'}
				{else}
					{l s='%d customers' sprintf=$num_customers}
				{/if}
			</h3>
			{$customers}
		</div>
	{/if}

	{if isset($num_groups) && $num_groups}
		<div class="panel">
			<h3>
				{if $num_groups == 1}
					{l s='1 group'}
				{else}
					{l s='%d groups' sprintf=$num_groups}
				{/if}
			</h3>
			{$groups}
		</div>
	{/if}

	{if isset($num_customer_address) && $num_customer_address}
		<div class="panel">
			<h3>
				{if $num_customer_address == 1}
					{l s='1 customer address'}
				{else}
					{l s='%d customer addresses' sprintf=$num_customer_address}
				{/if}
			</h3>
			{$customer_address}
		</div>
	{/if}

	{if isset($num_order_messages) && $num_order_messages}
		<div class="panel">
			<h3>
				{if $num_order_messages == 1}
					{l s='1 customer service message'}
				{else}
					{l s='%d customer service messages' sprintf=$num_order_messages}
				{/if}
			</h3>
			{$order_messages}
		</div>
	{/if}

	{if isset($num_orders) && $num_orders}
		<div class="panel">
			<h3>
				{if $num_orders == 1}
					{l s='1 order'}
				{else}
					{l s='%d orders' sprintf=$num_orders}
				{/if}
			</h3>
			{$orders}
		</div>
	{/if}

	{if isset($addons) && $addons}
	<div class="panel">
		<h3>
			{if $addons|@count == 1}
				{l s='1 addon'}
			{else}
				{l s='%d addons' sprintf=$addons|@count}
			{/if}
		</h3>
		<table class="table">
			<tbody>
			{foreach $addons key=key item=addon}
				<tr>
					<td><a href="{$addon.href|escape:'html':'UTF-8'}&amp;utm_source=back-office&amp;utm_medium=search&amp;utm_campaign=back-office-{$lang_iso|upper}&amp;utm_content={if $host_mode}cloud{else}download{/if}" class="_blank"><strong><i class="icon-external-link-sign"></i> {$addon.title|escape:'html':'UTF-8'}</strong></a></td>
					<td><a href="{$addon.href|escape:'html':'UTF-8'}&amp;utm_source=back-office&amp;utm_medium=search&amp;utm_campaign=back-office-{$lang_iso|upper}&amp;utm_content={if $host_mode}cloud{else}download{/if}" class="_blank">{if is_string($addon.description)}{$addon.description|truncate:256:'...'|escape:'html':'UTF-8'}{/if}</a></td>
				</tr>
			{/foreach}
		</tbody>
			<tfoot>
				<tr>
					<td colspan="2" class="text-center"><a href="http://addons.prestashop.com/search.php?search_query={$query|urlencode}&amp;utm_source=back-office&amp;utm_medium=search&amp;utm_campaign=back-office-{$lang_iso|upper}&amp;utm_content={if $host_mode}cloud{else}download{/if}" class="_blank"><strong>{l s='Show more results...'}</strong></a></td>
				</tr>
			</tfoot>
		</table>
	</div>
	{/if}

{/if}
<div class="row">
	<div class="col-lg-4">
		<div class="panel">
			<h3>{l s='Search qloapps.com/blog'}</h3>
			<a href="https://qloapps.com/?s={$query}" class="btn btn-default _blank">{l s='Go to the documentation'}</a>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="panel">
			<h3>{l s='Search qloapps.com/addons'}</h3>
			<a href="https://qloapps.com/addons/?add={$query}" class="btn btn-default _blank">{l s='Go to Addons'}</a>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="panel">
			<h3>{l s='Search forums.qloapps.com'}</h3>
			<a href="https://forums.qloapps.com/search?term={$query}" class="btn btn-default _blank">{l s='Go to the Forum'}</a>
		</div>
	</div>
</div>




