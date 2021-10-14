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
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

include_once dirname(__FILE__).'/../../../config/config.inc.php';
include_once _PS_ROOT_DIR_.'/init.php';


/*
 * Init var
 */

if (version_compare(_PS_VERSION_, '1.5', '<')) {
    include_once _PS_MODULE_DIR_.'paypal/backward_compatibility/backward.php';
    $context = Context::getContext();
    $ajax = Tools::getValue('ajax');
    /*
     * Pour la version 1.4
     */
    if ($ajax) {
        displayAjax($context);
    } else {
        displayConfirm($context);
    }
} else {
    $values = array(
        'id_cart' => (int) Tools::getValue('id_cart'),
        'id_module' => (int) Module::getInstanceByName('paypal')->id,
        'paymentId' => Tools::getValue('paymentId'),
        'token' => Tools::getValue('token'),
    );
    $values['key'] = Context::getContext()->customer->secure_key;
    $link = Context::getContext()->link->getModuleLink('paypal', 'submitplus', $values);
    Tools::redirect($link);
    die();
}

function displayConfirm($context)
{
    include _PS_ROOT_DIR_.'/header.php';

    $paypal = new PayPal();

    $id_module = (int) Module::getInstanceByName('paypal')->id;
    $id_cart = Tools::getValue('id_cart');
    $paymentId = Tools::getValue('paymentId');
    $token = Tools::getValue('token');

    if (!empty($id_cart) && !empty($paymentId) && !empty($token)) {
        $CallApiPaypalPlus = new CallApiPaypalPlus();
        $payment = json_decode($CallApiPaypalPlus->lookUpPayment($paymentId));

        if (isset($payment->state)) {
            $context->smarty->assign('state', $payment->state);

            $paypal->assignCartSummary();

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

                    $context->smarty->assign(array(
                        'PayerID' => $payment->payer->payer_info->payer_id,
                        'paymentId' => $paymentId,
                        'id_cart' => $id_cart,
                        'totalAmount' => Tools::displayPrice(Cart::getTotalCart($id_cart)),
                        'linkSubmitPlus' => _MODULE_DIR_.'paypal/paypal_plus/submit.php',
                    ));
                    break;

                case 'canceled':
                    /* LookUp cancel */
                    $paypal->validateOrder(
                        $id_cart,
                        getOrderStatus('order_canceled'),
                        $payment->transactions[0]->amount->total,
                        $payment->payer->payment_method,
                        null,
                        $transaction
                    );
                    break;

                default:
                    /* Erreur de payment */
                    $paypal->validateOrder(
                        $id_cart,
                        getOrderStatus('payment_error'),
                        $payment->transactions[0]->amount->total,
                        $payment->payer->payment_method,
                        null,
                        $transaction
                    );

                    break;
            }
        } else {
            $context->smarty->assign('state', 'failed');
        }
    } else {
        $context->smarty->assign('state', 'failed');
    }

    echo $context->smarty->fetch(_PS_MODULE_DIR_.'paypal/views/templates/front/order-confirmation-plus.tpl');

    include _PS_ROOT_DIR_.'/footer.php';

    die();
}

function displayAjax($context)
{
    $id_cart = Tools::getValue('id_cart');
    $payerID = Tools::getValue('payerID');
    $paymentId = Tools::getValue('paymentId');
    $submit = Tools::getValue('submit');
    $paypal = new PayPal();
    $return = array();

    if (
        (!empty($id_cart) && $context->cart->id == $id_cart) &&
        !empty($payerID) &&
        !empty($paymentId) &&
        !empty($submit)
    ) {
        include_once _PS_MODULE_DIR_.'paypal/paypal.php';

        $CallApiPaypalPlus = new CallApiPaypalPlus();
        $payment = json_decode($CallApiPaypalPlus->executePayment($payerID, $paymentId));

        if (isset($payment->state)) {
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
                        $id_cart,
                        getOrderStatus('payment'),
                        $payment->transactions[0]->amount->total,
                        $payment->payer->payment_method,
                        null,
                        $transaction
                    );
                    $return['success'][] = $paypal->l('Your payment has been taken into account');
                } else {
                    $paypal->validateOrder(
                        $id_cart,
                        getOrderStatus('payment_error'),
                        $payment->transactions[0]->amount->total,
                        $payment->payer->payment_method,
                        null,
                        $transaction
                    );
                    $return['error'][] = $paypal->l('An error occured during the payment');
                }

                if (isset($payment->payment_instruction)) {
                    $order = Order::getOrderByCartId($id_cart);
                    $paypal_plus_pui = new PaypalPlusPui();
                    $paypal_plus_pui->id_order = $order->id;
                    $paypal_plus_pui->pui_informations = json_encode($payment->payment_instruction);
                }
            } elseif ($submit == 'confirmCancel') {
                $paypal->validateOrder(
                    $id_cart,
                    getOrderStatus('order_canceled'),
                    $payment->transactions[0]->amount->total,
                    $payment->payer->payment_method,
                    null,
                    $transaction
                );
                $return['success'][] = $paypal->l('Your order has been canceled');
            } else {
                $return['error'][] = $paypal->l('An error occured during the payment');
            }
        } else {
            $return['error'][] = $paypal->l('An error occured during the payment');
        }
    } else {
        $return['error'][] = $paypal->l('An error occured during the payment');
    }

    echo json_encode($return);
    die();
}

function getOrderStatus($template)
{
    /*
     * payment
     * payment_error
     * order_canceled
     * refund
     */
    $context = Context::getContext();
    return Db::getInstance()->getValue('SELECT id_order_state FROM '._DB_PREFIX_.'order_state_lang WHERE template = "'.pSQL($template).'" AND id_lang = "'.(int) $context->cookie->id_lang.'"');
}
