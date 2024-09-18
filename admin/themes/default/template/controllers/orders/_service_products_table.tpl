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

<div class="row">
    <div class="col-lg-12">
        <table class="table" id="customer_products_details" {if !$order_service_products}style="display:none"{/if}>
            <thead>
                <tr>
                    {if $refund_allowed}
                        <th class="standard_refund_fields" style="display:none"></th>
                    {/if}
                    {* <th class="text-center"><span class="title_box">{l s='Room No.'}</span></th> *}
                    <th class="text-center"><span class="title_box">{l s='Image'}</th>
                    <th class="text-center"><span class="title_box">{l s='Name'}</span></th>
                    <th class="text-center"><span class="title_box">{l s='Unit Price (Tax excl.)'}</span></th>
                    <th class="text-center"><span class="title_box">{l s='Quantity'}</span></th>
                    <th class="text-center"><span class="title_box">{l s='Total Price (Tax incl.)'}</span></th>
                    {* {if isset($refundReqBookings) && $refundReqBookings}
                        <th class="text-center"><span class="title_box">{l s='Refund State'}</span></th>
                        <th class="text-center"><span class="title_box">{l s='Refunded amount'}</span></th>
                    {/if} *}
                    {* <th class="text-center"><span class="title_box">{l s='Reallocate Room'}</span></th> *}
                    {if ($can_edit && !$order->hasBeenDelivered())}
                        <th class="text-right"><span class="title_box">{l s='Edit Order'}</th>
                    {/if}
                </tr>
            </thead>
            <tbody>
            {if $order_service_products}
                {foreach from=$order_service_products item=product}
                    {* Include product line partial *}
                    {include file='controllers/orders/_service_product_line.tpl'}
                {/foreach}
            {else}
                {* <tr>
                    <td>{l s='No Data Found.'}</td>
                </tr> *}
            {/if}
            {* Include product line partial *}
            {include file='controllers/orders/_new_service_product.tpl'}
            </tbody>
        </table>
    </div>
</div>