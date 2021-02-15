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

class HotelRoomTypeFeaturePricing extends ObjectModel
{
    public $id_product;
    public $feature_price_name;
    public $date_selection_type;
    public $date_from;
    public $date_to;
    public $is_special_days_exists;
    public $special_days;
    public $impact_way;
    public $impact_type;
    public $impact_value;
    public $active;
    public $date_add;
    public $date_upd;

    public $groupBox;

    public static $definition = array(
        'table' => 'htl_room_type_feature_pricing',
        'primary' => 'id_feature_price',
        'multilang' => true,
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'feature_price_name' => array('type' => self::TYPE_STRING),
            'date_from' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_to' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'impact_way' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'is_special_days_exists' => array('type' => self::TYPE_INT),
            'date_selection_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'special_days' => array('type' => self::TYPE_STRING),
            'impact_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'impact_value' => array('type' => self::TYPE_FLOAT),
            'active' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            //lang fields
            'feature_price_name' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isCatalogName',
                'required' => true,
                'size' => 128
            ),
    ));

    protected $webserviceParameters = array(
        'objectsNodeName' => 'feature_prices',
        'objectNodeName' => 'feature_price',
        'fields' => array(
            'id_product' => array(
                'xlink_resource' => array(
                    'resourceName' => 'room_types',
                )
            ),
        ),
        'associations' => array(
            'groups' => array('resource' => 'group'),
        )
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->moduleInstance = Module::getInstanceByName('hotelreservationsystem');
        parent::__construct($id, $id_lang, $id_shop);
    }

    public function add($autodate = true, $null_values = true)
    {
        $return = parent::add($autodate, $null_values);

        // call to add/update all the group entries
        $this->updateGroup($this->groupBox);

        return $return;
    }

    public function update($nullValues = false)
    {
        // first call to add/update all the group entries
        $this->updateGroup($this->groupBox);
        return parent::update($nullValues);
    }

    public function delete()
    {
        // first call to delete all the group entries
        $this->cleanGroups();
        return parent::delete();
    }

    public function getFeaturePriceInfo($idFeaturePrice)
    {
        if (!$idFeaturePrice) {
            return false;
        }
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing` WHERE `id_feature_price`='.
            (int) $idFeaturePrice
        );
    }

    /**
     * [getRoomTypeActiveFeaturePrices returns room type active feature price plans]
     * @param  [int] $id_product [id of the product]
     * @return [array|false]     [returns array of all active feature plans of the room type if found else returns false]
     */
    public static function getRoomTypeActiveFeaturePrices($id_product)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing`
            WHERE `id_product`='.(int) $id_product.' AND `active`=1'
        );
    }

    /**
     * [getRoomTypeActiveFeaturePricesByDateRange returns room type active feature price plans by supplied date Range]
     * @param  [int] $id_product [id of the product]
     * @param  [date] $date_from  [start date of the date range]
     * @param  [date] $date_to    [end date of the date range]
     * @return [array|false]      [returns array of all active feature plans of the room type if found else returns false]
     */
    public static function getRoomTypeActiveFeaturePricesByDateRange($id_product, $date_from, $date_to)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing`
            WHERE (`id_product`=0 OR `id_product`='.(int) $id_product.')
            AND `active`=1 AND `date_from` <= \''.$date_to.'\' AND `date_to` >= \''.$date_from.'\''
        );
    }

    /**
     * [checkRoomTypeFeaturePriceExistance returns room type active feature price plan by supplied date Range and supplied feature price plan type else returns false]
     * @param  [int] $id_product [id of the product]
     * @param  [date] $date_from  [start date of the date range]
     * @param  [date] $date_to    [end date of the date range]
     * @param  [type] $type       [Type of the feature price plan must be among 'specific_date', 'special_day' and 'date_range']
     * @return [array|false]      [returns room type active feature price plan by supplied date Range and supplied feature price plan type else returns false]
     */
    public function checkRoomTypeFeaturePriceExistance(
        $id_product,
        $date_from,
        $date_to,
        $type = 'date_range',
        $current_Special_days = false,
        $id_feature_price = 0
    ) {
        $date_from = date('Y-m-d', strtotime($date_from));
        $date_to = date('Y-m-d', strtotime($date_to));
        if ($type == 'specific_date') {
            return Db::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing`
                WHERE `id_product`='.(int) $id_product.'
                AND `date_selection_type`=2
                AND `date_from` = \''.pSQL($date_from).'\'
                AND `id_feature_price`!='.(int) $id_feature_price
            );
        } elseif ($type == 'special_day') {
            $featurePrice = Db::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing`
                WHERE `id_product`='.(int) $id_product.'
                AND `is_special_days_exists`=1 AND `active`=1
                AND `date_from` < \''.pSQL($date_to).'\'
                AND `date_to` > \''.pSQL($date_from).'\'
                AND `id_feature_price`!='.(int) $id_feature_price
            );
            if ($featurePrice) {
                $specialDays = json_decode($featurePrice['special_days']);
                $currentSpecialDays = json_decode($current_Special_days);
                $commonValues = array_intersect($specialDays, $currentSpecialDays);
                if ($commonValues) {
                    return $featurePrice;
                }
            }
            return false;
        } elseif ($type == 'date_range') {
            return Db::getInstance()->getRow(
                'SELECT * FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing`
                WHERE `id_product`='.(int) $id_product.'
                AND `date_selection_type`=1
                AND `is_special_days_exists`=0
                AND `date_from` <= \''.pSQL($date_to).'\'
                AND `date_to` >= \''.pSQL($date_from).'\'
                AND `id_feature_price`!='.(int) $id_feature_price
            );
        }
        return false;
    }

    /**
     * [countFeaturePriceSpecialDays returns number of special days between a date range]
     * @param  [array] $specialDays [array containing special days to be counted]
     * @param  [date] $date_from   [start date of the date range]
     * @param  [date] $date_to     [end date of the date range]
     * @return [int]              [number of special days]
     */
    public static function countFeaturePriceSpecialDays($specialDays, $date_from, $date_to)
    {
        $specialDaysCount = 0;
        $date_from = date('Y-m-d', strtotime($date_from));
        $date_to = date('Y-m-d', strtotime($date_to));

        for($date = $date_from; $date < $date_to; $date = date('Y-m-d', strtotime('+1 day', strtotime($date)))) {
            if (in_array(Tools::strtolower(Date('D', $date)), $specialDays)) {
                $specialDaysCount++;
            }
        }
        return $specialDaysCount;
    }

    /**
     * [getHotelRoomTypesRatesByDate returns hotel room types rates accrding to feature price plans]
     * @param  [int]  $id_hotel   [id of th hotel]
     * @param  integer $id_product [id of the product if supplied only rates of this room type will be returned]
     * @param  [date]  $date_from  [start date of the date range]
     * @param  [date]  $date_to    [end date of the date range]
     * @return [array|false]       [returns array containing rates of room type of a hotel if found else returns false]
     */
    public function getHotelRoomTypesRatesAndInventoryByDate($id_hotel, $id_product=0, $date_from, $date_to)
    {
        $hotelRoomType = new HotelRoomType();
        $context = Context::getContext();
        $roomTypeRatesAndInventory = array();
        $hotelCartBookingData = new HotelCartBookingData();
        $objBookingDetail = new HotelBookingDetail();
        $incr = 0;
        $date_from = date('Y-m-d', strtotime($date_from));
        $date_to = date('Y-m-d', strtotime($date_to));
        for($date = $date_from; $date < $date_to; $date = date('Y-m-d', strtotime('+1 day', strtotime($date)))) {
            $currentDate = date('Y-m-d', $date);
            $nextDayDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));
            if ($id_product) {
                $roomTypeAvailabilityInfo = $objBookingDetail->DataForFrontSearch(
                    $currentDate,
                    $nextDayDate,
                    $id_hotel,
                    $id_product,
                    1,
                    0,
                    0,
                    -1,
                    0,
                    0
                );
                if (isset($roomTypeAvailabilityInfo['stats']['num_avail'])) {
                    $totalAvailableRooms = $roomTypeAvailabilityInfo['stats']['num_avail'];
                } else {
                    $totalAvailableRooms = 0;
                }

                $roomTypePrice = $hotelCartBookingData->getRoomTypeTotalPrice($id_product, $currentDate, $nextDayDate);
                $roomTypeRatesAndInventory[$incr]['date'] = $currentDate;
                $roomTypeRatesAndInventory[$incr]['room_types'][0]['id'] = $id_product;
                $roomTypeRatesAndInventory[$incr]['room_types'][0]['rates'] = $roomTypePrice;
                $roomTypeRatesAndInventory[$incr]['room_types'][0]['available_rooms'] = $totalAvailableRooms;
            } else {
                $hotelRoomTypes = $hotelRoomType->getRoomTypeByHotelId($id_hotel, $context->language->id);
                if ($hotelRoomTypes) {
                    $roomTypeRatesAndInventory[$incr]['date'] = $currentDate;
                    foreach ($hotelRoomTypes as $key => $product) {
                        $roomTypePrice = $hotelCartBookingData->getRoomTypeTotalPrice(
                            $product['id_product'],
                            $currentDate,
                            $nextDayDate
                        );
                        $roomTypeAvailabilityInfo = $objBookingDetail->DataForFrontSearch(
                            $currentDate,
                            $nextDayDate,
                            $id_hotel,
                            $product['id_product'],
                            1,
                            0,
                            0,
                            -1,
                            0,
                            0
                        );
                        if (isset($roomTypeAvailabilityInfo['stats']['num_avail'])) {
                            $totalAvailableRooms = $roomTypeAvailabilityInfo['stats']['num_avail'];
                        } else {
                            $totalAvailableRooms = 0;
                        }
                        $roomTypeRatesAndInventory[$incr]['room_types'][$key]['id'] = $product['id_product'];
                        $roomTypeRatesAndInventory[$incr]['room_types'][$key]['rates'] = $roomTypePrice;
                        $roomTypeRatesAndInventory[$incr]['room_types'][$key]['available_rooms'] = $totalAvailableRooms;
                    }
                } else {
                    return false;
                }
            }
            $incr++;
        }
        return $roomTypeRatesAndInventory;
    }

    /**
     * [updateRoomTypesFeaturePrices update and creates feature price plans by supplied information]
     * @param  [array] $featurePricePlans [feature price plans sent from channel manager]
     * @return [array]        [success if process is finished successfully else fasiled with errors]
     * @information [if any feature price plan for the same date_from and date_to(supplied in the $featurePricePlans array) it will be updated otherwise it is added. While adding date range type rate plans if any plan already exist then feature price for all specific dates in the date range will be created (or updated if specific date feature price plan exists)]
     */
    public function updateRoomTypesFeaturePricesAvailability($featurePricePlans)
    {
        $this->errors = array();
        if ($featurePricePlans) {
            if (isset($featurePricePlans['data']) && $featurePricePlans['data']) {
                foreach ($featurePricePlans['data'] as $roomTypeRatesData) {
                    $dateFrom = date('Y-m-d', strtotime($roomTypeRatesData['dateFrom']));
                    $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($roomTypeRatesData['dateTo'])));
                    if ($roomTypeRatesData['roomType']) {
                        foreach ($roomTypeRatesData['roomType'] as $key => $roomTypeRates) {
                            $id_product = $key;
                            // feature price rates create and updates
                            if (isset($roomTypeRates['rate'])) {
                                //$productPriceTE = Product::getPriceStatic((int) $id_product, false);
                                $productPriceTE = 1000;
                                if ($productPriceTE != $roomTypeRates['rate']) {
                                    if ($productPriceTE > $roomTypeRates['rate']) {
                                        $priceImpactWay = 1;
                                        $impactValue = $productPriceTE - $roomTypeRates['rate'];
                                    } else {
                                        $priceImpactWay = 2;
                                        $impactValue = $roomTypeRates['rate'] - $productPriceTE;
                                    }
                                    $params = array();
                                    $params['roomTypeId'] = $id_product;
                                    $params['featurePriceName'] = 'Webservice Feature Price';
                                    $params['dateFrom'] = $dateFrom;
                                    $params['dateTo'] = $dateTo;
                                    $params['priceImpactWay'] = $priceImpactWay;
                                    $params['isSpecialDaysExists'] = 0;
                                    $params['jsonSpecialDays'] = null;
                                    $params['priceImpactType'] = 2;
                                    $params['impactValue'] = $impactValue;
                                    $params['enableFeaturePrice'] = 1;
                                    $nextDate = date('Y-m-d', strtotime('+1 day', strtotime($dateFrom)));
                                    if ($nextDate == $dateTo) {
                                        $params['dateSelectionType'] = 2;
                                        $featurePriceExists = $this->checkRoomTypeFeaturePriceExistance(
                                            $id_product,
                                            $dateFrom,
                                            $dateTo,
                                            'specific_date'
                                        );
                                        if ($featurePriceExists) {
                                            if (!$this->saveFeaturePricePlan($featurePriceExists['id'], 2, $params)) {
                                                $this->errors[] = $this->moduleInstance->l('Some error occured while saving Feature Price Plan Info:: Date From : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Date To : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Room Type Id : ', 'HotelRoomTypeFeaturePricing').$params['roomTypeId'];
                                            }
                                        } else {
                                            if (!$this->saveFeaturePricePlan(0, 2, $params)) {
                                                $this->errors[] = $this->moduleInstance->l('Some error occured while saving Feature Price Plan Info:: Date From : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Date To : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Room Type Id : ', 'HotelRoomTypeFeaturePricing').$params['roomTypeId'];
                                            }
                                        }
                                    } else {
                                        $params['dateSelectionType'] = 1;
                                        $featurePriceExists = $this->checkRoomTypeFeaturePriceExistance(
                                            $id_product,
                                            $dateFrom,
                                            $dateTo,
                                            'date_range'
                                        );
                                        if ($featurePriceExists) {
                                            if ($featurePriceExists['date_from'] == $dateFrom
                                                && $featurePriceExists['date_to'] == $dateTo
                                            ) {
                                                if (!$this->saveFeaturePricePlan(
                                                    $featurePriceExists['id'],
                                                    1,
                                                    $params
                                                )) {
                                                    $this->errors[] = $this->moduleInstance->l('Some error occured while saving Feature Price Plan Info:: Date From : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Date To : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Room Type Id : ', 'HotelRoomTypeFeaturePricing').$params['roomTypeId'];
                                                }
                                            } else {
                                                for($date = $dateFrom; $date < $dateTo; $date = date('Y-m-d', strtotime('+1 day', strtotime($date)))) {
                                                    $currentDate = date('Y-m-d', $date);
                                                    $nextDayDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));
                                                    $params['dateFrom'] = $currentDate;
                                                    $params['dateTo'] = $nextDayDate;
                                                    $params['dateSelectionType'] = 2;
                                                    $featurePriceExists = $this->checkRoomTypeFeaturePriceExistance(
                                                        $id_product,
                                                        $currentDate,
                                                        $nextDayDate,
                                                        'specific_date'
                                                    );
                                                    if ($featurePriceExists) {
                                                        if (!$this->saveFeaturePricePlan(
                                                            $featurePriceExists['id'],
                                                            2,
                                                            $params
                                                        )) {
                                                            $this->errors[] = $this->moduleInstance->l('Some error occured while saving Feature Price Plan Info:: Date From : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Date To : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Room Type Id : ', 'HotelRoomTypeFeaturePricing').$params['roomTypeId'];
                                                        }
                                                    } else {
                                                        if (!$this->saveFeaturePricePlan(0, 2, $params)) {
                                                            $this->errors[] = $this->moduleInstance->l('Some error occured while saving Feature Price Plan Info:: Date From : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Date To : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Room Type Id : ', 'HotelRoomTypeFeaturePricing').$params['roomTypeId'];
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            if (!$this->saveFeaturePricePlan(0, 1, $params)) {
                                                $this->errors[] = $this->moduleInstance->l('Some error occured while saving Feature Price Plan Info:: Date From : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Date To : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Room Type Id : ', 'HotelRoomTypeFeaturePricing').$params['roomTypeId'];
                                            }
                                        }
                                    }
                                }
                            }

                            if (isset($roomTypeRates['inventory'])) {
                                $totalAvailableNotBooked = 0;
                                $totalAvailableRooms = 0;
                                $hotelRoomType = new HotelRoomType();
                                $hotelBookingDetail = new HotelBookingDetail();
                                $hotelRoomInformation = new HotelRoomInformation();
                                $roomTypeInfo = $hotelRoomType->getRoomTypeInfoByIdProduct($id_product);
                                $id_hotel = $roomTypeInfo['id_hotel'];
                                if ($id_hotel) {
                                    $roomTypeAvailabilityInfo = $hotelBookingDetail->DataForFrontSearch(
                                        $dateFrom,
                                        $dateTo,
                                        $id_hotel,
                                        $id_product,
                                        1,
                                        0,
                                        0,
                                        -1,
                                        0,
                                        0
                                    );
                                    $bookedRoomsInfo = $hotelRoomInformation->getRoomTypeBookedRoomsForDateRange(
                                        $id_hotel,
                                        $id_product,
                                        $dateFrom,
                                        $dateTo
                                    );
                                    $countBookedRooms = count($bookedRoomsInfo);
                                    if (isset($roomTypeAvailabilityInfo['stats']['total_rooms'])) {
                                        $totalAvailableNotBooked = $roomTypeAvailabilityInfo['stats']['total_rooms'] - $countBookedRooms;
                                        $totalAvailableRooms = $roomTypeAvailabilityInfo['stats']['num_avail'];
                                    }

                                    if ($roomTypeRates['inventory'] <= $totalAvailableNotBooked) {
                                        if ($roomTypeRates['inventory'] < $totalAvailableRooms) {
                                            $numDisabledRooms = $totalAvailableRooms - $roomTypeRates['inventory'];
                                            $availableRooms = $hotelRoomInformation->getRoomTypeAvailableRoomsForDateRange(
                                                $id_hotel,
                                                $id_product,
                                                $dateFrom,
                                                $dateTo
                                            );
                                            if ($availableRooms) {
                                                foreach ($availableRooms as $room) {
                                                    $objRoomDisableDates = new HotelRoomDisableDates();
                                                    $params['id_room'] = $room['id'];
                                                    $params['date_from'] = $dateFrom;
                                                    $params['date_to'] = $dateTo;
                                                    if (!($objRoomDisableDates->checkIfRoomAlreadyDisabled($params))) {
                                                        if ($numDisabledRooms > 0) {
                                                            $hotelRoomInformation = new HotelRoomInformation($room['id']);
                                                            if ($hotelRoomInformation->id_status == 3) {
                                                                $params['reason'] = $this->moduleInstance->l('Disabled from channel manager.', 'HotelRoomTypeFeaturePricing');
                                                                if (!$objRoomDisableDates->updateDisableDateRanges(
                                                                    $params
                                                                )) {
                                                                    $this->errors[] = $this->moduleInstance->l('Some error occurred while saving disable dates for '.$dateFrom.' To '.$dateTo.' for room id-'.$room['id'], 'HotelRoomTypeFeaturePricing');
                                                                }
                                                            } else {
                                                                $hotelRoomInformation->id_status = 3;
                                                                if ($hotelRoomInformation->save()) {
                                                                    $objRoomDisableDates = new HotelRoomDisableDates();
                                                                    $objRoomDisableDates->id_room = $room['id'];
                                                                    $objRoomDisableDates->date_from = $dateFrom;
                                                                    $objRoomDisableDates->date_to = $dateTo;
                                                                    $objRoomDisableDates->reason = $this->moduleInstance->l('Disabled from channel manager.', 'HotelRoomTypeFeaturePricing');
                                                                    $objRoomDisableDates->save();
                                                                }
                                                            }
                                                            $numDisabledRooms--;
                                                        }
                                                    }
                                                }
                                            }
                                        } elseif ($roomTypeRates['inventory'] > $totalAvailableRooms) {
                                            $roomsToEnable = $roomTypeRates['inventory'] - $totalAvailableRooms;
                                            $disabledRooms = $hotelRoomInformation->getRoomTypeDisabledRoomsForDateRange(
                                                $id_hotel,
                                                $id_product,
                                                $dateFrom,
                                                $dateTo
                                            );
                                            if ($disabledRooms) {
                                                foreach ($disabledRooms as $disableRoom) {
                                                    if ($roomsToEnable > 0) {
                                                        $hotelRoomInformation = new HotelRoomInformation($disableRoom['id']);
                                                        $objRoomDisableDates = new HotelRoomDisableDates();
                                                        $params['id_room'] = $disableRoom['id'];
                                                        $params['date_from'] = $dateFrom;
                                                        $params['date_to'] = $dateTo;
                                                        if (!$objRoomDisableDates->deleteDisabledDatesForDateRange(
                                                            $params
                                                        )) {
                                                            $this->errors[] = $this->moduleInstance->l('Some error occurred while saving deleting dates for '.$dateFrom.' To '.$dateTo.' for room id-'.$disableRoom['id'], 'HotelRoomTypeFeaturePricing');
                                                        }
                                                        $disabledDates = $objRoomDisableDates->getRoomDisableDates(
                                                            $disableRoom['id']
                                                        );
                                                        if (!count($disabledDates)) {
                                                            $hotelRoomInformation->id_status = 1;
                                                            $hotelRoomInformation->disabled_dates = null;
                                                        }
                                                        $hotelRoomInformation->save();
                                                    }
                                                    $roomsToEnable--;
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                $this->errors[] = $this->moduleInstance->l(
                                    'Requested rooms inventory is not available.',
                                    'HotelRoomTypeFeaturePricing'
                                );
                            }
                        }
                    } else {
                        $this->errors[] = $this->moduleInstance->l(
                            'Room Types for which Feature prices to be updated are not found.',
                            'HotelRoomTypeFeaturePricing'
                        );
                    }
                }
            } else {
                $this->errors[] = $this->moduleInstance->l(
                    'Update Information not found.',
                    'HotelRoomTypeFeaturePricing'
                );
            }
        } else {
            $this->errors[] = $this->moduleInstance->l(
                'Update Information not found.',
                'HotelRoomTypeFeaturePricing'
            );
        }

        $result = array();
        if (count($this->errors)) {
            $result['status'] = 'failed';
            $result['errors'] = $this->errors;
        } else {
            $result['status'] = 'success';
        }

        return $result;
    }

    /**
     * [saveFeaturePricePlan add or update feature price plan]
     * @param  integer $id                [id of the feature price plan if 0 means to add else to update the feature price plan]
     * @param  [int]  $dateSelectionType [date selection type 1 or 2 (date range or specific date)]
     * @param  [array]  $params            [Room type rate plan info]
     * @return [bool]                     [returns true is successfuly added or updated else returns false]
     */
    public function saveFeaturePricePlan($id = 0, $dateSelectionType, $params)
    {
        if ($id) {
            $roomTypeFeaturePricing = new HotelRoomTypeFeaturePricing($id);
        } else {
            $roomTypeFeaturePricing = new HotelRoomTypeFeaturePricing();
        }
        $roomTypeFeaturePricing->id_product = $params['roomTypeId'];
        // lang fields
        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            $roomTypeFeaturePricing->feature_price_name[$language['id_lang']] = $params['featurePriceName'];
        }
        $roomTypeFeaturePricing->date_selection_type = $params['dateSelectionType'];
        if ($dateSelectionType == 1) {
            $roomTypeFeaturePricing->date_from = $params['dateFrom'];
            $roomTypeFeaturePricing->date_to = $params['dateTo'];
        } else {
            $roomTypeFeaturePricing->date_from = $params['dateFrom'];
            $roomTypeFeaturePricing->date_to = $params['dateTo'];
        }
        $roomTypeFeaturePricing->impact_way = $params['priceImpactWay'];
        $roomTypeFeaturePricing->is_special_days_exists = $params['isSpecialDaysExists'];
        $roomTypeFeaturePricing->special_days = $params['jsonSpecialDays'];
        $roomTypeFeaturePricing->impact_type = $params['priceImpactType'];
        $roomTypeFeaturePricing->impact_value = $params['impactValue'];
        $roomTypeFeaturePricing->active = $params['enableFeaturePrice'];
        return $roomTypeFeaturePricing->save();
    }

    /**
     * [getRoomTypeTotalPrice Returns Total price of the room type according to supplied dates].
     *
     * @param [int]  $id_product [id of the room type]
     * @param [date] $date_from  [date from]
     * @param [date] $date_to    [date to]
     *
     * @return [float] [Returns Total price of the room type]
     */
    public static function getRoomTypeTotalPrice($id_product, $date_from, $date_to, $quantity = 0, $id_group = 0)
    {
        $totalPrice = array();
        $totalPrice['total_price_tax_incl'] = 0;
        $totalPrice['total_price_tax_excl'] = 0;
        $featureImpactPriceTE = 0;
        $featureImpactPriceTI = 0;
        $productPriceTI = Product::getPriceStatic((int) $id_product, true);
        $productPriceTE = Product::getPriceStatic((int) $id_product, false);
        if ($productPriceTE) {
            $taxRate = (($productPriceTI-$productPriceTE)/$productPriceTE)*100;
        } else {
            $taxRate = 0;
        }

        // Initializations
        if (!$id_group) {
            $id_group = (int)Group::getCurrent()->id;
        }

        // if date_from and date_to are same then date_to will be the next date date of date_from
        if (strtotime($date_from) == strtotime($date_to)) {
            $date_to = date('Y-m-d', strtotime('+1 day', strtotime($date_from)));
        }
        $context = Context::getContext();
        $id_currency = Validate::isLoadedObject($context->currency) ? (int)$context->currency->id : (int)Configuration::get('PS_CURRENCY_DEFAULT');

        $hotelCartBookingData = new HotelCartBookingData();
        $date_from = date('Y-m-d', strtotime($date_from));
        $date_to = date('Y-m-d', strtotime($date_to));
        for($currentDate = $date_from; $currentDate < $date_to; $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)))) {
            if ($featurePrice = $hotelCartBookingData->getProductFeaturePricePlanByDateByPriority(
                $id_product,
                $currentDate,
                $id_group
            )) {
                if ($featurePrice['impact_type'] == 1) {
                    //percentage
                    $featureImpactPriceTE = $productPriceTE * ($featurePrice['impact_value'] / 100);
                    $featureImpactPriceTI = $productPriceTI * ($featurePrice['impact_value'] / 100);
                } else {
                    //Fixed Price
                    $taxPrice = ($featurePrice['impact_value']*$taxRate)/100;
                    $featureImpactPriceTE = Tools::convertPrice($featurePrice['impact_value'], $id_currency);
                    $featureImpactPriceTI = Tools::convertPrice($featurePrice['impact_value']+$taxPrice, $id_currency);
                }
                if ($featurePrice['impact_way'] == 1) {
                    // Decrease
                    $priceWithFeatureTE = ($productPriceTE - $featureImpactPriceTE);
                    $priceWithFeatureTI = ($productPriceTI - $featureImpactPriceTI);
                } else {
                    // Increase
                    $priceWithFeatureTE = ($productPriceTE + $featureImpactPriceTE);
                    $priceWithFeatureTI = ($productPriceTI + $featureImpactPriceTI);
                }
                if ($priceWithFeatureTI < 0) {
                    $priceWithFeatureTI = 0;
                    $priceWithFeatureTE = 0;
                }
                if ($quantity) {
                    $totalPrice['total_price_tax_incl'] += $priceWithFeatureTI * $quantity;
                    $totalPrice['total_price_tax_excl'] += $priceWithFeatureTE * $quantity;
                } else {
                    $totalPrice['total_price_tax_incl'] += $priceWithFeatureTI;
                    $totalPrice['total_price_tax_excl'] += $priceWithFeatureTE;
                }
            } else {
                if ($quantity) {
                    $totalPrice['total_price_tax_incl'] += $productPriceTI * $quantity;
                    $totalPrice['total_price_tax_excl'] += $productPriceTE * $quantity;
                } else {
                    $totalPrice['total_price_tax_incl'] += $productPriceTI;
                    $totalPrice['total_price_tax_excl'] += $productPriceTE;
                }
            }
        }
        return $totalPrice;
    }

    /**
     * [getRoomTypeFeaturePricePerDay returns per day feature price od the Room Type]
     * @param  [int] $id_product [id of the product]
     * @param  [date] $date_from  [start date]
     * @param  [date] $date_to    [end date]
     * @return [float] [returns per day feature price of the Room Type]
     */
    public static function getRoomTypeFeaturePricesPerDay($id_product, $date_from, $date_to, $use_tax = true, $id_group = 0)
    {
        $dateFrom = date('Y-m-d', strtotime($date_from));
        $dateTo = date('Y-m-d', strtotime($date_to));
        $totalDurationPrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
            $id_product,
            $dateFrom,
            $dateTo,
            0,
            $id_group
        );

        $totalDurationPriceTI = $totalDurationPrice['total_price_tax_incl'];
        $totalDurationPriceTE = $totalDurationPrice['total_price_tax_excl'];
        $objHotelBookingDetail = new HotelBookingDetail();
        $numDaysInDuration = $objHotelBookingDetail->getNumberOfDays($dateFrom, $dateTo);
        if ($use_tax) {
            $pricePerDay = $totalDurationPriceTI/$numDaysInDuration;
        } else {
            $pricePerDay = $totalDurationPriceTE/$numDaysInDuration;
        }
        return $pricePerDay;
    }

    /**
     * [getFeaturePricesbyIdProduct returns all feature prices by product]
     * @param  [int] $id_product [id of the product]
     * @return [array] [returns all feature prices by product]
     */
    public function getFeaturePricesbyIdProduct($id_product)
    {
        $idLang = Context::getContext()->language->id;
        return Db::getInstance()->executeS(
            'SELECT hrfp.*, hrfpl.`feature_price_name` FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing` hrfp
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_feature_pricing_lang` hrfpl
            ON(hrfp.`id_feature_price` = hrfpl.`id_feature_price` AND hrfpl.`id_lang` = '.(int)$idLang.')
            WHERE `id_product` = '.(int)$id_product
        );
    }

    public function deleteFeaturePriceByIdProduct($idProduct)
    {
        if (!$idProduct) {
            return false;
        }
        return Db::getInstance()->delete('htl_room_type_feature_pricing', 'id_product = '.(int)$idProduct);
    }

    /**
     * Update customer groups associated to the object
     * @param array $groups groups
     */
    public function updateGroup($groups)
    {
        $this->cleanGroups();
        if ($groups && !empty($groups)) {
            $this->addGroups($groups);
        }
    }

    /**
     * Deletes groups entries in the table. Send id_group if you want to delete entries by group i.e. when group deletes
     * @param integer $idGroup
     * @return void
     */
    public function cleanGroups($idGroup = 0)
    {
        if ($idGroup) {
            $condition = 'id_group = '.(int)$idGroup;
        } else {
            $condition = 'id_feature_price = '.(int)$this->id;
        }

    	return Db::getInstance()->delete('htl_room_type_feature_pricing_group', $condition);
    }

    /**
     * Add customer groups associated to the object
     * @param array $groups groups
     */
    public function addGroups($groups)
    {
        if ($groups && !empty($groups)) {
            foreach ($groups as $group) {
                $row = array('id_feature_price' => (int)$this->id, 'id_group' => (int)$group);
                Db::getInstance()->insert('htl_room_type_feature_pricing_group', $row, false, true, Db::INSERT_IGNORE);
            }
        }
    }

    public function getGroups($idFeaturePrice)
    {
        $groups = array();
        if ($results = Db::getInstance()->executeS(
            ' SELECT `id_group` FROM '._DB_PREFIX_.'htl_room_type_feature_pricing_group
            WHERE `id_feature_price` = '.(int)$idFeaturePrice
        )) {
            foreach ($results as $group) {
                $groups[] = (int)$group['id_group'];
            }
        }
        return $groups;
    }

    // Webservice:: get groups in the feature price
    public function getWsGroups()
    {
        return Db::getInstance()->executeS('
			SELECT fg.`id_group` as id
			FROM '._DB_PREFIX_.'htl_room_type_feature_pricing_group fg
			'.Shop::addSqlAssociation('group', 'fg').'
			WHERE fg.`id_feature_price` = '.(int)$this->id
        );
    }

    // Webservice:: set groups in the feature price
    public function setWsGroups($result)
    {
        $groups = array();
        foreach ($result as $row) {
            $groups[] = $row['id'];
        }
        $this->cleanGroups();
        $this->addGroups($groups);
        return true;
    }
}
