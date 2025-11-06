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
        <div id="hotelInteriorBlock" class="row home_block_container">
            <div class="col-xs-12 col-sm-12">
                {if $HOTEL_INTERIOR_HEADING && $HOTEL_INTERIOR_DESCRIPTION}
                    <div class="row home_block_desc_wrapper">
                        <div class="col-md-offset-1 col-md-10 col-lg-offset-2 col-lg-8">
                            {block name='hotel_interior_block_heading'}
                                <p class="home_block_heading">{$HOTEL_INTERIOR_HEADING|escape:'htmlall':'UTF-8'}</p>
                            {/block}
                            {block name='hotel_interior_block_description'}
                                <p class="home_block_description">{$HOTEL_INTERIOR_DESCRIPTION|escape:'htmlall':'UTF-8'}</p>
                            {/block}
                            {block name='displayInteriorExtraContent'}
                                {hook h="displayInteriorExtraContent"}
                            {/block}
                            <hr class="home_block_desc_line"/>
                        </div>
                    </div>
                {/if}
                {block name='hotel_interior_images'}
                    <div class="row home_block_content htlInterior-owlCarousel">
                        <div class="col-sm-12 col-xs-12">
                            <div class="owl-carousel owl-theme">
                                {assign var='intImgIteration' value=0}
                                {foreach from=$InteriorImg item=img_name name=intImg}
                                    {if $smarty.foreach.intImg.iteration%3 == 1}
                                    <div class="interiorImgWrapper">
                                    {/if}
                                        <div class="interiorbox" data-fancybox-group="interiorGallery" rel="interiorGallery" href="{$link->getMediaLink("`$module_dir|escape:'htmlall':'UTF-8'`views/img/hotel_interior/`$img_name['name']|escape:'htmlall':'UTF-8'`.jpg")}" title="{$img_name['display_name']|escape:'htmlall':'UTF-8'}">
                                            <div class="interiorboxInner">
                                                <img src="{$link->getMediaLink("`$module_dir|escape:'htmlall':'UTF-8'`views/img/hotel_interior/`$img_name['name']|escape:'htmlall':'UTF-8'`.jpg")}" class="interiorImg" alt="{$img_name['display_name']|escape:'htmlall':'UTF-8'}">
                                            </div>
                                            <div class="interiorHoverBlockWrapper">
                                                <div class="interiorHoverPrimaryBlock">
                                                    <div class="interiorHoverSecondaryBlock">
                                                        <i class="icon-search-plus"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    {if $smarty.foreach.intImg.iteration%3 == 0}
                                    </div>
                                    {/if}
                                    {assign var='intImgIteration' value=$smarty.foreach.intImg.iteration}
                                {/foreach}
                                {if $intImgIteration%3}
                                    {assign var='intImgLeft' value=3-($intImgIteration%3)}
                                    {for $foo=1 to $intImgLeft}
                                        <div class="interiorbox">
                                            <div class="interiorboxInner">
                                                <img src="{$link->getMediaLink("`$module_dir|escape:'htmlall':'UTF-8'`views/img/Default-Image.png")}" class="interiorImg" alt="Default Image">
                                            </div>
                                        </div>
                                    {/for}
                                    </div>
                                {/if}
                            </div>
                        </div>
                    </div>
                {/block}
            </div>
            <hr class="home_block_seperator"/>
        </div>
    {/if}
{/block}
