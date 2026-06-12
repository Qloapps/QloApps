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

        // Additional guests rows based on max_guests of the room type
        $additionalGuestsRows = 0;
        if (Validate::isLoadedObject($hotelBookingDetail)) {
            $totalGuests = (int)$hotelBookingDetail->adults + (int)$hotelBookingDetail->children;
            $additionalGuestsRows = ($totalGuests > 1) ? ($totalGuests - 1) : 0;
        }
        
        $guestRegCardFields = array();
        $guestRegCardInfoJson = Configuration::get('QLO_GUEST_REGISTRATION_CARD_INFO');
        if ($guestRegCardInfoJson !== false && $guestRegCardInfoJson !== '') {
            $decoded = Tools::jsonDecode($guestRegCardInfoJson, true);
            if (is_array($decoded)) {
                foreach ($decoded as $sectionId => $fieldIds) {
                    $fieldIds = array_map('intval', (array)$fieldIds);
                    if (!empty($fieldIds)) {
                        $guestRegCardFields[(int)$sectionId] = array_flip($fieldIds);
                    }
                }
            }
        }

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
            'guest_reg_card_fields'             => $guestRegCardFields,
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
