{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script>
	var currency_format = {$currency->format|intval};
	var currency_sign = '{$currency->sign|@addcslashes:'\''}';
	var currency_blank = {$currency->blank|intval};
	var priceDisplayPrecision = 0;
</script>
<div class="clearfix"></div>
<section id="dashtrends" class="panel widget{if $allow_push} allow_push{/if}">
	<header class="panel-heading">
		<i class="icon-bar-chart"></i> {l s='Dashboard' mod='dashtrends'}
		<span class="panel-heading-action">
			<a class="list-toolbar-btn" href="{$link->getAdminLink('AdminDashboard')|escape:'html':'UTF-8'}&amp;profitability_conf=1" title="{l s='Configure' mod='dashtrends'}">
				<i class="process-icon-configure"></i>
			</a>
			<a class="list-toolbar-btn" href="#" onclick="refreshDashboard('dashtrends'); return false;" title="{l s='Refresh' mod='dashtrends'}">
				<i class="process-icon-refresh"></i>
			</a>
		</span>
	</header>
	<div id="dashtrends_toolbar" class="row">
		<dl class="col-xs-4 col-lg-2 label-tooltip" onclick="selectDashtrendsChart(this, 'sales');" data-toggle="tooltip" data-original-title="{l s='Sum of revenue (excl. tax) generated within the date range by orders considered validated.' mod='dashtrends'}" data-placement="bottom">
				<dt>{l s='Sales' mod='dashtrends'}</dt>
				<dd class="data_value size_l"><span id="sales_score"></span></dd>
				<dd class="dash_trend"><span id="sales_score_trends"></span></dd>
		</dl>
		<dl class="col-xs-4 col-lg-2 label-tooltip" onclick="selectDashtrendsChart(this, 'orders');" data-toggle="tooltip" data-original-title="{l s='Total number of orders received within the date range that are considered validated.' mod='dashtrends'}" data-placement="bottom">
				<dt>{l s='Orders' mod='dashtrends'}</dt>
				<dd class="data_value size_l"><span id="orders_score"></span></dd>
				<dd class="dash_trend"><span id="orders_score_trends"></span></dd>
		</dl>
		<dl class="col-xs-4 col-lg-2 label-tooltip" onclick="selectDashtrendsChart(this, 'average_cart_value');" data-toggle="tooltip" data-original-title="{l s='Average Cart Value is a metric representing the value of an average order within the date range. It is calculated by dividing Sales by Orders.' mod='dashtrends'}" data-placement="bottom">
				<dt>{l s='Cart Value' mod='dashtrends'}</dt>
				<dd class="data_value size_l"><span id="cart_value_score"></span></dd>
				<dd class="dash_trend"><span id="cart_value_score_trends"></span></dd>
		</dl>
		<dl class="col-xs-4 col-lg-2 label-tooltip" onclick="selectDashtrendsChart(this, 'visits');" data-toggle="tooltip" data-original-title="{l s='Total number of visits within the date range. A visit is the period of time a user is actively engaged with your website.' mod='dashtrends'}" data-placement="bottom">
				<dt>{l s='Visits' mod='dashtrends'}</dt>
				<dd class="data_value size_l"><span id="visits_score"></span></dd>
				<dd class="dash_trend"><span id="visits_score_trends"></span></dd>
		</dl>
		<dl class="col-xs-4 col-lg-2 label-tooltip" onclick="selectDashtrendsChart(this, 'conversion_rate');" data-toggle="tooltip" data-original-title="{l s='Ecommerce Conversion Rate is the percentage of visits that resulted in an validated order.' mod='dashtrends'}" data-placement="bottom">
			<dt>{l s='Conversion Rate' mod='dashtrends'}</dt>
			<dd class="data_value size_l"><span id="conversion_rate_score"></span></dd>
			<dd class="dash_trend"><span id="conversion_rate_score_trends"></span></dd>
		</dl>
		<dl class="col-xs-4 col-lg-2 label-tooltip" onclick="selectDashtrendsChart(this, 'net_profits');" data-toggle="tooltip" data-original-title="{l s='Net profit is a measure of the profitability of a venture after accounting for all Ecommerce costs. You can provide these costs by clicking on the configuration icon right above here.' mod='dashtrends'}" data-placement="bottom">
				<dt>{l s='Net Profit' mod='dashtrends'}</dt>
				<dd class="data_value size_l"><span id="net_profits_score"></span></dd>
				<dd class="dash_trend"><span id="net_profits_score_trends"></span></dd>
		</dl>
	</div>
	<div id="dash_trends_chart1" class="chart with-transitions">
		<svg></svg>
	</div>
</section>
