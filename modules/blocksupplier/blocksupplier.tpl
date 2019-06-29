{*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Block suppliers module -->
<div id="suppliers_block_left" class="block blocksupplier">
	<h4 class="title_block">{if $display_link_supplier}<a href="{$link->getPageLink('supplier')|escape:'html'}" title="{l s='Suppliers' mod='blocksupplier'}">{/if}{l s='Suppliers' mod='blocksupplier'}{if $display_link_supplier}</a>{/if}</h4>
	<div class="block_content">
{if $suppliers}
	{if $text_list}
	<ul class="bullet">
	{foreach from=$suppliers item=supplier name=supplier_list}
		{if $smarty.foreach.supplier_list.iteration <= $text_list_nb}
		<li class="{if $smarty.foreach.supplier_list.last}last_item{elseif $smarty.foreach.supplier_list.first}first_item{else}item{/if}">
            {if $display_link_supplier}<a href="{$link->getsupplierLink($supplier.id_supplier, $supplier.link_rewrite)|escape:'html'}" title="{l s='More about' mod='blocksupplier'} {$supplier.name}">{/if}{$supplier.name|escape:'html':'UTF-8'}{if $display_link_supplier}</a>{/if}
		</li>
		{/if}
	{/foreach}
	</ul>
	{/if}
	{if $form_list}
		<form action="{$smarty.server.SCRIPT_NAME|escape:'html':'UTF-8'}" method="get">
			<p>
				<select id="supplier_list" onchange="autoUrl('supplier_list', '');">
					<option value="0">{l s='All suppliers' mod='blocksupplier'}</option>
				{foreach from=$suppliers item=supplier}
					<option value="{$link->getsupplierLink($supplier.id_supplier, $supplier.link_rewrite)|escape:'html'}">{$supplier.name|escape:'html':'UTF-8'}</option>
				{/foreach}
				</select>
			</p>
		</form>
	{/if}
{else}
	<p>{l s='No supplier' mod='blocksupplier'}</p>
{/if}
	</div>
</div>
<!-- /Block suppliers module -->
