<hr class="gap-line">

<div class="row" id="testimonial_block">
    <div class="col-xs-12 col-sm-12">
        <div class="hotel_testimonial_heading">
            <p>{$parent_testimonial_data.testimonial_heading} </p>
        </div>
        <div class="hotel_testimonial_content">
            <p>{$parent_testimonial_data.testimonial_description} </p>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12">
        <div class="span12">
            <div id="owl-demo" class="owl-carousel">
                {if isset($testimonials_data) && $testimonials_data}
                    {foreach $testimonials_data as $data}
                        <div>
                            <div class="row margin-lr-0 testimonial_container">
                                
                                <!-- Hide for xs screen -->
                                <div class="row testimonial_content hidden-xs">
                                    <div class='col-sm-offset-1 col-sm-2'>
                                        <img src="{$module_dir}views/img/icon-double-codes.png" class="img-responsive">
                                    </div>
                                    <div class='col-sm-7 col-md-6 margin-top-70'>
                                        <p class="testi_block_content">{$data.testimonial_content}</p>
                                    </div>
                                </div>

                                <!-- Visible for xs screen -->
                                <div class="row margin-lr-0 testimonial_content visible-xs">
                                    <div class="row">
                                        <div class='col-xs-3'>
                                            <img src="{$module_dir}views/img/icon-double-codes.png" class="img-responsive">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class='col-xs-12'>
                                            <p class="testi_block_content">{$data.testimonial_content}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12">
                                        {if isset($data.testimonial_image) && $data.testimonial_image}
                                            <img src="{$module_dir}views/img/{$data.testimonial_image}" class="testimonial_person_img">
                                        {/if}
                                        <p class="testimonial_person_name">{$data.name}</p>
                                        <p class="testimonial_person_desig">{$data.designation}</p>
                                    </div>
                                    <!-- <div class="col-sm-offset-5 col-sm-1">
                                        {if isset($data.testimonial_image) && $data.testimonial_image}
                                            <img height="85px" width="85px" src="{$module_dir}views/img/{$data.testimonial_image}">
                                        {else}
                                            <img height="85px" width="85px" src="{$module_dir}views/img/default.png">
                                        {/if}
                                    </div>
                                    <div class="col-sm-3 text-left">
                                        <p class="testimonial_person_name">{$data.name}</p>
                                        <p class="testimonial_person_desig">{$data.designation}</p>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    {/foreach}
                {/if}
            </div>
        </div>
    </div>
</div>

<style>
    #owl-demo .owl-item div
    {
        padding:5px;
    }
</style>

<script>
    $(document).ready(function()
    {
        $("#owl-demo").owlCarousel({
            autoPlay : 5000,
            stopOnHover : true,
            paginationSpeed : 1000,
            goToFirstSpeed : 2000,
            singleItem : true,
            autoHeight : true,
            pagination :true,
            navigation:false,
            // transitionStyle:"fade"
        });
    });
</script>