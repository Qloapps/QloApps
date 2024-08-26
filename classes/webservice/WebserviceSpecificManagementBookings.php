<?php
/**
* Since 2010 Webkul.
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
*  @copyright since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WebserviceSpecificManagementBookingsCore Extends ObjectModel implements WebserviceSpecificManagementInterface
{
    protected $objOutput;
    protected $output;
    protected $wsObject;

    public $id;
    public $id_product;
    public $id_order;
    public $id_order_detail;
    public $id_cart;
    public $id_room;
    public $id_hotel;
    public $id_customer;
    public $booking_type;
    public $comment;
    public $check_in;
    public $check_out;
    public $date_from;
    public $date_to;
    public $total_price_tax_excl;
    public $total_price_tax_incl;
    public $total_paid_amount;
    public $is_back_order;
    public $id_status;
    public $is_refunded;
    public $is_cancelled;
    public $hotel_name;
    public $room_type_name;
    public $city;
    public $state;
    public $country;
    public $zipcode;
    public $phone;
    public $email;
    public $check_in_time;
    public $check_out_time;
    public $room_num;
    public $adults;
    public $children;
    public $child_ages;

    public const BOOKING_API_BOOKING_STATUS_NEW = 1;
    public const BOOKING_API_BOOKING_STATUS_COMPLETED = 2;
    public const BOOKING_API_BOOKING_STATUS_CANCELLED = 3;
    public const BOOKING_API_BOOKING_STATUS_REFUNDED = 4;

    public const BOOKING_API_PAYMENT_STATUS_COMPLETED = 1;
    public const BOOKING_API_PAYMENT_STATUS_PARTIAL = 2;
    public const BOOKING_API_PAYMENT_STATUS_AWATING = 3;

    public static $definition = array(
        'table' => 'htl_booking_detail',
        'primary' => 'booking_id',
        'fields' => array()
    );

    public $webserviceParameters = array(
        'objectsNodeName' => 'bookings',
        'objectNodeName' => 'booking',
        'fields' => array(
            'id_property' => array('type' => self::TYPE_INT, 'required' => true),
            'currency' => array('type' => self::TYPE_STRING),
            'booking_status' => array('type' => self::TYPE_INT, 'required' => true),
            'payment_status' => array('type' => self::TYPE_INT, 'required' => true),
            'source' => array('type' => self::TYPE_STRING),
            'booking_date' => array('type' => self::TYPE_DATE),
            'remark' => array('type' => self::TYPE_STRING),
            'id_language' => array('type' => self::TYPE_INT),
        ),
        'associations' => array(
            'customer_detail' => array(
                'single_entity' => true,
                'id_customer' => array('type' => self::TYPE_INT),
                'firstname' => array('type' => self::TYPE_STRING, 'required' => true),
                'lastname' => array('type' => self::TYPE_STRING, 'required' => true),
                'email' => array('type' => self::TYPE_STRING, 'required' => true),
                'phone' => array('type' => self::TYPE_STRING, 'required' => true),
                'address' => array('type' => self::TYPE_STRING),
                'city' => array('type' => self::TYPE_STRING),
                'zip' => array('type' => self::TYPE_STRING),
                'state_code' => array('type' => self::TYPE_STRING),
                'country_code' => array('type' => self::TYPE_STRING),
            ),
            'price_details' => array(
                'single_entity' => true,
                'total_paid' => array('type' => self::TYPE_FLOAT, 'required' => true),
                'total_price_with_tax' => array('type' => self::TYPE_FLOAT),
                'total_tax' => array('type' => self::TYPE_FLOAT),
            ),
            'payment_detail' => array(
                'single_entity' => true,
                'payment_type' => array('type' => self::TYPE_STRING),
                'payment_method' => array('type' => self::TYPE_STRING),
                'transaction_id' => array('type' => self::TYPE_STRING),
            ),
            'cart_rules' => array(
                'setter' => false,
                'resource' => 'cart_rule',
                'fields' => array(
                    'name' => array('type' => self::TYPE_STRING),
                    'code' => array('type' => self::TYPE_STRING),
                    'type' => array('type' => self::TYPE_INT),
                    'value' => array('type' => self::TYPE_INT),
                    'currency' => array('type' => self::TYPE_STRING),
                )
            ),
            'room_types' => array(
                'setter' => false,
                'resource' => 'room_type',
                'fields' => array(
                    'id_room_type' => array('type' => self::TYPE_INT, 'required' => true),
                    'checkin_date' => array('type' => self::TYPE_DATE, 'required' => true),
                    'checkout_date' => array('type' => self::TYPE_DATE, 'required' => true),
                    'total_price_with_tax' => array('type' => self::TYPE_FLOAT),
                    'number_of_rooms' => array('type' => self::TYPE_INT, 'required' => true),
                    'rooms' => array(
                        'resource' => 'room',
                        'fields' => array(
                            'id_room' => array('type' => self::TYPE_INT),
                            'adults' => array('type' => self::TYPE_INT),
                            'child' => array('type' => self::TYPE_INT),
                            'child_ages' => array(
                                'child_age' => array('type' => self::TYPE_INT)
                            ),
                            'total_price_with_tax' => array('type' => self::TYPE_INT),
                            'services' => array(
                                'resource' => 'service',
                                'fields' => array(
                                    'id_service' => array('type' => self::TYPE_INT),
                                    'quantity' => array('type' => self::TYPE_INT),
                                    'total_price_without_tax' => array('type' => self::TYPE_INT),
                                )
                            ),
                            'facilities' => array(
                                'resource' => 'facility',
                                'fields' => array(
                                    'id_facility' => array('type' => self::TYPE_INT),
                                    'id_option' => array('type' => self::TYPE_INT),
                                    'name' => array('type' => self::TYPE_STRING),
                                    'total_price_without_tax' => array('type' => self::TYPE_INT),
                                )
                            )
                        )
                    )
                ),
            )
        )
    );

    /**
     * @param WebserviceOutputBuilderCore $obj
     * @return WebserviceSpecificManagementInterface
     */
    public function setObjectOutput(WebserviceOutputBuilderCore $obj)
    {
        $this->objOutput = $obj;
        return $this;
    }

    public function setWsObject(WebserviceRequestCore $obj)
    {
        $this->wsObject = $obj;
        return $this;
    }

    public function setUrlSegment($segments)
    {
        $this->urlSegment = $segments;
        return $this;
    }

    public function getWsObject()
    {
        return $this->wsObject;
    }
    public function getObjectOutput()
    {
        return $this->objOutput;
    }

    public function getUrlSegment()
    {
        return $this->urlSegment;
    }

    public function getResponseJson()
    {
        $this->output = json_encode($this->output);
    }

    public function getResponseXml()
    {
        if (is_array($this->output)) {
            $parentKeys = array(
                'room_types' => 'room_type',
                'rooms' => 'room',
                'facilities' => 'facility',
                'services' => 'service',
                'cart_rules' => 'cart_rule',
                'remarks' => 'remark'
            );
            $this->output = $this->renderOutputUsingArray($this->output, array(), $parentKeys);
        }

        $this->output = $this->objOutput->getObjectRender()->overrideContent($this->output);
    }

    public function getContent()
    {
        return $this->output;
    }

    public function manage()
    {
        $this->context = Context::getContext();
        switch ($this->wsObject->method) {
            case 'GET':
                if (isset($this->wsObject->urlFragments['schema'])) {
                    $object = new WebserviceSpecificManagementBookings();
                    $typeOfView = WebserviceOutputBuilder::VIEW_DETAILS;
                    $this->wsObject->objects = [];
                    $this->wsObject->objects[] = $object;
                    $this->wsObject->objects['empty'] = $object;
                    $this->wsObject->schemaToDisplay = $this->wsObject->urlFragments['schema'];

                    $this->output .= $this->objOutput->getContent(
                        $this->wsObject->objects,
                        $this->wsObject->schemaToDisplay,
                        $this->wsObject->fieldsToDisplay,
                        $this->wsObject->depth,
                        $typeOfView,
                        false
                    );
                    $this->renderResponse();
                } else {
                    if (isset($this->wsObject->urlSegment[1]) && $this->wsObject->urlSegment[1]) {
                        $this->getBooking($this->wsObject->urlSegment[1]);
                        $this->renderResponse();
                    } else {
                        $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('bookings', array());
                        $bookings = Db::getInstance()->executeS('SELECT `id_order` FROM `'._DB_PREFIX_.'orders` WHERE 1');
                        foreach ($bookings as $booking) {
                            $more_attr = array(
                                'xlink_resource' => $this->wsObject->wsUrl.$this->wsObject->urlSegment[0].'/'.$booking['id_order'],
                                'id' => (int) $booking['id_order']
                            );
                            $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('booking', array('objectsNodeName' => 'bookings'), $more_attr, false);
                        }
                        $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('bookings', array());
                        $this->output = $this->objOutput->getObjectRender()->overrideContent($this->output);
                    }
                }
            break;
            case 'POST':
                $inputData = $this->getRequestParams('booking');
                $this->formatRequest($inputData);
                if ($this->validatePostRequest($inputData)) {
                    $this->handlePostRequest($inputData);
                    $this->deleteFeaturePrices();
                    $this->deleteApiCreatedVoucher();
                    $this->renderResponse();
                } else {
                    throw new WebserviceException(
                        $this->error_msg,
                        array(404, 400)
                    );
                }
            break;
            case 'PUT':
                // handle update here.
                $inputData = $this->getRequestParams('booking');
                // since post and put requires different data, validation is also different
                $this->formatRequest($inputData);
                if ($this->validatePutRequestParams($inputData)) {
                    $this->handlePutRequest($inputData);
                    $this->deleteFeaturePrices();
                    $this->deleteApiCreatedVoucher();
                    $this->renderResponse();
                } else {
                    throw new WebserviceException(
                        $this->error_msg,
                        array(404, 400)
                    );
                }

                break;
        }

        return $this->output;
    }

    public function formatRequest(&$params)
    {
        $associations = array();
        if (isset($params['associations'])) {
            $associations = $params['associations'];
            unset($params['associations']);
        }

        $params = array_merge($params, $associations);
        if (isset($params['room_types'])) {
            $params['room_types'] = $this->formatRoomTypesFromRequest($params);
        }

        if (isset($params['cart_rules'])) {
            $params['cart_rules'] = $this->formatCartRules($params);
        }
    }

    public function formatCartRules($params)
    {
        $formattedCartRules = array();
        if (isset($params['cart_rules']['cart_rule'][0])) {
            $formattedCartRules = $params['cart_rules']['cart_rule'];
        } else if (isset($params['cart_rules'])
            && !isset($params['cart_rules'][0])
            && isset($params['cart_rules']['cart_rule'])
        ) {
            $formattedCartRules[] = $params['cart_rules']['cart_rule'];
        } else if (isset($params['cart_rules'][0])) {
            $formattedCartRules = $params['cart_rules'];
        }

        return $formattedCartRules;
    }

    public function formatRoomTypesFromRequest($params)
    {
        $roomTypes = array();
        if (isset($params['room_types']['room_type'][0])) {
            $roomTypes = $params['room_types']['room_type'];
        } else if (isset($params['room_types'])
            && !isset($params['room_types'][0])
            && isset($params['room_types']['room_type'])
        ) {
            $roomTypes[] = $params['room_types']['room_type'];
        } else if (isset($params['room_types']) && isset($params['room_types'][0])) {
            $roomTypes = $params['room_types'];
        }

        if (count($roomTypes)) {
            $formattedRoomTypes = array();
            foreach ($roomTypes as $roomTypeKey => $roomType) {
                $dateProductJoinKey = $roomType['id_room_type'].'_'.strtotime($roomType['checkin_date']).strtotime($roomType['checkout_date']);
                if (!isset($formattedRoomTypes[$dateProductJoinKey])) {
                    $formattedRoomTypes[$dateProductJoinKey] = $roomType;
                } else {
                    $formattedRoomTypes[$dateProductJoinKey]['number_of_rooms'] += $roomType['number_of_rooms'];
                }

                if (isset($roomType['rooms'])) {
                    $formattedRoomTypes[$dateProductJoinKey]['rooms'] = $this->formatRoomFromRequest($roomType);
                }
            }

            $roomTypes = $formattedRoomTypes;
        }

        return $roomTypes;
    }

    public function formatRoomFromRequest($requestedRooms)
    {
        $rooms = array();
        if (isset($requestedRooms['rooms']['room'][0])) {
            $rooms = $requestedRooms['rooms']['room'];
        } else if (isset($requestedRooms['rooms'])
            && !isset($requestedRooms['rooms'][0])
            && isset($requestedRooms['rooms']['room'])
        ) {
            $rooms[] = $requestedRooms['rooms']['room'];
        } else if (isset($requestedRooms['rooms']) && isset($requestedRooms['rooms'][0])) {
            $rooms = $requestedRooms['rooms'];
        }

        if (count($rooms)) {
            $formattedRooms = array();
            foreach ($rooms as $roomKey => $room) {
                $selectedDemands = $this->formatDemandsFromRequest($room);
                $selectedServices = $this->formatServicesFromRequest($room);
                $occupancy = $this->formatOccupancyFromRequest($room, $requestedRooms['id_room_type']);
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
            }

            $rooms = $formattedRooms;
        }

        return $rooms;
    }

    public function formatOccupancyFromRequest($room, $idRoomType)
    {
        $child_ages = array();
        if (isset($room['child_ages']['child_age'][0])
            && is_array($room['child_ages']['child_age'])
        ) {
            $child_ages = $room['child_ages']['child_age'];
        } else if (isset($room['child_ages'])
            && !isset($room['child_ages'][0])
            && isset($room['child_ages']['child_age'])
            && $room['child_ages']['child_age']
        ) {
            $child_ages = $room['child_ages']['child_age'];
        } else if (isset($room['child_ages']) && isset($room['child_ages'][0])) {
            $child_ages = $room['child_ages'];
        }

        $objRoomType = new HotelRoomType();
        // using to set base occupancy for the room if no occupancy is given, and we are not validating occupancy since Wsorder has booking without occupancy
        $roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($idRoomType);

        return array(
            array(
                'adults' => isset($room['adult']) && $room['adult'] ? $room['adult'] : $roomTypeInfo['adults'],
                'children' =>  isset($room['child']) && $room['child'] ? $room['child'] : $roomTypeInfo['children'],
                'child_ages' => $child_ages
            )
        );
    }

    public function formatServicesFromRequest($room)
    {
        $selectedServices = array();
        if (isset($room['services']['service'][0])) {
            $selectedServices = $room['services']['service'];
        } else if (isset($room['services'])
            && !isset($room['services'][0])
            && isset($room['services']['service'])
        ) {
            $selectedServices[] = $room['services']['service'];
        } else if (isset($room['services']) && isset($room['services'][0])) {
            $selectedServices = $room['services'];
        }

        $formattedServices = array();
        foreach ($selectedServices as $service) {
            $key = isset($service['id_service']) ? $service['id_service'] : 0;
            $formattedServices[$key]['id_product'] = $key;
            $formattedServices[$key]['quantity'] = isset($service['quantity']) ? $service['quantity'] : 1;
            if (isset($service['total_price_with_tax'])) {
                $formattedServices[$key]['total_price_with_tax'] = $service['total_price_with_tax'];
            }
        }

        return $formattedServices;
    }

    public function formatDemandsFromRequest($room)
    {
        $selectedDemands = array();
        if (isset($room['facilities']['facility'][0])) {
            $selectedDemands = $room['facilities']['facility'];
        } else if (isset($room['facilities'])
            && !isset($room['facilities'][0])
            && isset($room['facilities']['facility'])
        ) {
            $selectedDemands[] = $room['facilities']['facility'];
        } else if (isset($room['facilities']) && isset($room['facilities'][0])) {
            $selectedDemands = $room['facilities'];
        }

        $formattedDemands = array();
        foreach ($selectedDemands as $key => $demand) {
            $formattedDemands[$key]['id_global_demand'] = isset($demand['id_facility'])? $demand['id_facility'] : 0;
            $formattedDemands[$key]['id_option'] = isset($demand['id_option'])? $demand['id_option'] : 0;
            if (isset($demand['total_price_with_tax'])) {
                $formattedDemands[$key]['total_price_with_tax'] = $demand['total_price_with_tax'];
            }
        }

        return $formattedDemands;
    }

    public function validatePostRequest($params)
    {
        $this->error_msg = '';
        if (!isset($params['currency'])
            || !$params['currency']
            || !Currency::getIdByIsoCode($params['currency'])
            || (!Validate::isLoadedObject((new Currency(Currency::getIdByIsoCode($params['currency'])))))
        ) {
            $this->error_msg = Tools::displayError('Please provide valid currency for the booking');
        } elseif (!isset($params['customer_detail'])
            || !$params['customer_detail']
        ) {
            $this->error_msg = Tools::displayError('Customer details not found.');
        } else if (isset($params['customer_detail']['id_customer'])
            && $params['customer_detail']['id_customer']
            && !Validate::isLoadedObject(new Customer((int) $params['customer_detail']['id_customer']))
        ) {
            $this->error_msg = Tools::displayError('Please provide a valid id_customer for the booking');
        } else if (!isset($params['customer_detail']['firstname'])
            || empty(trim($params['customer_detail']['firstname']))
        ) {
            $this->error_msg = Tools::displayError('Please provide a first name for the booking');
        } else if (!isset($params['customer_detail']['lastname'])
            || empty(trim($params['customer_detail']['lastname']))
        ) {
            $this->error_msg = Tools::displayError('Please provide a last name for the booking');
        } else if (!isset($params['customer_detail']['email'])
            || empty(trim($params['customer_detail']['email']))
        ) {
            $this->error_msg = Tools::displayError('Please provide an email for the booking');
        } else if (Configuration::get('PS_ONE_PHONE_AT_LEAST')
            && (!isset($params['customer_detail']['phone']) || empty(trim($params['customer_detail']['phone'])))
        ) {
            $this->error_msg = Tools::displayError('Please provide a phone number for the booking');
        } else if (!$this->validateAddressFields($params['customer_detail'])
            && $this->error_msg == ''
        ) {
            $this->error_msg = Tools::displayError('Invalid address provided');
        } else if (isset($params['id'])) {
            $this->error_msg = Tools::displayError('id is forbidden when adding a new resource');
        } else if (!$this->validateRequestedRoomTypes($params['room_types'])
            && $this->error_msg == ''
        ) {
            $this->error_msg = Tools::displayError('Requested room(s) not available');
        } else if (isset($params['payment_detail']['payment_type'])
            && $params['payment_detail']['payment_type'] != 'online'
            && $params['payment_detail']['payment_type'] != 'remote'
            && $params['payment_detail']['payment_type'] != 'pay at hotel'
        ) {
            $this->error_msg = Tools::displayError('Invalid payment type');
        } else {
            $this->validatePostCartRules($params);
        }

        if (!$this->error_msg && $this->error_msg == '') {
            return true;
        }

        return false;
    }

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
            } else if (($objCountry = new Country($idCountry)) && $objCountry->contains_states
                && (!isset($params['state_code']) || !$params['state_code'])
            ) {
                $this->error_msg = Tools::displayError('State code is required for the given country.');
            } elseif ($objCountry->contains_states
                && (!$idState = State::getIdByIso($params['state_code'], $objCountry->id))
            ) {
                $this->error_msg = Tools::displayError('Invalid state code.');
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

    public function validateRequestedRoomTypes($roomTypes = array())
    {
        $objBookingDetail = new HotelBookingDetail();
        $objRoomType = new HotelRoomType();
        $idHotel = false;
        if (!$roomTypes) {
            return false;
        } else {
            foreach ($roomTypes as $roomType) {
                if ($this->validateRoomType($roomType)) {
                    if (!$idHotel) {
                        $roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($roomType['id_room_type']);
                        $idHotel = $roomTypeInfo['id_hotel'];
                    }

                    $dateFrom = date('Y-m-d', strtotime($roomType['checkin_date']));
                    $dateTo = date('Y-m-d', strtotime($roomType['checkout_date']));
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

                                if (isset($room['services']) && $room['services']
                                    && !$this->validateRequestedServices($room['services'], $roomType['id_room_type'])
                                ) {
                                    return false;
                                }

                                if (isset($room['facilities']) && $room['facilities']
                                    && !$this->validateRequestedDemands($room['facilities'], $roomType['id_room_type'])
                                ) {
                                    return false;
                                }
                            }
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    public function handlePostRequest($params)
    {
        $objHotelRoomType = new HotelRoomType();
        $this->context->cart = new Cart();
        $this->processGuestDetails($params['customer_detail']);
        $this->processLanguage($params);
        $this->processCurrency($params);
        //saving the cart after adding the guest, language and the currency in the cart
        $this->context->cart->save();
        $this->addRoomsToCart($params['room_types']);
        $this->processCustomer($params['customer_detail']);
        // validating these here since these cart rule checkValidity() only works if there are products in the cart.
        if (($error = $this->applyCartRules($params)) && $error != '') {
            throw new WebserviceException(
                $error,
                array(404, 400)
            );

            return false;
        }

        $totalAmount = isset($params['price_details']['total_paid']) ? $params['price_details']['total_paid'] : 0;
        $objPaymentModule = new WebserviceOrder();
        $paymentStatus = false;
        if (isset($params['payment_status'])) {
            $paymentStatus = $params['payment_status'];
        }

        switch ($paymentStatus) {
            case self::BOOKING_API_PAYMENT_STATUS_COMPLETED:
                $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
            break;
            case self::BOOKING_API_PAYMENT_STATUS_PARTIAL:
                $orderStatus = Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED');
            break;
            case self::BOOKING_API_PAYMENT_STATUS_AWATING:
                $orderStatus = Configuration::get('PS_OS_AWAITING_PAYMENT');
            break;
            default:
                $cartTotal = $this->context->cart->getOrderTotal(true, Cart::BOTH);
                if ($totalAmount > 0 && $totalAmount < $cartTotal) {
                    $orderStatus = Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED');
                } else if ($totalAmount >= $cartTotal) {
                    $orderStatus = $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
                }  else {
                    $orderStatus = Configuration::get('PS_OS_AWAITING_PAYMENT');
                }
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
            } else if ($params['payment_detail']['payment_type'] == 'pay at hotel') {
                $objPaymentModule->payment_type = OrderPayment::PAYMENT_TYPE_PAY_AT_HOTEL;
            }
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
            $this->bookingCustomer->secure_key
        )) {
            $this->updateServicesAndDemands($objPaymentModule->currentOrder);
            $objOrder = new Order($objPaymentModule->currentOrder);
            if (isset($params['booking_date'])
                && $params['booking_date']
            ) {
                $objOrder->date_add = date('Y-m-d H:i:s', strtotime($params['booking_date']));
            }

            // update the price after the services has been updated.
            if (isset($params['price_details']['total_price_with_tax']) && $params['price_details']['total_price_with_tax']) {
                $objOrder->total_paid_tax_incl = $params['price_details']['total_price_with_tax'];
            }

            $objOrder->save();
            $this->getBooking($objPaymentModule->currentOrder);

            return true;
        }

        $this->wsObject->setError(400, Tools::displayError('Unable to create booking.'), 200);

        return false;
    }

    public function updateServicesAndDemands($idOrder)
    {
        $objOrder = new Order($idOrder);
        $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
        if (isset($this->roomWiseInfomation) && $this->roomWiseInfomation) {
            if ($orderedServices = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts($objOrder->id)) {
                foreach ($orderedServices as $orderedService) {
                    $objHotelBookingDetail = new HotelBookingDetail($orderedService['id_htl_booking_detail']);
                    $dateJoinKey = strtotime($objHotelBookingDetail->date_from).''.strtotime($objHotelBookingDetail->date_to).$orderedService['id_room'];
                    if (isset($this->roomWiseInfomation[$dateJoinKey]['services'])
                        && $this->roomWiseInfomation[$dateJoinKey]['services']
                        && isset($orderedService['additional_services'])
                        && $orderedService['additional_services']
                    ) {
                        foreach ($orderedService['additional_services'] as $service) {
                            $objOrderDetail = new OrderDetail($service['id_order_detail']);
                            $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail($service['id_room_type_service_product_order_detail']);
                            if (isset($this->roomWiseInfomation[$dateJoinKey]['services'][$service['id_product']])
                                && isset($this->roomWiseInfomation[$dateJoinKey]['services'][$service['id_product']]['total_price_with_tax'])
                            ) {
                                $oldPriceTaxExcl = $objRoomTypeServiceProductOrderDetail->total_price_tax_excl;
                                $oldPriceTaxIncl = $objRoomTypeServiceProductOrderDetail->total_price_tax_incl;
                                if ($oldPriceTaxExcl > 0) {
                                    $oldTaxMultiplier = $oldPriceTaxIncl / $oldPriceTaxExcl;
                                } else {
                                    $oldTaxMultiplier = 1;
                                }

                                $quantity = $objRoomTypeServiceProductOrderDetail->quantity;
                                if ($objOrderDetail->product_price_calculation_method == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                                    $quantity = $quantity * HotelHelper::getNumberOfDays(
                                        $objHotelBookingDetail->date_from,
                                        $objHotelBookingDetail->date_to
                                    );
                                }

                                $totalPriceTaxExcl = 0;
                                $totalPriceTaxIncl = 0;
                                if ((int) $this->roomWiseInfomation[$dateJoinKey]['services'][$service['id_product']]['total_price_with_tax']) {
                                    $totalPriceTaxExcl = $this->roomWiseInfomation[$dateJoinKey]['services'][$service['id_product']]['total_price_with_tax']/$oldTaxMultiplier;
                                    $totalPriceTaxIncl = $this->roomWiseInfomation[$dateJoinKey]['services'][$service['id_product']]['total_price_with_tax'];
                                }

                                $unitPriceTaxExcl = 0;
                                $unitPriceTaxIncl = 0;
                                if ($totalPriceTaxExcl > 0) {
                                    $unitPriceTaxExcl = $totalPriceTaxExcl/$quantity;
                                    $unitPriceTaxIncl = $totalPriceTaxIncl/$quantity;
                                }

                                $objRoomTypeServiceProductOrderDetail->unit_price_tax_excl = Tools::ps_round($unitPriceTaxExcl, 6);
                                $objRoomTypeServiceProductOrderDetail->unit_price_tax_incl = Tools::ps_round($unitPriceTaxIncl, 6);
                                $objRoomTypeServiceProductOrderDetail->total_price_tax_excl = Tools::ps_round($totalPriceTaxExcl, 6);
                                $objRoomTypeServiceProductOrderDetail->total_price_tax_incl = Tools::ps_round($totalPriceTaxIncl, 6);
                                $objRoomTypeServiceProductOrderDetail->save();

                                $priceDiffTaxExcl = $objRoomTypeServiceProductOrderDetail->total_price_tax_excl - $oldPriceTaxExcl;
                                $priceDiffTaxIncl = $objRoomTypeServiceProductOrderDetail->total_price_tax_incl - $oldPriceTaxIncl;
                                $objOrderDetail->total_price_tax_excl += $priceDiffTaxExcl;
                                $objOrderDetail->total_price_tax_incl += $priceDiffTaxIncl;
                                $objOrderDetail->unit_price_tax_excl = Tools::ps_round(($objOrderDetail->total_price_tax_excl / $objOrderDetail->product_quantity), 6);
                                $objOrderDetail->unit_price_tax_incl = Tools::ps_round(($objOrderDetail->total_price_tax_incl / $objOrderDetail->product_quantity), 6);
                                $objOrderDetail->save();

                                $objOrder->total_paid_tax_excl += $priceDiffTaxExcl;
                                $objOrder->total_paid_tax_incl += $priceDiffTaxIncl;
                                $objOrder->total_paid += $priceDiffTaxIncl;
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
                    $dateJoinKey = strtotime($orderedRoom['date_from']).''.strtotime($orderedRoom['date_to']).$orderedRoom['id_room'];
                    if (isset($this->roomWiseInfomation[$dateJoinKey]['demands']) && $this->roomWiseInfomation[$dateJoinKey]['demands']) {
                        if ($demands = json_decode($this->roomWiseInfomation[$dateJoinKey]['demands'], true)) {
                            $this->addDemandsInRoom($demands, $orderedRoom['id']);
                        }
                    }
                }
            }
        }
    }

    public function processGuestDetails($params)
    {
        if (isset($params['id_customer'])
            && Validate::isLoadedObject($objCustomer = new Customer((int) $params['id_customer']))
        ) {
            $this->bookingCustomer = $objCustomer;
        } else {
            $objCustomer = new Customer();
            $this->bookingCustomer = $objCustomer->getByEmail($params['email']);
        }

        if (isset($this->bookingCustomer->id)
            && $this->bookingCustomer->id
        ) {
            $idGuest = Guest::getFromCustomer($this->bookingCustomer->id);
        } else {
            $idGuest = $this->createGuestForBooking();
        }

        $this->context->cart->id_guest = $idGuest;
    }

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

    public function processCustomer($params)
    {
        $this->context->cookie->id_guest = $this->context->cart->id_guest;
        if (!isset($this->bookingCustomer->id)) {
            $objCustomer = new Customer();
            $objCustomer->firstname = $params['firstname'];
            $objCustomer->lastname = $params['lastname'];
            $objCustomer->email = $params['email'];
            $objCustomer->passwd = md5(time()._COOKIE_KEY_);
            $objCustomer->cleanGroups();
            $objCustomer->add();
            $this->bookingCustomer = $objCustomer;
        } else {
            // update name
            if (isset($params['firstname']) && Validate::isName($params['firstname'])) {
                $this->bookingCustomer->firstname = $params['firstname'];
            }

            if (isset($params['lastname']) && Validate::isName($params['lastname'])) {
                $this->bookingCustomer->lastname = $params['lastname'];
            }

            if (isset($params['email']) && Validate::isEmail($params['email'])) {
                $this->bookingCustomer->email = $params['email'];
            } else {
                $params['email'] = $this->bookingCustomer->email;
            }

            $this->bookingCustomer->save();
        }

        if (isset($params['country_code'])
            && $params['country_code']
        ) {
            $params['id_country'] = Country::getByIso($params['country_code']);
            $objCountry = new Country($params['id_country']);
            if ($objCountry->contains_states) {
                $params['id_state'] = State::getIdByIso($params['state_code']);
            }

            $active = true;
            $cache_id = 'Address::getFirstCustomerAddressId_'.(int) $this->bookingCustomer->id.'-'.(bool)$active;
            Cache::clean($cache_id);
            if ($idAddress = Address::getFirstCustomerAddressId($this->bookingCustomer->id)) {
                $objAddress = new Address((int) $idAddress);
            } else {
                $objAddress = new Address();
                $objAddress->alias = 'Generated by bookings API';
            }

            $objAddress->id_customer = $this->bookingCustomer->id;
            $objAddress->firstname = $params['firstname'];
            $objAddress->lastname = $params['lastname'];
            if (isset($params['phone'])) {
                $objAddress->phone = $params['phone'];
            }

            $objAddress->auto_generated = true;
            $objAddress->address1 = $params['address'];
            $objAddress->city = $params['city'];
            $objAddress->postcode = isset($params['zip']) ? $params['zip'] : '';
            $objAddress->id_country = $params['id_country'];
            $objAddress->id_state = isset($params['id_state']) ? $params['id_state'] : 0;

            $objAddress->save();
        }

        if (isset($params['phone']) && Validate::isPhoneNumber($params['phone'])) {
            CartCustomerGuestDetail::updateCustomerPhoneNumber($params['email'], $params['phone']);
        }

        // to remove the older non ordered cart for this customer.
        $this->context->cookie->id_cart = $this->context->cart->id;
        $this->context->updateCustomer($this->bookingCustomer, 1);
    }

    public function renderResponse()
    {
        if (get_class($this->objOutput->getObjectRender()) == 'WebserviceOutputJSON') {
            $this->getResponseJson();
        } else {
            $this->getResponseXml();
        }
    }

    public function renderHeader($header)
    {
        return '<'.$header.'>';
    }

    public function renderFooter($footer)
    {
        return '</'.$footer.'>';
    }

    public function renderOutputUsingArray($response, $keyToIgnore = array(), $parentKeys = array(), $parentKey = '', $useEmpty = false)
    {
        $output = '';
        foreach ($response as $key => $res) {
            if (in_array($key, $keyToIgnore) && $key) {
                continue;
            }

            $currentKey = $key;

            if (gettype($key) == 'integer' && isset($parentKeys[$parentKey])) {
                $key = $parentKeys[$parentKey];
            }

            if (is_array($res) && count($res)) {
                $output .= $this->renderHeader($key);
                $output .= $this->renderOutputUsingArray($res, $keyToIgnore, $parentKeys, $key, $useEmpty);
                $output .= $this->renderFooter($key);
            } else {
                if (empty($res) && !$useEmpty) {
                    $res = 0;
                }

                if (isset($this->wsObject->urlFragments['schema']) && $this->wsObject->method == 'GET') {
                    if ($this->wsObject->urlFragments['schema'] == 'blank' || $this->wsObject->urlFragments['schema'] == 'synopsis') {
                        $res = null;
                    } else {
                        throw new WebserviceException(
                            'Please select a schema of type \'synopsis\' to get the whole schema informations (which fields are required, which kind of content...) or \'blank\' to get an empty schema to fill before using POST request.',
                            array(100, 400)
                        );
                    }
                }

                $output .= $this->objOutput->objectRender->renderField(
                    array(
                        'sqlId' => $key,
                        'value' => $res
                    )
                );
            }
        }

        return $output;
    }

    public function applyCartRules($params)
    {
        $error = '';
        if ($requestedCartRules = $this->formatCartRules($params)) {
            if ($cartRules = $this->sortCreateCartRules($requestedCartRules)) {
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

    public function sortCreateCartRules($requestedCartRules)
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
                    if ($cartRule['type'] == 'percentage') {
                        $objCartRule->reduction_percent = $cartRule['value'];
                    } else if ($cartRule['type'] == 'amount') {
                        $objCartRule->reduction_amount = $cartRule['value'];
                    }

                    if ($objCartRule->add()) {
                        $cartRules[] = $objCartRule->id;
                        $this->apiCreatedVoucher[] = $objCartRule->id;
                    }
                }
            }
        }

        return $cartRules;
    }

    public function validatePostCartRules($params)
    {
        if (isset($params['cart_rules']) && $params['cart_rules']) {
            $cartRulesCount = array();
            foreach ($params['cart_rules'] as $cartRule) {
                if (!($code = trim($cartRule['code']))
                    || !Validate::isCleanHtml($code)
                ) {
                    $this->error_msg = Tools::displayError('Invalid cart rule!!');
                    break;
                } else if (!Validate::isLoadedObject($objCartRule = new CartRule(CartRule::getIdByCode($code)))
                    && (!isset($cartRule['type']) || $cartRule['type'] != 'percentage' || $cartRule['type'] != 'amount')
                    && (!isset($cartRule['value']) || !$cartRule['value'] || ($cartRule['type'] == 'percentage' && $cartRule['value'] > 100))
                ) {
                    $this->error_msg = Tools::displayError('Invalid cart rule parameter!!');
                    break;
                }
            }
        }
    }

    public function validateRequestedServices($services, $idRoomType)
    {
        if ($services) {
            $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
            foreach ($services as $key => $serviceProduct) {
                if (!isset($serviceProduct['id_product'])
                    || !$serviceProduct['id_product']
                ) {
                    $this->error_msg = Tools::displayError('Invalid request for service product');
                    return false;
                } else if (Validate::isLoadedObject($objServiceProduct = new Product((int) $serviceProduct['id_product']))) {
                    if (!$objRoomTypeServiceProduct->isRoomTypeLinkedWithProduct($idRoomType, $serviceProduct['id_product'])) {
                        $this->error_msg = Tools::displayError('Service is not linked with the requested room type.');
                        return false;
                    }
                } else {
                    $this->error_msg = Tools::displayError('Service product not found.');
                    return false;
                }
            }
        }

        return true;
    }

    public function validateRequestedDemands($requestedDemands, $idRoomType)
    {
        $objHotelRoomTypeDemand = new HotelRoomTypeDemand();
        $objHotelDemandOptions = new HotelRoomTypeGlobalDemandAdvanceOption();
        // Incase the there is no demand for this room but demands are still sent in the request for this room type.
        if ($roomTypeDemands = $objHotelRoomTypeDemand->getRoomTypeDemands($idRoomType)) {
            foreach ($requestedDemands as $demandKey => $requestedDemand) {
                if (!isset($requestedDemand['id_global_demand'])
                    || !isset($roomTypeDemands[$requestedDemand['id_global_demand']])
                ) {
                    $this->error_msg = Tools::displayError('Invalid request for facilities.');
                    return false;
                } else if (isset($roomTypeDemands[$requestedDemand['id_global_demand']]['adv_option'])
                    && (!isset($requestedDemand['id_option']) || !isset($roomTypeDemands[$requestedDemand['id_global_demand']]['adv_option'][$requestedDemand['id_option']]))
                ) {
                    $this->error_msg = Tools::displayError('Invalid id option.');
                    return false;
                }
            }
        } else {
            $this->error_msg = Tools::displayError('Invalid request for facilities.');
            return false;
        }

        return true;
    }

    public function addRoomsToCart($roomTypes)
    {
        $this->roomWiseInfomation = array();
        $objRoomType = new HotelRoomType();
        $objHotelCartBookingData = new HotelCartBookingData();
        if (defined('_PS_ADMIN_DIR_')) {
            $PS_ROOM_UNIT_SELECTION_TYPE = Configuration::get('PS_BACKOFFICE_ROOM_BOOKING_TYPE');
        } else {
            $PS_ROOM_UNIT_SELECTION_TYPE = Configuration::get('PS_FRONT_ROOM_UNIT_SELECTION_TYPE');
        }

        $quantityType = false;
        if ($PS_ROOM_UNIT_SELECTION_TYPE != HotelBookingDetail::PS_ROOM_UNIT_SELECTION_TYPE_OCCUPANCY) {
            $quantityType = 'integer';
        }

        foreach ($roomTypes as $roomType) {
            $dateFrom = date('Y-m-d', strtotime($roomType['checkin_date']));
            $dateTo = date('Y-m-d', strtotime($roomType['checkout_date']));
            $roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($roomType['id_room_type']);
            $idHotel = $roomTypeInfo['id_hotel'];
            $occupancy = array(
                array(
                    'adults' => $roomTypeInfo['adults'],
                    'children' => $roomTypeInfo['children'],
                    'child_ages' => array()
                )
            );
            $productPriceTI = Product::getPriceStatic((int) $roomType['id_room_type'], true);
            $productPriceTE = Product::getPriceStatic((int) $roomType['id_room_type'], false);
            if ($productPriceTE) {
                $taxRate = (($productPriceTI-$productPriceTE)/$productPriceTE)*100;
            } else {
                $taxRate = 0;
            }

            $taxRateM = $taxRate/100;
            if (isset($roomType['rooms']) && count($roomType['rooms'])) {
                foreach ($roomType['rooms'] as $room) {
                    $roomServiceProducts = array();
                    if (isset($room['services'])
                        && $room['services']
                    ) {
                        $roomServiceProducts = $room['services'];
                    }

                    // since we cannot update them after ordering them and will have to replace them if orderd here. So, we will not add them for now.
                    $roomDemands = json_encode(array());

                    if (isset($room['occupancy']) && count($room['occupancy'])) {
                        $occupancy = $room['occupancy'];
                    }

                    $idRoom = 0;
                    if (isset($room['id_room'])) {
                        $idRoom = $room['id_room'];
                    }

                    if ($quantityType) {
                        $occupancy = 1; // since we are adding rooms one by one into the cart.
                    }

                    if ($idHtlCartBookingData = $objHotelCartBookingData->updateCartBooking(
                        $roomType['id_room_type'],
                        $occupancy,
                        'up',
                        $idHotel,
                        $idRoom,
                        $dateFrom,
                        $dateTo,
                        $roomDemands,
                        $roomServiceProducts,
                        $this->context->cart->id,
                        $this->context->cart->id_guest
                    )) {
                        $objCartBookingData = new HotelCartBookingData((int) $idHtlCartBookingData);
                        $dateJoinKey = strtotime($dateFrom).strtotime($dateTo).$objCartBookingData->id_room;
                        // To update the price after valiate order is called.
                        if (isset($room['facilities'])
                            && $room['facilities']
                        ) {
                            $roomDemands = json_encode($room['facilities']);
                        }

                        $this->roomWiseInfomation[$dateJoinKey]['services'] = $roomServiceProducts;
                        $this->roomWiseInfomation[$dateJoinKey]['demands'] = $roomDemands;

                        if (isset($room['total_price_with_tax'])) {
                            $room['total_price_with_tax'] = Tools::ps_round($room['total_price_with_tax']/(1+$taxRateM), 6);
                            // need the id Room of the latest added room type
                            $this->featurePrices[] = $this->createFeaturePrice(
                                array(
                                    'id_product' => (int) $roomType['id_room_type'],
                                    'id_cart' => (int) $this->context->cart->id,
                                    'id_guest' => (int) $this->context->cart->id_guest,
                                    'date_from' => date('Y-m-d', strtotime($dateFrom)),
                                    'date_to' => date('Y-m-d', strtotime($dateTo)),
                                    'id_room' => $objCartBookingData->id_room,
                                    'price' => $room['total_price_with_tax']
                                )
                            );
                        }
                    }
                }
            } else {
                $roomWiseOccupancy = $occupancy;
                if (isset($roomType['number_of_rooms'])) {
                    while ($roomType['number_of_rooms'] > 1) {
                        $roomWiseOccupancy[] = reset($occupancy);
                        $roomType['number_of_rooms']--;
                    }
                }

                if ($quantityType) {
                    $roomWiseOccupancy = count($roomWiseOccupancy);
                }

                $roomDemands = json_encode(array());
                $roomServiceProducts = array();
                $objHotelCartBookingData->updateCartBooking(
                    $roomType['id_room_type'],
                    $roomWiseOccupancy,
                    'up',
                    $idHotel,
                    0,
                    $dateFrom,
                    $dateTo,
                    $roomDemands,
                    $roomServiceProducts,
                    $this->context->cart->id,
                    $this->context->cart->id_guest
                );

                if ($idRooms = $objHotelCartBookingData->getCustomerIdRoomsByIdCartIdProduct(
                    $this->context->cart->id,
                    $roomType['id_room_type'],
                    date('Y-m-d', strtotime($dateFrom)),
                    date('Y-m-d', strtotime($dateTo))
                )) {
                    if (isset($roomType['total_price_with_tax'])) {
                        $roomType['total_price_with_tax'] = (float) $roomType['total_price_with_tax']/(1+$taxRateM);
                        $roomType['total_price_with_tax'] = $roomType['total_price_with_tax']/count($idRooms);
                        $roomType['total_price_with_tax'] = Tools::ps_round($roomType['total_price_with_tax'], 6);
                        foreach ($idRooms as $idRoom) {
                            $this->featurePrices[] = $this->createFeaturePrice(
                                array(
                                    'id_product' => (int) $roomType['id_room_type'],
                                    'id_cart' => (int) $this->context->cart->id,
                                    'id_guest' => (int) $this->context->cart->id_guest,
                                    'date_from' => date('Y-m-d', strtotime($dateFrom)),
                                    'date_to' => date('Y-m-d', strtotime($dateTo)),
                                    'id_room' => $idRoom['id_room'],
                                    'price' => $roomType['total_price_with_tax']
                                )
                            );
                        }
                    }
                }
            }
        }

        $this->removeAutoAddedServices();
    }

    public function removeAutoAddedServices()
    {
        if (Validate::isLoadedobject($this->context->cart)) {
            $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail();
            if ($serviceProducts = $objRoomTypeServiceProductCartDetail->getServiceProductsInCart(
                $this->context->cart->id,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                null,
                1, // for auto added products
            )) {
                foreach ($serviceProducts as $serviceProduct) {
                    $dateJoinKey = strtotime($serviceProduct['date_from']).strtotime($serviceProduct['date_to']).$serviceProduct['id_room'];
                    if (isset($serviceProduct['selected_products_info']) && $serviceProduct['selected_products_info']) {
                        foreach ($serviceProduct['selected_products_info'] as $service) {
                            // Checking if the auto add service was sent in the request
                            if (!isset($this->roomWiseInfomation[$dateJoinKey]['services'][$service['id_product']])
                                && ($idRoomTypeServiceProductCartDetail = $objRoomTypeServiceProductCartDetail->alreadyExists(
                                $service['id_product'],
                                $this->context->cart->id,
                                $serviceProduct['htl_cart_booking_id'])
                            )) {
                                $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail((int) $idRoomTypeServiceProductCartDetail);
                                $objRoomTypeServiceProductCartDetail->delete();
                            }
                        }
                    }
                }
            }
        }
    }

    public function handlePutRequest($params)
    {
        $objOrder = new Order((int) $params['id']);
        $this->addOrderHistory($params);
        $this->addCustomerMessage($params);
        $this->processCurrency($params);
        $this->processGuestDetails($params['customer_detail']);
        $this->processCustomer($params['customer_detail']);
        $objHotelCartBookingData = new HotelCartBookingData();
        $objHotelBookingDetail = new HotelBookingDetail();
        $objRoomType = new HotelRoomType();
        $roomsToRemove = array();
        $roomsToAdd = array();
        $roomsToUpdate = array();
        $roomTypes = $params['room_types'];
        if ($roomsInOrder = $objHotelBookingDetail->getOrderCurrentDataByOrderId($objOrder->id)) {
            foreach ($roomsInOrder as $orderRoomKey => $orderRoom) {
                $dateProductJoinKey = $orderRoom['id_product'].'_'.strtotime($orderRoom['date_from']).strtotime($orderRoom['date_to']);
                if (isset($roomTypes[$dateProductJoinKey])) {
                    if (isset($roomTypes[$dateProductJoinKey]['number_of_rooms'])) {
                        $room = array();
                        $room_key = 'r_'.$orderRoom['id_room'];
                        // if there are multiple rooms then there can be a room at index 1 so we are adding a prifix for the below condition.
                        if (isset($roomTypes[$dateProductJoinKey]['rooms'][$room_key])) {
                            $room = $roomTypes[$dateProductJoinKey]['rooms'][$room_key];
                            unset($roomTypes[$dateProductJoinKey]['rooms'][$room_key]);
                        } else if (isset($roomTypes[$dateProductJoinKey]['rooms'][0])) {
                            $room = array_shift($roomTypes[$dateProductJoinKey]['rooms']);
                        }

                        if ($room) {
                            // update the room only if there is room wise breakdown.
                            $roomsToUpdate[$dateProductJoinKey]['requested'][$orderRoom['id_room']] = $room;
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
        if ($roomsToAdd && !$this->validateRequestedRoomTypes($roomsToAdd)) {
            if ($this->error_msg == '') {
                $this->error_msg = Tools::displayError('Requested room(s) not available');
            }

            return false;
        }

        // Adding new rooms in the booking
        if (count($roomsToAdd)) {
            $this->createNewCartForBooking($objOrder->id);
            $this->addRoomsToCart($roomsToAdd);
            $objCart = $this->context->cart;
            $objOrderDetail = new OrderDetail();
            $objOrderDetail->createList($objOrder, $objCart, $objOrder->getCurrentOrderState(), $objCart->getProducts(), 0, true, 0);

            // update totals amount of order
            // creating the new object to reload the data changes made while removing the rooms.
            $objOrder = new Order((int) $params['id']);
            $objOrder->total_products += (float)$objCart->getOrderTotal(false, Cart::ONLY_ROOMS);
            $objOrder->total_products_wt += (float)$objCart->getOrderTotal(true, Cart::ONLY_ROOMS);
            $objOrder->total_paid += Tools::ps_round((float)($objCart->getOrderTotal(true, Cart::ONLY_ROOMS)), 2);
            $objOrder->total_paid_tax_excl += Tools::ps_round((float)($objCart->getOrderTotal(false, Cart::ONLY_ROOMS)), 2);
            $objOrder->total_paid_tax_incl += Tools::ps_round((float)($objCart->getOrderTotal(true, Cart::ONLY_ROOMS)), 2);
            $objOrder->total_discounts += (float)abs($objCart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
            $objOrder->total_discounts_tax_excl += (float)abs($objCart->getOrderTotal(false, Cart::ONLY_DISCOUNTS));
            $objOrder->total_discounts_tax_incl += (float)abs($objCart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));

            // Save changes of order
            $res = $objOrder->update();
            $vatAddress = new Address((int) $objOrder->id_address_tax);
            $idLang = (int) $this->context->cart->id_lang;
            foreach ($roomsToAdd as $roomType) {
                $orderDetails = $objHotelBookingDetail->getPsOrderDetailsByProduct($roomType['id_room_type'], $objOrder->id);
                $IdOrderDetail = end($orderDetails)['id_order_detail']; // to get the max id_order_detail
                $objCartBookingData = new HotelCartBookingData();
                if ($cartBookingData = $objCartBookingData->getOnlyCartBookingData(
                    $this->context->cart->id,
                    $this->context->cart->id_guest,
                    $roomType['id_room_type']
                )) {
                    foreach ($cartBookingData as $cb_k => $cb_v) {
                        $objCartBookingData = new HotelCartBookingData($cb_v['id']);
                        $objCartBookingData->id_order = $objOrder->id;
                        $objCartBookingData->save();
                        $objBookingDetail = new HotelBookingDetail();
                        $objBookingDetail->id_product = $roomType['id_room_type'];
                        $objBookingDetail->id_order = $objOrder->id;
                        $objBookingDetail->id_order_detail = $IdOrderDetail;
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
                        $objBookingDetail->total_paid_amount = Tools::ps_round($total_price['total_price_tax_incl'], 5);

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
                            $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
                            $objRoomTypeServiceProductPrice = new RoomTypeServiceProductPrice();
                            $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail();
                            $dateJoinKey = strtotime($objCartBookingData->date_from).strtotime($objCartBookingData->date_to).$objCartBookingData->id_room;
                            if (isset($this->roomWiseInfomation[$dateJoinKey]['services'])
                                && ($services = $objRoomTypeServiceProductCartDetail->getRoomServiceProducts($objCartBookingData->id))
                            ) {
                                foreach ($services as $service) {
                                    $insertedServiceProductIdOrderDetail = $objBookingDetail->getLastInsertedServiceIdOrderDetail($objOrder->id, $service['id_product']);
                                    $numDays = 1;
                                    if (Product::getProductPriceCalculation($service['id_product']) == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                                        $numDays = HotelHelper::getNumberOfDays($objBookingDetail->date_from, $objBookingDetail->date_to);
                                    }

                                    $totalPriceTaxExcl = $objRoomTypeServiceProductPrice->getServicePrice(
                                        (int) $service['id_product'],
                                        $roomTypeInfo['id'],
                                        $service['quantity'],
                                        $objBookingDetail->date_from,
                                        $objBookingDetail->date_to,
                                        false
                                    );
                                    $totalPriceTaxIncl = $objRoomTypeServiceProductPrice->getServicePrice(
                                        (int)$service['id_product'],
                                        $roomTypeInfo['id'],
                                        $service['quantity'],
                                        $objBookingDetail->date_from,
                                        $objBookingDetail->date_to,
                                        true
                                    );
                                    $unitPriceTaxExcl = $totalPriceTaxExcl / ($numDays * $service['quantity']);
                                    $unitPriceTaxIncl = $totalPriceTaxIncl / ($numDays * $service['quantity']);
                                    if (isset($this->roomWiseInfomation[$dateJoinKey]['services'][$service['id_product']])
                                        && isset($this->roomWiseInfomation[$dateJoinKey]['services'][$service['id_product']]['total_price_with_tax'])
                                    ) {
                                        if ($unitPriceTaxExcl > 0) {
                                            $taxMultiplier = $unitPriceTaxIncl / $unitPriceTaxExcl;
                                        } else {
                                            $taxMultiplier = 1;
                                        }

                                        $totalPriceTaxExclOld = $totalPriceTaxExcl;
                                        $totalPriceTaxInclOld = $totalPriceTaxIncl;
                                        $unitPriceTaxExclOld = $unitPriceTaxExcl;
                                        $unitPriceTaxInclOld = $unitPriceTaxIncl;
                                        $totalPriceTaxExcl = 0;
                                        $totalPriceTaxIncl = 0;
                                        if ((int) $this->roomWiseInfomation[$dateJoinKey]['services'][$service['id_product']]['total_price_with_tax']) {
                                            $totalPriceTaxExcl = $this->roomWiseInfomation[$dateJoinKey]['services'][$service['id_product']]['total_price_with_tax']/$taxMultiplier;
                                            $totalPriceTaxIncl = $this->roomWiseInfomation[$dateJoinKey]['services'][$service['id_product']]['total_price_with_tax'];
                                        }

                                        $quantity = $service['quantity'];
                                        if ($objOrderDetail->product_price_calculation_method == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                                            $quantity = $quantity * HotelHelper::getNumberOfDays(
                                                $objHotelBookingDetail->date_from,
                                                $objHotelBookingDetail->date_to
                                            );
                                        }

                                        $unitPriceTaxExcl = 0;
                                        $unitPriceTaxIncl = 0;
                                        if ($totalPriceTaxExcl > 0) {
                                            $unitPriceTaxExcl = $totalPriceTaxExcl/$quantity;
                                            $unitPriceTaxIncl = $totalPriceTaxIncl/$quantity;
                                        }

                                        $priceDiffTaxExcl = $totalPriceTaxExclOld - $totalPriceTaxExcl;
                                        $priceDiffTaxIncl = $totalPriceTaxInclOld - $totalPriceTaxIncl;
                                        $objOrderDetail = new OrderDetail($insertedServiceProductIdOrderDetail);
                                        $objOrderDetail->total_price_tax_excl += $priceDiffTaxExcl;
                                        $objOrderDetail->total_price_tax_incl += $priceDiffTaxIncl;
                                        $objOrderDetail->unit_price_tax_excl = Tools::ps_round(($objOrderDetail->total_price_tax_excl / $objOrderDetail->product_quantity), 6);
                                        $objOrderDetail->unit_price_tax_incl = Tools::ps_round(($objOrderDetail->total_price_tax_incl / $objOrderDetail->product_quantity), 6);
                                        $objOrderDetail->save();

                                        $objOrder->total_paid_tax_excl += $totalPriceTaxExcl;
                                        $objOrder->total_paid_tax_incl += $totalPriceTaxIncl;
                                        $objOrder->total_paid += $totalPriceTaxIncl;
                                    }

                                    $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
                                    $objRoomTypeServiceProductOrderDetail->id_product = $service['id_product'];
                                    $objRoomTypeServiceProductOrderDetail->id_order = $objBookingDetail->id_order;
                                    $objRoomTypeServiceProductOrderDetail->id_order_detail = $insertedServiceProductIdOrderDetail;
                                    $objRoomTypeServiceProductOrderDetail->id_cart = $this->context->cart->id;
                                    $objRoomTypeServiceProductOrderDetail->id_htl_booking_detail = $objBookingDetail->id;
                                    $objRoomTypeServiceProductOrderDetail->unit_price_tax_excl = $unitPriceTaxExcl;
                                    $objRoomTypeServiceProductOrderDetail->unit_price_tax_incl = $unitPriceTaxIncl;
                                    $objRoomTypeServiceProductOrderDetail->total_price_tax_excl = $totalPriceTaxExcl;
                                    $objRoomTypeServiceProductOrderDetail->total_price_tax_incl = $totalPriceTaxIncl;
                                    $objRoomTypeServiceProductOrderDetail->name = $service['name'];
                                    $objRoomTypeServiceProductOrderDetail->quantity = $service['quantity'];
                                    $objRoomTypeServiceProductOrderDetail->save();
                                }

                                $objOrder->save();
                            }

                            if (isset($this->roomWiseInfomation[$dateJoinKey]['demands']) && $this->roomWiseInfomation[$dateJoinKey]['demands']) {
                                if ($demands = json_decode($this->roomWiseInfomation[$dateJoinKey]['demands'], true)) {
                                    $this->addDemandsInRoom($demands, $objBookingDetail->id);
                                }
                            }
                        }
                    }
                }
            }

            $this->deleteFeaturePrices();
            HotelRoomTypeFeaturePricing::deleteByIdCart($this->context->cart->id);
        }

        // only perform any update if request is valid.
        // Update the information for the services that were updated in the existing rooms
        if (count($roomsToUpdate)) {
            $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
            $objBookingDemand = new HotelBookingDemands();
            foreach ($roomsToUpdate as $roomsByDate) {
                if (isset($roomsByDate['requested'])
                    && $roomsByDate['requested']
                ) {
                    foreach ($roomsByDate['requested'] as $roomsKey => $room) {
                        $numDays = HotelHelper::getNumberOfDays($roomsByDate['order'][$roomsKey]['date_from'], $roomsByDate['order'][$roomsKey]['date_to']);
                        if (isset($room['total_price_with_tax'])
                            && ((float) $room['total_price_with_tax']) != ((float) $roomsByDate['order'][$roomsKey]['total_price_tax_incl']/$numDays)
                        ) {
                            $this->updateRoomPrice($room, $roomsByDate['order'][$roomsKey]);
                        }

                        $idHotelBookingDetail = $roomsByDate['order'][$roomsKey]['id'];
                        $existingServices = $objRoomTypeServiceProductOrderDetail->getSelectedServicesForRoom($idHotelBookingDetail);
                        $requestedServices = $room['services'];
                        $servicesToUpdate = array();
                        $servicesToRemove = array();
                        if (!empty($existingServices['additional_services'])) {
                            foreach ($existingServices['additional_services'] as $orderedService) {
                                if (isset($requestedServices[$orderedService['id_product']])) {
                                    if ($requestedServices[$orderedService['id_product']]['quantity'] != $orderedService['quantity']
                                        || (isset($requestedServices[$orderedService['id_product']]['total_price_with_tax'])  && $requestedServices[$orderedService['id_product']]['total_price_with_tax'] != $orderedService['total_price_tax_incl'])
                                    ) {
                                        $orderedService['new_quantity'] = $requestedServices[$orderedService['id_product']]['quantity'];
                                        $orderedService['id_htl_booking_detail'] = $idHotelBookingDetail;
                                        if (isset($requestedServices[$orderedService['id_product']]['total_price_with_tax'])  && $requestedServices[$orderedService['id_product']]['total_price_with_tax'] != $orderedService['total_price_tax_incl']) {
                                            $orderedService['total_price_with_tax'] = $requestedServices[$orderedService['id_product']]['total_price_with_tax'];
                                        }

                                        $orderedService['id_htl_booking_detail'] = $idHotelBookingDetail;
                                        $servicesToUpdate[] = $orderedService;
                                    }

                                    // Unsetting the existing service to filter the services that are not in order but in the request.
                                    unset($requestedServices[$orderedService['id_product']]);
                                } else {
                                    $servicesToRemove[] = $orderedService;
                                }
                            }
                        }

                        // adding the remaning services left after the filteration process
                        $this->removeServicesFromRoom($servicesToRemove);
                        $this->addServicesInRoom($requestedServices, $idHotelBookingDetail);
                        $this->updateServicesInRoom($servicesToUpdate);

                        $idOrder = $roomsByDate['order'][$roomsKey]['id_order'];
                        $idProduct = $roomsByDate['order'][$roomsKey]['id_product'];
                        $idRoom = $roomsByDate['order'][$roomsKey]['id_room'];
                        $dateFrom = $roomsByDate['order'][$roomsKey]['date_from'];
                        $dateTo = $roomsByDate['order'][$roomsKey]['date_to'];

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
                        $this->removeDemandsFromRoom($roomExtraDemand);
                        $this->addDemandsInRoom($requestedDemands, $idHotelBookingDetail);
                    }
                }
            }
        }

        // removing in the last.
        if (count($roomsToRemove)) {
            $this->removeRoomLineFromBooking($params, $roomsToRemove);
        }

        $cartRules = $objOrder->getCartRules();
        //Removing the stored cached object
        $this->removeCartRules($objOrder->id, $cartRules);
        $this->addCartRulesInOrder($params);

        if (isset($params['price_details']['total_paid']) && $objOrder->total_paid_real != $params['price_details']['total_paid']) {
            $newAmount = $params['price_details']['total_paid'] - $objOrder->total_paid_real;
            $this->addOrderPayment($params);
        }

        if (isset($params['price_details']['total_price_with_tax']) && $params['price_details']['total_price_with_tax'] != $objOrder->total_paid_tax_incl) {
            $objOrder = new Order($objOrder->id);
            $objOrder->total_paid_tax_incl = $params['price_details']['total_price_with_tax'];
            $objOrder->save();
        }

        $this->getBooking($objOrder->id);
    }

    public function removeCartRules($idOrder, $cartRules = array())
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

    public function addCartRulesInOrder($params)
    {
        if (isset($params['cart_rules'])) {
            $objOrder = new Order((int) $params['id']);
            $cartRulesFormatted = array();
            foreach ($params['cart_rules'] as $key => $cartRule) {
                $cartRulesFormatted[$key]['code'] = $cartRule['code'];
                $cartRulesFormatted[$key]['value'] = $cartRule['value'];
                $cartRulesFormatted[$key]['currency'] = isset($cartRule['currency']) ? $cartRule['currency'] : '';
                $cartRulesFormatted[$key]['type'] = 'amount';
            }

            if ($idCartRules = $this->sortCreateCartRules($cartRulesFormatted)) {
                foreach ($idCartRules as $idCartRule) {
                    $objCartRule = new CartRule($idCartRule);
                    $invoiceCollection = $objOrder->getInvoicesCollection();
                    $invoiceCartRules = array();
                    foreach ($invoiceCollection as $orderInvoice) {
                        if (!($objCartRule->reduction_amount > $orderInvoice->total_paid_tax_incl)) {
                            $this->errors[] = Tools::displayError('The discount value is greater than the order invoice total.').$orderInvoice->getInvoiceNumberFormatted(Context::getContext()->language->id, (int)$objOrder->id_shop).')';
                            $invoiceCartRules[$orderInvoice->id]['value_tax_incl'] = Tools::ps_round($objCartRule->reduction_amount, 2);
                            $invoiceCartRules[$orderInvoice->id]['value_tax_excl'] = Tools::ps_round($objCartRule->reduction_amount / (1 + ($objOrder->getTaxesAverageUsed() / 100)), 2);

                            // Update OrderInvoice
                            $this->applyDiscountOnInvoice($orderInvoice, $invoiceCartRules[$orderInvoice->id]['value_tax_incl'], $invoiceCartRules[$orderInvoice->id]['value_tax_excl']);
                        }
                    }
                    // Create OrderCartRule
                    foreach ($invoiceCartRules as $idInvoice => $rule) {
                        $ObjOrderCartRule = new OrderCartRule();
                        $ObjOrderCartRule->id_order = $objOrder->id;
                        $ObjOrderCartRule->id_cart_rule = $objCartRule->id;
                        $ObjOrderCartRule->id_order_invoice = $idInvoice;
                        $ObjOrderCartRule->name = $objCartRule->code;
                        $ObjOrderCartRule->value = $objCartRule->reduction_amount;
                        $ObjOrderCartRule->value_tax_excl = $rule['value_tax_excl'];
                        $ObjOrderCartRule->free_shipping = 0;
                        $ObjOrderCartRule->add();

                        $objOrder->total_discounts = Tools::ps_round($objOrder->total_discounts + $ObjOrderCartRule->value, 6);
                        $objOrder->total_discounts_tax_incl = Tools::ps_round($objOrder->total_discounts_tax_incl + $ObjOrderCartRule->value, 6);
                        $objOrder->total_discounts_tax_excl = Tools::ps_round($objOrder->total_discounts_tax_excl + $ObjOrderCartRule->value_tax_excl, 6);
                        $objOrder->total_paid = Tools::ps_round($objOrder->total_paid - $ObjOrderCartRule->value, 6);
                        $objOrder->total_paid_tax_incl = Tools::ps_round($objOrder->total_paid_tax_incl - $ObjOrderCartRule->value, 6);
                        $objOrder->total_paid_tax_excl = Tools::ps_round($objOrder->total_paid_tax_excl - $ObjOrderCartRule->value_tax_excl, 6);
                    }
                }
            }

            $objOrder->update();
        }
    }

    protected function applyDiscountOnInvoice($order_invoice, $value_tax_incl, $value_tax_excl)
    {
        // Update OrderInvoice
        $order_invoice->total_discount_tax_incl += $value_tax_incl;
        $order_invoice->total_discount_tax_excl += $value_tax_excl;
        $order_invoice->total_paid_tax_incl -= $value_tax_incl;
        $order_invoice->total_paid_tax_excl -= $value_tax_excl;
        $order_invoice->update();
    }

    public function removeRoomLineFromBooking($params, $roomsToRemove)
    {
        $objOrder = new Order((int) $params['id']);
        $objBookingDemand = new HotelBookingDemands();
        $objHotelBookingDetail = new HotelBookingDetail();
        $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
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
            $additionlServicesTI = $objRoomTypeServiceProductOrderDetail->getSelectedServicesForRoom(
                $idHotelBooking,
                1,
                1
            );
            $additionlServicesTE = $objRoomTypeServiceProductOrderDetail->getSelectedServicesForRoom(
                $idHotelBooking,
                1,
                0
            );
            $selectedAdditonalServices = $objRoomTypeServiceProductOrderDetail->getSelectedServicesForRoom(
                $idHotelBooking
            );
            $diffProductsTaxIncl = $bookingPriceTaxIncl;
            $diffProductsTaxExcl = $bookingPriceTaxExcl;
            $objHotelBookingDetail = new HotelBookingDetail((int) $idHotelBooking);
            $roomQuantity = (int) HotelHelper::getNumberOfDays($dateFrom, $dateTo);
            if (isset($selectedAdditonalServices['additional_services'])
                && count($selectedAdditonalServices['additional_services'])
            ) {
                foreach ($selectedAdditonalServices['additional_services'] as $service) {
                    $serviceOrderDetail = new OrderDetail($service['id_order_detail']);
                    $cart_quantity = $service['quantity'];
                    if ($service['product_price_calculation_method'] == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                        $cart_quantity = $cart_quantity * $quantity;
                    }

                    if ($cart_quantity >= $serviceOrderDetail->product_quantity) {
                        $serviceOrderDetail->delete();
                    } else {
                        $serviceOrderDetail->total_price_tax_incl -= Tools::ps_round($service['total_price_tax_incl'], 6);
                        $serviceOrderDetail->total_price_tax_excl -= Tools::ps_round($service['total_price_tax_excl'], 6);
                        $serviceOrderDetail->product_quantity -= $cart_quantity;

                        // update taxes
                        $serviceOrderDetail->updateTaxAmount($objOrder);

                        // Save order detail
                        $serviceOrderDetail->update();
                    }
                }
            }

            // Update Order
            // values changes as values are calculated accoding to the quantity of the product by webkul
            $objOrder->total_paid = Tools::ps_round($objOrder->total_paid - ($diffProductsTaxIncl + $roomExtraDemandTI + $additionlServicesTI));
            $objOrder->total_paid_tax_incl = Tools::ps_round($objOrder->total_paid_tax_incl - ($diffProductsTaxIncl + $roomExtraDemandTI + $additionlServicesTI));
            $objOrder->total_paid_tax_excl = Tools::ps_round($objOrder->total_paid_tax_excl - ($diffProductsTaxExcl + $roomExtraDemandTE + $additionlServicesTE));
            $objOrder->total_products = Tools::ps_round($objOrder->total_products - ($diffProductsTaxExcl + $additionlServicesTE));
            $objOrder->total_products_wt = Tools::ps_round($objOrder->total_products_wt - ($diffProductsTaxIncl + $additionlServicesTI));

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

            $objOrder->update();
            // delete the demands of this booking
            $objBookingDemand->deleteBookingDemands($idHotelBooking);
            $objRoomTypeServiceProductOrderDetail->deleteRoomSevices($idHotelBooking);
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

    public function addOrderHistory($params)
    {
        $objOrder = new Order((int) $params['id']);
        $orderStatus = false;
        if ($params['booking_status'] == self::BOOKING_API_BOOKING_STATUS_CANCELLED) {
            $orderStatus = new OrderState(Configuration::get('PS_OS_CANCELED'));
        } else if ($params['booking_status'] == self::BOOKING_API_BOOKING_STATUS_REFUNDED) {
            $orderStatus = new OrderState(Configuration::get('PS_OS_REFUND'));
        } else if ($params['booking_status'] == self::BOOKING_API_BOOKING_STATUS_COMPLETED) {
            $orderStatus = new OrderState(Configuration::get('PS_OS_PAYMENT_ACCEPTED'));
        } else if ($params['booking_status'] == self::BOOKING_API_BOOKING_STATUS_NEW) {
            $paymentStatus = false;
            if (isset($params['payment_status'])) {
                $paymentStatus = $params['payment_status'];
            }

            switch ($paymentStatus) {
                case self::BOOKING_API_PAYMENT_STATUS_COMPLETED:
                    $orderStatus =  new OrderState(Configuration::get('PS_OS_PAYMENT_ACCEPTED'));
                break;
                case self::BOOKING_API_PAYMENT_STATUS_PARTIAL:
                    $orderStatus =  new OrderState(Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED'));
                break;
                case self::BOOKING_API_PAYMENT_STATUS_AWATING:
                    $orderStatus =  new OrderState(Configuration::get('PS_OS_AWAITING_PAYMENT'));
                break;
                default:
                    return false;
            }
        }

        if ($orderStatus) {
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
    }

    public function addCustomerMessage($params)
    {
        if (isset($params['remark']) && !empty(trim($params['remark']))) {
            $objOrder = new Order((int) $params['id']);
            $objMessage = new Message();
            $message = strip_tags($params['remark'], '<br>');
            $saveMessage = true;
            $idCart = Cart::getCartIdByOrderId($objOrder->id);
            if ($customerMessages = Message::getMessagesByOrderId($objOrder->id, true)) {
                foreach ($customerMessages as $customerMessage) {
                    if ($customerMessage['message'] == $message) {
                        $saveMessage = false;
                    }
                }
            }

            if (Validate::isCleanHtml($message) && $saveMessage) {
                $objMessage->message = $message;
                $objMessage->id_cart = (int) $idCart;
                $objMessage->id_customer = (int) ($objOrder->id_customer);
                $objMessage->id_order = (int) $objOrder->id;
                $objMessage->private = 1;
                $objMessage->add();
            }
        }
    }

    public function addOrderPayment($params)
    {
        if (isset($params['payment_detail']) && $params['payment_detail']
            && isset($params['price_details']['total_paid']) && $params['price_details']['total_paid']
        ) {
            $objOrder = new Order($params['id']);
            $amount = $params['price_details']['total_paid'] - $objOrder->total_paid_real;
            $paymentMethod = null;
            if (isset($params['payment_details']['payment_method']) && $params['payment_details']['payment_method']) {
                $paymentMethod = $params['payment_details']['payment_method'];
            }

            $transactionId = null;
            if (isset($params['payment_details']['transaction_id']) && $params['payment_details']['transaction_id']) {
                $transactionId = $params['payment_details']['transaction_id'];
            }

            $paymentType = OrderPayment::PAYMENT_TYPE_ONLINE;
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
            }

            $objOrder->addOrderPayment(
                $amount,
                $paymentMethod,
                $transactionId,
                $newCurrency,
                null,
                $orderInvoice,
                $paymentType
            );
        }
    }

    public function createNewCartForBooking($idOrder)
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

    public function validatePutRequestParams($params)
    {
        $objCustomer = new Customer();
        $this->error_msg = '';
        if (!isset($params['id'])
            || !$params['id']
            || !Validate::isLoadedObject(new Order($params['id']))
        ) {
            $this->error_msg = Tools::displayError('Booking not found!!');
        } else if (!isset($params['booking_status'])
            || !$params['booking_status']
        ) {
            $this->error_msg = Tools::displayError('Invalid booking status');
        } else if (!isset($params['payment_status'])
            || !$params['payment_status']
        ) {
            $this->error_msg = Tools::displayError('Invalid payment status');
        } else if (!isset($params['room_types'])
            || !count($params['room_types'])
        ) {
            $this->error_msg = Tools::displayError('Rooms not found in the request.');
        } else if (isset($params['customer_detail']['id_customer'])
            && !Validate::isLoadedObject(new Customer($params['customer_detail']['id_customer']))
        )  {
            $this->error_msg = Tools::displayError('Invalid ID customer.');
        } else if (!isset($params['customer_detail']['email'])
            || !Validate::isEmail($params['customer_detail']['email'])
            || !$objCustomer->getByEmail($params['customer_detail']['email'])
        ) {
            $this->error_msg = Tools::displayError('Customer not found.');
        } else if (!$this->validatePutRequestRoomTypes($params['room_types'])
            && $this->error_msg == ''
        ) {
            $this->error_msg = Tools::displayError('Requested room(s) not available');
        } else {
            $this->validatePutCartRules($params);
        }

        if (!$this->error_msg && $this->error_msg == '') {
            return true;
        }

        return false;
    }

    public function validatePutCartRules($params)
    {
        if (isset($params['cart_rules']) && count($params['cart_rules'])) {
            $cartRulesCount = array();
            foreach ($params['cart_rules'] as $cartRule) {
                if (!($code = trim($cartRule['code']))
                    || !Validate::isCleanHtml($code)
                ) {
                    $this->error_msg = Tools::displayError('Invalid cart rule!!');
                    break;
                } else if (!Validate::isLoadedObject($objCartRule = new CartRule(CartRule::getIdByCode($code)))
                    && (!isset($cartRule['value']) || !$cartRule['value'])
                ) {
                    $this->error_msg = Tools::displayError('Value for the cart rule is required if cart rule is provided!!');
                    break;
                }
            }
        }
    }

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
                        } else if (isset($room['id_room']) && !Validate::isLoadedObject($room['id_room'])) {
                            $this->error_msg = Tools::displayError('Invalid Id room');
                            return false;
                        }

                        if (isset($room['services']) && $room['services']
                            && !$this->validateRequestedServices($room['services'], $roomType['id_room_type'])
                        ) {
                           return false;
                        }

                        if (isset($room['facilities']) && $room['facilities']
                            && !$this->validateRequestedDemands($room['facilities'], $roomType['id_room_type'])
                        ) {
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

    public function validateRoomType($roomType)
    {
        $status = true;
        if (!isset($roomType['id_room_type'])){
            $this->error_msg = Tools::displayError('Id room type is missing');
            $status = false;
        } else if (!isset($roomType['checkin_date']) || !Validate::isDate($roomType['checkin_date'])) {
            $this->error_msg = Tools::displayError('Invalid check in date in the request');
            $status = false;
        } else if (!isset($roomType['checkout_date']) || !Validate::isDate($roomType['checkout_date'])) {
            $this->error_msg = Tools::displayError('Invalid check out date in the request');
            $status = false;
        } else if (!isset($roomType['number_of_rooms'])) {
            $this->error_msg = Tools::displayError('number of rooms is missing');
            $status = false;
        } else if (!Validate::isLoadedObject(new Product((int) $roomType['id_room_type']))
            || !Product::isBookingProduct((int) $roomType['id_room_type'])
        ) {
            $this->error_msg = Tools::displayError('Invalid room type in the request');
            $status = false;
        } else if (isset($roomType['rooms']) && count($roomType['rooms']) && count($roomType['rooms']) != $roomType['number_of_rooms']) {
            $this->error_msg = Tools::displayError('Room count does not matches with the number of rooms');
            $status = false;
        } else if (strtotime($roomType['checkin_date']) > strtotime($roomType['checkout_date'])) {
            $this->error_msg = Tools::displayError('Invalid check in and check out dates in the request');
            $status = false;
        }

        return $status;
    }


    public function createFeaturePrice($params)
    {
        $feature_price_name = array();
        foreach (Language::getIDs(true) as $idLang) {
            $feature_price_name[$idLang] = 'Api-Booking-Price';
        }

        $numDays = HotelHelper::getNumberOfDays($params['date_from'], $params['date_to']);
        if (!$numDays) {
            $numDays = 1;
        }

        $objRoomTypeFeaturePricing = new HotelRoomTypeFeaturePricing();
        $objRoomTypeFeaturePricing->id_product = (int) $params['id_product'];
        $objRoomTypeFeaturePricing->id_cart = (int) $params['id_cart'];
        $objRoomTypeFeaturePricing->id_guest = (int) $params['id_guest'];
        $objRoomTypeFeaturePricing->id_room = (int) $params['id_room'];
        $objRoomTypeFeaturePricing->feature_price_name = $feature_price_name;
        $objRoomTypeFeaturePricing->date_selection_type = HotelRoomTypeFeaturePricing::DATE_SELECTION_TYPE_RANGE;
        $objRoomTypeFeaturePricing->date_from = date('Y-m-d', strtotime($params['date_from']));
        $objRoomTypeFeaturePricing->date_to = date('Y-m-d', strtotime($params['date_to']));
        $objRoomTypeFeaturePricing->is_special_days_exists = 0;
        $objRoomTypeFeaturePricing->special_days = json_encode(false);
        $objRoomTypeFeaturePricing->impact_way = HotelRoomTypeFeaturePricing::IMPACT_WAY_FIX_PRICE;
        $objRoomTypeFeaturePricing->impact_type = HotelRoomTypeFeaturePricing::IMPACT_TYPE_FIXED_PRICE;
        $objRoomTypeFeaturePricing->impact_value = $params['price']/$numDays;
        $objRoomTypeFeaturePricing->active = 1;
        $objRoomTypeFeaturePricing->groupBox = array_column(Group::getGroups(Configuration::get('PS_LANG_DEFAULT')), 'id_group');

        if ($objRoomTypeFeaturePricing->add()) {
            return $objRoomTypeFeaturePricing->id;
        }

        return false;
    }

    public function deleteFeaturePrices()
    {
        if (isset($this->featurePrices) && $this->featurePrices) {
            foreach ($this->featurePrices as $idFeaturePrice) {
                // To filter false ids
                if ((int) $idFeaturePrice) {
                    $objFeaturePrice = new HotelRoomTypeFeaturePricing((int) $idFeaturePrice);
                    $objFeaturePrice->delete();
                }
            }
        }
    }

    public function deleteApiCreatedVoucher()
    {
        if (isset($this->apiCreatedVoucher) && $this->apiCreatedVoucher) {
            foreach ($this->apiCreatedVoucher as $idCartRule) {
                $objCartRule = new CartRule((int) $idCartRule);
                if (Validate::isLoadedobject($objCartRule)) {
                    $objCartRule->delete();
                }
            }
        }
    }

    public function updateRoomPrice($room, $bookingData)
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

            $productPriceTI = Product::getPriceStatic((int) $objHotelBookingDetail->id_product, true);
            $productPriceTE = Product::getPriceStatic((int) $objHotelBookingDetail->id_product, false);
            if ($productPriceTE) {
                $taxRate = (($productPriceTI-$productPriceTE)/$productPriceTE)*100;
            } else {
                $taxRate = 0;
            }

            $taxRateM =  $taxRate/100;
            if (isset($room['total_price_with_tax'])) {
                $room['total_price_with_tax'] = (float) $room['total_price_with_tax']/ (1+$taxRateM);
                $this->featurePrices[] = $this->createFeaturePrice(
                    array(
                        'id_product' => (int) $objHotelBookingDetail->id_product,
                        'id_cart' => (int) $objCart->id,
                        'id_guest' => (int) $objCart->id_guest,
                        'date_from' => date('Y-m-d', strtotime($objHotelBookingDetail->date_from)),
                        'date_to' => date('Y-m-d', strtotime($objHotelBookingDetail->date_to)),
                        'id_room' => $objHotelBookingDetail->id_room,
                        'price' => $room['total_price_with_tax']
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

            $objHotelBookingDetail->total_price_tax_incl = $roomTotalPrice['total_price_tax_incl'];
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
            $this->deleteFeaturePrices();
        }
    }

    public function removeServicesFromRoom($services)
    {
        if (count($services)) {
            foreach ($services as $service) {
                $idRoomTypeServiceProductOrderDetail = $service['id_room_type_service_product_order_detail'];
                if (Validate::isLoadedObject($objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail((int) $idRoomTypeServiceProductOrderDetail))) {
                    $objOrderDetail = new OrderDetail((int) $objRoomTypeServiceProductOrderDetail->id_order_detail);
                    $priceTaxExcl = $objRoomTypeServiceProductOrderDetail->total_price_tax_excl;
                    $priceTaxIncl = $objRoomTypeServiceProductOrderDetail->total_price_tax_incl;
                    $quantity = $objRoomTypeServiceProductOrderDetail->quantity;
                    $objHotelBookingDetail = new HotelBookingDetail($objRoomTypeServiceProductOrderDetail->id_htl_booking_detail);

                    if ($objRoomTypeServiceProductOrderDetail->delete()) {
                        $objOrder = new Order($objRoomTypeServiceProductOrderDetail->id_order);
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

    // Does not contain the validations for the services since we are validating the products while formatting the request.
    public function addServicesInRoom($services, $idHotelBookingDetail)
    {
        if ($services) {
            $objHotelBookingDetail = new HotelBookingDetail((int) $idHotelBookingDetail);
            $objOrder = new Order($objHotelBookingDetail->id_order);
            // set context currency So that we can get prices in the order currency
            $this->context->currency = new Currency($objOrder->id_currency);
            $objHotelRoomType = new HotelRoomType();
            $objHotelCartBookingData = new HotelCartBookingData();
            $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
            $objRoomTypeServiceProductPrice = new RoomTypeServiceProductPrice();
            $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail();
            $roomHtlCartInfo = $objHotelCartBookingData->getRoomRowByIdProductIdRoomInDateRange(
                $objHotelBookingDetail->id_cart,
                $objHotelBookingDetail->id_product,
                $objHotelBookingDetail->date_from,
                $objHotelBookingDetail->date_to,
                $objHotelBookingDetail->id_room
            );

            $this->createNewCartForBooking($objOrder->id);
            $objCart = $this->context->cart;
            foreach ($services as $service) {
                $objRoomTypeServiceProductCartDetail->addServiceProductInCart(
                    $service['id_product'],
                    $service['quantity'],
                    $objCart->id,
                    $roomHtlCartInfo['id']
                );
            }

            $unitPriceTaxIncl = 0;
            $unitPriceTaxExcl = 0;
            $productList = $objCart->getProducts();
            $objOrderDetail = new OrderDetail();
            $objOrderDetail->createList($objOrder, $objCart, $objOrder->getCurrentOrderState(), $productList, 0, true);
            foreach ($productList as &$product) {
                if ($idRoomTypeServiceProductCartDetail = $objRoomTypeServiceProductCartDetail->alreadyExists(
                    $product['id_product'],
                    $objCart->id,
                    $roomHtlCartInfo['id'])
                ) {
                    $objRoomTypeServiceProductCartDetail = new RoomTypeServiceProductCartDetail((int) $idRoomTypeServiceProductCartDetail);
                    $numDays = 1;
                    if (Product::getProductPriceCalculation($product['id_product']) == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                        $numDays = HotelHelper::getNumberOfDays($objHotelBookingDetail->date_from, $objHotelBookingDetail->date_to);
                    }

                    $quantity = $objRoomTypeServiceProductCartDetail->quantity * $numDays;
                    $unitPriceTaxExcl = $objRoomTypeServiceProductPrice->getServicePrice(
                        (int) $product['id_product'],
                        0,
                        1,
                        $objHotelBookingDetail->date_from,
                        $objHotelBookingDetail->date_to,
                        false,
                        $objCart->id
                    )/ $numDays;
                    $unitPriceTaxIncl = $objRoomTypeServiceProductPrice->getServicePrice(
                        (int) $product['id_product'],
                        0,
                        1,
                        $objHotelBookingDetail->date_from,
                        $objHotelBookingDetail->date_to,
                        true,
                        $objCart->id
                    )/ $numDays;

                    if ($unitPriceTaxIncl > 0) {
                        $oldTaxMultiplier = $unitPriceTaxExcl / $unitPriceTaxIncl;
                    } else {
                        $oldTaxMultiplier = 1;
                    }

                    $totalPriceTaxExcl = $unitPriceTaxExcl * $quantity;
                    $totalPriceTaxIncl = $unitPriceTaxIncl * $quantity;
                    if (isset($service['total_price_with_tax'])) {
                        $totalPriceTaxExcl = 0;
                        $totalPriceTaxIncl = 0;
                        if ((int) $service['total_price_with_tax']) {
                            $totalPriceTaxExcl = $service['total_price_with_tax']/$oldTaxMultiplier;
                            $totalPriceTaxIncl = $service['total_price_with_tax'];
                            if ($totalPriceTaxExcl > 0) {
                                $unitPriceTaxExcl = $totalPriceTaxExcl/$quantity;
                                $unitPriceTaxIncl = $totalPriceTaxIncl/$quantity;
                            }
                        }
                    }

                    $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
                    $objRoomTypeServiceProductOrderDetail->id_product = $product['id_product'];
                    $objRoomTypeServiceProductOrderDetail->id_order = $objHotelBookingDetail->id_order;
                    $objRoomTypeServiceProductOrderDetail->id_order_detail = $objOrderDetail->id;
                    $objRoomTypeServiceProductOrderDetail->id_cart = $objCart->id;
                    $objRoomTypeServiceProductOrderDetail->id_htl_booking_detail = $objHotelBookingDetail->id;
                    $objRoomTypeServiceProductOrderDetail->unit_price_tax_excl = Tools::ps_round($unitPriceTaxExcl, 6);
                    $objRoomTypeServiceProductOrderDetail->unit_price_tax_incl = Tools::ps_round($unitPriceTaxIncl, 6);
                    $objRoomTypeServiceProductOrderDetail->total_price_tax_excl = Tools::ps_round($totalPriceTaxExcl, 6);
                    $objRoomTypeServiceProductOrderDetail->total_price_tax_incl = Tools::ps_round($totalPriceTaxIncl, 6);
                    $objRoomTypeServiceProductOrderDetail->name = $product['name'];
                    $objRoomTypeServiceProductOrderDetail->quantity = $objRoomTypeServiceProductCartDetail->quantity;
                    $objRoomTypeServiceProductOrderDetail->save();

                    // update totals amount of order
                    $objOrder->total_products += (float) $objRoomTypeServiceProductOrderDetail->total_price_tax_excl;
                    $objOrder->total_products_wt += (float) $objRoomTypeServiceProductOrderDetail->total_price_tax_incl;

                    $objOrder->total_paid += Tools::ps_round((float) $objRoomTypeServiceProductOrderDetail->total_price_tax_incl, 2);
                    $objOrder->total_paid_tax_excl += Tools::ps_round((float) $objRoomTypeServiceProductOrderDetail->total_price_tax_excl, 2);
                    $objOrder->total_paid_tax_incl += Tools::ps_round((float) $objRoomTypeServiceProductOrderDetail->total_price_tax_incl, 2);
                }
            }

            $objOrder->total_discounts += (float)abs($objCart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
            $objOrder->total_discounts_tax_excl += (float)abs($objCart->getOrderTotal(false, Cart::ONLY_DISCOUNTS));
            $objOrder->total_discounts_tax_incl += (float)abs($objCart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
            $objOrder->update();
        }
    }

    // Does not contain the validations for the services since we are validating the products while formatting the request.
    public function updateServicesInRoom($services)
    {
        if ($services) {
            foreach ($services as $service) {
                $objHotelBookingDetail = new HotelBookingDetail((int) $service['id_htl_booking_detail']);
                $idRoomTypeServiceProductOrderDetail = $service['id_room_type_service_product_order_detail'];
                $quantity = $service['new_quantity'];
                if (Validate::isLoadedObject($objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail((int) $idRoomTypeServiceProductOrderDetail))) {
                    $objOrderDetail = new OrderDetail((int) $objRoomTypeServiceProductOrderDetail->id_order_detail);
                    $oldPriceTaxExcl = $objRoomTypeServiceProductOrderDetail->total_price_tax_excl;
                    $oldPriceTaxIncl = $objRoomTypeServiceProductOrderDetail->total_price_tax_incl;
                    if ($oldPriceTaxExcl > 0) {
                        $oldTaxMultiplier = $oldPriceTaxIncl / $oldPriceTaxExcl;
                    } else {
                        $oldTaxMultiplier = 1;
                    }

                    $oldQuantity = $objRoomTypeServiceProductOrderDetail->quantity;
                    if ($quantity <= 0) {
                        $quantity = 1;
                    }

                    $objRoomTypeServiceProductOrderDetail->quantity = $quantity;
                    if ($objOrderDetail->product_price_calculation_method == Product::PRICE_CALCULATION_METHOD_PER_DAY) {
                        $quantity = $quantity * HotelHelper::getNumberOfDays(
                            $objHotelBookingDetail->date_from,
                            $objHotelBookingDetail->date_to
                        );
                    }

                    $totalPriceTaxExcl = $objRoomTypeServiceProductOrderDetail->unit_price_tax_excl * $quantity;
                    $totalPriceTaxIncl = $objRoomTypeServiceProductOrderDetail->unit_price_tax_incl * $quantity;
                    $unitPriceTaxExcl = $objRoomTypeServiceProductOrderDetail->unit_price_tax_excl;
                    $unitPriceTaxIncl = $objRoomTypeServiceProductOrderDetail->unit_price_tax_incl;
                    if (isset($service['total_price_with_tax'])) {
                        $totalPriceTaxExcl = 0;
                        $totalPriceTaxIncl = 0;
                        if ((int) $service['total_price_with_tax']) {
                            $totalPriceTaxExcl = $service['total_price_with_tax']/$oldTaxMultiplier;
                            $totalPriceTaxIncl = $service['total_price_with_tax'];
                            $unitPriceTaxExcl = $totalPriceTaxExcl/$quantity;
                            $unitPriceTaxIncl = $totalPriceTaxIncl/$quantity;
                        }
                    }

                    $objRoomTypeServiceProductOrderDetail->unit_price_tax_excl = Tools::ps_round($unitPriceTaxExcl, 6);
                    $objRoomTypeServiceProductOrderDetail->unit_price_tax_incl = Tools::ps_round($unitPriceTaxIncl, 6);
                    $objRoomTypeServiceProductOrderDetail->total_price_tax_excl = Tools::ps_round($totalPriceTaxExcl, 6);
                    $objRoomTypeServiceProductOrderDetail->total_price_tax_incl = Tools::ps_round($totalPriceTaxExcl, 6);
                    if ($objRoomTypeServiceProductOrderDetail->save()) {
                        $objOrder = new Order($objRoomTypeServiceProductOrderDetail->id_order);
                        $priceDiffTaxExcl = $objRoomTypeServiceProductOrderDetail->total_price_tax_excl - $oldPriceTaxExcl;
                        $priceDiffTaxIncl = $objRoomTypeServiceProductOrderDetail->total_price_tax_incl - $oldPriceTaxIncl;
                        $quantityDiff = $objRoomTypeServiceProductOrderDetail->quantity - $oldQuantity;

                        $objOrderDetail->product_quantity += $quantityDiff;
                        $objOrderDetail->total_price_tax_excl += $priceDiffTaxExcl;
                        $objOrderDetail->total_price_tax_incl += $priceDiffTaxIncl;
                        $objOrderDetail->unit_price_tax_excl = ($objOrderDetail->total_price_tax_excl / $objOrderDetail->product_quantity);
                        $objOrderDetail->unit_price_tax_incl = ($objOrderDetail->total_price_tax_incl / $objOrderDetail->product_quantity);
                        $objOrderDetail->updateTaxAmount($objOrder);

                        $objOrderDetail->update();

                        if ($objOrderDetail->id_order_invoice != 0) {
                            // values changes as values are calculated accoding to the quantity of the product by webkul
                            $objOrderInvoice = new OrderInvoice($objOrderDetail->id_order_invoice);
                            $objOrderInvoice->total_paid_tax_excl += $priceDiffTaxExcl;
                            $objOrderInvoice->total_paid_tax_incl += $priceDiffTaxIncl;
                            $objOrderInvoice->update();
                        }

                        $objOrder->total_paid_tax_excl += $priceDiffTaxExcl;
                        $objOrder->total_paid_tax_incl += $priceDiffTaxIncl;
                        $objOrder->total_paid += $priceDiffTaxIncl;

                        $objOrder->update();
                    }
                }
            }
        }
    }

    public function addDemandsInRoom($demands, $idHotelBooking)
    {
        if (Validate::isLoadedObject($objBookingDetail = new HotelBookingDetail((int) $idHotelBooking))) {
            if ($demands) {
                $objOrder = new Order($objBookingDetail->id_order);
                // set context currency So that we can get prices in the order currency
                $this->context->currency = new Currency($objOrder->id_currency);

                $vatAddress = new Address((int) $objOrder->id_address_tax);
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
                        $numDays = $objHtlBkDtl->getNumberOfDays(
                            $objBookingDetail->date_from,
                            $objBookingDetail->date_to
                        );
                        if ($numDays > 1) {
                            $qty *= $numDays;
                        }
                    }

                    $totalPriceTaxExcl = $unitPriceTaxExcl * $qty;
                    $totalPriceTaxIncl = $unitPriceTaxIncl * $qty;
                    $objBookingDemand->unit_price_tax_excl = $unitPriceTaxExcl;
                    $objBookingDemand->unit_price_tax_incl = $unitPriceTaxIncl;
                    if (isset($demand['total_price_with_tax'])) {
                        $totalPriceTaxExcl = $demand['total_price_with_tax'] / $taxMultiplier;
                        $totalPriceTaxIncl = $demand['total_price_with_tax'];
                        $objBookingDemand->unit_price_tax_excl = $totalPriceTaxExcl/$qty;
                        $objBookingDemand->unit_price_tax_incl = $totalPriceTaxExcl/$qty;
                    }

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
                        && Validate::isLoadedObject($vatAddress)
                    ) {
                        $taxManager = TaxManagerFactory::getManager(
                            $vatAddress,
                            $objGlobalDemand->id_tax_rules_group
                        );
                        $taxCalc = $taxManager->getTaxCalculator();
                        $objBookingDemand->tax_computation_method = (int)$taxCalc->computation_method;
                        $objBookingDemand->tax_calculator = $taxCalc;
                        // Now save tax details of the extra demand
                        $objBookingDemand->setBookingDemandTaxDetails();
                    }
                }

                $objOrder->save();
            }
        }
    }

    public function removeDemandsFromRoom($demands)
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
                                    $objOrder_invoice = new OrderInvoice($objOrderDetail->id_order_invoice);
                                    $objOrder_invoice->total_paid_tax_excl -= $objBookingDemand->total_price_tax_excl;
                                    $objOrder_invoice->total_paid_tax_incl -= $objBookingDemand->total_price_tax_incl;
                                    $objOrder_invoice->update();
                                }
                            }
                        }
                    }
                }
            }
        }

    }

    public function getBooking($idBooking)
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
            $objRoomTypeServiceProductOrderDetail = new RoomTypeServiceProductOrderDetail();
            $objOrderReturn = new OrderReturn();
            $idHotel = HotelBookingDetail::getIdHotelByIdOrder($objOrder->id);
            $objHotelBranchInformation = new HotelBranchInformation($idHotel, Configuration::get('PS_LANG_DEFAULT'));
            $objOrderState = new OrderState($objOrder->current_state, Configuration::get('PS_LANG_DEFAULT'));
            $objCustomer = new Customer($objOrder->id_customer);
            $params['id'] = (int) $objOrder->id;
            $params['id_property'] = (int) $idHotel;
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
                'total_paid' => (float) $objOrder->total_paid_real,
                'total_price_with_tax' => (float) $objOrder->total_paid_tax_incl,
                'total_tax' => (float) ($objOrder->total_paid_tax_incl - $objOrder->total_paid_tax_excl)
            );

            $orderCartRules = array();
            if ($cartRules = $objOrder->getCartRules()) {
                foreach ($cartRules as $cartRule) {
                    $rule = array();
                    $rule['code'] = $cartRule['name'];
                    $rule['value'] = (float) $cartRule['value'];
                    $rule['currency'] = strtoupper($objCurrency->iso_code);
                    $rule['id_order_invoice'] = (int) $cartRule['id_order_invoice'];
                    $orderCartRules[] = $rule;
                }
            }

            $params['associations']['customer_detail'] = $customerDetails;
            $params['associations']['price_details'] = $priceDetails;
            $params['associations']['cart_rules'] = $orderCartRules;
            $roomTypeInfo = array();
            if (Group::getPriceDisplayMethod($objCustomer->id_default_group) == PS_TAX_INC) {
                $useTax = 1;
            }
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
                        $roomTypeInfo[$dateJoin]['total_price_with_tax'] = (float)$orderData['total_price_tax_incl'];
                        $roomTypeInfo[$dateJoin]['total_tax'] = (float)($orderData['total_price_tax_incl'] - $orderData['total_price_tax_excl']);
                        $roomTypeInfo[$dateJoin]['number_of_rooms'] = 1;
                        $roomTypeInfo[$dateJoin]['name'] = $orderData['room_type_name'];
                    } else {
                        $roomTypeInfo[$dateJoin]['total_price_with_tax'] += $orderData['total_price_tax_incl'];
                        $roomTypeInfo[$dateJoin]['total_tax'] += $orderData['total_price_tax_incl'] - $orderData['total_price_tax_excl'];
                        $roomTypeInfo[$dateJoin]['number_of_rooms'] += 1;
                    }

                    $roomInfo = array();
                    $roomInfo['id_room'] = (int) $orderData['id_room'];
                    $roomInfo['id_hotel_booking'] = (int) $orderData['id'];
                    $roomInfo['adults'] = (int) $orderData['adults'];
                    $roomInfo['child'] = (int) $orderData['children'];
                    $roomInfo['total_price_with_tax'] = (float)$orderData['total_price_tax_incl'];
                    $roomInfo['total_tax'] = (float) ($orderData['total_price_tax_incl'] - $orderData['total_price_tax_excl']);
                    if(isset($roomInfo['facilities'])) {
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
                            if ($useTax) {
                                $demand['total_price'] = (float) $extraDemand['total_price_tax_incl'];
                                $demand['unit_price'] = (float) $extraDemand['unit_price_tax_incl'];
                            } else {
                                $demand['total_price'] = (float) $extraDemand['total_price_tax_excl'];
                                $demand['unit_price'] = (float) $extraDemand['unit_price_tax_excl'];
                            }

                            $demand['per_night'] = 0;
                            if ($extraDemand['price_calc_method'] == HotelRoomTypeGlobalDemand::WK_PRICE_CALC_METHOD_EACH_DAY) {
                                $demand['per_night'] = 1;
                            }

                            $roomInfo['facilities'][] = $demand;
                        }
                    }

                    if(isset($roomInfo['services'])) {
                        unset($roomInfo['services']);
                    }

                    if ($additionalServices = $objRoomTypeServiceProductOrderDetail->getroomTypeServiceProducts(
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

                                if ($useTax) {
                                    $services['unit_price'] = (float) $service['total_price_tax_incl'] / $services['quantity'];
                                    $services['total_price'] = (float) $service['total_price_tax_incl'];
                                } else {
                                    $services['unit_price'] = (float) $service['total_price_tax_excl'] / $services['quantity'];
                                    $services['total_price'] = (float) $service['total_price_tax_excl'];
                                }

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
            $this->output['booking'] = $params;
        }
    }

    public function createGuestForBooking()
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

    public function getRequestParams($head = false)
    {
        $putresource = fopen('php://input', 'r');
        $inputXML = '';
        while ($putData = fread($putresource, 1024)) {
            $inputXML .= $putData;
        }

        fclose($putresource);
        // If xml
        if (simplexml_load_string($inputXML)) {
            if (isset($inputXML) && strncmp($inputXML, 'xml=', 4) == 0) {
                $inputXML = Tools::substr($inputXML, 4);
            }
        } else {
            // If input type is json
            $array = json_decode($inputXML, true);
            if (isset($array['json']) && $array['json'] && ($head ? isset($array[$head]) : true)) {
                return ($head ? $array[$head] : $array);
            } else {
                WebserviceRequest::getInstance()->setError(500, 'Invalid request.', 127);
                return;
            }
        }

        try {
            $xml = new SimpleXMLElement($inputXML);
        } catch (Exception $error) {
            WebserviceRequest::getInstance()->setError(500, 'XML error : '.$error->getMessage()."\n".'XML length : '.Tools::strlen($inputXML)."\n".'Original XML : '.$inputXML, 127);

            return;
        }

        $xmlEntities = $xml->children();
        // Convert multi-dimention xml into an array
        $array = json_decode(json_encode($xmlEntities), true);

        return ($head ? $array[$head] : $array);
    }

}