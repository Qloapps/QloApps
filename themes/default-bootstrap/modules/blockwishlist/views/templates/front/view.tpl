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
<div id="view_wishlist">
    {capture name=path}
        <a href="{$link->getPageLink('my-account', true)|escape:'html'}">{l s='My account' mod='blockwishlist'}</a>
        <span class="navigation-pipe">{$navigationPipe}</span>
        <a href="{$link->getModuleLink('blockwishlist', 'mywishlist')|escape:'html'}">{l s='My wishlists' mod='blockwishlist'}</a>
        <span class="navigation-pipe">{$navigationPipe}</span>
        {$current_wishlist.name}
    {/capture}

    <h1 class="page-heading bottom-indent">
        {l s='Wishlist' mod='blockwishlist'}
    </h1>
    {if $wishlists}
        <p>
            <strong class="dark">
                {l s='Other wishlists of %1s %2s:' sprintf=[$current_wishlist.firstname, $current_wishlist.lastname] mod='blockwishlist'}
            </strong>
            {foreach from=$wishlists item=wishlist name=i}
                {if $wishlist.id_wishlist != $current_wishlist.id_wishlist}
                    <a href="{$link->getModuleLink('blockwishlist', 'view', ['token' => $wishlist.token])|escape:'html':'UTF-8'}" rel="nofollow" title="{$wishlist.name}">
                        {$wishlist.name}
                    </a>
                    {if !$smarty.foreach.i.last}
                        /
                    {/if}
                {/if}
            {/foreach}
        </p>
    {/if}

    <div class="wlp_bought">
        {assign var='nbItemsPerLine' value=3}
        {assign var='nbItemsPerLineTablet' value=2}
        {assign var='nbLi' value=$products|@count}
        {math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
        {math equation="nbLi/nbItemsPerLineTablet" nbLi=$nbLi nbItemsPerLineTablet=$nbItemsPerLineTablet assign=nbLinesTablet}
        <ul class="row wlp_bought_list">
            {foreach from=$products item=product name=i}
                {math equation="(total%perLine)" total=$smarty.foreach.i.total perLine=$nbItemsPerLine assign=totModulo}
                {math equation="(total%perLineT)" total=$smarty.foreach.i.total perLineT=$nbItemsPerLineTablet assign=totModuloTablet}
                {if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
                {if $totModuloTablet == 0}{assign var='totModuloTablet' value=$nbItemsPerLineTablet}{/if}
                <li
                        id="wlp_{$product.id_product}_{$product.id_product_attribute}"
                        class="ajax_block_product col-xs-12 col-sm-6 col-md-4 {if $smarty.foreach.i.iteration%$nbItemsPerLine == 0} last-in-line{elseif $smarty.foreach.i.iteration%$nbItemsPerLine == 1} first-in-line{/if} {if $smarty.foreach.i.iteration > ($smarty.foreach.i.total - $totModulo)}last-line{/if} {if $smarty.foreach.i.iteration%$nbItemsPerLineTablet == 0}last-item-of-tablet-line{elseif $smarty.foreach.i.iteration%$nbItemsPerLineTablet == 1}first-item-of-tablet-line{/if} {if $smarty.foreach.i.iteration > ($smarty.foreach.i.total - $totModuloTablet)}last-tablet-line{/if}">
                    <div class="row">
                        <div class="col-xs-6 col-sm-12">
                            <div class="product_image">
                                <a
                                        href="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite)|escape:'html':'UTF-8'}"
                                        title="{l s='Product detail' mod='blockwishlist'}">
                                    <img
                                            class="replace-2x img-responsive"
                                            src="{$link->getImageLink($product.link_rewrite, $product.cover, 'home_default')|escape:'html':'UTF-8'}"
                                            alt="{$product.name|escape:'html':'UTF-8'}"/>
                                </a>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-12">
                            <div class="product_infos">
                                <p id="s_title" class="product-name">
                                    {$product.name|truncate:30:'...'|escape:'html':'UTF-8'}
                                    {if isset($product.attributes_small)}
                                        <a
                                                href="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite)|escape:'html':'UTF-8'}"
                                                title="{l s='Product detail' mod='blockwishlist'}">
                                            <small>{$product.attributes_small|escape:'html':'UTF-8'}</small>
                                        </a>
                                    {/if}
                                </p>
                                <div class="wishlist_product_detail">
                                    <p class="form-group">
                                        <label for="quantity_{$product.id_product}_{$product.id_product_attribute}">
                                            {l s='Quantity' mod='blockwishlist'}:
                                        </label>
                                        <input class="form-control grey" type="text"
                                               id="quantity_{$product.id_product}_{$product.id_product_attribute}"
                                               value="{$product.quantity|intval}" size="3"/>
                                    </p>

                                    <p class="form-group selector1">
                                        <span><strong>{l s='Priority' mod='blockwishlist'}:</strong> {$product.priority_name}</span>
                                    </p>
                                    <div class="btn_action">
                                        {if (isset($product.attribute_quantity) && $product.attribute_quantity >= 1) || (!isset($product.attribute_quantity) && $product.product_quantity >= 1) || (isset($product.allow_oosp) && $product.allow_oosp)}
                                            {if !$ajax}
                                                <form id="addtocart_{$product.id_product|intval}_{$product.id_product_attribute|intval}"
                                                      action="{$link->getPageLink('cart')|escape:'html':'UTF-8'}"
                                                      method="post">
                                                    <p class="hidden">
                                                        <input type="hidden" name="id_product"
                                                               value="{$product.id_product|intval}"
                                                               id="product_page_product_id"/>
                                                        <input type="hidden" name="add" value="1"/>
                                                        <input type="hidden" name="token" value="{$token}"/>
                                                        <input type="hidden" name="id_product_attribute"
                                                               id="idCombination"
                                                               value="{$product.id_product_attribute|intval}"/>
                                                    </p>
                                                </form>
                                            {/if}
                                            <a
                                                    href="javascript:void(0);"
                                                    class="button ajax_add_to_cart_button btn btn-default"
                                                    onclick="WishlistBuyProduct('{$token|escape:'html':'UTF-8'}', '{$product.id_product}', '{$product.id_product_attribute}', '{$product.id_product}_{$product.id_product_attribute}', this, {$ajax});"
                                                    title="{l s='Add to cart' mod='blockwishlist'}"
                                                    rel="nofollow">
                                                <span>{l s='Add to cart' mod='blockwishlist'}</span>
                                            </a>
                                        {else}
                                            <span class="button ajax_add_to_cart_button btn btn-default disabled">
												<span>{l s='Add to cart' mod='blockwishlist'}</span>
											</span>
                                        {/if}
                                        <a
                                                class="button lnk_view btn btn-default"
                                                href="{$link->getProductLink($product.id_product,  $product.link_rewrite, $product.category_rewrite)|escape:'html':'UTF-8'}"
                                                title="{l s='View' mod='blockwishlist'}"
                                                rel="nofollow">
                                            <span>{l s='View' mod='blockwishlist'}</span>
                                        </a>
                                    </div>
                                    <!-- .btn_action -->
                                </div>
                                <!-- .wishlist_product_detail -->
                            </div>
                            <!-- .product_infos -->
                        </div>
                    </div>
                </li>
            {/foreach}
        </ul>
    </div>
</div> <!-- #view_wishlist -->
