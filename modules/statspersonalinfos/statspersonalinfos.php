<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class StatsPersonalInfos extends ModuleGraph
{
	private $html = '';
	private $option;

	public function __construct()
	{
		$this->name = 'statspersonalinfos';
		$this->tab = 'analytics_stats';
		$this->version = '1.4.0';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Registered customer information');
		$this->description = $this->l('Adds information about your registered customers (such as gender and age) to the Stats dashboard.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		return (parent::install() && $this->registerHook('AdminStatsModules'));
	}

	public function hookAdminStatsModules()
	{
		$this->html = '
			<div class="panel-heading">
				'.$this->displayName.'
			</div>
			<h4>'.$this->l('Guide').'</h4>
			<div class="alert alert-warning">
				<h4>'.$this->l('Target your audience').'</h4>
				<p>'.
					$this->l('In order for each message to have an impact, you need to know who it is being addressed to. ').
					'<br>'.
					$this->l('Defining your target audience is essential when choosing the right tools to win them over.').
					'<br>'.
					$this->l('It is best to limit an action to a group -- or to groups -- of clients.').
					'<br>'.
					$this->l('Storing registered customer information allows you to accurately define customer profiles so you can adapt your special deals and promotions.').'
				</p>
				<div>
					'.$this->l('You can increase your sales by:').'
					<ul>
						<li class="bullet">'.$this->l('Launching targeted advertisement campaigns.').'</li>
						<li class="bullet">'.$this->l('Contacting a group of clients by email or newsletter.').'</li>
					</ul>
				</div>
			</div>';
		$has_customers = (bool)Db::getInstance()->getValue('SELECT id_customer FROM '._DB_PREFIX_.'customer');
		if ($has_customers)
		{
			if (Tools::getValue('export'))
				if (Tools::getValue('exportType') == 'gender')
					$this->csvExport(array(
						'type' => 'pie',
						'option' => 'gender'
					));
				else if (Tools::getValue('exportType') == 'age')
					$this->csvExport(array(
						'type' => 'pie',
						'option' => 'age'
					));
				else if (Tools::getValue('exportType') == 'country')
					$this->csvExport(array(
						'type' => 'pie',
						'option' => 'country'
					));
				else if (Tools::getValue('exportType') == 'currency')
					$this->csvExport(array(
						'type' => 'pie',
						'option' => 'currency'
					));
				else if (Tools::getValue('exportType') == 'language')
					$this->csvExport(array(
						'type' => 'pie',
						'option' => 'language'
					));

			$this->html .= '
				<div class="row row-margin-bottom">
					<div class="col-lg-12">
						<div class="col-lg-8">
							'.$this->engine(array(
					'type' => 'pie',
					'option' => 'gender'
				)).'
						</div>
						<div class="col-lg-4">
							<p>'.$this->l('Gender distribution allows you to determine the percentage of men and women shoppers on your store.').'</p>
							<hr/>
							<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1&exportType=gender').'">
								<i class="icon-cloud-upload"></i> '.$this->l('CSV Export').'
							</a>
						</div>
					</div>
				</div>
				<div class="row row-margin-bottom">
					<div class="col-lg-12">
						<div class="col-lg-8">
							'.$this->engine(array(
					'type' => 'pie',
					'option' => 'age'
				)).'
						</div>
						<div class="col-lg-4">
							<p>'.$this->l('Age ranges allow you to better understand target demographics.').'</p>
							<hr/>
							<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1&exportType=age').'">
								<i class="icon-cloud-upload"></i> '.$this->l('CSV Export').'
							</a>
						</div>
					</div>
				</div>
				<div class="row row-margin-bottom">
					<div class="col-lg-12">
						<div class="col-lg-8">
							'.$this->engine(array(
					'type' => 'pie',
					'option' => 'country'
				)).'
						</div>
						<div class="col-lg-4">
							<p>'.$this->l('Country distribution allows you to analyze which part of the World your customers are shopping from.').'</p>
							<hr/>
							<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1&exportType=country').'">
								<i class="icon-cloud-upload"></i> '.$this->l('CSV Export').'
							</a>
						</div>
					</div>
				</div>
				<div class="row row-margin-bottom">
					<div class="col-lg-12">
						<div class="col-lg-8">
							'.$this->engine(array(
					'type' => 'pie',
					'option' => 'currency'
				)).'
						</div>
						<div class="col-lg-4">
							<p>'.$this->l('Currency range allows you to determine which currency your customers are using.').'</p>
							<hr/>
							<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1&exportType=currency').'">
								<i class="icon-cloud-upload"></i> '.$this->l('CSV Export').'
							</a>
						</div>
					</div>
				</div>
				<div class="row row-margin-bottom">
					<div class="col-lg-12">
						<div class="col-lg-8">
							'.$this->engine(array(
					'type' => 'pie',
					'option' => 'language'
				)).'
						</div>
						<div class="col-lg-4">
							<p>'.$this->l('Language distribution allows you to analyze the browsing language used by your customers.').'</p>
							<hr/>
							<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1&exportType=language').'">
								<i class="icon-cloud-upload"></i> '.$this->l('CSV Export').'
							</a>
						</div>
					</div>
				</div>';
		}
		else
			$this->html .= '<p>'.$this->l('No customers have registered yet.').'</p>';

		return $this->html;
	}

	public function setOption($option, $layers = 1)
	{
		$this->option = $option;
	}

	protected function getData($layers)
	{
		switch ($this->option)
		{
			case 'gender':
				$this->_titles['main'] = $this->l('Gender distribution');
				$genders = array(
					0 => $this->l('Male'),
					1 => $this->l('Female'),
					2 => $this->l('Unknown'),
				);

				$sql = 'SELECT g.type, c.id_gender, COUNT(c.id_customer) AS total
						FROM '._DB_PREFIX_.'customer c
						LEFT JOIN '._DB_PREFIX_.'gender g ON c.id_gender = g.id_gender
						WHERE 1
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'c').'
						GROUP BY c.id_gender';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

				$genders_results = array();
				foreach ($result as $row)
				{
					$type = (is_null($row['type'])) ? 2 : $row['type'];
					if (!isset($genders_results[$type]))
						$genders_results[$type] = 0;
					$genders_results[$type] += $row['total'];
				}

				foreach ($genders_results as $type => $total)
				{
					$this->_values[] = $total;
					$this->_legend[] = $genders[$type];
				}
				break;

			case 'age':
				$this->_titles['main'] = $this->l('Age range');

				// 0 - 18 years
				$sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) < 18
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
							AND `birthday` IS NOT NULL';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
				if (isset($result['total']) && $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('0-18');
				}

				// 18 - 24 years
				$sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) >= 18
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) < 25
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
							AND `birthday` IS NOT NULL';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
				if (isset($result['total']) && $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('18-24');
				}

				// 25 - 34 years
				$sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) >= 25
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) < 35
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
							AND `birthday` IS NOT NULL';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
				if (isset($result['total']) && $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('25-34');
				}

				// 35 - 49 years
				$sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) >= 35
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) < 50
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
							AND `birthday` IS NOT NULL';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
				if (isset($result['total']) && $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('35-49');
				}

				// 50 - 59 years
				$sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) >= 50
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) < 60
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
							AND `birthday` IS NOT NULL';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
				if (isset($result['total']) && $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('50-59');
				}

				// More than 60 years
				$sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) >= 60
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
							AND `birthday` IS NOT NULL';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
				if (isset($result['total']) && $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('60+');
				}

				// Total unknown
				$sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE `birthday` IS NULL
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
				if (isset($result['total']) && $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('Unknown');
				}
				break;

			case 'country':
				$this->_titles['main'] = $this->l('Country distribution');
				$sql = 'SELECT cl.`name`, COUNT(c.`id_country`) AS total
						FROM `'._DB_PREFIX_.'address` a
						LEFT JOIN `'._DB_PREFIX_.'customer` cu ON cu.id_customer = a.id_customer
						LEFT JOIN `'._DB_PREFIX_.'country` c ON a.`id_country` = c.`id_country`
						LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = '.(int)$this->context->language->id.')
						WHERE a.id_customer != 0
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'cu').'
						GROUP BY c.`id_country`';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
				foreach ($result as $row)
				{
					$this->_values[] = $row['total'];
					$this->_legend[] = $row['name'];
				}
				break;

			case 'currency':
				$this->_titles['main'] = $this->l('Currency distribution');
				$sql = 'SELECT c.`name`, COUNT(c.`id_currency`) AS total
						FROM `'._DB_PREFIX_.'orders` o
						LEFT JOIN `'._DB_PREFIX_.'currency` c ON o.`id_currency` = c.`id_currency`
						WHERE 1
							'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
						GROUP BY c.`id_currency`';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
				foreach ($result as $row)
				{
					$this->_values[] = $row['total'];
					$this->_legend[] = $row['name'];
				}
				break;

			case 'language':
				$this->_titles['main'] = $this->l('Language distribution');
				$sql = 'SELECT c.`name`, COUNT(c.`id_lang`) AS total
						FROM `'._DB_PREFIX_.'orders` o
						LEFT JOIN `'._DB_PREFIX_.'lang` c ON o.`id_lang` = c.`id_lang`
						WHERE 1
							'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
						GROUP BY c.`id_lang`';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
				foreach ($result as $row)
				{
					$this->_values[] = $row['total'];
					$this->_legend[] = $row['name'];
				}
				break;
		}
	}
}
