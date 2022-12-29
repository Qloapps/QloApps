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

{extends file='helpers/form/form.tpl'}

{block name="fieldset"}
    <div class="panel" id="general">
        <div class="panel-heading">
            <i class="icon icon-pencil"></i>
            {l s='Category Information' mod='qlohotelreview'}
        </div>

        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{l s='Enable to use this category.' mod='qlohotelreview'}">
                        {l s='Enabled' mod='qlohotelreview'}
                    </span>
                </label>
                <div class="col-lg-9">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio" name="active" id="active_on" value="1" {if $currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if}>
                        <label for="active_on">{l s='Yes' mod='qlohotelreview'}</label>
                        <input type="radio" name="active" id="active_off" value="0" {if !$currentTab->getFieldValue($currentObject, 'active')|intval}checked="checked"{/if}>
                        <label for="active_off">{l s='No' mod='qlohotelreview'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3 required">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="{l s='Enter a name for this category.' mod='qlohotelreview'}">
                        {l s='Name' mod='qlohotelreview'}
                    </span>
                </label>
                <div class="col-lg-9">
                    {if $languages|count > 0}
                        {foreach $languages as $language}
                            {assign var="value_text" value=$currentTab->getFieldValue($currentObject, "name", $language.id_lang)}
                            <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                <div class="col-lg-6">
                                    <input type="text" name="name_{$language.id_lang}" class="" value="{$value_text|escape:"html":"UTF-8"}">
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                        {$language.iso_code}
                                        <i class="icon-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        {foreach from=$languages item=language}
                                            <li>
                                                <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                            </div>
                        {/foreach}
                    {/if}
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" value="1" name="submitCategory" class="btn btn-default pull-right">
                <i class="process-icon-save"></i>
                {l s='Save' mod='qlohotelreview'}
            </button>
            <button type="submit" value="1" name="submitCategoryAndStay" class="btn btn-default pull-right">
                <i class="process-icon-save"></i>
                {l s='Save and stay' mod='qlohotelreview'}
            </button>
            <a href="javascript:void(0);" class="btn btn-default" onclick="window.history.back();">
                <i class="process-icon-cancel"></i>
                {l s='Cancel' mod='qlohotelreview'}
            </a>
        </div>
    </div>
{/block}