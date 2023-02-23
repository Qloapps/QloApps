{**
* 2010-2023 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2023 Webkul IN
* @license LICENSE.txt
*}

{if isset($booking_data['rm_data']) && $booking_data['rm_data']}
	{foreach from=$booking_data['rm_data'] key=room_k item=room_v}
		<div class="col-sm-12 room_cont" data-id-product="{$room_v['id_product']|escape:'htmlall':'UTF-8'}">
			<div class="row">
				<div class="col-sm-4">
						<a href="{$room_v['product_link']|escape:'htmlall':'UTF-8'}">
						<img src="{$room_v['image']|escape:'htmlall':'UTF-8'}" class="img-responsive room-type-image">
						{hook h='displayRoomTypeListImageAfter' product=$room_v}
					</a>
				</div>
				<div class="col-sm-8 room_info_cont">
					<div class="row">
						<p class="rm_heading col-sm-12 col-md-7">{$room_v['name']|escape:'htmlall':'UTF-8'}</p>
						{if !isset($restricted_country_mode) && !$PS_CATALOG_MODE && !$order_date_restrict}
							<p class="rm_left col-sm-12 col-md-5" {if $room_v['room_left']>$warning_num}  style="display:none"{/if}>
								{l s='Hurry!'} <span class="remain_rm_qty">{$room_v['room_left']|escape:'htmlall':'UTF-8'}</span> {l s='rooms left'}
							</p>
						{/if}
					</div>
					<div class="rm_desc">{$room_v['description']|truncate:190:"":true}&nbsp;<a href="{$room_v['product_link']|escape:'htmlall':'UTF-8'}">{l s='View More'}....</a></div>
					<div class="room_features_cont">
						<div class="row">
							<div class="col-sm-12 col-md-5 col-lg-6">
								{if !empty($room_v['feature'])}
									<p class="rm_amenities_cont">
										{foreach from=$room_v['feature'] key=feat_k item=feat_v}
											<img title="{$feat_v.name|escape:'htmlall':'UTF-8'}" src="{$link->getMediaLink("`$feat_img_dir`{$feat_v.value}")|escape:'htmlall':'UTF-8'}" class="rm_amen">
										{/foreach}
									</p>
								{/if}
							</div>
							<div class="col-sm-12 hidden-md hidden-lg">
								<p class="capa_txt"><span>{l s='Max Capacity:'}</span><span class="capa_data"> {$room_v['max_adults']|escape:'htmlall':'UTF-8'} {l s='Adults'}, {$room_v['max_children']|escape:'htmlall':'UTF-8'} {l s='child'}</span></p>
							</div>
							<div class="col-sm-12 col-md-7 col-lg-6">
								{if !isset($restricted_country_mode) && !$PS_CATALOG_MODE && !$order_date_restrict}

									<p class="rm_price_cont">
										{if $room_v['feature_price_diff'] >= 0}
											<span class="rm_price_val {if $room_v['feature_price_diff']>0}room_type_old_price{/if}">
												{displayPrice price = $room_v['price_without_reduction']|round:2|floatVal}
											</span>
										{/if}
										{if $room_v['feature_price_diff']}
											<span class="rm_price_val">
												{displayPrice price = $room_v['feature_price']|round:2|floatVal}
											</span>
										{/if}
										<span class="rm_price_txt">/{l s='Per Night'}</span>
									</p>
								{/if}
							</div>
							<div class="col-md-4 col-lg-3 visible-md visible-lg">
								<div class="capa_txt"><span>{l s='Max Capacity:'}</span><br><span class="capa_data"> {$room_v['max_adults']|escape:'htmlall':'UTF-8'} {l s='Adults'}, {$room_v['max_children']|escape:'htmlall':'UTF-8'} {l s='child'}</span></div>
							</div>
							<div class="col-sm-12 col-md-8 col-lg-9">
								<div class="booking_room_fields">
									{if !isset($restricted_country_mode) && !$PS_CATALOG_MODE && !$order_date_restrict}
										{if isset($occupancy_required_for_booking) && $occupancy_required_for_booking}
											<div class="booking_guest_occupancy_conatiner">
												{include file="./occupancy_field.tpl" room_type_info=$room_v total_available_rooms=$room_v['room_left']}
											</div>
										{else}
											<div>
												<label>{l s='Qty:'}</label>
												{include file="./quantity_field.tpl" total_available_rooms=$room_v['room_left']}
											</div>
										{/if}
										<div>
											<a cat_rm_check_in="{$booking_date_from|escape:'htmlall':'UTF-8'}" cat_rm_check_out="{$booking_date_to|escape:'htmlall':'UTF-8'}" href="" rm_product_id="{$room_v['id_product']}" cat_rm_book_nm_days="{$num_days|escape:'htmlall':'UTF-8'}" data-id-product-attribute="0" data-id-product="{$room_v['id_product']|intval}" class="btn btn-default button button-medium ajax_add_to_cart_button"><span>{l s='Book Now'}</span></a>
										</div>
									{/if}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	{/foreach}
{else}
	<div class="noRoomsAvailAlert">
		<span>{l s='No room available for this hotel!'}</span>
	</div>
{/if}
