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

{block name='hotel_features_block'}
    {if isset($hotelAmenities) && $hotelAmenities}
        <div id="hotelAmenitiesBlock" class=" home_block_container col-12">
            <div class="row">
                <div class="col-12 home_amenities_wrapper">
                    {if $HOTEL_AMENITIES_HEADING && $HOTEL_AMENITIES_DESCRIPTION}
                        <div class="home_block_desc_wrapper">
                            {block name='hotel_features_block_heading'}
                                <div class="home_block_heading">{$HOTEL_AMENITIES_HEADING|escape:'htmlall':'UTF-8'}</div>
                            {/block}
                            {block name='hotel_features_block_description'}
                                <div class="block_description">
                                    {$HOTEL_AMENITIES_DESCRIPTION|escape:'htmlall':'UTF-8'}
                                    <hr class="block_desc_line"/>
                                </div>
                            {/block}
                        </div>
                    {/if}
                    {block name='hotel_features_images'}
                        <div class="homeAmenitiesBlock home_block_content">
                            {assign var='amenityPosition' value=0}
                            {assign var='amenityIteration' value=0}
                            {foreach from=$hotelAmenities item=amenity name=amenityBlock}
                                {if $smarty.foreach.amenityBlock.iteration%2 == 1}
                                    <div class="row">
                                    {assign var='amenityPosition' value=(!$amenityPosition)}
                                {/if}

                                {assign var='imageUrl' value={$link->getMediaLink("`$module_dir|escape:'htmlall':'UTF-8'`views/img/hotels_features_img/`$amenity.id_features_block|escape:'htmlall':'UTF-8'`.jpg")}}
                                <div class="homeAmenitiesBlockContainer col-md-12 col-lg-6 {if !$amenityPosition}flex-lg-row-reverse{/if} {if $smarty.foreach.amenityBlock.iteration%2 == 0}flex-sm-row-reverse flex-lg-row{/if}">
                                    <div class="col-md-6 p-0">
                                        <div class="amenity_img_primary">
                                            <div class="amenity_img_secondary" style="background-image: url('{$imageUrl}')">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 amenity_desc_cont">
                                        <div class="amenity_desc_primary">
                                            <div class="amenity_desc_secondary">
                                                <div class="mb-1 h8 font-weight-bold">{$amenity['feature_title']|escape:'htmlall':'UTF-8'}</div>
                                                <div class="amenity_description p-2">{$amenity['feature_description']|escape:'htmlall':'UTF-8'}</div>
                                                <hr class="block_desc_line" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {if $smarty.foreach.amenityBlock.iteration%2 == 0}
                                    </div>
                                {/if}
                                {assign var='amenityIteration' value=$smarty.foreach.intImg.iteration}
                            {/foreach}
                            {if !$amenityIteration%2}
                                <div>
                                </div>
                            {/if}
                        </div>
                    {/block}
                </div>
            </div>
        </div>
        <hr class="block_seperator"/>
    {/if}
{/block}
