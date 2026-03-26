<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/afl-3.0.php
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
 * @license https://opensource.org/licenses/afl-3.0.php Academic Free License 3.0
 */

class HTMLTemplateRegistrationFormCore extends HTMLTemplate
{
    public $order;
    public $available_in_your_account = false;
    protected $id_hotel = 0;
    protected $id_hotel_booking_detail = null;

    /**
     * @param Order $objOrder
     * @param $smarty
     */
    public function __construct(Order $objOrder, $smarty)
    {
        $this->order = $objOrder;
        $this->smarty = $smarty;
        // Grab the ID directly from the URL parameters
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
        if ($this->isPdfHeaderEnabled()) {
            return parent::getHeader();
        }

        return '';
    }

    public function requestMinimalMargins()
    {
        return !$this->isPdfHeaderEnabled();
    }

    /**
     * Returns the template's HTML content
     *
     * @return string HTML content
     */
    public function getContent()
    {
        $objCustomer = new Customer((int)$this->order->id_customer);
        $objHotelBookingDetail = new HotelBookingDetail();
        $hotelBookingDetails = $objHotelBookingDetail->getBookingDataByOrderId($this->order->id);
        
        // Filter booking details if room_id is provided
        if ($this->id_hotel_booking_detail) {
            $hotelBookingDetails = array_filter($hotelBookingDetails, function($detail) {
                return $detail['id'] == $this->id_hotel_booking_detail;
            });
        }
        
        $this->id_hotel = (int)HotelBookingDetail::getIdHotelByIdOrder($this->order->id);

        $data = array(
            'order' => $this->order,
            'registration_form' => array(
                'guest' => $this->getGuestData($objCustomer),
                'hotel' => $this->getHotelData($hotelBookingDetails),
                'stay' => $this->getStayData($hotelBookingDetails),
                'property' => $this->getPropertyData($hotelBookingDetails),
                'config' => $this->getRegistrationConfig(),
                'dynamic_fields' => $this->getDynamicFields(),
            ),
        );

        $this->smarty->assign($data);
        $this->smarty->assign(array(
            'style_tab' => $this->smarty->fetch($this->getTemplate('invoice.style-tab')),
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

    /**
     * @param Customer $objCustomer
     *
     * @return array
     */
    protected function getGuestData(Customer $objCustomer)
    {
        $guestFullName = trim($objCustomer->firstname.' '.$objCustomer->lastname);
        $guestEmail = $objCustomer->email;
        $guestPhone = '';
        $guestAddress = '';
        $guestCityCountry = '';
        $guestPostcode = '';

        if ($this->order->id_address_invoice
            && Validate::isLoadedObject($objGuestAddress = new Address((int)$this->order->id_address_invoice))
        ) {
            if ($objGuestAddress->firstname || $objGuestAddress->lastname) {
                $guestFullName = trim($objGuestAddress->firstname.' '.$objGuestAddress->lastname);
            }

            if ($objGuestAddress->phone_mobile) {
                $guestPhone = $objGuestAddress->phone_mobile;
            } elseif ($objGuestAddress->phone) {
                $guestPhone = $objGuestAddress->phone;
            }

            $guestAddress = $objGuestAddress->address1;
            if (!empty($objGuestAddress->address2)) {
                $guestAddress .= ', ' . $objGuestAddress->address2;
            }

            $guestPostcode = (string)$objGuestAddress->postcode;
            $guestCityCountryParts = array();
            if ($objGuestAddress->city) {
                $guestCityCountryParts[] = $objGuestAddress->city;
            }
            if ($objGuestAddress->id_country && Validate::isLoadedObject($objCountry = new Country((int)$objGuestAddress->id_country, (int)$this->order->id_lang))) {
                $guestCityCountryParts[] = $objCountry->name;
            }
            $guestCityCountry = implode(', ', array_filter($guestCityCountryParts));
        }

        if ($id_order_customer_guest_detail = OrderCustomerGuestDetail::isCustomerGuestBooking($this->order->id)) {
            if (Validate::isLoadedObject(
                $objOrderCustomerGuestDetail = new OrderCustomerGuestDetail((int)$id_order_customer_guest_detail)
            )) {
                $guestFullName = trim($objOrderCustomerGuestDetail->firstname.' '.$objOrderCustomerGuestDetail->lastname);
                $guestEmail = $objOrderCustomerGuestDetail->email;
                $guestPhone = $objOrderCustomerGuestDetail->phone;
            }
        }

        return array(
            'full_name' => $guestFullName,
            'email' => $guestEmail,
            'mobile' => $guestPhone,
            'address' => $guestAddress,
            'city_country' => $guestCityCountry,
            'postcode' => $guestPostcode,
        );
    }

    /**
     * @param array $hotelBookingDetails
     *
     * @return array
     */
    protected function getHotelData($hotelBookingDetails)
    {
        $hotelName = '';
        $hotelAddress = '';
        $hotelEmail = '';
        $hotelPhone = '';
        $checkInTime = '';
        $checkOutTime = '';
        $hotelPolicies = '';
        $hotelCity = '';
        $hotelCountry = '';

        if (!empty($hotelBookingDetails)) {
            $hotelBookingDetail = reset($hotelBookingDetails);
            $hotelName = $hotelBookingDetail['hotel_name'];
            $hotelEmail = $hotelBookingDetail['email'];
            $hotelPhone = $hotelBookingDetail['phone'];
            $checkInTime = $this->formatHotelTime($hotelBookingDetail['check_in_time']);
            $checkOutTime = $this->formatHotelTime($hotelBookingDetail['check_out_time']);
            $hotelCity = $hotelBookingDetail['city'];
            $hotelCountry = $hotelBookingDetail['country'];
        }

        if ($idHotel = HotelBookingDetail::getIdHotelByIdOrder($this->order->id)) {
            $objHotelBranchInformation = new HotelBranchInformation((int)$idHotel, (int)$this->order->id_lang);
            if (Validate::isLoadedObject($objHotelBranchInformation)) {
                $hotelName = $objHotelBranchInformation->hotel_name;
                $hotelEmail = $objHotelBranchInformation->email;
                $hotelPhone = $objHotelBranchInformation->phone;
                $checkInTime = $this->formatHotelTime($objHotelBranchInformation->check_in);
                $checkOutTime = $this->formatHotelTime($objHotelBranchInformation->check_out);
                $hotelPolicies = (string)$objHotelBranchInformation->policies;
                $hotelCity = (string)$objHotelBranchInformation->city;
                if ($objHotelBranchInformation->id_country && Validate::isLoadedObject($objCountry = new Country((int)$objHotelBranchInformation->id_country, (int)$this->order->id_lang))) {
                    $hotelCountry = (string)$objCountry->name;
                }

                if ($idHotelAddress = $objHotelBranchInformation->getHotelIdAddress()) {
                    if (Validate::isLoadedObject($objHotelAddress = new Address((int)$idHotelAddress))) {
                        $objHotelAddress->firstname = $hotelName;
                        $objHotelAddress->lastname = '';
                        $hotelAddress = AddressFormat::generateAddress($objHotelAddress, array(), '<br />', ' ');
                        if (!$hotelCity && $objHotelAddress->city) {
                            $hotelCity = (string)$objHotelAddress->city;
                        }
                        if (!$hotelCountry && $objHotelAddress->id_country && Validate::isLoadedObject($objCountry = new Country((int)$objHotelAddress->id_country, (int)$this->order->id_lang))) {
                            $hotelCountry = (string)$objCountry->name;
                        }
                    }
                }
            }
        }

        if (!$hotelAddress && !empty($hotelBookingDetails)) {
            $hotelBookingDetail = reset($hotelBookingDetails);
            $hotelAddressParts = array(
                $hotelBookingDetail['hotel_name'],
                $hotelBookingDetail['city'],
                $hotelBookingDetail['state'],
                $hotelBookingDetail['country'],
                $hotelBookingDetail['zipcode'],
            );
            $hotelAddress = implode(', ', array_filter($hotelAddressParts));
        }

        return array(
            'name' => $hotelName,
            'address' => $hotelAddress,
            'email' => $hotelEmail,
            'phone' => $hotelPhone,
            'check_in_time' => $checkInTime,
            'check_out_time' => $checkOutTime,
            'policies' => $hotelPolicies,
            'city' => $hotelCity,
            'country' => $hotelCountry,
        );
    }

    /**
     * @param array $hotelBookingDetails
     *
     * @return array
     */
    protected function getStayData($hotelBookingDetails)
    {
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
            foreach ($hotelBookingDetails as $hotelBookingDetail) {
                if (!$arrivalDate || strtotime($hotelBookingDetail['date_from']) < strtotime($arrivalDate)) {
                    $arrivalDate = $hotelBookingDetail['date_from'];
                }

                if (!$departureDate || strtotime($hotelBookingDetail['date_to']) > strtotime($departureDate)) {
                    $departureDate = $hotelBookingDetail['date_to'];
                }

                if (!empty($hotelBookingDetail['room_num'])) {
                    $roomNumbers[] = $hotelBookingDetail['room_num'];
                }

                if (!empty($hotelBookingDetail['room_type_name'])) {
                    $roomTypes[] = $hotelBookingDetail['room_type_name'];
                }

                $adults += (int)$hotelBookingDetail['adults'];
                $children += (int)$hotelBookingDetail['children'];

                $nights = (int)HotelHelper::getNumberOfDays($hotelBookingDetail['date_from'], $hotelBookingDetail['date_to']);
                if ($nights > 0) {
                    $totalRate += ((float)$hotelBookingDetail['total_price_tax_incl'] / $nights);
                    ++$totalRateCount;
                }
            }

            if ($totalRateCount) {
                $avgRate = $totalRate / $totalRateCount;
                $ratePerNight = Tools::displayPrice($avgRate, $currency, false);
            }

            $firstBookingDetail = reset($hotelBookingDetails);
            $checkInTime = isset($firstBookingDetail['check_in_time']) ? $this->formatHotelTime($firstBookingDetail['check_in_time']) : '';
            $checkOutTime = isset($firstBookingDetail['check_out_time']) ? $this->formatHotelTime($firstBookingDetail['check_out_time']) : '';
            $arrivalDateTime = $arrivalDate ? Tools::displayDate($arrivalDate) : '';
            $departureDateTime = $departureDate ? Tools::displayDate($departureDate) : '';
            if ($arrivalDateTime && $checkInTime) {
                $arrivalDateTime .= ' '.$checkInTime;
            }
            if ($departureDateTime && $checkOutTime) {
                $departureDateTime .= ' '.$checkOutTime;
            }
        }

        return array(
            'arrival_date' => $arrivalDate ? Tools::displayDate($arrivalDate) : '',
            'departure_date' => $departureDate ? Tools::displayDate($departureDate) : '',
            'arrival_date_time' => $arrivalDateTime,
            'departure_date_time' => $departureDateTime,
            'room_number' => implode(', ', array_unique($roomNumbers)),
            'room_type' => implode(', ', array_unique($roomTypes)),
            'booking_reference' => $this->order->getUniqReference(),
            'adults' => $adults,
            'children' => $children,
            'rate_per_night' => $ratePerNight,
        );
    }

    /**
     * @param array $hotelBookingDetails
     *
     * @return array
     */
    protected function getPropertyData($hotelBookingDetails)
    {
        $propertyLogo = '';
        if ($this->id_hotel) {
            $propertyLogo = $this->getHotelLogoPath($this->id_hotel);
        }
        if (!$propertyLogo) {
            $propertyLogo = (string)$this->getLogo();
        }

        $cityCountry = '';
        if (!empty($hotelBookingDetails)) {
            $hotelBookingDetail = reset($hotelBookingDetails);
            $cityCountry = implode(', ', array_filter(array($hotelBookingDetail['city'], $hotelBookingDetail['country'])));
        }
        if (!$cityCountry) {
            $hotelData = $this->getHotelData($hotelBookingDetails);
            $cityCountry = implode(', ', array_filter(array($hotelData['city'], $hotelData['country'])));
        }

        $website = '';
        if (method_exists('Tools', 'getShopDomainSsl')) {
            $website = Tools::getShopDomainSsl(true, true);
        } else {
            $website = Tools::getHttpHost(true);
        }
        $website .= __PS_BASE_URI__;

        $additionalGuestsRows = 0;
        if (!empty($hotelBookingDetails)) {
            $idProducts = array();
            foreach ($hotelBookingDetails as $hotelBookingDetail) {
                if (!empty($hotelBookingDetail['id_product'])) {
                    $idProducts[] = (int)$hotelBookingDetail['id_product'];
                }
            }
            $idProducts = array_unique($idProducts);
            if (!empty($idProducts)) {
                $objHotelRoomType = new HotelRoomType();
                $roomTypes = $objHotelRoomType->getRoomTypeDetailByRoomTypeIds(implode(',', $idProducts), false);
                if (!empty($roomTypes) && isset($roomTypes[0]['max_guests'])) {
                    $maxGuests = (int)$roomTypes[0]['max_guests'];
                    $additionalGuestsRows = ($maxGuests > 1) ? ($maxGuests - 1) : 0;
                }
            }
        }

        return array(
            'logo_path' => $propertyLogo,
            'city_country' => $cityCountry,
            'website' => $website,
            'additional_guests_rows' => (int)$additionalGuestsRows,
        );
    }

    /**
     * @param int $idHotel
     *
     * @return string
     */
    protected function getHotelLogoPath($idHotel)
    {
        if (!$idHotel) {
            return '';
        }

        if ($cover = HotelImage::getCover((int)$idHotel)) {
            $imgPath = rtrim(_PS_HOTEL_IMG_DIR_, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.(int)$idHotel.DIRECTORY_SEPARATOR.(int)$cover['id'].'.jpg';
            if (file_exists($imgPath)) {
                return $imgPath;
            }
        }

        return '';
    }

    /**
     * @return bool
     */
    protected function isPdfHeaderEnabled()
    {
        return (bool)Configuration::get('QLO_GUEST_REG_ENABLE_HEADER');
    }

    /**
     * @return array
     */
    protected function getRegistrationConfig()
    {
        $selectedSections = $this->getSelectedOptionalSections();

        return array(
            'show_header' => $this->isPdfHeaderEnabled(),
            'show_additional_guests' => in_array(1, $selectedSections),
            'show_billing_corporate_details' => in_array(2, $selectedSections),
            'show_payment_deposit' => in_array(3, $selectedSections),
            'show_property_regulations' => in_array(4, $selectedSections),
            'show_office_use_only' => in_array(5, $selectedSections),
            'show_footer' => in_array(6, $selectedSections),
        );
    }

    /**
     * @return array
     */
    protected function getDynamicFields()
    {
        $idLang = (int)$this->order->id_lang;

        return array(
            'purpose_of_visit' => GuestRegPurpose::getActiveOptions($idLang),
            'identity_proof' => GuestRegIdProof::getActiveOptions($idLang),
            'payment_method' => GuestRegPaymentMethod::getActiveOptions($idLang),
        );
    }

    /**
     * @return array
     */
    protected function getSelectedOptionalSections()
    {
        $value = Configuration::get('QLO_GUEST_REG_OPTIONAL_SECTIONS');

        if ($value === false || $value === '') {
            return array(1, 2, 3, 4, 5, 6);
        }

        $value = Tools::jsonDecode($value, true);

        if (!is_array($value)) {
            return array();
        }

        return array_map('intval', $value);
    }

    /**
     * @param string $hotelTime
     *
     * @return string
     */
    protected function formatHotelTime($hotelTime)
    {
        if (!$hotelTime || $hotelTime == '00:00:00') {
            return '';
        }

        return date('h:i a', strtotime($hotelTime));
    }
}
    
