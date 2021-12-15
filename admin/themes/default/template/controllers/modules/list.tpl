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

<table id="module-list" class="table">
	<thead>
		<tr>
			<th colspan="4">
				{include file='controllers/modules/filters.tpl'}
			</th>
		</tr>
	</thead>
	{if count($modules)}
		<tbody>
			{foreach from=$modules item=module}
				{capture name="moduleStatutClass"}{if isset($module->id) && $module->id gt 0 && $module->active == 1}module_active{else}module_inactive{/if}{/capture}
				<tr>
					<td class="{{$smarty.capture.moduleStatutClass}} text-center" style="width: 1%;">
						{if (isset($module->id) && $module->id > 0) || !isset($module->type) || $module->type != 'addonsMustHave'}
						<input type="checkbox" name="modules" value="{$module->name|escape:'html':'UTF-8'}" class="noborder" title="{l s='Module %1s '|sprintf:$module->name}"{if !isset($module->confirmUninstall) OR empty($module->confirmUninstall)} data-rel="false"{else} data-rel="{$module->confirmUninstall|addslashes}"{/if}/>
						{/if}
					</td>
					<td class="fixed-width-xs">
						<img width="57" alt="{$module->displayName|escape:'html':'UTF-8'}" title="{$module->displayName|escape:'html':'UTF-8'}" src="{if isset($module->image)}{$module->image}{else}{$modules_uri}/{$module->name}/{$module->logo}{/if}" />
					</td>
					<td>
						<div id="anchor{$module->name|ucfirst}" title="{$module->displayName|escape:'html':'UTF-8'}">
							<div class="text-muted">
								{$module->categoryName}
							</div>
							<div class="module_name">
								<span style="display:none">{$module->name|escape:'html':'UTF-8'}</span>
								{$module->displayName|escape:'html':'UTF-8'}
								<small class="text-muted">v{$module->version} - {l s='by'} {$module->author}</small>
								{if isset($module->type) && $module->type == 'addonsBought'}
								- <span class="module-badge-bought help-tooltip text-warning" data-title="{l s="You bought this module on QloApps Addons. Thank You."}"><i class="icon-pushpin"></i> <small>{l s="Bought"}</small></span>
								{elseif isset($module->type) && $module->type == 'addonsMustHave'}
									- <span class="module-badge-popular help-tooltip text-primary" data-title="{l s="This module is available on QloApps Addons"}"><i class="icon-group"></i> <small>{l s="Popular"}</small></span>
								{elseif isset($module->type) && $module->type == 'addonsPartner'}
									- <span class="module-badge-partner help-tooltip text-warning" data-title="{l s="Official, QloApps certified module. Free, secure and includes updates!"}"><i class="icon-pushpin"></i> <small>{l s="Official"}</small></span>
								{elseif isset($module->id) && $module->id gt 0}
									{if isset($module->version_addons) && $module->version_addons}
										<span class="label label-warning">{l s='Need update'}</span>
									{/if}
								{/if}
							</div>
							<p class="module_description">
								{if isset($module->description) && $module->description ne ''}
									{$module->description}
								{/if}
								{if isset($module->show_quick_view) &&  $module->show_quick_view}
									<br><a href="{$currentIndex|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}&amp;ajax=1&amp;action=GetModuleQuickView&amp;module={$module->name|urlencode}" class="fancybox-quick-view"><i class="icon-search"></i> {l s='Read more'}</a>
								{/if}
							</p>
							{if isset($module->message) && (empty($module->name) !== false) && (!isset($module->type) || ($module->type != 'addonsMustHave' || $module->type !== 'addonsNative'))}<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>{$module->message}</div>{/if}
						</div>
					</td>
					<td class="actions">
						<div class="btn-group-action">
							<div class="btn-group pull-right">
								{if isset($module->type) && $module->type == 'addonsMustHave'}
									<a class="btn btn-default _blank" href="{$module->addons_buy_url|replace:' ':'+'|escape:'html':'UTF-8'}">
										<i class="icon-shopping-cart"></i> &nbsp;{if isset($module->price)}{if $module->price|floatval == 0}{l s='Free'}{elseif isset($module->id_currency)}{displayPrice price=$module->price currency=$module->id_currency}{/if}{/if}
									</a>
								{else}
									{if isset($module->id) && $module->id gt 0}
										{if isset($module->version_addons) && $module->version_addons}
											<a class="btn btn-warning" href="{$module->options.update_url|escape:'html':'UTF-8'}">
												<i class="icon-refresh"></i> {l s='Update it!'}
											</a>
										{elseif !isset($module->not_on_disk)}
											{if $module->optionsHtml|count > 0}
												{assign var=option value=$module->optionsHtml[0]}
												{$option}
											{/if}
										{else}
											<a class="btn btn-danger" {if !empty($module->options.uninstall_onclick)}onclick="{$module->options.uninstall_onclick}"{/if} href="{$module->options.uninstall_url|escape:'html':'UTF-8'}">
												<i class="icon-minus-sign-alt"></i>&nbsp;{l s='Uninstall'}
											</a>
										{/if}
									{else}
										{if isset($module->trusted) && $module->trusted}
											{if $module->trusted == 2}
												<a class="btn btn-success untrustedaddon" href="#" data-target="#moduleNotTrustedCountry" data-toggle="modal" data-link="{$module->options.install_url|escape:'html':'UTF-8'}" data-module-name="{$module->displayName|escape:'html':'UTF-8'}">
													<i class="icon-plus-sign-alt"></i>&nbsp;{l s='Install'}
												</a>
											{else}
												<a class="btn btn-success" href="{$module->options.install_url|escape:'html':'UTF-8'}">
													<i class="icon-plus-sign-alt"></i>&nbsp;{l s='Install'}
												</a>
											{/if}
										{else}
										<a class="btn btn-success untrustedaddon" href="#" data-target="#moduleNotTrusted" data-toggle="modal" data-link="{$module->options.install_url|escape:'html':'UTF-8'}" data-module-display-name="{$module->displayName|escape:'html':'UTF-8'}" data-module-name="{$module->name|escape:'html':'UTF-8'}" data-module-image="{if isset($module->image)}{$module->image}{else}{$modules_uri}/{$module->name}/{$module->logo}{/if}" data-author-name="{$module->author|escape:'html':'UTF-8'}" data-author-uri="{if isset($module->author_uri)}{$module->author_uri|escape:'html':'UTF-8'}{/if}">
											<i class="icon-plus-sign-alt"></i>&nbsp;{l s='Install'}
										</a>
										{/if}
									{/if}

									{if !isset($module->not_on_disk) && isset($module->id)}
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" >
											<span class="caret">&nbsp;</span>
										</button>

										<ul class="dropdown-menu">
											{foreach $module->optionsHtml key=key item=option}
												{if $key != 0}
													{if strpos($option, 'title="divider"') !== false}
														<li class="divider"></li>
													{else}
														<li>{$option}</li>
													{/if}
												{/if}
											{/foreach}
										</ul>
									{elseif !isset($module->not_on_disk) && !isset($module->id)}
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" >
											<span class="caret">&nbsp;</span>
										</button>
										<ul class="dropdown-menu">
											{foreach $module->optionsHtml key=key item=option}
												{if $key != 0}
													{if strpos($option, 'title="divider"') !== false}
														<li class="divider"></li>
													{else}
														<li>{$option}</li>
													{/if}
												{/if}
											{/foreach}
										</ul>
									{elseif isset($module->not_on_disk)}
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" >
											<span class="caret">&nbsp;</span>
										</button>
										<ul class="dropdown-menu">
											{if isset($module->preferences) && isset($module->preferences['favorite']) && $module->preferences['favorite'] == 1}
												<li>
													<a class="action_module action_unfavorite toggle_favorite" data-module="{$module->name}" data-value="0" href="#">
														<i class="icon-star"></i> {l s='Remove from Favorites'}
													</a>
													<a class="action_module action_favorite toggle_favorite" data-module="{$module->name}" data-value="1" href="#" style="display: none;">
														<i class="icon-star"></i> {l s='Mark as Favorite'}
													</a>
												</li>
											{else}
												<li>
													<a class="action_module action_unfavorite toggle_favorite" data-module="{$module->name}" data-value="0" href="#" style="display: none;">
														<i class="icon-star"></i> {l s='Remove from Favorites'}
													</a>
													<a class="action_module action_favorite toggle_favorite" data-module="{$module->name}" data-value="1" href="#">
														<i class="icon-star"></i> {l s='Mark as Favorite'}
													</a>
												</li>
											{/if}
										</ul>
									{else}
										&nbsp;
									{/if}
								{/if}
							</div>
						</div>
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<div class="btn-group pull-left">
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
			{l s='bulk actions'}
			 <span class="caret"></span>
		</button>
		<ul class="dropdown-menu">
			<li>
			 	<a href="#" onclick="modules_management('install')">
					<i class="icon-plus-sign-alt"></i>&nbsp;
					{l s='Install the selection'}
				</a>
			</li>
			<li>
				<a href="#" onclick="modules_management('uninstall')">
					<i class="icon-minus-sign-alt"></i>&nbsp;
					{l s='Uninstall the selection'}
				</a>
			</li>
		</ul>
	</div>
	{else}
		<tbody>
			<tr>
				<td colspan="4" class="list-empty">
					<div class="list-empty-msg">
						<i class="icon-warning-sign list-empty-icon"></i> {l s='No modules available in this section.'}
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	{/if}
<script type="text/javascript">
	$(document).ready(function(){
		$('.fancybox-quick-view').fancybox({
			type: 'ajax',
			autoDimensions: false,
			autoSize: false,
			width: 600,
			height: 'auto',
			helpers: {
				overlay: {
					locked: false
				}
			}
		});
	});
</script>
