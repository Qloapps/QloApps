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

{block name='hotel_room_block'}
    {if isset($hotelRoomDisplay) && $hotelRoomDisplay}
        <div id="hotelRoomsBlock" class="row home_block_container">
            <div class="col-xs-12 col-sm-12">
                {if $HOTEL_ROOM_DISPLAY_HEADING && $HOTEL_ROOM_DISPLAY_DESCRIPTION}
                    <div class="row home_block_desc_wrapper">
                        <div class="col-md-offset-1 col-md-10 col-lg-offset-2 col-lg-8">
                            {block name='hotel_room_block_heading'}
                                <p class="home_block_heading">{$HOTEL_ROOM_DISPLAY_HEADING|escape:'htmlall':'UTF-8'}</p>
                            {/block}
                            {block name='hotel_room_block_description'}
                                <p class="home_block_description">{$HOTEL_ROOM_DISPLAY_DESCRIPTION|escape:'htmlall':'UTF-8'}</p>
                            {/block}
                            <hr class="home_block_desc_line"/>
                        </div>
                    </div>
                {/if}
                {block name='hotel_room_block_content'}
                    <div class="row home_block_content">
                        <div class="col-sm-12 col-xs-12">
                            {assign var='htlRoomBlockIteration' value=0}
                            {foreach from=$hotelRoomDisplay item=roomDisplay name=htlRoom}
                                {if $smarty.foreach.htlRoom.iteration%2}
                                    <div class="row">
                                {/if}
                                        <div class="col-sm-12 col-md-6 margin-btm-30">
                                            {block name='hotel_room_block_room_type_image'}
                                                <a href="{$link->getProductLink($roomDisplay.id_product)|escape:'html':'UTF-8'}">
                                                    <img src="{$roomDisplay.image|escape:'htmlall':'UTF-8'}" alt="{$roomDisplay.name|escape:'htmlall':'UTF-8'}" class="img-responsive width-100">
                                                </a>
                                            {/block}
                                            {block name='displayHotelRoomsBlockImageAfter'}
                                                {hook h='displayHotelRoomsBlockImageAfter' room_type=$roomDisplay}
                                            {/block}
                                            <div class="hotelRoomDescContainer">
                                                {block name='hotel_room_block_room_type_description'}
                                                    <div class="row margin-lr-0">
                                                        <p class="htlRoomTypeNameText pull-left">{$roomDisplay.name|escape:'htmlall':'UTF-8'}</p>
                                                        {if $roomDisplay.show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
                                                            <p class="htlRoomTypePriceText pull-right">
                                                                {if $roomDisplay.feature_price_diff >= 0}
                                                                    <span class="wk_roomType_price {if $roomDisplay.feature_price_diff>0}room_type_old_price{/if}">{convertPrice price = $roomDisplay.price_without_reduction}</span>
                                                                {/if}
                                                                {if $roomDisplay.feature_price_diff}
                                                                    <span class="wk_roomType_price">{convertPrice price = $roomDisplay.feature_price}</span>
                                                                {/if}
                                                                <span class="wk_roomType_price_type">
                                                                    /&nbsp;{l s='Per Night' mod='wkhotelroom'}
                                                                </span>
                                                            </p>
                                                        {/if}
                                                    </div>
                                                    <div class="row margin-lr-0 htlRoomTypeDescText htlRoomTypeDescTextContainer">{$roomDisplay.description|escape:'html':'UTF-8'}</div>
                                                    <div class="row htlRoomTypeDescOriginal" hidden>{$roomDisplay.description|escape:'html':'UTF-8'}</div>
                                                    <div class="htlRoomTypeDescExtras"><span class='htlRoomTypeDescReadmore'>...{l s='Read More.' mod='wkhotelroom'}</span><span class='htlRoomTypeDescReadless'>...{l s='Read Less.' mod='wkhotelroom'}</span></div>
                                                {/block}
                                                {block name='hotel_room_block_action'}
                                                    <div class="row margin-lr-0">
                                                        <a class="btn htlRoomTypeBookNow" href="{$link->getProductLink($roomDisplay.id_product)|escape:'html':'UTF-8'}"><span>{if !isset($restricted_country_mode) && !$PS_CATALOG_MODE}{l s='book now' mod='wkhotelroom'}{else}{l s='View' mod='wkhotelroom'}{/if}</span></a>
                                                    </div>
                                                {/block}
                                            </div>
                                        </div>
                                {if !($smarty.foreach.htlRoom.iteration%2)}
                                    </div>
                                {/if}
                                {assign var='htlRoomBlockIteration' value=$smarty.foreach.htlRoom.iteration}
                            {/foreach}
                            {if $htlRoomBlockIteration%2}
                                </div>
                            {/if}
                        </div>
                    </div>
                {/block}
            </div>
            <hr class="home_block_seperator"/>
        </div>
    {/if}
{/block}
