<table class="table table-recap order-total-info-table">
    {if isset($list['has_room_bookings']) && $list['has_room_bookings']}
        <tr>
            <td>
                <strong>{l s="Total Rooms Cost"}</strong>
            </td>
            <td>
                {$list['room_price_tax_excl']}
            </td>
        </tr>
    {/if}
    {if isset($list['has_standalone_products']) && $list['has_standalone_products']}
        <tr>
            <td>
                <strong>{l s="Total Products Cost"}</strong>
            </td>
            <td>
                {$list['service_products_price_tax_excl']}
            </td>
        </tr>
    {/if}
    {if isset($list['has_room_bookings']) && $list['has_room_bookings']}
        <tr>
            <td>
                <strong>{l s="Total Extra Services Cost"}</strong>
            </td>
            <td>
                {$list['additional_service_price_tax_excl']}
            </td>
        </tr>
    {/if}
    {if isset($list['has_room_bookings']) && $list['has_room_bookings']}
        <tr>
            <td>
                <strong>{l s="Total Convenience Fees"}</strong>
            </td>
            <td>
                {$list['total_convenience_fee_te']}
            </td>
        </tr>
    {/if}
    <tr>
        <td>
            <strong>{l s="Total Tax"}</strong>
        </td>
        <td>
            {$list['total_order_tax']}
        </td>
    </tr>
    <tr>
        <td>
            <strong>{l s="Discounts"}</strong>
        </td>
        <td>
            {$list['total_discounts']}
        </td>
    </tr>
    <tr>
        <td>
            <strong>{l s="Final Booking Amount"}</strong>
        </td>
        <td>
            {$list['total_paid']}
        </td>
    </tr>
</table>

<style>
    .order-total-info-table {
        width: 100%;
        border-collapse: collapse;
    }
    .order-total-info-table td {
        border:1px solid #D6D4D4;
        background-color: #fbfbfb;
        font-family: Arial;
        font-size: 13px;
        padding: 7px 10px;
        text-align:right;
        color: #333;
    }
</style>