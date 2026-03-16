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
        <div id="hotelRoomsBlock" class="home_block_container container">
            <div class="row">
                <div class="col-12">
                    {if $HOTEL_ROOM_DISPLAY_HEADING && $HOTEL_ROOM_DISPLAY_DESCRIPTION}
                        <div class="home_block_desc_wrapper">
                            {block name='hotel_room_block_heading'}
                                <div class="home_block_heading">{$HOTEL_ROOM_DISPLAY_HEADING|escape:'htmlall':'UTF-8'}</div>
                            {/block}
                            {block name='hotel_room_block_description'}
                                <div class="block_description">
                                    {$HOTEL_ROOM_DISPLAY_DESCRIPTION|escape:'htmlall':'UTF-8'}
                                    <hr class="block_desc_line"/>
                                </div>
                            {/block}
                        </div>
                    {/if}
                    {block name='hotel_room_block_content'}
                        <div class="row home_block_content">
                            <div class="col-12">
                                <div class="owl-carousel owl-theme owl-loaded">
                                    {foreach from=$hotelRoomDisplay item=roomDisplay name=htlRoom}
                                        <div class="col-12">
                                            <div class="qlo-card p-0">
                                                {block name='hotel_room_block_room_type_image'}
                                                    <div class="room_card_room_type_image">
                                                        <a href="{$link->getProductLink($roomDisplay.id_product)|escape:'html':'UTF-8'}">
                                                            <img class="img-fluid" src="{$roomDisplay.image|escape:'htmlall':'UTF-8'}" alt="{$roomDisplay.name|escape:'htmlall':'UTF-8'}">
                                                        </a>
                                                        {block name='hotel_room_block_room_type_price'}
                                                            {if $roomDisplay.show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
                                                                <div class="room_card_room_type_price_text">
                                                                    {if $roomDisplay.feature_price_diff >= 0}
                                                                        <span class="room_block_room_type_price {if $roomDisplay.feature_price_diff>0}room_type_old_price{/if}">{convertPrice price = $roomDisplay.price_without_reduction}</span>
                                                                    {/if}
                                                                    {if $roomDisplay.feature_price_diff}
                                                                        <span class="room_block_room_type_price">{convertPrice price = $roomDisplay.feature_price}</span>
                                                                    {/if}
                                                                    <span class="room_block_room_type_price_type">
                                                                        /&nbsp;{l s='Per Night' mod='wkhotelroom'}
                                                                    </span>
                                                                </div>
                                                            {/if}
                                                        {/block}
                                                    </div>
                                                {/block}
                                                {block name='displayHotelRoomsBlockImageAfter'}
                                                    {hook h='displayHotelRoomsBlockImageAfter' room_type=$roomDisplay}
                                                {/block}
                                                <div class="room_card_room_type_name_info_container">
                                                    {block name='hotel_room_block_room_type_name'}
                                                        <div class="room_card_room_type_name_text">{$roomDisplay.name|escape:'htmlall':'UTF-8'}</div>
                                                        <div class="room_card_room_type_features">
                                                            {if !empty($roomDisplay.features)}
                                                                {foreach $roomDisplay.features as $roomFeature}
                                                                    <div class="rm_ftr_wrapper" title="{$roomFeature.name|escape:'html':'UTF-8'}" alt="{$roomFeature.name|escape:'html':'UTF-8'}" >
                                                                        <img height="24px" width="24px" src="{$link->getMediaLink("`$ftr_img_src|escape:'html':'UTF-8'`{$roomFeature.value|escape:'html':'UTF-8'}")}">
                                                                    </div>
                                                                {/foreach}
                                                            {/if}
                                                        </div>
                                                    {/block}
                                                    {block name='hotel_room_block_action'}
                                                        <a class="btn room_card_action" href="{$link->getProductLink($roomDisplay.id_product)|escape:'html':'UTF-8'}"><span>{if !isset($restricted_country_mode) && !$PS_CATALOG_MODE}{l s='Book Now' mod='wkhotelroom'}{else}{l s='View' mod='wkhotelroom'}{/if}</span></a>
                                                    {/block}
                                                </div>
                                            </div>
                                        </div>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                    {/block}
                </div>
            </div>
        </div>
        <hr class="block_seperator"/>
    {/if}
{/block}
