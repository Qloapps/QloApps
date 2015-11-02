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
<script type="text/javascript">
	$(document).ready(function() {
		
		var id_tab_parentmodule = {$id_tab_parentmodule|intval};
		var id_tab_module = {$id_tab_module|intval};
		
		$('tr.child-'+id_tab_parentmodule+' > td > input.view.'+id_tab_module).change( function () {

			if (!$(this).prop('checked'))
			{
				$('#table_module_2 thead th:eq(1) input').trigger('click');
				if ($('#table_module_2 thead th:eq(1) input').prop('checked'))
					$('#table_module_2 thead th:eq(1) input').trigger('click');
			}
		});
		
		$('tr.child-'+id_tab_parentmodule+' > td > input.edit.'+id_tab_module).change( function () {

			if (!$(this).prop('checked'))
			{
				$('#table_module_2 thead th:eq(2) input').trigger('click');
				if ($('#table_module_2 thead th:eq(2) input').prop('checked'))
					$('#table_module_2 thead th:eq(2) input').trigger('click');
			}
		});
		
		$('div.productTabs').find('a').each(function() {
			$(this).attr('href', '#');
		});

		$('div.productTabs a').click(function() {
			var id = $(this).attr('id');
			$('.nav-profile').removeClass('selected');
			$(this).addClass('selected active');
			$(this).siblings().removeClass('active');
			$('.tab-profile').hide()
			$('.'+id).show();
		});

		$('.ajaxPower').change(function(){
			var tout = $(this).data('rel').split('||');
			var id_tab = tout[0];
			var id_profile = tout[1];
			var perm = tout[2];
			var enabled = $(this).is(':checked')? 1 : 0;
			var tabsize = tout[3];
			var tabnumber = tout[4];
			var table = 'table#table_'+id_profile;

			if (perm == 'all' && $(this).parent().parent().hasClass('parent'))
			{
				if (enabled)
					$(this).parent().parent().parent().find('.child-'+id_tab+' input[type=checkbox]').attr('checked', 'checked');
				else
					$(this).parent().parent().parent().find('.child-'+id_tab+' input[type=checkbox]').removeAttr('checked');
				$.ajax({
					url: "{$link->getAdminLink('AdminAccess')|addslashes}",
					cache: false,
					data : {
						ajaxMode : '1',
						id_tab: id_tab,
						id_profile: id_profile,
						perm: perm,
						enabled: enabled,
						submitAddAccess: '1',
						addFromParent: '1',
						action: 'updateAccess',
						ajax: '1',
						token: '{getAdminToken tab='AdminAccess'}'
					},
					success : function(res,textStatus,jqXHR)
					{
						try {
							if (res == 'ok')
								showSuccessMessage("{l s='Update successful'}");
							else
								showErrorMessage("{l s='Update error'}");
						} catch(e) {
							jAlert('Technical error');
						}
					}
				});
			}
			perfect_access_js_gestion(this, perm, id_tab, tabsize, tabnumber, table);

			$.ajax({
				url: "{$link->getAdminLink('AdminAccess')|addslashes}",
				cache: false,
				data : {
					ajaxMode : '1',
					id_tab: id_tab,
					id_profile: id_profile,
					perm: perm,
					enabled: enabled,
					submitAddAccess: '1',
					action: 'updateAccess',
					ajax: '1',
					token: '{getAdminToken tab='AdminAccess'}'
				},
				success : function(res,textStatus,jqXHR)
				{
					try
					{
						if (res == 'ok')
							showSuccessMessage("{l s='Update successful'}");
						else
							showErrorMessage("{l s='Update error'}");
					}
					catch(e)
					{
						jAlert('Technical error');
					}
				}
			});
		});

		$(".changeModuleAccess").change(function(){
			var tout = $(this).data('rel').split('||');
			var id_module = tout[0];
			var perm = tout[1];
			var id_profile = tout[2];
			var enabled = $(this).is(':checked') ? 1 : 0;
			var enabled_attr = $(this).is(':checked') ? true : false;
			var table = 'table#table_module_'+id_profile;

			if (id_module == -1)
				$(table+' .ajax-ma-'+perm).each(function(key, value) {
					$(this).attr("checked", enabled_attr);
				});
			else if (!enabled)
				$(table+' #ajax-ma-'+perm+'-master').each(function(key, value) {
					$(this).attr("checked", enabled_attr);
				});

			$.ajax({
				url: "{$link->getAdminLink('AdminAccess')|addslashes}",
				cache: false,
				data : {
					ajaxMode: '1',
					id_module: id_module,
					perm: perm,
					enabled: enabled,
					id_profile: id_profile,
					changeModuleAccess: '1',
					action: 'updateModuleAccess',
					ajax: '1',
					token: '{getAdminToken tab='AdminAccess'}'
				},
				success : function(res,textStatus,jqXHR)
				{
					try
					{
						if (res == 'ok')
							showSuccessMessage("{l s='Update successful'}");
						else
							showErrorMessage("{l s='Update error'}");
					}
					catch(e)
					{
						jAlert('Technical error');
					}
				}
			});
		});

	});
</script>
{if $show_toolbar}
	{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
	<div class="leadin">{block name="leadin"}{/block}</div>
{/if}
<div class="row">
	<div class="productTabs col-lg-2">
		<div class="tab list-group">
		{foreach $profiles as $profile}
			<a class="list-group-item nav-profile {if $profile.id_profile == $current_profile}active{/if}" id="profile-{$profile.id_profile}" href="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}&amp;id_profile={$profile.id_profile}">{$profile.name}</a>
		{/foreach}
		</div>
	</div>
	<form id="{$table}_form" class="defaultForm form-horizontal col-lg-10" action="{$current|escape:'html':'UTF-8'}&amp;{$submit_action}=1&amp;token={$token|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data">
		{if $form_id}
			<input type="hidden" name="{$identifier}" id="{$identifier}" value="{$form_id}" />
		{/if}
		{assign var=tabsize value=count($tabs)}
		{foreach $tabs AS $tab}
			{if $tab.id_tab > $tabsize}
				{assign var=tabsize value=$tab.id_tab}
			{/if}
		{/foreach}
		{foreach $profiles as $profile}
		<div class="profile-{$profile.id_profile} tab-profile" style="display:{if $profile.id_profile != $current_profile}none{/if}">
			<div class="row">			
			{if $profile.id_profile != $admin_profile}
				<div class="col-lg-6">	
					<div class="panel">
						<h3>{l s='Menu'}</h3>
						<table class="table" id="table_{$profile.id_profile}">
							<thead>
								<tr>
									<th></th>
									<th>
										<input type="checkbox" name="1" class="viewall ajaxPower"{if $access_edit == 1} data-rel="-1||{$profile.id_profile}||view||{$tabsize}||{count($tabs)}"{else} disabled="disabled"{/if}/>
										{l s='View'}
									</th>
									<th>
										<input type="checkbox" name="1" class="addall ajaxPower"{if $access_edit == 1} data-rel="-1||{$profile.id_profile}||add||{$tabsize}||{count($tabs)}"{else} disabled="disabled"{/if}/>
										{l s='Add'}
									</th>
									<th>
										<input type="checkbox" name="1" class="editall ajaxPower"{if $access_edit == 1} data-rel="-1||{$profile.id_profile}||edit||{$tabsize}||{count($tabs)}"{else} disabled="disabled"{/if}/>
										{l s='Edit'}
									</th>
									<th>
										<input type="checkbox" name="1" class="deleteall ajaxPower"{if $access_edit == 1} data-rel="-1||{$profile.id_profile}||delete||{$tabsize}||{count($tabs)}"{else} disabled="disabled"{/if}/>
										{l s='Delete'}
									</th>
									<th>
										<input type="checkbox" name="1" class="allall ajaxPower"{if $access_edit == 1} data-rel="-1||{$profile.id_profile}||all||{$tabsize}||{count($tabs)}"{else} disabled="disabled"{/if}/>
										{l s='All'}
									</th>
								</tr>
							</thead>
							<tbody>
								{if !count($tabs)}
									<tr>
										<td colspan="6">{l s='No menu'}</td>
									</tr>
								{else}
									{foreach $tabs AS $tab}
										{assign var=access value=$accesses[$profile.id_profile]}
										{if !$tab.id_parent OR $tab.id_parent == -1}
											{assign var=is_child value=false}
											{assign var=result_accesses value=0}
											<tr{if !$is_child} class="parent"{/if}>
												<td{if !$is_child} class="bold"{/if}>{if $is_child} &raquo; {/if}<strong>{$tab.name}</strong></td>
												{foreach $perms as $perm}
													{if $access_edit == 1}
														<td>
															<input type="checkbox" data-rel="{$access[$tab.id_tab]['id_tab']}||{$profile.id_profile}||{$perm}||{$tabsize}||{count($tabs)}" class="ajaxPower {$perm} {$access[$tab.id_tab]['id_tab']}"{if $access[$tab.id_tab][$perm] == 1} checked="checked"{/if}/>
														</td>
													{else}
														<td>
															<input type="checkbox" disabled="disabled"{if $access[$tab.id_tab][$perm] == 1} checked="checked"{/if}/>
														</td>
													{/if}
													{assign var=result_accesses value=$result_accesses + $access[$tab.id_tab][$perm]}
												{/foreach}
												<td>
													<input type="checkbox"{if $access_edit == 1} data-rel="{$access[$tab.id_tab]['id_tab']}||{$profile.id_profile}||all||{$tabsize}||{count($tabs)}" class="ajaxPower all {$access[$tab.id_tab]['id_tab']}"{else} class="all {$access[$tab.id_tab]['id_tab']}" disabled="disabled"{/if}{if $result_accesses == 4} checked="checked"{/if}/>
												</td>
											</tr>
											{foreach $tabs AS $child}
												{if $child.id_parent === $tab.id_tab}
													{if isset($access[$child.id_tab])}
														{assign var=is_child value=true}
														{assign var=result_accesses value=0}
														<tr class="child-{$child.id_parent}">
															<td{if !$is_child} class="bold"{/if}>{if $is_child} &raquo; {/if}{$child.name}</td>
															{foreach $perms as $perm}
																{if $access_edit == 1}
																	<td>
																		<input type="checkbox" data-rel="{$access[$child.id_tab]['id_tab']}||{$profile.id_profile}||{$perm}||{$tabsize}||{count($tabs)}" class="ajaxPower {$perm} {$access[$child.id_tab]['id_tab']}"{if $access[$child.id_tab][$perm] == 1} checked="checked"{/if}/>
																	</td>
																{else}
																	<td>
																		<input type="checkbox" disabled="disabled"{if $access[$child.id_tab][$perm] == 1} checked="checked"{/if}/>
																	</td>
																{/if}
																{assign var=result_accesses value=$result_accesses + $access[$child.id_tab][$perm]}
															{/foreach}
															<td>
																<input type="checkbox"{if $access_edit == 1} data-rel="{$access[$child.id_tab]['id_tab']}||{$profile.id_profile}||all||{$tabsize}||{count($tabs)}" class="ajaxPower all {$access[$child.id_tab]['id_tab']}"{else} class="all {$access[$child.id_tab]['id_tab']}" disabled="disabled"{/if}{if $result_accesses == 4} checked="checked"{/if}/>
															</td>
														</tr>
													{/if}
												{/if}
											{/foreach}

										{/if}

									{/foreach}
								{/if}
							</tbody>
						</table>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="panel">
						<h3>{l s='Modules'}</h3>
						<table class="table" id="table_module_{$profile.id_profile}">
							<thead>
								<tr>
									<th></th>
									<th>
										<input type="checkbox"{if $access_edit == 1} class="changeModuleAccess" data-rel="-1||view||{$profile.id_profile}"{else} disabled="disabled"{/if}/> {l s='View'}
									</th>
									<th>
										<input type="checkbox"{if $access_edit == 1} class="changeModuleAccess" data-rel="-1||configure||{$profile.id_profile}"{else} disabled="disabled"{/if}/> {l s='Configure'}
									</th>
									<th>
										<input type="checkbox"{if $access_edit == 1} class="changeModuleAccess" data-rel="-1||uninstall||{$profile.id_profile}"{else} disabled="disabled"{/if}/> {l s='Uninstall'}
									</th>
								</tr>
							</thead>
							<tbody>
								{if !count($modules)}
									<tr>
										<td colspan="3">{l s='No modules are installed'}</td>
									</tr>
								{else}
									{foreach $modules[$profile.id_profile] AS $module}
										<tr>
											<td>&raquo; {$module.name}</td>
											<td>
												<input type="checkbox" value="1"{if $module.view == true} checked="checked"{/if}{if $access_edit == 1} class="ajax-ma-view changeModuleAccess" data-rel="{$module.id_module}||view||{$profile.id_profile}"{else} class="ajax-ma-view" disabled="disabled"{/if}/>
											</td>
											<td>
												<input type="checkbox" value="1"{if $module.configure == true} checked="checked"{/if}{if $access_edit == 1} class="ajax-ma-configure changeModuleAccess" data-rel="{$module.id_module}||configure||{$profile.id_profile}"{else} class="ajax-ma-configure" disabled="disabled"{/if}/>
											</td>
											<td>
												<input type="checkbox" value="1"{if $module.uninstall == true} checked="checked"{/if}{if $access_edit == 1} class="ajax-ma-uninstall changeModuleAccess" data-rel="{$module.id_module}||uninstall||{$profile.id_profile}"{else} class="ajax-ma-uninstall" disabled="disabled"{/if}/>
											</td>
										</tr>
									{/foreach}
								{/if}
							</tbody>
						</table>
					</div>
				</div>
			{else}
				<div class="col-lg-12">
					<div class="panel">
						{l s='Administrator permissions cannot be modified.'}
					</div>
				</div>
			{/if}
			</div>
		</div>
		{/foreach}
	</form>
</div>