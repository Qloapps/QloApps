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

class DashAvailability extends Module
{
    public function __construct()
    {
        $this->name = 'dashavailability';
        $this->tab = 'dashboard';
        $this->version = '1.0.3';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6');
        $this->author = 'Webkul';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Dashboard Availability');
        $this->description = $this->l('Adds a block with a graphical representation of availability of your hotel`s room.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

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
            Media::addJsDef(array(
                'avail_rooms_txt' => $this->l('Available Rooms'),
            ));
            $this->context->controller->addJs($this->_path.'views/js/'.$this->name.'.js');
            $this->context->controller->addCSS($this->_path.'views/css/'.$this->name.'.css');
        }
    }

    public function hookDashboardZoneTwo($params)
    {
        Media::addJsDef(array(
            'dashAvailAajaxUrl' => $this->context->link->getModuleLink($this->name, 'chartdata'),
        ));

        $this->context->smarty->assign(array(
            'dateFromBar' => date('Y-m-d', strtotime('now')),
        ));

        return $this->display(__FILE__, 'dashboard_zone_two.tpl');
    }

    public function hookDashboardData($params)
    {
        $dateFrom = null;
        $days = null;

        if ($params['extra'] == 'undefined') {
            $dateFrom = $params['date_from'];
            $days = 5;
        } else {
            $extra = json_decode($params['extra']);
            $dateFrom = $extra->date_from;
            $days = $extra->days;
        }

        if (Configuration::get('PS_DASHBOARD_SIMULATION')) {
            $from = strtotime($dateFrom.' 00:00:00');
            $to = strtotime($dateFrom.'+'.$days.' days 23:59:59');
            $data = array();
            for ($date = $from; $date <= $to; $date = strtotime('+1 days', $date)) {
                $availability_data['values'][] = array($date, round(rand(0, 20)));
            }
        } else {
            $availability_data = AdminStatsController::getAvailabilityLineChartData($days, $dateFrom, $params['id_hotel']);
        }

        $availability_data = array_merge(
            $availability_data,
            array(
                'id' => 'availabilities',
                'key' => $this->l('Availability'),
                'border_color' => '#11f0fc',
                'color' => '#72C3F0',
            )
        );
        $data[] = $availability_data;

        $data = array(
            'chart_type' => 'line_chart_availability',
            'date_format' => $this->context->language->date_format_lite,
            'data' => $data
        );

        return array(
            'data_chart' => array('availability_line_chart1' => $data),
        );
    }
}
