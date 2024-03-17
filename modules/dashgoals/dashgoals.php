<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
	exit;
}

class Dashgoals extends Module
{
	protected static $month_labels = array();
	protected static $types = array('traffic', 'conversion', 'avg_cart_value');

	protected static $real_color = array('#9E5BA1', '#00A89C', '#3AC4ED', '#F99031');
	protected static $more_color = array('#803E84', '#008E7E', '#20B2E7', '#F66E1B');
	protected static $less_color = array('#BC77BE', '#00C2BB', '#51D6F2', '#FBB244');

    const DG_VALUE_TYPE_TEXT = 1;
    const DG_VALUE_TYPE_PRICE = 2;
    const DG_VALUE_TYPE_PERCENT = 3;

	public function __construct()
	{
		$this->name = 'dashgoals';
		$this->tab = 'dashboard';
		$this->version = '1.0.2';
		$this->author = 'PrestaShop';

		parent::__construct();

		$this->displayName = $this->l('Dashboard Goals');
		$this->description = $this->l('Adds a block with your store\'s forecast.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7.0.99');

		Dashgoals::$month_labels = array(
			'01' => $this->l('January'),
			'02' => $this->l('February'),
			'03' => $this->l('March'),
			'04' => $this->l('April'),
			'05' => $this->l('May'),
			'06' => $this->l('June'),
			'07' => $this->l('July'),
			'08' => $this->l('August'),
			'09' => $this->l('September'),
			'10' => $this->l('October'),
			'11' => $this->l('November'),
			'12' => $this->l('December')
		);
	}

	public function install()
	{
		Configuration::updateValue('PS_DASHGOALS_CURRENT_YEAR', date('Y'));
		for ($month = '01'; $month <= 12; $month = sprintf('%02d', $month + 1)) {
			$key = Tools::strtoupper('dashgoals_traffic_'.$month.'_'.date('Y'));
			if (!ConfigurationKPI::get($key)) {
				ConfigurationKPI::updateValue($key, 600);
			}

			$key = Tools::strtoupper('dashgoals_conversion_'.$month.'_'.date('Y'));
			if (!ConfigurationKPI::get($key)) {
				ConfigurationKPI::updateValue($key, 2);
			}

			$key = Tools::strtoupper('dashgoals_avg_cart_value_'.$month.'_'.date('Y'));
			if (!ConfigurationKPI::get($key)) {
				ConfigurationKPI::updateValue($key, 80);
			}
		}

		// Prepare tab
		$tab = new Tab();
		$tab->active = 1;
		$tab->class_name = 'AdminDashgoals';
		$tab->name = array();
		foreach (Language::getLanguages(true) as $lang) {
			$tab->name[$lang['id_lang']] = 'Dashgoals';
		}
		$tab->id_parent = -1;
		$tab->module = $this->name;

		return (
			$tab->add()
			&& parent::install()
			&& $this->registerHook('dashboardZoneTwo')
			&& $this->registerHook('dashboardData')
			&& $this->registerHook('actionAdminControllerSetMedia')
		);
	}

	public function uninstall()
	{
		$id_tab = (int)Tab::getIdFromClassName('AdminDashgoals');
		if ($id_tab) {
			$tab = new Tab($id_tab);
			$tab->delete();
		}

		return parent::uninstall();
	}

	public function hookActionAdminControllerSetMedia()
	{
		if (get_class($this->context->controller) == 'AdminDashboardController') {
            Media::addJsDef(array(
                'goal_set_txt' => $this->l('Goal Set'),
                'goal_diff_txt' => $this->l('Goal Difference'),
                'VALUE_TYPE_PRICE' => self::DG_VALUE_TYPE_PRICE,
                'VALUE_TYPE_PERCENT' => self::DG_VALUE_TYPE_PERCENT,
            ));

			$this->context->controller->addJS($this->_path.'views/js/'.$this->name.'.js');
			$this->context->controller->addCSS($this->_path.'views/css/'.$this->name.'.css');
		}
	}

	public function setMonths($year)
	{
		$months = array();
		for ($i = '01'; $i <= 12; $i = sprintf('%02d', $i + 1)) {
			$months[$i.'_'.$year] = array('label' => Dashgoals::$month_labels[$i], 'values' => array());
		}

		foreach (Dashgoals::$types as $type) {
			foreach ($months as $month => &$month_row) {
				$key = 'dashgoals_'.$type.'_'.$month;
				if (Tools::isSubmit('submitDashGoals')) {
					ConfigurationKPI::updateValue(Tools::strtoupper($key), (float)Tools::getValue($key));
				}
				$month_row['values'][$type] = ConfigurationKPI::get(Tools::strtoupper($key));
			}
		}

		return $months;
	}

	public function hookDashboardZoneTwo($params)
	{
		$year = Configuration::get('PS_DASHGOALS_CURRENT_YEAR');
		$months = $this->setMonths($year);

		$this->context->smarty->assign(
			array(
				'colors' => self::$real_color,
				'currency' => $this->context->currency,
				'goals_year' => $year,
				'goals_months' => $months,
				'dashgoals_ajax_link' => $this->context->link->getAdminLink('AdminDashgoals')
			)
		);

		return $this->display(__FILE__, 'dashboard_zone_two.tpl');
	}

	public function hookDashboardData($params)
	{
		$year = ((isset($params['extra']) && $params['extra'] > 1970 && $params['extra'] < 2999) ? $params['extra'] : Configuration::get('PS_DASHGOALS_CURRENT_YEAR'));

		return array('data_chart' => array('dash_goals_chart1' => $this->getChartData($year)));
	}

	protected function fakeConfigurationKPI_get($key)
	{
		$start = array(
			'TRAFFIC' => 3000,
			'CONVERSION' => 2,
			'AVG_CART_VALUE' => 90
		);

		if (preg_match('/^DASHGOALS_([A-Z_]+)_([0-9]{2})/', $key, $matches))
		{
			if ($matches[1] == 'TRAFFIC')
				return $start[$matches[1]] * (1 + ($matches[2] - 1) / 10);
			else
				return $start[$matches[1]];
		}
	}

	public function getChartData($year)
	{
		// There are stream types (different charts) and for each types there are 3 available zones (one color for the goal, one if you over perform and one if you under perfom)
		$stream_types = array(
			array('type' => 'traffic', 'title' => $this->l('Traffic')),
			array('type' => 'conversion', 'title' => $this->l('Conversion rate')),
			array('type' => 'avg_cart_value', 'title' => $this->l('Average order value')),
			array('type' => 'sales', 'title' => $this->l('Sales')),
		);
		$stream_zones = array(
			array('zone' => 'real'),
			array('zone' => 'more'),
			array('zone' => 'less')
		);

		// We initialize all the streams types for all the zones
		$streams = array();

		foreach ($stream_types as $key => $stream_type) {
			$streams[$stream_type['type']] = array();
			foreach ($stream_zones as $stream_zone) {
				$streams[$stream_type['type']][$stream_zone['zone']] = array(
					'key' => $stream_type['type'].'_'.$stream_zone['zone'],
					'title' => $stream_type['title'],
					'color' => ($stream_zone['zone'] == 'more' ? self::$more_color[$key] : ($stream_zone['zone'] == 'less' ? self::$less_color[$key] : self::$real_color[$key])),
					'values' => array(),
					'disabled' => (isset($stream_type['type']) && $stream_type['type'] == 'sales') ? false : true
				);
			}
		}

		if (Configuration::get('PS_DASHBOARD_SIMULATION')) {
			$visits = $orders = $sales = array();
			$from = strtotime(date('Y-01-01 00:00:00'));
			$to = strtotime(date('Y-12-31 00:00:00'));
			for ($date = $from; $date <= $to; $date = strtotime('+1 day', $date)) {
				$visits[$date] = round(rand(2000, 5000));
				$orders[$date] = round(rand(40, 100));
				$sales[$date] = round(rand(3000, 9000), 2);
			}

			// Now we can calculate the value for every months
			for ($i = '01'; $i <= 12; $i = sprintf('%02d', $i + 1)) {
				$timestamp = strtotime($year.'-'.$i.'-01');

                // Start setting traffic dummy data
				$month_goal = $this->fakeConfigurationKPI_get('DASHGOALS_TRAFFIC_'.$i.'_'.$year);
				$value = (isset($visits[$timestamp]) ? $visits[$timestamp] : 0);
				$stream_values = $this->getValuesFromGoals($month_goal, $value, Dashgoals::$month_labels[$i]);

                $goal_diff = $value - $month_goal;

                $monthGoalInfo = array();
                if ($goal_diff < 0) {
                    $monthGoalInfo['complete'] = 0;
                } else {
                    $monthGoalInfo['complete'] = 1;
                }
                $goal_diff = abs($goal_diff);
                $monthGoalInfo['value_type'] = self::DG_VALUE_TYPE_TEXT;
                $monthGoalInfo['value'] = Tools::ps_round($value, 2);
                $monthGoalInfo['goal'] = Tools::ps_round($month_goal, 2);
                $monthGoalInfo['goal_diff'] = Tools::ps_round($goal_diff, 2);
                $monthGoalInfo['goal_diff_percent'] = ($value > 0) ? Tools::ps_round(($goal_diff * 100) / ($month_goal > 0 ? $month_goal : 1), 2) : 100;
                // for dummy data future goal will be 0 always
                $monthGoalInfo['is_future_goal'] = 0;

                $stream_values['real'] = array_merge($stream_values['real'], $monthGoalInfo);
                $stream_values['less'] = array_merge($stream_values['less'], $monthGoalInfo);
                $stream_values['more'] = array_merge($stream_values['more'], $monthGoalInfo);

				foreach ($stream_zones as $stream_zone) {
                    $stream_values[$stream_zone['zone']]['title'] = $streams['traffic'][$stream_zone['zone']]['title'];
					$streams['traffic'][$stream_zone['zone']]['values'][] = $stream_values[$stream_zone['zone']];
				}

                // Start setting conversion rate dummy data
				$month_goal = $this->fakeConfigurationKPI_get('DASHGOALS_CONVERSION_'.$i.'_'.$year);
				$value = 100 * ((isset($visits[$timestamp]) && $visits[$timestamp] && isset($orders[$timestamp]) && $orders[$timestamp]) ? ($orders[$timestamp] / $visits[$timestamp]) : 0);
				$stream_values = $this->getValuesFromGoals($month_goal, $value, Dashgoals::$month_labels[$i]);

                $goal_diff = $value - $month_goal;

                $monthGoalInfo = array();
                if ($goal_diff < 0) {
                    $monthGoalInfo['complete'] = 0;
                } else {
                    $monthGoalInfo['complete'] = 1;
                }
                $goal_diff = abs($goal_diff);
                $monthGoalInfo['value_type'] = self::DG_VALUE_TYPE_PERCENT;
                $monthGoalInfo['value'] = Tools::ps_round($value, 2);
                $monthGoalInfo['goal'] = Tools::ps_round($month_goal, 2);
                $monthGoalInfo['goal_diff'] = Tools::ps_round($goal_diff, 2);
                $monthGoalInfo['goal_diff_percent'] = ($value > 0) ? Tools::ps_round(($goal_diff * 100) / ($month_goal > 0 ? $month_goal : 1), 2) : 100;
                // for dummy data future goal will be 0 always
                $monthGoalInfo['is_future_goal'] = 0;

                $stream_values['real'] = array_merge($stream_values['real'], $monthGoalInfo);
                $stream_values['less'] = array_merge($stream_values['less'], $monthGoalInfo);
                $stream_values['more'] = array_merge($stream_values['more'], $monthGoalInfo);

				foreach ($stream_zones as $stream_zone) {
                    $stream_values[$stream_zone['zone']]['title'] = $streams['conversion'][$stream_zone['zone']]['title'];
					$streams['conversion'][$stream_zone['zone']]['values'][] = $stream_values[$stream_zone['zone']];
				}

                // Start setting avg cart value dummy data
				$month_goal = $this->fakeConfigurationKPI_get('DASHGOALS_AVG_CART_VALUE_'.$i.'_'.$year);
				$value = ((isset($orders[$timestamp]) && $orders[$timestamp] && isset($sales[$timestamp]) && $sales[$timestamp]) ? ($sales[$timestamp] / $orders[$timestamp]) : 0);
				$stream_values = $this->getValuesFromGoals($month_goal, $value, Dashgoals::$month_labels[$i]);

				$goal_diff = $value - $month_goal;

                $monthGoalInfo = array();
                if ($goal_diff < 0) {
                    $monthGoalInfo['complete'] = 0;
                } else {
                    $monthGoalInfo['complete'] = 1;
                }
                $goal_diff = abs($goal_diff);
                $monthGoalInfo['value_type'] = self::DG_VALUE_TYPE_PRICE;
                $monthGoalInfo['value'] = Tools::ps_round($value, 2);
                $monthGoalInfo['goal'] = Tools::ps_round($month_goal, 2);
                $monthGoalInfo['goal_diff'] = Tools::ps_round($goal_diff, 2);
                $monthGoalInfo['goal_diff_percent'] = ($value > 0) ? Tools::ps_round(($goal_diff * 100) / ($month_goal > 0 ? $month_goal : 1), 2) : 100;
                // for dummy data future goal will be 0 always
                $monthGoalInfo['is_future_goal'] = 0;

                $stream_values['real'] = array_merge($stream_values['real'], $monthGoalInfo);
                $stream_values['less'] = array_merge($stream_values['less'], $monthGoalInfo);
                $stream_values['more'] = array_merge($stream_values['more'], $monthGoalInfo);

				foreach ($stream_zones as $stream_zone) {
                    $stream_values[$stream_zone['zone']]['title'] = $streams['avg_cart_value'][$stream_zone['zone']]['title'];
					$streams['avg_cart_value'][$stream_zone['zone']]['values'][] = $stream_values[$stream_zone['zone']];
				}

                // Start setting sales dummy data
				$month_goal = $this->fakeConfigurationKPI_get('DASHGOALS_TRAFFIC_'.$i.'_'.$year) * $this->fakeConfigurationKPI_get('DASHGOALS_CONVERSION_'.$i.'_'.$year) / 100 * $this->fakeConfigurationKPI_get('DASHGOALS_AVG_CART_VALUE_'.$i.'_'.$year);
				$value = (isset($sales[$timestamp]) ? $sales[$timestamp] : 0);
				$stream_values = $this->getValuesFromGoals($month_goal, $value, Dashgoals::$month_labels[$i]);

                $goal_diff = $value - $month_goal;

                $monthGoalInfo = array();
                if ($goal_diff < 0) {
                    $monthGoalInfo['complete'] = 0;
                } else {
                    $monthGoalInfo['complete'] = 1;
                }
                $goal_diff = abs($goal_diff);
                $monthGoalInfo['value_type'] = self::DG_VALUE_TYPE_PRICE;
                $monthGoalInfo['value'] = Tools::ps_round($value, 2);
                $monthGoalInfo['goal'] = Tools::ps_round($month_goal, 2);
                $monthGoalInfo['goal_diff'] = Tools::ps_round($goal_diff, 2);
                $monthGoalInfo['goal_diff_percent'] = ($value > 0) ? Tools::ps_round(($goal_diff * 100) / ($month_goal > 0 ? $month_goal : 1), 2) : 100;
                // for dummy data future goal will be 0 always
                $monthGoalInfo['is_future_goal'] = 0;

                $stream_values['real'] = array_merge($stream_values['real'], $monthGoalInfo);
                $stream_values['less'] = array_merge($stream_values['less'], $monthGoalInfo);
                $stream_values['more'] = array_merge($stream_values['more'], $monthGoalInfo);

				foreach ($stream_zones as $stream_zone) {
                    $stream_values[$stream_zone['zone']]['title'] = $streams['sales'][$stream_zone['zone']]['title'];
					$streams['sales'][$stream_zone['zone']]['values'][] = $stream_values[$stream_zone['zone']];
				}
			}
		} else {
			// Retrieve gross data from AdminStatsController
			$visits = AdminStatsController::getVisits(false, $year.date('-01-01'), $year.date('-12-31'), 'month');
			$orders = AdminStatsController::getOrders($year.date('-01-01'), $year.date('-12-31'), 'month');
			$sales = AdminStatsController::getTotalSales($year.date('-01-01'), $year.date('-12-31'), 'month');

			// Now we can calculate the value for every months
			for ($i = '01'; $i <= 12; $i = sprintf('%02d', $i + 1)) {
				$timestamp = strtotime($year.'-'.$i.'-01');

                // send if goal is for future or past
                $isFutureGoal = 0;
                if (strtotime($year.'-'.$i) > strtotime(date('Y-m'))) {
                    $isFutureGoal = 1;
                }

                // Start setting traffic data
				$month_goal = ConfigurationKPI::get('DASHGOALS_TRAFFIC_'.$i.'_'.$year);
				$value = (isset($visits[$timestamp]) ? $visits[$timestamp] : 0);
				$stream_values = $this->getValuesFromGoals($month_goal, $value, Dashgoals::$month_labels[$i]);

				$goal_diff = $value - $month_goal;

                $monthGoalInfo = array();
                if ($goal_diff < 0) {
                    $monthGoalInfo['complete'] = 0;
                } else {
                    $monthGoalInfo['complete'] = 1;
                }
                $goal_diff = abs($goal_diff);
                $monthGoalInfo['value_type'] = self::DG_VALUE_TYPE_TEXT;
                $monthGoalInfo['value'] = Tools::ps_round($value, 2);
                $monthGoalInfo['goal'] = Tools::ps_round($month_goal, 2);
                $monthGoalInfo['goal_diff'] = Tools::ps_round($goal_diff, 2);
                $monthGoalInfo['goal_diff_percent'] = ($value > 0) ? Tools::ps_round(($goal_diff * 100) / ($month_goal > 0 ? $month_goal : 1), 2) : 100;
                $monthGoalInfo['is_future_goal'] = $isFutureGoal;

                $stream_values['real'] = array_merge($stream_values['real'], $monthGoalInfo);
                $stream_values['less'] = array_merge($stream_values['less'], $monthGoalInfo);
                $stream_values['more'] = array_merge($stream_values['more'], $monthGoalInfo);

				foreach ($stream_zones as $stream_zone) {
                    $stream_values[$stream_zone['zone']]['title'] = $streams['traffic'][$stream_zone['zone']]['title'];
					$streams['traffic'][$stream_zone['zone']]['values'][] = $stream_values[$stream_zone['zone']];
				}

                // Start setting conversion rate data
				$month_goal = ConfigurationKPI::get('DASHGOALS_CONVERSION_'.$i.'_'.$year);
				$value = 100 * ((isset($visits[$timestamp]) && $visits[$timestamp] && isset($orders[$timestamp]) && $orders[$timestamp]) ? ($orders[$timestamp] / $visits[$timestamp]) : 0);
				$stream_values = $this->getValuesFromGoals($month_goal, $value, Dashgoals::$month_labels[$i]);

				$goal_diff = $value - $month_goal;

                $monthGoalInfo = array();
                if ($goal_diff < 0) {
                    $monthGoalInfo['complete'] = 0;
                } else {
                    $monthGoalInfo['complete'] = 1;
                }
                $goal_diff = abs($goal_diff);
                $monthGoalInfo['value'] = Tools::ps_round($value, 2);
                $monthGoalInfo['value_type'] = self::DG_VALUE_TYPE_PERCENT;
                $monthGoalInfo['goal'] = Tools::ps_round($month_goal, 2);
                $monthGoalInfo['goal_diff'] = Tools::ps_round($goal_diff, 2);
                $monthGoalInfo['goal_diff_percent'] = ($value > 0) ? Tools::ps_round(($goal_diff * 100) / ($month_goal > 0 ? $month_goal : 1), 2) : 100;
                $monthGoalInfo['is_future_goal'] = $isFutureGoal;

                $stream_values['real'] = array_merge($stream_values['real'], $monthGoalInfo);
                $stream_values['less'] = array_merge($stream_values['less'], $monthGoalInfo);
                $stream_values['more'] = array_merge($stream_values['more'], $monthGoalInfo);

				foreach ($stream_zones as $stream_zone) {
                    $stream_values[$stream_zone['zone']]['title'] = $streams['conversion'][$stream_zone['zone']]['title'];
					$streams['conversion'][$stream_zone['zone']]['values'][] = $stream_values[$stream_zone['zone']];
				}

                // Start setting average cart value data
				$month_goal = ConfigurationKPI::get('DASHGOALS_AVG_CART_VALUE_'.$i.'_'.$year);
				$value = ((isset($orders[$timestamp]) && $orders[$timestamp] && isset($sales[$timestamp]) && $sales[$timestamp]) ? ($sales[$timestamp] / $orders[$timestamp]) : 0);
				$stream_values = $this->getValuesFromGoals($month_goal, $value, Dashgoals::$month_labels[$i]);

				$goal_diff = $value - $month_goal;

                $monthGoalInfo = array();
                if ($goal_diff < 0) {
                    $monthGoalInfo['complete'] = 0;
                } else {
                    $monthGoalInfo['complete'] = 1;
                }
                $goal_diff = abs($goal_diff);
                $monthGoalInfo['value'] = Tools::ps_round($value, 2);
                $monthGoalInfo['value_type'] = self::DG_VALUE_TYPE_PRICE;
                $monthGoalInfo['goal'] = Tools::ps_round($month_goal, 2);
                $monthGoalInfo['goal_diff'] = Tools::ps_round($goal_diff, 2);
                $monthGoalInfo['goal_diff_percent'] = ($value > 0) ? Tools::ps_round(($goal_diff * 100) / ($month_goal > 0 ? $month_goal : 1), 2) : 100;
                $monthGoalInfo['is_future_goal'] = $isFutureGoal;

                $stream_values['real'] = array_merge($stream_values['real'], $monthGoalInfo);
                $stream_values['less'] = array_merge($stream_values['less'], $monthGoalInfo);
                $stream_values['more'] = array_merge($stream_values['more'], $monthGoalInfo);

				foreach ($stream_zones as $stream_zone) {
                    $stream_values[$stream_zone['zone']]['title'] = $streams['avg_cart_value'][$stream_zone['zone']]['title'];
					$streams['avg_cart_value'][$stream_zone['zone']]['values'][] = $stream_values[$stream_zone['zone']];
				}

                // Start setting up data for sales
				$month_goal = ConfigurationKPI::get('DASHGOALS_TRAFFIC_'.$i.'_'.$year) * ConfigurationKPI::get('DASHGOALS_CONVERSION_'.$i.'_'.$year) / 100 * ConfigurationKPI::get('DASHGOALS_AVG_CART_VALUE_'.$i.'_'.$year);

				$value = (isset($sales[$timestamp]) && $sales[$timestamp]) ? $sales[$timestamp] : 0;

                $stream_values = $this->getValuesFromGoals($month_goal, isset($sales[$timestamp]) ? $sales[$timestamp] : 0, Dashgoals::$month_labels[$i]);

				$goal_diff = $value - $month_goal;

                $monthGoalInfo = array();
                if ($goal_diff < 0) {
                    $monthGoalInfo['complete'] = 0;
                } else {
                    $monthGoalInfo['complete'] = 1;
                }
                $goal_diff = abs($goal_diff);
                $monthGoalInfo['value'] = Tools::ps_round($value, 2);
                $monthGoalInfo['value_type'] = self::DG_VALUE_TYPE_PRICE;
                $monthGoalInfo['goal'] = Tools::ps_round($month_goal, 2);
                $monthGoalInfo['goal_diff'] = Tools::ps_round($goal_diff, 2);
                $monthGoalInfo['goal_diff_percent'] = ($value > 0) ? Tools::ps_round(($goal_diff * 100) / ($month_goal > 0 ? $month_goal : 1), 2) : 100;
                $monthGoalInfo['is_future_goal'] = $isFutureGoal;

                $stream_values['real'] = array_merge($stream_values['real'], $monthGoalInfo);
                $stream_values['less'] = array_merge($stream_values['less'], $monthGoalInfo);
                $stream_values['more'] = array_merge($stream_values['more'], $monthGoalInfo);

				foreach ($stream_zones as $stream_zone) {
                    $stream_values[$stream_zone['zone']]['title'] = $streams['sales'][$stream_zone['zone']]['title'];
					$streams['sales'][$stream_zone['zone']]['values'][] = $stream_values[$stream_zone['zone']];
				}
			}
		}

        // Merge all the streams before sending
		$all_streams = array();
		foreach ($stream_types as $stream_type) {
			foreach ($stream_zones as $stream_zone) {
				$all_streams[] = $streams[$stream_type['type']][$stream_zone['zone']];
			}
		}

		return array('chart_type' => 'bar_chart_goals', 'data' => $all_streams);
	}

	protected function getValuesFromGoals($month_goal, $value, $label)
    {
        // Initialize value for each zone
        $stream_values = [
            'real' => ['x' => $label, 'y' => 0],
            'less' => ['x' => $label, 'y' => 0],
            'more' => ['x' => $label, 'y' => 0],
        ];

        if ($value > 0) {
            $goalDiff = $value - $month_goal;
            if (($goalDiff) > 0) {
                $stream_values['real']['y'] = (float)Tools::ps_round($month_goal, 2);
                $stream_values['more']['y'] = (float)Tools::ps_round($goalDiff, 2);
            } elseif (($goalDiff) < 0) {
                $stream_values['real']['y'] = (float)Tools::ps_round($value, 2);
                $stream_values['less']['y'] = (float)Tools::ps_round(($month_goal - $value), 2);
            }
        } else {
            $stream_values['less']['y'] = (float)Tools::ps_round($month_goal, 2);
        }

        return $stream_values;
    }
}
