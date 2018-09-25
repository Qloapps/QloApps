<?php
/**
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkPaypalRefund extends ObjectModel
{
	public $transaction_id;
	public $refund_details;
	public $date_add;

	public static $definition = array(
		'table' => 'wkpaypal_refund',
		'primary' => 'id',
		'fields' => array(
			'transaction_id' => array('type' => self::TYPE_INT),
			'refund_details' => array('type' => self::TYPE_STRING),
			'date_add' => 	array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
		),
	);

	public static function getRefundHistoryByTransactionId($transactionId)
	{
		return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'wkpaypal_refund`	WHERE `transaction_id` = '.(int) $transactionId);
	}
}