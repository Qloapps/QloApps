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

class OrderCustomerGuestDetailCore extends ObjectModel
{
    public $id_order_customer_guest_detail;
    public $id_order;
    public $id_gender;
    public $firstname;
    public $lastname;
    public $email;
    public $phone;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'order_customer_guest_detail',
        'primary' => 'id_order_customer_guest_detail',
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_gender' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'firstname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 32, 'required' => true),
            'lastname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 32, 'required' => true),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 128, 'required' => true),
            'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 32, 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public static function isCustomerGuestBooking($id_order)
    {
        return Db::getInstance()->getValue('
            SELECT `id_order_customer_guest_detail`
            FROM `'._DB_PREFIX_.'order_customer_guest_detail`
            WHERE `id_order` = '.(int)$id_order
        );
    }
}