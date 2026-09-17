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
<div id="container" class="row">
	<div class="sidebar navigation col-md-3">
		<nav class="list-group categorieList">
		{if count($module_tabs)}
			{foreach $module_tabs as $module_name => $moduleData}
				{if isset($moduleData.tabs) && $moduleData.tabs}
					{foreach $moduleData.tabs as $tab}
						{assign var="tab_active" value=($current_module_name == $module_name && $current_tab == $tab.key)}
						<a class="list-group-item{if $tab_active} active{/if}" href="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}&amp;module={$module_name|escape:'html':'UTF-8'}&amp;tab={$tab.key|escape:'html':'UTF-8'}">{$tab.label}</a>
					{/foreach}
				{else}
					<a class="list-group-item{if $current_module_name == $module_name} active{/if}" href="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}&amp;module={$module_name|escape:'html':'UTF-8'}">{$moduleData.display_name}</a>
				{/if}
			{/foreach}
		{else}
			{l s='No module has been installed.'}
		{/if}
		</nav>
	</div>