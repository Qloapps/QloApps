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

class QghcAri
{
    // Returns full ARI for a property — one flat ari_info entry per date segment.
    public function getAriByIdProperty($params)
    {
        $finalPayload = array();

        if ($dateWiseHotelAri = $this->getHotelAri($params)) {
            foreach ($dateWiseHotelAri as $idHotel => $productWiseAri) {
                if (!isset($finalPayload[$idHotel])) {
                    $finalPayload[$idHotel] = array(
                        'id_property'     => '',
                        'id_pms_property' => (string)$idHotel,
                        'ari_info'        => array(),
                    );
                }
                foreach ($productWiseAri as $idProduct => $dayByDayAri) {
                    if (empty($dayByDayAri)) {
                        continue;
                    }
                    foreach ($this->buildFlatAriSegments($idProduct, $dayByDayAri) as $segment) {
                        $finalPayload[$idHotel]['ari_info'][] = $segment;
                    }
                }
            }
        }

        return json_encode(array(
            'success' => true,
            'data'    => array_values($finalPayload),
        ));
    }

    // Returns ARI only for changed dates — same flat segment format as getAriByIdProperty.
    // Deletes fetched rows after building the response (race-safe via ID capture).
    public function getAriChange($params)
    {
        $idProperty = (int)($params['id_property'] ?? 0);
        if (!$idProperty) {
            return json_encode(array('success' => false, 'error' => 'id_property is required'));
        }

        $objAriUpdates = new QghcAriUpdates();
        $changedRows   = $objAriUpdates->getChangedRows($idProperty);

        if (empty($changedRows)) {
            return json_encode(array('success' => true, 'data' => array()));
        }

        $changedIds             = array();
        $changedDatesByRoomType = array();

        foreach ($changedRows as $row) {
            $changedIds[]  = (int)$row['id_qghc_ari_updates'];
            $idRoomType    = (int)$row['id_room_type'];
            $date          = date('Y-m-d', strtotime($row['ari_date']));
            $changedDatesByRoomType[$idRoomType][$date] = true;
        }

        $collectedAri = array();

        foreach ($changedDatesByRoomType as $idRoomType => $changedDates) {
            ksort($changedDates);
            $ranges = $this->buildConsecutiveDateRanges(array_keys($changedDates));

            foreach ($ranges as $range) {
                $ariParams = array(
                    'id_property'  => $idProperty,
                    'id_room_type' => (string)$idRoomType,
                    'date_from'    => $range['from'],
                    'date_to'      => date('Y-m-d', strtotime($range['to'] . ' +1 day')),
                );

                $ariData = $this->getHotelAri($ariParams);

                if (!empty($ariData[$idProperty][$idRoomType])) {
                    if (!isset($collectedAri[$idProperty][$idRoomType])) {
                        $collectedAri[$idProperty][$idRoomType] = array();
                    }
                    foreach ($ariData[$idProperty][$idRoomType] as $dayEntry) {
                        if (isset($changedDates[$dayEntry['date_from']])) {
                            $collectedAri[$idProperty][$idRoomType][] = $dayEntry;
                        }
                    }
                }
            }
        }

        $finalPayload = array();

        foreach ($collectedAri as $idHotel => $productWiseAri) {
            if (!isset($finalPayload[$idHotel])) {
                $finalPayload[$idHotel] = array(
                    'id_property'     => '',
                    'id_pms_property' => (string)$idHotel,
                    'ari_info'        => array(),
                );
            }
            foreach ($productWiseAri as $idProduct => $dayByDayAri) {
                if (empty($dayByDayAri)) {
                    continue;
                }
                foreach ($this->buildFlatAriSegments($idProduct, $dayByDayAri) as $segment) {
                    $finalPayload[$idHotel]['ari_info'][] = $segment;
                }
            }
        }

        // Delete only the rows we originally fetched — new changed rows written during
        // processing are preserved for the next call.
        $objAriUpdates->deleteChangedRowsByIds($changedIds);

        return json_encode(array(
            'success' => true,
            'data'    => array_values($finalPayload),
        ));
    }

    // Per-date ARI calculation — restricted to room types configured for QGHC.
    public function getHotelAri($params)
    {
        $idProperty = (int)($params['id_property'] ?? 0);
        if (!$idProperty) {
            return array();
        }

        $minimumBookingOffset  = HotelOrderRestrictDate::getMinimumBookingOffset($idProperty);
        $maximumCheckoutOffset = HotelOrderRestrictDate::getMaximumCheckoutOffset($idProperty);

        // Room types connected to QGHC for this property — the only ones ever allowed out
        // to Google, regardless of what's requested. Changed rows in qghc_ari_updates
        // are written by hotel-wide events (e.g. booking-offset changes) that don't check QGHC
        // connection, so only_change=1 must re-filter here too, not just the full-sync path.
        $connectedProducts = QghcRoomType::getConnectedProductIds($idProperty);

        if (!empty($params['id_room_type'])) {
            $requested  = array_filter(array_map('intval', explode('|', $params['id_room_type'])));
            $idProducts = array_values(array_intersect($requested, $connectedProducts));
        } else {
            $idProducts = $connectedProducts;
        }

        if (empty($idProducts)) {
            return array();
        }

        // Hotel's own resolved max booking offset (hotel-specific override if set,
        // otherwise the global default — see HotelOrderRestrictDate::getMaxOrderDate()).
        $maxOrderDate = date('Y-m-d', strtotime(HotelOrderRestrictDate::getMaxOrderDate($idProperty)));

        $dateFrom = !empty($params['date_from'])
            ? $params['date_from']
            : date('Y-m-d');
        $dateTo   = !empty($params['date_to'])
            ? $params['date_to']
            : $maxOrderDate;

        // The resolved offset is a hard ceiling, not just a default: dates beyond it
        // aren't bookable at this hotel regardless of what the caller explicitly
        // requests, so never return ARI past it even for an explicit wider date_to.
        if (strtotime($dateTo) > strtotime($maxOrderDate)) {
            $dateTo = $maxOrderDate;
        }

        $bookingParams = array(
            'date_from'       => date('Y-m-d 00:00:00', strtotime($dateFrom)),
            'date_to'         => date('Y-m-d 23:59:59', strtotime($dateTo)),
            'hotel_id'        => $idProperty,
            'search_available' => 0,
            'search_booked'   => 1,
            'search_unavai'   => 1,
            'search_partial'  => 0,
            'search_cart_rms' => 0,
        );

        $objBookingDtl          = new HotelBookingDetail();
        $searchAriData          = array();
        $productsLosRestriction = $this->getDateWiseRestrictionOfProducts($idProducts, $dateFrom, $dateTo);

        foreach ($idProducts as $idProduct) {
            $bookingParams['id_room_type'] = $idProduct;

            if (!isset($searchAriData[$idProperty])) {
                $searchAriData[$idProperty] = array();
            }
            if (!isset($searchAriData[$idProperty][$idProduct])) {
                $searchAriData[$idProperty][$idProduct] = array();
            }

            $bookingData = $objBookingDtl->getBookingData($bookingParams);
            $rooms       = HotelRoomInformation::getHotelRoomsInfo($idProperty, $idProduct);
            $fallbackLos = array();

            for (
                $currentDate = $dateFrom;
                $currentDate < $dateTo;
                $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)))
            ) {
                $nextDate    = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));
                $ariDateInfo = array(
                    'date_from' => $currentDate,
                    'date_to'   => $nextDate,
                );

                $totalBookingPrice = QghcPropertyService::getRoomTypeFinalPriceForSdm(
                    $idProduct,
                    $currentDate,
                    date('Y-m-d H:i:s', strtotime('+1 day', strtotime($currentDate))),
                    1
                );

                // LOS restrictions
                if (
                    isset($productsLosRestriction[$idProduct])
                    && isset($productsLosRestriction[$idProduct][$currentDate])
                    && $productsLosRestriction[$idProduct][$currentDate]
                ) {
                    $ariDateInfo['min_los'] = $productsLosRestriction[$idProduct][$currentDate]['min_los'] ?? 1;
                    $ariDateInfo['max_los'] = $productsLosRestriction[$idProduct][$currentDate]['max_los'] ?? 30;
                } else {
                    if (!isset($fallbackLos[$idProduct])) {
                        $losRow = Db::getInstance()->getRow(
                            'SELECT `min_los`, `max_los` FROM `' . _DB_PREFIX_ . 'htl_room_type`
                             WHERE `id_product` = ' . (int)$idProduct
                        );
                        $fallbackLos[$idProduct] = $losRow
                            ? array('min_los' => (int)$losRow['min_los'], 'max_los' => (int)$losRow['max_los'])
                            : array('min_los' => 1, 'max_los' => 0);
                    }
                    $ariDateInfo['min_los'] = $fallbackLos[$idProduct]['min_los'];
                    $ariDateInfo['max_los'] = $fallbackLos[$idProduct]['max_los'];
                }

                if ($totalBookingPrice && is_array($totalBookingPrice) && isset($totalBookingPrice['total_price_tax_incl'])) {
                    $ariDateInfo['total_price_with_tax'] = (float)$totalBookingPrice['total_price_tax_incl'];
                    $ariDateInfo['total_price_tax_excl'] = (float)$totalBookingPrice['total_price_tax_excl'];
                    $ariDateInfo['tax']                  = (float)$totalBookingPrice['total_price_tax_incl'] - (float)$totalBookingPrice['total_price_tax_excl'];
                } else {
                    $ariDateInfo['total_price_with_tax'] = 0.0;
                    $ariDateInfo['total_price_tax_excl'] = 0.0;
                    $ariDateInfo['tax']                  = 0.0;
                }
                $ariDateInfo['minimum_booking_offset'] = $minimumBookingOffset;
                $ariDateInfo['maximum_booking_offset'] = $maximumCheckoutOffset;

                if (!empty($bookingData['rm_data'])) {
                    foreach ($bookingData['rm_data'] as $roomType) {
                        $ariDateInfo['total_available_rooms'] = $roomType['numberOfRooms'];

                        // Booked rooms
                        $bookedRoomIds = array();
                        if (isset($roomType['data']['booked'])) {
                            foreach ($roomType['data']['booked'] as $bookedRow) {
                                if (!empty($bookedRow['detail'])) {
                                    foreach ($bookedRow['detail'] as $bookDetail) {
                                        if (
                                            $currentDate >= date('Y-m-d', strtotime($bookDetail['date_from']))
                                            && $currentDate < date('Y-m-d', strtotime($bookDetail['date_to']))
                                        ) {
                                            $bookedRoomIds[] = $bookedRow['id_room'];
                                        }
                                    }
                                }
                            }
                        }
                        $bookedRooms = array_values(array_filter($rooms, function($room) use ($bookedRoomIds) {
                            return in_array($room['id'], $bookedRoomIds);
                        }));
                        $ariDateInfo['total_available_rooms'] -= count($bookedRooms);

                        // Unavailable rooms
                        $unavailableRoomIds = array();
                        if (isset($roomType['data']['unavailable'])) {
                            foreach ($roomType['data']['unavailable'] as $unavailRow) {
                                if (!empty($unavailRow['detail'])) {
                                    foreach ($unavailRow['detail'] as $unavailDetail) {
                                        // Skip LOS-restriction mismatch (status 4) — it is a booking
                                        // search filter, not a physical room block. Counting it would
                                        // zero-out availability whenever the ARI query range is wider
                                        // than the room type's max_los (e.g. full-year ARI sync).
                                        if (HotelRoomInformation::STATUS_SEARCH_LOS_UNSATISFIED === (int)$unavailDetail['id_status']) {
                                            continue;
                                        }
                                        if (!$unavailDetail['date_from'] || !$unavailDetail['date_to']) {
                                            $unavailableRoomIds[] = $unavailRow['id_room'];
                                        } elseif (
                                            $currentDate >= date('Y-m-d', strtotime($unavailDetail['date_from']))
                                            && $currentDate < date('Y-m-d', strtotime($unavailDetail['date_to']))
                                        ) {
                                            $unavailableRoomIds[] = $unavailRow['id_room'];
                                        }
                                    }
                                }
                            }
                        }
                        $unavailableRooms = array_values(array_filter($rooms, function($room) use ($unavailableRoomIds) {
                            return in_array($room['id'], $unavailableRoomIds);
                        }));
                        $ariDateInfo['total_available_rooms'] -= count($unavailableRooms);
                        $ariDateInfo['total_available_rooms']  = max(0, $ariDateInfo['total_available_rooms']);
                    }
                } else {
                    $ariDateInfo['total_available_rooms'] = 0;
                }

                $searchAriData[$idProperty][$idProduct][] = $ariDateInfo;
            }
        }

        return $searchAriData;
    }

    public function getDateWiseRestrictionOfProducts($idProducts, $dateFrom, $dateTo)
    {
        $losArr = array();

        foreach ($idProducts as $idProduct) {
            if (!isset($losArr[$idProduct])) {
                $losArr[$idProduct] = array();
            }

            // Load room type by id_product (NOT by primary key `id` — they differ).
            $roomTypeRow = Db::getInstance()->getRow(
                'SELECT `min_los`, `max_los` FROM `' . _DB_PREFIX_ . 'htl_room_type`
                 WHERE `id_product` = ' . (int)$idProduct
            );
            if ($roomTypeRow) {
                $minLosDefault = (int)$roomTypeRow['min_los'];
                $maxLosDefault = (int)$roomTypeRow['max_los'];

                $objRestrRange = new HotelRoomTypeRestrictionDateRange();
                $lengthStayArr = $objRestrRange->getRoomTypeLengthOfStayRestriction($idProduct);

                // Pre-index restrictions by date for O(1) lookup
                $restrictionByDate = array();
                foreach ($lengthStayArr as $restriction) {
                    for (
                        $restrDate = $restriction['date_from'];
                        $restrDate <= $restriction['date_to'];
                        $restrDate = date('Y-m-d', strtotime('+1 day', strtotime($restrDate)))
                    ) {
                        if (!isset($restrictionByDate[$restrDate])) {
                            $restrictionByDate[$restrDate] = array(
                                'min_los' => isset($restriction['min_los']) && $restriction['min_los'] !== ''
                                    ? (int)$restriction['min_los'] : $minLosDefault,
                                'max_los' => isset($restriction['max_los']) && $restriction['max_los'] !== ''
                                    ? (int)$restriction['max_los'] : $maxLosDefault,
                            );
                        }
                    }
                }

                for (
                    $currentDate = $dateFrom;
                    $currentDate < $dateTo;
                    $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)))
                ) {
                    $losArr[$idProduct][$currentDate] = isset($restrictionByDate[$currentDate])
                        ? $restrictionByDate[$currentDate]
                        : array('min_los' => $minLosDefault, 'max_los' => $maxLosDefault);
                }
            }
        }

        return $losArr;
    }

    // Splits day-by-day ARI for one room type into flat ari_info segments.
    // A new segment starts when availability, price, LOS, or booking offset changes.
    // Returns an array of complete ari_info entries ready to append to the payload.
    private function buildFlatAriSegments($idProduct, $dayByDayAri)
    {
        usort($dayByDayAri, function($a, $b) {
            return strcmp($a['date_from'], $b['date_from']);
        });

        $segments = array();
        $cur      = null;

        foreach ($dayByDayAri as $day) {
            if ($cur === null) {
                $cur = $this->openSegment($idProduct, $day);
            } elseif (
                $cur['_to']          === $day['date_from']
                && $cur['availability']  === (int)$day['total_available_rooms']
                && $cur['_price']        == (float)$day['total_price_with_tax']
                && $cur['_min_los']      === (int)$day['min_los']
                && $cur['_max_los']      === (int)$day['max_los']
                && $cur['_min_off']      === (int)$day['minimum_booking_offset']
                && $cur['_max_off']      === (int)$day['maximum_booking_offset']
            ) {
                $cur['date_to'] = $day['date_to'];
                $cur['_to']     = $day['date_to'];
            } else {
                $segments[] = $this->closeSegment($cur);
                $cur = $this->openSegment($idProduct, $day);
            }
        }

        if ($cur !== null) {
            $segments[] = $this->closeSegment($cur);
        }

        return $segments;
    }

    // Opens a new segment starting at $day, storing merge-state fields prefixed with _.
    private function openSegment($idProduct, $day)
    {
        return array(
            'id_room_type'     => '',
            'id_pms_room_type' => (string)$idProduct,
            'date_from'        => $day['date_from'],
            'date_to'          => $day['date_to'],
            'weekdays'         => array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'),
            'availability'     => (int)$day['total_available_rooms'],
            'closed'           => 0,
            'rates'            => array($this->buildRateObject($day)),
            // Internal merge-state fields — stripped before output
            '_to'      => $day['date_to'],
            '_price'   => (float)$day['total_price_with_tax'],
            '_min_los' => (int)$day['min_los'],
            '_max_los' => (int)$day['max_los'],
            '_min_off' => (int)$day['minimum_booking_offset'],
            '_max_off' => (int)$day['maximum_booking_offset'],
        );
    }

    // Strips internal merge-state fields and returns the finished ari_info entry.
    private function closeSegment(array $seg)
    {
        unset($seg['_to'], $seg['_price'],
              $seg['_min_los'], $seg['_max_los'],
              $seg['_min_off'], $seg['_max_off']);
        return $seg;
    }

    // Builds a single rate object for a day.
    // occupancy_based_price / occupancy_based_price_with_tax are empty objects
    // (populated when the occupancy pricing module is installed).
    private function buildRateObject($day)
    {
        return array(
            'id_rate_plan'                   => '0',
            'id_pms_rate_plan'               => '0',
            'price'                          => (float)$day['total_price_with_tax'],
            'price_with_tax'                 => (float)$day['total_price_with_tax'],
            'price_without_tax'              => (float)$day['total_price_tax_excl'],
            'occupancy_based_price'          => new stdClass(),
            'occupancy_based_price_with_tax' => new stdClass(),
            'min_los_arrival'                => 0,
            'max_los_arrival'                => 0,
            'min_los_stay'                   => (int)$day['min_los'],
            'max_los_stay'                   => (int)$day['max_los'],
            'stop_sell'                      => ((int)$day['total_available_rooms'] <= 0) ? 1 : 0,
            'close_to_arrival'               => 0,
            'close_to_departure'             => 0,
            'min_advanced_booking_offset'    => (int)$day['minimum_booking_offset'],
            'max_advanced_booking_offset'    => (int)$day['maximum_booking_offset'],
        );
    }

    // Groups a sorted list of date strings into consecutive ranges.
    private function buildConsecutiveDateRanges($dates)
    {
        $ranges = array();
        if (empty($dates)) {
            return $ranges;
        }

        $start = $dates[0];
        $prev  = $dates[0];

        for ($i = 1; $i < count($dates); $i++) {
            $expectedNext = date('Y-m-d', strtotime($prev . ' +1 day'));
            if ($dates[$i] === $expectedNext) {
                $prev = $dates[$i];
            } else {
                $ranges[] = array('from' => $start, 'to' => $prev);
                $start = $dates[$i];
                $prev  = $dates[$i];
            }
        }
        $ranges[] = array('from' => $start, 'to' => $prev);

        return $ranges;
    }
}
