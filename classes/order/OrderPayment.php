<?php
/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderPaymentCore extends ObjectModel
{
    const PAYMENT_TYPE_ONLINE = 1;
    const PAYMENT_TYPE_PAY_AT_HOTEL = 2;
    const PAYMENT_TYPE_REMOTE_PAYMENT = 3;

    public $order_reference;
    public $id_currency;
    public $amount;
    public $payment_method;
    public $payment_type;
    public $conversion_rate;
    public $transaction_id;
    public $card_number;
    public $card_brand;
    public $card_expiration;
    public $card_holder;
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'order_payment',
        'primary' => 'id_order_payment',
        'fields' => array(
            'order_reference' =>    array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 9),
            'id_currency' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'amount' =>            array('type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'required' => true),
            'payment_method' =>    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'payment_type' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'conversion_rate' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'transaction_id' =>    array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 254),
            'card_number' =>        array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 254),
            'card_brand' =>        array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 254),
            'card_expiration' =>    array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 254),
            'card_holder' =>        array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 254),
            'date_add' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function add($autodate = true, $nullValues = false)
    {
        if (parent::add($autodate, $nullValues)) {
            Hook::exec('actionPaymentCCAdd', array('paymentCC' => $this));
            return true;
        }
        return false;
    }

    /**
     * Get the detailed payment of an order
     *
     * @deprecated 1.5.3.0
     * @param int $id_order
     * @return array
     */
    public static function getByOrderId($id_order)
    {
        Tools::displayAsDeprecated();
        $order = new Order($id_order);
        return OrderPayment::getByOrderReference($order->reference);
    }

    /**
     * Get the detailed payment of an order
     * @param int $order_reference
     * @return array
     * @since 1.5.0.13
     */
    public static function getByOrderReference($order_reference)
    {
        return ObjectModel::hydrateCollection('OrderPayment',
            Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'order_payment`
			WHERE `order_reference` = \''.pSQL($order_reference).'\'')
        );
    }

    /**
     * Get Order Payments By Invoice ID
     *
     * @param int $id_invoice Invoice ID
     * @return PrestaShopCollection Collection of OrderPayment
     */
    public static function getByInvoiceId($id_invoice)
    {
        $payments = Db::getInstance()->executeS('SELECT id_order_payment FROM `'._DB_PREFIX_.'order_invoice_payment` WHERE id_order_invoice = '.(int)$id_invoice);
        if (!$payments) {
            return array();
        }

        $payment_list = array();
        foreach ($payments as $payment) {
            $payment_list[] = $payment['id_order_payment'];
        }

        $payments = new PrestaShopCollection('OrderPayment');
        $payments->where('id_order_payment', 'IN', $payment_list);
        return $payments;
    }

    /**
     * Return order invoice object linked to the payment
     *
     * @param int $id_order Order Id
     *
     * @since 1.5.0.13
     */
    public function getOrderInvoice($id_order)
    {
        $res = Db::getInstance()->getValue('
		SELECT id_order_invoice
		FROM `'._DB_PREFIX_.'order_invoice_payment`
		WHERE id_order_payment = '.(int)$this->id.'
		AND id_order = '.(int)$id_order);

        if (!$res) {
            return false;
        }

        return new OrderInvoice((int)$res);
    }

    /**
     * Provides the average conversion rate for a given order in any currency
     * @param [string] $order_reference
     * @param [int] $idCurrency
     * @return float
     */
    public function getAverageConversionRate($order_reference, $idCurrency)
    {
        return Db::getInstance()->getValue('SELECT (SUM(`amount` * `conversion_rate`) / SUM(`amount`)) FROM `'._DB_PREFIX_.'order_payment` WHERE `order_reference` = \''.pSQL($order_reference).'\' AND `id_currency` = '.(int)$idCurrency);
    }

    // ── REPORT METHODS ────────────────────────────────────────────────────────

    /**
     * Payment detail rows for the payment report.
     *
     * @param array $params date_from, date_to, id_hotel, id_order, id_customer, payment_method
     * @return array
     */
    public static function getPaymentsInfo(array $params)
    {
        $dateFrom      = pSQL($params['date_from']);
        $dateTo        = pSQL(isset($params['date_to']) ? $params['date_to'] : $params['date_from']);
        $idHotel       = isset($params['id_hotel'])       ? $params['id_hotel']              : false;
        $idOrder       = isset($params['id_order'])       ? (int) $params['id_order']       : 0;
        $idCustomer    = isset($params['id_customer'])    ? (int) $params['id_customer']    : 0;
        $paymentMethod = isset($params['payment_method']) ? pSQL($params['payment_method']) : '';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT op.`id_order_payment`, op.`date_add`, o.`id_order`,
            op.`order_reference` AS reference,
            CONCAT(c.`firstname`, " ", c.`lastname`) AS customer_name,
            op.`payment_method`, op.`payment_type`, op.`amount`, op.`conversion_rate`,
            op.`transaction_id`, cur.`iso_code` AS currency_iso, cur.`sign` AS currency_sign
            FROM `'._DB_PREFIX_.'order_payment` op
            INNER JOIN `'._DB_PREFIX_.'orders` o ON (o.`reference` = op.`order_reference` AND o.`valid` = 1)
            INNER JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)
            LEFT JOIN `'._DB_PREFIX_.'currency` cur ON (cur.`id_currency` = op.`id_currency`)
            INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
                ON (hbd.`id_order` = o.`id_order` AND hbd.`is_cancelled` = 0)
            WHERE op.`date_add` BETWEEN "'.$dateFrom.' 00:00:00" AND "'.$dateTo.' 23:59:59"'
            .($idOrder       ? ' AND o.`id_order` = '.$idOrder                       : '')
            .($idCustomer    ? ' AND o.`id_customer` = '.$idCustomer                 : '')
            .($paymentMethod ? ' AND op.`payment_method` = "'.$paymentMethod.'"'     : '')
            .HotelBranchInformation::addHotelRestriction($idHotel, 'hbd').'
            GROUP BY op.`id_order_payment`
            ORDER BY op.`date_add` DESC'
        );
    }

    /**
     * Distinct payment methods used in hotel orders.
     *
     * @param int|false $idHotel
     * @return array rows: payment_method
     */
    public static function getDistinctPaymentMethods($idHotel = false)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT DISTINCT op.`payment_method`
            FROM `'._DB_PREFIX_.'order_payment` op
            INNER JOIN `'._DB_PREFIX_.'orders` o
                ON (o.`reference` = op.`order_reference` AND o.`valid` = 1)
            INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
                ON (hbd.`id_order` = o.`id_order` AND hbd.`is_cancelled` = 0)
            WHERE op.`payment_method` IS NOT NULL AND op.`payment_method` != ""'
            .HotelBranchInformation::addHotelRestriction($idHotel, 'hbd').'
            ORDER BY op.`payment_method`'
        );
    }
}
