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

/**
 * Builds and renders the Booking Voucher PDF for a given Order.
 * Data assembly follows the same inline pattern as HTMLTemplateInvoice::getContent().
 */
class HTMLTemplateBookingVoucherCore extends HTMLTemplate
{
    /** @var Order */
    public $order;
    public $available_in_your_account = false;

    /**
     * @param Order $order
     * @param Smarty $smarty
     */
    public function __construct(Order $order, $smarty)
    {
        $this->order = $order;
        $this->smarty = $smarty;
        $this->date = Tools::displayDate(date('Y-m-d'));
        $this->title = sprintf(self::l('Booking Voucher - %s'), $this->order->reference);
        $this->shop = new Shop((int)$this->order->id_shop);
    }

    /**
     * @return string HTML header
     */
    public function getHeader()
    {
        $this->assignCommonHeaderData();
        $this->smarty->assign(array('header' => self::l('Booking Voucher')));

        return $this->smarty->fetch($this->getTemplate('header'));
    }

    /**
     * Assembles all voucher data inline and renders the template.
     *
     * @return string HTML content
     */
    public function getContent()
    {
        // --- Bookings ---
        $objHotelBookingDetail = new HotelBookingDetail();
        $bookings = $objHotelBookingDetail->getBookingDataByOrderId((int)$this->order->id);
        if (!is_array($bookings)) {
            $bookings = array();
        }

        // --- Order state ---
        $orderState = $this->order->getCurrentStateFull((int)$this->order->id_lang);

        // --- Hotel / property ---
        $objHotel = null;
        $idHotel = HotelBookingDetail::getIdHotelByIdOrder((int)$this->order->id);
        if ($idHotel) {
            $objHotel = new HotelBranchInformation((int)$idHotel, (int)$this->order->id_lang);
            if (!Validate::isLoadedObject($objHotel)) {
                $objHotel = null;
            }
        }

        $hotelAddress = '';
        $paymentPolicy = '';
        $cancellationPolicy = '';

        if ($objHotel) {
            $policies = is_array($objHotel->policies) ? '' : (string)$objHotel->policies;
            if ($policies) {
                $policyText = html_entity_decode($policies, ENT_QUOTES, 'UTF-8');
                $policyText = preg_replace('/<br\\s*\\/?>/i', "\n", $policyText);
                $policyText = strip_tags($policyText);
                $policyText = preg_replace("/[\r\n]+/", "\n", $policyText);
                $paymentPolicy = nl2br(trim($policyText));
            }

            $objBranchRefundRules = new HotelBranchRefundRules();
            $refundRules = $objBranchRefundRules->getHotelRefundRules(
                (int)$objHotel->id, 0, 1, (int)$this->order->id_lang, 1
            );
            if (is_array($refundRules) && !empty($refundRules)) {
                $policyBlocks = array();
                foreach ($refundRules as $rule) {
                    $ruleName = isset($rule['name']) ? trim(strip_tags($rule['name'])) : '';
                    $ruleDescription = isset($rule['description']) ? trim($rule['description']) : '';
                    if ($ruleName === '' && $ruleDescription === '') {
                        continue;
                    }
                    $block = $ruleName !== '' ? '<strong>'.Tools::safeOutput($ruleName).'</strong>' : '';
                    if ($ruleDescription !== '') {
                        $block .= ($block !== '' ? ': ' : '').$ruleDescription;
                    }
                    $policyBlocks[] = $block;
                }
                if (!empty($policyBlocks)) {
                    $cancellationPolicy = implode('<br /><br />', $policyBlocks);
                }
            }

            if ($idHotelAddress = $objHotel->getHotelIdAddress()) {
                $objHotelAddress = new Address((int)$idHotelAddress);
                if (Validate::isLoadedObject($objHotelAddress)) {
                    $objHotelAddress->firstname = $objHotel->hotel_name;
                    $hotelAddress = AddressFormat::generateAddress(
                        $objHotelAddress,
                        array('avoid' => array('lastname')),
                        '<br />',
                        ' '
                    );
                }
            }
        }

        // --- Rooms, services, occupancy ---
        $totalRooms = 0;
        $totalRoomNights = 0;
        $totalGuests = 0;
        $serviceGroups = array();
        $roomTypeRows = array();

        foreach ($bookings as $booking) {
            $nights = (int)HotelHelper::getNumberOfDays($booking['date_from'], $booking['date_to']);
            if ($nights <= 0) {
                $nights = 1;
            }

            $totalGuests += (int)$booking['adults'] + (int)$booking['children'];
            $totalRoomNights += $nights;
            $totalRooms++;

            $dateFrom = ($booking['date_from'] && $booking['date_from'] !== '0000-00-00 00:00:00')
                ? Tools::displayDate($booking['date_from'], null, false) : '--';
            $dateTo = ($booking['date_to'] && $booking['date_to'] !== '0000-00-00 00:00:00')
                ? Tools::displayDate($booking['date_to'], null, false) : '--';

            // Services for this booking
            $bookingServices = array();

            $objBookingDemand = new HotelBookingDemands();
            $extraDemands = $objBookingDemand->getRoomTypeBookingExtraDemands(
                (int)$this->order->id, 0, 0, 0, 0, 0, 0, 0,
                (int)$booking['id'], (int)$booking['id_order_detail']
            );
            if (is_array($extraDemands)) {
                foreach ($extraDemands as $demand) {
                    $bookingServices[] = array('name' => $demand['name'], 'quantity' => 1);
                }
            }

            // Auto-added services bundled with room price (for cost calculation only)
            $objServiceProductOrderDetail = new ServiceProductOrderDetail();
            $autoAddedWithRoomPriceTaxExcl = (float)$objServiceProductOrderDetail->getRoomTypeServiceProducts(
                (int)$this->order->id, 0, 0,
                isset($booking['id_product']) ? (int)$booking['id_product'] : 0,
                isset($booking['date_from']) ? $booking['date_from'] : 0,
                isset($booking['date_to']) ? $booking['date_to'] : 0,
                isset($booking['id_room']) ? (int)$booking['id_room'] : 0,
                1, 0, 1, Product::PRICE_ADDITION_TYPE_WITH_ROOM
            );

            // Additional services chosen by guest
            $additionalServices = $objServiceProductOrderDetail->getRoomTypeServiceProducts(
                0, 0, 0, 0, 0, 0, 0, 0, 1, 0, null, 0, (int)$booking['id']
            );
            if (isset($additionalServices[(int)$booking['id']]['additional_services'])) {
                foreach ($additionalServices[(int)$booking['id']]['additional_services'] as $service) {
                    $bookingServices[] = array('name' => $service['name'], 'quantity' => (int)$service['quantity']);
                }
            }

            if (!empty($bookingServices)) {
                $serviceGroups[] = array(
                    'room_type_name' => isset($booking['room_type_name']) ? $booking['room_type_name'] : '',
                    'check_in_date' => $dateFrom,
                    'check_out_date' => $dateTo,
                    'room_count' => $totalRooms,
                    'services' => $bookingServices,
                );
            }

            $roomTotalWithAutoServicesTaxExcl = (float)$booking['total_price_tax_excl'] + $autoAddedWithRoomPriceTaxExcl;

            // Aggregate per room type + date range
            $roomTypeRowKey = implode('|', array(
                (int)$booking['id_order_detail'],
                isset($booking['room_type_name']) ? $booking['room_type_name'] : '',
                isset($booking['date_from']) ? $booking['date_from'] : '',
                isset($booking['date_to']) ? $booking['date_to'] : '',
            ));

            if (!isset($roomTypeRows[$roomTypeRowKey])) {
                $idOrderDetail = (int)$booking['id_order_detail'];
                $detailTaxes = $idOrderDetail ? OrderDetail::getTaxListStatic($idOrderDetail) : null;
                $orderDetailTaxLabel = self::l('No tax');
                if ($detailTaxes) {
                    $taxTemp = array();
                    foreach ($detailTaxes as $tax) {
                        $objTax = new Tax($tax['id_tax']);
                        $taxTemp[] = sprintf(self::l('%1$s%2$s%%'), $objTax->rate + 0, '&nbsp;');
                    }
                    $orderDetailTaxLabel = implode(', ', $taxTemp);
                }

                $roomTypeRows[$roomTypeRowKey] = array(
                    'room_type_name' => isset($booking['room_type_name']) ? $booking['room_type_name'] : '',
                    'order_detail_tax_label' => $orderDetailTaxLabel,
                    'rooms' => 0,
                    'adults' => 0,
                    'children' => 0,
                    'nights' => $nights,
                    'check_in_date' => $dateFrom,
                    'check_out_date' => $dateTo,
                    'unit_price_tax_excl' => 0,
                    'total_price_tax_excl' => 0,
                );
            }

            $roomTypeRows[$roomTypeRowKey]['rooms'] += 1;
            $roomTypeRows[$roomTypeRowKey]['adults'] += (int)$booking['adults'];
            $roomTypeRows[$roomTypeRowKey]['children'] += (int)$booking['children'];
            $roomTypeRows[$roomTypeRowKey]['total_price_tax_excl'] += $roomTotalWithAutoServicesTaxExcl;
        }

        foreach ($roomTypeRows as &$roomTypeRow) {
            $denominator = (int)$roomTypeRow['nights'] * (int)$roomTypeRow['rooms'];
            if ($denominator > 0) {
                $roomTypeRow['unit_price_tax_excl'] = Tools::ps_round(
                    $roomTypeRow['total_price_tax_excl'] / $denominator,
                    6,
                    $this->order->round_mode
                );
            }
        }
        unset($roomTypeRow);

        $guestAddress = '';
        $guest = new Customer((int)$this->order->id_customer);

        $addressId = (int)$this->order->id_address_invoice ?: (int)$this->order->id_address_delivery;
        if ($addressId) {
            $address = new Address($addressId);
            if (Validate::isLoadedObject($address)) {
                $guestAddress = AddressFormat::generateAddress($address, array(), '<br />', ' ');
            }
        }

        if ($idCustomerGuestDetail = OrderCustomerGuestDetail::isCustomerGuestBooking((int)$this->order->id)) {
            $objGuest = new OrderCustomerGuestDetail((int)$idCustomerGuestDetail);
            if (Validate::isLoadedObject($objGuest)) {
                $guest = $objGuest;
            }
        }
        // --- Totals ---
        $objBookingDemand = new HotelBookingDemands();
        $totalDemandsPriceTE = (float)$objBookingDemand->getRoomTypeBookingExtraDemands(
            (int)$this->order->id, 0, 0, 0, 0, 1, 1, 0
        );
        $totalDemandsPriceTI = (float)$objBookingDemand->getRoomTypeBookingExtraDemands(
            (int)$this->order->id, 0, 0, 0, 0, 1, 1, 1
        );

        $orderDetails = OrderDetail::getList((int)$this->order->id);
        $idsOrderDetail = is_array($orderDetails)
            ? array_unique(array_map('intval', array_column($orderDetails, 'id_order_detail')))
            : array();

        $footer = array(
            'room_price_tax_excl' => 0,
            'service_products_price_tax_excl' => 0,
            'additional_service_price_tax_excl' => 0,
            'total_convenience_fee_te' => 0,
        );
        $roomsCostTaxIncl = 0;
        $serviceProductsCostTaxIncl = 0;
        $additionalServicesCostTaxIncl = 0;
        $convenienceFeeTaxIncl = 0;

        if (!empty($idsOrderDetail)) {
            $footer['room_price_tax_excl'] = $this->order->getTotalProductsWithoutTaxes(false, true, null, null, null, $idsOrderDetail) + $this->order->getTotalProductsWithoutTaxes(false, false, Product::SELLING_PREFERENCE_WITH_ROOM_TYPE, 1, Product::PRICE_ADDITION_TYPE_WITH_ROOM, $idsOrderDetail);
            $roomsCostTaxIncl = $this->order->getTotalProductsWithTaxes(false, true, null, null, null, $idsOrderDetail) + $this->order->getTotalProductsWithTaxes(false, false, Product::SELLING_PREFERENCE_WITH_ROOM_TYPE, 1, Product::PRICE_ADDITION_TYPE_WITH_ROOM, $idsOrderDetail);
            $footer['service_products_price_tax_excl'] = $this->order->getTotalProductsWithoutTaxes(false, false, Product::SELLING_PREFERENCE_STANDALONE, false, false, $idsOrderDetail) + $this->order->getTotalProductsWithoutTaxes(false, false, Product::SELLING_PREFERENCE_HOTEL_STANDALONE, false, false, $idsOrderDetail);
            $serviceProductsCostTaxIncl = $this->order->getTotalProductsWithTaxes(false, false, Product::SELLING_PREFERENCE_STANDALONE, false, false, $idsOrderDetail) + $this->order->getTotalProductsWithTaxes(false, false, Product::SELLING_PREFERENCE_HOTEL_STANDALONE, false, false, $idsOrderDetail);
            $footer['additional_service_price_tax_excl'] = $this->order->getTotalProductsWithoutTaxes(false, false, Product::SELLING_PREFERENCE_WITH_ROOM_TYPE, 0, null, $idsOrderDetail) + $totalDemandsPriceTE;
            $additionalServicesCostTaxIncl = $this->order->getTotalProductsWithTaxes(false, false, Product::SELLING_PREFERENCE_WITH_ROOM_TYPE, 0, null, $idsOrderDetail) + $totalDemandsPriceTI;
            $footer['total_convenience_fee_te'] = $this->order->getTotalProductsWithoutTaxes(false, false, Product::SELLING_PREFERENCE_WITH_ROOM_TYPE, 1, Product::PRICE_ADDITION_TYPE_INDEPENDENT, $idsOrderDetail);
            $convenienceFeeTaxIncl = $this->order->getTotalProductsWithTaxes(false, false, Product::SELLING_PREFERENCE_WITH_ROOM_TYPE, 1, Product::PRICE_ADDITION_TYPE_INDEPENDENT, $idsOrderDetail);
        }

        $productDiscountsTaxIncl = (float)$this->order->total_discounts_tax_incl;
        foreach ($this->order->getCartRules() as $cartRule) {
            if (!empty($cartRule['free_shipping'])) {
                $productDiscountsTaxIncl -= (float)$this->order->total_shipping_tax_incl;
            }
        }
        if ($productDiscountsTaxIncl < 0) {
            $productDiscountsTaxIncl = 0;
        }

        $footer['total_without_discount_te'] = $footer['room_price_tax_excl'] + $footer['total_convenience_fee_te'] + $footer['additional_service_price_tax_excl'] + $footer['service_products_price_tax_excl'];
        $totalWithoutDiscountTaxIncl = $roomsCostTaxIncl + $serviceProductsCostTaxIncl + $additionalServicesCostTaxIncl + $convenienceFeeTaxIncl;
        $footer['total_tax_without_discount'] = max(0, $totalWithoutDiscountTaxIncl - $footer['total_without_discount_te']);
        $grandTotal = max(0.0, $totalWithoutDiscountTaxIncl - $productDiscountsTaxIncl);
        $footer['product_discounts_tax_incl'] = $productDiscountsTaxIncl;
        $footer['total_paid_tax_incl'] = $grandTotal;
        $footer['amount_due'] = Tools::ps_round(
            max(0, $grandTotal - (float)$this->order->getTotalPaid()),
            _PS_PRICE_COMPUTE_PRECISION_,
            $this->order->round_mode
        );

        $paymentMethods = array();
        $payments = $this->order->getOrderPaymentDetail();
        if (is_array($payments) && $payments) {
            foreach ($payments as $paymentDetail) {
                $method = isset($paymentDetail['payment_method']) ? trim($paymentDetail['payment_method']) : '';
                if (!$method) {
                    $method = $this->order->payment;
                }
                $amount = isset($paymentDetail['real_paid_amount'])
                    ? (float)$paymentDetail['real_paid_amount']
                    : (float)$paymentDetail['amount'];
                $idCurrency = isset($paymentDetail['id_currency'])
                    ? (int)$paymentDetail['id_currency']
                    : (int)$this->order->id_currency;
                $paymentMethods[] = $method.': '.Tools::displayPrice($amount, $idCurrency);
            }
        }
        $paymentMethod = $paymentMethods ? implode('<br /><br /> ', $paymentMethods) : $this->order->payment;

        $data = array(
            'order' => $this->order,
            'order_state' => $orderState,
            'booking_date' => Tools::displayDate($this->order->date_add, null, false),
            'issue_date' => Tools::displayDate(date('Y-m-d'), null, false),
            'hotel' => $objHotel,
            'hotel_address' => $hotelAddress,
            'guest' => $guest,
            'guest_address' => $guestAddress,
            'total_guests' => (int)$totalGuests,
            'total_rooms' => (int)$totalRooms,
            'total_room_nights' => (int)$totalRoomNights,
            'room_type_rows' => array_values($roomTypeRows),
            'service_groups' => $serviceGroups,
            'payment_method' => $paymentMethod,
            'footer' => $footer,
            'payment_policy' => $paymentPolicy,
            'cancellation_policy' => $cancellationPolicy,
        );

        if (Tools::getValue('debug')) {
            die(json_encode($data));
        }
        $this->smarty->assign($data);

        return $this->smarty->fetch($this->getTemplate('booking-voucher'));
    }

    /**
     * @return string
     */
    public function getBulkFilename()
    {
        return self::l('BookingVouchers').'.pdf';
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return sprintf(self::l('BOOKING VOUCHER - Refrence No. #%s.pdf'), $this->order->reference);
    }
}
