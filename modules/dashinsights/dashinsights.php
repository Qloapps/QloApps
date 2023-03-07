<?php
/**
* 2010-2023 Webkul.
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
* @copyright 2010-2023 Webkul IN
* @license LICENSE.txt
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class DashInsights extends Module
{
    public function __construct()
    {
        $this->name = 'dashinsights';
        $this->tab = 'dashboard';
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6');
        $this->author = 'Webkul';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Dashboard Booking Insights');
        $this->description = $this->l('Adds a block with a graphical representation of booking insights.');
        $this->confirmUnsinstall = $this->l('Are you sure you want to uninstall?');

        $this->allow_push = true;
    }

    public function install()
    {
        return (parent::install()
            && $this->registerHook('dashboardZoneOne')
            && $this->registerHook('dashboardZoneTwo')
            && $this->registerHook('dashboardData')
            && $this->registerHook('actionAdminControllerSetMedia')
        );
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('controller') == 'AdminDashboard') {
            $this->context->controller->addCSS($this->_path.'views/css/'.$this->name.'.css');
            $this->context->controller->addJS($this->_path.'views/js/'.$this->name.'.js');
        }
    }

    public function hookDashboardZoneOne()
    {
        return $this->display(__FILE__, 'dashboard-zone-one.tpl');
    }

    public function hookDashboardZoneTwo()
    {
        return $this->display(__FILE__, 'dashboard-zone-two.tpl');
    }

    public function hookDashboardData($params)
    {
        $dateFrom = $params['date_from'];
        $dateTo = $params['date_to'];
        $idHotel = $params['id_hotel'];

        $idsHotel = array();
        if (!$idHotel) {
            $idsHotel = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 1);
        } else {
            $idsHotel[] = $idHotel;
        }

        $hotelWiseRoomNights = array();
        $hotelWiseDaysOfTheWeek = array();
        $hotelWiseLengthOfStay = array();
        if (Configuration::get('PS_DASHBOARD_SIMULATION')) {
            if ($dateFrom == $dateTo) {
                $dateTo = date('Y-m-d', strtotime('+1 day', strtotime($dateTo)));
            }

            // prepare dates
            $discreteDates = array();
            $dateTemp = $dateFrom;
            while ($dateTemp <= $dateTo) {
                $dateNext = date('Y-m-d', strtotime('+1 day', strtotime($dateTemp)));
                $discreteDates[] = array(
                    'timestamp_from' => strtotime($dateTemp),
                );
                $dateTemp = $dateNext;
            };

            // set room nights demo data
            foreach ($idsHotel as $idHotel) {
                foreach ($discreteDates as $discreteDate) {
                    $hotelWiseRoomNights[$idHotel][$discreteDate['timestamp_from']] = round(rand(0, 100));
                }
            }

            // set days of the week demo data
            foreach ($idsHotel as $idHotel) {
                for ($i = 1; $i <= 7; $i++) {
                    $hotelWiseDaysOfTheWeek[$idHotel][$i] = round(rand(0, 100));
                }
            }

            // set length of stay demo data
            foreach ($idsHotel as $idHotel) {
                for ($i = 1; $i <= 7; $i++) {
                    $hotelWiseLengthOfStay[$idHotel][$i] = round(rand(0, 15));
                }
            }
        } else {
            $hotelWiseRoomNights = AdminStatsController::getRoomNightsData($dateFrom, $dateTo, $idHotel);
            $hotelWiseDaysOfTheWeek = AdminStatsController::getOccupiedRoomsForDaysOfTheWeek($dateFrom, $dateTo, $idHotel);

            $days = array(
                1 => array(1, 1),
                2 => array(2, 2),
                3 => array(3, 3),
                4 => array(4, 4),
                5 => array(5, 5),
                6 => array(6, 6),
                7 => array(7, 100),
            );

            $hotelWiseLengthOfStay = AdminStatsController::getLengthOfStayPercentages($days, $dateFrom, $dateTo, $idHotel);
        }

        // format room nights data
        $roomNightsFormattedData = array();
        foreach ($hotelWiseRoomNights as $idHotel => &$hotelRoomNights) {
            $hotelData = array();
            foreach ($hotelRoomNights as $timestamp => $hotelRoomNight) {
                $hotelData[] = array($timestamp, $hotelRoomNight);
            }

            $objHotelBranchInformation = new HotelBranchInformation($idHotel, $this->context->language->id);
            $roomNightsFormattedData[] = array(
                'key' => $objHotelBranchInformation->hotel_name,
                'values' => $hotelData,
            );
        }

        // format days of the week data
        $daysOfTheWeekFormattedData = array();
        $weekDays = array(
            $this->l('SUN'),
            $this->l('MON'),
            $this->l('TUE'),
            $this->l('WED'),
            $this->l('THU'),
            $this->l('FRI'),
            $this->l('SAT'),
        );
        foreach ($hotelWiseDaysOfTheWeek as $idHotel => &$hotelDaysOfTheWeek) {
            $hotelData = array();
            foreach ($hotelDaysOfTheWeek as $dayOfWeek => $hotelDayOfTheWeek) {
                $hotelData[] = array(
                    'x' => $weekDays[$dayOfWeek - 1],
                    'y' => $hotelDayOfTheWeek,
                );
            }

            $objHotelBranchInformation = new HotelBranchInformation($idHotel, $this->context->language->id);
            $daysOfTheWeekFormattedData[] = array(
                'key' => $objHotelBranchInformation->hotel_name,
                'values' => $hotelData,
            );
        }

        // format length of stay data
        $lengthOfStayFormattedData = array();
        $losInfos = array(
            array('days' => 1, 'label' => $this->l('1')),
            array('days' => 2, 'label' => $this->l('2')),
            array('days' => 3, 'label' => $this->l('3')),
            array('days' => 4, 'label' => $this->l('4')),
            array('days' => 5, 'label' => $this->l('5')),
            array('days' => 6, 'label' => $this->l('6')),
            array('days' => 7, 'label' => $this->l('7+')),
        );
        foreach ($hotelWiseLengthOfStay as $idHotel => &$hotelLengthOfStay) {
            $hotelData = array();
            foreach ($hotelLengthOfStay as $numDays => $hotelLengthOfStay) {
                $hotelData[] = array(
                    'x' => $losInfos[$numDays - 1]['label'],
                    'y' => $hotelLengthOfStay,
                );
            }

            $objHotelBranchInformation = new HotelBranchInformation($idHotel, $this->context->language->id);
            $lengthOfStayFormattedData[] = array(
                'key' => $objHotelBranchInformation->hotel_name,
                'values' => $hotelData,
            );
        }

        $return = array(
            'data_chart' => array(
                'dashinsights_line_chart1' => array(
                    'chart_type' => 'line_chart_dashinsights',
                    'data' => $roomNightsFormattedData,
                    'date_format' => $this->context->language->date_format_lite,
                ),
                'dashinsights_multibar_chart1' => array(
                    'chart_type' => 'multibar_chart_dotw_dashinsights',
                    'data' => $daysOfTheWeekFormattedData,
                    'date_format' => $this->context->language->date_format_lite,
                ),
                'dashinsights_multibar_chart2' => array(
                    'chart_type' => 'multibar_chart_los_dashinsights',
                    'data' => $lengthOfStayFormattedData,
                    'date_format' => $this->context->language->date_format_lite,
                    'axis_labels' => array('x' => $this->l('Days')),
                ),
            ),
        );

        return $return;
    }
}
