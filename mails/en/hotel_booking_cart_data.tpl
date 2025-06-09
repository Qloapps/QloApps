{if isset($list) && $list}
    <table class="table table-recap room-booking-table">
        <thead>
            <tr>
                <th colspan="9" class="table-caption">{l s='Rooms Booking Detail'}</th>
            </tr>
            <tr>
                <th>{l s="Room Image"}</th>
                <th>{l s="Room Description"}</th>
                <th>{l s="Hotel"}</th>
                <th>{l s="Room Capcity"}</th>
                <th>{l s="Unit Price"}</th>
                <th>{l s="Rooms Qty"}</th>
                <th>{l s="Check-in Date"}</th>
                <th>{l s="Check-out Date"}</th>
                <th>{l s="Total"}</th>
            </tr>
        </thead>
        {foreach from=$list key=data_k item=data_v}
            {foreach from=$data_v['date_diff'] key=rm_k item=rm_v}
                <tr>
                    <td>
                        <img src="{$data_v['cover_img']}" class="img-responsive" />
                    </td>
                    <td >
                        {$data_v['name']}
                    </td>
                    <td >
                        {$data_v['hotel_name']}
                    </td>
                    <td >
                        {$rm_v['adults']} {l s='Adults'}, {$rm_v['children']} {l s='Children'}
                    </td>
                    <td>
                        {convertPrice price=$rm_v['avg_paid_unit_price_tax_excl']}
                    </td>
                    <td>
                        {$rm_v['num_rm']}
                    </td>
                    <td>
                        {if !isset($show_full_date)}{assign var="show_full_date" value=0}{/if}
                        {assign var="is_full_date" value=($show_full_date && ($rm_v['data_form']|date_format:'%D' == $rm_v['data_to']|date_format:'%D'))}
                        {$rm_v['data_form']|date_format:"%d-%b-%G"}{if $is_full_date} {$rm_v['data_form']|date_format:"%I:%M %p"}{/if}
                    </td>
                    <td>
                        {$rm_v['data_to']|date_format:"%d-%b-%G"}{if $is_full_date} {$rm_v['data_to']|date_format:"%I:%M %p"}{/if}
                    </td>
                    <td>
                        {convertPrice price=$rm_v['amount_tax_excl']}
                    </td>
                </tr>
            {/foreach}
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
