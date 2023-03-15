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

{extends file="helpers/options/options.tpl"}

{block name="input"}
    {if $field['type'] == 'select'}
        <div class="col-lg-9">
            {if $field['list']}
                <select class="form-control fixed-width-xxl {if isset($field['class'])}{$field['class']}{/if}" name="{$key}"
                    {if isset($field['js'])} onchange="{$field['js']}" {/if} id="{$key}" {if isset($field['size'])}
                size="{$field['size']}" {/if}>
                {foreach $field['list'] AS $k => $option}
                    <option value="{$option[$field['identifier']]}" {if $field['value'] == $option[$field['identifier']]}
                        selected="selected" {/if}>{$option['name']}</option>
                {/foreach}
            </select>
        {elseif isset($input.empty_message)}
            {$input.empty_message}
        {/if}
    </div>
{elseif $field['type'] == 'group'}
    {assign var=groups value=$field['values']}
    {if count($groups) && isset($groups)}
        <div class="row">
            <div class="col-lg-6">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="fixed-width-xs">
                                <span class="title_box">
                                    <input type="checkbox" name="checkme" id="checkme"
                                        onclick="checkDelBoxes(this.form, 'WK_ALLOW_ORDER_STATUS_TO_REFUND[]', this.checked)" />
                                </span>
                            </th>
                            <th class="fixed-width-xs"><span class="title_box">{l s='ID'}</span></th>
                            <th>
                                <span class="title_box">
                                    {l s='Order States'}
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $groups as $key => $group}
                            <tr>
                                <td>
                                    {assign var=id_checkbox value=WK_ALLOW_ORDER_STATUS_TO_REFUND|cat:'_'|cat:$group['id_order_state']}
                                    <input type="checkbox" name="WK_ALLOW_ORDER_STATUS_TO_REFUND[]" class="groupBox" id="{$id_checkbox}"
                                        value="{$group['id_order_state']}" {if $group['checked']}checked="checked" {/if} />
                                </td>
                                <td>{$group['id_order_state']}</td>
                                <td>
                                    <label for="{$id_checkbox}">{$group['name']}</label>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    {else}
        <p>
            {l s='No group created'}
        </p>
    {/if}
{elseif $field['type'] == 'bool'}
    <div class="col-lg-9">
        <span class="switch prestashop-switch fixed-width-lg">
            {strip}
                <input type="radio" name="{$key}" id="{$key}_on" value="1" {if $field['value']} checked="checked"
                        {/if}{if isset($field['js']['on'])}
                        {$field['js']['on']}{/if}{if isset($field['disabled']) && (bool)$field['disabled']} disabled="disabled"
                    {/if} />
                <label for="{$key}_on" class="radioCheck">
                    {l s='Yes'}
                </label>
                <input type="radio" name="{$key}" id="{$key}_off" value="0" {if !$field['value']} checked="checked"
                        {/if}{if isset($field['js']['off'])}
                        {$field['js']['off']}{/if}{if isset($field['disabled']) && (bool)$field['disabled']} disabled="disabled"
                    {/if} />
                <label for="{$key}_off" class="radioCheck">
                    {l s='No'}
                </label>
            {/strip}
            <a class="slide-button btn"></a>
        </span>
    </div>
{elseif $field['type'] == 'radio'}
    <div class="col-lg-9">
        {foreach $field['choices'] AS $k => $v}
            <p class="radio">
                {strip}
                    <label for="{$key}_{$k}">
                        <input type="radio" name="{$key}" id="{$key}_{$k}" value="{$k}" {if $k == $field['value']} checked="checked"
                                {/if}{if isset($field['js'][$k])} {$field['js'][$k]}{/if} />
                            {$v}
                        </label>
                    {/strip}
                </p>
            {/foreach}
        </div>
    {elseif $field['type'] == 'checkbox'}
        <div class="col-lg-9">
            {foreach $field['choices'] AS $k => $v}
                <p class="checkbox">
                    {strip}
                        <label class="col-lg-3" for="{$key}{$k}_on">
                            <input type="checkbox" name="{$key}" id="{$key}{$k}_on" value="{$k|intval}" {if $k == $field['value']}
                                    checked="checked" {/if}{if isset($field['js'][$k])} {$field['js'][$k]}{/if} />
                                {$v}
                            </label>
                        {/strip}
                    </p>
                {/foreach}
            </div>
        {elseif $field['type'] == 'text'}
            <div class="col-lg-9">{if isset($field['suffix'])}<div class="input-group{if isset($field.class)} {$field.class}{/if}">
                {/if}
                <input class="form-control {if isset($field['class'])}{$field['class']}{/if}" type="{$field['type']}"
                    {if isset($field['id'])} id="{$field['id']}" {/if}
                    size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}"
                    value="{if isset($field['no_escape']) && $field['no_escape']}{$field['value']|escape:'UTF-8'}{else}{$field['value']|escape:'html':'UTF-8'}{/if}"
                    {if isset($field['autocomplete']) && !$field['autocomplete']}autocomplete="off" {/if} />
                {if isset($field['suffix'])}
                    <span class="input-group-addon">
                        {$field['suffix']|strval}
                    </span>
                {/if}
                {if isset($field['suffix'])}
            </div>{/if}
        </div>
    {elseif $field['type'] == 'password'}
        <div class="col-lg-9">{if isset($field['suffix'])}<div class="input-group{if isset($field.class)} {$field.class}{/if}">
            {/if}
            <input type="{$field['type']}" {if isset($field['id'])} id="{$field['id']}" {/if}
                size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}" value=""
                {if isset($field['autocomplete']) && !$field['autocomplete']} autocomplete="off" {/if} />
            {if isset($field['suffix'])}
                <span class="input-group-addon">
                    {$field['suffix']|strval}
                </span>
            {/if}
            {if isset($field['suffix'])}
        </div>{/if}
    </div>
{elseif $field['type'] == 'textarea'}
    <div class="col-lg-9">
        <textarea class="textarea-autosize" name={$key} cols="{$field['cols']}"
            rows="{$field['rows']}">{$field['value']|escape:'html':'UTF-8'}</textarea>
    </div>
{elseif $field['type'] == 'file'}
    <div class="col-lg-9">{$field['file']}</div>
{elseif $field['type'] == 'color'}
    <div class="col-lg-2">
        <div class="input-group">
            <input type="color" size="{$field['size']}" data-hex="true" {if isset($input.class)}class="{$field['class']}"
                {else}class="color mColorPickerInput"
                {/if} name="{$field['name']}"
                class="{if isset($field['class'])}{$field['class']}{/if}" value="{$field['value']|escape:'html':'UTF-8'}" />
        </div>
    </div>
{elseif $field['type'] == 'price'}
    <div class="col-lg-9">
        <div class="input-group fixed-width-lg">
            <span class="input-group-addon">{$currency_left_sign}{$currency_right_sign} {l s='(tax excl.)'}</span>
            <input type="text" size="{if isset($field['size'])}{$field['size']|intval}{else}5{/if}" name="{$key}"
                value="{$field['value']|escape:'html':'UTF-8'}" />
        </div>
    </div>
{elseif $field['type'] == 'textLang' || $field['type'] == 'textareaLang' || $field['type'] == 'selectLang'}
    {if $field['type'] == 'textLang'}
        <div class="col-lg-9">
            <div class="row">
                {foreach $field['languages'] AS $id_lang => $value}
                    {if $field['languages']|count > 1}
                        <div class="translatable-field lang-{$id_lang}" {if $id_lang != $current_id_lang}style="display:none;" {/if}>
                            <div class="col-lg-9">
                            {else}
                                <div class="col-lg-12">
                                {/if}
                                <input type="text" name="{$key}_{$id_lang}" value="{$value|escape:'html':'UTF-8'}"
                                    {if isset($input.class)}class="{$input.class}" {/if} />
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
        <div class="col-lg-9">
            {foreach $field['languages'] AS $id_lang => $value}
                <div class="row translatable-field lang-{$id_lang}" {if $id_lang != $current_id_lang}style="display:none;" {/if}>
                    <div id="{$key}_{$id_lang}" class="col-lg-9">
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
    {elseif $field['type'] == 'selectLang'}
        {foreach $languages as $language}
            <div id="{$key}_{$language.id_lang}" style="display: {if $language.id_lang == $current_id_lang}block{else}none{/if};"
                class="col-lg-9">
                <select name="{$key}_{$language.iso_code|upper}">
                    {foreach $field['list'] AS $k => $v}
                        <option value="{if isset($v.cast)}{$v.cast[$v[$field.identifier]]}{else}{$v[$field.identifier]}{/if}"
                            {if $field['value'][$language.id_lang] == $v['name']} selected="selected" {/if}>
                            {$v['name']}
                        </option>
                    {/foreach}
                </select>
            </div>
        {/foreach}
    {/if}
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