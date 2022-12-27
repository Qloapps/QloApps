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

{if isset($hotels_info) && count($hotels_info)}
	<div class="header-rmsearch-wrapper">
        <div class="filter_header">
            <p>{l s='Search Rooms' mod='wkroomsearchblock'}</p>
            <hr class="header-bottom-hr">
        </div>
		<form method="POST" id="search_hotel_block_form">
			{if isset($location_enabled) && $location_enabled}
				<div class="form-group">
					<input type="text" class="form-control header-rmsearch-input" id="hotel_location" name="hotel_location" autocomplete="off" placeholder="{l s='Hotel location' mod='wkroomsearchblock'}" {if isset($search_data)}value="{$search_data['location']|escape:'htmlall':'UTF-8'}" city_cat_id="{$search_data['parent_data']['id_category']|escape:'htmlall':'UTF-8'}"{/if}>
					<div class="dropdown">
						<ul class="location_search_results_ul dropdown-menu"></ul>
					</div>
				</div>
			{/if}
			<div class="form-group {if count($hotels_info) <= 1 && !$show_hotel_name} hidden {/if}">
				{if !$show_hotel_name}
					<input type="hidden" id="max_order_date" name="max_order_date" value="{if isset($hotels_info[0]['max_order_date'])}{$hotels_info[0]['max_order_date']|escape:'htmlall':'UTF-8'}{/if}">
					<input type="hidden" id="preparation_time" name="preparation_time" value="{if isset($hotels_info[0]['preparation_time'])}{$hotels_info[0]['preparation_time']|escape:'htmlall':'UTF-8'}{/if}">
					<input type="hidden" id="hotel_cat_id" name="hotel_cat_id" value="{$hotels_info[0]['id_category']}">
					<input type="hidden" id="id_hotel" name="id_hotel" value="{$hotels_info[0]['id']|escape:'htmlall':'UTF-8'}">
					<input type="text" id="htl_name" class="form-control header-rmsearch-input" value="{$hotels_info[0]['hotel_name']}" readonly>
				{else}
					{if isset($hotels_info) && count($hotels_info)}
						<div class="dropdown">
							<input type="hidden" id="hotel_cat_id" name="hotel_cat_id" {if isset($search_data)}value="{$search_data['htl_dtl']['id_category']|escape:'htmlall':'UTF-8'}"{/if}>
							<input type="hidden" id="preparation_time" name="preparation_time" value="{if isset($preparation_time)}{$preparation_time|escape:'htmlall':'UTF-8'}{/if}">
                            <input type="hidden" id="id_hotel" name="id_hotel" {if isset($search_data)}value="{$search_data['htl_dtl']['id']|escape:'htmlall':'UTF-8'}"{/if}>
                            <input type="hidden" id="max_order_date" name="max_order_date" value="{if isset($max_order_date)}{$max_order_date|escape:'htmlall':'UTF-8'}{/if}">

							<button class="form-control header-rmsearch-input {if isset($error) && $error == 1}error_border{/if}" type="button" data-toggle="dropdown" id="id_hotel_button">
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
				{/if}
			</div>
            <div class="form-group check_in_field_block">
                <input type="text" class="form-control header-rmsearch-input input-date" id="check_in_time" name="check_in_time" autocomplete="off" placeholder="{l s='Check-In' mod='wkroomsearchblock'}"  {if isset($search_data)}value="{$search_data['date_from']|escape:'htmlall':'UTF-8'}"{/if} readonly>
            </div>
            <div class="form-group check_out_field_block">
                <input type="text" class="form-control header-rmsearch-input input-date" id="check_out_time" name="check_out_time" autocomplete="off" placeholder="{l s='Check-Out' mod='wkroomsearchblock'}"  {if isset($search_data)}value="{$search_data['date_to']|escape:'htmlall':'UTF-8'}"{/if} readonly>
            </div>
			<div class="form-group">
				<button type="submit" class="btn btn btn-lg btn-primary" name="search_room_submit" id="search_room_submit">
					<span>{l s='Search Rooms' mod='wkroomsearchblock'}</span> <i class="icon-arrow-right pull-right"></i>
				</button>
			</div>
		</form>
	</div>
{/if}