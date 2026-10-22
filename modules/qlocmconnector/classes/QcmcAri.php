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

class QcmcAri extends QmkWebService
{
    //function called by cron to process the queue data pending for processing, push ari hotel wise in foreach
    public function processAriUpdateData()
    {
        $objAriUpdates = new QcmcAriUpdates();
        $objChannelManagerApiService = new QcmcChannelManagerApiService();
        $objConnectorModule = Module::getInstanceByName('qlocmconnector');

        $pendingRowsToProcess = $objAriUpdates->getPendingAriUpdateRows();

        if ($pendingRowsToProcess) {
            $flatAriUpdateDataPayload = $this->buildAriUpdatePayload($pendingRowsToProcess);

            foreach ($flatAriUpdateDataPayload as $hotelPayload) {

                $hotelId = (int)$hotelPayload['id_pms_property'];
                $rowsForHotel = array_filter($pendingRowsToProcess, function ($row) use ($hotelId) {
                    return (int)$row['id_hotel'] === $hotelId;
                });

                if (empty($rowsForHotel)) {
                    continue;
                }

                $rowIdsForHotel = array_column($rowsForHotel, 'id_ari_updates');

                $objAriUpdates->markProcessingAriUpdateData($rowIdsForHotel);

                $result = $objChannelManagerApiService->pushARI($hotelPayload);

                if (isset($result['success']) && $result['success']) {
                    $objAriUpdates->deleteAriUpdateDataByIds($rowIdsForHotel);
                } else {
                    $objAriUpdates->resetAriUpdateDataToPending($rowIdsForHotel);
                    if (isset($result['http_code']) && $result['http_code']==401) {

                        return false;
                    }
                }
            }
        }

        return true;
    }

    //function to build the payload as per pms standard and calculate availability on pms side
    private function buildAriUpdatePayload($flatAriUpdateRows)
    {
        $flatAriUpdatePayloadDateWise = [];

        //build flat items without calling getAvailableRoomsByDate
        foreach ($flatAriUpdateRows as $flatAriUpdateRow) {
            if (empty($flatAriUpdateRow['ari_data'])) {
                continue;
            }

            $flatAriUpdateRowData = json_decode($flatAriUpdateRow['ari_data'], true);

            if (!is_array($flatAriUpdateRowData)) {
                continue;
            }

            $flatAriUpdateItem = [
                'id_pms_property' => (int)$flatAriUpdateRow['id_hotel'],
                'id_pms_room_type' => (int)$flatAriUpdateRow['id_room_type'],
                'date' => date('Y-m-d', strtotime($flatAriUpdateRow['date'])),
            ];

            //this is just a marker to check later that availabilty must be added
            if (array_key_exists('inventory', $flatAriUpdateRowData)) {
                $flatAriUpdateItem['need_availability'] = true;
            }

            if (array_key_exists('min_booking_offset', $flatAriUpdateRowData)) {
                $flatAriUpdateItem['min_booking_offset'] = $flatAriUpdateRowData['min_booking_offset'];
            } else {
                // Get default min_booking_offset from hotel settings
                $flatAriUpdateItem['min_booking_offset'] = HotelOrderRestrictDate::getMinimumBookingOffset(
                    (int)$flatAriUpdateRow['id_hotel']
                );
            }

            if (array_key_exists('max_booking_offset', $flatAriUpdateRowData)) {
                $flatAriUpdateItem['max_booking_offset'] = $flatAriUpdateRowData['max_booking_offset'];
            } else {
                // Get default max_booking_offset from hotel settings
                $flatAriUpdateItem['max_booking_offset'] = HotelOrderRestrictDate::getMaximumCheckoutOffset(
                    (int)$flatAriUpdateRow['id_hotel']
                );
            }

            // Get LOS (length of stay) restrictions
            $flatAriUpdateItem['los'] = $this->getLosRestrictionForDate(
                (int)$flatAriUpdateRow['id_room_type'],
                $flatAriUpdateRow['date']
            );

            $flatAriUpdatePayloadDateWise[] = $flatAriUpdateItem;
        }

        if (empty($flatAriUpdatePayloadDateWise)) {
            return [];
        }

        //calculate per-hotel min/max date so we can fetch booking data in one call per hotel
        $hotelDateRanges = [];
        foreach ($flatAriUpdatePayloadDateWise as &$datewiseAriUpdateData) {
            $idHotel = $datewiseAriUpdateData['id_pms_property'];
            $idProduct = $datewiseAriUpdateData['id_pms_room_type'];
            $date = $datewiseAriUpdateData['date'];
            if (!isset($hotelDateRanges[$idHotel])) {
                $hotelDateRanges[$idHotel] = ['min' => $date, 'max' => $date];
            } else {
                if ($date < $hotelDateRanges[$idHotel]['min']) {
                    $hotelDateRanges[$idHotel]['min'] = $date;
                }
                if ($date > $hotelDateRanges[$idHotel]['max']) {
                    $hotelDateRanges[$idHotel]['max'] = $date;
                }
            }

            $totalBookingPrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                $idProduct,
                $datewiseAriUpdateData['date'],
                date('Y-m-d H:i:s', strtotime('+1 day', strtotime($datewiseAriUpdateData['date']))),
                1
            );

            $datewiseAriUpdateData['date_from'] = $datewiseAriUpdateData['date'];
            $datewiseAriUpdateData['date_to'] = $datewiseAriUpdateData['date'];
            $datewiseAriUpdateData['price_with_tax'] = $totalBookingPrice['total_price_tax_incl'];
            $datewiseAriUpdateData['price_without_tax'] = $totalBookingPrice['total_price_tax_excl'];
            $datewiseAriUpdateData['tax'] = $totalBookingPrice['total_price_tax_incl'] - $totalBookingPrice['total_price_tax_excl'];

            unset($datewiseAriUpdateData['date']);
        }
        unset($datewiseAriUpdateData);

        //For each hotel call getBookingData once and build an avialability map
        $availabilityMap = [];

        foreach ($hotelDateRanges as $hotelId => $dateRange) {

            $startDate = strtotime($dateRange['min']);
            $endDate = strtotime($dateRange['max']);

            // call booking data only one time per hotel
            $bookingParams = [
                'date_from' => date('Y-m-d 00:00:00', $startDate),
                'date_to' => date('Y-m-d 23:59:59', $endDate),
                'hotel_id' => $hotelId,
                'search_available' => 1,
                'search_booked' => 1,
                'search_unavai' => 1,
                'search_partial' => 0,
                'search_cart_rms' => 0,
                'only_search_data' => 1,
            ];

            $objBookingDtl = new HotelBookingDetail();
            $bookingData = $objBookingDtl->getBookingData($bookingParams);

            if (!isset($availabilityMap[$hotelId])) {
                $availabilityMap[$hotelId] = [];
            }

            if (!empty($bookingData['rm_data'])) {
                foreach ($bookingData['rm_data'] as $roomTypeId => $roomTypeData) {

                    if (!isset($availabilityMap[$hotelId][$roomTypeId])) {
                        $availabilityMap[$hotelId][$roomTypeId] = [];
                    }

                    // number of units for this room-type
                    $numRooms = (int)($roomTypeData['numberOfRooms'] ?? 0);

                    $unAvailableRooms = [];
                    if (!empty($roomTypeData['data']['booked'])) {
                        foreach ($roomTypeData['data']['booked'] as $bookedRow) {
                            if (!empty($bookedRow['detail'])) {
                                foreach ($bookedRow['detail'] as $bookedDetail) {
                                    $unAvailableRooms[] = [
                                        'date_from' => strtotime($bookedDetail['date_from']),
                                        'date_to' => strtotime($bookedDetail['date_to']),
                                    ];
                                }
                            }
                        }
                    }

                    if (count($roomTypeData['data']['unavailable'])) {
                        foreach ($roomTypeData['data']['unavailable'] as $unavailableRooms) {
                            foreach ($unavailableRooms['detail'] as $unavailableDetail) {
                                if ($unavailableDetail['id_status'] == HotelRoomInformation::STATUS_INACTIVE
                                ) {
                                    $unAvailableRooms[] = [
                                        'date_from' => $startDate,
                                        'date_to' => $endDate,
                                    ];
                                } else if ($unavailableDetail['id_status'] == HotelRoomInformation::STATUS_TEMPORARY_INACTIVE
                                    && $unavailableDetail['date_from'] && $unavailableDetail['date_to']
                                ) {
                                    $unAvailableRooms[] = [
                                        'date_from' => strtotime($unavailableDetail['date_from']),
                                        'date_to' => strtotime($unavailableDetail['date_to']),
                                    ];
                                }
                            }
                        }
                    }

                    for ($date = $startDate; $date <= $endDate; $date = strtotime('+1 day', $date)) {
                        $dateStr = date('Y-m-d', $date);

                        $unAvailableRoomCount = 0;
                        if (!empty($unAvailableRooms)) {
                            foreach ($unAvailableRooms as $unAvailableRoom) {
                                if ($date >= $unAvailableRoom['date_from'] && $date < $unAvailableRoom['date_to']) {
                                    $unAvailableRoomCount++;
                                }
                            }
                        }

                        $available = max(0, $numRooms - $unAvailableRoomCount);
                        $availabilityMap[$hotelId][$roomTypeId][$dateStr] = $available;
                    }
                }
            }
        }

        foreach ($flatAriUpdatePayloadDateWise as &$payloadItem) {
            if (!empty($payloadItem['need_availability'])) {
                $available = $availabilityMap[$payloadItem['id_pms_property']][$payloadItem['id_pms_room_type']][$payloadItem['date_to']] ?? 0;
                $payloadItem['availability'] = (int)$available;

                unset($payloadItem['need_availability']);
            }
        }

        unset($payloadItem);

        //sort and merge consecutive dates
        usort($flatAriUpdatePayloadDateWise, function($firstCmp, $secondCmp) {
            if ($firstCmp['id_pms_property'] !== $secondCmp['id_pms_property']) {
                return $firstCmp['id_pms_property'] <=> $secondCmp['id_pms_property'];
            }
            if ($firstCmp['id_pms_room_type'] !== $secondCmp['id_pms_room_type']) {
                return $firstCmp['id_pms_room_type'] <=> $secondCmp['id_pms_room_type'];
            }

            return strcmp($firstCmp['date_to'], $secondCmp['date_to']);
        });

        $mergedAriUpdatePayload = [];
        $currentData = [];

        foreach ($flatAriUpdatePayloadDateWise as $mergeFlatAriUpdateItem) {
            if(empty($currentData)) {
                $currentData = $mergeFlatAriUpdateItem;
            } else {
                if (
                    (strtotime($currentData['date_to'] . ' +1 day') == strtotime($mergeFlatAriUpdateItem['date_from']))
                    && $currentData['id_pms_property'] == $mergeFlatAriUpdateItem['id_pms_property']
                    && $currentData['id_pms_room_type'] == $mergeFlatAriUpdateItem['id_pms_room_type']
                    && $currentData['price_with_tax'] == $mergeFlatAriUpdateItem['price_with_tax']
                    && $currentData['min_booking_offset'] == $mergeFlatAriUpdateItem['min_booking_offset']
                    && $currentData['max_booking_offset'] == $mergeFlatAriUpdateItem['max_booking_offset']
                    && $currentData['availability'] == $mergeFlatAriUpdateItem['availability']
                    && (
                        isset($currentData['los']) && isset($mergeFlatAriUpdateItem['los'])
                        && $currentData['los']['min_los'] == $mergeFlatAriUpdateItem['los']['min_los']
                        && $currentData['los']['max_los'] == $mergeFlatAriUpdateItem['los']['max_los']
                    ) && $currentData['price_without_tax'] == $mergeFlatAriUpdateItem['price_without_tax']
                    && $currentData['tax'] == $mergeFlatAriUpdateItem['tax']
                ) {
                    $currentData['date_to'] = $mergeFlatAriUpdateItem['date_to'];
                } else {
                    $mergedAriUpdatePayload[] = $currentData;
                    $currentData = $mergeFlatAriUpdateItem;
                }
            }
        }

        if ($currentData) {
            $mergedAriUpdatePayload[] = $currentData;
        }

        $finalAriUpdatePayload = [];

        foreach ($mergedAriUpdatePayload as $row) {
            if (!isset($finalAriUpdatePayload[$row['id_pms_property']])) {
                $finalAriUpdatePayload[$row['id_pms_property']] = [
                    'id_property' => '',
                    'id_pms_property' => (string)$row['id_pms_property'],
                    'ari_info' => []
                ];
            }

            // Use LOS restrictions from merged data or get from database
            $losRestriction = $row['los'] ?? $this->getLosRestrictionForDate(
                (int)$row['id_pms_room_type'],
                $row['date_from']
            );

            // Get booking offset from merged data or use defaults
            $minBookingOffset = $row['min_booking_offset'] ?? HotelOrderRestrictDate::getMinimumBookingOffset((int)$row['id_pms_property']);
            $maxBookingOffset = $row['max_booking_offset'] ?? HotelOrderRestrictDate::getMaximumCheckoutOffset((int)$row['id_pms_property']);

            $ariInfo = [
                'id_room_type' => '',
                'id_pms_room_type' => (string)$row['id_pms_room_type'],
                'date_from' => $row['date_from'],
                'date_to' => $row['date_to'],
                'weekdays' => ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'],
                'closed' => 0,
                'rates' => [
                    json_encode([
                        "id_rate_plan"=> "0",
                        "id_pms_rate_plan" => "0",
                        'price_with_tax' => isset($row['price_with_tax']) ? (float)$row['price_with_tax'] : 0,
                        'price_without_tax' => isset($row['price_without_tax']) ? (float)$row['price_without_tax'] : 0,
                        'tax' => isset($row['tax']) ? (float)$row['tax'] : 0,
                        "occupancy_based_price" => json_encode([]),
                        "min_los_arrival" => 0,
                        "max_los_arrival" => 0,
                        "min_los_stay" => isset($losRestriction['min_los']) ? (int)$losRestriction['min_los'] : 1,
                        "max_los_stay" => isset($losRestriction['max_los']) ? (int)$losRestriction['max_los'] : 30,
                        "stop_sell" => 0,
                        "close_to_arrival" => 0,
                        "close_to_departure" => 0,
                        "min_advanced_booking_offset" => (int)$minBookingOffset,
                        "max_advanced_booking_offset" => (int)$maxBookingOffset
                    ])
                ]
            ];

            if (isset($row['availability'])) {
                $ariInfo['availability'] = (int)$row['availability'];
            }
            Hook::exec('actionChannelManagerAriPayloadModifier', array(
                'ariInfo' => &$ariInfo,
                'idHotel' => $row['id_pms_property'],
                'idRoomType' => $row['id_pms_room_type'],
                'dateFrom' => $row['date_from'],
                'dateTo' => $row['date_to'],
            ));
            $finalAriUpdatePayload[$row['id_pms_property']]['ari_info'][] = $ariInfo;
        }
        return array_values($finalAriUpdatePayload);
    }

    //function to get ari of specific property for channel manager
    public function getAriByIdProperty($params)
    {
        $finalAriUpdatePayload = [];
        if($dateWiseHotelAri = $this->getHotelAri($params)) {
            if($mergeData = $this->mergeHotelAriData($dateWiseHotelAri)) {
                foreach ($mergeData as $idHotel => $productWiseAri){
                    foreach ($productWiseAri as $idProduct => $ariData) {
                        foreach ($ariData as $key => $ari) {
                            $ariInfo = [
                                'id_room_type' => '',
                                'id_pms_room_type' => (string)$idProduct,
                                'availability'=> $ari['total_available_rooms'],
                                'date_from' => $ari['date_from'],
                                'date_to' => $ari['date_to'],
                                'weekdays' => ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'],
                                'closed' => 0,
                                'rates' => [
                                    json_encode([
                                        "id_rate_plan"=> "0",
                                        "id_pms_rate_plan"=> "0",
                                        'price_with_tax' => $ari['total_price_with_tax'],
                                        'price_without_tax' => $ari['total_price_tax_excl'],
                                        "occupancy_based_price" => json_encode([]),
                                        "min_los_arrival"=> 0,
                                        "max_los_arrival"=> 0,
                                        "min_los_stay"=> $ari['min_los'],
                                        "max_los_stay"=> $ari['max_los'],
                                        "stop_sell"=> 0,
                                        "close_to_arrival"=> 0,
                                        "close_to_departure"=> 0,
                                        "min_advanced_booking_offset"=> $ari['minimum_booking_offset'],
                                        "max_advanced_booking_offset"=> $ari['maximum_booking_offset']
                                    ])
                                ],
                            ];

                            if (!isset($finalAriUpdatePayload[$idHotel])) {
                                $finalAriUpdatePayload[$idHotel] = [
                                    'id_property' => '',
                                    'id_pms_property' => (string)$idHotel,
                                    'ari_info' => []
                                ];
                            }
                            $finalAriUpdatePayload[$idHotel]['ari_info'][] = $ariInfo;
                        }
                    }
                }
            }
        }

        return json_encode(array(
            'success' => true,
            'data' => array_values($finalAriUpdatePayload)
        ));
    }

    /**
     *
     * @param array $params Parameters including id_property, id_room_type, date_from, date_to
     * @return string JSON encoded ARI response
     */
    public function getHotelAri($params)
    {
        if (!$idProperty = (int)($params['id_property'])) {
            return [];
        }

        $minimumBookingOffset = HotelOrderRestrictDate::getMinimumBookingOffset($idProperty);
        $maximumCheckoutOffset = HotelOrderRestrictDate::getMaximumCheckoutOffset($idProperty);
        // Get room type IDs
        $idProducts = [];
        if (!empty($params['id_room_type'])) {
            $idProducts = array_filter(array_map('intval', explode('|', $params['id_room_type'])));
        } else {
            $objHotelRoomType = new HotelRoomType();
            if ($roomTypesByIdProperty = $objHotelRoomType->getRoomTypeByHotelId($idProperty, Context::getContext()->language->id)) {
                foreach ($roomTypesByIdProperty as $roomTypeInfo) {
                    if ($roomTypeInfo['active']) {
                        $idProducts[] = $roomTypeInfo['id_room_type'];
                    }
                }
            }
        }

        if (empty($idProducts)) {
            return [];
        }

        // Get date range
        $dateFrom = !empty($params['date_from']) ? $params['date_from'] : date('Y-m-d');
        $dateTo = !empty($params['date_to']) ? $params['date_to'] : date('Y-m-d', strtotime(HotelOrderRestrictDate::getMaxOrderDate($idProperty)));

        // Build ARI params
        $bookingParams = [
            'date_from' => date('Y-m-d 00:00:00', strtotime($dateFrom)),
            'date_to' => date('Y-m-d 23:59:59', strtotime($dateTo)),
            'hotel_id' => $idProperty,
            'search_available' => 0,
            'search_booked' => 1,
            'search_unavai' => 1,
            'search_partial' => 0,
            'search_cart_rms' => 0,
        ];

        $objBookingDtl = new HotelBookingDetail();
        $searchAriData = [];
        $productsLosRestriction = $this->getDateWiseRestrictionOfProducts($idProducts, $dateFrom, $dateTo);
        foreach ($idProducts as $idProduct) {
            $bookingParams['id_room_type'] = $idProduct;
            if(!isset($searchAriData[$idProperty])) {
                $searchAriData[$idProperty] = array();
            }

            if(!isset($searchAriData[$idProperty][$idProduct])) {
                $searchAriData[$idProperty][$idProduct] = array();
            }

            $bookingData = $objBookingDtl->getBookingData($bookingParams);

            // Calculate date-wise data
            for ($currentDate = $dateFrom; $currentDate < $dateTo; $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)))) {
                $nextDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));

                if (!empty($bookingData['rm_data'])) {
                    foreach ($bookingData['rm_data'] as $roomType) {
                        $ariDateInfo = [
                            'date_from' => $currentDate,
                            'date_to' => $nextDate,
                        ];

                        $ariDateInfo['total_available_rooms'] = $roomType['numberOfRooms'];

                        // Get all rooms of product(room type)
                        $rooms = HotelRoomInformation::getHotelRoomsInfo($idProperty, $idProduct);
                        $numRooms = count($rooms);
                        $roomDetail = [];

                        // Booked rooms
                        if ($bookingParams['search_booked'] && isset($roomType['data']['booked'])) {
                            $bookedRoomIds = [];
                            foreach ($roomType['data']['booked'] as $bookedRow) {
                                if (!empty($bookedRow['detail'])) {
                                    foreach ($bookedRow['detail'] as $bookDetail) {
                                        if ($currentDate >= date('Y-m-d', strtotime($bookDetail['date_from'])) && $currentDate < date('Y-m-d', strtotime($bookDetail['date_to']))) {
                                            $bookedRoomIds[] = $bookedRow['id_room'];
                                        }
                                    }
                                }
                            }
                            $roomDetail['booked'] = array_values(array_filter($rooms, function($room) use ($bookedRoomIds) {
                                return in_array($room['id'], $bookedRoomIds);
                            }));
                        }
                        $ariDateInfo['total_available_rooms'] -= count($roomDetail['booked']);

                        // Unavailable rooms
                        if ($bookingParams['search_unavai'] && isset($roomType['data']['unavailable'])) {
                            $unavailableRoomIds = [];
                            foreach ($roomType['data']['unavailable'] as $unavailRow) {
                                if (!empty($unavailRow['detail'])) {
                                    foreach ($unavailRow['detail'] as $unavailDetail) {
                                        if(!$unavailDetail['date_from'] || !$unavailDetail['date_to']) {
                                            $unavailableRoomIds[] = $unavailRow['id_room'];
                                        } else if ($currentDate >= date('Y-m-d', strtotime($unavailDetail['date_from'])) && $currentDate < date('Y-m-d', strtotime($unavailDetail['date_to']))) {
                                            $unavailableRoomIds[] = $unavailRow['id_room'];
                                        }
                                    }
                                }
                            }
                            $roomDetail['unavailable'] = array_values(array_filter($rooms, function($room) use ($unavailableRoomIds) {
                                return in_array($room['id'], $unavailableRoomIds);
                            }));
                        }
                        $ariDateInfo['total_available_rooms'] -= count($roomDetail['unavailable']);
                        $totalBookingPrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                            $idProduct,
                            $currentDate,
                            date('Y-m-d H:i:s', strtotime('+1 day', strtotime($currentDate))),
                            1
                        );

                        if(isset($productsLosRestriction[$idProduct])
                            && isset($productsLosRestriction[$idProduct][$currentDate])
                        && $productsLosRestriction[$idProduct][$currentDate]
                        ) {
                            $ariDateInfo['min_los'] = $productsLosRestriction[$idProduct][$currentDate]['min_los'] ?? 1;
                            $ariDateInfo['max_los'] = $productsLosRestriction[$idProduct][$currentDate]['max_los'] ?? 30;
                        } else {
                            // Use defaults from room type
                            $objRoomType = new HotelRoomType($idProduct);
                            $ariDateInfo['min_los'] = (int) $objRoomType->min_los;
                            $ariDateInfo['max_los'] = (int) $objRoomType->max_los;
                        }
                        $ariDateInfo['total_price_with_tax'] = $totalBookingPrice['total_price_tax_incl'];
                        $ariDateInfo['total_price_tax_excl'] = $totalBookingPrice['total_price_tax_excl'];
                        $ariDateInfo['tax'] = $totalBookingPrice['total_price_tax_incl'] - $totalBookingPrice['total_price_tax_excl'];
                        $ariDateInfo['minimum_booking_offset'] = $minimumBookingOffset;
                        $ariDateInfo['maximum_booking_offset'] = $maximumCheckoutOffset;
                        $searchAriData[$idProperty][$idProduct][] = $ariDateInfo;
                    }
                } else {
                    $ariDateInfo = [
                        'date_from' => $currentDate,
                        'date_to' => $nextDate,
                    ];
                    $ariDateInfo['total_available_rooms'] = 0;
                    $totalBookingPrice = HotelRoomTypeFeaturePricing::getRoomTypeTotalPrice(
                        $idProduct,
                        $currentDate,
                        date('Y-m-d H:i:s', strtotime('+1 day', strtotime($currentDate))),
                        1
                    );

                    if(isset($productsLosRestriction[$idProduct])
                        && isset($productsLosRestriction[$idProduct][$currentDate])
                    && $productsLosRestriction[$idProduct][$currentDate]
                    ) {
                        $ariDateInfo['min_los'] = $productsLosRestriction[$idProduct][$currentDate]['min_los'] ?? 1;
                        $ariDateInfo['max_los'] = $productsLosRestriction[$idProduct][$currentDate]['max_los'] ?? 30;
                    } else {
                        // Use defaults from room type
                        $objRoomType = new HotelRoomType($idProduct);
                        $ariDateInfo['min_los'] = (int) $objRoomType->min_los;
                        $ariDateInfo['max_los'] = (int) $objRoomType->max_los;
                    }
                    $ariDateInfo['minimum_booking_offset'] = $minimumBookingOffset;
                    $ariDateInfo['maximum_booking_offset'] = $maximumCheckoutOffset;
                    $ariDateInfo['total_price_with_tax'] = $totalBookingPrice['total_price_tax_incl'];
                    $ariDateInfo['total_price_tax_excl'] = $totalBookingPrice['total_price_tax_excl'];
                    $ariDateInfo['tax'] = $totalBookingPrice['total_price_tax_incl'] - $totalBookingPrice['total_price_tax_excl'];
                    $searchAriData[$idProperty][$idProduct][] = $ariDateInfo;
                }
            }
        }
        return $searchAriData;
    }


    public function getDateWiseRestrictionOfProducts($idProducts, $dateFrom, $dateTo)
    {
        $lengthOfStayArr = [];
        foreach ($idProducts as $idProduct) {
            if(!isset($lengthOfStayArr[$idProduct])) {
                $lengthOfStayArr[$idProduct] = [];
            }
            if (Validate::isLoadedObject($objRoomType = new HotelRoomType($idProduct))) {
                $minLosDefault = (int) $objRoomType->min_los;
                $maxLosDefault = (int) $objRoomType->max_los;

                $objHotelRoomTypeRestrictionDateRange = new HotelRoomTypeRestrictionDateRange();
                $lengthStayArr = $objHotelRoomTypeRestrictionDateRange->getRoomTypeLengthOfStayRestriction($idProduct);

                // Pre-index restrictions by date for O(1) lookup instead of O(n) per date
                $restrictionByDate = [];
                foreach ($lengthStayArr as $restriction) {
                    $dateFromRestriction = $restriction['date_from'];
                    $dateToRestriction = $restriction['date_to'];

                    // Build a date-indexed lookup for fast access
                    for ($restrDate = $dateFromRestriction; $restrDate <= $dateToRestriction; $restrDate = date('Y-m-d', strtotime('+1 day', strtotime($restrDate)))) {
                        if (!isset($restrictionByDate[$restrDate])) {
                            $restrictionByDate[$restrDate] = [
                                'min_los' => isset($restriction['min_los']) && $restriction['min_los'] !== '' ? (int) $restriction['min_los'] : $minLosDefault,
                                'max_los' => isset($restriction['max_los']) && $restriction['max_los'] !== '' ? (int) $restriction['max_los'] : $maxLosDefault,
                            ];
                        }
                    }
                }

                // Now iterate through requested date range with O(1) lookup per date
                for ($currentDate = $dateFrom; $currentDate < $dateTo; $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)))) {
                    if (isset($restrictionByDate[$currentDate])) {
                        $lengthOfStayArr[$idProduct][$currentDate] = $restrictionByDate[$currentDate];
                    } else {
                        // Use defaults if no restriction found for this date
                        $lengthOfStayArr[$idProduct][$currentDate] = [
                            'min_los' => $minLosDefault,
                            'max_los' => $maxLosDefault,
                        ];
                    }
                }
            }
        }

        return $lengthOfStayArr;
    }

    /**
     * Get LOS (Length of Stay) restriction for a specific room type and date
     *
     * @param int $idRoomType Room type ID
     * @param string $date Date in Y-m-d format
     * @return array Array with min_los and max_los
     */
    public function getLosRestrictionForDate($idRoomType, $date)
    {
        $result = [
            'min_los' => 0,
            'max_los' => 0,
        ];

        if (Validate::isLoadedObject($objRoomType = new HotelRoomType($idRoomType))) {
            $minLosDefault = (int) $objRoomType->min_los;
            $maxLosDefault = (int) $objRoomType->max_los;

            $objHotelRoomTypeRestrictionDateRange = new HotelRoomTypeRestrictionDateRange();
            $lengthStayArr = $objHotelRoomTypeRestrictionDateRange->getRoomTypeLengthOfStayRestriction($idRoomType);

            if (!empty($lengthStayArr)) {
                foreach ($lengthStayArr as $restriction) {
                    $dateFromRestriction = $restriction['date_from'];
                    $dateToRestriction = $restriction['date_to'];

                    // Check if the date falls within the restriction range
                    if ($date >= $dateFromRestriction && $date <= $dateToRestriction) {
                        $result['min_los'] = isset($restriction['min_los']) && $restriction['min_los'] !== ''
                            ? (int) $restriction['min_los']
                            : $minLosDefault;
                        $result['max_los'] = isset($restriction['max_los']) && $restriction['max_los'] !== ''
                            ? (int) $restriction['max_los']
                            : $maxLosDefault;
                        break;
                    }
                }
            }

            // If no restriction found for the date, return defaults
            if ($result['min_los'] == 0 && $result['max_los'] == 0) {
                $result['min_los'] = $minLosDefault;
                $result['max_los'] = $maxLosDefault;
            }
        }

        return $result;
    }

    public function mergeHotelAriData($ariData)
    {
        $mergedData = [];
        foreach ($ariData as $hotelId => $roomTypeData) {
            if(!isset($mergedData[$hotelId])) {
                $mergedData[$hotelId] = [];
            }

            if (!is_array($roomTypeData)) {
                continue;
            }

            foreach ($roomTypeData as $idRoomType => $roomTypeWiseAri) {
                if (!is_array($roomTypeWiseAri) || empty($roomTypeWiseAri)) {
                    continue;
                }

                // Sort by date_from to ensure chronological order
                usort($roomTypeWiseAri, function($a, $b) {
                    return strcmp($a['date_from'], $b['date_from']);
                });

                if(!isset($mergedData[$hotelId][$idRoomType])) {
                    $mergedData[$hotelId][$idRoomType] = [];
                }

                $temp = [];
                foreach ($roomTypeWiseAri as $ari) {
                    if (empty($temp)) {
                        // First record - initialize temp
                        $temp = array(
                            'date_from' => $ari['date_from'],
                            'date_to' => $ari['date_to'],
                            'total_available_rooms' => $ari['total_available_rooms'],
                            'min_los' => $ari['min_los'],
                            'max_los' => $ari['max_los'],
                            'total_price_with_tax' => $ari['total_price_with_tax'],
                            'total_price_tax_excl' => $ari['total_price_tax_excl'],
                            'tax' => $ari['tax'],
                            'minimum_booking_offset' => $ari['minimum_booking_offset'],
                            'maximum_booking_offset' => $ari['maximum_booking_offset']
                        );
                    } else {
                        // Check if consecutive dates (temp.date_to == ari.date_from) AND all other fields match
                        if ($temp['date_to'] == $ari['date_from'] &&
                            $temp['min_los'] == $ari['min_los'] &&
                            $temp['max_los'] == $ari['max_los'] &&
                            $temp['total_available_rooms'] == $ari['total_available_rooms'] &&
                            $temp['total_price_with_tax'] == $ari['total_price_with_tax'] &&
                            $temp['total_price_tax_excl'] == $ari['total_price_tax_excl'] &&
                            $temp['tax'] == $ari['tax'] &&
                            $temp['minimum_booking_offset'] == $ari['minimum_booking_offset'] &&
                            $temp['maximum_booking_offset'] == $ari['maximum_booking_offset']
                        ) {
                            // Merge - extend date_to
                            $temp['date_to'] = $ari['date_to'];
                        } else {
                            // Not consecutive or different parameters - push temp and start new
                            $mergedData[$hotelId][$idRoomType][] = $temp;
                            $temp = array(
                                'date_from' => $ari['date_from'],
                                'date_to' => $ari['date_to'],
                                'total_available_rooms' => $ari['total_available_rooms'],
                                'min_los' => $ari['min_los'],
                                'max_los' => $ari['max_los'],
                                'total_price_with_tax' => $ari['total_price_with_tax'],
                                'total_price_tax_excl' => $ari['total_price_tax_excl'],
                                'tax' => $ari['tax'],
                                'minimum_booking_offset' => $ari['minimum_booking_offset'],
                                'maximum_booking_offset' => $ari['maximum_booking_offset']
                            );
                        }
                    }
                }

                // Push remaining temp data
                if (!empty($temp)) {
                    $mergedData[$hotelId][$idRoomType][] = $temp;
                }
            }
        }
        return $mergedData;
    }
}
