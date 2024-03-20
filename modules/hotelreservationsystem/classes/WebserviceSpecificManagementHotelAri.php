<?php
/**
* 2010-2023 Webkul.
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
*  @copyright 2010-2023 Webkul IN
*  @license   https://store.webkul.com/license.html
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
                        $obBookingDtl = new HotelBookingDetail();
                        $ariInfo = $obBookingDtl->getBookingData($bookingParams);

                        // append request fields also. will be attached in the response xml
                        $ariInfo['id_hotel'] = $ariParams['id_hotel'];
                        $ariInfo['date_from'] = $bookingParams['date_from'];
                        $ariInfo['date_to'] = $bookingParams['date_to'];
                        $ariInfo['num_rooms'] = $totalRooms;

                        // We have to create the json and xml response for request by ourself. So we need to check if data to be sent in xml or json
                        // We have no way to check the output format from parent classed. So we used below code
                        if (get_class($this->objOutput->getObjectRender()) == 'WebserviceOutputJSON') {
                            $this->getResponseJson($ariInfo);
                        } else {
                            $this->getResponseXml($ariInfo);
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
    private function getResponseXml($ariInfo)
    {
        $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('hotel_ari', array());

        $field = array('sqlId' => 'id_hotel', 'value' => $ariInfo['id_hotel'], 'xlink_resource' => 'hotels');
        $this->output .= $this->objOutput->getObjectRender()->renderField($field);

        $field = array('sqlId' => 'date_from', 'value' => $ariInfo['date_from']);
        $this->output .= $this->objOutput->getObjectRender()->renderField($field);

        $field = array('sqlId' => 'date_to', 'value' => $ariInfo['date_to']);
        $this->output .= $this->objOutput->getObjectRender()->renderField($field);

        $objCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $field = array('sqlId' => 'currency', 'value' => $objCurrency->iso_code);
        $this->output .= $this->objOutput->getObjectRender()->renderField($field);

        // check if availability stats are available then only process further
        if (isset($ariInfo['stats']) && $ariInfo['stats']) {
            $field = array('sqlId' => 'total_rooms', 'value' => $ariInfo['stats']['total_rooms']);
            $this->output .= $this->objOutput->getObjectRender()->renderField($field);

            if (isset($ariInfo['stats']['num_avail'])) {
                $field = array('sqlId' => 'total_available_rooms', 'value' => $ariInfo['stats']['num_avail']);
                $this->output .= $this->objOutput->getObjectRender()->renderField($field);
            }

            if (isset($ariInfo['stats']['num_unavail'])) {
                $field = array('sqlId' => 'total_unavailable_rooms', 'value' => $ariInfo['stats']['num_unavail']);
                $this->output .= $this->objOutput->getObjectRender()->renderField($field);
            }

            if (isset($ariInfo['stats']['num_part_avai'])) {
                $field = array('sqlId' => 'total_partial_available_rooms', 'value' => $ariInfo['stats']['num_part_avai']);
                $this->output .= $this->objOutput->getObjectRender()->renderField($field);
            }

            if (isset($ariInfo['stats']['num_booked'])) {
                $field = array('sqlId' => 'total_booked_rooms', 'value' => $ariInfo['stats']['num_booked']);
                $this->output .= $this->objOutput->getObjectRender()->renderField($field);
            }
        }

        // check if room type data is available then only process further
        if (isset($ariInfo['rm_data']) && $ariInfo['rm_data']) {
            $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('room_types', array());

            foreach ($ariInfo['rm_data'] as $roomTypeInfo) {
                $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('room_type', array(), array('id' => $roomTypeInfo['id_product'], 'xlink_resource' => $this->wsObject->wsUrl.'room_types'.'/'.$roomTypeInfo['id_product']));

                $field = array('sqlId' => 'id_room_type', 'value' => $roomTypeInfo['id_product'], 'xlink_resource' => 'room_types');
                $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                // get feature price of room type
                $roomTypePrice = HotelRoomTypeFeaturePricing::getRoomTypeFeaturePricesPerDay(
                    $roomTypeInfo['id_product'],
                    $ariInfo['date_from'],
                    $ariInfo['date_to'],
                    0
                );
                $field = array('sqlId' => 'base_price', 'value' => $roomTypePrice);
                $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                // get feature price of room type
                $roomTypePrice = HotelRoomTypeFeaturePricing::getRoomTypeFeaturePricesPerDay(
                    $roomTypeInfo['id_product'],
                    $ariInfo['date_from'],
                    $ariInfo['date_to'],
                    1
                );
                $field = array('sqlId' => 'base_price_with_tax', 'value' => $roomTypePrice);
                $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                $totalBookingPrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                    $roomTypeInfo['id_product'],
                    $ariInfo['date_from'],
                    $ariInfo['date_to'],
                    $ariInfo['num_rooms']
                );
                $field = array('sqlId' => 'total_price', 'value' => $totalBookingPrice['total_price_tax_excl']);
                $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                $totalBookingPrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                    $roomTypeInfo['id_product'],
                    $ariInfo['date_from'],
                    $ariInfo['date_to'],
                    $ariInfo['num_rooms']
                );
                $field = array('sqlId' => 'total_price_with_tax', 'value' => $totalBookingPrice['total_price_tax_incl']);
                $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                $objRoomType = new Product($roomTypeInfo['id_product']);
                $field = array('sqlId' => 'name', 'value' => $objRoomType->name, 'i18n' => true);
                $this->output .= $this->objOutput->getObjectRender()->renderField($field);

                // rooms info of the room type
                if (isset($roomTypeInfo['data']) && $roomTypeInfo['data']) {
                    $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('rooms', array());
                    foreach ($roomTypeInfo['data'] as $key => $roomsInfo) {
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

        // wrap with the common parent header of QloApps xml api responses
        $this->output = $this->objOutput->getObjectRender()->overrideContent($this->output);

        return $this->output;
    }

    // create json for the response of the ari request
    private function getResponseJson($ariInfo)
    {
        $ariFormatted = array();
        $ariFormatted['hotel_ari'] = array();
        $ariFormatted['hotel_ari']['id_hotel'] = $ariInfo['id_hotel'];
        $ariFormatted['hotel_ari']['date_from'] = $ariInfo['date_from'];
        $ariFormatted['hotel_ari']['date_to'] = $ariInfo['date_to'];

        $objCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $ariFormatted['hotel_ari']['currency'] = $objCurrency->iso_code;

        // check if availability stats are available then only process further
        if (isset($ariInfo['stats']) && $ariInfo['stats']) {
            $ariFormatted['hotel_ari']['total_rooms'] = $ariInfo['stats']['total_rooms'];
            if (isset($ariInfo['stats']['num_avail'])) {
                $ariFormatted['hotel_ari']['total_available_rooms'] = $ariInfo['stats']['num_avail'];
            }
            if (isset($ariInfo['stats']['num_unavail'])) {
                $ariFormatted['hotel_ari']['total_unavailable_rooms'] = $ariInfo['stats']['num_unavail'];
            }
            if (isset($ariInfo['stats']['num_part_avai'])) {
                $ariFormatted['hotel_ari']['total_partial_available_rooms'] = $ariInfo['stats']['num_part_avai'];
            }
            if (isset($ariInfo['stats']['num_booked'])) {
                $ariFormatted['hotel_ari']['total_booked_rooms'] = $ariInfo['stats']['num_booked'];
            }
        }

        // check if room type data is available then only process further
        if (isset($ariInfo['rm_data']) && $ariInfo['rm_data']) {
            $ariFormatted['hotel_ari']['room_types'] = array();

            foreach ($ariInfo['rm_data'] as $roomTypeIndex => $roomTypeInfo) {
                // get feature price of room type
                $roomTypePriceTE = HotelRoomTypeFeaturePricing::getRoomTypeFeaturePricesPerDay(
                    $roomTypeInfo['id_product'],
                    $ariInfo['date_from'],
                    $ariInfo['date_to'],
                    0
                );
                $roomTypePriceTI = HotelRoomTypeFeaturePricing::getRoomTypeFeaturePricesPerDay(
                    $roomTypeInfo['id_product'],
                    $ariInfo['date_from'],
                    $ariInfo['date_to'],
                    1
                );
                $totalBookingPrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                    $roomTypeInfo['id_product'],
                    $ariInfo['date_from'],
                    $ariInfo['date_to'],
                    $ariInfo['num_rooms']
                );

                $ariFormatted['hotel_ari']['room_types'][$roomTypeIndex] = array(
                    'id_room_type' => $roomTypeInfo['id_product'],
                    'base_price' => $roomTypePriceTE,
                    'base_price_with_tax' => $roomTypePriceTI,
                    'total_price' => $totalBookingPrice['total_price_tax_excl'],
                    'total_price_with_tax' => $totalBookingPrice['total_price_tax_incl']
                );

                $objRoomType = new Product($roomTypeInfo['id_product']);
                $ariFormatted['hotel_ari']['room_types'][$roomTypeIndex]['name'] = array();
                foreach ($this->objOutput->getObjectRender()->languages as $idLang) {
                    $ariFormatted['hotel_ari']['room_types'][$roomTypeIndex]['name'][] = array(
                        'id' => $idLang,
                        'value' => $objRoomType->name[$idLang]
                    );
                }
                // if only one lang than do not set name as array and set only value of the room
                if (count($ariFormatted['hotel_ari']['room_types'][$roomTypeIndex]['name']) == 1) {
                    $ariFormatted['hotel_ari']['room_types'][$roomTypeIndex]['name'] = $ariFormatted['hotel_ari']['room_types'][$roomTypeIndex]['name'][0]['value'];
                }

                // rooms info of the room type
                if (isset($roomTypeInfo['data']) && $roomTypeInfo['data']) {
                    $ariFormatted['hotel_ari']['room_types'][$roomTypeIndex]['rooms'] = array();

                    foreach ($roomTypeInfo['data'] as $key => $roomsInfo) {
                        $roomsAriInfo = array();
                        if ($key == 'available') {
                            $keyRoomAvailability = 'available';
                        } elseif ($key == 'unavailable') {
                            $keyRoomAvailability = 'unavailable';
                        } elseif ($key == 'booked') {
                            $keyRoomAvailability = 'booked';
                        } elseif ($key == 'partially_available') {
                            $keyRoomAvailability = 'partially_available';
                        }

                        if ($roomsInfo) {
                            if ($key == 'partially_available') {
                                foreach ($roomsInfo as $dateIndex => $partialRoomsInfo) {
                                    if (isset($partialRoomsInfo['rooms']) && $partialRoomsInfo['rooms']) {
                                        $roomsAriInfo[$dateIndex]['date_from'] = $partialRoomsInfo['date_from'];
                                        $roomsAriInfo[$dateIndex]['date_to'] = $partialRoomsInfo['date_to'];

                                        $roomsAriInfo[$dateIndex]['rooms'] = array();
                                        foreach ($partialRoomsInfo['rooms'] as $roomInfo) {
                                            $roomDetail = array();
                                            $roomDetail['id_room'] = $roomInfo['id_room'];
                                            $roomDetail['room_number'] = $roomInfo['room_num'];

                                            $roomsAriInfo[$dateIndex]['rooms'][] = $roomDetail;
                                        }
                                    }
                                }
                            } else {
                                foreach ($roomsInfo as $roomIndex => $roomInfo) {
                                    $roomsAriInfo[$roomIndex]['id_room'] = $roomInfo['id_room'];
                                    $roomsAriInfo[$roomIndex]['room_number'] = $roomInfo['room_num'];
                                }
                            }
                        }

                        $ariFormatted['hotel_ari']['room_types'][$roomTypeIndex]['rooms'][$keyRoomAvailability] = array_values($roomsAriInfo);
                    }
                }

                $ariFormatted['hotel_ari']['room_types'] = array_values($ariFormatted['hotel_ari']['room_types']);
            }
        }

        // change the content to the json form for api response
        $this->output .= json_encode($ariFormatted);
        $this->output = preg_replace_callback("/\\\\u([a-f0-9]{4})/", function ($matches) {
            return iconv('UCS-4LE','UTF-8', pack('V', hexdec('U' . $matches[1])));
        }, $this->output);

        return $this->output;
    }
}
