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

class QloDuitkuPaymentCallBackModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::init();
    }

    public function postProcess()
    {
        $apiKey = Configuration::get('QDP_DUITKU_PAYMENT_API_KEY');
        $merchantCode = isset($_POST['merchantCode']) ? $_POST['merchantCode'] : null;
        $paymentAmount = isset($_POST['amount']) ? $_POST['amount'] : null;
        $merchantOrderId = isset($_POST['merchantOrderId']) ? $_POST['merchantOrderId'] : null;
        $productDetail = isset($_POST['productDetail']) ? $_POST['productDetail'] : null;
        $additionalParam = isset($_POST['additionalParam']) ? $_POST['additionalParam'] : null;
        $paymentMethod = isset($_POST['paymentCode']) ? $_POST['paymentCode'] : null;
        $resultCode = isset($_POST['resultCode']) ? $_POST['resultCode'] : null;
        $merchantUserId = isset($_POST['merchantUserId']) ? $_POST['merchantUserId'] : null;
        $reference = isset($_POST['reference']) ? $_POST['reference'] : null;
        $signature = isset($_POST['signature']) ? $_POST['signature'] : null;

        $this->module->logger->log('Start callback transaction ', FileLogger::INFO);
        $this->module->logger->log($_POST, FileLogger::INFO);

        if (!empty($merchantCode) && !empty($paymentAmount) && !empty($merchantOrderId) && !empty($signature)) {
            $params = $merchantCode . $paymentAmount . $merchantOrderId . $apiKey;
            $calcSignature = md5($params);

            if ($signature == $calcSignature) {

                $orderResponse = explode('_', $merchantOrderId);
                $idCart = (int) end($orderResponse);
                $objCart = new Cart((int) $idCart);
                $objCustomer = new Customer((int) $objCart->id_customer);
                $id_currency = (int) $objCart->id_currency;
                $this->context->currency = new Currency($id_currency);

                $objDuitkuTransaction = new QdpDuitkuTransaction();

                $res = $objDuitkuTransaction->checkExists($reference);
                $this->module->logger->log('Fetch transaction', FileLogger::DEBUG);
                $this->module->logger->log($res, FileLogger::DEBUG);

                if (is_array($res) && !empty($res['id_duitku_payment_transaction'])) {
                    $objDuitkuTransaction = new QdpDuitkuTransaction((int)$res['id_duitku_payment_transaction']);
                } else {
                    $objDuitkuTransaction = new QdpDuitkuTransaction();
                }

                $objDuitkuTransaction->id_transaction = $reference;
                $objDuitkuTransaction->environment = Configuration::get('QDP_DUITKU_PAYMENT_ENVIRONMENT');
                $objDuitkuTransaction->id_cart = (int) $idCart;
                $objDuitkuTransaction->id_order = $merchantOrderId;
                $objDuitkuTransaction->id_currency = $id_currency;

                if ($resultCode == '00') {
                    if ($objCart->is_advance_payment) {
                        $cartTotalAmount = $objCart->getOrderTotal(true, Cart::ADVANCE_PAYMENT);
                        $orderStatus = Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED');
                    } else {
                        $cartTotalAmount = $objCart->getOrderTotal(true, Cart::BOTH);
                        $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
                    }
                    $transactionStatus = QdpDuitkuTransaction::QDP_TRANSACTION_COMPLETED;
                } else {
                    if ($objCart->is_advance_payment) {
                        $cartTotalAmount = $objCart->getOrderTotal(true, Cart::ADVANCE_PAYMENT);
                    } else {
                        $cartTotalAmount = $objCart->getOrderTotal(true, Cart::BOTH);
                    }
                    $orderStatus = Configuration::get('PS_OS_ERROR');
                    $transactionStatus = QdpDuitkuTransaction::QDP_TRANSACTION_FAILED;
                }


                $cart_amount = round($cartTotalAmount, 2);
                $objDuitkuTransaction->cart_total = $cart_amount;

                $objDuitkuTransaction->status = $transactionStatus;

                $objDuitkuTransaction->save();

                $extraVars = array();

                $extraVars['transaction_id'] = $reference;
                $lockExists =  $objDuitkuTransaction->getCartLock($idCart);
                if (Validate::isLoadedObject($objCart)) {
                    $this->module->logger->log('Callback After Cart Validation in return.', FileLogger::DEBUG);
                    if ($lockExists) {
                        $this->module->logger->log('Order already in process for cart id ' . $idCart, FileLogger::DEBUG);

                        $orderCounter = 0;
                        while (1) {
                            sleep(1);

                            if ($objCart->OrderExists()) {
                                $objDuitkuTransaction->removeCartLock($idCart);
                                $allOrders = Order::getAllOrdersByCartId($idCart);
                                $this->module->logger->log('Orders.', FileLogger::INFO);
                                foreach ($allOrders as $ord) {
                                    if ($ord['current_state'] != Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED')
                                        && $ord['current_state'] != Configuration::get('PS_OS_PAYMENT_ACCEPTED')
                                    ) {
                                        $order_state = new OrderState($orderStatus);
                                        $history = new OrderHistory();
                                        $history->id_order = $ord['id_order'];
                                        $history->id_employee = 0;
                                        $order = new Order($ord['id_order']);

                                        $useExistingPayment = false;
                                        if (!$order->hasInvoice()) {
                                            $useExistingPayment = true;
                                        }
                                        $history->changeIdOrderState((int) $order_state->id, $order, $useExistingPayment);

                                        $carrier = new Carrier($order->id_carrier, $order->id_lang);
                                        $templateVars = array();

                                        if (!($history->addWithemail())) {
                                            $this->module->logger->log('An error occurred while changing order status, or we were unable to send an email to the customer.');
                                        }
                                    }
                                }
                                break;
                            }
                            $orderCounter++;
                            if ($orderCounter > 5) {
                                $this->module->logger->log('Order not created for cart id ' . $idCart . ' after 5 seconds', FileLogger::DEBUG);
                                break;
                            }
                        }
                    } else {
                        $objDuitkuTransaction->addCartLock($idCart);
                        if ($this->module->validateOrder(
                            (int) $idCart,
                            $orderStatus,
                            $cart_amount,
                            $this->module->l('Duitku Payment', 'return'),
                            null,
                            $extraVars,
                            (int) $id_currency,
                            false,
                            $objCustomer->secure_key
                        )) {
                            $this->module->logger->log('Callback Process Order created.', FileLogger::DEBUG);
                        } else {
                            $this->module->logger->log('Callback Process Order already exist!!.', FileLogger::DEBUG);
                        }
                    }
                } else {
                    $this->module->logger->log(' Invalid Cart Object', FileLogger::DEBUG);
                }
            } else {
                $this->module->logger->log(' Bad Signature', FileLogger::DEBUG);
            }
        } else {
            $this->module->logger->log(' Bad Parameter', FileLogger::DEBUG);
        }
    }
}
