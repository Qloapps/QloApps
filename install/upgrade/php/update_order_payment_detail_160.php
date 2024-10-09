<?php
/**
* 2010-2023 Webkul.
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
* @copyright 2010-2023 Webkul IN
* @license LICENSE.txt
*/

function update_order_payment_detail_160()
{

    if ($orderPayment = Db::getInstance()->executeS('SELECT `order_reference`, `id_order_payment`, `amount` FROM `'._DB_PREFIX_.'order_payment`')) {
        $references = array();
        foreach ($orderPayment as $payment) {
            if (!isset($references[$payment['order_reference']])) {
                $references[$payment['order_reference']] = array();
            }
            $references[$payment['order_reference']][] = array(
                'id_order_payment' => $payment['id_order_payment'],
                'amount' => $payment['amount']
            );
        }
        if (!empty($references)) {
            foreach ($references as $key => $reference) {
                $orders = Db::getInstance()->executeS('SELECT `id_order`, `advance_paid_amount` FROM `'._DB_PREFIX_.'orders` WHERE `reference` = "'.pSQL($key).'"');
                if (count($orders) == 1) {
                    $order = array_shift($orders);
                    foreach ($reference as $payment) {
                        $objOrderPaymentDetail = new OrderPaymentDetail();
                        $objOrderPaymentDetail->id_order_payment = $payment['id_order_payment'];
                        $objOrderPaymentDetail->id_order = $order['id_order'];
                        $objOrderPaymentDetail->amount = $payment['amount'];
                        $objOrderPaymentDetail->save();
                    }
                } else {
                    foreach ($reference as $key => $payment) {
                        if ($key === 0) {
                            $totalAdvancePayment = array_sum(array_column($orders, 'advance_paid_amount'));
                            if ($payment['amount'] == $totalAdvancePayment) {
                                $lastOrder = end($orders);
                                $objOrder = new Order($lastOrder['id_order']);
                                $objOrder->total_paid_real -= $totalAdvancePayment;
                                $objOrder->save();
                                foreach ($orders as $order) {
                                    $objOrderPaymentDetail = new OrderPaymentDetail();
                                    $objOrderPaymentDetail->id_order_payment = $payment['id_order_payment'];
                                    $objOrderPaymentDetail->id_order = $order['id_order'];
                                    $objOrderPaymentDetail->amount = $order['advance_paid_amount'];
                                    $objOrderPaymentDetail->save();

                                    $objOrder = new Order($order['id_order']);
                                    $objOrder->total_paid_real += $objOrder->advance_paid_amount;
                                    $objOrder->save();

                                }
                            }
                        } else {
                            foreach ($orders as $order) {
                                $ordersPayment = Db::getInstance()->getRow('
                                    SELECT `total_paid_real`, IFNULL(SUM(`amount`), 0) as payment_amount
                                    FROM `'._DB_PREFIX_.'orders` o
                                    LEFT JOIN `'._DB_PREFIX_.'order_payment_detail` opd
                                    ON (opd.`id_order` = o.`id_order`)
                                    WHERE o.`id_order` = '.(int) $order['id_order'].'
                                    GROUP BY o.`id_order`'
                                );
                                if ($ordersPayment['total_paid_real'] > $ordersPayment['payment_amount']) {
                                    $objOrderPaymentDetail = new OrderPaymentDetail();
                                    $objOrderPaymentDetail->id_order_payment = $payment['id_order_payment'];
                                    $objOrderPaymentDetail->id_order = $order['id_order'];
                                    $objOrderPaymentDetail->amount = $ordersPayment['total_paid_real'] - $ordersPayment['payment_amount'];
                                    $objOrderPaymentDetail->save();
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return true;
}

