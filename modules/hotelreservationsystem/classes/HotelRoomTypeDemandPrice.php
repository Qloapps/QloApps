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

class HotelRoomTypeDemandPrice extends ObjectModel
{
    public $id_product;
    public $id_global_demand;
    public $id_option;
    public $price;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_room_type_demand_price',
        'primary' => 'id_room_type_demand_price',
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_global_demand' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_option' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'price' => array('type' => self::TYPE_FLOAT),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

    public function getRoomTypeDemandPrice($idProduct, $idGlobalDemand, $idOption = 0)
    {
        return Db::getInstance()->getValue(
            'SELECT `price` FROM `'._DB_PREFIX_.'htl_room_type_demand_price` rdp
            WHERE `id_product`='.(int)$idProduct.
            ' AND `id_global_demand`='.(int)$idGlobalDemand.
            ' AND `id_option`='.(int)$idOption
        );
    }

    public function getRoomTypeDemandsTotalPrice($idProduct, $roomDemands)
    {
        $totalDemandsPrice = 0;
        if ($roomDemands) {
            $context = Context::getContext();
            if (isset($context->currency->id)
                && Validate::isLoadedObject($context->currency)
            ) {
                $idCurrency = (int)$context->currency->id;
            } else {
                $idCurrency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
            }
            foreach ($roomDemands as $demand) {
                $idGlobalDemand = $demand['id_global_demand'];
                $idOption = $demand['id_option'];
                if ($idOption) {
                    $objOption = new HotelRoomTypeGlobalDemandAdvanceOption($idOption);
                    $priceByRoom = $this->getRoomTypeDemandPrice(
                        $idProduct,
                        $idGlobalDemand,
                        $idOption
                    );
                    if (Validate::isPrice($priceByRoom)) {
                        $totalDemandsPrice += $priceByRoom;
                    } else {
                        $totalDemandsPrice += $objOption->price;
                    }
                } else {
                    $objGlobalDemand = new HotelRoomTypeGlobalDemand($idGlobalDemand);
                    $priceByRoom = $this->getRoomTypeDemandPrice(
                        $idProduct,
                        $idGlobalDemand,
                        $idOption
                    );
                    if (Validate::isPrice($priceByRoom)) {
                        $totalDemandsPrice += $priceByRoom;
                    } else {
                        $totalDemandsPrice += $objGlobalDemand->price;
                    }
                }
            }
        }
        $totalDemandsPrice = Tools::convertPrice(
            $totalDemandsPrice,
            $idCurrency
        );
        return $totalDemandsPrice;
    }

    public function deleteRoomTypeDemandPrices($idProduct = 0, $idGlobalDemand = 0, $idOption = 0)
    {
        $where = '1';
        if ($idProduct) {
            $where .= ' AND `id_product`='.(int)$idProduct;
        }
        if ($idGlobalDemand) {
            $where .= ' AND `id_global_demand`='.(int)$idGlobalDemand;
        }
        if ($idOption) {
            $where .= ' AND `id_option`='.(int)$idOption;
        }
        return Db::getInstance()->delete(
            'htl_room_type_demand_price',
            $where
        );
    }
}

