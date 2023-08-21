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

<div class="form-group dropdown">
    <button class="form-control booking_guest_occupancy input-occupancy{if isset($error) && $error == 1} error_border{/if}" type="button">
        <span class="">
            {if isset($occupancies) && $occupancies}
                {if (isset($occupancy_adults) && $occupancy_adults)}{$occupancy_adults} {if $occupancy_adults > 1}{l s='Adults'}{else}{l s='Adult'}{/if}, {if isset($occupancy_children) && $occupancy_children}{$occupancy_children} {if $occupancy_children > 1} {l s='Children'}{else}{l s='Child'}{/if}, {/if}{$occupancies|count} {if $occupancies|count > 1}{l s='Rooms'}{else}{l s='Room'}{/if}{else}{l s='1 Adult, 1 Room'}{/if}
            {else}
                {l s='Select Occupancy'}
            {/if}
        </span>
    </button>

    <div class="dropdown-menu booking_occupancy_wrapper">
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
                    <div class="occupancy_info_block selected" occ_block_index="{$key|escape:'htmlall':'UTF-8'}">
                        <div class="occupancy_info_head"><span class="room_num_wrapper">{l s='Room'} - {$countRoom|escape:'htmlall':'UTF-8'} </span>{if !$smarty.foreach.occupancyInfo.first}<a class="remove-room-link pull-right" href="#">{l s='Remove'}</a>{/if}</div>
                        <div class="row">
                            <div class="form-group col-sm-5 col-xs-6 occupancy_count_block">
                                <div class="row">
                                    <label class="col-sm-12">{l s='Adults'}</label>
                                    <div class="col-sm-12">
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
                            </div>
                            <div class="form-group col-sm-7 col-xs-6 occupancy_count_block">
                                <div class="row">
                                    <label class="col-sm-12">{l s='Children'}</label>
                                    <div class="col-sm-12 clearfix">
                                        <input type="hidden" class="num_occupancy num_children room_occupancies" name="occupancy[{$key|escape:'htmlall':'UTF-8'}][children]" max="{}" value="{$occupancy['children']|escape:'htmlall':'UTF-8'}">
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
                                    <div class="col-sm-12">
                                        <span class="label-desc-txt">({l s='Below'}  {$max_child_age|escape:'htmlall':'UTF-8'} {l s='years'})</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row children_age_info_block" {if isset($occupancy['child_ages']) && $occupancy['child_ages']}style="display:block;"{/if}>
                            <label class="col-sm-12">{l s='All Children'}</label>
                            <div class="col-sm-12">
                                <div class="children_ages">
                                    {if isset($occupancy['child_ages']) && $occupancy['child_ages']}
                                        {foreach $occupancy['child_ages'] as $childAge}
                                            <div>
                                                <select class="guest_child_age room_occupancies" name="occupancy[{$key|escape:'htmlall':'UTF-8'}][child_ages][]">
                                                    <option value="-1" {if $childAge == -1}selected{/if}>{l s='Select 1'}</option>
                                                    <option value="0" {if $childAge == 0}selected{/if}>{l s='Under 1'}</option>
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
                        <hr class="occupancy-info-separator">
                    </div>
                    {assign var=countRoom value=$countRoom+1}
                {/foreach}
            {else}
                <div class="occupancy_info_block" occ_block_index="0">
                    <div class="occupancy_info_head"><span class="room_num_wrapper">{l s='Room - 1'}</span></div>
                    <div class="row">
                        <div class="form-group col-sm-5 col-xs-6 occupancy_count_block">
                            <div class="row">
                                <label class="col-sm-12">{l s='Adults'}</label>
                                <div class="col-sm-12">
                                    <input type="hidden" class="num_occupancy num_adults" name="num_adults[]" value="{$room_type_info['adults']}">
                                    <div class="occupancy_count pull-left">
                                        <span>{$room_type_info['adults']}</span>
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
                        </div>
                        <div class="form-group col-sm-7 col-xs-6 occupancy_count_block">
                            <div class="row">
                                <label class="col-sm-12">{l s='Children'}</label>
                                <div class="col-sm-12 clearfix">
                                    <input type="hidden" class="num_occupancy num_children" name="num_children[]" value="0">
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
                                <div class="col-sm-12">
                                    <span class="label-desc-txt">({l s='Below'}  {$max_child_age|escape:'htmlall':'UTF-8'} {l s='years'})</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row children_age_info_block">
                        <label class="col-sm-12">{l s='All Children'}</label>
                        <div class="col-sm-12">
                            <div class="children_ages">
                            </div>
                        </div>
                    </div>
                    <hr class="occupancy-info-separator">
                </div>
            {/if}
        </div>
        <div class="add_occupancy_block">
            <a class="add_new_occupancy_btn" href="#"><i class="icon-plus"></i> <span>{l s='Add Room'}</span></a>
        </div>
    </div>
</div>