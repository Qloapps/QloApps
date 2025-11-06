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

<form method="POST" id="search_hotel_block_form">
    {hook h='displaySearchFormFieldsBefore'}
    {block name="search_form_fields_wrapper"}
        <div class="grid" id="search_form_fields_wrapper" style="grid-template-columns: repeat({$total_columns}, 1fr);">
            {block name='search_form_location'}
                {if isset($location_enabled) && $location_enabled}
                    <div class="form-group grid-item area-4" style="grid-column: span 4;">
                        <div class="dropdown">
                            <input type="text" class="form-control header-rmsearch-input input-location" id="hotel_location" name="hotel_location" autocomplete="off" placeholder="{l s='Hotel Location' mod='wkroomsearchblock'}" {if isset($search_data['location'])}value="{$search_data['location']|escape:'htmlall':'UTF-8'}"{/if}>
                            <input hidden="hidden" name="location_category_id" id="location_category_id" {if isset($search_data['location_category_id'])}value="{$search_data['location_category_id']|escape:'htmlall':'UTF-8'}"{/if}>
                            <ul class="location_search_results_ul dropdown-menu"></ul>
                        </div>
                    </div>
                {/if}
            {/block}
            {block name='search_form_hotel'}
                {if count($hotels_info) <= 1 && !$show_hotel_name}
                    <input type="hidden" id="max_order_date" name="max_order_date" value="{if isset($hotels_info[0]['max_order_date'])}{$hotels_info[0]['max_order_date']|escape:'htmlall':'UTF-8'}{/if}">
                    <input type="hidden" id="min_booking_offset" name="min_booking_offset" value="{if isset($hotels_info[0]['min_booking_offset'])}{$hotels_info[0]['min_booking_offset']|escape:'htmlall':'UTF-8'}{/if}">
                    <input type="hidden" id="hotel_cat_id" name="hotel_cat_id" value="{$hotels_info[0]['id_category']}">
                    <input type="hidden" id="id_hotel" name="id_hotel" value="{$hotels_info[0]['id']|escape:'htmlall':'UTF-8'}">
                    <input type="hidden" id="htl_name" class="form-control header-rmsearch-input" value="{$hotels_info[0]['hotel_name']}" readonly>
                {else}
                    <div class="form-group grid-item area-5" style="grid-column: span 5;">
                        <input type="hidden" id="hotel_cat_id" name="hotel_cat_id" {if isset($search_data)}value="{$search_data['htl_dtl']['id_category']|escape:'htmlall':'UTF-8'}"{/if}>
                        <input type="hidden" id="id_hotel" name="id_hotel" {if isset($search_data)}value="{$search_data['htl_dtl']['id']|escape:'htmlall':'UTF-8'}"{/if}>
                        <input type="hidden" id="max_order_date" name="max_order_date" value="{if isset($max_order_date)}{$max_order_date|escape:'htmlall':'UTF-8'}{/if}">
                        <input type="hidden" id="min_booking_offset" name="min_booking_offset" value="{if isset($min_booking_offset)}{$min_booking_offset|escape:'htmlall':'UTF-8'}{/if}">

                        <div class="hotel-selector-wrap {if isset($language_is_rtl) && $language_is_rtl}rtl{/if}">
                            <select name="id_hotel" class="chosen header-rmsearch-input" data-placeholder="{l s='Select Hotel' mod='wkroomsearchblock'}" id="id_hotel_button">
                                <option value=""></option>
                                {foreach $hotels_info as $name_val}
                                    <option class="search_result_li" value="{$name_val['id']|escape:'htmlall':'UTF-8'}" data-id-hotel="{$name_val['id']|escape:'htmlall':'UTF-8'}" data-hotel-cat-id="{$name_val['id_category']|escape:'htmlall':'UTF-8'}" data-max_order_date="{$name_val['max_order_date']}" data-min_booking_offset="{$name_val['min_booking_offset']|escape:'htmlall':'UTF-8'}" {if isset($search_data) && $name_val['id'] == $search_data['htl_dtl']['id']}selected{/if}>{$name_val['hotel_name']|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                {/if}
            {/block}

            {block name='search_form_dates'}
                {if isset($multiple_dates_input) && $multiple_dates_input}
                    <div class="grid-item area-5 multi-date" id="daterange_value" style="grid-column: span 5;">
                        <div class="form-group">
                            <input type="hidden" id="check_in_time" name="check_in_time" {if isset($search_data['date_from'])}value="{$search_data['date_from']|escape:'htmlall':'UTF-8'}"{/if}>
                            <div class="form-control header-rmsearch-input input-date" autocomplete="off" id="daterange_value_from" placeholder="{l s='Check-in' mod='wkroomsearchblock'}"><span>{l s='Check-in' mod='wkroomsearchblock'}</span></div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" id="check_out_time" name="check_out_time" {if isset($search_data['date_to'])}value="{$search_data['date_to']|escape:'htmlall':'UTF-8'}"{/if}>
                            <div class="form-control header-rmsearch-input input-date" autocomplete="off" id="daterange_value_to" placeholder="{l s='Check-out' mod='wkroomsearchblock'}"><span>{l s='Check-out' mod='wkroomsearchblock'}</span></div>
                        </div>
                    </div>
                {else}
                    <div class="form-group grid-item area-5" style="grid-column: span 5;">
                        <input type="hidden" id="check_in_time" name="check_in_time" {if isset($search_data['date_from'])}value="{$search_data['date_from']|escape:'htmlall':'UTF-8'}"{/if}>
                        <input type="hidden" id="check_out_time" name="check_out_time" {if isset($search_data['date_to'])}value="{$search_data['date_to']|escape:'htmlall':'UTF-8'}"{/if}>
                        <div class="form-control header-rmsearch-input input-date" id="daterange_value"  autocomplete="off" placeholder="{l s='Check-in - Check-out' mod='wkroomsearchblock'}" tabindex="-1"><span>{l s='Check-in' mod='wkroomsearchblock'} &nbsp;<i class="icon icon-minus"></i>&nbsp; {l s='Check-out' mod='wkroomsearchblock'}</span></div>
                    </div>
                {/if}
            {/block}

            {block name='search_form_occupancy'}
                {if isset($is_occupancy_wise_search) && $is_occupancy_wise_search}
                    <div class="form-group grid-item area-4" style="grid-column: span 4;">
                        <div class="dropdown">
                            <button class="form-control input-occupancy header-rmsearch-input {if isset($error) && $error == 1}error_border{/if}" type="button" data-toggle="dropdown" id="guest_occupancy">
                                <span class="pull-left">{if (isset($search_data['occupancy_adults']) && $search_data['occupancy_adults'])}{$search_data['occupancy_adults']} {if $search_data['occupancy_adults'] > 1}{l s='Adults' mod='wkroomsearchblock'}{else}{l s='Adult' mod='wkroomsearchblock'}{/if}, {if isset($search_data['occupancy_children']) && $search_data['occupancy_children']}{$search_data['occupancy_children']} {if $search_data['occupancy_children'] > 1}
                                {l s='Children' mod='wkroomsearchblock'}{else}{l s='Child' mod='wkroomsearchblock'}{/if}, {/if}{$search_data['occupancies']|count} {if $search_data['occupancies']|count > 1}{l s='Rooms' mod='wkroomsearchblock'}{else}{l s='Room' mod='wkroomsearchblock'}{/if}{else}{l s='1 Adult, 1 Room' mod='wkroomsearchblock'}{/if}</span>
                            </button>
                            <div id="search_occupancy_wrapper" class="dropdown-menu">
                                <div id="occupancy_inner_wrapper">
                                    {if isset($search_data['occupancies']) && $search_data['occupancies']}
                                        {assign var=countRoom value=1}
                                        {foreach from=$search_data['occupancies'] key=key item=$occupancy name=occupancyInfo}
                                            <div class="occupancy-room-block">
                                                <div class="occupancy_info_head"><span class="room_num_wrapper">{l s='Room' mod='wkroomsearchblock'} - {$countRoom|escape:'htmlall':'UTF-8'} </span>{if !$smarty.foreach.occupancyInfo.first}<a class="remove-room-link pull-right" href="#">{l s='Remove' mod='wkroomsearchblock'}</a>{/if}</div>
                                                <div class="occupancy_info_block" occ_block_index="{$key|escape:'htmlall':'UTF-8'}">
                                                    <div class="row">
                                                        <div class="form-group occupancy_count_block col-sm-5 col-xs-6">
                                                            <label>{l s='Adults' mod='wkroomsearchblock'}</label>
                                                            <div>
                                                                <input type="hidden" class="num_occupancy num_adults room_occupancies" name="occupancy[{$key|escape:'htmlall':'UTF-8'}][adults]" value="{$occupancy['adults']|escape:'htmlall':'UTF-8'}">
                                                                <div class="occupancy_count pull-left">
                                                                    <span>{$occupancy['adults']|escape:'htmlall':'UTF-8'}</span>
                                                                </div>
                                                                <div class="qty_direction pull-left">
                                                                    <a href="#" data-field-qty="qty" class="btn btn-default occupancy_quantity_up">
                                                                        <span><i class="icon-plus"></i></span>
                                                                    </a>
                                                                    <a href="#" data-field-qty="qty" class="btn btn-default occupancy_quantity_down">
                                                                        <span><i class="icon-minus"></i></span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group occupancy_count_block col-sm-7 col-xs-6">
                                                            <label>{l s='Children' mod='wkroomsearchblock'}</label>
                                                            <div class="clearfix">
                                                                <input type="hidden" class="num_occupancy num_children room_occupancies" name="occupancy[{$key|escape:'htmlall':'UTF-8'}][children]" value="{$occupancy['children']|escape:'htmlall':'UTF-8'}">
                                                                <div class="occupancy_count pull-left">
                                                                    <span>{$occupancy['children']|escape:'htmlall':'UTF-8'}</span>
                                                                </div>
                                                                <div class="qty_direction pull-left">
                                                                    <a href="#" data-field-qty="qty" class="btn btn-default occupancy_quantity_up">
                                                                        <span><i class="icon-plus"></i></span>
                                                                    </a>
                                                                    <a href="#" data-field-qty="qty" class="btn btn-default occupancy_quantity_down">
                                                                        <span><i class="icon-minus"></i></span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <p class="label-desc-txt">({l s='Below' mod='wkroomsearchblock'}  {$max_child_age|escape:'htmlall':'UTF-8'} {l s='years' mod='wkroomsearchblock'})</p>
                                                        </div>
                                                    </div>
                                                    <p style="display:none;"><span class="text-danger occupancy-input-errors"></span></p>

                                                    <div class="row">
                                                        <div class="form-group children_age_info_block col-sm-12" {if isset($occupancy['child_ages']) && $occupancy['child_ages']}style="display:block;"{/if}>
                                                            <label>{l s='All Children' mod='wkroomsearchblock'}</label>
                                                            <div class="children_ages">
                                                                {if isset($occupancy['child_ages']) && $occupancy['child_ages']}
                                                                    {foreach $occupancy['child_ages'] as $childAge}
                                                                        <div>
                                                                            <select class="guest_child_age room_occupancies" name="occupancy[{$key|escape:'htmlall':'UTF-8'}][child_ages][]">
                                                                                <option value="-1" {if $childAge == -1}selected{/if}>{l s='Select 1' mod='wkroomsearchblock'}</option>
                                                                                <option value="0" {if $childAge == 0}selected{/if}>{l s='Under 1' mod='wkroomsearchblock'}</option>
                                                                                {for $age=1 to ($max_child_age-1)}
                                                                                    <option value="{$age|escape:'htmlall':'UTF-8'}" {if $childAge == $age}selected{/if}>{$age|escape:'htmlall':'UTF-8'}</option>
                                                                                {/for}
                                                                            </select>
                                                                        </div>
                                                                    {/foreach}
                                                                {/if}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr class="occupancy-info-separator">
                                            </div>
                                            {assign var=countRoom value=$countRoom+1}
                                        {/foreach}
                                    {else}
                                        <div class="occupancy-room-block">
                                            <div class="occupancy_info_head"><span class="room_num_wrapper">{l s='Room - 1' mod='wkroomsearchblock'}</span></div>
                                            <div class="occupancy_info_block" occ_block_index="0">
                                                <div class="row">
                                                    <div class="form-group occupancy_count_block col-sm-5 col-xs-6">
                                                        <label>{l s='Adults' mod='wkroomsearchblock'}</label>
                                                        <div>
                                                            <input type="hidden" class="num_occupancy num_adults room_occupancies" name="occupancy[0][adults]" value="1">
                                                            <div class="occupancy_count pull-left">
                                                                <span>1</span>
                                                            </div>
                                                            <div class="qty_direction pull-left">
                                                                <a href="#" data-field-qty="qty" class="btn btn-default occupancy_quantity_up">
                                                                    <span>
                                                                        <i class="icon-plus"></i>
                                                                    </span>
                                                                </a>
                                                                <a href="#" data-field-qty="qty" class="btn btn-default occupancy_quantity_down">
                                                                    <span>
                                                                        <i class="icon-minus"></i>
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group occupancy_count_block col-sm-7 col-xs-6">
                                                        <label>{l s='Children' mod='wkroomsearchblock'}</label>
                                                        <div class="clearfix">
                                                            <input type="hidden" class="num_occupancy num_children room_occupancies" name="occupancy[0][children]" value="0">
                                                            <div class="occupancy_count pull-left">
                                                                <span>0</span>
                                                            </div>
                                                            <div class="qty_direction pull-left">
                                                                <a href="#" data-field-qty="qty" class="btn btn-default occupancy_quantity_up">
                                                                    <span>
                                                                        <i class="icon-plus"></i>
                                                                    </span>
                                                                </a>
                                                                <a href="#" data-field-qty="qty" class="btn btn-default occupancy_quantity_down">
                                                                    <span>
                                                                        <i class="icon-minus"></i>
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <p class="label-desc-txt">({l s='Below' mod='wkroomsearchblock'}  {$max_child_age|escape:'htmlall':'UTF-8'} {l s='years' mod='wkroomsearchblock'})</p>
                                                    </div>
                                                </div>
                                                <p style="display:none;"><span class="text-danger occupancy-input-errors"></span></p>
                                                <div class="row">
                                                    <div class="form-group children_age_info_block col-sm-12">
                                                        <label>{l s='All Children' mod='wkroomsearchblock'}</label>
                                                        <div class="children_ages">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr class="occupancy-info-separator">
                                        </div>
                                    {/if}
                                </div>
                                <div class="occupancy_block_actions">
                                    <span id="add_new_occupancy">
                                        <a class="add_new_occupancy_btn" href="#"><i class="icon-plus"></i> <span>{l s='Add Room' mod='wkroomsearchblock'}</span></a>
                                    </span>
                                    <span>
                                        <button class="submit_occupancy_btn btn btn-primary">{l s='Done' mod='wkroomsearchblock'}</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
            {/block}
            {block name='search_form_submit'}
                <div class="form-group grid-item search_room_submit_block area-4" style="grid-column: span 4;">
                    <button type="submit" class="btn btn btn-primary" name="search_room_submit" id="search_room_submit">
                        <span>{l s='Search Rooms' mod='wkroomsearchblock'}</span>
                    </button>
                </div>
            {/block}
        </div>
    {/block}
</form>
