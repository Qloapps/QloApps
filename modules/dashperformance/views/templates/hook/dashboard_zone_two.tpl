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

<div class="col-sm-12">
	<section id="dashperformance" class="panel widget {if $allow_push} allow_push{/if}">
		<header class="panel-heading">
			<i class="icon-bar-chart"></i> {l s='Performance' mod='dashperformance'} <small class="text-muted">{l s='(Amounts are tax exclusive)' mod='dashperformance'}</small>
			<span class="panel-heading-action">
				<a class="list-toolbar-btn" href="#" onclick="refreshDashboard('dashperformance'); return false;" title="{l s='Refresh' mod='dashperformance'}">
					<i class="process-icon-refresh"></i>
				</a>
			</span>
		</header>
		<section>
			<div class="row stats-wrap">
				<div class="col-xs-6 col-lg-3">
					<div class="stat-box label-tooltip" data-toggle="tooltip" data-original-title="{l s='Average Daily Rate (ADR) represents the average rental income per paid occupied room over a given time period.' mod='dashperformance'}" data-placement="top" style="background-color: #B7F0FF;">
						<div class="title-wrapper">
							<p>{l s='Average Daily Rate' mod='dashperformance'}</p>
						</div>
						<div class="value-wrapper">
							<span id="dp_average_daily_rate" style="color: #0093BA;">--</span>
						</div>
					</div>
				</div>
                <div class="col-xs-6 col-lg-3">
					<div class="stat-box label-tooltip" data-toggle="tooltip" data-original-title="{l s='Average Occupancy Rate (AOR) is the average percentage of rooms booked out over a given time period.' mod='dashperformance'}" data-placement="top" style="background-color: #FFE5B4;">
						<div class="title-wrapper">
							<p>{l s='Average Occupancy Rate' mod='dashperformance'}</p>
						</div>
						<div class="value-wrapper">
							<span id="dp_average_occupancy_rate" style="color: #E09400;">--</span>
						</div>
					</div>
				</div>
                <div class="col-xs-6 col-lg-3">
					<div class="stat-box label-tooltip" data-toggle="tooltip" data-original-title="{l s='Direct Revenue Ratio (DRR) measures the percentage of online revenue that comes directly from your website vs. third party channels.' mod='dashperformance'}" data-placement="top" style="background-color: #B6FFB6;">
						<div class="title-wrapper">
							<p>{l s='Direct Revenue Ratio' mod='dashperformance'}</p>
						</div>
						<div class="value-wrapper">
							<span id="dp_direct_revenue_ratio" style="color: #00B200;">--</span>
						</div>
					</div>
				</div>
                <div class="col-xs-6 col-lg-3">
					<div class="stat-box label-tooltip" data-toggle="tooltip" data-original-title="{l s='Cancellation Rate (CR) is the percentage of all cancelled orders out of all orders over a given time period.' mod='dashperformance'}" data-placement="top" style="background-color: #FFBBB8;">
						<div class="title-wrapper">
							<p>{l s='Cancellation Rate' mod='dashperformance'}</p>
						</div>
						<div class="value-wrapper">
							<span id="dp_cancellation_rate" style="color: #FF4036;">--</span>
						</div>
					</div>
				</div>
                <div class="col-xs-6 col-lg-3">
					<div class="stat-box label-tooltip" data-toggle="tooltip" data-original-title="{l s='Revenue Per Available Room (RevPAR) measures how many rooms are being booked, and how much revenue those bookings are generating.' mod='dashperformance'}" data-placement="top" style="background-color: #B6FFB6;">
						<div class="title-wrapper">
							<p>{l s='Revenue Per Available Room' mod='dashperformance'}</p>
						</div>
						<div class="value-wrapper">
							<span id="dp_revenue_per_available_room" style="color: #00B200;">--</span>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-lg-3">
					<div class="stat-box label-tooltip" data-toggle="tooltip" data-original-title="{l s='Total Revenue Per Available Room (TrevPAR) measures the total revenue being generated per available room including additional facilities and service products.' mod='dashperformance'}" data-placement="top" style="background-color: #EBCDFF;">
						<div class="title-wrapper">
							<p>{l s='Total Revenue Per Available Room' mod='dashperformance'}</p>
						</div>
						<div class="value-wrapper">
							<span id="dp_total_revenue_per_available_room" style="color: #FF4036;">--</span>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-lg-3">
					<div class="stat-box label-tooltip" data-toggle="tooltip" data-original-title="{l s='Gross Operating Profit Per Available Room (GOPPAR) measures how much gross operating profit comes from each room.' mod='dashperformance'}" data-placement="top" style="background-color: #B7F0FF;">
						<div class="title-wrapper">
							<p>{l s='Gross Operating Profit Per Available Room' mod='dashperformance'}</p>
						</div>
						<div class="value-wrapper">
							<span id="dp_gross_operating_profit_par" style="color: #0093BA;">--</span>
						</div>
					</div>
				</div>
				<div class="col-xs-6 col-lg-3">
					<div class="stat-box label-tooltip" data-toggle="tooltip" data-original-title="{l s='Average Length of Stay (ALOS) is the average amount of days guests stay at the hotel over a given time period.' mod='dashperformance'}" data-placement="top" style="background-color: #FFE5B4;">
						<div class="title-wrapper">
							<p>{l s='Average Length of Stay' mod='dashperformance'}</p>
						</div>
						<div class="value-wrapper">
							<span id="dp_average_length_of_stay" style="color: #E09400;">--</span>
						</div>
					</div>
				</div>
			</div>
		</section>
	</section>
</div>
<div class="clearfix"></div>
