<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 *  @copyright 2007-2018 PrestaShop SA
 *  @version  Release: $Revision: 13573 $
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @since 1.5.0
 */

require_once _PS_MODULE_DIR_.'paypal/classes/PaypalPlusPui.php';

class PayPalSubmitplusModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
    public $display_column_right = false;
    public $ssl = true;

    /*public function init(){
    $this->page_name = 'Confirm Payment';
    }*/

    public function __construct()
    {
        parent::__construct();

        if (class_exists('Context')) {
            $this->context = Context::getContext();
        } else {
            global $smarty, $cookie;
            $this->context = new StdClass();
            $this->context->smarty = $smarty;
            $this->context->cookie = $cookie;
        }
    }

    public function initContent()
    {
        parent::initContent();

        $paypal = new PayPal();

        $this->id_module = (int) Tools::getValue('id_module');
        $this->id_cart = Tools::getValue('id_cart');
        $this->paymentId = Tools::getValue('paymentId');
        $this->token = Tools::getValue('token');

        if (!empty($this->id_cart) && !empty($this->paymentId) && !empty($this->token)) {
            $CallApiPaypalPlus = new CallApiPaypalPlus();
            $payment = Tools::jsonDecode($CallApiPaypalPlus->lookUpPayment($this->paymentId));

            if (isset($payment->state)) {
                $this->context->smarty->assign('state', $payment->state);

                $transaction = array(
                    'id_transaction' => $payment->id,
                    'payment_status' => $payment->state,
                    'currency' => $payment->transactions[0]->amount->currency,
                    'payment_date' => date("Y-m-d H:i:s"),
                    'total_paid' => $payment->transactions[0]->amount->total,
                    'id_invoice' => 0,
                    'shipping' => 0,
                );

                switch ($payment->state) {
                    case 'created':
                        /* LookUp OK */
                        /* Affichage bouton confirmation */

                        $this->context->smarty->assign(
                            array(
                                'PayerID' => $payment->payer->payer_info->payer_id,
                                'paymentId' => $this->paymentId,
                                'id_cart' => $this->id_cart,
                                'totalAmount' => Tools::displayPrice(Cart::getTotalCart($this->id_cart)),
                                'linkSubmitPlus' => $this->context->link->getModuleLink('paypal', 'submitplus'),
                            )
                        );
                        break;

                    case 'canceled':
                        /* LookUp cancel */
                        $paypal->validateOrder(
                            $this->id_cart,
                            $this->getOrderStatus('order_canceled'),
                            $payment->transactions[0]->amount->total,
                            $payment->payer->payment_method,
                            null,
                            $transaction
                        );
                        break;

                    default:
                        /* Erreur de payment */
                        $paypal->validateOrder(
                            $this->id_cart,
                            $this->getOrderStatus('payment_error'),
                            $payment->transactions[0]->amount->total,
                            $payment->payer->payment_method,
                            null,
                            $transaction
                        );

                        break;
                }
            } else {
                $this->context->smarty->assign('state', 'failed');
            }
        } else {
            $this->context->smarty->assign('state', 'failed');
        }

        if (($this->context->customer->is_guest) || $this->context->customer->id == false) {

            /* If guest we clear the cookie for security reason */
            $this->context->customer->mylogout();
        }

        $this->module->assignCartSummary();

        if ($this->context->getMobileDevice() == true) {
            $this->setTemplate('order-confirmation-plus-mobile.tpl');
        } else {
            $this->setTemplate('order-confirmation-plus.tpl');
        }
    }

    private function displayHook()
    {
        if (Validate::isUnsignedId($this->id_order) && Validate::isUnsignedId($this->id_module)) {
            $order = new Order((int) $this->id_order);
            $currency = new Currency((int) $order->id_currency);

            if (Validate::isLoadedObject($order)) {
                $params = array();
                $params['objOrder'] = $order;
                $params['currencyObj'] = $currency;
                $params['currency'] = $currency->sign;
                $params['total_to_pay'] = $order->getOrdersTotalPaid();

                return $params;
            }
        }

        return false;
    }

    public function displayAjax()
    {
        $ajax = Tools::getValue('ajax');
        $return = array();
        if (!$ajax) {
            $return['error'][] = $this->module->l('An error occured during the payment');
            echo Tools::jsonEncode($return);
            die();
        }

        $id_cart = Tools::getValue('id_cart');
        $payerID = Tools::getValue('payerID');
        $paymentId = Tools::getValue('paymentId');
        $submit = Tools::getValue('submit');

        if ((!empty($id_cart) && $this->context->cart->id == $id_cart)
            && !empty($payerID)
            && !empty($paymentId)
            && !empty($submit)
        ) {
            $CallApiPaypalPlus = new CallApiPaypalPlus();
            $payment = Tools::jsonDecode($CallApiPaypalPlus->executePayment($payerID, $paymentId));


            if (isset($payment->state)) {
                $paypal = new PayPal();

                $transaction = array(
                    'id_transaction' => $payment->transactions[0]->related_resources[0]->sale->id,
                    'payment_status' => $payment->state,
                    'total_paid' => $payment->transactions[0]->amount->total,
                    'id_invoice' => 0,
                    'shipping' => 0,
                    'currency' => $payment->transactions[0]->amount->currency,
                    'payment_date' => date("Y-m-d H:i:s"),
                );

                if ($submit == 'confirmPayment') {
                    if ($payment->state == 'approved') {
                        $paypal->validateOrder(
                            $this->id_cart,
                            $this->getOrderStatus('payment'),
                            $payment->transactions[0]->amount->total,
                            $payment->payer->payment_method,
                            null,
                            $transaction
                        );
                        $return['success'][] = $this->module->l('Your payment has been taken into account');
                    } else {
                        $paypal->validateOrder(
                            $this->id_cart,
                            $this->getOrderStatus('payment_error'),
                            $payment->transactions[0]->amount->total,
                            $payment->payer->payment_method,
                            null,
                            $transaction
                        );
                        $return['error'][] = $this->module->l('An error occured during the payment');
                    }
                    if (isset($payment->payment_instruction)) {
                        $id_order = Order::getOrderByCartId($this->id_cart);

                        $paypal_plus_pui = new PaypalPlusPui();
                        $paypal_plus_pui->id_order = $id_order;
                        $paypal_plus_pui->pui_informations = Tools::jsonEncode($payment->payment_instruction);

                        $paypal_plus_pui->save();
                    }
                } elseif ($submit == 'confirmCancel') {
                    $paypal->validateOrder(
                        $this->id_cart,
                        $this->getOrderStatus('order_canceled'),
                        $payment->transactions[0]->amount->total,
                        $payment->payer->payment_method,
                        null,
                        $transaction
                    );
                    $return['success'][] = $this->module->l('Your order has been canceled');
                } else {
                    $return['error'][] = $this->module->l('An error occured during the payment');
                }
            } else {
                $return['error'][] = $this->module->l('An error occured during the payment');
            }
        } else {
            $return['error'][] = $this->module->l('An error occured during the payment');
        }

        echo Tools::jsonEncode($return);
        die();
    }

    /**
     * Execute the hook displayPaymentReturn
     */
    public function displayPaymentReturn()
    {
        $params = $this->displayHook();

        if ($params && is_array($params)) {
            return Hook::exec('displayPaymentReturn', $params, (int) $this->id_module);
        }

        return false;
    }

    /**
     * Execute the hook displayOrderConfirmation
     */
    public function displayOrderConfirmation()
    {
        $params = $this->displayHook();

        if ($params && is_array($params)) {
            return Hook::exec('displayOrderConfirmation', $params);
        }

        return false;
    }

    public function getOrderStatus($template)
    {
        /*
         * payment
         * payment_error
         * order_canceled
         * refund
         */
        return Db::getInstance()->getValue('SELECT id_order_state FROM '._DB_PREFIX_.'order_state_lang WHERE template = "'.pSQL($template).'" AND id_lang = "'.(int) $this->context->language->id.'"');
    }
}
