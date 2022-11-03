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

class StatsProduct extends ModuleGraph
{
    private $html = '';
    private $query = '';
    private $option = 0;
    private $id_product = 0;

    public function __construct()
    {
        $this->name = 'statsproduct';
        $this->tab = 'analytics_stats';
        $this->version = '1.5.2';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();
        $this->query = array();
        $this->displayName = $this->l('Room type details');
        $this->description = $this->l('Adds detailed statistics for each room type to the Stats dashboard.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7.0.99');
    }

    public function install()
    {
        return (parent::install() && $this->registerHook('AdminStatsModules'));
    }

    public function getTotalOrders($id_product)
    {
        $date_between = ModuleGraph::getDateBetween();
        $sql = 'SELECT COUNT(DISTINCT o.`id_order`)
        FROM `'._DB_PREFIX_.'orders` o
        INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
        ON (hbd.`id_order` = o.`id_order` AND hbd.`id_product` = '.(int) $id_product.')
        AND o.`valid` = 1 AND o.`date_add` BETWEEN '.$date_between;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public function getTotalRoomNights($id_product)
    {
        $date_between = ModuleGraph::getDateBetween();
        $sql = 'SELECT SUM(DATEDIFF(hbd.`date_to`, hbd.`date_from`))
        FROM `'._DB_PREFIX_.'orders` o
        INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
        ON (hbd.`id_order` = o.`id_order` AND hbd.`id_product` = '.(int) $id_product.')
        AND o.`valid` = 1 AND o.`date_add` BETWEEN '.$date_between;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public function getTotalRevenue($id_product)
    {
        $date_between = ModuleGraph::getDateBetween();
        $sql = 'SELECT SUM(hbd.`total_price_tax_excl` / o.`conversion_rate`)
        FROM `'._DB_PREFIX_.'orders` o
        INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd
        ON (hbd.`id_order` = o.`id_order` AND hbd.`id_product` = '.(int) $id_product.')
        AND o.`valid` = 1 AND o.`date_add` BETWEEN '.$date_between;

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public function getTotalViews($id_product)
    {
        $date_between = ModuleGraph::getDateBetween();
        $sql = 'SELECT SUM(pv.`counter`) AS total
        FROM `'._DB_PREFIX_.'page_viewed` pv
        LEFT JOIN `'._DB_PREFIX_.'date_range` dr ON pv.`id_date_range` = dr.`id_date_range`
        LEFT JOIN `'._DB_PREFIX_.'page` p ON pv.`id_page` = p.`id_page`
        LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = p.`id_page_type`
        WHERE pt.`name` = "product"
        AND p.`id_object` = '.(int) $id_product.'
        AND dr.`time_start` BETWEEN '.$date_between.'
        AND dr.`time_end` BETWEEN '.$date_between;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        return isset($result['total']) ? $result['total'] : 0;
    }

    private function getProducts()
    {
        $sql = 'SELECT p.`id_product`, p.reference, pl.`name`,
        (SELECT COUNT(hri.`id`) FROM `'._DB_PREFIX_.'htl_room_information` hri WHERE hri.`id_product` = p.`id_product`) AS total_rooms
        FROM `'._DB_PREFIX_.'product` p';
        if (Tools::getValue('id_hotel')) {
            $sql .= ' INNER JOIN `'._DB_PREFIX_.'htl_room_type` hrt ON (hrt.`id_product` = p.`id_product` AND hrt.`id_hotel` = '.(int)Tools::getValue('id_hotel').')';
        }
        $sql .= ' LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang`='.(int)$this->context->language->id.')
        ORDER BY pl.`name`';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    private function getSales($id_product)
    {
        $sql = 'SELECT o.`date_add`, o.`id_order`, o.`id_customer`, c.`firstname`, c.`lastname`, od.`product_quantity`, (od.`product_price` * od.`product_quantity`) AS total, od.`tax_name`, od.`product_name`, SUM(DATEDIFF(hbd.`date_to`, hbd.`date_from`)) AS total_booked
        FROM `'._DB_PREFIX_.'orders` o
        LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.`id_order` = od.`id_order`
        LEFT JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (od.`id_order` = hbd.`id_order` AND od.`product_id` = hbd.`id_product`)
        LEFT JOIN `'._DB_PREFIX_.'customer` c ON c.`id_customer` = o.`id_customer`
        WHERE o.`date_add` BETWEEN '.$this->getDate().'
        AND o.valid = 1
        AND od.product_id = '.(int)$id_product.' GROUP BY od.`id_order`';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function hookAdminStatsModules()
    {
        $id_hotel = (int)Tools::getValue('id_hotel');
        $currency = Context::getContext()->currency;

        if (Tools::getValue('export')) {
            if (!Tools::getValue('exportType')) {
                $this->csvExport(array(
                    'layers' => 2,
                    'type' => 'line',
                    'option' => '42'
                ));
            }
        }

        $this->html = '
			<div class="panel-heading">
				'.$this->displayName.'
			</div>
			<h4>'.$this->l('Guide').'</h4>
			<div class="alert alert-warning">
				<h4>'.$this->l('Number of orders compared to number of views').'</h4>
					'.$this->l('After choosing a hotel and selecting a room type, informational graphs will appear.').'
					<ul>
						<li class="bullet">'.$this->l('If you notice that a room type is often booked but viewed infrequently, you should display it more prominently at front office.').'</li>
						<li class="bullet">'.$this->l('On the other hand, if a room type has many views but is not often booked, we advise you to check or modify this room type\'s information, description and photography again, see if you can find something better.').'
						</li>
					</ul>
			</div>';
        if ($id_product = (int)Tools::getValue('id_product')) {
            if (Tools::getValue('export')) {
                if (Tools::getValue('exportType') == 1) {
                    $this->csvExport(array(
                        'layers' => 2,
                        'type' => 'line',
                        'option' => '1-'.$id_product
                    ));
                } elseif (Tools::getValue('exportType') == 2) {
                    $this->csvExport(array(
                        'type' => 'pie',
                        'option' => '3-'.$id_product
                    ));
                }
            }
            $product = new Product($id_product, false, $this->context->language->id);
            $total_orders = $this->getTotalOrders($product->id);
            $total_room_nights = $this->getTotalRoomNights($product->id);
            $total_revenue = $this->getTotalRevenue($product->id);
            $total_views = $this->getTotalViews($product->id);
            $this->html .= '<h4>'.$product->name.' - '.$this->l('Details').'</h4>
			<div class="row row-margin-bottom">
				<div class="col-lg-12">
					<div class="col-lg-8">
						'.$this->engine(array(
                            'layers' => 2,
                            'type' => 'line',
                            'option' => '1-'.$id_product,
                            'has_label_y' => true,
                        )).'
					</div>
					<div class="col-lg-4">
						<ul class="list-unstyled">
							<li>'.$this->l('Total orders:').' '.$total_orders.'</li>
							<li>'.$this->l('Total room nights:').' '.$total_room_nights.'</li>
							<li>'.$this->l('Revenue:').' '.Tools::displayprice($total_revenue, $currency).'</li>
							<li>'.$this->l('Total views:').' '.$total_views.'</li>
							<li>'.$this->l('Conversion rate:').' '.sprintf('%0.2f', ($total_views ? $total_orders / $total_views : 0) * 100).'%</li>
						</ul>
						<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1&exportType=1">
							<i class="icon-cloud-download"></i> '.$this->l('CSV Export').'
						</a>
					</div>
				</div>
			</div>';
            if ($has_attribute = $product->hasAttributes() && $total_orders) {
                $this->html .= '
				<h3 class="space">'.$this->l('Attribute sales distribution').'</h3>
				<center>'.$this->engine(array('type' => 'pie', 'option' => '3-'.$id_product)).'</center><br />
				<a href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1&exportType=2"><img src="../img/admin/asterisk.gif" alt=""/>'.$this->l('CSV Export').'</a>';
            }
            if ($total_orders) {
                $sales = $this->getSales($id_product);
                $this->html .= '
				<h4>'.$this->l('Sales').'</h4>
				<div style="overflow-y:scroll;height:'.min(400, (count($sales) + 1) * 32).'px">
					<table class="table">
						<thead>
							<tr>
								<th>
									<span class="title_box  active">'.$this->l('Date').'</span>
								</th>
								<th>
									<span class="title_box  active">'.$this->l('Order').'</span>
								</th>
								<th>
									<span class="title_box  active">'.$this->l('Customer').'</span>
								</th>
								'.($has_attribute ? '<th><span class="title_box  active">'.$this->l('Attribute').'</span></th>' : '').'
								<th>
									<span class="title_box  active">'.$this->l('Room nights booked').'</span>
								</th>
								<th>
									<span class="title_box  active">'.$this->l('Revenue').'</span>
								</th>
							</tr>
						</thead>
						<tbody>';
                $token_order = Tools::getAdminToken('AdminOrders'.(int)Tab::getIdFromClassName('AdminOrders').(int)$this->context->employee->id);
                $token_customer = Tools::getAdminToken('AdminCustomers'.(int)Tab::getIdFromClassName('AdminCustomers').(int)$this->context->employee->id);
                foreach ($sales as $sale) {
                    $this->html .= '
						<tr>
							<td>'.Tools::displayDate($sale['date_add'], null, false).'</td>
							<td class="text-left"><a href="?tab=AdminOrders&id_order='.$sale['id_order'].'&vieworder&token='.$token_order.'" target="_blank">#'.(int)$sale['id_order'].'</a></td>
							<td class="text-left"><a href="?tab=AdminCustomers&id_customer='.$sale['id_customer'].'&viewcustomer&token='.$token_customer.'" target="_blank">'.$sale['firstname'].' '.$sale['lastname'].' (#'.(int) $sale['id_customer'].')'.'</a></td>
							'.($has_attribute ? '<td>'.$sale['product_name'].'</td>' : '').'
							<td>'.(int)$sale['total_booked'].'</td>
							<td>'.Tools::displayprice($sale['total'], $currency).'</td>
						</tr>';
                }
                $this->html .= '
						</tbody>
					</table>
				</div>';
            }
        } else {
            $objBranchInfo = new HotelBranchInformation();
            $hotels = $objBranchInfo->hotelBranchesInfo((int)$this->context->language->id);
            $this->html .= '
			<form action="#" method="post" id="hotelsForm" class="form-horizontal">
				<div class="row row-margin-bottom">
					<label class="control-label col-lg-3">
						<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="'.$this->l('Choose a hotel to access it\'s room type statistics.').'">
							'.$this->l('Choose a hotel').'
						</span>
					</label>
					<div class="col-lg-3">
						<select name="id_hotel" onchange="$(\'#hotelsForm\').submit();">
							<option value="0">'.$this->l('All').'</option>';
            foreach ($hotels as $hotel) {
                $this->html .= '<option value="'.$hotel['id'].'"'.($id_hotel == $hotel['id'] ? ' selected="selected"' : '').'>'.$hotel['hotel_name'].'</option>';
            }
            $this->html .= '
						</select>
					</div>
				</div>
			</form>
			<h4>'.$this->l('Room types available').'</h4>
			<table class="table" style="border: 0; cellspacing: 0;">
				<thead>
					<tr>
						<th>
							<span class="title_box active">'.$this->l('Room type name').'</span>
						</th>
						<th>
							<span class="title_box text-center active">'.$this->l('Total rooms').'</span>
						</th>
						<th>
							<span class="title_box text-center active">'.$this->l('Action').'</span>
						</th>
					</tr>
				</thead>
                <tbody>';

            // get room types info
            foreach ($this->getProducts() as $product) {
                $this->html .= '
				<tr>
					<td>
						<a href="'.$this->context->link->getAdminLink('AdminProducts').'&updateproduct&id_product='.$product['id_product'].'" target="_blank">'.$product['name'].'</a>
					</td>
					<td class="center">'.$product['total_rooms'].'</td>
					<td class="center">
                        <a class="btn btn-sm btn-default" href="'.$this->context->link->getAdminLink('AdminStats').'&module='.$this->name.'&id_product='.$product['id_product'].'" title="'.$this->l('View').'"><i class="icon icon-eye"></i></a>
                    </td>
				</tr>';
            }

            $this->html .= '
				</tbody>
			</table>
			<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1').'">
				<i class="icon-cloud-download"></i> '.$this->l('CSV Export').'
			</a>';
        }

        return $this->html;
    }

    public function setOption($option, $layers = 1)
    {
        $options = explode('-', $option);
        if (count($options) === 2) {
            list($this->option, $this->id_product) = $options;
        } else {
            $this->option = $option;
        }
        $date_between = $this->getDate();
        switch ($this->option) {
            case 1:
                $this->_titles['main'][0] = $this->l('Room nights');
                $this->_titles['main'][1] = $this->l('Views (x100)');
                $this->_titles['x'] = $this->l('Date');
                $this->_titles['y'] = $this->l('Room nights, Views (x100)');

                $this->query[0] = 'SELECT o.`date_add`, SUM(DATEDIFF(hbd.`date_to`, hbd.`date_from`)) AS total
                FROM `'._DB_PREFIX_.'order_detail` od
                LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = od.`id_order`
                INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (o.`id_order` = hbd.`id_order` AND od.`product_id` = hbd.`id_product`)
                WHERE od.`product_id` = '.(int)$this->id_product.'
                '.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
                AND o.valid = 1
                AND o.`date_add` BETWEEN '.$date_between.'
                GROUP BY o.`date_add`';

                $this->query[1] = 'SELECT dr.`time_start` AS date_add, (SUM(pv.`counter`) / 100) AS total
                FROM `'._DB_PREFIX_.'page_viewed` pv
                LEFT JOIN `'._DB_PREFIX_.'date_range` dr ON pv.`id_date_range` = dr.`id_date_range`
                LEFT JOIN `'._DB_PREFIX_.'page` p ON pv.`id_page` = p.`id_page`
                LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = p.`id_page_type`
                WHERE pt.`name` = \'product\'
                '.Shop::addSqlRestriction(false, 'pv').'
                AND p.`id_object` = '.(int)$this->id_product.'
                AND dr.`time_start` BETWEEN '.$date_between.'
                AND dr.`time_end` BETWEEN '.$date_between.'
                GROUP BY dr.`time_start`';
                break;

            case 3:
                $this->query = 'SELECT product_attribute_id, COUNT(hbd.`id_room`) AS total
                FROM `'._DB_PREFIX_.'orders` o
                LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.`id_order` = od.`id_order`
                INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (o.`id_order` = hbd.`id_order` AND od.`product_id` = hbd.`id_product`)
                WHERE od.`product_id` = '.(int)$this->id_product.'
                '.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
                AND o.valid = 1
                AND o.`date_add` BETWEEN '.$date_between.'
                GROUP BY od.`product_attribute_id`';
                $this->_titles['main'] = $this->l('Attributes');
                break;

            case 42:
                $this->_titles['main'][1] = $this->l('Reference');
                $this->_titles['main'][2] = $this->l('Name');
                $this->_titles['main'][3] = $this->l('Stock');
                break;
        }
    }

    private function getHotelCategories($idLang)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT c.*, cl.`name` FROM `'._DB_PREFIX_.'category` c
            INNER JOIN `'._DB_PREFIX_.'htl_branch_info` hbi ON (hbi.`id_category` = c.`id_category`)
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
            ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.(int)$idLang.')'
        );
        return $result;
    }

    protected function getData($layers)
    {
        if ($this->option == 42) {
            $products = $this->getProducts();
            foreach ($products as $product) {
                $this->_values[0][] = $product['reference'];
                $this->_values[1][] = $product['name'];
                $this->_values[2][] = $product['quantity'];
                $this->_legend[] = $product['id_product'];
            }
        } elseif ($this->option != 3) {
            $this->setDateGraph($layers, true);
        } else {
            $product = new Product($this->id_product, false, (int)$this->getLang());

            $comb_array = array();
            $assoc_names = array();
            $combinations = $product->getAttributeCombinations((int)$this->getLang());
            foreach ($combinations as $combination) {
                $comb_array[$combination['id_product_attribute']][] = array(
                    'group' => $combination['group_name'],
                    'attr' => $combination['attribute_name']
                );
            }
            foreach ($comb_array as $id_product_attribute => $product_attribute) {
                $list = '';
                foreach ($product_attribute as $attribute) {
                    $list .= trim($attribute['group']).' - '.trim($attribute['attr']).', ';
                }
                $list = rtrim($list, ', ');
                $assoc_names[$id_product_attribute] = $list;
            }

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query);
            foreach ($result as $row) {
                $this->_values[] = $row['total'];
                $this->_legend[] = @$assoc_names[$row['product_attribute_id']];
            }
        }
    }

    protected function setAllTimeValues($layers)
    {
        for ($i = 0; $i < $layers; $i++) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query[$i]);
            foreach ($result as $row) {
                $this->_values[$i][(int)substr($row['date_add'], 0, 4)] += $row['total'];
            }
        }
    }

    protected function setYearValues($layers)
    {
        for ($i = 0; $i < $layers; $i++) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query[$i]);
            foreach ($result as $row) {
                $this->_values[$i][(int)substr($row['date_add'], 5, 2)] += $row['total'];
            }
        }
    }

    protected function setMonthValues($layers)
    {
        for ($i = 0; $i < $layers; $i++) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query[$i]);
            foreach ($result as $row) {
                $this->_values[$i][(int)substr($row['date_add'], 8, 2)] += $row['total'];
            }
        }
    }

    protected function setDayValues($layers)
    {
        for ($i = 0; $i < $layers; $i++) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query[$i]);
            foreach ($result as $row) {
                $this->_values[$i][(int)substr($row['date_add'], 11, 2)] += $row['total'];
            }
        }
    }
}
