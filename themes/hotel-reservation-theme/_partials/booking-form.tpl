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
        <form action="" method="post">
            <div class="form-group htl_location_block">
                <label for="" class="control-label">{l s='Hotel Location'}</label>
                <p>{$hotel_location|escape:'html':'UTF-8'}</p>
            </div>
            {if $product->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE && !$order_date_restrict}
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for="" class="control-label">{l s='Check-in Date'}</label>
                        <input type="text" class="form-control input-date" name="room_check_in" id="room_check_in" value="{if isset($date_from)}{$date_from|date_format:"%d-%m-%Y"}{/if}" autocomplete="off" readonly />
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="" class="control-label">{l s='Check-out Date'}</label>
                        <input type="text" class="form-control input-date" name="room_check_out" id="room_check_out" value="{if isset($date_to)}{$date_to|escape:'html':'UTF-8'|date_format:"%d-%m-%Y"}{/if}" autocomplete="off" readonly />
                    </div>
                </div>
                <div class="room_unavailability_date_error_div"></div>
                {if $total_available_rooms > 0}
                    <div class="unvail_rooms_cond_display row">
                        <div class="form-group col-sm-6" id="quantity_wanted_p"{if (!$allow_oosp && $product->quantity <= 0) || !$product->available_for_order || $PS_CATALOG_MODE} style="display: none;"{/if}>
                            <label for="quantity_wanted">{l s='No. of Rooms'}</label>
                            <div class="qty_sec_cont">
                                <div class="qty_input_cont row margin-lr-0">
                                    <input autocomplete="off" type="text" min="1" name="qty" id="quantity_wanted" class="text" value="{$quantity|intval}">

                                    <input type="hidden" id="num_days" value="{if isset($num_days)}{$num_days|escape:'html':'UTF-8'}{/if}">
                                    <input type="hidden" id="max_avail_type_qty" value="{if isset($total_available_rooms)}{$total_available_rooms|escape:'html':'UTF-8'}{/if}">
                                </div>
                                <div class="qty_direction">
                                    <a href="#" data-field-qty="qty" class="btn btn-default product_quantity_up">
                                        <span>
                                            <i class="icon-plus"></i>
                                        </span>
                                    </a>
                                    <a href="#" data-field-qty="qty" class="btn btn-default product_quantity_down">
                                        <span>
                                            <i class="icon-minus"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                            <span class="clearfix"></span>
                        </div>
                    </div>
                    {if isset($has_room_type_demands) && $has_room_type_demands}
                        <hr class="separator-hr-mg-10 unvail_rooms_cond_display">
                        <div class="row price_desc_block unvail_rooms_cond_display">
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
                        <hr class="separator-hr-mg-10 form-group unvail_rooms_cond_display">
                    {/if}
                    <div class="room_unavailability_qty_error_div"></div>
                    <div class="row unvail_rooms_cond_display">
                        <div class="total_price_block col-xs-7 form-group">
                            <label class="control-label">{l s='Subtotal'}</label>
                            <p>{convertPrice price=$total_price|floatval}</p>
                        </div>
                        {if $total_available_rooms <= $warning_count}
                            <div class="col-xs-5 form-group pull-right num_quantity_alert">
                                <span class="num_searched_avail_rooms">
                                    {$total_available_rooms|escape:'html':'UTF-8'}
                                </span>
                                {if $total_available_rooms > 1} {l s='rooms left!'} {else} {l s='room left!'} {/if}
                            </div>
                        {/if}
                    </div>

                    <div class="unvail_rooms_cond_display">
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