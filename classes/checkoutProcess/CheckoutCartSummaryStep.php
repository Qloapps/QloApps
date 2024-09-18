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

class CheckoutCartSummaryStepCore extends AbstractCheckoutStepCore
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
