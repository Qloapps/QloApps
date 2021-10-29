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

    public function getRoomTypeDemandsTotalPrice($idProduct, $roomDemands, $useTax = null, $dateFrom = 0, $dateTo = 0)
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
            $objRoomDmdPrice = new HotelRoomTypeDemandPrice();
            if ($useTax === null) {
                $useTax = HotelBookingDetail::useTax();
            }
            $objBookingDetail = new HotelBookingDetail();
            foreach ($roomDemands as $demand) {
                $idGlobalDemand = $demand['id_global_demand'];
                $idOption = $demand['id_option'];
                $objGlobalDemand = new HotelRoomTypeGlobalDemand($idGlobalDemand);
                if ($idOption) {
                    $objOption = new HotelRoomTypeGlobalDemandAdvanceOption($idOption);
                    $price = HotelRoomTypeDemand::getPriceStatic(
                        $idProduct,
                        $idGlobalDemand,
                        $idOption,
                        $useTax
                    );
                } else {
                    $price = HotelRoomTypeDemand::getPriceStatic(
                        $idProduct,
                        $idGlobalDemand,
                        0,
                        $useTax
                    );
                }
                if ($objGlobalDemand->price_calc_method == HotelRoomTypeGlobalDemand::WK_PRICE_CALC_METHOD_EACH_DAY) {
                    if ($dateFrom && $dateTo) {
                        $numDays = $objBookingDetail->getNumberOfDays($dateFrom, $dateTo);
                        if ($numDays > 1) {
                            $price *= $numDays;
                        }
                    }
                }
                $totalDemandsPrice += $price;
            }
        }
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
}

