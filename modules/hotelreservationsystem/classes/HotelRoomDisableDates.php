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

class HotelRoomDisableDates extends ObjectModel
{
    public $id;
    public $id_room_type;
    public $id_room;
    public $date_from;
    public $date_to;
    public $reason;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_room_disable_dates',
        'primary' => 'id',
        'fields' => array(
            'id_room_type' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_room' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'date_from' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'date_to' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'reason' =>        array('type' => self::TYPE_STRING),
            'date_add' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate')
        ),
    );

    public function getRoomDisableDates($id_room)
    {
        return Db::getInstance()->executeS('SELECT `date_from`, `date_to`, `reason` FROM `'._DB_PREFIX_.'htl_room_disable_dates` WHERE `id_room`='.(int)$id_room);
    }

    public function checkIfRoomAlreadyDisabled($params)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_room_disable_dates` WHERE `id_room` = '.(int)$params['id_room'].' AND `date_from` <= \''.pSQL($params['date_from']).'\' AND `date_to` >= \''.pSQL($params['date_to']).'\'');
    }

    public function updateDisableDateRanges($params)
    {
        if ($this->deleteDisabledDatesForDateRange($params)) {
            $roomDisableDates = new HotelRoomDisableDates();
            $roomDisableDates->id_room = $params['id_room'];
            $roomDisableDates->date_from = $params['date_from'];
            $roomDisableDates->date_to = $params['date_to'];
            $roomDisableDates->reason = $params['reason'];
            return $roomDisableDates->save();
        }
        return false;
    }

    public function deleteDisabledDatesForDateRange($params)
    {
        return Db::getInstance()->delete('htl_room_disable_dates', '`id_room` = '.(int)$params['id_room'].' AND `date_from` >= \''.pSQL($params['date_from']).'\' AND `date_to` <= \''.pSQL($params['date_to']).'\'');
    }

    public function deleteRoomDisableDates($id_room)
    {
        return Db::getInstance()->delete('htl_room_disable_dates', '`id_room`='.(int)$id_room);
    }


    public function validateDisableDateRange($disableDates)
    {
        $hotelResModInstance = Module::getInstanceByName('hotelreservationsystem');
        $wkDateErrors = array();
        if (count($disableDates)) {
            foreach ($disableDates as $disable_key => $disableDate) {
                if (!$disableDate['date_to'] && !$disableDate['date_from']) {
                    unset($disableDates[$disable_key]);
                } elseif (!Validate::isDate($disableDate['date_from']) || !Validate::isDate($disableDate['date_to'])) {
                    $wkDateErrors[] = $hotelResModInstance->l('Please fill valid date in disable date fields', 'HotelRoomDisableDates');
                } elseif (($disableDate['date_from'] && !$disableDate['date_to']) || (!$disableDate['date_from'] && $disableDate['date_to'])) {
                    $wkDateErrors[] = $hotelResModInstance->l('Please fill all date from and date to for disable dates fields.', 'HotelRoomDisableDates');
                } else {
                    foreach ($disableDates as $key => $disDate) {
                        if ($key != $disable_key) {
                            if ((($disableDate['date_from'] < $disDate['date_from']) && ($disableDate['date_to'] <= $disDate['date_from'])) || (($disableDate['date_from'] > $disDate['date_from']) && ($disableDate['date_from'] >= $disDate['date_to']))) {
                                // continue
                            } else {
                                $wkDateErrors[] = $hotelResModInstance->l('Some date are conflicting with each other. Please check and reselect the date ranges.', 'HotelRoomDisableDates');
                            }
                        }
                    }
                }
            }
        }

        if (!count($disableDates)) {
            $wkDateErrors[] = $hotelResModInstance->l('Please enter disable dates for status temporary disable.', 'HotelRoomDisableDates');
        }

        return $wkDateErrors;
    }

    public function deleteRoomDisableDatesByIdRoomType($idRoomType)
    {
        if (!$idRoomType) {
            return false;
        }

        return Db::getInstance()->delete('htl_room_disable_dates', '`id_room_type`='.(int)$idRoomType);
    }

    public function checkIfRoomAlreadyDisabledInDateRange($params)
    {
        return Db::getInstance()->getValue('
            SELECT `id` FROM `'._DB_PREFIX_.'htl_room_disable_dates`
            WHERE `id_room` = '.(int)$params['id_room'].'
            AND (
                `date_from` BETWEEN  \''.pSQL($params['date_from']).'\' AND \''.pSQL($params['date_to']).'\'
                OR `date_to` BETWEEN  \''.pSQL($params['date_from']).'\' AND \''.pSQL($params['date_to']).'\'
            )'
        );
    }

}
