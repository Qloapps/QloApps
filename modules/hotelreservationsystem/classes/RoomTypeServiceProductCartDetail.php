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
        $idCart,
        $idHtlCartData
    ) {
        return Db::getInstance()->getValue(
            'SELECT `id_room_type_service_product_cart_detail` FROM `'._DB_PREFIX_.'htl_room_type_service_product_cart_detail`
            WHERE `id_product` = '.(int)$idProduct.' AND `htl_cart_booking_id` = '.(int)$idHtlCartData.'
            AND `id_cart` = '.(int)$idCart
        );
    }

    public function addServiceProductInCart(
        $idProduct,
        $quantity,
        $idCart,
        $idHtlCartData
    ) {
        if ($id_room_type_service_product_cart_detail = $this->alreadyExists($idProduct, $idCart, $idHtlCartData)) {
            $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail($id_room_type_service_product_cart_detail);
        } else {
            $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail();
            $objRoomTypeServiceProductCartDetail->id_product = $idProduct;
            $objRoomTypeServiceProductCartDetail->htl_cart_booking_id = $idHtlCartData;
            $objRoomTypeServiceProductCartDetail->id_cart = $idCart;
        }

        if ($updateQty = $quantity - $objRoomTypeServiceProductCartDetail->quantity) {
            if (Product::getProductPriceCalculation($idProduct) == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                $objHotelCartBookingData = new HotelCartBookingData($idHtlCartData);
                $numdays = HotelHelper::getNumberOfDays($objHotelCartBookingData->date_from, $objHotelCartBookingData->date_to);
                $updateQty *= $numdays;

            }
            $way = $updateQty > 0 ? 'up' : 'down';
            $objRoomTypeServiceProductCartDetail->quantity = $quantity;
            if ($objRoomTypeServiceProductCartDetail->save()) {
                $objCart = new Cart($idCart);
                return $objCart->updateQty((int)abs($updateQty), $idProduct, null, false, $way);
            }
        } else {
            return true;
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

        $sql = 'SELECT rscd.`id_product`, rscd.`quantity`, cbd.`id_product` as `room_type_id_product`, cbd.`date_from`, cbd.`date_to`
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
                $totalPrice += $objRoomTypeServiceProductPrice->getServicePrice(
                    (int)$product['id_product'],
                    (int)$product['room_type_id_product'],
                    $qty,
                    $product['date_from'],
                    $product['date_to'],
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
            p.`price_addition_type`, cbd.`id_product` as `room_type_id_product`, pl.`name`, cbd.`date_from`, cbd.`date_to`
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
                    $selectedServiceProducts[$product['id_product']]['total_price'] += $objRoomTypeServiceProductPrice->getServicePrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        $qty,
                        $product['date_from'],
                        $product['date_to'],
                        $useTax,
                        false,
                        $id_address
                    );
                    $selectedServiceProducts[$product['id_product']]['total_price_tax_excl'] += $objRoomTypeServiceProductPrice->getServicePrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        $qty,
                        $product['date_from'],
                        $product['date_to'],
                        false,
                        false,
                        $id_address
                    );
                    $selectedServiceProducts[$product['id_product']]['total_price_tax_incl'] += $objRoomTypeServiceProductPrice->getServicePrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        $qty,
                        $product['date_from'],
                        $product['date_to'],
                        true,
                        false,
                        $id_address
                    );
                } else {
                    $selectedServiceProducts[$product['id_product']] = $product;

                    $selectedServiceProducts[$product['id_product']]['unit_price_tax_excl'] = $objRoomTypeServiceProductPrice->getServicePrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        1,
                        null,
                        null,
                        false,
                        false,
                        $id_address
                    );
                    $selectedServiceProducts[$product['id_product']]['unit_price_tax_incl'] = $objRoomTypeServiceProductPrice->getServicePrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        1,
                        null,
                        null,
                        true,
                        false,
                        $id_address
                    );
                    $selectedServiceProducts[$product['id_product']]['total_price'] = $objRoomTypeServiceProductPrice->getServicePrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        $qty,
                        $product['date_from'],
                        $product['date_to'],
                        $useTax,
                        false,
                        $id_address
                    );
                    $selectedServiceProducts[$product['id_product']]['total_price_tax_excl'] = $objRoomTypeServiceProductPrice->getServicePrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        $qty,
                        $product['date_from'],
                        $product['date_to'],
                        false,
                        false,
                        $id_address
                    );
                    $selectedServiceProducts[$product['id_product']]['total_price_tax_incl'] = $objRoomTypeServiceProductPrice->getServicePrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        $qty,
                        $product['date_from'],
                        $product['date_to'],
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

        $idLang = Context::getContext()->language->id;

        $sql = 'SELECT rscd.`id_product`, rscd.`quantity`, cbd.`id_cart`, cbd.`id` as `htl_cart_booking_id` ,
            cbd.`id_product` as `room_type_id_product`, cbd.`adults`, cbd.`children`, cbd.`date_from`, cbd.`date_to`';
        if (!$getTotalPrice) {
            $sql .= ', pl.`name`, cbd.`id_guest`, cbd.`id_customer`, p.`auto_add_to_cart`, p.`price_addition_type`,
                cbd.`id_hotel`, cbd.`id_room`, cbd.`date_from`, cbd.`date_to`, cbd.`is_refunded`';
        }
        $sql .= ' FROM `'._DB_PREFIX_.'htl_cart_booking_data` cbd
            LEFT JOIN `'._DB_PREFIX_.'htl_room_type_service_product_cart_detail` rscd
            ON(rscd.`htl_cart_booking_id` = cbd.`id`)
            LEFT JOIN `'._DB_PREFIX_.'product` p
            ON (p.`id_product` = rscd.`id_product`)';
        if (!$getTotalPrice) {
            $sql .=  ' LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.(int)$idLang.')';
        }
        $sql .= ' WHERE 1';

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
                    $totalPrice += $objRoomTypeServiceProductPrice->getServicePrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        $qty,
                        $product['date_from'],
                        $product['date_to'],
                        $useTax,
                        false,
                        $id_address
                    );

                } else {
                    $qty = $product['quantity'] ? (int)$product['quantity'] : 1;
                    if (isset($selectedServiceProducts[$product['htl_cart_booking_id']])) {
                        if ($idProduct) {
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['quantity'] += $product['quantity'];
                        } else {
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['selected_products_info'][$product['id_product']] = array(
                                'id_product' => $product['id_product'],
                                'name' => $product['name'],
                                'quantity' => $product['quantity'],
                                'auto_add_to_cart' => $product['auto_add_to_cart'],
                                'price_addition_type' => $product['price_addition_type'],
                                'unit_price_tax_excl' => $objRoomTypeServiceProductPrice->getServicePrice(
                                    (int)$product['id_product'],
                                    (int)$product['room_type_id_product'],
                                    1,
                                    null,
                                    null,
                                    false,
                                    false,
                                    $id_address
                                ),
                                'unit_price_tax_incl' => $objRoomTypeServiceProductPrice->getServicePrice(
                                    (int)$product['id_product'],
                                    (int)$product['room_type_id_product'],
                                    1,
                                    null,
                                    null,
                                    true,
                                    false,
                                    $id_address
                                ),
                            );
                        }
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price'] += $objRoomTypeServiceProductPrice->getServicePrice(
                            (int)$product['id_product'],
                            (int)$product['room_type_id_product'],
                            $qty,
                            $product['date_from'],
                            $product['date_to'],
                            $useTax,
                            false,
                            $id_address
                        );
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price_tax_excl'] += $objRoomTypeServiceProductPrice->getServicePrice(
                            (int)$product['id_product'],
                            (int)$product['room_type_id_product'],
                            $qty,
                            $product['date_from'],
                            $product['date_to'],
                            false,
                            false,
                            $id_address
                        );
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price_tax_incl'] += $objRoomTypeServiceProductPrice->getServicePrice(
                            (int)$product['id_product'],
                            (int)$product['room_type_id_product'],
                            $qty,
                            $product['date_from'],
                            $product['date_to'],
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
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['name'] = $product['name'];
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['quantity'] = $product['quantity'];
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['auto_add_to_cart'] = $product['auto_add_to_cart'];
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['price_addition_type'] = $product['price_addition_type'];
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['unit_price_tax_excl'] = $objRoomTypeServiceProductPrice->getServicePrice(
                                (int)$product['id_product'],
                                (int)$product['room_type_id_product'],
                                1,
                                null,
                                null,
                                false,
                                false,
                                $id_address
                            );
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['unit_price_tax_incl'] = $objRoomTypeServiceProductPrice->getServicePrice(
                                (int)$product['id_product'],
                                (int)$product['room_type_id_product'],
                                1,
                                null,
                                null,
                                true,
                                false,
                                $id_address
                            );
                        } else {
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['selected_products_info'][$product['id_product']] =  array(
                                'id_product' => $product['id_product'],
                                'name' => $product['name'],
                                'quantity' => $product['quantity'],
                                'auto_add_to_cart' => $product['auto_add_to_cart'],
                                'price_addition_type' => $product['price_addition_type'],
                                'unit_price_tax_excl' => $objRoomTypeServiceProductPrice->getServicePrice(
                                    (int)$product['id_product'],
                                    (int)$product['room_type_id_product'],
                                    1,
                                    null,
                                    null,
                                    false,
                                    false,
                                    $id_address
                                ),
                                'unit_price_tax_incl' => $objRoomTypeServiceProductPrice->getServicePrice(
                                    (int)$product['id_product'],
                                    (int)$product['room_type_id_product'],
                                    1,
                                    null,
                                    null,
                                    true,
                                    false,
                                    $id_address
                                ),
                            );
                        }

                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price'] = $objRoomTypeServiceProductPrice->getServicePrice(
                            (int)$product['id_product'],
                            (int)$product['room_type_id_product'],
                            $qty,
                            $product['date_from'],
                            $product['date_to'],
                            $useTax,
                            false,
                            $id_address
                        );
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price_tax_excl'] = $objRoomTypeServiceProductPrice->getServicePrice(
                            (int)$product['id_product'],
                            (int)$product['room_type_id_product'],
                            $qty,
                            $product['date_from'],
                            $product['date_to'],
                            false,
                            false,
                            $id_address
                        );
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price_tax_incl'] = $objRoomTypeServiceProductPrice->getServicePrice(
                            (int)$product['id_product'],
                            (int)$product['room_type_id_product'],
                            $qty,
                            $product['date_from'],
                            $product['date_to'],
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
        $idHtlCartData,
        $idProduct,
        $quantity,
        $idCart,
        $operator
    ) {
        $id_room_type_service_product_cart_detail = $this->alreadyExists($idProduct, $idCart, $idHtlCartData);

        if ($operator == 'up') {
            return $this->addServiceProductInCart(
                $idProduct,
                $quantity,
                $idCart,
                $idHtlCartData
            );
        } else {
            if ($id_room_type_service_product_cart_detail) {
                $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail($id_room_type_service_product_cart_detail);
                $updateQty = $objRoomTypeServiceProductCartDetail->quantity;
                if ($objRoomTypeServiceProductCartDetail->delete()) {
                    $objCart = new Cart($idCart);
                    return $objCart->updateQty((int)abs($updateQty), $idProduct, null, false, 'down');
                }
            } else {
                return true;
            }
        }
        return false;
    }

    public static function validateServiceProductsInCart()
    {
        $context = Context::getContext();
        if ($cartProducts = $context->cart->getProducts()) {
            $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail();
            foreach ($cartProducts as $product) {
                if (!$product['active'] && !$product['booking_product']) {
                    $serviceProducts = $objRoomTypeServiceProductCartDetail->getServiceProductsInCart($context->cart->id, $product['id_product']);
                    foreach ($serviceProducts as $serviceProduct) {
                        $objRoomTypeServiceProductCartDetail->removeServiceProductByIdHtlCartBooking($serviceProduct['htl_cart_booking_id']);
                    }
                }
            }
        }
    }
}