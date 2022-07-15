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
        $this->version = '1.0.0';
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
            $this->context->controller->addJs($this->_path.'views/js/'.$this->name.'.js');
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
            $data['dp_average_daily_rate'] = Tools::displayPrice(round(rand(2000, 20000)));
            $data['dp_cancellation_rate'] = (round(rand(1, 1000), 2) / 100).'%';
            $data['dp_revenue'] = Tools::displayPrice(round(rand(20000, 70000)));
            $data['dp_nights_stayed'] = rand(100, 1000);
        } else {
            $data['dp_average_daily_rate'] = AdminStatsController::getAverageDailyRate(
                $params['date_from'],
                $params['date_to'],
                $params['id_hotel']
            );
            $data['dp_cancellation_rate'] = AdminStatsController::getCancellationRate(
                $params['date_from'],
                $params['date_to'],
                $params['id_hotel']
            );
            $data['dp_revenue'] = AdminStatsController::getRevenue(
                $params['date_from'],
                $params['date_to'],
                $params['id_hotel']
            );
            $data['dp_nights_stayed'] = AdminStatsController::getNightsStayed(
                $params['date_from'],
                $params['date_to'],
                $params['id_hotel']
            );
        }

        return array('data_value' => $data);
    }
}
