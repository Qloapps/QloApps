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
	<div class="panel-heading"><i class="icon-sitemap"></i> {l s='Sitemap generation' mod='qlositemap'}</div>
	<div class="alert alert-info">
		<p>{l s='Submit the main sitemap URL in your search engine webmaster account (for example Google Search Console).' mod='qlositemap'}</p>
		<p>{l s='The main sitemap lists one sitemap file for each active language.' mod='qlositemap'}</p>
		<p>{l s='You can customize the sitemap configuration from here.' mod='qlositemap'} <a class="btn btn-default btn-sm" href="{$qsm_config_link|escape:'html':'UTF-8'}"><i class="icon-cogs"></i> {l s='Configuration' mod='qlositemap'}</a></p>
	</div>
	<h4>{l s='Your sitemap' mod='qlositemap'}</h4>
	{if $qsm_sitemap_links}
		<p><a href="{$qsm_index_url|escape:'html':'UTF-8'}" target="_blank">{$qsm_index_url|escape:'html':'UTF-8'}</a></p>
		<p>{l s='Your last update was made on this date:' mod='qlositemap'} {$qsm_last_export|escape:'html':'UTF-8'}</p>
	{else}
		<p>{l s='No sitemap has been generated yet.' mod='qlositemap'}</p>
	{/if}
	<div class="panel-footer">
		<button type="submit" name="submitQloSitemap" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Generate sitemap' mod='qlositemap'}</button>
	</div>
</div>
