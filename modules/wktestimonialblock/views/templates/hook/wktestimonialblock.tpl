<div id="hotelTestimonialBlock" class="row home_block_container">
    <div class="col-xs-12 col-sm-12">
        <div class="row home_block_desc_wrapper">
            <div class="col-md-offset-1 col-md-10 col-lg-offset-2 col-lg-8">
                <p class="home_block_heading">{$HOTEL_TESIMONIAL_BLOCK_HEADING}</p>
                <p class="home_block_description">{$HOTEL_TESIMONIAL_BLOCK_CONTENT}</p>
                <hr class="home_block_desc_line"/>
            </div>
        </div>
        {if $testimonials_data}
            <div class="row home_block_content htlTestemonial-owlCarousel">
                <div class="col-sm-12 col-xs-12">
                    <div class="owl-carousel">
                        {foreach $testimonials_data as $tesimonial}
                            <div class="row">
                                <div class='col-xs-4 col-sm-offset-1 col-sm-2'>
                                    <img src="{$module_dir}views/img/icon-double-codes.png" class="img-responsive">
                                </div>
                                <div class='col-xs-12 col-sm-7'>
                                    <div class="row">
                                        <div class="col-sm-12 testimonialContentContainer">
                                            <p class="testimonialContentText">{$tesimonial.testimonial_content}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 testimonialPersonDetail">
                                            {if isset($tesimonial.testimonial_image) && $tesimonial.testimonial_image}
                                                <img src="{$module_dir}views/img/hotels_testimonials_img/{$tesimonial.testimonial_image}" class="testimonialPersonImg">
                                            {/if}
                                            <p class="testimonialPersonName">{$tesimonial.name}</p>
                                            <p class="testimonialPersonDesig">{$tesimonial.designation}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        {/if}
    </div>
</div>
