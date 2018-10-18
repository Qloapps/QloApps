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


{extends file="page_header_toolbar.tpl"}

{block name=pageTitle}
<h2 class="page-title">
	{l s='List of modules'}
</h2>
{/block}

{block name=toolbarBox}
<div class="page-bar toolbarBox">
	<div class="btn-toolbar">
		<ul class="nav nav-pills pull-right">
			{* {if isset($upgrade_available) && $upgrade_available|@count}
				{assign var='modules' value=''}
				{foreach from=$upgrade_available item='module'}
					{assign var='modules' value=$modules|cat:$module.name:'|'}
				{/foreach}
				{assign var='modules' value=$modules|substr:0:-1}
				<li>
					<a id="desc-module-update-all" class="toolbar_btn" href="{$currentIndex|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}&amp;updateAll=1" title="{l s='Update all'}">
						<i class="process-icon-refresh"></i>
						<div>{l s='Update all'}</div>
					</a>
				</li>
			{else}
				<li>
					<a id="desc-module-check-and-update-all" class="toolbar_btn" href="{$currentIndex|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}&amp;check=1" title="{l s='Check for update'}">
						<i class="process-icon-refresh"></i>
						<div>{l s='Check for update'}</div>
					</a>
				</li>
			{/if} *}
			{if $add_permission eq '1' && ($context_mode != Context::MODE_HOST)}
			<li>
				<a id="desc-module-new" class="toolbar_btn anchor" href="#" onclick="$('#module_install').slideToggle();" title="{l s='Add a new module'}">
					<i class="process-icon-new"></i>
					<div>{l s='Add a new module'}</div>
				</a>
			</li>
			{else}
			<li>
				<a id="desc-module-new" class="toolbar_btn" href="{$link->getAdminLink('AdminModules')}&addnewmodule" title="{l s='Add a new module'}">
					<i class="process-icon-new"></i>
					<div>{l s='Add a new module'}</div>
				</a>
			</li>
			{/if}
			{if isset($help_link)}
			{* <li>
				<a class="toolbar_btn  btn-help" href="{$help_link|escape}" title="{l s='Help'}">
					<i class="process-icon-help"></i>
					<div>{l s='Help'}</div>
				</a>
			</li> *}
			{/if}
		</ul>
	</div>
</div>
{/block}
