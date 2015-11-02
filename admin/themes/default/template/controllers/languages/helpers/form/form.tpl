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
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helpers/form/form.tpl"}

{block name="input"}
	{if $input.type == 'special'}
		<div id="#resultCheckLangPack">
			<p id="lang_pack_loading" style="display:none"><img src="../img/admin/{$input.img}" alt="" /> {$input.text}</p>
			<p id="lang_pack_msg" style="display:none"></p>
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name=script}
		var langPackOk = "<img src=\"{$smarty.const._PS_IMG_}admin/information.png\" alt=\"\" /> {l s='A language pack is available for this ISO.'}";
		var langPackVersion = "{l s='The Prestashop version compatible with this language and your system is:'}";
		var langPackInfo = "{l s='After creating the language, you can import the content of the language pack, which you can download under "Localization -- Translations."'}";
		var noLangPack = "<img src=\"{$smarty.const._PS_IMG_}admin/information.png\" alt=\"\" /> {l s='No language pack is available on prestashop.com for this ISO code'}";
		var download = "{l s='Download'}";

	$(document).ready(function() {
		$('#iso_code').keyup(function(e) {
			e.preventDefault();
			checkLangPack("{$token|escape:'html':'UTF-8'}");
		});
	});

{/block}

{block name="other_fieldsets"}
	{if isset($fields['new'])}
		<br /><br />
		<div class="panel" style="width:572px;">
			{foreach $fields['new'] as $key => $field}
				{if $key == 'legend'}
					<legend>
						{if isset($field.image)}<img src="{$field.image}" alt="{$field.title}" />{/if}
						{$field.title}
					</legend>
					<p>{l s='This language pack is NOT complete and cannot be used in the front or back office because some files are missing.'}</p>
					<br />
				{elseif $key == 'list_files'}
					{foreach $field as $list}
						<label>{$list.label}</label>
						<div class="margin-form" style="margin-top:4px;">
							{foreach $list.files as $key => $file}
								{if !file_exists($key)}
									<font color="red">
								{/if}
								{$key}
								{if !file_exists($key)}
									</font>
								{/if}
								<br />
							{/foreach}
						</div>
						<br style="clear:both;" />
					{/foreach}
				{/if}
			{/foreach}
			<br />
			<div class="small">{l s='Missing files are marked in red'}</div>
		</div>
	{/if}
{/block}
