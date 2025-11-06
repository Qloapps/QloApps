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
    public $is_cancelled;
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
    public $adults;
    public $children;
    public $child_ages;
    public $planned_check_out;

    public $date_add;
    public $date_upd;
    protected $moduleInstance;
    const STATUS_ALLOTED = 1;
    const STATUS_CHECKED_IN = 2;
    const STATUS_CHECKED_OUT = 3;

    // booking allotment types
    const ALLOTMENT_AUTO = 1;
    const ALLOTMENT_MANUAL = 2;

    // Search algorithm: Exact room types reults, All room types
    const SEARCH_EXACT_ROOM_TYPE_ALGO = 1;
    const SEARCH_ALL_ROOM_TYPE_ALGO = 2;

    // Search Type: Occupancy wise search, Normal search
    const SEARCH_TYPE_OWS = 1;
    const SEARCH_TYPE_NORMAL = 2;

    //
    const PS_ROOM_UNIT_SELECTION_TYPE_OCCUPANCY = 1;
    const PS_ROOM_UNIT_SELECTION_TYPE_QUANTITY = 2;

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
            'is_cancelled' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
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
            'planned_check_out' => array('type' => self::TYPE_STRING, 'required' => true),
            'adults' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
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

    public function update($null_values = false)
    {
        $result = parent::update($null_values);

        // if automatic overbooking resolution is enabled
        if (Configuration::get('PS_OVERBOOKING_AUTO_RESOLVE')) {
            // if room is getting free and this room is not already in back order then resolve the overbookings for this free room
            // $this->is_cancelled == 1 is not checked because currently we always set is_refunded to 1 when room is free
            // $this->is_back_order == 0 is checked because $this->is_back_order == 1 is used as room is free
            if ($this->is_refunded == 1 && $this->is_back_order == 0) {
                $this->resolveOverBookings();
            }
        }

        return $result;
    }

    public function getBookingDataParams(&$params)
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
        if (!isset($params['hourly_booking'])) {
            $params['hourly_booking'] = false;
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

        if (!isset($params['amenities'])) {
            $params['amenities'] = 0;
        }
        if (!isset($params['price'])) {
            $params['price'] = 0;
        }

        if (!isset($params['only_search_data'])) {
            $params['only_search_data'] = 0;
        }

        if (!isset($params['full_detail'])) {
            $params['full_detail'] = 0;
        }

        Hook::exec('actionBookingDataParamsModifier', array('params' => &$params));

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
        $context = Context::getContext();

        // extract all keys and values of the array [$params] into variables and values
        extract($this->getBookingDataParams($params));

        if ($date_from && $date_to && $hotel_id) {
            $date_from = date('Y-m-d H:i:s', strtotime($date_from));
            $stayStartDate = date('Y-m-d', strtotime($date_from));
            $date_to = date('Y-m-d H:i:s', strtotime($date_to));

            $objRoomType = new HotelRoomType();
            $lengthOfStay = HotelHelper::getNumberOfDays($date_from, $date_to);

            // Check LOS restriction for back-office
            $applyLosRestriction = true;
            if (isset($context->employee->id)) {
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
                    $searchParams = array(
                        'idHotel' => $hotel_id,
                        'dateFrom' => $date_from,
                        'dateTo' => $date_to,
                        'idRoomType' => $id_room_type,
                        'allowedIdRoomTypes' => $allowedIdRoomTypes,
                        'applyLosRestriction' => $applyLosRestriction,
                        'allParams' => $params,
                    );
                    $unavailRoomTypes = $this->getSearchUnavailableRooms($searchParams);
                }

                // Cart Rooms
                if ($search_cart_rms) {
                    $searchParams = array(
                        'idHotel' => $hotel_id,
                        'idCart' => $id_cart,
                        'idGuest' => $id_guest,
                        'idRoomType' => $id_room_type,
                        'allowedIdRoomTypes' => $allowedIdRoomTypes,
                        'allParams' => $params,
                    );
                    $cartRoomTypes = $this->getSearchCartRooms($searchParams);
                }

                // Available Rooms
                if ($search_available) {
                    $searchParams = array(
                        'idHotel' => $hotel_id,
                        'dateFrom' => $date_from,
                        'dateTo' => $date_to,
                        'idCart' => $id_cart,
                        'idGuest' => $id_guest,
                        'idRoomType' => $id_room_type,
                        'searchOccupancy' => $occupancy,
                        'allowedIdRoomTypes' => $allowedIdRoomTypes,
                        'applyLosRestriction' => $applyLosRestriction,
                        'hourlyBooking' => $hourly_booking,
                        'allParams' => $params,
                    );
                    $availableRoomTypes = $this->getSearchAvailableRooms($searchParams);
                    if ($availableRoomTypes['unavailableRoomTypes'] && $search_unavai) {
                        // format unavailable room types
                        foreach ($availableRoomTypes['unavailableRoomTypes'] as $idProduct => $roomTypeDetail) {
                            foreach ($roomTypeDetail as $idRoom => $roomDetail) {
                                if (!isset($unavailRoomTypes[$idProduct][$idRoom])) {
                                    $unavailRoomTypes[$idProduct][$idRoom] = array(
                                        'id_product' => $roomDetail['id_product'],
                                        'id_room' => $roomDetail['id_room'],
                                        'id_hotel' => $roomDetail['id_hotel'],
                                        'room_num' => $roomDetail['room_num'],
                                        'detail' => array()
                                    );
                                }
                                $unavailRoomTypes[$idProduct][$idRoom]['detail'][] = array(
                                    'id_status' => $roomDetail['id_status'],
                                    'room_comment' => $roomDetail['room_comment']
                                );
                            }
                        }
                    }

                    $availableRoomTypes = $availableRoomTypes['availableRoomTypes'];
                }

                // Booked Rooms
                if ($search_booked) {
                    $searchParams = array(
                        'idHotel' => $hotel_id,
                        'dateFrom' => $date_from,
                        'dateTo' => $date_to,
                        'idRoomType' => $id_room_type,
                        'allowedIdRoomTypes' => $allowedIdRoomTypes,
                        'allParams' => $params,
                    );
                    $bookedRoomTypes = $this->getSearchBookedRooms($searchParams);
                }

                // Partially available Rooms
                if ($search_partial) {
                    $searchParams = array(
                        'idHotel' => $hotel_id,
                        'dateFrom' => $date_from,
                        'dateTo' => $date_to,
                        'idCart' => $id_cart,
                        'idGuest' => $id_guest,
                        'idRoomType' => $id_room_type,
                        'searchOccupancy' => $occupancy,
                        'allowedIdRoomTypes' => $allowedIdRoomTypes,
                        'hourlyBooking' => $hourly_booking,
                        'allParams' => $params,
                    );

                    $partiallyAvailRoomTypes = $this->getSearchPartiallyAvailRooms($searchParams);

                    // Unavailable rooms and booked rooms are already included in there respective search
                    // So, no need to seperate them here
                    $partiallyAvailRoomsCount = $partiallyAvailRoomTypes['partiallyAvailRoomsCount'];
                    $partiallyAvailRoomTypes = $partiallyAvailRoomTypes['partiallyAvailRooms'];
                }

                // Now we will formate the data after geting search result according to search type
                $roomTypesDetail = $objRoomType->getRoomTypeDetailByRoomTypeIds($allowedIdRoomTypes, true, $full_detail);
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

                        $roomTypeSearchData = array_merge(
                            $roomTypeDetail,
                            array(
                                'id_product' => $idProduct,
                                'data' => array(),
                            )
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

                    Hook::exec('actionBookingDataResultModifier', array('params' => $params, 'final_search_response' => &$finalSearchResponse));

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
     *      'allParams' => ...,
     * );
     */
    protected function getSearchUnavailableRooms($params)
    {
        $context = Context::getContext();
        // Check LOS restriction for back-office
        if (!isset($params['applyLosRestriction'])) {
            $applyLosRestriction = true;
            if (isset($context->employee->id)) {
                if (!Configuration::get('PS_LOS_RESTRICTION_BO')) {
                    $applyLosRestriction = false;
                }
            }
        }

        extract($params);

        $lengthOfStay = HotelHelper::getNumberOfDays($dateFrom, $dateTo);
        $stayStartDate = date('Y-m-d', strtotime($dateFrom));

        // Room status inactive
        $sql = array();
        $sql[] = 'SELECT `id` AS `id_room`, `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment`, `id_status`, NULL AS `date_from`, NULL AS `date_to`
                FROM `'._DB_PREFIX_.'htl_room_information`
                WHERE `id_hotel`='.(int)$idHotel.' AND `id_status` = '. HotelRoomInformation::STATUS_INACTIVE.' AND IF('.(int)$idRoomType.' > 0, `id_product` = '.(int)$idRoomType.', 1) AND `id_product` IN ('.$allowedIdRoomTypes.')';

        // check room is temperory inactive
        $sql[] = 'SELECT hri.`id` AS `id_room`, hri.`id_product`, hri.`id_hotel`, hri.`room_num`, hri.`comment` AS `room_comment`, hri.`id_status`, hrdd.`date_from` AS `date_from`, hrdd.`date_to` AS `date_to`
                FROM `'._DB_PREFIX_.'htl_room_information` AS hri
                INNER JOIN `'._DB_PREFIX_.'htl_room_disable_dates` AS hrdd ON (hrdd.`id_room_type` = hri.`id_product` AND hrdd.	id_room = hri.`id`)
                WHERE hri.`id_hotel`='.$idHotel.' AND hri.`id_status` = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .' AND (
                    (hrdd.`date_from` <= \''.pSQL($dateFrom).'\' AND hrdd.`date_to` > \''.pSQL($dateFrom).'\' AND hrdd.`date_to` <= \''.pSQL($dateTo).'\') OR
                    (hrdd.`date_from` >= \''.pSQL($dateFrom).'\' AND hrdd.`date_to` > \''.pSQL($dateFrom).'\' AND hrdd.`date_to` <= \''.pSQL($dateTo).'\') OR
                    (hrdd.`date_from` >= \''.pSQL($dateFrom).'\' AND hrdd.`date_from` < \''.pSQL($dateTo).'\' AND hrdd.`date_to` >= \''.pSQL($dateTo).'\') OR
                    (hrdd.`date_from` <= \''.pSql($dateFrom).'\' AND hrdd.`date_to` >= \''.pSql($dateTo).'\')
                ) AND IF('.(int)$idRoomType.' > 0, hri.`id_product` = '.(int)$idRoomType.', 1) AND hri.`id_product` IN ('.$allowedIdRoomTypes.')';


        if ($applyLosRestriction) {
            $sql[] = 'SELECT hri.`id` AS `id_room`, hri.`id_product`, hri.`id_hotel`, hri.`room_num`, hri.`comment` AS `room_comment`, '.HotelRoomInformation::STATUS_SEARCH_LOS_UNSATISFIED.' AS `id_status`, NULL AS `date_from`, NULL AS `date_to`
                    FROM `'._DB_PREFIX_.'htl_room_information` AS hri
                    INNER JOIN `'._DB_PREFIX_.'htl_room_type` AS hrt ON (hrt.`id_product` = hri.`id_product`)
                    LEFT JOIN `'._DB_PREFIX_.'htl_room_type_restriction_date_range` AS hrtr ON (hrt.`id_product` = hrtr.`id_product` AND (hrtr.`date_from` <= \''.pSQL($stayStartDate).'\' AND hrtr.`date_to` > \''.pSQL($stayStartDate).'\'))
                    WHERE hri.`id_hotel`='.(int)$idHotel.' AND (IFNULL(hrtr.`min_los`, hrt.`min_los`) >'. (int)$lengthOfStay.' OR IF(IFNULL(hrtr.`max_los`, hrt.`max_los`) > 0, IFNULL(hrtr.`max_los`, hrt.`max_los`) < '.(int)$lengthOfStay.', 0)) AND IF('.(int)$idRoomType.' > 0, hri.`id_product` = '.(int)$idRoomType.', 1) AND hri.`id_product` IN ('.$allowedIdRoomTypes.')';

        }

        Hook::exec('actionUnavailRoomSearchSqlModifier', array(
            'sql' => &$sql,
            'params' => array(
                'id_hotel' => $idHotel,
                'id_product' => $idRoomType,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'all_params' => $allParams
            )
        ));

        $sql = implode(' UNION ', $sql);
        $sql .= ' ORDER BY `id_room`, `date_from` ASC';

        $unavailRoomTypes = array();
        if ($unavailRooms = Db::getInstance()->executeS($sql)) {
            foreach ($unavailRooms as $unavailRoomDetail) {
                if (!isset($unavailRoomTypes[$unavailRoomDetail['id_product']][$unavailRoomDetail['id_room']])) {
                    $unavailRoomTypes[$unavailRoomDetail['id_product']][$unavailRoomDetail['id_room']] = array(
                        'id_product' => $unavailRoomDetail['id_product'],
                        'id_room' => $unavailRoomDetail['id_room'],
                        'id_hotel' => $unavailRoomDetail['id_hotel'],
                        'room_num' => $unavailRoomDetail['room_num'],
                        'detail' => array()
                    );
                }
                $unavailRoomTypes[$unavailRoomDetail['id_product']][$unavailRoomDetail['id_room']]['detail'][] = array(
                    'id_status' => $unavailRoomDetail['id_status'],
                    'room_comment' => $unavailRoomDetail['room_comment'],
                    'date_from' => $unavailRoomDetail['date_from'],
                    'date_to' => $unavailRoomDetail['date_to'],
                );
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
     *      'allParams' => ...,
     *  );
     */
    protected function getSearchCartRooms($params)
    {
        extract($params);

        $cartRoomTypes = array();
        if ($idCart && $idGuest) {
            $selectCartRoomSearch = 'SELECT cbd.`id_product`, cbd.`id_room`, cbd.`id_hotel`, cbd.`booking_type`,
                cbd.`comment`, rf.`room_num`, cbd.`date_from`, cbd.`date_to`';
            $joinCartRoomSearch = 'INNER JOIN `'._DB_PREFIX_.'htl_room_information` AS rf ON (rf.`id` = cbd.`id_room`)';
            $whereCartRoomSearch = 'WHERE cbd.`id_hotel`= '.(int)$idHotel.' AND cbd.`id_cart` = '.(int)$idCart.'
                AND cbd.`id_guest` ='.(int)$idGuest.' AND cbd.`is_refunded` = 0 AND cbd.`is_back_order` = 0
                AND IF('.(int)$idRoomType.' > 0, rf.`id_product` = '.(int)$idRoomType.', 1) AND rf.`id_product`
                IN ('.$allowedIdRoomTypes.')';
            $groupByCartRoomSearch = '';
            $orderByCartRoomSearch = '';
            $orderWayCartRoomSearch = '';

            Hook::exec('actionCartRoomSearchSqlModifier', array(
                'select' => $selectCartRoomSearch,
                'join' => &$joinCartRoomSearch,
                'where' => &$whereCartRoomSearch,
                'group_by' => &$groupByCartRoomSearch,
                'order_by' => &$orderByCartRoomSearch,
                'order_way' => &$orderWayCartRoomSearch,
                'params' => array(
                    'id_hotel' => $idHotel,
                    'id_product' => $idRoomType,
                    'idCart' => $idCart,
                    'idGuest' => $idGuest,
                    'all_params' => $allParams
                )
            ));

            $sql = $selectCartRoomSearch;
            $sql .= ' FROM `'._DB_PREFIX_.'htl_cart_booking_data` AS cbd';
            $sql .= ' '.$joinCartRoomSearch;
            $sql .= ' '.$whereCartRoomSearch;
            $sql .= ' '.$groupByCartRoomSearch;
            $sql .= ' '.$orderByCartRoomSearch;
            $sql .= ' '.$orderWayCartRoomSearch;

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
     *      'idHotel' => ...,
     *      'dateFrom' => ...,
     *      'dateTo' => ...,
     *      'idCart' => ...,
     *      'idGuest' => ...,
     *      'idRoomType' => ...,
     *      'searchOccupancy' => ...,
     *      'allowedIdRoomTypes' => ...,
     *      'applyLosRestriction' => ...,
     *      'hourlyBooking' => ...,
     *      'allParams' => ...,
     * );
     */
    protected function getSearchAvailableRooms($params)
    {
        $context = Context::getContext();
        // Check LOS restriction for back-office
        if (!isset($params['applyLosRestriction'])) {
            $applyLosRestriction = true;
            if (isset($context->employee->id)) {
                if (!Configuration::get('PS_LOS_RESTRICTION_BO')) {
                    $applyLosRestriction = false;
                }
            }
        }

        extract($params);

        $stayStartDate = date('Y-m-d', strtotime($dateFrom));
        $lengthOfStay = HotelHelper::getNumberOfDays($dateFrom, $dateTo);

        if (isset($context->employee->id)) {
            $QLO_OWS_SEARCH_ALGO_TYPE = Configuration::get('PS_BACKOFFICE_OWS_SEARCH_ALGO_TYPE');
            $QLO_SEARCH_TYPE = Configuration::get('PS_BACKOFFICE_SEARCH_TYPE');
        } else {
            $QLO_OWS_SEARCH_ALGO_TYPE = Configuration::get('PS_FRONT_OWS_SEARCH_ALGO_TYPE');
            $QLO_SEARCH_TYPE = Configuration::get('PS_FRONT_SEARCH_TYPE');
        }

        // Exculde Booked rooms
        $excludeRoomId = array();
        $excludeRoomId['checked_out'] = 'SELECT `id_room`
        FROM `'._DB_PREFIX_.'htl_booking_detail`
        WHERE `id_hotel` = '.(int)$idHotel.' AND `is_back_order` = 0 AND `is_refunded` = 0 AND IF(`id_status` = '. self::STATUS_CHECKED_OUT.',
            IF('.(int) $hourlyBooking.', 1, (DATE_FORMAT(`check_out`,  "%Y-%m-%d") != DATE_FORMAT(\''.pSQL($dateFrom).'\',  "%Y-%m-%d")) AND (`check_out` > \''.pSQL($dateFrom).'\' AND `check_out` <= \''.PSQL($dateTo).'\')) AND (
                (`date_from` <= \''.pSQL($dateFrom).'\' AND `check_out` > \''.pSQL($dateFrom).'\' AND `check_out` <= \''.PSQL($dateTo).'\') OR
                (`date_from` >= \''.pSQL($dateFrom).'\' AND `check_out` > \''.pSQL($dateFrom).'\' AND `check_out` <= \''.pSQL($dateTo).'\') OR
                (`date_from` >= \''.pSQL($dateFrom).'\' AND `date_from` < \''.pSQL($dateTo).'\' AND `check_out` >= \''.pSQL($dateTo).'\') OR
                (`date_from` <= \''.pSQL($dateFrom).'\' AND `check_out` >= \''.pSQL($dateTo).'\')
        ), (
            (`date_from` <= \''.pSQL($dateFrom).'\' AND `date_to` > \''.pSQL($dateFrom).'\' AND `date_to` <= \''.PSQL($dateTo).'\') OR
            (`date_from` >= \''.pSQL($dateFrom).'\' AND `date_to` <= \''.pSQL($dateTo).'\') OR
            (`date_from` >= \''.pSQL($dateFrom).'\' AND `date_from` < \''.pSQL($dateTo).'\' AND `date_to` >= \''.pSQL($dateTo).'\') OR
            (`date_from` <= \''.pSQL($dateFrom).'\' AND `date_to` >= \''.pSQL($dateTo).'\')
        )) AND IF('.(int)$idRoomType.' > 0, `id_product` = '.(int)$idRoomType.', 1) AND `id_product` IN ('.$allowedIdRoomTypes.')';

        // Exclude temporary disable rooms
        $excludeRoomId['disabled'] = 'SELECT hri.`id` AS id_room
            FROM `'._DB_PREFIX_.'htl_room_information` AS hri
            INNER JOIN `'._DB_PREFIX_.'htl_room_disable_dates` AS hrdd ON (hrdd.`id_room_type` = hri.`id_product` AND hrdd.`id_room` = hri.`id`)
            WHERE hri.`id_hotel`='.(int)$idHotel.' AND hri.`id_status` = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .' AND (hrdd.`date_from` < \''.pSql($dateTo).'\' AND hrdd.`date_to` > \''.pSql($dateFrom).'\') AND IF('.(int)$idRoomType.' > 0, hri.`id_product` = '.(int)$idRoomType.', 1) AND hri.`id_product` IN ('.$allowedIdRoomTypes.')';

        // LOS Restriction. Also, Remember to check max LOS restriction is greater than zero
        if ($applyLosRestriction) {
            $excludeRoomId['length_of_stay'] = 'SELECT hri.`id` AS `id_room`
                FROM `'._DB_PREFIX_.'htl_room_information` AS hri
                INNER JOIN `'._DB_PREFIX_.'htl_room_type` AS hrt ON (hrt.`id_product` = hri.`id_product`)
                LEFT JOIN `'._DB_PREFIX_.'htl_room_type_restriction_date_range` AS hrtr ON (hrt.`id_product` = hrtr.`id_product` AND (hrtr.`date_from` <= \''.pSQL($stayStartDate).'\' AND hrtr.`date_to` > \''.pSQL($stayStartDate).'\'))
                WHERE hri.`id_hotel`='.(int)$idHotel.' AND (IFNULL(hrtr.`min_los`, hrt.`min_los`) >'. (int)$lengthOfStay.' OR IF(IFNULL(hrtr.`max_los`, hrt.`max_los`) > 0, IFNULL(hrtr.`max_los`, hrt.`max_los`) < '.(int)$lengthOfStay.', 0)) AND IF('.(int)$idRoomType.' > 0, hri.`id_product` = '.(int)$idRoomType.', 1) AND hri.`id_product` IN ('.$allowedIdRoomTypes.')';
        }
        // We will remove cart rooms after finally getting available rooms from booking

        $selectAvailRoomSearch = 'SELECT ri.`id` AS `id_room`, ri.`id_product`, ri.`id_hotel`, ri.`room_num`, ri.`comment` AS `room_comment`, hrt.`max_adults` AS max_adult, hrt.`max_children`, hrt.`max_guests` AS max_occupancy';

        $joinAvailRoomSearch = 'INNER JOIN `'._DB_PREFIX_.'htl_room_type` AS hrt ON (hrt.`id_product` = ri.`id_product`)';

        $whereAvailRoomSearch = 'WHERE ri.`id_hotel`='.(int)$idHotel.' AND ri.`id_status` != '. HotelRoomInformation::STATUS_INACTIVE.' AND IF('.(int)$idRoomType.' > 0, ri.`id_product` = '.(int)$idRoomType.', 1) AND ri.`id_product` IN ('.$allowedIdRoomTypes.')';

        $groupByAvailRoomSearch = '';
        $orderByAvailRoomSearch = 'ORDER BY ri.`id`';
        $orderWayAvailRoomSearch = 'ASC';

        Hook::exec('actionAvailRoomSearchSqlModifier', array(
            'select' => $selectAvailRoomSearch,
            'join' => &$joinAvailRoomSearch,
            'where' => &$whereAvailRoomSearch,
            'exclude_room_id' => &$excludeRoomId,
            'group_by' => &$groupByAvailRoomSearch,
            'order_by' => &$orderByAvailRoomSearch,
            'order_way' => &$orderWayAvailRoomSearch,
            'params' => array(
                'id_hotel' => $idHotel,
                'id_product' => $idRoomType,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'all_params' => $allParams
            )
        ));

        $sql = $selectAvailRoomSearch;
        $sql .= ' FROM `'._DB_PREFIX_.'htl_room_information` AS ri';
        $sql .= ' '.$joinAvailRoomSearch;
        $sql .= ' '.$whereAvailRoomSearch;
        $sql .= ' AND ri.`id` NOT IN ('.implode(' UNION ', $excludeRoomId).')';
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

                // segregating available and unavailable roomTypes
                $unavailableRoomTypes = $availableRoomTypes['unavailableRoomTypes'];
                if ($unavailableRoomTypes) {
                    // Adding an id_status key to the unavailable rooms array to identify rooms unavailable due to unsatisfied occupancy search criteria.
                    // if in case we need to add multiple keys then we can use array union operator
                    // Instead of looping here, we can do this in the getavailableRoomSatisfingOccupancy function
                    foreach ($unavailableRoomTypes as $idProduct => $roomTypeDetail) {
                        foreach ($roomTypeDetail as $idRoom => $roomDetail) {
                            $unavailableRoomTypes[$idProduct][$roomDetail['id_room']]['id_status'] = HotelRoomInformation::STATUS_SEARCH_OCCUPANCY_UNSATISFIED;
                        }
                    }
                }
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
                $totalReqOccupancy += (int)$reqRoomOccupancy['adults'] + (int)$reqRoomOccupancy['children'];
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
                        if (($reqRoomOccupancy['adults'] <= $roomsDetail['maxAdult']) &&
                        ($reqRoomOccupancy['children'] <= $roomsDetail['maxChildren']) &&
                        ((int)$reqRoomOccupancy['adults'] + (int)$reqRoomOccupancy['children'] <= $roomsDetail['maxOccupancy'])) {

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
                            (int)$reqRoomOccupancy['adults'] + (int)$reqRoomOccupancy['children'],
                            $reqRoomOccupancy['adults'],
                            $reqRoomOccupancy['children']
                        );

                        // In case avail rooms, required adults and required childers are same
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
     *      'hourlyBooking' => ...,
     *      'allParams' => ...,
     * );
     */
    protected function getSearchPartiallyAvailRooms($params)
    {
        extract($params);

        $sql = array();
        $sql[] = 'SELECT bd.`id_product`, bd.`id_room`, bd.`id_hotel`, bd.`id_customer`, bd.`booking_type`, bd.`id_status` AS booking_status, bd.`comment` AS `room_comment`, rf.`room_num`, bd.`date_from`, IF(bd.`id_status` = '. self::STATUS_CHECKED_OUT .', bd.`check_out`, bd.`date_to`) AS `date_to`, hrt.`max_adults` AS `max_adult`, hrt.`max_children`, hrt.`max_guests` AS `max_occupancy`
            FROM `'._DB_PREFIX_.'htl_booking_detail` AS bd
            INNER JOIN `'._DB_PREFIX_.'htl_room_information` AS rf ON (rf.`id` = bd.`id_room`)
            INNER JOIN `'._DB_PREFIX_.'htl_room_type` AS hrt ON (hrt.`id_product` = rf.`id_product`)
            WHERE bd.`id_hotel`='.(int)$idHotel.' AND rf.`id_status` != '. HotelRoomInformation::STATUS_INACTIVE .' AND bd.`is_back_order` = 0 AND bd.`is_refunded` = 0 AND IF(bd.`id_status` = '. self::STATUS_CHECKED_OUT .', IF('.(int) $hourlyBooking.', 1, (DATE_FORMAT(`check_out`,  "%Y-%m-%d") != DATE_FORMAT(\''.pSQL($dateFrom).'\',  "%Y-%m-%d")) AND (`check_out` > \''.pSQL($dateFrom).'\' AND `check_out` <= \''.PSQL($dateTo).'\')) AND (
                (bd.`date_from` <= \''.pSQL($dateFrom).'\' AND bd.`check_out` > \''.pSQL($dateFrom).'\' AND bd.`check_out` < \''.pSQL($dateTo).'\') OR
                (bd.`date_from` > \''.pSQL($dateFrom).'\' AND bd.`date_from` < \''.pSQL($dateTo).'\' AND bd.`check_out` >= \''.pSQL($dateTo).'\') OR
                (bd.`date_from` > \''.pSQL($dateFrom).'\' AND bd.`date_from` < \''.pSQL($dateTo).'\' AND bd.`check_out` > \''.pSQL($dateFrom).'\' AND bd.`check_out` < \''.pSQL($dateTo).'\')
            ), (
                (bd.`date_from` <= \''.pSQL($dateFrom).'\' AND bd.`date_to` > \''.pSQL($dateFrom).'\' AND bd.`date_to` < \''.pSQL($dateTo).'\') OR
                (bd.`date_from` > \''.pSQL($dateFrom).'\' AND bd.`date_from` < \''.pSQL($dateTo).'\' AND bd.`date_to` >= \''.pSQL($dateTo).'\') OR
                (bd.`date_from` > \''.pSQL($dateFrom).'\' AND bd.`date_from` < \''.pSQL($dateTo).'\' AND bd.`date_to` < \''.pSQL($dateTo).'\')
            )) AND IF('.(int)$idRoomType.' > 0, rf.`id_product` = '.(int)$idRoomType.', 1) AND rf.`id_product` IN ('.$allowedIdRoomTypes.')';

        $sql[] = 'SELECT hri.`id_product`, hrdd.`id_room`, hri.`id_hotel`, 0 AS `id_customer`, 0 AS `booking_type`, 0 AS `booking_status`, 0 AS `room_comment`, hri.`room_num`, hrdd.`date_from`, hrdd.`date_to`, hrt.`max_adults` AS `max_adult`, hrt.`max_children`, hrt.`max_guests` AS `max_occupancy`
            FROM `'._DB_PREFIX_.'htl_room_information` AS hri
            INNER JOIN `'._DB_PREFIX_.'htl_room_type` AS hrt ON (hrt.`id_product` = hri.`id_product`)
            INNER JOIN `'._DB_PREFIX_.'htl_room_disable_dates` AS hrdd ON (hrdd.`id_room_type` = hri.`id_product` AND hrdd.`id_room` = hri.`id`)
            WHERE hri.`id_hotel`='.(int)$idHotel.' AND hri.`id_status` = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .' AND (
                (hrdd.`date_from` <= \''.pSQL($dateFrom).'\' AND hrdd.`date_to` > \''.pSQL($dateFrom).'\' AND hrdd.`date_to` < \''.pSQL($dateTo).'\') OR
                (hrdd.`date_from` > \''.pSQL($dateFrom).'\' AND hrdd.`date_from` < \''.pSQL($dateTo).'\' AND hrdd.`date_to` >= \''.pSQL($dateTo).'\') OR
                (hrdd.`date_from` > \''.pSQL($dateFrom).'\' AND hrdd.`date_from` < \''.pSQL($dateTo).'\' AND hrdd.`date_to` < \''.pSQL($dateTo).'\')
            ) AND IF('.(int)$idRoomType.' > 0, hri.`id_product` = '.(int)$idRoomType.', 1) AND hri.`id_product` IN ('.$allowedIdRoomTypes.')';

        Hook::exec('actionPartiallyAvailRoomSearchSqlModifier', array(
            'sql' => &$sql,
            'params' => array(
                'id_hotel' => $idHotel,
                'id_product' => $idRoomType,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'all_params' => $allParams
            )
        ));

        $sql = implode(' UNION ', $sql);
        $sql .= ' ORDER BY id_room ASC';

        $part_arr = Db::getInstance()->executeS($sql);

        // Get date wise available rooms
        $dateWiseRoomTypes = array();

        // Below variables will be used for formating
        $partialAvailRoomType = array();
        $partialRoomsList = array();


        $unavailableRoomTypes = array();

        if ($part_arr) {
            // Occupancy Wise Search OR Normal Search
            if (isset($context->employee->id)) {
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
            unset($roomDetail);

            // If all dates are covered
            if (!$datesToCover) {
                // array in ascending order of dates
                ksort($dateWiseRoomTypes);

                $dateWiseCartRooms = array();
                // get cart partially available rooms
                if (!empty($idCart) && !empty($idGuest)) {
                    $sql = 'SELECT `id_product`, `id_room`, `date_from`
                        FROM `'._DB_PREFIX_.'htl_cart_booking_data`
                        WHERE `id_hotel` = '.(int)$idHotel.' AND `id_cart` = '.(int)$idCart.' AND `id_guest` = '.(int)$idGuest.' AND `is_refunded` = 0 AND  `is_back_order` = 0 AND IF('.(int)$idRoomType.' > 0, `id_product` = '.(int)$idRoomType.', 1) AND `id_product` IN ('.$allowedIdRoomTypes.') AND (';

                    $datetimeObj = new DateTime();
                    $countIteration = 0;
                    foreach ($timeStampWiseRooms as $timeStamp => $roomList) {
                        $countIteration++;

                        $datetimeObj->setTimestamp($timeStamp);
                        $partialDateFrom = $datetimeObj->format('Y-m-d H:i:s');

                        $datetimeObj->modify('+1 day');
                        $partialDateTo = $datetimeObj->format('Y-m-d H:i:s');

                        $sql .= '(`date_from` = \''.pSQL($partialDateFrom).'\' AND `date_to` = \''.pSQL($partialDateTo).'\' AND `id_room` IN ('.implode(",", $roomList).'))';

                        if (count($timeStampWiseRooms) > $countIteration) {
                            $sql .= ' OR ';
                        }
                    }
                    $sql .= ')';

                    unset($datetimeObj, $countIteration);

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
                foreach ($partiallyAvailRooms as $partAvailRoomDetail) {
                    $unavailableRoomTypes[$partAvailRoomDetail['id_product']][$partAvailRoomDetail['id_room']] = array(
                        'id_room' => $partAvailRoomDetail['id_room'],
                        'id_product' => $partAvailRoomDetail['id_product'],
                        'id_hotel' => $partAvailRoomDetail['id_hotel'],
                        'room_num' => $partAvailRoomDetail['room_num'],
                        'room_comment' => ''
                    );
                }

                $partiallyAvailRooms = array();
                $dateWiseRoomTypes = array();
            }

            unset($datesToCover, $timeStampWiseRooms, $partiallyAvailRooms);
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

        $selectBookedRoomSearch = 'SELECT `id`, `id_order`, `id_product`, `id_room`, `id_hotel`, `id_customer`, `booking_type`, `id_status` AS booking_status, `comment`, `room_num`, `date_from`, IF(`id_status` = '. self::STATUS_CHECKED_OUT.', `check_out`,`date_to`) AS `date_to`, `check_in`, `check_out`, `date_to` AS `booking_date_to`';
        $joinBookedRoomSearch = '';
        $whereBookedRoomSearch = 'WHERE `id_hotel` = '.(int)$idHotel.' AND `is_back_order` = 0 AND `is_refunded` = 0 AND IF(`id_status` = '. self::STATUS_CHECKED_OUT.', (
            (`date_from` <= \''.pSQL($dateFrom).'\' AND `check_out` > \''.pSQL($dateFrom).'\' AND `check_out` <= \''.PSQL($dateTo).'\') OR
            (`date_from` >= \''.pSQL($dateFrom).'\' AND `check_out` > \''.pSQL($dateFrom).'\' AND `check_out` <= \''.pSQL($dateTo).'\') OR
            (`date_from` >= \''.pSQL($dateFrom).'\' AND `date_from` < \''.pSQL($dateTo).'\' AND `check_out` >= \''.pSQL($dateTo).'\') OR
            (`date_from` <= \''.pSQL($dateFrom).'\' AND `check_out` >= \''.pSQL($dateTo).'\')
        ), (
            (`date_from` <= \''.pSQL($dateFrom).'\' AND `date_to` > \''.pSQL($dateFrom).'\' AND `date_to` <= \''.PSQL($dateTo).'\') OR
            (`date_from` >= \''.pSQL($dateFrom).'\' AND `date_to` <= \''.pSQL($dateTo).'\') OR
            (`date_from` >= \''.pSQL($dateFrom).'\' AND `date_from` < \''.pSQL($dateTo).'\' AND `date_to` >= \''.pSQL($dateTo).'\') OR
            (`date_from` <= \''.pSQL($dateFrom).'\' AND `date_to` >= \''.pSQL($dateTo).'\')
        )) AND IF('.(int)$idRoomType.' > 0, `id_product` = '.(int)$idRoomType.', 1) AND `id_product` IN ('.$allowedIdRoomTypes.')';
        $groupByBookedRoomSearch = '';
        $orderByBookedRoomSearch = 'ORDER BY `id_room`, `date_from`';
        $orderWayBookedRoomSearch = 'ASC';

        Hook::exec('actionBookedRoomSearchSqlModifier', array(
            'select' => $selectBookedRoomSearch,
            'join' => &$joinBookedRoomSearch,
            'where' => &$whereBookedRoomSearch,
            'group_by' => &$groupByBookedRoomSearch,
            'order_by' => &$orderByBookedRoomSearch,
            'order_way' => &$orderWayBookedRoomSearch,
            'params' => array(
                'id_hotel' => $idHotel,
                'id_product' => $idRoomType,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'all_params' => $allParams
            )
        ));

        $sql = $selectBookedRoomSearch;
        $sql .= ' FROM `'._DB_PREFIX_.'htl_booking_detail`';
        $sql .= ' '.$joinBookedRoomSearch;
        $sql .= ' '.$whereBookedRoomSearch;
        $sql .= ' '.$groupByBookedRoomSearch;
        $sql .= ' '.$orderByBookedRoomSearch;
        $sql .= ' '.$orderWayBookedRoomSearch;

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
                    'id_htl_booking' => $booked_v['id'],
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
        $sql = 'SELECT `id`, `id_product`, `id_order`, `id_cart`, `id_room`, `id_hotel`, `id_customer`,
        `check_out`, `check_in`, `id_status`
        FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `id_room` = '.(int)$id_room.
        ' AND `is_back_order` = 0 AND `is_refunded` = 0 AND ((date_from <= \''.pSQL($date_from).'\' AND date_to > \''.
        pSQL($date_from).'\' AND date_to <= \''.pSQL($date_to).'\') OR (date_from > \''.pSQL($date_from).
        '\' AND date_to < \''.pSQL($date_to).'\') OR (date_from >= \''.pSQL($date_from).'\' AND date_from < \''.
        pSQL($date_to).'\' AND date_to >= \''.pSQL($date_to).'\') OR (date_from < \''.pSQL($date_from).
        '\' AND date_to > \''.pSQL($date_to).'\'))';

        if ($this->id) {
            $sql .= ' AND `id` !='.(int) $this->id;
        }

        $sql .= ' ORDER BY `date_add` DESC';

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
     * @deprecated use HotelHelper::getNumberOfDays() instead
     */
    public function getNumberOfDays($dateFrom, $dateTo)
    {
        return HotelHelper::getNumberOfDays($dateFrom, $dateTo);
    }

    /**
     *
     * @param [date] $dateFrom [Start date of the booking]
     * @param [date] $dateTo   [End date of the booking]
     *
     * @return [int] [Returns number of days between two dates]
     * @deprecated use HotelHelper::getNumberOfDays() instead
     */
    public static function getDays($dateFrom, $dateTo)
    {
        return HotelHelper::getNumberOfDays($dateFrom, $dateTo);
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
     * [getBookingDataByOrderReference :: To get all room bookings by order reference].
     * @param [string] $orderReference [Reference of the order]
     * @return [array] [If data found Returns the array containing the information of the booking of an order reference]
     */
    public function getBookingDataByOrderReference($orderReference)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `id_order` IN ( SELECT `id_order` FROM `'._DB_PREFIX_.'orders` WHERE `reference` = "'.pSQL($orderReference).'" )'
        );
    }

    public static function getIdHotelByIdOrder($idOrder, $includeServiceProducts = true)
    {
        if (!$idHotel = Db::getInstance()->getValue(
            'SELECT `id_hotel` FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `id_order` = '.(int) $idOrder
        )) {
            if ($includeServiceProducts) {
                $idHotel = Db::getInstance()->getValue(
                    'SELECT `id_hotel` FROM `'._DB_PREFIX_.'service_product_order_detail` WHERE `id_order` = '.(int) $idOrder
                );
            }
        }

        return $idHotel;
    }

    /**
     * [updateBookingOrderStatusBYOrderId :: To update the order status of a room in the booking].
     * @param [int] $order_id   [Id of the order]
     * @param [int] $new_status [Id of the new status of the order to be updated]
     * @param [int] $id_room    [Id of the room which order status is to be ypdated]
     * @return [Boolean] [Returns true if successfully updated else returns false]
     */
    public function updateBookingOrderStatusByOrderId(
        $idOrder,
        $newStatus,
        $idRoom,
        $dateFrom,
        $dateTo,
        $statusDate = ''
    ) {
        $roomBookingData = $this->getRoomBookingData($idRoom, $idOrder, $dateFrom, $dateTo);
        $objHotelBookingDetail = new self($roomBookingData['id']);

        if (Validate::isLoadedObject($objHotelBookingDetail)) {
            if ($statusDate) {
                $statusDate = date('Y-m-d H:i:s', strtotime($statusDate));
            } else {
                $statusDate = date('Y-m-d H:i:s');
            }

            if ($newStatus == self::STATUS_CHECKED_IN) {
                $objHotelBookingDetail->id_status = $newStatus;
                $objHotelBookingDetail->check_in = ($statusDate > $dateTo ? $dateTo : $statusDate);
            } elseif ($newStatus == self::STATUS_CHECKED_OUT) {
                $objHotelBookingDetail->id_status = $newStatus;
                $objHotelBookingDetail->check_out = ($statusDate > $dateTo ? $dateTo : $statusDate);
            } else {
                $objHotelBookingDetail->id_status = $newStatus;
                $objHotelBookingDetail->check_in = '';
                $objHotelBookingDetail->check_out = '';
            }

            return $objHotelBookingDetail->save();
        }

        return false;
    }

    /**
     * [dataForFrontSearch ].
     *
     * @param [date] $date_from     [Start date of the booking]
     * @param [date] $date_to       [End date of the booking]
     * @param [int]  $id_hotel      [Id of the Hotel]
     * @param [int]  $id_product    [ID of the product]
     * @param [int]  $only_search_data [used for product page and category page for block cart]
     * @param [int]  $adults         []
     * @param [int]  $children      []
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
        $context = Context::getContext();

        if (!isset($bookingParams['search_available'])) {
            $bookingParams['search_available'] = 1;
        }
        if (!isset($bookingParams['search_partial'])) {
            $bookingParams['search_partial'] = 0;
        }
        if (!isset($bookingParams['search_booked'])) {
            $bookingParams['search_booked'] = 0;
        }
        if (!isset($bookingParams['search_unavai'])) {
            $bookingParams['search_unavai'] = 0;
        }

        $bookingParams['full_detail'] = 1;
        $bookingData = $this->getBookingData($bookingParams);

        extract($this->getBookingDataParams($bookingParams));

        if (!$only_search_data) {
            if (!empty($bookingData)) {
                foreach ($bookingData['rm_data'] as $key => $value) {
                    $product_feature = Product::getFrontFeaturesStatic($context->language->id, $value['id_product']);
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
                            continue;
                        }
                    }
                    $productFeaturePrice = HotelRoomTypeFeaturePricing::getRoomTypeFeaturePricesPerDay($value['id_product'], $date_from, $date_to, self::useTax(), 0, 0, 0, 0, 1, 1, $bookingParams['occupancy']);
                    if (!empty($price) && ($price['from'] > $productFeaturePrice || $price['to'] < $productFeaturePrice)) {
                        unset($bookingData['rm_data'][$key]);
                        continue;
                    }

                    if (count($value['data']['available']) || (isset($get_all_room_types) && $get_all_room_types)) {
                        $prod_price = Product::getPriceStatic($value['id_product'], self::useTax());
                        $productPriceWithoutReduction = HotelRoomTypeFeaturePricing::getRoomTypeFeaturePricesPerDay($value['id_product'], $date_from, $date_to, self::useTax(), 0, 0, 0, 0, 1, 0, $bookingParams['occupancy']);
                        $cover_image_arr = Product::getCover($value['id_product']);
                        if (!empty($cover_image_arr)) {
                            $cover_img = $context->link->getImageLink($value['link_rewrite'], $value['id_product'].'-'.$cover_image_arr['id_image'], 'home_default');
                        } else {
                            $cover_img = $context->link->getImageLink($value['link_rewrite'], $context->language->iso_code.'-default', 'home_default');
                        }
                        $bookingData['rm_data'][$key]['image'] = $cover_img;
                        $bookingData['rm_data'][$key]['feature'] = $product_feature;
                        $bookingData['rm_data'][$key]['price'] = $prod_price;
                        $bookingData['rm_data'][$key]['feature_price'] = $productFeaturePrice;
                        $bookingData['rm_data'][$key]['price_without_reduction'] = $productPriceWithoutReduction;
                        $bookingData['rm_data'][$key]['feature_price_diff'] = $productPriceWithoutReduction - $productFeaturePrice;
                        $bookingData['rm_data'][$key]['room_left'] = count($bookingData['rm_data'][$key]['data']['available']);

                        // create URL with the parameters from URL
                        $urlData = array ('date_from' => $date_from, 'date_to' => $date_to);
                        if (!isset($occupancy)) {
                            $occupancy = Tools::getValue('occupancy');
                        }
                        if ($occupancy) {
                            $urlData['occupancy'] = $occupancy;
                        }
                        if ($location = Tools::getValue('location')) {
                            $urlData['location'] = $location;
                        }

                        if (Configuration::get('PS_REWRITING_SETTINGS')) {
                            $bookingData['rm_data'][$key]['product_link'] = $context->link->getProductLink($value['id_product']).'?'.http_build_query($urlData);
                        } else {
                            $bookingData['rm_data'][$key]['product_link'] = $context->link->getProductLink($value['id_product']).'&'.http_build_query($urlData);
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
    public function getAvailableRoomsForReallocation($date_from, $date_to, $id_room_type, $hotel_id, $room_types_to_upgrade = 0)
    {
        $context = Context::getContext();
        if (isset($context->cookie->id_cart)) {
            $current_admin_cart_id = $context->cookie->id_cart;
        }
        $exclude_ids = 'SELECT `id_room` FROM `'._DB_PREFIX_.'htl_booking_detail`
            WHERE `date_from` < \''.pSQL($date_to).'\' AND `date_to` > \''.pSQL($date_from).'\'
            AND `is_refunded`=0 AND `is_back_order`=0
            UNION
            SELECT hri.`id` AS id_room
            FROM `'._DB_PREFIX_.'htl_room_information` AS hri
            INNER JOIN `'._DB_PREFIX_.'htl_room_disable_dates` AS hrdd ON (hrdd.`id_room_type` = hri.`id_product` AND hrdd.`id_room` = hri.`id`)
            WHERE hri.`id_hotel`='.(int)$hotel_id.($id_room_type ? ' AND `id_product` = '.(int)$id_room_type : '').'
            AND hri.`id_status` = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .'
            AND (hrdd.`date_from` <= \''.pSql($date_to).'\' AND hrdd.`date_to` >= \''.pSql($date_from).'\')';

        if (isset($current_admin_cart_id) && $current_admin_cart_id) {
            $sql = 'SELECT `id` AS `id_room`, `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment`
            FROM `'._DB_PREFIX_.'htl_room_information`
            WHERE `id_hotel`='.(int)$hotel_id.($id_room_type ? ' AND `id_product` = '.(int)$id_room_type : '').'
            AND (id_status = '. HotelRoomInformation::STATUS_ACTIVE .' or id_status = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .')
            AND `id` NOT IN ('.$exclude_ids.')
            AND `id` NOT IN (SELECT `id_room` FROM `'._DB_PREFIX_.'htl_cart_booking_data` WHERE `id_cart`='.
            (int)$current_admin_cart_id.')';
        } else {
            $sql = 'SELECT `id` AS `id_room`, `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment`
            FROM `'._DB_PREFIX_.'htl_room_information`
            WHERE `id_hotel`='.(int)$hotel_id.($id_room_type ? ' AND `id_product` = '.(int)$id_room_type : '').'
            AND (id_status = '. HotelRoomInformation::STATUS_ACTIVE .' or id_status = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .')
            AND `id` NOT IN ('.$exclude_ids.')';
        }

        if ($avail_rooms = Db::getInstance()->executeS($sql)) {
            // if requested for room type upgrade options also then get room type upgrade options
            if ($room_types_to_upgrade) {
                $availableRoomTypes = array();
                $context = Context::getContext();
                foreach ($avail_rooms as $roomInfo) {
                    $availableRoomTypes[$roomInfo['id_product']]['id_product'] = $roomInfo['id_product'];
                    $objProduct = new Product($roomInfo['id_product'], false, $context->language->id);
                    $availableRoomTypes[$roomInfo['id_product']]['room_type_name'] = $objProduct->name;
                    $availableRoomTypes[$roomInfo['id_product']]['rooms'][] = $roomInfo;
                }

                return $availableRoomTypes;
            }

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
        $sql = 'SELECT `id` as `id_hotel_booking`, `id_room`, `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment`
            FROM `'._DB_PREFIX_.'htl_booking_detail`
            WHERE `id_hotel` = '.(int)$hotel_id.' AND `id_product` = '.(int)$id_room_type.'
            AND `date_from` = \''.pSQL($date_from).'\' AND `date_to` = \''.pSQL($date_to).'\'
            AND `id_room`!='.(int)$id_room.' AND `is_refunded` = 0 AND `is_back_order` = 0';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * @deprecated : use reallocateBooking() instead
     * [reallocateRoomWithAvailableSameRoomType :: To reallocate rooms with available rooms in case of reallocation of the room].
     * @param [int]  $currentRoomId [id of the room to be reallocated]
     * @param [date] $dateFrom       [start date of the booking of the room]
     * @param [date] $dateTo         [end date of the booking of the room]
     * @param [date] $swappedRoomId [id of the room with which the $current_room_id will be reallocated]
     * @param [int]  $idOrder [id of the order]
     * @return [boolean] [true if rooms successfully reallocated else returns false]
     */
    public function reallocateRoomWithAvailableSameRoomType($currentRoomId, $dateFrom, $dateTo, $swappedRoomId, $idOrder = 0)
    {
        $result = false;

        $dateFrom = date('Y-m-d H:i:s', strtotime($dateFrom));
        $dateTo = date('Y-m-d H:i:s', strtotime($dateTo));

        if ($idHotelBooking = Db::getInstance()->getValue(
            'SELECT `id` FROM `'._DB_PREFIX_.'htl_booking_detail`
            WHERE date_from = \''.pSQL($dateFrom).'\'
            AND date_to = \''.pSQL($dateTo).'\'
            AND id_room = '.(int) $currentRoomId.
            ((int) $idOrder ? ' AND id_order = '.(int) $idOrder : '')
        )) {
            $result = $this->reallocateBooking($idHotelBooking, $swappedRoomId);
        }

        return $result;
    }

    /**
     * Reallocate the room in the booking with the available sent room
     * @param [int] $idHotelBooking : id of the hotel booking which room has to be reallocated
     * @param [int] $idRoom : id of the room which has to assigned in reallocation
     * @return boolean
     */
    public function reallocateBooking($idHotelBooking, $idRoom, $priceDiffTaxExcl = 0)
    {
        $result = true;
        $reallocatedBookingId = 0;
        // get the cart booking data for the given booking
        if (Validate::isLoadedObject($objOldHotelBooking = new HotelBookingDetail($idHotelBooking))) {
            $objectHotelBookingFrom = clone $objOldHotelBooking;
            $objHotelRoomInfo = new HotelRoomInformation($idRoom);
            $idNewRoomType = $objHotelRoomInfo->id_product;
            if ($objOldHotelBooking->id_product != $idNewRoomType) {
                $objOrder = new Order($objOldHotelBooking->id_order);
                $objOldOrderDetail = new OrderDetail($objOldHotelBooking->id_order_detail);

                $productQty = (int) HotelHelper::getNumberOfDays($objOldHotelBooking->date_from, $objOldHotelBooking->date_to);
                $oldRoomPriceTaxExcl = $objOldHotelBooking->total_price_tax_excl / $productQty;

                // Calculate new room price per qty
                $priceDiffPerQtyTaxExcl = $priceDiffTaxExcl / $productQty;
                $newRoomPriceTaxExcl = $oldRoomPriceTaxExcl + $priceDiffPerQtyTaxExcl;
                $newRoomPriceTaxExcl = $newRoomPriceTaxExcl < 0 ? 0 : $newRoomPriceTaxExcl;

                $totalRoomPriceTaxIncl = $objOldHotelBooking->total_price_tax_incl;
                $totalRoomPriceTaxExcl = $objOldHotelBooking->total_price_tax_excl;

                // ===============================================================
                // Start: Add Process of the old booking
                // ===============================================================
                // Total method
                $totalMethod = Cart::BOTH_WITHOUT_SHIPPING;
                // Create new cart
                $cart = new Cart();
                $cart->id_shop_group = $objOrder->id_shop_group;
                $cart->id_shop = $objOrder->id_shop;
                $cart->id_customer = $objOrder->id_customer;
                $cart->id_carrier = $objOrder->id_carrier;
                $cart->id_address_delivery = $objOrder->id_address_delivery;
                $cart->id_address_invoice = $objOrder->id_address_invoice;
                $cart->id_currency = $objOrder->id_currency;
                $cart->id_lang = $objOrder->id_lang;
                $cart->secure_key = $objOrder->secure_key;
                $cart->add();

                // Save context (in order to apply cart rule)
                $context = Context::getContext();
                $context->cart = $cart;
                $context->customer = new Customer($objOrder->id_customer);
                $context->currency = new Currency($objOrder->id_currency);

                // always add taxes even if not displayed to the customer
                $useTaxes = true;

                $initialProductPriceTE = Product::getPriceStatic(
                    $idNewRoomType,
                    $useTaxes,
                    null,
                    6,
                    null,
                    false,
                    true,
                    1,
                    false,
                    $objOrder->id_customer,
                    $cart->id,
                    $objOrder->id_address_tax
                );

                // create feature price if needed
                $createFeaturePrice = ($newRoomPriceTaxExcl != $initialProductPriceTE);
                if ($createFeaturePrice) {
                    $featurePriceParams = array();
                    $featurePriceParams = array(
                        'id_cart' => $context->cart->id,
                        'id_guest' => $context->cookie->id_guest,
                        'impact_value' => $newRoomPriceTaxExcl,
                        'id_product' => $idNewRoomType,
                    );
                }

                $bookingParams = array(
                    'date_from' => $objOldHotelBooking->date_from,
                    'date_to' => $objOldHotelBooking->date_to,
                    'hotel_id' => $objOldHotelBooking->id_hotel,
                    'id_room_type' => $idNewRoomType,
                    'only_search_data' => 1,
                );

                if ($roomAvailabilityInfo = $objOldHotelBooking->dataForFrontSearch($bookingParams)) {
                    if ($availableRooms = $roomAvailabilityInfo['rm_data'][$idNewRoomType]['data']['available']) {
                        $roomInfo = reset($availableRooms);
                        $objCartBookingData = new HotelCartBookingData();
                        $objCartBookingData->id_cart = $context->cart->id;
                        $objCartBookingData->id_guest = $context->cookie->id_guest;
                        $objCartBookingData->id_customer = $objOrder->id_customer;
                        $objCartBookingData->id_currency = $objOrder->id_currency;
                        $objCartBookingData->id_product = $roomInfo['id_product'];
                        $objCartBookingData->id_room = $roomInfo['id_room'];
                        $objCartBookingData->id_hotel = $roomInfo['id_hotel'];
                        $objCartBookingData->booking_type = $objOldHotelBooking->booking_type;
                        $objCartBookingData->comment = $objOldHotelBooking->comment;
                        $objCartBookingData->quantity = $productQty;
                        $objCartBookingData->date_from = $objOldHotelBooking->date_from;
                        $objCartBookingData->date_to = $objOldHotelBooking->date_to;
                        $objCartBookingData->adults = $objOldHotelBooking->adults;
                        $objCartBookingData->children = $objOldHotelBooking->children;
                        $objCartBookingData->child_ages = $objOldHotelBooking->child_ages;
                        $objCartBookingData->save();

                        // create feature price if needed
                        if ($createFeaturePrice) {
                            $featurePriceParams['id_room'] = $roomInfo['id_room'];
                            $featurePriceParams['restrictions'] = array(
                                array(
                                    'date_from' => $objOldHotelBooking->date_from,
                                    'date_to' => $objOldHotelBooking->date_to
                                )
                            );
                            HotelRoomTypeFeaturePricing::createRoomTypeFeaturePrice($featurePriceParams);
                        }
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }

                // Add product to cart
                $updateQuantity = $cart->updateQty(
                    $productQty,
                    $idNewRoomType,
                    null,
                    null,
                    'up',
                    0,
                    new Shop($cart->id_shop)
                );

                // If order is valid, we can create a new invoice or edit an existing invoice
                if ($objOrder->hasInvoice()) {
                    $objOrderInvoice = new OrderInvoice($objOldOrderDetail->id_order_invoice);
                    $objOrderInvoice->total_paid_tax_excl += (float)($cart->getOrderTotal(false, $totalMethod));
                    $objOrderInvoice->total_paid_tax_incl += (float)($cart->getOrderTotal($useTaxes, $totalMethod));
                    $objOrderInvoice->total_products += (float)$cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
                    $objOrderInvoice->total_products_wt += (float)$cart->getOrderTotal($useTaxes, Cart::ONLY_PRODUCTS);
                    $result &= $objOrderInvoice->update();
                }

                // Create Order detail information
                $objOrderDetail = new OrderDetail();
                $objOrderDetail->createList($objOrder, $cart, $objOrder->getCurrentOrderState(), $cart->getProducts(), (isset($objOrderInvoice->id) ? $objOrderInvoice->id : 0), $useTaxes, (int)Tools::getValue('add_product_warehouse'));

                // update totals amount of order
                $objOrder->total_products += (float)$cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
                $objOrder->total_products_wt += (float)$cart->getOrderTotal($useTaxes, Cart::ONLY_PRODUCTS);

                $objOrder->total_paid += (float)($cart->getOrderTotal(true, $totalMethod));
                $objOrder->total_paid_tax_excl += (float)($cart->getOrderTotal(false, $totalMethod));
                $objOrder->total_paid_tax_incl += (float)($cart->getOrderTotal($useTaxes, $totalMethod));

                // Save changes of order
                $result &= $objOrder->update();

                // Update Tax lines
                $objOrderDetail->updateTaxAmount($objOrder);

                $objRoomType = new HotelRoomType();
                $objBookingDetail = new HotelBookingDetail();
                $idNewOrderDetail = $objBookingDetail->getLastInsertedRoomIdOrderDetail($objOrder->id);
                $objCartBookingData = new HotelCartBookingData();
                if ($cartBookingsData = $objCartBookingData->getOnlyCartBookingData(
                    $context->cart->id,
                    $context->cart->id_guest,
                    $idNewRoomType
                )) {
                    foreach ($cartBookingsData as $cartBookingInfo) {
                        $objCartBookingData = new HotelCartBookingData($cartBookingInfo['id']);
                        $objCartBookingData->id_order = $objOrder->id;
                        $objCartBookingData->save();

                        $objBookingDetail = new HotelBookingDetail();
                        $objBookingDetail->id_product = $idNewRoomType;
                        $objBookingDetail->id_order = $objOrder->id;
                        $objBookingDetail->id_order_detail = $idNewOrderDetail;
                        $objBookingDetail->id_cart = $context->cart->id;
                        $objBookingDetail->id_room = $objCartBookingData->id_room;
                        $objBookingDetail->id_hotel = $objCartBookingData->id_hotel;
                        $objBookingDetail->id_customer = $objOrder->id_customer;
                        $objBookingDetail->booking_type = $objCartBookingData->booking_type;
                        $objBookingDetail->comment = $objCartBookingData->comment;
                        $objBookingDetail->id_status = 1;
                        $objBookingDetail->room_type_name = Product::getProductName($idNewRoomType, null, $objOrder->id_lang);
                        $objBookingDetail->date_from = $objCartBookingData->date_from;
                        $objBookingDetail->date_to = $objCartBookingData->date_to;
                        $objBookingDetail->adults = $objCartBookingData->adults;
                        $objBookingDetail->children = $objCartBookingData->children;
                        $objBookingDetail->child_ages = $objCartBookingData->child_ages;

                        $occupancy = array(
                            array(
                                'adults' => $objCartBookingData->adults,
                                'children' => $objCartBookingData->children,
                                'child_ages' => json_decode($objCartBookingData->child_ages)
                            )
                        );

                        $totalRoomTypePrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                            $idNewRoomType,
                            $objCartBookingData->date_from,
                            $objCartBookingData->date_to,
                            $occupancy,
                            Group::getCurrent()->id,
                            $context->cart->id,
                            $context->cookie->id_guest,
                            $objCartBookingData->id_room,
                            0
                        );
                        $objBookingDetail->total_price_tax_excl = $totalRoomTypePrice['total_price_tax_excl'];
                        $objBookingDetail->total_price_tax_incl = $totalRoomTypePrice['total_price_tax_incl'];
                        $objBookingDetail->total_paid_amount = $totalRoomTypePrice['total_price_tax_incl'];

                        // Save hotel information/location/contact
                        if (Validate::isLoadedObject($objRoom = new HotelRoomInformation($objCartBookingData->id_room))) {
                            $objBookingDetail->room_num = $objRoom->room_num;
                        }
                        if (Validate::isLoadedObject($objHotelBranch = new HotelBranchInformation(
                            $objCartBookingData->id_hotel,
                            $context->cart->id_lang
                        ))) {
                            $addressInfo = $objHotelBranch->getAddress($objCartBookingData->id_hotel);
                            $objBookingDetail->hotel_name = $objHotelBranch->hotel_name;
                            $objBookingDetail->city = $addressInfo['city'];
                            $objBookingDetail->state = State::getNameById($addressInfo['id_state']);
                            $objBookingDetail->country = Country::getNameById($context->cart->id_lang, $addressInfo['id_country']);
                            $objBookingDetail->zipcode = $addressInfo['postcode'];;
                            $objBookingDetail->phone = $addressInfo['phone'];
                            $objBookingDetail->email = $objHotelBranch->email;
                            $objBookingDetail->check_in_time = $objHotelBranch->check_in;
                            $objBookingDetail->check_out_time = $objHotelBranch->check_out;
                        }

                        if ($result &= $objBookingDetail->save()) {
                            $reallocatedBookingId = $objBookingDetail->id;
                            $objectHotelBookingTo = $objBookingDetail;
                            // Get Booking Demands of the old booking to add in the new booking creation
                            $objBookingDemand = new HotelBookingDemands();
                            if ($oldBookingDemands = $objBookingDemand->getRoomTypeBookingExtraDemands(
                                $objOldHotelBooking->id_order,
                                $objOldHotelBooking->id_product,
                                $objOldHotelBooking->id_room,
                                $objOldHotelBooking->date_from,
                                $objOldHotelBooking->date_to
                            )) {
                                if (isset($oldBookingDemands[$objOldHotelBooking->id_room]['extra_demands']) && $oldBookingDemands[$objOldHotelBooking->id_room]['extra_demands']) {
                                    foreach ($oldBookingDemands[$objOldHotelBooking->id_room]['extra_demands'] as $bookingDemand) {
                                        $objBookingDemand = new HotelBookingDemands($bookingDemand['id_booking_demand']);
                                        $objBookingDemand->id_htl_booking = $objBookingDetail->id;
                                        $objBookingDemand->save();
                                    }
                                }
                            }

                            // Get Booking services of the old booking to add in the new booking creation
                            $objServiceProductOrderDetail = new ServiceProductOrderDetail();
                            if ($oldAdditonalServices = $objServiceProductOrderDetail->getRoomTypeServiceProducts(
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
                                if (isset($oldAdditonalServices[$idHotelBooking]['additional_services']) && $oldAdditonalServices[$idHotelBooking]['additional_services']) {
                                    foreach ($oldAdditonalServices[$idHotelBooking]['additional_services'] as $service) {
                                        $objServiceProductOrderDetail = new ServiceProductOrderDetail($service['id_service_product_order_detail']);
                                        $objServiceProductOrderDetail->id_htl_booking_detail = $objBookingDetail->id;
                                        $objServiceProductOrderDetail->save();
                                    }
                                }
                            }
                        }
                    }
                } else {
                    return false;
                }

                // delete cart feature prices after room addition success
                HotelRoomTypeFeaturePricing::deleteFeaturePrices($context->cart->id);
                // since only one room is added in the cart on reallocation process.
                $idNewCartBookingData = false;
                if (isset($objCartBookingData->id)) {
                    $idNewCartBookingData = $objCartBookingData->id;
                }
                // ===============================================================
                // END: Add Process of the old booking
                // ===============================================================

                // ===============================================================
                // Start: Delete Process of the old booking
                // ===============================================================
                $deleteQty = false;
                if ($productQty >= $objOldOrderDetail->product_quantity) {
                    $deleteQty = true;
                } else {
                    // Calculate differences of price (Before / After)
                    $objOldOrderDetail->total_price_tax_incl -= Tools::processPriceRounding(
                        ($objOldOrderDetail->total_price_tax_incl - $totalRoomPriceTaxIncl),
                        1,
                        $objOrder->round_type,
                        $objOrder->round_mode
                    );
                    $objOldOrderDetail->total_price_tax_excl -= Tools::processPriceRounding(
                        ($objOldOrderDetail->total_price_tax_excl - $totalRoomPriceTaxExcl),
                        1,
                        $objOrder->round_type,
                        $objOrder->round_mode
                    );

                    $old_quantity = $objOldOrderDetail->product_quantity;

                    $objOldOrderDetail->product_quantity = $old_quantity - $productQty;
                    $objOldOrderDetail->reduction_percent = 0;

                    // update taxes
                    $result &= $objOldOrderDetail->updateTaxAmount($objOrder);

                    // Save order detail
                    $result &= $objOldOrderDetail->update();
                }

                // Update OrderInvoice of this OrderDetail
                if ($objOldOrderDetail->id_order_invoice != 0) {
                    // values changes as values are calculated accoding to the quantity of the product by webkul
                    $objOrderInvoice = new OrderInvoice($objOldOrderDetail->id_order_invoice);
                    $objOrderInvoice->total_paid_tax_excl = Tools::ps_round(
                        ($objOrderInvoice->total_paid_tax_excl - $totalRoomPriceTaxExcl),
                        _PS_PRICE_COMPUTE_PRECISION_
                    );
                    $objOrderInvoice->total_paid_tax_excl = $objOrderInvoice->total_paid_tax_excl > 0 ? $objOrderInvoice->total_paid_tax_excl : 0;

                    $objOrderInvoice->total_paid_tax_incl = Tools::ps_round(
                        ($objOrderInvoice->total_paid_tax_incl - $totalRoomPriceTaxIncl),
                        _PS_PRICE_COMPUTE_PRECISION_
                    );
                    $objOrderInvoice->total_paid_tax_incl = $objOrderInvoice->total_paid_tax_incl > 0 ? $objOrderInvoice->total_paid_tax_incl : 0;

                    $objOrderInvoice->total_products = Tools::ps_round(
                        ($objOrderInvoice->total_products - $totalRoomPriceTaxExcl),
                        _PS_PRICE_COMPUTE_PRECISION_
                    );
                    $objOrderInvoice->total_products = $objOrderInvoice->total_products > 0 ? $objOrderInvoice->total_products : 0;

                    $objOrderInvoice->total_products_wt = Tools::ps_round(
                        ($objOrderInvoice->total_products_wt - $totalRoomPriceTaxIncl),
                        _PS_PRICE_COMPUTE_PRECISION_
                    );
                    $objOrderInvoice->total_products_wt = $objOrderInvoice->total_products_wt > 0 ? $objOrderInvoice->total_products_wt : 0;

                    $result &= $objOrderInvoice->update();
                }

                // values changes as values are calculated accoding to the quantity of the product by webkul
                $objOrder->total_paid = Tools::ps_round(
                    ($objOrder->total_paid_tax_incl - $totalRoomPriceTaxIncl),
                    _PS_PRICE_COMPUTE_PRECISION_
                );
                $objOrder->total_paid = $objOrder->total_paid > 0 ? $objOrder->total_paid : 0;

                $objOrder->total_paid_tax_incl = Tools::ps_round(
                    ($objOrder->total_paid_tax_incl - $totalRoomPriceTaxIncl),
                    _PS_PRICE_COMPUTE_PRECISION_
                );
                $objOrder->total_paid_tax_incl = $objOrder->total_paid_tax_incl > 0 ? $objOrder->total_paid_tax_incl : 0;

                $objOrder->total_paid_tax_excl = Tools::ps_round(
                    ($objOrder->total_paid_tax_excl - $totalRoomPriceTaxExcl),
                    _PS_PRICE_COMPUTE_PRECISION_
                );
                $objOrder->total_paid_tax_excl = $objOrder->total_paid_tax_excl > 0 ? $objOrder->total_paid_tax_excl : 0;

                $objOrder->total_products = Tools::ps_round(
                    ($objOrder->total_products - $totalRoomPriceTaxExcl),
                    _PS_PRICE_COMPUTE_PRECISION_
                );
                $objOrder->total_products = $objOrder->total_products > 0 ? $objOrder->total_products : 0;

                $objOrder->total_products_wt = Tools::ps_round(
                    ($objOrder->total_products_wt - $totalRoomPriceTaxIncl),
                    _PS_PRICE_COMPUTE_PRECISION_
                );
                $objOrder->total_products_wt = $objOrder->total_products_wt > 0 ? $objOrder->total_products_wt : 0;

                $result &= $objOrder->update();

                // Reinject quantity in stock
                $objOldOrderDetail->reinjectQuantity($objOldOrderDetail, $objOldOrderDetail->product_quantity, $deleteQty);

                // retrieve and delete HotelCartBookingData row
                $idHotelCartBookingData = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'htl_cart_booking_data`
                    WHERE date_from = "'.pSQL($objOldHotelBooking->date_from).'" AND date_to = "'.pSQL($objOldHotelBooking->date_to).'"
                    AND id_room = '.(int) $objOldHotelBooking->id_room.' AND `id_order` = '.(int) $objOldHotelBooking->id_order
                );
                $objCartBookingData = new HotelCartBookingData($idHotelCartBookingData);
                if ($idNewCartBookingData) {
                    $objServiceProductCartDetail = new ServiceProductCartDetail();
                    if ($oldCartAdditonalServices = $objServiceProductCartDetail->getServiceProductsInCart(
                        0, // if reallocated twice the id cart will not be used as it was not changed in first reallocation.
                        [],
                        null,
                        $objCartBookingData->id
                    )) {
                        foreach ($oldCartAdditonalServices as $service) {
                            $objServiceProductCartDetail = new ServiceProductCartDetail($service['id_service_product_cart_detail']);
                            $objServiceProductCartDetail->htl_cart_booking_id = (int) $idNewCartBookingData;
                            $objServiceProductCartDetail->save();
                        }
                    }
                }
                $objCartBookingData->delete();

                // delete the booking detail
                $objOldHotelBooking = new HotelBookingDetail($idHotelBooking);
                if ($objOldHotelBooking->delete()) {
                    // delete refund request of the room if exists.
                    OrderReturnDetail::deleteReturnDetailByIdBookingDetail($objOldHotelBooking->id_order, $idHotelBooking);
                }

                // ===============================================================
                // END Delete Process of the old booking
                // ===============================================================
            } else {
                // If we are reallocating to the same room type then we need to update only the room details
                // update in the cart booking data
                // retrieve HotelCartBookingData row
                $idHotelCartBookingData = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'htl_cart_booking_data`
                    WHERE date_from = "'.pSQL($objOldHotelBooking->date_from).'" AND date_to = "'.pSQL($objOldHotelBooking->date_to).'"
                    AND id_room = '.(int) $objOldHotelBooking->id_room.' AND `id_order` = '.(int) $objOldHotelBooking->id_order
                );
                $objCartBookingData = new HotelCartBookingData($idHotelCartBookingData);
                $objCartBookingData->id_room = $idRoom;
                // set backorder to 0 as available reallocate rooms will always be free
                $objOldHotelBooking->is_back_order = 0;
                $objCartBookingData->save();

                // update in the hotel booking detail table
                $objOldHotelBooking->id_room = $idRoom;
                $objOldHotelBooking->room_num = $objHotelRoomInfo->room_num;
                // set backorder to 0 as available reallocate rooms will always be free
                $objOldHotelBooking->is_back_order = 0;

                $result &= $objOldHotelBooking->save();

                $reallocatedBookingId = $objOldHotelBooking->id;
                $objectHotelBookingTo = $objOldHotelBooking;
            }

            if ($result && $reallocatedBookingId) {
                Hook::exec(
                    'actionRoomReallocateAfter',
                    array(
                        'id_htl_booking_from' => $idHotelBooking,
                        'id_htl_booking_to' => $reallocatedBookingId,
                        'objectHotelBookingFrom' => $objectHotelBookingFrom,
                        'objectHotelBookingTo' => $objectHotelBookingTo,
                    )
                );

                return $reallocatedBookingId;
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * @deprecated : use swapBooking() instead
     * [swapRoomWithAvailableSameRoomType :: To swap rooms with available rooms in case of reallocation of the room].
     * @param [int]  $current_room_id [Id of the room to be swapped]
     * @param [date] $date_from       [start date of the booking of the room]
     * @param [date] $date_to         [end date of the booking of the room]
     * @param [date] $swapped_room_id [Id of the room with which the $current_room_id will be swapped]
     *
     * @return [boolean] [true if rooms successfully swapped else returns false]
     */
    public function swapRoomWithAvailableSameRoomType($idRoomFrom, $dateFrom, $dateTo, $idRoomTo, $idOrderFrom = 0, $idOrderTo = 0)
    {
        $result = false;

        $dateFrom = date('Y-m-d H:i:s', strtotime($dateFrom));
        $dateTo = date('Y-m-d H:i:s', strtotime($dateTo));

        // Get the booking details for the given rooms as per given parameters
        $idHotelBookingFrom = Db::getInstance()->getValue(
            'SELECT `id` FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `is_refunded` = 0
            AND `date_from`=\''.pSQL($dateFrom).'\'
            AND `date_to`=\''.pSQL($dateTo).'\'
            AND `id_room`='.(int)$idRoomFrom.
            ((int) $idOrderFrom ? ' AND id_order = '.(int) $idOrderFrom : '')
        );

        $idHotelBookingTo = Db::getInstance()->getValue(
            'SELECT `id` FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `is_refunded` = 0
            AND `date_from`=\''.pSQL($dateFrom).'\'
            AND `date_to`=\''.pSQL($dateTo).'\'
            AND `id_room`='.(int)$idRoomTo.
            ((int) $idOrderTo ? ' AND id_order = '.(int) $idOrderTo : '')
        );

        if ($idHotelBookingFrom && $idHotelBookingTo) {
            $result = $this->swapBooking($idHotelBookingFrom, $idHotelBookingTo);
        }

        return $result;
    }

    public function swapBooking($idHotelBookingFrom, $idHotelBookingTo)
    {
        $result = true;
        // get the cart booking data for the given booking
        if (Validate::isLoadedObject($objHotelBookingFrom = new HotelBookingDetail($idHotelBookingFrom))
            && Validate::isLoadedObject($objHotelBookingTo = new HotelBookingDetail($idHotelBookingTo))
        ) {
            // Swap the rooms in the room cart booking table
            $objHotelCartBooking = new HotelCartBookingData();
            $cartBookingInfoFrom = $objHotelCartBooking->getRoomRowByIdProductIdRoomInDateRange(
                $objHotelBookingFrom->id_cart,
                $objHotelBookingFrom->id_product,
                $objHotelBookingFrom->date_from,
                $objHotelBookingFrom->date_to,
                $objHotelBookingFrom->id_room
            );
            $cartBookingInfoTo = $objHotelCartBooking->getRoomRowByIdProductIdRoomInDateRange(
                $objHotelBookingTo->id_cart,
                $objHotelBookingTo->id_product,
                $objHotelBookingTo->date_from,
                $objHotelBookingTo->date_to,
                $objHotelBookingTo->id_room
            );

            if ($cartBookingInfoFrom && $cartBookingInfoTo) {
                if (Validate::isLoadedObject($objHotelCartBookingFrom = new HotelCartBookingData($cartBookingInfoFrom['id']))
                    && Validate::isLoadedObject($objHotelCartBookingTo = new HotelCartBookingData($cartBookingInfoTo['id']))
                ) {
                    $idRoomFrom = $objHotelCartBookingFrom->id_room;
                    $idRoomTo = $objHotelCartBookingTo->id_room;

                    $objHotelCartBookingFrom->id_room = $idRoomTo;
                    $objHotelCartBookingTo->id_room = $idRoomFrom;

                    $result &= $objHotelCartBookingFrom->save();
                    $result &= $objHotelCartBookingTo->save();
                }
            }

            // Swap the rooms in the room booking table
            // also transfer the backorder status of the room from which room is being swapped
            $idRoomFrom = $objHotelBookingFrom->id_room;
            $roomNumFrom = $objHotelBookingFrom->room_num;
            $roomFromBackOrder = $objHotelBookingFrom->is_back_order;

            $idRoomTo = $objHotelBookingTo->id_room;
            $roomNumTo = $objHotelBookingTo->room_num;
            $roomToBackOrder = $objHotelBookingTo->is_back_order;

            $objHotelBookingFrom->id_room = $idRoomTo;
            $objHotelBookingFrom->room_num = $roomNumTo;
            $objHotelBookingFrom->is_back_order = $roomToBackOrder;

            $objHotelBookingTo->id_room = $idRoomFrom;
            $objHotelBookingTo->room_num = $roomNumFrom;
            $objHotelBookingTo->is_back_order = $roomFromBackOrder;

            $result &= $objHotelBookingFrom->save();
            $result &= $objHotelBookingTo->save();
            if ($result) {
                Hook::exec(
                    'actionRoomSwapAfter',
                    array(
                        'id_htl_booking_from' => $idHotelBookingFrom,
                        'id_htl_booking_to' => $idHotelBookingTo
                    )
                );
            }
        } else {
            $result = false;
        }

        return $result;
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
    public function updateOrderRefundStatus($id_order, $date_from = false, $date_to = false, $id_rooms = array(), $is_refunded = 1, $is_cancelled = null)
    {
        $table = 'htl_booking_detail';
        $data = array('is_refunded' => (int) $is_refunded);

        if (!is_null($is_cancelled)) {
            $data['is_cancelled'] = (int) $is_cancelled;
        }

        if ($id_rooms) {
            foreach ($id_rooms as $key_rm => $val_rm) {
                $where = 'id_order='.(int)$id_order.' AND id_room = '.(int)$val_rm['id_room'].' AND `date_from`= \''.
                pSQL($date_from).'\' AND `date_to` = \''.pSQL($date_to).'\'';
                $result = Db::getInstance()->update($table, $data, $where);
            }
        } else {
            $result = Db::getInstance()->update($table, $data, 'id_order='.(int)$id_order);
        }

        // if automatic overbooking resolution is enabled
        if ($result && Configuration::get('PS_OVERBOOKING_AUTO_RESOLVE') && $is_refunded) {
            // if room is getting free and this room is not already in back order then resolve the overbookings for this free room
            $this->resolveOverBookings();
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
    public function deleteOrderedRoomFromOrder($idOrder, $idHotel, $idRoom, $dateFrom, $dateTo)
    {
        $roomBookingData = $this->getRoomBookingData($idRoom, $idOrder, $dateFrom, $dateTo);
        $objHotelBookingDetail = new self($roomBookingData['id']);

        if (Validate::isLoadedObject($objHotelBookingDetail)) {
            return $objHotelBookingDetail->delete();
        }

        return false;
    }

    public function getRoomBookinInformationForDateRangeByOrder(
        $id_room,
        $old_date_from,
        $old_date_to,
        $new_date_from,
        $new_date_to
    ) {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `id_room`='.(int)$id_room.'
        AND `date_from` < \''.pSQL($new_date_to).'\' AND `date_from` != \''.pSQL($old_date_from).'\'
        AND IF(`id_status` !='.HotelBookingDetail::STATUS_CHECKED_OUT.',
            `date_to` != \''.pSQL($old_date_to).'\' AND `date_to` > \''.pSQL($new_date_from).'\',
            `check_out` != \''.pSQL($old_date_to).'\' AND `check_out` > \''.pSQL($new_date_from).'\'
        )
        AND `is_refunded`=0 AND `is_back_order`=0';

        return Db::getInstance()->executeS($sql);
    }

    public function updateHotelCartHotelOrderOnOrderEdit(
        $idOrder,
        $idRoom,
        $oldDateFrom,
        $oldDateTo,
        $newDateFrom,
        $newDateTo,
        $occupancy,
        $newTotalPrice,
        $idHotelBookingDetail
    ) {
        $objHotelBookingDetail = new self((int) $idHotelBookingDetail);
        if (Validate::isLoadedObject($objHotelBookingDetail)) {
            // retrieve HotelCartBookingData row
            $idHotelCartBookingData = Db::getInstance()->getValue(
                'SELECT `id`
                FROM `'._DB_PREFIX_.'htl_cart_booking_data`
                WHERE date_from = "'.pSQL($oldDateFrom).'" AND date_to = "'.pSQL($oldDateTo).'"
                AND id_room = '.(int) $idRoom.' AND `id_order` = '.(int) $idOrder
            );

            $objHotelCartBookingData = new HotelCartBookingData($idHotelCartBookingData);
            if (Validate::isLoadedObject($objHotelCartBookingData)) {
                // calculate new prices
                $newNumDays = HotelHelper::getNumberOfDays($newDateFrom, $newDateTo);

                // update $objHotelCartBookingData
                $objHotelCartBookingData->date_from = $newDateFrom;
                $objHotelCartBookingData->date_to = $newDateTo;
                $objHotelCartBookingData->quantity = $newNumDays;
                $objHotelCartBookingData->adults = $occupancy['adults'];
                $objHotelCartBookingData->children = $occupancy['children'];
                $objHotelCartBookingData->child_ages = json_encode($occupancy['child_ages']);

                // update $objHotelBookingDetail
                $objHotelBookingDetail->date_from = $newDateFrom;
                $objHotelBookingDetail->date_to = $newDateTo;
                $objHotelBookingDetail->total_price_tax_excl = $newTotalPrice['tax_excl'];
                $objHotelBookingDetail->total_price_tax_incl = $newTotalPrice['tax_incl'];
                $objHotelBookingDetail->adults = $occupancy['adults'];
                $objHotelBookingDetail->children = $occupancy['children'];
                $objHotelBookingDetail->child_ages = json_encode($occupancy['child_ages']);

                if ($objHotelCartBookingData->save() && $objHotelBookingDetail->save()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * [getPsOrderDetailIdByIdProduct :: Returns id_order_details accoording to the product and order Id].
     * @param [int] $id_product [Id of the product]
     * @param [int] $id_order   [Id of the order]
     * @return [int|false] [If found id_order_detail else returns false]
     */
    public function getPsOrderDetailIdByIdProduct($id_product, $id_order, $selling_preference_type = 0)
    {
        $sql = 'SELECT `id_order_detail` FROM `'._DB_PREFIX_.'order_detail` WHERE `id_order`='.(int)$id_order.' AND `product_id`='.(int)$id_product;

        if ($selling_preference_type) {
            $sql .= ' AND `selling_preference_type`='.(int)$selling_preference_type;
        }

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
                $order_detail_data[$key]['room_type'] = $value['room_type_name'];
                // Check if product is still available
                if (Validate::isLoadedObject($objProduct = new Product((int) $value['id_product'], Configuration::get('PS_LANG_DEFAULT')))
                    && $productCoverImg = Product::getCover($value['id_product'])
                ) {
                    $order_detail_data[$key]['image_link'] = $context->link->getImageLink(
                        $objProduct->link_rewrite[Configuration::get('PS_LANG_DEFAULT')],
                        $productCoverImg['id_image'], 'small_default'
                    );
                } else {
                    $order_detail_data[$key]['image_link'] = $context->link->getImageLink('', $context->language->iso_code.'-default', 'small_default');
                }

                $objOrderDetail = new OrderDetail($value['id_order_detail']);
                $order_detail_data[$key]['original_unit_price_tax_excl'] = $objOrderDetail->unit_price_tax_excl;
                $order_detail_data[$key]['original_unit_price_tax_incl'] = $objOrderDetail->unit_price_tax_incl;
                $order_detail_data[$key]['unit_price_without_reduction_tax_excl'] = $objOrderDetail->unit_price_tax_excl + $objOrderDetail->reduction_amount_tax_excl;
                $order_detail_data[$key]['unit_price_without_reduction_tax_incl'] = $objOrderDetail->unit_price_tax_incl + $objOrderDetail->reduction_amount_tax_incl;

                $num_days = HotelHelper::getNumberOfDays($value['date_from'], $value['date_to']);
                $order_detail_data[$key]['quantity'] = $num_days;
                $order_detail_data[$key]['paid_unit_price_tax_excl'] = $value['total_price_tax_excl'] / $num_days;
                $order_detail_data[$key]['paid_unit_price_tax_incl'] = $value['total_price_tax_incl'] / $num_days;

                $order_detail_data[$key]['feature_price_diff'] = (float)($order_detail_data[$key]['unit_price_without_reduction_tax_incl'] - $order_detail_data[$key]['paid_unit_price_tax_incl']);

                $order_detail_data[$key]['child_ages'] = json_decode($value['child_ages']);

                // Check if this booking as any refund history then enter refund data
                if ($refundInfo = OrderReturn::getOrdersReturnDetail($id_order, 0, $value['id'])) {
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

    public function getLastInsertedRoomIdOrderDetail($id_order)
    {
        return Db::getInstance()->getValue(
            'SELECT MAX(`id_order_detail`) FROM `'._DB_PREFIX_.'order_detail` WHERE `is_booking_product` = 1 AND `id_order`='.(int)$id_order
        );
    }

    public function getLastInsertedServiceIdOrderDetail($id_order, $id_product)
    {
        return Db::getInstance()->getValue(
            'SELECT MAX(`id_order_detail`) FROM `'._DB_PREFIX_.'order_detail` WHERE `is_booking_product` = 0 AND `product_id`='.(int) $id_product.' AND `id_order`='.(int)$id_order
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
    public function getOnlyOrderBookingData($id_order, $id_guest, $id_product, $id_customer = 0, $id_order_detail = 0)
    {
        $sql = 'SELECT hbd.*, od.`unit_price_tax_incl`, od.`unit_price_tax_excl`, od.`reduction_amount_tax_excl`,
        od.`reduction_amount_tax_incl` FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        INNER JOIN `'._DB_PREFIX_.'order_detail` od ON (od.`id_order_detail` = hbd.`id_order_detail`)
        WHERE hbd.`id_order` = '.(int)$id_order.' AND hbd.`id_product` = '.(int)$id_product;

        if ($id_order_detail) {
            $sql .=  ' AND hbd.`id_order_detail` = '.(int)$id_order_detail;
        }

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
                $context->customer = $objCustomer;
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
        $context = Context::getContext();
        if ($params) {
            if (!isset($context->cookie->id_guest)) {
                Guest::setNewGuest($context->cookie);
            }
            $context->cart = new Cart();
            $idCustomer = (int)$params['id_customer'];
            $customer = new Customer((int)$idCustomer);
            $context->customer = $customer;
            $context->cart->id_customer = $idCustomer;
            if (Validate::isLoadedObject($context->cart) && $context->cart->OrderExists()) {
                return;
            }
            if (!$context->cart->secure_key) {
                $context->cart->secure_key = $context->customer->secure_key;
            }
            if (!$context->cart->id_shop) {
                $context->cart->id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');
            }
            if (!$context->cart->id_lang) {
                $context->cart->id_lang = Configuration::get('PS_LANG_DEFAULT');
            }
            if (!$context->cart->id_currency) {
                $context->cart->id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
            }

            $addresses = $customer->getAddresses((int)$context->cart->id_lang);

            if (!$context->cart->id_address_invoice && isset($addresses[0])) {
                $context->cart->id_address_invoice = (int)$addresses[0]['id_address'];
            }
            $context->cart->setNoMultishipping();

            if ($context->cart->save()) {
                return $context->cart->id;
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
        $context = Context::getContext();
        $this->errors = array();
        $id_cart = $params['id_cart'];
        $date_from = date("Y-m-d", strtotime($params['date_from']));
        $date_to = date("Y-m-d", strtotime($params['date_to']));
        $id_product = $params['id_room_type'];

        $objBookingDetail = new HotelBookingDetail();
        $num_day = HotelHelper::getNumberOfDays($date_from, $date_to); //quantity of product
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
                    'id_guest' => $context->cookie->id_guest,
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

            $update_quantity = $context->cart->updateQty($num_day*$params['req_qty'], $id_product, null, false, $direction);

            /*
            * To add Rooms in hotel cart
            */
            $id_customer = $context->cart->id_customer;
            $id_currency = $context->cart->id_currency;

            $hotel_room_info_arr = $hotel_room_data['rm_data'][$id_product]['data']['available'];
            $chkQty = 0;
            foreach ($hotel_room_info_arr as $key_hotel_room_info => $val_hotel_room_info) {
                if ($chkQty < $params['req_qty']) {
                    $obj_htl_cart_booking_data = new HotelCartBookingData();
                    $obj_htl_cart_booking_data->id_cart = $context->cart->id;
                    $obj_htl_cart_booking_data->id_guest = $context->cookie->id_guest;
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
            $total_amount = (float)$context->cart->getOrderTotal(true, Cart::BOTH);
            //$this->module = Module::getInstanceByName('hotelreservationsystem');
            $orderCreated = $channelOrderPayment->validateOrder((int) $context->cart->id, (int) 2, (float) $total_amount, 'Channel Manager Booking', null, array(), null, false, $context->cart->secure_key);
            if ($orderCreated) {
                $idOrder = Order::getOrderByCartId($context->cart->id);
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

    public function deleteHotelOrderInfo($idOrder)
    {
        $result = true;
        $objHotelCartBookingData = new HotelCartBookingData();
        $bookingsCartData = $objHotelCartBookingData->getCartCurrentDataByOrderId($idOrder);
        if (is_array($bookingsCartData) && count($bookingsCartData)) {
            foreach ($bookingsData as $bookingCartData) {
                $objHotelCartBookingData = new HotelCartBookingData($bookingCartData['id']);
                if (Validate::isLoadedObject($objHotelCartBookingData)) {
                    $result &= $objHotelCartBookingData->delete();
                }
            }
        }

        $bookingsData = $this->getBookingDataByOrderId($idOrder);
        if (is_array($bookingsData) && count($bookingsData)) {
            foreach ($bookingsData as $bookingData) {
                $objHotelBookingDetail = new self($bookingData['id']);
                if (Validate::isLoadedObject($objHotelBookingDetail)) {
                    $result &= $objHotelBookingDetail->delete();
                }
            }
        }

        return $result;
    }

    public function deleteHotelOrderRoomInfo($idOrder, $idProduct, $idRoom)
    {
        $result = Db::getInstance()->executeS(
            'SELECT `id`
            FROM `'._DB_PREFIX_.'htl_cart_booking_data`
            WHERE `id_order` = '.(int) $idOrder.' AND `id_product` = '.(int) $idProduct.'
            AND `id_room` = '.(int) $idRoom
        );

        $return = true;
        if (is_array($result) && count($result)) {
            foreach ($result as $row) {
                $objHotelCartBookingData = new HotelCartBookingData($row['id']);
                if (Validate::isLoadedObject($objHotelCartBookingData)) {
                    $return &= $objHotelCartBookingData->delete();
                }
            }
        }

        $result = Db::getInstance()->executeS(
            'SELECT `id`
            FROM `'._DB_PREFIX_.'htl_booking_detail`
            WHERE `id_order` = '.(int) $idOrder.' AND `id_product` = '.(int) $idProduct.'
            AND `id_room` = '.(int) $idRoom
        );

        if (is_array($result) && count($result)) {
            foreach ($result as $row) {
                $objHotelBookingDetail = new self($row['id']);
                if (Validate::isLoadedObject($objHotelBookingDetail)) {
                    $return &= $objHotelBookingDetail->delete();
                }
            }
        }

        return $return;
    }

    public function deleteHotelOrderRoomTypeInfo($idOrder, $idProduct)
    {
        $result = Db::getInstance()->executeS(
            'SELECT `id`
            FROM `'._DB_PREFIX_.'htl_cart_booking_data`
            WHERE `id_order` = '.(int) $idOrder.' AND `id_product` = '.(int) $idProduct
        );

        $return = true;
        if (is_array($result) && count($result)) {
            foreach ($result as $row) {
                $objHotelCartBookingData = new HotelCartBookingData($row['id']);
                if (Validate::isLoadedObject($objHotelCartBookingData)) {
                    $return &= $objHotelCartBookingData->delete();
                }
            }
        }

        $result = Db::getInstance()->executeS(
            'SELECT `id`
            FROM `'._DB_PREFIX_.'htl_booking_detail`
            WHERE `id_order` = '.(int) $idOrder.' AND `id_product` = '.(int) $idProduct
        );

        if (is_array($result) && count($result)) {
            foreach ($result as $row) {
                $objHotelBookingDetail = new self($row['id']);
                if (Validate::isLoadedObject($objHotelBookingDetail)) {
                    $return &= $objHotelBookingDetail->delete();
                }
            }
        }

        return $return;
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
        Tools::displayAsDeprecated();

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
                $bookingsData = self::getOrderInfoIdOrderIdProduct($id_order, $id_product);
                if (is_array($bookingsData) && count($bookingsData)) {
                    $result = true;
                    foreach ($bookingsData as $bookingData) {
                        $objHotelBookingDetail = new self($bookingData['id']);
                        if (Validate::isLoadedObject($objHotelBookingDetail)) {
                            $objHotelBookingDetail->id_order_detail = $orderDetail->id_order_detail;
                            $result &= $objHotelBookingDetail->save();
                        }
                    }

                    return $result;
                }
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
        $sql = 'SELECT `id`, `id_product`, `id_order_detail`, `id_hotel`, `id_customer`, `booking_type`, `id_status`, `check_in`, `check_out`
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

    // process the booking tables changes when a booking refund/cancellation is processed
    public function processRefundInBookingTables()
    {
        if (Validate::isLoadedObject($this)) {
            $reduction_amount = array(
                'total_price_tax_excl' => 0,
                'total_price_tax_incl' => 0,
                'total_products_tax_excl' => 0,
                'total_products_tax_incl' => 0,
            );
            $objOrder = new Order($this->id_order);
            $orderTotalPaid = $objOrder->getTotalPaid();
            $orderDiscounts = $objOrder->getCartRules();

            $hasOrderDiscountOrPayment = ((float)$orderTotalPaid > 0 || $orderDiscounts) ? true : false;

            // things to do if order is not paid
            if (!$hasOrderDiscountOrPayment) {
                $objHotelBookingDemands = new HotelBookingDemands();
                $objServiceProductOrderDetail = new ServiceProductOrderDetail();

                $reduction_amount['total_price_tax_excl'] = (float) $this->total_price_tax_excl;
                $reduction_amount['total_products_tax_excl'] = (float) $this->total_price_tax_excl;
                $reduction_amount['total_price_tax_incl'] = (float) $this->total_price_tax_incl;
                $reduction_amount['total_products_tax_incl'] = (float) $this->total_price_tax_incl;

                // reduce facilities amount from order and services_detail
                if ($roomDemands = $objHotelBookingDemands->getRoomTypeBookingExtraDemands(
                    $this->id_order,
                    $this->id_product,
                    $this->id_room,
                    $this->date_from,
                    $this->date_to,
                    0,
                    0,
                    1,
                    $this->id
                )) {
                    foreach ($roomDemands as $roomDemand) {
                        $objHotelBookingDemands = new HotelBookingDemands($roomDemand['id_booking_demand']);
                        $reduction_amount['total_price_tax_excl'] += (float) $objHotelBookingDemands->total_price_tax_excl;
                        $reduction_amount['total_price_tax_incl'] += (float) $objHotelBookingDemands->total_price_tax_incl;
                        $objHotelBookingDemands->total_price_tax_excl = 0;
                        $objHotelBookingDemands->total_price_tax_incl = 0;
                        $objHotelBookingDemands->save();
                    }
                }

                // reduce services amount from order and services_detail
                if ($roomServices = $objServiceProductOrderDetail->getRoomTypeServiceProducts(
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
                    $this->id
                )) {
                    foreach ($roomServices[$this->id]['additional_services'] as $roomService) {
                        $objServiceProductOrderDetail = new ServiceProductOrderDetail(
                            $roomService['id_service_product_order_detail']
                        );
                        $reduction_amount['total_price_tax_excl'] += (float) $objServiceProductOrderDetail->total_price_tax_excl;
                        $reduction_amount['total_products_tax_excl'] += (float) $objServiceProductOrderDetail->total_price_tax_excl;
                        $reduction_amount['total_price_tax_incl'] += (float) $objServiceProductOrderDetail->total_price_tax_incl;
                        $reduction_amount['total_products_tax_incl'] += (float) $objServiceProductOrderDetail->total_price_tax_incl;

                        if (Validate::isLoadedObject($objOrderDetail = new OrderDetail($objServiceProductOrderDetail->id_order_detail))) {
                            $objOrderDetail->product_quantity_refunded += $objServiceProductOrderDetail->quantity;
                            if ($objOrderDetail->product_quantity_refunded > $objOrderDetail->product_quantity) {
                                $objOrderDetail->product_quantity_refunded = $objOrderDetail->product_quantity;
                            }

                            $objOrderDetail->total_price_tax_excl -= (float) Tools::processPriceRounding(
                                $objServiceProductOrderDetail->total_price_tax_excl,
                                1,
                                $objOrder->round_type,
                                $objOrder->round_mode
                            );
                            $objOrderDetail->total_price_tax_excl = $objOrderDetail->total_price_tax_excl > 0 ? $objOrderDetail->total_price_tax_excl : 0;

                            $objOrderDetail->total_price_tax_incl -= (float) Tools::processPriceRounding(
                                $objServiceProductOrderDetail->total_price_tax_incl,
                                1,
                                $objOrder->round_type,
                                $objOrder->round_mode
                            );
                            $objOrderDetail->total_price_tax_incl = $objOrderDetail->total_price_tax_incl > 0 ? $objOrderDetail->total_price_tax_incl : 0;

                            $objServiceProductOrderDetail->total_price_tax_excl = 0;
                            $objServiceProductOrderDetail->total_price_tax_incl = 0;
                            $objServiceProductOrderDetail->save();

                            $objOrderDetail->updateTaxAmount($objOrder) && $objOrderDetail->save();
                        }

                        $objServiceProductOrderDetail->total_price_tax_excl = 0;
                        $objServiceProductOrderDetail->total_price_tax_incl = 0;
                        $objServiceProductOrderDetail->save();
                    }
                }
            }

            // enter refunded quantity in the order detail table
            $idOrderDetail = $this->id_order_detail;
            if (Validate::isLoadedObject($objOrderDetail = new OrderDetail($idOrderDetail))) {
                $numDays = HotelHelper::getNumberOfDays(
                    $this->date_from,
                    $this->date_to
                );

                $objOrderDetail->product_quantity_refunded += $numDays;
                if ($objOrderDetail->product_quantity_refunded > $objOrderDetail->product_quantity) {
                    $objOrderDetail->product_quantity_refunded = $objOrderDetail->product_quantity;
                }

                if (!$hasOrderDiscountOrPayment) {
                    // reduce room amount from order and order detail
                    $objOrderDetail->total_price_tax_incl -= Tools::processPriceRounding(
                        $this->total_price_tax_incl,
                        1,
                        $objOrder->round_type,
                        $objOrder->round_mode
                    );
                    $objOrderDetail->total_price_tax_incl = $objOrderDetail->total_price_tax_incl > 0 ? $objOrderDetail->total_price_tax_incl : 0;

                    $objOrderDetail->total_price_tax_excl -= Tools::processPriceRounding(
                        $this->total_price_tax_excl,
                        1,
                        $objOrder->round_type,
                        $objOrder->round_mode
                    );
                    $objOrderDetail->total_price_tax_excl = $objOrderDetail->total_price_tax_excl > 0 ? $objOrderDetail->total_price_tax_excl : 0;

                    if (Validate::isLoadedObject($objOrder = new Order($this->id_order))) {
                        $objOrder->total_paid = Tools::ps_round(
                            ($objOrder->total_paid - $reduction_amount['total_price_tax_incl']),
                            _PS_PRICE_COMPUTE_PRECISION_
                        );
                        $objOrder->total_paid = $objOrder->total_paid > 0 ? $objOrder->total_paid : 0;

                        $objOrder->total_paid_tax_excl = Tools::ps_round(($objOrder->total_paid_tax_excl - $reduction_amount['total_price_tax_excl']),
                            _PS_PRICE_COMPUTE_PRECISION_
                        );
                        $objOrder->total_paid_tax_excl = $objOrder->total_paid_tax_excl > 0 ? $objOrder->total_paid_tax_excl : 0;

                        $objOrder->total_paid_tax_incl = Tools::ps_round(($objOrder->total_paid_tax_incl - $reduction_amount['total_price_tax_incl']),
                            _PS_PRICE_COMPUTE_PRECISION_
                        );
                        $objOrder->total_paid_tax_incl = $objOrder->total_paid_tax_incl > 0 ? $objOrder->total_paid_tax_incl : 0;

                        $objOrder->total_products = Tools::ps_round(($objOrder->total_products - $reduction_amount['total_products_tax_excl']),
                            _PS_PRICE_COMPUTE_PRECISION_
                        );
                        $objOrder->total_products = $objOrder->total_products > 0 ? $objOrder->total_products : 0;

                        $objOrder->total_products_wt = Tools::ps_round(($objOrder->total_products_wt - $reduction_amount['total_products_tax_incl']),
                            _PS_PRICE_COMPUTE_PRECISION_
                        );
                        $objOrder->total_products_wt = $objOrder->total_products_wt > 0 ? $objOrder->total_products_wt : 0;

                        $objOrder->save();

                        // Update OrderInvoice
                        if ($objOrder->hasInvoice()) {
                            $objOrderInvoice = new OrderInvoice($objOrderDetail->id_order_invoice);
                            $objOrderInvoice->total_products -= $reduction_amount['total_products_tax_excl'];
                            $objOrderInvoice->total_products_wt -= $reduction_amount['total_products_tax_incl'];
                            $objOrderInvoice->total_paid_tax_excl -= $reduction_amount['total_price_tax_excl'];
                            $objOrderInvoice->total_paid_tax_incl -= $reduction_amount['total_price_tax_incl'];
                            $objOrderInvoice->update();
                        }
                    }
                }

                $objOrderDetail->save();
            }

            // as refund is completed then set the booking as refunded
            $this->is_refunded = 1;
            if (!$hasOrderDiscountOrPayment) {
                // Reduce room amount from htl_booking_detail
                $this->is_cancelled = 1;
                $this->total_price_tax_excl = 0;
                $this->total_price_tax_incl = 0;
            }

            $this->save();

            return true;
        }

        return false;
    }

    /**
     * Get overbooked rooms in the order|hotel
     * @param [int] $idOrder : id of the order
     * @param [int] $idHotel : id of the hotel
     * @param [string] $dateFrom
     * @param [string] $dateTo
     * @param [int] $datewiseBreakup : send 1 to get overbooked rooms in datewise breakup
     * @param [int] $roomCount : send 1 to get the count of the rooms overbooked only
     * @param [int] $bookedRoomFlag : send 0 for no action | send 1 to get booked room info | send 2 to get overbooked rooms which are free
     *
     * @return array | integer : array of overbooked rooms | count of overbooked rooms
     */
    public function getOverbookedRooms(
        $idOrder = 0,
        $idHotel = 0,
        $dateFrom = '',
        $dateTo = '',
        $datewiseBreakup = 0,
        $roomCount = 0,
        $bookedRoomInfoFlag = 0
    ) {
        $result = array();

        $sql = 'SELECT';
        if ($roomCount) {
            $sql .= ' COUNT(*)';
        } else {
            $sql .= ' *';
        }

        $sql .= ' FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `is_back_order` = 1 AND `is_refunded` = 0 AND `is_cancelled` = 0';

        if ($idOrder) {
            $sql .= ' AND `id_order` = '.(int) $idOrder;
        }
        if ($idHotel) {
            $sql .= ' AND `id_hotel` = '.(int) $idHotel;
        }

        if ($datewiseBreakup && $dateFrom && $dateTo) {
            for ($currentDate = $dateFrom; $currentDate < $dateTo; $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)))) {
                $dateSql = $sql . ' AND `date_from` <= \''.pSQL($currentDate).'\' AND `date_to` > \''.pSQL($currentDate).'\'';

                if ($roomCount) {
                    $result[$currentDate] = Db::getInstance()->getValue($dateSql);
                } else {
                    $result[$currentDate] = Db::getInstance()->executeS($dateSql);
                }
            }
        } else {
            if ($dateFrom && $dateTo) {
                $sql .= ' AND `date_from` < \''.pSQL($dateTo).'\' AND `date_to` > \''.pSQL($dateFrom).'\'';
            }

            if ($roomCount) {
                $result = Db::getInstance()->getValue($sql);
            } else {
                $result = Db::getInstance()->executeS($sql);
            }
        }

        if (!$roomCount && $result) {
            $link = new Link();
            foreach ($result as $key => $bookingInfo) {
                $result[$key]['child_ages'] = json_decode($bookingInfo['child_ages'], true);

                if ($bookedRoomInfoFlag) {
                    $result[$key]['booked_room_info'] = $this->chechRoomBooked($bookingInfo['id_room'], $bookingInfo['date_from'], $bookingInfo['date_to']);
                    $result[$key]['orders_filter_link'] = $link->getAdminLink('AdminOrders');

                    // if need only rooms which are available to resolve overbooking the unset booked rooms
                    if ($bookedRoomInfoFlag == 2 && $result[$key]['booked_room_info']) {
                        unset($result[$key]);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get all the orders which are in overbooking
     * @param integer $onlyFutureDates : for orders which bookings dates are in future
     * @return array
     */
    public function getOverBookedOrders($onlyFutureDates = 0)
    {
        $sql = 'SELECT DISTINCT `id_order` FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `is_back_order` = 1 AND `is_refunded` = 0 AND `is_cancelled` = 0';

        if ($onlyFutureDates) {
            $sql .= ' AND `date_to` > \''.pSQL(date('Y-m-d')).'\'';
        }
        $sql .= ' ORDER BY `id_order` ASC';

        $result = Db::getInstance()->executeS($sql);

        return array_column($result, 'id_order');
    }

    // Resolve overbookings by checking current available rooms
    // Overbookings resolution will only be done if all overbookings are getting resolved in the order
    public function resolveOverBookings($idHotelBooking = 0)
    {
        $result = false;

        $overBookedOrders = array();
        if ($idHotelBooking) {
            if (Validate::isLoadedObject($objHtlBookingDetail = new HotelBookingDetail($idHotelBooking))) {
                $overBookedOrders = array($objHtlBookingDetail->id_order);
            }
        } else {
            $overBookedOrders = $this->getOverBookedOrders(1);
        }

        if ($overBookedOrders) {
            foreach ($overBookedOrders as $idOrder) {
                // get all overbooked rooms in the order so that we can decide status of the order after overbooking resolved
                $overBookedRooms = $this->getOverbookedRooms($idOrder);
                $totalOverBookedInOrder = count($overBookedRooms);

                // if request for specific booking resolve
                if ($idHotelBooking) {
                    $overBookedRooms = array((array)($objHtlBookingDetail));
                }

                if ($overBookedRooms) {
                    $resolvableOverbookings = array();
                    foreach($overBookedRooms as $roomBooking) {
                        $params = array(
                            'idHotel' => $roomBooking['id_hotel'],
                            'dateFrom' => $roomBooking['date_from'],
                            'dateTo' => $roomBooking['date_to'],
                            'idRoomType' => $roomBooking['id_product'],
                            'searchOccupancy' => 0,
                            'allowedIdRoomTypes' => implode(",", array($roomBooking['id_product']))
                        );

                        if ($availableRooms = $this->getSearchAvailableRooms($params)) {
                            if (isset($availableRooms['availableRoomTypes']['roomTypes'][$roomBooking['id_product']]['rooms'])
                                && $availableRooms['availableRoomTypes']['roomTypes'][$roomBooking['id_product']]['rooms']
                            ) {
                                $availableRoomsInfo = $availableRooms['availableRoomTypes']['roomTypes'][$roomBooking['id_product']]['rooms'];
                                if ($roomIdsAvailable = array_column($availableRoomsInfo, 'id_room')) {
                                    // If room is still there in the available rooms list then we can resolve its overbooking
                                    if (in_array($roomBooking['id_room'], $roomIdsAvailable)) {
                                        $resolvableOverbookings[] = $roomBooking['id'];
                                    }
                                }
                            }
                        }
                    }

                    // if request for specific booking Or all overbooked rooms are resolved then resolve the overbooking by setting is_back_order to 0
                    if ($idHotelBooking || (count($resolvableOverbookings) == $totalOverBookedInOrder)) {
                        foreach ($resolvableOverbookings as $idHtlBookingDtl) {
                            $objBookingDetail = new HotelBookingDetail($idHtlBookingDtl);
                            $objBookingDetail->is_back_order = 0;
                            $objBookingDetail->update();

                            $result = true;
                        }

                        // After all overbookings are resolved we can update the status of the order
                        if (count($resolvableOverbookings) == $totalOverBookedInOrder) {
                            $objOrder = new Order($idOrder);
                            $idOrderState = 0;
                            if ($objOrder->current_state == Configuration::get('PS_OS_OVERBOOKING_PAID')) {
                                $idOrderState = Configuration::get('PS_OS_PAYMENT_ACCEPTED');
                            } elseif ($objOrder->current_state == Configuration::get('PS_OS_OVERBOOKING_PARTIAL_PAID')) {
                                $idOrderState = Configuration::get('PS_OS_PARTIAL_PAYMENT_ACCEPTED');
                            } elseif ($objOrder->current_state == Configuration::get('PS_OS_OVERBOOKING_UNPAID')) {
                                $idOrderState = Configuration::get('PS_OS_AWAITING_PAYMENT');
                            }

                            // if we have order state to change
                            if ($idOrderState) {
                                $objOrderHistory = new OrderHistory();
                                $objOrderHistory->id_order = (int)$idOrder;
                                $objOrderHistory->changeIdOrderState((int)$idOrderState, $objOrder, true);
                                $objOrderHistory->add();
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function add($auto_date = true, $null_values = false)
    {
        if (!$this->planned_check_out) {
            $objHotelBranchInfo  = new HotelBranchInformation((int) $this->id_hotel);
            $dateTo = new DateTime($this->date_to);
            $timeParts = explode(':', $objHotelBranchInfo->check_out);
            $dateTo->setTime($timeParts[0], $timeParts[1]);

            $this->planned_check_out = $dateTo->format('Y-m-d H:i:s');
        }

        return parent::add($auto_date, $null_values);
    }
}
