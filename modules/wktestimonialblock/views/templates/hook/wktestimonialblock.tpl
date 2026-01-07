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

{block name='testimonial_block'}
    {if isset($testimonials_data) && $testimonials_data}
        <div class="container">
            <div id="hotelTestimonialBlock" class="home_block_container">
                <div class="col-12">
                    {if $HOTEL_TESIMONIAL_BLOCK_HEADING && $HOTEL_TESIMONIAL_BLOCK_CONTENT}
                        <div class="home_block_desc_wrapper">
                            <div>
                                {block name='testimonial_block_heading'}
                                    <p class="home_block_heading">{$HOTEL_TESIMONIAL_BLOCK_HEADING|escape:'htmlall':'UTF-8'}</p>
                                {/block}
                                {block name='testimonial_block_description'}
                                    <div class="block_description">
                                        {$HOTEL_TESIMONIAL_BLOCK_CONTENT|escape:'htmlall':'UTF-8'}
                                        <hr class="block_desc_line"/>
                                    </div>
                                {/block}
                            </div>
                        </div>
                    {/if}
                    {block name='testimonial_block_content'}
                        <div class="home_block_content htlTestemonial-owlCarousel container">
                            <div class="testimonial_container">
                                <div class="testimonial_cards_container">
                                    <div class="owl-carousel owl-theme owl-loaded">
                                        {foreach $testimonials_data as $tesimonial}
                                            <div class="col-12">
                                                <div class="col-12 testimonial_card">
                                                    <div class="row pt-4">
                                                        <div class="testimonial_content col-12">
                                                            <div class="testimonial_content_quote">
                                                                <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/icon-double-codes.png" class="img-responsive">
                                                            </div>
                                                            <div class="testimonial_content_comment">{$tesimonial.testimonial_content|escape:'htmlall':'UTF-8'|truncate:270:"...":true}</div>
                                                        </div>
                                                    </div>
                                                    <div class="row col-12 testimonial_card_user_info">
                                                        <div class="testimonial_card_user_icon">
                                                            <span class="btn-secondary">{$tesimonial.name|escape:'htmlall':'UTF-8'|truncate:1:"":true}</span>
                                                        </div>
                                                        <div class="testimonialPersonDetail">
                                                            <div class="testimonialPersonName">{$tesimonial.name|escape:'htmlall':'UTF-8'}</div>
                                                            <div class="testimonialPersonDesig">{$tesimonial.designation|escape:'htmlall':'UTF-8'}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        {/foreach}
                                    </div>
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
