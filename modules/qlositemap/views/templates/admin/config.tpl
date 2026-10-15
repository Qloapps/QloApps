{**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}

<div class="panel">
	<div class="panel-heading"><i class="icon-sitemap"></i> {l s='Your sitemaps' mod='qlositemap'}</div>
		{if $qsm_sitemap_links}
			<p>{l s='Please set up the following sitemap URL in your Google Search Console account:' mod='qlositemap'}<br><a href="{$qsm_index_url|escape:'html':'UTF-8'}" target="_blank">{$qsm_index_url|escape:'html':'UTF-8'}</a></p>
			<p>{l s='A separate sitemap file is generated for each active language.' mod='qlositemap'}</p>
			<p>{l s='The above URL is the master sitemap file. It refers to the following sub-sitemap files:' mod='qlositemap'}</p>
			<ul class="list-unstyled">
				{foreach from=$qsm_sitemap_links item=sitemap_link}
					<li><a href="{$qsm_store_url|escape:'html':'UTF-8'}{$sitemap_link.link|escape:'html':'UTF-8'}" target="_blank">{$sitemap_link.link|escape:'html':'UTF-8'}</a></li>
				{/foreach}
			</ul>
			<p>{l s='Your last update was made on this date:' mod='qlositemap'} {$qsm_last_export|escape:'html':'UTF-8'}</p>
		{else}
			<p>{l s='No sitemap has been generated yet.' mod='qlositemap'}</p>
		{/if}
</div>

<form method="post" action="{$qsm_form_action|escape:'html':'UTF-8'}" class="form-horizontal">
	<div class="panel">
		<div class="panel-heading"><i class="icon-wrench"></i> {l s='Configure your sitemap' mod='qlositemap'}</div>
		<div class="alert alert-info">
			<strong>{l s='CMS pages:' mod='qlositemap'}</strong>
			{l s='You can manage which CMS pages are included in the sitemap in two ways. If "CMS pages" is selected here, no CMS page is included in the sitemap. To leave out only specific CMS pages, set "Indexation by search engines" to No on the page itself (Preferences > CMS).' mod='qlositemap'}
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="qsm_frequency"><span class="label-tooltip" data-toggle="tooltip" title="{l s='How often do you update your property website?' mod='qlositemap'}">{l s='Sitemap frequency' mod='qlositemap'}</span></label>
			<div class="col-lg-3">
				<select name="QLOSITEMAP_FREQUENCY" id="qsm_frequency" class="form-control">
					{foreach from=$qsm_frequencies key=freq_value item=freq_label}
						<option value="{$freq_value|escape:'html':'UTF-8'}"{if $freq_value == $qsm_frequency} selected="selected"{/if}>{$freq_label|escape:'html':'UTF-8'}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title="{l s='QloApps pages you do not want to include in your sitemap files' mod='qlositemap'}">{l s='Excluded QloApps pages' mod='qlositemap'}</span></label>
			<div class="col-lg-9">
				{if $qsm_pages.core}
					<div class="checkbox"><label><input type="checkbox" class="qsm-check-all" data-group="qsm-page-core"> <strong>{l s='Select all' mod='qlositemap'}</strong></label></div>
					<div class="qsm-page-list">
					{foreach from=$qsm_pages.core item=page}
						<div class="checkbox qsm-page-item">
							<label>
								<input type="checkbox" class="qsm-page-core" name="QLOSITEMAP_DISABLED_PAGES[]" value="{$page.page|escape:'html':'UTF-8'}"{if $page.excluded} checked="checked"{/if}>
								<strong>{$page.title|escape:'html':'UTF-8'}{if $page.show_key} [{$page.page|escape:'html':'UTF-8'}]{/if}</strong>
							</label>
						</div>
					{/foreach}
					</div>
				{else}
					<p class="form-control-static text-muted">{l s='No QloApps pages available.' mod='qlositemap'}</p>
				{/if}
			</div>
		</div>
		{if $qsm_pages.module}
		<div class="form-group">
			<label class="control-label col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title="{l s='Module pages you do not want to include in your sitemap files' mod='qlositemap'}">{l s='Excluded module pages' mod='qlositemap'}</span></label>
			<div class="col-lg-9">
				{if $qsm_pages.module}
					<div class="checkbox"><label><input type="checkbox" class="qsm-check-all" data-group="qsm-page-module"> <strong>{l s='Select all' mod='qlositemap'}</strong></label></div>
					<div class="qsm-page-list">
					{foreach from=$qsm_pages.module item=page}
						<div class="checkbox qsm-page-item">
							<label>
								<input type="checkbox" class="qsm-page-module" name="QLOSITEMAP_DISABLED_PAGES[]" value="{$page.page|escape:'html':'UTF-8'}"{if $page.excluded} checked="checked"{/if}>
								<strong>{$page.title|escape:'html':'UTF-8'}{if $page.show_key} [{$page.page|escape:'html':'UTF-8'}]{/if}</strong>
							</label>
						</div>
					{/foreach}
					</div>
				{/if}
			</div>
		</div>
		{/if}
		<div class="panel-footer">
			<button type="submit" name="submitQloSitemapConfig" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='qlositemap'}</button>
		</div>
	</div>
</form>

<form method="post" action="{$qsm_form_action|escape:'html':'UTF-8'}" class="form-horizontal">
<div class="panel">
	<div class="panel-heading"><i class="icon-time"></i> {l s='Automatic sitemap generation' mod='qlositemap'}</div>
	{if $qsm_cron_task}
		<div class="form-group">
			<label class="control-label col-lg-3" for="qsm_cron"><span class="label-tooltip" data-toggle="tooltip" title="{l s='How often should the sitemap be regenerated automatically?' mod='qlositemap'}">{l s='Sitemap generation schedule' mod='qlositemap'}</span></label>
			<div class="col-lg-3">
				<select name="QLOSITEMAP_CRON" id="qsm_cron" class="form-control">
					{if !$qsm_cron_task.option}
						<option value="" selected="selected" disabled="disabled">{l s='Custom' mod='qlositemap'}: {$qsm_cron_task.readable|escape:'html':'UTF-8'}</option>
					{/if}
					{foreach from=$qsm_cron_options key=cron_value item=cron_label}
						<option value="{$cron_value|escape:'html':'UTF-8'}"{if $cron_value == $qsm_cron_task.option} selected="selected"{/if}>{$cron_label|escape:'html':'UTF-8'}</option>
					{/foreach}
				</select>
			</div>
		</div>
	{/if}
	{include file="{$qsm_template_dir}hook/sitemap-cron-info.tpl"}
	<div class="panel-footer">
		<button type="submit" name="submitQloSitemap" class="btn btn-default pull-right"><i class="process-icon-refresh"></i> {l s='Generate sitemap' mod='qlositemap'}</button>
		{if $qsm_cron_task}
			<button type="submit" name="submitQloSitemapCron" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='qlositemap'}</button>
		{/if}
	</div>
</div>
</form>

<script type="text/javascript">
{literal}
	$(document).on('change', '.qsm-check-all', function () {
		$('.' + $(this).data('group')).prop('checked', $(this).prop('checked'));
	});
	$(document).on('change', '.qsm-page-core, .qsm-page-module', function () {
		var group = $(this).hasClass('qsm-page-core') ? 'qsm-page-core' : 'qsm-page-module';
		$('.qsm-check-all[data-group="' + group + '"]').prop('checked', $('.' + group + ':not(:checked)').length === 0);
	});
	$(function () {
		$('.qsm-check-all').each(function () {
			var group = $(this).data('group');
			$(this).prop('checked', $('.' + group + ':not(:checked)').length === 0);
		});
	});
{/literal}
</script>
<style>
	.qsm-page-list {
		display: grid;
		grid-template-columns: repeat(3, 1fr);
		column-gap: 15px;
	}
	@media (max-width: 991px) {
		.qsm-page-list {
			grid-template-columns: repeat(2, 1fr);
		}
	}
	.qsm-page-list .qsm-page-item {
		margin: 0 0 8px;
	}
</style>
