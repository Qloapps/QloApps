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

class QdpDuitkuTransaction extends objectModel
{
    const QDP_CURL_REQUEST_TYPE_POST = 1;
    const QDP_CURL_REQUEST_TYPE_GET = 2;

    const QDP_DUITKU_ENVIRONMENT_SANDBOX = 1;
    const QDP_DUITKU_ENVIRONMENT_PRODUCTION = 2;

    // payment status
    const QDP_TRANSACTION_COMPLETED = 1;
    const QDP_TRANSACTION_PARTIAL = 2;
    const QDP_TRANSACTION_AWAITING = 3;
    const QDP_TRANSACTION_FAILED = 4;

    const QDP_ORDER_ID_PREFIX = 'QLO_CART_ID_';
    const QDP_CUSTOMER_ID_PREFIX = 'QLO_CUSTOMER_ID_';
    const QDP_DUITKU_HOST_SANDBOX_URL = 'https://api-sandbox.duitku.com/api/merchant/';
    const QDP_DUITKU_HOST_PRODUCTION_URL = 'https://passport.duitku.com/webapi/api/merchant/';

    public $id_transaction;
    public $environment;
    public $id_cart;
    public $id_order;
    public $id_currency;
    public $cart_total;
    public $status;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'qdp_duitku_payment_transaction',
        'primary' => 'id_duitku_payment_transaction',
        'fields' => array(
            'id_transaction' => array('type' => self::TYPE_STRING, 'required' => true),
            'id_cart' => array('type' => self::TYPE_INT, 'required' => true),
            'id_order' => array('type' => self::TYPE_STRING, 'required' => true),
            'cart_total' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'status' => array('type' => self::TYPE_INT),
            'environment' => array('type' => self::TYPE_INT),
            'id_currency' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public static function getPaymentStatuses()
    {
        $objDuitkuPayment = Module::getInstanceByName('qloduitkupayment');

        return array(
            self::QDP_TRANSACTION_COMPLETED => $objDuitkuPayment->l('Completed', 'QDPCashfreeTransaction'),
            self::QDP_TRANSACTION_AWAITING => $objDuitkuPayment->l('Awaiting', 'QDPCashfreeTransaction'),
            self::QDP_TRANSACTION_FAILED => $objDuitkuPayment->l('Failed', 'QDPCashfreeTransaction'),
        );
    }

    public function checkExists($id_transaction)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "qdp_duitku_payment_transaction
        WHERE id_transaction = '" . pSQL($id_transaction) . "'";

        return Db::getInstance()->getRow($sql);
    }

    public function getCartLock($id_cart)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "qdp_duitku_order_lock
        WHERE id_cart = '" . (int) $id_cart . "'";

        return Db::getInstance()->getRow($sql);
    }

    public function addCartLock($id_cart)
    {
        $db = Db::getInstance();
        $db->insert(
            'qdp_duitku_order_lock',
            array(
                'id_cart' => (int) $id_cart,
                'date_add' => date('Y-m-d H:i:s'),
                'date_upd' => date('Y-m-d H:i:s'),
            )
        );

        return $db->Insert_ID();
    }

    public function removeCartLock($id_cart)
    {
        $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'qdp_duitku_order_lock
        WHERE id_cart = ' . (int) $id_cart;

        return Db::getInstance()->execute($sql);
    }   
}
