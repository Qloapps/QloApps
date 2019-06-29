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

<li {if isset($last) && $last == 'true'}class="last"{/if}>
	<strong><a href="{$node.link|escape:'html':'UTF-8'}" title="{$node.name|escape:'html':'UTF-8'}">{$node.name|escape:'html':'UTF-8'}</a></strong>
	{if isset($node.children) && $node.children|@count > 0}
		<ul>
		{foreach from=$node.children item=child name=categoryCmsTreeBranch}
			{if isset($child.children) && $child.children|@count > 0 || isset($child.cms) && $child.cms|@count > 0}
				{if $smarty.foreach.categoryCmsTreeBranch.last && $node.cms|@count == 0}
					{include file="$tpl_dir./category-cms-tree-branch.tpl" node=$child last='true'}
				{else}
					{include file="$tpl_dir./category-cms-tree-branch.tpl" node=$child}
				{/if}
			{/if}
		{/foreach}
		{if isset($node.cms) && $node.cms|@count > 0}
			{foreach from=$node.cms item=cms name=cmsTreeBranch}
				<li {if $smarty.foreach.cmsTreeBranch.last}class="last"{/if} ><a href="{$cms.link|escape:'html':'UTF-8'}" title="{$cms.meta_title|escape:'html':'UTF-8'}">{$cms.meta_title|escape:'html':'UTF-8'}</a></li>
			{/foreach}
		{/if}
		</ul>
	{elseif isset($node.cms) && $node.cms|@count > 0}
		<ul>
		{foreach from=$node.cms item=cms name=cmsTreeBranch}
			<li {if $smarty.foreach.cmsTreeBranch.last}class="last"{/if} ><a href="{$cms.link|escape:'html':'UTF-8'}" title="{$cms.meta_title|escape:'html':'UTF-8'}">{$cms.meta_title|escape:'html':'UTF-8'}</a></li>
		{/foreach}
		</ul>
	{/if}
</li>
