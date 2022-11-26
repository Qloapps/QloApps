<?php
/**
* 2010-2021 Webkul.
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
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

require_once _PS_MODULE_DIR_.'qlopaypalcommerce/classes/WkPaypalCommerceDb.php';
require_once _PS_MODULE_DIR_.'qlopaypalcommerce/classes/WkPaypalCommerceHelper.php';
require_once _PS_MODULE_DIR_.'qlopaypalcommerce/classes/WkPayPalCommerceOrder.php';
require_once _PS_MODULE_DIR_.'qlopaypalcommerce/classes/WkPaypalCommerceRefund.php';
require_once _PS_MODULE_DIR_.'qlopaypalcommerce/classes/WkPaypalCommerceWebhook.php';
require_once _PS_MODULE_DIR_.'qlopaypalcommerce/libs/PayPalCommerce.php';
