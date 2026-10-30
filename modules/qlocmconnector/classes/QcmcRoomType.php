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

class QcmcRoomType extends QmkWebService
{
    public function getRoomTypeList($idProperty)
    {
        $objHotelBranch = new HotelBranchInformation($idProperty, $this->context->language->id);
        if ($objHotelBranch->id) {
            $objHotelRoomType = new HotelRoomType();
            $allRoomTypes = $objHotelRoomType->getRoomTypeByHotelId($idProperty, $this->context->language->id);
            $roomTypes = array();

            foreach ($allRoomTypes as $room) {
                $response = array();
                $response['id'] = $room['id_product'];
                $objHotelRoomType = new HotelRoomType($response['id'], $this->context->language->id);
                $objHotelRoomInformation = new HotelRoomInformation();
                $response['id_product'] = $room['id_product'];
                $response['name'] = $room['room_type'];
                $response['total_rooms'] = count($objHotelRoomInformation->getHotelRoomsInfo($idProperty, $response['id']));
                $response['base_occupancy'] = $objHotelRoomType->adults;
                $response['max_adults'] = $objHotelRoomType->max_adults;
                $response['max_children'] = $objHotelRoomType->max_children;
                $response['max_infants'] = $objHotelRoomType->max_children;
                $roomTypes[] = $response;
            }

            return json_encode(array(
                'success' => true,
                'data' => $roomTypes
            ));
        } else {
            return json_encode(array(
                'success' => false,
                'data' => [],
                'message' => $this->objModule->l('Property not found.', 'QcmcChannelManagerRoomType'),
            ));
        }
    }
}
