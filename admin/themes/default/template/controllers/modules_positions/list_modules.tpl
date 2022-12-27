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
	var come_from = 'AdminModulesPositions';
</script>

<div>{block name="leadin"}{/block}</div>

{if !$can_move}
					<p class="alert alert-warning">
						{l s='If you want to order/move the following data, please select a shop from the shop list.'}
					</p>
{/if}

<div class="row">
	<div class="col-lg-9">
		<div class="panel">
			<form class="well form-horizontal" id="position_filer">
				<div class="row">
					<div class="form-group col-lg-6 col-sm-12">
						<label class="control-label col-lg-4" style="text-align: left">{l s='Show'}</label>
						<div class="col-lg-7">
							<select id="show_modules" class="filter" style="width: 100%;">
								<option value="all">{l s='All modules'}&nbsp;</option>
								{foreach $modules as $module}
									<option value="{$module->id|intval}"{if $display_key == $module->id} selected="selected"{/if}>{$module->displayName|escape:'html':'UTF-8'}</option>
								{/foreach}
							</select>
						</div>
					</div>
					<div class="form-group col-lg-6 col-sm-12">
						<label class="control-label col-lg-offset-1 col-lg-4" style="text-align: left">{l s='Search for a hook'}</label>
						<div class="col-lg-7">
							<div class="input-group">
								<div class="input-group-addon"><i class="icon icon-search"></i></div>
								<input type="text" class="form-control" id="hook_search" name="hook_search" placeholder="">
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
							<p class="checkbox">
								<label class="control-label" for="hook_position">
									<input type="checkbox" id="hook_position"/>
									{l s='Display non-positionable hooks'}
								</label>
							</p>
					</div>
				</div>
			</form>
			<div id="modulePosition">
				<form method="post" action="{$url_submit|escape:'html':'UTF-8'}" >
					{foreach $hooks as $hook}
					<section class="hook_panel {if $hook['position'] == 0}hook_position{/if}" {if $hook['position'] == 0}style="display:none;"{/if}>
						<a name="{$hook['name']}"></a>
						<header class="hook_panel_header">
							<span class="hook_name">{$hook['name']}</span>
							<!-- <span class="hook_title">{$hook['title']}</span> -->
							<span class="badge pull-right">
								{if $hook['module_count'] && $can_move}
								<input type="checkbox" id="Ghook{$hook['id_hook']}" onclick="hookCheckboxes({$hook['id_hook']}, 0, this)"/>
								{/if}
								{$hook['module_count']} {if $hook['module_count'] > 1}{l s='Modules'}{else}{l s='Module'}{/if}
							</span>

							{if !empty($hook['description'])}
							<div class="hook_description">{$hook['description']}</div>
							{/if}
						</header>

						{if $hook['module_count']}
						<section class="module_list">
						<ul class="list-unstyled{if $hook['modules']|count > 1} sortable{/if}">

							{foreach $hook['modules'] as $position => $module}
							{if isset($module['instance'])}
							<li id="{$hook['id_hook']|intval}_{$module['instance']->id|intval}" class="module_position_{$module['instance']->id|intval} module_list_item{if $can_move && $hook['module_count'] >= 2} draggable{/if}">
								<div class="module_col_select">
									<input type="checkbox" id="mod{$hook['id_hook']|intval}_{$module['instance']->id|intval}" class="modules-position-checkbox hook{$hook['id_hook']}" onclick="hookCheckboxes({$hook['id_hook']}, 1, this)" name="unhooks[]" value="{$hook['id_hook']}_{$module['instance']->id}"/>
								</div>
								{if !$display_key}
								<div class="module_col_position{if $can_move && $hook['module_count'] >= 2} dragHandle{/if}" id="td_{$hook['id_hook']|intval}_{$module['instance']->id}">
									<span class="positions">{$module@iteration}</span>
									{if $can_move}
									<div class="btn-group-vertical">
										<a class="btn btn-default btn-xs" href="{$current|escape:'html':'UTF-8'}&amp;id_module={$module['instance']->id|intval}&amp;id_hook={$hook['id_hook']|intval}&amp;direction=0&amp;token={$token|escape:'html':'UTF-8'}&amp;changePosition#{$hook['name']}">
											<i class="icon-chevron-up"></i>
										</a>

										<a class="btn btn-default btn-xs" href="{$current|escape:'html':'UTF-8'}&amp;id_module={$module['instance']->id|intval}&amp;id_hook={$hook['id_hook']|intval}&amp;direction=1&amp;token={$token|escape:'html':'UTF-8'}&amp;changePosition#{$hook['name']}">
											<i class="icon-chevron-down"></i>
										</a>
									</div>
									{/if}
								</div>
								{/if}
								<div class="module_col_icon">
									<img width="57" src="../modules/{$module['instance']->name}/logo.png" alt="{$module['instance']->name|stripslashes}" />
								</div>
								<div class="module_col_infos">
									<span class="module_name">
										{$module['instance']->displayName|stripslashes} {if $module['instance']->version}
										<small class="text-muted">&nbsp;-&nbsp;v{if $module['instance']->version|intval == $module['instance']->version}{sprintf('%.1f', $module['instance']->version)}{else}{$module['instance']->version|floatval}{/if}</small>{/if}
									</span>
									<div class="module_description">{$module['instance']->description}</div>
								</div>
								<div class="module_col_actions">
									<!-- <div class="lab_modules_positions" for="mod{$hook['id_hook']}_{$module['instance']->id}"></div> -->
									<div class="btn-group">
										<a class="btn btn-default" href="{$current|escape:'html':'UTF-8'}&amp;id_module={$module['instance']->id|intval}&amp;id_hook={$hook['id_hook']}&amp;editGraft{if $display_key}&amp;show_modules={$display_key}{/if}&amp;token={$token|escape:'html':'UTF-8'}">
											<i class="icon-pencil"></i>
											{l s='Edit'}
										</a>
										<a class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											<span class="caret"></span>&nbsp;
										</a>
										<ul class="dropdown-menu">
											<li>
												<a href="{$current|escape:'html':'UTF-8'}&amp;id_module={$module['instance']->id|intval}&amp;id_hook={$hook['id_hook']}&amp;deleteGraft{if $display_key}&amp;show_modules={$display_key}{/if}&amp;token={$token|escape:'html':'UTF-8'}">
													<i class="icon-minus-sign-alt"></i>
													{l s='Unhook'}
												</a>
											</li>
										</ul>
									</div>
								</div>
							</li>
							{/if}
						{/foreach}
						</ul>
						</section>
	{else}
							<!-- <p>{l s='No module was found for this hook.'}</p> -->
	{/if}
					</section>
{/foreach}
					<div id="unhook_button_position_bottom">
						<button type="submit" class="btn btn-default" name="unhookform">
							<i class="icon-minus-sign-alt"></i>
							{l s='Unhook the selection'}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="col-lg-3">
		<div class="panel">
			<h3><i class="icon-eye-open"></i> {l s='Live Edit'}</h3>
			{if $live_edit}
				<p>{l s='You have to select a shop to use Live Edit'}</p>
			{else}
				<p>{l s='Click here to be redirected to the front office of your website where you can move and delete modules directly.'}</p>
					<a class="btn btn-default _blank" href="{$url_live_edit|escape:'html':'UTF-8'}">
						<i class="icon-edit"></i>
						{l s='Run Live Edit'}
					</a>
			{/if}
		</div>
		<div class="panel" id="modules-position-selection-panel">
			<h3><i class="icon-check"></i> {l s='Selection'}</h3>
			<p>
				<span id="modules-position-single-selection">{l s='1 module selected'}</span>
				<span id="modules-position-multiple-selection">
					<span id="modules-position-selection-count"></span> {l s='modules selected'}
				</span>
			</p>
			<div class="text-center">
				<button class="btn btn-default"><i class="icon-remove"></i> {l s='Unhook the selection'}</button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('.sortable').sortable({
		forcePlaceholderSize: true
	}).bind('sortupdate', function(e, ui) {
		var ids = ui.item.attr('id').split('_');
		var way = (ui.start_index < ui.end_index)? 1 : 0;
		var data = ids[0]+'[]=';

		$.each(e.target.children, function(index, element) {
			data += '&'+ids[0]+'[]='+$(element).attr('id');
		});

		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			async: false,
			url: currentIndex + '&token=' + token + '&' + 'rand=' + new Date().getTime(),
			data: data + '&action=updatePositions&id_hook='+ids[0]+'&id_module='+ids[1]+'&way='+way+'&ajax=1' ,
			success: function(data) {
				start = 0;

				$.each(e.target.children, function(index, element) {
					$(element).find('.positions').html(++start);
				});

				showSuccessMessage(update_success_msg);
			}
		});
	});
</script>
