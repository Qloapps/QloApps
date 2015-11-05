<hr class="gap-line">
<div class="slideview">
    <!-- Slides -->
    <div class="slideview-content">
        {assign var=val value=1}
        {if isset($testimonials_data) && $testimonials_data} 
            {foreach $testimonials_data as $data}
                <div class="slide slide-{$val}">
                    <article class="container">
                        <div id="testimonial_block" class="row">
                            <div class="hotel_testimonial_heading">
                                <p>{l s='Guest Testimonials' mod='wktestimonialblock'}</p>
                            </div>
                            <div class="hotel_testimonial_content">
                                <p>{$data.testimonial_description} </p>
                            </div>
                            <div class="testimonial_container">
                                <div class="testimonial_content row">
                                    <div class='col-sm-3'>
                                        <img src="{$module_dir}views/img/icon-double-codes.png" class="img-responsive">
                                    </div>
                                    <div class='col-sm-6 testimonial_block_content_container'>
                                        <p class="testi_block_content">{$data.testimonial_content}</p>
                                    </div>
                                    <div class='col-sm-3'></div>
                                </div>
                                <div class="row person_image">
                                    <div class="col-sm-5"></div>
                                    <div class="col-sm-1">
                                        {if isset($data.testimonial_image) && $data.testimonial_image}
                                            <img height="85px" width="85px" src="{$module_dir}views/img/{$data.testimonial_image}">
                                        {else}
                                            <img height="85px" width="85px" src="{$module_dir}views/img/default.png">
                                        {/if}
                                    </div>
                                    <div class="col-sm-3 text-left">
                                        <p class="testimonial_person_name">{$data.name}</p>
                                        <p class="testimonial_person_desig">{$data.designation}</p>
                                    </div>
                                    <div class="col-sm-3"></div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
                {assign var=val value=$val+1}
            {/foreach}
        {/if} 
    </div>
    <!-- <a href="http://www.jqueryscript.net/slider/">Slider</a> controls -->
    <a class="slideview-button slideview-prev" aria-label="Previous"></a> 
    <a class="slideview-button slideview-next" aria-label="Next"></a>
    <div class="slideview-pagination"></div>
</div>

<script>
  $(document).ready(function(){
    $(".slideview").slideview({
      nextButton: '.slideview-next',
      prevButton: '.slideview-prev'
    });
  });
</script>