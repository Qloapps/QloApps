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

if (!defined('_PS_VERSION_')) {
    exit;
}

class DashGuestCycle extends Module
{
    public function __construct()
    {
        $this->name = 'dashguestcycle';
        $this->tab = 'dashboard';
        $this->version = '1.0.3';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6');
        $this->author = 'Webkul';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Dashboard Guest Cycle');
        $this->description = $this->l('Adds a block with a graphical representation of guest cycle stats.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->allow_push = true;
    }

    public function install()
    {
        return (parent::install()
            && $this->registerHook('actionAdminDashboardKPIListingModifier')
            && $this->registerHook('dashboardZoneTwo')
            && $this->registerHook('dashboardData')
            && $this->registerHook('actionAdminControllerSetMedia')
        );
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('controller') == 'AdminDashboard') {
            $this->context->controller->addCSS($this->_path.'views/css/'.$this->name.'.css');
        }
    }

    public function hookActionAdminDashboardKPIListingModifier(array $params)
    {
        $idHotel   = (int)$this->context->cookie->stats_id_hotel;
        $kpiValues = $this->getKpiValues($idHotel);

        $kpisData = array(
            array(
                'id'      => 'box-dgc-arrivals',
                'color'   => 'color1',
                'title'   => $this->l('Arrivals'),
                'tooltip' => $this->l('The number of arrivals scheduled for today.'),
                'value'   => $kpiValues['box-dgc-arrivals'],
            ),
            array(
                'id'      => 'box-dgc-departures',
                'color'   => 'color2',
                'title'   => $this->l('Departures'),
                'tooltip' => $this->l('The number of departures scheduled for today.'),
                'value'   => $kpiValues['box-dgc-departures'],
            ),
            array(
                'id'      => 'box-dgc-new-bookings',
                'color'   => 'color3',
                'title'   => $this->l('New Bookings'),
                'tooltip' => $this->l('The number of new bookings created today so far.'),
                'value'   => $kpiValues['box-dgc-new-bookings'],
            ),
            array(
                'id'      => 'box-dgc-occupied',
                'color'   => 'color4',
                'title'   => $this->l('Occupied Rooms'),
                'tooltip' => $this->l('The count of rooms currently occupied by guests.'),
                'value'   => $kpiValues['box-dgc-occupied'],
            ),
            array(
                'id'      => 'box-dgc-messages',
                'color'   => 'color5',
                'title'   => $this->l('Guest Messages'),
                'tooltip' => $this->l('The number of new messages received from guests today.'),
                'value'   => $kpiValues['box-dgc-messages'],
            ),
            array(
                'id'      => 'box-dgc-cancelled',
                'color'   => 'color6',
                'title'   => $this->l('Cancelled Bookings'),
                'tooltip' => $this->l('The number of bookings cancelled today so far.'),
                'value'   => $kpiValues['box-dgc-cancelled'],
            ),
            array(
                'id'      => 'box-dgc-guests',
                'color'   => 'color1',
                'title'   => $this->l('Guests (Adults/Children)'),
                'tooltip' => $this->l('The number of adults and children scheduled to stay today.'),
                'value'   => $kpiValues['box-dgc-guests'],
            ),
        );

        foreach ($kpisData as $data) {
            $helper          = new HelperKpi();
            $helper->id      = $data['id'];
            $helper->color   = $data['color'];
            $helper->title   = $data['title'];
            $helper->tooltip = $data['tooltip'];
            $helper->value   = $data['value'];
            $params['kpis'][] = $helper;
        }
    }

    private function getKpiValues($idHotel)
    {
        $dateToday = date('Y-m-d');

        if (Configuration::get('PS_DASHBOARD_SIMULATION')) {
            $totalArrivals     = rand(100, 1000);
            $arrived           = rand(0, $totalArrivals);
            $totalDepartures   = rand(100, 1000);
            $departed          = rand(0, $totalDepartures);
            $newBookings       = rand(10, 500);
            $occupied          = rand(10, 500);
            $newMessages       = rand(0, 20);
            $cancelledBookings = rand(0, 20);
            $totalAdults       = rand(100, 1000);
            $children          = rand(0, $totalAdults);
        } else {
            $arrivalsData   = AdminStatsController::getArrivalsByDate($dateToday, $idHotel);
            $departuresData = AdminStatsController::getDeparturesByDate($dateToday, $idHotel);
            $guestsData     = AdminStatsController::getGuestsByDate($dateToday, $idHotel);

            $arrived           = (int) $arrivalsData['arrived'];
            $totalArrivals     = (int) $arrivalsData['total_arrivals'];
            $departed          = (int) $departuresData['departed'];
            $totalDepartures   = (int) $departuresData['total_departures'];
            $newBookings       = count(AdminStatsController::getNewBookingsInfoByDate($dateToday, $idHotel));
            $occupied          = (int) AdminStatsController::getDistinctRoomBookingsCount(
                date('Y-m-d', strtotime('-1 day')),
                $dateToday,
                $idHotel,
                HotelBookingDetail::STATUS_CHECKED_IN
            );
            $newMessages       = (int) CustomerMessage::getMessagesByDate($dateToday);
            $cancelledBookings = (int) AdminStatsController::getCancelledBookingsByDate($dateToday, $idHotel);
            $totalAdults       = (int) $guestsData['adults'];
            $children          = (int) $guestsData['children'];
        }

        return array(
            'box-dgc-arrivals'     => sprintf('%02d', $arrived).'/'.sprintf('%02d', $totalArrivals),
            'box-dgc-departures'   => sprintf('%02d', $departed).'/'.sprintf('%02d', $totalDepartures),
            'box-dgc-new-bookings' => sprintf('%02d', $newBookings),
            'box-dgc-occupied'     => sprintf('%02d', $occupied),
            'box-dgc-messages'     => sprintf('%02d', $newMessages),
            'box-dgc-cancelled'    => sprintf('%02d', $cancelledBookings),
            'box-dgc-guests'       => sprintf('%02d', $totalAdults).'/'.sprintf('%02d', $children),
        );
    }

    public function hookDashboardZoneTwo()
    {
        return $this->display(__FILE__, 'dashboard-zone-two.tpl');
    }

    public function hookDashboardData($params)
    {
        $dataValue = array();
        $dataTable = array();

        $dateToday = date('Y-m-d');

        // KPI box values — updated on every AJAX refresh (demo toggle, date change)
        foreach ($this->getKpiValues((int) $params['id_hotel']) as $boxId => $value) {
            $dataValue[$boxId . ' .value'] = $value;
        }

        // set tables data
        $tableCurrentArrivals = $this->getArrivalsTableContentsByDate($dateToday, $params['id_hotel']);
        $dataValue['dgc_count_upcoming_arrivals'] = count($tableCurrentArrivals['body']);

        $tableCurrentDepartures = $this->getDeparturesTableContentsByDate($dateToday, $params['id_hotel']);
        $dataValue['dgc_count_upcoming_departures'] = count($tableCurrentDepartures['body']);

        $tableCurrentInHouse = $this->getInHousesTableContents($params['id_hotel']);
        $dataValue['dgc_count_current_in_house'] = count($tableCurrentInHouse['body']);

        $tableNewBookings = $this->getNewBookingsTableContentsByDate($dateToday, $params['id_hotel']);
        $dataValue['dgc_count_new_bookings'] = count($tableNewBookings['body']);

        $tableCancellations = $this->getCancellationsTableContentsByDate($dateToday, $params['id_hotel']);
        $dataValue['dgc_count_cancellations'] = count($tableCancellations['body']);

        $dataTable = array(
            'dgc_table_current_arrivals' => $tableCurrentArrivals,
            'dgc_table_current_departures' => $tableCurrentDepartures,
            'dgc_table_current_in_house' => $tableCurrentInHouse,
            'dgc_table_new_bookings' => $tableNewBookings,
            'dgc_table_cancellations' => $tableCancellations,
        );

        return array('data_value' => $dataValue, 'data_table' => $dataTable);
    }

    public function getArrivalsTableContentsByDate($date, $idHotel)
    {
        $header = array(
            'name' => array('title' => $this->l('Customer Name'), 'class' => 'text-left'),
            'room_num' => array('title' => $this->l('Room No.'), 'class' => 'text-center'),
            'room_type' => array('title' => $this->l('Room Type'), 'class' => 'text-left'),
            'hotel' => array('title' => $this->l('Hotel'), 'class' => 'text-left'),
            'guests' => array('title' => $this->l('Guests'), 'class' => 'text-center'),
            'check_out' => array('title' => $this->l('Check-out (LOS)'), 'class' => 'text-left'),
            'order_id' => array('title' => $this->l('Order ID'), 'class' => 'text-center'),
        );

        if ($idHotel != 0) {
            unset($header['hotel']);
        }

        $arrivalsInfo = AdminStatsController::getArrivalsInfoByDate($date, $idHotel);

        $body = array();
        foreach ($arrivalsInfo as $arrivalInfo) {
            $tr = array();
            $tr[] = array(
                'value' => '<a href="'.$this->context->link->getAdminLink('AdminCustomers', true).'&id_customer='.$arrivalInfo['id_customer'].'&viewcustomer" target="_blank">'.Tools::htmlentitiesUTF8($arrivalInfo['customer_name']).'</a>',
                'class' => 'text-left',
            );
            $tr[] = array(
                'value' => $arrivalInfo['room_num'],
                'class' => 'text-center',
            );
            $tr[] = array(
                'value' => '<a href="'.$this->context->link->getAdminLink('AdminProducts', true).'&id_product='.$arrivalInfo['id_product'].'&updateproduct" target="_blank">'.Tools::htmlentitiesUTF8($arrivalInfo['room_type_name']).'</a>',
                'class' => 'text-left',
            );

            if ($idHotel == 0) {
                $tr[] = array(
                    'value' => '<a href="'.$this->context->link->getAdminLink('AdminAddHotel', true).'&id='.$arrivalInfo['id_hotel'].'&updatehtl_branch_info" target="_blank">'.Tools::htmlentitiesUTF8($arrivalInfo['hotel_name']).'</a>',
                    'class' => 'text-left',
                );
            }

            $tr[] = array(
                'value' => $arrivalInfo['with_occupancy'] ? ($arrivalInfo['adults'] + $arrivalInfo['children']) : '--',
                'class' => 'text-center',
            );
            $tr[] = array(
                'value' => Tools::displayDate($arrivalInfo['date_to']).' ('.$arrivalInfo['los'].' '.($arrivalInfo['los'] > 1 ? $this->l('Nights') : $this->l('Night')).')',
                'class' => 'text-left',
            );
            $tr[] = array(
                'value' => '<a href="'.$this->context->link->getAdminLink('AdminOrders', true).'&id_order='.$arrivalInfo['id_order'].'&vieworder" target="_blank">#'.Tools::htmlentitiesUTF8($arrivalInfo['id_order']).'</a>',
                'class' => 'text-center',
            );

            $body[] = $tr;
        }

        return array('header' => array_values($header), 'body' => $body);
    }

    public function getDeparturesTableContentsByDate($date, $idHotel)
    {
        $header = array(
            'name' => array('title' => $this->l('Customer Name'), 'class' => 'text-left'),
            'room_num' => array('title' => $this->l('Room No.'), 'class' => 'text-center'),
            'room_type' => array('title' => $this->l('Room Type'), 'class' => 'text-left'),
            'hotel' => array('title' => $this->l('Hotel'), 'class' => 'text-left'),
            'guests' => array('title' => $this->l('Guests'), 'class' => 'text-center'),
            'check_in' => array('title' => $this->l('Check-in (LOS)'), 'class' => 'text-left'),
            'order_id' => array('title' => $this->l('Order ID'), 'class' => 'text-center'),
        );

        if ($idHotel != 0) {
            unset($header['hotel']);
        }

        $departuresInfo = AdminStatsController::getDeparturesInfoByDate($date, $idHotel);

        $body = array();
        foreach ($departuresInfo as $departureInfo) {
            $tr = array();
            $tr[] = array(
                'value' => '<a href="'.$this->context->link->getAdminLink('AdminCustomers', true).'&id_customer='.$departureInfo['id_customer'].'&viewcustomer" target="_blank">'.Tools::htmlentitiesUTF8($departureInfo['customer_name']).'</a>',
                'class' => 'text-left',
            );
            $tr[] = array(
                'value' => $departureInfo['room_num'],
                'class' => 'text-center',
            );
            $tr[] = array(
                'value' => '<a href="'.$this->context->link->getAdminLink('AdminProducts', true).'&id_product='.$departureInfo['id_product'].'&updateproduct" target="_blank">'.Tools::htmlentitiesUTF8($departureInfo['room_type_name']).'</a>',
                'class' => 'text-left',
            );

            if ($idHotel == 0) {
                $tr[] = array(
                    'value' => '<a href="'.$this->context->link->getAdminLink('AdminAddHotel', true).'&id='.$departureInfo['id_hotel'].'&updatehtl_branch_info" target="_blank">'.Tools::htmlentitiesUTF8($departureInfo['hotel_name']).'</a>',
                    'class' => 'text-left',
                );
            }

            $tr[] = array(
                'value' => $departureInfo['with_occupancy'] ? ($departureInfo['adults'] + $departureInfo['children']) : '--',
                'class' => 'text-center',
            );
            $tr[] = array(
                'value' => Tools::displayDate($departureInfo['date_from']).' ('.$departureInfo['los'].' '.($departureInfo['los'] > 1 ? $this->l('Nights') : $this->l('Night')).')',
                'class' => 'text-left',
            );
            $tr[] = array(
                'value' => '<a href="'.$this->context->link->getAdminLink('AdminOrders', true).'&id_order='.$departureInfo['id_order'].'&vieworder" target="_blank">#'.Tools::htmlentitiesUTF8($departureInfo['id_order']).'</a>',
                'class' => 'text-center',
            );

            $body[] = $tr;
        }

        return array('header' => array_values($header), 'body' => $body);
    }

    public function getInHousesTableContents($idHotel)
    {
        $header = array(
            'name' => array('title' => $this->l('Customer Name'), 'class' => 'text-left'),
            'room_num' => array('title' => $this->l('Room No.'), 'class' => 'text-center'),
            'room_type' => array('title' => $this->l('Room Type'), 'class' => 'text-left'),
            'hotel' => array('title' => $this->l('Hotel'), 'class' => 'text-left'),
            'guests' => array('title' => $this->l('Guests'), 'class' => 'text-center'),
            'check_in' => array('title' => $this->l('Check-in'), 'class' => 'text-left'),
            'check_out' => array('title' => $this->l('Check-out (LOS)'), 'class' => 'text-left'),
            'order_id' => array('title' => $this->l('Order ID'), 'class' => 'text-center'),
        );

        if ($idHotel != 0) {
            unset($header['hotel']);
        }

        $inHousesInfo = AdminStatsController::getInHousesInfo($idHotel);

        $body = array();
        foreach ($inHousesInfo as $inHouseInfo) {
            $tr = array();
            $tr[] = array(
                'value' => '<a href="'.$this->context->link->getAdminLink('AdminCustomers', true).'&id_customer='.$inHouseInfo['id_customer'].'&viewcustomer" target="_blank">'.Tools::htmlentitiesUTF8($inHouseInfo['customer_name']).'</a>',
                'class' => 'text-left',
            );
            $tr[] = array(
                'value' => $inHouseInfo['room_num'],
                'class' => 'text-center',
            );
            $tr[] = array(
                'value' => '<a href="'.$this->context->link->getAdminLink('AdminProducts', true).'&id_product='.$inHouseInfo['id_product'].'&updateproduct" target="_blank">'.Tools::htmlentitiesUTF8($inHouseInfo['room_type_name']).'</a>',
                'class' => 'text-left',
            );

            if ($idHotel == 0) {
                $tr[] = array(
                    'value' => '<a href="'.$this->context->link->getAdminLink('AdminAddHotel', true).'&id='.$inHouseInfo['id_hotel'].'&updatehtl_branch_info" target="_blank">'.Tools::htmlentitiesUTF8($inHouseInfo['hotel_name']).'</a>',
                    'class' => 'text-left',
                );
            }

            $tr[] = array(
                'value' => $inHouseInfo['with_occupancy'] ? ($inHouseInfo['adults'] + $inHouseInfo['children']) : '--',
                'class' => 'text-center',
            );
            $tr[] = array(
                'value' => Tools::displayDate($inHouseInfo['date_from']),
                'class' => 'text-left',
            );
            $tr[] = array(
                'value' => Tools::displayDate($inHouseInfo['date_to']).' ('.$inHouseInfo['los'].' '.($inHouseInfo['los'] > 1 ? $this->l('Nights') : $this->l('Night')).')',
                'class' => 'text-left',
            );
            $tr[] = array(
                'value' => '<a href="'.$this->context->link->getAdminLink('AdminOrders', true).'&id_order='.$inHouseInfo['id_order'].'&vieworder" target="_blank">#'.Tools::htmlentitiesUTF8($inHouseInfo['id_order']).'</a>',
                'class' => 'text-center',
            );

            $body[] = $tr;
        }

        return array('header' => array_values($header), 'body' => $body);
    }

    public function getNewBookingsTableContentsByDate($date, $idHotel)
    {
        $header = array(
            'order_id' => array('title' => $this->l('Order ID'), 'class' => 'text-center'),
            'name' => array('title' => $this->l('Customer Name'), 'class' => 'text-left'),
            'hotel' => array('title' => $this->l('Hotel'), 'class' => 'text-left'),
            'total' => array('title' => $this->l('Total Rooms'), 'class' => 'text-center'),
            'guests' => array('title' => $this->l('Guests'), 'class' => 'text-center'),
            'order_total' => array('title' => $this->l('Order Total'), 'class' => 'text-right'),
            'order_status' => array('title' => $this->l('Order Status'), 'class' => 'text-left'),
        );

        if ($idHotel != 0) {
            unset($header['hotel']);
        }

        $newBookingsInfo = AdminStatsController::getNewBookingsInfoByDate($date, $idHotel);

        $body = array();
        foreach ($newBookingsInfo as $newBookingInfo) {
            $objCurrency = Currency::getCurrency($newBookingInfo['id_currency']);

            $tr = array();
            $tr[] = array(
                'value' => '<a href="'.$this->context->link->getAdminLink('AdminOrders', true).'&id_order='.$newBookingInfo['id_order'].'&vieworder" target="_blank">#'.Tools::htmlentitiesUTF8($newBookingInfo['id_order']).'</a>',
                'class' => 'text-center',
            );
            $tr[] = array(
                'value' => '<a href="'.$this->context->link->getAdminLink('AdminCustomers', true).'&id_customer='.$newBookingInfo['id_customer'].'&viewcustomer" target="_blank">'.Tools::htmlentitiesUTF8($newBookingInfo['customer_name']).'</a>',
                'class' => 'text-left',
            );

            if ($idHotel == 0) {
                $tr[] = array(
                    'value' => '<a href="'.$this->context->link->getAdminLink('AdminAddHotel', true).'&id='.$newBookingInfo['id_hotel'].'&updatehtl_branch_info" target="_blank">'.Tools::htmlentitiesUTF8($newBookingInfo['hotel_name']).'</a>',
                    'class' => 'text-left',
                );
            }

            $tr[] = array(
                'value' => $newBookingInfo['total_rooms'],
                'class' => 'text-center',
            );
            $tr[] = array(
                'value' => $newBookingInfo['with_occupancy'] ? $newBookingInfo['total_guests'] : '--',
                'class' => 'text-center',
            );
            $tr[] = array(
                'value' => Tools::displayPrice($newBookingInfo['total_paid_tax_excl'], $objCurrency),
                'class' => 'text-right',
            );
            $tr[] = array(
                'value' => '<span class="label color_field" style="background-color:'.$newBookingInfo['state_color'].'">'.$newBookingInfo['state_name'].'</span>',
                'class' => 'text-left',
            );

            $body[] = $tr;
        }

        return array('header' => array_values($header), 'body' => $body);
    }

    public function getCancellationsTableContentsByDate($date, $idHotel)
    {
        $header = array(
            'request_id' => array('title' => $this->l('Request ID'), 'class' => 'text-center'),
            'name' => array('title' => $this->l('Customer Name'), 'class' => 'text-left'),
            'room_num' => array('title' => $this->l('Room No.'), 'class' => 'text-center'),
            'room_type' => array('title' => $this->l('Room Type'), 'class' => 'text-left'),
            'hotel' => array('title' => $this->l('Hotel'), 'class' => 'text-left'),
            'guests' => array('title' => $this->l('Guests'), 'class' => 'text-center'),
            'check_in' => array('title' => $this->l('Check-in'), 'class' => 'text-left'),
            'check_out' => array('title' => $this->l('Check-out'), 'class' => 'text-left'),
            'order_id' => array('title' => $this->l('Order ID'), 'class' => 'text-center'),
        );

        if ($idHotel != 0) {
            unset($header['hotel']);
        }

        $cancellationsInfo = AdminStatsController::getCancellationsInfoByDate($date, $idHotel);

        $body = array();
        foreach ($cancellationsInfo as $cancellationInfo) {
            $objCurrency = Currency::getCurrency($cancellationInfo['id_currency']);

            $tr = array();
            $tr[] = array(
                'value' => '<a href="'.$this->context->link->getAdminLink('AdminOrderRefundRequests', true).'&id_order_return='.$cancellationInfo['id_order_return'].'&vieworder_return" target="_blank">#'.Tools::htmlentitiesUTF8($cancellationInfo['id_order_return']).'</a>',
                'class' => 'text-center',
            );
            $tr[] = array(
                'value' => '<a href="'.$this->context->link->getAdminLink('AdminCustomers', true).'&id_customer='.$cancellationInfo['id_customer'].'&viewcustomer" target="_blank">'.Tools::htmlentitiesUTF8($cancellationInfo['customer_name']).'</a>',
                'class' => 'text-left',
            );
            $tr[] = array(
                'value' => $cancellationInfo['room_num'],
                'class' => 'text-center',
            );
            $tr[] = array(
                'value' => '<a href="'.$this->context->link->getAdminLink('AdminProducts', true).'&id_product='.$cancellationInfo['id_product'].'&updateproduct" target="_blank">'.Tools::htmlentitiesUTF8($cancellationInfo['room_type_name']).'</a>',
                'class' => 'text-left',
            );

            if ($idHotel == 0) {
                $tr[] = array(
                    'value' => '<a href="'.$this->context->link->getAdminLink('AdminAddHotel', true).'&id='.$cancellationInfo['id_hotel'].'&updatehtl_branch_info" target="_blank">'.Tools::htmlentitiesUTF8($cancellationInfo['hotel_name']).'</a>',
                    'class' => 'text-left',
                );
            }

            $tr[] = array(
                'value' => $cancellationInfo['with_occupancy'] ? $cancellationInfo['total_guests'] : '--',
                'class' => 'text-center',
            );
            $tr[] = array(
                'value' => Tools::displayDate($cancellationInfo['date_from']),
                'class' => 'text-left',
            );
            $tr[] = array(
                'value' => Tools::displayDate($cancellationInfo['date_to']),
                'class' => 'text-left',
            );
            $tr[] = array(
                'value' => '<a href="'.$this->context->link->getAdminLink('AdminOrders', true).'&id_order='.$cancellationInfo['id_order'].'&vieworder" target="_blank">#'.Tools::htmlentitiesUTF8($cancellationInfo['id_order']).'</a>',
                'class' => 'text-center',
            );

            $body[] = $tr;
        }

        return array('header' => array_values($header), 'body' => $body);
    }
}
