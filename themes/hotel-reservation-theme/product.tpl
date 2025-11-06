{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{block name='product'}
	{block name='errors'}
		{include file="$tpl_dir./errors.tpl"}
	{/block}
	{if $errors|@count == 0}
		{if !isset($priceDisplayPrecision)}
			{assign var='priceDisplayPrecision' value=2}
		{/if}
		{if !$priceDisplay || $priceDisplay == 2}
			{assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, 6)}
			{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
		{elseif $priceDisplay == 1}
			{assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, 6)}
			{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
		{/if}
	<div class="product_wrapper" itemscope itemtype="http://schema.org/Product">
		{if isset($product) && $product}
			<meta itemprop="url" content="{$link->getProductLink($product)}">
			<div class="primary_block row">
				<!-- {if !$content_only}
					<div class="container">
						<div class="top-hr"></div>
					</div>
				{/if} --><!-- by webkul -->
				{if isset($adminActionDisplay) && $adminActionDisplay}
					<div id="admin-action" class="container">
						<p class="alert alert-info">{l s='This room type is not visible to your customers.'}
							<input type="hidden" id="admin-action-product-id" value="{$product->id}" />
							<a id="publish_button" class="btn btn-default button button-small" href="#">
								<span>{l s='Publish'}</span>
							</a>
							<a id="lnk_view" class="btn btn-default button button-small" href="#">
								<span>{l s='Back'}</span>
							</a>
						</p>
						<p id="admin-action-result"></p>
					</div>
				{/if}
				{if isset($confirmation) && $confirmation}
					<p class="confirmation">
						{$confirmation}
					</p>
				{/if}
				<!-- left infos-->
				{block name='product_left_column'}
					<div class="pb-left-column col-xs-12 col-sm-8 col-md-8">
						<div class="room_type_img_containter card">
							<div class="room_hotel_name_block {if isset($language_is_rtl) && $language_is_rtl}rtl{/if}">
								{block name='product_name'}
									<div class="hotel_name_block">
										<h1><span class="hotel_name">{$product->name}
											{* Block for booking products *}
											{if isset($id_hotel) && $id_hotel}&nbsp;-&nbsp;{$hotel_name}{/if}</span>{if isset($hotel_rating) && $hotel_rating}<div id="hotel_rating">{for $i=0; $i < $hotel_rating; $i++}<i class="icon-star"></i>{/for}</div>{/if}
										</h1>
										{block name='displayRoomTypeDetailRoomTypeNameBlock'}
											{hook h='displayRoomTypeDetailRoomTypeNameBlock' id_product=$product->id}
										{/block}
									</div>
									{block name='displayRoomTypeDetailRoomTypeNameAfter'}
										{hook h='displayRoomTypeDetailRoomTypeNameAfter' product=$product id_product=$product->id}
									{/block}
								{/block}
							</div>
							{block name='displayRoomTypeDetailRoomTypeImageBlockBefore'}
								{hook h='displayRoomTypeDetailRoomTypeImageBlockBefore' id_product=$product->id}
							{/block}
							<!-- product img-->
							{block name='product_images'}
								<div class="row">

									{block name='product_cover_image'}
										<div id="image-block-cont" class="col-xs-12 col-sm-9 col-sm-push-3 col-md-10 col-md-push-2">
											<div id="image-block" class="clearfix">
												<!-- {if $product->new}
													<span class="new-box">
														<span class="new-label">{l s='New'}</span>
													</span>
												{/if} -->
												{if $product->on_sale}
													<span class="sale-box no-print">
														<span class="sale-label">{l s='Sale!'}</span>
													</span>
												{elseif $product->specificPrice && $product->specificPrice.reduction && $productPriceWithoutReduction > $productPrice}
													<span class="discount">{l s='Reduced price!'}</span>
												{/if}
												{if $have_image}
													<span id="view_full_size">
														{if $jqZoomEnabled && $have_image && !$content_only}
															<a class="jqzoom" title="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}" rel="gal1" href="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'thickbox_default')|escape:'html':'UTF-8'}">
																<img itemprop="image" src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large_default')|escape:'html':'UTF-8'}" title="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}" alt="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}"/>
															</a>
														{else}
															<img id="bigpic" itemprop="image" src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large_default')|escape:'html':'UTF-8'}" title="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}" alt="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}" width="{$largeSize.width}" height="{$largeSize.height}"/>
															<!-- {if !$content_only}
																<span class="span_link no-print">{l s='View larger'}</span>
															{/if} -->
														{/if}
													</span>
												{else}
													<span id="view_full_size">
														<img itemprop="image" src="{$img_prod_dir}{$lang_iso}-default-large_default.jpg" id="bigpic" alt="" title="{$product->name|escape:'html':'UTF-8'}" width="{$largeSize.width}" height="{$largeSize.height}"/>
														{* {if !$content_only}
															<span class="span_link">
																{l s='View larger'}
															</span>
														{/if} *}
													</span>
												{/if}
												{block name='displayRoomTypeImageAfter'}
													{hook h="displayRoomTypeImageAfter"}
												{/block}
											</div> <!-- end image-block -->
										</div>
									{/block}
									{block name='product_thumbnails'}
										<div class="col-xs-12 col-sm-3 col-sm-pull-9 col-md-2 col-md-pull-10">
											{if isset($images) && count($images) > 0}
												<!-- thumbnails -->
													<div id="views_block" class="clearfix {if isset($images) && count($images) < 2}hidden{/if}">
														{if isset($images) && count($images) > 2}
															{* <span class="view_scroll_spacer"> *}
																<a id="view_scroll_left" class="" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">
																	{l s='Previous'}
																</a>
															{* </span> *}
														{/if}
														<div id="thumbs_list">
															<ul id="thumbs_list_frame">
															{if isset($images)}
																{foreach from=$images item=image name=thumbnails}
																	{assign var=imageIds value="`$product->id`-`$image.id_image`"}
																	{if !empty($image.legend)}
																		{assign var=imageTitle value=$image.legend|escape:'html':'UTF-8'}
																	{else}
																		{assign var=imageTitle value=$product->name|escape:'html':'UTF-8'}
																	{/if}
																	<li id="thumbnail_{$image.id_image}"{if $smarty.foreach.thumbnails.last} class="last"{/if}>
																		<a {if $jqZoomEnabled && $have_image && !$content_only} href="javascript:void(0);" rel="{ldelim}gallery: 'gal1', smallimage: '{$link->getImageLink($product->link_rewrite, $imageIds, 'large_default')|escape:'html':'UTF-8'}',largeimage: '{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html':'UTF-8'}'{rdelim}"{else} href="{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')|escape:'html':'UTF-8'}"	data-fancybox-group="other-views" class="fancybox{if $image.id_image == $cover.id_image} shown{/if}"{/if} title="{$imageTitle}">
																			<img class="img-responsive" id="thumb_{$image.id_image}" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'cart_default')|escape:'html':'UTF-8'}" alt="{$imageTitle}" title="{$imageTitle}"{if isset($cartSize)} height="{$cartSize.height}" width="{$cartSize.width}"{/if} itemprop="image" />
																		</a>
																	</li>
																{/foreach}
															{/if}
															{block name='displayRoomTypeThumbnailsBottom'}
																{hook h='displayRoomTypeThumbnailsBottom'}
															{/block}
															</ul>
														</div> <!-- end thumbs_list -->
														{if isset($images) && count($images) > 2}
															<a id="view_scroll_right" title="{l s='Other views'}" href="javascript:{ldelim}{rdelim}">
																{l s='Next'}
															</a>
														{/if}
														{block name='displayRoomTypeThumbnailsAfter'}
															{hook h="displayRoomTypeThumbnailsAfter"}
														{/block}
													</div> <!-- end views-block -->
												<!-- end thumbnails -->
											{/if}
										</div>
									{/block}
								</div>
								{if isset($images) && count($images) > 1}
									<p class="resetimg clear no-print">
										<span id="wrapResetImages" style="display: none;">
											<a href="{$link->getProductLink($product)|escape:'html':'UTF-8'}" data-id="resetImages">
												<i class="icon-repeat"></i>
												{l s='Display all pictures'}
											</a>
										</span>
									</p>
								{/if}
							{/block}
						</div>

						<div id="service_products_cont">
							{if isset($service_products_exists) && $service_products_exists}
								{block name='service_products'}
									{include file="{$tpl_dir}_partials/service-products.tpl"}
								{/block}
							{/if}
						</div>

						<div class="product_info_containter">
							<!-- tab hook is added here -->
							<!--HOOK_PRODUCT_TAB -->
							<section class="page-product-box">
								{block name='product_tabs'}
									<ul class="nav nav-tabs product_description_tabs">
									<li class="active"><a href="#product_info_tab" class="idTabHrefShort" data-toggle="tab">{if $product->booking_product}{l s='Room Information'}{else}{l s='Product Information'}{/if}</a></li>
										{* Block for booking products *}
										{if isset($id_hotel) && $id_hotel}
											<li><a href="#refund_policies_tab" class="idTabHrefShort" data-toggle="tab">{l s='Refund Policies'}</a></li>
										{/if}
										{if $display_google_maps && !empty($hotel_latitude) && !empty($hotel_longitude) && ($hotel_latitude|floatval != 0 && $hotel_longitude|floatval != 0)}
											<li><a href="#room_type_map_tab" class="idTabHrefShort" data-toggle="tab">{l s='View on Map'}</a></li>
										{/if}
										{block name='displayProductTab'}
											{$HOOK_PRODUCT_TAB}
										{/block}
									</ul>
								{/block}
								{block name='product_tabs_content'}
									<div class="tab-content product_description_tabs_contents">
										{block name='product_info_tab_content'}
											<div id="product_info_tab" class="tab-pane active card">
												<div id="product_info_tab_information">
													{block name='product_info_tab_room_description'}
														<div class="row info_margin_div room_description">
															<div class="col-sm-12">
																{$product->description}
															</div>
														</div>
													{/block}
													{block name='product_info_tab_room_guests'}
														{if isset($room_type_info['adults']) && isset($room_type_info['children']) }
															<div class="info_margin_div">
																<div class="room_info_heading">
																	<span>{l s='Max Capacity'}</span>
																</div>
																<div class="room_info_content">
																	<p>{$room_type_info['adults']} {l s='Adults'}, {$room_type_info['children']} {if $room_type_info['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if} ({l s='Max guests'}: {$room_type_info['max_guests']|escape:'htmlall':'UTF-8'})</p>
																</div>
															</div>
														{/if}
													{/block}
													{block name='product_info_tab_room_timing'}
														{if isset($id_hotel) && $id_hotel}
															<div class="info_margin_div">
																<div class="room_info_heading">
																	<span>{l s='Check-in and check-out time'}</span>
																</div>
																<div class="room_info_content">
																	<p>{l s='Check-in: '}{$hotel_check_in|escape:'html':'UTF-8'}</p>
																	<p>{l s='Check-out: '}{$hotel_check_out|escape:'html':'UTF-8'}</p>
																</div>
															</div>
														{/if}
													{/block}
													{block name='product_info_tab_room_bed_type'}
														{if isset($selected_bed_types) && $selected_bed_types && isset($bed_types_info) && $bed_types_info}
															<div class="info_margin_div">
																<div class="room_info_heading">
																	<span>{l s='Bed Types'}</span>
																</div>
																<div class="room_info_content">
																	{foreach $selected_bed_types as $selected_bed_type}
																		{if isset($bed_types_info[$selected_bed_type])}
																			<p>{$bed_types_info[$selected_bed_type]['name']}: {$bed_types_info[$selected_bed_type]['area']} {l s='sq.'}{$dimension_unit}</p>
																		{/if}
																	{/foreach}
																</div>
															</div>
														{/if}
													{/block}
													{block name='product_info_tab_room_features'}
														{if isset($features) && $features}
															<div class="info_margin_div">
																<div class="room_info_heading">
																	<span>{l s='Room Features'}</span>
																</div>
																<div class="room_info_content row">
																	{foreach from=$features key=ftr_k item=ftr_v}
																		<div class="col-md-3 col-sm-4 col-xs-6">
																			<div class="rm_ftr_wrapper" title="{$ftr_v.name|escape:'html':'UTF-8'}" alt="{$ftr_v.name|escape:'html':'UTF-8'}" >
																				<img src="{$link->getMediaLink("`$ftr_img_src|escape:'html':'UTF-8'`{$ftr_v.value|escape:'html':'UTF-8'}")}">  {$ftr_v.name|escape:'html':'UTF-8'}
																			</div>
																		</div>
																	{/foreach}
																</div>
															</div>
														{/if}
													{/block}
													{* Block for booking products *}
													{if isset($id_hotel) && $id_hotel}
														{block name='product_info_tab_hotel_features'}
															{if isset($hotel_features) && $hotel_features}
																<div class="info_margin_div">
																	<div class="room_info_heading">
																		<span>{l s='Hotel Features'}</span>
																	</div>
																	<div class="room_info_content row">
																		{foreach from=$hotel_features key=ftr_k item=ftr_v}
																			<div class="col-sm-4 col-xs-12"><i class="circle-small">o</i> {$ftr_v|escape:'html':'UTF-8'}</div>
																		{/foreach}
																	</div>
																</div>
															{/if}
														{/block}
														{block name='product_info_tab_hotel_description'}
															{if isset($hotel_description) && $hotel_description}
																<div class="info_margin_div">
																	<div class="room_info_heading">
																		<span>{l s='Hotel Description'}</span>
																	</div>
																	<div class="room_info_content">
																		{$hotel_description}
																	</div>
																</div>
															{/if}
														{/block}
													{/if}
													{block name='product_info_tab_hotel_images'}
														{if isset($hotel_has_images) && $hotel_has_images}
															<div class="room_info_hotel_images_wrap">
																<div class="info_margin_div">
																	<div class="room_info_heading">
																		<span>{l s='Hotel Images'}</span>
																	</div>
																	<div class="room_info_content" id="room_info_hotel_images">
																		<div class="row images-wrap"></div>
																		<div class="row skeleton-loading-wrap">
																			<div class="skeleton-loading-wave clearfix">
																				<div class="col-sm-4">
																					<div class="loading-container-box"></div>
																				</div>
																				<div class="col-sm-4">
																					<div class="loading-container-box"></div>
																				</div>
																				<div class="col-sm-4">
																					<div class="loading-container-box"></div>
																				</div>
																			</div>
																		</div>
																	</div>
																	<a class="btn btn-primary btn-show-more-images hide" data-id-product="{$product->id}" data-next-page="1">
																		<span>{l s='SHOW MORE'}</span>
																	</a>
																</div>
															</div>
														{/if}
													{/block}
													<!-- <div class="info_margin_div">
														<div class="room_info_heading">
															<span>{l s='Rooms'}</span>
														</div>
														<div class="room_info_content row"></div>
													</div> -->
													{block name='product_info_tab_hotel_policies'}
														{if isset($hotel_policies) && $hotel_policies}
															<div class="info_margin_div">
																<div class="room_info_heading">
																	<span>{l s='Hotel Policies'}</span>
																</div>
																<div class="room_info_content">
																	<p class="">{$hotel_policies}</p>
																</div>
															</div>
														{/if}
													{/block}
												</div>
											</div>
										{/block}
										{block name='product_refund_policies_tab_content'}
											{* Block for booking products *}
											{if isset($id_hotel) && $id_hotel}
												<div id="refund_policies_tab" class="tab-pane card">
													{if isset($isHotelRefundable) && $isHotelRefundable}
														{if isset($hotelRefundRules) && $hotelRefundRules}
															{foreach $hotelRefundRules as $refundRule}
																<div class="info_margin_div">
																	<div class="room_info_content">
																		<i class="circle-small">o</i> <span class="refund-rule-name">{$refundRule['name']|escape:'html':'UTF-8'} - </span> <span>{$refundRule['description']|escape:'html':'UTF-8'}</span>
																	</div>
																</div>
															{/foreach}
														{else}
															{l s='N/A'}
														{/if}
													{else}
														<span class="non_refundable_txt error_msg">{l s='Non Refundable'}</span>
													{/if}
												</div>
											{/if}
										{/block}
										{block name='product_map_tab_content'}
											{if $display_google_maps && !empty($hotel_latitude) && !empty($hotel_longitude) && ($hotel_latitude|floatval != 0 && $hotel_longitude|floatval != 0)}
												<div id="room_type_map_tab" class="tab-pane card">
													<div class="map-wrap"></div>
													<div id="room-info-map-ui-content" style="display: none;">
														<div class="hotel-info-wrap">
															{if isset($hotel_image_link) && $hotel_image_link}
																<div class="hotel-image-wrap">
																	<img class="img img-responsive" src="{$hotel_image_link}">
																</div>
															{/if}
															<div>
																<p class="name">{$hotel_name|escape:'html':'UTF-8'}</p>
																<p class="address">{$hotel_address1|escape:'html':'UTF-8'}</p>
																<p class="contact">{l s='Contact:'} {$hotel_phone|escape:'html':'UTF-8'}</p>
																<a class="btn view-on-map" href="https://www.google.com/maps/search/?api=1&query={if $hotel_map_input_text != ''}{$hotel_map_input_text|urlencode}{else}{($hotel_latitude|cat:','|cat:$hotel_longitude)|urlencode}{/if}" target="_blank">
																	<span>{l s='View on Map'}</span>
																</a>
															</div>
														</div>
													</div>
												</div>
											{/if}
										{/block}
										{block name='displayProductTabContent'}
											{if isset($HOOK_PRODUCT_TAB_CONTENT) && $HOOK_PRODUCT_TAB_CONTENT}{$HOOK_PRODUCT_TAB_CONTENT}{/if}
										{/block}
									</div>
								{/block}
							</section>
						</div>
					</div> <!-- end pb-left-column -->
				{/block}

				{block name='product_right_column'}
					<div class="pb-right-column col-xs-12 col-sm-4 col-md-4">
						{if ($product->show_price && !isset($restricted_country_mode)) || isset($groups) || $product->reference || (isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS)}

							{block name='booking_form'}
								{include file='./_partials/booking-form.tpl'}
							{/block}

							{block name='product_demands'}
								{* extra room type demands *}
								{if isset($room_type_demands) && $room_type_demands}
									<div class="col-sm-12 card room_demands_container">
										<label for="" class="control-label">{l s='Additional Facilities'}</label>
										{foreach $room_type_demands as $idGlobalDemand => $demand}
											<div class="row room_demand_block">
												{if $product->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
													<div class="col-xs-1">
														<p class="checkbox">
															<input value="{$idGlobalDemand|escape:'html':'UTF-8'}" type="checkbox" class="id_room_type_demand" data-id_global_demand="{$idGlobalDemand|escape:'html':'UTF-8'}" />
														</p>
													</div>
												{/if}
												<div class="col-xs-11 demand_adv_option_block">
													<p>{$demand['name']|escape:'html':'UTF-8'} {if $product->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}<span class="pull-right"><span class="extra_demand_option_price">{convertPrice price = $demand['price']}</span>{if $demand['price_calc_method'] == $WK_PRICE_CALC_METHOD_EACH_DAY}{l s='/Night'}{/if}</span>{/if}</p>
													{if isset($demand['adv_option']) && $demand['adv_option']}
														<select class="id_option">
															{foreach $demand['adv_option'] as $idOption => $option}
																<option optionPrice="{$option['price']|escape:'html':'UTF-8'}" value="{$idOption|escape:'html':'UTF-8'}">{$option['name']|escape:'html':'UTF-8'}</option>
															{/foreach}
														</select>
													{else}
														<input type="hidden" class="id_option" value="0" />
													{/if}
												</div>
											</div>
										{/foreach}
										<div class="room_demands_container_overlay">
										</div>
									</div>
								{/if}
							{/block}
						{/if}
						{block name='displayRightColumnProduct'}
							{if isset($HOOK_EXTRA_RIGHT) && $HOOK_EXTRA_RIGHT}{$HOOK_EXTRA_RIGHT}{/if}
						{/block}
					</div>
				{/block}

			</div> <!-- end primary_block -->
			{if !$content_only}
				{if (isset($quantity_discounts) && count($quantity_discounts) > 0)}
					<!-- quantity discount -->
					<section class="page-product-box ">
						<h3 class="page-product-heading">{l s='Volume discounts'}</h3>
						<div id="quantityDiscount">
							<table class="std table-product-discounts">
								<thead>
									<tr>
										<th>{l s='Quantity'}</th>
										<th>{if $display_discount_price}{l s='Price'}{else}{l s='Discount'}{/if}</th>
										<th>{l s='You Save'}</th>
									</tr>
								</thead>
								<tbody>
									{foreach from=$quantity_discounts item='quantity_discount' name='quantity_discounts'}
										{if $quantity_discount.price >= 0 || $quantity_discount.reduction_type == 'amount'}
											{$realDiscountPrice=$quantity_discount.base_price|floatval-$quantity_discount.real_value|floatval}
										{else}
											{$realDiscountPrice=$quantity_discount.base_price|floatval*(1 - $quantity_discount.reduction)|floatval}
										{/if}
										<tr class="quantityDiscount_{$quantity_discount.id_product_attribute}" data-real-discount-value="{convertPrice price = $realDiscountPrice}" data-discount-type="{$quantity_discount.reduction_type}" data-discount="{$quantity_discount.real_value|floatval}" data-discount-quantity="{$quantity_discount.quantity|intval}">
											<td>
												{$quantity_discount.quantity|intval}
											</td>
											<td>
												{if $quantity_discount.price >= 0 || $quantity_discount.reduction_type == 'amount'}
													{if $display_discount_price}
														{if $quantity_discount.reduction_tax == 0 && !$quantity_discount.price}
															{convertPrice price = $productPriceWithoutReduction|floatval-($productPriceWithoutReduction*$quantity_discount.reduction_with_tax)|floatval}
														{else}
															{convertPrice price=$productPriceWithoutReduction|floatval-$quantity_discount.real_value|floatval}
														{/if}
													{else}
														{convertPrice price=$quantity_discount.real_value|floatval}
													{/if}
												{else}
													{if $display_discount_price}
														{if $quantity_discount.reduction_tax == 0}
															{convertPrice price = $productPriceWithoutReduction|floatval-($productPriceWithoutReduction*$quantity_discount.reduction_with_tax)|floatval}
														{else}
															{convertPrice price = $productPriceWithoutReduction|floatval-($productPriceWithoutReduction*$quantity_discount.reduction)|floatval}
														{/if}
													{else}
														{$quantity_discount.real_value|floatval}%
													{/if}
												{/if}
											</td>
											<td>
												<span>{l s='Up to'}</span>
												{if $quantity_discount.price >= 0 || $quantity_discount.reduction_type == 'amount'}
													{$discountPrice=$productPriceWithoutReduction|floatval-$quantity_discount.real_value|floatval}
												{else}
													{$discountPrice=$productPriceWithoutReduction|floatval-($productPriceWithoutReduction*$quantity_discount.reduction)|floatval}
												{/if}
												{$discountPrice=$discountPrice * $quantity_discount.quantity}
												{$qtyProductPrice=$productPriceWithoutReduction|floatval * $quantity_discount.quantity}
												{convertPrice price=$qtyProductPrice - $discountPrice}
											</td>
										</tr>
									{/foreach}
								</tbody>
							</table>
						</div>
					</section>
				{/if}
				<!-- {if isset($features) && $features}
					<section class="page-product-box">
						<h3 class="page-product-heading">{l s='Data sheet'}</h3>
						<table class="table-data-sheet">
							{foreach from=$features item=feature}
							<tr class="{cycle values="odd,even"}">
								{if isset($feature.value)}
								<td>{$feature.name|escape:'html':'UTF-8'}</td>
								<td>{$feature.value|escape:'html':'UTF-8'}</td>
								{/if}
							</tr>
							{/foreach}
						</table>
					</section>
				{/if} -->
				<!-- {if isset($product) && $product->description}
					<section class="page-product-box">
						<h3 class="page-product-heading">{l s='More info'}</h3>
						<div  class="rte">{$product->description}</div>
					</section>
				{/if} --><!-- by webkul commented -->
				{if isset($packItems) && $packItems|@count > 0}
				<section id="blockpack">
					<h3 class="page-product-heading">{l s='Pack content'}</h3>
					{block name='product_list'}
						{include file="$tpl_dir./product-list.tpl" products=$packItems}
					{/block}
				</section>
				{/if}
				<!-- tab hook is shifted to left column -->
				<!--end HOOK_PRODUCT_TAB -->
				{if isset($accessories) && $accessories}
					<!--Accessories -->
					<section class="page-product-box">
						<h3 class="page-product-heading">{l s='Accessories'}</h3>
						<div class="block products_block accessories-block clearfix">
							<div class="block_content">
								<ul id="bxslider" class="bxslider clearfix">
									{foreach from=$accessories item=accessory name=accessories_list}
										{if ($accessory.allow_oosp || $accessory.quantity_all_versions > 0 || $accessory.quantity > 0) && $accessory.available_for_order && !isset($restricted_country_mode)}
											{assign var='accessoryLink' value=$link->getProductLink($accessory.id_product, $accessory.link_rewrite, $accessory.category)}
											<li class="item product-box ajax_block_product{if $smarty.foreach.accessories_list.first} first_item{elseif $smarty.foreach.accessories_list.last} last_item{else} item{/if} product_accessories_description">
												<div class="product_desc">
													<a href="{$accessoryLink|escape:'html':'UTF-8'}" title="{$accessory.legend|escape:'html':'UTF-8'}" class="product-image product_image">
														<img class="lazyOwl" src="{$link->getImageLink($accessory.link_rewrite, $accessory.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{$accessory.legend|escape:'html':'UTF-8'}" width="{$homeSize.width}" height="{$homeSize.height}"/>
													</a>
													<div class="block_description">
														<a href="{$accessoryLink|escape:'html':'UTF-8'}" title="{l s='More'}" class="product_description">
															{$accessory.description_short|strip_tags|truncate:25:'...'}
														</a>
													</div>
												</div>
												<div class="s_title_block">
													<h5 itemprop="name" class="product-name">
														<a href="{$accessoryLink|escape:'html':'UTF-8'}">
															{$accessory.name|truncate:20:'...':true|escape:'html':'UTF-8'}
														</a>
													</h5>
													{if $accessory.show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
													<span class="price">
														{if $priceDisplay != 1}
														{displayWtPrice p=$accessory.price}{else}{displayWtPrice p=$accessory.price_tax_exc}
														{/if}
													</span>
													{/if}
												</div>
												<div class="clearfix" style="margin-top:5px">
													{if !$PS_CATALOG_MODE && ($accessory.allow_oosp || $accessory.quantity > 0)}
														<div class="no-print">
															<a class="exclusive button ajax_add_to_cart_button" href="{$link->getPageLink('cart', true, NULL, "qty=1&amp;id_product={$accessory.id_product|intval}&amp;token={$static_token}&amp;add")|escape:'html':'UTF-8'}" data-id-product="{$accessory.id_product|intval}" title="{l s='Add to cart'}">
																<span>{l s='Add to cart'}</span>
															</a>
														</div>
													{/if}
												</div>
											</li>
										{/if}
									{/foreach}
								</ul>
							</div>
						</div>
					</section>
					<!--end Accessories -->
				{/if}
				{block name='displayFooterProduct'}
					{if isset($HOOK_PRODUCT_FOOTER) && $HOOK_PRODUCT_FOOTER}{$HOOK_PRODUCT_FOOTER}{/if}
				{/block}
				<!-- description & features -->
				{if (isset($product) && $product->description) || (isset($features) && $features) || (isset($accessories) && $accessories) || (isset($HOOK_PRODUCT_TAB) && $HOOK_PRODUCT_TAB) || (isset($attachments) && $attachments) || isset($product) && $product->customizable}
					{if isset($attachments) && $attachments}
					<!--Download -->
					<section class="page-product-box">
						<h3 class="page-product-heading">{l s='Download'}</h3>
						{foreach from=$attachments item=attachment name=attachements}
							{if $smarty.foreach.attachements.iteration %3 == 1}<div class="row">{/if}
								<div class="col-lg-4">
									<h4><a href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")|escape:'html':'UTF-8'}">{$attachment.name|escape:'html':'UTF-8'}</a></h4>
									<p class="text-muted">{$attachment.description|escape:'html':'UTF-8'}</p>
									<a class="btn btn-default btn-block" href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")|escape:'html':'UTF-8'}">
										<i class="icon-download"></i>
										{l s="Download"} ({Tools::formatBytes($attachment.file_size, 2)})
									</a>
									<hr />
								</div>
							{if $smarty.foreach.attachements.iteration %3 == 0 || $smarty.foreach.attachements.last}</div>{/if}
						{/foreach}
					</section>
					<!--end Download -->
					{/if}
					{if isset($product) && $product->customizable}
					<!--Customization -->
					<section class="page-product-box">
						<h3 class="page-product-heading">{l s='Product customization'}</h3>
						<!-- Customizable products -->
						<form method="post" action="{$customizationFormTarget}" enctype="multipart/form-data" id="customizationForm" class="clearfix">
							<p class="infoCustomizable">
								{l s='After saving your customized product, remember to add it to your cart.'}
								{if $product->uploadable_files}
								<br />
								{l s='Allowed file formats are: GIF, JPG, PNG'}{/if}
							</p>
							{if $product->uploadable_files|intval}
								<div class="customizableProductsFile">
									<h5 class="product-heading-h5">{l s='Pictures'}</h5>
									<ul id="uploadable_files" class="clearfix">
										{counter start=0 assign='customizationField'}
										{foreach from=$customizationFields item='field' name='customizationFields'}
											{if $field.type == 0}
												<li class="customizationUploadLine{if $field.required} required{/if}">{assign var='key' value='pictures_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field}
													{if isset($pictures.$key)}
														<div class="customizationUploadBrowse">
															<img src="{$pic_dir}{$pictures.$key}_small" alt="" />
																<a href="{$link->getProductDeletePictureLink($product, $field.id_customization_field)|escape:'html':'UTF-8'}" title="{l s='Delete'}" >
																	<img src="{$img_dir}icon/delete.gif" alt="{l s='Delete'}" class="customization_delete_icon" width="11" height="13" />
																</a>
														</div>
													{/if}
													<div class="customizationUploadBrowse form-group">
														<label class="customizationUploadBrowseDescription">
															{if !empty($field.name)}
																{$field.name}
															{else}
																{l s='Please select an image file from your computer'}
															{/if}
															{if $field.required}<sup>*</sup>{/if}
														</label>
														<input type="file" name="file{$field.id_customization_field}" id="img{$customizationField}" class="form-control customization_block_input {if isset($pictures.$key)}filled{/if}" />
													</div>
												</li>
												{counter}
											{/if}
										{/foreach}
									</ul>
								</div>
							{/if}
							{if $product->text_fields|intval}
								<div class="customizableProductsText">
									<h5 class="product-heading-h5">{l s='Text'}</h5>
									<ul id="text_fields">
									{counter start=0 assign='customizationField'}
									{foreach from=$customizationFields item='field' name='customizationFields'}
										{if $field.type == 1}
											<li class="customizationUploadLine{if $field.required} required{/if}">
												<label for ="textField{$customizationField}">
													{assign var='key' value='textFields_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field}
													{if !empty($field.name)}
														{$field.name}
													{/if}
													{if $field.required}<sup>*</sup>{/if}
												</label>
												<textarea name="textField{$field.id_customization_field}" class="form-control customization_block_input" id="textField{$customizationField}" rows="3" cols="20">{strip}
													{if isset($textFields.$key)}
														{$textFields.$key|stripslashes}
													{/if}
												{/strip}</textarea>
											</li>
											{counter}
										{/if}
									{/foreach}
									</ul>
								</div>
							{/if}
							<p id="customizedDatas">
								<input type="hidden" name="quantityBackup" id="quantityBackup" value="" />
								<input type="hidden" name="submitCustomizedDatas" value="1" />
								<button class="button btn btn-default button button-small" name="saveCustomization">
									<span>{l s='Save'}</span>
								</button>
								<span id="ajax-loader" class="unvisible">
									<img src="{$img_ps_dir}loader.gif" alt="loader" />
								</span>
							</p>
						</form>
						<p class="clear required"><sup>*</sup> {l s='required fields'}</p>
					</section>
					<!--end Customization -->
					{/if}
				{/if}
			{/if}
		{else}
			<div class="bootstrap">
				<div class="alert alert-warning">
					{l s='This room type has not enough information. Please save information of related hotel and other required room information for the booking of this room type.'}
				</div>
			</div>
		{/if}
	</div> <!-- itemscope product wrapper -->
	{block name='product_js_vars'}
		{strip}
			{if isset($id_hotel) && $id_hotel}
				{addJsDef id_hotels = $id_hotel}
				{addJsDef max_order_date = $max_order_date}
				{addJsDef min_booking_offset = $min_booking_offset}
				{addJsDef booking_date_to = $date_to}
				{addJsDef booking_date_from = $date_from}
			{/if}
			{if isset($smarty.get.ad) && $smarty.get.ad}
				{addJsDefL name=ad}{$base_dir|cat:$smarty.get.ad|escape:'html':'UTF-8'}{/addJsDefL}
			{/if}
			{if isset($smarty.get.adtoken) && $smarty.get.adtoken}
				{addJsDefL name=adtoken}{$smarty.get.adtoken|escape:'html':'UTF-8'}{/addJsDefL}
			{/if}
			{addJsDef product_controller_url=$product_controller_url}
			{addJsDef allowBuyWhenOutOfStock=$allow_oosp|boolval}
			{addJsDef availableNowValue=$product->available_now|escape:'quotes':'UTF-8'}
			{addJsDef availableLaterValue=$product->available_later|escape:'quotes':'UTF-8'}
			{addJsDef attribute_anchor_separator=$attribute_anchor_separator|escape:'quotes':'UTF-8'}
			{addJsDef attributesCombinations=$attributesCombinations}
			{addJsDef currentDate=$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}
			{if isset($combinations) && $combinations}
				{addJsDef combinations=$combinations}
				{addJsDef combinationsFromController=$combinations}
				{addJsDef displayDiscountPrice=$display_discount_price}
				{addJsDefL name='upToTxt'}{l s='Up to' js=1}{/addJsDefL}
			{/if}
			{if isset($combinationImages) && $combinationImages}
				{addJsDef combinationImages=$combinationImages}
			{/if}
			{addJsDef customizationId=$id_customization}
			{addJsDef customizationFields=$customizationFields}
			{addJsDef default_eco_tax=$product->ecotax|floatval}
			{addJsDef displayPrice=$priceDisplay|intval}
			{addJsDef ecotaxTax_rate=$ecotaxTax_rate|floatval}
			{if isset($cover.id_image_only)}
				{addJsDef idDefaultImage=$cover.id_image_only|intval}
			{else}
				{addJsDef idDefaultImage=0}
			{/if}
			{addJsDef img_ps_dir=$img_ps_dir}
			{addJsDef img_prod_dir=$img_prod_dir}
			{addJsDef id_product=$product->id|intval}
			{addJsDef jqZoomEnabled=$jqZoomEnabled|boolval}
			{addJsDef maxQuantityToAllowDisplayOfLastQuantityMessage=$last_qties|intval}
			{addJsDef minimalQuantity=$product->minimal_quantity|intval}
			{addJsDef noTaxForThisProduct=$no_tax|boolval}
			{if isset($customer_group_without_tax)}
				{addJsDef customerGroupWithoutTax=$customer_group_without_tax|boolval}
			{else}
				{addJsDef customerGroupWithoutTax=false}
			{/if}
			{if isset($group_reduction)}
				{addJsDef groupReduction=$group_reduction|floatval}
			{else}
				{addJsDef groupReduction=false}
			{/if}
			{addJsDef oosHookJsCodeFunctions=Array()}
			{addJsDef productHasAttributes=isset($groups)|boolval}
			{addJsDef productPriceTaxExcluded=($product->getPriceWithoutReduct(true)|default:'null' - $product->ecotax)|floatval}
			{addJsDef productPriceTaxIncluded=($product->getPriceWithoutReduct(false)|default:'null' - $product->ecotax)|floatval}
			{addJsDef productBasePriceTaxExcluded=($product->getPrice(false, null, 6, null, false, false) - $product->ecotax)|floatval}
			{addJsDef productBasePriceTaxExcl=($product->getPrice(false, null, 6, null, false, false)|floatval)}
			{addJsDef productBasePriceTaxIncl=($product->getPrice(true, null, 6, null, false, false)|floatval)}
			{addJsDef productReference=$product->reference|escape:'html':'UTF-8'}
			{addJsDef productAvailableForOrder=$product->available_for_order|boolval}
			{addJsDef productPriceWithoutReduction=$productPriceWithoutReduction|floatval}
			{addJsDef productPrice=$productPrice|floatval}
			{addJsDef productUnitPriceRatio=$product->unit_price_ratio|floatval}
			{addJsDef productShowPrice=(!$PS_CATALOG_MODE && $product->show_price)|boolval}
			{addJsDef PS_CATALOG_MODE=$PS_CATALOG_MODE}
			{if $product->specificPrice && $product->specificPrice|@count}
				{addJsDef product_specific_price=$product->specificPrice}
			{else}
				{addJsDef product_specific_price=array()}
			{/if}
			{if $display_qties == 1 && $product->quantity}
				{addJsDef quantityAvailable=$product->quantity}
			{else}
				{addJsDef quantityAvailable=0}
			{/if}
			{addJsDef quantitiesDisplayAllowed=$display_qties|boolval}
			{if $product->specificPrice && $product->specificPrice.reduction && $product->specificPrice.reduction_type == 'percentage'}
				{addJsDef reduction_percent=$product->specificPrice.reduction*100|floatval}
			{else}
				{addJsDef reduction_percent=0}
			{/if}
			{if $product->specificPrice && $product->specificPrice.reduction && $product->specificPrice.reduction_type == 'amount'}
				{addJsDef reduction_price=$product->specificPrice.reduction|floatval}
			{else}
				{addJsDef reduction_price=0}
			{/if}
			{if $product->specificPrice && $product->specificPrice.price}
				{addJsDef specific_price=$product->specificPrice.price|floatval}
			{else}
				{addJsDef specific_price=0}
			{/if}
			{addJsDef specific_currency=($product->specificPrice && $product->specificPrice.id_currency)|boolval} {* TODO: remove if always false *}
			{addJsDef stock_management=$PS_STOCK_MANAGEMENT|intval}
			{addJsDef taxRate=$tax_rate|floatval}
			{addJsDefL name=doesntExist}{l s='This combination does not exist for this product. Please select another combination.' js=1}{/addJsDefL}
			{addJsDefL name=doesntExistNoMore}{l s='This product is no longer in stock' js=1}{/addJsDefL}
			{addJsDefL name=doesntExistNoMoreBut}{l s='with those attributes but is available with others.' js=1}{/addJsDefL}
			{addJsDefL name=fieldRequired}{l s='Please fill in all the required fields before saving your customization.' js=1}{/addJsDefL}
			{addJsDefL name=uploading_in_progress}{l s='Uploading in progress, please be patient.' js=1}{/addJsDefL}
			{addJsDefL name='product_fileDefaultHtml'}{l s='No file selected' js=1}{/addJsDefL}
			{addJsDefL name='product_fileButtonHtml'}{l s='Choose File' js=1}{/addJsDefL}
			{addJsDef currency_sign = $currency->sign}
			{addJsDef currency_format = $currency->format}
			{addJsDef currency_blank = $currency->blank}
			{addJsDefL name=correct_date_cond}{l s='Check Out Date should be greater than Check in date.' js=1}{/addJsDefL}
			{addJsDefL name=some_error_cond}{l s='Some error occured .Please try again.' js=1}{/addJsDefL}
			{addJsDefL name=unavail_qty_text}{l s='Required quantity of rooms are Not available.' js=1}{/addJsDefL}
			{addJsDefL name=out_of_stock_cond}{l s='No room is available for this period.' js=1}{/addJsDefL}
			{addJsDefL name=wrong_qty_cond}{l s='you are trying for a invalid quantity.' js=1}{/addJsDefL}
			{addJsDefL name=select_txt}{l s='Select' js=1}{/addJsDefL}
			{addJsDefL name=remove_txt}{l s='Remove' js=1}{/addJsDefL}
			{addJsDefL name=unselect_txt}{l s='Unselect' js=1}{/addJsDefL}
			{addJsDefL name=service_added_txt}{l s='Service added' js=1}{/addJsDefL}
			{addJsDefL name=service_removed_txt}{l s='Service removed' js=1}{/addJsDefL}
			{addJsDefL name=service_updated_txt}{l s='Service updated' js=1}{/addJsDefL}
			{addJsDefL name=max_service_product_qty_txt}{l s='Maximum allowed quantity in the cart is' js=1}{/addJsDefL}
			{addJsDefL name=out_of_stock_text}{l s='Requested quantitity is out of stock' js=1}{/addJsDefL}
		{/strip}
	{/block}
	{/if}
{/block}
