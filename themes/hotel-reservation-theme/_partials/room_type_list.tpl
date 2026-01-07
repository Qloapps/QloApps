{**
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

{block name='displayRoomTypeListBefore'}
	{hook h='displayRoomTypeListBefore'}
{/block}
{if !empty($booking_data['rm_data']) && (isset($booking_data['stats']) && $booking_data['stats']['num_avail'] || !empty($display_all_room_types))}
	{foreach from=$booking_data['rm_data'] key=room_k item=room_v}
		{if $room_v['data']['available']|count || !empty($display_all_room_types) }
			<div class="room_category_card card" data-id-product="{$room_v['id_product']|escape:'htmlall':'UTF-8'}">
				<div class="row col-12">
					{block name='room_type_list_room_image'}
						<div class="col-sm-4 col-lg-3 room_image_cont">
								<a href="{$room_v['product_link']|escape:'htmlall':'UTF-8'}">
								<img src="{$room_v['image']|escape:'htmlall':'UTF-8'}" class="img-responsive room-type-image">
								{block name='displayRoomTypeListImageAfter'}
									{hook h='displayRoomTypeListImageAfter' product=$room_v}
								{/block}
							</a>
						</div>
					{/block}
					{block name='room_type_list_room_detail'}
						<div class="col-sm-8 col-lg-9 room_info_cont">
							{block name='room_type_list_heading'}
								<div class="row">
									<div class="rm_heading col-sm-12 col-md-7 col-lg-6">{$room_v['name']|escape:'htmlall':'UTF-8'}</div>
									{block name='room_type_list_room_price'}
										<div class="col-sm-12 col-md-7 col-lg-6">
											{if !isset($restricted_country_mode) && !$PS_CATALOG_MODE && !$order_date_restrict}
												<p class="rm_price_cont">
													{if $room_v['feature_price_diff'] >= 0}
														<span class="rm_price_val {if $room_v['feature_price_diff']>0}room_type_old_price{/if}">
															{displayPrice price = $room_v['price_without_reduction']|floatVal}/
														</span>
													{/if}
													{if $room_v['feature_price_diff']}
														<span class="rm_price_val">
															{displayPrice price = $room_v['feature_price']|floatVal}/
														</span>
													{/if}
													<span class="rm_price_txt">{l s='Per Night'}</span>
												</p>
											{/if}
										</div>
									{/block}
								</div>
							{/block}
							{block name='room_type_list_subheading'}
								<div class="help_block">
									<div><span>{$room_v['max_guests']|escape:'htmlall':'UTF-8'} {l s='Max guests:'}</span><span class="capa_data"> {$room_v['max_adults']|escape:'htmlall':'UTF-8'} {l s='Adults'}, {$room_v['max_children']|escape:'htmlall':'UTF-8'} {if $room_v['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}</span></div>
								</div>
							{/block}
							{block name='room_type_list_room_description'}
								<div class="rm_desc">{$room_v['description_short']|truncate:190:"":true}&nbsp;<a class="view_more" href="{$room_v['product_link']|escape:'htmlall':'UTF-8'}">{l s='View More'}....</a></div>
							{/block}
							<div class="room_features_cont">
								<div class="row">
									{block name='room_type_list_room_features'}
										<div class="col-sm-12">
											{if !empty($room_v['feature'])}
												<div class="amenity_wrapper">
													{foreach from=$room_v['feature'] key=feat_k item=feat_v}
														<div class="rm_ftr_wrapper amenity" title="{$feat_v.name|escape:'html':'UTF-8'}" alt="{$feat_v.name|escape:'html':'UTF-8'}" >
															<span>
																<img title="{$feat_v.name|escape:'htmlall':'UTF-8'}" src="{$link->getMediaLink("`$feat_img_dir`{$feat_v.value}")|escape:'htmlall':'UTF-8'}" class="rm_amen">
															</span>
															<span>{$feat_v.name|escape:'html':'UTF-8'}</span>
														</div>
													{/foreach}
												</div>
											{/if}
										</div>
									{/block}
								</div>
							</div>
							{block name="room_type_list_bottom"}
								{if (!isset($display_all_room_types) || !$display_all_room_types)}
									<div class="room_type_list_bottom">
										{block name='room_type_list_room_quantity'}
											<div class="remain_rm_qty">
												{if !isset($restricted_country_mode) && !$PS_CATALOG_MODE && !$order_date_restrict}
													{if $room_v['room_left'] <= $warning_num}
														{l s='Hurry!'} <span>{$room_v['room_left']|escape:'htmlall':'UTF-8'}</span> {l s='rooms left'}
													{/if}
												{/if}
											</div>
										{/block}
										{block name='room_type_list_room_booking_fields'}
											<div class="float-right room_type_list_actions">
												{if !isset($restricted_country_mode) && !$PS_CATALOG_MODE && !$order_date_restrict}
													<div class="booking_room_fields">
														{if isset($occupancy_required_for_booking) && $occupancy_required_for_booking}
															<div class="booking_guest_occupancy_conatiner">
																{block name='occupancy_field'}
																	{include file="./occupancy_field.tpl" room_type_info=$room_v total_available_rooms=$room_v['room_left'] show_occupancy_info=false}
																{/block}
															</div>
														{else}
															<div>
																<label>{l s='Qty:'}</label>
																{block name='quantity_field'}
																	{include file="./quantity_field.tpl" total_available_rooms=$room_v['room_left']}
																{/block}
															</div>
														{/if}
														{block name='room_type_list_room_book_now_button'}
															<a cat_rm_check_in="{$booking_date_from|escape:'htmlall':'UTF-8'}" cat_rm_check_out="{$booking_date_to|escape:'htmlall':'UTF-8'}" href="" rm_product_id="{$room_v['id_product']}" cat_rm_book_nm_days="{$num_days|escape:'htmlall':'UTF-8'}" data-id-product-attribute="0" data-id-product="{$room_v['id_product']|intval}" class="btn btn-primary ajax_add_to_cart_button"><span>{l s='Book Now'}</span></a>
														{/block}
													</div>
												{/if}
												</div>
											</div>
										{/block}
									</div>
								{/if}
							{/block}
					{/block}
				</div>
			</div>
		{/if}
	{/foreach}
{else}
	<div class="noRoomsAvailAlert">
		<span>{l s='No room available for this hotel!'}</span>
	</div>
{/if}

{block name='displayRoomTypeListAfter'}
	{hook h='displayRoomTypeListAfter'}
{/block}
