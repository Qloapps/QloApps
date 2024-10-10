{*
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($testimonials_data) && $testimonials_data}
    <div id="hotelTestimonialBlock" class="row home_block_container">
        <div class="col-xs-12 col-sm-12">
            {if $HOTEL_TESIMONIAL_BLOCK_HEADING && $HOTEL_TESIMONIAL_BLOCK_CONTENT}
                <div class="row home_block_desc_wrapper">
                    <div class="col-md-offset-1 col-md-10 col-lg-offset-2 col-lg-8">
                        <p class="home_block_heading">{$HOTEL_TESIMONIAL_BLOCK_HEADING|escape:'htmlall':'UTF-8'}</p>
                        <p class="home_block_description">{$HOTEL_TESIMONIAL_BLOCK_CONTENT|escape:'htmlall':'UTF-8'}</p>
                        <hr class="home_block_desc_line"/>
                    </div>
                </div>
            {/if}
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
        </div>
    </div>
{/if}
