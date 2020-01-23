{if isset($extraDemands) && $extraDemands}
	{foreach $extraDemands as $roomDemand}
		<div class="row">
			<div class="col-sm-12 demand_header">
				<i class="icon icon-tasks"></i> {l s='Room\'s Extra Demands'}
			</div>
			<div>
				<div class="col-sm-12 demand_detail">
					{foreach $roomDemand['extra_demands'] as $demand}
						<p>
							<span>{$demand['name']}</span>
							<span class="pull-right">{displayPrice price=$demand['total_price_tax_incl'] currency=$orderCurrency}</span>
						</p>
					{/foreach}
				</div>
			</div>
		</div>
	{/foreach}
{/if}