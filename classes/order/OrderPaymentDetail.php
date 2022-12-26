<?php
/**
* 2010-2022 Webkul.
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
            'id_order' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'amount' =>              array('type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'required' => true),
            'date_add' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

    /**
     * Get Order Payments By Order ID
     *
     * @param int $id_invoice Order ID
     * @return array OrderPayment detail
     */
    public static function getByOrderId($id_order)
    {
        return Db::getInstance()->executeS('
            SELECT opd.*, op.`id_currency`, op.`payment_method`, op.`conversion_rate`, op.`transaction_id`, op.`card_number`, op.`card_brand`, op.`card_expiration`, op.`card_holder`
            FROM `'._DB_PREFIX_.'order_payment_detail` opd
            INNER JOIN `'._DB_PREFIX_.'order_payment`op ON (op.`id_order_payment` = opd.`id_order_payment`)
            WHERE `id_order` = '.(int)$id_order
        );
    }

    /**
     * Get Order Payments By Invoice ID
     *
     * @param int $id_invoice Invoice ID
     * @return array OrderPayment detail
     */
    public static function getByInvoiceId($id_invoice)
    {
        return Db::getInstance()->executeS('
            SELECT opd.`id_order_payment_detail`, opd.`id_order_payment`, opd.`id_order`, opd.`date_add`, op.*, opd.`amount` as `amount`
            FROM `'._DB_PREFIX_.'order_payment_detail` opd
            INNER JOIN `'._DB_PREFIX_.'order_payment` op ON (opd.`id_order_payment` = op.`id_order_payment`)
            INNER JOIN `'._DB_PREFIX_.'order_invoice_payment` oip ON (oip.`id_order_payment_detail` = opd.`id_order_payment_detail`)
            WHERE oip.`id_order_invoice` = '.(int)$id_invoice
        );
    }
}
