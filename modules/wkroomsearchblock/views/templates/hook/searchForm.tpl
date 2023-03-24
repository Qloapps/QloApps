<form method="POST" id="search_hotel_block_form" class="grid">
    {if isset($location_enabled) && $location_enabled}
        <div class="form-group area-{$column_widths['location']}">
            <input type="text" class="form-control header-rmsearch-input input-location" id="hotel_location" name="hotel_location" autocomplete="off" placeholder="{l s='Hotel location' mod='wkroomsearchblock'}" {if isset($search_data)}value="{$search_data['location']|escape:'htmlall':'UTF-8'}"{/if}>
            <input hidden="hidden" name="location_category_id" id="location_category_id" {if isset($search_data)}value="{$search_data['location_category_id']|escape:'htmlall':'UTF-8'}"{/if}>
            <div class="dropdown">
                <ul class="location_search_results_ul dropdown-menu"></ul>
            </div>
        </div>
    {/if}
    {if count($hotels_info) <= 1 && !$show_hotel_name}
        <input type="hidden" id="max_order_date" name="max_order_date" value="{if isset($hotels_info[0]['max_order_date'])}{$hotels_info[0]['max_order_date']|escape:'htmlall':'UTF-8'}{/if}">
        <input type="hidden" id="preparation_time" name="preparation_time" value="{if isset($hotels_info[0]['preparation_time'])}{$hotels_info[0]['preparation_time']|escape:'htmlall':'UTF-8'}{/if}">
        <input type="hidden" id="hotel_cat_id" name="hotel_cat_id" value="{$hotels_info[0]['id_category']}">
        <input type="hidden" id="id_hotel" name="id_hotel" value="{$hotels_info[0]['id']|escape:'htmlall':'UTF-8'}">
        <input type="hidden" id="htl_name" class="form-control header-rmsearch-input" value="{$hotels_info[0]['hotel_name']}" readonly>
    {else}
        <div class="form-group area-{$column_widths['hotel']}">
            {if isset($hotels_info) && count($hotels_info)}
                <div class="dropdown">
                    <input type="hidden" id="hotel_cat_id" name="hotel_cat_id" {if isset($search_data)}value="{$search_data['htl_dtl']['id_category']|escape:'htmlall':'UTF-8'}"{/if}>
                    <input type="hidden" id="id_hotel" name="id_hotel" {if isset($search_data)}value="{$search_data['htl_dtl']['id']|escape:'htmlall':'UTF-8'}"{/if}>
                    <input type="hidden" id="max_order_date" name="max_order_date" value="{if isset($max_order_date)}{$max_order_date|escape:'htmlall':'UTF-8'}{/if}">
                    <input type="hidden" id="preparation_time" name="preparation_time" value="{if isset($preparation_time)}{$preparation_time|escape:'htmlall':'UTF-8'}{/if}">

                    <button class="form-control header-rmsearch-input input-hotel {if isset($error) && $error == 1}error_border{/if}" type="button" data-toggle="dropdown" id="id_hotel_button">
                        {if isset($search_data)}
                        <span id="hotel_cat_name" class="pull-left">{$search_data['htl_dtl']['hotel_name']|escape:'htmlall':'UTF-8'}</span>
                    {else}
                        <span id="hotel_cat_name" class="pull-left">{l s='Select Hotel' mod='wkroomsearchblock'}</span>
                    {/if}
                    </button>
                    <ul class="dropdown-menu hotel_dropdown_ul">
                        {if isset($hotels_info) && $hotels_info}
                            {foreach $hotels_info as $name_val}
                                <li tabindex="-1" class="search_result_li" data-id-hotel="{$name_val['id']|escape:'htmlall':'UTF-8'}" data-hotel-cat-id="{$name_val['id_category']|escape:'htmlall':'UTF-8'}" data-max_order_date="{$name_val['max_order_date']}" data-preparation_time="{$name_val['preparation_time']|escape:'htmlall':'UTF-8'}">{$name_val['hotel_name']|escape:'htmlall':'UTF-8'}</li>
                            {/foreach}
                        {/if}
                    </ul>
                </div>
            {/if}
        </div>
    {/if}
    {if isset($multiple_dates_input) && $multiple_dates_input}
        <div class="grid area-{$column_widths['date']}" id="daterange_value">
            <div class="form-group">
                <input type="hidden" id="check_in_time" name="check_in_time" {if isset($search_data)}value="{$search_data['date_from']|escape:'htmlall':'UTF-8'}"{/if}>
                <div class="form-control header-rmsearch-input input-date" autocomplete="off" id="daterange_value_from" placeholder="{l s='Check-in' mod='wkroomsearchblock'}"><span>{l s='Check-in' mod='wkroomsearchblock'}</span></div>
            </div>
            <div class="form-group">
                <input type="hidden" id="check_out_time" name="check_out_time" {if isset($search_data)}value="{$search_data['date_to']|escape:'htmlall':'UTF-8'}"{/if}>
                <div class="form-control header-rmsearch-input input-date" autocomplete="off" id="daterange_value_to" placeholder="{l s='Check-out' mod='wkroomsearchblock'}"><span>{l s='Check-out' mod='wkroomsearchblock'}</span></div>
            </div>
        </div>
    {else}
        <div class="form-group area-{$column_widths['date']}">
            <input type="hidden" id="check_in_time" name="check_in_time" {if isset($search_data)}value="{$search_data['date_from']|escape:'htmlall':'UTF-8'}"{/if}>
            <input type="hidden" id="check_out_time" name="check_out_time" {if isset($search_data)}value="{$search_data['date_to']|escape:'htmlall':'UTF-8'}"{/if}>
            <div class="form-control header-rmsearch-input input-date" id="daterange_value"  autocomplete="off" placeholder="{l s='Check-in - Check-out' mod='wkroomsearchblock'}"><span>{l s='Check-in' mod='wkroomsearchblock'} &nbsp;<i class="icon icon-minus"></i>&nbsp; {l s='Check-out' mod='wkroomsearchblock'}</span></div>
        </div>
    {/if}

    {if isset($is_occupancy_wise_search) && $is_occupancy_wise_search}
        <div class="form-group area-{$column_widths['occupancy']}">
            <div class="dropdown">
                <button class="form-control input-occupancy header-rmsearch-input {if isset($error) && $error == 1}error_border{/if}" type="button" data-toggle="dropdown" id="guest_occupancy">
                    <span class="pull-left">{if (isset($search_data['occupancy_adults']) && $search_data['occupancy_adults'])}{$search_data['occupancy_adults']} {if $search_data['occupancy_adults'] > 1}{l s='Adults' mod='wkroomsearchblock'}{else}{l s='Adult' mod='wkroomsearchblock'}{/if}, {if isset($search_data['occupancy_children']) && $search_data['occupancy_children']}{$search_data['occupancy_children']} {if $search_data['occupancy_children'] > 1}
                    {l s='Children' mod='wkroomsearchblock'}{else}{l s='Child' mod='wkroomsearchblock'}{/if}, {/if}{$search_data['occupancies']|count} {if $search_data['occupancies']|count > 1}{l s='Rooms' mod='wkroomsearchblock'}{else}{l s='Room'}{/if}{else}{l s='1 Adult, 1 Room' mod='wkroomsearchblock'}{/if}</span>
                </button>
                <div id="search_occupancy_wrapper" class="dropdown-menu">
                    <div id="occupancy_inner_wrapper">
                        {if isset($search_data['occupancies']) && $search_data['occupancies']}
                            {assign var=countRoom value=1}
                            {foreach from=$search_data['occupancies'] key=key item=$occupancy name=occupancyInfo}
                                <div class="occupancy-room-block">
                                    <div class="occupancy_info_head"><span class="room_num_wrapper">{l s='Room' mod='wkroomsearchblock'} - {$countRoom|escape:'htmlall':'UTF-8'} </span>{if !$smarty.foreach.occupancyInfo.first}<a class="remove-room-link pull-right" href="#">{l s='Remove' mod='wkroomsearchblock'}</a>{/if}</div>
                                    <div class="occupancy_info_block" occ_block_index="{$key|escape:'htmlall':'UTF-8'}">
                                        <div class="form-group occupancy_count_block">
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
                                        <div class="form-group occupancy_count_block">
                                            <label>{l s='Child' mod='wkroomsearchblock'}<span class="label-desc-txt"> ({l s='Below' mod='wkroomsearchblock'} {$max_child_age|escape:'htmlall':'UTF-8'} {l s='years' mod='wkroomsearchblock'})</span></label>
                                            <div>
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
                                        </div>
                                        <div class="form-group children_age_info_block" {if isset($occupancy['child_ages']) && $occupancy['child_ages']}style="display:block;"{/if}>
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
                                    <hr class="occupancy-info-separator">
                                </div>
                                {assign var=countRoom value=$countRoom+1}
                            {/foreach}
                        {else}
                            <div class="occupancy-room-block">
                                <div class="occupancy_info_head"><span class="room_num_wrapper">{l s='Room - 1' mod='wkroomsearchblock'}</span></div>
                                <div class="occupancy_info_block" occ_block_index="0">
                                    <div class="form-group occupancy_count_block">
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
                                    <div class="form-group occupancy_count_block">
                                        <label>{l s='Child' mod='wkroomsearchblock'} <span class="label-desc-txt">({l s='Below' mod='wkroomsearchblock'}  {$max_child_age|escape:'htmlall':'UTF-8'} {l s='years' mod='wkroomsearchblock'})</span></label>
                                        <div>
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
                                    </div>
                                    <div class="form-group children_age_info_block">
                                        <label>{l s='All Children' mod='wkroomsearchblock'}</label>
                                        <div class="children_ages">
                                        </div>
                                    </div>
                                </div>
                                <hr class="occupancy-info-separator">
                            </div>
                        {/if}
                    </div>
                    <div id="add_new_occupancy">
                        <a class="add_new_occupancy_btn" href="#"><i class="icon-plus"></i> <span>{l s='Add Room' mod='wkroomsearchblock'}</span></a>
                    </div>
                </div>
            </div>
        </div>
    {/if}
    <div class="form-group search_room_submit_block area-{$column_widths['search']}">
        <button type="submit" class="btn btn btn-primary pull-right" name="search_room_submit" id="search_room_submit">
            <span>{l s='Search Rooms' mod='wkroomsearchblock'}</span>
        </button>
    </div>
</form>