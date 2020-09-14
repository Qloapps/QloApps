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

class QloRoomType extends QloWebservice
{
    public function getRoomType($idHotel)
    {
        $objHotelRoomType = new HotelRoomType();
        $idLang = $this->context->language->id;
        $roomTypes = $objHotelRoomType->getRoomTypeByHotelId((int)$idHotel, $idLang);

        $required = ['id', 'name', 'status'];

        $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('room_types', array());
        foreach ($roomTypes as $roomType) {
            $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('room_type', array());
            $roomType['name'] = $roomType['room_type'];
            $roomType['id'] = $roomType['id_product'];
            $roomType['status'] = $roomType['active'];
            foreach ($roomType as $key => $value) {
                if (in_array($key, $required)) {
                    $this->output .= $this->renderField($key, $value);
                }
            }
            $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('room_type', array());
        }
        $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('room_types', array());
        return $this->output;
    }

    public function getRoomRates(array $params)
    {
        $objRoomTypeFeaturePrice = new HotelRoomTypeFeaturePricing();

        $idProd = 0;
        if (isset($params['idRoomType'])) {
            $idProd = $params['idRoomType'];
        }

        $rates = $objRoomTypeFeaturePrice->getHotelRoomTypesRatesAndInventoryByDate($params['idHotel'], $idProd, $params['dateFrom'], $params['dateTo']);

        $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('getRoomRatesApi', array());
        foreach ($rates as $datewiseRate) {
            $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('rate_list', array());
            foreach ($datewiseRate as $attr => $value) {
                // how to put line written in else before if and no need to use else condition
                if ($attr == 'room_types') {
                    $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('room_types', array());
                    foreach ($value as $roomType) {
                        $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('room_type', array());
                        foreach ($roomType as $roomTypeAttr => $roomTypeData) {
                            if ($roomTypeAttr == 'rates') {
                                $roomTypeData['tax_inc'] = $roomTypeData['total_price_tax_incl'];
                                $roomTypeData['tax_exc'] = $roomTypeData['total_price_tax_excl'];
                                unset($roomTypeData['total_price_tax_incl']);
                                unset($roomTypeData['total_price_tax_excl']);
                                $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('rate', array());
                                foreach ($roomTypeData as $rateAttr => $rate) {
                                    $this->output .= $this->renderField($rateAttr, $rate);
                                }
                                $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('rate', array());
                            } else {
                                $this->output .= $this->renderField($roomTypeAttr, $roomTypeData);
                            }
                        }
                        $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('room_type', array());
                    }
                    $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('room_types', array());
                } else {
                    $this->output .= $this->renderField($attr, $value);
                }
            }
            $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('rate_list', array());
        }
        $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('getRoomRatesApi', array());
        return $this->output;
    }

    public function updRoomRateAvail($availRateData)
    {
        $hotelId = $availRateData['Hotel']['id'];
        $formatedData = [
            'propertyId' => $hotelId,
            'data' => [],
        ];

        $rateData = $availRateData['Hotel']['rate_lists']['rate_list'];

        foreach ($rateData as $dateWise) {
            $formateSubData = array();
            $formateSubData['dateFrom'] = $dateWise['date_range']['date_from'];
            $formateSubData['dateTo'] = $dateWise['date_range']['date_to'];

            if (isset($dateWise['date_range']['RoomTypeLists']['RoomType']['id'])) {
                $roomTypeDetail = $dateWise['date_range']['RoomTypeLists']['RoomType'];

                $formateSubData['roomType'][$roomTypeDetail['id']]['status'] = $roomTypeDetail['status'];
                if (isset($roomTypeDetail['rate'])) {
                    $formateSubData['roomType'][$roomTypeDetail['id']]['rate'] = $roomTypeDetail['rate'];
                }
                if (isset($roomTypeDetail['Inventory'])) {
                    $formateSubData['roomType'][$roomTypeDetail['id']]['inventory'] = $roomTypeDetail['Inventory'];
                }
            } else {
                foreach ($dateWise['date_range']['RoomTypeLists']['RoomType'] as $roomTypeDetail) {
                    $formateSubData['roomType'][$roomTypeDetail['id']]['status'] = $roomTypeDetail['status'];

                    if (isset($roomTypeDetail['rate'])) {
                        $formateSubData['roomType'][$roomTypeDetail['id']]['rate'] = $roomTypeDetail['rate'];
                    }
                    if (isset($roomTypeDetail['Inventory'])) {
                        $formateSubData['roomType'][$roomTypeDetail['id']]['inventory'] = $roomTypeDetail['Inventory'];
                    }
                }
            }

            $formatedData['data'][] = $formateSubData;
        }
        $objRoomTypeFeaturePrice = new HotelRoomTypeFeaturePricing();
        $objRoomTypeFeaturePrice->updateRoomTypesFeaturePricesAvailability($formatedData);

        $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('success', array());
        $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('success', array());

        return $this->output;
    }
    // /**
    //  * Get address details by id_address
    //  *
    //  * @api /getaddress
    //  * @param  int $idAddress
    //  * @return xml output
    //  */
    // public function getaddress($idAddress)
    // {
    //     $address = new Address($idAddress);
    //     if (!Validate::isLoadedObject($address)) {
    //         return false;
    //     }

    //     // convert object to array
    //     $address = Tools::jsonDecode(Tools::jsonEncode($address), true);

    //     $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('address', array());
    //     $address['country'] = Country::getNameById($this->id_lang, $address['id_country']);
    //     $address['state'] = State::getNameById($address['id_state']);
    //     foreach ($address as $key => $addr) {
    //         $this->output .= $this->renderField($key, $addr);
    //     }
    //     $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('address', array());
    //     return $this->output;
    // }

    // /**
    //  * [getCustomerAddress description]
    //  *
    //  * @api /getcustomeraddress
    //  * @param  int $idCustomer
    //  * @return xml output
    //  */
    // public function getCustomerAddress($idCustomer)
    // {
    //     $customer = new Customer($idCustomer);
    //     if (!Validate::isLoadedObject($customer)) {
    //         return false;
    //     }

    //     $required = ['id', 'country', 'state', 'alias', 'company', 'lastname', 'firstname',
    //         'address1', 'address2', 'postcode', 'city', 'other', 'other', 'phone_mobile', 'phone', 'deleted'];
    //     $addresses = $customer->getAddresses($this->id_lang);
    //     $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('addresses', array());
    //     foreach ($addresses as $address) {
    //         $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('address', array());
    //         $address['id'] = $address['id_address'];
    //         $address['country'] = Country::getNameById($this->id_lang, $address['id_country']);
    //         $address['state'] = State::getNameById($address['id_state']);
    //         foreach ($address as $key => $value) {
    //             if (in_array($key, $required)) {
    //                 $this->output .= $this->renderField($key, $value);
    //             }
    //         }
    //         $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('address', array());
    //     }
    //     $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('addresses', array());
    //     return $this->output;
    // }

    // /**
    //  * Check is customer have his first address or not
    //  *
    //  * @api /checkfirstaddress
    //  * @param  int $idCustomer
    //  * @return xml message
    //  */
    // public function checkFirstAddressProcess($idCustomer)
    // {
    //     if (Address::getFirstCustomerAddressId($idCustomer)) {
    //         return $this->getResult($this->mkmodule->l('Address exist for this customer.', 'mkaddress'), '1');
    //     }

    //     return $this->getResult($this->mkmodule->l('No address added by this customer yet.', 'mkaddress'), '0');
    // }

    // /**
    //  * Get required field in address from configuration
    //  *
    //  * @return xml node
    //  */
    // public function addressConfiguration()
    // {
    //     $requiredFields = AddressFormat::getFieldsRequired();
    //     $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('required_fields', array());
    //     $this->output .= $this->renderField('phone_config', Configuration::get('PS_ONE_PHONE_AT_LEAST'));
    //     foreach ($requiredFields as $field) {
    //         if ($field !== 'Country:name') {
    //             $this->output .= $this->renderField($field, 1);
    //         }
    //     }

    //     $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('required_fields', array());
    // }

    // /**
    //  * Get country and state list
    //  *
    //  * @api /getcountrieswithstates
    //  * @return [type] [description]
    //  */
    // public function getCountriesWithStatesProcess()
    // {
    //     $countries = Country::getCountries($this->id_lang, true);
    //     $this->output .= $this->addressConfiguration();
    //     $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('countries', array());
    //     if ($countries) {
    //         foreach ($countries as $country) {
    //             // set if default country
    //             if ($country['id_country'] == Configuration::get('PS_COUNTRY_DEFAULT')) {
    //                 $country['is_default'] = 1;
    //             } else {
    //                 $country['is_default'] = 0;
    //             }
    //             // check if company field exist
    //             if ($this->isCompanyFieldAvailable($country['id_country'])) {
    //                 $country['is_company'] = 1;
    //             } else {
    //                 $country['is_company'] = 0;
    //             }

    //             $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('country', array());
    //             $country['country_name'] = $country['country'];
    //             unset($country['country']);
    //             foreach ($country as $k => $c) {
    //                 if ($k == 'states') {
    //                     $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader($k, array());
    //                     foreach ($c as $s) {
    //                         $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('state', array());
    //                         foreach ($s as $kkk => $ss) {
    //                             $this->output .= $this->renderField($kkk, $ss);
    //                         }
    //                         $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('state', array());
    //                     }
    //                     $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter($k, array());
    //                 } else {
    //                     $this->output .= $this->renderField($k, $c);
    //                 }
    //             }
    //             $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('country', array());
    //         }
    //     }
    //     $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('countries', array());
    //     return $this->output;
    // }

    // /**
    //  * Is 'company' field available while adding address
    //  *
    //  * @param  int  $idCountry
    //  * @return boolean
    //  */
    // public function isCompanyFieldAvailable($idCountry)
    // {
    //     if (in_array('company', AddressFormat::getOrderedAddressFields($idCountry, true, true))) {
    //         return true;
    //     }
    //     return false;
    // }
}
