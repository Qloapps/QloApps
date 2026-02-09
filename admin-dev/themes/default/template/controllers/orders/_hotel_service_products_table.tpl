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

<div class="row">
    <div class="col-lg-12">
        <table class="table" id="customer_products_details">
            <thead>
                <tr>
                    <th><span class="title_box">{l s='Image'}</th>
                    <th><span class="title_box">{l s='Name'}</span></th>
                    <th><span class="title_box">{l s='Quantity'}</span></th>
                    <th><span class="title_box">{l s='Price (Tax excl.)'}</span></th>
                    <th><span class="title_box">{l s='Total Tax'}</span></th>
                    <th><span class="title_box">{l s='Total Price (Tax incl.)'}</span></th>
                    {if isset($refundReqProducts) && $refundReqProducts}
                        <th><span class="title_box">{l s='Refund State'}</span></th>
                        <th><span class="title_box">{l s='Refunded amount'}</span></th>
                    {/if}
                    {if $can_edit}
                        <th><span class="title_box">{l s='Actions'}</th>
                    {/if}
                </tr>
            </thead>
            <tbody>
                {if $hotel_service_products}
                    {foreach from=$hotel_service_products item=product}
                        {* Include product line partial *}
                        {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                            {assign var=product_price value=($product['unit_price_tax_excl'])}
                        {else}
                            {assign var=product_price value=$product['unit_price_tax_incl']}
                        {/if}
                        <tr class="product-line-row" data-id_product="{$product.id_product}" data-id_order_detail="{$product.id_order_detail}" data-id_service_product_order_detail="{$product.id_service_product_order_detail}" data-id_hotel="{$product.id_hotel}">
                            <td>
                                {if isset($product.image_link) && $product.image_link}
                                    <img class="img img-responsive" src="{$product.image_link|escape:'html':'UTF-8'}" />
                                {/if}
                            </td>
                            <td>
                                <a href="{$link->getAdminLink('AdminNormalProducts')|escape:'html':'UTF-8'}&amp;id_product={$product['id_product']|intval}&amp;updateproduct">
                                    <span class="productName">{$product['name']}{if isset($product['option_name']) && $product['option_name']} : {$product['option_name']}{/if}</span><br />
                                </a>
                            </td>
                            <td>
                                {if $product['allow_multiple_quantity']}<span class="">{(int)$product['quantity']}</span>{else}--{/if}
                            </td>
                            <td class="unit_price_tax_excl">
                                <p>{displayPrice price=$product.total_price_tax_excl currency=$currency->id}</p>
                                <p class="help-block">{l s='Unit price'} : {displayPrice price=$product.unit_price_tax_excl currency=$currency->id}</p>
                            </td>
                            <td>
                                <span>{displayPrice price=($product.total_price_tax_incl - $product.total_price_tax_excl) currency=$currency->id}</span>
                            </td>
                            <td>
                                <span>{displayPrice price=$product.total_price_tax_incl currency=$currency->id}</span>
                            </td>
                            {if (isset($refundReqProducts) && $refundReqProducts)}
                                <td>
                                    {if $product.id_service_product_order_detail|in_array:$refundReqProducts}
                                        {if $product.is_cancelled}
                                            <span class="badge badge-danger">{l s='Cancelled'}</span>
                                        {elseif isset($product.refund_info) && (!$product.refund_info.refunded || $product.refund_info.id_customization)}
                                            <span class="badge" style="background-color:{$product.refund_info.color|escape:'html':'UTF-8'}">{$product.refund_info.name|escape:'html':'UTF-8'}</span>
                                        {else}
                                            <span>--</span>
                                        {/if}
                                    {else}
                                        <span>--</span>
                                    {/if}
                                </td>
                                <td>
                                    {if $product.is_refunded && isset($product.refund_info) && $product.refund_info}
                                        {convertPriceWithCurrency price=$product.refund_info.refunded_amount currency=$currency->id}
                                    {else}
                                        --
                                    {/if}
                                </td>
                            {/if}
                            {if ($can_edit && !$order->hasBeenDelivered())}
                                <td class="room_invoice" style="display: none;">
                                {if sizeof($invoices_collection)}
                                <select name="product_invoice" class="edit_product_invoice">
                                    {foreach from=$invoices_collection item=invoice}
                                    <option value="{$invoice->id}" {*{if $invoice->id == $product['id_order_invoice']}selected="selected"{/if}*}>
                                        #{Configuration::get('PS_INVOICE_PREFIX', $current_id_lang, null, $order->id_shop)}{'%06d'|sprintf:$invoice->number}
                                    </option>
                                    {/foreach}
                                </select>
                                {else}
                                &nbsp;
                                {/if}
                                </td>
                                <td class="product_action">
                                    {* edit/delete controls *}
                                     {if isset($refundReqProducts) && $refundReqProducts && $product.id_service_product_order_detail|in_array:$refundReqProducts && $product.is_cancelled}
                                        <button href="#" class="btn btn-default delete_product_line">
                                            <i class="icon-trash"></i>
                                            {l s='Delete'}
                                        </button>
                                    {else}
                                        <div class="btn-group">
                                            {* <button type="button" class="btn btn-default delete_product_line">
                                                <i class="icon-trash"></i>
                                                {l s='Delete'}
                                            </button> *}
                                            <button type="button" class="btn btn-default edit_product_change_link" data-product_line_data="{$product|json_encode|escape}">
                                                <i class="icon-pencil"></i>
                                                {l s='Edit'}
                                            </button>
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li>
                                                    <a href="#" class="delete_product_line">
                                                        <i class="icon-trash"></i>
                                                        {l s='Delete'}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    {/if}
                                    {* Update controls *}
                                    {* <button type="button" class="btn btn-default submitProductChange" style="display: none;">
                                        <i class="icon-ok"></i>
                                        {l s='Update'}
                                    </button>
                                    <button type="button" class="btn btn-default cancel_product_change_link" style="display: none;">
                                        <i class="icon-remove"></i>
                                        {l s='Cancel'}
                                    </button> *}
                                </td>
                            {/if}
                        </tr>
                    {/foreach}
                {else}
                    <tr>
                        {assign var=colspan value=6}
                        {if isset($refundReqProducts) && $refundReqProducts}
                            {assign var=colspan value=($colspan+2)}
                        {/if}
                        {if ($can_edit)}
                            {assign var=colspan value=($colspan+1)}
                        {/if}
                        <td class="list-empty hidden-print" colspan="{$colspan}">
                            <div class="list-empty-msg">
                                <i class="icon-warning-sign list-empty-icon"></i>
                                {l s='No products added yet'}
                            </div>
                        </td>
                    </tr>
                {/if}
            </tbody>
        </table>
    </div>
</div>