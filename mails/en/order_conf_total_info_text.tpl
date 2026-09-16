{if isset($list['has_room_bookings']) && $list['has_room_bookings']}
{l s="Total Rooms Cost"}  {$list['room_price_tax_excl']}
{/if}

{if isset($list['has_standalone_products']) && $list['has_standalone_products']}
{l s="Total Products Cost"}  {$list['service_products_price_tax_excl']}
{/if}

{if isset($list['has_room_bookings']) && $list['has_room_bookings']}
{l s="Total Extra Services Cost"}  {$list['additional_service_price_tax_excl']}
{/if}

{if isset($list['has_room_bookings']) && $list['has_room_bookings']}
{l s="Total Convenience Fees"}  {$list['total_convenience_fee_te']}
{/if}

{l s="Room and Service Tax"}  {$list['total_order_tax']}

{if isset($list['has_tourism_tax']) && $list['has_tourism_tax']}
{l s="Total Tourism Tax"}  {$list['total_tourism_tax']}
{/if}

{l s="Discounts"}  {$list['total_discounts']}

{l s="Final Booking Amount"}  {$list['total_paid']}