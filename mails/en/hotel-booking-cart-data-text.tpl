{if isset($list)}
{foreach from=$list key=data_k item=data_v}
{foreach from=$data_v['date_diff'] key=rm_k item=rm_v}
<img src="{$data_v['cover_img']}" class="img-responsive" />

{$data_v['name']}

{$data_v['hotel_name']}

{$rm_v['adults']} {l s='Adults'}, {$rm_v['children']} {l s='Children'}

{convertPrice price=$rm_v['avg_paid_unit_price_tax_excl']}

{$rm_v['num_rm']}

{$rm_v['data_form']|date_format:"%d-%b-%G"}

{$rm_v['data_to']|date_format:"%d-%b-%G"}

{$rm_v['amount_tax_excl']}


{/foreach}
{/foreach}
{/if}

