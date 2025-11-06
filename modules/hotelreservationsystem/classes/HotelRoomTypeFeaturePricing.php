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

class HotelRoomTypeFeaturePricing extends ObjectModel
{
    public $id_product;
    public $id_cart = 0;
    public $id_guest = 0;
    public $id_room = 0;
    public $feature_price_name;
    public $impact_way;
    public $impact_type;
    public $impact_value;
    public $active;
    public $date_add;
    public $date_upd;

    public $groupBox;

    const DATE_SELECTION_TYPE_RANGE = 1;
    const DATE_SELECTION_TYPE_SPECIFIC = 2;

    const IMPACT_WAY_DECREASE = 1;
    const IMPACT_WAY_INCREASE = 2;
    const IMPACT_WAY_FIX_PRICE = 3;

    const IMPACT_TYPE_PERCENTAGE = 1;
    const IMPACT_TYPE_FIXED_PRICE = 2;

    protected $moduleInstance;

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
            'restrictions' => array(
                'resource' => 'price_rule',
                'getter' => 'getWsFeaturePriceRestriction',
                'setter' => 'setWsFeaturePriceRestriction',
                'fields' => array(
                    'id' => array(),
                    'date_from' => array(),
                    'date_to' => array(),
                    'date_selection_type' => array(),
                    'is_special_days_exists' => array(),
                    'special_days' => array(),
                ),
            ),
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
        $objFeaturePriceRestriction = new HotelRoomTypeFeaturePricingRestriction();
        if ($existingFeaturePrices = $objFeaturePriceRestriction->getRestrictionsByIdFeaturePrice($this->id)) {
            $existingFeaturePrices = array_column($existingFeaturePrices, 'id_feature_price_restriction', 'id_feature_price_restriction');
            $objFeaturePriceRestriction->deleteFeaturePriceRestrictionsById($existingFeaturePrices);
        }

        return parent::delete();
    }

    public function getFeaturePrices(
        $idRoomType,
        $restrictions = array(),
        $groups = array(),
        $skipFeaturePriceId = null,
        $active = null
    ) {
        $sql = 'SELECT *, GROUP_CONCAT(rtfpg.`id_group`) AS id_group FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing_restriction` rtfpr
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_feature_pricing` rtfp
            ON (rtfpr.`id_feature_price` = rtfp.`id_feature_price` AND rtfp.`id_product`='.(int) $idRoomType.')
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_feature_pricing_group` rtfpg
            ON (rtfp.`id_feature_price` = rtfpg.`id_feature_price` '.($groups ? ' AND rtfpg.`id_group` IN ('.pSQL(implode(', ',$groups)).')' : ' ' ).')
            WHERE 1 '.( !is_null($active) ? ' AND rtfp.`active`= '.(int) $active: ' ').' ' .(!is_null($skipFeaturePriceId) ? ' AND rtfpr.`id_feature_price`!='.(int) $skipFeaturePriceId : ' ');
        $sqlWhere = '';
        if ($restrictions && is_array($restrictions)) {
            foreach ($restrictions as $restriction) {
                if ($sqlWhere != '') {
                    $sqlWhere .= ' OR ';
                }

                $dateFrom = date('Y-m-d', strtotime($restriction['date_from']));
                $dateTo = date('Y-m-d', strtotime($restriction['date_to']));
                if ($restriction['date_selection_type'] == self::DATE_SELECTION_TYPE_SPECIFIC) {
                    $sqlWhere .= ' (rtfpr.`date_selection_type` = '.(int) self::DATE_SELECTION_TYPE_SPECIFIC.'
                        AND rtfpr.`date_from` = \''.pSQL($dateFrom).'\')';
                } else if ($restriction['date_selection_type'] == self::DATE_SELECTION_TYPE_RANGE) {
                    if ($restriction['is_special_days_exists']) {
                        $sqlWhere .= ' (rtfpr.`is_special_days_exists`=1
                        AND rtfpr.`date_from` <= \''.pSQL($dateTo).'\'
                        AND rtfpr.`date_to` >= \''.pSQL($dateFrom).'\')';
                    } else {
                        $sqlWhere .= ' (rtfpr.`date_selection_type` = '.(int) self::DATE_SELECTION_TYPE_RANGE.'
                        AND rtfpr.`is_special_days_exists`=0
                        AND rtfpr.`date_from` <= \''.pSQL($dateTo).'\'
                        AND rtfpr.`date_to` >= \''.pSQL($dateFrom).'\')';
                    }
                }
            }
        }

        if ($sqlWhere != '') {
            $sqlWhere = ' AND ('.$sqlWhere.')';
        }

        $sql .= $sqlWhere.' GROUP BY rtfpr.`id_feature_price`';

        return Db::getInstance()->executeS($sql);
    }

    public function saveFeaturePriceRestrictions($idFeaturePrice, $restrictions)
    {
        $res = true;
        $objFeaturePriceRestriction = new HotelRoomTypeFeaturePricingRestriction();
        if ($existingFeaturePrices = $objFeaturePriceRestriction->getRestrictionsByIdFeaturePrice($idFeaturePrice)) {
            $existingFeaturePrices = array_column($existingFeaturePrices, 'id_feature_price_restriction', 'id_feature_price_restriction');
        }

        if ($restrictions) {
            foreach ($restrictions as $featurePriceRule) {
                if (isset($featurePriceRule['id']) && in_array($featurePriceRule['id'], $existingFeaturePrices)) {
                    $objFeaturePriceRestriction = new HotelRoomTypeFeaturePricingRestriction($featurePriceRule['id']);
                    unset($existingFeaturePrices[$featurePriceRule['id']]);
                } else {
                    $objFeaturePriceRestriction = new HotelRoomTypeFeaturePricingRestriction();
                }

                $objFeaturePriceRestriction->id_feature_price = $idFeaturePrice;
                $objFeaturePriceRestriction->date_from = $featurePriceRule['date_from'];
                $objFeaturePriceRestriction->date_to = $featurePriceRule['date_to'];
                $objFeaturePriceRestriction->date_selection_type = isset($featurePriceRule['date_selection_type']) ? $featurePriceRule['date_selection_type'] : HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE;
                $objFeaturePriceRestriction->special_days = json_encode(array());
                if ($objFeaturePriceRestriction->date_selection_type == HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE
                    && isset($featurePriceRule['is_special_days_exists']) && $featurePriceRule['is_special_days_exists']
                ) {
                    $objFeaturePriceRestriction->is_special_days_exists = $featurePriceRule['is_special_days_exists'];
                    if (isset($featurePriceRule['is_special_days_exists'])
                        && $featurePriceRule['is_special_days_exists']
                        && isset($featurePriceRule['special_days'])
                        && $featurePriceRule['special_days']
                    ) {
                        $objFeaturePriceRestriction->special_days = json_encode($featurePriceRule['special_days']);
                    }
                } else {
                    $objFeaturePriceRestriction->is_special_days_exists = 0;
                }

                $res &= $objFeaturePriceRestriction->save();
            }
        }

        if ($existingFeaturePrices) {
            $res &= $objFeaturePriceRestriction->deleteFeaturePriceRestrictionsById($existingFeaturePrices);
        }

        return $res;
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
                        foreach($roomServicesServices as $selectedService) {
                            $totalPrice['total_price_tax_incl'] += $selectedService['total_price_tax_incl'];
                            $totalPrice['total_price_tax_excl'] += $selectedService['total_price_tax_excl'];
                        }
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
            'SELECT hrfp.*, hrfpr.`date_from`, hrfpr.`date_to`, hrfpr.`date_selection_type`, hrfpr.`is_special_days_exists`, hrfpr.`special_days`, hrfpl.`feature_price_name`
            FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing` hrfp
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_feature_pricing_lang` hrfpl
            ON(hrfp.`id_feature_price` = hrfpl.`id_feature_price` AND hrfpl.`id_lang` = '.(int)$idLang.')
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_feature_pricing_restriction` hrfpr
            ON (hrfpr.`id_feature_price` = hrfp.`id_feature_price`)
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
            'SELECT hrfp.`id_feature_price`  FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing` hrfp
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_feature_pricing_restriction` hrfpr
            ON (hrfpr.`id_feature_price` = hrfp.`id_feature_price`)
            WHERE 1'.
            ($id_cart ? ' AND hrfp.`id_cart` = '.(int) $id_cart : '').
            ($id_product ? ' AND hrfp.`id_product` = '.(int) $id_product : '').
            ($id_room ? ' AND hrfp.`id_room` = '.(int) $id_room : '').
            ($date_from ? ' AND hrfpr.`date_from` = "'.pSQL($date_from) .'"' : '').
            ($date_to ? ' AND hrfpr.`date_to` = "'.pSQL($date_to) .'"' : '')
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

    public function getWsFeaturePriceRestriction()
    {
        return Db::getInstance()->executeS(
            'SELECT *, id_feature_price_restriction AS `id` FROM `'._DB_PREFIX_.'htl_room_type_feature_pricing_restriction`
            WHERE `id_feature_price` ='.(int)$this->id.' ORDER BY `id_feature_price` ASC'
        );

    }

    public function setWsFeaturePriceRestriction($restrictions)
    {
        foreach ($restrictions as $restrictionKey => $restriction) {
            if ($restriction['date_selection_type'] == HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE
                && $restriction['is_special_days_exists']
            ) {
                $specialDays = json_decode($restriction['special_days'], true);
                $restrictions[$restrictionKey]['special_days'] = $specialDays;
            }
        }

        return $this->saveFeaturePriceRestrictions($this->id, $restrictions);
    }

    public function getDuplicateRestrictions(
        $roomTypeId,
        $groups,
        $skipFeaturePriceId,
        $restrictions
    ) {
        $duplicateRestrictions = array();
        if ($existingFeturePrices = $this->getFeaturePrices(
            $roomTypeId,
            $restrictions,
            $groups,
            $skipFeaturePriceId,
            true
        )) {
            foreach ($existingFeturePrices as $existingFeturePrice) {
                foreach ($restrictions as $restrictionKey => $restriction) {
                    if ($restriction['date_selection_type'] == $existingFeturePrice['date_selection_type']) {
                        if ($restriction['date_selection_type'] == HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_SPECIFIC) {
                            if (strtotime($restriction['date_from']) != strtotime($existingFeturePrice['date_from'])) {
                                continue;
                            } else {
                                $duplicateRestrictions[] = $restrictionKey;
                            }
                        } else if ($restriction['date_selection_type'] == HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE) {
                            if (((strtotime($existingFeturePrice['date_from']) < strtotime($restriction['date_from'])) && (strtotime($existingFeturePrice['date_to']) <= strtotime($restriction['date_from']))) || ((strtotime($existingFeturePrice['date_from']) > strtotime($restriction['date_from'])) && (strtotime($existingFeturePrice['date_from']) >= strtotime($restriction['date_to'])))) {
                                continue;
                            } else {
                                if ($existingFeturePrice['is_special_days_exists'] && $restriction['is_special_days_exists']) {
                                    if (!empty($existingFeturePrice['special_days']) && !empty($restriction['special_days'])) {
                                        $existingDays = json_decode($existingFeturePrice['special_days'], true);
                                        if (array_intersect($existingDays, $restriction['special_days'])) {
                                            $duplicateRestrictions[] = $restrictionKey;
                                        }
                                    }
                                } else {
                                    $duplicateRestrictions[] = $restrictionKey;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $duplicateRestrictions;
    }

    public function validateFields($die = true, $error_return = false)
    {
        if (isset($this->webservice_validation) && $this->webservice_validation) {
            $restrictions = array();
            $idGroups = array();
            if (isset($this->associations)) {
                foreach ($this->associations->children() as $association) {
                    if ($association->getName() == 'restrictions') {
                        $assocItems = $association->children();
                        foreach ($assocItems as $assocItem) {
                            /** @var SimpleXMLElement $assocItem */
                            $fields = $assocItem->children();
                            $entry = array();
                            foreach ($fields as $fieldName => $fieldValue) {
                                $entry[$fieldName] = (string)$fieldValue;
                            }

                            $restrictions[] = $entry;
                        }
                    } else if ($association->getName() == 'groups') {
                        $assocItems = $association->children();
                        foreach ($assocItems as $assocItem) {
                            /** @var SimpleXMLElement $assocItem */
                            $fields = $assocItem->children();
                            $entry = array();
                            foreach ($fields as $fieldName => $fieldValue) {
                                $entry[$fieldName] = (string)$fieldValue;
                            }

                            $idGroups[] = $entry;
                        }
                    }
                }
            }
            if ($idGroups) {
                $idGroups = array_column($idGroups, 'id');
            }

            if ($restrictions) {
                $hasError = false;
                $weekDays = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
                // check for invalid special days
                foreach ($restrictions as $restrictionKey => $restriction) {
                    if ($restriction['date_selection_type'] == HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE) {
                        if ($restriction['is_special_days_exists']) {
                            $specialDays = json_decode($restriction['special_days'], true);
                            $restrictions[$restrictionKey]['special_days'] = $specialDays;
                            if (is_array($specialDays) && $specialDays) {
                                if (count(array_diff($specialDays, $weekDays))) {
                                    $message = Tools::displayError('Invalid special days. format must match with : ["mon", "tue", "wed", "thu", "fri", "sat", "sun"].', true);
                                    $hasError = true;
                                    break;
                                }
                            } else {
                                $message = Tools::displayError('Invalid special days. format must match with : ["mon", "tue", "wed", "thu", "fri", "sat", "sun"].', true);
                                $hasError = true;
                                break;
                            }
                        }
                    }
                }

                if (!$hasError) {
                    // check for conflicting dates in the rules.
                    foreach ($restrictions as $restrictionKey => $restriction) {
                        foreach ($restrictions as $priceRestrictionKey => $priceRestriction) {
                            if ($priceRestrictionKey != $restrictionKey) {
                                if ($priceRestriction['date_selection_type'] == $restriction['date_selection_type']) {
                                    if ($priceRestriction['date_selection_type'] == HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_SPECIFIC) {
                                        if (strtotime($priceRestriction['date_from']) != strtotime($restriction['date_from'])) {
                                            continue;
                                        } else {
                                            $message = Tools::displayError('You can not add conflicting dates.', true);
                                            $hasError = true;
                                            break;
                                        }
                                    } else if ($priceRestriction['date_selection_type'] == HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE) {
                                        if (((strtotime($restriction['date_from']) < strtotime($priceRestriction['date_from'])) && (strtotime($restriction['date_to']) <= strtotime($priceRestriction['date_from']))) || ((strtotime($restriction['date_from']) > strtotime($priceRestriction['date_from'])) && (strtotime($restriction['date_from']) >= strtotime($priceRestriction['date_to'])))) {
                                            continue;
                                        } else {
                                            if ($restriction['is_special_days_exists'] && $priceRestriction['is_special_days_exists']) {
                                                if (!empty($restriction['special_days']) && !empty($priceRestriction['special_days'])) {
                                                    if (array_intersect($restriction['special_days'], $priceRestriction['special_days'])) {
                                                        $message = Tools::displayError('You can not add conflicting days for similar date ranges.', true);
                                                        $hasError = true;
                                                        break;
                                                    }
                                                }
                                            } else {
                                                $message = Tools::displayError('You can not add conflicting date ranges.', true);
                                                $hasError = true;
                                                break;
                                            }
                                        }
                                    }
                                }
                            }

                        }
                    }
                }

                if (!$hasError) {
                    $hasDuplicate = $this->getDuplicateRestrictions(
                        $this->id_product,
                        $idGroups,
                        $this->id,
                        $restrictions
                    );

                    if ($hasDuplicate) {
                        $message = Tools::displayError('An advanced price rule already exists with overlapping conditions.', false);
                    }
                }
            } else {
                $message = Tools::displayError('Price rules are required.', false);
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
            if (isset($params['name']) && $params['name']) {
                $featurePriceName[$idLang] = $params['name'];
            } else {
                $featurePriceName[$idLang] = 'Auto-generated';
            }
        }

        if (isset($params['id']) && $params['id']) {
            $objFeaturePricing = new HotelRoomTypeFeaturePricing($params['id']);
        } else {
            $objFeaturePricing = new HotelRoomTypeFeaturePricing();
        }

        $objFeaturePricing->id_product = (int) $params['id_product'];
        $objFeaturePricing->id_cart = (int) isset($params['id_cart']) ? $params['id_cart'] : 0;
        $objFeaturePricing->id_guest = (int) isset($params['id_guest']) ? $params['id_guest'] : 0;
        $objFeaturePricing->id_room = (int) isset($params['id_room']) ? $params['id_room'] : 0;
        $objFeaturePricing->feature_price_name = $featurePriceName;
        $objFeaturePricing->impact_way = isset($params['impact_way']) ? $params['impact_way'] : HotelRoomTypeFeaturePricing::IMPACT_WAY_FIX_PRICE;
        $objFeaturePricing->impact_type = isset($params['impact_type']) ? $params['impact_type'] : HotelRoomTypeFeaturePricing::IMPACT_TYPE_FIXED_PRICE;
        $objFeaturePricing->impact_value = isset($params['impact_value']) ? $params['impact_value'] : 0;
        $objFeaturePricing->active = isset($params['active']) ? $params['active'] : 1;
        $objFeaturePricing->groupBox = !empty($params['groupBox']) ?  $params['groupBox'] : array_column(Group::getGroups($context->language->id), 'id_group');
        if ($objFeaturePricing->add()) {
            $objFeaturePricing->saveFeaturePriceRestrictions($objFeaturePricing->id, $params['restrictions']);
        }

        return $objFeaturePricing->id;
    }
}
