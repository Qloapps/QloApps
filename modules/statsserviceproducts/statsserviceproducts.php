<?php
/**
 * 2010-2023 Webkul.
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
 * @copyright 2010-2023 Webkul IN
 * @license LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class statsServiceProducts extends ModuleGrid
{
    private $query = null;
    private $columns = null;
    private $default_sort_column = null;
    private $paging_message = null;

    public function __construct()
    {
        $this->name = 'statsserviceproducts';
        $this->tab = 'analytics_stats';
        $this->version = '1.0.0';
        $this->author = 'Webkul';
        $this->need_instance = 0;

        parent::__construct();

        $this->default_sort_column = 'totalPriceSold';
        $this->default_sort_direction = 'DESC';
        $this->paging_message = sprintf($this->l('Displaying %1$s of %2$s'), '{0} - {1}', '{2}');

        $this->columns = array(
			array(
				'id' => 'name',
				'header' => $this->l('Name'),
				'dataIndex' => 'name',
				'align' => 'left'
			),
            array(
				'id' => 'auto_add_to_cart',
				'header' => $this->l('Auto add to cart'),
				'dataIndex' => 'auto_add_to_cart',
				'align' => 'center'
			),
			array(
				'id' => 'totalQuantitySold',
				'header' => $this->l('Quantity sold'),
				'dataIndex' => 'totalQuantitySold',
				'align' => 'center'
			),
			array(
				'id' => 'avgPriceSold',
				'header' => $this->l('Average Price'),
				'dataIndex' => 'avgPriceSold',
				'align' => 'center'
			),
			array(
				'id' => 'totalPriceSold',
				'header' => $this->l('Sales'),
				'dataIndex' => 'totalPriceSold',
				'align' => 'center'
			),
			array(
				'id' => 'averageQuantitySold',
				'header' => $this->l('Quantity sold in a day'),
				'dataIndex' => 'averageQuantitySold',
				'align' => 'center'
			),
			array(
				'id' => 'active',
				'header' => $this->l('Active'),
				'dataIndex' => 'active',
				'align' => 'center'
			)
		);

        $this->displayName = $this->l('Extra services overview');
        $this->description = $this->l('Show extra services overview based on sales.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return (parent::install() && $this->registerHook('AdminStatsModules'));
    }

    public function hookAdminStatsModules($params)
    {
        $engine_params = array(
            'id' => 'id_product',
            'title' => $this->displayName,
            'columns' => $this->columns,
            'defaultSortColumn' => $this->default_sort_column,
            'defaultSortDirection' => $this->default_sort_direction,
            'pagingMessage' => $this->paging_message
        );

        if (Tools::getValue('export')) {
            $this->csvExport($engine_params);
        }

        $this->context->smarty->assign(array(
            'module_name' => $this->displayName,
            'grid_table' => $this->engine($engine_params),
            'export_link' => Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1',
        ));

        return $this->display(__FILE__, 'services_content_block.tpl');
    }

    public function getData()
    {
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		$date_between = $this->getDate();
		$array_date_between = explode(' AND ', $date_between);

		$this->query = 'SELECT SQL_CALC_FOUND_ROWS p.`id_product`, pl.`name`, p.`active`, p.`auto_add_to_cart`,
            IFNULL(odd.`avgPriceSold`, 0) as avgPriceSold,
            IFNULL(odd.`totalQuantitySold`, 0) as totalQuantitySold,
            IFNULL(odd.`averageQuantitySold`, 0) as averageQuantitySold,
            IFNULL(odd.`totalPriceSold`, 0) as totalPriceSold
            FROM '._DB_PREFIX_.'product p
            '.Shop::addSqlAssociation('product', 'p').'
            LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (p.id_product = pl.id_product AND pl.id_lang = '.(int)$this->getLang().' '.Shop::addSqlRestrictionOnLang('pl').')
            LEFT JOIN (
                SELECT p.`id_product` as id_product,
                ROUND(AVG(od.`product_price` / o.`conversion_rate`), 2) as avgPriceSold,
                IFNULL(SUM(od.`product_quantity`), 0) AS totalQuantitySold,
                ROUND(IFNULL(IFNULL(SUM(od.`product_quantity`), 0) / (1 + LEAST(TO_DAYS('.$array_date_between[1].'), TO_DAYS(NOW())) - GREATEST(TO_DAYS('.$array_date_between[0].'), TO_DAYS(product_shop.date_add))), 0), 2) as averageQuantitySold,
                ROUND(IFNULL(SUM((od.product_price * od.product_quantity) / o.conversion_rate), 0), 2) AS totalPriceSold
                FROM '._DB_PREFIX_.'order_detail od
                INNER JOIN '._DB_PREFIX_.'product p ON (p.`id_product` = od.`product_id`)
                '.Shop::addSqlAssociation('product', 'p').'
                LEFT JOIN '._DB_PREFIX_.'orders o ON (od.id_order = o.id_order)
                WHERE o.valid = 1 AND o.invoice_date BETWEEN '.$date_between.'
                GROUP BY od.`product_id`
            ) odd ON (odd.`id_product` = p.`id_product`)
            WHERE p.`booking_product` = 0 AND p.`active` = 1
            GROUP BY p.id_product';

        if (Validate::IsName($this->_sort))
		{
			$this->query .= ' ORDER BY `'.bqSQL($this->_sort).'`';
			if (isset($this->_direction) && Validate::isSortDirection($this->_direction))
				$this->query .= ' '.$this->_direction;
		}
		$this->_totalCount = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT COUNT(p.`id_product`) FROM '._DB_PREFIX_.'product p WHERE p.`booking_product` = 0 AND p.`active` = 1'
        );

		if (($this->_start === 0 || Validate::IsUnsignedInt($this->_start)) && Validate::IsUnsignedInt($this->_limit))
			$this->query .= ' LIMIT '.(int)$this->_start.', '.(int)$this->_limit;

		$values = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query);
		foreach ($values as &$value)
		{
            if (!Tools::getValue('export')) {
                if ($value['active']) {
                    $value['active'] = '<span class="badge badge-success">'.$this->l('Yes').'</span>';
                } else {
                    $value['active'] = '<span class="badge badge-danger">'.$this->l('No').'</span>';
                }
                if ($value['auto_add_to_cart']) {
                    $value['auto_add_to_cart'] = '<span class="badge badge-success">'.$this->l('Yes').'</span>';
                } else {
                    $value['auto_add_to_cart'] = '<span class="badge badge-danger">'.$this->l('No').'</span>';
                }
            }
			$value['avgPriceSold'] = Tools::displayPrice($value['avgPriceSold'], $currency);
			$value['totalPriceSold'] = Tools::displayPrice($value['totalPriceSold'], $currency);
		}
		unset($value);

		$this->_values = $values;
    }
}
