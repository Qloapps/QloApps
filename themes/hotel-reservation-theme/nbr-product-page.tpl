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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if isset($p) AND $p}
	{if isset($smarty.get.id_category) && $smarty.get.id_category && isset($category)}
		{assign var='requestPage' value=$link->getPaginationLink('category', $category, false, false, true, false)}
		{assign var='requestNb' value=$link->getPaginationLink('category', $category, true, false, false, true)}
	{elseif isset($smarty.get.id_manufacturer) && $smarty.get.id_manufacturer && isset($manufacturer)}
		{assign var='requestPage' value=$link->getPaginationLink('manufacturer', $manufacturer, false, false, true, false)}
		{assign var='requestNb' value=$link->getPaginationLink('manufacturer', $manufacturer, true, false, false, true)}
	{elseif isset($smarty.get.id_supplier) && $smarty.get.id_supplier && isset($supplier)}
		{assign var='requestPage' value=$link->getPaginationLink('supplier', $supplier, false, false, true, false)}
		{assign var='requestNb' value=$link->getPaginationLink('supplier', $supplier, true, false, false, true)}
	{else}
		{assign var='requestPage' value=$link->getPaginationLink(false, false, false, false, true, false)}
		{assign var='requestNb' value=$link->getPaginationLink(false, false, true, false, false, true)}
	{/if}
	<!-- nbr product/page -->
	{if $nb_products > $nArray[0]}
		<form action="{if !is_array($requestNb)}{$requestNb|escape:'html':'UTF-8'}{else}{$requestNb.requestUrl|escape:'html':'UTF-8'}{/if}" method="get" class="nbrItemPage">
			<div class="clearfix selector1">
				{if isset($search_query) AND $search_query}
					<input type="hidden" name="search_query" value="{$search_query|escape:'html':'UTF-8'}" />
				{/if}
				{if isset($tag) AND $tag AND !is_array($tag)}
					<input type="hidden" name="tag" value="{$tag|escape:'html':'UTF-8'}" />
				{/if}
				<label for="nb_page_items{if isset($paginationId)}_{$paginationId}{/if}">
					{l s='Show'}
				</label>
				{if is_array($requestNb)}
					{foreach from=$requestNb item=requestValue key=requestKey}
						{if $requestKey != 'requestUrl'}
							<input type="hidden" name="{$requestKey|escape:'html':'UTF-8'}" value="{$requestValue|escape:'html':'UTF-8'}" />
						{/if}
					{/foreach}
				{/if}
				<select name="n" id="nb_page_items{if isset($paginationId)}_{$paginationId}{/if}" class="form-control">
					{assign var="lastnValue" value="0"}
					{foreach from=$nArray item=nValue}
						{if $lastnValue <= $nb_products}
							<option value="{$nValue|escape:'html':'UTF-8'}" {if $n == $nValue}selected="selected"{/if}>{$nValue|escape:'html':'UTF-8'}</option>
						{/if}
						{assign var="lastnValue" value=$nValue}
					{/foreach}
				</select>
				<span>{l s='per page'}</span>
			</div>
		</form>
	{/if}
	<!-- /nbr product/page -->
{/if}
