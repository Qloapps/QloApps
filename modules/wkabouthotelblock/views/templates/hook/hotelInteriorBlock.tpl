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

{block name='hotel_interior_block'}
    {if isset($InteriorImg) && $InteriorImg}
        <div class="container">
            <div id="hotelInteriorBlock" class="home_block_container">
                <div class="col-12">
                    {if $HOTEL_INTERIOR_HEADING && $HOTEL_INTERIOR_DESCRIPTION}
                        <div class="home_block_desc_wrapper">
                            <div>
                                {block name='hotel_interior_block_heading'}
                                    <div class="home_block_heading">{$HOTEL_INTERIOR_HEADING|escape:'htmlall':'UTF-8'}</div>
                                {/block}
                                {block name='hotel_interior_block_description'}
                                    <div class="block_description">
                                        {$HOTEL_INTERIOR_DESCRIPTION|escape:'htmlall':'UTF-8'}
                                        <hr class="block_desc_line"/>
                                    </div>
                                {/block}
                                {block name='displayInteriorExtraContent'}
                                    {hook h="displayInteriorExtraContent"}
                                {/block}
                            </div>
                        </div>
                    {/if}
                    {block name='hotel_interior_images'}
                        <div class="home_block_content htlInterior-owlCarousel row">
                            <div class="col-12">
                                <div class="owl-carousel owl-theme owl-loaded">
                                    {assign var='intImgIteration' value=0}
                                    {foreach from=$InteriorImg item=img_name name=intImg}
                                        {if $smarty.foreach.intImg.iteration%2 == 1}
                                            <div class="interiorImgWrapper justify-content-between">
                                        {/if}
                                            <div class="interiorbox" data-fancybox-group="interiorGallery" rel="interiorGallery" href="{$link->getMediaLink("`$module_dir|escape:'htmlall':'UTF-8'`views/img/hotel_interior/`$img_name['name']|escape:'htmlall':'UTF-8'`.jpg")}" title="{$img_name['display_name']|escape:'htmlall':'UTF-8'}">
                                                <div class="interiorboxInner">
                                                    <img width="275px" height="250px" src="{$link->getMediaLink("`$module_dir|escape:'htmlall':'UTF-8'`views/img/hotel_interior/`$img_name['name']|escape:'htmlall':'UTF-8'`.jpg")}" class="interiorImg" alt="{$img_name['display_name']|escape:'htmlall':'UTF-8'}">
                                                </div>
                                                <div class="interiorHoverBlockWrapper">
                                                    <div class="interiorHoverPrimaryBlock">
                                                        <div class="interiorHoverSecondaryBlock">
                                                            <i class="icon-search-plus"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        {if $smarty.foreach.intImg.iteration%2 == 0}
                                            </div>
                                        {/if}
                                        {assign var='intImgIteration' value=$smarty.foreach.intImg.iteration}
                                    {/foreach}

                                    {if $intImgIteration%2}
                                            <div class="interiorbox">
                                                <div class="interiorboxInner">
                                                    <img width="275px" height="250px" src="{$link->getMediaLink("`$module_dir|escape:'htmlall':'UTF-8'`views/img/Default-Image.png")}" class="interiorImg" alt="Default Image">
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
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
