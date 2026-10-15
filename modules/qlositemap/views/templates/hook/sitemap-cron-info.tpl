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

	<div class="alert alert-info">
		<p><strong>{l s='Sitemap generation' mod='qlositemap'}</strong></p>
		<ul>
			{if $qsm_cron_manager_enabled}
				{if $qsm_cron_task && !$qsm_cron_task.active}
					<li><strong>{l s='Automatic:' mod='qlositemap'}</strong> {l s='automatic sitemap generation is turned off. To generate the sitemap automatically, choose Weekly, Monthly or Yearly in Sitemap generation schedule.' mod='qlositemap'}</li>
				{elseif $qsm_cron_task}
					<li><strong>{l s='Automatic:' mod='qlositemap'}</strong> {l s='the sitemaps are regenerated automatically. Current schedule:' mod='qlositemap'} <strong>{$qsm_cron_task.readable|escape:'html':'UTF-8'}</strong>.</li>
				{else}
					<li><strong>{l s='Automatic:' mod='qlositemap'}</strong> {l s='the sitemaps are regenerated automatically. You can configure the schedule in the' mod='qlositemap'} <a href="{$qsm_cron_manager_link|escape:'html':'UTF-8'}">{l s='Cron Task Manager' mod='qlositemap'}</a>.</li>
				{/if}
			{/if}
			<li><strong>{l s='Manual:' mod='qlositemap'}</strong> {l s='to generate the sitemap manually, click on the "Generate sitemap" button. This erases the old sitemap files and creates new ones.' mod='qlositemap'}</li>
		</ul>
	</div>
	{if !$qsm_cron_manager_enabled}
	<div class="alert alert-warning">
		<ul>
			<li>
				{if $qsm_cron_manager_installed}
					<strong>{l s='Automatic:' mod='qlositemap'}</strong> {l s='the Cron Task Manager module is disabled, so the sitemaps are not regenerated automatically.' mod='qlositemap'}
					<a href="{$qsm_cron_manager_link|escape:'html':'UTF-8'}">{l s='Enable the module' mod='qlositemap'}</a>
					{l s='to turn on the automatic generation.' mod='qlositemap'}
				{elseif $qsm_cron_manager_link}
					<strong>{l s='Automatic:' mod='qlositemap'}</strong> {l s='the Cron Task Manager module is not installed, so the sitemaps are not regenerated automatically.' mod='qlositemap'}
					<a href="{$qsm_cron_manager_link|escape:'html':'UTF-8'}">{l s='Install the module' mod='qlositemap'}</a>
					{l s='to turn on the automatic generation.' mod='qlositemap'}
				{else}
					<strong>{l s='Automatic:' mod='qlositemap'}</strong> {l s='the Cron Task Manager module (qlocrontaskmanager) is not present, so the sitemaps are not regenerated automatically. Add the module to turn on the automatic generation.' mod='qlositemap'}
				{/if}
			</li>
		</ul>
	</div>
	{/if}
