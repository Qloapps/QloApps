<?php
/**
* Copyright since 2010 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade QloApps to newer
* versions in the future. If you wish to customize QloApps for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class CsvOrder extends PaymentModule
{
    public $active = 1;
    public $name = 'csvorder';

    public function __construct()
    {
        $this->payment_type = OrderPayment::PAYMENT_TYPE_REMOTE_PAYMENT;
    }

}

