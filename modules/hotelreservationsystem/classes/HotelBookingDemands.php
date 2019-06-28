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

class HotelBookingDemands extends ObjectModel
{
    public $id_htl_booking;
    public $name;
    public $price;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_booking_demands',
        'primary' => 'id_booking_demands',
        'fields' => array(
            'id_htl_booking' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'price' => array('type' => self::TYPE_FLOAT),
            'name' => array(
                'type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'required' => true, 'size' => 128
            ),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate')
    ));

    public function getRoomTypeBookingExtraDemands(
        $idOrder,
        $idProduct = 0,
        $idRoom = 0,
        $dateFrom = 0,
        $dateTo = 0,
        $groupByRoom = 1,
        $getTotalPrice = 0
    ) {
        $context = Context::getContext();
        if (isset($context->currency->id)
            && Validate::isLoadedObject($context->currency)
        ) {
            $idCurrency = (int)$context->currency->id;
        } else {
            $idCurrency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        }
        $totalDemandsPrice = 0;
        $sql = 'SELECT hb.`id_room`, hd.* FROM `'._DB_PREFIX_.'htl_booking_demands` hd
        LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hb ON (hd.`id_htl_booking` = hb.`id`)
        WHERE hd.`id_htl_booking` IN
        (SELECT `id` FROM `'._DB_PREFIX_.'htl_booking_detail`
        WHERE `id_order`='.(int) $idOrder;
        if ($idProduct) {
            $sql .= ' AND `id_product`='.(int)$idProduct;
        }
        if ($idRoom) {
            $sql .= ' AND `id_room`='.(int)$idRoom;
        }
        if ($dateFrom && $dateTo) {
            $dateFrom = date('Y-m-d', strtotime($dateFrom));
            $dateTo = date('Y-m-d', strtotime($dateTo));
            $sql .= ' AND `date_from`=\''.pSQL($dateFrom).'\' AND `date_to`= \''.pSQL($dateTo).'\'';
        }
        $sql .= ')';
        if ($getTotalPrice) {
            $totalDemandsPrice = 0;
        }
        if ($roomTypeDemands =  Db::getInstance()->executeS($sql)) {
            if ($getTotalPrice) {
                foreach ($roomTypeDemands as $demand) {
                    $totalDemandsPrice += $demand['price'];
                }
            } else {
                if ($groupByRoom) {
                    $roomDemands = array();
                    foreach ($roomTypeDemands as $demand) {
                        $roomDemands[$demand['id_room']]['id_room'] = $demand['id_room'];
                        $roomDemands[$demand['id_room']]['extra_demands'][] = $demand;
                    }
                    return $roomDemands;
                }
            }
        }
        if ($getTotalPrice) {
            // in PS16 prices are not converted after order
            // $totalDemandsPrice = Tools::convertPrice(
            //     $totalDemandsPrice,
            //     $idCurrency
            // );
            return $totalDemandsPrice;
        } else {
            return $roomTypeDemands;
        }
    }
}
