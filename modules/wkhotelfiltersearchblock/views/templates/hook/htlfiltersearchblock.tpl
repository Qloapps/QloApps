<!-- {$booking_data.stats.num_avail} -->
<!-- {$booking_data.stats.total_rooms} -->
<div class="row margin-lr-0 block" id="filter_search_block">
    <div class="filter_header">
        <div class="col-sm-12">
            <p>{l s='Search Rooms' mod='wkhotelfiltersearchblock'}</p>
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
                    <input class="form-control" placeholder="Enter a city, state, country name" type="text" id="hotel_location" name="hotel_location" autocomplete="off" {if isset($search_data)}value="{$search_data['parent_data']['name']}" city_cat_id="{$search_data['parent_data']['id_category']}"{/if}/>
                    <ul class="location_search_results_ul"></ul>
                </div>
            {/if}
            <div class="form-group htl_nm_cont">
                <label class="control-label" for="">{l s='Hotel Name' mod='wkroomsearchblock'}</label>
                <div class="dropdown">
                    <button class="btn btn-default hotel_cat_id_btn dropdown-toggle" type="button" data-toggle="dropdown">
                        {if isset($search_data)}
                            <span id="hotel_cat_name" class="pull-left">{$search_data['htl_dtl']['hotel_name']}</span>
                        {else}
                            <span id="hotel_cat_name" class="pull-left">{l s='Select Hotel'}</span>
                        {/if}
                        <input type="hidden" id="hotel_cat_id" name="hotel_cat_id" {if isset($search_data)}value="{$search_data['htl_dtl']['id_category']}"{/if}>
                        <div class="caret_div">
                            <span class="caret"></span>
                        </div>
                    </button>
                    <ul class="dropdown-menu hotel_dropdown_ul">
                        {if isset($all_hotels_info) && $all_hotels_info}
                            {foreach from=$all_hotels_info key=htl_k item=htl_v}
                                <li class="hotel_name" data-hotel-cat-id="{$htl_v['id_category']}">
                                    {$htl_v['hotel_name']}
                                </li>
                            {/foreach}
                        {/if} 
                    </ul>
                </div>
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
                <button type="submit" name="filter_search_btn" class="btn filter_search_btn col-sm-12" id="filter_search_btn">
                    <span>{l s='Search' mod='wkhotelfiltersearchblock'}</span>
                </button>
            </div>
        </form>
    </div>
</div>
{strip}
    {addJsDefL name=hotel_name_cond}{l s='Please select a hotel name' js=1 mod='wkroomsearchblock'}{/addJsDefL}
    {addJsDefL name=check_in_time_cond}{l s='Please enter Check In time' js=1 mod='wkroomsearchblock'}{/addJsDefL}
    {addJsDefL name=check_out_time_cond}{l s='Please enter Check Out time' js=1 mod='wkroomsearchblock'}{/addJsDefL}
{/strip}