{if isset($extraDemands) && $extraDemands}
	{assign var=roomCount value=1}
	{foreach $extraDemands as $roomDemand}
		<div class="card">
			<div class="row">
				<div class="col-sm-12 demand_header">
					{l s='Room'} {$roomCount|escape:'html':'UTF-8'}&nbsp;
					<span>({if {$roomDemand['adult']} <= 9}0{$roomDemand['adult']}{else}{$roomDemand['adult']}{/if} {if $roomDemand['adult'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}, {if {$roomDemand['children']} <= 9}0{$roomDemand['children']}{else}{$roomDemand['children']}{/if} {if $roomDemand['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if})</span>
				</div>
				<div>
					<div class="col-sm-12 demand_detail">
						{foreach $roomDemand['extra_demands'] as $demand}
							<div class="row demand_detail_price_block">
								<div class="col-xs-6">{$demand['name']|escape:'html':'UTF-8'}</div>
								<div class="col-xs-6">
									<span class="pull-right">
										{if $useTax}
											{displayPrice price="{$demand['total_price_tax_incl']|escape:'html':'UTF-8'}"}
										{else}
											{displayPrice price="{$demand['total_price_tax_excl']|escape:'html':'UTF-8'}"}
										{/if}
									</span>
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