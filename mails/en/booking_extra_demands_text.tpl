{if isset($list)}
{l s='Extra Demands Details'}
{l s='Room Type'}
{l s='Extra demand name'}
{l s='Total'} {l s='(Tax excl.)'}

{foreach from=$list key=data_k item=data_v}
{foreach from=$data_v['date_diff'] key=rm_k item=rm_v}
{if isset($rm_v['extra_demands']) && $rm_v['extra_demands']}
{assign var=roomCount value=1}
{foreach $rm_v['extra_demands'] as $roomDemand}
{foreach name=demandRow from=$roomDemand['extra_demands'] item=demand}

{if $smarty.foreach.demandRow.first}
{$data_v['name']}
{$rm_v['data_form']|date_format:"%d-%m-%Y"} {l s='to'} {$rm_v['data_to']|date_format:"%d-%m-%Y"}
{l s='Room'} - {$roomCount}
{/if}

{$demand['name']}

{convertPrice price=$demand['total_price_tax_excl']}

{/foreach}
{assign var=roomCount value=$roomCount+1}
{/foreach}
{/if}
{/foreach}
{/foreach}
{/if}
