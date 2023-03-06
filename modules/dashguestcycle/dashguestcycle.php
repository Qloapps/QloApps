<?php
/**
* 2010-2021 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2021 Webkul IN
* @license LICENSE.txt
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
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6');
        $this->author = 'Webkul';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Dashboard Guest Cycle');
        $this->description = $this->l('Adds a block with a graphical representation of guest cycle stats.');
        $this->confirmUnsinstall = $this->l('Are you sure you want to uninstall?');

        $this->allow_push = true;
    }

    public function install()
    {
        return (parent::install()
            && $this->registerHook('dashboardTop')
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

    public function hookDashboardTop()
    {
        return $this->display(__FILE__, 'dashboard-top.tpl');
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
        if (Configuration::get('PS_DASHBOARD_SIMULATION')) {
            $dataValue['dgc_total_arrivals'] = sprintf('%02d', rand(100, 1000));
            $dataValue['dgc_arrived'] = sprintf('%02d', rand(0, $dataValue['dgc_total_arrivals']));
            $dataValue['dgc_total_departures'] = sprintf('%02d', rand(100, 1000));
            $dataValue['dgc_departed'] = sprintf('%02d', rand(0, $dataValue['dgc_total_departures']));
            $dataValue['dgc_new_bookings'] = sprintf('%02d', rand(10, 500));
            $dataValue['dgc_stay_overs'] = sprintf('%02d', rand(10, 500));
            $dataValue['dgc_new_messages'] = sprintf('%02d', rand(0, 20));
            $dataValue['dgc_cancelled_bookings'] = sprintf('%02d', rand(0, 20));
            $dataValue['dgc_guests_adults'] = sprintf('%02d', rand(100, 1000));
            $dataValue['dgc_guests_children'] = sprintf('%02d', rand(0, $dataValue['dgc_guests_adults']));
        } else {
            // set badges data
            $arrivalsData = AdminStatsController::getArrivalsByDate($dateToday, $params['id_hotel']);
            $departuresData = AdminStatsController::getDeparturesByDate($dateToday, $params['id_hotel']);
            $guestsData = AdminStatsController::getGuestsByDate($dateToday, $params['id_hotel']);
            $dataValue['dgc_arrived'] = sprintf('%02d', $arrivalsData['arrived']);
            $dataValue['dgc_total_arrivals'] = sprintf('%02d', $arrivalsData['total_arrivals']);
            $dataValue['dgc_departed'] = sprintf('%02d', $departuresData['departed']);
            $dataValue['dgc_total_departures'] = sprintf('%02d', $departuresData['total_departures']);
            $dataValue['dgc_new_bookings'] = sprintf('%02d', AdminStatsController::getBookingsByDate($dateToday, $params['id_hotel']));
            $dataValue['dgc_stay_overs'] = sprintf('%02d', AdminStatsController::getStayOversByDate($dateToday, $params['id_hotel']));
            $dataValue['dgc_new_messages'] = sprintf('%02d', CustomerMessage::getMessagesByDate($dateToday));
            $dataValue['dgc_cancelled_bookings'] = sprintf('%02d', AdminStatsController::getCancelledBookingsByDate($dateToday, $params['id_hotel']));
            $dataValue['dgc_guests_adults'] = sprintf('%02d', $guestsData['adults']);
            $dataValue['dgc_guests_children'] = sprintf('%02d', $guestsData['children']);
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
                'value' => $arrivalInfo['adults'] + $arrivalInfo['children'],
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
                'value' => $departureInfo['adults'] + $departureInfo['children'],
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
                'value' => $inHouseInfo['adults'] + $inHouseInfo['children'],
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
                'value' => $newBookingInfo['total_guests'],
                'class' => 'text-center',
            );
            $tr[] = array(
                'value' => Tools::displayPrice($newBookingInfo['total_paid_tax_excl'], $objCurrency),
                'class' => 'text-right',
            );
            $tr[] = array(
                'value' => $newBookingInfo['state_name'],
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
                'value' => $cancellationInfo['total_guests'],
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
