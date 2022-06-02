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
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
*/

class OrderPaymentDetailCore extends ObjectModel
{
    public $id_order_payment;
    public $id_order;
    public $amount;
    public $date_add;

    public static $definition = array(
        'table' => 'order_payment_detail',
        'primary' => 'id_order_payment_detail',
        'fields' => array(
            'id_order_payment' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_order' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'amount' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'required' => true),
            'date_add' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
        'associations' => array(
            'order_payment' => array('type' => self::HAS_ONE, 'table' => 'order_payment')
        )
    );

    public static function getByOrderId($id_order)
    {
        return Db::getInstance()->executeS('
        SELECT opd.*, op.`id_currency`, op.`payment_method`, op.`conversion_rate`, op.`transaction_id`, op.`card_number`, op.`card_brand`, op.`card_expiration`, op.`card_holder`
        FROM `'._DB_PREFIX_.'order_payment_detail` opd
        INNER JOIN `'._DB_PREFIX_.'order_payment`op ON (op.`id_order_payment` = opd.`id_order_payment`)
        WHERE `id_order` = '.(int)$id_order);
    }

    /**
     * Get Order Payments By Invoice ID
     *
     * @param int $id_invoice Invoice ID
     * @return PrestaShopCollection Collection of OrderPayment
     */
    public static function getByInvoiceId($id_invoice)
    {
        $payments = Db::getInstance()->executeS('SELECT id_order_payment_detail FROM `'._DB_PREFIX_.'order_invoice_payment` WHERE id_order_invoice = '.(int)$id_invoice);
        if (!$payments) {
            return array();
        }

        $payment_list = array();
        foreach ($payments as $payment) {
            $payment_list[] = $payment['id_order_payment_detail'];
        }
        $order_payments = new DbQuery();
        $order_payments->select('opd.*, op.*, opd.`amount` as `amount`');
        $order_payments->from('order_payment_detail', 'opd');
        $order_payments->innerJoin('order_payment', 'op', 'opd.`id_order_payment` = op.`id_order_payment`');
        $order_payments->where('id_order_payment_detail IN ('.pSQL(implode(', ', $payment_list)).')');
        return Db::getInstance()->executeS($order_payments);
        // $payments = new PrestaShopCollection('OrderPaymentDetail');
        // $payments->join('order_payment', 'id_order_payment');
        // $payments->where('id_order_payment_detail', 'IN', $payment_list);
        // ddd($payments->getAll());
    }
}
