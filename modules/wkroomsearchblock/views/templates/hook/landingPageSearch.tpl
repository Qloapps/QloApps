{*
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($is_index_page) && $is_index_page}
    <div class="header-rmsearch-container header-rmsearch-hide-xs hidden-xs">
        {if isset($hotels_info) && count($hotels_info)}
            <div class="header-rmsearch-wrapper" id="xs_room_search_form">
                <div class="header-rmsearch-primary">
                    <div class="fancy_search_header_xs">
                        <p>{l s='Search Rooms' mod='wkroomsearchblock'}</p>
                        <hr>
                    </div>
                    <div class="container">
                        <div class="row header-rmsearch-inner-wrapper">
                            <form method="POST" id="search_hotel_block_form">
                                {if isset($location_enabled) && $location_enabled}
                                    <div class="form-group
                                    {if count($hotels_info) <= 1 && !$show_hotel_name}
                                        col-sm-3
                                    {else}
                                        col-sm-6 col-lg-3
                                    {/if}">
                                        <input type="text" class="form-control header-rmsearch-input" id="hotel_location" name="hotel_location" autocomplete="off" placeholder="{l s='Hotel Location' mod='wkroomsearchblock'}">
                                        <div class="dropdown">
                                            <ul class="location_search_results_ul"></ul>
                                        </div>
                                    </div>
                                {/if}
                                <div class="form-group {if count($hotels_info) <= 1 && !$show_hotel_name} hidden {/if}
                                {if isset($location_enabled) && $location_enabled}
                                    col-sm-6 col-lg-3
                                {else}
                                    col-sm-3
                                {/if}">
                                    <input type="hidden" name="is_hotel_rooms_search" value="1">
                                    {if !$show_hotel_name}
                                        <input type="hidden" id="max_order_date" name="max_order_date" value="{if isset($hotels_info[0]['max_order_date'])}{$hotels_info[0]['max_order_date']|escape:'htmlall':'UTF-8'}{/if}">
                                        <input type="hidden" id="hotel_cat_id" name="hotel_cat_id" value="{$hotels_info[0]['id_category']}">
                                        <input type="hidden" id="id_hotel" name="id_hotel" value="{$hotels_info[0]['id']|escape:'htmlall':'UTF-8'}">
                                        <input type="text" id="htl_name" class="form-control header-rmsearch-input" value="{$hotels_info[0]['hotel_name']}" readonly>
                                    {else}
                                        {if isset($hotels_info) && count($hotels_info)}
                                            <input type="hidden" id="max_order_date" name="max_order_date" value="{if isset($max_order_date)}{$max_order_date|escape:'htmlall':'UTF-8'}{/if}">
                                            <div class="dropdown">
                                                <input type="hidden" id="hotel_cat_id" name="hotel_cat_id">
                                                <input type="hidden" id="id_hotel" name="id_hotel">
                                                <button class="form-control header-rmsearch-input {if isset($error) && $error == 1}error_border{/if}" type="button" data-toggle="dropdown" id="id_hotel_button">
                                                    <span id="hotel_cat_name" class="pull-left">{l s='Select Hotel' mod='wkroomsearchblock'}</span>
                                                </button>
                                                <ul class="dropdown-menu hotel_dropdown_ul">
                                                    {if isset($hotels_info) && $hotels_info}
                                                        {foreach $hotels_info as $name_val}
                                                            <li class="search_result_li" data-id-hotel="{$name_val['id']|escape:'htmlall':'UTF-8'}" data-hotel-cat-id="{$name_val['id_category']|escape:'htmlall':'UTF-8'}" data-max_order_date="{$name_val['max_order_date']|escape:'htmlall':'UTF-8'}">{$name_val['hotel_name']|escape:'htmlall':'UTF-8'}</li>
                                                        {/foreach}
                                                    {/if}
                                                </ul>
                                            </div>
                                        {/if}
                                    {/if}
                                </div>
                                <div class="form-group
                                {if count($hotels_info) <= 1}
                                    {if isset($location_enabled) && $location_enabled && $show_hotel_name}
                                        col-sm-4 col-lg-2
                                    {elseif isset($location_enabled) && !$location_enabled && !$show_hotel_name}
                                        col-sm-4
                                    {else}
                                        col-sm-3
                                    {/if}
                                {elseif isset($location_enabled) && $location_enabled}
                                    col-sm-4 col-lg-2
                                {else}
                                    col-sm-3
                                {/if}">
                                    <input type="text" class="form-control header-rmsearch-input input-date" id="check_in_time" name="check_in_time" autocomplete="off" placeholder="{l s='Check In Date' mod='wkroomsearchblock'}">
                                </div>
                                <div class="form-group
                                {if count($hotels_info) <= 1}
                                    {if isset($location_enabled) && $location_enabled && $show_hotel_name}
                                        col-sm-4 col-lg-2
                                    {elseif isset($location_enabled) && !$location_enabled && !$show_hotel_name}
                                        col-sm-4
                                    {else}
                                        col-sm-3
                                    {/if}
                                {elseif isset($location_enabled) && $location_enabled}
                                    col-sm-4 col-lg-2
                                {else}
                                    col-sm-3
                                {/if}">
                                    <input type="text" class="form-control header-rmsearch-input input-date" id="check_out_time" name="check_out_time" autocomplete="off" placeholder="{l s='Check Out Date' mod='wkroomsearchblock'}">
                                </div>
                                <div class="form-group
                                {if count($hotels_info) <= 1}
                                    {if isset($location_enabled) && $location_enabled && $show_hotel_name}
                                        col-sm-4 col-lg-2
                                    {elseif isset($location_enabled) && !$location_enabled && !$show_hotel_name}
                                        col-sm-4
                                    {else}
                                        col-sm-3
                                    {/if}
                                {elseif isset($location_enabled) && $location_enabled}
                                    col-sm-4 col-lg-2
                                {else}
                                    col-sm-3
                                {/if}">
                                    <button type="submit" class="btn btn-default button button-medium exclusive" name="search_room_submit" id="search_room_submit">
                                        <span>{l s='Search Now' mod='wkroomsearchblock'}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    </div>
{/if}