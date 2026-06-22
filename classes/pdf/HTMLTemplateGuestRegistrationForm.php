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

class HTMLTemplateGuestRegistrationFormCore extends HTMLTemplate
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
        $this->assignCommonHeaderData();
        $this->smarty->assign(array('header' => self::l('Guest Registration Card')));

        return $this->smarty->fetch($this->getTemplate('header'));
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

        $additionalGuestsRows = 0;
        if (Validate::isLoadedObject($hotelBookingDetail)) {
            $totalGuests = (int)$hotelBookingDetail->adults + (int)$hotelBookingDetail->children;
            $additionalGuestsRows = ($totalGuests > 1) ? ($totalGuests - 1) : 0;
        }

        // Build saved config: [sectionId => [fieldId => true, ...]]
        $grcFields = array();
        $grcInfoJson = Configuration::get('QLO_GUEST_REGISTRATION_CARD_INFO');
        if ($grcInfoJson !== false && $grcInfoJson !== '') {
            $grcData = Tools::jsonDecode($grcInfoJson, true);
            if (is_array($grcData)) {
                foreach ($grcData as $sectionId => $fieldIds) {
                    $fieldIds = array_map('intval', (array)$fieldIds);
                    if (!empty($fieldIds)) {
                        $grcFields[(int)$sectionId] = array_flip($fieldIds);
                    }
                }
            }
        }

        // When nothing saved, show all sections and fields
        $allVisible = empty($grcFields);

        // One pass: build false|string label for every field directly.
        // false = hidden, non-empty string = visible (used for both visibility check and label text in template).
        $grcInfo = Order::getRegistrationCardInfo();
        $fieldLabels = array();
        foreach ($grcInfo as $sectionId => $section) {
            $isSectionEnabled = $allVisible || isset($grcFields[$sectionId]);
            $fieldLabels[$sectionId] = array();
            foreach ($section['fields'] as $fieldId => $fieldLabel) {
                $isVisible = $isSectionEnabled && ($allVisible || isset($grcFields[$sectionId][$fieldId]));
                $fieldLabels[$sectionId][$fieldId] = $isVisible ? $fieldLabel : false;
            }
        }

        $section1  = Order::GRC_SECTION_GUEST_INFO;
        $section2  = Order::GRC_SECTION_TRAVEL_INFO;
        $section3  = Order::GRC_SECTION_BOOKING_INFO;
        $section4  = Order::GRC_SECTION_IDENTIFICATION;
        $section5  = Order::GRC_SECTION_ADDITIONAL_GUESTS;
        $section6  = Order::GRC_SECTION_BILLING_CORPORATE;
        $section7  = Order::GRC_SECTION_PAYMENT_DEPOSIT;
        $section8  = Order::GRC_SECTION_GUEST_SIGNATURE;
        $section9  = Order::GRC_SECTION_PROPERTY_REGS;
        $section10 = Order::GRC_SECTION_OFFICE_USE_ONLY;

        // $labels: false = field hidden, label string = field visible.
        // Template uses {if $labels.key} for visibility AND {$labels.key} for label — no separate boolean assigns.
        $labels = array(
            // Section 1: Guest Information
            'title'        => $fieldLabels[$section1][Order::GRC_GUEST_TITLE],
            'full_name'    => $fieldLabels[$section1][Order::GRC_GUEST_FULL_NAME],
            'phone'        => $fieldLabels[$section1][Order::GRC_GUEST_PHONE],
            'email'        => $fieldLabels[$section1][Order::GRC_GUEST_EMAIL],
            'dob'          => $fieldLabels[$section1][Order::GRC_GUEST_DOB],
            'nationality'  => $fieldLabels[$section1][Order::GRC_GUEST_NATIONALITY],
            'city_country' => $fieldLabels[$section1][Order::GRC_GUEST_CITY_COUNTRY],
            'postal_code'  => $fieldLabels[$section1][Order::GRC_GUEST_POSTAL_CODE],
            'address'      => $fieldLabels[$section1][Order::GRC_GUEST_ADDRESS],

            // Section 2: Travel Information
            'arrived_from' => $fieldLabels[$section2][Order::GRC_TRAVEL_ARRIVED_FROM],
            'next_dest'    => $fieldLabels[$section2][Order::GRC_TRAVEL_NEXT_DESTINATION],
            'flight'       => $fieldLabels[$section2][Order::GRC_TRAVEL_FLIGHT_TRAIN],
            'vehicle'      => $fieldLabels[$section2][Order::GRC_TRAVEL_VEHICLE_REG],
            'purpose'      => $fieldLabels[$section2][Order::GRC_TRAVEL_PURPOSE_OF_VISIT],

            // Section 3: Booking Information
            'booking_ref'         => $fieldLabels[$section3][Order::GRC_BOOKING_REFERENCE],
            'booking_rate'        => $fieldLabels[$section3][Order::GRC_BOOKING_RATE_PER_NIGHT],
            'booking_arrival'     => $fieldLabels[$section3][Order::GRC_BOOKING_ARRIVAL],
            'booking_departure'   => $fieldLabels[$section3][Order::GRC_BOOKING_DEPARTURE],
            'booking_room_type'   => $fieldLabels[$section3][Order::GRC_BOOKING_ROOM_TYPE],
            'booking_room_number' => $fieldLabels[$section3][Order::GRC_BOOKING_ROOM_NUMBER],
            'num_guests'          => $fieldLabels[$section3][Order::GRC_BOOKING_NUM_GUESTS],

            // Section 4: Identification Document
            'id_proof'           => $fieldLabels[$section4][Order::GRC_ID_IDENTITY_PROOF],
            'id_number'          => $fieldLabels[$section4][Order::GRC_ID_NUMBER],
            'passport'           => $fieldLabels[$section4][Order::GRC_ID_PASSPORT_NO],
            'place_of_issue'     => $fieldLabels[$section4][Order::GRC_ID_PLACE_OF_ISSUE],
            'date_of_issue'      => $fieldLabels[$section4][Order::GRC_ID_DATE_OF_ISSUE],
            'date_of_expiry'     => $fieldLabels[$section4][Order::GRC_ID_DATE_OF_EXPIRY],
            'visa'               => $fieldLabels[$section4][Order::GRC_ID_VISA_NUMBER],
            'valid_until'        => $fieldLabels[$section4][Order::GRC_ID_VALID_UNTIL],
            'arrival_in_country' => $fieldLabels[$section4][Order::GRC_ID_ARRIVAL_DATE_IN_COUNTRY],

            // Section 5: Additional Guests
            'addguest_name'        => $fieldLabels[$section5][Order::GRC_ADD_GUEST_NAME],
            'addguest_id_type'     => $fieldLabels[$section5][Order::GRC_ADD_GUEST_ID_TYPE],
            'addguest_id_number'   => $fieldLabels[$section5][Order::GRC_ADD_GUEST_ID_NUMBER],
            'addguest_nationality' => $fieldLabels[$section5][Order::GRC_ADD_GUEST_NATIONALITY],

            // Section 6: Billing & Corporate
            'company' => $fieldLabels[$section6][Order::GRC_BILLING_COMPANY],
            'tax_id'  => $fieldLabels[$section6][Order::GRC_BILLING_TAX_ID],

            // Section 7: Payment & Deposit
            'payment_method'   => $fieldLabels[$section7][Order::GRC_PAYMENT_METHOD],
            'card_number'      => $fieldLabels[$section7][Order::GRC_PAYMENT_CARD_NUMBER],
            'security_deposit' => $fieldLabels[$section7][Order::GRC_PAYMENT_SECURITY_DEPOSIT],

            // Section 8: Guest Signature
            'signature' => $fieldLabels[$section8][Order::GRC_SIG_SIGNATURE],
            'sig_date'  => $fieldLabels[$section8][Order::GRC_SIG_DATE],

            // Section 9: Property Regulations (checkin/checkout share one flag; labels come from Translate, not grcInfo)
            'checkin_time'   => $fieldLabels[$section9][Order::GRC_PROP_CHECKIN_CHECKOUT_TIME] ? Translate::getAdminTranslation('Check-in Time', 'AdminGuestRegistrationController', false, false)  : false,
            'checkout_time'  => $fieldLabels[$section9][Order::GRC_PROP_CHECKIN_CHECKOUT_TIME] ? Translate::getAdminTranslation('Check-out Time', 'AdminGuestRegistrationController', false, false) : false,
            'hotel_policies' => $fieldLabels[$section9][Order::GRC_PROP_HOTEL_POLICIES],

            // Section 10: For Office Use Only
            'staff_name'          => $fieldLabels[$section10][Order::GRC_OFFICE_STAFF_NAME],
            'office_checkin_time' => $fieldLabels[$section10][Order::GRC_OFFICE_CHECKIN_TIME],
            'id_verified'         => $fieldLabels[$section10][Order::GRC_OFFICE_ID_VERIFIED],
            'reg_no'              => $fieldLabels[$section10][Order::GRC_OFFICE_REG_NO],
        );

        $showLocalIdGroup = (bool)$labels['id_proof'] || (bool)$labels['id_number'];
        $showIntIdGroup  = (bool)$labels['passport'] || (bool)$labels['place_of_issue'] || (bool)$labels['date_of_issue']
                || (bool)$labels['date_of_expiry'] || (bool)$labels['visa'] || (bool)$labels['valid_until']
                || (bool)$labels['arrival_in_country'];

        $this->smarty->assign(array(
            'style_tab'              => $this->smarty->fetch($this->getTemplate('invoice.style-tab')),
            'hotel'                  => $objHotelBranchInformation,
            'hotel_country'          => $hotelCountry,
            'booking_reference'      => $this->order->getUniqReference(),
            'arrival_date_time'      => $arrivalDateTime,
            'departure_date_time'    => $departureDateTime,
            'room_type'              => Validate::isLoadedObject($hotelBookingDetail) ? $hotelBookingDetail->room_type_name : '',
            'room_number'            => Validate::isLoadedObject($hotelBookingDetail) ? $hotelBookingDetail->room_num : '',
            'adults'                 => Validate::isLoadedObject($hotelBookingDetail) ? (int)$hotelBookingDetail->adults : 0,
            'children'               => Validate::isLoadedObject($hotelBookingDetail) ? (int)$hotelBookingDetail->children : 0,
            'rate_per_night'         => $ratePerNight,
            'additional_guests_rows' => $additionalGuestsRows,

            // Section visibility
            'show_section_guest_info'        => !empty(array_filter($fieldLabels[$section1])),
            'show_section_travel_info'       => !empty(array_filter($fieldLabels[$section2])),
            'show_section_booking_info'      => !empty(array_filter($fieldLabels[$section3])),
            'show_section_identification'    => !empty(array_filter($fieldLabels[$section4])),
            'show_section_additional_guests' => !empty(array_filter($fieldLabels[$section5])),
            'show_section_billing_corporate' => !empty(array_filter($fieldLabels[$section6])),
            'show_section_payment_deposit'   => !empty(array_filter($fieldLabels[$section7])),
            'show_section_guest_signature'   => !empty(array_filter($fieldLabels[$section8])),
            'show_section_property_regs'     => !empty(array_filter($fieldLabels[$section9])),
            'show_section_office_use'        => !empty(array_filter($fieldLabels[$section10])),

            'section_additional_guests' => $grcInfo[$section5]['name'],
            'section_property_regs'     => $grcInfo[$section9]['name'],

            'local_id' => $showLocalIdGroup,
            'int_id'   => $showIntIdGroup,
            'labels'   => $labels,
        ));

        return $this->smarty->fetch($this->getTemplate('guest-registration-form'));
    }

    /**
     * Returns the template filename when using bulk rendering
     *
     * @return string filename
     */
    public function getBulkFilename()
    {
        return 'guest-registration-forms.pdf';
    }

    /**
     * Returns the template filename
     *
     * @return string filename
     */
    public function getFilename()
    {
        $filename = 'guest-registration-form-'.$this->order->reference;
        if ($this->id_hotel_booking_detail) {
            $filename .= '-room-'.$this->id_hotel_booking_detail;
        }

        return $filename.'.pdf';
    }
}
