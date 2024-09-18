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

class HotelOrderRestrictDate extends ObjectModel
{
    public $id;
    public $id_hotel;
    public $use_global_max_order_date;
    public $max_order_date;
    public $use_global_preparation_time;
    public $preparation_time;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_order_restrict_date',
        'primary' => 'id',
        'fields' => array(
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'use_global_max_order_date' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'max_order_date' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'use_global_preparation_time' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'preparation_time' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public static function getDataByHotelId($idHotel)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'htl_order_restrict_date` ord WHERE ord.`id_hotel` = '.(int) $idHotel
        );
    }

    /*Max date of ordering for order restrict*/
    public static function getMaxOrderDate($idHotel)
    {
        $result = self::getDataByHotelId($idHotel);
        if (is_array($result) && count($result) && !$result['use_global_max_order_date']) {
            return $result['max_order_date'];
        }

        if ($globalBookingDate = Configuration::get('MAX_GLOBAL_BOOKING_DATE')) {
            return $globalBookingDate;
        }

        return 0;
    }

    /**
     * @param int $id_hotel
     * @return int|false
     */
    public static function getPreparationTime($idHotel)
    {
        $result = self::getDataByHotelId($idHotel);
        if (is_array($result) && count($result) && !$result['use_global_preparation_time']) {
            return (int) $result['preparation_time'];
        }

        $globalPreparationTime = Configuration::get('GLOBAL_PREPARATION_TIME');
        if ($globalPreparationTime != '0') {
            return (int) $globalPreparationTime;
        }

        return false;
    }

    public static function validateOrderRestrictDateOnPayment(&$controller)
    {
        if ($errors = HotelCartBookingData::validateCartBookings()) {
            $controller->errors = array_merge($controller->errors, $errors);

            return true;
        }

        return false;
    }
}
