{*
* 2010-2022 Webkul.
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
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
*}

<div class="booking-form card">
    <div class="booking_room_fields">
        <form id="booking-form" action="" method="post">
            <div class="form-group htl_location_block">
                <label for="" class="control-label">{l s='Hotel Location'}</label>
                <p>{$hotel_location|escape:'html':'UTF-8'}</p>
            </div>
            {if $product->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE && !$order_date_restrict}
                <div class="row">
                    <div class="form-group col-sm-12">
                        <div class="form-control input-date" id="room_date_range"  autocomplete="off" placeholder="{l s='Check-in - Check-out' mod='wkroomsearchblock'}"><span>{l s='Check-in' mod='wkroomsearchblock'} &nbsp;<i class="icon icon-minus"></i>&nbsp; {l s='Check-out' mod='wkroomsearchblock'}</span></div>
                        <input type="hidden" class="input-date" name="room_check_in" id="room_check_in" value="{if isset($date_from)}{$date_from}{/if}" />
                        <input type="hidden" class="input-date" name="room_check_out" id="room_check_out" value="{if isset($date_to)}{$date_to}{/if}" />
                    </div>
                </div>
                {if $total_available_rooms > 0}
                    <div class="row">
                        <div class="form-group col-sm-12"{if !$product->available_for_order || $PS_CATALOG_MODE} style="display: none;"{/if}>
                            {if isset($occupancy_wise_booking) && $occupancy_wise_booking}
                                <label class="control-label">{l s='Guests'}</label>
                                {include file="./occupancy_field.tpl"}
                            {else}
                                <label class="control-label">{l s='No. of Rooms'}</label>
                                {include file="./quantity_field.tpl"}
                            {/if}
                        </div>
                    </div>
                    {if isset($has_room_type_demands) && $has_room_type_demands}
                        <hr class="separator-hr-mg-10">
                        <div class="row price_desc_block">
                            <div class="col-sm-6">
                                <label class="control-label">{l s='Room Price'}</label>
                                    <p>
                                        <span class="total_rooms_price_block">{convertPrice price=$rooms_price|floatval}</span>
                                        <span class="pull-right plus-sign">+</span>
                                    </p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">{l s='Extra Facilities'}</label>
                                <p class="extra_demands_price_block">
                                    {if isset($demands_price)}{convertPrice price=$demands_price}{else}{convertPrice price=0}{/if}
                                </p>
                            </div>
                        </div>
                        <hr class="separator-hr-mg-10 form-group">
                    {/if}
                    <div id="booking_action_block">
                        <div class="row">
                            <div class="total_price_block col-xs-7 form-group">
                                <label class="control-label">{l s='Subtotal'}</label>
                                <p>{convertPrice price=$total_price|floatval}</p>
                            </div>
                            {if $total_available_rooms <= $warning_count}
                                <div class="col-xs-5 form-group text-right num_quantity_alert">
                                    <span class="num_searched_avail_rooms">
                                        {$total_available_rooms|escape:'html':'UTF-8'}
                                    </span>
                                    {if $total_available_rooms > 1} {l s='rooms left!'} {else} {l s='room left!'} {/if}
                                </div>
                            {/if}
                        </div>
                        <div>
                            {if (!$allow_oosp && $product->quantity <= 0) || !$product->available_for_order || (isset($restricted_country_mode) && $restricted_country_mode) || $PS_CATALOG_MODE || $order_date_restrict}
                            {else}
                                <p id="add_to_cart" class="buttons_bottom_block no-print">
                                    <button type="submit" name="Submit" class="exclusive book_now_submit">
                                        <span>
                                            {if isset($content_only) && $content_only && (isset($product->customization_required) && $product->customization_required)}{l s='Customize'}{else}{l s='Book Now'}{/if}
                                        </span>
                                    </button>
                                </p>
                            {/if}
                        </div>
                        <div id="booking_action_loader"><i class="icon-refresh icon-spin"></i></div>
                    </div>
                {else}
                    <div class="sold_out_alert">
                        <span>{l s='All rooms sold out!'}</span>
                    </div>
                {/if}
            {/if}
            {if $order_date_restrict}
                <div class="order_restrict_alert">
                    <span>{l s='You can\'t book rooms after %s.' sprintf=[{dateFormat date=$max_order_date full=0}]}</span>
                </div>
            {/if}
        </form>
    </div>
</div>