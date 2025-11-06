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

<div class="order-detail-content">
    {if isset($cart_htl_data) && $cart_htl_data}
        {block name='shopping_cart_heading'}
            <p class="cart_section_title">{l s='rooms information'}</p>
        {/block}
        {foreach from=$cart_htl_data key=data_k item=data_v}
            {foreach from=$data_v['date_diff'] key=rm_k item=rm_v}
                <div class="row cart_product_line">
                    <div class="col-sm-2 product-img-block">
                        {block name='shopping_cart_room_type_cover_image'}
                            <p>
                                <a href="{$link->getProductLink($data_v['id_product'])}">
                                    <img src="{$data_v['cover_img']}" class="img-responsive" />
                                </a>
                            </p>
                            <p class="room_remove_block">
                                <a class="cart_room_delete" href="{$rm_v['link']}" data-id_product="{$data_v['id_product']}" data-date_from="{$rm_v['data_form']}" data-date_to="{$rm_v['data_to']}" data-qty="{$rm_v['num_rm']}"><i class="icon-trash"></i> &nbsp;{l s='Remove'}</a>
                            </p>
                            {block name='displayCartRoomImageAfter'}
                                {hook h='displayCartRoomImageAfter' id_product=$data_v['id_product']}
                            {/block}
                        {/block}
                    </div>
                    <div class="col-sm-10">
                        <div class="room-info-container">
                            {block name='shopping_cart_room_type_cover_image_mobile'}
                                <div class="product-xs-img">
                                    <a href="{$link->getProductLink($data_v['id_product'])}">
                                        <img src="{$data_v['cover_img']}" class="img-responsive" />
                                    </a>
                                </div>
                            {/block}
                            {block name='shopping_cart_room_detail'}
                                <div class="product-xs-info">
                                    {block name='shopping_cart_room_type_name'}
                                        <p class="product-name">
                                            <a href="{$link->getProductLink($data_v['id_product'])}">
                                                {$data_v['name']}
                                            </a>
                                            <a class="btn btn-default pull-right product-xs-remove" href="{$rm_v['link']}"><i class="icon-trash"></i></a>
                                            {block name='displayCartRoomTypeNameAfter'}
                                                {hook h='displayCartRoomTypeNameAfter' id_product=$data_v['id_product']}
                                            {/block}
                                        </p>
                                    {/block}
                                    {block name='shopping_cart_room_type_hotel_location'}
                                        {if isset($data_v['hotel_info']['location'])}
                                            <p class="hotel-location">
                                                <i class="icon-map-marker"></i> &nbsp;{$data_v['hotel_info']['location']}
                                            </p>
                                        {/if}
                                    {/block}
                                    {block name='displayCartRoomTypeInfo'}
                                        {hook h='displayCartRoomTypeInfo' id_product=$data_v['id_product']}
                                    {/block}
                                </div>
                            {/block}
                        </div>
                        {block name='shopping_cart_room_type_features'}
                            {if isset($data_v['hotel_info']['room_features'])}
                                <div class="room-type-features">
                                {foreach $data_v['hotel_info']['room_features'] as $feature}
                                    <span class="room-type-feature">
                                        <img src="{$THEME_DIR}img/icon/form-ok-circle.svg" /> {$feature['name']}
                                    </span>
                                {/foreach}
                                </div>
                            {/if}
                        {/block}
                        {block name='shopping_cart_room_type_booking_information'}
                            {assign var="is_full_date" value=($show_full_date && ($rm_v['data_form']|date_format:'%D' == $rm_v['data_to']|date_format:'%D'))}
                            <div class="room_duration_block">
                                <div class="col-sm-3 col-xs-6">
                                    <p class="room_duration_block_head">{l s='CHECK IN'}</p>
                                    <p class="room_duration_block_value">{$rm_v['data_form']|date_format:"%d %b, %a"}{if $is_full_date} {$rm_v['data_form']|date_format:"%H:%M"}{/if}</p>
                                </div>
                                <div class="col-sm-3 col-xs-6">
                                    <p class="room_duration_block_head">{l s='CHECK OUT'}</p>
                                    <p class="room_duration_block_value">{$rm_v['data_to']|date_format:"%d %b, %a"}{if $is_full_date} {$rm_v['data_to']|date_format:"%H:%M"}{/if}</p>
                                </div>
                                <div class="col-sm-6 col-xs-6">
                                    <p class="room_duration_block_head">{l s='OCCUPANCY'}</p>
                                    <p class="room_duration_block_value">
                                        {if {$rm_v['adults']} <= 9}0{$rm_v['adults']}{else}{$rm_v['adults']}{/if} {if $rm_v['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if $rm_v['children']}, {if $rm_v['children'] <= 9}0{$rm_v['children']}{else}{$rm_v['children']}{/if} {if $rm_v['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if}, {if {$rm_v['num_rm']} <= 9}0{/if}{$rm_v['num_rm']}{if $rm_v['num_rm'] > 1} {l s='Rooms'}{else} {l s='Room'}{/if}
                                    </p>
                                </div>
                            </div>
                        {/block}
                        {block name='shopping_cart_room_type_price_detail'}
                            <div class="row room_price_detail_block">
                                {block name='shopping_cart_room_type_and_service_price'}
                                    <div class="col-sm-7 margin-btm-sm-10">
                                        {if $rm_v['amount'] && isset($rm_v['total_price_without_discount']) && $rm_v['total_price_without_discount'] > $rm_v['amount']}
                                            <span class="room_type_old_price">
                                                {displayPrice price=$rm_v['total_price_without_discount']|floatval}
                                            </span>
                                        {/if}
                                        <div class="row">
                                            <div class="{if (isset($data_v['extra_demands']) && $data_v['extra_demands']) || (isset($data_v['service_products']) && $data_v['service_products'])}col-xs-6 plus-sign{else}col-xs-12{/if}">
                                                <div class="price_block">
                                                    <p class="total_price">
                                                        <span>
                                                            {displayPrice price=($rm_v['amount'])}
                                                        </span>
                                                        {if (($rm_v['amount'] - $rm_v['amount_without_auto_add']) > 0) && (in_array($data_v['id_product'], $discounted_products) || $PS_ROOM_PRICE_AUTO_ADD_BREAKDOWN)}
                                                            <span class="room-price-detail">
                                                                <img src="{$img_dir}icon/icon-info.svg" />
                                                            </span>
                                                            <div class="room-price-detail-container" style="display: none;">
                                                                <div class="room-price-detail-tooltip-cont">
                                                                    <div><label>{l s='Room price'}</label> : {displayPrice price=($rm_v['amount_without_auto_add'])}</div>
                                                                    <div><label>{l s='Additional charges'}</label> : {displayPrice price=($rm_v['amount'] - $rm_v['amount_without_auto_add'])}</div>
                                                                </div>
                                                            </div>
                                                        {/if}
                                                    </p>
                                                    <p class="total_price_detial">
                                                        {l s='Total rooms price'} {if $display_tax_label}{if $priceDisplay} {l s='(Excl.'} {else}{l s='(Incl.)'}{/if} {l s='all taxes.)'}{/if}
                                                    </p>
                                                </div>
                                            </div>
                                            {if (isset($data_v['extra_demands']) && $data_v['extra_demands']) || (isset($data_v['service_products']) && $data_v['service_products'])}
                                                <div class="col-xs-6">
                                                    <div class="demand_price_block">
                                                        <p class="demand_total_price">
                                                            <span>
                                                                {displayPrice price=$rm_v['demand_price']}
                                                            </span>
                                                        </p>
                                                        <p class="total_price_detial">
                                                            <a data-date_from="{$rm_v['data_form']|escape:'html':'UTF-8'}" data-date_to="{$rm_v['data_to']|escape:'html':'UTF-8'}" data-id_product="{$data_v['id_product']|escape:'html':'UTF-8'}" data-action="{$link->getPageLink('order-opc')}" class="open_rooms_extra_services_panel" href="#rooms_type_extra_services_form">
                                                                {l s='Extra Services'}
                                                            </a>
                                                        </p>
                                                    </div>
                                                </div>
                                            {/if}
                                        </div>
                                    </div>
                                {/block}
                                {block name='shopping_cart_room_type_total_price'}
                                    <div class="col-sm-5">
                                        <div class="total_price_block col-xs-12">
                                            <p class="total_price">
                                                <span>
                                                    {displayPrice price=($rm_v['amount']+$rm_v['demand_price'])}
                                                </span>
                                            </p>
                                            <p class="total_price_detial">
                                                {l s='Total price for'} {$rm_v['num_days']} {l s='Night(s) stay'}{if $display_tax_label}{if $priceDisplay} {l s='(Excl.'} {else}{l s='(Incl.'}{/if} {l s='all taxes.)'}{/if}
                                            </p>
                                        </div>
                                    </div>
                                {/block}
                            </div>
                        {/block}
                        {block name='displayCartProductContentAfter'}
                            {hook h='displayCartProductContentAfter' cart_detail=$data_v key=$rm_k}
                        {/block}
                    </div>
                </div>
                {block name='displayCartProductAfter'}
                    {hook h='displayCartProductAfter' cart_detail=$data_v key=$rm_k}
                {/block}
                <hr>
            {/foreach}
        {/foreach}
    {/if}
    {block name='displayAfterShoppingCartRoomsSummary'}
		{hook h="displayAfterShoppingCartRoomsSummary"}
	{/block}
    {if (isset($hotel_products) && $hotel_products) || (isset($standalone_products) && $standalone_products)}
        <p class="cart_section_title">{l s='Product information'}</p>
    {/if}
    {if isset($hotel_products) && $hotel_products}
        {foreach from=$hotel_products key=data_k item=product}
            <div class="row cart_product_line">
                <div class="col-sm-2 product-img-block">
                    <p>
                        <a href="{$link->getProductLink($product['id_product'])}">
                            <img src="{$product['cover_img']}" class="img-responsive" />
                        </a>
                    </p>

                    <p class="product_remove_block">
                        <a id="{$product.id_product}_{if $product.id_product_option}{$product.id_product_option}{else}0{/if}_{if $product.id_hotel}{$product.id_hotel}{else}0{/if}" class="cart_quantity_delete" href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;token={$token_cart}")|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Delete'}">
                            <i class="icon-trash"></i> &nbsp;{l s='Remove'}
                        </a>
                        {* <a href="{$rm_v['link']}"><i class="icon-trash"></i> &nbsp;{l s='Remove'}</a> *}
                    </p>
                    {block name='displayCartProductImageAfter'}
                        {hook h='displayCartProductImageAfter' id_product=$product['id_product']}
                    {/block}
                </div>
                <div class="col-sm-10">
                    <div class="product-info-container">
                        <div class="product-xs-img">
                            <a href="{$link->getProductLink($product['id_product'])}">
                                <img src="{$product['cover_img']}" class="img-responsive" />
                            </a>
                        </div>
                        <div class="product-xs-info">
                            <p class="product-name">
                                <a href="{$link->getProductLink($product['id_product'])}">
                                    {$product['name']}{if isset($product['option_name']) && $product['option_name']} : {$product['option_name']}{/if}
                                </a>
                                <a class="btn btn-default pull-right product-xs-remove" href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;ipa=0&amp;id_address_delivery={$product.id_address_delivery}&amp;token={$token_cart}")|escape:'html':'UTF-8'}"><i class="icon-trash"></i></a>
                                {block name='displayCartProductNameAfter'}
                                    {hook h='displayCartProductNameAfter' id_product=$product['id_product']}
                                {/block}
                            </p>

                            {if isset($product['hotel_info']['location'])}
                                <p class="hotel-location">
                                    <i class="icon-map-marker"></i> &nbsp;{$product['hotel_info']['location']}
                                </p>
                                {block name='displayCartProductHotelLocationAfter'}
                                    {hook h='displayCartProductHotelLocationAfter' id_product=$product['id_product']}
                                {/block}
                            {/if}
                        </div>
                    </div>
                    <div class="row product_price_detail_block">
                        <div class="col-sm-7">
                            <div class="price_block col-xs-7">
                                <p class="total_price">
                                    <span>
                                        {if $priceDisplay}{displayPrice price=($product['unit_price_tax_excl'])}{else}{displayPrice price=($product['unit_price_tax_incl'])}{/if}
                                    </span>
                                </p>
                                <p class="total_price_detial">
                                    {l s='Unit price'} {if $display_tax_label}{if $priceDisplay} {l s='(Excl.'} {else}{l s='(Incl.)'}{/if} {l s='all taxes.)'}{/if}
                                </p>
                            </div>
                            {if $product.allow_multiple_quantity}
                                <div class="col-xs-5">
                                    <div class="quantity_cont">
                                        <input type="hidden" value="{$product.quantity}" name="quantity_{$product.id_product}_{if $product.id_product_option}{$product.id_product_option}{else}0{/if}_{if $product.id_hotel}{$product.id_hotel}{else}0{/if}_hidden" />
                                        <input size="2" type="text" autocomplete="off" class="cart_quantity_input grey" value="{$product.quantity}"  name="quantity_{$product.id_product}_{if $product.id_product_option}{$product.id_product_option}{else}0{/if}_{if $product.id_hotel}{$product.id_hotel}{else}0{/if}" />
                                        <div class="cart_quantity_button">
                                            <a rel="nofollow" class="cart_quantity_up btn btn-default" id="cart_quantity_up_{$product.id_product}_{if $product.id_product_option}{$product.id_product_option}{else}0{/if}_{if $product.id_hotel}{$product.id_hotel}{else}0{/if}" href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery=0&amp;token={$token_cart}")|escape:'html':'UTF-8'}" title="{l s='Add'}"><span><i class="icon-plus"></i></span></a>
                                            {if $product.minimal_quantity < ($product.quantity)}
                                                <a rel="nofollow" class="cart_quantity_down btn btn-default" id="cart_quantity_down_{$product.id_product}_{if $product.id_product_option}{$product.id_product_option}{else}0{/if}_{if $product.id_hotel}{$product.id_hotel}{else}0{/if}" href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery=0&amp;op=down&amp;token={$token_cart}")|escape:'html':'UTF-8'}" title="{l s='Subtract'}">
                                                    <span><i class="icon-minus"></i></span>
                                                </a>
                                            {else}
                                                <a class="cart_quantity_down btn btn-default disabled" href="#" id="cart_quantity_down_{$product.id_product}_{if $product.id_product_option}{$product.id_product_option}{else}0{/if}_{if $product.id_hotel}{$product.id_hotel}{else}0{/if}" title="{l s='You must purchase a minimum of %d of this product.' sprintf=1}">
                                                    <span><i class="icon-minus"></i></span>
                                                </a>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            {/if}
                        </div>
                        <div class="col-sm-5">
                            <div class="total_price_block col-xs-12">
                                <p class="total_price">
                                    <span>
                                        {if $priceDisplay}{displayPrice price=($product['total_price_tax_excl'])}{else}{displayPrice price=($product['total_price_tax_incl'])}{/if}
                                    </span>
                                </p>
                                <p class="total_price_detial">
                                    {l s='Total price'} {if $display_tax_label}{if $priceDisplay} {l s='(Excl.'} {else}{l s='(Incl.)'}{/if} {l s='all taxes.)'}{/if}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
        {/foreach}
    {/if}
    {if isset($standalone_products) && $standalone_products}
        {foreach from=$standalone_products key=data_k item=product}
            <div class="row cart_product_line">
                <div class="col-sm-2 product-img-block">
                    <p>
                        <a href="{$link->getProductLink($product['id_product'])}">
                            <img src="{$product['cover_img']}" class="img-responsive" />
                        </a>
                    </p>

                    <p class="product_remove_block">
                        <a id="{$product.id_product}_{if $product.id_product_option}{$product.id_product_option}{else}0{/if}_0" class="cart_quantity_delete" href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;token={$token_cart}")|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Delete'}">
                            <i class="icon-trash"></i> &nbsp;{l s='Remove'}
                        </a>
                        {* <a href="{$rm_v['link']}"><i class="icon-trash"></i> &nbsp;{l s='Remove'}</a> *}
                    </p>
                    {block name='displayCartProductImageAfter'}
                        {hook h='displayCartProductImageAfter' id_product=$product['id_product']}
                    {/block}

                </div>
                <div class="col-sm-10">
                    <div class="product-info-container">
                        <div class="product-xs-img">
                            <a href="{$link->getProductLink($product['id_product'])}">
                                <img src="{$product['cover_img']}" class="img-responsive" />
                            </a>
                        </div>
                        <div class="product-xs-info">
                            <p class="product-name">
                                <a href="{$link->getProductLink($product['id_product'])}">
                                    {$product['name']}{if isset($product['option_name']) && $product['option_name']} : {$product['option_name']}{/if}
                                </a>
                                <a class="btn btn-default pull-right product-xs-remove" href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;token={$token_cart}")|escape:'html':'UTF-8'}"><i class="icon-trash"></i></a>
                                {block name='displayCartProductNameAfter'}
                                    {hook h='displayCartProductNameAfter' id_product=$product['id_product']}
                                {/block}
                            </p>
                        </div>
                    </div>
                    <div class="row product_price_detail_block">
                        <div class="col-sm-7">
                            <div class="price_block col-xs-7">
                                <p class="total_price">
                                    <span>
                                        {if $priceDisplay}{displayPrice price=($product['unit_price_tax_excl'])}{else}{displayPrice price=($product['unit_price_tax_incl'])}{/if}
                                    </span>
                                </p>
                                <p class="total_price_detial">
                                    {l s='Unit price'} {if $display_tax_label}{if $priceDisplay} {l s='(Excl.'} {else}{l s='(Incl.)'}{/if} {l s='all taxes.)'}{/if}
                                </p>
                            </div>
                            {if $product.allow_multiple_quantity}
                                <div class="col-xs-5">
                                    <div class="quantity_cont">
                                        <input type="hidden" value="{$product.quantity}" name="quantity_{$product.id_product}_{if $product.id_product_option}{$product.id_product_option}{else}0{/if}_0_hidden" />
                                        <input size="2" type="text" autocomplete="off" class="cart_quantity_input grey" value="{$product.quantity}"  name="quantity_{$product.id_product}_{if $product.id_product_option}{$product.id_product_option}{else}0{/if}_0" />
                                        <div class="cart_quantity_button">
                                            <a rel="nofollow" class="cart_quantity_up btn btn-default" id="cart_quantity_up_{$product.id_product}_{if $product.id_product_option}{$product.id_product_option}{else}0{/if}_0" href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")|escape:'html':'UTF-8'}" title="{l s='Add'}"><span><i class="icon-plus"></i></span></a>
                                            {if $product.minimal_quantity < ($product.quantity)}
                                                <a rel="nofollow" class="cart_quantity_down btn btn-default" id="cart_quantity_down_{$product.id_product}_{if $product.id_product_option}{$product.id_product_option}{else}0{/if}_0" href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;op=down&amp;token={$token_cart}")|escape:'html':'UTF-8'}" title="{l s='Subtract'}">
                                                    <span><i class="icon-minus"></i></span>
                                                </a>
                                            {else}
                                                <a class="cart_quantity_down btn btn-default disabled" href="#" id="cart_quantity_down_{$product.id_product}_{if $product.id_product_option}{$product.id_product_option}{else}0{/if}_0" title="{l s='You must purchase a minimum of %d of this product.' sprintf=$product.minimal_quantity}">
                                                    <span><i class="icon-minus"></i></span>
                                                </a>
                                            {/if}

                                        </div>
                                    </div>
                                </div>
                            {/if}
                        </div>
                        <div class="col-sm-5">
                            <div class="total_price_block col-xs-12">
                                <p class="total_price">
                                    <span>
                                        {if $priceDisplay}{displayPrice price=($product['total_price_tax_excl'])}{else}{displayPrice price=($product['total_price_tax_incl'])}{/if}
                                    </span>
                                </p>
                                <p class="total_price_detial">
                                    {l s='Total price'} {if $display_tax_label}{if $priceDisplay} {l s='(Excl.'} {else}{l s='(Incl.)'}{/if} {l s='all taxes.)'}{/if}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                {block name='displayCartProductContainerBottom'}
                    {hook h='displayCartProductContainerBottom' id_product=$product['id_product']}
                {/block}
            </div>
            <hr>
        {/foreach}
    {/if}

    {* proceed only if no order restrict errors are there *}
    {if !$orderRestrictErr}
        {block name='shopping_cart_proceed_action'}
            <div class="row">
                <div class="col-sm-12 proceed_btn_block">
                    <a class="btn btn-default button button-medium pull-right" href="{$link->getPageLink('order-opc', null, null, ['proceed_to_customer_dtl' => 1])}" title="Proceed to checkout" rel="nofollow">
                        <span>
                            {l s='Proceed'}
                        </span>
                    </a>
                </div>
            </div>
        {/block}
    {/if}
</div>
