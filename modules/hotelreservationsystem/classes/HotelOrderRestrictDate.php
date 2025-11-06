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
    public $use_global_max_checkout_offset;
    public $max_checkout_offset;
    public $use_global_min_booking_offset;
    public $min_booking_offset;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_order_restrict_date',
        'primary' => 'id',
        'fields' => array(
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'use_global_max_checkout_offset' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'max_checkout_offset' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'use_global_min_booking_offset' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'min_booking_offset' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * @param int $id_hotel
     * @return array hote wise restriction.
     */
    public static function getDataByHotelId($idHotel)
    {
        $cache_key = 'HotelOrderRestrictDate::getDataByHotelId'.(int)$idHotel;
        if (!Cache::isStored($cache_key)) {
            $res = Db::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'htl_order_restrict_date` ord WHERE ord.`id_hotel` = '.(int) $idHotel
            );
            Cache::store($cache_key, $res);
        } else {
            $res = Cache::retrieve($cache_key);
        }

        return $res;
    }

    /**
     * @param int $id_hotel
     * @return string Maximum checkout date according to the max booking offset for the hotel.
     */
    public static function getMaxOrderDate($idHotel)
    {
        $result = self::getDataByHotelId($idHotel);
        if (is_array($result) && count($result) && !$result['use_global_max_checkout_offset']) {
            return date('Y-m-d H:i:s', strtotime('+ '.$result['max_checkout_offset'].' days'));
        }

        // since this cannot be zero, this function will always return a date.
        $globalBookingDate = (int) Configuration::get('PS_MAX_CHECKOUT_OFFSET');

        return date('Y-m-d H:i:s', strtotime('+ '.$globalBookingDate.' days'));
    }

    /**
     * @param int $id_hotel
     * @return int Maximum allowable number of days between booking date and checkout date.
     */
    public static function getMaximumCheckoutOffset($idHotel)
    {
        $result = self::getDataByHotelId($idHotel);
        if (is_array($result) && count($result) && !$result['use_global_max_checkout_offset']) {
            return $result['max_checkout_offset'];
        }

        return (int) Configuration::get('PS_MAX_CHECKOUT_OFFSET');
    }

    /**
     * @param int $id_hotel
     * @return int Minimum number of days required between booking and check-in.
     */
    public static function getMinimumBookingOffset($idHotel)
    {
        $result = self::getDataByHotelId($idHotel);
        if (is_array($result) && count($result) && !$result['use_global_min_booking_offset']) {
            return (int) $result['min_booking_offset'];
        }

        return (int) Configuration::get('PS_MIN_BOOKING_OFFSET');
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
