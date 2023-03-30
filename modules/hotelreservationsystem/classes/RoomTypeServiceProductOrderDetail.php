<?php
/**
* 2010-2022 Webkul.
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
*  @copyright 2010-2022 Webkul IN
*  @license   https://store.webkul.com/license.html
*/


class RoomTypeServiceProductOrderDetail extends ObjectModel
{
    public $id_product;
    public $id_order;
    public $id_order_detail;
    public $id_cart;
    public $id_htl_booking_detail;
    public $unit_price_tax_excl;
    public $unit_price_tax_incl;
    public $total_price_tax_excl;
    public $total_price_tax_incl;
    public $name;
    public $quantity;
    public $auto_added;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'htl_room_type_service_product_order_detail',
        'primary' => 'id_room_type_service_product_order_detail',
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order_detail' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_htl_booking_detail' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'unit_price_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'unit_price_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_price_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_price_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'name' => array('type' => self::TYPE_STRING, 'required' => true),
            'quantity' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'auto_added' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

    public function getroomTypeServiceProducts(
        $idOrder,
        $idProduct = 0,
        $idHotel = 0,
        $roomTypeIdProduct = 0,
        $dateFrom = 0,
        $dateTo = 0,
        $idRoom = 0,
        $getTotalPrice = 0,
        $useTax = null,
        $autoAddToCart = 0,
        $priceAdditionType = null
    ) {

        if ($useTax === null) {
            $useTax = Product::$_taxCalculationMethod == PS_TAX_EXC ? false : true;
        }

        $sql = 'SELECT rsod.*';
        if (!$getTotalPrice) {
            $sql .= ', hbd.`id_product` as `room_type_id_product`, od.`product_allow_multiple_quantity`, hbd.`id_room`, hbd.`adults`, hbd.`children`';
        }
        $sql .= ' FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_service_product_order_detail` rsod ON(rsod.`id_htl_booking_detail` = hbd.`id`)';

        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON(od.`id_order_detail` = rsod.`id_order_detail`)';
        $sql .= ' WHERE rsod.`id_order` = '.(int)$idOrder;

        if (!is_null($autoAddToCart)) {
            $sql .= ' AND od.`product_auto_add` = '. (int)$autoAddToCart;
            if ($autoAddToCart == 1 && !is_null($priceAdditionType)) {
                $sql .= ' AND od.`product_price_addition_type` = '.$priceAdditionType;
            }
        }
        if ($idProduct) {
            $sql .= ' AND rsod.`id_product`='.(int) $idProduct;
        }
        if ($idHotel) {
            $sql .= ' AND hbd.`id_hotel`='.(int) $idHotel;
        }
        if ($roomTypeIdProduct) {
            $sql .= ' AND hbd.`id_product`='.(int) $roomTypeIdProduct;
        }
        if ($dateFrom && $dateTo) {
            $sql .= ' AND hbd.`date_from` = \''.pSQL($dateFrom).'\' AND hbd.`date_to` = \''.pSQL($dateTo).'\'';
        }
        if ($idRoom) {
            $sql .= ' AND hbd.`id_room`='.(int) $idRoom;
        }
        $sql .= ' ORDER BY hbd.`id`';

        if ($getTotalPrice) {
            $totalPrice = 0;
        }

        $selectedAdditionalServices = array();
        if ($additionalServices = Db::getInstance()->executeS($sql)) {
            $moduleObj = Module::getInstanceByName('hotelreservationsystem');
            foreach ($additionalServices as $product) {
                if ($getTotalPrice) {
                    if ($useTax) {
                        $totalPrice += $product['total_price_tax_incl'];
                    } else {
                        $totalPrice += $product['total_price_tax_excl'];
                    }
                } else {

                    $taxes = OrderDetailCore::getTaxListStatic($product['id_order_detail']);
                    $tax_temp = array();
                    foreach ($taxes as $tax) {
                        $obj = new Tax($tax['id_tax']);
                        $tax_temp[] = sprintf($moduleObj->l('%1$s%2$s%%'), ($obj->rate + 0), '&nbsp;');
                    }
                    $product_tax = $taxes;
                    $product_tax_label = implode(', ', $tax_temp);
                    if (isset($selectedAdditionalServices[$product['id_htl_booking_detail']])) {
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['total_price_tax_excl'] += $product['total_price_tax_excl'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['total_price_tax_incl'] += $product['total_price_tax_incl'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['additional_services'][] = array(
                            'id_room_type_service_product_order_detail' => $product['id_room_type_service_product_order_detail'],
                            'id_order_detail' => $product['id_order_detail'],
                            'id_product' => $product['id_product'],
                            'name' => $product['name'],
                            'quantity' => $product['quantity'],
                            'product_tax' => $product_tax,
                            'product_tax_label' => $product_tax_label,
                            'allow_multiple_quantity' => $product['product_allow_multiple_quantity'],
                            'total_price_tax_excl' => $product['total_price_tax_excl'],
                            'total_price_tax_incl' => $product['total_price_tax_incl'],
                        );
                    } else {
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['id_order'] = $product['id_order'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['id_cart'] = $product['id_cart'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['id_htl_booking_detail'] = $product['id_htl_booking_detail'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['adults'] = $product['adults'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['children'] = $product['children'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['total_price_tax_excl'] = $product['total_price_tax_excl'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['total_price_tax_incl'] = $product['total_price_tax_incl'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['room_type_id_product'] = $product['room_type_id_product'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['id_room'] = $product['id_room'];


                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['additional_services'] = array(
                            array(
                                'id_room_type_service_product_order_detail' => $product['id_room_type_service_product_order_detail'],
                                'id_order_detail' => $product['id_order_detail'],
                                'id_product' => $product['id_product'],
                                'name' => $product['name'],
                                'quantity' => $product['quantity'],
                                'allow_multiple_quantity' => $product['product_allow_multiple_quantity'],
                                'product_tax' => $product_tax,
                                'product_tax_label' => $product_tax_label,
                                'total_price_tax_excl' => $product['total_price_tax_excl'],
                                'total_price_tax_incl' => $product['total_price_tax_incl'],
                            ),
                        );
                    }
                }
            }
        }

        if ($getTotalPrice) {
            return $totalPrice;
        }
        return $selectedAdditionalServices;
    }

    public function getSelectedServicesForRoom(
        $idHotelBookingDetail,
        $getTotalPrice = 0,
        $useTax = null
    ) {

        if ($useTax === null) {
            $useTax = Product::$_taxCalculationMethod == PS_TAX_EXC ? false : true;
        }

        $sql = 'SELECT rsod.*';
        if (!$getTotalPrice) {
            $sql .= ', hbd.`id_product` as `room_type_id_product`, hbd.`id_room`, od.`product_allow_multiple_quantity`,
                od.`product_auto_add`, od.`product_price_calculation_method`, od.`product_price_addition_type`';
        }
        $sql .= ' FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            INNER JOIN `'._DB_PREFIX_.'htl_room_type_service_product_order_detail` rsod ON(rsod.`id_htl_booking_detail` = hbd.`id`)';

        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON(od.`id_order_detail` = rsod.`id_order_detail`)';

        $sql .= ' WHERE hbd.`id` = '.(int)$idHotelBookingDetail;

        if ($getTotalPrice) {
            $totalPrice = 0;
        }
        $selectedAdditionalServices = array();
        if ($additionalServices = Db::getInstance()->executeS($sql)) {
            foreach ($additionalServices as $product) {
                if ($getTotalPrice) {
                    if ($useTax) {
                        $totalPrice += $product['total_price_tax_incl'];
                    } else {
                        $totalPrice += $product['total_price_tax_excl'];
                    }
                } else {
                    if (isset($selectedAdditionalServices['additional_services'])) {
                        $selectedAdditionalServices['total_price_tax_excl'] += $product['total_price_tax_excl'];
                        $selectedAdditionalServices['total_price_tax_incl'] += $product['total_price_tax_incl'];
                        $selectedAdditionalServices['additional_services'][] = array(
                            'id_room_type_service_product_order_detail' => $product['id_room_type_service_product_order_detail'],
                            'id_order_detail' => $product['id_order_detail'],
                            'id_product' => $product['id_product'],
                            'name' => $product['name'],
                            'quantity' => $product['quantity'],
                            'allow_multiple_quantity' => $product['product_allow_multiple_quantity'],
                            'product_auto_add' => $product['product_auto_add'],
                            'product_price_addition_type' => $product['product_price_addition_type'],
                            'product_price_calculation_method' => $product['product_price_calculation_method'],
                            'unit_price_tax_excl' => $product['unit_price_tax_excl'],
                            'unit_price_tax_incl' => $product['unit_price_tax_incl'],
                            'total_price_tax_excl' => $product['total_price_tax_excl'],
                            'total_price_tax_incl' => $product['total_price_tax_incl'],
                        );
                    } else {
                        $selectedAdditionalServices['id_order'] = $product['id_order'];
                        $selectedAdditionalServices['id_cart'] = $product['id_cart'];
                        $selectedAdditionalServices['id_htl_booking_detail'] = $product['id_htl_booking_detail'];
                        $selectedAdditionalServices['total_price_tax_excl'] = $product['total_price_tax_excl'];
                        $selectedAdditionalServices['total_price_tax_incl'] = $product['total_price_tax_incl'];
                        $selectedAdditionalServices['room_type_id_product'] = $product['room_type_id_product'];
                        $selectedAdditionalServices['id_room'] = $product['id_room'];
                        $selectedAdditionalServices['additional_services'] = array(
                            array(
                                'id_room_type_service_product_order_detail' => $product['id_room_type_service_product_order_detail'],
                                'id_order_detail' => $product['id_order_detail'],
                                'id_product' => $product['id_product'],
                                'name' => $product['name'],
                                'quantity' => $product['quantity'],
                                'allow_multiple_quantity' => $product['product_allow_multiple_quantity'],
                                'product_auto_add' => $product['product_auto_add'],
                                'product_price_addition_type' => $product['product_price_addition_type'],
                                'product_price_calculation_method' => $product['product_price_calculation_method'],
                                'unit_price_tax_excl' => $product['unit_price_tax_excl'],
                                'unit_price_tax_incl' => $product['unit_price_tax_incl'],
                                'total_price_tax_excl' => $product['total_price_tax_excl'],
                                'total_price_tax_incl' => $product['total_price_tax_incl'],
                            ),
                        );
                    }
                }
            }
        }
        if ($getTotalPrice) {
            return $totalPrice;
        }
        return $selectedAdditionalServices;
    }

    public function deleteRoomSevices($idHotelBookingDetail)
    {
        $services = Db::getInstance()->executeS(
            'SELECT `id_room_type_service_product_order_detail` FROM `'._DB_PREFIX_.'htl_room_type_service_product_order_detail` pod
            WHERE `id_htl_booking_detail` = '.(int)$idHotelBookingDetail
        );
        $res = true;
        foreach ($services as $service) {
            $objServiceProductOrderDetail = new self($service['id_room_type_service_product_order_detail']);
            $res &= $objServiceProductOrderDetail->delete();
        }

        return $res;
    }

    public function delete()
    {
        // delete entry from order detail table.
        return parent::delete();
    }
}