<div id="hotelInteriorBlock" class="row home_block_container">
    <div class="col-xs-12 col-sm-12">
        <div class="row home_block_desc_wrapper">
            <div class="col-md-offset-1 col-md-10 col-lg-offset-2 col-lg-8">
                <p class="home_block_heading">{$HOTEL_INTERIOR_HEADING}</p>
                <p class="home_block_description">{$HOTEL_INTERIOR_DESCRIPTION}</p>
                <hr class="home_block_desc_line"/>
            </div>
        </div>
        {if $InteriorImg}
            <div class="row home_block_content htlInterior-owlCarousel">
                <div class="col-sm-12 col-xs-12">
                    <div class="owl-carousel owl-theme">
                        {assign var='intImgIteration' value=0}
                        {foreach from=$InteriorImg item=img_name name=intImg}
                            {if $smarty.foreach.intImg.iteration%3 == 1}
                            <div class="interiorImgWrapper">
                            {/if}
                                <div class="interiorbox" data-fancybox-group="interiorGallery" rel="interiorGallery" href="{$module_dir}views/img/hotel_interior/{$img_name['name']}" title="{$img_name['display_name']}">
                                    <div class="interiorboxInner">
                                        <img src="{$module_dir}views/img/hotel_interior/{$img_name['name']}" class="interiorImg" alt="{$img_name['display_name']}">
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
                                        <img src="{$module_dir}views/img/Default-Image.png" class="interiorImg" alt="Default Image">
                                    </div>
                                </div>
                            {/for}
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        {/if}
    </div>
    <hr class="home_block_seperator"/>
</div>