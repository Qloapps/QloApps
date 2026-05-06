<?php
/**
 * HotelAnalyticsCore — centralized analytics gateway for QloApps.
 *
 * Replaces the fragmented static methods in AdminStatsController with
 * consolidated, domain-specific functions. All functions accept a single
 * $params array so callers can be configured without changing function
 * signatures. The mathematical logic is a 1:1 replication of the canonical
 * AdminStatsController calculations.
 *
 * Base tables:
 *   - Room metrics  → htl_booking_detail
 *   - Service/demand metrics → service_product_order_detail + htl_booking_demands
 *
 * JOINs are added only when a specific $params key strictly requires them
 * (e.g., p.active=1 check, conversion_rate from orders).
 *
 * @see controllers/admin/AdminStatsController.php (canonical source)
 */
class HotelAnalyticsCore extends ObjectModel
{
    public static $definition = array(
        'table' => 'htl_booking_detail',
        'primary' => 'id',
        'fields' => array(),
    );

    // =========================================================================
    // SECTION 1: ROOM REVENUE
    // =========================================================================

    /**
     * Returns room revenue (excluding services/demands) normalized to a
     * per-night basis, divided by currency conversion rate.
     *
     * Replicates: AdminStatsController::getRoomsRevenueForDiscreteDates()
     *
     * $params keys:
     *   - date_from         string  'Y-m-d'  Start of date range (inclusive)
     *   - date_to           string  'Y-m-d'  End of date range (exclusive last night)
     *   - id_hotel          mixed   int|array|null  Hotel filter. null = all hotels
     *                                the employee has access to (via addHotelRestriction)
     *   - id_product        int|null  Filter by room type product. null = all
     *   - datewise_breakdown bool   If true returns array keyed by unix timestamp
     *                               (one entry per day). If false returns float.
     *   - use_cache         bool    Use Cache layer for per-day queries (default true)
     *
     * @param array $params
     * @return float|array  scalar when datewise_breakdown=false, date-keyed array otherwise
     */
    public static function getRoomRevenue(array $params)
    {
        $dateFrom = pSQL($params['date_from']);
        $dateTo   = isset($params['date_to']) ? pSQL($params['date_to']) : $dateFrom;
        $idHotel  = isset($params['id_hotel']) ? $params['id_hotel'] : null;
        $idProduct = isset($params['id_product']) ? (int) $params['id_product'] : 0;
        $useCache = isset($params['use_cache']) ? (bool) $params['use_cache'] : true;
        $datewise = isset($params['datewise_breakdown']) ? (bool) $params['datewise_breakdown'] : false;

        $productFilter = $idProduct ? ' AND hbd.`id_product` = '.$idProduct : '';
        $hotelFilter   = !is_null($idHotel)
            ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
            : '';

        if ($datewise) {
            // Per-day breakdown: replicate the while-loop + one-query-per-day
            // pattern from AdminStatsController::getRoomsRevenueForDiscreteDates().
            $discreteDates = self::buildDiscreteDates($dateFrom, $dateTo);
            $result = array();
            foreach ($discreteDates as $d) {
                $cacheKey = 'HotelAnalyticsCore::getRoomRevenue_'
                    .(int) $d['timestamp_from'].'_'
                    .(is_array($idHotel) ? implode('_', $idHotel) : (int) $idHotel)
                    .'_'.$idProduct;
                if (!Cache::isStored($cacheKey) || !$useCache) {
                    $sql = 'SELECT IFNULL(SUM(
                                (hbd.`total_price_tax_excl` / o.`conversion_rate`)
                                / DATEDIFF(hbd.`date_to`, hbd.`date_from`)
                            ), 0)
                            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                            LEFT JOIN `'._DB_PREFIX_.'product` p
                                ON (p.`id_product` = hbd.`id_product`)
                            LEFT JOIN `'._DB_PREFIX_.'orders` o
                                ON (o.`id_order` = hbd.`id_order`)
                            WHERE p.`active` = 1
                            AND o.`valid` = 1
                            AND hbd.`is_refunded` = 0
                            AND hbd.`date_from` < "'.pSQL($d['date_to']).' 00:00:00"
                            AND hbd.`date_to`   > "'.pSQL($d['date_from']).' 00:00:00"'
                            .$productFilter
                            .$hotelFilter;
                    Cache::store($cacheKey, Db::getInstance()->getValue($sql));
                }
                $result[$d['timestamp_from']] = Cache::retrieve($cacheKey);
            }
            return $result;
        }

        // Aggregate scalar: LEAST/GREATEST overlap for a single query covering
        // the full range. Equivalent to summing the per-day loop, more efficient.
        $sql = 'SELECT IFNULL(SUM(
                    (hbd.`total_price_tax_excl` / o.`conversion_rate`)
                    * DATEDIFF(
                        LEAST(hbd.`date_to`,   DATE_ADD("'.pSQL($dateTo).'", INTERVAL 1 DAY)),
                        GREATEST(hbd.`date_from`, "'.pSQL($dateFrom).'" )
                    ) / DATEDIFF(hbd.`date_to`, hbd.`date_from`)
                ), 0)
                FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                LEFT JOIN `'._DB_PREFIX_.'product` p
                    ON (p.`id_product` = hbd.`id_product`)
                LEFT JOIN `'._DB_PREFIX_.'orders` o
                    ON (o.`id_order` = hbd.`id_order`)
                WHERE p.`active` = 1
                AND o.`valid` = 1
                AND hbd.`is_refunded` = 0
                AND hbd.`date_from` < "'.pSQL($dateTo).' 23:59:59"
                AND hbd.`date_to`   > "'.pSQL($dateFrom).' 00:00:00"'
                .$productFilter
                .$hotelFilter;

        $value = Db::getInstance()->getValue($sql);
        return $value ? (float) $value : 0.0;
    }

    // =========================================================================
    // SECTION 2: SERVICE REVENUE
    // =========================================================================

    /**
     * Returns service revenue (room-linked services + extra demands), normalized
     * per night using the same DATEDIFF of the parent booking.
     *
     * Replicates: AdminStatsController::getServicesRevenueForDiscreteDates()
     *             (which runs two sub-queries: services + demands)
     *
     * $params keys:
     *   - date_from          string   'Y-m-d'
     *   - date_to            string   'Y-m-d'
     *   - id_hotel           mixed    int|array|null
     *   - datewise_breakdown bool
     *
     * @param array $params
     * @return float|array
     */
    public static function getServiceRevenue(array $params)
    {
        $dateFrom = pSQL($params['date_from']);
        $dateTo   = isset($params['date_to']) ? pSQL($params['date_to']) : $dateFrom;
        $idHotel  = isset($params['id_hotel']) ? $params['id_hotel'] : null;
        $datewise = isset($params['datewise_breakdown']) ? (bool) $params['datewise_breakdown'] : false;

        $hotelFilter = !is_null($idHotel)
            ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
            : '';

        if ($datewise) {
            $discreteDates = self::buildDiscreteDates($dateFrom, $dateTo);
            $result = array();
            foreach ($discreteDates as $d) {
                $total = 0;

                // 1) Room-linked service products (service_product_order_detail)
                $sqlServices = 'SELECT SUM(
                        (spod.`total_price_tax_excl` / o.`conversion_rate`)
                        / DATEDIFF(hbd.`date_to`, hbd.`date_from`)
                    )
                    FROM `'._DB_PREFIX_.'service_product_order_detail` spod
                    LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
                        ON (spod.`id_htl_booking_detail` = hbd.`id`)
                    LEFT JOIN `'._DB_PREFIX_.'product` p
                        ON (p.`id_product` = hbd.`id_product`)
                    LEFT JOIN `'._DB_PREFIX_.'orders` o
                        ON (o.`id_order` = hbd.`id_order`)
                    WHERE p.`active` = 1
                    AND o.`valid` = 1
                    AND hbd.`is_refunded` = 0
                    AND hbd.`date_from` < "'.pSQL($d['date_to']).' 00:00:00"
                    AND hbd.`date_to`   > "'.pSQL($d['date_from']).' 00:00:00"'
                    .$hotelFilter;
                if ($v = Db::getInstance()->getValue($sqlServices)) {
                    $total += (float) $v;
                }

                // 2) Extra demands (htl_booking_demands)
                $sqlDemands = 'SELECT SUM(
                        (hdmd.`total_price_tax_excl` / o.`conversion_rate`)
                        / DATEDIFF(hbd.`date_to`, hbd.`date_from`)
                    )
                    FROM `'._DB_PREFIX_.'htl_booking_demands` hdmd
                    LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
                        ON (hdmd.`id_htl_booking` = hbd.`id`)
                    LEFT JOIN `'._DB_PREFIX_.'product` p
                        ON (p.`id_product` = hbd.`id_product`)
                    LEFT JOIN `'._DB_PREFIX_.'orders` o
                        ON (o.`id_order` = hbd.`id_order`)
                    WHERE p.`active` = 1
                    AND o.`valid` = 1
                    AND hbd.`is_refunded` = 0
                    AND hbd.`date_from` < "'.pSQL($d['date_to']).' 00:00:00"
                    AND hbd.`date_to`   > "'.pSQL($d['date_from']).' 00:00:00"'
                    .$hotelFilter;
                if ($v = Db::getInstance()->getValue($sqlDemands)) {
                    $total += (float) $v;
                }

                $result[$d['timestamp_from']] = $total;
            }
            return $result;
        }

        // Aggregate scalar
        $total = 0;

        $sqlServices = 'SELECT IFNULL(SUM(
                (spod.`total_price_tax_excl` / o.`conversion_rate`)
                * DATEDIFF(
                    LEAST(hbd.`date_to`,   DATE_ADD("'.pSQL($dateTo).'", INTERVAL 1 DAY)),
                    GREATEST(hbd.`date_from`, "'.pSQL($dateFrom).'" )
                ) / DATEDIFF(hbd.`date_to`, hbd.`date_from`)
            ), 0)
            FROM `'._DB_PREFIX_.'service_product_order_detail` spod
            LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
                ON (spod.`id_htl_booking_detail` = hbd.`id`)
            LEFT JOIN `'._DB_PREFIX_.'product` p
                ON (p.`id_product` = hbd.`id_product`)
            LEFT JOIN `'._DB_PREFIX_.'orders` o
                ON (o.`id_order` = hbd.`id_order`)
            WHERE p.`active` = 1
            AND o.`valid` = 1
            AND hbd.`is_refunded` = 0
            AND hbd.`date_from` < "'.pSQL($dateTo).' 23:59:59"
            AND hbd.`date_to`   > "'.pSQL($dateFrom).' 00:00:00"'
            .$hotelFilter;
        if ($v = Db::getInstance()->getValue($sqlServices)) {
            $total += (float) $v;
        }

        $sqlDemands = 'SELECT IFNULL(SUM(
                (hdmd.`total_price_tax_excl` / o.`conversion_rate`)
                * DATEDIFF(
                    LEAST(hbd.`date_to`,   DATE_ADD("'.pSQL($dateTo).'", INTERVAL 1 DAY)),
                    GREATEST(hbd.`date_from`, "'.pSQL($dateFrom).'" )
                ) / DATEDIFF(hbd.`date_to`, hbd.`date_from`)
            ), 0)
            FROM `'._DB_PREFIX_.'htl_booking_demands` hdmd
            LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
                ON (hdmd.`id_htl_booking` = hbd.`id`)
            LEFT JOIN `'._DB_PREFIX_.'product` p
                ON (p.`id_product` = hbd.`id_product`)
            LEFT JOIN `'._DB_PREFIX_.'orders` o
                ON (o.`id_order` = hbd.`id_order`)
            WHERE p.`active` = 1
            AND o.`valid` = 1
            AND hbd.`is_refunded` = 0
            AND hbd.`date_from` < "'.pSQL($dateTo).' 23:59:59"
            AND hbd.`date_to`   > "'.pSQL($dateFrom).' 00:00:00"'
            .$hotelFilter;
        if ($v = Db::getInstance()->getValue($sqlDemands)) {
            $total += (float) $v;
        }

        return $total;
    }

    // =========================================================================
    // SECTION 3: TOTAL REVENUE (rooms + services combined)
    // =========================================================================

    /**
     * Returns total revenue = room revenue + service revenue.
     *
     * Replicates: AdminStatsController::getTotalRevenueForDiscreteDates()
     *
     * $params keys: same as getRoomRevenue() and getServiceRevenue()
     *   - date_from, date_to, id_hotel, datewise_breakdown
     *
     * @param array $params
     * @return float|array
     */
    public static function getTotalRevenue(array $params)
    {
        $roomRevenue    = self::getRoomRevenue($params);
        $serviceRevenue = self::getServiceRevenue($params);

        if (isset($params['datewise_breakdown']) && $params['datewise_breakdown']) {
            $result = array();
            foreach ($roomRevenue as $timestamp => $roomVal) {
                $result[$timestamp] = $roomVal
                    + (isset($serviceRevenue[$timestamp]) ? $serviceRevenue[$timestamp] : 0);
            }
            return $result;
        }

        return $roomRevenue + $serviceRevenue;
    }

    // =========================================================================
    // SECTION 4: ROOM INVENTORY (total rooms per hotel)
    // =========================================================================

    /**
     * Returns the count of physical rooms. When datewise_breakdown is true,
     * returns per-day counts using AdminStatsController::getTotalRoomsForDiscreteDates()
     * logic (subtracts inactive + temporarily-inactive rooms per day).
     *
     * Replicates: AdminStatsController::getTotalRooms()
     *             AdminStatsController::getTotalRoomsForDiscreteDates()
     *
     * $params keys:
     *   - id_hotel           mixed    int|array|null
     *   - active             bool|null  Filter by product.active. null = all
     *   - datewise_breakdown bool     Requires date_from + date_to
     *   - date_from          string   Required if datewise_breakdown=true
     *   - date_to            string   Required if datewise_breakdown=true
     *   - use_cache          bool
     *
     * @param array $params
     * @return int|array
     */
    public static function getRoomInventory(array $params)
    {
        $idHotel  = isset($params['id_hotel']) ? $params['id_hotel'] : null;
        $active   = isset($params['active'])   ? $params['active']   : null;
        $datewise = isset($params['datewise_breakdown']) ? (bool) $params['datewise_breakdown'] : false;
        $useCache = isset($params['use_cache']) ? (bool) $params['use_cache'] : true;

        $hotelFilterHri = !is_null($idHotel)
            ? HotelBranchInformation::addHotelRestriction($idHotel, 'hri')
            : '';
        $hotelFilterHbd = !is_null($idHotel)
            ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
            : '';
        $activeFilter = !is_null($active) ? ' AND p.`active` = '.(int) $active : '';

        if (!$datewise) {
            $sql = 'SELECT COUNT(hri.`id`)
                    FROM `'._DB_PREFIX_.'htl_room_information` hri
                    INNER JOIN `'._DB_PREFIX_.'product` p
                        ON (p.`id_product` = hri.`id_product`)
                    WHERE p.`booking_product` = 1'
                    .$activeFilter
                    .$hotelFilterHri;
            return (int) Db::getInstance()->getValue($sql);
        }

        $dateFrom = pSQL($params['date_from']);
        $dateTo   = pSQL($params['date_to']);
        $discreteDates = self::buildDiscreteDates($dateFrom, $dateTo);
        $result = array();

        foreach ($discreteDates as $d) {
            $cacheKey = 'HotelAnalyticsCore::getRoomInventory_'
                .(int) $d['timestamp_from'].'_'
                .(is_array($idHotel) ? implode('_', $idHotel) : (int) $idHotel);
            if (!Cache::isStored($cacheKey) || !$useCache) {
                // Replicates AdminStatsController::getTotalRoomsForDiscreteDates()
                // subquery structure exactly.
                $sql = 'SELECT (num_total_added - num_inactive - num_temporarily_inactive) AS num_total
                FROM (
                    SELECT (
                        SELECT IFNULL(COUNT(hri.`id`), 0)
                        FROM `'._DB_PREFIX_.'htl_room_information` hri
                        LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
                        WHERE p.`active` = 1'.$hotelFilterHri.'
                    ) AS num_total_added,
                    (
                        SELECT IFNULL(COUNT(hri.`id`), 0)
                        FROM `'._DB_PREFIX_.'htl_room_information` hri
                        LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
                        WHERE hri.`id_status` = '.(int) HotelRoomInformation::STATUS_INACTIVE.'
                        AND p.`active` = 1'.$hotelFilterHri.'
                    ) AS num_inactive,
                    (
                        SELECT IFNULL(COUNT(hri.`id`), 0)
                        FROM `'._DB_PREFIX_.'htl_room_information` hri
                        LEFT JOIN `'._DB_PREFIX_.'htl_room_disable_dates` hrdd
                            ON (hrdd.`id_room` = hri.`id`)
                        LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
                        WHERE hri.`id_status` = '.(int) HotelRoomInformation::STATUS_TEMPORARY_INACTIVE.'
                        AND p.`active` = 1
                        AND ("'.pSQL($d['date_to']).'" > hrdd.`date_from`
                             AND "'.pSQL($d['date_from']).'" < hrdd.`date_to`)'
                        .$hotelFilterHri.'
                    ) AS num_temporarily_inactive
                ) AS counts';
                Cache::store($cacheKey, Db::getInstance()->getValue($sql));
            }
            $result[$d['timestamp_from']] = (int) Cache::retrieve($cacheKey);
        }
        return $result;
    }

    // =========================================================================
    // SECTION 5: OCCUPANCY STATS (booked rooms)
    // =========================================================================

    /**
     * Returns booked room counts for a date range.
     *
     * Replicates: AdminStatsController::getOccupiedRoomsForDiscreteDates()
     *             AdminStatsController::getTotalBookedRooms()
     *             AdminStatsController::getOccupancyData() (when metric_type = 'full')
     *
     * $params keys:
     *   - date_from          string   'Y-m-d'
     *   - date_to            string   'Y-m-d'
     *   - id_hotel           mixed    int|array|null
     *   - id_product         int|null  Filter by room type
     *   - datewise_breakdown bool     Returns per-day array when true
     *   - metric_type        string   'booked' (default) | 'full'
     *                                 'full' returns array with count_total,
     *                                 count_occupied, count_unavailable, count_available
     *   - use_cache          bool
     *
     * @param array $params
     * @return int|array
     */
    public static function getOccupancyStats(array $params)
    {
        $dateFrom   = pSQL($params['date_from']);
        $dateTo     = pSQL(isset($params['date_to']) ? $params['date_to'] : $params['date_from']);
        $idHotel    = isset($params['id_hotel'])    ? $params['id_hotel']    : null;
        $idProduct  = isset($params['id_product'])  ? (int) $params['id_product'] : 0;
        $datewise   = isset($params['datewise_breakdown']) ? (bool) $params['datewise_breakdown'] : false;
        $metricType = isset($params['metric_type']) ? $params['metric_type'] : 'booked';
        $useCache   = isset($params['use_cache'])   ? (bool) $params['use_cache'] : true;

        // Equalise same-day ranges (match AdminStatsController::getOccupancyData())
        if ($dateFrom == $dateTo) {
            $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateTo)));
        }

        $productFilter  = $idProduct ? ' AND hbd.`id_product` = '.$idProduct : '';
        $hotelFilterHbd = !is_null($idHotel)
            ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
            : '';

        // 'full' mode returns the occupancyData array (non-datewise only)
        if ($metricType === 'full') {
            return self::getFullOccupancyData($dateFrom, $dateTo, $idHotel);
        }

        if ($datewise) {
            $discreteDates = self::buildDiscreteDates($dateFrom, $dateTo);
            $result = array();
            foreach ($discreteDates as $d) {
                $cacheKey = 'HotelAnalyticsCore::getOccupancyStats_'
                    .(int) $d['timestamp_from'].'_'
                    .(is_array($idHotel) ? implode('_', $idHotel) : (int) $idHotel)
                    .'_'.$idProduct;
                if (!Cache::isStored($cacheKey) || !$useCache) {
                    $sql = 'SELECT COUNT(DISTINCT hbd.`id_room`)
                            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                            LEFT JOIN `'._DB_PREFIX_.'htl_room_information` hri
                                ON (hri.`id` = hbd.`id_room`)
                            LEFT JOIN `'._DB_PREFIX_.'product` p
                                ON (p.`id_product` = hri.`id_product`)
                            WHERE p.`active` = 1
                            AND hbd.`is_refunded` = 0
                            AND hbd.`date_from` < "'.pSQL($d['date_to']).' 00:00:00"
                            AND hbd.`date_to`   > "'.pSQL($d['date_from']).' 00:00:00"'
                            .$productFilter
                            .$hotelFilterHbd;
                    Cache::store($cacheKey, Db::getInstance()->getValue($sql));
                }
                $result[$d['timestamp_from']] = (int) Cache::retrieve($cacheKey);
            }
            return $result;
        }

        // Aggregate: COUNT distinct rooms across the full range
        $sql = 'SELECT COUNT(hbd.`id_room`)
                FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                WHERE hbd.`is_refunded` = 0
                AND hbd.`date_from` <= "'.pSQL($dateTo).' 00:00:00"
                AND hbd.`date_to`    > "'.pSQL($dateFrom).' 00:00:00"'
                .$productFilter
                .$hotelFilterHbd;

        return (int) Db::getInstance()->getValue($sql);
    }

    /**
     * Full occupancy breakdown: total / occupied / unavailable / available.
     * Internal helper called by getOccupancyStats() when metric_type='full'.
     *
     * Replicates: AdminStatsController::getOccupancyData()
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @param mixed  $idHotel
     * @return array  keys: count_total, count_occupied, count_unavailable, count_available
     */
    protected static function getFullOccupancyData($dateFrom, $dateTo, $idHotel)
    {
        $hotelFilterHbd = !is_null($idHotel)
            ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
            : '';
        $hotelFilterHbi = !is_null($idHotel)
            ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbi', 'id')
            : '';
        $hotelFilterHri = !is_null($idHotel)
            ? HotelBranchInformation::addHotelRestriction($idHotel, 'hri')
            : '';

        $data = array('count_total' => 0, 'count_occupied' => 0, 'count_available' => 0, 'count_unavailable' => 0);

        // Total rooms (active products)
        $data['count_total'] = (int) Db::getInstance()->getValue(
            'SELECT COUNT(hri.`id`)
             FROM `'._DB_PREFIX_.'htl_room_information` hri
             INNER JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
             WHERE p.`booking_product` = 1 AND p.`active` = 1'.$hotelFilterHri
        );

        // Occupied rooms (not refunded, overlapping date range)
        $occupiedRows = Db::getInstance()->executeS(
            'SELECT DISTINCT hbd.`id_room`
             FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
             LEFT JOIN `'._DB_PREFIX_.'htl_room_information` hri ON (hri.`id` = hbd.`id_room`)
             LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
             WHERE p.`active` = 1
             AND hbd.`is_refunded` = 0
             AND IF(hbd.`id_status` = '.(int) HotelBookingDetail::STATUS_CHECKED_OUT.',
                 hbd.`date_from` < DATE_FORMAT(hbd.`check_out`, "%Y-%m-%d")
                 AND hbd.`date_from` < "'.pSQL($dateTo).'"
                 AND DATE_FORMAT(hbd.`check_out`, "%Y-%m-%d") > "'.pSQL($dateFrom).'",
                 hbd.`date_from` < "'.pSQL($dateTo).'"
                 AND hbd.`date_to`   > "'.pSQL($dateFrom).'"
             )'.$hotelFilterHbd
        );

        $occupiedIds = $occupiedRows ? array_column($occupiedRows, 'id_room') : array();
        $data['count_occupied'] = count($occupiedIds);
        $notInClause = $occupiedIds
            ? ' AND hri.`id` NOT IN ('.implode(',', array_map('intval', $occupiedIds)).')'
            : '';

        // Permanently inactive rooms
        $countInactive = (int) Db::getInstance()->getValue(
            'SELECT COUNT(hri.`id`)
             FROM `'._DB_PREFIX_.'htl_room_information` hri
             LEFT JOIN `'._DB_PREFIX_.'htl_branch_info` hbi ON (hbi.`id` = hri.`id_hotel`)
             LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
             WHERE p.`active` = 1'
            .$notInClause
            .' AND hri.`id_status` = '.(int) HotelRoomInformation::STATUS_INACTIVE
            .$hotelFilterHbi
        );

        // Temporarily inactive (disable dates overlap query range)
        $countDisabled = (int) Db::getInstance()->getValue(
            'SELECT IFNULL(COUNT(hri.`id`), 0)
             FROM `'._DB_PREFIX_.'htl_room_information` hri
             LEFT JOIN `'._DB_PREFIX_.'htl_room_disable_dates` hrdd ON (hrdd.`id_room` = hri.`id`)
             LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = hri.`id_product`)
             WHERE hri.`id_status` = '.(int) HotelRoomInformation::STATUS_TEMPORARY_INACTIVE
            .$notInClause
            .' AND ("'.pSQL($dateTo).'" > hrdd.`date_from` AND "'.pSQL($dateFrom).'" < hrdd.`date_to`)
             AND p.`active` = 1'
            .$hotelFilterHri
        );

        $data['count_unavailable'] = $countInactive + $countDisabled;
        $data['count_available']   = $data['count_total'] - $data['count_occupied'] - $data['count_unavailable'];

        return $data;
    }

    // =========================================================================
    // SECTION 6: DERIVED KPIs
    // =========================================================================

    /**
     * Average Daily Rate = total room revenue / total booked room-nights.
     *
     * Replicates: AdminStatsController::getAverageDailyRate()
     *             AdminStatsController::getAverageDailyRateForDiscreteDates()
     *
     * $params keys:
     *   - date_from, date_to, id_hotel, datewise_breakdown
     *
     * @param array $params
     * @return float|array
     */
    public static function getAverageDailyRate(array $params)
    {
        $datewise = isset($params['datewise_breakdown']) && $params['datewise_breakdown'];

        $occupiedRooms  = self::getOccupancyStats(array_merge($params, array('datewise_breakdown' => $datewise)));
        $roomRevenue    = self::getRoomRevenue(array_merge($params, array('datewise_breakdown' => $datewise)));

        if ($datewise) {
            $result = array();
            foreach ($occupiedRooms as $ts => $rooms) {
                $result[$ts] = $rooms ? $roomRevenue[$ts] / $rooms : 0.0;
            }
            return $result;
        }

        return $occupiedRooms ? ($roomRevenue / $occupiedRooms) : 0.0;
    }

    /**
     * Revenue Per Available Room (RevPAR) = total room revenue / total rooms.
     *
     * Replicates: AdminStatsController::getRevenuePerAvailableRoom()
     *
     * $params keys:
     *   - date_from, date_to, id_hotel
     *
     * @param array $params
     * @return float
     */
    public static function getRevPAR(array $params)
    {
        $inventoryParams = array_merge($params, array('datewise_breakdown' => true));
        $revenueParams   = array_merge($params, array('datewise_breakdown' => true));

        $roomRevenue = self::getRoomRevenue($revenueParams);
        $totalRooms  = self::getRoomInventory($inventoryParams);

        $totalRevenue = array_sum($roomRevenue);
        $totalRoomCount = array_sum($totalRooms);

        return $totalRoomCount ? ($totalRevenue / $totalRoomCount) : 0.0;
    }

    /**
     * Total Revenue Per Available Room (TRevPAR) = total revenue (rooms + services) / total rooms.
     *
     * Replicates: AdminStatsController::getTotalRevenuePerAvailableRoom()
     *
     * $params keys:
     *   - date_from, date_to, id_hotel
     *
     * @param array $params
     * @return float
     */
    public static function getTRevPAR(array $params)
    {
        $discreteParams = array_merge($params, array('datewise_breakdown' => true));

        $totalRevenue = self::getTotalRevenue($discreteParams);
        $totalRooms   = self::getRoomInventory($discreteParams);

        return array_sum($totalRooms) ? (array_sum($totalRevenue) / array_sum($totalRooms)) : 0.0;
    }

    /**
     * Occupancy rate as a percentage: (booked rooms / total rooms) * 100.
     *
     * Replicates: AdminStatsController::getAverageOccupancyRate()
     *             AdminStatsController::getOccupancyRateForDiscreteDates()
     *
     * $params keys:
     *   - date_from, date_to, id_hotel, datewise_breakdown
     *
     * @param array $params
     * @return float|array  Values are fractions (0–1) when datewise_breakdown=true,
     *                      percentage (0–100) when false.
     */
    public static function getOccupancyRate(array $params)
    {
        $datewise = isset($params['datewise_breakdown']) && $params['datewise_breakdown'];

        if ($datewise) {
            $occupiedParams  = array_merge($params, array('datewise_breakdown' => true));
            $inventoryParams = array_merge($params, array('datewise_breakdown' => true));

            $occupiedRooms = self::getOccupancyStats($occupiedParams);
            $totalRooms    = self::getRoomInventory($inventoryParams);

            $result = array();
            foreach ($totalRooms as $ts => $total) {
                $result[$ts] = $total ? $occupiedRooms[$ts] / $total : 0.0;
            }
            return $result;
        }

        $discreteParams  = array_merge($params, array('datewise_breakdown' => true));
        $occupiedRooms   = self::getOccupancyStats($discreteParams);
        $totalRooms      = self::getRoomInventory($discreteParams);

        $totalOccupied   = array_sum($occupiedRooms);
        $totalRoomCount  = array_sum($totalRooms);

        return $totalRoomCount ? ($totalOccupied / $totalRoomCount) * 100 : 0.0;
    }

    // =========================================================================
    // SECTION 7: BOOKING LIFECYCLE METRICS
    // =========================================================================

    /**
     * Cancellation rate = (cancelled bookings / total bookings) * 100.
     *
     * Replicates: AdminStatsController::getCancellationRate()
     *
     * $params keys:
     *   - date_from   string  Filter by booking date_add
     *   - date_to     string
     *   - id_hotel    mixed
     *
     * @param array $params
     * @return float  Percentage 0–100
     */
    public static function getCancellationRate(array $params)
    {
        $dateFrom = pSQL($params['date_from']);
        $dateTo   = pSQL(isset($params['date_to']) ? $params['date_to'] : $params['date_from']);
        $idHotel  = isset($params['id_hotel']) ? $params['id_hotel'] : false;

        $hotelFilter = $idHotel !== false
            ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
            : '';

        $total = (int) Db::getInstance()->getValue(
            'SELECT COUNT(hbd.`id`)
             FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
             WHERE hbd.`date_add` BETWEEN "'.$dateFrom.' 00:00:00" AND "'.$dateTo.' 23:59:59"'
            .$hotelFilter
        );

        $cancelled = (int) Db::getInstance()->getValue(
            'SELECT COUNT(hbd.`id`)
             FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
             WHERE hbd.`is_refunded` = 1
             AND hbd.`date_add` BETWEEN "'.$dateFrom.' 00:00:00" AND "'.$dateTo.' 23:59:59"'
            .$hotelFilter
        );

        return $total ? (($cancelled / $total) * 100) : 0.0;
    }

    /**
     * Total nights stayed across all bookings overlapping the date range.
     *
     * Replicates: AdminStatsController::getNightsStayed()
     *
     * $params keys:
     *   - date_from, date_to, id_hotel
     *
     * @param array $params
     * @return int
     */
    public static function getNightsStayed(array $params)
    {
        $dateFrom = date('Y-m-d H:i:s', strtotime($params['date_from']));
        $dateTo   = date('Y-m-d H:i:s', strtotime(isset($params['date_to']) ? $params['date_to'] : $params['date_from']));
        $idHotel  = isset($params['id_hotel']) ? $params['id_hotel'] : false;

        $hotelFilter = $idHotel !== false
            ? HotelBranchInformation::addHotelRestriction($idHotel, 'hbd')
            : '';

        $checkedOut = (int) HotelBookingDetail::STATUS_CHECKED_OUT;

        return (int) Db::getInstance()->getValue(
            'SELECT IFNULL(SUM(DATEDIFF(
                IF(hbd.`id_status` = '.$checkedOut.',
                    IF("'.$dateTo.'" > check_out, check_out, "'.$dateTo.'"),
                    IF("'.$dateTo.'" > date_to,   date_to,   "'.$dateTo.'")),
                IF(hbd.`id_status` = '.$checkedOut.',
                    IF("'.$dateFrom.'" < check_in,  check_in,  "'.$dateFrom.'"),
                    IF("'.$dateFrom.'" < date_from,  date_from, "'.$dateFrom.'"))
            )), 0)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            WHERE hbd.`is_refunded` = 0
            AND hbd.`is_back_order` = 0
            AND (IF(hbd.`id_status` = '.$checkedOut.',
                (hbd.`check_in`  < \''.pSQL($dateTo).'\' AND hbd.`check_out` >= \''.pSQL($dateFrom).'\'),
                (hbd.`date_from` < \''.pSQL($dateTo).'\' AND hbd.`date_to`   >= \''.pSQL($dateFrom).'\')
            ))'.$hotelFilter
        );
    }

    /**
     * Average Length of Stay = total room-nights / total distinct bookings.
     *
     * Replicates: AdminStatsController::getAverageLengthOfStay()
     *
     * $params keys:
     *   - date_from, date_to, id_hotel
     *
     * @param array $params
     * @return float
     */
    public static function getAverageLengthOfStay(array $params)
    {
        $discreteParams = array_merge($params, array('datewise_breakdown' => true));
        $nightsByDate   = self::getOccupancyStats($discreteParams);
        $totalNights    = array_sum($nightsByDate);

        $totalBookings  = self::getOccupancyStats(array_merge($params, array('datewise_breakdown' => false)));

        return $totalBookings ? ($totalNights / $totalBookings) : 0.0;
    }

    // =========================================================================
    // SECTION 8: PRIVATE UTILITIES
    // =========================================================================

    /**
     * Builds an array of consecutive single-day intervals between $dateFrom and $dateTo.
     * Each entry: ['date_from', 'date_to', 'timestamp_from']
     * Matches the while-loop pattern in AdminStatsController discrete-date functions.
     *
     * @param string $dateFrom  'Y-m-d'
     * @param string $dateTo    'Y-m-d' (inclusive last day of range)
     * @return array
     */
    protected static function buildDiscreteDates($dateFrom, $dateTo)
    {
        $dates    = array();
        $current  = $dateFrom;
        while ($current <= $dateTo) {
            $next = date('Y-m-d', strtotime('+1 day', strtotime($current)));
            $dates[] = array(
                'date_from'      => $current,
                'date_to'        => $next,
                'timestamp_from' => strtotime($current),
            );
            $current = $next;
        }
        return $dates;
    }
}
