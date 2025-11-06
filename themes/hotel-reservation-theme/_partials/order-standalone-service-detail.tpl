{*
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


<div class="product-detail" data-id-product="{$product.id_product}">
    <div class="row">
        {block name='order_standalone_product_image'}
            <div class="col-xs-3 col-sm-2">
                <a href="{$link->getProductLink($product.id_product)|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" target="_blank">
                    <img class="img img-responsive img-room-type" src="{$product.cover_img|escape:'html':'UTF-8'}" />
                </a>
            </div>
        {/block}
        {block name='order_standalone_product_detail'}
            <div class="col-xs-9 col-sm-10 info-wrap">
                <div class="row">
                    <div class="col-xs-12">
                        <a href="{$link->getProductLink($product.id_product)|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" target="_blank" class="product-name">
                            <h3>{$product.name|escape:'html':'UTF-8'}{if $product.option_name} : {$product.option_name|escape:'html':'UTF-8'}{/if}</h3>
                        </a>
                        {if $product['is_refunded'] || $product['is_cancelled']}
                            <div class="num-refunded-rooms">
                                {if $product['is_cancelled']}
                                    <span class="badge badge-danger">
                                        {l s='Cancelled'}
                                    </span>
                                {else}
                                    <span class="badge badge-danger">
                                        {l s='Refunded'}
                                    </span>
                                {/if}
                            </div>
                        {/if}
                    </div>
                    <div class="col-xs-12">
                        <div class="description-list">
                            <dl class="">
                                <div class="row">
                                    {if $product.allow_multiple_quantity}
                                        <div class="col-xs-12 col-md-6">
                                            <div class="row">
                                                <dt class="col-xs-5">{l s='Quantity'}</dt>
                                                <dd class="col-xs-7">{$product.quantity}</dd>
                                            </div>
                                        </div>
                                    {/if}
                                    <div class="col-xs-12 col-md-6">
                                        <div class="row">
                                            <dt class="col-xs-5">{l s='Unit Price'}</dt>
                                            <dd class="col-xs-7">
                                                {if $group_use_tax}
                                                    {displayWtPriceWithCurrency price=$product.unit_price_tax_incl  currency=$currency}
                                                {else}
                                                    {displayWtPriceWithCurrency price=$product.unit_price_tax_excl  currency=$currency}
                                                {/if}
                                            </dd>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    {if $product.allow_multiple_quantity}
                                        <div class="col-xs-12 col-md-6">
                                        </div>
                                    {/if}
                                    <div class="col-xs-12 col-md-6">
                                        <div class="row">
                                            <dt class="col-xs-5">{l s='Total Pricing'}</dt>
                                            <dd class="col-xs-7">
                                                {if $group_use_tax}
                                                    {displayWtPriceWithCurrency price=$product.total_price_tax_incl  currency=$currency}
                                                {else}
                                                    {displayWtPriceWithCurrency price=$product.total_price_tax_excl  currency=$currency}
                                                {/if}
                                            </dd>
                                        </div>
                                    </div>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        {/block}
    </div>
</div>