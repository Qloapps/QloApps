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
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><i class="icon-remove-sign"></i></button>
    <h4 class="modal-title"><i class="icon icon-exclamation-triangle"></i>&nbsp;{l s='Confirm Update'}</h4>
    <p>{l s='This action is irreversable, Are you sure you want to change the status?'}</p>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-sm-6">
            <label>{l s='Credit Slip'}</label>
            <p class="control-value">
                <a href="{$link->getAdminLink('AdminCartRules')}&updatecart_rule&id_cart_rule={$orderSlip->id}" target="_blank">#{$orderSlip->id}</a>
            </p>
        </div>
        <div class="col-sm-6">
            <label>{l s='Amount'}</label>
            <p class="control-value">{displayPrice price=$orderSlip->amount currency=$order->id_currency}</p>
        </div>
        <div class="col-sm-6">
            <label>{l s='Customer'}</label>
            <p class="control-value">
                {$customer->firstname} {$customer->lastname} (<a href="{$link->getAdminLink('AdminCustomers')}&viewcustomer&id_customer={$customer->id}" target="_blank">#{$customer->id}</a>)
            </p>
        </div>
        <div class="col-sm-6">
            <label>{l s='Order'}</label>
            <p class="control-value">
                {$order->reference} (<a href="{$link->getAdminLink('AdminOrders')}&vieworder&id_order={$order->id}" target="_blank">#{$order->id}</a>)
            </p>
        </div>
    </div>
</div>