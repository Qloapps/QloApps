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

if (!defined('_PS_VERSION_')) {
    exit;
}

class StatsBestVouchers extends ModuleGrid
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
        $this->name = 'statsbestvouchers';
        $this->tab = 'analytics_stats';
        $this->version = '1.5.2';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->default_sort_column = 'ca';
        $this->default_sort_direction = 'DESC';
        $this->empty_message = $this->l('Empty recordset returned.');
        $this->paging_message = sprintf($this->l('Displaying %1$s of %2$s'), '{0} - {1}', '{2}');

        $this->columns = array(
            array(
                'id' => 'code',
                'header' => $this->l('Code'),
                'dataIndex' => 'code',
                'align' => 'center'
            ),
            array(
                'id' => 'name',
                'header' => $this->l('Name'),
                'dataIndex' => 'name',
                'align' => 'center'
            ),
            array(
                'id' => 'ca',
                'header' => $this->l('Sales'),
                'dataIndex' => 'ca',
                'align' => 'center'
            ),
            array(
                'id' => 'total',
                'header' => $this->l('Total used'),
                'dataIndex' => 'total',
                'align' => 'center'
            )
        );

        $this->displayName = $this->l('Best vouchers');
        $this->description = $this->l('Adds a list of the best vouchers to the Stats dashboard.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7.0.99');
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
            'emptyMessage' => $this->empty_message,
            'pagingMessage' => $this->paging_message
        );

        if (Tools::getValue('export')) {
            $this->csvExport($engine_params);
        }

        $this->html = '
			<div class="panel-heading">
				'.$this->displayName.'
			</div>
			'.$this->engine($engine_params).'
			<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1').'">
				<i class="icon-cloud-download"></i> '.$this->l('CSV Export').'
			</a>';

        return $this->html;
    }

    public function getData()
    {
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $this->query = 'SELECT SQL_CALC_FOUND_ROWS ocr.id_cart_rule, cr.code, ocr.name, COUNT(ocr.id_cart_rule) as total, ROUND(SUM(o.total_paid_real) / o.conversion_rate,2) as ca
				FROM '._DB_PREFIX_.'order_cart_rule ocr
				LEFT JOIN '._DB_PREFIX_.'orders o ON o.id_order = ocr.id_order
				LEFT JOIN '._DB_PREFIX_.'cart_rule cr ON cr.id_cart_rule = ocr.id_cart_rule
				WHERE o.valid = 1
                AND o.`id_order` IN (
                    SELECT id_order FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                    WHERE 1 '.HotelBranchInformation::addHotelRestriction(false, 'hbd').')
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					AND o.invoice_date BETWEEN '.$this->getDate().'
				GROUP BY ocr.id_cart_rule';

        if (Validate::IsName($this->_sort)) {
            $this->query .= ' ORDER BY `'.bqSQL($this->_sort).'`';
            if (isset($this->_direction) && (Tools::strtoupper($this->_direction) == 'ASC' || Tools::strtoupper($this->_direction) == 'DESC')) {
                $this->query .= ' '.pSQL($this->_direction);
            }
        }

        if (($this->_start === 0 || Validate::IsUnsignedInt($this->_start)) && Validate::IsUnsignedInt($this->_limit)) {
            $this->query .= ' LIMIT '.(int)$this->_start.', '.(int)$this->_limit;
        }

        $values = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query);
        $this->_totalCount = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT FOUND_ROWS()');
        foreach ($values as &$value) {
            if (Tools::getValue('export') == false) {
                $value['code'] = '<a href="'.$this->context->link->getAdminLink('AdminCartRules').'&id_cart_rule='.$value['id_cart_rule'].'&updatecart_rule" target="_blank">'.$value['code'].'</a>';
                $value['name'] = '<a href="'.$this->context->link->getAdminLink('AdminCartRules').'&id_cart_rule='.$value['id_cart_rule'].'&updatecart_rule" target="_blank">'.$value['name'].'</a>';
            }
            $value['ca'] = Tools::displayPrice($value['ca'], $currency);
        }

        $this->_values = $values;
    }
}
