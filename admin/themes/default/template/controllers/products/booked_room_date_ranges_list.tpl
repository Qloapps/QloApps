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

{sprintf({l s='The room "%s" already has a booking for the selected date range.'}, $orderDetails->room_num)}
<div class="row">
    <div class="col-xs-12">
        <span class="error_message_label">{l s='Order ID'}:</span> <a href="{$link->getAdminLink('AdminOrders')}&id_order={$orderDetails->id_order}&vieworder" target="_blank"><strong>#{$orderDetails->id_order|intval}</strong></a>
    </div>
    <div class="col-xs-12">
        <span class="error_message_label">{l s='Date From'}:</span> {dateFormat date=$orderDetails->date_from}
    </div>
    <div class="col-xs-12">
        <span class="error_message_label">{l s='Date To'}:</span> {dateFormat date=$orderDetails->date_to}
    </div>
    <div>
    </div>
</div>
