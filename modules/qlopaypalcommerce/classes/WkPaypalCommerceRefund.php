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

class WkPaypalCommerceRefund extends ObjectModel
{
    // Write all table fields here
    public $order_trans_id;
    public $paypal_refund_id;
    public $refund_amount;
    public $refund_reason;
    public $refund_type;
    public $currency_code;
    public $response;
    public $refund_status;
    public $date_add;
    public $date_upd;

    // refund types
    const WK_PAYPAL_COMMERCE_REFUND_TYPE_FULL = 1;
    const WK_PAYPAL_COMMERCE_REFUND_TYPE_PARTIAL = 2;

    public static $definition = array(
        'table' =>  'wk_paypal_commerce_refund',
        'primary'   =>  'id_paypal_commerce_refund',
        'multilang' =>  false,
        'fields'    =>  array(
            'order_trans_id'   =>  array(
                'type'  =>  self::TYPE_INT,
                'validate'  =>  'isUnsignedInt'
            ),
            'paypal_refund_id' =>  array(
                'type'  =>  self::TYPE_STRING
            ),
            'refund_amount' =>  array(
                'type'  =>  self::TYPE_FLOAT
            ),
            'currency_code' =>  array(
                'type'  =>  self::TYPE_STRING
            ),
            'refund_reason' =>  array(
                'type'  =>  self::TYPE_STRING
            ),
            'refund_type' =>  array(
                'type'  =>  self::TYPE_INT,
                'validate'  =>  'isUnsignedInt'
            ),
            'response' =>  array(
                'type'  =>  self::TYPE_STRING
            ),
            'refund_status'    =>  array(
                'type'  =>  self::TYPE_STRING
            ),
            'date_add'  =>  array(
                'type'  =>  self::TYPE_DATE,
                'required'  =>  false,
                'validate'  =>  'isDateFormat'
            ),
            'date_upd'  =>  array(
                'type'  =>  self::TYPE_DATE,
                'required'  =>  false,
                'validate'  =>  'isDateFormat'
            )
        )
    );

    public static function getRefundListByTransID($idTrans)
    {
        $refunds = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT pcr.*, pco.`id_currency`
            FROM `'._DB_PREFIX_.'wk_paypal_commerce_refund` pcr
            LEFT JOIN `'._DB_PREFIX_.'wk_paypal_commerce_order` pco
            ON pcr.`order_trans_id` = pco.`id_paypal_commerce_order`
            WHERE pcr.`order_trans_id` = ' .(int) $idTrans.'
            ORDER BY pcr.`id_paypal_commerce_refund` DESC'
        );

        if ($refunds) {
            foreach ($refunds as $key => $value) {
                $refunds[$key]['amount_refunded_formatted'] = Tools::displayPrice(
                    $value['refund_amount'],
                    new Currency((int)$value['id_currency'])
                );
            }
            return $refunds;
        }
        return false;
    }

    /**
     * Get total refunded amount by order id
     * @param  int $idTrans ID
     * @param  bool $formatted Get formatted amount
     * @return float Total refunded amount
     */
    public static function getTotalRefundedAmount($idTrans, $formatted = false)
    {
        $totalRefund = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow(
            'SELECT SUM(pcr.`refund_amount`) AS sum_amount_refunded, pco.`id_currency`
            FROM `'._DB_PREFIX_.'wk_paypal_commerce_refund` pcr
            LEFT JOIN `'._DB_PREFIX_.'wk_paypal_commerce_order` pco
            ON pcr.`order_trans_id` = pco.`id_paypal_commerce_order`
            WHERE pcr.`order_trans_id` = ' .(int)$idTrans.'
            GROUP BY pcr.`order_trans_id`'
        );

        if ($totalRefund) {
            $formattedAmt = Tools::displayPrice(
                $totalRefund['sum_amount_refunded'],
                new Currency((int)$totalRefund['id_currency'])
            );
            return $formatted ? $formattedAmt : $totalRefund['sum_amount_refunded'];
        }
        return false;
    }
}
