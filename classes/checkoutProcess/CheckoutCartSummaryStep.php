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

class CheckoutCartSummaryStep extends AbstractCheckoutStep
{
    public function __construct()
    {
        parent::__construct();
        $this->step_key = 'checkout_rooms_summary';
    }

    public function handleRequest()
    {
        // set data in checkout_session cookie
        if (Tools::getValue('proceed_to_customer_dtl')) {
            $this->step_is_reachable = 1;
            $this->step_is_complete = 1;
            $this->step_is_current = 0;
            $this->context->cookie->__set('cart_summary_proceeded', 1);
        } elseif ($this->context->cookie->__get('cart_summary_proceeded')) {
            $this->step_is_reachable = 1;
            $this->step_is_complete = 1;
            $this->step_is_current = 0;
        }

        return $this;
    }
}
