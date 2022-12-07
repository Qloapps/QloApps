<?php
/**
* 2010-2021 Webkul.
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
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class HotelRoomTypeRestrictionDateRange extends ObjectModel
{
    public $id_rt_restriction;
    public $id_product;
    public $min_los;
    public $max_los;
    public $date_from;
    public $date_to;
    public $date_add;
    public $date_upd;

    const WK_ROOM_TYPE_MIN_LOS = 1;
    const WK_ROOM_TYPE_MAX_LOS = 2;

    public static $definition = array(
        'table' => 'htl_room_type_restriction_date_range',
        'primary' => 'id_rt_restriction',
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'min_los' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true, 'default' => 1),
            'max_los' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true, 'default' => 0),
            'date_from' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'date_to' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate')
        ),
    );

    protected $webserviceParameters = array(
        'objectMethods' => array(
            'add' => 'addWs',
            'update' => 'updateWs',
            'delete' => 'deleteWs',
        ),
        'objectsNodeName' => 'restriction_date_ranges',
        'objectNodeName' => 'restriction_date_range',
        'fields' => array(
            'id_product' => array(
                'xlink_resource' => array(
                    'resourceName' => 'room_types',
                )
            ),
        ),
    );

    public function getRoomTypeLengthOfStayRestriction($idRoomType)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'htl_room_type_restriction_date_range` WHERE `id_product` = '.(int)$idRoomType;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function getRoomTypeLengthOfStay($idRoomType, $date = false, $losRestrictionType = false)
    {
        $losRestriction = array();

        // if date is given the return LOS for date range
        if ($date) {
            $date = date('Y-m-d', strtotime($date));

            $sql = 'SELECT `min_los`, `max_los` 
                    FROM `'._DB_PREFIX_.'htl_room_type_restriction_date_range` 
                    WHERE `id_product` = '.(int) $idRoomType.' AND `date_from` <= \''.pSQL($date).'\' AND `date_to` > \''.pSQL($date).'\'';

            $losRestriction = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        }

        // if no LOS for date range the find it for room type
        if (!$losRestriction) {
            $sql = 'SELECT `min_los`, `max_los` 
                    FROM `'._DB_PREFIX_.'htl_room_type` 
                    WHERE `id_product` = '.(int) $idRoomType;

            $losRestriction = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        }

        if ($losRestriction) {
            if ($losRestrictionType) {
                if ($losRestrictionType == self::WK_ROOM_TYPE_MIN_LOS) {
                    return $losRestriction['min_los'];
                } elseif ($losRestrictionType == self::WK_ROOM_TYPE_MAX_LOS) {
                    return $losRestriction['max_los'];
                }
            }

            return $losRestriction;
        }

        return false;
    }

    public function validateRoomTypeLengthOfStayRestriction($dateFromRestriction, $dateToRestriction, $minLosDays, $maxLosDays)
    {
        $errors = array();
        $objModule = Module::getinstanceByName('hotelreservationsystem');
        foreach ($dateFromRestriction as $dateFromKey => $dateFrom) {
            $dateTo = $dateToRestriction[$dateFromKey];
            $minDay = $minLosDays[$dateFromKey];
            $maxDay = $maxLosDays[$dateFromKey];

            // validate the fields before saving in the table
            if (!$minDay) {
                $errors[] = $objModule->l('Minimum length of stay is required.', 'HotelRoomTypeLosDateRanges').
                ' [ '.$objModule->l('LOS for date range in row no.', 'HotelRoomTypeLosDateRanges').' : '.($dateFromKey+1).' ]';
            } elseif (!Validate::isUnsignedInt($minDay)) {
                $errors[] = $objModule->l('Minimum length of stay is invalid.', 'HotelRoomTypeLosDateRanges').'
                [ '.$objModule->l('LOS for date range in row no.', 'HotelRoomTypeLosDateRanges').' : '.($dateFromKey+1).' ]';
            }

            if ($maxDay == null) {
                $errors[] = $objModule->l('Maximum length of stay is required.', 'HotelRoomTypeLosDateRanges').
                ' [ '.$objModule->l('LOS for date range in row no.', 'HotelRoomTypeLosDateRanges').' : '.($dateFromKey+1).' ]';
            } elseif (!Validate::isUnsignedInt($maxDay)) {
                $errors[] = $objModule->l('Maximum length of stay is invalid.', 'HotelRoomTypeLosDateRanges').
                ' [ '.$objModule->l('LOS for date range in row no.', 'HotelRoomTypeLosDateRanges').' : '.($dateFromKey+1).' ]';
            } elseif ($minDay && $maxDay > 0 && ($minDay > $maxDay)) {
                $this->errors[] = Tools::displayError('Value of global maximum length of stay must be greater than global minimum length of stay.');
            }

            if (!$dateFrom || $dateFrom == '') {
                $errors[] = $objModule->l('Date from is required.', 'HotelRoomTypeLosDateRanges').
                ' [ '.$objModule->l('LOS for date range in row no.', 'HotelRoomTypeLosDateRanges').' : '.($dateFromKey+1).' ]';
            } else {
                $dateFrom = date('Y-m-d', strtotime($dateFrom));
                if (!Validate::isDate($dateFrom)) {
                    $errors[] = $objModule->l('Date from is invalid.', 'HotelRoomTypeLosDateRanges').
                    ' [ '.$objModule->l('LOS for date range in row no.', 'HotelRoomTypeLosDateRanges').' : '.($dateFromKey+1).' ]';
                }
            }

            if (!$dateTo || $dateTo == '') {
                $errors[] = $objModule->l('Date to is required.', 'HotelRoomTypeLosDateRanges').'
                [ '.$objModule->l('LOS for date range in row no.', 'HotelRoomTypeLosDateRanges').' : '.($dateFromKey+1).' ]';
            } else {
                $dateTo = date('Y-m-d', strtotime($dateTo));
                if (!Validate::isDate($dateTo)) {
                    $errors[] = $objModule->l('Date to is invalid.', 'HotelRoomTypeLosDateRanges').
                    ' [ '.$objModule->l('LOS for date range in row no.', 'HotelRoomTypeLosDateRanges').' : '.($dateFromKey+1).' ]';
                } elseif (strtotime($dateTo) <= strtotime($dateFrom)) {
                    $errors[] = $dateFrom.' and '.$dateTo . $objModule->l('\'Date to\' must be a greater than \'date from\'.', 'HotelRoomTypeLosDateRanges').
                    ' [ '.$objModule->l('LOS for date range in row no.', 'HotelRoomTypeLosDateRanges').' : '.($dateFromKey+1).' ]';
                } else {
                    // validate date ranges  from other date ranges
                    foreach ($dateToRestriction as $dateToKey => $newDateTo) {
                        if ($dateFromKey > $dateToKey) {
                            if (!((strtotime($dateFrom) < strtotime($dateFromRestriction[$dateToKey]) && strtotime($dateTo) <= strtotime($dateFromRestriction[$dateToKey])) || (strtotime($dateFrom) >= strtotime($newDateTo) && strtotime($dateTo) > strtotime($newDateTo)))) {
                                $errors[] = $objModule->l('Date ranges are conflicting with each other.', 'HotelRoomTypeLosDateRanges').' [ '.$objModule->l('Conflicting rows are', 'HotelRoomTypeLosDateRanges').' : '.($dateFromKey+1).', '.($dateToKey+1).' ]';
                            }
                        }
                    }
                }
            }
        }

        return $errors;
    }

    // Webservice :: webservice add function
    public function addWs($autodate = true, $null_values = false)
    {
        $objRoomType = new HotelRoomType();
        if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($this->id_product)) {
            return $this->add($autodate, $null_values);
        }
        return false;
    }

    // Webservice :: webservice update function
    public function updateWs($null_values = false)
    {
        $objRoomType = new HotelRoomType();
        if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($this->id_product)) {
            return $this->update($null_values);
        }
        return false;
    }

    // Webservice :: webservice delete function
    public function deleteWs()
    {
        return $this->delete();
    }
}
