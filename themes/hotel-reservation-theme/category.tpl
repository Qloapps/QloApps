{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{assign var=gallery_total value=$hotel_gallery_all_images|@count}
{assign var=thumb_count value=$hotel_gallery_images|@count}
{block name='category'}
    {block name='category_hotel_gallery'}
        {if isset($hotel_cover_image) && $hotel_cover_image}
            <div class="qlo-category-gallery {if $thumb_count == 0}gallery--single {elseif $thumb_count == 1}gallery--two{/if}">
                <div class="qlo-category-gallery__cover">
                    <a href="{$hotel_cover_image.large_link}" class="js-hotel-image"><img src="{$hotel_cover_image.large_link}" alt="{$objHotel->hotel_name}" loading="lazy"/></a>
                </div>
                {if isset($hotel_gallery_images) && $hotel_gallery_images}
                <div class="qlo-category-gallery__grid">
                    {foreach from=$hotel_gallery_images item=hotel_image name=gallery}
                    <div class="qlo-category-gallery__thumb">
                        <a href="{$hotel_image.large_link}" class="js-hotel-image"><img src="{$hotel_image.large_link}" loading="lazy"/></a>
                        {if $smarty.foreach.gallery.last}
                            <a href="javascript:void(0);" class="qlo-category-gallery__full-gallery js-category-full-gallery-trigger"><span>{l s='View Full Gallery'}</span><i class="icon-long-arrow-right"></i></a>
                        {/if}
                    </div>
                    {/foreach}
                </div>
                {/if}
            </div>
            <div class="mobile-hotel-gallery">
                <div class="owl-carousel owl-theme">
                    {foreach from=$hotel_gallery_all_images item=image}
                        <div class="item">
                            <a href="{$image.large_link}" class="js-hotel-image"><img src="{$image.large_link}"></a>
                        </div>
                    {/foreach}
                </div>
                <div class="gallery-counter">
                    <span class="current">01</span>/<span class="total">{$gallery_total|string_format:"%02d"}</span>
                </div>
            </div>
        {/if}

        {block name='full_gallery_modal_block'}
            <div class="qlo-full-gallery-modal" id="full-gallery-modal" style="display: none;">
                <div class="qlo-full-gallery__overlay"></div>
                <div class="qlo-full-gallery__container">
                    <button class="qlo-full-gallery__close" type="button"><i class="icon-times"></i></button>
                    
                    {* Hotel Name Header *}
                    <div class="qlo-full-gallery__header m-3"><h2 class="qlo-full-gallery__hotel-name">{$objHotel->hotel_name}</h2></div>
                    
                    {* Category Tabs - Centered *}
                    <div class="qlo-full-gallery__tabs">
                        <div class="qlo-full-gallery__tabs-wrapper">
                            <button class="qlo-gallery-tab active" data-category="all">{l s='All photos'}</button>
                            {foreach from=$hotel_image_categories item=category}
                                <button class="qlo-gallery-tab" data-category="{$category.id_htl_image_category}">{$category.name}</button>
                            {/foreach}
                        </div>
                    </div>
                    
                    {* Main Image Display - Desktop *}
                    <div class="qlo-full-gallery__main qlo-full-gallery__main--desktop">
                        <button class="qlo-full-gallery__nav qlo-full-gallery__nav--prev" type="button"><i class="icon-chevron-left"></i></button>
                        <div class="qlo-full-gallery__image-container"><img src="" alt="" class="qlo-full-gallery__main-image" id="main-image-modal"></div>
                        <button class="qlo-full-gallery__nav qlo-full-gallery__nav--next" type="button"><i class="icon-chevron-right"></i></button>
                    </div>
                    
                    {* Mobile Owl Carousel *}
                    <div class="qlo-full-gallery__main qlo-full-gallery__main--mobile">
                        <div class="owl-carousel owl-theme" id="gallery-mobile-carousel"></div>
                    </div>
                    
                    {* Thumbnails Strip - Desktop *}
                    <div class="qlo-full-gallery__thumbnails qlo-full-gallery__thumbnails--desktop">
                        <div class="qlo-gallery-thumbnails__scroll" id="gallery-thumbnail"></div>
                    </div>
                </div>
            </div>
        {/block}
    {/block}
    <div class="cat_cont container p-0">
        <div class="col-12">
            <div class="category_info_block row">
                <div class="col-12 col-md-12 pr-md-3">
                    <div class="category_heading">
                        <span class="block_title">
                            {$objHotel->hotel_name}
                        </span>
                        {if isset($hotel_address) && $hotel_address != ''}
                            <span class="d-block">
                            <i class="icon-location-dot"></i>
                                {$hotel_address}
                            </span>
                        {/if}
                        <span>
                            <i class="icon-envelope"></i>
                            {$objHotel->email}
                        </span>
                        {if isset($hotel_contact) && $hotel_contact != ''}
                            <span class="seprator-pipe">
                            <i class="icon-phone"></i>
                                {$hotel_contact}
                            </span>
                        {/if}
                        <span class="seprator-pipe">
                            <span id="hotel_rating">{for $i=0; $i < $objHotel->rating; $i++}<i class="icon-star"></i>{/for}{for $i=$objHotel->rating; $i < 5; $i++}<i class="icon-star text-grey"></i>{/for}</span>
                        </span>
                    </div>
                    <div class="category_sub_info">
                        {* todo: reviews block *}
                        {*apply qlo-border-class where you want to add default qloapps border *}
                        <div class="col-md-6 qlo-border-class">
                            <div class="category-meta">
                                <div class="category-meta-item">
                                    <span class="category-meta-label">{l s='Check-in'}</span>
                                    <span class="category-meta-value">{$objHotel->check_in}</span>
                                </div>
                                <div class="category-meta-item">
                                    <span class="category-meta-label">{l s='Check-out'}</span>
                                    <span class="category-meta-value">{$objHotel->check_out}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {block name='category_hotel_info_google_map'}
                    {* Map is rendered inside the Location section/tab when enabled *}
                {/block}
            </div>
            {block name='category_tabs'}
                <div class="tab_container">
                    <ul class="nav nav-pills tab_list_headings">
                        <li class="nav-item"><a href="#category_info_tab" data-toggle="tab" class="idTabHrefShort nav-pill active">{l s='Overview'}</a></li>
                        {* <li class="nav-item"><a href="#hotel_amenities" data-toggle="tab" class="idTabHrefShort nav-pill">{l s='Room Types'}</a></li> *}
                        <li class="nav-item"><a href="#room_types_list_tab" data-toggle="tab" class="idTabHrefShort nav-pill">{l s='Room Types'}</a></li>
                        {if isset($display_hotel_location) && $display_hotel_location}
                            <li class="nav-item"><a href="#location_tab" data-toggle="tab" class="idTabHrefShort nav-pill">{l s='Location'}</a></li>
                        {/if}
                        <li class="nav-item"><a href="#about_us_tab" data-toggle="tab" class="idTabHrefShort nav-pill">{l s='About us'}</a></li>
                        <li class="nav-item"><a href="#policies_tab" data-toggle="tab" class="idTabHrefShort nav-pill">{l s='Policies'}</a></li>
                        {* <li class="nav-item"><a href="#review_tab" data-toggle="tab" class="idTabHrefShort nav-pill">{l s='Review'}</a></li> *}
                        {block name='displayCategoryTab'}
                            {hook h='displayCategoryTab'}
                        {/block}
                    </ul>
                </div>
            {/block}
            {block name='category_tabs_content'}
                <div class="tab-content">
                    {block name='category_info_tab_content'}
                        <div id="category_info_tab" class=" active">
                            <div id="category_info_tab_content">
                                {block name='category_info_description'}
                                    <div class="category_description_heading block_heading">
                                        {$objHotel->hotel_name}
                                    </div>
                                    <div class="row info_margin_div category_description_content">
                                        <div class="col-12">
                                            {$objHotel->description}
                                        </div>
                                    </div>
                                {/block}
                            </div>
                        </div>
                        <hr class="block_seperator"/>
                    {/block}
                    {block name='room_types_list_tab'}
                        <div id="room_types_list_tab">
                            <div class="room_type_section_heading block_heading">
                                {l s='Room Types'}
                            </div>
                            <input type="hidden" id="max_order_date" name="max_order_date" value="{$max_order_date}">
                            <div id="room_type_list_filters" class="col-12">
                                {block name='room_type_list_filters'}
                                    {include file="./_partials/room_type_list_filters.tpl"}
                                {/block}
                            </div>
                            <div id="category_data_cont">
                                {block name='room_type_list'}
                                    {include file="./_partials/room_type_list.tpl"}
                                {/block}
                            </div>
                        </div>
                    {/block}
                    <hr class="block_seperator"/>
                    {block name='location_tab'}
                        {if isset($display_hotel_location) && $display_hotel_location}
                            <div id="location_tab">
                                <div class="block_heading">
                                    <span>{l s='Location'}</span>
                                </div>
                                <div class="qlo-border-class w-auto p-0">
                                    <div class="map-wrap"></div>
                                </div>
                            </div>
                            <hr class="block_seperator"/>
                        {/if}
                    {/block}
                </div>
            {/block}
        </div>
    </div>

    {block name='category_js_vars'}
        {strip}
            {addJsDef product_controller_url=$link->getPageLink('product')}
            {* {addJsDef feat_img_dir=$feat_img_dir} *}
            {* {addJsDef ratting_img=$ratting_img} *}
            {addJsDef currency_prefix = $currency->prefix}
            {addJsDef currency_suffix = $currency->suffix}
            {if isset($max_order_date)}
                {addJsDef max_order_date = $max_order_date}
            {/if}
            {if isset($hotel_gallery_by_category)}
                {addJsDef hotelGalleryByCategory = $hotel_gallery_by_category}
            {/if}
        {/strip}
    {/block}
{/block}
