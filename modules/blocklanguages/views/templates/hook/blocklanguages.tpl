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

{block name='block_languages'}
    {if count($languages) > 1}
        <div id="languages-block-top" class="languages-block-wrap header-nav-option">
            <div class="dropdown">
                {if isset($languages[$lang_id]['iso_code'])}
                    <button class="btn dropdown-toggle btn-sm" type="button" data-toggle="dropdown">
                        {$languages[$lang_id]['name']|regex_replace:"/\s\(.*\)$/":""}
                    </button>
                {/if}

                <ul class="dropdown-menu dropdown-menu-right">
                    {foreach from=$languages key=k item=language}
                        <li>
                            {assign var=indice_lang value=$language.id_lang}
                            {if isset($lang_rewrite_urls.$indice_lang)}
                                <a class="dropdown-item {if $language.iso_code == $lang_iso}disabled{/if}" href="{$lang_rewrite_urls.$indice_lang|escape:'html':'UTF-8'}" title="{$language.name}">
                            {else}
                                <a class="dropdown-item {if $language.iso_code == $lang_iso}disabled{/if}" href="{$link->getLanguageLink($language.id_lang)|escape:'html':'UTF-8'}" title="{$language.name}">
                            {/if}
                                <span>{$language.name|regex_replace:"/\s\(.*\)$/":""}</span>
                            </a>
                        </li>
                    {/foreach}
                </ul>
            </div>
        </div>
    {/if}
{/block}
