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

{if isset($service_product) && $service_product}
    <li class="row service-product-element">
        <div class="col-xs-4 col-sm-3 col-md-2">
            <a href="{$link->getImageLink($service_product.link_rewrite, $service_product.id_image, 'large_default')|escape:'html':'UTF-8'}" rel="htl-images{$service_product['id_product']}" class="fancybox" title="{if !empty($service_product.legend)}{$service_product.legend|escape:'html':'UTF-8'}{else}{$service_product.name|escape:'html':'UTF-8'}{/if}">
                <img class="img-responsive service-product-img" src="{$link->getImageLink($service_product.link_rewrite, $service_product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($service_product.legend)}{$service_product.legend|escape:'html':'UTF-8'}{else}{$service_product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($service_product.legend)}{$service_product.legend|escape:'html':'UTF-8'}{else}{$service_product.name|escape:'html':'UTF-8'}{/if}">
            </a>
            {foreach $service_product.images as $image}
                {if $image['cover'] == 0}
                    <a href="{$link->getImageLink($service_product.link_rewrite, $image.id_image, 'large_default')|escape:'html':'UTF-8'}" rel="htl-images{$service_product['id_product']}" class="fancybox hidden"  title="{if !empty($image.legend)}{$image.legend|escape:'html':'UTF-8'}{else}{$service_product.name|escape:'html':'UTF-8'}{/if}">
                    </a>
                {/if}
            {/foreach}
        </div>
        <div class="col-xs-8 col-sm-9 col-md-10">
            <div class="row">
                {block name='service_product_name'}
                    <div class="col-sm-12 clearfix service-product-block">
                        <span class="service-product-name">{$service_product['name']}</span>
                    </div>
                {/block}
                {block name='service_product_description'}
                    {if $service_product['description_short']}
                        <div class="col-sm-12 clearfix service-product-short-desc service-product-block">
                            {$service_product['description_short']}
                        </div>
                    {/if}
                {/block}

                <div class="col-sm-12 service_product_action_block">
                    {block name='service_products_actions_right'}
                        {if !$PS_CATALOG_MODE && !$order_date_restrict && ($service_product.show_price && !isset($restricted_country_mode))}
                            <div class="service-product-price">
                                {block name='service_product_price'}
                                    {if !$priceDisplay}{convertPrice price=$service_product.price_tax_incl}{else}{convertPrice price=$service_product.price_tax_exc}{/if}{if $service_product.price_calculation_method == Product::PRICE_CALCULATION_METHOD_PER_DAY}<span class="price-label">{l s='/Night'}</span>{/if}
                                {/block}
                                {if $service_product.allow_multiple_quantity && $service_product.available_for_order && $service_product.max_quantity > 0}
                                    <div class="service-max-quantity-info">
                                        {l s='Maximum'} {$service_product.max_quantity} {l s='quantity can be added'}.
                                    </div>
                                {/if}
                            </div>
                        {/if}
                    {/block}

                    {block name='service_products_actions_left'}
                        {if ($service_product.show_price && !isset($restricted_country_mode))}
                            {if $service_product.available_for_order && !$PS_CATALOG_MODE && !$order_date_restrict && !((isset($restricted_country_mode) && $restricted_country_mode))}
                                <div class="service-product-actions">
                                    {if $service_product.allow_multiple_quantity && $service_product.available_for_order}
                                        <div class="qty_container">
                                            <input type="hidden" class="room_service_product_qty" id="room_service_product_qty_{$service_product.id_product}" name="room_service_product_qty_{$service_product.id_product}" data-id-product="{$service_product.id_product}" data-max_quantity="{$service_product.max_quantity}" value="{if isset($service_product.quantity_added) && $service_product.quantity_added}{$service_product.quantity_added|escape:'html':'UTF-8'}{else}1{/if}">
                                            <div class="qty_count pull-left">
                                                <span>{if isset($service_product.quantity_added) && $service_product.quantity_added}{$service_product.quantity_added|escape:'html':'UTF-8'}{else}1{/if}</span>
                                            </div>
                                            <div class="qty_direction pull-left">
                                                <a href="#" class="btn btn-default quantity_up room_service_product_qty_up"><span><i class="icon-plus"></i></span></a>
                                                <a href="#" class="btn btn-default quantity_down room_service_product_qty_down"><span><i class="icon-minus"></i></span></a>
                                            </div>
                                        </div>
                                    {/if}
                                    <button class="btn btn-service-product {if isset($service_product.selected) && $service_product.selected} btn-danger remove_roomtype_product{else} btn-success add_roomtype_product{/if} select_room_service select_room_service_{$service_product.id_product} pull-right" data-id-product="{$service_product.id_product}">{if isset($service_product.selected) && $service_product.selected}{l s='Remove'}{else}{l s='Select'}{/if}</button>
                                </div>
                            {/if}
                        {/if}
                    {/block}
                </div>
            </div>
            {if !isset($product->id)}{assign var=id_product value=0}{else}{assign var=id_product value=$product->id}{/if}
            {block name='displayServiceProductListRowBottom'}
                {hook h='displayServiceProductListRowBottom' id_product=$id_product id_service_product=$service_product['id_product']}
            {/block}
        </div>
    </li>
{/if}
