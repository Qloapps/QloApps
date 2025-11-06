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

class HotelRoomTypeFeaturePricingRestriction extends ObjectModel
{
    public $id_feature_price;
    public $date_selection_type;
    public $date_from;
    public $date_to;
    public $is_special_days_exists;
    public $special_days;
    public $date_add;
    public $date_upd;
    public static $definition = array(
        'table' => 'htl_room_type_feature_pricing_restriction',
        'primary' => 'id_feature_price_restriction',
        'multilang' => false,
        'fields' => array(
            'id_feature_price' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'date_from' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'date_to' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'is_special_days_exists' => array('type' => self::TYPE_INT, 'required' => true),
            'date_selection_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'special_days' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate')
        )
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'restrictions',
        'objectNodeName' => 'restriction',
        'fields' => array(
            'id_feature_price' => array(
                'xlink_resource' => array(
                    'resourceName' => 'feature_prices',
                )
            ),
        )
    );

    public function getRestrictionsByIdFeaturePrice($idFeaturePrice)
    {
        $sql = 'SELECT *, id_feature_price_restriction AS `id` FROM `'._DB_PREFIX_.$this->table.'`
            WHERE `id_feature_price` ='.(int) $idFeaturePrice;

        return Db::getInstance()->executeS($sql);
    }

    public function deleteFeaturePriceRestrictionsById($featurePriceRules)
    {
        $res = true;
        if ($featurePriceRules) {
            foreach ($featurePriceRules as $idFeaturePriceRules) {
                $objFeaturePriceRule = new HotelRoomTypeFeaturePricingRestriction($idFeaturePriceRules);
                $res &= $objFeaturePriceRule->delete();
            }
        }

        return $res;
    }

}
