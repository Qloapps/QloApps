{if isset($extraDemands) && $extraDemands}
	{assign var=roomCount value=1}
	{foreach $extraDemands as $roomDemand}
		<div class="card">
			<div class="row">
				<div class="col-sm-12 demand_header">
					{l s='Room'} {$roomCount|escape:'html':'UTF-8'}
				</div>
				<div>
					<div class="col-sm-12 demand_detail">
						{foreach $roomDemand['extra_demands'] as $demand}
							<div class="row demand_detail_price_block">
								<div class="col-xs-6">{$demand['name']|escape:'html':'UTF-8'}</div>
								<div class="col-xs-6">
									<span class="pull-right">{displayPrice price="{$demand['price']|escape:'html':'UTF-8'}"}</span>
								</div>
							</div>
						{/foreach}
					</div>
				</div>
			</div>
		</div>
		{assign var=roomCount value=$roomCount+1}
	{/foreach}
{/if}