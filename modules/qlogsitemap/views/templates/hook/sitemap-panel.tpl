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
	<div class="panel-heading"><i class="icon-sitemap"></i> {l s='Sitemap generation' mod='qlogsitemap'}</div>
	<div class="alert alert-info">
		<p>{l s='Submit the main sitemap URL in your search engine webmaster account (for example Google Search Console).' mod='qlogsitemap'}</p>
		<p>{l s='The main sitemap lists one sitemap file for each active language.' mod='qlogsitemap'}</p>
	</div>
	<h4>{l s='Your sitemaps' mod='qlogsitemap'}</h4>
	{if $qgs_sitemap_links}
		<p>{l s='Please set up the following sitemap URL in your search engine webmaster account:' mod='qlogsitemap'}<br><a href="{$qgs_index_url|escape:'html':'UTF-8'}" target="_blank">{$qgs_index_url|escape:'html':'UTF-8'}</a></p>
		<p>{l s='The above URL is the master sitemap file. It refers to the following sub-sitemap files:' mod='qlogsitemap'}</p>
		<ul>
			{foreach from=$qgs_sitemap_links item=sitemap_link}
				<li><a href="{$qgs_store_url|escape:'html':'UTF-8'}{$sitemap_link.link|escape:'html':'UTF-8'}" target="_blank">{$sitemap_link.link|escape:'html':'UTF-8'}</a></li>
			{/foreach}
		</ul>
		<p>{l s='Your last update was made on this date:' mod='qlogsitemap'} {$qgs_last_export|escape:'html':'UTF-8'}</p>
	{else}
		<p>{l s='No sitemap has been generated yet.' mod='qlogsitemap'}</p>
	{/if}
	<div class="alert alert-info">
		<p><strong>{l s='Sitemap generation' mod='qlogsitemap'}</strong></p>
		<ul>
			<li>{l s='Manual: click on the "Generate sitemap" button. This erases the old sitemap files and creates new ones.' mod='qlogsitemap'}</li>
			{if $qgs_cron_manager_enabled}
				{if $qgs_cron_task && !$qgs_cron_task.active}
					<li>{l s='Automatic: the sitemap task is disabled, so the sitemaps are not regenerated automatically. You can enable it in the' mod='qlogsitemap'} <a href="{$qgs_cron_manager_link|escape:'html':'UTF-8'}">{l s='Cron Task Manager' mod='qlogsitemap'}</a>.</li>
				{elseif $qgs_cron_task}
					<li>{l s='Automatic: the sitemaps are regenerated automatically. Current schedule:' mod='qlogsitemap'} <strong>{$qgs_cron_task.readable|escape:'html':'UTF-8'}</strong>. {l s='You can configure this schedule in the' mod='qlogsitemap'} <a href="{$qgs_cron_manager_link|escape:'html':'UTF-8'}">{l s='Cron Task Manager' mod='qlogsitemap'}</a>.</li>
				{else}
					<li>{l s='Automatic: the sitemaps are regenerated automatically. You can configure the schedule in the' mod='qlogsitemap'} <a href="{$qgs_cron_manager_link|escape:'html':'UTF-8'}">{l s='Cron Task Manager' mod='qlogsitemap'}</a>.</li>
				{/if}
			{else}
				<li>
					{if $qgs_cron_manager_installed}
						{l s='Automatic: the Cron Task Manager module is disabled, so the sitemaps are not regenerated automatically.' mod='qlogsitemap'}
						{if $qgs_cron_manager_link}<a href="{$qgs_cron_manager_link|escape:'html':'UTF-8'}">{l s='Enable the module' mod='qlogsitemap'}</a>{else}{l s='Enable the module' mod='qlogsitemap'}{/if}
					{else}
						{l s='Automatic: the Cron Task Manager module is not installed, so the sitemaps are not regenerated automatically.' mod='qlogsitemap'}
						{if $qgs_cron_manager_link}<a href="{$qgs_cron_manager_link|escape:'html':'UTF-8'}">{l s='Install the module' mod='qlogsitemap'}</a>{else}{l s='Install the module' mod='qlogsitemap'}{/if}
					{/if}
					{l s='to turn on the automatic generation.' mod='qlogsitemap'}
				</li>
			{/if}
		</ul>
	</div>
	<div class="panel-footer">
		<button type="submit" name="submitQloGsitemap" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Generate sitemap' mod='qlogsitemap'}</button>
	</div>
</div>
