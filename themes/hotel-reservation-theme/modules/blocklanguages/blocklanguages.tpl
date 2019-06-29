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
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2017 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}
<!-- Block languages module -->
{if count($languages) > 1}
	<div class="row">
		<section class="col-xs-12 col-sm-12">
			<div class="row margin-lr-0 footer-section-heading">
				<p>{l s='Language' mod='wkfooterexploreblock'}</p>
				<hr/>
			</div>
			<div class="row margin-lr-0">
				<div id="languages-block-top" class="languages-block">
					{foreach from=$languages key=k item=language name="languages"}
						{if $language.iso_code == $lang_iso}
							<div class="current">
								<span>{$language.name|regex_replace:"/\s\(.*\)$/":""}</span>
							</div>
						{/if}
					{/foreach}
					<ul id="first-languages" class="languages-block_ul toogle_content">
						{foreach from=$languages key=k item=language name="languages"}
							<li {if $language.iso_code == $lang_iso}class="selected"{/if}>
							{if $language.iso_code != $lang_iso}
								{assign var=indice_lang value=$language.id_lang}
								{if isset($lang_rewrite_urls.$indice_lang)}
									<a href="{$lang_rewrite_urls.$indice_lang|escape:'html':'UTF-8'}" title="{$language.name}">
								{else}
									<a href="{$link->getLanguageLink($language.id_lang)|escape:'html':'UTF-8'}" title="{$language.name}">
								{/if}
							{/if}
									<span>{$language.name|regex_replace:"/\s\(.*\)$/":""}</span>
							{if $language.iso_code != $lang_iso}
								</a>
							{/if}
							</li>
						{/foreach}
					</ul>
				</div>
			</div>
		</section>
	</div>
{/if}

<!-- /Block languages module -->
