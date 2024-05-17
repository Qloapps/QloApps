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
        $this->version = '1.5.3';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->default_sort_column = 'totalRevenue';
        $this->default_sort_direction = 'DESC';
        $this->empty_message = $this->l('Empty recordset returned');
        $this->paging_message = sprintf($this->l('Displaying %1$s of %2$s'), '{0} - {1}', '{2}');

        $this->columns = array(
            array(
                'id' => 'name',
                'header' => $this->l('Hotel name'),
                'dataIndex' => 'hotel_name',
                'align' => 'center'
            ),
            array(
                'id' => 'totalRoomsBooked',
                'header' => $this->l('Rooms booked'),
                'dataIndex' => 'totalRoomsBooked',
                'align' => 'center',
                'tooltip' => $this->l('The room nights booked for the hotel.'),
            ),
            array(
                'id' => 'availableRooms',
                'header' => $this->l('Available rooms'),
                'dataIndex' => 'availableRooms',
                'align' => 'center',
                'tooltip' => $this->l('The total room nights available for booking for the hotel.'),
            ),
            array(
                'id' => 'totalOrders',
                'header' => $this->l('Orders'),
                'dataIndex' => 'totalOrders',
                'align' => 'center',
            ),
            array(
                'id' => 'totalRevenue',
                'header' => $this->l('Revenue'),
                'dataIndex' => 'totalRevenue',
                'align' => 'center',
            ),
            array(
                'id' => 'totalMargin',
                'header' => $this->l('Margin'),
                'dataIndex' => 'totalMargin',
                'align' => 'center',
            ),
            array(
                'id' => 'averageRevenue',
                'header' => $this->l('Avg. revenue'),
                'dataIndex' => 'averageRevenue',
                'align' => 'center',
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
        $engine_params = array(
            'id' => 'id_category',
            'title' => $this->displayName,
            'columns' => $this->columns,
            'defaultSortColumn' => $this->default_sort_column,
            'defaultSortDirection' => $this->default_sort_direction,
            'emptyMessage' => $this->empty_message,
            'pagingMessage' => $this->paging_message,
        );

        if (Tools::getValue('export')) {
            $this->csvExport($engine_params);
        }

        $this->html = '
			<div class="panel-heading">
				<i class="icon-sitemap"></i> '.$this->displayName.'
			</div>';

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
        $date_from = date('Y-m-d', strtotime($this->_employee->stats_date_from));
        $date_to = date('Y-m-d', strtotime($this->_employee->stats_date_to));
        if ($date_from == $date_to) {
            $date_to = date('Y-m-d', strtotime('+1 day', strtotime($date_to)));
        }
        $id_lang = $this->getLang();

        //If column 'order_detail.original_wholesale_price' does not exist, create it
        Db::getInstance(_PS_USE_SQL_SLAVE_)->query('SHOW COLUMNS FROM `'._DB_PREFIX_.'order_detail` LIKE "original_wholesale_price"');
        if (Db::getInstance()->NumRows() == 0) {
            Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'order_detail` ADD `original_wholesale_price` DECIMAL( 20, 6 ) NOT NULL DEFAULT  "0.000000"');
        }

        // Get best hotels
        $this->query = 'SELECT hbi.`id`, hbil.`hotel_name` AS hotel_name,
        (
            SELECT IFNULL(SUM(DATEDIFF(LEAST(hbd.`date_to`, "'.pSQL($date_to).'"), GREATEST(hbd.`date_from`, "'.pSQL($date_from).'"))), 0)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = hbd.`id_order`)
            WHERE hbd.`id_hotel` = hbi.`id` AND o.`valid` = 1 AND is_refunded = 0
            AND hbd.`date_to` > "'.pSQL($date_from).'" AND hbd.`date_from` < "'.pSQL($date_to).'"
        ) AS totalRoomsBooked,
        (
            SELECT SUM(max_room_nights) - SUM(disabled_room_nights)
            FROM (
                SELECT hri.`id_hotel`, DATEDIFF("'.pSQL($date_to).'", "'.pSQL($date_from).'") AS max_room_nights,
                CASE
                    WHEN hri.`id_status` = '.(int) HotelRoomInformation::STATUS_INACTIVE.' THEN DATEDIFF("'.pSQL($date_to).'", "'.pSQL($date_from).'")
                    WHEN hri.`id_status` = '.(int) HotelRoomInformation::STATUS_TEMPORARY_INACTIVE.' THEN IF(hrdd.`date_to` > "'.pSQL($date_from).'" AND hrdd.`date_from` < "'.pSQL($date_to).'", SUM(ABS(DATEDIFF(LEAST(hrdd.`date_to`, "'.pSQL($date_to).'"), GREATEST(hrdd.`date_from`, "'.pSQL($date_from).'")))), 0)
                    ELSE 0
                END AS disabled_room_nights
                FROM `'._DB_PREFIX_.'htl_room_information` hri
                LEFT JOIN `'._DB_PREFIX_.'htl_room_disable_dates` hrdd
                ON (hrdd.`id_room` = hri.`id`)
                GROUP BY hri.`id`
            ) AS t
            WHERE t.`id_hotel` = hbi.`id`
        ) AS totalRooms,
        (
            SELECT COUNT(DISTINCT hbd.`id_order`) FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = hbd.`id_order`)
            WHERE hbd.`id_hotel` = hbi.`id` AND o.`valid` = 1
            AND hbd.`date_to` > "'.pSQL($date_from).'" AND hbd.`date_from` < "'.pSQL($date_to).'"
        ) AS totalOrders,
        (
            SELECT IFNULL(SUM(ROUND((DATEDIFF(LEAST(hbd.`date_to`, "'.pSQL($date_to).'"), GREATEST(hbd.`date_from`, "'.pSQL($date_from).'")) / DATEDIFF(hbd.`date_to`, hbd.`date_from`)) * (hbd.`total_price_tax_excl` / o.`conversion_rate`), 2)), 0)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            LEFT JOIN `'._DB_PREFIX_.'orders` o
            ON (o.`id_order` = hbd.`id_order`)
            WHERE hbd.`id_hotel` = hbi.`id` AND o.`valid` = 1
            AND hbd.`date_to` > "'.pSQL($date_from).'" AND hbd.`date_from` < "'.pSQL($date_to).'"
        ) AS totalRevenue,
        (
            SELECT IFNULL(SUM(ROUND((DATEDIFF(LEAST(hbd.`date_to`, "'.pSQL($date_to).'"), GREATEST(hbd.`date_from`, "'.pSQL($date_from).'")) / DATEDIFF(hbd.`date_to`, hbd.`date_from`)) * (
                CASE
                    WHEN od.`original_wholesale_price` <> "0.000000" THEN od.`original_wholesale_price`
                    WHEN p.`wholesale_price` <> "0.000000" THEN p.`wholesale_price`
                    ELSE 0
                END
            ), 2)), 0)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            LEFT JOIN `'._DB_PREFIX_.'orders` o
            ON (o.`id_order` = hbd.`id_order`)
            LEFT JOIN `'._DB_PREFIX_.'product` p
            ON (p.`id_product` = hbd.`id_product`)
            LEFT JOIN `'._DB_PREFIX_.'order_detail` od
            ON (od.`id_order_detail` = hbd.`id_order_detail`)
            WHERE hbd.`id_hotel` = hbi.`id` AND o.`valid` = 1
            AND hbd.`date_to` > "'.pSQL($date_from).'" AND hbd.`date_from` < "'.pSQL($date_to).'"
        ) AS totalOperatingCost
        FROM `'._DB_PREFIX_.'htl_branch_info` hbi
        LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbil
        ON (hbil.`id` = hbi.`id` AND hbil.`id_lang` = '.(int)$id_lang .')
        WHERE 1 '.HotelBranchInformation::addHotelRestriction(false, 'hbi', 'id').'
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

        foreach ($values as &$value) {
            if (Tools::getValue('export') == false) {
                $value['hotel_name'] = '<a href="'.$this->context->link->getAdminLink('AdminAddHotel').'&id='.$value['id'].'&updatehtl_branch_info" target="_blank">'.$value['hotel_name'].'</a>';
            }

            $value['totalMargin'] = 0;
            if (((float) $value['totalRevenue']) > 0 && ((float) $value['totalOperatingCost']) > 0) {
                $value['totalMargin'] = max($value['totalRevenue'] - $value['totalOperatingCost'], 0);
            }
            $value['totalMargin'] = Tools::displayPrice($value['totalMargin'], $currency);

            $value['availableRooms'] = max($value['totalRooms'] - $value['totalRoomsBooked'], 0); // availableRooms can be negative if more rooms are disabled than available for booking

            $value['averageRevenue'] = $value['totalRoomsBooked'] ? ((float) $value['totalRevenue'] / (int) $value['totalRoomsBooked']) : 0;
            $value['averageRevenue'] = Tools::displayPrice((float) $value['averageRevenue'], $currency);

            $value['totalRevenue'] = Tools::displayPrice((float) $value['totalRevenue'], $currency);
        }

        $this->_values = $values;
    }
}
