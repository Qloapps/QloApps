{if isset($list) && $list}
    <table class="table table-recap room-booking-table">
        <thead>
            <tr>
                <th colspan="9" class="table-caption">{l s='Rooms Bookings Detail'}</th>
            </tr>
            <tr>
                <th>{l s="Room Type"}</th>
                <th>{l s="Hotel"}</th>
                <th>{l s="Rooms Qty"}</th>
                <th>{l s="Duration"}</th>
            </tr>
        </thead>
        {foreach from=$list key=data_k item=data_v}
            <tr>
                <td>
                    {$data_v['room_type_name']}
                </td>
                <td>
                    {$data_v['hotel_name']}
                </td>
                <td>
                    {$data_v['num_rooms']}
                </td>
                <td>
                    {$data_v['date_from']|date_format:"%d-%b-%G"} To {$data_v['date_to']|date_format:"%d-%b-%G"}
                </td>
            </tr>
        {/foreach}
    </table>
    <style>
        .room-booking-table {
            width:100%;
            border-collapse:collapse;
            padding:5px;
        }
        .room-booking-table th {
            border:1px solid #D6D4D4;
            background-color: #fbfbfb;
            color: #333;
            font-family: Arial;
            font-size: 13px;
            padding: 7px 7px 5px 10px;
            text-align:left;
        }
        .room-booking-table th.table-caption {
            text-align: left;
            padding:10px;
        }
        .room-booking-table td {
            border:1px solid #D6D4D4;
            padding:5px;
            text-align:left;
            padding: 5px 5px 5px 10px;
        }
        .pull-right {
            float: right;
        }
    </style>
{/if}