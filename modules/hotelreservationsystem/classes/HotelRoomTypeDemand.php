<?php
/**
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class HotelRoomTypeDemand extends ObjectModel
{
    public $id_product;
    public $id_global_demand;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_room_type_demand',
        'primary' => 'id_room_type_demand',
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_global_demand' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

    public function getRoomTypeDemands($idProduct, $idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        $roomTypeDemandInfo = array();
        if ($roomTypeDemands = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_room_type_demand` rd
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_global_demand` rgd
            ON (rd.`id_global_demand` = rgd.`id_global_demand`)
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_global_demand_lang` rgdl
            ON (rgd.`id_global_demand` = rgdl.`id_global_demand` AND rgdl.`id_lang` = '.(int)$idLang.')
            WHERE rd.`id_product`='.(int)$idProduct
        )) {
            $objAdvOption = new HotelRoomTypeGlobalDemandAdvanceOption();
            $objRoomDemandPrice = new HotelRoomTypeDemandPrice();
            $context = Context::getContext();
            if (isset($context->currency->id)
                && Validate::isLoadedObject($context->currency)
            ) {
                $idCurrency = (int)$context->currency->id;
            } else {
                $idCurrency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
            }
            foreach ($roomTypeDemands as &$demand) {
                $idGlobalDemand = $demand['id_global_demand'];
                $roomTypeDemandInfo[$idGlobalDemand]['name'] = $demand['name'];
                $priceByRoom = $objRoomDemandPrice->getRoomTypeDemandPrice($idProduct, $idGlobalDemand);
                if (Validate::isPrice($priceByRoom)) {
                    $roomTypeDemandInfo[$idGlobalDemand]['price'] = $priceByRoom;
                } else {
                    $roomTypeDemandInfo[$idGlobalDemand]['price'] = $demand['price'];
                }
                $roomTypeDemandInfo[$idGlobalDemand]['price'] = Tools::convertPrice(
                    $roomTypeDemandInfo[$idGlobalDemand]['price'],
                    $idCurrency
                );
                if ($advOptions = $objAdvOption->getGlobalDemandAdvanceOptions($idGlobalDemand, $idLang)) {
                    foreach ($advOptions as &$option) {
                        $idOption = $option['id'];
                        $priceByRoom = $objRoomDemandPrice->getRoomTypeDemandPrice($idProduct, $idGlobalDemand, $idOption);
                        if (Validate::isPrice($priceByRoom)) {
                            $roomTypeDemandInfo[$idGlobalDemand]['adv_option'][$idOption]['price'] = $priceByRoom;
                        } else {
                            $roomTypeDemandInfo[$idGlobalDemand]['adv_option'][$idOption]['price'] = $option['price'];
                        }
                        $roomTypeDemandInfo[$idGlobalDemand]['adv_option'][$idOption]['price'] = Tools::convertPrice(
                            $roomTypeDemandInfo[$idGlobalDemand]['adv_option'][$idOption]['price'],
                            $idCurrency
                        );
                        $roomTypeDemandInfo[$idGlobalDemand]['adv_option'][$idOption]['name'] = $option['name'];
                    }
                }
            }
            return $roomTypeDemandInfo;
        }
        return false;
    }

    public function deleteRoomTypeDemands($idProduct = 0, $idGlobalDemand = 0)
    {
        $where = '1';
        if ($idProduct) {
            $where .= ' AND `id_product`='.(int)$idProduct;
        }
        if ($idGlobalDemand) {
            $where .= ' AND `id_global_demand`='.(int)$idGlobalDemand;
        }
        return Db::getInstance()->delete(
            'htl_room_type_demand',
            $where
        );
    }
}

