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
	<ul class=" nav nav-pills">
		<li><a href="#mod-alert" data-toggle="tab">{l s="Alert"}&nbsp;<span class="badge {if $module_alerts|@count}badge-danger{else}badge-success{/if}">{$module_alerts|@count}</span></a></li>
		<li><a href="#mod-update" data-toggle="tab">{l s="Updates"}&nbsp;<span class="badge {if $upgrade_available|@count}badge-danger{else}badge-success{/if}">{$upgrade_available|@count}</span></a></li>
	</ul>
	<div class="tab-content">
		<div id="mod-alert" class="tab-pane">
		<hr>
			{if $module_alerts|@count}
				<div class="alert alert-warning">
					{l s='There are %d alerts regarding your modules.'  sprintf=count($module_alerts)}
					<ul>
					{foreach from=$module_alerts item='alert'}
						<li>{$alert}</li>
					{/foreach}
					</ul>
				</div>
			{else}
				<div class="alert alert-success">
					{l s='There are no alerts regarding your modules.'}
				</div>
			{/if}
		</div>
		<div id="mod-update" class="tab-pane">
			<hr>
			{if $upgrade_available|@count}
				<div class="alert alert-info">
					{l s='An upgrade is available for some of your modules!'}
					<ul>
					{foreach from=$upgrade_available item='module'}
						<li><a href="{$currentIndex|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}&amp;anchor={$module.anchor|escape:'html':'UTF-8'}"><b>{$module.displayName|escape:'html':'UTF-8'}</b></a></li>
					{/foreach}
					</ul>
				</div>
			{else}
				<div class="alert alert-success">
					{l s='All modules are up to date!'}
				</div>
			{/if}
		</div>
	</div>
</div>
<div class="alert bg-info">
	<div class="row modules-addons-info">
		<h4>{l s='Explore all QloApps addons'} <a class="btn btn-default _blank" href="https://qloapps.com/addons/"> {l s='QloApps addons'}</a></h4>
	</div>
</div>

{$kpis}
{if $add_permission eq '1'}
<div id="module_install" class="row" style="{if !isset($smarty.post.downloadflag)}display: none;{/if}">
	<div class="panel col-lg-12">
		<form id="module_install_form" action="{$currentIndex|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" class="form-horizontal">
			<h3>{l s='Add a new module'}</h3>
			<p class="alert alert-info">{l s='The module must either be a Zip file (.zip) or a tarball file (.tar, .tar.gz, .tgz).'}</p>
			<div class="form-group">
				<label for="file" class="control-label col-lg-3">
					<span class="label-tooltip" data-toggle="tooltip" title="{l s='Upload a module from your computer.'}">
						{l s='Module file'}
					</span>
				</label>
				<div class="col-sm-9">
					<div class="row">
						<div class="col-lg-7">
							<input id="file" type="file" name="file" class="hide" />
							<div class="dummyfile input-group">
								<span class="input-group-addon"><i class="icon-file"></i></span>
								<input id="file-name" type="text" class="disabled" name="filename" readonly />
								<span class="input-group-btn">
									<button id="file-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
										<i class="icon-folder-open"></i> {l s='Choose a file'}
									</button>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-lg-9 col-lg-push-3">
					<button class="btn btn-default" type="submit" name="uploadAndInstall">
						<i class="icon-upload-alt" ></i>
						{l s='Upload  and install this module'}
					</button>
				</div>
			</div>
			<div id="module_install_status" class="form-group" style="display:none">
				<div class="col-lg-6 col-lg-push-3">
					<ul class="list-unstyled">
						<li class="mod_status_upload" style="display:none"><i class="icon-refresh icon-spin"></i>&nbsp;{l s='Uploading module.'}</li>
						<li class="mod_status_check" style="display:none"><i class="icon-refresh icon-spin"></i>&nbsp;{l s='Checking module if module is trusted.'}</li>
						<li class="mod_status_install" style="display:none"><i class="icon-refresh icon-spin"></i>&nbsp;{l s='Installing module.'}</li>
						<li class="mod_status_update" style="display:none"><i class="icon-refresh icon-spin"></i>&nbsp;{l s='Module already installed, checking and installing updates.'}</li>
						<li class="mod_status_rollback" style="display:none"><i class="icon-refresh icon-spin"></i>&nbsp;{l s='Rolling back changes.'}</li>
					</ul>
					<div class="install_msg"></div>
					<div class="install_errors" style="display:none">
						{l s='Errors.'}
						<div class="list"></div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
{/if}
<div class="panel">
	<div class="panel-heading">
		<i class="icon-list-ul"></i>
		{l s='Modules list'}
	</div>
	<!--start sidebar module-->
	<div class="row">
		<div class="categoriesTitle col-md-3">
			<div class="list-group">
				<form id="filternameForm" method="post" class="list-group-item form-horizontal">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="icon-search"></i>
						</span>
						<input class="form-control" placeholder="{l s='Search'}" type="text" value="" name="moduleQuicksearch" id="moduleQuicksearch" autocomplete="off" />
					</div>
				</form>
				<a class="categoryModuleFilterLink list-group-item {if isset($categoryFiltered.favorites)}active{/if}" href="{$currentIndex|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}&amp;filterCategory=favorites" id="filter_favorite">
					{l s='Favorites'} <span id="favorite-count" class="badge pull-right">{$nb_modules_favorites}</span>
				</a>
				<a class="categoryModuleFilterLink list-group-item {if count($categoryFiltered) lte 0}active{/if}" href="{$currentIndex|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}&amp;unfilterCategory=yes" id="filter_all">
					{l s='All'} <span class="badge pull-right">{$nb_modules}</span>
				</a>
				{foreach from=$list_modules_categories item=module_category key=module_category_key}
					<a class="categoryModuleFilterLink list-group-item {if isset($categoryFiltered[$module_category_key])}active{/if}" href="{$currentIndex|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}&amp;{if isset($categoryFiltered[$module_category_key])}un{/if}filterCategory={$module_category_key}" id="filter_{$module_category_key}">
						{$module_category.name} <span class="badge pull-right">{$module_category.nb}</span>
					</a>
				{/foreach}
			</div>
		</div>
		<div id="moduleContainer" class="col-md-9">
			{include file='controllers/modules/list.tpl'}
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('#file-selectbutton').click(function(e){
			$('#file').trigger('click');
		});
		$('#file-name').click(function(e){
			$('#file').trigger('click');
		});
		$('#file').change(function(e){
			var val = $(this).val();
			var file = val.split(/[\\/]/);
			$('#file-name').val(file[file.length-1]);
		});
	});
</script>
