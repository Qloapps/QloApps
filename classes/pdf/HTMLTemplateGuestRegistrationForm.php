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

        // Compute per-field visibility using Order constants — no magic numbers
        $grcInfo = Order::getRegistrationCardInfo();
        $fieldVisibility = array();
        foreach ($grcInfo as $sectionId => $section) {
            $isSectionEnabled = $allVisible || isset($grcFields[$sectionId]);
            $fieldVisibility[$sectionId] = array();
            foreach ($section['fields'] as $fieldId => $dummy) {
                $fieldVisibility[$sectionId][$fieldId] = $isSectionEnabled && ($allVisible || isset($grcFields[$sectionId][$fieldId]));
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
            'title'        => ($fieldVisibility[$section1][Order::GRC_GUEST_TITLE] ?? false)        ? $grcInfo[$section1]['fields'][Order::GRC_GUEST_TITLE]        : false,
            'full_name'    => ($fieldVisibility[$section1][Order::GRC_GUEST_FULL_NAME] ?? false)    ? $grcInfo[$section1]['fields'][Order::GRC_GUEST_FULL_NAME]    : false,
            'phone'        => ($fieldVisibility[$section1][Order::GRC_GUEST_PHONE] ?? false)        ? $grcInfo[$section1]['fields'][Order::GRC_GUEST_PHONE]        : false,
            'email'        => ($fieldVisibility[$section1][Order::GRC_GUEST_EMAIL] ?? false)        ? $grcInfo[$section1]['fields'][Order::GRC_GUEST_EMAIL]        : false,
            'dob'          => ($fieldVisibility[$section1][Order::GRC_GUEST_DOB] ?? false)          ? $grcInfo[$section1]['fields'][Order::GRC_GUEST_DOB]          : false,
            'nationality'  => ($fieldVisibility[$section1][Order::GRC_GUEST_NATIONALITY] ?? false)  ? $grcInfo[$section1]['fields'][Order::GRC_GUEST_NATIONALITY]  : false,
            'city_country' => ($fieldVisibility[$section1][Order::GRC_GUEST_CITY_COUNTRY] ?? false) ? $grcInfo[$section1]['fields'][Order::GRC_GUEST_CITY_COUNTRY] : false,
            'postal_code'  => ($fieldVisibility[$section1][Order::GRC_GUEST_POSTAL_CODE] ?? false)  ? $grcInfo[$section1]['fields'][Order::GRC_GUEST_POSTAL_CODE]  : false,
            'address'      => ($fieldVisibility[$section1][Order::GRC_GUEST_ADDRESS] ?? false)      ? $grcInfo[$section1]['fields'][Order::GRC_GUEST_ADDRESS]      : false,
            
            // Section 2: Travel Information
            'arrived_from' => ($fieldVisibility[$section2][Order::GRC_TRAVEL_ARRIVED_FROM] ?? false)     ? $grcInfo[$section2]['fields'][Order::GRC_TRAVEL_ARRIVED_FROM]     : false,
            'next_dest'    => ($fieldVisibility[$section2][Order::GRC_TRAVEL_NEXT_DESTINATION] ?? false) ? $grcInfo[$section2]['fields'][Order::GRC_TRAVEL_NEXT_DESTINATION] : false,
            'flight'       => ($fieldVisibility[$section2][Order::GRC_TRAVEL_FLIGHT_TRAIN] ?? false)     ? $grcInfo[$section2]['fields'][Order::GRC_TRAVEL_FLIGHT_TRAIN]     : false,
            'vehicle'      => ($fieldVisibility[$section2][Order::GRC_TRAVEL_VEHICLE_REG] ?? false)      ? $grcInfo[$section2]['fields'][Order::GRC_TRAVEL_VEHICLE_REG]      : false,
            'purpose'      => ($fieldVisibility[$section2][Order::GRC_TRAVEL_PURPOSE_OF_VISIT] ?? false) ? $grcInfo[$section2]['fields'][Order::GRC_TRAVEL_PURPOSE_OF_VISIT] : false,
            
            // Section 3: Booking Information
            'booking_ref'         => ($fieldVisibility[$section3][Order::GRC_BOOKING_REFERENCE] ?? false)      ? $grcInfo[$section3]['fields'][Order::GRC_BOOKING_REFERENCE]      : false,
            'booking_rate'        => ($fieldVisibility[$section3][Order::GRC_BOOKING_RATE_PER_NIGHT] ?? false) ? $grcInfo[$section3]['fields'][Order::GRC_BOOKING_RATE_PER_NIGHT] : false,
            'booking_arrival'     => ($fieldVisibility[$section3][Order::GRC_BOOKING_ARRIVAL] ?? false)        ? $grcInfo[$section3]['fields'][Order::GRC_BOOKING_ARRIVAL]        : false,
            'booking_departure'   => ($fieldVisibility[$section3][Order::GRC_BOOKING_DEPARTURE] ?? false)      ? $grcInfo[$section3]['fields'][Order::GRC_BOOKING_DEPARTURE]      : false,
            'booking_room_type'   => ($fieldVisibility[$section3][Order::GRC_BOOKING_ROOM_TYPE] ?? false)      ? $grcInfo[$section3]['fields'][Order::GRC_BOOKING_ROOM_TYPE]      : false,
            'booking_room_number' => ($fieldVisibility[$section3][Order::GRC_BOOKING_ROOM_NUMBER] ?? false)    ? $grcInfo[$section3]['fields'][Order::GRC_BOOKING_ROOM_NUMBER]    : false,
            'num_guests'          => ($fieldVisibility[$section3][Order::GRC_BOOKING_NUM_GUESTS] ?? false)     ? $grcInfo[$section3]['fields'][Order::GRC_BOOKING_NUM_GUESTS]     : false,
            
            // Section 4: Identification Document
            'id_proof'           => ($fieldVisibility[$section4][Order::GRC_ID_IDENTITY_PROOF] ?? false)          ? $grcInfo[$section4]['fields'][Order::GRC_ID_IDENTITY_PROOF]          : false,
            'id_number'          => ($fieldVisibility[$section4][Order::GRC_ID_NUMBER] ?? false)                  ? $grcInfo[$section4]['fields'][Order::GRC_ID_NUMBER]                  : false,
            'passport'           => ($fieldVisibility[$section4][Order::GRC_ID_PASSPORT_NO] ?? false)             ? $grcInfo[$section4]['fields'][Order::GRC_ID_PASSPORT_NO]             : false,
            'place_of_issue'     => ($fieldVisibility[$section4][Order::GRC_ID_PLACE_OF_ISSUE] ?? false)          ? $grcInfo[$section4]['fields'][Order::GRC_ID_PLACE_OF_ISSUE]          : false,
            'date_of_issue'      => ($fieldVisibility[$section4][Order::GRC_ID_DATE_OF_ISSUE] ?? false)           ? $grcInfo[$section4]['fields'][Order::GRC_ID_DATE_OF_ISSUE]           : false,
            'date_of_expiry'     => ($fieldVisibility[$section4][Order::GRC_ID_DATE_OF_EXPIRY] ?? false)          ? $grcInfo[$section4]['fields'][Order::GRC_ID_DATE_OF_EXPIRY]          : false,
            'visa'               => ($fieldVisibility[$section4][Order::GRC_ID_VISA_NUMBER] ?? false)             ? $grcInfo[$section4]['fields'][Order::GRC_ID_VISA_NUMBER]             : false,
            'valid_until'        => ($fieldVisibility[$section4][Order::GRC_ID_VALID_UNTIL] ?? false)             ? $grcInfo[$section4]['fields'][Order::GRC_ID_VALID_UNTIL]             : false,
            'arrival_in_country' => ($fieldVisibility[$section4][Order::GRC_ID_ARRIVAL_DATE_IN_COUNTRY] ?? false) ? $grcInfo[$section4]['fields'][Order::GRC_ID_ARRIVAL_DATE_IN_COUNTRY] : false,
            
            // Section 5: Additional Guests
            'addguest_name'        => ($fieldVisibility[$section5][Order::GRC_ADD_GUEST_NAME] ?? false)        ? $grcInfo[$section5]['fields'][Order::GRC_ADD_GUEST_NAME]        : false,
            'addguest_id_type'     => ($fieldVisibility[$section5][Order::GRC_ADD_GUEST_ID_TYPE] ?? false)     ? $grcInfo[$section5]['fields'][Order::GRC_ADD_GUEST_ID_TYPE]     : false,
            'addguest_id_number'   => ($fieldVisibility[$section5][Order::GRC_ADD_GUEST_ID_NUMBER] ?? false)   ? $grcInfo[$section5]['fields'][Order::GRC_ADD_GUEST_ID_NUMBER]   : false,
            'addguest_nationality' => ($fieldVisibility[$section5][Order::GRC_ADD_GUEST_NATIONALITY] ?? false) ? $grcInfo[$section5]['fields'][Order::GRC_ADD_GUEST_NATIONALITY] : false,
            
            // Section 6: Billing & Corporate
            'company' => ($fieldVisibility[$section6][Order::GRC_BILLING_COMPANY] ?? false) ? $grcInfo[$section6]['fields'][Order::GRC_BILLING_COMPANY] : false,
            'tax_id'  => ($fieldVisibility[$section6][Order::GRC_BILLING_TAX_ID] ?? false)  ? $grcInfo[$section6]['fields'][Order::GRC_BILLING_TAX_ID]  : false,
            
            // Section 7: Payment & Deposit
            'payment_method'   => ($fieldVisibility[$section7][Order::GRC_PAYMENT_METHOD] ?? false)           ? $grcInfo[$section7]['fields'][Order::GRC_PAYMENT_METHOD]           : false,
            'card_number'      => ($fieldVisibility[$section7][Order::GRC_PAYMENT_CARD_NUMBER] ?? false)      ? $grcInfo[$section7]['fields'][Order::GRC_PAYMENT_CARD_NUMBER]      : false,
            'security_deposit' => ($fieldVisibility[$section7][Order::GRC_PAYMENT_SECURITY_DEPOSIT] ?? false) ? $grcInfo[$section7]['fields'][Order::GRC_PAYMENT_SECURITY_DEPOSIT] : false,
            
            // Section 8: Guest Signature
            'signature' => ($fieldVisibility[$section8][Order::GRC_SIG_SIGNATURE] ?? false) ? $grcInfo[$section8]['fields'][Order::GRC_SIG_SIGNATURE] : false,
            'sig_date'  => ($fieldVisibility[$section8][Order::GRC_SIG_DATE] ?? false)      ? $grcInfo[$section8]['fields'][Order::GRC_SIG_DATE]      : false,
            
            // Section 9: Property Regulations (checkin/checkout share one visibility flag)
            'checkin_time'   => ($fieldVisibility[$section9][Order::GRC_PROP_CHECKIN_CHECKOUT_TIME] ?? false) ? Translate::getAdminTranslation('Check-in Time', 'AdminGuestRegistrationController', false, false)  : false,
            'checkout_time'  => ($fieldVisibility[$section9][Order::GRC_PROP_CHECKIN_CHECKOUT_TIME] ?? false) ? Translate::getAdminTranslation('Check-out Time', 'AdminGuestRegistrationController', false, false) : false,
            'hotel_policies' => ($fieldVisibility[$section9][Order::GRC_PROP_HOTEL_POLICIES] ?? false) ? $grcInfo[$section9]['fields'][Order::GRC_PROP_HOTEL_POLICIES] : false,
            
            // Section 10: For Office Use Only
            'staff_name'          => ($fieldVisibility[$section10][Order::GRC_OFFICE_STAFF_NAME] ?? false)   ? $grcInfo[$section10]['fields'][Order::GRC_OFFICE_STAFF_NAME]   : false,
            'office_checkin_time' => ($fieldVisibility[$section10][Order::GRC_OFFICE_CHECKIN_TIME] ?? false) ? $grcInfo[$section10]['fields'][Order::GRC_OFFICE_CHECKIN_TIME] : false,
            'id_verified'         => ($fieldVisibility[$section10][Order::GRC_OFFICE_ID_VERIFIED] ?? false)  ? $grcInfo[$section10]['fields'][Order::GRC_OFFICE_ID_VERIFIED]  : false,
            'reg_no'              => ($fieldVisibility[$section10][Order::GRC_OFFICE_REG_NO] ?? false)       ? $grcInfo[$section10]['fields'][Order::GRC_OFFICE_REG_NO]       : false,
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
            'show_section_guest_info'        => !empty(array_filter($fieldVisibility[$section1] ?? array())),
            'show_section_travel_info'       => !empty(array_filter($fieldVisibility[$section2] ?? array())),
            'show_section_booking_info'      => !empty(array_filter($fieldVisibility[$section3] ?? array())),
            'show_section_identification'    => !empty(array_filter($fieldVisibility[$section4] ?? array())),
            'show_section_additional_guests' => !empty(array_filter($fieldVisibility[$section5] ?? array())),
            'show_section_billing_corporate' => !empty(array_filter($fieldVisibility[$section6] ?? array())),
            'show_section_payment_deposit'   => !empty(array_filter($fieldVisibility[$section7] ?? array())),
            'show_section_guest_signature'   => !empty(array_filter($fieldVisibility[$section8] ?? array())),
            'show_section_property_regs'     => !empty(array_filter($fieldVisibility[$section9] ?? array())),
            'show_section_office_use'        => !empty(array_filter($fieldVisibility[$section10] ?? array())),

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
