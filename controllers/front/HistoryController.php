<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class HistoryControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'history';
    public $authRedirection = 'history';
    public $ssl = true;

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(array(
            _THEME_CSS_DIR_.'history.css',
            _THEME_CSS_DIR_.'addresses.css'
        ));
        $this->addJS(array(
            _THEME_JS_DIR_.'history.js',
            _THEME_JS_DIR_.'tools.js' // retro compat themes 1.5
        ));
        $this->addJqueryPlugin(array('fancybox')); //fancybox not found for some client theme
        $this->addJqueryPlugin(array('scrollTo', 'footable', 'footable-sort'));
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if ($is_htlInst = Module::isInstalled('hotelreservationsystem')) {
            require_once(_PS_MODULE_DIR_.'hotelreservationsystem/define.php');

            $advance_payment_active = Configuration::get('WK_ALLOW_ADVANCED_PAYMENT');
            $obj_customer_adv = new HotelCustomerAdvancedPayment();

            $obj_ord_ref_info = new HotelOrderRefundInfo();

            $order_refund_info = array();

            if ($advance_payment_active) {
                $this->context->smarty->assign('adv_active', $advance_payment_active);
            }
        }

        if ($orders = Order::getCustomerOrders($this->context->customer->id)) {
            foreach ($orders as &$order) {
                $myOrder = new Order((int)$order['id_order']);
                if (Validate::isLoadedObject($myOrder)) {
                    $order['virtual'] = $myOrder->isVirtual(false);
                }

                if ($is_htlInst) {
                    //For Advanced Payment
                    if ($advance_payment_active) {
                        $order_adv_dtl = $obj_customer_adv->getCstAdvPaymentDtlByIdOrder($myOrder->id);
                        if ($order_adv_dtl) {
                            $order['due_amount'] = $order_adv_dtl['total_order_amount'] - $order_adv_dtl['total_paid_amount'];
                            $order['pay_currency'] = $order_adv_dtl['id_currency'];
                        } else {
                            $order['due_amount'] = 0;
                        }
                    }

                    // For order Refund
                    $order_refund_data = $obj_ord_ref_info->getOrderRefundInforDataByOrderId((int)$order['id_order']);
                    if ($order_refund_data) {
                        $waitting = 0;
                        $accepted = 0;
                        $refunded = 0;
                        $rejected = 0;
                        foreach ($order_refund_data as $key_ref_data => $val_ref_data) {
                            if ($val_ref_data['refund_stage_id'] == 1) {
                                $order_refund_info[$order['id_order']]['waitting'] = ++$waitting;
                            } elseif ($val_ref_data['refund_stage_id'] == 2) {
                                $order_refund_info[$order['id_order']]['accepted'] = ++$accepted;
                            } elseif ($val_ref_data['refund_stage_id'] == 3) {
                                $order_refund_info[$order['id_order']]['refunded'] = ++$refunded;
                            } elseif ($val_ref_data['refund_stage_id'] == 4) {
                                $order_refund_info[$order['id_order']]['rejected'] = ++$rejected;
                            }
                        }
                    }
                }
            }
        }
        $this->context->smarty->assign(array(
            'order_refund_info' => $order_refund_info, // by webkul
            'orders' => $orders,
            'invoiceAllowed' => (int)Configuration::get('PS_INVOICE'),
            'reorderingAllowed' => !(bool)Configuration::get('PS_DISALLOW_HISTORY_REORDERING'),
            'slowValidation' => Tools::isSubmit('slowvalidation')
        ));

        $this->setTemplate(_PS_THEME_DIR_.'history.tpl');
    }
}
