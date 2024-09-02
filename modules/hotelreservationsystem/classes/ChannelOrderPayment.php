<?php
/**
* Copyright since 2007 Webkul.
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
*  @copyright since 2007 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class ChannelOrderPayment extends PaymentModule
{
    public function __construct()
    {
        //$this->module = Module::getInstanceByName('hotelreservationsystem');
        $this->module = Module::getInstanceByName('hotelreservationsystem');
        $installedPayments = PaymentModule::getInstalledPaymentModules();
        if ($installedPayments) {
            $this->name = $installedPayments[0]['name'];
        } else {
            $this->name = 'hotelreservationsystem';
        }
        $this->orderSource = 'otherSources';
        $this->paymentModule = Module::getInstanceByName($this->name);
        $this->active = 1;
    }
}
