<?php
/**
* 2010-2021 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2021 Webkul IN
* @license LICENSE.txt
*/

class WkPaypalCommerceHelper
{
    // Validate PayPal credentials
    public static function getAccessToken()
    {
        $apiResp = array();
        if (Tools::isSubmit('submit_paypal_commerce')) {
            $wkClientID = trim(Tools::getValue('WK_PAYPAL_COMMERCE_CLIENT_ID'));
            $wkClientSecret = trim(Tools::getValue('WK_PAYPAL_COMMERCE_CLIENT_SECRET'));
            $wkEnvironment = Tools::getValue('WK_PAYPAL_COMMERCE_PAYMENT_MODE');
        } else {
            $wkClientID = trim(Configuration::get('WK_PAYPAL_COMMERCE_CLIENT_ID'));
            $wkClientSecret = trim(Configuration::get('WK_PAYPAL_COMMERCE_CLIENT_SECRET'));
            $wkEnvironment = Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE');
        }

        $base_url = ($wkEnvironment == 'sandbox') ? PayPalHelper::WK_PAYPAL_SANDBOX_URL : PayPalHelper::WK_PAYPAL_LIVE_URL;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $base_url . "/" . PayPalHelper::WK_PAYPAL_ACCESS_TOKEN_URI,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "grant_type=client_credentials",
            CURLOPT_HTTPHEADER => array(
                "PayPal-Partner-Attribution-Id: " . PayPalHelper::WK_PAYPAL_COMMERCE_ATTRIBUTION_ID,
                "authorization: Basic " . base64_encode($wkClientID . ":" . $wkClientSecret),
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new PrestaShopException(sprintf('cURL Error #: %s', $err));
        } else {
            $accessToken = Tools::jsonDecode($response, true);
            if (isset($accessToken['error']) && !empty($accessToken['error'])) {
                $apiResp['success'] = false;
                $apiResp['message'] = $accessToken['error_description'];
            } else {
                $apiResp['success'] = true;
                $apiResp['access_token'] = $accessToken['access_token'];
            }
        }

        return $apiResp;
    }

    public static function payPalAuthAssertion()
    {
        $temp = array(
            "alg" => "none"
        );
        $returnData = base64_encode(Tools::jsonEncode($temp)) . '.';
        $temp = array(
            "iss" => trim(Configuration::get('WK_PAYPAL_COMMERCE_CLIENT_ID')),
            "payer_id" => Configuration::get('WK_PAYPAL_COMMERCE_MERCHANT_ID')
        );
        $returnData .= base64_encode(Tools::jsonEncode($temp)) . '.';
        return $returnData;
    }

    /**
     * Create webhook
     */
    public static function createWebhookUrl($token)
    {
        $apiResp = array();
        if ($token) {
            $wkEnvironment = Tools::getValue('WK_PAYPAL_COMMERCE_PAYMENT_MODE');
            if ($wkEnvironment == 'sandbox') {
                $base_url = PayPalHelper::WK_PAYPAL_SANDBOX_URL;
                $webhookUrl = Context::getContext()->link->getModuleLink('qlopaypalcommerce', 'webhook', array(), true);
            } else {
                $base_url = PayPalHelper::WK_PAYPAL_LIVE_URL;
                $webhookUrl = Context::getContext()->link->getModuleLink('qlopaypalcommerce', 'callback', array(), true);
            }

            $curl = curl_init();

            $postData = array(
                'url' => $webhookUrl,
                'event_types' => array(
                    array(
                        'name' => 'CHECKOUT.ORDER.APPROVED',
                    ),
                    array(
                        'name' => 'CHECKOUT.ORDER.COMPLETED',
                    ),
                    array(
                        'name' => 'PAYMENT.CAPTURE.COMPLETED',
                    ),
                    array(
                        'name' => 'PAYMENT.CAPTURE.DENIED',
                    ),
                    array(
                        'name' => 'PAYMENT.CAPTURE.PENDING',
                    ),
                    array(
                        'name' => 'PAYMENT.CAPTURE.REFUNDED',
                    ),
                    array(
                        'name' => 'PAYMENT.CAPTURE.REVERSED',
                    )
                )
            );

            curl_setopt_array($curl, array(
                CURLOPT_URL => $base_url . "/v1/notifications/webhooks",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => Tools::jsonEncode($postData),
                CURLOPT_HTTPHEADER => array(
                    "PayPal-Partner-Attribution-Id: " . PayPalHelper::WK_PAYPAL_COMMERCE_ATTRIBUTION_ID,
                    "authorization: Bearer " . $token,
                    "cache-control: no-cache",
                    "content-type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                throw new PrestaShopException(sprintf('cURL Error #: %s', $err));
            } else {
                $webhookResponse = Tools::jsonDecode($response, true);
                if (isset($webhookResponse['id']) && !empty($webhookResponse['id'])) {
                    $apiResp['success'] = true;
                    $apiResp['webhook_id'] = $webhookResponse['id'];
                } elseif (isset($webhookResponse['error']) && !empty($webhookResponse['error'])) {
                    $apiResp['success'] = false;
                    $apiResp['message'] = $webhookResponse['error_description'];
                } elseif ($webhookResponse['name'] != 'WEBHOOK_URL_ALREADY_EXISTS') {
                    $apiResp['success'] = false;
                    $apiResp['message'] = $webhookResponse['name'];
                    if (isset($webhookResponse['details'][0]['issue'])) {
                        $apiResp['message'] .= " : " . $webhookResponse['details'][0]['issue'];
                    }
                }
            }
        }

        return $apiResp;
    }

    public static function deleteWebhookUrl()
    {
        $accessToken = self::getAccessToken();
        if ($accessToken['success']) {
            $wkEnvironment = Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE');
            if ($wkEnvironment == 'sandbox') {
                $base_url = PayPalHelper::WK_PAYPAL_SANDBOX_URL;
                $webhookId = Configuration::get('WK_PAYPAL_COMMERCE_SANDBOX_WEBHOOK_ID');
            } else {
                $base_url = PayPalHelper::WK_PAYPAL_LIVE_URL;
                $webhookId = Configuration::get('WK_PAYPAL_COMMERCE_LIVE_WEBHOOK_ID');
            }

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $base_url . "/v1/notifications/webhooks/" . $webhookId,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "DELETE",
                CURLOPT_HTTPHEADER => array(
                    "PayPal-Partner-Attribution-Id: " . PayPalHelper::WK_PAYPAL_COMMERCE_ATTRIBUTION_ID,
                    "authorization: Bearer " . $accessToken['access_token'],
                    "cache-control: no-cache",
                    "content-type: application/json"
                ),
            ));

            curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                throw new PrestaShopException(sprintf('cURL Error #: %s', $err));
            }
        }

        return true;
    }

    public static function validateWebhookSig($postData)
    {
        $apiResp = array();
        $accessToken = self::getAccessToken();
        if ($postData && $accessToken['success']) {
            $wkEnvironment = Configuration::get('WK_PAYPAL_COMMERCE_PAYMENT_MODE');
            $base_url = ($wkEnvironment == 'sandbox') ? PayPalHelper::WK_PAYPAL_SANDBOX_URL : PayPalHelper::WK_PAYPAL_LIVE_URL;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $base_url . "/v1/notifications/verify-webhook-signature",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => Tools::jsonEncode($postData),
                CURLOPT_HTTPHEADER => array(
                    "PayPal-Partner-Attribution-Id: " . PayPalHelper::WK_PAYPAL_COMMERCE_ATTRIBUTION_ID,
                    "authorization: Bearer " . $accessToken['access_token'],
                    "cache-control: no-cache",
                    "content-type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                throw new PrestaShopException(sprintf('cURL Error #: %s', $err));
            } else {
                $apiResp = Tools::jsonDecode($response, true);
            }
        }
        return $apiResp;
    }

    public function getProductName($idProduct)
    {
        $idLang = (int)Context::getContext()->language->id;
        $productObj = new Product((int)$idProduct, false, (int)$idLang);
        return $productObj->name;
    }

    /**
     * logMsg
     * @param  mixed $logMsg
     * @return bool
     */
    public static function logMsg($logType, $logMsg, $newLine = false)
    {
        $file = fopen(dirname(__FILE__).'/../log/'.$logType.'.log', 'a');
        $error_msg = $newLine ? "\r\n\n" : "\n";
        $error_msg .= date('d-m-Y H:i:s').'  ----  '.$logMsg;
        fwrite($file, $error_msg);
        fclose($file);
        return true;
    }

    public function getSimpleAddress($idCustomer, $idAddress = null, $idLang = null)
    {
        if (null === $idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $sql = 'SELECT DISTINCT
                      a.`id_address` AS `id`,
                      a.`alias`,
                      a.`firstname`,
                      a.`lastname`,
                      a.`company`,
                      a.`address1`,
                      a.`address2`,
                      a.`postcode`,
                      a.`city`,
                      a.`id_state`,
                      s.name AS state,
                      s.`iso_code` AS state_iso,
                      a.`id_country`,
                      cl.`name` AS country,
                      co.`iso_code` AS country_iso,
                      a.`other`,
                      a.`phone`,
                      a.`phone_mobile`,
                      a.`vat_number`,
                      a.`dni`
                    FROM `' . _DB_PREFIX_ . 'address` a
                    LEFT JOIN `' . _DB_PREFIX_ . 'country` co ON (a.`id_country` = co.`id_country`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (co.`id_country` = cl.`id_country`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'state` s ON (s.`id_state` = a.`id_state`)
                    WHERE `id_lang` = ' . (int) $idLang . '
                    AND `id_customer` = ' . (int) $idCustomer . '
                    AND a.`deleted` = 0
                    AND a.`active` = 1';

        if (null !== $idAddress) {
            $sql .= ' AND a.`id_address` = ' . (int) $idAddress;
        }

        return Db::getInstance()->getRow($sql);
    }

    public static function getOrdersByCartId($idCart)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'orders` WHERE `id_cart` = '.(int) $idCart
        );
    }

    public function getPaypalOrderItemDetails($idCart)
    {
        $items = array();

        $objCart = new Cart($idCart);
        if ($cartProducts = $objCart->getProducts()) {
            $objCartBookingData = new HotelCartBookingData();
            $currency = Currency::getCurrency((int) $objCart->id_currency);

            foreach ($cartProducts as $product) {
                $idProduct = $product['id_product'];
                if ($roomTypeBookings = $objCartBookingData->getCartInfoIdCartIdProduct(
                    $objCart->id,
                    $idProduct
                )) {
                    foreach ($roomTypeBookings as $cartRoomInfo) {
                        $roomTotalPrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                            $idProduct,
                            $cartRoomInfo['date_from'],
                            $cartRoomInfo['date_to']
                        );
                        $roomTotalPriceTE = $roomTotalPrice['total_price_tax_excl'];
                        $roomTotalPriceTI = $roomTotalPrice['total_price_tax_incl'];

                        if ($objCart->is_advance_payment) {
                            $roomTotalPriceTI = $this->getRoomMinAdvPaymentAmount(
                                $idProduct,
                                $cartRoomInfo['date_from'],
                                $cartRoomInfo['date_to'],
                                1
                            );

                            $roomTotalPriceTE = $this->getRoomMinAdvPaymentAmount(
                                $idProduct,
                                $cartRoomInfo['date_from'],
                                $cartRoomInfo['date_to'],
                                0
                            );
                        }

                        $dateJoin = $idProduct.'_'.strtotime($cartRoomInfo['date_from']).strtotime($cartRoomInfo['date_to']);

                        if (isset($items[$dateJoin])) {
                            // quantity
                            $items[$dateJoin]['quantity'] += 1;
                        } else {
                            $items[$dateJoin]['name'] = $product['name'];
                            $items[$dateJoin]['sku'] = $product['reference'];
                            $items[$dateJoin]['category'] = 'DIGITAL_GOODS';

                            // $items[$dateJoin]['description'] = Tools::substr($product['description_short'], 0, 127);

                            $items[$dateJoin]['description'] = 'Date From : '.date('d-m-Y', strtotime($cartRoomInfo['date_from'])).', Date To : '.date('d-m-Y', strtotime($cartRoomInfo['date_to']));

                            // unit amount
                            $items[$dateJoin]['unit_amount']['value'] = Tools::ps_round($roomTotalPriceTE, 2);
                            $items[$dateJoin]['unit_amount']['currency_code'] = $currency['iso_code'];

                            // tax values
                            $tax = $roomTotalPriceTI - $roomTotalPriceTE;
                            $items[$dateJoin]['tax']['value'] = Tools::ps_round($tax, 2);
                            $items[$dateJoin]['tax']['currency_code'] = $currency['iso_code'];

                            // quantity
                            $items[$dateJoin]['quantity'] = 1;
                        }
                    }
                }
            }

            $items = array_values($items);
            $itemKey = count($items);

            if ($totalFacilityCostTE = $objCartBookingData->getCartExtraDemands($objCart->id, 0, 0, 0, 0, 1, 0, 0)) {
                $totalFacilityCostTI = $objCartBookingData->getCartExtraDemands($objCart->id, 0, 0, 0, 0, 1, 0, 1);
                $items[$itemKey]['name'] = 'Extra facilities';
                // $items[$itemKey]['sku'] = $product['reference'];
                $items[$itemKey]['category'] = 'DIGITAL_GOODS';

                $items[$itemKey]['description'] = 'Price of all Extra facilities added to the bookings';

                // unit amount
                $items[$itemKey]['unit_amount']['value'] = Tools::ps_round($totalFacilityCostTE, 2);
                $items[$itemKey]['unit_amount']['currency_code'] = $currency['iso_code'];

                // tax values
                $tax = $totalFacilityCostTI - $totalFacilityCostTE;
                $items[$itemKey]['tax']['value'] = Tools::ps_round($tax, 2);
                $items[$itemKey]['tax']['currency_code'] = $currency['iso_code'];

                // quantity
                $items[$itemKey]['quantity'] = 1;
            }
        }

        return $items;
    }

    // returns all the currencies supported by paypal
    public static function checkPaypalCurrencySuuport($idCart)
    {
        $objCart = new Cart($idCart);
        $objCurr = new Currency($objCart->id_currency);

        $supportedCurr = array ('AUD', 'BRL', 'CAD', 'CNY', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN',
        'TWD', 'NZD', 'NOK', 'PHP', 'PLN', 'GBP', 'RUB', 'SGD', 'SEK', 'CHF', 'THB', 'USD');

        if (in_array(Tools::strtoupper($objCurr->iso_code), $supportedCurr)) {
            return true;
        }

        return false;
    }

    // function to find advance payment amount for date ranges
    public function getRoomMinAdvPaymentAmount($idProduct, $dateFrom, $dateTo, $withTaxes = 1)
    {
        $dateFrom = date('Y-m-d', strtotime($dateFrom));
        $dateTo = date('Y-m-d', strtotime($dateTo));
        $advGlobalPercent = Configuration::get('WK_ADVANCED_PAYMENT_GLOBAL_MIN_AMOUNT');
        $advGlobalTI = Configuration::get('WK_ADVANCED_PAYMENT_INC_TAX');
        $rmQty = 0;

        $objBookingDetail = new HotelBookingDetail();
        $roomTypePriceRawTI = Product::getPriceStatic($idProduct, true, null, 6, null, false, true);
        $roomTypePriceRawTE = Product::getPriceStatic($idProduct, false, null, 6, null, false, true);

        $roomTotalPrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice($idProduct, $dateFrom, $dateTo);

        $roomTypeTotalTI = $roomTotalPrice['total_price_tax_incl'];
        $roomTypeTotalTE = $roomTotalPrice['total_price_tax_excl'];

        $rmQty = $objBookingDetail->getNumberOfDays($dateFrom, $dateTo);

        $objAdvPayment = new HotelAdvancedPayment();
        if ($advInfo = $objAdvPayment->getIdAdvPaymentByIdProduct($idProduct)) {
            if ($advInfo['active']) {
                // Advanced payment is calculated by product advanced payment setting
                if ($advInfo['calculate_from']) {
                    // Percentage
                    if ($advInfo['payment_type'] == HotelAdvancedPayment::WK_ADVANCE_PAYMENT_TYPE_PERCENTAGE) {
                        if ($advInfo['tax_include']) {
                            $prodPrice = $roomTypeTotalTI;
                        } else {
                            $prodPrice = $roomTypeTotalTE;
                        }
                        $advPrice = ($prodPrice*$advInfo['value'])/100 ;
                    } else {
                        $advInfo['value'] = Tools::convertPrice($advInfo['value']);

                        if ($advInfo['tax_include']) { //Fixed
                            if ($roomTypePriceRawTI < $advInfo['value']) {
                                $advPrice = $roomTypeTotalTI;
                            } else {
                                $advPrice = $advInfo['value'] * $rmQty;
                            }
                        } else {
                            if ($roomTypePriceRawTE < $advInfo['value']) {
                                $advPrice = $roomTypePriceRawTE * $rmQty;
                            } else {
                                $advPrice = $advInfo['value'] * $rmQty;
                            }
                        }
                        if ($withTaxes) {
                            if ($roomTypePriceRawTE) {
                                $taxRate = (($roomTypePriceRawTI-$roomTypePriceRawTE)/$roomTypePriceRawTE)*100;
                            } else {
                                $taxRate = 0;
                            }

                            $taxRate = HotelRoomType::getRoomTypeTaxRate($idProduct);
                            $taxPrice = ($advPrice * $taxRate) / 100;
                            $advPrice += $taxPrice;
                        }
                    }
                } else { // Advanced payment is calculated by Global advanced payment setting
                    if ($advGlobalTI && $withTaxes) {
                        $advPrice = ($roomTypeTotalTI*$advGlobalPercent)/100 ;
                    } else {
                        $advPrice = ($roomTypeTotalTE*$advGlobalPercent)/100 ;
                    }
                }
            } else {
                $prodPrice = $roomTypeTotalTI;
                $advPrice = $prodPrice;
            }
        } else {
            if ($advGlobalTI) {
                $advPrice = ($roomTypeTotalTI * $advGlobalPercent) / 100 ;
            } else {
                $advPrice = ($roomTypeTotalTE * $advGlobalPercent) / 100 ;
            }
        }

        return $advPrice;
    }
}
