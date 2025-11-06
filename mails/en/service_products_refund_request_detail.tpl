{if isset($list['service_products']) && $list['service_products']}
    <table class="table table-recap service-products-table">
        <thead>
            <tr>
                <th colspan="9" class="table-caption">{l s='Service Products Detail'}</th>
            </tr>
            <tr>
                <th>{l s="Product name"}</th>
                {if isset($list['is_hotel_products']) && $list['is_hotel_products']}
                    <th>{l s="Hotel"}</th>
                {/if}
                <th>{l s="Quantity"}</th>
            </tr>
        </thead>
        {foreach $list['service_products'] as $product}
            <tr>
                <td>
                    {$product['name']}{if isset($product['option_name']) && $product['option_name']} : {$product['option_name']}{/if}
                </td>
                {if isset($list['is_hotel_products']) && $list['is_hotel_products']}
                    <td>
                        {$product['hotel_name']}
                    </td>
                {/if}
                <td>
                    {$product['quantity']}
                </td>
            </tr>
        {/foreach}
    </table>
    <style>
        .service-products-table {
            width:100%;
            border-collapse:collapse;
            padding:5px;
        }
        .service-products-table th {
            border:1px solid #D6D4D4;
            background-color: #fbfbfb;
            color: #333;
            font-family: Arial;
            font-size: 13px;
            padding: 7px 7px 5px 10px;
            text-align:left;
        }
        .service-products-table th.table-caption {
            text-align: left;
            padding:10px;
        }
        .service-products-table td {
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