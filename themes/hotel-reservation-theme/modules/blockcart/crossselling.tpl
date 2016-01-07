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
<!-- {if isset($orderProducts) && count($orderProducts) > 0}
	<div class="crossseling-content">
		<h2>{l s='Customers who bought this product also bought:' mod='blockcart'}</h2>
		<div id="blockcart_list">
			<ul id="blockcart_caroucel">
				{foreach from=$orderProducts item='orderProduct' name=orderProduct}
					<li>
						<div class="product-image-container">
							<a href="{$orderProduct.link|escape:'html':'UTF-8'}" title="{$orderProduct.name|htmlspecialchars}" class="lnk_img">
								<img class="img-responsive" src="{$orderProduct.image}" alt="{$orderProduct.name|htmlspecialchars}" />
							</a>
						</div>
						<p class="product-name">
							<a href="{$orderProduct.link|escape:'html':'UTF-8'}" title="{$orderProduct.name|htmlspecialchars}">
								{$orderProduct.name|truncate:15:'...'|escape:'html':'UTF-8'}
							</a>
						</p>
						{if $orderProduct.show_price == 1 AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
							<span class="price_display">
								<span class="price">{convertPrice price=$orderProduct.displayed_price}</span>
							</span>
						{/if}
					</li>
				{/foreach}
			</ul>
		</div>
	</div>
{/if} -->