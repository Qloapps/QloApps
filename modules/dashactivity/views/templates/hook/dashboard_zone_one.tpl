{*
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
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2016 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

<section id="dashactivity" class="widget allow_push">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-time"></i> {l s="Activity overview" mod='dashactivity'}
			<span class="panel-heading-action">
				<a class="list-toolbar-btn" href="#" onclick="toggleDashConfig('dashactivity'); return false;" title="{l s="Configure" mod='dashactivity'}">
					<i class="process-icon-configure"></i>
				</a>
				<a class="list-toolbar-btn" href="#" onclick="refreshDashboard('dashactivity'); return false;" title="{l s="Refresh" mod='dashactivity'}">
					<i class="process-icon-refresh"></i>
				</a>
			</span>
		</div>
		<section id="dashactivity_config" class="dash_config hide">
			<header><i class="icon-wrench"></i> {l s="Configuration" mod='dashactivity'}</header>
			{$dashactivity_config_form}
		</section>

		<section class="activity-section dash-live">
			<span class="title">
				<a href="{$link->getAdminLink("AdminStats")|escape:"html":"UTF-8"}&module=statslive" target="_blank">
					<span>{l s="Online Visitors" mod='dashactivity'}</span>
				</a>
			</span>
			<span class="value">
				<span id="online_visitor"></span>
			</span>
			<div class="sub-title">
				<small class="text-muted">
					{l s="in the last %d minutes" sprintf=$DASHACTIVITY_VISITOR_ONLINE|intval mod='dashactivity'}
				</small>
			</div>
		</section>

		<section class="activity-section dash-live">
			<span class="title">
				<a href="{$link->getAdminLink("AdminCarts")|escape:"html":"UTF-8"}" target="_blank">
					<span>{l s="Active Booking Carts" mod='dashactivity'}</span>
				</a>
			</span>
			<span class="value">
				<span id="active_shopping_cart"></span>
			</span>
			<div class="sub-title">
				<small class="text-muted">
					{l s="in the last %d minutes" sprintf=$DASHACTIVITY_CART_ACTIVE|intval mod='dashactivity'}
				</small>
			</div>
		</section>

		<section id="dash_pending" class="activity-section">
			<span class="title">
				<span>{l s="Currently Pending" mod='dashactivity'}</span>
			</span>
			<ul class="stats-list">
				<li>
					<span class="item-label">
						<a href="{$link->getAdminLink("AdminOrders")|escape:"html":"UTF-8"}" target="_blank">
							<span>{l s="Bookings" mod='dashactivity'}</span>
						</a>
					</span>
					<span class="item-value">
						<span id="pending_orders"></span>
					</span>
				</li>
				<li>
					<span class="item-label">
						<a href="{$link->getAdminLink("AdminOrderRefundRequests")|escape:"html":"UTF-8"}" target="_blank">
							<span>{l s="Refunds" mod='dashactivity'}</span>
						</a>
					</span>
					<span class="item-value">
						<span id="return_exchanges"></span>
					</span>
				</li>
				<li>
					<span class="item-label">
						<a href="{$link->getAdminLink("AdminCarts")|escape:"html":"UTF-8"}" target="_blank">
							<span>{l s="Abandoned Carts" mod='dashactivity'}</span>
						</a>
					</span>
					<span class="item-value">
						<span id="abandoned_cart"></span>
					</span>
				</li>
			</ul>
		</section>
	</div>

	<div class="panel">
		<div class="panel-heading">
			<i class="icon-time"></i> {l s="Activity overview" mod='dashactivity'}
			<span class="panel-heading-action">
				<a class="list-toolbar-btn" href="#" onclick="refreshDashboard('dashactivity'); return false;" title="{l s="Refresh" mod='dashactivity'}">
					<i class="process-icon-refresh"></i>
				</a>
			</span>
		</div>
		<section id="dash_customers" class="activity-section">
			<span class="title">
				<span>{l s="Customers & Newsletters" mod='dashactivity'}</span>
			</span>
			<div class="sub-title">
				<small class="text-muted" id="customers-newsletters-subtitle"></small>
			</div>

			<ul class="stats-list">
				<li>
					<span class="item-label">
						<a href="{$link->getAdminLink("AdminCustomers")|escape:"html":"UTF-8"}" target="_blank">
							<span>{l s="New Customers" mod='dashactivity'}</span>
						</a>
					</span>
					<span class="item-value">
						<span id="new_customers"></span>
					</span>
				</li>
				<li>
					<span class="item-label">
						<a href="{$link->getAdminLink("AdminStats")|escape:"html":"UTF-8"}&module=statsnewsletter"
							target="_blank">
							<span>{l s="New Subscriptions" mod='dashactivity'}</span>
						</a>
					</span>
					<span class="item-value">
						<span id="new_registrations"></span>
					</span>
				</li>
				<li>
					<span class="item-label">
						<a href="{$link->getAdminLink("AdminModules")|escape}&configure=blocknewsletter&module_name=blocknewsletter"
							target="_blank">
							<span>{l s="Total Subscribers" mod='dashactivity'}</span>
						</a>
					</span>
					<span class="item-value">
						<span id="total_suscribers"></span>
					</span>
				</li>
			</ul>
		</section>

		<section id="dash_traffic" class="activity-section">
			<span class="title">
				<span>{l s="Traffic" mod='dashactivity'}</span>
			</span>
			<div class="sub-title">
				<small class="text-muted" id="traffic-subtitle"></small>
			</div>
			<ul class="stats-list">
				<li>
					<span class="item-label">
						<a href="{$link->getAdminLink("AdminStats")|escape:"html":"UTF-8"}&module=statsforecast"
							target="_blank">
							<span>{l s="Visits" mod='dashactivity'}</span>
						</a>
					</span>
					<span class="item-value">
						<span id="visits"></span>
					</span>
				</li>
				<li>
					<span class="item-label">
						<a href="{$link->getAdminLink("AdminStats")|escape:"html":"UTF-8"}&module=statsvisits"
							target="_blank">
							<span>{l s="Unique Visitors" mod='dashactivity'}</span>
						</a>
					</span>
					<span class="item-value">
						<span id="unique_visitors"></span>
					</span>
				</li>
				<li>
					<span class="item-label heading">
						<span>{l s="Traffic Sources" mod='dashactivity'}</span>
					</span>

					<ul class="data_list_small" id="dash_traffic_source"></ul>
					<div id="dash_traffic_chart2" class="chart with-transitions">
						<svg></svg>
					</div>
				</li>
			</ul>
		</section>
	</div>
</section>
