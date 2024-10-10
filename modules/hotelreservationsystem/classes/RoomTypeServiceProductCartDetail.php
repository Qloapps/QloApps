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
            $way = $updateQty > 0 ? 'up' : 'down';
            $objRoomTypeServiceProductCartDetail->quantity += $updateQty;
            if (Product::getProductPriceCalculation($idProduct) == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                $objHotelCartBookingData = new HotelCartBookingData($idHtlCartData);
                $numdays = HotelHelper::getNumberOfDays($objHotelCartBookingData->date_from, $objHotelCartBookingData->date_to);
                $updateQty *= $numdays;
            }
            if ($objRoomTypeServiceProductCartDetail->save()) {
                $objCart = new Cart($idCart);
                return $objCart->updateQty((int)abs($updateQty), $idProduct, null, false, $way);
            }
        } else {
            return true;
        }

        return false;
    }

    public function removeServiceProductByIdHtlCartBooking($htlCartBookingId, $idService = 0)
    {
        if ($stadardProductsData = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'htl_room_type_service_product_cart_detail`
            WHERE `htl_cart_booking_id` = ' . (int)$htlCartBookingId.
            ($idService? ' AND `id_product` = '.(int)$idService : '')
        )) {
            foreach ($stadardProductsData as $product) {
                if (Validate::isLoadedObject(
                    $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail($product['id_room_type_service_product_cart_detail'])
                )) {
                    $updateQty = $product['quantity'];
                    if (Product::getProductPriceCalculation($product['id_product']) == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                        $objHotelCartBookingData = new HotelCartBookingData($htlCartBookingId);
                        $numdays = HotelHelper::getNumberOfDays($objHotelCartBookingData->date_from, $objHotelCartBookingData->date_to);
                        $updateQty *= $numdays;
                    }
                    if ($objRoomTypeServiceProductCartDetail->delete()) {
                        $objCart = new Cart($product['id_cart']);
                        if (isset(Context::getContext()->controller->controller_type)) {
                            $controllerType = Context::getContext()->controller->controller_type;
                        } else {
                            $controllerType = 'front';
                        }
                        if ($controllerType == 'admin' || $controllerType == 'moduleadmin') {
                            if ($cartQty = Cart::getProductQtyInCart($product['id_cart'], $product['id_product'])) {
                                if ($product['quantity'] < $cartQty) {
                                    Db::getInstance()->update(
                                        'cart_product',
                                        array('quantity' => (int)($cartQty - $product['quantity'])),
                                        '`id_product` = '.(int)$product['id_product'].' AND `id_cart` = '.(int)$product['id_cart']
                                    );
                                } else {
                                    //if room type has no qty remaining in cart then delete row
                                    Db::getInstance()->delete(
                                        'cart_product',
                                        '`id_product` = '.(int)$product['id_product'].' AND `id_cart` = '.(int)$product['id_cart']
                                    );
                                }
                            }
                        } else {
                            $objCart->updateQty((int)($updateQty), $product['id_product'], null, false, 'down');
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * @deprecated since 1.6.1 use getServiceProductsInCart() instead
    */
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
        return $this->getServiceProductsInCart(
            $idCart,
            $idProduct,
            $idHotel,
            $roomTypeIdProduct,
            $dateFrom,
            $dateTo,
            $htlCartBookingId,
            1,
            $useTax,
            $autoAddToCart,
            $id_address,
            $priceAdditionType
        );
    }

    public function getRoomServiceProducts(
        $htlCartBookingId,
        $idLang = 0,
        $useTax = null,
        $autoAddToCart = 0,
        $id_address = null,
        $priceAdditionType = null
    ) {
        if (Validate::isLoadedObject($objHotelCartBookingData = new HotelCartBookingData($htlCartBookingId))) {
            $selectedServiceProducts = $this->getServiceProductsInCart(
                $objHotelCartBookingData->id_cart,
                0,
                0,
                0,
                0,
                0,
                $htlCartBookingId,
                0,
                $useTax,
                $autoAddToCart,
                $id_address,
                $priceAdditionType
            );

            if (isset($selectedServiceProducts[$htlCartBookingId]['selected_products_info'])) {
                return $selectedServiceProducts[$htlCartBookingId]['selected_products_info'];
            }
        }

        return array();
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
        $priceAdditionType = null,
        $idRoom = 0
    ) {
        if ($useTax === null)
            $useTax = Product::$_taxCalculationMethod == PS_TAX_EXC ? false : true;

        $idLang = Context::getContext()->language->id;

        $sql = 'SELECT rscd.`id_product`, rscd.`quantity`, cbd.`id_cart`, cbd.`id` as `htl_cart_booking_id` ,
            cbd.`id_product` as `room_type_id_product`, cbd.`adults`, cbd.`children`, cbd.`date_from`, cbd.`date_to`';
        if (!$getTotalPrice) {
            $sql .= ', pl.`name`, cbd.`id_guest`, cbd.`id_customer`, p.`auto_add_to_cart`, p.`price_addition_type`,
                p.`price_calculation_method`, cbd.`id_hotel`, cbd.`id_room`, cbd.`date_from`, cbd.`date_to`,
                cbd.`is_refunded`, p.`allow_multiple_quantity`';
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
        if ($idRoom) {
            $sql .= ' AND cbd.`id_room`='.(int) $idRoom;
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
                $qty = $product['quantity'] ? (int)$product['quantity'] : 1;
                $numdays = 1;
                if (Product::getProductPriceCalculation($product['id_product']) == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                    $numdays = HotelHelper::getNumberOfDays($product['date_from'], $product['date_to']);
                }

                if ($getTotalPrice) {
                    $servicePrice = $objRoomTypeServiceProductPrice->getServicePrice(
                        (int)$product['id_product'],
                        (int)$product['room_type_id_product'],
                        1,
                        $product['date_from'],
                        $product['date_to'],
                        $useTax,
                        false,
                        $id_address
                    );

                    $totalPrice += Tools::processPriceRounding($servicePrice, $qty);
                } else {
                    if (isset($selectedServiceProducts[$product['htl_cart_booking_id']])) {
                        if ($idProduct) {
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['quantity'] += $product['quantity'];
                        } else {
                            $servicePrice = $objRoomTypeServiceProductPrice->getServicePrice(
                                (int)$product['id_product'],
                                (int)$product['room_type_id_product'],
                                1,
                                $product['date_from'],
                                $product['date_to'],
                                $useTax,
                                false,
                                $id_address
                            );

                            $servicePriceTE = $objRoomTypeServiceProductPrice->getServicePrice(
                                (int)$product['id_product'],
                                (int)$product['room_type_id_product'],
                                1,
                                $product['date_from'],
                                $product['date_to'],
                                false,
                                false,
                                $id_address
                            );

                            $servicePriceTI = $objRoomTypeServiceProductPrice->getServicePrice(
                                (int)$product['id_product'],
                                (int)$product['room_type_id_product'],
                                1,
                                $product['date_from'],
                                $product['date_to'],
                                true,
                                false,
                                $id_address
                            );
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['selected_products_info'][$product['id_product']] = array(
                                'id_product' => $product['id_product'],
                                'name' => $product['name'],
                                'quantity' => $product['quantity'],
                                'auto_add_to_cart' => $product['auto_add_to_cart'],
                                'allow_multiple_quantity' => $product['allow_multiple_quantity'],
                                'price_addition_type' => $product['price_addition_type'],
                                'price_calculation_method' => $product['price_calculation_method'],
                                'unit_price_tax_excl' => ($objRoomTypeServiceProductPrice->getServicePrice(
                                    (int)$product['id_product'],
                                    (int)$product['room_type_id_product'],
                                    1,
                                    $product['date_from'],
                                    $product['date_to'],
                                    false,
                                    false,
                                    $id_address
                                ) / $numdays),
                                'unit_price_tax_incl' => ($objRoomTypeServiceProductPrice->getServicePrice(
                                    (int)$product['id_product'],
                                    (int)$product['room_type_id_product'],
                                    1,
                                    $product['date_from'],
                                    $product['date_to'],
                                    true,
                                    false,
                                    $id_address
                                ) / $numdays),
                                'total_price' => $servicePrice,
                                'total_price_tax_excl' => $servicePriceTE,
                                'total_price_tax_incl' => $servicePriceTI
                            );
                        }

                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price'] += Tools::processPriceRounding($servicePrice, $qty);
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price_tax_excl'] += Tools::processPriceRounding($servicePriceTE, $qty);
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price_tax_incl'] += Tools::processPriceRounding($servicePriceTI, $qty);
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
                        $servicePrice = $objRoomTypeServiceProductPrice->getServicePrice(
                            (int)$product['id_product'],
                            (int)$product['room_type_id_product'],
                            1,
                            $product['date_from'],
                            $product['date_to'],
                            $useTax,
                            false,
                            $id_address
                        );

                        $servicePriceTE = $objRoomTypeServiceProductPrice->getServicePrice(
                            (int)$product['id_product'],
                            (int)$product['room_type_id_product'],
                            1,
                            $product['date_from'],
                            $product['date_to'],
                            false,
                            false,
                            $id_address
                        );

                        $servicePriceTI = $objRoomTypeServiceProductPrice->getServicePrice(
                            (int)$product['id_product'],
                            (int)$product['room_type_id_product'],
                            1,
                            $product['date_from'],
                            $product['date_to'],
                            true,
                            false,
                            $id_address
                        );
                        if ($idProduct) {
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['id_product'] = $product['id_product'];
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['name'] = $product['name'];
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['quantity'] = $product['quantity'];
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['auto_add_to_cart'] = $product['auto_add_to_cart'];
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['allow_multiple_quantity'] = $product['allow_multiple_quantity'];
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['price_addition_type'] = $product['price_addition_type'];
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['price_calculation_method'] = $product['price_calculation_method'];
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['unit_price_tax_excl'] = $objRoomTypeServiceProductPrice->getServicePrice(
                                (int)$product['id_product'],
                                (int)$product['room_type_id_product'],
                                1,
                                $product['date_from'],
                                $product['date_to'],
                                false,
                                false,
                                $id_address
                            ) / $numdays;
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['unit_price_tax_incl'] = $objRoomTypeServiceProductPrice->getServicePrice(
                                (int)$product['id_product'],
                                (int)$product['room_type_id_product'],
                                1,
                                $product['date_from'],
                                $product['date_to'],
                                true,
                                false,
                                $id_address
                            ) / $numdays;
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price'] = $servicePrice;
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price_tax_excl'] = $servicePriceTE;
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price_tax_incl'] = $servicePriceTI;
                        } else {
                            $selectedServiceProducts[$product['htl_cart_booking_id']]['selected_products_info'][$product['id_product']] =  array(
                                'id_product' => $product['id_product'],
                                'name' => $product['name'],
                                'quantity' => $product['quantity'],
                                'auto_add_to_cart' => $product['auto_add_to_cart'],
                                'allow_multiple_quantity' => $product['allow_multiple_quantity'],
                                'price_addition_type' => $product['price_addition_type'],
                                'price_calculation_method' => $product['price_calculation_method'],
                                'unit_price_tax_excl' => ($objRoomTypeServiceProductPrice->getServicePrice(
                                    (int)$product['id_product'],
                                    (int)$product['room_type_id_product'],
                                    1,
                                    null,
                                    null,
                                    false,
                                    false,
                                    $id_address
                                ) / $numdays),
                                'unit_price_tax_incl' => ($objRoomTypeServiceProductPrice->getServicePrice(
                                    (int)$product['id_product'],
                                    (int)$product['room_type_id_product'],
                                    1,
                                    null,
                                    null,
                                    true,
                                    false,
                                    $id_address
                                ) / $numdays),
                                'total_price' => $servicePrice,
                                'total_price_tax_excl' => $servicePriceTE,
                                'total_price_tax_incl' => $servicePriceTI
                            );
                        }



                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price'] = Tools::processPriceRounding($servicePrice, $qty);
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price_tax_excl'] = Tools::processPriceRounding($servicePriceTE, $qty);
                        $selectedServiceProducts[$product['htl_cart_booking_id']]['total_price_tax_incl'] = Tools::processPriceRounding($servicePriceTI, $qty);
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

    public function getAllServiceProduct($idCart)
    {
        return Db::getInstance()->executeS(
            'SELECT spcd.*,  cbd.`id_product` as `id_product_room_type`, cbd.`id_room`, cbd.`id_hotel`, cbd.`date_from`, cbd.`date_to` FROM `' . _DB_PREFIX_ . 'htl_room_type_service_product_cart_detail` spcd
            INNER JOIN `'._DB_PREFIX_.'htl_cart_booking_data` cbd
            ON(spcd.`htl_cart_booking_id` = cbd.`id`)
            WHERE spcd.`id_cart` = ' . (int)$idCart
        );
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
