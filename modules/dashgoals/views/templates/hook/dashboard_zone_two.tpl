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

<div class="clearfix"></div>
<div class="col-sm-12">
	<script>
		var currency_format = {$currency->format|intval};
		var currency_sign = "{$currency->sign|addslashes}";
		var currency_blank = {$currency->blank|intval};
		var priceDisplayPrecision = 0;
		var dashgoals_year = {$goals_year|intval};
		var dashgoals_ajax_link = "{$dashgoals_ajax_link|addslashes}";
	</script>

	<section id="dashgoals" class="panel widget">
		<header class="panel-heading">
			<i class="icon-bar-chart"></i>
			{l s='Target' mod='dashgoals'}
			<span id="dashgoals_title" class="badge">{$goals_year}</span>
			<span class="btn-group">
				<a href="javascript:void(0);" onclick="dashgoals_changeYear('backward');" class="btn btn-default btn-xs"><i class="icon-backward"></i></a>
				<a href="javascript:void(0);" onclick="dashgoals_changeYear('forward');" class="btn btn-default btn-xs"><i class="icon-forward"></i></a>
			</span>
			
			<span class="panel-heading-action">
				<a class="list-toolbar-btn" href="javascript:void(0);" onclick="toggleDashConfig('dashgoals');" title="{l s="Configure" mod="dashtrends"}">
					<i class="process-icon-configure"></i>
				</a>
				<a class="list-toolbar-btn" href="javascript:void(0);" onclick="refreshDashboard('dashgoals');" title="{l s="Refresh" mod="dashtrends"}">
					<i class="process-icon-refresh"></i>
				</a>
			</span>
		</header>
		{include file='./config.tpl'}
		<section class="loading text-center">
			<div class="dashgoals row">
				<div class="col-xs-6 col-sm-3">
					<label class="btn btn-default label-tooltip" style="background-color:{$colors[0]};"
						data-toggle="tooltip" data-original-title="{l s="Traffic is the measure of number of visitors on your website over a given time period." mod="dashgoals"}">
						<input type="radio" name="options" onchange="selectDashgoalsChart('traffic');"/>
						{l s="Traffic" mod="dashgoals"}
					</label>
				</div>
				<div class="col-xs-6 col-sm-3">
					<label class="btn btn-default label-tooltip" style="background-color:{$colors[1]};"
						data-toggle="tooltip" data-original-title="{l s="Conversion is the measure of visitors who make a booking on your website over a given time period." mod="dashgoals"}">
						<input type="radio" name="options" onchange="selectDashgoalsChart('conversion');"/>
						{l s="Conversion" mod="dashgoals"}
					</label>
				</div>
				<div class="col-xs-6 col-sm-3">
					<label class="btn btn-default label-tooltip" style="background-color:{$colors[2]};"
						data-toggle="tooltip" data-original-title="{l s="Average Cart Value is the average amount spent on each booking over a given time period." mod="dashgoals"}">
						<input type="radio" name="options" onchange="selectDashgoalsChart('avg_cart_value');"/>
						{l s="Avg. Cart Value" mod="dashgoals"}
					</label>
				</div>
				<div class="col-xs-6 col-sm-3">
					<label class="btn btn-default label-tooltip" style="background-color:{$colors[3]};"
						data-toggle="tooltip" data-original-title="{l s="Sales is the measure of total sales on your website over a given time period." mod="dashgoals"}">
						<input type="radio" name="options" onchange="selectDashgoalsChart('sales');"/>
						{l s="Sales" mod="dashgoals"}
					</label>
				</div>
			</div>
			<div id="dash_goals_chart1" class="chart with-transitions">
				<svg></svg>
			</div>
		</section>
	</section>
</div>
<div class="clearfix"></div>