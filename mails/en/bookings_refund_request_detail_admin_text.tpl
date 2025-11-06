{if isset($list) && $list}

{l s="Room Num"}

{l s="Room Type"}

{l s="Hotel"}

{l s="Duration"}

{foreach from=$list key=data_k item=data_v}

{$data_v['room_num']}

{$data_v['room_type_name']}

{$data_v['hotel_name']}

{$data_v['date_from']|date_format:"%d-%b-%G"} To {$data_v['date_to']|date_format:"%d-%b-%G"}

{/foreach}
{/if}