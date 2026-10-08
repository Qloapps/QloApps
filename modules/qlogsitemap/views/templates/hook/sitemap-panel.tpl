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
		<p>{l s='Generate your XML sitemaps by clicking on the following button (this will erase the old sitemap files). Submit the main sitemap URL in your search engine webmaster account (for example Google Search Console).' mod='qlogsitemap'}</p>
		{if $qgs_cron_manager_enabled}
			<p>{l s='The sitemaps are also regenerated automatically every week (Sunday at midnight) by the' mod='qlogsitemap'} <a href="{$qgs_cron_manager_link|escape:'html':'UTF-8'}">{l s='Cron Task Manager' mod='qlogsitemap'}</a>.</p>
		{/if}
	</div>
	{if !$qgs_cron_manager_enabled}
		<div class="alert alert-warning">
			{if $qgs_cron_manager_installed}
				{l s='The Cron Task Manager module is disabled, so the sitemaps are not regenerated automatically.' mod='qlogsitemap'}
				{if $qgs_cron_manager_link}<a href="{$qgs_cron_manager_link|escape:'html':'UTF-8'}">{l s='Enable the module' mod='qlogsitemap'}</a>{else}{l s='Enable the module' mod='qlogsitemap'}{/if},
			{else}
				{l s='The Cron Task Manager module is not installed, so the sitemaps are not regenerated automatically.' mod='qlogsitemap'}
				{if $qgs_cron_manager_link}<a href="{$qgs_cron_manager_link|escape:'html':'UTF-8'}">{l s='Install the module' mod='qlogsitemap'}</a>{else}{l s='Install the module' mod='qlogsitemap'}{/if},
			{/if}
			{l s='or generate the sitemaps manually with the button below.' mod='qlogsitemap'}
		</div>
	{/if}
	{if $qgs_sitemap_links}
		<p>{l s='Main sitemap:' mod='qlogsitemap'} <a href="{$qgs_index_url|escape:'html':'UTF-8'}" target="_blank">{$qgs_index_url|escape:'html':'UTF-8'}</a></p>
		<ul>
			{foreach from=$qgs_sitemap_links item=sitemap_link}
				<li><a href="{$qgs_store_url|escape:'html':'UTF-8'}{$sitemap_link.link|escape:'html':'UTF-8'}" target="_blank">{$sitemap_link.link|escape:'html':'UTF-8'}</a></li>
			{/foreach}
		</ul>
		<p>{l s='Last update:' mod='qlogsitemap'} {$qgs_last_export|escape:'html':'UTF-8'}</p>
	{else}
		<p>{l s='This shop has no sitemap yet.' mod='qlogsitemap'}</p>
	{/if}
	<div class="panel-footer">
		<button type="submit" name="submitQloGsitemap" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Generate sitemap' mod='qlogsitemap'}</button>
	</div>
</div>
