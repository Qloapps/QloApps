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

{block name='hotel_display_block'}
    {if isset($hotelInfoData) && $hotelInfoData}
        <div class="container">
            <div id="hotelDisplayBlock" class="home_block_container">
                <div class="col-12">
                    {if $HOTEL_BLOCK_DISPLAY_HEADING && $HOTEL_BLOCK_DISPLAY_DESCRIPTION}
                        <div class="home_block_desc_wrapper">
                            <div>
                                {block name='hotel_block_heading'}
                                    <div class="home_block_heading">{$HOTEL_BLOCK_DISPLAY_HEADING|escape:'htmlall':'UTF-8'}</div>
                                {/block}
                                {block name='hotel_block_description'}
                                    <div class="block_description">
                                        {$HOTEL_BLOCK_DISPLAY_DESCRIPTION|escape:'htmlall':'UTF-8'}
                                        <hr class="block_desc_line"/>
                                    </div>
                                {/block}
                                {block name='displayblockExtraContent'}
                                    {hook h="displayblockExtraContent"}
                                {/block}
                            </div>
                        </div>
                    {/if}
                    {block name='hotel_block_hotel_info_container'}
                        <div class="home_block_content htlDisplayBlock-owlCarousel">
                            <div>
                                <div class="owl-carousel owl-theme owl-loaded">
                                    {foreach from=$hotelInfoData item=hotelInfo}
                                        <div class="col-12 hotel_info_container">
                                            <div class="hotel_info_block">
                                                <div class="hotel_info_block_image" data-fancybox-group="hotelBlockGallery" rel="hotelBlockGallery" href="{$hotelInfo['image_link']}" title="{$hotelInfo['hotel_name']|escape:'htmlall':'UTF-8'}">
                                                    <img src="{$hotelInfo['image_link']}" class="hotelInfoData" alt="{$hotelInfo['hotel_name']|escape:'htmlall':'UTF-8'}">
                                                </div>
                                                {block name='hotel_block_hotel_info'}
                                                    <div class="hotel_info_block_content">
                                                        <div>
                                                            <div class="hotel_block_hotel_name home_block_heading">
                                                                {$hotelInfo['hotel_name']|escape:'htmlall':'UTF-8'}
                                                            </div>
                                                            <div class="hotel_block_hotel_desc">
                                                                {$hotelInfo['short_description']|escape:'htmlall':'UTF-8'}
                                                            </div>
                                                            <div class="hotel_block_hotel_action">
                                                            {* @todo: add hotel page link *}
                                                                <a class="btn" href="">{l s='Explore More' mod='hotelreservationsystem'}</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/block}
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
