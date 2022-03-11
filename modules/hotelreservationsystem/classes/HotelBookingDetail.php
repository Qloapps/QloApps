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

class HotelBookingDetail extends ObjectModel
{
    private $allReqDates;
    private $dltDates;
    private $partAvaiDates;            // used to remove cart rooms from partial available rooms

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
    public $total_price_tax_excl;    // Total price paid for this date range for this room type
    public $total_price_tax_incl;    // Total price paid for this date range for this room type
    public $total_paid_amount;       // Advance payment amount for the room
    public $is_back_order;
    public $id_status;
    public $is_refunded;
    // public $available_for_order;

    // hotel information/location/contact
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
    public $adult;
    public $children;
    public $child_ages;

    public $date_add;
    public $date_upd;

    const STATUS_ALLOTED = 1;
    const STATUS_CHECKED_IN = 2;
    const STATUS_CHECKED_OUT = 3;

    // booking allotment types
    const ALLOTMENT_AUTO = 1;
    const ALLOTMENT_MANUAL = 2;

    // Search algorithm: Exact room types reults, All room types
    const SEARCH_EXACT_ROOM_TYPE_ALGO = 1;
    const SEARCH_ALL_ROOM_TYPE_ALGO = 2;

    // Search TYpe: Occupancy wise search, Normal search
    const SEARCH_TYPE_OWS = 1;
    const SEARCH_TYPE_NORMAL = 2;

    public static $definition = array(
        'table' => 'htl_booking_detail',
        'primary' => 'id',
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_order_detail' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_room' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'booking_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_status' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'comment' => array('type' => self::TYPE_STRING),
            'check_in' => array('type' => self::TYPE_DATE),
            'check_out' => array('type' => self::TYPE_DATE),
            'date_from' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'date_to' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'total_price_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_price_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'total_paid_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'default' => 0, 'required' => true),
            'is_refunded' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            // 'available_for_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'is_back_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),

            // hotel information/location/contact
            'room_num' => array('type' => self::TYPE_STRING, 'required' => true),
            'room_type_name' => array('type' => self::TYPE_STRING, 'required' => true),
            'hotel_name' => array('type' => self::TYPE_STRING, 'required' => true),
            'city' => array('type' => self::TYPE_STRING, 'validate' => 'isCityName', 'size' => 64, 'required' => true),
            'state' => array('type' => self::TYPE_STRING),
            'country' => array('type' => self::TYPE_STRING, 'required' => true),
            'zipcode' => array('type' => self::TYPE_STRING),
            'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 32, 'required' => true),
            'email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 255, 'required' => true),
            'check_in_time' => array('type' => self::TYPE_STRING, 'required' => true),
            'check_out_time' => array('type' => self::TYPE_STRING, 'required' => true),
            'adult' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'children' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'child_ages' => array('type' => self::TYPE_STRING),

            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'bookings',
        'objectNodeName' => 'booking',
        'fields' => array(
            'id_product' => array(
                'xlink_resource' => array(
                    'resourceName' => 'products',
                )
            ),
            'id_hotel' => array(
                'xlink_resource' => array(
                    'resourceName' => 'hotels',
                )
            ),
            'id_order' => array(
                'xlink_resource' => array(
                    'resourceName' => 'orders',
                )
            ),
        ),
        'associations' => array(
            'booking_extra_demands' => array(
                'setter' => false,
                'resource' => 'extra_demand',
                'fields' => array('id' => array())
            ),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->moduleInstance = Module::getInstanceByName('hotelreservationsystem');
        parent::__construct($id);
    }

    public function getBookingDataParams($params)
    {
        if (!isset($params['id_room_type'])) {
            $params['id_room_type'] = 0;
        }
        if (!isset($params['id_cart'])) {
            $params['id_cart'] = 0;
        }
        if (!isset($params['id_guest'])) {
            $params['id_guest'] = 0;
        }
        if (!isset($params['search_available'])) {
            $params['search_available'] = 1;
        }
        if (!isset($params['search_partial'])) {
            $params['search_partial'] = 1;
        }
        if (!isset($params['search_booked'])) {
            $params['search_booked'] = 1;
        }
        if (!isset($params['search_unavai'])) {
            $params['search_unavai'] = 1;
        }
        if (!isset($params['search_cart_rms'])) {
            $params['search_cart_rms'] = 0;
        }

        if (!isset($params['only_active_roomtype'])) {
            $params['only_active_roomtype'] = 1;
        }
        if (!isset($params['only_active_hotel'])) {
            $params['only_active_hotel'] = 1;
        }
        if (!isset($params['occupancy'])) {
            $params['occupancy'] = array();
        }

        if (!isset($params['ratting'])) {
            $params['ratting'] = -1;
        }
        if (!isset($params['amenities'])) {
            $params['amenities'] = 0;
        }
        if (!isset($params['price'])) {
            $params['price'] = 0;
        }

        if (!isset($params['only_search_data'])) {
            $params['only_search_data'] = 0;
        }

        return $params;
    }

    /**
     * [getBookingData :: To get Array of rooms data].
     *
     * @param [type] $date_from        [Start date of booking]
     * @param [type] $date_to          [End date of booking]
     * @param int    $hotel_id         [Id of the hotel to which the room belongs]
     * @param int    $id_room_type     [Id of the product to which the room belongs]
     * @param int    $num_rooms        [Number of rooms booked for the period $date_from to $date_to]
     * @param int    $search_available [If you want only data information for available rooms]
     * @param int    $search_partial   [If you want only data information for partial rooms]
     * @param int    $search_booked    [If you want only data information for booked rooms]
     * @param int    $search_unavai    [If you want only data information for unavailable rooms]
     * @param int    $id_cart          [Id of the cart to which the room belongs at the time of booking]
     * @param int    $id_guest         [Id guest of the customer Who booked the rooms]
     * @param int    $search_cart_rms  [If you want data of the current cart in the admin office]
     * @param array  $occupancy        [search occupancy for room]
     *
     * @return [array] [Returns Array of rooms data ]
     *
     */
    public function getBookingData($params)
    {
        $this->context = Context::getContext();

        // extract all keys and values of the array [$params] into variables and values
        extract($this->getBookingDataParams($params));

        if ($date_from && $date_to && $hotel_id) {
            $date_from = date('Y-m-d H:i:s', strtotime($date_from));
            $stayStartDate = date('Y-m-d', strtotime($date_from));
            $date_to = date('Y-m-d H:i:s', strtotime($date_to));

            $objRoomType = new HotelRoomType();
            $lengthOfStay = $this->getNumberOfDays($date_from, $date_to);

            // Check LOS restriction for back-office
            $applyLosRestriction = true;
            if (isset($this->context->employee->id)) {
                if (!Configuration::get('PS_LOS_RESTRICTION_BO')) {
                    $applyLosRestriction = false;
                }
            }

            if ($room_types = $objRoomType->getIdProductByHotelId(
                $hotel_id,
                $id_room_type,
                $only_active_roomtype,
                $only_active_hotel
            )) {
                $allowedIdRoomTypes = implode(",", array_column($room_types, 'id_product'));

                // Unavailable Rooms
                if ($search_unavai) {
                    $params = array(
                        'idHotel' => $hotel_id,
                        'dateFrom' => $date_from,
                        'dateTo' => $date_to,
                        'idRoomType' => $id_room_type,
                        'allowedIdRoomTypes' => $allowedIdRoomTypes,
                        'applyLosRestriction' => $applyLosRestriction,
                    );
                    $unavailRoomTypes = $this->getSearchUnavailableRooms($params);
                }

                // Cart Rooms
                if ($search_cart_rms) {
                    $params = array(
                        'idHotel' => $hotel_id,
                        'idCart' => $id_cart,
                        'idGuest' => $id_guest,
                        'idRoomType' => $id_room_type,
                        'allowedIdRoomTypes' => $allowedIdRoomTypes,
                    );
                    $cartRoomTypes = $this->getSearchCartRooms($params);
                }

                // Available Rooms
                if ($search_available) {
                    $params = array(
                        'idHotel' => $hotel_id,
                        'dateFrom' => $date_from,
                        'dateTo' => $date_to,
                        'idCart' => $id_cart,
                        'idGuest' => $id_guest,
                        'idRoomType' => $id_room_type,
                        'searchOccupancy' => $occupancy,
                        'allowedIdRoomTypes' => $allowedIdRoomTypes,
                        'applyLosRestriction' => $applyLosRestriction,
                    );
                    $availableRoomTypes = $this->getSearchAvailableRooms($params);
                    if ($availableRoomTypes['unavailableRoomTypes'] && $search_unavai) {
                        foreach ($availableRoomTypes['unavailableRoomTypes'] as $idProduct => $roomTypeDetail) {
                            if (!isset($unavailRoomTypes[$idProduct])) {
                                $unavailRoomTypes[$idProduct] = array();
                            }

                            // FYI: used array_merge because we have int index
                            $unavailRoomTypes[$idProduct] = array_merge($unavailRoomTypes[$idProduct], $roomTypeDetail);
                        }
                    }

                    $availableRoomTypes = $availableRoomTypes['availableRoomTypes'];
                }

                // Booked Rooms
                if ($search_booked) {
                    $params = array(
                        'idHotel' => $hotel_id,
                        'dateFrom' => $date_from,
                        'dateTo' => $date_to,
                        'idRoomType' => $id_room_type,
                        'allowedIdRoomTypes' => $allowedIdRoomTypes,
                    );
                    $bookedRoomTypes = $this->getSearchBookedRooms($params);
                }

                // Partially available Rooms
                if ($search_partial) {
                    $params = array(
                        'idHotel' => $hotel_id,
                        'dateFrom' => $date_from,
                        'dateTo' => $date_to,
                        'idCart' => $id_cart,
                        'idGuest' => $id_guest,
                        'idRoomType' => $id_room_type,
                        'searchOccupancy' => $occupancy,
                        'allowedIdRoomTypes' => $allowedIdRoomTypes,
                    );

                    $partiallyAvailRoomTypes = $this->getSearchPartiallyAvailRooms($params);

                    if ($partiallyAvailRoomTypes['unavailableRoomTypes'] && $search_unavai) {
                        foreach ($partiallyAvailRoomTypes['unavailableRoomTypes'] as $idProduct => $roomTypeDetail) {
                            if (!isset($unavailRoomTypes[$idProduct])) {
                                $unavailRoomTypes[$idProduct] = array();
                            }

                            // FYI: used array_merge because we have int index
                            $unavailRoomTypes[$idProduct] = array_merge($unavailRoomTypes[$idProduct], $roomTypeDetail);
                        }
                    }

                    $partiallyAvailRoomsCount = $partiallyAvailRoomTypes['partiallyAvailRoomsCount'];
                    $partiallyAvailRoomTypes = $partiallyAvailRoomTypes['partiallyAvailRooms'];
                }

                $roomTypesDetail = $objRoomType->getRoomTypeDetailByRoomTypeIds($allowedIdRoomTypes);
                if ($roomTypesDetail) {
                    // Formate data for response
                    $finalSearchResponse = array(
                        'rm_data' => array(),
                        'stats' => array(
                            'total_room_type' => count($room_types),
                            'total_rooms' => 0,
                            'max_avail_occupancy' => 0
                        ),
                    );
                    foreach ($roomTypesDetail as $roomTypeDetail) {
                        $finalSearchResponse['stats']['total_rooms'] += $roomTypeDetail['numberOfRooms'];
                        $idProduct = $roomTypeDetail['id_product'];

                        $roomTypeSearchData = array(
                            'name' => (new Product((int) $idProduct, false, $this->context->cookie->id_lang))->name,
                            'id_product' => $idProduct,
                            'adult' => $roomTypeDetail['adult'],
                            'children' => $roomTypeDetail['children'],
                            'max_adults' => $roomTypeDetail['max_adults'],
                            'max_children' => $roomTypeDetail['max_children'],
                            'max_guests' => $roomTypeDetail['max_guests'],
                            'data' => array(),
                        );

                        if ($search_unavai) {
                            $roomTypeSearchData['data']['unavailable'] = isset($unavailRoomTypes[$idProduct]) ? $unavailRoomTypes[$idProduct] : array();

                            if (!isset($finalSearchResponse['stats']['num_unavail'])) {
                                $finalSearchResponse['stats']['num_unavail'] = 0;
                            }
                            $finalSearchResponse['stats']['num_unavail'] += count($roomTypeSearchData['data']['unavailable']);
                        }

                        if ($search_cart_rms) {
                            $roomTypeSearchData['data']['cart_rooms'] = isset($cartRoomTypes[$idProduct]) ? $cartRoomTypes[$idProduct] : array();

                            if (!isset($finalSearchResponse['stats']['num_cart'])) {
                                $finalSearchResponse['stats']['num_cart'] = 0;
                            }
                            $finalSearchResponse['stats']['num_cart'] += count($roomTypeSearchData['data']['cart_rooms']);
                        }

                        if ($search_booked) {
                            $roomTypeSearchData['data']['booked'] = isset($bookedRoomTypes[$idProduct]) ? $bookedRoomTypes[$idProduct] : array();

                            if (!isset($finalSearchResponse['stats']['num_booked'])) {
                                $finalSearchResponse['stats']['num_booked'] = 0;
                            }
                            $finalSearchResponse['stats']['num_booked'] += count($roomTypeSearchData['data']['booked']);
                        }

                        if ($search_available) {
                            $roomTypeSearchData['data']['available'] = array();
                            if (!isset($finalSearchResponse['stats']['num_avail'])) {
                                $finalSearchResponse['stats']['num_avail'] = 0;
                            }
                            if (isset($availableRoomTypes['roomTypes'][$idProduct])) {

                                $roomTypeSearchData['data']['available'] = $availableRoomTypes['roomTypes'][$idProduct]['rooms'];

                                $finalSearchResponse['stats']['num_avail'] += count($roomTypeSearchData['data']['available']);

                                $finalSearchResponse['stats']['max_avail_occupancy'] += count($roomTypeSearchData['data']['available']) * (int)$availableRoomTypes['roomTypes'][$idProduct]['maxOccupancy'];
                            }
                        }

                        if ($search_partial) {
                            $roomTypeSearchData['data']['partially_available'] = isset($partiallyAvailRoomTypes[$idProduct]) ? $partiallyAvailRoomTypes[$idProduct] : array();

                            $finalSearchResponse['stats']['num_part_avai'] = $partiallyAvailRoomsCount;
                        }

                        $finalSearchResponse['rm_data'][$idProduct] = $roomTypeSearchData;
                    }
                    unset($roomTypesDetail);

                    return $finalSearchResponse;
                }
            }
        }

        return array();
    }

    /**
     * $params = array(
     *      'idHotel' => ...,
     *      'dateFrom' => ...,
     *      'dateTo' => ...,
     *      'idRoomType' => ...,
     *      'allowedIdRoomTypes' => ...,
     *      'applyLosRestriction' => ...,
     * );
     */
    protected function getSearchUnavailableRooms($params)
    {
        $this->context = Context::getContext();
        // Check LOS restriction for back-office
        if (!isset($params['applyLosRestriction'])) {
            $applyLosRestriction = true;
            if (isset($this->context->employee->id)) {
                if (!Configuration::get('PS_LOS_RESTRICTION_BO')) {
                    $applyLosRestriction = false;
                }
            }
        }

        extract($params);

        $lengthOfStay = $this->getNumberOfDays($dateFrom, $dateTo);
        $stayStartDate = date('Y-m-d', strtotime($dateFrom));

        // Room status inactive
        $sql1 = 'SELECT `id` AS `id_room`, `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment`
                FROM `'._DB_PREFIX_.'htl_room_information`
                WHERE `id_hotel`='.(int)$idHotel.' AND `id_status` = '. HotelRoomInformation::STATUS_INACTIVE.' AND IF('.(int)$idRoomType.' > 0, `id_product` = '.(int)$idRoomType.', 1) AND `id_product` IN ('.$allowedIdRoomTypes.')';
        // if ($idRoomType) {
        //     $sql1 .= ' AND `id_product` ='.(int)$idRoomType;
        // }

        // check room is temperory inactive
        $sql2 = 'SELECT hri.`id` AS `id_room`, hri.`id_product`, hri.`id_hotel`, hri.`room_num`, hri.`comment` AS `room_comment`
                FROM `'._DB_PREFIX_.'htl_room_information` AS hri
                INNER JOIN `'._DB_PREFIX_.'htl_room_disable_dates` AS hrdd ON (hrdd.`id_room_type` = hri.`id_product` AND hrdd.	id_room = hri.`id`)
                WHERE hri.`id_hotel`='.$idHotel.' AND hri.`id_status` = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .' AND hrdd.`date_from` < \''.pSql($dateTo).'\' AND hrdd.`date_to` > \''.pSql($dateFrom).'\' AND IF('.(int)$idRoomType.' > 0, hri.`id_product` = '.(int)$idRoomType.', 1) AND hri.`id_product` IN ('.$allowedIdRoomTypes.')';

        $sql = $sql1.' UNION '.$sql2;

        if ($applyLosRestriction) {
            $sql3 = 'SELECT hri.`id` AS `id_room`, hri.`id_product`, hri.`id_hotel`, hri.`room_num`, hri.`comment` AS `room_comment`
                    FROM `'._DB_PREFIX_.'htl_room_information` AS hri
                    INNER JOIN `'._DB_PREFIX_.'htl_room_type` AS hrt ON (hrt.`id_product` = hri.`id_product`)
                    LEFT JOIN `'._DB_PREFIX_.'htl_room_type_restriction_date_range` AS hrtr ON (hrt.`id_product` = hrtr.`id_product` AND (hrtr.`date_from` <= \''.pSQL($stayStartDate).'\' AND hrtr.`date_to` > \''.pSQL($stayStartDate).'\'))
                    WHERE hri.`id_hotel`='.(int)$idHotel.' AND (IFNULL(hrtr.`min_los`, hrt.`min_los`) >'. (int)$lengthOfStay.' OR IF(IFNULL(hrtr.`max_los`, hrt.`max_los`) > 0, IFNULL(hrtr.`max_los`, hrt.`max_los`) < '.(int)$lengthOfStay.', 0)) AND IF('.(int)$idRoomType.' > 0, hri.`id_product` = '.(int)$idRoomType.', 1) AND hri.`id_product` IN ('.$allowedIdRoomTypes.')';

            $sql = $sql.' UNION '.$sql3;
        }

        $unavailRoomTypes = array();
        if ($unavailRooms = Db::getInstance()->executeS($sql)) {
            foreach ($unavailRooms as $unavailRoomDetail) {
                $unavailRoomTypes[$unavailRoomDetail['id_product']][] = $unavailRoomDetail;
            }
        }

        return $unavailRoomTypes;
    }

    /**
     * $params = array(
     *      'idHotel' => ...,
     *      'idCart' => ...,
     *      'idGuest' => ...,
     *      'idRoomType' => ...,
     *      'allowedIdRoomTypes' => ...,
     *  );
     */
    protected function getSearchCartRooms($params)
    {
        extract($params);

        $cartRoomTypes = array();
        if ($idCart && $idGuest) {
            $sql = 'SELECT cbd.`id_product`, cbd.`id_room`, cbd.`id_hotel`, cbd.`booking_type`, cbd.`comment`, rf.`room_num`, cbd.`date_from`, cbd.`date_to`
                FROM `'._DB_PREFIX_.'htl_cart_booking_data` AS cbd
                INNER JOIN `'._DB_PREFIX_.'htl_room_information` AS rf ON (rf.`id` = cbd.`id_room`)
                WHERE cbd.`id_hotel`= '.(int)$idHotel.' AND cbd.`id_cart` = '.(int)$idCart.' AND cbd.`id_guest` ='.(int)$idGuest.' AND cbd.`is_refunded` = 0 AND cbd.`is_back_order` = 0 AND IF('.(int)$idRoomType.' > 0, rf.`id_product` = '.(int)$idRoomType.', 1) AND rf.`id_product` IN ('.$allowedIdRoomTypes.')';

            if ($cartRooms = Db::getInstance()->executeS($sql)) {
                foreach ($cartRooms as $cartRoomDetail) {
                    $cartRoomTypes[$cartRoomDetail['id_product']][] = $cartRoomDetail;
                }
            }
        }

        return $cartRoomTypes;
    }

    /**
     * $params = array(
     *          'idHotel' => ...,
     *          'dateFrom' => ...,
     *          'dateTo' => ...,
     *          'idCart' => ...,
     *          'idGuest' => ...,
     *          'idRoomType' => ...,
     *          'searchOccupancy' => ...,
     *          'allowedIdRoomTypes' => ...,
     *          'applyLosRestriction' => ...,
     * );
     */
    protected function getSearchAvailableRooms($params)
    {
        $this->context = Context::getContext();
        // Check LOS restriction for back-office
        if (!isset($params['applyLosRestriction'])) {
            $applyLosRestriction = true;
            if (isset($this->context->employee->id)) {
                if (!Configuration::get('PS_LOS_RESTRICTION_BO')) {
                    $applyLosRestriction = false;
                }
            }
        }

        extract($params);

        $stayStartDate = date('Y-m-d', strtotime($dateFrom));
        $lengthOfStay = $this->getNumberOfDays($dateFrom, $dateTo);

        if (isset($this->context->employee->id)) {
            $QLO_OWS_SEARCH_ALGO_TYPE = Configuration::get('PS_BACKOFFICE_OWS_SEARCH_ALGO_TYPE');
            $QLO_SEARCH_TYPE = Configuration::get('PS_BACKOFFICE_SEARCH_TYPE');
        } else {
            $QLO_OWS_SEARCH_ALGO_TYPE = Configuration::get('PS_FRONT_OWS_SEARCH_ALGO_TYPE');
            $QLO_SEARCH_TYPE = Configuration::get('PS_FRONT_SEARCH_TYPE');
        }

        // Exculde Booked rooms
        $exclude_ids = 'SELECT `id_room`
        FROM `'._DB_PREFIX_.'htl_booking_detail`
        WHERE `id_hotel` = '.(int)$idHotel.' AND `is_back_order` = 0 AND `is_refunded` = 0 AND IF(`id_status` = '. self::STATUS_CHECKED_OUT.', (
            (DATE_FORMAT(`check_out`,  "%Y-%m-%d") > \''.pSQL($dateFrom).'\' AND DATE_FORMAT(`check_out`,  "%Y-%m-%d") <= \''.PSQL($dateTo).'\') AND (
                (`date_from` <= \''.pSQL($dateFrom).'\' AND `check_out` > \''.pSQL($dateFrom).'\' AND `check_out` <= \''.PSQL($dateTo).'\') OR
                (`date_from` >= \''.pSQL($dateFrom).'\' AND `check_out` > \''.pSQL($dateFrom).'\' AND `check_out` <= \''.pSQL($dateTo).'\') OR
                (`date_from` >= \''.pSQL($dateFrom).'\' AND `date_from` < \''.pSQL($dateTo).'\' AND `check_out` >= \''.pSQL($dateTo).'\') OR
                (`date_from` <= \''.pSQL($dateFrom).'\' AND `check_out` >= \''.pSQL($dateTo).'\')
            )
        ), (
            (`date_from` <= \''.pSQL($dateFrom).'\' AND `date_to` > \''.pSQL($dateFrom).'\' AND `date_to` <= \''.PSQL($dateTo).'\') OR
            (`date_from` >= \''.pSQL($dateFrom).'\' AND `date_to` <= \''.pSQL($dateTo).'\') OR
            (`date_from` >= \''.pSQL($dateFrom).'\' AND `date_from` < \''.pSQL($dateTo).'\' AND `date_to` >= \''.pSQL($dateTo).'\') OR
            (`date_from` <= \''.pSQL($dateFrom).'\' AND `date_to` >= \''.pSQL($dateTo).'\')
        )) AND IF('.(int)$idRoomType.' > 0, `id_product` = '.(int)$idRoomType.', 1) AND `id_product` IN ('.$allowedIdRoomTypes.')';

        // We have removed cart rooms after finally getting available rooms from booking
        // Exclude temporary disable rooms
        $exclude_ids .= ' UNION
            SELECT hri.`id` AS id_room
            FROM `'._DB_PREFIX_.'htl_room_information` AS hri
            INNER JOIN `'._DB_PREFIX_.'htl_room_disable_dates` AS hrdd ON (hrdd.`id_room_type` = hri.`id_product` AND hrdd.`id_room` = hri.`id`)
            WHERE hri.`id_hotel`='.(int)$idHotel.' AND hri.`id_status` = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .' AND (hrdd.`date_from` < \''.pSql($dateTo).'\' AND hrdd.`date_to` > \''.pSql($dateFrom).'\') AND IF('.(int)$idRoomType.' > 0, hri.`id_product` = '.(int)$idRoomType.', 1) AND hri.`id_product` IN ('.$allowedIdRoomTypes.')';

        // LOS Restriction. Also, Remember to check max LOS restriction is greater than zero
        if ($applyLosRestriction) {
            $exclude_ids .= ' UNION
                SELECT hri.`id` AS `id_room`
                FROM `'._DB_PREFIX_.'htl_room_information` AS hri
                INNER JOIN `'._DB_PREFIX_.'htl_room_type` AS hrt ON (hrt.`id_product` = hri.`id_product`)
                LEFT JOIN `'._DB_PREFIX_.'htl_room_type_restriction_date_range` AS hrtr ON (hrt.`id_product` = hrtr.`id_product` AND (hrtr.`date_from` <= \''.pSQL($stayStartDate).'\' AND hrtr.`date_to` > \''.pSQL($stayStartDate).'\'))
                WHERE hri.`id_hotel`='.(int)$idHotel.' AND (IFNULL(hrtr.`min_los`, hrt.`min_los`) >'. (int)$lengthOfStay.' OR IF(IFNULL(hrtr.`max_los`, hrt.`max_los`) > 0, IFNULL(hrtr.`max_los`, hrt.`max_los`) < '.(int)$lengthOfStay.', 0)) AND IF('.(int)$idRoomType.' > 0, hri.`id_product` = '.(int)$idRoomType.', 1) AND hri.`id_product` IN ('.$allowedIdRoomTypes.')';
        }

        $selectAvailRoomSearch = 'SELECT ri.`id` AS `id_room`, ri.`id_product`, ri.`id_hotel`, ri.`room_num`, ri.`comment` AS `room_comment`, hrt.`max_adults` AS max_adult, hrt.`max_children`, hrt.`max_guests` AS max_occupancy';

        $joinAvailRoomSearch = 'INNER JOIN `'._DB_PREFIX_.'htl_room_type` AS hrt ON (hrt.`id_product` = ri.`id_product`)';

        $whereAvailRoomSearch = 'WHERE ri.`id_hotel`='.(int)$idHotel.' AND ri.`id_status` != '. HotelRoomInformation::STATUS_INACTIVE.' AND ri.`id` NOT IN ('.$exclude_ids.') AND IF('.(int)$idRoomType.' > 0, ri.`id_product` = '.(int)$idRoomType.', 1) AND ri.`id_product` IN ('.$allowedIdRoomTypes.')';

        $groupByAvailRoomSearch = '';
        $orderByAvailRoomSearch = '';
        $orderWayAvailRoomSearch = '';

        Hook::exec('actionAvailRoomSearchSqlModifier',
            array(
                'select' => $selectAvailRoomSearch,
                'join' => &$joinAvailRoomSearch,
                'where' => &$whereAvailRoomSearch,
                'group_by' => &$groupByAvailRoomSearch,
                'order_by' => &$orderByAvailRoomSearch,
                'order_way' => &$orderWayAvailRoomSearch,
                'params' => array(
                    'id_hotel' => $idHotel,
                    'id_product' => $idRoomType,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo
                )
            )
        );

        $sql = $selectAvailRoomSearch;
        $sql .= ' FROM `'._DB_PREFIX_.'htl_room_information` AS ri';
        $sql .= ' '.$joinAvailRoomSearch;
        $sql .= ' '.$whereAvailRoomSearch;
        $sql .= ' '.$groupByAvailRoomSearch;
        $sql .= ' '.$orderByAvailRoomSearch;
        $sql .= ' '.$orderWayAvailRoomSearch;

        $availableRoomTypes = array();
        $unavailableRoomTypes = array();

        $avai_rooms = Db::getInstance()->executeS($sql);
        if ($avai_rooms) {
            $availableRoomTypes = array(
                'roomTypes' => array(),
                'maxTotalOccupancy' => 0,
                // 'totalRooms' => 0,
            );
            foreach ($avai_rooms as $avaiRoom) {
                if(!isset($availableRoomTypes['roomTypes'][$avaiRoom['id_product']])) {
                    $availableRoomTypes['roomTypes'][$avaiRoom['id_product']] = array(
                        'rooms' => array(),
                        'maxAdult' => 0,
                        'maxChildren' => 0,
                        'maxOccupancy' => 0,
                        'roomCount' => 0,
                    );
                }

                $availableRoomTypes['roomTypes'][$avaiRoom['id_product']]['rooms'][$avaiRoom['id_room']] = $avaiRoom;
                $availableRoomTypes['roomTypes'][$avaiRoom['id_product']]['maxAdult'] = $avaiRoom['max_adult'];
                $availableRoomTypes['roomTypes'][$avaiRoom['id_product']]['maxChildren'] = $avaiRoom['max_children'];
                $availableRoomTypes['roomTypes'][$avaiRoom['id_product']]['maxOccupancy'] = $avaiRoom['max_occupancy'];
                $availableRoomTypes['roomTypes'][$avaiRoom['id_product']]['roomCount'] += 1;

                $availableRoomTypes['maxTotalOccupancy'] += (int)$avaiRoom['max_occupancy'];
                // $availableRoomTypes['totalRooms'] += 1;
            }

            if ($QLO_SEARCH_TYPE == HotelBookingDetail::SEARCH_TYPE_OWS && $searchOccupancy) {
                $availableRoomTypes = $this->getAvailableRoomSatisfingOccupancy($searchOccupancy, $availableRoomTypes, $QLO_OWS_SEARCH_ALGO_TYPE);
                $unavailableRoomTypes = $availableRoomTypes['unavailableRoomTypes'];
                $availableRoomTypes = $availableRoomTypes['availableRoomTypes'];
            }

            if ($availableRoomTypes && !empty($idCart) && !empty($idGuest)) {
                $sql = 'SELECT `id_product`, `id_room`
                    FROM `'._DB_PREFIX_.'htl_cart_booking_data`
                    WHERE `id_hotel` = '.(int)$idHotel.' AND `id_cart` = '.(int)$idCart.' AND `id_guest` = '.(int)$idGuest.' AND `is_refunded` = 0 AND  `is_back_order` = 0 AND ((`date_from` <= \''.pSQL($dateFrom).'\' AND `date_to` > \''.pSQL($dateFrom).'\' AND `date_to` <= \''.pSQL($dateTo).'\') OR (`date_from` > \''.pSQL($dateFrom).'\' AND `date_to` < \''.pSQL($dateTo).'\') OR (`date_from` >= \''.pSQL($dateFrom).'\' AND `date_from` < \''.pSQL($dateTo).'\' AND `date_to` >= \''.pSQL($dateTo).'\') OR (`date_from` < \''.pSQL($dateFrom).'\' AND `date_to` > \''.pSQL($dateTo).'\')) AND IF('.(int)$idRoomType.' > 0, `id_product` = '.(int)$idRoomType.', 1) AND `id_product` IN ('.$allowedIdRoomTypes.')';

                $availCartRooms = Db::getInstance()->executeS($sql);
                // Also, don't forget to remove cart rooms, because we didn't remove cart rooms from sql query
                if ($availCartRooms) {
                    foreach ($availCartRooms as $cartRoomDetail) {
                        unset($availableRoomTypes['roomTypes'][$cartRoomDetail['id_product']]['rooms'][$cartRoomDetail['id_room']]);

                        if (isset($availableRoomTypes['roomTypes'][$cartRoomDetail['id_product']]) && !$availableRoomTypes['roomTypes'][$cartRoomDetail['id_product']]['rooms']) {
                            unset($availableRoomTypes['roomTypes'][$cartRoomDetail['id_product']]);
                        }
                    }
                    unset($availCartRooms);
                }
            }

            // there might be a change maxTotalOccupancy will give wrong information
            // because of all process might have changed the available roooms data/count
            // So further it is no use for us
            unset($availableRoomTypes['maxTotalOccupancy']);
        }

        return array(
            'unavailableRoomTypes' => $unavailableRoomTypes,
            'availableRoomTypes' => $availableRoomTypes,
        );
    }

    protected function getAvailableRoomSatisfingOccupancy($searchOccupancy, $availableRoomTypes, $QLO_OWS_SEARCH_ALGO_TYPE, $cartRooms = array())
    {
        $unavailableRoomTypes = array();
        if ($searchOccupancy && $availableRoomTypes) {
            // TODO: Try to merge with the next loop
            $totalReqOccupancy = 0;
            foreach ($searchOccupancy as $reqRoomOccupancy) {
                $totalReqOccupancy += (int)$reqRoomOccupancy['adult'] + (int)$reqRoomOccupancy['children'];
            }

            // Check total required occupancy must be <= hotel total available occupancy
            if ($totalReqOccupancy > $availableRoomTypes['maxTotalOccupancy']) {
                // This means hotel cannot take the booking of uncoming no. of guest
                // Move all the rooms to unavailable rooms and show no available room on front
                foreach ($availableRoomTypes['roomTypes'] as $idProduct => $roomTypeDetail) {
                    $unavailableRoomTypes[$idProduct] = $roomTypeDetail['rooms'];
                }

                // instead of unset each room type index, we will completely override the array to empty
                $availableRoomTypes = array();
            }

            // if total Required Occupancy can be fulfilled by hotel. So, now following step need to check
            // step 1: First, assign room type according to the respective required occupancy
            // Step 2: required occupancy with minimum available room type will be selected first for assiging room type
            // and if multiple required occupancies have same no. of available room types then we will take the weightage of maxOccupancy
            // Step 3: After sorting and selecting req occupancy in ascending order according to step 2,
            // now we need to select which available room type should be selected in case multiple room types are satisfing required occupancy
            // So, for this the weightage which have less MaxOccupancy allowred and having more rooms available if maximumOccupancy is also same.
            // Also, for better understanding we have writen step no. where they are executed.
            if ($availableRoomTypes && $QLO_OWS_SEARCH_ALGO_TYPE == HotelBookingDetail::SEARCH_EXACT_ROOM_TYPE_ALGO) {
                $reqOccupancies = array();

                $eligibleRoomTypes = array();
                $ineligibleRoomTypes = array();

                foreach ($searchOccupancy as $reqRoomOccupancy) {
                    if (!isset($reqRoomOccupancy['roomTypes'])) {
                        $reqRoomOccupancy['roomTypes'] = array();
                    }

                    foreach ($availableRoomTypes['roomTypes'] as $idProduct => $roomsDetail) {
                        // below is STEP 1
                        if (($reqRoomOccupancy['adult'] <= $roomsDetail['maxAdult']) &&
                        ($reqRoomOccupancy['children'] <= $roomsDetail['maxChildren']) &&
                        ((int)$reqRoomOccupancy['adult'] + (int)$reqRoomOccupancy['children'] <= $roomsDetail['maxOccupancy'])) {

                            // below is STEP 3
                            $reqRoomOccupancy['roomTypes'][$idProduct] = ($roomsDetail['maxOccupancy']*100 - $roomsDetail['roomCount']);

                            $eligibleRoomTypes[$idProduct] = $idProduct;
                            if (isset($ineligibleRoomTypes[$idProduct])) {
                                unset($ineligibleRoomTypes[$idProduct]);
                            }
                        } else {
                            if (!isset($eligibleRoomTypes[$idProduct])) {
                                $ineligibleRoomTypes[$idProduct] = $idProduct;
                            }
                        }
                    }

                    // Sort accordind to weight
                    asort($reqRoomOccupancy['roomTypes']);

                    if ($reqRoomOccupancy['roomTypes']) {
                        // Room occupancy weight index will tell us which required occupancy (search) should be selected first for assigning roomtype
                        // below is STEP 2
                        $reqRoomOccupancyWt = $this->generateRoomOccupancyWeight(
                            count($reqRoomOccupancy['roomTypes']),
                            (int)$reqRoomOccupancy['adult'] + (int)$reqRoomOccupancy['children'],
                            $reqRoomOccupancy['adult'],
                            $reqRoomOccupancy['children']
                        );

                        // In case avail rooms, required adult and required childers are same
                        // then to overcome the loose of required occupancy array
                        while (isset($reqOccupancies[$reqRoomOccupancyWt])) {
                            $reqRoomOccupancyWt += 1;
                        }

                        $reqOccupancies[$reqRoomOccupancyWt] = $reqRoomOccupancy;
                    } else {
                        // In case no room type satisfies specific occupancy
                        // check if exact search, move all room types to unavailable and break loop
                        if ($QLO_OWS_SEARCH_ALGO_TYPE == HotelBookingDetail::SEARCH_EXACT_ROOM_TYPE_ALGO) {
                            foreach ($availableRoomTypes['roomTypes'] as $idProduct => $roomTypeDetail) {
                                $unavailableRoomTypes[$idProduct] = $roomTypeDetail['rooms'];
                            }

                            // instead of unset each room type index, we will completely override the array to empty
                            $availableRoomTypes = array();
                            break;
                        }
                    }
                }
                unset($eligibleRoomTypes);

                if ($availableRoomTypes && $QLO_OWS_SEARCH_ALGO_TYPE == HotelBookingDetail::SEARCH_EXACT_ROOM_TYPE_ALGO) {
                    // Sort accordind to weight
                    ksort($reqOccupancies);

                    $usedRoomTypeQty = array();
                    foreach ($reqOccupancies as &$reqRoomOccupancy) {
                        while (count($reqRoomOccupancy['roomTypes']) > 0) {
                            $selectedRoomTypeId = key($reqRoomOccupancy['roomTypes']);
                            unset($reqRoomOccupancy['roomTypes'][$selectedRoomTypeId]);

                            if (!isset($usedRoomTypeQty[$selectedRoomTypeId])) {
                                $usedRoomTypeQty[$selectedRoomTypeId] = 0;
                            }

                            if (((int)$availableRoomTypes['roomTypes'][$selectedRoomTypeId]['roomCount'] - (int)$usedRoomTypeQty[$selectedRoomTypeId]) > 0) {
                                $reqRoomOccupancy['selectedRoomType'] = $selectedRoomTypeId;
                                $usedRoomTypeQty[$selectedRoomTypeId] += 1;
                                break;
                            }
                        }

                        // if no room type is selected, that means we cannot fulfill the the guest requiremtn if exact search
                        if (!isset($reqRoomOccupancy['selectedRoomType'])) {

                            // if exact search, move all room types to unavailable and break loop
                            if ($QLO_OWS_SEARCH_ALGO_TYPE == HotelBookingDetail::SEARCH_EXACT_ROOM_TYPE_ALGO) {
                                foreach ($availableRoomTypes['roomTypes'] as $idProduct => $roomTypeDetail) {
                                    $unavailableRoomTypes[$idProduct] = $roomTypeDetail['rooms'];
                                }

                                // instead of unset each room type index, we will completely override the array to empty
                                $availableRoomTypes = array();
                                break;
                            }

                        }
                    }
                    unset($usedRoomTypeQty);
                }
            }

            // If we are here than hotel can fulfill the required rooms with there respective occupancies
            // So, formate according to output data
            // but first check HotelBookingDetail::SEARCH_EXACT_ROOM_TYPE_ALGO,
            // if true, then only return rooms which satisfy search requirment
            if ($availableRoomTypes && $QLO_OWS_SEARCH_ALGO_TYPE == HotelBookingDetail::SEARCH_EXACT_ROOM_TYPE_ALGO && count($ineligibleRoomTypes) > 0) {
                foreach ($ineligibleRoomTypes as $idProduct) {
                    // Move ineligibleRoomTypes to unavailable rooms
                    if (!isset($unavailableRoomTypes[$idProduct])) {
                            $unavailableRoomTypes[$idProduct] = array();
                    }
                    $unavailableRoomTypes[$idProduct] = $availableRoomTypes['roomTypes'][$idProduct]['rooms'];

                    unset($availableRoomTypes['roomTypes'][$idProduct]);
                }
            }
            unset($ineligibleRoomTypes);
        }

        return array(
            'unavailableRoomTypes' => $unavailableRoomTypes,
            'availableRoomTypes' => $availableRoomTypes && isset($availableRoomTypes['roomTypes']) ? $availableRoomTypes : array(),
        );
    }


    /**
     * $params = array(
     *      'idHotel' => ...,
     *      'dateFrom' => ...,
     *      'dateTo' => ...,
     *      'idCart' => ...,
     *      'idGuest' => ...,
     *      'idRoomType' => ...,
     *      'searchOccupancy' => ...,
     *      'allowedIdRoomTypes' => ...,
     * );
     */
    protected function getSearchPartiallyAvailRooms($params)
    {
        extract($params);

        $sql1 = 'SELECT bd.`id_product`, bd.`id_room`, bd.`id_hotel`, bd.`id_customer`, bd.`booking_type`, bd.`id_status` AS booking_status, bd.`comment` AS `room_comment`, rf.`room_num`, bd.`date_from`, IF(bd.`id_status` = '. self::STATUS_CHECKED_OUT .', bd.`check_out`, bd.`date_to`) AS `date_to`, hrt.`max_adults` AS `max_adult`, hrt.`max_children`, hrt.`max_guests` AS `max_occupancy`
            FROM `'._DB_PREFIX_.'htl_booking_detail` AS bd
            INNER JOIN `'._DB_PREFIX_.'htl_room_information` AS rf ON (rf.`id` = bd.`id_room`)
            INNER JOIN `'._DB_PREFIX_.'htl_room_type` AS hrt ON (hrt.`id_product` = rf.`id_product`)
            WHERE bd.`id_hotel`='.(int)$idHotel.' AND rf.`id_status` != '. HotelRoomInformation::STATUS_INACTIVE .' AND bd.`is_back_order` = 0 AND bd.`is_refunded` = 0 AND IF(bd.`id_status` = '. self::STATUS_CHECKED_OUT .', (
                (DATE_FORMAT(`check_out`,  "%Y-%m-%d") > \''.pSQL($dateFrom).'\' AND DATE_FORMAT(`check_out`,  "%Y-%m-%d") < \''.PSQL($dateTo).'\') AND (
                    (bd.`date_from` <= \''.pSQL($dateFrom).'\' AND bd.`check_out` > \''.pSQL($dateFrom).'\' AND bd.`check_out` < \''.pSQL($dateTo).'\') OR
                    (bd.`date_from` > \''.pSQL($dateFrom).'\' AND bd.`date_from` < \''.pSQL($dateTo).'\' AND bd.`check_out` >= \''.pSQL($dateTo).'\') OR
                    (bd.`date_from` > \''.pSQL($dateFrom).'\' AND bd.`date_from` < \''.pSQL($dateTo).'\' AND bd.`check_out` > \''.pSQL($dateFrom).'\' AND bd.`check_out` < \''.pSQL($dateTo).'\')
                )
            ), (
                (bd.`date_from` <= \''.pSQL($dateFrom).'\' AND bd.`date_to` > \''.pSQL($dateFrom).'\' AND bd.`date_to` < \''.pSQL($dateTo).'\') OR
                (bd.`date_from` > \''.pSQL($dateFrom).'\' AND bd.`date_from` < \''.pSQL($dateTo).'\' AND bd.`date_to` >= \''.pSQL($dateTo).'\') OR
                (bd.`date_from` > \''.pSQL($dateFrom).'\' AND bd.`date_from` < \''.pSQL($dateTo).'\' AND bd.`date_to` < \''.pSQL($dateTo).'\')
            )) AND IF('.(int)$idRoomType.' > 0, rf.`id_product` = '.(int)$idRoomType.', 1) AND rf.`id_product` IN ('.$allowedIdRoomTypes.')';

        $sql2 = 'SELECT hri.`id_product`, hrdd.`id_room`, hri.`id_hotel`, 0 AS `id_customer`, 0 AS `booking_type`, 0 AS `booking_status`, 0 AS `room_comment`, hri.`room_num`, hrdd.`date_from`, hrdd.`date_to`, hrt.`max_adults` AS `max_adult`, hrt.`max_children`, hrt.`max_guests` AS `max_occupancy`
            FROM `'._DB_PREFIX_.'htl_room_information` AS hri
            INNER JOIN `'._DB_PREFIX_.'htl_room_type` AS hrt ON (hrt.`id_product` = hri.`id_product`)
            INNER JOIN `'._DB_PREFIX_.'htl_room_disable_dates` AS hrdd ON (hrdd.`id_room_type` = hri.`id_product` AND hrdd.`id_room` = hri.`id`)
            WHERE hri.`id_hotel`='.(int)$idHotel.' AND hri.`id_status` = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .' AND (
                (hrdd.`date_from` <= \''.pSQL($dateFrom).'\' AND hrdd.`date_to` > \''.pSQL($dateFrom).'\' AND hrdd.`date_to` < \''.pSQL($dateTo).'\') OR
                (hrdd.`date_from` > \''.pSQL($dateFrom).'\' AND hrdd.`date_from` < \''.pSQL($dateTo).'\' AND hrdd.`date_to` >= \''.pSQL($dateTo).'\') OR
                (hrdd.`date_from` > \''.pSQL($dateFrom).'\' AND hrdd.`date_from` < \''.pSQL($dateTo).'\' AND hrdd.`date_to` < \''.pSQL($dateTo).'\')
            ) AND IF('.(int)$idRoomType.' > 0, hri.`id_product` = '.(int)$idRoomType.', 1) AND hri.`id_product` IN ('.$allowedIdRoomTypes.')';

        $sql = $sql1.' UNION '.$sql2;
        $part_arr = Db::getInstance()->executeS($sql);

        // Get date wise available rooms
        $dateWiseRoomTypes = array();

        // Below variables will be used for formating
        $partialAvailRoomType = array();
        $partialRoomsList = array();


        $unavailableRoomTypes = array();

        if ($part_arr) {
            // Occupancy Wise Search OR Normal Search
            if (isset($this->context->employee->id)) {
                $QLO_SEARCH_TYPE = Configuration::get('PS_BACKOFFICE_SEARCH_TYPE');
            } else {
                $QLO_SEARCH_TYPE = Configuration::get('PS_FRONT_SEARCH_TYPE');
            }

            $period = new DatePeriod(
                new DateTime($dateFrom),
                new DateInterval('P1D'),
                new DateTime($dateTo)
            );

            // FYI:: Last date not included
            // Get all dates to be covered in booking
            $searchDates = array();
            foreach ($period as $objDate) {
                $searchDates[$objDate->getTimestamp()] = array(
                    'date_from' => $objDate->format('Y-m-d'),
                    'date_to' => $objDate->modify('+1 day')->format('Y-m-d'),
                );
            }

            $partiallyAvailRooms = array();
            foreach ($part_arr as $roomDetail) {
                if (!isset($partiallyAvailRooms[$roomDetail['id_room']])) {
                    $availableDates = $searchDates;
                } else {
                    $availableDates = $partiallyAvailRooms[$roomDetail['id_room']]['availableDates'];
                }

                $period = new DatePeriod(
                    new DateTime($roomDetail['date_from']),
                    new DateInterval('P1D'),
                    new DateTime($roomDetail['date_to'])
                );
                foreach ($period as $objDate) {
                    if (isset($availableDates[$objDate->getTimestamp()])) {
                        unset($availableDates[$objDate->getTimestamp()]);
                    }
                }

                // If room is available for booking
                if ($availableDates) {
                    if (!isset($partiallyAvailRooms[$roomDetail['id_room']])) {
                        $partiallyAvailRooms[$roomDetail['id_room']] = array(
                            'id_product' => $roomDetail['id_product'],
                            'id_room' => $roomDetail['id_room'],
                            'id_hotel' => $roomDetail['id_hotel'],
                            'room_num' => $roomDetail['room_num'],
                            'max_adult' => $roomDetail['max_adult'],
                            'max_children' => $roomDetail['max_children'],
                            'max_occupancy' => $roomDetail['max_occupancy'],
                            // 'booked_dates' => array(),
                        );
                    }
                    $partiallyAvailRooms[$roomDetail['id_room']]['availableDates'] = $availableDates;

                    // FYI:: For backward compatibility (will help in formating)
                    // $partiallyAvailRooms[$roomDetail['id_room']]['avai_dates'] = $availableDates;
                    // $partiallyAvailRooms[$roomDetail['id_room']]['booked_dates'][] = array(
                    //     'date_from' => $roomDetail['date_from'],
                    //     'date_to' => $roomDetail['date_to'],
                    //     'id_customer' => $roomDetail['id_customer'],
                    //     'booking_type' => $roomDetail['booking_type'],
                    //     'booking_status' => $roomDetail['booking_status'],
                    //     'comment' => $roomDetail['room_comment'],
                    // );
                } else {
                    if (!isset($unavailableRoomTypes[$roomDetail['id_product']][$roomDetail['id_room']])) {
                        $unavailableRoomTypes[$roomDetail['id_product']][$roomDetail['id_room']] = array(
                            'id_room' => $roomDetail['id_room'],
                            'id_product' => $roomDetail['id_product'],
                            'id_hotel' => $roomDetail['id_hotel'],
                            'room_num' => $roomDetail['room_num'],
                            'room_comment' => ''
                        );
                    }

                    if (isset($partiallyAvailRooms[$roomDetail['id_room']])) {
                        unset($partiallyAvailRooms[$roomDetail['id_room']]);
                    }
                }
            }

            // will be used to check all dates are covered by partially avail rooms
            $datesToCover = $searchDates;

            // Will be used to generate query to find partially avail rooms in cart
            $timeStampWiseRooms = array();

            // Arrange available rooms, date wise
            $datetimeObj = new DateTime();
            foreach ($partiallyAvailRooms as $idRoom => &$roomDetail) {
                foreach ($roomDetail['availableDates'] as $dateTimeStamp => $dateOnRoomAvail) {
                    unset($datesToCover[$dateTimeStamp]);

                    $timeStampWiseRooms[$dateTimeStamp][] = $idRoom;

                    if (!isset($dateWiseRoomTypes[$dateTimeStamp])) {
                        $dateWiseRoomTypes[$dateTimeStamp] = array(
                            // 'date' => $dateOnRoomAvail,
                            'dateFrom' => $datetimeObj->setTimestamp($dateTimeStamp)->format('Y-m-d'),
                            'dateTo' => $datetimeObj->modify('+1 day')->format('Y-m-d'),
                            'roomTypes' => array(),
                            'maxTotalOccupancy' => 0,
                            // 'roomTotalCount' => 0
                        );
                    }
                    if (!isset($dateWiseRoomTypes[$dateTimeStamp]['roomTypes'][$roomDetail['id_product']])) {
                        $dateWiseRoomTypes[$dateTimeStamp]['roomTypes'][$roomDetail['id_product']] = array(
                            'rooms' => array(),
                            'maxAdult' => 0,
                            'maxChildren' => 0,
                            'maxOccupancy' => 0,
                            'roomCount' => 0,
                        );
                    }
                    // not needed any further
                    unset($roomDetail['availableDates']);

                    $dateWiseRoomTypes[$dateTimeStamp]['roomTypes'][$roomDetail['id_product']]['rooms'][$idRoom] = $roomDetail;
                    $dateWiseRoomTypes[$dateTimeStamp]['roomTypes'][$roomDetail['id_product']]['maxAdult'] = $roomDetail['max_adult'];
                    $dateWiseRoomTypes[$dateTimeStamp]['roomTypes'][$roomDetail['id_product']]['maxChildren'] = $roomDetail['max_children'];
                    $dateWiseRoomTypes[$dateTimeStamp]['roomTypes'][$roomDetail['id_product']]['maxOccupancy'] = $roomDetail['max_occupancy'];
                    $dateWiseRoomTypes[$dateTimeStamp]['roomTypes'][$roomDetail['id_product']]['roomCount'] += 1;

                    $dateWiseRoomTypes[$dateTimeStamp]['maxTotalOccupancy'] += $roomDetail['max_occupancy'];
                    // $dateWiseRoomTypes[$dateTimeStamp]['roomTotalCount'] += 1;
                }
            }
            unset($datetimeObj);

            // If all dates rae covered
            if (!$datesToCover) {
                // array in ascending order of dates
                ksort($dateWiseRoomTypes);

                $dateWiseCartRooms = array();
                // get cart partially available rooms
                if (!empty($idCart) && !empty($idGuest)) {
                    $sql = 'SELECT `id_product`, `id_room`, `date_from`
                        FROM `'._DB_PREFIX_.'htl_cart_booking_data`
                        WHERE `id_hotel` = '.(int)$idHotel.' AND `id_cart` = '.(int)$idCart.' AND `id_guest` = '.(int)$idGuest.' AND `is_refunded` = 0 AND  `is_back_order` = 0 AND IF('.(int)$idRoomType.' > 0, `id_product` = '.(int)$idRoomType.', 1) AND `id_product` IN ('.$allowedIdRoomTypes.')';

                    $datetimeObj = new DateTime();
                    foreach ($timeStampWiseRooms as $timeStamp => $roomList) {
                        $datetimeObj->setTimestamp($timeStamp);
                        $partialDateFrom = $datetimeObj->format('Y-m-d H:i:s');

                        $datetimeObj->modify('+1 day');
                        $partialDateTo = $datetimeObj->format('Y-m-d H:i:s');

                        $sql .= ' AND (`date_from` = \''.pSQL($partialDateFrom).'\' AND `date_to` = \''.pSQL($partialDateTo).'\' AND `id_room` IN ('.implode(",", $roomList).'))';
                    }
                    unset($datetimeObj);

                    $partiallyAvailCartRooms = Db::getInstance()->executeS($sql);
                    if ($partiallyAvailCartRooms) {
                        foreach ($partiallyAvailCartRooms as $cartRoomDetail) {
                            $dateWiseCartRooms[strtotime($cartRoomDetail['date_from'])][] = array(
                                'id_product' => $cartRoomDetail['id_product'],
                                'id_room' => $cartRoomDetail['id_room']
                            );
                        }
                    }
                }

                foreach ($dateWiseRoomTypes as $timeStamp => &$roomTypeDetail) {
                    if ($QLO_SEARCH_TYPE == HotelBookingDetail::SEARCH_TYPE_OWS && $searchOccupancy) {
                        $dateWiseAvailableRooms = $this->getAvailableRoomSatisfingOccupancy(
                            $searchOccupancy,
                            $roomTypeDetail,
                            HotelBookingDetail::SEARCH_ALL_ROOM_TYPE_ALGO // All options for partial rooms
                        );

                        // Case: Unavailables rooms should be check, if they are available for anyother date range
                        // Above case is not needed to be checked for now beacuse either the date is completely unavailable for booking
                        // or we will get available rooms because of "HotelBookingDetail::SEARCH_ALL_ROOM_TYPE_ALGO" variable)

                        if ($dateWiseAvailableRooms['availableRoomTypes'] && isset($dateWiseAvailableRooms['availableRoomTypes']['roomTypes'])) {
                            $roomTypeDetail = $dateWiseAvailableRooms['availableRoomTypes'];

                            // there might be a change maxTotalOccupancy will give wrong information
                            // because of all process might have changed the available roooms data/count
                            // So further it is no use for us
                            unset($roomTypeDetail['maxTotalOccupancy']);
                        } else {
                            // Partial rooms are not availble for this date
                            // Move all the rooms to unavailable rooms
                            // Break the loop
                            foreach ($partiallyAvailRooms as $roomDetail) {
                                if (!isset($unavailableRoomTypes[$roomDetail['id_product']][$roomDetail['id_room']])) {
                                    $unavailableRoomTypes[$roomDetail['id_product']][$roomDetail['id_room']] = array(
                                        'id_room' => $roomDetail['id_room'],
                                        'id_product' => $roomDetail['id_product'],
                                        'id_hotel' => $roomDetail['id_hotel'],
                                        'room_num' => $roomDetail['room_num'],
                                        'room_comment' => ''
                                    );
                                }
                                // $unavailableRoomTypes[$roomDetail['id_product']][$roomDetail['id_room']] = $roomDetail;
                            }

                            $partiallyAvailRooms = array();
                            $dateWiseRoomTypes = array();
                            break;
                        }
                    }

                    // Don't forget to remove cart rooms
                    if ($roomTypeDetail['roomTypes'] && $dateWiseCartRooms && isset($dateWiseCartRooms[$timeStamp])) {
                        foreach ($dateWiseCartRooms[$timeStamp] as $cartRoomDetail) {
                            unset($roomTypeDetail['roomTypes'][$cartRoomDetail['id_product']]['rooms'][$cartRoomDetail['id_room']]);

                            if (!$roomTypeDetail['roomTypes'][$cartRoomDetail['id_product']]['rooms']) {
                                unset($roomTypeDetail['roomTypes'][$cartRoomDetail['id_product']]);
                            }
                        }
                    }
                }
                // free space
                unset($roomTypeDetail);

                // Return room type wise instead of date wise
                foreach ($dateWiseRoomTypes as $timeStamp => $dateWiseDetail) {
                    foreach ($dateWiseDetail['roomTypes'] as $idProduct => $roomTypeDetail) {
                        $partialAvailRoomType[$idProduct][$timeStamp] = array(
                            'date_from' => $dateWiseDetail['dateFrom'],
                            'date_to' => $dateWiseDetail['dateTo'],
                            'rooms' => $roomTypeDetail['rooms'],
                        );
                        $partialRoomsList = array_unique(array_merge($partialRoomsList, array_keys($roomTypeDetail['rooms'])), SORT_NUMERIC);
                    }
                }
            } else {
                // If all dates are not covered then move all partially available room types to unavailable
                foreach ($partiallyAvailRooms as $roomDetail) {
                    if (!isset($unavailableRoomTypes[$roomDetail['id_product']][$roomDetail['id_room']])) {
                        $unavailableRoomTypes[$roomDetail['id_product']][$roomDetail['id_room']] = array(
                            'id_room' => $roomDetail['id_room'],
                            'id_product' => $roomDetail['id_product'],
                            'id_hotel' => $roomDetail['id_hotel'],
                            'room_num' => $roomDetail['room_num'],
                            'room_comment' => ''
                        );
                    }
                }

                $partiallyAvailRooms = array();
                $dateWiseRoomTypes = array();
            }
            unset($datesToCover);
            unset($timeStampWiseRooms);
            unset($partiallyAvailRooms);
        }

        return array(
            'partiallyAvailRooms' => $dateWiseRoomTypes ? $partialAvailRoomType : $dateWiseRoomTypes,
            'unavailableRoomTypes' => $unavailableRoomTypes,
            'partiallyAvailRoomsCount' => count($partialRoomsList),
        );
    }

    /**
     * $params = array(
     *      'idHotel' => ...,
     *      'dateFrom' => ...,
     *      'dateTo' => ...,
     *      'idRoomType' => ...,
     *      'allowedIdRoomTypes' => ...,
     *  );
     */
    protected function getSearchBookedRooms($params)
    {
        extract($params);

        $sql = 'SELECT bd.`id_product`, bd.`id_room`, bd.`id_hotel`, bd.`id_customer`, bd.`booking_type`, bd.`id_status` AS booking_status, bd.`comment`, rf.`room_num`, bd.`date_from`, bd.`date_to`
                FROM `'._DB_PREFIX_.'htl_booking_detail` AS bd
                INNER JOIN `'._DB_PREFIX_.'htl_room_information` AS rf ON (rf.`id` = bd.`id_room`)
                WHERE bd.`id_hotel`='.(int)$idHotel.' AND bd.`is_refunded` = 0 AND bd.`is_back_order` = 0 AND IF(bd.`id_status` = '. self::STATUS_CHECKED_OUT .', bd.`date_from` <= \''.pSQL($dateFrom).'\' AND bd.`check_out` >= \''.pSQL($dateTo).'\', bd.`date_from` <= \''.pSQL($dateFrom).'\' AND bd.date_to >= \''.pSQL($dateTo).'\') AND IF('.(int)$idRoomType.' > 0, rf.`id_product` = '.(int)$idRoomType.', 1) AND rf.`id_product` IN ('.$allowedIdRoomTypes.')';

        $bookedRoomTypes = array();
        if ($booked_rooms = Db::getInstance()->executeS($sql)) {
            foreach ($booked_rooms as $booked_k => $booked_v) {
                if (!isset($bookedRoomTypes[$booked_v['id_product']][$booked_v['id_room']])) {
                    $bookedRoomTypes[$booked_v['id_product']][$booked_v['id_room']] = array(
                        'id_product' => $booked_v['id_product'],
                        'id_room' => $booked_v['id_room'],
                        'id_hotel' => $booked_v['id_hotel'],
                        'room_num' => $booked_v['room_num'],
                        'detail' => array()
                    );
                }
                $bookedRoomTypes[$booked_v['id_product']][$booked_v['id_room']]['detail'][] = array(
                    'date_from' => $booked_v['date_from'],
                    'date_to' => $booked_v['date_to'],
                    'id_customer' => $booked_v['id_customer'],
                    'booking_type' => $booked_v['booking_type'],
                    'booking_status' => $booked_v['booking_status'],
                    'comment' => $booked_v['comment'],
                );
            }
            unset($booked_rooms);
        }

        return $bookedRoomTypes;
    }

    // This function algo is same as available rooms algo and it not similar to booked rooms algo.
    public function chechRoomBooked($id_room, $date_from, $date_to)
    {
        $sql = 'SELECT `id_product`, `id_order`, `id_cart`, `id_room`, `id_hotel`, `id_customer`
        FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `id_room` = '.(int)$id_room.
        ' AND `is_back_order` = 0 AND `is_refunded` = 0 AND ((date_from <= \''.pSQL($date_from).'\' AND date_to > \''.
        pSQL($date_from).'\' AND date_to <= \''.pSQL($date_to).'\') OR (date_from > \''.pSQL($date_from).
        '\' AND date_to < \''.pSQL($date_to).'\') OR (date_from >= \''.pSQL($date_from).'\' AND date_from < \''.
        pSQL($date_to).'\' AND date_to >= \''.pSQL($date_to).'\') OR (date_from < \''.pSQL($date_from).
        '\' AND date_to > \''.pSQL($date_to).'\'))';

        return Db::getInstance()->getRow($sql);
    }

    /**
     * [createDateRangeArray :: This function will return array of dates from date_form to date_to (Not including date_to)
     * 							if ($for_check == 0)
     * 							{
     * 								Then this function will remove these dates from $allReqDates this array
     * 							}].
     *
     * @param [date] $strDateFrom [Start date of the date range]
     * @param [date] $strDateTo   [End date of the date range]
     * @param int    $for_check   [
     *                            if ($for_check == 0)
     *                            {
     *                            Then this function will remove these dates from $allReqDates this array
     *                            }
     *                            if ($for_check == 0)
     *                            {
     *                            This function will return array of dates from date_form to date_to (Not including 									date_to)
     *                            }
     *                            ]
     *
     * @return [array] [Returns array of the dates]
     */
    public function createDateRangeArray($strDateFrom, $strDateTo, $for_check = 0, $id_room = 0, $dlt_date = 1)
    {
        $aryRange = array();

        $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

        if ($iDateTo >= $iDateFrom) {
            $entryDate = date('Y-M-d', $iDateFrom);
            array_push($aryRange, $entryDate); // first entry

            if ($dlt_date) {
                $this->checkAllDatesCover($entryDate, $id_room);
            }

            while ($iDateFrom < $iDateTo) {
                $iDateFrom += 86400; // add 24 hours
                if ($iDateFrom != $iDateTo || !$for_check) {
                    // to stop last entry in check partial case

                    $entryDate = date('Y-M-d', $iDateFrom);
                    array_push($aryRange, $entryDate);

                    if ($iDateFrom != $iDateTo && $dlt_date) {
                        $this->checkAllDatesCover($entryDate, $id_room);
                    }
                }
            }
        }

        return $aryRange;
    }

    /**
     * [checkAllDatesCover description :: Check the passed date is available in the array $allReqDates if available then removes date from array $all_date_arr].
     *
     * @param [date] $dateCheck [Date to checked in the array $allReqDates]
     *
     * @return [boolean] [Returns true]
     */
    public function checkAllDatesCover($dateCheck, $id_room)
    {
        if (isset($this->allReqDates) && !empty($this->allReqDates)) {
            if (($key = array_search($dateCheck, $this->allReqDates)) !== false) {
                if ($id_room) {
                    $this->dltDates[$id_room] = $dateCheck;
                }

                unset($this->allReqDates[$key]);
            }
        }
        return true;
    }

    /**
     * [getPartialRange :: To get array containing ].
     * @param [array] $dateArr [Array containing dates]
     * @return [array] [IF passed array of dates contains more than one date then returns ]
     */
    public function getPartialRange($dateArr, $id_room = 0, $rm_data_key = false)
    {
        $dateRange = array();

        if (count($dateArr) >= 2) {
            for ($i = 0; $i < count($dateArr) - 1; ++$i) {
                $dateRange[strtotime($dateArr[$i])] = array('date_from' => $dateArr[$i], 'date_to' => $dateArr[$i + 1]);
                if ($id_room && ($rm_data_key !== false)) {
                    $this->partAvaiDates[$id_room.strtotime($dateArr[$i])] = array('rm_data_key' => $rm_data_key);
                }
            }
        } else {
            $dateRange = $dateArr;
        }

        return $dateRange;
    }

    /**
     * [getNumberOfDays ::To get number of datys between two dates].
     *
     * @param [date] $dateFrom [Start date of the booking]
     * @param [date] $dateTo   [End date of the booking]
     *
     * @return [int] [Returns number of days between two dates]
     */
    public function getNumberOfDays($dateFrom, $dateTo)
    {
        $startDate = new DateTime($dateFrom);
        $endDate = new DateTime($dateTo);
        $daysDifference = $startDate->diff($endDate)->days;

        return $daysDifference;
    }

    public function generateRoomOccupancyWeight($availRoomTypeCount, $totalOccupancy, $totalAdult, $totalChildren)
    {
        return (10000000*(int)$availRoomTypeCount - (100000*(int)$totalOccupancy + 1000*(int)$totalAdult + 10*$totalChildren));
    }

    /**
     * [getBookingDataByOrderId :: To get booking information by id order].
     *
     * @param [int] $order_id [Id of the order]
     *
     * @return [array|false] [If data found Returns the array containing the information of the booking of an order else returns false]
     */
    public function getBookingDataByOrderId($order_id)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `id_order`='.(int)$order_id
        );
    }

    /**
     * [updateBookingOrderStatusBYOrderId :: To update the order status of a room in the booking].
     * @param [int] $order_id   [Id of the order]
     * @param [int] $new_status [Id of the new status of the order to be updated]
     * @param [int] $id_room    [Id of the room which order status is to be ypdated]
     * @return [Boolean] [Returns true if successfully updated else returns false]
     */
    public function updateBookingOrderStatusByOrderId(
        $order_id,
        $new_status,
        $id_room,
        $date_from,
        $date_to,
        $status_date = ''
    ) {
        if ($status_date) {
            $status_date = date('Y-m-d H:i:s', strtotime($status_date));
        } else {
            $status_date = date('Y-m-d H:i:s');
        }

        $table = 'htl_booking_detail';

        // create date to update in the table
        if ($new_status == self::STATUS_CHECKED_IN) {
            $data = array(
                'id_status' => $new_status,
                'check_in' => ($status_date > $date_to ? $date_to : $status_date)
            );
        } elseif ($new_status == self::STATUS_CHECKED_OUT) {
            $data = array(
                'id_status' => $new_status,
                'check_out' => ($status_date > $date_to ? $date_to : $status_date)
            );
        } else {
            $data = array(
                'id_status' => $new_status,
                'check_in' => '',
                'check_out' => ''
            );
        }

        // where conditions
        $where = 'id_order = '.(int)$order_id.' AND id_room = '.(int)$id_room.
        ' AND `date_from` = \''.pSQL($date_from).'\' AND `date_to` = \''.pSQL($date_to).'\'';

        return Db::getInstance()->update($table, $data, $where);
    }

    /**
     * [dataForFrontSearch ].
     *
     * @param [date] $date_from     [Start date of the booking]
     * @param [date] $date_to       [End date of the booking]
     * @param [int]  $id_hotel      [Id of the Hotel]
     * @param [int]  $id_product    [ID of the product]
     * @param [int]  $only_search_data [used for product page and category page for block cart]
     * @param [int]  $adult         []
     * @param [int]  $children      []
     * @param []     $ratting       [description]
     * @param []     $amenities     [description]
     * @param []     $price         [description]
     * @param [int]  $id_cart       [Id of the cart]
     * @param [int]  $id_guest      [Id of the guest]
     *
     * @return [array] [Returns true if successfully updated else returns false]
     *                 Note:: $only_search_data is used for product page and category page for block cart
     */
    public function dataForFrontSearch($bookingParams)
    {
        if (Module::isInstalled('productcomments')) {
            require_once _PS_MODULE_DIR_.'productcomments/ProductComment.php';
        }

        $this->context = Context::getContext();

        $bookingParams['search_available'] = 1;
        $bookingParams['search_partial'] = 0;
        $bookingParams['search_booked'] = 0;
        $bookingParams['search_unavai'] = 0;

        $bookingData = $this->getBookingData($bookingParams);

        extract($this->getBookingDataParams($bookingParams));

        if (!$only_search_data) {
            if (!empty($bookingData)) {
                $objRoomType = new HotelRoomType();

                foreach ($bookingData['rm_data'] as $key => $value) {
                    if (empty($value['data']['available'])) {
                        unset($bookingData['rm_data'][$key]);
                    } else {
                        if (Module::isInstalled('productcomments')) {
                            $prod_ratting = ProductComment::getAverageGrade($value['id_product'])['grade'];
                        }
                        if (empty($prod_ratting)) {
                            $prod_ratting = 0;
                        }

                        if ($prod_ratting < $ratting && $ratting != -1) {
                            unset($bookingData['rm_data'][$key]);
                        } else {
                            $product = new Product($value['id_product'], false, $this->context->language->id);

                            $product_feature = $product->getFrontFeaturesStatic($this->context->language->id, $value['id_product']);

                            $prod_amen = array();
                            if (!empty($amenities) && $amenities) {
                                $prod_amen = $amenities;
                                foreach ($product_feature as $a_key => $a_val) {
                                    if (($pa_key = array_search($a_val['id_feature'], $prod_amen)) !== false) {
                                        unset($prod_amen[$pa_key]);
                                        if (empty($prod_amen)) {
                                            break;
                                        }
                                    }
                                }
                                if (!empty($prod_amen)) {
                                    unset($bookingData['rm_data'][$key]);
                                }
                            }

                            if (empty($prod_amen)) {
                                $prod_price = Product::getPriceStatic($value['id_product'], self::useTax());
                                $productPriceWithoutReduction = $product->getPriceWithoutReduct(!self::useTax());
                                $productFeaturePrice = HotelRoomTypeFeaturePricing::getRoomTypeFeaturePricesPerDay($value['id_product'], $date_from, $date_to, self::useTax());

                                if (empty($price) || ($price['from'] <= $prod_price && $price['to'] >= $prod_price)) {
                                    $cover_image_arr = $product->getCover($value['id_product']);

                                    if (!empty($cover_image_arr)) {
                                        $cover_img = $this->context->link->getImageLink($product->link_rewrite, $product->id.'-'.$cover_image_arr['id_image'], 'home_default');
                                    } else {
                                        $cover_img = $this->context->link->getImageLink($product->link_rewrite, $this->context->language->iso_code.'-default', 'home_default');
                                    }

                                    $room_left = count($bookingData['rm_data'][$key]['data']['available']);

                                    $rm_dtl = $objRoomType->getRoomTypeInfoByIdProduct($value['id_product']);

                                    $bookingData['rm_data'][$key]['name'] = $product->name;
                                    $bookingData['rm_data'][$key]['image'] = $cover_img;
                                    $bookingData['rm_data'][$key]['description'] = $product->description_short;
                                    $bookingData['rm_data'][$key]['feature'] = $product_feature;
                                    $bookingData['rm_data'][$key]['price'] = $prod_price;
                                    $bookingData['rm_data'][$key]['price_without_reduction'] = $productPriceWithoutReduction;
                                    $bookingData['rm_data'][$key]['feature_price'] = $productFeaturePrice;
                                    $bookingData['rm_data'][$key]['feature_price_diff'] = $productPriceWithoutReduction - $productFeaturePrice;

                                    // if ($room_left <= (int)Configuration::get('WK_ROOM_LEFT_WARNING_NUMBER'))
                                    $bookingData['rm_data'][$key]['room_left'] = $room_left;

                                    $bookingData['rm_data'][$key]['adult'] = $rm_dtl['adult'];
                                    $bookingData['rm_data'][$key]['children'] = $rm_dtl['children'];

                                    $bookingData['rm_data'][$key]['ratting'] = $prod_ratting;
                                    if (Module::isInstalled('productcomments')) {
                                        $bookingData['rm_data'][$key]['num_review'] = ProductComment::getCommentNumber($value['id_product']);
                                    }

                                    // create URL with the parameters from URL
                                    $urlData = array ('date_from' => $date_from, 'date_to' => $date_to);
                                    if (!isset($occupancy)) {
                                        $occupancy = Tools::getValue('occupancy');
                                    }
                                    if ($occupancy) {
                                        $urlData['occupancy'] = $occupancy;
                                    }

                                    if (Configuration::get('PS_REWRITING_SETTINGS')) {
                                        $bookingData['rm_data'][$key]['product_link'] = $this->context->link->getProductLink($product).'?'.http_build_query($urlData);
                                    } else {
                                        $bookingData['rm_data'][$key]['product_link'] = $this->context->link->getProductLink($product).'&'.http_build_query($urlData);
                                    }
                                } else {
                                    unset($bookingData['rm_data'][$key]);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $bookingData;
    }

    /**
     * [getAvailableRoomsForReallocation :: Get the available rooms For the reallocation of the selected room].
     *
     * @param [date] $date_from[Start date of booking of the room to be swapped with available rooms]
     * @param [date] $date_to         [End date of booking of the room to be swapped with available rooms]
     * @param [int]  $id_room_type       [Id of the product to which the room belongs to be swapped]
     * @param [int]  $hotel_id        [Id of the Hotel to which the room belongs to be swapped]
     *
     * @return [array|false] [Returs array of the available rooms for swapping if rooms found else returnss false]
     */
    public function getAvailableRoomsForReallocation($date_from, $date_to, $id_room_type, $hotel_id)
    {
        if (isset($_COOKIE['wk_id_cart'])) {
            $current_admin_cart_id = $_COOKIE['wk_id_cart'];
        }
        $exclude_ids = 'SELECT `id_room` FROM `'._DB_PREFIX_.'htl_booking_detail`
            WHERE `date_from` < \''.pSQL($date_to).'\' AND `date_to` > \''.pSQL($date_from).'\'
            AND `is_refunded`=0 AND `is_back_order`=0
            UNION
            SELECT hri.`id` AS id_room
            FROM `'._DB_PREFIX_.'htl_room_information` AS hri
            INNER JOIN `'._DB_PREFIX_.'htl_room_disable_dates` AS hrdd ON (hrdd.`id_room_type` = hri.`id_product` AND hrdd.`id_room` = hri.`id`)
            WHERE hri.`id_hotel`='.(int)$hotel_id.' AND hri.`id_product` ='.(int)$id_room_type.'
            AND hri.`id_status` = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .'
            AND (hrdd.`date_from` <= \''.pSql($date_to).'\' AND hrdd.`date_to` >= \''.pSql($date_from).'\')';

        if (isset($current_admin_cart_id) && $current_admin_cart_id) {
            $sql = 'SELECT `id` AS `id_room`, `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment`
            FROM `'._DB_PREFIX_.'htl_room_information`
            WHERE `id_hotel`='.(int)$hotel_id.' AND `id_product`='.(int)$id_room_type.'
            AND (id_status = '. HotelRoomInformation::STATUS_ACTIVE .' or id_status = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .')
            AND `id` NOT IN ('.$exclude_ids.')
            AND `id` NOT IN (SELECT `id_room` FROM `'._DB_PREFIX_.'htl_cart_booking_data` WHERE `id_cart`='.
            (int)$current_admin_cart_id.')';
        } else {
            $sql = 'SELECT `id` AS `id_room`, `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment`
            FROM `'._DB_PREFIX_.'htl_room_information`
            WHERE `id_hotel`='.(int)$hotel_id.' AND `id_product`='.(int)$id_room_type.'
            AND (id_status = '. HotelRoomInformation::STATUS_ACTIVE .' or id_status = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .')
            AND `id` NOT IN ('.$exclude_ids.')';
        }
        $avail_rooms = Db::getInstance()->executeS($sql);
        if ($avail_rooms) {
            return $avail_rooms;
        }

        return false;
    }

    /**
        * [getAvailableRoomsForSwaping :: Get the available rooms for the swapping of the selected room with another room].
        * @param [date] $date_from       [Start date of booking of the room to be swapped with available rooms]
        * @param [date] $date_to         [End date of booking of the room to be swapped with available rooms]
        * @param [int]  $room_type       [Id of the product to which the room belongs to be swapped]
        * @param [int]  $hotel_id        [Id of the Hotel to which the room belongs to be swapped]
        *
        * @return [array|false] [Returs array of the available rooms for swapping if rooms found else returnss false]
        */
    public function getAvailableRoomsForSwapping($date_from, $date_to, $id_room_type, $hotel_id, $id_room)
    {
        $sql = 'SELECT `id` AS `id_room`, `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment`
            FROM `'._DB_PREFIX_.'htl_room_information`
            WHERE `id_hotel`='.(int)$hotel_id.' AND `id_product`='.(int)$id_room_type.'
            AND (id_status = '. HotelRoomInformation::STATUS_ACTIVE .' or id_status = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .')
            AND `id` IN (
                SELECT `id_room` FROM `'._DB_PREFIX_.'htl_booking_detail`
                WHERE `date_from` = \''.pSQL($date_from).'\' AND `date_to` = \''.pSQL($date_to).'\'
                AND `id_room`!='.(int)$id_room.' AND `is_refunded`=0 AND `is_back_order`=0
            )';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * [reallocateRoomWithAvailableSameRoomType :: To reallocate rooms with available rooms in case of reallocation of the room].
     * @param [int]  $current_room_id [Id of the room to be reallocated]
     * @param [date] $date_from       [start date of the booking of the room]
     * @param [date] $date_to         [end date of the booking of the room]
     * @param [date] $swapped_room_id [Id of the room with which the $current_room_id will be reallocated]
     *
     * @return [boolean] [true if rooms successfully reallocated else returns false]
     */
    public function reallocateRoomWithAvailableSameRoomType($current_room_id, $date_from, $date_to, $swapped_room_id)
    {
        $date_from = date('Y-m-d H:i:s', strtotime($date_from));
        $date_to = date('Y-m-d H:i:s', strtotime($date_to));
        $table = 'htl_cart_booking_data';
        $table2 = 'htl_booking_detail';
        $data = array('id_room' => $swapped_room_id);
        $where = 'date_from=\''.pSQL($date_from).'\' AND date_to=\''.pSQL($date_to).'\' AND id_room='.
        (int)$current_room_id;

        if ($result = Db::getInstance()->update($table, $data, $where)) {
            if($room_num = Db::getInstance()->getValue(
                'SELECT `room_num` FROM `'._DB_PREFIX_.'htl_room_information` WHERE `id` = '.$swapped_room_id
            )) {
                $data['room_num'] = $room_num;
            }
            if ($result2 = Db::getInstance()->update($table2, $data, $where)) {
                Hook::exec(
                    'actionRoomReAllocateAfter',
                    array(
                        'room_id' => $current_room_id,
                        'realloc_room_id' => $swapped_room_id,
                        'date_from' => $date_from,
                        'date_to' => $date_to,
                    )
                );
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * [swapRoomWithAvailableSameRoomType :: To swap rooms with available rooms in case of reallocation of the room].
     * @param [int]  $current_room_id [Id of the room to be swapped]
     * @param [date] $date_from       [start date of the booking of the room]
     * @param [date] $date_to         [end date of the booking of the room]
     * @param [date] $swapped_room_id [Id of the room with which the $current_room_id will be swapped]
     *
     * @return [boolean] [true if rooms successfully swapped else returns false]
     */
    public function swapRoomWithAvailableSameRoomType($current_room_id, $date_from, $date_to, $swapped_room_id)
    {
        $date_from = date('Y-m-d H:i:s', strtotime($date_from));
        $date_to = date('Y-m-d H:i:s', strtotime($date_to));

        $idcrt1 = Db::getInstance()->getValue(
            'SELECT `id` FROM `'._DB_PREFIX_.'htl_cart_booking_data` WHERE `is_refunded` = 0
            AND `date_from`=\''.pSQL($date_from).'\' AND `date_to`=\''.pSQL($date_to).'\'
            AND `id_room`='.(int)$swapped_room_id
        );
        $idcrt2 = Db::getInstance()->getValue(
            'SELECT `id` FROM `'._DB_PREFIX_.'htl_cart_booking_data` WHERE `is_refunded` = 0
            AND `date_from`=\''.pSQL($date_from).'\' AND `date_to`=\''.pSQL($date_to).'\'
            AND `id_room`='.(int)$current_room_id
        );

        $swap_room = Db::getInstance()->getRow(
            'SELECT `id`, `room_num` FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `is_refunded` = 0
            AND `date_from`=\''.pSQL($date_from).'\' AND `date_to`=\''.pSQL($date_to).'\'
            AND `id_room`='.(int)$swapped_room_id
        );
        $curr_room = Db::getInstance()->getRow(
            'SELECT `id`, `room_num` FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `is_refunded` = 0
            AND `date_from`=\''.pSQL($date_from).'\' AND `date_to`=\''.pSQL($date_to).'\'
            AND `id_room`='.(int)$current_room_id
        );
        $sql = 'UPDATE `'._DB_PREFIX_.'htl_cart_booking_data` SET `id_room`=IF(`id`='.(int)$idcrt1.','.
        (int)$current_room_id.','.(int)$swapped_room_id.') WHERE `id` IN('.(int)$idcrt1.','.(int)$idcrt2.')';

        $sql1 = 'UPDATE `'._DB_PREFIX_.'htl_booking_detail`
            SET `id_room`=IF(`id`='.(int)$swap_room['id'].','.(int)$current_room_id.','.(int)$swapped_room_id.'),
            `room_num`=IF(
                `id`='.(int)$swap_room['id'].',\''.pSQL($curr_room['room_num']).'\',\''.pSQL($swap_room['room_num']).'\'
            )
            WHERE `id` IN('.(int)$swap_room['id'].','.(int)$curr_room['id'].')';

        if ($result = Db::getInstance()->execute($sql)) {
            $result2 = Db::getInstance()->execute($sql1);
            if ($result2) {
                Hook::exec(
                    'actionRoomSwapAfter',
                    array(
                        'room_id' => $current_room_id,
                        'swapped_room_id' => $swapped_room_id,
                        'date_from' => $date_from,
                        'date_to' => $date_to,
                    )
                );
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * [updateOrderRefundStatus :: To update the refund status of a room booked in the order if amount refunded by the admin].
     * @param [int]  $id_order  [Id of the order]
     * @param [date] $date_from [start date of the bookin of the room]
     * @param [date] $date_to   [end date of the bookin of the room]
     * @param [int]  $id_room   [id of the room for which refund is done]
     *
     * @return [boolean] [true if updated otherwise false]
     */
    public function updateOrderRefundStatus($id_order, $date_from = false, $date_to = false, $id_rooms = array())
    {
        $table = 'htl_booking_detail';
        $data = array('is_refunded' => 1);
        if ($id_rooms) {
            foreach ($id_rooms as $key_rm => $val_rm) {
                $where = 'id_order='.(int)$id_order.' AND id_room = '.(int)$val_rm['id_room'].' AND `date_from`= \''.
                pSQL($date_from).'\' AND `date_to` = \''.pSQL($date_to).'\'';
                $result = Db::getInstance()->update($table, $data, $where);
            }
        } else {
            return Db::getInstance()->update($table, $data, 'id_order='.(int)$id_order);
        }
        return $result;
    }

    /**
     * [useTax : To get whether tax is enabled for the current group or disabled].
     *
     * @return [Boolean] [If tax is enabled for the current group returns true else returns false]
     */
    public static function useTax()
    {
        $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
        if (!$priceDisplay || $priceDisplay == 2) {
            $price_tax = true;
        } elseif ($priceDisplay == 1) {
            $price_tax = false;
        }
        return $price_tax;
    }

    /**
     * [getPsOrderDetailsByProduct : To get details of the order by id_order and id_product].
     *
     * @param [Int] $id_product [Id of the product]
     * @param [Int] $id_order   [Id of the order]
     *
     * @return [Array|false] [If data found returns details of the order by id_product and id_order else returns false]
     */
    public function getPsOrderDetailsByProduct($id_product, $id_order)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'order_detail` WHERE `id_order`='.(int)$id_order.' AND `product_id`='.
        (int)$id_product;
        return Db::getInstance()->executeS($sql);
    }

    /**
     * [deleteRoomFromOrder : Deletes a row from the table with the supplied conditions].
     * @param [int]  $id_order  [Id of the order]
     * @param [int]  $id_room   [id_of the room]
     * @param [date] $date_from [Start date of the booking]
     * @param [date] $date_to   [End date of the booking]
     *
     * @return [Boolean] [True if deleted else false]
     */
    public function deleteOrderedRoomFromOrder($id_order, $id_hotel, $id_room, $date_from, $date_to)
    {
        return Db::getInstance()->delete(
            'htl_booking_detail',
            '`id_order`='.(int) $id_order.' AND `id_hotel`='.(int) $id_hotel.' AND `id_room`='.(int) $id_room.
            ' AND `date_from`=\''.pSQL($date_from).'\' AND `date_to`=\''.pSQL($date_to).'\''
        );

        return $delete;
    }

    public function getRoomBookinInformationForDateRangeByOrder(
        $id_room,
        $old_date_from,
        $old_date_to,
        $new_date_from,
        $new_date_to
    ) {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `id_room`='.(int)$id_room.
        ' AND `date_from` < \''.pSQL($new_date_to).'\' AND `date_to` > \''.pSQL($new_date_from).
        '\' AND `date_from` != \''.pSQL($old_date_from).'\' AND `date_to` != \''.pSQL($old_date_to).
        '\' AND `is_refunded`=0 AND `is_back_order`=0';

        return Db::getInstance()->executeS($sql);
    }

    public function UpdateHotelCartHotelOrderOnOrderEdit(
        $id_order,
        $id_room,
        $old_date_from,
        $old_date_to,
        $new_date_from,
        $new_date_to,
        $occupancy,
        $new_total_price = null
    ) {
        $rowByIdOrderIdRoom = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `id_room`='.(int)$id_room.' AND `id_order`='.(int)$id_order);
        $newTotalPriceTE = '';
        $newTotalPriceTI = '';
        $newNumDays = $this->getNumberOfDays($new_date_from, $new_date_to);
        if ($new_total_price) {
            $newTotalPriceTE = $new_total_price['tax_excl'];
            $newTotalPriceTI = $new_total_price['tax_incl'];
        } else {
            $oldNumDays = $this->getNumberOfDays($old_date_from, $old_date_to);
            $unitRoomPriceTE = $rowByIdOrderIdRoom['total_price_tax_excl'] / $oldNumDays;
            $unitRoomPriceTI = $rowByIdOrderIdRoom['total_price_tax_incl'] / $oldNumDays;
            $newTotalPriceTE = $unitRoomPriceTE * $newNumDays;
            $newTotalPriceTI = $unitRoomPriceTI * $newNumDays;
        }

        // update `total_paid_amount` on database
        $totalPaidAmount = 0;
        $isAdvancePayment = Db::getInstance()->getValue(
            'SELECT o.`is_advance_payment`
            FROM `'._DB_PREFIX_.'orders` o
            WHERE o.`id_order` = '.(int) $id_order
        );

        if ($isAdvancePayment) {
            $objHotelAdvancedPayment = new HotelAdvancedPayment();
            $productAdvancePayment = $objHotelAdvancedPayment->getIdAdvPaymentByIdProduct($rowByIdOrderIdRoom['id_product']);

            if (!$productAdvancePayment || (isset($productAdvancePayment['payment_type']) && $productAdvancePayment['payment_type'])) {
                $totalPaidAmount = $objHotelAdvancedPayment->getRoomMinAdvPaymentAmount(
                    $rowByIdOrderIdRoom['id_product'],
                    $new_date_from,
                    $new_date_to
                );
            }
        } else {
            $totalPaidAmount = $newTotalPriceTI;
        }

        $cart_booking = array(
            'table' => 'htl_cart_booking_data',
            'data' => array(
                'date_from' => $new_date_from,
                'date_to' => $new_date_to,
                'quantity' => $newNumDays,
                'adult' => $occupancy['adult'],
                'children' => $occupancy['children'],
                'child_ages' => json_encode($occupancy['child_ages']),
            ),
        );

        $booking_detail = array(
            'table' => 'htl_booking_detail',
            'data' => array(
                'date_from' => $new_date_from,
                'date_to' => $new_date_to,
                'total_price_tax_excl' => $newTotalPriceTE,
                'total_price_tax_incl' => $newTotalPriceTI,
                'total_paid_amount' => $totalPaidAmount,
                'adult' => $occupancy['adult'],
                'children' => $occupancy['children'],
                'child_ages' => json_encode($occupancy['child_ages']),
            ),
        );

        $where = 'id_order = '.(int)$id_order.' AND id_room = '.(int)$id_room.' AND date_from= \''.pSQL($old_date_from).
        '\' AND date_to = \''.pSQL($old_date_to).'\' AND `is_refunded`=0 AND `is_back_order`=0';

        $result = Db::getInstance()->update($cart_booking['table'], $cart_booking['data'], $where);
        $result &= Db::getInstance()->update($booking_detail['table'], $booking_detail['data'], $where);

        return $result;
    }

    /**
     * [getPsOrderDetailIdByIdProduct :: Returns id_order_details accoording to the product and order Id].
     * @param [int] $id_product [Id of the product]
     * @param [int] $id_order   [Id of the order]
     * @return [int|false] [If found id_order_detail else returns false]
     */
    public function getPsOrderDetailIdByIdProduct($id_product, $id_order)
    {
        $sql = 'SELECT `id_order_detail` FROM `'._DB_PREFIX_.'order_detail` WHERE `id_order`='.(int)$id_order.' AND `product_id`='.(int)$id_product;
        return Db::getInstance()->getvalue($sql);
    }

    /**
     * [getOrderCurrentDataByOrderId :: To get booking information of the order by Order id].
     * @param [int] $id_order [Id of the order]
     * @return [array|false] [If data found Returns the array containing the information of the cart of the passed order id else returns false]
     */
    public function getOrderCurrentDataByOrderId($id_order)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `id_order`='.(int)$id_order
        );
    }

    /**
     * [getOrderFormatedBookinInfoByIdOrder : To get Order booking information with some additional information in a custom famated way].
     * @param [Int] $id_order [Id of the order]
     * @return [Array|false] [If data found returns cart booking information with some additional information else returns false]
     */
    public function getOrderFormatedBookinInfoByIdOrder($id_order)
    {
        if ($order_detail_data = $this->getOrderCurrentDataByOrderId((int) $id_order)) {
            $context = Context::getContext();
            $objHtlBranchInfo = new HotelBranchInformation();

            foreach ($order_detail_data as $key => $value) {
                $product_image_id = Product::getCover($value['id_product']);
                $link_rewrite = ((new Product((int) $value['id_product'], Configuration::get('PS_LANG_DEFAULT')))->link_rewrite[Configuration::get('PS_LANG_DEFAULT')]);

                if ($product_image_id) {
                    $order_detail_data[$key]['image_link'] = $context->link->getImageLink($link_rewrite, $product_image_id['id_image'], 'small_default');
                } else {
                    $order_detail_data[$key]['image_link'] = $context->link->getImageLink($link_rewrite, $context->language->iso_code.'-default', 'small_default');
                }

                $objOrderDetail = new OrderDetail($value['id_order_detail']);
                $order_detail_data[$key]['room_type'] = $objOrderDetail->product_name;
                $order_detail_data[$key]['original_unit_price_tax_excl'] = $objOrderDetail->unit_price_tax_excl;
                $order_detail_data[$key]['original_unit_price_tax_incl'] = $objOrderDetail->unit_price_tax_incl;
                $order_detail_data[$key]['unit_price_without_reduction_tax_excl'] = $objOrderDetail->unit_price_tax_excl + $objOrderDetail->reduction_amount_tax_excl;
                $order_detail_data[$key]['unit_price_without_reduction_tax_incl'] = $objOrderDetail->unit_price_tax_incl + $objOrderDetail->reduction_amount_tax_incl;

                $num_days = $this->getNumberOfDays($value['date_from'], $value['date_to']);
                $order_detail_data[$key]['quantity'] = $num_days;
                $order_detail_data[$key]['paid_unit_price_tax_excl'] = $value['total_price_tax_excl'] / $num_days;
                $order_detail_data[$key]['paid_unit_price_tax_incl'] = $value['total_price_tax_incl'] / $num_days;

                $order_detail_data[$key]['feature_price_diff'] = (float)($order_detail_data[$key]['unit_price_without_reduction_tax_incl'] - $order_detail_data[$key]['paid_unit_price_tax_incl']);

                $order_detail_data[$key]['child_ages'] = json_decode($value['child_ages']);

                // Check if this booking as any refund history then enter refund data
                if ($refundInfo = OrderReturnCore::getOrdersReturnDetail($id_order, 0, $value['id'])) {
                    $order_detail_data[$key]['refund_info'] = reset($refundInfo);
                }
            }
            return $order_detail_data;
        }

        return false;
    }

    /**
     * [getOrderCurrentDataByOrderId :: To get Last inserted Id order detail of any order].
     *
     * @param [int] $id_order [Id of the order]
     *
     * @return [int] [last inserted id_order_detail]
     */
    public function getLastInsertedIdOrderDetail($id_order)
    {
        return Db::getInstance()->getValue(
            'SELECT MAX(`id_order_detail`) FROM `'._DB_PREFIX_.'order_detail` WHERE `id_order`='.(int)$id_order
        );
    }

    /**
     * [getOnlyOrderBookingData description].
     * @param [type] $id_order    [description]
     * @param [type] $id_guest    [description]
     * @param [type] $id_product  [description]
     * @param int    $id_customer [description]
     * @return [type] [description]
     */
    public function getOnlyOrderBookingData($id_order, $id_guest, $id_product, $id_customer = 0)
    {
        $sql = 'SELECT hbd.*, od.`unit_price_tax_incl`, od.`unit_price_tax_excl`, od.`reduction_amount_tax_excl`,
        od.`reduction_amount_tax_incl` FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        INNER JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = hbd.`id_order_detail`)
        WHERE hbd.`id_order` = '.(int)$id_order.' AND hbd.`id_product` = '.(int)$id_product;

        if ($id_customer) {
            $sql .=  ' AND hbd.`id_customer` = '.(int)$id_customer;
        }
        return Db::getInstance()->executeS($sql);
    }

    /**
     * [getOrderInfoIdOrderIdProduct :: Returns Cart Info by id_product]
     * @param  [int] $id_order    [order id]
     * @param  [int] $id_product [product id]
     * @return [array/false]     [returns all entries if data found else return false]
     */
    public static function getOrderInfoIdOrderIdProduct($id_order, $id_product)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_booking_detail`
            WHERE `id_order`='.(int) $id_order.' AND `id_product`='.(int) $id_product
        );
    }

    /**
     * [getCustomerIdRoomsByIdOrderIdProduct :: To get array of rooms ids in the cart booked by a customer for a date range]
     * @param  [int] $id_order    [Id of the id_order]
     * @param  [int] $id_product [Id of the product]
     * @param  [date] $date_from [Start date of the booking]
     * @param  [date] $date_to   [End date of the booking]
     * @return [array|false]     [If rooms found returns array containing rooms ids else returns false]
     */
    public function getCustomerIdRoomsByIdOrderIdProduct($id_order, $id_product, $date_from, $date_to)
    {
        return Db::getInstance()->executeS(
            'SELECT `id_room` FROM `'._DB_PREFIX_.'htl_booking_detail`
            WHERE `id_order`='.(int)$id_order.' AND `id_product`='.(int)$id_product.
            ' AND `date_from`=\''.pSQL($date_from).'\' AND `date_to`= \''.pSQL($date_to).'\''
        );
    }

    /**
     * [getBookedRoomsByIdOrderDetail returns booking information of room type by id_order_detail]
     * @param  [int] $id_order_detail [id_order_detail from 'order_detail' table]
     * @param  [int] $id_product      [id of the product]
     * @return [array|false]          [If information found returns array containing info ids else returns false]
     */
    public function getBookedRoomsByIdOrderDetail($id_order_detail, $id_product)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_booking_detail`
            WHERE `id_order_detail`='.(int)$id_order_detail.' AND `id_product`='.(int)$id_product
        );
    }

    /**
     * [createQloAppsBookingByChannels create booking on QloApps commig from different channels]
     * @param  [array] $params [array containing details of orders]
     * @return [boolean] [true if order is created or returns false]
     */
    public function createQloAppsBookingByChannels($params)
    {
        $this->errors = array();
        $result['status'] = 'failed';
        if ($params) {
            $customerId = $this->createQloCustomerChannelCustomerInfo($params);
            if ($customerId) {
                $params['id_customer'] = $customerId;
                $idAddress = $this->createQloCustomerAddressByChannelCustomerInfo($params);
                if ($idAddress) {
                    $params['id_address'] = $idAddress;
                    $idCart = $this->createQloCartForBookingFromChannel($params);
                    $params['id_cart'] = $idCart;
                    if ($idCart) {
                        $idOrder = $this->ProcessCreateQloOrderForChannelBooking($params);
                        if ($idOrder) {
                            $result['status'] = 'success';
                            $result['id_order'] = $idOrder;
                            return $result;
                        } else {
                            $this->errors[] = $this->moduleInstance->l('Some error occurred while creating order', 'HotelBookingDetail');
                        }
                    } else {
                        $this->errors[] = $this->moduleInstance->l('Some error occurred while creating cart', 'HotelBookingDetail');
                    }
                } else {
                    $this->errors[] = $this->moduleInstance->l('Some error occurred while creating customer address', 'HotelBookingDetail');
                }
            } else {
                $this->errors[] = $this->moduleInstance->l('Some error occurred while creating customer.', 'HotelBookingDetail');
            }
        }
        if ($result['status'] == 'failed') {
            $result['errors'] = $this->errors;
        }
    }

    /**
     * [createQloCustomerChannelCustomerInfo create customer in QloApps from supplied information from channel manager]
     * @param  [array] $params [array containg customer information]
     * @return [int|false]     [return customer Id if customer created successfully else returns false]
     */
    public function createQloCustomerChannelCustomerInfo($params)
    {
        if ($params) {
            $customer_id = 0;
            $firstName = $params['fname'];
            $lastName = $params['lname'];
            $customeremail = $firstName.$lastName.'@'.$params['channel_name'].'.com';
            $customer_dtl = Customer::getCustomersByEmail($customeremail);

            if (!$customer_dtl) {
                $channelName = $params['channel_name'];
                $objCustomer = new Customer();
                $objCustomer->firstname = $firstName;
                $objCustomer->lastname = $lastName;
                $objCustomer->email = $customeremail;
                $objCustomer->passwd = 'qloChannelCustomer';
                $objCustomer->save();
                $this->context->customer = $objCustomer;
                $customerId = $objCustomer->id;
            } else {
                $customerId = $customer_dtl[0]['id_customer']; //if already exist customer
            }
            return $customerId;
        }
        return false;
    }

    /**
     * [createQloCustomerAddressByChannelCustomerInfo create customer's Address in QloApps from supplied information from channel manager]
     * @param  [array] $params [array containg customer information]
     * @return [int|false]     [return customer address Id if address created successfully else returns false]
     */
    public function createQloCustomerAddressByChannelCustomerInfo($params)
    {
        $customerId = $params['id_customer'];
        if ($customerId) {
            $firstName = $params['fname'];
            $lastName = $params['lname'];
            //Create customer address
            $objCustomerAddress = new Address();
            $objCustomerAddress->id_country = Country::getByIso('US');
            $objCustomerAddress->id_state = State::getIdByIso('NY');
            $objCustomerAddress->id_customer = $customerId;
            $objCustomerAddress->alias = 'My Dummy address';
            $objCustomerAddress->lastname = $lastName;
            $objCustomerAddress->firstname = $firstName;
            $objCustomerAddress->address1 = 'New York, US';
            $objCustomerAddress->postcode = '10001';
            $objCustomerAddress->city = 'New York';
            $objCustomerAddress->phone_mobile = '0987654321';
            $objCustomerAddress->save();
            return $objCustomerAddress->id;
        }
        return false;
    }

    /**
     * [createQloCartForBookingFromChannel create cart in QloApps from supplied cart information from channel manager]
     * @param  [array] $params [array containg channel cart information]
     * @return [int|false]     [return cart Id if cart created successfully else returns false]
     */
    public function createQloCartForBookingFromChannel($params)
    {
        $this->context = Context::getContext();
        if ($params) {
            if (!isset($this->context->cookie->id_guest)) {
                Guest::setNewGuest($this->context->cookie);
            }
            $this->context->cart = new Cart();
            $idCustomer = (int)$params['id_customer'];
            $customer = new Customer((int)$idCustomer);
            $this->context->customer = $customer;
            $this->context->cart->id_customer = $idCustomer;
            if (Validate::isLoadedObject($this->context->cart) && $this->context->cart->OrderExists()) {
                return;
            }
            if (!$this->context->cart->secure_key) {
                $this->context->cart->secure_key = $this->context->customer->secure_key;
            }
            if (!$this->context->cart->id_shop) {
                $this->context->cart->id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');
            }
            if (!$this->context->cart->id_lang) {
                $this->context->cart->id_lang = Configuration::get('PS_LANG_DEFAULT');
            }
            if (!$this->context->cart->id_currency) {
                $this->context->cart->id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
            }

            $addresses = $customer->getAddresses((int)$this->context->cart->id_lang);

            if (!$this->context->cart->id_address_invoice && isset($addresses[0])) {
                $this->context->cart->id_address_invoice = (int)$addresses[0]['id_address'];
            }
            if (!$this->context->cart->id_address_delivery && isset($addresses[0])) {
                $this->context->cart->id_address_delivery = $addresses[0]['id_address'];
            }
            $this->context->cart->setNoMultishipping();

            if ($this->context->cart->save()) {
                return $this->context->cart->id;
            }
        }
        return false;
    }

    /**
     * [ProcessCreateQloOrderForChannelBooking create order for the booking created in the channel manager]
     * @param  [array] $params [array containg channel cart information]
     * @return [int|false]     [return order Id if order created successfully else returns false]
     */
    public function ProcessCreateQloOrderForChannelBooking($params)
    {
        $this->context = Context::getContext();
        $this->errors = array();
        $id_cart = $params['id_cart'];
        $date_from = date("Y-m-d", strtotime($params['date_from']));
        $date_to = date("Y-m-d", strtotime($params['date_to']));
        $id_product = $params['id_room_type'];

        $objBookingDetail = new HotelBookingDetail();
        $num_day = $objBookingDetail->getNumberOfDays($date_from, $date_to); //quantity of product
        $product = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'));
        $obj_room_type = new HotelRoomType();
        $room_info_by_id_product = $obj_room_type->getRoomTypeInfoByIdProduct($id_product);
        if ($room_info_by_id_product) {
            $id_hotel = $room_info_by_id_product['id_hotel'];

            if ($id_hotel) {
                /*Check Order restrict condition before adding in to cart*/
                $max_order_date = HotelOrderRestrictDate::getMaxOrderDate($id_hotel);
                if ($max_order_date) {
                    if (strtotime('-1 day', strtotime($max_order_date)) < strtotime($date_from)
                        || strtotime($max_order_date) < strtotime($date_to)
                    ) {
                        $max_order_date = date('Y-m-d', strtotime($max_order_date));
                        $this->errors[] = $this->moduleInstance->l('You can\'t Book room after date ', 'HotelBookingDetail').$max_order_date;
                    }
                }
                /*END*/
                $objBookingDetail = new HotelBookingDetail();
                $bookingParams = array(
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'hotel_id' => $id_hotel,
                    'id_room_type' => $id_product,
                    'only_search_data' => 1,
                    'id_cart' => $id_cart,
                    'id_guest' => $this->context->cookie->id_guest,
                );
                $hotel_room_data = $objBookingDetail->dataForFrontSearch($bookingParams);
                $total_available_rooms = $hotel_room_data['stats']['num_avail'];

                if ($total_available_rooms < $params['req_qty']) {
                    $this->errors[] = $this->moduleInstance->l('Required number of rooms are not available', 'HotelBookingDetail');
                }
            } else {
                $this->errors[] = $this->moduleInstance->l('Hotel Not found.', 'HotelBookingDetail');
            }
        } else {
            $this->errors[] = $this->moduleInstance->l('Rooms not found for this product.', 'HotelBookingDetail');
        }
        if (!count($this->errors)) {
            $unit_price = Product::getPriceStatic($id_product, HotelBookingDetail::useTax(), null, 6, null, false, true, $num_day*$params['req_qty']);

            $direction = 'up';

            $update_quantity = $this->context->cart->updateQty($num_day*$params['req_qty'], $id_product, null, false, $direction);

            /*
            * To add Rooms in hotel cart
            */
            $id_customer = $this->context->cart->id_customer;
            $id_currency = $this->context->cart->id_currency;

            $hotel_room_info_arr = $hotel_room_data['rm_data'][$id_product]['data']['available'];
            $chkQty = 0;
            foreach ($hotel_room_info_arr as $key_hotel_room_info => $val_hotel_room_info) {
                if ($chkQty < $params['req_qty']) {
                    $obj_htl_cart_booking_data = new HotelCartBookingData();
                    $obj_htl_cart_booking_data->id_cart = $this->context->cart->id;
                    $obj_htl_cart_booking_data->id_guest = $this->context->cookie->id_guest;
                    $obj_htl_cart_booking_data->id_customer = $id_customer;
                    $obj_htl_cart_booking_data->id_currency = $id_currency;
                    $obj_htl_cart_booking_data->id_product = $val_hotel_room_info['id_product'];
                    $obj_htl_cart_booking_data->id_room = $val_hotel_room_info['id_room'];
                    $obj_htl_cart_booking_data->id_hotel = $val_hotel_room_info['id_hotel'];
                    $obj_htl_cart_booking_data->booking_type = HotelBookingDetail::ALLOTMENT_AUTO;
                    $obj_htl_cart_booking_data->quantity = $num_day;
                    $obj_htl_cart_booking_data->date_from = $date_from;
                    $obj_htl_cart_booking_data->date_to = $date_to;
                    $obj_htl_cart_booking_data->save();
                    ++$chkQty;
                } else {
                    break;
                }
            }
            $channelOrderPayment = new ChannelOrderPayment();
            $total_amount = (float)$this->context->cart->getOrderTotal(true, Cart::BOTH);
            //$this->module = Module::getInstanceByName('hotelreservationsystem');
            $orderCreated = $channelOrderPayment->validateOrder((int) $this->context->cart->id, (int) 2, (float) $total_amount, 'Channel Manager Booking', null, array(), null, false, $this->context->cart->secure_key);
            if ($orderCreated) {
                $idOrder = Order::getOrderByCartId($this->context->cart->id);
                $order = new Order($idOrder);
                $order->source = 'Channel Manager Booking';
                if ($idOrder) {
                    return $idOrder;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public function deleteHotelOrderInfo($id_order)
    {
        $cartTable = 'htl_cart_booking_data';
        $orderTable = 'htl_booking_detail';
        $condition = 'id_order = '.(int)$id_order;
        if (Db::getInstance()->delete($orderTable, $condition)) {
            return Db::getInstance()->delete($cartTable, $condition);
        }
        return false;
    }

    public function deleteHotelOrderRoomInfo($id_order, $id_product, $id_room)
    {
        $cartTable = 'htl_cart_booking_data';
        $orderTable = 'htl_booking_detail';
        $condition = 'id_order = '.(int)$id_order.' AND id_product = '.(int)$id_product.' AND id_room = '.(int)$id_room;
        if (Db::getInstance()->delete($orderTable, $condition)) {
            return Db::getInstance()->delete($cartTable, $condition);
        }
        return false;
    }

    public function deleteHotelOrderRoomTypeInfo($id_order, $id_product)
    {
        $cartTable = 'htl_cart_booking_data';
        $orderTable = 'htl_booking_detail';
        $condition = 'id_order = '.(int)$id_order.' AND id_product = '.(int)$id_product;
        if (Db::getInstance()->delete($orderTable, $condition)) {
            return Db::getInstance()->delete($cartTable, $condition);
        }
        return false;
    }

    public function enterHotelOrderBookingFormCartBookingData($id_cart)
    {
        $cart = new Cart($id_cart);
        $objCartBooking = new HotelCartBookingData();
        $objHtlBooking = new HotelBookingDetail();

        $cart_products = $cart->getProducts();
        foreach ($cart_products as $product) {
            $objCartBooking = new HotelCartBookingData();
            $htlCartBookingData = $objCartBooking->getOnlyCartBookingData($cart->id, $cart->id_guest, $product['id_product']);
            if ($htlCartBookingData) {
                foreach ($htlCartBookingData as $cartBooking) {
                    $objCartBooking = new HotelCartBookingData($cartBooking['id']);
                    $objCartBooking->id_order = $order->id;
                    $objCartBooking->id_customer = $cart->id_customer;
                    $objCartBooking->save();

                    $objHtlBooking = new HotelBookingDetail();
                    $id_order_detail = $objHtlBooking->getPsOrderDetailIdByIdProduct($product['id_product'], $order->id);
                    $objHtlBooking->id_product = $product['id_product'];
                    $objHtlBooking->id_order = $order->id;
                    $objHtlBooking->id_order_detail = $id_order_detail;
                    $objHtlBooking->id_cart = $cart->id;
                    $objHtlBooking->id_room = $objCartBooking->id_room;
                    $objHtlBooking->id_hotel = $objCartBooking->id_hotel;
                    $objHtlBooking->id_customer = $cart->id_customer;
                    $objHtlBooking->booking_type = $objCartBooking->booking_type;
                    $objHtlBooking->id_status = self::STATUS_ALLOTED;
                    $objHtlBooking->comment = $objCartBooking->comment;

                    // For Back Order(Because of cart lock)
                    if ($objCartBooking->is_back_order) {
                        $objHtlBooking->is_back_order = 1;
                    }

                    $total_price = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice($product['id_product'], $objCartBooking->date_from, $objCartBooking->date_to);
                    $objHtlBooking->date_from = $objCartBooking->date_from;
                    $objHtlBooking->date_to = $objCartBooking->date_to;
                    $objHtlBooking->total_price_tax_excl = $total_price['total_price_tax_excl'];
                    $objHtlBooking->total_price_tax_incl = $total_price['total_price_tax_incl'];
                    $objHtlBooking->save();
                }
            }
        }
        return true;
    }

    public function updateHotelOrderRoomDurationInfo($id_order, $id_product, $id_room, $update_params)
    {
        $cartTable = 'htl_cart_booking_data';
        $orderTable = 'htl_booking_detail';
        $condition = '`id_order` = '.(int)$id_order.' AND `id_product` = '.(int)$id_product.
        ' AND `id_room` = '.(int)$id_room;
        if (Db::getInstance()->update($orderTable, $update_params, $condition)) {
            return Db::getInstance()->update($cartTable, $update_params, $condition);
        }
        return false;
    }

    public function updateProductQuantityInPsOrderDetail($id_order, $id_product, $quantity)
    {
        if ($this->getPsOrderDetailsByProduct($id_product, $id_order)) {
            return Db::getInstance()->update('order_detail', array('product_quantity' => $quantity), '`id_order` = '.$id_order.' AND `product_id` = '.$id_product);
        } else {
            $order = new Order($id_order);
            $product = new Product($id_product, false, Context::getContext()->language->id);
            $orderDetail = new OrderDetail();
            $orderDetail->id_order = $id_order;

            $orderDetail->product_id = (int)$id_product;
            $orderDetail->product_name = $product->name;
            $orderDetail->product_price = $product->price;
            $orderDetail->product_attribute_id = 0;

            $orderDetail->product_quantity = (int)$quantity;
            $orderDetail->product_ean13 = $product->ean13;
            $orderDetail->product_upc = $product->upc;
            $orderDetail->product_reference = $product->reference;
            $orderDetail->product_supplier_reference = $product->supplier_reference;
            $orderDetail->product_weight = (float)$product->weight;
            $orderDetail->id_warehouse = 0;

            $product_quantity = (int)Product::getQuantity($orderDetail->product_id, $orderDetail->product_attribute_id);
            $orderDetail->product_quantity_in_stock = ($product_quantity - (int)$quantity < 0) ?
                $product_quantity : (int)$quantity;
            // Set order invoice id
            $orderDetail->id_order_invoice = 0;

            // Set shop id
            $orderDetail->id_shop = (int)$order->id_shop;

            // Add new entry to the table
            if ($orderDetail->save()) {
                return Db::getInstance()->update(
                    'htl_booking_detail',
                    array('id_order_detail' => $orderDetail->id_order_detail),
                    '`id_order` = '.(int)$id_order.' AND `id_product` = '.(int)$id_product
                );
            }
        }
        return false;
    }

    /**
     * [getCustomerRoomByIdOrderIdProduct :: To get array of rooms ids in the cart booked by a customer for a date range]
     * @param  [int] $id_order    [Id of the id_order]
     * @param  [int] $id_product [Id of the product]
     * @param  [date] $date_from [Start date of the booking]
     * @param  [date] $date_to   [End date of the booking]
     * @return [array|false]     [If rooms found returns array containing rooms ids else returns false]
     */
    public function getRowByIdOrderIdProductInDateRange($id_order, $id_product, $date_from, $date_to, $id_room = 0)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'htl_booking_detail`
            WHERE `id_order`='.(int)$id_order.' AND `id_product`='.(int)$id_product.
            ' AND `date_from`=\''.pSQL($date_from).'\' AND `date_to`= \''.pSQL($date_to).'\''.' AND `id_room`='.(int)$id_room
        );
    }

    public function getRoomBookingData($idRoom, $idOrder, $date_from, $date_to)
    {
        $sql = 'SELECT `id_product`, `id_order_detail`, `id_hotel`, `id_customer`, `booking_type`, `id_status`, `check_in`, `check_out`
                FROM `'._DB_PREFIX_.'htl_booking_detail`
                WHERE `id_order`='.(int)$idOrder.' AND `id_room`='.(int)$idRoom.'
                AND `date_from`=\''.pSQL($date_from).'\' AND `date_to`= \''.pSQL($date_to).'\'';

        return Db::getInstance()->getRow($sql);
    }

    public static function getAllHotelOrderStatus()
    {
        $moduleInstance = Module::getInstanceByName('hotelreservationsystem');

        $pages = array(
            'STATUS_ALLOTED' => array(
                'id_status' => self::STATUS_ALLOTED,
                'name' => $moduleInstance->l('Alloted', 'hotelreservationsystem')
            ),
            'STATUS_CHECKED_IN' => array(
                'id_status' => self::STATUS_CHECKED_IN,
                'name' => $moduleInstance->l('Checked In', 'hotelreservationsystem')
            ),
            'STATUS_CHECKED_OUT' => array(
                'id_status' => self::STATUS_CHECKED_OUT,
                'name' => $moduleInstance->l('Checked Out', 'hotelreservationsystem')
            ),
        );
        return $pages;
    }

    public static function getAllAllotmentTypes()
    {
        $moduleInstance = Module::getInstanceByName('hotelreservationsystem');
        $allotments = array(
            array(
                'id_allotment' => self::ALLOTMENT_AUTO,
                'name' => $moduleInstance->l('Auto Allotment', 'hotelreservationsystem')
            ),
            array(
                'id_allotment' => self::ALLOTMENT_MANUAL,
                'name' => $moduleInstance->l('Manual Allotment', 'hotelreservationsystem')
            ),
        );
        return $allotments;
    }

    // Webservice funcions
    public function getWsBookingExtraDemands()
    {
        return Db::getInstance()->executeS(
            'SELECT `id_booking_demand` as `id` FROM `'._DB_PREFIX_.'htl_booking_demands` WHERE `id_htl_booking` = '.(int)$this->id.' ORDER BY `id` ASC'
        );
    }

    public function getOrderStatusToFreeBookedRoom()
    {
        return (array(
            Configuration::get('PS_OS_CANCELED'),
            Configuration::get('PS_OS_REFUND'),
            Configuration::get('PS_OS_ERROR'),
        ));
    }
}