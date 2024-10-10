{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{block name='blockcart'}
	<!-- MODULE Block cart -->
	{if isset($blockcart_top) && $blockcart_top}
	<div class="header-top-item {if $PS_CATALOG_MODE}header_user_catalog{/if}">
	{/if}
		{block name='blockcart_shopping_cart'}
			<div class="shopping_cart">
				<a href="{$link->getPageLink($order_process, true)|escape:'html':'UTF-8'}" title="{l s='View my booking cart' mod='blockcart'}" rel="nofollow">
					<!-- <b>{l s='Cart' mod='blockcart'}</b> -->
					<span class="badge badge_style ajax_cart_quantity{if $cart_qties == 0} unvisible{/if}">{$total_products_in_cart}</span>
					<!-- <span class="ajax_cart_product_txt{if $cart_qties != 1} unvisible{/if}">{l s='Rooms' mod='blockcart'}</span> -->
					<!-- <span class="ajax_cart_product_txt_s{if $cart_qties < 2} unvisible{/if}">{l s='Rooms' mod='blockcart'}</span> -->
					<span class="ajax_cart_total{if $cart_qties == 0} unvisible{/if}">
						{if $cart_qties > 0}
							{if $priceDisplay == 1}
								{assign var='blockcart_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
								{convertPrice price=$cart->getOrderTotal(false, $blockcart_cart_flag)}
							{else}
								{assign var='blockcart_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
								{convertPrice price=$cart->getOrderTotal(true, $blockcart_cart_flag)}
							{/if}
						{/if}
					</span>
					<span class="badge badge_style ajax_cart_no_product{if $cart_qties > 0} unvisible{/if}">0</span>
					{if $ajax_allowed && isset($blockcart_top) && !$blockcart_top}
						<span class="block_cart_expand{if !isset($colapseExpandStatus) || (isset($colapseExpandStatus) && $colapseExpandStatus eq 'expanded')} unvisible{/if}">&nbsp;</span>
						<span class="block_cart_collapse{if isset($colapseExpandStatus) && $colapseExpandStatus eq 'collapsed'} unvisible{/if}">&nbsp;</span>
					{/if}
				</a>
				{block name='blockcart_shopping_cart_content'}
					{if !$PS_CATALOG_MODE}
						<div class="cart_block block exclusive">
							<div class="block_content">
								<!-- block list of products -->
								<div class="cart_block_list{if isset($blockcart_top) && !$blockcart_top}{if isset($colapseExpandStatus) && $colapseExpandStatus eq 'expanded' || !$ajax_allowed || !isset($colapseExpandStatus)} expanded{else} collapsed unvisible{/if}{/if}">
									{block name='blockcart_shopping_cart_products'}
										{if $products}
											<dl class="products">
												{foreach from=$products key=data_k item='product' name='myLoop'}
												{* only show products that are booking or global without room *}
													{if $product.booking_product || ($product.service_product_type == Product::SERVICE_PRODUCT_WITHOUT_ROOMTYPE)}
														{assign var='productId' value=$product.id_product}
														{assign var='productAttributeId' value=$product.id_product_attribute}
														<dt data-id="cart_block_product_{$product.id_product|intval}_{if $product.id_product_attribute}{$product.id_product_attribute|intval}{else}0{/if}_{if $product.id_address_delivery}{$product.id_address_delivery|intval}{else}0{/if}" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
															<a class="cart-images" href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category)|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'cart_default')}" alt="{$product.name|escape:'html':'UTF-8'}" /></a>
															<div class="cart-info">
																{block name='blockcart_shopping_cart_product_name'}
																	<div class="product-name">
																	<!-- quantity changed for number of rooms -->
																		<!-- <span class="quantity-formated"><span class="quantity">{$cart_booking_data[$data_k]['total_num_rooms']}</span>&nbsp;x&nbsp;</span> -->
																		<a class="cart_block_product_name" href="{$link->getProductLink($product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}">{$product.name|truncate:30:'...':true|escape:'html':'UTF-8'}</a>
																	</div>
																{/block}

																{if isset($product.attributes_small)}
																	<div class="product-atributes">
																		<a href="{$link->getProductLink($product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'html':'UTF-8'}" title="{l s='Product detail' mod='blockcart'}">{$product.attributes_small}</a>
																	</div>
																{/if}
																{block name='blockcart_shopping_cart_product_total_price'}
																	<div class="cart-info-sec rm_product_info_{$product.id_product}">
																		<span class="product_info_label">{l s='Price' mod='blockcart'}:</span>
																		<span class="price product_info_data" ttl_prod_price="{$product.bookingData.total_room_type_amount}">
																			{if !isset($product.is_gift) || !$product.is_gift}
																				{displayWtPrice p="`$product.bookingData.total_room_type_amount`"}
																				<div id="hookDisplayProductPriceBlock-price">
																					{block name='displayProductPriceBlock'}
																						{hook h="displayProductPriceBlock" product=$product type="price" from="blockcart"}
																					{/block}
																				</div>
																			{else}
																				{l s='Free!' mod='blockcart'}
																			{/if}
																		</span>
																	</div>
																{/block}

																{block name='blockcart_shopping_cart_product_quantity'}
																	<div class="cart-info-sec rm_product_info_{$product.id_product}">
																		<span class="product_info_label">{l s='Total Qty.' mod='blockcart'}:</span>
																		<span class="quantity-formated">
																			{if $product.booking_product}
																				<span class="quantity product_info_data">{$product.bookingData['total_num_rooms']}</span>
																			{else}
																				<span class="quantity product_info_data">{$product.cart_quantity}</span>
																			{/if}
																		</span>
																	</div>
																{/block}
															</div>
															<span class="remove_link">
																{if !isset($customizedDatas.$productId.$productAttributeId) && (!isset($product.is_gift) || !$product.is_gift)}
																	<a class="ajax_cart_block_remove_link" href="{$link->getPageLink('cart', true, NULL, "delete=1&id_product={$product.id_product|intval}&ipa={$product.id_product_attribute|intval}&id_address_delivery={$product.id_address_delivery|intval}&token={$static_token}")|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='remove this product from my cart' mod='blockcart'}">&nbsp;</a>
																{/if}
															</span>
															<div style="clear:both"></div>
															{if $product.booking_product}
																{block name='blockcart_shopping_cart_dates'}
																	<div id="booking_dates_container_{$product.id_product}" class="cart_prod_cont">
																		<div class="table-responsive">
																			<table class="table">
																				<tbody>
																					<tr>
																						<th>{l s='Duration' mod='blockcart'}</th>
																						<th>{l s='Qty.' mod='blockcart'}</th>
																						<th>{l s='Price' mod='blockcart'}</th>
																						<th>&nbsp;<!-- {l s='Remove' mod='blockcart'} --></th>
																					</tr>
																					{foreach from=$product.bookingData['date_diff'] key=data_k1 item=data_v}
																						<tr class="rooms_remove_container">
																							{assign var="is_full_date" value=($show_full_date && ($data_v['data_form']|date_format:'%D' == $data_v['data_to']|date_format:'%D'))}
																							<td>
																								{dateFormat date=$data_v['data_form'] full=$is_full_date}&nbsp;-&nbsp;{dateFormat date=$data_v['data_to'] full=$is_full_date}
																							</td>
																							<td class="num_rooms_in_date">{$data_v['num_rm']}</td>
																							<td>{convertPrice price=($data_v['amount'] + $data_v['demand_price'])}</td>
																							<td><a class="remove_rooms_from_cart_link" href="#" rm_price="{$data_v['amount']}" id_product="{$product.id_product|intval}" date_from="{$data_v['data_form']}" date_to="{$data_v['data_to']}" num_rooms="{$data_v['num_rm']}" title="{l s='remove this room from my cart' mod='blockcart'}"></a></td>
																						</tr>
																					{/foreach}
																				</tbody>
																			</table>
																		</div>
																	</div>
																{/block}
															{/if}
														</dt>
														{if isset($product.attributes_small)}
															<dd data-id="cart_block_combination_of_{$product.id_product|intval}{if $product.id_product_attribute}_{$product.id_product_attribute|intval}{/if}_{$product.id_address_delivery|intval}" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
														{/if}
														<!-- Customizable datas -->
														{if isset($customizedDatas.$productId.$productAttributeId[$product.id_address_delivery])}
															{if !isset($product.attributes_small)}
																<dd data-id="cart_block_combination_of_{$product.id_product|intval}_{if $product.id_product_attribute}{$product.id_product_attribute|intval}{else}0{/if}_{if $product.id_address_delivery}{$product.id_address_delivery|intval}{else}0{/if}" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
															{/if}
															<ul class="cart_block_customizations" data-id="customization_{$productId}_{$productAttributeId}">
																{foreach from=$customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] key='id_customization' item='customization' name='customizations'}
																	<li name="customization">
																		<div data-id="deleteCustomizableProduct_{$id_customization|intval}_{$product.id_product|intval}_{$product.id_product_attribute|intval}_{$product.id_address_delivery|intval}" class="deleteCustomizableProduct">
																			<a class="ajax_cart_block_remove_link" href="{$link->getPageLink('cart', true, NULL, "delete=1&id_product={$product.id_product|intval}&ipa={$product.id_product_attribute|intval}&id_customization={$id_customization|intval}&token={$static_token}")|escape:'html':'UTF-8'}" rel="nofollow">&nbsp;</a>
																		</div>
																		{if isset($customization.datas.$CUSTOMIZE_TEXTFIELD.0)}
																			{$customization.datas.$CUSTOMIZE_TEXTFIELD.0.value|replace:"<br />":" "|truncate:28:'...'|escape:'html':'UTF-8'}
																		{else}
																			{l s='Customization #%d:' sprintf=$id_customization|intval mod='blockcart'}
																		{/if}
																	</li>
																{/foreach}
															</ul>
															{if !isset($product.attributes_small)}</dd>{/if}
														{/if}
														{if isset($product.attributes_small)}</dd>{/if}
													{/if}
												{/foreach}
											</dl>
										{/if}
									{/block}
									<p class="cart_block_no_products{if $products} unvisible{/if}">
										{l s='No products' mod='blockcart'}
									</p>
									{block name='blockcart_shopping_cart_discounts'}
										{if $discounts|@count > 0}
											<table class="vouchers{if $discounts|@count == 0} unvisible{/if}">
												{foreach from=$discounts item=discount}
													{if $discount.value_real > 0}
														<tr class="bloc_cart_voucher" data-id="bloc_cart_voucher_{$discount.id_discount|intval}">
															<td class="quantity">1x</td>
															<td class="name" title="{$discount.description}">
																{$discount.name|truncate:18:'...'|escape:'html':'UTF-8'}
															</td>
															<td class="price">
																-{if $priceDisplay == 1}{convertPrice price=$discount.value_tax_exc}{else}{convertPrice price=$discount.value_real}{/if}
															</td>
															<td class="delete">
																{if strlen($discount.code)}
																	<a class="delete_voucher" href="{$link->getPageLink("$order_process", true)}?deleteDiscount={$discount.id_discount|intval}" title="{l s='Delete' mod='blockcart'}" rel="nofollow">
																		<i class="icon-remove-sign"></i>
																	</a>
																{/if}
															</td>
														</tr>
													{/if}
												{/foreach}
											</table>
										{/if}
									{/block}
									{block name='blockcart_shopping_cart_prices'}
										<div class="cart-prices">
											<!-- <div class="cart-prices-line first-line">
												<span class="price cart_block_shipping_cost ajax_cart_shipping_cost{if !($page_name == 'order-opc') && $shipping_cost_float == 0 && (!isset($cart->id_address_delivery) || !$cart->id_address_delivery)} unvisible{/if}">
													{if $shipping_cost_float == 0}
														{if !($page_name == 'order-opc') && (!isset($cart->id_address_delivery) || !$cart->id_address_delivery)}{l s='To be determined' mod='blockcart'}{else}{l s='Free shipping!' mod='blockcart'}{/if}
													{else}
														{$shipping_cost}
													{/if}
												</span>
												<span{if !($page_name == 'order-opc') && $shipping_cost_float == 0 && (!isset($cart->id_address_delivery) || !$cart->id_address_delivery)} class="unvisible"{/if}>
													{l s='Shipping' mod='blockcart'}
												</span>
											</div>
											{if $show_wrapping}
												<div class="cart-prices-line">
													{assign var='cart_flag' value='Cart::ONLY_WRAPPING'|constant}
													<span class="price cart_block_wrapping_cost">
														{if $priceDisplay == 1}
															{convertPrice price=$cart->getOrderTotal(false, $cart_flag)}{else}{convertPrice price=$cart->getOrderTotal(true, $cart_flag)}
														{/if}
													</span>
													<span>
														{l s='Wrapping' mod='blockcart'}
													</span>
											</div>
											{/if} --><!-- commented by webkul unnecessary data -->
											{block name='blockcart_shopping_cart_total_tax'}
												{if $show_tax && $use_tax}
													<div class="cart-prices-line">
														<span class="price cart_block_tax_cost ajax_cart_tax_cost">{$tax_cost}</span>
														<span>{l s='Tax' mod='blockcart'}</span>
													</div>
												{/if}
											{/block}
											{block name='blockcart_shopping_cart_total_convenience_fee'}
												{if isset($total_convenience_fee)}
													<div class="cart-prices-line">
														<span class="price cart_block_convenience_fee ajax_cart_convenience_fee">{convertPrice price=$total_convenience_fee}</span>
														<span class="price">{l s='Convenience Fees' mod='blockcart'}</strong>
													</div>
												{/if}
											{/block}
											{block name='blockcart_shopping_cart_total'}
												<div class="cart-prices-line last-line">
													<span class="price cart_block_total ajax_block_cart_total" total_cart_price="{$totalToPay}">{$total}</span>
													<span>{l s='Total' mod='blockcart'}</span>
												</div>
												{if $use_taxes && $display_tax_label == 1 && $show_tax}
													<p>
													{if $priceDisplay == 0}
														{l s='Prices are tax included' mod='blockcart'}
													{elseif $priceDisplay == 1}
														{l s='Prices are tax excluded' mod='blockcart'}
													{/if}
													</p>
												{/if}
											{/block}
										</div>
									{/block}
									{block name='blockcart_shopping_cart_checkout_action'}
										<p class="cart-buttons">
											<a id="button_order_cart" class="btn btn-default button button-small" href="{$link->getPageLink("$order_process", true)|escape:"html":"UTF-8"}" title="{l s='Check out' mod='blockcart'}" rel="nofollow">
												<span>
													{l s='Check out' mod='blockcart'}<i class="icon-chevron-right right"></i>
												</span>
											</a>
										</p>
									{/block}
								</div>
							</div>
						</div><!-- .cart_block -->
					{/if}
				{/block}
			</div>
		{/block}
	{if isset($blockcart_top) && $blockcart_top}
	</div>
	{/if}
	{counter name=active_overlay assign=active_overlay}
	{block name='blockcart_layer_cart'}
		{if !$PS_CATALOG_MODE && $active_overlay == 1}
			<div id="layer_cart">
				<div class="clearfix">
					{block name='blockcart_layer_cart_left'}
						<div class="layer_cart_product col-xs-12 col-md-6">
							<span class="cross" title="{l s='Close window' mod='blockcart'}"></span>
							{block name='blockcart_layer_cart_left_heading'}
								<h2 class="layer_cart_room_txt">
									<i class="icon-check"></i>{l s='Room successfully added to your cart' mod='blockcart'}
								</h2>
								<h2 class="layer_cart_product_txt">
									<i class="icon-check"></i>{l s='Item successfully added to your cart' mod='blockcart'}
								</h2>
							{/block}
							{block name='blockcart_layer_cart_product_image'}
								<div class="product-image-container layer_cart_img">
								</div>
							{/block}
							{block name='blockcart_layer_cart_product_info'}
								<div class="layer_cart_product_info">
									<span id="layer_cart_product_title" class="product-name"></span>
									<span id="layer_cart_product_attributes"></span>
									<div class="layer_cart_room_txt">
										<strong class="dark">{l s='Time Duration' mod='blockcart'} &nbsp;-&nbsp;</strong>
										<span id="layer_cart_product_time_duration"></span>
									</div>
									<div>
										<strong class="dark layer_cart_room_txt">{if isset($occupancy_required_for_booking) && $occupancy_required_for_booking}{l s='Room occupancy' mod='blockcart'}{else}{l s='Rooms quantity added' mod='blockcart'}{/if} &nbsp;-&nbsp;</strong>
										<strong class="dark layer_cart_product_txt">{l s='Quantity' mod='blockcart'} &nbsp;-&nbsp;</strong>
										<span id="layer_cart_product_quantity"></span>
									</div>
									<div>
										<strong class="dark layer_cart_room_txt">{l s='Room type cost' mod='blockcart'} &nbsp;-&nbsp;</strong>
										<strong class="dark layer_cart_product_txt">{l s='Total' mod='blockcart'} &nbsp;-&nbsp;</strong>
										<span id="layer_cart_product_price"></span>
									</div>
								</div>
							{/block}
						</div>
					{/block}
					{block name='blockcart_layer_cart_right'}
						<div class="layer_cart_cart col-xs-12 col-md-6">
							{block name='blockcart_layer_cart_right_heading'}
								<h2>
									<!-- Plural Case [both cases are needed because page may be updated in Javascript] -->
									<span class="ajax_cart_product_txt_s {if $cart_qties < 2} unvisible{/if}">
										{l s='There are [1]%d[/1] item(s) in your cart.' mod='blockcart' sprintf=[$cart_qties] tags=['<span class="ajax_cart_quantity">']}
									</span>

									<!-- Singular Case [both cases are needed because page may be updated in Javascript] -->
									<span class="ajax_cart_product_txt {if $cart_qties > 1} unvisible{/if}">
										{l s='1 item in your cart.' mod='blockcart'}
									</span>
								</h2>
							{/block}

							{block name='blockcart_layer_cart_total_price'}
								<div class="layer_cart_row">
									<strong class="dark">
										{l s='Total Rooms Cost in cart' mod='blockcart'}
										{if $display_tax_label}
											{if $priceDisplay == 1}
												{l s='(tax excl.)' mod='blockcart'}
											{else}
												{l s='(tax incl.)' mod='blockcart'}
											{/if}
										{/if}
									</strong>
									<span class="ajax_block_room_total pull-right">
										{if $cart_qties > 0}
											{convertPrice price=$cart->getOrderTotal(false, Cart::ONLY_PRODUCTS)}
										{/if}
									</span>
								</div>
							{/block}

							<!-- {if $show_wrapping}
								<div class="layer_cart_row">
									<strong class="dark">
										{l s='Wrapping' mod='blockcart'}
										{if $use_taxes && $display_tax_label && $show_tax}
											{if $priceDisplay == 1}
												{l s='(tax excl.)' mod='blockcart'}
											{else}
												{l s='(tax incl.)' mod='blockcart'}
											{/if}
										{/if}
									</strong>
									<span class="price cart_block_wrapping_cost">
										{if $priceDisplay == 1}
											{convertPrice price=$cart->getOrderTotal(false, Cart::ONLY_WRAPPING)}
										{else}
											{convertPrice price=$cart->getOrderTotal(true, Cart::ONLY_WRAPPING)}
										{/if}
									</span>
								</div>
							{/if} -->
							<!-- <div class="layer_cart_row">
								<strong class="dark{if $shipping_cost_float == 0 && (!isset($cart->id_address_delivery) || !$cart->id_address_delivery)} unvisible{/if}">
									{l s='Total shipping' mod='blockcart'}&nbsp;{if $display_tax_label}{if $priceDisplay == 1}{l s='(tax excl.)' mod='blockcart'}{else}{l s='(tax incl.)' mod='blockcart'}{/if}{/if}
								</strong>
								<span class="ajax_cart_shipping_cost{if $shipping_cost_float == 0 && (!isset($cart->id_address_delivery) || !$cart->id_address_delivery)} unvisible{/if}">
									{if $shipping_cost_float == 0}
										{if (!isset($cart->id_address_delivery) || !$cart->id_address_delivery)}{l s='To be determined' mod='blockcart'}{else}{l s='Free shipping!' mod='blockcart'}{/if}
									{else}
										{$shipping_cost}
									{/if}
								</span>
							</div> -->
							{block name='blockcart_layer_cart_total_convenience_fee'}
								{if isset($total_convenience_fee)}
									<div class="layer_cart_row">
										<strong class="dark">
											{l s='Convenience Fees' mod='blockcart'}
											{if $display_tax_label}
												{if $priceDisplay == 1}
													{l s='(tax excl.)' mod='blockcart'}
												{else}
													{l s='(tax incl.)' mod='blockcart'}
												{/if}
											{/if}
										</strong>
										<span class="price ajax_cart_convenience_fee pull-right">{convertPrice price=$total_convenience_fee}</span>
									</div>
								{/if}
							{/block}
							{block name='blockcart_layer_cart_total_tax'}
								{if $show_tax && $use_tax}
									<div class="layer_cart_row">
										<strong class="dark">{l s='Tax' mod='blockcart'}</strong>
										<span class="price cart_block_tax_cost ajax_cart_tax_cost pull-right">{$tax_cost}</span>
									</div>
								{/if}
							{/block}
							{block name='blockcart_layer_cart_total_price'}
								<div class="layer_cart_row">
									<strong class="dark">
										{l s='Total' mod='blockcart'}
										{if $display_tax_label}
											{if $priceDisplay == 1}
												{l s='(tax excl.)' mod='blockcart'}
											{else}
												{l s='(tax incl.)' mod='blockcart'}
											{/if}
										{/if}
									</strong>
									<span class="ajax_block_cart_total pull-right">
										{if $cart_qties > 0}
											{if $priceDisplay == 1}
												{convertPrice price=$cart->getOrderTotal(false)}
											{else}
												{convertPrice price=$cart->getOrderTotal(true)}
											{/if}
										{/if}
									</span>
								</div>
								{block name='blockcart_layer_cart_actions'}
									<div class="button-container">
										<span class="continue btn btn-default button exclusive-medium" title="{l s='Continue browsing' mod='blockcart'}">
											<span>
												<i class="icon-chevron-left left"></i>{l s='Continue browsing' mod='blockcart'}
											</span>
										</span>
										<a class="btn btn-default button button-medium"	href="{$link->getPageLink("$order_process", true)|escape:"html":"UTF-8"}" title="{l s='Proceed to checkout' mod='blockcart'}" rel="nofollow">
											<span>
												{l s='Proceed to checkout' mod='blockcart'}<i class="icon-chevron-right right"></i>
											</span>
										</a>
									</div>
								{/block}
							{/block}
						</div>
					{/block}
				</div>
				<div class="crossseling"></div>
			</div> <!-- #layer_cart -->
			<div class="layer_cart_overlay"></div>
		{/if}
	{/block}
	{block name='blockcart_js_vars'}
		{strip}
		{addJsDefL name=someErrorCondition}{l s='Some Error occured.Please try again.' mod='blockcart' js=1}{/addJsDefL}
		{addJsDef CUSTOMIZE_TEXTFIELD=$CUSTOMIZE_TEXTFIELD}
		{addJsDef img_dir=$img_dir|escape:'quotes':'UTF-8'}
		{addJsDef generated_date=$smarty.now|intval}
		{addJsDef ajax_allowed=$ajax_allowed|boolval}
		{addJsDef hasDeliveryAddress=(isset($cart->id_address_delivery) && $cart->id_address_delivery)}

		{addJsDefL name=customizationIdMessage}{l s='Customization #' mod='blockcart' js=1}{/addJsDefL}
		{addJsDefL name=removingLinkText}{l s='remove this product from my cart' mod='blockcart' js=1}{/addJsDefL}
		{addJsDefL name=freeShippingTranslation}{l s='Free shipping!' mod='blockcart' js=1}{/addJsDefL}
		{addJsDefL name=freeProductTranslation}{l s='Free!' mod='blockcart' js=1}{/addJsDefL}
		{addJsDefL name=delete_txt}{l s='Delete' mod='blockcart' js=1}{/addJsDefL}
		{addJsDefL name=toBeDetermined}{l s='To be determined' mod='blockcart' js=1}{/addJsDefL}
		{/strip}
		{* MODULE Block cart

		###################################################################
		By webkul to send needed variable in ajax-cart.js
		################################################################### *}
		{addJsDef module_dir=$module_dir}
		{addJsDef currency_sign = $currency->sign}
		{addJsDef room_warning_num = $warning_num}
		{addJsDef currency_format = $currency->format}
		{addJsDef currency_blank = $currency->blank}
		{addJsDefL name=adults_txt}{l s='Adults' mod='blockcart' js=1}{/addJsDefL}
		{addJsDefL name=children_txt}{l s='Children' mod='blockcart' js=1}{/addJsDefL}
		{addJsDefL name=price_txt}{l s='Price' mod='blockcart' js=1}{/addJsDefL}
		{addJsDefL name=total_qty_txt}{l s='Total Qty.' mod='blockcart' js=1}{/addJsDefL}
		{addJsDefL name=qty_txt}{l s='Qty' mod='blockcart' js=1}{/addJsDefL}
		{addJsDefL name=duration_txt}{l s='Duration' mod='blockcart' js=1}{/addJsDefL}
		{addJsDefL name=capacity_txt}{l s='Capacity' mod='blockcart' js=1}{/addJsDefL}
		{addJsDefL name=remove_rm_title}{l s='Remove this room from my cart' mod='blockcart' js=1}{/addJsDefL}
		{addJsDefL name=no_internet_txt}{l s='No internet. Please check your internet connection.' mod='blockcart' js=1}{/addJsDefL}

		{addJsDef rm_avail_process_lnk = $link->getModuleLink('blockcart', 'checkroomavailabilityajaxprocess')}
		{addJsDef pagename = $current_page}
		{/strip}
	{/block}
	{* ###################################################################
	End
	################################################################### *}
{/block}
