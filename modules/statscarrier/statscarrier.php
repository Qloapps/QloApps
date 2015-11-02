<?php
/*
* 2007-2014 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class StatsCarrier extends ModuleGraph
{
	private $html = '';
	private $option = '';

	public function __construct()
	{
		$this->name = 'statscarrier';
		$this->tab = 'analytics_stats';
		$this->version = '1.3.1';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Carrier distribution');
		$this->description = $this->l('Adds a graph displaying each carriers\' distribution to the Stats dashboard.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		return (parent::install() && $this->registerHook('AdminStatsModules'));
	}

	public function hookAdminStatsModules($params)
	{
		$sql = 'SELECT COUNT(o.`id_order`) as total
				FROM `'._DB_PREFIX_.'orders` o
				WHERE o.`date_add` BETWEEN '.ModuleGraph::getDateBetween().'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					'.((int)Tools::getValue('id_order_state') ? 'AND (SELECT oh.id_order_state FROM `'._DB_PREFIX_.'order_history` oh WHERE o.id_order = oh.id_order ORDER BY oh.date_add DESC, oh.id_order_history DESC LIMIT 1) = '.(int)Tools::getValue('id_order_state') : '');
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
		$states = OrderState::getOrderStates($this->context->language->id);

		if (Tools::getValue('export'))
			$this->csvExport(array('type' => 'pie', 'option' => Tools::getValue('id_order_state')));
		$this->html = '
			<div class="panel-heading">
				'.$this->displayName.'
			</div>
			<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" class="form-horizontal alert">
				<div class="row">
					<div class="col-lg-5 col-lg-offset-6">
						<select name="id_order_state">
							<option value="0"'.((!Tools::getValue('id_order_state')) ? ' selected="selected"' : '').'>'.$this->l('All').'</option>';
		foreach ($states as $state)
			$this->html .= '<option value="'.$state['id_order_state'].'"'.(($state['id_order_state'] == Tools::getValue('id_order_state')) ? ' selected="selected"' : '').'>'.$state['name'].'</option>';
		$this->html .= '</select>
					</div>
					<div class="col-lg-1">
						<input type="submit" name="submitState" value="'.$this->l('Filter').'" class="btn btn-default pull-right" />
					</div>
				</div>
			</form>

			<div class="alert alert-info">
				'.$this->l('This graph represents the carrier distribution for your orders. You can also narrow the focus of the graph to display distribution for a particular order status.').'
			</div>
			<div class="row row-margin-bottom">
				<div class="col-lg-12">
					<div class="col-lg-8">
						'.($result['total'] ? $this->engine(array(
					'type' => 'pie',
					'option' => Tools::getValue('id_order_state')
				)).'
					</div>
					<div class="col-lg-4">
						<a href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1&exportType=language').'" class="btn btn-default">
							<i class="icon-cloud-upload"></i> '.$this->l('CSV Export').'
						</a>' : $this->l('No valid orders have been received for this period.')).'
					</div>
				</div>
			</div>';

		return $this->html;
	}

	public function setOption($option, $layers = 1)
	{
		$this->option = (int)$option;
	}

	protected function getData($layers)
	{
		$state_query = '';
		if ((int)$this->option)
			$state_query = 'AND (
				SELECT oh.id_order_state FROM `'._DB_PREFIX_.'order_history` oh
				WHERE o.id_order = oh.id_order
				ORDER BY oh.date_add DESC, oh.id_order_history DESC
				LIMIT 1) = '.(int)$this->option;
		$this->_titles['main'] = $this->l('Percentage of orders listed by carrier.');

		$sql = 'SELECT c.name, COUNT(DISTINCT o.`id_order`) as total
				FROM `'._DB_PREFIX_.'carrier` c
				LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.id_carrier = c.id_carrier
				WHERE o.`date_add` BETWEEN '.ModuleGraph::getDateBetween().'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					'.$state_query.'
				GROUP BY c.`id_carrier`';
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		foreach ($result as $row)
		{
			$this->_values[] = $row['total'];
			$this->_legend[] = $row['name'];
		}
	}
}


