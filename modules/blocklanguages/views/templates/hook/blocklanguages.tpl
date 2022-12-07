{**
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
    <div id="languages-block-top" class="languages-block-wrap nav-main-item-right hidden-xs pull-right">
        <div class="dropdown">
            {foreach from=$languages key=k item=language}
                {if $language.iso_code == $lang_iso}
                    <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">
                        {$language.name|regex_replace:"/\s\(.*\)$/":""}
                        <span class="caret"></span>
                    </button>
                {/if}
            {/foreach}

            <ul class="dropdown-menu">
                {foreach from=$languages key=k item=language}
                    <li {if $language.iso_code == $lang_iso}class="disabled"{/if}>
                        {assign var=indice_lang value=$language.id_lang}
                        {if isset($lang_rewrite_urls.$indice_lang)}
                            <a href="{$lang_rewrite_urls.$indice_lang|escape:'html':'UTF-8'}" title="{$language.name}">
                        {else}
                            <a href="{$link->getLanguageLink($language.id_lang)|escape:'html':'UTF-8'}" title="{$language.name}">
                        {/if}
                            <span>{$language.name|regex_replace:"/\s\(.*\)$/":""}</span>
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
{/if}
