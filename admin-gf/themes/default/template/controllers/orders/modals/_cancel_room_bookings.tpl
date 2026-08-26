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

<div class="modal-body">
    {if $bookingOrderInfo|count > 0 || $serviceProducts|count > 0}
        {if $bookingOrderInfo|count > 0 && $serviceProducts|count > 0}
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#refund_rooms_tab" role="tab" data-toggle="tab">{l s='Rooms'}</a>
                </li>
                <li role="presentation">
                    <a href="#refund_products_tab" aria-controls="products" role="tab" data-toggle="tab">{l s='Products'}</a>
                </li>
            </ul>
        {/if}

        <form id="order_refund_form" action="{$current_index}&amp;vieworder&amp;token={$smarty.get.token|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}" method="post">
            {if $bookingOrderInfo|count > 0 && $serviceProducts|count > 0}
                <div class="form-group tab-content clearfix">
            {/if}
                {if $bookingOrderInfo|count}
                    <div id="refund_rooms_tab" class="form-group table-responsive tab-pane active">
                        <table class="table" id="customer_cart_details">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>{l s='Room No.'}</th>
                                    <th>{l s='Room Type'}</th>
                                    <th>{l s='Hotel Name'}</th>
                                    <th>{l s='Duration'}</th>
                                    <th>{l s='Total Price (Tax incl.)'}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$bookingOrderInfo item=bookingInfo}
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="id_htl_booking[]" value="{$bookingInfo.id|escape:'html':'UTF-8'}"/>
                                        </td>
                                        <td><b>{$bookingInfo.room_num|escape:'html':'UTF-8'}</b></td>
                                        <td>{$bookingInfo.room_type_name|escape:'html':'UTF-8'}</td>
                                        <td>{$bookingInfo.hotel_name|escape:'html':'UTF-8'}</td>
                                        {assign var="is_full_date" value=($show_full_date && ($bookingInfo['date_from']|date_format:'%D' == $bookingInfo['date_to']|date_format:'%D'))}
                                        <td>{dateFormat date=$bookingInfo.date_from full=$is_full_date} - {dateFormat date=$bookingInfo.date_to full=$is_full_date}</span></td>
                                        <td>{convertPriceWithCurrency price=$bookingInfo.total_price_tax_incl currency=$currency->id}</td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                {/if}
                {if $serviceProducts|count}
                    <div id="refund_products_tab" class="form-group table-responsive tab-pane">
                        <table class="table" id="customer_cart_product_details">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>{l s='Name'}</th>
                                    <th>{l s='Quantity'}</th>
                                    <th>{l s='Total Price (Tax incl.)'}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$serviceProducts item=product}
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="id_service_product_order_detail[]" value="{$product.id_service_product_order_detail|escape:'html':'UTF-8'}"/>
                                        </td>
                                        <td><b>{$product.name|escape:'html':'UTF-8'}{if $product.option_name} : {$product.option_name|escape:'html':'UTF-8'}{/if}</b></td>
                                        <td>{if $product.allow_multiple_quantity}{$product.quantity|escape:'html':'UTF-8'}{else}--{/if}</td>
                                        <td>{convertPriceWithCurrency price=$product.total_price_tax_incl currency=$currency->id}</td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                {/if}
            {if $bookingOrderInfo|count > 0 && $serviceProducts|count > 0}
                </div>
            {/if}
            <div class="form-group">
                <label class="control-label">{l s='Reason to Cancel'}</label>
                <textarea rows="3" class="textarea-autosize cancellation_reason" name="cancellation_reason"></textarea>
            </div>

            <button style="display: none;" type="submit" name="initiateRefund" class="btn btn-primary" id="initiateRefund">
                {if $order->hasBeenPaid()}<i class="icon-undo"></i> {l s='Initiate Refund'}{else}{l s='Submit'}{/if}
            </button>

        </form>
    {else}
        <div class="list-empty">
            <div class="list-empty-msg">
                <i class="icon-warning-sign list-empty-icon"></i>
                {l s='No room bookings found to cancel'}
            </div>
        </div>
    {/if}

    {if isset($loaderImg) && $loaderImg}
        <div class="loading_overlay">
            <img src='{$loaderImg}' class="loading-img"/>
        </div>
    {/if}
</div>
