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

class HotelRoomInformation extends ObjectModel
{
    public $id;
    public $id_product;
    public $id_hotel;
    public $room_num;
    public $id_status;
    public $floor;
    public $comment;
    public $date_add;
    public $date_upd;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;
    const STATUS_TEMPORARY_INACTIVE = 3;

    const STATUS_SEARCH_LOS_UNSATISFIED = 4;
    const STATUS_SEARCH_OCCUPANCY_UNSATISFIED = 5;

    public static $definition = array(
        'table' => 'htl_room_information',
        'primary' => 'id',
        'fields' => array(
            'id_product' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_hotel' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'room_num' =>    array('type' => self::TYPE_STRING),
            'id_status' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'floor' =>        array('type' => self::TYPE_STRING),
            'comment' =>    array('type' => self::TYPE_STRING),
            'date_add' =>    array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>    array('type' => self::TYPE_DATE, 'validate' => 'isDate')
        ),
    );

    protected $webserviceParameters = array(
        'objectMethods' => array(
            'add' => 'addWs',
            'update' => 'updateWs',
            'delete' => 'deleteWs',
        ),
        'objectsNodeName' => 'rooms',
        'objectNodeName' => 'room',
        'fields' => array(
            'id_product' => array(
                'xlink_resource' => array(
                    'resourceName' => 'products',
                )
            ),
            'id_hotel' => array(
                'setter' => false,
                'xlink_resource' => array(
                    'resourceName' => 'hotels',
                )
            ),
        ),
    );

    public function update($null_values = false)
    {
        if ($idRoom = $this->id) {
            // delete rooms from cart which are set inactive
            if ($this->id_status == HotelRoomInformation::STATUS_INACTIVE) {
                $objCartBookingData = new HotelCartBookingData();
                if (!$objCartBookingData->deleteCartBookingData(0, 0, $idRoom)) {
                    return false;
                }
            }
        }
        return parent::update();
    }

    //Overrided ObjectModel::delete()
    public function delete()
    {
        if ($idRoom = $this->id) {
            $objCartBookingData = new HotelCartBookingData();
            if (!$this->deleteRoomDisableDates()
                || !$objCartBookingData->deleteCartBookingData(0, 0, $idRoom)
                || !parent::delete()
            ) {
                return false;
            }
        }
        return true;
    }

    public function deleteRoomDisableDates($idRoom = false)
    {
        if (!$idRoom) {
            $idRoom = $this->id;
            if (!$idRoom) {
                return false;
            }
        }

        $objRoomDisableDates = new HotelRoomDisableDates();
        $objRoomDisableDates->deleteRoomDisableDates((int)$idRoom);

        return true;
    }

    /**
     * [deleteByProductId :: To delete all rooms information which belongs to a room type(which is a product in real) By product id]
     * @param  [int] $id_product [Id of the product form which all rooms information to be deleted]
     * @return [Boolean]         [Returns true if deleted successfully else returns false]
     */
    public function deleteByProductId($idProduct)
    {
        if ($rooms = Db::getInstance()->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'htl_room_information` WHERE `id_product`='.(int) $idProduct
        )) {
            foreach ($rooms as $room) {
                $objRoomInfo = new HotelRoomInformation($room['id']);
                if (!$objRoomInfo->delete()) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getAllRoomStatus()
    {
        $status = array(
            'STATUS_ACTIVE' => array(
                'id' => HotelRoomInformation::STATUS_ACTIVE,
                'status' => self::getRoomStatusTitle(HotelRoomInformation::STATUS_ACTIVE)
            ),
            'STATUS_INACTIVE' => array(
                'id' => HotelRoomInformation::STATUS_INACTIVE,
                'status' => self::getRoomStatusTitle(HotelRoomInformation::STATUS_INACTIVE)
            ),
            'STATUS_TEMPORARY_INACTIVE' => array(
                'id' => HotelRoomInformation::STATUS_TEMPORARY_INACTIVE,
                'status' => self::getRoomStatusTitle(HotelRoomInformation::STATUS_TEMPORARY_INACTIVE)
            ),
        );
        return $status;
    }

    public static function getRoomStatusTitle($idStatus)
    {
        $moduleInstance = Module::getInstanceByName('hotelreservationsystem');
        $status = array(
            HotelRoomInformation::STATUS_ACTIVE => $moduleInstance->l('Active', 'hotelreservationsystem'),
            HotelRoomInformation::STATUS_INACTIVE => $moduleInstance->l('Inactive', 'hotelreservationsystem'),
            HotelRoomInformation::STATUS_TEMPORARY_INACTIVE => $moduleInstance->l('Temporarily Inactive', 'hotelreservationsystem'),
            HotelRoomInformation::STATUS_SEARCH_LOS_UNSATISFIED => $moduleInstance->l('Length of stay restriction not satisfied', 'hotelreservationsystem'),
            HotelRoomInformation::STATUS_SEARCH_OCCUPANCY_UNSATISFIED => $moduleInstance->l('Occupancy exceeds room capacity', 'hotelreservationsystem'),
        );

        return isset($status[$idStatus]) ? $status[$idStatus] : '';
    }

    /**
     * [getHotelRoomInfoByProductId :: To get all rooms information belong to a room type(product) by product id]
     * @param  [int] $id_product [Id of the product]
     * @return [array|false]     [If data found returns array containing all rooms information belongs to a room type(product) which product id is passed else returns false]
     */
    public function getHotelRoomInfoByProductId($id_product)
    {
        $result = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_room_information` WHERE `id_product`='.(int) $id_product);
        if ($result) {
            return $result;
        }
        return false;
    }

    /**
     * [getHotelRoomInfo :: To get all rooms information belong to a room type(product) and belongss to a hotel wwhich hotel id is 								passed]
     * @param  [type]  $id_product [Id of the room type(product)]
     * @param  [type]  $id_hotel   [Id of the hotel]
     * @param  [int] $is_getNum  [
     *                           If $is_getNum is passed
     *                           	 then returns number of rooms belong to a room type(product) and belongss to a hotel wwhich hotel 									id is passed
     *                           else
     *                             	Returns array containing all rooms information belong to a room type(product) and belongss to 										a hotel wwhich hotel id is passed
     *                           	  	 ]
     * @return [array|int|boolean] [If $is_getNum is passed
     *                           	 then returns number of rooms belong to a room type(product) and belongss to a hotel wwhich hotel 									id is passed
     *                           else
     *                             	if data found
     *                             		Returns array containing all rooms information belong to a room type(product) and belongss to 										a hotel wwhich hotel id is passed
     *                             	else
     *                             	returns false; ]
     */
    public function getHotelRoomInfo($id_product, $id_hotel, $is_getNum = 0)
    {
        $sql = "SELECT * FROM `"._DB_PREFIX_."htl_room_information` WHERE `id_product` = ".(int) $id_product." AND `id_hotel` = ".(int) $id_hotel;

        $rm_info = Db::getInstance()->executeS($sql);

        if ($is_getNum) {
            $no_row = Db::getInstance()->NumRows();
            return $no_row;
        } else {
            if ($rm_info) {
                return $rm_info;
            } else {
                return false;
            }
        }
    }

    /**
     * [getHotelRoomInfoById :: To get Information of a room by its id(primary key)]
     * @param  [int] $id         [id of the room in the table(primary key)]
     * @return [array|false]     [If data found returns array containing information of the room which id is passed else returns false]
     */
    public function getHotelRoomInfoById($id)
    {
        $sql = "SELECT `room_num` FROM `"._DB_PREFIX_."htl_room_information` WHERE `id` = ".(int) $id;
        $rm_num = Db::getInstance()->getValue($sql);

        if ($rm_num) {
            return $rm_num;
        } else {
            return false;
        }
    }

    public static function getHotelRoomsInfo($idHotel = null, $idProduct = null, $idLang = null)
    {
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $sql = 'SELECT hri.*, hri.`id_product`, hri.`id_hotel`, hrt.`adults`, hrt.`children`, hrt.`max_adults`,
        hrt.`max_children`, hrt.`max_guests`, hrt.`min_los`, hrt.`max_los`, pl.`name` AS room_type_name, hbil.`hotel_name` AS hotel_name
        FROM `'._DB_PREFIX_.'htl_room_information` hri
        INNER JOIN `'._DB_PREFIX_.'htl_room_type` hrt ON (hrt.`id_product` = hri.`id_product`)
        INNER JOIN `'._DB_PREFIX_.'htl_branch_info` hbi ON (hbi.`id` = hri.`id_hotel`)
        INNER JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbil ON (hbil.`id` = hri.`id_hotel` AND hbil.`id_lang` = '.(int) $idLang.')
        INNER JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
        INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.(int) $idLang.')
        WHERE 1 '.($idHotel ? ' AND hri.`id_hotel` = '.(int) $idHotel : '').
        ($idProduct ? ' AND hri.`id_product` = '.(int) $idProduct : '').'
        ORDER BY hri.`id_product`, hri.`id`';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Deprecated
     * [deleteHotelRoomInfoById :: To delete room information which id is passed]
     * @param  [int] $id_room_info [Id of the room which information(row in the table) to be deleted]
     * @return [Boolean]         [Returns true if deleted successfully else returns false]
     */
    public function deleteHotelRoomInfoById($id_room_info)
    {
        $result = Db::getInstance()->delete('htl_room_information', '`id` = '.(int)$id_room_info, 1);

        return $result;
    }

    public function getRoomTypeAvailableRoomsForDateRange($id_hotel, $id_product, $date_from, $date_to)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_room_information` where `id_hotel`='.(int) $id_hotel.' AND `id_product`='.(int) $id_product.' AND `id` NOT IN (SELECT `id_room` from `'._DB_PREFIX_.'htl_booking_detail` where `date_from`< \''.pSQL($date_to).'\' AND `date_to`>\''.pSQL($date_from).'\' AND `id_product`='.(int) $id_product.' AND `id_hotel`='.(int) $id_hotel.')');
    }

    public function getRoomTypeDisabledRoomsForDateRange($id_hotel, $id_product, $date_from, $date_to)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_room_information` where `id_hotel`='.(int) $id_hotel.' AND `id_product`='.(int) $id_product.' AND `id_status`=3 AND `id` NOT IN (SELECT `id_room` from `'._DB_PREFIX_.'htl_booking_detail` where `date_from`< \''.pSQL($date_to).'\' AND `date_to`>\''.pSQL($date_from).'\' AND `id_product`='.(int) $id_product.' AND `id_hotel`='.(int) $id_hotel.')');
    }

    public function getRoomTypeBookedRoomsForDateRange($id_hotel, $id_product, $date_from, $date_to)
    {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_booking_detail` where `date_from`< \''.pSQL($date_to).'\' AND `date_to`>\''.pSQL($date_from).'\' AND `id_product`='.(int) $id_product.' AND `id_hotel`='.(int) $id_hotel);
    }

    public function getFutureBookings($idRoom)
    {
        return Db::getInstance()->executeS('SELECT `id`, `id_order`, `date_from`, `date_to` FROM `'._DB_PREFIX_.'htl_booking_detail` where `date_to` > \''.pSQL(date('Y-m-d')).'\' AND `is_refunded` = 0 AND `id_room`='.(int) $idRoom);
    }

    // Webservice :: webservice add function
    public function addWs($autodate = true, $null_values = false)
    {
        $objRoomType = new HotelRoomType();
        if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($this->id_product)) {
            $this->id_hotel = $roomTypeInfo['id_hotel'];
            return $this->add($autodate, $null_values);
        } else {
            WebserviceRequest::getInstance()->setError(400, 'Invalid id product', 134);
        }

        return false;
    }

    // Webservice :: webservice update function
    public function updateWs($null_values = false)
    {
        $objRoomType = new HotelRoomType();
        if ($roomTypeInfo = $objRoomType->getRoomTypeInfoByIdProduct($this->id_product)) {
            $this->id_hotel = $roomTypeInfo['id_hotel'];
            return $this->update($null_values);
        } else {
            WebserviceRequest::getInstance()->setError(400, 'Invalid id product', 134);
        }

        return false;
    }

    // Webservice :: webservice delete function
    public function deleteWs()
    {
        return $this->delete();
    }

    /**
     * Total room count. Optional hotel and active filters.
     *
     * Replaces: AdminStatsController::getTotalRooms()
     *
     * @param mixed    $idHotel  int|array|null — null = all hotels (no restriction)
     * @param int|null $active   1 = active only, 0 = inactive only, null = both
     * @return int
     */
    public static function getTotalRooms($idHotel = null, $active = null)
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(hri.`id`)
            FROM `'._DB_PREFIX_.'htl_room_information` hri
            INNER JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
            WHERE p.`booking_product` = 1'
            .(!is_null($active) ? ' AND p.`active` = '.(int) $active : '')
            .(!is_null($idHotel) ? HotelBranchInformation::addHotelRestriction($idHotel, 'hri') : '')
        );
    }

    /**
     * Total active rooms per night across a date range.
     * Returns one entry per night: timestamp => int.
     * Note: does not account for temporarily-disabled rooms (future improvement).
     *
     * Replaces: AdminStatsController::getTotalRoomsForDiscreteDates() (simplified)
     *
     * @param array $params date_from, date_to, id_hotel
     * @return array  unix_timestamp => int
     */
    public static function getTotalRoomsForDiscreteDates(array $params)
    {
        $dateFrom = $params['date_from'];
        $dateTo   = isset($params['date_to']) ? $params['date_to'] : date('Y-m-d', strtotime('+1 day', strtotime($dateFrom)));
        $idHotel  = isset($params['id_hotel']) ? $params['id_hotel'] : null;

        $totalRooms = self::getTotalRooms($idHotel, 1);

        $result   = array();
        $dateTemp = $dateFrom;
        while ($dateTemp < $dateTo) {
            $result[strtotime($dateTemp)] = $totalRooms;
            $dateTemp = date('Y-m-d', strtotime('+1 day', strtotime($dateTemp)));
        }

        return $result;
    }

    /**
     * Available rooms per night (total minus booked, inactive, temporarily inactive).
     * Replaces: AdminStatsController::getAvailableRoomsForDiscreteDates()
     *
     * @param array $params date_from, date_to, id_hotel
     * @return array  unix_timestamp => int
     */
    public static function getAvailableRoomsForDiscreteDates(array $params)
    {
        $dateFrom  = $params['date_from'];
        $dateTo    = isset($params['date_to']) ? $params['date_to'] : date('Y-m-d', strtotime('+1 day', strtotime($dateFrom)));
        $idHotel   = isset($params['id_hotel'])   ? $params['id_hotel']          : null;
        $idProduct = isset($params['id_product']) ? (int) $params['id_product'] : 0;

        $result  = array();
        $current = $dateFrom;
        while ($current < $dateTo) {
            $next     = date('Y-m-d', strtotime('+1 day', strtotime($current)));
            $ts       = strtotime($current);
            $cacheKey = 'HotelRoomInformation::getAvailableRoomsForDiscreteDates_'.(int) $ts.
                (is_array($idHotel) ? implode('_', $idHotel) : (int) $idHotel).
                '_'.$idProduct;
            if (!Cache::isStored($cacheKey)) {
                $value = Db::getInstance()->getValue(
                    'SELECT (num_total - num_booked - num_inactive - num_temporarily_inactive) AS num_available
                    FROM (
                        SELECT (
                            SELECT IFNULL(COUNT(hri.`id`), 0)
                            FROM `'._DB_PREFIX_.'htl_room_information` hri
                            LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
                            WHERE p.`active` = 1'
                            .HotelBranchInformation::addHotelRestriction($idHotel, 'hri')
                            .($idProduct ? ' AND hri.`id_product` = '.$idProduct : '').'
                        ) AS num_total,
                        (
                            SELECT COUNT(DISTINCT hbd.`id_room`)
                            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                            LEFT JOIN `'._DB_PREFIX_.'htl_room_information` hri ON (hri.`id` = hbd.`id_room`)
                            LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
                            WHERE p.`active` = 1 AND hbd.`is_refunded` = 0 AND hbd.`is_cancelled` = 0
                            AND hbd.`date_from` < "'.pSQL($next).' 00:00:00"
                            AND hbd.`date_to` > "'.pSQL($current).' 00:00:00"'
                            .HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
                            .($idProduct ? ' AND hbd.`id_product` = '.$idProduct : '').'
                        ) AS num_booked,
                        (
                            SELECT IFNULL(COUNT(hri.`id`), 0)
                            FROM `'._DB_PREFIX_.'htl_room_information` hri
                            LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
                            WHERE hri.`id_status` = '.(int) self::STATUS_INACTIVE.'
                            AND p.`active` = 1'
                            .HotelBranchInformation::addHotelRestriction($idHotel, 'hri')
                            .($idProduct ? ' AND hri.`id_product` = '.$idProduct : '').'
                        ) AS num_inactive,
                        (
                            SELECT IFNULL(COUNT(hri.`id`), 0)
                            FROM `'._DB_PREFIX_.'htl_room_information` hri
                            LEFT JOIN `'._DB_PREFIX_.'htl_room_disable_dates` hrdd ON (hrdd.`id_room` = hri.`id`)
                            LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
                            WHERE hri.`id_status` = '.(int) self::STATUS_TEMPORARY_INACTIVE.'
                            AND ("'.pSQL($current).'" >= hrdd.`date_from` AND "'.pSQL($current).'" < hrdd.`date_to`)
                            AND p.`active` = 1'
                            .HotelBranchInformation::addHotelRestriction($idHotel, 'hri')
                            .($idProduct ? ' AND hri.`id_product` = '.$idProduct : '').'
                        ) AS num_temporarily_inactive
                    ) AS t'
                );
                Cache::store($cacheKey, $value);
            }
            $result[$ts] = Cache::retrieve($cacheKey);
            $current     = $next;
        }

        return $result;
    }

    /**
     * Day-by-day availability per room type for the Room Type Availability report.
     * Returns flat array of rows: one per (date × room type).
     *
     * @param array $params date_from, date_to, id_hotel, id_product, id_lang
     * @return array  date, id_product, room_type_name, total_rooms, rooms_booked, out_of_order, available, rate, occupancy_pct
     */
    public static function getAvailabilityReport(array $params)
    {
        $dateFrom  = pSQL($params['date_from']);
        $dateTo    = pSQL(isset($params['date_to']) ? $params['date_to'] : $params['date_from']);
        $idHotel   = isset($params['id_hotel'])   ? $params['id_hotel']          : false;
        $idProduct = isset($params['id_product']) ? (int) $params['id_product'] : 0;
        $idLang    = isset($params['id_lang'])    ? (int) $params['id_lang']     : 0;
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        $roomTypes = Db::getInstance()->executeS(
            'SELECT hri.`id_product`, pl.`name` AS room_type_name,
                COUNT(hri.`id`) AS total_rooms,
                SUM(CASE WHEN hri.`id_status` IN ('.(int) self::STATUS_INACTIVE.','.(int) self::STATUS_TEMPORARY_INACTIVE.') THEN 1 ELSE 0 END) AS ooo_rooms
            FROM `'._DB_PREFIX_.'htl_room_information` hri
            INNER JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product` AND p.`active` = 1)
            INNER JOIN `'._DB_PREFIX_.'product_lang` pl
                ON (pl.`id_product` = hri.`id_product` AND pl.`id_lang` = '.(int) $idLang.')
            WHERE 1'
            .HotelBranchInformation::addHotelRestriction($idHotel, 'hri')
            .($idProduct ? ' AND hri.`id_product` = '.$idProduct : '').'
            GROUP BY hri.`id_product`'
        );

        if (!$roomTypes) {
            return array();
        }

        $bookingsRaw = Db::getInstance()->executeS(
            'SELECT hbd.`id_product`, hbd.`id_room`, hbd.`date_from`, hbd.`date_to`
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            INNER JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = hbd.`id_order` AND o.`valid` = 1)
            WHERE hbd.`is_refunded` = 0 AND hbd.`is_cancelled` = 0
            AND hbd.`date_from` < "'.$dateTo.'" AND hbd.`date_to` > "'.$dateFrom.'"'
            .HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
            .($idProduct ? ' AND hbd.`id_product` = '.$idProduct : '')
        );

        $bookingsByProduct = array();
        foreach ($bookingsRaw as $b) {
            $bookingsByProduct[$b['id_product']][] = $b;
        }

        $result  = array();
        $current = $dateFrom;
        while ($current < $dateTo) {
            $next = date('Y-m-d', strtotime('+1 day', strtotime($current)));
            foreach ($roomTypes as $rt) {
                $idProd      = $rt['id_product'];
                $bookedRooms = array();
                if (isset($bookingsByProduct[$idProd])) {
                    foreach ($bookingsByProduct[$idProd] as $b) {
                        if ($b['date_from'] < $next && $b['date_to'] > $current) {
                            $bookedRooms[$b['id_room']] = true;
                        }
                    }
                }
                $booked    = count($bookedRooms);
                $total     = (int) $rt['total_rooms'];
                $ooo       = (int) $rt['ooo_rooms'];
                $available = max(0, $total - $booked - $ooo);
                $result[]  = array(
                    'date'           => $current,
                    'id_product'     => $idProd,
                    'room_type_name' => $rt['room_type_name'],
                    'total_rooms'    => $total,
                    'rooms_booked'   => $booked,
                    'out_of_order'   => $ooo,
                    'available'      => $available,
                    'occupancy_pct'  => $total ? round($booked / $total * 100, 1) : 0.0,
                );
            }
            $current = $next;
        }

        return $result;
    }

    /**
     * Room-night occupancy breakdown (total / occupied / available / unavailable).
     * Replaces: AdminStatsController::getOccupancyData()
     *
     * @param array $params date_from, date_to, id_hotel
     * @return array  count_total, count_occupied, count_available, count_unavailable
     */
    public static function getOccupancyData(array $params)
    {
        $dateFrom  = $params['date_from'];
        $dateTo    = isset($params['date_to']) ? $params['date_to'] : $dateFrom;
        $idHotel   = isset($params['id_hotel'])   ? $params['id_hotel']          : false;
        $idProduct = isset($params['id_product']) ? (int) $params['id_product'] : 0;

        if ($dateFrom == $dateTo) {
            $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateTo)));
        }

        $dtFrom    = new DateTime($dateFrom);
        $dtTo      = new DateTime($dateTo);
        $numNights = (int) $dtFrom->diff($dtTo)->days;
        if ($numNights <= 0) {
            $numNights = 1;
        }

        $data = array('count_total' => 0, 'count_occupied' => 0, 'count_available' => 0, 'count_unavailable' => 0);

        $totalRooms = (int) Db::getInstance()->getValue(
            'SELECT IFNULL(COUNT(hri.`id`), 0)
            FROM `'._DB_PREFIX_.'htl_room_information` hri
            LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
            WHERE p.`active` = 1 AND p.`booking_product` = 1'
            .HotelBranchInformation::addHotelRestriction($idHotel, 'hri')
            .($idProduct ? ' AND hri.`id_product` = '.$idProduct : '')
        );
        $data['count_total'] = $totalRooms * $numNights;

        $checkedOutStatus = (int) HotelBookingDetail::STATUS_CHECKED_OUT;

        $data['count_occupied'] = (int) Db::getInstance()->getValue(
            'SELECT IFNULL(SUM(
                GREATEST(0, DATEDIFF(
                    LEAST(
                        IF(hbd.`id_status` = '.$checkedOutStatus.',
                            DATE_FORMAT(hbd.`check_out`, "%Y-%m-%d"), hbd.`date_to`),
                        "'.pSQL($dateTo).'"
                    ),
                    GREATEST(hbd.`date_from`, "'.pSQL($dateFrom).'")
                ))
            ), 0)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            LEFT JOIN `'._DB_PREFIX_.'htl_room_information` hri ON (hri.`id` = hbd.`id_room`)
            LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
            WHERE p.`active` = 1 AND p.`booking_product` = 1 AND hbd.`is_refunded` = 0 AND hbd.`is_cancelled` = 0
            AND hbd.`date_from` < "'.pSQL($dateTo).'"
            AND IF(hbd.`id_status` = '.$checkedOutStatus.',
                DATE_FORMAT(hbd.`check_out`, "%Y-%m-%d"), hbd.`date_to`
            ) > "'.pSQL($dateFrom).'"'
            .HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
            .($idProduct ? ' AND hbd.`id_product` = '.$idProduct : '')
        );

        $countInactiveRooms = (int) Db::getInstance()->getValue(
            'SELECT IFNULL(COUNT(hri.`id`), 0)
            FROM `'._DB_PREFIX_.'htl_room_information` hri
            LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
            WHERE hri.`id_status` = '.(int) self::STATUS_INACTIVE.'
            AND p.`active` = 1 AND p.`booking_product` = 1'
            .HotelBranchInformation::addHotelRestriction($idHotel, 'hri')
            .($idProduct ? ' AND hri.`id_product` = '.$idProduct : '')
        );

        $countOccupiedInactiveNights = (int) Db::getInstance()->getValue(
            'SELECT IFNULL(SUM(
                GREATEST(0, DATEDIFF(
                    LEAST(
                        IF(hbd.`id_status` = '.$checkedOutStatus.',
                            DATE_FORMAT(hbd.`check_out`, "%Y-%m-%d"), hbd.`date_to`),
                        "'.pSQL($dateTo).'"
                    ),
                    GREATEST(hbd.`date_from`, "'.pSQL($dateFrom).'")
                ))
            ), 0)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            LEFT JOIN `'._DB_PREFIX_.'htl_room_information` hri ON (hri.`id` = hbd.`id_room`)
            LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
            WHERE hri.`id_status` = '.(int) self::STATUS_INACTIVE.'
            AND p.`active` = 1 AND p.`booking_product` = 1 AND hbd.`is_refunded` = 0 AND hbd.`is_cancelled` = 0
            AND hbd.`date_from` < "'.pSQL($dateTo).'"
            AND IF(hbd.`id_status` = '.$checkedOutStatus.',
                DATE_FORMAT(hbd.`check_out`, "%Y-%m-%d"), hbd.`date_to`
            ) > "'.pSQL($dateFrom).'"'
            .HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
            .($idProduct ? ' AND hbd.`id_product` = '.$idProduct : '')
        );

        $countTemporarilyInactiveNights = (int) Db::getInstance()->getValue(
            'SELECT IFNULL(SUM(
                GREATEST(0, DATEDIFF(
                    LEAST(hrdd.`date_to`, "'.pSQL($dateTo).'"),
                    GREATEST(hrdd.`date_from`, "'.pSQL($dateFrom).'")
                ))
            ), 0)
            FROM `'._DB_PREFIX_.'htl_room_information` hri
            INNER JOIN `'._DB_PREFIX_.'htl_room_disable_dates` hrdd ON (hrdd.`id_room` = hri.`id`)
            LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
            WHERE hri.`id_status` = '.(int) self::STATUS_TEMPORARY_INACTIVE.'
            AND hrdd.`date_from` < "'.pSQL($dateTo).'" AND hrdd.`date_to` > "'.pSQL($dateFrom).'"
            AND p.`active` = 1 AND p.`booking_product` = 1'
            .HotelBranchInformation::addHotelRestriction($idHotel, 'hri')
            .($idProduct ? ' AND hri.`id_product` = '.$idProduct : '')
        );

        $countOccupiedTemporaryInactiveNights = (int) Db::getInstance()->getValue(
            'SELECT IFNULL(SUM(
                GREATEST(0, DATEDIFF(
                    LEAST(
                        IF(hbd.`id_status` = '.$checkedOutStatus.',
                            DATE_FORMAT(hbd.`check_out`, "%Y-%m-%d"), hbd.`date_to`),
                        hrdd.`date_to`, "'.pSQL($dateTo).'"
                    ),
                    GREATEST(hbd.`date_from`, hrdd.`date_from`, "'.pSQL($dateFrom).'")
                ))
            ), 0)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            INNER JOIN `'._DB_PREFIX_.'htl_room_disable_dates` hrdd ON (hrdd.`id_room` = hbd.`id_room`)
            INNER JOIN `'._DB_PREFIX_.'htl_room_information` hri ON (hri.`id` = hbd.`id_room`)
            LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
            WHERE hri.`id_status` = '.(int) self::STATUS_TEMPORARY_INACTIVE.'
            AND p.`active` = 1 AND p.`booking_product` = 1 AND hbd.`is_refunded` = 0 AND hbd.`is_cancelled` = 0
            AND hrdd.`date_from` < "'.pSQL($dateTo).'" AND hrdd.`date_to` > "'.pSQL($dateFrom).'"
            AND hbd.`date_from` < "'.pSQL($dateTo).'"
            AND IF(hbd.`id_status` = '.$checkedOutStatus.',
                DATE_FORMAT(hbd.`check_out`, "%Y-%m-%d"), hbd.`date_to`
            ) > "'.pSQL($dateFrom).'"'
            .HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
            .($idProduct ? ' AND hbd.`id_product` = '.$idProduct : '')
        );

        $inactiveUnavailable  = max(0, ($countInactiveRooms * $numNights) - $countOccupiedInactiveNights);
        $temporaryUnavailable = max(0, $countTemporarilyInactiveNights - $countOccupiedTemporaryInactiveNights);

        $data['count_unavailable'] = (int) ($inactiveUnavailable + $temporaryUnavailable);
        $countAvailable = (int) $data['count_total'] - (int) $data['count_occupied'] - (int) $data['count_unavailable'];
        $data['count_available'] = $countAvailable > 0 ? $countAvailable : 0;

        return $data;
    }

    /**
     * Distinct active room types for a hotel, for use in filter dropdowns.
     *
     * @param array $params  id_hotel (required), id_lang (optional)
     * @return array  rows: id_product, room_type_name
     */
    public static function getRoomTypes(array $params)
    {
        $idHotel = isset($params['id_hotel']) ? (int) $params['id_hotel'] : 0;
        $idLang  = !empty($params['id_lang']) ? (int) $params['id_lang'] : (int) Context::getContext()->language->id;

        return Db::getInstance()->executeS(
            'SELECT DISTINCT hri.`id_product`, pl.`name` AS room_type_name
            FROM `'._DB_PREFIX_.'htl_room_information` hri
            INNER JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
            INNER JOIN `'._DB_PREFIX_.'product_lang` pl
                ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.$idLang.')
            WHERE p.`active` = 1 AND p.`booking_product` = 1'
            . ($idHotel ? ' AND hri.`id_hotel` = '.$idHotel : '')
            . ' ORDER BY pl.`name`'
        );
    }

    /**
     * Room status for a date range — for reporting use only.
     * A room is Occupied if any booking overlaps [date_from, date_to).
     * A room is Under Maintenance if any disable-date entry overlaps the range
     * AND hri.id_status = STATUS_TEMPORARY_INACTIVE; otherwise treated as Active.
     * Each room appears exactly once regardless of how many bookings fall in range.
     *
     * @param array $params  date_from, date_to, id_hotel, id_product, floor, id_lang
     * @return array  rows: id_room, room_num, floor, id_status, room_type_name, hotel_name,
     *                      id_order, date_from, date_to, booking_status, guest_name
     */
    public static function getRoomStatusForReports(array $params)
    {
        $dateFrom              = pSQL($params['date_from']);
        $dateTo                = pSQL(isset($params['date_to']) ? $params['date_to'] : $params['date_from']);
        $idHotel               = isset($params['id_hotel'])               ? $params['id_hotel']                : false;
        $idProduct             = isset($params['id_product'])             ? (int) $params['id_product']        : 0;
        $idLang                = isset($params['id_lang'])                ? (int) $params['id_lang']           : 0;
        $floor                 = isset($params['floor'])                  ? pSQL($params['floor'])             : '';
        $housekeepingInstalled = !empty($params['housekeeping_installed']);
        if (!$idLang) {
            $idLang = Context::getContext()->language->id;
        }

        return Db::getInstance()->executeS(
            'SELECT hri.`id` AS id_room, hri.`room_num`, hri.`floor`,
            CASE WHEN hri.`id_status` = '.(int) self::STATUS_TEMPORARY_INACTIVE.' AND hrdd.`id_room` IS NULL
                 THEN '.(int) self::STATUS_ACTIVE.'
                 ELSE hri.`id_status`
            END AS id_status,
            pl.`name` AS room_type_name, hbil.`hotel_name`,
            bkgs.`id_order`, bkgs.`date_from`, bkgs.`date_to`,
            bkgs.`id_status` AS booking_status,
            CONCAT(c.`firstname`, " ", c.`lastname`) AS guest_name'
            .($housekeepingInstalled ? ', hri.`id_housekeeping_status`' : '').'
            FROM `'._DB_PREFIX_.'htl_room_information` hri
            INNER JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
            INNER JOIN `'._DB_PREFIX_.'product_lang` pl
                ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.(int) $idLang.')
            INNER JOIN `'._DB_PREFIX_.'htl_branch_info` hbi ON (hbi.`id` = hri.`id_hotel`)
            INNER JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbil
                ON (hbil.`id` = hri.`id_hotel` AND hbil.`id_lang` = '.(int) $idLang.')
            LEFT JOIN (
                SELECT `id_room`
                FROM `'._DB_PREFIX_.'htl_room_disable_dates`
                WHERE `date_from` < "'.$dateTo.'" AND `date_to` > "'.$dateFrom.'"
                GROUP BY `id_room`
            ) hrdd ON (hrdd.`id_room` = hri.`id`)
            LEFT JOIN (
                SELECT hbd.`id_room`,
                    MIN(hbd.`id_order`) AS `id_order`,
                    MIN(hbd.`date_from`) AS `date_from`,
                    MAX(hbd.`date_to`) AS `date_to`,
                    MIN(hbd.`id_status`) AS `id_status`,
                    MIN(hbd.`id_customer`) AS `id_customer`
                FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                WHERE hbd.`is_refunded` = 0
                AND hbd.`date_from` < "'.$dateTo.'" AND hbd.`date_to` > "'.$dateFrom.'"
                GROUP BY hbd.`id_room`
            ) bkgs ON (bkgs.`id_room` = hri.`id`)
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = bkgs.`id_customer`)
            WHERE p.`active` = 1 AND p.`booking_product` = 1'
            .($floor     ? ' AND hri.`floor` = "'.$floor.'"'     : '')
            .($idProduct ? ' AND hri.`id_product` = '.$idProduct : '')
            .($idHotel ? HotelBranchInformation::addHotelRestriction($idHotel, 'hri') : '').'
            ORDER BY hbil.`hotel_name`, pl.`name`, hri.`room_num`'
        );
    }

    /**
     * @param array $params  id_hotel, id_product
     * @return array  rows with 'floor' key, sorted
     */
    public static function getDistinctFloors(array $params = array())
    {
        $idHotel   = isset($params['id_hotel'])   ? $params['id_hotel']         : false;
        $idProduct = isset($params['id_product']) ? (int) $params['id_product'] : 0;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT DISTINCT hri.`floor`
            FROM `'._DB_PREFIX_.'htl_room_information` hri
            INNER JOIN `'._DB_PREFIX_.'product` p
                ON (p.`id_product` = hri.`id_product` AND p.`active` = 1 AND p.`booking_product` = 1)
            WHERE hri.`floor` != "" AND hri.`floor` IS NOT NULL'
            .($idProduct ? ' AND hri.`id_product` = '.$idProduct : '')
            .($idHotel ? HotelBranchInformation::addHotelRestriction($idHotel, 'hri') : '').'
            ORDER BY hri.`floor`'
        );
    }
}
