{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
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