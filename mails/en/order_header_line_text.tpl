{if isset($list['for_admin']) && $list['for_admin']}
{if isset($list['hotel_name']) && $list['hotel_name']}
{if isset($list['has_room_bookings']) && $list['has_room_bookings']}
{l s='New bookings are created on'} {$list['hotel_name']}!
{else}
{l s='New order is created on'} {$list['hotel_name']}!
{/if}
{else}
{l s='New order is created'}!
{/if}
{else}
{if isset($list['hotel_name']) && $list['hotel_name']}
{if isset($list['has_room_bookings']) && $list['has_room_bookings']}
{l s='Thank you for booking with'} {$list['hotel_name']}!
{else}
{l s='Thank you for order with'} {$list['hotel_name']}!
{/if}
{else}
{l s='Thank you for your order'}!
{/if}
{/if}