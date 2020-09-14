<?php
/**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderReturnControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'order-return';
    public $authRedirection = 'order-follow';
    public $ssl = true;

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $this->show_breadcrump = true;
        $this->display_column_left = false;
        $this->display_column_right = false;

        parent::initContent();

        if (isset($this->context->customer->id) && $this->context->customer->id) {
            if (Validate::isLoadedObject($objOrderReturn = new OrderReturn(Tools::getValue('id_order_return')))) {
                if ($this->context->customer->id == $objOrderReturn->id_customer) {
                    $refundStatuses = OrderReturnStateCore::getOrderReturnStates($this->context->language->id);
                    $objOrder = new Order($objOrderReturn->id_order);
                    $orderCurrency = new Currency($objOrder->id_currency);

                    $refundReqBookings = $objOrderReturn->getOrderRefundRequestedBookings($objOrderReturn->id_order, $objOrderReturn->id, 0, 1);

                    $this->context->smarty->assign(
                        array (
                            'orderReturnInfo' => (array)$objOrderReturn,
                            'refundReqBookings' => $refundReqBookings,
                            'orderInfo' => (array) $objOrder,
                            'orderCurrency' => (array) $orderCurrency,
                            'currentStateInfo' => (array) new OrderReturnState($objOrderReturn->state,
                            $this->context->language->id),
                            'isRefundCompleted' => $objOrderReturn->hasBeenCompleted(),
                        )
                    );

                    $this->setTemplate(_PS_THEME_DIR_.'order-return.tpl');
                } else {
                    Tools::redirect($this->context->link->getpagelink('my-account'));
                }
            } else {
                Tools::redirect($this->context->link->getpagelink('my-account'));
            }
        } else {
            Tools::redirect(
                'index.php?controller=authentication&back='.urlencode($this->context->link->getpageLink('my-account'))
            );
        }
    }
}

