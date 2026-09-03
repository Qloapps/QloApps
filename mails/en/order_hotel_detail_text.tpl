{if isset($list['hotel_name']) && $list['hotel_name']}
{l s='Property details'}:

{l s='Property Name'}: {$list['hotel_name']}
{l s='Property Phone'}: {$list['hotel_phone']}
{l s='Property Email'}: {$list['hotel_email']}
{l s='Total Stays'}: {$list['num_rooms']}

{/if}