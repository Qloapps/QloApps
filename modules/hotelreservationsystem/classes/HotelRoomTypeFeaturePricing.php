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
    public $id_cart = 0;
    public $id_guest = 0;
    public $id_room = 0;
    public $feature_price_name;
    public $date_selection_type;
    public $is_special_days_exists;
    public $special_days;
    public $impact_way;
    public $impact_type;
    public $impact_value;
    public $active;
    public $date_add;
    public $date_upd;

    public $groupBox;

    const DATE_SELECTION_TYPE_RANGE = 1;
    const DATE_SELECTION_TYPE_SPECIFIC = 2;
    const DATE_SELECTION_TYPE_SPECIAL_DAYS = 3;

    const IMPACT_WAY_DECREASE = 1;
    const IMPACT_WAY_INCREASE = 2;
    const IMPACT_WAY_FIX_PRICE = 3;

    const IMPACT_TYPE_PERCENTAGE = 1;
    const IMPACT_TYPE_FIXED_PRICE = 2;

    public static $definition = array(
        'table' => 'htl_room_type_feature_pricing',
        'primary' => 'id_feature_price',
        'multilang' => true,
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_guest' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_room' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'impact_way' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'is_special_days_exists' => array('type' => self::TYPE_INT, 'required' => true),
            'date_selection_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'special_days' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'impact_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'impact_value' => array('type' => self::TYPE_FLOAT, 'required' => true),
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
        $this->deleteDatesByIdFeature($this->id);

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

    public function getDatesByIdFeature($idFeaturePrice)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing_date_range`
            WHERE `id_feature_price` ='.(int) $idFeaturePrice;

        return Db::getInstance()->executeS($sql);
    }

    public function deleteDatesByIdFeature($idFeaturePrice)
    {
        $sql = 'DELETE FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing_date_range`
            WHERE `id_feature_price` ='.(int) $idFeaturePrice;

        return Db::getInstance()->execute($sql);
    }

    public function saveUpdateDateRanges($idFeaturePrice, $dateRanges)
    {
        $this->deleteDatesByIdFeature($idFeaturePrice);
        $featurePriceDates = array();
        foreach ($dateRanges as $dateRange) {
            $featurePriceDates[] = array(
                'date_from' => pSQL($dateRange['date_from']),
                'date_to' => pSQL($dateRange['date_to']),
                'id_feature_price' => $idFeaturePrice
            );
        }

        return Db::getInstance()->insert('htl_room_type_feature_pricing_date_range', $featurePriceDates);
    }

    /**
     * [checkRoomTypeFeaturePriceExistanceForDateRanges returns room type active feature price plan by supplied date Ranges and supplied feature price plan type else returns false]
     * @param  [int] $idProduct [id of the product]
     * @param  [date] $dateRanges  [date ranges]
     * @param  [type] $type       [Type of the feature price plan.]
     * @return [array|false]      [returns room type active feature price plan by supplied date Ranges and supplied feature price plan type else returns false]
     */
    public function checkRoomTypeFeaturePriceExistanceForDateRanges(
        $idProduct,
        $dateRanges,
        $groups = array(),
        $type = self::DATE_SELECTION_TYPE_RANGE,
        $currentSpecialDays = false,
        $idFeaturePrice = 0
    ) {
        $dateRangeCondition = '';
        foreach ($dateRanges as $dateRange) {
            if ($dateRangeCondition != '') {
                $dateRangeCondition .= ' OR ';
            }

            $dateRangeCondition .= '(rtfpd.`date_from` <= "'.pSQL($dateRange['date_to']).'" AND rtfpd.`date_to` >= "'.pSQL($dateRange['date_from']).'")';
        }

        if ($dateRangeCondition != '') {
            $dateRangeCondition = ' AND ('.$dateRangeCondition.')';
        }

        if ($type == self::DATE_SELECTION_TYPE_SPECIFIC) {
            return Db::getInstance()->executeS(
                'SELECT rtfp.*, rtfpd.`date_from`, GROUP_CONCAT(rtfpg.`id_group`) AS `id_groups`, rtfpd.`date_to`
                FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing` rtfp
                INNER JOIN `'._DB_PREFIX_.'htl_room_type_feature_pricing_group` rtfpg
                ON (rtfp.`id_feature_price` = rtfpg.`id_feature_price`)
                LEFT JOIN `'._DB_PREFIX_.'htl_room_type_feature_pricing_date_range` rtfpd
                ON (rtfp.`id_feature_price` = rtfpd.`id_feature_price`)
                WHERE rtfp.`id_product`='.(int) $idProduct.' AND rtfp.`active` = 1 AND rtfp.`id_cart` = 0
                AND rtfp.`date_selection_type` = '.(int) self::DATE_SELECTION_TYPE_SPECIFIC.'
                AND rtfp.`id_feature_price`!='.(int) $idFeaturePrice.'
                '.($groups ? ' AND rtfpg.`id_group` IN ('.pSQL(implode(', ',$groups)).') ' : '').
                $dateRangeCondition.'
                GROUP BY rtfp.`id_feature_price`, rtfpd.`date_from`'
            );
        } elseif ($type == self::DATE_SELECTION_TYPE_SPECIAL_DAYS) {
            $featurePrices = Db::getInstance()->executeS(
                'SELECT rtfp.*, rtfpd.`date_from`, GROUP_CONCAT(rtfpg.`id_group`) AS `id_groups`, rtfpd.`date_to`
                FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing` rtfp
                INNER JOIN `'._DB_PREFIX_.'htl_room_type_feature_pricing_group` rtfpg
                ON (rtfp.`id_feature_price` = rtfpg.`id_feature_price`)
                LEFT JOIN `'._DB_PREFIX_.'htl_room_type_feature_pricing_date_range` rtfpd
                ON (rtfp.`id_feature_price` = rtfpd.`id_feature_price`)
                WHERE rtfp.`id_product`='.(int) $idProduct.'
                AND rtfp.`is_special_days_exists`=1 AND `active`=1 AND rtfp.`id_cart` = 0
                AND rtfp.`id_feature_price`!='.(int) $idFeaturePrice.'
                '.($groups ? ' AND rtfpg.`id_group` IN ('.pSQL(implode(', ',$groups)).') ' : '').
                $dateRangeCondition.'
                GROUP BY rtfp.`id_feature_price`, rtfpd.`date_from`'
            );
            $commonFeaturePrices = array();
            if ($featurePrices) {
                foreach ($featurePrices as $featurePrice) {
                    $specialDays = json_decode($featurePrice['special_days']);
                    $currentSpecialDays = json_decode($currentSpecialDays);
                    $commonValues = array_intersect($specialDays, $currentSpecialDays);
                    if ($commonValues) {
                        $commonFeaturePrices[] = $featurePrice;
                    }
                }
            }

            return $commonFeaturePrices;
        } elseif ($type == self::DATE_SELECTION_TYPE_RANGE) {
            return Db::getInstance()->executeS(
                'SELECT rtfp.*, rtfpd.`date_from`, GROUP_CONCAT(rtfpg.`id_group`) AS `id_groups`, rtfpd.`date_to`
                FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing` rtfp
                INNER JOIN `'._DB_PREFIX_.'htl_room_type_feature_pricing_group` rtfpg
                ON (rtfp.`id_feature_price` = rtfpg.`id_feature_price`)
                LEFT JOIN `'._DB_PREFIX_.'htl_room_type_feature_pricing_date_range` rtfpd
                ON (rtfp.`id_feature_price` = rtfpd.`id_feature_price`)
                WHERE rtfp.`id_product`='.(int) $idProduct.' AND rtfp.`active` = 1 AND rtfp.`id_cart` = 0
                AND rtfp.`date_selection_type` = '.(int) self::DATE_SELECTION_TYPE_RANGE.'
                AND rtfp.`is_special_days_exists`= 0
                AND rtfp.`id_feature_price`!='.(int) $idFeaturePrice.'
                '.($groups ? ' AND rtfpg.`id_group` IN ('.pSQL(implode(', ',$groups)).') ' : '').
                $dateRangeCondition.'
                GROUP BY rtfp.`id_feature_price`, rtfpd.`date_from`'
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
        $objBookingDetail = new HotelBookingDetail();
        $incr = 0;
        $date_from = date('Y-m-d', strtotime($date_from));
        $date_to = date('Y-m-d', strtotime($date_to));
        for($date = $date_from; $date < $date_to; $date = date('Y-m-d', strtotime('+1 day', strtotime($date)))) {
            $currentDate = date('Y-m-d', strtotime($date));
            $nextDayDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));
            if ($id_product) {
                $bookingParams = array(
                    'date_from' => $currentDate,
                    'date_to' => $nextDayDate,
                    'hotel_id' => $id_hotel,
                    'id_room_type' => $id_product,
                    'only_search_data' => 1,
                );
                $roomTypeAvailabilityInfo = $objBookingDetail->dataForFrontSearch($bookingParams);
                if (isset($roomTypeAvailabilityInfo['stats']['num_avail'])) {
                    $totalAvailableRooms = $roomTypeAvailabilityInfo['stats']['num_avail'];
                } else {
                    $totalAvailableRooms = 0;
                }

                $roomTypePrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice($id_product, $currentDate, $nextDayDate);
                $roomTypeRatesAndInventory[$incr]['date'] = $currentDate;
                $roomTypeRatesAndInventory[$incr]['room_types'][0]['id'] = $id_product;
                $roomTypeRatesAndInventory[$incr]['room_types'][0]['rates'] = $roomTypePrice;
                $roomTypeRatesAndInventory[$incr]['room_types'][0]['available_rooms'] = $totalAvailableRooms;
            } else {
                $hotelRoomTypes = $hotelRoomType->getRoomTypeByHotelId($id_hotel, $context->language->id);
                if ($hotelRoomTypes) {
                    $roomTypeRatesAndInventory[$incr]['date'] = $currentDate;
                    foreach ($hotelRoomTypes as $key => $product) {
                        $roomTypePrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                            $product['id_product'],
                            $currentDate,
                            $nextDayDate
                        );
                        $bookingParams = array(
                            'date_from' => $currentDate,
                            'date_to' => $nextDayDate,
                            'hotel_id' => $id_hotel,
                            'id_room_type' => $product['id_product'],
                            'only_search_data' => 1,
                        );
                        $roomTypeAvailabilityInfo = $objBookingDetail->dataForFrontSearch($bookingParams);
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
                                $productPriceTE = Product::getPriceStatic((int) $id_product, false);
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
                                    $featurePriceDateTo = date('Y-m-d', (strtotime($dateTo) - _TIME_1_DAY_));
                                    $params['dateTo'] = $featurePriceDateTo;
                                    $params['priceImpactWay'] = $priceImpactWay;
                                    $params['isSpecialDaysExists'] = 0;
                                    $params['jsonSpecialDays'] = null;
                                    $params['priceImpactType'] = 2;
                                    $params['impactValue'] = $impactValue;
                                    $params['enableFeaturePrice'] = 1;
                                    $nextDate = date('Y-m-d', strtotime('+1 day', strtotime($dateFrom)));
                                    if ($nextDate == $dateTo) {
                                        $params['dateSelectionType'] = self::DATE_SELECTION_TYPE_SPECIFIC;
                                        if ($featurePriceExists = $this->checkRoomTypeFeaturePriceExistanceForDateRanges(
                                            $id_product,
                                            array(array('date_from' => $dateFrom, 'date_to' => $featurePriceDateTo)),
                                            self::DATE_SELECTION_TYPE_SPECIFIC
                                        )) {
                                            $featurePriceExists = reset($featurePriceDateTo);
                                        }

                                        if ($featurePriceExists) {
                                            if (!$this->saveFeaturePricePlan($params, $featurePriceExists['id_feature_price'])) {
                                                $this->errors[] = $this->moduleInstance->l('Some error occured while saving Feature Price Plan Info:: Date From : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Date To : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Room Type Id : ', 'HotelRoomTypeFeaturePricing').$params['roomTypeId'];
                                            }
                                        } else {
                                            if (!$this->saveFeaturePricePlan($params)) {
                                                $this->errors[] = $this->moduleInstance->l('Some error occured while saving Feature Price Plan Info:: Date From : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Date To : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Room Type Id : ', 'HotelRoomTypeFeaturePricing').$params['roomTypeId'];
                                            }
                                        }
                                    } else {
                                        $params['dateSelectionType'] = self::DATE_SELECTION_TYPE_RANGE;
                                        if ($featurePriceExists = $this->checkRoomTypeFeaturePriceExistanceForDateRanges(
                                            $id_product,
                                            array(array('date_from' => $dateFrom, 'date_to' => $featurePriceDateTo)),
                                            self::DATE_SELECTION_TYPE_RANGE
                                        )) {
                                            $featurePriceExists = reset($featurePriceDateTo);
                                        }
                                        if ($featurePriceExists) {
                                            if ($featurePriceExists['date_from'] == $dateFrom
                                                && $featurePriceExists['date_to'] == $featurePriceDateTo
                                            ) {
                                                if (!$this->saveFeaturePricePlan(
                                                    $params,
                                                    $featurePriceExists['id_feature_price']
                                                )) {
                                                    $this->errors[] = $this->moduleInstance->l('Some error occured while saving Feature Price Plan Info:: Date From : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Date To : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Room Type Id : ', 'HotelRoomTypeFeaturePricing').$params['roomTypeId'];
                                                }
                                            } else {
                                                for($date = $dateFrom; $date < $dateTo; $date = date('Y-m-d', strtotime('+1 day', strtotime($date)))) {
                                                    // Creating feature prices day wise, for single days.
                                                    $nextDayDate = $currentDate = date('Y-m-d', $date);
                                                    $params['dateFrom'] = $currentDate;
                                                    $params['dateTo'] = $nextDayDate;
                                                    $params['dateSelectionType'] = self::DATE_SELECTION_TYPE_SPECIFIC;
                                                    $featurePriceExists = $this->checkRoomTypeFeaturePriceExistanceForDateRanges(
                                                        $id_product,
                                                        array(array('date_from' => $currentDate, 'date_to' => $nextDayDate)),
                                                        self::DATE_SELECTION_TYPE_SPECIFIC
                                                    );
                                                    if ($featurePriceExists) {
                                                        if (!$this->saveFeaturePricePlan(
                                                            $params,
                                                            $featurePriceExists['id_feature_price']
                                                        )) {
                                                            $this->errors[] = $this->moduleInstance->l('Some error occured while saving Feature Price Plan Info:: Date From : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Date To : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Room Type Id : ', 'HotelRoomTypeFeaturePricing').$params['roomTypeId'];
                                                        }
                                                    } else {
                                                        if (!$this->saveFeaturePricePlan($params)) {
                                                            $this->errors[] = $this->moduleInstance->l('Some error occured while saving Feature Price Plan Info:: Date From : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Date To : ', 'HotelRoomTypeFeaturePricing').$params['dateFrom'].$this->moduleInstance->l(' Room Type Id : ', 'HotelRoomTypeFeaturePricing').$params['roomTypeId'];
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            if (!$this->saveFeaturePricePlan($params)) {
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
                                    $bookingParams = array(
                                        'date_from' => $dateFrom,
                                        'date_to' => $dateTo,
                                        'hotel_id' => $id_hotel,
                                        'id_room_type' => $id_product,
                                        'only_search_data' => 1,
                                    );
                                    $roomTypeAvailabilityInfo = $hotelBookingDetail->dataForFrontSearch($bookingParams);
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
    public function saveFeaturePricePlan($params, $id = 0)
    {
        return HotelRoomTypeFeaturePricing::createRoomTypeFeaturePrice(
            array(
                'id' => $id,
                'id_product' => $params['roomTypeId'],
                'name' => $params['featurePriceName'],
                'date_selection_type' => $params['dateSelectionType'],
                'date_from' => $params['dateFrom'],
                'date_to' => $params['dateTo'],
                'active' => $params['enableFeaturePrice'],
                'impact_way' => $params['priceImpactWay'],
                'is_special_days_exists' => $params['isSpecialDaysExists'],
                'special_days' => $params['jsonSpecialDays'],
                'impact_type' => $params['priceImpactType'],
                'impact_value' => $params['impactValue'],
            )
        );
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
    public static function getRoomTypeTotalPrice(
        $id_product,
        $date_from,
        $date_to,
        $occupancy = null,
        $id_group = 0,
        $id_cart = 0,
        $id_guest = 0,
        $id_room = 0,
        $with_auto_room_services = 1,
        $use_reduc = 1
    ) {
        $totalPrice = array();
        $totalPrice['total_price_tax_incl'] = 0;
        $totalPrice['total_price_tax_excl'] = 0;
        $featureImpactPriceTE = 0;
        $featureImpactPriceTI = 0;
        $productPriceTI = Product::getPriceStatic((int) $id_product, 1, 0, 6, null, 0, $use_reduc, 1, 0, null, null, null, $nothing, 1, 1, null, 1, 0, 0, $id_group);
        $productPriceTE = Product::getPriceStatic((int) $id_product, 0, 0, 6, null, 0, $use_reduc, 1, 0, null, null, null, $nothing, 1, 1, null, 1, 0, 0, $id_group);
        if ($productPriceTE) {
            $taxRate = (($productPriceTI-$productPriceTE)/$productPriceTE)*100;
        } else {
            $taxRate = 0;
        }

        if (is_array($occupancy) && count($occupancy)) {
            $quantity = count($occupancy);
        } else {
            $quantity = $occupancy;
        }

        // Initializations
        if (!$id_group) {
            $id_group = (int)Group::getCurrent()->id;
        }

        // if date_from and date_to are same then date_to will be the next date date of date_from
        if (strtotime($date_from) == strtotime($date_to)) {
            $date_to = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($date_from)));
        }
        $context = Context::getContext();
        $id_currency = Validate::isLoadedObject($context->currency) ? (int)$context->currency->id : (int)Configuration::get('PS_CURRENCY_DEFAULT');

        for($currentDate = date('Y-m-d', strtotime($date_from)); $currentDate < date('Y-m-d', strtotime($date_to)); $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)))) {
            if ($use_reduc && ($featurePrice = HotelCartBookingData::getProductFeaturePricePlanByDateByPriority(
                $id_product,
                $currentDate,
                $id_group,
                $id_cart,
                $id_guest,
                $id_room
            ))) {
                if ($featurePrice['impact_type'] == self::IMPACT_TYPE_PERCENTAGE) {
                    //percentage
                    $featureImpactPriceTE = $productPriceTE * ($featurePrice['impact_value'] / 100);
                    $featureImpactPriceTI = $productPriceTI * ($featurePrice['impact_value'] / 100);
                } else {
                    //Fixed Price
                    $taxPrice = ($featurePrice['impact_value']*$taxRate)/100;
                    $featureImpactPriceTE = Tools::convertPrice($featurePrice['impact_value'], $id_currency);
                    $featureImpactPriceTI = Tools::convertPrice($featurePrice['impact_value']+$taxPrice, $id_currency);
                }
                if ($featurePrice['impact_way'] == self::IMPACT_WAY_DECREASE) {
                    // Decrease
                    $priceWithFeatureTE = ($productPriceTE - $featureImpactPriceTE);
                    $priceWithFeatureTI = ($productPriceTI - $featureImpactPriceTI);
                } elseif ($featurePrice['impact_way'] == self::IMPACT_WAY_INCREASE) {
                    // Increase
                    $priceWithFeatureTE = ($productPriceTE + $featureImpactPriceTE);
                    $priceWithFeatureTI = ($productPriceTI + $featureImpactPriceTI);
                } else {
                    // Fix
                    $priceWithFeatureTE = $featureImpactPriceTE;
                    $priceWithFeatureTI = $featureImpactPriceTI;
                }
                if ($priceWithFeatureTI < 0) {
                    $priceWithFeatureTI = 0;
                    $priceWithFeatureTE = 0;
                }
                $totalPrice['total_price_tax_incl'] += $priceWithFeatureTI;
                $totalPrice['total_price_tax_excl'] += $priceWithFeatureTE;
            } else {
                $totalPrice['total_price_tax_incl'] += $productPriceTI;
                $totalPrice['total_price_tax_excl'] += $productPriceTE;
            }
        }
        Hook::exec('actionRoomTypeTotalPriceModifier',
            array(
                'total_prices' => &$totalPrice,
                'id_room_type' => $id_product,
                'id_room' => $id_room,
                'date_from' => $date_from,
                'date_to' => $date_to,
                'id_currency' => $id_currency,
                'quantity' => $quantity,
                'id_cart' => $id_cart,
                'id_guest' => $id_guest,
                'id_group' => $id_group,
                'use_reduc' => $use_reduc,
                'tax_rate' => $taxRate,
                'occupancy' => $occupancy
            )
        );
        if ($with_auto_room_services) {
            if ($id_cart && $id_room) {
                $objHotelCartBookingData = new HotelCartBookingData();
                if ($roomHtlCartInfo = $objHotelCartBookingData->getRoomRowByIdProductIdRoomInDateRange(
                    $id_cart,
                    $id_product,
                    $date_from,
                    $date_to,
                    $id_room
                )) {
                    $objServiceProductCartDetail = new ServiceProductCartDetail();
                    if ($roomServicesServices = $objServiceProductCartDetail->getServiceProductsInCart(
                        $id_cart,
                        [],
                        null,
                        $roomHtlCartInfo['id'],
                        null,
                        null,
                        null,
                        null,
                        0,
                        1,
                        Product::PRICE_ADDITION_TYPE_WITH_ROOM
                    )) {
                        $selectedServices = array_shift($roomServicesServices);
                        $totalPrice['total_price_tax_incl'] += $selectedServices['total_price_tax_incl'];
                        $totalPrice['total_price_tax_excl'] += $selectedServices['total_price_tax_excl'];
                    }
                }

            } else {
                if ($servicesWithTax = RoomTypeServiceProduct::getAutoAddServices(
                    $id_product,
                    $date_from,
                    $date_to,
                    Product::PRICE_ADDITION_TYPE_WITH_ROOM,
                    true,
                    $use_reduc
                )) {
                    foreach($servicesWithTax as $service) {
                        $totalPrice['total_price_tax_incl'] += Tools::processPriceRounding($service['price']);
                    }
                }
                if ($servicesWithoutTax = RoomTypeServiceProduct::getAutoAddServices(
                    $id_product,
                    $date_from,
                    $date_to,
                    Product::PRICE_ADDITION_TYPE_WITH_ROOM,
                    false,
                    $use_reduc
                )) {
                    foreach($servicesWithoutTax as $service) {
                        $totalPrice['total_price_tax_excl'] += Tools::processPriceRounding($service['price']);
                    }
                }
            }
        }

        if (!$quantity) {
            $quantity = 1;
        }
        $totalPrice['total_price_tax_incl'] = Tools::processPriceRounding($totalPrice['total_price_tax_incl'], $quantity);
        $totalPrice['total_price_tax_excl'] = Tools::processPriceRounding($totalPrice['total_price_tax_excl'], $quantity);

        return $totalPrice;
    }

    /**
     * [getRoomTypeFeaturePricePerDay returns per day feature price od the Room Type]
     * @param  [int] $id_product [id of the product]
     * @param  [date] $date_from  [start date]
     * @param  [date] $date_to    [end date]
     * @return [float] [returns per day feature price of the Room Type]
     */
    public static function getRoomTypeFeaturePricesPerDay(
        $id_product,
        $date_from,
        $date_to,
        $use_tax = true,
        $id_group = 0,
        $id_cart = 0,
        $id_guest = 0,
        $id_room = 0,
        $with_auto_room_services = 1,
        $use_reduc = 1,
        $occupancy = array()
    ) {
        $dateFrom = date('Y-m-d H:i:s', strtotime($date_from));
        $dateTo = date('Y-m-d H:i:s', strtotime($date_to));
        $totalDurationPrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
            $id_product,
            $dateFrom,
            $dateTo,
            $occupancy,
            $id_group,
            $id_cart,
            $id_guest,
            $id_room,
            $with_auto_room_services,
            $use_reduc
        );

        $totalDurationPriceTI = $totalDurationPrice['total_price_tax_incl'];
        $totalDurationPriceTE = $totalDurationPrice['total_price_tax_excl'];
        $numDaysInDuration = HotelHelper::getNumberOfDays($dateFrom, $dateTo);
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
    public function getFeaturePricesbyIdProduct($id_product, $id_cart = 0, $id_guest = 0, $id_room = 0)
    {
        $idLang = Context::getContext()->language->id;
        return Db::getInstance()->executeS(
            'SELECT hrfp.*, hrfpl.`feature_price_name` FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing` hrfp
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_feature_pricing_lang` hrfpl
            ON(hrfp.`id_feature_price` = hrfpl.`id_feature_price` AND hrfpl.`id_lang` = '.(int)$idLang.')
            WHERE `id_product` = '.(int)$id_product.' AND `id_cart` = '.(int)$id_cart.' AND `id_guest` = '.(int)$id_guest.' AND `id_room` = '.(int)$id_room
        );
    }

    /**
     * @deprecated since 1.6.1 use deleteFeaturePrices() instead
    */
    public function deleteFeaturePriceByIdProduct($idProduct)
    {
        if (!$idProduct) {
            return false;
        }
        return HotelRoomTypeFeaturePricing::deleteFeaturePrices(false, $idProduct);
    }

    /**
     * @deprecated since 1.6.1 use deleteFeaturePrices() instead
    */
    public static function deleteByIdCart(
        $id_cart,
        $id_product = false,
        $id_room = false,
        $date_from = false,
        $date_to = false
    ) {
        return HotelRoomTypeFeaturePricing::deleteFeaturePrices(
            $id_cart,
            $id_product,
            $id_room,
            $date_from,
            $date_to
        );
    }

    public static function deleteFeaturePrices(
        $id_cart = false,
        $id_product = false,
        $id_room = false,
        $date_from = false,
        $date_to = false
    ) {
        if ($date_from) {
            $date_from = date('Y-m-d', strtotime($date_from));
        }

        if ($date_to) {
            $date_to = date('Y-m-d', strtotime($date_to));
        }

        $idfeaturePrices = Db::getInstance()->executeS(
            'SELECT rtfpd.`id_feature_price` FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing` rtfp
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_feature_pricing_date_range` rtfpd
            ON (rtfp.`id_feature_price` = rtfpd.`id_feature_price`)
            WHERE 1'.
            ($id_cart ? ' AND rtfp.`id_cart` = '.(int) $id_cart : '').
            ($id_product ? ' AND rtfp.`id_product` = '.(int) $id_product : '').
            ($id_room ? ' AND rtfp.`id_room` = '.(int) $id_room : '').
            ($date_from ? ' AND rtfpd.`date_from` = "'.pSQL($date_from) .'"' : '').
            ($date_to ? ' AND rtfpd.`date_to` = "'.pSQL($date_to) .'"' : '')
        );
        $res = true;
        foreach ($idfeaturePrices as $featurePrice) {
            $objHotelRoomTypeFeaturePricing = new HotelRoomTypeFeaturePricing((int)$featurePrice['id_feature_price']);
            $res = $res && $objHotelRoomTypeFeaturePricing->delete();
        }
        return $res;
    }

    /**
     * Update customer groups associated to the object
     * @param array $groups groups
     */
    public function updateGroup($groups)
    {
        if ($groups && !empty($groups)) {
            $this->cleanGroups();
            $this->addGroups($groups);
        }
    }

    /**
     * Deletes groups entries in the table. Send id_group if you want to delete entries by group i.e. when group deletes
     * @param integer $idGroup
     * @return bool
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

    public function validateFields($die = true, $error_return = false)
    {
        if (isset($this->webservice_validation) && $this->webservice_validation) {
            $weekDays = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
            if($this->is_special_days_exists) {
                if ($this->special_days
                    && ($specialDays = json_decode($this->special_days, true))
                ) {
                    if (is_array($specialDays) && $specialDays) {
                        if (count(array_diff($specialDays, $weekDays))) {
                            $message = Tools::displayError('Invalid special days. format must match with : ["mon", "tue", "wed", "thu", "fri", "sat", "sun"]', false);
                        }
                    } else {
                        $message = Tools::displayError('Invalid special days. format must match with : ["mon", "tue", "wed", "thu", "fri", "sat", "sun"]', false);
                    }
                } else {
                    $message = Tools::displayError('Invalid special days. format must match with : ["mon", "tue", "wed", "thu", "fri", "sat", "sun"]', false);
                }
            }

            if (isset($message) && $message != '') {
                if ($die) {
                    throw new PrestaShopException($message);
                }

                return $error_return ? $message : false;
            }
        }

        return parent::validateFields($die, $error_return);
    }

    public static function createRoomTypeFeaturePrice($params)
    {
        $context = Context::getContext();
        $featurePriceName = array();
        foreach (Language::getIDs(true) as $idLang) {
            $featurePriceName[$idLang] = !empty($params['name']) ? $params['name'] : 'Auto-generated';
        }

        if (!empty($params['id'])) {
            $objFeaturePricing = new HotelRoomTypeFeaturePricing((int) $params['id']);
        } else {
            $objFeaturePricing = new HotelRoomTypeFeaturePricing();
        }

        $objFeaturePricing->id_product = (int) $params['id_product'];
        $objFeaturePricing->id_cart = !empty($params['id_cart']) ? $params['id_cart'] : (int) $params['id_cart'];
        $objFeaturePricing->id_guest = !empty($params['id_guest']) ? $params['id_guest'] : (int) $params['id_guest'];
        $objFeaturePricing->id_room = !empty($params['id_room']) ? $params['id_room'] : (int) $params['id_room'];
        $objFeaturePricing->feature_price_name = $featurePriceName;
        $objFeaturePricing->date_selection_type = !empty($params['date_selection_type']) ? $params['date_selection_type'] : HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE;
        $objFeaturePricing->is_special_days_exists = !empty($params['is_special_days_exists']) ? $params['is_special_days_exists'] : 0;
        $objFeaturePricing->special_days = !empty($params['special_days']) ? json_encode($params['special_days']) : json_encode(array());
        $objFeaturePricing->impact_way = !empty($params['impact_way']) ? $params['impact_way'] : HotelRoomTypeFeaturePricing::IMPACT_WAY_FIX_PRICE;
        $objFeaturePricing->impact_type = !empty($params['impact_type']) ? $params['impact_type'] :  HotelRoomTypeFeaturePricing::IMPACT_TYPE_FIXED_PRICE;
        $objFeaturePricing->impact_value = $params['price'];
        $objFeaturePricing->active = isset($params['active']) ? (int) $params['active'] : 1;
        $objFeaturePricing->groupBox = !empty($params['id_groups']) ? $params['id_groups'] : array_column(Group::getGroups($context->language->id), 'id_group');
        if ($objFeaturePricing->save()) {
            $objFeaturePricing->saveUpdateDateRanges($objFeaturePricing->id,
                array(
                    array(
                        'date_from' => date('Y-m-d', strtotime($params['date_from'])),
                        'date_to' => date('Y-m-d', strtotime($params['date_to']))
                    )
                )
            );
        }

        return $objFeaturePricing->id;
    }
}
