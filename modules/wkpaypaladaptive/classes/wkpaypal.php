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

class WkPaypal extends ObjectModel
{
	public $id;
	public $id_customer;
	public $paypalemail;
	public $date_add;
	public $date_upd;

	public static $definition = array(
		'table' => 'wkpaypal',
		'primary' => 'id',
		'fields' => array(
			'id_customer' => array('type' => self::TYPE_INT, 'required' => true),
			'paypalemail' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true),
			'date_add' => 	array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
			'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
		),
	);

	public function findCutomerDetailByCid($id_customer)
	{
		return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'wkpaypal` WHERE `id_customer`='.(int)$id_customer);
	}
}