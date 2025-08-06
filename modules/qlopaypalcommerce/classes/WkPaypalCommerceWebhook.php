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

class WkPaypalCommerceWebhook
{
    public function capturePending($eventData)
    {
        $transaction_id = $eventData['resource']['id'];

        $objPaypalOrder = new WKPayPalCommerceOrder();
        if ($objPaypalOrder->getTransactionDetailsByPaypalTransaction(
            $transaction_id
        )) {
            $ppOrderStatus = $eventData['resource']['status'];
            $this->updateOrderCapturePaypalOrderStatus($transaction_id, $ppOrderStatus);
        }
    }

    public function captureRefunded($eventData)
    {
        $refundID = $eventData['resource']['id'];

        $id = Db::getInstance()->getValue(
            'SELECT `id_paypal_commerce_refund` FROM `'._DB_PREFIX_.'wk_paypal_commerce_refund`
            WHERE `paypal_refund_id` =  "' . pSQL($refundID) .'"'
        );

        $refundObj = new WkPaypalCommerceRefund($id);
        $refundObj->paypal_refund_id = $refundID;
        $refundObj->refund_amount = (float)$eventData['resource']['amount']['value'];
        $refundObj->currency_code = $eventData['resource']['amount']['currency_code'];
        if (isset($eventData['resource']['note_to_payer'])) {
            $refundObj->refund_reason = $eventData['resource']['note_to_payer'];
        }
        $refundObj->response = Tools::jsonEncode($eventData);
        $refundObj->refund_status = $eventData['resource']['status'];
        $refundObj->save();
    }

    public function captureDenied($eventData)
    {
        $transaction_id = $eventData['resource']['id'];
        $objPaypalOrder = new WKPayPalCommerceOrder();
        if ($transactionData = $objPaypalOrder->getTransactionDetailsByPaypalTransaction(
            $transaction_id
        )) {
            $cartID = $transactionData['id_cart'];
            // if orders found for the transaction then set the order status
            if ($orders = WkPaypalCommerceHelper::getOrdersByCartId($cartID)) {
                foreach ($orders as $order) {
                    $this->updatePaymentStatus(
                        (int)Configuration::get('PS_OS_ERROR'),
                        $order['id_order']
                    );
                }
            }

            $ppOrderStatus = $eventData['resource']['status'];
            $this->updateOrderCapturePaypalOrderStatus($transaction_id, $ppOrderStatus);
        }
    }

    public function orderCompleted($eventData)
    {
        $purchaseUnits = $eventData['resource']['purchase_units'];
        $payment_status = $eventData['resource']['status'];
        foreach ($purchaseUnits as $purchase) {
            $transaction_id = $purchase['payments']['captures'][0]['id'];

            $objPaypalOrder = new WKPayPalCommerceOrder();
            if ($transactionData = $objPaypalOrder->getTransactionDetailsByPaypalTransaction(
                $transaction_id
            )) {
                // Check if PayPal order is already finished with status COMPLETED
                if ($transactionData['pp_payment_status'] != 'COMPLETED') {
                    // if order completed then change the status of the order
                    if ($eventData['event_type'] == 'CHECKOUT.ORDER.COMPLETED') {
                        $cartID = $transactionData['id_cart'];
                        $objCart = new Cart($cartID);
                        if ($objCart->is_advance_payment) {
                            $orderStatus = Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED');
                        } else {
                            $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
                        }
                        if ($orders = WkPaypalCommerceHelper::getOrdersByCartId($cartID)) {
                            foreach ($orders as $order) {
                                $this->updatePaymentStatus(
                                    (int)$orderStatus,
                                    $order['id_order']
                                );
                            }
                        }
                    }
                }

                $this->updateOrderPaypalOrderStatus($transaction_id, $payment_status, $eventData['resource']);
            }
        }
    }

    public function captureCompleted($eventData)
    {
        $transaction_id = $eventData['resource']['id'];
        $objPaypalOrder = new WKPayPalCommerceOrder();
        if ($transactionData = $objPaypalOrder->getTransactionDetailsByPaypalTransaction(
            $transaction_id
        )) {
            $cartID = $transactionData['id_cart'];
            $objCart = new Cart($cartID);
            if ($objCart->is_advance_payment) {
                $orderStatus = Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED');
            } else {
                $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
            }
            if ($orders = WkPaypalCommerceHelper::getOrdersByCartId($cartID)) {
                foreach ($orders as $order) {
                    $this->updatePaymentStatus(
                        (int)$orderStatus,
                        $order['id_order']
                    );
                }
            }

            $ppOrderStatus = $eventData['resource']['status'];
            $this->updateOrderCapturePaypalOrderStatus($transaction_id, $ppOrderStatus);
        }
    }

    /**
     * updatePaymentStatus
     * @param  int $id_order_state Order Status ID
     * @param  int $id_order Order ID
     * @return void
     */
    public function updatePaymentStatus($id_order_state, $id_order)
    {
        $order = new Order($id_order);
        $currentOrderState = $order->getCurrentOrderState();
        if ($currentOrderState->id != $id_order_state) {
            $useExistPayment = false;
            if (!$order->hasInvoice()) {
                $useExistPayment = true;
            }

            $orderHistory = new OrderHistory();
            $orderHistory->id_order = (int)$id_order;
            $orderHistory->changeIdOrderState(
                (int)$id_order_state,
                $order,
                $useExistPayment
            );
            $orderHistory->addWithemail(true, null);
        }
    }

    public function updateOrderCapturePaypalOrderStatus($transaction_id, $payment_status)
    {
        return Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'wk_paypal_commerce_order`
            SET `pp_payment_status` = "'.pSQL($payment_status).'"
            WHERE `pp_transaction_id` = "'.pSQL($transaction_id).'"
            '
        );
    }

    public function updateOrderPaypalOrderStatus($transaction_id, $payment_status, $orderData)
    {
        return Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'wk_paypal_commerce_order`
            SET `pp_payment_status` = "'.pSQL($payment_status).'",
            `response` = "'.pSQL(Tools::jsonEncode($orderData)).'"
            WHERE `pp_transaction_id` = "'.pSQL($transaction_id).'"
            '
        );
    }
}
