{*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}

<div class="booking-form card">
    <div class="booking_room_fields">
        {block name='booking_form_content'}
            <form id="booking-form" action="" method="post">
                {block name='product_hidden_fields'}
                    <p class="hidden">
                        <input type="hidden" name="token" value="{$static_token}" />
                        <input type="hidden" name="id_product" value="{$product->id|intval}" id="product_page_product_id" />
                        <input type="hidden" name="booking_product" value="{$product->booking_product|intval}" id="product_page_booking_product" />
                        <input type="hidden" name="add" value="1" />
                        <input type="hidden" name="id_product_attribute" id="idCombination" value="" />
                    </p>
                {/block}
                {block name='booking_form_hotel_location'}
                    {if isset($id_hotel) && $id_hotel}
                        <div class="form-group htl_location_block">
                            <label for="" class="control-label">{l s='Hotel Location'}</label>
                            <p>{$hotel_location|escape:'html':'UTF-8'}</p>
                        </div>
                    {/if}
                {/block}
                {if $product->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
                    {* Block for booking products *}
                    {if $product->booking_product}
                        {if !$order_date_restrict}
                            {hook h='displayRoomTypeBookingFormFieldsBefore' id_product=$product->id id_hotel=$id_hotel}
                            <div class="row">
                                {block name='booking_form_dates'}
                                    <div class="form-group col-sm-12">
                                        <label class="control-label">{l s='Check In - Check Out'}</label>
                                        <div class="form-control input-date" id="room_date_range"  autocomplete="off" placeholder="{l s='Check-in - Check-out'}"><span>{l s='Check-in'} &nbsp;<i class="icon icon-minus"></i>&nbsp; {l s='Check-out'}</span></div>
                                        <input type="hidden" class="input-date" name="room_check_in" id="room_check_in" value="{if isset($date_from)}{$date_from}{/if}" />
                                        <input type="hidden" class="input-date" name="room_check_out" id="room_check_out" value="{if isset($date_to)}{$date_to}{/if}" />
                                    </div>
                                {/block}
                            </div>
                            {if $total_available_rooms > 0}
                                {block name='booking_form_quantity_wrapper'}
                                    <div class="row">
                                        <div class="form-group col-sm-12"{if !$product->available_for_order || $PS_CATALOG_MODE} style="display: none;"{/if}>
                                            {if isset($occupancy_required_for_booking) && $occupancy_required_for_booking}
                                                <label class="control-label">{l s='Guests'}</label>
                                                {block name='occupancy_field'}
                                                    {include file="./occupancy_field.tpl"}
                                                {/block}
                                            {else}
                                                <label class="control-label">{l s='No. of Rooms'}</label>
                                                {block name='quantity_field'}
                                                    {include file="./quantity_field.tpl"}
                                                {/block}
                                            {/if}
                                        </div>
                                    </div>
                                {/block}
                                {block name='booking_form_price_information'}
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
                                                {if (isset($selected_demands) && $selected_demands) || (isset($selected_service_product) && $selected_service_product)}
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
                                                                                    <a class="btn btn-sm btn-default remove_roomtype_product" data-id-product="{$product['id_product']}"><i class="icon-trash"></i></a>
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
                                {/block}
                                {block name='booking_form_actions'}
                                    <div id="booking_action_block">
                                        <div class="row">
                                            {block name='booking_form_total_price'}
                                                <div class="total_price_block col-xs-7 form-group">
                                                    <label class="control-label">{l s='Total'}</label>
                                                    <p>
                                                        {if $total_price && $total_price_without_discount > $total_price}
                                                            <span class="room_type_old_price">
                                                                {convertPrice price=$total_price_without_discount|floatval}
                                                            </span>
                                                        {/if}
                                                        {convertPrice price=$total_price|floatval}
                                                    </p>
                                                </div>
                                            {/block}
                                            {block name='booking_form_available_quantity'}
                                                {if $total_available_rooms <= $warning_count}
                                                    <div class="col-xs-5 form-group text-right num_quantity_alert">
                                                        <span class="num_searched_avail_rooms">
                                                            {$total_available_rooms|escape:'html':'UTF-8'}
                                                        </span>
                                                        {if $total_available_rooms > 1} {l s='rooms left!'} {else} {l s='room left!'} {/if}
                                                    </div>
                                                {/if}
                                            {/block}
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
                                                {block name='booking_form_book_now_button'}
                                                    <p id="add_to_cart" class="buttons_bottom_block no-print">
                                                        <button type="submit" name="Submit" class="exclusive book_now_submit">
                                                            <span>
                                                                {if isset($content_only) && $content_only && (isset($product->customization_required) && $product->customization_required)}{l s='Customize'}{else}{l s='Book Now'}{/if}
                                                            </span>
                                                            <span id="booking_action_loader"></span>
                                                        </button>
                                                    </p>
                                                {/block}
                                            {/if}
                                        </div>
                                    </div>
                                {/block}
                            {else}
                                <div class="sold_out_alert">
                                    <span>{l s='All rooms sold out!'}</span>
                                </div>
                            {/if}
                        {/if}
                    {else}
                        {block name='booking_form_associated_hotels'}
                            {if isset($associated_hotels) && $associated_hotels}
                                <div class="form-group">
                                    <label class="control-label">{l s='Select Hotel'}</label>
                                    <select class="chosen input-hotel" name="service_id_hotel" id="service_id_hotel">
                                        {foreach $associated_hotels as $hotel}
                                            <option value="{$hotel.id_hotel}" {if isset($service_id_hotel) && $service_id_hotel == $hotel['id_hotel']}selected{elseif $hotel@first}selected{/if}>{$hotel.name}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            {/if}
                        {/block}
                        {block name='booking_form_product_option'}
                            {if isset($product_option) && $product_option}
                                <label class="control-label">{l s='Variants'}</label>
                                <hr>
                                <div class="product-options-block">
                                    {foreach $product_option as $option}
                                        <div class="form-group">
                                            <label for="id_product_option{$option['id_product_option']}" class="top">
                                                <input type="radio" name="id_product_option" id="id_product_option{$option['id_product_option']}" value="{$option['id_product_option']}" {if isset($id_product_option) && $id_product_option == $option['id_product_option']}checked="checked"{else if $option@first}checked="checked"{/if}/>
                                                {$option['name']}
                                                <span class="pull-right">{convertPrice price=$option['price']}</span>
                                            </label>
                                        </div>
                                        <hr>
                                    {/foreach}
                                </div>
                            {/if}
                        {/block}
                        {block name='booking_form_service_product_quantity'}
                            {assign var='is_out_of_stock' value=false}
                            {assign var='max_qty_reached' value=false}
                            {if (!$allow_oosp && $product->quantity <= 0) || !$product->available_for_order || (isset($restricted_country_mode) && $restricted_country_mode) || $PS_CATALOG_MODE}
                                {assign var='is_out_of_stock' value=true}
                            {else if $product->allow_multiple_quantity && !$product->max_quantity}
                                {assign var='max_qty_reached' value=true}
                            {/if}

                            {if $product->allow_multiple_quantity}
                                <div class="row">
                                    <div class="form-group col-sm-6" id="quantity_wanted_p"{if $is_out_of_stock || $max_qty_reached} style="display: none;"{/if}>
                                        <label for="quantity_wanted_p">{l s='Quantity'}</label>
                                        {* {block name='quantity_field'}
                                            {include file="./quantity_field.tpl"}
                                        {/block} *}

                                            <div class="qty_container">
                                                <input type="hidden" class="stock_qty" id="stock_qty" name="stock_qty" data-id-product="{$product->id}" data-stock_quantity="{$product->quantity}" data-allow_oosp="{$allow_oosp}" >
                                                <input type="hidden" class="service_product_qty" id="service_product_qty" name="service_product_qty" data-id-product="{$product->id}" data-cart_quantity="{if isset($product->cart_quantity) && $product->cart_quantity}{$product->cart_quantity}{else}0{/if}" data-max_quantity="{if isset($product->max_quantity)}{$product->max_quantity|escape:'html':'UTF-8'}{else}{$product->quantity}{/if}" value="{if isset($quantity)}{$quantity|intval}{else}{if $product->minimal_quantity > 1}{$product->minimal_quantity}{else}1{/if}{/if}">
                                                <div class="qty_count pull-left">
                                                    <span>{if isset($quantity)}{$quantity|intval}{else}{if $product->minimal_quantity > 1}{$product->minimal_quantity}{else}1{/if}{/if}</span>
                                                </div>
                                                <div class="qty_direction pull-left">
                                                    <a href="#" class="btn btn-default quantity_up service_product_qty_up"><span><i class="icon-plus"></i></span></a>
                                                    <a href="#" class="btn btn-default quantity_down service_product_qty_down"><span><i class="icon-minus"></i></span></a>
                                                </div>
                                            </div>
                                        <span class="clearfix"></span>
                                    </div>
                                </div>
                                {if !$is_out_of_stock && !$max_qty_reached}
                                    <hr class="separator-hr-mg-10">
                                {/if}
                            {else}
                                <input type="hidden" class="service_product_qty" id="service_product_qty" name="service_product_qty" data-id-product="{$product->id}" data-max_quantity="1" value="1">
                            {/if}
                        {/block}
                        {block name='booking_form_actions'}
                            <div id="booking_action_block">
                                <div class="row">
                                    {block name='booking_form_total_price'}
                                        <div class="total_price_block col-xs-7 form-group">
                                            <label class="control-label">{l s='Price'}</label>
                                            <p>
                                                {if $service_price && $service_price_without_reduction > $service_price}
                                                    <span class="room_type_old_price">
                                                        {convertPrice price=$service_price_without_reduction|floatval}
                                                    </span>
                                                {/if}
                                                {convertPrice price=$service_price}
                                            </p>
                                        </div>
                                    {/block}
                                    {block name='booking_form_available_quantity'}
                                        {* {if $product->quantity <= 20}
                                            <div class="col-xs-5 form-group text-right num_quantity_alert">
                                                <span class="num_searched_avail_rooms">
                                                    {$product->quantity|escape:'html':'UTF-8'}
                                                </span>
                                                {l s=' Qty Available!'}
                                            </div>
                                        {/if} *}
                                    {/block}
                                </div>
                                <div>
                                   {if $is_out_of_stock}
                                        <div class="sold_out_alert">
                                            <span>{l s='Product is out of stock!'}</span>
                                        </div>
                                    {else if $max_qty_reached}
                                        <div class="sold_out_alert">
                                            <span>{l s='Max. quantity reached for cart!'}</span>
                                        </div>
                                    {else}
                                        {block name='booking_form_book_now_button'}
                                                <p id="add_to_cart" class="buttons_bottom_block no-print">
                                                    <button type="submit" name="Submit" class="exclusive book_now_submit">
                                                        <span>
                                                            {if isset($content_only) && $content_only && (isset($product->customization_required) && $product->customization_required)}{l s='Customize'}{else}{l s='Book Now'}{/if}
                                                        </span>
                                                        <span id="booking_action_loader"></span>
                                                    </button>
                                                </p>
                                        {/block}
                                    {/if}
                                </div>
                            </div>
                        {/block}
                    {/if}
                {/if}
                {* {if $order_date_restrict}
                    <div class="order_restrict_alert">
                        <span>{l s='You can\'t book rooms after %s.' sprintf=[{dateFormat date=$max_order_date full=0}]}</span>
                    </div>
                {/if} *}
            </form>
        {/block}
    </div>
</div>
