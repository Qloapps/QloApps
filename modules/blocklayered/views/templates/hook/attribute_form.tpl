{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registred Trademark & Property of PrestaShop SA
*}
<label>
	{l s='URL' mod='blocklayered'}
</label>
<div class="margin-form">
	<div class="translatable">
		{foreach $languages as $language}
			<div class="lang_{$language.id_lang}" style="display:{if $language.id_lang == $default_form_language}block{else}none{/if}; float: left;">
				<input type="text" name="url_name_{$language.id_lang}" id="url_name_{$language.id_lang}" value="{if isset($values[$language['id_lang']]) && isset($values[$language['id_lang']]['url_name'])}{$values[$language['id_lang']]['url_name']|escape:'htmlall':'UTF-8'}{/if}" size="64" maxlength="64" />
				<p class="preference_description">{l s='When the Layered Navigation Block module is enabled, you can get more detailed URLs by choosing the word that best represent this attribute. By default, PrestaShop uses the attribute\'s name, but you can change that setting using this field.' mod='blocklayered'}</p>
				<span class="hint" name="help_box">{l s='Invalid characters: <>;=#{}_' mod='blocklayered'}<span class="hint-pointer">&nbsp;</span></span>
			</div>
		{/foreach}
	</div>
</div>
<div class="clear"></div>
<label>
	{l s='Meta title' mod='blocklayered'}
</label>
<div class="margin-form">
	<div class="translatable">
		{foreach $languages as $language}
			<div class="lang_{$language.id_lang}" style="display:{if $language.id_lang == $default_form_language}block{else}none{/if}; float: left;">
				<input type="text" name="meta_title_{$language.id_lang}" id="meta_title_{$language.id_lang}" value="{if isset($values[$language['id_lang']]) && isset($values[$language['id_lang']]['meta_title'])}{$values[$language['id_lang']]['meta_title']|escape:'htmlall':'UTF-8'}{/if}" size="70" maxlength="70" />
				<p class="preference_description">{l s='When the Layered Navigation Block module is enabled, you can get more detailed page titles by choosing the word that best represent this attribute. By default, PrestaShop uses the attribute\'s name, but you can change that setting using this field.' mod='blocklayered'}</p>
			</div>
		{/foreach}
	</div>
</div>
<div class="clear"></div>
