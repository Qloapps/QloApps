{*
* Since 2010 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright Since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
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