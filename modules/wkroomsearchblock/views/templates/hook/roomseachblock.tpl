{if isset($is_index_page) && $is_index_page}
    <div class="header-rmsearch-container header-rmsearch-hide-xs hidden-xs">
        {include file="./roomSearchWrapper.tpl"}
    </div>
    {*<div class="header-rmsearch-container header-rmsearch-show-xs visible-xs">
        {include file="./roomSearchWrapperXS.tpl"}
    </div>*}
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