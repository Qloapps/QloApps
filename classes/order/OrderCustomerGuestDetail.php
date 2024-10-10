<?php
/**
* 2010-2022 Webkul.
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
*  @copyright 2010-2022 Webkul IN
*  @license   https://store.webkul.com/license.html
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
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_gender' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'firstname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 32),
            'lastname' => array('type' => self::TYPE_STRING, 'validate' => 'isName', 'size' => 32),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 128),
            'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 32),
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

    public function validateGuestInfo()
    {
        $isValid = true;
        if (!trim($this->firstname) || !Validate::isName($this->firstname)) {
            $isValid = false;
        }
        if (!trim($this->lastname) || !Validate::isName($this->lastname)) {
            $isValid = false;
        }
        if (!trim($this->email) || !Validate::isEmail($this->email)) {
            $isValid = false;
        }
        if (!trim($this->phone) || !Validate::isPhoneNumber($this->phone)) {
            $isValid = false;
        }
        return $isValid;
    }
}