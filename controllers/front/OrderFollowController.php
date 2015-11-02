<?php
/*
* 2007-2015 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderFollowControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'order-follow';
    public $authRedirection = 'order-follow';
    public $ssl = true;

    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitReturnMerchandise')) {
            $customizationQtyInput = Tools::getValue('customization_qty_input');
            $order_qte_input = Tools::getValue('order_qte_input');
            $customizationIds = Tools::getValue('customization_ids');

            if (!$id_order = (int)Tools::getValue('id_order')) {
                Tools::redirect('index.php?controller=history');
            }
            if (!$order_qte_input && !$customizationQtyInput && !$customizationIds) {
                Tools::redirect('index.php?controller=order-follow&errorDetail1');
            }
            if (!$customizationIds && !$ids_order_detail = Tools::getValue('ids_order_detail')) {
                Tools::redirect('index.php?controller=order-follow&errorDetail2');
            }

            $order = new Order((int)$id_order);
            if (!$order->isReturnable()) {
                Tools::redirect('index.php?controller=order-follow&errorNotReturnable');
            }
            if ($order->id_customer != $this->context->customer->id) {
                die(Tools::displayError());
            }
            $orderReturn = new OrderReturn();
            $orderReturn->id_customer = (int)$this->context->customer->id;
            $orderReturn->id_order = $id_order;
            $orderReturn->question = htmlspecialchars(Tools::getValue('returnText'));
            if (empty($orderReturn->question)) {
                Tools::redirect('index.php?controller=order-follow&errorMsg&'.
                    http_build_query(array(
                        'ids_order_detail' => $ids_order_detail,
                        'order_qte_input' => $order_qte_input,
                        'id_order' => Tools::getValue('id_order'),
                    )));
            }

            if (!$orderReturn->checkEnoughProduct($ids_order_detail, $order_qte_input, $customizationIds, $customizationQtyInput)) {
                Tools::redirect('index.php?controller=order-follow&errorQuantity');
            }

            $orderReturn->state = 1;
            $orderReturn->add();
            $orderReturn->addReturnDetail($ids_order_detail, $order_qte_input, $customizationIds, $customizationQtyInput);
            Hook::exec('actionOrderReturn', array('orderReturn' => $orderReturn));
            Tools::redirect('index.php?controller=order-follow');
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $ordersReturn = OrderReturn::getOrdersReturn($this->context->customer->id);
        if (Tools::isSubmit('errorQuantity')) {
            $this->context->smarty->assign('errorQuantity', true);
        } elseif (Tools::isSubmit('errorMsg')) {
            $this->context->smarty->assign(
                array(
                    'errorMsg' => true,
                    'ids_order_detail' => Tools::getValue('ids_order_detail', array()),
                    'order_qte_input' => Tools::getValue('order_qte_input', array()),
                    'id_order' => (int)Tools::getValue('id_order'),
                )
            );
        } elseif (Tools::isSubmit('errorDetail1')) {
            $this->context->smarty->assign('errorDetail1', true);
        } elseif (Tools::isSubmit('errorDetail2')) {
            $this->context->smarty->assign('errorDetail2', true);
        } elseif (Tools::isSubmit('errorNotReturnable')) {
            $this->context->smarty->assign('errorNotReturnable', true);
        }

        $this->context->smarty->assign('ordersReturn', $ordersReturn);

        $this->setTemplate(_PS_THEME_DIR_.'order-follow.tpl');
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(array(_THEME_CSS_DIR_.'history.css', _THEME_CSS_DIR_.'addresses.css'));
        $this->addJqueryPlugin('scrollTo');
        $this->addJS(array(
            _THEME_JS_DIR_.'history.js',
            _THEME_JS_DIR_.'tools.js') // retro compat themes 1.5
        );
        $this->addjqueryPlugin('footable');
        $this->addJqueryPlugin('footable-sort');
    }
}
