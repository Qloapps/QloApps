{**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
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