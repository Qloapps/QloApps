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

<div class="panel advanced_price_rule {if isset($rowsToHighlight) && in_array($key, $rowsToHighlight)}error-border{/if}" data-advanced_price_rule_index="{$key}">
    <input type="hidden" name="restriction[{$key}][id]" value="{if isset($restriction['id'])}{$restriction['id']}{/if}">
    <div class="row advance_price_rule_header_container {if !isset($collapse)}shown{/if}" data-toggle="collapse" data-target="#advanced_price_rule_{$key}">
        <div class="col-xs-9 advance_price_rule_header"></div>
        <div class="col-xs-3">
            <div class="col-xs-offset-7 col-xs-2">
                <a class="btn btn-default remove_advanced_price_rule">
                    <i class="icon-trash"></i>
                </a>
            </div>
            <div class="col-xs-offset-1 col-xs-2">
                <a class="btn btn-default">
                    <i class="icon-caret-down"></i>
                </a>
            </div>
        </div>
    </div>
    <div id="advanced_price_rule_{$key}" class="collapse advanced_price_rule_body {if isset($collapse)}in{/if}">
        <div class="form-group">
            <label for="restriction[{$key}][date_selection_type]" class="control-label col-xs-4">
                {l s='Date Selection type' mod='hotelreservationsystem'}
            </label>
            <div class="col-xs-5">
                <select class="form-control date_selection_type" name="restriction[{$key}][date_selection_type]" id="date_selection_type_{$key}">
                    <option value="{HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE}" {if isset($restriction['date_selection_type']) && $restriction['date_selection_type'] == HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE}selected="seleted"{/if}>
                        {l s='Date Range' mod='hotelreservationsystem'}
                    </option>
                    <option value="{HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_SPECIFIC}" {if isset($restriction['date_selection_type']) && $restriction['date_selection_type'] == HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_SPECIFIC}selected="seleted"{/if}>
                        {l s='Specific Date' mod='hotelreservationsystem'}
                    </option>
                </select>
            </div>
            <div class="col-xs-3 advanced_price_rule_body_actions">
                <div class="col-xs-offset-7 col-xs-2">
                    <a class="btn btn-default remove_advanced_price_rule">
                        <i class="icon-trash"></i>
                    </a>
                </div>
                <div class="col-xs-offset-1 col-xs-2">
                    <a class="btn btn-default" data-toggle="collapse" data-target="#advanced_price_rule_{$key}">
                        <i class="icon-caret-up"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="form-group specific_date_type_{$key}" {if !isset($restriction['date_selection_type']) || $restriction['date_selection_type'] == HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE}style="display:none;"{/if}>
            <label class="col-xs-4 control-label required" for="restriction[{$key}][specific_date]" >
                {l s='Specific Date' mod='hotelreservationsystem'}
            </label>
            <div class="col-xs-5">
                <input type="text" id="specific_date_{$key}" name="restriction[{$key}][specific_date]" class="specific_date form-control datepicker-input" value="{if isset($restriction['specific_date'])}{$restriction['specific_date']}{else}{$date_from}{/if}" readonly/>
            </div>
        </div>

        <div class="form-group date_range_type_{$key}" {if isset($restriction['date_selection_type']) && $restriction['date_selection_type'] != HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE}style="display:none;"{/if}>
            <label class="col-xs-4 control-label required" for="restriction[{$key}][date_from]" >
                {l s='Date From' mod='hotelreservationsystem'}
            </label>
            <div class="col-xs-5">
                <input type="text" id="feature_plan_date_from_{$key}" name="restriction[{$key}][date_from]" class="feature_plan_date_from form-control datepicker-input" value="{if isset($restriction['date_from'])}{$restriction['date_from']}{else}{$date_from}{/if}" readonly/>
            </div>
        </div>
        <div class="form-group date_range_type_{$key}" {if isset($restriction['date_selection_type']) && $restriction['date_selection_type'] != HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE}style="display:none;"{/if}>
            <label class="col-xs-4 control-label required" for="restriction[{$key}][date_to]" >
                {l s='Date To' mod='hotelreservationsystem'}
            </label>
            <div class="col-xs-5">
                <input type="text" id="feature_plan_date_to_{$key}" name="restriction[{$key}][date_to]" class="feature_plan_date_to form-control datepicker-input" value="{if isset($restriction['date_to'])}{$restriction['date_to']}{else}{$date_to}{/if}" readonly/>
            </div>
        </div>

        <div class="form-group special_days_content_{$key}" {if isset($restriction['date_selection_type']) && $restriction['date_selection_type'] != HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE}style="display:none;"{/if}>
            <label class="control-label col-xs-4" for="restriction[{$key}][is_special_days_exists]">
                <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Enable this option to add restriction to specific week days (for example, weekends) of the selected date range.' mod='hotelreservationsystem'}">
                    {l s='Restrict to Week Days' mod='hotelreservationsystem'}
                </span>
            </label>
            <div class="col-xs-5">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" value="1" id="restriction[{$key}][is_special_days_exists_on]" name="restriction[{$key}][is_special_days_exists]" class="is_special_days_exists" {if isset($restriction['is_special_days_exists']) && $restriction['is_special_days_exists']}checked="checked"{/if}>
                    <label for="restriction[{$key}][is_special_days_exists_on]">{l s='Yes' mod='hotelreservationsystem'}</label>
                    <input type="radio" value="0" id="restriction[{$key}][is_special_days_exists_off]" name="restriction[{$key}][is_special_days_exists]" class="is_special_days_exists" {if !isset($restriction['is_special_days_exists']) || !$restriction['is_special_days_exists']}checked="checked"{/if}>
                    <label for="restriction[{$key}][is_special_days_exists_off]">{l s='No' mod='hotelreservationsystem'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>

        <div class="form-group week_days week_days_{$key}" {if isset($restriction['special_days']) && is_array($restriction['special_days']) && isset($restriction['is_special_days_exists']) && $restriction['is_special_days_exists'] && isset($restriction['date_selection_type']) && $restriction['date_selection_type'] == HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE}style="display:block;"{/if}>
            <label for="restriction[{$key}][special_days]" class="control-label col-xs-4">
                {l s='Select Week Days' mod='hotelreservationsystem'}
            </label>
            <div class="col-xs-8 checkboxes-wrap">
            {foreach $week_days as $dayVal => $day}
                <div class="day-wrap">
                    <input type="checkbox" name="restriction[{$key}][special_days][]" value="{$dayVal}" {if (isset($restriction['special_days']) && is_array($restriction['special_days']) && in_array($dayVal, $restriction['special_days']))}checked="checked" {/if}/>
                    <p>{$day}</p>
                </div>
            {/foreach}
            </div>
        </div>
    </div>
</div>
