{*
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
