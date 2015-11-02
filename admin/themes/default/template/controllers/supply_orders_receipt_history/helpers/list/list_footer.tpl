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
{extends file="helpers/list/list_footer.tpl"}
{block name="footer"}
<div class="panel-footer">
	{foreach from=$toolbar_btn item=btn key=k}
		{if $k == 'back'}
			{assign 'back_button' $btn}
			{break}
		{/if}
	{/foreach}
	{if isset($back_button)}
		<a id="desc-{$table}-{if isset($back_button.imgclass)}{$back_button.imgclass}{else}{$k}{/if}" class="btn btn-default{if isset($back_button.target) && $back_button.target} _blank{/if}"{if isset($back_button.href)} href="{$back_button.href|escape:'html':'UTF-8'}"{/if}{if isset($back_button.js) && $back_button.js} onclick="{$back_button.js}"{/if}>
			<i class="process-icon-back{if isset($back_button.class)} {$back_button.class}{/if}" ></i> <span{if isset($back_button.force_desc) && $back_button.force_desc == true } class="locked"{/if}>{$back_button.desc}</span>
		</a>
	{/if}
	<a href="" class="btn btn-default pull-right" onclick="if (confirm('{l s='Update selected items?'}'))sendBulkAction($(this).closest('form').get(0), 'submitBulkUpdatesupply_order_detail'); return false;">
		<i class="process-icon-refresh" ></i> <span>{l s='Update selected items'}</span>
	</a>
</div>
{/block}
