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

class WebserviceSpecificManagementCmApi implements WebserviceSpecificManagementInterface
{
    public const PMS_TOKEN = '86KqaQNAYdR4';
    public const API_BOOKING_STATUS_NEW = 'new';
    public const API_BOOKING_STATUS_COMPLETED = 'completed';
    public const API_BOOKING_STATUS_CANCELLED = 'cancelled';
    public const API_BOOKING_STATUS_REFUNDED = 'refunded';

    public const API_BOOKING_PAYMENT_STATUS_COMPLETED = 'paid';
    public const API_BOOKING_PAYMENT_STATUS_AWATING = 'unpaid';
    public const API_BOOKING_PAYMENT_STATUS_PARTIAL = 'partial';

    public const API_CART_RULE_VALUE_TYPE_AMOUNT = 1;
    public const API_CART_RULE_VALUE_TYPE_PERCENTAGE = 2;

    public const API_SERVICE_PRICE_MODE_PER_BOOKING = 1;
    public const API_SERVICE_PRICE_MODE_PER_NIGHT = 2;
    public const API_SERVICE_PRICE_MODE_PER_PERSON_PER_NIGHT = 3;

    public const SERVICE_PRICE_MODE_PER_STAY = 'per_stay';
    public const SERVICE_PRICE_MODE_PER_NIGHT = 'per_night';
    public const SERVICE_PRICE_MODE_PER_PERSON_PER_NIGHT = 'per_person_per_night';


    protected $objOutput;
    protected $output;
    protected $wsObject;
    protected $outputFormat;
    public $wsRequestedRoomTypes = array();
    public $wsRequestedRooms = array();
    public $wsIdServices = array();
    public $bookingCustomer;
    public $booking_type = false;
    public $wsTaxRulesGroup = array();
    public $wsFeaturePrices = array();
    public $wsCartRules = array();
    public $context;
    protected $error_msg = '';
    protected $id_hotel;

    protected $allowedMethods = array(
        'GET' => array(),
        'POST' => array()
    );

    public function setObjectOutput(WebserviceOutputBuilderCore $obj)
    {
        $this->objOutput = $obj;
        return $this;
    }

    public function getObjectOutput()
    {
        return $this->objOutput;
    }

    public function setWsObject(WebserviceRequestCore $obj)
    {
        $this->wsObject = $obj;

        return $this;
    }

    public function getWsObject()
    {
        return $this->wsObject;
    }

    public function manage()
    {
        $this->objOutput->setHeaderParams('pms_token', self::PMS_TOKEN);
        $this->context = Context::getContext();
        $objQloCMConnector = Module::getInstanceByName('qlocmconnector');
        $objQcmcProperty = new QcmcProperty($this->objOutput, $this->wsObject, $this->output, $this->outputFormat);
        $objQcmcRoomType = new QcmcRoomType($this->objOutput, $this->wsObject, $this->output, $this->outputFormat);
        $objQcmcBooking = new QcmcBooking($this->objOutput, $this->wsObject, $this->output, $this->outputFormat);
        $objAri = new QcmcAri($this->objOutput, $this->wsObject, $this->output, $this->outputFormat);

        switch ($this->wsObject->method) {
            case 'GET':
                switch ($this->wsObject->urlSegment[1]) {
                    case 'test_connection':
                        $this->output = json_encode([
                            "success" => true,
                            "QLO_VERSION" => _QLOAPPS_VERSION_
                        ]);
                        break;
                    case 'properties':
                        $this->output = $objQcmcProperty->getHotelList();
                        break;
                    case 'room_types':
                        if ($this->validateFilters(Tools::getValue('filter')) && isset(Tools::getValue('filter')['id_property'])) {
                            $this->output = $objQcmcRoomType->getRoomTypeList(Tools::getValue('filter')['id_property']);
                        } else {
                            $errorResponse = ["success" => false, "error" => $objQloCMConnector->l('id_property is required!')];
                            echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                            exit;
                        }
                        break;
                    case 'bookings':
                        $inputData = Tools::getAllValues();
                        if (!isset($inputData['filter'])) {
                            $inputData['filter']['id_property'] = false;
                        }

                        if ($this->validateFilters(Tools::getValue('filter')) && isset(Tools::getValue('filter')['id_property'])) {
                            $this->output = $objQcmcBooking->getBookingList($inputData['filter']);
                        } else {
                            $errorResponse = ["success" => false, "error" => $objQloCMConnector->l('id_property is required!')];
                            echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                            exit;
                        }
                        break;
                    case 'ari':
                        $inputData = Tools::getAllValues();
                        if (!isset($inputData['filter'])) {
                            $inputData['filter']['id_property'] = false;
                        }

                        if ($this->validateFilters(Tools::getValue('filter')) && isset(Tools::getValue('filter')['id_property'])) {
                            $this->output = $objAri->getAriByIdProperty($inputData['filter']);
                        } else {
                            $errorResponse = ["success" => false, "error" => $objQloCMConnector->l('id_property is required!')];
                            echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                            exit;
                        }
                        break;
                    default:
                        $errorResponse = ["success" => false, "error" => $objQloCMConnector->l('This HTTP method is not allowed')];
                        echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                        exit;
                }
                break;
            case 'POST':
                switch ($this->wsObject->urlSegment[1]) {
                    case 'booking_notification':
                        $inputData = $this->getPostRequest();
                        $this->id_hotel = $inputData['id_property'];
                        $this->handleRoomTypePriceWithService($inputData);
                        $this->processCustomerDetails($inputData);
                        if ($inputData['booking_status'] == self::API_BOOKING_STATUS_NEW || !Validate::isLoadedObject(new Order($inputData['id_booking']))) {
                            $inputData['booking_status'] = self::API_BOOKING_STATUS_NEW;
                            $this->booking_type = self::API_BOOKING_STATUS_NEW;
                            if ($this->validatePostRequest($inputData)) {
                                $this->handlePostRequest($inputData);
                                $this->deleteWsServices();
                                $this->deleteWsFeaturePrices();
                                $this->deleteWsCartRules();
                                $this->getResponseJson();
                            } else {
                                $errorResponse = ["success" => false, "error" => $objQloCMConnector->l($this->error_msg)];
                                echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                                exit;
                            }
                        } else {
                            if ($this->validatePutRequest($inputData)) {
                                $this->handlePutRequest($inputData);
                                $this->deleteWsServices();
                                $this->deleteWsFeaturePrices();
                                $this->deleteWsCartRules();
                                $this->getResponseJson();
                            } else {
                                $errorResponse = ["success" => false, "error" => $objQloCMConnector->l($this->error_msg)];
                                echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                                exit;
                            }
                        }
                        break;
                    default:
                        $errorResponse = ["success" => false, "error" => $objQloCMConnector->l('This HTTP method is not allowed')];
                        echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                        exit;
                }
        }

        return $this->wsObject->getOutputEnabled();
    }

    public function validateFilters($filters = [])
    {
        $objQloCMConnector = Module::getInstanceByName('qlocmconnector');
        $allowedFilters = [
            'id_property' => 'int',
            'id_room_type' => 'string',
            'date_updated' => ['gte', 'gt', 'lt', 'lte'],
            'check_out' => ['gte', 'gt', 'lt', 'lte'],
            'check_in' => ['gte', 'gt', 'lt', 'lte']
        ];

        $validatedFilters = [];

        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                if (!isset($allowedFilters[$key]) || !is_array($allowedFilters[$key])) {
                    $errorResponse = ["success" => false, "error" => $objQloCMConnector->l("Invalid filter: $key")];
                    echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    exit;
                }

                foreach ($value as $operator => $subValue) {
                    if (!in_array($operator, $allowedFilters[$key])) {
                        $errorResponse = ["success" => false, "error" => $objQloCMConnector->l("Invalid filter operator: $operator for $key")];
                        echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                        exit;
                    }

                    if ($key === 'date_updated' && !$this->validateDate($subValue)) {
                        $errorResponse = ["success" => false, "error" => $objQloCMConnector->l("Invalid date format for $key[$operator]. Expected YYYY-MM-DD.")];
                        echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                        exit;
                    }

                    $validatedFilters["filter[$key][$operator]"] = $subValue;
                }
            } else {
                if (!isset($allowedFilters[$key])) {
                    $errorResponse = ["success" => false, "error" => $objQloCMConnector->l("Invalid filter: $key")];
                    echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    exit;
                }

                if ($key === 'id_property' && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $errorResponse = ["success" => false, "error" => $objQloCMConnector->l("Invalid value for id_property. Must be an integer.")];
                    echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    exit;
                }

                $validatedFilters["filter[$key]"] = $value;
            }
        }

        return $validatedFilters;
    }

    // Helper function to validate date format
    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }


    /**
     * Creating feature price for the room.
     * Also they will be deleted after the booking has been created or updated.
     */
    public function createFeaturePrice($params)
    {
        $numDays = HotelHelper::getNumberOfDays($params['date_from'], $params['date_to']);
        if (!$numDays) {
            $numDays = 1;
        }

        if ($this->context->cart->id_currency != (int)Configuration::get('PS_CURRENCY_DEFAULT')) {
            $currency = Currency::getCurrencyInstance($this->context->cart->id_currency);
            $params['price'] = Tools::ps_round($params['price']/$currency->conversion_rate, 6);
        }

        $params['name'] = 'Api-Booking-Price';
        $params['impact_type'] = HotelRoomTypeFeaturePricing::IMPACT_TYPE_FIXED_PRICE;
        $params['impact_value'] = $params['price']/$numDays;
        $params['is_special_days_exists'] = 0;
        $params['special_days'] = json_encode(false);
        $params['restrictions'] = array(
            array(
                'date_from' => $params['date_from'],
                'date_to' => $params['date_to']
            )
        );

        return HotelRoomTypeFeaturePricing::createRoomTypeFeaturePrice($params);
    }

    /**
     * Get the JSON response.
     */
    public function getResponseJson()
    {
        if ($this->output && is_array($this->output)) {
            $this->output = json_encode($this->output);
        }
    }

    public function getContent()
    {

        $content = $this->output;
        $array = json_decode($content, true);

        if (!is_array($array)) {
            $outputXML = simplexml_load_string($content, null, LIBXML_NOCDATA);
            $content = json_encode($outputXML);
        }

        $this->objOutput->setHeaderParams('Content-Type', 'application/json');
        $content = preg_replace_callback(
            '/\\\\u([a-f0-9]{4})/',
            function ($matches) {
                return iconv(
                    'UCS-4LE',
                    'UTF-8',
                    pack(
                        'V',
                        hexdec($matches[1])
                    )
                );
            },
            $content
        );

        header('Content-Type: application/json');

        return $content;
    }

    /**
     * Validating the POST request.
     */
    public function validatePostRequest($params)
    {
        $this->error_msg = '';
        if (isset($params['id'])) {
            $this->error_msg = Tools::displayError('id is forbidden when adding a new resource');
        } elseif (!isset($params['currency'])
            || !$params['currency']
            || !Currency::getIdByIsoCode($params['currency'])
            || (!Validate::isLoadedObject((new Currency(Currency::getIdByIsoCode($params['currency'])))))
        ) {
            $this->error_msg = Tools::displayError('Please provide valid currency.');
        } elseif (!isset($params['guest_detail'])
            || !$params['guest_detail']
        ) {
            $this->error_msg = Tools::displayError('Customer details not found.');
        } elseif (!isset($params['id_property'])
            || !Validate::isLoadedObject(new HotelBranchInformation((int) $params['id_property']))
        ) {
            $this->error_msg = Tools::displayError('Please provide a valid id_property.');
        } elseif (isset($params['guest_detail']['id_customer'])
            && $params['guest_detail']['id_customer']
            && !Validate::isLoadedObject(new Customer((int) $params['guest_detail']['id_customer']))
        ) {
            $this->error_msg = Tools::displayError('Please provide a valid id_customer.');
        } elseif (!isset($params['guest_detail']['firstname'])
            || !$params['guest_detail']['firstname']
            || empty(trim($params['guest_detail']['firstname']))
        ) {
            $this->error_msg = Tools::displayError('Please provide a valid first name.');
        } elseif (!isset($params['guest_detail']['lastname'])
            || !$params['guest_detail']['lastname']
            || empty(trim($params['guest_detail']['lastname']))
        ) {
            $this->error_msg = Tools::displayError('Please provide a valid last name.');
        } elseif (!isset($params['guest_detail']['email'])
            || !$params['guest_detail']['email']
            || empty(trim($params['guest_detail']['email']))
        ) {
            $this->error_msg = Tools::displayError('Please provide a valid email.');
        } elseif (Configuration::get('PS_ONE_PHONE_AT_LEAST')
            && (!isset($params['guest_detail']['phone']) || !$params['guest_detail']['phone'] || empty(trim($params['guest_detail']['phone'])))
        ) {
            $this->error_msg = Tools::displayError('Please provide a valid phone number.');
        } elseif (isset($params['price_details']['total_price_with_tax'])
            && $params['price_details']['total_price_with_tax']
            && (!Validate::isPrice($params['price_details']['total_price_with_tax']))
        ) {
            $this->error_msg = Tools::displayError('Invalid value for total paid.');
        } elseif (isset($params['price_details']['total_price_with_tax'])
            && $params['price_details']['total_price_with_tax']
            && (!Validate::isPrice($params['price_details']['total_price_with_tax']))
        ) {
            $this->error_msg = Tools::displayError('Invalid value for total amount with tax.');
        } elseif (!$this->validateAddressFields($params['guest_detail'])
            && $this->error_msg == ''
        ) {
            $this->error_msg = Tools::displayError('Invalid address provided.');
        } elseif (!$this->validateRequestedRoomTypes($params['room_bookings'], $params['id_property'])
            && $this->error_msg == ''
        ) {
            $this->error_msg = Tools::displayError('Requested room(s) not available.');
        } elseif (isset($params['payment_detail']['payment_type'])
            && $params['payment_detail']['payment_type'] != 'online'
            && $params['payment_detail']['payment_type'] != 'remote'
            && $params['payment_detail']['payment_type'] != 'pay at hotel'
        ) {
            $this->error_msg = Tools::displayError('Invalid payment type.');
        } elseif (!isset($params['booking_status'])) {
            $this->error_msg = Tools::displayError('Invalid booking status.');
        }

        if (!$this->error_msg && $this->error_msg == '') {
            return true;
        }

        return false;
    }

    /**
     * Validating the address fields in POST and PUT requests.
     */
    public function validateAddressFields($params)
    {
        $status = true;
        if (isset($params['address'])
            || isset($params['city'])
            || isset($params['country_code'])
            || isset($params['state_code'])
            || isset($params['zip'])
        ) {
            $status = false;
            $idCountry = Country::getByIso($params['country_code']);
            $objCountry = new Country($idCountry);
            if (!isset($params['address']) || !$params['address']) {
                $this->error_msg = Tools::displayError('Address is required.');
            } elseif (!isset($params['city']) || !$params['city']) {
                $this->error_msg = Tools::displayError('City is required.');
            } elseif (!isset($params['country_code']) || !$params['country_code']) {
                $this->error_msg = Tools::displayError('Country code is required.');
            } else if (!isset($params['address']) || !$params['address']) {
                $this->error_msg = Tools::displayError('Address is required.');
            } else if (!isset($params['country_code']) || !$params['country_code']) {
                $this->error_msg = Tools::displayError('Country code is required.');
            } else if (!$idCountry = Country::getByIso($params['country_code'])) {
                $this->error_msg = Tools::displayError('Invalid country code.');
            } else if ($objCountry->need_zip_code && (!isset($params['zip']) || !$params['zip'])) {
                $this->error_msg = Tools::displayError('Zip code is required.');
            } elseif ($objCountry->need_zip_code
                && (!Validate::isPostCode($params['zip']) || ($objCountry->zip_code_format && !$objCountry->checkZipCode($params['zip'])))
            ) {
                $this->error_msg = sprintf(Tools::displayError('The Zip/Postal code you have entered is invalid. It must follow this format: %s'), str_replace('C', $objCountry->iso_code, str_replace('N', '0', str_replace('L', 'A', $objCountry->zip_code_format))));
            } else {
                $status = true;
            }
        }

        return $status;
    }


    /**
     * Checking room type information validity.
     */
    public function validateRequestedRoomTypes($roomTypes = array(), $idHotel = false)
    {
        $objBookingDetail = new HotelBookingDetail();
        $objRoomType = new HotelRoomType();
        $roomUnitSelectionType = Configuration::get('PS_FRONT_ROOM_UNIT_SELECTION_TYPE');

        $quantityWise = false;
        if ($roomUnitSelectionType != HotelBookingDetail::PS_ROOM_UNIT_SELECTION_TYPE_OCCUPANCY) {
            $quantityWise = true;
        }

        if (!$roomTypes) {
            return false;
        } else {
            foreach ($roomTypes as $roomType) {
                if ($this->validateRoomType($roomType)) {
                    if (!$idHotel) {
                        $roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($roomType['id_room_type']);
                        $idHotel = $roomTypeInfo['id_hotel'];
                    }


                    $dateFrom = date('Y-m-d', strtotime($roomType['check_in_date']));
                    $dateTo = date('Y-m-d', strtotime($roomType['check_out_date']));
                    $bookingParams = array(
                        'date_from' => $dateFrom,
                        'date_to' => $dateTo,
                        'hotel_id' => $idHotel,
                        'id_room_type' => $roomType['id_room_type'],
                        'only_search_data' => 1
                    );

                    if (($hotelRoomData = $objBookingDetail->dataForFrontSearch($bookingParams))
                        && isset($hotelRoomData['rm_data'][$roomType['id_room_type']]['data']['available'])
                        && $hotelRoomData['rm_data'][$roomType['id_room_type']]['data']['available']
                    ) {

                        if ($hotelRoomData['stats']['num_avail'] < $roomType['number_of_rooms']) {
                            return false;
                        } elseif (isset($roomType['rooms']) && count($roomType['rooms'])) {
                            foreach ($roomType['rooms'] as $room) {
                                if (isset($room['id_room']) && !isset($hotelRoomData['rm_data'][$roomType['id_room_type']]['data']['available'][$room['id_room']])) {
                                    return false;
                                }
                            }
                        }
                    } else {
                        if ($quantityWise) {
                            $occupancy = $hotelRoomData['rm_data'][$roomType['id_room_type']]['max_guests'];
                        } else {
                            $occupancy = array();
                            if (isset($roomType['rooms']) && count($roomType['rooms'])) {
                                foreach ($roomType['rooms'] as $room) {
                                    $occupancy[] =  array(
                                        'adults' => isset($room['occupancy']['adults']),
                                        'children' => $room['occupancy']['children'],
                                        'child_ages' => $room['occupancy']['children_ages']
                                    );
                                }
                            }
                        }

                        $params = array(
                            'date_from' => $dateFrom,
                            'date_to' => $dateTo,
                            'hotel_id' => $idHotel,
                            'id_room_type' => $roomType['id_room_type'],
                            'num_rooms' => $roomType['number_of_rooms'],
                            'search_available' => 0,
                            'search_partial' => 1,
                            'search_booked' => 0,
                            'search_unavai' => 0,
                            'id_cart' => $this->context->cart->id,
                            'id_guest' => null,
                            'search_cart_rms' => 0,
                            'occupancy' => $occupancy
                        );

                        $isCheckInDateAvailable = false;
                        $isCheckOutDateAvailable = false;
                        $isDurationAvailable = true;

                        $partiallyAvailableRooms = $objBookingDetail->getBookingData($params)['rm_data'];

                        foreach ($partiallyAvailableRooms as $availbleRooms) {
                            foreach ($availbleRooms['data'] as $key => $rooms) {
                                if($key == 'partially_available') {
                                    foreach ($rooms as $room) {
                                        if(!$isCheckInDateAvailable){
                                            if( $dateFrom == $room['date_from']) {
                                                $isCheckInDateAvailable = true;
                                            }
                                        }

                                        if(!$isCheckOutDateAvailable){
                                            if($dateTo == $room['date_to']) {
                                                $isCheckOutDateAvailable = true;
                                            }
                                        }

                                        if(!($room['date_from'] >= $dateFrom && $room['date_to'] <= $dateTo)) {
                                            $isDurationAvailable = false;
                                        }
                                    }
                                }
                            }
                        }

                        if ($isCheckInDateAvailable && $isCheckOutDateAvailable && $isDurationAvailable) {
                            return true;
                        }
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Validating the Room types fields.
     */
    public function validateRoomType($roomType)
    {
        $status = true;
        if (!isset($roomType['id_room_type'])) {
            $this->error_msg = Tools::displayError('Id room type is missing');
            $status = false;
        } elseif (!isset($roomType['check_in_date']) || !Validate::isDate($roomType['check_in_date'])) {
            $this->error_msg = Tools::displayError('Invalid check in date in the request');
            $status = false;
        } elseif (!isset($roomType['check_out_date']) || !Validate::isDate($roomType['check_out_date'])) {
            $this->error_msg = Tools::displayError('Invalid check out date in the request');
            $status = false;
        } elseif (!isset($roomType['number_of_rooms'])) {
            $this->error_msg = Tools::displayError('number of rooms is missing');
            $status = false;
        } elseif (!Validate::isLoadedObject(new Product((int) $roomType['id_room_type']))
            || !Product::isBookingProduct((int) $roomType['id_room_type'])
        ) {
            $this->error_msg = Tools::displayError('Invalid room type in the request');
            $status = false;
        } elseif (isset($roomType['rooms']) && count($roomType['rooms']) && count($roomType['rooms']) != $roomType['number_of_rooms']) {
            $this->error_msg = Tools::displayError('Room count does not matches with the number of rooms');
            $status = false;
        } elseif (strtotime($roomType['check_in_date']) > strtotime($roomType['check_out_date'])) {
            $this->error_msg = Tools::displayError('Invalid check in and check out dates in the request');
            $status = false;
        }

        return $status;
    }

    /**
     * Operations required for PUT requests.
     */
    public function handlePostRequest($params)
    {
        $this->context->cart = new Cart();
        $this->processGuestDetails($params['guest_detail']);
        $this->processLanguage($params);
        $this->processCurrency($params);
        // Saving the cart after adding the guest, language and the currency in the cart.
        $this->context->cart->save();
        $this->processCustomer($params['guest_detail']);
        $this->addRoomsInCart($params['room_bookings']);
        // validating Cart rules here since the cart rule checkValidity() only works if there are products in the cart.
        if (($error = $this->applyCartRules($params)) && $error != '') {
            throw new WebserviceException(
                $error,
                array(404, 400)
            );

            return false;
        }

        $totalAmount = isset($params['price_details']['total_price_with_tax']) ? $params['price_details']['total_price_with_tax'] : 0;
        $totalTax = isset($params['price_details']['total_tax']) ? $params['price_details']['total_tax'] : 0;
        $objPaymentModule = new WebserviceOrder();
        $bookingStatus = self::API_BOOKING_STATUS_NEW;
        if (isset($params['booking_status']) && $params['booking_status']) {
            $bookingStatus = $params['booking_status'];
        }

        $cartTotal = $this->context->cart->getOrderTotal(false, Cart::BOTH);
        switch ($bookingStatus) {
            case self::API_BOOKING_STATUS_NEW:
                $paymentStatus = false;
                if (isset($params['payment_status'])) {
                    $paymentStatus = $params['payment_status'];
                }

                switch ($paymentStatus) {
                    case self::API_BOOKING_PAYMENT_STATUS_COMPLETED:
                        if (($totalAmount-$totalTax) >= $cartTotal) {
                            $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
                        } else {
                            $orderStatus = Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED');
                        }
                        break;
                    case self::API_BOOKING_PAYMENT_STATUS_PARTIAL:
                        $orderStatus = Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED');
                        break;
                    case self::API_BOOKING_PAYMENT_STATUS_AWATING:
                        $orderStatus = Configuration::get('PS_OS_AWAITING_PAYMENT');
                        break;
                    default:
                        $orderStatus = Configuration::get('PS_OS_AWAITING_PAYMENT');
                        break;
                }
                break;
            case self::API_BOOKING_STATUS_COMPLETED:
                $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
                if (!$totalAmount) {
                    $totalAmount = $cartTotal;
                } elseif ($totalAmount > 0 && $totalAmount < $cartTotal) {
                    $orderStatus = Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED');
                }

                break;
            case self::API_BOOKING_STATUS_CANCELLED:
                $orderStatus = Configuration::get('PS_OS_CANCELED');
                break;
            case self::API_BOOKING_STATUS_REFUNDED:
                $orderStatus = Configuration::get('PS_OS_REFUND');
                break;
            default:
                break;
        }

        if (isset($params['source']) && $params['source']) {
            $objPaymentModule->orderSource = $params['source'];
        }

        $extraVars = array();
        $message = null;
        if (isset($params['payment_detail']['transaction_id'])
            && $params['payment_detail']['transaction_id']
        ) {
            $extraVars['transaction_id'] = $params['payment_detail']['transaction_id'];
        }

        if(isset($params['payment_status']) && $params['payment_status'] == self::API_BOOKING_PAYMENT_STATUS_COMPLETED) {
            $objPaymentModule->payment_type = OrderPayment::PAYMENT_TYPE_REMOTE_PAYMENT;
        }

        if (isset($params['remark'])) {
            $message = $params['remark'];
        }

        if (isset($params['payment_detail']['payment_method'])
            && $params['payment_detail']['payment_method']
        ) {
            $objPaymentModule->displayName = $params['payment_detail']['payment_method'];
        }

        if (isset($params['payment_detail']['payment_type'])
            && $params['payment_detail']['payment_type']
        ) {
            if ($params['payment_detail']['payment_type'] == 'remote') {
                $objPaymentModule->payment_type = OrderPayment::PAYMENT_TYPE_REMOTE_PAYMENT;
            } elseif ($params['payment_detail']['payment_type'] == 'pay at hotel') {
                $objPaymentModule->payment_type = OrderPayment::PAYMENT_TYPE_PAY_AT_HOTEL;
            }
        }
        $objOrderState = new OrderState($orderStatus);
        if ($objOrderState->paid) {
            $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
        } else {
            $orderStatus = Configuration::get('PS_OS_AWAITING_PAYMENT');
        }

        if ($objPaymentModule->validateOrder(
            $this->context->cart->id,
            $orderStatus,
            $totalAmount,
            $objPaymentModule->displayName,
            $message,
            $extraVars,
            null,
            false,
            $this->bookingCustomer->secure_key,
            null,
            false
        )) {
            $this->updateServicesAndDemandsInOrder($objPaymentModule->currentOrder);
            if (!empty($this->wsRequestedRooms)) {
                $this->updateRoomTaxRulesGroupsInOrder($objPaymentModule->currentOrder);
            }
            $this->manageOrderPrice($objPaymentModule->currentOrder, $params);
            $this->addOrderHistory($objPaymentModule->currentOrder, $objOrderState);
            $objOrder = new Order($objPaymentModule->currentOrder);
            if (isset($params['booking_date'])
                && $params['booking_date']
                && Validate::isDate($params['booking_date'])
            ) {
                $objOrder->date_add = date('Y-m-d H:i:s', strtotime($params['booking_date']));
            }

            if(isset($params['id_channel_manager_booking']) && $params['id_channel_manager_booking']) {
                $objOrder->id_channel_manager_booking = $params['id_channel_manager_booking'];
            }
            $objOrder->save();
            $this->getBookingDetails($objPaymentModule->currentOrder);

            return true;
        }

        $this->wsObject->setError(400, Tools::displayError('Unable to create booking.'), 200);

        return false;
    }

    /**
     * Used to get the booking details for the  GET, POST and PUT request.
     */
    public function getBookingDetails($idBooking)
    {
        $objOrder = new Order($idBooking);
        if (!Validate::isLoadedObject($objOrder)) {
            $this->objOutput->setStatus(404);
            $this->getWsObject()->setOutputEnabled(false);
            return false;
        } else {
            $objCurrency = new Currency($objOrder->id_currency);
            $objBookingDetail = new HotelBookingDetail();
            $objBookingDemand = new HotelBookingDemands();
            $objServiceProductOrderDetail = new ServiceProductOrderDetail();
            $objOrderReturn = new OrderReturn();
            $idHotel = HotelBookingDetail::getIdHotelByIdOrder($objOrder->id);
            $objHotelBranchInformation = new HotelBranchInformation($idHotel, Configuration::get('PS_LANG_DEFAULT'));
            $objOrderState = new OrderState($objOrder->current_state, Configuration::get('PS_LANG_DEFAULT'));
            $objCustomer = new Customer($objOrder->id_customer);
            $params['id'] = (int) $objOrder->id;
            $params['id_pms_booking'] = (int) $objOrder->id;
            $params['id_property'] = (int) $idHotel;
            if(isset($objOrder->id_channel_manager_booking) && $objOrder->id_channel_manager_booking) {
                $params['id_channel_manager_booking'] = $objOrder->id_channel_manager_booking;
            }
            $params['currency'] = strtoupper($objCurrency->iso_code);
            // $params['current_status'] = $objOrderState->name;
            $params['source'] = $objOrder->source;
            $params['booking_date'] = $objOrder->date_add;
            $params['id_language'] = (int) $objOrder->id_lang;
            $assoc = array();
            $customerDetails = array(
                'id_customer' => (int) $objCustomer->id,
                'firstname' => $objCustomer->firstname,
                'lastname' => $objCustomer->lastname,
                'email' => $objCustomer->email,
                'phone' => isset($objCustomer->phone) ? $objCustomer->phone : ''
            );

            $priceDetails = array(
                'total_paid' => Tools::ps_round($objOrder->total_paid_real, _PS_PRICE_COMPUTE_PRECISION_),
                'total_price_without_tax' => Tools::ps_round($objOrder->total_paid_tax_excl, _PS_PRICE_COMPUTE_PRECISION_),
                'total_tax' => Tools::ps_round(($objOrder->total_paid_tax_incl - $objOrder->total_paid_tax_excl), _PS_PRICE_COMPUTE_PRECISION_)
            );

            $orderCartRules = array();
            if ($cartRules = $objOrder->getCartRules()) {
                foreach ($cartRules as $cartRule) {
                    $rule = array();
                    $rule['code'] = $cartRule['name'];
                    $rule['value'] = Tools::ps_round($cartRule['value'], _PS_PRICE_COMPUTE_PRECISION_);
                    $rule['currency'] = strtoupper($objCurrency->iso_code);
                    $rule['id_order_invoice'] = (int) $cartRule['id_order_invoice'];
                    $orderCartRules[] = $rule;
                }
            }

            $params['associations']['guest_detail'] = $customerDetails;
            $params['associations']['price_details'] = $priceDetails;
            $params['associations']['cart_rules'] = $orderCartRules;
            $roomTypeInfo = array();
            if (Group::getPriceDisplayMethod($objCustomer->id_default_group) == PS_TAX_INC) {
                $useTax = 1;
            } else {
                $useTax = null;
            }

            $params['associations']['remarks'] = array();
            if ($customerMessages = Message::getMessagesByOrderId($objOrder->id, true)) {
                foreach ($customerMessages as $customerMessage) {
                    $message = $customerMessage['message'];
                    $params['associations']['remarks'][] = $message;
                }
            }


            if ($orderDetailData = $objBookingDetail->getOrderFormatedBookinInfoByIdOrder($objOrder->id)) {
                foreach ($orderDetailData as $orderDetailKey => $orderData) {
                    $dateJoin = $orderData['id_product'].'_'.strtotime($orderData['date_from']).strtotime($orderData['date_to']);
                    if (!isset($roomTypeInfo[$dateJoin])) {
                        $roomTypeInfo[$dateJoin]['id_room_type'] = (int) $orderData['id_product'];
                        $roomTypeInfo[$dateJoin]['checkin_date'] = $orderData['date_from'];
                        $roomTypeInfo[$dateJoin]['checkout_date'] = $orderData['date_to'];
                        $roomTypeInfo[$dateJoin]['total_tax'] = ($orderData['total_price_tax_incl'] - $orderData['total_price_tax_excl']);
                        $roomTypeInfo[$dateJoin]['number_of_rooms'] = 1;
                        $roomTypeInfo[$dateJoin]['name'] = $orderData['room_type_name'];
                    } else {
                        $roomTypeInfo[$dateJoin]['total_tax'] += ($orderData['total_price_tax_incl'] - $orderData['total_price_tax_excl']);
                        $roomTypeInfo[$dateJoin]['number_of_rooms'] += 1;
                    }

                    $roomTypeInfo[$dateJoin]['total_tax'] = Tools::ps_round($roomTypeInfo[$dateJoin]['total_tax'], _PS_PRICE_COMPUTE_PRECISION_);

                    $roomInfo = array();
                    $roomInfo['id_room'] = (int) $orderData['id_room'];
                    $roomInfo['id_hotel_booking'] = (int) $orderData['id'];
                    $roomInfo['adults'] = (int) $orderData['adults'];
                    $roomInfo['child'] = (int) $orderData['children'];
                    $roomInfo['unit_price_without_tax'] = Tools::ps_round($orderData['total_price_tax_excl'], _PS_PRICE_COMPUTE_PRECISION_);
                    $roomInfo['total_tax'] = Tools::ps_round(($orderData['total_price_tax_incl'] - $orderData['total_price_tax_excl']), _PS_PRICE_COMPUTE_PRECISION_);
                    if (isset($roomInfo['facilities'])) {
                        unset($roomInfo['facilities']);
                    }
                    if ($extraDemands = $objBookingDemand->getRoomTypeBookingExtraDemands(
                        $orderData['id_order'],
                        $orderData['id_product'],
                        $orderData['id_room'],
                        $orderData['date_from'],
                        $orderData['date_to'],
                        0,
                        0,
                        $useTax
                    )) {
                        $roomInfo['facilities'] = array();
                        foreach ($extraDemands as $extraDemand) {
                            $demand = array();
                            $demand['name'] = $extraDemand['name'];
                            $demand['quantity'] = 1;
                            $demand['unit_price_without_tax'] = Tools::ps_round($extraDemand['unit_price_tax_excl'], _PS_PRICE_COMPUTE_PRECISION_);
                            $demand['total_tax'] = Tools::ps_round(($extraDemand['unit_price_tax_incl'] - $extraDemand['unit_price_tax_excl']), _PS_PRICE_COMPUTE_PRECISION_);

                            $demand['per_night'] = 0;
                            if ($extraDemand['price_calc_method'] == HotelRoomTypeGlobalDemand::WK_PRICE_CALC_METHOD_EACH_DAY) {
                                $demand['per_night'] = 1;
                            }

                            $roomInfo['facilities'][] = $demand;
                        }
                    }

                    if (isset($roomInfo['services'])) {
                        unset($roomInfo['services']);
                    }

                    if ($additionalServices = $objServiceProductOrderDetail->getroomTypeServiceProducts(
                        $orderData['id_order'],
                        0,
                        0,
                        $orderData['id_product'],
                        $orderData['date_from'],
                        $orderData['date_to'],
                        $orderData['id_room'],
                        0,
                        $useTax
                    )) {
                        $roomInfo['services'] = array();
                        foreach ($additionalServices as $additionalService) {
                            foreach ($additionalService['additional_services'] as $service) {
                                $services = array();
                                $services['id_service'] = (int) $service['id_product'];
                                $services['name'] = $service['name'];
                                $services['quantity'] = (int) $service['quantity'];
                                $services['unit_price_without_tax'] = Tools::ps_round(($service['total_price_tax_excl'] / $services['quantity']), _PS_PRICE_COMPUTE_PRECISION_);
                                $services['total_price_without_tax'] = Tools::ps_round(($service['total_price_tax_excl']), _PS_PRICE_COMPUTE_PRECISION_);
                                $services['total_tax'] = Tools::ps_round(($service['total_price_tax_incl'] - $service['total_price_tax_excl']), _PS_PRICE_COMPUTE_PRECISION_);

                                $objProduct = new Product($service['id_product']);
                                $services['per_night'] = 0;
                                if ($objProduct->price_calculation_method == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                                    $services['per_night'] = 1;
                                }

                                $services['price_mode'] = (int) $objProduct->price_calculation_method;
                                $roomInfo['services'][] = $services;
                            }
                        }
                    }

                    $roomTypeInfo[$dateJoin]['rooms'][] = $roomInfo;
                }
            }

            $params['associations']['room_types'] = array_values($roomTypeInfo);
            $this->output['data'] = $params;
        }
    }

    /**
     * Deleting the newly created services.
     */
    public function deleteWsServices()
    {
        if (isset($this->wsIdServices) && $this->wsIdServices) {
            foreach ($this->wsIdServices as $wsIdService) {
                // To filter false ids
                if ((int) $wsIdService) {
                    $objProduct = new Product($wsIdService);
                    $objProduct->delete();
                }
            }
        }
    }

    /**
     * Deleting the newly created feature price.
     */
    public function deleteWsFeaturePrices()
    {
        if (isset($this->wsFeaturePrices) && $this->wsFeaturePrices) {
            foreach ($this->wsFeaturePrices as $idFeaturePrice) {
                // To filter false ids
                if ((int) $idFeaturePrice && Validate::isLoadedObject($objFeaturePrice = new HotelRoomTypeFeaturePricing((int) $idFeaturePrice))) {
                    $objFeaturePrice->delete();
                }
            }
        }
    }

    /**
     * Deleting the newly created cart Rules.
     */
    public function deleteWsCartRules()
    {
        if (isset($this->wsCartRules) && $this->wsCartRules) {
            foreach ($this->wsCartRules as $idCartRule) {
                $objCartRule = new CartRule((int) $idCartRule);
                if (Validate::isLoadedobject($objCartRule)) {
                    $objCartRule->delete();
                }
            }
        }
    }

    public function updateRoomTaxRulesGroupsInOrder($idOrder)
    {
        $objOrder = new Order($idOrder);
        $objHotelBookingDetail = new HotelBookingDetail();
        if ($roomsInOrder = $objHotelBookingDetail->getOrderCurrentDataByOrderId($idOrder)) {
            foreach ($roomsInOrder as $orderRoomKey => $orderRoom) {
                $dateRoomJoinKey = strtotime($orderRoom['date_from']).''.strtotime($orderRoom['date_to']).$orderRoom['id_product'].$orderRoom['id_room'];
                if (isset($this->wsRequestedRooms[$dateRoomJoinKey])) {
                    $objHotelBookingDetail = new HotelBookingDetail((int) $orderRoom['id']);
                    $objOrderDetail = new OrderDetail((int) $objHotelBookingDetail->id_order_detail);

                    $objAddress = new Address((int) $objOrder->id_address_tax);
                    $priceWithTax = $objHotelBookingDetail->total_price_tax_incl;

                    if (isset($this->wsRequestedRooms[$dateRoomJoinKey]['id_tax_rules_group'])) {
                        // Getting new Tax
                        $objTaxManager = TaxManagerFactory::getManager($objAddress, $this->wsRequestedRooms[$dateRoomJoinKey]['id_tax_rules_group']);
                        $objTaxCalculator = $objTaxManager->getTaxCalculator();
                        $objOrderDetail->id_tax_rules_group = $this->wsRequestedRooms[$dateRoomJoinKey]['id_tax_rules_group'];
                        $priceWithTax = $objTaxCalculator->addTaxes($objHotelBookingDetail->total_price_tax_excl);
                    } else if (isset($this->wsRequestedRooms[$dateRoomJoinKey]['total_tax'])) {
                        $priceWithTax = $objHotelBookingDetail->total_price_tax_excl + $this->wsRequestedRooms[$dateRoomJoinKey]['total_tax'];
                        $objOrderDetail->id_tax_rules_group = 0;
                        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'order_detail_tax` WHERE id_order_detail='.(int)$objOrderDetail->id);
                    }

                    $taxDiff = $priceWithTax - $objHotelBookingDetail->total_price_tax_incl;

                    // Updating the price
                    $objHotelBookingDetail->total_price_tax_incl += $taxDiff;
                    $objHotelBookingDetail->save();

                    $objOrderDetail->total_price_tax_incl += $taxDiff;
                    $objOrderDetail->unit_price_tax_incl = Tools::ps_round(($objOrderDetail->total_price_tax_incl / $objOrderDetail->product_quantity), _PS_PRICE_COMPUTE_PRECISION_);
                    $objOrderDetail->unit_price_tax_excl = Tools::ps_round(($objOrderDetail->total_price_tax_excl / $objOrderDetail->product_quantity), _PS_PRICE_COMPUTE_PRECISION_);
                    if (($objOrderDetail->id_order_invoice)
                        && Validate::isLoadedObject($objOrderInvoice = new OrderInvoice($objOrderDetail->id_order_invoice))
                    ) {
                        $objOrderInvoice->total_products_wt += $taxDiff;
                        $objOrderInvoice->total_paid_tax_incl += $taxDiff;
                        $objOrderInvoice->save();
                    }

                    if (isset($this->wsRequestedRooms[$dateRoomJoinKey]['id_tax_rules_group']) && $this->wsRequestedRooms[$dateRoomJoinKey]['id_tax_rules_group']) {
                        $objOrderDetail->id_tax_rules_group = $this->wsRequestedRooms[$dateRoomJoinKey]['id_tax_rules_group'];
                        $objOrderDetail->save();
                        $this->saveTaxCalculator($objOrderDetail->id, $this->wsRequestedRooms[$dateRoomJoinKey]['id_tax_rules_group']);
                    } else {
                        $objOrderDetail->save();
                    }

                    $objOrder->total_paid += $taxDiff;
                    $objOrder->total_paid_tax_incl += $taxDiff;
                    if ($objOrder->current_state == Configuration::get('PS_OS_PAYMENT_ACCEPTED')) {
                        $objOrder->total_paid_real = Tools::ps_round($objOrder->total_paid_tax_incl, _PS_PRICE_COMPUTE_PRECISION_);
                    }

                    $objOrder->total_products_wt += $taxDiff;
                }
            }

            $objOrder->update();
        }
    }

    public function saveTaxCalculator($idOrderDetail, $idTaxRulesGroup)
    {
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'order_detail_tax` WHERE id_order_detail=' . (int)$idOrderDetail);

        $values = '';
        $objOrderDetail = new OrderDetail($idOrderDetail);
        foreach ($this->wsTaxRulesGroup[$idTaxRulesGroup] as $taxRuleGroup) {
            $idTax = $taxRuleGroup['tax'];
            $value = '(' . (int)$objOrderDetail->id . ',' . (int)$idTax . ',' .
                Tools::ps_round(($objOrderDetail->unit_price_tax_excl*$taxRuleGroup['tax_rate'])/100, _PS_PRICE_COMPUTE_PRECISION_) . ',' .
                Tools::ps_round(($objOrderDetail->total_price_tax_excl*$taxRuleGroup['tax_rate'])/100, _PS_PRICE_COMPUTE_PRECISION_)  . '),';

            if (empty($values)) {
                $values .= rtrim($value, ',');
            } else {
                $values .= ',' . rtrim($value, ',');
            }
        }
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'order_detail_tax` (id_order_detail, id_tax, unit_amount, total_amount)
        VALUES ' . $values;

        return Db::getInstance()->execute($sql);
    }

    public function createTaxRulesGroup($taxGroupRate, $taxes, $objAddress)
    {
        if($taxGroupRate) {
            $objTaxRulesGroup = new TaxRulesGroup();
            $objTaxRulesGroup->name = 'TAX ('.Tools::ps_round($taxGroupRate, _PS_PRICE_COMPUTE_PRECISION_).'%)';
            $objTaxRulesGroup->deleted = 1;
            $objTaxRulesGroup->active = 1;
            $objTaxRulesGroup->add();

            if((!isset($taxes) || !$taxes) && $taxGroupRate) {
                $taxes = array(
                    array(
                        'name' => $objTaxRulesGroup->name,
                        'rate' => Tools::ps_round($taxGroupRate, _PS_PRICE_COMPUTE_PRECISION_)
                ));
            }

            $this->createTaxeRulesInGroup($objTaxRulesGroup->id, $taxes, $objAddress);

            return $objTaxRulesGroup->id;
        }
        return 0;
    }

    public function createTaxeRulesInGroup($idTaxRulesGroup, $taxes, $objAddress)
    {
        if($taxes) {
            foreach($taxes as $tax) {
                $objTax = new Tax();
                foreach (Language::getLanguages(false) as $language) {
                    $objTax->name[$language['id_lang']] = $tax['name'];
                }

                $objTax->rate = $tax['rate'];
                $objTax->active = 1;
                $objTax->deleted = 1;
                $objTax->add();

                $objTaxRule = new TaxRule();
                $objTaxRule->id_tax = $objTax->id;
                $objTaxRule->id_tax_rules_group = $idTaxRulesGroup;
                $objTaxRule->id_country = $objAddress->id_country;
                $objTaxRule->id_state = $objAddress->id_state;
                $objTaxRule->behavior = 1;

                $objTaxRule->add();

                $this->wsTaxRulesGroup[$idTaxRulesGroup][] = array(
                    'tax' => $objTax->id,
                    'tax_rate' => $objTax->rate,
                    'tax_rule' => $objTaxRule->id
                );
            }
        }

    }

    /**
     * Adding history to the order.
     */
    public function addOrderHistory($idOrder, $orderStatus)
    {
        $objOrder = new Order((int) $idOrder);
        $currentOrderStatus = $objOrder->getCurrentOrderState();
        if ($currentOrderStatus->id != $orderStatus->id) {
            $objOrderHistory = new OrderHistory();
            $objOrderHistory->id_order = $objOrder->id;
            $useExistingsPayment = false;
            if (!$objOrder->hasInvoice()) {
                $useExistingsPayment = true;
            }

            $objOrderHistory->changeIdOrderState((int)$orderStatus->id, $objOrder, $useExistingsPayment);
            $objOrderHistory->add(true, array());
        }
    }

    public function createGuest()
    {
        $guest = new Guest();
        $guest->id_operating_system = 7; // For Android Device
        $guest->id_web_browser = 1; // For Other(Opera)
        $guest->mobile_theme = 1; // For Mobile device
        $guest->save();
        if ($guest->id) {
            return $guest->id;
        }

        return 0;
    }

    /**
     * Updating the services and their prices.
     */
    public function updateServicesAndDemandsInOrder($idOrder)
    {
        $objOrder = new Order($idOrder);
        $objServiceProductOrderDetail = new ServiceProductOrderDetail();
        if (isset($this->wsRequestedRoomTypes) && $this->wsRequestedRoomTypes) {
            if ($orderedServices = $objServiceProductOrderDetail->getRoomTypeServiceProducts($objOrder->id,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                null,
                null
            )) {
                foreach ($orderedServices as $orderedService) {
                    $objHotelBookingDetail = new HotelBookingDetail($orderedService['id_htl_booking_detail']);
                    $dateRoomJoinKey = strtotime($objHotelBookingDetail->date_from).''.strtotime($objHotelBookingDetail->date_to).$orderedService['id_room_type'].$orderedService['id_room'];

                    if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'])
                        && $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services']
                        && isset($orderedService['additional_services'])
                        && $orderedService['additional_services']
                    ) {
                        foreach ($orderedService['additional_services'] as $service) {
                            $objOrderDetail = new OrderDetail($service['id_order_detail']);
                            $objServiceProductOrderDetail = new ServiceProductOrderDetail($service['id_service_product_order_detail']);
                            $quantity = $objServiceProductOrderDetail->quantity;
                            if ($objOrderDetail->product_price_calculation_method == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                                $quantity = $quantity * HotelHelper::getNumberOfDays(
                                    $objHotelBookingDetail->date_from,
                                    $objHotelBookingDetail->date_to
                                );
                            }

                            if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']])) {
                                $oldPriceTaxExcl = $objServiceProductOrderDetail->total_price_tax_excl;
                                $oldPriceTaxIncl = $objServiceProductOrderDetail->total_price_tax_incl;
                                if ($oldPriceTaxExcl > 0) {
                                    $oldTaxMultiplier = $oldPriceTaxIncl / $oldPriceTaxExcl;
                                } else {
                                    $oldTaxMultiplier = 1;
                                }

                                if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['unit_price_without_tax'])) {
                                    $unitPriceTaxExcl = $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['unit_price_without_tax'];
                                    $unitPriceTaxIncl = $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['unit_price_without_tax'] * $oldTaxMultiplier;
                                    $totalPriceTaxExcl = $unitPriceTaxExcl * $quantity;
                                    $totalPriceTaxIncl = $unitPriceTaxIncl * $quantity;
                                    $objServiceProductOrderDetail->unit_price_tax_excl = Tools::ps_round($unitPriceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);
                                    $objServiceProductOrderDetail->unit_price_tax_incl = Tools::ps_round($unitPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                                    $objServiceProductOrderDetail->total_price_tax_excl = Tools::ps_round($totalPriceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);
                                    $objServiceProductOrderDetail->total_price_tax_incl = Tools::ps_round($totalPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);

                                    $priceDiffTaxExcl = $objServiceProductOrderDetail->total_price_tax_excl - $oldPriceTaxExcl;
                                    $priceDiffTaxIncl = $objServiceProductOrderDetail->total_price_tax_incl - $oldPriceTaxIncl;

                                    $objOrderDetail->total_price_tax_excl += $priceDiffTaxExcl;
                                    $objOrderDetail->total_price_tax_incl += $priceDiffTaxIncl;

                                    $objOrderDetail->unit_price_tax_excl = Tools::ps_round(($objOrderDetail->total_price_tax_excl / $objOrderDetail->product_quantity), _PS_PRICE_COMPUTE_PRECISION_);
                                    $objOrderDetail->unit_price_tax_incl = Tools::ps_round(($objOrderDetail->total_price_tax_incl / $objOrderDetail->product_quantity), _PS_PRICE_COMPUTE_PRECISION_);

                                    $objOrder->total_paid_tax_excl += $priceDiffTaxExcl;
                                    $objOrder->total_paid_tax_incl += $priceDiffTaxIncl;
                                    $objOrder->total_paid += $priceDiffTaxIncl;
                                } elseif (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_price_without_tax'])) {
                                    $totalPriceTaxExcl = $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_price_without_tax'];
                                    $totalPriceTaxIncl = $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_price_without_tax'] * $oldTaxMultiplier;
                                    $unitPriceTaxExcl = $totalPriceTaxExcl / $quantity;
                                    $unitPriceTaxIncl = $totalPriceTaxIncl / $quantity;

                                    $objServiceProductOrderDetail->unit_price_tax_excl = Tools::ps_round($unitPriceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);
                                    $objServiceProductOrderDetail->unit_price_tax_incl = Tools::ps_round($unitPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                                    $objServiceProductOrderDetail->total_price_tax_excl = Tools::ps_round($totalPriceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);
                                    $objServiceProductOrderDetail->total_price_tax_incl = Tools::ps_round($totalPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);

                                    $priceDiffTaxExcl = Tools::ps_round($objServiceProductOrderDetail->total_price_tax_excl - $oldPriceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);
                                    $priceDiffTaxIncl = Tools::ps_round($objServiceProductOrderDetail->total_price_tax_incl - $oldPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);

                                    $objOrderDetail->total_price_tax_excl += $priceDiffTaxExcl;
                                    $objOrderDetail->total_price_tax_incl += $priceDiffTaxIncl;

                                    $objOrderDetail->unit_price_tax_excl = Tools::ps_round(($objOrderDetail->total_price_tax_excl / $objOrderDetail->product_quantity), _PS_PRICE_COMPUTE_PRECISION_);
                                    $objOrderDetail->unit_price_tax_incl = Tools::ps_round(($objOrderDetail->total_price_tax_incl / $objOrderDetail->product_quantity), _PS_PRICE_COMPUTE_PRECISION_);

                                    $objOrder->total_paid_tax_excl += $priceDiffTaxExcl;
                                    $objOrder->total_paid_tax_incl += $priceDiffTaxIncl;
                                    $objOrder->total_paid += $priceDiffTaxIncl;
                                }
                            }

                            if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group'])) {
                                $objAddress = new Address((int) $objOrder->id_address_tax);
                                $objTaxManager = TaxManagerFactory::getManager($objAddress, $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group']);
                                $objTaxCalculator = $objTaxManager->getTaxCalculator();
                                $unitPriceTaxIncl = $objTaxCalculator->addTaxes($objServiceProductOrderDetail->unit_price_tax_excl);
                                $oldPriceTaxIncl = Tools::ps_round($objServiceProductOrderDetail->total_price_tax_incl, _PS_PRICE_COMPUTE_PRECISION_) ;

                                $objServiceProductOrderDetail->unit_price_tax_incl = Tools::ps_round($unitPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                                $objServiceProductOrderDetail->total_price_tax_incl = Tools::ps_round(($unitPriceTaxIncl * $quantity), _PS_PRICE_COMPUTE_PRECISION_);

                                $priceDiffTaxIncl = $objServiceProductOrderDetail->total_price_tax_incl - $oldPriceTaxIncl;

                                $objOrderDetail->total_price_tax_incl += $priceDiffTaxIncl;
                                $objOrderDetail->unit_price_tax_incl = Tools::ps_round(($objOrderDetail->total_price_tax_incl / $objOrderDetail->product_quantity), _PS_PRICE_COMPUTE_PRECISION_);

                                $objOrder->total_paid_tax_incl += $priceDiffTaxIncl;
                                $objOrder->total_paid += $priceDiffTaxIncl;
                            } elseif (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_tax'])
                                && $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_tax']
                            ) {
                                $oldPriceTaxIncl = $objServiceProductOrderDetail->total_price_tax_incl;
                                $objServiceProductOrderDetail->total_price_tax_incl = $objServiceProductOrderDetail->total_price_tax_excl + $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_tax'];

                                $objServiceProductOrderDetail->unit_price_tax_incl = Tools::ps_round($objServiceProductOrderDetail->total_price_tax_incl / $quantity, _PS_PRICE_COMPUTE_PRECISION_);
                                $objServiceProductOrderDetail->total_price_tax_incl = Tools::ps_round(($objServiceProductOrderDetail->total_price_tax_incl), _PS_PRICE_COMPUTE_PRECISION_);

                                $priceDiffTaxIncl = $objServiceProductOrderDetail->total_price_tax_incl - $oldPriceTaxIncl;
                                $objOrderDetail->total_price_tax_incl += $priceDiffTaxIncl;
                                $objOrderDetail->unit_price_tax_incl = Tools::ps_round(($objOrderDetail->total_price_tax_incl / $objOrderDetail->product_quantity), _PS_PRICE_COMPUTE_PRECISION_);

                                $objOrder->total_paid_tax_incl += $priceDiffTaxIncl;
                                $objOrder->total_paid += $priceDiffTaxIncl;
                            }

                            $objServiceProductOrderDetail->save();
                            $objOrderDetail->save();

                            $isAutoAdded = false;
                            if ($objOrderDetail->product_auto_add && $objOrderDetail->product_price_addition_type == Product::PRICE_ADDITION_TYPE_WITH_ROOM) {
                                $isAutoAdded = true;
                            }

                            if (!$isAutoAdded
                                && isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group'])
                                && $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group']
                            ) {
                                $this->saveTaxCalculator($objOrderDetail->id, $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group']);
                            }
                        }
                    }
                }

                // To save the changes made till now, since we are again loading this order while adding demands if any.
                $objOrder->save();
            }

            $objHotelBookingDetail = new HotelBookingDetail();
            if ($orderedRooms = $objHotelBookingDetail->getOrderCurrentDataByOrderId($objOrder->id)) {
                foreach ($orderedRooms as $orderedRoom) {
                    $dateRoomJoinKey = strtotime($orderedRoom['date_from']).''.strtotime($orderedRoom['date_to']).$orderedRoom['id_product'].$orderedRoom['id_room'];
                    if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['demands']) && $this->wsRequestedRoomTypes[$dateRoomJoinKey]['demands']) {
                        if ($demands = json_decode($this->wsRequestedRoomTypes[$dateRoomJoinKey]['demands'], true)) {
                            $this->addDemandsInOrderedRoom($demands, $orderedRoom['id']);
                        }
                    }
                }
            }
        }
    }

    public function manageOrderPrice($idOrder, $params)
    {
        $objOrder = new Order($idOrder);
        if (isset($params['price_details']['total_price_with_tax']) && (int) $params['price_details']['total_price_with_tax']) {
            if ($params['price_details']['total_price_with_tax'] < $objOrder->total_paid_tax_incl) {
                $cartRule['code'] = Tools::passwdGen(8, 'NO_NUMERIC');
                $cartRule['currency'] =  '';
                $cartRule['value'] = $objOrder->total_paid_tax_incl - $params['price_details']['total_price_with_tax'];
                if ($cartRule['value'] && $objOrder->getInvoicesCollection() && count($objOrder->getInvoicesCollection())) {
                    $cartRule['value'] /= count($objOrder->getInvoicesCollection());
                }

                $cartRule['type'] = self::API_CART_RULE_VALUE_TYPE_AMOUNT;
                $this->addCartRulesToOrder(array($cartRule), $objOrder->id);
            } else if ($params['price_details']['total_price_with_tax'] > $objOrder->total_paid_tax_incl) {
                $objHotelBookingDetail = new HotelBookingDetail();
                $objHotelBookingDemands = new HotelBookingDemands();
                if ($roomsInOrder = $objHotelBookingDetail->getOrderCurrentDataByOrderId($idOrder)) {
                    $requestedPrice = $params['price_details']['total_price_with_tax'];
                    $roomsToUpdate = array();
                    foreach ($roomsInOrder as $orderRoomKey => $orderRoom) {
                        $dateRoomJoinKey = strtotime($orderRoom['date_from']).''.strtotime($orderRoom['date_to']).$orderRoom['id_product'].$orderRoom['id_room'];
                        if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['room']['unit_price_without_tax'])) {
                            $objHotelBookingDetail = new HotelBookingDetail((int) $orderRoom['id']);
                            $requestedPrice -= $objHotelBookingDetail->total_price_tax_incl;
                        } else {
                            $roomsToUpdate[] = $orderRoom;
                        }
                    }

                    $serviceProductPrice = $objOrder->getTotalProductsWithTaxes(false, false, Product::SELLING_PREFERENCE_WITH_ROOM_TYPE);
                    if ($demands = $objHotelBookingDemands->getExtraDemandsTaxesDetails($objOrder->id)) {
                        $demandsPrice = array_sum(array_column($demands, 'total_price_tax_incl'));
                        // Adding the tax
                        $demandsPrice += array_sum(array_column($demands, 'total_amount'));
                        $serviceProductPrice += $demandsPrice;
                    }
                    $requestedPrice -= $serviceProductPrice;
                    // This empty means that all the rooms are sent with price in the request, so we will add a service to a room to manage the order total sent in the request.

                    if ($requestedPrice > 0) {
                        if (empty($roomsToUpdate)) {
                            $service = array();
                            $service['name'] = 'Created By API';
                            $service['total_price_without_tax'] = $requestedPrice;
                            $service['price_mode'] = self::API_SERVICE_PRICE_MODE_PER_BOOKING;
                            $service['quantity'] = 1;
                            $service['is_new'] = 1;
                            $firstRoomInOrder = array_shift($roomsInOrder);
                            $this->addServicesInOrderedRoom(array($service), $firstRoomInOrder['id']);
                        } else {
                            $roomsTotal = array_sum(array_column($roomsToUpdate, 'total_price_tax_incl'));
                            $objOrder = new Order($idOrder);
                            foreach ($roomsToUpdate as $roomInfo) {
                                $objHotelBookingDetail = new HotelBookingDetail((int) $roomInfo['id']);
                                $taxMultiplier = $objHotelBookingDetail->total_price_tax_incl / $objHotelBookingDetail->total_price_tax_excl;
                                $roomNewPrice = ($objHotelBookingDetail->total_price_tax_incl / $roomsTotal) * $requestedPrice;

                                $oldPriceTaxIncl = $objHotelBookingDetail->total_price_tax_incl;
                                $oldPriceTaxExcl = $objHotelBookingDetail->total_price_tax_excl;

                                $objHotelBookingDetail->total_price_tax_incl = Tools::ps_round($roomNewPrice, _PS_PRICE_COMPUTE_PRECISION_);
                                $objHotelBookingDetail->total_price_tax_excl = Tools::ps_round(($roomNewPrice / $taxMultiplier), _PS_PRICE_COMPUTE_PRECISION_);
                                $objHotelBookingDetail->save();
                                // Updating the price
                                $priceDiffTaxIncl = Tools::ps_round($objHotelBookingDetail->total_price_tax_incl - $oldPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                                $priceDiffTaxExcl = Tools::ps_round($objHotelBookingDetail->total_price_tax_excl - $oldPriceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);

                                $objOrderDetail = new OrderDetail((int) $objHotelBookingDetail->id_order_detail);
                                $objOrderDetail->total_price_tax_incl += $priceDiffTaxIncl;
                                $objOrderDetail->total_price_tax_excl += $priceDiffTaxExcl;
                                $objOrderDetail->save();
                                $objOrder->total_paid_tax_incl += $priceDiffTaxIncl;
                                $objOrder->total_products_wt += $priceDiffTaxIncl;

                                $objOrder->total_products += $priceDiffTaxExcl;
                                $objOrder->total_paid_tax_excl += $priceDiffTaxExcl;
                                $objOrder->total_paid += $priceDiffTaxIncl;
                            }
                            $objOrder->save();
                        }
                    }
                }
            }
        }

        if($params['payment_status'] == self::API_BOOKING_PAYMENT_STATUS_COMPLETED) {
            $this->managePaymentHistory($objOrder->reference, $params['price_details']['total_price_with_tax']);
        }
    }

    /**
     * Adding the required guest informations into the cart.
     */
    public function processGuestDetails($guestDetails)
    {
        if (isset($guestDetails['id_customer'])
            && Validate::isLoadedObject($objCustomer = new Customer((int) $guestDetails['id_customer']))
        ) {
            $this->bookingCustomer = $objCustomer;
        } else {
            $objCustomer = new Customer();
            $this->bookingCustomer = $objCustomer->getByEmail($guestDetails['email'], null, false);
        }

        if (isset($this->bookingCustomer->id)
            && $this->bookingCustomer->id
        ) {
            $idGuest = Guest::getFromCustomer($this->bookingCustomer->id);
        } else {
            $idGuest = $this->createGuest();
        }

        $this->context->cart->id_guest = $idGuest;
    }

    /**
     * Adding the cart language.
     */
    public function processLanguage($params)
    {
        $idLang = Configuration::get('PS_LANG_DEFAULT');
        if (isset($params['id_language'])
            && Validate::isLoadedObject($objLanguage = new Language((int) $params['id_language']))
            && $objLanguage->active
        ) {
            $idLang = $objLanguage->id;
        }

        $this->context->language = new Language((int) $idLang);
        $this->context->cart->id_lang = $idLang;
    }

    /**
     * Adding the cart currency.
     */
    public function processCurrency($params)
    {
        $idCurrency = Configuration::get('PS_CURRENCY_DEFAULT');
        if (isset($params['currency'])
            && ($selectedCurrency = Currency::getIdByIsoCode($params['currency']))
            && Validate::isLoadedObject($objCurrency = new Currency($selectedCurrency))
            && $objCurrency->active
        ) {
            $idCurrency = $selectedCurrency;
        }

        $this->context->currency = new Currency((int) $idCurrency);
        $this->context->cart->id_currency = $idCurrency;
    }

    public function formatRoomTypesInRequestData($data)
    {
        $roomTypes = $data;
        if (count($roomTypes)) {
            $formattedRoomTypes = array();
            foreach ($roomTypes as $roomTypeKey => $roomType) {
                $dateProductJoinKey = $roomType['id_room_type'].'_'.strtotime($roomType['check_in_date']).strtotime($roomType['check_out_date']);

                if (!isset($formattedRoomTypes[$dateProductJoinKey])) {
                    $formattedRoomTypes[$dateProductJoinKey] = $roomType;
                } else {
                    $formattedRoomTypes[$dateProductJoinKey]['number_of_rooms'] += $roomType['number_of_rooms'];
                }

                if (isset($roomType['rooms'])) {
                    $formattedRoomTypes[$dateProductJoinKey]['rooms'] = $this->formatRoomInRequestData($roomType);
                }
            }

            $roomTypes = $formattedRoomTypes;
        }

        return $roomTypes;
    }

    public function formatServicesInRequestData($data)
    {
        $selectedServices = array();
        if (isset($data['services']['service'][0])) {
            $selectedServices = $data['services']['service'];
        } else if (isset($data['services'])
            && !isset($data['services'][0])
            && isset($data['services']['service'])
        ) {
            $selectedServices[] = $data['services']['service'];
        } else if (isset($data['services']) && isset($data['services'][0])) {
            $selectedServices = $data['services'];
        }

        $formattedServices = array();
        foreach ($selectedServices as $service) {
            $key = isset($service['id_service']) ? $service['id_service'] : 'new_'.rand();
            if (isset($service['id_service'])) {
                $formattedServices[$key]['quantity'] = isset($service['quantity']) ? $service['quantity'] : 1;
                $formattedServices[$key]['id_product'] = $service['id_service'];
                if (isset($service['unit_price_without_tax'])) {
                    $formattedServices[$key]['unit_price_without_tax'] = $service['unit_price_without_tax'];
                } else if (isset($service['total_price_without_tax'])) {
                    $formattedServices[$key]['total_price_without_tax'] = $service['total_price_without_tax'];
                }

                if (isset($service['id_tax_rules_group'])) {
                    $formattedServices[$key]['id_tax_rules_group'] = $service['id_tax_rules_group'];
                } else if ($service['total_tax']) {
                    $formattedServices[$key]['total_tax'] = $service['total_tax'];
                }
            } else {
                $service['is_new'] = true;
                $service['quantity'] = isset($service['quantity']) ? $service['quantity'] : 1;
                $formattedServices[$key] = $service;
            }
        }

        return $formattedServices;
    }

    public function formatDemandsInRequestData($data)
    {
        $selectedDemands = array();
        if (isset($data['facilities']['facility'][0])) {
            $selectedDemands = $data['facilities']['facility'];
        } else if (isset($data['facilities'])
            && !isset($data['facilities'][0])
            && isset($data['facilities']['facility'])
        ) {
            $selectedDemands[] = $data['facilities']['facility'];
        } else if (isset($data['facilities']) && isset($data['facilities'][0])) {
            $selectedDemands = $data['facilities'];
        }

        $formattedDemands = array();
        foreach ($selectedDemands as $key => $demand) {
            $formattedDemands[$key]['id_global_demand'] = isset($demand['id_facility']) ? $demand['id_facility'] : 0;
            $formattedDemands[$key]['id_option'] = isset($demand['id_option']) ? $demand['id_option'] : 0;
            if (isset($demand['unit_price_without_tax'])) {
                $formattedDemands[$key]['unit_price_without_tax'] = $demand['unit_price_without_tax'];
            }

            if (isset($demand['id_tax_rules_group'])) {
                $formattedDemands[$key]['id_tax_rules_group'] = $demand['id_tax_rules_group'];
            }
        }

        return $formattedDemands;
    }

    public function formatRoomInRequestData($data)
    {
        $rooms = array();
        if (isset($data['rooms']['room'][0])) {
            $rooms = $data['rooms']['room'];
        } else if (isset($data['rooms'])
            && !isset($data['rooms'][0])
            && isset($data['rooms']['room'])
        ) {
            $rooms[] = $data['rooms']['room'];
        } else if (isset($data['rooms']) && isset($data['rooms'][0])) {
            $rooms = $data['rooms'];
        }

        if (count($rooms)) {
            $formattedRooms = array();
            foreach ($rooms as $roomKey => $room) {
                $selectedDemands = $this->formatDemandsInRequestData($room);
                $selectedServices = $this->formatServicesInRequestData($room);

                $occupancy = array(
                    'adults' => $room['occupancy']['adults'],
                    'children' => $room['occupancy']['children'],
                    'child_ages' => $room['occupancy']['children_ages']
                );

                $rooms[$roomKey]['facilities'] = $selectedDemands;
                $rooms[$roomKey]['services'] = $selectedServices;
                $rooms[$roomKey]['occupancy'] = $occupancy;
                $key = $roomKey;
                if (isset($room['id_room'])) {
                    $key = 'r_'.$room['id_room'];
                }

                $formattedRooms[$key] = $room;
                $formattedRooms[$key]['facilities'] = $selectedDemands;
                $formattedRooms[$key]['services'] = $selectedServices;
                $formattedRooms[$key]['occupancy'] = $occupancy;


                if (isset($room['id_tax_rules_group'])) {
                    $formattedRooms[$key]['id_tax_rules_group'] = $room['id_tax_rules_group'];
                } else if ($room['total_tax']) {
                    $formattedRooms[$key]['total_tax'] = $room['total_tax'];
                }
            }

            $rooms = $formattedRooms;
        }

        return $rooms;
    }

    /**
     * Processing the PUT request.
     */
    public function handlePutRequest($params)
    {
        $this->processCurrency($params);
        $this->processGuestDetails($params['guest_detail']);
        $this->processCustomer($params['guest_detail']);
        $objOrder = new Order((int) $params['id_booking']);
        $objHotelBookingDetail = new HotelBookingDetail();
        $roomsToRemove = array();
        $roomsToAdd = array();
        $roomsToUpdate = array();
        $roomTypes = $this->formatRoomTypesInRequestData($params['room_bookings']);
        if($params['booking_status'] != self::API_BOOKING_STATUS_CANCELLED
            && $params['booking_status'] != self::API_BOOKING_STATUS_REFUNDED
        ) {
            if ($roomsInOrder = $objHotelBookingDetail->getOrderCurrentDataByOrderId($objOrder->id)) {
                foreach ($roomsInOrder as $orderRoomKey => $orderRoom) {
                    $dateProductJoinKey = $orderRoom['id_product'].'_'.strtotime($orderRoom['date_from']).strtotime($orderRoom['date_to']);
                    if (isset($roomTypes[$dateProductJoinKey])) {
                        if (isset($roomTypes[$dateProductJoinKey]['number_of_rooms'])) {
                            $room = array();
                            // if there are multiple rooms then there can be a room at index 1 so we are adding a prifix for the below condition.
                            $room_key = 'r_'.$orderRoom['id_room'];
                            if (isset($roomTypes[$dateProductJoinKey]['rooms'][$room_key])) {
                                $room = $roomTypes[$dateProductJoinKey]['rooms'][$room_key];
                                unset($roomTypes[$dateProductJoinKey]['rooms'][$room_key]);
                            } elseif (isset($roomTypes[$dateProductJoinKey]['rooms'][0])) {
                                $room = array_shift($roomTypes[$dateProductJoinKey]['rooms']);
                            }

                            if ($room) {
                                // update the room only if there is room wise breakdown.
                                $roomsToUpdate[$dateProductJoinKey]['requested'][$orderRoom['id_room']] = $room;
                                $roomsToUpdate[$dateProductJoinKey]['order'][$orderRoom['id_room']] = $roomsInOrder[$orderRoomKey];
                            } else {
                                $roomsToUpdate[$dateProductJoinKey]['order'][$orderRoom['id_room']] = $roomsInOrder[$orderRoomKey];
                            }

                            if ($roomTypes[$dateProductJoinKey]['number_of_rooms'] > 1) {
                                // Since we are traversing the rooms from order one by one.
                                $roomTypes[$dateProductJoinKey]['number_of_rooms']--;
                            } else {
                                unset($roomTypes[$dateProductJoinKey]);
                            }
                        }

                        unset($roomsInOrder[$orderRoomKey]);
                    }
                }

                $roomsToRemove = $roomsInOrder;
            }

            $roomsToAdd = $roomTypes;
            if ($roomsToAdd && !$this->validateRequestedRoomTypes($roomsToAdd, $params['id_property'])) {
                if ($this->error_msg == '') {
                    throw new WebserviceException(
                        Tools::displayError('Requested room(s) not available'),
                        array(404, 400)
                    );
                }

                return false;
            }
        }

        // Adding new rooms in the booking
        if (count($roomsToAdd)) {
            $this->addRoomsInOrder($objOrder->id, $roomsToAdd);
        }

        $objAddress = new Address((int) $objOrder->id_address_tax);
        // only perform any update if request is valid.
        // Update the information for the services that were updated in the existing rooms
        if (count($roomsToUpdate)) {
            $objServiceProductOrderDetail = new ServiceProductOrderDetail();
            $objBookingDemand = new HotelBookingDemands();
            foreach ($roomsToUpdate as $roomsByDate) {
                if (isset($roomsByDate['requested']) && $roomsByDate['requested']) {
                    foreach ($roomsByDate['requested'] as $roomsKey => $room) {
                        $idOrder = $roomsByDate['order'][$roomsKey]['id_order'];
                        $idProduct = $roomsByDate['order'][$roomsKey]['id_product'];
                        $idRoom = $roomsByDate['order'][$roomsKey]['id_room'];
                        $dateFrom = $roomsByDate['order'][$roomsKey]['date_from'];
                        $dateTo = $roomsByDate['order'][$roomsKey]['date_to'];
                        $dateRoomJoinKey = strtotime($dateFrom).strtotime($dateTo).$idProduct.$idRoom;


                        if(Validate::isLoadedObject($objAddress)){
                            $totalPriceTaxExcl = $room['total_price_with_tax'] - $room['total_tax'];
                            if(isset($room['taxes']) && $room['taxes']) {
                                $room['id_tax_rules_group'] = $this->createTaxRulesGroup(($room['total_tax']/$totalPriceTaxExcl)*100, $room['taxes'], $objAddress);
                            } else {
                                $room['id_tax_rules_group'] = $this->createTaxRulesGroup(($room['total_tax']/$totalPriceTaxExcl)*100, false, $objAddress);
                            }
                        }

                        if (isset($room['id_tax_rules_group']) && Validate::isLoadedObject(new TaxRulesGroup((int) $room['id_tax_rules_group']))) {
                            $this->wsRequestedRooms[$dateRoomJoinKey]['id_tax_rules_group'] = $room['id_tax_rules_group'];
                        } else if (!empty($room['total_tax'])) {
                            $totalPriceTaxExcl = $room['total_price_with_tax'] - $room['total_tax'];
                            $this->wsRequestedRooms[$dateRoomJoinKey]['id_tax_rules_group'] = $this->createTaxRulesGroup(($room['total_tax']/$totalPriceTaxExcl)*100, $room['taxes'], $objAddress);
                        }

                        $numDays = HotelHelper::getNumberOfDays($dateFrom, $dateTo);
                        if (isset($room['total_price_with_tax'])
                            && ((float) $room['total_price_with_tax']) != ((float) $roomsByDate['order'][$roomsKey]['total_price_tax_incl'])
                        ) {
                            $this->updateRoomPriceInOrder($room, $roomsByDate['order'][$roomsKey]);
                            $this->wsRequestedRoomTypes[$dateRoomJoinKey]['room'] = $room;
                        }

                        $idHotelBookingDetail = $roomsByDate['order'][$roomsKey]['id'];
                        if ($existingServices = $objServiceProductOrderDetail->getRoomTypeServiceProducts(
                            0,
                            0,
                            0,
                            0,
                            0,
                            0,
                            0,
                            0,
                            null,
                            null,
                            null,
                            0,
                            $idHotelBookingDetail
                        )) {
                            if (!empty($existingServices[$idHotelBookingDetail]['additional_services'])) {
                                $this->removeServicesFromOrderedRoom($existingServices[$idHotelBookingDetail]['additional_services']);
                            }
                        }

                        $this->addServicesInOrderedRoom($room['services'], $idHotelBookingDetail);
                        // Since we don't store the id_global_demand in the order, we will remove the previous ones and add the new ones.
                        $requestedDemands = $room['facilities'];
                        $roomExtraDemand = $objBookingDemand->getRoomTypeBookingExtraDemands(
                            $idOrder,
                            $idProduct,
                            $idRoom,
                            $dateFrom,
                            $dateTo,
                            0
                        );

                        $this->removeDemandsInOrderedRoom($roomExtraDemand);
                    }
                } else if (isset($roomsByDate['order'])
                    && $roomsByDate['order']
                ) {
                    $hotelBookingDetail = array_shift($roomsByDate['order']);
                    if ($existingServices = $objServiceProductOrderDetail->getRoomTypeServiceProducts(
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        null,
                        null,
                        null,
                        0,
                        $hotelBookingDetail['id']
                    )) {
                        if (!empty($existingServices[$hotelBookingDetail['id']]['additional_services'])) {
                            $this->removeServicesFromOrderedRoom($existingServices[$hotelBookingDetail['id']]['additional_services']);
                        }
                    }

                }
            }
        }

        // removing in the last.
        if (count($roomsToRemove)) {
            $this->removeRoomLineFromOrder($params, $roomsToRemove);
        }

        $cartRules = $objOrder->getCartRules();
        //Removing the stored cached object
        $this->removeOrderCartRules($objOrder->id, $cartRules);
        if (isset($params['cart_rules']) && $params['cart_rules']) {
            $this->addCartRulesToOrder($params['cart_rules'], $objOrder->id);
        }

        $this->updateRoomTaxRulesGroupsInOrder($objOrder->id);
        // Calling this after the older cart rules are removed from the order.
        if ($params['booking_status'] != self::API_BOOKING_STATUS_CANCELLED && $params['booking_status'] != self::API_BOOKING_STATUS_REFUNDED) {
            $this->manageOrderPrice($objOrder->id, $params);
        } else {
            $this->adjustPriceForOrderReversal($objOrder->id, $params);
        }

        if (isset($params['booking_status'])) {
            $objOrderState = false;
            if ($params['booking_status'] == self::API_BOOKING_STATUS_CANCELLED) {
                $objOrderState = new OrderState(Configuration::get('PS_OS_CANCELED'));
            } elseif ($params['booking_status'] == self::API_BOOKING_STATUS_REFUNDED) {
                $objOrderState = new OrderState(Configuration::get('PS_OS_REFUND'));
            } elseif ($params['booking_status'] == self::API_BOOKING_STATUS_COMPLETED) {
                $objOrderState = new OrderState(Configuration::get('PS_OS_PAYMENT_ACCEPTED'));
            } elseif ($params['booking_status'] == self::API_BOOKING_STATUS_NEW) {
                $objOrderState =  new OrderState(Configuration::get('PS_OS_AWAITING_PAYMENT'));
                $paymentStatus = false;
                if (isset($params['payment_status'])) {
                    $paymentStatus = $params['payment_status'];
                }

                switch ($paymentStatus) {
                    case self::API_BOOKING_PAYMENT_STATUS_COMPLETED:
                        $objOrderState = new OrderState(Configuration::get('PS_OS_PAYMENT_ACCEPTED'));
                        break;
                    case self::API_BOOKING_PAYMENT_STATUS_PARTIAL:
                        $objOrderState = new OrderState(Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED'));
                        break;
                    case self::API_BOOKING_PAYMENT_STATUS_AWATING:
                        $objOrderState = new OrderState(Configuration::get('PS_OS_AWAITING_PAYMENT'));
                        break;
                    default:
                        $objOrderState = new OrderState(Configuration::get('PS_OS_AWAITING_PAYMENT'));
                        break;
                }
            }

            if ($objOrderState) {
                $statuses = array(Configuration::get('PS_OS_CANCELED'), Configuration::get('PS_OS_REFUND'));
                if (!in_array($objOrder->current_state, $statuses) && in_array($objOrderState->id, $statuses)) {
                    if ($bookings = $objHotelBookingDetail->getOrderCurrentDataByOrderId($objOrder->id)) {
                        $objOrderReturn = new OrderReturn();
                        $objOrderReturn->id_customer = $objOrder->id_customer;
                        $objOrderReturn->id_order = $objOrder->id;
                        $objOrderReturn->state = 0;
                        $objOrderReturn->by_admin = 1;
                        $objOrderReturn->refunded_amount = 0;
                        $objOrderReturn->save();
                        if ($objOrderReturn->id) {
                            foreach ($bookings as $booking) {
                                $objHtlBooking = new HotelBookingDetail($booking['id']);
                                if(Configuration::get('PS_OS_REFUND') == $objOrderState->id){
                                    if(Configuration::get('PS_OS_AWAITING_REMOTE_PAYMENT') == $objOrder->current_state) {
                                        $objHtlBooking->is_cancelled = 1;
                                    } else {
                                        if ($objOrder->is_advance_payment['advance_payment']
                                            || Configuration::get('PS_OS_PAYMENT_ACCEPTED') == $objOrder->current_state
                                            || Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED') == $objOrder->current_state
                                        ) {
                                            $objHtlBooking->is_refunded = 1;
                                        } else {
                                            $objHtlBooking->is_cancelled = 1;
                                        }
                                    }
                                } else {
                                    $objHtlBooking->is_cancelled = 1;
                                }
                                $objHtlBooking->save();

                                $numDays = HotelHelper::getNumberOfDays($objHtlBooking->date_from, $objHtlBooking->date_to);
                                $objOrderReturnDetail = new OrderReturnDetail();
                                $objOrderReturnDetail->id_order_return = $objOrderReturn->id;
                                $objOrderReturnDetail->id_order_detail = $objHtlBooking->id_order_detail;
                                $objOrderReturnDetail->product_quantity = $numDays;
                                $objOrderReturnDetail->id_htl_booking = $objHtlBooking->id;
                                $objOrderReturnDetail->refunded_amount = 0;
                                if (!$objOrder->getCartRules() && $objOrder->getTotalPaid() <= 0) {
                                    $objOrderReturnDetail->id_customization = 1;
                                }
                                $objOrderReturnDetail->save();
                            }
                        }

                        $objOrderReturn->changeIdOrderReturnState(Configuration::get('PS_ORS_REFUNDED'));
                    }
                }
                $this->addOrderHistory($params['id_booking'], $objOrderState);
            }
        }

        if ( isset($params['price_details']['total_price_with_tax'])
            && $objOrder->total_paid_real != $params['price_details']['total_price_with_tax']
            && isset($params['payment_status']) && (!in_array($params['payment_status'], array(
                self::API_BOOKING_PAYMENT_STATUS_AWATING,self::API_BOOKING_STATUS_CANCELLED, self::API_BOOKING_STATUS_REFUNDED)
            ))
        ) {
            $this->addOrderPayment($params);
        }
        $this->deleteWsFeaturePrices();
        $this->getBookingDetails($objOrder->id);
    }

    /**
     * Updating the room price.
     */
    public function updateRoomPriceInOrder($room, $bookingData)
    {
        $idHotelBooking = $bookingData['id'];
        if (Validate::isLoadedObject($objHotelBookingDetail = new HotelBookingDetail((int) $idHotelBooking))) {
            $objOrder = new Order((int) $objHotelBookingDetail->id_order);
            $objCart = new Cart($objOrder->id_cart);
            $objOrderDetail = new OrderDetail((int) $objHotelBookingDetail->id_order_detail);
            //removing the old price
            $objOrder->total_paid -= $objHotelBookingDetail->total_price_tax_incl;
            $objOrder->total_paid_tax_incl -= $objHotelBookingDetail->total_price_tax_incl;
            $objOrder->total_paid_tax_excl -= $objHotelBookingDetail->total_price_tax_excl;
            $objOrder->total_products -= $objHotelBookingDetail->total_price_tax_excl;
            $objOrder->total_products_wt -= $objHotelBookingDetail->total_price_tax_incl;

            $objOrderDetail->total_price_tax_incl -= $objHotelBookingDetail->total_price_tax_incl;
            $objOrderDetail->total_price_tax_excl -= $objHotelBookingDetail->total_price_tax_excl;

            $roomPriceTaxExcl = 0;
            if (isset($room['unit_price_without_tax'])) {
                $roomPriceTaxExcl = $room['unit_price_without_tax'];
            } else if (isset($room['total_price_with_tax']) && isset($room['total_tax'])) {
                $roomPriceTaxExcl = abs($room['total_price_with_tax'] - $room['total_tax']);
            }

            if ($roomPriceTaxExcl) {
                $room['unit_price_without_tax'] = Tools::ps_round($roomPriceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);
                $this->wsFeaturePrices[] = $this->createFeaturePrice(
                    array(
                        'id_product' => (int) $objHotelBookingDetail->id_product,
                        'id_cart' => (int) $objCart->id,
                        'id_guest' => (int) $objCart->id_guest,
                        'date_from' => date('Y-m-d', strtotime($objHotelBookingDetail->date_from)),
                        'date_to' => date('Y-m-d', strtotime($objHotelBookingDetail->date_to)),
                        'id_room' => $objHotelBookingDetail->id_room,
                        'price' => $room['unit_price_without_tax']
                    )
                );
            }

            $roomTotalPrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                $objHotelBookingDetail->id_product,
                $objHotelBookingDetail->date_from,
                $objHotelBookingDetail->date_to,
                0,
                Group::getCurrent()->id,
                $objCart->id,
                $objCart->id_guest,
                $objHotelBookingDetail->id_room,
                0
            );

            $objHotelBookingDetail->total_price_tax_incl = $roomTotalPrice['total_price_tax_excl'] + (isset($room['total_tax']) ? $room['total_tax'] : 0);
            $objHotelBookingDetail->total_price_tax_excl = $roomTotalPrice['total_price_tax_excl'];
            $objHotelBookingDetail->total_paid_amount = $roomTotalPrice['total_price_tax_excl'];
            $objHotelBookingDetail->save();

            // Updating the price
            $objOrderDetail->total_price_tax_incl += $objHotelBookingDetail->total_price_tax_incl;
            $objOrderDetail->total_price_tax_excl += $objHotelBookingDetail->total_price_tax_excl;
            $objOrderDetail->save();

            $objOrder->total_paid += $objHotelBookingDetail->total_price_tax_incl;
            $objOrder->total_paid_tax_incl += $objHotelBookingDetail->total_price_tax_incl;
            $objOrder->total_paid_tax_excl += $objHotelBookingDetail->total_price_tax_excl;
            $objOrder->total_products += $objHotelBookingDetail->total_price_tax_excl;
            $objOrder->total_products_wt += $objHotelBookingDetail->total_price_tax_incl;
            $objOrder->update();

            HotelRoomTypeFeaturePricing::deleteByIdCart($objCart->id);
        }
    }


    /**
     * Adding the service into the order.
     */
    public function addServicesInOrderedRoom(array &$services, $idHotelBookingDetail)
    {
        if ($services) {
            $objHotelBookingDetail = new HotelBookingDetail((int) $idHotelBookingDetail);

            $objOrder = new Order($objHotelBookingDetail->id_order);
            $objAddress = new Address((int)$objOrder->id_address_tax);

            // set context currency So that we can get prices in the order currency
            $this->context->currency = new Currency($objOrder->id_currency);
            $objHotelCartBookingData = new HotelCartBookingData();
            $objRoomTypeServiceProductPrice = new RoomTypeServiceProductPrice();
            $objServiceProductCartDetail = new ServiceProductCartDetail();
            $roomHtlCartInfo = $objHotelCartBookingData->getRoomRowByIdProductIdRoomInDateRange(
                $objHotelBookingDetail->id_cart,
                $objHotelBookingDetail->id_product,
                $objHotelBookingDetail->date_from,
                $objHotelBookingDetail->date_to,
                $objHotelBookingDetail->id_room
            );

            $idTaxRulesGroup = 0;
            if($idOrderDetail = $objHotelBookingDetail->id_order_detail) {
                if(Validate::isLoadedObject($objOrderDetail = new OrderDetail((int)$idOrderDetail))){
                    $idTaxRulesGroup = $objOrderDetail->id_tax_rules_group;
                }
            }

            $this->createCartForOrder($objOrder->id);
            $idOrderInvoice = false;
            if (($objOrderInvoice = $objOrder->getInvoicesCollection()->getFirst())
                && Validate::isLoadedObject($objOrderInvoice)
            ) {
                $idOrderInvoice = $objOrderInvoice->id;
            }

            $objCart = $this->context->cart;
            $formattedServices = array();
            foreach ($services as $serviceKey => &$service) {
                if (!isset($service['id_product'])) {
                    $idServiceTaxRulesGroup = 0;
                    $servicePriceTaxExculded = $service['total_price_with_tax'] - $service['total_tax'];
                    $serviceTaxRate = ($service['total_tax']/$servicePriceTaxExculded)*100;
                    if(isset($service['taxes']) && $service['taxes']) {
                        $idServiceTaxRulesGroup = $this->createTaxRulesGroup($serviceTaxRate, $service['taxes'], $objAddress);
                    } else {
                        $idServiceTaxRulesGroup = $this->createTaxRulesGroup($serviceTaxRate, false, $objAddress);
                    }
                    $service['id_tax_rules_group'] = $idServiceTaxRulesGroup;
                    $serviceKey = $this->createWsService($service, $objHotelBookingDetail->id_product, $idServiceTaxRulesGroup);
                    $service['id_product'] = $serviceKey;
                }

                if (!isset($service['quantity'])) {
                    $service['quantity'] = 1;
                }
                $formattedServices[$serviceKey] = $service;
            }

            $services = $formattedServices;
            foreach ($services as $service) {
                $objServiceProductCartDetail->addServiceProductInCart(
                    $objCart->id,
                    $service['id_product'],
                    $service['quantity'],
                    false,
                    $roomHtlCartInfo['id']
                );
            }

            $unitPriceTaxIncl = 0;
            $unitPriceTaxExcl = 0;
            $productList = $objCart->getProducts(true);
            $objOrderDetail = new OrderDetail();

            $objOrderDetail->createList($objOrder, $objCart, $objOrder->getCurrentOrderState(), $productList, $idOrderInvoice, true);
            foreach ($productList as &$product) {
                if ($idServiceProductCartDetail = $objServiceProductCartDetail->alreadyExists(
                    $objCart->id,
                    $product['id_product'],
                    $roomHtlCartInfo['id']
                )
                ) {
                    $objServiceProductCartDetail = new ServiceProductCartDetail((int) $idServiceProductCartDetail);
                    $insertedServiceProductIdOrderDetail = $objHotelBookingDetail->getLastInsertedServiceIdOrderDetail($objOrder->id, $service['id_product']);
                    $objOrderDetail = new OrderDetail($insertedServiceProductIdOrderDetail);

                    $numDays = 1;
                    if (Product::getProductPriceCalculation($product['id_product']) == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                        $numDays = HotelHelper::getNumberOfDays($objHotelBookingDetail->date_from, $objHotelBookingDetail->date_to);
                    }

                    $quantity = $objServiceProductCartDetail->quantity * $numDays;

                    $unitPriceTaxExcl = Product::getServiceProductPrice(
                        (int) $product['id_product'],
                        0,
                        false,
                        false,
                        false,
                        1,
                        $objHotelBookingDetail->date_from,
                        $objHotelBookingDetail->date_to,
                        $objCart->id
                    )/ $numDays;

                    $unitPriceTaxIncl = Product::getServiceProductPrice(
                        (int) $product['id_product'],
                        0,
                        false,
                        false,
                        true,
                        1,
                        $objHotelBookingDetail->date_from,
                        $objHotelBookingDetail->date_to,
                        $objCart->id
                    )/ $numDays;


                    if ($unitPriceTaxIncl > 0) {
                        $oldTaxMultiplier = $unitPriceTaxExcl / $unitPriceTaxIncl;
                    } else {
                        $oldTaxMultiplier = 1;
                    }

                    $totalPriceTaxExcl = $unitPriceTaxExcl * $quantity;
                    $totalPriceTaxIncl = $unitPriceTaxIncl * $quantity;

                    $objOrderDetail->unit_price_tax_incl -= $unitPriceTaxIncl;
                    $objOrderDetail->unit_price_tax_excl -= $unitPriceTaxExcl;
                    $objOrderDetail->total_price_tax_excl -= $totalPriceTaxExcl;
                    $objOrderDetail->total_price_tax_incl -= $totalPriceTaxIncl;

                    if (isset($services[$product['id_product']]['unit_price_without_tax'])) {
                        $unitPriceTaxExcl = $services[$product['id_product']]['unit_price_without_tax'];
                        $unitPriceTaxIncl = $unitPriceTaxExcl * $oldTaxMultiplier;

                        $totalPriceTaxExcl = $unitPriceTaxExcl * $quantity;
                        $totalPriceTaxIncl =  $unitPriceTaxIncl * $quantity;
                    } elseif (isset($services[$product['id_product']]['total_price_without_tax'])) {
                        $totalPriceTaxExcl = $services[$product['id_product']]['total_price_without_tax'];
                        $totalPriceTaxIncl = $totalPriceTaxExcl * $oldTaxMultiplier;

                        $unitPriceTaxExcl = $totalPriceTaxExcl / $quantity;
                        $unitPriceTaxIncl =  $totalPriceTaxIncl / $quantity;
                    }

                    if (isset($services[$product['id_product']]['id_tax_rules_group'])) {
                        $objAddress = new Address((int) $objOrder->id_address_tax);
                        $objTaxManager = TaxManagerFactory::getManager($objAddress, $services[$product['id_product']]['id_tax_rules_group']);
                        $objTaxCalculator = $objTaxManager->getTaxCalculator();
                        $unitPriceTaxIncl = $objTaxCalculator->addTaxes($unitPriceTaxExcl);

                        $unitPriceTaxIncl = Tools::ps_round($unitPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                        $totalPriceTaxIncl = Tools::ps_round(($unitPriceTaxIncl * $quantity), _PS_PRICE_COMPUTE_PRECISION_);
                    } elseif (isset($services[$product['id_product']]['total_tax'])) {
                        $totalPriceTaxIncl = $totalPriceTaxExcl + $services[$product['id_product']]['total_tax'];
                        $totalPriceTaxIncl = Tools::ps_round(($totalPriceTaxIncl), _PS_PRICE_COMPUTE_PRECISION_);
                        $unitPriceTaxIncl = Tools::ps_round($totalPriceTaxIncl / $quantity, _PS_PRICE_COMPUTE_PRECISION_);
                    }

                    $objServiceProductOrderDetail = new ServiceProductOrderDetail();
                    $objServiceProductOrderDetail->id_product = $product['id_product'];
                    $objServiceProductOrderDetail->id_order = $objHotelBookingDetail->id_order;
                    $objServiceProductOrderDetail->id_order_detail = $objOrderDetail->id;
                    $objServiceProductOrderDetail->id_cart = $objCart->id;
                    $objServiceProductOrderDetail->id_htl_booking_detail = $objHotelBookingDetail->id;
                    $objServiceProductOrderDetail->unit_price_tax_excl = Tools::ps_round($unitPriceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);
                    $objServiceProductOrderDetail->unit_price_tax_incl = Tools::ps_round($unitPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                    $objServiceProductOrderDetail->total_price_tax_excl = Tools::ps_round($totalPriceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);
                    $objServiceProductOrderDetail->total_price_tax_incl = Tools::ps_round($totalPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                    $objServiceProductOrderDetail->name = $product['name'];
                    $objServiceProductOrderDetail->quantity = $objServiceProductCartDetail->quantity;
                    $objServiceProductOrderDetail->save();

                    // update totals amount of order
                    $objOrder->total_products += $objServiceProductOrderDetail->total_price_tax_excl;
                    $objOrder->total_products_wt += $objServiceProductOrderDetail->total_price_tax_incl;

                    $objOrder->total_paid += Tools::ps_round($objServiceProductOrderDetail->total_price_tax_incl, _PS_PRICE_COMPUTE_PRECISION_);
                    $objOrder->total_paid_tax_excl += Tools::ps_round($objServiceProductOrderDetail->total_price_tax_excl, _PS_PRICE_COMPUTE_PRECISION_);
                    $objOrder->total_paid_tax_incl += Tools::ps_round($objServiceProductOrderDetail->total_price_tax_incl, _PS_PRICE_COMPUTE_PRECISION_);

                    $objOrderDetail->unit_price_tax_excl += $unitPriceTaxExcl;
                    $objOrderDetail->unit_price_tax_incl += $unitPriceTaxIncl;
                    $objOrderDetail->total_price_tax_excl += $unitPriceTaxExcl * $objServiceProductOrderDetail->quantity;
                    $objOrderDetail->total_price_tax_incl += $unitPriceTaxIncl * $objServiceProductOrderDetail->quantity;
                }
            }

            $objOrderDetail->update();

            $objOrder->total_discounts += (float)abs($objCart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
            $objOrder->total_discounts_tax_excl += (float)abs($objCart->getOrderTotal(false, Cart::ONLY_DISCOUNTS));
            $objOrder->total_discounts_tax_incl += (float)abs($objCart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
            $objOrder->update();
        }
    }

    /**
     * Since we have to create new cart to add rooms and services in the order.
     * So, we are creating new cart.
     */
    public function createCartForOrder($idOrder)
    {
        $objOrder = new Order((int) $idOrder);
        $objCart = new Cart();
        $objCart->id_shop_group = $objOrder->id_shop_group;
        $objCart->id_shop = $objOrder->id_shop;
        $objCart->id_customer = $objOrder->id_customer;
        $objCart->id_carrier = $objOrder->id_carrier;
        $objCart->id_address_delivery = $objOrder->id_address_delivery;
        $objCart->id_address_invoice = $objOrder->id_address_invoice;
        $objCart->id_currency = $objOrder->id_currency;
        $objCart->id_lang = $objOrder->id_lang;
        $objCart->secure_key = $objOrder->secure_key;
        $objCart->id_guest = Guest::getFromCustomer((int) $objOrder->id_customer);
        // Save new cart
        $objCart->add();

        // Save context (in order to apply cart rule)
        $this->context->cart = $objCart;
        $this->context->customer = new Customer((int) $objOrder->id_customer);
    }

    /**
     * Removing room from the order.
     */
    public function removeRoomLineFromOrder($params, $roomsToRemove)
    {
        $objOrder = new Order((int) $params['id_booking']);
        $objBookingDemand = new HotelBookingDemands();
        $objHotelBookingDetail = new HotelBookingDetail();
        $objServiceProductOrderDetail = new ServiceProductOrderDetail();
        foreach ($roomsToRemove as $roomType) {
            $dateFrom = $roomType['date_from'];
            $dateTo = $roomType['date_to'];
            $quantity = (int) HotelHelper::getNumberOfDays($dateFrom, $dateTo);
            $objOrderDetail = new OrderDetail((int) $roomType['id_order_detail']);
            $idHotelBooking = $roomType['id'];
            $idHotel = $roomType['id_hotel'];
            $bookingPriceTaxIncl = $roomType['total_price_tax_incl'];
            $bookingPriceTaxExcl = $roomType['total_price_tax_excl'];
            $roomExtraDemandTI = $objBookingDemand->getRoomTypeBookingExtraDemands(
                $objOrder->id,
                $roomType['id_product'],
                $roomType['id_room'],
                $dateFrom,
                $dateTo,
                0,
                1,
                1
            );
            $roomExtraDemandTE = $objBookingDemand->getRoomTypeBookingExtraDemands(
                $objOrder->id,
                $roomType['id_product'],
                $roomType['id_room'],
                $dateFrom,
                $dateTo,
                0,
                1,
                0
            );
            $additionlServicesTI = $objServiceProductOrderDetail->getRoomTypeServiceProducts(
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                1,
                1,
                null,
                null,
                0,
                $idHotelBooking
            );
            $additionlServicesTE = $objServiceProductOrderDetail->getRoomTypeServiceProducts(
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                1,
                0,
                null,
                null,
                0,
                $idHotelBooking
            );

            $diffProductsTaxIncl = $bookingPriceTaxIncl;
            $diffProductsTaxExcl = $bookingPriceTaxExcl;
            $objHotelBookingDetail = new HotelBookingDetail((int) $idHotelBooking);
            $roomQuantity = (int) HotelHelper::getNumberOfDays($dateFrom, $dateTo);
            if ($selectedAdditonalServices = $objServiceProductOrderDetail->getRoomTypeServiceProducts(
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                null,
                null,
                null,
                0,
                $idHotelBooking
            )) {
                if (isset($selectedAdditonalServices[$idHotelBooking]['additional_services'])
                    && count($selectedAdditonalServices[$idHotelBooking]['additional_services'])
                ) {
                    foreach ($selectedAdditonalServices[$idHotelBooking]['additional_services'] as $service) {
                        $serviceOrderDetail = new OrderDetail($service['id_order_detail']);
                        $cart_quantity = $service['quantity'];
                        if ($service['price_calculation_method'] == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                            $cart_quantity = $cart_quantity * $quantity;
                        }

                        if ($cart_quantity >= $serviceOrderDetail->product_quantity) {
                            $serviceOrderDetail->delete();
                        } else {
                            $serviceOrderDetail->total_price_tax_incl -= Tools::ps_round($service['total_price_tax_incl'], _PS_PRICE_COMPUTE_PRECISION_);
                            $serviceOrderDetail->total_price_tax_excl -= Tools::ps_round($service['total_price_tax_excl'], _PS_PRICE_COMPUTE_PRECISION_);
                            $serviceOrderDetail->product_quantity -= $cart_quantity;

                            // update taxes
                            $serviceOrderDetail->updateTaxAmount($objOrder);

                            // Save order detail
                            $serviceOrderDetail->update();
                        }
                    }
                }
            }

            // Update Order
            // values changes as values are calculated accoding to the quantity of the product by webkul
            $objOrder->total_paid = Tools::ps_round($objOrder->total_paid - ($diffProductsTaxIncl + $roomExtraDemandTI + $additionlServicesTI), _PS_PRICE_COMPUTE_PRECISION_);
            $objOrder->total_paid_tax_incl = Tools::ps_round($objOrder->total_paid_tax_incl - ($diffProductsTaxIncl + $roomExtraDemandTI + $additionlServicesTI), _PS_PRICE_COMPUTE_PRECISION_);
            $objOrder->total_paid_tax_excl = Tools::ps_round($objOrder->total_paid_tax_excl - ($diffProductsTaxExcl + $roomExtraDemandTE + $additionlServicesTE), _PS_PRICE_COMPUTE_PRECISION_);
            $objOrder->total_products = Tools::ps_round($objOrder->total_products - ($diffProductsTaxExcl + $additionlServicesTE), _PS_PRICE_COMPUTE_PRECISION_);
            $objOrder->total_products_wt = Tools::ps_round($objOrder->total_products_wt - ($diffProductsTaxIncl + $additionlServicesTI), _PS_PRICE_COMPUTE_PRECISION_);

            $objBookingDetail = new HotelBookingDetail($idHotelBooking);
            $bookingPriceTaxIncl = $objBookingDetail->total_price_tax_incl;
            $bookingPriceTaxExcl = $objBookingDetail->total_price_tax_excl;

            if ($objOrderDetail->id_order_invoice != 0) {
                $objOrderInvoice = new OrderInvoice($objOrderDetail->id_order_invoice);
                $objOrderInvoice->total_paid_tax_excl = $objOrder->total_paid_tax_excl;
                $objOrderInvoice->total_paid_tax_incl = $objOrder->total_paid_tax_incl;
                $objOrderInvoice->total_products = $objOrder->total_products;
                $objOrderInvoice->total_products_wt = $objOrder->total_products_wt;
                $objOrderInvoice->update();
            }

            if ($roomQuantity >= $objOrderDetail->product_quantity) {
                $objOrderDetail->delete();
            } else {
                $objOrderDetail->total_price_tax_incl -= $diffProductsTaxIncl;
                $objOrderDetail->total_price_tax_excl -= $bookingPriceTaxExcl;
                $oldRoomQuantity = $objOrderDetail->product_quantity;
                $objOrderDetail->product_quantity = $oldRoomQuantity - $roomQuantity;
                $objOrderDetail->reduction_percent = 0;
                // update taxes
                $objOrderDetail->updateTaxAmount($objOrder);
                // Save order detail
                $objOrderDetail->update();
            }
            if(!Validate::isPrice($objOrder->total_paid)) {
                $objOrder->total_paid = 0;
            }
            $objOrder->update();
            // delete the demands of this booking
            $objBookingDemand->deleteBookingDemands($idHotelBooking);
            $objServiceProductOrderDetail->deleteSeviceProducts(0, $idHotelBooking);
            $objHotelCartBookingData = new HotelCartBookingData();
            $objHotelCartBookingData->deleteOrderedRoomFromCart(
                $objOrder->id,
                $idHotel,
                $roomType['id_room'],
                $dateFrom,
                $dateTo
            );
            $objHotelBookingDetail = new HotelBookingDetail();
            $objHotelBookingDetail->deleteOrderedRoomFromOrder(
                $objOrder->id,
                $idHotel,
                $roomType['id_room'],
                $dateFrom,
                $dateTo
            );
            $objCart = new Cart($roomType['id_cart']);
            $objCart->updateQty($quantity, $roomType['id_product'], null, false, 'down', 0, null, true);
        }
    }


    /**
     * Adding the cart rules in to the order.
     */
    public function addCartRulesToOrder($cartRules, $idOrder)
    {
        $objOrder = new Order((int) $idOrder);
        $cartRulesFormatted = array();
        foreach ($cartRules as $key => $cartRule) {
            $cartRulesFormatted[$key]['code'] = $cartRule['code'];
            $cartRulesFormatted[$key]['value'] = $cartRule['value'];
            $cartRulesFormatted[$key]['type'] = $cartRule['type'];
            $cartRulesFormatted[$key]['currency'] = isset($cartRule['currency']) ? $cartRule['currency'] : '';
        }

        if ($idCartRules = $this->createCartRules($cartRulesFormatted)) {
            foreach ($idCartRules as $idCartRule) {
                $objCartRule = new CartRule($idCartRule);
                $invoiceCartRules = array();
                if ($invoiceCollection = $objOrder->getInvoicesCollection()) {
                    foreach ($invoiceCollection as $orderInvoice) {
                        if ((int) $objCartRule->reduction_amount) {
                            if (!($objCartRule->reduction_amount > $orderInvoice->total_paid_tax_incl)) {
                                $this->error_msg = Tools::displayError('The discount value is greater than the order invoice total.').$orderInvoice->getInvoiceNumberFormatted(Context::getContext()->language->id, (int)$objOrder->id_shop).')';
                                $invoiceCartRules[$orderInvoice->id]['value_tax_incl'] = Tools::ps_round($objCartRule->reduction_amount, _PS_PRICE_COMPUTE_PRECISION_);
                                $invoiceCartRules[$orderInvoice->id]['value_tax_excl'] = Tools::ps_round($objCartRule->reduction_amount / (1 + ($objOrder->getTaxesAverageUsed() / 100)), _PS_PRICE_COMPUTE_PRECISION_);

                                $this->applyDiscountOnInvoice($orderInvoice, $invoiceCartRules[$orderInvoice->id]['value_tax_incl'], $invoiceCartRules[$orderInvoice->id]['value_tax_excl']);
                            }
                        } elseif ($objCartRule->reduction_percent) {
                            $invoiceCartRules[$orderInvoice->id]['value_tax_incl'] = Tools::ps_round($objOrder->total_paid_tax_incl * $objCartRule->reduction_percent / 100, _PS_PRICE_COMPUTE_PRECISION_);
                            $invoiceCartRules[$orderInvoice->id]['value_tax_excl'] = Tools::ps_round($objOrder->total_paid_tax_excl * $objCartRule->reduction_percent / 100, _PS_PRICE_COMPUTE_PRECISION_);

                            $this->applyDiscountOnInvoice($orderInvoice, $invoiceCartRules[$orderInvoice->id]['value_tax_incl'], $invoiceCartRules[$orderInvoice->id]['value_tax_excl']);
                        }
                    }
                } else {
                    if ($objCartRule->reduction_percent) {
                        $invoiceCartRules[0]['value_tax_incl'] = Tools::ps_round($objOrder->total_paid_tax_incl * $objCartRule->reduction_percent / 100, _PS_PRICE_COMPUTE_PRECISION_);
                        $invoiceCartRules[0]['value_tax_excl'] = Tools::ps_round($objOrder->total_paid_tax_excl * $objCartRule->reduction_percent / 100, _PS_PRICE_COMPUTE_PRECISION_);
                    } elseif ($objCartRule->reduction_amount) {
                        $invoiceCartRules[0]['value_tax_incl'] = Tools::ps_round($objCartRule->reduction_amount, _PS_PRICE_COMPUTE_PRECISION_);
                        $invoiceCartRules[0]['value_tax_excl'] = Tools::ps_round($objCartRule->reduction_amount / (1 + ($objOrder->getTaxesAverageUsed() / 100)), _PS_PRICE_COMPUTE_PRECISION_);
                    }
                }

                // Create OrderCartRule
                foreach ($invoiceCartRules as $idInvoice => $rule) {
                    $ObjOrderCartRule = new OrderCartRule();
                    $ObjOrderCartRule->id_order = $objOrder->id;
                    $ObjOrderCartRule->id_cart_rule = $objCartRule->id;
                    $ObjOrderCartRule->id_order_invoice = $idInvoice;
                    $ObjOrderCartRule->name = $objCartRule->code;
                    $ObjOrderCartRule->value = $rule['value_tax_incl'];
                    $ObjOrderCartRule->value_tax_excl = $rule['value_tax_excl'];
                    $ObjOrderCartRule->free_shipping = 0;
                    $ObjOrderCartRule->add();

                    $objOrder->total_discounts = Tools::ps_round($objOrder->total_discounts + $ObjOrderCartRule->value, _PS_PRICE_COMPUTE_PRECISION_);
                    $objOrder->total_discounts_tax_incl = Tools::ps_round($objOrder->total_discounts_tax_incl + $ObjOrderCartRule->value, _PS_PRICE_COMPUTE_PRECISION_);
                    $objOrder->total_discounts_tax_excl = Tools::ps_round($objOrder->total_discounts_tax_excl + $ObjOrderCartRule->value_tax_excl, _PS_PRICE_COMPUTE_PRECISION_);
                    $objOrder->total_paid = Tools::ps_round($objOrder->total_paid - $ObjOrderCartRule->value, _PS_PRICE_COMPUTE_PRECISION_);
                    $objOrder->total_paid_tax_incl = Tools::ps_round($objOrder->total_paid_tax_incl - $ObjOrderCartRule->value, _PS_PRICE_COMPUTE_PRECISION_);
                    $objOrder->total_paid_tax_excl = Tools::ps_round($objOrder->total_paid_tax_excl - $ObjOrderCartRule->value_tax_excl, _PS_PRICE_COMPUTE_PRECISION_);
                }
            }
        }

        $objOrder->update();
    }

    /**
     * Removing the cart rules from the order.
     */
    public function removeOrderCartRules($idOrder, $cartRules = array())
    {
        if ($cartRules) {
            $objOrder = new Order($idOrder);
            foreach ($cartRules as $rule) {
                $objOrderCartRule = new OrderCartRule($rule['id_order_cart_rule']);
                if ($objOrderCartRule->id_order_invoice) {
                    $objOrderInvoice = new OrderInvoice($objOrderCartRule->id_order_invoice);
                    $objOrderInvoice->total_discount_tax_excl = ($objOrderInvoice->total_discount_tax_excl - $objOrderCartRule->value_tax_excl) > 0 ? ($objOrderInvoice->total_discount_tax_excl - $objOrderCartRule->value_tax_excl) : 0;
                    $objOrderInvoice->total_discount_tax_incl = ($objOrderInvoice->total_discount_tax_incl - $objOrderCartRule->value) > 0 ? ($objOrderInvoice->total_discount_tax_incl - $objOrderCartRule->value) : 0;

                    $objOrderInvoice->total_paid_tax_excl += $objOrderCartRule->value_tax_excl;
                    $objOrderInvoice->total_paid_tax_incl += $objOrderCartRule->value;

                    // Update Order Invoice
                    $objOrderInvoice->update();
                }

                $objOrder->total_discounts = ($objOrder->total_discounts - $objOrderCartRule->value) > 0 ? ($objOrder->total_discounts - $objOrderCartRule->value) : 0;
                $objOrder->total_discounts_tax_incl = ($objOrder->total_discounts_tax_incl - $objOrderCartRule->value) > 0 ? ($objOrder->total_discounts_tax_incl - $objOrderCartRule->value) : 0;
                $objOrder->total_discounts_tax_excl = ($objOrder->total_discounts_tax_excl - $objOrderCartRule->value_tax_excl) > 0 ? ($objOrder->total_discounts_tax_excl - $objOrderCartRule->value_tax_excl) : 0;

                $objOrder->total_paid += $objOrderCartRule->value;
                $objOrder->total_paid_tax_incl += $objOrderCartRule->value;
                $objOrder->total_paid_tax_excl += $objOrderCartRule->value_tax_excl;

                // Delete Order Cart Rule and update Order
                $objOrderCartRule->delete();
            }

            $objOrder->update();
        }
    }

    /**
     * Adding rooms in the cart.
     */
    public function addRoomsInCart(array &$roomTypes, $idAddressTax = false)
    {
        $objRoomType = new HotelRoomType();
        $objHotelCartBookingData = new HotelCartBookingData();
        $roomUnitSelectionType = Configuration::get('PS_FRONT_ROOM_UNIT_SELECTION_TYPE');

        if($idAddressTax) {
            $objAddress = new Address((int)$idAddressTax);
        } else if (isset($this->id_hotel) && $this->id_hotel) {
            if(Validate::isLoadedObject($objHotelBranchInformation = new HotelBranchInformation((int)$this->id_hotel))) {
                $objAddress = new Address((int)$objHotelBranchInformation->getHotelIdAddress());
            }
        }
        $objBookingDetail = new HotelBookingDetail();

        $quantityWise = false;
        if ($roomUnitSelectionType != HotelBookingDetail::PS_ROOM_UNIT_SELECTION_TYPE_OCCUPANCY) {
            $quantityWise = true;
        }

        foreach ($roomTypes as &$roomType) {
            $dateFrom = date('Y-m-d', strtotime($roomType['check_in_date']));
            $dateTo = date('Y-m-d', strtotime($roomType['check_out_date']));
            $roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($roomType['id_room_type']);
            $idHotel = $roomTypeInfo['id_hotel'];
            $occupancy = array(
                array(
                    'adults' => $roomTypeInfo['adults'],
                    'children' => $roomTypeInfo['children'],
                    'child_ages' => array()
                )
            );

            if (isset($roomType['rooms']) && count($roomType['rooms'])) {
                foreach ($roomType['rooms'] as &$room) {
                    $roomServices = array();
                    $roomTaxes = array();
                    $totalPriceTaxExcl = $room['total_price_with_tax'] - $room['total_tax'];
                    if(isset($room['taxes']) && $room['taxes']) {
                        $room['id_tax_rules_group'] = $this->createTaxRulesGroup(($room['total_tax']/$totalPriceTaxExcl)*100, $room['taxes'], $objAddress);
                    } else {
                        $room['id_tax_rules_group'] = $this->createTaxRulesGroup(($room['total_tax']/$totalPriceTaxExcl)*100, false, $objAddress);
                    }

                    if (isset($room['services']) && $room['services']) {
                        $idServiceTaxRulesGroup = 0;
                        foreach ($room['services'] as $serviceKey => &$service) {
                            $servicePriceTaxExculded = $service['total_price_with_tax'] - $service['total_tax'];
                            $serviceTaxRate = ($service['total_tax']/$servicePriceTaxExculded)*100;
                            if(isset($service['taxes']) && $service['taxes']) {
                                $idServiceTaxRulesGroup = $this->createTaxRulesGroup($serviceTaxRate, $service['taxes'], $objAddress);
                            } else {
                                $idServiceTaxRulesGroup = $this->createTaxRulesGroup($serviceTaxRate, false, $objAddress);
                            }
                            $service['id_tax_rules_group'] = $idServiceTaxRulesGroup;
                            $service['id_product'] = $this->createWsService($service, $roomType['id_room_type'], $idServiceTaxRulesGroup);

                            $serviceKey = $service['id_product'];
                            if (!isset($service['quantity'])) {
                                $service['quantity'] = 1;
                            }
                            $roomServices[$serviceKey] = $service;
                        }
                        unset($service);
                    }

                    // since we cannot update them after ordering them and will have to replace them if orderd here. So, we will not add them for now.
                    $roomDemands = json_encode(array());

                    $idRoom = 0;
                    if (isset($room['id_room'])) {
                        $idRoom = $room['id_room'];
                    }

                    $child_ages = array();
                    if(isset($room['occupancy']['children_ages'])) {
                        $child_ages = $room['occupancy']['children_ages'];
                    } else if(isset($room['occupancy']['child_ages'])) {
                        $child_ages = $room['occupancy']['child_ages'];
                    }

                    $occupancy = array(
                        array(
                            'adults' => $room['occupancy']['adults'],
                            'children' => $room['occupancy']['children'],
                            'child_ages' => $child_ages
                        )
                    );

                    if ($quantityWise) {
                        $occupancy = 1; // since we are adding rooms one by one into the cart.
                    }

                    //updated code
                    $bookingParams = array(
                        'date_from' => $dateFrom,
                        'date_to' => $dateTo,
                        'hotel_id' => $idHotel,
                        'id_room_type' => $roomType['id_room_type'],
                        'only_search_data' => 1
                    );

                    if (($hotelRoomData = $objBookingDetail->dataForFrontSearch($bookingParams))
                        && isset($hotelRoomData['rm_data'][$roomType['id_room_type']]['data']['available'])
                        && $hotelRoomData['rm_data'][$roomType['id_room_type']]['data']['available']
                    ) {
                        if ($idHtlCartBookingData = $objHotelCartBookingData->updateCartBooking(
                            $roomType['id_room_type'],
                            $occupancy,
                            'up',
                            $idHotel,
                            $idRoom,
                            $dateFrom,
                            $dateTo,
                            $roomDemands,
                            $roomServices,
                            $this->context->cart->id,
                            $this->context->cart->id_guest
                        )) {
                            $objCartBookingData = new HotelCartBookingData((int) $idHtlCartBookingData);
                            $dateRoomJoinKey = strtotime($dateFrom).strtotime($dateTo).$objCartBookingData->id_product.$objCartBookingData->id_room;
                            // To update the price after valiate order is called.
                            if (isset($room['facilities'])
                                && $room['facilities']
                            ) {
                                $roomDemands = json_encode($room['facilities']);
                            }
                            $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'] = $roomServices;
                            $this->wsRequestedRoomTypes[$dateRoomJoinKey]['demands'] = $roomDemands;
                            $this->wsRequestedRoomTypes[$dateRoomJoinKey]['room'] = $room;

                            if (isset($room['id_tax_rules_group']) && Validate::isLoadedObject(new TaxRulesGroup((int) $room['id_tax_rules_group']))) {
                                $this->wsRequestedRooms[$dateRoomJoinKey]['id_tax_rules_group'] = $room['id_tax_rules_group'];
                            } else if (isset($room['total_tax'])) {
                                $this->wsRequestedRooms[$dateRoomJoinKey]['total_tax'] = $room['total_tax'];
                            }


                            $roomPriceTaxExcl = 0;
                            if (isset($room['unit_price_without_tax'])) {
                                $roomPriceTaxExcl = $room['unit_price_without_tax'];
                            } else if (isset($room['total_price_with_tax']) && isset($room['total_tax'])) {
                                $roomPriceTaxExcl = abs($room['total_price_with_tax'] - $room['total_tax']);
                            }

                            $room['unit_price_without_tax'] = Tools::ps_round($roomPriceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);
                            $this->wsFeaturePrices[] = $this->createFeaturePrice(
                                array(
                                    'id_product' => (int) $roomType['id_room_type'],
                                    'id_cart' => (int) $this->context->cart->id,
                                    'id_guest' => (int) $this->context->cart->id_guest,
                                    'date_from' => date('Y-m-d', strtotime($dateFrom)),
                                    'date_to' => date('Y-m-d', strtotime($dateTo)),
                                    'id_room' => $objCartBookingData->id_room,
                                    'price' => $room['unit_price_without_tax']
                                )
                            );
                        }
                    } else {
                        // added coded for partial booking
                        $params = array(
                            'date_from' => $dateFrom,
                            'date_to' => $dateTo,
                            'hotel_id' => $idHotel,
                            'id_room_type' => $roomType['id_room_type'],
                            'num_rooms' => $roomType['number_of_rooms'],
                            'search_available' => 0,
                            'search_partial' => 1,
                            'search_booked' => 0,
                            'search_unavai' => 0,
                            'id_cart' => $this->context->cart->id,
                            'id_guest' => null,
                            'search_cart_rms' => 0,
                            'occupancy' => $occupancy
                        );

                        $partiallyAvailableRooms = $objBookingDetail->getBookingData($params)['rm_data'];
                        $partialRooms = array();

                        foreach ($partiallyAvailableRooms as $availableRooms) {
                            foreach ($availableRooms['data'] as $key => $availableRoom) {
                                if($key == 'partially_available') {
                                    foreach ($availableRoom as $avlRoom) {

                                        $avl_room = reset($avlRoom['rooms']);
                                        $avl_date_from = $avlRoom['date_from'];
                                        $avl_date_to = $avlRoom['date_to'];

                                        if(isset($partialRooms[$avl_room['id_room']])){
                                            if($partialRooms[$avl_room['id_room']]['date_to'] == $avl_date_from) {
                                                $partialRooms[$avl_room['id_room']]['date_to'] = $avl_date_to;
                                            } else {
                                                $partialRooms[$avl_room['id_room'].'_'.$avl_date_from.'_'.$avl_date_to] = array(
                                                    'date_from' => $avl_date_from,
                                                    'date_to' => $avl_date_to,
                                                    'id_room' => $avl_room['id_room']
                                                );
                                            }
                                        } else {
                                            $partialRooms[$avl_room['id_room']] = array(
                                                'date_from' => $avl_date_from,
                                                'date_to' => $avl_date_to,
                                                'id_room' => $avl_room['id_room']
                                            );
                                        }
                                    }
                                }
                            }
                        }

                        foreach ($partialRooms as $avlRoom) {
                            if ($idHtlCartBookingData = $objHotelCartBookingData->updateCartBooking(
                                $roomType['id_room_type'],
                                $occupancy,
                                'up',
                                $idHotel,
                                $avlRoom['id_room'],
                                $avlRoom['date_from'],
                                $avlRoom['date_to'],
                                $roomDemands,
                                $roomServices,
                                $this->context->cart->id,
                                $this->context->cart->id_guest
                            )) {
                                $objCartBookingData = new HotelCartBookingData((int) $idHtlCartBookingData);

                                /* Partial Booking indicator */
                                Hook::exec(
                                    'actionAdminPartialBookingIndicator',
                                    array(
                                        'objCartBookingData' => $objCartBookingData,
                                        'id_room_type' => $roomType['id_room_type']
                                    )
                                );
                                /* End */

                                $dateRoomJoinKey = strtotime($dateFrom).strtotime($dateTo).$objCartBookingData->id_product.$objCartBookingData->id_room;
                                // To update the price after valiate order is called.
                                if (isset($room['facilities'])
                                    && $room['facilities']
                                ) {
                                    $roomDemands = json_encode($room['facilities']);
                                }

                                $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'] = $roomServices;
                                $this->wsRequestedRoomTypes[$dateRoomJoinKey]['demands'] = $roomDemands;
                                $this->wsRequestedRoomTypes[$dateRoomJoinKey]['room'] = $room;
                                if (isset($room['id_tax_rules_group']) && Validate::isLoadedObject(new TaxRulesGroup((int) $room['id_tax_rules_group']))) {
                                    $this->wsRequestedRooms[$dateRoomJoinKey]['id_tax_rules_group'] = $room['id_tax_rules_group'];
                                } elseif (isset($room['total_tax'])) {
                                    $this->wsRequestedRooms[$dateRoomJoinKey]['total_tax'] = $room['total_tax'];
                                }
                            }
                        }
                    }
                    // end here
                }
                unset($room);
            } else {
                $roomWiseOccupancy = $occupancy;
                if (isset($roomType['number_of_rooms'])) {
                    while ($roomType['number_of_rooms'] > 1) {
                        $roomWiseOccupancy[] = reset($occupancy);
                        $roomType['number_of_rooms']--;
                    }
                }

                if ($quantityWise) {
                    $roomWiseOccupancy = count($roomWiseOccupancy);
                }

                $roomDemands = json_encode(array());
                $roomServices = array();
                $objHotelCartBookingData->updateCartBooking(
                    $roomType['id_room_type'],
                    $roomWiseOccupancy,
                    'up',
                    $idHotel,
                    0,
                    $dateFrom,
                    $dateTo,
                    $roomDemands,
                    $roomServices,
                    $this->context->cart->id,
                    $this->context->cart->id_guest
                );
            }
        }
        unset($roomType);
        $this->removeAutoAddedServicesFromCart();
    }


    /**
     * Creating new service product for the request.
     * @return int id of new created service product
     */
    public function createWsService(array &$service, $idRoomType, $idServiceTaxRulesGroup = 0)
    {
        // A single service will be created and will be deleted after the booking is completed.
        $objProduct = new Product();
        if(!trim($service['name']) || !Validate::isCatalogName(trim($service['name']))) {
            $service['name'] = 'Api service';
        }
        foreach (Language::getLanguages(false) as $language) {
            $objProduct->name[$language['id_lang']] = $service['name'];
            $linkRewrite = Tools::link_rewrite($service['name']);
            $objProduct->link_rewrite[$language['id_lang']] = $linkRewrite;
        }
        $price = 0;
        $quantity = 1;
        if (isset($service['quantity'])) {
            $quantity = $service['quantity'];
        }

        if(isset($service['price_mode'])) {
            if($service['price_mode'] == self::SERVICE_PRICE_MODE_PER_STAY) {
            $service['price_mode'] = self::API_SERVICE_PRICE_MODE_PER_BOOKING;
            } else if($service['price_mode'] == self::SERVICE_PRICE_MODE_PER_NIGHT) {
                $service['price_mode'] = self::API_SERVICE_PRICE_MODE_PER_NIGHT;
            } else if ($service['price_mode'] == self::SERVICE_PRICE_MODE_PER_PERSON_PER_NIGHT) {
                $service['price_mode'] = self::API_SERVICE_PRICE_MODE_PER_PERSON_PER_NIGHT;
            } else {
                $service['price_mode'] = self::API_SERVICE_PRICE_MODE_PER_BOOKING;
            }
        } else {
            $service['price_mode'] = self::API_SERVICE_PRICE_MODE_PER_BOOKING;
        }

    	$price = $service['total_price_with_tax'] - $service['total_tax'];
        $service['total_price_without_tax'] = $price;

        if ($this->context->cart->id_currency != (int)Configuration::get('PS_CURRENCY_DEFAULT')) {
            $currency = Currency::getCurrencyInstance($this->context->cart->id_currency);
            $price = Tools::ps_round($price/$currency->conversion_rate, _PS_PRICE_COMPUTE_PRECISION_);
        }


        $objProduct->booking_product = false;
        $objProduct->available_for_order = true;
        $objProduct->id_category_default = Configuration::get('PS_SERVICE_CATEGORY');
        $objProduct->active = 1;
        $objProduct->id_shop_default = Configuration::get('PS_SHOP_DEFAULT');
        $objProduct->indexed = false;
        $objProduct->condition = 'new';
        $objProduct->price = $price;
        $objProduct->out_of_stock = false;
        $objProduct->id_tax_rules_group = $idServiceTaxRulesGroup;
        $objProduct->is_virtual = 1;
        $objProduct->show_price = true;
        $objProduct->auto_add_to_cart = false;
        $objProduct->show_at_front = false;
        $objProduct->price_calculation_method = $service['price_mode'];
        $objProduct->redirect_type = '404';
        $objProduct->visibility = 'none';
        $objProduct->minimal_quantity = 1;
        $objProduct->selling_preference_type = Product::SELLING_PREFERENCE_WITH_ROOM_TYPE;
        $objProduct->allow_multiple_quantity = true;
        $objProduct->max_quantity = $quantity;
        if ($objProduct->save()) {
            $objProduct->updateCategories(array(
                Configuration::get('PS_SERVICE_CATEGORY')
            ));
            $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
            $objRoomTypeServiceProduct->addRoomProductLink(
                $objProduct->id,
                array($idRoomType),
                RoomTypeServiceProduct::WK_ELEMENT_TYPE_ROOM_TYPE
            );

            StockAvailable::setQuantity($objProduct->id, 0, 99999999);
            $this->wsIdServices[] = $objProduct->id;
        }

        return $objProduct->id;
    }

    /**
     * Removing the non requested auto added services.
     */
    public function removeAutoAddedServicesFromCart()
    {
        if (Validate::isLoadedobject($this->context->cart)) {
            $this->context->cart->getProducts(true);
            $objServiceProductCartDetail = new ServiceProductCartDetail();
            if ($serviceProducts = $objServiceProductCartDetail->getServiceProductsInCart(
                $this->context->cart->id,
                [],
                0,
                null,
                null,
                null,
                null,
                null,
                0,
                1
            )) {
                foreach ($serviceProducts as $serviceProduct) {
                    $dateRoomJoinKey = strtotime($serviceProduct['date_from']).strtotime($serviceProduct['date_to']).$serviceProduct['id_product'].$serviceProduct['id_room'];
                    // Checking if the auto add service was sent in the request
                    if (!isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$serviceProduct['id_product']])
                        && ($idServiceProductCartDetail = $objServiceProductCartDetail->alreadyExists(
                        $this->context->cart->id,
                        $serviceProduct['id_product'],
                        $serviceProduct['id_hotel_cart_booking'])
                    )) {
                        $objServiceProductCartDetail = new ServiceProductCartDetail((int) $idServiceProductCartDetail);
                        $this->context->cart->updateQty($objServiceProductCartDetail->quantity, $objServiceProductCartDetail->id_product, null, false, 'down');
                        $objServiceProductCartDetail->delete();
                    }
                }
            }
        }
    }

    /**
     * Adding the cart rules, after creating them.
     */
    public function applyCartRules($params)
    {
        $error = '';
        if ($cartRules = $this->formatCartRulesInRequestData($params)) {
            if ($cartRules = $this->createCartRules($cartRules)) {
                foreach ($cartRules as $cartRule) {
                    $objCartRule = new CartRule((int) $cartRule);
                    if (($error = $objCartRule->checkValidity($this->context))
                        && $error != ''
                    ) {
                        break;
                    } else {
                        $this->context->cart->addCartRule($objCartRule->id);
                    }
                }
            }
        }

        return $error;
    }

    /**
     * Getting cart rules and creating new ones if they do not exists.
     */
    public function createCartRules($requestedCartRules)
    {
        $cartRules = array();
        if ($requestedCartRules) {
            $languags = Language::getIDs(true);
            foreach ($requestedCartRules as $cartRule) {
                if (Validate::isLoadedObject($objCartRule = new CartRule(CartRule::getIdByCode($cartRule['code'])))) {
                    $cartRules[] = $objCartRule->id;
                } else {
                    $idCurrency = $this->context->currency->id;
                    if (isset($cartRule['currency'])
                        && ($selectedCurrency = Currency::getIdByIsoCode($cartRule['currency']))
                        && Validate::isLoadedObject($objCurrency = new Currency($selectedCurrency))
                        && $objCurrency->active
                    ) {
                        $idCurrency = $objCurrency->id;
                    }

                    $objCartRule = new CartRule();
                    foreach ($languags as $idLang) {
                        $objCartRule->name[$idLang] = $cartRule['code'];
                    }

                    $objCartRule->quantity = 1;
                    $objCartRule->quantity_per_user = 1;
                    $objCartRule->id_customer = $this->bookingCustomer->id;
                    $objCartRule->highlight = 1;
                    $objCartRule->date_from = date('Y-m-d H:i:s');
                    $objCartRule->date_to = date('Y-m-d H:i:s', strtotime($objCartRule->date_from) + (3600 * 24 * 365.25));;
                    $objCartRule->active = 1;
                    $objCartRule->reduction_tax = false;
                    $objCartRule->minimum_amount_currency = $idCurrency;
                    $objCartRule->reduction_currency = $idCurrency;
                    $objCartRule->code = $cartRule['code'];
                    if ($cartRule['type'] == self::API_CART_RULE_VALUE_TYPE_PERCENTAGE) {
                        $objCartRule->reduction_percent = $cartRule['value'];
                    } else if ($cartRule['type'] == self::API_CART_RULE_VALUE_TYPE_AMOUNT) {
                        $objCartRule->reduction_amount = $cartRule['value'];
                    }

                    if ($objCartRule->add()) {
                        $cartRules[] = $objCartRule->id;
                        $this->wsCartRules[] = $objCartRule->id;
                    }
                }
            }
        }

        return $cartRules;
    }


    public function addRoomsInOrder($idOrder, $roomTypes)
    {
        $objOrder = new Order($idOrder);
        $objHotelBookingDetail = new HotelBookingDetail();
        $objRoomType = new HotelRoomType();
        $this->createCartForOrder($objOrder->id);
        $this->addRoomsInCart($roomTypes, $objOrder->id_address_tax);
        $idOrderInvoice = false;
        if (($objOrderInvoice = $objOrder->getInvoicesCollection()->getFirst())
            && Validate::isLoadedObject($objOrderInvoice)
        ) {
            $idOrderInvoice = $objOrderInvoice->id;
        }

        $objCart = $this->context->cart;
        $objOrderDetail = new OrderDetail();
        $objOrderDetail->createList($objOrder, $objCart, $objOrder->getCurrentOrderState(), $objCart->getProducts(true), $idOrderInvoice);

        // update totals amount of order
        // creating the new object to reload the data changes made while removing the rooms.
        $objOrder = new Order($idOrder);
        $objOrder->total_products += (float)$objCart->getOrderTotal(false, Cart::ONLY_ROOMS);
        $objOrder->total_products_wt += (float)$objCart->getOrderTotal(true, Cart::ONLY_ROOMS);
        $objOrder->total_paid += Tools::ps_round((float)($objCart->getOrderTotal(true, Cart::ONLY_ROOMS)), _PS_PRICE_COMPUTE_PRECISION_);
        $objOrder->total_paid_tax_excl += Tools::ps_round((float)($objCart->getOrderTotal(false, Cart::ONLY_ROOMS)), _PS_PRICE_COMPUTE_PRECISION_);
        $objOrder->total_paid_tax_incl += Tools::ps_round((float)($objCart->getOrderTotal(true, Cart::ONLY_ROOMS)), _PS_PRICE_COMPUTE_PRECISION_);
        $objOrder->total_discounts += (float)abs($objCart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
        $objOrder->total_discounts_tax_excl += (float)abs($objCart->getOrderTotal(false, Cart::ONLY_DISCOUNTS));
        $objOrder->total_discounts_tax_incl += (float)abs($objCart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));

        // Save changes of order
        $res = $objOrder->update();
        $objAddress = new Address((int) $objOrder->id_address_tax);
        $idLang = (int) $this->context->cart->id_lang;
        foreach ($roomTypes as $roomType) {
            $orderDetails = $objHotelBookingDetail->getPsOrderDetailsByProduct($roomType['id_room_type'], $objOrder->id);
            $idOrderDetail = end($orderDetails)['id_order_detail']; // to get the max id_order_detail
            $objCartBookingData = new HotelCartBookingData();
            if ($cartBookingData = $objCartBookingData->getOnlyCartBookingData(
                $this->context->cart->id,
                $this->context->cart->id_guest,
                $roomType['id_room_type']
            )) {

                foreach ($cartBookingData as $cb_k => $cb_v) {

                    $objCartBookingData = new HotelCartBookingData($cb_v['id']);
                    $objCartBookingData->id_order = $objOrder->id;
                    // $objCartBookingData->save();
                    $objBookingDetail = new HotelBookingDetail();
                    $objBookingDetail->id_product = $roomType['id_room_type'];
                    $objBookingDetail->id_order = $objOrder->id;
                    $objBookingDetail->id_order_detail = $idOrderDetail;
                    $objBookingDetail->id_cart = $this->context->cart->id;
                    $objBookingDetail->id_room = $objCartBookingData->id_room;
                    $objBookingDetail->id_hotel = $objCartBookingData->id_hotel;
                    $objBookingDetail->id_customer = $objOrder->id_customer;
                    $objBookingDetail->booking_type = $objCartBookingData->booking_type;
                    $objBookingDetail->id_status = 1;
                    $objBookingDetail->comment = $objCartBookingData->comment;
                    $objBookingDetail->room_type_name = Product::getProductName($roomType['id_room_type'], null, $objOrder->id_lang);

                    $objBookingDetail->date_from = $objCartBookingData->date_from;
                    $objBookingDetail->date_to = $objCartBookingData->date_to;
                    $objBookingDetail->adults = $objCartBookingData->adults;
                    $objBookingDetail->children = $objCartBookingData->children;
                    $objBookingDetail->child_ages = $objCartBookingData->child_ages;

                    $total_price = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                        $roomType['id_room_type'],
                        $objCartBookingData->date_from,
                        $objCartBookingData->date_to,
                        0,
                        Group::getCurrent()->id,
                        $this->context->cart->id,
                        $this->context->cart->id_guest,
                        $objCartBookingData->id_room,
                        0
                    );

                    $objBookingDetail->total_price_tax_excl = $total_price['total_price_tax_excl'];
                    $objBookingDetail->total_price_tax_incl = $total_price['total_price_tax_incl'];
                    $objBookingDetail->total_paid_amount = Tools::ps_round($total_price['total_price_tax_incl'], _PS_PRICE_COMPUTE_PRECISION_);
                    if ($idOrderInvoice) {
                        $objOrderInvoice = new OrderInvoice($idOrderInvoice);
                        $objOrderInvoice->total_paid_tax_excl += Tools::ps_round($total_price['total_price_tax_excl'], _PS_PRICE_COMPUTE_PRECISION_);
                        $objOrderInvoice->total_paid_tax_incl += Tools::ps_round($total_price['total_price_tax_incl'], _PS_PRICE_COMPUTE_PRECISION_);
                        $objOrderInvoice->total_products += Tools::ps_round($total_price['total_price_tax_excl'], _PS_PRICE_COMPUTE_PRECISION_);
                        $objOrderInvoice->total_products_wt += Tools::ps_round($total_price['total_price_tax_incl'], _PS_PRICE_COMPUTE_PRECISION_);
                        $objOrderInvoice->update();
                    }

                    // Save hotel information/location/contact
                    if (Validate::isLoadedObject($objRoom = new HotelRoomInformation($objCartBookingData->id_room))) {
                        $objBookingDetail->room_num = $objRoom->room_num;
                    }

                    if (Validate::isLoadedObject($objHotelBranch = new HotelBranchInformation(
                        $objCartBookingData->id_hotel,
                        $idLang
                    ))) {
                        $addressInfo = $objHotelBranch->getAddress($objCartBookingData->id_hotel);
                        $objBookingDetail->hotel_name = $objHotelBranch->hotel_name;
                        $objBookingDetail->city = $addressInfo['city'];
                        $objBookingDetail->state = State::getNameById($addressInfo['id_state']);
                        $objBookingDetail->country = Country::getNameById($idLang, $addressInfo['id_country']);
                        $objBookingDetail->zipcode = $addressInfo['postcode'];;
                        $objBookingDetail->phone = $addressInfo['phone'];
                        $objBookingDetail->email = $objHotelBranch->email;
                        $objBookingDetail->check_in_time = $objHotelBranch->check_in;
                        $objBookingDetail->check_out_time = $objHotelBranch->check_out;
                    }

                    if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($roomType['id_room_type'])) {
                        $objBookingDetail->adults = $objCartBookingData->adults;
                        $objBookingDetail->children = $objCartBookingData->children;
                        $objBookingDetail->child_ages = $objCartBookingData->child_ages;
                    }

                    if ($objBookingDetail->save()) {
                        $objServiceProductCartDetail = new ServiceProductCartDetail();
                        $dateRoomJoinKey = strtotime($objCartBookingData->date_from).strtotime($objCartBookingData->date_to).$objCartBookingData->id_product.$objCartBookingData->id_room;

                        if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'])
                            && ($services = $objServiceProductCartDetail->getServiceProductsInCart($objCartBookingData->id_cart,
                            [],
                            null,
                            $objCartBookingData->id))
                        ) {
                            foreach ($services as $service) {
                                $insertedServiceProductIdOrderDetail = $objBookingDetail->getLastInsertedServiceIdOrderDetail($objOrder->id, $service['id_product']);
                                $numDays = 1;
                                if (Product::getProductPriceCalculation($service['id_product']) == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                                    $numDays = HotelHelper::getNumberOfDays($objBookingDetail->date_from, $objBookingDetail->date_to);
                                }
                                $totalPriceTaxExcl = Product::getServiceProductPrice(
                                    $service['id_product'],
                                    0,
                                    false,
                                    $roomTypeInfo['id'],
                                    false,
                                    $service['quantity'],
                                    $objBookingDetail->date_from,
                                    $objBookingDetail->date_to
                                );
                                $totalPriceTaxIncl = Product::getServiceProductPrice(
                                    $service['id_product'],
                                    0,
                                    false,
                                    $roomTypeInfo['id'],
                                    true,
                                    $service['quantity'],
                                    $objBookingDetail->date_from,
                                    $objBookingDetail->date_to
                                );
                                $unitPriceTaxExcl = $totalPriceTaxExcl / ($numDays * $service['quantity']);
                                $unitPriceTaxIncl = $totalPriceTaxIncl / ($numDays * $service['quantity']);
                                if ($unitPriceTaxExcl > 0) {
                                    $taxMultiplier = $unitPriceTaxIncl / $unitPriceTaxExcl;
                                } else {
                                    $taxMultiplier = 1;
                                }

                                $quantity = $service['quantity'] * $numDays;
                                $objOrderDetail = new OrderDetail($insertedServiceProductIdOrderDetail);
                                $totalPriceTaxExclOld = $totalPriceTaxExcl;
                                $totalPriceTaxInclOld = $totalPriceTaxIncl;
                                if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']])
                                    && isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['unit_price_without_tax'])
                                ) {
                                    $totalPriceTaxExcl = 0;
                                    $totalPriceTaxIncl = 0;
                                    if ((int) $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['unit_price_without_tax']) {
                                        $totalPriceTaxExcl = $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['unit_price_without_tax'];
                                        $totalPriceTaxIncl = $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['unit_price_without_tax'] * $taxMultiplier;
                                    }

                                    if ($totalPriceTaxExcl > 0) {
                                        $unitPriceTaxExcl = $totalPriceTaxExcl / $quantity;
                                        $unitPriceTaxIncl = $totalPriceTaxIncl / $quantity;
                                    }
                                } else if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_price_without_tax'])) {
                                    $totalPriceTaxExcl = $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_price_without_tax'];
                                    $totalPriceTaxIncl = $totalPriceTaxExcl * $taxMultiplier;

                                    $unitPriceTaxExcl = $totalPriceTaxExcl / $quantity;
                                    $unitPriceTaxIncl =  $totalPriceTaxIncl / $quantity;
                                }

                                $objAddress = new Address((int) $objOrder->id_address_tax);
                                if (!empty($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_tax'])) {
                                    $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group'] = $this->createTaxRulesGroup(($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_tax']/$totalPriceTaxExcl)*100, false, $objAddress);
                                }

                                if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group']) && $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group']) {
                                    $objTaxManager = TaxManagerFactory::getManager($objAddress, $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group']);
                                    $objTaxCalculator = $objTaxManager->getTaxCalculator();
                                    $unitPriceTaxIncl = $objTaxCalculator->addTaxes($unitPriceTaxExcl);

                                    $unitPriceTaxIncl = Tools::ps_round($unitPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                                    $totalPriceTaxIncl = Tools::ps_round(($unitPriceTaxIncl * $quantity), _PS_PRICE_COMPUTE_PRECISION_);
                                }

                                $priceDiffTaxExcl = Tools::ps_round($totalPriceTaxExcl - $totalPriceTaxExclOld, _PS_PRICE_COMPUTE_PRECISION_);
                                $priceDiffTaxIncl = Tools::ps_round($totalPriceTaxIncl - $totalPriceTaxInclOld, _PS_PRICE_COMPUTE_PRECISION_) ;
                                $objOrderDetail->total_price_tax_excl += $priceDiffTaxExcl;
                                $objOrderDetail->total_price_tax_incl += $priceDiffTaxIncl;

                                $objOrderDetail->unit_price_tax_excl = Tools::ps_round(($objOrderDetail->total_price_tax_excl / $objOrderDetail->product_quantity), _PS_PRICE_COMPUTE_PRECISION_);
                                $objOrderDetail->unit_price_tax_incl = Tools::ps_round(($objOrderDetail->total_price_tax_incl / $objOrderDetail->product_quantity), _PS_PRICE_COMPUTE_PRECISION_);
                                $objOrderDetail->save();

                                $objServiceProductOrderDetail = new ServiceProductOrderDetail();
                                $objServiceProductOrderDetail->id_product = $service['id_product'];
                                $objServiceProductOrderDetail->id_order = $objBookingDetail->id_order;
                                $objServiceProductOrderDetail->id_order_detail = $insertedServiceProductIdOrderDetail;
                                $objServiceProductOrderDetail->id_cart = $this->context->cart->id;
                                $objServiceProductOrderDetail->id_htl_booking_detail = $objBookingDetail->id;
                                $objServiceProductOrderDetail->unit_price_tax_excl = Tools::ps_round($unitPriceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);
                                $objServiceProductOrderDetail->unit_price_tax_incl = Tools::ps_round($unitPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                                $objServiceProductOrderDetail->total_price_tax_excl = Tools::ps_round($totalPriceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);
                                $objServiceProductOrderDetail->total_price_tax_incl = Tools::ps_round($totalPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                                $objServiceProductOrderDetail->name = $service['name'];
                                $objServiceProductOrderDetail->quantity = $service['quantity'];
                                $objServiceProductOrderDetail->save();

                                $objOrder->total_products += $objServiceProductOrderDetail->total_price_tax_excl;
                                $objOrder->total_products_wt += $objServiceProductOrderDetail->total_price_tax_incl;

                                if ($idOrderInvoice) {
                                    $objOrderInvoice = new OrderInvoice($idOrderInvoice);
                                    $objOrderInvoice->total_paid_tax_excl += Tools::ps_round($totalPriceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);
                                    $objOrderInvoice->total_paid_tax_incl += Tools::ps_round($totalPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                                    $objOrderInvoice->total_products += Tools::ps_round($totalPriceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);
                                    $objOrderInvoice->total_products_wt += Tools::ps_round($totalPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                                    $objOrderInvoice->update();
                                }

                                $objOrder->total_paid += Tools::ps_round($totalPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                                $objOrder->total_paid_tax_excl += Tools::ps_round($totalPriceTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);
                                $objOrder->total_paid_tax_incl += Tools::ps_round($totalPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);

                                $isAutoAdded = false;
                                if ($objOrderDetail->product_auto_add && $objOrderDetail->product_price_addition_type == Product::PRICE_ADDITION_TYPE_WITH_ROOM) {
                                    $isAutoAdded = true;
                                }

                                if (!$isAutoAdded
                                    && isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group'])
                                    && $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group']
                                ) {
                                    $this->saveTaxCalculator($objOrderDetail->id, $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group']);
                                }
                            }

                            $objOrder->save();
                        }
                    }
                }
            }
        }

        $this->deleteWsFeaturePrices();
        if(isset($this->context->cart) && $this->context->cart->id) {
            HotelRoomTypeFeaturePricing::deleteFeaturePrices($this->context->cart->id);
        }
    }


    public function formatCartRulesInRequestData($data)
    {
        $formattedCartRules = array();
        if (isset($data['cart_rules']['cart_rule'][0])) {
            $formattedCartRules = $data['cart_rules']['cart_rule'];
        } elseif (isset($data['cart_rules'])
            && !isset($data['cart_rules'][0])
            && isset($data['cart_rules']['cart_rule'])
        ) {
            $formattedCartRules[] = $data['cart_rules']['cart_rule'];
        } elseif (isset($data['cart_rules'][0])) {
            $formattedCartRules = $data['cart_rules'];
        }

        return $formattedCartRules;
    }

    public function getPostRequest($head = false)
    {
        $putresource = fopen('php://input', 'r');
        $inputXML = '';
        while ($putData = fread($putresource, 1024)) {
            $inputXML .= $putData;
        }

        fclose($putresource);

        return json_decode($inputXML, true);
    }


    // Adjust the price to 0 when the booking is cancelled or refunded
    public function adjustPriceForOrderReversal($idOrder, $params)
    {
        $objOrder = new Order((int)$idOrder);
        $objServiceProductOrderDetail = new ServiceProductOrderDetail();
        $objHotelBookingDetail = new HotelBookingDetail();
        if(isset($params['price_details']['total_price_with_tax'])){
            if ($roomsInOrder = $objHotelBookingDetail->getOrderCurrentDataByOrderId($objOrder->id)) {
                foreach ($roomsInOrder as $roomInfo) {
                    $objHotelBookingDetail = new HotelBookingDetail((int) $roomInfo['id']);
                    $objHotelBookingDetail->total_price_tax_incl = 0;
                    $objHotelBookingDetail->total_price_tax_excl = 0;
                    $objHotelBookingDetail->unit_price_tax_incl = 0;
                    $objHotelBookingDetail->unit_price_tax_excl = 0;
                    $objHotelBookingDetail->save();

                    // Updating the price
                    $objOrderDetail = new OrderDetail((int) $objHotelBookingDetail->id_order_detail);
                    $orderDetailsList[] = $objOrderDetail->id;
                    $objOrderDetail->total_price_tax_incl = 0;
                    $objOrderDetail->total_price_tax_excl = 0;
                    $objOrderDetail->unit_price_tax_incl = 0;
                    $objOrderDetail->unit_price_tax_excl = 0;
                    $objOrderDetail->save();

                    // Update OrderInvoice of this OrderDetail
                    if ($objOrderDetail->id_order_invoice != 0) {
                        // values changes as values are calculated accoding to the quantity of the product by webkul
                        $objOrderInvoice = new OrderInvoice($objOrderDetail->id_order_invoice);
                        $objOrderInvoice->total_paid_tax_excl = 0;
                        $objOrderInvoice->total_paid_tax_incl = 0;
                        $objOrderInvoice->update();
                    }
                }

                $objOrder->total_paid_tax_incl = 0;
                $objOrder->total_products_wt = 0;
                $objOrder->total_products = 0;
                $objOrder->total_paid_tax_excl = 0;
                $objOrder->total_paid_real = 0;
                $objOrder->total_paid = 0;
                $objOrder->save();

                if($services = $objServiceProductOrderDetail->getroomTypeServiceProducts((int)$objOrder->id)){
                    foreach ($services as $key => $service) {
                        if(isset($service['additional_services']) && $service['additional_services']){
                            foreach($service['additional_services'] as $additionalService){
                                if($idServiceProductOrderDetail = $additionalService['id_service_product_order_detail']){
                                    if(Validate::isLoadedObject($objServiceProductOrderDetail = new ServiceProductOrderDetail((int) $idServiceProductOrderDetail))) {
                                        // Updating the price
                                        $objOrderDetail = new OrderDetail((int) $objServiceProductOrderDetail->id_order_detail);
                                        $orderDetailsList[] = $objOrderDetail->id;
                                        $objOrderDetail->total_price_tax_incl = 0;
                                        $objOrderDetail->total_price_tax_excl = 0;
                                        $objOrderDetail->unit_price_tax_incl = 0;
                                        $objOrderDetail->unit_price_tax_excl = 0;
                                        $objOrderDetail->save();

                                        $objServiceProductOrderDetail->unit_price_tax_excl = 0;
                                        $objServiceProductOrderDetail->unit_price_tax_incl = 0;
                                        $objServiceProductOrderDetail->total_price_tax_excl = 0;
                                        $objServiceProductOrderDetail->total_price_tax_incl = 0;
                                        $objServiceProductOrderDetail->save();
                                    }
                                }
                            }
                        }
                    }
                }

                if($orderDetailsList) {
                    Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'order_detail_tax` WHERE id_order_detail IN ('.implode(',', $orderDetailsList).')');
                }

            }
        }

    }

    /**
     * Deleting the service from the order.
     */
    public function removeServicesFromOrderedRoom($services)
    {
        if (count($services)) {
            foreach ($services as $service) {
                $idServiceProductOrderDetail = $service['id_service_product_order_detail'];
                if (Validate::isLoadedObject($objServiceProductOrderDetail = new ServiceProductOrderDetail((int) $idServiceProductOrderDetail))) {
                    $objOrderDetail = new OrderDetail((int) $objServiceProductOrderDetail->id_order_detail);
                    $priceTaxExcl = $objServiceProductOrderDetail->total_price_tax_excl;
                    $priceTaxIncl = $objServiceProductOrderDetail->total_price_tax_incl;
                    $objHotelBookingDetail = new HotelBookingDetail($objServiceProductOrderDetail->id_htl_booking_detail);
                    $numDays = 1;
                    if (Product::getProductPriceCalculation($service['id_product']) == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                        $numDays = HotelHelper::getNumberOfDays($objHotelBookingDetail->date_from, $objHotelBookingDetail->date_to);
                    }

                    $quantity = $objOrderDetail->product_quantity * $numDays;
                    if ($objServiceProductOrderDetail->delete()) {
                        $objOrder = new Order($objServiceProductOrderDetail->id_order);
                        if ($quantity >= $objOrderDetail->product_quantity) {
                            $objOrderDetail->delete();
                        } else {
                            $objOrderDetail->product_quantity -= $quantity;

                            $objOrderDetail->total_price_tax_excl -= $priceTaxExcl;
                            $objOrderDetail->total_price_tax_incl -= $priceTaxIncl;

                            $objOrderDetail->updateTaxAmount($objOrder);

                            $objOrderDetail->update();
                        }

                        $objOrder->total_paid_tax_excl -= $priceTaxExcl;
                        $objOrder->total_paid_tax_incl -= $priceTaxIncl;
                        $objOrder->total_paid -= $priceTaxIncl;

                        $objOrder->update();
                    }
                }
            }
        }
    }

    /**
     * Deleting the demands into the order.
    */
    public function removeDemandsInOrderedRoom($demands)
    {
        if (count($demands)) {
            foreach ($demands as $demand) {
                $idBookingDemand = $demand['id_booking_demand'];
                if (Validate::isLoadedObject($objBookingDemand = new HotelBookingDemands($idBookingDemand))) {
                    if ($objBookingDemand->deleteBookingDemandTaxDetails($idBookingDemand)) {
                        if ($objBookingDemand->delete()) {
                            if (Validate::isLoadedObject($objBookingDetail = new HotelBookingDetail($objBookingDemand->id_htl_booking))) {
                                // change order total
                                $objOrder = new Order($objBookingDetail->id_order);
                                $objOrder->total_paid_tax_excl -= $objBookingDemand->total_price_tax_excl;
                                $objOrder->total_paid_tax_incl -= $objBookingDemand->total_price_tax_incl;
                                $objOrder->total_paid -= $objBookingDemand->total_price_tax_incl;
                                $objOrder->save();

                                $objOrderDetail = new OrderDetail($objBookingDetail->id_order_detail);
                                // Update OrderInvoice of this OrderDetail
                                if ($objOrderDetail->id_order_invoice != 0) {
                                    // values changes as values are calculated accoding to the quantity of the product by webkul
                                    $objOrderInvoice = new OrderInvoice($objOrderDetail->id_order_invoice);
                                    $objOrderInvoice->total_paid_tax_excl -= $objBookingDemand->total_price_tax_excl;
                                    $objOrderInvoice->total_paid_tax_incl -= $objBookingDemand->total_price_tax_incl;
                                    $objOrderInvoice->update();
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    protected function applyDiscountOnInvoice($objOrderInvoice, $valueTaxIncl, $valueTaxExcl)
    {
        // Update OrderInvoice
        $objOrderInvoice->total_discount_tax_incl += $valueTaxIncl;
        $objOrderInvoice->total_discount_tax_excl += $valueTaxExcl;
        $objOrderInvoice->total_paid_tax_incl -= $valueTaxIncl;
        $objOrderInvoice->total_paid_tax_excl -= $valueTaxExcl;
        $objOrderInvoice->update();
    }

    /**
     * Adding the demands into the order.
     */
    public function addDemandsInOrderedRoom($demands, $idHotelBooking)
    {
        if (Validate::isLoadedObject($objBookingDetail = new HotelBookingDetail((int) $idHotelBooking))) {
            if ($demands) {
                $objOrder = new Order($objBookingDetail->id_order);
                // set context currency So that we can get prices in the order currency
                $this->context->currency = new Currency($objOrder->id_currency);

                $objAddress = new Address((int) $objOrder->id_address_tax);
                $idLang = (int) $objOrder->id_lang;
                $idProduct = $objBookingDetail->id_product;
                $objHtlBkDtl = new HotelBookingDetail();
                $objRoomDemandPrice = new HotelRoomTypeDemandPrice();
                foreach ($demands as $demand) {
                    $idGlobalDemand = $demand['id_global_demand'];
                    $idOption = $demand['id_option'];
                    $objBookingDemand = new HotelBookingDemands();
                    $objBookingDemand->id_htl_booking = $idHotelBooking;
                    $objGlobalDemand = new HotelRoomTypeGlobalDemand($idGlobalDemand, $idLang);
                    if ($idOption) {
                        $objOption = new HotelRoomTypeGlobalDemandAdvanceOption($idOption, $idLang);
                        $objBookingDemand->name = $objOption->name;
                    } else {
                        $idOption = 0;
                        $objBookingDemand->name = $objGlobalDemand->name;
                    }

                    $unitPriceTaxExcl = HotelRoomTypeDemand::getPriceStatic($idProduct, $idGlobalDemand, $idOption, 0);
                    $unitPriceTaxIncl = HotelRoomTypeDemand::getPriceStatic($idProduct, $idGlobalDemand, $idOption, 1);
                    $taxMultiplier = 1;
                    if ($unitPriceTaxExcl > 0) {
                        $taxMultiplier = $unitPriceTaxIncl / $unitPriceTaxExcl;
                    }

                    $qty = 1;
                    if ($objGlobalDemand->price_calc_method == HotelRoomTypeGlobalDemand::WK_PRICE_CALC_METHOD_EACH_DAY) {
                        $numDays = HotelHelper::getNumberOfDays(
                            $objBookingDetail->date_from,
                            $objBookingDetail->date_to
                        );
                        if ($numDays > 1) {
                            $qty *= $numDays;
                        }
                    }

                    $totalPriceTaxExcl = $unitPriceTaxExcl * $qty;
                    $totalPriceTaxIncl = $unitPriceTaxIncl * $qty;
                    if (isset($demand['unit_price_without_tax'])) {
                        $unitPriceTaxExcl = $demand['unit_price_without_tax'];
                        $unitPriceTaxIncl = $demand['unit_price_without_tax'] * $taxMultiplier;
                        $totalPriceTaxExcl = $unitPriceTaxExcl * $qty;
                        $totalPriceTaxIncl = $unitPriceTaxIncl * $qty;
                    }

                    if (isset($demand['id_tax_rules_group'])) {
                        $objTaxManager = TaxManagerFactory::getManager(
                            $objAddress,
                            $demand['id_tax_rules_group']
                        );
                        $objTaxManager = TaxManagerFactory::getManager($objAddress, $demand['id_tax_rules_group']);
                        $objTaxCalculator = $objTaxManager->getTaxCalculator();

                        $unitPriceTaxIncl = $objTaxCalculator->addTaxes($unitPriceTaxExcl);
                        $totalPriceTaxIncl = $unitPriceTaxIncl * $qty;
                    } else {
                        $objTaxManager = TaxManagerFactory::getManager(
                            $objAddress,
                            $objGlobalDemand->id_tax_rules_group
                        );
                        $objTaxCalculator = $objTaxManager->getTaxCalculator();
                    }

                    $objBookingDemand->unit_price_tax_excl = $unitPriceTaxExcl;
                    $objBookingDemand->unit_price_tax_incl = $unitPriceTaxIncl;
                    $objBookingDemand->total_price_tax_excl = $totalPriceTaxExcl;
                    $objBookingDemand->total_price_tax_incl = $totalPriceTaxIncl;
                    $objOrderDetail = new OrderDetail($objBookingDetail->id_order_detail);

                    // Update OrderInvoice of this OrderDetail
                    if ($objOrderDetail->id_order_invoice != 0) {
                        // values changes as values are calculated accoding to the quantity of the product by webkul
                        $objOrderInvoice = new OrderInvoice($objOrderDetail->id_order_invoice);
                        $objOrderInvoice->total_paid_tax_excl += $objBookingDemand->total_price_tax_excl;
                        $objOrderInvoice->total_paid_tax_incl += $objBookingDemand->total_price_tax_incl;
                        $objOrderInvoice->update();
                    }

                    // change order total
                    $objOrder->total_paid_tax_excl += $objBookingDemand->total_price_tax_excl;
                    $objOrder->total_paid_tax_incl += $objBookingDemand->total_price_tax_incl;
                    $objOrder->total_paid += $objBookingDemand->total_price_tax_incl;
                    $objBookingDemand->price_calc_method = $objGlobalDemand->price_calc_method;
                    $objBookingDemand->id_tax_rules_group = $objGlobalDemand->id_tax_rules_group;
                    if ($objBookingDemand->save()
                        && Validate::isLoadedObject($objAddress)
                    ) {
                        $objBookingDemand->tax_computation_method = (int)$objTaxCalculator->computation_method;
                        $objBookingDemand->tax_calculator = $objTaxCalculator;
                        // Now save tax details of the extra demand
                        $objBookingDemand->setBookingDemandTaxDetails();
                    }
                }

                $objOrder->save();
            }
        }
    }

    /**
     * Adding the cart customer.
     */
    public function processCustomer($customerDetails)
    {
        $this->context->cookie->id_guest = $this->context->cart->id_guest;
        if (!isset($this->bookingCustomer->id)) {
            $objCustomer = new Customer();
            $objCustomer->firstname = $customerDetails['firstname'];
            $objCustomer->lastname = $customerDetails['lastname'];
            $objCustomer->email = $customerDetails['email'];
            $objCustomer->passwd = md5(time()._COOKIE_KEY_);
            $objCustomer->phone = (isset($customerDetails['phone']) ? $customerDetails['phone'] : '');
            $objCustomer->cleanGroups();
            $objCustomer->add();
            $this->bookingCustomer = $objCustomer;
        } else {
            if (isset($customerDetails['firstname']) && Validate::isName($customerDetails['firstname'])) {
                $this->bookingCustomer->firstname = $customerDetails['firstname'];
            }

            if (isset($customerDetails['lastname']) && Validate::isName($customerDetails['lastname'])) {
                $this->bookingCustomer->lastname = $customerDetails['lastname'];
            }

            if (isset($customerDetails['email']) && Validate::isEmail($customerDetails['email'])) {
                $this->bookingCustomer->email = $customerDetails['email'];
            }

            if (isset($customerDetails['phone']) && Validate::isPhoneNumber($customerDetails['phone'])) {
                $this->bookingCustomer->phone = $customerDetails['phone'];
            }

            $this->bookingCustomer->save();
        }

        // Since the address is validated if even a single address field is present in the request.
        if (isset($customerDetails['country_code'])
            && $customerDetails['country_code']
        ) {
            $customerDetails['id_country'] = Country::getByIso($customerDetails['country_code']);
            $objCountry = new Country($customerDetails['id_country']);
            if ($objCountry->contains_states) {
                $customerDetails['id_state'] = State::getIdByIso($customerDetails['state_code']);
            }

            $active = true;
            $cache_id = 'Address::getFirstCustomerAddressId_'.(int) $this->bookingCustomer->id.'-'.(bool)$active;
            Cache::clean($cache_id);
            if ($idAddress = Address::getFirstCustomerAddressId($this->bookingCustomer->id)) {
                $objAddress = new Address((int) $idAddress);
            } else {
                $objAddress = new Address();
                $objAddress->alias = 'Generated by bookings API';
                $objAddress->id_customer = $this->bookingCustomer->id;
                $objAddress->auto_generated = true;
            }

            $objAddress->firstname = $customerDetails['firstname'];
            $objAddress->lastname = $customerDetails['lastname'];
            if (isset($customerDetails['phone'])) {
                $objAddress->phone = $customerDetails['phone'];
            }

            $objAddress->address1 = $customerDetails['address'];
            $objAddress->city = $customerDetails['city'];
            $objAddress->postcode = isset($customerDetails['zip']) ? $customerDetails['zip'] : '';
            $objAddress->id_country = $customerDetails['id_country'];
            $objAddress->id_state = isset($customerDetails['id_state']) ? $customerDetails['id_state'] : 0;

            $objAddress->save();
        }

        // to remove the older non ordered cart for this customer.
        $this->context->cookie->id_cart = $this->context->cart->id;
        $this->context->updateCustomer($this->bookingCustomer, 1);
    }


    /**
     * Validating the PUT request.
     */
    public function validatePutRequest($params)
    {
        $objCustomer = new Customer();
        $this->error_msg = '';
        if (!isset($params['id_booking']) || !$params['id_booking']) {
            $this->error_msg = Tools::displayError('id is required with PUT requests.');
        } elseif (!Validate::isLoadedObject(new Order($params['id_booking']))) {
            $this->error_msg = Tools::displayError('Booking not found.');
        } elseif (!isset($params['booking_status'])) {
            $this->error_msg = Tools::displayError('Invalid booking status.');
        } elseif (!isset($params['room_bookings'])
            || !count($params['room_bookings'])
        ) {
            $this->error_msg = Tools::displayError('Rooms not found in the request.');
        } elseif (isset($params['guest_detail']['id_customer'])
            && !Validate::isLoadedObject(new Customer($params['guest_detail']['id_customer']))
        ) {
            $this->error_msg = Tools::displayError('Invalid ID customer.');
        } elseif (!isset($params['id_property'])
            || !Validate::isLoadedObject(new HotelBranchInformation((int) $params['id_property']))
        ) {
            $this->error_msg = Tools::displayError('Please provide a valid id_property.');
        } elseif (!isset($params['guest_detail']['email'])
            || !Validate::isEmail($params['guest_detail']['email'])
            || !$objCustomer->getByEmail($params['guest_detail']['email'])
        ) {
            $this->error_msg = Tools::displayError('Customer not found.');
        } elseif (!$this->validateAddressFields($params['guest_detail'])
            && $this->error_msg == ''
        ) {
            $this->error_msg = Tools::displayError('Invalid address provided.');
        } elseif (!$this->validatePutRequestRoomTypes($params['room_bookings'])
            && $this->error_msg == ''
        ) {
            $this->error_msg = Tools::displayError('Requested room(s) not available');
        }

        if ($this->error_msg == '') {
            return true;
        }

        return false;
    }

    /**
     * Validating the PUT request Room types.
     */
    public function validatePutRequestRoomTypes($roomTypes)
    {
        foreach ($roomTypes as $roomType) {
            if ($this->validateRoomType($roomType)) {
                if (isset($roomType['rooms']) && count($roomType['rooms'])) {
                    foreach ($roomType['rooms'] as $room) {
                        if (!isset($room['id_room'])) {
                            if ($roomType['number_of_rooms']) {
                                $roomType['number_of_rooms']--;
                            } else {
                                return false;
                            }
                        } elseif (isset($room['id_room'])
                            && (
                                !Validate::isLoadedObject($objHotelRoomInfomation = new HotelRoomInformation($room['id_room']))
                                || $objHotelRoomInfomation->id_product != $roomType['id_room_type']
                            )
                        ) {
                            $this->error_msg = Tools::displayError('Invalid Id room.');
                            return false;
                        }

                        if (isset($room['id_tax_rules_group']) && !Validate::isLoadedObject(new TaxRulesGroup((int) $room['id_tax_rules_group']))) {
                            $this->error_msg = Tools::displayError('Invalid id_tax_rules_group.');
                            return false;
                        } elseif (isset($room['total_tax']) && !Validate::isPrice($room['total_tax'])) {
                            $this->error_msg = Tools::displayError('Invalid total tax.');
                            return false;
                        }
                    }
                }
            } else {
                return false;
            }
        }

        return true;
    }

    //process customer details to create bookings successfully
    public function processCustomerDetails(array &$inputData)
    {
        if(empty(trim($inputData['guest_detail']['firstname'])) || !Validate::isGenericName($inputData['guest_detail']['firstname'])) {
            $inputData['guest_detail']['firstname'] = 'QloApps';
        }

        if(empty(trim($inputData['guest_detail']['lastname'])) || !Validate::isGenericName($inputData['guest_detail']['lastname'])) {
            $inputData['guest_detail']['lastname'] = $inputData['guest_detail']['firstname'];
        }

        if(empty(trim($inputData['guest_detail']['phone'])) || !Validate::isPhoneNumber($inputData['guest_detail']['phone'])) {
            $inputData['guest_detail']['phone'] = '12345678';
        }

        $guestEmail = trim($inputData['guest_detail']['email']);
        if ($inputData['booking_status'] != self::API_BOOKING_STATUS_NEW) {
            if(Validate::isLoadedObject($objOrder = new Order((int)$inputData['id_booking']))) {
                if(Validate::isLoadedObject($objCustomer = new Customer((int)$objOrder->id_customer))) {
                    $inputData['guest_detail']['email'] = $objCustomer->email;
                }
            }
        } else if (empty($guestEmail) || !Validate::isEmail($guestEmail)) {
            $inputData['guest_detail']['email'] = Tools::strtolower(trim($inputData['guest_detail']['firstname']));
            $inputData['guest_detail']['email'] .= Tools::strtolower(trim($inputData['guest_detail']['lastname']));
            $inputData['guest_detail']['email'] .= Tools::passwdGen(3, 'NUMERIC').'@qloapps.com';
        }

        if (!isset($inputData['guest_detail']['city']) || empty(trim($inputData['guest_detail']['city'])) || !Validate::isCityName(trim($inputData['guest_detail']['city']))) {
            $inputData['guest_detail']['city'] = 'Demo City';
        }

        if (!isset($inputData['guest_detail']['country_code']) || empty(trim($inputData['guest_detail']['country_code'])) || !preg_match('/^([A-Z]{2,3}|\d{3})$/i', trim($inputData['guest_detail']['country_code']))) {
            if (Validate::isLoadedObject($objCountry = new Country((int)Configuration::get('PS_COUNTRY_DEFAULT')))) {
                $inputData['guest_detail']['country_code'] = $objCountry->iso_code;
            } else {
                $inputData['guest_detail']['country_code'] = 'IN';
            }
        }

        if ($idCountry = Country::getByIso($inputData['guest_detail']['country_code'])) {
            if (Validate::isLoadedObject($objCountry = new Country($idCountry))) {
                if ($objCountry->contains_states) {
                    if (!isset($inputData['guest_detail']['state']) || empty(trim($inputData['guest_detail']['state'])) || !Validate::isCleanHtml(trim($inputData['guest_detail']['state']))) {
                        if (($statesByCountry = State::getStatesByIdCountry($idCountry)) && isset($statesByCountry[0])) {
                            $inputData['guest_detail']['state'] = $statesByCountry[0]['name'];
                        } else {
                            $inputData['guest_detail']['state'] = 'Demo State';
                        }
                    }

                    if (!isset($inputData['guest_detail']['state_code']) || empty(trim($inputData['guest_detail']['state_code'])) || !Validate::isCleanHtml(trim($inputData['guest_detail']['state_code']))) {
                        if ($idState = State::getIdByName($inputData['guest_detail']['state'])) {
                            $inputData['guest_detail']['state_code'] = $idState;
                        } else if (($statesByCountry = State::getStatesByIdCountry($idCountry)) && isset($statesByCountry[0])) {
                            $inputData['guest_detail']['state_code'] = $statesByCountry[0]['id_state'];
                        } else {
                            $inputData['guest_detail']['state_code'] = 1;
                        }
                    }
                }

                if($objCountry->need_zip_code) {
                    if(!isset($inputData['guest_detail']['zip']) || empty(trim($inputData['guest_detail']['zip'])) || !$objCountry->checkZipCode(trim($inputData['guest_detail']['zip']))){
                        $inputData['guest_detail']['zip'] = Tools::generateRandomZipcode($objCountry->id);
                    }
                } else {
                    $inputData['guest_detail']['zip'] = Tools::generateRandomZipcode($objCountry->id);
                }
            }
        }

        if(!isset($inputData['guest_detail']['address']) || empty(trim($inputData['guest_detail']['address'])) || !Validate::isAddress(trim($inputData['guest_detail']['address']))) {
            $addressParts = [];

            if (!empty($inputData['guest_detail']['city'])) {
                $addressParts[] = $inputData['guest_detail']['city'];
            }

            if (!empty($inputData['guest_detail']['state'])) {
                $addressParts[] = $inputData['guest_detail']['state'];
            }

            if (!empty($inputData['guest_detail']['zip'])) {
                $addressParts[] = $inputData['guest_detail']['zip'];
            }

            if (!empty($inputData['guest_detail']['country_code'])) {
                $addressParts[] = $inputData['guest_detail']['country_code'];
            }

            $inputData['guest_detail']['address'] = !empty($addressParts) ? implode(', ', $addressParts) : 'Demo Address';
        }

        // if invalid or empty address assign a demo address
        if(empty($inputData['guest_detail']['address']) || !Validate::isAddress($inputData['guest_detail']['address'])) {
            $inputData['guest_detail']['address'] = 'Demo Address';
        }
    }

    /**
     * Adding order payment from PUT requests.
     */
    public function addOrderPayment($params)
    {
        if (isset($params['price_details']['total_price_with_tax']) && $params['price_details']['total_price_with_tax'] ) {
            $objOrder = new Order($params['id_booking']);
            $amount = $params['price_details']['total_price_with_tax'] - $objOrder->getTotalPaid();
            $paymentMethod = null;
            if (isset($params['payment_details']['payment_method']) && $params['payment_details']['payment_method']) {
                $paymentMethod = $params['payment_details']['payment_method'];
            }

            $transactionId = null;
            if (isset($params['payment_details']['transaction_id']) && $params['payment_details']['transaction_id']) {
                $transactionId = $params['payment_details']['transaction_id'];
            }

            $paymentType = OrderPayment::PAYMENT_TYPE_REMOTE_PAYMENT;
            if (isset($params['payment_details']['payment_type']) && $params['payment_details']['payment_type']) {
                $paymentType = $params['payment_details']['payment_type'];
            }

            $idCurrency = $objOrder->id_currency;
            $paymentCurrency = null;
            if (isset($params['currency'])
                && ($selectedCurrency = Currency::getIdByIsoCode($params['currency']))
            ) {
                $objCurrency = new Currency($selectedCurrency);
                if ($objCurrency->active) {
                    $idCurrency = $selectedCurrency;
                }
            }

            $newCurrency = null;
            if ($idCurrency != $objOrder->id_currency) {
                $newCurrency = new Currency($idCurrency);
            }

            $orderInvoice = null;
            if ($invoice = $objOrder->hasInvoice()) {
                $orderInvoice = new OrderInvoice((int) $invoice);

                $totalPaidTaxIncl = $params['price_details']['total_price_with_tax'];
                $totalPaidTaxExcl = $params['price_details']['total_tax'];

                $orderInvoice->total_paid_tax_excl = $totalPaidTaxExcl;
                $orderInvoice->total_paid_tax_incl = $totalPaidTaxIncl;
                $orderInvoice->total_products = $totalPaidTaxExcl;
                $orderInvoice->total_products_wt = $totalPaidTaxIncl;
                $orderInvoice->save();
            }

            if ($amount > 0) {
                $objOrder->addOrderPayment(
                    $amount,
                    null,
                    $transactionId,
                    $newCurrency,
                    null,
                    $orderInvoice,
                    $paymentType
                );
            }
            $objOrder->total_paid_real = $params['price_details']['total_price_with_tax'];
            if (isset($params['id_channel_manager_booking']) && $params['id_channel_manager_booking']) {
                $objOrder->id_channel_manager_booking = $params['id_channel_manager_booking'];
            }
            $objOrder->save();
            $this->managePaymentHistory($objOrder->referece, $objOrder->total_paid_real);

        }
    }

    public function managePaymentHistory($orderReference, $totalPaidAmount)
    {
        if ($orderReference && $totalPaidAmount) {
           $getSql = 'SELECT `id_order_payment`, `amount`
           FROM `' . _DB_PREFIX_ . 'order_payment`
           WHERE `order_reference` = "' . pSQL($orderReference) . '"';


            if($results = Db::getInstance()->executeS($getSql)){
                foreach ($results as $key => $result) {
                    if(($idPaymentHistory = $result['id_order_payment']) && ((int)round($result['amount']) !== (int)round($totalPaidAmount))) {
                        if(Validate::isLoadedObject($objPaymentHistory = new OrderPayment((int) $idPaymentHistory))) {
                            $objPaymentHistory->delete();
                        }

                    }
                }
            }
        }
    }

    public function handleRoomTypePriceWithService(array &$inputData)
    {
        foreach ($inputData['room_bookings'] as &$roomBookings) {
            foreach ($roomBookings['rooms'] as &$room) {
                if(isset($room['services']) && !empty($room['services'])){
                    $totalServicePrice = 0;
                    $totalServiceTax = 0;
                    foreach ($room['services'] as $service) {
                        $totalServicePrice += $service['total_price_with_tax'];
                        if(isset($service['total_tax'])){
                            $totalServiceTax += $service['total_tax'];
                        }
                    }
                    $room['total_price_with_tax'] -= $totalServicePrice;
                    $room['total_tax'] -= $totalServiceTax;
                    $roomBookings['total_price_with_tax'] -= $totalServicePrice;
                    $roomBookings['total_tax'] -= $totalServiceTax;
                }
            }
        }
        return;
    }
}
