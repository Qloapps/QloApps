<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@qloapps.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to https://store.webkul.com/customisation-guidelines for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/afl-3.0.php Academic Free License 3.0
 */

class QloDuitkuPaymentReturnModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
        $response = Tools::getAllValues();

        $this->module->logger->log('Start return process.', FileLogger::INFO);
        $this->module->logger->log($response, FileLogger::INFO);

        if (isset($response['resultCode'])) {
            if ($response['resultCode'] == '00' || $response['resultCode'] == '01') {
                $merchantOrderId = Tools::getValue("merchantOrderId");
                $reference = Tools::getValue("reference");
                $orderResponse = explode('_', $merchantOrderId);
                $idCart = (int) end($orderResponse);
                $objCart = new Cart((int) $idCart);

                $objCustomer = new Customer((int) $objCart->id_customer);
                $id_currency = $objCart->id_currency;
                if ($objCart->is_advance_payment) {
                    $cartTotalAmount = $objCart->getOrderTotal(true, Cart::ADVANCE_PAYMENT);
                } else {
                    $cartTotalAmount = $objCart->getOrderTotal(true, Cart::BOTH);
                }

                $cartAmount = round($cartTotalAmount, 2);

                $objDuitkuTransaction = new QdpDuitkuTransaction();
                $objDuitkuTransaction->id_transaction = $reference;
                $objDuitkuTransaction->environment = Configuration::get('QDP_DUITKU_PAYMENT_ENVIRONMENT');
                $objDuitkuTransaction->id_cart = (int) $idCart;
                $objDuitkuTransaction->id_order = $merchantOrderId;
                $objDuitkuTransaction->id_currency = $id_currency;
                $objDuitkuTransaction->cart_total = $cartAmount;

                $objDuitkuTransaction->status = QdpDuitkuTransaction::QDP_TRANSACTION_AWAITING;
                $orderStatus = Configuration::get('PS_OS_AWAITING_PAYMENT');

                $res = $objDuitkuTransaction->checkExists($objDuitkuTransaction->id_transaction);

                $extraVars['transaction_id'] = $reference;
                $this->module->logger->log('Before Cart Validation in return.', FileLogger::DEBUG);
                $lockExists = $objDuitkuTransaction->getCartLock((int) $idCart);
                try {
                    if ($lockExists) {
                        $this->module->logger->log('Order already in process for cart id ' . $idCart, FileLogger::DEBUG);

                        $orderCounter = 0;
                        $redirect = false;

                        while (1) {
                            sleep(2);

                            if ($objCart->OrderExists()) {
                                $redirect = true;
                                $objDuitkuTransaction->removeCartLock($idCart);
                                break;
                            }

                            $orderCounter++;
                            if ($orderCounter > 5) {
                                $redirect = true;
                                $this->module->logger->log('Order not created for cart id ' . $idCart . ' after 5 seconds', FileLogger::DEBUG);

                                break;
                            }
                        }
                        if ($redirect) {
                            $url = $this->context->link->getPageLink('order-confirmation', true, null, [
                                'id_cart'   => $idCart,
                                'id_module' => $this->module->id,
                                'id_order'  => Order::getOrderByCartId($idCart),
                                'success'   => 1,
                                'key'       => $objCustomer->secure_key,
                            ]);

                            Tools::redirect($url);
                        }
                    } else {

                        $this->module->logger->log('After Cart Validation in return.', FileLogger::DEBUG);
                        $objDuitkuTransaction->addCartLock((int) $idCart);
                        if (!$res) {
                            $objDuitkuTransaction->save(); 
                        }

                        if ($this->module->validateOrder(
                            (int) $idCart,
                            $orderStatus,
                            $cartAmount,
                            $this->module->l('Duitku Payment', 'return'),
                            null,
                            $extraVars,
                            (int) $id_currency,
                            false,
                            $objCustomer->secure_key
                        )) {

                            $this->module->logger->log('Return Process Order created.', FileLogger::DEBUG);

                            $url = $this->context->link->getPageLink('order-confirmation', true, null, [
                                'id_cart'   => $idCart,
                                'id_module' => $this->module->id,
                                'id_order'  => Order::getOrderByCartId($idCart),
                                'success'   => 1,
                                'key'       => $objCustomer->secure_key,
                            ]);
                            Tools::redirect($url);
                        }
                    }

                    $this->module->logger->log('After Cart Validation in return.', FileLogger::DEBUG);
                } catch (Exception $e) {
                    Tools::redirect($this->context->link->getModuleLink('qloduitkupayment', 'error') . '?validation_err=1&id=' . $response['merchantOrderId']);
                }
            } else {
                $this->module->logger->log('Payment cancelled. Find POST dump below.', FileLogger::ERROR);
                $this->module->logger->log($response, FileLogger::ERROR);
                Tools::redirect($this->context->link->getPageLink('order-opc', true, null, array('duitku_err' => 1)));
            }
        } else {
            $this->module->logger->log('Order transaction failed: ' . $response['reference'], FileLogger::ERROR);
            Tools::redirect($this->context->link->getModuleLink('qloduitkupayment', 'error', array('auth_error' => 1)));
        }
    }
}
