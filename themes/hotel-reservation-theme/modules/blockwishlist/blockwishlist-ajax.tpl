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
{if $products}
	<dl class="products">
		{foreach from=$products item=product name=i}
			<dt class="{if $smarty.foreach.i.first}first_item{elseif $smarty.foreach.i.last}last_item{else}item{/if}">
				<span class="quantity-formated">
					<span class="quantity">{$product.quantity|intval}</span>x
				</span>
				<a class="cart_block_product_name" href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category_rewrite)|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}">
					{$product.name|truncate:13:'...'|escape:'html':'UTF-8'}
				</a>
				<a class="ajax_cart_block_remove_link" href="javascript:;" onclick="javascript:WishlistCart('wishlist_block_list', 'delete', '{$product.id_product}', {$product.id_product_attribute}, '0');" rel="nofollow" title="{l s='remove this product from my wishlist' mod='blockwishlist'}">
					<i class="icon-remove-sign"></i>
				</a>
			</dt>
			{if isset($product.attributes_small)}
			<dd class="{if $smarty.foreach.i.first}first_item{elseif $smarty.foreach.i.last}last_item{else}item{/if}">
				<a href="{$link->getProductLink($product.id_product, $product.link_rewrite)|escape:'html':'UTF-8'}" title="{l s='Product detail' mod='blockwishlist'}">
					{$product.attributes_small|escape:'html':'UTF-8'}
				</a>
			</dd>
			{/if}
		{/foreach}
	</dl>
{else}
	<dl class="products no-products">
		{if isset($error) && $error}
			<dt>{l s='You must create a wishlist before adding products' mod='blockwishlist'}</dt>
		{else}
			<dt>{l s='No products' mod='blockwishlist'}</dt>
		{/if}
	</dl>
{/if}