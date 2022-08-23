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
        $this->version = '1.5.2';
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
                'header' => $this->l('Hotel name'),
                'dataIndex' => 'hotel_name',
                'align' => 'left'
            ),
            array(
                'id' => 'totalRoomsBooked',
                'header' => $this->l('Rooms booked'),
                'dataIndex' => 'totalRoomsBooked',
                'align' => 'center'
            ),
            array(
                'id' => 'totalPriceSold',
                'header' => $this->l('Sales'),
                'dataIndex' => 'totalPriceSold',
                'align' => 'center'
            ),
            array(
                'id' => 'totalWholeSalePriceSold',
                'header' => $this->l('Margin'),
                'dataIndex' => 'totalWholeSalePriceSold',
                'align' => 'center'
            ),
            array(
                'id' => 'totalPageViewed',
                'header' => $this->l('Views'),
                'dataIndex' => 'totalPageViewed',
                'align' => 'center'
            ),
            array(
                'id' => 'percentageViews',
                'header' => $this->l('Precentage of views'),
                'dataIndex' => 'percentageViews',
                'align' => 'center'
            ),
            array(
                'id' => 'percentageRoomsBooked',
                'header' => $this->l('Precentage of rooms booked'),
                'dataIndex' => 'percentageRoomsBooked',
                'align' => 'center'
            ),
        );

        $this->displayName = $this->l('Best hotels');
        $this->description = $this->l('Adds a list of the best hotels to the Stats dashboard.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7.0.99');
    }

    public function install()
    {
        return (parent::install() && $this->registerHook('AdminStatsModules'));
    }

    public function hookAdminStatsModules($params)
    {
        $onlyChildren = (int)Tools::getValue('onlyChildren');

        $engine_params = array(
            'id' => 'id_category',
            'title' => $this->displayName,
            'columns' => $this->columns,
            'defaultSortColumn' => $this->default_sort_column,
            'defaultSortDirection' => $this->default_sort_direction,
            'emptyMessage' => $this->empty_message,
            'pagingMessage' => $this->paging_message,
            'customParams' => array(
                'onlyChildren' => $onlyChildren,
            )
        );

        if (Tools::getValue('export')) {
            $this->csvExport($engine_params);
        }

        $this->html = '
			<div class="panel-heading">
				<i class="icon-sitemap"></i> '.$this->displayName.'
			</div>';
        if (!(Module::isEnabled('statsdata') && Configuration::get('PS_STATSDATA_PAGESVIEWS'))) {
			$link = $this->context->link->getAdminLink('AdminModules').'&configure=statsdata';
            $this->html .= '<div class="alert alert-info">'.$this->l('You must enable the "Save global page views" option from ').'<u><a href="'.$link.'" target="_blank">Data mining for statistics</a></u>'.$this->l(' module in order to display the most viewed hotels, or use the QloApps Google Analytics module.').'</div>';
        }

        $this->html .= $this->engine($engine_params).'
            <div class="row form-horizontal">
                <div class="col-md-3">
                    <a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1').'">
                        <i class="icon-cloud-download"></i> '.$this->l('CSV Export').'
                    </a>
                </div>
            </div>';
        return $this->html;
    }

    public function getData()
    {
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $date_between = $this->getDate();
        $id_lang = $this->getLang();

        //If column 'order_detail.original_wholesale_price' does not exist, create it
        Db::getInstance(_PS_USE_SQL_SLAVE_)->query('SHOW COLUMNS FROM `'._DB_PREFIX_.'order_detail` LIKE "original_wholesale_price"');
        if (Db::getInstance()->NumRows() == 0) {
            Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'order_detail` ADD `original_wholesale_price` DECIMAL( 20, 6 ) NOT NULL DEFAULT  "0.000000"');
        }

        // If a shop is selected, get all children categories for the shop
        $categories = array();
        if (Shop::getContext() != Shop::CONTEXT_ALL) {
            $sql = 'SELECT c.nleft, c.nright
					FROM '._DB_PREFIX_.'category c
					WHERE c.id_category IN (
						SELECT hbi.id_category
						FROM '._DB_PREFIX_.'htl_branch_info hbi)';
            if ($result = Db::getInstance()->executeS($sql)) {
                $ntree_restriction = array();
                foreach ($result as $row) {
                    $ntree_restriction[] = '(nleft >= '.$row['nleft'].' AND nright <= '.$row['nright'].')';
                }

                if ($ntree_restriction) {
                    $sql = 'SELECT id_category
							FROM '._DB_PREFIX_.'category
							WHERE '.implode(' OR ', $ntree_restriction);
                    if ($result = Db::getInstance()->executeS($sql)) {
                        foreach ($result as $row) {
                            $categories[] = $row['id_category'];
                        }
                    }
                }
            }
        }

        $onlyChildren = '';
        if ((int)Tools::getValue('onlyChildren') == 1) {
            $onlyChildren = 'AND NOT EXISTS (SELECT NULL FROM '._DB_PREFIX_.'category WHERE id_parent = ca.id_category)';
        }

        // Get best hotels
        $this->query = '
                SELECT hbi.`id`, hbil.`hotel_name` AS hotel_name,
                (
					SELECT IFNULL(SUM(pv.`counter`), 0)
                    FROM `' . _DB_PREFIX_ . 'page_viewed` pv
                    LEFT JOIN `' . _DB_PREFIX_ . 'page` p ON pv.`id_page` = p.`id_page`
                    LEFT JOIN `' . _DB_PREFIX_ . 'page_type` pt ON pt.`id_page_type` = p.`id_page_type`
                    LEFT JOIN `' . _DB_PREFIX_ . 'date_range` dr ON pv.`id_date_range` = dr.`id_date_range`
                    WHERE pt.`name` = "category"
                    AND p.`id_object` = hbi.`id_category`
                    AND dr.`time_start` BETWEEN '.$date_between.'
                    AND dr.`time_end` BETWEEN '.$date_between.'
				) AS totalPageViewed,
                (
                    SELECT COUNT(`id_room`) FROM `' . _DB_PREFIX_ . 'htl_booking_detail` hbd
                    LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON (o.`id_order` = hbd.`id_order`)
                    WHERE hbd.`id_hotel` = hbi.`id` AND o.`valid` = 1 AND o.`invoice_date` BETWEEN '.$date_between.'
			    ) AS totalRoomsBooked,
                IFNULL(SUM(t.`totalQuantitySold`), 0) AS totalQuantitySold,
                IFNULL(SUM(t.`totalPriceSold`), 0) AS totalPriceSold,
                IFNULL(SUM(t.`totalWholeSalePriceSold`), 0) AS totalWholeSalePriceSold
				FROM `' . _DB_PREFIX_ . 'htl_branch_info` hbi
                LEFT JOIN `' . _DB_PREFIX_ . 'htl_room_type` hrt ON hrt.`id_hotel` = hbi.`id`
                LEFT JOIN `' . _DB_PREFIX_ . 'product` pr ON pr.`id_product` = hrt.`id_product`
                LEFT JOIN `' . _DB_PREFIX_ . 'htl_branch_info_lang` hbil ON hbil.`id` = hbi.`id` AND hbil.`id_lang` = '.(int)$id_lang .'
				LEFT JOIN (
                    SELECT pr.`id_product`, pa.`wholesale_price`,
						IFNULL(SUM(cp.`product_quantity`), 0) AS totalQuantitySold,
						IFNULL(SUM(cp.`product_price` * cp.`product_quantity`), 0) / o.conversion_rate AS totalPriceSold,
						IFNULL(SUM(
                            CASE
								WHEN cp.`original_wholesale_price` <> "0.000000"
								THEN cp.`original_wholesale_price` * cp.`product_quantity`
								WHEN pr.`wholesale_price` <> "0.000000"
								THEN pr.`wholesale_price` * cp.`product_quantity`
							END
                        ), 0) / o.conversion_rate AS totalWholeSalePriceSold
					FROM `' . _DB_PREFIX_ . 'product` pr
					LEFT OUTER JOIN `' . _DB_PREFIX_ . 'order_detail` cp ON pr.`id_product` = cp.`product_id`
					LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON o.`id_order` = cp.`id_order`
					LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.`id_product_attribute` = cp.`product_attribute_id`
					AND o.id_shop IN (1)
					WHERE o.valid = 1
					AND o.invoice_date BETWEEN '.$date_between.'
					GROUP BY pr.`id_product`
				) t ON t.`id_product` = hrt.`id_product`
                GROUP BY (hbi.`id`)';

        if (Validate::IsName($this->_sort)) {
            $this->query .= ' ORDER BY `'.bqSQL($this->_sort).'`';
            if (isset($this->_direction) && Validate::isSortDirection($this->_direction)) {
                $this->query .= ' '.$this->_direction;
            }
        }

        if (($this->_start === 0 || Validate::IsUnsignedInt($this->_start)) && Validate::IsUnsignedInt($this->_limit)) {
            $this->query .= ' LIMIT '.(int)$this->_start.', '.(int)$this->_limit;
        }

        $values = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query);
        $this->_totalCount = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT FOUND_ROWS()');

        // get stats total
        $sql = 'SELECT IFNULL(SUM(pv.`counter`), 0)
        FROM `'._DB_PREFIX_.'page_viewed` pv
        LEFT JOIN `'._DB_PREFIX_.'page` p ON pv.`id_page` = p.`id_page`
        LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = p.`id_page_type`
        LEFT JOIN `'._DB_PREFIX_.'date_range` dr ON pv.`id_date_range` = dr.`id_date_range`
        WHERE pt.`name` = "category"
        AND dr.`time_start` BETWEEN '.$date_between.'
        AND dr.`time_end` BETWEEN '.$date_between;
        $total_views = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

        $sql = 'SELECT COUNT(hbd.`id_room`)
        FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
        LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = hbd.`id_order`)
        WHERE o.`valid` = 1 AND o.`invoice_date` BETWEEN '.$date_between;
        $total_rooms_booked = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

        $stats_total = array(
            'views' => $total_views,
            'rooms_booked' => $total_rooms_booked,
        );

        foreach ($values as &$value) {
            if (isset($value['totalWholeSalePriceSold'])) {
                $value['totalWholeSalePriceSold'] = Tools::displayPrice($value['totalPriceSold'] - $value['totalWholeSalePriceSold'], $currency);
            }
            $value['totalPriceSold'] = Tools::displayPrice($value['totalPriceSold'], $currency);

            $value['percentageViews'] = sprintf('%0.2f', $stats_total['views'] ? ($value['totalPageViewed'] / $stats_total['views']) * 100 : 0).'%';
            $value['percentageRoomsBooked'] = sprintf('%0.2f', $stats_total['rooms_booked'] ? ($value['totalRoomsBooked'] / $stats_total['rooms_booked']) * 100 : 0).'%';
        }

        $this->_values = $values;
    }
}
