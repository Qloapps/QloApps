<?php
/**
* 2010-2016 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkPaypalTransaction extends ObjectModel
{
	public $id_cart;
	public $pay_key;
	public $status;
	public $sender_email;
	public $action_type;
	public $payment_method;
	public $payment_info;
	public $memo;
	public $currency_code;
	public $is_delayed_paid;
	public $is_refunded;
	public $date_add;
	public $date_upd;

	public static $definition = array(
		'table' => 'wkpaypal_transaction',
		'primary' => 'id',
		'fields' => array(
			'id_cart' => array('type' => self::TYPE_INT),
			'currency_code' => array('type' => self::TYPE_STRING, 'validate' => 'isLanguageIsoCode', 'size' => 3),
			'pay_key' => array('type' => self::TYPE_STRING),
			'status' => array('type' => self::TYPE_STRING),
			'sender_email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail'),
			'action_type' => array('type' => self::TYPE_STRING),
			'payment_method' => array('type' => self::TYPE_INT),
			'memo' => array('type' => self::TYPE_STRING),
			'payment_info' => array('type' => self::TYPE_STRING),
			'is_delayed_paid' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'is_refunded' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'date_add' => 	array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
			'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
		),
	);

	public static function isPayKeyExist($pay_key)
	{
		return Db::getInstance()->getRow(
			'SELECT * FROM `'._DB_PREFIX_."wkpaypal_transaction`
			WHERE `pay_key`='$pay_key'"
		);
	}

	public function updatePaypalOrderStatus($id_order, $id_order_state)
	{
		$order = new Order($id_order);
		$order_state = new OrderState($id_order_state);
		$current_order_state = $order->getCurrentOrderState();
		if ($current_order_state->id != $order_state->id) {
			// Create new OrderHistory
            $history = new OrderHistory();
            $history->id_order = $order->id;
            $use_existings_payment = false;
            if (!$order->hasInvoice()) {
                $use_existings_payment = true;
            }
            $history->changeIdOrderState((int) $order_state->id, $order, $use_existings_payment);
            $carrier = new Carrier($order->id_carrier, $order->id_lang);
            $templateVars = array();
            if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number) {
                $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));
            }
            // Save all changes
            if ($history->addWithemail(true, $templateVars)) {
                // synchronizes quantities if needed..
                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                    foreach ($order->getProducts() as $product) {
                        if (StockAvailable::dependsOnStock($product['product_id'])) {
                            StockAvailable::synchronize($product['product_id'], (int) $product['id_shop']);
                        }
                    }
                }
                return true;
            }
		} elseif ($current_order_state->id == $order_state->id) {
            return true;
        }
	}

	public static function saveAwaitingTransaction($id_order)
	{
		$txn = new self();
		$txn->id_order = $id_order;
		$txn->save();
	}

	public function updateTxnDetails($id_cart, $id_order)
	{
		$id = Db::getInstance()->getValue(
			'SELECT `id` FROM `'._DB_PREFIX_.bqSQL(self::$definition['table']).'`
			WHERE `id_cart` = '.(int)$id_cart
		);

		$obj_txn = new MpPaypalTransaction($id);
		$obj_txn->id_order = $id_order;
		$obj_txn->save();
	}


	public function getIdByPayKey($pay_key)
	{
		return Db::getInstance()->getValue(
			'SELECT `id` FROM `'._DB_PREFIX_."wkpaypal_transaction`
			WHERE `pay_key` = '$pay_key'"
		);
	}

	public static function getTransactionByIdCart($id_cart)
	{
		return Db::getInstance()->getRow(
			'SELECT * FROM `'._DB_PREFIX_.'wkpaypal_transaction`
			WHERE `id_cart`='. (int)$id_cart
		);
	}

	public static function updateDelayedPaid($pay_key)
	{
		$id = Db::getInstance()->update(
			'wkpaypal_transaction',
			array('is_delayed_paid' => 1),
			'`pay_key` = \''.pSQL($pay_key).'\''
		);
	}

	public function getOrdersByReference($reference)
	{
		if ($reference) {
			return Db::getInstance()->executeS('SELECT `id_order` FROM `'._DB_PREFIX_.'orders` WHERE reference = \''. pSQL($reference).'\'');
		}

		return false;
	}
}