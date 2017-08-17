<div class="header-rmsearch-wrapper" id="xs_room_search_form">
    <div class="header-rmsearch-primary">
    	<div class="fancy_search_header_xs">
			<p>{l s='Search Rooms' mod='wkroomsearchblock'}</p>
			<hr>
		</div>
        <div class="container">
            <div class="row header-rmsearch-inner-wrapper">
                <form method="POST" id="search_hotel_block_form">
				    {if isset($location_enable) && $location_enable}
				        <div class="form-group col-sm-6 col-lg-3">
				            <input type="text" class="form-control header-rmsearch-input"  id="hotel_location" name="hotel_location" autocomplete="off" placeholder="Hotel Location">
				            <div class="dropdown">
				                <ul class="location_search_results_ul"></ul>
				            </div>
				        </div>
				    {/if}    
				    <div class="form-group col-sm-6 col-lg-3">
				        <div class="dropdown">
				            <button class="form-control header-rmsearch-input {if isset($error) && $error == 1}error_border{/if}" type="button" data-toggle="dropdown">
				                <span id="hotel_cat_name" class="pull-left">{l s='Select Hotel' mod='wkroomsearchblock'}</span>
				                <input type="hidden" name="is_hotel_rooms_search" value="1">
				                <input type="hidden" id="hotel_cat_id" name="hotel_cat_id">
								<input type="hidden" id="id_hotel" name="id_hotel">
				                <input type="hidden" id="max_order_date" name="max_order_date" value="{if isset($max_order_date)}{$max_order_date}{/if}">
				                <span class="arrow_span">
				                    <i class="icon icon-angle-down"></i>
				                </span>
				            </button>
				            <ul class="dropdown-menu hotel_dropdown_ul">
				                {if isset($hotel_name) && $hotel_name}
				                    {foreach $hotel_name as $name_val}
				                        <li class="hotel_name" data-id-hotel="{$name_val['id']}" data-hotel-cat-id="{$name_val['id_category']}">{$name_val['hotel_name']}</li>
				                    {/foreach}
				                {/if} 
				            </ul>
				        </div>
				    </div>
				    <div class="form-group col-sm-4 col-lg-2">
				        <input type="text" class="form-control header-rmsearch-input input-date" id="check_in_time" name="check_in_time" autocomplete="off" placeholder="Check In Date">
				    </div>
				    <div class="form-group col-sm-4 col-lg-2">
				        <input type="text" class="form-control header-rmsearch-input input-date" id="check_out_time" name="check_out_time" autocomplete="off" placeholder="Check Out Date">
				    </div>
				    <div class="form-group col-sm-4 col-lg-2">
				        <button type="submit" class="btn btn-default button button-medium exclusive" name="search_room_submit" id="search_room_submit">
				            <span>{l s='Search Now' mod='wkroomsearchblock'}</span>
				        </button>
				    </div>
				</form>
            </div>
        </div>
    </div>
</div>
