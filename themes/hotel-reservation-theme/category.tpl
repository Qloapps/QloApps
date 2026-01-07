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

{block name='category'}
    {* todo: add hotel images *}
    <div class="cat_cont container p-0">
        <div class="col-12">
            <div class="category_info_block">
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
                <div class="">
                </div>
            </div>
            {block name='category_tabs'}
                <div class="tab_container">
                    <ul class="nav nav-pills tab_list_headings">
                        <li class="nav-item"><a href="#category_info_tab" data-toggle="tab" class="idTabHrefShort nav-pill active">{l s='Overview'}</a></li>
                        {* <li class="nav-item"><a href="#hotel_amenities" data-toggle="tab" class="idTabHrefShort nav-pill">{l s='Room Types'}</a></li> *}
                        <li class="nav-item"><a href="#room_types_list_tab" data-toggle="tab" class="idTabHrefShort nav-pill">{l s='Room Types'}</a></li>
                        <li class="nav-item"><a href="#location_tab" data-toggle="tab" class="idTabHrefShort nav-pill">{l s='Location'}</a></li>
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
                            <div id="room_type_list_filters" class="col-12 row">
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
        {/strip}
    {/block}
{/block}
