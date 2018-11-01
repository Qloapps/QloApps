<?php
/*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class StatsBestSuppliers extends ModuleGrid
{
	private $html = null;
	private $query = null;
	private $columns = null;
	private $default_sort_column = null;
	private $default_sort_direction = null;
	private $empty_message = null;
	private $paging_message = null;

	public function __construct()
	{
		$this->name = 'statsbestsuppliers';
		$this->tab = 'analytics_stats';
		$this->version = '1.4.1';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->default_sort_column = 'sales';
		$this->default_sort_direction = 'DESC';
		$this->empty_message = $this->l('Empty record set returned');
		$this->paging_message = sprintf($this->l('Displaying %1$s of %2$s'), '{0} - {1}', '{2}');

		$this->columns = array(
			array(
				'id' => 'name',
				'header' => $this->l('Name'),
				'dataIndex' => 'name',
				'align' => 'center'
			),
			array(
				'id' => 'quantity',
				'header' => $this->l('Quantity sold'),
				'dataIndex' => 'quantity',
				'align' => 'center'
			),
			array(
				'id' => 'sales',
				'header' => $this->l('Total paid'),
				'dataIndex' => 'sales',
				'align' => 'center'
			)
		);

		$this->displayName = $this->l('Best suppliers');
		$this->description = $this->l('Adds a list of the best suppliers to the Stats dashboard.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7.0.99');
	}

	public function install()
	{
		return (parent::install() && $this->registerHook('AdminStatsModules'));
	}

	public function hookAdminStatsModules($params)
	{
		$engine_params = array(
			'id' => 'id_category',
			'title' => $this->displayName,
			'columns' => $this->columns,
			'defaultSortColumn' => $this->default_sort_column,
			'defaultSortDirection' => $this->default_sort_direction,
			'emptyMessage' => $this->empty_message,
			'pagingMessage' => $this->paging_message
		);

		if (Tools::getValue('export') == 1)
				$this->csvExport($engine_params);
		$this->html = '
			<div class="panel-heading">
				'.$this->displayName.'
			</div>
			'.$this->engine($engine_params).'
			<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1').'">
				<i class="icon-cloud-upload"></i> '.$this->l('CSV Export').'
			</a>';
		return $this->html;
	}

	/**
	 * @return int Get total of distinct suppliers
	 */
	public function getTotalCount()
	{
		$sql = 'SELECT COUNT(DISTINCT(s.id_supplier))
				FROM '._DB_PREFIX_.'order_detail od
				LEFT JOIN '._DB_PREFIX_.'product p ON p.id_product = od.product_id
				LEFT JOIN '._DB_PREFIX_.'orders o ON o.id_order = od.id_order
				LEFT JOIN '._DB_PREFIX_.'supplier s ON s.id_supplier = p.id_supplier
				WHERE o.invoice_date BETWEEN '.$this->getDate().'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					AND o.valid = 1
					AND s.id_supplier IS NOT NULL';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
	}

	public function getData()
	{
		$this->_totalCount = $this->getTotalCount();

		$this->query = 'SELECT s.name, SUM(od.product_quantity) as quantity, ROUND(SUM(od.product_quantity * od.product_price) / o.conversion_rate, 2) as sales
				FROM '._DB_PREFIX_.'order_detail od
				LEFT JOIN '._DB_PREFIX_.'product p ON p.id_product = od.product_id
				LEFT JOIN '._DB_PREFIX_.'orders o ON o.id_order = od.id_order
				LEFT JOIN '._DB_PREFIX_.'supplier s ON s.id_supplier = p.id_supplier
				WHERE o.invoice_date BETWEEN '.$this->getDate().'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					AND o.valid = 1
					AND s.id_supplier IS NOT NULL
				GROUP BY p.id_supplier';
		if (Validate::IsName($this->_sort))
		{
			$this->query .= ' ORDER BY `'.$this->_sort.'`';
			if (isset($this->_direction) && Validate::isSortDirection($this->_direction))
				$this->query .= ' '.$this->_direction;
		}

		if (($this->_start === 0 || Validate::IsUnsignedInt($this->_start)) && Validate::IsUnsignedInt($this->_limit))
			$this->query .= ' LIMIT '.$this->_start.', '.($this->_limit);
		$this->_values = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query);
	}
}
