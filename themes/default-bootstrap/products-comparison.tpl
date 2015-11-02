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
{capture name=path}{l s='Product Comparison'}{/capture}
<h1 class="page-heading">{l s='Product Comparison'}</h1>
{if $hasProduct}
	<div class="products_block table-responsive">
		<table id="product_comparison" class="table table-bordered">
			<tr>
				<td class="td_empty compare_extra_information">
					{$HOOK_COMPARE_EXTRA_INFORMATION}
					<span>{l s='Features:'}</span>
				</td>
				{assign var='taxes_behavior' value=false}
				{if $use_taxes && (!$priceDisplay  || $priceDisplay == 2)}
					{assign var='taxes_behavior' value=true}
				{/if}
				{foreach from=$products item=product name=for_products}
					{assign var='replace_id' value=$product->id|cat:'|'}
					<td class="ajax_block_product comparison_infos product-block product-{$product->id}">
						<div class="remove">
							<a class="cmp_remove" href="{$link->getPageLink('products-comparison', true)|escape:'html':'UTF-8'}" title="{l s='Remove'}" data-id-product="{$product->id}">
								<i class="icon-trash"></i>
							</a>
						</div>
						<div class="product-image-block">
							<a
							class="product_image"
							href="{$product->getLink()|escape:'html':'UTF-8'}"
							title="{$product->name|escape:'html':'UTF-8'}">
								<img
								class="img-responsive"
								src="{$link->getImageLink($product->link_rewrite, $product->id_image, 'home_default')|escape:'html':'UTF-8'}"
								alt="{$product->name|escape:'html':'UTF-8'}" />
							</a>
							{if isset($product->new) && $product->new == 1}
								<a class="new-box" href="{$product->getLink()|escape:'html':'UTF-8'}">
									<span class="new-label">{l s='New'}</span>
								</a>
							{/if}
							{if isset($product->show_price) && $product->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
								{if $product->on_sale}
									<a class="sale-box" href="{$product->getLink()|escape:'html':'UTF-8'}">
										<span class="sale-label">{l s='Sale!'}</span>
									</a>
								{/if}
							{/if}
						</div> <!-- end product-image-block -->
						<h5>
							<a class="product-name"	href="{$product->getLink()|escape:'html':'UTF-8'}" title="{$product->name|truncate:32:'...'|escape:'html':'UTF-8'}">
								{$product->name|truncate:45:'...'|escape:'html':'UTF-8'}
							</a>
						</h5>
						<div class="prices-container">
							{if isset($product->show_price) && $product->show_price && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
								<span class="price product-price">{convertPrice price=$product->getPrice($taxes_behavior)}</span>
								{hook h="displayProductPriceBlock" id_product=$product->id type="price"}
								{if isset($product->specificPrice) && $product->specificPrice}
									{if {$product->specificPrice.reduction_type == 'percentage'}}
										<span class="old-price product-price">
											{displayWtPrice p=($product->getPrice(true, null, 6, null, false, false))}
										</span>
										<span class="price-percent-reduction">
											-{$product->specificPrice.reduction*100|floatval}%
										</span>
									{else}
										<span class="old-price product-price">
											{convertPrice price=($product->getPrice($taxes_behavior) + $product->specificPrice.reduction)}
										</span>
										<span class="price-percent-reduction">
											-{convertPrice price=$product->specificPrice.reduction}
										</span>
									{/if}
									{hook h="displayProductPriceBlock" product=$product type="old_price"}
								{/if}
								{hook h="displayProductPriceBlock" product=$product type="price"}
								{if $product->on_sale}
									{elseif $product->specificPrice AND $product->specificPrice.reduction}
										<div class="product_discount">
											<span class="reduced-price">{l s='Reduced price!'}</span>
										</div>
									{/if}
									{if !empty($product->unity) && $product->unit_price_ratio > 0.000000}
										{math equation="pprice / punit_price"  pprice=$product->getPrice($taxes_behavior)  punit_price=$product->unit_price_ratio assign=unit_price}
										<span class="comparison_unit_price">
											&nbsp;{convertPrice price=$unit_price} {l s='per %s' sprintf=$product->unity|escape:'html':'UTF-8'}
										</span>
										{hook h="displayProductPriceBlock" product=$product type="unit_price"}
									{else}
								{/if}
							{/if}
						</div> <!-- end prices-container -->
						<div class="product_desc">
							{$product->description_short|strip_tags|truncate:60:'...'}
						</div>
						<div class="comparison_product_infos">
							<p class="comparison_availability_statut">
								{if !(($product->quantity <= 0 && !$product->available_later) OR ($product->quantity != 0 && !$product->available_now) OR !$product->available_for_order OR $PS_CATALOG_MODE)}
									<span class="availability_label">{l s='Availability:'}</span>
									<span class="availability_value"{if $product->quantity <= 0} class="warning-inline"{/if}>
										{if $product->quantity <= 0}
											{if $product->allow_oosp}
												{$product->available_later|escape:'html':'UTF-8'}
											{else}
												{l s='This product is no longer in stock.'}
											{/if}
										{else}
											{$product->available_now|escape:'html':'UTF-8'}
										{/if}
									</span>
								{/if}
							</p>
							{if !$product->is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}
							{hook h="displayProductPriceBlock" product=$product type="weight"}
							<div class="clearfix">
								<div class="button-container">
									{if (!$product->hasAttributes() OR (isset($add_prod_display) AND ($add_prod_display == 1))) AND $product->minimal_quantity == 1 AND $product->customizable != 2 AND !$PS_CATALOG_MODE}
										{if ($product->quantity > 0 OR $product->allow_oosp)}
											<a class="button ajax_add_to_cart_button btn btn-default" data-id-product="{$product->id}" href="{$link->getPageLink('cart', true, NULL, "qty=1&amp;id_product={$product->id}&amp;token={$static_token}&amp;add")|escape:'html':'UTF-8'}" title="{l s='Add to cart'}">
												<span>{l s='Add to cart'}</span>
											</a>
										{else}
											<span class="ajax_add_to_cart_button button btn btn-default disabled">
												<span>{l s='Add to cart'}</span>
											</span>
										{/if}
									{/if}
									<a class="button lnk_view btn btn-default" href="{$product->getLink()|escape:'html':'UTF-8'}" title="{l s='View'}">
										<span>{l s='View'}</span>
									</a>
								</div>
							</div>
						</div> <!-- end comparison_product_infos -->
					</td>
				{/foreach}
			</tr>
			{if $ordered_features}
				{foreach from=$ordered_features item=feature}
					<tr>
						{cycle values='comparison_feature_odd,comparison_feature_even' assign='classname'}
						<td class="{$classname} feature-name" >
							<strong>{$feature.name|escape:'html':'UTF-8'}</strong>
						</td>
						{foreach from=$products item=product name=for_products}
							{assign var='product_id' value=$product->id}
							{assign var='feature_id' value=$feature.id_feature}
							{if isset($product_features[$product_id])}
								{assign var='tab' value=$product_features[$product_id]}
								<td class="{$classname} comparison_infos product-{$product->id}">{if (isset($tab[$feature_id]))}{$tab[$feature_id]|escape:'html':'UTF-8'}{/if}</td>
							{else}
								<td class="{$classname} comparison_infos product-{$product->id}"></td>
							{/if}
						{/foreach}
					</tr>
				{/foreach}
			{else}
				<tr>
					<td></td>
					<td colspan="{$products|@count}" class="text-center">{l s='No features to compare'}</td>
				</tr>
			{/if}
			{$HOOK_EXTRA_PRODUCT_COMPARISON}
		</table>
	</div> <!-- end products_block -->
{else}
	<p class="alert alert-warning">{l s='There are no products selected for comparison.'}</p>
{/if}
<ul class="footer_link">
	<li>
		<a class="button lnk_view btn btn-default" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}">
			<span><i class="icon-chevron-left left"></i>{l s='Continue Shopping'}</span>
		</a>
	</li>
</ul>
