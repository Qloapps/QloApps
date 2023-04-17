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

class StatsOrigin extends ModuleGraph
{
    private $_html;

    public function __construct()
    {
        $this->name = 'statsorigin';
        $this->tab = 'analytics_stats';
        $this->version = '1.4.2';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Visitors origin');
        $this->description = $this->l('Adds a graph displaying the websites your visitors came from to the Stats dashboard.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7.0.99');
    }

    public function install()
    {
        return (parent::install() && $this->registerHook('AdminStatsModules'));
    }

    private function getOrigins($dateBetween)
    {
        $directLink = $this->l('Direct link');
        $sql = 'SELECT http_referer
				FROM '._DB_PREFIX_.'connections
				WHERE 1
					'.Shop::addSqlRestriction().'
					AND date_add BETWEEN '.$dateBetween;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->query($sql);
        $websites = array($directLink => 0);
        while ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->nextRow($result)) {
            if (!isset($row['http_referer']) || empty($row['http_referer'])) {
                ++$websites[$directLink];
            } else {
                $website = preg_replace('/^www./', '', parse_url($row['http_referer'], PHP_URL_HOST));
                if (!isset($websites[$website])) {
                    $websites[$website] = 1;
                } else {
                    ++$websites[$website];
                }
            }
        }
        arsort($websites);
        return $websites;
    }

    public function hookAdminStatsModules()
    {
        $websites = $this->getOrigins(ModuleGraph::getDateBetween());
        if (Tools::getValue('export')) {
            if (Tools::getValue('exportType') == 'top') {
                $this->csvExport(array('type' => 'pie'));
            }
        }
        $this->_html = '<div class="panel-heading">'.$this->l('Origin').'</div>';
        if (count($websites)) {
            $this->_html .= '
			<div class="alert alert-info">
				'.$this->l('This page lists the 10 most popular referral websites that bring customers to your website.').'
			</div>
			<h4>'.$this->l('Guide').'</h4>
			<div class="alert alert-warning">
				<h4>'.$this->l('What is a referral website?').'</h4>
				<p>
					'.$this->l('A referer is the URL of the previous webpage from which a link was followed by a visitor.').'<br />
					'.$this->l('A referer also enables you to know which keywords visitors use in search engines when searching for your website.').'<br /><br />
					'.$this->l('A referer can be:').'
				</p>
				<ul>
					<li>'.$this->l('Someone who posts a link to your website.').'</li>
					<li>'.$this->l('A partner who has agreed to a link exchange in order to attract new customers.').'</li>
				</ul>
			</div>
			<div class="row row-margin-bottom">
				<div class="col-lg-12">
					<div class="col-lg-8">
						'.$this->engine(array('type' => 'pie')).'
					</div>
					<div class="col-lg-4">
						<a href="'.Tools::safeOutput($_SERVER['REQUEST_URI'].'&export=1&exportType=top').'" class="btn btn-default">
							<i class="icon-cloud-download"></i> '.$this->l('CSV Export').'
						</a>
					</div>
				</div>
			</div>
			<table class="table">
				<thead>
					<tr>
						<th><span class="title_box active">'.$this->l('Origin').'</span></th>
						<th><span class="title_box active">'.$this->l('Total').'</span></th>
					</tr>
				</thead>
				<tbody>';
            foreach ($websites as $website => $total) {
                $this->_html .= '
					<tr>
						<td>'.(!strstr($website, ' ') ? '<a href="'.Tools::getProtocol().$website.'">' : '').$website.(!strstr($website, ' ') ? '</a>' : '').'</td><td>'.$total.'</td>
					</tr>';
            }
            $this->_html .= '
				</tbody>
			</table>';
        } else {
            $this->_html .= '<p>'.$this->l('Direct links only').'</p>';
        }
        return $this->_html;
    }

    protected function getData($layers)
    {
        $this->_titles['main'] = $this->l('Top ten referral websites');
        $websites = $this->getOrigins($this->getDate());
        $total = 0;
        $total2 = 0;
        $i = 0;
        foreach ($websites as $website => $totalRow) {
            if (!$totalRow) {
                continue;
            }
            $total += $totalRow;
            if ($i++ < 9) {
                $this->_legend[] = $website;
                $this->_values[] = $totalRow;
                $total2 += $totalRow;
            }
        }
        if ($total != $total2) {
            $this->_legend[] = $this->l('Others');
            $this->_values[] = $total - $total2;
        }
    }
}
