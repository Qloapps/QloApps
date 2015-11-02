<div class="row cat_cont">
    <div class="col-sm-12">
        <div class="row margin-lr-0 top_filter_cont">
            <div class="col-sm-2 sort_by">
                <p>{l s='Sort By:'}</p>
            </div>
            <div class="col-sm-3">
                <div class="filter_dw_cont">
                    <button class="btn btn-default dropdown-toggle" type="button" id="gst_rating" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="pull-left sort_btn_span" data-sort-by="0" data-sort-value="0" data-sort-for="{l s='Ratting'}">{l s='Ratting'}</span>
                        <span class="caret pull-right margin-top-7"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="gst_rating">
                        <li><a href="#" class="sort_result" data-sort-by="1" data-value="1">{l s='Ratting Ascending'}</a></li>
                        <li><a href="#" class="sort_result" data-sort-by="1" data-value="2">{l s='Ratting Descending'}</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="filter_dw_cont">
                    <button class="btn btn-default dropdown-toggle" type="button" id="price_ftr" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="pull-left sort_btn_span" data-sort-by="0" data-sort-value="0" data-sort-for="{l s='Price'}">{l s='Price'}</span>
                        <span class="caret pull-right margin-top-7"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="price_ftr">
                        <li><a href="#" class="sort_result" data-sort-by="2" data-value="1">{l s='Price : Lowest First'}</a></li>
                        <li><a href="#" class="sort_result" data-sort-by="2" data-value="2">{l s='Price : Highest first '}</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row margin-lr-0" id="category_data_cont">
            {foreach from=$booking_data['rm_data'] key=room_k item=room_v}
                <div class="col-sm-12 room_cont">
                    <div class="row">
                        <div class="col-sm-4">
                            <a href="{$room_v['product_link']}">
                            <img src="{$room_v['image']}" class="img-responsive">
                            </a>
                        </div>
                        <div class="col-sm-8">
                            <p class="rm_heading">{$room_v['name']}</p>
                            <div class="rm_desc">{$room_v['description']}&nbsp;<a href="{$room_v['product_link']}">{l s='View More'}....</a></div>

                            <p><span class="capa_txt">{l s='Max Capacity:'}</span><span class="capa_data"> {$room_v['adult']} {l s='Adults'}, {$room_v['children']} {l s='child'}</span></p>

                            <div class="rm_review_cont pull-left">
                                {for $foo=1 to 5}
                                    {if $foo <= $room_v['ratting']}
                                        <div class="rm_ratting_yes" style="background-image:url({$ratting_img});"></div>
                                    {else}
                                        <div class="rm_ratting_no" style="background-image:url({$ratting_img});"></div>
                                    {/if}
                                {/for}
                                <span class="rm_review">{$room_v['num_review']} {l s='Reviews'}</span>
                            </div>

                            {if isset($room_v['room_left'])}
                                <span class="rm_left pull-right">{l s='Hurry!'} <span class="cat_remain_rm_qty_{$room_v['id_product']}">{$room_v['room_left']}</span> {l s='rooms left'}</span>
                            {/if}

                            {if !empty($room_v['feature'])}
                                <div class="rm_amenities_cont">
                                    {foreach from=$room_v['feature'] key=feat_k item=feat_v}
                                        <img src="{$feat_img_dir}{$feat_v['value']}" class="rm_amen">
                                    {/foreach}
                                </div>
                            {/if}
                            <div class="row margin-lr-0 pull-left rm_price_cont">
                                <span class="pull-left rm_price_val">{convertPrice price=$room_v['price']}</span><span class="pull-left rm_price_txt">/{l s='Per Night'}</span>
                            </div>

                            <a cat_rm_check_in="{$booking_date_from}" cat_rm_check_out="{$booking_date_to}" href="" rm_product_id="{$room_v['id_product']}" cat_rm_book_nm_days="{$num_days}" class="btn rm_book_btn pull-right">{l s='Book Now'}</a>

                            <!-- ################################################ -->

                            <div class="rm_qty_cont pull-right clearfix" id="cat_rm_quantity_wanted_{$room_v['id_product']}">
                                <span class="qty_txt">{l s='Qty.'}:</span>
                                <div class="qty_sec_cont row">
                                    <div class="qty_input_cont row margin-lr-0">
                                        <input autocomplete="off" type="text" min="1" name="qty_{$room_v['id_product']}" id="cat_quantity_wanted_{$room_v['id_product']}" class="text-center form-control cat_quantity_wanted" value="1" id_room_product="{$room_v['id_product']}">
                                    </div>
                                    <div class="qty_direction">
                                        <a href="#" data-room_id_product="{$room_v['id_product']}" data-field-qty="qty_{$room_v['id_product']}" class="btn btn-default cat_rm_quantity_up">
                                            <span>
                                                <i class="icon-plus"></i>
                                            </span>
                                        </a>
                                        <a href="#" data-field-qty="qty_{$room_v['id_product']}" class="btn btn-default cat_rm_quantity_down">
                                            <span>
                                                <i class="icon-minus"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            

                            <!-- <div id="cat_rm_quantity_wanted_{$room_v['id_product']}">
                                <a href="#" data-field-qty="qty_{$room_v['id_product']}" class="btn btn-default button-minus cat_rm_quantity_down">
                                    <span>
                                        <i class="icon-minus"></i>
                                    </span>
                                </a>
                                <input autocomplete="off" type="text" min="1" name="qty_{$room_v['id_product']}" id="cat_quantity_wanted_{$room_v['id_product']}" class="text" value="1" />
                                
                                <a href="#" data-room_id_product="{$room_v['id_product']}" data-field-qty="qty_{$room_v['id_product']}" class="btn btn-default button-plus cat_rm_quantity_up">
                                    <span><i class="icon-plus"></i></span>
                                </a>
                            </div> -->
                            

                            <!-- ################################################ -->
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</div>
{strip}
    {addJsDef product_controller_url=$link->getPageLink('product')}
    {addJsDef feat_img_dir=$feat_img_dir}
    {addJsDef ratting_img=$ratting_img}
    {addJsDef currency_prefix = $currency->prefix}
    {addJsDef currency_suffix = $currency->suffix}

{/strip}