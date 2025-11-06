<?php
/**
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
*/

class CheckoutCustomerDetailsStepCore extends AbstractCheckoutStepCore
{
    public function __construct()
    {
        parent::__construct();
        $this->step_key = 'checkout_customer';
    }

    public function handleRequest()
    {
        $idAddressDelivery = $this->context->cart->id_address_delivery;
        // $objAddress = new Address($idAddressDelivery);
        if (Tools::getValue('proceed_to_customer_dtl')) {
            $this->step_is_reachable = 1;
            $this->step_is_current = 1;
            if ($this->context->cookie->__get('customer_details_proceeded')) {
                $this->step_is_current = 0;
                $this->step_is_complete = 1;
            }
        } elseif (Tools::getValue('proceed_to_payment')) {
            $guestInfoComplete = true;
            if ($idCustomerGuestDetail = CustomerGuestDetail::getCustomerGuestIdByIdCart($this->context->cart->id)) {
                $guestInfoComplete = false;
                $objCustomerGuestDetail = new CustomerGuestDetail($idCustomerGuestDetail);
                if ($objCustomerGuestDetail->validateGuestInfo()) {
                    $guestInfoComplete = true;
                }
            }
            $this->step_is_reachable = 1;
            $this->step_is_current = 1;
            if ($guestInfoComplete) {
                $this->step_is_current = 0;
                $this->step_is_complete = 1;
                $this->context->cookie->__set('customer_details_proceeded', 1);
            }
        } elseif ($this->context->cookie->__get('customer_details_proceeded')
            || $this->context->cookie->__get('cart_summary_proceeded')
        ) {
        } elseif ($this->context->customer->logged) {
            $this->step_is_reachable = 1;
            if ($idAddressDelivery) {
                $this->step_is_complete = 1;
            } else {
                $this->step_is_complete = 0;
            }
        }
    }
}
