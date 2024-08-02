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

{l s='This room already has a booking for the selected date range.'}
<div class="row">
    <div class="col-xs-12">
        {l s='Order ID:'} <a href="{$link->getAdminLink('AdminOrders')}&id_order={$orderDetails->id_order}&vieworder" target="_blank"><strong>#{$orderDetails->id_order|intval}</strong></a>
    </div>
    <div class="col-xs-12">
        {l s='Date From:'} {dateFormat date=$orderDetails->date_from}
    </div>
    <div class="col-xs-12">
        {l s='Date To:'} {dateFormat date=$orderDetails->date_to}
    </div>
    <div>
    </div>
</div>
