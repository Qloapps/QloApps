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

class QcmcProperty extends QmkWebService
{
    public function getHotelList()
    {
        $objHotelBranchInfo = new HotelBranchInformation();
        $allHotels = $objHotelBranchInfo->getAllHotels();
        $hotels = array();

        foreach ($allHotels as $hotel) {
            $response = array();
            $response['id'] = $hotel['id'];
            $objHotelBranch = new HotelBranchInformation($response['id'], $this->context->language->id);
            $response['name'] = $objHotelBranch->hotel_name;
            $response['property_type'] = $this->objModule->l('hotel', 'QcmcChannelManagerProperty');
            $response['description'] = $objHotelBranch->description;
            $response['email'] = $objHotelBranch->email;
            $response['phone'] = $objHotelBranch->phone;
            $response['currency'] = Currency::getCurrency($this->context->currency->id)['iso_code'];
            $response['country_code'] = Country::getIsoById($objHotelBranch->id_country);
            $response['state'] = State::getNameById($objHotelBranch->id_state);
            $response['city'] = $objHotelBranch->city;
            $response['address1'] = $objHotelBranch->address;
            $response['address2'] = '';
            $response['zip_code'] = $objHotelBranch->zipcode;
            $response['latitude'] = $objHotelBranch->latitude;
            $response['longitude'] = $objHotelBranch->longitude;
            $response['timezone'] = Configuration::get('PS_TIMEZONE');
            $hotels[] = $response;
        }

        return json_encode(array(
            'success' => true,
            'data' => $hotels
        ));
    }
}
