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

class DashOccupancy extends Module
{
    public function __construct()
    {
        $this->name = 'dashoccupancy';
        $this->tab = 'dashboard';
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6');
        $this->author = 'Webkul';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Dashboard Occupancy');
        $this->description = $this->l('Adds a block with a graphical representation of occupancy of your hotel`s room.');
        $this->confirmUnsinstall = $this->l('Are you sure you want to uninstall?');

        $this->allow_push = true;
    }

    public function install()
    {
        return (parent::install()
            && $this->registerHook('dashboardZoneOne')
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

    public function hookDashboardZoneOne($params)
    {
        $occupancyData = AdminStatsController::getOccupancyData(
            $params['date_from'],
            $params['date_to'],
            $params['id_hotel']
        );

        $this->context->smarty->assign(array(
            'count_total' =>  sprintf('%02d', $occupancyData['count_total']),
            'count_occupied' => sprintf('%02d', $occupancyData['count_occupied']),
            'count_available' =>  sprintf('%02d', $occupancyData['count_available']),
            'count_unavailable' =>  sprintf('%02d', $occupancyData['count_unavailable']),
            'date_occupancy_range' => $this->l('(from %s to %s)'),
            'date_occupancy_avail_format' => $this->context->language->date_format_lite,
        ));

        return $this->display(__FILE__, 'dashboard_zone_one.tpl');
    }

    public function hookDashboardData($params)
    {
        $data = array();
        if (Configuration::get('PS_DASHBOARD_SIMULATION')) {
            $occupancyData = array();
            $occupancyData['count_total'] = sprintf('%02d', rand(0, 1000));
            $tmp = $occupancyData['count_total'];
            $occupancyData['count_occupied'] = sprintf('%02d', round(rand(0, $occupancyData['count_total'])));
            $tmp = $tmp - $occupancyData['count_occupied'];
            $occupancyData['count_available'] = sprintf('%02d', round(rand(0, $tmp)));
            $tmp = $tmp - $occupancyData['count_available'];
            $occupancyData['count_unavailable'] = sprintf('%02d', $tmp);
        } else {
            $occupancyData = AdminStatsController::getOccupancyData(
                $params['date_from'],
                $params['date_to'],
                $params['id_hotel']
            );
        }

        $chartData = array(
            array(
                'label' => $this->l('Occupied'),
                'value' => $occupancyData['count_total']
                    ? ($occupancyData['count_occupied'] / $occupancyData['count_total']) * 100
                    : 0,
            ),
            array(
                'label' => $this->l('Available'),
                'value' => $occupancyData['count_total']
                    ? ($occupancyData['count_available'] / $occupancyData['count_total']) * 100
                    : 0,
            ),
            array(
                'label' => $this->l('Unavailable'),
                'value' => $occupancyData['count_total']
                    ? ($occupancyData['count_unavailable'] / $occupancyData['count_total']) * 100
                    : 0,
            ),
        );

        $data['count_total'] = sprintf('%02d', $occupancyData['count_total']);
        $data['count_occupied'] = sprintf('%02d', $occupancyData['count_occupied']);
        $data['count_available'] = sprintf('%02d', $occupancyData['count_available']);
        $data['count_unavailable'] = sprintf('%02d', $occupancyData['count_unavailable']);
        $data['chart_data'] = $chartData;

        return array('data_avail_pie_chart' => $data);
    }
}
