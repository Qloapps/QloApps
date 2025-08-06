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

class HotelAdvancedPayment extends ObjectModel
{
    public $id;
    public $id_product;
    public $payment_type;
    public $value;
    public $id_currency;
    public $tax_include;
    public $calculate_from;
    public $active;
    public $date_add;
    public $date_upd;

    const WK_ADVANCE_PAYMENT_TYPE_PERCENTAGE = 1;
    const WK_ADVANCE_PAYMENT_TYPE_FIXED = 2;

    public static $definition = array(
        'table' => 'htl_advance_payment',
        'primary' => 'id',
        'fields' => array(
            'id_product' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'payment_type' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'value' =>                    array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'id_currency' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'tax_include' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'calculate_from' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' =>                 array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
    ));

    protected $webserviceParameters = array(
        'objectsNodeName' => 'advance_payments',
        'objectNodeName' => 'advance_payment',
        'fields' => array(
            'id_product' => array(
                'xlink_resource' => array(
                    'resourceName' => 'room_types',
                )
            ),
        ),
    );

    /**
     * [getIdAdvPaymentByIdProduct :: To get Advance payment Information By id_product]
     * @param  [int] $id_product [id of the product which Advance payment Information you want]
     * @return [array|false]     [Returns array if information of advance payment of that id_product found otherwise returs false]
     */
    public function getIdAdvPaymentByIdProduct($id_product)
    {
        $result = Db::getInstance()->getRow("SELECT * FROM `"._DB_PREFIX_."htl_advance_payment` WHERE `id_product`=".(int) $id_product);

        if ($result) {
            return $result;
        }
        return false;
    }

    /**
     * Get the advance payment amount of the room type in the cart
     * @param [int] $idCart : cart id
     * @param [int] $idProduct : id_product of room type
     * @param integer $advGlobalPercent
     * @param integer $advGlobalTaxIncl
     * @param integer $withTaxes : Amount with(1) or without tax (0)
     * @return [float] advance payment amount of the room type in the cart
     */
    public function getProductMinAdvPaymentAmountByIdCart(
        $idCart,
        $idProduct,
        $advGlobalPercent = 0,
        $advGlobalTaxIncl = 0,
        $withTaxes = 1
    ) {
        if (!$advGlobalPercent) {
            $advGlobalPercent = Configuration::get('WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT');
        }
        if (!$advGlobalTaxIncl) {
            $advGlobalTaxIncl = Configuration::get('WK_ADVANCED_PAYMENT_INC_TAX');
        }

        $roomTypeTotalTI = 0;
        $roomTypeTotalTE = 0;
        $productCartQuantity = 0;
        $objHtlCartBook = new HotelCartBookingData();
        if ($roomTypesByIdProduct = $objHtlCartBook->getCartInfoIdCartIdProduct((int) $idCart, (int)$idProduct)) {
            foreach ($roomTypesByIdProduct as $cartRoomInfo) {
                $roomTotalPrices = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                    $cartRoomInfo['id_product'],
                    $cartRoomInfo['date_from'],
                    $cartRoomInfo['date_to'],
                    0,
                    0,
                    $idCart,
                    $cartRoomInfo['id_guest'],
                    $cartRoomInfo['id_room'],
                    0
                );

                $roomTypeTotalTI += $roomTotalPrices['total_price_tax_incl'];
                $roomTypeTotalTE += $roomTotalPrices['total_price_tax_excl'];
                $productCartQuantity += $cartRoomInfo['quantity'];
            }
        }

        $advPaymentAmount = 0;
        if ($prodAdvPayInfo = $this->getIdAdvPaymentByIdProduct($idProduct)) {
            if ($prodAdvPayInfo['active']) {
                // Advanced payment is calculated by product advanced payment setting
                if ($prodAdvPayInfo['calculate_from']) {
                    // room type original prices
                    $prodRawPriceTI = Product::getPriceStatic($idProduct, true, null, 6, null, false, true);
                    $prodRawPriceTE = Product::getPriceStatic($idProduct, false, null, 6, null, false, true);

                    if ($prodAdvPayInfo['payment_type'] == self::WK_ADVANCE_PAYMENT_TYPE_PERCENTAGE) { // Percentage
                        $advPaymentAmount = ($roomTypeTotalTE * $prodAdvPayInfo['value']) / 100 ;
                    } else {
                        $prodAdvPayInfo['value'] = Tools::convertPrice($prodAdvPayInfo['value']);

                        if ($prodRawPriceTE < $prodAdvPayInfo['value']) {
                            $advPaymentAmount = $prodRawPriceTE * $productCartQuantity;
                        } else {
                            $advPaymentAmount = $prodAdvPayInfo['value'] * $productCartQuantity;
                        }
                    }

                    // add taxes to the advance room type price
                    if ($withTaxes && $prodAdvPayInfo['tax_include']) {
                        if ($prodRawPriceTE) {
                            $taxRate = (($prodRawPriceTI - $prodRawPriceTE) / $prodRawPriceTE) * 100;
                        } else {
                            $taxRate = 0;
                        }
                        $taxRate = HotelRoomType::getRoomTypeTaxRate($idProduct);
                        $taxPrice = ($advPaymentAmount * $taxRate) / 100;
                        $advPaymentAmount += $taxPrice;
                    }
                } else { // Advanced payment is calculated by Global advanced payment setting
                    if ($advGlobalTaxIncl && $withTaxes) {
                        $advPaymentAmount = ($roomTypeTotalTI * $advGlobalPercent) / 100 ;
                    } else {
                        $advPaymentAmount = ($roomTypeTotalTE * $advGlobalPercent) / 100 ;
                    }
                }
            } else { // if advance payment is disabled for this room type then send the room type full price
                if ($withTaxes) {
                    $advPaymentAmount = $roomTypeTotalTI;
                } else {
                    $advPaymentAmount = $roomTypeTotalTE;
                }
            }
        } else { // if no advance payment info for the room type the calculate from Global settings
            if ($withTaxes && $advGlobalTaxIncl) {
                $advPaymentAmount = ($roomTypeTotalTI * $advGlobalPercent) / 100 ;
            } else {
                $advPaymentAmount = ($roomTypeTotalTE * $advGlobalPercent) / 100 ;
            }
        }

        return $advPaymentAmount;
    }

    /**
     * [getProductMinAdvPaymentAmount :: To get minimum advance payment amount paid by the customer for a particular product for a given quantities product]
     * @param  [int]  $id_product         [ID of the product which minimum advance payment amount paid by the customer for given quantities you want]
     * @param  [int]  $prod_qty           [Product quantity]
     * @param  float $adv_global_percent [How much percent customer has to pay of total order in case of partial payment]
     * @param  boolean $adv_global_tax_incl [How much percent customer has to pay of total order in case of partial payment will be calculated from tax included price or tax excluded price]
     * @return [float]                     [Returns the amount paid by the customer in advance for the product for given quantities]
     */
    public function getProductMinAdvPaymentAmount($id_product, $prod_qty, $adv_global_percent = 0, $adv_global_tax_incl = 0)
    {
        if (!$adv_global_percent) {
            $adv_global_percent = Configuration::get('WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT');
        }

        if (!$adv_global_tax_incl) {
            $adv_global_tax_incl = Configuration::get('WK_ADVANCED_PAYMENT_INC_TAX');
        }

        $price_with_tax = Product::getPriceStatic($id_product, true, null, 6, null, false, true, $prod_qty);
        $price_without_tax = Product::getPriceStatic($id_product, false, null, 6, null, false, true, $prod_qty);

        $prod_adv = $this->getIdAdvPaymentByIdProduct($id_product);

        if ($prod_adv) {
            if ($prod_adv['active']) {
                if ($prod_adv['calculate_from']) {
                    // Advanced payment is calculated by product advanced payment setting

                    if ($prod_adv['payment_type'] == self::WK_ADVANCE_PAYMENT_TYPE_PERCENTAGE) {
                        // Percentage

                        if ($prod_adv['tax_include']) {
                            $prod_price = $price_with_tax * $prod_qty;
                        } else {
                            $prod_price = $price_without_tax * $prod_qty;
                        }

                        $adv_amount = ($prod_price*$prod_adv['value'])/100 ;
                    } else {
                        $prod_adv['value'] = Tools::convertPrice($prod_adv['value']);

                        if ($prod_adv['tax_include']) {
                            //Fixed

                            if ($price_with_tax < $prod_adv['value']) {
                                $adv_amount = $price_with_tax * $prod_qty;
                            } else {
                                $adv_amount = $prod_adv['value'] * $prod_qty;
                            }
                        } else {
                            if ($price_without_tax < $prod_adv['value']) {
                                $adv_amount = $price_without_tax * $prod_qty;
                            } else {
                                $adv_amount = $prod_adv['value'] * $prod_qty;
                            }
                        }
                    }
                } else {
                    // Advanced payment is calculated by Global advanced payment setting

                    if ($adv_global_tax_incl) {
                        $prod_price = $price_with_tax * $prod_qty;
                    } else {
                        $prod_price = $price_without_tax * $prod_qty;
                    }

                    $adv_amount = ($prod_price*$adv_global_percent)/100 ;
                }
            } else {
                $prod_price = $price_with_tax * $prod_qty;
                $adv_amount = $prod_price;
            }
        } else {
            if ($adv_global_tax_incl) {
                $prod_price = $price_with_tax * $prod_qty;
            } else {
                $prod_price = $price_without_tax * $prod_qty;
            }

            $adv_amount = ($prod_price * $adv_global_percent)/100 ;
        }

        return $adv_amount;
    }

    /**
     * [getMinAdvPaymentAmount description :: To get minimum advance payment amount paid by the customer for all products in the cart]
     * @return [float] [Returns the amount paid by the customer in advance for the product for given quantities]
     */
    public function getMinAdvPaymentAmount()
    {
        $context = Context::getContext();

        $cart_product = $context->cart->getProducts();
        $adv_amount = 0;

        $adv_global_percent = Configuration::get('WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT');
        $adv_global_tax_incl = Configuration::get('WK_ADVANCED_PAYMENT_INC_TAX');

        foreach ($cart_product as $prod_key => $cart_prod) {
            $adv_amount += $this->getProductMinAdvPaymentAmountByIdCart($context->cart->id, $cart_prod['id_product']);
        }

        return $adv_amount;
    }

    /**
     * [getMinAdvPaymentAmount description :: To get minimum advance payment amount paid by the customer for all products in the cart]
     * @return [float] [Returns the amount paid by the customer in advance for the product for given quantities]
     */
    public function getOrderMinAdvPaymentAmount($id_order)
    {
        $order = new Order($id_order);

        $orderProducts = $order->getProducts();
        $adv_amount = 0;

        $adv_global_percent = Configuration::get('WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT');
        $adv_global_tax_incl = Configuration::get('WK_ADVANCED_PAYMENT_INC_TAX');

        foreach ($orderProducts as $prod_key => $product) {
            $adv_amount += $this->getProductMinAdvPaymentAmountByIdCart($order->id_cart, $product['id_product']);
        }

        return $adv_amount;
    }

    public function _checkFreeAdvancePaymentOrder()
    {
        if (Configuration::get('WK_ALLOW_ADVANCED_PAYMENT')) {
            $adv_amount = $this->getMinAdvPaymentAmount();
            $vouchersTotal = Context::getContext()->cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
            return ($adv_amount - $vouchersTotal) <= 0 ? true : false;
        }
        return false;
    }

    /**
     * Get the advance payment amount of the room type booking in the cart
     * @param [int] $idProduct : id_product of room type
     * * @param [date] $dateFrom : date from of the booking
     * * @param [date] $dateFrom : date to of the booking
     * @param integer $withTaxes : Amount with(1) or without tax (0)
     * @return [float] advance payment amount of the room type in the cart
     */
    public function getRoomMinAdvPaymentAmount($idProduct, $dateFrom, $dateTo, $withTaxes = 1, $idRoom = 0, $idCart = 0, $idGuest = 0)
    {
        $dateFrom = date('Y-m-d', strtotime($dateFrom));
        $dateTo = date('Y-m-d', strtotime($dateTo));

        // Advance payment information from global settings
        $advGlobalPercent = Configuration::get('WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT');
        $advGlobalTaxIncl = Configuration::get('WK_ADVANCED_PAYMENT_INC_TAX');

        $roomTotalPrices = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
            $idProduct,
            $dateFrom,
            $dateTo,
            0,
            0,
            $idCart,
            $idGuest,
            $idRoom,
            0
        );
        $roomTypeTotalTI = $roomTotalPrices['total_price_tax_incl'];
        $roomTypeTotalTE = $roomTotalPrices['total_price_tax_excl'];

        $advPaymentAmount = 0;
        if ($prodAdvPayInfo = $this->getIdAdvPaymentByIdProduct($idProduct)) {
            if ($prodAdvPayInfo['active']) {
                // Advanced payment is calculated by product advanced payment setting
                if ($prodAdvPayInfo['calculate_from']) {
                    // room type original prices
                    $prodRawPriceTI = Product::getPriceStatic($idProduct, true, null, 6, null, false, true);
                    $prodRawPriceTE = Product::getPriceStatic($idProduct, false, null, 6, null, false, true);

                    if ($prodAdvPayInfo['payment_type'] == self::WK_ADVANCE_PAYMENT_TYPE_PERCENTAGE) { // Percentage
                        $advPaymentAmount = ($roomTypeTotalTE * $prodAdvPayInfo['value']) / 100 ;
                    } else {
                        $prodAdvPayInfo['value'] = Tools::convertPrice($prodAdvPayInfo['value']);

                        $numdays = 0;
                        $objBookingDtl = new HotelBookingDetail();
                        $numdays = $objBookingDtl->getNumberOfDays($dateFrom, $dateTo);

                        if ($prodRawPriceTE < $prodAdvPayInfo['value']) {
                            $advPaymentAmount = $prodRawPriceTE * $numdays;
                        } else {
                            $advPaymentAmount = $prodAdvPayInfo['value'] * $numdays;
                        }
                    }

                    // add taxes to the advance room type price
                    if ($withTaxes && $prodAdvPayInfo['tax_include']) {
                        if ($prodRawPriceTE) {
                            $taxRate = (($prodRawPriceTI - $prodRawPriceTE) / $prodRawPriceTE) * 100;
                        } else {
                            $taxRate = 0;
                        }
                        $taxRate = HotelRoomType::getRoomTypeTaxRate($idProduct);
                        $taxPrice = ($advPaymentAmount * $taxRate) / 100;
                        $advPaymentAmount += $taxPrice;
                    }
                } else { // Advanced payment is calculated by Global advanced payment setting
                    if ($advGlobalTaxIncl && $withTaxes) {
                        $advPaymentAmount = ($roomTypeTotalTI * $advGlobalPercent) / 100 ;
                    } else {
                        $advPaymentAmount = ($roomTypeTotalTE * $advGlobalPercent) / 100 ;
                    }
                }
            } else { // if advance payment is disabled for this room type then send the room type full price
                if ($withTaxes) {
                    $advPaymentAmount = $roomTypeTotalTI;
                } else {
                    $advPaymentAmount = $roomTypeTotalTE;
                }
            }
        } else { // if no advance payment info for the room type the calculate from Global settings
            if ($withTaxes && $advGlobalTaxIncl) {
                $advPaymentAmount = ($roomTypeTotalTI * $advGlobalPercent) / 100 ;
            } else {
                $advPaymentAmount = ($roomTypeTotalTE * $advGlobalPercent) / 100 ;
            }
        }

        return $advPaymentAmount;
    }

    // check if advance payment is available for the current cart
    public function isAdvancePaymentAvailableForCurrentCart()
    {
        if (Configuration::get('WK_ALLOW_ADVANCED_PAYMENT')) {
            $context = Context::getContext();
            // check if there is any ptoduct in the cart with advance payment option available
            if ($cartProducts = $context->cart->getProducts()) {
                foreach ($cartProducts as $product) {
                    $idProduct = $product['id_product'];
                    if ($advPaymentInfo = $this->getIdAdvPaymentByIdProduct($idProduct)) {
                        if ($advPaymentInfo['active']) {
                            // if no impact on price with advancne payment then no need of advance payment
                            if ($context->cart->getOrderTotal() == $context->cart->getOrderTotal(true, CART::ADVANCE_PAYMENT)) {
                                return false;
                            }

                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }
}
