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

class WkPaypalHelper
{
	public $context;
	public function __construct()
	{
		$this->context = Context::getContext();
	}

	public function getReciverListDetails($cartTotal, $bodyparams)
    {
        if ($cartTotal > 0) {
            $admin_paypal_email = Configuration::get('PAYPAL_EMAIL');
            $receiverList = array("receiverList.receiver(0).email" => $admin_paypal_email,
                                "receiverList.receiver(0).amount" => $cartTotal, );
            $bodyparams += $receiverList;
        }
        return $bodyparams;
    }
}