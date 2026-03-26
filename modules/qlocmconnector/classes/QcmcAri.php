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

    //function to get ari of specific property 
    public function getAriByIdProperty($params)
    {
        $fetchAriResponsePayload = array();
        if ($idProperty = (int)($params['id_property'])) {
            $roomTypeIds = [];
            if (isset($params['id_room_type'])) {
                $roomTypeIds = array_filter(array_map('intval',explode('|', $params['id_room_type'])));
            } else {
                $objHotelRoomType = new HotelRoomType();
                if ($roomTypesByIdProperty = $objHotelRoomType->getRoomTypeByHotelId($idProperty, Context::getContext()->language->id)) {
                    foreach ($roomTypesByIdProperty as $roomTypeInfo) {
                        if ($roomTypeInfo['active']) {
                            $roomTypeIds[] = $roomTypeInfo['id_room_type']; 
                        }
                    }
                }
            }
            if (!empty($roomTypeIds)) {
                $startDate = strtotime(date('Y-m-d'));
                $endDate = strtotime(HotelOrderRestrictDate::getMaxOrderDate($idProperty));

                $flatAriFetchRows = [];
                for ($date = $startDate; $date <= $endDate; $date = strtotime('+1 day', $date)) {
                    foreach ($roomTypeIds as $idRoomType) {
                        $flatAriFetchRows[] = [
                            'id_hotel' => $idProperty,
                            'id_room_type' => $idRoomType,
                            'date' => date('Y-m-d', $date),
                            'ari_data' => json_encode(['availability' => 1])
                        ];
                    }
                }
                if ($flatAriFetchRows) {
                    $fetchAriResponsePayload = $this->buildAriUpdatePayload($flatAriFetchRows);
                }
            }
        }
        return json_encode(array(
            'success' => true,
            'data' => $fetchAriResponsePayload
        ));
    }

    //function to build the payload as per pms standard and calculate availability
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
            if (array_key_exists('availability', $flatAriUpdateRowData)) {
                $flatAriUpdateItem['need_availability'] = true;
            }

            $flatAriUpdatePayloadDateWise[] = $flatAriUpdateItem;
        }

        if (empty($flatAriUpdatePayloadDateWise)) {
            return [];
        }

        //calculate per-hotel min/max date so we can fetch booking data in one call per hotel
        $hotelDateRanges = [];
        foreach ($flatAriUpdatePayloadDateWise as $datewiseAriUpdateData) {
            $idProperty = $datewiseAriUpdateData['id_pms_property'];
            $date = $datewiseAriUpdateData['date'];
            if (!isset($hotelDateRanges[$idProperty])) {
                $hotelDateRanges[$idProperty] = ['min' => $date, 'max' => $date];
            } else {
                if ($date < $hotelDateRanges[$idProperty]['min']) {
                    $hotelDateRanges[$idProperty]['min'] = $date;
                }
                if ($date > $hotelDateRanges[$idProperty]['max']) {
                    $hotelDateRanges[$idProperty]['max'] = $date;
                }
            }
        }

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
                $available = $availabilityMap[$payloadItem['id_pms_property']][$payloadItem['id_pms_room_type']][$payloadItem['date']] ?? 0;
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

            return strcmp($firstCmp['date'], $secondCmp['date']);
        });

        $mergedAriUpdatePayload = [];
        $currentData = null;

        foreach ($flatAriUpdatePayloadDateWise as $mergeFlatAriUpdateItem) {
            $keySameData = $currentData && ($currentData['id_pms_property'] === $mergeFlatAriUpdateItem['id_pms_property'])
            && $currentData['id_pms_room_type'] === $mergeFlatAriUpdateItem['id_pms_room_type']
            &&(
                (isset($currentData['availability']) && isset($mergeFlatAriUpdateItem['availability'])
                && $currentData['availability'] == $mergeFlatAriUpdateItem['availability'])
            );

            $isConsecutive = $currentData && date('Y-m-d', strtotime($currentData['date_to'].' +1 day')) === $mergeFlatAriUpdateItem['date'];

            if ($keySameData && $isConsecutive) {
                $currentData['date_to'] = $mergeFlatAriUpdateItem['date'];
            } else {
                if ($currentData) $mergedAriUpdatePayload[] = $currentData;
                $currentData = [
                    'id_pms_property' => $mergeFlatAriUpdateItem['id_pms_property'],
                    'id_pms_room_type' => $mergeFlatAriUpdateItem['id_pms_room_type'],
                    'date_from' => $mergeFlatAriUpdateItem['date'],
                    'date_to' => $mergeFlatAriUpdateItem['date'],
                ];

                if (isset($mergeFlatAriUpdateItem['availability'])) {
                    $currentData['availability'] = $mergeFlatAriUpdateItem['availability'];
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

            $ariInfo = [
                'id_room_type' => '',
                'id_pms_room_type' => (string)$row['id_pms_room_type'],
                'date_from' => $row['date_from'],
                'date_to' => $row['date_to'],
                'weekdays' => ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'],
                'price' => '',
                'closed' => 0,
                'rates' => []
            ];

            if (isset($row['availability'])) {
                $ariInfo['availability'] = (int)$row['availability'];
            }

            $finalAriUpdatePayload[$row['id_pms_property']]['ari_info'][] = $ariInfo;
        }

        return array_values($finalAriUpdatePayload);
    }
}