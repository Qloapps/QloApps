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
		<p>{l s='You can customize the sitemap configuration (update frequency and excluded pages) on the module configuration page.' mod='qlogsitemap'} <a class="btn btn-default btn-sm" href="{$qgs_config_link|escape:'html':'UTF-8'}"><i class="icon-cogs"></i> {l s='Sitemap configuration' mod='qlogsitemap'}</a></p>
	</div>
	<h4>{l s='Your sitemaps' mod='qlogsitemap'}</h4>
	{if $qgs_sitemap_links}
		<p>{l s='Please set up the following sitemap URL in your search engine webmaster account:' mod='qlogsitemap'}<br><a href="{$qgs_index_url|escape:'html':'UTF-8'}" target="_blank">{$qgs_index_url|escape:'html':'UTF-8'}</a></p>
		<p>{l s='Your last update was made on this date:' mod='qlogsitemap'} {$qgs_last_export|escape:'html':'UTF-8'}</p>
	{else}
		<p>{l s='No sitemap has been generated yet.' mod='qlogsitemap'}</p>
	{/if}
	{include file="{$qgs_template_dir}hook/sitemap-cron-info.tpl"}
	<div class="panel-footer">
		<button type="submit" name="submitQloGsitemap" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Generate sitemap' mod='qlogsitemap'}</button>
	</div>
</div>
