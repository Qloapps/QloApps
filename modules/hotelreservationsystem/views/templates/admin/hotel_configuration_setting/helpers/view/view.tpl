{**
 * 2010-2022 Webkul.
 *
 * NOTICE OF LICENSE
 *
 * All right is reserved,
 * Please go through LICENSE.txt file inside our module
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright 2010-2022 Webkul IN
 * @license LICENSE.txt
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
