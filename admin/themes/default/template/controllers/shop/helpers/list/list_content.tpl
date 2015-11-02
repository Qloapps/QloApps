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

{extends file="helpers/list/list_content.tpl"}

{block name="open_td"}
	{if $key == 'url'}
		<td{if isset($params.position)} id="td_{if !empty($position_group_identifier)}{$position_group_identifier}{else}0{/if}_{$tr.$identifier}{if $smarty.capture.tr_count > 1}_{($smarty.capture.tr_count - 1)|intval}{/if}"{/if} class="{if !$no_link}pointer{/if}{if isset($params.class)} {$params.class}{/if}{if isset($params.align)} {$params.align}{/if}">
	{else}
		<td class="pointer" onclick="document.location = '{$current_index|escape:'html':'UTF-8'}&amp;shop_id={$tr.$identifier|escape:'html':'UTF-8'}{if $view}&amp;view{else}&amp;update{/if}{$table|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}'">
	{/if}
{/block}

{block name="td_content"}
	{if $key == 'url'}
		{if isset($tr.$key)}
			<a href="{$tr.$key}" onmouseover="$(this).css('text-decoration', 'underline')" onmouseout="$(this).css('text-decoration', 'none')" class="_blank">{$tr.$key}</a>
		{else}
			<a href="{$link->getAdminLink('AdminShopUrl')|escape:'html':'UTF-8'}&amp;shop_id={$tr.$identifier|intval}&amp;addshop_url" class="multishop_warning">{l s='Click here to set a URL for this shop.'}</a>
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
