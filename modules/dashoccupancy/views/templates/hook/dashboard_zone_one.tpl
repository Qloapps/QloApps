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

<section id="dashoccupancy" class="panel widget allow_push">
	<header class="panel-heading">
		<i class="icon-bar-chart"></i>
		<span>
			{l s='Occupancy' mod='dashoccupancy'}
			&nbsp;<small class='text-muted' id='dashoccupancy_date_range'></small>
		</span>
		<span class="panel-heading-action">
			<a class="list-toolbar-btn" href="javascript:void(0);" title="Refresh" onclick="refreshDashboard('dashoccupancy'); return false;">
				<i class="process-icon-refresh"></i>
			</a>
		</span>
	</header>
	<div class="row text-center avil-chart-head">
		<div class="col-md-4 col-xs-4">
			<div class="row">
				<div class="col-md-11 label-tooltip col-lg-11 avail-pie-label-container" style="background: #A569DF;"  data-toggle="tooltip" data-original-title="{l s='The total number of rooms booked for date range out of total number of rooms.' mod='dashoccupancy'}">
					<div class="">
						<p class="avail-pie-text">
							{l s='Occupied' mod='dashoccupancy'}
						</p>
						<div class="avail-pie-value-container">
							<p class="avail-pie-value" id="pie_occupied_text">
								{$count_occupied|escape:'htmlall':'UTF-8'}/{$count_total|escape:'htmlall':'UTF-8'}
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4 col-xs-4">
			<div class="row">
				<div class="col-md-11 col-lg-11 avail-pie-label-container label-tooltip" style="background: #56CE56;" data-toggle="tooltip" data-original-title="{l s='The total number of rooms available for booking for date range.' mod='dashoccupancy'}">
					<div class="">
						<p class="avail-pie-text">
							{l s='Available' mod='dashoccupancy'}
						</p>
						<div class="avail-pie-value-container">
							<p class="avail-pie-value" id="pie_avail_text">
								{$count_available|escape:'htmlall':'UTF-8'}
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4 col-xs-4">
			<div class="row">
				<div class="col-md-11 col-lg-11 avail-pie-label-container label-tooltip" style="background: #FF655C;" data-toggle="tooltip" data-original-title="{l s='The total number of rooms unavailable for booking for date range.' mod='dashoccupancy'}">
					<div class="">
						<p class="avail-pie-text">
							{l s='Unavailable' mod='dashoccupancy'}
						</p>
						<div class="avail-pie-value-container">
							<p class="avail-pie-value" id="pie_inactive_text">
								{$count_unavailable|escape:'htmlall':'UTF-8'}
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="avil-chart-svg" id="availablePieChart">
		<svg></svg>
	</div>
</section>

<script type='text/javascript'>
	date_occupancy_range = '{$date_occupancy_range|escape:'html':'UTF-8'}';
	date_occupancy_avail_format = '{$date_occupancy_avail_format|escape:'html':'UTF-8'}';
</script>
