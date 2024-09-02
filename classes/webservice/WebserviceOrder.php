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

class WebserviceOrderCore extends PaymentModule
{
    public $active = 1;
    public $name = 'wsorder';

    public function __construct()
    {
        $this->displayName = $this->l('Order from API');
        $this->validateOrderAmount = false;
    }
}
