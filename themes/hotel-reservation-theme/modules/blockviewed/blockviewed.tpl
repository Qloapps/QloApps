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

<!-- Block Viewed products -->
<div id="viewed-products_block_left" class="block">
	<p class="title_block">{l s='Viewed products' mod='blockviewed'}</p>
	<div class="block_content products-block">
		<ul>
			{foreach from=$productsViewedObj item=viewedProduct name=myLoop}
				<li class="clearfix{if $smarty.foreach.myLoop.last} last_item{elseif $smarty.foreach.myLoop.first} first_item{else} item{/if}">
					<a
					class="products-block-image" 
					href="{$viewedProduct->product_link|escape:'html':'UTF-8'}" 
					title="{l s='More about %s' mod='blockviewed' sprintf=[$viewedProduct->name|escape:'html':'UTF-8']}" >
						<img 
						src="{if isset($viewedProduct->id_image) && $viewedProduct->id_image}{$link->getImageLink($viewedProduct->link_rewrite, $viewedProduct->cover, 'small_default')}{else}{$img_prod_dir}{$lang_iso}-default-medium_default.jpg{/if}" 
						alt="{$viewedProduct->legend|escape:'html':'UTF-8'}" />
					</a>
					<div class="product-content">
						<h5>
							<a class="product-name" 
							href="{$viewedProduct->product_link|escape:'html':'UTF-8'}" 
							title="{l s='More about %s' mod='blockviewed' sprintf=[$viewedProduct->name|escape:'html':'UTF-8']}">
								{$viewedProduct->name|truncate:25:'...'|escape:'html':'UTF-8'}
							</a>
						</h5>
						<p class="product-description">{$viewedProduct->description_short|strip_tags:'UTF-8'|truncate:40}</p>
					</div>
				</li>
			{/foreach}
		</ul>
	</div>
</div>
