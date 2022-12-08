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

        return $errors;
    }

    public function assignSearchPanelVariables()
    {
        $smartyVars = array();
        // send if page is landing or not
        $smartyVars['is_index_page'] = 0;
        if (Tools::getValue('controller') == 'index') {
            $smartyVars['is_index_page'] = 1;
        }

        $idHotel = 0;
        $objHotelInfo = new HotelBranchInformation();
        $hotelsInfo = $objHotelInfo->hotelBranchesInfo(0, 1);

        $locationEnabled = Configuration::get('WK_HOTEL_LOCATION_ENABLE');
        // if room type page
        if ($idProduct = Tools::getValue('id_product')) {
            $objHtlRoomType = new HotelRoomType();
            if ($roomTypeInfo = $objHtlRoomType->getRoomTypeInfoByIdProduct($idProduct)) {
                if (Validate::isLoadedObject($objHotelInfo = new HotelBranchInformation((int) $roomTypeInfo['id_hotel']))) {
                    $idHotelCategory = $objHotelInfo->id_category;
                }
            }
        } else {
            // if category page
            $idHotelCategory = Tools::getValue('id_category');
        }
        if ($idHotelCategory) {
            if (!$dateFrom = Tools::getValue('date_from')) {
                $dateFrom = date('Y-m-d');
                $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateFrom)));
            }
            if (!$dateTo = Tools::getValue('date_to')) {
                $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateFrom)));
            }
            $smartyVars['date_from'] = $dateFrom;
            $smartyVars['date_to'] = $dateTo;

            if (Validate::isLoadedObject($objCategory = new Category((int) $idHotelCategory))) {
                $idHotel = HotelBranchInformation::getHotelIdByIdCategory($idHotelCategory);
                $htlCategoryInfo = $objHotelInfo->getCategoryDataByIdCategory((int) $objCategory->id_parent);

                $objBookingDetail = new HotelBookingDetail();
                $searchedData['num_days'] = $objBookingDetail->getNumberOfDays($dateFrom, $dateTo);

                $searchedData['parent_data'] = $htlCategoryInfo;
                $searchedData['date_from'] = date('d-m-Y', strtotime($dateFrom));
                $searchedData['date_to'] = date('d-m-Y', strtotime($dateTo));
                $searchedData['htl_dtl'] = $objHotelInfo->hotelBranchesInfo(0, 1, 1, $idHotel);

                $searchedData['location'] = $searchedData['htl_dtl']['city'];
                if (isset($searchedData['htl_dtl']['state_name'])) {
                    $searchedData['location'] .= ', '.$searchedData['htl_dtl']['state_name'];
                }
                $searchedData['location'] .= ', '.$searchedData['htl_dtl']['country_name'];

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

                $smartyVars['search_data'] = $searchedData;
            }

            // if location is enabled the send hotels of the selected location only
            if ($locationEnabled) {
                $hotelsInfo = $objHotelInfo->hotelBranchInfoByCategoryId($idHotelCategory);
            }
        }

        $totalActiveHotels = count($hotelsInfo);
        // set hotels order restrict date on all hotels
        foreach ($hotelsInfo as &$hotel) {
            $maxOrderDate = HotelOrderRestrictDate::getMaxOrderDate($hotel['id']);
            $hotel['max_order_date'] = date('Y-m-d', strtotime($maxOrderDate));
            $hotel['preparation_time'] = HotelOrderRestrictDate::getPreparationTime($hotel['id']);
        }
        $smartyVars['location_enabled'] = $locationEnabled;
        $smartyVars['total_active_hotels'] = $totalActiveHotels;
        $smartyVars['hotels_info'] = $hotelsInfo;
        $smartyVars['show_hotel_name'] = Configuration::get('WK_HOTEL_NAME_ENABLE');

        $maxOrderDate = HotelOrderRestrictDate::getMaxOrderDate($idHotel);
        $smartyVars['max_order_date'] = date('Y-m-d', strtotime($maxOrderDate));
        $smartyVars['preparation_time'] = HotelOrderRestrictDate::getPreparationTime($idHotel);

        Context::getContext()->smarty->assign($smartyVars);
    }
}
