{if isset($list['hotel_name']) && $list['hotel_name']}
{l s='Hotel details'}:

{l s='Hotel Name'}: {$list['hotel_name']}
{l s='Hotel Phone'}: {$list['hotel_phone']}
{l s='Hotel Email'}: {$list['hotel_email']}
{l s='Total Rooms'}: {$list['num_rooms']}

{/if}