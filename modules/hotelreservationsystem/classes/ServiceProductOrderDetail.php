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

    /**
     * Total service-product revenue (tax excl.) for orders in the date range.
     * Filters by o.invoice_date (same axis as AdminStats getTotalSales).
     * Does NOT include htl_booking_demands — those are excluded going forward.
     *
     * @param array $params date_from, date_to, id_hotel, id_product, id_room, id_order, id_customer
     * @return float
     */
    /**
     * Total service revenue for hotel orders in the date range, or per-line rows when detailed.
     *
     * @param array $params       date_from, date_to, id_hotel, id_product, id_room, id_order, id_customer
     * @param bool  $detailedInfo false → float sum; true → array of per-service-line rows
     * @return float|array
     */
    public static function getTotalServiceRevenue(array $params)
    {
        $dateFrom         = pSQL($params['date_from']);
        $dateTo           = pSQL(isset($params['date_to']) ? $params['date_to'] : $params['date_from']);
        $idHotel          = isset($params['id_hotel'])          ? $params['id_hotel']                : false;
        $idProduct        = isset($params['id_product'])        ? (int) $params['id_product']        : 0;
        $idRoom           = isset($params['id_room'])           ? (int) $params['id_room']           : 0;
        $idOrder          = isset($params['id_order'])          ? (int) $params['id_order']          : 0;
        $idCustomer       = isset($params['id_customer'])       ? (int) $params['id_customer']       : 0;
        $idCategory       = isset($params['id_category'])       ? (int) $params['id_category']       : 0;
        $idServiceProduct = isset($params['id_service_product']) ? (int) $params['id_service_product'] : 0;
        $granularity      = isset($params['granularity'])       ? $params['granularity']             : false;
        $idLang           = isset($params['id_lang'])           ? (int) $params['id_lang']           : 0;
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $baseFrom =
            'FROM `'._DB_PREFIX_.'service_product_order_detail` spod
            LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
                ON (spod.`id_htl_booking_detail` = hbd.`id`)
            LEFT JOIN `'._DB_PREFIX_.'product` p
                ON (p.`id_product` = hbd.`id_product`)
            LEFT JOIN `'._DB_PREFIX_.'orders` o
                ON (o.`id_order` = hbd.`id_order`)';

        $baseWhere =
            'WHERE p.`active` = 1
            AND o.`valid` = 1
            AND hbd.`is_refunded` = 0
            AND spod.`is_cancelled` = 0
            AND o.`invoice_date` BETWEEN "'.$dateFrom.' 00:00:00" AND "'.$dateTo.' 23:59:59"'
            .($idProduct        ? ' AND hbd.`id_product` = '.$idProduct  : '')
            .($idRoom           ? ' AND hbd.`id_room` = '.$idRoom        : '')
            .($idOrder          ? ' AND hbd.`id_order` = '.$idOrder      : '')
            .($idCustomer       ? ' AND hbd.`id_customer` = '.$idCustomer : '')
            .($idServiceProduct ? ' AND spod.`id_product` = '.$idServiceProduct : '')
            .($idCategory       ? ' AND spod.`id_product` IN (SELECT `id_product` FROM `'._DB_PREFIX_.'product` WHERE `id_category_default` = '.$idCategory.')' : '')
            .HotelBranchInformation::addHotelRestriction($idHotel, 'hbd');

        if ($granularity === 'day' || $granularity === 'month') {
            $groupLen = $granularity === 'month' ? 7 : 10;
            $tsSuffix = $granularity === 'month' ? '-01' : '';
            $rows     = Db::getInstance()->executeS(
                'SELECT LEFT(o.`invoice_date`, '.$groupLen.') AS grp,
                IFNULL(SUM(spod.`total_price_tax_excl` / o.`conversion_rate`), 0) AS amt
                '.$baseFrom.' '.$baseWhere.'
                GROUP BY LEFT(o.`invoice_date`, '.$groupLen.')'
            );
            $result = array();
            foreach ($rows as $row) {
                $result[strtotime($row['grp'].$tsSuffix)] = (float) $row['amt'];
            }
            return $result;
        }

        return (float) Db::getInstance()->getValue(
            'SELECT IFNULL(SUM(spod.`total_price_tax_excl` / o.`conversion_rate`), 0)
            '.$baseFrom.' '.$baseWhere
        );
    }

    /**
     * Per-service-line rows for a date range. One row per service order line.
     * Used by services report — returns full detail including customer, category, pricing.
     *
     * @param array $params  date_from, date_to, id_hotel, id_product, id_room, id_order,
     *                       id_customer, id_category, id_service_product, id_lang
     * @return array  rows: id_service_product_order_detail, date_add, id_order, reference,
     *                      customer_name, service_name, service_category, quantity, unit_price,
     *                      hotel_name, room_num, room_type_name, date_from, date_to,
     *                      total_price_tax_excl, tax_amount, total_price_tax_incl
     */
    public static function getServicesInfo(array $params)
    {
        $dateFrom         = pSQL($params['date_from']);
        $dateTo           = pSQL(isset($params['date_to']) ? $params['date_to'] : $params['date_from']);
        $idHotel          = isset($params['id_hotel'])          ? $params['id_hotel']                : false;
        $idProduct        = isset($params['id_product'])        ? (int) $params['id_product']        : 0;
        $idRoom           = isset($params['id_room'])           ? (int) $params['id_room']           : 0;
        $idOrder          = isset($params['id_order'])          ? (int) $params['id_order']          : 0;
        $idCustomer       = isset($params['id_customer'])       ? (int) $params['id_customer']       : 0;
        $idCategory       = isset($params['id_category'])       ? (int) $params['id_category']       : 0;
        $idServiceProduct = isset($params['id_service_product']) ? (int) $params['id_service_product'] : 0;
        $idLang           = isset($params['id_lang'])           ? (int) $params['id_lang']           : 0;
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $baseFrom =
            'FROM `'._DB_PREFIX_.'service_product_order_detail` spod
            LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
                ON (spod.`id_htl_booking_detail` = hbd.`id`)
            LEFT JOIN `'._DB_PREFIX_.'product` p
                ON (p.`id_product` = hbd.`id_product`)
            LEFT JOIN `'._DB_PREFIX_.'orders` o
                ON (o.`id_order` = hbd.`id_order`)';

        $baseWhere =
            'WHERE p.`active` = 1
            AND o.`valid` = 1
            AND hbd.`is_refunded` = 0
            AND spod.`is_cancelled` = 0
            AND o.`invoice_date` BETWEEN "'.$dateFrom.' 00:00:00" AND "'.$dateTo.' 23:59:59"'
            .($idProduct        ? ' AND hbd.`id_product` = '.$idProduct  : '')
            .($idRoom           ? ' AND hbd.`id_room` = '.$idRoom        : '')
            .($idOrder          ? ' AND hbd.`id_order` = '.$idOrder      : '')
            .($idCustomer       ? ' AND hbd.`id_customer` = '.$idCustomer : '')
            .($idServiceProduct ? ' AND spod.`id_product` = '.$idServiceProduct : '')
            .($idCategory       ? ' AND spod.`id_product` IN (SELECT `id_product` FROM `'._DB_PREFIX_.'product` WHERE `id_category_default` = '.$idCategory.')' : '')
            .HotelBranchInformation::addHotelRestriction($idHotel, 'hbd');

        return Db::getInstance()->executeS(
            'SELECT spod.`id_service_product_order_detail`, spod.`date_add`,
            o.`id_order`, o.`reference`,
            CONCAT(c.`firstname`, " ", c.`lastname`) AS customer_name,
            spod.`name` AS service_name,
            IFNULL(cl.`name`, "") AS service_category,
            spod.`quantity`,
            (spod.`unit_price_tax_excl` / o.`conversion_rate`) AS unit_price,
            spod.`hotel_name`, hbd.`room_num`, hbd.`room_type_name`,
            hbd.`date_from`, hbd.`date_to`,
            (spod.`total_price_tax_excl` / o.`conversion_rate`) AS total_price_tax_excl,
            ((spod.`total_price_tax_incl` - spod.`total_price_tax_excl`) / o.`conversion_rate`) AS tax_amount,
            (spod.`total_price_tax_incl` / o.`conversion_rate`) AS total_price_tax_incl
            '.$baseFrom.'
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)
            LEFT JOIN `'._DB_PREFIX_.'product` sp ON (sp.`id_product` = spod.`id_product`)
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
                ON (cl.`id_category` = sp.`id_category_default` AND cl.`id_lang` = '.(int) $idLang.')
            '.$baseWhere.'
            ORDER BY o.`invoice_date` DESC, spod.`id_service_product_order_detail` ASC'
        );
    }

    /**
     * Service-product revenue per day, keyed by Unix timestamp.
     *
     * @param array $params date_from, date_to, id_hotel, id_product, id_room, id_order, id_customer
     * @return array timestamp => float
     */
    public static function getDatewiseServiceRevenue(array $params)
    {
        $dateFrom   = $params['date_from'];
        $dateTo     = isset($params['date_to']) ? $params['date_to'] : $params['date_from'];
        $idHotel    = isset($params['id_hotel'])    ? $params['id_hotel']           : false;
        $idProduct  = isset($params['id_product'])  ? (int) $params['id_product']  : 0;
        $idRoom     = isset($params['id_room'])     ? (int) $params['id_room']     : 0;
        $idOrder    = isset($params['id_order'])    ? (int) $params['id_order']    : 0;
        $idCustomer = isset($params['id_customer']) ? (int) $params['id_customer'] : 0;

        $result  = array();
        $current = $dateFrom;

        while ($current <= $dateTo) {
            $ts       = strtotime($current);
            $cacheKey = 'ServiceProductOrderDetail::getDatewiseServiceRevenue_'.$ts.'_'
                .(is_array($idHotel) ? implode('_', $idHotel) : (int) $idHotel)
                .'_'.$idProduct.'_'.$idRoom;

            if (!Cache::isStored($cacheKey)) {
                $value = Db::getInstance()->getValue(
                    'SELECT IFNULL(SUM(spod.`total_price_tax_excl` / o.`conversion_rate`), 0)
                    FROM `'._DB_PREFIX_.'service_product_order_detail` spod
                    LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
                        ON (spod.`id_htl_booking_detail` = hbd.`id`)
                    LEFT JOIN `'._DB_PREFIX_.'product` p
                        ON (p.`id_product` = hbd.`id_product`)
                    LEFT JOIN `'._DB_PREFIX_.'orders` o
                        ON (o.`id_order` = hbd.`id_order`)
                    WHERE p.`active` = 1
                    AND o.`valid` = 1
                    AND hbd.`is_refunded` = 0
                    AND o.`invoice_date` BETWEEN "'.pSQL($current).' 00:00:00" AND "'.pSQL($current).' 23:59:59"'
                    .($idProduct  ? ' AND hbd.`id_product` = '.$idProduct  : '')
                    .($idRoom     ? ' AND hbd.`id_room` = '.$idRoom        : '')
                    .($idOrder    ? ' AND hbd.`id_order` = '.$idOrder      : '')
                    .($idCustomer ? ' AND hbd.`id_customer` = '.$idCustomer : '')
                    .HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
                );
                Cache::store($cacheKey, (float) $value);
            }

            $result[$ts] = (float) Cache::retrieve($cacheKey);
            $current     = date('Y-m-d', strtotime('+1 day', strtotime($current)));
        }

        return $result;
    }

    /**
     * Service revenue with tax per day, keyed by Unix timestamp.
     * Used by revenue report daily table — needs both excl and tax columns per day.
     *
     * @param array $params  date_from, date_to, id_hotel, id_product, id_room, id_order, id_customer
     * @return array  timestamp => ['service_revenue' => float, 'tax_amount' => float]
     */
    public static function getDatewiseServiceRevenueTax(array $params)
    {
        $dateFrom   = $params['date_from'];
        $dateTo     = isset($params['date_to']) ? $params['date_to'] : $params['date_from'];
        $idHotel    = isset($params['id_hotel'])    ? $params['id_hotel']           : false;
        $idProduct  = isset($params['id_product'])  ? (int) $params['id_product']  : 0;
        $idRoom     = isset($params['id_room'])     ? (int) $params['id_room']     : 0;
        $idOrder    = isset($params['id_order'])    ? (int) $params['id_order']    : 0;
        $idCustomer = isset($params['id_customer']) ? (int) $params['id_customer'] : 0;

        $result  = array();
        $current = $dateFrom;

        $whereFilters =
            ($idProduct  ? ' AND hbd.`id_product` = '.$idProduct  : '')
            .($idRoom    ? ' AND hbd.`id_room` = '.$idRoom        : '')
            .($idOrder   ? ' AND hbd.`id_order` = '.$idOrder      : '')
            .($idCustomer ? ' AND hbd.`id_customer` = '.$idCustomer : '')
            .HotelBranchInformation::addHotelRestriction($idHotel, 'hbd');

        $joins =
            'FROM `'._DB_PREFIX_.'service_product_order_detail` spod
            LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
                ON (spod.`id_htl_booking_detail` = hbd.`id`)
            LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hbd.`id_product`)
            LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = hbd.`id_order`)';

        while ($current <= $dateTo) {
            $next = date('Y-m-d', strtotime('+1 day', strtotime($current)));
            $ts   = strtotime($current);
            $row  = Db::getInstance()->getRow(
                'SELECT
                IFNULL(SUM(spod.`total_price_tax_excl` / o.`conversion_rate`), 0) AS service_revenue,
                IFNULL(SUM((spod.`total_price_tax_incl` - spod.`total_price_tax_excl`) / o.`conversion_rate`), 0) AS tax_amount
                '.$joins.'
                WHERE p.`active` = 1 AND o.`valid` = 1 AND hbd.`is_refunded` = 0
                AND spod.`is_cancelled` = 0
                AND o.`invoice_date` BETWEEN "'.pSQL($current).' 00:00:00" AND "'.pSQL($current).' 23:59:59"'
                .$whereFilters
            );
            $result[$ts] = array(
                'service_revenue' => (float) $row['service_revenue'],
                'tax_amount'      => (float) $row['tax_amount'],
            );
            $current = $next;
        }

        return $result;
    }

    /**
     * Service revenue per night keyed by Unix timestamp, using booking-overlap + proration.
     * Each booking's total service cost is divided equally across its nights.
     * Replaces: AdminStatsController::getServicesRevenueForDiscreteDates() (spod path only — no demands table)
     *
     * @param array $params date_from, date_to, id_hotel
     * @return array timestamp => float
     */
    public static function getServicesRevenueForDiscreteDates(array $params)
    {
        $dateFrom = $params['date_from'];
        $dateTo   = isset($params['date_to']) ? $params['date_to'] : date('Y-m-d', strtotime('+1 day', strtotime($dateFrom)));
        $idHotel  = isset($params['id_hotel']) ? $params['id_hotel'] : null;

        $result  = array();
        $current = $dateFrom;

        while ($current < $dateTo) {
            $next  = date('Y-m-d', strtotime('+1 day', strtotime($current)));
            $ts    = strtotime($current);

            $result[$ts] = (float) Db::getInstance()->getValue(
                'SELECT IFNULL(SUM((spod.`total_price_tax_excl` / o.`conversion_rate`) / DATEDIFF(hbd.`date_to`, hbd.`date_from`)), 0)
                FROM `'._DB_PREFIX_.'service_product_order_detail` spod
                LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
                    ON (spod.`id_htl_booking_detail` = hbd.`id`)
                LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hbd.`id_product`)
                LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = hbd.`id_order`)
                WHERE p.`active` = 1
                AND o.`valid` = 1
                AND hbd.`is_refunded` = 0
                AND hbd.`date_from` < "'.pSQL($next).' 00:00:00"
                AND hbd.`date_to` > "'.pSQL($current).' 00:00:00"'
                .(!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '')
            );

            $current = $next;
        }

        return $result;
    }

    /**
     * Distinct service categories that have at least one active service product.
     *
     * @param int $idLang
     * @return array  rows with id_category, name
     */
    public static function getServiceCategories($idLang = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        return Db::getInstance()->executeS(
            'SELECT DISTINCT c.`id_category`, cl.`name`
            FROM `'._DB_PREFIX_.'product` p
            INNER JOIN `'._DB_PREFIX_.'category` c ON (c.`id_category` = p.`id_category_default`)
            INNER JOIN `'._DB_PREFIX_.'category_lang` cl
                ON (cl.`id_category` = c.`id_category` AND cl.`id_lang` = '.(int) $idLang.')
            WHERE p.`booking_product` = 0 AND p.`active` = 1
            ORDER BY cl.`name` ASC'
        );
    }

    /**
     * Active service products, optionally filtered by category.
     *
     * @param int $idLang
     * @param int $idCategory  0 = all categories
     * @return array  rows with id_product, name
     */
    public static function getServiceProducts($idLang = 0, $idCategory = 0)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }
        return Db::getInstance()->executeS(
            'SELECT p.`id_product`, pl.`name`
            FROM `'._DB_PREFIX_.'product` p
            INNER JOIN `'._DB_PREFIX_.'product_lang` pl
                ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.(int) $idLang.')
            WHERE p.`booking_product` = 0 AND p.`active` = 1'
            .($idCategory ? ' AND p.`id_category_default` = '.(int) $idCategory : '').'
            ORDER BY pl.`name` ASC'
        );
    }

    /**
     * Tax breakdown for service product orders in a date range.
     * Returns one row per service line. Tax amount = total_incl - total_excl (no order_detail_tax join
     * since services use a flat tax difference rather than per-rule breakdown).
     *
     * $params: date_from, date_to, id_hotel, id_lang
     *
     * @param array $params
     * @return array  rows: id_order, reference, customer_name, room_type_name (service name),
     *                      room_num, date_add, taxable_amount, tax_name (NULL), tax_rate (NULL),
     *                      tax_amount, revenue_source ('service')
     */
    /**
     * Prorated service product purchase cost per night across a date range.
     * Uses booking-overlap filter (date_from < next AND date_to > current).
     * Supplier price used when non-zero; otherwise estimated from margin config.
     * Per-booking-calculation-method services are divided by booking length.
     *
     * @param array $params  date_from, date_to, id_hotel
     * @return array  unix_timestamp => float
     */
    public static function getServiceOperatingExpensesForDiscreteDates(array $params)
    {
        $dateFrom = $params['date_from'];
        $dateTo   = isset($params['date_to']) ? $params['date_to'] : date('Y-m-d', strtotime('+1 day', strtotime($dateFrom)));
        $idHotel  = isset($params['id_hotel']) ? $params['id_hotel'] : null;
        $margin   = (int) Configuration::get('CONF_AVERAGE_PRODUCT_MARGIN');

        $result  = array();
        $current = $dateFrom;
        while ($current < $dateTo) {
            $next = date('Y-m-d', strtotime('+1 day', strtotime($current)));
            $ts   = strtotime($current);

            $result[$ts] = (float) Db::getInstance()->getValue(
                'SELECT IFNULL(SUM(
                    IF(od.`purchase_supplier_price` <> "0.000000",
                       od.`purchase_supplier_price`
                           / IF(od.`product_price_calculation_method` = '.Product::PRICE_CALCULATION_METHOD_PER_BOOKING.',
                               DATEDIFF(hbd.`date_to`, hbd.`date_from`), 1),
                       ((od.`original_product_price` / o.`conversion_rate`) * '.$margin.' / 100)
                           / IF(od.`product_price_calculation_method` = '.Product::PRICE_CALCULATION_METHOD_PER_BOOKING.',
                               DATEDIFF(hbd.`date_to`, hbd.`date_from`), 1))
                ), 0)
                FROM `'._DB_PREFIX_.'service_product_order_detail` spod
                LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (spod.`id_htl_booking_detail` = hbd.`id`)
                LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = spod.`id_product`)
                LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = spod.`id_order_detail`)
                LEFT JOIN `'._DB_PREFIX_.'orders` o ON (od.`id_order` = o.`id_order`)
                WHERE p.`active` = 1 AND o.`valid` = 1 AND hbd.`is_refunded` = 0
                AND hbd.`date_from` < "'.pSQL($next).' 00:00:00"
                AND hbd.`date_to`   > "'.pSQL($current).' 00:00:00"'
                .(!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd') : '')
            );

            $current = $next;
        }

        return $result;
    }

    public static function getTaxBreakdown(array $params)
    {
        $dateFrom = pSQL($params['date_from']);
        $dateTo   = pSQL(isset($params['date_to']) ? $params['date_to'] : $params['date_from']);
        $idHotel  = isset($params['id_hotel']) ? $params['id_hotel'] : false;
        $idTax    = isset($params['id_tax'])   ? (int) $params['id_tax'] : 0;
        $idLang   = isset($params['id_lang'])  ? (int) $params['id_lang'] : 0;
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        return Db::getInstance()->executeS(
            'SELECT hbd.`id_order`, o.`reference`,
            CONCAT(c.`firstname`, " ", c.`lastname`) AS customer_name,
            spod.`name` AS room_type_name, hbd.`room_num`, spod.`date_add`,
            spod.`total_price_tax_excl` / o.`conversion_rate` AS taxable_amount,
            MAX(tl.`name`) AS tax_name,
            MAX(t.`rate`) AS tax_rate,
            (spod.`total_price_tax_incl` - spod.`total_price_tax_excl`) / o.`conversion_rate` AS tax_amount,
            "service" AS revenue_source
            FROM `'._DB_PREFIX_.'service_product_order_detail` spod
            INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (hbd.`id` = spod.`id_htl_booking_detail`)
            INNER JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = hbd.`id_order` AND o.`valid` = 1)
            INNER JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = hbd.`id_customer`)
            LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = spod.`id_order_detail`)
            LEFT JOIN `'._DB_PREFIX_.'order_detail_tax` odt ON (odt.`id_order_detail` = od.`id_order_detail`)
            LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = odt.`id_tax`)
            LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (tl.`id_tax` = t.`id_tax` AND tl.`id_lang` = '.(int) $idLang.')
            WHERE spod.`is_cancelled` = 0
            AND hbd.`is_refunded` = 0
            AND (spod.`total_price_tax_incl` - spod.`total_price_tax_excl`) > 0
            AND spod.`date_add` BETWEEN "'.$dateFrom.' 00:00:00" AND "'.$dateTo.' 23:59:59"'
            .($idTax ? ' AND odt.`id_tax` = '.$idTax : '')
            .HotelBranchInformation::addHotelRestriction($idHotel, 'hbd').'
            GROUP BY spod.`id_service_product_order_detail`
            ORDER BY spod.`date_add` ASC'
        );
    }
}
