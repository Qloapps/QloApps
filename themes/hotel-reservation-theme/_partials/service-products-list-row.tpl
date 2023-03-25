{**
* 2010-2023 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2023 Webkul IN
* @license LICENSE.txt
*}

{if isset($product) && $product}
    <li class="row service-product-element">
        <div class="col-xs-4 col-sm-3 col-md-2">
            <a href="{$link->getImageLink($product.link_rewrite, $product.id_image, 'large_default')|escape:'html':'UTF-8'}" rel="htl-images{$product['id_product']}" class="fancybox" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}">
                <img class="img-responsive service-product-img" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}">
            </a>
            {foreach $product.images as $image}
                {if $image['cover'] == 0}
                    <a href="{$link->getImageLink($product.link_rewrite, $image.id_image, 'large_default')|escape:'html':'UTF-8'}" rel="htl-images{$product['id_product']}" class="fancybox hidden">
                    </a>
                {/if}
            {/foreach}
        </div>
        <div class="col-xs-8 col-sm-9 col-md-10">
            <div class="row">
                <div class="col-sm-12 clearfix service-product-block">
                    <span class="service-product-name">{$product['name']}</span>
                </div>
                {if $product['description_short']}
                    <div class="col-sm-12 clearfix service-product-short-desc service-product-block">
                        {$product['description_short']}
                    </div>
                {/if}

                <div class="col-sm-12 service_product_action_block">
                    {if !$PS_CATALOG_MODE && !$order_date_restrict && ($product.show_price && !isset($restricted_country_mode))}
                        <span class="service-product-price">
                            {if !$priceDisplay}{convertPrice price=$product.price_tax_incl}{else}{convertPrice price=$product.price_tax_exc}{/if}
                        </span>
                    {/if}

                    <div>
                    {if ($product.show_price && !isset($restricted_country_mode))}
                        {if $product.available_for_order && !$PS_CATALOG_MODE && !$order_date_restrict && !((isset($restricted_country_mode) && $restricted_country_mode))}
                            <button class="btn btn-service-product{if isset($product.selected) && $product.selected} btn-danger remove_roomtype_product{else} btn-success add_roomtype_product{/if} pull-right" data-id-product="{$product.id_product}">{if isset($product.selected) && $product.selected}{l s='Remove'}{else}{l s='Select'}{/if}</button>
                            {if $product.allow_multiple_quantity && $product.available_for_order}
                                <div class="qty_container pull-right">
                                    <input type="hidden" class="service_product_qty" id="service_product_qty_{$product.id_product}" name="service_product_qty_{$product.id_product}" data-id-product="{$product.id_product}" data-max_quantity="{$product.max_quantity}" value="{if isset($product.quantity_added) && $product.quantity_added}{$product.quantity_added|escape:'html':'UTF-8'}{else}1{/if}">
                                    <div class="qty_count pull-left">
                                        <span>{if isset($product.quantity_added) && $product.quantity_added}{$product.quantity_added|escape:'html':'UTF-8'}{else}1{/if}</span>
                                    </div>
                                    <div class="qty_direction pull-left">
                                        <a href="#" class="btn btn-default quantity_up service_product_qty_up"><span><i class="icon-plus"></i></span></a>
                                        <a href="#" class="btn btn-default quantity_down service_product_qty_down"><span><i class="icon-minus"></i></span></a>
                                    </div>
                                </div>
                            {/if}
                        {/if}
                    {/if}
                </div>
            </div>
        </div>
    </li>
{/if}