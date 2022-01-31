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
            && $this->registerHook('dashboardZoneTwo')
            && $this->registerHook('dashboardData')
            && $this->registerHook('actionAdminControllerSetMedia')
        );
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (get_class($this->context->controller) == 'AdminDashboardController') {
            $this->context->controller->addJs($this->_path.'views/js/'.$this->name.'.js');
        }
        $this->context->controller->addCSS($this->_path.'views/css/'.$this->name.'.css');
    }
    
    public function hookDashboardZoneTwo($params)
    {
        Media::addJsDef(array(
            'ajaxUrl' => $this->context->link->getModuleLink($this->name, 'chartdata'),
            'date_from' => $params['date_from'],
            'date_to' => $params['date_to'],
        ));
        $availPieChartLabelData = AdminStatsController::getAvailPieChartData($params['date_from'], $params['date_to']);
        $totalRooms = sprintf("%02d", AdminStatsController::getTotalRooms());
        $this->context->smarty->assign(array(
            'totalRooms' => $totalRooms,
            'inactiveRooms' =>  $availPieChartLabelData['inactive'],
            'availableRooms' =>  $availPieChartLabelData['available'],
            'occupiedRooms' =>  $availPieChartLabelData['occupied'],
            'date_occupancy_range' => $this->l('(from %s to %s)'),
            'date_occupancy_avail_format' => $this->context->language->date_format_lite,
		));
        return $this->display(__FILE__, 'dashboard_zone_two.tpl');
    }

    public function hookDashboardData($params)
    {
        $totalRooms = sprintf("%02d", AdminStatsController::getTotalRooms());
        $data = array();
		$data['total_rooms'] = $totalRooms;

		if (Configuration::get('PS_DASHBOARD_SIMULATION'))
		{
			$data['occupied'] = round(rand(0, 20));
			$data['available'] = round(rand(0, 20));
			$data['inactive'] = round(rand(0, 20));
			$availPieChartData = array();
			$objChartData = array();
			$objChartData['label'] = $this->l('Occupied');;
			$objChartData['value'] = $data['occupied']*100/$totalRooms;
			$availPieChartData[] = $objChartData;

			$objChartData = array();
			$objChartData['label'] = $this->l('Available');
			$objChartData['value'] = $data['available']*100/$totalRooms;
			$availPieChartData[] = $objChartData;

			$objChartData = array();
			$objChartData['label'] = $this->l('Inactive/ maintainance');
			$objChartData['value'] = $data['inactive']*100/$totalRooms;
			$availPieChartData[] = $objChartData;

			$data['chart_data'] = $availPieChartData;

		} else {
            $dateFrom = $params['date_from'];
            $dateTo = $params['date_to'];
			$availPieChartLabelData = AdminStatsController::getAvailPieChartData(
				$dateFrom,
				 $dateTo
			);

			$availPieChartData = array();
			$objChartData = array();
			$objChartData['label'] = $this->l('Occupied');;
			$objChartData['value'] = $availPieChartLabelData['occupied']*100/$totalRooms;
			$availPieChartData[] = $objChartData;

			$objChartData = array();
			$objChartData['label'] = $this->l('Available');
			$objChartData['value'] = $availPieChartLabelData['available']*100/$totalRooms;
			$availPieChartData[] = $objChartData;

			$objChartData = array();
			$objChartData['label'] = $this->l('Inactive/ maintainance');
			$objChartData['value'] = $availPieChartLabelData['inactive']*100/$totalRooms;
			$availPieChartData[] = $objChartData;

			$data['occupied'] = $availPieChartLabelData['occupied'];
			$data['available'] = $availPieChartLabelData['available'];
			$data['inactive'] = $availPieChartLabelData['inactive'];
			$data['chart_data'] = $availPieChartData;

		}

        return array('data_avail_pie_chart' => $data);
    }
}
