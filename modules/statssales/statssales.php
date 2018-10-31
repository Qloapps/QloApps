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

class StatsSales extends ModuleGraph
{
    private $html = '';
    private $query = '';
    private $query_group_by = '';
    private $option = '';
    private $id_country = '';

    public function __construct()
    {
        $this->name = 'statssales';
        $this->tab = 'analytics_stats';
        $this->version = '1.3.1';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Sales and orders');
        $this->description = $this->l('Adds graphics presenting the evolution of sales and orders to the Stats dashboard.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return (parent::install() && $this->registerHook('AdminStatsModules'));
    }

    public function hookAdminStatsModules()
    {
        $totals = $this->getTotals();
        $currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
        if (($id_export = (int)Tools::getValue('export')) == 1) {
            $this->csvExport(array(
                'layers' => 0,
                'type' => 'line',
                'option' => '1-'.(int)Tools::getValue('id_country')
            ));
        } elseif ($id_export == 2) {
            $this->csvExport(array(
                'layers' => 0,
                'type' => 'line',
                'option' => '2-'.(int)Tools::getValue('id_country')
            ));
        } elseif ($id_export == 3) {
            $this->csvExport(array(
                'type' => 'pie',
                'option' => '3-'.(int)Tools::getValue('id_country')
            ));
        }

        $this->html = '
			<div class="panel-heading">
				'.$this->displayName.'
			</div>
			<h4>'.$this->l('Guide').'</h4>
			<div class="alert alert-warning">
				<h4>'.$this->l('About order statuses').'</h4>
				<p>
					'.$this->l('In your Back Office, you can modify the following order statuses: Awaiting Check Payment, Payment Accepted, Preparation in Progress, Canceled, Refund, Payment Error, Out of Stock, and Awaiting Bank Wire Payment.').'<br />
					'.$this->l('These order statuses cannot be removed from the Back Office; however you have the option to add more.').'
				</p>
			</div>
			<div class="alert alert-info">
				<p>'
                    .$this->l('The following graphs represent the evolution of your website\'s orders and sales turnover for a selected period.').'<br/>'
                    .$this->l('You should often consult this screen, as it allows you to quickly monitor your website\'s sustainability. It also allows you to monitor multiple time periods.').'<br/>'
                    .$this->l('Only valid orders are graphically represented.')
                .'</p>
			</div>
			<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" class="form-horizontal alert">
				<div class="row">
					<div class="col-lg-4 col-lg-offset-7">
						<select name="id_country">
							<option value="0"'.((!Tools::getValue('id_order_state')) ? ' selected="selected"' : '').'>'.$this->l('All countries').'</option>';
        foreach (Country::getCountries($this->context->language->id) as $country) {
            $this->html .= '<option value="'.$country['id_country'].'"'.(($country['id_country'] == Tools::getValue('id_country')) ? ' selected="selected"' : '').'>'.$country['name'].'</option>';
        }
        $this->html .= '</select>
					</div>
					<div class="col-lg-1">
						<input type="submit" name="submitCountry" value="'.$this->l('Filter').'" class="btn btn-default pull-right" />
					</div>
				</div>
			</form>
			<div class="row row-margin-bottom">
				<div class="col-lg-12">
					<div class="col-lg-8">
						'.$this->engine(array(
                'type' => 'line',
                'option' => '1-'.(int)Tools::getValue('id_country'),
            )).'
					</div>
					<div class="col-lg-4">
						<ul class="list-unstyled">
							<li>'.$this->l('Orders placed:').' <span class="totalStats">'.(int)$totals['orderCount'].'</span></li>
						</ul>
						<hr/>
						<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1').'">
							<i class="icon-cloud-upload"></i> '.$this->l('CSV Export').'
						</a>
					</div>
				</div>
			</div>
			<div class="row row-margin-bottom">
				<div class="col-lg-12">
					<div class="col-lg-8">
						'.$this->engine(array(
                'type' => 'line',
                'option' => '2-'.(int)Tools::getValue('id_country')
            )).'
					</div>
					<div class="col-lg-4">
						<ul class="list-unstyled">
							<li>'.$this->l('Sales:').' '.Tools::displayPrice($totals['orderSum'], $currency).'</li>
						</ul>
						<hr/>
						<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=2').'">
							<i class="icon-cloud-upload"></i> '.$this->l('CSV Export').'
						</a>
					</div>
				</div>
			</div>
			<div class="alert alert-info">
				'.$this->l('You can view the distribution of order statuses below.').'
			</div>
			<div class="row row-margin-bottom">
				<div class="col-lg-12">
					<div class="col-lg-8">
						'.($totals['orderCount'] ? $this->engine(array(
                'type' => 'pie',
                'option' => '3-'.(int)Tools::getValue('id_country')
            )) : $this->l('No orders for this period.')).'
					</div>
					<div class="col-lg-4">
						<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=3').'">
							<i class="icon-cloud-upload"></i> '.$this->l('CSV Export').'
						</a>
					</div>
				</div>
			</div>';

        return $this->html;
    }

    private function getTotals()
    {
        $sql = 'SELECT COUNT(o.`id_order`) as orderCount, SUM(o.`total_paid_real` / o.conversion_rate) as orderSum
				FROM `'._DB_PREFIX_.'orders` o
				'.((int)Tools::getValue('id_country') ? 'LEFT JOIN `'._DB_PREFIX_.'address` a ON o.id_address_delivery = a.id_address' : '').'
				WHERE o.valid = 1
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					'.((int)Tools::getValue('id_country') ? 'AND a.id_country = '.(int)Tools::getValue('id_country') : '').'
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween();
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public function setOption($options, $layers = 1)
    {
        list($this->option, $this->id_country) = explode('-', $options);
        switch ($this->option) {
            case 1:
                $this->_titles['main'] = $this->l('Orders placed');
                break;
            case 2:
                $currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
                $this->_titles['main'] = sprintf($this->l('Sales currency: %s'), $currency->iso_code);
                break;
            case 3:
                $this->_titles['main'] = $this->l('Percentage of orders per status.');
                break;
        }
    }

    protected function getData($layers)
    {
        if ($this->option == 3) {
            return $this->getStatesData();
        }

        $this->query = '
			SELECT o.`invoice_date`, o.`total_paid_real` / o.conversion_rate as total_paid_real
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON od.`id_order` = o.`id_order`
			'.((int)$this->id_country ? 'LEFT JOIN `'._DB_PREFIX_.'address` a ON o.id_address_delivery = a.id_address' : '').'
			WHERE o.valid = 1
				'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
				'.((int)$this->id_country ? 'AND a.id_country = '.(int)$this->id_country : '').'
				AND o.`invoice_date` BETWEEN ';
        $this->query_group_by = ' GROUP BY o.id_order';
        $this->setDateGraph($layers, true);
    }

    protected function setAllTimeValues($layers)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query.$this->getDate().$this->query_group_by);
        foreach ($result as $row) {
            if ($this->option == 1) {
                $this->_values[(int)substr($row['invoice_date'], 0, 4)] += 1;
            } else {
                $this->_values[(int)substr($row['invoice_date'], 0, 4)] += $row['total_paid_real'];
            }
        }
    }

    protected function setYearValues($layers)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query.$this->getDate().$this->query_group_by);
        foreach ($result as $row) {
            $mounth = (int)substr($row['invoice_date'], 5, 2);
            if (!isset($this->_values[$mounth])) {
                $this->_values[$mounth] = 0;
            }
            if ($this->option == 1) {
                $this->_values[$mounth] += 1;
            } else {
                $this->_values[$mounth] += $row['total_paid_real'];
            }
        }
    }

    protected function setMonthValues($layers)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query.$this->getDate().$this->query_group_by);
        foreach ($result as $row) {
            if ($this->option == 1) {
                $this->_values[(int)substr($row['invoice_date'], 8, 2)] += 1;
            } else {
                $this->_values[(int)substr($row['invoice_date'], 8, 2)] += $row['total_paid_real'];
            }
        }
    }

    protected function setDayValues($layers)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query.$this->getDate().$this->query_group_by);
        foreach ($result as $row) {
            if ($this->option == 1) {
                $this->_values[(int)substr($row['invoice_date'], 11, 2)] += 1;
            } else {
                $this->_values[(int)substr($row['invoice_date'], 11, 2)] += $row['total_paid_real'];
            }
        }
    }

    private function getStatesData()
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT osl.`name`, COUNT(oh.`id_order`) as total
		FROM `'._DB_PREFIX_.'order_state` os
		LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$this->getLang().')
		LEFT JOIN `'._DB_PREFIX_.'order_history` oh ON os.`id_order_state` = oh.`id_order_state`
		LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = oh.`id_order`
		'.((int)$this->id_country ? 'LEFT JOIN `'._DB_PREFIX_.'address` a ON o.id_address_delivery = a.id_address' : '').'
		WHERE oh.`id_order_history` = (
			SELECT ios.`id_order_history`
			FROM `'._DB_PREFIX_.'order_history` ios
			WHERE ios.`id_order` = oh.`id_order`
			ORDER BY ios.`date_add` DESC, oh.`id_order_history` DESC
			LIMIT 1
		)
		'.((int)$this->id_country ? 'AND a.id_country = '.(int)$this->id_country : '').'
		AND o.`date_add` BETWEEN '.ModuleGraph::getDateBetween().'
		GROUP BY oh.`id_order_state`');
        foreach ($result as $row) {
            $this->_values[] = $row['total'];
            $this->_legend[] = $row['name'];
        }
    }
}
