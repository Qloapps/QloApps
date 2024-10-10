<div class="row">
    <div class="col-lg-12">
        <table class="table" id="customer_cart_details">
            <thead>
                <tr>
                    {if $refund_allowed}
                        <th class="standard_refund_fields" style="display:none"></th>
                    {/if}
                    <th class="text-center"><span class="title_box">{l s='Room No.'}</span></th>
                    <th class="text-center"><span class="title_box">{l s='Room Image'}</th>
                    <th class="text-center"><span class="title_box">{l s='Room Type'}</span></th>
                    <th class="text-center"><span class="title_box">{l s='Hotel Name'}</span></th>
                    <th class="text-center"><span class="title_box">{l s='Duration'}</span></th>
                    <th class="text-center fixed-width-lg"><span class="title_box">{l s='Occupancy'}</span></th>
                    <th class="text-center"><span class="title_box">{l s='Room Price (Tax excl.)'}</span></th>
                    <th class="text-center"><span class="title_box">{l s='Extra Services/Fee (Tax excl.)'}</span></th>
                    <th class="text-center"><span class="title_box">{l s='Total Tax'}</span></th>
                    <th class="text-center"><span class="title_box">{l s='Total Price (Tax incl.)'}</span></th>
                    {if (isset($refundReqBookings) && $refundReqBookings) || (isset($isCancelledRoom) && $isCancelledRoom)}
                        <th class="text-center"><span class="title_box">{l s='Refund/Cancellation Status'}</span></th>
                        <th class="text-center"><span class="title_box">{l s='Refunded amount'}</span></th>
                    {/if}
                    {if ($can_edit && !$order->hasBeenDelivered())}
                    <th class="text-center fixed-width-lg"><span class="title_box">{l s='Edit Order'}</th>
                    {/if}
                </tr>
            </thead>
            <tbody>
            {if $order_detail_data}
                {foreach from=$order_detail_data item=data}
                    {* Include product line partial *}
                    {include file='controllers/orders/_product_line.tpl'}
                {/foreach}
            {else}
                {* <tr>
                    <td>{l s='No Data Found.'}</td>
                </tr> *}
            {/if}
            {* Include product line partial *}
            {include file='controllers/orders/_new_product.tpl'}
            </tbody>
        </table>
    </div>
</div>