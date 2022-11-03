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

require_once _PS_MODULE_DIR_.'hotelreservationsystem/define.php';

class StatsBestProducts extends ModuleGrid
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
        $this->name = 'statsbestproducts';
        $this->tab = 'analytics_stats';
        $this->version = '1.5.2';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->default_sort_column = 'totalRevenue';
        $this->default_sort_direction = 'DESC';
        $this->empty_message = $this->l('An empty record-set was returned.');
        $this->paging_message = sprintf($this->l('Displaying %1$s of %2$s'), '{0} - {1}', '{2}');

        $this->columns = array(
            array(
                'id' => 'roomTypeName',
                'header' => $this->l('Room type name'),
                'dataIndex' => 'roomTypeName',
                'align' => 'center',
            ),
            array(
                'id' => 'hotelName',
                'header' => $this->l('Hotel name'),
                'dataIndex' => 'hotelName',
                'align' => 'center',
            ),
            array(
                'id' => 'totalRoomsBooked',
                'header' => $this->l('Rooms booked'),
                'dataIndex' => 'totalRoomsBooked',
                'tooltip' => $this->l('The room nights booked for the room type.'),
                'align' => 'center',
            ),
            array(
                'id' => 'sellingPrice',
                'header' => $this->l('Price sold'),
                'dataIndex' => 'sellingPrice',
                'tooltip' => $this->l('The average price at which this room type has been booked.'),
                'align' => 'center',
            ),
            array(
                'id' => 'totalRevenue',
                'header' => $this->l('Revenue'),
                'dataIndex' => 'totalRevenue',
                'align' => 'center',
            ),
            array(
                'id' => 'bookingsPerDay',
                'header' => $this->l('Bookings per day'),
                'dataIndex' => 'bookingsPerDay',
                'align' => 'center',
            ),
            array(
                'id' => 'availableRooms',
                'header' => $this->l('Available rooms'),
                'dataIndex' => 'availableRooms',
                'tooltip' => $this->l('The room nights available for booking for the room type.'),
                'align' => 'center',
            ),
            array(
                'id' => 'active',
                'header' => $this->l('Active'),
                'dataIndex' => 'active',
                'align' => 'center',
            ),
        );

        $this->displayName = $this->l('Best-selling room types');
        $this->description = $this->l('Adds a list of the best-selling room types to the Stats dashboard.');
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

        $html = '<div class="panel-heading">'.$this->displayName.'</div>';
        if (!(Module::isEnabled('statsdata') && Configuration::get('PS_STATSDATA_PAGESVIEWS'))) {
			$link = $this->context->link->getAdminLink('AdminModules').'&configure=statsdata';
            $html .= '<div class="alert alert-info">'.$this->l('You must enable the "Save global page views" option from ').'<u><a href="'.$link.'" target="_blank">Data mining for statistics</a></u>'.$this->l(' module in order to display the most viewed room types, or use the QloApps Google Analytics module.').'</div>';
        }
        $html .= $this->engine($engine_params).'
		<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1').'">
			<i class="icon-cloud-download"></i> '.$this->l('CSV Export').'
		</a>';

        return $html;
    }

    public function getData()
    {
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $date_between = $this->getDate();
        $date_from = date('Y-m-d', strtotime($this->_employee->stats_date_from));
        $date_to = date('Y-m-d', strtotime($this->_employee->stats_date_to));
        $array_date_between = explode(' AND ', $date_between);
        $id_lang = $this->getLang();

        $this->query = 'SELECT p.`id_product`, pl.`name` AS room_type_name,
        p.`active`, hrt.`id_hotel`, hbil.`hotel_name`,
        (
            SELECT IFNULL(SUM(DATEDIFF(LEAST(hbd.`date_to`, "'.pSQL($date_to).'"), GREATEST(hbd.`date_from`, "'.pSQL($date_from).'"))), 0)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            LEFT JOIN `'._DB_PREFIX_.'orders` o
            ON (o.`id_order` = hbd.`id_order`)
            WHERE hbd.`id_product` = p.`id_product` AND o.`valid` = 1
            AND hbd.`date_to` > "'.pSQL($date_from).'" AND hbd.`date_from` < "'.pSQL($date_to).'"
        ) AS totalRoomsBooked,
        (
            SELECT IFNULL(AVG(hbd.`total_price_tax_excl` / o.`conversion_rate`), 0)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            LEFT JOIN `'._DB_PREFIX_.'orders` o
            ON (o.`id_order` = hbd.`id_order`)
            WHERE hbd.`id_product` = p.`id_product` AND o.`valid` = 1
            AND hbd.`date_to` > "'.pSQL($date_from).'" AND hbd.`date_from` < "'.pSQL($date_to).'"
        ) AS sellingPrice,
        (
            SELECT IFNULL(SUM(ROUND((DATEDIFF(LEAST(hbd.`date_to`, "'.pSQL($date_to).'"), GREATEST(hbd.`date_from`, "'.pSQL($date_from).'")) / DATEDIFF(hbd.`date_to`, hbd.`date_from`)) * hbd.`total_price_tax_excl`, 2)), 0)
            FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
            LEFT JOIN `'._DB_PREFIX_.'orders` o
            ON (o.`id_order` = hbd.`id_order`)
            WHERE hbd.`id_product` = p.`id_product` AND o.`valid` = 1
            AND hbd.`date_to` > "'.pSQL($date_from).'" AND hbd.`date_from` < "'.pSQL($date_to).'"
        ) AS totalRevenue,
        (
            SELECT SUM(max_room_nights) - SUM(disabled_room_nights)
            FROM (
                SELECT hri.`id_product`, DATEDIFF("'.pSQL($date_to).'", "'.pSQL($date_from).'") AS max_room_nights,
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
            WHERE t.`id_product` = p.`id_product`
        ) AS totalRooms
        FROM `'._DB_PREFIX_.'product` p
        LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
        ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.(int) $id_lang .')
        LEFT JOIN `'._DB_PREFIX_.'htl_room_type` hrt
        ON (hrt.`id_product` = p.`id_product`)
        LEFT JOIN `'._DB_PREFIX_.'htl_branch_info_lang` hbil
        ON (hbil.`id` = hrt.`id_hotel` AND hbil.`id_lang` = '.(int) $id_lang .')';

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

        $objHotelBookingDetail = new HotelBookingDetail();
        $numberOfDays = $objHotelBookingDetail->getNumberOfDays($date_from, $date_to);
        foreach ($values as &$value) {
            $value['roomTypeName'] = '<a href="'.$this->context->link->getAdminLink('AdminProducts').'&id_product='.$value['id_product'].'&updateproduct" target="_blank">'.$value['room_type_name'].'</a>';
            $value['hotelName'] = '<a href="'.$this->context->link->getAdminLink('AdminAddHotel').'&id='.$value['id_hotel'].'&updatehtl_branch_info" target="_blank">'.$value['hotel_name'].'</a>';
            $value['totalRoomsBooked'] = (int) $value['totalRoomsBooked'];
            $value['availableRooms'] = max($value['totalRooms'] - $value['totalRoomsBooked'], 0); // availableRooms can be negative if more rooms are disabled than available for booking
            $value['bookingsPerDay'] = sprintf('%0.2f', ($numberOfDays ? $value['totalRoomsBooked'] / $numberOfDays : 0));
            $value['sellingPrice'] = Tools::displayPrice($value['sellingPrice'], $currency);
            $value['totalRevenue'] = Tools::displayPrice($value['totalRevenue'], $currency);

            if ($value['active']) {
                $value['active'] = '<span class="badge badge-success">'.$this->l('Yes').'</span>';
            } else {
                $value['active'] = '<span class="badge badge-danger">'.$this->l('No').'</span>';
            }
        }

        $this->_values = $values;
    }
}
