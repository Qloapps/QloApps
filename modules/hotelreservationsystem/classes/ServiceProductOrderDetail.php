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


class ServiceProductOrderDetail extends ObjectModel
{
    public $id_product;
    public $id_order;
    public $id_order_detail;
    public $id_cart;
    public $id_hotel;
    public $id_htl_booking_detail;
    public $id_product_option;
    public $tax_computation_method;
    public $id_tax_rules_group;
    public $unit_price_tax_excl;
    public $unit_price_tax_incl;
    public $total_price_tax_excl;
    public $total_price_tax_incl;
    public $name;
    public $option_name;
    public $hotel_name;
    public $quantity;
    public $auto_added;
    public $is_refunded;
    public $is_cancelled;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'service_product_order_detail',
        'primary' => 'id_service_product_order_detail',
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order_detail' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_htl_booking_detail' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_product_option' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'tax_computation_method' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_tax_rules_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'unit_price_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'unit_price_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_price_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_price_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'name' => array('type' => self::TYPE_STRING, 'required' => true),
            'option_name' => array('type' => self::TYPE_STRING),
            'hotel_name' => array('type' => self::TYPE_STRING),
            'quantity' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'auto_added' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'is_refunded' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'is_cancelled' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

    public function add($autodate = true, $null_values = true)
    {
        if (Validate::isLoadedObject($objOrder = new Order((int)$this->id_order))
            && Validate::isLoadedObject($objServiceProduct = new Product((int)$this->id_product))
            && Validate::isLoadedObject($objOrderDetail = new OrderDetail((int)$this->id_order_detail))
        ) {
            if ($objOrderDetail->selling_preference_type == Product::SELLING_PREFERENCE_WITH_ROOM_TYPE) {
                if ($this->id_htl_booking_detail
                    && Validate::isLoadedObject($objHotelBookingDetail = new HotelBookingDetail((int)$this->id_htl_booking_detail))
                ) {
                    $idRoomType = $objHotelBookingDetail->id_product;
                    $objAddress = new Address((int)$objOrder->id_address_tax);
                    if ($objServiceProduct->auto_add_to_cart && $objServiceProduct->price_addition_type == Product::PRICE_ADDITION_TYPE_WITH_ROOM) {
                        if (Validate::isLoadedObject($objRoomTypeProduct = new Product((int)$idRoomType))) {
                            $this->id_tax_rules_group = $objRoomTypeProduct->id_tax_rules_group;
                        }
                    } else {
                        $objRoomTypeServiceProductPrice = new RoomTypeServiceProductPrice();
                        if ($serviceProductPriceRoomInfo = $objRoomTypeServiceProductPrice->getProductRoomTypeLinkPriceInfo(
                            $this->id_product,
                            $idRoomType,
                            RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE
                        )) {
                            //Special tax rule group for the Service product accroding to Room type
                            $this->id_tax_rules_group = $serviceProductPriceRoomInfo['id_tax_rules_group'];
                        } else {
                            // Use default tax rule group for the service product
                            $this->id_tax_rules_group = $objOrderDetail->id_tax_rules_group;
                        }
                    }

                    $taxCalculator = TaxManagerFactory::getManager($objAddress, $this->id_tax_rules_group)->getTaxCalculator();
                    $this->tax_computation_method = (int)$taxCalculator->computation_method;
                }
            } else {
                // Use default tax rule group for the service product
                $this->id_tax_rules_group = $objOrderDetail->id_tax_rules_group;
                $this->tax_computation_method = $objOrderDetail->tax_computation_method;
            }

            return parent::add($autodate, $null_values);
        }

        return false;
    }

    public function getServiceProductsInOrder(
        $idOrder,
        $idOrderDetail = 0,
        $idProduct = 0,
        $sellingPreferenceType = 0
    ) {
        $sql = 'SELECT spo.* FROM `'._DB_PREFIX_.'service_product_order_detail` spo';

        if ($sellingPreferenceType) {
            $sql .= ' INNER JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = spo.`id_order_detail` AND od.`id_order` = '.(int)$idOrder.')';
        }

        $sql .= ' WHERE 1 AND spo.`id_order` = '.(int)$idOrder;

        if ($idOrderDetail) {
            $sql .= ' AND spo.`id_order_detail` = '.(int)$idOrderDetail;
        }

        if ($idProduct) {
            $sql .= ' AND spo.`id_product` = '.(int)$idProduct;
        }

        if ($sellingPreferenceType) {
            $sql .= ' AND od.`selling_preference_type` = '.(int)$sellingPreferenceType;
        }

        if ($products = Db::getInstance()->executeS($sql)) {
            $objContext = Context::getContext();
            $defaultImageLink = $objContext->link->getImageLink('', $objContext->language->iso_code.'-default', 'small_default');
            foreach ($products as $key => $product) {
                // Check if this booking as any refund history then enter refund data
                if ($refundInfo = OrderReturn::getOrdersReturnDetail($idOrder, 0, 0, $product['id_service_product_order_detail'])) {
                    $products[$key]['refund_info'] = reset($refundInfo);
                }

                $products[$key]['cover_image'] = $defaultImageLink;
                $products[$key]['allow_multiple_quantity'] = 0;
                if (Validate::isLoadedObject($objProduct = new Product((int) $product['id_product'], Configuration::get('PS_LANG_DEFAULT')))) {
                    $products[$key]['allow_multiple_quantity'] = $objProduct->allow_multiple_quantity;
                    if ($productCoverImg = Product::getCover($product['id_product'])) {
                        $products[$key]['cover_image'] = $objContext->link->getImageLink(
                            $objProduct->link_rewrite[Configuration::get('PS_LANG_DEFAULT')],
                            $productCoverImg['id_image'], 'small_default'
                        );
                    }
                }
            }
        }

        return $products;
    }

    public function getRoomTypeServiceProducts(
        $idOrder = 0,
        $idProduct = 0,
        $idHotel = 0,
        $roomTypeIdProduct = 0,
        $dateFrom = 0,
        $dateTo = 0,
        $idRoom = 0,
        $getTotalPrice = 0,
        $useTax = null,
        $autoAddToCart = null,
        $priceAdditionType = null,
        $idOrderDetail = 0,
        $idHtlBookingDetail = 0
    ) {
        if ($useTax === null) {
            $useTax = Product::$_taxCalculationMethod == PS_TAX_EXC ? false : true;
        }

        $sql = 'SELECT spod.*';
        if (!$getTotalPrice) {
            $sql .= ', hbd.`id_product` as `id_room_type`, od.`product_price_calculation_method`,
            hbd.`id_room`, hbd.`adults`, hbd.`children`, hbd.`date_from`, hbd.`date_to`, hbd.`room_type_name`, p.`max_quantity`,
            spod.`id_product` as id_product,  od.`product_allow_multiple_quantity`, od.`product_price_calculation_method`, od.`product_auto_add`, od.`product_price_addition_type`, IF(p.`id_product`, 0, 1) as `product_deleted`';
        }
        $sql .= ' FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            LEFT JOIN `'._DB_PREFIX_.'service_product_order_detail` spod ON(spod.`id_htl_booking_detail` = hbd.`id`)';

        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON(od.`id_order_detail` = spod.`id_order_detail`)';
        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'product` p ON(spod.`id_product` = p.`id_product`)';
        $sql .= ' WHERE spod.`id_htl_booking_detail` IS NOT NULL';

        if ($idOrder) {
            $sql .= ' AND spod.`id_order` = '.(int)$idOrder;
        }

        if ($idOrderDetail) {
            $sql .= ' AND spod.`id_order_detail` = '.(int)$idOrderDetail;
        }

        if (!is_null($autoAddToCart)) {
            $sql .= ' AND od.`product_auto_add` = '. (int)$autoAddToCart;
            if ($autoAddToCart == 1 && !is_null($priceAdditionType)) {
                $sql .= ' AND od.`product_price_addition_type` = '.$priceAdditionType;
            }
        }
        if ($idProduct) {
            $sql .= ' AND spod.`id_product`='.(int) $idProduct;
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
        if ($idHtlBookingDetail) {
            $sql .= ' AND hbd.`id` = '.(int)$idHtlBookingDetail;
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
                            'id_service_product_order_detail' => $product['id_service_product_order_detail'],
                            'id_order_detail' => $product['id_order_detail'],
                            'id_product' => $product['id_product'],
                            'name' => $product['name'],
                            'quantity' => $product['quantity'],
                            'product_tax' => $product_tax,
                            'product_tax_label' => $product_tax_label,
                            'allow_multiple_quantity' => $product['product_allow_multiple_quantity'],
                            'tax_computation_method' => $product['tax_computation_method'],
                            'id_tax_rules_group' => $product['id_tax_rules_group'],
                            'price_calculation_method' => $product['product_price_calculation_method'],
                            'total_price_tax_excl' => $product['total_price_tax_excl'],
                            'total_price_tax_incl' => $product['total_price_tax_incl'],
                            'unit_price_tax_excl' => $product['unit_price_tax_excl'],
                            'unit_price_tax_incl' => $product['unit_price_tax_incl'],
                            'product_auto_add' => $product['product_auto_add'],
                            'product_price_addition_type' => $product['product_price_addition_type'],
                            'max_quantity' => (int) $product['max_quantity'],
                            'product_deleted' => $product['product_deleted']
                        );
                    } else {
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['id_order'] = $product['id_order'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['id_cart'] = $product['id_cart'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['id_htl_booking_detail'] = $product['id_htl_booking_detail'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['adults'] = $product['adults'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['children'] = $product['children'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['total_price_tax_excl'] = $product['total_price_tax_excl'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['total_price_tax_incl'] = $product['total_price_tax_incl'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['id_room_type'] = $product['id_room_type'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['id_room'] = $product['id_room'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['date_from'] = $product['date_from'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['date_to'] = $product['date_to'];
                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['room_type_name'] = $product['room_type_name'];

                        $selectedAdditionalServices[$product['id_htl_booking_detail']]['additional_services'] = array(
                            array(
                                'id_service_product_order_detail' => $product['id_service_product_order_detail'],
                                'id_order_detail' => $product['id_order_detail'],
                                'id_product' => $product['id_product'],
                                'name' => $product['name'],
                                'quantity' => $product['quantity'],
                                'allow_multiple_quantity' => $product['product_allow_multiple_quantity'],
                                'tax_computation_method' => $product['tax_computation_method'],
                                'id_tax_rules_group' => $product['id_tax_rules_group'],
                                'price_calculation_method' => $product['product_price_calculation_method'],
                                'product_tax' => $product_tax,
                                'product_tax_label' => $product_tax_label,
                                'total_price_tax_excl' => $product['total_price_tax_excl'],
                                'total_price_tax_incl' => $product['total_price_tax_incl'],
                                'unit_price_tax_excl' => $product['unit_price_tax_excl'],
                                'unit_price_tax_incl' => $product['unit_price_tax_incl'],
                                'product_auto_add' => $product['product_auto_add'],
                                'product_price_addition_type' => $product['product_price_addition_type'],
                                'max_quantity' => (int) $product['max_quantity'],
                                'product_deleted' => $product['product_deleted'],
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
        $useTax = null,
        $autoAddToCart = null,
        $priceAdditionType = null
    ) {

        if ($useTax === null) {
            $useTax = Product::$_taxCalculationMethod == PS_TAX_EXC ? false : true;
        }

        $sql = 'SELECT spod.*';
        if (!$getTotalPrice) {
            $sql .= ', hbd.`id_product` as `room_type_id_product`, hbd.`id_room`, od.`product_allow_multiple_quantity`, p.`max_quantity`,
                od.`product_auto_add`, od.`product_price_calculation_method`, od.`product_price_addition_type`';
        }
        $sql .= ' FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            INNER JOIN `'._DB_PREFIX_.'service_product_order_detail` spod ON(spod.`id_htl_booking_detail` = hbd.`id`)';

        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON(od.`id_order_detail` = spod.`id_order_detail`)';
        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = spod.`id_product`)';

        $sql .= ' WHERE hbd.`id` = '.(int)$idHotelBookingDetail;

        if (!is_null($autoAddToCart)) {
            $sql .= ' AND od.`product_auto_add` = '. (int)$autoAddToCart;
            if ($autoAddToCart == 1 && !is_null($priceAdditionType)) {
                $sql .= ' AND od.`product_price_addition_type` = '.$priceAdditionType;
            }
        }

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
                    $taxes = OrderDetailCore::getTaxListStatic($product['id_order_detail']);
                    $tax_temp = array();
                    foreach ($taxes as $tax) {
                        $obj = new Tax($tax['id_tax']);
                        $tax_temp[] = sprintf('%1$s%2$s%%', ($obj->rate + 0), '&nbsp;');
                    }
                    $product_tax_label = implode(', ', $tax_temp);
                    if (isset($selectedAdditionalServices['additional_services'])) {
                        $selectedAdditionalServices['total_price_tax_excl'] += $product['total_price_tax_excl'];
                        $selectedAdditionalServices['total_price_tax_incl'] += $product['total_price_tax_incl'];
                        $selectedAdditionalServices['additional_services'][] = array(
                            'id_service_product_order_detail' => $product['id_service_product_order_detail'],
                            'id_order_detail' => $product['id_order_detail'],
                            'id_product' => $product['id_product'],
                            'name' => $product['name'],
                            'id_cart' => $product['id_cart'],
                            'quantity' => $product['quantity'],
                            'allow_multiple_quantity' => $product['product_allow_multiple_quantity'],
                            'max_quantity' => $product['max_quantity'],
                            'product_auto_add' => $product['product_auto_add'],
                            'product_price_addition_type' => $product['product_price_addition_type'],
                            'price_calculation_method' => $product['product_price_calculation_method'],
                            'unit_price_tax_excl' => $product['unit_price_tax_excl'],
                            'unit_price_tax_incl' => $product['unit_price_tax_incl'],
                            'total_price_tax_excl' => $product['total_price_tax_excl'],
                            'total_price_tax_incl' => $product['total_price_tax_incl'],
                            'product_tax_label' => $product_tax_label,
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
                                'id_service_product_order_detail' => $product['id_service_product_order_detail'],
                                'id_order_detail' => $product['id_order_detail'],
                                'id_product' => $product['id_product'],
                                'name' => $product['name'],
                                'id_cart' => $product['id_cart'],
                                'quantity' => $product['quantity'],
                                'allow_multiple_quantity' => $product['product_allow_multiple_quantity'],
                                'max_quantity' => $product['max_quantity'],
                                'product_auto_add' => $product['product_auto_add'],
                                'product_price_addition_type' => $product['product_price_addition_type'],
                                'price_calculation_method' => $product['product_price_calculation_method'],
                                'unit_price_tax_excl' => $product['unit_price_tax_excl'],
                                'unit_price_tax_incl' => $product['unit_price_tax_incl'],
                                'total_price_tax_excl' => $product['total_price_tax_excl'],
                                'total_price_tax_incl' => $product['total_price_tax_incl'],
                                'product_tax_label' => $product_tax_label,
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

    public function deleteSeviceProducts(
        $idOrder = 0,
        $idHotelBookingDetail = 0,
        $idProduct = 0,
        $idProductOption = 0
    ) {
        $sql = 'SELECT `id_service_product_order_detail` FROM `'._DB_PREFIX_.'service_product_order_detail` WHERE 1';

        if ($idOrder) {
            $sql .= ' AND `id_order` = '.(int)$idOrder;
        }
        if ($idHotelBookingDetail) {
            $sql .= ' AND `id_htl_booking_detail` = '.(int)$idHotelBookingDetail;
        }
        if ($idProduct) {
            $sql .= ' AND `id_product` = '.(int)$idProduct;
        }
        if ($idProductOption) {
            $sql .= ' AND `id_product_option` = '.(int)$idProductOption;
        }

        $result = true;
        if ($services = Db::getInstance()->executeS($sql)) {
            foreach ($services as $service) {
                $objServiceProductOrderDetail = new self($service['id_service_product_order_detail']);
                $result &= $objServiceProductOrderDetail->delete();
            }
        }

        return $result;
    }

    // process the tables changes when a product refund/cancellation is processed
    public function processRefundInTables()
    {
        if (Validate::isLoadedObject($this)) {
            $reduction_amount = array(
                'total_price_tax_excl' => 0,
                'total_price_tax_incl' => 0,
                'total_products_tax_excl' => 0,
                'total_products_tax_incl' => 0,
            );
            $objOrder = new Order($this->id_order);
            $orderTotalPaid = $objOrder->getTotalPaid();
            $orderDiscounts = $objOrder->getCartRules();

            $hasOrderDiscountOrPayment = ((float)$orderTotalPaid > 0 || $orderDiscounts) ? true : false;

            // things to do if order is not paid
            if (!$hasOrderDiscountOrPayment) {
                $objHotelBookingDemands = new HotelBookingDemands();
                $objServiceProductOrderDetail = new ServiceProductOrderDetail();

                $reduction_amount['total_price_tax_excl'] = (float) $this->total_price_tax_excl;
                $reduction_amount['total_products_tax_excl'] = (float) $this->total_price_tax_excl;
                $reduction_amount['total_price_tax_incl'] = (float) $this->total_price_tax_incl;
                $reduction_amount['total_products_tax_incl'] = (float) $this->total_price_tax_incl;
            }

            // enter refunded quantity in the order detail table
            $idOrderDetail = $this->id_order_detail;
            if (Validate::isLoadedObject($objOrderDetail = new OrderDetail($idOrderDetail))) {

                $objOrderDetail->product_quantity_refunded += $this->quantity;
                if ($objOrderDetail->product_quantity_refunded > $objOrderDetail->product_quantity) {
                    $objOrderDetail->product_quantity_refunded = $objOrderDetail->product_quantity;
                }

                if (!$hasOrderDiscountOrPayment) {
                    // reduce room amount from order and order detail
                    $objOrderDetail->total_price_tax_incl -= Tools::processPriceRounding(
                        $this->total_price_tax_incl,
                        1,
                        $objOrder->round_type,
                        $objOrder->round_mode
                    );

                    $objOrderDetail->total_price_tax_excl -= Tools::processPriceRounding(
                        $this->total_price_tax_excl,
                        1,
                        $objOrder->round_type,
                        $objOrder->round_mode
                    );

                    if (Validate::isLoadedObject($objOrder = new Order($this->id_order))) {
                        $objOrder->total_paid = Tools::ps_round(
                            ($objOrder->total_paid - $reduction_amount['total_price_tax_incl']),
                            _PS_PRICE_COMPUTE_PRECISION_
                        );
                        $objOrder->total_paid = $objOrder->total_paid > 0 ? $objOrder->total_paid : 0;

                        $objOrder->total_paid_tax_excl = Tools::ps_round(($objOrder->total_paid_tax_excl - $reduction_amount['total_price_tax_excl']),
                            _PS_PRICE_COMPUTE_PRECISION_
                        );
                        $objOrder->total_paid_tax_excl = $objOrder->total_paid_tax_excl > 0 ? $objOrder->total_paid_tax_excl : 0;

                        $objOrder->total_paid_tax_incl = Tools::ps_round(($objOrder->total_paid_tax_incl - $reduction_amount['total_price_tax_incl']),
                            _PS_PRICE_COMPUTE_PRECISION_
                        );
                        $objOrder->total_paid_tax_incl = $objOrder->total_paid_tax_incl > 0 ? $objOrder->total_paid_tax_incl : 0;

                        $objOrder->total_products = Tools::ps_round(($objOrder->total_products - $reduction_amount['total_products_tax_excl']),
                            _PS_PRICE_COMPUTE_PRECISION_
                        );
                        $objOrder->total_products = $objOrder->total_products > 0 ? $objOrder->total_products : 0;

                        $objOrder->total_products_wt = Tools::ps_round(($objOrder->total_products_wt - $reduction_amount['total_products_tax_incl']),
                            _PS_PRICE_COMPUTE_PRECISION_
                        );
                        $objOrder->total_products_wt = $objOrder->total_products_wt > 0 ? $objOrder->total_products_wt : 0;

                        $objOrder->save();
                    }
                }

                $objOrderDetail->save();
            }

            // as refund is completed then set the booking as refunded
            $this->is_refunded = 1;
            if (!$hasOrderDiscountOrPayment) {
                // Reduce room amount from htl_booking_detail
                $this->is_cancelled = 1;
                $this->total_price_tax_excl = 0;
                $this->total_price_tax_incl = 0;
            }

            $this->save();

            return true;
        }

        return false;
    }
}
