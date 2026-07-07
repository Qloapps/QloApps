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
    
if (!defined('_PS_VERSION_')) {
    exit;
}

class QloHotelReports extends Module
{
    public const REPORT_DEFAULT = 'reservation';

    /**
     * Flat report registry: each report is its own tab.
     * key => array('label' => translatable string, 'tpl' => template filename)
     * Labels are plain English; call $this->l($config['label']) at render time.
     */
    private static $reports = array(
        'reservation'      => array('label' => 'Reservations',         'tpl' => 'group-bookings.tpl'),
        'arrivals'         => array('label' => 'Arrivals',              'tpl' => 'group-bookings.tpl'),
        'in-house'         => array('label' => 'In-House',              'tpl' => 'group-bookings.tpl'),
        'departures'       => array('label' => 'Departures',            'tpl' => 'group-bookings.tpl'),
        'no-show'          => array('label' => 'No-Shows',              'tpl' => 'group-bookings.tpl'),
        'cancellation'     => array('label' => 'Cancellations',         'tpl' => 'group-bookings.tpl'),
        'revenue'          => array('label' => 'Revenue',               'tpl' => 'group-revenue.tpl'),
        'refund'           => array('label' => 'Refunds',               'tpl' => 'group-revenue.tpl'),
        'payment'          => array('label' => 'Payments',              'tpl' => 'group-revenue.tpl'),
        'tax'              => array('label' => 'Tax',                   'tpl' => 'group-revenue.tpl'),
        'outstanding'      => array('label' => 'Outstanding',           'tpl' => 'group-revenue.tpl'),
        'occupancy'        => array('label' => 'Occupancy',             'tpl' => 'group-occupancy.tpl'),
        'availability'     => array('label' => 'Availability',          'tpl' => 'group-occupancy.tpl'),
        'room-status'      => array('label' => 'Room Status',           'tpl' => 'group-occupancy.tpl'),
        'room-perf'        => array('label' => 'Room Type Performance', 'tpl' => 'group-occupancy.tpl'),
        'source'           => array('label' => 'Booking Source',        'tpl' => 'group-channels.tpl'),
        'payment-method'   => array('label' => 'Payment Methods',       'tpl' => 'group-channels.tpl'),
        'services'         => array('label' => 'Services',              'tpl' => 'group-guests.tpl'),
        'guest-directory'  => array('label' => 'Guest Directory',       'tpl' => 'group-guests.tpl'),
        'daily-summary'    => array('label' => 'Daily Summary',         'tpl' => 'group-property.tpl'),
        'hotel-comparison' => array('label' => 'Hotel Comparison',      'tpl' => 'group-property.tpl'),
        'out-of-order'     => array('label' => 'Out of Order Rooms',    'tpl' => 'group-property.tpl'),
    );

    public function __construct()
    {
        $this->name = 'qlohotelreports';
        $this->tab = 'analytics_stats';
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6');
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Hotel Reports');
        $this->description = $this->l('Comprehensive hotel reports: reservations, revenue, occupancy, channels, guests, and more.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('AdminStatsModules')
            && $this->registerHook('actionAdminControllerSetMedia');
    }

    /**
     * Returns sidebar tab definitions for AdminStatsTabController.
     * Each entry becomes a separate sidebar link in the Stats left nav.
     *
     * @return array  array of ['key' => string, 'label' => string]
     */
    public function getStatsTabs()
    {
        $tabs = array();
        foreach (self::$reports as $key => $config) {
            $tabs[] = array('key' => $key, 'label' => $this->l($config['label']));
        }
        return $tabs;
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (get_class($this->context->controller) == 'AdminStatsController') {
            $this->context->controller->addCSS($this->_path . 'views/css/' . $this->name . '.css');
            $this->context->controller->addJs($this->_path . 'views/js/' . $this->name . '.js');
        }
    }

    public function hookAdminStatsModules($params)
    {
        $report = Tools::getValue('tab', self::REPORT_DEFAULT);
        if (!array_key_exists($report, self::$reports)) {
            $report = self::REPORT_DEFAULT;
        }

        $dateFrom = $this->context->employee->stats_date_from;
        $dateTo   = $this->context->employee->stats_date_to;
        $idHotel  = (int) Tools::getValue('id_hotel', 0);
        $idLang   = (int) $this->context->language->id;
        $hotels   = (new HotelBranchInformation())->hotelsNameAndId();
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

        if (Tools::getValue('export')) {
            $bookingReports  = array('reservation', 'arrivals', 'departures', 'in-house', 'no-show', 'cancellation');
            $revenueReports  = array('revenue', 'refund', 'payment', 'tax', 'outstanding');
            $occupancyReports = array('occupancy', 'availability', 'room-status', 'room-perf');

            if (in_array($report, $bookingReports)) {
                $this->exportBookingsCsv($dateFrom, $dateTo, $idHotel, $report);
            } elseif (in_array($report, $revenueReports)) {
                $this->exportRevenueCsv($dateFrom, $dateTo, $idHotel, (int) Tools::getValue('id_product', 0), $report);
            } elseif (in_array($report, $occupancyReports)) {
                $this->exportOccupancyCsv($dateFrom, $dateTo, $idHotel, $idLang, $report);
            } elseif ($report === 'services' || $report === 'guest-directory') {
                $this->exportGuestsCsv($dateFrom, $dateTo, $idHotel, $report);
            } elseif ($report === 'source' || $report === 'payment-method') {
                $this->exportChannelsCsv($dateFrom, $dateTo, $idHotel, $report);
            } else {
                $this->exportPropertyCsv($dateFrom, $dateTo, $idHotel, $idLang, $report);
            }
            return '';
        }

        $baseUrl = $this->context->link->getAdminLink('AdminStats') . '&module=' . $this->name;

        $this->context->smarty->assign(array(
            'active_report'  => $report,
            'report_label'   => $this->l(self::$reports[$report]['label']),
            'date_from'      => $dateFrom,
            'date_to'        => $dateTo,
            'hotels'         => $hotels,
            'id_hotel'       => $idHotel,
            'currency_sign'  => $currency->sign,
            'currency_iso'   => $currency->iso_code,
            'group_tpl_path' => _PS_MODULE_DIR_ . $this->name . '/views/templates/hook/'
                . self::$reports[$report]['tpl'],
        ));

        if (in_array($report, array('reservation', 'arrivals', 'in-house', 'departures', 'no-show', 'cancellation'))) {
            $idProduct    = (int) Tools::getValue('id_product', 0);
            $idStatus     = (int) Tools::getValue('booking_status', 0);
            $bookingType  = (int) Tools::getValue('booking_type', 0);
            $idOrderState = (int) Tools::getValue('id_order_state', 0);

            $filterBaseUrl = $baseUrl . '&tab=' . $report
                . ($idHotel ? '&id_hotel=' . $idHotel : '')
                . ($idProduct ? '&id_product=' . $idProduct : '');

            $this->context->smarty->assign(array(
                'room_types'             => HotelRoomInformation::getRoomTypes(array(
                    'id_hotel' => $idHotel,
                    'id_lang'  => $idLang,
                )),
                'filter_id_product'      => $idProduct,
                'filter_booking_status'  => $idStatus,
                'filter_booking_type'    => $bookingType,
                'filter_id_order_state'  => $idOrderState,
                'booking_statuses'       => array(
                    HotelBookingDetail::STATUS_ALLOTED     => $this->l('Allotted'),
                    HotelBookingDetail::STATUS_CHECKED_IN  => $this->l('Checked In'),
                    HotelBookingDetail::STATUS_CHECKED_OUT => $this->l('Checked Out'),
                ),
                'booking_sources'        => array(
                    HotelBookingDetail::ALLOTMENT_AUTO   => $this->l('Online / Direct'),
                    HotelBookingDetail::ALLOTMENT_MANUAL => $this->l('Walk-in / Admin'),
                ),
                'order_states'           => OrderState::getOrderStates($idLang),
                'filter_base_url'        => $filterBaseUrl,
                'export_url'             => $filterBaseUrl
                    . ($idProduct    ? '&id_product='      . $idProduct    : '')
                    . ($idStatus     ? '&booking_status='  . $idStatus     : '')
                    . ($bookingType  ? '&booking_type='    . $bookingType  : '')
                    . ($idOrderState ? '&id_order_state='  . $idOrderState : '')
                    . '&export=1',
            ));

            $currencyMap = $this->currencySignMap();

            if ($report === 'reservation') {
                $reservations = HotelBookingDetail::getBookingsInfo(array(
                    'date_from'        => $dateFrom,
                    'date_to'          => $dateTo,
                    'id_hotel'         => $idHotel ?: false,
                    'id_product'       => $idProduct,
                    'id_status'        => $idStatus,
                    'booking_type'     => $bookingType,
                    'id_order_state' => $idOrderState,
                ));
                $this->attachCurrencyToRows($reservations, $currencyMap, $currency->sign, $currency->iso_code);

                $totals = array('nights' => 0, 'adults' => 0, 'children' => 0,
                    'total_price_tax_excl' => 0.0, 'tax_amount' => 0.0,
                    'total_price_tax_incl' => 0.0, 'balance_due' => 0.0);
                $countedOrderIds = array();
                foreach ($reservations as $row) {
                    $rate = (float) $row['conversion_rate'];
                    if ($rate <= 0) {
                        $rate = 1.0;
                    }
                    $totals['nights']               += (int) $row['nights'];
                    $totals['adults']               += (int) $row['adults'];
                    $totals['children']             += (int) $row['children'];
                    $totals['total_price_tax_excl'] += (float) $row['total_price_tax_excl'] / $rate;
                    $totals['tax_amount']           += (float) $row['tax_amount'] / $rate;
                    $totals['total_price_tax_incl'] += (float) $row['total_price_tax_incl'] / $rate;
                    $idOrder = (int) $row['id_order'];
                    if (!isset($countedOrderIds[$idOrder])) {
                        $countedOrderIds[$idOrder] = true;
                        $totals['balance_due'] += max(0.0, (float) $row['balance_due'] / $rate);
                    }
                }

                $this->context->smarty->assign(array(
                    'reservations'       => $reservations,
                    'reservation_totals' => $totals,
                ));
            } elseif ($report === 'cancellation') {
                $cancellations = HotelBookingDetail::getCancellations(array(
                    'date_from'     => $dateFrom,
                    'date_to'       => $dateTo,
                    'id_hotel'      => $idHotel ?: null,
                    'id_product'    => $idProduct,
                    'detailed_info' => true,
                ));
                $this->attachCurrencyToRows($cancellations, $currencyMap, $currency->sign, $currency->iso_code);

                $totalRefunded = 0.0;
                foreach ($cancellations as $row) {
                    $totalRefunded += (float) $row['refunded_amount'];
                }

                $this->context->smarty->assign(array(
                    'cancellations'  => $cancellations,
                    'total_refunded' => $totalRefunded,
                ));
            } elseif ($report === 'no-show') {
                $noShows = HotelBookingDetail::getArrivalsInfo(array(
                    'date_from'  => $dateFrom,
                    'date_to'    => $dateTo,
                    'id_hotel'   => $idHotel ?: false,
                    'id_product' => $idProduct,
                    'id_status'  => HotelBookingDetail::STATUS_ALLOTED,
                ));
                $this->attachCurrencyToRows($noShows, $currencyMap, $currency->sign, $currency->iso_code);

                $noShowTotals = array('los' => 0, 'adults' => 0, 'children' => 0, 'total_price_tax_incl' => 0.0);
                foreach ($noShows as $row) {
                    $rate = (float) $row['conversion_rate'];
                    if ($rate <= 0) { $rate = 1.0; }
                    $noShowTotals['los']                  += (int)   $row['los'];
                    $noShowTotals['adults']               += (int)   $row['adults'];
                    $noShowTotals['children']             += (int)   $row['children'];
                    $noShowTotals['total_price_tax_incl'] += (float) $row['total_price_tax_incl'] / $rate;
                }

                $this->context->smarty->assign(array(
                    'no_shows'       => $noShows,
                    'no_show_totals' => $noShowTotals,
                ));
            } elseif ($report === 'arrivals') {
                $arrivals = HotelBookingDetail::getArrivalsInfo(array(
                    'date_from'  => $dateFrom,
                    'date_to'    => $dateTo,
                    'id_hotel'   => $idHotel ?: false,
                    'id_product' => $idProduct,
                ));
                $this->attachCurrencyToRows($arrivals, $currencyMap, $currency->sign, $currency->iso_code);

                $arrivalTotals = array('adults' => 0, 'children' => 0, 'los' => 0, 'total_price_tax_incl' => 0.0);
                foreach ($arrivals as $row) {
                    $rate = (float) $row['conversion_rate'];
                    if ($rate <= 0) { $rate = 1.0; }
                    $arrivalTotals['adults']               += (int)   $row['adults'];
                    $arrivalTotals['children']             += (int)   $row['children'];
                    $arrivalTotals['los']                  += (int)   $row['los'];
                    $arrivalTotals['total_price_tax_incl'] += (float) $row['total_price_tax_incl'] / $rate;
                }

                $this->context->smarty->assign(array(
                    'arrivals'       => $arrivals,
                    'arrival_totals' => $arrivalTotals,
                ));
            } elseif ($report === 'departures') {
                $departures = HotelBookingDetail::getDeparturesInfo(array(
                    'date_from'  => $dateFrom,
                    'date_to'    => $dateTo,
                    'id_hotel'   => $idHotel ?: false,
                    'id_product' => $idProduct,
                ));
                $this->attachCurrencyToRows($departures, $currencyMap, $currency->sign, $currency->iso_code);

                $departureTotals = array('adults' => 0, 'children' => 0, 'los' => 0, 'total_price_tax_incl' => 0.0);
                foreach ($departures as $row) {
                    $rate = (float) $row['conversion_rate'];
                    if ($rate <= 0) { $rate = 1.0; }
                    $departureTotals['adults']               += (int)   $row['adults'];
                    $departureTotals['children']             += (int)   $row['children'];
                    $departureTotals['los']                  += (int)   $row['los'];
                    $departureTotals['total_price_tax_incl'] += (float) $row['total_price_tax_incl'] / $rate;
                }

                $this->context->smarty->assign(array(
                    'departures'      => $departures,
                    'departure_totals' => $departureTotals,
                ));
            } elseif ($report === 'in-house') {
                $inHouse = HotelBookingDetail::getInHouseInfo(array(
                    'date_from'  => $dateFrom,
                    'date_to'    => $dateTo,
                    'id_hotel'   => $idHotel ?: false,
                    'id_product' => $idProduct,
                ));
                $this->attachCurrencyToRows($inHouse, $currencyMap, $currency->sign, $currency->iso_code);

                $inHouseTotals = array('adults' => 0, 'children' => 0, 'los' => 0, 'total_price_tax_incl' => 0.0);
                foreach ($inHouse as $row) {
                    $rate = (float) $row['conversion_rate'];
                    if ($rate <= 0) { $rate = 1.0; }
                    $inHouseTotals['adults']               += (int)   $row['adults'];
                    $inHouseTotals['children']             += (int)   $row['children'];
                    $inHouseTotals['los']                  += (int)   $row['los'];
                    $inHouseTotals['total_price_tax_incl'] += (float) $row['total_price_tax_incl'] / $rate;
                }

                $this->context->smarty->assign(array(
                    'in_house'        => $inHouse,
                    'in_house_totals' => $inHouseTotals,
                ));
            }
        }

        if (in_array($report, array('revenue', 'refund', 'payment', 'tax', 'outstanding'))) {
            $idProduct         = (int) Tools::getValue('id_product', 0);
            $refundStatus      = (int) Tools::getValue('refund_status', 0);
            $outstandingStatus = (int) Tools::getValue('outstanding_status', 0);
            $paymentMethod     = pSQL(Tools::getValue('payment_method', ''));
            $idTax             = (int) Tools::getValue('id_tax', 0);
            $revenueSource     = Tools::getValue('revenue_source', 'all');
            if (!in_array($revenueSource, array('room', 'service', 'all'))) {
                $revenueSource = 'room';
            }
            $filterBaseUrl = $baseUrl . '&tab=' . $report
                . ($idHotel ? '&id_hotel=' . $idHotel : '')
                . ($idProduct ? '&id_product=' . $idProduct : '');

            $baseParams = array(
                'date_from'  => $dateFrom,
                'date_to'    => $dateTo,
                'id_hotel'   => $idHotel ?: false,
                'id_product' => $idProduct,
            );

            $this->context->smarty->assign(array(
                'filter_id_product'         => $idProduct,
                'filter_refund_status'      => $refundStatus,
                'filter_outstanding_status' => $outstandingStatus,
                'filter_payment_method'     => $paymentMethod,
                'payment_methods'           => OrderPayment::getDistinctPaymentMethods($idHotel ?: false),
                'payment_types'             => array(
                    OrderPayment::PAYMENT_TYPE_ONLINE         => $this->l('Online'),
                    OrderPayment::PAYMENT_TYPE_PAY_AT_HOTEL   => $this->l('Pay at Hotel'),
                    OrderPayment::PAYMENT_TYPE_REMOTE_PAYMENT => $this->l('Remote Payment'),
                ),
                'booking_statuses'          => array(
                    HotelBookingDetail::STATUS_ALLOTED     => $this->l('Allotted'),
                    HotelBookingDetail::STATUS_CHECKED_IN  => $this->l('Checked In'),
                    HotelBookingDetail::STATUS_CHECKED_OUT => $this->l('Checked Out'),
                ),
                'filter_id_tax'             => $idTax,
                'filter_revenue_source'     => $revenueSource,
                'tax_names'                 => Tax::getTaxes($idLang),
                'refund_states'             => OrderReturnState::getOrderReturnStates($idLang),
                'room_types'                => HotelRoomInformation::getRoomTypes(array(
                    'id_hotel' => $idHotel,
                    'id_lang'  => $idLang,
                )),
                'filter_base_url'           => $filterBaseUrl,
                'export_url'             => $filterBaseUrl
                    . '&id_product=' . $idProduct
                    . ($report === 'refund'       && $refundStatus      ? '&refund_status='      . $refundStatus                  : '')
                    . ($report === 'outstanding'  && $outstandingStatus ? '&outstanding_status=' . $outstandingStatus              : '')
                    . ($report === 'payment'      && $paymentMethod     ? '&payment_method='     . urlencode($paymentMethod)       : '')
                    . ($report === 'tax'          && $idTax             ? '&id_tax='             . $idTax                         : '')
                    . ($report === 'tax'                                ? '&revenue_source='     . urlencode($revenueSource)       : '')
                    . '&export=1',
            ));

            if ($report === 'revenue') {
                $dailyRoomDetailed  = HotelBookingDetail::getDatewiseRoomRevenueTax($baseParams);
                $dailyServiceData   = ServiceProductOrderDetail::getDatewiseServiceRevenueTax($baseParams);
                $rawDiscounts       = Order::getTotalDiscounts(array(
                    'date_from'   => $dateFrom,
                    'date_to'     => $dateTo,
                    'id_hotel'    => $idHotel ?: false,
                    'granularity' => 'day',
                ));
                $dailyDiscounts     = is_array($rawDiscounts) ? $rawDiscounts : array();
                $dailyBookings      = HotelBookingDetail::getTotalBookings(array(
                    'date_from'   => $dateFrom,
                    'date_to'     => $dateTo,
                    'id_hotel'    => $idHotel ?: false,
                    'id_product'  => $idProduct,
                    'granularity' => 'day',
                ));
                $dailyRoomsOccupied = HotelBookingDetail::getOccupiedRoomsForDiscreteDates($baseParams);

                $totalRoomsInventory = HotelRoomInformation::getTotalRooms($idHotel ?: null, 1);

                $dailyRows = array();
                $totals    = array(
                    'rooms_sold' => 0, 'bookings' => 0, 'room_revenue' => 0.0,
                    'service_revenue' => 0.0, 'discounts' => 0.0,
                    'tax_amount' => 0.0, 'refund_amount' => 0.0,
                    'total_collection' => 0.0, 'net_revenue' => 0.0,
                );

                foreach ($dailyRoomDetailed as $ts => $roomData) {
                    $svcDay  = isset($dailyServiceData[$ts]) ? $dailyServiceData[$ts] : array('service_revenue' => 0.0, 'tax_amount' => 0.0);
                    $roomRev = $roomData['room_revenue'];
                    $taxAmt  = $roomData['tax_amount'] + $svcDay['tax_amount'];
                    $svcRev  = $svcDay['service_revenue'];
                    $disc    = isset($dailyDiscounts[$ts])    ? $dailyDiscounts[$ts]    : 0.0;
                    $bkgs    = isset($dailyBookings[$ts])     ? $dailyBookings[$ts]     : 0;
                    $rooms   = isset($dailyRoomsOccupied[$ts]) ? $dailyRoomsOccupied[$ts] : 0;
                    $netRev  = $roomRev + $svcRev - $disc;
                    $totalCol = $netRev + $taxAmt;
                    $adrDay    = $rooms > 0 ? round($roomRev / $rooms, 2) : 0.0;
                    $revparDay = $totalRoomsInventory > 0 ? round($roomRev / $totalRoomsInventory, 2) : 0.0;
                    $occPct    = $totalRoomsInventory > 0 ? round($rooms / $totalRoomsInventory * 100, 1) : 0.0;

                    $dailyRows[] = array(
                        'date'             => date('Y-m-d', $ts),
                        'rooms_sold'       => $rooms,
                        'bookings'         => $bkgs,
                        'room_revenue'     => $roomRev,
                        'service_revenue'  => $svcRev,
                        'discounts'        => $disc,
                        'tax_amount'       => $taxAmt,
                        'refund_amount'    => 0.0,
                        'total_collection' => $totalCol,
                        'net_revenue'      => $netRev,
                        'adr'              => $adrDay,
                        'revpar'           => $revparDay,
                        'occupancy_pct'    => $occPct,
                    );

                    $totals['rooms_sold']        += $rooms;
                    $totals['bookings']          += $bkgs;
                    $totals['room_revenue']      += $roomRev;
                    $totals['service_revenue']   += $svcRev;
                    $totals['discounts']         += $disc;
                    $totals['tax_amount']        += $taxAmt;
                    $totals['total_collection']  += $totalCol;
                    $totals['net_revenue']       += $netRev;
                }

                $this->context->smarty->assign(array(
                    'daily_rows'     => $dailyRows,
                    'revenue_totals' => $totals,
                ));
            } elseif ($report === 'refund') {
                $refunds      = HotelBookingDetail::getCancellations(array(
                    'date_from'     => $dateFrom,
                    'date_to'       => $dateTo,
                    'id_hotel'      => $idHotel ?: null,
                    'id_product'    => $idProduct,
                    'detailed_info' => true,
                ));
                $revCurrencyMap = $this->currencySignMap();
                $this->attachCurrencyToRows($refunds, $revCurrencyMap, $currency->sign, $currency->iso_code);
                $totalRefunded = 0.0;
                foreach ($refunds as $row) {
                    $totalRefunded += (float) $row['refunded_amount'];
                }
                $refunds = array_filter($refunds, function ($r) { return (float) $r['refunded_amount'] > 0; });
                if ($refundStatus > 0) {
                    $refunds = array_filter($refunds, function ($r) use ($refundStatus) {
                        return (int) $r['refund_status'] === $refundStatus;
                    });
                }

                $this->context->smarty->assign(array(
                    'refunds'        => array_values($refunds),
                    'total_refunded' => $totalRefunded,
                ));
            } elseif ($report === 'payment') {
                $payments      = OrderPayment::getPaymentsInfo(
                    array_merge($baseParams, array('payment_method' => $paymentMethod))
                );
                $totalPayments = 0.0;
                foreach ($payments as $row) {
                    $rate = (float) $row['conversion_rate'];
                    if ($rate <= 0) { $rate = 1.0; }
                    $totalPayments += (float) $row['amount'] / $rate;
                }
                $this->context->smarty->assign(array(
                    'payments'       => $payments,
                    'total_payments' => $totalPayments,
                ));
            } elseif ($report === 'tax') {
                $taxRows     = HotelBookingDetail::getTaxBreakdown(array(
                    'date_from'      => $dateFrom,
                    'date_to'        => $dateTo,
                    'id_hotel'       => $idHotel ?: false,
                    'id_product'     => $idProduct,
                    'id_tax'         => $idTax,
                    'revenue_source' => $revenueSource,
                    'id_lang'        => $idLang,
                ));
                $taxByName   = array();
                $totalTax    = 0.0;
                $totalExcl   = 0.0;
                foreach ($taxRows as $row) {
                    $totalTax  += (float) $row['tax_amount'];
                    $totalExcl += (float) $row['taxable_amount'];
                    $name = $row['tax_name'] ? $row['tax_name'] : 'Unknown';
                    if (!isset($taxByName[$name])) {
                        $taxByName[$name] = array(
                            'tax_name'       => $name,
                            'tax_rate'       => (float) $row['tax_rate'],
                            'taxable_amount' => 0.0,
                            'tax_amount'     => 0.0,
                        );
                    }
                    $taxByName[$name]['taxable_amount'] += (float) $row['taxable_amount'];
                    $taxByName[$name]['tax_amount']     += (float) $row['tax_amount'];
                }
                $this->context->smarty->assign(array(
                    'tax_rows'      => $taxRows,
                    'tax_by_name'   => array_values($taxByName),
                    'tax_totals'    => array(
                        'taxable_amount' => $totalExcl,
                        'tax_amount'     => $totalTax,
                        'grand_total'    => $totalExcl + $totalTax,
                    ),
                ));
            } elseif ($report === 'outstanding') {
                $outstanding      = Order::getOutstandingBalance(
                    array_merge($baseParams, array('id_status' => $outstandingStatus))
                );
                $totalOutstanding = 0.0;
                foreach ($outstanding as $row) {
                    $totalOutstanding += (float) $row['balance_due'];
                }
                $this->context->smarty->assign(array(
                    'outstanding'       => $outstanding,
                    'total_outstanding' => $totalOutstanding,
                ));
            }
        }

        if (in_array($report, array('occupancy', 'availability', 'room-status', 'room-perf'))) {
            $idProduct     = (int) Tools::getValue('id_product', 0);
            $floor         = pSQL(Tools::getValue('floor', ''));
            $filterBaseUrl = $baseUrl . '&tab=' . $report
                . ($idHotel   ? '&id_hotel='   . $idHotel   : '')
                . ($idProduct ? '&id_product=' . $idProduct : '');


            $baseParams = array(
                'date_from'  => $dateFrom,
                'date_to'    => $dateTo,
                'id_hotel'   => $idHotel ?: false,
                'id_product' => $idProduct,
            );

            $this->context->smarty->assign(array(
                'filter_id_product'     => $idProduct,
                'filter_floor'          => $floor,
                'available_floors'      => HotelRoomInformation::getDistinctFloors(array(
                    'id_hotel'   => $idHotel,
                    'id_product' => $idProduct,
                )),
                'room_types'            => HotelRoomInformation::getRoomTypes(array(
                    'id_hotel' => $idHotel,
                    'id_lang'  => $idLang,
                )),
                'room_statuses'         => array(
                    HotelRoomInformation::STATUS_INACTIVE           => array('label' => $this->l('Out of Order'),      'class' => 'label-default'),
                    HotelRoomInformation::STATUS_TEMPORARY_INACTIVE => array('label' => $this->l('Under Maintenance'), 'class' => 'label-warning'),
                ),
                'filter_base_url'       => $filterBaseUrl,
                'export_url'            => $filterBaseUrl
                    . '&id_product=' . $idProduct
                    . ($floor ? '&floor=' . urlencode($floor) : '')
                    . '&export=1',
            ));

            if ($report === 'occupancy') {
                $occupancyData   = HotelRoomInformation::getOccupancyData($baseParams);
                $dailyOccupied   = HotelBookingDetail::getOccupiedRoomsForDiscreteDates($baseParams);
                $dailyBooked     = HotelBookingDetail::getOccupiedRoomsForDiscreteDates(
                    array_merge($baseParams, array('id_status' => HotelBookingDetail::STATUS_ALLOTED))
                );
                $dailyCheckedIn  = HotelBookingDetail::getOccupiedRoomsForDiscreteDates(
                    array_merge($baseParams, array('id_status' => HotelBookingDetail::STATUS_CHECKED_IN))
                );
                $dailyAvailable  = HotelRoomInformation::getAvailableRoomsForDiscreteDates($baseParams);
                $dailyRevenue    = HotelBookingDetail::getRoomRevenueForDiscreteDates($baseParams);
                $dailyAdr        = HotelBookingDetail::getDatewiseAverageDailyRate($baseParams);
                $numNights       = max(1, (int)(new DateTime($dateTo))->diff(new DateTime($dateFrom))->days);
                $totalRooms      = $numNights ? (int) round($occupancyData['count_total'] / $numNights) : 0;

                $dailyRows = array();
                foreach ($dailyOccupied as $ts => $occupied) {
                    $available    = isset($dailyAvailable[$ts])  ? (int) $dailyAvailable[$ts]      : 0;
                    $unavailable  = max(0, $totalRooms - $occupied - $available);
                    $revenue      = isset($dailyRevenue[$ts])    ? (float) $dailyRevenue[$ts]       : 0.0;
                    $adr          = isset($dailyAdr[$ts])        ? round((float) $dailyAdr[$ts], 2) : 0.0;
                    $revpar       = $totalRooms ? round($revenue / $totalRooms, 2)                  : 0.0;
                    $dailyRows[] = array(
                        'date'              => date('Y-m-d', $ts),
                        'total'             => $totalRooms,
                        'available'         => $available,
                        'rooms_booked'      => isset($dailyBooked[$ts])    ? (int) $dailyBooked[$ts]    : 0,
                        'rooms_occupied'    => isset($dailyCheckedIn[$ts]) ? (int) $dailyCheckedIn[$ts] : 0,
                        'out_of_order'      => $unavailable,
                        'occupancy_pct'     => $totalRooms ? round($occupied / $totalRooms * 100, 1) : 0.0,
                        'adr'               => $adr,
                        'revpar'            => $revpar,
                        'total_room_revenue'=> round($revenue, 2),
                    );
                }

                $this->context->smarty->assign(array(
                    'daily_rows' => $dailyRows,
                ));
            } elseif ($report === 'availability') {
                $availabilityRows = HotelRoomInformation::getAvailabilityReport(array_merge($baseParams, array(
                    'id_product' => (int) Tools::getValue('id_product', 0),
                    'id_lang'    => $idLang,
                )));

                $this->context->smarty->assign(array(
                    'availability_rows' => $availabilityRows,
                ));
            } elseif ($report === 'room-status') {
                $showHousekeeping = Module::isInstalled('qlohousekeeping') && Module::isEnabled('qlohousekeeping');
                $housekeepingStatuses = array();
                if ($showHousekeeping) {
                    $hkStatus = new QhkHouseKeepingTaskStatus();
                    $housekeepingStatuses = $hkStatus->getHousekeepingTaskStatusList(false, $idLang);
                }

                $perPage  = 10;
                $page     = max(1, (int) Tools::getValue('rooms_page', 1));
                $allRooms = HotelRoomInformation::getRoomStatusForReports(array(
                    'date_from'              => $dateFrom,
                    'date_to'                => $dateTo,
                    'id_hotel'               => $idHotel ?: false,
                    'id_product'             => $idProduct,
                    'floor'                  => $floor,
                    'id_lang'                => $idLang,
                    'housekeeping_installed' => $showHousekeeping,
                ));
                $roomsTotal      = count($allRooms);
                $roomsTotalPages = max(1, (int) ceil($roomsTotal / $perPage));
                $page            = min($page, $roomsTotalPages);
                $kpiOccupied     = 0;
                $kpiOutOfOrder          = 0;
                foreach ($allRooms as $room) {
                    if ($room['id_order']) {
                        $kpiOccupied++;
                    } elseif (in_array((int) $room['id_status'], array(
                        HotelRoomInformation::STATUS_INACTIVE,
                        HotelRoomInformation::STATUS_TEMPORARY_INACTIVE,
                    ))) {
                        $kpiOutOfOrder++;
                    }
                }
                $this->context->smarty->assign(array(
                    'rooms'                => array_slice($allRooms, ($page - 1) * $perPage, $perPage),
                    'rooms_page'           => $page,
                    'rooms_total_pages'    => $roomsTotalPages,
                    'rooms_total'          => $roomsTotal,
                    'rooms_page_base_url'  => $filterBaseUrl . ($floor ? '&floor=' . urlencode($floor) : '') . '&rooms_page=',
                    'rooms_offset_start'   => $roomsTotal ? ($page - 1) * $perPage + 1 : 0,
                    'rooms_offset_end'     => min($page * $perPage, $roomsTotal),
                    'kpi_total'            => $roomsTotal,
                    'kpi_occupied'         => $kpiOccupied,
                    'kpi_available'        => $roomsTotal - $kpiOccupied - $kpiOutOfOrder,
                    'kpi_out_of_order'             => $kpiOutOfOrder,
                    'show_housekeeping'    => $showHousekeeping,
                    'housekeeping_statuses' => $housekeepingStatuses,
                ));
            } elseif ($report === 'room-perf') {
                $roomTypePerformanceRows = HotelRoomType::getRoomTypePerformance(array(
                    'date_from'  => $dateFrom,
                    'date_to'    => $dateTo,
                    'id_hotel'   => $idHotel ?: false,
                    'id_product' => $idProduct,
                    'id_lang'    => $idLang,
                ));
                $this->context->smarty->assign(array(
                    'roomTypePerformance_rows' => $roomTypePerformanceRows,
                ));
            }
        }

        if ($report === 'services' || $report === 'guest-directory') {
            $guestType        = pSQL(Tools::getValue('guest_type', ''));
            $idCategory       = (int) Tools::getValue('id_category', 0);
            $idServiceProduct = (int) Tools::getValue('id_service_product', 0);
            $filterBaseUrl    = $baseUrl . '&tab=' . $report
                . ($idHotel         ? '&id_hotel='          . $idHotel         : '')
                . ($guestType       ? '&guest_type='        . urlencode($guestType) : '');


            $baseParams = array(
                'date_from' => $dateFrom,
                'date_to'   => $dateTo,
                'id_hotel'  => $idHotel ?: false,
                'id_lang'   => $idLang,
            );

            $this->context->smarty->assign(array(
                'filter_base_url'       => $filterBaseUrl,
                'filter_guest_type'     => $guestType,
                'filter_id_category'    => $idCategory,
                'filter_id_service'     => $idServiceProduct,
                'service_categories'    => ServiceProductOrderDetail::getServiceCategories($idLang),
                'service_products'      => ServiceProductOrderDetail::getServiceProducts($idLang, $idCategory),
                'export_url'            => $filterBaseUrl
                    . ($report === 'services' && $idCategory       ? '&id_category='        . $idCategory                    : '')
                    . ($report === 'services' && $idServiceProduct ? '&id_service_product=' . $idServiceProduct              : '')
                    . ($report === 'guest-directory' && $guestType ? '&guest_type='         . urlencode($guestType)          : '')
                    . '&export=1',
            ));

            if ($report === 'services') {
                $serviceRows = ServiceProductOrderDetail::getServicesInfo(array_merge($baseParams, array(
                    'id_category'        => $idCategory,
                    'id_service_product' => $idServiceProduct,
                )));
                $totalExcl   = 0.0;
                $totalTax    = 0.0;
                $totalIncl   = 0.0;
                foreach ($serviceRows as $row) {
                    $totalExcl += (float) $row['total_price_tax_excl'];
                    $totalTax  += (float) $row['tax_amount'];
                    $totalIncl += (float) $row['total_price_tax_incl'];
                }
                $this->context->smarty->assign(array(
                    'service_rows' => $serviceRows,
                    'service_totals' => array(
                        'total_excl' => $totalExcl,
                        'total_tax'  => $totalTax,
                        'total_incl' => $totalIncl,
                    ),
                ));
            } elseif ($report === 'guest-directory') {
                $guests = HotelBookingDetail::getGuestDirectory($baseParams);
                if ($guestType === 'new') {
                    $guests = array_values(array_filter($guests, function ($g) {
                        return (int) $g['total_stays'] === 1;
                    }));
                } elseif ($guestType === 'returning') {
                    $guests = array_values(array_filter($guests, function ($g) {
                        return (int) $g['total_stays'] > 1;
                    }));
                }
                $this->context->smarty->assign(array(
                    'guests' => $guests,
                ));
            }
        }

        if ($report === 'source' || $report === 'payment-method') {
            $bookingType = (int) Tools::getValue('booking_type', 0);
            $filterBaseUrl = $baseUrl . '&tab=' . $report
                . ($idHotel    ? '&id_hotel='    . $idHotel    : '')
                . ($bookingType ? '&booking_type=' . $bookingType : '');


            $baseParams = array(
                'date_from'    => $dateFrom,
                'date_to'      => $dateTo,
                'id_hotel'     => $idHotel ?: false,
                'booking_type' => $bookingType,
            );

            $this->context->smarty->assign(array(
                'filter_base_url'     => $filterBaseUrl,
                'filter_booking_type' => $bookingType,
                'export_url'          => $filterBaseUrl . '&export=1',
            ));

            if ($report === 'source') {
                $sourceRows = HotelBookingDetail::getChannelStats($baseParams);
                $sourceTotals = array(
                    'bookings' => 0, 'room_nights' => 0,
                    'revenue_excl' => 0.0, 'revenue_incl' => 0.0,
                    'discount_amount' => 0.0, 'refund_amount' => 0.0,
                    'tax_amount' => 0.0, 'cancellations' => 0,
                );
                foreach ($sourceRows as $row) {
                    $sourceTotals['bookings']        += (int) $row['bookings'];
                    $sourceTotals['room_nights']      += (int) $row['room_nights'];
                    $sourceTotals['revenue_excl']     += (float) $row['revenue_excl'];
                    $sourceTotals['revenue_incl']     += (float) $row['revenue_incl'];
                    $sourceTotals['discount_amount']  += (float) $row['discount_amount'];
                    $sourceTotals['refund_amount']    += (float) $row['refund_amount'];
                    $sourceTotals['tax_amount']       += (float) $row['tax_amount'];
                    $sourceTotals['cancellations']    += (int) $row['cancellations'];
                }
                $this->context->smarty->assign(array(
                    'source_rows'    => $sourceRows,
                    'source_totals'  => $sourceTotals,
                ));
            } elseif ($report === 'payment-method') {
                $paymentRows = HotelBookingDetail::getPaymentMethodStats($baseParams);
                $totalBookings = 0;
                $totalRevenue  = 0.0;
                foreach ($paymentRows as $row) {
                    $totalBookings += (int) $row['bookings'];
                    $totalRevenue  += (float) $row['revenue_incl'];
                }
                $this->context->smarty->assign(array(
                    'payment_rows'   => $paymentRows,
                    'total_bookings' => $totalBookings,
                    'total_revenue'  => $totalRevenue,
                ));
            }
        }

        if (in_array($report, array('daily-summary', 'hotel-comparison', 'out-of-order'))) {
            $idProduct = (int) Tools::getValue('id_product', 0);
            $floor     = pSQL(Tools::getValue('floor', ''));
            $filterBaseUrl = $baseUrl . '&tab=' . $report
                . ($idHotel    ? '&id_hotel='   . $idHotel    : '')
                . ($idProduct  ? '&id_product=' . $idProduct  : '');


            $baseParams = array(
                'date_from'  => $dateFrom,
                'date_to'    => $dateTo,
                'id_hotel'   => $idHotel ?: false,
                'id_product' => $idProduct,
            );

            $this->context->smarty->assign(array(
                'filter_base_url'      => $filterBaseUrl,
                'filter_id_product'    => $idProduct,
                'filter_floor'         => $floor,
                'available_floors'     => HotelRoomInformation::getDistinctFloors(array(
                    'id_hotel'   => $idHotel,
                    'id_product' => $idProduct,
                )),
                'room_types'           => HotelRoomInformation::getRoomTypes(array(
                    'id_hotel' => $idHotel,
                    'id_lang'  => $idLang,
                )),
                'room_statuses'        => array(
                    HotelRoomInformation::STATUS_INACTIVE           => array('label' => $this->l('Out of Order'),      'class' => 'label-default'),
                    HotelRoomInformation::STATUS_TEMPORARY_INACTIVE => array('label' => $this->l('Under Maintenance'), 'class' => 'label-warning'),
                ),
                'export_url'           => $filterBaseUrl
                    . ($floor ? '&floor=' . urlencode($floor) : '')
                    . '&export=1',
            ));

            if ($report === 'daily-summary') {
                $bookingsByDay    = HotelBookingDetail::getDatewiseBookings($baseParams);
                $arrivalsByDay    = HotelBookingDetail::getDatewiseArrivals($baseParams);
                $departuresByDay  = HotelBookingDetail::getDatewiseDepartures($baseParams);
                $cancsByDay       = HotelBookingDetail::getDatewiseCancellations($baseParams);
                $occupancyByDay   = HotelBookingDetail::getDatewiseOccupancyRate($baseParams);
                $adrByDay         = HotelBookingDetail::getDatewiseAverageDailyRate($baseParams);
                $revparByDay      = HotelBookingDetail::getDatewiseRevPAR($baseParams);
                $revenueByDay     = HotelBookingDetail::getDatewiseRoomRevenue($baseParams);
                $roomsSoldByDay   = HotelBookingDetail::getOccupiedRoomsForDiscreteDates($baseParams);
                $checkedInByDay   = HotelBookingDetail::getOccupiedRoomsForDiscreteDates(
                    array_merge($baseParams, array('id_status' => HotelBookingDetail::STATUS_CHECKED_IN))
                );
                $totalRooms       = HotelRoomInformation::getTotalRooms($idHotel ?: null, 1);

                $dailyRows = array();
                $current   = $dateFrom;
                while ($current <= $dateTo) {
                    $ts = strtotime($current);
                    $dailyRows[] = array(
                        'date'          => $current,
                        'total_rooms'   => $totalRooms,
                        'rooms_sold'    => isset($roomsSoldByDay[$ts])   ? (int)   $roomsSoldByDay[$ts]   : 0,
                        'occupancy'     => isset($occupancyByDay[$ts])   ? (float) $occupancyByDay[$ts] * 100 : 0.0,
                        'adr'           => isset($adrByDay[$ts])         ? (float) $adrByDay[$ts]         : 0.0,
                        'revpar'        => isset($revparByDay[$ts])      ? (float) $revparByDay[$ts]      : 0.0,
                        'revenue'       => isset($revenueByDay[$ts])     ? (float) $revenueByDay[$ts]     : 0.0,
                        'arrivals'      => isset($arrivalsByDay[$ts])    ? (int)   $arrivalsByDay[$ts]    : 0,
                        'departures'    => isset($departuresByDay[$ts])  ? (int)   $departuresByDay[$ts]  : 0,
                        'inhouse_guests'=> isset($checkedInByDay[$ts])   ? (int)   $checkedInByDay[$ts]   : 0,
                        'cancels'       => isset($cancsByDay[$ts])       ? (int)   $cancsByDay[$ts]       : 0,
                        'bookings'      => isset($bookingsByDay[$ts])    ? (int)   $bookingsByDay[$ts]    : 0,
                    );
                    $current = date('Y-m-d', strtotime('+1 day', strtotime($current)));
                }
                $this->context->smarty->assign(array('daily_rows' => $dailyRows));

            } elseif ($report === 'hotel-comparison') {
                $hotels     = (new HotelBranchInformation())->hotelsNameAndId();
                $hotelRows  = array();
                foreach ($hotels as $hotel) {
                    $hotelId     = (int) $hotel['id'];
                    $hotelParams = array_merge($baseParams, array('id_hotel' => $hotelId));
                    $bookings    = HotelBookingDetail::getTotalBookings($hotelParams);
                    $cancels     = HotelBookingDetail::getTotalCancellations($hotelParams);
                    $roomNights  = HotelBookingDetail::getTotalRoomNights($hotelParams);
                    $roomRevenue = HotelBookingDetail::getTotalRoomRevenue($hotelParams);
                    $extraRev    = ServiceProductOrderDetail::getTotalServiceRevenue($hotelParams);
                    $obRows      = Order::getOutstandingBalance($hotelParams);
                    $hotelRows[] = array(
                        'hotel_name'          => $hotel['hotel_name'],
                        'total_rooms'         => HotelRoomInformation::getTotalRooms($hotelId, 1),
                        'rooms_sold'          => $roomNights,
                        'occupancy'           => HotelBookingDetail::getOccupancyRate($hotelParams) * 100,
                        'room_revenue'        => $roomRevenue,
                        'extra_service_rev'   => $extraRev,
                        'gross_revenue'       => $roomRevenue + $extraRev,
                        'adr'                 => HotelBookingDetail::getAverageDailyRate($hotelParams),
                        'revpar'              => HotelBookingDetail::getRevPAR($hotelParams),
                        'bookings'            => $bookings,
                        'cancellations'       => $cancels,
                        'cancel_rate_pct'     => $bookings > 0 ? round($cancels / $bookings * 100, 1) : 0.0,
                        'no_shows'            => HotelBookingDetail::getTotalNoShows($hotelParams),
                        'avg_los'             => ($bookings > 0 && $roomNights > 0) ? round($roomNights / $bookings, 1) : 0.0,
                        'outstanding_balance' => $obRows ? array_sum(array_column($obRows, 'balance_due')) : 0.0,
                    );
                }
                $this->context->smarty->assign(array('hotel_rows' => $hotelRows));

            } elseif ($report === 'out-of-order') {
                $outOfOrderRows = HotelRoomDisableDates::getDisabledRooms(
                    array_merge($baseParams, array('id_lang' => $idLang, 'floor' => $floor))
                );
                $this->context->smarty->assign(array('outOfOrder_rows' => $outOfOrderRows));
            }
        }

        return $this->display(__FILE__, 'admin-stats-modules.tpl');
    }

    /**
     * Return array of currency data indexed by id_currency.
     * Loaded once; used to attach currency_sign/currency_iso per booking row.
     */
    private function currencySignMap()
    {
        $map  = array();
        $rows = Currency::getCurrencies(false, false, true);
        foreach ($rows as $row) {
            $map[(int) $row['id_currency']] = array(
                'sign'     => $row['sign'],
                'iso_code' => $row['iso_code'],
            );
        }
        return $map;
    }

    /**
     * Attach currency_sign and currency_iso to each row using id_currency field.
     * Falls back to the default store currency when id_currency is not in the map.
     */
    private function attachCurrencyToRows(array &$rows, array $currencyMap, $defaultSign, $defaultIso)
    {
        foreach ($rows as &$row) {
            $idCur = (int) $row['id_currency'];
            $row['currency_sign'] = isset($currencyMap[$idCur]) ? $currencyMap[$idCur]['sign']     : $defaultSign;
            $row['currency_iso']  = isset($currencyMap[$idCur]) ? $currencyMap[$idCur]['iso_code'] : $defaultIso;
        }
        unset($row);
    }

    /**
     * Stream CSV for a Bookings sub-report and exit.
     */
    private function exportBookingsCsv($dateFrom, $dateTo, $idHotel, $report)
    {
        header('Content-Type: text/csv; charset=utf-8');
        $idProduct       = (int) Tools::getValue('id_product', 0);
        $defaultCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $csvCurrencyMap  = $this->currencySignMap();

        if ($report === 'no-show') {
            $rows = HotelBookingDetail::getArrivalsInfo(array(
                'date_from'  => $dateFrom,
                'date_to'    => $dateTo,
                'id_hotel'   => $idHotel ?: false,
                'id_product' => $idProduct,
                'id_status'  => HotelBookingDetail::STATUS_ALLOTED,
            ));
            $this->attachCurrencyToRows($rows, $csvCurrencyMap, $defaultCurrency->sign, $defaultCurrency->iso_code);
            header('Content-Disposition: attachment; filename="no-show-report-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Booking ID'), $this->l('Guest Name'), $this->l('Room Type'),
                $this->l('Room No.'), $this->l('Check-in Date'),
                $this->l('Total Amount'), $this->l('Penalty Charged'), $this->l('Currency'),
            ));
            foreach ($rows as $row) {
                fputcsv($out, array(
                    $row['id_order'], $row['customer_name'],
                    $row['room_type_name'], $row['room_num'], $row['actual_checkin'],
                    number_format((float) $row['total_price_tax_incl'], 2, '.', ''),
                    '',
                    $row['currency_iso'],
                ));
            }
            fclose($out);
            exit;
        }

        if ($report === 'arrivals' || $report === 'departures') {
            if ($report === 'arrivals') {
                $rows = HotelBookingDetail::getArrivalsInfo(array(
                    'date_from'  => $dateFrom,
                    'date_to'    => $dateTo,
                    'id_hotel'   => $idHotel ?: false,
                    'id_product' => $idProduct,
                ));
                $filename = 'arrivals-report-'.$dateFrom.'-to-'.$dateTo.'.csv';
            } else {
                $rows = HotelBookingDetail::getDeparturesInfo(array(
                    'date_from'  => $dateFrom,
                    'date_to'    => $dateTo,
                    'id_hotel'   => $idHotel ?: false,
                    'id_product' => $idProduct,
                ));
                $filename = 'departures-report-'.$dateFrom.'-to-'.$dateTo.'.csv';
            }
            $this->attachCurrencyToRows($rows, $csvCurrencyMap, $defaultCurrency->sign, $defaultCurrency->iso_code);
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Order ID'), $this->l('Guest'), $this->l('Hotel'),
                $this->l('Room Type'), $this->l('Room No.'), $this->l('Check-in'),
                $this->l('Check-out'), $this->l('Nights'), $this->l('Adults'),
                $this->l('Children'), $this->l('Total (incl. Tax)'), $this->l('Status'),
                $this->l('Currency'),
            ));
            $statusLabels = array(
                HotelBookingDetail::STATUS_ALLOTED     => $this->l('Allotted'),
                HotelBookingDetail::STATUS_CHECKED_IN  => $this->l('Checked In'),
                HotelBookingDetail::STATUS_CHECKED_OUT => $this->l('Checked Out'),
            );
            foreach ($rows as $row) {
                fputcsv($out, array(
                    $row['id_order'], $row['customer_name'], $row['hotel_name'],
                    $row['room_type_name'], $row['room_num'], $row['actual_checkin'],
                    $row['actual_checkout'], $row['los'], $row['adults'], $row['children'],
                    $row['total_price_tax_incl'],
                    isset($statusLabels[$row['id_status']]) ? $statusLabels[$row['id_status']] : $row['id_status'],
                    $row['currency_iso'],
                ));
            }
            fclose($out);
            exit;
        }

        if ($report === 'in-house') {
            $rows = HotelBookingDetail::getInHouseInfo(array(
                'date_from'  => $dateFrom,
                'date_to'    => $dateTo,
                'id_hotel'   => $idHotel ?: false,
                'id_product' => $idProduct,
            ));
            $this->attachCurrencyToRows($rows, $csvCurrencyMap, $defaultCurrency->sign, $defaultCurrency->iso_code);
            header('Content-Disposition: attachment; filename="in-house-report-'.date('Y-m-d').'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Order ID'), $this->l('Guest'), $this->l('Hotel'),
                $this->l('Room Type'), $this->l('Room No.'), $this->l('Check-in'),
                $this->l('Check-out'), $this->l('Nights'), $this->l('Adults'),
                $this->l('Children'), $this->l('Total (incl. Tax)'), $this->l('Currency'),
            ));
            foreach ($rows as $row) {
                fputcsv($out, array(
                    $row['id_order'], $row['customer_name'], $row['hotel_name'],
                    $row['room_type_name'], $row['room_num'], $row['actual_checkin'],
                    $row['actual_checkout'], $row['los'], $row['adults'],
                    $row['children'], $row['total_price_tax_incl'], $row['currency_iso'],
                ));
            }
            fclose($out);
            exit;
        }

        if ($report === 'cancellation') {
            $rows = HotelBookingDetail::getCancellations(array(
                'date_from'     => $dateFrom,
                'date_to'       => $dateTo,
                'id_hotel'      => $idHotel ?: null,
                'id_product'    => (int) Tools::getValue('id_product', 0),
                'detailed_info' => true,
            ));
            $this->attachCurrencyToRows($rows, $csvCurrencyMap, $defaultCurrency->sign, $defaultCurrency->iso_code);
            header('Content-Disposition: attachment; filename="cancellation-report-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Booking ID'),
                $this->l('Guest Name'),
                $this->l('Room Type'),
                $this->l('Room No.'),
                $this->l('Check-in Date'),
                $this->l('Cancellation Date'),
                $this->l('Cancellation Reason'),
                $this->l('Cancellation Remark'),
                $this->l('Refund Amount'),
                $this->l('Refund Status'),
                $this->l('Booking Date'),
                $this->l('Currency'),
            ));
            foreach ($rows as $row) {
                fputcsv($out, array(
                    $row['id_order'],
                    $row['customer_name'],
                    $row['room_type_name'],
                    $row['room_num'],
                    $row['hotel_check_in'],
                    isset($row['cancellation_date']) ? $row['cancellation_date'] : '',
                    isset($row['cancellation_reason']) ? $row['cancellation_reason'] : '',
                    '',
                    isset($row['refunded_amount']) ? number_format((float) $row['refunded_amount'], 2, '.', '') : '0.00',
                    isset($row['refund_status']) ? $row['refund_status'] : '',
                    isset($row['booking_date']) ? $row['booking_date'] : '',
                    $row['currency_iso'],
                ));
            }
        } else {
            $idStatus     = (int) Tools::getValue('booking_status', 0);
            $bookingType  = (int) Tools::getValue('booking_type', 0);
            $idOrderState = (int) Tools::getValue('id_order_state', 0);

            $rows = HotelBookingDetail::getBookingsInfo(array(
                'date_from'        => $dateFrom,
                'date_to'          => $dateTo,
                'id_hotel'         => $idHotel ?: false,
                'id_product'       => $idProduct,
                'id_status'        => $idStatus,
                'booking_type'     => $bookingType,
                'id_order_state' => $idOrderState,
            ));
            $this->attachCurrencyToRows($rows, $csvCurrencyMap, $defaultCurrency->sign, $defaultCurrency->iso_code);

            $statusLabels = array(
                HotelBookingDetail::STATUS_ALLOTED     => $this->l('Allotted'),
                HotelBookingDetail::STATUS_CHECKED_IN  => $this->l('Checked In'),
                HotelBookingDetail::STATUS_CHECKED_OUT => $this->l('Checked Out'),
            );
            $sourceLabels = array(
                HotelBookingDetail::ALLOTMENT_AUTO   => $this->l('Online / Direct'),
                HotelBookingDetail::ALLOTMENT_MANUAL => $this->l('Walk-in / Admin'),
            );

            header('Content-Disposition: attachment; filename="reservation-report-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Reservation ID'),
                $this->l('Guest Name'),
                $this->l('Guest Contact'),
                $this->l('Room Type'),
                $this->l('Room No.'),
                $this->l('Check-in Date'),
                $this->l('Check-out Date'),
                $this->l('Nights'),
                $this->l('Adults'),
                $this->l('Children'),
                $this->l('Room Rate Per Night'),
                $this->l('Booking Source'),
                $this->l('Booking Status'),
                $this->l('Total (excl. Tax)'),
                $this->l('Tax Amount'),
                $this->l('Grand Total'),
                $this->l('Balance Due'),
                $this->l('Payment Status'),
                $this->l('Created By'),
                $this->l('Booking Date'),
                $this->l('Currency'),
            ));
            foreach ($rows as $row) {
                $balanceDue    = max(0.0, (float) $row['order_total'] - (float) $row['order_paid']);
                if ((float) $row['order_paid'] <= 0) {
                    $paymentStatus = $this->l('Pending');
                } elseif ($balanceDue > 0) {
                    $paymentStatus = $this->l('Partial');
                } else {
                    $paymentStatus = $this->l('Paid');
                }
                fputcsv($out, array(
                    $row['id_order'],
                    $row['customer_name'],
                    isset($row['phone']) ? $row['phone'] : '',
                    $row['room_type_name'],
                    $row['room_num'],
                    $row['hotel_check_in'],
                    $row['hotel_check_out'],
                    $row['nights'],
                    $row['adults'],
                    $row['children'],
                    number_format((float) $row['unit_price_tax_excl'], 2, '.', ''),
                    isset($sourceLabels[$row['booking_type']]) ? $sourceLabels[$row['booking_type']] : $row['booking_type'],
                    isset($statusLabels[$row['id_status']]) ? $statusLabels[$row['id_status']] : $row['id_status'],
                    number_format((float) $row['total_price_tax_excl'], 2, '.', ''),
                    number_format((float) $row['tax_amount'], 2, '.', ''),
                    number_format((float) $row['total_price_tax_incl'], 2, '.', ''),
                    number_format($balanceDue, 2, '.', ''),
                    $paymentStatus,
                    isset($row['created_by']) ? $row['created_by'] : '',
                    isset($row['date_add']) ? $row['date_add'] : '',
                    $row['currency_iso'],
                ));
            }
        }

        fclose($out);
        exit;
    }

    /**
     * Stream CSV for a Revenue & Finance sub-report and exit.
     */
    private function exportRevenueCsv($dateFrom, $dateTo, $idHotel, $idProduct, $report)
    {
        $currencyIso = (new Currency(Configuration::get('PS_CURRENCY_DEFAULT')))->iso_code;
        $baseParams  = array(
            'date_from'  => $dateFrom,
            'date_to'    => $dateTo,
            'id_hotel'   => $idHotel ?: false,
            'id_product' => $idProduct,
        );

        header('Content-Type: text/csv; charset=utf-8');

        if ($report === 'payment') {
            $csvPaymentMethod = pSQL(Tools::getValue('payment_method', ''));
            $rows = OrderPayment::getPaymentsInfo(
                array_merge($baseParams, array('payment_method' => $csvPaymentMethod))
            );
            header('Content-Disposition: attachment; filename="payment-report-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Date'), $this->l('Order ID'), $this->l('Reference'),
                $this->l('Guest Name'), $this->l('Payment Method'), $this->l('Payment Type'),
                $this->l('Amount'), $this->l('Currency'), $this->l('Transaction ID'),
                $this->l('Payment Status'),
            ));
            foreach ($rows as $row) {
                $rate = isset($row['conversion_rate']) ? (float) $row['conversion_rate'] : 1.0;
                if ($rate <= 0) { $rate = 1.0; }
                fputcsv($out, array(
                    isset($row['date_add']) ? $row['date_add'] : '',
                    $row['id_order'],
                    $row['reference'],
                    $row['customer_name'],
                    $row['payment_method'],
                    isset($row['payment_type']) ? $row['payment_type'] : '',
                    number_format((float) $row['amount'] / $rate, 2, '.', ''),
                    $currencyIso,
                    isset($row['transaction_id']) ? $row['transaction_id'] : '',
                    $this->l('Success'),
                ));
            }
        } elseif ($report === 'refund') {
            $rows = HotelBookingDetail::getCancellations(array(
                'date_from'  => $dateFrom,
                'date_to'    => $dateTo,
                'id_hotel'   => $idHotel ?: null,
                'id_product' => $idProduct,
                'detailed_info' => true,
            ));
            $rows = array_filter($rows, function ($r) { return (float) $r['refunded_amount'] > 0; });
            header('Content-Disposition: attachment; filename="refund-report-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Order ID'), $this->l('Guest Name'), $this->l('Booking Date'),
                $this->l('Cancellation Date'), $this->l('Check-in'), $this->l('Check-out'),
                $this->l('Original Booking Amount'), $this->l('Refunded Amount'),
                $this->l('Refund Status'), $this->l('Reason'), $this->l('Currency'),
            ));
            foreach ($rows as $row) {
                fputcsv($out, array(
                    $row['id_order'],
                    $row['customer_name'],
                    isset($row['booking_date']) ? $row['booking_date'] : '',
                    isset($row['cancellation_date']) ? $row['cancellation_date'] : '',
                    $row['date_from'],
                    $row['date_to'],
                    number_format((float) $row['total_price_tax_incl'], 2, '.', ''),
                    number_format((float) $row['refunded_amount'], 2, '.', ''),
                    isset($row['refund_status']) ? $row['refund_status'] : '',
                    isset($row['cancellation_reason']) ? $row['cancellation_reason'] : '',
                    $currencyIso,
                ));
            }
        } elseif ($report === 'tax') {
            $idLangRev     = Context::getContext()->language->id;
            $csvRevSrc     = Tools::getValue('revenue_source', 'room');
            if (!in_array($csvRevSrc, array('room', 'service', 'all'))) {
                $csvRevSrc = 'room';
            }
            $csvIdTax = (int) Tools::getValue('id_tax', 0);
            $rows = HotelBookingDetail::getTaxBreakdown(array(
                'date_from'      => $dateFrom,
                'date_to'        => $dateTo,
                'id_hotel'       => $idHotel ?: false,
                'id_product'     => $idProduct,
                'id_tax'         => $csvIdTax,
                'revenue_source' => $csvRevSrc,
                'id_lang'        => $idLangRev,
            ));
            header('Content-Disposition: attachment; filename="tax-report-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Booking Date'), $this->l('Booking Ref.'), $this->l('Guest Name'),
                $this->l('Revenue Source'), $this->l('Room Type'),
                $this->l('Taxable Amount'), $this->l('Tax Name'),
                $this->l('Tax Rate %'), $this->l('Tax Amount'), $this->l('Currency'),
            ));
            foreach ($rows as $row) {
                $serviceLabel = (isset($row['revenue_source']) && $row['revenue_source'] === 'service')
                    ? $this->l('Service Charge')
                    : $this->l('Room');
                fputcsv($out, array(
                    isset($row['date_add']) ? $row['date_add'] : '',
                    $row['reference'],
                    $row['customer_name'],
                    $serviceLabel,
                    $row['room_type_name'],
                    number_format((float) $row['taxable_amount'], 2, '.', ''),
                    isset($row['tax_name']) ? $row['tax_name'] : '',
                    number_format((float) $row['tax_rate'], 2, '.', ''),
                    number_format((float) $row['tax_amount'], 2, '.', ''),
                    $currencyIso,
                ));
            }
        } elseif ($report === 'outstanding') {
            $rows = Order::getOutstandingBalance($baseParams);
            header('Content-Disposition: attachment; filename="outstanding-report-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Order ID'), $this->l('Reference'), $this->l('Guest Name'),
                $this->l('Email'), $this->l('Phone'),
                $this->l('Room Type'), $this->l('Room No.'),
                $this->l('Check-in'), $this->l('Check-out'),
                $this->l('Total Charges'), $this->l('Total Paid'), $this->l('Balance Due'),
                $this->l('Days Overdue'), $this->l('Last Payment Date'), $this->l('Status'),
                $this->l('Currency'),
            ));
            $bookingStatusLabels = array(
                HotelBookingDetail::STATUS_ALLOTED      => $this->l('Confirmed'),
                HotelBookingDetail::STATUS_CHECKED_IN   => $this->l('Checked In'),
                HotelBookingDetail::STATUS_CHECKED_OUT  => $this->l('Checked Out'),
            );
            foreach ($rows as $row) {
                $statusLabel = isset($bookingStatusLabels[(int) $row['id_status']])
                    ? $bookingStatusLabels[(int) $row['id_status']] : '';
                fputcsv($out, array(
                    $row['id_order'],
                    $row['reference'],
                    $row['customer_name'],
                    isset($row['email']) ? $row['email'] : '',
                    isset($row['phone']) ? $row['phone'] : '',
                    $row['room_type_name'],
                    $row['room_num'],
                    $row['date_from'],
                    $row['date_to'],
                    number_format((float) $row['total_charges'], 2, '.', ''),
                    number_format((float) $row['total_paid'], 2, '.', ''),
                    number_format((float) $row['balance_due'], 2, '.', ''),
                    (int) $row['days_overdue'],
                    isset($row['last_payment_date']) ? $row['last_payment_date'] : '',
                    $statusLabel,
                    $currencyIso,
                ));
            }
        } else {
            $dailyRoomDetailed = HotelBookingDetail::getDatewiseRoomRevenueTax($baseParams);
            $dailyServiceData  = ServiceProductOrderDetail::getDatewiseServiceRevenueTax($baseParams);
            $rawDiscounts      = Order::getTotalDiscounts(array(
                'date_from' => $dateFrom, 'date_to' => $dateTo,
                'id_hotel'  => $idHotel ?: false, 'granularity' => 'day',
            ));
            $dailyDiscounts = is_array($rawDiscounts) ? $rawDiscounts : array();
            $dailyRoomsOcc  = HotelBookingDetail::getOccupiedRoomsForDiscreteDates($baseParams);
            $dailyBookings  = HotelBookingDetail::getTotalBookings(array(
                'date_from' => $dateFrom, 'date_to' => $dateTo,
                'id_hotel'  => $idHotel ?: false, 'id_product' => $idProduct, 'granularity' => 'day',
            ));
            $dailyAdr = HotelBookingDetail::getDatewiseAverageDailyRate($baseParams);

            $totalRoomsRev = HotelRoomInformation::getTotalRooms($idHotel ?: null, 1);

            header('Content-Disposition: attachment; filename="revenue-report-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Date'), $this->l('Rooms Sold'), $this->l('Total Bookings'),
                $this->l('Room Revenue (excl. Tax)'), $this->l('Extra Services Revenue'),
                $this->l('Discount Amount'), $this->l('Tax Amount'), $this->l('Refund Amount'),
                $this->l('Total Collection'), $this->l('Net Revenue'),
                $this->l('ADR'), $this->l('RevPAR'), $this->l('Occupancy %'),
                $this->l('Currency'),
            ));
            foreach ($dailyRoomDetailed as $ts => $roomData) {
                $svcDay      = isset($dailyServiceData[$ts]) ? $dailyServiceData[$ts] : array('service_revenue' => 0.0, 'tax_amount' => 0.0);
                $svcRev      = $svcDay['service_revenue'];
                $disc        = isset($dailyDiscounts[$ts]) ? (float) $dailyDiscounts[$ts] : 0.0;
                $roomRevExcl = (float) $roomData['room_revenue'];
                $taxAmt      = (float) $roomData['tax_amount'] + $svcDay['tax_amount'];
                $collection  = $roomRevExcl + $svcRev;
                $netRevenue  = $collection - $disc;
                $roomsSold   = isset($dailyRoomsOcc[$ts]) ? (int) $dailyRoomsOcc[$ts] : 0;
                $adr         = isset($dailyAdr[$ts]) ? (float) $dailyAdr[$ts] : 0.0;
                $revpar      = $totalRoomsRev ? round($roomRevExcl / $totalRoomsRev, 2) : 0.0;
                $occupancy   = $totalRoomsRev ? round($roomsSold / $totalRoomsRev * 100, 1) : 0.0;
                fputcsv($out, array(
                    "\t" . date('Y-m-d', $ts),
                    $roomsSold,
                    isset($dailyBookings[$ts]) ? (int) $dailyBookings[$ts] : 0,
                    number_format($roomRevExcl, 2, '.', ''),
                    number_format($svcRev, 2, '.', ''),
                    number_format($disc, 2, '.', ''),
                    number_format($taxAmt, 2, '.', ''),
                    '0.00',
                    number_format($collection, 2, '.', ''),
                    number_format($netRevenue, 2, '.', ''),
                    number_format($adr, 2, '.', ''),
                    number_format($revpar, 2, '.', ''),
                    number_format($occupancy, 1, '.', '').'%',
                    $currencyIso,
                ));
            }
        }

        fclose($out);
        exit;
    }

    /**
     * Stream CSV for an Occupancy sub-report and exit.
     */
    private function exportOccupancyCsv($dateFrom, $dateTo, $idHotel, $idLang, $report)
    {
        $currencyIso = (new Currency(Configuration::get('PS_CURRENCY_DEFAULT')))->iso_code;
        $idProduct   = (int) Tools::getValue('id_product', 0);
        $baseParams  = array(
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
            'id_hotel'  => $idHotel ?: false,
        );

        header('Content-Type: text/csv; charset=utf-8');

        if ($report === 'availability') {
            $availRows = HotelRoomInformation::getAvailabilityReport(array(
                'date_from'  => $dateFrom,
                'date_to'    => $dateTo,
                'id_hotel'   => $idHotel ?: false,
                'id_product' => $idProduct,
                'id_lang'    => $idLang,
            ));
            header('Content-Disposition: attachment; filename="availability-report-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Date'), $this->l('Room Type'), $this->l('Total Rooms (Inventory)'),
                $this->l('Rooms Booked'), $this->l('Out of Order / Maintenance'),
                $this->l('Rooms Available'), $this->l('Rate'), $this->l('Occupancy %'),
            ));
            foreach ($availRows as $row) {
                fputcsv($out, array(
                    $row['date'],
                    $row['room_type_name'],
                    (int) $row['total_rooms'],
                    (int) $row['rooms_booked'],
                    (int) $row['out_of_order'],
                    (int) $row['available'],
                    '—',
                    number_format((float) $row['occupancy_pct'], 1, '.', '').'%',
                ));
            }
        } elseif ($report === 'room-status') {
            $csvFloorOcc = pSQL(Tools::getValue('floor', ''));
            $rows = HotelRoomInformation::getRoomStatusForReports(array(
                'date_from'  => $dateFrom,
                'date_to'    => $dateTo,
                'id_hotel'   => $idHotel ?: false,
                'id_product' => $idProduct,
                'floor'      => $csvFloorOcc,
                'id_lang'    => $idLang,
            ));
            header('Content-Disposition: attachment; filename="room-status-report-'.$dateFrom.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Room No.'), $this->l('Floor'), $this->l('Room Type'),
                $this->l('Status'), $this->l('Housekeeping Status'),
                $this->l('Current Guest'), $this->l('Check-out Date'),
            ));
            foreach ($rows as $row) {
                if ($row['id_order']) {
                    $statusLabel = $this->l('Occupied');
                } elseif ((int) $row['id_status'] === HotelRoomInformation::STATUS_INACTIVE
                    || (int) $row['id_status'] === HotelRoomInformation::STATUS_TEMPORARY_INACTIVE) {
                    $statusLabel = $this->l('Under Maintenance');
                } else {
                    $statusLabel = $this->l('Vacant');
                }
                fputcsv($out, array(
                    $row['room_num'],
                    isset($row['floor']) ? $row['floor'] : '',
                    $row['room_type_name'],
                    $statusLabel,
                    '—',
                    isset($row['guest_name']) ? $row['guest_name'] : '',
                    isset($row['date_to']) ? $row['date_to'] : '',
                ));
            }
        } elseif ($report === 'room-perf') {
            $rows = HotelRoomType::getRoomTypePerformance(array(
                'date_from' => $dateFrom,
                'date_to'   => $dateTo,
                'id_hotel'  => $idHotel ?: false,
                'id_lang'   => $idLang,
            ));
            header('Content-Disposition: attachment; filename="room-type-performance-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Room Type'), $this->l('Hotel'), $this->l('Total Rooms'),
                $this->l('Total Room Nights Available'), $this->l('Total Room Nights Sold'),
                $this->l('Occupancy Rate %'),
                $this->l('Total Revenue (excl. Tax)'), $this->l('Tax Amount'),
                $this->l('Total Revenue (incl. Tax)'),
                $this->l('ADR'), $this->l('RevPAR'),
                $this->l('Cancellation Count'), $this->l('No-Show Count'), $this->l('Avg. LOS'),
                $this->l('Currency'),
            ));
            foreach ($rows as $row) {
                fputcsv($out, array(
                    $row['room_type_name'],
                    $row['hotel_name'],
                    (int) $row['total_rooms'],
                    (int) $row['total_nights_available'],
                    (int) $row['room_nights'],
                    number_format((float) $row['occupancy_pct'], 1, '.', '').'%',
                    number_format((float) $row['room_revenue'], 2, '.', ''),
                    number_format((float) $row['tax_amount'], 2, '.', ''),
                    number_format((float) $row['total_revenue'], 2, '.', ''),
                    number_format((float) $row['adr'], 2, '.', ''),
                    number_format((float) $row['revpar'], 2, '.', ''),
                    (int) $row['cancel_count'],
                    (int) $row['no_show_count'],
                    number_format((float) $row['avg_los'], 1, '.', ''),
                    $currencyIso,
                ));
            }
        } else {
            $totalRoomsOcc  = HotelRoomInformation::getTotalRooms($idHotel ?: null, 1);
            $dailyAllOcc    = HotelBookingDetail::getOccupiedRoomsForDiscreteDates($baseParams);
            $dailyBooked    = HotelBookingDetail::getOccupiedRoomsForDiscreteDates(
                array_merge($baseParams, array('id_status' => HotelBookingDetail::STATUS_ALLOTED))
            );
            $dailyCheckedIn = HotelBookingDetail::getOccupiedRoomsForDiscreteDates(
                array_merge($baseParams, array('id_status' => HotelBookingDetail::STATUS_CHECKED_IN))
            );
            $dailyAvailable = HotelRoomInformation::getAvailableRoomsForDiscreteDates($baseParams);
            $dailyRevenue   = HotelBookingDetail::getRoomRevenueForDiscreteDates($baseParams);
            $dailyAdrOcc    = HotelBookingDetail::getDatewiseAverageDailyRate($baseParams);

            header('Content-Disposition: attachment; filename="occupancy-report-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Date'), $this->l('Total Rooms (Inventory)'), $this->l('Available Rooms'),
                $this->l('Rooms Booked'), $this->l('Rooms Occupied'), $this->l('Out of Order'),
                $this->l('Complimentary'), $this->l('Occupancy Rate %'),
                $this->l('ADR'), $this->l('RevPAR'), $this->l('Total Room Revenue'),
                $this->l('Currency'),
            ));
            foreach ($dailyAllOcc as $ts => $allOcc) {
                $available    = isset($dailyAvailable[$ts]) ? (int) $dailyAvailable[$ts] : 0;
                $booked       = isset($dailyBooked[$ts])    ? (int) $dailyBooked[$ts]    : 0;
                $occupied     = isset($dailyCheckedIn[$ts]) ? (int) $dailyCheckedIn[$ts] : 0;
                $outOfOrder   = max(0, $totalRoomsOcc - $available - (int) $allOcc);
                $revenue      = isset($dailyRevenue[$ts])   ? (float) $dailyRevenue[$ts] : 0.0;
                $adr          = isset($dailyAdrOcc[$ts])    ? (float) $dailyAdrOcc[$ts]  : 0.0;
                $revpar       = $totalRoomsOcc ? round($revenue / $totalRoomsOcc, 2) : 0.0;
                $occupancyPct = $totalRoomsOcc ? round((int) $allOcc / $totalRoomsOcc * 100, 1) : 0.0;
                fputcsv($out, array(
                    "\t" . date('Y-m-d', $ts),
                    $totalRoomsOcc,
                    $available,
                    $booked,
                    $occupied,
                    $outOfOrder,
                    '—',
                    number_format($occupancyPct, 1, '.', '').'%',
                    number_format($adr, 2, '.', ''),
                    number_format($revpar, 2, '.', ''),
                    number_format($revenue, 2, '.', ''),
                    $currencyIso,
                ));
            }
        }

        fclose($out);
        exit;
    }

    /**
     * Stream CSV for a Guests sub-report and exit.
     */
    private function exportGuestsCsv($dateFrom, $dateTo, $idHotel, $report)
    {
        header('Content-Type: text/csv; charset=utf-8');
        $currencyIso = (new Currency(Configuration::get('PS_CURRENCY_DEFAULT')))->iso_code;
        $baseParams  = array(
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
            'id_hotel'  => $idHotel ?: false,
        );

        if ($report === 'services') {
            $idLangSvc        = Context::getContext()->language->id;
            $csvIdCategory    = (int) Tools::getValue('id_category', 0);
            $csvIdServiceProd = (int) Tools::getValue('id_service_product', 0);
            $rows = ServiceProductOrderDetail::getServicesInfo(
                array_merge($baseParams, array(
                    'id_lang'            => $idLangSvc,
                    'id_category'        => $csvIdCategory,
                    'id_service_product' => $csvIdServiceProd,
                ))
            );
            header('Content-Disposition: attachment; filename="services-report-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Date'), $this->l('Booking Ref. No.'), $this->l('Guest Name'),
                $this->l('Room No.'), $this->l('Service Name'), $this->l('Service Category'),
                $this->l('Quantity'), $this->l('Unit Price'),
                $this->l('Total Price (excl. Tax)'), $this->l('Tax Amount'), $this->l('Grand Total'),
                $this->l('Currency'),
            ));
            foreach ($rows as $row) {
                fputcsv($out, array(
                    isset($row['date_add']) ? "\t" . date('Y-m-d', strtotime($row['date_add'])) : '',
                    isset($row['reference']) ? $row['reference'] : '',
                    $row['customer_name'],
                    isset($row['room_num']) ? $row['room_num'] : '',
                    $row['service_name'],
                    isset($row['service_category']) ? $row['service_category'] : '',
                    (int) $row['quantity'],
                    number_format((float) $row['unit_price'], 2, '.', ''),
                    number_format((float) $row['total_price_tax_excl'], 2, '.', ''),
                    number_format((float) $row['tax_amount'], 2, '.', ''),
                    number_format((float) $row['total_price_tax_incl'], 2, '.', ''),
                    $currencyIso,
                ));
            }
        } elseif ($report === 'guest-directory') {
            $idLangDir   = Context::getContext()->language->id;
            $csvGuestType = pSQL(Tools::getValue('guest_type', ''));
            $guests = HotelBookingDetail::getGuestDirectory(
                array_merge($baseParams, array('id_lang' => $idLangDir))
            );
            if ($csvGuestType === 'new') {
                $guests = array_values(array_filter($guests, function ($g) {
                    return (int) $g['total_stays'] === 1;
                }));
            } elseif ($csvGuestType === 'returning') {
                $guests = array_values(array_filter($guests, function ($g) {
                    return (int) $g['total_stays'] > 1;
                }));
            }
            header('Content-Disposition: attachment; filename="guest-directory-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Guest ID'), $this->l('Guest Name'), $this->l('Email'),
                $this->l('Phone'), $this->l('Country'), $this->l('State'), $this->l('City'),
                $this->l('Company'), $this->l('VAT Number'), $this->l('Address'),
                $this->l('Postcode'), $this->l('Guest Type'),
                $this->l('Total Stays'), $this->l('Total Nights'), $this->l('Last Stay Date'),
                $this->l('Lifetime Revenue'), $this->l('Avg Spend Per Stay'), $this->l('Currency'),
            ));
            foreach ($guests as $g) {
                $address = isset($g['address1']) ? $g['address1'] : '';
                if (!empty($g['address2'])) {
                    $address .= ', '.$g['address2'];
                }
                $guestType = ((int) $g['total_stays'] === 1) ? $this->l('New') : $this->l('Returning');
                fputcsv($out, array(
                    (int) $g['id_customer'],
                    $g['customer_name'],
                    $g['email'],
                    isset($g['phone']) ? $g['phone'] : '',
                    isset($g['country']) ? $g['country'] : '',
                    isset($g['state']) ? $g['state'] : '',
                    isset($g['city']) ? $g['city'] : '',
                    isset($g['company']) ? $g['company'] : '',
                    isset($g['vat_number']) ? $g['vat_number'] : '',
                    $address,
                    isset($g['postcode']) ? $g['postcode'] : '',
                    $guestType,
                    (int) $g['total_stays'],
                    (int) $g['total_nights'],
                    isset($g['last_stay']) ? $g['last_stay'] : '',
                    number_format((float) $g['lifetime_revenue'], 2, '.', ''),
                    number_format((float) $g['avg_spend_per_stay'], 2, '.', ''),
                    $currencyIso,
                ));
            }
        } else {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
        }

        fclose($out);
        exit;
    }

    /**
     * Stream CSV for a Channels sub-report and exit.
     */
    private function exportChannelsCsv($dateFrom, $dateTo, $idHotel, $report)
    {
        header('Content-Type: text/csv; charset=utf-8');
        $currencyIso = (new Currency(Configuration::get('PS_CURRENCY_DEFAULT')))->iso_code;
        $baseParams  = array(
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
            'id_hotel'  => $idHotel ?: false,
        );

        if ($report === 'source') {
            $rows = HotelBookingDetail::getChannelStats($baseParams);
            $totalRevExcl = 0.0;
            foreach ($rows as $r) {
                $totalRevExcl += (float) $r['revenue_excl'];
            }
            header('Content-Disposition: attachment; filename="booking-source-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Channel'), $this->l('Bookings'), $this->l('Room Nights'),
                $this->l('Room Revenue (excl. Tax)'), $this->l('Discount Amount'),
                $this->l('Refund Amount'), $this->l('Tax Amount'),
                $this->l('Total Collection'), $this->l('Net Revenue'),
                $this->l('Cancellations'), $this->l('Cancel Rate %'),
                $this->l('ADR'), $this->l('Contribution %'), $this->l('Currency'),
            ));
            foreach ($rows as $row) {
                $revExcl     = (float) $row['revenue_excl'];
                $revIncl     = (float) $row['revenue_incl'];
                $taxAmt      = $revIncl - $revExcl;
                $discAmt     = isset($row['discount_amount']) ? (float) $row['discount_amount'] : 0.0;
                $netRev      = $revExcl - $discAmt;
                $bookings    = (int) $row['bookings'];
                $cancels     = isset($row['cancellations']) ? (int) $row['cancellations'] : 0;
                $cancelRate  = $bookings > 0 ? round($cancels / $bookings * 100, 1) : 0.0;
                $roomNights  = (int) $row['room_nights'];
                $adr         = $roomNights > 0 ? round($revExcl / $roomNights, 2) : 0.0;
                $contrib     = $totalRevExcl > 0 ? round($revExcl / $totalRevExcl * 100, 1) : 0.0;
                fputcsv($out, array(
                    $row['channel_label'],
                    $bookings,
                    $roomNights,
                    number_format($revExcl, 2, '.', ''),
                    number_format($discAmt, 2, '.', ''),
                    '0.00',
                    number_format($taxAmt, 2, '.', ''),
                    number_format($revIncl, 2, '.', ''),
                    number_format($netRev, 2, '.', ''),
                    $cancels,
                    number_format($cancelRate, 1, '.', '').'%',
                    number_format($adr, 2, '.', ''),
                    number_format($contrib, 1, '.', '').'%',
                    $currencyIso,
                ));
            }
        } elseif ($report === 'payment-method') {
            $rows = HotelBookingDetail::getPaymentMethodStats($baseParams);
            header('Content-Disposition: attachment; filename="payment-methods-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Payment Method'), $this->l('Module'),
                $this->l('Bookings'), $this->l('Revenue (excl. Tax)'), $this->l('Revenue (incl. Tax)'),
                $this->l('Currency'),
            ));
            foreach ($rows as $row) {
                fputcsv($out, array(
                    $row['payment_method'], $row['module'],
                    $row['bookings'],
                    number_format((float) $row['revenue_excl'], 2),
                    number_format((float) $row['revenue_incl'], 2),
                    $currencyIso,
                ));
            }
        } else {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
        }

        fclose($out);
        exit;
    }

    /**
     * Stream CSV for a Property sub-report and exit.
     */
    private function exportPropertyCsv($dateFrom, $dateTo, $idHotel, $idLang, $report)
    {
        header('Content-Type: text/csv; charset=utf-8');
        $currencyIso = (new Currency(Configuration::get('PS_CURRENCY_DEFAULT')))->iso_code;
        $baseParams  = array(
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
            'id_hotel'  => $idHotel ?: false,
        );

        if ($report === 'daily-summary') {
            $totalRoomsProp  = HotelRoomInformation::getTotalRooms($idHotel ?: null, 1);
            $roomsSoldByDay  = HotelBookingDetail::getOccupiedRoomsForDiscreteDates($baseParams);
            $checkedInByDay  = HotelBookingDetail::getOccupiedRoomsForDiscreteDates(
                array_merge($baseParams, array('id_status' => HotelBookingDetail::STATUS_CHECKED_IN))
            );
            $arrivalsByDay   = HotelBookingDetail::getDatewiseArrivals($baseParams);
            $departuresByDay = HotelBookingDetail::getDatewiseDepartures($baseParams);
            $cancsByDay      = HotelBookingDetail::getDatewiseCancellations($baseParams);
            $adrByDay        = HotelBookingDetail::getDatewiseAverageDailyRate($baseParams);
            $revparByDay     = HotelBookingDetail::getDatewiseRevPAR($baseParams);
            $revenueByDay    = HotelBookingDetail::getDatewiseRoomRevenue($baseParams);

            header('Content-Disposition: attachment; filename="daily-summary-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Date'), $this->l('Total Rooms'), $this->l('Rooms Sold'),
                $this->l('Occupancy %'), $this->l('ADR'), $this->l('RevPAR'),
                $this->l('Total Revenue'), $this->l('Arrivals'), $this->l('Departures'),
                $this->l('In-house Guests'), $this->l('Cancellations'), $this->l('No-Shows'),
                $this->l('Currency'),
            ));
            $current = $dateFrom;
            while ($current <= $dateTo) {
                $ts        = strtotime($current);
                $roomsSold = isset($roomsSoldByDay[$ts]) ? (int) $roomsSoldByDay[$ts] : 0;
                $occupancy = $totalRoomsProp ? round($roomsSold / $totalRoomsProp * 100, 1) : 0.0;
                fputcsv($out, array(
                    "\t" . date('Y-m-d', $ts),
                    $totalRoomsProp,
                    $roomsSold,
                    number_format($occupancy, 1, '.', '').'%',
                    number_format(isset($adrByDay[$ts])       ? (float) $adrByDay[$ts]     : 0.0, 2, '.', ''),
                    number_format(isset($revparByDay[$ts])    ? (float) $revparByDay[$ts]  : 0.0, 2, '.', ''),
                    number_format(isset($revenueByDay[$ts])   ? (float) $revenueByDay[$ts] : 0.0, 2, '.', ''),
                    isset($arrivalsByDay[$ts])   ? (int) $arrivalsByDay[$ts]   : 0,
                    isset($departuresByDay[$ts]) ? (int) $departuresByDay[$ts] : 0,
                    isset($checkedInByDay[$ts])  ? (int) $checkedInByDay[$ts]  : 0,
                    isset($cancsByDay[$ts])      ? (int) $cancsByDay[$ts]      : 0,
                    '—',
                    $currencyIso,
                ));
                $current = date('Y-m-d', strtotime('+1 day', strtotime($current)));
            }

        } elseif ($report === 'hotel-comparison') {
            $hotels = (new HotelBranchInformation())->hotelsNameAndId();
            header('Content-Disposition: attachment; filename="hotel-comparison-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Hotel Name'), $this->l('Total Rooms'), $this->l('Rooms Sold'),
                $this->l('Occupancy %'), $this->l('Room Revenue'), $this->l('Extra Service Revenue'),
                $this->l('Gross Revenue'), $this->l('ADR'), $this->l('RevPAR'),
                $this->l('Total Bookings'), $this->l('Total Cancellations'), $this->l('Cancel Rate %'),
                $this->l('No-Shows'), $this->l('Avg LOS'), $this->l('Outstanding Balance'),
                $this->l('Currency'),
            ));
            foreach ($hotels as $hotel) {
                $hotelParams  = array_merge($baseParams, array('id_hotel' => (int) $hotel['id']));
                $totalRoomsH  = HotelRoomInformation::getTotalRooms((int) $hotel['id'], 1);
                $bookings     = (int) HotelBookingDetail::getTotalBookings($hotelParams);
                $cancels      = (int) HotelBookingDetail::getTotalCancellations($hotelParams);
                $cancelRate   = $bookings > 0 ? round($cancels / $bookings * 100, 1) : 0.0;
                $roomNights   = (int) HotelBookingDetail::getTotalRoomNights($hotelParams);
                $roomRevenue  = (float) HotelBookingDetail::getTotalRoomRevenue($hotelParams);
                $extraRev     = (float) ServiceProductOrderDetail::getTotalServiceRevenue($hotelParams);
                $grossRevenue = $roomRevenue + $extraRev;
                $occupancy    = round(HotelBookingDetail::getOccupancyRate($hotelParams) * 100, 1);
                $noShows      = (int) HotelBookingDetail::getTotalNoShows($hotelParams);
                $avgLos       = ($bookings > 0 && $roomNights > 0) ? round($roomNights / $bookings, 1) : 0.0;
                $obRows       = Order::getOutstandingBalance($hotelParams);
                $outstanding  = is_array($obRows)
                    ? array_sum(array_column($obRows, 'balance_due')) : 0.0;
                fputcsv($out, array(
                    $hotel['hotel_name'],
                    $totalRoomsH,
                    $roomNights,
                    number_format($occupancy, 1, '.', '').'%',
                    number_format($roomRevenue, 2, '.', ''),
                    number_format($extraRev, 2, '.', ''),
                    number_format($grossRevenue, 2, '.', ''),
                    number_format((float) HotelBookingDetail::getAverageDailyRate($hotelParams), 2, '.', ''),
                    number_format((float) HotelBookingDetail::getRevPAR($hotelParams), 2, '.', ''),
                    $bookings,
                    $cancels,
                    number_format($cancelRate, 1, '.', '').'%',
                    $noShows,
                    number_format($avgLos, 1, '.', ''),
                    number_format((float) $outstanding, 2, '.', ''),
                    $currencyIso,
                ));
            }

        } elseif ($report === 'out-of-order') {
            $csvIdProductProp = (int) Tools::getValue('id_product', 0);
            $csvFloorProp     = pSQL(Tools::getValue('floor', ''));
            $rows = HotelRoomDisableDates::getDisabledRooms(
                array_merge($baseParams, array(
                    'id_lang'    => $idLang,
                    'id_product' => $csvIdProductProp,
                    'floor'      => $csvFloorProp,
                ))
            );
            header('Content-Disposition: attachment; filename="out-of-order-'.$dateFrom.'-to-'.$dateTo.'.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, array(
                $this->l('Room No.'), $this->l('Floor'), $this->l('Room Type'),
                $this->l('OOO Status'), $this->l('Reason'),
                $this->l('Start Date'), $this->l('Expected End Date'), $this->l('Actual End Date'),
                $this->l('Duration (Days)'), $this->l('Current Status'),
                $this->l('Marked By'), $this->l('Resolved By'), $this->l('Est. Revenue Loss'),
            ));
            $today = date('Y-m-d');
            foreach ($rows as $row) {
                $oooStatus     = ((int) $row['id_status'] === 2)
                    ? $this->l('Out of Order') : $this->l('Under Maintenance');
                $currentStatus = ($row['disabled_to'] >= $today)
                    ? $this->l('Active') : $this->l('Resolved');
                fputcsv($out, array(
                    $row['room_num'],
                    isset($row['floor']) ? $row['floor'] : '',
                    $row['room_type_name'],
                    $oooStatus,
                    $row['reason'] ? $row['reason'] : '',
                    $row['disabled_from'],
                    $row['disabled_to'],
                    '—',
                    (int) $row['disabled_days'],
                    $currentStatus,
                    '—',
                    '—',
                    '—',
                ));
            }
        } else {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
        }

        fclose($out);
        exit;
    }
}
