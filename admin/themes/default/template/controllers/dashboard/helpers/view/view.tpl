{*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script>
	var dashboard_ajax_url = '{$link->getAdminLink('AdminDashboard')}';
	var adminstats_ajax_url = '{$link->getAdminLink('AdminStats')}';
	var no_results_translation = '{l s='No result' js=1}';
	var dashboard_use_push = '{$dashboard_use_push|intval}';
	var read_more = '{l s='Read more' js=1}';
</script>

<div id="dashboard">
	{$hookDashboardTop}
	<div class="row">
		{if $warning}
			<div class="col-lg-12">
				<div class="alert alert-warning">{$warning}</div>
			</div>
		{/if}
		<div class="col-lg-12">
			<div class="panel clearfix">
				<div class="col-lg-6">
					<div id="calendar">
						<form action="{$action|escape}" method="post" id="calendar_form" name="calendar_form" class="form-inline">
							<div class="btn-toolbar">
								<div class="btn-group input-group">
									<button type="button" name="submitDateDay" class="btn btn-default submitDateDay{if (!isset($preselect_date_range) || !$preselect_date_range) || (isset($preselect_date_range) && $preselect_date_range == 'day')} active{/if}">
										{l s='Day'}
									</button>
									<button type="button" name="submitDateMonth" class="btn btn-default submitDateMonth {if isset($preselect_date_range) && $preselect_date_range == 'month'}active{/if}">
										{l s='Month'}
									</button>
									<button type="button" name="submitDateYear" class="btn btn-default submitDateYear{if isset($preselect_date_range) && $preselect_date_range == 'year'} active{/if}">
										{l s='Year'}
									</button>
									<button type="button" name="submitDateDayPrev" class="btn btn-default submitDateDayPrev{if isset($preselect_date_range) && $preselect_date_range == 'prev-day'} active{/if}">
										{l s='Day'}-1
									</button>
									<button type="button" name="submitDateMonthPrev" class="btn btn-default submitDateMonthPrev{if isset($preselect_date_range) && $preselect_date_range == 'prev-month'} active{/if}">
										{l s='Month'}-1
									</button>
									<button type="button" name="submitDateYearPrev" class="btn btn-default submitDateYearPrev{if isset($preselect_date_range) && $preselect_date_range == 'prev-year'} active{/if}">
										{l s='Year'}-1
									</button>
								</div>
								<input type="hidden" name="datepickerFrom" id="datepickerFrom" value="{$date_from|escape}" class="form-control">
								<input type="hidden" name="datepickerTo" id="datepickerTo" value="{$date_to|escape}" class="form-control">
								<input type="hidden" name="preselectDateRange" id="preselectDateRange" value="{if isset($preselect_date_range)}{$preselect_date_range}{/if}" class="form-control">
								<div class="form-group input-group">
									<button id="datepickerExpand" class="btn btn-default" type="button">
										<i class="icon-calendar-empty"></i>
										<span class="hidden-xs">
											{l s='From'}
											<strong class="text-info" id="datepicker-from-info">{$date_from|escape}</strong>
											{l s='To'}
											<strong class="text-info" id="datepicker-to-info">{$date_to|escape}</strong>
											<strong class="text-info" id="datepicker-diff-info"></strong>
										</span>
										<i class="icon-caret-down"></i>
									</button>
									{$calendar}
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="col-lg-6">
					<form action="{$action|escape}" method="post" class="form-inline">
						<div class="text-right">
							<select class="form-control stats-filter-hotel" name="stats_id_hotel">
								{foreach from=$hotel_options item=hotel_option}
									<option value="{$hotel_option.id_hotel}" {if $hotel_option.id_hotel == $id_hotel}selected{/if}>
										{$hotel_option.hotel_name|escape:'html':'UTF-8'}
									</option>
								{/foreach}
							</select>
							<input type="hidden" id="submit-stats-hotel" name="submitStatsHotel" value="1" disabled>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	{include file='recomended-banner.tpl'}
	<div class="row" id="recommendation-wrapper" style="display:none">
	</div>
	<div class="row">
		<div class="col-md-8 col-md-push-4 col-lg-7 col-lg-push-3" id="hookDashboardZoneTwo">
			<div class='row'>
				{$hookDashboardZoneTwo}
			</div>
		</div>
		<div class="col-md-4 col-md-pull-8 col-lg-3 col-lg-pull-7" id="hookDashboardZoneOne">
			{$hookDashboardZoneOne}
		</div>
		<div class="col-md-4 col-md-pull-8 col-lg-2 col-lg-pull-0" id="hookDashboardZoneThree">
			<div class="row">
				{$hookDashboardZoneThree}
				{if isset($upgrade_info) && $upgrade_info}
					<div class="col-sm-12">
						<section class="widget panel">
							{$upgrade_info->dash_upgrade_panel}
						</section>
					</div>
				{/if}
				<div class="col-sm-12">
					<section class="dash_links widget panel">
						<h3><i class="icon-link"></i> {l s="Help Center"}</h3>
							<dl>
								<dt><a href="https://qloapps.com/qlo-reservation-system/" class="_blank">{l s="Official Documentation"}</a></dt>
								<dd>{l s="Qloapps User Guide"}</dd>
							</dl>
							<dl>
								<dt><a href="https://forums.qloapps.com/" class="_blank">{l s="Qloapps Forum"}</a></dt>
								<dd>{l s="Connect with the Qloapps community"}</dd>
							</dl>
							<dl>
								<dt><a href="https://qloapps.com/addons/" class="_blank">{l s="Qloapps Addons"}</a></dt>
								<dd>{l s="Enhance your store Qloapps modules"}</dd>
							</dl>
							<dl>
								<dt><a href="https://qloapps.com/contact/" class="_blank">{l s="Contact Us!"}</a></dt>
								<dd>{l s="Contact us for any help"}</dd>
							</dl>
					</section>
				</div>
			</div>
		</div>

	</div>
</div>
