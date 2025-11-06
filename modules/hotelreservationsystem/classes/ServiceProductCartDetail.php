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

class ServiceProductCartDetail extends ObjectModel
{
    public $id_cart;
    public $id_product;
    public $id_product_option;
    public $id_hotel;
    public $htl_cart_booking_id;
    public $quantity;

    public static $definition = array(
        'table' => 'service_product_cart_detail',
        'primary' => 'id_service_product_cart_detail',
        'fields' => array(
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_product_option' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'htl_cart_booking_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'quantity' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
        ),
    );

    public function alreadyExists(
        $idCart,
        $idProduct = false,
        $idHtlCartData = false,
        $idHotel = false,
        $idProductOption = false,
        $idServiceProductCartDetail = false
    ) {

        $sql = 'SELECT `id_service_product_cart_detail` FROM `'._DB_PREFIX_.'service_product_cart_detail`
            WHERE `id_cart` = '.(int)$idCart;

        if ($idProduct) {
            $sql .= ' AND `id_product` = '.(int)$idProduct;
        }
        if ($idHotel) {
            $sql .= ' AND `id_hotel` = '.(int)$idHotel;
        }
        if ($idHtlCartData) {
            $sql .= ' AND `htl_cart_booking_id` = '.(int)$idHtlCartData;
        }
        if ($idProductOption) {
            $sql .= ' AND `id_product_option` = '.(int)$idProductOption;
        }
        if ($idServiceProductCartDetail) {
            $sql .= ' AND `id_service_product_cart_detail` = '.(int)$idServiceProductCartDetail;
        }

        return Db::getInstance()->getValue($sql);
    }

    public function removeServiceProductByIdHtlCartBooking(
        $htlCartBookingId,
        $idService = 0
        )
    {
        if ($stadardProductsData = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'service_product_cart_detail`
            WHERE `htl_cart_booking_id` = ' . (int)$htlCartBookingId.
            ($idService? ' AND `id_product` = '.(int)$idService : '')
        )) {
            foreach ($stadardProductsData as $product) {
                if (Validate::isLoadedObject(
                    $objServiceProductCartDetail = new ServiceProductCartDetail($product['id_service_product_cart_detail'])
                )) {
                    $updateQty = $product['quantity'];
                    if (Product::getProductPriceCalculation($product['id_product']) == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                        $objHotelCartBookingData = new HotelCartBookingData($htlCartBookingId);
                        $numdays = HotelHelper::getNumberOfDays($objHotelCartBookingData->date_from, $objHotelCartBookingData->date_to);
                        $updateQty *= $numdays;
                    }
                    if ($objServiceProductCartDetail->delete()) {
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
     * To get service products in cart as per sent parameters
     * @param [type] $idCart
     * @param integer $idHotel : send id_hotel for products for specific hotel
     * @param integer $idHotelCartBooking: send htl_cart_booking_id for products for specific hotel room booking
     * @param array $sellingPreferenceTypes: Send selling preference type whcih products to be fetched
     * @param integer $idProduct
     * @param [type] $idProductOption
     * @param [type] $useTax
     * @param integer $getTotalPrice
     * @param [type] $idLang
     * @return array|float
     */
    // public function getProducts(
    public function getServiceProductsInCart(
        $idCart = 0,
        $sellingPreferenceTypes = [],
        $idHotel = null,
        $idHotelCartBooking = null,
        $idProductRoomType = null,
        $idProduct = null,
        $idProductOption = null,
        $useTax = null,
        $getTotalPrice = 0,
        $autoAddToCart = null,
        $priceAdditionType = null,
        $groupByProductId = 0,
        $detailedInfo = 0,
        $idLang = 0,
        $idServiceProductCartDetail = 0
    ) {
        if ($useTax === null) {
            $useTax = Product::$_taxCalculationMethod == PS_TAX_EXC ? false : true;
        }

        if (!$idLang) {
            $language = Context::getContext()->language;
        } else {
            $language = new Language($idLang);
        }

        $sql = 'SELECT spc.*, p.`selling_preference_type`, hcbd.`date_from`, spc.`id_cart` as service_id_cart, hcbd.`date_to`, p.`price_calculation_method`, hcbd.`id_product` as `id_product_room_type`';
        if (!$getTotalPrice) {
            $sql .= ', hbil.`hotel_name`, p.`auto_add_to_cart`, p.`price_addition_type` ';
        }
        $sql .= ' FROM `'._DB_PREFIX_.'service_product_cart_detail` spc';
        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = spc.`id_product`)';

        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'htl_cart_booking_data` hcbd ON (hcbd.`id` = spc.`htl_cart_booking_id`)';

        if (!$getTotalPrice) {
            $sql .= ' LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbil ON (hbil.`id` = spc.`id_hotel` AND hbil.`id_lang` = '. $language->id.')';
        }

        $sql .= ' WHERE spc.`id_product`!=0 ';
        if ($idCart) {
            $sql .= ' AND spc.`id_cart`='.(int) $idCart;
        }

        if (!is_null($idProductRoomType)) {
            $sql .= ' AND hcbd.`id_product`='.(int) $idProductRoomType;
        }

        if (!is_null($autoAddToCart)) {
            $sql .= ' AND p.`auto_add_to_cart` = '. (int)$autoAddToCart;
            if ($autoAddToCart == 1 && !is_null($priceAdditionType)) {
                $sql .= ' AND p.`price_addition_type` = '.$priceAdditionType;
            }
        }

        if ($sellingPreferenceTypes) {
            $sql .= ' AND p.`selling_preference_type` IN ('.implode(',', $sellingPreferenceTypes).')';
        }
        if (!is_null($idHotelCartBooking)) {
            $sql .= ' AND spc.`htl_cart_booking_id`='.(int) $idHotelCartBooking;
        }
        if (!is_null($idHotel)) {
            $sql .= ' AND spc.`id_hotel`='.(int) $idHotel;
        }
        if (!is_null($idProduct)) {
            $sql .= ' AND spc.`id_product`='.(int) $idProduct;
        }
        if (!is_null($idProductOption)) {
            $sql .= ' AND spc.`id_product_option`='.(int) $idProductOption;
        }

        if ($idServiceProductCartDetail) {
            $sql .= ' AND spc.`id_service_product_cart_detail`='.(int) $idServiceProductCartDetail;
        }

        if ($getTotalPrice) {
            $totalPrice = 0;
        }

        $selectedProducts = array();
        $objServiceProductOption = new ServiceProductOption();
        if ($serviceProducts = Db::getInstance()->executeS($sql)) {
            $context = Context::getContext();
            foreach ($serviceProducts as $product) {
                $objProduct = new Product($product['id_product'], false, $language->id);
                if (!$objProduct->booking_product) {
                    if ($getTotalPrice) {
                        $qty = $product['quantity'] ? (int)$product['quantity'] : 1;
                        $totalPrice += Product::getServiceProductPrice(
                            $objProduct->id,
                            $product['id_product_option'],
                            $product['id_hotel'],
                            $product['id_product_room_type'],
                            $useTax,
                            $qty,
                            $product['date_from'],
                            $product['date_to'],
                            $idCart,
                            null,
                            1,
                            null,
                            $product['htl_cart_booking_id']
                        );
                    } else {
                        $numDays = 1;
                        // If price type is per day but the dates are not valid.
                        if (($objProduct->price_calculation_method == Product::PRICE_CALCULATION_METHOD_PER_DAY)
                            && (!$numDays = HotelHelper::getNumberOfDays($product['date_from'], $product['date_to']))
                        ) {
                            $numDays = 1;
                        }

                        $priceTaxIncl = Product::getServiceProductPrice(
                            $objProduct->id,
                            $product['id_product_option'],
                            $product['id_hotel'],
                            $product['id_product_room_type'],
                            true,
                            1,
                            $product['date_from'],
                            $product['date_to'],
                            $idCart,
                            null,
                            1,
                            null,
                            $product['htl_cart_booking_id']
                        )/$numDays;
                        $priceTaxExcl = Product::getServiceProductPrice(
                            $objProduct->id,
                            $product['id_product_option'],
                            $product['id_hotel'],
                            $product['id_product_room_type'],
                            false,
                            1,
                            $product['date_from'],
                            $product['date_to'],
                            $idCart,
                            null,
                            1,
                            null,
                            $product['htl_cart_booking_id']
                        )/$numDays;

                        $optionDetails = false;
                        if (ServiceProductOption::productHasOptions($product['id_product'])) {
                            $optionDetails = $objServiceProductOption->getProductOptions(
                                $objProduct->id,
                                $product['id_product_option']
                            );
                        }
                        $coverImageArr = $objProduct->getCover($product['id_product']);
                        if (!empty($coverImageArr)) {
                            $coverImg = $context->link->getImageLink(
                                $objProduct->link_rewrite,
                                $objProduct->id.'-'.$coverImageArr['id_image'],
                                'small_default'
                            );
                        } else {
                            $coverImg = $context->link->getImageLink(
                                $objProduct->link_rewrite,
                                $language->iso_code.'-default',
                                'small_default'
                            );
                        }
                        $productInfo = array(
                            'id_service_product_cart_detail' => $product['id_service_product_cart_detail'],
                            'id_cart' => $product['id_cart'],
                            'id_hotel' => $product['id_hotel'],
                            'id_hotel_cart_booking' => $product['htl_cart_booking_id'],
                            'hotel_name' => $product['hotel_name'],
                            'id_product' =>$objProduct->id,
                            'selling_preference_type' => $product['selling_preference_type'],
                            'id_product_option' => $product['id_product_option'],
                            'name' => $objProduct->name,
                            'option_name' => isset($optionDetails['name']) ? $optionDetails['name'] : false,
                            'minimal_quantity' => $objProduct->minimal_quantity,
                            'allow_multiple_quantity' => $objProduct->allow_multiple_quantity,
                            'max_quantity' => $objProduct->max_quantity,
                            'unit_price_tax_incl' => $priceTaxIncl,
                            'unit_price_tax_excl' => $priceTaxExcl,
                            'quantity' => $product['quantity'],
                            'total_price_tax_incl' => $priceTaxIncl * (int)$product['quantity'] * $numDays,
                            'total_price_tax_excl' => $priceTaxExcl * (int)$product['quantity'] * $numDays,
                            'cover_img' => $coverImg,
                            'price_calculation_method' => $product['price_calculation_method'],
                            'auto_add_to_cart' => $product['auto_add_to_cart'],
                            'price_addition_type' => $product['price_addition_type'],
                            'total_price' => ($useTax ? $priceTaxIncl * (int)$product['quantity'] * $numDays : $priceTaxExcl * (int)$product['quantity'] * $numDays),
                        );

                        if ($product['htl_cart_booking_id']) {
                            $objHotelCartBooking = new HotelCartBookingData($product['htl_cart_booking_id']);
                            $productInfo['date_from'] = $objHotelCartBooking->date_from;
                            $productInfo['date_to'] = $objHotelCartBooking->date_to;
                            $productInfo['id_room_type_hotel'] = $objHotelCartBooking->id_hotel;
                            $productInfo['id_room_type'] = $objHotelCartBooking->id_product;
                            $productInfo['id_room'] = $objHotelCartBooking->id_room;
                        } else {
                            $productInfo['date_from'] = $product['date_from'] = '';
                            $productInfo['date_to'] = $product['date_to'] = '';
                            $productInfo['id_room_type_hotel'] = 0;
                            $productInfo['id_room_type'] = 0;
                            $productInfo['id_room'] = 0;
                        }

                        if ($detailedInfo) {
                            $objHotelBranchInformation = new HotelBranchInformation();
                            $hotelInfo = $objHotelBranchInformation->hotelBranchesInfo($language->id, 2, 1, $product['id_hotel']);
                            $hotelInfo['location'] = $hotelInfo['hotel_name'].', '.$hotelInfo['city'].
                                ($hotelInfo['state_name']?', '.$hotelInfo['state_name']:'').', '.
                                $hotelInfo['country_name'].', '.$hotelInfo['postcode'];
                            $productInfo['hotel_info'] = $hotelInfo;
                        }

                        if ($groupByProductId) {
                            $selectedProducts[$objProduct->id] = $productInfo;
                        } else {
                            $selectedProducts[] = $productInfo;
                        }
                    }
                }
            }
        }

        if ($getTotalPrice) {
            return $totalPrice;
        }

        return $selectedProducts;
    }

    public function updateCartServiceProduct(
        $idCart,
        $idProduct,
        $operator,
        $quantity = false,
        $idHotel = false,
        $idHtlCartData = false,
        $idProductOption = null
    ) {
        if ($operator == 'up') {
            return $this->addServiceProductInCart(
                $idCart,
                $idProduct,
                $quantity,
                $idHotel,
                $idHtlCartData,
                $idProductOption
            );
        } else {
            return $this->removeCartServiceProduct(
                $idCart,
                $idProduct,
                $quantity,
                $idHotel,
                $idHtlCartData,
                $idProductOption
            );
        }
        return false;
    }

    public function addServiceProductInCart(
        $idCart,
        $idProduct,
        $quantity,
        $idHotel = false,
        $idHtlCartData = false,
        $idProductOption = null
    ) {
        if ($quantity <= 0) {
            $quantity = 1;
        }

        $isAvailable = true;
        Hook::exec('actionCheckServiceAvailability', array(
            'id_product' => $idProduct,
            'quantity' => $quantity,
            'id_cart' => $idCart,
            'htl_cart_booking_id' => $idHtlCartData,
            'is_service_available' => &$isAvailable,
        ));

        if (!$isAvailable) {
            return false;
        }
        if ($id_service_product_cart_detail = $this->alreadyExists(
            $idCart,
            $idProduct,
            $idHtlCartData,
            $idHotel,
            $idProductOption
        )) {
            $objServiceProductCartDetail = new ServiceProductCartDetail($id_service_product_cart_detail);
        } else {
            $objServiceProductCartDetail = new ServiceProductCartDetail();
            $objServiceProductCartDetail->id_product = $idProduct;
            $objServiceProductCartDetail->quantity = 0;
            $objServiceProductCartDetail->id_hotel = $idHotel;
            $objServiceProductCartDetail->htl_cart_booking_id = $idHtlCartData;
            $objServiceProductCartDetail->id_cart = $idCart;
            $objServiceProductCartDetail->id_product_option = $idProductOption;
        }

        $objProduct = new Product((int) $idProduct);
        if ($objProduct->allow_multiple_quantity) {
            $objServiceProductCartDetail->quantity += $quantity;
        } else {
            $objServiceProductCartDetail->quantity = 1;
        }

        if ($objServiceProductCartDetail->save()) {
            if ($objProduct->price_calculation_method == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                if (Validate::isLoadedObject($objHotelCartBooking = new HotelCartBookingData($idHtlCartData))) {
                    $numDays = HotelHelper::getNumberOfDays(
                        $objHotelCartBooking->date_from,
                        $objHotelCartBooking->date_to
                    );
                    $quantity = $objServiceProductCartDetail->quantity * $numDays;
                }
            }

            $objCart = new Cart($idCart);
            return $objCart->updateQty($quantity, $idProduct);
        }

        return true;
    }

    public function removeCartServiceProduct(
        $idCart,
        $idProduct = null,
        $quantity = false,
        $idHotel = null,
        $idHtlCartData = null,
        $idProductOption = null,
        $idServiceProductCartDetail = 0
    ) {
        if ($serviceProducts = $this->getServiceProductsInCart(
            $idCart,
            [],
            $idHotel,
            $idHtlCartData,
            null,
            $idProduct,
            $idProductOption,
            null,
            0,
            null,
            null,
            0,
            0,
            0,
            $idServiceProductCartDetail
        )) {
            $updateQunatity = false;
            $res = true;
            foreach ($serviceProducts as $product) {
                $objServiceProductCartDetail = new ServiceProductCartDetail($product['id_service_product_cart_detail']);
                if ($quantity) {
                    $removedQuantity = $quantity;
                    $objServiceProductCartDetail->quantity -= $quantity;
                    if ($objServiceProductCartDetail->quantity > 0) {
                        $updateQunatity = $objServiceProductCartDetail->save();
                    } else {
                        $updateQunatity = $objServiceProductCartDetail->delete();
                    }
                } else {
                    $removedQuantity = $objServiceProductCartDetail->quantity;
                    $updateQunatity = $objServiceProductCartDetail->delete();
                }
                if ($updateQunatity) {
                    $objCart = new Cart($idCart);
                    if (isset(Context::getContext()->controller->controller_type)) {
                        $controllerType = Context::getContext()->controller->controller_type;
                    } else {
                        $controllerType = 'front';
                    }
                    if ($controllerType == 'admin' || $controllerType == 'moduleadmin') {
                        if ($cartQty = Cart::getProductQtyInCart($idCart, (int) $product['id_product'])) {
                            if ($removedQuantity < $cartQty) {
                                $res &= Db::getInstance()->update(
                                    'cart_product',
                                    array('quantity' => (int)($cartQty - $removedQuantity)),
                                    '`id_product` = '.(int)$product['id_product'].' AND `id_cart` = '.(int)$idCart
                                );
                            } else {
                                //if room type has no qty remaining in cart then delete row
                                $res &= Db::getInstance()->delete(
                                    'cart_product',
                                    '`id_product` = '.(int)$product['id_product'].' AND `id_cart` = '.(int)$idCart
                                );
                            }
                        }
                    } else {
                        $res &= $objCart->updateQty((int)$removedQuantity, $product['id_product'], null, false, 'down');
                    }
                }
                if ($quantity) {
                    break;
                }
            }

            return $res;
        }

        return true;
    }

    public function delete()
    {
        $objCart = new Cart($this->id_cart);
        if ($specificPriceInfo = SpecificPrice::getSpecificPrice(
            (int)$this->id_product,
            0,
            $objCart->id_currency,
            0,
            0,
            1,
            0,
            0,
            $objCart->id,
            0,
            $this->htl_cart_booking_id
        )) {
            $objSpecificPrice = new SpecificPrice($specificPriceInfo['id_specific_price']);
            $objSpecificPrice->delete();
        }

        return parent::delete();
    }

    public static function validateServiceProductsInCart()
    {
        $context = Context::getContext();
        if ($cartProducts = $context->cart->getProducts()) {
            $objServiceProductCartDetail = new ServiceProductCartDetail();
            foreach ($cartProducts as $product) {
                if (!$product['active'] && !$product['booking_product']) {
                    if ($serviceProducts = $objServiceProductCartDetail->getServiceProductsInCart(
                        $context->cart->id,
                        [],
                        null,
                        null,
                        null,
                        $product['id_product']
                    )) {
                        foreach ($serviceProducts as $serviceProduct) {
                            $objServiceProductCartDetail->removeServiceProductByIdHtlCartBooking($serviceProduct['htl_cart_booking_id']);
                        }
                    }
                }
            }
        }
    }
}
