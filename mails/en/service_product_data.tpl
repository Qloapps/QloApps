{if isset($list) && $list}
    <table class="table table-recap service-product-table"><!-- Title -->
        <tr>
            <th colspan="5" class="table-caption">{l s='Service Products Detail'}</th>
        </tr>
        <tr>
            <th>{l s='Image'}</th>
            <th>{l s='Name'}</th>
            <th>{l s='Unit Price'}</th>
            <th>{l s='Qty'}</th>
            <th>{l s='Total'}</th>
        </tr>
        <tbody>
            {foreach from=$list key=key item=product}
                <tr>
                    <td>
                        <img src="{$product['cover_img']}" class="img-responsive" />
                    </td>
                    <td>
                        {$product['name']}{if isset($product['option_name']) && $product['option_name']} : {$product['option_name']}{/if}
                    </td>
                    <td>
                        {convertPrice price=$product['unit_price_tax_excl']}
                    </td>
                    <td>
                        {$product['quantity']}
                    </td>
                    <td>
                        {convertPrice price=$product['total_price_tax_excl']}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>

    <style>
        <style>
            .pull-right {
                float: right;
            }
            .service-product-table {
                width:100%;
                border-collapse:collapse;
                padding:5px;
            }
            .service-product-table th {
                border:1px solid #D6D4D4;
                background-color: #fbfbfb;
                color: #333;
                font-family: Arial;
                font-size: 13px;
                padding: 7px 7px 5px 10px;
                text-align:left;
            }
            .service-product-table th.table-caption {
                text-align: left;
                padding:10px;
            }
            .service-product-table td {
                border:1px solid #D6D4D4;
                padding: 5px 5px 5px 10px;
                text-align:left;
            }
        </style>
    </style>
{/if}
