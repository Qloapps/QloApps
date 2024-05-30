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
<div class="panel">
	<div class="panel-heading">
		<i class="icon-list-ul"></i>
		{l s='Modules list'}
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="btn-group pull-right">
				<a class="btn btn-default {if !isset($smarty.get.select)} active{/if}" href="index.php?controller={$smarty.get.controller|htmlentities}&amp;token={$smarty.get.token|htmlentities}">
					<i class="icon-list"></i>
					{l s='Normal view'} 
				</a>
				<a class="btn btn-default {if $smarty.get.select == 'favorites'} active{/if}" href="javascript:void(0);">
					<i class="icon-star"></i>
					{l s='Favorites view'}
				</a>
			</div>
		</div>
	</div>
	<div class="row">
		<div id="container" class="col-lg-12">
			<div id="moduleContainer">
				<table class="table">
					<thead>
						<tr class="nodrag nodrop">
							<th colspan="2"></th>
							<th>{l s='Module'}</th>
							<th>{l s='Tab'}</th>
							<th>{l s='Categories'}</th>
							<th>{l s='Interest'}</th>
							<th>{l s='Favorite'}</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$modules key=km item=module}
							{capture name="moduleStatusClass"}
								{if isset($module->id) && $module->id gt 0}
									module_active
								{else}
									module_inactive
								{/if}
							{/capture}
							<tr>
								<td width="10px" class="{$smarty.capture.moduleStatusClass}"></td>
								<td width="40px">
									<img src="{if isset($module->image)}{$module->image}{else}../modules/{$module->name}/{$module->logo}{/if}" width="32" height="32" />
								</td>
								<td class="moduleName">
									<h4>{$module->displayName}</h4>
									<span class="moduleFavDesc text-muted">{$module->description|truncate:80:'...'}</span>
								</td>
								<td width="240px">
									{assign var="module_name" value=$module->name}
									<select name="t_{$module->name}" multiple="multiple" class="chosen moduleTabPreferencesChoise">
										{foreach $tabs AS $t}
											{if $t.active}
												<option {if isset($tab_modules_preferences.$module_name) && in_array($t.id_tab, $tab_modules_preferences.$module_name)} selected="selected" {/if} class="group" value="{$t.id_tab}">{if $t.name eq ''}{$t.class_name}{else}{$t.name}{/if}</option>
												{foreach from=$t.sub_tabs item=t2}
													{if $t2.active}
														{assign var="id_tab" value=$t.id_tab}
														<option {if isset($tab_modules_preferences.$module_name) && in_array($t2.id_tab, $tab_modules_preferences.$module_name)} selected="selected" {/if} value="{$t2.id_tab}">{if $t2.name eq ''}{$t2.class_name}{else}{$t2.name|escape:'html':'UTF-8'}{/if}</option>
													{/if}
												{/foreach}
											{/if}
										{/foreach}
									</select>
								</td>
								<td>
									<span>{$module->categoryName}</span>
								</td>
								<td>
									<select name="i_{$module->name}" class="moduleFavorite">
										<option value="" selected="selected">-</option>
										<option value="1" {if isset($module->preferences.interest) && $module->preferences.interest eq '1'}selected="selected"{/if}>{l s='Yes'}</option>
										<option value="0" {if isset($module->preferences.interest) && $module->preferences.interest eq '0'}selected="selected"{/if}>{l s='No'}</option>
									</select>
								</td>
								<td>
									<select name="f_{$module->name}" class="moduleFavorite">
										<option value="" selected="selected">-</option>
										<option value="1" {if isset($module->preferences.favorite) && $module->preferences.favorite eq '1'}selected="selected"{/if}>{l s='Yes'}</option>
										<option value="0" {if isset($module->preferences.favorite) && $module->preferences.favorite eq '0'}selected="selected"{/if}>{l s='No'}</option>
									</select>
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
