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

class WkRoomSearchHelper
{
    public function validateSearchFields()
    {
        $objModule = new WkRoomSearchBlock();
        $errors = array();
        $hotelCategoryId = Tools::getValue('hotel_cat_id');
        $checkIn = Tools::getValue('check_in_time');
        $checkOut = Tools::getValue('check_out_time');

        // change dates format to acceptable format
        $checkIn = date('Y-m-d', strtotime($checkIn));
        $checkOut = date('Y-m-d', strtotime($checkOut));

        $currentDate = date('Y-m-d');
        $maxOrderDate = Tools::getValue('max_order_date');
        $maxOrderDate = date('Y-m-d', strtotime($maxOrderDate));

        if ($hotelCategoryId == '') {
            $errors[] = $objModule->l('Please enter a location', 'WkRoomSearchHelper');
        }
        if ($checkIn == '' || !Validate::isDate($checkIn)) {
            $errors[] = $objModule->l('Please select a valid Check-In', 'WkRoomSearchHelper');
        }
        if ($checkOut == '' || !Validate::isDate($checkOut)) {
            $errors[] = $objModule->l('Please select a valid Check-Out', 'WkRoomSearchHelper');
        }
        if ($checkIn && $checkOut) {
            if (($checkIn < $currentDate)
                || ($checkOut <= $checkIn)
                || ($maxOrderDate < $checkIn || $maxOrderDate < $checkOut)
            ) {
                $errors[] = $objModule->l('Please select a valid date range', 'WkRoomSearchHelper');
            }
        }

        // Lets validate guest occupancy fields
        // Get guest occupancy variable
        if (Configuration::get('PS_FRONT_SEARCH_TYPE') == HotelBookingDetail::SEARCH_TYPE_OWS) {
            $guestOccupancy = Tools::getValue('occupancy');
            if (!count($guestOccupancy)) {
                $errors[] = $objModule->l('Invalid occupancy', 'WkRoomSearchHelper');
            } else {
                $adultTypeErr = 0;
                $childTypeErr = 0;
                $childAgeErr = 0;
                foreach ($guestOccupancy as $occupancy) {
                    if (!isset($occupancy['adults']) || !Validate::isUnsignedInt($occupancy['adults'])) {
                        $adultTypeErr = 1;
                    }
                    if (!isset($occupancy['children']) || !Validate::isUnsignedInt($occupancy['children'])) {
                        $childTypeErr = 1;
                    } elseif ($occupancy['children']) {
                        if (!isset($occupancy['child_ages']) || ($occupancy['children'] != count($occupancy['child_ages']))) {
                            $childAgeErr = 1;
                        } else {
                            foreach ($occupancy['child_ages'] as $childAge) {
                                if (!Validate::isUnsignedInt($childAge)) {
                                    $childAgeErr = 1;
                                }
                            }
                        }
                    }
                }
                if ($adultTypeErr) {
                    $errors[] = $objModule->l('Invalid adults', 'WkRoomSearchHelper');
                }
                if ($childTypeErr) {
                    $errors[] = $objModule->l('Invalid children', 'WkRoomSearchHelper');
                }
                if ($childAgeErr) {
                    $errors[] = $objModule->l('Invalid children ages', 'WkRoomSearchHelper');
                }
            }
        }

        return $errors;
    }

    public function assignSearchPanelVariables()
    {
        $smartyVars = array();
        $context = Context::getContext();
        // send if page is landing or not
        $smartyVars['is_index_page'] = 0;
        if (Tools::getValue('controller') == 'index') {
            $smartyVars['is_index_page'] = 1;
        }

        $idHotel = 0;
        $objHotelInfo = new HotelBranchInformation();
        $hotelsInfo = $objHotelInfo->hotelBranchesInfo(0, 1);

        $locationEnabled = Configuration::get('WK_HOTEL_LOCATION_ENABLE');
        $occupancyEnabled = false;
        if (Configuration::get('PS_FRONT_SEARCH_TYPE') == HotelBookingDetail::SEARCH_TYPE_OWS) {
            $occupancyEnabled = true;
        }
        // if room type page
        if ($idProduct = Tools::getValue('id_product')) {
            $objHtlRoomType = new HotelRoomType();
            if ($roomTypeInfo = $objHtlRoomType->getRoomTypeInfoByIdProduct($idProduct)) {
                if (Validate::isLoadedObject($objHotelInfo = new HotelBranchInformation((int) $roomTypeInfo['id_hotel']))) {
                    $idHotelCategory = $objHotelInfo->id_category;
                }
            } else {
                $idHotelCategory = false;
            }
        } else {
            // if category page
            $idHotelCategory = Tools::getValue('id_category');
        }
        $locationCategoryId = Tools::getValue('location');
        if ($idHotelCategory) {
            if (Validate::isLoadedObject($objCategory = new Category((int) $idHotelCategory))) {
                if ($objCategory->hasParent(Configuration::get('PS_LOCATIONS_CATEGORY'))) {

                    if (!$dateFrom = Tools::getValue('date_from')) {
                        $dateFrom = date('Y-m-d');
                        $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateFrom)));
                    }
                    if (!$dateTo = Tools::getValue('date_to')) {
                        $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateFrom)));
                    }

                    $idHotel = HotelBranchInformation::getHotelIdByIdCategory($idHotelCategory);
                    $htlCategoryInfo = $objHotelInfo->getCategoryDataByIdCategory((int) $objCategory->id_parent);
                    $searchedData['htl_dtl'] = $objHotelInfo->hotelBranchesInfo(0, 1, 1, $idHotel);
                    $minBookingOffset = (int) HotelOrderRestrictDate::getMinimumBookingOffset($idHotel);
                    if ($minBookingOffset
                        && strtotime(date('Y-m-d', strtotime('+'. ($minBookingOffset) .' days'))) > strtotime($dateFrom)
                    ) {
                        $dateFrom = date('Y-m-d', strtotime(date('Y-m-d', strtotime('+'. ($minBookingOffset) .' days'))));
                        if (strtotime($dateFrom) >= strtotime($dateTo)) {
                            $controller = Tools::getValue('controller');
                            if ($controller == 'product'
                                && ($idProduct = Tools::getValue('id_product'))
                            ) {
                                $objHotelRoomTypeRestrictionDateRange = new HotelRoomTypeRestrictionDateRange();
                                $los = $objHotelRoomTypeRestrictionDateRange->getRoomTypeLengthOfStay($idProduct, $dateFrom);
                                $dateTo = date('Y-m-d', strtotime('+'.$los['min_los'].' day', strtotime($dateFrom)));
                            } else {
                                $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateFrom)));
                            }
                        }
                    }

                    $searchedData['num_days'] = HotelHelper::getNumberOfDays($dateFrom, $dateTo);
                    $searchedData['parent_data'] = $htlCategoryInfo;
                    if (Tools::getValue('date_from') && Tools::getValue('date_to')) {
                        $searchedData['date_from'] = $dateFrom;
                        $searchedData['date_to'] = $dateTo;
                    }

                    if ($locationCategoryId) {
                        $objLocationCategory = new Category($locationCategoryId, $context->language->id);
                        if ($objLocationCategory->hasParent(Configuration::get('PS_LOCATIONS_CATEGORY'))) {
                            $searchedData['location'] = $objLocationCategory->name;
                            $searchedData['location_category_id'] = $locationCategoryId;
                        } else {
                            $locationCategoryId = false;
                        }
                    }

                    $searchedData['order_date_restrict'] = false;
                    $max_order_date = HotelOrderRestrictDate::getMaxOrderDate($idHotel);
                    $searchedData['max_order_date'] = date('Y-m-d', strtotime($max_order_date));
                    if ($max_order_date) {
                        if (strtotime('-1 day', strtotime($max_order_date)) < strtotime($dateFrom)
                            || strtotime($max_order_date) < strtotime($dateTo)
                        ) {
                            $searchedData['order_date_restrict'] = true;
                        }
                    }
                }

                if ($occupancyEnabled) {
                    // send occupancy information searched by the user
                    if ($occupancies = Tools::getvalue('occupancy')) {
                        if (Validate::isOccupancy($occupancies)) {
                            if ($searchedData['occupancies'] = $occupancies) {
                                $searchedData['occupancy_adults'] = array_sum(
                                    array_column($searchedData['occupancies'], 'adults')
                                );
                                $searchedData['occupancy_children'] = array_sum(
                                    array_column($searchedData['occupancies'], 'children')
                                );
                            }
                        }
                    }
                }
                $smartyVars['search_data'] = $searchedData;
            }

            // if location is enabled the send hotels of the selected location only
            if ($locationEnabled && $locationCategoryId) {
                $hotelsInfo = Category::getAllCategoriesName($locationCategoryId);
            }
        }

        $totalActiveHotels = count($hotelsInfo);
        // set hotels order restrict date on all hotels
        foreach ($hotelsInfo as $key => $hotel) {
            if ($hotel_info = $objHotelInfo->hotelBranchInfoByCategoryId($hotel['id_category'])) {
                $maxOrderDate = HotelOrderRestrictDate::getMaxOrderDate($hotel_info['id']);
                $hotelsInfo[$key]['id'] = $hotel_info['id'];
                $hotelsInfo[$key]['hotel_name'] = $hotel_info['hotel_name'];
                $hotelsInfo[$key]['max_order_date'] = date('Y-m-d', strtotime($maxOrderDate));
                $hotelsInfo[$key]['min_booking_offset'] = (int) HotelOrderRestrictDate::getMinimumBookingOffset($hotel_info['id']);
            } else {
                unset($hotelsInfo[$key]);
            }
        }
        $smartyVars['location_enabled'] = $locationEnabled;
        $smartyVars['total_active_hotels'] = $totalActiveHotels;
        $smartyVars['hotels_info'] = $hotelsInfo;
        $smartyVars['show_hotel_name'] = Configuration::get('WK_HOTEL_NAME_ENABLE');
        $smartyVars['max_child_age'] = Configuration::get('WK_GLOBAL_CHILD_MAX_AGE');
        $smartyVars['hotel_name_search_threshold'] = (int) Configuration::get('WK_HOTEL_NAME_SEARCH_THRESHOLD');

        $maxOrderDate = HotelOrderRestrictDate::getMaxOrderDate($idHotel);
        $smartyVars['max_order_date'] = date('Y-m-d', strtotime($maxOrderDate));
        $smartyVars['min_booking_offset'] = (int) HotelOrderRestrictDate::getMinimumBookingOffset($idHotel);

        if (!$locationEnabled
            && !$smartyVars['show_hotel_name']
            && count($hotelsInfo) <= 1
            && !$occupancyEnabled
        ) {
            $smartyVars['multiple_dates_input'] = true;
            Media::addJSDef(array(
                'multiple_dates_input' => true
            ));
        }

        $totalColumns = 9;// min value
        if ($locationEnabled) {
            $totalColumns += 4;
        }

        if (!(count($hotelsInfo) <= 1 && !$smartyVars['show_hotel_name'])) {
            $totalColumns += 5;
        }

        if (Configuration::get('PS_FRONT_SEARCH_TYPE') == HotelBookingDetail::SEARCH_TYPE_OWS) {
            $totalColumns += 4;
        }

        $smartyVars['total_columns'] = $totalColumns;
        Hook::exec('actionSearchPanelParamsModifier', array('params' => &$smartyVars));

        Context::getContext()->smarty->assign($smartyVars);
    }
}
