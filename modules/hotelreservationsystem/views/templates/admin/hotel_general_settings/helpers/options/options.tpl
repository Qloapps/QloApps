{**
 * 2010-2023 Webkul.
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
 * @copyright 2010-2023 Webkul IN
 * @license LICENSE.txt
 *}

{extends file='helpers/options/options.tpl'}

{block name='input'}
    {if $field['type'] == 'text'}
        <div class="col-lg-4">{if isset($field['suffix'])}<div class="input-group{if isset($field.class)} {$field.class}{/if}">{/if}
            <input class="form-control {if isset($field['class'])}{$field['class']}{/if}" type="{$field['type']}"{if isset($field['id'])} id="{$field['id']}"{/if} size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}" value="{if isset($field['no_escape']) && $field['no_escape']}{$field['value']|escape:'UTF-8'}{else}{$field['value']|escape:'html':'UTF-8'}{/if}" {if isset($field['autocomplete']) && !$field['autocomplete']}autocomplete="off"{/if}/>
            {if isset($field['suffix'])}
            <span class="input-group-addon">
                {$field['suffix']|strval}
            </span>
            {/if}
            {if isset($field['suffix'])}</div>{/if}
        </div>
    {elseif $field['type'] == 'textLang' || $field['type'] == 'textareaLang' || $field['type'] == 'selectLang'}
        {if $field['type'] == 'textLang'}
            <div class="col-lg-5">
                <div class="row">
                {foreach $field['languages'] AS $id_lang => $value}
                    {if $field['languages']|count > 1}
                    <div class="translatable-field lang-{$id_lang}" {if $id_lang != $current_id_lang}style="display:none;"{/if}>
                        <div class="col-lg-9">
                    {else}
                    <div class="col-lg-12">
                    {/if}
                            <input type="text"
                                name="{$key}_{$id_lang}"
                                value="{$value|escape:'html':'UTF-8'}"
                                {if isset($input.class)}class="{$input.class}"{/if}
                            />
                    {if $field['languages']|count > 1}
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                {foreach $languages as $language}
                                    {if $language.id_lang == $id_lang}{$language.iso_code}{/if}
                                {/foreach}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach $languages as $language}
                                <li>
                                    <a href="javascript:hideOtherLanguage({$language.id_lang});">{$language.name}</a>
                                </li>
                                {/foreach}
                            </ul>
                        </div>
                    </div>
                    {else}
                    </div>
                    {/if}
                {/foreach}
                </div>
            </div>
        {elseif $field['type'] == 'textareaLang'}
            <div class="col-lg-5">
                {foreach $field['languages'] AS $id_lang => $value}
                    <div class="row translatable-field lang-{$id_lang}" {if $id_lang != $current_id_lang}style="display:none;"{/if}>
                        <div id="{$key}_{$id_lang}" class="col-lg-9" >
                            <textarea class="textarea-autosize" name="{$key}_{$id_lang}">{$value|replace:'\r\n':"\n"}</textarea>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                {foreach $languages as $language}
                                    {if $language.id_lang == $id_lang}{$language.iso_code}{/if}
                                {/foreach}
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach $languages as $language}
                                <li>
                                    <a href="javascript:hideOtherLanguage({$language.id_lang});">{$language.name}</a>
                                </li>
                                {/foreach}
                            </ul>
                        </div>

                    </div>
                {/foreach}
                <script type="text/javascript">
                    $(document).ready(function() {
                        $(".textarea-autosize").autosize();
                    });
                </script>
            </div>
        {/if}
    {else}
        {$smarty.block.parent}
    {/if}

    {if isset($field['desc']) && !empty($field['desc'])}
        <div class="col-lg-9 col-lg-offset-3">
            <div class="help-block">
                {if is_array($field['desc'])}
                    {foreach $field['desc'] as $p}
                        {if is_array($p)}
                            <span id="{$p.id}">{$p.text}</span><br />
                        {else}
                            {$p}<br />
                        {/if}
                    {/foreach}
                {else}
                    {$field['desc']}
                {/if}
            </div>
        </div>
    {/if}
{/block}
