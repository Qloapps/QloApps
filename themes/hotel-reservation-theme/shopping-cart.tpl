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

{capture name=path}{l s='Your shopping cart'}{/capture}

{* <h1 id="cart_title" class="page-heading">{l s='Shopping-cart summary'}
	{if !isset($empty) && !$PS_CATALOG_MODE}
		<span class="heading-counter">{l s='Your shopping cart contains:'}
			<span id="summary_products_quantity">{$productNumber} {if $productNumber == 1}{l s='product'}{else}{l s='products'}{/if}</span>
		</span>
	{/if}
</h1> *}

{if isset($account_created)}
	<p class="alert alert-success">
		{l s='Your account has been created.'}
	</p>
{/if}

{assign var='current_step' value='summary'}
{include file="$tpl_dir./order-steps.tpl"}

{if isset($empty)}
	<p class="alert alert-warning">{l s='Your shopping cart is empty.'}</p>
{elseif $PS_CATALOG_MODE}
	<p class="alert alert-warning">{l s='This store has not accepted your new order.'}</p>
{else}
	<p id="emptyCartWarning" class="alert alert-warning unvisible">{l s='Your shopping cart is empty.'}</p>
	{if isset($lastProductAdded) AND $lastProductAdded}
		<div class="cart_last_product">
			<div class="cart_last_product_header">
				<div class="left">{l s='Last product added'}</div>
			</div>
			<a class="cart_last_product_img" href="{$link->getProductLink($lastProductAdded.id_product, $lastProductAdded.link_rewrite, $lastProductAdded.category, null, null, $lastProductAdded.id_shop)|escape:'html':'UTF-8'}">
				<img src="{$link->getImageLink($lastProductAdded.link_rewrite, $lastProductAdded.id_image, 'small_default')|escape:'html':'UTF-8'}" alt="{$lastProductAdded.name|escape:'html':'UTF-8'}"/>
			</a>
			<div class="cart_last_product_content">
				<p class="product-name">
					<a href="{$link->getProductLink($lastProductAdded.id_product, $lastProductAdded.link_rewrite, $lastProductAdded.category, null, null, null, $lastProductAdded.id_product_attribute)|escape:'html':'UTF-8'}">
						{$lastProductAdded.name|escape:'html':'UTF-8'}
					</a>
				</p>
				{if isset($lastProductAdded.attributes) && $lastProductAdded.attributes}
					<small>
						<a href="{$link->getProductLink($lastProductAdded.id_product, $lastProductAdded.link_rewrite, $lastProductAdded.category, null, null, null, $lastProductAdded.id_product_attribute)|escape:'html':'UTF-8'}">
							{$lastProductAdded.attributes|escape:'html':'UTF-8'}
						</a>
					</small>
				{/if}
			</div>
		</div>
	{/if}
	{assign var='total_discounts_num' value="{if $total_discounts != 0}1{else}0{/if}"}
	{assign var='use_show_taxes' value="{if $use_taxes && $show_taxes}2{else}0{/if}"}
	{assign var='total_wrapping_taxes_num' value="{if $total_wrapping != 0}1{else}0{/if}"}
	{* eu-legal *}
	{hook h="displayBeforeShoppingCartBlock"}

	<div class="order-detail-content">
		<p class="room_info_text">{l s='rooms information'}</p>
		{foreach from=$cart_htl_data key=data_k item=data_v}
			{foreach from=$data_v['date_diff'] key=rm_k item=rm_v}
				<div class="row cart_product_line">
					<div class="col-sm-2 room-type-img-block">
						<p>
							<a href="{$link->getProductLink($data_v['id_product'])}">
								<img src="{$data_v['cover_img']}" class="img-responsive" />
							</a>
						</p>
						<p class="room_remove_block">
							<a href="{$rm_v['link']}"><i class="icon-trash"></i> &nbsp;{l s='Remove'}</a>
						</p>
						{hook h='displayCartRoomImageAfter' id_product=$data_v['id_product']}
					</div>
					<div class="col-sm-10">
						<div class="room-info-container">
							<div class="room-xs-img">
								<a href="{$link->getProductLink($data_v['id_product'])}">
									<img src="{$data_v['cover_img']}" class="img-responsive" />
								</a>
							</div>
							<div class="room-xs-info">
								<p class="product-name">
									<a href="{$link->getProductLink($data_v['id_product'])}">
										{$data_v['name']}
									</a>
									<a class="btn btn-default pull-right room-xs-remove" href="{$rm_v['link']}"><i class="icon-trash"></i></a>
								</p>
								{if isset($data_v['hotel_info']['location'])}
									<p class="hotel-location">
										<i class="icon-map-marker"></i> &nbsp;{$data_v['hotel_info']['location']}
									</p>
								{/if}
							</div>
						</div>
						{if isset($data_v['hotel_info']['room_features'])}
							<div class="room-type-features">
							{foreach $data_v['hotel_info']['room_features'] as $feature}
								<span class="room-type-feature">
									<img src="{$THEME_DIR}img/icon/form-ok-circle.svg" /> {$feature['name']}
								</span>
							{/foreach}
							</div>
						{/if}
						<div class="room_duration_block">
							<div class="col-sm-3 col-xs-6">
								<p class="room_duration_block_head">{l s='CHECK IN'}</p>
								<p class="room_duration_block_value">{$rm_v['data_form']|date_format:"%d %b, %a"}</p>
							</div>
							<div class="col-sm-3 col-xs-6">
								<p class="room_duration_block_head">{l s='CHECK OUT'}</p>
								<p class="room_duration_block_value">{$rm_v['data_to']|date_format:"%d %b, %a"}</p>
							</div>
							<div class="col-sm-2 col-xs-6">
								<p class="room_duration_block_head">{l s='ROOMS'}</p>
								<p class="room_duration_block_value">
									{if {$rm_v['num_rm']} <= 9}0{$rm_v['num_rm']}{else}{$rm_v['num_rm']}{/if}
								</p>
							</div>
							<div class="col-sm-4 col-xs-6">
								<p class="room_duration_block_head">{l s='NO. OF GUESTS'}</p>
								<p class="room_duration_block_value">
									{if {$data_v['adult']} <= 9}0{$data_v['adult']}{else}{$data_v['adult']}{/if} {l s='Adults'}, {if {$data_v['children']} <= 9}0{$data_v['children']}{else}{$data_v['children']}{/if} {l s='Child'}
								</p>
							</div>
						</div>
						<div class="row room_price_detail_block">
							<div class="col-sm-7">
								<div class="row">
									<div class="col-sm-6">
										<div class="rooms_price_block">
											<p class="room_total_price">
												<span class="room_type_current_price">
													{displayPrice price=($rm_v['amount'])}
												</span>
												{if isset($data_v['extra_demands']) && $data_v['extra_demands']}
													<span class="plus-sign pull-right">
														+
													</span>
												{/if}
											</p>
											<p class="room_total_price_detial">
												{l s='Total rooms price'} {if $display_tax_label}{if $priceDisplay} {l s='(Excl.'} {else}{l s='(Incl.)'}{/if} {l s='all taxes.)'}{/if}
											</p>
										</div>
									</div>
									{if isset($data_v['extra_demands']) && $data_v['extra_demands']}
										<div class="col-sm-6">
											<div class="demand_price_block">
												<p class="demand_total_price">
													<span class="room_type_current_price">
														{displayPrice price=$rm_v['demand_price']}
													</span>
												</p>
												<p class="room_total_price_detial">
													<a date_from="{$rm_v['data_form']|escape:'html':'UTF-8'}" date_to="{$rm_v['data_to']|escape:'html':'UTF-8'}" id_product="{$data_v['id_product']|escape:'html':'UTF-8'}" class="open_rooms_extra_demands" href="#rooms_type_extra_demands_form">
														{l s='Additional Facilities'}
													</a>
												</p>
											</div>
										</div>
									{/if}
								</div>
							</div>
							<div class="col-sm-5">
								<div class="total_price_block">
									<p class="room_total_price">
										<span class="room_type_current_price">
											{displayPrice price=($rm_v['amount']+$rm_v['demand_price'])}
										</span>
									</p>
									<p class="room_total_price_detial">
										{l s='Total price for'} {$rm_v['num_days']} {l s='Night(s) stay'}{if $display_tax_label}{if $priceDisplay} {l s='(Excl.'} {else}{l s='(Incl.'}{/if} {l s='all taxes.)'}{/if}
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<hr>
			{/foreach}
		{/foreach}

		{* proceed only if no order restrict errors are there *}
		{if !$orderRestrictErr}
			<div class="row">
				<div class="col-sm-12 proceed_btn_block">
					<a class="btn btn-default button button-medium pull-right" href="{$link->getPageLink('order-opc', null, null, ['proceed_to_customer_dtl' => 1])}" title="Proceed to checkout" rel="nofollow">
						<span>
							{l s='Proceed'}
						</span>
					</a>
				</div>
			</div>
		{/if}
	</div>

	{if $show_option_allow_separate_package}
	<p>
		<label for="allow_seperated_package" class="checkbox inline">
			<input type="checkbox" name="allow_seperated_package" id="allow_seperated_package" {if $cart->allow_seperated_package}checked="checked"{/if} autocomplete="off"/>
			{l s='Send available products first'}
		</label>
	</p>
	{/if}

	{* Define the style if it doesn't exist in the PrestaShop version*}
	{* Will be deleted for 1.5 version and more *}
	{if !isset($addresses_style)}
		{$addresses_style.company = 'address_company'}
		{$addresses_style.firstname = 'address_name'}
		{$addresses_style.lastname = 'address_name'}
		{$addresses_style.address1 = 'address_address1'}
		{$addresses_style.address2 = 'address_address2'}
		{$addresses_style.city = 'address_city'}
		{$addresses_style.country = 'address_country'}
		{$addresses_style.phone = 'address_phone'}
		{$addresses_style.phone_mobile = 'address_phone_mobile'}
		{$addresses_style.alias = 'address_title'}
	{/if}
	{if !$advanced_payment_api && ((!empty($delivery_option) && (!isset($isVirtualCart) || !$isVirtualCart)) OR $delivery->id || $invoice->id) && !$opc}
		<div class="order_delivery clearfix row">
			{if !isset($formattedAddresses) || (count($formattedAddresses.invoice) == 0 && count($formattedAddresses.delivery) == 0) || (count($formattedAddresses.invoice.formated) == 0 && count($formattedAddresses.delivery.formated) == 0)}
				{if $delivery->id}
					<div class="col-xs-12 col-sm-6"{if !$have_non_virtual_products} style="display: none;"{/if}>
						<ul id="delivery_address" class="address item box">
							<li><h3 class="page-subheading">{l s='Delivery address'}&nbsp;<span class="address_alias">({$delivery->alias})</span></h3></li>
							{if $delivery->company}<li class="address_company">{$delivery->company|escape:'html':'UTF-8'}</li>{/if}
							<li class="address_name">{$delivery->firstname|escape:'html':'UTF-8'} {$delivery->lastname|escape:'html':'UTF-8'}</li>
							<li class="address_address1">{$delivery->address1|escape:'html':'UTF-8'}</li>
							{if $delivery->address2}<li class="address_address2">{$delivery->address2|escape:'html':'UTF-8'}</li>{/if}
							<li class="address_city">{$delivery->postcode|escape:'html':'UTF-8'} {$delivery->city|escape:'html':'UTF-8'}</li>
							<li class="address_country">{$delivery->country|escape:'html':'UTF-8'} {if $delivery_state}({$delivery_state|escape:'html':'UTF-8'}){/if}</li>
						</ul>
					</div>
				{/if}
				{if $invoice->id}
					<div class="col-xs-12 col-sm-6">
						<ul id="invoice_address" class="address alternate_item box">
							<li><h3 class="page-subheading">{l s='Invoice address'}&nbsp;<span class="address_alias">({$invoice->alias})</span></h3></li>
							{if $invoice->company}<li class="address_company">{$invoice->company|escape:'html':'UTF-8'}</li>{/if}
							<li class="address_name">{$invoice->firstname|escape:'html':'UTF-8'} {$invoice->lastname|escape:'html':'UTF-8'}</li>
							<li class="address_address1">{$invoice->address1|escape:'html':'UTF-8'}</li>
							{if $invoice->address2}<li class="address_address2">{$invoice->address2|escape:'html':'UTF-8'}</li>{/if}
							<li class="address_city">{$invoice->postcode|escape:'html':'UTF-8'} {$invoice->city|escape:'html':'UTF-8'}</li>
							<li class="address_country">{$invoice->country|escape:'html':'UTF-8'} {if $invoice_state}({$invoice_state|escape:'html':'UTF-8'}){/if}</li>
						</ul>
					</div>
				{/if}
			{else}
				{foreach from=$formattedAddresses key=k item=address}
					<div class="col-xs-12 col-sm-6"{if $k == 'delivery' && !$have_non_virtual_products} style="display: none;"{/if}>
						<ul class="address {if $address@last}last_item{elseif $address@first}first_item{/if} {if $address@index % 2}alternate_item{else}item{/if} box">
							<li>
								<h3 class="page-subheading">
									{if $k eq 'invoice'}
										{l s='Invoice address'}
									{elseif $k eq 'delivery' && $delivery->id}
										{l s='Delivery address'}
									{/if}
									{if isset($address.object.alias)}
										<span class="address_alias">({$address.object.alias})</span>
									{/if}
								</h3>
							</li>
							{foreach $address.ordered as $pattern}
								{assign var=addressKey value=" "|explode:$pattern}
								{assign var=addedli value=false}
								{foreach from=$addressKey item=key name=foo}
								{$key_str = $key|regex_replace:AddressFormat::_CLEANING_REGEX_:""}
									{if isset($address.formated[$key_str]) && !empty($address.formated[$key_str])}
										{if (!$addedli)}
											{$addedli = true}
											<li><span class="{if isset($addresses_style[$key_str])}{$addresses_style[$key_str]}{/if}">
										{/if}
										{$address.formated[$key_str]|escape:'html':'UTF-8'}
									{/if}
									{if ($smarty.foreach.foo.last && $addedli)}
										</span></li>
									{/if}
								{/foreach}
							{/foreach}
						</ul>
					</div>
				{/foreach}
			{/if}
		</div>
	{/if}
	<div id="HOOK_SHOPPING_CART">{$HOOK_SHOPPING_CART}</div>
	{* <p class="cart_navigation clearfix">
		{if !$opc}
			<a  href="{if $back}{$link->getPageLink('order', true, NULL, 'step=1&amp;back={$back}')|escape:'html':'UTF-8'}{else}{$link->getPageLink('order', true, NULL, 'step=1')|escape:'html':'UTF-8'}{/if}" class="button btn btn-default standard-checkout button-medium" title="{l s='Proceed to checkout'}">
				<span>{l s='Proceed to checkout'}<i class="icon-chevron-right right"></i></span>
			</a>
		{/if}
		<a href="{if (isset($smarty.server.HTTP_REFERER) && ($smarty.server.HTTP_REFERER == $link->getPageLink('order', true) || $smarty.server.HTTP_REFERER == $link->getPageLink('order-opc', true) || strstr($smarty.server.HTTP_REFERER, 'step='))) || !isset($smarty.server.HTTP_REFERER)}{$link->getPageLink('index')}{else}{$smarty.server.HTTP_REFERER|escape:'html':'UTF-8'|secureReferrer}{/if}" class="button-exclusive btn btn-default" title="{l s='Continue shopping'}">
			<i class="icon-chevron-left"></i>{l s='Continue shopping'}
		</a>
	</p> *}
	<div class="clear"></div>
	<div class="cart_navigation_extra">
		<div id="HOOK_SHOPPING_CART_EXTRA">{if isset($HOOK_SHOPPING_CART_EXTRA)}{$HOOK_SHOPPING_CART_EXTRA}{/if}</div>
	</div>
	{strip}
		{addJsDef deliveryAddress=$cart->id_address_delivery|intval}
		{addJsDefL name=txtProduct}{l s='product' js=1}{/addJsDefL}
		{addJsDefL name=txtProducts}{l s='products' js=1}{/addJsDefL}
	{/strip}
{/if}

{* Fancybox for extra demands*}
<div style="display:none;" id="rooms_extra_demands">
	<div id="rooms_type_extra_demands">
		<div class="panel">
			<div class="rooms_extra_demands_head">
				<h3>{l s='Additional Facilities'}</h3>
				<p class="rooms_extra_demands_text">{l s='Add below additional facilities to the rooms for better hotel experience'}</p>
			</div>
			<div id="room_type_demands_desc"></div>
		</div>
	</div>
</div>