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
                    <div class="row home_block_content htlRoom-owlCarousel">
                        <div class="col-sm-12 col-xs-12">
                            <div class="owl-carousel owl-theme">
                                {foreach from=$hotelRoomDisplay item=roomDisplay name=htlRoom}
                                    <div class="htlRoomCard">
                                        {block name='hotel_room_block_room_type_image'}
                                            <a class="htlRoomCardImg" href="{$link->getProductLink($roomDisplay.id_product)|escape:'html':'UTF-8'}">
                                                <img src="{$roomDisplay.image|escape:'htmlall':'UTF-8'}" alt="{$roomDisplay.name|escape:'htmlall':'UTF-8'}">
                                            </a>
                                        {/block}
                                        {block name='displayHotelRoomsBlockImageAfter'}
                                            {hook h='displayHotelRoomsBlockImageAfter' room_type=$roomDisplay}
                                        {/block}
                                        <div class="htlRoomCardBody">
                                            {block name='hotel_room_block_room_type_description'}
                                                <p class="htlRoomTypeNameText">{$roomDisplay.name|escape:'htmlall':'UTF-8'}</p>
                                                {if $roomDisplay.feature}
                                                    <div class="htlRoomCardFeatured">
                                                        {foreach from=$roomDisplay.feature item=feat}
                                                            <span title="{$feat.name|escape:'htmlall':'UTF-8'}">
                                                                    {if $feat.logo_type == 'icon'}<i class="{$feat.logo|escape:'htmlall':'UTF-8'}"></i>{else}<img class="htlRoomFeatureImg" src="{$smarty.const._PS_IMG_}rf/{$feat.logo_image|escape:'htmlall':'UTF-8'}" alt="{$feat.name|escape:'htmlall':'UTF-8'}" onerror="this.style.display='none'">{/if}
                                                                    {$feat.value|escape:'htmlall':'UTF-8'}
                                                                </span>
                                                        {/foreach}
                                                    </div>
                                                {/if}
                                                <div class="htlRoomCardMeta">
                                                    <span class="htlRoomCardGuests"><i class="icon-group"></i> {$roomDisplay.max_adults|intval} {l s='Adults' mod='wkhotelroom'}{if $roomDisplay.max_children}, {$roomDisplay.max_children|intval} {if $roomDisplay.max_children > 1}{l s='Children' mod='wkhotelroom'}{else}{l s='Child' mod='wkhotelroom'}{/if}{/if}</span>
                                                    {if $roomDisplay.amenities}
                                                        <span class="htlRoomCardAmen">
                                                            {foreach from=$roomDisplay.amenities item=amenity}
                                                                {if $amenity.logo}
                                                                    <span title="{$amenity.name|escape:'htmlall':'UTF-8'}">
                                                                        {if $amenity.logo_type == 'icon'}<i class="{$amenity.logo|escape:'htmlall':'UTF-8'}"></i>{else}<img class="htlRoomAmenityImg" src="{$amenity_img_dir|escape:'htmlall':'UTF-8'}{$amenity.logo|escape:'htmlall':'UTF-8'}" alt="{$amenity.name|escape:'htmlall':'UTF-8'}">{/if}
                                                                    </span>
                                                                {/if}
                                                            {/foreach}
                                                        </span>
                                                    {/if}
                                                </div>
                                            {/block}
                                            <div class="htlRoomCardFoot">
                                                {if $roomDisplay.show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
                                                    <p class="htlRoomTypePriceText">
                                                        {if $roomDisplay.feature_price_diff >= 0}
                                                            <span class="wk_roomType_price {if $roomDisplay.feature_price_diff>0}room_type_old_price{/if}">{convertPrice price = $roomDisplay.price_without_reduction}</span>
                                                        {/if}
                                                        {if $roomDisplay.feature_price_diff}
                                                            <span class="wk_roomType_price">{convertPrice price = $roomDisplay.feature_price}</span>
                                                        {/if}
                                                        <span class="wk_roomType_price_type">/&nbsp;{l s='night' mod='wkhotelroom'}</span>
                                                    </p>
                                                {/if}
                                                {block name='hotel_room_block_action'}
                                                    <a class="btn htlRoomTypeBookNow" href="{$link->getProductLink($roomDisplay.id_product)|escape:'html':'UTF-8'}"><span>{if !isset($restricted_country_mode) && !$PS_CATALOG_MODE}{l s='Book now' mod='wkhotelroom'}{else}{l s='View' mod='wkhotelroom'}{/if}</span></a>
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
    {/if}
{/block}
