<?php
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
        public $id_status;
        public $comment;
        public $check_in;
        public $check_out;
        public $date_from;
        public $date_to;
        public $is_refunded;
        public $is_back_order;
        public $date_add;
        public $date_upd;

        public static $definition = array(
            'table' => 'htl_booking_detail',
            'primary' => 'id',
            'fields' => array(
                'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'id_order_detail' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'id_room' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'booking_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'id_status' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'comment' => array('type' => self::TYPE_STRING),
                'check_in' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
                'check_out' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
                'date_from' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
                'date_to' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
                'is_refunded' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'is_back_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
                'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ), );

        /**
         * [getBookingData :: To get Array of rooms data].
         *
         * @param [type] $date_from        [Start date of booking]
         * @param [type] $date_to          [End date of booking]
         * @param [type] $hotel_id         [Id of the hotel to which the room belongs]
         * @param [type] $room_type        [Id of the product to which the room belongs]
         * @param int    $adult            []
         * @param int    $children         []
         * @param int    $num_rooms        [Number of rooms bokked for the period $date_from to $date_to]
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
        public function getBookingData($date_from, $date_to, $hotel_id, $room_type, $adult = 0, $children = 0, $num_rooms = 1, $for_calendar = 0, $search_available = 1, $search_partial = 1, $search_booked = 1, $search_unavai = 1, $id_cart = 0, $id_guest = 0, $search_cart_rms = 0)
        {
            $date_from = date('Y-m-d H:i:s', strtotime($date_from));
            $date_to = date('Y-m-d H:i:s', strtotime($date_to));

            $obj_room_info = new HotelRoomInformation();

            //For check-in and check-out time
            //
            // $obj_hotel_info = new HotelBranchInformation();
            // $hotel_info = $obj_hotel_info->hotelBranchInfoById($hotel_id);

            // $date_from = date('Y-m-d H:i:s', strtotime("$date_from +".date('H',strtotime($hotel_info['check_in']))." hours +".date('i', strtotime($hotel_info['check_in']))." minutes"));
            // $date_to = date('Y-m-d H:i:s', strtotime("$date_to +".date('H', strtotime($hotel_info['check_out']))." hours +".date('i', strtotime($hotel_info['check_out']))." minutes"));

            $obj_rm_type = new HotelRoomType();
            $room_types = $obj_rm_type->getIdProductByHotelId($hotel_id, $room_type, 1, 1);
            if ($room_types) {
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
                        $sql = 'SELECT cbd.id_product, cbd.id_room, cbd.id_hotel, cbd.booking_type, cbd.comment, rf.room_num, cbd.date_from, cbd.date_to 
							FROM `'._DB_PREFIX_.'htl_cart_booking_data` AS cbd
							INNER JOIN `'._DB_PREFIX_.'htl_room_information` AS rf ON (rf.id = cbd.id_room)
							WHERE cbd.id_hotel='.$hotel_id.' AND cbd.id_product ='.$room_type['id_product'].' AND cbd.id_cart = '.$id_cart.' AND cbd.id_guest ='.$id_guest.' AND cbd.is_refunded = 0 AND cbd.is_back_order = 0';
                        $cart_rooms = Db::getInstance()->executeS($sql);
                        $num_cart += count($cart_rooms);
                    }

                    if ($search_booked) {
                        $sql = 'SELECT bd.id_product, bd.id_room, bd.id_hotel, bd.id_customer, bd.booking_type, bd.id_status AS booking_status, bd.comment, rf.room_num, bd.date_from, bd.date_to 
							FROM `'._DB_PREFIX_.'htl_booking_detail` AS bd
							INNER JOIN `'._DB_PREFIX_.'htl_room_information` AS rf ON (rf.id = bd.id_room)
							WHERE bd.id_hotel='.$hotel_id.' AND bd.id_product ='.$room_type['id_product']." AND bd.date_from <= '$date_from' AND bd.date_to >='$date_to' AND bd.is_refunded = 0 AND bd.is_back_order = 0";

                        $booked_rooms = Db::getInstance()->executeS($sql);

                        foreach ($booked_rooms as $booked_k => $booked_v) {
                            $booked_rooms[$booked_k]['detail'][] = array('date_from' => $booked_v['date_from'],
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
                        $sql = 'SELECT `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment` FROM `'._DB_PREFIX_.'htl_room_information` WHERE id_hotel='.$hotel_id.' AND id_product ='.$room_type['id_product'].' AND id_status = 2';
                        $unavail_rooms = Db::getInstance()->executeS($sql);
                        $num_unavail += count($unavail_rooms);
                    }

                    if ($search_available) {
                        $exclude_ids = 'SELECT id_room 
								FROM '._DB_PREFIX_."htl_booking_detail 
								WHERE is_back_order = 0 AND is_refunded = 0 AND ((date_from <= '$date_from' AND date_to > '$date_from' AND date_to <= '$date_to') OR (date_from > '$date_from' AND date_to < '$date_to') OR (date_from >= '$date_from' AND date_from < '$date_to' AND date_to >= '$date_to') OR (date_from < '$date_from' AND date_to > '$date_to'))";

                        if (!empty($id_cart) && !empty($id_guest)) {
                            $exclude_ids .= ' UNION
								SELECT id_room 
								FROM '._DB_PREFIX_.'htl_cart_booking_data 
								WHERE id_cart='.$id_cart.' AND id_guest='.$id_guest." AND is_refunded = 0 AND  is_back_order = 0 AND ((date_from <= '$date_from' AND date_to > '$date_from' AND date_to <= '$date_to') OR (date_from > '$date_from' AND date_to < '$date_to') OR (date_from >= '$date_from' AND date_from < '$date_to' AND date_to >= '$date_to') OR (date_from < '$date_from' AND date_to > '$date_to'))";
                        }

                        $sql = 'SELECT ri.`id` AS `id_room`, ri.`id_product`, ri.`id_hotel`, ri.`room_num`, ri.`comment` AS `room_comment` 
							FROM `'._DB_PREFIX_.'htl_room_information` AS ri ';
                        if ($adult || $children) {
                            $sql .= 'INNER JOIN '._DB_PREFIX_.'htl_room_type AS rt ON (rt.id_product = ri.id_product AND rt.id_hotel = ri.id_hotel';
                            if ($adult) {
                                $sql .= ' AND rt.adult >= '.$adult;
                            }
                            if ($children) {
                                $sql .= ' AND rt.children >= '.$children;
                            }
                            $sql .= ')';
                        }

                        $sql .= ' WHERE ri.id_hotel='.$hotel_id.' AND ri.id_product='.$room_type['id_product'].' AND ri.id_status = 1 AND ri.id NOT IN ('.$exclude_ids.')';

                        $avai_rooms = Db::getInstance()->executeS($sql);
                        $num_avail += count($avai_rooms);
                    }

                    if ($search_partial) {
                        $sql = 'SELECT bd.id_product, bd.id_room, bd.id_hotel, bd.id_customer, bd.booking_type, bd.id_status AS booking_status, bd.comment AS `room_comment`, rf.room_num, bd.date_from, bd.date_to
							FROM `'._DB_PREFIX_.'htl_booking_detail` AS bd 
							INNER JOIN `'._DB_PREFIX_.'htl_room_information` AS rf ON (rf.id = bd.id_room AND rf.id_status = 1)
							WHERE bd.id_hotel='.$hotel_id.' AND bd.id_product='.$room_type['id_product']." AND bd.is_back_order = 0 AND bd.is_refunded = 0 AND ((bd.date_from <= '$date_from' AND bd.date_to > '$date_from' AND bd.date_to < '$date_to') OR (bd.date_from > '$date_from' AND bd.date_from < '$date_to' AND bd.date_to >= '$date_to') OR (bd.date_from > '$date_from' AND bd.date_from < '$date_to' AND bd.date_to < '$date_to')) ORDER BY bd.id_room";

                        $part_arr = Db::getInstance()->executeS($sql);
                        $partial_avai_rooms = array();
                        foreach ($part_arr as $pr_key => $pr_val) {
                            $partial_avai_rooms[$pr_val['id_room']]['id_product'] = $pr_val['id_product'];
                            $partial_avai_rooms[$pr_val['id_room']]['id_room'] = $pr_val['id_room'];
                            $partial_avai_rooms[$pr_val['id_room']]['id_hotel'] = $pr_val['id_hotel'];
                            $partial_avai_rooms[$pr_val['id_room']]['room_num'] = $pr_val['room_num'];

                            $partial_avai_rooms[$pr_val['id_room']]['booked_dates'][] = array('date_from' => $pr_val['date_from'],
                                                                                            'date_to' => $pr_val['date_to'],
                                                                                            'id_customer' => $pr_val['id_customer'],
                                                                                            'booking_type' => $pr_val['booking_type'],
                                                                                            'booking_status' => $pr_val['booking_status'],
                                                                                            'comment' => $pr_val['room_comment'], );

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

        // This function algo is same as available rooms algo and it not similar to booked rooms algo.
        public function chechRoomBooked($id_room, $date_from, $date_to)
        {
            $sql = 'SELECT `id_product`, `id_order`, `id_cart`, `id_room`, `id_hotel`, `id_customer` FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `id_room` = '.$id_room." AND `is_back_order` = 0 AND `is_refunded` = 0 AND ((date_from <= '$date_from' AND date_to > '$date_from' AND date_to <= '$date_to') OR (date_from > '$date_from' AND date_to < '$date_to') OR (date_from >= '$date_from' AND date_from < '$date_to' AND date_to >= '$date_to') OR (date_from < '$date_from' AND date_to > '$date_to'))";

            $result = Db::getInstance()->getRow($sql);
            if ($result) {
                return $result;
            }

            return false;
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
         *
         * @param [array] $dateArr [Array containing dates]
         *
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
           /* $startTimeStamp = strtotime($dateFrom);
            $endTimeStamp = strtotime($dateTo);

            $timeDiff = abs($endTimeStamp - $startTimeStamp);*/

            /*$numberDays = $timeDiff / 86400;  // 86400 seconds in one day

            // and you might want to convert to integer
            $numberDays = $numberDays;*/

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
            $result = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `id_order`='.$order_id);
            if ($result) {
                return $result;
            }

            return false;
        }

        /**
         * [updateBookingOrderStatusBYOrderId :: To update the order status of a room in the booking].
         *
         * @param [int] $order_id   [Id of the order]
         * @param [int] $new_status [Id of the new status of the order to be updated]
         * @param [int] $id_room    [Id of the room which order status is to be ypdated]
         *
         * @return [Boolean] [Returns true if successfully updated else returns false]
         */
        public function updateBookingOrderStatusByOrderId($order_id, $new_status, $id_room, $date_from, $date_to)
        {
            $table = 'htl_booking_detail';
            if ($new_status == 2) {
                $data = array('id_status' => $new_status,'check_in' => date('Y-m-d'));
            } elseif ($new_status == 3) {
                $data = array('id_status' => $new_status,'check_out' => date('Y-m-d'));
            } else {
                $data = array('id_status' => $new_status);
            }
            $where = 'id_order = '.$order_id.' AND id_room = '.$id_room.' AND `date_from` = \''.pSQL($date_from).'\' AND `date_to` = \''.pSQL($date_to).'\'';
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

            $booking_data = $this->getBookingData($date_from, $date_to, $id_hotel, $id_product, $adult, $children, 0, 0, 1, 0, 0, 0, $id_cart, $id_guest);
            // ddd($booking_data);
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

            if (isset($current_admin_cart_id) && $current_admin_cart_id) {
                $sql = 'SELECT `id` AS `id_room`, `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment` FROM `'._DB_PREFIX_.'htl_room_information` WHERE `id_hotel`='.$hotel_id.' AND `id_product`='.$room_type.' AND id_status = 1 AND `id` NOT IN (SELECT `id_room` FROM `'._DB_PREFIX_."htl_booking_detail` WHERE `date_from` < '$date_to' AND `date_to` > '$date_from' AND `is_refunded`=0 AND `is_back_order`=0) AND `id` NOT IN (SELECT `id_room` FROM `"._DB_PREFIX_.'htl_cart_booking_data` WHERE `id_cart`='.$current_admin_cart_id.')';
            } else {
                $sql = 'SELECT `id` AS `id_room`, `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment` FROM `'._DB_PREFIX_.'htl_room_information` WHERE `id_hotel`='.$hotel_id.' AND `id_product`='.$room_type.' AND id_status = 1 AND `id` NOT IN (SELECT `id_room` FROM `'._DB_PREFIX_."htl_booking_detail` WHERE `date_from` < '$date_to' AND `date_to` > '$date_from' AND `is_refunded`=0 AND `is_back_order`=0)";
            }
            $avail_rooms = Db::getInstance()->executeS($sql);
            if ($avail_rooms) {
                return $avail_rooms;
            }

            return false;
        }

        /**
         * [getAvailableRoomsForSwaping :: Get the available rooms for the swapping of the selected room with another room].
         *
         * @param [date] $date_from[Start date of booking of the room to be swapped with available rooms]
         * @param [date] $date_to         [End date of booking of the room to be swapped with available rooms]
         * @param [int]  $room_type       [Id of the product to which the room belongs to be swapped]
         * @param [int]  $hotel_id        [Id of the Hotel to which the room belongs to be swapped]
         *
         * @return [array|false] [Returs array of the available rooms for swapping if rooms found else returnss false]
         */
        public function getAvailableRoomsForSwapping($date_from, $date_to, $room_type, $hotel_id, $id_room)
        {
            $sql = 'SELECT `id` AS `id_room`, `id_product`, `id_hotel`, `room_num`, `comment` AS `room_comment` FROM `'._DB_PREFIX_.'htl_room_information` WHERE `id_hotel`='.$hotel_id.' AND `id_product`='.$room_type.' AND `id_status` = 1 AND `id` IN ('.'SELECT `id_room` FROM `'._DB_PREFIX_."htl_booking_detail` WHERE `date_from` = '$date_from' AND `date_to` = '$date_to' AND `id_room`!=".$id_room.' AND `is_refunded`=0 AND `is_back_order`=0)';

            $avail_rooms = Db::getInstance()->executeS($sql);
            if ($avail_rooms) {
                return $avail_rooms;
            }

            return false;
        }

        /**
         * [reallocateRoomWithAvailableSameRoomType :: To reallocate rooms with available rooms in case of reallocation of the room].
         *
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
            $table = 'htl_booking_detail';
            $table2 = 'htl_cart_booking_data';
            $data = array('id_room' => $swapped_room_id);
            $where = "date_from='$date_from' AND date_to='$date_to' AND id_room=".$current_room_id;
            $result = Db::getInstance()->update($table, $data, $where);
            $result2 = Db::getInstance()->update($table2, $data, $where);
            if ($result) {
                $result2 = Db::getInstance()->update($table2, $data, $where);
                if ($result2) {
                    return true;
                }

                return false;
            }

            return false;
        }

        /**
         * [swapRoomWithAvailableSameRoomType :: To swap rooms with available rooms in case of reallocation of the room].
         *
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

            $idcrt1 = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_."htl_cart_booking_data` WHERE `date_from`='$date_from' AND `date_to`='$date_to' AND `id_room`=".$swapped_room_id);
            $idcrt2 = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_."htl_cart_booking_data` WHERE `date_from`='$date_from' AND `date_to`='$date_to' AND `id_room`=".$current_room_id);

            $id1 = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_."htl_booking_detail` WHERE `date_from`='$date_from' AND `date_to`='$date_to' AND `id_room`=".$swapped_room_id);
            $id2 = Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_."htl_booking_detail` WHERE `date_from`='$date_from' AND `date_to`='$date_to' AND `id_room`=".$current_room_id);

            $sql = 'UPDATE `'._DB_PREFIX_.'htl_cart_booking_data` SET `id_room`=IF(`id`='.$idcrt1.','.$current_room_id.','.$swapped_room_id.') WHERE `id` IN('.$idcrt1.','.$idcrt2.')';
            $sql1 = 'UPDATE `'._DB_PREFIX_.'htl_booking_detail` SET `id_room`=IF(`id`='.$id1.','.$current_room_id.','.$swapped_room_id.') WHERE `id` IN('.$id1.','.$id2.')';

            $result = Db::getInstance()->execute($sql);
            if ($result) {
                $result2 = Db::getInstance()->execute($sql1);
                if ($result2) {
                    return true;
                }

                return false;
            }

            return false;
        }

        /**
         * [updateOrderRefundStatus :: To update the refund status of a room booked in the order if amount refunded by the admin].
         *
         * @param [int]  $id_order  [Id of the order]
         * @param [date] $date_from [start date of the bookin of the room]
         * @param [date] $date_to   [end date of the bookin of the room]
         * @param [int]  $id_room   [id of the room for which refund is done]
         *
         * @return [boolean] [true if updated otherwise false]
         */
        public function updateOrderRefundStatus($id_order, $date_from, $date_to, $id_rooms)
        {
            $table = 'htl_booking_detail';
            $data = array('is_refunded' => 1);
            foreach ($id_rooms as $key_rm => $val_rm) {
                $where = '`id_order` = '.$id_order.' AND `id_room` = '.$val_rm['id_room']." AND `date_from` = '$date_from' AND `date_to` = '$date_to'";
                $result = Db::getInstance()->update($table, $data, $where);
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
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'order_detail` WHERE `id_order`='.$id_order.' AND `product_id`='.$id_product;
            $result = Db::getInstance()->executeS($sql);
            if ($result) {
                return $result;
            }

            return false;
        }

        /**
         * [deleteRoomFromOrder : Deletes a row from the table with the supplied conditions].
         *
         * @param [int]  $id_order  [Id of the order]
         * @param [int]  $id_room   [id_of the room]
         * @param [date] $date_from [Start date of the booking]
         * @param [date] $date_to   [End date of the booking]
         *
         * @return [Boolean] [True if deleted else false]
         */
        public function deleteOrderedRoomFromOrder($id_order, $id_hotel, $id_room, $date_from, $date_to)
        {
            $delete = Db::getInstance()->delete('htl_booking_detail', '`id_order`='.(int) $id_order.' AND `id_hotel`='.(int) $id_hotel.' AND `id_room`='.(int) $id_room." AND `date_from`='$date_from' AND `date_to`='$date_to'");

            return $delete;
        }

        public function getRoomBookinInformationForDateRangeByOrder($id_room, $old_date_from, $old_date_to, $new_date_from, $new_date_to)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `id_room`='.$id_room." AND `date_from` < '$new_date_to' AND `date_to` > '$new_date_from' AND `date_from` != '$old_date_from' AND `date_to` != '$old_date_to' AND `is_refunded`=0 AND `is_back_order`=0";
            $result = Db::getInstance()->executeS($sql);
            if ($result) {
                return $result;
            }

            return false;
        }

        public function UpdateHotelCartHotelOrderOnOrderEdit($id_order, $id_room, $old_date_from, $old_date_to, $new_date_from, $new_date_to)
        {
            $table = 'htl_cart_booking_data';
            $table1 = 'htl_booking_detail';
            $num_days = $this->getNumberOfDays($new_date_from, $new_date_to);
            $data_cart = array('date_from' => $new_date_from,'date_to' => $new_date_to,'quantity' => $num_days);
            $data_order = array('date_from' => $new_date_from,'date_to' => $new_date_to);
            $where = 'id_order = '.$id_order.' AND id_room = '.$id_room." AND date_from = '$old_date_from' AND date_to = '$old_date_to' AND `is_refunded`=0 AND `is_back_order`=0";

            $result = Db::getInstance()->update($table, $data_cart, $where);

            $result1 = Db::getInstance()->update($table1, $data_order, $where);

            return $result;
        }

        /**
         * [getPsOrderDetailIdByIdProduct :: Returns id_order_details accoording to the product and order Id].
         *
         * @param [int] $id_product [Id of the product]
         * @param [int] $id_order   [Id of the order]
         *
         * @return [int|false] [If found id_order_detail else returns false]
         */
        public function getPsOrderDetailIdByIdProduct($id_product, $id_order)
        {
            $sql = 'SELECT `id_order_detail` FROM `'._DB_PREFIX_.'order_detail` WHERE `id_order`='.$id_order.' AND `product_id`='.$id_product;
            $result = Db::getInstance()->getvalue($sql);
            if ($result) {
                return $result;
            }

            return false;
        }

        /**
         * [getOrderCurrentDataByOrderId :: To get booking information of the order by Order id].
         *
         * @param [int] $id_order [Id of the order]
         *
         * @return [array|false] [If data found Returns the array containing the information of the cart of the passed order id else returns false]
         */
        public function getOrderCurrentDataByOrderId($id_order)
        {
            $result = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `id_order`='.$id_order);
            if ($result) {
                return $result;
            } else {
                return false;
            }
        }

        /**
         * [getOrderFormatedBookinInfoByIdOrder : To get Order booking information with some additional information in a custom famated way].
         *
         * @param [Int] $id_order [Id of the order]
         *
         * @return [Array|false] [If data found returns cart booking information with some additional information else returns false]
         */
        public function getOrderFormatedBookinInfoByIdOrder($id_order)
        {
            $context = Context::getContext();
            $order_detail_data = $this->getOrderCurrentDataByOrderId((int) $id_order);
            if ($order_detail_data) {
                foreach ($order_detail_data as $key => $value) {
                    $product_image_id = Product::getCover($value['id_product']);
                    $link_rewrite = ((new Product((int) $value['id_product'], Configuration::get('PS_LANG_DEFAULT')))->link_rewrite[Configuration::get('PS_LANG_DEFAULT')]);

                    if ($product_image_id) {
                        $order_detail_data[$key]['image_link'] = $context->link->getImageLink($link_rewrite, $product_image_id['id_image'], 'small_default');
                    } else {
                        $order_detail_data[$key]['image_link'] = $context->link->getImageLink($link_rewrite, $context->language->iso_code.'-default', 'small_default');
                    }

                    $order_detail_data[$key]['room_type'] = (new Product((int) $value['id_product']))->name[Configuration::get('PS_LANG_DEFAULT')];
                    $order_detail_data[$key]['room_num'] = (new HotelRoomInformation((int) $value['id_room']))->room_num;
                    $order_detail_data[$key]['date_from'] = $value['date_from'];
                    $order_detail_data[$key]['date_to'] = $value['date_to'];
                    $num_days = $this->getNumberOfDays($value['date_from'], $value['date_to']);
                    $order_detail_data[$key]['quantity'] = $num_days;
                }
            }
            if ($order_detail_data) {
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
            $result = Db::getInstance()->getValue('SELECT MAX(`id_order_detail`) FROM `'._DB_PREFIX_.'order_detail` WHERE `id_order`='.$id_order);
            if ($result) {
                return $result;
            } else {
                return false;
            }
        }

        /**
         * [getOnlyOrderBookingData description].
         *
         * @param [type] $id_order    [description]
         * @param [type] $id_guest    [description]
         * @param [type] $id_product  [description]
         * @param int    $id_customer [description]
         *
         * @return [type] [description]
         */
        public function getOnlyOrderBookingData($id_order, $id_guest, $id_product, $id_customer = 0)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'htl_booking_detail` WHERE `id_order` = '.$id_order.' AND `id_product` = '.$id_product;

            if ($id_customer) {
                $sql .=  ' AND `id_customer` = '.$id_customer;
            }

            $order_book_data = Db::getInstance()->executeS($sql);

            if ($order_book_data) {
                return $order_book_data;
            } else {
                return false;
            }
        }
    }
