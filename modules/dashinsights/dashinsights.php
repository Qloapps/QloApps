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
            && $this->updateHookPositions()
        );
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('controller') == 'AdminDashboard') {
            $this->context->controller->addCSS($this->_path.'views/css/'.$this->name.'.css');
            $this->context->controller->addJS($this->_path.'views/js/'.$this->name.'.js');
        }
    }

    public function updateHookPositions()
    {
        $idHook = Hook::getIdByName('dashboardZoneOne');
        $result = $this->updatePosition($idHook, 0, 2);

        $idHook = Hook::getIdByName('dashboardZoneTwo');
        $result &= $this->updatePosition($idHook, 0, 6);

        return $result;
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

        // set common variables
        $this->accessibleIdsHotel = HotelBranchInformation::getProfileAccessedHotels($this->context->employee->id_profile, 1, 1);

        // set dates info
        $this->discreteDates = array();
        $dateTemp = $dateFrom;
        while ($dateTemp <= $dateTo) {
            $dateNext = date('Y-m-d', strtotime('+1 day', strtotime($dateTemp)));
            $this->discreteDates[] = array(
                'timestamp_from' => strtotime($dateTemp),
            );
            $dateTemp = $dateNext;
        };

        // set colors to be used for charts
        $this->chartColors = array('#545fd5', '#fc7c63');

        // get data now
        $roomNightsData = $this->getRoomNightsData($dateFrom, $dateTo, $idHotel);
        $daysOfTheWeekData = $this->getDaysOfTheWeekData($dateFrom, $dateTo, $idHotel);
        $lengthOfStayData = $this->getLengthOfStayData($dateFrom, $dateTo, $idHotel);

        // set labels
        $panelsLabel = '';
        if ($idHotel) {
            $objHotelBranchInformation = new HotelBranchInformation($idHotel, $this->context->language->id);
            $addressInfo = HotelBranchInformation::getAddress($idHotel, $this->context->language->id);
            $panelsLabel = sprintf('(%s, %s)', $objHotelBranchInformation->hotel_name, $addressInfo['city']);
        } else {
            $panelsLabel = $this->l('(All Hotels)');
        }

        $return = array(
            'data_value' => array(
                'dashinsights_heading_zone_one' => $panelsLabel,
                'dashinsights_heading_zone_two' => $panelsLabel,
            ),
            'data_chart' => array(
                'dashinsights_line_chart1' => array(
                    'chart_type' => 'line_chart_dashinsights',
                    'data' => $roomNightsData,
                    'date_format' => $this->context->language->date_format_lite,
                ),
                'dashinsights_multibar_chart1' => array(
                    'chart_type' => 'multibar_chart_dotw_dashinsights',
                    'data' => $daysOfTheWeekData,
                    'date_format' => $this->context->language->date_format_lite,
                ),
                'dashinsights_multibar_chart2' => array(
                    'chart_type' => 'multibar_chart_los_dashinsights',
                    'data' => $lengthOfStayData,
                    'date_format' => $this->context->language->date_format_lite,
                    'axis_labels' => array('x' => $this->l('Days')),
                    'y_format' => '%',
                ),
            ),
        );

        return $return;
    }

    public function getRoomNightsData($dateFrom, $dateTo, $idHotel)
    {
        $seriesWiseRoomNights = array();
        if (Configuration::get('PS_DASHBOARD_SIMULATION')) {
            // set room nights demo data
            if ($idHotel == 0) { // if 'All Hotels' is selected
                $allHotelSeriesInfo = array(
                    'data' => array(),
                    'label' => $this->l('All Hotels'),
                );

                foreach ($this->discreteDates as $discreteDate) {
                    $allHotelSeriesInfo['data'][$discreteDate['timestamp_from']] = rand(1, 100);
                }
                $seriesWiseRoomNights[] = $allHotelSeriesInfo;
            } else { // if one of the hotels is selected
                $hotelRoomNights = array();
                $otherHotelsRoomNights = array();

                // current hotel series info
                $objHotelBranchInformation = new HotelBranchInformation($idHotel, $this->context->language->id);
                $currentHotelRoomNightsData = array();
                foreach ($this->discreteDates as $discreteDate) {
                    $currentHotelRoomNightsData[$discreteDate['timestamp_from']] = rand(1, 100);
                }

                $currentHotelSeriesInfo = array(
                    'data' => $currentHotelRoomNightsData,
                    'label' => $objHotelBranchInformation->hotel_name,
                );

                // average series info
                $averageRoomNightsData = array();
                foreach ($this->discreteDates as $discreteDate) {
                    $averageRoomNightsData[$discreteDate['timestamp_from']] = sprintf('%0.2f', rand(1, 10000) / 100);
                }

                $averageSeriesInfo = array(
                    'data' => $averageRoomNightsData,
                    'label' => $this->l('Others Average'),
                );

                $seriesWiseRoomNights = array(
                    $currentHotelSeriesInfo,
                    $averageSeriesInfo,
                );
            }
        } else {
            if ($idHotel == 0) { // if 'All Hotels' is selected
                $idsHotel = $this->accessibleIdsHotel;
                $allHotelSeriesInfo = array(
                    'data' => AdminStatsController::getRoomNightsData($dateFrom, $dateTo, $idsHotel),
                    'label' => $this->l('All Hotels'),
                );

                $seriesWiseRoomNights[] = $allHotelSeriesInfo;
            } else { // if one of the hotels is selected
                $hotelRoomNights = array();
                $otherHotelsRoomNights = array();

                $objHotelBranchInformation = new HotelBranchInformation($idHotel, $this->context->language->id);
                $currentHotelRoomNightsData = AdminStatsController::getRoomNightsData($dateFrom, $dateTo, $idHotel);

                $currentHotelSeriesInfo = array(
                    'data' => $currentHotelRoomNightsData,
                    'label' => $objHotelBranchInformation->hotel_name,
                );

                $seriesWiseRoomNights[] = $currentHotelSeriesInfo;

                // calculate average of other hotels
                $idsHotel = $this->accessibleIdsHotel;
                if (($key = array_search($idHotel, $idsHotel)) !== false) {
                    unset($idsHotel[$key]);
                }

                if (count($idsHotel) > 0) { // display average series only if other hotels are available
                    $averageRoomNightsData = AdminStatsController::getRoomNightsData($dateFrom, $dateTo, $idsHotel, true, true);
                    $averageSeriesInfo = array(
                        'data' => $averageRoomNightsData,
                        'label' => $this->l('Others Average'),
                    );

                    $seriesWiseRoomNights[] = $averageSeriesInfo;
                }
            }
        }

        // format room nights data
        $roomNightsFormattedData = array();
        $colorIndex = 0;
        foreach ($seriesWiseRoomNights as $key => &$hotelRoomNights) {
            $hotelData = array();
            foreach ($hotelRoomNights['data'] as $timestamp => $hotelRoomNight) {
                $hotelData[] = array($timestamp, $hotelRoomNight);
            }

            $roomNightsFormattedData[] = array(
                'key' => $hotelRoomNights['label'],
                'values' => $hotelData,
                'color' => $this->chartColors[$colorIndex++],
            );
        }

        return $roomNightsFormattedData;
    }

    public function getDaysOfTheWeekData($dateFrom, $dateTo, $idHotel)
    {
        $seriesWiseDaysOfTheWeek = array();
        if (Configuration::get('PS_DASHBOARD_SIMULATION')) {
            // set room nights demo data
            if ($idHotel == 0) { // if 'All Hotels' is selected
                $allHotelSeriesInfo = array(
                    'data' => array(),
                    'label' => $this->l('All Hotels'),
                );

                // 1 = SUN
                for ($i = 1; $i <= 7; $i++) {
                    $allHotelSeriesInfo['data'][$i] = round(rand(0, 100));
                }

                $seriesWiseDaysOfTheWeek[] = $allHotelSeriesInfo;
            } else { // if one of the hotels is selected
                $hotelDaysOfTheWeek = array();
                $otherHotelsDaysOfTheWeek = array();

                // current hotel series info
                $objHotelBranchInformation = new HotelBranchInformation($idHotel, $this->context->language->id);
                $currentHotelDaysOfTheWeekData = array();
                for ($i = 1; $i <= 7; $i++) {
                    $currentHotelDaysOfTheWeekData[$i] = round(rand(0, 100));
                }

                $currentHotelSeriesInfo = array(
                    'data' => $currentHotelDaysOfTheWeekData,
                    'label' => $objHotelBranchInformation->hotel_name,
                );

                // average series info
                $averageDaysOfTheWeekData = array();
                for ($i = 1; $i <= 7; $i++) {
                    $averageDaysOfTheWeekData[$i] = sprintf('%0.2f', rand(1, 10000) / 100);
                }

                $averageSeriesInfo = array(
                    'data' => $averageDaysOfTheWeekData,
                    'label' => $this->l('Others Average'),
                );

                $seriesWiseDaysOfTheWeek = array(
                    $currentHotelSeriesInfo,
                    $averageSeriesInfo,
                );
            }
        } else {
            if ($idHotel == 0) { // if 'All Hotels' is selected
                $idsHotel = $this->accessibleIdsHotel;
                $allHotelSeriesInfo = array(
                    'data' => AdminStatsController::getOccupiedRoomsForDaysOfTheWeek($dateFrom, $dateTo, $idsHotel),
                    'label' => $this->l('All Hotels'),
                );

                $seriesWiseDaysOfTheWeek[] = $allHotelSeriesInfo;
            } else { // if one of the hotels is selected
                $hotelDaysOfTheWeek = array();
                $otherHotelsDaysOfTheWeek = array();

                $objHotelBranchInformation = new HotelBranchInformation($idHotel, $this->context->language->id);
                $currentHotelDaysOfTheWeekData = AdminStatsController::getOccupiedRoomsForDaysOfTheWeek($dateFrom, $dateTo, $idHotel);

                $currentHotelSeriesInfo = array(
                    'data' => $currentHotelDaysOfTheWeekData,
                    'label' => $objHotelBranchInformation->hotel_name,
                );

                $seriesWiseDaysOfTheWeek[] = $currentHotelSeriesInfo;

                // calculate average of other hotels
                $idsHotel = $this->accessibleIdsHotel;
                if (($key = array_search($idHotel, $idsHotel)) !== false) {
                    unset($idsHotel[$key]);
                }

                if (count($idsHotel) > 0) { // display average series only if other hotels are available
                    $averageDaysOfTheWeekData = AdminStatsController::getOccupiedRoomsForDaysOfTheWeek($dateFrom, $dateTo, $idsHotel, true, true);
                    $averageSeriesInfo = array(
                        'data' => $averageDaysOfTheWeekData,
                        'label' => $this->l('Others Average'),
                    );

                    $seriesWiseDaysOfTheWeek[] = $averageSeriesInfo;
                }
            }
        }

        // format days of the week data
        $daysOfTheWeekFormattedData = array();
        $colorIndex = 0;
        $weekDays = array(
            $this->l('SUN'),
            $this->l('MON'),
            $this->l('TUE'),
            $this->l('WED'),
            $this->l('THU'),
            $this->l('FRI'),
            $this->l('SAT'),
        );

        foreach ($seriesWiseDaysOfTheWeek as $key => &$hotelDaysOfTheWeek) {
            $hotelData = array();
            foreach ($hotelDaysOfTheWeek['data'] as $dayOfWeek => $hotelDayOfTheWeek) {
                $hotelData[] = array(
                    'x' => $weekDays[$dayOfWeek - 1],
                    'y' => $hotelDayOfTheWeek,
                );
            }

            $daysOfTheWeekFormattedData[] = array(
                'key' => $hotelDaysOfTheWeek['label'],
                'values' => $hotelData,
                'color' => $this->chartColors[$colorIndex++],
            );
        }

        return $daysOfTheWeekFormattedData;
    }

    public function getLengthOfStayData($dateFrom, $dateTo, $idHotel)
    {
        $seriesWiseLengthOfStay = array();

        // day ranges to get length of stay data
        $day = array(
            1 => array(1, 1),
            2 => array(2, 2),
            3 => array(3, 3),
            4 => array(4, 4),
            5 => array(5, 5),
            6 => array(6, 6),
            7 => array(7, 100),
        );

        if (Configuration::get('PS_DASHBOARD_SIMULATION')) {
            // set room nights demo data
            if ($idHotel == 0) { // if 'All Hotels' is selected
                $allHotelSeriesInfo = array(
                    'data' => array(),
                    'label' => $this->l('All Hotels'),
                );

                for ($i = 1; $i <= 7; $i++) {
                    $allHotelSeriesInfo['data'][$i] = sprintf('%0.2f', rand(1, 100) / 100);
                }

                $seriesWiseLengthOfStay[] = $allHotelSeriesInfo;
            } else { // if one of the hotels is selected
                $hotelLengthOfStay = array();
                $otherHotelsLengthOfStay = array();

                // current hotel series info
                $objHotelBranchInformation = new HotelBranchInformation($idHotel, $this->context->language->id);
                $currentHotelLengthOfStayData = array();
                for ($i = 1; $i <= 7; $i++) {
                    $currentHotelLengthOfStayData[$i] = sprintf('%0.2f', rand(1, 100) / 100);
                }

                $currentHotelSeriesInfo = array(
                    'data' => $currentHotelLengthOfStayData,
                    'label' => $objHotelBranchInformation->hotel_name,
                );

                // average series info
                $averageLengthOfStayData = array();
                for ($i = 1; $i <= 7; $i++) {
                    $averageLengthOfStayData[$i] = sprintf('%0.2f', rand(1, 100) / 100);
                }

                $averageSeriesInfo = array(
                    'data' => $averageLengthOfStayData,
                    'label' => $this->l('Others Average'),
                );

                $seriesWiseLengthOfStay = array(
                    $currentHotelSeriesInfo,
                    $averageSeriesInfo,
                );
            }
        } else {
            if ($idHotel == 0) { // if 'All Hotels' is selected
                $idsHotel = $this->accessibleIdsHotel;
                $allHotelSeriesInfo = array(
                    'data' => AdminStatsController::getLengthOfStayPercentages($day, $dateFrom, $dateTo, $idsHotel),
                    'label' => $this->l('All Hotels'),
                );

                $seriesWiseLengthOfStay[] = $allHotelSeriesInfo;
            } else { // if one of the hotels is selected
                $hotelLengthOfStay = array();
                $otherHotelsLengthOfStay = array();

                $objHotelBranchInformation = new HotelBranchInformation($idHotel, $this->context->language->id);
                $currentHotelLengthOfStayData = AdminStatsController::getLengthOfStayPercentages($day, $dateFrom, $dateTo, $idHotel);

                $currentHotelSeriesInfo = array(
                    'data' => $currentHotelLengthOfStayData,
                    'label' => $objHotelBranchInformation->hotel_name,
                );

                $seriesWiseLengthOfStay[] = $currentHotelSeriesInfo;

                // calculate average of other hotels
                $idsHotel = $this->accessibleIdsHotel;
                if (($key = array_search($idHotel, $idsHotel)) !== false) {
                    unset($idsHotel[$key]);
                }

                if (count($idsHotel) > 0) { // display average series only if other hotels are available
                    $averageLengthOfStayData = AdminStatsController::getLengthOfStayPercentages($day, $dateFrom, $dateTo, $idsHotel, true, true);
                    $averageSeriesInfo = array(
                        'data' => $averageLengthOfStayData,
                        'label' => $this->l('Others Average'),
                    );

                    $seriesWiseLengthOfStay[] = $averageSeriesInfo;
                }
            }
        }

        // format length of stay data
        $lengthOfStayFormattedData = array();
        $colorIndex = 0;
        $losInfos = array(
            array('days' => 1, 'label' => $this->l('1')),
            array('days' => 2, 'label' => $this->l('2')),
            array('days' => 3, 'label' => $this->l('3')),
            array('days' => 4, 'label' => $this->l('4')),
            array('days' => 5, 'label' => $this->l('5')),
            array('days' => 6, 'label' => $this->l('6')),
            array('days' => 7, 'label' => $this->l('7+')),
        );

        foreach ($seriesWiseLengthOfStay as $idHotel => &$hotelLengthOfStay) {
            $hotelData = array();
            foreach ($hotelLengthOfStay['data'] as $numDays => $lengthOfStay) {
                $hotelData[] = array(
                    'x' => $losInfos[$numDays - 1]['label'],
                    'y' => $lengthOfStay,
                );
            }

            $lengthOfStayFormattedData[] = array(
                'key' => $hotelLengthOfStay['label'],
                'values' => $hotelData,
                'color' => $this->chartColors[$colorIndex++],
            );
        }

        return $lengthOfStayFormattedData;
    }
}
