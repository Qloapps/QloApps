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
        <div id="hotelTestimonialBlock" class="row home_block_container">
            <div class="col-xs-12 col-sm-12">
                {if $HOTEL_TESIMONIAL_BLOCK_HEADING && $HOTEL_TESIMONIAL_BLOCK_CONTENT}
                    <div class="row home_block_desc_wrapper">
                        <div class="col-md-offset-1 col-md-10 col-lg-offset-2 col-lg-8">
                            {block name='testimonial_block_heading'}
                                <p class="home_block_heading">{$HOTEL_TESIMONIAL_BLOCK_HEADING|escape:'htmlall':'UTF-8'}</p>
                            {/block}
                            {block name='testimonial_block_description'}
                                <p class="home_block_description">{$HOTEL_TESIMONIAL_BLOCK_CONTENT|escape:'htmlall':'UTF-8'}</p>
                            {/block}
                            <hr class="home_block_desc_line"/>
                        </div>
                    </div>
                {/if}
                {block name='testimonial_block_content'}
                    <div class="row home_block_content htlTestemonial-owlCarousel">
                        <div class="col-sm-12 col-xs-12">
                            <div class="owl-carousel">
                                {foreach $testimonials_data as $tesimonial}
                                    <div class="row">
                                        <div class='col-xs-4 col-sm-offset-1 col-sm-2'>
                                            <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/icon-double-codes.png" class="img-responsive">
                                        </div>
                                        <div class='col-xs-12 col-sm-7'>
                                            <div class="row">
                                                <div class="col-sm-12 testimonialContentContainer">
                                                    <p class="testimonialContentText">{$tesimonial.testimonial_content|escape:'htmlall':'UTF-8'}</p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12 testimonialPersonDetail">
                                                    <img width="60px" src="{$tesimonial.img_url}" class="testimonialPersonImg">
                                                    <p class="testimonialPersonName">{$tesimonial.name|escape:'htmlall':'UTF-8'}</p>
                                                    <p class="testimonialPersonDesig">{$tesimonial.designation|escape:'htmlall':'UTF-8'}</p>
                                                </div>
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
