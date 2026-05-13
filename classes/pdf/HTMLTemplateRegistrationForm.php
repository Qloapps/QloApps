<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License version 3.0
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/license/osl-3.0-php
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
 * @license https://opensource.org/license/osl-3.0-php Open Software License version 3.0
 */

class HTMLTemplateRegistrationFormCore extends HTMLTemplate
{
    public $order;
    public $available_in_your_account = false;
    protected $id_hotel_booking_detail = null;

    /**
     * @param Order $objOrder
     * @param $smarty
     */
    public function __construct(Order $objOrder, $smarty)
    {
        $this->order = $objOrder;
        $this->smarty = $smarty;
        $this->id_hotel_booking_detail = (int)Tools::getValue('id_hotel_booking_detail');
        $this->date = Tools::displayDate($objOrder->date_add);
        $this->title = $objOrder->getUniqReference();
        $this->shop = new Shop((int)$this->order->id_shop);
    }

    /**
     * Returns the template's HTML header
     *
     * @return string HTML header
     */
    public function getHeader()
    {
        if ((bool)Configuration::get('QLO_GUEST_REG_ENABLE_HEADER')) {
            return parent::getHeader();
        }

        return '';
    }

    /**
     * Returns the template's HTML content
     *
     * @return string HTML content
     */
    public function getContent()
    {
        $idLang = (int)$this->order->id_lang;
        $hotelBookingDetail = new HotelBookingDetail((int)$this->id_hotel_booking_detail);
        $idHotel = Validate::isLoadedObject($hotelBookingDetail) ? (int)$hotelBookingDetail->id_hotel : 0;

        $objHotelBranchInformation = null;
        $hotelCountry = '';

        if ($idHotel) {
            $objHotelBranchInformation = new HotelBranchInformation($idHotel, $idLang);
            if (Validate::isLoadedObject($objHotelBranchInformation)) {
                if ($objHotelBranchInformation->id_country) {
                    $objCountry = new Country((int)$objHotelBranchInformation->id_country, $idLang);
                    if (Validate::isLoadedObject($objCountry)) {
                        $hotelCountry = (string)$objCountry->name;
                    }
                }
            } else {
                $objHotelBranchInformation = null;
            }
        }

        // Property logo: hotel image first, then shop logo
        $propertyLogoPath = '';
        if ($idHotel && ($cover = HotelImage::getCover($idHotel))) {
            $imgPath = rtrim(_PS_HOTEL_IMG_DIR_, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.(int)$idHotel.DIRECTORY_SEPARATOR.(int)$cover['id'].'.jpg';
            if (file_exists($imgPath)) {
                $propertyLogoPath = $imgPath;
            }
        }
        if (!$propertyLogoPath) {
            $propertyLogoPath = (string)$this->getLogo();
        }

        $ratePerNight = '';
        $arrivalDateTime = '';
        $departureDateTime = '';

        if (Validate::isLoadedObject($hotelBookingDetail)) {
            $nights = (int)HotelHelper::getNumberOfDays($hotelBookingDetail->date_from, $hotelBookingDetail->date_to);
            if ($nights > 0) {
                $currency = new Currency((int)$this->order->id_currency);
                $ratePerNight = Tools::displayPrice((float)$hotelBookingDetail->total_price_tax_incl / $nights, $currency, false);
            }
            $arrivalDateTime = $hotelBookingDetail->date_from ? Tools::displayDate($hotelBookingDetail->date_from) : '';
            $departureDateTime = $hotelBookingDetail->date_to ? Tools::displayDate($hotelBookingDetail->date_to) : '';
            if ($arrivalDateTime && $objHotelBranchInformation && $objHotelBranchInformation->check_in && $objHotelBranchInformation->check_in != '00:00:00') {
                $arrivalDateTime .= ' '.date('h:i a', strtotime($objHotelBranchInformation->check_in));
            }
            if ($departureDateTime && $objHotelBranchInformation && $objHotelBranchInformation->check_out && $objHotelBranchInformation->check_out != '00:00:00') {
                $departureDateTime .= ' '.date('h:i a', strtotime($objHotelBranchInformation->check_out));
            }
        }

        // Additional guests rows based on max_guests of the room type
        $additionalGuestsRows = 0;
        if (Validate::isLoadedObject($hotelBookingDetail) && $hotelBookingDetail->id_product) {
            $roomTypeDetails = (new HotelRoomType())->getRoomTypeDetailByRoomTypeIds((string)(int)$hotelBookingDetail->id_product, false);
            if (!empty($roomTypeDetails) && isset($roomTypeDetails[0]['max_guests'])) {
                $maxGuests = (int)$roomTypeDetails[0]['max_guests'];
                $additionalGuestsRows = ($maxGuests > 1) ? ($maxGuests - 1) : 0;
            }
        }

        // Optional sections
        $sectionsValue = Configuration::get('QLO_GUEST_REG_OPTIONAL_SECTIONS');
        if ($sectionsValue === false || $sectionsValue === '') {
            $selectedSections = array(1, 2, 3, 4, 5, 6);
        } else {
            $decoded = Tools::jsonDecode($sectionsValue, true);
            $selectedSections = is_array($decoded) ? array_map('intval', $decoded) : array();
        }

        $this->smarty->assign(array(
            'style_tab'                      => $this->smarty->fetch($this->getTemplate('invoice.style-tab')),
            'hotel'                          => $objHotelBranchInformation,
            'property_logo_path'             => $propertyLogoPath,
            'property_city_country'          => implode(', ', array_filter(array($objHotelBranchInformation ? (string)$objHotelBranchInformation->city : '', $hotelCountry))),
            'booking_reference'              => $this->order->getUniqReference(),
            'arrival_date_time'              => $arrivalDateTime,
            'departure_date_time'            => $departureDateTime,
            'room_type'                      => Validate::isLoadedObject($hotelBookingDetail) ? $hotelBookingDetail->room_type_name : '',
            'room_number'                    => Validate::isLoadedObject($hotelBookingDetail) ? $hotelBookingDetail->room_num : '',
            'adults'                         => Validate::isLoadedObject($hotelBookingDetail) ? (int)$hotelBookingDetail->adults : 0,
            'children'                       => Validate::isLoadedObject($hotelBookingDetail) ? (int)$hotelBookingDetail->children : 0,
            'rate_per_night'                 => $ratePerNight,
            'additional_guests_rows'         => $additionalGuestsRows,
            'purpose_of_visit_options'       => GuestVisitPurpose::getGuestVisitPurposes(1, $idLang),
            'identity_proof_options'         => IdProof::getRegistrationIdProofs(1, $idLang),
            'payment_method_options'         => GuestRegistrationPaymentMethod::getRegistrationPaymentMethods(1, $idLang),
            'show_property_logo'             => in_array(1, $selectedSections),
            'show_additional_guests'         => in_array(2, $selectedSections),
            'show_billing_corporate_details' => in_array(3, $selectedSections),
            'show_payment_deposit'           => in_array(4, $selectedSections),
            'show_property_regulations'      => in_array(5, $selectedSections),
            'show_office_use_only'           => in_array(6, $selectedSections),
        ));

        return $this->smarty->fetch($this->getTemplate('registration-form'));
    }

    /**
     * Returns the template filename when using bulk rendering
     *
     * @return string filename
     */
    public function getBulkFilename()
    {
        return 'registration-forms.pdf';
    }

    /**
     * Returns the template filename
     *
     * @return string filename
     */
    public function getFilename()
    {
        $filename = 'registration-form-'.$this->order->reference;
        if ($this->id_hotel_booking_detail) {
            $filename .= '-room-'.$this->id_hotel_booking_detail;
        }

        return $filename.'.pdf';
    }
}
