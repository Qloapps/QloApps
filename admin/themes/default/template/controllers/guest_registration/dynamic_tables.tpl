{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*}

<form method="post" action="{$dynamic_form_action|escape:'html':'UTF-8'}" class="guest-registration-dynamic-form">
    {assign var=default_iso_code value=''}
    {foreach from=$languages item=language}
        {if $language.id_lang == $default_form_language}
            {assign var=default_iso_code value=$language.iso_code}
        {/if}
    {/foreach}
    <div class="panel">
        <h3><i class="icon-list-ul"></i> {l s='Dynamic values'}</h3>

        <div class="panel">
            <h3><i class="icon-tags"></i> {l s='Purpose of visit'}</h3>
            {assign var=entity_name value='guest_reg_purpose'}
            {assign var=entity_rows value=$guest_reg_purpose_rows}
            <div class="table-responsive-row clearfix">
                <table class="table dynamic-reg-table" data-entity="{$entity_name}">
                    <thead>
                        <tr>
                            <th style="display:none;">{l s='ID'}</th>
                            <th>{l s='Name'}</th>
                            <th class="fixed-width-md">{l s='Active'}</th>
                            <th class="fixed-width-md text-center">{l s='Action'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$entity_rows item=row}
                            <tr class="dynamic-row">
                                <td style="display:none;">
                                    <input type="hidden" name="{$entity_name}[{$row.id|intval}][id]" value="{$row.id|intval}" />
                                </td>
                                <td class="dynamic-name-cell">
                                    {foreach from=$languages item=language}
                                        <div class="translatable-field row lang-{$language.id_lang}" {if $language.id_lang != $default_form_language}style="display:none;"{/if}>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="{$entity_name}[{$row.id|intval}][name][{$language.id_lang|intval}]" value="{$row.name[$language.id_lang]|default:''|escape:'html':'UTF-8'}" />
                                            </div>
                                            <div class="col-lg-2">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                    <span class="js-locale-btn">{$language.iso_code|escape:'html':'UTF-8'}</span> <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    {foreach from=$languages item=language_option}
                                                        <li><a href="#" class="js-change-language" data-id-lang="{$language_option.id_lang|intval}" data-iso-code="{$language_option.iso_code|escape:'html':'UTF-8'}">{$language_option.name|escape:'html':'UTF-8'}</a></li>
                                                    {/foreach}
                                                </ul>
                                            </div>
                                        </div>
                                    {/foreach}
                                </td>
                                <td>
                                    <select class="form-control" name="{$entity_name}[{$row.id|intval}][active]">
                                        <option value="1" {if $row.active}selected="selected"{/if}>{l s='Yes'}</option>
                                        <option value="0" {if !$row.active}selected="selected"{/if}>{l s='No'}</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-default remove-dynamic-row"><i class="icon-trash"></i> {l s='Remove'}</button>
                                </td>
                            </tr>
                        {/foreach}
                        <tr class="dynamic-template-row" style="display:none;">
                            <td style="display:none;"><input type="hidden" data-name="__ENTITY__[__INDEX__][id]" value="" /></td>
                            <td class="dynamic-name-cell">
                                {foreach from=$languages item=language}
                                    <div class="translatable-field row lang-{$language.id_lang}" {if $language.id_lang != $default_form_language}style="display:none;"{/if}>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control" data-name="__ENTITY__[__INDEX__][name][{$language.id_lang|intval}]" value="" />
                                        </div>
                                        <div class="col-lg-2">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                <span class="js-locale-btn">{$language.iso_code|escape:'html':'UTF-8'}</span> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                {foreach from=$languages item=language_option}
                                                    <li><a href="#" class="js-change-language" data-id-lang="{$language_option.id_lang|intval}" data-iso-code="{$language_option.iso_code|escape:'html':'UTF-8'}">{$language_option.name|escape:'html':'UTF-8'}</a></li>
                                                {/foreach}
                                            </ul>
                                        </div>
                                    </div>
                                {/foreach}
                            </td>
                            <td>
                                <select class="form-control" data-name="__ENTITY__[__INDEX__][active]">
                                    <option value="1" selected="selected">{l s='Yes'}</option>
                                    <option value="0">{l s='No'}</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-default remove-dynamic-row"><i class="icon-trash"></i> {l s='Remove'}</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-default add-dynamic-row" data-entity="{$entity_name}"><i class="icon-plus"></i> {l s='Add More'}</button>
        </div>

        <div class="panel">
            <h3><i class="icon-tags"></i> {l s='Identity proof'}</h3>
            {assign var=entity_name value='guest_reg_id_proof'}
            {assign var=entity_rows value=$guest_reg_id_proof_rows}
            <div class="table-responsive-row clearfix">
                <table class="table dynamic-reg-table" data-entity="{$entity_name}">
                    <thead>
                        <tr>
                            <th style="display:none;">{l s='ID'}</th>
                            <th>{l s='Name'}</th>
                            <th class="fixed-width-md">{l s='Active'}</th>
                            <th class="fixed-width-md text-center">{l s='Action'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$entity_rows item=row}
                            <tr class="dynamic-row">
                                <td style="display:none;">
                                    <input type="hidden" name="{$entity_name}[{$row.id|intval}][id]" value="{$row.id|intval}" />
                                </td>
                                <td class="dynamic-name-cell">
                                    {foreach from=$languages item=language}
                                        <div class="translatable-field row lang-{$language.id_lang}" {if $language.id_lang != $default_form_language}style="display:none;"{/if}>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="{$entity_name}[{$row.id|intval}][name][{$language.id_lang|intval}]" value="{$row.name[$language.id_lang]|default:''|escape:'html':'UTF-8'}" />
                                            </div>
                                            <div class="col-lg-2">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                    <span class="js-locale-btn">{$language.iso_code|escape:'html':'UTF-8'}</span> <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    {foreach from=$languages item=language_option}
                                                        <li><a href="#" class="js-change-language" data-id-lang="{$language_option.id_lang|intval}" data-iso-code="{$language_option.iso_code|escape:'html':'UTF-8'}">{$language_option.name|escape:'html':'UTF-8'}</a></li>
                                                    {/foreach}
                                                </ul>
                                            </div>
                                        </div>
                                    {/foreach}
                                </td>
                                <td>
                                    <select class="form-control" name="{$entity_name}[{$row.id|intval}][active]">
                                        <option value="1" {if $row.active}selected="selected"{/if}>{l s='Yes'}</option>
                                        <option value="0" {if !$row.active}selected="selected"{/if}>{l s='No'}</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-default remove-dynamic-row"><i class="icon-trash"></i> {l s='Remove'}</button>
                                </td>
                            </tr>
                        {/foreach}
                        <tr class="dynamic-template-row" style="display:none;">
                            <td style="display:none;"><input type="hidden" data-name="__ENTITY__[__INDEX__][id]" value="" /></td>
                            <td class="dynamic-name-cell">
                                {foreach from=$languages item=language}
                                    <div class="translatable-field row lang-{$language.id_lang}" {if $language.id_lang != $default_form_language}style="display:none;"{/if}>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control" data-name="__ENTITY__[__INDEX__][name][{$language.id_lang|intval}]" value="" />
                                        </div>
                                        <div class="col-lg-2">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                <span class="js-locale-btn">{$language.iso_code|escape:'html':'UTF-8'}</span> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                {foreach from=$languages item=language_option}
                                                    <li><a href="#" class="js-change-language" data-id-lang="{$language_option.id_lang|intval}" data-iso-code="{$language_option.iso_code|escape:'html':'UTF-8'}">{$language_option.name|escape:'html':'UTF-8'}</a></li>
                                                {/foreach}
                                            </ul>
                                        </div>
                                    </div>
                                {/foreach}
                            </td>
                            <td>
                                <select class="form-control" data-name="__ENTITY__[__INDEX__][active]">
                                    <option value="1" selected="selected">{l s='Yes'}</option>
                                    <option value="0">{l s='No'}</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-default remove-dynamic-row"><i class="icon-trash"></i> {l s='Remove'}</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-default add-dynamic-row" data-entity="{$entity_name}"><i class="icon-plus"></i> {l s='Add More'}</button>
        </div>

        <div class="panel">
            <h3><i class="icon-tags"></i> {l s='Payment method'}</h3>
            {assign var=entity_name value='guest_reg_payment_method'}
            {assign var=entity_rows value=$guest_reg_payment_method_rows}
            <div class="table-responsive-row clearfix">
                <table class="table dynamic-reg-table" data-entity="{$entity_name}">
                    <thead>
                        <tr>
                            <th style="display:none;">{l s='ID'}</th>
                            <th>{l s='Name'}</th>
                            <th class="fixed-width-md">{l s='Active'}</th>
                            <th class="fixed-width-md text-center">{l s='Action'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$entity_rows item=row}
                            <tr class="dynamic-row">
                                <td style="display:none;">
                                    <input type="hidden" name="{$entity_name}[{$row.id|intval}][id]" value="{$row.id|intval}" />
                                </td>
                                <td class="dynamic-name-cell">
                                    {foreach from=$languages item=language}
                                        <div class="translatable-field row lang-{$language.id_lang}" {if $language.id_lang != $default_form_language}style="display:none;"{/if}>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" name="{$entity_name}[{$row.id|intval}][name][{$language.id_lang|intval}]" value="{$row.name[$language.id_lang]|default:''|escape:'html':'UTF-8'}" />
                                            </div>
                                            <div class="col-lg-2">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                    <span class="js-locale-btn">{$language.iso_code|escape:'html':'UTF-8'}</span> <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    {foreach from=$languages item=language_option}
                                                        <li><a href="#" class="js-change-language" data-id-lang="{$language_option.id_lang|intval}" data-iso-code="{$language_option.iso_code|escape:'html':'UTF-8'}">{$language_option.name|escape:'html':'UTF-8'}</a></li>
                                                    {/foreach}
                                                </ul>
                                            </div>
                                        </div>
                                    {/foreach}
                                </td>
                                <td>
                                    <select class="form-control" name="{$entity_name}[{$row.id|intval}][active]">
                                        <option value="1" {if $row.active}selected="selected"{/if}>{l s='Yes'}</option>
                                        <option value="0" {if !$row.active}selected="selected"{/if}>{l s='No'}</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-default remove-dynamic-row"><i class="icon-trash"></i> {l s='Remove'}</button>
                                </td>
                            </tr>
                        {/foreach}
                        <tr class="dynamic-template-row" style="display:none;">
                            <td style="display:none;"><input type="hidden" data-name="__ENTITY__[__INDEX__][id]" value="" /></td>
                            <td class="dynamic-name-cell">
                                {foreach from=$languages item=language}
                                    <div class="translatable-field row lang-{$language.id_lang}" {if $language.id_lang != $default_form_language}style="display:none;"{/if}>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control" data-name="__ENTITY__[__INDEX__][name][{$language.id_lang|intval}]" value="" />
                                        </div>
                                        <div class="col-lg-2">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                <span class="js-locale-btn">{$language.iso_code|escape:'html':'UTF-8'}</span> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                {foreach from=$languages item=language_option}
                                                    <li><a href="#" class="js-change-language" data-id-lang="{$language_option.id_lang|intval}" data-iso-code="{$language_option.iso_code|escape:'html':'UTF-8'}">{$language_option.name|escape:'html':'UTF-8'}</a></li>
                                                {/foreach}
                                            </ul>
                                        </div>
                                    </div>
                                {/foreach}
                            </td>
                            <td>
                                <select class="form-control" data-name="__ENTITY__[__INDEX__][active]">
                                    <option value="1" selected="selected">{l s='Yes'}</option>
                                    <option value="0">{l s='No'}</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-default remove-dynamic-row"><i class="icon-trash"></i> {l s='Remove'}</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-default add-dynamic-row" data-entity="{$entity_name}"><i class="icon-plus"></i> {l s='Add More'}</button>
        </div>

        <div class="panel-footer">
            <button type="submit" name="submitBulkGuestRegistrationValues" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save'}
            </button>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function() {
        var dynamicRowIndexes = {};
        var showDynamicLanguage = function(idLang, isoCode) {
            $('.translatable-field').hide();
            $('.lang-' + idLang).show();

            if (isoCode) {
                $('.js-locale-btn').text(isoCode);
            }
        };

        $('.dynamic-reg-table').each(function() {
            var entity = $(this).data('entity');
            dynamicRowIndexes[entity] = $(this).find('tr.dynamic-row').length;
        });

        $(document).on('click', '.add-dynamic-row', function(e) {
            e.preventDefault();

            var entity = $(this).data('entity');
            var $table = $('.dynamic-reg-table[data-entity="' + entity + '"]');
            var $template = $table.find('tr.dynamic-template-row').first().clone();
            var rowIndex = 'new_' + dynamicRowIndexes[entity];

            dynamicRowIndexes[entity]++;

            $template.removeClass('dynamic-template-row').addClass('dynamic-row').show();

            $template.find('[data-name]').each(function() {
                var inputName = $(this).attr('data-name')
                    .replace('__ENTITY__', entity)
                    .replace(/__INDEX__/g, rowIndex);

                $(this).attr('name', inputName).removeAttr('data-name');
            });

            $table.find('tbody').append($template);
            showDynamicLanguage({$default_form_language|intval}, '{$default_iso_code|escape:'javascript':'UTF-8'}');
        });

        $(document).on('click', '.remove-dynamic-row', function(e) {
            e.preventDefault();
            $(this).closest('tr').remove();
        });

        $(document).on('click', '.js-change-language', function(e) {
            e.preventDefault();

            var idLang = $(this).data('id-lang');
            var isoCode = $(this).data('iso-code');

            showDynamicLanguage(idLang, isoCode);
        });

        showDynamicLanguage({$default_form_language|intval}, '{$default_iso_code|escape:'javascript':'UTF-8'}');
    });
</script>
