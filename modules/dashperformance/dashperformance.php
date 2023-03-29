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

class DashPerformance extends Module
{
    public function __construct()
    {
        $this->name = 'dashperformance';
        $this->tab = 'dashboard';
        $this->version = '1.0.1';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6');
        $this->author = 'Webkul';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Dashboard Performance');
        $this->description = $this->l('Adds a block with a graphical representation of performance of your website.');
        $this->confirmUnsinstall = $this->l('Are you sure you want to uninstall?');

        $this->allow_push = true;
    }

    public function install()
    {
        return (parent::install()
            && $this->registerHook('dashboardZoneTwo')
            && $this->registerHook('dashboardData')
            && $this->registerHook('actionAdminControllerSetMedia')
        );
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (get_class($this->context->controller) == 'AdminDashboardController') {
            $this->context->controller->addCSS($this->_path.'views/css/'.$this->name.'.css');
        }
    }

    public function hookDashboardZoneTwo($params)
    {
        return $this->display(__FILE__, 'dashboard_zone_two.tpl');
    }

    public function hookDashboardData($params)
    {
        $data = array();
        if (Configuration::get('PS_DASHBOARD_SIMULATION')) {
            $data['dp_average_daily_rate'] = Tools::displayPrice(sprintf('%0.2f', rand(100000, 1000000) / 100));
            $data['dp_total_revenue_per_available_room'] = Tools::displayPrice(sprintf('%0.2f', rand(pow(10, 5), pow(10, 6)) / 100));
            $data['dp_average_occupancy_rate'] = sprintf('%0.2f', rand(5000, 10000) / 100).'%';
            $data['dp_revenue_per_available_room'] = Tools::displayPrice(sprintf('%0.2f', rand(pow(10, 5), pow(10, 6)) / 100));
            $data['dp_gross_operating_profit_par'] = Tools::displayPrice(sprintf('%0.2f', rand(pow(10, 6), pow(10, 7)) / 100));
            $data['dp_average_length_of_stay'] = sprintf('%0.2f', rand(1, 500) / 100);
            $data['dp_direct_revenue_ratio'] = sprintf('%0.2f', rand(4000, 8000) / 100).'%';
            $data['dp_cancellation_rate'] = sprintf('%0.2f', rand(1, 1000) / 100).'%';
        } else {
            $data['dp_average_daily_rate'] = Tools::displayPrice(AdminStatsController::getAverageDailyRate(
                $params['date_from'],
                $params['date_to'],
                $params['id_hotel']
            ));
            $data['dp_total_revenue_per_available_room'] = Tools::displayPrice(AdminStatsController::getTotalRevenuePerAvailableRoom(
                $params['date_from'],
                $params['date_to'],
                $params['id_hotel']
            ));
            $data['dp_average_occupancy_rate'] = sprintf('%0.2f', AdminStatsController::getAverageOccupancyRate(
                $params['date_from'],
                $params['date_to'],
                $params['id_hotel']
            )).'%';
            $data['dp_revenue_per_available_room'] = Tools::displayPrice(AdminStatsController::getRevenuePerAvailableRoom(
                $params['date_from'],
                $params['date_to'],
                $params['id_hotel']
            ));
            $data['dp_gross_operating_profit_par'] = Tools::displayPrice(AdminStatsController::getGrossOperatingProfitPerAvailableRoom(
                $params['date_from'],
                $params['date_to'],
                $params['id_hotel']
            ));
            $data['dp_average_length_of_stay'] = sprintf('%0.2f', AdminStatsController::getAverageLengthOfStay(
                $params['date_from'],
                $params['date_to'],
                $params['id_hotel']
            ));
            $data['dp_direct_revenue_ratio'] = sprintf('%0.2f', AdminStatsController::getDirectRevenueRatio(
                $params['date_from'],
                $params['date_to'],
                $params['id_hotel']
            )).'%';
            $data['dp_cancellation_rate'] = sprintf('%0.2f', AdminStatsController::getCancellationRate(
                $params['date_from'],
                $params['date_to'],
                $params['id_hotel']
            )).'%';
        }

        return array('data_value' => $data);
    }
}
