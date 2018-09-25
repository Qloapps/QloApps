{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="row margin-lr-0 block" id="filter_search_block">
    <div class="filter_header">
        <div class="col-sm-12">
            <p>{l s='Search Rooms' mod='wkhotelfiltersearchblock'}</p>
            <hr class="theme-text-underline">
        </div>
    </div>
    <div class="col-sm-12 category_page_search_block clear-both">
        <form method="POST" autocomplete="on" autofill="on">

            {if isset($error) && ($error == 1)}
                <p class="error_msg"><i class="icon-times-circle-o"></i>&nbsp;&nbsp;{l s='All Fields are mandatory.' mod='wkroomsearchblock'}</p>
            {/if}
            {if isset($location_enable) && $location_enable}
                <div class="form-group hotel_location_div">
                    <label class="control-label" for="">{l s='Hotel Location' mod='wkroomsearchblock'}</label>
                    <input class="form-control" placeholder="Enter a city, state, country name" type="text" id="hotel_location" name="hotel_location" autocomplete="off" {if isset($search_data)}value="{$search_data['location']}" city_cat_id="{$search_data['parent_data']['id_category']}"{/if}/>
                    <div class="dropdown">
                        <ul class="location_search_results_ul"></ul>
                    </div>
                </div>
            {/if}

            <div class="form-group htl_nm_cont {if $totalActiveHotels <= 1 && !$show_only_active_htl}hidden{/if}">
                <label class="control-label" for="">{l s='Hotel Name' mod='wkroomsearchblock'}</label>
                {if isset($all_hotels_info) && $totalActiveHotels}
                    <div class="dropdown">
                        <button class="btn btn-default hotel_cat_id_btn dropdown-toggle" type="button" data-toggle="dropdown">
                            {if isset($search_data)}
                                <span id="hotel_cat_name" class="pull-left">{$search_data['htl_dtl']['hotel_name']}</span>
                            {else}
                                <span id="hotel_cat_name" class="pull-left">{l s='Select Hotel'}</span>
                            {/if}
                            <input type="hidden" id="hotel_cat_id" name="hotel_cat_id" {if isset($search_data)}value="{$search_data['htl_dtl']['id_category']}"{/if}>
                            <input type="hidden" id="id_hotel" name="id_hotel" {if isset($search_data)}value="{$search_data['htl_dtl']['id']}"{/if}>
                            <input type="hidden" id="max_order_date" name="max_order_date" value="{if isset($max_order_date)}{$max_order_date}{/if}">
                            <span class="arrow_span">
                                <i class="icon icon-angle-down"></i>
                            </span>
                        </button>
                        <ul class="dropdown-menu hotel_dropdown_ul">
                            {if isset($all_hotels_info) && $all_hotels_info}
                                {foreach from=$all_hotels_info key=htl_k item=htl_v}
                                    <li class="hotel_name" data-id-hotel="{$htl_v['id']}" data-hotel-cat-id="{$htl_v['id_category']}" data-max_order_date="{$htl_v['max_order_date']}">
                                        {$htl_v['hotel_name']}
                                    </li>
                                {/foreach}
                            {/if}
                        </ul>
                    </div>
                {/if}
                <p class="error_msg" id="select_htl_error_p"></p>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <label class="control-label" for="check_in_time">{l s='Check In Time' mod='wkroomsearchblock'}</label>
                        <div class="input-group">
                            <input class="form-control" type="text" id="check_in_time" name="check_in_time" {if isset($search_data)}value="{$search_data['date_from']}"{/if}/>
                            <label class="input-group-addon" for="check_in_time"><i class="icon-calendar"></i></label>
                        </div>
                        <p class="error_msg" id="check_in_time_error_p"></p>
                    </div>
                    <div class="col-xs-12 col-sm-12 margin-top-10">
                        <label class="control-label" for="check_out_time">{l s='Check Out Time' mod='wkroomsearchblock'}</label>
                        <div class="input-group">
                            <input class="form-control" type="text" id="check_out_time" name="check_out_time" {if isset($search_data)}value="{$search_data['date_to']}"{/if} />
                            <label class="input-group-addon" for="check_out_time"><i class="icon-calendar"></i></label>
                        </div>
                        <p class="error_msg" id="check_out_time_error_p"></p>
                    </div>
                </div>
            </div>
            <div>
                <button type="submit" name="filter_search_btn" class="btn btn-default button button-medium exclusive" id="filter_search_btn">
                    <span>{l s='Search' mod='wkhotelfiltersearchblock'}</span>
                </button>
            </div>
        </form>
    </div>
</div>
{strip}
    {addJsDefL name=no_results_found_cond}{l s='No results found for this search' js=1 mod='wkroomsearchblock'}{/addJsDefL}
    {addJsDefL name=hotel_loc_cond}{l s='Please enter a hotel location' js=1 mod='wkroomsearchblock'}{/addJsDefL}
    {addJsDefL name=hotel_name_cond}{l s='Please select a hotel name' js=1 mod='wkroomsearchblock'}{/addJsDefL}
    {addJsDefL name=check_in_time_cond}{l s='Please enter Check In time' js=1 mod='wkroomsearchblock'}{/addJsDefL}
    {addJsDefL name=check_out_time_cond}{l s='Please enter Check Out time' js=1 mod='wkroomsearchblock'}{/addJsDefL}
    {addJsDefL name=num_adults_cond}{l s='Please enter number of adults.' js=1 mod='wkroomsearchblock'}{/addJsDefL}
    {addJsDefL name=num_children_cond}{l s='Please enter number of children.' js=1 mod='wkroomsearchblock'}{/addJsDefL}
    {addJsDefL name=some_error_occur_cond}{l s='Some error occured. Please try again.' js=1 mod='wkroomsearchblock'}{/addJsDefL}
    {addJsDefL name=less_checkin_date}{l s='Check In date can not be before current date.' js=1 mod='wkroomsearchblock'}{/addJsDefL}
    {addJsDefL name=more_checkout_date}{l s='Check Out date must be greater than Check In date.' js=1 mod='wkroomsearchblock'}{/addJsDefL}
    {addJsDef autocomplete_search_url=$link->getModuleLink('wkroomsearchblock','autocompletesearch')}
    {addJsDefL name=hotel_name_cond}{l s='Please select a hotel name' js=1 mod='wkroomsearchblock'}{/addJsDefL}
    {addJsDefL name=check_in_time_cond}{l s='Please enter Check In time' js=1 mod='wkroomsearchblock'}{/addJsDefL}
    {addJsDefL name=check_out_time_cond}{l s='Please enter Check Out time' js=1 mod='wkroomsearchblock'}{/addJsDefL}
    {addJsDef max_order_date=$max_order_date}
    {addJsDef booking_date_to=$booking_date_to}
{/strip}