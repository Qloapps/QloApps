{*
* Copyright since 2007 Webkul.
*
* NOTICE OF LICENSE
*
* All rights is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright since 2007 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($display_multishop_checkboxes) && $display_multishop_checkboxes}
	{if isset($multilang) && $multilang}
		{if isset($only_checkbox)}
			{foreach from=$languages item=language}
				<input type="checkbox" name="multishop_check[{$field}][{$language.id_lang}]" value="1" onclick="ProductMultishop.checkField(this.checked, '{$field}_{$language.id_lang}', '{$type}')" {if !empty($multishop_check[$field][$language.id_lang])}checked="checked"{/if} />
			{/foreach}
		{else}
			{foreach from=$languages item=language}
				<input style="{if !$language.is_default}display: none;{/if}" class="multishop_lang_{$language.id_lang} lang-{$language.id_lang} translatable-field" type="checkbox" name="multishop_check[{$field}][{$language.id_lang}]" value="1" onclick="ProductMultishop.checkField(this.checked, '{$field}_{$language.id_lang}','{$type}')"
				{if !empty($multishop_check[$field][$language.id_lang])}checked="checked"{/if} />
			{/foreach}
		{/if}
	{else}
		<input type="checkbox" name="multishop_check[{$field}]" value="1" onclick="ProductMultishop.checkField(this.checked, '{$field}', '{$type}')" {if !empty($multishop_check[$field])}checked="checked"{/if} />
	{/if}
{/if}