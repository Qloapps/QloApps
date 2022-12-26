<?php
/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class CheckoutCustomerDetailsStep extends AbstractCheckoutStep
{
    public function __construct()
    {
        parent::__construct();
        $this->step_key = 'checkout_customer';
    }

    public function handleRequest()
    {
        $idAddressDelivery = $this->context->cart->id_address_delivery;
        $objAddress = new Address($idAddressDelivery);
        if (Tools::getValue('proceed_to_customer_dtl')) {
            $this->step_is_reachable = 1;
            $this->step_is_current = 1;
            if ($idAddressDelivery) {
                if (!Validate::isLoadedObject($objAddress)) {
                    if ($this->context->cookie->__get('customer_details_proceeded')) {
                        $this->step_is_current = 0;
                        $this->step_is_complete = 1;
                    }
                }
            }
        } elseif (Tools::getValue('proceed_to_payment')) {
            $guestInfoComplete = true;
            if ($id_customer_guest_detail = CartCustomerGuestDetail::getCartCustomerGuest($this->context->cart->id)) {
                $guestInfoComplete = false;
                $objCustomerGuestDetail = new CartCustomerGuestDetail($id_customer_guest_detail);
                if ($objCustomerGuestDetail->validateGuestInfo()) {
                    $guestInfoComplete = true;
                }
            }
            $this->step_is_reachable = 1;
            $this->step_is_current = 1;
            if ($idAddressDelivery && $guestInfoComplete) {
                if (Validate::isLoadedObject($objAddress)) {
                    $this->step_is_current = 0;
                    $this->step_is_complete = 1;
                    $this->context->cookie->__set('customer_details_proceeded', 1);
                }
            }
        } elseif ($this->context->cookie->__get('customer_details_proceeded')
            || $this->context->cookie->__get('cart_summary_proceeded')
        ) {
            if ($idAddressDelivery) {
                if (!Validate::isLoadedObject($objAddress)) {
                    $this->context->cookie->__set('customer_details_proceeded', 0);
                    $this->step_is_reachable = 1;
                    $this->step_is_current = 1;
                }
            } else {
                $this->step_is_reachable = 1;
                $this->step_is_current = 1;
                $this->context->cookie->__set('customer_details_proceeded', 0);
            }
        } elseif ($this->context->customer->logged) {
            $this->step_is_reachable = 1;
            if ($idAddressDelivery) {
                $this->step_is_complete = 1;
            } else {
                $this->step_is_complete = 0;
                $this->step_is_current = 1;
            }
        }
    }
}
