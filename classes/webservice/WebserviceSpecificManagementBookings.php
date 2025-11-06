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

class WebserviceSpecificManagementBookingsCore Extends ObjectModel implements WebserviceSpecificManagementInterface
{
    protected $objOutput;
    protected $output;
    protected $wsObject;
    public $outputType = 'xml';

    public $wsRequestedRoomTypes = array();
    public $wsRequestedRooms = array();
    public $wsIdServices = false;
    public $bookingCustomer = false;
    public $wsFeaturePrices = array();
    public $wsCartRules = array();
    public $wsTaxRulesGroup = array();
    public $context;
    protected $error_msg = '';

    public const API_BOOKING_STATUS_NEW = 1;
    public const API_BOOKING_STATUS_COMPLETED = 2;
    public const API_BOOKING_STATUS_CANCELLED = 3;
    public const API_BOOKING_STATUS_REFUNDED = 4;

    public const API_BOOKING_PAYMENT_STATUS_COMPLETED = 1;
    public const API_BOOKING_PAYMENT_STATUS_PARTIAL = 2;
    public const API_BOOKING_PAYMENT_STATUS_AWATING = 3;

    public const API_CART_RULE_VALUE_TYPE_AMOUNT = 1;
    public const API_CART_RULE_VALUE_TYPE_PERCENTAGE = 2;

    public const API_SERVICE_PRICE_MODE_PER_BOOKING = 1;
    public const API_SERVICE_PRICE_MODE_PER_DAY = 2;

    public static $definition = array(
        'table' => 'htl_booking_detail',
        'primary' => 'id',
        'fields' => array()
    );

    public $webserviceParameters = array(
        'objectsNodeName' => 'bookings',
        'objectNodeName' => 'booking',
        'fields' => array(
            'id_property' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'currency' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'booking_status' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'payment_status' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'source' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'booking_date' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'remark' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'id_language' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
        ),
        'associations' => array(
            'customer_detail' => array(
                'only_leaf_nodes' => true,
                'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
                'firstname' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
                'lastname' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
                'email' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
                'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
                'address' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'city' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'zip' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'state_code' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'country_code' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            ),
            'price_details' => array(
                'only_leaf_nodes' => true,
                'total_paid' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
                'total_price_with_tax' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice')
            ),
            'payment_detail' => array(
                'only_leaf_nodes' => true,
                'payment_type' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'payment_method' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                'transaction_id' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            ),
            'cart_rules' => array(
                'setter' => false,
                'resource' => 'cart_rule',
                'fields' => array(
                    'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                    'code' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                    'type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
                    'value' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedInt'),
                    'currency' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                )
            ),
            'room_types' => array(
                'setter' => false,
                'resource' => 'room_type',
                'fields' => array(
                    'id_room_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
                    'checkin_date' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
                    'checkout_date' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
                    'number_of_rooms' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
                    'rooms' => array(
                        'resource' => 'room',
                        'fields' => array(
                            'id_room' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
                            'adults' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
                            'child' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
                            'child_ages' => array(
                                'child_age' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt')
                            ),
                            'unit_price_without_tax' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
                            'id_tax_rules_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
                            'total_tax' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
                            'services' => array(
                                'resource' => 'service',
                                'fields' => array(
                                    'id_service' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
                                    'quantity' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
                                    'price_mode' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
                                    'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                                    'unit_price_without_tax' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
                                    'total_price_without_tax' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
                                    'id_tax_rules_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
                                    'total_tax' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice')
                                )
                            ),
                            'facilities' => array(
                                'resource' => 'facility',
                                'fields' => array(
                                    'id_facility' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
                                    'id_option' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
                                    'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
                                    'unit_price_without_tax' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
                                    'id_tax_rules_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
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

    public function getWsObject()
    {
        return $this->wsObject;
    }

    public function getObjectOutput()
    {
        return $this->objOutput;
    }

    /**
     * Get the JSON response.
     */
    public function getResponseJson()
    {
        if ($this->output && is_array($this->output)) {
            $this->output = json_encode($this->output);
        } else if (isset($this->wsObject->urlFragments['schema']) && $this->wsObject->method == 'GET') {
            $this->output = array();
            $this->output['booking'] = $this->renderJsonFields($this->webserviceParameters['fields']);
            $this->output['booking']['associations'] = $this->renderJsonAssoiations($this->webserviceParameters['associations']);
            $this->output = json_encode($this->output);
        }
    }

    public function renderJsonFields($fields)
    {
        $output = array();
        foreach ($fields as $key => $field) {
            if (isset($field['resource']) && isset($field['fields'])) {
                $output[$key][] = $this->renderJsonFields($field['fields']);
            } else {
                $output[$key] = $this->renderJsonField($field);
            }
        }

        return $output;
    }

    public function renderJsonField($field)
    {
        if (isset($field['type'])) {
            $field = '';
        } else {
            $field = array();
        }

        return $field;
    }

    public function renderJsonAssoiations($fields)
    {
        $output = array();
        foreach ($fields as $key => $field) {
            if (isset($field['only_leaf_nodes'])) {
                unset($field['only_leaf_nodes']);
                $output[$key] = $this->renderJsonFields($field);
            } else if (isset($field['fields'])) {
                $output[$key][] = $this->renderJsonFields($field['fields']);
            }
        }

        return $output;
    }

    /**
     * Get the XML response.
     */
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
            $this->output = $this->renderXmlOutputUsingArray($this->output, array(), $parentKeys);
        }

        $this->output = $this->objOutput->getObjectRender()->overrideContent($this->output);
    }

    public function getContent()
    {
        return $this->output;
    }

    /**
     * Always called first, and is used to handle the request.
     */
    public function manage()
    {
        $this->context = Context::getContext();
        if (get_class($this->objOutput->getObjectRender()) == 'WebserviceOutputJSON') {
            $this->outputType = 'json';
        }

        switch ($this->wsObject->method) {
            case 'GET':
            case 'HEAD':
                if (isset($this->wsObject->urlSegment[1]) && $this->wsObject->urlSegment[1]) {
                    $this->getBookingDetails($this->wsObject->urlSegment[1]);
                    $this->renderResponse();
                } else if (isset($this->wsObject->urlFragments['schema'])) {
                    if ($this->outputType != 'json')  {
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
                    }

                    $this->renderResponse();
                } else {
                    // @todo: add filters for the booking webservice
                    // $filters = $this->manageFilters();
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

            break;
            case 'POST':
                $inputData = $this->getRequestParams('booking');
                $this->formatRequestData($inputData);
                if ($this->validatePostRequest($inputData)) {
                    $this->handlePostRequest($inputData);
                    $this->deleteWsServices();
                    $this->deleteWsFeaturePrices();
                    $this->deleteWsCartRules();
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
                $this->formatRequestData($inputData);
                if ($this->validatePutRequest($inputData)) {
                    $this->handlePutRequest($inputData);
                    $this->deleteWsServices();
                    $this->deleteWsFeaturePrices();
                    $this->deleteWsCartRules();
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

    /**
     * Read the data send with POST and PUT requests.
     */
    public function getRequestParams($head = false)
    {
        $postData = fopen('php://input', 'r');
        $inputXML = '';
        while ($putData = fread($postData, 1024)) {
            $inputXML .= $putData;
        }

        fclose($postData);
        // If xml
        if ($array = json_decode($inputXML, true))  {
            if (($head ? isset($array[$head]) : true)) {
                return ($head ? $array[$head] : $array);
            } else {
                WebserviceRequest::getInstance()->setError(500, 'Invalid request.', 127);
                return;
            }
        } else if (simplexml_load_string($inputXML)) {
            if (isset($inputXML) && strncmp($inputXML, 'xml=', 4) == 0) {
                $inputXML = Tools::substr($inputXML, 4);
            }
        } else {
            return false;
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

    /**
     * Formatting the request data for easier handeling.
     */
    public function formatRequestData(&$data)
    {
        $associations = array();
        if (isset($data['associations'])) {
            $associations = $data['associations'];
            unset($data['associations']);
        }

        $data = array_merge($data, $associations);
        if (isset($data['room_types'])) {
            $data['room_types'] = $this->formatRoomTypesInRequestData($data);
        }

        if (isset($data['cart_rules'])) {
            $data['cart_rules'] = $this->formatCartRulesInRequestData($data);
        }
    }

    public function formatCartRulesInRequestData($data)
    {
        $formattedCartRules = array();
        if (isset($data['cart_rules']['cart_rule'][0])) {
            $formattedCartRules = $data['cart_rules']['cart_rule'];
        } else if (isset($data['cart_rules'])
            && !isset($data['cart_rules'][0])
            && isset($data['cart_rules']['cart_rule'])
        ) {
            $formattedCartRules[] = $data['cart_rules']['cart_rule'];
        } else if (isset($data['cart_rules'][0])) {
            $formattedCartRules = $data['cart_rules'];
        }

        return $formattedCartRules;
    }

    public function formatRoomTypesInRequestData($data)
    {
        $roomTypes = array();
        if (isset($data['room_types']['room_type'][0])) {
            $roomTypes = $data['room_types']['room_type'];
        } else if (isset($data['room_types'])
            && !isset($data['room_types'][0])
            && isset($data['room_types']['room_type'])
        ) {
            $roomTypes[] = $data['room_types']['room_type'];
        } else if (isset($data['room_types']) && isset($data['room_types'][0])) {
            $roomTypes = $data['room_types'];
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
                    $formattedRoomTypes[$dateProductJoinKey]['rooms'] = $this->formatRoomInRequestData($roomType);
                }
            }

            $roomTypes = $formattedRoomTypes;
        }

        return $roomTypes;
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
                $occupancy = $this->formatOccupancyInRequestData($room, $data['id_room_type']);
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

    public function formatOccupancyInRequestData($data, $idRoomType)
    {
        $objRoomType = new HotelRoomType();
        // using to set base occupancy for the room if no occupancy is given, and we are not validating occupancy since Wsorder has booking without occupancy
        $roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($idRoomType);
        $child_ages = array();
        if (isset($data['child_ages']['child_age'][0])
            && is_array($data['child_ages']['child_age'])
        ) {
            $child_ages = $data['child_ages']['child_age'];
        } else if (isset($data['child_ages'])
            && !isset($data['child_ages'][0])
            && isset($data['child_ages']['child_age'])
            && $data['child_ages']['child_age']
        ) {
            $child_ages = $data['child_ages']['child_age'];
        } else if (isset($data['child_ages']) && isset($data['child_ages'][0])) {
            $child_ages = $data['child_ages'];
        } else {
            $data['child'] = 0;
        }

        if (isset($data['child']) && !$data['child']) {
            $child_ages = array();
        } else if (!is_array($child_ages)) {
            $child_ages = array($child_ages);
        }

        return array(
            array(
                'adults' => isset($data['adults']) ? $data['adults'] : $roomTypeInfo['adults'],
                'children' =>  isset($data['child']) ? $data['child'] : $roomTypeInfo['children'],
                'child_ages' => $child_ages
            )
        );
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
                } else if (isset($service['total_tax'])) {
                    $formattedServices[$key]['total_tax'] = $service['total_tax'];
                }
            } else {
                $service['is_new'] = true;
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

    /**
     * Validating the POST request.
     */
    public function validatePostRequest($params)
    {
        $this->error_msg = '';
        if (isset($params['id'])) {
            $this->error_msg = Tools::displayError('id is forbidden when adding a new resource');
        } else if (!isset($params['currency'])
            || !$params['currency']
            || !Currency::getIdByIsoCode($params['currency'])
            || (!Validate::isLoadedObject((new Currency(Currency::getIdByIsoCode($params['currency'])))))
        ) {
            $this->error_msg = Tools::displayError('Please provide valid currency.');
        } elseif (!isset($params['customer_detail'])
            || !$params['customer_detail']
        ) {
            $this->error_msg = Tools::displayError('Customer details not found.');
        } else if (!isset($params['id_property'])
            || !Validate::isLoadedObject(new HotelBranchInformation((int) $params['id_property']))
        ) {
            $this->error_msg = Tools::displayError('Please provide a valid id_property.');
        } else if (isset($params['customer_detail']['id_customer'])
            && $params['customer_detail']['id_customer']
            && !Validate::isLoadedObject(new Customer((int) $params['customer_detail']['id_customer']))
        ) {
            $this->error_msg = Tools::displayError('Please provide a valid id_customer.');
        } else if (!isset($params['customer_detail']['firstname'])
            || !$params['customer_detail']['firstname']
            || empty(trim($params['customer_detail']['firstname']))
        ) {
            $this->error_msg = Tools::displayError('Please provide a valid first name.');
        } else if (!isset($params['customer_detail']['lastname'])
            || !$params['customer_detail']['lastname']
            || empty(trim($params['customer_detail']['lastname']))
        ) {
            $this->error_msg = Tools::displayError('Please provide a valid last name.');
        } else if (!isset($params['customer_detail']['email'])
            || !$params['customer_detail']['email']
            || empty(trim($params['customer_detail']['email']))
        ) {
            $this->error_msg = Tools::displayError('Please provide a valid email.');
        } else if (Configuration::get('PS_ONE_PHONE_AT_LEAST')
            && (!isset($params['customer_detail']['phone']) || !$params['customer_detail']['phone'] || empty(trim($params['customer_detail']['phone'])))
        ) {
            $this->error_msg = Tools::displayError('Please provide a valid phone number.');
        } else if (isset($params['price_details']['total_paid'])
            && $params['price_details']['total_paid']
            && (!Validate::isPrice($params['price_details']['total_paid']))
        ) {
            $this->error_msg = Tools::displayError('Invalid value for total paid.');
        } else if (isset($params['price_details']['total_price_with_tax'])
            && $params['price_details']['total_price_with_tax']
            && (!Validate::isPrice($params['price_details']['total_price_with_tax']))
        ) {
            $this->error_msg = Tools::displayError('Invalid value for total amount with tax.');
        } else if (!$this->validateAddressFields($params['customer_detail'])
            && $this->error_msg == ''
        ) {
            $this->error_msg = Tools::displayError('Invalid address provided.');
        } else if (!$this->validateRequestedRoomTypes($params['room_types'], $params['id_property'])
            && $this->error_msg == ''
        ) {
            $this->error_msg = Tools::displayError('Requested room(s) not available.');
        } else if (isset($params['payment_detail']['payment_type'])
            && $params['payment_detail']['payment_type'] != 'online'
            && $params['payment_detail']['payment_type'] != 'remote'
            && $params['payment_detail']['payment_type'] != 'pay at hotel'
        ) {
            $this->error_msg = Tools::displayError('Invalid payment type.');
        } else if (isset($params['booking_status'])
            && ($params['booking_status'] < self::API_BOOKING_STATUS_NEW || $params['booking_status'] > self::API_BOOKING_STATUS_REFUNDED)
        ) {
            $this->error_msg = Tools::displayError('Invalid booking status.');
        } else if (isset($params['payment_status']) &&
            ($params['payment_status'] < self::API_BOOKING_PAYMENT_STATUS_COMPLETED || $params['payment_status'] > self::API_BOOKING_PAYMENT_STATUS_AWATING)
        ) {
            $this->error_msg = Tools::displayError('Invalid payment status.');
        } else if (isset($params['cart_rules']) && $params['cart_rules']) {
            if (is_array($params['cart_rules'])) {
                $this->validateCartRules($params['cart_rules']);
            } else {
                $this->error_msg = Tools::displayError('Invalid cart rules.');
            }
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

    /**
     * Checking room type information validity.
     */
    public function validateRequestedRoomTypes($roomTypes = array(), $idHotel = 0)
    {
        $objBookingDetail = new HotelBookingDetail();
        $objRoomType = new HotelRoomType();
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

    /**
     * Operations required for PUT requests.
     */
    public function handlePostRequest($params)
    {
        $this->context->cart = new Cart();
        $this->processGuestDetails($params['customer_detail']);
        $this->processLanguage($params);
        $this->processCurrency($params);
        // Saving the cart after adding the guest, language and the currency in the cart.
        $this->context->cart->save();
        $this->addRoomsInCart($params['room_types']);
        $this->processCustomer($params['customer_detail']);
        // validating Cart rules here since the cart rule checkValidity() only works if there are products in the cart.
        if (($error = $this->applyCartRules($params)) && $error != '') {
            throw new WebserviceException(
                $error,
                array(404, 400)
            );

            return false;
        }

        $totalAmount = isset($params['price_details']['total_paid']) ? $params['price_details']['total_paid'] : 0;
        $totalPrice = isset($params['price_details']['total_price_with_tax']) ? $params['price_details']['total_price_with_tax'] : 0;
        $objPaymentModule = new WebserviceOrder();
        $bookingStatus = self::API_BOOKING_STATUS_NEW;
        if (isset($params['booking_status']) && $params['booking_status']) {
            $bookingStatus = $params['booking_status'];
        }

        $cartTotal = $this->context->cart->getOrderTotal(true, Cart::BOTH);
        switch ($bookingStatus) {
            case self::API_BOOKING_STATUS_NEW:
                if ($totalAmount > 0 && $totalPrice > 0 && $totalPrice == $totalAmount) {
                    $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
                } else if ($totalAmount > 0 && $totalAmount < $cartTotal) {
                    $orderStatus = Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED');
                } else if ($totalAmount >= $cartTotal) {
                    $orderStatus = $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
                }  else {
                    $paymentStatus = false;
                    if (isset($params['payment_status'])) {
                        $paymentStatus = $params['payment_status'];
                    }

                    switch ($paymentStatus) {
                        case self::API_BOOKING_PAYMENT_STATUS_COMPLETED:
                            $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
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
                }
                break;
            case self::API_BOOKING_STATUS_COMPLETED:
                $orderStatus = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
                if (!$totalAmount) {
                    $totalAmount = $cartTotal;
                } else if ($totalAmount > 0 && $totalAmount < $cartTotal) {
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

        $objOrderState = new OrderState($orderStatus);
        if ($objOrderState->paid) {
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

            $objOrder->save();
            $this->getBookingDetails($objPaymentModule->currentOrder);

            return true;
        }

        $this->wsObject->setError(400, Tools::displayError('Unable to create booking.'), 200);

        return false;
    }

    public function manageOrderPrice($idOrder, $params)
    {
        $objOrder = new Order($idOrder);
        if (isset($params['price_details']['total_price_with_tax']) && (int) $params['price_details']['total_price_with_tax']) {
            if ($params['price_details']['total_price_with_tax'] < $objOrder->total_paid_tax_incl) {
                $cartRule['code'] = Tools::passwdGen(8, 'NO_NUMERIC');
                $cartRule['currency'] =  '';
                $cartRule['value'] = $objOrder->total_paid_tax_incl - $params['price_details']['total_price_with_tax'];
                if ($cartRule['value']) {
                    $cartRule['value'] /= count($objOrder->getInvoicesCollection()) ? count($objOrder->getInvoicesCollection()) : 1;
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
                        $dateRoomJoinKey = strtotime($orderRoom['date_from']).''.strtotime($orderRoom['date_to']).$orderRoom['id_room'];
                        if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['room']['unit_price_without_tax'])) {
                            $objHotelBookingDetail = new HotelBookingDetail((int) $orderRoom['id']);
                            $requestedPrice -= $objHotelBookingDetail->total_price_tax_incl;
                        } else {
                            $roomsToUpdate[] = $orderRoom;
                        }
                    }

                    $serviceProductPrice = $objOrder->getTotalProductsWithTaxes(false, false, Product::SELLING_PREFERENCE_WITH_ROOM_TYPE);
                    if ($demands = $objHotelBookingDemands->getExtraDemandsTaxesDetails($objOrder->id)) {
                        $demandsPrice = array_sum(array_column($demands, 'total_price_tax_excl'));
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
                                $priceDiffTaxIncl = $objHotelBookingDetail->total_price_tax_incl - $oldPriceTaxIncl;
                                $priceDiffTaxExcl = $objHotelBookingDetail->total_price_tax_excl - $oldPriceTaxExcl;

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
    }

    public function updateRoomTaxRulesGroupsInOrder($idOrder)
    {
        $objOrder = new Order($idOrder);
        $objHotelBookingDetail = new HotelBookingDetail();
        if ($roomsInOrder = $objHotelBookingDetail->getOrderCurrentDataByOrderId($idOrder)) {
            foreach ($roomsInOrder as $orderRoomKey => $orderRoom) {
                $dateRoomJoinKey = strtotime($orderRoom['date_from']).''.strtotime($orderRoom['date_to']).$orderRoom['id_room'];
                if (isset($this->wsRequestedRooms[$dateRoomJoinKey])) {
                    $objHotelBookingDetail = new HotelBookingDetail((int) $orderRoom['id']);
                    $objOrderDetail = new OrderDetail((int) $objHotelBookingDetail->id_order_detail);

                    $priceWithTax = $objHotelBookingDetail->total_price_tax_incl;
                    $objAddress = new Address((int) $objOrder->id_address_tax);
                    if (!empty($this->wsRequestedRooms[$dateRoomJoinKey]['total_tax'])) {
                        $this->wsRequestedRooms[$dateRoomJoinKey]['id_tax_rules_group'] = $this->createTaxRule(($this->wsRequestedRooms[$dateRoomJoinKey]['total_tax']/$objHotelBookingDetail->total_price_tax_excl)*100, $objAddress);
                    } else if (isset($this->wsRequestedRooms[$dateRoomJoinKey]['total_tax'])) {
                        $objOrderDetail->id_tax_rules_group = 0;
                        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'order_detail_tax` WHERE id_order_detail='.(int)$objOrderDetail->id);
                        $priceWithTax = $objHotelBookingDetail->total_price_tax_excl + $this->wsRequestedRooms[$dateRoomJoinKey]['total_tax'];
                    }

                    if (isset($this->wsRequestedRooms[$dateRoomJoinKey]['id_tax_rules_group'])) {
                        // Getting new Tax
                        $objTaxManager = TaxManagerFactory::getManager($objAddress, $this->wsRequestedRooms[$dateRoomJoinKey]['id_tax_rules_group']);
                        $objTaxCalculator = $objTaxManager->getTaxCalculator();
                        $priceWithTax = $objTaxCalculator->addTaxes($objHotelBookingDetail->total_price_tax_excl);
                    }

                    $taxDiff = $priceWithTax - $objHotelBookingDetail->total_price_tax_incl;

                    // Updating the price
                    $objHotelBookingDetail->total_price_tax_incl += $taxDiff;
                    $objHotelBookingDetail->save();

                    $objOrderDetail->unit_price_tax_incl += $taxDiff;
                    $objOrderDetail->total_price_tax_incl += $taxDiff;
                    if (($objOrderDetail->id_order_invoice)
                        && Validate::isLoadedObject($objOrderInvoice = new OrderInvoice($objOrderDetail->id_order_invoice))
                    ) {
                        $objOrderInvoice->total_products_wt += $taxDiff;
                        $objOrderInvoice->total_paid_tax_incl += $taxDiff;
                        $objOrderInvoice->save();
                    }

                    $objOrderDetail->save();
                    if (isset($this->wsRequestedRooms[$dateRoomJoinKey]['id_tax_rules_group'])) {
                        $this->saveTaxCalculator($objOrderDetail->id, $this->wsRequestedRooms[$dateRoomJoinKey]['id_tax_rules_group']);
                    }
                    $objOrder->total_paid += $taxDiff;
                    $objOrder->total_paid_tax_incl += $taxDiff;
                    $objOrder->total_products_wt += $taxDiff;
                }
            }

            $objOrder->update();
        }
    }

    public function saveTaxCalculator($idOrderDetail, $idTaxRulesGroup)
    {
        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'order_detail_tax` WHERE id_order_detail='.(int)$idOrderDetail);

        $idTax = $this->wsTaxRulesGroup[$idTaxRulesGroup]['tax'];
        $objOrderDetail = new OrderDetail($idOrderDetail);
        $values = '('.(int)$objOrderDetail->id.','.(int)$idTax.','.
            (float)($objOrderDetail->unit_price_tax_incl - $objOrderDetail->unit_price_tax_excl).','.
            (float)($objOrderDetail->total_price_tax_incl - $objOrderDetail->total_price_tax_excl).'),';
        $values = rtrim($values, ',');
        $sql = 'INSERT INTO `'._DB_PREFIX_.'order_detail_tax` (id_order_detail, id_tax, unit_amount, total_amount)
				VALUES '.$values;

        return Db::getInstance()->execute($sql);
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
                    $dateRoomJoinKey = strtotime($objHotelBookingDetail->date_from).''.strtotime($objHotelBookingDetail->date_to).$orderedService['id_room'];
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
                                } else if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_price_without_tax'])) {
                                    $totalPriceTaxExcl = $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_price_without_tax'];
                                    $totalPriceTaxIncl = $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_price_without_tax'] * $oldTaxMultiplier;
                                    $unitPriceTaxExcl = $totalPriceTaxExcl / $quantity;
                                    $unitPriceTaxIncl = $totalPriceTaxIncl / $quantity;

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
                                }
                            }

                            $objAddress = new Address((int) $objOrder->id_address_tax);
                            $isAutoAdded = false;
                            if ($objOrderDetail->product_auto_add && $objOrderDetail->product_price_addition_type == Product::PRICE_ADDITION_TYPE_WITH_ROOM) {
                                $isAutoAdded = true;
                            }
                            $priceDiffTaxIncl = 0;
                            if (!empty($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_tax'])) {
                                $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group'] = $this->createTaxRule(($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_tax']/$objServiceProductOrderDetail->total_price_tax_excl)*100, $objAddress);
                            } else if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_tax'])) {
                                $objOrderDetail->id_tax_rules_group = 0;
                                Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'order_detail_tax` WHERE id_order_detail='.(int)$objOrderDetail->id);
                                $unitPriceTaxIncl = $objServiceProductOrderDetail->total_price_tax_excl + $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_tax'];
                                $priceDiffTaxIncl = $unitPriceTaxIncl - $objServiceProductOrderDetail->total_price_tax_incl;

                                $objServiceProductOrderDetail->unit_price_tax_incl = Tools::ps_round($unitPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                                $objServiceProductOrderDetail->total_price_tax_incl = Tools::ps_round(($unitPriceTaxIncl * $quantity), _PS_PRICE_COMPUTE_PRECISION_);
                            }

                            if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group'])) {
                                $objTaxManager = TaxManagerFactory::getManager($objAddress, $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group']);
                                $objTaxCalculator = $objTaxManager->getTaxCalculator();
                                $unitPriceTaxIncl = $objTaxCalculator->addTaxes($objServiceProductOrderDetail->unit_price_tax_excl);
                                $oldPriceTaxIncl = $objServiceProductOrderDetail->total_price_tax_incl;

                                $objServiceProductOrderDetail->unit_price_tax_incl = Tools::ps_round($unitPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                                $objServiceProductOrderDetail->total_price_tax_incl = Tools::ps_round(($unitPriceTaxIncl * $quantity), _PS_PRICE_COMPUTE_PRECISION_);

                                $priceDiffTaxIncl = $objServiceProductOrderDetail->total_price_tax_incl - $oldPriceTaxIncl;
                            }

                            $objOrderDetail->total_price_tax_incl += $priceDiffTaxIncl;
                            $objOrderDetail->unit_price_tax_incl = Tools::ps_round(($objOrderDetail->total_price_tax_incl / $objOrderDetail->product_quantity), _PS_PRICE_COMPUTE_PRECISION_);

                            $objOrder->total_paid_tax_incl += $priceDiffTaxIncl;
                            $objOrder->total_paid += $priceDiffTaxIncl;

                            $objServiceProductOrderDetail->save();
                            $objOrderDetail->save();
                            if (!$isAutoAdded && isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group'])) {
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
                    $dateRoomJoinKey = strtotime($orderedRoom['date_from']).''.strtotime($orderedRoom['date_to']).$orderedRoom['id_room'];
                    if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['demands']) && $this->wsRequestedRoomTypes[$dateRoomJoinKey]['demands']) {
                        if ($demands = json_decode($this->wsRequestedRoomTypes[$dateRoomJoinKey]['demands'], true)) {
                            $this->addDemandsInOrderedRoom($demands, $orderedRoom['id']);
                        }
                    }
                }
            }
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

    /**
     * Validating the cart rules.
     */
    public function validateCartRules($cartRules, $inOrder = false)
    {
        foreach ($cartRules as $cartRule) {
            if (!isset($cartRule['code']) || !$cartRule['code']) {
                $this->error_msg = Tools::displayError('Invalid code for cart rule.');
                break;
            } else if (!($code = trim($cartRule['code']))
                || !Validate::isCleanHtml($code)
            ) {
                $this->error_msg = Tools::displayError('Invalid cart rule.');
                break;
            } else if ($inOrder && !Validate::isLoadedObject($objCartRule = new CartRule(CartRule::getIdByCode($code)))
                && (!isset($cartRule['value']) || !$cartRule['value'] || !isset($cartRule['type']) || !$cartRule['type'])
            ) {
                $this->error_msg = Tools::displayError('Invalid cart rule parameters.');
                break;
            } else if (!$inOrder && !Validate::isLoadedObject($objCartRule = new CartRule(CartRule::getIdByCode($code)))
                && (!isset($cartRule['type']) || $cartRule['type'] != self::API_CART_RULE_VALUE_TYPE_PERCENTAGE || $cartRule['type'] != self::API_CART_RULE_VALUE_TYPE_AMOUNT)
                && (!isset($cartRule['value']) || !$cartRule['value'] || ($cartRule['type'] == self::API_CART_RULE_VALUE_TYPE_PERCENTAGE && $cartRule['value'] > 100))
            ) {
                $this->error_msg = Tools::displayError('Invalid cart rule parameters.');
                break;
            } else if (isset($cartRule['currency'])
                && !Validate::isLoadedObject($objCurrency = new Currency(Currency::getIdByIsoCode($cartRule['currency'])))
            ) {
                $this->error_msg = Tools::displayError('Invalid currency for cart rules.');
                break;
            }
        }
    }

    /**
     * Validating the services related information.
     */
    public function validateRequestedServices($services, $idRoomType)
    {
        if ($services) {
            $objRoomTypeServiceProduct = new RoomTypeServiceProduct();
            foreach ($services as $key => $service) {
                if (isset($service['id_tax_rules_group']) && !Validate::isLoadedObject(new TaxRulesGroup((int) $service['id_tax_rules_group']))) {
                    $this->error_msg = Tools::displayError('Invalid id_tax_rules_group for services.');
                    return false;
                } else if (isset($service['total_tax']) && !Validate::isPrice($service['total_tax'])) {
                    $this->error_msg = Tools::displayError('Invalid tax for services.');
                    return false;
                } else if (isset($service['id_product'])) {
                    if (!Validate::isLoadedObject(new Product((int) $service['id_product']))) {
                        $this->error_msg = Tools::displayError('Invalid request for service.');
                        return false;
                    } else if (!$objRoomTypeServiceProduct->isRoomTypeLinkedWithProduct($idRoomType, $service['id_product'])) {
                        $this->error_msg = Tools::displayError('Service is not linked with the requested room type.');
                        return false;
                    }
                } else {
                    if (!isset($service['name'])) {
                        $this->error_msg = Tools::displayError('Service name is required for non existing services.');
                        return false;
                    } else if (!Validate::isCatalogName($service['name'])) {
                        $this->error_msg = sprintf(Tools::displayError('Invalid service name %s.'), $service['name']);
                        return false;
                    } else if (!isset($service['total_price_without_tax']) && !isset($service['unit_price_without_tax'])) {
                        $this->error_msg = sprintf(Tools::displayError('Price required for service %s.'), $service['name']);
                        return false;
                    } else if ((isset($service['total_price_without_tax']) && !Validate::isPrice($service['total_price_without_tax']))
                        || (isset($service['unit_price_without_tax']) && !Validate::isPrice($service['unit_price_without_tax']))
                    ) {
                        $this->error_msg = sprintf(Tools::displayError('Invalid price for service %s.'), $service['name']);
                        return false;
                    } else if (isset($service['quantity']) && $service['quantity'] < 1) {
                        $this->error_msg = sprintf(Tools::displayError('Invalid quantity for service %s.'), $service['name']);
                        return false;
                    } else if (isset($service['price_mode'])
                        && $service['price_mode'] != self::API_SERVICE_PRICE_MODE_PER_BOOKING
                        && $service['price_mode'] != self::API_SERVICE_PRICE_MODE_PER_DAY
                    ) {
                        $this->error_msg = sprintf(Tools::displayError('Invalid price mode for service %s.'), $service['name']);
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Validating the demands related information.
     */
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
                    && (!isset($requestedDemand['id_option']))
                ) {
                    $this->error_msg = sprintf(Tools::displayError('Id option is required for the facility with ID %s.'), $requestedDemand['id_global_demand']);
                    return false;
                } else if (isset($roomTypeDemands[$requestedDemand['id_global_demand']]['adv_option'])
                    && !isset($roomTypeDemands[$requestedDemand['id_global_demand']]['adv_option'][$requestedDemand['id_option']])
                ) {
                    $this->error_msg = sprintf(Tools::displayError('Invalid id option for the facility with ID %s.'), $requestedDemand['id_global_demand']);
                    return false;
                } else if (isset($requestedDemand['id_tax_rules_group']) && !Validate::isLoadedObject(new TaxRulesGroup((int) $requestedDemand['id_tax_rules_group']))) {
                    $this->error_msg = Tools::displayError('Invalid id_tax_rules_group for facilities.');
                    return false;
                } else if (isset($requestedDemand['total_tax']) && !Validate::isPrice($requestedDemand['total_tax'])) {
                    $this->error_msg = Tools::displayError('Invalid tax for facilities.');
                    return false;
                }
            }
        } else {
            $this->error_msg = Tools::displayError('Invalid request for facilities.');
            return false;
        }

        return true;
    }

    /**
     * Adding rooms in the cart.
     */
    public function addRoomsInCart($roomTypes)
    {
        $objRoomType = new HotelRoomType();
        $objHotelCartBookingData = new HotelCartBookingData();
        $roomUnitSelectionType = Configuration::get('PS_FRONT_ROOM_UNIT_SELECTION_TYPE');

        $quantityWise = false;
        if ($roomUnitSelectionType != HotelBookingDetail::PS_ROOM_UNIT_SELECTION_TYPE_OCCUPANCY) {
            $quantityWise = true;
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

            if (isset($roomType['rooms']) && count($roomType['rooms'])) {
                foreach ($roomType['rooms'] as $room) {
                    $roomServices = array();
                    if (isset($room['services'])
                        && $room['services']
                    ) {
                        foreach ($room['services'] as $serviceKey => $service) {
                            if (isset($service['is_new'])) {
                                if (!isset($service['price_mode'])) {
                                    $service['price_mode'] = self::API_SERVICE_PRICE_MODE_PER_BOOKING;
                                }

                                $service['id_product'] = $this->createWsService($service, $roomType['id_room_type']);
                                $serviceKey = $service['id_product'];
                                $objProduct = new Product($service['id_product']);
                                if (!isset($service['quantity']) || !$objProduct->allow_multiple_quantity) {
                                    $service['quantity'] = 1;
                                }
                            }

                            $roomServices[$serviceKey] = $service;
                        }
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

                    if ($quantityWise) {
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
                        $roomServices,
                        $this->context->cart->id,
                        $this->context->cart->id_guest
                    )) {
                        $objCartBookingData = new HotelCartBookingData((int) $idHtlCartBookingData);
                        $dateRoomJoinKey = strtotime($dateFrom).strtotime($dateTo).$objCartBookingData->id_room;
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

                        if (isset($room['unit_price_without_tax'])) {
                            $room['unit_price_without_tax'] = Tools::ps_round($room['unit_price_without_tax'], _PS_PRICE_COMPUTE_PRECISION_);
                            // need the id Room of the latest added room type
                            $this->wsFeaturePrices[] = $this->createFeaturePrice(
                                array(
                                    'id_product' => (int) $roomType['id_room_type'],
                                    'id_cart' => (int) $this->context->cart->id,
                                    'id_guest' => (int) $this->context->cart->id_guest,
                                    'date_from' => date('Y-m-d', strtotime($dateFrom)),
                                    'date_to' => date('Y-m-d', strtotime($dateTo) - _TIME_1_DAY_),
                                    'id_room' => $objCartBookingData->id_room,
                                    'price' => $room['unit_price_without_tax']
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

        $this->removeAutoAddedServicesFromCart();
    }

    public function createTaxRule($taxRate, $objAddress)
    {
        // return same rule if tax rate is same.
        foreach ($this->wsTaxRulesGroup as $idTaxRuleGroup => $taxRuleGroup) {
            if ($taxRate == $taxRuleGroup['tax_rate']) {
                return $idTaxRuleGroup;
            }
        }

        $objTax = new Tax();
        foreach (Language::getLanguages(false) as $language) {
            $objTax->name[$language['id_lang']] = $taxRate.' %';
        }

        $objTax->rate = $taxRate;
        $objTax->active = 1;
        $objTax->deleted = 1;
        $objTax->add();

        $objTaxRulesGroup = new TaxRulesGroup();
        $objTaxRulesGroup->name = $taxRate.' %';
        $objTaxRulesGroup->deleted = 1;
        $objTaxRulesGroup->active = 1;
        $objTaxRulesGroup->add();

        $objTaxRule = new TaxRule();
        $objTaxRule->id_tax = $objTax->id;
        $objTaxRule->id_tax_rules_group = (int)$objTaxRulesGroup->id;
        $objTaxRule->id_country = $objAddress->id_country;
        $objTaxRule->id_state = $objAddress->id_state;
        $objTaxRule->id_state = 0;
        $objTaxRule->add();

        $this->wsTaxRulesGroup[$objTaxRulesGroup->id]['tax'] = $objTax->id;
        $this->wsTaxRulesGroup[$objTaxRulesGroup->id]['tax_rate'] = $objTax->id;
        $this->wsTaxRulesGroup[$objTaxRulesGroup->id]['tax_rule'] = $objTaxRule->id;

        return $objTaxRulesGroup->id;
    }

    /**
     * Creating new service product for the request.
     * @return int id of new created service product
     */
    public function createWsService($service, $idRoomType)
    {
        // A single service will be created and will be deleted after the booking is completed.
        $objProduct = new Product();
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

        if (isset($service['unit_price_without_tax'])) {
            $price = $service['unit_price_without_tax'];
        } else if (isset($service['total_price_without_tax'])) {
            $price = $service['total_price_without_tax'] / $quantity;
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
        $objProduct->id_tax_rules_group = 0;
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
                    $dateRoomJoinKey = strtotime($serviceProduct['date_from']).strtotime($serviceProduct['date_to']).$serviceProduct['id_room'];
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
     * Processing the PUT request.
     */
    public function handlePutRequest($params)
    {
        $this->processCurrency($params);
        $this->processGuestDetails($params['customer_detail']);
        $this->processCustomer($params['customer_detail']);
        $objOrder = new Order((int) $params['id']);
        $objHotelBookingDetail = new HotelBookingDetail();
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
                        // if there are multiple rooms then there can be a room at index 1 so we are adding a prifix for the below condition.
                        $room_key = 'r_'.$orderRoom['id_room'];
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

        // Adding new rooms in the booking
        if (count($roomsToAdd)) {
            $this->addRoomsInOrder($objOrder->id, $roomsToAdd);
        }

        // only perform any update if request is valid.
        // Update the information for the services that were updated in the existing rooms
        if (count($roomsToUpdate)) {
            $objServiceProductOrderDetail = new ServiceProductOrderDetail();
            $objBookingDemand = new HotelBookingDemands();
            foreach ($roomsToUpdate as $roomsByDate) {
                if (isset($roomsByDate['requested'])
                    && $roomsByDate['requested']
                ) {
                    foreach ($roomsByDate['requested'] as $roomsKey => $room) {
                        $idOrder = $roomsByDate['order'][$roomsKey]['id_order'];
                        $idProduct = $roomsByDate['order'][$roomsKey]['id_product'];
                        $idRoom = $roomsByDate['order'][$roomsKey]['id_room'];
                        $dateFrom = $roomsByDate['order'][$roomsKey]['date_from'];
                        $dateTo = $roomsByDate['order'][$roomsKey]['date_to'];
                        $dateRoomJoinKey = strtotime($dateFrom).strtotime($dateTo).$idRoom;

                        if (isset($room['id_tax_rules_group']) && Validate::isLoadedObject(new TaxRulesGroup((int) $room['id_tax_rules_group']))) {
                            $this->wsRequestedRooms[$dateRoomJoinKey]['id_tax_rules_group'] = $room['id_tax_rules_group'];
                        } else if (!empty($room['total_tax'])) {
                            $this->wsRequestedRooms[$dateRoomJoinKey]['total_tax'] = $room['total_tax'];
                        }

                        $numDays = HotelHelper::getNumberOfDays($dateFrom, $dateTo);
                        if (isset($room['unit_price_without_tax'])
                            && ((float) $room['unit_price_without_tax']) != ((float) $roomsByDate['order'][$roomsKey]['total_price_tax_excl']/$numDays)
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
                        $this->addDemandsInOrderedRoom($requestedDemands, $idHotelBookingDetail);
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

        $this->updateRoomTaxRulesGroupsInOrder($objOrder->id);
        if (isset($params['remark']) && $params['remark'] && !empty(trim($params['remark']))) {
            $this->addCustomerMessage($params['id'], $params['remark']);
        }

        $cartRules = $objOrder->getCartRules();
        //Removing the stored cached object
        $this->removeOrderCartRules($objOrder->id, $cartRules);
        if (isset($params['cart_rules']) && $params['cart_rules']) {
            $this->addCartRulesToOrder($params['cart_rules'], $objOrder->id);
        }

        // Calling this after the older cart rules are removed from the order.
        $this->manageOrderPrice($objOrder->id, $params);
        if (isset($params['booking_status'])) {
            $objOrderState = false;
            if ($params['booking_status'] == self::API_BOOKING_STATUS_CANCELLED) {
                $objOrderState = new OrderState(Configuration::get('PS_OS_CANCELED'));
            } else if ($params['booking_status'] == self::API_BOOKING_STATUS_REFUNDED) {
                $objOrderState = new OrderState(Configuration::get('PS_OS_REFUND'));
            } else if ($params['booking_status'] == self::API_BOOKING_STATUS_COMPLETED) {
                $objOrderState = new OrderState(Configuration::get('PS_OS_PAYMENT_ACCEPTED'));
            } else if ($params['booking_status'] == self::API_BOOKING_STATUS_NEW) {
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
                $this->addOrderHistory($params['id'], $objOrderState);
            }
        }

        if (isset($params['price_details']['total_paid']) && $objOrder->total_paid_real != $params['price_details']['total_paid']) {
            $this->addOrderPayment($params);
        }

        $this->getBookingDetails($objOrder->id);
    }

    public function addRoomsInOrder($idOrder, $roomTypes)
    {
        $objOrder = new Order($idOrder);
        $objHotelBookingDetail = new HotelBookingDetail();
        $objRoomType = new HotelRoomType();
        $this->createCartForOrder($objOrder->id);
        $this->addRoomsInCart($roomTypes);
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
                    $objCartBookingData->save();
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
                        $dateRoomJoinKey = strtotime($objCartBookingData->date_from).strtotime($objCartBookingData->date_to).$objCartBookingData->id_room;
                        if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'])
                            && ($services = $objServiceProductCartDetail->getServiceProductsInCart(
                                $objCartBookingData->id_cart,
                                [],
                                null,
                                $objCartBookingData->id
                        ))) {
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
                                    $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group'] = $this->createTaxRule(($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_tax']/$totalPriceTaxExcl)*100, $objAddress);
                                } else if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_tax'])) {
                                    $objOrderDetail->id_tax_rules_group = 0;
                                    Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'order_detail_tax` WHERE id_order_detail='.(int)$objOrderDetail->id);
                                    $unitPriceTaxIncl = $objServiceProductOrderDetail->total_price_tax_excl + $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['total_tax'];
                                    $priceDiffTaxIncl = $unitPriceTaxIncl - $objServiceProductOrderDetail->total_price_tax_incl;
                                }

                                if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group'])) {
                                    $objTaxManager = TaxManagerFactory::getManager($objAddress, $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group']);
                                    $objTaxCalculator = $objTaxManager->getTaxCalculator();
                                    $unitPriceTaxIncl = $objTaxCalculator->addTaxes($unitPriceTaxExcl);

                                    $unitPriceTaxIncl = Tools::ps_round($unitPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                                    $totalPriceTaxIncl = Tools::ps_round(($unitPriceTaxIncl * $quantity), _PS_PRICE_COMPUTE_PRECISION_);
                                }

                                $priceDiffTaxExcl = $totalPriceTaxExcl - $totalPriceTaxExclOld;
                                $priceDiffTaxIncl = $totalPriceTaxIncl - $totalPriceTaxInclOld;
                                $objOrderDetail->total_price_tax_excl += Tools::ps_round($priceDiffTaxExcl, _PS_PRICE_COMPUTE_PRECISION_);
                                $objOrderDetail->total_price_tax_incl += Tools::ps_round($priceDiffTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);

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

                                if (!$isAutoAdded && isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group'])) {
                                    $this->saveTaxCalculator($objOrderDetail->id, $this->wsRequestedRoomTypes[$dateRoomJoinKey]['services'][$service['id_product']]['id_tax_rules_group']);
                                }

                            }

                            $objOrder->save();
                        }

                        if (isset($this->wsRequestedRoomTypes[$dateRoomJoinKey]['demands']) && $this->wsRequestedRoomTypes[$dateRoomJoinKey]['demands']) {
                            if ($demands = json_decode($this->wsRequestedRoomTypes[$dateRoomJoinKey]['demands'], true)) {
                                $this->addDemandsInOrderedRoom($demands, $objBookingDetail->id);
                            }
                        }
                    }
                }
            }
        }

        $this->deleteWsFeaturePrices();
        HotelRoomTypeFeaturePricing::deleteFeaturePrices($this->context->cart->id);
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

                                // Update OrderInvoice
                                $this->applyDiscountOnInvoice($orderInvoice, $invoiceCartRules[$orderInvoice->id]['value_tax_incl'], $invoiceCartRules[$orderInvoice->id]['value_tax_excl']);
                            }
                        } else if ($objCartRule->reduction_percent) {
                            $invoiceCartRules[$orderInvoice->id]['value_tax_incl'] = Tools::ps_round($objOrder->total_paid_tax_incl * $objCartRule->reduction_percent / 100, _PS_PRICE_COMPUTE_PRECISION_);
                            $invoiceCartRules[$orderInvoice->id]['value_tax_excl'] = Tools::ps_round($objOrder->total_paid_tax_excl * $objCartRule->reduction_percent / 100, _PS_PRICE_COMPUTE_PRECISION_);

                            $this->applyDiscountOnInvoice($orderInvoice, $invoiceCartRules[$orderInvoice->id]['value_tax_incl'], $invoiceCartRules[$orderInvoice->id]['value_tax_excl']);
                        }
                    }
                } else {
                    if ($objCartRule->reduction_percent) {
                        $invoiceCartRules[0]['value_tax_incl'] = Tools::ps_round($objOrder->total_paid_tax_incl * $objCartRule->reduction_percent / 100, _PS_PRICE_COMPUTE_PRECISION_);
                        $invoiceCartRules[0]['value_tax_excl'] = Tools::ps_round($objOrder->total_paid_tax_excl * $objCartRule->reduction_percent / 100, _PS_PRICE_COMPUTE_PRECISION_);
                    } else if ($objCartRule->reduction_amount) {
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
     * Removing room from the order.
     */
    public function removeRoomLineFromOrder($params, $roomsToRemove)
    {
        $objOrder = new Order((int) $params['id']);
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

    /**
     * Adding new custmer messages.
     */
    public function addCustomerMessage($idOrder, $remark)
    {
        $objOrder = new Order((int) $idOrder);
        $objMessage = new Message();
        $message = strip_tags($remark, '<br>');
        $saveMessage = true;
        $idCart = Cart::getCartIdByOrderId($objOrder->id);
        if ($customerMessages = Message::getMessagesByOrderId($objOrder->id, false)) {
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

    /**
     * Adding order payment from PUT requests.
     */
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

            if ($amount) {
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
     * Validating the PUT request.
     */
    public function validatePutRequest($params)
    {
        $objCustomer = new Customer();
        $this->error_msg = '';
        if (!isset($params['id']) || !$params['id']) {
            $this->error_msg = Tools::displayError('id is required with PUT requests.');
        } else if (!Validate::isLoadedObject(new Order($params['id']))) {
            $this->error_msg = Tools::displayError('Booking not found.');
        } else if (isset($params['booking_status']) && ($params['booking_status'] < 0 || $params['booking_status'] > 4)) {
            $this->error_msg = Tools::displayError('Invalid booking status.');
        } else if (!isset($params['room_types'])
            || !count($params['room_types'])
        ) {
            $this->error_msg = Tools::displayError('Rooms not found in the request.');
        } else if (isset($params['customer_detail']['id_customer'])
            && !Validate::isLoadedObject(new Customer($params['customer_detail']['id_customer']))
        )  {
            $this->error_msg = Tools::displayError('Invalid ID customer.');
        } else if (!isset($params['id_property'])
            || !Validate::isLoadedObject(new HotelBranchInformation((int) $params['id_property']))
        ) {
            $this->error_msg = Tools::displayError('Please provide a valid id_property.');
        } else if (!isset($params['customer_detail']['email'])
            || !Validate::isEmail($params['customer_detail']['email'])
            || !$objCustomer->getByEmail($params['customer_detail']['email'])
        ) {
            $this->error_msg = Tools::displayError('Customer not found.');
        } else if (isset($params['booking_status'])
            && ($params['booking_status'] < self::API_BOOKING_STATUS_NEW || $params['booking_status'] > self::API_BOOKING_STATUS_REFUNDED)
        ) {
            $this->error_msg = Tools::displayError('Invalid booking status.');
        } else if (!$this->validatePutRequestRoomTypes($params['room_types'])
            && $this->error_msg == ''
        ) {
            $this->error_msg = Tools::displayError('Requested room(s) not available');
        } else if (isset($params['cart_rules']) && $params['cart_rules']) {
            if (is_array($params['cart_rules'])) {
                $this->validateCartRules($params['cart_rules']);
            } else {
                $this->error_msg = Tools::displayError('Invalid cart rules.');
            }
        }

        if (!$this->error_msg && $this->error_msg == '') {
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
                        } else if (isset($room['id_room'])
                            && (!Validate::isLoadedObject($objHotelRoomInfomation = new HotelRoomInformation($room['id_room']))
                                || $objHotelRoomInfomation->id_product != $roomType['id_room_type']
                            )
                        ) {
                            $this->error_msg = Tools::displayError('Invalid Id room.');
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

                        if (isset($room['id_tax_rules_group']) && !Validate::isLoadedObject(new TaxRulesGroup((int) $room['id_tax_rules_group']))) {
                            $this->error_msg = Tools::displayError('Invalid id_tax_rules_group.');
                            return false;
                        } else if (!empty($room['total_tax']) && !Validate::isPrice($room['total_tax'])) {
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

    /**
     * Validating the Room types fields.
     */
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
                if ((int) $idFeaturePrice) {
                    $objFeaturePrice = new HotelRoomTypeFeaturePricing((int) $idFeaturePrice);
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

            if (isset($room['unit_price_without_tax'])) {
                $room['unit_price_without_tax'] = Tools::ps_round($room['unit_price_without_tax'], _PS_PRICE_COMPUTE_PRECISION_);
                $this->wsFeaturePrices[] = $this->createFeaturePrice(
                    array(
                        'id_product' => (int) $objHotelBookingDetail->id_product,
                        'id_cart' => (int) $objCart->id,
                        'id_guest' => (int) $objCart->id_guest,
                        'date_from' => date('Y-m-d', strtotime($objHotelBookingDetail->date_from)),
                        'date_to' => date('Y-m-d', strtotime($objHotelBookingDetail->date_to) - _TIME_1_DAY_),
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

            HotelRoomTypeFeaturePricing::deleteFeaturePrices($objCart->id);
            $this->deleteWsFeaturePrices();
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
     * Adding the service into the order.
     */
    public function addServicesInOrderedRoom($services, $idHotelBookingDetail)
    {
        if ($services) {
            $objHotelBookingDetail = new HotelBookingDetail((int) $idHotelBookingDetail);
            $objOrder = new Order($objHotelBookingDetail->id_order);
            // set context currency So that we can get prices in the order currency
            $this->context->currency = new Currency($objOrder->id_currency);
            $objHotelCartBookingData = new HotelCartBookingData();
            $objServiceProductCartDetail = new ServiceProductCartDetail();
            $roomHtlCartInfo = $objHotelCartBookingData->getRoomRowByIdProductIdRoomInDateRange(
                $objHotelBookingDetail->id_cart,
                $objHotelBookingDetail->id_product,
                $objHotelBookingDetail->date_from,
                $objHotelBookingDetail->date_to,
                $objHotelBookingDetail->id_room
            );
            $idOrderInvoice = false;
            if (($objOrderInvoice = $objOrder->getInvoicesCollection()->getFirst())
                && Validate::isLoadedObject($objOrderInvoice)
            ) {
                $idOrderInvoice = $objOrderInvoice->id;
            }

            $this->createCartForOrder($objOrder->id);
            $objCart = $this->context->cart;
            $formattedServices = array();
            foreach ($services as $serviceKey => $service) {
                if (!isset($service['id_product']) && isset($service['is_new'])) {
                    $serviceKey = $this->createWsService($service, $objHotelBookingDetail->id_product);
                    $service['id_product'] = $serviceKey;
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
                    $roomHtlCartInfo['id'])
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

                    $objOrderDetail->unit_price_tax_incl -= $unitPriceTaxIncl;
                    $objOrderDetail->unit_price_tax_excl -= $unitPriceTaxExcl;

                    $totalPriceTaxExcl = $unitPriceTaxExcl * $quantity;
                    $totalPriceTaxIncl = $unitPriceTaxIncl * $quantity;
                    if (isset($services[$product['id_product']]['unit_price_without_tax'])) {
                        $unitPriceTaxExcl = $services[$product['id_product']]['unit_price_without_tax'];
                        $unitPriceTaxIncl = $unitPriceTaxExcl * $oldTaxMultiplier;

                        $totalPriceTaxExcl = $unitPriceTaxExcl * $quantity;
                        $totalPriceTaxIncl =  $unitPriceTaxIncl * $quantity;
                    } else if (isset($services[$product['id_product']]['total_price_without_tax'])) {
                        $totalPriceTaxExcl = $services[$product['id_product']]['total_price_without_tax'];
                        $totalPriceTaxIncl = $totalPriceTaxExcl * $oldTaxMultiplier;

                        $unitPriceTaxExcl = $totalPriceTaxExcl / $quantity;
                        $unitPriceTaxIncl =  $totalPriceTaxIncl / $quantity;
                    }

                    if (!empty($services[$product['id_product']]['total_tax'])) {
                        $totalPriceTaxIncl = $totalPriceTaxExcl + $services[$product['id_product']]['total_tax'];
                        $totalPriceTaxIncl = Tools::ps_round(($totalPriceTaxIncl), _PS_PRICE_COMPUTE_PRECISION_);
                        $unitPriceTaxIncl = Tools::ps_round($totalPriceTaxIncl / $quantity, _PS_PRICE_COMPUTE_PRECISION_);
                    } else if (isset($services[$product['id_product']]['total_tax'])) {
                        $objOrderDetail->id_tax_rules_group = 0;
                        Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'order_detail_tax` WHERE id_order_detail='.(int)$objOrderDetail->id);
                        $unitPriceTaxIncl = $unitPriceTaxExcl + $services[$product['id_product']]['total_tax'];
                        $totalPriceTaxIncl = Tools::ps_round(($unitPriceTaxIncl * $quantity), _PS_PRICE_COMPUTE_PRECISION_);
                    }

                    $objAddress = new Address((int) $objOrder->id_address_tax);
                    if (!empty($services[$product['id_product']]['total_tax'])) {
                        $services[$product['id_product']]['id_tax_rules_group'] = $this->createTaxRule(($services[$product['id_product']]['total_tax']/$totalPriceTaxExcl)*100, $objAddress);
                    }

                    if (isset($services[$product['id_product']]['id_tax_rules_group'])) {
                        $objTaxManager = TaxManagerFactory::getManager($objAddress, $services[$product['id_product']]['id_tax_rules_group']);
                        $objTaxCalculator = $objTaxManager->getTaxCalculator();
                        $unitPriceTaxIncl = $objTaxCalculator->addTaxes($unitPriceTaxExcl);

                        $unitPriceTaxIncl = Tools::ps_round($unitPriceTaxIncl, _PS_PRICE_COMPUTE_PRECISION_);
                        $totalPriceTaxIncl = Tools::ps_round(($unitPriceTaxIncl * $quantity), _PS_PRICE_COMPUTE_PRECISION_);
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

                    $objOrderDetail->unit_price_tax_incl = $unitPriceTaxIncl;
                    $objOrderDetail->total_price_tax_incl = $unitPriceTaxIncl * $objServiceProductOrderDetail->quantity;
                    $objOrderDetail->unit_price_tax_excl = $unitPriceTaxExcl;
                    $objOrderDetail->total_price_tax_excl = $unitPriceTaxExcl * $objServiceProductOrderDetail->quantity;

                    $isAutoAdded = false;
                    if ($objOrderDetail->product_auto_add && $objOrderDetail->product_price_addition_type == Product::PRICE_ADDITION_TYPE_WITH_ROOM) {
                        $isAutoAdded = true;
                    }

                    $objOrderDetail->update();
                    if (!$isAutoAdded && isset($services[$product['id_product']]['id_tax_rules_group'])) {
                        $this->saveTaxCalculator($objOrderDetail->id, $services[$product['id_product']]['id_tax_rules_group']);
                    }
                }
            }


            $objOrder->total_discounts += (float)abs($objCart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
            $objOrder->total_discounts_tax_excl += (float)abs($objCart->getOrderTotal(false, Cart::ONLY_DISCOUNTS));
            $objOrder->total_discounts_tax_incl += (float)abs($objCart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
            $objOrder->update();
        }
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

            $params['associations']['customer_detail'] = $customerDetails;
            $params['associations']['price_details'] = $priceDetails;
            $params['associations']['cart_rules'] = $orderCartRules;
            $roomTypeInfo = array();
            if (Group::getPriceDisplayMethod($objCustomer->id_default_group) == PS_TAX_INC) {
                $useTax = 1;
            }

            $params['associations']['remarks'] = array();
            if ($customerMessages = Message::getMessagesByOrderId($objOrder->id)) {
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
                            $demand['unit_price_without_tax'] = Tools::ps_round($extraDemand['unit_price_tax_excl'], _PS_PRICE_COMPUTE_PRECISION_);
                            $demand['total_tax'] = Tools::ps_round(($extraDemand['unit_price_tax_incl']- $extraDemand['unit_price_tax_excl']), _PS_PRICE_COMPUTE_PRECISION_);

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

                    if ($additionalServices = $objServiceProductOrderDetail->getRoomTypeServiceProducts(
                        $orderData['id_order'],
                        0,
                        0,
                        $orderData['id_product'],
                        $orderData['date_from'],
                        $orderData['date_to'],
                        $orderData['id_room'],
                        0,
                        $useTax,
                        null
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
            $this->output['booking'] = $params;
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

    public function renderResponse()
    {
        if ($this->outputType == 'json') {
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

    /**
     * Used to render output in XML using array.
     */
    public function renderXmlOutputUsingArray($response, $keyToIgnore = array(), $parentKeys = array(), $parentKey = '', $useEmpty = false)
    {
        $output = '';
        foreach ($response as $key => $res) {
            if (in_array($key, $keyToIgnore) && $key) {
                continue;
            }

            if (gettype($key) == 'integer' && isset($parentKeys[$parentKey])) {
                $key = $parentKeys[$parentKey];
            }

            if (is_array($res) && count($res)) {
                $output .= $this->renderHeader($key);
                $output .= $this->renderXmlOutputUsingArray($res, $keyToIgnore, $parentKeys, $key, $useEmpty);
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
}
