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
	<div class="panel-heading"><i class="icon-sitemap"></i> {l s='Your sitemaps' mod='qlogsitemap'}</div>
		{if $qgs_sitemap_links}
			<p>{l s='Please set up the following sitemap URL in your Google Search Console account:' mod='qlogsitemap'}<br><a href="{$qgs_index_url|escape:'html':'UTF-8'}" target="_blank">{$qgs_index_url|escape:'html':'UTF-8'}</a></p>
			<p>{l s='The above URL is the master sitemap file. It refers to the following sub-sitemap files:' mod='qlogsitemap'}</p>
			<ul class="list-unstyled">
				{foreach from=$qgs_sitemap_links item=sitemap_link}
					<li><a href="{$qgs_store_url|escape:'html':'UTF-8'}{$sitemap_link.link|escape:'html':'UTF-8'}" target="_blank">{$sitemap_link.link|escape:'html':'UTF-8'}</a></li>
				{/foreach}
			</ul>
			<p>{l s='Your last update was made on this date:' mod='qlogsitemap'} {$qgs_last_export|escape:'html':'UTF-8'}</p>
		{else}
			<p>{l s='No sitemap has been generated yet.' mod='qlogsitemap'}</p>
		{/if}
</div>

<form method="post" action="{$qgs_form_action|escape:'html':'UTF-8'}" class="form-horizontal">
	<div class="panel">
		<div class="panel-heading"><i class="icon-wrench"></i> {l s='Configure your sitemap' mod='qlogsitemap'}</div>
		<p>{l s='Several sitemap files will be generated depending on how your server is configured and on the number of activated products in your catalog.' mod='qlogsitemap'}</p>
		<div class="form-group">
			<label class="control-label col-lg-3" for="qgs_frequency"><span class="label-tooltip" data-toggle="tooltip" title="{l s='How often do you update your property website?' mod='qlogsitemap'}">{l s='Update frequency' mod='qlogsitemap'}</span></label>
			<div class="col-lg-3">
				<select name="QLOGSITEMAP_FREQUENCY" id="qgs_frequency" class="form-control">
					{foreach from=$qgs_frequencies key=freq_value item=freq_label}
						<option value="{$freq_value|escape:'html':'UTF-8'}"{if $freq_value == $qgs_frequency} selected="selected"{/if}>{$freq_label|escape:'html':'UTF-8'}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title="{l s='QloApps pages you do not want to include in your sitemap files' mod='qlogsitemap'}">{l s='Excluded QloApps pages' mod='qlogsitemap'}</span></label>
			<div class="col-lg-9">
				{if $qgs_pages.core}
					<p><button type="button" class="btn btn-default qgs-check-all" data-group="qgs-page-core" data-check="{l s='Check all' mod='qlogsitemap'}" data-uncheck="{l s='Uncheck all' mod='qlogsitemap'}">{l s='Check all' mod='qlogsitemap'}</button></p>
					{foreach from=$qgs_pages.core item=page}
						<div class="checkbox">
							<label>
								<input type="checkbox" class="qgs-page-core" name="QLOGSITEMAP_DISABLED_PAGES[]" value="{$page.page|escape:'html':'UTF-8'}"{if $page.excluded} checked="checked"{/if}>
								<strong>{$page.title|escape:'html':'UTF-8'}{if $page.show_key} [{$page.page|escape:'html':'UTF-8'}]{/if}</strong>
							</label>
						</div>
					{/foreach}
				{else}
					<p class="form-control-static text-muted">{l s='No QloApps pages available.' mod='qlogsitemap'}</p>
				{/if}
			</div>
		</div>
		{if $qgs_pages.module}
		<div class="form-group">
			<label class="control-label col-lg-3"><span class="label-tooltip" data-toggle="tooltip" title="{l s='Module pages you do not want to include in your sitemap files' mod='qlogsitemap'}">{l s='Excluded module pages' mod='qlogsitemap'}</span></label>
			<div class="col-lg-9">
				{if $qgs_pages.module}
					<p><button type="button" class="btn btn-default qgs-check-all" data-group="qgs-page-module" data-check="{l s='Check all' mod='qlogsitemap'}" data-uncheck="{l s='Uncheck all' mod='qlogsitemap'}">{l s='Check all' mod='qlogsitemap'}</button></p>
					{foreach from=$qgs_pages.module item=page}
						<div class="checkbox">
							<label>
								<input type="checkbox" class="qgs-page-module" name="QLOGSITEMAP_DISABLED_PAGES[]" value="{$page.page|escape:'html':'UTF-8'}"{if $page.excluded} checked="checked"{/if}>
								<strong>{$page.title|escape:'html':'UTF-8'}{if $page.show_key} [{$page.page|escape:'html':'UTF-8'}]{/if}</strong>
							</label>
						</div>
					{/foreach}
				{/if}
			</div>
		</div>
		{/if}
		<div class="panel-footer">
			<button type="submit" name="submitQloGsitemapConfig" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='qlogsitemap'}</button>
		</div>
	</div>
</form>

<form method="post" action="{$qgs_form_action|escape:'html':'UTF-8'}" class="form-horizontal">
<div class="panel">
	<div class="panel-heading"><i class="icon-time"></i> {l s='Cron configuration' mod='qlogsitemap'}</div>
	{if $qgs_cron_task}
		<div class="form-group">
			<label class="control-label col-lg-3" for="qgs_cron"><span class="label-tooltip" data-toggle="tooltip" title="{l s='How often should the sitemap be regenerated automatically?' mod='qlogsitemap'}">{l s='Auto regeneration' mod='qlogsitemap'}</span></label>
			<div class="col-lg-3">
				<select name="QLOGSITEMAP_CRON" id="qgs_cron" class="form-control">
					{if !$qgs_cron_task.option}
						<option value="" selected="selected" disabled="disabled">{l s='Custom' mod='qlogsitemap'}: {$qgs_cron_task.readable|escape:'html':'UTF-8'}</option>
					{/if}
					{foreach from=$qgs_cron_options key=cron_value item=cron_label}
						<option value="{$cron_value|escape:'html':'UTF-8'}"{if $cron_value == $qgs_cron_task.option} selected="selected"{/if}>{$cron_label|escape:'html':'UTF-8'}</option>
					{/foreach}
				</select>
			</div>
		</div>
	{/if}
	{include file="{$qgs_template_dir}hook/sitemap-cron-info.tpl"}
	<div class="panel-footer">
		<button type="submit" name="submitQloGsitemap" class="btn btn-default pull-right"><i class="process-icon-refresh"></i> {l s='Generate sitemap' mod='qlogsitemap'}</button>
		{if $qgs_cron_task}
			<button type="submit" name="submitQloGsitemapCron" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='qlogsitemap'}</button>
		{/if}
	</div>
</div>
</form>

<script type="text/javascript">
{literal}
	$(document).on('click', '.qgs-check-all', function () {
		var $boxes = $('.' + $(this).data('group'));
		var check = $boxes.filter(':not(:checked)').length > 0;
		$boxes.prop('checked', check);
		$(this).text(check ? $(this).data('uncheck') : $(this).data('check'));
	});
{/literal}
</script>
