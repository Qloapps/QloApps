{*
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

{foreach from=$languages item=language}
	{if $languages|count > 1}
	<div class="translatable-field row lang-{$language.id_lang}">
		<div class="col-lg-9">
	{/if}
		{if isset($maxchar)}
		<div class="input-group">
			<span id="{$input_name}_{$language.id_lang}_counter" class="input-group-addon">
				<span class="text-count-down">{$maxchar}</span>
			</span>
			{/if}
			<input type="text"
			id="{$input_name}_{$language.id_lang}"
			class="form-control {if isset($input_class)}{$input_class} {/if}"
			name="{$input_name}_{$language.id_lang}"
			value="{$input_value[$language.id_lang]|default:''|escape:'html'}"
			onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"
			onblur="updateLinkRewrite();"
			{if isset($required)} required="required"{/if}
			{if isset($maxchar)} data-maxchar="{$maxchar}"{/if}
			{if isset($maxlength)} maxlength="{$maxlength}"{/if} />
			{if isset($maxchar)}
		</div>
		{/if}
	{if $languages|count > 1}
		</div>
		<div class="col-lg-2">
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
				{$language.iso_code}
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				{foreach from=$languages item=language}
				<li>
					<a href="javascript:tabs_manager.allow_hide_other_languages = false;hideOtherLanguage({$language.id_lang});">{$language.name}</a>
				</li>
				{/foreach}
			</ul>
		</div>
	</div>
	{/if}
{/foreach}
{if isset($maxchar)}
<script type="text/javascript">

$(document).ready(function(){
{foreach from=$languages item=language}
	countDown($("#{$input_name}_{$language.id_lang}"), $("#{$input_name}_{$language.id_lang}_counter"));
{/foreach}
});
</script>
{/if}
