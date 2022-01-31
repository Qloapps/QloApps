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
            && $this->registerHook('dashboardUnnamedOne')
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

    public function hookDashboardUnnamedOne($params)
    {
        return $this->display(__FILE__, 'dashboard-top.tpl');
    }

    public function hookDashboardData($params)
    {
        $data = array();
        if (Configuration::get('PS_DASHBOARD_SIMULATION')) {
            $data['dgc_total_arrivals'] = sprintf('%02d', rand(100, 1000));
            $data['dgc_arrived'] = sprintf('%02d', rand(0, $data['dgc_total_arrivals']));
            $data['dgc_total_departures'] = sprintf('%02d', rand(100, 1000));
            $data['dgc_departed'] = sprintf('%02d', rand(0, $data['dgc_total_departures']));
            $data['dgc_new_bookings'] = sprintf('%02d', rand(10, 500));
            $data['dgc_stay_overs'] = sprintf('%02d', rand(10, 500));
            $data['dgc_new_messages'] = sprintf('%02d', rand(0, 20));
            $data['dgc_cancelled_bookings'] = sprintf('%02d', rand(0, 20));
            $data['dgc_guests_adults'] = sprintf('%02d', rand(100, 1000));
            $data['dgc_guests_children'] = sprintf('%02d', rand(0, $data['dgc_guests_adults']));
        } else {
            $dateToday = date('Y-m-d');
            $arrivalsData = AdminStatsController::getArrivalsByDate($dateToday);
            $departuresData = AdminStatsController::getDeparturesByDate($dateToday);
            $guestsData = AdminStatsController::getGuestsByDate($dateToday);
            $data['dgc_arrived'] = sprintf('%02d', $arrivalsData['arrived']);
            $data['dgc_total_arrivals'] = sprintf('%02d', $arrivalsData['total_arrivals']);
            $data['dgc_departed'] = sprintf('%02d', $departuresData['departed']);
            $data['dgc_total_departures'] = sprintf('%02d', $departuresData['total_departures']);
            $data['dgc_new_bookings'] = sprintf('%02d', AdminStatsController::getBookingsByDate($dateToday));
            $data['dgc_stay_overs'] = sprintf('%02d', AdminStatsController::getStayOversByDate($dateToday));
            $data['dgc_new_messages'] = sprintf('%02d', CustomerMessage::getMessagesByDate($dateToday));
            $data['dgc_cancelled_bookings'] = sprintf('%02d', AdminStatsController::getCancelledBookingsByDate($dateToday));
            $data['dgc_guests_adults'] = sprintf('%02d', $guestsData['adults']);
            $data['dgc_guests_children'] = sprintf('%02d', $guestsData['children']);
        }

        return array('data_value' => $data);
    }
}
