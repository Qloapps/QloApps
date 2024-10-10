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
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><i class="icon-remove-sign"></i></button>
    <h4 class="modal-title"><i class="icon icon-exclamation-triangle"></i> {l s='Confirm Delete'}</h4>
    <p>{l s='Are you sure, you want to delete this cart rule?'}</p>
</div>
{if isset($cartRule)}
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-4">
                <label>{l s='Voucher'}</label>
                <p class="control-value">
                    {$cartRule->code} (<a href="{$link->getAdminLink('AdminCartRules')}&updatecart_rule&id_cart_rule={$cartRule->id}" target="_blank">#{$cartRule->id}</a>)
                </p>
            </div>
            <div class="col-sm-4">
                <label>{l s='Reason'}</label>
                <p class="control-value">
                    {if $generatedBy == CartRule::GENERATED_BY_REFUND}
                        {l s='Generated against refund'}
                        (<a href="{$link->getAdminLink('AdminOrderRefundRequests')}&vieworder_return&id_order_return={$generatedById}" target="_blank">#{$generatedById}</a>)
                    {else if $generatedBy == CartRule::GENERATED_BY_ORDER_SLIP}
                        {l s='Generated against credit slip'}
                        (<a href="{$link->getAdminLink('AdminPdf')}&submitAction=generateOrderSlipPDF&id_order_slip={$generatedById}" target="_blank">#{$generatedById}</a>)
                    {/if}
                </p>
            </div>
            <div class="col-sm-4">
                <label>{l s='Amount'}</label>
                <p class="control-value">{displayPrice price=$cartRule->reduction_amount currency=$cartRule->reduction_currency}</p>
            </div>
            <div class="col-sm-4">
                <label>{l s='Customer'}</label>
                <p class="control-value">
                    {$customer->firstname} {$customer->lastname} (<a href="{$link->getAdminLink('AdminCustomers')}&viewcustomer&id_customer={$customer->id}" target="_blank">#{$customer->id}</a>)
                </p>
            </div>
            <div class="col-sm-4">
                <label>{l s='Order'}</label>
                <p class="control-value">
                    {$order->reference} (<a href="{$link->getAdminLink('AdminOrders')}&vieworder&id_order={$order->id}" target="_blank">#{$order->id}</a>)
                </p>
            </div>
            {if !$cartRule->quantity}
                <div class="col-sm-4">
                    <label>{l s='Status'}</label>
                    <p class="control-value">
                        <span class="badge badge-danger">{l s='Used'}</span>
                    </p>
                </div>
            {/if}
        </div>
    </div>
{/if}