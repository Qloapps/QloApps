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

class HotelRoomTypeGlobalDemand extends ObjectModel
{
    public $name;
    public $price;
    public $id_tax_rules_group;
    public $price_calc_method;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_room_type_global_demand',
        'primary' => 'id_global_demand',
        'multilang' => true,
        'fields' => array(
            'price' => array('type' => self::TYPE_FLOAT),
            'id_tax_rules_group' => array('type' => self::TYPE_INT),
            'price_calc_method' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            //lang fields
            'name' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isCatalogName',
                'required' => true,
                'size' => 128
            ),
    ));

    protected $webserviceParameters = array(
        'objectsNodeName' => 'extra_demands',
        'objectNodeName' => 'extra_demand',
        'fields' => array(
            'id_tax_rules_group' => array(
                'xlink_resource' => array(
                    'resourceName' => 'tax_rule_groups'
                )
            ),
        ),
        'associations' => array(
            'demand_advance_options' => array(
                'resource' => 'advance_option',
                'setter' => false,
                'fields' => array(
                    'id' => array('required' => true),
                )
            ),
        ),
    );

    const WK_PRICE_CALC_METHOD_EACH_DAY = 1;
    const WK_PRICE_CALC_METHOD_RANGE = 0;

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
    }

    public function delete()
    {
        // delete advance options of the global demands
        $objAdvOption = new HotelRoomTypeGlobalDemandAdvanceOption();
        if ($advOptions = $objAdvOption->getGlobalDemandAdvanceOptions($this->id)) {
            foreach ($advOptions as $option) {
                $objAdvOption = new HotelRoomTypeGlobalDemandAdvanceOption($option['id']);
                $objAdvOption->delete();
            }
        }
        // delete the global demands from cart
        $objCartBookingData = new HotelCartBookingData();
        if ($cartExtraDemands = $objCartBookingData->getCartExtraDemands()) {
            foreach ($cartExtraDemands as &$demandInfo) {
                if (isset($demandInfo['extra_demands']) && $demandInfo['extra_demands']) {
                    $cartChanged = 0;
                    foreach ($demandInfo['extra_demands'] as $key => $demand) {
                        if ($this->id == $demand['id_global_demand']) {
                            $cartChanged = 1;
                            unset($demandInfo['extra_demands'][$key]);
                        }
                    }
                    if ($cartChanged) {
                        if (Validate::isLoadedObject($objCartBooking = new HotelCartBookingData($demandInfo['id']))) {
                            $objCartBooking->extra_demands = json_encode($demandInfo['extra_demands']);
                            $objCartBooking->save();
                        }
                    }
                }
            }
        }

        // delete the info from room type demands table
        $objRoomTypeDemand = new HotelRoomTypeDemand();
        $objRoomTypeDemand->deleteRoomTypeDemands(0, $this->id);
        $objRoomTypeDemandPrice = new HotelRoomTypeDemandPrice();
        $objRoomTypeDemandPrice->deleteRoomTypeDemandPrices(0, $this->id);
        return parent::delete();
    }

    public function getAllDemands($idLang = null)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'htl_room_type_global_demand` has
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_global_demand_lang` hasl
            ON (has.`id_global_demand` = hasl.`id_global_demand` AND hasl.`id_lang` = '.(int)$idLang.')';
        if ($demands = Db::getInstance()->executeS($sql)) {
            $objAdvOption = new HotelRoomTypeGlobalDemandAdvanceOption();
            foreach ($demands as &$demand) {
                $demand['adv_option'] = array();
                if ($advOptions = $objAdvOption->getGlobalDemandAdvanceOptions($demand['id_global_demand'], $idLang)) {
                    $demand['adv_option'] = $advOptions;
                }
            }
        }
        return $demands;
    }

    public static function getIdTaxRulesGroupByIdGlobalDemanu($idGlobalDemand)
    {
        $context = Context::getContext();
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT `id_tax_rules_group` FROM `'._DB_PREFIX_.'htl_room_type_global_demand`
            WHERE `id_global_demand` = '.(int)$idGlobalDemand
        );
    }

    // Webservice :: get advance options of the global demand
    public function getWsDemandAdvanceOptions()
    {
        return Db::getInstance()->executeS(
            'SELECT `id_option` as `id` FROM `'._DB_PREFIX_.'htl_room_type_global_demand_advance_option` WHERE `id_global_demand` = '.(int)$this->id.' ORDER BY `id` ASC'
        );
    }
}
