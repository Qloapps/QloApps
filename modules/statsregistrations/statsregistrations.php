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

class StatsRegistrations extends ModuleGraph
{
    private $html = '';
    private $query = '';

    public function __construct()
    {
        $this->name = 'statsregistrations';
        $this->tab = 'analytics_stats';
        $this->version = '1.4.2';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Customer accounts');
        $this->description = $this->l('Adds a registration progress tab to the Stats dashboard.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7.0.99');
    }

    /**
     * Called during module installation
     */
    public function install()
    {
        return (parent::install() && $this->registerHook('AdminStatsModules'));
    }

    /**
     * @return int Get total of registration in date range
     */
    public function getTotalRegistrations()
    {
        $sql = 'SELECT COUNT(`id_customer`) as total
				FROM `'._DB_PREFIX_.'customer`
				WHERE `date_add` BETWEEN '.ModuleGraph::getDateBetween().'
				'.Shop::addSqlRestriction(Shop::SHARE_ORDER);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        return isset($result['total']) ? $result['total'] : 0;
    }

    /**
     * @return int Get total of blocked visitors during registration process
     */
    public function getBlockedVisitors()
    {
        $sql = 'SELECT COUNT(DISTINCT c.`id_guest`) as blocked
				FROM `'._DB_PREFIX_.'page_type` pt
				LEFT JOIN `'._DB_PREFIX_.'page` p ON p.id_page_type = pt.id_page_type
				LEFT JOIN `'._DB_PREFIX_.'connections_page` cp ON p.id_page = cp.id_page
				LEFT JOIN `'._DB_PREFIX_.'connections` c ON c.id_connections = cp.id_connections
				LEFT JOIN `'._DB_PREFIX_.'guest` g ON c.id_guest = g.id_guest
				WHERE pt.name = "authentication"
					'.Shop::addSqlRestriction(false, 'c').'
					AND (g.id_customer IS NULL OR g.id_customer = 0)
					AND c.`date_add` BETWEEN '.ModuleGraph::getDateBetween();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        return $result['blocked'];
    }

    public function getFirstBuyers()
    {
        $sql = 'SELECT COUNT(DISTINCT o.`id_customer`) as buyers
				FROM `'._DB_PREFIX_.'orders` o
                INNER JOIN `'._DB_PREFIX_.'htl_booking_detail` hbd ON (hbd.`id_order` = o.`id_order`)
				LEFT JOIN `'._DB_PREFIX_.'guest` g ON o.id_customer = g.id_customer
				LEFT JOIN `'._DB_PREFIX_.'connections` c ON c.id_guest = g.id_guest
				WHERE o.`date_add` BETWEEN '.ModuleGraph::getDateBetween().'
                    '.HotelBranchInformation::addHotelRestriction(false, 'hbd').'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					AND o.valid = 1
					AND ABS(TIMEDIFF(o.date_add, c.date_add)+0) < 120000';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

        return $result['buyers'];
    }

    public function hookAdminStatsModules()
    {
        $total_registrations = $this->getTotalRegistrations();
        $total_blocked = $this->getBlockedVisitors();
        $total_buyers = $this->getFirstBuyers();
        if (Tools::getValue('export')) {
            $this->csvExport(array(
                'layers' => 0,
                'type' => 'line'
            ));
        }
        $this->html = '
		<div class="panel-heading">
			'.$this->displayName.'
		</div>
		<div class="alert alert-info">
			<ul>
				<li>
					'.$this->l('Number of visitors who stopped at registration step:').' <span class="totalStats">'.(int)$total_blocked.($total_registrations ? ' ('.number_format(100 * $total_blocked / ($total_registrations + $total_blocked), 2).'%)' : '').'</span></li>
					<li>'.$this->l('Number of visitors who placed an order directly after registration:').' <span class="totalStats">'.(int)$total_buyers.($total_registrations ? ' ('.number_format(100 * $total_buyers / ($total_registrations), 2).'%)' : '').'</span></li>
				<li>
					'.$this->l('Total customer accounts:').' <span class="totalStats">'.$total_registrations.'</span></li>
			</ul>
		</div>
		<h4>'.$this->l('Guide').'</h4>
		<div class="alert alert-warning">
			<h4>'.$this->l('Number of customer accounts created').'</h4>
			<p>'.$this->l('The total number of accounts created is not in itself important information. However, it is beneficial to analyze the number created over time. This will indicate whether or not things are on the right track. You feel me?').'</p>
		</div>
		<h4>'.$this->l('How to act on the registrations\' evolution?').'</h4>
		<div class="alert alert-warning">
			'.$this->l('If you let your website run without changing anything, the number of customer registrations should stay stable or show a slight decline.').'
			'.$this->l('A significant increase or decrease in customer registration shows that there has probably been a change to your website. With that in mind, we suggest that you identify the cause, correct the issue and get back in the business of making money!').'<br />
			'.$this->l('Here is a summary of what may affect the creation of customer accounts:').'
			<ul>
				<li>'.$this->l('An advertising campaign can attract an increased number of visitors to your website. This will likely be followed by an increase in customer accounts and profit margins, which will depend on customer "quality." Well-targeted advertising is typically more effective than large-scale advertising... and it\'s cheaper too!').'</li>
				<li>'.$this->l('Specials, sales, promotions and/or contests typically demand a customers\' attentions. Offering such things will not only keep your business lively, it will also increase traffic, build customer loyalty and genuinely change your current business philosophy.').'</li>
				<li>'.$this->l('Design and user-friendliness are more important than ever in the world of online sales. An ill-chosen or hard-to-follow graphical theme can keep customers at bay. This means that you should aspire to find the right balance between beauty and functionality for your website.').'</li>
			</ul>
		</div>

		<div class="row row-margin-bottom">
			<div class="col-lg-12">
				<div class="col-lg-8">
					'.$this->engine(array('type' => 'line')).'
				</div>
				<div class="col-lg-4">
					<a class="btn btn-default export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1').'">
						<i class="icon-cloud-download"></i> '.$this->l('CSV Export').'
					</a>
				</div>
			</div>
		</div>';

        return $this->html;
    }

    protected function getData($layers)
    {
        $this->query = '
			SELECT `date_add`
			FROM `'._DB_PREFIX_.'customer`
			WHERE 1
				'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
				AND `date_add` BETWEEN';
        $this->_titles['main'] = $this->l('Number of customer registrations');
        $this->_formats['y'] = 'd';
        $this->setDateGraph($layers, true);
    }

    protected function setAllTimeValues($layers)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query.$this->getDate());
        foreach ($result as $row) {
            $this->_values[(int)Tools::substr($row['date_add'], 0, 4)]++;
        }
    }

    protected function setYearValues($layers)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query.$this->getDate());
        foreach ($result as $row) {
            $mounth = (int)substr($row['date_add'], 5, 2);
            if (!isset($this->_values[$mounth])) {
                $this->_values[$mounth] = 0;
            }
            $this->_values[$mounth]++;
        }
    }

    protected function setMonthValues($layers)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query.$this->getDate());
        foreach ($result as $row) {
            $this->_values[(int)Tools::substr($row['date_add'], 8, 2)]++;
        }
    }

    protected function setDayValues($layers)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->query.$this->getDate());
        foreach ($result as $row) {
            $this->_values[(int)Tools::substr($row['date_add'], 11, 2)]++;
        }
    }
}
