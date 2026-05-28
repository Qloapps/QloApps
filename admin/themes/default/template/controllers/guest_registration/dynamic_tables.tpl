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

<form method="post" action="{$dynamic_form_action|escape:'html':'UTF-8'}" class="guest-registration-dynamic-form">
    {assign var=default_iso_code value=''}
    {foreach from=$languages item=language}
        {if $language.id_lang == $default_form_language}
            {assign var=default_iso_code value=$language.iso_code}
        {/if}
    {/foreach}
    <div class="panel">
        <h3><i class="icon-list-ul"></i> {l s='Dynamic values'}</h3>
        <div class="alert alert-info">
            {l s='Note: If no options are added or active for the dynamic fields below (Purpose of Visit, Identity Proof, Payment Method), the corresponding sections on the PDF will display blank lines, requiring guests to fill them in manually.'}
        </div>
        <div class="panel">
            <h3><i class="icon-plane"></i> {l s='Purpose of visit'}</h3>
            {assign var=entity_name value='guest_visit_purpose'}
            {assign var=entity_rows value=$guest_visit_purpose_rows}
            {if isset($smarty.post.$entity_name) && is_array($smarty.post.$entity_name)}
                {assign var=entity_rows value=$smarty.post.$entity_name}
            {/if}
            <div class="table-responsive-row clearfix">
                <table class="table dynamic-reg-table" data-entity="{$entity_name}">
                    <thead>
                        <tr>
                            <th style="display:none;">{l s='ID'}</th>
                            <th>{l s='Name'}</th>
                            <th class="fixed-width-md text-center">{l s='Active'}</th>
                            <th class="fixed-width-md text-center">{l s='Action'}</th>
                        </tr>
                    </thead>
	                    <tbody>
	                        {foreach from=$entity_rows key=rowKey item=row}
	                            <tr class="dynamic-row">
	                                <td style="display:none;">
	                                    <input type="hidden" name="{$entity_name}[{$rowKey|escape:'html':'UTF-8'}][id]" value="{$row.id|intval}" />
	                                </td>
	                                <td class="dynamic-name-cell">
	                                    {foreach from=$languages item=language}
	                                        <div class="translatable-field row lang-{$language.id_lang}" {if $language.id_lang != $default_form_language}style="display:none;"{/if}>
	                                            <div class="col-lg-{if $languages|count > 1}9{else}12{/if}">
	                                                <input type="text" class="form-control" name="{$entity_name}[{$rowKey|escape:'html':'UTF-8'}][name][{$language.id_lang|intval}]" value="{$row.name[$language.id_lang]|default:''|escape:'html':'UTF-8'}" />
	                                            </div>
                                            {if $languages|count > 1}
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
                                            {/if}
                                        </div>
                                    {/foreach}
                                </td>
	                                <td class="text-center">
	                                    <input type="hidden" class="dynamic-active-input" name="{$entity_name}[{$rowKey|escape:'html':'UTF-8'}][active]" value="{if $row.active}1{else}0{/if}" />
	                                    <a href="#" class="dynamic-status-toggle list-action-enable {if $row.active}action-enabled{else}action-disabled{/if}" data-active="{if $row.active}1{else}0{/if}" title="{if $row.active}{l s='Enabled'}{else}{l s='Disabled'}{/if}">
	                                        <i class="icon-check{if !$row.active} hidden{/if}"></i>
	                                        <i class="icon-remove{if $row.active} hidden{/if}"></i>
	                                    </a>
	                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-default remove-dynamic-row"><i class="icon-trash"></i> {l s='Remove'}</button>
                                </td>
                            </tr>
	                        {/foreach}
	                        <tr class="dynamic-empty-row" {if $entity_rows}style="display:none;"{/if}>
	                            <td class="list-empty" colspan="4">
	                                <div class="list-empty-msg">
	                                    <i class="icon-warning-sign list-empty-icon"></i>
	                                    {l s='No records found'}
	                                </div>
	                            </td>
	                        </tr>
	                        <tr class="dynamic-template-row" style="display:none;">
                            <td style="display:none;"><input type="hidden" data-name="__ENTITY__[__INDEX__][id]" value="" /></td>
                            <td class="dynamic-name-cell">
                                {foreach from=$languages item=language}
                                    <div class="translatable-field row lang-{$language.id_lang}" {if $language.id_lang != $default_form_language}style="display:none;"{/if}>
                                        <div class="col-lg-{if $languages|count > 1}9{else}12{/if}">
                                            <input type="text" class="form-control" data-name="__ENTITY__[__INDEX__][name][{$language.id_lang|intval}]" value="" />
                                        </div>
                                        {if $languages|count > 1}
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
                                        {/if}
                                    </div>
                                {/foreach}
                            </td>
	                            <td class="text-center">
	                                <input type="hidden" class="dynamic-active-input" data-name="__ENTITY__[__INDEX__][active]" value="1" />
	                                <a href="#" class="dynamic-status-toggle list-action-enable action-enabled" data-active="1" title="{l s='Enabled'}">
	                                    <i class="icon-check"></i>
	                                    <i class="icon-remove hidden"></i>
	                                </a>
	                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-default remove-dynamic-row"><i class="icon-trash"></i> {l s='Remove'}</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-default add-dynamic-row" data-entity="{$entity_name}"><i class="icon-plus"></i> {l s='Add New field'}</button>
        </div>

        <div class="panel">
            <h3><i class="icon-file-text"></i> {l s='Identity proof'}</h3>
            {assign var=entity_name value='reg_id_proof'}
            {assign var=entity_rows value=$reg_id_proof_rows}
            {if isset($smarty.post.$entity_name) && is_array($smarty.post.$entity_name)}
                {assign var=entity_rows value=$smarty.post.$entity_name}
            {/if}
            <div class="table-responsive-row clearfix">
                <table class="table dynamic-reg-table" data-entity="{$entity_name}">
                    <thead>
                        <tr>
                            <th style="display:none;">{l s='ID'}</th>
                            <th>{l s='Name'}</th>
                            <th class="fixed-width-md text-center">{l s='Active'}</th>
                            <th class="fixed-width-md text-center">{l s='Action'}</th>
                        </tr>
                    </thead>
	                    <tbody>
	                        {foreach from=$entity_rows key=rowKey item=row}
	                            <tr class="dynamic-row">
	                                <td style="display:none;">
	                                    <input type="hidden" name="{$entity_name}[{$rowKey|escape:'html':'UTF-8'}][id]" value="{$row.id|intval}" />
	                                </td>
	                                <td class="dynamic-name-cell">
	                                    {foreach from=$languages item=language}
	                                        <div class="translatable-field row lang-{$language.id_lang}" {if $language.id_lang != $default_form_language}style="display:none;"{/if}>
	                                            <div class="col-lg-{if $languages|count > 1}9{else}12{/if}">
	                                                <input type="text" class="form-control" name="{$entity_name}[{$rowKey|escape:'html':'UTF-8'}][name][{$language.id_lang|intval}]" value="{$row.name[$language.id_lang]|default:''|escape:'html':'UTF-8'}" />
	                                            </div>
                                            {if $languages|count > 1}
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
                                            {/if}
                                        </div>
                                    {/foreach}
                                </td>
	                                <td class="text-center">
	                                    <input type="hidden" class="dynamic-active-input" name="{$entity_name}[{$rowKey|escape:'html':'UTF-8'}][active]" value="{if $row.active}1{else}0{/if}" />
	                                    <a href="#" class="dynamic-status-toggle list-action-enable {if $row.active}action-enabled{else}action-disabled{/if}" data-active="{if $row.active}1{else}0{/if}" title="{if $row.active}{l s='Enabled'}{else}{l s='Disabled'}{/if}">
	                                        <i class="icon-check{if !$row.active} hidden{/if}"></i>
	                                        <i class="icon-remove{if $row.active} hidden{/if}"></i>
	                                    </a>
	                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-default remove-dynamic-row"><i class="icon-trash"></i> {l s='Remove'}</button>
                                </td>
                            </tr>
	                        {/foreach}
	                        <tr class="dynamic-empty-row" {if $entity_rows}style="display:none;"{/if}>
	                            <td class="list-empty" colspan="4">
	                                <div class="list-empty-msg">
	                                    <i class="icon-warning-sign list-empty-icon"></i>
	                                    {l s='No records found'}
	                                </div>
	                            </td>
	                        </tr>
	                        <tr class="dynamic-template-row" style="display:none;">
                            <td style="display:none;"><input type="hidden" data-name="__ENTITY__[__INDEX__][id]" value="" /></td>
                            <td class="dynamic-name-cell">
                                {foreach from=$languages item=language}
                                    <div class="translatable-field row lang-{$language.id_lang}" {if $language.id_lang != $default_form_language}style="display:none;"{/if}>
                                        <div class="col-lg-{if $languages|count > 1}9{else}12{/if}">
                                            <input type="text" class="form-control" data-name="__ENTITY__[__INDEX__][name][{$language.id_lang|intval}]" value="" />
                                        </div>
                                        {if $languages|count > 1}
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
                                        {/if}
                                    </div>
                                {/foreach}
                            </td>
	                            <td class="text-center">
	                                <input type="hidden" class="dynamic-active-input" data-name="__ENTITY__[__INDEX__][active]" value="1" />
	                                <a href="#" class="dynamic-status-toggle list-action-enable action-enabled" data-active="1" title="{l s='Enabled'}">
	                                    <i class="icon-check"></i>
	                                    <i class="icon-remove hidden"></i>
	                                </a>
	                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-default remove-dynamic-row"><i class="icon-trash"></i> {l s='Remove'}</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-default add-dynamic-row" data-entity="{$entity_name}"><i class="icon-plus"></i> {l s='Add New field'}</button>
        </div>

        <div class="panel">
            <h3><i class="icon-credit-card"></i> {l s='Payment method'}</h3>
            {assign var=entity_name value='guest_reg_payment_method'}
            {assign var=entity_rows value=$guest_reg_payment_method_rows}
            {if isset($smarty.post.$entity_name) && is_array($smarty.post.$entity_name)}
                {assign var=entity_rows value=$smarty.post.$entity_name}
            {/if}
            <div class="table-responsive-row clearfix">
                <table class="table dynamic-reg-table" data-entity="{$entity_name}">
                    <thead>
                        <tr>
                            <th style="display:none;">{l s='ID'}</th>
                            <th>{l s='Name'}</th>
                            <th class="fixed-width-md text-center">{l s='Active'}</th>
                            <th class="fixed-width-md text-center">{l s='Action'}</th>
                        </tr>
                    </thead>
	                    <tbody>
	                        {foreach from=$entity_rows key=rowKey item=row}
	                            <tr class="dynamic-row">
	                                <td style="display:none;">
	                                    <input type="hidden" name="{$entity_name}[{$rowKey|escape:'html':'UTF-8'}][id]" value="{$row.id|intval}" />
	                                </td>
	                                <td class="dynamic-name-cell">
	                                    {foreach from=$languages item=language}
	                                        <div class="translatable-field row lang-{$language.id_lang}" {if $language.id_lang != $default_form_language}style="display:none;"{/if}>
	                                            <div class="col-lg-{if $languages|count > 1}9{else}12{/if}">
	                                                <input type="text" class="form-control" name="{$entity_name}[{$rowKey|escape:'html':'UTF-8'}][name][{$language.id_lang|intval}]" value="{$row.name[$language.id_lang]|default:''|escape:'html':'UTF-8'}" />
	                                            </div>
                                            {if $languages|count > 1}
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
                                            {/if}
                                        </div>
                                    {/foreach}
                                </td>
	                                <td class="text-center">
	                                    <input type="hidden" class="dynamic-active-input" name="{$entity_name}[{$rowKey|escape:'html':'UTF-8'}][active]" value="{if $row.active}1{else}0{/if}" />
	                                    <a href="#" class="dynamic-status-toggle list-action-enable {if $row.active}action-enabled{else}action-disabled{/if}" data-active="{if $row.active}1{else}0{/if}" title="{if $row.active}{l s='Enabled'}{else}{l s='Disabled'}{/if}">
	                                        <i class="icon-check{if !$row.active} hidden{/if}"></i>
	                                        <i class="icon-remove{if $row.active} hidden{/if}"></i>
	                                    </a>
	                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-default remove-dynamic-row"><i class="icon-trash"></i> {l s='Remove'}</button>
                                </td>
                            </tr>
	                        {/foreach}
	                        <tr class="dynamic-empty-row" {if $entity_rows}style="display:none;"{/if}>
	                            <td class="list-empty" colspan="4">
	                                <div class="list-empty-msg">
	                                    <i class="icon-warning-sign list-empty-icon"></i>
	                                    {l s='No records found'}
	                                </div>
	                            </td>
	                        </tr>
	                        <tr class="dynamic-template-row" style="display:none;">
                            <td style="display:none;"><input type="hidden" data-name="__ENTITY__[__INDEX__][id]" value="" /></td>
                            <td class="dynamic-name-cell">
                                {foreach from=$languages item=language}
                                    <div class="translatable-field row lang-{$language.id_lang}" {if $language.id_lang != $default_form_language}style="display:none;"{/if}>
                                        <div class="col-lg-{if $languages|count > 1}9{else}12{/if}">
                                            <input type="text" class="form-control" data-name="__ENTITY__[__INDEX__][name][{$language.id_lang|intval}]" value="" />
                                        </div>
                                        {if $languages|count > 1}
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
                                        {/if}
                                    </div>
                                {/foreach}
                            </td>
	                            <td class="text-center">
	                                <input type="hidden" class="dynamic-active-input" data-name="__ENTITY__[__INDEX__][active]" value="1" />
	                                <a href="#" class="dynamic-status-toggle list-action-enable action-enabled" data-active="1" title="{l s='Enabled'}">
	                                    <i class="icon-check"></i>
	                                    <i class="icon-remove hidden"></i>
	                                </a>
	                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-default remove-dynamic-row"><i class="icon-trash"></i> {l s='Remove'}</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-default add-dynamic-row" data-entity="{$entity_name}"><i class="icon-plus"></i> {l s='Add New field'}</button>
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
	        var enabledTitle = '{l s='Enabled' js=1}';
	        var disabledTitle = '{l s='Disabled' js=1}';
	        var showDynamicLanguage = function(idLang, isoCode) {
	            $('.translatable-field').hide();
	            $('.lang-' + idLang).show();

            if (isoCode) {
                $('.js-locale-btn').text(isoCode);
            }
        };

	        $('.dynamic-reg-table').each(function() {
	            var entity = $(this).data('entity');
	            var nextIndex = $(this).find('tr.dynamic-row').length;
	            var newRowPattern = new RegExp('^' + entity + '\\[new_(\\d+)\\]');

	            $(this).find(':input[name]').each(function() {
	                var match = ($(this).attr('name') || '').match(newRowPattern);
	                if (match) {
	                    nextIndex = Math.max(nextIndex, parseInt(match[1], 10) + 1);
	                }
	            });

	            dynamicRowIndexes[entity] = nextIndex;
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

	            $table.find('tr.dynamic-empty-row').hide();
	            $table.find('tbody').append($template);
	            showDynamicLanguage({$default_form_language|intval}, '{$default_iso_code|escape:'javascript':'UTF-8'}');
	        });

	        $(document).on('click', '.remove-dynamic-row', function(e) {
	            e.preventDefault();
	            var $table = $(this).closest('table.dynamic-reg-table');
	            $(this).closest('tr').remove();

	            if (!$table.find('tr.dynamic-row').length) {
	                $table.find('tr.dynamic-empty-row').show();
	            }
	        });

	        $(document).on('click', '.dynamic-status-toggle', function(e) {
	            e.preventDefault();

	            var $toggle = $(this);
	            var isActive = parseInt($toggle.attr('data-active'), 10) === 1;
	            var nextActive = isActive ? 0 : 1;

	            $toggle.attr('data-active', nextActive)
	                .toggleClass('action-enabled', nextActive === 1)
	                .toggleClass('action-disabled', nextActive === 0)
	                .attr('title', nextActive === 1 ? enabledTitle : disabledTitle)
	                .closest('td')
	                .find('.dynamic-active-input')
	                .val(nextActive);

	            $toggle.find('.icon-check').toggleClass('hidden', nextActive !== 1);
	            $toggle.find('.icon-remove').toggleClass('hidden', nextActive === 1);
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
