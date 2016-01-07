{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
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
{include file="$tpl_dir./errors.tpl"}

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
	<div id="order-detail-content" class="table_block table-responsive">
		<table id="cart_summary" class="table table-bordered {if $PS_STOCK_MANAGEMENT}stock-management-on{else}stock-management-off{/if}">
			<thead>
				<tr class="table_head">
					<th class="cart_product">{l s='Room Image'}</th>
					<th class="cart_description">{l s='Room Description'}</th>
					<th>{l s='Room Capacity'}</th>
					<th class="cart_unit">{l s='Unit Price'}</th>
					<th>{l s='Rooms'}</th>
					<th>{l s='Check-in Date'}</th>
					<th>{l s='Check-out Date'}</th>
					<th class="cart_delete last_item">&nbsp;</th>
					<th class="cart_total">{l s='Total'}</th>
				</tr>
			</thead>
			<tfoot>
				{assign var='rowspan_total' value=2+$total_discounts_num+$total_wrapping_taxes_num}

				{if $use_taxes && $show_taxes && $total_tax != 0}
					{assign var='rowspan_total' value=$rowspan_total+1}
				{/if}

				{if $priceDisplay != 0}
					{assign var='rowspan_total' value=$rowspan_total+1}
				{/if}

				<!-- {if $total_shipping_tax_exc <= 0 && (!isset($isVirtualCart) || !$isVirtualCart) && $free_ship}
					{assign var='rowspan_total' value=$rowspan_total+1}
				{else}
					{if $use_taxes && $total_shipping_tax_exc != $total_shipping}
						{if $priceDisplay && $total_shipping_tax_exc > 0}
							{assign var='rowspan_total' value=$rowspan_total+1}
						{elseif $total_shipping > 0}
							{assign var='rowspan_total' value=$rowspan_total+1}
						{/if}
					{elseif $total_shipping_tax_exc > 0}
						{assign var='rowspan_total' value=$rowspan_total+1}
					{/if}
				{/if} -->

				{if $use_taxes}
					{if $priceDisplay}
						<tr class="cart_total_price table_tfoot">
							<td rowspan="{$rowspan_total}" colspan="3" id="cart_voucher" class="cart_voucher">
								{if $voucherAllowed}
									{if isset($errors_discount) && $errors_discount}
										<ul class="alert alert-danger">
											{foreach $errors_discount as $k=>$error}
												<li>{$error|escape:'html':'UTF-8'}</li>
											{/foreach}
										</ul>
									{/if}
									<form action="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}" method="post" id="voucher">
										<fieldset>
											<h4>{l s='Vouchers'}</h4>
											<input type="text" class="discount_name form-control" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" />
											<input type="hidden" name="submitDiscount" />
											<button type="submit" name="submitAddDiscount" class="btn btn-default"><span>{l s='OK'}</span></button>
										</fieldset>
									</form>
									{if $displayVouchers}
										<p id="title" class="title-offers">{l s='Take advantage of our exclusive offers:'}</p>
										<div id="display_cart_vouchers">
											{foreach $displayVouchers as $voucher}
												{if $voucher.code != ''}<span class="voucher_name" data-code="{$voucher.code|escape:'html':'UTF-8'}">{$voucher.code|escape:'html':'UTF-8'}</span> - {/if}{$voucher.name}<br />
											{/foreach}
										</div>
									{/if}
								{/if}
							</td>
							<td colspan="3" class="text-right">{if $display_tax_label}{l s='Total products (tax excl.)'}{else}{l s='Total products'}{/if}</td>
							<td colspan="3" class="price" id="total_product">{displayPrice price=$total_products}</td>
						</tr>
					{else}
						<tr class="cart_total_price table_tfoot">
							<td rowspan="{$rowspan_total}" colspan="3" id="cart_voucher" class="cart_voucher">
								{if $voucherAllowed}
									{if isset($errors_discount) && $errors_discount}
										<ul class="alert alert-danger">
											{foreach $errors_discount as $k=>$error}
												<li>{$error|escape:'html':'UTF-8'}</li>
											{/foreach}
										</ul>
									{/if}
									<form action="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}" method="post" id="voucher">
										<fieldset>
											<h4>{l s='Vouchers'}</h4>
											<input type="text" class="discount_name form-control" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" />
											<input type="hidden" name="submitDiscount" />
											<button type="submit" name="submitAddDiscount" class="btn btn-default"><span>{l s='OK'}</span></button>
										</fieldset>
									</form>
									{if $displayVouchers}
										<p id="title" class="title-offers">{l s='Take advantage of our exclusive offers:'}</p>
										<div id="display_cart_vouchers">
											{foreach $displayVouchers as $voucher}
												{if $voucher.code != ''}<span class="voucher_name" data-code="{$voucher.code|escape:'html':'UTF-8'}">{$voucher.code|escape:'html':'UTF-8'}</span> - {/if}{$voucher.name}<br />
											{/foreach}
										</div>
									{/if}
								{/if}
							</td>
							<td colspan="3" class="text-right">{if $display_tax_label}{l s='Total products (tax incl.)'}{else}{l s='Total products'}{/if}</td>
							<td colspan="3" class="price" id="total_product">{displayPrice price=$total_products_wt}</td>
						</tr>
					{/if}
				{else}
					<tr class="cart_total_price table_tfoot">
						<td rowspan="{$rowspan_total}" colspan="3" id="cart_voucher" class="cart_voucher">
							{if $voucherAllowed}
								{if isset($errors_discount) && $errors_discount}
									<ul class="alert alert-danger">
										{foreach $errors_discount as $k=>$error}
											<li>{$error|escape:'html':'UTF-8'}</li>
										{/foreach}
									</ul>
								{/if}
								<form action="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}" method="post" id="voucher">
									<fieldset>
										<h4>{l s='Vouchers'}</h4>
										<input type="text" class="discount_name form-control" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" />
										<input type="hidden" name="submitDiscount" />
										<button type="submit" name="submitAddDiscount" class="btn btn-default">
											<span>{l s='OK'}</span>
										</button>
									</fieldset>
								</form>
								{if $displayVouchers}
									<p id="title" class="title-offers">{l s='Take advantage of our exclusive offers:'}</p>
									<div id="display_cart_vouchers">
										{foreach $displayVouchers as $voucher}
											{if $voucher.code != ''}<span class="voucher_name" data-code="{$voucher.code|escape:'html':'UTF-8'}">{$voucher.code|escape:'html':'UTF-8'}</span> - {/if}{$voucher.name}<br />
										{/foreach}
									</div>
								{/if}
							{/if}
						</td>
						<td colspan="3" class="text-right">{l s='Total products'}</td>
						<td colspan="3" class="price" id="total_product">{displayPrice price=$total_products}</td>
					</tr>
				{/if}
				<tr{if $total_wrapping == 0} style="display: none;"{/if} class="table_tfoot">
					<td colspan="3" class="text-right">
						{if $use_taxes}
							{if $display_tax_label}{l s='Total gift wrapping (tax incl.)'}{else}{l s='Total gift-wrapping cost'}{/if}
						{else}
							{l s='Total gift-wrapping cost'}
						{/if}
					</td>
					<td colspan="3" class="price-discount price" id="total_wrapping">
						{if $use_taxes}
							{if $priceDisplay}
								{displayPrice price=$total_wrapping_tax_exc}
							{else}
								{displayPrice price=$total_wrapping}
							{/if}
						{else}
							{displayPrice price=$total_wrapping_tax_exc}
						{/if}
					</td>
				</tr>
				<!-- {if $total_shipping_tax_exc <= 0 && (!isset($isVirtualCart) || !$isVirtualCart) && $free_ship}
					<tr class="cart_total_delivery{if !$opc && (!isset($cart->id_address_delivery) || !$cart->id_address_delivery)} unvisible{/if} table_tfoot">
						<td colspan="3" class="text-right">{l s='Total shipping'}</td>
						<td colspan="3" class="price" id="total_shipping">{l s='Free shipping!'}</td>
					</tr>
				{else}
					{if $use_taxes && $total_shipping_tax_exc != $total_shipping}
						{if $priceDisplay}
							<tr class="table_tfoot cart_total_delivery{if $total_shipping_tax_exc <= 0} unvisible{/if}">
								<td colspan="3" class="text-right">{if $display_tax_label}{l s='Total shipping (tax excl.)'}{else}{l s='Total shipping'}{/if}</td>
								<td colspan="3" class="price" id="total_shipping">{displayPrice price=$total_shipping_tax_exc}</td>
							</tr>
						{else}
							<tr class="table_tfoot cart_total_delivery{if $total_shipping <= 0} unvisible{/if}">
								<td colspan="3" class="text-right">{if $display_tax_label}{l s='Total shipping (tax incl.)'}{else}{l s='Total shipping'}{/if}</td>
								<td colspan="3" class="price" id="total_shipping" >{displayPrice price=$total_shipping}</td>
							</tr>
						{/if}
					{else}
						<tr class="table_tfoot cart_total_delivery{if $total_shipping_tax_exc <= 0} unvisible{/if}">
							<td colspan="3" class="text-right">{l s='Total shipping'}</td>
							<td colspan="3" class="price" id="total_shipping" >{displayPrice price=$total_shipping_tax_exc}</td>
						</tr>
					{/if}
				{/if} -->
				<tr class="table_tfoot cart_total_voucher{if $total_discounts == 0} unvisible{/if}">
					<td colspan="3" class="text-right">
						{if $display_tax_label}
							{if $use_taxes && $priceDisplay == 0}
								{l s='Total vouchers (tax incl.)'}
							{else}
								{l s='Total vouchers (tax excl.)'}
							{/if}
						{else}
							{l s='Total vouchers'}
						{/if}
					</td>
					<td colspan="3" class="price-discount price" id="total_discount">
						{if $use_taxes && $priceDisplay == 0}
							{assign var='total_discounts_negative' value=$total_discounts * -1}
						{else}
							{assign var='total_discounts_negative' value=$total_discounts_tax_exc * -1}
						{/if}
						{displayPrice price=$total_discounts_negative}
					</td>
				</tr>
				{if $use_taxes && $show_taxes && $total_tax != 0 }
					{if $priceDisplay != 0}
					<tr class="table_tfoot table_total_tr cart_total_price">
						<td colspan="3" class="text-right">{if $display_tax_label}{l s='Total (tax excl.)'}{else}{l s='Total'}{/if}</td>
						<td colspan="3" class="price" id="total_price_without_tax">{displayPrice price=$total_price_without_tax}</td>
					</tr>
					{/if}
					<tr class="table_tfoot cart_total_tax">
						<td colspan="3" class="text-right">{l s='Tax'}</td>
						<td colspan="3" class="price" id="total_tax">{displayPrice price=$total_tax}</td>
					</tr>
				{/if}
				<tr class="table_tfoot table_total_tr cart_total_price">
					<td colspan="3" class="total_price_container text-right">
						<span>{l s='Total'}</span>
                        <div class="hookDisplayProductPriceBlock-price">
                            {hook h="displayCartTotalPriceLabel"}
                        </div>
					</td>
					{if $use_taxes}
						<td colspan="3" class="price" id="total_price_container">
							<span id="total_price">{displayPrice price=$total_price}</span>
						</td>
					{else}
						<td colspan="3" class="price" id="total_price_container">
							<span id="total_price">{displayPrice price=$total_price_without_tax}</span>
						</td>
					{/if}
				</tr>
			</tfoot>
			<tbody>
				{if isset($cart_htl_data)}
					{foreach from=$cart_htl_data key=data_k item=data_v}
						{foreach from=$data_v['date_diff'] key=rm_k item=rm_v}
							<tr class="table_body">
								<td class="cart_product">
									<a href="{$link->getProductLink($data_v['id_product'])}">
										<img src="{$data_v['cover_img']}" class="img-responsive" />
									</a>
								</td>
								<td class="cart_description">
									<p class="product-name">
										<a href="{$link->getProductLink($data_v['id_product'])}">
											{$data_v['name']}
										</a>
									</p>
								</td>
								<td>
									<p class="text-left">
										{$data_v['adult']} {l s='Adults'}, {$data_v['children']} {l s='Children'}
									</p>
								</td>
								<td class="cart_unit">
									<p class="text-center">
										{displayPrice price=$data_v['unit_price']}
									</p>
								</td>
								<td class="text-center">
									<p>
										{$rm_v['num_rm']}
									</p>
								</td>
								<td class="text-center">
									<p>
										{$rm_v['data_form']|date_format:"%d %b %Y"}
									</p>
								</td>
								<td class="text-center">
									<p>
										{$rm_v['data_to']|date_format:"%d %b %Y"}
									</p>
								</td>
								<td class="text-center">
									<a href="{$rm_v['link']}"><i class="icon-trash"></i></a>
								</td>
								<td class="cart_total text-left">
									<p class="text-left">
										{displayPrice price=$rm_v['amount']}
									</p>
								</td>
							</tr>
						{/foreach}
					{/foreach}
				{/if}
			</tbody>

			{if sizeof($discounts)}
				<tbody>
					{foreach $discounts as $discount}
					{if ((float)$discount.value_real == 0 && $discount.free_shipping != 1) || ((float)$discount.value_real == 0 && $discount.code == '')}
						{continue}
					{/if}
						<tr class="table_body cart_discount {if $discount@last}last_item{elseif $discount@first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount}">
							<td class="cart_discount_name" colspan="3">{$discount.name}</td>
							<td class="cart_discount_price">
								<span class="price-discount">
								{if !$priceDisplay}{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}{/if}
								</span>
							</td>
							<td class="cart_discount_delete">1</td>
							<td colspan="2"></td>
							<td class="price_discount_del text-center">
								{if strlen($discount.code)}
									<a
										href="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}?deleteDiscount={$discount.id_discount}"
										class="price_discount_delete"
										title="{l s='Delete'}">
										<i class="icon-trash"></i>
									</a>
								{/if}
							</td>
							<td class="cart_discount_price">
								<span class="price-discount price">{if !$priceDisplay}{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}{/if}</span>
							</td>
						</tr>
					{/foreach}
				</tbody>
			{/if}
		</table>
	</div> <!-- end order-detail-content -->

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
		{$addresses_style.vat_number = 'address_company'}
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