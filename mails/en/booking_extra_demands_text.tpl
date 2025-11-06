{if isset($list) && $list}

{l s='Room Extra Demands Detail'}

{l s='Room Type'}
{l s='Name'}
{l s='Total'} {l s='(Tax excl.)'}

{foreach from=$list key=data_k item=data_v}
{foreach from=$data_v['date_diff'] key=rm_k item=rm_v}

{if isset($rm_v['extra_demands']) && $rm_v['extra_demands']}
{assign var=roomCount value=1}

{foreach $rm_v['extra_demands'] as $roomDemand}
{foreach name=demandRow from=$roomDemand['extra_demands'] item=demand}

{if !isset($room_demand_exists)}
{assign var=room_demand_exists value=1}
{/if}

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

{if !isset($room_demand_exists)}
{l s='No room extra demands added.'}
{/if}


{l s='Rooms Services Detail'}

{l s='Room Type'}
{l s='Name'}
{l s='Qty'}
{l s='Total'}

{foreach from=$list key=data_k item=data_v}
{foreach from=$data_v['date_diff'] key=rm_k item=rm_v}

{if isset($rm_v['additional_services']) && $rm_v['additional_services']}
{assign var=roomCount value=1}

{foreach $rm_v['additional_services'] as $roomService}
{foreach name=serviceRow from=$roomService['additional_services'] item=service}

{if !isset($room_additinal_services_exists)}
{assign var=room_additinal_services_exists value=1}
{/if}

{if $smarty.foreach.serviceRow.first}
{$data_v['name']}
{$rm_v['data_form']|date_format:"%d-%m-%Y"} {l s='to'} {$rm_v['data_to']|date_format:"%d-%m-%Y"}
{l s='Room'} - {$roomCount}
{/if}

{$service['name']}

{if $service['allow_multiple_quantity']}
{$service['quantity']}
{else}
{l s='--'}
{/if}

{convertPrice price=$service['total_price_tax_excl']}

{/foreach}
{assign var=roomCount value=$roomCount+1}
{/foreach}
{/if}
{/foreach}
{/foreach}

{if !isset($room_additinal_services_exists)}
{l s='No room services added.'}
{/if}
{/if}
