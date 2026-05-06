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

    public function requestMinimalMargins()
    {
        return !(bool)Configuration::get('QLO_GUEST_REG_ENABLE_HEADER');
    }

    /**
     * Returns the template's HTML content
     *
     * @return string HTML content
     */
    public function getContent()
    {
        $idLang = (int)$this->order->id_lang;
        $idHotel = (int)HotelBookingDetail::getIdHotelByIdOrder($this->order->id);

        $objHotelBookingDetail = new HotelBookingDetail();
        $hotelBookingDetails = $objHotelBookingDetail->getBookingDataByOrderId($this->order->id);
        if ($this->id_hotel_booking_detail) {
            $hotelBookingDetails = array_filter($hotelBookingDetails, function ($detail) {
                return $detail['id'] == $this->id_hotel_booking_detail;
            });
        }

        // Hotel info from HotelBranchInformation (authoritative)
        $hotelName = '';
        $hotelCheckInTime = '';
        $hotelCheckOutTime = '';
        $hotelPolicies = '';
        $hotelCity = '';
        $hotelCountry = '';

        if ($idHotel) {
            $objHotelBranchInformation = new HotelBranchInformation($idHotel, $idLang);
            if (Validate::isLoadedObject($objHotelBranchInformation)) {
                $hotelName = $objHotelBranchInformation->hotel_name;
                $hotelCity = (string)$objHotelBranchInformation->city;
                $policies = $objHotelBranchInformation->policies;
                $hotelPolicies = is_array($policies) ? '' : (string)$policies;

                $checkIn = $objHotelBranchInformation->check_in;
                $hotelCheckInTime = ($checkIn && $checkIn != '00:00:00') ? date('h:i a', strtotime($checkIn)) : '';

                $checkOut = $objHotelBranchInformation->check_out;
                $hotelCheckOutTime = ($checkOut && $checkOut != '00:00:00') ? date('h:i a', strtotime($checkOut)) : '';

                if ($objHotelBranchInformation->id_country) {
                    $objCountry = new Country((int)$objHotelBranchInformation->id_country, $idLang);
                    if (Validate::isLoadedObject($objCountry)) {
                        $hotelCountry = (string)$objCountry->name;
                    }
                }
            }
        }

        // Fallback to booking detail row for name/city/country if branch info unavailable
        if ((!$hotelName || !$hotelCity || !$hotelCountry) && !empty($hotelBookingDetails)) {
            $firstDetail = reset($hotelBookingDetails);
            if (!$hotelName) {
                $hotelName = $firstDetail['hotel_name'];
            }
            if (!$hotelCity) {
                $hotelCity = $firstDetail['city'];
            }
            if (!$hotelCountry) {
                $hotelCountry = $firstDetail['country'];
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

        // Stay data: dates, rooms, guests, rate
        $arrivalDate = '';
        $departureDate = '';
        $roomNumbers = array();
        $roomTypes = array();
        $adults = 0;
        $children = 0;
        $ratePerNight = '';
        $arrivalDateTime = '';
        $departureDateTime = '';

        if (!empty($hotelBookingDetails)) {
            $currency = new Currency((int)$this->order->id_currency);
            $totalRate = 0;
            $totalRateCount = 0;
            foreach ($hotelBookingDetails as $detail) {
                if (!$arrivalDate || strtotime($detail['date_from']) < strtotime($arrivalDate)) {
                    $arrivalDate = $detail['date_from'];
                }
                if (!$departureDate || strtotime($detail['date_to']) > strtotime($departureDate)) {
                    $departureDate = $detail['date_to'];
                }
                if (!empty($detail['room_num'])) {
                    $roomNumbers[] = $detail['room_num'];
                }
                if (!empty($detail['room_type_name'])) {
                    $roomTypes[] = $detail['room_type_name'];
                }
                $adults += (int)$detail['adults'];
                $children += (int)$detail['children'];
                $nights = (int)HotelHelper::getNumberOfDays($detail['date_from'], $detail['date_to']);
                if ($nights > 0) {
                    $totalRate += ((float)$detail['total_price_tax_incl'] / $nights);
                    ++$totalRateCount;
                }
            }

            if ($totalRateCount) {
                $ratePerNight = Tools::displayPrice($totalRate / $totalRateCount, $currency, false);
            }

            $arrivalDateTime = $arrivalDate ? Tools::displayDate($arrivalDate) : '';
            $departureDateTime = $departureDate ? Tools::displayDate($departureDate) : '';
            if ($arrivalDateTime && $hotelCheckInTime) {
                $arrivalDateTime .= ' '.$hotelCheckInTime;
            }
            if ($departureDateTime && $hotelCheckOutTime) {
                $departureDateTime .= ' '.$hotelCheckOutTime;
            }
        }

        // Additional guests rows based on max_guests of the room type
        $additionalGuestsRows = 0;
        if (!empty($hotelBookingDetails)) {
            $idProducts = array_unique(array_filter(array_map('intval', array_column($hotelBookingDetails, 'id_product'))));
            if (!empty($idProducts)) {
                $objHotelRoomType = new HotelRoomType();
                $roomTypeDetails = $objHotelRoomType->getRoomTypeDetailByRoomTypeIds(implode(',', $idProducts), false);
                if (!empty($roomTypeDetails) && isset($roomTypeDetails[0]['max_guests'])) {
                    $maxGuests = (int)$roomTypeDetails[0]['max_guests'];
                    $additionalGuestsRows = ($maxGuests > 1) ? ($maxGuests - 1) : 0;
                }
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
            // Hotel / property header
            'hotel_name'                     => $hotelName,
            'property_logo_path'             => $propertyLogoPath,
            'property_city_country'          => implode(', ', array_filter(array($hotelCity, $hotelCountry))),
            // Booking stay info
            'booking_reference'              => $this->order->getUniqReference(),
            'arrival_date_time'              => $arrivalDateTime,
            'departure_date_time'            => $departureDateTime,
            'room_type'                      => implode(', ', array_unique($roomTypes)),
            'room_number'                    => implode(', ', array_unique($roomNumbers)),
            'adults'                         => $adults,
            'children'                       => $children,
            'rate_per_night'                 => $ratePerNight,
            // Property regulations section
            'hotel_check_in_time'            => $hotelCheckInTime,
            'hotel_check_out_time'           => $hotelCheckOutTime,
            'hotel_policies'                 => $hotelPolicies,
            // Additional guests table
            'additional_guests_rows'         => $additionalGuestsRows,
            // Dynamic field options
            'purpose_of_visit_options'       => GuestVisitPurpose::getGuestVisitPurposes(1, $idLang),
            'identity_proof_options'         => GuestRegistrationIdProof::getRegistrationIdProofs(1, $idLang),
            'payment_method_options'         => GuestRegistrationPaymentMethod::getRegistrationPaymentMethods(1, $idLang),
            // Optional section visibility flags
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
