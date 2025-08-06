{**
* 2010-2021 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2021 Webkul IN
* @license LICENSE.txt
*}

<div class="col-md-12 col-lg-12">
	<section id="dashavailability" class="panel widget allow_push">
		<header class="panel-heading">
			<i class="icon-bar-chart"></i>
			{l s="Availability" mod='dashavailability'}
			<span class="panel-heading-action">
			<a class="list-toolbar-btn" href="javascript:void(0);" title="Refresh" onclick="refreshAvailabilityBarData();" title="{l s="Refresh" mod='dashavailability'}">
					<i class="process-icon-refresh"></i>
				</a>
			</span>
		</header>
		<div class="row avil-chart-head">
				<div class="col-xs-5 col-lg-6">
					<div class="pull-left">
						<button class="avail-bar-date datepicker" type="button" id="avail_datepicker"
						onclick="availDatePicker()">
							<i class="icon-calendar-empty"></i>
							<span class="hidden-xs bar-date">
								{l s="From" mod='dashavailability'}
								<strong>{$dateFromBar|escape:'htmlall':'UTF-8'}</strong>
							</span>
							<i class="icon-caret-down"></i>
						</button>
						<input type="text" id="bardate" name="datepickerFrom" class="datepicker">
					</div>
				</div>
				<div class="col-xs-2 col-md-2  col-lg-2 pull-left">
					<button id='avail_bar_day_5' class="avail-bar-btn bar-btn-active" data-days="5">
						{l s="5 Days" mod='dashavailability'}
					</button>
				</div>
				<div class="col-xs-2 col-md-2  col-lg-2 pull-left">
					<button id='avail_bar_day_15' class="avail-bar-btn" data-days="15)">
						{l s="15 Days" mod='dashavailability'}
					</button>
				</div>
				<div class="col-xs-2 col-md-2  col-lg-2 pull-left">
					<button id='avail_bar_day_30' class="avail-bar-btn" data-days="30)">
						{l s="30 Days" mod='dashavailability'}
					</button>
				</div>
		</div>
		<div class="avil-chart-svg" id="availability_line_chart1">
			<svg></svg>
		</div>
	</section>
</div>