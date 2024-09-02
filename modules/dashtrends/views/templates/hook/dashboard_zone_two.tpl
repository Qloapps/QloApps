{*
* Copyright since 2007 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright since 2007 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class='clearfix'></div>

<div class='col-sm-12'>
	<script>
		var currency_format = {$currency->format|floatval};
		var currency_sign = '{$currency->sign|@addcslashes:'\''}';
		var currency_blank = {$currency->blank|intval};
		var priceDisplayPrecision = {$_PS_PRICE_DISPLAY_PRECISION_|intval};
	</script>

	<section id="dashtrends" class="panel widget{if $allow_push} allow_push{/if}">
		<header class="panel-heading">
			<i class="icon-bar-chart"></i> {l s='Revenue' mod='dashtrends'}
			<span class="panel-heading-action">
				<a class="list-toolbar-btn" href="{$link->getAdminLink('AdminDashboard')|escape:'html':'UTF-8'}&amp;profitability_conf=1" title="{l s='Configure' mod='dashtrends'}">
					<i class="process-icon-configure"></i>
				</a>
				<a class="list-toolbar-btn" href="#" onclick="refreshDashboard('dashtrends'); return false;" title="{l s='Refresh' mod='dashtrends'}">
					<i class="process-icon-refresh"></i>
				</a>
			</span>
		</header>
		<section id="dashtrends_toolbar">
			<div class="col-md-4 col-xs-6">
				<dl class="label-tooltip" onclick="selectDashtrendsChart(this, 'sales');" data-toggle="tooltip" data-placement="top" data-original-title="{l s='Sum of revenue (excluding tax) generated within the date range by orders considered validated.' mod='dashtrends'}" data-placement="bottom" style='background-color: #72C3F0;'>
					<dt>{l s='Sales' mod='dashtrends'}</dt>
					<dd class="data_value size_l"><span id="sales_score"></span></dd>
				</dl>
			</div>
			<div class="col-md-4 col-xs-6">
				<dl class="label-tooltip" onclick="selectDashtrendsChart(this, 'orders');" data-toggle="tooltip" data-placement="top" data-original-title="{l s='Total number of orders received within the date range that are considered validated.' mod='dashtrends'}" data-placement="bottom" style='background-color: #56CE56;'>
					<dt>{l s='Orders' mod='dashtrends'}</dt>
					<dd class="data_value size_l"><span id="orders_score"></span></dd>
				</dl>
			</div>
			<div class="col-md-4 col-xs-6">
				<dl class="label-tooltip" onclick="selectDashtrendsChart(this, 'average_cart_value');" data-toggle="tooltip" data-placement="top" data-original-title="{l s='Average Order Value is a metric representing the value of an average order within the date range. It is calculated by dividing sales by bookings. This data is provided by the module "Data mining for statistics", so please make sure it is installed and configured.' mod='dashtrends'}" data-placement="bottom" style='background-color: #FF655C;'>
					<dt>{l s='Average Order Value' mod='dashtrends'}</dt>
					<dd class="data_value size_l"><span id="cart_value_score"></span></dd>
				</dl>
			</div>
			<div class="col-md-4 col-xs-6">
				<dl class="label-tooltip" onclick="selectDashtrendsChart(this, 'visits');" data-toggle="tooltip" data-placement="top" data-original-title="{l s='Total number of visits within the date range. A visit is the period of time a user is actively engaged with your website. This data is provided by the module "Data mining for statistics", so please make sure it is installed and configured.' mod='dashtrends'}" data-placement="bottom" style='background-color: #FF7F0E;'>
					<dt>{l s='Visits' mod='dashtrends'}</dt>
					<dd class="data_value size_l"><span id="visits_score"></span></dd>
				</dl>
			</div>
			<div class="col-md-4 col-xs-6">
				<dl class="label-tooltip" onclick="selectDashtrendsChart(this, 'conversion_rate');" data-toggle="tooltip" data-placement="top" data-original-title="{l s='Conversion Rate is the percentage of visits that resulted in a validated booking.' mod='dashtrends'}" data-placement="bottom" style="background-color: #A569DF;">
					<dt>{l s='Conversion Rate' mod='dashtrends'}</dt>
					<dd class="data_value size_l"><span id="conversion_rate_score"></span></dd>
				</dl>
			</div>
			<div class="col-md-4 col-xs-6">
				<dl class="label-tooltip" onclick="selectDashtrendsChart(this, 'net_profits');" data-toggle="tooltip" data-placement="top" data-original-title="{l s='Net Profit is the amount of money earned after deducting all operating costs and expenses over a period of time. You can provide these costs by clicking on the configuration icon right above here.' mod='dashtrends'}" data-placement="bottom" style="background-color: #AF8A42;">
					<dt>{l s='Net Profit' mod='dashtrends'}</dt>
					<dd class="data_value size_l"><span id="net_profits_score"></span></dd>
				</dl>
			</div>
		</section>
		<div id="dash_trends_chart1" class="chart with-transitions">
			<svg></svg>
		</div>
		<div class='clearfix'></div>
	</section>
</div>
<div class='clearfix'></div>
