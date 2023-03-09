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

class RoomTypeServiceProductCartDetail extends ObjectModel
{
    public $id_product;
    public $quantity;
    public $id_cart;
    public $htl_cart_booking_id;

    public static $definition = array(
        'table' => 'htl_room_type_service_product_cart_detail',
        'primary' => 'id_room_type_service_product_cart_detail',
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'quantity' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'htl_cart_booking_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
        ),
    );

    public function alreadyExists(
        $idProduct,
        $idHtlCartData
    ) {
        return Db::getInstance()->getValue(
            'SELECT `id_room_type_service_product_cart_detail` FROM `'._DB_PREFIX_.'htl_room_type_service_product_cart_detail`
            WHERE `id_product` = '.(int)$idProduct.' AND `htl_cart_booking_id` = '.(int)$idHtlCartData
        );
    }

    public function addServiceProductInCart(
        $idProduct,
        $quantity,
        $idCart,
        $idHtlCartData
    ) {
        if ($id_room_type_service_product_cart_detail = $this->alreadyExists($idProduct, $idHtlCartData)) {
            $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail($id_room_type_service_product_cart_detail);
        } else {
            $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail();
            $objRoomTypeServiceProductCartDetail->id_product = $idProduct;
        }
        $objRoomTypeServiceProductCartDetail->quantity = $quantity;
        $objRoomTypeServiceProductCartDetail->id_cart = $idCart;
        $objRoomTypeServiceProductCartDetail->htl_cart_booking_id = $idHtlCartData;
        if ($objRoomTypeServiceProductCartDetail->save()) {
            $objCart = new Cart($idCart);
            return $objCart->updateQty((int)($quantity), $idProduct);
        }

        return false;
    }

    public function removeServiceProductByIdHtlCartBooking($htlCartBookingId)
    {
        if ($stadardProductsData = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_room_type_service_product_cart_detail`
            WHERE `htl_cart_booking_id` = '.(int)$htlCartBookingId
        )) {
            foreach ($stadardProductsData as $product) {
                if (Validate::isLoadedObject(
                    $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail($product['id_room_type_service_product_cart_detail'])
                )) {
                    if ($objRoomTypeServiceProductCartDetail->delete()) {
                        $objCart = new Cart($product['id_cart']);
                        $objCart->updateQty((int)($product['quantity']), $product['id_product'], null, false, 'down');
                    }
                }
            }
        }

        return true;
    }

    public function getServiceProductsTotalInCart(
        $idCart,
        $idProduct = 0,
        $idHotel = 0,
        $roomTypeIdProduct = 0,
        $dateFrom = 0,
        $dateTo = 0,
        $htlCartBookingId = 0,
        $useTax = null,
        $autoAddToCart = 0,
        $id_address = null,
        $priceAdditionType = null
    ) {
        if ($useTax === null)
            $useTax = Product::$_taxCalculationMethod == PS_TAX_EXC ? false : true;

        $sql = 'SELECT rscd.`id_product`, rscd.`quantity`, cbd.`id_product` as `room_type_id_product`
            FROM `'._DB_PREFIX_.'htl_cart_booking_data` cbd
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_service_product_cart_detail` rscd ON(rscd.`htl_cart_booking_id` = cbd.`id`)
            LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = rscd.`id_product`)
            WHERE 1';

        if (!is_null($autoAddToCart)) {
            $sql .= ' AND p.`auto_add_to_cart` = '. (int)$autoAddToCart;
            if ($autoAddToCart == 1 && !is_null($priceAdditionType)) {
                $sql .= ' AND p.`price_addition_type` = '.$priceAdditionType;
            }
        }
        if ($idCart) {
            $sql .= ' AND cbd.`id_cart`='.(int) $idCart;
        }
        if ($idProduct) {
            $sql .= ' AND rscd.`id_product`='.(int) $idProduct;
        }
        if ($idHotel) {
            $sql .= ' AND cbd.`id_hotel`='.(int) $idHotel;
        }
        if ($roomTypeIdProduct) {
            $sql .= ' AND cbd.`id_product`='.(int) $roomTypeIdProduct;
        }
        if ($dateFrom && $dateTo) {
            $sql .= ' AND cbd.`date_from` = \''.pSQL($dateFrom).'\' AND cbd.`date_to` = \''.pSQL($dateTo).'\'';
        }
        if ($htlCartBookingId) {
            $sql .= ' AND cbd.`id`='.(int) $htlCartBookingId;
        }
        $sql .= ' ORDER BY cbd.`id`';
        $totalPrice = 0;

        $objRoomTypeServiceProductPrice = new RoomTypeServiceProductPrice();
        if ($serviceProducts = Db::getInstance()->executeS($sql)) {
            foreach ($serviceProducts as $product) {
                $qty = $product['quantity'] ? (int)$product['quantity'] : 1;
                $totalPrice += $objRoomTypeServiceProductPrice->getProductPrice(
                    (int)$product['id_product'],
                    (int)$product['room_type_id_product'],
                    $qty,
                    $useTax,
                    false,
                    $id_address
                );
            }
        }
        return $totalPrice;
    }

    public function getRoomServiceProducts(
        $htlCartBookingId,
        $idLang = 0,
        $useTax = null,
        $autoAddToCart = 0,
        $id_address = null,
        $priceAdditionType = null
    ) {
        if ($useTax === null)
            $useTax = Product::$_taxCalculationMethod == PS_TAX_EXC ? false : true;

        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $sql = 'SELECT rscd.`id_product`, rscd.`quantity`, cbd.`id_cart`, p.`auto_add_to_cart`, p.`allow_multiple_quantity`, cbd.`id` as `htl_cart_booking_id`,
            p.`price_addition_type`, cbd.`id_product` as `room_type_id_product`, pl.`name`
            FROM `'._DB_PREFIX_.'htl_cart_booking_data` cbd
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_service_product_cart_detail` rscd ON(rscd.`htl_cart_booking_id` = cbd.`id`)
            LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = rscd.`id_product`)
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$idLang.')
            WHERE 1';

        if (!is_null($autoAddToCart)) {
            $sql .= ' AND p.`auto_add_to_cart` = '. (int)$autoAddToCart;
            if ($autoAddToCart == 1 && !is_null($priceAdditionType)) {
                $sql .= ' AND p.`price_addition_type` = '.$priceAdditionType;
            }
        }
        if ($htlCartBookingId) {
            $sql .= ' AND cbd.`id`='.(int) $htlCartBookingId;
        }
        $sql .= ' ORDER BY cbd.`id`';

        $objRoomTypeServiceProductPrice = new RoomTypeServiceProductPrice();
        $selectedServiceProducts = array();

        if ($serviceProducts = Db::getInstance()->executeS($sql)) {
            foreach ($serviceProducts as $product) {
                $qty = $product['quantity'] ? (int)$product['quantity'] : 1;
                if (isset($selectedServiceProducts[$product['id_product']])) {
                    $selectedServiceProducts[$product['id_product']]['quantity'] += $product['quantity'];
                    $selectedServiceProducts[$product['id_product']]['total_price'] += $objRoomTypeServiceProductPrice->getProductPrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        $qty,
                        $useTax,
                        false,
                        $id_address
                    );
                    $selectedServiceProducts[$product['id_product']]['total_price_tax_excl'] += $objRoomTypeServiceProductPrice->getProductPrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        $qty,
                        false,
                        false,
                        $id_address
                    );
                    $selectedServiceProducts[$product['id_product']]['total_price_tax_incl'] += $objRoomTypeServiceProductPrice->getProductPrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        $qty,
                        true,
                        false,
                        $id_address
                    );
                } else {
                    $selectedServiceProducts[$product['id_product']] = $product;

                    $selectedServiceProducts[$product['id_product']]['unit_price_tax_excl'] = $objRoomTypeServiceProductPrice->getProductPrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        1,
                        false,
                        false,
                        $id_address
                    );
                    $selectedServiceProducts[$product['id_product']]['unit_price_tax_incl'] = $objRoomTypeServiceProductPrice->getProductPrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        1,
                        true,
                        false,
                        $id_address
                    );
                    $selectedServiceProducts[$product['id_product']]['total_price'] = $objRoomTypeServiceProductPrice->getProductPrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        $qty,
                        $useTax,
                        false,
                        $id_address
                    );
                    $selectedServiceProducts[$product['id_product']]['total_price_tax_excl'] = $objRoomTypeServiceProductPrice->getProductPrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        $qty,
                        false,
                        false,
                        $id_address
                    );
                    $selectedServiceProducts[$product['id_product']]['total_price_tax_incl'] = $objRoomTypeServiceProductPrice->getProductPrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        $qty,
                        true,
                        false,
                        $id_address
                    );
                }
            }

        }

        return $selectedServiceProducts;
    }

    public function getServiceProductsInCart(
        $idCart,
        $idProduct = 0,
        $idHotel = 0,
        $roomTypeIdProduct = 0,
        $dateFrom = 0,
        $dateTo = 0,
        $htlCartBookingId = 0,
        $getTotalPrice = 0,
        $useTax = null,
        $autoAddToCart = 0,
        $id_address = null,
        $priceAdditionType = null
    ) {
        if ($useTax === null)
            $useTax = Product::$_taxCalculationMethod == PS_TAX_EXC ? false : true;

        $sql = 'SELECT rscd.`id_product`, rscd.`quantity`, cbd.`id_cart`, cbd.`id` as `htl_cart_booking_id` ,
            cbd.`id_product` as `room_type_id_product`, cbd.`adults`, cbd.`children`';
        if (!$getTotalPrice) {
            $sql .= ', cbd.`id_guest`, cbd.`id_customer`, p.`auto_add_to_cart`, p.`price_addition_type`,
                cbd.`id_hotel`, cbd.`id_room`, cbd.`date_from`, cbd.`date_to`, cbd.`is_refunded`';
        }
        $sql .= ' FROM `'._DB_PREFIX_.'htl_cart_booking_data` cbd
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_service_product_cart_detail` rscd ON(rscd.`htl_cart_booking_id` = cbd.`id`)
            LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = rscd.`id_product`)
            WHERE 1';

        if (!is_null($autoAddToCart)) {
            $sql .= ' AND p.`auto_add_to_cart` = '. (int)$autoAddToCart;
            if ($autoAddToCart == 1 && !is_null($priceAdditionType)) {
                $sql .= ' AND p.`price_addition_type` = '.$priceAdditionType;
            }
        }
        if ($idCart) {
            $sql .= ' AND cbd.`id_cart`='.(int) $idCart;
        }
        if ($idProduct) {
            $sql .= ' AND rscd.`id_product`='.(int) $idProduct;
        }
        if ($idHotel) {
            $sql .= ' AND cbd.`id_hotel`='.(int) $idHotel;
        }
        if ($roomTypeIdProduct) {
            $sql .= ' AND cbd.`id_product`='.(int) $roomTypeIdProduct;
        }
        if ($dateFrom && $dateTo) {
            $sql .= ' AND cbd.`date_from` = \''.pSQL($dateFrom).'\' AND cbd.`date_to` = \''.pSQL($dateTo).'\'';
        }
        if ($htlCartBookingId) {
            $sql .= ' AND cbd.`id`='.(int) $htlCartBookingId;
        }
        $sql .= ' ORDER BY cbd.`id`';

        if ($getTotalPrice) {
            $totalPrice = 0;
        }
        $objRoomTypeServiceProductPrice = new RoomTypeServiceProductPrice();
        $objHotelRoomType = new HotelRoomType();
        $selectedServiceProducts = array();

        if ($serviceProducts = Db::getInstance()->executeS($sql)) {
            foreach ($serviceProducts as $product) {
                if ($getTotalPrice) {
                    $qty = $product['quantity'] ? (int)$product['quantity'] : 1;
                    $totalPrice += $objRoomTypeServiceProductPrice->getProductPrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        $qty,
                        $useTax,
                        false,
                        $id_address
                    );

                } else {
                    $roomTypeInfo = $objHotelRoomType->getRoomTypeInfoByIdProduct($product['room_type_id_product']);
                    $qty = $product['quantity'] ? (int)$product['quantity'] : 1;
                    if (isset($selectedServiceProducts[$product['htl_cart_booking_id']])) {
                        if ($idProduct) {
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['quantity'] += $product['quantity'];
                        } else {
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['selected_products_info'][$product['id_product']] = array(
                                'id_product' => $product['id_product'],
                                'quantity' => $product['quantity'],
                                'auto_add_to_cart' => $product['auto_add_to_cart'],
                                'price_addition_type' => $product['price_addition_type'],
                                'unit_price_tax_excl' => $objRoomTypeServiceProductPrice->getProductPrice(
                                    (int)$product['id_product'],
                                    (int)$product['room_type_id_product'],
                                    1,
                                    false,
                                    false,
                                    $id_address
                                ),
                                'unit_price_tax_incl' => $objRoomTypeServiceProductPrice->getProductPrice(
                                    (int)$product['id_product'],
                                    (int)$product['room_type_id_product'],
                                    1,
                                    true,
                                    false,
                                    $id_address
                                ),
                            );
                        }
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price'] += $objRoomTypeServiceProductPrice->getProductPrice(
                            (int)$product['id_product'],
                            (int)$product['room_type_id_product'],
                            $qty,
                            $useTax,
                            false,
                            $id_address
                        );
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price_tax_excl'] += $objRoomTypeServiceProductPrice->getProductPrice(
                            (int)$product['id_product'],
                            (int)$product['room_type_id_product'],
                            $qty,
                            false,
                            false,
                            $id_address
                        );
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price_tax_incl'] += $objRoomTypeServiceProductPrice->getProductPrice(
                            (int)$product['id_product'],
                            (int)$product['room_type_id_product'],
                            $qty,
                            true,
                            false,
                            $id_address
                        );
                    } else {
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['htl_cart_booking_id'] = $product['htl_cart_booking_id'];
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['id_cart'] = $product['id_cart'];
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['room_type_id_product'] = $product['room_type_id_product'];
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['id_guest'] = $product['id_guest'];
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['id_customer'] = $product['id_customer'];
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['id_hotel'] = $product['id_hotel'];
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['id_room'] = $product['id_room'];
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['date_from'] = $product['date_from'];
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['date_to'] = $product['date_to'];
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['is_refunded'] = $product['is_refunded'];
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['adults'] = $product['adults'];
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['children'] = $product['children'];
                        if ($idProduct) {
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['id_product'] = $product['id_product'];
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['quantity'] = $product['quantity'];
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['auto_add_to_cart'] = $product['auto_add_to_cart'];
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['price_addition_type'] = $product['price_addition_type'];
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['unit_price_tax_excl'] = $objRoomTypeServiceProductPrice->getProductPrice(
                                (int)$product['id_product'],
                                (int)$product['room_type_id_product'],
                                1,
                                false,
                                false,
                                $id_address
                            );
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['unit_price_tax_incl'] = $objRoomTypeServiceProductPrice->getProductPrice(
                                (int)$product['id_product'],
                                (int)$product['room_type_id_product'],
                                1,
                                true,
                                false,
                                $id_address
                            );
                        } else {
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['selected_products_info'][$product['id_product']] =  array(
                                'id_product' => $product['id_product'],
                                'quantity' => $product['quantity'],
                                'auto_add_to_cart' => $product['auto_add_to_cart'],
                                'price_addition_type' => $product['price_addition_type'],
                                'unit_price_tax_excl' => $objRoomTypeServiceProductPrice->getProductPrice(
                                    (int)$product['id_product'],
                                    (int)$product['room_type_id_product'],
                                    1,
                                    false,
                                    false,
                                    $id_address
                                ),
                                'unit_price_tax_incl' => $objRoomTypeServiceProductPrice->getProductPrice(
                                    (int)$product['id_product'],
                                    (int)$product['room_type_id_product'],
                                    1,
                                    true,
                                    false,
                                    $id_address
                                ),
                            );
                        }

                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price'] = $objRoomTypeServiceProductPrice->getProductPrice(
                            (int)$product['id_product'],
                            (int)$product['room_type_id_product'],
                            $qty,
                            $useTax,
                            false,
                            $id_address
                        );
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price_tax_excl'] = $objRoomTypeServiceProductPrice->getProductPrice(
                            (int)$product['id_product'],
                            (int)$product['room_type_id_product'],
                            $qty,
                            false,
                            false,
                            $id_address
                        );
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price_tax_incl'] = $objRoomTypeServiceProductPrice->getProductPrice(
                            (int)$product['id_product'],
                            (int)$product['room_type_id_product'],
                            $qty,
                            true,
                            false,
                            $id_address
                        );
                    }
                }
            }

        }

        if ($getTotalPrice) {
            return $totalPrice;
        }
        return $selectedServiceProducts;
    }

    public function updateCartServiceProduct(
        $htl_cart_booking_id,
        $id_product,
        $quantity,
        $id_cart,
        $operator
    ) {
        $id_room_type_service_product_cart_detail = $this->alreadyExists($id_product, $htl_cart_booking_id);

        if ($operator == 'up') {
            if ($id_room_type_service_product_cart_detail) {
                $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail($id_room_type_service_product_cart_detail);
            } else {
                $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail();
                $objRoomTypeServiceProductCartDetail->id_product = $id_product;
                $objRoomTypeServiceProductCartDetail->htl_cart_booking_id = $htl_cart_booking_id;
                $objRoomTypeServiceProductCartDetail->id_cart = $id_cart;
            }
            $updateQty = $quantity - $objRoomTypeServiceProductCartDetail->quantity;
            $way = $updateQty > 0 ? 'up' : 'down';
            $objRoomTypeServiceProductCartDetail->quantity = $quantity;
            if ($objRoomTypeServiceProductCartDetail->save()) {
                $objCart = new Cart($id_cart);
                return $objCart->updateQty((int)abs($updateQty), $id_product, null, false, $way);
            }
        } else {
            if ($id_room_type_service_product_cart_detail) {
                $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail($id_room_type_service_product_cart_detail);
                $updateQty = $objRoomTypeServiceProductCartDetail->quantity;
                if ($objRoomTypeServiceProductCartDetail->delete()) {
                    $objCart = new Cart($id_cart);
                    return $objCart->updateQty((int)abs($updateQty), $id_product, null, false, 'down');
                }
            } else {
                return true;
            }
        }
        return false;
    }
}