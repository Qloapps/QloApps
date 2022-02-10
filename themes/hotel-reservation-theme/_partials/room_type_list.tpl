{if isset($booking_data['rm_data']) && $booking_data['rm_data']}
	{foreach from=$booking_data['rm_data'] key=room_k item=room_v}
		<div class="col-sm-12 room_cont">
			<div class="row">
				<div class="col-sm-4">
					<a href="{$room_v['product_link']}">
						<img src="{$room_v['image']}" class="img-responsive">
						{hook h='displayRoomTypeListImageAfter' product=$room_v}
					</a>
				</div>
				<div class="col-sm-8">
					<p class="rm_heading">{$room_v['name']}</p>
					<div class="rm_desc">{$room_v['description']}&nbsp;<a href="{$room_v['product_link']}">{l s='View More'}....</a></div>

					<p><span class="capa_txt">{l s='Max Capacity:'}</span><span class="capa_data"> {$room_v['adult']} {l s='Adults'}, {$room_v['children']} {l s='child'}</span></p>
					{if isset($room_v['num_review'])}
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
					{/if}

					{if !isset($restricted_country_mode) && !$PS_CATALOG_MODE && !$order_date_restrict}
						<span class="rm_left pull-right" {if $room_v['room_left']>$warning_num}  style="display:none"{/if}>{l s='Hurry!'} <span class="cat_remain_rm_qty_{$room_v['id_product']}">{$room_v['room_left']}</span> {l s='rooms left'}</span>
					{/if}

					{if !empty($room_v['feature'])}
						<div class="rm_amenities_cont">
							{foreach from=$room_v['feature'] key=feat_k item=feat_v}
								<img title="{$feat_v.name}" src="{$link->getMediaLink("`$feat_img_dir`{$feat_v.value}")}" class="rm_amen">  {* by webkul change meddia link*}
								{* <img src="{$feat_img_dir}{$feat_v['value']}" class="rm_amen"> *}
							{/foreach}
						</div>
					{/if}
					<div class="row margin-lr-0 pull-left rm_price_cont">
						{if $room_v['feature_price_diff'] >= 0}
							<span class="pull-left rm_price_val {if $room_v['feature_price_diff']>0}room_type_old_price{/if}">
								{displayPrice price = $room_v['price_without_reduction']|round:2|floatVal}
							</span>
						{/if}
						{if $room_v['feature_price_diff']}
							<span class="pull-left rm_price_val">
								{displayPrice price = $room_v['feature_price']|round:2|floatVal}
							</span>
						{/if}
						<span class="pull-left rm_price_txt">/{l s='Per Night'}</span>
					</div>

					{if !isset($restricted_country_mode) && !$PS_CATALOG_MODE && !$order_date_restrict}
						<a cat_rm_check_in="{$booking_date_from}" cat_rm_check_out="{$booking_date_to}" href="" rm_product_id="{$room_v['id_product']}" cat_rm_book_nm_days="{$num_days}" data-id-product-attribute="0" data-id-product="{$room_v['id_product']|intval}" class="btn btn-default button button-medium ajax_add_to_cart_button pull-right"><span>{l s='Book Now'}</span></a>

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
					{/if}


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
{else}
	<div class="noRoomsAvailAlert">
		<span>{l s='No room available for this hotel!'}</span>
	</div>
{/if}
