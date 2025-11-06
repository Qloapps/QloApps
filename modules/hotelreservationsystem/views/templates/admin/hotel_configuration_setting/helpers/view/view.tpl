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

<div class="panel htl_conf_panel">
	<h3 class="tab"> <i class="icon-cogs"></i>&nbsp;&nbsp; {l s='Hotel Configuration' mod='hotelreservationsystem'}</h3>
	<div class="panel-body">
		{foreach from=$settings_links item=settings_link}
			<div class="btn-group setting-link-div col-sm-3 col-xs-12">
				<a type="button" href="{$settings_link.generated_link|escape:'html':'UTF-8'}" {if $settings_link.new_window}target="_blank"{/if} class="setting-link btn btn-default col-sm-10 col-xs-10">
					<span class="col-sm-2 col-xs-2"><i class="{$settings_link.icon}"></i></span>
					<span class="setting-title col-sm-10 col-xs-10">{$settings_link.name|escape:'html':'UTF-8'}</span>
				</a>
				<a tabindex="0" class="btn btn-default col-sm-2 col-xs-2" role="button" data-toggle="popover" data-trigger="focus" title="{$settings_link.name|escape:'html':'UTF-8'}" data-content="{$settings_link.hint|escape:'html':'UTF-8'}" data-placement="bottom">
					<i class="icon-question-circle"></i>
				</a>
			</div>
		{/foreach}

		{hook h='displayAddModuleSettingLink'}
	</div>
</div>
