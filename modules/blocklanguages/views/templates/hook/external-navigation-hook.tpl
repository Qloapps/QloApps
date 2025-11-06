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

{if count($languages) > 1}
    <ul class="nav nav-pills nav-stacked visible-xs wk-nav-style">
        <li>
            <a class="btn-language-selector-popup">
                {foreach from=$languages item=language}
                    {if $language.iso_code == $lang_iso}
                        {$language.name|regex_replace:"/\s\(.*\)$/":""}
                        <span class="caret"></span>
                    {/if}
                {/foreach}
            </a>
        </li>
    </ul>

    <div id="language-selector-popup" style="display: none;">
        <div class="list-group">
            {foreach from=$languages key=k item=language}
                {assign var=indice_lang value=$language.id_lang}
                <a class="list-group-item {if $language.iso_code == $lang_iso}active{/if}"
                    {if $language.iso_code != $lang_iso}
                        {if isset($lang_rewrite_urls.$indice_lang)}
                            href="{$lang_rewrite_urls.$indice_lang|escape:'html':'UTF-8'}" title="{$language.name}"
                        {else}
                            href="{$link->getLanguageLink($language.id_lang)|escape:'html':'UTF-8'}" title="{$language.name}"
                        {/if}
                    {/if}
                    >
                    <span>{$language.name|regex_replace:"/\s\(.*\)$/":""}</span>
                </a>
            {/foreach}
        </div>
    </div>
{/if}
