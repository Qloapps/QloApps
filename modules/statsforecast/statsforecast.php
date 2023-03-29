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

class StatsForecast extends Module
{
    private $html = '';
    private $t1 = 0;
    private $t2 = 0;
    private $t3 = 0;
    private $t4 = 0;
    private $t5 = 0;
    private $t6 = 0;
    private $t7 = 0;
    private $t8 = 0;
    private $t9 = 0;

    public function __construct()
    {
        $this->name = 'statsforecast';
        $this->tab = 'analytics_stats';
        $this->version = '1.4.3';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Stats Dashboard');
        $this->description = $this->l('This is the main module for the Stats dashboard. It displays a summary of all your current statistics.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7.0.99');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('AdminStatsModules')
            && $this->registerHook('actionAdminControllerSetMedia');
    }

    public function getContent()
    {
        Tools::redirectAdmin('index.php?controller=AdminStats&module=statsforecast&token='.Tools::getAdminTokenLite('AdminStats'));
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('controller') == 'AdminStats'
            && (Tools::getValue('module') == false || Tools::getValue('module') == 'statsforecast')
        ) {
            $this->context->controller->addCSS($this->getPathUri().'views/css/conversion-graph.css');
            $this->context->controller->addJS($this->getPathUri().'views/js/conversion-graph.js');
        }
    }

    public function hookAdminStatsModules()
    {
        Context::getContext()->controller->addJqueryPlugin('connections');

        $ru = AdminController::$currentIndex.'&module='.$this->name.'&token='.Tools::getValue('token');

        $db = Db::getInstance();

        if (!isset($this->context->cookie->stats_granularity)) {
            $this->context->cookie->stats_granularity = 10;
        }
        if (Tools::isSubmit('submitIdZone')) {
            $this->context->cookie->stats_id_zone = (int)Tools::getValue('stats_id_zone');
            Tools::redirectAdmin($ru);
        }
        if (Tools::isSubmit('submitGranularity')) {
            $this->context->cookie->stats_granularity = Tools::getValue('stats_granularity');
            Tools::redirectAdmin($ru);
        }

        $currency = $this->context->currency;
        $employee = $this->context->employee;

        $from = max(strtotime(_PS_CREATION_DATE_.' 00:00:00'), strtotime($employee->stats_date_from.' 00:00:00'));
        $to = strtotime($employee->stats_date_to.' 23:59:59');
        $to2 = min(time(), $to);
        $interval = ($to - $from) / 60 / 60 / 24;
        $interval2 = ($to2 - $from) / 60 / 60 / 24;
        $prop30 = $interval / $interval2;

        if ($this->context->cookie->stats_granularity == 7) {
            $interval_avg = $interval2 / 30;
        }
        if ($this->context->cookie->stats_granularity == 4) {
            $interval_avg = $interval2 / 365;
        }
        if ($this->context->cookie->stats_granularity == 10) {
            $interval_avg = $interval2;
        }
        if ($this->context->cookie->stats_granularity == 42) {
            $interval_avg = $interval2 / 7;
        }

        $data_table = array();
        if ($this->context->cookie->stats_granularity == 10) {
            for ($i = $from; $i <= $to2; $i = strtotime('+1 day', $i)) {
                $data_table[date('Y-m-d', $i)] = array(
                    'fix_date' => date('Y-m-d', $i),
                    'countOrders' => 0,
                    'totalRoomsBooked' => 0,
                    'totalSales' => 0,
                    'totalOperatingCost' => 0,
                );
            }
        }

        $date_from_gadd = ($this->context->cookie->stats_granularity != 42
            ? 'LEFT(date_add, '.(int)$this->context->cookie->stats_granularity.')'
            : 'IFNULL(MAKEDATE(YEAR(date_add),DAYOFYEAR(date_add)-WEEKDAY(date_add)), CONCAT(YEAR(date_add),"-01-01*"))');

        $date_from_ginvoice = ($this->context->cookie->stats_granularity != 42
            ? 'LEFT(invoice_date, '.(int)$this->context->cookie->stats_granularity.')'
            : 'IFNULL(MAKEDATE(YEAR(invoice_date),DAYOFYEAR(invoice_date)-WEEKDAY(invoice_date)), CONCAT(YEAR(invoice_date),"-01-01*"))');

        $date_to_ginvoice = '';
        if ($this->context->cookie->stats_granularity == 42) {
            $date_to_ginvoice = 'DATE_ADD('.$date_from_ginvoice.', INTERVAL 6 DAY) as end_date,';
        }

        $result = $db->query(
            'SELECT
            '.$date_from_ginvoice.' AS fix_date,
            '.($this->context->cookie->stats_granularity == 42 ? $date_to_ginvoice : '').'
            COUNT(o.`id_order`) AS countOrders,
            SUM((SELECT IFNULL(SUM(DATEDIFF(hbd.`date_to`, hbd.`date_from`)), 0) FROM `'._DB_PREFIX_.'htl_booking_detail` hbd WHERE o.`id_order` = hbd.`id_order`)) AS totalRoomsBooked,
            SUM(o.`total_paid_tax_excl` / o.`conversion_rate`) AS totalSales,
            SUM((
                SELECT SUM(ROUND(DATEDIFF(hbd.`date_to`, hbd.`date_from`) * (
                    CASE
                        WHEN od.`original_wholesale_price` <> "0.000000" THEN od.`original_wholesale_price`
                        WHEN p.`wholesale_price` <> "0.000000" THEN p.`wholesale_price`
                        ELSE 0
                    END
                ), 2))
                FROM `'._DB_PREFIX_.'htl_booking_detail` hbd
                LEFT JOIN `'._DB_PREFIX_.'product` p
                ON (p.`id_product` = hbd.`id_product`)
                LEFT JOIN `'._DB_PREFIX_.'order_detail` od
                ON (od.`id_order_detail` = hbd.`id_order_detail`)
                WHERE hbd.`id_order` = o.`id_order`
            )) AS totalOperatingCost
            FROM '._DB_PREFIX_.'orders o
            WHERE o.`valid` = 1
            AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
            '.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
            GROUP BY '.$date_from_ginvoice
        );

        while ($row = $db->nextRow($result)) {
            $data_table[$row['fix_date']] = $row;
        }

        $this->html .= '<div>
			<div class="panel-heading"><i class="icon-dashboard"></i> '.$this->displayName.'</div>
			<div class="alert alert-info">'.$this->l('The listed amounts do not include tax.').'</div>
			<form id="granularity" action="'.Tools::safeOutput($ru).'" method="post" class="form-horizontal">
				<div class="row row-margin-bottom">
					<label class="control-label col-lg-3">
						'.$this->l('Time frame').'
					</label>
					<div class="col-lg-2">
						<input type="hidden" name="submitGranularity" value="1" />
						<select name="stats_granularity" onchange="this.form.submit();">
							<option value="10">'.$this->l('Daily').'</option>
							<option value="42" '.($this->context->cookie->stats_granularity == '42' ? 'selected="selected"' : '').'>'.$this->l('Weekly').'</option>
							<option value="7" '.($this->context->cookie->stats_granularity == '7' ? 'selected="selected"' : '').'>'.$this->l('Monthly').'</option>
							<option value="4" '.($this->context->cookie->stats_granularity == '4' ? 'selected="selected"' : '').'>'.$this->l('Yearly').'</option>
						</select>
					</div>
				</div>
			</form>

			<table class="table">
				<thead>
					<tr>
						<th></th>
						<th class="text-left"><span class="title_box active">'.$this->l('Visits').'</span></th>
						<th class="text-left"><span class="title_box active">'.$this->l('Registrations').'</span></th>
						<th class="text-left"><span class="title_box active">'.$this->l('Orders placed').'</span></th>
						<th class="text-left"><span class="title_box active">'.$this->l('Booked rooms').'</span></th>
						<th class="text-left"><span class="title_box active">'.$this->l('Percentage of registrations').'</span></th>
						<th class="text-left"><span class="title_box active">'.$this->l('Conversion rate').'</span></th>
						<th class="text-left"><span class="title_box active">'.$this->l('Revenue').'</span></th>
						<th class="text-left"><span class="title_box active">'.$this->l('Margin').'</span></th>
					</tr>
				</thead>';

        $visit_array = array();
        $sql = 'SELECT '.$date_from_gadd.' as fix_date, COUNT(*) as visits
        FROM '._DB_PREFIX_.'connections c
        WHERE c.`date_add` BETWEEN '.ModuleGraph::getDateBetween().'
        '.Shop::addSqlRestriction(false, 'c').'
        GROUP BY '.$date_from_gadd;
        $visits = Db::getInstance()->query($sql);
        while ($row = $db->nextRow($visits)) {
            $visit_array[$row['fix_date']] = $row['visits'];
        }

        foreach ($data_table as $row) {
            $visits_today = (int)(isset($visit_array[$row['fix_date']]) ? $visit_array[$row['fix_date']] : 0);

            $date_from_greg = ($this->context->cookie->stats_granularity != 42
                ? 'LIKE \''.$row['fix_date'].'%\''
                : 'BETWEEN \''.Tools::substr($row['fix_date'], 0, 10).' 00:00:00\' AND DATE_ADD(\''.Tools::substr($row['fix_date'], 0, 8).Tools::substr($row['fix_date'], 8, 2).' 23:59:59\', INTERVAL 7 DAY)');
            $row['registrations'] = Db::getInstance()->getValue('
			SELECT COUNT(*) FROM '._DB_PREFIX_.'customer
			WHERE date_add BETWEEN '.ModuleGraph::getDateBetween().'
			AND date_add '.$date_from_greg
                .Shop::addSqlRestriction(Shop::SHARE_CUSTOMER));

            $this->html .= '
			<tr>
				<td class="fixed-width-sm">'.($this->context->cookie->stats_granularity == 42 ? (Tools::displayDate($row['fix_date']).' - '.Tools::displayDate($row['end_date'])) : $row['fix_date']).'</td>
				<td class="text-left">'.$visits_today.'</td>
				<td class="text-left">'.(int)$row['registrations'].'</td>
				<td class="text-left">'.(int)$row['countOrders'].'</td>
				<td class="text-left">'.(int)$row['totalRoomsBooked'].'</td>
				<td class="text-left">'.sprintf('%0.2f', $visits_today ? ($row['registrations'] / $visits_today) * 100 : 0).' %'.'</td>
				<td class="text-left">'.sprintf('%0.2f', $visits_today ? ($row['countOrders'] / $visits_today) * 100 : 0).' %'.'</td>
				<td class="text-left">'.Tools::displayPrice($row['totalSales'], $currency).'</td>
				<td class="text-left">'.Tools::displayPrice($row['totalOperatingCost'], $currency).'</td>
			</tr>';

            $this->t1 += $visits_today;
            $this->t2 += (int)$row['registrations'];
            $this->t3 += (int)$row['countOrders'];
            $this->t4 += (int)$row['totalRoomsBooked'];
            $this->t8 += $row['totalSales'];
            $this->t9 += $row['totalOperatingCost'];
        }

        $this->html .= '
				<tr>
					<th></th>
                    <th class="text-left"><span class="title_box active">'.$this->l('Visits').'</span></th>
                    <th class="text-left"><span class="title_box active">'.$this->l('Registrations').'</span></th>
                    <th class="text-left"><span class="title_box active">'.$this->l('Orders placed').'</span></th>
                    <th class="text-left"><span class="title_box active">'.$this->l('Booked rooms').'</span></th>
                    <th class="text-left"><span class="title_box active">'.$this->l('Percentage of registrations').'</span></th>
                    <th class="text-left"><span class="title_box active">'.$this->l('Conversion rate').'</span></th>
                    <th class="text-left"><span class="title_box active">'.$this->l('Revenue').'</span></th>
                    <th class="text-left"><span class="title_box active">'.$this->l('Margin').'</span></th>
				</tr>
				<tr>
					<td>'.$this->l('Total').'</td>
					<td class="text-left">'.(int)$this->t1.'</td>
					<td class="text-left">'.(int)$this->t2.'</td>
					<td class="text-left">'.(int)$this->t3.'</td>
					<td class="text-left">'.(int)$this->t4.'</td>
					<td class="text-left">--</td>
					<td class="text-left">--</td>
					<td class="text-left">'.Tools::displayPrice($this->t8, $currency).'</td>
					<td class="text-left">'.Tools::displayPrice($this->t9, $currency).'</td>
				</tr>
				<tr>
					<td>'.$this->l('Average').'</td>
					<td class="text-left">'.sprintf('%0.2f', $this->t1 / $interval_avg).'</td>
					<td class="text-left">'.sprintf('%0.2f', $this->t2 / $interval_avg).'</td>
					<td class="text-left">'.sprintf('%0.2f', $this->t3 / $interval_avg).'</td>
					<td class="text-left">'.sprintf('%0.2f', $this->t4 / $interval_avg).'</td>
					<td class="text-left">'.sprintf('%0.2f', ($this->t1 ? ($this->t2 / $this->t1) * 100 : 0)).' %</td>
					<td class="text-left">'.sprintf('%0.2f', ($this->t1 ? ($this->t3 / $this->t1) * 100 : 0)).' %</td>
					<td class="text-left">'.Tools::displayPrice($this->t8 / $interval_avg, $currency).'</td>
					<td class="text-left">'.Tools::displayPrice($this->t9 / $interval_avg, $currency).'</td>
				</tr>
				<tr>
					<td>'.$this->l('Forecast').'</td>
					<td class="text-left">'.(int)($this->t1 * $prop30).'</td>
					<td class="text-left">'.(int)($this->t2 * $prop30).'</td>
					<td class="text-left">'.(int)($this->t3 * $prop30).'</td>
					<td class="text-left">'.(int)($this->t4 * $prop30).'</td>
					<td class="text-left">--</td>
					<td class="text-left">--</td>
					<td class="text-left">'.Tools::displayPrice($this->t8 * $prop30, $currency).'</td>
					<td class="text-left">'.Tools::displayPrice($this->t9 * $prop30, $currency).'</td>
				</tr>
			</table>
		</div>';

        $ca = $this->getRealCA();

        $sql = 'SELECT COUNT(DISTINCT c.id_guest)
        FROM '._DB_PREFIX_.'connections c
        WHERE c.date_add BETWEEN '.ModuleGraph::getDateBetween();
        $visitors = Db::getInstance()->getValue($sql);

        $sql = 'SELECT COUNT(DISTINCT g.`id_customer`)
        FROM '._DB_PREFIX_.'connections c
        INNER JOIN '._DB_PREFIX_.'guest g ON c.id_guest = g.id_guest
        WHERE g.id_customer != 0
        AND c.date_add BETWEEN '.ModuleGraph::getDateBetween();
        $customers = Db::getInstance()->getValue($sql);

        $sql = 'SELECT COUNT(DISTINCT g.`id_guest`)
        FROM '._DB_PREFIX_.'connections c
        INNER JOIN '._DB_PREFIX_.'guest g ON c.`id_guest` = g.`id_guest`
        WHERE g.`id_customer` = 0
        AND c.`date_add` BETWEEN '.ModuleGraph::getDateBetween();
        $guests = Db::getInstance()->getValue($sql);

        $sql = 'SELECT COUNT(DISTINCT c.`id_cart`)
        FROM '._DB_PREFIX_.'cart c
        WHERE c.`id_customer` != 0
        AND (c.`date_add` BETWEEN '.ModuleGraph::getDateBetween().' OR c.`date_upd` BETWEEN '.ModuleGraph::getDateBetween().')';
        $carts_registered = Db::getInstance()->getValue($sql);

        $sql = 'SELECT COUNT(DISTINCT c.`id_cart`)
        FROM '._DB_PREFIX_.'cart c
        WHERE c.`id_customer` = 0
        AND (c.`date_add` BETWEEN '.ModuleGraph::getDateBetween().' OR c.`date_upd` BETWEEN '.ModuleGraph::getDateBetween().')';
        $carts_unregistered = Db::getInstance()->getValue($sql);

        $sql = 'SELECT COUNT(*)
        FROM '._DB_PREFIX_.'orders o
        WHERE o.`valid` = 1
        AND o.`date_add` BETWEEN '.ModuleGraph::getDateBetween();
        $orders = Db::getInstance()->getValue($sql);

        $sql = 'SELECT COUNT(*)
        FROM '._DB_PREFIX_.'orders o
        INNER JOIN '._DB_PREFIX_.'customer c ON c.`id_customer` = o.`id_customer`
        WHERE o.`valid` = 1 AND c.`is_guest` = 0
        AND o.`date_add` BETWEEN '.ModuleGraph::getDateBetween();
        $orders_registered = Db::getInstance()->getValue($sql);

        $sql = 'SELECT COUNT(*)
        FROM '._DB_PREFIX_.'orders o
        INNER JOIN '._DB_PREFIX_.'customer c ON c.`id_customer` = o.`id_customer`
        WHERE o.`valid` = 1 AND c.`is_guest` = 1
        AND o.`date_add` BETWEEN '.ModuleGraph::getDateBetween();
        $orders_unregistered = Db::getInstance()->getValue($sql);

        $this->context->smarty->assign(array(
            'conversion_graph_data' => array(
                'values' => array(
                    'visitors' => $visitors,
                    'visitors_registered' => $customers,
                    'visitors_unregistered' => $guests,
                    'carts_registered' => $carts_registered,
                    'carts_unregistered' => $carts_unregistered,
                    'orders' => $orders,
                    'orders_registered' => $orders_registered,
                    'orders_unregistered' => $orders_unregistered,
                ),
                'percentages' => array(
                    'visitors_registered' => sprintf('%0.2f', ($visitors ? $customers / $visitors : 0) * 100),
                    'visitors_unregistered' => sprintf('%0.2f', ($visitors ? $guests / $visitors : 0) * 100),
                    'orders' => sprintf('%0.2f', ($orders ? $carts_registered / $orders : 0) * 100),
                    'orders_top_registered' => sprintf('%0.2f', ($carts_registered ? $orders_registered / $carts_registered : 0) * 100),
                    'orders_top_unregistered' => sprintf('%0.2f', ($carts_unregistered ? $orders_unregistered / $carts_unregistered : 0) * 100),
                    'orders_bottom_registered' => sprintf('%0.2f', ($orders ? $orders_registered / $orders : 0) * 100),
                    'orders_bottom_unregistered' => sprintf('%0.2f', ($orders ? $orders_unregistered / $orders : 0) * 100),
                ),
            ),
        ));

        $this->html .= '
		<div class="row row-margin-bottom">
			<h4><i class="icon-filter"></i> '.$this->l('Conversion').'</h4>
		</div>
		<div class="row row-margin-bottom">'.
            $this->context->smarty->fetch($this->getLocalPath().'views/templates/conversion.tpl').'
		</div>
		<div class="alert alert-info">
			<p>'.$this->l('A simple statistical calculation lets you know the monetary value of your visitors:').'</p>
			<p>'.$this->l('On average, each visitor places an order for this amount:').' <b>'.Tools::displayPrice($ca['ventil']['total'] / max(1, $visitors), $currency).'.</b></p>
			<p>'.$this->l('On average, each registered visitor places an order for this amount:').' <b>'.Tools::displayPrice($ca['ventil']['total'] / max(1, $customers), $currency).'</b>.</p>
		</div>';

        $from = strtotime($employee->stats_date_from.' 00:00:00');
        $to = strtotime($employee->stats_date_to.' 23:59:59');
        $interval = ($to - $from) / 60 / 60 / 24;

        $this->html .= '
			<div class="row row-margin-bottom">
				<h4><i class="icon-money"></i> '.$this->l('Payment distribution').'</h4>
				<div class="alert alert-info">'
            .$this->l('The amounts in this section include taxes, so you can get an estimation of the commission due to the payment method.').'
				</div>
				<form id="cat" action="'.Tools::safeOutput($ru).'#payment" method="post" class="form-horizontal">
					<div class="row row-margin-bottom">
						<label class="control-label col-lg-3">
							'.$this->l('Zone:').'
						</label>
						<div class="col-lg-3">
							<input type="hidden" name="submitIdZone" value="1" />
							<select name="stats_id_zone" onchange="this.form.submit();">
								<option value="0">'.$this->l('-- No filter --').'</option>';
        foreach (Zone::getZones() as $zone) {
            $this->html .= '<option value="'.(int)$zone['id_zone'].'" '.($this->context->cookie->stats_id_zone == $zone['id_zone'] ? 'selected="selected"' : '').'>'.$zone['name'].'</option>';
        }
        $this->html .= '
							</select>
						</div>
					</div>
				</form>
				<table class="table">
					<thead>
						<tr>
							<th class="text-left"><span class="title_box active">'.$this->l('Module').'</span></th>
							<th class="text-left"><span class="title_box active">'.$this->l('Orders').'</span></th>
							<th class="text-left"><span class="title_box active">'.$this->l('Revenue').'</span></th>
							<th class="text-left"><span class="title_box active">'.$this->l('Percentage of orders').'</span></th>
							<th class="text-left"><span class="title_box active">'.$this->l('Percentage of revenue').'</span></th>
							<th class="text-left"><span class="title_box active">'.$this->l('Average order value').'</span></th>
						</tr>
					</thead>
					<tbody>';
		if (is_array($ca['payment']) && count($ca['payment'])) {
			foreach ($ca['payment'] as $payment) {
				$this->html .= '
						<tr>
							<td class="text-left">'.$payment['payment_method'].'</td>
							<td class="text-left">'.(int)$payment['nb'].'</td>
							<td class="text-left">'.Tools::displayPrice($payment['total'], $currency).'</td>
							<td class="text-left">'.sprintf('%0.2f', ($ca['ventil']['nb'] ? ($payment['nb'] / $ca['ventil']['nb']) * 100 : 0)).'%</td>
							<td class="text-left">'.sprintf('%0.2f', ($ca['ventil']['nb'] ? ($payment['total'] / $ca['ventil']['total_incl']) * 100 : 0)).'%</td>
							<td class="text-left">'.Tools::displayPrice($payment['total'] / (int)$payment['nb'], $currency).'</td>
						</tr>';
			}
		} else {
			$this->html .= '
				<tr>
					<td colspan="6" class="text-center">'.$this->l('No data available.').'</td>
				</tr>';
		}
        $this->html .= '
					</tbody>
				</table>
			</div>
			<div class="row row-margin-bottom">
				<h4><i class="icon-flag"></i> '.$this->l('Language distribution').'</h4>
				<table class="table">
					<thead>
						<tr>
							<th class="text-left"><span class="title_box active">'.$this->l('Language').'</span></th>
							<th class="text-left"><span class="title_box active">'.$this->l('Revenue').'</span></th>
							<th class="text-left"><span class="title_box active">'.$this->l('Percentage of revenue').'</span></th>
						</tr>
					</thead>
					<tbody>';
		if (is_array($ca['lang']) && count($ca['lang'])) {
			foreach ($ca['lang'] as $ophone => $amount) {
				$this->html .= '
						<tr>
							<td class="text-left">'.$ophone.'</td>
							<td class="text-left">'.Tools::displayPrice($amount, $currency).'</td>
							<td class="text-left">'.sprintf('%0.2f', ($ca['ventil']['total'] ? ($amount / $ca['ventil']['total']) * 100 : 0)).'%</td>
						</tr>';
			}
		} else {
			$this->html .= '
				<tr>
					<td colspan="5" class="text-center">'.$this->l('No data available.').'</td>
				</tr>';
		}
        $this->html .= '
					</tbody>
				</table>
			</div>
			<div class="row row-margin-bottom">
				<h4><i class="icon-map-marker"></i> '.$this->l('Zone distribution').'</h4>
				<table class="table">
					<thead>
						<tr>
							<th class="text-left"><span class="title_box active">'.$this->l('Zone').'</span></th>
							<th class="text-left"><span class="title_box active">'.$this->l('Orders').'</span></th>
							<th class="text-left"><span class="title_box active">'.$this->l('Revenue').'</span></th>
							<th class="text-left"><span class="title_box active">'.$this->l('Percentage of orders').'</span></th>
							<th class="text-left"><span class="title_box active">'.$this->l('Percentage of revenue').'</span></th>
						</tr>
					</thead>
					<tbody>';
		if (is_array($ca['zones']) && count($ca['zones'])) {
			foreach ($ca['zones'] as $zone) {
				$this->html .= '
						<tr>
							<td class="text-left">'.(isset($zone['name']) ? $zone['name'] : $this->l('Undefined')).'</td>
							<td class="text-left">'.(int)($zone['nb']).'</td>
							<td class="text-left">'.Tools::displayPrice($zone['total'], $currency).'</td>
							<td class="text-left">'.sprintf('%0.2f', $ca['ventil']['nb'] ? ($zone['nb'] / $ca['ventil']['nb']) * 100 : 0).'%</td>
							<td class="text-left">'.sprintf('%0.2f', $ca['ventil']['total'] ? ($zone['total'] / $ca['ventil']['total']) * 100 : 0).'%</td>
						</tr>';
			}
		} else {
			$this->html .= '
				<tr>
					<td colspan="5" class="text-center">'.$this->l('No data available.').'</td>
				</tr>';
		}
        $this->html .= '
					</tbody>
				</table>
			</div>
			<div class="row row-margin-bottom">
				<h4><i class="icon-money"></i> '.$this->l('Currency distribution').'</h4>
				<form id="cat_2" action="'.Tools::safeOutput($ru).'#currencies" method="post" class="form-horizontal">
					<div class="row row-margin-bottom">
						<label class="control-label col-lg-3">
							'.$this->l('Zone:').'
						</label>
						<div class="col-lg-3">
							<input type="hidden" name="submitIdZone" value="1" />
							<select name="stats_id_zone" onchange="this.form.submit();">
								<option value="0">'.$this->l('-- No filter --').'</option>';
        foreach (Zone::getZones() as $zone) {
            $this->html .= '<option value="'.(int)$zone['id_zone'].'" '.($this->context->cookie->stats_id_zone == $zone['id_zone'] ? 'selected="selected"' : '').'>'.$zone['name'].'</option>';
        }
        $this->html .= '
							</select>
						</div>
					</div>
				</form>
				<table class="table">
					<thead>
						<tr>
							<th class="text-left"><span class="title_box active">'.$this->l('Currency').'</span></th>
							<th class="text-left"><span class="title_box active">'.$this->l('Orders').'</span></th>
							<th class="text-left"><span class="title_box active">'.$this->l('Revenue (converted)').'</span></th>
							<th class="text-left"><span class="title_box active">'.$this->l('Percentage of orders').'</span></th>
							<th class="text-left"><span class="title_box active">'.$this->l('Percentage of revenue').'</span></th>
						</tr>
					</thead>
					<tbody>';
		if (is_array($ca['currencies']) && count($ca['currencies'])) {
			foreach ($ca['currencies'] as $currency_row) {
				$this->html .= '
							<tr>
								<td class="text-left">'.$currency_row['name'].'</td>
								<td class="text-left">'.(int)($currency_row['nb']).'</td>
								<td class="text-left">'.Tools::displayPrice($currency_row['total'], $currency).'</td>
								<td class="text-left">'.sprintf('%0.2f', $ca['ventil']['nb'] ? ($currency_row['nb'] / $ca['ventil']['nb']) * 100 : 0).'%</td>
								<td class="text-left">'.sprintf('%0.2f', $ca['ventil']['total'] ? ($currency_row['total'] / $ca['ventil']['total']) * 100 : 0).'%</td>
							</tr>';
			}
		} else {
			$this->html .= '
				<tr>
					<td colspan="5" class="text-center">'.$this->l('No data available.').'</td>
				</tr>';
		}
        $this->html .= '
					</tbody>
				</table>
			</div>';

        return $this->html;
    }

    private function getRealCA()
    {
        $employee = $this->context->employee;
        $ca = array();

        $where = $join = '';
        if ((int)$this->context->cookie->stats_id_zone) {
            $join = ' LEFT JOIN `'._DB_PREFIX_.'address` a ON o.id_address_invoice = a.id_address LEFT JOIN `'._DB_PREFIX_.'country` co ON co.id_country = a.id_country';
            $where = ' AND co.id_zone = '.(int)$this->context->cookie->stats_id_zone.' ';
        }

        $lang_values = '';
        $sql = 'SELECT l.id_lang, l.iso_code
				FROM `'._DB_PREFIX_.'lang` l
				'.Shop::addSqlAssociation('lang', 'l').'
				WHERE l.active = 1';
        $languages = Db::getInstance()->executeS($sql);
        foreach ($languages as $language) {
            $lang_values .= 'SUM(IF(o.id_lang = '.(int)$language['id_lang'].', total_paid_tax_excl / o.conversion_rate, 0)) as '.pSQL($language['iso_code']).',';
        }
        $lang_values = rtrim($lang_values, ',');

        if ($lang_values) {
            $sql = 'SELECT '.$lang_values.'
					FROM `'._DB_PREFIX_.'orders` o
					WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o');
            $ca['lang'] = Db::getInstance()->getRow($sql);
            arsort($ca['lang']);
        } else {
            $ca['lang'] = array();
        }

        $sql = 'SELECT reference
					FROM `'._DB_PREFIX_.'orders` o
					'.$join.'
					WHERE o.valid
					'.$where.'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					AND o.invoice_date BETWEEN '.ModuleGraph::getDateBetween().'';
        $result = Db::getInstance()->executeS($sql);
        if (count($result)) {
            $references = array();
            foreach ($result as $r) {
                $references[] = $r['reference'];
            }
            $sql = 'SELECT op.payment_method, SUM(op.amount / op.conversion_rate) as total, COUNT(DISTINCT op.order_reference) as nb
					FROM `'._DB_PREFIX_.'order_payment` op
					WHERE op.`date_add` BETWEEN '.ModuleGraph::getDateBetween().'
					AND op.order_reference IN (
						"'.implode('","', $references).'"
					)
					GROUP BY op.payment_method
					ORDER BY total DESC';
            $ca['payment'] = Db::getInstance()->executeS($sql);
        } else {
            $ca['payment'] = array();
        }

        $sql = 'SELECT z.name, SUM(o.total_paid_tax_excl / o.conversion_rate) as total, COUNT(*) as nb
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'address` a ON o.id_address_invoice = a.id_address
				LEFT JOIN `'._DB_PREFIX_.'country` c ON c.id_country = a.id_country
				LEFT JOIN `'._DB_PREFIX_.'zone` z ON z.id_zone = c.id_zone
				WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
				GROUP BY c.id_zone
				ORDER BY total DESC';
        $ca['zones'] = Db::getInstance()->executeS($sql);

        $sql = 'SELECT cu.name, SUM(o.total_paid_tax_excl / o.conversion_rate) as total, COUNT(*) as nb
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'currency` cu ON o.id_currency = cu.id_currency
				'.$join.'
				WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.$where.'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
				GROUP BY o.id_currency
				ORDER BY total DESC';
        $ca['currencies'] = Db::getInstance()->executeS($sql);

        $sql = 'SELECT SUM(total_paid_tax_excl / o.conversion_rate) as total,
			SUM(total_paid_tax_incl / o.conversion_rate) as total_incl, COUNT(*) AS nb
				FROM `'._DB_PREFIX_.'orders` o
				WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o');
        $ca['ventil'] = Db::getInstance()->getRow($sql);

        $sql = 'SELECT /*pac.id_attribute,*/ agl.name as gname, al.name as aname, COUNT(*) as total
				FROM '._DB_PREFIX_.'orders o
				LEFT JOIN '._DB_PREFIX_.'order_detail od ON o.id_order = od.id_order
				INNER JOIN '._DB_PREFIX_.'product_attribute_combination pac ON od.product_attribute_id = pac.id_product_attribute
				INNER JOIN '._DB_PREFIX_.'attribute a ON pac.id_attribute = a.id_attribute
				INNER JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (a.id_attribute_group = agl.id_attribute_group AND agl.id_lang = '.(int)$this->context->language->id.')
				INNER JOIN '._DB_PREFIX_.'attribute_lang al ON (a.id_attribute = al.id_attribute AND al.id_lang = '.(int)$this->context->language->id.')
				WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
				GROUP BY pac.id_attribute';
        $ca['attributes'] = Db::getInstance()->executeS($sql);

        return $ca;
    }
}
