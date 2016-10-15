<div id="hotelAmenitiesBlock" class="row home_block_container">
    <div class="col-xs-12 col-sm-12 home_amenities_wrapper">
        <div class="row home_block_desc_wrapper">
            <div class="col-md-offset-1 col-md-10 col-lg-offset-2 col-lg-8">
                <p class="home_block_heading">{$HOTEL_AMENITIES_HEADING}</p>
                <p class="home_block_description">{$HOTEL_AMENITIES_DESCRIPTION}</p>
                <hr class="home_block_desc_line"/>
            </div>
        </div>
        {if $hotelAmenities}
            <div class="homeAmenitiesBlock home_block_content">
                {assign var='amenityPosition' value=0}
                {assign var='amenityIteration' value=0}
                {foreach from=$hotelAmenities item=amenity name=amenityBlock}
                    {if $smarty.foreach.amenityBlock.iteration%2 != 0}
                        <div class="row margin-lr-0">
                        {if $amenityPosition}
                            {assign var='amenityPosition' value=0}
                        {else}
                            {assign var='amenityPosition' value=1}
                        {/if}
                    {/if}
                            <div class="col-md-6 padding-lr-0 hidden-xs hidden-sm">
                                <div class="row margin-lr-0 amenity_content">
                                    {if $amenityPosition}
                                        <div class="col-md-6 padding-lr-0">
                                            <div class="amenity_img_primary">
                                                <div class="amenity_img_secondary" style="background-image: url('{$module_dir}views/img/hotels_features_img/{$amenity.feature_image}')">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 padding-lr-0 amenity_desc_cont">
                                            <div class="amenity_desc_primary">
                                                <div class="amenity_desc_secondary">
                                                    <p class="amenity_heading">{$amenity['feature_title']}</p>
                                                    <p class="amenity_description">{$amenity['feature_description']}</p>
                                                    <hr class="amenity_desc_hr" />
                                                </div>
                                            </div>
                                        </div>
                                    {else}
                                        <div class="col-md-6 padding-lr-0 amenity_desc_cont">
                                            <div class="amenity_desc_primary">
                                                <div class="amenity_desc_secondary">
                                                    <p class="amenity_heading">{$amenity['feature_title']}</p>
                                                    <p class="amenity_description">{$amenity['feature_description']}</p>
                                                    <hr class="amenity_desc_hr" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 padding-lr-0">
                                            <div class="amenity_img_primary">
                                                <div class="amenity_img_secondary" style="background-image: url('{$module_dir}views/img/hotels_features_img/{$amenity.feature_image}')">
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
                                </div>
                            </div>
                            <div class="col-sm-12 padding-lr-0 visible-sm">
                                <div class="row margin-lr-0 amenity_content">
                                    {if $smarty.foreach.amenityBlock.iteration%2 != 0}
                                        <div class="col-sm-6 padding-lr-0">
                                            <div class="amenity_img_primary">
                                                <div class="amenity_img_secondary" style="background-image: url('{$module_dir}views/img/hotels_features_img/{$amenity.feature_image}')">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 padding-lr-0 amenity_desc_cont">
                                            <div class="amenity_desc_primary">
                                                <div class="amenity_desc_secondary">
                                                    <p class="amenity_heading">{$amenity['feature_title']}</p>
                                                    <p class="amenity_description">{$amenity['feature_description']}</p>
                                                    <hr class="amenity_desc_hr" />
                                                </div>
                                            </div>
                                        </div>
                                    {else}
                                        <div class="col-sm-6 padding-lr-0 amenity_desc_cont">
                                            <div class="amenity_desc_primary">
                                                <div class="amenity_desc_secondary">
                                                    <p class="amenity_heading">{$amenity['feature_title']}</p>
                                                    <p class="amenity_description">{$amenity['feature_description']}</p>
                                                    <hr class="amenity_desc_hr" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 padding-lr-0">
                                            <div class="amenity_img_primary">
                                                <div class="amenity_img_secondary" style="background-image: url('{$module_dir}views/img/hotels_features_img/{$amenity.feature_image}')">
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
                                </div>
                            </div>
                            <div class="col-xs-12 padding-lr-0 visible-xs">
                                <div class="row margin-lr-0 amenity_content">
                                    <div class="col-xs-12 padding-lr-0">
                                        <div class="amenity_img_primary">
                                            <div class="amenity_img_secondary" style="background-image: url('{$module_dir}views/img/hotels_features_img/{$amenity.feature_image}')">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 padding-lr-0 amenity_desc_cont">
                                        <div class="amenity_desc_primary">
                                            <div class="amenity_desc_secondary">
                                                <p class="amenity_heading">{$amenity['feature_title']}</p>
                                                <p class="amenity_description">{$amenity['feature_description']}</p>
                                                <hr class="amenity_desc_hr" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    {if $smarty.foreach.amenityBlock.iteration%2 == 0}
                        </div>
                    {/if}
                    {assign var='amenityIteration' value=$smarty.foreach.amenityBlock.iteration}
                {/foreach}
                {if $amenityIteration%2}
                    </div>
                {/if}
            </div>
        {/if}
    </div>
    <hr class="home_block_seperator"/>
</div>