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
            {if isset($id_hotel) && $id_hotel}
                <div class="form-group htl_location_block">
                    <label for="" class="control-label">{l s='Hotel Location'}</label>
                    <p>{$hotel_location|escape:'html':'UTF-8'}</p>
                </div>
            {/if}
            {if $product->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
                {* Block for booking products *}
                {if $product->booking_product}
                    {if  !$order_date_restrict}
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <div class="form-control input-date" id="room_date_range"  autocomplete="off" placeholder="{l s='Check-in - Check-out'}"><span>{l s='Check-in'} &nbsp;<i class="icon icon-minus"></i>&nbsp; {l s='Check-out'}</span></div>
                                <input type="hidden" class="input-date" name="room_check_in" id="room_check_in" value="{if isset($date_from)}{$date_from}{/if}" />
                                <input type="hidden" class="input-date" name="room_check_out" id="room_check_out" value="{if isset($date_to)}{$date_to}{/if}" />
                            </div>
                        </div>
                        {if $total_available_rooms > 0}
                            <div class="row">
                                <div class="form-group col-sm-12"{if !$product->available_for_order || $PS_CATALOG_MODE} style="display: none;"{/if}>
                                    {if isset($occupancy_required_for_booking) && $occupancy_required_for_booking}
                                        <label class="control-label">{l s='Guests'}</label>
                                        {include file="./occupancy_field.tpl"}
                                    {else}
                                        <label class="control-label">{l s='No. of Rooms'}</label>
                                        {include file="./quantity_field.tpl"}
                                    {/if}
                                </div>
                            </div>
                            {if (isset($has_room_type_demands) && $has_room_type_demands) || (isset($service_products_exists) && $service_products_exists)}
                                <hr class="separator-hr-mg-10">
                                <div class="row price_desc_block">
                                    <div class="col-sm-6">
                                        <label class="control-label">{l s='Room Price'}</label>
                                            <p>
                                                <span class="total_price_block">{convertPrice price=$rooms_price|floatval}</span>
                                                <span class="pull-right plus-sign">+</span>
                                            </p>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="control-label">{l s='Extra Services'}</label>
                                        <p class="extra_demands_price_block">
                                            {if isset($demands_price)}{convertPrice price=$demands_price}{else}{convertPrice price=0}{/if}
                                            {if (isset($selected_demands) && $selected_demands) || (isset($selected_service_product) && $selected_service_product)}
                                                <span class="services-info">
                                                    <img src="{$img_dir}icon/icon-info.svg" />
                                                </span>
                                            {/if}
                                        </p>
                                        {if (isset(selected_demands) && selected_demands) || (isset(selected_service_product) && selected_service_product)}
                                            <div class="services-info-container" style="display: none;">
                                                <div class="services-info-tooltip-cont">
                                                    {if isset($selected_service_product) && $selected_service_product}
                                                        <div class="extra-service-panel">
                                                            <p class="panel_title">{l s='Selected services'} <span>{l s='(Per room)'}</span></p>
                                                            <div class="services-list">
                                                                {foreach $selected_service_product as $product}
                                                                    <div class="services-list-row">
                                                                        <div>
                                                                            {$product['name']}
                                                                            {if $product['allow_multiple_quantity']}
                                                                                <p>{l s='qty'}: {$product['quantity']}</p>
                                                                            {/if}
                                                                        </div>
                                                                        <div class="text-right">
                                                                            <p>{displayPrice price=$product['price']}</p>
                                                                            <a class="btn btn-sm btn-default remove_roomtype_product" data-id_product="{$product['id_product']}"><i class="icon-trash"></i></a>
                                                                        </div>
                                                                    </div>
                                                                {/foreach}
                                                            </div>
                                                        </div>
                                                    {/if}
                                                    {if isset($selected_demands) && $selected_demands}
                                                        <div class="extra-service-panel">
                                                            <p class="panel_title">{l s='Selected facilities'} <span>{l s='(Per room)'}</span></p>
                                                            <div class="services-list">
                                                                {foreach $selected_demands as $product}
                                                                    <div class="services-list-row">
                                                                        <div>
                                                                            {$product['name']}
                                                                            {if isset($product['advance_option']) && $product['advance_option']}
                                                                                <p>{l s='Option:'} {$product['advance_option']['name']}</p>
                                                                            {/if}
                                                                        </div>
                                                                        <div class="text-right">
                                                                            <p>{displayPrice price=$product['price']}</p>
                                                                            <a class="btn btn-sm btn-default remove_roomtype_demand" data-id_global_demand="{$product['id_global_demand']}"><i class="icon-trash"></i></a>
                                                                        </div>
                                                                    </div>
                                                                {/foreach}
                                                            </div>
                                                        </div>
                                                    {/if}
                                                    <hr>
                                                    <div class="extra-service-panel">
                                                        <div class="summary-row">
                                                            <div>{l s='Total price per room'}</div>
                                                            <div><p class="service_price">{displayPrice price=$demands_price_per_room}</p></div>
                                                        </div>
                                                        <div class="summary-row">
                                                            <div>{l s='Total price:'}</div>
                                                            <div><p class="service_price">{displayPrice price=$demands_price}</p></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        {/if}
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
                                        <div id="additional_products" class="hidden">
                                            {if isset($selected_service_product) && $selected_service_product}
                                                {foreach $selected_service_product as $product}
                                                    <input type="hidded" id="service_product_{$product['id_product']}" name="service_product[{$product['id_product']}][]" class="service_product" data-id_product="{$product['id_product']}" value="{$product['quantity']}">
                                                {/foreach}
                                            {/if}
                                        </div>
                                        <p id="add_to_cart" class="buttons_bottom_block no-print">
                                            <button type="submit" name="Submit" class="exclusive book_now_submit">
                                                <span>
                                                    {if isset($content_only) && $content_only && (isset($product->customization_required) && $product->customization_required)}{l s='Customize'}{else}{l s='Book Now'}{/if}
                                                </span>
                                                <span id="booking_action_loader"></span>
                                            </button>
                                        </p>
                                    {/if}
                                </div>
                            </div>
                        {else}
                            <div class="sold_out_alert">
                                <span>{l s='All rooms sold out!'}</span>
                            </div>
                        {/if}
                    {/if}
                {else}

                    {if $product->allow_multiple_quantity}
                        <div class="row">
                            <div class="form-group col-sm-6" id="quantity_wanted_p"{if (!$allow_oosp && $product->quantity <= 0) || !$product->available_for_order || $PS_CATALOG_MODE} style="display: none;"{/if}>
                                <label for="quantity_wanted">{l s='Quantity'}</label>
                                <div class="qty_sec_cont">
                                    <div class="qty_input_cont row margin-lr-0">
                                        <input autocomplete="off" type="text" min="1" name="qty" id="quantity_wanted" class="text" value="{if isset($quantityBackup)}{$quantityBackup|intval}{else}{if $product->minimal_quantity > 1}{$product->minimal_quantity}{else}1{/if}{/if}">
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
                    {/if}
                    <div class="row">
                        <div class="total_price_block col-xs-7 form-group">
                            <label class="control-label">{l s='Price'}</label>
                            <p>
                                {convertPrice price=$productPrice|floatval}
                            </p>
                        </div>
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