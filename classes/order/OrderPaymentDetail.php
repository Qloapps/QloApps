<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
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
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

class OrderPaymentDetailCore extends ObjectModel
{
    public $id_order_payment;
    public $id_order;
    public $amount;
    public $receipt_number;
    public $date_add;

    public static $definition = array(
        'table' => 'order_payment_detail',
        'primary' => 'id_order_payment_detail',
        'fields' => array(
            'id_order_payment' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_order' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'amount' =>              array('type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'required' => true),
            'receipt_number' =>      array('type' => self::TYPE_INT, 'validate' => 'isGenericName', 'size' => 64),
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

    public static function getLastPaymentReceiptNumber()
    {
        $sql = 'SELECT MAX(`receipt_number`) FROM `'._DB_PREFIX_.'order_payment_detail`';
        if (Configuration::get('PS_PAYMENT_RECEIPTS_RESET')) {
            $sql .= ' WHERE DATE_FORMAT(`date_add`, "%Y") = '.(int)date('Y');
        }
        return Db::getInstance()->getValue($sql);
    }

    public static function getPaymentDetailsByPaymentId($id_order_payment)
    {
        return Db::getInstance()->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'order_payment_detail`
            WHERE `id_order_payment` = '.(int)$id_order_payment
        );
    }

    public static function getByDateInterval($date_from, $date_to)
    {
        $payment_list = Db::getInstance()->executeS('
            SELECT DISTINCT opd.*
            FROM `'._DB_PREFIX_.'order_payment_detail` opd
            LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = opd.`id_order`)
            WHERE DATE_ADD(opd.`date_add`, INTERVAL -1 DAY) <= \''.pSQL($date_to).'\'
            AND opd.`date_add` >= \''.pSQL($date_from).'\'
            AND opd.`receipt_number` > 0
            '.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
            ORDER BY opd.`date_add` ASC, opd.`id_order_payment_detail` ASC
        ');

        return ObjectModel::hydrateCollection('OrderPaymentDetail', $payment_list);
    }

    public function getPaymentReceiptNumberFormated($id_lang, $id_shop = null)
    {
        $format = '%1$s%2$06d';

        if (Configuration::get('PS_PAYMENT_RECEIPTS_USE_YEAR')) {
            $format = '%1$s%2$06d/%3$s';
        }

        return sprintf($format, Configuration::get('PS_PAYMENT_RECEIPTS_PREFIX', (int)$id_lang, null, (int)$id_shop), $this->receipt_number, date('Y', strtotime($this->date_add)));
    }
}
