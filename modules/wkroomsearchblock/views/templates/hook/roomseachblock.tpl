{if isset($is_index_page) && $is_index_page}
    <div class="row search_block_container">
        <div class="hidden-xs col-sm-7">
            <div class="outer_div">
                <div class="inner_div">
                    <div class="block_heading">
                        {$header_block_title}
                    </div>
                    <div class="block_description">
                        {$header_block_content}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-5">
            <div class="search_block">
                <div class="row search-header">
                    <div class="search-heading">
                        <i class='icon-search'></i>&nbsp&nbsp{l s='Search Rooms' mod='wkroomsearchblock'}
                    </div>
                </div>
                <hr style="border:1px solid #bf9958;margin-top:10px;">
                <form method="POST" id="search_hotel_block_form">
                    {if isset($location_enable) && $location_enable}
                        <div class="form-group hotel_location_div">
                            <label class="control-label" for="">{l s='Hotel Location' mod='wkroomsearchblock'}</label>
                            <input class="form-control" placeholder="Enter a city, state, country name" type="text" id="hotel_location" name="hotel_location" autocomplete="off"/>
                            <ul class="location_search_results_ul"></ul>
                        </div>
                    {/if}
                    <div class="form-group">
                        <label class="control-label" for="">{l s='Hotel Name' mod='wkroomsearchblock'}</label>
                        <div class="dropdown">
                            <button class="btn btn-default dropdown-toggle hotel_cat_id_btn {if isset($error) && $error == 1}error_border{/if}" type="button" data-toggle="dropdown">
                                <span id="hotel_cat_name" class="pull-left">{l s='Select Hotel'}</span>
                                <input type="hidden" id="hotel_cat_id" name="hotel_cat_id">
                                <div class="caret_div">
                                    <span class="caret"></span>
                                </div>  
                            </button>
                            <ul class="dropdown-menu hotel_dropdown_ul">
                                {if isset($hotel_name) && $hotel_name}
                                    {foreach $hotel_name as $name_val}
                                        <li class="hotel_name" data-hotel-cat-id="{$name_val['id_category']}">{$name_val['hotel_name']}</li>
                                    {/foreach}
                                {/if} 
                            </ul>
                        </div>
                        <p class="error_msg" id="select_htl_error_p">{if isset($error) && $error == 1}{l s='Please select a hotel.' mod='wkroomsearchblock'}{/if}</p>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-6 col-md-6 col-sm-12">
                                <label class="control-label" for="check_in_time">{l s='Check In Time' mod='wkroomsearchblock'}</label>
                                <input type="hidden" name="is_hotel_rooms_search" value="1">
                                <div class="input-group">
                                    <input class="form-control {if isset($error) && ($error == 2 || $error == 5)}error_border{/if}" type="text" id="check_in_time" name="check_in_time" autocomplete="off"/>
                                    <label class="input-group-addon calender-icon-cont" for="check_in_time"><i class="icon-calendar"></i></label>
                                </div>
                                <p class="error_msg" id="check_in_time_error_p">
                                    {if isset($error)}
                                        {if ($error == 2)}
                                            {l s='Check In date is required.' mod='wkroomsearchblock'}
                                        {elseif ($error == 5)}
                                            {l s='check In date can not be before todays date.' mod='wkroomsearchblock'}
                                        {/if}
                                    {/if}
                                </p>
                            </div>
                            <div class="col-xs-6 col-md-6 col-sm-12">
                                <label class="control-label" for="check_out_time">{l s='Check Out Time' mod='wkroomsearchblock'}</label>
                                <div class="input-group">
                                    <input class="form-control {if isset($error) && ($error == 3 || $error == 4)}error_border{/if}" type="text" id="check_out_time" name="check_out_time" autocomplete="off"/>
                                    <label class="input-group-addon calender-icon-cont" for="check_out_time"><i class="icon-calendar"></i></label>
                                </div>
                                <p class="error_msg" id="check_out_time_error_p">
                                    {if isset($error)}
                                        {if ($error == 3)}
                                            {l s='Check Our Date is required.' mod='wkroomsearchblock'}
                                        {elseif ($error == 4)}
                                            {l s='check Out date must be greater then check In date.' mod='wkroomsearchblock'}
                                        {/if}
                                    {/if}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-default button button-medium" name="search_room_submit" id="search_room_submit">
                            <span>{l s='Search Now' mod='wkroomsearchblock'}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{/if}

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
{/strip}