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

class CheckoutPaymentStepCore extends AbstractCheckoutStepCore
{
    public function __construct() {
        parent::__construct();
        $this->step_key = 'checkout_payment';
    }

    public function handleRequest()
    {
        if ($this->context->cookie->__get('customer_details_proceeded')) 
        {
            $this->step_is_reachable = 1;
            $this->step_is_current = 1;
            $this->step_is_complete = 0;
        }
    }
}

