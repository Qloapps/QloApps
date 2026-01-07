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

{function name="renderOccupancyBlock" key=0 occupancy=null countRoom=1 showRemove=false max_child_age=0 hide_children=false}
    <div class="occupancy_info_block mb-3" occ_block_index="{$key|escape:'htmlall':'UTF-8'}">
        <div class="occupancy_info_head font-weight-bold mb-2">
            <span class="room_num_wrapper">
                {l s='Room'} - {$countRoom|escape:'htmlall':'UTF-8'}
            </span>
            {if $showRemove}
                <a class="remove-room-link float-right text-danger" href="#">
                    {l s='Remove'}
                </a>
            {/if}
        </div>
        <div class="row">
            {* Adults *}
            <div class="form-group col-sm-5 col-6 occupancy_count_block">
                <label>{l s='Adults'}</label>
                <div class="d-flex">
                    <input type="hidden" class="num_occupancy num_adults room_occupancies" name="occupancy[{$key}][adults]" value="{if $occupancy}{$occupancy.adults}{else}1{/if}">
                    <div class="occupancy_count border p-small rounded float-left mr-1 max-width-7">
                        <span class="min-width-3 d-block text-center">{if $occupancy}{$occupancy.adults}{else}1{/if}</span>
                    </div>

                    <div class="qty_direction d-flex flex-column gap-1">
                        <a href="#" class="p-0 border rounded occupancy_quantity_up"><i class="icon-plus"></i></a>
                        <a href="#" class="p-0 border rounded occupancy_quantity_down"><i class="icon-minus"></i></a>
                    </div>
                </div>
            </div>

            {* Children *}
            <div class="form-group col-sm-7 col-6 occupancy_count_block {if $hide_children}hide{/if}">
                <label class="d-block">{l s='Children'}</label>

                <div class="d-flex clearfix">
                    <input type="hidden" class="num_occupancy num_children room_occupancies" name="occupancy[{$key}][children]" value="{if $occupancy}{$occupancy.children}{else}0{/if}">
                    <div class="occupancy_count border p-small rounded float-left mr-1 max-width-7">
                        <span class="min-width-3 d-block text-center">{if $occupancy}{$occupancy.children}{else}0{/if}</span>
                    </div>

                    <div class="qty_direction d-flex flex-column gap-1">
                        <a href="#" class="p-0 border rounded occupancy_quantity_up"><i class="icon-plus"></i></a>
                        <a href="#" class="p-0 border rounded occupancy_quantity_down"><i class="icon-minus"></i></a>
                    </div>
                </div>
                <div class="label-desc-txt text-muted small mt-1">
                    ({l s='Below'} {$max_child_age} {l s='years'})
                </div>
            </div>
        </div>

        <p class="mb-0" style="display:none;">
            <span class="text-danger occupancy-input-errors"></span>
        </p>

        {* Children Ages *}
        <div class="form-group row children_age_info_block mt-2" style="display:{if !empty($occupancy.child_ages)}block{else}none{/if}">
            <label class="col-sm-12 small text-muted">
                {l s='All Children'}
            </label>
            <div class="col-sm-12">
                <div class="children_ages d-flex flex-wrap gap-2">
                    {if !empty($occupancy.child_ages)}
                        {foreach $occupancy.child_ages as $childAge}
                            <div>
                                <select class="guest_child_age room_occupancies custom-select custom-select-sm" name="occupancy[{$key}][child_ages][]">
                                    <option value="-1" {if $childAge == -1}selected{/if}>
                                        {l s='Select 1'}
                                    </option>
                                    <option value="0" {if $childAge == 0}selected{/if}>
                                        {l s='Under 1'}
                                    </option>
                                    {for $age=1 to ($max_child_age-1)}
                                        <option value="{$age}" {if $childAge == $age}selected{/if}>
                                            {$age}
                                        </option>
                                    {/for}
                                </select>
                            </div>
                        {/foreach}
                    {/if}
                </div>
            </div>
        </div>
        <hr class="occupancy-info-separator">
    </div>
{/function}

<div class="dropdown">
    {block name='occupancy_field_button'}
        <button class="form-control booking_guest_occupancy input-occupancy {if isset($error) && $error == 1} error_border{/if}" data-display="static" type="button">
            <span class=" align-self-center">
                {if isset($occupancies) && $occupancies}
                    {if (isset($occupancy_adults) && $occupancy_adults)}{$occupancy_adults} {if $occupancy_adults > 1}{l s='Adults'}{else}{l s='Adult'}{/if}, {if isset($occupancy_children) && $occupancy_children}{$occupancy_children} {if $occupancy_children > 1} {l s='Children'}{else}{l s='Child'}{/if}, {/if}{$occupancies|count} {if $occupancies|count > 1}{l s='Rooms'}{else}{l s='Room'}{/if}{else}{l s='1 Adult, 1 Room'}{/if}
                {else}
                    {l s='Select Occupancy'}
                {/if}
            </span>
        </button>
    {/block}

    {block name='occupancy_field_content'}
        <div class="dropdown-menu booking_occupancy_wrapper  occupancy-wrapper dropdown-menu-right">
            <input type="hidden" class="max_avail_type_qty" value="{if isset($total_available_rooms)}{$total_available_rooms|escape:'html':'UTF-8'}{/if}">
            <input type="hidden" class="max_adults" value="{$room_type_info['max_adults']|escape:'html':'UTF-8'}">
            <input type="hidden" class="max_children" value="{$room_type_info['max_children']|escape:'html':'UTF-8'}">
            <input type="hidden" class="max_guests" value="{$room_type_info['max_guests']|escape:'html':'UTF-8'}">
            <input type="hidden" class="base_adult" value="{$room_type_info['adults']|escape:'html':'UTF-8'}">
            <input type="hidden" class="base_children" value="{$room_type_info['children']|escape:'html':'UTF-8'}">
            <div class="booking_occupancy_inner">
                {if isset($occupancies) && $occupancies}
                    {assign var=countRoom value=1}
                    {foreach from=$occupancies key=key item=$occupancy name=occupancyInfo}
                        {renderOccupancyBlock key=$key occupancy=$occupancy countRoom=$countRoom showRemove=!$smarty.foreach.occupancyInfo.first max_child_age=$max_child_age hide_children=!$room_type_info.max_children}
                        {assign var=countRoom value=$countRoom+1}
                    {/foreach}
                {else}
                    {renderOccupancyBlock key=0 occupancy=null countRoom=1 showRemove=false max_child_age=$max_child_age hide_children=true}
                {/if}
            </div>
            {block name='occupancy_field_actions'}
                <div class="occupancy_block_actions d-flex justify-content-between align-items-center">
                    <span class="add_occupancy_block font-weight-bold" >
                        <a class="add_new_occupancy_btn text-primary text-decoration-none {if isset($occupancies) && $occupancies && isset($total_available_rooms) && $total_available_rooms <= count($occupancies)} disabled{/if}" data-title-available="{l s='Click to add more rooms.'}" data-title-unavailable="{l s='No more rooms available.'}" href="#">
                            <i class="icon-plus"></i> <span>{l s='Add Room'}</span>
                        </a>
                    </span>
                    <span>
                        <button type="submit" class="submit_occupancy_btn btn btn-primary">{l s='Done'}</button>
                    </span>
                </div>
            {/block}
        </div>
    {/block}
</div>
{if !isset($show_occupancy_info) || $show_occupancy_info == true}
    <div class="help_block">{$room_type_info['max_guests']|escape:'htmlall':'UTF-8'} {l s='Max guests'}: {$room_type_info['max_adults']} {l s='Adults'}, {$room_type_info['max_children']} {if $room_type_info['max_children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}</div>
{/if}
