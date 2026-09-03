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

/**
 * Audit log: one row per room-booking status change (htl_booking_detail.id_status).
 */
class HotelBookingStatusHistory extends ObjectModel
{
    public $id_htl_booking;
    public $id_status_from;
    public $id_status_to;
    public $id_employee;
    public $id_customer;
    public $remark;
    public $date_add;

    public static $definition = array(
        'table' => 'htl_booking_status_history',
        'primary' => 'id_booking_status_history',
        'fields' => array(
            'id_htl_booking' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_status_from' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'allow_null' => true),
            'id_status_to' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_employee' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'allow_null' => true),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'allow_null' => true),
            'remark' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function getBookingStatusHistoryByOrder($idOrder)
    {
        $sql = 'SELECT h.*, hbd.room_num, hbd.id_product, hbd.date_from, hbd.date_to,
                    e.`firstname` AS efirstname, e.`lastname` AS elastname,
                    c.`firstname` AS cfirstname, c.`lastname` AS clastname
                FROM `'._DB_PREFIX_.$this->table.'` h
                INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON hbd.`id` = h.`id_htl_booking`
                LEFT OUTER JOIN `'._DB_PREFIX_.'employee` e ON e.`id_employee` = h.`id_employee`
                LEFT JOIN `'._DB_PREFIX_.'customer` c ON c.`id_customer` = h.`id_customer`
                WHERE hbd.`id_order` = '.(int) $idOrder.'
                ORDER BY h.`id_booking_status_history` ASC';

        return Db::getInstance()->executeS($sql);
    }
}
