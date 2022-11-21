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
	<section id="dashproducts" class="panel widget {if $allow_push} allow_push{/if}">
		<header class="panel-heading">
			<i class="icon-bar-chart"></i> {l s='Sales' mod='dashproducts'}
			<span class="panel-heading-action">
				<a class="list-toolbar-btn" href="#" onclick="toggleDashConfig('dashproducts'); return false;" title="{l s="Configure" mod="dashproducts"}">
					<i class="process-icon-configure"></i>
				</a>
				<a class="list-toolbar-btn" href="#"  onclick="refreshDashboard('dashproducts'); return false;"  title="{l s="Refresh" mod="dashproducts"}">
					<i class="process-icon-refresh"></i>
				</a>
			</span>
		</header>

		<section id="dashproducts_config" class="dash_config hide">
			<header><i class="icon-wrench"></i> {l s='Configuration' mod='dashproducts'}</header>
			{$dashproducts_config_form}
		</section>

		<section>
			<nav>
				<ul class="nav nav-pills row">
					<li class="col-xs-6 col-sm-3 nav-item active">
						<a href="#dash_recent_orders" data-toggle="tab">
							<span>{l s="New Bookings" mod="dashproducts"}</span>
						</a>
					</li>
					<li class="col-xs-6 col-sm-3 nav-item">
						<a href="#dash_best_sellers" data-toggle="tab">
							<span>{l s="Best Selling" mod="dashproducts"}</span>
						</a>
					</li>
					<li class="col-xs-6 col-sm-3 nav-item">
						<a href="#dash_most_viewed" data-toggle="tab">
							<span>{l s="Most Viewed" mod="dashproducts"}</span>
						</a>
					</li>
					<li class="col-xs-6 col-sm-3 nav-item">
						<a href="#dash_top_search" data-toggle="tab">
							<span>{l s="Top Searches" mod="dashproducts"}</span>
						</a>
					</li>
				</ul>
			</nav>

			<div class="tab-content panel">
				<div class="tab-pane active" id="dash_recent_orders">
					<h3>{l s="Last %d bookings" sprintf=$DASHPRODUCT_NBR_SHOW_LAST_ORDER|intval mod="dashproducts"}</h3>
					<div class="table-responsive">
						<table class="table data_table" id="table_recent_orders">
							<thead></thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
				<div class="tab-pane" id="dash_best_sellers">
					<h3>
						{l s="Top %d room types" sprintf=$DASHPRODUCT_NBR_SHOW_BEST_SELLER|intval mod="dashproducts"}
						<span>{l s="From" mod="dashproducts"} {$date_from} {l s="to" mod="dashproducts"} {$date_to}</span>
					</h3>
					<div class="table-responsive">
						<table class="table data_table" id="table_best_sellers">
							<thead></thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
				<div class="tab-pane" id="dash_most_viewed">
					<h3>
						{l s="Most Viewed" mod="dashproducts"}
						<span>{l s="From" mod="dashproducts"} {$date_from} {l s="to" mod="dashproducts"} {$date_to}</span>
					</h3>
					<div class="table-responsive">
						<table class="table data_table" id="table_most_viewed">
							<thead></thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
				<div class="tab-pane" id="dash_top_search">
					<h3>
						{l s="Top %d most searched hotels" sprintf=$DASHPRODUCT_NBR_SHOW_TOP_SEARCH|intval mod="dashproducts"}
						<span>{l s="From" mod="dashproducts"} {$date_from} {l s="to" mod="dashproducts"} {$date_to}</span>
					</h3>
					<div class="table-responsive">
						<table class="table data_table" id="table_top_10_most_search">
							<thead></thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</section>
	</section>
</div>
<div class="clearfix"></div>