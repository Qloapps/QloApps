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