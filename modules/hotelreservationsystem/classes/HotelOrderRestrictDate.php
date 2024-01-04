<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
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
        Tools::displayAsDeprecated();

        return HotelCartBookingData::validateRoomTypeAvailabilities($controller);
    }
}
