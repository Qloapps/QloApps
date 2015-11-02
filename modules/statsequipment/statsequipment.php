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

class StatsEquipment extends ModuleGraph
{
	private $html = '';
	private $query = '';
	private $query2 = '';

	public function __construct()
	{
		$this->name = 'statsequipment';
		$this->tab = 'analytics_stats';
		$this->version = '1.2.3';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Browsers and operating systems');
		$this->description = $this->l('Adds a tab containing graphs about web browser and operating system usage to the Stats dashboard.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		return (parent::install() && $this->registerHook('AdminStatsModules'));
	}

	/**
	 * @return array Get list of browser "plugins" (javascript, media player, etc.)
	 */
	private function getEquipment()
	{
		$sql = 'SELECT DISTINCT g.*
				FROM `'._DB_PREFIX_.'connections` c 
				LEFT JOIN `'._DB_PREFIX_.'guest` g ON g.`id_guest` = c.`id_guest`
				WHERE c.`date_add` BETWEEN '.ModuleGraph::getDateBetween().'
					'.Shop::addSqlRestriction(false, 'c');
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->query($sql);

		$calc_array = array(
			'jsOK' => 0,
			'jsKO' => 0,
			'javaOK' => 0,
			'javaKO' => 0,
			'wmpOK' => 0,
			'wmpKO' => 0,
			'qtOK' => 0,
			'qtKO' => 0,
			'realOK' => 0,
			'realKO' => 0,
			'flashOK' => 0,
			'flashKO' => 0,
			'directorOK' => 0,
			'directorKO' => 0
		);
		while ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->nextRow($result))
		{
			if (!$row['javascript'])
			{
				++$calc_array['jsKO'];
				continue;
			}
			++$calc_array['jsOK'];
			($row['windows_media']) ? ++$calc_array['wmpOK'] : ++$calc_array['wmpKO'];
			($row['real_player']) ? ++$calc_array['realOK'] : ++$calc_array['realKO'];
			($row['adobe_flash']) ? ++$calc_array['flashOK'] : ++$calc_array['flashKO'];
			($row['adobe_director']) ? ++$calc_array['directorOK'] : ++$calc_array['directorKO'];
			($row['sun_java']) ? ++$calc_array['javaOK'] : ++$calc_array['javaKO'];
			($row['apple_quicktime']) ? ++$calc_array['qtOK'] : ++$calc_array['qtKO'];
		}

		if (!$calc_array['jsOK'])
			return false;

		$equip = array(
			'Windows Media Player' => $calc_array['wmpOK'] / ($calc_array['wmpOK'] + $calc_array['wmpKO']),
			'Real Player' => $calc_array['realOK'] / ($calc_array['realOK'] + $calc_array['realKO']),
			'Apple Quicktime' => $calc_array['qtOK'] / ($calc_array['qtOK'] + $calc_array['qtKO']),
			'Sun Java' => $calc_array['javaOK'] / ($calc_array['javaOK'] + $calc_array['javaKO']),
			'Adobe Flash' => $calc_array['flashOK'] / ($calc_array['flashOK'] + $calc_array['flashKO']),
			'Adobe Shockwave' => $calc_array['directorOK'] / ($calc_array['directorOK'] + $calc_array['directorKO'])
		);
		arsort($equip);

		return $equip;
	}

	public function hookAdminStatsModules($params)
	{
		if (Tools::getValue('export'))
			if (Tools::getValue('exportType') == 'browser')
				$this->csvExport(array('type' => 'pie', 'option' => 'wb'));
			else if (Tools::getValue('exportType') == 'os')
				$this->csvExport(array('type' => 'pie', 'option' => 'os'));

		$equipment = $this->getEquipment();
		$this->html = '
		<div class="panel-heading">'
			.$this->displayName.'
		</div>
		<h4>'.$this->l('Guide').'</h4>
		<div class="alert alert-warning">
			<h4>'.$this->l('Making sure that your website is accessible to as many people as possible').'</h4>
			<p>
			'.$this->l('When managing a website, it is important to keep track of the software used by visitors so as to be sure that the site displays the same way for everyone. PrestaShop was built to be compatible with the most recent Web browsers and computer operating systems (OS). However, because you may end up adding advanced features to your website or even modifying the core PrestaShop code, these additions may not be accessible to everyone. That is why it is a good idea to keep track of the percentage of users for each type of software before adding or changing something that only a limited number of users will be able to access.').'
			</p>
		</div>
		<div class="row row-margin-bottom">
			<div class="col-lg-12">
				<div class="col-lg-8">
					'.$this->engine(array('type' => 'pie', 'option' => 'wb')).'
				</div>
				<div class="col-lg-4">
					<p>'.$this->l('Indicates the percentage of each web browser used by customers.').'</p>
					<hr/>
					<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1&exportType=browser').'">
						<i class="icon-cloud-upload"></i>'.$this->l('CSV Export').'
					</a>
				</div>
			</div>
		</div>
		<div class="row row-margin-bottom">
			<div class="col-lg-12">
				<div class="col-lg-8">
					'.$this->engine(array('type' => 'pie', 'option' => 'os')).'
				</div>
				<div class="col-lg-4">
					<p>'.$this->l('Indicates the percentage of each operating system used by customers.').'</p>
					<hr/>
					<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1&exportType=os').'">
						<i class="icon-cloud-upload"></i>'.$this->l('CSV Export').'
					</a>
				</div>
			</div>
		</div>';
		if ($equipment)
		{
			$this->html .= '<table class="table">
				<tr><th><span class="title_box  active">'.$this->l('Plugins').'</th></span><th></th></tr>';
			foreach ($equipment as $name => $value)
				$this->html .= '<tr><td>'.$name.'</td><td>'.number_format(100 * $value, 2).'%</td></tr>';
			$this->html .= '</table>';
		}

		return $this->html;
	}

	public function setOption($option, $layers = 1)
	{
		switch ($option)
		{
			case 'wb':
				$this->_titles['main'] = $this->l('Web browser used');
				$this->query = 'SELECT wb.`name`, COUNT(g.`id_web_browser`) AS total
						FROM `'._DB_PREFIX_.'web_browser` wb
						LEFT JOIN `'._DB_PREFIX_.'guest` g ON g.`id_web_browser` = wb.`id_web_browser`
						LEFT JOIN `'._DB_PREFIX_.'connections` c ON g.`id_guest` = c.`id_guest`
						WHERE 1
							'.Shop::addSqlRestriction(false, 'c').'
							AND c.`date_add` BETWEEN ';
				$this->query2 = ' GROUP BY g.`id_web_browser`';
				break;

			case 'os':
				$this->_titles['main'] = $this->l('Operating system used');
				$this->query = 'SELECT os.`name`, COUNT(g.`id_operating_system`) AS total
						FROM `'._DB_PREFIX_.'operating_system` os
						LEFT JOIN `'._DB_PREFIX_.'guest` g ON g.`id_operating_system` = os.`id_operating_system`
						LEFT JOIN `'._DB_PREFIX_.'connections` c ON g.`id_guest` = c.`id_guest`
						WHERE 1
							'.Shop::addSqlRestriction(false, 'c').'
							AND c.`date_add` BETWEEN ';
				$this->query2 = ' GROUP BY g.`id_operating_system`';
				break;
		}
	}

	protected function getData($layers)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query.$this->getDate().$this->query2);
		$this->_values = array();
		$i = 0;
		foreach ($result as $row)
		{
			$this->_values[$i] = $row['total'];
			$this->_legend[$i++] = $row['name'];
		}
	}
}
