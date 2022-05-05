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

    public $date_add;
    public $date_upd;

    const STATUS_ALLOTED = 1;
    const STATUS_CHECKED_IN = 2;
    const STATUS_CHECKED_OUT = 3;

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
        if (!isset($params['room_type'])) {
            $params['room_type'] = 0;
        }
        if (!isset($params['adult'])) {
            $params['adult'] = 0;
        }
        if (!isset($params['children'])) {
            $params['children'] = 0;
        }
        if (!isset($params['num_rooms'])) {
            $params['num_rooms'] = 1;
        }
        if (!isset($params['for_calendar'])) {
            $params['for_calendar'] = 0;
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
        if (!isset($params['id_cart'])) {
            $params['id_cart'] = 0;
        }
        if (!isset($params['id_guest'])) {
            $params['id_guest'] = 0;
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
        return $params;
    }

    /**
     * [getBookingData :: To get Array of rooms data].
     *
     * @param [type] $date_from        [Start date of booking]
     * @param [type] $date_to          [End date of booking]
     * @param [type] $hotel_id         [Id of the hotel to which the room belongs]
     * @param [type] $room_type        [Id of the product to which the room belongs]
     * @param int    $adult            []
     * @param int    $children         []
     * @param int    $num_rooms        [Number of rooms booked for the period $date_from to $date_to]
     * @param int    $for_calendar     [Used for calender and also for getting stats of rooms]
     * @param int    $search_available [If you want only data information for available rooms]
     * @param int    $search_partial   [If you want only data information for partial rooms]
     * @param int    $search_booked    [If you want only data information for booked rooms]
     * @param int    $search_unavai    [If you want only data information for unavailable rooms]
     * @param int    $id_cart          [Id of the cart to which the room belongs at the time of booking]
     * @param int    $id_guest         [Id guest of the customer Who booked the rooms]
     * @param int    $search_cart_rms  [If you want data of the current cart in the admin office]
     *
     * @return [array] [Returns Array of rooms data ]
     *
     * Note: Adult and children both are used for front category page only in available rooms
     * Note :: $for_calendar is used both for calender and also for getting stats of rooms
     */
    public function getBookingData($params)
    {
        // extract all keys and values of the array [$params] into variables and values
        extract($this->getBookingDataParams($params));
        if ($date_from && $date_to && $hotel_id) {
            $date_from = date('Y-m-d H:i:s', strtotime($date_from));
            $date_to = date('Y-m-d H:i:s', strtotime($date_to));

            $obj_room_info = new HotelRoomInformation();
            $obj_rm_type = new HotelRoomType();

            if ($room_types = $obj_rm_type->getIdProductByHotelId(
                $hotel_id,
                $room_type,
                $only_active_roomtype,
                $only_active_hotel
            )) {
                $total_rooms = 0;
                $num_booked = 0;
                $num_unavail = 0;
                $num_avail = 0;
                $num_part_avai = 0;
                $num_cart = 0;
                $new_part_arr = array();
                $booking_data = array();

                if ($search_partial) {
                    $this->partAvaiDates = array();
                    $this->allReqDates = $this->createDateRangeArray($date_from, $date_to, 1);
                }

                foreach ($room_types as $key => $room_type) {
                    if ($search_partial) {
                        $this->dltDates = array();
                    }

                    $total_rooms += $obj_room_info->getHotelRoomInfo($room_type['id_product'], $hotel_id, 1);

                    $obj_product = new Product((int) $room_type['id_product']);
                    $product_name = $obj_product->name[Configuration::get('PS_LANG_DEFAULT')];

                    if ($search_cart_rms) {
                        if ($id_cart && $id_guest) {
                            $sql = 'SELECT cbd.`id_product`, cbd.`id_room`, cbd.`id_hotel`, cbd.`booking_type`, cbd.`comment`, rf.`room_num`, cbd.`date_from`, cbd.`date_to`
                                FROM `'._DB_PREFIX_.'htl_cart_booking_data` AS cbd
                                INNER JOIN `'._DB_PREFIX_.'htl_room_information` AS rf ON (rf.`id` = cbd.`id_room`)
                                WHERE cbd.`id_hotel`='.(int)$hotel_id.' AND cbd.`id_product` ='.(int)$room_type['id_product'].' AND cbd.`id_cart` = '.(int)$id_cart.' AND cbd.`id_guest` ='.(int)$id_guest.' AND cbd.`is_refunded` = 0 AND cbd.`is_back_order` = 0';
                            $cart_rooms = Db::getInstance()->executeS($sql);
                        } else {
                            $cart_rooms = array();
                        }
                        $num_cart += count($cart_rooms);
                    }

                    if ($search_booked) {
                        $sql = 'SELECT bd.`id_product`, bd.`id_room`, bd.`id_hotel`, bd.`id_customer`, bd.`booking_type`, bd.`id_status` AS booking_status, bd.`comment`, rf.`room_num`, bd.`date_from`, bd.`date_to`
                            FROM `'._DB_PREFIX_.'htl_booking_detail` AS bd
                            INNER JOIN `'._DB_PREFIX_.'htl_room_information` AS rf ON (rf.`id` = bd.`id_room`)
                            WHERE bd.`id_hotel`='.(int)$hotel_id.' AND bd.`id_product` ='.(int)$room_type['id_product'].' AND bd.`is_refunded` = 0 AND bd.`is_back_order` = 0 AND IF(bd.`id_status` = '. self::STATUS_CHECKED_OUT .', bd.`date_from` <= \''.pSQL($date_from).'\' AND bd.`check_out` >= \''.pSQL($date_to).'\', bd.`date_from` <= \''.pSQL($date_from).'\' AND bd.date_to >= \''.pSQL($date_to).'\')';

                        $booked_rooms = Db::getInstance()->executeS($sql);

                        foreach ($booked_rooms as $booked_k => $booked_v) {
                            $booked_rooms[$booked_k]['detail'][] = array(
                                'date_from' => $booked_v['date_from'],
                                'date_to' => $booked_v['date_to'],
                                'id_customer' => $booked_v['id_customer'],
                                'booking_type' => $booked_v['booking_type'],
                                'booking_status' => $booked_v['booking_status'],
                                'comment' => $booked_v['comment'],
                            );

                            unset($booked_rooms[$booked_k]['date_from']);
                            unset($booked_rooms[$booked_k]['date_to']);
                            unset($booked_rooms[$booked_k]['id_customer']);
                            unset($booked_rooms[$booked_k]['booking_type']);
                            unset($booked_rooms[$booked_k]['booking_status']);
                            unset($booked_rooms[$booked_k]['comment']);
                        }

                        $num_booked += count($booked_rooms);
                    }

                    if ($search_unavai) {
                        $sql1 = 'SELECT `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment`
                                FROM `'._DB_PREFIX_.'htl_room_information`
                                WHERE `id_hotel`='.(int)$hotel_id.' AND `id_product` ='.(int)$room_type['id_product'].' AND `id_status` = '. HotelRoomInformation::STATUS_INACTIVE;

                        $sql2 = 'SELECT hri.`id_product`, hri.`id_hotel`, hri.`room_num`, hri.`comment` AS `room_comment`
                                FROM `'._DB_PREFIX_.'htl_room_information` AS hri
                                INNER JOIN `'._DB_PREFIX_.'htl_room_disable_dates` AS hrdd ON (hrdd.`id_room_type` = hri.`id_product` AND hrdd.	id_room = hri.`id`)
                                WHERE hri.`id_hotel`='.$hotel_id.' AND hri.`id_product` ='.$room_type['id_product'].' AND hri.`id_status` = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .' AND hrdd.`date_from` <= \''.pSql($date_from).'\' AND hrdd.`date_to` >= \''.pSql($date_to).'\'';

                        $sql = $sql1.' UNION '.$sql2;

                        $unavail_rooms = Db::getInstance()->executeS($sql);
                        $num_unavail += count($unavail_rooms);
                    }

                    if ($search_available) {
                        $exclude_ids = 'SELECT `id_room`
                        FROM `'._DB_PREFIX_.'htl_booking_detail`
                        WHERE `is_back_order` = 0 AND `is_refunded` = 0 AND IF(`id_status` = '. self::STATUS_CHECKED_OUT.', (
                            (DATE_FORMAT(`check_out`,  "%Y-%m-%d") > \''.pSQL($date_from).'\' AND DATE_FORMAT(`check_out`,  "%Y-%m-%d") <= \''.PSQL($date_to).'\') AND (
                                (`date_from` <= \''.pSQL($date_from).'\' AND `check_out` > \''.pSQL($date_from).'\' AND `check_out` <= \''.PSQL($date_to).'\') OR
                                (`date_from` >= \''.pSQL($date_from).'\' AND `check_out` > \''.pSQL($date_from).'\' AND `check_out` <= \''.pSQL($date_to).'\') OR
                                (`date_from` >= \''.pSQL($date_from).'\' AND `date_from` < \''.pSQL($date_to).'\' AND `check_out` >= \''.pSQL($date_to).'\') OR
                                (`date_from` <= \''.pSQL($date_from).'\' AND `check_out` >= \''.pSQL($date_to).'\')
                            )
                        ), (
                            (`date_from` <= \''.pSQL($date_from).'\' AND `date_to` > \''.pSQL($date_from).'\' AND `date_to` <= \''.PSQL($date_to).'\') OR
                            (`date_from` >= \''.pSQL($date_from).'\' AND `date_to` <= \''.pSQL($date_to).'\') OR
                            (`date_from` >= \''.pSQL($date_from).'\' AND `date_from` < \''.pSQL($date_to).'\' AND `date_to` >= \''.pSQL($date_to).'\') OR
                            (`date_from` <= \''.pSQL($date_from).'\' AND `date_to` >= \''.pSQL($date_to).'\')
                        ))';

                        if (!empty($id_cart) && !empty($id_guest)) {
                            $exclude_ids .= ' UNION
                                SELECT `id_room`
                                FROM `'._DB_PREFIX_.'htl_cart_booking_data`
                                WHERE id_cart='.(int)$id_cart.' AND id_guest='.(int)$id_guest.' AND is_refunded = 0 AND  is_back_order = 0 AND ((date_from <= \''.pSQL($date_from).'\' AND date_to > \''.pSQL($date_from).'\' AND date_to <= \''.pSQL($date_to).'\') OR (date_from > \''.pSQL($date_from).'\' AND date_to < \''.pSQL($date_to).'\') OR (date_from >= \''.pSQL($date_from).'\' AND date_from < \''.pSQL($date_to).'\' AND date_to >= \''.pSQL($date_to).'\') OR (date_from < \''.pSQL($date_from).'\' AND date_to > \''.pSQL($date_to).'\'))';
                        }

                        // For excludes temporary disable rooms
                        $exclude_ids .= ' UNION
                            SELECT hri.`id` AS id_room
                            FROM `'._DB_PREFIX_.'htl_room_information` AS hri
                            INNER JOIN `'._DB_PREFIX_.'htl_room_disable_dates` AS hrdd ON (hrdd.`id_room_type` = hri.`id_product` AND hrdd.`id_room` = hri.`id`)
                            WHERE hri.`id_hotel`='.(int)$hotel_id.' AND hri.`id_product` ='.(int)$room_type['id_product'].' AND hri.`id_status` = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .' AND (hrdd.`date_from` <= \''.pSql($date_to).'\' AND hrdd.`date_to` >= \''.pSql($date_from).'\')';

                        $selectAvailRoomSearch = 'SELECT ri.`id` AS `id_room`, ri.`id_product`, ri.`id_hotel`, ri.`room_num`, ri.`comment` AS `room_comment`';

                        $joinAvailRoomSearch = '';

                        $whereAvailRoomSearch = 'WHERE ri.`id_hotel`='.(int)$hotel_id.' AND ri.`id_product`='.(int)$room_type['id_product'].' AND ri.`id_status` != '. HotelRoomInformation::STATUS_INACTIVE .' AND ri.`id` NOT IN ('.$exclude_ids.')';

                        $groupByAvailRoomSearch = '';
                        $orderByAvailRoomSearch = '';
                        $orderWayAvailRoomSearch = '';

                        Hook::exec('actionAvailRoomSearchSqlModifier',
                            array(
                                'select' => &$selectAvailRoomSearch,
                                'join' => &$joinAvailRoomSearch,
                                'where' => &$whereAvailRoomSearch,
                                'group_by' => &$groupByAvailRoomSearch,
                                'order_by' => &$orderByAvailRoomSearch,
                                'order_way' => &$orderWayAvailRoomSearch,
                                'params' => array(
                                    'id_hotel' => $hotel_id,
                                    'id_product' => $room_type['id_product'],
                                    'date_from' => $date_from,
                                    'date_to' => $date_to
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

                        $avai_rooms = Db::getInstance()->executeS($sql);
                        $num_avail += count($avai_rooms);
                    }

                    if ($search_partial) {
                        $sql1 = 'SELECT bd.`id_product`, bd.`id_room`, bd.`id_hotel`, bd.`id_customer`, bd.`booking_type`, bd.`id_status` AS booking_status, bd.`comment` AS `room_comment`, rf.`room_num`, bd.`date_from`, IF(bd.`id_status` = '. self::STATUS_CHECKED_OUT .', bd.`check_out`, bd.`date_to`) AS `date_to`
                            FROM `'._DB_PREFIX_.'htl_booking_detail` AS bd
                            INNER JOIN `'._DB_PREFIX_.'htl_room_information` AS rf ON (rf.`id` = bd.`id_room`)
                            WHERE bd.`id_hotel`='.(int)$hotel_id.' AND bd.`id_product`='.(int)$room_type['id_product'].' AND rf.`id_status` != '. HotelRoomInformation::STATUS_INACTIVE .' AND bd.`is_back_order` = 0 AND bd.`is_refunded` = 0 AND IF(bd.`id_status` = '. self::STATUS_CHECKED_OUT .', (
                                (DATE_FORMAT(`check_out`,  "%Y-%m-%d") > \''.pSQL($date_from).'\' AND DATE_FORMAT(`check_out`,  "%Y-%m-%d") < \''.PSQL($date_to).'\') AND (
                                    (bd.`date_from` <= \''.pSQL($date_from).'\' AND bd.`check_out` > \''.pSQL($date_from).'\' AND bd.`check_out` < \''.pSQL($date_to).'\') OR
                                    (bd.`date_from` > \''.pSQL($date_from).'\' AND bd.`date_from` < \''.pSQL($date_to).'\' AND bd.`check_out` >= \''.pSQL($date_to).'\') OR
                                    (bd.`date_from` > \''.pSQL($date_from).'\' AND bd.`date_from` < \''.pSQL($date_to).'\' AND bd.`check_out` > \''.pSQL($date_from).'\' AND bd.`check_out` < \''.pSQL($date_to).'\')
                                )
                            ), (
                                (bd.`date_from` <= \''.pSQL($date_from).'\' AND bd.`date_to` > \''.pSQL($date_from).'\' AND bd.`date_to` < \''.pSQL($date_to).'\') OR
                                (bd.`date_from` > \''.pSQL($date_from).'\' AND bd.`date_from` < \''.pSQL($date_to).'\' AND bd.`date_to` >= \''.pSQL($date_to).'\') OR
                                (bd.`date_from` > \''.pSQL($date_from).'\' AND bd.`date_from` < \''.pSQL($date_to).'\' AND bd.`date_to` < \''.pSQL($date_to).'\')
                            ))';

                        $sql2 = 'SELECT hri.`id_product`, hrdd.`id_room`, hri.`id_hotel`, 0 AS `id_customer`, 0 AS `booking_type`, 0 AS `booking_status`, 0 AS `room_comment`, hri.`room_num`, hrdd.`date_from`, hrdd.`date_to`
                            FROM `'._DB_PREFIX_.'htl_room_information` AS hri
                            INNER JOIN `'._DB_PREFIX_.'htl_room_disable_dates` AS hrdd ON (hrdd.`id_room_type` = hri.`id_product` AND hrdd.`id_room` = hri.`id`)
                            WHERE hri.`id_hotel`='.(int)$hotel_id.' AND hri.`id_product`='.(int)$room_type['id_product'].' AND
                            hri.`id_status` = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .' AND (
                                (hrdd.`date_from` <= \''.pSQL($date_from).'\' AND hrdd.`date_to` > \''.pSQL($date_from).'\' AND hrdd.`date_to` < \''.pSQL($date_to).'\') OR
                                (hrdd.`date_from` > \''.pSQL($date_from).'\' AND hrdd.`date_from` < \''.pSQL($date_to).'\' AND hrdd.`date_to` >= \''.pSQL($date_to).'\') OR
                                (hrdd.`date_from` > \''.pSQL($date_from).'\' AND hrdd.`date_from` < \''.pSQL($date_to).'\' AND hrdd.`date_to` < \''.pSQL($date_to).'\')
                            )';
                        // $part_arr2 = Db::getInstance()->executeS($sql2);
                        // $part_arr = array_merge($part_arr1 ? $part_arr1 : array(), $part_arr2 ? $part_arr2 : array());

                        $sql = $sql1.' UNION '.$sql2;
                        // NOTE:: Before code if written to use "ORDER BY" with union
                        $sql = 'SELECT s.*
                                FROM ('.$sql.') AS s
                                ORDER BY s.`id_room`';
                        $part_arr = Db::getInstance()->executeS($sql);

                        $partial_avai_rooms = array();
                        foreach ($part_arr as $pr_val) {
                            $partial_avai_rooms[$pr_val['id_room']]['id_product'] = $pr_val['id_product'];
                            $partial_avai_rooms[$pr_val['id_room']]['id_room'] = $pr_val['id_room'];
                            $partial_avai_rooms[$pr_val['id_room']]['id_hotel'] = $pr_val['id_hotel'];
                            $partial_avai_rooms[$pr_val['id_room']]['room_num'] = $pr_val['room_num'];

                            if ($pr_val['id_customer']) {
                                $partial_avai_rooms[$pr_val['id_room']]['booked_dates'][] = array(
                                    'date_from' => $pr_val['date_from'],
                                    'date_to' => $pr_val['date_to'],
                                    'id_customer' => $pr_val['id_customer'],
                                    'booking_type' => $pr_val['booking_type'],
                                    'booking_status' => $pr_val['booking_status'],
                                    'comment' => $pr_val['room_comment']
                                );
                            }

                            if (!isset($partial_avai_rooms[$pr_val['id_room']]['avai_dates'])) {
                                if (($pr_val['date_from'] <= $date_from) && ($pr_val['date_to'] > $date_from) && ($pr_val['date_to'] < $date_to)) {
                                    // from lower to middle range

                                    $forRange = $this->createDateRangeArray($pr_val['date_to'], $date_to, 0, $pr_val['id_room']);
                                    $available_dates = $this->getPartialRange($forRange, $pr_val['id_room'], $key);
                                } elseif (($pr_val['date_from'] > $date_from) && ($pr_val['date_from'] < $date_to) && ($pr_val['date_to'] >= $date_to)) {
                                    // from middle to higher range

                                    $forRange = $this->createDateRangeArray($date_from, $pr_val['date_from'], 0, $pr_val['id_room']);
                                    $available_dates = $this->getPartialRange($forRange, $pr_val['id_room'], $key);
                                } elseif (($pr_val['date_from'] > $date_from) && ($pr_val['date_from'] < $date_to) && ($pr_val['date_to'] > $date_from) && ($pr_val['date_to'] < $date_to)) {
                                    // between range

                                    $forRange1 = $this->createDateRangeArray($date_from, $pr_val['date_from'], 0, $pr_val['id_room']);
                                    $init_range = $this->getPartialRange($forRange1, $pr_val['id_room'], $key);

                                    $forRange2 = $this->createDateRangeArray($pr_val['date_to'], $date_to, 0, $pr_val['id_room']);
                                    $last_range = $this->getPartialRange($forRange2, $pr_val['id_room'], $key);

                                    $available_dates = $init_range + $last_range;
                                }

                                $partial_avai_rooms[$pr_val['id_room']]['avai_dates'] = $available_dates;
                            } else {
                                /*
                                    * Note :: createDateRangeArray function check and unset dates from allReqDates(array) but below it will only return array of date because dates already been removed from "if" condition
                                    */
                                $bk_dates = $this->createDateRangeArray($pr_val['date_from'], $pr_val['date_to'], 0, 0, 0);
                                if (count($bk_dates) >= 2) {
                                    for ($i = 0; $i < count($bk_dates) - 1; ++$i) {
                                        $dateJoin = strtotime($bk_dates[$i]);

                                        if (isset($partial_avai_rooms[$pr_val['id_room']]['avai_dates'][$dateJoin])) {
                                            if (isset($this->dltDates[$pr_val['id_room']]) && $this->dltDates[$pr_val['id_room']]) {
                                                $this->allReqDates[] = $bk_dates[$i];
                                            }
                                            unset($partial_avai_rooms[$pr_val['id_room']]['avai_dates'][$dateJoin]);
                                            unset($this->partAvaiDates[$pr_val['id_room'].$dateJoin]);
                                        }
                                    }
                                }
                            }
                        }

                        $rm_part_avai = count($partial_avai_rooms);
                        $num_part_avai += $rm_part_avai;
                    }

                    // if (!$for_calendar)
                    // {
                    $booking_data['rm_data'][$key]['name'] = $product_name;
                    $booking_data['rm_data'][$key]['id_product'] = (int) $room_type['id_product'];

                    if ($search_available) {
                        $booking_data['rm_data'][$key]['data']['available'] = $avai_rooms;
                    }

                    if ($search_unavai) {
                        $booking_data['rm_data'][$key]['data']['unavailable'] = $unavail_rooms;
                    }

                    if ($search_booked) {
                        $booking_data['rm_data'][$key]['data']['booked'] = $booked_rooms;
                    }

                    if ($search_partial) {
                        $booking_data['rm_data'][$key]['data']['partially_available'] = $partial_avai_rooms;
                    }

                    if ($search_cart_rms) {
                        $booking_data['rm_data'][$key]['data']['cart_rooms'] = $cart_rooms;
                    }
                    // }
                }

                if ($search_partial) {
                    foreach ($booking_data['rm_data'] as $bk_data_key => $bk_data_val) {
                        foreach ($bk_data_val['data']['partially_available'] as $part_rm_key => $part_rm_val) {
                            if (empty($part_rm_val['avai_dates'])) {
                                unset($booking_data['rm_data'][$bk_data_key]['data']['partially_available'][$part_rm_key]['avai_dates']);

                                $booking_data['rm_data'][$bk_data_key]['data']['partially_available'][$part_rm_key]['detail'] = $booking_data['rm_data'][$bk_data_key]['data']['partially_available'][$part_rm_key]['booked_dates'];

                                unset($booking_data['rm_data'][$bk_data_key]['data']['partially_available'][$part_rm_key]['booked_dates']);

                                if ($search_booked) {
                                    $booking_data['rm_data'][$bk_data_key]['data']['booked'] = array_merge($booking_data['rm_data'][$bk_data_key]['data']['booked'], array($booking_data['rm_data'][$bk_data_key]['data']['partially_available'][$part_rm_key]));
                                    $num_booked += 1;

                                    unset($booking_data['rm_data'][$bk_data_key]['data']['partially_available'][$part_rm_key]);
                                }
                                $num_part_avai -= 1;
                            } elseif (!empty($this->allReqDates)) {
                                unset($booking_data['rm_data'][$bk_data_key]['data']['partially_available'][$part_rm_key]['avai_dates']);
                                unset($booking_data['rm_data'][$bk_data_key]['data']['partially_available'][$part_rm_key]['booked_dates']);
                                if ($search_unavai) {
                                    $booking_data['rm_data'][$bk_data_key]['data']['partially_available'][$part_rm_key]['room_comment'] = '';
                                    $booking_data['rm_data'][$bk_data_key]['data']['unavailable'] = array_merge($booking_data['rm_data'][$bk_data_key]['data']['unavailable'], array($booking_data['rm_data'][$bk_data_key]['data']['partially_available'][$part_rm_key]));

                                    $num_unavail += 1;

                                    unset($booking_data['rm_data'][$bk_data_key]['data']['partially_available'][$part_rm_key]);
                                }
                                $num_part_avai -= 1;
                            }
                        }

                        // Remove Cart Rooms from Partial available rooms
                        if ($search_cart_rms) {
                            foreach ($bk_data_val['data']['cart_rooms'] as $cart_key => $cart_val) {
                                if (isset($this->partAvaiDates[$cart_val['id_room'].strtotime($cart_val['date_from'])])) {
                                    $rm_data_key = $this->partAvaiDates[$cart_val['id_room'].strtotime($cart_val['date_from'])]['rm_data_key'];

                                    unset($booking_data['rm_data'][$rm_data_key]['data']['partially_available'][$cart_val['id_room']]['avai_dates'][strtotime($cart_val['date_from'])]);

                                    if (empty($booking_data['rm_data'][$rm_data_key]['data']['partially_available'][$cart_val['id_room']]['avai_dates'])) {
                                        unset($booking_data['rm_data'][$rm_data_key]['data']['partially_available'][$cart_val['id_room']]);
                                        $num_part_avai -= 1;
                                    }
                                }
                            }

                            unset($this->partAvaiDates);
                        }
                    }
                }

                if ($for_calendar) {
                    unset($booking_data['rm_data']);
                }

                $booking_data['stats']['total_rooms'] = $total_rooms;

                if ($search_booked) {
                    $booking_data['stats']['num_booked'] = $num_booked;
                }

                if ($search_unavai) {
                    $booking_data['stats']['num_unavail'] = $num_unavail;
                }

                if ($search_available) {
                    $booking_data['stats']['num_avail'] = $num_avail;
                }

                if ($search_partial) {
                    $booking_data['stats']['num_part_avai'] = $num_part_avai;
                }

                if ($search_partial) {
                    $booking_data['stats']['num_cart'] = $num_cart;
                }

                return $booking_data;
            }
        }
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
     * [DataForFrontSearch ].
     *
     * @param [date] $date_from     [Start date of the booking]
     * @param [date] $date_to       [End date of the booking]
     * @param [int]  $id_hotel      [Id of the Hotel]
     * @param [int]  $id_product    [ID of the product]
     * @param [int]  $for_room_type [used for product page and category page for block cart]
     * @param [int]  $adult         []
     * @param [int]  $children      []
     * @param []     $ratting       [description]
     * @param []     $amenities     [description]
     * @param []     $price         [description]
     * @param [int]  $id_cart       [Id of the cart]
     * @param [int]  $id_guest      [Id of the guest]
     *
     * @return [array] [Returns true if successfully updated else returns false]
     *                 Note:: $for_room_type is used for product page and category page for block cart
     */
    public function DataForFrontSearch($date_from, $date_to, $id_hotel, $id_product = 0, $for_room_type = 0, $adult = 0, $children = 0, $ratting = -1, $amenities = 0, $price = 0, $id_cart = 0, $id_guest = 0)
    {
        if (Module::isInstalled('productcomments')) {
            require_once _PS_MODULE_DIR_.'productcomments/ProductComment.php';
        }

        $this->context = Context::getContext();

        $bookingParams = array();
        $bookingParams['date_from'] = $date_from;
        $bookingParams['date_to'] = $date_to;
        $bookingParams['hotel_id'] = $id_hotel;
        $bookingParams['room_type'] = $id_product;
        $bookingParams['adult'] = $adult;
        $bookingParams['children'] = $children;
        $bookingParams['num_rooms'] = 0;
        $bookingParams['for_calendar'] = 0;
        $bookingParams['search_available'] = 1;
        $bookingParams['search_partial'] = 0;
        $bookingParams['search_booked'] = 0;
        $bookingParams['search_unavai'] = 0;
        $bookingParams['id_cart'] = $id_cart;
        $bookingParams['id_guest'] = $id_guest;

        $booking_data = $this->getBookingData($bookingParams);

        if (!$for_room_type) {
            if (!empty($booking_data)) {
                $obj_rm_type = new HotelRoomType();

                foreach ($booking_data['rm_data'] as $key => $value) {
                    if (empty($value['data']['available'])) {
                        unset($booking_data['rm_data'][$key]);
                    } else {
                        if (Module::isInstalled('productcomments')) {
                            $prod_ratting = ProductComment::getAverageGrade($value['id_product'])['grade'];
                        }
                        if (empty($prod_ratting)) {
                            $prod_ratting = 0;
                        }

                        if ($prod_ratting < $ratting && $ratting != -1) {
                            unset($booking_data['rm_data'][$key]);
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
                                    unset($booking_data['rm_data'][$key]);
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

                                    $room_left = count($booking_data['rm_data'][$key]['data']['available']);

                                    $rm_dtl = $obj_rm_type->getRoomTypeInfoByIdProduct($value['id_product']);

                                    $booking_data['rm_data'][$key]['name'] = $product->name;
                                    $booking_data['rm_data'][$key]['image'] = $cover_img;
                                    $booking_data['rm_data'][$key]['description'] = $product->description_short;
                                    $booking_data['rm_data'][$key]['feature'] = $product_feature;
                                    $booking_data['rm_data'][$key]['price'] = $prod_price;
                                    $booking_data['rm_data'][$key]['price_without_reduction'] = $productPriceWithoutReduction;
                                    $booking_data['rm_data'][$key]['feature_price'] = $productFeaturePrice;
                                    $booking_data['rm_data'][$key]['feature_price_diff'] = $productPriceWithoutReduction - $productFeaturePrice;

                                    // if ($room_left <= (int)Configuration::get('WK_ROOM_LEFT_WARNING_NUMBER'))
                                    $booking_data['rm_data'][$key]['room_left'] = $room_left;

                                    $booking_data['rm_data'][$key]['adult'] = $rm_dtl['adult'];
                                    $booking_data['rm_data'][$key]['children'] = $rm_dtl['children'];

                                    $booking_data['rm_data'][$key]['ratting'] = $prod_ratting;
                                    if (Module::isInstalled('productcomments')) {
                                        $booking_data['rm_data'][$key]['num_review'] = ProductComment::getCommentNumber($value['id_product']);
                                    }

                                    if (Configuration::get('PS_REWRITING_SETTINGS')) {
                                        $booking_data['rm_data'][$key]['product_link'] = $this->context->link->getProductLink($product).'?date_from='.$date_from.'&date_to='.$date_to;
                                    } else {
                                        $booking_data['rm_data'][$key]['product_link'] = $this->context->link->getProductLink($product).'&date_from='.$date_from.'&date_to='.$date_to;
                                    }
                                } else {
                                    unset($booking_data['rm_data'][$key]);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $booking_data;
    }

    /**
     * [getAvailableRoomsForReallocation :: Get the available rooms For the reallocation of the selected room].
     *
     * @param [date] $date_from[Start date of booking of the room to be swapped with available rooms]
     * @param [date] $date_to         [End date of booking of the room to be swapped with available rooms]
     * @param [int]  $room_type       [Id of the product to which the room belongs to be swapped]
     * @param [int]  $hotel_id        [Id of the Hotel to which the room belongs to be swapped]
     *
     * @return [array|false] [Returs array of the available rooms for swapping if rooms found else returnss false]
     */
    public function getAvailableRoomsForReallocation($date_from, $date_to, $room_type, $hotel_id)
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
            WHERE hri.`id_hotel`='.(int)$hotel_id.' AND hri.`id_product` ='.(int)$room_type.'
            AND hri.`id_status` = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .'
            AND (hrdd.`date_from` <= \''.pSql($date_to).'\' AND hrdd.`date_to` >= \''.pSql($date_from).'\')';

        if (isset($current_admin_cart_id) && $current_admin_cart_id) {
            $sql = 'SELECT `id` AS `id_room`, `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment`
            FROM `'._DB_PREFIX_.'htl_room_information`
            WHERE `id_hotel`='.(int)$hotel_id.' AND `id_product`='.(int)$room_type.'
            AND (id_status = '. HotelRoomInformation::STATUS_ACTIVE .' or id_status = '. HotelRoomInformation::STATUS_TEMPORARY_INACTIVE .')
            AND `id` NOT IN ('.$exclude_ids.')
            AND `id` NOT IN (SELECT `id_room` FROM `'._DB_PREFIX_.'htl_cart_booking_data` WHERE `id_cart`='.
            (int)$current_admin_cart_id.')';
        } else {
            $sql = 'SELECT `id` AS `id_room`, `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment`
            FROM `'._DB_PREFIX_.'htl_room_information`
            WHERE `id_hotel`='.(int)$hotel_id.' AND `id_product`='.(int)$room_type.'
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
    public function getAvailableRoomsForSwapping($date_from, $date_to, $room_type, $hotel_id, $id_room)
    {
        $sql = 'SELECT `id` AS `id_room`, `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment`
            FROM `'._DB_PREFIX_.'htl_room_information`
            WHERE `id_hotel`='.(int)$hotel_id.' AND `id_product`='.(int)$room_type.'
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
        $new_date_to
    ) {
        $rowByIdOrderIdRoom = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `id_room`='.(int)$id_room.' AND `id_order`='.(int)$id_order);
        $numDays = $this->getNumberOfDays($old_date_from, $old_date_to);
        $paidUnitRoomPriceTE = $rowByIdOrderIdRoom['total_price_tax_excl']/$numDays;
        $paidUnitRoomPriceTI = $rowByIdOrderIdRoom['total_price_tax_incl']/$numDays;

        $newNumDays = $this->getNumberOfDays($new_date_from, $new_date_to);
        $newTotalPriceTE = $paidUnitRoomPriceTE * $newNumDays;
        $newTotalPriceTI = $paidUnitRoomPriceTI * $newNumDays;
        //$total_price = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice($rowByIdOrderIdRoom['id_product'], $new_date_from, $new_date_to);
        $table = 'htl_cart_booking_data';
        $table1 = 'htl_booking_detail';
        $num_days = $this->getNumberOfDays($new_date_from, $new_date_to);
        $data_cart = array(
            'date_from' => $new_date_from,
            'date_to' => $new_date_to,
            'quantity' => $num_days
        );
        $data_order = array(
            'date_from' => $new_date_from,
            'date_to' => $new_date_to,
            'total_price_tax_excl' => $newTotalPriceTE,
            'total_price_tax_incl' => $newTotalPriceTI
        );

        $where = 'id_order = '.(int)$id_order.' AND id_room = '.(int)$id_room.' AND date_from= \''.pSQL($old_date_from).
        '\' AND date_to = \''.pSQL($old_date_to).'\' AND `is_refunded`=0 AND `is_back_order`=0';

        $result = Db::getInstance()->update($table, $data_cart, $where);

        $result1 = Db::getInstance()->update($table1, $data_order, $where);

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

        $obj_booking_dtl = new HotelBookingDetail();
        $num_day = $obj_booking_dtl->getNumberOfDays($date_from, $date_to); //quantity of product
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
                $obj_booking_dtl = new HotelBookingDetail();
                $hotel_room_data = $obj_booking_dtl->DataForFrontSearch($date_from, $date_to, $id_hotel, $id_product, 1, 0, 0, -1, 0, 0, $id_cart, $this->context->cookie->id_guest);
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

            $hotel_room_info_arr = $hotel_room_data['rm_data'][0]['data']['available'];
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
                    $obj_htl_cart_booking_data->booking_type = 1;
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
