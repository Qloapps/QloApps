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

class StatsBestCategories extends ModuleGrid
{
	private $html;
	private $query;
	private $columns;
	private $default_sort_column;
	private $default_sort_direction;
	private $empty_message;
	private $paging_message;

	public function __construct()
	{
		$this->name = 'statsbestcategories';
		$this->tab = 'analytics_stats';
		$this->version = '1.4.1';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->default_sort_column = 'totalPriceSold';
		$this->default_sort_direction = 'DESC';
		$this->empty_message = $this->l('Empty recordset returned');
		$this->paging_message = sprintf($this->l('Displaying %1$s of %2$s'), '{0} - {1}', '{2}');

		$this->columns = array(
			array(
				'id' => 'name',
				'header' => $this->l('Name'),
				'dataIndex' => 'name',
				'align' => 'left'
			),
			array(
				'id' => 'totalQuantitySold',
				'header' => $this->l('Total Quantity Sold'),
				'dataIndex' => 'totalQuantitySold',
				'align' => 'center'
			),
			array(
				'id' => 'totalPriceSold',
				'header' => $this->l('Total Price'),
				'dataIndex' => 'totalPriceSold',
				'align' => 'right'
			),
			array(
				'id' => 'totalPageViewed',
				'header' => $this->l('Total Viewed'),
				'dataIndex' => 'totalPageViewed',
				'align' => 'center'
			)
		);

		$this->displayName = $this->l('Best categories');
		$this->description = $this->l('Adds a list of the best categories to the Stats dashboard.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
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

		if (Tools::getValue('export'))
			$this->csvExport($engine_params);

		$this->html = '
			<div class="panel-heading">
				<i class="icon-sitemap"></i> '.$this->displayName.'
			</div>
			'.$this->engine($engine_params).'
			<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1').'">
				<i class="icon-cloud-upload"></i> '.$this->l('CSV Export').'
			</a>';

		return $this->html;
	}

	public function getData()
	{
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		$date_between = $this->getDate();
		$id_lang = $this->getLang();

		// If a shop is selected, get all children categories for the shop
		$categories = array();
		if (Shop::getContext() != Shop::CONTEXT_ALL)
		{
			$sql = 'SELECT c.nleft, c.nright
					FROM '._DB_PREFIX_.'category c
					WHERE c.id_category IN (
						SELECT s.id_category
						FROM '._DB_PREFIX_.'shop s
						WHERE s.id_shop IN ('.implode(', ', Shop::getContextListShopID()).')
					)';
			if ($result = Db::getInstance()->executeS($sql))
			{
				$ntree_restriction = array();
				foreach ($result as $row)
					$ntree_restriction[] = '(nleft >= '.$row['nleft'].' AND nright <= '.$row['nright'].')';

				if ($ntree_restriction)
				{
					$sql = 'SELECT id_category
							FROM '._DB_PREFIX_.'category
							WHERE '.implode(' OR ', $ntree_restriction);
					if ($result = Db::getInstance()->executeS($sql))
					{
						foreach ($result as $row)
							$categories[] = $row['id_category'];
					}
				}
			}
		}

		// Get best categories
		$this->query = '
		SELECT SQL_CALC_FOUND_ROWS ca.`id_category`, CONCAT(parent.name, \' > \', calang.`name`) as name,
			IFNULL(SUM(t.`totalQuantitySold`), 0) AS totalQuantitySold,
			ROUND(IFNULL(SUM(t.`totalPriceSold`), 0), 2) AS totalPriceSold,
			(
				SELECT IFNULL(SUM(pv.`counter`), 0)
				FROM `'._DB_PREFIX_.'page` p
				LEFT JOIN `'._DB_PREFIX_.'page_viewed` pv ON p.`id_page` = pv.`id_page`
				LEFT JOIN `'._DB_PREFIX_.'date_range` dr ON pv.`id_date_range` = dr.`id_date_range`
				LEFT JOIN `'._DB_PREFIX_.'product` pr ON CAST(p.`id_object` AS UNSIGNED INTEGER) = pr.`id_product`
				LEFT JOIN `'._DB_PREFIX_.'category_product` capr2 ON capr2.`id_product` = pr.`id_product`
				WHERE capr.`id_category` = capr2.`id_category`
				AND p.`id_page_type` = 1
				AND dr.`time_start` BETWEEN '.$date_between.'
				AND dr.`time_end` BETWEEN '.$date_between.'
			) AS totalPageViewed
		FROM `'._DB_PREFIX_.'category` ca
		LEFT JOIN `'._DB_PREFIX_.'category_lang` calang ON (ca.`id_category` = calang.`id_category` AND calang.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('calang').')
		LEFT JOIN `'._DB_PREFIX_.'category_lang` parent ON (ca.`id_parent` = parent.`id_category` AND parent.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('parent').')
		LEFT JOIN `'._DB_PREFIX_.'category_product` capr ON ca.`id_category` = capr.`id_category`
		LEFT JOIN (
			SELECT pr.`id_product`, t.`totalQuantitySold`, t.`totalPriceSold`
			FROM `'._DB_PREFIX_.'product` pr
			LEFT JOIN (
				SELECT pr.`id_product`,
					IFNULL(SUM(cp.`product_quantity`), 0) AS totalQuantitySold,
					IFNULL(SUM(cp.`product_price` * cp.`product_quantity`), 0) / o.conversion_rate AS totalPriceSold
				FROM `'._DB_PREFIX_.'product` pr
				LEFT OUTER JOIN `'._DB_PREFIX_.'order_detail` cp ON pr.`id_product` = cp.`product_id`
				LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = cp.`id_order`
				'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
				WHERE o.valid = 1
				AND o.invoice_date BETWEEN '.$date_between.'
				GROUP BY pr.`id_product`
			) t ON t.`id_product` = pr.`id_product`
		) t	ON t.`id_product` = capr.`id_product`
		'.(($categories) ? 'WHERE ca.id_category IN ('.implode(', ', $categories).')' : '').'
		GROUP BY ca.`id_category`
		HAVING ca.`id_category` != 1';

		if (Validate::IsName($this->_sort))
		{
			$this->query .= ' ORDER BY `'.bqSQL($this->_sort).'`';
			if (isset($this->_direction) && Validate::isSortDirection($this->_direction))
				$this->query .= ' '.$this->_direction;
		}

		if (($this->_start === 0 || Validate::IsUnsignedInt($this->_start)) && Validate::IsUnsignedInt($this->_limit))
			$this->query .= ' LIMIT '.(int)$this->_start.', '.(int)$this->_limit;

		$values = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query);
		foreach ($values as &$value)
			$value['totalPriceSold'] = Tools::displayPrice($value['totalPriceSold'], $currency);

		$this->_values = $values;
		$this->_totalCount = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT FOUND_ROWS()');
	}
}
