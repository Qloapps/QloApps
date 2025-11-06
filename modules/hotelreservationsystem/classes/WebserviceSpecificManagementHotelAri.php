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

class WebserviceSpecificManagementHotelAri extends ObjectModel implements WebserviceSpecificManagementInterface
{
    // fields for the object
    public $id_room_type;
    public $id_hotel;
    public $date_from;
    public $date_to;
    public $get_available_rooms;
    public $get_booked_rooms;
    public $get_partial_available_rooms;
    public $get_unavailable_rooms;
    public $date_wise_breakdown;

    /** @var WebserviceOutputBuilder */
    protected $objOutput;

    /** @var WebserviceRequest */
    protected $wsObject;

    protected $output = '';

    public static $definition = array(
        'table' => 'htl_booking_detail',
        'primary' => 'id_hotel',
        'fields' => array(
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'id_room_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_from' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'date_to' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'get_available_rooms' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'get_booked_rooms' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'get_partial_available_rooms' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'get_unavailable_rooms' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_wise_breakdown' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'hotel_aris',
        'objectNodeName' => 'hotel_ari',
        'fields' => array(
            'id_hotel' => array(
                'xlink_resource' => array(
                    'resourceName' => 'hotels',
                )
            ),
            'id_room_type' => array(
                'xlink_resource' => array(
                    'resourceName' => 'room_types',
                )
            ),
        ),
        'associations' => array(
            'room_occupancies' => array(
                'resource' => 'room_occupancy',
                'fields' => array(
                    'adults' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
                    'children' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
                ),
            ),
        ),
    );

    /* ------------------------------------------------
     * GETTERS & SETTERS
     * ------------------------------------------------ */

    /**
     * @param WebserviceOutputBuilderCore $obj
     * @return WebserviceSpecificManagementInterface
     */
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
        // create object of the class to create schema of the api
        if (isset($this->wsObject->urlFragments['schema'])) {
            if ($this->wsObject->method == 'GET') {
                $objAriWebserviceManage = new WebserviceSpecificManagementHotelAri();

                $this->wsObject->objects = [];
                $this->wsObject->objects[] = $objAriWebserviceManage;
                $this->wsObject->objects['empty'] = $objAriWebserviceManage;

                // If a schema is asked the view must be in details type
                $typeOfView = WebserviceOutputBuilder::VIEW_DETAILS;

                if ($this->wsObject->urlFragments['schema'] == 'blank' || $this->wsObject->urlFragments['schema'] == 'synopsis') {
                    $this->wsObject->schemaToDisplay = $this->wsObject->urlFragments['schema'];
                } else {
                    $this->wsObject->setError(400, 'Please select a schema of type \'synopsis\' to get the whole schema informations (which fields are required, which kind of content...) or \'blank\' to get an empty schema to fill before using POST request', 28);
                    return false;
                }

                $this->output .= $this->objOutput->getContent(
                    $this->wsObject->objects,
                    $this->wsObject->schemaToDisplay,
                    $this->wsObject->fieldsToDisplay,
                    $this->wsObject->depth,
                    $typeOfView,
                    false
                );

                $this->output = $this->objOutput->getObjectRender()->overrideContent($this->output);
            } else {
                $this->wsObject->setError(405, 'Method '.$this->wsObject->method.' is not valid', 23);
            }
        } else {
            if ($this->wsObject->method == 'POST') {
                // If request comes of ari data then send data as per the ari serach request
                try {
                    // get what is sent from post request xml request
                    $inputXml = '';
                    $postResource = fopen("php://input", "r");
                    while ($postData = fread($postResource, 1024)) {
                        $inputXml .= $postData;
                    }
                    fclose($postResource);

                    if (isset($inputXml) && strncmp($inputXml, 'xml=', 4) == 0) {
                        // Now $inputXml has the post request XML.
                        $inputXml = substr($inputXml, 4);
                    }

                    // All validations are passed. lets return the response of the request
                    if (($retValidateFields = $this->validateRequestXml($inputXml))) {
                        $simpleXMLObj = new SimpleXMLElement($inputXml);
                        $xmlEntities = $simpleXMLObj->children();
                        $ariParams = json_decode(json_encode($xmlEntities), true);
                        $ariParams = $ariParams['hotel_ari'];

                        // Set request array for sending to the function which returns ari info as per the request
                        $bookingParams = array();
                        $bookingParams['date_from'] = date('Y-m-d', strtotime($ariParams['date_from']));
                        $bookingParams['date_to'] = date('Y-m-d', strtotime($ariParams['date_to']));
                        $bookingParams['hotel_id'] = $ariParams['id_hotel'];

                        if (isset($ariParams['id_room_type'])) {
                            $bookingParams['id_room_type'] = $ariParams['id_room_type'];
                        }

                        if (isset($ariParams['get_available_rooms'])
                            || isset($ariParams['get_booked_rooms'])
                            || isset($ariParams['get_partial_available_rooms'])
                            || isset($ariParams['get_unavailable_rooms'])
                        ) {
                            if (isset($ariParams['get_available_rooms']) && $ariParams['get_available_rooms']) {
                                $bookingParams['search_available'] = 1;
                            } else {
                                $bookingParams['search_available'] = 0;
                            }

                            if (isset($ariParams['get_booked_rooms']) && $ariParams['get_booked_rooms']) {
                                $bookingParams['search_booked'] = 1;
                            } else {
                                $bookingParams['search_booked'] = 0;
                            }

                            if (isset($ariParams['get_partial_available_rooms']) && $ariParams['get_partial_available_rooms']) {
                                $bookingParams['search_partial'] = 1;
                            } else {
                                $bookingParams['search_partial'] = 0;
                            }

                            if (isset($ariParams['get_unavailable_rooms']) && $ariParams['get_unavailable_rooms']) {
                                $bookingParams['search_unavai'] = 1;
                            } else {
                                $bookingParams['search_unavai'] = 0;
                            }
                        } else {
                            // if no search type is provided then we will send available rooms
                            $bookingParams['search_available'] = 1;
                            $bookingParams['search_booked'] = 0;
                            $bookingParams['search_partial'] = 0;
                            $bookingParams['search_unavai'] = 0;
                        }

                        $totalRooms = 1;
                        if (isset($ariParams['associations']['room_occupancies']['room_occupancy'])) {
                            if (isset($ariParams['associations']['room_occupancies']['room_occupancy']['adults'])) {
                                $ariParams['associations']['room_occupancies']['room_occupancy'] = array($ariParams['associations']['room_occupancies']['room_occupancy']);
                            }
                            $bookingParams['room_occupancy'] = $ariParams['associations']['room_occupancies']['room_occupancy'];

                            $totalRooms = count($bookingParams['room_occupancy']);
                        }

                        // now call the function
                        $objCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
                        $objBookingDtl = new HotelBookingDetail();
                        $searchAriData = [];

                        // If api is called for getting ari info for all the dates in the provided date range
                        if (isset($ariParams['date_wise_breakdown']) && $ariParams['date_wise_breakdown']) {
                            $dateWiseBreakdown = 1;

                            /**
                             * To generate data for each day, we will need to get data for booked and unavailable dates, s
                             * so saving the booking_params for generating the response later
                             */
                            $getavail = $bookingParams['search_available'];
                            $getBooked = $bookingParams['search_booked'];
                            $getUnavai = $bookingParams['search_unavai'];
                            $bookingParams['search_available'] = 1;
                            $bookingParams['search_booked'] = 1;
                            $bookingParams['search_unavai'] = 1;
                            $bookingData = $objBookingDtl->getBookingData($bookingParams);

                            // saving date wise booked and unavailable rooms to manage available rooms calculation .
                            $bookedDates = array();
                            $unavailableDates = array();
                            if (isset($bookingData['rm_data']) && $bookingData['rm_data']) {
                                foreach ($bookingData['rm_data'] as $key => $roomType) {
                                    if ($rooms = HotelRoomInformation::getHotelRoomsInfo($ariParams['id_hotel'], $roomType['id_product'])) {
                                        foreach ($rooms as $room) {
                                            $bookingData['rm_data'][$key]['all_rooms'][$room['id']] = array('id_room' => $room['id'], 'room_num' => $room['room_num']);
                                        }
                                        if (count($roomType['data']['booked'])) {
                                            foreach ($roomType['data']['booked'] as $bookedRoom) {
                                                foreach ($bookedRoom['detail'] as $bookDetail) {
                                                    for ($currentDate = date('Y-m-d', strtotime($bookDetail['date_from']));
                                                        $currentDate < date('Y-m-d', strtotime($bookDetail['date_to']));
                                                        $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)))
                                                    ) {
                                                        $bookedDates[$currentDate][$roomType['id_product']][$bookedRoom['id_room']] = $bookedRoom['id_room'];
                                                    }
                                                }
                                            }
                                        }
                                        if (count($roomType['data']['unavailable'])) {
                                            foreach ($roomType['data']['unavailable'] as $unavailableRooms) {
                                                foreach ($unavailableRooms['detail'] as $unavailableDetail) {
                                                    if ($unavailableDetail['date_from'] && $unavailableDetail['date_to']) {
                                                        $startDate = date('Y-m-d', strtotime($unavailableDetail['date_from']));
                                                        $endDate = date('Y-m-d', strtotime($unavailableDetail['date_to']));
                                                    } else {
                                                        $startDate = $bookingParams['date_from'];
                                                        $endDate = $bookingParams['date_to'];
                                                    }
                                                    for ($currentDate = $startDate;
                                                        $currentDate < $endDate;
                                                        $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)))
                                                    ) {
                                                        $unavailableDates[$currentDate][$roomType['id_product']][$unavailableRooms['id_room']] = $unavailableRooms['id_room'];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            // creating ari details for every date
                            for ($currentDate = $bookingParams['date_from']; $currentDate < $bookingParams['date_to']; $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)))) {
                                $nextDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));
                                // append request fields also. will be attached in the response xml
                                $ariDateInfo['id_hotel'] = $ariParams['id_hotel'];
                                $ariDateInfo['date_from'] = $currentDate;
                                $ariDateInfo['date_to'] = $nextDate;
                                $ariDateInfo['currency'] = $objCurrency->iso_code;
                                if (isset($bookingData['rm_data']) && $bookingData['rm_data']) {
                                    $ariDateInfo['total_rooms'] = 0;
                                    $ariDateInfo['room_types'] = array();
                                    if ($getavail) {
                                        $ariDateInfo['total_available_rooms'] = 0;
                                    }
                                    if ($getUnavai) {
                                        $ariDateInfo['total_unavailable_rooms'] = 0;
                                    }
                                    if ($getBooked) {
                                        $ariDateInfo['total_booked_rooms'] = 0;
                                    }
                                    foreach ($bookingData['rm_data'] as $roomType)
                                    {
                                        if (count($roomType['all_rooms'])) {
                                            $ariDateInfo['total_rooms'] += count($roomType['all_rooms']);
                                            $roomDetail = array();

                                            $roomDetail['available'] = $roomType['all_rooms'];
                                            if (isset($bookedDates[$currentDate][$roomType['id_product']])) {
                                                $roomDetail['available'] = array_diff_key($roomDetail['available'], $bookedDates[$currentDate][$roomType['id_product']]);
                                                    $roomDetail['booked'] = array_intersect_key($roomType['all_rooms'], $bookedDates[$currentDate][$roomType['id_product']]);
                                            } else {
                                                    $roomDetail['booked'] = array();
                                            }
                                            if (isset($unavailableDates[$currentDate][$roomType['id_product']])) {
                                                $roomDetail['available'] = array_diff_key($roomDetail['available'], $unavailableDates[$currentDate][$roomType['id_product']]);
                                                    $roomDetail['unavailable'] = array_intersect_key($roomType['all_rooms'], $unavailableDates[$currentDate][$roomType['id_product']]);
                                            } else {
                                                    $roomDetail['unavailable'] = array();
                                            }
                                            if ($getavail) {
                                                $ariDateInfo['total_available_rooms'] += count($roomDetail['available']);
                                            } else {
                                                unset($roomDetail['available']);
                                            }
                                            if ($getUnavai) {
                                                $ariDateInfo['total_unavailable_rooms'] += count($roomDetail['unavailable']);
                                            } else {
                                                unset($roomDetail['unavailable']);
                                            }
                                            if ($getBooked) {
                                                $ariDateInfo['total_booked_rooms'] += count($roomDetail['booked']);
                                            } else {
                                                unset($roomDetail['booked']);
                                            }
                                        }

                                        $roomTypePriceTE = HotelRoomTypeFeaturePricing::getRoomTypeFeaturePricesPerDay(
                                            $roomType['id_product'],
                                            $currentDate,
                                            date('Y-m-d H:i:s', strtotime('+1 day', strtotime($currentDate))),
                                            0
                                        );
                                        $roomTypePriceTI = HotelRoomTypeFeaturePricing::getRoomTypeFeaturePricesPerDay(
                                            $roomType['id_product'],
                                            $currentDate,
                                            date('Y-m-d H:i:s', strtotime('+1 day', strtotime($currentDate))),
                                            1
                                        );
                                        $totalBookingPrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                                            $roomType['id_product'],
                                            $currentDate,
                                            date('Y-m-d H:i:s', strtotime('+1 day', strtotime($currentDate))),
                                            $totalRooms
                                        );
                                        $ariDateInfo['room_types'][] = array(
                                            'id_room_type' => $roomType['id_product'],
                                            'base_price' => $roomTypePriceTE,
                                            'base_price_with_tax' => $roomTypePriceTI,
                                            'total_price' => $totalBookingPrice['total_price_tax_excl'],
                                            'total_price_with_tax' => $totalBookingPrice['total_price_tax_incl'],
                                            'name' => $roomType['name'],
                                            'rooms' => $roomDetail
                                        );
                                    }
                                }
                                $searchAriData[] = $ariDateInfo;
                            }
                        } else { // If api is called for getting ari info for a particular date range
                            $dateWiseBreakdown = 0;

                            // If api is called for getting ari info for a particular date range
                            $bookingData = $objBookingDtl->getBookingData($bookingParams);
                            // append request fields also. will be attached in the response xml
                            $ariInfo = array();
                            $ariInfo['id_hotel'] = $ariParams['id_hotel'];
                            $ariInfo['date_from'] = $bookingParams['date_from'];
                            $ariInfo['date_to'] = $bookingParams['date_to'];
                            $ariInfo['currency'] = $objCurrency->iso_code;
                            if (isset($bookingData['rm_data']) && $bookingData['rm_data']) {
                                if (isset($bookingData['stats']['total_rooms'])) {
                                    $ariInfo['total_rooms'] = $bookingData['stats']['total_rooms'];
                                }
                                if (isset($bookingData['stats']['num_avail'])) {
                                    $ariInfo['total_available_rooms'] = $bookingData['stats']['num_avail'];
                                }
                                if (isset($bookingData['stats']['num_unavail'])) {
                                    $ariInfo['total_unavailable_rooms'] = $bookingData['stats']['num_unavail'];
                                }
                                if (isset($bookingData['stats']['num_part_avai'])) {
                                    $ariInfo['total_partial_available_rooms'] = $bookingData['stats']['num_part_avai'];
                                }
                                if (isset($bookingData['stats']['num_booked'])) {
                                    $ariInfo['total_booked_rooms'] = $bookingData['stats']['num_booked'];
                                }
                                $ariInfo['room_types'] = array();
                                foreach ($bookingData['rm_data'] as $roomType) {
                                    $roomTypePriceTE = HotelRoomTypeFeaturePricing::getRoomTypeFeaturePricesPerDay(
                                        $roomType['id_product'],
                                        $bookingParams['date_from'],
                                        $bookingParams['date_to'],
                                        0
                                    );
                                    $roomTypePriceTI = HotelRoomTypeFeaturePricing::getRoomTypeFeaturePricesPerDay(
                                        $roomType['id_product'],
                                        $bookingParams['date_from'],
                                        $bookingParams['date_to'],
                                        1
                                    );

                                    $totalBookingPrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                                        $roomType['id_product'],
                                        $bookingParams['date_from'],
                                        $bookingParams['date_to'],
                                        $totalRooms
                                    );
                                    $ariInfo['room_types'][] = array(
                                        'id_room_type' => $roomType['id_product'],
                                        'base_price' => $roomTypePriceTE,
                                        'base_price_with_tax' => $roomTypePriceTI,
                                        'total_price' => $totalBookingPrice['total_price_tax_excl'],
                                        'total_price_with_tax' => $totalBookingPrice['total_price_tax_incl'],
                                        'name' => $roomType['name'],
                                        'rooms' => $roomType['data']
                                    );
                                }
                            }
                            $searchAriData[] = $ariInfo;
                        }

                        // We have to create the json and xml response for request by ourself. So we need to check if data to be sent in xml or json
                        // We have no way to check the output format from parent classed. So we used below code
                        if (get_class($this->objOutput->getObjectRender()) == 'WebserviceOutputJSON') {
                            $this->getResponseJson($searchAriData, $dateWiseBreakdown);
                        } else {
                            $this->getResponseXml($searchAriData, $dateWiseBreakdown);
                        }
                    }

                    return false;

                } catch (Exception $error) {
                    $this->wsObject->setError(500, 'XML error : '.$error->getMessage()."\n".'XML length : '.strlen($inputXml)."\n".'Original XML : '.$inputXml, 127);

                    return false;
                }
            } else {
                $this->wsObject->setError(405, 'Method '.$this->wsObject->method.' is not valid', 23);
            }
        }
    }

    // return the response of the api. So return the variable in which we are creating final response for the api request
    public function getContent()
    {
        return $this->output;
    }

    // We have overrided this function for unseting the id field set from ObjectModel::getWebserviceParameters()
    public function getWebserviceParameters($ws_params_attribute_name = null)
    {
        $resource_parameters = parent::getWebserviceParameters($ws_params_attribute_name);
        unset($resource_parameters['fields']['id']);
        return $resource_parameters;
    }

    // This function fully validated the request data(xml)
    private function validateRequestXml($inputXml)
    {
        // check if xml is present in the request
        if ($inputXml) {
            $isValidXml = simplexml_load_string($inputXml);
            // check if xml is valid in the request
            if ($isValidXml) {
                // lets check if valid xml or not. if xml is not valid then it will be in catch block of calling code and error will be thrown
                $simpleXMLObj = new SimpleXMLElement($inputXml);

                /** @var SimpleXMLElement|Countable $xmlEntities */
                $xmlEntities = $simpleXMLObj->children();
                $resourceConfiguration = $this->getWebserviceParameters();
                /** @var ObjectModel $object */
                $retrieveData = $resourceConfiguration['retrieveData'];
                $object = new $retrieveData['className']();

                // Through below validations checks, we are checking if required fields in request xml are present or not
                foreach ($xmlEntities as $xmlEntity) {
                    /** @var SimpleXMLElement $xmlEntity */
                    $attributes = $xmlEntity->children();

                    foreach ($resourceConfiguration['fields'] as $fieldName => $fieldProperties) {
                        $sqlId = $fieldProperties['sqlId'];
                        if (isset($attributes->$fieldName) && isset($fieldProperties['sqlId']) && (!isset($fieldProperties['i18n']) || !$fieldProperties['i18n'])) {
                            if (isset($fieldProperties['setter'])) {
                                // if we have to use a specific setter
                                if (!$fieldProperties['setter']) {
                                    // if it's forbidden to set this field
                                    $this->wsObject->setError(400, 'parameter "'.$fieldName.'" not writable. Please remove this attribute of this XML', 93);
                                    return false;
                                } else {
                                    $setter = $fieldProperties['setter'];
                                    $object->$setter((string)$attributes->$fieldName);
                                }
                            } elseif (property_exists($object, $sqlId)) {
                                $object->$sqlId = (string)$attributes->$fieldName;
                            } else {
                                $this->wsObject->setError(400, 'Parameter "'.$fieldName.'" can\'t be set to the object "'.$this->resourceConfiguration['retrieveData']['className'].'"', 123);
                                return false;
                            }
                        } elseif (isset($fieldProperties['required']) && $fieldProperties['required'] && !$fieldProperties['i18n']) {
                            $this->wsObject->setError(400, 'parameter "'.$fieldName.'" required', 41);
                            return false;
                        }
                    }
                }

                // Through below validations checks, we are checking if fields are satifying the fields definition of the class
                if (($retValidateFields = $object->validateFields(false, true)) !== true) {
                    $this->wsObject->setError(400, 'Validation error: "'.$retValidateFields.'"', 85);
                    return false;
                }

                // Get array form the xml request data
                $ariParams = json_decode(json_encode($xmlEntities), true);
                if (isset($ariParams['hotel_ari']) && $ariParams['hotel_ari']) {
                    $ariParams = $ariParams['hotel_ari'];

                    // Below are custom validations we need other then required and data-types validations. e.g. (date from must be before date to)

                    // date to must be after date from
                    if (strtotime($ariParams['date_to']) <= strtotime($ariParams['date_from'])) {
                        $this->wsObject->setError(400, 'Validation error: "Date to" must be after "date from"', 85);
                        return false;
                    }


                    // validate all the information of sent rooms occupancies
                    if (isset($ariParams['associations']['room_occupancies']['room_occupancy']) && $ariParams['associations']['room_occupancies']['room_occupancy']) {
                        if (isset($ariParams['associations']['room_occupancies']['room_occupancy']['adults'])) {
                            $ariParams['associations']['room_occupancies']['room_occupancy'] = array($ariParams['associations']['room_occupancies']['room_occupancy']);
                        }

                        $occupancies = $ariParams['associations']['room_occupancies']['room_occupancy'];
                        foreach ($occupancies as $key => $occupancy) {
                            if (isset($occupancy['adults'])) {
                                if (!$occupancy['adults'] || !Validate::isUnsignedInt($occupancy['adults'])) {
                                    $this->wsObject->setError(400, 'Validation error: Invalid value for number of adults for Room-'.($key+1).' occupancy(value must be greater than 0).', 85);
                                    return false;
                                }
                            } else {
                                $this->wsObject->setError(400, 'Validation error: Missing information for adults for Room-'.($key+1).' occupancy.', 85);
                                return false;
                            }

                            if (isset($occupancy['children'])) {
                                if (!Validate::isUnsignedInt($occupancy['children'])) {
                                    $this->wsObject->setError(400, 'Validation error: Invalid value for number of children for Room-'.($key+1).' occupancy.', 85);
                                    return false;
                                }
                            } else {
                                $this->wsObject->setError(400, 'Validation error: Missing information for children for Room-'.($key+1).' occupancy.', 85);
                                return false;
                            }
                        }
                    }
                } else {
                    $this->wsObject->setError(400, 'Validation error: Invalid request XML', 85);
                    return false;
                }
            } else {
                $this->wsObject->setError(400, 'Validation error: Invalid ARI search parameters found.', 85);
                return false;
            }
        } else {
            $this->wsObject->setError(400, 'Validation error: ARI search parameters are missing.', 85);
            return false;
        }

        return true;
    }

    // ObjectModel::validateField() is sending error with object name.
    // By overriding, we are sending $human_errors = true for more descriptive error strings
    public function validateField($field, $value, $id_lang = null, $skip = array(), $human_errors = false)
    {
        return parent::validateField($field, $value, $id_lang, $skip, true);
    }

    // create xml for the response of the ari request
    private function getResponseXml($ariInfoArray, $dateWiseBreakdown = 0)
    {
        if ($ariInfoArray) {
            // if date wise breakup is requested the parent node will be 'hotel_aris' as per other APIs in QloApps (orders > order)
            if ($dateWiseBreakdown) {
                $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('hotel_aris', array());
            }
            foreach ($ariInfoArray as $ariInfo) {
                $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('hotel_ari', array());

                $field = array('sqlId' => 'id_hotel', 'value' => $ariInfo['id_hotel'], 'xlink_resource' => 'hotels');
                $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                $field = array('sqlId' => 'date_from', 'value' => $ariInfo['date_from']);
                $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                $field = array('sqlId' => 'date_to', 'value' => $ariInfo['date_to']);
                $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                $field = array('sqlId' => 'currency', 'value' => $ariInfo['currency']);
                $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                if (isset($ariInfo['total_rooms'])) {
                    $field = array('sqlId' => 'total_rooms', 'value' => $ariInfo['total_rooms']);
                    $this->output .= $this->objOutput->getObjectRender()->renderField($field);
                }

                if (isset($ariInfo['total_available_rooms'])) {
                    $field = array('sqlId' => 'total_available_rooms', 'value' => $ariInfo['total_available_rooms']);
                    $this->output .= $this->objOutput->getObjectRender()->renderField($field);
                }

                if (isset($ariInfo['total_unavailable_rooms'])) {
                    $field = array('sqlId' => 'total_unavailable_rooms', 'value' => $ariInfo['total_unavailable_rooms']);
                    $this->output .= $this->objOutput->getObjectRender()->renderField($field);
                }

                if (isset($ariInfo['total_partial_available_rooms'])) {
                    $field = array('sqlId' => 'total_partial_available_rooms', 'value' => $ariInfo['total_partial_available_rooms']);
                    $this->output .= $this->objOutput->getObjectRender()->renderField($field);
                }

                if (isset($ariInfo['total_booked_rooms'])) {
                    $field = array('sqlId' => 'total_booked_rooms', 'value' => $ariInfo['total_booked_rooms']);
                    $this->output .= $this->objOutput->getObjectRender()->renderField($field);
                }

                // check if room type data is available then only process further
                if (isset($ariInfo['room_types']) && $ariInfo['room_types']) {
                    $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('room_types', array());

                    foreach ($ariInfo['room_types'] as $roomTypeInfo) {
                        $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('room_type', array(), array(
                            'id' => $roomTypeInfo['id_room_type'],
                            'xlink_resource' => $this->wsObject->wsUrl.'room_types'.'/'.$roomTypeInfo['id_room_type']
                        ));

                        $field = array('sqlId' => 'id_room_type', 'value' => $roomTypeInfo['id_room_type'], 'xlink_resource' => 'room_types');
                        $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                        $field = array('sqlId' => 'base_price', 'value' => $roomTypeInfo['base_price']);
                        $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                        $field = array('sqlId' => 'base_price_with_tax', 'value' => $roomTypeInfo['base_price_with_tax']);
                        $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                        $field = array('sqlId' => 'total_price', 'value' => $roomTypeInfo['total_price']);
                        $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                        $field = array('sqlId' => 'total_price_with_tax', 'value' => $roomTypeInfo['total_price_with_tax']);
                        $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                        $objRoomType = new Product($roomTypeInfo['id_room_type']);
                        $field = array('sqlId' => 'name', 'value' => $objRoomType->name, 'i18n' => true);
                        $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                        // rooms info of the room type
                        if (isset($roomTypeInfo['rooms']) && $roomTypeInfo['rooms']) {
                            $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('rooms', array());
                            foreach ($roomTypeInfo['rooms'] as $key => $roomsInfo) {
                                if ($key == 'available') {
                                    $nodeRoomAvailability = 'available';
                                } elseif ($key == 'unavailable') {
                                    $nodeRoomAvailability = 'unavailable';
                                } elseif ($key == 'booked') {
                                    $nodeRoomAvailability = 'booked';
                                } elseif ($key == 'partially_available') {
                                    $nodeRoomAvailability = 'partial_available';
                                }

                                $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader($nodeRoomAvailability, array());
                                if ($key == 'partially_available') {
                                    foreach ($roomsInfo as $dateIndex => $partialRoomsInfo) {
                                        if (isset($partialRoomsInfo['rooms']) && $partialRoomsInfo['rooms']) {
                                            $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('rooms', array(), array('date_from' => $partialRoomsInfo['date_from'], 'date_to' => $partialRoomsInfo['date_to']));

                                            foreach ($partialRoomsInfo['rooms'] as $roomInfo) {
                                                $roomDetail = array();
                                                $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('room', array(), array('id' => $roomInfo['id_room']));

                                                $field = array('sqlId' => 'id_room', 'value' => $roomInfo['id_room']);
                                                $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                                                $field = array('sqlId' => 'room_number', 'value' => $roomInfo['room_num']);
                                                $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                                                $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('room', array());
                                            }

                                            $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('rooms', array());
                                        }
                                    }
                                } else {
                                    if ($roomsInfo) {
                                        foreach ($roomsInfo as $roomInfo) {
                                            $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('room', array(), array('id' => $roomInfo['id_room']));

                                            $field = array('sqlId' => 'id_room', 'value' => $roomInfo['id_room']);
                                            $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                                            $field = array('sqlId' => 'room_number', 'value' => $roomInfo['room_num']);
                                            $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                                            $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('room', array());
                                        }
                                    }
                                }

                                $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter($nodeRoomAvailability, array());
                            }

                            $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('rooms', array());
                        }

                        $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('room_type', array());
                    }

                    $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('room_types', array());
                }

                $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('hotel_ari', array());
            }

            // if date wise breakup is requested the parent node will be 'hotel_aris' as per other APIs in QloApps (orders > order)
            if ($dateWiseBreakdown) {
                $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('hotel_aris', array());
            }
        }

        // wrap with the common parent header of QloApps xml api responses
        $this->output = $this->objOutput->getObjectRender()->overrideContent($this->output);

        return $this->output;
    }

    // create json for the response of the ari request
    private function getResponseJson($ariInfoArray, $dateWiseBreakdown = 0)
    {
        $ariReponse = [];
        if ($dateWiseBreakdown) {
            // if date wise breakup is requested the parent node will be 'hotel_aris' as per other APIs in QloApps (orders > order)
            $ariReponse['hotel_aris'] = array_values($ariInfoArray);
        } else {
            $ariReponse['hotel_ari'] = $ariInfoArray[0];
        }

        $this->output .= json_encode($ariReponse);
        $this->output = preg_replace_callback("/\\\\u([a-f0-9]{4})/", function ($matches) {
            return iconv('UCS-4LE','UTF-8', pack('V', hexdec('U' . $matches[1])));
        }, $this->output);

        return $this->output;
    }
}
