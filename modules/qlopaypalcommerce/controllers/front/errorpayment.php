<?php
/**
* 2010-2021 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2021 Webkul IN
* @license LICENSE.txt
*/

class QloPaypalCommerceErrorpaymentModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;

        parent::init();

        if (Tools::getValue('err_msg') && Tools::getValue('err_name')) {
            $error = Tools::getValue('err_msg');
            $err_name = Tools::getValue('err_name');
            $this->context->smarty->assign(array(
                'err_msg' => $error,
                'err_name' => $err_name,
            ));
            $this->setTemplate('paypal_error.tpl');
        } else {
            Tools::redirect($this->context->link->getPageLink('order-opc', true, null));
        }
    }
}
